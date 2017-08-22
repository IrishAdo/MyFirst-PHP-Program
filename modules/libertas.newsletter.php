<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.users.php
* @date 09 Oct 2002
*/
class newsletter extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_name 				= "newsletter";
	var $module_name_label			= "Newsletter Management Module";	// label describing the module 
	var $module_admin 				= "1";
	var $admin_access 				= 0;
	var $module_debug 				= false;
	var $module_label 				= "MANAGEMENT_NEWSLETTER";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.9 $';
	var $module_creation 			= "06/11/2002";
	var $module_command 			= "NEWSLETTER_";

	var $author_access				= 0;
	var $page_author_access			= 0;
	var $publisher_access			= 0;
	var $list_access				= 0;
	// all commands specifically for this module will start with this token
	var $display_options			= array(
		array (0, FILTER_ORDER_NAME_A_Z		,"newsletter_data.newsletter_label asc"),
		array (1, FILTER_ORDER_NAME_Z_A		,"newsletter_data.newsletter_label Desc")
	);
	var $available_forms			= array("NEWSLETTER_SIGN_UP");
//NEWSLETTER_SIGN_UP_NOW	
	var $module_admin_options 		= array();
	
	var $module_display_options 	= array(
		array("NEWSLETTER_SUBSCRIPTION",	LOCALE_DISPLAY_NEWSLETTER)
	);
		
	//var $WebObjects				 	= array(
	//	array(2,"Display The Newsletter Subscription Form","NEWSLETTER_SUBSCRIPTION",0,0)
	//);
	
	
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
			/**
			* generic functions
			*/
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
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($this->admin_access==1){
				if ($user_command==$this->module_command."LIST"){
					return $this->newsletter_list($parameter_list);
				}
				if ($user_command==$this->module_command."LIST_ISSUES"){
					return $this->newsletter_issue_list($parameter_list);
				}
				/**
				* functions that are not generic
				*/ 
				
				
				if ($user_command==$this->module_command."DISPLAY_SUBSCRIPTION_FORM"){
					return $this->subscription_form($parameter_list);
				}
				if ($user_command==$this->module_command."ADD_TO_USER_PROFILE"){
					return $this->add_to_user_profile($parameter_list);
				}
				if ($user_command==$this->module_command."REGISTER"){
					return $this->newsletter_register($parameter_list);
				}
				if ($user_command==$this->module_command."DO_SUBSCRIBE"){
					return $this->newsletter_do_subscribe($parameter_list);
				}								
				if (($user_command==$this->module_command."ADD") || ($user_command==$this->module_command."EDIT")){
					return $this->newsletter_editor($parameter_list);
				}
				if (($user_command==$this->module_command."ADD_ISSUE") || ($user_command==$this->module_command."EDIT_ISSUE")){
					return $this->newsletter_issue_editor($parameter_list);
				}
				if (($user_command==$this->module_command."REMOVE_ISSUE")){
					return $this->newsletter_remove_issue($parameter_list);
				}
				if (($user_command==$this->module_command."REMOVE")){
					return $this->newsletter_remove($parameter_list);
				}
				
				if (($user_command==$this->module_command."SAVE")){
					$this->newsletter_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_LIST"));
				}
				if (($user_command==$this->module_command."READY")){
					$this->newsletter_ready($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_LIST"));
				}
				if (($user_command==$this->module_command."SAVE_ISSUE")){
					$this->newsletter_issue_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_LIST_ISSUES&identifier=".$this->check_parameters($parameter_list,"newsletter_identifier",-1)));
				}
				if (($user_command==$this->module_command."ISSUE_READY")){
					$this->newsletter_issue_ready($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_LIST"));
				}
				if (($user_command==$this->module_command."SEND_TEST")){
					return $this->newsletter_issue_test($parameter_list);
				}
				if (($user_command==$this->module_command."SEND_TO_SUBSCRIBERS")){
					return $this->newsletter_issue_send_final($parameter_list);
				}
				if (($user_command==$this->module_command."PREVIEW")){
					return $this->newsletter_preview($parameter_list);
				}
				if (($user_command==$this->module_command."PUBLISH")){
					return $this->newsletter_publish($parameter_list);
				}
				if (($user_command==$this->module_command."SIGN_UP")){
					return $this->sign_up_now_form($parameter_list);
				}
				if (($user_command==$this->module_command."SIGN_UP_NOW_SAVE")){
					return $this->sign_up_now_form_save($parameter_list);
				}
				if ($user_command==$this->module_command."LIST_FORMS"){
					return $this->list_forms();
				}
				if (($user_command==$this->module_command."ISSUE_REJECT")){
					return $this->issue_reject($parameter_list);
				}
				if (($user_command==$this->module_command."VALIDATE")){
					return $this->verify_email($parameter_list);
				}
				if (($user_command==$this->module_command."UNSUBSCRIBE")){
					return $this->unsubscribe_email($parameter_list);
				}
				if (($user_command==$this->module_command."SEND_TO_PAGE")){
					return $this->send_to_page($parameter_list);
				}
				if (($user_command==$this->module_command."CACHE_AVAILABLE_FORM")){
					return $this->cache_available_forms($parameter_list);
				}
				if (($user_command==$this->module_command."ISSUE_AVAILABLE")){
					return $this->newsletter_issue_available($parameter_list);					
				}
				/*Modify By Ali Imran Ahmad for newsletter list*/
				
				if (($user_command==$this->module_command."SUBSCRIBER_LIST")){
					return $this->subscriber_list($parameter_list);					
				}
				if (($user_command==$this->module_command."SUBSCRIBER_REMOVE")){
					return $this->subscriber_remove($parameter_list);
				}
				if (($user_command==$this->module_command."SUBSCRIBER_ADD") || ($user_command==$this->module_command."SUBSCRIBER_EDIT")){
					return $this->subscriber_form($parameter_list);
				}
				if (($user_command==$this->module_command."SUBSCRIBER_SAVE")){
					return $this->subscriber_save($parameter_list);
				}
				if (($user_command==$this->module_command."SUBSCRIBER_SUBSCRIBE")){
					return $this->subscriber_subscribe($parameter_list);
				}
				if (($user_command==$this->module_command."SUBSCRIBER_UNSUBSCRIBE")){
					return $this->subscriber_unsubscribe($parameter_list);
				}
				
				if (($user_command==$this->module_command."SUBSCRIBER_IMPORT")){
					return $this->subscriber_import_form($parameter_list);
				}
				if (($user_command==$this->module_command."IMPORT_SAVE")){
					return $this->subscriber_importfile($parameter_list);
				}
				if (($user_command==$this->module_command."IMPORT_CONFIRMED")){
					return $this->subscriber_import_confirmed($parameter_list);
				}
				/*End of Modification by Ali Imran*/

				/* Starts Subscriber Group section Added (By Muhammad Imran) */
				if (($user_command==$this->module_command."SUBSCRIBER_GROUP_LIST")){
					return $this->subscriber_group_list($parameter_list);					
				}
				if (($user_command==$this->module_command."SUBSCRIBER_GROUP_REMOVE")){
					return $this->subscriber_group_remove($parameter_list);
				}
				if (($user_command==$this->module_command."SUBSCRIBER_GROUP_ADD") || ($user_command==$this->module_command."SUBSCRIBER_GROUP_EDIT")){
					return $this->subscriber_group_form($parameter_list);
				}
				if (($user_command==$this->module_command."SUBSCRIBER_GROUP_SAVE")){
					return $this->subscriber_group_save($parameter_list);
				}
				if (($user_command==$this->module_command."SELECT_SUBSCRIBERS_GROUP")){
					return $this->select_subscriber_group($parameter_list);
				}
				if (($user_command==$this->module_command."SET_DEFAULT_GROUP")){
					return $this->set_default_group($parameter_list);					
				}
				if (($user_command==$this->module_command."SET_DEFAULT_GROUP_SAVE")){
					return $this->set_default_group_save($parameter_list);					
				}
				
				/* Ends Subscriber Group section Added (By Muhammad Imran) */
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	
	/**
	* Initialise function
	*
	* This function will initialise some variables for this modules functions to use.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise",__LINE__,""));
		}
		$this->load_locale("newsletter_admin");
		
		$this->editor_configurations = Array(
			"ENTRY_HTML_DESCRIPTION" => Array(
				"status"=>"unlocked", 
				"locked_to" => "", 
				"identifier"=>0
			),
			"ENTRY_DESCRIPTION" => Array(
				"status"=>"unlocked", 
				"locked_to" => "", 
				"identifier"=>0
			)
		);
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		$this->super_user_access		= 0;
		$this->author_access			= 0;
		$this->publisher_access			= 0;
		$this->list_access				= 0;
		$this->module_admin_access		= 0;

		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (
					("NEWSLETTER_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("NEWSLETTER_AUTHOR"==$access[$index])
				){
					$this->author_access=1;
				}
				if (
					("PAGE_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("PAGE_AUTHOR"==$access[$index])
				){
					$this->page_author_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("NEWSLETTER_ALL"==$access[$index]) ||
					("NEWSLETTER_PUBLISHER"==$access[$index])
				){
					$this->publisher_access=1;
				}
			}
		}
		if (($this->publisher_access || $this->author_access ) && (($this->parent->module_type=="admin")||($this->parent->module_type=="preview"))){
			$this->list_access=1;
			$this->module_admin_access=1;
		}

		if ($this->parent->module_type=="web"){
			$this->admin_access = 0;
		}else{
			$this->admin_access = 1;
		}
		$this->module_admin_options[count($this->module_admin_options)] = array("NEWSLETTER_LIST", "MANAGE_NEWSLETTER","");
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
		* Table structure for table 'contact_data'
		*/
		$fields = array(
			array("newsletter_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("newsletter_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("newsletter_label"		,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("newsletter_from_label"	,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("newsletter_from_email"	,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("newsletter_description"	,"text"				,"NOT NULL"	,"default ''"),
			array("newsletter_date_created"	,"datetime"			,"NOT NULL"	,"default ''"),
			array("newsletter_status"		,"small integer"	,"NOT NULL"	,"default '0'"),
			array("newsletter_location"		,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary="newsletter_identifier";
		$tables[count($tables)] = array("newsletter_data", $fields, $primary);
		/**
		* Table structure for table 'newletter_archive' a record of all sent newletters
		*/
		$fields = array(
			array("newsarchive_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("newsarchive_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("newsarchive_subject"		,"varchar(40)"		,"NULL"		,"default ''"),
			array("newsarchive_plaintext"	,"text"				,"NOT NULL"	,""),
			array("newsarchive_rtftext"		,"text"				,"NOT NULL"	,"default ''"),
			array("newsarchive_status"		,"small integer"	,"NOT NULL"	,"default '0'"),
			array("newsarchive_date"		,"datetime"			,"NOT NULL"	,"default ''")
		);
		$primary="newsarchive_identifier";
		$tables[count($tables)] = array("newsletter_archive", $fields, $primary);
		
		/**
		* Table structure for table 'newsletter_subscription' a list of people that have subscribed
		* to a particular mailing list
		*/
		$fields = array(
			array("subscriber_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("subscriber_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("subscriber_email"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("subscriber_newsletter"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("subscriber_verified"		,"small integer"	,"NOT NULL"	,"default '0'")
		);
		$primary = "subscriber_identifier";
		$tables[count($tables)] = array("newsletter_subscription", $fields, $primary);

		return $tables;
	}
	
	function subscription_form($parameters){
		$current_menu_location = $this->check_parameters($parameters,"current_menu_location");	

		$out ="<module name=\"newsletter\" display=\"newsletterform\">";
		$out .="<form name=\"newsletter_subscription_form\" method=\"post\" label=\"Newsletter\">";
		$out .="<input type=\"hidden\" name=\"command\" value=\"NEWSLETTER_DO_SUBSCRIBE\">COMMAND</input>";
		
		$sql		= "select * from newsletter_data where newsletter_client=$this->client_identifier and newsletter_status = 2 and 
		newsletter_location = '$current_menu_location' 
		OR newsletter_location like '%|$current_menu_location|%'
		OR newsletter_location like '%$current_menu_location|%'
		OR newsletter_location like '%|$current_menu_location%'
		"; //live
		$result		= $this->call_command("DB_QUERY",array($sql));
		$num_rows 	= $r = $this->call_command("DB_NUM_ROWS",array($result));
		if ($num_rows > 1){
			$out .= "<checkboxes name=\"newsletter_sign_up\" label=\"".LOCALE_SIGNUP_TO_A_NEWSLETTER."\" type='vertical'><options>";
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
//				$out .= "<option value='".$r["newsletter_identifier"]."' ><![CDATA[".$this->check_parameters($r,"newsletter_label")."<br/>".$this->check_parameters($r,"newsletter_description")."]]></option>";
				$out .= "<option value='".$r["newsletter_identifier"]."' ><![CDATA[".$this->strip_tidy($this->check_parameters($r,"newsletter_label"))."]]></option>";
			}
			$out .= "</options></checkboxes>";
		}
		else {
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$out .="<input type=\"hidden\" name=\"newsletter_sign_up\" value=\"".$r["newsletter_identifier"]."\">NEWLETTER</input>";
		}		
		
		$out .="<seperator_row><seperator>";
		$out .="<input type=\"text\" label=\"Your name\" name=\"name\" size=\"40\" required=\"true\" />";
		$out .="<input type=\"text\" label=\"Your email\" name=\"email\" size=\"40\" required=\"true\" />";
		$out .="<input type=\"text\" label=\"Company name\" name=\"company\" size=\"40\" />";
		$out .= "	<radio label='".LOCALE_EMAIL_FORMATTING."' name='email_rtf'><option value='0'><![CDATA[LOCALE_PLAINTEXT]]></option><option value='1'><![CDATA[LOCALE_RICHTEXT]]></option></radio>";		
		$out .="</seperator></seperator_row>";		
		$out .="<input iconify=\"SAVE\" type=\"submit\" value=\"sign up\" />";		

		$out .="</form>";
		$out .="</module>";
		$command 		= $this->check_parameters($parameters,"command");
		if ($command != "NEWSLETTER_DO_SUBSCRIBE")
			return $out;
	}

	function newsletter_list($parameters){
		$_SESSION["SESSION_USER_LANGUAGE"]="en";

		$where = "";
		$join="";
		$order_by="";
		$status =array();

		$access_type			= $this->check_parameters($_SESSION,"access_type","AUTHOR_ACCESS");
		$status_filter 			= $this->check_parameters($parameters,"status_filter",-2);
		$lang_of_choice 		= $this->check_parameters($_SESSION,"SESSION_USER_LANGUAGE","en");
		$group_filter 			= $this->check_parameters($parameters,"group_filter",-1);
		$menu_location 			= $this->check_parameters($parameters,"menu_location",-1);
		$order_filter 			= $this->check_parameters($parameters,"order_filter",0);
		$join 					= "";
		$access_levels			= 0;
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
		  if ($status_filter==-2){
			if ($this->author_access==1){
				$status_filter="1";
				$access_levels++;
			}
			if ($this->publisher_access==1){
				$status_filter="3";
				$access_levels++;
			}
		  }
		}
		if (($status_filter==-2) || ($access_levels>1)){
			$status_filter="-1";
		}
		$parameters["status_filter"]= $status_filter;		
		$order_by .= "order by ".$this->display_options[$order_filter][2];
		$lang_of_choice = "en";
		if (empty($filter_newsletterlation)){
			$newsletterlation = $lang_of_choice;
		} else {
			$newsletterlation = $filter_newsletterlation;
		}
		
		$sql = "
				select 
					*
				from newsletter_data 
				where 
					newsletter_data.newsletter_client=$this->client_identifier
				$order_by
		";
		
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$variables = Array();
		$variables["FILTER"]			= "";//$this->filter($parameters,"NEWSLETTER_LIST")."<menus selected=\"$menu_location\"/>";
		$variables["MENU_LINKS"]		= "?command=NEWLETTER_LIST&page=1&search=1&menu_location=";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		
		if($this->author_access==1){
			$variables["PAGE_BUTTONS"] = Array(Array("ADD","NEWSLETTER_ADD",ADD_NEW,""));
		}

		if ($this->module_admin_access==1 || $search==1){
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($result){
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
				
				$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"LOCALE_NEWSLETTER_FORM"));
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
					$counter++;
					$i = count($variables["RESULT_ENTRIES"]);
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=> $r["newsletter_identifier"],
						"ENTRY_BUTTONS" => Array(),
						"attributes"	=> Array()
					);
					
					$checkin=false;
					$variables["RESULT_ENTRIES"][$i]["attributes"]	=	Array(
						Array(NEWSLETTER_TITLE, $r["newsletter_label"],"TITLE","EDIT_DOCUMENT"),
						Array(LOCALE_EDIT,"?command=NEWSLETTER_EDIT&identifier=".$r["newsletter_identifier"]."","NO","NO"),
						Array(ENTRY_STATUS,$this->get_constant("LOCALE_NEWSLETTER_STATUS_TYPE_".$r["newsletter_status"]))//,
//						$r["newsletter_date_created"]
					);
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=		Array("PREVIEW","NEWSLETTER_PREVIEW",LOCALE_PREVIEW);
							
					if($this->author_access==1 ){
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT","NEWSLETTER_EDIT",EDIT_EXISTING);						
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE","NEWSLETTER_REMOVE",REMOVE_EXISTING);						
						if ($r["newsletter_status"]==0){
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("NEXT_STAGE","NEWSLETTER_READY",SEND_TO_PUBLISHER);	
						}
						if ($r["newsletter_status"]==2){
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("ISSUES","NEWSLETTER_LIST_ISSUES",LOCALE_MANAGE_ISSUES);
						}
					}
					if ($this->publisher_access){
						if ($r["newsletter_status"]==1){
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("PUBLISH","NEWSLETTER_PUBLISH",LOCALE_SAVE_DATA_SITE);
						}
					}
					
					/* Modify By Ali Imran Ahmad for View Subscribers*/
					if ($r["newsletter_status"]==2){
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("SUBSCRIBERS","NEWSLETTER_SUBSCRIBER_LIST",'Subscribers');
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("IMPORT","NEWSLETTER_SUBSCRIBER_IMPORT",'Import');
					}
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("SUBSCRIBER_GROUPS","NEWSLETTER_SUBSCRIBER_GROUP_LIST",'Subscriber Groups');
					/* End modification of Ali Imran Ahmad  */

//					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("MEMBERS","NEWSLETTER_MEMBERS","LOCALE_MEMBERS");
				}
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}
	
	function newsletter_issue_list($parameters){

		$where = "";
		$join="";
		$order_by="";
		$status =array();

		$access_type			= $this->check_parameters($_SESSION,"access_type","AUTHOR_ACCESS");
		$status_filter 			= $this->check_parameters($parameters,"status_filter",-2);
		$lang_of_choice 		= $this->check_parameters($_SESSION,"SESSION_USER_LANGUAGE","en");
		$group_filter 			= $this->check_parameters($parameters,"group_filter",-1);
		$menu_location 			= $this->check_parameters($parameters,"menu_location",-1);
		$order_filter 			= $this->check_parameters($parameters,"order_filter",0);
		$identifier 			= $this->check_parameters($parameters,"identifier",0);
		$join 					= "";
		$access_levels			= 0;
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
		  if ($status_filter==-2){
			if ($this->author_access==1){
				$status_filter="1";
				$access_levels++;
			}
			if ($this->publisher_access==1){
				$status_filter="3";
				$access_levels++;
			}
		  }
		}
		if (($status_filter==-2) || ($access_levels>1)){
			$status_filter="-1";
		}
		$parameters["status_filter"]= $status_filter;		
		$order_by .= "order by ".$this->display_options[$order_filter][2];
		$lang_of_choice = "en";
		if (empty($filter_newsletterlation)){
			$newsletterlation = $lang_of_choice;
		} else {
			$newsletterlation = $filter_newsletterlation;
		}
		
		$sql = "
				select 
					*
				from newsletter_archive 
				where 
					newsletter_archive.newsarchive_client=$this->client_identifier and 
					newsletter_archive.newsarchive_newsletter=$identifier
				";
		
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$variables = Array();
		$variables["FILTER"]			= "";//$this->filter($parameters,"NEWSLETTER_LIST")."<menus selected=\"$menu_location\"/>";
		$variables["MENU_LINKS"]		= "?command=NEWLETTER_LIST&page=1&search=1&menu_location=";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		
		if ($this->module_admin_access==1 || $search==1){
			$result = $this->call_command("DB_QUERY",array($sql));
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "<module name=\"newsletter\" display=\"form\"><text><![CDATA[LOCALE_SORRY_SQL_ERROR]]></text></module>";
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
				if($this->author_access==1){
					$variables["PAGE_BUTTONS"] = Array(
						Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME),
//						Array("CANCEL","NEWSLETTER_LIST",LOCALE_CANCEL,""),
						Array("ADD","NEWSLETTER_ADD_ISSUE",ADD_NEW,"newsletter=$identifier")
					);
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
				$user_identifier 			 = $_SESSION["SESSION_USER_IDENTIFIER"];
				$variables["END_PAGE"]		 = $end_page;
				$variables["ENTRY_BUTTONS"]  = Array();
				$variables["CONDITION"]		 = Array();
				$variables["RESULT_ENTRIES"] = Array();
				$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"LOCALE_NEWSLETTER_FORM"));
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
					$counter++;
					$i = count($variables["RESULT_ENTRIES"]);
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=> $r["newsarchive_identifier"],
						"ENTRY_BUTTONS" => Array(),
						"attributes"	=> Array()
					);
					
					$checkin=false;
					$variables["RESULT_ENTRIES"][$i]["attributes"]	=	Array(
						Array(NEWSLETTER_TITLE, $r["newsarchive_subject"],"TITLE","EDIT_DOCUMENT",NEWSLETTER_TITLE),
						Array("EDIT_DOCUMENT","?command=NEWSLETTER_EDIT&identifier=".$r["newsarchive_identifier"]."","NO","NO"),
						Array(ENTRY_STATUS,$this->get_constant("LOCALE_ISSUE_STATUS_TYPE_".$r["newsarchive_status"]))
					);
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("NEXT_STAGE","NEWSLETTER_SEND_TEST&amp;newsletter=".$identifier,RECIEVE_TEST);	
					if($this->author_access==1 ){
						if ($r["newsarchive_status"]==0){
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT","NEWSLETTER_EDIT_ISSUE&amp;newsletter=".$identifier,EDIT_EXISTING);
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE","NEWSLETTER_REMOVE_ISSUE&amp;newsletter=".$identifier,REMOVE_EXISTING);
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("NEXT_STAGE","NEWSLETTER_ISSUE_READY&amp;newsletter=".$identifier,REQUEST_APPROVAL);	
						}
					}
					if ($this->publisher_access){
						if ($r["newsarchive_status"]==1){
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REJECT","NEWSLETTER_ISSUE_REJECT&amp;newsletter=".$identifier,LOCALE_REJECT);
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("PUBLISH","NEWSLETTER_SELECT_SUBSCRIBERS_GROUP&amp;newsletter=".$identifier,SEND_EMAIL_TO_SUBSCRIBERS);
/*							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("PUBLISH","NEWSLETTER_SEND_TO_SUBSCRIBERS&amp;newsletter=".$identifier,SEND_EMAIL_TO_SUBSCRIBERS);
*/						}
						if ($r["newsarchive_status"]==2){
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REJECT","NEWSLETTER_ISSUE_REJECT&amp;newsletter=".$identifier,LOCALE_SEND_TO_AUTHOR);
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("AVAILABLE","NEWSLETTER_ISSUE_AVAILABLE&amp;newsletter=".$identifier,LOCALE_SEND_AVAILABLE);
						}
					}
					if ($this->page_author_access){
						if ($r["newsarchive_status"]==2){
							//$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("PUBLISH","NEWSLETTER_SEND_TO_PAGE&amp;newsletter=".$identifier,LOCALE_SEND_TO_PAGE);
						}
					}
				}
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}

	function add_to_user_profile($parameters){
		$sign_up 	= $this->check_parameters($parameters,"newsletter_sign_up",Array());
		$user_id	= $this->check_parameters($parameters,"user_identifier",0);
		$max_len 	= count($sign_up);
		if ($user_id>0 && $max_len ==0){
			$sql = "select 
						newsletter_subscription.subscriber_newsletter
					from user_info 
						inner join contact_data on contact_data.contact_user = user_info.user_identifier
						inner join email_addresses on email_addresses.email_contact = contact_data.contact_identifier
						inner join newsletter_subscription on newsletter_subscription.subscriber_email = email_addresses.email_identifier
					where 
						user_identifier=$user_id and user_client=$this->client_identifier";
			$result		= $this->call_command("DB_QUERY",array($sql));
			$num_rows 	= $r = $this->call_command("DB_NUM_ROWS",array($result));
			if ($num_rows > 0){
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$sign_up[count($sign_up)] = $r["subscriber_newsletter"];
				}
			}
		}
		$max_len 	= count($sign_up);
		$sql		= "select * from newsletter_data where newsletter_client=$this->client_identifier and newsletter_data.newsletter_status = 2"; //live
		$result		= $this->call_command("DB_QUERY",array($sql));
		$num_rows 	= $r = $this->call_command("DB_NUM_ROWS",array($result));
		if ($num_rows > 0){
			$out = "<input name=\"call_command\" value=\"NEWSLETTER_REGISTER\" type=\"hidden\"/>";
			$out .= "<checkboxes name=\"newsletter_sign_up\" label=\"".LOCALE_SIGNUP_TO_A_NEWSLETTER."\" type='vertical'><options>";
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$out .= "<option value='".$r["newsletter_identifier"]."' ";
				for ($index=0;$index<$max_len;$index++){
					if ($r["newsletter_identifier"] == $sign_up[$index]){
						$out.="selected='true'";
					}
				}
				$out .= "><![CDATA[".$this->check_parameters($r,"newsletter_label")."<br/>".$this->check_parameters($r,"newsletter_description")."]]></option>";
			}
			$out .= "</options></checkboxes>";
		}else{
			$out="";
		}
		return $out;
	}
	
	function newsletter_register($parameters){
		$email = $this->call_command("EMAIL_EXTRACT_IDENTIFIER",array("email" => $this->check_parameters($parameters,"contact_email")));
		$sign_up = $this->check_parameters($parameters,"newsletter_sign_up",Array());
		$max_len 	= count($sign_up);
		$out ="";
		$sql = "delete from newsletter_subscription where subscriber_client=$this->client_identifier and subscriber_email=$email";
		$this->call_command("DB_QUERY",array($sql));
		if ($max_len>0){
			for ($index=0;$index<$max_len;$index++){
				$sql = "insert into newsletter_subscription (subscriber_client, subscriber_email, subscriber_newsletter, subscriber_verified) values ($this->client_identifier, $email, ".$sign_up[$index].", 1)";
				$this->call_command("DB_QUERY",array($sql));
			}
		}
		return $out;
	}
	function newsletter_do_subscribe($parameters){
		$email 		= $this->check_parameters($parameters,"email");
		$name 		= $this->check_parameters($parameters,"name");
		$company 		= $this->check_parameters($parameters,"company");
		$email_rtf	= $this->check_parameters($parameters,"email_rtf",1);		
		$sign_up	= $this->check_parameters($parameters,"newsletter_sign_up",Array());
		$max_len 	= count($sign_up);

		if (!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
			$message_confirmation =	"<text><![CDATA[Email Address is not valid. Please provide a valid email address.]]></text>";						
		}
		else {
			/* Subscribe for a newsletter insertion starts ( Modified By Muhammad Imran Mirza ) */
//			$sql = "delete from newsletter_subscription where subscriber_client=$this->client_identifier and subscriber_email='$email'";
//			$this->call_command("DB_QUERY",array($sql));
			if ($max_len>1){
				for ($index=0;$index<$max_len;$index++){
					$sql = "delete from newsletter_subscription where subscriber_client=$this->client_identifier and subscriber_email='$email' and subscriber_newsletter = ".$sign_up[$index];
					$this->call_command("DB_QUERY",array($sql));

					/* Starts Get subscriber group (By Muhammad Imran 11/02/2008)*/
					$sql_group = "select subscriber_group_identifier from newsletter_subscription_group where subscriber_group_client=$this->client_identifier and subscriber_group_active=1 and subscriber_group_default=1 and subscriber_group_newsletter = ".$sign_up[$index];
					$result_group = $this->call_command("DB_QUERY",array($sql_group));
					$r_group = $this->call_command("DB_FETCH_ARRAY",array($result_group));
					$subscriber_group = $r_group['subscriber_group_identifier'];
					/* Starts Get subscriber group (By Muhammad Imran 11/02/2008)*/

					$sql = "insert into newsletter_subscription (subscriber_client, subscriber_email, subscriber_name, subscriber_company, subscriber_newsletter, subscriber_verified,subscriber_rtf,subscriber_group) values ($this->client_identifier, '$email', '$name', '$company', ".$sign_up[$index].", 1,$email_rtf,'$subscriber_group')";
					$this->call_command("DB_QUERY",array($sql));					
				}
			}
			else {
				$sign_up	= $this->check_parameters($parameters,"newsletter_sign_up",0);
				/* To fix for more than one newsletter but if check for only one was giving error 'Array' By Muhammad Imran 11/02/2008 */
				if (is_array($sign_up))
					$sign_up = $sign_up[0];
				else
					$sign_up = $sign_up;

				$sql = "delete from newsletter_subscription where subscriber_client=$this->client_identifier and subscriber_email='$email' and subscriber_newsletter = $sign_up";
				$this->call_command("DB_QUERY",array($sql));

					/* Starts Get subscriber group 11/02/2008 */
					$sql_group = "select subscriber_group_identifier from newsletter_subscription_group where subscriber_group_client=$this->client_identifier and subscriber_group_active=1 and subscriber_group_default=1 and subscriber_group_newsletter = $sign_up";
					$result_group = $this->call_command("DB_QUERY",array($sql_group));
					$r_group = $this->call_command("DB_FETCH_ARRAY",array($result_group));
					$subscriber_group = $r_group['subscriber_group_identifier'];
					/* Ends Get subscriber group 11/02/2008*/

				$sql = "insert into newsletter_subscription (subscriber_client, subscriber_email, subscriber_name, subscriber_company, subscriber_newsletter, subscriber_verified,subscriber_rtf,subscriber_group) values ($this->client_identifier, '$email', '$name', '$company', ".$sign_up.", 1,$email_rtf,'$subscriber_group')";				
				$this->call_command("DB_QUERY",array($sql));		
			}
			/* Subscribe for a newsletter insertion ends ( Modified By Muhammad Imran Mirza ) */
			$message_confirmation =	"<text><![CDATA[Thank you for subscribing to our newsletter.]]></text>";
			
			/* Starts Auto reply Email portion */
			
			$sql ="select * from newsletter_data where newsletter_identifier=$sign_up and newsletter_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$news_label		= $r["newsletter_label"];
			$description 	= $r["newsletter_description"];
			$from_email		= $r["newsletter_from_email"];
			$from_label		= $r["newsletter_from_label"];
			$menu_parent	= $r["newsletter_location"];

			$email_body["from"]		= '"'.$from_email.'" <'.$from_email.'>';
//			$email_body["subject"]	= "Thank you for subscribing to our newsletter";
			$email_body["subject"]	= $from_label;
			$email_body["to"]		= "$email";
			$email_body["format"]	= "HTML";

			$description=str_replace("{name}",$name,$description);
			$description=str_replace('src=&quot;uploads/','src=&quot;http://'.$this->parent->domain.'/uploads/',$description);

			$description	= str_replace('href=&quot;','href=&quot;http://'.$this->parent->domain.'/',$description);
			$description	= str_replace('href=&quot;http://'.$this->parent->domain.'/www.','href=&quot;www.',$description);
			$description	= str_replace('href=&quot;http://'.$this->parent->domain.'/http:','href=&quot;http:',$description);


			$email_body["body"]		= $description;
/*			$email_body["body"]		= "
Thank you for subscribing to recieve our newsletter.

";
*/
			$this->call_command("EMAIL_QUICK_SEND",$email_body);
			/* Ends Auto reply Email portion */

			/* Starts Send Admin new subscriber Email portion */
			
			$email_body_admin["from"]	= '"'.$email.'" <'.$email.'>';
			$email_body_admin["subject"]= "New Subscriber";
			$email_body_admin["to"]		= "$from_email";
			$email_body_admin["format"]	= "PLAIN";
			$email_body_admin["body"]	= "A new subscriber $name ($email) has just subscribed to the newsletter.";

			$this->call_command("EMAIL_QUICK_SEND",$email_body_admin);
			
			/* Ends Send Admin new subscriber Email portion */
		}
		
		$out = "<module name=\"".$this->module_name."\" display=\"confirm\">";
		$out .= "<text><![CDATA[<h1 class=\"entrylocation\"><span>Newsletter</span></h1>]]></text>";
		$out .=	$message_confirmation;
		$out .=	"</module>";
		
		return $out;
	}
	
		
	function newsletter_issue_editor($parameters){
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		$newsletter_id	= $this->check_parameters($parameters,"newsletter",-1);
		$command 		= $this->check_parameters($parameters,"command");
		$rtf_issue 		= "";
		$plain_issue 	= "";
		$issue_subject	= "";
		$label			= "LOCALE_ISSUE_ADD";
		if ($identifier != -1){
			$label			= "LOCALE_ISSUE_EDIT";
			$sql ="select * from newsletter_archive where newsarchive_identifier=$identifier and newsarchive_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$rtf_issue 		= $r["newsarchive_rtftext"];
				$plain_issue 	= $r["newsarchive_plaintext"];
				$issue_subject	= $r["newsarchive_subject"];
				$newsletter_id	= $r["newsarchive_newsletter"];
			}
		}
		$this->load_editors();

		$out="<module name=\"newsletter\" display=\"form\">";
		$out .= "<page_options>";

		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));
		$out .= 	$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","NEWSLETTER_LIST_ISSUES",LOCALE_CANCEL,"identifier=$identifier"));
		$out .= "</page_options>";
		$out .= "<form name=\"newsletter_issue\" method=\"post\" label=\"".$this->get_constant($label)."\">";
		$out .= "	<input type=\"hidden\" name=\"newsletter_identifier\"><![CDATA[$newsletter_id]]></input>";
		$out .= "	<input type=\"hidden\" name=\"issue_identifier\"><![CDATA[$identifier]]></input>";
		$out .= "	<input type=\"hidden\" name=\"prev_command\"><![CDATA[$command]]></input>";
		$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[".NEWSLETTER_SAVE_ISSUE."]]></input>";
		$out .= "	<input type=\"text\" label=\"".ENTRY_SUBJECT."\" size=\"255\" name=\"newsarchive_subject\" required=\"YES\"><![CDATA[$issue_subject]]></input>";
		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_HTML_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .= "	<textarea label=\"".ENTRY_HTML_DESCRIPTION."\" size=\"40\" height=\"15\" name=\"newsarchive_rtftext\" type=\"RICH-TEXT\" required=\"YES\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$rtf_issue]]></textarea>";
		$out .= "	<textarea label=\"".ENTRY_PLAIN_DESCRIPTION."\" size=\"40\" height=\"15\" name=\"newsarchive_plaintext\" type=\"PLAIN\" required=\"YES\"><![CDATA[$plain_issue]]></textarea>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" />";
		$out .= "</form></module>";
	return $out;
	}
	function newsletter_issue_save($parameters){
		$newsletter_identifier		= $this->check_parameters($parameters,"newsletter_identifier",-1);
		$issue_identifier			= $this->check_parameters($parameters,"issue_identifier",-1);
		$newsarchive_subject		= $this->validate_with_url($this->check_parameters($parameters,"newsarchive_subject"));
		$newsarchive_rtftext		= $this->tidy($this->check_parameters($parameters,"newsarchive_rtftext"));
//		$newsarchive_rtftext		= $this->validate_with_url($this->check_parameters($parameters,"newsarchive_rtftext"));
		$newsarchive_plaintext		= $this->check_parameters($parameters,"newsarchive_plaintext");
		$newsarchive_status 		= $this->check_parameters($parameters,"newsarchive_status",0);
		$newsarchive_date			= $this->libertasGetDate("Y/m/d H:i:s");
		$command 					= $this->check_parameters($parameters,"command");
		$user_id					= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER");
		if ($issue_identifier==-1){
			$sql = "insert into newsletter_archive (newsarchive_client, newsarchive_subject, newsarchive_rtftext, newsarchive_plaintext, newsarchive_status, newsarchive_date, newsarchive_newsletter, newsarchive_author) values ('$this->client_identifier', '$newsarchive_subject','$newsarchive_rtftext', '$newsarchive_plaintext', '$newsarchive_status', '$newsarchive_date', '$newsletter_identifier', $user_id)";
		}else{
			$sql = "update newsletter_archive set 
						newsarchive_client='$this->client_identifier', 
						newsarchive_subject='$newsarchive_subject', 
						newsarchive_rtftext='$newsarchive_rtftext', 
						newsarchive_plaintext='$newsarchive_plaintext', 
						newsarchive_status='$newsarchive_status', 
						newsarchive_newsletter='$newsletter_identifier',
						newsarchive_author='$user_id'
					where
						newsarchive_identifier=$issue_identifier";
		}
		
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST_ISSUES&identifier=$newsletter_identifier"));								

	}
	
	function newsletter_issue_available($parameters){	
		$newsletter					= $this->check_parameters($parameters,"newsletter",-1);
		$issue_identifier			= $this->check_parameters($parameters,"identifier",-1);
		$sql = "update newsletter_archive set 
						newsarchive_status=1  
					where 						
						newsarchive_newsletter='$newsletter' and newsarchive_identifier=$issue_identifier";
				
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST_ISSUES&identifier=$newsletter"));								
	}
		
	
	function newsletter_issue_test($parameters){
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		$email_address	= $this->check_parameters($_SESSION,"SESSION_EMAIL");
		
		$sql ="select * from newsletter_archive inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_archive.newsarchive_newsletter where newsarchive_identifier=$identifier and newsarchive_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$rtf_issue 			= $r["newsarchive_rtftext"];
			$plain_issue 		= $r["newsarchive_plaintext"];
			$issue_subject		= $r["newsarchive_subject"];
			$newsletter_id		= $r["newsarchive_newsletter"];
			$newsletter_email	= $r["newsletter_from_email"];
			$newsletter_label	= $r["newsletter_from_label"];
		}

		$email["from"]		= '"'.$newsletter_label.'" ' .$newsletter_email;
		$email["subject"]	= $issue_subject." plain";
		$email["to"]		= "$email_address";
		$email["format"]	= "PLAIN";
		$email["body"]		= $plain_issue;

		$this->call_command("EMAIL_QUICK_SEND",$email);
		
		$email["subject"]	= $issue_subject." html";
		$email["to"]		= "$email_address";
		$email["format"]	= "HTML";

		$rtf_issue	= str_replace('src=&quot;uploads/','src=&quot;http://'.$this->parent->domain.'/uploads/',$rtf_issue);
		$rtf_issue	= str_replace('href=&quot;','href=&quot;http://'.$this->parent->domain.'/',$rtf_issue);
		$rtf_issue	= str_replace('href=&quot;http://'.$this->parent->domain.'/www.','href=&quot;www.',$rtf_issue);
		$rtf_issue	= str_replace('href=&quot;http://'.$this->parent->domain.'/http:','href=&quot;http:',$rtf_issue);

		$email["body"]		= $rtf_issue;
		$this->call_command("EMAIL_QUICK_SEND",$email);

		$out="<module name=\"newsletter\" display=\"form\">";
		$out .= "<page_options>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));
		$out .= 	$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","NEWSLETTER_LIST_ISSUES",LOCALE_CANCEL,"identifier=$newsletter_id"));
		$out .= "</page_options>";
		$out .= "<form name=\"newsletter_issue\" method=\"post\" label=\"".LOCALE_TEST_NEWSLETTER_ISSUE_TITLE."\">";
		$out .= "<text><![CDATA[".LOCALE_TEST_NEWSLETTER_SENT."]]></text>";
		$out .= "</form></module>";
		return $out;
	}

	function newsletter_issue_send_final($parameters){
		$identifier			= $this->check_parameters($parameters,"identifier",-1);
		$page				= $this->check_parameters($parameters,"page",1);
		$subscriber_group	= $this->check_parameters($parameters,"subscriber_group");
		$already_sent_values= $this->check_parameters($parameters,"already_sent_values");
		
		$group_sql = "";
		$subscriber_group_list = implode(",", $subscriber_group);
		$subscriber_group_list_arr = explode(",",$subscriber_group_list);
		foreach($subscriber_group_list_arr as $group_list){
			if($group_sql != "")
				$group_sql .= " or ";
			$group_sql .= " subscriber_group like '%$group_list%' ";
		}
		$already_sent_values_arr = split(",",$already_sent_values);
		$subscriber_group_list_all_arr = array_merge($subscriber_group,$already_sent_values_arr);
		$subscriber_group_list_all_arr = array_unique($subscriber_group_list_all_arr);
		$subscriber_group_list_all = implode(",", $subscriber_group_list_all_arr);
		$subscriber_group_list_all = trim($subscriber_group_list_all,",");
		
//		$subscriber_group_list_all = $already_sent_values.','.$subscriber_group_list;
		
		$P_SIZE				= 20;
		if ($identifier!=-1){
			$sql ="select * from newsletter_archive inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_archive.newsarchive_newsletter where newsarchive_identifier=$identifier and newsarchive_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$rtf_issue 			= $this->check_parameters($r,"newsarchive_rtftext");
				$plain_issue 		= $this->check_parameters($r,"newsarchive_plaintext");
				$issue_subject		= $this->check_parameters($r,"newsarchive_subject");
				$newsletter_id		= $this->check_parameters($r,"newsarchive_newsletter");
				$newsletter_email	= $this->check_parameters($r,"newsletter_from_email");
				$newsletter_label	= $this->check_parameters($r,"newsletter_from_label");
			}
			$email["from"]		= '"'.$newsletter_label.'" <'.$newsletter_email.'>';
			$sql ="select * from newsletter_subscription where subscriber_newsletter=$newsletter_id and subscriber_client=$this->client_identifier and subscriber_verified=1 and ($group_sql)";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($result){
				$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
				
				$goto = ((--$page)*$P_SIZE);
				if (($goto!=0)&&($number_of_records>$goto)){
					$pointer = $this->call_command("DB_SEEK",array($result,$goto));
				}
				if ($goto+$P_SIZE>$number_of_records){
					$finish = $number_of_records;
				}else{
					$finish = $goto+$P_SIZE;
				}
				$goto++;
				$page++;
				$count=0;
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$count++;
					//$email["subject"]	= ((($page-1) * $P_SIZE)+($count))." ".$issue_subject;
					
					if ($r["subscriber_rtf"]==0){
//						$email_plain[count($email_plain)]["EMAIL"]		= $r["subscriber_email"];
						$email_plain[count($email_plain)] = Array("EMAIL"=>$r["subscriber_email"], "NAME"=>$r["subscriber_name"]);
						$plain_issue				= str_replace("{name}","[[contact_first_name]]",$plain_issue);
						
						$body_plain					= $plain_issue."\n\n\nYou can unsubscribe from this newsletter by pasting the following url into your browser http://".$this->parent->domain."".$this->parent->base."index.php?command=NEWSLETTER_UNSUBSCRIBE&email=[[contact_email]]&newsletter=".$newsletter_id	;
					} else {						
//						$email_html[count($email_html)]["EMAIL"]	= $r["subscriber_email"];
						$email_html[count($email_html)] = Array("EMAIL"=>$r["subscriber_email"], "NAME"=>$r["subscriber_name"]);
						$rtf_issue					    = str_replace("{name}","[[contact_first_name]]",$rtf_issue);

//						echo $rtf_issue;echo 'ffff';
						$rtf_issue					    = str_replace('src=&quot;uploads/','src=&quot;http://'.$this->parent->domain.'/uploads/',$rtf_issue);
						$rtf_issue	= str_replace('href=&quot;','href=&quot;http://'.$this->parent->domain.'/',$rtf_issue);
						$rtf_issue	= str_replace('href=&quot;http://'.$this->parent->domain.'/www.','href=&quot;www.',$rtf_issue);
						$rtf_issue	= str_replace('href=&quot;http://'.$this->parent->domain.'/http:','href=&quot;http:',$rtf_issue);
						
//						echo $rtf_issue;echo 'ass';die;

//						$rtf_issue					                = str_replace("{name}",$r["subscriber_name"],$rtf_issue);

						$body_html									= $rtf_issue."<p>You can unsubscribe from this newsletter by <a href='http://".$this->parent->domain."/index.php?command=NEWSLETTER_UNSUBSCRIBE&email=[[contact_email]]&newsletter=".$newsletter_id."'>Clicking Here</a></p>";
					}
					
				}
				if ($plain_issue != ""){
					$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_plain, "subject"=>$issue_subject, "body"=>$body_plain, "from"=>$email["from"]));	
				}				
				if ($rtf_issue != ""){
					$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_html, "subject"=>$issue_subject, "body"=>$body_html,"from"=>$email["from"],"format"=>"HTML"));									
				}
