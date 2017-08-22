<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.users.php
* @date 09 Oct 2002
*/
/**
* manage client information
*/

class client extends module{
	/**
	*  Class Variables
	*/
	var $module_name_label		= "Client Data Module";
	var $module_name			= "client";
	var $module_admin			= "1";
	var $module_grouping		= "LOCALE_MANAGEMENT_GROUP_PREFS";
	
	var $module_modify	 		= '$Date: 2005/02/22 16:51:25 $';
	var $module_version 		= '$Revision: 1.15 $';
	var $module_debug			= false;
	var $module_command			= "CLIENT_"; 		// all commands specifically for this module will start with this token
	var $module_label			= "MANAGEMENT_CLIENT";
	var $client_identifier		= -1;
	var $client_logo_setting	= 2;
	var $client_contact			= -1;
	var $client_strapline		= "";
	var $client_logo_alignment	= "LEFT";
	var $client_robot_setting	= "index,follow";
	var $client_revisit_setting	= "";
	/**
	*  Management Menu entries
	*/
	
	var $module_admin_options = array();
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access	= array(
		array("CLIENT_ALL", "COMPLETE_ACCESS","")
	);
	
	
	/**
	*  Channel options
	*/
	var $module_display_options 	= array();
	
	/**
	*  filter options
	*/
	var $display_options			= array();
	/**
	*  Class Methods
	*/
	
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*/
		if (strpos($user_command,$this->module_command)===0){
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
				return $this->get_module_version();
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."GET_IDENTIFIER"){
				return $this->get_client_identifier();
			}
			if ($user_command==$this->module_command."DISPLAY_DETAIL"){
				return $this->display_client_info();
			}
			if ($user_command==$this->module_command."GET_DOMAIN_IDENTIFIER"){
				return $this->get_domain_identifier();
			}
			if ($user_command==$this->module_command."GET_DOMAINS"){
				return $this->get_domains();
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."FORM"){
				return $this->module_form($parameter_list);
			}
			if ($user_command==$this->module_command."DOMAIN_ADD"){
				return $this->module_domain_form($parameter_list);
			}
			if ($user_command==$this->module_command."SAVE_DETAILS"){
				return $this->module_save_details($parameter_list);
			}
			if ($user_command==$this->module_command."DOMAIN_SAVE_DETAILS"){
				$this->module_domain_save_details($parameter_list);
				$user_command=$this->module_command."PARK_DOMAINS";
			}
			if ($user_command==$this->module_command."DOMAIN_REMOVE"){
				$this->module_domain_remove_details($parameter_list);
				$user_command=$this->module_command."PARK_DOMAINS";
			}
			if ($user_command==$this->module_command."PARK_DOMAINS"){
				return $this->display_domain_list($parameter_list);
			}
			if ($user_command==$this->module_command."GET_CONTACT"){
				$this->get_client_contact();
			}
			if ($user_command == $this->module_command."EDIT_FOOTER"){
				return $this->footer_form($parameter_list);
			}
			if ($user_command == $this->module_command."SAVE_FOOTER"){
				return $this->footer_save($parameter_list);
			}
			if ($user_command == $this->module_command."GET_FOOTER"){
				return $this->get_footer($parameter_list);
			}
		}else{
			return ""; // wrong command sent to system
		}
	}
	
	function initialise(){
		$this->module_admin_options			= array(
		);

		$this->editor_configurations = Array(
			"ENTRY_FOOTER_DESCRIPTION" => Array(
				"status"=>"unlocked", 
				"locked_to" => "", 
				"identifier"=>0
			)
		);
	}
	
	/**
	* function produces abstract structure of modules db structure
	*
    * used to generate the table structure required abstract function that passes an abstract
	* representation of the desired table structure to the proper database module which will
	* interpet the abstract and convert it into a valid SQL Create table structure.
	*
	* @return Array list of abstract table definitions for this module
	*/
	function create_table(){
		$tables=array();
		/**
		* Table structure for table 'user_info'
		*/
		
		$fields = array(
			array("client_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment", "key"),
			array("client_name"				,"varchar(255)"				,""	,"default ''"),
			array("client_contact"			,"unsigned integer"			,""	,"default ''", "key"),
			array("client_logo_setting"		,"unsigned small integer"	,""	,"default ''"),
			array("client_strapline"		,"varchar(255)"				,""	,"default ''"),
			array("client_logo_alignment"	,"varchar(10)"				,""	,"default 'LEFT'"),
			array("client_robot_setting"	,"varchar(50)"				,""	,"default 'index,follow'"),
			array("client_revisit_setting"	,"unsigned small integer"	,""	,"default '29'"),
			array("client_date_created"		,"datetime"					,"" ,"default ''")
		);
		$primary ="client_identifier";
		$tables[count($tables)] = array("client",$fields,$primary);
		/**
		* Table data for table 'client'
		*/
//		$this->call_command("DB_QUERY",array("INSERT INTO client (client_name) VALUES('admin');"));
		/**
		* Table structure for table 'domains'
		*/
		$fields = array(
		array("domain_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
		array("domain_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("domain_name"			,"varchar(255)"		,"NULL"		,"default ''")
		);
		
		$primary="domain_identifier";
		$tables[count($tables)] = array("domain",$fields,$primary);
		/**
		* Table structure for table 'site_footer_data'
		*/
		$fields = array(
		array("sfd_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
		array("sfd_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("sfd_text"		,"text"				,"NULL"		,"default ''")
		);
		
		$primary="sfd_identifier";
		$tables[count($tables)] = array("site_footer_data",$fields,$primary);


/*		$sql = "insert into domain (domain_client,domain_name) values (1,'".$this->parent->domain."')";
		$this->parent->db_pointer->database_query($sql);
		$sql = "insert into domain (domain_client,domain_name) values (1,'localhost')";
		$this->parent->db_pointer->database_query($sql);
		*/
		return $tables;
	}
	function get_client_identifier(){
		if ($this->client_identifier==-1){
			$NAME_OF_SERVER = $this->call_command("ENGINE_GET_DOMAIN");
			$sql = "select domain_client, client_logo_setting, client_strapline, client_logo_alignment, client_contact, client_robot_setting, client_revisit_setting from domain inner join client on client.client_identifier = domain.domain_client 
						where 
							domain_name='www.".$NAME_OF_SERVER."' or 
							domain_name='".$NAME_OF_SERVER."' or 
							domain_name='".$NAME_OF_SERVER.$this->parent->base."' or 
							domain_name='[[dev]]".$this->parent->base."'";	


			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}	
			
			$result  = $this->parent->db_pointer->database_query($sql);
			
			$counter = $this->parent->db_pointer->database_num_rows($result);
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Number of records",__LINE__,"[$counter]"));
			}	
			if ($counter>0){
				while($r = $this->parent->db_pointer->database_fetch_array($result)){
					$this->client_identifier		= $this->check_parameters($r,"domain_client");
					$this->client_logo_setting		= $this->check_parameters($r,"client_logo_setting",2);
					$this->client_strapline			= $this->check_parameters($r,"client_strapline");
					$this->client_logo_alignment	= $this->check_parameters($r,"client_logo_alignment");
					$this->client_contact			= $this->check_parameters($r,"client_contact");
 					$this->client_robot_setting		= $this->check_parameters($r,"client_robot_setting","index,follow");
 					$this->client_revisit_setting	= $this->check_parameters($r,"client_revisit_setting","29 days");				}
			}
			$this->parent->db_pointer->database_free_result($result);

			$this->parent->client_identifier	= $this->client_identifier;
			$this->parent->system_prefs			= $this->call_command("SYSPREFS_LOAD_SYSTEM_PREFERENCE");
//			print_r($this->parent->system_prefs);
			$this->client_robot_setting			= $this->check_prefs(Array("sp_meta_04_robot_restriction","default"=>"LOCALE_SP_INDEX_FOLLOW","module"=>"METADATA_", "options"=>"LOCALE_SP_INDEX_FOLLOW:LOCALE_SP_INDEX_NOFOLLOW:LOCALE_SP_NOINDEX_FOLLOW:LOCALE_SP_NOINDEX_NOFOLLOW"));
			$this->client_revisit_setting		= $this->check_prefs(Array("sp_meta_05_robot_return_time","default"=>"30","module"=>"METADATA_", "options"=>"1:7:14:21:30:60:90"));
			$this->client_contact				= $this->check_prefs(Array("sp_meta_02_display_default_title","default"=>"","module"=>"METADATA_", "options"=>"TEXT"));
			$this->client_contact_home			= $this->check_prefs(Array("sp_meta_01_display_home_title","default"=>"","module"=>"METADATA_", "options"=>"TEXT"));
			$this->client_contact_home_display	= $this->check_prefs(Array("sp_meta_03_display_home_behaviour","default"=>"LOCALE_SP_PAGE_TITLE_AND_DEFAULT_TITLE","module"=>"METADATA_", "options"=>"LOCALE_SP_PAGE_TITLE_ONLY:LOCALE_SP_PAGE_TITLE_AND_DEFAULT_TITLE:LOCALE_SP_DEFAULT_TITLE_AND_PAGE_TITLE"));
//			$this->client_pics_label			= $this->check_prefs(Array("sp_client_pics_label","default"=>"","module"=>"PICS_", "options"=>"TEXT"));

/*            	$pics_label = $r["pics_label"];
            $this->parent->db_pointer->database_free_result($result);
			require_once "external_classes/leknor.pics_metadata.php";
			$this->client_pics_label = new PICS_Label($pics_label,false,false);
			*/
		}
		return $this->client_identifier;
	}
	function get_client_contact(){
		return $this->client_contact;
	}
	function get_domain_identifier(){
		if ($this->domain_identifier==-1){
			$NAME_OF_SERVER = $this->call_command("ENGINE_GET_DOMAIN");

			$sql = "select * from domain where domain_name='".$NAME_OF_SERVER."'";	
			//print __FILE__." ".__LINE__. " ".$sql;
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}	
			$result = $this->parent->db_pointer->database_query($sql);
			$counter = $this->parent->db_pointer->database_num_rows($result);
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Number of records",__LINE__,"[$counter]"));
			}	
			if ($counter>0){
				while ($r = $this->parent->db_pointer->database_fetch_array($result)) {
					$this->domain_identifier= $r["domain_identifier"];
				}
			}
			$this->parent->db_pointer->database_free_result($result);
		}
		return $this->domain_identifier;
	}
	
	function get_domains(){
		$sql = "select * from domain where domain_client=$this->client_identifier";	
		$domains = array();
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}	
		$result = $this->parent->db_pointer->database_query($sql);
		$counter = $this->parent->db_pointer->database_num_rows($result);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Number of records",__LINE__,"[$counter]"));
		}	
		if ($counter>0){
			while ($r = $this->parent->db_pointer->database_fetch_array($result)) {
				$domains[count($domains)] = $r["domain_name"];
			}
		}
		$this->parent->db_pointer->database_free_result($result);
		return $domains;
	}
	
	function module_form($parameters){
		$out	 = "<module name=\"$this->module_name\" display=\"form\">";
		$out	.= "<form name=\"client_details\">";
		$out 	.= "<page_options><header><![CDATA[Client Details]]></header></page_options>";
		$out	.= "<input type=\"hidden\" name=\"command\" value=\"CLIENT_SAVE_DETAILS\"/>";
		$out	.= "<input type=\"hidden\" name=\"client_contact\" value=\"$this->client_contact\"/>";
		/*
		$out	.= "<input type=\"file\" name=\"client_logo_setting\" label=\"".LOCALE_LOAD_LOGO."\" value=\"saf\" preview=\"NO\">";
						<choice label=\"".LOCALE_NONE."\" name=\"choice\" value=\"0\" visibility=\"hidden\"";
		if ($this->client_logo_setting == 0){
			$out 	.= " checked=\"true\"";
		}
		$out 	.= "></choice>
						<choice label=\"".LOCALE_TEXT."\" name=\"choice\" value=\"1\" visibility=\"hidden\"";
		if ($this->client_logo_setting == 1){
			$out 	.= " checked=\"true\"";
		}
		$out 	.= "></choice>
						<choice label=\"".LOCALE_TEXT_AND_LOGO."\" name=\"choice\" value=\"4\" visibility=\"hidden\"";
		if ($this->client_logo_setting == 4){
			$out 	.= " checked=\"true\"";
		}
		$out 	.= "></choice>
						<choice label=\"".LOCALE_KEEP."\" name=\"choice\" value=\"2\" visibility=\"hidden\"";
		if ($this->client_logo_setting == 2){
			$out 	.= " checked=\"true\"";
		}
		$out 	.= "></choice>
					<choice label=\"".LOCALE_REPLACE."\" name=\"choice\" value=\"3\" visibility=\"visible\"></choice>";
		$out	.= "</input>";
*/
		$parameters["contact_identifier"]	=	$this->client_contact;

//		$out	.= "<input name=\"client_strapline\" label=\"".LOCALE_STRAPLINE."\" type=\"text\" size=\"255\"><![CDATA[$this->client_strapline]]></input>";
/*		$out	.= "<select name=\"client_logo_alignment\" label=\"".LOCALE_LOGO_ALIGNMENT."\" type=\"text\" size=\"255\">
					<option value=\"LEFT\"";
		if ($this->client_logo_alignment == "LEFT"){
			$out 	.= " selected=\"true\"";
		}
		$out 	.= ">".LOCALE_ALIGN_LEFT."</option>
					<option value=\"MIDDLE\"";
		if ($this->client_logo_alignment == "MIDDLE"){
			$out 	.= " selected=\"true\"";
		}
		$out 	.= ">".LOCALE_ALIGN_MIDDLE."</option>
					<option value=\"RIGHT\"";
		if ($this->client_logo_alignment == "RIGHT"){
			$out 	.= " selected=\"true\"";
		}
		$out 	.= ">".LOCALE_ALIGN_RIGHT."</option>
					</select>";
*/
		$out	.= "<select name=\"client_robot_setting\" label=\"".LOCALE_ROBOT_SETTING."\" type=\"text\" size=\"255\">
					<option value=\"index,follow\"";
		if ($this->client_robot_setting == "index,follow"){
			$out 	.= " selected=\"true\"";
		}
		$out 	.= ">".LOCALE_INDEX_FOLLOW."</option>
					<option value=\"index,nofollow\"";
		if ($this->client_robot_setting == "index,nofollow"){
			$out 	.= " selected=\"true\"";
		}
		$out 	.= ">".LOCALE_INDEX_NOFOLLOW."</option>
					<option value=\"noindex,follow\"";
		if ($this->client_robot_setting == "noindex,follow"){
			$out 	.= " selected=\"true\"";
		}
		$out 	.= ">".LOCALE_NOINDEX_FOLLOW."</option>
					<option value=\"noindex,nofollow\"";
		if ($this->client_robot_setting == "noindex,nofollow"){
			$out 	.= " selected=\"true\"";
		}
		$out 	.= ">".LOCALE_NOINDEX_NOFOLLOW."</option>
					</select>";
		$out	.= "<select name=\"client_revisit_setting\" label=\"".LOCALE_REVISIT_SETTING."\" type=\"text\" size=\"255\">";
		$list =array(1,7,14,21,30,60,90);
		for ($i=0;$i<count($list);$i++){
		$out 	.= "<option value=\"".$list[$i]."\"";
		if ($this->client_revisit_setting == $list[$i]){
			$out 	.= " selected=\"true\"";
		}
		$out 	.= ">".$list[$i]." day(s)</option>";
		}
		$out 	.= "</select>";
		$parameters["name"] ="LOCALE_CLIENT_DETAILS";
		$out	.= $this->call_command("CONTACT_FORM",$parameters);
		$out 	.= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
		$out	.= "</form>";
		$out	.= "</module>";
		return $out;
	}

	function module_save_details($parameters){
//		$this->client_strapline 		= trim($this->strip_tidy($this->check_parameters($parameters,"client_strapline")));
//		$this->client_logo_setting 		= $this->check_parameters($parameters,"client_logo_setting_choice",1);
//		$this->client_logo_alignment	= $this->check_parameters($parameters,"client_logo_alignment","LEFT");
		$this->client_robot_setting		= $this->check_parameters($parameters,"client_robot_setting");
		$this->client_revisit_setting	= $this->check_parameters($parameters,"client_revisit_setting");
		$error = "";
/*
		if ($this->client_logo_setting==3){
			$f = $this->check_parameters($_FILES,"client_logo_setting",-1);
			$root=$this->check_parameters($this->parent->site_directories,"ROOT");
			if ($f != -1){
				$destination_filename = str_replace("//","/",$root."/images/company_logo.gif");
				@unlink($destination_filename);
				@move_uploaded_file($_FILES["client_logo_setting"]["tmp_name"],$destination_filename);
				if (!file_exists($destination_filename)){
					$error = LOCALE_FILE_CHMOD_ERROR;
					$this->call_command("TASK_SUBMIT",Array(
					"from" => "libertas_system@".$this->parent->domain,
					"to" => "support@libertas-solutions.com",
					"subject" => "#LS0000012 - Unable to upload company_logo.gif",
					"msg" => "The system was unable to upload a file to the location /images/company_logo.gif\nfrom the following Domain: ".$this->parent->domain)
					);
				}
				@chmod($destination_filename, 0755);
			}
			$this->client_logo_setting=2;
		}
*/
		$sql= "update client set 
		client_robot_setting='$this->client_robot_setting', 
		client_revisit_setting='$this->client_revisit_setting' where client_identifier=$this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		$parameters["contact_identifier"] = $parameters["client_contact"];
		$parameters["next_command"] = "ENGINE_SPLASH";
		$this->call_command("CONTACT_SAVE",$parameters);
		$out	 = "<module name=\"$this->module_name\" display=\"confirmation\">";
		if ($error!=""){
			$out	.= "<text><![CDATA[$error]]></text>";
		} else {
			$out	.= "<text><![CDATA[LOCALE_CLIENT_CONFIRMATION]]></text>";
		}
		$out	.= "</module>";
		return $out;
	}
	
	function display_client_info(){
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$type = "ECMS";
		}else if ($this->parent->server[LICENCE_TYPE]==MECM){
			$type = "MECM";
		}else{
			$type = "SITE";
		}
		$out	 = "<module name=\"$this->module_name\" display=\"reference\">";
		$out	.= "<licence>";
		$out	.= "<product type='$type'><![CDATA[".$this->parent->product_name."]]></product>";
		$out	.= "</licence>";
		$out	.= "<client identifier=\"$this->client_identifier\" logo=\"$this->client_logo_setting\" alignment=\"$this->client_logo_alignment\" contact=\"$this->client_contact\">";
		$out	.= "<strapline><![CDATA[$this->client_strapline]]></strapline>";
		$out	.= "<robots><![CDATA[$this->client_robot_setting]]></robots>";
		$out	.= "<revisit><![CDATA[$this->client_revisit_setting]]></revisit>";
		$out	.= "<homepagetitle><![CDATA[$this->client_contact_home]]></homepagetitle>";
		$out	.= "<internalpagetitle><![CDATA[$this->client_contact]]></internalpagetitle>";
		$out	.= "<homepagedisplayformat><![CDATA[$this->client_contact_home_display]]></homepagedisplayformat>";
		$out 	.= $this->call_command("CONTACT_VIEW_USER",Array("uid_identifier"=>$this->client_contact));
		$out	.= "</client>";
		$out	.= "</module>";
		return $out;
	}
	
	function display_domain_list($parameters){
		$sql = "
				select 
					domain.*
					
				from domain 
				where 
					domain.domain_client=$this->client_identifier
				order by 
					domain.domain_name";
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}

		$variables = Array();
		$variables["HEADER"]			= "Parked Domain List";
		$variables["FILTER"]			= "";//$this->filter($parameters,"PAGE_LIST")."<menus selected=\"$menu_location\"/>";
		$variables["MENU_LINKS"]		= "?command=PAGE_LIST&page=1&search=1&menu_location=";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		
			$result = $this->parent->db_pointer->database_query($sql);
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				$number_of_records = $this->parent->db_pointer->database_num_rows($result);
				$page = $this->check_parameters($parameters,"page",1);
				$goto = ((--$page)*$this->page_size);
				if (($goto!=0)&&($number_of_records>$goto)){
					$this->call_command("DB_SEEK",array($result,$goto));
				}
				if ($goto+$this->page_size>$number_of_records){
					$finish = $number_of_records;
				}else{
					$finish = $goto+$this->page_size;
				}
				$goto++;
				$page++;
				
				$num_pages=floor($number_of_records / $this->page_size);
				$remainder = $number_of_records % $this->page_size;
				if ($remainder>0){
					$num_pages++;
				}
				
				$counter=0;
				$variables["PAGE_BUTTONS"] = Array(Array("ADD","CLIENT_DOMAIN_ADD",ADD_NEW,""));
				
				$variables["NUMBER_OF_ROWS"]	= $number_of_records;
				$variables["START"]				= $goto;
				$variables["FINISH"]			= $finish;
				$variables["CURRENT_PAGE"]		= $page;
				$variables["NUMBER_OF_PAGES"]	= $num_pages;
				
				$start_page=intval($page / $this->page_size);
				$remainder = $page % $this->page_size;
				if ($remainder>0){
					$start_page++;
				}
				
				$variables["START_PAGE"]		= $start_page;
				
				if (($start_page+$this->page_size)>$num_pages){
					$end_page	 =	$num_pages;
				}else{
					$end_page	=	$this->page_size;
				}
				$user_identifier 		= $_SESSION["SESSION_USER_IDENTIFIER"];
				$variables["END_PAGE"]			= $end_page;
				$variables["ENTRY_BUTTONS"] =Array();
				$variables["CONDITION"]= array();
				$variables["RESULT_ENTRIES"] =Array();
				
				$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"LOCALE_PAGE_FORM"));
				while (($r = $this->parent->db_pointer->database_fetch_array($result))&&($counter<$this->page_size)){
					$counter++;
					$i = count($variables["RESULT_ENTRIES"]);
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=> $r["domain_identifier"],
						"ENTRY_BUTTONS" => Array(),
						"attributes"	=> Array(
							Array(LOCALE_DOMAIN, $r["domain_name"],"TITLE","")
						)
					);
					if ($r["domain_name"] != $this->parent->domain){
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"] = Array(Array("REMOVE","CLIENT_DOMAIN_REMOVE",REMOVE_EXISTING,""));
					}

				}
		}
		$out = $this->generate_list($variables);
		return $out;
	}
	
	function module_domain_form($parameters){
		$out	 = "<module name=\"$this->module_name\" display=\"form\">";
		$out	.= "<form name=\"client_details\" label=\"".LOCALE_ADD_NEW_DOMAIN_MSG."\">";
		$out	.= "<page_options><header><![CDATA[Add A new Domain To the Parked Domain List]]></header></page_options>";
		$out	.= "<input type=\"hidden\" name=\"command\" value=\"CLIENT_DOMAIN_SAVE_DETAILS\"/>";
		$out	.= "<text><![CDATA[".LOCALE_ADD_NEW_DOMAIN_MSG."]]></text>";
		$out	.= "<input type=\"text\" label=\"".LOCALE_DOMAIN."\" name=\"domain_name\" size=\"255\" required=\"YES\"/>";
		$out 	.= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
		$out	.= "</form>";
		$out	.= "</module>";
		return $out;
	}

	function module_domain_save_details($parameters){
		$d_name = $this->check_parameters($parameters,"domain_name");
		if ($d_name!=""){
			$sql = "insert into domain (domain_name,domain_client) values ('$d_name', $this->client_identifier)";
			$result = $this->parent->db_pointer->database_query($sql);
		}
	}
	function module_domain_remove_details($parameters){
		$d_id = $this->check_parameters($parameters,"identifier");
		if ($d_id!=""){
			$sql = "delete from domain where domain_identifier=$d_id and domain_client = $this->client_identifier";
			$result = $this->parent->db_pointer->database_query($sql);
		}
	}
	function footer_form($parameters){
		$this->load_editors();
		$identifier	= $this->check_parameters($parameters, "identifier", -1);
		$footer_data="";
		if (($identifier==-1) ||($identifier=="")){
			$sql 	 = "Select * from site_footer_data where sfd_client=$this->client_identifier";
		}else{
			$sql 	 = "Select * from site_footer_data where sfd_identifier=$identifier and sfd_client=$this->client_identifier";
		}
		$result = $this->parent->db_pointer->database_query($sql);
		
		if ($result){
			$r = $this->parent->db_pointer->database_fetch_array($result);
			$footer_data = $this->add_root_dir_to_paths($this->check_parameters($r,"sfd_text"));
			$identifier  = $this->check_parameters($r,"sfd_identifier",-1);
		}
		$out	 = "<module name=\"$this->module_name\" display=\"form\">";
		$out	.= "<form name=\"client_details\">";
		$out	.= "<input type=\"hidden\" name=\"command\" value=\"CLIENT_SAVE_FOOTER\"/>";
		$out	.= "<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_FOOTER_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out 	.= "<textarea label='" . ENTRY_FOOTER_DESCRIPTION . "' size=\"40\" height=\"15\" name=\"client_footer\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$footer_data]]></textarea>";
		$out 	.= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
		$out	.= "</form>";
		$out	.= "</module>";
		return $out;
	}

	function footer_save($parameters){
		$sfd_text 		= $this->split_me($this->validate($this->tidy($this->check_parameters($parameters,"client_footer"))),"'","&#39;");
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		if ($identifier==-1){
			$sql= "insert into site_footer_data (sfd_client, sfd_text) values ('$this->client_identifier', '$sfd_text')";
		}else{
			$sql= "update site_footer_data set sfd_text='$sfd_text' where sfd_client=$this->client_identifier";
		}
		$this->parent->db_pointer->database_query($sql);
		$out	 = "<module name=\"$this->module_name\" display=\"form\">";
		$out	.= "<form name=\"client_details\">";
		$out	.= "<text><![CDATA[".LOCALE_FOOTER_CONFIRMATION."]]></text>";
		$out	.= "<input type=\"hidden\" name=\"command\" value=\"\"/>";
		$out 	.= "<input type=\"submit\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\"/>";
		$out	.= "</form>";
		$out	.= "</module>";
		$fdata = $this->get_footer(Array("regen"=>1));
		return $out;
	}
	
	function get_footer($parameters){
		$regen = $this->check_parameters($parameters,"regen",-1);
		if ($regen==1){
			$cdata = $this->check_parameters($parameters,"cdata","__NOT_FOUND__");
			if ("__NOT_FOUND__" == $cdata){
				$sql = "Select * from site_footer_data where sfd_client = $this->client_identifier";
				$result = $this->parent->db_pointer->database_query($sql);
				if ($result){
					$r 			= $this->parent->db_pointer->database_fetch_array($result);
					$cdata 		= $this->check_parameters($r,"sfd_text");
					$identifier	= $this->check_parameters($r,"sfd_identifier");
				}
			}
			$out ="<footer><![CDATA[$cdata]]></footer>";
			$DATA_FILES_DIR=$this->call_command("ENGINE_GET_PATH",Array("DATA_FILES_DIR"));
			$filename = $DATA_FILES_DIR."/footer_".$this->client_identifier."_".$identifier.".xml";
			$fp = fopen($filename, 'w');
			fwrite($fp, $out);
			fclose($fp);
			$um = umask(0);
			@chmod($filename, LS__FILE_PERMISSION);
			umask($um);
			$out		= "<module name='client' display='footer'>".$out."</module>";
		} else {
			if ($this->parent->module_type=="website"){
				$sql = "Select * from site_footer_data where sfd_client = $this->client_identifier";
				$result = $this->parent->db_pointer->database_query($sql);
				if ($result){
					$r 			= $this->parent->db_pointer->database_fetch_array($result);
					$identifier	= $this->check_parameters($r,"sfd_identifier");
				}
				$DATA_FILES_DIR=$this->call_command("ENGINE_GET_PATH",Array("DATA_FILES_DIR"));
				$filename	= $DATA_FILES_DIR."/footer_".$this->client_identifier."_".$identifier.".xml";
				if (file_exists($filename)){
					$fp 		= fopen($filename, 'r');
					$out		= "<module name='client' display='footer'>".fread($fp, filesize($filename))."</module>";
					fclose($fp);
				} else {
					$out = $this->call_command("CLIENT_GET_FOOTER", Array("regen" => 1));
				}
			} else {
				$out		= "";
			}
			return $out;
		}
	}
}
?>