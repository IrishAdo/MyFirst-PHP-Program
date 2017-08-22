<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.information_presentation.php
* @date 12 Feb 2004
*/
/**
* This is the presentation module for the information directory it allows the website to extract directory information
*
* Featured List Output
* Directory Navigation Output
* Directory Search Ouptut
* Directory Adminsitration from web site
*/
class information_presentation extends module{
	/**#@+
	* Class Variables
    *
    * @access private
    * @var string
	*/
	var $module_load_type			= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_grouping			= "";
	var $module_name_label			= "Information Directory Manager Module (Presentation)";
	var $module_name				= "information_presentation";
	var $module_presentation_name	= "information_presentation";
	var $module_admin				= "0";
	var $module_command				= "INFORMATION_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "INFORMATIONADMIN_";
	var $webAdmin					= "INFORMATIONADMIN_";
	var $module_label				= "";
	var $module_modify	 			= '$Date: 2005/03/02 09:24:01 $';
	var $module_version 			= '$Revision: 1.95 $';
	var $module_creation 			= "26/02/2004";
	var $shop_type					= "INFORMATION";
	/*************************************************************************************************************************
    * extra commands defined by
    *************************************************************************************************************************/
	var $extra_commands 			= Array();
	var $loadedcat					= Array();
	/**#@+
	*  Class Variables
    *
    * @access private
    * @var Integer
	*/
	var $searched					= 0;
	var $basicSearch 				= 0;
	var $advancedSearch 			= 0;
	var $search_page_size			= 50;
	/**#@+
	*  Class Variables
    *
    * @access private
    * @var Array
	*/
    var $displayed_directories      = Array();
	var $fields                     = Array();
	var $display_options			= Array();
	var $a2z						= 0;

	var $metadata_fields = Array();
    /**
	* Call command
    *
    * @uses $this->call_command("function name", Array(Associative array of parameters));
    * @param String command to be called
    * @param Array parameter list to be sent to the desired function
	*/
	function command($user_command, $parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/**
		* check that the command that is to be called starts with the modules starter definition
		*/
//		print "<li>".__FILE__.", ".__LINE__." -> $user_command</li>";
        if (strpos($user_command, $this->module_command)===0){
            /*************************************************************************************************************************
            * default functions
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
	        *                       D I R E C T O R Y   P R E S E N T A T I O N   F U N C T I O N S
        	*************************************************************************************************************************/
			if ($user_command==$this->module_command."TEST_QUERY"){
				return $this->call_command($this->webAdmin."TEST_QUERY",$parameter_list);
			}
			if ($user_command==$this->module_command."GEN_SQL_CACHE"){
				return $this->gen_sql_cache($parameter_list);
			}
			if ($user_command==$this->module_command."UPDATE_RSS"){
				return $this->update_rss($parameter_list);
			}
			
	        /*************************************************************************************************************************
	        *                       D I R E C T O R Y   P R E S E N T A T I O N   F U N C T I O N S
        	*************************************************************************************************************************/
		    if ($user_command==$this->module_command."DISPLAY" && $this->searched==0){
				return $this->information_display($parameter_list);
			}
			if ($user_command==$this->module_command."SHOW"){
				return $this->information_show($parameter_list);
			}
			if ($user_command==$this->module_command."SHOW_IT"){
				return $this->information_show_it($parameter_list);
			}
			if ($user_command==$this->module_command."GET_A2Z"){
				return $this->widget_atoz($parameter_list);
			}
			
            /*************************************************************************************************************************
	        *                     D I R E C T O R Y   W E B   M A N A G E M E N T   F U N C T I O N S
        	*************************************************************************************************************************/
            if (($user_command==$this->module_command."ADD_ENTRY")|| ($user_command==$this->module_command."EDIT_ENTRY")){
				return $this->information_modify($parameter_list);
			}
			if  ($user_command==$this->module_command."SAVE_ENTRY"){
				return $this->information_save($parameter_list);
			}
			if ($user_command==$this->module_command."VERIFY"){
				return $this->information_verify($parameter_list);
			}
			if ($user_command==$this->module_command."DISCARD"){
				return $this->information_discard($parameter_list);
			}
			if ($user_command==$this->module_command."EMAIL_VERIFICATION_REQUEST"){
				return $this->information_email_request($parameter_list);
			}
			/*************************************************************************************************************************
            *                            D I R E C T O R Y   W E B   S E A R C H   F U N C T I O N S
            *************************************************************************************************************************/
            if ($user_command==$this->module_command."SEARCH"){
				$cmd  = $this->check_parameters($parameter_list,"command");
				if($cmd==$this->module_command."SEARCH"){
					$this->searched = $this->basicSearch;
					$parameter_list["search_type"] = "basic";
				} else {
					$this->searched = $this->basicSearch;
					$parameter_list = Array("search_type"=> "basic");
				}
				$out = $this->information_search($parameter_list);
				$this->basicSearch = $this->searched;
				return $out;
			}
			if ($user_command==$this->module_command."ADVANCED_SEARCH"){
				$this->searched = $this->advancedSearch;
				$parameter_list["command"]=$this->module_command."ADVANCED_SEARCH";
				$parameter_list["search_type"] = "advanced";
				$out = $this->information_search($parameter_list);
				$this->advancedSearch = $this->searched;
				return $out;
			}
            /*************************************************************************************************************************
            *                                D I R E C T O R Y   W E B   F E A T U R E   L I S T S
        	*************************************************************************************************************************/
            if ($user_command==$this->module_command."FEATURES"){
				return $this->featured_list($parameter_list);
			}
			if ($user_command==$this->module_command."FEATURE_PREVIEW"){
				return $this->featured_preview($parameter_list);
			}
			if ($user_command==$this->module_command."RSS_EXTRACT"){
				return $this->featured_list_rss($parameter_list);
			}
            /*************************************************************************************************************************
            *                                    D I R E C T O R Y   W E B   A 2 Z   O U T P U T
            *************************************************************************************************************************/
            if($user_command==$this->module_command."A2Z"){
                return $this->display_atoz($parameter_list);
            }
            /*************************************************************************************************************************
            *                     A D V A N C E D   F O R M   B U I L D E R   F O R M   S A V E   F U N C T I O N S
            *************************************************************************************************************************/
            if($user_command==$this->module_command."SAVE_FBA"){
                return $this->save_fba($parameter_list);
            }
            if($user_command==$this->module_command."LOAD_FBA"){
                return $this->load_fba($parameter_list);
            }
			$len = count($this->extra_commands);
			for($i=0;$i<$len;$i++){
				if($user_command==$this->extra_commands[$i][0]){
					$out ="";
					eval("\$out =  \$this->".$this->extra_commands[$i][1]."(\$parameter_list);");
					return $out;
				}
			}
		}
		return "";
	}
	/*************************************************************************************************************************
    *                                D I R E C T O R Y   S E T U P   F U N C T I O N S
	*************************************************************************************************************************/

	/**
	* Initialise function
	*
	* This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	* module::initialise() function allowing you to define any extra constructor
	* functionality.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		/*************************************************************************************************************************
		* retrieve the metadata fields
		*************************************************************************************************************************/
		$this->metadata_fields	= $this->call_command("METADATAADMIN_GET_FIELDLIST", Array());
		return 1;
	}
	/*
	*                       D I R E C T O R Y   P R E S E N T A T I O N   F U N C T I O N S
	*/

	/**
    * Display the information directory
    *
	* This function will return the page as requested by the user it uses special cached webpages to know
	* what category and what entry to display, these pages remove the need for passing parameters in the URL
	*
    * @param Array ("fake_uri", "category", "identifier", "page" => 1 )
    * @return String the string is an XMLstring holding the xml data which represents this page in the directory
	*/
	function information_display($parameters){
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"parameters",__LINE__,"".print_r($parameters,true).""));}
		$cmd 			= $this->check_parameters($parameters,"command","");
		$fake_uri		= $this->check_parameters($parameters,"fake_uri","");
		$page			= $this->check_parameters($parameters,"page",1);
		$category		= $this->check_parameters($parameters,"category",-1);
		$position_lay	= $this->check_parameters($parameters,"__layout_position","");
		$identifier 	= $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"wo_owner_id",-1));
        if($category==""){
            $category=-1;
        }
		if($cmd!="" && $cmd!=$this->module_command."DISPLAY"){
			return "";
		}
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$months = Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		$ids= Array();
		/**
        * exit function if already displayed
        */
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"loading directory ",__LINE__,print_r($this->displayed_directories,true)));}
        if (in_array($identifier, $this->displayed_directories)){
            return "";
        }
        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"loading directory ",__LINE__,"$identifier"));}
        $this->displayed_directories[count($this->displayed_directories)] = $identifier;

		$info_identifier=-1;
		$work_status= 0;
		if ($fake_uri==""){
			$fake_uri = $this->parent->script;
		}
		$info_add_label			= "";
		$info_no_stock_label	= "";
		$info_no_stock_display	= "";
		$label					= "";
		$current_category 		= $category;
		$display_format			= "hide_categories";
		$display_columns		= "1";
		$info_summary_only		= 0;
		$date_list = Array();
		/*
        * select directory information for this url
		*/
		$sql = "select information_list.*, menu_data.menu_url from information_list
					inner join menu_data on info_menu_location = menu_identifier
					where
						information_list.info_client=$this->client_identifier and
						menu_url = '".$fake_uri."' and
						information_list.info_status=1 and 
						info_identifier = $identifier and
						info_owner = '$this->webContainer'
						";

		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name, __FUNCTION__."::SQL", __LINE__, "$sql"));}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name, "category", __LINE__, "[$category]"));}
		$result = $this->parent->db_pointer->database_query($sql);
		$c=0;
		if ($this->call_command("DB_NUM_ROWS",array($result)) > 0){
			while (($c<1) && ($r = $this->parent->db_pointer->database_fetch_array($result) )){
				$c++;
				$label 					= $r["info_label"];
				$work_status			= $r["info_workflow_status"];
				$identifier				= $r["info_identifier"];
				$info_identifier		= $r["info_identifier"];
				//$info_update_access 	= $r["info_update_access"];
				$display_format 		= $r["info_display"];
				$display_columns		= $r["info_columns"];
				//$menu_url				= $r["menu_url"];
				$info_summary_layout	= $r["info_summary_layout"];
				$current_category		= $r["info_category"];
				$cat_label				= $r["info_cat_label"];
				$this->search_page_size = $r["info_searchresults"];
				$info_add_label			= $r["info_add_label"];
				$info_no_stock_label	= $r["info_no_stock_label"];
				$info_no_stock_display	= $r["info_no_stock_display"];
				$info_summary_only		= $r["info_summary_only"];
			}
		}
        $this->parent->db_pointer->database_free_result($result);
        if ($c==0){
            return "";
        }
		/**
        * retrieve a list of all identifiers that appear on this page in alphabetic order based on the ie_title field
        */
		$info_nodisplay_sql = "";
		if($info_no_stock_display==0){
			$info_nodisplay_sql = " md_quantity!=0 and";
		}
		$calendar = 0;
		
		if ($info_summary_layout == 0) {
			$calendar = 1;
		} 
		if (($info_summary_layout>3 || $info_summary_layout==2) && ($category!="" && $category!=-1)){
			if($info_summary_layout>3){
				$calendar =1;
			}

			$info_summary_layout=0;
		}
		
		if ($info_summary_layout<4){
			if( $display_format!="hide_categories"){
				$category_sql=" inner join category_to_object on (cto_object = ie_identifier and ie_client = cto_client and cto_module='".$this->webContainer."')  and (cto_clist = $category) ";
			}


	/** For Cruise or Mind associates Site to change Order By Column if field name is ' Order ' portion starts (Added By Muhammad Imran Mirza) **/
	
		$order_by_column = '';
		$iev_entry_values_str =  '';
	
		if (($this->parent->db_pointer->database == 'system_cruise_new' && $identifier == '105367289483993633') || ($this->parent->db_pointer->database == 'system_libertas' && $identifier == '120082049030309504')){
			$sql_order = "select information_fields.* from information_fields 
									inner join information_list on info_identifier = if_list 
									where if_client = $this->client_identifier and if_label = 'Order' and info_status=1 and if_screen=0";
				$result_order = $this->parent->db_pointer->database_query($sql_order);
				$number_of_records_order = $this->call_command("DB_NUM_ROWS",array($result_order));
				if ($number_of_records_order >= 1) {

					if ($this->parent->db_pointer->database == 'system_cruise_new' && $identifier == '105367289483993633')
						$order_by_field_name = 'ie_otext1';
					elseif ($this->parent->db_pointer->database == 'system_libertas' && $identifier == '120082049030309504')
						$order_by_field_name = 'ie_otext5';

					$iev_entry_values_str = "left outer join information_entry_values on iev_entry=ie_identifier and iev_field = '$order_by_field_name'";
//					$order_by_column = " order by CAST( SUBSTRING( iev_value, 4, length( iev_value ) -7 ) AS SIGNED )";
					$order_by_column = 1;
				}
		}//if system_cruise_new
			
/*			if ($order_by_column == "")
				$order_by_column = 'md_title';
*/
			if ($iev_entry_values_str == "")
				$iev_entry_values_str = "left outer join information_entry_values on iev_entry=ie_identifier and iev_field = 'ie_odateonly1'";
				
	/** For Cruise Site or Mind associates to change Order By Column if field name is ' Order ' portion ends (Added By Muhammad Imran Mirza) **/

/*			
			if ($this->parent->db_pointer->database == 'system_cruise_new' && $identifier == '105367289483993633'){
				$iev_entry_values_str = "left outer join information_entry_values on iev_entry=ie_identifier and iev_field = 'ie_otext1'";
			}else{
				$iev_entry_values_str = "left outer join information_entry_values on iev_entry=ie_identifier and iev_field = 'ie_odateonly1'";
			}
*/			
			$sql = "select distinct md_identifier, ie_parent, iua_user from information_entry
						$category_sql
						inner join metadata_details on md_link_id = ie_identifier and md_module='$this->webContainer' and md_client= ie_client
						$iev_entry_values_str 
						left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list
					where 
						ie_published=1 and
						ie_client=$this->client_identifier and 
						ie_list = $identifier and 
						$info_nodisplay_sql
						ie_status=1 and ie_cached=1 and ie_version_wip=1 ";

			if($calendar==0 && $order_by_column == ""){				
				//$sql .= "order by ie_uri";
				$sql .= "order by md_title";
			} else {
				if(($this->parent->db_pointer->database == 'system_ards' && ($this->parent->domain == 'ards-council.gov.uk') || $this->parent->domain == 'www.ards-council.gov.uk'))
					$sql .= "order by  CAST( SUBSTRING( iev_value, 4, length( iev_value ) -7 ) AS SIGNED ),md_date_remove";
				else
					$sql .= "order by SUBSTRING( iev_value, 4, length( iev_value ) -7 ),md_title";
//					$sql .= "order by  CAST( SUBSTRING( iev_value, 4, length( iev_value ) -7 ) AS SIGNED ),md_title";
			}
			//if ($_SERVER['REMOTE_ADDR'] == "61.5.139.12")			
			//print '<li> sql : '.$sql.'</li>';
		} else {
			/*************************************************************************************************************************
            * retrieve current date
            *************************************************************************************************************************/
			$date_now = $this->libertasGetDate();
			$y = date("Y",strtotime($date_now));
			$m = date("m",strtotime($date_now));
			$d = date("d",strtotime($date_now));
			/*************************************************************************************************************************
            * retrieve paramters
            *************************************************************************************************************************/
			$year	= $this->check_parameters($parameters,"y","__NOT_FOUND__");
			$month	= $this->check_parameters($parameters,"m","__NOT_FOUND__");
			$day	= $this->check_parameters($parameters,"d","__NOT_FOUND__");
			$query = 0;
			if($year=="__NOT_FOUND__"){
				$year = $y;
			} else {
				$query = 1;
			}
			if($month=="__NOT_FOUND__"){
				$month = $m;
			} else {
				$query = 1;
			}
			if($day=="__NOT_FOUND__"){
				$day = $d;
			} else {
				$query = 1;
			}
			$check1 = "$year/$month/$day";
			$check2 = "$y/$m/$d";
			if($query==1){
				$info_summary_layout=6;
			}
			if ($info_summary_layout==4){
				$sql = "select distinct year(md_date_remove) as yr, count(ie_parent) as total from information_entry
							inner join metadata_details on md_link_id = ie_identifier and md_module='$this->webContainer' and md_client= ie_client
							left outer join category_to_object on (cto_object = ie_identifier and ie_client = cto_client and cto_module='".$this->webContainer."')  and (cto_clist = $category)
							left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list
						where 
							ie_published=1 and ie_version_wip=1 and
							ie_client=$this->client_identifier and 
							ie_list = $identifier and 
							$info_nodisplay_sql
							ie_status=1
						group by year(md_date_remove) order by md_date_remove 
					  ";
				$result  = $this->parent->db_pointer->database_query($sql);
				while($r = $this->parent->db_pointer->database_fetch_array($result)){
                	$date_list[count($date_list)] = $r["yr"];
                }
                $this->parent->db_pointer->database_free_result($result);
				$now	= $this->libertasGetDate("Y/m/d");
				$next	= $this->libertasGetDate("Y/m/d" ,mktime(0, 0, 0, date("m"), date("d"), date("Y")+1));
				
				$sql = "select distinct md_identifier, ie_parent, iua_user from information_entry
							inner join metadata_details on md_link_id = ie_identifier and md_module='$this->webContainer' and md_client= ie_client";
				if ($category!=-1){
					$sql .="left outer join category_to_object on (cto_object = ie_identifier and ie_client = cto_client and cto_module='".$this->webContainer."')  and (cto_clist = $category)";
				}
				$sql .="	left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list
						where 
							md_date_remove >= '$now' and
							md_date_remove < '$next' and
							ie_published=1 and
							ie_client=$this->client_identifier and 
							ie_list = $identifier and 
							$info_nodisplay_sql
							ie_status=1
						order by md_date_remove
					  ";
			} else if($info_summary_layout==5){
				if($category==-1){
					$sql = "select distinct year(md_date_remove) as yr, month(md_date_remove) as mth, count(ie_parent) as total from information_entry
								inner join metadata_details on md_link_id = ie_identifier and md_module='$this->webContainer' and md_client= ie_client
								left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list
							where 
								ie_published=1 and ie_version_wip=1 and
								ie_client=$this->client_identifier and 
								ie_list = $identifier and 
								$info_nodisplay_sql
								ie_status=1
							group by year(md_date_remove) order by md_date_remove
						  ";
				} else {
					$sql = "select distinct year(md_date_remove) as yr, month(md_date_remove) as mth, count(ie_parent) as total from information_entry
								inner join metadata_details on md_link_id = ie_identifier and md_module='$this->webContainer' and md_client= ie_client
								left outer join category_to_object on (cto_object = ie_identifier and ie_client = cto_client and cto_module='".$this->webContainer."')  and (cto_clist = $category)
								left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list
							where 
								ie_published=1  and ie_version_wip=1 and
								ie_client=$this->client_identifier and 
								ie_list = $identifier and 
								$info_nodisplay_sql
								ie_status=1
							group by year(md_date_remove) order by md_date_remove
						  ";
				}
				
				$result  = $this->parent->db_pointer->database_query($sql);
				while($r = $this->parent->db_pointer->database_fetch_array($result)){
					$pos = count($date_list);
										//   1  2  3  4  5  6  7  8  9  10 11 12 
					$date_list[$pos] = Array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, "year"=>$r["yr"]);
					$date_list[$pos][$r["mth"] - 1]++;
                }
                $this->parent->db_pointer->database_free_result($result);
				$d = Date("j");
				$now	= $this->libertasGetDate("Y/m/1", mktime(0, 0, 0, $month, 1, $year));
//				print "[$d, $month+1, $year]";
				$next	= $this->libertasGetDate("Y/m/j", mktime(0, 0, 0, $month+1, $d, $year));
				$ym = $this->check_parameters($parameters,"ym","__NOT_FOUND__");
				if($ym=="__NOT_FOUND__"){
				$sql = "select distinct md_identifier, ie_parent, iua_user from information_entry
							inner join metadata_details on md_link_id = ie_identifier and md_module='$this->webContainer' and md_client= ie_client
							left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list
						where 
							md_date_remove >= '$now' and
							md_date_remove < '$next' and
							ie_published=1 and
							ie_client=$this->client_identifier and 
							ie_list = $identifier and 
							$info_nodisplay_sql
							ie_status=1
						order by md_date_remove 
					  ";
				} else {
					$dl = split("::",$ym);
				$sql = "select distinct md_identifier, ie_parent, iua_user from information_entry
							inner join metadata_details on md_link_id = ie_identifier and md_module='$this->webContainer' and md_client= ie_client
							left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list
						where 
							year(md_date_remove) = '".$dl[0]."' and
							month(md_date_remove) = '".$dl[1]."' and
							ie_published=1 and
							ie_client=$this->client_identifier and 
							ie_list = $identifier and 
							$info_nodisplay_sql
							ie_status=1
						order by md_date_remove 
					  ";
				}
 			} else if($info_summary_layout==6){
				$sql = "select distinct md_identifier, ie_parent, iua_user from information_entry
							inner join metadata_details on md_link_id = ie_identifier and md_module='$this->webContainer' and md_client= ie_client
							left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list
						where 
							md_date_remove >= '$year/$month/$day 00:00:00' and
							md_date_remove < '$year/$month/$day 23:59:59' and
							ie_published=1 and
							ie_client=$this->client_identifier and 
							ie_list = $identifier and 
							ie_status=1
						order by md_date_remove
					  ";
			}
		}
//		if(($this->parent->db_pointer->database == 'system_mfelt' && ($this->parent->domain == 'magherafelt.gov.uk') || $this->parent->domain == 'www.magherafelt.gov.uk'))
		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
	//		$this->exitprogram();
        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}

        $result = $this->parent->db_pointer->database_query($sql);
		$numRecords = $this->call_command("DB_NUM_ROWS",array($result));
		if ($numRecords==1){
			$r = $this->parent->db_pointer->database_fetch_array($result);
			$parameters["unset_identifier"] = $r["ie_parent"];
			return $this->information_show($parameters);
		}
        /**
        * load the desired category list
        */
        if ($category==-1){
            $category = $current_category;
        }

	    $cats = $this->call_command("CATEGORY_LOAD",Array("identifier" => $current_category, "category" => $category, "recache"=>0));
		$pages="";		
		$num_of_pages = 1;
//		$this->search_page_size=50;
		if($numRecords>$this->search_page_size){
			$num_of_pages = ceil($numRecords/$this->search_page_size);
		}
		if ($page>$num_of_pages){
			$page=1;
		}
		$b = 1;
		if($page>5){
			$b=$page-5;
		}
		$e = $b+10;
		if($b+10>$num_of_pages){
			$e = $num_of_pages;
		}
		if ($num_of_pages>1){
			for($i=0; $i<$num_of_pages; $i++){
//				if(($i+1)>$b && ($i+1)<$e){
					$pages.="<page>".($i+1)."</page>";
//				}
				$this->checkPage(
					"_page".($i+1).".php",
					dirname($this->parent->real_script),
					Array("page"=>($i+1),"category"=>$category),
					$this->module_command."DISPLAY",
					$label ." - page ".($i+1)
                );
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"_page".($i+1).".php"));}
			}
		}
		
		$start = 0 + (($page - 1) * $this->search_page_size);
		$end = $start+$this->search_page_size;
		if($end>$numRecords){
			$end=$numRecords;
		}
	    
		/**
        * start to display the results
        */
		/* Commited By Muhammad Imran Mirza */
		//$out  = "<module name=\"".$this->module_name."\" display=\"INFORMATION\">\n";	
		/* Commited By Muhammad Imran Mirza */
		$parameters["menu_url"] 		= dirname($this->parent->script)."/_search.php";
//		$out .= $this->filter($parameters, $info_identifier);
		$out ="<shop><![CDATA[".$this->shop_type."]]></shop>";			
		$out .= "<list>$identifier</list>";		
		/** START OF PAGING (Commited By Muhammad Imran Mirza) */
/*
				$out .= "<pagespancommon";
				$out .= " command=\"";
				$out .= $this->check_parameters($parameters,"command");
				$out .= "\" number_of_records=\"".($numRecords)."\" start=\"".($start+1)."\" finish=\"".($end)."\" current_page=\"$page\" number_of_pages=\"$num_of_pages\" page_size=\"".$this->search_page_size."\">\n";
				$qstr = str_replace("+"," ",$this->parent->qstr);
				$pos = strpos($qstr,"&page=$page");

				if ($pos===false){
					// do nothing
				} else {
					// remove page=$page
					$qstr = substr($qstr,0,$pos).substr($qstr,$pos+strlen("&page=$page"));
				}
				$out .= "<searchfilter><![CDATA[".$qstr."]]></searchfilter>";
				
				$out .= "<pages>\n";
				if ($page<=5){
					$filter_start	= 1;
				} else {
					$filter_start	= $page -5;
				}
				if($filter_start+10 > $num_of_pages){
					$filter_end		= $num_of_pages;
				} else {
					$filter_end		= $filter_start+9;
				}
				for($index=$filter_start;$index<=$filter_end;$index++){
					$out .= "<page>$index</page>\n";
				}
				$out .= "</pages></pagespancommon>\n";				
*/				
		/** END OF PAGING  (Commited By Muhammad Imran Mirza) */		
		
		
		$elert = $this->call_command("ENGINE_HAS_MODULE",array("ELERT_"));
		$out .= "<elert>$elert</elert>";
		$out .= "<link_to_real_url type='1'></link_to_real_url>";
		$out .= "<workflow>$work_status</workflow>";
		$out .= "<label><![CDATA[$label]]></label>\n";
		$out .= "<cat_label><![CDATA[$cat_label]]></cat_label>\n";
		$o = split("_",$this->webContainer);
		$out .= "<display_type><![CDATA[".$o[0]."]]></display_type>\n";
		$out .= "<display_format><![CDATA[$display_format]]></display_format>\n";
		$out .= "<display_columns><![CDATA[$display_columns]]></display_columns>\n";
		$out .= "<label><![CDATA[$label]]></label>\n";
//		$out .= "<update_access status='$info_update_access' />";
		$out .= "<info_add_label><![CDATA[$info_add_label]]></info_add_label>\n";
		$out .= "<info_no_stock_label><![CDATA[$info_no_stock_label]]></info_no_stock_label>\n";
		$out .= "<info_no_stock_display><![CDATA[$info_no_stock_display]]></info_no_stock_display>\n";
		$out .= "<fake_uri><![CDATA[$fake_uri]]></fake_uri>\n";
		if(($this->parent->db_pointer->database == 'system_ards' && ($this->parent->domain == 'ards-council.gov.uk') || $this->parent->domain == 'www.ards-council.gov.uk'))
				$out .= "<ards_domain_database>1</ards_domain_database>\n";
		if($info_summary_layout==4){
			$out .="<dates year='$year'>";
			for ($dindex =0; $dindex< count($date_list);$dindex++){
				$out .= "<year id='".$date_list[$dindex]."'/>";
			}
			$out .="</dates>";
		} else if($info_summary_layout==5){
			$out .="<dates  year='$year' month='$month'>";
			for ($yindex =0; $yindex< count($date_list);$yindex++){
				$out .= "<year id='".$date_list[$yindex]["year"]."'>";
				for ($mindex =0; $mindex< 12; $mindex++){
					$out .= "<month id='".$mindex."'><label><![CDATA[".$months[$mindex]."]]></label></month>";
				}
				$out .= "</year>";
			}
			$out .="</dates>";
		}else if($info_summary_layout==6){
			$out .="<dates>";
			for ($yindex =0; $yindex< count($date_list);$yindex++){
				$out .= "<year id='".$date_list[$yindex]."'>";
				for ($mindex =0; $mindex< count($date_list[$yindex]);$mindex++){
					$out .= "<month id='".$date_list[$yindex][$mindex]["id"]."'><label><![CDATA[".$date_list[$yindex][$mindex]["name"]."]]></label>";
					for ($dindex =0; $dindex< count($date_list[$yindex][$mindex]["days"]);$dindex++){
						$out .= "<day id='".$date_list[$yindex][$mindex]["days"][$dindex]."' />";
					}
					$out .= "</month>";
				}
				$out .= "</year>";
			}
			$out .="</dates>";
		}
		$out .= "<current_category show_sub='1'>$category</current_category>\n$cats";
		if ($category=="" || $category==-1){
			$category = $current_category;
		}
		/*
		$pages="";		
		$num_of_pages = 1;
//		$this->search_page_size=50;
		if($numRecords>$this->search_page_size){
			$num_of_pages = ceil($numRecords/$this->search_page_size);
		}
		if ($page>$num_of_pages){
			$page=1;
		}
		$b = 1;
		if($page>5){
			$b=$page-5;
		}
		$e = $b+10;
		if($b+10>$num_of_pages){
			$e = $num_of_pages;
		}
		if ($num_of_pages>1){
			for($i=0; $i<$num_of_pages; $i++){
				if(($i+1)>$b && ($i+1)<$e){
					$pages.="<page>".($i+1)."</page>";
				}
				$this->checkPage(
					"_page".($i+1).".php",
					dirname($this->parent->real_script),
					Array("page"=>($i+1),"category"=>$category),
					$this->module_command."DISPLAY",
					$label ." - page ".($i+1)
                );
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"_page".($i+1).".php"));}
			}
		}
		
		$start = 0 + (($page - 1) * $this->search_page_size);
		$end = $start+$this->search_page_size;
		if($end>$numRecords){
			$end=$numRecords;
		}
		*/
		if($start>0){
			$this->call_command("DB_SEEK",array($result,$start));
		}
						
		
		$pos = $start;
		$listOfEntries=Array();
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"get list"));}
		while (($r = $this->parent->db_pointer->database_fetch_array($result)) && ($pos<$end)){
			$listOfEntries[count($listOfEntries)]= Array(
				"parent"	=> $r["ie_parent"],
				"md_id"		=> $r["md_identifier"]
			);
			$pos++;
		}