/*				
				if ($goto<$number_of_records){
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_SEND_TO_SUBSCRIBERS&identifier=$identifier&".SID."&page=".($page+1)));
				} else {
*/
					$identifier		= $this->check_parameters($parameters,"identifier",-1);
				
					$sql 			= "update newsletter_archive set 
											newsarchive_group='$subscriber_group_list_all', 
											newsarchive_status='2' 
										where 
											newsarchive_identifier=$identifier and 
											newsarchive_client=$this->client_identifier";
											
					$this->call_command("DB_QUERY",array($sql));
					$out="<module name=\"newsletter\" display=\"form\">";
					$out .= "<page_options>";
//					$out .= 	$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("OK","NEWSLETTER_LIST_ISSUES",LOCALE_OK,"identifier=$newsletter_id"));
					$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));
					
					$out .= "</page_options>";
					$out .= "<form name=\"newsletter_issue\" method=\"post\" label=\"".LOCALE_NEWSLETTER_ISSUE_TITLE."\">";
					$out .= "<text><![CDATA[".LOCALE_NEWSLETTER_SENT."]]></text>";
					$out .= "</form></module>";
					return $out;
//				}
			}
		}else{
			$out="<module name=\"newsletter\" display=\"form\">";
			$out .= "<page_options>";
//			$out .= 	$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("OK","NEWSLETTER_LIST",LOCALE_OK));
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));
			$out .= "</page_options>";
			$out .= "<form name=\"newsletter_issue\" method=\"post\" label=\"".LOCALE_NEWSLETTER_ISSUE_TITLE."\">";
			$out .= "<text><![CDATA[".LOCALE_NEWSLETTER_ISSUE_NOT_FOUND."]]></text>";
			$out .= "</form></module>";
			return $out;
		}
	}
	function newsletter_editor($parameters){
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		$command 		= $this->check_parameters($parameters,"command");
		$news_label		= "";
		$description	= "";
		$from_email 	= "";
		$from_label		= "";
		$menu_parent	= "";
		$display_tab	= "";
		$label			= LOCALE_ADD_NEW;
		if ($identifier != -1){
			$label			= LOCALE_EDIT;
			$sql ="select * from newsletter_data where newsletter_identifier=$identifier and newsletter_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$news_label		= $r["newsletter_label"];
				$description 	= $r["newsletter_description"];
				$from_email		= $r["newsletter_from_email"];
				$from_label		= $r["newsletter_from_label"];
				$menu_parent	= $r["newsletter_location"];
			}
		}
		$this->load_editors();
		$out="<module name=\"newsletter\" display=\"form\">";
		$out .= "<page_options>";

		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));

