<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.standard_forms.php
* @date 09 Oct 2002
*/
/**
* 
*/
class standard_forms extends module{
	/**
	*  Class Variables
	*/
	var $module_name_label			= "Standard Form Management Module";
	var $module_name				= "Standard Forms";						// name of module is used in configuration
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";		// what group does this module belong to
	var $module_label				= "MANAGEMENT_STANDARD_FORMS";			// label describing the module 
	var $module_creation			= "20/02/2003";							// date module was created
	var $module_modify	 		= '$Date: 2005/02/08 17:01:14 $';
	var $module_version 			= '$Revision: 1.21 $';									// Actual version of this module
	var $module_admin				= "1";									// does this system have an administrative section
	var $module_command				= "SFORM_";								// what does this commad start with ie TEMP_ (use caps)
	var $module_display_options		= array();								// what output channels does this module have
	var $module_admin_options 		= array();								// what options are available in the admin menu
	var $module_admin_user_access	= array(
		array("SFORM_ALL", 			"COMPLETE_ACCESS",""),
		array("SFORM_ADD", 			"ACCESS_LEVEL_ADD"),
		array("SFORM_EDIT",			"ACCESS_LEVEL_EDIT"),
		array("SFORM_REPORTS", 		"ACCESS_LEVEL_REPORTS"),
		array("SFORM_REMOVE",		"ACCESS_LEVEL_REMOVE")
	);
	