//print_r($listOfEntries);
			////categories
			$sql = "select * from category_to_object 
							inner join category on cat_identifier = cto_clist and cat_client=cto_client
						where cto_client = $this->client_identifier";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				$result  = $this->parent->db_pointer->database_query($sql);
				while($r = $this->parent->db_pointer->database_fetch_array($result)){
					$cat[$r["cat_identifier"]] = Array("cat_label"=>$r["cat_label"],"cat_parent"=>$r["cat_parent"],"cat_list"=>$r["cat_list_id"],"path"=>"");
				}
				$cat_path = "";
				foreach($cat as $key => $catEntry){
					if($cat[$key]["path"]==""){
						$path = $this->get_path($cat[$key], $catlist);
						$cat[$key]["path"] = $path;
					} else {
						$path = $cat[$key]["path"];
					}
					$cat_path .= "<cat_path id='$key'><![CDATA[".dirname($this->parent->script)."/$path]]></cat_path>";
				}
				
//				echo $cat_path;
			/////categories


		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$editable=0;
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"loading"));}
        if ($numRecords > 0){
	        $out .= "	<content category='$category' editable='$editable'>\n";
	        $out .="<info list='$identifier'>
		                <display type='$info_summary_layout' summary_only='$info_summary_only' page_spanning='1'>";
		    if ($numRecords>1 || ($numRecords==1 && $info_summary_layout=1)){
			    $lang="en";
				$screen=1;
		    } else {
			    //load cache
			    $lang="en";
				$screen=2;
		    }
			/*
			print 663;
			if($screen==2){
//				information_presentation_17_en_1_content.xml
				$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$identifier."_content.xml";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
				if (file_exists($fname)){
					$out .= join("", file($fname));
				} else {
				}
			} else {
				$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$identifier."_summary.xml";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
				if (file_exists($fname)){
					$out .= join("", file($fname));
				} else {
					$fields = $this->get_field_defs($identifier);
					$out .= $this->display_screen($screen, $fields);
				}
			}
			*/
//			print __LINE__;
			$fields = $this->get_field_defs($identifier,$screen);
			$out .= $this->display_screen($screen, $fields, $identifier);

            $out.="	$cat_path</display>";
			//echo $out; 
			
	//	if(($this->parent->db_pointer->database == 'system_mfelt' && ($this->parent->domain == 'magherafelt.gov.uk') || $this->parent->domain == 'www.magherafelt.gov.uk'))
//			print_r($listOfEntries);
			$ids = $listOfEntries;
			
		$mths = Array("Janurary", "Feburary", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		$mths_abr = Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		$mths_cspell = str_replace("Janurary","January",$mths);
		$mths_cspell = str_replace("Feburary","February",$mths_cspell);

	        $out .= "<results>";
			$m_list_entries = count($listOfEntries);
			$lang="en";
			/**** Starts Event Listing Portion ****/
			if ($this->module_presentation_name == "events"){
			
			/**** Starts Loop to add file paths for event list( Added By Muhammad Imran )****/
			$list = $identifier;

			/*starts To sort by chronological order date */
			$listOfEntries_sort=Array();

			for($p_counter=0; $p_counter<$m_list_entries; $p_counter++){
				$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$listOfEntries[$p_counter]["parent"].".xml";
				if (file_exists($fname)){

						$file_contents = join("", file($fname));
						$pos = strpos($file_contents, "<field type='datetime' name='ie_odate1' visible='yes'>");
						if ($pos == "")
							$pos = strpos($file_contents, "<field type='date' name='ie_odateonly1' visible='yes'>");

						$str_field = substr($file_contents,$pos);
						$pos = strpos($str_field, ",");
						$date_start = substr($str_field,$pos+2,11);
						$date_start = str_replace($mths_abr,$mths_cspell,$date_start);
						$date_start = strtotime($date_start);
						
						$listOfEntries_sort[$date_start]= Array(
							"parent"		=> $listOfEntries[$p_counter]["parent"],
							"md_id"			=> $listOfEntries[$p_counter]["md_id"]
						);

				}
			}

			ksort($listOfEntries_sort);
			$listOfEntries_sort_final=Array();
			foreach ($listOfEntries_sort as $values){
				$listOfEntries_sort_final[count($listOfEntries_sort_final)]= Array(
					"parent"	=> $values["parent"],
					"md_id"		=> $values["md_id"]
				);
			}
			/*ends To sort by chronological order date */
//die;				
			$m_list_entries = count($listOfEntries_sort_final);

			for($p_counter=0; $p_counter<$m_list_entries; $p_counter++){
				$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$listOfEntries_sort_final[$p_counter]["parent"].".xml";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
				if (file_exists($fname)){
				
//						$out .= join("", file($fname));
					/*
					if (!$myxml=simplexml_load_file($fname)){
						echo 'Error reading the XML file';
						}
	//					print_r($myxml);die;
						foreach($myxml as $event_link){
							//echo '<br />FLE: ' . $event_link->value . '<br />';	
	//						echo $file_uri = $event_link->value;die;
							$file_uri = $event_link->value;
	//						echo $file_cat = $event_link->choosencategory;
	//						echo '<br>';
							if ($file_uri != "")
								$file_uri_var = $file_uri;
	//							$file_path = str_replace("index.php","",$script).$file_uri;
						}
					*/
						
						$category_identifier = "";
						$xml_file = file_get_contents($fname);
						// load as file
						$sitemap = new SimpleXMLElement($xml_file);
						foreach($sitemap as $values_var) {
						//    echo "Number: {$url['identifier']}: {$url->loc} - {$url->lastmod} - {$url->changefreq} - {$url->priority}\r\n";
							$category_identifier = "{$values_var['identifier']}";
						} 
						
						$category_path = "";
						foreach($cat as $key_cat => $catValue){
							if ($cat[$key_cat]["category_identifier"] == $category_identifier){
//									echo $cat[$key_cat]["category_identifier"].'<br>';
								$category_path = $cat[$key_cat]["path"];
							}
						}
						//$category_identifier;

						$str_date = strtotime($this->libertasGetDate("d F Y",strtotime($r["yr"]."-".$r["mth"]."-".$e_day)));

//					$out .= "<td style='color:#999999'><a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".(($pmd-($start-1))+$i)))." 'href='$file_path?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".(($pmd-($start-1))+$i)."</a></td>";
						
						//echo $str_date.'<br>';die;

						/**Get Start Date**/
						$file_contents = join("", file($fname));

						/* Get URI*/
						$pos_uri = strpos($file_contents, "<field name='uri' link='no' visible='no'><value>");
						$str_pos_uri = substr($file_contents,$pos_uri);
//							$pos_uri2 = strpos($str_pos_uri, "</value>");
						$end_value = strpos($str_pos_uri, "</value></field>");
						$file_uri_var = substr($str_pos_uri,$str_pos_uri+57,$end_value-57-3);
						/* Get URI*/

						$start_inc = 54;
						$end_inc = 31;
						$start_find = "<field type='datetime' name='ie_odate1' visible='yes'>";
						$pos = strpos($file_contents, $start_find);
						if ($pos == ""){
							$start_find = "<field type='date' name='ie_odateonly1' visible='yes'>";
							$pos = strpos($file_contents, $start_find);
							$end_inc = 16;
						}
						
							
//						echo $file_contents = $this->libertasGetDate("d F Y",strtotime(substr($file_contents,$pos+54,16)));

						////starts To show Date as 20 May 2008 portion for start date
						$start_changeable_contents = substr($file_contents,$pos+$start_inc,$end_inc);
						$start_changed_contents = $this->libertasGetDate("d F Y",strtotime($start_changeable_contents));
						////ends To show Date as 20 May 2008 portion for start date


						$str_field = substr($file_contents,$pos);
						$pos = strpos($str_field, ",");
						$date_start = substr($str_field,$pos+2,11);
						$date_start = str_replace($mths_abr,$mths_cspell,$date_start);
						$date_start = strtotime($date_start);

						/**Get End Date**/
						$end_find = "<field type='datetime' name='ie_odate2' visible='yes'>";
						$pos2 = strpos($str_field, $end_find);
						if ($pos2 == ""){
							$end_find = "<field type='date' name='ie_odateonly2' visible='yes'>";
							$pos2 = strpos($str_field, $end_find);
							//$end_inc = 16;
						}

						////starts To show Date as 20 May 2008 portion for end date
						$end_changeable_contents = substr($str_field,$pos2+$start_inc,$end_inc);
						$end_changed_contents = $this->libertasGetDate("d F Y",strtotime($end_changeable_contents));
						////ends To show Date as 20 May 2008 portion for end date


						$str_field2 = substr($str_field,$pos2);
						$pos2 = strpos($str_field2, ",");
						$date_end = substr($str_field2,$pos+2,11);
						//$month2 = monToMonth(substr($date_end,3,3));
						$date_end = str_replace($mths_abr,$mths_cspell,$date_end);
						$date_end = strtotime($date_end);

/*echo 'sta:'.$date_start.'<br>';
echo 'str:'.$str_date.'<br>';
echo 'end'.$date_end.'<br><br>';
*/
//							if ($date_start <= $str_date && $date_end >= $str_date)
						if ($date_end >= $str_date){
//								echo $listOfEntries[$p_counter]["parent"];
						$file_path = dirname($this->parent->script).'/'.$category_path.'/'.$file_uri_var;

//									$out .= "<a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".$index))."'href='$file_path?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".$index."</a>";

//echo $info_summary_layout.'<br>';
						
						////starts To show Date as 20 May 2008 portion for start date
						if ($info_summary_layout != "0") {
							$replaced_contents = str_replace($start_find.$start_changeable_contents,$start_find.$start_changed_contents,$file_contents);
							////starts To show Date as 20 May 2008 portion for end date
							$replaced_contents = str_replace($end_find.$end_changeable_contents,$end_find.$end_changed_contents,$replaced_contents);
							$out .= $replaced_contents;
						}else{
							$out .= join("", file($fname));						
						}
						//echo $out = $this->libertasGetDate("d F Y",strtotime(substr($file_contents,$pos+2,11)));
								
							}
							$str_date = "";
				}
			}
			/**** Ends Loop to add file paths for event list( Added By Muhammad Imran )****/

			/**** Ends Event Listing Portion ****/
			}else{#if not event list
			/**** Starts if other than Event Listing Portion ****/
				for($i=0; $i<$m_list_entries; $i++){
					$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$identifier."_".$lang."_".$listOfEntries[$i]["parent"].".xml";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
					if (file_exists($fname)){
	/*				
					$stra = str_replace('03-10-2007','asd',file($fname));
					$stra = str_replace('05-10-2007','bdf',$stra);
					$stra = str_replace('01-12-2007','aaaaaaaf',$stra);
						$out .= join("", $stra);
	*/				
						
						$out .= join("", file($fname));
						
					}
				}

			/**** Ends if other than Event Listing Portion ****/
			}

			//$out .= "		<pages>$pages</pages></results></info></content>\n";
			$out .= "		$pages</results></info></content>\n";			
			//echo $out;

		/** START OF PAGING (Added by Muhammad Imran Mirza) */
			if ($this->module_presentation_name != "events"){#Category path for event does not work
				$out_sub = $out;

				$out = "<data_list";
				$out .= " command=\"";
				$out .= $this->check_parameters($parameters,"command");
				$out .= "\" number_of_records=\"".($numRecords)."\" start=\"".($start+1)."\" finish=\"".($end)."\" current_page=\"$page\" number_of_pages=\"$num_of_pages\" page_size=\"".$this->search_page_size."\">\n";
				$qstr = str_replace("+"," ",$this->parent->qstr);
				$pos = strpos($qstr,"&page=$page");
				if ($pos===false){
					// do nothing
				} else {
					// remove page=$page
					$qstr = substr($qstr,0,$pos).substr($qstr,$pos+strlen("&page=$page"));
				}
				$out .= "<searchfilter><![CDATA[".$qstr."]]></searchfilter>";
				
				$out .= "<pages>\n";
				if ($page<=5){
					$filter_start	= 1;
				} else {
					$filter_start	= $page -5;
				}
				if($filter_start+10 > $num_of_pages){
					$filter_end		= $num_of_pages;
				} else {
					$filter_end		= $filter_start+9;
				}
				for($index=$filter_start;$index<=$filter_end;$index++){
					$out .= "<page>$index</page>\n";
				}
				$out .= "</pages>\n";
				$out .= $out_sub;
				$out .= "</data_list>\n";				
			}#Category path for event does not work
		/** END OF PAGING (Added by Muhammad Imran Mirza) */
			

		}
		$this->parent->db_pointer->database_free_result($result);
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"loaded"));}

		/** Added by Muhammad Imran Mirza */
//		print($out);
		$out  =	"<module name=\"".$this->module_name."\" display=\"INFORMATION\">$out</module>";
//		$out .= "</module>\n";
		/** Added by Muhammad Imran Mirza */

		return $out;
	}

	/**
    * Display a single entry from the directory
    *
    * @param Array ("fake_uri", "category", "unset_identifier/identifier")
    * @return String the string is an XMLstring holding the xml data which represents this page in the directory
	*/
	function information_show($parameters){
//	print "<li>".__FILE__."@".__LINE__."<p>".print_R($parameters,true)."</p></li>";
//		print_r($parameters);
        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"<pre>".print_r($parameters,true)."</pre>"));}
		$fake_uri	= $this->check_parameters($parameters,"fake_uri");
		$identifier	= $this->check_parameters($parameters,"unset_identifier",$this->check_parameters($parameters,"identifier",-1));
		$category	= $this->check_parameters($parameters,"category");
		$lang = "en";
		$out="";
		$md_identifier			= -1;
		$info_add_label			= "";
		$info_no_stock_label	= "";
		$info_no_stock_display	= "";
//		print $fake_uri." ".$identifier." ".$category;
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$info_summary_only=0;
		$out .="<module name=\"".$this->module_name."\" display=\"INFORMATION\">";
		$sql  = "select distinct md_identifier, cto_clist, information_list.*
					from information_list
				inner join information_entry on ie_list = info_identifier and ie_client=info_client
				inner join metadata_details on md_link_id = ie_identifier and ie_client= md_client and md_module='$this->webContainer'
				left outer join category_to_object on cto_object = ie_identifier and cto_client=ie_client
				where
					ie_parent = $identifier and
					ie_client = $this->client_identifier and 
					ie_published=1";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"<pre>".$sql."</pre>"));}
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result = $this->parent->db_pointer->database_query($sql);
		$c=0;
		$info_add_label				= "";
		$info_no_stock_label		= "";
		$info_no_stock_display		= "";
		$info_summary_only			=0;
		while (($r = $this->parent->db_pointer->database_fetch_array($result)) && $c==0){
			$ie_list		 		= $r["info_category"];
			$ie_id 					= $r["info_identifier"];
			$md_identifier			= $r["md_identifier"];
			$work_status			= $r["info_workflow_status"];
			$info_update_access		= $r["info_update_access"];
			$info_summary_layout	= $r["info_summary_layout"];
			$cat_label				= $r["info_cat_label"];
			$info_summary_only		= $r["info_summary_only"];
			$category 				= $r["cto_clist"];
			$info_add_label			= $r["info_add_label"];
			$info_no_stock_label	= $r["info_no_stock_label"];
			$info_no_stock_display	= $r["info_no_stock_display"];
			$c++;
		}
		
		$this->parent->db_pointer->database_free_result($result);
		$elert = $this->call_command("ENGINE_HAS_MODULE",array("ELERT_"));
		$parameters["menu_url"] 		= dirname($this->parent->script)."/_search.php";
//		$out .= $this->filter($parameters, $ie_id);
		$out .= "<shop><![CDATA[$this->shop_type]]></shop>";
		
		$out .= "<link_to_real_url type='1'></link_to_real_url>";
		$out .= "<elert>$elert</elert>";
		$out .= "<list>$ie_id</list>";
		$out .= "<workflow>$work_status</workflow>";
		$out .= "<cat_label><![CDATA[$cat_label]]></cat_label>\n";
		$out .= "<fake_uri><![CDATA[$fake_uri]]></fake_uri>\n";
		$out .= "<info_add_label><![CDATA[$info_add_label]]></info_add_label>\n";
		$out .= "<info_no_stock_label><![CDATA[$info_no_stock_label]]></info_no_stock_label>\n";
		$out .= "<info_no_stock_display><![CDATA[$info_no_stock_display]]></info_no_stock_display>\n";
		if($category == ""){
			$category = -1;
		}
		$out .= $this->call_command("CATEGORY_LOAD",Array("identifier" => $ie_list, "recache"=>0, "category"=>$category));
		$out .= "<current_category show_sub='0'>$category</current_category>\n";
		$out .= "<uri><![CDATA[".dirname($this->parent->script)."/]]></uri>";
		if(($this->parent->db_pointer->database == 'system_ards' && ($this->parent->domain == 'ards-council.gov.uk') || $this->parent->domain == 'www.ards-council.gov.uk'))
				$out .= "<ards_domain_database>2</ards_domain_database>\n";
		/*************************************************************************************************************************
        *	display a to z if required
        *************************************************************************************************************************/
		$a2zout="";
//			print __LINE__;
		if ($info_summary_layout==2){
			// if a2z layout get letters
	        $letters= Array(
	            "undefined"=>0, "a"=>0, "b"=>0, "c"=>0, "d"=>0, "e"=>0, "f"=>0, "g"=>0, "h"=>0, "i"=>0, "j"=>0, "k"=>0, "l"=>0, "m"=>0, "n"=>0, "o"=>0, "p"=>0, "q"=>0, "r"=>0, "s"=>0, "t"=>0, "u"=>0, "v"=>0, "w"=>0, "x"=>0, "y"=>0, "z"=>0
	        );
			$choosenletter="";
			$sql = "select mid(ie_uri,1,1) as letter, count(mid(ie_uri,1,1)) as total from information_entry
	                    where ie_status =1 and ie_cached=1 and ie_list = $ie_id  and ie_client = $this->client_identifier
	                group by mid(ie_uri,1,1)
	                order by letter";
	        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
	        while ($r = $this->parent->db_pointer->database_fetch_array($result)){
	            $letter = $r["letter"];
	            $total  = $r["total"];
	            //print "<li> $letter = $total </li>";
	            if ($letter>="a" && $letter<="z"){
	                $letters[$letter] = $total;
	            } else {
	                $letters["undefined"] += $total;
	            }
	        }
	        $this->parent->db_pointer->database_free_result($result);
	        /**
	        * output the a2z letters
	        */
	        $a2zout               .= "<letters choosenletter='$choosenletter'>";
	        $a2zout               .= "<letter count='".$letters["undefined"]."' lcase='undefined'>#</letter>";
			for ($index = 1 ; $index<=13;$index++){
				$a2zout           .= "<letter count='".$letters[chr($index+96)]."' lcase='".chr($index+96)."'>".chr($index+64)."</letter>";
			}
			$a2zout               .= "</letters>";
			$a2zout               .= "<letters>";
			for ($index = 14 ; $index<=26;$index++){
				$a2zout           .= "<letter count='".$letters[chr($index+96)]."' lcase='".chr($index+96)."'>".chr($index+64)."</letter>";
			}
			$a2zout               .= "</letters>";
			$out .=$a2zout;
		}
		/*************************************************************************************************************************
        * display the record of choice
        *************************************************************************************************************************/
		if ($info_update_access==2){
			$sql = "
				select distinct ie_parent, iua_user from information_entry
					left outer join information_update_access on iua_client=ie_client and iua_entry = ie_parent and iua_list = ie_list
				where ie_client=$this->client_identifier and ie_parent = $identifier and ie_list = $ie_id
				";
//			print "<p>$sql</p>";
			$result  = $this->parent->db_pointer->database_query($sql);
			$u_id = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1);
//			print "<p>$u_id</p>";
			$editable=0;
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				if ($this->check_parameters($r,"iua_user")==$u_id){
					$editable=1;
				}
			}
		}
		if ($info_update_access==0){
			$editable=0;
		}
		if ($info_update_access==1){
			$editable=1;
		}
//		print $editable;
		$this->parent->db_pointer->database_free_result($result);

		/* Starts Enquire Section to get productcode/productname(Balmoral : By Muhammad Imran)*/
		$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$ie_id."_".$lang."_".$identifier.".xml";
		if (file_exists($fname)){
/*			$xml_file = file_get_contents($fname);
			// load as file
			$sitemap = new SimpleXMLElement($xml_file);
			foreach($sitemap as $values_var) {
			//    echo "Number: {$url['identifier']}: {$url->loc} - {$url->lastmod} - {$url->changefreq} - {$url->priority}\r\n";
			//	$category_identifier = "{$values_var['identifier']}";
			print_r($values_var);
			} 
*/
			$file_complete = join("",file($fname));
			
			/** Starts Product Name Querystring ***/
			$pos_start = strpos($file_complete,"<field  type='CDATA' name='ie_title' visible='yes'><![CDATA[");
			$str_part = substr($file_complete,$pos_start);
			$pos_end = strpos($str_part,"]]></field>");

			$str_part = substr($str_part,0,$pos_end);
			$str_part_arr = explode("<![CDATA[",$str_part);
			$prodname_parsed = $str_part_arr[1];
			/** Ends Product Name Querystring ***/

			/** Starts Product Code Querystring ***/
			$pos_start = strpos($file_complete,"<field  type='CDATA' name='ie_otext3' visible='yes'><![CDATA[");
			$str_part = substr($file_complete,$pos_start);
			$pos_end = strpos($str_part,"]]></field>");

			$str_part = substr($str_part,0,$pos_end);
			$str_part_arr = explode("<![CDATA[",$str_part);
			$prodcode_parsed = $str_part_arr[1];
			/** Ends Product Code Querystring ***/

		}
		$prodcode = $prodcode_parsed;
		$prodname = $prodname_parsed;

		$out .= "<prodcode><![CDATA[$prodcode]]></prodcode>";
		$out .= "<prodname><![CDATA[$prodname]]></prodname>";
		/* Ends Enquire Section to get productcode/productname(Balmoral : By Muhammad Imran)*/

		$out .= "	<content category='$category' editable='$editable'><info list='$ie_id'><display summary_only='$info_summary_only'>\n";
		
		$fields = $this->get_field_defs($ie_id, 2);
		$out .= $this->display_screen(2, $fields, $ie_id);
/*		$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$ie_id."_content.xml";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
		if (file_exists($fname)){
			$out .= join("", file($fname));
		} else {
			$fields = $this->get_field_defs($ie_id);
			$out .= $this->display_screen(2, $fields);
		}
		*/
		$out .="</display><results>";
		$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$ie_id."_".$lang."_".$identifier.".xml";
		if (file_exists($fname)){
//		print "<li>".__FILE__."@".__LINE__."<p>$fname</p></li>";
			$out .= join("",file($fname));
/*			$out = str_replace("</entry>","<url>
      <loc>http://www.example.com/</loc>
      <lastmod>2005-01-01</lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.8</priority>
   </url>
</entry>",$out);
*/
//	        $a2zout .= "<fake_uri><![CDATA[".$fake_uri."]]></fake_uri><uri><![CDATA[$menu_uri]]></uri>";

		/* Starts Enquire Section to add Enquire Link to jump Enquire Form (Balmoral : By Muhammad Imran)*/
$out = str_replace("<field id='ie_otext2'  link='0' sumlabel='1' conlabel='1'><label><![CDATA[Sizes]]></label></field>","<field id='ie_otext2'  link='0' sumlabel='1' conlabel='1'><label><![CDATA[Sizes]]></label></field><field id='__add_to_enquire__'  link='0' sumlabel='1' conlabel='1'><!--label><![CDATA[Add to enquire]]></label--></field>",$out);

//echo $out;die;
//$out = str_replace("</entry>","<field id='__add_to_basket__'  link='0' sumlabel='1' conlabel='1'><label><![CDATA[Add to enquire]]></label></field></entry>",$out);
		/* Ends Enquire Section to add Enquire Link to jump Enquire Form (Balmoral : By Muhammad Imran)*/

//echo $out;die;
			$mname = $data_files."/metadata_".$this->client_identifier."_".$lang."_".$md_identifier.".xml";
//			print "<li>".__FILE__."@".__LINE__."<p>$mname</p></li>";
			if (file_exists($mname)){
				$out .= join("", file($mname));
			}
		}
		$out .= "	</results></info></content>\n";
		$out .="</module>";
//		print $out;
//		pritn __LINE__;
//		$this->exitprogram();
		return $out;
	}
	/**
    * Display a single entry from the directory
    *
    * @param Array ("fake_uri", "category", "unset_identifier/identifier")
    * @return String the string is an XMLstring holding the xml data which represents this page in the directory
	*/
	function information_show_it($parameters){
        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"<pre>".print_r($parameters,true)."</pre>"));}
		$info_list	= $this->check_parameters($parameters,"information_list",-1);
		$identifier	= $this->check_parameters($parameters,"unset_identifier",$this->check_parameters($parameters,"identifier",-1));
		if($identifier=="" || $identifier==-1){
//			print_r($_SESSION);
			$identifier= $this->check_parameters($_SESSION,"FEATURE_INDEX_$info_list",-1);
			if($identifier=="" || $identifier==-1){
				return "";
			}
		}
		$lang = "en";
		$out="";
		$info_add_label			= "";
		$info_no_stock_label	= "";
		$info_no_stock_display	= "";
//		print $fake_uri." ".$identifier." ".$category;
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$out .="<module name=\"".$this->module_name."\" display=\"INFORMATION\">";
		$info_summary_only=0;
		$sql  = "select distinct info_label, info_identifier, info_category, info_summary_only, info_cat_label, info_workflow_status, info_update_access, info_summary_layout, menu_url
					from information_list
				inner join information_entry on ie_list = info_identifier and ie_client=info_client
				inner join menu_data on info_client=menu_client and info_menu_location = menu_identifier
				where
					ie_parent = $identifier and
					ie_list = $info_list and 
					ie_client = $this->client_identifier";
//		print "<li>$sql</li>";
		$result = $this->parent->db_pointer->database_query($sql);
		$menu_url="";
		$link_to_real_url ="";
		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			$ie_list		 		= $r["info_category"];
			$ie_id 					= $r["info_identifier"];
			$work_status			= $r["info_workflow_status"];
			$info_update_access		= $r["info_update_access"];
			$info_summary_layout	= $r["info_summary_layout"];
			$info_summary_only		= $r["info_summary_only"];
			$cat_label				= $r["info_cat_label"];
			$fake_uri				= $r["menu_url"];
			$feature_url			=	"_feature-".$this->make_uri($r["info_label"]).".php";
			$menu_url 				=	$r["menu_url"];
        }
//		print "<li>$sql</li>";
        $this->parent->db_pointer->database_free_result($result);
		if($menu_url==""){
			$link_to_real_url = 0;
		}else{
			$link_to_real_url = $this->call_command("LAYOUTSITE_MENU_HAS_ACCESS", Array("fake_uri"=>$menu_url));
		}
//			print __LINE__;
		$sql = "select * from category_to_object where cto_object = $identifier and cto_module='".$this->webContainer."' and cto_client=$this->client_identifier";
//		print "<li>$sql</li>";
		$result  = $this->parent->db_pointer->database_query($sql);
		$category=-1;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$category = $r["cto_clist"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$elert = $this->call_command("ENGINE_HAS_MODULE",array("ELERT_"));
		$parameters["menu_url"] 		= dirname($this->parent->script)."/_search.php";
//		$out .= $this->filter($parameters, $ie_id);
		$out .="<shop><![CDATA[INFORMATION]]></shop>";
		$out .= "<elert>$elert</elert>";
		$out .= "<list>$info_list</list>";
//		$out .= "<workflow>$work_status</workflow>";
		$out .= "<cat_label><![CDATA[$cat_label]]></cat_label>\n";
		$out .= "<link_to_real_url type='$link_to_real_url'>$feature_url</link_to_real_url>";
		$out .= "<fake_uri><![CDATA[$fake_uri]]></fake_uri>\n";
		$out .= "<info_add_label><![CDATA[$info_add_label]]></info_add_label>\n";
		$out .= "<info_no_stock_label><![CDATA[$info_no_stock_label]]></info_no_stock_label>\n";
		$out .= "<info_no_stock_display><![CDATA[$info_no_stock_display]]></info_no_stock_display>\n";
		$out .= "<current_category show_sub='0'>$category</current_category>\n";
		$out .= $this->call_command("CATEGORY_LOAD",Array("identifier" => $ie_list, "recache"=>0, "category"=>$category));
		/*************************************************************************************************************************
        * do not display a to z 
        *************************************************************************************************************************/
		$a2zout="";
		/*************************************************************************************************************************
        * display the record of choice
        *************************************************************************************************************************/
		if ($info_update_access==2){
			$sql = "
				select distinct ie_parent, iua_user from information_entry
					left outer join information_update_access on iua_client=ie_client and iua_entry = ie_parent and iua_list = ie_list
				where ie_client=$this->client_identifier and ie_parent = $identifier and ie_list = $info_list
				";
//			print "<p>$sql</p>";
			$result  = $this->parent->db_pointer->database_query($sql);
			$u_id = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1);
///			print "<p>$u_id</p>";
			$editable=0;
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				if ($this->check_parameters($r,"iua_user")==$u_id){
					$editable=1;
				}
			}
		}
		if ($info_update_access==0){
			$editable=0;
		}
		if ($info_update_access==1){
			$editable=1;
		}