//		$out .= 	$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","NEWSLETTER_LIST_ISSUES",LOCALE_CANCEL,"identifier=$identifier"));
		$out .= "</page_options>";
		$out .= "<form name=\"newsletter_issue\" method=\"post\" label=\"".$this->get_constant($label)."\">";
		$out .= "	<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>";
		$out .= "	<input type=\"hidden\" name=\"prev_command\"><![CDATA[$command]]></input>";
		$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[NEWSLETTER_SAVE]]></input>";
		$out .= "	<page_sections>";
		$out .= "		<section label=\"News Letter\"";
			if ($display_tab==""){
				$out .= " selected='true'";
			}
			$out .= ">";

		$menu_parent = explode("|",$menu_parent);

		$data =  $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_parent));
//		$out .= "	<select label=\"".LOCALE_DEFAULT_MENU_MSG."\" name=\"newsletter_location\" multiple=\"1\" size=\"7\">$data</select>";
		$out .= "	<select label=\"".LOCALE_DEFAULT_MENU_MSG."\" name=\"newsletter_location\" multiple=\"1\" size=\"7\">$data</select>";
		$out .= "	<input type=\"text\" label=\"".NEWSLETTER_TITLE."\" size=\"255\" name=\"newsletter_label\" required=\"YES\"><![CDATA[$news_label]]></input>";
		$out .= "	<input type=\"text\" label=\"".LOCALE_FROM_EMAIL."\" size=\"255\" name=\"newsletter_from_email\" required=\"YES\"><![CDATA[$from_email]]></input>";
		$out .= "	<input type=\"text\" label=\"".LOCALE_FROM_LABEL."\" size=\"255\" name=\"newsletter_from_label\" required=\"YES\"><![CDATA[$from_label]]></input>";
		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_HTML_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .= "	<textarea label=\"".ENTRY_DESCRIPTION."\" size=\"40\" height=\"15\" name=\"newsletter_description\" type=\"RICH-TEXT\" required=\"YES\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$description]]></textarea>";
		$out .= "		</section>";
		$out .= "	</page_sections>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" />";
		$out .= "</form></module>";
	return $out;
	}

	function newsletter_save($parameters){
		$identifier					= $this->check_parameters($parameters,"identifier",-1);
		$from_email					= $this->check_parameters($parameters,"newsletter_from_email");
		$from_label					= $this->check_parameters($parameters,"newsletter_from_label");
		$description				= $this->tidy($this->check_parameters($parameters,"newsletter_description"));
		$label				 		= $this->tidy($this->check_parameters($parameters,"newsletter_label"));
		$newsletter_location		= $this->check_parameters($parameters,"newsletter_location");
		
		foreach ($newsletter_location as $newsletter_values){
			$newsletter_location_val .= $newsletter_values."|";
		}
		$newsletter_location = trim($newsletter_location_val,"|");
		
		$newsletter_date			= $this->libertasGetDate("Y/m/d H:i:s");
		/*	Commented By Muhammad Imran Mirza */
		/*
		$special_webobjects			= Array(
			"NEWSLETTER_SUBSCRIPTION" => Array(
			"owner_module" 	=> $this->module_name,
			"label" 		=> "Newsletter Subscription (".strip_tags($this->check_parameters($parameters,"newsletter_label")).")",
			"wo_command"	=> "DISPLAY_SUBSCRIPTION_FORM",
			"file"			=> "",
			"available"		=> 1,
			"type"			=> 2
			)
		);						
		*/
		/*	Commented By Muhammad Imran Mirza */
		/*	Added By Muhammad Imran Mirza */

		$special_webobjects			= Array(
			"NEWSLETTER_SUBSCRIPTION" => Array(
			"owner_module" 	=> $this->module_command,
			"label" 		=> "Newsletter Subscription (".strip_tags($this->check_parameters($parameters,"newsletter_label")).")",
			"wo_command"	=> "DISPLAY_SUBSCRIPTION_FORM",
			"file"			=> "",
			"available"		=> 1,
			"type"			=> 2
			)
		);						
		/*	Added By Muhammad Imran Mirza */
		
		if ($identifier==-1){
			$sql = "insert into newsletter_data (
						newsletter_client, 
						newsletter_label, 
						newsletter_from_email, 
						newsletter_from_label, 
						newsletter_description, 
						newsletter_date_created, 
						newsletter_status,
						newsletter_location
					) values (
						'$this->client_identifier', 
						'$label',
						'$from_email', 
						'$from_label', 
						'$description', 
						'$newsletter_date',
						0,
						'$newsletter_location'
					)";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY",array($sql));
					
			/* Get and Assign owner_id to show newsletter (Added By Muhammad Imran Mirza )*/

			$sql_max_identifier ="select max(newsletter_identifier) as max_identifier from newsletter_data";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql_max_identifier"));}
			$result_max_identifier  = $this->call_command("DB_QUERY",Array($sql_max_identifier));
            $r_max_identifier = $this->call_command("DB_FETCH_ARRAY",Array($result_max_identifier));
			$data_max_identifier = $r_max_identifier["max_identifier"];

				$this->call_command("WEBOBJECTS_MANAGE_MODULE_WO",
					Array(
						"webobjects"	=> $special_webobjects,
						"owner_id" 		=> $data_max_identifier, //dummy (dummy Replaced by newsletter_data identifier Added by Muhammad Imran)
						"starter"		=> $this->module_command,
						"cmd"			=> "ADD"
					)
				);
			
			/* Get and Assign owner_id to show newsletter (Added By Muhammad Imran Mirza )*/
			
		}else{
			$sql = "update newsletter_data set  
						newsletter_label='$label', 
						newsletter_from_email='$from_email', 
						newsletter_from_label='$from_label', 
						newsletter_description='$description',
						newsletter_location='$newsletter_location',
						newsletter_status = 0			
					where
						newsletter_identifier=$identifier and 
						newsletter_client='$this->client_identifier'";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY",array($sql));
			
			/* Get and Assign owner_id to show newsletter (Added By Muhammad Imran Mirza )*/
					$this->call_command("WEBOBJECTS_MANAGE_MODULE_WO",
						Array(
							"webobjects"	=> $special_webobjects,
							"owner_id" 		=> $identifier, //dummy (dummy Replaced by newsletter_data identifier Added by Muhammad Imran)
							"starter"		=> $this->module_command,
							"cmd"			=> "UPDATE"
						)
					);
			/* Get and Assign owner_id to show newsletter (Added By Muhammad Imran Mirza )*/
			
		}
		

		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST"));						
	}

	function newsletter_ready($parameters){
		$identifier					= $this->check_parameters($parameters,"identifier",-1);
		$sql = "update newsletter_data set 
					newsletter_status='1' 
				where
					newsletter_client='$this->client_identifier' and 
					newsletter_identifier=$identifier";
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST"));						
	}

	function newsletter_preview($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier",-1);
		$type		= $this->check_parameters($parameters,"identifier",-1);
		$sql		= "select * from newsletter_data where 
							newsletter_client='$this->client_identifier' and 
							newsletter_identifier=$identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$news_email		= $r["newsletter_from_email"];
			$news_from		= $r["newsletter_from_label"];
			$news_label		= $r["newsletter_label"];
			$description 	= $r["newsletter_description"];
		}
		$out = "<page_options>";

		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));
