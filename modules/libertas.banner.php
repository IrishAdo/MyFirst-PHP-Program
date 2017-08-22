<?PHP
/*************************************************************************************************************************
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.banner.php
* @date 08 Nov 2004
*************************************************************************************************************************/
/*************************************************************************************************************************
* This module is the administration module for the Banner manager
*************************************************************************************************************************/
class banner extends module{
	/*************************************************************************************************************************
	*  Class Variables (generic
	*************************************************************************************************************************/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_name_label			= "Banner Manager (Administration)";
	var $module_name				= "banner";
	var $module_admin				= "1";
	var $module_command				= "BANNER_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "BANNER_";
	var $module_label				= "MANAGEMENT_BANNERS";
	var $module_modify		 		= '$Date: 2005/02/09 12:05:09 $';
	var $module_version 			= '$Revision: 1.5 $';
	var $module_creation 			= "27/01/2005";

	function command($user_command, $parameter_list = array()){
		/*************************************************************************************************************************
		* If debug is turned on then output the command sent and the parameter list too.
		*************************************************************************************************************************/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,print_r($parameter_list,true),__LINE__,"command"));
		}
		
		/*************************************************************************************************************************
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*************************************************************************************************************************/
		if (strpos($user_command, $this->module_command)===0){
			/*************************************************************************************************************************
			* Generic module functions
			*************************************************************************************************************************/
			if ($user_command==$this->module_command."DEBUG_ON"){
				$this->module_debug=true;
			}
			if ($user_command==$this->module_command."DEBUG_OFF"){
				$this->module_debug=false;
			}
			if ($user_command==$this->module_command."GET_MODULE"){
				return $this->get_module_name();
			}
			if ($user_command==$this->module_command."GET_VERSION"){
				return $this->module_version;
			}
			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
				return $this->webContainer;
			}
			/*************************************************************************************************************************
			* web site functions
			*************************************************************************************************************************/
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->module_display($parameter_list);
			}
			if ($user_command==$this->module_command."CLICK"){
				return $this->module_click($parameter_list);
			}
		}
		return "";
	}
	/*************************************************************************************************************************
	*                               B A N N E R   A D M I N   M A N A G E R   F U N C T I O N S
	*************************************************************************************************************************/
	
	/*************************************************************************************************************************
	* Initialise function
	* This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	* module::initialise() function allowing you to define any extra constructor
	* functionality.
	*************************************************************************************************************************/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		/*************************************************************************************************************************
		* request the client identifier once we use this variable often
		*************************************************************************************************************************/
		$this->client_identifier = $this->parent->client_identifier;
		/*************************************************************************************************************************
		* load the required locale
		*************************************************************************************************************************/
		$this->load_locale($this->module_name);
		return 1;
	}
	/*************************************************************************************************************************
    * display a banner
    *************************************************************************************************************************/
	function module_display($parameters){
		/*************************************************************************************************************************
        * get list of banenrs to display
        *************************************************************************************************************************/
		$cml	= $this->check_parameters($parameters,"current_menu_location",-1);
		$id		= $this->check_parameters($parameters,"wo_owner_id",-1);
		$cmd = $this->check_parameters($parameters,"command");
		$pos = $this->check_parameters($parameters,"__layout_position");
		if (($pos==2 || $pos==3) && $cmd!=""){
			return "";
		}
		if($cml==-1 || $id == -1){
			return "";
		}
		$format				= 1; // text = 0, gfx =1
		$be_random_toggle	= 0;
		$sql = "select * from banner_list where bl_client = $this->client_identifier and bl_identifier = $id and bl_status =1";
		//print "<li>1".$sql."</li>";
		$result  = $this->parent->db_pointer->database_query($sql);
		$bl_homepage_exception = 0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$bl_homepage_exception	= $r["bl_homepage_exception"];
        	$bl_open_in_window 		= $r["bl_open_in_window"];
        	$bl_direction			= $r["bl_direction"];
        	$bl_number_to_display	= $r["bl_number_to_display"];
        }
        $this->parent->db_pointer->database_free_result($result);

		/*************************************************************************************************************************
        * get list of banner images and display one randomly
        *************************************************************************************************************************/
		$sql = "select * from banner_list 
					inner join banner_types on bl_type = bt_identifier and bt_client = bl_client 
					inner join banner_entry_grouping on bl_identifier = beg_list and beg_client = bl_client 
					inner join banner_entry on be_identifier = beg_banner and beg_client = be_client and be_status =1 and 
						(
							(be_max_click_through = -1 or be_max_click_through>be_cur_click_through) and 
							(be_max_page_impressions = -1 or be_max_page_impressions>be_cur_page_impressions) and 
							(be_date_starts = '0000-00-00 00:00:00' or be_date_starts < '".date("Y-m-d H:i:s")."') and 
							(be_date_expires = '0000-00-00 00:00:00' or be_date_expires > '".date("Y-m-d H:i:s")."')
						)
					left outer join menu_to_object on mto_menu=$cml and mto_module ='BANNER_ENTRY' and mto_object=be_identifier
					left outer join file_to_object on fto_object = be_identifier and fto_client=be_client
					left outer join file_info on fto_file = file_identifier and fto_client=file_client 
					inner join menu_location_settings on be_client = mls_client and mls_link_id = be_identifier and mls_module='BANNER_ENTRY' 
				 	where ";
		if($this->parent->script=="index.php"){
			if($bl_homepage_exception==1){
				$sql.= "
					(
					  (mls_all_locations = 0 and mto_menu is not null) 
					) and ";
			} else {
				$sql.= "
					(
					  (mls_all_locations = 1 and mto_menu is null)  or 
					  (mls_all_locations = 0 and mto_menu is not null) 
					) and ";
			}
		} else {
			$sql.= "
				(
				  (mls_all_locations = 1 and mto_menu is null)  or 
				  (mls_all_locations = 0 and mto_menu is not null) 
				) and ";
		}
		$sql .= "
			bl_client = $this->client_identifier and 
			bl_identifier = $id and 
			bl_status =1
		";
		//print "<li>1".$sql."</li>";
		$out  ="<module name=\"".$this->module_name."\" display=\"banner\">";
		$result  = $this->parent->db_pointer->database_query($sql);
		$num_rows  = $this->parent->db_pointer->database_num_rows($result);
		if($num_rows==0){
			if($bl_homepage_exception==1){
				$sql = "select * from banner_list 
							inner join banner_types on bl_type = bt_identifier and bt_client = bl_client 
							inner join banner_entry_grouping on bl_identifier = beg_list and beg_client = bl_client 
							inner join banner_entry on be_identifier = beg_banner and beg_client = be_client and be_status =1 and 
								(
									(be_max_click_through = -1 or be_max_click_through>be_cur_click_through) and 
									(be_max_page_impressions = -1 or be_max_page_impressions>be_cur_page_impressions) and 
									(be_date_starts = '0000-00-00 00:00:00' or be_date_starts < '".date("Y-m-d H:i:s")."') and 
									(be_date_expires = '0000-00-00 00:00:00' or be_date_expires > '".date("Y-m-d H:i:s")."')
								)
							left outer join menu_to_object on mto_menu=$cml and mto_module ='BANNER_ENTRY' and mto_object=be_identifier
							inner join file_to_object on fto_object = be_identifier and fto_client=be_client
							inner join file_info on fto_file = file_identifier and fto_client=file_client 
							inner join menu_location_settings on be_client = mls_client and mls_link_id = be_identifier and mls_module='BANNER_ENTRY' 
						 	where 
								(
									  (mls_all_locations = 1 and mto_menu is null)  or 
									  (mls_all_locations = 0 and mto_menu is not null) 
								) and 
								bl_client = $this->client_identifier and 
								bl_identifier = $id and 
								bl_status =1";
				//print "<li>2".$sql."</li>";
				$result  = $this->parent->db_pointer->database_query($sql);
				$num_rows  = $this->parent->db_pointer->database_num_rows($result);
				if($num_rows==0){
					return "";
				}
			} else {
				return "";
			}
		}