//		print $editable;
		$this->parent->db_pointer->database_free_result($result);
		$out .= "	<content category='$category' editable='$editable'><info list='$info_list'><display summary_only='$info_summary_only'>\n";
		$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$info_list."_content.xml";
		$fields = $this->get_field_defs($ie_id,2);
		$out.= $this->display_screen(2, $fields, $ie_id);
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
		if (file_exists($fname)){
//			$out .= join("", file($fname));
		} else {
		}
		$out .="</display><results>";
		$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$info_list."_".$lang."_".$identifier.".xml";
//		print $fname;
		if (file_exists($fname)){
			$out .= join("",file($fname));
		}
		$out .= "	</results></info></content>\n";
		$out .="</module>";
//		print $out;
//		$this->exitprogram();

		return $out;
	}

    /**
	*                     D I R E C T O R Y   W E B   M A N A G E M E N T   F U N C T I O N S
    */

	/**
    * allows the addition of new entries via the website based on workflow choosen for the directory
	*/
	function information_modify($parameters){
		$command			= $this->check_parameters($parameters,"command");
		if ($command==$this->module_command."ADD_ENTRY"){
			$list 				= $this->check_parameters($parameters,"identifier",-1);
			$form_label 		= LOCALE_ADD;
			$identifier=-1;
		} else {
			$form_label			= LOCALE_EDIT;
			$list 				= $this->check_parameters($parameters,"list",-1);
			$identifier			= $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"unset_identifier",-1));
		}
		/*************************************************************************************************************************
        * check to see if we are needing to use a form from the FORM builder
        *************************************************************************************************************************/
		$sql ="
		SELECT distinct formbuilder_settings.*, user_to_object.*, formbuilder_override.*
FROM `formbuilder_settings` 
inner join formbuilder_module_map on fbmm_setting = fbs_identifier 
inner join information_entry on ie_list = fbmm_link_id
inner join user_to_object on uto_object = fbs_identifier and uto_identifier = ie_user
inner join formbuilder_override on fbo_owner = fbs_identifier and fbo_command='USERS_SHOW_PROFILE_FORM'
where fbmm_link_id = $list and fbmm_client=$this->client_identifier and fbmm_module='$this->webContainer' and fbs_type=0 
and ie_parent = $identifier";
		$found=0;
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$fbs_identifier = $r["fbs_identifier"];
			$mod = str_replace("ADMIN_","_",$r["uto_module"]);
			$found=1;
        }
        $this->parent->db_pointer->database_free_result($result);
		if($found!=0){
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$this->parent->base."-profile.php"));
			$this->exitprogram();
		}
		
		$real_identifier	= -1;
		$parent				= -1;
		$prevcommand		= $this->check_parameters($parameters,"prevcommand",$command);
		$category			= $this->check_parameters($parameters,"category",-1);
//		$info_menu_location	= -1;
//		$information_listing= "";
		$info_category		= 0;
		$out="";
		/*
		* if the user is adding a new entry or editing a valid entry then ok should be true;
		*/
		/**
        * get metadata record
        */
		$sql ="select * from metadata_details where md_module = '$this->webContainer' and md_client=$this->client_identifier and md_link_id = $identifier";
        $result  = $this->parent->db_pointer->database_query($sql);
		$len = count($this->metadata_fields);
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			for($i=0; $i<$len;$i++){
				$this->metadata_fields[$i]["value"] = $r[$this->metadata_fields[$i]["key"]];
			}
        }
        $this->parent->db_pointer->database_free_result($result);
		$ok = 1;
		$wrk = 0;
		$current_rank=0;
		$sql = "select info_category, info_workflow_status, information_fields.* from information_fields
				inner join information_list on info_identifier = if_list
				where if_client = $this->client_identifier and if_list = $list and info_status=1 and if_screen=0 order by if_rank";
		$sql="select * from information_fields 
					left outer join information_entry_values on (iev_entry =$identifier and iev_field = if_name and if_list= iev_list and if_client=iev_client) 
					left outer join information_entry on (iev_entry = ie_identifier and ie_client = iev_client) 
					inner join information_list on (info_identifier = if_list)
			  where 
					if_client=$this->client_identifier and 
					if_screen=0 and 
					info_status=1 and 
					if_list = $list
					order by if_rank";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result = $this->parent->db_pointer->database_query($sql);
		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			$info_category = $r["info_category"];
			$this->fields[$r["if_name"]][0] = $r["if_label"];
			$this->fields[$r["if_name"]][1] = $current_rank;
			$this->fields[$r["if_name"]][3] = 1;
			$this->fields[$r["if_name"]][4] = $r["if_type"];
			$this->fields[$r["if_name"]]["value"] = "";
			$this->fields[$r["if_name"]]["specified"] = Array();
			$this->fields[$r["if_name"]]["error"] = 0;
			if ($identifier==-1){
				if (($r["if_type"] == "radio") || ($r["if_type"] == "select") || ($r["if_type"] == "list") || ($r["if_type"] == "check")){
					$sql = "select * from information_options where io_client=$this->client_identifier and io_field='".$r["if_name"]."' and io_list= ".$r["if_list"]." order by io_rank";
					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$this->fields[$r["if_name"]]["value"] .= "<option><![CDATA[" . urldecode($option_r["io_value"]) . "]]></option>";
//						print $option_r["io_value"]." ".urldecode($option_r["io_value"]);
					}
					$this->parent->db_pointer->database_free_result($option_result);
				}
			}
			$wrk = $r["info_workflow_status"];
			$current_rank++;
		}
		if ($wrk == 0){
			$ok = 0;
		} else {
			if ($wrk == 1 || $wrk == 5){
				$ok = 1;
			} else {
				if ($this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0)!=0){
					$ok = 1;
				} else {
					$ok = 0;
				}
			}
		}
		if ($ok==1){
			if ($identifier!=-1){
				// edit
				$sql = "
						select * from information_entry
						where 
							ie_identifier=$identifier and 
							ie_client=$this->client_identifier
					";
				$result  = $this->parent->db_pointer->database_query($sql);
        	    while($r = $this->parent->db_pointer->database_fetch_array($result)){
    	        	$parent = $r["ie_parent"];
	            }
            	$this->parent->db_pointer->database_free_result($result);
				$sql = "select * from information_entry 
						where 
						ie_client = $this->client_identifier and ie_list = $list and ie_parent  = $identifier and ie_published=1 and ie_version_minor=0
						group by ie_version_major desc";
				$result  = $this->parent->db_pointer->database_query($sql);
				if($this->call_command("DB_NUM_ROWS",Array($result)) > 0){
	                $r 					= $this->parent->db_pointer->database_fetch_array($result);
                	$real_identifier	= $r["ie_identifier"];
    	        	$parent				= $r["ie_parent"];
                }
                $this->parent->db_pointer->database_free_result($result);
				$sql = "
				select * from information_entry 
					inner join information_fields on if_list = ie_list and if_client=ie_client and if_screen=0
					inner join metadata_details on md_module = '$this->webContainer' and md_client=ie_client and md_link_id = ie_identifier
					left outer join information_entry_values on  iev_field = if_name and if_client=iev_client and iev_entry = ie_identifier
				where 
					ie_identifier = $real_identifier and 
					ie_client = $this->client_identifier and 
					if_list = $list
				order by if_rank";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result = $this->parent->db_pointer->database_query($sql);
				while ($r = $this->parent->db_pointer->database_fetch_array($result)){
					$this->fields[$r["if_name"]][0] = $r["if_label"];
					$this->fields[$r["if_name"]][1] = $current_rank;
					$this->fields[$r["if_name"]][3] = 1;
					$this->fields[$r["if_name"]][4] = $r["if_type"];
					if($r["if_map"]!=""){
						$this->fields[$r["if_name"]]["value"] = $this->html_2_txt($r[$r["if_map"]]);
					} else {
						$this->fields[$r["if_name"]]["value"] = $this->html_2_txt($r["iev_value"]);
					}
					$this->fields[$r["if_name"]]["specified"] = Array();
					$this->fields[$r["if_name"]]["error"] = 0;
				}
			}
			if ($prevcommand!=$command){
				$errorarray = $this->check_parameters($parameters, "errorarray");
				foreach($this->fields as $key => $list){
					$this->fields[$key]["value"] = $this->check_parameters($parameters,$key);
					if (in_array($key,$errorarray)){
						$this->fields[$key]["error"] =1;
					}
				}
			}
			foreach($this->fields as $key => $value){
				if($this->fields[$key][4]=="select" || $this->fields[$key][4]=="radio" || $this->fields[$key][4]=="list" || $this->fields[$key][4]=="check"){
					$sql = "select * from information_options where io_list = $list and io_client = $this->client_identifier and io_field='$key'";
					$v = $this->fields[$key]["value"];
					$this->fields[$key]["value"]="";
					$result  = $this->parent->db_pointer->database_query($sql);
                    while($r = $this->parent->db_pointer->database_fetch_array($result)){
						$selected="";
						if($v == $r["io_value"]){
							$selected = " selected='true'";
						}
                    	$this->fields[$key]["value"] .= "<option value=\"".$r["io_value"]."\" $selected>".$r["io_value"]."</option>";
                    }
                    $this->parent->db_pointer->database_free_result($result);
				}
			}
			
			$sql = "select * from category_to_object where cto_object = $real_identifier and cto_client=$this->client_identifier and cto_module='INFORMATIONADMIN_'";
			$result  = $this->parent->db_pointer->database_query($sql);
			$list_of_categories="";
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$list_of_categories .="<input type=\"hidden\" name=\"entrycategory[]\" value=\"".$r["cto_clist"]."\" />";
            }
            $this->parent->db_pointer->database_free_result($result);
			$out 	  = "";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"OK",__LINE__,"$ok"));}
//			$info_menu_locations	= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($info_menu_location));
//			$information_listing 	= $this->call_command("CATEGORYADMIN_LIST_PRIMARY",Array("identifier"=>$info_category));
			$out .="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
			$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."SAVE_ENTRY\" />";
			$out .="		<input type=\"hidden\" name=\"prevcommand\" value=\"$prevcommand\" />";
			$out .="		<input type=\"hidden\" name=\"list\" value=\"$list\" />";
			/**
            * only show this field if editting existing entry
            */
			$out .=			$list_of_categories;
			$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$real_identifier\" />";
			$out .="		<input type=\"hidden\" name=\"parent_id\" value=\"$parent\" />";
			$out .="		<input type=\"hidden\" name=\"list_id\" value=\"$list\" />";
			/**
			*  List of available fields indexes
			-=-=-=-=-=-=-
			- Set Defaults for fields.
			-  0 = Field label,
			-  1 = Rank, 
			-  2 = Description, 
			-  3 = Selected, 
			-  4 = Type
			*/
//			$max_fields = count($this->fields);
//			print_r($this->fields);
			$out .="			<seperator_row><seperator>\n";
			foreach($this->fields as $key => $list){
				if($list["error"]==1){
					$error=" error='1' ";
				} else {
					$error="";
				}
				if ($list[3]==1){
					if($list[4]=="colsplitter"){
						$out .="			</seperator><seperator>\n";
					}
					if($list[4]=="rowsplitter"){
						$out .="			</seperator></seperator_row><seperator_row><seperator>\n";
					}
					if($list[4]=="text" || $list[4]=="URL" || $list[4]=="email" || $list[4]=="double"){
						$out .="			<input $error type=\"text\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\"><![CDATA[".$list["value"]."]]></input>\n";
					}
					if($list[4]=="smallmemo"){
						$out .="			<textarea $error type=\"text\" name=\"".$key."\" label=\"".$list[0]."\" size=\"20\" height=\"6\"><![CDATA[".$list["value"]."]]></textarea>\n";
					}
					if($list[4]=="memo"){
						$out .="			<textarea $error type=\"text\" name=\"".$key."\" label=\"".$list[0]."\" size=\"40\" height=\"12\"><![CDATA[".$list["value"]."]]></textarea>\n";
					}
					if($list[4]=="radio"){
						$out .="			<radio $error type='vertical' name=\"".$key."\" label=\"".$list[0]."\">".$list["value"]."</radio>\n";
					}
					if($list[4]=="select"){
						$out .="			<select $error name=\"".$key."\" label=\"".$list[0]."\">".$list["value"]."</select>\n";
					}
					if($list[4]=="check"){
						$out .="			<checkboxes $error type='vertical' name=\"".$key."\" label=\"".$list[0]."\">".$list["value"]."</checkboxes>\n";
					}
					if($list[4]=="list"){
						$out .="			<select $error multiple='1' size='10' name=\"".$key."\" label=\"".$list[0]."\">".$list["value"]."</select>\n";
					}
					if ($list[4]=="datetime"){
						$year_start = $this->check_prefs(Array("sp_combo_year"));
						$year_finish = Date("Y")+5;
						$out.= "            <input $error type=\"date_time\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\" value=\"".$this->check_parameters($list["specified"],0,$this->libertasGetDate("Y-m-d H:i:s"))."\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
					} else if ($list[4]=="date"){
						$year_start = $this->check_prefs(Array("sp_combo_year"));
						$year_finish = Date("Y")+5;
						$out.= "            <input $error type=\"date\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\" value=\"".$this->check_parameters($list["specified"],0,$this->libertasGetDate("Y-m-d H:i:s"))."\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
					} else if ($list[4]=="time"){
						$year_start = $this->check_prefs(Array("sp_combo_year"));
						$year_finish = Date("Y")+5;
						$out.= "            <input $error type=\"time\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\" value=\"".$this->check_parameters($list["specified"],0,$this->libertasGetDate("Y-m-d H:i:s"))."\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
					} 
				}
			}
			$out .="			</seperator></seperator_row><seperator_row><seperator>\n";
			$out .= "<select name='entrycategory' label='Category'><option value=\"\">Select One</option>".$this->call_command("CATEGORY_GET_OPTION_LIST", Array("id"=>$info_category, "selected"=>$category))."</select>";
			$out .="			</seperator></seperator_row>\n";
			$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
			$out .="	</form>";
			$out .="</module>";
		} else {
			$out .="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
			if ($wrk == 0){
				$out .="		<text><![CDATA[Sorry you are not able to publish to this directory from the web site]]></text>";
			} else {
				$out .="		<text><![CDATA[Sorry you must be logged into be able to publish to this directory]]></text>";
			}
			$out .="		<input type=\"submit\" iconify=\"CANCEL\" value=\"Return to Section\" />";
			$out .="	</form>";
			$out .="</module>";
		}
		return $out;
	}
	/**
	* save the web based form information 
	*/
	function information_save($parameters){	
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
	$old_ie_identifier 		= $this->check_parameters($parameters, "identifier",		$this->check_parameters($parameters, "ie_identifier",		-1));

		$ok = $this->checkDuplicates($parameters);
		$info_update_access=0;
		if ($ok==1){
			//print_r($parameters);
			$confirm = "Thank You,<br/><br/>Your entry has been submitted.";
			$identifier 		= $this->check_parameters($parameters, "identifier",		$this->check_parameters($parameters, "ie_identifier",		-1));
			$parent 			= $this->check_parameters($parameters, "parent_id",			$this->check_parameters($parameters, "ie_parent", 			-1));
			$list_id			= $this->check_parameters($parameters, "list_id",			$this->check_parameters($parameters, "module_identifier", 	-1));
			$category			= $this->check_parameters($parameters, "entrycategory",		Array());
			$just_create		= $this->check_parameters($parameters, "just_create", 		-1);
			$uid				= $this->check_parameters($parameters, "user_identifier", 	0);
			$frm_status			= $this->check_parameters($parameters, "ie_status",		 	0);
			$fbs_expires		= $this->check_parameters($parameters,"__expires__",		"0000-00-00 00:00:00");
			$fbs_grace			= $this->check_parameters($parameters,"__grace__",			"0000-00-00 00:00:00");
			$fbs_review			= $this->check_parameters($parameters,"__review__",			"0000-00-00 00:00:00");

			$menu_uri 			= $this->parent->script;
			$emailVerify		= 0;
			if ($identifier==-1){
				$cmd = "add";
			} else {
				$cmd = "edit";
			}
			
			$sql = "select 
						information_list.*, memo_information.mi_memo ,menu_data.menu_url
					from information_list 
						inner join menu_data on info_menu_location = menu_identifier and info_client=menu_client
						left outer join memo_information on (info_client=mi_client and mi_link_id = info_identifier and mi_type='$this->webContainer') 
					where 
						(mi_type='$this->webContainer' or mi_type is NULL) and
						info_identifier = $list_id and 
						info_client = $this->client_identifier";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result = $this->parent->db_pointer->database_query($sql);
			$wrk =0;
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$wrk 				= $r["info_workflow_status"];
				$confirm 			= $r["mi_memo"];
				$info_update_access	= $r["info_update_access"];
				$menu_uri			= $r["menu_url"];
			}
			$ie_status = $frm_status;
			if($frm_status==0){
				if ($wrk == 0){
					$ie_status = 0;
				} else {
					if ($this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0)!=0){
						if ($wrk == 1 || $wrk == 5 || $wrk == 3){
							$ie_status = 1;
						} else {
							$ie_status = 0;
						}
					} else {
						if ($wrk == 1 || $wrk == 5){
							$ie_status = 1;
							if ($wrk == 5){
								$emailVerify=1;
								$ie_status = 0;
							}
						} else {
							$ie_status = 0;
						}
					}
				}
			}  else {
				$ie_status = $frm_status;
			}
			/**
			* get metadata record info
			*/
			$sql ="select * from metadata_details where md_module = '$this->webContainer' and md_client=$this->client_identifier and md_link_id = $identifier";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result  = $this->parent->db_pointer->database_query($sql);
			$md_fields = Array();
			$len = count($this->metadata_fields);
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				for($i=0; $i<$len;$i++){
					$md_fields[$this->metadata_fields[$i]["key"]] = $r[$this->metadata_fields[$i]["key"]];
				}
			}
			$this->parent->db_pointer->database_free_result($result);
//			print $ie_status." ".$wrk;
			$sql = "select * from information_fields where if_client = $this->client_identifier and if_list = $list_id and if_screen=0 order by if_rank";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result = $this->parent->db_pointer->database_query($sql);
			$current_rank =1;
			$this->fields = Array();
			$title_set =0;
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$this->fields[$r["if_name"]][0] = $r["if_label"];
				$this->fields[$r["if_name"]][1] = $current_rank;
				$this->fields[$r["if_name"]][2] = $r["if_type"];
				if ($r["if_type"]=="list" || $r["if_type"]=="radio" || $r["if_type"]=="check" || $r["if_type"]=="select" || $r["if_type"]=="double"  || $r["if_type"]=="integer"){
					$this->fields[$r["if_name"]]["value"] = $this->check_parameters($parameters, $r["if_name"]);
				} else if ($r["if_type"]=="date" || $r["if_type"]=="datetime" || $r["if_type"]=="date_time" || $r["if_type"]=="time" ){
					$d = $this->check_date($parameters, $r["if_name"]);
					if($d==""){
						$d = $this->check_parameters($parameters, $r["if_name"]);
					}
					$this->fields[$r["if_name"]]["value"] = $d;
				} else if ($r["if_type"]=="URL" || $r["if_type"]=="text" ){
					$this->fields[$r["if_name"]]["value"] = htmlentities(strip_tags(html_entity_decode($this->moduletidy($this->txt2html($this->validate($this->check_locale_starter($this->check_parameters($parameters, $r["if_name"]))))))));
				}else {
					$this->fields[$r["if_name"]]["value"] = $this->moduletidy($this->txt2html($this->validate($this->check_locale_starter($this->check_parameters($parameters, $r["if_name"])))));
				}
				if($r["if_map"]!=""){
					if ($r["if_map"]=="md_title"){
						$md_fields[$r["if_map"]] = strip_tags($this->fields["ie_title"]["value"]);
					} else if ($r["if_type"]=="list" || $r["if_type"]=="radio" || $r["if_type"]=="check" || $r["if_type"]=="select"  || $r["if_type"]=="text" || $r["if_type"]=="double"  || $r["if_type"]=="integer"){
						$md_fields[$r["if_map"]] = strip_tags($this->fields[$r["if_name"]]["value"]);
					} else {
						$md_fields[$r["if_map"]] = $this->fields[$r["if_name"]]["value"];
					}
				}
				$current_rank++;
			}
//			print_r($this->fields);
//			$this->exitprogram();
//			print_r($md_fields);
			$major=0;
			$minor=1;
			$user=-1;
			if ($parent!=-1){
				$sql = "select * from information_entry where ie_parent = $parent and ie_client=$this->client_identifier
							order by ie_version_major desc, ie_version_minor desc;";
				$result  = $this->parent->db_pointer->database_query($sql);
				if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
		        	$r = $this->parent->db_pointer->database_fetch_array($result);
		        	$major = $r["ie_version_major"];
					$minor = $r["ie_version_minor"] +1 ;
					$user  = $r["ie_user"];
	        	}
		        $this->parent->db_pointer->database_free_result($result);
			}
			$this->parent->db_pointer->database_free_result($result);
			$ie_date_created = $this->libertasGetDate("Y/m/d H:i:s");
			if($emailVerify==1){
				$ie_uri = md5($ie_date_created);
			} else {
				$ie_uri = "";
			}
			$ie_identifier = $this->getUid();
			$identifier = $ie_identifier;
			if ($parent==-1 || $parent==""){
				$parent = $ie_identifier;
			}
			$longDescription="";
			if ($just_create==-1){
				$user_identy = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
			} else {
				if($user==-1){
					$user_identy = $uid;
				} else {
					$user_identy = $user;
				}
			}
			$sql = "insert into information_entry (ie_identifier, ie_parent, ie_client, ie_list, ie_date_created, ie_status, ie_user, ie_version_minor, ie_version_major, ie_version_wip, ie_published, ie_uri) values 
						($ie_identifier, $parent, $this->client_identifier, '$list_id', '$ie_date_created', '$ie_status', '".$user_identy."', $minor, $major, 1, $ie_status, '$ie_uri')";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->parent->db_pointer->database_query($sql);
			/**
			* version control set all other version to off line
			*/
			$sql 			= "update information_entry set ie_version_wip=0 where ie_identifier!=$ie_identifier and ie_parent=$parent and ie_client=$this->client_identifier";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$this->parent->db_pointer->database_query($sql);
			$cat_id_list	=	$category;
			/**
            * add metadata for this record
            */
//			print_r($md_fields);
			$this->call_command("METADATAADMIN_MODIFY", Array("identifier"=>$identifier, "module"=> $this->webContainer, "fields" => $md_fields, "command"=>"EDIT", "longDescription" => $longDescription));

			$this->call_command("CATEGORYADMIN_TO_OBJECT_UPDATE",
				Array(
					"data_list"		=> $cat_id_list,
					"module"		=> $this->webContainer,
					"identifier"	=> $ie_identifier
				)
			);
			$sql = "delete from information_entry_values where iev_client=$this->client_identifier and iev_entry=$ie_identifier ";
			$this->parent->db_pointer->database_query($sql);
			$_SESSION["IEV_BASKET_SESSION"]=$ie_identifier;
			foreach($this->fields as $key => $list){
				$iev_field	= $key;
				$mapto		= $this->check_parameters($list,"map");
				if($mapto==""){
					if (is_array($list["value"])){
						foreach($list["value"] as $k => $v){
							$iev_identifier = $this->getUid();
							$v = htmlentities(strip_tags(html_entity_decode($v)));
							$sql = "insert into information_entry_values (iev_identifier, iev_client, iev_entry, iev_field, iev_value, iev_list) values ($iev_identifier, '$this->client_identifier', '$ie_identifier', '$iev_field', '$v', '$list_id');";
//							print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
							if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
							$this->parent->db_pointer->database_query($sql);
						}
					} else {
						if ((substr($key,0,8)=="ie_omemo") || ($key == "ie_summary") || ($key == "ie_description") || ($key == "ie_content")){
							$iev_value = $list["value"];
						} else {
							$iev_value = htmlentities(strip_tags(html_entity_decode($list["value"])));
						}
						$iev_identifier = $this->getUid();
						$sql = "insert into information_entry_values (iev_identifier, iev_client, iev_entry, iev_field, iev_value, iev_list) values ($iev_identifier, '$this->client_identifier', '$identifier', '$iev_field', '$iev_value', '$list_id');";
//						print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
						$this->parent->db_pointer->database_query($sql);
						if ($key=="ie_summary"){
							$longDescription = $iev_value." ".$longDescription;
						} else if ($key=="ie_title"){
							$longDescription = $iev_value." ".$longDescription;
							$md_title 		= $iev_value;
						} else if ($key=="ie_description"){
							$longDescription = $iev_value." ".$longDescription;
						} else {
							$longDescription = $longDescription." ".$iev_value;
						}
					}
				}
			}
			if ($ie_status==1){
				$sql ="update information_entry set ie_version_wip =0 where ie_identifier!=$identifier and ie_client=$this->client_identifier and ie_parent=$parent and ie_list = $list_id";
				$this->parent->db_pointer->database_query($sql);
				$this->call_command("INFORMATIONADMIN_CACHE_ENTRY", Array(
						"identifier" => $identifier, 
						"old_ie_identifier" => $old_ie_identifier, 
						"list" => $list_id, 
						"url"=> $menu_uri
					)
				);
			}
			if ($emailVerify==1){
				// email joe bloggs to verify post.
				$out ="<module name=\"".$this->module_name."\" display=\"form\">
						<form name='user_form' label='Thank you'>
							<input type='hidden' name='command' value=\"".$this->module_command."EMAIL_VERIFICATION_REQUEST\"/>
							<input type='hidden' name='ie_uri' value='$ie_uri'/>
							<text><![CDATA[Thank you for taking the time to submit information to our site.]]></text>
							<text><![CDATA[Before this information can become available it must be verified please supply a valid email address which we will email you a link that will allow you to verify the post.]]></text>
							<text><![CDATA[You email address will not be stored in any way an email will simply just be sent to requesting verification.]]></text>
							<input type='text' label='Email address' name='emailaddress' value=''/>
							<input type='submit' icon='SAVE' value='".SAVE_DATA."'/>
							<text><![CDATA[<a href='" . $this->parent->real_script . "?command=".$this->module_command."DISCARD&amp;identifier=$ie_uri'>Cancel</a>]]></text>
						</form>
					</module>";
			} else {
				$out ="<module name=\"".$this->module_name."\" display=\"form\">
							<form name='user_form' label='Thank you'>
								<text><![CDATA[<p>$confirm</p>]]></text>
								<text><![CDATA[<p><a href='" . $this->parent->script . "'>Back</a></p>]]></text>
							</form>
						</module>";
			}
			if ($info_update_access==2){
				if($cmd == "add"){
					if($just_create==1){
						$user_id = $uid;
					} else {
						$user_id = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
					}
					if ($user_id==0){
						$iua_identifier = $this->getUid();
						$sql = "insert into information_update_access (iua_identifier, iua_entry, iua_client, iua_list, iua_user) values ($iua_identifier, $parent, $this->client_identifier, $list_id, $user_id)";
						$this->parent->db_pointer->database_query($sql);
					}
				}
			}
			if ($ie_status==1){
				$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_INFODIR__"], "identifier" => $identifier, "url"=> $this->parent->script));
			} else {
				$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_INFODIR_APPROVER__"], "identifier" => $identifier, "url"=> "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=INFORMATIONADMIN_LIST_ENTRIES&amp;status_filter=0&amp;identifier=$list_id"));
			}