//		$out .= 	$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","NEWSLETTER_LIST",LOCALE_CANCEL,"identifier=$identifier"));
		$out .= "</page_options>";
		$out .= "	<header><![CDATA[".LOCALE_FROM_EMAIL."]]></header>";
		$out .= "	<row><![CDATA[$news_email]]></row>";
		$out .= "	<header><![CDATA[".LOCALE_FROM_LABEL."]]></header>";
		$out .= "	<row><![CDATA[$news_from]]></row>";

		$out .= "	<header><![CDATA[".LOCALE_NEWSLETTER_DESCRIPTION."]]></header>";

		$out .= "	<header><![CDATA[$news_label]]></header>";
		$out .= "	<row><![CDATA[$description]]></row>";
		return "<module name=\"pages\" display=\"table\"><table label='".LOCALE_NEWSLETTER_PREVIEW."'>".$out."</table></module>";
	}

	function newsletter_publish($parameters){
		$identifier					= $this->check_parameters($parameters,"identifier",-1);
		$sql = "update newsletter_data set 
					newsletter_status='2' 
				where
					newsletter_client='$this->client_identifier' and 
					newsletter_identifier=$identifier";
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST&amp;newsletter=$identifier"));		
	}


	function newsletter_issue_ready($parameters){
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		$news			= $this->check_parameters($parameters,"newsletter",-1);
		$sql = "update newsletter_archive set 
					newsarchive_status = '1' 
				where
					newsarchive_client = '$this->client_identifier' and 
					newsarchive_identifier = $identifier and 
					newsarchive_newsletter = $news";
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST_ISSUES&amp;identifier=$news"));				
	}

	function sign_up_now_form($parameters){
		$show_module = $this->check_parameters($parameters,"show_module",1);
		$out  = "	<input type='hidden' size='255' name='command'><![CDATA[NEWSLETTER_SIGN_UP_NOW_SAVE]]></input>";
		$out .= "	<input label='".LOCALE_EMAIL."' type='text' size='255' name='email_address'></input>";
		$out .= "	<radio label='".LOCALE_EMAIL_FORMATTING."' name='email_rtf'><option value='0'><![CDATA[LOCALE_PLAINTEXT]]></option><option value='1'><![CDATA[LOCALE_RICHTEXT]]></option></radio>";
		$out .= "	<input type=\"submit\" iconify=\"REGISTER\" value='Send'/>";
		if ($show_module==1){
			return "<module name=\"newsletter\" display=\"signup\"><form label=\"".LOCALE_NEWSLETTER_SIGN_UP."\" name='newsletter_sign_up'>".$out."</form></module>";
		}else{
			return "<form label=\"".LOCALE_NEWSLETTER_SIGN_UP."\" name='NEWSLETTER_SIGN_UP'>".$out."</form>";
		}
	}

	function sign_up_now_form_save($parameters){
		$email_address	= $this->check_parameters($parameters,"email_address");
		$email			= $this->check_parameters($parameters,"email",$email_address);
		
		if (!$this->str_is_int($email_address)){
			$email_address = $this->call_command("EMAIL_INSERT_ADDRESS",$parameters);
		}
		$sql = "select newsletter_data.newsletter_identifier from newsletter_data where newsletter_client=$this->client_identifier and newsletter_status=2";
		$result = $this->call_command("DB_QUERY",array($sql));
		$rows = $this->call_command("DB_NUM_ROWS",array($result));
		if ($rows==1){
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$news_id = $r["newsletter_identifier"];
			$sql = "insert into newsletter_subscription (subscriber_client, subscriber_email, subscriber_newsletter, subscriber_verified) values ($this->client_identifier, $email_address, ".$news_id.", 0)";
			$this->call_command("DB_QUERY",array($sql));
			$this->send_subscribe_email_verify($parameters);
		}else{
			$sign_up 	= $this->check_parameters($parameters,"newsletter_sign_up",Array());
			$max_len = count($sign_up);
			if ($max_len>0){
				for ($index=0;$index<$max_len;$index++){
					$sql = "insert into newsletter_subscription (subscriber_client, subscriber_email, subscriber_newsletter, subscriber_verified) values ($this->client_identifier, $email_address, ".$sign_up[$index].", 0)";
					$this->call_command("DB_QUERY",array($sql));
				}
				$this->send_subscribe_email_verify($parameters);
				$out  = "<text><![CDATA[".LOCALE_NEWSLETTER_CONFIRM_MSG."]]></text>";
				$out .= "";
				$txt ="<module name=\"pages\" display=\"form\"><form label=\"LOCALE_NEWSLETTER_CONFIRM\" name=\"choose_your_newletters\">".$out."</form></module>";
				return $txt;
				
			} else {
				$out		 =  "	<input type='hidden' name='email_address'><![CDATA[$email_address]]></input>";
				$out		.=  "	<input type='hidden' name='email'><![CDATA[$email]]></input>";
				$out 		.= "	<input type='hidden' size='255' name='command'><![CDATA[NEWSLETTER_SIGN_UP_NOW_SAVE]]></input>";
				$sql		= "select * from newsletter_data where newsletter_data.newsletter_status = 2"; //live
				$result		= $this->call_command("DB_QUERY",array($sql));
				$num_rows 	= $this->call_command("DB_NUM_ROWS",array($result));
				if ($num_rows > 0){
				$out 		.= "	<checkboxes name=\"newsletter_sign_up\" label=\"LOCALE_SIGNUP_TO_A_NEWSLETTER\" type='vertical'><options>";
					while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
						$out .= "<option value='".$r["newsletter_identifier"]."' ";
						for ($index=0;$index<$max_len;$index++){
							if ($r["newsletter_identifier"] == $sign_up[$index]){
								$out.="selected='true'";
							}
						}
						$out .= "><![CDATA[<strong>".$this->check_parameters($r,"newsletter_label")."</strong><br/>".$this->check_parameters($r,"newsletter_description")."]]></option>";
					}
					$out .= "</options></checkboxes>";
				}
				$out .= "	<input type=\"submit\" iconify=\"REGISTER\" alt='LOCALE_REGISTER'/>";
				$txt = "<module name=\"pages\" display=\"form\"><form label=\"LOCALE_NEWSLETTER_SIGNUPS\" name=\"choose_your_newletters\">".$out."</form></module>";
//				print $txt;
				return $txt;
			}
		}
	}
	function list_forms(){
		/**
			* This is a list of the forms available to the website only NOT forms that the Administration 
			- can use.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/	
		return $this->available_forms;
	}
	
	function str_is_int($str) {
		$var=intval($str);
		return ("$str"=="$var");
	}

	function verify_subscription_email($parameters){
		$email_address	= $this->check_parameters($parameters,"email_address");
		$codex 			= $this->check_parameters($parameters,"codex");
	}
	
	function issue_reject($parameters){
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		$news			= $this->check_parameters($parameters,"newsletter",-1);
		$sql = "update newsletter_archive set 
					newsarchive_status='0' 
				where
					newsarchive_client='$this->client_identifier' and 
					newsarchive_identifier=$identifier and 
					newsarchive_newsletter=$news";
		$this->call_command("DB_QUERY",array($sql));
		$out = "	<text><![CDATA[".LOCALE_ISSUE_REJECTED_MSG."]]></text>";
		return "<module name=\"pages\" display=\"form\">
				<page_options>". $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","NEWSLETTER_LIST_ISSUES",LOCALE_CANCEL,"identifier=$news"))."</page_options><form label=\"".LOCALE_ISSUE_REJECTED."\" name=\"confirm\">".$out."</form></module>";
	}
	
	function send_subscribe_email_verify($parameters){
		$email_address = $this->check_parameters($parameters,"email_address");
		$email_add = $this->check_parameters($parameters,"email");
		if (strlen($email_add)>0){
			$email_address = $email_add;
		}
		$email = array();
		if (strlen($email_address)>0){
			$sql = "select * from email_addresses where email_address='$email_address' and email_client=$this->client_identifier";
			$result		= $this->call_command("DB_QUERY",array($sql));
			$email_codex = "";
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$email_codex = $r["email_codex"];
			}
			$email["subject"]	= "Please verify your regisatration.";
			$email["to"]		= "$email_address";
			$email["format"]	= "PLAIN";
			$email["body"]		= "
Thankyou for registering to recieve our newletter.

We require that you now verify your registration by coping the following link into your browser.
http://".$this->parent->domain."".$this->parent->base."index.php?command=NEWSLETTER_VALIDATE&email=".$email_address."&codex=".$email_codex."

The newsletter will	only be sent to you when you verify that you have requested this information.";
			$this->call_command("EMAIL_QUICK_SEND",$email);
		}
	}
	
	function verify_email($parameters){
		$email_address	= $this->check_parameters($parameters,"email");
		$email_codex	= $this->check_parameters($parameters,"codex");
		$sql 			= "update email_addresses set email_verified=1 where email_address='$email_address' and email_codex='$email_codex' and email_client = $this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		
		$sql 			= "select * from email_addresses where email_address='$email_address' and email_codex='$email_codex' and email_client = $this->client_identifier";
		$result			= $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$email_identifier = $r["email_identifier"];
		}
		$sql 			= "update newsletter_subscription set subscriber_verified = 1 where subscriber_email = '$email_identifier' and subscriber_client = $this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		$out  			= "<text><![CDATA[LOCALE_NEWSLETTER_VALIDATOR_MSG]]></text>";
		return 			  "<module name=\"pages\" display=\"form\"><form label=\"LOCALE_NEWSLETTER_VALIDATOR\" name=\"choose_your_newletters\">".$out."</form></module>";
	}

	function unsubscribe_email($parameters){
		$email_address	= $this->check_parameters($parameters,"email");
		$email_codex	= $this->check_parameters($parameters,"codex");
		$newsletter		= $this->check_parameters($parameters,"newsletter");
		/*
		$sql 			= "select * from email_addresses where email_address='$email_address' and email_codex='$email_codex' and email_client = $this->client_identifier";
		$result			= $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$email_identifier = $r["email_identifier"];
		}
		*/
		$sql 			= "delete from newsletter_subscription where subscriber_newsletter = '$newsletter' and subscriber_client = $this->client_identifier and subscriber_email = '$email_address'";
		
		$this->call_command("DB_QUERY",array($sql));
		$out = "	<text><![CDATA[".LOCALE_UNSUBSCRIBE_CONFIRM_MSG."]]></text>";
//		return "<module name=\"pages\" display=\"form\"><form label=\"".LOCALE_NEWSLETTER_UNSUBSCRIBE_CONFIRM."\" name=\"confirm\">".$out."</form></module>";

		return "<module name=\"pages\" display=\"form\"><form name=\"confirm\">".$out."</form></module>";
	}
	
	function newsletter_remove_issue($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		$news_id	= $this->check_parameters($parameters,"newsletter");

		$sql = "delete from newsletter_archive where newsarchive_identifier = $identifier and newsarchive_newsletter = $news_id and newsarchive_client = $this->client_identifier ";
		$this->call_command("DB_QUERY",array($sql));				
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_LIST_ISSUES"));					
	}
	
	function newsletter_remove($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		$sql = "delete from newsletter_archive where newsarchive_newsletter = $identifier and newsarchive_client = $this->client_identifier ";
		$this->call_command("DB_QUERY",array($sql));				

		$sql = "delete from newsletter_data where newsletter_identifier = $identifier and newsletter_client = $this->client_identifier ";
		$this->call_command("DB_QUERY",array($sql));		
		
		$sql = "delete from newsletter_subscription where subscriber_newsletter = $identifier and subscriber_client = $this->client_identifier ";
		$this->call_command("DB_QUERY",array($sql));
			/* Get and Assign owner_id to show newsletter (Added By Muhammad Imran Mirza )*/
		/*	Commented By Muhammad Imran Mirza */
/*
		$special_webobjects			= Array(
			"NEWSLETTER_SUBSCRIPTION" => Array(
			"owner_module" 	=> $this->module_name,
			"label" 		=> "",
			"wo_command"	=> "DISPLAY_SUBSCRIPTION_FORM",
			"available"		=> 1,			
			"type"			=> 2
			)
		);						
		
		$this->call_command("WEBOBJECTS_MANAGE_MODULE_WO",
			Array(
				"webobjects"	=> $special_webobjects,
				"owner_id" 		=> 0, //dummy
				"starter"		=> $this->module_command,
				"cmd"			=> "REMOVE"
			)
		);
*/
		/*	Commented By Muhammad Imran Mirza */

		/*	Added By Muhammad Imran Mirza */
		$special_webobjects			= Array(
			"NEWSLETTER_SUBSCRIPTION" => Array(
			"owner_module" 	=> $this->module_command,
			"label" 		=> "",
			"wo_command"	=> "DISPLAY_SUBSCRIPTION_FORM",
			"available"		=> 1,			
			"type"			=> 2
			)
		);						
		
		$this->call_command("WEBOBJECTS_MANAGE_MODULE_WO",
			Array(
				"webobjects"	=> $special_webobjects,
				"owner_id" 		=> $identifier, //dummy (dummy Replaced by newsletter_data identifier Added by Muhammad Imran)
				"starter"		=> $this->module_command,
				"cmd"			=> "REMOVE"
			)
		);
		/*	Added By Muhammad Imran Mirza */
		
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_LIST"));					
	}
	

	function send_to_page($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		$news_id	= $this->check_parameters($parameters,"newsletter");
		$sql 		= "select * from newsletter_archive inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_archive.newsarchive_newsletter where newsarchive_identifier='$identifier' and newsarchive_newsletter='$news_id' and newsarchive_client = $this->client_identifier";
		$result			= $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$page_title	= $r["newsarchive_subject"];
			$page_body	= $r["newsarchive_rtftext"];
			$menu_location	= $r["newsletter_location"];
		}
		return $this->call_command("PAGE_IMPORT",Array(
				"command"	=> "PAGE_IMPORT",
				"page_title"	=> $page_title,
				"menu_location" => $menu_location,
				"page_body"	=>	$page_body
			)
		);
	}
	
	function cache_available_forms($parameters){
		$frms = $this->available_forms;
		$lang="en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		for ($index=0,$max=count($frms);$index<$max;$index++){
			$out = $this->call_command($frms[$index],Array("show_module"=>0));
			$fp = fopen($data_files."/form_".$this->client_identifier."_".$lang."_".$frms[$index].".xml", 'w');
			fwrite($fp, $out);
			fclose($fp);
			$um = umask(0);
			@chmod($data_files."/form_".$this->client_identifier."_".$lang."_".$frms[$index].".xml", LS__FILE_PERMISSION);
			umask($um);

		}
	}
	
	/* Function For Paging and Search Subscriber List (Added by Muhammad Imran)*/
	function filter_entries($parameters,$type){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_filter",__LINE__,"[]"));
		}
		$identifier 	= $this->check_parameters($parameters,"identifier",0);
		$keywords		= $this->check_parameters($parameters,"keywords","");