	var $add_access					= 0;
	var $edit_access				= 0;
	var $remove_access				= 0;
	var $report_access				= 0;
	var $admin_access				= 0;

//	var $available_forms 			= "";//array();
	/**
	*  Class Variables
	*/
	var $preferences = Array(
		Array('sp_wai_forms'				,"LOCALE_SP_WAI_FORMS"					,"Yes"	,"Yes:No", "SFORM_", "ALL"),
		Array("sp_sform_from_field_required","LOCALE_SFORM_FROM_FIELD_REQUIRED"		,"No"	,"Yes:No", "SFORM_", "ALL"),
		Array("sp_sform_show_country"		,"LOCALE_SFORM_SHOW_COUNTRY"			,"No"	,"Yes:No", "SFORM_", "ALL"),
		Array("sp_sform_show_tracer"		,"LOCALE_SFORM_SHOW_TRACER"				,"No"	,"Yes:No", "SFORM_", "ALL"),
		Array("sp_sform_show_source"		,"LOCALE_SFORM_SHOW_SOURCE"				,"No"	,"Yes:No", "SFORM_", "ALL"),
		Array("sp_sform_show_language"		,"LOCALE_SFORM_SHOW_LANGUAGE"			,"No"	,"Yes:No", "SFORM_", "ALL"),
		Array("sp_sform_show_referer"		,"LOCALE_SFORM_SHOW_REFERER"			,"No"	,"Yes:No", "SFORM_", "ALL")
//		Array("sp_sform_use_users_email"	,"LOCALE_SFORM_SEND_FROM_USERS_EMAIL"	,"Yes"	,"Yes:No", "SFORM_", "ALL"),
	);
	/**
	*  Class Methods
	*/
	
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*/
		if (strpos($user_command,$this->module_command)===0){
			/**
			* basic commands
			*/
			if ($user_command==$this->module_command."DEBUG_ON"){
				$this->module_debug=true;
			}
			if ($user_command==$this->module_command."DEBUG_OFF"){
				$this->module_debug=false;
			}
			if ($user_command==$this->module_command."GET_PREFS"){
				return $this->preferences;
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
			if ($user_command==$this->module_command."CREATE_NEW_CLIENT_DETAILS"){
				return $this->create_client_details($parameter_list);
			}
			if ($user_command==$this->module_command."LOAD_PREFS"){
				return $this->load_prefs();
			}
			/**
			* needed for administrative access
			*/
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/**
			* specific functions for this module
			*/
			if (($user_command==$this->module_command."DISPLAY_CONTACT_US") || ($user_command==$this->module_command."DISPLAY_CONTACT_US_CACHE")){
				return $this->display_contact_us($parameter_list);
			}
			if ($user_command==$this->module_command."CONTACT_US_SAVE"){
				return $this->send_contact_us($parameter_list);
			}
			if ($user_command==$this->module_command."INTRO"){
				return $this->splash($parameter_list);
			}
			
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				Web Site Form builder functions
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			
			if ($user_command==$this->module_command."DISPLAY_FORM"){
				return $this->display_built_form($parameter_list);
			}
			if ($user_command==$this->module_command."FORM_SUBMISSION"){
				return $this->save_form_info($parameter_list);
			}
			if ($user_command==$this->module_command."LOAD_CACHE"){
				return $this->load_cached_forms($parameter_list);
			}
			if ($user_command==$this->module_command."FORM_RESTRICTIONS"){
				return $this->form_restrictions($parameter_list);
			}
			
			if ($this->admin_access){
				/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- Admin functions
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($user_command==$this->module_command."SETTINGS"){
					return $this->display_prefs($parameter_list);
				}
				if ($user_command==$this->module_command."SETTINGS_SAVE"){
					return $this->display_prefs_save($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE"){
					$this->remove($parameter_list);
					$user_command=$this->module_command."LIST";
				}
				if ($user_command==$this->module_command."LIST"){
					return $this->display_list($parameter_list);
				}
				if ($user_command==$this->module_command."LIST_FORMS"){
					return $this->available_forms;
				}
				if (($user_command==$this->module_command."ADD") || ($user_command==$this->module_command."EDIT")){
					return $this->add($parameter_list);
				}
				if ($user_command==$this->module_command."SAVE_NEW"){
					$this->save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=SFORM_LIST"));
				}
				if ($user_command==$this->module_command."FORM_ACCESS_DEFINITION"){
					return $this->metadata_form_access($parameter_list);
				}
				if ($user_command==$this->module_command."FORM_ACCESS_SAVE"){
					$this->metadata_form_access_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."FORM_ACCESS_LIST"));
				}
				
				if ($user_command==$this->module_command."FORM_ACCESS_LIST"){
					return $this->manage_forms($parameter_list);
				}
				/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- Admin Form builder functions
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($user_command==$this->module_command."BUILDER_LIST"){
					return $this->form_builder_list($parameter_list);
				}
				if ($this->add_access || $this->edit_access){
					if ($user_command==$this->module_command."BUILDER_MODIFY"){
						return $this->display_form_builder($parameter_list);
					}
				}
				if ($this->remove_access){
					if ($user_command==$this->module_command."BUILDER_REMOVE"){
						return $this->remove_form($parameter_list);
					}
				}
				if ($user_command==$this->module_command."BUILDER_CLONE"){
					return $this->clone_form($parameter_list);
				}
				if ($user_command==$this->module_command."BUILDER_REMOVE_CONFIRM"){
					return $this->remove_form_confirm($parameter_list);
				}
				if ($user_command==$this->module_command."FORM_BUILDER_SUBMIT"){
					return $this->save_form_builder_info($parameter_list);
				}
				if ($user_command==$this->module_command."FORM_EMBED"){
					return $this->form_embed_info($parameter_list);
				}
				if ($user_command==$this->module_command."GET_FILE"){
					return $this->get_uploadedfile($parameter_list);
				}
				if ($user_command==$this->module_command."REPORT_FILTER"){
					return $this->generate_report_filter($parameter_list);
				}
				if ($user_command==$this->module_command."REPORT"){
					return $this->generate_report($parameter_list);
				}
				/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- End Form builder functions
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($user_command==$this->module_command."RESTORE"){
					return $this->restore($parameter_list);
				}
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	/**
	* call the initialisation function only when this module is created
	*/
	
	function initialise(){
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* load the locale
		*/
		$this->load_locale("sform");
		/**
		* define some access functionality
		*/
		$this->preferences[count($this->preferences)] =	Array('sp_from_email'				,"LOCALE_SFORM_DEFAULT_EMAIL"			,'info@'.$this->parseDomain($this->parent->domain)		, 'TEXT'					, "SFORM_", "ALL");

		if (($this->parent->server[LICENCE_TYPE]==MECM)||($this->parent->server[LICENCE_TYPE]==ECMS)){
			$this->module_admin_options			= array(
				array("SFORM_BUILDER_LIST", "LOCALE_FORM_BUILDER","SFORM_BUILD_FORMS", "Interactive tools/Standard Form Manager")
			);
		} else {
			$this->module_admin_options			= array();
		}
		$this->module_display_options 		= array(
			//array("SFORM_DISPLAY_CONTACT_US","LOCALE_DEFAULT_CONTACT_US")
		);
		$this->available_forms				= array();//"SFORM_DISPLAY_CONTACT_US"
		
		if (($this->parent->module_type=="admin")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
			$this->module_admin_access=1;
		}
		$this->editor_configurations = Array(
			"ENTRY_DESCRIPTION" => Array(
				"status"=>"unlocked", 
				"locked_to" => "", 
				"identifier"=>0
			)
		);
		$this->add_access				= 0;
		$this->edit_access				= 0;
		$this->remove_access			= 0;
		$this->report_access			= 0;
		$this->admin_access				= 0;
		if ($this->parent->module_type!="website"){
			$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
			$max_grps = count($grp_info);
			for($i=0;$i < $max_grps; $i++){
				$access = $grp_info[$i]["ACCESS"];
				for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
					if (
						("SFORM_ALL"==$access[$index]) ||
						("ALL"==$access[$index]) || 
						("SFORM_AUTHOR"==$access[$index])
					){
						$this->add_access=1;
					}
					if (
						("ALL"==$access[$index]) ||
						("SFORM_ALL"==$access[$index]) ||
						("SFORM_EDIT"==$access[$index])
					){
						$this->edit_access=1;
					}
					if (
						("ALL"==$access[$index]) ||
						("SFORM_ALL"==$access[$index]) ||
						("SFORM_REMOVE"==$access[$index])
					){
						$this->remove_access=1;
					}
					if (
						("ALL"==$access[$index]) ||
						("SFORM_ALL"==$access[$index]) ||
						("SFORM_REPORT"==$access[$index])
					){
						$this->report_access=1;
					}
				}
			}
			if (($this->add_access || $this->edit_access || $this->remove_access || $this->report_access ) && 
				(($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files"))){
				$this->admin_access=1;
			}
		}
		
	}
	
	function display_list($parameters){
		$menu_location 			= $this->check_parameters($parameters,"menu_location",-1);
		$join 					= "";
		$display_commands 		= "";
		$frm_list				= $this->call_command("ENGINE_RETRIEVE",Array("LIST_FORMS"));
		$max = count($frm_list);
		for($index=0;$index<$max;$index++){
			if (is_array($frm_list[$index][1])){
			    if (strlen($display_commands)>0){
					$display_commands.="', '";
				}
				if($this->check_parameters($frm_list[$index][1],0,"__NOT_FOUND__")!="__NOT_FOUND__"){
					if(!is_array($frm_list[$index][1][0])){
						$display_commands .= join("', '",$frm_list[$index][1]);
					}
				}
			}
		}
		$sql = "select 
					*
				from menu_data 
					inner join display_data on display_menu = menu_identifier
				where 
					display_command in ('$display_commands') and display_client= $this->client_identifier";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$variables = Array();
		$variables["HEADER"]			= "List of System Forms currently placed on menu locations";
		$variables["FILTER"]			= "";
//		$variables["MENU_LINKS"]		= "?command=PAGE_LIST&page=1&search=1&menu_location=";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["as"]				= "table";
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		

		$result = $this->call_command("DB_QUERY",array($sql));
		
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
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
			$variables["PAGE_BUTTONS"] = Array(Array("CANCEL","SFORM_INTRO",LOCALE_CANCEL));
			$variables["PAGE_BUTTONS"][count($variables["PAGE_BUTTONS"])] = Array("ADD","SFORM_ADD",ADD_NEW);
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
			$max = count($this->available_forms);
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$i]=Array(
					"identifier"	=> $r["display_identifier"],
					"ENTRY_BUTTONS" => Array(),
					"attributes"	=> Array(
						Array(ENTRY_MENU_LOCATION, $this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL",array($r["menu_identifier"])),"SUMMARY",""),
						Array(LOCALE_FORM, $this->get_constant($r["display_command"]),"TITLE","NO")
					)
				);
				for($index = 0; $index<$max;$index++){
					if ($this->available_forms[$index].''==$r["display_command"].''){
//						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array("LOCALE_FORM", $this->available_forms[$index][0],"YES","NO");
					}
				}
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	= Array("EDIT","SFORM_EDIT",EDIT_EXISTING,"");					
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	= Array("REMOVE","SFORM_REMOVE",REMOVE_EXISTING,"");					
			}
		}
		$out = $this->generate_list($variables);//.$this->call_command("LAYOUT_WEB_MENU");
		return $out;
	}	

	function display_contact_us($parameters){
		$subject 	= $this->check_locale_starter($this->check_parameters($parameters,"form_subject",""));
		$body 		= $this->check_locale_starter($this->check_parameters($parameters,"form_body",""));
		$check		= $this->check_parameters($parameters,"check",0);
		$show_module= $this->check_parameters($parameters,"show_module",1);
		$client_information = "";
		$parameters["name"]="LOCALE_CONTACT_US";
		$parameters["access_command"] ="SFORM_CONTACT_US_SAVE";
		$parameters["times_through"]=0;
		$out="";
		if($show_module==1){
			$out  ="<module name=\"users\" display=\"form\">";
		}
		$out .="<form name=\"SFORM_DISPLAY_CONTACT_US\" method=\"post\" label=\"".LOCALE_CONTACT_US."\">";
		$out .="<input type=\"hidden\" name=\"command\" value=\"SFORM_CONTACT_US_SAVE\"/>";
		
		if (($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==0) || ($show_module==0)){
			$client_information = $this->call_command("CONTACT_REGISTER_FORM",$parameters);
			$out .= $client_information;
		}
		if ($check){
			if (strlen($subject)==0){
				$out .="<text type=\"error\"><![CDATA[".LOCALE_SUPPLY_VALID_SUBJECT."]]></text>";
			}
		}
		$out .="<input type=\"text\" label=\"".LOCALE_SUBJECT."\" size=\"255\" name=\"form_subject\" required=\"YES\"><![CDATA[$subject]]></input>";
		if ($check){
			if (strlen($body)==0){
				$out .="<text type=\"error\"><![CDATA[".LOCALE_SUPPLY_VALID_MSG."]]></text>";
			}
		}
		$out .="<textarea label=\"".LOCALE_MSG."\" size=\"40\" height=\"5\" name=\"form_body\" type=\"PLAIN-TEXT\" required=\"YES\"><![CDATA[$body]]></textarea>";
		$out .="<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
		$out .="</form>";
		if ($show_module==1){
			$out .="</module>";
		}
		return $out;
	}
	function send_contact_us($parameters){
		$sent = true;
		$subject 	= $this->check_parameters($parameters,"form_subject","");
		$body 		= $this->check_parameters($parameters,"form_body","");
		$sql ="select * from available_forms where frm_client=$this->client_identifier and frm_label='LOCALE_CONTACT_US';";
		$result = $this->call_command("DB_QUERY",array($sql));
		$email_from  = "";
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$email_from = $r["frm_email_address"];
		}
		if (!($this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0)>0)){
			$parameters["name"]="LOCALE_CONTACT_US";
			$parameters["access_command"] ="SFORM_CONTACT_US_SAVE";
			$parameters["hide"]=1;
			$parameters["plain_text"]=1;
			$client_information = $this->call_command("CONTACT_REGISTER_FORM",$parameters);
		}else{
			$parameters["plain_text"]=1;
			$parameters["identifier"]=$_SESSION["SESSION_USER_IDENTIFIER"];
			$client_information = "";
		}
		if ((strlen($subject)>0) && (strlen($body)>0) && (strlen($client_information)==0)){
			$to 		= $email_from; // this is a contact us form so send to site admin not web user
			$details 	= $this->call_command("CONTACT_VIEW_USER",$parameters);
			$sent 		= $this->call_command("EMAIL_QUICK_SEND",Array(
				"from"		=> "$email_from",
				"subject"	=> "$subject",
				"body"		=> "The contact form on http://".$this->parent->domain." as sent you the following information\n-=- Msg -=-=-=-=-=-=-=-=-=-=-=-=-=-=-\n\n$body\n\n-=- From =-=-=-=-=-=-=-=-=-=-=-=-=-=-\n$details\n\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\nPowered by libertas-Solutions.\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-",
				"to"		=> $to
				)
			);
			$out  ="<module name=\"users\" display=\"confirmation\">";
			if (!$sent){
				$out .="<text><![CDATA[".LOCALE_EMIAL_PROBLEM."]]></text>";
			}
			if ($sent){
				$out .="<text><![CDATA[".LOCALE_SENT_MSG."]]></text>";
			}
			$out .="</module>";
		} else {
			$parameters["hide"]=0;
			$parameters["check"]=1;
			$out = $this->display_contact_us($parameters);
		}
		return $out;
	}
	
	function remove($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$sql = "delete from display_data where display_identifier = $identifier and display_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
	}
	function add($parameters){
		$id = $this->check_parameters($parameters,"identifier",-1);
		$menu_id = -1;
		$menu_frm="";
		if ($id!=-1){
			$sql = "select * from display_data where display_client=$this->client_identifier and display_identifier = $id";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$menu_id = $r["display_menu"];
				$menu_frm = $r["display_command"];
			}
		}
		$menu 		= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_id));
		$frm_list	= $this->call_command("ENGINE_RETRIEVE",Array("LIST_FORMS"));
		$max = count($frm_list);
		$display_commands="";
		for($index=0;$index<$max;$index++){
			if (is_array($frm_list[$index][1])){
				$m = count($frm_list[$index][1]);
				for ($i=0;$i<$m;$i++){
					$display_commands .= "<option value=\"".$frm_list[$index][1][$i]."\"";
					if ($frm_list[$index][1][$i] == $menu_frm){
						$display_commands .= " selected=\"true\"";
					}
					$display_commands .= ">".$this->get_constant($frm_list[$index][1][$i])."</option>";
				}
			}
		}
		$out  ="<module name=\"users\" display=\"form\">";
		$out .= "<page_options>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","SFORM_LIST",LOCALE_CANCEL));
		$out .= "</page_options>";			
		$out .="<form name=\"contact_us\" method=\"post\" label=\"Add a form\">";
		$out .="<input type=\"hidden\" name=\"identifier\" value=\"$id\"/>";
		$out .="<input type=\"hidden\" name=\"command\" value=\"SFORM_SAVE_NEW\"/>";
		$out .="<text><![CDATA[".LOCALE_ADD_A_FOR_TO_A_LOCATION_MSG."]]></text>";
		$out .="<select label=\"".LOCALE_FORM."\" name=\"form_code\">$display_commands</select>";
		$out .="<select label=\"".ENTRY_MENU_LOCATION."\" name=\"menu_location\">$menu</select>";
		$out .="<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
		$out .="</form>";
		$out .="</module>";
		return $out;
	}
	function save($parameters){
		$form_code = $this->check_parameters($parameters,"form_code");
		$menu_location = $this->check_parameters($parameters,"menu_location");
		$sql = "insert into display_data (display_client,display_menu,display_command) values ($this->client_identifier,$menu_location,'$form_code')";
		$result = $this->call_command("DB_QUERY",array($sql));
		$this->call_command("LAYOUT_CACHE_MENU_STRUCTURE");
	}
	function splash($parameters){
		$out  ="<module name=\"users\" display=\"table\"><table>";
		$out .="<row><![CDATA[".LOCALE_STANDARD_FORM_SPLASH."]]></row>";
		$out .="</table></module>";
		return $out;
	}
	
	function manage_forms($parameters){
		$menu_location 			= $this->check_parameters($parameters,"menu_location",-1);
		$join 					= "";
		$display_commands 		= "";
		$frm_list				= $this->call_command("ENGINE_RETRIEVE",Array("LIST_FORMS"));
		$max = count($frm_list);
		for($index=0;$index<$max;$index++){
			if (is_array($frm_list[$index][1])){
			    if (strlen($display_commands)>0){
					$display_commands.="', '";
				}
				if($this->check_parameters($frm_list[$index][1],0,"__NOT_FOUND__")!="__NOT_FOUND__"){
					if(!is_array($frm_list[$index][1][0])){
						$display_commands .= join("', '",$frm_list[$index][1]);
					}
				}
			}
		}

		$sql = "select 
					*
				from available_forms
				where 
					frm_client = $this->client_identifier";
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$variables = Array();
		$variables["FILTER"]			= "";
//		$variables["MENU_LINKS"]		= "?command=PAGE_LIST&page=1&search=1&menu_location=";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["as"]				= "table";
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		

		$result = $this->call_command("DB_QUERY",array($sql));
		
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
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
			$variables["PAGE_BUTTONS"] = Array(Array("CANCEL","SFORM_INTRO",LOCALE_CANCEL));
			
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
			$max = count($this->available_forms);
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$i]=Array(
					"identifier"	=> $r["frm_identifier"],
					"ENTRY_BUTTONS" => Array(),
					"attributes"	=> Array(
						Array(LOCALE_FORM, $this->get_constant($r["frm_label"]),"TITLE","NO")
					)
				);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT","SFORM_FORM_ACCESS_DEFINITION",LOCALE_MODIFY);	
			}
		}
		$out = $this->generate_list($variables);//.$this->call_command("LAYOUT_WEB_MENU");
		return $out;
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
		$tables = array();
		/**
		* Table structure for table 'available_fields'
		*/
		
		$fields = array(
			array("af_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("af_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("af_form"			,"unsigned integer"			,"NOT NULL"	,"default ''"),
			array("af_available"	,"unsigned small integer"	,"NOT NULL"	,"default ''"),
			array("af_name"			,"VARCHAR(255)"				,"NOT NULL"	,"default ''"),
			array("af_locale"		,"VARCHAR(255)"				,"NOT NULL"	,"default ''"),
			array("af_required"		,"unsigned small integer"	,"NOT NULL"	,"default ''")
		);

		$primary ="af_identifier";
		$tables[count($tables)] = array("available_fields", $fields, $primary);
		
		/**
		* Table structure for table 'available_forms'
		*/
		
		$fields = array(
			array("frm_identifier"		,"unsigned integer"		,"NOT NULL"	,"auto_increment"),
			array("frm_client"			,"unsigned integer"		,"NOT NULL"	,"default '0'"),
			array("frm_label"			,"VARCHAR(255)"			,"NOT NULL"	,"default ''"),
			array("frm_date_created"	,"datetime"				,"NOT NULL"	,"default ''"),
			array("frm_email_address"	,"VARCHAR(255)"			,"NOT NULL"	,"default ''")
		);

		$primary ="frm_identifier";
		$tables[count($tables)] = array("available_forms", $fields, $primary);
		/**
		* F O R M   B U I L D E R   T A B L E S
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* form_builder_data            - holds the initial record inserted by a user
		* form_builder_fields          - holds the field information for the form that was submitted
		* form_builder_group_fields    - holds the fields that are groupable
		* form_builder_emails          - holds the subject line(s) and the email address(s) to use.
		* form_builder_locations       - holds the locations that the form will be published to.
		* form_builder_required_fields - holds the fields that are required to be filled in non blank.
		* form_builder_structure       - holds the editable structure of the form. (main function)
		*/
		
		/**
		* form_builder_structure
		*/
		$fields = array(
			array("fb_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fb_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fb_xml_structure"		,"text"						,"NOT NULL"	,"default ''"),
			array("fb_label"				,"VARCHAR(255)"				,"NOT NULL"	,"default ''"),
			array("fb_action"				,"unsigned small integer"	,"NOT NULL"	,"default ''"),
			array("fb_created"				,"datetime"					,"NOT NULL"	,"default ''"),
			array("fb_creator"				,"unsigned integer"			,"NOT NULL"	,"default ''"),
			array("fb_url"					,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fb_method"				,"varchar(5)"				,"NOT NULL"	,"default 'post'"),
			array("fb_confirm_screen"		,"text"						,"NOT NULL"	,"default ''"),
			array("fb_emailscreen"			,"text"						,"NOT NULL"	,"default ''"),
			array("fb_submit_button_label"	,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fb_default_email"		,"varchar(255)"				,"NULL"	,"default ''"),
			array("fb_default_subject"		,"varchar(255)"				,"NULL"	,"default ''"),
			array("fb_status"				,"unsigned small integer"	,"NOT NULL"	,"default '4'")
		);

		$primary ="fb_identifier";
		$tables[count($tables)] = array("form_builder_structure", $fields, $primary);

		/**
		* form_builder_required_fields
		*/
		$fields = array(
			array("fbr_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbr_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbr_form"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbr_index"			,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);

		$primary ="fbr_identifier";
		$tables[count($tables)] = array("form_builder_required_fields", $fields, $primary);

		/**
		* form_builder_group_fields
		*/
		$fields = array(
			array("fbg_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbg_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbg_form"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbg_index"			,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);

		$primary ="fbg_identifier";
		$tables[count($tables)] = array("form_builder_group_fields", $fields, $primary);
		/**
		* form_builder_locations
		*/
		$fields = array(
			array("fbl_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbl_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbl_form"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbl_location"		,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);

		$primary ="fbl_identifier";
		$tables[count($tables)] = array("form_builder_locations", $fields, $primary);
		/**
		* form_builder_fields
		*/
		$fields = array(
			array("fbf_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbf_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbf_field"			,"varchar(255)"				,"NOT NULL"	,"default '0'"),
			array("fbf_link"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbf_value"			,"blob"						,"NOT NULL"	,"default ''"),
			array("fbf_value_group"		,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbf_mime_type"		,"varchar(255)"				,"NOT NULL"	,"default ''")
		);

		$primary ="fbf_identifier";
		$tables[count($tables)] = array("form_builder_fields", $fields, $primary);
		/**
		* form_builder_emails
		*/
		$fields = array(
			array("fbe_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbe_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbe_form"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbe_default"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("fbe_email"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbe_subject"			,"varchar(255)"				,"NOT NULL"	,"default ''")
		);

		$primary ="fbe_identifier";
		$tables[count($tables)] = array("form_builder_emails", $fields, $primary);
		/**
		* form_builder_emails
		*/
		$fields = array(
			array("fbd_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbd_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbd_form"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbd_filled_in_by"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbd_submitted"		,"datetime"					,"NOT NULL"	,"default ''")
		);

		$primary ="fbd_identifier";
		$tables[count($tables)] = array("form_builder_data", $fields, $primary);
		/**
		* form_builder_field_types
		*/
		$fields = array(
			array("fbft_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbft_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbft_form"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbft_field"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbft_type"			,"varchar(255)"				,"NOT NULL"	,"default ''")
		);

		$primary ="fbft_identifier";
		$tables[count($tables)] = array("form_builder_field_types", $fields, $primary);

		return $tables;
	}

	function create_client_details($parameters){
		$client_identifier = $this->check_parameters($parameters,"client_identifier",-1);
		$email_address = $this->check_parameters($parameters,"email_address");
		/**
		* insert available_fields data
		*/
		$data = array();
		$counter=0;
		$length=count($this->parent->modules);
		foreach($this->parent->modules as $index => $moduleEntry){
			if($this->parent->modules[$index]["cc_code"]==1){
			//print "<li>looking for :".$this->parent->modules[$index]["tag"]."LIST_FORM_FIELD_ACCESS";
				$tables = $this->call_command($this->parent->modules[$index]["tag"]."LIST_FORM_FIELD_ACCESS");
				if (is_array($tables)){
					$m = count($tables);
					for ($i=0;$i<$m;$i++){
						if (!empty($tables[$i])){
							$data[$counter++] = $tables[$i];
						}
					}
				}
			}
		}
		$length_array = count($data);
		for ($index=0; $length_array > $index;$index++){
			$sql = "INSERT INTO available_forms (frm_client, frm_label, frm_email_address) VALUES ($client_identifier,'".$data[$index][0]."','$email_address');";
//			print "<li>$sql</li>";
			$this->call_command("DB_QUERY",array($sql));
			$result = $this->call_command("DB_QUERY",array("select * from available_forms  where frm_client=$client_identifier and frm_label='".$data[$index][0]."';"));
			$id = "";
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$id = $r["frm_identifier"];
			}
			for ($i=0,$len=count($data[$index][1]);$i<$len;$i++){
				eval ($data[$index][1][$i]);
//				print "<li>$sql</li>";
				$this->call_command("DB_QUERY",array($sql));
			}
		}
		
	}

	
	function metadata_form_access($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,""));
		}
		$id = $this->check_parameters($parameters,"identifier",-1);
		
		if ($id==-1){
			$name = $this->check_parameters($parameters,"name","");
			$sql = "select * from available_fields inner join available_forms on frm_identifier = af_form where af_client=$this->client_identifier and (frm_label='$name') order by af_identifier";
		}else{
			$sql = "select * from available_fields inner join available_forms on frm_identifier = af_form where af_client=$this->client_identifier and frm_identifier=$id order by af_identifier";
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$entry=Array();
		$email="";
		if ($result){
			while($r= $this->call_command("DB_FETCH_ARRAY",array($result))){
				$email 		= $r["frm_email_address"];
				$frm_id 	= $r["af_form"];
				$frm_name 	= $r["frm_label"];
				$entry[count($entry)] = Array(
					"value" => $r["af_name"],
					"locale" => $r["af_locale"],
					"available" => $r["af_available"],
					"required" => $r["af_required"]
				);
			}
		}
		$max = count($entry);
		$frm="";
		for ($index=0;$index<$max;$index++){
			$frm .="		<field_form value=\"".$entry[$index]["value"]."\" label=\"".$this->get_constant($entry[$index]["locale"])."\" requires=\"".$entry[$index]["required"]."\" available=\"".$entry[$index]["available"]."\"/>";
		}
		$out	 = "<module name=\"$this->module_name\" display=\"form\">\n";
		$out 	.= "<page_options>";
		$out 	.= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","SFORM_FORM_ACCESS_LIST",LOCALE_CANCEL));
		$out 	.= "</page_options>";			
		$out	.= "<form name=\"sys_prefs\" label=\"".$this->get_constant($frm_name)."\" method=\"POST\" width=\"100%\">\n";
		$out	.= "<input name=\"command\" type=\"hidden\" value=\"".$this->module_command."FORM_ACCESS_SAVE\"/>\n";
		$out	.= "<input name=\"frm_id\" type=\"hidden\" value=\"$frm_id\"/>\n";
		$out	.= "<input name=\"email\" size=\"255\" type=\"text\" label=\"".LOCALE_EMAIL_SENT_TO."\"><![CDATA[$email]]></input>\n";
		$out	.= "<text><![CDATA[".LOCALE_FOMR_STRUCTURE_MANAGMENT."]]></text>\n";
		$out	.= $frm;
		$out	.= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>\n";
		$out	.= "</form>\n";
		$out	.= "</module>";

		return $out;
	}
	function metadata_form_access_save($parameters){
		$email			= $this->check_parameters($parameters,"email");
		$max_avail 		= count($this->check_parameters($parameters,"available",null));
		$max_required	= count($this->check_parameters($parameters,"required",null));
		$frm_id = $this->check_parameters($parameters,"frm_id");
		$sql[0] = "update available_forms set frm_email_address='$email' where frm_client=$this->client_identifier and frm_identifier = $frm_id";
		$sql[1] = "update available_fields set af_available='0', af_required='0' where af_client=$this->client_identifier and af_form = $frm_id";
		for($index=0;$index<$max_avail;$index++){
			$found=0;
			for($i=0;$i<$max_required;$i++){
				if ($parameters["required"][$i]==$parameters["available"][$index]){
					$found=1;
				}
			}
			$sql[count($sql)] =  "update available_fields set af_available='1', af_required='".$found."' where af_name ='".$parameters["available"][$index]."' and af_client=$this->client_identifier and af_form = $frm_id";
		}
		$max = count($sql);
		for($index=0;$index<$max;$index++){
			$this->call_command("DB_QUERY",Array($sql[$index]));
		}
	}

	function form_restrictions($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"form_restrictions",__LINE__,""));
		}
		$id = $this->check_parameters($parameters,"identifier",-1);
		
		if ($id==-1){
			$name = $this->check_parameters($parameters,"name","");
			$sql = "select * from available_fields inner join available_forms on frm_identifier = af_form where af_client=$this->client_identifier and (frm_label='$name') and af_available=1";
		}else{
			$sql = "select * from available_fields inner join available_forms on frm_identifier = af_form where af_client=$this->client_identifier and frm_identifier=$id and af_available=1";
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$entry=Array();
		if ($result){
		$i=0;
			while($r= $this->call_command("DB_FETCH_ARRAY",array($result))){
				if ($i==0){
					$entry[-1]["email"] = $r["frm_email_address"];
				}
				$entry[$r["af_name"]] = Array(
					"value" => $r["af_name"],
					"locale" => $r["af_locale"],
					"available" => $r["af_available"],
					"required" => $r["af_required"]
				);
				$i++;
			}
		}
		return $entry;
	}

	function form_builder_list($parameters){
		$where = "";
		$join="";
		$order_by="";
		$status =array();
		
		$order_by .= "order by fb_identifier desc";
		$lang_of_choice = "en";
		if (empty($filter_translation)){
			$translation = $lang_of_choice;
		} else {
			$translation = $filter_translation;
		}
		
		$sql = "select * from form_builder_structure where fb_client = $this->client_identifier order by fb_label asc";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$variables = Array();
		$variables["FILTER"]			= "";
//		$variables["MENU_LINKS"]		= "?command=PAGE_LIST&page=1&search=1&menu_location=";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["as"]				= "table";
		$this->page_size=50;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		
		if ($this->module_admin_access==1){
			$result = $this->call_command("DB_QUERY",array($sql));
			
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
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
				
				if ($number_of_records>0){
					$num_pages=floor($number_of_records / $this->page_size);
					$remainder = $number_of_records % $this->page_size;
					if ($remainder>0){
						$num_pages++;
					}
				}else{
					$num_pages=0;
					$remainder=0;
				}
				$counter=0;
				if($this->parent->module_type=="admin"){
					if($this->add_access){
						$variables["PAGE_BUTTONS"] = Array(Array("ADD","SFORM_BUILDER_MODIFY",ADD_NEW));
					}
				}
				
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
				$variables["END_PAGE"]			= $end_page;
				$variables["ENTRY_BUTTONS"] =Array();
				$variables["CONDITION"]= array();
				$variables["RESULT_ENTRIES"] =Array();
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
					$counter++;
					$identifier = $r["fb_identifier"];
					$sql	 = "select * from form_builder_emails where fbe_form =$identifier and fbe_client = $this->client_identifier";
					//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
					$result_emails = $this->call_command("DB_QUERY",array($sql));
					$emails = "";
					while($rec= $this->call_command("DB_FETCH_ARRAY",array($result_emails))){
						$emails .= "<li>".$rec["fbe_subject"]." => ".$rec["fbe_email"]."</li>";
					}
					$this->call_command("DB_FREE",array($result_emails));
					/*
					if($emails==""){
						$emails="[[nbsp]]";
					} else {
						$emails="<ul>$emails</ul>";
					}
					*/
				
					if($emails=="" || ($rec["fbe_subject"]=="" && $rec["fbe_email"]=="")){
						$emails = "<li>".$r["fb_default_subject"]." => ".$r["fb_default_email"]."</li>";
					} else {
						$emails="<ul>$emails</ul>";
					}

					$i = count($variables["RESULT_ENTRIES"]);
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=> $r["fb_identifier"],
						"ENTRY_BUTTONS" => Array(),
						"attributes"	=> Array(
								Array(ENTRY_TITLE, $r["fb_label"],"TITLE","NO"),
								Array(LOCALE_DEFAULT_EMAIL, $r["fb_default_email"],"SUMMARY"),
								Array(LOCALE_SUBJECT_EMAIL, $emails)
						)
					);
					if($this->edit_access){
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])] = Array("EDIT", "SFORM_BUILDER_MODIFY", EDIT_EXISTING);
					}
					if($this->remove_access){
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])] = Array("REMOVE", "SFORM_BUILDER_REMOVE", REMOVE_EXISTING);
					}
					if($this->add_access){
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])] = Array("COPY", "SFORM_BUILDER_CLONE", LOCALE_NEW_COPY);
					}
					if (($r["fb_action"]==1 || $r["fb_action"]==2) && $this->report_access){
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]=Array("REPORT", "SFORM_REPORT_FILTER", LOCALE_REPORT);
					}
				}
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}
	function display_built_form($parameters){
		$out="";
		$menu_id 	= $this->call_command("LAYOUT_GET_LOCATION_ID");
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$display 	= $this->check_parameters($parameters,"display","form");
		$values		= $this->check_parameters($parameters,"values");
		$emails = Array();
		$form_str ="";
		if ($identifier!=-1){
			$sql = "select * from form_builder_structure where form_builder_structure.fb_identifier =$identifier and fb_client = $this->client_identifier";
		} else {
			$sql = "select * from form_builder_structure inner join form_builder_locations on fbl_form = fb_identifier where form_builder_locations.fbl_location =$menu_id and fb_client = $this->client_identifier";
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$entry=Array();
		$action = -1;
		if ($result){
			while($r= $this->call_command("DB_FETCH_ARRAY",array($result))){
				$form_str 	= $r["fb_xml_structure"];
				$label 		= $r["fb_label"];
				$action 	= $r["fb_action"];
				$identifier	= $r["fb_identifier"];
				$fb_submit_button_label=$this->check_parameters($r,"fb_submit_button_label",SAVE_DATA);
				$url 		= $this->check_parameters($r,"fb_url");
				$method 		= $this->check_parameters($r,"fb_method","post");
				if ($action ==0 || $action==1){
					$sql	 = "select * from form_builder_emails where fbe_form =$identifier and fbe_client = $this->client_identifier";
					$result_emails = $this->call_command("DB_QUERY",array($sql));
					$entry=Array();
					if ($result_emails){
						while($r= $this->call_command("DB_FETCH_ARRAY",array($result_emails))){
							$emails[count($emails)] = Array($r["fbe_subject"],$r["fbe_email"],$r["fbe_default"]);
						}
					}
				}
				if($display!="none"){
					$out  =	"<module name=\"".$this->module_name."\" display=\"".$display."\">";
				}
				if ($url!="" && $action != 4){
					$dest = "action='$url'";
				} else {
					$dest="";
				}
				$out .=	"<form name=\"libertas_form_".$identifier."\" method=\"$method\" label=\"".$label."\" $dest>";
				$out .=	"<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
				$out .=	"<input type=\"hidden\" name=\"command\" value=\"SFORM_FORM_SUBMISSION\"/>";
				if (strlen($values)>0){
					$out .=	"<text><![CDATA[<span class='required'>".LOCALE_FORM_REQUIRED_WARNING."</span>]]></text>";
				}
				if (count($emails)==0){
				} else {
					$out .= "<emails>";
					for ($index=0, $max = count($emails);$index<$max;$index++){
						$out .="<option value='".str_replace(Array("&"), Array("&amp;"), $emails[$index][0])."'";
						if ($emails[$index][2]==1){
							$out .=" selected='true'";
						}
						$out .="><![CDATA[".$emails[$index][0]."]]></option>";
					}
					$out .= "</emails>";
				}
				$out .=	$form_str;
				
				$out .=	"<input iconify=\"SAVE\" type=\"submit\" value=\"".$fb_submit_button_label."\"/>";
				$out .= $values;
				$out .=	"</form>";
				if($display!="none"){
					$out .=	"</module>";
				}
			}

			return $out;
		}
	}

	function save_form_builder_info($parameters){
		$identifier 			= $this->check_parameters($parameters,"identifier");
		$submission				= $this->check_parameters($parameters,"submission");
		$destination_url		= $this->check_parameters($parameters,"destination_url");
		$method					= $this->check_parameters($parameters,"method");
		$form_submit_button_label= $this->check_parameters($parameters,"form_submit_button_label");
		$confirm_screen			= $this->split_me($this->tidy($this->validate($this->check_parameters($parameters,"confirm_screen"))),"'","&#39;");
		$emailscreen			= $this->text_tidy($this->check_parameters($parameters,"emailscreen"));
		$xml_representation_data= $this->tidy($this->validate($this->check_parameters($parameters,"xml_representation")));
		$xml_representation_data= substr($xml_representation_data, 3, strrpos($xml_representation_data,"<")-2);
		$xml_representation		= $this->processToXML($xml_representation_data);
		$field_types			= $this->processFieldTypes($xml_representation_data);
		$xml_representation 	= str_replace("'", "''", $xml_representation);
		$email_list				= split("@@", $this->check_parameters($parameters,"email_list"));
		$label					= $this->check_parameters($parameters,"form_header_label");
		$group_fields			= split(",", $this->check_parameters($parameters,"group_fields"));
		$required_fields		= split(",", $this->check_parameters($parameters,"required_fields"));
		$emails 				= Array();
		$fb_default_email		= $this->check_parameters($parameters,"fb_default_email");
		$fb_default_subject		= $this->check_parameters($parameters,"fb_default_subject");
		$fb_status				= $this->check_parameters($parameters,"fb_status","1");

		for($index=0,$max = count($email_list);$index<$max;$index++){
			$emails[$index] 	= split(":",$email_list[$index]);
		}
		
		/**
		* Add a new form to the system
		*/
		if ($identifier==-1){
			$now = $this->libertasGetDate("Y/m/d H:i:s");
			$sql = "insert into form_builder_structure (
						fb_xml_structure,
						fb_label,
						fb_action,
						fb_client,
						fb_created,
						fb_creator,
						fb_url,
						fb_method,
						fb_confirm_screen,
						fb_emailscreen,
						fb_submit_button_label,
						fb_default_email,
						fb_default_subject,
						fb_status
					) values (
						'$xml_representation', 
						'$label', 
						'$submission', 
						".$this->client_identifier.",
						'$now',
						'".$_SESSION["SESSION_USER_IDENTIFIER"]."',
						'$destination_url',
						'$method',
						'$confirm_screen',
						'$emailscreen',
						'$form_submit_button_label',
						'$fb_default_email',
						'$fb_default_subject',
						'$fb_status'
					)";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "select * from form_builder_structure where fb_client=".$this->client_identifier." and fb_created='$now' and fb_creator = '".$_SESSION["SESSION_USER_IDENTIFIER"]."'";
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$identifier = $r["fb_identifier"];
			}
			if ($submission==1 || $submission=0){
				for($index=0,$max = count($emails);$index<$max;$index++){
					$default = ($emails[$index][2]? 1 : 0);
					$sql = "insert into form_builder_emails (fbe_client, fbe_form, fbe_subject, fbe_email, fbe_default) values (".$this->client_identifier.", $identifier, '".$emails[$index][0]."', '".$emails[$index][1]."', ".$default.")";
					$this->call_command("DB_QUERY",Array($sql));
				}
			}
			foreach ($field_types as $key=>$val){
				$sql = "insert into form_builder_field_types (fbft_client, fbft_form, fbft_field, fbft_type) values (".$this->client_identifier.", $identifier, '$key', '$val')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			if (count($group_fields)>0){
				$max = count($group_fields);
				for($index=0;$index<$max;$index++){
					$sql = "insert into form_builder_group_fields (fbg_client, fbg_form, fbg_index) values ($this->client_identifier, $identifier, ".$group_fields[$index].")";
					$this->call_command("DB_QUERY",Array($sql));
				}
			}
			if (count($required_fields)>0){
				$max = count($required_fields);
				for($index=0;$index<$max;$index++){
					$sql = "insert into form_builder_required_fields (fbr_client, fbr_form, fbr_index) values ($this->client_identifier, $identifier, ".substr($required_fields[$index],5).")";
					$this->call_command("DB_QUERY",Array($sql));
				}
			}
		} else {
			$sql = "delete from form_builder_emails where fbe_client = $this->client_identifier and fbe_form = $identifier";
			$this->call_command("DB_QUERY",Array($sql));
			for($index=0,$max = count($emails);$index<$max;$index++){
				if (count($emails[$index])==3){
					$default = ($emails[$index][2].""=="true" ? 1 :0 );
					$sql = "insert into form_builder_emails (fbe_client, fbe_form, fbe_subject, fbe_email, fbe_default) values (".$this->client_identifier.",$identifier, '".$emails[$index][0]."', '".$emails[$index][1]."', ".$default.")";
					$this->call_command("DB_QUERY",Array($sql));
				}
			}
			$sql = "update form_builder_structure set fb_default_email='$fb_default_email', fb_default_subject='$fb_default_subject', fb_status='$fb_status', fb_submit_button_label='$form_submit_button_label', fb_method='$method', fb_emailscreen='$emailscreen', fb_confirm_screen='$confirm_screen', fb_url='$destination_url', fb_xml_structure='$xml_representation', fb_label='$label', fb_action='$submission' where fb_client=".$this->client_identifier." and fb_identifier=$identifier";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "delete from form_builder_group_fields where fbg_client = $this->client_identifier and fbg_form = $identifier";
			$this->call_command("DB_QUERY",Array($sql));
			if (count($group_fields)>0){
				$max = count($group_fields);
				for($index=0;$index<$max;$index++){
					$sql = "insert into form_builder_group_fields (fbg_client, fbg_form, fbg_index) values ($this->client_identifier, $identifier, ".$group_fields[$index].")";
					$this->call_command("DB_QUERY",Array($sql));
				}
			}
			$sql = "delete from form_builder_required_fields where fbr_client = $this->client_identifier and fbr_form = $identifier";
			$this->call_command("DB_QUERY",Array($sql));
			if (count($required_fields)>0){
				$max = count($required_fields);
				for($index=0;$index<$max;$index++){
					$sql = "insert into form_builder_required_fields (fbr_client, fbr_form, fbr_index) values ($this->client_identifier, $identifier, ".substr($required_fields[$index],5).")";
					$this->call_command("DB_QUERY",Array($sql));
				}
			}
			$sql = "delete from form_builder_field_types where fbft_client = ".$this->client_identifier." and fbft_form = $identifier";
			$this->call_command("DB_QUERY",Array($sql));
			foreach ($field_types as $key=>$val){
				$sql = "insert into form_builder_field_types (fbft_client, fbft_form, fbft_field, fbft_type) values (".$this->client_identifier.", $identifier, '$key', '$val')";
				$this->call_command("DB_QUERY",Array($sql));
			}
		}


		$this->cache_form($identifier);
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=SFORM_BUILDER_LIST"));
		exit();
	}
	
	function double_quote($str){
		$str = join("''",split("'",$str));
		return $str;
	}
	
	function form_embed_info($parameters){
		$sql = "select * from form_builder_structure where fb_client = $this->client_identifier";
		$frm_list = $this->call_command("ENGINE_RETRIEVE",Array("LIST_FORMS"));
//		print_r($frm_list);
		$forms ="";
		$label="";
		$l = count( $frm_list);
		for ($index=0;$index<$l;$index++){
			$find = $this->check_parameters($frm_list[$index],1,"__NOT_FOUND__");
			if ($find.""=="Array"){
				for ($z=0,$m=count($frm_list[$index][1]);$z<$m;$z++){
					$list = $frm_list[$index][1][$z];
					if (is_array($list)){
						$forms .= "<form_builder identifier='".$list["id"]."'>\n";
							$forms .= "	<label><![CDATA[".$list["label"]."]]></label>\n";
							$forms .= "	<action><![CDATA[]]></action>\n";
						$forms .= "</form_builder>";
					} else {
						if (($list!="SFORM_DISPLAY_CONTACT_US" && $list!="Standard Contact Form") && ($label!="SFORM_DISPLAY_CONTACT_US" && $label!="Standard Contact Form")){
							$forms .= "<form_builder identifier='".$list."'>\n";
							eval("\$label = $list;");
							$forms .= "	<label><![CDATA[".$label."]]></label>\n";
							$forms .= "	<action><![CDATA[]]></action>\n";
							$forms .= "</form_builder>";
						}
					}
				}
			}
		}
		
		$forms .= "<form_builder identifier=''>\n";
		$forms .= "	<label><![CDATA[---- User Defined Forms ----]]></label>\n";
		$forms .= "	<action><![CDATA[]]></action>\n";
		$forms .= "</form_builder>";
		$result = $this->call_command("DB_QUERY",Array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$forms .= "<form_builder identifier='libertas_form_".$r["fb_identifier"]."'>\n";
			$forms .= "	<label><![CDATA[".$r["fb_label"]."]]></label>\n";
			$forms .= "	<action><![CDATA[".$r["fb_action"]."]]></action>\n";
			$forms .= "</form_builder>";
		}
		
		$out  =	"<module name=\"".$this->module_name."\" display=\"embedded_list\">";
		$out .= $forms;
		$out .= "</module>";
		print $out;
		return $out;
	}
	
	
	function cache_form($identifier){
		$out = $this->display_built_form(Array("identifier" => $identifier,"display"=>"none"));
		$lang="en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($identifier*1 == $identifier){
			$filename = $data_files."/form_".$this->client_identifier."_".$lang."_libertas_form_".$identifier.".xml";
		} else {
			$filename = $data_files."/form_".$this->client_identifier."_".$lang."_".$identifier.".xml";
		}
		$fp = fopen($filename, 'w');
		fwrite($fp, $out);
		fclose($fp);
					$um = umask(0);
					@chmod($filename, LS__FILE_PERMISSION);
					umask($um);
	}
	
	function load_cached_forms($parameters){
		$trans_ids = $this->check_parameters($parameters,"list_of_trans");
		$form_ids = $this->check_parameters($parameters,"list_of_forms");

		/* Starts Enquire Section to get productcode/productname from Enquire Link as Query String(Balmoral : By Muhammad Imran)*/
		$prodcode = $this->check_parameters($_REQUEST,"prodcode");
		$prodname = $this->check_parameters($_REQUEST,"prodname");
		/* Ends Enquire Section to get productcode/productname from Enquire Link as Query String(Balmoral : By Muhammad Imran)*/

		$loaded="";
		$lang="en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if($trans_ids == ""){
			$out ='<module name="Standard Forms" display="embeddedInformation">';
			for($index=0,$max=count($form_ids);$index<$max;$index++){
				$fid = $form_ids[$index];
				if(strpos($loaded,"::$fid::")===false){
					$loaded.="::$fid::";
//					print "<p>".$data_files."/form_".$this->client_identifier."_".$lang."_".$fid .".xml</p>";
					if (file_exists($data_files."/form_".$this->client_identifier."_".$lang."_".$fid .".xml")){
						$fp = file($data_files."/form_".$this->client_identifier."_".$lang."_".$fid .".xml");
						$out .= join(" ",$fp);
					} else {
						//$out .= "<text><![CDATA[".$data_files."/form_".$this->client_identifier."_".$lang."_". $fid .".xml not found]]</text>";
					}
				}
			}

/* Starts Enquire Section to attache productcode/productname with enquire form to sen as email (Balmoral : By Muhammad Imran)*/
				$out = str_replace('</seperator></seperator_row><input iconify="SAVE" type="submit" value="Submit"/></form>','
				<input type="hidden"  label="Product Code" name="prodcode" size="255" value="'.$prodcode.'"/>
				<input type="hidden"  label="Product Name" name="prodname" size="255" value="'.$prodname.'"/>
				</seperator></seperator_row><input iconify="SAVE" type="submit" value="Submit"/></form>',$out);
/* Ends Enquire Section to attache productcode/productname with enquire form to sen as email (Balmoral : By Muhammad Imran)*/

			$out .= "</module>";
			return $out;
		} else {
			$list = join($trans_ids, ",");
			if ($list!=""){
				$sql = "select embed_libertas_form.* from embed_libertas_form 
							left outer join form_builder_structure on fb_identifier = embed_libertas_form.form_int_identifier
						 where trans_identifier in ($list) and client_identifier = $this->client_identifier and (
							(form_str_identifier like 'libertas_form_%' and fb_status = 4 ) or (form_str_identifier not like 'libertas_form_%')
						)";
				
//				print $sql;
				$result = $this->call_command("DB_QUERY",Array($sql));
				$out ='<module name="Standard Forms" display="embeddedInformation">';
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$fid = $this->check_parameters($r,"form_str_identifier","");
					if ($fid==""){
						$fid = $this->check_parameters($r,"form_int_identifier",0);
					}
					if(strpos($loaded,"::$fid::")===false){
						$loaded.="::$fid::";
						$fname = $data_files."/form_".$this->client_identifier."_".$lang."_".$fid .".xml";
						//print "<li>$fname</li>";
						if (file_exists($fname)){
							$fp = file($fname);
							$out .= join(" ",$fp);
						} else {
//							print $data_files."/form_".$this->client_identifier."_".$lang."_".$fid .".xml not found <br/>";
						}
					}
				}
				
/* Starts Enquire Section to attache productcode/productname with enquire form to sen as email (Balmoral : By Muhammad Imran)*/
				$out = str_replace('</seperator></seperator_row><input iconify="SAVE" type="submit" value="Submit"/></form>','
				<input type="hidden"  label="Product Code" name="prodcode" size="255" value="'.$prodcode.'"/>
				<input type="hidden"  label="Product Name" name="prodname" size="255" value="'.$prodname.'"/>
				</seperator></seperator_row><input iconify="SAVE" type="submit" value="Submit"/></form>',$out);
/* Ends Enquire Section to attache productcode/productname with enquire form to sen as email (Balmoral : By Muhammad Imran)*/
				
				$out .= "</module>";
			} else {
				$out = "";
			}
			return $out;
		}
	}
	function restore($parameters){
		$sql = "select fb_identifier from form_builder_structure where fb_client = $this->client_identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$lang="en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$frms = $this->available_forms;
		if ($frms!=""){
			for ($index=0,$max=count($frms);$index<$max;$index++){
				$out = $this->call_command($frms[$index]."_CACHE",Array("show_module"=>0,"show_anyway"=>1));
				$fp = fopen($data_files."/form_".$this->client_identifier."_".$lang."_".$frms[$index].".xml", 'w');
				fwrite($fp, $out);
				fclose($fp);
					$um = umask(0);
					@chmod($data_files."/form_".$this->client_identifier."_".$lang."_".$frms[$index].".xml", LS__FILE_PERMISSION);
					umask($um);
			}
		}
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$identifier = $r["fb_identifier"];
			$out = $this->display_built_form(Array("identifier" => $identifier,"display"=>"none"));
			$fp = fopen($data_files."/form_".$this->client_identifier."_".$lang."_libertas_form_".$identifier.".xml", 'w');
			fwrite($fp, $out);
			fclose($fp);
					$um = umask(0);
					@chmod($data_files."/form_".$this->client_identifier."_".$lang."_libertas_form_".$identifier.".xml", LS__FILE_PERMISSION);
					umask($um);
		}
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=ENGINE_RESTORE"));
	}
	
	function remove_form($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$out = "";
		$user_location_access = $this->check_parameters($_SESSION,"SESSION_MANAGEMENT_ACCESS");

		if (count($user_location_access)==0){
			$user_secured_location = 0;
		$sql = "
		select distinct page_trans_data.trans_identifier, page_trans_data.trans_title
		from embed_libertas_form 
			inner join page_trans_data on page_trans_data.trans_identifier = embed_libertas_form.trans_identifier 
			where client_identifier = $this->client_identifier and form_int_identifier = $identifier
		";
		}else{
			$user_secured_location = 1;
		$sql = "
			select distinct page_trans_data.trans_identifier, page_trans_data.trans_title, relate_user_menu.*
			from embed_libertas_form 
				inner join page_trans_data on page_trans_data.trans_identifier = embed_libertas_form.trans_identifier 
				inner join menu_access_to_page on menu_access_to_page.trans_identifier  = embed_libertas_form.trans_identifier 
				left outer join relate_user_menu on menu_access_to_page.menu_identifier = relate_user_menu.menu_identifier 
				where embed_libertas_form.client_identifier = $this->client_identifier and form_int_identifier = $identifier
			";
		}
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		$author_access=0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (
					("PAGE_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("PAGE_AUTHOR"==$access[$index]) ||
					("PAGE_AUTHOR_ACCESS"==$access[$index])
				){
					$author_access=1;
				}
			}
		}
		//print $sql;
		$result = $this->call_command("DB_QUERY",Array($sql));
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			$out .= "<form><text><![CDATA[<table>";
				$out .= "<tr><td colspan='2'><strong>What follows in a list of documents that have embedded this form.</strong></td></tr>";
			while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
				$out .= "<tr><td>".$r["trans_title"]."</td>";
				if($author_access==1){
					if ($this->check_parameters($r,"user_identifier","__NULL__") != "__NULL__" || $user_secured_location==0){
						$out .= "<td><a href='admin/index.php?command=PAGE_EDIT&identifier=".$r["trans_identifier"]."'>Edit</a></td>";
					} else {
						$out .= "<td><a href='admin/index.php?command=PAGE_UPDATE_REQUEST&cancel=SFORM_BUILDER_LIST&identifier=".$r["trans_identifier"]."'>Request Update</a></td>";
					}
				}
				$out .= "</tr>";
			}
			$out .= "<tr><td colspan='2'><strong>You are required to remove the form from these pages before you can remove this form from the system.</strong></td></tr>";
			$out .= "</table>]]></text>";
			$out .= "<input type=\"button\" iconify=\"CANCEL\" value=\"ENTRY_NO\" command=\"SFORM_BUILDER_LIST\"/>";
			$out .= "</form>";
		}else{
			$out .="<form name=\"process_form\" label=\"Remove this entry completely\">";
			$out .= "<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>";
			$out .= "<input type=\"hidden\" name=\"command\"><![CDATA[SFORM_BUILDER_REMOVE_CONFIRM]]></input>";
			$out .= "<text><![CDATA[
						There are no pages with this form embedded,<br>
						Are you sure you want to remove this form now?<br>
						Removal of this form will result in the loss of any<br>
						Stored data associated with this form to be lost.
					]]></text>";
			$out .= "<input type=\"button\" iconify=\"CANCEL\" value=\"ENTRY_NO\" command=\"SFORM_BUILDER_LIST\"/>";
			$out .= "<input type=\"submit\" iconify=\"YES\" value=\"ENTRY_YES\"/>";
			$out .="</form>";
		}
		$out = "<module name=\"".$this->module_name."\" display=\"form\">".$out."</module>";
		return $out;
	}
	function remove_form_confirm($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$sql = "delete from form_builder_data where fbd_client=$this->client_identifier and fbd_form=$identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from form_builder_emails where fbe_client=$this->client_identifier and fbe_form=$identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from form_builder_fields where fbf_client=$this->client_identifier and fbf_link=$identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from form_builder_group_fields where fbg_client=$this->client_identifier and fbg_form=$identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from form_builder_locations where fbl_client=$this->client_identifier and fbl_form=$identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from form_builder_required_fields where fbr_client=$this->client_identifier and fbr_form=$identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from form_builder_structure where fb_client=$this->client_identifier and fb_identifier=$identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$out = "<module name=\"".$this->module_name."\" display=\"form\">
					<form label='User definable form removal confirmation.'>
						<text><![CDATA[You have successfully removed the from from the system]]></text>
						<input type=\"button\" iconify=\"CANCEL\" value=\"BACK\" command=\"SFORM_BUILDER_LIST\"/>
					</form>
				</module>";
		return $out;
	}


	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Form Builder 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function display_form_builder($parameters){
	
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		$submit_button_label="";
		$emails 		= Array();
		$str 			= "";
		$sql 			= "select * from form_builder_structure where fb_identifier =$identifier and fb_client = $this->client_identifier";
		$result			= $this->call_command("DB_QUERY",array($sql));
		$entry			= Array();
		$action			= 0;
		$label			= "";
		$url			= "";
		$fb_default_email		= "";
		$fb_default_subject		= "";
		$fb_status				= "";
		$confirm_screen = "";
		$emailscreen = "";
		$method 		= "";
		if ($result){
			while($r= $this->call_command("DB_FETCH_ARRAY",array($result))){
				$str 					= str_replace(Array("\r\n","\n"), Array("",""),$this->check_parameters($r,"fb_xml_structure"));
				$label 					= $this->check_parameters($r,"fb_label");
				$action 				= $this->check_parameters($r,"fb_action");
				$url 					= $this->check_parameters($r,"fb_url");
				$confirm_screen			= $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$this->check_parameters($r,"fb_confirm_screen")));
				$method					= $this->check_parameters($r,"fb_method","post");
				$emailscreen			= str_replace(Array("\r\n"), Array("<br>"), $this->check_parameters($r,"fb_emailscreen",""));
				$submit_button_label	= $this->check_parameters($r,"fb_submit_button_label","");
				$fb_default_email		= $this->check_parameters($r,"fb_default_email","");
				$fb_default_subject		= $this->check_parameters($r,"fb_default_subject","");
				$fb_status				= $this->check_parameters($r,"fb_status","");
			}
		}
		if ($action ==0 || $action==1){
			$sql	 = "select * from form_builder_emails where fbe_form =$identifier and fbe_client = $this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			$entry=Array();
			if ($result){
				while($r= $this->call_command("DB_FETCH_ARRAY",array($result))){
					$emails[count($emails)] = Array($r["fbe_subject"],$r["fbe_email"],$r["fbe_default"]);
				}
			}
		}
		$this_editor 				= $this->check_parameters($this->editor_configurations,"ENTRY_DESCRIPTION",Array());
		$config_status_of_editor	= $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  				= $this->check_parameters($this_editor,"locked_to","");
	
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="<page_options>";
		$out .="<header><![CDATA[".LOCALE_FORM_BUILDER."]]></header>";
		$out .=		$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","SFORM_BUILDER_LIST",LOCALE_CANCEL));
		$out .="	<button iconify='SAVE' value='".SAVE_DATA."' command='SFORM_BUILD_XML'/>
				</page_options>";			
		$out .="<form name=\"wizard_frm\" method=\"post\" label=\"".LOCALE_FORM_BUILDER."\">";
		$out .="	<input type='hidden' name='identifier' value='$identifier'/>";
		$out .="	<input type='hidden' name='command' value='SFORM_FORM_BUILDER_SUBMIT' />";
		$out .="	<input type='hidden' name='xml_representation' value='' />";
		$out .="	<input type='hidden' name='group_fields' value=''/>";
		$out .="	<input type='hidden' name='required_fields' value=''/>";
		$out .="	<input type='hidden' name='email_list' value=''/>";
		$out .="	<input type='hidden' name='destination_url' value=''/>";