//			$this->exitprogram();
		} else {
			$parameters["errorarray"] = $ok;
			$out = $this->information_modify($parameters);
		}
		return $out;
	}

	function get_path($catdata = Array(), $clist = Array()){
		/**
		* catdata	- holds category information for this record ("cat_label", "cat_parent", "cat_list", "path")
		* clist		- holds the complete list of existing categories
        */
		$counter = count($clist);
//		$path = "";
		$cat_parent = $this->check_parameters($catdata,"cat_parent",-1);
		$path = $this->make_uri($this->check_parameters($catdata,"cat_label"));
		$list = $this->check_parameters($catdata,"cat_list",-1);
		if ($cat_parent!=$list){
			for ($ci=0;$ci<$counter;$ci++){
				if($cat_parent==$this->check_parameters($this->check_parameters($clist,$ci,Array()),"cat_identifier",-2)){
					$clist[$ci]["cat_list"] = $list;
					$path = $this->get_path($clist[$ci],$clist). "/".$path;
				}
			}
		}
		return $path;
	}

	function information_verify($parameters){
		$ie_uri  = $this->check_parameters($parameters,"ie_uri");
		if ($ie_uri!=""){
			$sql ="select * from information_entry where ie_uri='$ie_uri' and ie_client=$this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			$ok=0;
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$ok=1;
            	$sql = "update information_entry set ie_uri='' where ie_uri='$ie_uri' and ie_client=$this->client_identifier and ie_identifier=".$r["ie_identifier"];
				$this->parent->db_pointer->database_query($sql);
            }
            $this->parent->db_pointer->database_free_result($result);
			if($ok==1){
			$out ="<module name=\"".$this->module_name."\" display=\"form\">
					<form name='user_form' label='Thank you'>
						<text><![CDATA[Thank you a the entry has been verified.]]></text>
					</form>
				</module>";
			} else {
			$out ="<module name=\"".$this->module_name."\" display=\"form\">
					<form name='user_form' label='Sorry there was a problem'>
						<text><![CDATA[Their was a problem with the verification marker that was supplied please check the email you recieved and try again.]]></text>
					</form>
				</module>";
			}
		}else{
			$out ="<module name=\"".$this->module_name."\" display=\"form\">
					<form name='user_form' label='Sorry there was a problem'>
						<text><![CDATA[The verification marker was not found please check the email you recieved and try again.]]></text>
					</form>
				</module>";
		}
		return $out;
	}
	function information_discard($parameters){
		$ie_uri  = $this->check_parameters($parameters,"ie_uri");
		if ($ie_uri!=""){
			$sql ="select * from information_entry where ie_uri = '$ie_uri' and ie_client=$this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			$ok = 0;
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$ok=1;
            	$sql = "delete from information_entry where ie_uri = '$ie_uri' and ie_client=$this->client_identifier and ie_identifier=".$r["ie_identifier"];
				$this->parent->db_pointer->database_query($sql);
            	$sql = "delete from information_entry_values where iev_client=$this->client_identifier and iev_entry=".$r["ie_identifier"];
				$this->parent->db_pointer->database_query($sql);
            }
            $this->parent->db_pointer->database_free_result($result);
			if($ok==1){
			$out ="<module name=\"".$this->module_name."\" display=\"form\">
					<form name='user_form' label='Thank you'>
						<text><![CDATA[Thank you a the entry has been discarded.]]></text>
					</form>
				</module>";
			} else {
			$out ="<module name=\"".$this->module_name."\" display=\"form\">
					<form name='user_form' label='Sorry there was a problem'>
						<text><![CDATA[Their was a problem with the verification marker or the entry has been removed by an administrator.]]></text>
					</form>
				</module>";
			}
		}else{
			$out ="<module name=\"".$this->module_name."\" display=\"form\">
					<form name='user_form' label='Sorry there was a problem'>
						<text><![CDATA[The verification marker was not found please check the email you recieved and try again.]]></text>
					</form>
				</module>";
		}
		return $out;
	}

	function information_email_request($parameters){
		
		$ie_uri  		= $this->check_parameters($parameters,"ie_uri");
		$emailaddress	= $this->check_parameters($parameters,"emailaddress");
		$sql = "select * from information_entry left outer join memo_information on mi_link_id = ie_list and ie_client=mi_client and mi_type='$this->webContainer' and mi_field='verifyemail') where ie_uri='$ie_uri' and ie_client= $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$emailverifycontent = $this->check_parameters($r,"mi_memo");
        }
        $this->parent->db_pointer->database_free_result($result);
		$emailbody		 = $emailverifycontent;
		$emailbody 		.=	"--------------------------------------------------------------------------\n";
		$emailbody 		.=	"This email address has been supplied by a person who submitted some content\n";
		$emailbody 		.=	"onto the http://".$this->parent->domain.$this->parent->base." website\n";
		$emailbody 		.=	"\n";
		$emailbody 		.=	"Please choose an action: -\n";
		$emailbody 		.=	"\n";
		$emailbody 		.=	"Verify the information that you submitted.\n";
		$emailbody 		.=	"http://".$this->parent->domain.$this->parent->base."index.php?command=".$this->module_command."VERIFY&ie_uri=$ie_uri\n";
		$emailbody 		.=	"\n";
		$emailbody 		.=	"Discard the information that you submitted.\n";
		$emailbody 		.=	"http://".$this->parent->domain.$this->parent->base."index.php?command=".$this->module_command."DISCARD&ie_uri=$ie_uri\n";
		$emailbody 		.=	"\n";
//		$emailbody 		.=	"Non verified content will be removed after 5 days\n";
		$emailbody 		.=	"\n";
		$emailbody 		.=	"--------------------------------------------------------------------------\n";
		$emailbody 		.=	"Some email programs break urls choose the url should be copied into a\n";
		$emailbody 		.=	"browser address bar before submitting\n";
		$emailbody 		.=	"--------------------------------------------------------------------------\n";
		$emailbody 		.=	"Your email address has not been recorded.\n";
		
		$this->call_command("EMAIL_QUICK_SEND",
			Array(
				"from"		=> "info@".$this->parseDomain($this->parent->domain),
				"to" 		=> "$emailaddress",
				"subject"	=> "Verification of content submitted to the ".$this->parent->domain." website",
				"body"		=> $emailbody
			)
		);
		
			$out ="<module name=\"".$this->module_name."\" display=\"form\">
					<form name='user_form' label='Thank you'>
						<text><![CDATA[Thank you a verification email has been sent to you and should arrive shortly.]]></text>
					</form>
				</module>";
		return $out;
	}
	function checkDuplicates($parameters){
		$duplicate_sql		= "";
		$list 				= $this->check_parameters($parameters,"list_id");
		$parent_id			= $this->check_parameters($parameters,"parent_id");
		$dlist = Array();
		$sql 	 = "select if_name, if_label, if_type, if_duplicate, if_map from information_fields 
					where 
						if_list=$list and 
						if_client=$this->client_identifier and 
						if_screen=0 and
						if_type not in ('rowsplitter','colsplitter')
				order by if_rank";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if ($r["if_duplicate"]!=""){
				$dlist[count($dlist)] = Array(
					"name"	=> $r["if_name"],
					"type"	=> $r["if_duplicate"]
				);
			}
		}
		$this->parent->db_pointer->database_free_result($result);
		$maxdup = count($dlist);
		for($start = 0; $start < $maxdup ; $start++){
			$duplicate_check = $this->check_parameters($dlist,$start,Array());
			$dupname = $this->check_parameters($duplicate_check,"name");
			$duptype = $this->check_parameters($duplicate_check,"type");
			$value 		= $this->validate(trim($this->check_parameters($parameters, $dupname, "")));
//			$field_name = $this->check_parameters($parameters,);
			if ($duplicate_sql!=""){
				$duplicate_sql .= " or ";
			}
			if ($duptype=='exact'){
				$duplicate_sql .= "(iev_field='$dupname' and iev_value='$value') ";
			}
			if ($duptype=='contains'){
				$duplicate_sql .= "(iev_field='$dupname' and iev_value like '%$value%') ";
			}
			if ($duptype=='startswith'){
				$duplicate_sql .= "(iev_field='$dupname' and iev_value like '$value%') ";
			}
		}

		$sql = "SELECT distinct iev_field from information_entry 
				inner join information_entry_values on ie_list = iev_list and iev_entry = ie_identifier 
				where ($duplicate_sql) and (ie_version_wip=1) and ie_parent=$parent_id and ie_client=$this->client_identifier and ie_list = $list";
		$result  = $this->parent->db_pointer->database_query($sql);
		$errorarray = Array();
		$error=0;
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$errorarray[count($errorarray)]=$r["iev_field"];
			$error=1;
		}
		$this->parent->db_pointer->database_free_result($result);
		if ($error==1){
			return $errorarray;
		} else {
			return 1;
		}
	}
	


	function gen_sql_cache($parameters){
		$match_list	= $this->check_parameters($parameters,"match_list");
		$block		= $this->check_parameters($parameters,"block");
		$list		= $this->check_parameters($parameters,"identifier", -1);
		$maps		= $this->check_parameters($parameters,"maps", Array());
		$order		= $this->check_parameters($parameters,"order", Array("field"=>"","dir"=>0));
		$blocklist	= split("\r\n",$block);
//		print "[".__LINE__."]";
//		print "lock::";
//		print_r($blocklist);
//		print "MACTH::";
//		print_r($match_list);
//		print "MAPS::";
//		print_r($maps);

		$where	= "";
		$join	= "";
		$max = count($blocklist)-1;
		$mc = count($maps);
//		print "[$max,$mc]";

		for($index = 0 ; $index <$max ; $index++){
			$blocklist[$index] = split(":::",$blocklist[$index]);
			$i = $blocklist[$index][2]*1;
			$ok =0;
			for($zi = 0; $zi<$mc;$zi++){
				if ($blocklist[$index][1]==$maps[$zi][0] && $maps[$zi][1]!=""){
//				print "(".$blocklist[$index][1]."==".$maps[$zi][0].")";
					$ok =1;
					$z = $zi;
					break;
				}
			}
//			print "<li>$index:: $ok</li>";
			if($ok==1){
				if($blocklist[$index][3]==0){
					$where .= " and ";
				} else {
					$where .= " or ";
				}
				$where .= " metadata_details.".$maps[$z][1]." ". str_replace(Array("[[value]]"), Array($blocklist[$index][4]) , trim($match_list[$i][1]))." ";		
			}else{
				if($blocklist[$index][3]==0){
					$join .= "inner join ";
				} else {
					$join .= "left outer join ";
				}
				$join .= " information_entry_values as iev".$index." on iev".$index.".iev_list = ie_list and ie_identifier = iev".$index.".iev_entry and ie_client=iev".$index.".iev_client and (iev".$index.".iev_field = '".$blocklist[$index][1]."' and iev".$index.".iev_value ". str_replace(Array("[[value]]"), Array($blocklist[$index][4]) , trim($match_list[$i][1])).") ";		
			}
		}
//		print_r(Array("join"=>$join,"where"=>$where));
//		$this->exitprogram();
		return Array("join"=>$join,"where"=>$where, "order"=>$order);
	}


	function filter($parameters, $identifier=0){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_filter",__LINE__,"[]"));
		}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Filter",__LINE__,"".print_r($parameters,true).""));}
//		$keys ="";
		if($identifier==0){
			$identifier 	= $this->check_parameters($parameters,"unset_identifier",$this->check_parameters($parameters,"identifier",$identifier));
		}
		$keywords		= $this->check_parameters($parameters,"keywords","");
		//$page_boolean	= $this->check_parameters($parameters,"page_boolean","or");
		$search 		= $this->check_parameters($parameters,"search",0);
		//$page_search 	= $this->check_parameters($parameters,"page_search");
		//$status_filter 	= $this->check_parameters($parameters,"status_filter",-1);
		$command		= $this->check_parameters($parameters,"command");
		$menu_url		= $this->check_parameters($parameters,"menu_url", $this->parent->real_script);
		if($command!=$this->module_command."ADVANCED_SEARCH"){
			$command=$this->module_command."SEARCH";
		}
		$search++;
//		$extra_fields = Array();
		$out = "<uid>".md5(uniqid(rand(), true))."</uid>\n";
		$out .= "<form name=\"filter_form_".md5(uniqid(rand(), true))."\" label=\"".FILTER_RESULTS."\" action='".$this->parent->base.$menu_url."' method=\"get\">\n";
		$out .= "<input type=\"hidden\" name=\"search\" ><![CDATA[2]]></input>\n";
		if($command==$this->module_command."ADVANCED_SEARCH"){
			/*
            * load from database so that we can set the previous values
            */
			$sql = "select * from information_fields 
				inner join information_list on if_list = info_identifier and if_client=info_client
			where if_client = $this->client_identifier and if_screen=3 and if_list=$identifier order by if_rank";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
            $result  = $this->parent->db_pointer->database_query($sql);
			$out .= "<seperator_row>\n<seperator>\n";
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$value = $this->check_parameters($parameters,$r["if_name"]);
				$info_category=$r["info_category"];
				if (($r["if_type"]=="select") || ($r["if_type"]=="radio") || ($r["if_type"]=="check") || ($r["if_type"]=="list")){
					$options="";
					$sql ="SELECT * FROM information_options where io_field='".$r["if_name"]."' and io_client=$this->client_identifier and io_list=$identifier order by io_rank";
                    $resultopt  = $this->parent->db_pointer->database_query($sql);
                    while($ropt = $this->parent->db_pointer->database_fetch_array($resultopt)){
                    	$options.="<option value=\"".htmlentities($ropt["io_value"], ENT_QUOTES)."\"";
						if($value ==$ropt["io_value"] ){
                    		$options.=" selected='true' ";
						}
                    	$options.=">".htmlentities($ropt["io_value"], ENT_QUOTES)."</option>";
                    }
					$this->parent->db_pointer->database_free_result($resultopt);
					$out .= "<select name='".$r["if_name"]."' label='".$r["if_label"]."'>\n<option value=''>Select One</option>\n$options</select>\n";
				} else if ($r["if_type"]=="colsplitter"){
					$out .= "</seperator>\n<seperator>\n";
				} else if ($r["if_type"]=="rowsplitter"){
					$out .= "</seperator>\n</seperator_row>\n<seperator_row>\n<seperator>\n";
				} else {
					if(substr($r["if_name"],0,2)!="__"){
						$out .= "<input type='text'  name='".$r["if_name"]."' label='".$r["if_label"]."'><value><![CDATA[".$this->check_parameters($parameters,$r["if_name"])."]]></value></input>\n";
					}
				}
            }
			$out .= "\n</seperator>\n</seperator_row>\n<seperator_row>\n<seperator>\n";
			$ie_category  = $this->check_parameters($parameters,"ie_category");
			$category_list = $this->call_command("CATEGORY_GET_OPTION_LIST", Array("id"=>$info_category, "selected"=>$ie_category));
			$out .= "<select name='ie_category' label='Category'><option value=''>Select One</option>";
			$out .= "<optiondata><![CDATA[".htmlentities($category_list)."]]></optiondata>";
			$out .= "<option_selected><![CDATA[".$ie_category."]]></option_selected>";
			$out .= "</select>";
			$out .= "<text><![CDATA[]]></text>";
			$out .= "</seperator></seperator_row>\n";
            $this->parent->db_pointer->database_free_result($result);
		} else {
			$out .="<input type=\"text\" adv=\"0\" name=\"keywords\" label=\"Search Keywords\" size=\"255\"><![CDATA[$keywords]]></input>\n";
		}
		$out .= "<input type=\"submit\" iconify=\"SEARCH\" name=\"\" value=\"".FILTER_RESULTS."\"/>\n";
		$out .= "</form>\n";
		return $out;
	}
    /**
    * search the directory and return results
    *
    * @param Array ("wo_owner_id", "current_menu_location")
    * @return String XmlString that represents the desired out put of this feature list
    */
	function information_search($parameters){
		$display_format 		= 1;
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"information_search",__LINE__,"".print_r($parameters,true).""));}
		$out					= "";
		$total_num_pages		= 0;
		$info_add_label			= "";
		$info_no_stock_label	= "";
		$info_no_stock_display	= "";
		$quantity 				= Array();
		$cat					= Array();
		$num_pages 				= 0;
		$dirout 				= "";
		$dout					= "";
		$outdisplay				= "";
		$start					= 0;
		$end					= 0;
		$shop_enabled 			= 0;
		$entry_ids				= Array();
		$cml 					= $this->check_parameters($parameters,"current_menu_location");