//		$page_boolean	= $this->check_parameters($parameters,"page_boolean","or");
		$search 		= $this->check_parameters($parameters,"search",0);
//		$page_search 	= $this->check_parameters($parameters,"page_search");
		$status_filter 	= $this->check_parameters($parameters,"status_filter",-1);
		$search++;
		
		$out  = "\t\t\t\t<form name=\"filter_form\" label=\"".FILTER_RESULTS."\" method=\"get\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\"><![CDATA[$type]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" ><![CDATA[1]]></input>\n";
/*		$out .="<input type=\"text\" name=\"keywords\" label=\"Search phrase\" size=\"255\"><![CDATA[$keywords]]></input>";
		$out .="\t\t\t\t\t<select label=\"Status of Entries\" name=\"status_filter\">
			<option value=\"-1\">All </option>
			<option value=\"0\"";
		if ($status_filter==0){
			$out .= " selected='true'";
		}
		$out .= ">Requiring Approval</option>
			<option value=\"1\"";
		if ($status_filter==1){
			$out .= " selected='true'";
		}
		$out .= ">Approved</option>
		</select>";
		$out .= "\t\t\t\t\t<input type=\"submit\" iconify=\"SEARCH\" name=\"\" value=\"".FILTER_RESULTS."\"/>\n";
*/		$out .= "\t\t\t\t</form>";
		return $out;
	}
	/* Function For Paging and Search Subscriber List (Added by Muhammad Imran)*/

		/* Modification function added by Ali Imran*/
	function subscriber_list($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		$sql="select *				
				from newsletter_subscription inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_subscription.subscriber_newsletter
				where subscriber_newsletter='$identifier' AND subscriber_client = $this->client_identifier order by newsletter_subscription.subscriber_email";
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		$result = $this->call_command("DB_QUERY",array($sql));
				
		$variables = Array();
//		$variables["FILTER"]			= "";// $this->filter($parameters);
		//for paging by Muhammad Imran
		$variables["FILTER"]			= $this->filter_entries($parameters,$this->module_command."SUBSCRIBER_LIST");
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["as"]				= "table";
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		
		$variables["HEADER"]			= MANAGE_SUBSCRIBERS ." ".strip_tags(html_entity_decode($r['newsletter_label']));
		$variables["PAGE_BUTTONS"] = Array(
			Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME),
//			Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL),
			Array("ADD",$this->module_command."SUBSCRIBER_ADD",ADD_NEW,"subscriber_newsletter=$identifier")
		);
		if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$page = $this->check_parameters($parameters,"page","1");
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
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
			$variables["PAGE_COMMAND"] 		= $this->module_command."SUBSCRIBER_LIST";
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) && ($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
//				$subscriber_group = $r["subscriber_group"];
				$subscriber_group = explode("|",$r["subscriber_group"]);
				$subscriber_group_name = "";
				foreach($subscriber_group as $subscriber_group_id){
					$sql_group = "select subscriber_group_name from newsletter_subscription_group where subscriber_group_identifier=".$subscriber_group_id;
					$result_group = $this->call_command("DB_QUERY",array($sql_group));
					$r_group = $this->call_command("DB_FETCH_ARRAY",array($result_group));
					$subscriber_group_name .= $r_group["subscriber_group_name"].", ";
				}
				$subscriber_group_name = rtrim($subscriber_group_name,", ");
				$variables["RESULT_ENTRIES"][$i]=Array(
				"identifier"		=> $r["subscriber_identifier"],
				"ENTRY_BUTTONS" 	=> Array(),
				"attributes"		=> Array(
						Array(LOCALE_SUBSCRIBER_LABEL,$r["subscriber_name"],"TITLE","NO"),
						Array(LOCALE_SUBSCRIBER_GROUP_LABEL,$subscriber_group_name,"TITLE","NO"),
						Array(LOCALE_EMAIL_SUBSCRIBER,$r["subscriber_email"],"TITLE","NO")
					)
				);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT" , $this->module_command."SUBSCRIBER_EDIT&amp;subscriber_newsletter=".$identifier,	EDIT_EXISTING);

				if($r["subscriber_verified"] == 1){
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("UNSUBSCRIBE"   , $this->module_command."SUBSCRIBER_UNSUBSCRIBE",			UNSUBSCRIBE_EXISTING);
				}else{
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("SUBSCRIBE"   , $this->module_command."SUBSCRIBER_SUBSCRIBE",			SUBSCRIBE_EXISTING);
				}
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE" , $this->module_command."SUBSCRIBER_REMOVE",	REMOVE_EXISTING);
				
			
			}
		}
		$out = $this->generate_list($variables);
		return $out;

	}
	
	function subscriber_remove($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		
		$sql="select *				
				from newsletter_subscription inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_subscription.subscriber_newsletter
				where subscriber_identifier='$identifier' AND subscriber_client = $this->client_identifier";
		
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		
		$sql = "delete from newsletter_subscription where subscriber_identifier = $identifier and subscriber_client = $this->client_identifier ";
		$this->call_command("DB_QUERY",array($sql));				
		
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_SUBSCRIBER_LIST&identifier=".$r["subscriber_newsletter"]));					
	}
	
	function subscriber_subscribe($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		
		$sql="select *				
				from newsletter_subscription inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_subscription.subscriber_newsletter
				where subscriber_identifier='$identifier' AND subscriber_client = $this->client_identifier";
		
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		
		$sql = "update newsletter_subscription SET subscriber_verified=1 where subscriber_identifier = $identifier and subscriber_client = $this->client_identifier ";
		$this->call_command("DB_QUERY",array($sql));				
		
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_SUBSCRIBER_LIST&identifier=".$r["subscriber_newsletter"]));					
	}
	
	function subscriber_unsubscribe($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		
		$sql="select *				
				from newsletter_subscription inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_subscription.subscriber_newsletter
				where subscriber_identifier='$identifier' AND subscriber_client = $this->client_identifier";
		
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		
		$sql = "update newsletter_subscription SET subscriber_verified=0 where subscriber_identifier = $identifier and subscriber_client = $this->client_identifier ";
		$this->call_command("DB_QUERY",array($sql));				
		
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_SUBSCRIBER_LIST&identifier=".$r["subscriber_newsletter"]));					
	}
	
	
	function subscriber_form($parameters){
		$identifier				= $this->check_parameters($parameters,"identifier",-1);
		$subscriber_newsletter  = $this->check_parameters($parameters,"subscriber_newsletter");
		
		$subscriber_email 	= "";
		$subscriber_identifier 	= "";
		$menu_parent	= "";
		$display_tab	= "";
		$label			= LOCALE_ADD_NEW;
		$subscriber_rtf = 1;
		$sql ="select * from newsletter_data where newsletter_identifier=$subscriber_newsletter and newsletter_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		$newsletter_label = $r["newsletter_label"];
			
		$command 		= $this->check_parameters($parameters,"command");
		if ($identifier != -1){
			$label			= LOCALE_EDIT;
			$sql ="select * from newsletter_subscription where subscriber_identifier=$identifier and subscriber_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$subscriber_email		= $r["subscriber_email"];
				$subscriber_name		= $r["subscriber_name"];
				$subscriber_company		= $r["subscriber_company"];
				$subscriber_group		= $r["subscriber_group"];
				$subscriber_rtf			= $r["subscriber_rtf"];
				$from_subscribe			= $r["subscriber_verified"];
				$subscriber_newsletter	= $r["subscriber_newsletter"];
				$subscriber_identifier  = $r["subscriber_identifier"];
			}
		}

		$out="<module name=\"subscription\" display=\"form\">";
		$out .= "<page_options>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));

		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."SUBSCRIBER_LIST&amp;identifier=$subscriber_newsletter",LOCALE_CANCEL));
		$out .= "</page_options>";
		$out .= "<form name=\"newsletter_issue\" method=\"post\" label=\"Newsletter Subscription\">";
		$out .= "	<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>";
		$out .= "	<input type=\"hidden\" name=\"subscriber_newsletter\"><![CDATA[$subscriber_newsletter]]></input>";
		$out .= "	<input type=\"hidden\" name=\"prev_command\"><![CDATA[$command]]></input>";
		$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[NEWSLETTER_SUBSCRIBER_SAVE]]></input>";
		$out .= "	<page_sections>";
		$out .= "		<section label=\"Subscription\"";
			if ($display_tab==""){
				$out .= " selected='true'";
			}
			$out .= ">";

		$out .= "	<input type=\"text\" label=\"".LOCALE_NAME_SUBSCRIBER."\" size=\"255\" name=\"subscriber_name\" required=\"YES\"><![CDATA[$subscriber_name]]></input>";
		$out .= "	<input type=\"text\" label=\"".LOCALE_EMAIL_SUBSCRIBER."\" size=\"255\" name=\"subscriber_email\" required=\"YES\"><![CDATA[$subscriber_email]]></input>";
		$out .= "	<input type=\"text\" label=\"".LOCALE_COMPANY_SUBSCRIBER."\" size=\"255\" name=\"subscriber_company\"><![CDATA[$subscriber_company]]></input>";
		//$out .= "	<select label='".LOCALE_SUBSCRIBER_GROUP_LABEL."' name='subscriber_group'>";
		//				$out .= "<option value='0'> Select One </option>";
		$out.="<checkboxes label='Choose from the ".LOCALE_SUBSCRIBER_GROUP_LABEL."' name='subscriber_group'>";
					$sql_group ="select * from newsletter_subscription_group where subscriber_group_newsletter=$subscriber_newsletter and subscriber_group_client=$this->client_identifier";
					$result_group = $this->call_command("DB_QUERY",array($sql_group));
					while ($r_group = $this->call_command("DB_FETCH_ARRAY",array($result_group))){
						$db_subscriber_group_id = $r_group["subscriber_group_identifier"];
						$db_subscriber_group_name = $r_group["subscriber_group_name"];
						
						$out .= "<option value='$db_subscriber_group_id'";
								$subscriber_group_ids = explode("|",$subscriber_group);
								if (in_array($db_subscriber_group_id,$subscriber_group_ids))
										$out .= " selected ='true'";
								$out .= 				">".$db_subscriber_group_name."
								</option>";
					}
		$out.="</checkboxes>";
		//$out .= "</select>";

		$out .= "	<radio label='".LOCALE_EMAIL_SUBSCRIPTION_FORMATTING."' name='subscriber_rtf'>
					<option value='0'";
					if ($subscriber_rtf==0){
						$out .= " selected ='true'";
					}
					$out .= 				">".LOCALE_PLAINTEXT."</option>
					<option value='1'";
					if ($subscriber_rtf==1){
						$out .= " selected ='true'";
					}
					$out .= 				">".LOCALE_RICHTEXT."</option>
		</radio>";
		
		$out .= "		</section>";
		$out .= "	</page_sections>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" />";
		$out .= "</form></module>";
	return $out;
	}

	function subscriber_save($parameters){
		$identifier					= $this->check_parameters($parameters,"identifier",-1);
		$from_email					= $this->check_parameters($parameters,"subscriber_email");
		$subscriber_name			= $this->check_parameters($parameters,"subscriber_name");
		$subscriber_company			= $this->check_parameters($parameters,"subscriber_company");
		$subscriber_rtf				= $this->check_parameters($parameters,"subscriber_rtf");
		$subscriber_newsletter		= $this->check_parameters($parameters,"subscriber_newsletter");
		$subscriber_group			= $this->check_parameters($parameters,"subscriber_group",0);
		//$newsletter_date			= $this->libertasGetDate("Y/m/d H:i:s");
		if(is_array($subscriber_group)){
			$subscriber_ids = "";
			foreach($subscriber_group as $val)
				$subscriber_ids .= $val."|";
			$subscriber_group = rtrim($subscriber_ids,"|");
		}
		if ($identifier==-1){
			$sql = "insert into newsletter_subscription (
						subscriber_client, 
						subscriber_email, 
						subscriber_name, 
						subscriber_company, 
						subscriber_verified,
						subscriber_rtf,
						subscriber_group,
						subscriber_newsletter
					) values (
						'$this->client_identifier', 
						'$from_email', 
						'$subscriber_name', 
						'$subscriber_company', 
						1,
						'$subscriber_rtf', 
						'$subscriber_group',
						$subscriber_newsletter
					)";

		}else{
			$sql = "update newsletter_subscription set  
						subscriber_email='$from_email', 
						subscriber_name='$subscriber_name', 
						subscriber_company='$subscriber_company', 
						subscriber_group='$subscriber_group', 
						subscriber_rtf=$subscriber_rtf
					where
						subscriber_identifier=$identifier and 
						subscriber_client='$this->client_identifier'";
		}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."SUBSCRIBER_LIST&identifier=$subscriber_newsletter"));						
	}

	/*End Modification By Ali Imran*/
	
	/*** Starts Subscriber Group entry/edit form (Function added by Muhammad Imran )*/
	function subscriber_group_form($parameters){
		$identifier				= $this->check_parameters($parameters,"identifier",-1);
		$subscriber_group_newsletter  = $this->check_parameters($parameters,"subscriber_group_newsletter");
		
		$subscriber_group_identifier 	= "";
		$menu_parent					= "";
		$display_tab					= "";
		$label							= LOCALE_ADD_NEW;
		
		$sql ="select * from newsletter_data where newsletter_identifier=$subscriber_newsletter and newsletter_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		$newsletter_label = $r["newsletter_label"];
			
		$command 		= $this->check_parameters($parameters,"command");
		if ($identifier != -1){
			$label			= LOCALE_EDIT;
			$sql ="select * from newsletter_subscription_group where subscriber_group_identifier=$identifier and subscriber_group_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$subscriber_group_name				= $r["subscriber_group_name"];
				$subscriber_group_active			= $r["subscriber_group_active"];
				$subscriber_group_newsletter		= $r["subscriber_group_newsletter"];
				$subscriber_group_identifier  		= $r["subscriber_group_identifier"];
			}
		}

		$out="<module name=\"subscription_group\" display=\"form\">";
		$out .= "<page_options>";

		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."SUBSCRIBER_GROUP_LIST&amp;identifier=$subscriber_group_newsletter",LOCALE_CANCEL));
		$out .= "</page_options>";
		$out .= "<form name=\"newsletter_subscription_group\" method=\"post\" label=\"Newsletter Subscription Group\">";
		$out .= "	<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>";
		$out .= "	<input type=\"hidden\" name=\"subscriber_group_newsletter\"><![CDATA[$subscriber_group_newsletter]]></input>";
		$out .= "	<input type=\"hidden\" name=\"prev_command\"><![CDATA[$command]]></input>";
		$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[NEWSLETTER_SUBSCRIBER_GROUP_SAVE]]></input>";
		$out .= "	<page_sections>";
		$out .= "		<section label=\"Subscription Group\"";
			if ($display_tab==""){
				$out .= " selected='true'";
			}
			$out .= ">";

		$out .= "	<input type=\"text\" label=\"".LOCALE_NAME_SUBSCRIBER_GROUP."\" size=\"255\" name=\"subscriber_group_name\" required=\"YES\"><![CDATA[$subscriber_group_name]]></input>";