//		for ($i=0;$i<50;$i++){
//			$randIndex = rand(0,3);
//			print "[0..3 = $randIndex]";
//		}

//		$this->call_command("DB_SEEK",Array($result,$randomIndex));
		$be_identifier=-1;
		$log_id = -1;

//       	print "[$bl_open_in_window, $bl_direction, $bl_number_to_display]";
		$record = Array();// store the list of banners
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$c = count($record);
			$record[$c] = Array();
			// entry details
        	$record[$c]["be_identifier"]	= $r["be_identifier"];
			$record[$c]["be_txt_label"]		= $r["be_txt_label"];
			$record[$c]["be_txt_des1"]		= $r["be_txt_des1"];
			$record[$c]["be_txt_des2"]		= $r["be_txt_des2"];
			$record[$c]["be_txt_url"]		= $r["be_txt_url"];
			$record[$c]["be_random_toggle"]	= $r["be_random_toggle"];
			//file details
			$record[$c]["file_md5_tag"]		= $this->check_parameters($r, "file_md5_tag");
			$record[$c]["file_name"]		= $this->check_parameters($r, "file_name");
			$record[$c]["file_label"]		= $this->check_parameters($r, "file_label");
			$record[$c]["file_width"]		= $this->check_parameters($r, "file_width");
			$record[$c]["file_height"]		= $this->check_parameters($r, "file_height");
			//type details
			$record[$c]["bt_width"]			= $r["bt_width"];
			$record[$c]["bt_height"]		= $r["bt_height"];
		}

		if (($c+1) < $bl_number_to_display){
			$bl_number_to_display = $c+1;
		}
		/*
		else {
			$randomIndex = rand($bl_number_to_display-1,$num_rows -1);
			$c=$randomIndex;
			$s = $c;
		}
		*/
		$alreadyshown = array();
		for( $i=0; $i<$bl_number_to_display; $i++){
			$c = $this->getRandomValue(0,$num_rows -1,$alreadyshown);
			//print "<li>$i of $bl_number_to_display = $c, $num_rows</li>";			
			$be_identifier = $record[$c]["be_identifier"];
			$log_id = $be_identifier;
			$randomToggle =rand(0,1);
			if ($this->check_parameters($record[$c],"file_name")=="" || ($record[$c]["be_random_toggle"]==1 && $randomToggle == 1)){
				$format=1; // text = 1, gfx =0
    	    	$out .= "<banner type='txt' identifier='".$record[$c]["be_identifier"]."' open_new_window='$bl_open_in_window' direction='$bl_direction'>";
					$out .= "<label><![CDATA[".$record[$c]["be_txt_label"]."]]></label>";
					$out .= "<text><![CDATA[".$record[$c]["be_txt_des1"]."]]></text>";
					$out .= "<text><![CDATA[".$record[$c]["be_txt_des2"]."]]></text>";
					$out .= "<homepage><![CDATA[".$record[$c]["be_txt_url"]."]]></homepage>";
	        	$out .= "</banner>";
			} else {
				$format=0; // text = 1, gfx =0
    	    	$out .= "<banner type='gfx' identifier='".$record[$c]["be_identifier"]."' open_new_window='$bl_open_in_window' direction='$bl_direction'>";
					$out .= "<src><![CDATA[uploads/".$record[$c]["file_md5_tag"].$this->file_extension($record[$c]["file_name"])."]]></src>";
					$out .= "<longdesc><![CDATA[-/-file-download.php?identifier=".$record[$c]["file_md5_tag"]."]]></longdesc>";
					$out .= "<alt><![CDATA[".$record[$c]["file_label"]."]]></alt>";
					$out .= "<width><![CDATA[".(($record[$c]["file_width"]<$record[$c]["bt_width"])?$record[$c]["file_width"]:$record[$c]["bt_width"])."]]></width>";
					$out .= "<height><![CDATA[".(($record[$c]["file_height"]<$record[$c]["bt_height"])?$record[$c]["file_height"]:$record[$c]["bt_height"])."]]></height>";
					$out .= "<label><![CDATA[".$record[$c]["be_txt_label"]."]]></label>";
					$out .= "<text><![CDATA[".$record[$c]["be_txt_des1"]."]]></text>";
					$out .= "<text><![CDATA[".$record[$c]["be_txt_des2"]."]]></text>";
					$out .= "<homepage><![CDATA[".$record[$c]["be_txt_url"]."]]></homepage>";
	        	$out .= "</banner>";
			}
			if(!$this->parent->is_bot){
				/*************************************************************************************************************************
            	* if not a bot then log it
        	    *************************************************************************************************************************/
				$sql = "update banner_entry set be_cur_page_impressions = (be_cur_page_impressions + 1) where be_identifier = ".$log_id." and be_client = $this->client_identifier and be_status =1";
				$this->parent->db_pointer->database_query($sql);
				$sql = "select * from banner_log where bnrlog_day ='".Date("Y-m-d")."' and bnrlog_client = $this->client_identifier and bnrlog_banner=$be_identifier and bnrlog_format='$format'";
				$result  = $this->parent->db_pointer->database_query($sql);
				$num_rows2  = $this->parent->db_pointer->database_num_rows($result);
            	$this->parent->db_pointer->database_free_result($result);
				if($num_rows2==0){
					$sql = "insert into banner_log (bnrlog_day, bnrlog_format, bnrlog_client, bnrlog_pages, bnrlog_clicks, bnrlog_banner) values ('".Date("Y-m-d")."', $format,  $this->client_identifier, 1,0,$log_id)";
				} else {
					$sql = "update banner_log set bnrlog_pages=bnrlog_pages+1 where bnrlog_day='".Date("Y-m-d")." 00:00:00' and bnrlog_client = $this->client_identifier and bnrlog_banner = $log_id and bnrlog_format='$format'";
				}
				$this->parent->db_pointer->database_query($sql);
			}
			$alreadyshown[count($alreadyshown)] = $c;
			/*
			$c++;
			if($c>$num_rows){
				$c=0;
			}
			
			if($s == $c){
				break;
			}
			*/
        }
        $this->parent->db_pointer->database_free_result($result);
		$out .="</module>";