//		print_r($parameters);
//			print __LINE__;
		$search_directory_identifier=-1;
		if ($this->check_parameters($parameters,"field","__NOT_FOUND__")!="__NOT_FOUND__"){
			$parameters[$this->check_parameters($parameters,"field")] = $this->check_parameters($parameters,"filter");
		}
		$search_type  = $this->check_parameters($parameters,"search_type");
		$show_form = 0;
		//print "[$search_type]";
		if($search_type=="basic"){
			$sql = "
				select * from information_search
					inner join information_list on ibs_list = info_identifier and ibs_client = info_client
					inner join menu_data on info_menu_location = menu_identifier and info_client = menu_client
					left outer join menu_to_object on mto_client = ibs_client and mto_object = ibs_identifier and (mto_menu is null  or mto_menu=menu_identifier)
				where ibs_client = $this->client_identifier and 
					menu_data.menu_url ='".$this->parent->script."'
			";
            $result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	if ((is_null($r["mto_identifier"]) && $r["ibs_all_locations"]==1) || (!is_null($r["mto_identifier"]) && $r["ibs_all_locations"]==0)){
					$show_form 						= 1;
					$shop_enabled					= $r["info_shop_enabled"];
					$cat_label						= $r["info_cat_label"];
					$cat_list_identifier			= $r["info_category"];
					$search_directory_identifier	= $r["ibs_list"];
					$parameters["menu_url"] 		= dirname($r["menu_url"])."/_search.php";
					$info_add_label					= $r["info_add_label"];
					$info_no_stock_label			= $r["info_no_stock_label"];
					$info_no_stock_display			= $r["info_no_stock_display"];
				}
            }
            $this->parent->db_pointer->database_free_result($result);
		}
		if($search_type=="advanced"){
			$sql = "
				select * from information_advanced_search
					inner join information_list on ias_list = info_identifier and ias_client = info_client
					inner join menu_data on info_menu_location = menu_identifier and info_client = menu_client
					left outer join menu_to_object on mto_client = ias_client and mto_object = ias_identifier and (mto_menu is null or mto_menu=menu_identifier)
				where ias_client = $this->client_identifier and 
					menu_data.menu_url ='".$this->parent->script."'
			";
            $result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	if ((is_null($r["mto_identifier"]) && $r["ias_all_locations"]==1) || (!is_null($r["mto_identifier"]) && $r["ias_all_locations"]==0)){
					$show_form 						= 1;
					$search_directory_identifier	= $r["ias_list"];
					$cat_label						= $r["info_cat_label"];
					$cat_list_identifier			= $r["info_category"];
					$parameters["menu_url"] 		= dirname($r["menu_url"])."/_search.php";
					$info_add_label					= $r["info_add_label"];
					$info_no_stock_label			= $r["info_no_stock_label"];
					$info_no_stock_display			= $r["info_no_stock_display"];
				}
            }
            $this->parent->db_pointer->database_free_result($result);
		}
		$screen = 1;
		if($this->searched==0){
			$this->searched=1;
//			$list_category =-1;
			$keymap = Array();
			$sql = "select * from information_fields  
					inner join information_list on if_list = info_identifier and if_client = info_client
					inner join menu_data on info_menu_location = menu_data.menu_identifier and menu_data.menu_url = '".$this->parent->script."' and menu_client = info_client
				where if_client = $this->client_identifier and if_screen in (0,$screen) order by if_screen desc, if_rank
			";
			$result  = $this->parent->db_pointer->database_query($sql);
			$key_fields="";
			$keys =0;
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				if($r["if_screen"]==$screen){
					if($key_fields!=""){
						$key_fields .= ",";
					}
					$key_fields .= "'".$r["if_name"]."'";
	            	$keymap[$keys] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"]);
					$keys++;
				} else {
					if($r["if_screen"] == 0){
						for($i=0; $i<$keys; $i++){
							if ($keymap[$i][0]==$r["if_name"]){
								if($r["if_map"] != ""){
									$keymap[$i][1] = $r["if_map"];
								}
							}
						}
					}
				}
            }
	        $this->parent->db_pointer->database_free_result($result);
			$keywords	= $this->check_locale_starter($this->check_parameters($parameters,"keywords",$this->check_parameters($parameters,"search_phrase","")));
			$fake_uri	= $this->check_parameters($parameters,"fake_uri","");
			$page		= $this->check_parameters($parameters,"page",1);
			if ($fake_uri==""){
				$fake_uri = $this->parent->script;
			}
			$label="";
			$identifier = $this->check_parameters($parameters,"unset_identifier",
				$this->check_parameters($parameters,"wo_owner_id",
					$this->check_parameters($parameters,"identifier",	-1)
				)
			);
			if ($identifier==""){
				$identifier = $this->check_parameters($parameters,"wo_owner_id",
					$this->check_parameters($parameters,"identifier",	-1)
				);
			}
			$search	= $this->check_parameters($parameters,"search",	0);
			$search	= 1;
			$sql = "select info_category, menu_url, menu_identifier, if_name, if_list, information_list.* from information_fields
						inner join information_list on if_list = info_identifier and info_client=if_client
						inner join menu_data on menu_identifier = info_menu_location and menu_client = info_client
					where 
						if_client = $this->client_identifier and 
						if_screen=0 and 
						if_name not in ('ie_splitterCol', 'ie_splitterRow') and 
						info_identifier=$identifier
					";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
	        $result  = $this->parent->db_pointer->database_query($sql);
			$splitkeys = Array();
			$keys="__NOT_FOUND__";
			$info_display_format=0;
			$info_summary_only	=0;
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$this->searched					= 1;
				$keys							= "__FOUND__";
	        	$splitkeys[count($splitkeys)]	= $r["if_name"];
				$identifier 					= $r["if_list"];
				$menu_url 						= $r["menu_url"];
				$menu_identifier				= $r["menu_identifier"];
				$info_category					= $r["info_category"];
				$cat_label 						= $r["info_cat_label"];
				$info_add_label					= $r["info_add_label"];
				$info_no_stock_label			= $r["info_no_stock_label"];
				$info_no_stock_display			= $r["info_no_stock_display"];
				$info_display_format			= $r["info_summary_layout"];
				$info_summary_only				= $r["info_summary_only"];
	        }
			if($info_display_format==1)
				$display_format = 0;
			if($info_display_format==3)
				$display_format = 0;
			$extraCat="";
			if ($this->check_parameters($parameters,"ie_category",$this->check_parameters($parameters,"__category__","__NOT_FOUND__"))!="__NOT_FOUND__"){
				$extraCat						= $this->check_parameters($parameters,"ie_category",$this->check_parameters($parameters,"__category__","__NOT_FOUND__"));
				if ($extraCat!=""){
					$cat_list  = $this->call_command("CATEGORY_GET_CHILDREN",Array("rootNode"=>$extraCat,"info_category" => $info_category));
				}
			}
	        $this->parent->db_pointer->database_free_result($result);
			$settings = $this->call_command("SHOP_GET_SETTINGS");
			if ($keys!="__NOT_FOUND__"){
				$where		= "";
				$w ="";
				$or_where	= "";
				$values		= "";
					if ($keywords!=""){
						$klistArray = split(" ",$keywords);
						if(!is_array($klistArray)){
							$klist = Array();
							$klist[0] = $klistArray;
						} else {
							$klist = $klistArray;
						}
						$sql = "select
							distinct info_summary_layout, info_cat_label, info_identifier, info_searchresults, info_category, ie_parent , ie_identifier , sort_record.md_title  as sortrecord, info_shop_enabled, md_quantity, info_add_label, info_no_stock_label, info_no_stock_display
							from information_entry
								inner join information_fields on if_list = $identifier and $this->client_identifier = if_client and if_screen in(1,2)
								inner join information_list on ie_list = info_identifier and info_client = $this->client_identifier
								inner join information_entry_values as fielddata on fielddata.iev_list = $identifier and fielddata.iev_field=if_name and ie_identifier = fielddata.iev_entry";
								
						$sql .=	" inner join metadata_details as sort_record on sort_record.md_client = $this->client_identifier and sort_record.md_module='$this->webContainer' and ie_identifier = sort_record.md_link_id ";
						$sql .=	"where
							ie_list = $identifier and  ie_client = $this->client_identifier and ie_status=1 and ie_published =1 and ie_cached=1 and (
						";
						for($i=0; $i<count($klist);$i++){
							if($i!=0){
								$sql.=" or ";
							}
							$sql .= " fielddata.iev_value like '%".$klist[$i]."%'";
						}
						$sql.=") order by sort_record.md_date_remove,sortrecord";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					} else {
						if($keys!="__NOT_FOUND__"){
							if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"information_search",__LINE__,"".print_r($splitkeys,true).""));}
							for($spliti=0;$spliti < count($splitkeys);$spliti++){
								$test = $this->check_locale_starter($this->check_parameters($parameters,$splitkeys[$spliti],""));
								if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"information_search",__LINE__,"looking for ".$splitkeys[$spliti]." - $spliti [$test]"));}
								if($test!=""){
									$test = str_replace(Array("'"),Array("&#39;"),$test);
									if($splitkeys[$spliti]=="ie_title"){
									} else if($splitkeys[$spliti]=="__category__"){
									} else if(
										(substr($splitkeys[$spliti],0,9)=="ie_oradio") ||
										(substr($splitkeys[$spliti],0,10)=="ie_oselect") ||
										(substr($splitkeys[$spliti],0,11)=="ie_ocheckbox") ||
										(substr($splitkeys[$spliti],0,8)=="ie_olist")
									){
										$values .="inner join information_entry_values as iev".$spliti." on iev".$spliti.".iev_client = $this->client_identifier and (iev".$spliti.".iev_list = $identifier and iev".$spliti.".iev_entry = ie_identifier and iev".$spliti.".iev_field ='".$splitkeys[$spliti]."' and iev".$spliti.".iev_value like '%".str_replace(" ","%",$test)."%')\n";
										$w .= " and iev".$spliti.".iev_value like '%".str_replace(" ","%",$test)."%'";
									} else {
										$values .="inner join information_entry_values as iev".$spliti." on iev".$spliti.".iev_client = $this->client_identifier and (iev".$spliti.".iev_list = $identifier and iev".$spliti.".iev_entry = ie_identifier and iev".$spliti.".iev_field ='".$splitkeys[$spliti]."' and iev".$spliti.".iev_value like '%".str_replace(" ","%",$test)."%')\n";
										$w .= " and iev".$spliti.".iev_value like '%".$test."%'";
									}
								}
							}
						}
						$join ="";
						if($keywords!=""){
							$join .="
								inner join information_entry_values as cat_val on (cat_val.iev_list = $identifier and cat_val.iev_client=$this->client_identifier and cat_val.iev_entry = ie_identifier)
								left outer join category_to_object on (cto_object = cat_val.iev_entry and cat_val.iev_client = cto_client and cto_module='".$this->webContainer."')
								left outer join category on cto_clist = cat_identifier and cto_client=cat_client 
							";
							if ($or_where!=""){
							 $or_where = " or $or_where";
							}
							$where .= "(cat_label like '%$keywords%' or (cat_val.iev_value like '%$keywords%') $or_where) and ";
						} else {
							if ($or_where!=""){
								$where .= "($or_where) and ";
							}
							if ($extraCat!=""){
								$join .="
									inner join category_to_object on (cto_object = sort_record.md_link_id and cto_client=$this->client_identifier and cto_module='$this->webContainer' and cto_clist in ($cat_list))
								";
							}
						}
						/** If searching in upcoming events then add data criteria to sql */
						if (strstr($this->parent->script,"events/upcoming-events")){
							$future_date_clause = " and sort_record.md_date_remove>CURDATE() ";
						}						
						$title = $this->check_locale_starter($this->check_parameters($parameters, "ie_title"));
						if($title==""){
							$values =" inner join metadata_details as sort_record on sort_record.md_client = $this->client_identifier  and sort_record.md_module='$this->webContainer' and ie_identifier = sort_record.md_link_id ".$future_date_clause.$values;;
						} else {
							$values =" inner join metadata_details as sort_record on sort_record.md_client = $this->client_identifier  and sort_record.md_module='$this->webContainer' and ie_identifier = sort_record.md_link_id  and sort_record.md_title like '%".$this->check_locale_starter($this->check_parameters($parameters, "ie_title"))."%' ".$future_date_clause.$values;
						}
						$sql = "select
							distinct info_summary_layout, info_cat_label, info_identifier, info_searchresults, info_category, ie_parent , ie_identifier , sort_record.md_title  as sortrecord, info_shop_enabled, md_quantity, info_add_label, info_no_stock_label, info_no_stock_display
							from information_entry
								inner join information_fields on (if_list = $identifier and if_client=$this->client_identifier and if_screen = 0 )
								$values
								inner join information_list on info_identifier=$identifier and ie_client=info_client 
								$join
							where
							$where
							ie_list = $identifier and  ie_client = $this->client_identifier and ie_status=1 and ie_published =1 and ie_cached=1 and ie_version_wip =1 $w
							 order by sort_record.md_date_remove,sortrecord 
						";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
						
					}

				//print "</li>".$this->parent->script. " fake ".$this->fake_uri. "".$sql."</li>";							
				$parameters["menu_url"] 		= dirname($menu_url)."/_search.php";				
				$parameters["$menu_identifier"] = $menu_identifier;
				$info_summary_layout=0;
				if ($search>=1){
					if($search!=0){
						$result = $this->parent->db_pointer->database_query($sql);
					}else{
						$result = NULL;
					}
					if (!$result){
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
						}
						return "";
					}else{
						/**
                        * print no chacing headers
                        */
						// Date in the past
						//header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
						$timeheaderexpires = time() + 80;
						header("Expires: " . gmdate("D, d M Y H:i:s", $timeheaderexpires) . " GMT");
						
						// always modified
						header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
						 
						// HTTP/1.1
						//header("Cache-Control: no-store, no-cache, must-revalidate");
						//header("Cache-Control: post-check=0, pre-check=0", false);
						
						// HTTP/1.0
						//header("Pragma: no-cache");
						/**
                        *
                        */
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
						}
						$entry_list = Array();
	                    $num_of_records = $this->call_command("DB_NUM_ROWS", Array($result));
	                    if ($num_of_records>0){
						    $r = $this->parent->db_pointer->database_fetch_array($result);
	                        $this->search_page_size 	= $r["info_searchresults"];
							$shop_enabled				= $r["info_shop_enabled"];
							$list						= $r["info_identifier"];
							$clist						= $r["info_category"];
							$cat_label 					= $r["info_cat_label"];
							$info_summary_layout		= $r["info_summary_layout"];
							$info_add_label				= $r["info_add_label"];
							$info_no_stock_label		= $r["info_no_stock_label"];
							$info_no_stock_display		= $r["info_no_stock_display"];
	                        $this->call_command("DB_SEEK", Array($result,0));
					        if($page==1){
							    $start	= 0;
	                        	$end	= $this->search_page_size;
						    } else {
							    $start  = ($page - 1)*$this->search_page_size;
							    $end    = $start + ($this->search_page_size - 1 );
						    }
						    if ($end >= $num_of_records){
							    $end    = $num_of_records;
						    }
	                        if($start !=0){
	                            $this->call_command("DB_SEEK", Array($result,$start));
	                        }
	                        $pos = $start;
					        while (($r = $this->parent->db_pointer->database_fetch_array($result)) && ($pos <= $end-1)){
								if (($info_no_stock_display==0 && $r["md_quantity"]!=0) || $info_no_stock_display==1){
		                    	    $entry_list[count($entry_list)] = $r["ie_parent"];
		                    	    $entry_ids[count($entry_ids)] = $r["ie_identifier"];
	                            	$pos ++;
								}
	                        }
							if(count($entry_list)+$start<$end){
								$num_of_records = count($entry_list)+$start;
							}
	                    } else {
							// zero records
							$pos=0;
							$start=0;
						}
						$end=$pos;
	                    $this->parent->db_pointer->database_free_result($result);
						$complete_extract_list="";
						$display_list=Array();
						for ($i=0;$i<$this->search_page_size;$i++){
							if(($start+$i)<$end){
								if($complete_extract_list!=""){
									$complete_extract_list .=", ";
								}
								$complete_extract_list .= $entry_list[$i];
								$display_list[count($display_list)] = $entry_list[$i];
							}
						}
						$where ="";
						if($info_no_stock_display == 0){
							$where =" and md_quantity!=0 ";
						}
						// and if_type in ('text','select', 'radio', 'check', 'list')
						if($display_format==0){
							$sql = "select *
										from metadata_details 
										inner join information_entry on ie_client = md_client and ie_identifier = md_link_id and md_module='$this->webContainer'
											left outer join information_entry_values on ie_identifier = iev_entry and ie_client=iev_client and iev_list = ie_list
											left outer join information_fields on if_list = iev_list and if_client = iev_client and if_name=iev_field and if_screen =0 and if_name in ($key_fields)
											left outer join category_to_object on cto_module='$this->webContainer' and cto_client=ie_client and cto_object=ie_identifier
											left outer join category on cat_client = cto_client and cat_identifier = cto_clist
										where
											 ie_status=1 and ie_cached=1 and ie_published=1 and ie_parent in (".$complete_extract_list.") and ie_client=$this->client_identifier and ie_list=$identifier $where
										order by ie_parent, if_rank
							";

							if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		                    $result  = $this->parent->db_pointer->database_query($sql);
							$clist = $identifier;
							$dirout  = "<text><![CDATA[\n";
							if($info_summary_layout==1 || $info_summary_layout==4){
								$dirout .= "<script type=\"text/javascript\" src=\"/libertas_images3/javascripts/sortabletable.js\"><!-- load sortable table--></script>";
							}
							$dirout 		.= "<link type=\"text/css\" rel=\"StyleSheet\" href=\"/libertas_images3/themes/sortabletable.css\">";
							$dirout 		.= "<table class='sortable' cellspacing='0' cellpadding='0'>\n";
							$entrys			 = Array();
							$field_list		 = Array();
							$field_label	 = Array();
							$ids			 = Array();
							$c=0;
							$prev_parent = "";
							$max=0;
							while ($r = $this->parent->db_pointer->database_fetch_array($result)){
								if($prev_parent != $r["ie_parent"]){
									$prev_parent = $r["ie_parent"];
								}
								if(!isset($entrys[$r["ie_parent"]])){
									$entrys[$r["ie_parent"]] = Array();
									$entrys[$r["ie_parent"]]["uri"] = $r["ie_uri"];
									$quantity[$r["ie_parent"]] = $r["md_quantity"];
									$m=count($keymap);
									for($x=0;$x<$m;$x++){
										$entrys[$r["ie_parent"]][$keymap[$x][0]]=Array($this->check_parameters($r,$keymap[$x][1]),"text",0,"");
										if(!in_array($keymap[$x][0], $field_list)){
											$field_list[$keymap[$x][3]]		= $keymap[$x][0];
											if($keymap[$x][3]>$max){
												$max=$keymap[$x][3];
											}
											if($keymap[$x][0]=="colsplitter" || $keymap[$x][0]=="rowsplitter"){
											} else if($keymap[$x][0]!="__category__"){
												$field_label[$keymap[$x][3]]	= $keymap[$x][2];
											} else {
												$field_label[$keymap[$x][3]]	= $cat_label;
											}
										}
									}
									$ids[count($ids)]=$r["ie_identifier"];
								}
								if($r["if_name"]!=""){
									if(!in_array($r["if_name"], $field_list)){
										$field_list[$r["if_rank"]]	= $r["if_name"];
										$field_label[$r["if_rank"]]	= $r["if_label"];
									}
								}
								$cat[$r["cat_identifier"]] = Array("cat_label"=>$r["cat_label"],"cat_parent"=>$r["cat_parent"],"cat_list"=>$r["cat_list_id"],"path"=>"");
								$clist = $r["cat_list_id"];
//								print "<li>".$r["if_name"]."</li>";
//								if($r["if_name"]=="ie_title"){
//									$entrys[$r["ie_parent"]][$r["if_name"]] = Array("[".$r["md_title"]."]",$r["if_type"],$r["if_filterable"],$r["ie_uri"]);
//								} 
									if($this->check_parameters($r,"if_name","")!=""){
										$entrys[$r["ie_parent"]][$r["if_name"]] = Array($r["iev_value"],$r["if_type"],$r["if_filterable"],$r["ie_uri"]);
									}
								
								$entrys[$r["ie_parent"]]["cat"] = $r["cat_identifier"];
								$entrys[$r["ie_parent"]]["quantity"] = $r["md_quantity"];
								$entrys[$r["ie_parent"]]["canbuy"] = $r["md_canbuy"];
							}
							$catlist = $this->call_command("CATEGORY_LOAD",Array("identifier" => $clist, "recache"=>0, "return_array"=>1));
		                    $total_num_pages = ceil($num_of_records / $this->search_page_size) ;
//							print "[$total_num_pages = $num_of_records]";
//							print_r($entrys);
							$this->parent->db_pointer->database_free_result($result);
							$fc = count($field_label);
							$fields = $this->get_field_defs($identifier,0);
							$dirout .= "<tr>";
							//print "[$max]";
							for($i=0;$i<=$max;$i++){
								//print "<li>".$this->check_parameters($field_label,$i)."</li>";
								if($this->check_parameters($field_label,$i)!="colsplitter" && $this->check_parameters($field_label,$i)!="rowsplitter" && $this->check_parameters($field_label,$i)!=""){
									$dirout .= "<th>";
									$dirout .= $this->check_parameters($field_label,$i);
									$dirout .= "</th>";
								}
							}
							$dirout .= "</tr>";
							$c=1;
							$cl = count($display_list);
							for($index=0;$index<$cl;$index++){
								$dirout .= "<tr>";
								$key = $display_list[$index];
								$title_uri = $this->check_parameters($entrys[$key],"uri","");
								for($i=0;$i<=$max;$i++){
									if($this->check_parameters($field_label,$i)!="colsplitter" && $this->check_parameters($field_label,$i)!="rowsplitter" && $this->check_parameters($field_label,$i)!=""){
										$dirout .= "<td>";
										$label = $this->check_parameters( $this->check_parameters($entrys[$key],$field_list[$i],Array()),0,"[[nbsp]]");
										$uri = $this->check_parameters( $this->check_parameters($entrys[$key],$field_list[$i],Array()),3,"");
										$filter = $this->check_parameters( $this->check_parameters($entrys[$key],$field_list[$i],Array()),2,"0");
										if($field_list[$i]=="ie_title"){
											if($info_summary_only==0){ /// not summary only content (has content)
												if($cat[$entrys[$key]["cat"]]["path"]==""){
													$path = $this->get_path($cat[$entrys[$key]["cat"]], $catlist);
													$cat[$entrys[$key]["cat"]]["path"] = $path;
												} else {
													$path = $cat[$entrys[$key]["cat"]]["path"];
												}
												$dirout .= "<a title='View this entry' href='".dirname($this->parent->real_script)."/$path/".$title_uri."'>".$label."</a>";
											} else {
												$dirout .= "".$label."";
											}
										} else if($field_list[$i]=="ie_price"){
											$dirout .= "[[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]]".$label;
										} else {
											if($field_list[$i] == "__add_to_basket__"){
												$dirout .= "<a title='Add this item to the basket' href='_add-to-cart.php?identifier=".$ids[$index]."&amp;type=".$this->shop_type."'>$info_add_label</a>";
											} else if($field_list[$i] == "__category__"){
												$path = $this->get_path($cat[$entrys[$key]["cat"]], $catlist);
												$dirout .= "<a href='".dirname($this->parent->real_script)."/".$path."/index.php' title ='Go to the category $path'>".$cat[$entrys[$key]["cat"]]["cat_label"]."</a>";
											} else {
												if($filter=="1"){
													$dirout .= "<a href='".dirname($this->parent->real_script)."/_filter-".$this->make_uri($field_label[$i])."-".$this->make_uri($label).".php' title='Filter on (".$label.")'>".$label."</a>";
												} else {
													$dirout .= $label;
												}
											}
										}
										$dirout .= "</td>";
									}
								}
								$dirout .= "</tr>\n";
								$c++;
							}
							$dirout .= "</table>]]></text>";
							$outdisplay="results";
						} else {
	//					print_r($parameters);
							$outdisplay="results";
							$outdisplay="INFORMATION";
							$parameters["menu_url"] 		= dirname($this->parent->script)."/_search.php";
							$dout = "<list>$identifier</list>";
							$dout .= "<link_to_real_url type='1'></link_to_real_url>";
							$dout .= "<label><![CDATA[$label]]></label>\n";
							$dout .= "<cat_label><![CDATA[$cat_label]]></cat_label>\n";
							$o = split("_",$this->webContainer);
							$dout .= "<display_type><![CDATA[".$o[0]."]]></display_type>\n";
							$dout .= "<display_format><![CDATA[$display_format]]></display_format>\n";
							$dout .= "<display_columns><![CDATA[1]]></display_columns>\n";
							$dout .= "<label><![CDATA[$label]]></label>\n";
							$dout .= "<info_add_label><![CDATA[$info_add_label]]></info_add_label>\n";
							$dout .= "<info_no_stock_label><![CDATA[$info_no_stock_label]]></info_no_stock_label>\n";
							$dout .= "<info_no_stock_display><![CDATA[$info_no_stock_display]]></info_no_stock_display>\n";
						    $cats 	 = $this->call_command("CATEGORY_LOAD",Array("identifier" => $info_category, "category" => $info_category, "recache"=>0));
							$dout .= "<fake_uri><![CDATA[$fake_uri]]></fake_uri>\n$cats";
//							print "<li>".__FILE__."@".__LINE__."<p>".print_r($entry_list, true)."</p></li>";
//							$this->exitprogram();
							$catlist = $this->call_command("CATEGORY_LOAD",Array("identifier" => $info_category, "recache"=>0, "return_array"=>1));
							
//							print "<pre>".print_r($catlist,true)."</pre>";
							$sql = "select * from category_to_object 
										inner join category on cat_identifier = cto_clist and cat_client=cto_client
									where cto_object in (".join(", ",$entry_ids).") and cto_client = $this->client_identifier and cto_module='$this->webContainer'";
							//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
							$result  = $this->parent->db_pointer->database_query($sql);
                            while($r = $this->parent->db_pointer->database_fetch_array($result)){
                            	$cat[$r["cat_identifier"]] = Array("cat_label"=>$r["cat_label"],"cat_parent"=>$r["cat_parent"],"cat_list"=>$r["cat_list_id"],"path"=>"");
                            }
							$cat_path = "";
							foreach($cat as $key => $catEntry){
								if($cat[$key]["path"]==""){
									$path = $this->get_path($cat[$key], $catlist);
									$cat[$key]["path"] = $path;
								} else {
									$path = $cat[$key]["path"];
								}
								$cat_path .= "<cat_path id='$key'><![CDATA[".dirname($this->parent->script)."/$path]]></cat_path>";
							}
							//"cat_label", "cat_parent", "cat_list", "path"
							//print "[$cat_path]";
                            $this->parent->db_pointer->database_free_result($result);
							$category=-1;
							$lang="en";
							$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
							$dirout .= "	<content category='$category' editable='0'><info list='$identifier'><display summary_only='$info_summary_only'>\n";
							$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$identifier."_summary.xml";
							if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
							if (file_exists($fname)){
								$dirout .= join("", file($fname));
							} else {
								$fields = $this->get_field_defs($identifier);
								$dirout .= $this->display_screen(2, $fields, $identifier);
							}
							$dirout .="$cat_path</display><results>";
							$result  = $this->parent->db_pointer->database_query($sql);
							$total_num_pages = $num_of_records / $this->search_page_size;
		                    for($i = 0 ; $i < count($entry_list) ; $i++){
								$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$identifier."_".$lang."_".$entry_list[$i].".xml";
								$md_identifier = $r["md_identifier"];
								if (file_exists($fname)){
									$dirout .= join("",file($fname));									
									$mname = $data_files."/metadata_".$this->client_identifier."_".$lang."_".$md_identifier.".xml";
									if (file_exists($mname)){
										$dirout .= join("", file($mname));
									}									
								}
                            }
                            $this->parent->db_pointer->database_free_result($result);
							$dirout .= "	</results></info></content>\n";
						}
					}
				}
				$out="";
				$out .= "<cat_label><![CDATA[$cat_label]]></cat_label>";
				$out .= "<shop><![CDATA[".$this->shop_type."]]></shop>".$dout;
				$out .= "<data_list command=\"";
				$out .= $this->check_parameters($parameters,"command");
				$out .= "\" number_of_records=\"".($num_of_records)."\" start=\"".($start+1)."\" finish=\"".($end)."\" current_page=\"$page\" number_of_pages=\"$total_num_pages\" page_size=\"".$this->search_page_size."\">\n";
				$qstr = str_replace("+"," ",$this->parent->qstr);
				$pos = strpos($qstr,"&page=$page");
				if ($pos===false){
					// do nothing
				} else {
					// remove page=$page
					$qstr = substr($qstr,0,$pos).substr($qstr,$pos+strlen("&page=$page"));
				}
				$out .= "<searchfilter><![CDATA[".$qstr."]]></searchfilter>";
				$out .= "<pages>\n";
				if ($page<=5){
					$filter_start	= 1;
				} else {
					$filter_start	= $page -5;
				}
				if($filter_start+10 > $total_num_pages){
					$filter_end		= $total_num_pages;
				} else {
					$filter_end		= $filter_start+9;
				}
				for($index=$filter_start;$index<=$filter_end;$index++){
					$out .= "<page>$index</page>\n";
				}
				$out .= "</pages>\n";
				$out .= $dirout;
				$out .= "</data_list>\n";
				$out ="<module name=\"".$this->module_name."\" display=\"$outdisplay\">$out</module>";
//				print $out;
//				$this->exitprogram();
//				exit();
			} else {
				$out="";
				if ($show_form==1){
					$out ="\n<module name=\"".$this->module_name."\" display=\"form\">\n".$this->filter($parameters, $search_directory_identifier)."\n</module>\n";
				}
			} 
			
//			print $out;
//			$this->exitprogram();
			return $out;
		} else {
			return "";
		}
	}
/*
function bidirectionalBubbleSort($array){
 if(!$length = count($array)){
  return $array;
 };
 $start = -1;
 while($start < $length){
  ++$start;
  --$length;
  for($i= $start; $i < $length; ++$i){
   if($array[$i] > $array[$i + 1]){
    $temp = $array[$i];
    $array[$i] = $array[$i + 1];
    $array[$i + 1] = $temp;
   }
  }
  for($i = $length; --$i >= $start;){
   if($array[$i] > $array[$i + 1]){
    $temp = $array[$i];
    $array[$i] = $array[$i + 1];
    $array[$i + 1] = $temp;
   }
  }
 }
 return $array;
}

function quickSort($array){
 if(!$length = count($array)){
  return $array;
 };
 
 $k = $array[0];
 $x = $y = array();
 
 for($i=1;$i<$length;$i++){
  if($array[$i] <= $k){
   $x[] = $array[$i];
  }else{
   $y[] = $array[$i];
  };
 };
 return array_merge($this->quickSort($x),array($k),$this->quickSort($y));
}



function shellSort($array){
 if(!$length = count($array)){
  return $array;
 };
 $k = 0;
 $gap[0] = (int)($length/2);
 while($gap[$k]>1){
  $k++;
  $gap[$k] = (int)($gap[$k-1]/2);
 }
 
 for($i = 0; $i <= $k; $i++){
  $step = $gap[$i];
  for($j = $step; $j<$length; $j++){
   $temp = $array[$j];
   $p = $j+$step;
   while($p >= 0 && $temp < $array[$p]){
    $array[$p-$step] = $array[$p];
    $p = $p+$step;
   };
   $array[$p+$step] = $temp;
  };
 };
 print_r($array);
 die;
 
 return $array;
}
*/
    /**
    * display a featured list
    *
    * @param Array ("wo_owner_id", "current_menu_location")
    * @return String XmlString that represents the desired out put of this feature list
    */
    function featured_list($parameters){
		$__layout_position		= $this->check_parameters($parameters, "__layout_position");
		$cmd					= $this->check_parameters($parameters, "command");
		if($__layout_position==2 && $cmd !='' || $this->parent->module_type=='preview'){
			return "";
		}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,print_r($parameters,true)));}
		$identifier  			= $this->check_parameters($parameters,"wo_owner_id",-1);
		$current_menu_location	= $this->check_parameters($parameters,"current_menu_location",-1);
		$lang					= "en";
		$fake_uri				= "";
		$info_summary_only			=0;
	    $list_type				= -1;
		$list					= -1;
		$label					= "";
		$format					= -1;
		$auto_counter			= -1;
		$category				= -1;
		$out					= "";
		$zout					= "";
		$length					= 0;
		/*
		+=-=-=-
		| 2 Featured company settings:
		| * Manually choose & set for a period of time e.g. X days between certain dates
		|	0	define multiple entries, timed rotation
		|	0	will need display options (configurable) could be full content or summary
		| *	Automatically choose X number of entries
		|	0	new selection per visit / per page load
		|	0	will need display options (configurable) could be full content or summary
		| *	Both manual + automatic could appear on same page - positioning & ranking important
		|
		| *	Status (live/draft)
		| *	Display Container
		| *	List of entries (manual/auto)
		| *	Display options
		|	0	Per visit / per page
		|	0	Display content or summary
		|
		+=-=-=-
		*/
		$now = $this->libertasGetDate();
		$ids= Array();
		$sql = "select * from information_features
			inner join menu_to_object on menu_to_object.mto_object=information_features.ifeature_identifier and menu_to_object.mto_client=information_features.ifeature_client and menu_to_object.mto_module = '".$this->webContainer."FEATURES' and menu_to_object.mto_menu = $current_menu_location
			inner join information_list on ifeature_list = info_identifier and ifeature_client = info_client
			inner join menu_data on info_menu_location = menu_identifier and menu_client = info_client
		where
			ifeature_identifier = $identifier and
			ifeature_client = $this->client_identifier and
			ifeature_status = 1 and
			(
				(ifeature_date_start='0000-00-00 00:00:00' or ifeature_date_start < '$now') and 
				(ifeature_date_finish='0000-00-00 00:00:00' or ifeature_date_finish >= '$now')
			)
		";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->parent->db_pointer->database_query($sql);
		$c=0;
		$menu_url 		= "";
		$feature_url	= "";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$c++;
		    $list_type		=	$r["ifeature_list_type"];
			$list			=   $r["ifeature_list"];
			$format			=	$r["ifeature_display_format"];
			$rotation		=   $r["ifeature_display_rotation"];
			$label			=	$r["ifeature_label"];
			$auto_counter	=	$r["ifeature_auto_counter"];
			$start			=	$r["ifeature_date_start"];
			$ifeature_as_rss=	$r["ifeature_as_rss"];
//			$end			=	$r["ifeature_date_finish"];
			$category		=	$r["info_category"];
			$cat_label		=	$r["info_cat_label"];
			$info_summary_only = $r["info_summary_only"];
			$feature_url	=	"_feature-".$this->make_uri($r["info_label"]).".php";
			$menu_url 		=	$r["menu_url"];
        }
        $this->parent->db_pointer->database_free_result($result);
		/**
		* load list of ids to load
		*/
		if($c!=0){
			if($menu_url==""){
				$link_to_real_url = 0;
			}else{
//				print "link_to_real_url ::".$menu_url."]";
				$link_to_real_url = $this->call_command("LAYOUTSITE_MENU_HAS_ACCESS", Array("fake_uri"=>$menu_url));
//				print "link_to_real_url ::$link_to_real_url::".$menu_url."]";
//				$this->exitprogram();
			}
			$counter=0;
			//[1, 1, 0, 0, Featured Company, 0, 0000-00-00 00:00:00, 0000-00-00 00:00:00]
			//print "[".__LINE__." @ ".__FILE__.", $link_to_real_url, ".$list_type.", ".$list.", ".$list_type.", ".$format.", ".$rotation.", ".$label.", ".$auto_counter.", ".$start."]";
			if ($list_type==0){
				// manual get list from table.
				$sql = "select * from information_feature_list
						inner join information_entry on ifl_entry = ie_parent and ie_client = ifl_client and ie_status =1 and ie_published=1 and ie_list = $list
						inner join metadata_details on md_link_id = ie_identifier and ie_client = md_client and md_module='".$this->webContainer."'
						inner join information_list on info_identifier = ie_list and info_client=ie_client
						inner join menu_data on menu_identifier = info_menu_location and info_client=menu_client
					where ifl_client = $this->client_identifier and ifl_owner = $identifier order by ifl_rank";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
	            while($r = $this->parent->db_pointer->database_fetch_array($result)){
	            	$ids[$counter] = $r["ifl_entry"];
//	            	$menu_url = $r["menu_url"];
					$counter++;
	            }
				$loadall =1;
	            $this->parent->db_pointer->database_free_result($result);
			} else if ($list_type==1){
				// use filter defined by admin user
				$keymap 		= Array();
				$out 			= "";
				$sql = "select * from information_fields  
							inner join information_list on if_list = info_identifier and if_client = info_client
						where if_client = $this->client_identifier and if_screen =0 and if_list = $list	";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				$result  = $this->parent->db_pointer->database_query($sql);
		        while($r = $this->parent->db_pointer->database_fetch_array($result)){
		        	$keymap[count($keymap)] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"]);
		        }
			    $this->parent->db_pointer->database_free_result($result);
//				print "<li>".__FILE__."@".__LINE__."<p>".print_r($keymap,true)."</p></li>";
				$condition = $this->call_command("FILTERADMIN_GET_SQL",
					Array(
						"owner"		 => $identifier,
						"module"	 => $this->webContainer,
						"cmd"		 => "GET",
						"maps" 		 => $keymap
					)
				);
				if ($condition == " ( )"){
					$condition="";
				}
				/*************************************************************************************************************************
    		    * retrieve order def
	    	    *************************************************************************************************************************/
				$ord = $this->check_parameters($condition,"order",Array("field"=>"","dir"=>0));
				$ord_value="";
				if($ord["field"]!=""){
//					print_r($ord);
					$ord_value .= $ord["field"]. (($ord["dir"]==0)?" asc,":" desc,");
				}
				$sql = "select ie_identifier, ie_parent, menu_url from information_entry
							inner join metadata_details on ie_identifier = md_link_id and md_client= ie_client and md_module='$this->webContainer'
							".$condition["join"]."
							inner join information_list on info_identifier = ie_list and ie_client = info_client
							inner join menu_data on menu_identifier = info_menu_location and info_client = menu_client
						where ie_published=1 ".$condition["where"]." and ie_status =1 and ie_version_wip =1 and ie_list = $list and ie_client=$this->client_identifier order by $ord_value ie_identifier desc
					";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
	            while($r = $this->parent->db_pointer->database_fetch_array($result)){
	            	$ids[$counter] = $r["ie_parent"];
//	            	$ids[$counter] = $r["ie_identifier"];
					$fake_uri = $r["menu_url"];
					$counter++;
	            }
	            $this->parent->db_pointer->database_free_result($result);
				$loadall =1;
			} else {
				// Randomly pick a number of entries.
				$keymap 		= Array();
				$out 			= "";
				$sql = "select * from information_fields  
							inner join information_list on if_list = info_identifier and if_client = info_client
						where if_client = $this->client_identifier and if_screen =0 and if_list = $list	";
				$result  = $this->parent->db_pointer->database_query($sql);
		        while($r = $this->parent->db_pointer->database_fetch_array($result)){
		        	$keymap[count($keymap)] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"]);
		        }
			    $this->parent->db_pointer->database_free_result($result);
				$condition = $this->call_command("FILTERADMIN_GET_SQL",
					Array(
						"identifier" => $identifier,
						"module"	 => $this->webContainer,
						"cmd"		 => "GET",
						"maps" 		 => $keymap
					)
				);
				/*************************************************************************************************************************
    		    * retrieve order def
	    	    *************************************************************************************************************************/
				$ord = $this->check_parameters($condition,"order",Array("field"=>"","dir"=>0));
				$ord_value="";
				if($ord["field"]!=""){
//					print_r($ord);
					$ord_value .= " order by ".$ord["field"]. (($ord["dir"]==0)?" asc":" desc");
				}
				/*
				$sql = "select ie_parent, menu_url from information_entry
							inner join metadata_details on ie_identifier = md_link_id and md_client= ie_client and md_module='$this->webContainer'
							".$condition["join"]."
							inner join information_list on info_identifier = ie_list and ie_client = info_client
							inner join menu_data on menu_identifier = info_menu_location and info_client = menu_client
							where ie_published=1 ".$condition["where"]." and ie_status =1 and ie_list = $list and ie_client=$this->client_identifier order by ie_identifier desc
				";
				*/
				$sql = "select * from information_entry
							inner join metadata_details on ie_identifier = md_link_id and md_client= ie_client and md_module='$this->webContainer'
							".$condition["join"]."
						  where ie_published=1 and ie_status =1 ".$condition["where"]." and ie_list = $list and ie_client=$this->client_identifier $ord_value";
//				print "<li>$sql</li>";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
	            while($r = $this->parent->db_pointer->database_fetch_array($result)){
	            	$ids[$counter] = $r["ie_identifier"];
					$counter++;
	            }
	            $this->parent->db_pointer->database_free_result($result);
				$loadall =0;
			}
/*
			print "<li>$this->webContainer</li>";
			print "<pre>";
			print_r($ids);
			print "</pre>";
*/
//			$cats = $this->call_command("CATEGORY_LOAD", Array("identifier" => $category, "recache"=>0, "category" => -2));
			$cats="";
			$cat_paths = Array();
//			print __LINE__;
			$_SESSION["FEATURE_INDEX_$list"]=-1;
            $catlist = $this->call_command("CATEGORY_LOAD", Array("identifier" => $category, "recache"=>0, "return_array"=>1));
			$out  =	"<module name=\"".$this->module_name."\" display=\"FEATURE\">";
			$out .= "<elert>0</elert>";
			$out .= "<workflow>0</workflow>";
			$out .= "<link_to_real_url type='$link_to_real_url'>$feature_url</link_to_real_url>";
			$out .= "<directory_identifier>$list</directory_identifier>";
			$out .= "<label><![CDATA[$label]]></label>\n";
			$out .= "<cat_label><![CDATA[$cat_label]]></cat_label>\n";
			$out .= "<fake_uri><![CDATA[".$fake_uri."]]></fake_uri>\n";
			$out .= "<current_category show_sub='1'></current_category>$cats\n";
			if(($this->parent->db_pointer->database == 'system_ards' && ($this->parent->domain == 'ards-council.gov.uk') || $this->parent->domain == 'www.ards-council.gov.uk'))
				$out .= "<ards_domain_database>3</ards_domain_database>\n";
			
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
			$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$list."_feature_".$identifier.".xml";
			if (file_exists($fname)){
//				print $fname;
				$zout .= join("", file($fname));
			}
			$zout .= "</display><results>";

							/**Starts to get events greater than toDate By Muhammad Imran**/
					$ids_var = array();
					$ids_var = $ids;
					$counter_var = 0;
					foreach ($ids_var as $ids_values){
						$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids_values.".xml";
//						print "<li>".__FILE__."@".__LINE__."<p>($fail) $fname</p></li>";
						if (file_exists($fname)){

							$str_date = strtotime($this->libertasGetDate("d F Y",strtotime(date("Y-m-d"))));

							$file_contents = join("", file($fname));

							$pos = strpos($file_contents, "<field type='datetime' name='ie_odate1' visible='yes'>");
							if ($pos == "")
								$pos = strpos($file_contents, "<field type='date' name='ie_odateonly1' visible='yes'>");

							$str_field = substr($file_contents,$pos);
							$pos = strpos($str_field, ",");
							$date_start = substr($str_field,$pos+2,11);
							$date_start = str_replace($mths_abr,$mths_cspell,$date_start);
							$date_start = strtotime($date_start);

//echo 'sta:'.$date_start.'<br>';
//echo 'str:'.$str_date.'<br>';
//echo 'end'.$date_end.'<br><br>';
//							if ($str_date > $date_start)
							if ($date_start >= $str_date){
								$ids_arr[$counter_var] = array("parent"=>$ids_values,"evnt_dat"=>$date_start);
								$counter_var++;
							}
						}
					}
							/**Ends to get events greater than toDate By Muhammad Imran**/



			/**Starts to sort alphabetically by eventdate By Muhammad Imran**/
				//print_r($ids_arr);die;
				$ids = "";
				if (is_array($ids_arr)){
					usort($ids_arr, 'compare_event_date');
					//print_r($ids_arr);
					foreach ($ids_arr as $ids_values){
						$ids[] = $ids_values['parent'];
					}
				}
			/**Ends to sort alphabetically by eventdate By Muhammad Imran**/
//			print "<"."!-- $loadall, $list, $identifier --".">";
			if($loadall == 1 ){
				$maxcount = count($ids);
				if ($rotation==0 || $rotation==3){
					// static feature for session
					$start  = $this->check_parameters($_SESSION,"feature_".$identifier."position",-1);
					$start  = 0;//To sort from zero
					
					if ($maxcount >= $auto_counter){
						$len = $auto_counter;
					} else {
						$len = $maxcount;
					}
					if ($start==-1){
						$start = mt_rand(0,$maxcount);
						$_SESSION["feature_".$identifier."position"] = $start;
					}
					$c = $start;
					$index=0;
					$fail=0;
//					print "[$len]";
					while( $index < $len && $fail<3){
						if($c >= $maxcount){
							$c=0;
						}
						$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$c].".xml";
//						print "<li>".__FILE__."@".__LINE__."<p>($fail) $fname</p></li>";
						if (file_exists($fname)){
							if($length==0){
								$_SESSION["FEATURE_INDEX_$list"] = $ids[$c];
//								print "<"."!-- $rotation --- ".$ids[$c]." ---".">";
							}
//							print "<li>".__FILE__."@".__LINE__."<p>$fname</p></li>";

							$length++;
							$index++;
							$zout .= join("", file($fname));
							$cat_paths[count($cat_paths)] = $ids[$c];
							$c++;
						} else {
							$c++;
							$fail ++;
						}
					}
//				print "<li>".__FILE__."@".__LINE__."<p>$maxcount ".print_r($ids,true)."</p></li>";
				} else if ($rotation==1){
					//cycle feature for session
					$start  = $this->check_parameters($_SESSION,"feature_".$identifier."position",0);
					$start  = 0;//To sort from zero

					if ($maxcount >= $auto_counter){
						$len = $auto_counter;
					} else {
						$len = $maxcount;
					}
					if ("__NOT_FOUND__"  == $this->check_parameters($_SESSION,"feature_".$identifier."position","__NOT_FOUND__")){
						$_SESSION["feature_".$identifier."position"]=0;
					}
					$_SESSION["feature_".$identifier."position"]++;
					if ($start>=$maxcount){
						$start=0;
						$_SESSION["feature_".$identifier."position"]=0;
					}
	//				print "[$start]";
					$c=$start;
					for($index=0; $index < $len; $index++){
						if ($c >= $maxcount){
							$c  = 0;
						}
						$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$c].".xml";
//						print "<li>$fname</li>";
						if (file_exists($fname)){
							if($length==0){
								$_SESSION["FEATURE_INDEX_$list"] = $ids[$c];
								//print "<"."!-- $rotation --- ".$ids[$c]." ---".">";
							}
							$zout .= join("", file($fname));
							$cat_paths[count($cat_paths)] = $ids[$c];
							$length++;
						}
						$c++;
					}
				} else {
					
					if(($this->parent->db_pointer->database == 'system_ards' && ($this->parent->domain == 'ards-council.gov.uk') || $this->parent->domain == 'www.ards-council.gov.uk')){
						$maxcount = count($ids);
						//Added By Ali
						$random_pos = 0;
						$loaded=Array();
						$index=0;
						$failed=0;
	//					print_r($ids);
						while($index < $auto_counter){
							//$random_pos = mt_rand(0,($maxcount-1));
							if(!in_array($random_pos,$loaded)){
								/**
								* choose random entry
								*/
								$loaded[count($loaded)] = $random_pos;
								$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$random_pos].".xml";
			//					print "<li>$fname</li>";
								if (file_exists($fname)){
									if($length==0){
										$_SESSION["FEATURE_INDEX_$list"] = $ids[$random_pos];
										//print "<"."!-- $rotation --- ".$ids[$c]." ---".">";
									}
									$zout .= join("", file($fname));
									$cat_paths[count($cat_paths)] = $ids[$random_pos];
									$length++;
								}
								$failed=0; // incase there are not engough records build in a failure mech
								$index++;
								//Added By Ali
								$random_pos ++;
							} else {
								$failed++; // incase there are not engough records build in a failure mech
							}
							if($failed>=3){
								 /*
								 * if we falied to get a unique record  3 times move on to the next
								 */
								 $failed=0;
								 $index++;
							}
						}
				  }else{
				  	
						$maxcount = count($ids);
						$loaded=Array();
						$index=0;
						$failed=0;
	//					print_r($ids);
						while($index < $auto_counter){
							$random_pos = mt_rand(0,($maxcount-1));
							if(!in_array($random_pos,$loaded)){
								/**
								* choose random entry
								*/
								$loaded[count($loaded)] = $random_pos;
								$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$random_pos].".xml";
			//					print "<li>$fname</li>";
								if (file_exists($fname)){
									if($length==0){
										$_SESSION["FEATURE_INDEX_$list"] = $ids[$random_pos];
										//print "<"."!-- $rotation --- ".$ids[$c]." ---".">";
									}
									$zout .= join("", file($fname));
									$cat_paths[count($cat_paths)] = $ids[$random_pos];
									$length++;
								}
								$failed=0; // incase there are not engough records build in a failure mech
								$index++;
							} else {
								$failed++; // incase there are not engough records build in a failure mech
							}
							if($failed>=3){
								 /*
								 * if we falied to get a unique record  3 times move on to the next
								 */
								 $failed=0;
								 $index++;
							}
						}
				  
				  }
				
				}
			} else {
				$maxcount = count($ids);
				$loaded=Array();
				$index=0;
				$failed=0;
				while($index < $auto_counter){
					$random_pos = mt_rand(0,$maxcount);
					if(!in_array($random_pos,$loaded)){
						/**
	                    * chosoe random entry
	                    */
						$loaded[count($loaded)] = $random_pos;
						$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$random_pos].".xml";
	//					print "<li>$fname</li>";
						if (file_exists($fname)){
							if($length==0){
								$_SESSION["FEATURE_INDEX_$list"] = $ids[$random_pos];
							}
							$zout .= join("", file($fname));
							$cat_paths[count($cat_paths)] = $ids[$random_pos];
							$length++;
						}
						$failed=0; // incase there are not engough records build in a failure mech
						$index++;
					} else {
						$failed++; // incase there are not engough records build in a failure mech
					}
					if($failed>=3){
						 /*
	                     * if we falied to get a unique record  3 times move on to the next
	                     */
						 $failed=0;
						 $index++;
					}
				}
			}
			$obj_list = join(", ", $cat_paths);