/*		$out .= "	<radio label='".LOCALE_EMAIL_SUBSCRIPTION_FORMATTING."' name='subscriber_rtf'>
					<option value='0'";
					if ($subscriber_rtf==0){
						$out .= " selected ='true'";
					}
					$out .= 				">".LOCALE_PLAINTEXT."</option>
					<option value='1'";
					if ($subscriber_rtf==1){
						$out .= " selected ='true'";
					}
					$out .= 				">".LOCALE_RICHTEXT."</option>
		</radio>";
*/		
		$out .= "		</section>";
		$out .= "	</page_sections>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" />";
		$out .= "</form></module>";
	return $out;
	}
	/*** Ends Subscriber Group entry/edit form (Function added by Muhammad Imran )*/

	/*** Starts Subscriber Group entry/edit save (Function added by Muhammad Imran )*/
	function subscriber_group_save($parameters){
		$identifier						= $this->check_parameters($parameters,"identifier",-1);
		$subscriber_group_name			= $this->check_parameters($parameters,"subscriber_group_name");
		$subscriber_group_newsletter	= $this->check_parameters($parameters,"subscriber_group_newsletter");
		//$newsletter_date				= $this->libertasGetDate("Y/m/d H:i:s");
		
		if ($identifier==-1){
			$sql = "insert into newsletter_subscription_group (
						subscriber_group_client, 
						subscriber_group_name, 
						subscriber_group_newsletter
					) values (
						'$this->client_identifier', 
						'$subscriber_group_name', 
						$subscriber_group_newsletter
					)";

		}else{
			$sql = "update newsletter_subscription_group set  
						subscriber_group_name='$subscriber_group_name'
					where
						subscriber_group_identifier=$identifier and 
						subscriber_group_client='$this->client_identifier'";
		}

		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."SUBSCRIBER_GROUP_LIST&identifier=$subscriber_group_newsletter"));						
	}
	/*** Ends Subscriber Group entry/edit save (Function added by Muhammad Imran )*/

	/*** Starts Subscriber Group List (Function added by Muhammad Imran )*/
	function subscriber_group_list($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		$sql="select *				
				from newsletter_subscription_group inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_subscription_group.subscriber_group_newsletter
				where subscriber_group_newsletter='$identifier' AND subscriber_group_client = $this->client_identifier order by newsletter_subscription_group.subscriber_group_name";
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		$result = $this->call_command("DB_QUERY",array($sql));
				
		$variables = Array();
		$variables["FILTER"]			= "";// $this->filter($parameters);
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["as"]				= "table";
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		
		$variables["HEADER"]			= MANAGE_SUBSCRIBERS_GROUP ." ".strip_tags(html_entity_decode($r['newsletter_label']));

		$variables["PAGE_BUTTONS"] = Array(
			Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME),
			Array("NEWSLETTER_SET_DEFAULT_GROUP",$this->module_command."SET_DEFAULT_GROUP",LOCALE_NEWSLETTER_SET_DEFAULT_GROUP,"identifier=$identifier"),
//			Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL),
			Array("ADD",$this->module_command."SUBSCRIBER_GROUP_ADD",ADD_NEW,"subscriber_group_newsletter=$identifier")
		);
		if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$page = $this->check_parameters($parameters,"page","1");
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
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
			$variables["PAGE_COMMAND"] 		= $this->module_command."SUBSCRIBER_GROUP_LIST";
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) && ($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				
				$variables["RESULT_ENTRIES"][$i]=Array(
				"identifier"		=> $r["subscriber_group_identifier"],
				"ENTRY_BUTTONS" 	=> Array(),
				"attributes"		=> Array(
						Array(LOCALE_SUBSCRIBER_GROUP_LABEL,$r["subscriber_group_name"],"TITLE","NO")
					)
				);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT" , $this->module_command."SUBSCRIBER_GROUP_EDIT&amp;subscriber_group_newsletter=".$identifier,	EDIT_EXISTING);

/*				if($r["subscriber_verified"] == 1){
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("UNSUBSCRIBE"   , $this->module_command."SUBSCRIBER_UNSUBSCRIBE",			UNSUBSCRIBE_EXISTING);
				}else{
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("SUBSCRIBE"   , $this->module_command."SUBSCRIBER_SUBSCRIBE",			SUBSCRIBE_EXISTING);
				}
*/
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE" , $this->module_command."SUBSCRIBER_GROUP_REMOVE",	REMOVE_EXISTING);
				
			
			}
		}
		$out = $this->generate_list($variables);
		return $out;

	}
	/*** Ends Subscriber Group List (Function added by Muhammad Imran )*/

	/*** Starts Subscriber Group Delete (Function added by Muhammad Imran )*/

	function subscriber_group_remove($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		
		$sql="select *				
				from newsletter_subscription_group inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_subscription_group.subscriber_group_newsletter
				where subscriber_group_identifier='$identifier' AND subscriber_group_client = $this->client_identifier";
		
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		
		$sql = "delete from newsletter_subscription_group where subscriber_group_identifier = $identifier and subscriber_group_client = $this->client_identifier ";
		$this->call_command("DB_QUERY",array($sql));				

		$sql = "delete from newsletter_subscription where subscriber_group = $identifier and subscriber_client = $this->client_identifier ";
		$this->call_command("DB_QUERY",array($sql));				
		
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_SUBSCRIBER_GROUP_LIST&identifier=".$r["subscriber_group_newsletter"]));					
	}

	function select_subscriber_group($parameters){
		$identifier				= $this->check_parameters($parameters,"identifier",-1);
		$page					= $this->check_parameters($parameters,"page",1);
		$subscriber_newsletter  = $this->check_parameters($parameters,"newsletter");
/*
		
		$subscriber_group_identifier 	= "";
		$menu_parent					= "";
		$display_tab					= "";
		$label							= LOCALE_ADD_NEW;
		
		$sql ="select * from newsletter_data where newsletter_identifier=$subscriber_newsletter and newsletter_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		$newsletter_label = $r["newsletter_label"];
			
		$command 		= $this->check_parameters($parameters,"command");
		if ($identifier != -1){
			$label			= LOCALE_EDIT;
			$sql ="select * from newsletter_subscription_group where subscriber_group_identifier=$identifier and subscriber_group_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$subscriber_group_name				= $r["subscriber_group_name"];
				$subscriber_group_active			= $r["subscriber_group_active"];
				$subscriber_group_newsletter		= $r["subscriber_group_newsletter"];
				$subscriber_group_identifier  		= $r["subscriber_group_identifier"];
			}
		}
*/
		$out="<module name=\"subscription_group\" display=\"form\">";
		$out .= "<page_options>";

		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST_ISSUES&amp;identifier=$subscriber_newsletter",LOCALE_CANCEL));
		$out .= "</page_options>";
		$out .= "<form name=\"newsletter_subscription_group\" method=\"post\" label=\"Newsletter Subscriber Group\">";
		$out .= "	<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>";
		$out .= "	<input type=\"hidden\" name=\"newsletter\"><![CDATA[$subscriber_newsletter]]></input>";
		$out .= "	<input type=\"hidden\" name=\"prev_command\"><![CDATA[$command]]></input>";
		$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[NEWSLETTER_SEND_TO_SUBSCRIBERS]]></input>";

		$out .= "	<page_sections>";
		$out .= "		<section label=\"Subscriber Group\">";

		$out .= "	<checkboxes label='".LOCALE_SUBSCRIBER_GROUP_LABEL."' name='subscriber_group' required='true'>";
		
		$sql ="select newsarchive_group from newsletter_archive where newsarchive_identifier=$identifier and newsarchive_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		$newsarchive_group = $r["newsarchive_group"];
		$newsarchive_group_arr = split(",",$newsarchive_group);
		if (in_array("0",$newsarchive_group_arr))
			$already_sent = ' ( Already Sent )';
		else
			$already_sent = '';

		$out .= "	<input type=\"hidden\" name=\"already_sent_values\"><![CDATA[$newsarchive_group]]></input>";
		
		$out .= "	<option value='0'>Non Grouped".$already_sent."</option>";

					$sql_group ="select * from newsletter_subscription_group where subscriber_group_newsletter=$subscriber_newsletter and subscriber_group_client=$this->client_identifier";
					$result_group = $this->call_command("DB_QUERY",array($sql_group));
					while ($r_group = $this->call_command("DB_FETCH_ARRAY",array($result_group))){
						$db_subscriber_group_id = $r_group["subscriber_group_identifier"];
						$db_subscriber_group_name = $r_group["subscriber_group_name"];
						
						if (in_array($db_subscriber_group_id,$newsarchive_group_arr))
							$already_sent = ' ( Already Sent )';
						else
							$already_sent = '';
						
						$out .= "<option value='$db_subscriber_group_id'";
								if ($db_subscriber_group_id == $subscriber_group){
									$out .= " selected ='true'";
								}
								$out .= 				">".$db_subscriber_group_name.$already_sent."
								</option>";
					}
		$out .= "</checkboxes>";

		$out .= "		</section>";
		$out .= "	</page_sections>";
		$out .= "<input type=\"submit\" iconify=\"PUBLISH\" />";
		$out .= "</form></module>";
	return $out;
	}

	/*** Ends Subscriber Group Delete (Function added by Muhammad Imran )*/