//		if($bl_number_to_display!=1){
//			print str_replace(Array("<",">"), Array("&lt;","&gt;"),$out);
//			$this->exitprogram();
//		}
		return $out;
	}
	/*************************************************************************************************************************
    * get a random value between start and end and it should not be in alreadyshown array
	* 
	*************************************************************************************************************************/
	function getRandomValue($start, $end, $alreadyshown){
		$randomIndex = rand($start,$end);
		if (!in_array($randomIndex,$alreadyshown)){
			return $randomIndex;
		}
		else{
			return $this->getRandomValue($start, $end, $alreadyshown);
		}
		
	}
	/*************************************************************************************************************************
    * record the click of a banner
	* 
	* a banner has been clicked extract the destination URL also update the record with a seperate sql statement so that
	* all possible click throughs are recorded.
    *************************************************************************************************************************/
	function module_click($parameters){
		$ad = $this->check_parameters($parameters,"ad",-1);
		$format = $this->check_parameters($parameters,"f",0);
		if(!is_numeric($format) || !is_numeric($ad)){
			return "";
		}
		$sql = "select * from banner_entry where be_identifier = $ad and be_client = $this->client_identifier and be_status =1";
		$result  = $this->parent->db_pointer->database_query($sql);
		$url = "";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$url = $r["be_url"];
        }
        $this->parent->db_pointer->database_free_result($result);
		if($url==""){
			header("Location: http://".$this->parent->domain.$this->parent->base."index.php");
		} else {
			/*************************************************************************************************************************
            * if a bot then do not log redirect
            *************************************************************************************************************************/
			if($ad!=-1 && !$this->parent->is_bot){
				$sql = "update banner_entry set be_cur_click_through = (be_cur_click_through + 1) where be_identifier = $ad and be_client = $this->client_identifier and be_status =1";
				$this->parent->db_pointer->database_query($sql);
				$sql = "update banner_log set bnrlog_clicks=bnrlog_clicks+1 where bnrlog_format='$format' and bnrlog_day='".Date("Y-m-d")." 00:00:00' and bnrlog_client = $this->client_identifier and bnrlog_banner = $ad";
				$this->parent->db_pointer->database_query($sql);
			}
			header("Location: ".$url);
		}
		$this->exitprogram();
	}
}
?>