//			print_r($catlist);
//			* catdata	- holds category information for this record ("cat_label", "cat_parent", "cat_list", "path")
			$gen_paths = "";
			if($obj_list!=""){
				$sql = "select * from category_to_object 
							inner join category on cat_identifier = cto_clist and cat_client = cto_client
						where cto_object in ($obj_list) and cto_module='$this->webContainer' and cto_client=$this->client_identifier";
				$result  = $this->parent->db_pointer->database_query($sql);
                while($r = $this->parent->db_pointer->database_fetch_array($result)){
	                $cid = Array("cat_label"=>$r["cat_label"],"cat_parent"=>$r["cat_parent"],"cat_list"=>$r["cat_list_id"],"path"=>"");
					$gen_paths .= "<cat_path id='".$r["cat_identifier"]."'><![CDATA[".dirname($fake_uri)."/".$this->get_path($cid, $catlist)."]]></cat_path>";
                }
                $this->parent->db_pointer->database_free_result($result);
			}
			$out .= "<content category=\"$category\" editable=\"0\" len=\"$length\">
						<info list=\"$list\">
							<display  summary_only='$info_summary_only'>$zout$gen_paths</results></info></content>";
			$out	.=	"</module>";
		}
		return $out;
	}

    /**
    * preview a featured list
    *
    * @param Array ("wo_owner_id", "current_menu_location", "ifeature_list_type", "ifeature_list", "ifeature_display_format", "ifeature_display_rotation", "ifeature_label", "ifeature_auto_counter", "match_list" )
    * @return String XmlString that represents the desired out put of this feature list
    */
	function featured_preview($parameters){
		$this->parent->print_first .="<script>
		try{
			if(parent.document){
				if (parent.document.all.preview_loaded.value==0){
					parent.document.all.preview.style.display='';
					parent.document.all.preview_loading.style.display='none';
				}
			}
		} catch (e){
			alert('Sorry currently unable to show preview screen');
		}
		</script>";
//		$identifier  			= $this->check_parameters($parameters,"wo_owner_id",1);
//		$current_menu_location	= $this->check_parameters($parameters,"current_menu_location",-1);
		$identifier  = 1;
		$lang= "en";
		$fake_uri="";
//		$now = $this->libertasGetDate();

	    $list_type		=	$this->check_parameters($parameters,"ifeature_list_type"			);
		$list			=   $this->check_parameters($parameters,"ifeature_list"					);
		$format			=	$this->check_parameters($parameters,"ifeature_display_format"		);
		$rotation		=   $this->check_parameters($parameters,"ifeature_display_rotation"		);
		$label			=	$this->check_parameters($parameters,"ifeature_label"				);
		$auto_counter	=	$this->check_parameters($parameters,"ifeature_auto_counter"			);
		$ManualEntryId	=	$this->check_parameters($parameters,"ManualEntryId"					);
		$ManualEntryCat	=	$this->check_parameters($parameters,"ManualEntryCat"				);
		$sql = "select * from information_list
				left outer join information_fields  on if_list = info_identifier and if_client = info_client and if_screen =3 
				where info_identifier = $list and info_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$keymap = Array();
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if("__NOT_FOUND__" != $this->check_parameters($r,"if_name","__NOT_FOUND__")){
        		$keymap[count($keymap)] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"]);
			}
			$category  = $r["info_category"];
        }
	    $this->parent->db_pointer->database_free_result($result);
		/**
		* load list of ids to load
        */
		$ids= Array();
		$counter=0;
		if ($list_type==0){
			// manual get list from table.
			$sql = "select * from information_entry where ie_client = $this->client_identifier and ie_status =1 and ie_published=1 and ie_identifier in (".join(", ",$ManualEntryId).")";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$ids[$counter] = $r["ie_parent"];
				$counter++;
            }
			$loadall =1;
            $this->parent->db_pointer->database_free_result($result);
		} else if ($list_type==1){
			// use filter defined by admin user
			$condition 		= 	$this->gen_sql_cache(Array("match_list" => $this->call_command("FILTERADMIN_GET_MATCHLIST"), "block" => $this->check_parameters($parameters,"filter_builder_blockinfo"), "identifier"=>$list, "maps" => $keymap));
//			print_r( $condition);
			/*************************************************************************************************************************
            * chgeck for order by clause
            *************************************************************************************************************************/
			$ord = $this->check_parameters($condition,"order",Array("field"=>"","dir"=>0));
			$ord_value="";
			if($ord["field"]!=""){
//				print_r($ord);
				$ord_value .= $ord["field"]. (($ord["dir"]==0)?" asc,":" desc,");
			}

			$sql = "select ie_parent, menu_url, md_title  from information_entry
				inner join metadata_details on md_link_id = ie_identifier and ie_client = md_client and md_module='".$this->webContainer."'
			".$condition["join"]."
inner join information_list on info_identifier = ie_list and ie_client = info_client
inner join menu_data on menu_identifier = info_menu_location and info_client = menu_client
where ie_published=1 ".$condition["where"]." and ie_status =1 and ie_list = $list and ie_client=$this->client_identifier order by $ord_value ie_identifier desc
";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$ids[$counter] = $r["ie_parent"];
				$fake_uri = $r["menu_url"];
				$counter++;
            }
            $this->parent->db_pointer->database_free_result($result);
			$loadall =1;
		} else {
			// Randomly pick a number of entries.
			$condition = $this->call_command("FILTERADMIN_GET_SQL",	Array("identifier" => $identifier,"module"=> $this->webContainer,"cmd"=> "GET"));
/*
			$sql = "select ie_parent, menu_url, md_title  from information_entry
				inner join metadata_details on md_link_id = ie_identifier and ie_client = md_client and md_module='".$this->webContainer."'
".$condition["join"]."
inner join information_list on info_identifier = ie_list and ie_client = info_client
inner join menu_data on menu_identifier = info_menu_location and info_client = menu_client
where ie_published=1 ".$condition["where"]." and ie_status =1 and ie_list = $list and ie_client=$this->client_identifier order by ie_identifier desc
";
*/
			/*************************************************************************************************************************
    		* retrieve order def
	    	*************************************************************************************************************************/
			$ord = $this->check_parameters($condition,"order",Array("field"=>"","dir"=>0));
			$ord_value="";
			if($ord["field"]!=""){
//				print_r($ord);
				$ord_value .= " order by ".$ord["field"]. (($ord["dir"]==0)?" asc":" desc");
			}
			$sql = "select * from information_entry 
							inner join metadata_details on md_link_id = ie_identifier and ie_client = md_client and md_module='".$this->webContainer."'
			where ".$condition["join"]." and ie_status =1 where ie_list = $list ".$condition["where"]." and ie_client=$this->client_identifier $ord_value";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$ids[$counter] = $r["ie_identifier"];
				$counter++;
            }
            $this->parent->db_pointer->database_free_result($result);
			$loadall =0;
		}
		
		$cats = $this->call_command("CATEGORY_LOAD", Array("identifier" => $category, "recache"=>0, "category" => -2));
		$out	 =	"<module name=\"".$this->module_name."\" display=\"FEATURE\">";
		$out .= "<elert>0</elert>";
		$out .= "<workflow>0</workflow>";
		$out .= "<directory_identifier>$list</directory_identifier>";
		$out .= "<label><![CDATA[$label]]></label>\n";
		$out .= "<display_format><![CDATA[0]]></display_format>\n";
		$out .= "<display_columns><![CDATA[0]]></display_columns>\n";
		$out .= "<fake_uri><![CDATA[".$fake_uri."]]></fake_uri>\n";
		$out .= "<current_category show_sub='1'></current_category>$cats\n";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if($format==0){
			$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$list."_summary.xml";
		} else {
			$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$list."_content.xml";
		}
		$out .= "<content category=\"$category\" editable=\"0\">
					<info list=\"$list\">
						<display>";
			if (file_exists($fname)){
				$out .= join("", file($fname));
			}
			$out .= "</display><results>";
		
			if($loadall == 1 ){
				$maxcount = count($ids);
				if ($rotation==0){
					// static feature for session
					$start  = $this->check_parameters($_SESSION,"feature_".$identifier."position",-1);
					if ($maxcount >= $auto_counter){
						$len = $auto_counter;
					} else {
						$len = $maxcount;
					}
					if ($start==-1){
						$start = mt_rand(0,$maxcount);
						$_SESSION["feature_".$identifier."position"] = $start;
					}
					$c = $start;
					for($index=0; $index < $len; $index++){
						if($c >= $maxcount){
							$c=0;
						}
						$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$c].".xml";
						if (file_exists($fname)){
							$out .= join("", file($fname));
						}
						$c++;
					}
				} else if ($rotation==1){
					//cycle feature for session
					$start  = $this->check_parameters($_SESSION,"feature_".$identifier."position",0);
					if ($maxcount >= $auto_counter){
						$len = $auto_counter;
					} else {
						$len = $maxcount;
					}
					if ("__NOT_FOUND__"  == $this->check_parameters($_SESSION,"feature_".$identifier."position","__NOT_FOUND__")){
						$_SESSION["feature_".$identifier."position"]=0;
					}
					$_SESSION["feature_".$identifier."position"]++;
					if ($start>=$maxcount){
						$start=0;
						$_SESSION["feature_".$identifier."position"]=0;
					}
					$c=$start;
					for($index=0; $index < $len; $index++){
						if ($c >= $maxcount){
							$c  = 0;
						}
						$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$c].".xml";
						if (file_exists($fname)){
							$out .= join("", file($fname));
							$c++;
						}
					}
				} else {
					if ($maxcount >= $auto_counter){
						$len = $auto_counter;
					} else {
						$len = $maxcount;
					}
	//				print "<strong>rotate </strong>[$len, $maxcount, $auto_counter]";
					$loaded=Array();
					$index=0;
					$failed=0;
					while($index < $len){
						$random_pos = mt_rand(0,$maxcount);
						if(!in_array($random_pos,$loaded)){
							$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$random_pos].".xml";
	//						print "<li>$fname</li>";
							if (file_exists($fname)){
								$out .= join("", file($fname));
							}
							$failed=0; // incase there are not engough records build in a failure mech
							$index++;
						} else {
							$failed++; // incase there are not engough records build in a failure mech
						}
						if($failed>=3){
							 /*
	                    	 * if we falied to get a unique record  3 times move on to the next
	            	         */
							 $failed=0;
							 $index++;
						}
					}
				}
			} else {
				$maxcount = count($ids);
				$loaded=Array();
				$index=0;
				$failed=0;
				while($index < $auto_counter){
					$random_pos = mt_rand(0,$maxcount);
					if(!in_array($random_pos,$loaded)){
						/**
	                    * chosoe random entry
	                    */
						$loaded[count($loaded)] = $random_pos;
						$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$random_pos].".xml";
	//					print "<li>$fname</li>";
						if (file_exists($fname)){
							$out .= join("", file($fname));
						}
						$failed=0; // incase there are not engough records build in a failure mech
						$index++;
					} else {
						$failed++; // incase there are not engough records build in a failure mech
					}
					if($failed>=3){
						 /* if we falied to get a unique record  3 times move on to the next */
						 $failed=0;
						 $index++;
					}
				}
			}
	$out .= "</results></info></content>";
		$out	.=	"</module>";
		return $out;
	}

    /**
    * check to see if an a2z results are to be displayed
    */
    function display_atoz($parameters){
	//	print_R($parameters);
		$choosen_display_format= 1;
        $choosenletter         = $this->check_parameters($parameters,"letter");
        $label                 = $this->check_parameters($parameters,"fake_title");
        $page                  = $this->check_parameters($parameters,"page",1);
        $dirid                 = $this->check_parameters($parameters,"identifier",-1);
        $current_menu_location = $this->check_parameters($parameters,"current_menu_location");
		$category 			   = $this->check_parameters($parameters,"category",-1);
		$info_summary_only		= 0;
		if($category!=-1 && $category!=""){
			return $this->information_display($parameters);
		}
//        $fake_uri              = $this->parent->script;
        $complete_extract_list = "";
        $out                   = "";
		if($this->a2z==1){
			return "";
		}
		$this->a2z=1;
        $a2zout                = "";
		$cat 				   = Array();
        /**
        * Default setting for letters counting array
        */
		$letters= Array(
            "undefined"=>0,
            "a"=>0,
            "b"=>0,
            "c"=>0,
            "d"=>0,
            "e"=>0,
            "f"=>0,
            "g"=>0,
            "h"=>0,
            "i"=>0,
            "j"=>0,
            "k"=>0,
            "l"=>0,
            "m"=>0,
            "n"=>0,
            "o"=>0,
            "p"=>0,
            "q"=>0,
            "r"=>0,
            "s"=>0,
            "t"=>0,
            "u"=>0,
            "v"=>0,
            "w"=>0,
            "x"=>0,
            "y"=>0,
            "z"=>0
        );
		/**
        * SQL - get the information directory at this location
        */
		if($dirid==-1){
			$sql ="select * from information_list inner join menu_data on menu_identifier=info_menu_location and menu_client=info_client where info_menu_location = $current_menu_location and info_client = $this->client_identifier";
		} else {
			$sql ="select * from information_list inner join menu_data on menu_identifier=info_menu_location and menu_client=info_client where info_menu_location = $current_menu_location and info_client = $this->client_identifier and info_identifier =$dirid";
		}
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
           	$dirid 				= $r["info_identifier"];
			$cat_label			= $r["info_cat_label"];
			$cat_id				= $r["info_category"];
			$info_summary_only	= $r["info_summary_only"];
			$display_format		= $r["info_display"];
			$display_columns	= $r["info_columns"];
/*
			$ml = split("/",$r["menu_url"]);
			$menu_uri 	= "";
			for($i = 0 ; $i < count($ml) - 1; $i ++){
				if($menu_uri != ""){
					$menu_uri .= "/";
				}
				$menu_uri .= $ml[$i];
			}
			*/
			$menu_uri = dirname($r["menu_url"])."/";
        }
        $this->parent->db_pointer->database_free_result($result);
//		print "<li>".__FILE__."@".__LINE__."<p>$dirid $cat_label</p></li>";
		
        /**
        * SQL - get letter usage
        */
		
        if($dirid!=-1){
			$sql = "select * from information_fields where if_client = $this->client_identifier and if_list = $dirid and if_screen=1 order by if_rank";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
            $result  = $this->parent->db_pointer->database_query($sql);
			$keymap = Array();
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$keymap[count($keymap)] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"], $r["if_type"]);
            }
			$this->parent->db_pointer->database_free_result($result);
			$sql = "select mid(ie_uri,1,1) as letter, count(mid(ie_uri,1,1)) as total from information_entry
	                    where ie_status =1 and ie_cached=1 and ie_list = $dirid and ie_client = $this->client_identifier and ie_published=1 
	                group by mid(ie_uri,1,1)
	                order by letter";
	        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
	        while ($r = $this->parent->db_pointer->database_fetch_array($result)){
	            $letter = $r["letter"];
	            $total  = $r["total"];
	            //print "<li> $letter = $total </li>";
	            if ($letter>="a" && $letter<="z"){
	                $letters[$letter] = $total;
	            } else {
	                $letters["undefined"] += $total;
	            }
	        }
	        $this->parent->db_pointer->database_free_result($result);