/* Subscribers Import Addresses from file by Ali Imran*/
	function subscriber_import_form($parameters){
		$identifier				= $this->check_parameters($parameters,"identifier",-1);
		//$subscriber_newsletter  = $this->check_parameters($parameters,"subscriber_newsletter");
		
		$subscriber_file 	= "";
		
		$out="<module name=\"subscription\" display=\"form\">";
		$out .= "<page_options>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));

		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."SUBSCRIBER_LIST&amp;identifier=$subscriber_newsletter",LOCALE_CANCEL));
		$out .= "</page_options>";
		$out .= "<form name=\"newsletter_issue\" method=\"post\" label=\"Newsletter Subscription\">";
		$out .= "	<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>";
		$out .= "	<input type=\"hidden\" name=\"prev_command\"><![CDATA[$command]]></input>";
		$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[NEWSLETTER_IMPORT_SAVE]]></input>";
		$out .= "	<page_sections>";
		$out .="			<section label='Import details'>";
/*
		echo $sql="select *				
				from newsletter_subscription_group inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_subscription_group.subscriber_group_newsletter
				where subscriber_group_newsletter='$identifier' AND subscriber_group_client = $this->client_identifier order by newsletter_subscription_group.subscriber_group_name";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
/*			$subscriber_email		= $r["subscriber_email"];
			$subscriber_name		= $r["subscriber_name"];
			$subscriber_company		= $r["subscriber_company"];
			$subscriber_group		= $r["subscriber_group"];
			$subscriber_rtf			= $r["subscriber_rtf"];
			$from_subscribe			= $r["subscriber_verified"];
			$subscriber_newsletter	= $r["subscriber_newsletter"];
			$subscriber_identifier  = $r["subscriber_identifier"];
*/
/*			$subscriber_group_identifier  = $r["subscriber_group_identifier"];
			$subscriber_group_name  = $r["subscriber_group_name"];

		}
*/
		$out.="<select label='Choose from the ".LOCALE_SUBSCRIBER_GROUP_LABEL."' name='subscriber_group'>";
					$sql_group ="select * from newsletter_subscription_group where subscriber_group_newsletter=$identifier and subscriber_group_client=$this->client_identifier";
					$result_group = $this->call_command("DB_QUERY",array($sql_group));
					while ($r_group = $this->call_command("DB_FETCH_ARRAY",array($result_group))){
						$db_subscriber_group_id = $r_group["subscriber_group_identifier"];
						$db_subscriber_group_name = $r_group["subscriber_group_name"];
						
						$out .= "<option value='$db_subscriber_group_id'";
								$subscriber_group_ids = explode("|",$subscriber_group);
								if (in_array($db_subscriber_group_id,$subscriber_group_ids))
										$out .= " selected ='true'";
								$out .= 				">".$db_subscriber_group_name."
								</option>";
					}
		$out.="</select>";

		$out .="				<text><![CDATA[Please select an Excell file with columns Name and Email to Import.]]></text>";
			$out .="				<input type='file' name='import_file_format_tab' label='File to import'/>";
		
		$out .= "		</section>";
		$out .= "	</page_sections>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" />";
		$out .= "</form></module>";
	return $out;
	}
	
	//Import File
	function subscriber_importfile($parameters){
//		print_r($parameters);
		$identifier				= $this->check_parameters($parameters,"identifier");
		$subscriber_group			= $this->check_parameters($parameters,"subscriber_group",0);
		$f = $this->check_parameters($_FILES,"import_file_format_tab", "__NOT_FOUND__");
		$import_into_directory	 = $identifier;
//		print "[$f ,$ifs ,$import_into_directory]";
		$tmp_name = $f["tmp_name"];
		if(file_exists($tmp_name)){
			/*************************************************************************************************************************
            * remove old file if it exists (escaped previous upload)
            *************************************************************************************************************************/
			if(file_exists($this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.xls")){
				$um =umask(0);
				@chmod($this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.xls", LS__FILE_PERMISSION);
				umask($um);
				@unlink($this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.xls");
			}
			move_uploaded_file($tmp_name, $this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.xls");
			$csvfile = $this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.xls";
			$quries = "";
			require_once(dirname(__FILE__)."/libertas.reader.php");
			if(file_exists($csvfile) && filesize($csvfile)) {
				$data = new Spreadsheet_Excel_Reader();
				$data->setOutputEncoding('CP1251');
				$data->read($csvfile);
				for ($i = 1; $i <= $data->sheets[0]['numRows']; $i++) {
					if ($data->sheets[0]['cells'][$i][2] != "" && $data->sheets[0]['cells'][$i][2] != "Email"){
						$sql = 'insert into newsletter_subscription (
							subscriber_client, 
							subscriber_email, 
							subscriber_name,
							subscriber_verified, 
							subscriber_group,
							subscriber_newsletter
						) values (
							'.$this->client_identifier.', 
							"'.$data->sheets[0][cells][$i][2].'", 
							"'.$data->sheets[0][cells][$i][1].'",
							1, 
							'.$subscriber_group.',
							 '.$identifier.'	
						)';	
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
						$this->call_command("DB_QUERY",array($sql));
					}//#if($data->sheets[0]['cells'][$i][2] != "")
				}
			}
		}
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."IMPORT_CONFIRMED"));
		die();
	}
	
	function subscriber_import_confirmed($parameters){
			$out="<module name=\"confirm_subscription\" display=\"form\">";
			$out .= "<page_options>";
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));
	
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
			$out .= "</page_options>";
			$out .= "<form name=\"newsletter_issue\" method=\"post\" label=\"Newsletter Subscription\">";
			//$out .= "	<input type=\"hidden\" name=\"prev_command\"><![CDATA[$command]]></input>";
			//$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[NEWSLETTER_IMPORT_SAVE]]></input>";
			$out .= "	<page_sections>";
			$out .="			<section label='Import Done'>";
	
			$out .="				<text><![CDATA[Addresses are Imported Successfully]]></text>";
			
			$out .= "		</section>";
			$out .= "	</page_sections>";
			$out .= "</form></module>";
			return $out;
	}
	/* End Subscribers Import Addresses from file by Ali Imran*/
	
	/* Starts Set Default Group to Subscribe (Function added by Muhammad Imran 11-02-2008)*/

	function set_default_group($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,""));
		}