//		$out .="		<section label=\"".LOCALE_DEFAULT_MENU_MSG."\" name=\"trans_menu_location\" command=\"LAYOUT_MENU_SELECT\"><![CDATA[$menu_location_list]]></section>";
//		$out .="		<input type='hidden' name=\"trans_menu_locations\" ><![CDATA[$menu_location_identifiers]]></input>";
		$out .="	<page_sections>";
		$out .="	<section label=\"".LOCALE_MAIN."\" name=\"trans_menu_location\">";
		$out .="		<input type='text' name='form_header_label' label='".LOCALE_SFORM_FORM_LABEL."'><![CDATA[$label]]></input>";
		$out .="			<text><![CDATA[If you are emailing this what would be the default settings for the following]]></text>";
		$out .="			<input type='text' name='fb_default_subject' size='255' label='Default Subject'><![CDATA[$fb_default_subject]]></input>";
		$out .="			<input type='text' name='fb_default_email' size='255' label='Default Email Address'><![CDATA[$fb_default_email]]></input>";
		$out .="		<input type='text' name='form_submit_button_label' label='".LOCALE_SFORM_SUBMIT_LABEL."'><![CDATA[$submit_button_label]]></input>";
		$out .="		</section>";
		$out .="		<section label=\"Wizard\" name=\"wizard\">";
		$out .="		</section>";
		$out .="		<section label=\"Ranking\" name=\"myform\" onclick=\"checkMyForm\">";
		$out .="		</section>";
		$out .="		<section label=\"Preview\" name=\"preview\" onclick=\"checkMyPreview\">";
		$out .="		</section>";
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$hidden_tab = "";
		} else {
			$hidden_tab = "hidden='true'";
		}
		$out .="		<section label=\"Advanced\" name=\"Advanced\" $hidden_tab>";
		$out .=				"<radio name='method' label='Submit information via the following format'>
							<option value='post' ";
							if (($method=='post')||($method=='')){
								$out .= " selected='true'";
							}
		$out .=				"><![CDATA[Post]]></option>
							<option value='get'";
							if ($method=='get'){
								$out .= " selected='true'";
							}
		$out .=				"><![CDATA[Get]]></option>
							</radio>";
		$out .="		<radio name='submission' label='".LOCALE_SFORM_ON_SUBMISSION."' onclick='submission' span='list_of_emails'>
							<option value='0'";
		if ($action=="0"){
			$out .=" selected='true'";
		}
		$out .="><![CDATA[".LOCALE_SFORM_EMAIL."]]></option>";
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$out .="<option value='1'";
			if ($action=="1"){
				$out .=" selected='true'";
			}
			$out .="><![CDATA[".LOCALE_SFORM_STOREEMAIL."]]></option>
					<option value='2'";
			if ($action=="2"){
				$out .=" selected='true'";
			}
			$out .="><![CDATA[".LOCALE_SFORM_STORE."]]></option>";
			$out .="<option value='3'";
			if ($action=="3"){
				$out .=" selected='true'";
			}
			$out .="><![CDATA[".LOCALE_SFORM_TO_URL."]]></option>";
			$out .="<option value='4'";
			if ($action=="4"){
				$out .=" selected='true'";
			}
			$out .="><![CDATA[".LOCALE_SFORM_EMAIL_URL."]]></option>";			
		}
	
		$out .="			</radio>";
		$out .="			<select name='fb_status' label='Is this form available to be embedded?'>";
		$out .= "				<option value='4'";
		if ($fb_status==4){
			$out .= " selected='true'";
		}
		$out .= "				>Available</option>";
		$out .= "				<option value='1'";
		if ($fb_status==1){
			$out .= " selected='true'";
		}
		$out .= "				>Unavailable</option>";
		$out .= "			</select>";
		$email="";
		for ($index=0, $max = count($emails);$index<$max;$index++){
			$default= $emails[$index][2] ? "true" : "false" ;
			$email .="		<email selected='$default'>
								<subject><![CDATA[".$emails[$index][0]."]]></subject>
								<address><![CDATA[".$emails[$index][1]."]]></address>
							</email>";
		}
		$out .="<form_builder>
					<fields>$str</fields>
					<url><![CDATA[$url]]></url>
					$email
					</form_builder>";
		
		$out .="		</section>";
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$out .="		<section label=\"Confirm Message\" name=\"confirmationscreen\">";
			$out .="			<textarea size='40' height='15' name='confirm_screen' type='RICH-TEXT' label='Confirm Screen Message' config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$confirm_screen]]></textarea>";
			$out .="		</section>";
			$out .="		<section label=\"Email Message\" name=\"emailmsgscreen\" onclick='fb_generate_field_list' ><parameters></parameters>";
			$out .="			<textarea size='40' height='15' name='emailscreen' type='FIELD-TEXT' label='Email Message' config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$emailscreen]]></textarea>";
			$out .="		</section>";
		} else {
			$out .="		<input type='hidden' name='confirm_screen'><![CDATA[]]></input>";
			$out .="		<input type='hidden' name='emailscreen'><![CDATA[]]></input>";
		}
		$out .="</page_sections>";
		$out .="</form>";
		$out .="</module>";
		return $out;
	}

	function processToXML($str){
	//print $str."\n\n\n\n\n";
		$xml_array = split(":===:",$str);
		$xml_data  = "<seperator_row><seperator>";
		$val = split("::",$xml_array[0]);
//		print $xml_array[0];
		$xml_data .= "<input type='hidden'  name='number_of_fields' value='".$val[1]."'/>";
		$xml_count = count($xml_array);
		$find="";
		for($index=1 ; $index <$xml_count; $index++){
			//print "[$find]\n";
			if($find==""){
				$val = $this->get_keys($xml_array[$index]);
//				print_r($val); 
//				print "[".$val[0]."::(".$xml_array[$index].")]\n";
				/* starts Label Msg display Comment and added by Muhammad Imran*/
/*				if ($this->check_parameters($val,"fieldtype")=="CDATA"){
					$xml_data .= "<text><![CDATA[";
					$find="~~CDATA";
				}
*/				
				if ($this->check_parameters($val,"fieldtype")=="CDATA"){
					$xml_data .= "<text ";
					$find="~~CDATA";
				}
				/* ends Label Msg display Comment and added by Muhammad Imran*/

				if ($this->check_parameters($val,"fieldtype")=="hidden"){
					$xml_data .= "<input type='hidden' name='".$this->check_parameters($val,"name")."' value='".$this->check_parameters($val,"value")."'/>\n";
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="text"){
					$xml_data .= "<input type='text'  label='".$val["label"]."' name='".$val["name"]."' size='".$val["width"]."'";
					if($this->check_parameters($val,"required","0")=="1"){
						$xml_data .= " required='true'";
					}
					$xml_data .= "/>\n";
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="textarea"){
					$xml_data .= "<textarea label='".$val["label"]."' name='".$val["name"]."' size='".$val["width"]."' height='".$val["height"]."'";
					if($this->check_parameters($val,"required","0")=="1"){
						$xml_data .= " required='true'";
					}
					$xml_data .= "></textarea>\n";
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="date_time"){
					$xml_data .= "<input type='date_time' label='".$this->check_parameters($val,"label")."' name='".$this->check_parameters($val,"name")."' dateType='".$this->check_parameters($val,"dateType")."'/>\n";
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="fileupload"){
					$xml_data .= "<input type='file' label='".$this->check_parameters($val,"label")."' name='".$this->check_parameters($val,"name")."'/>\n";
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="select"){
					$xml_data .= "<select label='".$val["label"]."' name='".$val["name"]."'";
					if($this->check_parameters($val,"other","0")=="1"){
						$xml_data .= " other='true'";
					}
					if($this->check_parameters($val,"required","0")=="1"){
						$xml_data .= " required='true'";
					}
					if ($this->check_parameters($val,"multiple")!="0"){
						$xml_data .= " multiple='1'";
					}
					if ($this->check_parameters($val,"size")!=""){
						$xml_data .= " size='".$val["size"]."'";
					}
					$xml_data .= ">\n";
					if ($this->check_parameters($val,"other_label")!="" && $this->check_parameters($val,"other")!="0"){
						$xml_data .= " <other_label><![CDATA[".$val["other_label"]."]]></other_label>\n";
					}
					$find = "options";
				}
				if ($this->check_parameters($val,"fieldtype")=="radio"){
					$xml_data .= "<radio label='".$val["label"]."' name='".$val["name"]."' type='".$val["type"]."'";
					if($this->check_parameters($val,"other","0")=="1"){
						$xml_data .= " other='true'";
					}
					if($this->check_parameters($val,"required","0")=="1"){
						$xml_data .= " required='true'";
					}
					$xml_data .= ">\n";
					if ($this->check_parameters($val,"other_label")!="" && $this->check_parameters($val,"other")!="0"){
						$xml_data .= " <other_label><![CDATA[".$val["other_label"]."]]></other_label>\n";
					}
					$find = "options";
				}
				if ($this->check_parameters($val,"fieldtype")=="checkboxes"){
					$xml_data .= "<checkboxes label='".$val["label"]."' name='".$val["name"]."' type='".$val["type"]."'";
					if($this->check_parameters($val,"other","0")=="1"){
						$xml_data .= " other='true'";
					}
					if($this->check_parameters($val,"required","0")=="1"){
						$xml_data .= " required='true'";
					}
					$xml_data .= ">\n";
					if ($this->check_parameters($val,"other_label")!="" && $this->check_parameters($val,"other")!="0"){
						$xml_data .= " <other_label><![CDATA[".$val["other_label"]."]]></other_label>\n";
					}
					$find = "options";
				}
				if ($this->check_parameters($val,"fieldtype")=="subject"){
					$xml_data .= "<form_subject label='".$val["label"]."' name='".$val["name"]."' type='".$val["type"]."'>\n";
					$find = "options";
				}
				if ($this->check_parameters($val,"fieldtype")=="seperator"){
					$xml_data .= "</seperator><seperator>\n";
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="seperator_row"){
					$xml_data .= "</seperator></seperator_row><seperator_row><seperator>\n";
					$find="";
				}
//				print "[$find]";
			} else if($find=="~~CDATA"){
				if ($xml_array[$index]=="~~CDATA"){
					$xml_data .= "]]></text>";
					$find="";
				} else {
					//$xml_data .= $xml_array[$index];//
					/* Starts to get Label Msg add on form Comment and added By Muhammad Imran */
					
					//$xml_data .= str_replace(Array('"'),Array("[[quote]]"),$xml_array[$index]);

					$cdata_arr = split(":=========:",$xml_array[$index]);

					$cdata_name = split("::",$cdata_arr[1]);
					$cdata_value = split("::",$cdata_arr[2]);
					
					$xml_data .= "name='".$cdata_name[1]."'><![CDATA[";
					$xml_data .= str_replace(Array('"'),Array("[[quote]]"),$cdata_value[1]);
					/* Ends to get Label Msg add on form Comment and added By Muhammad Imran */
				}
			} else {
				if ($xml_array[$index]=="~~select"){
					$xml_data .= "</select>\n";
					$find="";
				} else if ($xml_array[$index]=="~~checkboxes"){
					$xml_data .= "</checkboxes>\n";
					$find="";
				} else if ($xml_array[$index]=="~~radio"){
					$xml_data .= "</radio>\n";
					$find="";
				} else if ($xml_array[$index]=="~~subject"){
					$xml_data .= "</form_subject>\n";
					$find="";
				} else {
					//print "check ".$xml_array[$index]."<br/>";
					$option = $this->get_option_values($xml_array[$index]);
					$xml_data .= "<option value='".$this->check_parameters($option,"value")."'";
					if ($this->check_parameters($option,"checked")=="true"){
						$xml_data .= " checked='true'";
					}
					$xml_data .= "><![CDATA[".$this->check_parameters($option,"label")."]]></option>";
				}
			}
		}
		$xml_data .= "</seperator></seperator_row>";
		return $xml_data;
	}
	
	function get_keys($str){
		$list = Array();
		$keys = split(":=========:",$str); 
		$list["fieldtype"] = $keys[0];
		for ($i=1;$i<count($keys);$i++){
			$k = split("::",$keys[$i]);
			$list[$k[0]] = $k[1];
		}
		return $list;
	}
	
	function get_option_values($str){
		$list = Array();
		$keys = split (":=========:",$str); 
		for ($i=0;$i<count($keys);$i++){
			$k = split("::",$keys[$i]);
			$list[$k[0]]=$k[1];
		}
		return $list;
	}

	function processFieldTypes($str){
//		print $str."\n\n\n\n\n";
		$xml_array = split(":===:",$str);
		$resultTypes = Array();
		//$xml_data  = "<seperator>";
		$val = split("::",$xml_array[0]);
//		print $xml_array[0];
		//$xml_data .= "<input type='hidden'  name='number_of_fields' value='".$val[1]."'/>";
		$xml_count = count($xml_array);
		$find="";
		$c =0;
		for($index=1 ; $index <$xml_count; $index++){
			if($find==""){
				$val = $this->get_keys($xml_array[$index]);
				if ($this->check_parameters($val,"fieldtype")=="hidden"){
					$resultTypes[$this->check_parameters($val,"name","field".$c)] = "hidden";
					$c++;
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="text"){
					$resultTypes[$this->check_parameters($val,"name","field".$c)] = "text";
					$c++;
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="textarea"){
					$resultTypes[$this->check_parameters($val,"name","field".$c)] = "textarea";
					$c++;
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="date_time"){
					$resultTypes[$this->check_parameters($val,"name","field".$c)] = "date_time";
					$c++;
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="fileupload"){
					$resultTypes[$this->check_parameters($val,"name","field".$c)] = "fileupload";
					$c++;
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="select"){
					$resultTypes[$this->check_parameters($val,"name","field".$c)] = "select";
					$c++;
					$find = "options";
				}
				if ($this->check_parameters($val,"fieldtype")=="checkboxes"){
					$resultTypes[$this->check_parameters($val,"name","field".$c)] = "checkboxes";
					$c++;
					$find = "options";
				}
				if ($this->check_parameters($val,"fieldtype")=="radio"){
					$resultTypes[$this->check_parameters($val,"name","field".$c)] = "radio";
					$c++;
					$find = "options";
				}
				if ($this->check_parameters($val,"fieldtype")=="seperator"){
					$c++;
					$find="";
				}
				if ($this->check_parameters($val,"fieldtype")=="seperator_row"){
					$c++;
					$find="";
				}
			} else if($find=="CDATA"){
				if ($xml_array[$index]=="~~CDATA"){
					$find="";
				}
			} else {
				if ($xml_array[$index]=="~~select"){
					$find="";
				} else if ($xml_array[$index]=="~~checkboxes"){
					$find="";
				} else if ($xml_array[$index]=="~~radio"){
					$find="";
				} else if ($xml_array[$index]=="~~subject"){
					$find="";
				} else {
					//$option = $this->get_option_values($xml_array[$index]);
				}
			}
		}
		return $resultTypes;
	}
	/*************************************************************************************************************************
    * save the form information   line 2076 - addslashes added back in 04 01 2005
    *************************************************************************************************************************/
	function save_form_info($parameters){
		$debug = $this->debugit(false, $parameters);
		$copy = $parameters;
		$number_of_fields	= $this->check_parameters($parameters,"number_of_fields",0);
		$identifier 		= $this->check_parameters($parameters,"identifier",0);
		$form_subject		= $this->check_parameters($parameters,"form_subject");
		/* Starts Enquire Section to get productcode/productname in the email (Balmoral : By Muhammad Imran)*/
		$prodcode			= $this->check_parameters($parameters,"prodcode");
		$prodname			= $this->check_parameters($parameters,"prodname");
		/* Ends Enquire Section to get productcode/productname in the email (Balmoral : By Muhammad Imran)*/

		$email_counter 		= 0;
		$confirm			= "";
//	addslashes(fread(fopen($form_data, "r"), filesize($form_data)))
		$sql = "select * from form_builder_structure where fb_identifier = $identifier and fb_client = $this->client_identifier";
		if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
		$result = $this->call_command("DB_QUERY",Array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$action 		= $r["fb_action"];
			$xml_structure  = $r["fb_xml_structure"];
			$confirm		= trim($r["fb_confirm_screen"]);
			$emailscreen	= html_entity_decode(html_entity_decode($r["fb_emailscreen"]));
			$label			= $r["fb_label"];
			$default_email	= $r["fb_default_email"];
			$default_subject= $r["fb_default_subject"];
			$destination_url= $r["fb_url"];			
		}
		if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$default_email</p>\n";
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		$sql = "select * from form_builder_required_fields where fbr_form = $identifier and fbr_client = $this->client_identifier";
		if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$required=Array();
		$required_count=0;
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$required[count($required)]		= $r["fbr_index"];
			$required_count					  ++;
		}
		$sql = "select * from form_builder_field_types where fbft_form = $identifier and fbft_client = $this->client_identifier";
		if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$fieldlist=Array();
		$fieldlist_count=0;
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$fieldlist[count($fieldlist)]		= Array($r["fbft_field"],$r["fbft_type"], $this->check_parameters($parameters,$r["fbft_field"]));
			$fieldlist_count					++;
		}
		$ok = true;
		if($destination_url == "") {			
			for ($index=0;$index<$required_count;$index++){														
				if ($this->check_locale_starter($this->check_parameters($parameters,"field".$required[$index]))==""){
					$ok = false;
				}
			}
		}
		else{
			foreach ($parameters as $key => $value) {
				$arrofkeys[] = $key;
			}
			for ($index=0;$index<$required_count;$index++){
				if ($this->check_locale_starter($this->check_parameters($parameters,$arrofkeys[$required[$index]+3]))==""){
					$ok = false;
				}
			}			
		}
		if ($ok){
			$str = "<form_data identifier='$identifier' submission_time='$now'>";
			$index =0;
//			print "[$fieldlist_count]";
			foreach($fieldlist as $key => $val ){
				$index=$key;
//				print $fieldlist[$index][1];
				if ($fieldlist[$index][1] == "fileupload"){
					$field = $this->check_parameters($_FILES,$fieldlist[$index][0],Array());
					$tmp_name = $this->check_parameters($field,"tmp_name");
					$file_name = $this->check_parameters($field,"name");
	//				print "\n\n<p>[$tmp_name, $file_name]</p>";
				} else if ($fieldlist[$index][1] == "date_time"){
					$fieldy = $this->check_parameters($parameters, $fieldlist[$index][0] . "_date_year", "0000");
					$fieldm = $this->check_parameters($parameters, $fieldlist[$index][0] . "_date_month", "00");
					$fieldd = $this->check_parameters($parameters, $fieldlist[$index][0] . "_date_day", "00");
					$fieldh = $this->check_parameters($parameters, $fieldlist[$index][0] . "_date_hour", "00:00");
					$field = $fieldy."/".$fieldm."/".$fieldd." ".$fieldh.":00";
					$parameters[$fieldlist[$index][0]] = $field;
					$str .= '<field name="'.$fieldlist[$index][0].'"><![CDATA['.strip_tags($field)."]]></field>";
				} else {
				
					$field = $this->check_locale_starter($this->check_parameters($parameters,$fieldlist[$index][0],""));
					if ($field.'' == 'Array'){
						for ($f_index=0, $max=count($field); $f_index<$max; $f_index++){
							if ($field[$f_index]=="_system_defined_other_"){
								$str .= '<field name="'.$this->makeCleanOutputforXSL($fieldlist[$index][0]).'"><![CDATA['.$this->makeCleanOutputforXSL($this->check_parameters($parameters, "other_entry_".$fieldlist[$index][0], ""))."]]></field>";
							} else {
								$str .= '<field name="'.$this->makeCleanOutputforXSL($fieldlist[$index][0]).'"><![CDATA['.$this->makeCleanOutputforXSL($field[$f_index])."]]></field>";
							}
						}
					} else {
						if ($field=="_system_defined_other_"){
							$str .= '<field name="'.$this->makeCleanOutputforXSL($fieldlist[$index][0]).'"><![CDATA['.$this->makeCleanOutputforXSL($this->check_parameters($parameters, "other_entry_".$fieldlist[$index][0], ""))."]]></field>";
						} else {
							$str .= '<field name="'.$this->makeCleanOutputforXSL($fieldlist[$index][0]).'"><![CDATA['.$this->makeCleanOutputforXSL($field)."]]></field>";
						}
					}
				}
			}
			$str .= '</form_data>';
//			print "<!-- $str -->";
//			$this->exitprogram();
			if ($action==0 || $action ==1 || $action == 4){
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
				- EMAIL THE DATA TO THE DESIRED PARTY
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
				*/
				$email	= Array();
				$email_counter =0;
				if (is_array($form_subject)){
					$fs_list = join("', '",$form_subject);
					$sql	= "select * from form_builder_emails where fbe_subject in ('$fs_list') and fbe_client=$this->client_identifier and fbe_form = $identifier";
					if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
					$result	= $this->call_command("DB_QUERY",Array($sql));
					$num	= $this->call_command("DB_NUM_ROWS",Array($result));
					if ($num==0){
						$email[count($email)]	= $default_email;
						$form_subject			= $default_subject;
					} else {
						while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
//							if (strlen($email)>0){
//								$email .= ", ";	
//							}
							$email[count($email)] = $r["fbe_email"];
							$email_counter++;
						}
					}
				} else {
					$sql	= "select * from form_builder_emails where fbe_subject='$form_subject' and fbe_client=$this->client_identifier and fbe_form = $identifier";
					$result	= $this->call_command("DB_QUERY",Array($sql));
					$num	= $this->call_command("DB_NUM_ROWS",Array($result));
					if ($num==0){
						$email[count($email)]	= $default_email;
						$form_subject			= $default_subject;
					} else {
						while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$val =  $this->check_parameters($r,"fbe_email");
							if($val==""){
								$val = $default_email;
							}
							$email[count($email)] = $val;
							$email_counter++;
						}
					}
				}
				if ($email_counter==0){
					$email[count($email)]	= $default_email;
					$form_subject			= $default_subject;
				}
				//print $sql;
				$str = "<form_submission>".$str."<form_structure>$xml_structure</form_structure></form_submission>";
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
				- if the user has defined a Email message to send then fill it in.
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=	
				*/
				if(strlen($emailscreen)>0){
					$xsl_files	= $this->parent->site_directories["XSL_THEMES_DIR"];
					$stylesheet = $xsl_files."/stylesheets/themes/site_administration/form_builder_email_generator.xsl";
					$this->call_command("XMLPARSER_LOAD_XML_STR",array($str));
					$this->call_command("XMLPARSER_LOAD_XSL_FILE",array($stylesheet));
					$cmd=strip_tags(str_replace(Array("<"."\?"."xml version=\"1.0\"\?".">"),Array(""),"\$l = Array(".$this->call_command("XMLPARSER_TRANSFORM").");"));
					///print "<!-- $cmd -->";
					eval($cmd);
					$unique_list = Array();
					$unique_counter=0;
					for ($index=0,$m=count($l);$index<$m;$index++){
						if ($unique_counter==0){
							$unique_list[0] = Array($l[$index][0],$l[$index][1]);
							$unique_counter++;
						} else {
							$found=-1;
							for($y = 0 ; $y< count($unique_list); $y++){
								if ($unique_list[$y][0] == $l[$index][0]){
									$unique_list[$y][1] = $unique_list[$y][1].", ".$l[$index][1];
									$found=1;
								}
							}
							if ($found==-1){
								$unique_list[count($unique_list)] = Array($l[$index][0],$l[$index][1]);
								$unique_counter++;
							}
						}
					}
					for($index=0,$m=count($unique_list);$index<$m;$index++){
						$emailscreen = str_replace(Array("[[".str_replace(Array("[[quote]]"),Array("\""),$unique_list[$index][0])."]]"), Array($unique_list[$index][1]), $emailscreen);	
					}
				} else {
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
					- if the user has not filled in a defined email message then send a default formatted one.
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=	
					*/
					$xsl_files	= $this->parent->site_directories["XSL_THEMES_DIR"];
					$stylesheet = $xsl_files."/stylesheets/themes/site_administration/form_builder_default_email_generator.xsl";
					
					$this->call_command("XMLPARSER_LOAD_XML_STR",array($str));
					$this->call_command("XMLPARSER_LOAD_XSL_FILE",array($stylesheet));
					$data = $this->call_command("XMLPARSER_TRANSFORM");
					$emailscreen = str_replace(Array("[[returns]]"), Array("\r\n"), substr($data,strlen('<?xml version="1.0"?>')));
				}

				/* Starts Enquire Section to Embedd productcode/productname in the email (Balmoral : By Muhammad Imran)*/
				if ($prodcode != ""){
					$prodcode_name = $prodname." (".$prodcode.")"."\r\n";
					$emailscreen = $prodcode_name.$emailscreen;
					//echo $emailscreen;die;
				}
				/* Ends Enquire Section to Embedd productcode/productname in the email (Balmoral : By Muhammad Imran)*/

				$output = $emailscreen;
				$e = Array();
				foreach($fieldlist as $key => $val ){
					$index=$key;
					if ($fieldlist[$index][1] == "fileupload"){
						$field = $this->check_parameters($_FILES,$fieldlist[$index][0],Array());
						if (file_exists($this->check_parameters($field,"tmp_name"))){
							if ($this->check_parameters($e,"attachments","__NOT_FOUND__")=="__NOT_FOUND__"){
								$e["attachments"] =Array();
							}
							$e["attachments"][count($e["attachments"])] = Array(
								"actual_filename"	=> $this->check_parameters($field,"tmp_name"), 
								"original_filename"	=> $this->check_parameters($field,"name"), 
								"file_size"			=> $this->check_parameters($field,"size"), 
								"mime_type"			=> $this->check_parameters($field,"type")
							);
						}
					}
				}
				if (is_array($form_subject)){
					$form_subject =join(",", $form_subject);
				}
				if ($this->parent->domain=="dev"){
//					$e["from"]		= "info@libertas-solutions.com";
					$e["from"]		= "info@".$this->parseDomain($this->parent->domain);
				} else {
					$e["from"]		= "info@".$this->parseDomain($this->parent->domain);
				}
				$e["format"]	= "plain";
				$e["subject"]	= $form_subject;
				$e["body"]		= str_replace(Array("_system_defined_other_"), Array(""),$output);
				$e["to"] 		= $email[0];
				if ($email_counter > 1){
					$e["cc"]		= Array();
					for ($ccindex=0;$ccindex < $email_counter - 1;$ccindex++){
						$e["cc"][$ccindex] 	= $email[$ccindex+1];
//						print "<p>".$email[$ccindex+1]."</p>";
					}
				}
				if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n".$email[0]."</p>\n";
				$this->call_command("EMAIL_QUICK_SEND",$e);