//			print "<li>".__FILE__."@".__LINE__."<pre>".print_r($letters,true)."</pre></li>";
	        /**
	        * output the a2z letters
	        */
	        $a2zout .= "<uri><![CDATA[$menu_uri]]></uri>";
	        $a2zout .= "<letters choosenletter='$choosenletter'>";
	        $a2zout .= "<letter count='".$letters["undefined"]."' lcase='undefined'>#</letter>";
			for ($index = 1 ; $index<=13;$index++){
				$a2zout .= "<letter count='".$letters[chr($index+96)]."' lcase='".chr($index+96)."'>".chr($index+64)."</letter>";
			}
			$a2zout .= "</letters>";
			$a2zout .= "<letters>";
			for ($index = 14 ; $index<=26;$index++){
				$a2zout .= "<letter count='".$letters[chr($index+96)]."' lcase='".chr($index+96)."'>".chr($index+64)."</letter>";
			}
			$a2zout .= "</letters>";
			if($choosenletter!=""){
		        /**
		        * select entries based on the starting letter
		        */
		        $sql = "select distinct ie_parent, info_searchresults, info_shop_enabled, info_category from information_list
		                 inner join information_entry on ie_list = info_identifier and ie_client= info_client
		                 where ie_status =1 and ie_uri like '$choosenletter%' and ie_list = $dirid and info_client = $this->client_identifier and ie_published=1 and ie_cached=1 and ie_version_wip=1 order by ie_uri";
// 				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
		        $entry_list = Array();
		        $num_of_records = $this->call_command("DB_NUM_ROWS", Array($result));
				$shop_enabled=0;

						/***** Added By Muhammad Imran Mirza *****/

//						$entry_list = Array();
//	                    $num_of_records = $this->call_command("DB_NUM_ROWS", Array($result));
	                    if ($num_of_records>0){
						    $r = $this->parent->db_pointer->database_fetch_array($result);

							$shop_enabled = $r["info_shop_enabled"];
							$this->search_page_size = $r["info_searchresults"];
							$clist                  = $r["info_category"];
	                        $this->call_command("DB_SEEK", Array($result,0));
					        if($page==1){
							    $start	= 0;
	                        	$end	= $this->search_page_size;
						    } else {
							    $start  = ($page - 1)*$this->search_page_size;
							    $end    = $start + ($this->search_page_size - 1 );
						    }
						    if ($end >= $num_of_records){
							    $end    = $num_of_records;
						    }
	                        if($start !=0){
	                            $this->call_command("DB_SEEK", Array($result,$start));
	                        }
	                        $pos = $start;
					        while (($r = $this->parent->db_pointer->database_fetch_array($result)) && ($pos <= $end-1)){
								$entry_list[count($entry_list)] = $r["ie_parent"];
                            	$pos ++;
	                        }
							if(count($entry_list)+$start<$end){
								$num_of_records = count($entry_list)+$start;
							}
	                    } else {
							// zero records
							$pos=0;
							$start=0;
						}
						$end=$pos;
	                    $this->parent->db_pointer->database_free_result($result);
						$complete_extract_list="";
						$display_list=Array();
						for ($i=0;$i<$this->search_page_size;$i++){
							if(($start+$i)<$end){
								if($complete_extract_list!=""){
									$complete_extract_list .=", ";
								}
								$complete_extract_list .= $entry_list[$i];
								$display_list[count($display_list)] = $entry_list[$i];
							}
						}

						/***** Added By Muhammad Imran Mirza *****/

						/***** Comment By Muhammad Imran Mirza *****/

				/*
		        if ($num_of_records>0){
				    $r = $this->parent->db_pointer->database_fetch_array($result);
					$shop_enabled = $r["info_shop_enabled"];
		            $this->search_page_size = $r["info_searchresults"];
		            $clist                  = $r["info_category"];
		            $this->call_command("DB_SEEK", Array($result,0));
		            if($page==1){
					    $start	= 0;
		            	$end	= $this->search_page_size;
				    } else {
					    $start  = ($page - 1)*$this->search_page_size;
					    $end    = $start + ($this->search_page_size - 1 );
				    }
				    if ($end >= $num_of_records){
					    $end    = $num_of_records;
				    }
		            if($start != 0){
		                $this->call_command("DB_SEEK", Array($start,$result));
		            }
		            $pos = $start;
		            while (($r = $this->parent->db_pointer->database_fetch_array($result)) && ($pos < $end)){
		                $entry_list[count($entry_list)] = $r["ie_parent"];
		                $pos ++;
		            }
		            $complete_extract_list = join(", ", $entry_list);
		        }
		        $this->parent->db_pointer->database_free_result($result);
				*/

						/***** Comment By Muhammad Imran Mirza *****/

				//echo $complete_extract_list;
		        if ($complete_extract_list!=""){
				//$choosen_display_format = 0;
					if($choosen_display_format==0){
				        $sql="select metadata_details.*, ie_identifier, ie_uri, ie_parent, information_fields.*, iev_value, cat_identifier, cat_label,cat_parent, cat_list_id
								from metadata_details
									inner join information_entry  on md_link_id = ie_identifier and md_module='INFORMATIONADMIN_' and md_client=ie_client
									inner join information_entry_values on iev_list = ie_list and ie_client= iev_client and iev_entry = ie_identifier and if_name = iev_field
									inner join information_fields on (if_list = iev_list and iev_client = if_client and iev_field = if_name and if_screen = 1 )
									left outer join category_to_object on cto_module='INFORMATIONADMIN_' and cto_client=ie_client and cto_object=ie_identifier
									left outer join category on cat_client = cto_client and cat_identifier = cto_clist
								where
									 ie_parent in (".$complete_extract_list.") and ie_client = $this->client_identifier and ie_cached=1
								order by iev_entry, if_rank, if_name";
						//and if_type in ('text','select', 'radio', 'check', 'list','memo')
//						print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			            if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			            $result  = $this->parent->db_pointer->database_query($sql);
			    		$dirout ="<text><![CDATA[
							<script type=\"text/javascript\" src=\"/libertas_images3/javascripts/sortabletable.js\"><!-- load sortable table--></script>
							<link type=\"text/css\" rel=\"StyleSheet\" href=\"/libertas_images3/themes/sortabletable.css\">
							<table class='sortable' cellspacing='0' cellpadding='0' style='width:95%'>
						";
			    		$entrys			= Array();
			    		$field_list		= Array();
						$field_list_key	= Array();
			    		$field_label	= Array();
			    		$ids			= Array();
			    		$c=0;
			    		$prev_parent = "";
	//					print_r($keymap);
			    		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			    			if($prev_parent != $r["ie_parent"]){
			    				//print "<li>$c</li>";
			    				$prev_parent = $r["ie_parent"];
			    			}
			    			// retrieve the page of results
							if(!isset($entrys[$r["ie_parent"]])){
		    					$entrys[$r["ie_parent"]] = Array();
								$m=count($keymap);
	//							print "[$m]";
								for($x=0;$x<$m;$x++){
	//								print "<li>".$keymap[$x][0]." - ".$keymap[$x][1]." = ".$this->check_parameters($r,$keymap[$x][1])."</li>";
									$entrys[$r["ie_parent"]][$keymap[$x][0]]=Array($this->check_parameters($r,$keymap[$x][1]),"text",0,"");
									if(!in_array($keymap[$x][0], $field_list)){
										$field_list[$keymap[$x][3]]		= $keymap[$x][0];
										$field_label[$keymap[$x][3]]	= $keymap[$x][2];
				    					$field_list_key[$keymap[$x][3]]		= $keymap[$x][0]."-".$keymap[$x][3];
										$types[$keymap[$x][0]."-".$keymap[$x][3]]		= $keymap[$x][4];
									}
								}
								$ids[count($ids)]=$r["ie_identifier"];
		    				}
		    				if(!in_array($r["if_name"], $field_list_key)){
								$ok=0;
								for($i = 0; $i < count($keymap) ; $i++){
									if($keymap[$i][0] == $r["if_name"]){
										$ok=1;
									}
								}
								if($ok==1){
									if(!in_array($r["if_name"]."-".$r["if_rank"], $field_list_key)){
			    						$field_list_key[count($field_list_key)]		= $r["if_name"]."-".$r["if_rank"];
				    					$field_list[count($field_list)]				= $r["if_name"];
				    					$field_label[count($field_label)]			= $r["if_label"];
										$types[$r["if_name"]."-".$r["if_rank"]]		= $r["if_type"];
									}
								}
		    				}
							if($r["cat_identifier"]!="")
			    				$cat[$r["cat_identifier"]] = Array("cat_label"=>$r["cat_label"],"cat_parent"=>$r["cat_parent"],"cat_list"=>$r["cat_list_id"],"path"=>"");
		    				$clist = $r["cat_list_id"];
							$uri = $r["ie_uri"];
							if(substr($uri,0,1)=="/"){
								$uri = substr($uri,1);
							}
							$entrys[$r["ie_parent"]][$r["if_name"]] = Array($r["iev_value"],$r["if_type"],$r["if_filterable"],$uri);
		    				$entrys[$r["ie_parent"]]["cat"] = $r["cat_identifier"];
			    		}
			            $catlist = $this->call_command("CATEGORY_LOAD", 
							Array(
								"identifier"	=> $clist, 
								"recache"		=> 0, 
								"return_array"	=> 1
							)
						);
						
						$fields = $this->get_field_defs($dirid,1);
						$available_fields = $this->get_screen_fields(1, $fields);
	//					print_r($available_fields);
	//					print_r($field_list_key);
	//					print_r($field_list);
		
			            $total_num_pages = $num_of_records;
			    		$num_pages = ceil($total_num_pages / $this->search_page_size);
			            $this->parent->db_pointer->database_free_result($result);
			    		$fc = count($field_list);
			    		$dirout .= "<tr>";
			    			$dirout .= "<th>#</th>";
				    		for($i=0;$i<$fc;$i++){
								if(in_array($field_list_key[$i],$available_fields)){
									if($field_label[$i]!="colsplitter" && $field_label[$i]!="rowsplitter"){
					    				$dirout .= "<th>";
					    				$dirout .= $field_label[$i];
					    				$dirout .= "</th>";
									}
								}
				    		}
							if ($shop_enabled==1 && ! in_array("__add_to_basket__",$field_list)){
								$dirout .= "<th>";
								$dirout .= "Add to basket";
								$dirout .= "</th>";
							}
			    		$dirout .= "</tr>";
			    		$c=1;
			    		$cl = count($entry_list);
	//  					print __LINE__;
	//		            print "<pre>";
	//		            print_r($available_fields);
	//		            print_r($field_list);
	//		            print "</pre>";
						
			    		for($index=0;$index<$cl;$index++){
			    			$dirout .= "<tr>";
			    			$dirout .= "<td style='vertical-align:top;'>".($start+$c)."</td>";
			    			$key = $entry_list[$index];
			    			for($zi=0;$zi<$fc;$zi++){
								if(in_array($field_list_key[$zi],$available_fields)){
			    					if($field_label[$zi]!="colsplitter" && $field_label[$zi]!="rowsplitter"){
										$dirout .= "<td style='vertical-align:top;'>";
					    				$label	= $this->check_parameters( $this->check_parameters($entrys[$key],$field_list[$zi],Array()),0,"[[nbsp]]");
										if($label==""){
											$label = "[[nbsp]]";
										}
					    				$type 	= $this->check_parameters( $this->check_parameters($entrys[$key],$field_list[$zi],Array()),1,"text");
										$filter = $this->check_parameters( $this->check_parameters($entrys[$key],$field_list[$zi],Array()),2,"0");
				    					if($field_list[$zi]=="ie_title"){
											if(count($cat)>0){
												$centry  = $this->check_parameters($entrys,$key,"__NOT_FOUND__");
												if($centry=="__NOT_FOUND__"){
													$path = "";
												} else {
						    						if($cat[$entrys[$key]["cat"]]["path"]==""){
						    							$path = $this->get_path($cat[$entrys[$key]["cat"]], $catlist)."/";
						    							$cat[$entrys[$key]["cat"]]["path"] = $path;
				    								} else {
				    									$path = $cat[$entrys[$key]["cat"]]["path"]."/";
				    								}
												}
											} else {
												$path = "";
											}
				    						$dirout .= "<a href='".dirname($this->parent->real_script)."/$path".$this->make_uri($label).".php'>".$label."</a>";
				    					} else {
											if($field_list[$zi] == "__add_to_basket__"){
												$dirout .= "<a title='Add this item to the basket' href='_add-to-cart.php?identifier=".$ids[$index]."&amp;type=".$this->shop_type."'>Add to basket</a>";
											} else if($field_list[$zi] == "__category__"){
												if(count($cat)>0){
													$path 	 = $this->get_path($cat[$entrys[$key]["cat"]], $catlist)."/";
													$lab = $cat[$entrys[$key]["cat"]]["cat_label"];
												} else {
													$path="";
													$lab = "No Category Defined";
												}
												$dirout .= "<a href='".dirname($this->parent->real_script)."/".$path."index.php' title ='Go to the category $path'>".$lab."</a>";
											} else {
												if($filter=="1"){
													$dirout .= "<a href='".dirname($this->parent->real_script)."/_filter-".$this->make_uri($field_label[$zi])."-".$this->make_uri($label).".php' title='Filter on (".$label.")'>".$label."</a>";
												} else {
													if($type=="URL"){
														if($label!=""){
															if(strpos($label,"http")===false){
																$dirout .= "<a href='http://$label'>$label</a>";
															} else {
																$dirout .= "<a href='$label'>$label</a>";
															}
														}else {
															$dirout .= "[[nbsp]]";
														}
													} else if($type=="email"){
														if($label!=""){
															$dirout .= "<a href='mailto:$label'>$label</a>";
														}else {
															$dirout .= "[[nbsp]]";
														}
													} else {
														$dirout .= $label;
													}
												}
											}
										}
										$dirout .= "</td>";
									}
								}
			    			}
							if ($shop_enabled==1 && ! in_array("__add_to_basket__",$field_list)){
								$dirout .= "<td>";
								$dirout .= "<a href='_add-to-cart.php?identifier=".$ids[$index]."&amp;type=".$this->shop_type."'>Add to basket</a>";
								$dirout .= "</td>";
							}
			    			$dirout .= "</tr>";
			    			$c++;
			    		}
			    		$dirout .= "</table>]]></text>";
			    		$out .= "<data_list command=\"";
	//		    		$searchfilter = "";
			    		$out .= $this->check_parameters($parameters,"PAGE_COMMAND");
			    		if($total_num_pages==0){
			    			$start=-1;
			    			$finish=0;
			    		} else {
			    			if($total_num_pages<$end){
			    				$finish = $total_num_pages;
			    			} else {
			    				$finish = $end;
			    			}
			    		}
			    //		print  "number_of_records=\"".($total_num_pages)."\" start=\"".($start+1)."\" finish=\"".($finish)."\" current_page=\"$page\" number_of_pages=\"$num_pages\" page_size=\"".$this->search_page_size;
			    		$out .= "\" number_of_records=\"".($total_num_pages)."\" start=\"".($start+1)."\" finish=\"".($finish)."\" current_page=\"$page\" number_of_pages=\"$num_pages\" page_size=\"".$this->search_page_size."\">\n";
			    		$qstr = str_replace("+"," ",$this->parent->qstr);
			    		$pos = strpos($qstr,"page=$page");
			    		if ($pos===false){
			    			// do nothing
			    		} else {
			    			// remove page=$page
			    			$qstr = substr($qstr,0,$pos).substr($qstr,$pos+strlen("page=$page"));
			    		}
			    		$out .= "<searchfilter><![CDATA[".$qstr."]]></searchfilter>";
						$out .= "<cat_label><![CDATA[$cat_label]]></cat_label>\n";
			    		$out .= "<pages>\n";
			    		for($index=1;$index<=$num_pages;$index++){
			    			$out .= "<page>$index</page>\n";
			    		}
			    		$out .= "</pages>\n";
			    		$out .= $dirout;
			    		$out .= "</data_list>\n";
					} else {
						// display as summary
				        $sql="select ie_parent, md_identifier, info_identifier, info_summary_layout
								from information_entry  
									inner join information_list on info_identifier = ie_list and info_client=ie_client
									inner join metadata_details on md_link_id = ie_identifier and md_module='$this->webContainer' and md_client= ie_client
								where
									 ie_parent in (".$complete_extract_list.") and ie_client = $this->client_identifier and ie_cached=1 and ie_published =1 and ie_status=1
								order by ie_uri";
//						print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";

						$result  = $this->parent->db_pointer->database_query($sql);

						$numRecords = $this->call_command("DB_NUM_ROWS",array($result));
//						print "[$numRecords]";
						if ($numRecords==1){
							$r = $this->parent->db_pointer->database_fetch_array($result);
							$parameters["unset_identifier"] = $r["ie_parent"];
	                        $this->parent->db_pointer->database_free_result($result);
							return $this->information_show($parameters);
						}else {
							
						}


					    $cats = $this->call_command("CATEGORY_LOAD",Array("identifier" => $cat_id, "category" => $cat_id, "recache"=>0));
						/**
				        * start to display the results
				        */
				//		$out  = "<module name=\"".$this->module_name."\" display=\"INFORMATION\">\n";
				//		$parameters["menu_url"] 		= dirname($this->parent->script)."/_search.php";
				//		$out .= $this->filter($parameters, $info_identifier);
						$out .="<shop><![CDATA[".$this->shop_type."]]></shop>";
						$out .= "<list>$dirid</list>";
						$out .= "<link_to_real_url type='1'></link_to_real_url>";
				/*
						$out .= "<workflow>$work_status</workflow>";
						$out .= "<label><![CDATA[$label]]></label>\n";
				*/
						$out .= "<cat_label><![CDATA[$cat_label]]></cat_label>\n";
						$o = split("_",$this->webContainer);
						$out .= "<display_type><![CDATA[".$o[0]."]]></display_type>\n";
						$out .= "<display_format><![CDATA[$display_format]]></display_format>\n";
						$out .= "<display_columns><![CDATA[$display_columns]]></display_columns>\n";
						$out .= "<label><![CDATA[$label]]></label>\n";
				//		$out .= "<update_access status='$info_update_access' />";
				//		$out .= "<info_add_label><![CDATA[$info_add_label]]></info_add_label>\n";
				//		$out .= "<info_no_stock_label><![CDATA[$info_no_stock_label]]></info_no_stock_label>\n";
				//		$out .= "<info_no_stock_display><![CDATA[$info_no_stock_display]]></info_no_stock_display>\n";
						$out .= "<fake_uri><![CDATA[".$this->parent->script."]]></fake_uri>\n";
						$out .= "<current_category show_sub='1'>$cat_id</current_category>\n$cats";
//						$out .= "<current_category show_sub='1'>$category</current_category>\n$cats";

						/***** Comment By Muhammad Imran Mirza *****/

						/*
						$num_of_pages = 1;

				//		$this->search_page_size=50;
//						print "[$numRecords]";
						if($numRecords>$this->search_page_size){
							$num_of_pages = ceil($numRecords/$this->search_page_size);
						}
						if ($page>$num_of_pages){
							$page=1;
						}

						/***** Comment By Muhammad Imran Mirza *****/

						/***** Added By Muhammad Imran Mirza *****/

						$pages="";
						//$numRecords = $num_of_records;
			            $total_num_pages = $num_of_records;
			    		$num_pages = ceil($total_num_pages / $this->search_page_size);
						$num_of_pages = $num_pages;

						/***** Added By Muhammad Imran Mirza *****/

						if ($num_of_pages>1){
							for($i=0; $i<$num_of_pages; $i++){
								$pages.="<page>".($i+1)."</page>";
								$this->checkPage(
									"_page".($i+1).".php",
									dirname($this->parent->real_script),
									Array("page"=>($i+1),"category"=>$category),
									$this->module_command."DISPLAY",
									$label ." - page ".($i+1)
				                );
								if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"_page".($i+1).".php"));}
							}
						}

						/***** Comment By Muhammad Imran Mirza *****/

						/*
						$start = 0 + (($page - 1) * $this->search_page_size);
						$end = $start+$this->search_page_size;
						if($end>$numRecords){
							$end=$numRecords;
						}
						if($start>0){
							$this->call_command("DB_SEEK",array($result,$start));
						}


			            $total_num_pages = $num_of_records;
			    		$num_pages = ceil($total_num_pages / $this->search_page_size);
						*/

						/***** Comment By Muhammad Imran Mirza *****/
						
						/***** Added By Muhammad Imran Mirza *****/

			    		if($total_num_pages==0){
			    			$start=-1;
			    			$finish=0;
			    		} else {
			    			if($total_num_pages<$end){
			    				$finish = $total_num_pages;
			    			} else {
			    				$finish = $end;
			    			}
			    		}

						/***** Added By Muhammad Imran Mirza *****/


						$pos = $start;
						$listOfEntries=Array();
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"get list"));}
						$numRecords=0;
//						print "[$pos,$end]";
						while (($r = $this->parent->db_pointer->database_fetch_array($result)) && ($pos<$end)){
							$listOfEntries[count($listOfEntries)]= Array(
								"parent"	=> $r["ie_parent"],
								"md_id"		=> $r["md_identifier"]
							);
							$pos++;
							$numRecords++;
//							$identifier = $r["info_identifier"];
							$info_summary_layout =$r["info_summary_layout"];
//							print "[$pos]";
						}
						
						////categories
						$sql = "select * from category_to_object 
										inner join category on cat_identifier = cto_clist and cat_client=cto_client
									where cto_client = $this->client_identifier";
							//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
							$result  = $this->parent->db_pointer->database_query($sql);
                            while($r = $this->parent->db_pointer->database_fetch_array($result)){
                            	$cat[$r["cat_identifier"]] = Array("cat_label"=>$r["cat_label"],"cat_parent"=>$r["cat_parent"],"cat_list"=>$r["cat_list_id"],"path"=>"");
                            }
							$cat_path = "";
							foreach($cat as $key => $catEntry){
								if($cat[$key]["path"]==""){
									$path = $this->get_path($cat[$key], $catlist);
									$cat[$key]["path"] = $path;
								} else {
									$path = $cat[$key]["path"];
								}
								$cat_path .= "<cat_path id='$key'><![CDATA[".dirname($this->parent->script)."/$path]]></cat_path>";
							}
						/////categories
						
						$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
						$editable=0;
						/**
						*
						*/
						$info_summary_layout =0;
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"loading"));}
//						print __LINE__." @ [$numRecords]";
				        if ($numRecords > 0){
					        $out .= "	<content category='$category' editable='$editable'>\n";
					        $out .= "		<info list='$dirid'>
								                <display type='$info_summary_layout' summary_only='$info_summary_only'>";
						    if ($numRecords>1 || ($numRecords==1 && $info_summary_layout=1)){
							    $lang="en";
								$screen=1;
						    } else {
							    //load cache
							    $lang="en";
								$screen=2;
						    }
							$fields = $this->get_field_defs($dirid,$screen);
							//print_r($fields);
							$out .= $this->display_screen($screen, $fields, $dirid);
				            $out.="	$cat_path</display>
					        <results>";
							$m = count($listOfEntries);
							$lang="en";

							for($i=0; $i<$m; $i++){
								$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$dirid."_".$lang."_".$listOfEntries[$i]["parent"].".xml";
								if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
								if (file_exists($fname)){
									$out .= join("", file($fname));
									$mname = $data_files."/metadata_".$this->client_identifier."_".$lang."_".$listOfEntries[$i]["md_id"].".xml";
									if (file_exists($mname)){
										$out .= join("", file($mname));
									}
								}
							}

						$out .= "		<pages>$pages</pages></results></info></content>\n";

						/***** Added By Muhammad Imran Mirza *****/

						$out_sub = $out;
//						$out = "";
			    		$out = "<data_list command=\"";
	//		    		$searchfilter = "";
			    		$out .= $this->check_parameters($parameters,"PAGE_COMMAND");
						/*
			    		if($total_num_pages==0){
			    			$start=-1;
			    			$finish=0;
			    		} else {
			    			if($total_num_pages<$end){
			    				$finish = $total_num_pages;
			    			} else {
			    				$finish = $end;
			    			}
			    		}
						*/
			    //		print  "number_of_records=\"".($total_num_pages)."\" start=\"".($start+1)."\" finish=\"".($finish)."\" current_page=\"$page\" number_of_pages=\"$num_pages\" page_size=\"".$this->search_page_size;
			    		$out .= "\" number_of_records=\"".($total_num_pages)."\" start=\"".($start+1)."\" finish=\"".($finish)."\" current_page=\"$page\" number_of_pages=\"$num_pages\" page_size=\"".$this->search_page_size."\">\n";
			    		$qstr = str_replace("+"," ",$this->parent->qstr);
			    		$pos = strpos($qstr,"page=$page");
			    		if ($pos===false){
			    			// do nothing
			    		} else {
			    			// remove page=$page
			    			$qstr = substr($qstr,0,$pos).substr($qstr,$pos+strlen("page=$page"));
			    		}
			    		$out .= "<searchfilter><![CDATA[".$qstr."]]></searchfilter>";
						$out .= "<cat_label><![CDATA[$cat_label]]></cat_label>\n";
			    		$out .= "<pages>\n";
			    		for($index=1;$index<=$num_pages;$index++){
			    			$out .= "<page>$index</page>\n";
			    		}
			    		$out .= "</pages>\n";
			    		$out .= $out_sub;
			    		$out .= "</data_list>\n";

//		echo $pages;
//		$this->exitprogram();

						/***** Added By Muhammad Imran Mirza *****/
							
						}
                       $this->parent->db_pointer->database_free_result($result);
					}




					$out  =	"<module name=\"".$this->module_name."\" display=\"INFORMATION\">$a2zout$out</module>";
					//echo $out;
//		$this->exitprogram();
					return $out;
				} else {
					return "<module name=\"".$this->module_name."\" display=\"ATOZ\">
	            	    $a2zout
	        	  	  <text><![CDATA[".LOCALE_GENERAL_NO_LETTER_RESULTS."]]></text>
	    	        </module>";
				}
			} else {
				//$out .= "<text class='pagelabel'><![CDATA[Search the directory A to Z, ]]></text>";
				
				
	    $cats = $this->call_command("CATEGORY_LOAD",Array("identifier" => $cat_id, "category" => $cat_id, "recache"=>0));
		/**
        * start to display the results
        */
//		$out  = "<module name=\"".$this->module_name."\" display=\"INFORMATION\">\n";
//		$parameters["menu_url"] 		= dirname($this->parent->script)."/_search.php";
//		$out .= $this->filter($parameters, $info_identifier);
		$out .="<shop><![CDATA[".$this->shop_type."]]></shop>";
		$out .= "<list>$dirid</list>";
		$out .= "<link_to_real_url type='1'></link_to_real_url>";
/*
		$out .= "<workflow>$work_status</workflow>";
		$out .= "<label><![CDATA[$label]]></label>\n";
*/
		$out .= "<cat_label><![CDATA[$cat_label]]></cat_label>\n";
		$o = split("_",$this->webContainer);
		$out .= "<display_type><![CDATA[".$o[0]."]]></display_type>\n";
		$out .= "<display_format><![CDATA[$display_format]]></display_format>\n";
		$out .= "<display_columns><![CDATA[$display_columns]]></display_columns>\n";
		$out .= "<label><![CDATA[$label]]></label>\n";
//		$out .= "<update_access status='$info_update_access' />";
//		$out .= "<info_add_label><![CDATA[$info_add_label]]></info_add_label>\n";
//		$out .= "<info_no_stock_label><![CDATA[$info_no_stock_label]]></info_no_stock_label>\n";
//		$out .= "<info_no_stock_display><![CDATA[$info_no_stock_display]]></info_no_stock_display>\n";
		$out .= "<fake_uri><![CDATA[".$this->parent->script."]]></fake_uri>\n";
		$out .= "<current_category show_sub='1'>$cat_id</current_category>\n$cats";
				
				$out  =	"<module name=\"".$this->module_name."\" display=\"ATOZ\">$a2zout$out</module>";
				return $out;
			}
		}
    }
	
	function get_field_defs($ie_id, $screen=-1){
		if ($screen==-1){
			$sql= "select * from information_fields 
						left outer join information_field_protection on ifp_field = if_identifier and ifp_client = if_client and if_list = ifp_list
					where if_client=$this->client_identifier and if_list = $ie_id order by if_rank";
		} else {
			$sql= "select * from information_fields 
						left outer join information_field_protection on ifp_field = if_identifier and ifp_client = if_client and if_list = ifp_list
					where if_client=$this->client_identifier and if_list = $ie_id and (if_screen=$screen or if_screen=0) order by if_screen desc, if_rank";
		}
		
//		print "<"."!--".__FILE__."@".__LINE__." $sql    --".">  \n";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
        $result  = $this->parent->db_pointer->database_query($sql);
		$fields = Array();
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if (($screen==-1) || ($screen == $r["if_screen"])){
				$save_key = $r["if_name"]."-".$r["if_rank"];
//				print "<"."!--".__FILE__."@".__LINE__." $save_key ".$r["if_label"]."  --".">  \n";
    	    	if("__NOT_FOUND__" == $this->check_parameters($fields,$r["if_name"],"__NOT_FOUND__")){
					$generalKey[$r["if_screen"]][$r["if_name"]] = $save_key;
					$fields[$save_key] = Array(
						"protected"	=> 0, 
						"filter"	=> $r["if_filterable"], 
						"groups"	=> Array(), 
						"screens" 	=> Array(), 
						"link_detail"	=> $r["if_link"], 
						"sumlabel"	=> $r["if_sumlabel"], 
						"conlabel"	=> $r["if_conlabel"], 
						"label"		=> $r["if_label"]
					);
				}
				$grp = $this->check_parameters($r,"ifp_group","__NOT_FOUND__");
				if ("__NOT_FOUND__" != $grp){
					$fields[$save_key]["protected"] = 1;
					$fields[$save_key]["groups"][count($fields[$save_key]["groups"])] = $r["ifp_group"];
				}
				$fields[$save_key]["screens"][count($fields[$save_key]["screens"])] = Array($r["if_screen"], $r["if_rank"]);
//				print "<li><strong>".$save_key."</strong> - ".print_r($fields[$save_key],true).", ". print_r(Array($r["if_screen"], $r["if_rank"]),true)."</li>";
			}
//			print "<li>".$r["if_name"]." - ".$r["if_screen"]."</li>";
			if (($screen!=-1) && ($screen != $r["if_screen"])){
				$grp = $this->check_parameters($r,"ifp_group","__NOT_FOUND__");
				$k = $this->check_parameters($generalKey[$screen],$r["if_name"],$r["if_name"]."-NEW");
				$savescreen = 1;
				if(substr($k,-4)=="-NEW"){
					$savescreen = 0;
				}
//				print "<p>[".$r["if_name"]." - $k]</p>";
				if($k!=""){
					if ("__NOT_FOUND__" != $grp){
	    	    		if("__NOT_FOUND__" == $this->check_parameters($fields,$k,"__NOT_FOUND__")){
							$generalKey[$screen][$r["if_name"]] = $k;
							$fields[$k] = Array(
								"protected"	=> 0, 
								"filter"	=> $r["if_filterable"], 
								"groups"	=> Array(), 
								"screens" 	=> Array(), 
								"link_detail"	=> $r["if_link"], 
								"sumlabel"	=> $r["if_sumlabel"], 
								"conlabel"	=> $r["if_conlabel"], 
								"label"		=> $r["if_label"]
							);
						}
						//print $k;
						$fields[$k]["protected"] = 1;
						$fields[$k]["groups"][count($fields[$k]["groups"])] = $r["ifp_group"];
					}
					if ($r["if_filterable"]==1){
	    	    		if("__NOT_FOUND__" == $this->check_parameters($fields,$k,"__NOT_FOUND__")){
							$generalKey[$screen][$r["if_name"]] = $k;
							$fields[$k] = Array(
								"protected"	=> 0, 
								"filter"	=> $r["if_filterable"], 
								"groups"	=> Array(), 
								"screens" 	=> Array(), 
								"link_detail"	=> $r["if_link"], 
								"sumlabel"	=> $r["if_sumlabel"], 
								"conlabel"	=> $r["if_conlabel"], 
								"label"		=> $r["if_label"]
							);
						}
						$k = $generalKey[$screen][$r["if_name"]];
						$fields[$k]["filter"] = 1;
					}
					if($savescreen==1){
//						print "<li>before) ".$k." - ".count($fields[$k]["screens"]).", ". print_r(Array($r["if_screen"], $r["if_rank"]),true)." ". print_r($fields[$k],true)."</li>";
						$fields[$k]["screens"][count($fields[$k]["screens"])] = Array($r["if_screen"], $r["if_rank"]);
//						print "<li>after)".$k." - ".count($fields[$k]["screens"]).", ". print_r(Array($r["if_screen"], $r["if_rank"]),true)." ". print_r($fields[$k],true)."</li>";
					}
				}
			}
        }

//		print "<!"."-- ";
//		print_r($generalKey);
//		print_r($fields);
//		print "--".">";

		$this->parent->db_pointer->database_free_result($result);
		return $fields;
	}
	
	function display_screen($screen, $fields, $identifier){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"[$screen]".print_r($fields,true)));}
		$out ="<seperator_row>\n";
		$out .="	<seperator>\n";
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$settings = $this->call_command("SHOP_GET_SETTINGS");
//		print "<"."!-- ";
//		print_r($grp_info);
//		print_r($fields);
//		print "--"."> ";

		$flist = Array();
		foreach ($fields as $key => $value){
			$ok =0;
			for($i=0;$i<count($fields[$key]["screens"]);$i++){
				if($fields[$key]["screens"][$i][0]==$screen){
					$flist[$fields[$key]["screens"][$i][1]] = $key;
					break;
				}
			}
		}
		ksort($flist);