//		$list_layouts = $this->call_command("WEBOBJECTS_LIST_LAYOUTS");

		$meta_type		= "";
//		$sql = "select * from web_layouts where smd_client= $this->client_identifier";

/*		$sql = "Select wol_identifier from web_layouts 
					left outer join theme_data on wol_theme = theme_identifier and (theme_secure = 0 or theme_secure = wol_client)
					where wol_client = $this->client_identifier and wol_default = 1";
*/
		$sql="select *				
				from newsletter_subscription_group inner join newsletter_data on newsletter_data.newsletter_identifier = newsletter_subscription_group.subscriber_group_newsletter
				where subscriber_group_newsletter='$identifier' AND subscriber_group_client = $this->client_identifier order by newsletter_subscription_group.subscriber_group_name";

		$result = $this->parent->db_pointer->database_query($sql);
		/*
		if ($result){
			$r= $this->parent->db_pointer->database_fetch_array($result);
			$group_identifier	= $r["subscriber_group_identifier"];
		}
		*/
//		$out	 = "<module name=\"$this->module_name\" display=\"form\"><page_options><button command=\"ENGINE_SPLASH\" alt=\"LOCALE_CANCEL\" iconify=\"CANCEL\" /></page_options>\n";

		$out	 = "<module name=\"$this->module_name\" display=\"form\">\n";

		$out .= "<page_options>";

		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEWSLETTER_HOME",$this->module_command."LIST",LOCALE_NEWSLETTER_HOME));
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."SUBSCRIBER_GROUP_LIST&amp;identifier=$identifier",LOCALE_CANCEL));
		$out .= "</page_options>";
		
		$out	.= "<form name=\"set_default_group_frm\" label=\"".LOCALE_SET_DEFAULT_GROUP."\" method=\"POST\" width=\"100%\">\n";
		$out	.= "<input name=\"command\" type=\"hidden\" value=\"".$this->module_command."SET_DEFAULT_GROUP_SAVE\"/>\n";
		$out	.= "<input name=\"newsletter_identifier\" type=\"hidden\" value=\"".$identifier."\"/>\n";

			$out .= "\t\t\t\t\t<select required= \"YES\" name=\"choose_group\" label=\"".LOCALE_CHOOSE_GROUP_LABEL."\" >\n";
			$out  .= "<option value='-1'>Select one</option>";
	
	//		print_r($list_layouts);
	//		echo '<br><br>';
	//		echo $list_layouts[0]['info_identifier'];
	
			$i=0;
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))) {
				if ($r["subscriber_group_identifier"] != ""){
					$out  .= "<option value='".$r["subscriber_group_identifier"]."'";
	
					if ($r["subscriber_group_default"] == 1)
						$out .= " selected='true'";
	
					$out  .= ">".$r["subscriber_group_name"]."</option>";
					$i++;
				}
			}
/*			while (list(, $value) = each($list_layouts)) {
				if ($list_layouts[$i][group_identifier] != ""){
					$out  .= "<option value='".$list_layouts[$i][group_identifier]."'";
	
					if ($list_layouts[$i]['group_identifier'] == $group_identifier)
						$out .= " selected='true'";
	
					$out  .= ">".$list_layouts[$i][group_label]."</option>";
					$i++;
				}
			}
*/			$out .= "\t\t\t\t\t</select>\n";

		$out	.= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>\n";
		$out	.= "</form>\n";
		$out	.= "</module>";
		return $out;
	}
	/* Ends Set Default Group to Subscribe (Function added by Muhammad Imran 11-02-2008)*/
	
	/* Starts Set Default Group save to Subscribe (Function added by Muhammad Imran 11-02-2008)*/
	function set_default_group_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,""));
		}
		$choose_group	= $this->check_parameters($parameters,"choose_group","0");
		$newsletter_identifier	= $this->check_parameters($parameters,"newsletter_identifier");
		
		$sql = "
			update 
				newsletter_subscription_group 
			set 
				subscriber_group_default ='0'
			where
				subscriber_group_client = $this->client_identifier and 
				subscriber_group_newsletter = $newsletter_identifier and 
				subscriber_group_default=1";
		$result = $this->parent->db_pointer->database_query($sql);
		$sql = "
			update 
				newsletter_subscription_group 
			set 
				subscriber_group_default =1
			where
				subscriber_group_client = $this->client_identifier and 
				subscriber_group_newsletter = $newsletter_identifier and 
				subscriber_group_identifier=$choose_group;";
		$result = $this->parent->db_pointer->database_query($sql);
/*
		$out	 = "<module name=\"$this->module_name\" display=\"form\"><page_options><button command=\"SYSPREFS_SYSTEM_METADATA\" alt=\"LOCALE_CANCEL\" iconify=\"CANCEL\" /></page_options>\n";
		$out	.= "<form name=\"sys_prefs\" label=\"".LOCALE_META_DEFAULT_FORM_CONFIRM."\" method=\"POST\">\n";
		$out	.= "<text><![CDATA[".LOCALE_META_SAVE_CONFIRM."]]></text>\n";
		$out	.= "</form>\n";
		$out	.= "</module>";
	return $out;
*/
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=NEWSLETTER_SET_DEFAULT_GROUP&identifier=".$newsletter_identifier));
	}
	/* Ends Set Default Group save to Subscribe (Function added by Muhammad Imran 11-02-2008)*/

}

?>