/*				print "body [info@".$this->parseDomain($this->parent->domain)."][$email][$form_subject][$output]";
				print $this->call_command("EMAIL_QUICK_SEND",Array(
						"from" 		=> $e["from"],
						"subject"	=> "subject",
						"body"		=> "body [info@".$this->parent->domain."][$email][$form_subject][$output]",
						"to"		=> "adrian@bloodmoongames.com",
						"format"	=> "plain"
					)
				);
				*/
				/*
					thank you msg
				*/
			}
			if ($action==1 || $action==2){
				$debug = false;
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
				- SAVE THE DATA TO THE SAVE_TABLE
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
				*/
				$now = $this->libertasGetDate("Y/m/d H:i:s");
				$sql = "insert into form_builder_data (fbd_client, fbd_form, fbd_filled_in_by, fbd_submitted) values ($this->client_identifier, $identifier, ".$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1).", '$now')";
				if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
				$result = $this->call_command("DB_QUERY",Array($sql));
				$sql = "select * from form_builder_data where fbd_client=$this->client_identifier and fbd_form=$identifier and fbd_filled_in_by=".$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1)." and fbd_submitted = '$now';";
				if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
				$result = $this->call_command("DB_QUERY",Array($sql));
				$link=-1;
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$link 		= $r["fbd_identifier"];
				}
				foreach($fieldlist as $key => $val ){
					$index=$key;
					$field = $this->check_parameters($parameters,$fieldlist[$index][0],"");
					if ($field."" == "Array"){
						for($option=0,$max = count($field);$option<$max;$option++){
							if ($field[$option]!="_system_defined_other_"){
								$sql = "insert into form_builder_fields (fbf_client, fbf_link, fbf_field, fbf_value_group) 
										values 
									($this->client_identifier, $link, '".$fieldlist[$index][0]."', '".$field[$option]."')";
								if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
								$this->call_command("DB_QUERY",Array($sql));
							}
						}
					} else {
						if ($fieldlist[$index][1] == "fileupload"){
							$f = $this->check_parameters($_FILES, $fieldlist[$index][0], Array());
							if (file_exists($this->check_parameters($f, "tmp_name"))){
								$data = addslashes(fread(fopen($f["tmp_name"], "r"), filesize($f["tmp_name"])));
								$sql = "insert into form_builder_fields (fbf_client, fbf_link, fbf_field, fbf_value, fbf_mime_type) 
										values 
									($this->client_identifier, $link, '".$fieldlist[$index][0]."', '$data', '".$this->check_parameters($f,"type")."')";
							}
							if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
							$this->call_command("DB_QUERY",Array($sql));
						} else {
							if ($field!="_system_defined_other_"){
								$sql = "insert into form_builder_fields (fbf_client, fbf_link, fbf_field, fbf_value_group) 
									values 
								($this->client_identifier, $link, '".$fieldlist[$index][0]."', '$field')";
								if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
								$this->call_command("DB_QUERY",Array($sql));
							}
						}
					}
				}
			}
			// SUBMIT TO A URL AFTER EMAILING CONTENTS
			if ($action==4){
				foreach($fieldlist as $key => $val ){
					$form_data .= $fieldlist[$key][0] ."=". urlencode($fieldlist[$key][2]) ."&";
				}
				$form_data = substr($form_data,0,strlen($form_data)-1);
				header("Location: ".$destination_url."?".$form_data);
			   	exit();							
			}