//
//		print "<"."!-- ";
//		print_r($flist);
//		print "--"."> ";
//
		foreach($flist as $index => $key){
			$l = split("-",$key);
			$fkey = $l[0];
//			print "<"."!-- $fkey --".">";
			if($fkey=="ie_splitterRow"){
				$out .="	</seperator>";
				$out .="</seperator_row>";
				$out .="<seperator_row>\n";
				$out .="	<seperator>\n";
			} else if ($fkey=="ie_splitterCol"){
				$out .="	</seperator>";
				$out .="	<seperator>\n";
			} else {
				$ok=0;
				if($fields[$key]["protected"]==0){
					$ok = 1;
				} else {
					$count_user_groups=count($grp_info);
					$count_field_groups=count($fields[$key]["groups"]);
					for($fg=0;$fg<$count_field_groups;$fg++){
						for($ug=0;$ug<$count_user_groups;$ug++){
							if (in_array($grp_info[$ug]["IDENTIFIER"],$fields[$key]["groups"])){
								$ok=1;
							}
						}
					}
				}
				if($ok==1){
					if($screen==1 && $fkey=='ie_title'){
						$out .="<field id='$fkey' link='".$fields[$key]["link_detail"]."' sumlabel='".$fields[$key]["sumlabel"]."' conlabel='".$fields[$key]["conlabel"]."'><label><![CDATA[".$fields[$key]["label"]."]]></label></field>\n";
					} else if($fkey=="ie_price"){
						$out .= "<field id='$fkey' link='".$fields[$key]["link_detail"]."' sumlabel='".$fields[$key]["sumlabel"]."' conlabel='".$fields[$key]["conlabel"]."' currency='[[".strtolower($this->check_parameters($settings,"ss_currency","gbp"))."]]'><label><![CDATA[".$fields[$key]["label"]."]]></label></field>\n";
					} else {
						$field_filter = "";
						$filter_attribute ="";
						if($fields[$key]["filter"]==1){
								$filteroptions="";
								$sql = "select * from information_list
											inner join information_fields on info_identifier = if_list and if_client = info_client and if_filterable=1 and if_name='$fkey'
											inner join information_options on io_list = info_identifier and io_field = if_name and io_client = info_client
										where info_identifier = $identifier and info_client=$this->client_identifier and if_screen=0 order by io_rank";
//								print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
						        $result  = $this->parent->db_pointer->database_query($sql);
						        while($r = $this->parent->db_pointer->database_fetch_array($result)){
									$filteroptions .= "<option value='_filter-".$this->make_uri(urldecode($r["if_label"]))."-".$this->make_uri(urldecode($r["io_value"])).".php'><![CDATA[".urldecode($r["io_value"])."]]></option>";
							    }
								$field_filter = "<filteroptions>$filteroptions</filteroptions>\n";
								$filter_attribute = "filter='1' ";
						}
						$out .="<field id='$fkey' $filter_attribute link='".$fields[$key]["link_detail"]."' sumlabel='".$fields[$key]["sumlabel"]."' conlabel='".$fields[$key]["conlabel"]."'><label><![CDATA[".$fields[$key]["label"]."]]></label>$field_filter</field>\n";
					}
				}
			}
		
		}
		$out .="	</seperator>";
		$out .="</seperator_row>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::output",__LINE__,"$out"));}
		return $out;
	}
	
	function get_screen_fields($screen, $fields){
		$field = Array();
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
//		print_r($grp_info);
		$flist = Array();
		foreach ($fields as $key => $value){
			$ok =0;
			for($i=0;$i<count($fields[$key]["screens"]);$i++){
				if($fields[$key]["screens"][$i][0]==$screen){
					$flist[$fields[$key]["screens"][$i][1]] = $key;
					break;
				}
			}
		}
		ksort($flist);
		foreach($flist as $index => $key){
			//if(in_array($screen,$fields[$key]["screens"])){
				if($key=="rowsplitter"){
					$field[count($field)] = $key;
				} else if ($key=="colsplitter"){
					$field[count($field)] = $key;
				} else {
					$ok=0;
					if($fields[$key]["protected"]==0){
						$ok = 1;
					} else {
						$count_user_groups=count($grp_info);
						$count_field_groups=count($fields[$key]["groups"]);
						for($fg=0;$fg<$count_field_groups;$fg++){
							for($ug=0;$ug<$count_user_groups;$ug++){
								if (in_array($grp_info[$ug]["IDENTIFIER"],$fields[$key]["groups"])){
									$ok=1;
								}
							}
						}
					}
					if($ok==1){
						$field[count($field)] = $key;
					}
				}
			//}
		}
		return $field;
	}
	
	
	/**
    * a gateway function to save an advanced form builder form to this module
    */
	function save_fba($parameters){
//		print "here";
//		print_r($parameters);
//		$this->exitprogram();
		$list 		= $this->check_parameters($parameters, "module_identifier");
		$__category__ 		= $this->check_parameters($parameters, "__category__",-1);
		$parameters["identifier"] = $this->check_parameters($parameters, "ie_identifier",-1);
		if ($parameters["identifier"]==""){
			$parameters["identifier"]=-1;
		}
		if ($__category__!=-1){
			$parameters["entrycategory"]=$parameters["__category__"];
		}
		return $this->information_save($parameters);
	}
	/**
    * a gateway function to retrieve an advanced form builder form entry
    */
	function load_fba($parameters){
	//	print_r($parameters);
		$list 		= $this->check_parameters($parameters, "module_identifier", -1);
		$mod_fields	= $this->check_parameters($parameters, "mod_fields", Array());
		$user		= $this->check_parameters($parameters, "user", -1);
		$params		= $this->check_parameters($parameters, "parameters", -1);
		$field_list = "";
		$match = Array();
		$sql = "select * from information_fields where if_screen=0 and if_list = $list and if_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if ($r["if_map"]!=""){
	        	$match[$r["if_name"]] = $r["if_map"];
			}
        }
		$this->parent->db_pointer->database_free_result($result);
//		print_r($match);
		for($i=0;$i<count($mod_fields);$i++){
			if($mod_fields[$i][5]=="INFORMATIONADMIN_::$list"){
				if($mod_fields[$i][0]=="__category__"){
					$match[$mod_fields[$i][0]] = "cto_clist";
				} else if($mod_fields[$i][0]=="__user__"){
					$match[$mod_fields[$i][0]] = "ie_user";
				} else if($mod_fields[$i][0]=="" || $mod_fields[$i][0]=="ie_identifier" || $mod_fields[$i][0]=="ie_parent"){
					$field_list .="";
				} else {
					if($field_list!=""){
						$field_list .=", ";
					}
					$field_list .="'".$mod_fields[$i][0]."'";
				}
			}
		}
		if($user==-1){
		$sql = "select * from information_entry_values 
					inner join information_entry on ie_identifier = iev_entry and ie_client = iev_client and iev_list = ie_list
					inner join information_fields on if_name = iev_field and iev_client = if_client and if_list = iev_list and if_screen =0
					inner join metadata_details on ie_identifier = md_link_id and ie_client = md_client and md_module = '".$this->webContainer."'
					left outer join  category_to_object on (cto_object = ie_identifier and ie_client = cto_client and cto_module='".$this->webContainer."')
				where ie_version_wip=1 and iev_field in ($field_list) and iev_client=$this->client_identifier and ie_user = ".$_SESSION["SESSION_USER_IDENTIFIER"]." and iev_list = $list ";
		} else {
		$sql = "select * from information_entry_values 
					inner join information_entry on ie_identifier = iev_entry and ie_client = iev_client and iev_list = ie_list
					inner join information_fields on if_name = iev_field and iev_client = if_client and if_list = iev_list and if_screen =0
					inner join metadata_details on ie_identifier = md_link_id and ie_client = md_client and md_module = '".$this->webContainer."'
					left outer join category_to_object on (cto_object = ie_identifier and ie_client = cto_client and cto_module='".$this->webContainer."')
				where 
				ie_version_wip=1 and 
				iev_field in ($field_list) and iev_client=$this->client_identifier and ie_user = ".$user." and iev_list = $list ";
		}
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		// 33502031579046141
		$result  = $this->parent->db_pointer->database_query($sql);
		$values = Array();
		$ie_identifier=-1;
		$ie_parent=-1;
		$ie_status = 0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
//			print "<li>".$r["if_type"]." ".$r["iev_field"]." ".$r["if_map"]." </li>";
			if($r["if_type"]=="check"){
				if("__NOT_FOUND__" == $this->check_parameters($values,$r["iev_field"],"__NOT_FOUND__")){
					$values[$r["iev_field"]] = Array();
				}
           		$values[$r["iev_field"]][count($values[$r["iev_field"]])] = $r["iev_value"];
			} else {
				$values[$r["iev_field"]] = $r["iev_value"];
			}
//			print "<li>".$values[$r["iev_field"]]." </li>";
			$ie_identifier	= $r["ie_identifier"];
			$ie_parent		= $r["ie_parent"];
			$ie_status		= $r["ie_status"];
			foreach($match as $key => $v){
				if($v!="md_description"){
	    			$values[$key] = $r[$v];
				}
			}
	    }
        $this->parent->db_pointer->database_free_result($result);
//		print "<LI>ie_status = $ie_status</LI>";
		$mod_fields[count($mod_fields)] = Array("ie_status", "", "system", "", "", "INFORMATIONADMIN_::$list", "0", "value"=>"$ie_status" , "required"=>0);
		for($i=0;$i<count($mod_fields);$i++){
			if($mod_fields[$i][5]=="INFORMATIONADMIN_::$list"){
				if($mod_fields[$i][0]=="__category__"){
					$mod_fields[$i]["value"] = $this->check_parameters($values, $mod_fields[$i][0]);
				} else if($mod_fields[$i][0]=="__user__"){
					$mod_fields[$i]["value"] = $this->check_parameters($values, $mod_fields[$i][0]);
				} else if($mod_fields[$i][0]=="ie_identifier"){
					$mod_fields[$i]["value"] = $ie_identifier;
				} else if($mod_fields[$i][0]=="ie_parent"){
					$mod_fields[$i]["value"] = $ie_parent;
				} else if($mod_fields[$i][0]=="ie_status"){
					// already done
				} else {
					$mod_fields[$i]["value"] = $this->check_parameters($values, $mod_fields[$i][0], $mod_fields[$i]["value"]);
				}
			}
		}
/*
		print "<pre> mod fields";
		print_r($values);
		print_r($mod_fields);
		print "</pre>";
*/
		return $mod_fields;
	}
	/*************************************************************************************************************************
    * set the ass rss field to zero (on deletion of RSS feed)
    *************************************************************************************************************************/
	function update_rss($parameters){
		$owner = $this->check_parameters($parameters,"owner");
		$sql = "update information_features set ifeature_as_rss = 0 where ifeature_client=$this->client_identifier and ifeature_identifier=$owner";
		$this->parent->db_pointer->database_query($sql);
	}
    /**
    * display a featured list
    *
    * @param Array ("wo_owner_id", "current_menu_location")
    * @return String XmlString that represents the desired out put of this feature list
    */
    function featured_list_rss($parameters){
		$__layout_position		= $this->check_parameters($parameters, "__layout_position");
		$cmd					= $this->check_parameters($parameters, "command");
		if($__layout_position==2 && $cmd !=''){
			return "";
		}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,print_r($parameters,true)));}
		$identifier  			= $this->check_parameters($parameters,"identifier",-1);
		$current_menu_location	= $this->check_parameters($parameters,"current_menu_location",-1);
		$lang					= "en";
		$fake_uri				= "";
	    $list_type				= -1;
		$list					= -1;
		$label					= "";
		$format					= -1;
		$auto_counter			= -1;
		$category				= -1;
		$out					= "";
		/*
		+=-=-=-
		| 2 Featured company settings:
		| * Manually choose & set for a period of time e.g. X days between certain dates
		|	0	define multiple entries, timed rotation
		|	0	will need display options (configurable) could be full content or summary
		| *	Automatically choose X number of entries
		|	0	new selection per visit / per page load
		|	0	will need display options (configurable) could be full content or summary
		| *	Both manual + automatic could appear on same page - positioning & ranking important
		|
		| *	Status (live/draft)
		| *	Display Container
		| *	List of entries (manual/auto)
		| *	Display options
		|	0	Per visit / per page
		|	0	Display content or summary
		|
		+=-=-=-
		*/
		$now = $this->libertasGetDate();
		$sql = "select * from information_features
			left outer join menu_to_object on menu_to_object.mto_object=information_features.ifeature_identifier and menu_to_object.mto_client=information_features.ifeature_client and menu_to_object.mto_module = '".$this->webContainer."FEATURES' and menu_to_object.mto_menu = $current_menu_location
			inner join information_list on ifeature_list = info_identifier and ifeature_client = info_client
			inner join menu_data on info_menu_location = menu_identifier and menu_client = info_client
		where
			ifeature_identifier = $identifier and
			ifeature_client = $this->client_identifier and
			ifeature_status = 1 and
			(
				(ifeature_date_start='0000-00-00 00:00:00' or ifeature_date_start < '$now') and 
				(ifeature_date_finish='0000-00-00 00:00:00' or ifeature_date_finish >= '$now')
			)
		";
		//print '<li> sql : '.$sql.'</li>';		

		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->parent->db_pointer->database_query($sql);
		$c=0;
		$menu_url 		= "";
		$feature_url	= "";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$c++;
		    $list_type		=	$r["ifeature_list_type"];
			$list			=   $r["ifeature_list"];
			$format			=	$r["ifeature_display_format"];
			$rotation		=   $r["ifeature_display_rotation"];
			$label			=	$r["ifeature_label"];
//			$label			=	htmlentities($r["ifeature_label"]);
			$auto_counter	=	$r["ifeature_auto_counter"];
			$start			=	$r["ifeature_date_start"];
			$ifeature_as_rss=	$r["ifeature_as_rss"];
//			$end			=	$r["ifeature_date_finish"];
			$category		=	$r["info_category"];
			$cat_label		=	$r["info_cat_label"];
			$feature_url	=	"_feature-".$this->make_uri($r["info_label"]).".php";
			$menu_url 		=	$r["menu_url"];
        }
        $this->parent->db_pointer->database_free_result($result);
		if($menu_url==""){
			$link_to_real_url = 0;
		}else{
			$link_to_real_url = $this->call_command("LAYOUTSITE_MENU_HAS_ACCESS", Array("fake_uri"=>$menu_url));
		}
		$this->loadedcat = $this->call_command("CATEGORY_LIST_LOAD", Array("identifier" => $category, "recache"=>0, "return_array" => 1, "returntype"=>1, "list"=>$category));
		/**
		* load list of ids to load
		*/
		$where_condition="";
		if($this->module_command=="EVENT_"){
			$where_condition = " md_date_remove>'".$this->libertasGetDate()."' and ";
		}
		if($c!=0){
			$ids= Array();
			$counter=0;
			//[1, 1, 0, 0, Featured Company, 0, 0000-00-00 00:00:00, 0000-00-00 00:00:00]
//			print "[".$list_type.", ".$list.", ".$list_type.", ".$format.", ".$rotation.", ".$label.", ".$auto_counter.", ".$start."]";
			if ($list_type==0){
				// manual get list from table.
				$sql = "select * from information_feature_list
						inner join information_entry on ifl_entry = ie_parent and ie_client = ifl_client and ie_status =1 and ie_published=1 and ie_list = $list
						inner join metadata_details on md_link_id = ie_identifier and ie_client = md_client and md_module='".$this->webContainer."'
						inner join information_list on info_identifier = ie_list and info_client=ie_client
						inner join menu_data on menu_identifier = info_menu_location and info_client=menu_client
					where $where_condition ifl_client = $this->client_identifier and ifl_owner = $identifier order by ifl_rank";
				//print "<li>list type= ".$list_type." ".__FILE__."@".__LINE__."<p>$sql</p></li>";				
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
	            while($r = $this->parent->db_pointer->database_fetch_array($result)){
//	            	$ids[$counter] = $r["ifl_entry"];
	            	$ids[$counter] = Array(
						"identifier" 	=> $r["ifl_entry"], 
//						"title" 		=> htmlentities($r["md_title"]), 
//						"description"	=> htmlentities($r["md_description"]), 
						"title" 		=> $r["md_title"], 
						"description"	=> $r["md_description"], 

						"link" 			=> "http://".$this->parent->domain.$this->parent->base.dirname($menu_url).$this->find_path_for($r["ie_identifier"])."/".$r["ie_uri"],
						"pubdate" 		=> date("r",strtotime($r["md_date_remove"]))
					);

//	            	$menu_url = $r["menu_url"];
					$counter++;
	            }
				$loadall =1;
	            $this->parent->db_pointer->database_free_result($result);
			} else if ($list_type==1){
				// use filter defined by admin user
				$keymap 		= Array();
				$out 			= "";
				$sql = "select * from information_fields  
							inner join information_list on if_list = info_identifier and if_client = info_client
						where if_client = $this->client_identifier and if_screen =3 and if_list = $list	";
				$result  = $this->parent->db_pointer->database_query($sql);
		        while($r = $this->parent->db_pointer->database_fetch_array($result)){
		        	$keymap[count($keymap)] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"]);
		        }
			    $this->parent->db_pointer->database_free_result($result);
				$condition = $this->call_command("FILTERADMIN_GET_SQL",
					Array(
						"owner"		 => $identifier,
						"module"	 => $this->webContainer,
						"cmd"		 => "GET",
						"maps" 		 => $keymap
					)
				);
				if ($condition == " ( )"){
					$condition="";
				}
				/*************************************************************************************************************************
    		    * retrieve order def
	    	    *************************************************************************************************************************/
				$ord = $this->check_parameters($condition,"order",Array("field"=>"","dir"=>0));
				$ord_value="";
				if($ord["field"]!=""){
//					print_r($ord);
					$ord_value .= $ord["field"]. (($ord["dir"]==0)?" asc,":" desc,");
				}
				if(is_array($condition["join"])){
					$condition_sql = join(" ", $condition["join"]);
				} else {
					$condition_sql = $condition["join"];
				}
				$sql = "select * from information_entry
							inner join metadata_details on ie_identifier = md_link_id and md_client= ie_client and md_module='$this->webContainer'
							".$condition_sql."
							inner join information_list on info_identifier = ie_list and ie_client = info_client
							inner join menu_data on menu_identifier = info_menu_location and info_client = menu_client
						where $where_condition ie_published=1 ".$condition["where"]." and ie_status =1 and ie_list = $list and ie_client=$this->client_identifier order by $ord_value md_date_remove";
				if ($this->module_debug){
					print "<li>".__FILE__."@".__LINE__."<pre>".print_r($condition, true)."</pre></li>";
				}
				/* this script to put md_date_remove into events RSS */
				/*
				if ($_SERVER['REMOTE_ADDR'] == "61.5.139.12"){
					$rsss  = $this->parent->db_pointer->database_query("select ie_identifier,iev_value from information_entry inner join information_entry_values  on ie_identifier=iev_entry  where iev_field='ie_odateonly1' and ie_list = 32451627704825116 and ie_published=1 and ie_status =1");
		            while($r = $this->parent->db_pointer->database_fetch_array($rsss)){
		            	 $this->parent->db_pointer->database_query("update metadata_details set md_date_remove='".$r["iev_value"]."' where md_link_id = ".$r["ie_identifier"]);
		            }
	
				}
				*/
				//print "<li>list type= ".$list_type." ".__FILE__."@".__LINE__."<p>$sql</p></li>";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
	            while($r = $this->parent->db_pointer->database_fetch_array($result)){
//	            	$ids[$counter] = $r["ie_parent"];
	            	$ids[$counter] = Array(
						"identifier" 	=> $r["ie_parent"], 
						"title" 		=> $r["md_title"], 
						"description"	=> $r["md_description"], 
						"link" 			=> "http://".$this->parent->domain.$this->parent->base.dirname($menu_url).$this->find_path_for($r["ie_identifier"])."/".$r["ie_uri"],
						"pubdate" 		=> date("r",strtotime($r["md_date_remove"]))
					);
					$fake_uri = $r["menu_url"];
					$counter++;
	            }
	            $this->parent->db_pointer->database_free_result($result);
				$loadall =1;
			} else {
				// Randomly pick a number of entries.
				$keymap 		= Array();
				$out 			= "";
				$sql = "select * from information_fields  
							inner join information_list on if_list = info_identifier and if_client = info_client
						where if_client = $this->client_identifier and if_screen =3 and if_list = $list	";
				$result  = $this->parent->db_pointer->database_query($sql);
		        while($r = $this->parent->db_pointer->database_fetch_array($result)){
		        	$keymap[count($keymap)] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"]);
		        }
			    $this->parent->db_pointer->database_free_result($result);
				$condition = $this->call_command("FILTERADMIN_GET_SQL",
					Array(
						"identifier" => $identifier,
						"module"	 => $this->webContainer,
						"cmd"		 => "GET",
						"maps" 		 => $keymap
					)
				);
				/*
				$sql = "select ie_parent, menu_url from information_entry
							inner join metadata_details on ie_identifier = md_link_id and md_client= ie_client and md_module='$this->webContainer'
							".$condition["join"]."
							inner join information_list on info_identifier = ie_list and ie_client = info_client
							inner join menu_data on menu_identifier = info_menu_location and info_client = menu_client
							where ie_published=1 ".$condition["where"]." and ie_status =1 and ie_list = $list and ie_client=$this->client_identifier order by ie_identifier desc
				";
				*/
				/*************************************************************************************************************************
    			* retrieve order def
	    		*************************************************************************************************************************/
				$ord = $this->check_parameters($condition,"order",Array("field"=>"","dir"=>0));
				$ord_value="";
				if($ord["field"]!=""){
//					print_r($ord);
					$ord_value .= " order by ".$ord["field"]. (($ord["dir"]==0)?" asc":" desc");
				}
				$sql = "select * from information_entry
							inner join metadata_details on ie_identifier = md_link_id and md_client= ie_client and md_module='$this->webContainer'
							".$condition["join"]."
						  where $where_condition ie_published=1 and ie_status =1 ".$condition["where"]." and ie_list = $list and ie_client=$this->client_identifier $ord_value";
				//print "<li>list type= ".$list_type." ".__FILE__."@".__LINE__."<p>$sql</p></li>";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
	            while($r = $this->parent->db_pointer->database_fetch_array($result)){
	            	$ids[$counter] = Array(
						"identifier" 	=> $r["ie_identifier"], 
						"title" 		=> $r["md_title"], 
						"description"	=> $r["md_description"], 
						"link" 			=> "http://".$this->parent->domain.$this->parent->base.dirname($menu_url).$this->find_path_for($r["ie_identifier"])."/".$r["ie_uri"],
						"pubdate" 		=> date("r",strtotime($r["md_date_remove"]))
					);
					$counter++;
	            }
	            $this->parent->db_pointer->database_free_result($result);
				$loadall =0;
			}
//			print_r($ids);
			if ($this->module_debug){
				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>"; 
				print "<li>$list_type</li>";
				$this->exitprogram();
			}
			$out	 =	"<rss version='2.0'>";
			$out	.=	"	<channel>";
			$out	.=	"	<title>$label</title>";
			$out	.=	"	<description/>";
			$out	.=	"	<link>http://".$this->parent->domain.$this->parent->base."$menu_url</link>";
			/*************************************************************************************************************************
			* generate the RSS output for the featured lists
    		*************************************************************************************************************************/
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
			$identifier_list = "";
			//print "<li>".__FILE__."@".__LINE__."<p>$rotation</p></li>";
			//print "<li>1 ".count($ids)."  ".$auto_counter. "   ".$rotation." </li>";
			if($loadall == 1 ){
				$maxcount = count($ids);
				if ($rotation==3){
					// static feature for session
					$start  = 0;
					if ($maxcount >= $auto_counter){
						$len = $auto_counter;
					} else {
						$len = $maxcount;
					}
					$c = $start;
//					print "[$len, $maxcount]";
					for($index=0; $index < $len; $index++){
						if($c >= $maxcount){
							$c=0;
						}
						//$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$c]["identifier"].".xml";
//						print "[$index, $c, $fname]";
						//if (file_exists($fname)){
							$out .= $this->rssentry($ids[$c]);
						//}
						$c++;
					}
				} else if ($rotation==0){
					// static feature for session
					$start  = $this->check_parameters($_SESSION,"feature_".$identifier."position",-1);
					if ($maxcount >= $auto_counter){
						$len = $auto_counter;
					} else {
						$len = $maxcount;
					}
					if ($start==-1){
						$start = mt_rand(0,$maxcount);
						$_SESSION["feature_".$identifier."position"] = $start;
					}
					$c = $start;
//					print "[$len, $maxcount]";
					for($index=0; $index <= $len; $index++){
						if($c >= $maxcount){
							$c=0;
						}
						$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$c]["identifier"].".xml";
//						print "[$index, $c, $fname]";
						if (file_exists($fname)){
							$out .= $this->rssentry($ids[$c]);
						}
						$c++;
					}
				} else if ($rotation==1){
					//cycle feature for session
					$start  = $this->check_parameters($_SESSION,"feature_".$identifier."position",0);
					if ($maxcount >= $auto_counter){
						$len = $auto_counter;
					} else {
						$len = $maxcount;
					}
					if ("__NOT_FOUND__"  == $this->check_parameters($_SESSION,"feature_".$identifier."position","__NOT_FOUND__")){
						$_SESSION["feature_".$identifier."position"]=0;
					}
					$_SESSION["feature_".$identifier."position"]++;
					if ($start>=$maxcount){
						$start=0;
						$_SESSION["feature_".$identifier."position"]=0;
					}
					$c=$start;
					for($index=0; $index < $len; $index++){
						if ($c >= $maxcount){
							$c  = 0;
						}
						$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$c]["identifier"].".xml";
						if (file_exists($fname)){
							$out .= $this->rssentry($ids[$c]);
						}
						$c++;
					}
				} else {
					$maxcount = count($ids);
					$loaded=Array();
					$index=0;
					$failed=0;
					$auto_counter = ($maxcount < $auto_counter)? $maxcount : $auto_counter;
					while($index < $auto_counter){
						$random_pos = mt_rand(0,($maxcount-1));
						if(!in_array($random_pos,$loaded)){
							/**
		                    * choose random entry
		                    */
							$loaded[count($loaded)] = $random_pos;
							$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$random_pos]["identifier"].".xml";
							if (file_exists($fname)){
							$out .= $this->rssentry($ids[$random_pos]);
							}
							$failed=0; // incase there are not engough records build in a failure mech
							$index++;
						} else {
							$failed++; // incase there are not engough records build in a failure mech
						}
						if($failed>=3){
							 /*
		                     * if we falied to get a unique record  3 times move on to the next
		                     */
							 $failed=0;
							 $index++;
						}
					}
				}
			} else {
				$maxcount = count($ids);
				$loaded=Array();
				$index=0;
				$failed=0;
				$auto_counter = ($maxcount < $auto_counter)? $maxcount : $auto_counter;
				while($index < $auto_counter){
					$random_pos = mt_rand(0,$maxcount);
					if(!in_array($random_pos,$loaded)){
						/**
	                    * choose random entry
	                    */
						$loaded[count($loaded)] = $random_pos;
						$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$ids[$random_pos]["identifier"].".xml";
						if (file_exists($fname)){
							$out .= $this->rssentry($ids[$random_pos]);
						}
						$failed=0; // incase there are not engough records build in a failure mech
						$index++;
					} else {
						$failed++; // incase there are not engough records build in a failure mech
					}
					if($failed>=3){
						 /*
	                     * if we falied to get a unique record  3 times move on to the next
	                     */
						 $failed=0;
						 $index++;
					}
				}
			}
			$out	 .=	"</channel>";
			$out	 .=	"</rss>";
		}
		if($out==""){
			return "";
		}
		header("Pragma: public");
		header("Expires: 0"); // set expiration time
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		// browser must download file from server instead of cache
	
		// force download dialog
		header("Content-Type: text/xml");
		// use the Content-Disposition header to supply a recommended filename 
		header("Content-Disposition: filename=livefeed.rss;");
	
	/*
	The Content-transfer-encoding header should be binary, since the file will be read 
	directly from the disk and the raw bytes passed to the downloading computer.
	The Content-length header is useful to set for downloads. The browser will be able to 
	show a progress meter as a file downloads. The content-lenght can be determines by 
	filesize function returns the size of a file. 
	*/
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".strlen($out));
		$code = LIBERTAS_LANG_CHARSET;
//		"<?xml version=\"1.0\" encoding=\"$code\" ?".">\n";
//		print '<'.'?xml version="1.0" encoding="$code"?'.'>';
		print $out;
		$this->exitprogram();
	}
	/*************************************************************************************************************************
    * retrieve record ans RSS 
    *************************************************************************************************************************/
	function rssentry($recdata){
		$out = "<item>\n";
		$out .= "	<title>".$recdata["title"]."</title>\n";
	//	$out .= "	<title>".htmlentities($recdata["title"])."</title>\n";
		$out .= "	<link>".$recdata["link"]."</link>\n";
		$out .= "	<description>".strip_tags(html_entity_decode($recdata["description"]))."</description>\n";
		$out .= "	<pubDate>".$recdata["pubdate"]."</pubDate>\n";
		$out .= "</item>\n";
		return $out;
	}

	function find_path_for($id){
		$sql = "select * from category_to_object 
					inner join information_entry on ie_identifier = cto_object and ie_client=cto_client and cto_module='$this->webContainer'
					inner join information_list on ie_list = info_identifier and ie_client=info_client
				where cto_module='$this->webContainer' and cto_client = $this->client_identifier and cto_object= $id";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$id = $r["cto_clist"];
			$list_id = $r["ie_list"];
			$category_id = $r["info_category"];
        }
        $this->parent->db_pointer->database_free_result($result);
		if (count($this->loadedcat)==0){
//			print "<li>sa fasd".count($this->loadedcat)."</li>";
			$this->loadedcat = $this->call_command("CATEGORYADMIN_LOAD", Array("returntype"=>1, "list" => $list_id));
		}
		if ($id !=-1){
			for ($index=0, $m = count($this->loadedcat); $index< $m ; $index++){
				if ($this->loadedcat[$index]["cat_identifier"] == $id && $this->loadedcat[$index]["cat_list_id"] != $id){
					if ($id !=-1){
//						print "<li>".$this->loadedcat[$index]["cat_label"]."</li>";
						return $this->find_path($this->loadedcat[$index]["cat_parent"]) ."/". $this->make_uri($this->loadedcat[$index]["cat_label"]);
					} else {
						return "";//$this->make_uri($this->loadedcat[$index]["cat_label"]);
					}
				}
			}
		}
	}
	function find_path($id){
		if ($id !=-1){
			for ($index=0, $m = count($this->loadedcat); $index< $m ; $index++){
				if ($this->loadedcat[$index]["cat_identifier"] == $id && $this->loadedcat[$index]["cat_list_id"] != $id){
					if ($id !=-1){
						return $this->find_path($this->loadedcat[$index]["cat_parent"]) ."/". $this->make_uri($this->loadedcat[$index]["cat_label"]);
					} else {
						return "";//$this->make_uri($this->loadedcat[$index]["cat_label"]);
					}
				}
			}
		}
	}
	/*************************************************************************************************************************
    * load the stored xml for the widget
    *************************************************************************************************************************/
	function widget_atoz($parameters){
        $dirid                 = $this->check_parameters($parameters,"wo_owner_id",-1);
		$lang="en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$dirid."_a2z.xml";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"loading directory ",__LINE__,$fname));}
		if(file_exists($fname)){
			$file = file($fname);
			return join($file);
		} else {
			return "";
		}
	}
}

function compare_event_date($a, $b)
{
return strnatcmp($a['evnt_dat'], $b['evnt_dat']);
}
?>