//			$out =	"<text><![CDATA[".LOCALE_FORM_BUILDER_FORM_SUBMIT."]]></text>";
			$out = "<module name=\"".$this->module_name."\" display=\"confirm\">";
			$out .= "<text><![CDATA[<h1 class=\"entrylocation\"><span>$label</span></h1>]]></text>";
			if ($confirm==""){
				$out .=	"<text><![CDATA[".LOCALE_FORM_BUILDER_FORM_SUBMIT."]]></text>";
			} else {
				$out .=	"<text><![CDATA[" . $confirm . "]]></text>";
			}
			$out .=	"</module>";
		} else {
			$out ="";
			$str = '<values identifier="'.$identifier.'" submission_time="'.$now.'">';
			
			for($index=0;$index<count($fieldlist);$index++){
				//$field = $this->check_parameters($copy,$fieldlist[$index][0],"");
		//		print $fieldlist[$index][0]." = ".$field." ".$fieldlist[$index][2]."<br>";
				if (is_array($fieldlist[$index][2])){
					$l = count($fieldlist[$index][2]);
					for($field_index=0;$field_index<$l;$field_index++){
						$str .= '<field name="'.$fieldlist[$index][0].'"><![CDATA['.$fieldlist[$index][2][$field_index].']]></field>';
					}
				} else {
					$str .= '<field name="'.$fieldlist[$index][0].'"><![CDATA['.$fieldlist[$index][2].']]></field>';
				}
			}
			$str .= '</values>';
			$out = $this->display_built_form(Array("identifier" => $identifier,"values"=>$str));
		}
		return $out;
	}

	function get_uploadedfile($parameters){
		$sql ="select fbf_value, fbf_mime_type from form_builder_fields where fbf_client= $this->client_identifier and fbf_field='field9' and fbf_link=24";
		$result = $this->call_command("DB_QUERY",Array($sql));
		while ($r=$this->call_command("DB_FETCH_ARRAY",Array($result))){
			header("Content-type: ".$r["fbf_mime_type"]);
			print $r["fbf_value"];
		}
		exit();
	}
	
	function clone_form($parameters){
		$debug = false;
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		if($identifier>-1){
			$entry=Array();
			$sql ="select * from form_builder_structure where fb_identifier = $identifier and fb_client = $this->client_identifier";
		if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r=$this->call_command("DB_FETCH_ARRAY",Array($result))){
				$entry["structure"] = $this->fixQuotes($r["fb_xml_structure"]);
				$entry["label"] = "Copy of ".$this->fixQuotes($r["fb_label"]);
				$entry["action"] = $r["fb_action"];
				$entry["created"] = $this->libertasGetDate("Y/m/d H:i:s");
				$entry["creator"] = $_SESSION["SESSION_USER_IDENTIFIER"];
				$entry["url"] = $r["fb_url"];
				$entry["method"] = ($r["fb_method"] == "get") ? "get" : "post";
				$entry["confirm"] = $this->fixQuotes($r["fb_confirm_screen"]);
				$entry["submit_button_label"] = $this->fixQuotes($r["fb_submit_button_label"]);
				$entry["emailscreen"] = $this->fixQuotes($r["fb_emailscreen"]);

			}
			$entry["required"] = Array();
			$sql ="select * from form_builder_required_fields where fbr_form = $identifier and fbr_client = $this->client_identifier";
			if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r=$this->call_command("DB_FETCH_ARRAY",Array($result))){
				$entry["required"][count($entry["required"])] = $r["fbr_index"];
			}
			$entry["groups"] = Array();
			$sql ="select * from form_builder_group_fields where fbg_form = $identifier and fbg_client = $this->client_identifier";
			if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r=$this->call_command("DB_FETCH_ARRAY",Array($result))){
				$entry["groups"][count($entry["groups"])] = $r["fbg_index"];
			}
			$entry["field_types"] = Array();
			$sql ="select * from form_builder_field_types where fbft_form = $identifier and fbft_client = $this->client_identifier";
			if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r=$this->call_command("DB_FETCH_ARRAY",Array($result))){
				$entry["field_types"][count($entry["field_types"])] = Array($this->fixQuotes($r["fbft_field"]), $this->fixQuotes($r["fbft_type"]));
			}
			$entry["emails"] = Array();
			$sql ="select * from form_builder_emails where fbe_form = $identifier and fbe_client = $this->client_identifier";
			if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r=$this->call_command("DB_FETCH_ARRAY",Array($result))){
				$entry["emails"][count($entry["emails"])] = Array($r["fbe_default"], $this->fixQuotes($r["fbe_email"]), $this->fixQuotes($r["fbe_subject"]));
			}
			/*
				we have now extracted all of the infromation that defines the structure of the form now add to database.
			*/
			$sql = "insert into form_builder_structure (
				fb_client, fb_xml_structure, fb_label, fb_action, fb_created, fb_creator, fb_url, fb_method, fb_confirm_screen, fb_emailscreen, fb_submit_button_label
			) values (
				$this->client_identifier, '".$entry["structure"]."', '".$entry["label"]."', '".$entry["action"]."', '".$entry["created"]."',
				'".$entry["creator"]."', '".$entry["url"]."', '".$entry["method"]."', '".$entry["confirm"]."', '".$entry["emailscreen"]."', '".$entry["submit_button_label"]."'
			)";
			if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "select * from form_builder_structure where fb_client = $this->client_identifier and fb_created = '".$entry["created"]."' and fb_xml_structure = '".$entry["structure"]."'";
			if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r=$this->call_command("DB_FETCH_ARRAY",Array($result))){
				$new_id = $r["fb_identifier"];
			}
			for($index=0;$index<count($entry["required"]);$index++){
				$sql = "insert into form_builder_required_fields (
					fbr_form, fbr_client, fbr_index
				) values (
					$new_id, $this->client_identifier, ".$entry["required"][$index]."
				)";
				if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
				$this->call_command("DB_QUERY",Array($sql));
			}
			for($index=0;$index<count($entry["groups"]);$index++){
				$sql = "insert into form_builder_group_fields (
					fbg_form, fbg_client, fbg_index
				) values (
					$new_id, $this->client_identifier, ".$entry["groups"][$index]."
				)";
				if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
				$this->call_command("DB_QUERY",Array($sql));
			}
			for($index=0;$index<count($entry["field_types"]);$index++){
				$sql = "insert into form_builder_field_types (
					fbft_form, fbft_client, fbft_field, fbft_type
				) values (
					$new_id, $this->client_identifier, '".$entry["field_types"][$index][0]."', '".$entry["field_types"][$index][1]."'
				)";
				if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
				$this->call_command("DB_QUERY",Array($sql));
			}
			for($index=0;$index<count($entry["emails"]);$index++){
				$sql = "insert into form_builder_emails (
					fbe_form, fbe_client, fbe_default, fbe_email, fbe_subject
				) values (
					$new_id, this->client_identifier, ".$entry["emails"][$index][0].", '".$entry["emails"][$index][1]."', '".$entry["emails"][$index][2]."'
				)";
				if ($debug) {print "<p>".__LINE__."</p>\n<p>$sql</p>\n\n";}
				$this->call_command("DB_QUERY",Array($sql));
			}
			$this->cache_form($new_id);
		}
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=SFORM_BUILDER_LIST"));
	}
	
	function fixQuotes($str){
		return join("''",split("'",$str));
	}
	
	function generate_report($parameters){
		
		$identifier = $this->check_parameters($parameters,"identifier", -1);
		$date_cond 	= $this->display_date_condition($parameters);
		
		$sql = "select 	fbd_filled_in_by, 
					fbd_submitted,
					fbf_field,
					fbf_link,
					fbf_value,
					fbf_mime_type, 
					fbf_value_group
				from form_builder_data 
					inner join form_builder_fields on 
						fbd_identifier = fbf_link 
					where 
						fbd_client = $this->client_identifier and fbd_form = $identifier $date_cond order by fbd_identifier, fbf_field";
		//print $sql;
		$result = $this->call_command("DB_QUERY",Array($sql));
		
		$out = "";
		$prev=-1;
		while ($r=$this->call_command("DB_FETCH_ARRAY",Array($result))){
			if ($out!="" && $r["fbf_link"]!=$prev){
				$out .= "\n";
			}  else {
				if ($out!=""){
					$out .="\t";
				}
			}
			$out .= $r["fbf_value_group"];
			$prev= $r["fbf_link"];
		}
		header("Pragma: public");
		header("Expires: 0"); // set expiration time
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	// browser must download file from server instead of cache
	
	// force download dialog
		header("Content-Type: application/force-download");
//		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		header("Content-Type: application/excel");
	// use the Content-Disposition header to supply a recommended filename and 
	// force the browser to display the save dialog. 
		header("Content-Disposition: filename=report_".Date("Ymd_His").".tab;");
	
//	The Content-transfer-encoding header should be binary, since the file will be read 
//	directly from the disk and the raw bytes passed to the downloading computer.
//	The Content-length header is useful to set for downloads. The browser will be able to 
//	show a progress meter as a file downloads. The content-lenght can be determines by 
//	filesize function returns the size of a file. 
		header("Content-Transfer-Encoding: text");
		header("Content-Length: ".strlen($out));
		print $out;
		$this->exitprogram();
	}
	
	function generate_report_filter($parameters){
		$str  = "<module name='$this->module_name' display='form'>";
		$str .= $this->display_form($parameters);
		//$str .= "<input label='Start Date' type='date_time' name='start_date'/>";
		//$str .= "<input label='End Date' type='date_time' name='end_date'/>";
		/*$str .= "<select name='start_date_day'>";
		for($i =1 ; $i<32;$i++){
			$str .= " <option";
			if ($i == $start_date_day){
				$str .= " checked='true'";
			}
			$str .= ">$i</option>";
		}
		$str .= "</select>";
		$str .= "<select name='start_date_month'>";
		for($i =0 ; $i<12;$i++){
			$str .= " <option value='".($i+1)."'";
			if ($i == $start_date_month){
				$str .= " checked='true'";
			}
			$str .= ">".$month[$i]."</option>";
		}
		$str .= "</select>";
		$str .= "<select name='start_date_year'>";
		for($i =2004 ; $i < date("Y")+1; $i++){
			$str .= " <option";
			if ($i == $start_date_year){
				$str .= " checked='true'";
			}
			$str .= ">$i</option>";
		}
		$str .= "</select>";
		$str .= "<text><![CDATA[End]]></text><select name='end_date_day'>";
		for($i =1 ; $i<32;$i++){
			$str .= " <option";
			if ($i == $start_date_day){
				$str .= " checked='true'";
			}
			$str .= ">$i</option>";
		}
		$str .= "</select>";
		$str .= "<select name='end_date_month'>";
		for($i =0 ; $i<12;$i++){
			$str .= " <option value='".($i+1)."'";
			if ($i == $start_date_month){
				$str .= " checked='true'";
			}
			$str .= ">".$month[$i]."</option>";
		}
		$str .= "</select>";
		$str .= "<select name='end_date_year'>";
		for($i =2004 ; $i <= date("Y")+1; $i++){
			$str .= " <option";
			if ($i == $start_date_year){
				$str .= " checked='true'";
			}
			$str .= ">$i</option>";
		}
		$str .= "</select>";*/
		$str .= "</module>";
 		return $str;	
	}
	
	function display_form($parameters){
		$s_filter_year		=$this->check_parameters($parameters, "s_filter_year");
		$s_filter_month		=$this->check_parameters($parameters, "s_filter_month");
		$s_filter_day		=$this->check_parameters($parameters, "s_filter_day");
		$s_filter_all_months	=$this->check_parameters($parameters, "s_filter_all_months","YES");
		$s_filter_all_days	=$this->check_parameters($parameters, "s_filter_all_days","YES");
		$s_filter_no_days	=$this->check_parameters($parameters, "s_filter_no_days","1");
		$e_filter_year		=$this->check_parameters($parameters, "e_filter_year",Date("Y"));
		$e_filter_month		=$this->check_parameters($parameters, "e_filter_month",Date("m"));
		$e_filter_day		=$this->check_parameters($parameters, "e_filter_day",Date("d"));
		$e_filter_all_months	=$this->check_parameters($parameters, "e_filter_all_months","YES");
		$e_filter_all_days	=$this->check_parameters($parameters, "e_filter_all_days","YES");
		$e_filter_no_days	=$this->check_parameters($parameters, "e_filter_no_days","1");
		$identifier			=$this->check_parameters($parameters, "identifier","");
		
		$cmd 				="SFORM_REPORT";
		$list_results		=Array();
		$months 			=Array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);
		$max_year = date("Y");
	//	////print $max_year;
		$a = Array("s","e");
		$label = Array("Start Filtering","End Filtering");
		$form = "
		<form method='get' label='".LOCALE_STATS_SELECT_TIME_FRAME."' name='sform_date_filter'>
			<input type='hidden' name='command' value='$cmd'/>
			<input type='hidden' name='identifier' value='$identifier'/>";
		for($i=0;$i<2;$i++){	
			$form .="<text><![CDATA[".$label[$i]."]]></text>";
			if ($max_year>2003){
				$form .="<select label='".LOCALE_STATS_SELECT_YEAR."' name='".$a[$i]."_filter_year'>";
				for ($y=2003;$y<=$max_year;$y++){
					$form .="<option value='$y'";
					if (eval("\$answer = (\$".$a[$i]."_filter_year==\$y);")){
						$form .=" selected='true'";
					}
					$form .=">$y</option>";
				}
				$form .= "</select>";
			} else {
				$form .= "<input type='hidden' name='".$a[$i]."_filter_year' value='2003'/>";
				
			}
			$form .= "<select label='".LOCALE_STATS_SELECT_MONTH."' name='".$a[$i]."_filter_month'>";
			if(eval("\$answer = (\$".$a[$i]."_filter_all_months=='YES');")){
				$form .= "<option value='-1'>All Months</option>";
			}
			for ($m=1;$m<=12;$m++){
				$form .="<option value='$m'";
				if (eval("\$answer = (\$".$a[$i]."_filter_month==\$m);")){
					$form .=" selected='true'";
				}
				$form .=">".$months[$m-1]."</option>";
			}
			$form .= "</select>";
			eval("\$answer = (\$".$a[$i]."_filter_no_days==1);");
			if($answer){
				$form .= "<select label='".LOCALE_STATS_SELECT_DAY."' name='".$a[$i]."_filter_day'>";
				if($_filter_all_days=="YES"){
					$form .= "<option value='-1'>All days</option>";
				}
				for ($d=1;$d<=31;$d++){
					$form .="<option value='$d'";
					if (eval("\$answer = (\$".$a[$i]."_filter_day==\$d);")){
						$form .=" selected='true'";
					}
					$form .=">$d</option>";
				}
				$form .= "</select>";
			}
		}
		$form .= "<input type='submit' iconify='SEARCH'/>
		</form>";
//		////print $form;
		return $form;
	}
	
	function display_date_condition($parameters){
		$s_filter_year		=$this->check_parameters($parameters, "s_filter_year");
		$s_filter_month		=$this->check_parameters($parameters, "s_filter_month");
		$s_filter_day		=$this->check_parameters($parameters, "s_filter_day");
		
		$e_filter_year		=$this->check_parameters($parameters, "e_filter_year");
		$e_filter_month		=$this->check_parameters($parameters, "e_filter_month");
		$e_filter_day		=$this->check_parameters($parameters, "e_filter_day");
		
		$sql=" and fbd_submitted between '$s_filter_year/$s_filter_month/$s_filter_day' and '$e_filter_year/$e_filter_month/$e_filter_day'";
		/*$sql="";
//		////print "[$_filter_year,$_filter_month,$_filter_day]";
		if (($s_filter_year!='') && ($s_filter_year!='-1')){
			$sql .=" and year(fbd_submitted) >= $s_filter_year ";
		}
		if (($s_filter_month!='') && ($s_filter_month!='-1')){
			$sql .=" and month(fbd_submitted) >= $s_filter_month ";
		}
		if (($s_filter_day!='') && ($s_filter_day!='-1')){
			$sql .=" and dayofmonth(fbd_submitted) >= $s_filter_day ";
		}
		if (($e_filter_year!='') && ($e_filter_year!='-1')){
			$sql .=" and year(fbd_submitted) <= $e_filter_year ";
		}
		if (($e_filter_month!='') && ($e_filter_month!='-1')){
			$sql .=" and month(fbd_submitted) <= $e_filter_month ";
		}
		if (($e_filter_day!='') && ($e_filter_day!='-1')){
			$sql .=" and dayofmonth(fbd_submitted) <= $e_filter_day ";
		}*/
		return $sql;
	}
	
	function display_prefs($parameters){
		$length = count($this->preferences);
		$str  = "<module name='$this->module_name' display='form'>
		<form method='get' label='".LOCALE_SFORM_PREFS."' name='sform_prefs'>
			<input type='hidden' name='command' value='SFORM_SETTINGS_SAVE'/>";
		$sql = "select system_preference_name,  system_preference_value from system_preferences where system_preference_client = $this->client_identifier and system_preference_module='".$this->webContainer."'";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			for ($i=0;$i<$length;$i++){
				if ($this->preferences[$i][0]==$r["system_preference_name"]){
					$this->preferences[$i][-1] = $r["system_preference_value"];
				}
        	}
        }
        $this->call_command("DB_FREE",Array($result));

		for ($i=0;$i<$length;$i++){
			$val  = $this->check_parameters($this->preferences[$i],-1,$this->preferences[$i][2]);
			if ($this->preferences[$i][3]=="TEXT"){
				$str .= "<input type='text' label='".$this->preferences[$i][1]."' name='".$this->preferences[$i][0]."' value='$val'/>";
			} else {
				$list = split(":",$this->preferences[$i][3]);
				$max_list = count($list);
				if ($max_list>2){
					$str .="<select label='".$this->preferences[$i][1]."' name='".$this->preferences[$i][0]."'>";
				} else {
					$str .="<radio type='horizontal' label='".$this->preferences[$i][1]."' name='".$this->preferences[$i][0]."'>";
				}
				for ($y=0;$y<$max_list;$y++){
					$str .="<option value='".$list[$y]."'";
					if ($list[$y]==$val){
						$str .=" selected='true'";
					}
					$str .=">".$list[$y]."</option>";
				}
				if ($max_list>2){
					$str .= "</select>";
				} else {
					$str .= "</radio>";
				}
			}
		}
		$str .= "<input type='submit' iconify='SAVE' value=\"SAVE_DATA\"/>
		</form>
		</module>";		
		return $str;
	}
	function display_prefs_save($parameters){
		$length = count($this->preferences);
		$sql="delete from system_preferences where system_preference_client = $this->client_identifier and system_preference_module='".$this->webContainer."'";
		$this->call_command("DB_QUERY",Array($sql));
		for ($i=0;$i<$length;$i++){
			$val = $this->check_parameters($parameters,$this->preferences[$i][0],$this->preferences[$i][2]);
			$sql ="insert into system_preferences (system_preference_name, system_preference_label, system_preference_client, system_preference_value, system_preference_options, system_preference_module) 
				values 
				('".$this->preferences[$i][0]."', '".$this->preferences[$i][1]."', $this->client_identifier, '".$val."', '".$this->preferences[$i][3]."', '".$this->preferences[$i][4]."');";
			$this->call_command("DB_QUERY",Array($sql));
		}
			$str  = "<module name='$this->module_name' display='text'>
		<text><![CDATA[Thanks your Form Preferences have been updated]]></text>
		</module>";		
		return $str;
	}
	
	function load_prefs(){
		$sql = "select * from system_preferences where system_preference_client = $this->client_identifier and system_preference_module='".$this->webContainer."'";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
			$this->call_command("DB_FREE",Array($result));
			$this->display_prefs_save(Array());
			$sql = "select system_preference_name,  from system_preferences where system_preference_client = $this->client_identifier and system_preference_module='".$this->webContainer."'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
		}
		$data = Array();
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$data[$r["system_preference_name"]] = $r["system_preference_value"];
        }
        $this->call_command("DB_FREE",Array($result));
		return $data;
	}
}
?>