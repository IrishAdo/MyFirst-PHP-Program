<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.users.php
* @date 09 Oct 2002
*/
/**
* this module should allow the engine to authenticate users when they
* log into the system
*/
class users extends module{
	/**
	*  Class Variables
	*/
	var $module_name				= "users";
	var $module_name_label			= "User Management Tool (DUAL)";
	var $module_label				= "MANAGEMENT_USER";
	var $module_admin				= "1";
	var $module_debug				= false;
	var $module_creation			= "13/09/2002";
	var $module_modify	 			= '$Date: 2005/02/21 16:35:45 $';
	var $module_version 			= '$Revision: 1.34 $';
	var $module_command				= "USERS_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "USERS_";
	var $has_module_contact			= 0;
	var $has_module_group			= 0;
	var $have_displayed_login_form	= 0;
	var $display_options			= null;

	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_SECURITY";
	var $module_admin_options 		= array();
	var $module_admin_user_access 	= array();
	var $module_display_options 	= array();

	var $available_forms 			= "";
	
	/**
	* SPECIAL PAGES
	*
	* Each special page will call a specific function as defined here
	*/
	var $specialPages			 	= array(
		array("-login.php"							,"USERS_SHOW_LOGIN"							,"HIDDEN",	"Login"),
		array("-forgot-username.php"				,"USERS_SHOW_GET_USERNAME_FORM"				,"HIDDEN",	"Forgot Username"),
		array("-forgot-password.php"				,"USERS_SHOW_GET_PWD_FORM"					,"HIDDEN",	"Forgotten Password"),
		array("-logout.php"							,"ENGINE_LOGOUT"							,"HIDDEN",	"Logout"),
		array("-join-now.php"						,"USERS_SHOW_REGISTER"						,"VISIBLE",	"Join Now"),
		array("-profile.php"						,"USERS_SHOW_PROFILE_FORM"					,"VISIBLE",	"Profile")
	);
	
	var $override_forms = array(
		array("-join-now.php"						,"USERS_SHOW_REGISTER"						,"VISIBLE",	"Join Now"),
		array("-profile.php"						,"USERS_SHOW_PROFILE_FORM"					,"VISIBLE",	"Profile")
	);
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
			if ($user_command==$this->module_command."GET_AUTHOR"){
				return $this->get_module_author();
			}
			if ($user_command==$this->module_command."GET_CREATION"){
				return $this->get_module_creation();
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."SPECIAL_PAGES"){
				return $this->specialPages;
			}
			if ($user_command==$this->module_command."OVERRIDE_FORMS"){
				return $this->override_forms;
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			if (($user_command==$this->module_command."CACHE_AVAILABLE_FORM") || ($user_command==$this->module_command."RESTORE")){
				return $this->cache_available_forms($parameter_list);
			}
			if ($user_command==$this->module_command."LIST_FORMS"){
				return $this->available_forms;
			}
			if ($user_command==$this->module_command."LOGIN"){
				return $this->user_login($parameter_list);
			}
			if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN")==1){
				if (($user_command==$this->module_command."SHOW_PROFILE_FORM") || ($user_command==$this->module_command."SHOW_PROFILE_FORM_CACHE")){
					return $this->show_profile($parameter_list);
				}
			}
			if (($user_command==$this->module_command."SHOW_LOGIN") || ($user_command==$this->module_command."CHECK_LOGIN")){
				//		print $user_command;
				if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==0){
					$out = $this->check_login($parameter_list);
					if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN")==1){
//						print "referesh";
						$this->call_command("ENGINE_REFRESH_BUFFER");
					} else {
						return $out;
					}
				} else {
					$parameter_list["ok"]=0;
					$parameter_list["show_anyway"]=0;//$this->check_parameters($parameter_list, "show_anyway",1);
					return $this->show_login($parameter_list);
				}
			}
			if ($user_command==$this->module_command."SHOW_LOGIN_CACHE"){
				return $this->show_login($parameter_list);
			}
			if ($user_command==$this->module_command."DRAW_FILTER"){
				return $this->user_filter($parameter_list[0],$parameter_list[1]);
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."SET_ADMIN_ACCESS"){
				return $this->user_admin_access($parameter_list);
			}
			if ($user_command==$this->module_command."GET_USER_PREFERENCE"){
				return $this->user_pref($parameter_list,$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER"));
			}
			if (($user_command==$this->module_command."SHOW_GET_PWD_FORM") || ($user_command==$this->module_command."SHOW_GET_PWD_FORM_CACHE")){
				return $this->get_pwd($parameter_list);
			}
			if (($user_command==$this->module_command."SHOW_GET_USERNAME_FORM") || ($user_command==$this->module_command."SHOW_GET_USERNAME_FORM_CACHE")){
				return $this->get_user_name($parameter_list);
			}
			if (($user_command==$this->module_command."SHOW_REGISTER")||($user_command==$this->module_command."SHOW_REGISTER_CACHE")){
				return $this->register_user($parameter_list);
			}
			if ($user_command==$this->module_command."REGISTRATION_SAVE"){
				return $this->register_user_save($parameter_list);
			}
			if ($user_command==$this->module_command."PROFILE_SAVE"){
				return $this->profile_user_save($parameter_list);
			}
			if ($user_command==$this->module_command."RETRIEVE_PWD"){
				return $this->get_pwd_confirm($parameter_list);
			}
			if ($user_command==$this->module_command."RETRIEVE_USERNAME"){
				return $this->get_user_name_confirm($parameter_list);
			}
			if ($user_command==$this->module_command."VALIDATE"){
				return $this->validate_user($parameter_list);
			}
			if ($user_command==$this->module_command."EXPORT"){
				return $this->export($parameter_list);
			}
			if ($user_command==$this->module_command."RENEWAL"){
				return $this->renewal($parameter_list);
			}
			if ($this->module_admin_access){
				if ($user_command==$this->module_command."MY_WORKSPACE"){
					return $this->retrieve_my_docs($parameter_list);
				}
				if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
					return $this->user_form($parameter_list);
				}
				if ($user_command==$this->module_command."STATUS_RETRIEVE"){
					return $this->get_status($parameter_list[0]);
				}
				if ($user_command==$this->module_command."SAVE"){
					$out = $this->save($parameter_list);
					if (strlen($out)>0){
						return $out;
					}else{
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=USERS_LIST"));
					}
				}
				if ($user_command==$this->module_command."REMOVE"){
					return  $this->remove_user_screen(@$parameter_list["identifier"]);
				}
				if ($user_command==$this->module_command."REMOVE_CONFIRM"){
					$this->remove_confirm(@$parameter_list["identifier"]);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=USERS_LIST"));
				}
				/*	Added by: By Imran Mirza	*/
				if ($user_command==$this->module_command."REMOVE_CONFIRM_MEMBER"){
					$this->remove_confirm(@$parameter_list["identifier"]);
				}
				if ($user_command==$this->module_command."UPDATE_ACCESS"){
					$this->user_admin_access_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=USERS_LIST"));
				}
				if ($user_command==$this->module_command."LIST"){
					return $this->user_list($parameter_list);
				}
				if ($user_command==$this->module_command."LIST_FORMS"){
					return $this->list_forms();
				}
				
			}
			if ($user_command==$this->module_command."CREATE_NEW_CLIENT_DETAILS"){
				return $this->create_client_details($parameter_list);
			}
			if ($user_command==$this->module_command."LIST_FORM_FIELD_ACCESS"){
				return $this->list_available_fields();
			}
            /*************************************************************************************************************************
            *                        A D V A N C E D   F O R M   B U I L D E R   F O R M   F U N C T I O N S
            *************************************************************************************************************************/
			if ($user_command == $this->module_command."GET_FIELD_LIST"){
				return $this->get_field_list($parameter_list);
			}
			if ($user_command == $this->module_command."GET_FIELD_OPTIONS"){
				return $this->get_field_options($parameter_list);
			}
            if($user_command==$this->module_command."SAVE_FBA"){
                return $this->save_fba($parameter_list);
            }
            if($user_command==$this->module_command."LOAD_FBA"){
                return $this->load_fba($parameter_list);
            }
		}else{
			return "";// wrong command sent to system
		}
	}
	
	function list_available_fields(){
		$data = array();
			$data[count($data)] = Array("USERS_SHOW_REGISTER",
				Array(
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_job_title', 'LOCALE_CONTACT_JOB_TITLE', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_company', 'LOCALE_CONTACT_COMPANY', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_web_site', 'LOCALE_CONTACT_WEB_URL', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_first_name', 'LOCALE_CONTACT_FIRST_NAME', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_initials', 'LOCALE_CONTACT_INITIALS', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_last_name', 'LOCALE_CONTACT_SURNAME', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_email', 'LOCALE_CONTACT_EMAIL_ADDRESS', '1', '1');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_telephone', 'LOCALE_CONTACT_PHONE', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_fax', 'LOCALE_CONTACT_FAX', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_address1', 'LOCALE_ADDRESS1', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_address2', 'LOCALE_ADDRESS2', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_address3', 'LOCALE_ADDRESS3', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_city', 'LOCALE_ADDRESS_CITY', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_county', 'LOCALE_ADDRESS_COUNTY', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_country', 'LOCALE_ADDRESS_COUNTRY', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_postcode', 'LOCALE_ADDRESS_POSTCODE', '1', '0');\";"
				)
			);
			return $data;
	}
	function create_client_details($parameters){
		$client_identifier = $this->check_parameters($parameters,"client_identifier",-1);
		/**
		* insert available_fields data
		*/
		//		$length_array = count($data);
//		$length_array=0;
//		for ($index=0; $length_array > $index;$index++){
//			$this->call_command("DB_QUERY",array($data[$index]));
//		}

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
		
		if (
			($this->parent->server[LICENCE_TYPE]==ECMS) || 
			($this->parent->server[LICENCE_TYPE]==MECM)
		){
			$this->available_forms = array("USERS_SHOW_PROFILE_FORM", "USERS_SHOW_REGISTER", "USERS_SHOW_GET_PWD_FORM", "USERS_SHOW_GET_USERNAME_FORM", "USERS_SHOW_LOGIN");
		}
		
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* we require access to the following two modules
		*/
		$this->has_module_contact 	= $this->call_command("ENGINE_HAS_MODULE",array("CONTACT_"));
		$this->has_module_group 	= $this->call_command("ENGINE_HAS_MODULE",array("GROUP_"));
		
		/**
		* define the filtering information that is available
		*/
		$this->display_options		= array(
		array (0,FILTER_ORDER_DATE_NEWEST	 ,"user_creation_date desc"),
		array (1,FILTER_ORDER_DATE_OLDEST	 ,"user_creation_date Asc"),
		array (2,FILTER_ORDER_USERNAME_A_Z ,"user_login_name asc"),
		array (3,FILTER_ORDER_USERNAME_Z_A ,"user_login_name asc")
		);
		
		/**
		* if we have access to the contact module then add these filters to the order filter
		*/
		if ($this->has_module_contact==1){
			$this->display_options[count($this->display_options)]=array (4,FILTER_ORDER_FIRST_NAME_A_Z	,"contact_first_name asc,contact_last_name asc");
			$this->display_options[count($this->display_options)]=array (5,FILTER_ORDER_FIRST_NAME_Z_A	,"contact_first_name desc,contact_last_name asc");
			$this->display_options[count($this->display_options)]=array (6,FILTER_ORDER_SURNAME_A_Z	,"contact_last_name asc, contact_first_name asc");
			$this->display_options[count($this->display_options)]=array (7,FILTER_ORDER_SURNAME_Z_A	,"contact_last_name desc, contact_first_name asc");
		}
		/**
		* if we have access to the group module then add these filters to the order filter
		*/
		if ($this->has_module_group==1){
			$this->display_options[count($this->display_options)]=array (4,FILTER_ORDER_GROUP_NAME_A_Z	,"group_label asc");
			$this->display_options[count($this->display_options)]=array (5,FILTER_ORDER_GROUP_NAME_Z_A	,"group_label desc");
		}
		

		$this->module_admin_options = array(
			array("USERS_ADD", "ADD_NEW"),
			array("USERS_LIST","LOCALE_LIST")
		);
		$this->module_admin_user_access = array(
			array("USERS_ALL","MANAGE_USER")
		);
		
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		$this->module_admin_access		= 0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$max = count($access);
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (($this->module_command."ALL" == $access[$index]) || ("ALL"==$access[$index]) || ($this->module_command==substr($access[$index],0,strlen($this->module_command)))){
					$this->module_admin_access=1;
				}
			}
		}
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$this->module_display_options = array(
				array("USERS_SHOW_LOGIN","CHANNEL_DISPLAY_LOGIN"),
				array("USERS_SHOW_REGISTER","CHANNEL_DISPLAY_REGISTRATION"),
				array("USERS_SHOW_GET_PWD_FORM","CHANNEL_DISPLAY_FORGOT_PWD"),
				array("USERS_SHOW_GET_USERNAME_FORM","CHANNEL_DISPLAY_FORGOT_USERNAME"),
				array("USERS_SHOW_PROFILE_FORM","CHANNEL_DISPLAY_PROFILE_FORM")
			);
		}
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
		* Table structure for table 'user_info'
		*/
		
		$fields = array(
			array("user_identifier"			,"unsigned integer"	,"NOT NULL"	,"auto_increment", "key"),
			array("user_login_name"			,"varchar(20)"		,"NOT NULL"	,"default ''"),
			array("user_login_pwd"			,"varchar(40)"		,"NOT NULL"	,"default ''"),
			array("user_creation_date"		,"datetime"			,"" 		,"default NULL"),
			array("user_date_grace"			,"datetime"			,"" 		,"default NULL"),
			array("user_date_review"		,"datetime"			,"" 		,"default NULL"),
			array("user_date_expires"		,"datetime"			,"" 		,"default NULL"),
			array("user_group"				,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("user_status"				,"unsigned integer"	,"NOT NULL"	,"default '1'"),
			array("user_client"				,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("user_uid"				,"varchar(6)"		,""			,"default ''")
		);
		$primary ="user_identifier";
		$tables[count($tables)] = array("user_info", $fields, $primary);
		/**
		* Table data for table 'user_info'
		*/
		$now = date("d/m/Y H:i:s");
		//$this->call_command("DB_QUERY",array("INSERT INTO user_info (user_login_name, user_login_pwd, user_creation_date, user_group, user_status, user_client) VALUES('admin', '6f5393979d674de36c433b47b7d8908e', '$now', '2', '2', '1');"));
		/**
		* Table structure for table 'user_status'
		*/
		$fields = array(
		array("user_status_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
		array("user_status_label"		,"varchar(255)"		,"NOT NULL"	,"default ''")
		);
		$primary="user_status_identifier";
		/**
		* Table data for table 'user_status'
		*/
		$data = array(
			'INSERT INTO user_status (user_status_label) VALUES ("LOCALE_USER_NOT_CONFIRMED");',
			'INSERT INTO user_status (user_status_label) VALUES ("LOCALE_USER_CONFIRMED");',
			'INSERT INTO user_status (user_status_label) VALUES ("LOCALE_USER_BLOCKED");'
		);
		$tables[count($tables)] = array("user_status", $fields, $primary, $data);
		/**
		* Table structure for table 'user_management_access'
		*/
		$fields = array(
		array("user_identifier"		,"unsigned integer"	,"NOT NULL"	,"default ''"),
		array("menu_identifier"		,"unsigned integer"	,"NOT NULL"	,"default ''")
		);
		$primary="";
		$tables[count($tables)] = array("relate_user_menu", $fields, $primary);
		/**
		* Table structure for table 'user_extended_functionality' 								  -
		*/
		$fields = array(
			array("tag_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("user_identifier"		,"unsigned integer"	,"NOT NULL"	,"default ''"),
			array("client_identifier"	,"unsigned integer"	,"NOT NULL"	,"default ''"),
			array("function_tag"		,"varchar(30)"		,"NOT NULL"	,"default ''"),
			array("function_value"		,"varchar(30)"		,""			,"default ''")
		);
		$primary="tag_identifier";
		$tables[count($tables)] = array("user_admin_functions", $fields, $primary);
		return $tables;
	}
	
	/**
	* remove confirm
	----------------
	- This module will allow an administrator to remove a user from the system.
	*/
	
	function remove_confirm($identifier){
		if ($identifier!=$_SESSION["SESSION_USER_IDENTIFIER"]){
			/**
			* delete this user it has been confirmed that the user is to be deleted
			*/
			$sql = "delete from user_info where user_client =$this->client_identifier and user_identifier=$identifier";
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
			$result = $this->call_command("DB_QUERY",array($sql));
			/**
			* delete this user's contact details if we have access to the contact table
			*/
			if ($this->has_module_contact==1){
				$sql = "delete from contact_data where contact_client = $this->client_identifier and contact_user = $identifier";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				$result = $this->call_command("DB_QUERY",array($sql));
			}
			/**
			* delete this user's group information
			*/
			if ($this->has_module_group==1){
				$sql = "delete from groups_belonging_to_user where client_identifier = $this->client_identifier and user_identifier = $identifier";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				$result = $this->call_command("DB_QUERY",array($sql));
			}
		}
	}
	
	/**
	* remove user screen
	-----------------------
	- This function will display the form for the user to select if they truly want to
	- remove the selected user.
	*/
	function remove_user_screen($identifier){
		
		/**
		* query if the user wishes to actually remove this users details as this might be a
		* mistake.
		*/
		
		$sql = "select * from user_info where user_client =$this->client_identifier and user_identifier=$identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$out ="<module name=\"users\" display=\"form\">";
		if ($result){
			$out .="<form name=\"user_remove_form\" label=\"".LOCALE_CONFIRM_DELETE."\">";
			if ($identifier!=$_SESSION["SESSION_USER_IDENTIFIER"]){
				$out .="	<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
				$out .="	<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."REMOVE_CONFIRM\"/>";
				$out .="	<text><![CDATA[".USER_REMOVE_CONFIRMATION_LABEL."]]></text>";
				$out .="	<input type=\"button\" command=\"".$this->module_command."LIST\" iconify=\"NO\" value=\"".NO_KEEP."\"/>";
				$out .="	<input type=\"submit\" iconify=\"YES\" value=\"".YES_REMOVE."\"/>";
			}else{
				$out .="	<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."LIST\"/>";
				$out .="	<text><![CDATA[Sorry you are not allowed to delete this user as you are this user]]></text>";
				$out .="	<input type=\"button\" command=\"".$this->module_command."LIST\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\"/>";
			}
			$out .="</form>";
			$this->call_command("DB_FREE",array($result));
		} else {
			$out .= "Sorry you do not have access to this user account";
		}
		$out .="</module>";
		return $out;
	}
	
	/**
	* display a list of the users in the system
	*
	* The user list function will prepare the Array of information that is to be supplied
	* to the screen including the results are what options are available to the
	* administrator.
	*/
	
	function user_list($parameters){  //
		$end_page 		= 1;
		$group_filter	= $this->check_parameters($parameters,"group_filter",0);
		$page			= $this->check_parameters($parameters,"page",1);
		$status			= $this->check_parameters($parameters,"status",$this->check_parameters($parameters,"identifier",-1));
		$order_filter	= $this->check_parameters($parameters,"order_filter",0);
		$filter_string	= str_replace(
							Array(" ","'"), 
							Array("%","&#39;"), 
							$this->check_parameters($parameters,"filter_string")
						  );
		$variables 		= array();	
		
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_list filter parameters",__LINE__,print_r($parameters,true)));}
		
		/**
		* Procude the SQL command that will retrieve the information from the database
		*/
		$where = "";
		$w2="";
		$w="";
		$join="";
		if ($this->has_module_contact==1){
			$join.=" 
			left outer join contact_data on user_info.user_identifier=contact_data.contact_user
			left outer join contact_address on contact_address.address_identifier=contact_data.contact_address
			left outer join contact_company on contact_address.address_identifier=contact_company.company_address
			left outer join email_addresses on contact_data.contact_identifier=email_addresses.email_contact
			";

			$where .= " and (contact_data.contact_client=$this->client_identifier or contact_data.contact_client is null) ";
			$w2	   .= " or
				contact_data.contact_first_name like '%$filter_string%' or 
				contact_data.contact_last_name like '%$filter_string%' or 
				contact_data.contact_initials like '%$filter_string%' or 
				contact_data.contact_job_title like '%$filter_string%' or 
				contact_data.contact_telephone like '%$filter_string%' or 
				contact_data.contact_fax like '%$filter_string%' or 
				contact_data.contact_profile like '%$filter_string%'";
			$where .= " and (contact_company.company_client=$this->client_identifier or contact_company.company_client is null) ";
			$w2    .= " or contact_company.company_name like '%$filter_string%' ";
			$where .= " and (contact_address.address_client=$this->client_identifier or contact_address.address_client is null) ";
			$w2	   .= " or
				contact_address.address_1 like '%$filter_string%' or 
				contact_address.address_2 like '%$filter_string%' or 
				contact_address.address_3 like '%$filter_string%' or 
				contact_address.address_city like '%$filter_string%' or 
				contact_address.address_county like '%$filter_string%' or 
				contact_address.address_postcode like '%$filter_string%'";
			$where .= " and (email_addresses.email_client=$this->client_identifier or email_addresses.email_client is null)";
			$w2    .= " or email_addresses.email_address like '%$filter_string%' ";
			
/*			*/
		}
		if ($filter_string!=""){
			$w = " and (user_login_name like '%$filter_string%' $w2)";
		}
		if ($group_filter>0){
			$join.=" left outer join groups_belonging_to_user on user_info.user_identifier = groups_belonging_to_user.user_identifier";
			$where .=" and groups_belonging_to_user.group_identifier = $group_filter";
		}
		if($status!=-1){
			$where .=" and user_info.user_status = $status";
		}
		$sql = "Select * from user_info $join where user_info.user_client=$this->client_identifier $where $w order by ".$this->display_options[$order_filter][2]."";
//		print $sql;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		/**
		* what functionality options are available on this page
		*/
		$variables["PAGE_BUTTONS"] = Array(Array("ADD",$this->module_command."ADD",ADD_NEW));
		$variables["HEADER"] = "User Manager (List users)";
		if (!$result){
			/**
			* No Records were returned.
			*/
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			$number_of_records	= 0;
			$goto				= 0;
			$finish				= 0;
			$page				= 1;
			$num_pages			= 1;
			$start_page			= 1;
			$end_page			= 1;
		}else{
			/**
			* When some records are returned we will only return the page of results that the
			- user has requested or the first page if the user has not requested any page.
			*/
			
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$this->page_size =50;
			/**
			* Start to work out what posisition on the record set we are supposed to be at.
			*/
			$page = $this->check_parameters($parameters,"page",1);
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$goto = ((--$page)*$this->page_size);
			
			/**
			* jump down the results to the starting record for our consideration
			*/
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
			}
			/**

			* produce the variables that will be used to work out what information will be
			- displayed
			*/
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
			
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page+=$this->page_size;
			
			$counter=0;
			
			/**
			* What options are available per result
			*/
/*				$variables["ENTRY_BUTTONS"] 	= Array(
					Array("EDIT",$this->module_command."EDIT",EDIT_EXISTING),
					Array("REMOVE",$this->module_command."REMOVE",REMOVE_EXISTING)
				);
*/
/*			if ($this->parent->server[LICENCE_TYPE]==ECMS)			{
				
			}*/
			/**
			* Retrieve the actual results that are to be displayed
			*/
			$variables["RESULT_ENTRIES"] 	= Array();
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$phone = $this->check_parameters($r,"contact_telephone","[[nbsp]]");
				if($phone==""){
					$phone="[[nbsp]]";
				}
				$uid = $r["user_identifier"];
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $uid,
					"ENTRY_BUTTONS" => Array(),
					"attributes"	=> Array(
						Array(ENTRY_USERNAME,	$this->check_parameters($r,"user_login_name"),"TITLE","NO"),
						Array(USER_FULL_NAME,	$this->check_parameters($r,"contact_last_name").", ".$this->check_parameters($r,"contact_first_name",""),"SUMMARY","NO"),
						Array(USER_COMPANY,		$this->check_parameters($r,"company_name","[[nbsp]]")),
//						Array(USER_PHONE,		$phone),
//						Array(LOCALE_EMAIL,		$this->check_parameters($r,"email_address","[[nbsp]]"))
					)
				);
				$override_edit =0;
				if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
					if ($this->has_module_group==1){
						$grps = $this->call_command("GROUP_RETRIEVE_INFORMATION",Array("user_identifier" => $r["user_identifier"]));
						$grp_info ="";
						$grp_type=1;
						for ($i=0,$max=count($grps);$i<$max;$i++){
							if ($grps[$i]["TYPE"]==2){
								$grp_type=2;
							}
							$grp_info .="<li>".$grps[$i]["LABEL"]."</li>";
						}	
						if($grp_info == ""){
							$grp_info = "[[nbsp]]";
						}
						$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array(USER_GROUP_NAME, $grp_info,"SUMMARY","NO");
					}
					$sql = "select distinct * from user_to_object where uto_client = $this->client_identifier and uto_identifier = $uid";
//					print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
					$result_obj  = $this->call_command("DB_QUERY",Array($sql));
                    while($r_obj = $this->call_command("DB_FETCH_ARRAY",Array($result_obj))){
                    	$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("edit" , $r_obj["uto_module"]."EXECUTE_EDIT&amp;module_identifier=".$r_obj["uto_object"],"Edit Member");
				$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE", $r_obj["uto_module"]."EXECUTE_DELETE&amp;module_identifier=".$r_obj["uto_object"],"Delete Member");
						$override_edit=1;
                    }
                    $this->call_command("DB_FREE",Array($result_obj));
				}
				if($override_edit==0){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."EDIT",EDIT_EXISTING);
				$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."REMOVE_CONFIRM",REMOVE_EXISTING);
				}
			}
			/**
			* produce the XML representation of the information above.
			*/
		}
		/**
		* retrieve the page spanning information
		*/
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["as"]	= "table";
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$variables["START_PAGE"]		= $start_page;
		$variables["END_PAGE"]			= $end_page;
		/**
		* retrieve the XML information for building the filter form
		*/
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$variables["FILTER"]			= $this->user_filter($parameters);
		}else{
			$variables["FILTER"]			= "";
		}
			
		$out = $this->generate_list($variables);
		
		return $out;
	}
	
	/**
	* user_filter
	-----------
	- The user filter will allow the user to filter the way that information is filtered
	- on the screen.
	*/
	function user_filter($parameters){
		$group_filter	= $this->check_parameters($parameters,"group_filter",0);
		$order_filter	= $this->check_parameters($parameters,"order_filter",0);
		$filter_string	= $this->check_parameters($parameters,"filter_string");
		$status			= $this->check_parameters($parameters,"status",$this->check_parameters($parameters,"identifier",-1));
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_filter",__LINE__,print_r($parameters,true)));}
		$out = "\t\t\t\t<form name=\"user_filter_form\" method=\"get\" label=\"".USER_TITLE_LABEL."\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\" value=\"USERS_LIST\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" value=\"1\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"status\" value=\"$status\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"text\" label=\"".SEARCH_KEYWORDS."\" name=\"filter_string\" size=\"20\"><![CDATA[$filter_string]]></input>\n";
		/**
		* retrieve the list of groups and display for selection
		*/
		if ($this->has_module_group){
			$group_list = $this->call_command("GROUP_RETRIEVE",array($group_filter));
			$out .= "\t\t\t\t\t<select name=\"group_filter\" label=\"".USER_GROUP_FILTER."\">\n";
			$out .= "\t\t\t\t\t\t<option value=\"-1\">".USER_DISPLAY_ALL_GROUPS."</option>\n";
			$out .= "$group_list";
			$out .= "\t\t\t\t\t</select>\n";
		}
		/**
		* display the order by filter option
		*/
		$out .= "\t\t\t\t\t<select name=\"order_filter\" label=\"".ENTRY_ORDER_BY."\">\n";
		for ($index=0,$max=count($this->display_options);$index<$max;$index++){
			$out .="\t\t\t\t\t\t<option value=\"".$this->display_options[$index][0]."\"";
			if ($order_filter==$this->display_options[$index][0]){
				$out .=" selected=\"true\"";
			}
			$out .=">".$this->display_options[$index][1]."</option>\n";
		}
		$out .= "\t\t\t\t\t</select>\n";
		$out .= "\t\t\t\t\t<input type=\"submit\" iconify=\"SEARCH\" value=\"".SEARCH_NOW."\"/>\n";
		$out .= "\t\t\t\t</form>";
		/**
		* return the filter XML document
		*/
		return $out;
	}
	
	/**
	* user login
	-----------------------
	- This function
	-
	*/
	function user_login($parameters){
		$user	= $this->check_locale_starter($this->check_parameters($parameters,"login_user_name"));
		$pwd	= $this->check_locale_starter($this->check_parameters($parameters,"login_user_pwd"));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_login",__LINE__,"[$user,$pwd]"));
		}
		
		/**
		* build up the sql command used based on the modules that we have access to.
		*/
		$join="";
		if ($this->has_module_contact==1){
			$join  = " 
				left outer join contact_data on contact_data.contact_user = user_info.user_identifier 
				left outer join contact_address on contact_data.contact_address = contact_address.address_identifier
				left outer join contact_company on contact_company.company_address = contact_address.address_identifier
				left outer join email_addresses on email_addresses.email_contact = contact_data.contact_identifier
			";
		}
/*
		if ($this->has_module_group==1){
			$join .= " 
			left outer join groups_belonging_to_user on groups_belonging_to_user.user_identifier = user_info.user_identifier
			left outer join group_data on group_data.group_identifier=groups_belonging_to_user.group_identifier ";
		}
*/
		$sql = "Select * from user_info $join where user_login_name='$user' and user_login_pwd='".md5($pwd)."' and user_client=$this->client_identifier and user_status=2";

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
		}
		/**
		* execute the sql command
		*/
		$result = $this->call_command("DB_QUERY",array($sql));
		$num_of_records = $this->call_command("DB_NUM_ROWS",array($result));

		if ($num_of_records==0){
			/**
			* no results were returned
			*/
			/*******************Uncommit & Commit by Ali to Remove User Access Logs***********************/
			$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_LOGIN_FAILED__",Array("username"=>$user,"password"=>$pwd)));
			/**********************************************************************************/
			return -1;
			
		}else{
			/**
			* a single record should have been returned (we only use the first record)
			*/

			if ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				/**
				* start setting the session information for this user.
				*/ 
				$user_identifier =$r["user_identifier"];
				$_SESSION["SESSION_USER_NAME"] = $user;
				$_SESSION["SESSION_USER_IDENTIFIER"] = $user_identifier;
				$_SESSION["SESSION_CLIENT_IDENTIFIER"] = $this->client_identifier;
				$_SESSION["SESSION_LOGGED_IN"] = 1;
				$_SESSION["SESSION_DATE_TIME"] = $this->libertasGetDate("Y/m/d H:i:s");
				/*************************************************************************************************************************
                * get expiry information conver to integer for simple comparing
                *************************************************************************************************************************/
				if($r["user_date_expires"]!="0000-00-00 00:00:00"){
					$_SESSION["SESSION_ACCOUNT_DATE_EXPIRES"] = strtotime($r["user_date_expires"]);
				} else {

					$_SESSION["SESSION_ACCOUNT_DATE_EXPIRES"] = 0;
				}
				if($r["user_date_grace"]!="0000-00-00 00:00:00"){
					$_SESSION["SESSION_ACCOUNT_DATE_GRACE"] = strtotime($r["user_date_grace"]);
				} else {
					$_SESSION["SESSION_ACCOUNT_DATE_GRACE"] = 0;
				}
				if($r["user_date_review"]!="0000-00-00 00:00:00"){
					$_SESSION["SESSION_ACCOUNT_DATE_REVIEW"] = strtotime($r["user_date_review"]);
				} else {
					$_SESSION["SESSION_ACCOUNT_DATE_REVIEW"] = 0;
				}

				
				$_SESSION["SESSION_FUNC_ACCESS"]=$this->user_pref(array("return_xml" => true),$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER"));
				/**
				* If we have the group module then set the session information for the group
				*/ 
				if ($this->has_module_group==1){
					$group_info = $this->call_command("GROUP_RETRIEVE_INFORMATION");
					$_SESSION["SESSION_GROUP"]	= $group_info;
					$max = count($group_info);
					$admin = 1;
					for($grp_index=0;$grp_index < $max;$grp_index++){
						if ($group_info[$grp_index]["TYPE"]==2){
							$admin = 2;
						}
					}
					$_SESSION["SESSION_GROUP_TYPE"]	= $admin;
				} else {
					$_SESSION["SESSION_GROUP"]	= Array();
					$_SESSION["SESSION_GROUP_TYPE"]	= 0;
				}
				
				/**
				* If we have the contact module then set the session information for the contact
				*/ 
				if ($this->has_module_contact==1){
					$_SESSION["SESSION_CONTACT_IDENTIFIER"]	= $this->check_parameters($r,"contact_identifier",-1);
					if ($this->check_parameters($r,"contact_identifier",-1)!=-1){
						$_SESSION["SESSION_LAST_NAME"]		= $this->check_parameters($r,"contact_last_name");
						$_SESSION["SESSION_FIRST_NAME"]		= $this->check_parameters($r,"contact_first_name");
						$_SESSION["SESSION_EMAIL"]			= $this->check_parameters($r,"email_address");
					}else{
						$_SESSION["SESSION_LAST_NAME"]		= "";
						$_SESSION["SESSION_FIRST_NAME"]		= "";
						$_SESSION["SESSION_EMAIL"]			= "";
					}
				}else{
					$_SESSION["SESSION_CONTACT_IDENTIFIER"]	= "";
					$_SESSION["SESSION_LAST_NAME"]			= "";
					$_SESSION["SESSION_FIRST_NAME"]			= "";
					$_SESSION["SESSION_EMAIL"]				= "";
				}
				
				/**
				* find if we have secured this users acces to managing certain sections of the
				- web structure.
				*/ 
				$sql = "Select * from relate_user_menu where user_identifier='$user_identifier'";
				$user_location_access=array();
				if($location_result = $this->call_command("DB_QUERY",array($sql))) {
				
					while($lr = $this->call_command("DB_FETCH_ARRAY",array($location_result))){
						$user_location_access[count($user_location_access)]=$lr["menu_identifier"];
					}
					$this->call_command("DB_FREE",array($location_result));
				}
				$_SESSION["SESSION_MANAGEMENT_ACCESS"]		= $user_location_access;
				$ok=1;
				/**
				* ok now update any records in the user access log to state that this user has accessed
				- any specific pages.
				*/ 
				/****************Uncommit & Commit by Ali to Remove User Access Logs*******///////////
				$this->call_command("USERACCESS_UPDATE",array($user_identifier,session_id()));
				/////////////************************************************/////////
				$user_identifier							= $this->check_parameters($_SESSION,"SESSION_USER");
				if (($this->parent->server[LICENCE_TYPE]!=ECMS) && ($this->parent->server[LICENCE_TYPE]!=MECM) && $this->parent->script=="admin/index.php"){
					$this->parent->refresh(Array("url"=>$this->parent->base."admin/index.php?command=PAGE_LIST"));
					$this->exitprogram();
				}
				return 1;
			} else {
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- User login failed log it and return to login screen
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$ok=-1;
				$_SESSION["SESSION_LOGGED_IN"]				= 0;
			}
			$this->call_command("DB_FREE",array($result));
			return $ok;
		}
	}
	
	
	/**
	* check login
	* 
	* This function will check to see if the user is currently logged in if not it will
	* attempt to log in to the system and on a failure it will display the login form.
	*/
	function check_login($parameters){
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($_SESSION, true)."</pre></li>";
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($_SERVER, true)."</pre></li>";
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		//$this->exitprogram();
		$redirect 						= $this->check_parameters($parameters, "redirect");
		if($redirect==""){
			$redirect 					= $this->check_parameters($_SESSION, "referal_script");
		}
		$command 						= $this->check_parameters($parameters, "command");
		$login_user_name				= $this->check_locale_starter($this->check_parameters($parameters,"login_user_name"));
		$login_user_pwd					= $this->check_locale_starter($this->check_parameters($parameters,"login_user_pwd"));
		$parameters["redirect"]			= $redirect;
		$parameters["login_user_name"]	= $login_user_name;
		$parameters["login_user_pwd"]	= $login_user_pwd;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"check_login",__LINE__,"[$command,$login_user_name,$login_user_pwd]"));
		}
		if (($login_user_name=="")||($login_user_pwd=="")){
			$parameters["ok"] =0;
			$ok 		 = $this->check_parameters($parameters, "ok", 0);
			$show_anyway = $this->check_parameters($parameters, "show_anyway",0);
			$out = $this->show_login($parameters);
		}else{
//		print $redirect;
//		$this->exitprogram();
			$out="";
			if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==0){
				$ok=0;
				if ($command=="ENGINE_LOGIN" || $command=="USERS_SHOW_LOGIN"){
					$parameters["ok"] = $this->user_login($parameters);
					if($parameters["ok"]==1){
							if($redirect!=""){
								unset($_SESSION["referal_script"]);
								if(substr($redirect,0,strlen($this->parent->base))==$this->parent->base){
									$this->call_command("ENGINE_REFRESH_BUFFER",Array($redirect));
								} else {
									$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$this->parent->base.$redirect));
								}
							} else {
								$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$this->parent->base.$this->parent->script));
							}
					}
				}
				if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==0){
					$out = $this->show_login($parameters);
				}
			}
		}
		return $out;
	}
	/**
	* This function will display the login form if the user is not logged in .
	*/
	function show_login($parameters){
		$ok 		 	= $this->check_parameters($parameters, "ok", 0);
		$show_anyway	= $this->check_parameters($parameters, "show_anyway",0);
		$show_module	= $this->check_parameters($parameters, "show_module",1);
		$redirect 		= $this->check_parameters($parameters, "redirect");
//		unset($_SESSION["referal_script"]);
		$out="";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"show_login",__LINE__,$_SESSION["SESSION_LOGGED_IN"]." - ".$this->have_displayed_login_form));
		}
		if (($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==0 || $show_anyway==1)){
			if ($this->have_displayed_login_form == 0){
				if ($show_module==1){
					$out  = "<module name=\"users\" display=\"form\">\n\t";
				}
				$out .= "\t\t<form label=\"".LOCALE_LOGIN_FORM."\" name=\"USERS_SHOW_LOGIN\" width='auto'>\n";
				$out .= "\t\t\t<input type=\"hidden\" name=\"command\" value=\"ENGINE_LOGIN\"/>\n";
				if (($redirect!="http://".$this->parent->domain.$this->parent->base) && ($redirect!="https://".$this->parent->domain.$this->parent->base) && ($redirect!="https://".$this->parent->domain."/") && ($redirect!="http://".$this->parent->domain."/") && !(substr($redirect,0,strlen("http://".$this->parent->domain."/")) != "http://".$this->parent->domain."/")){
					$out .= "\t\t\t<input type=\"hidden\" name=\"redirect\" ><![CDATA[$redirect]]></input>\n";
				}
				if ($ok==-1){
					$out .= "\t\t\t<text type=\"error\"><![CDATA[".LOCALE_LOGIN_INCORRECT."]]></text>\n";
				}
				$out .= "\t\t\t<input label=\"".ENTRY_USERNAME."\" size=\"20\" maxlength=\"50\" type=\"text\" name=\"login_user_name\" required=\"YES\"/>\n";
				$out .= "\t\t\t<input label=\"".ENTRY_PASSWORD."\" size=\"20\" type=\"password\" name=\"login_user_pwd\" required=\"YES\"/>\n";
				$out .= "\t\t\t<input type=\"submit\" iconify=\"LOGIN\" value=\"".LOGIN_TO_SYSTEM."\"/>\n";
				if ($this->parent->module_type=="website"){
					$out .= "\t\t\t<input type=\"button\" iconify='REGISTER' value='".USERS_SHOW_REGISTER."' command='-join-now.php'/>\n";
					$out .= "\t\t\t<input type=\"button\" iconify='GET_USERNAME' value='".USERS_SHOW_GET_USERNAME_FORM."' command='-forgot-username.php'/>\n";
					$out .= "\t\t\t<input type=\"button\" iconify='GET_PWD' value='".USERS_SHOW_GET_PWD_FORM."' command='-forgot-password.php'/>\n";
				}
				$out .= "\t\t</form>\n\t";
				if ($show_module==1){
					$out  .= "</module>";
				}
				$this->have_displayed_login_form=1;
			}
		}else{
			if (strlen($redirect)==0 && $show_anyway==1){
				$out  = "<module name=\"users\" display=\"form\">\n\t";
				$out .= "\t\t<form label=\"Logout of the system\">\n";
				$out .= "\t\t\t<input type=\"hidden\" name=\"command\" value=\"ENGINE_LOGOUT\"/>\n";
				$out .= "\t\t\t<text><![CDATA[".LOCALE_YOU_ARE_LOGGED_IN."]]></text>\n";
				$out .= "\t\t\t<input type=\"submit\" iconify=\"LOGOUT\" value=\"".LOGOUT_OF_SYSTEM."\"/>\n";
				$out .= "\t\t</form>\n\t</module>";
			} else {
				if (strlen($redirect)!=0){
					$this->call_command("ENGINE_REFRESH_BUFFER",Array($redirect));
				}
			}
		}
		return $out;
	}
	
	/**
	* get_status()
	* 
	* retrieve the status of a document supply the $level of the status to have it selected
	*/
	function get_status($level=-1){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_status",__LINE__,"[$level]"));
		}
		$sql = "Select * from user_status order by user_status_label";
		$result = $this->call_command("DB_QUERY",array($sql));
		$list = "";
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$list .="\t\t\t\t\t\t<option value=\"".$r["user_status_identifier"]."\"";
			if ($level==$r["user_status_identifier"]){
				$list .=" selected=\"true\"";
			}
			$list .=">".$this->get_constant($r["user_status_label"])."</option>\n";
			
		}
		$this->call_command("DB_FREE",array($result));
		return $list;
	}
	
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function user_pref($parameters,$user){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_form",__LINE__,"[$identifier]"));
		}
		$as_xml 		= $this->check_parameters($parameters,"return_xml",false);
		$sql 			= "select * from user_admin_functions where client_identifier = $this->client_identifier and user_identifier = $user;";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
		}

		$result 		= $this->call_command("DB_QUERY",array($sql));
		if ($as_xml){
			$out = "";
		}else{
			$out = Array();
		}
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if ($as_xml){
				$out .= "<function name=\"".$r["function_tag"]."\"><![CDATA[".$r["function_value"]."]]></function>";
			}else{
				$out[count($out)] = Array($r["function_tag"], $r["function_value"]);
			}
		}
		return $out;
	}
	
	function get_pwd($parameters){
		$error 			= $this->check_parameters($parameters,"error",0);
		$show_module	= $this->check_parameters($parameters,"show_module",1);
		$out="";
		if($show_module==1){
			$out  ="<module name=\"users\" display=\"form\">";
		}
		$out .="<form name=\"USERS_SHOW_GET_PWD_FORM\" method=\"post\" label=\"".LOCALE_RETRIEVE_PWD_FORM."\">";
		$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_RETRIEVE_PWD\"/>";
		if ($error>0){
			$out .="<text type=\"error\"><![CDATA[".LOCALE_USERS_SUPPLY_INVALID_USERNAME_OR_EMAIL."]]></text>";			
		}
		$out .="<input label =\"".LOCALE_USERS_REQUEST_PASSWORD."\" type=\"text\" name=\"users_login_name\"><![CDATA[]]></input>";
		$out .="<input label =\"".LOCALE_USERS_REQUEST_USERNAME."\" type=\"text\" name=\"users_email\"><![CDATA[]]></input>";
		$out .="<input iconify=\"GET_PWD\" type=\"submit\" value=\"".LOCALE_RETRIEVE_PWD."\"/>";
		$out .="</form>";
		if($show_module==1){
			$out .="</module>";
		}
		return $out;
	}

	function get_pwd_confirm($parameters){
		$username = $this->check_locale_starter($this->check_parameters($parameters,"users_login_name"));
		$email = $this->check_locale_starter($this->check_parameters($parameters,"users_email"));
		$sent = true;
		if ($username!=""){
			if ($email!=""){
			$sql = "select * from user_info 
						left outer join contact_data on contact_user = user_identifier 
						left outer join email_addresses on contact_data.contact_identifier = email_addresses.email_contact
					where 
						user_client=$this->client_identifier and 
						user_login_name = '$username' and 
						email_address = '$email'";
			$result 		= $this->call_command("DB_QUERY",array($sql));
			$identifier=-1;
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$firstname = $this->check_parameters($r,"contact_first_name");
				$identifier = $this->check_parameters($r,"user_identifier");
				$to = $email;
			}
			if ($identifier!=-1){
				$password = $this->generate_random_text(12);
				$encrypt_pwd = md5($password);
				$sql = "update user_info set user_login_pwd = '$encrypt_pwd' where user_identifier = $identifier and user_client=$this->client_identifier";
				$result 		= $this->call_command("DB_QUERY",array($sql));
				
				$subject 	= "Forgot your password?";
				$body 		= "
Thank you $firstname for using our automated system to reset your password.

Your password is $password

Please login to the site and change your password.

Thanks 
 The support team.
 http://".$this->parent->domain."
	";
				$sent = $this->call_command("EMAIL_QUICK_SEND",Array(
					"subject"	=> $subject,
					"body"		=> $body,
					"from"		=> "The support team",
					"to"		=> $to
					)
				);
				$out  ="<module name=\"users\" display=\"confirmation\">";
				if (!$sent){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_EMIAL_PROBLEM."]]></text>";
				}
				if ($sent){
					$out .="<text><![CDATA[".LOCALE_USERS_PWD_SENT_MSG."]]></text>";
				}
				$out .="</module>";
				}else{
					$out = $this->get_pwd(Array("error"=>2)); // 2 represents that username not found
				}
			}else{
				$out = $this->get_pwd(Array("error"=>3)); // 3 represents that you did not supply an email address
			}
		}else{
			$out = $this->get_pwd(Array("error"=>1)); // 1 represents that you did not supply a username to check
		}
		return $out;
	}


	function get_user_name($parameters){
		$error 			= $this->check_parameters($parameters,"error",0);
		$show_module	= $this->check_parameters($parameters,"show_module",1);
		$out="";
		if($show_module==1){
			$out  ="<module name=\"users\" display=\"form\">";
		}
		$out .="<form name=\"USERS_SHOW_GET_USERNAME_FORM\" method=\"post\" label=\"".LOCALE_RETRIEVE_USERNAME_FORM."\">";
		if ($error==1){
			$out .="<text type=\"error\"><![CDATA[".LOCALE_USERS_SUPPLY_EMAIL."]]></text>";			
		}
		if ($error==2){
			$out .="<text type=\"error\"><![CDATA[".LOCALE_USERS_SUPPLED_INVALID_EMAIL."]]></text>";			
		}
		$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_RETRIEVE_USERNAME\"/>";
		$out .="<input label =\"".LOCALE_USERS_REQUEST_USERNAME."\" type=\"text\" name=\"users_email\" value=\"\"/>";
		$out .="<input iconify=\"GET_USERNAME\" type=\"submit\" value=\"".LOCALE_RETRIEVE_USERNAME."\"/>";
		$out .="</form>";
		if($show_module==1){
			$out .="</module>";
		}
		return $out;
	}


	function get_user_name_confirm($parameters){
		$users_email = $this->check_locale_starter($this->check_parameters($parameters,"users_email"));
		$sent = true;
		if ($users_email!=""){
			$sql = "select * from user_info 
						left outer join contact_data on contact_data.contact_user = user_info.user_identifier 
						left outer join email_addresses on email_addresses.email_contact = contact_data.contact_identifier 
						where email_addresses.email_address = '$users_email' and 
						email_addresses.email_client= '$this->client_identifier' and 
						contact_data.contact_client= '$this->client_identifier'
						";
			$result 		= $this->call_command("DB_QUERY",array($sql));
			$identifier=-1;
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$firstname = $this->check_parameters($r,"contact_first_name");
				$username = $this->check_parameters($r,"user_login_name");
				$identifier = $this->check_parameters($r,"user_identifier");
			}
			if ($identifier!=-1){			
				$subject 	= "Forgot your username?";
				$body 		= "
Thank you $firstname for using our automated system to find your username.

Your username is $username

Thanks 
 The support team.
 http://".$this->parent->domain."
";
				$to = $users_email;
				$sent = $this->call_command("EMAIL_QUICK_SEND",Array(
					"subject"	=> $subject,
					"body"		=> $body,
					"from"		=> "The support team",
					"to"		=> $to
					)
				);
				$out  ="<module name=\"users\" display=\"confirmation\">";
				if (!$sent){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_EMIAL_PROBLEM."]]></text>";
				}
				if ($sent){
					$out .="<text><![CDATA[".LOCALE_USERS_USERNAME_SENT_MSG."]]></text>";
				}
				$out .="</module>";
			}else{
				$out = $this->get_user_name(Array("error"=>2)); // 2 represents that username not found
			}
		}else{
			$out = $this->get_user_name(Array("error"=>1)); // 1 represents that you did not supply a username to check
		}
		return $out;
	}
	
	
	function register_user($parameters){
		$out="";
		if($this->check_parameters($parameters,"show_module",1)){
			$out  ="<module name=\"users\" display=\"form\">";
		}
		$out .="<form name=\"USERS_SHOW_REGISTER\" method=\"post\" label=\"".LOCALE_USERS_REGISTER_INTREST."\">";
		$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_REGISTRATION_SAVE\"/>";
		$out .="<input type=\"hidden\" name=\"times_through\" value=\"1\"/>";
		$out .="<input type=\"text\" label=\"".ENTRY_USERNAME."\" size=\"20\" name=\"user_login_name\" value=\"\" required=\"YES\"/>";
		$out .="<input type=\"password\" label=\"".ENTRY_PASSWORD."\" size=\"20\" name=\"user_login_pwd\" value=\"\" required=\"YES\"/>";
		$out .="<input type=\"password\" label=\"".ENTRY_PASSWORD_CONFIRM."\" size=\"20\" name=\"user_confirm_login_pwd\" value=\"\" required=\"user_login_pwd\"/>";
		if ($this->has_module_contact){
			$out .=$this->call_command("CONTACT_REGISTER_FORM",array($parameters));
		}
		$extra = $this->call_command("ENGINE_RETRIEVE",array("ADD_TO_USER_PROFILE",$parameters));
		$length_of_extra = count($extra);
		for ($index=0;$index<$length_of_extra;$index++){
			if ($extra[$index][1]!=""){
				$out .=$extra[$index][1];
			}
		}
		$out .="<input iconify=\"REGISTER\" type=\"submit\" value=\"".LOCALE_REGISTER_USER."\"/>";
		$out .="</form>";
		if($this->check_parameters($parameters,"show_module",1)){
			$out .="</module>";
		}
		return $out;
	}

	function register_user_save($parameters){
		$total_errors = 0;
		$extra_options = "";
		$first_name				= ""; 
		$initials				= "";
		$last_name				= "";
		$login_name				= $this->check_locale_starter($this->check_parameters($parameters,"user_login_name"));
		$login_pwd 				= $this->check_locale_starter($this->check_parameters($parameters,"user_login_pwd"));
		$user_confirm_login_pwd = $this->check_locale_starter($this->check_parameters($parameters,"user_confirm_login_pwd"));
		$email					= $this->check_locale_starter($this->check_parameters($parameters,"contact_email"));
		$email_confirm			= $this->check_locale_starter($this->check_parameters($parameters,"contact_confirm_email"));
		$oname = $this->check_parameters($parameters,"other_login_names");
		if (($login_name=="") && ($oname!="")){
			$login_name=$oname;
		}
		if ($login_name!=""){
			$sql="select * from user_info where user_login_name='$login_name' and user_client=$this->client_identifier";
			$result  = $this->call_command("DB_QUERY",array($sql));
			$counter = $this->call_command("DB_NUM_ROWS",Array($result));
			if ($counter>0){
				$error_username = 2;
				$total_errors++;
				$extra_options = $this->retrieve_unique_usernames($parameters);
				$login_name="";
			}else{
				$error_username = 0;
			}
		}else{
			$error_username = 1;
			$total_errors++;
		}
		if ($login_pwd!=""){
			if ($login_pwd==$user_confirm_login_pwd){
				if (strlen($login_pwd)<6){
					$error_password = 1;
					$total_errors++;
					$login_pwd="";
				}else{
					$error_password = 0;
				}
			}else{
				$error_password = 1;
				$total_errors++;
				$login_pwd="";
			}
		}else{
			$error_password = 1;
			$total_errors++;
			$login_pwd="";
		}
		$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"USERS_SHOW_REGISTER"));
		if ($this->check_parameters($form_restriction_list,"contact_first_name","__NOT_FOUND__")!="__NOT_FOUND__"){
			if ($this->check_parameters($form_restriction_list["contact_first_name"],"required","0")!="0"){
				$first_name 		= $this->check_locale_starter($this->check_parameters($parameters,"contact_first_name"));
				if ($first_name==""){
					$total_errors++;
				}
			}
		}
		if ($this->check_parameters($form_restriction_list,"contact_last_name","__NOT_FOUND__")!="__NOT_FOUND__"){
			if ($this->check_parameters($form_restriction_list["contact_last_name"],"required","0")!="0"){
				$last_name			= $this->check_locale_starter($this->check_parameters($parameters,"contact_last_name"));
				if ($last_name==""){
					$total_errors++;
				}
			}
		}
		if ($this->check_parameters($form_restriction_list,"contact_email","__NOT_FOUND__")!="__NOT_FOUND__"){
			if ($this->check_parameters($form_restriction_list["contact_email"],"required","0")!="0"){
				if (($email!=$email_confirm) || ($email=="")){
					$total_errors++;
				}
			}
		}
		if ($total_errors>0){
			$out  ="<module name=\"users\" display=\"form\">";
			$out .="<form name=\"user_form\" method=\"post\" label=\"".LOCALE_USERS_REGISTER_INTREST."\">";
			$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_REGISTRATION_SAVE\"/>";
			$out .="<input type=\"hidden\" name=\"times_through\" value=\"1\"/>";
			if($error_username==1){
				$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_USERNAME."]]></text>";
			}
			if($error_username==2){
				$out .="<text type=\"error\"><![CDATA[".LOCALE_REGISTER_USERNAME_EXISTS."]]></text>";
				$out .="<radio name=\"other_login_names\" label=\"".LOCALE_ENTRY_OPTIONAL_USERNAMES."\">$extra_options</radio>";
			}
			$out .="<input type=\"text\" label=\"".ENTRY_USERNAME."\" size=\"20\" name=\"user_login_name\" required=\"YES\"><![CDATA[$login_name]]></input>";
			if($error_password==1){
				$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_PASSWORD."]]></text>";
			}
			$out .="<input type=\"password\" label=\"".ENTRY_PASSWORD."\" size=\"20\" name=\"user_login_pwd\" required=\"YES\"><![CDATA[$login_pwd]]></input>";
			$out .="<input type=\"password\" label=\"".ENTRY_PASSWORD_CONFIRM."\" size=\"20\" name=\"user_confirm_login_pwd\" required=\"user_login_pwd\"><![CDATA[$login_pwd]]></input>";
			if ($this->has_module_contact){
				$parameters["access_command"] ="USERS_REGISTRATION_SAVE";
				$parameters["times_through"] =1;
			$out.= $this->call_command("CONTACT_REGISTER_FORM",$parameters);
			}
			$extra = $this->call_command("ENGINE_RETRIEVE",array("ADD_TO_USER_PROFILE",$parameters));
			$length_of_extra = count($extra);
			for ($index=0;$index<$length_of_extra;$index++){
				if ($extra[$index][1]!=""){
					$out .=$extra[$index][1];
				}
			}
			$out .="<input iconify=\"REGISTER\" type=\"submit\" value=\"".LOCALE_REGISTER_USER."\"/>";
			$out .="</form>";
			$out .="</module>";
		}else{
			$uid = $this->generate_random_text(6);
			$parameters["email_codex"]=$uid;
			$parameters["prev_command"]="USERS_ADD";
			$oname = $this->check_parameters($parameters,"other_login_names");
			
			if ($oname!=""){
				$parameters["user_login_name"]=$oname;
			}
			$this->save($parameters);	
			if ($this->check_parameters($form_restriction_list,"contact_first_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				$initials			= $this->check_parameters($parameters,"contact_initials");
				if ($initials!=""){
					$full_name = "$first_name, $initials, $last_name";
				} else {
					$full_name = "$first_name, $last_name";
				}
			} else{
				$full_name=$login_name;
			}
			$call = $this->check_parameters($parameters,"call_command");
			if ($call!=""){
				$this->call_command($call,$parameters);
			}
		
			if ($this->check_parameters($form_restriction_list,"contact_email","__NOT_FOUND__")!="__NOT_FOUND__"){
				$sent = true;
				$out  ="<module name=\"users\" display=\"confirmation\">";
				if ($email!=""){
					$subject 	= "IMPORTANT - Registration email";
					$body 		= "Thank you $full_name for registering your interest in our site.
You are required to validate your email address by using the following link

http://".$this->parent->domain."/index.php?command=USERS_VALIDATE&email=$email&uid=$uid

Due to some email packages automatically cutting long lines please copy the complete url if it appears on multiple lines to a program like notepad and remove the returns before copying to your browser.

Thanks 
  The support team.";
					$to = $email; // change to $email for live tests
					$sent = $this->call_command("EMAIL_QUICK_SEND",Array(
						"subject"	=> $subject,
						"body"		=> $body,
						"to"		=> $to
						)
					);
					if (!$sent){
						$out .="<text type=\"error\"><![CDATA[".LOCALE_EMIAL_PROBLEM."]]></text>";
					}
					if ($sent){
						$out .="<text><![CDATA[".LOCALE_USERS_REGISTER_SENT_MSG."]]></text>";
						$subject 	 = "IMPORTANT - Registration email";
						$body 		 = "Contact details of new registered user.\n";
						foreach ($parameters as $key => $val){
							if (
								($key=="command") ||
								($key=="times_through") ||
								($key=="contact_identifier ") ||
								($key=="contact_times_through") ||
								($key=="user_login_pwd") ||
								($key=="user_confirm_login_pwd") ||
								($key=="x") ||
								($key=="y") ||
								($key=="SCRIPT_NAME") ||
								($key=="user_uid") ||
								($key=="PHPSESSID") ||
								($key=="contact_identifier") ||
								($key=="company_identifier") ||
								($key=="address_identifier") ||
								($key=="email_identifier") ||
								($key=="call_command") ||
								($key=="email_codex") ||
								($key=="prev_command")){
							}else{
								$body 	.= "$key :: $val.\n";
							}
						}
						$to = $this->check_parameters($form_restriction_list[-1],"email");
						$sent = $this->call_command("EMAIL_QUICK_SEND",Array(
							"subject"	=> $subject,
							"body"		=> $body,
							"to"		=> $to)
						);
					}
				} else {
					$out .="<text><![CDATA[".LOCALE_USERS_REGISTER_THANKYOU."]]></text>";
					$subject 	 = "IMPORTANT - Registration email";
					$body 		 = "Contact details of new registered user.\n";
					foreach ($parameters as $key => $val){
						if (
							($key=="command") ||
							($key=="times_through") ||
							($key=="contact_identifier ") ||
							($key=="contact_times_through") ||
							($key=="user_login_pwd") ||
							($key=="user_confirm_login_pwd") ||
							($key=="x") ||
							($key=="y") ||
							($key=="SCRIPT_NAME") ||
							($key=="user_uid") ||
							($key=="PHPSESSID") ||
							($key=="contact_identifier") ||
							($key=="company_identifier") ||
							($key=="address_identifier") ||
							($key=="email_identifier") ||
							($key=="call_command") ||
							($key=="email_codex") ||
							($key=="prev_command")){
						}else{
							$val = $this->check_locale_starter($val);
							$body 	.= "$key :: $val.\n";
						}
					}
					
					$to = $this->check_parameters($form_restriction_list[-1],"email");
					$sent = $this->call_command("EMAIL_QUICK_SEND",Array(
						"subject"	=> $subject,
						"body"		=> $body,
						"to"		=> $to)
					);
					
				}

				$out .="</module>";
				
			} else {
				$out  ="<module name=\"users\" display=\"confirmation\">";
				$out .="<text><![CDATA[".LOCALE_USERS_REGISTER_CONFIRM."]]></text>";
				$out .="</module>";
			}
		}
		return $out;
	}
	
	function validate_user($parameters){
		$email = $this->check_parameters($parameters,"email");
		$uid = $this->check_parameters($parameters,"uid");
		if (($uid=="") || ($email=="")){
			$out	 = "<module name=\"users\" display=\"confirmation\">";
			$out	.= "<text type=\"error\"><![CDATA[".LOCALE_USERS_VALIDATE_PROBLEM."]]></text>";
			$out 	.= "</module>";
		} else{
			$sql	 = "Select * from user_info 
							left outer join contact_data on contact_data.contact_user = user_info.user_identifier 
							left outer join email_addresses on email_addresses.email_contact = contact_data.contact_identifier 
						where email_addresses.email_address = '$email' and email_addresses.email_codex= '$uid'";
			$result  = $this->call_command("DB_QUERY",array($sql));
			$identifier = -1;
			$email_id	= -1;
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$identifier = $r["user_identifier"];
				$email_id	= $r["email_identifier"];
			}
			if ($identifier!=-1){
				$this->call_command("GROUP_SET_DEFAULT_FOR_USER",Array("identifier"=>$identifier));
			 	$sql	= "update user_info set user_status=2 where user_identifier=$identifier and user_client = $this->client_identifier";
				$result = $this->call_command("DB_QUERY",array($sql));
			}
			if ($email_id!=-1){
				$this->call_command("GROUP_SET_DEFAULT_FOR_USER",Array("identifier"=>$identifier));
			 	$sql	= "update email_addresses set email_verified=1 where email_identifier=$email_id and email_client = $this->client_identifier";
				$result = $this->call_command("DB_QUERY",array($sql));
			}
			$out	 = "<module name=\"users\" display=\"confirmation\">";
			$out	.= "<text><![CDATA[".LOCALE_USERS_VALIDATE_CONFIRMED."]]></text>";
			$out 	.= "</module>";
		}
		return $out;
	}

	function retrieve_unique_usernames($parameters){
		$login_name = $this->check_parameters($parameters,"user_login_name");
		$sql ="select * from user_info where user_login_name like '".$login_name."%' and user_client=$this->client_identifier";
		$result  = $this->call_command("DB_QUERY",array($sql));
		$names=Array();
		$names[count($names)] = $login_name;
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$names[count($names)] = $r["user_login_name"];
		}
		$max = count($names);
		$found=0;
		$list = Array();
		while ($found<6){
			$exists = false;
			$possible = $login_name.$this->generate_random_text(4);
			for($index=0,$max = count($names);$index<$max;$index++){
				if ($possible == $names[$index]){ 
					$exists=true;
					break;			
				}
			}
			if (!$exists){
				for($index=0,$max = count($list);$index<$max;$index++){
					if ($possible == $list[$index]){ 
						$exists=true;
						break;			
					}
				}
			}
			if (!$exists) {
				$found++;
				$list[count($list)]=$possible;
			}
		}
		$out="";
		for($index=0,$max = count($list);$index<$max;$index++){
			$out .= "<option value='".$list[$index]."'>".$list[$index]."</option>";
		}
		return $out;
	}
	function retrieve_my_docs($parameters){
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
			$max_grps = count($grp_info);
			$access_list = "";
			$access_array = array();
			$ALL=0;
			$open = 0;
			for($i=0;$i < $max_grps; $i++){
				$access = $grp_info[$i]["ACCESS"];
				$max = count($access);
				for($index=0;$index<$max;$index++){
					if ((substr($access[$index],0,strlen($this->module_command))==$this->module_command) || ($access[$index]=="ALL")){
						$open=1;
					}
				}
			}
			if ($open==1){
				$sql    = "
					select user_status, count(*) as total from user_info 
						left outer join contact_data on contact_data.contact_user = user_identifier 
					where user_client=$this->client_identifier 
						group by user_status 
					order by user_identifier desc
				";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				$result = $this->call_command("DB_QUERY",Array($sql));
				$out="<grouped label=\"User Information\">";
				if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
					$counter=0;
					while (($r=$this->call_command("DB_FETCH_ARRAY", array($result))) && ($counter<$this->page_size)){
					$status_label="";
						$status = $this->check_parameters($r,"user_status");
						if ($status=="0"){
							$status_label = "Total Blocked Users (".$r["total"].")";
						}
						if ($status=="1"){
							$status_label = "Total Unconfirmed Users (".$r["total"].")";
						}
						if ($status=="2"){
							$status_label = "Total Confirmed Users (".$r["total"].")";
						}
						$out .= "<title identifier=\"$status\"><![CDATA[".$status_label."]]></title>";
						$counter++;
					}
					$out .= "<commands><cmd label=\"List\">USERS_LIST</cmd></commands>";
				}else{
					$out .= "<text><![CDATA[".LOCALE_SORRY_NO_USERS."]]></text>";
				}
				$out .= "</grouped>";
				return "<module name=\"users\" label=\"".MANAGEMENT_USER."\" display=\"my_workspace\"><cmd label=\"List All\">USERS_LIST</cmd>".$out."</module>";
//<cmd label=\"".ADD_NEW."\">USERS_ADD</cmd>"
			}else{
				return "";
			}
		} else {
			return "";
		}
	}

	function show_profile($parameters){
		$parameters["access_command"]	= "USERS_REGISTRATION_SAVE";
		$parameters["times_through"]	= "0";
		$parameters["user_identifier"]	= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
		$out ="";
		if ($parameters["user_identifier"]>0){
			$show_module = $this->check_parameters($parameters,"show_module",1);
			if ($show_module==1){
				$out  ="<module name=\"users\" display=\"form\">";
			}
			$out .="<form name=\"user_form\" method=\"post\" label=\"".LOCALE_USERS_PROFILE."\">";
			$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_PROFILE_SAVE\"/>";
			$out .="<input type=\"hidden\" name=\"times_through\" value=\"1\"/>";
			$out .="<input type=\"password\" label=\"".ENTRY_PASSWORD."\" size=\"20\" name=\"user_login_pwd\" value=\"__KEEP__\" required=\"YES\"/>";
			$out .="<input type=\"password\" label=\"".ENTRY_PASSWORD_CONFIRM."\" size=\"20\" name=\"user_confirm_login_pwd\" value=\"__KEEP__\" required=\"user_login_pwd\"/>";
			if ($this->has_module_contact){
				$out .=$this->call_command("CONTACT_REGISTER_FORM",$parameters);
			}
			$extra = $this->call_command("ENGINE_RETRIEVE",array("ADD_TO_USER_PROFILE",$parameters));
			$length_of_extra = count($extra);
			for ($index=0;$index<$length_of_extra;$index++){
				if ($extra[$index][1]!=""){
					$out .=$extra[$index][1];
				}
			}
			$out .="<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
			$out .="</form>";
			if ($show_module==1){
				$out .="</module>";
			}
		}
		return $out;
	}
	
	function profile_user_save($parameters){
		$total_errors = 0;
		$extra_options = "";
		$login_pwd 		= $this->check_parameters($parameters,"user_login_pwd");
		$user_confirm_login_pwd = $this->check_parameters($parameters,"user_confirm_login_pwd");
		if ($login_pwd!=""){
			if ($login_pwd==$user_confirm_login_pwd){
				if (strlen($login_pwd)<6){
					$error_password = 1;
					$total_errors++;
					$login_pwd="";
				}else{
					$error_password = 0;
				}
			}else{
				$error_password = 1;
				$total_errors++;
				$login_pwd="";
			}
		}else{
			$error_password = 1;
			$total_errors++;
			$login_pwd="";
		}
		$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"USERS_SHOW_REGISTER"));
		if ($this->check_parameters($form_restriction_list,"contact_first_name","__NOT_FOUND__")!="__NOT_FOUND__"){
			$first_name 		= $this->check_parameters($parameters,"contact_first_name");
			if ($this->check_parameters($form_restriction_list["contact_first_name"],"required","0")!="0"){
				if ($first_name==""){
					$total_errors++;
				}
			}
		}
		if ($this->check_parameters($form_restriction_list,"contact_initials","__NOT_FOUND__")!="__NOT_FOUND__"){
			$initials			= $this->check_parameters($parameters,"contact_initials");
			if ($this->check_parameters($form_restriction_list["contact_initials"],"required","0")!="0"){
				if ($initials==""){
					$total_errors++;
				}
			}
		}
		if ($this->check_parameters($form_restriction_list,"contact_last_name","__NOT_FOUND__")!="__NOT_FOUND__"){
			$last_name			= $this->check_parameters($parameters,"contact_last_name");
			if ($this->check_parameters($form_restriction_list["contact_last_name"],"required","0")!="0"){
				if ($last_name==""){
					$total_errors++;
				}
			}
		}
		if ($this->check_parameters($form_restriction_list,"contact_email","__NOT_FOUND__")!="__NOT_FOUND__"){
			$email				= $this->check_parameters($parameters,"contact_email");
			$email_confirm		= $this->check_parameters($parameters,"contact_confirm_email");
			if ($this->check_parameters($form_restriction_list["contact_email"],"required","0")!="0"){
				if (($email!=$email_confirm) ||($email=="")){
					$total_errors++;
				}
			}
		}

		if ($total_errors>0){
			$out  ="<module name=\"users\" display=\"form\">";
			$out .="<form name=\"user_form\" method=\"post\" label=\"".LOCALE_USERS_REGISTER_INTREST."\">";
			$out .="<input type=\"hidden\" name=\"times_through\" value=\"1\"/>";
			$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_PROFILE_SAVE\"/>";
			if($error_password==1){
				$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_PASSWORD."]]></text>";
			}
			$out .="<input type=\"password\" label=\"".ENTRY_PASSWORD."\" size=\"20\" name=\"user_login_pwd\" value=\"$login_pwd\" required=\"YES\"/>";
			$out .="<input type=\"password\" label=\"".ENTRY_PASSWORD_CONFIRM."\" size=\"20\" name=\"user_confirm_login_pwd\" value=\"$login_pwd\" required=\"user_login_pwd\"/>";
			if ($this->has_module_contact){
				$parameters["access_command"] ="USERS_REGISTRATION_SAVE";
				$parameters["times_through"] =1;
				$out.= $this->call_command("CONTACT_REGISTER_FORM",$parameters);
			}
			$extra = $this->call_command("ENGINE_RETRIEVE",array("ADD_TO_USER_PROFILE",$parameters));
			$length_of_extra = count($extra);
			for ($index=0;$index<$length_of_extra;$index++){
				if ($extra[$index][1]!=""){
					$out .=$extra[$index][1];
				}
			}
			$out .="<input iconify=\"REGISTER\" type=\"submit\" value=\"".LOCALE_REGISTER_USER."\"/>";
			$out .="</form>";
			$out .="</module>";
		}else{
			$parameters["prev_command"]="USERS_EDIT";
			$parameters["user_login_name"]="__KEEP__";
			$parameters["user_identifier"]=$_SESSION["SESSION_USER_IDENTIFIER"];
			$parameters["times_through"]=1;
			$this->save($parameters);	
			$out  ="<module name=\"users\" display=\"confirmation\">";
			$out .="<text><![CDATA[".LOCALE_USERS_PROFILE_UPDATE."]]></text>";
			$out .="</module>";
		}
		return $out;
	}
	
	function list_forms(){
		/**
			* This is a list of the forms available to the website only NOT forms that the Administration 
			- can use.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/	
		return $this->available_forms;
	}
	
	function export($parameters){
		$output_array = Array();

		$group_filter=$this->check_parameters($parameters,"group_filter",0);
		$output_format=$this->check_parameters($parameters,"output_format","CSV");
		$page=$this->check_parameters($parameters,"page",1);
		$order_filter=$this->check_parameters($parameters,"order_filter",0);
		$user_filter_login_name=$this->check_parameters($parameters,"user_filter_login_name","");
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_list filter parameters",__LINE__,"[$group_filter,$order_filter,$page,$user_filter_login_name]"));
		}
		
		/**
		* Procude the SQL command that will retrieve the information from the database
		*/
		$where = "";
		$join="";
		if ($this->has_module_contact==1){
			$join.=" 
			left outer join contact_data on user_info.user_identifier=contact_data.contact_user
			left outer join email_addresses on contact_data.contact_identifier=email_addresses.email_contact
			";
		}
		if ($user_filter_login_name!=""){
			$where .= " and user_login_name like '%$user_filter_login_name%' ";
		}
		$sql = "Select * from user_info $join where user_info.user_client=$this->client_identifier $where order by ".$this->display_options[$order_filter][2]."";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		/**
		* what functionality options are available on this page
		*/
		if (!$result){
			/**
			* No Records were returned.
			*/
			
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
		}else{
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<10)){
				foreach ($r as $key => $value){
					$output_array[$counter][$key] = $r[$key];
				}
				$counter++;
			}
			return $this->export_format($output_array,$output_format);
		}
		return "";
	}
	
	function cache_available_forms($parameters){
		$frms = $this->available_forms;
		$lang="en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
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
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=ENGINE_RESTORE"));
	}

	function user_admin_access($parameters){
		$debug = $this->debugit(false, $parameters);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_form",__LINE__,"[$identifier]"));
		}
		$identifier = $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"user_identifier",-1));
		
		$grouplevel	= -1;
		$menu_locations = Array();
		$sql = "SELECT user_info.user_group, relate_user_menu.* FROM user_info inner join relate_user_menu on user_info.user_identifier = relate_user_menu.user_identifier where user_info.user_identifier = $identifier and user_client=$this->client_identifier";
		if ($debug) print "<p>".__FILE__." @ ".__LINE__." :: $sql</p>";
			if($user_result = $this->call_command("DB_QUERY",array($sql))) {
				while($r = $this->call_command("DB_FETCH_ARRAY",array($user_result))){
					$grouplevel	= $r["user_group"];
					$menu_locations[count($menu_locations)] = $r["menu_identifier"];
				}
				$this->call_command("DB_FREE",array($user_result));
			}
			//		$grouplevel		= $r["user_group"];
			$label			= "Edit User Access Restrictions";
			$submit_label	= "UPDATE_EXISTING";
			$channel_list 			= $this->call_command("ENGINE_RETRIEVE",array("RETURN_CHANNELS"));
			$access_list  			= $this->call_command("GROUP_GET_ACCESS",Array($grouplevel));
			$channels=array();
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations_indent_value",__LINE__,"[".count($channel_list)."]"));
			}
			for($index=0,$max=count($channel_list);$index<$max;$index++){
				for($aindex=0,$amax=count($access_list);$aindex<$amax;$aindex++){
					if (substr($access_list[$aindex],0,strlen($channel_list[$index][0])) == $channel_list[$index][0]){
						if (is_array($channel_list[$index][1])){
							$zmax=count($channel_list[$index][1]);
							for($zindex=0;$zindex<$zmax;$zindex++){
								$channels[count($channels)] = $channel_list[$index][1][$zindex];
							}
						}
					}
				}
			}
			
			$func_access = $this->user_pref(Array("return_xml" => false),$identifier);
//			if (count($func_access)>0){
				$no_selected="";
				$yes_selected="selected =\"true\"";
/*			}else{
				$no_selected="selected =\"true\"";
				$yes_selected="";
			}
*/
			$max=count($func_access);
			for($index=0;$index<$max;$index++){
				if($func_access[$index][0]=="can_use_editor"){
					if ($func_access[$index][1]==""){
						$no_selected="selected =\"true\"";
						$yes_selected="";
					}
				}
			}
		/**
		* generate the form for adding / editting the user details
		*/
		$out  ="<module name=\"users\" display=\"form\">";
		$out .="<page_options>";
		$out .="\t".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",array("CANCEL","USERS_LIST","RETURN_TO_LIST"));
		$out .="</page_options>";
		$out .="<form name=\"user_form\" method=\"post\" label=\"".SET_ACCESS."\">";
		$out .="<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
		$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_UPDATE_ACCESS\"/>";
		$out .="<text><![CDATA[".MODIFY_USER_ADMIN_ACCESS_WARNING."]]></text>";
		$out .="<checkboxes type='vertical' label=\"".ADMIN_LOCATION_ACCESS."\" name=\"user_relates_to_menu_entries\">".$this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_locations,"channels"=>$channels))."</checkboxes>";

		$out .="<radio label=\"".CAN_USER_CHOOSE_EDIT_MODE."\" name=\"can_use_editor\">";
		$out .="	<option value=\"\" $no_selected>".ENTRY_NO."</option>";
		$out .="	<option value=\"CAN_SWITCH_EDITOR\" $yes_selected>".ENTRY_YES."</option>";
		$out .="</radio>\n";
		$out .="<input iconify=\"SAVE\" type=\"submit\" value=\"".$this->get_constant($submit_label)."\"/>";
		$out .="</form>";
		$out .="</module>";
		return $out;
	}
	
	function user_admin_access_save($parameters){
		$debug = $this->debugit(false, $parameters);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_form",__LINE__,"[$identifier]"));
		}
		$user 	= $this->check_parameters($parameters,"identifier",-1);
		$locations		= $this->check_parameters($parameters,"user_relates_to_menu_entries",Array());
		for($index=0,$max=count($locations);$index<$max;$index++){
			$sql = "insert into relate_user_menu (user_identifier, menu_identifier) values($user, ".$locations[$index].");";
			if ($debug) print "<p>".__FILE__." @ ".__LINE__." :: $sql</p>";
			$this->call_command("DB_QUERY",array($sql));
		}
		if ($debug) $this->exitprogram();
		return "";
	}

	/***************************************************************************************************************************
	* Save function
	*
	* This function will allow the system to save the user details to the database.
	***************************************************************************************************************************/
	function save($parameters){
		$debug = $this->debugit(false,$parameters);
		$admin_group = 0;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save",__LINE__,"[".join($parameters,", ")."]"));
		}
		$out = $this->user_form($parameters);
		$user_relates_to_menu_entries = $this->check_parameters($parameters,"user_relates_to_menu_entries",Array());
		if (strlen($out)==0){
			unset($parameters["user_confirm_login_pwd"]);
			$login_pwd = $parameters["user_login_pwd"];
			if ($login_pwd!="__KEEP__"){
				$parameters["user_login_pwd"] = "".md5($parameters["user_login_pwd"])."";
			}else {
				unset($parameters["user_login_pwd"]);	
			}
			if ($parameters["user_login_name"]=="__KEEP__"){
				unset($parameters["user_login_name"]);	
			}
			$has_contact_module = $this->call_command("ENGINE_HAS_MODULE",array("CONTACT_"));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"previous command",__LINE__,"[".$parameters["prev_command"]."]"));
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if previous command was add then we want to insert the information into
			- the database
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($parameters["prev_command"]=="USERS_ADD"){
				$count=0;
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if the user_identifier exists then get rid of it (ie on an add it
				- is blank.
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				unset($parameters["user_identifier"]);
				$parameters["user_client"] = $this->client_identifier;
				$now = $this->libertasGetDate("Y/m/d H:i:s");
				$parameters["user_creation_date"]= "$now";
				if ($this->parent->server[LICENCE_TYPE]==SITE_WIZARD){
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- Site wizard product can only add Administrators 
					-  see after edit option for non sitewizard products code
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					$parameters["user_status"]=2;
					$parameters["group_list"]=Array();
					$sql = "select group_identifier from group_data where group_client=$this->client_identifier and group_type=2";
					$result = $this->call_command("DB_QUERY",array($sql));
					while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
						$parameters["group_list"][count($parameters["group_list"])]=$r["group_identifier"];
					}
				}
				$field_list ="";
				$value_list ="";
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- build the sql statement to insert the user information
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				reset($parameters);
				foreach ($parameters as $key => $val) {
					if ((strpos($key,"user_")===0) && ($key!="user_confirm_login_pwd") && $key!="user_relates_to_menu_entries"){
						if ($count>0){
							$field_list .= ", ";
							$value_list .= ", ";
						}
						$field_list .= $key;
						$value_list .= "'$val'";
						$count++;
					}
				}
				$sql="insert into user_info ($field_list) values ($value_list)";
				$this->call_command("DB_QUERY",array($sql));
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
				}
				$sql ="select * from user_info where user_creation_date='".$parameters["user_creation_date"]."' and user_login_name='".$parameters["user_login_name"]."'";
				$result = $this->call_command("DB_QUERY",array($sql));
				if ($result){
					while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
						$user_identifier = $r["user_identifier"];
					}
				}
//				print "identifier $user_identifier user_relates_to_menu_entries [".join(",",$user_relates_to_menu_entries)."]";
				$ug = $this->check_parameters($parameters,"group_list_1",array()); // Administrative group list
				if (count($ug)>0){
					$this->user_admin_access_save(Array("identifier" => $user_identifier, "user_relates_to_menu_entries" => $user_relates_to_menu_entries));
				}
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if previous command was edit then we want to update the information in
			- the database
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($parameters["prev_command"] == "USERS_EDIT"){
				$count=0;
				$field_list ="";
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- build the sql statement to update the user information
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$user_identifier = $parameters["user_identifier"];
				foreach ($parameters as $key => $val) {
					if ((strpos($key,"user_")===0) && ($key!="user_confirm_login_pwd") && ($key!="user_identifier") && $key!="user_relates_to_menu_entries"){
						if (($key=="user_login_name") && ($val!="__KEEP__")){
						
						}else{
							if ($count>0){
								$field_list .= ", ";
							}
							$field_list .= $key."='$val'";
							$count++;
						}
					}
				}
				if (strlen($field_list)>0){
					$sql="update user_info set $field_list where user_identifier=".$parameters["user_identifier"];
					$this->call_command("DB_QUERY",array($sql));
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
					}
				}
				$sql = "delete from relate_user_menu where user_identifier = $user_identifier;";
				if ($debug) print "<p>".__FILE__." @ ".__LINE__." :: $sql</p>";
				$this->call_command("DB_QUERY",array($sql));
				$ug = $this->check_parameters($parameters,"group_list_1",array()); // Administrative group list
				if (count($ug)>0){
					$this->user_admin_access_save(Array("identifier" => $user_identifier, "user_relates_to_menu_entries" => $user_relates_to_menu_entries));
				}
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if the system has a contact module then insert/update the users contact information
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			
			if ($has_contact_module){
				
				$contact_fields = $parameters; 
				if ($parameters["prev_command"]=="USERS_ADD"){
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- now that we have inserted the user information we need to get the
					- identifier for that user
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					
					$sql = "select * from user_info where user_login_name='".$parameters["user_login_name"]."' and user_login_pwd='".$parameters["user_login_pwd"]."'";
					
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
					}
					$results = $this->call_command("DB_QUERY",array($sql));
					if ($r = $this->call_command("DB_FETCH_ARRAY",array($results))){
						$contact_fields["contact_user"] = $r["user_identifier"];
					}
					$this->call_command("DB_FREE",array($results));
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- now that we have extracted the user identifier of the new user create a
					- entry in the contact table for personal information.
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					$contact_fields["contact_identifier"]="-1";
				}
				
				if ($parameters["prev_command"]=="USERS_EDIT"){
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- on edit then specify the contact_identifier
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					$contact_fields["contact_user"]			= $parameters["user_identifier"];
					$contact_fields["contact_identifier"]	= $parameters["contact_identifier"];
				}
				$this->call_command("CONTACT_SAVE",$contact_fields);
				$call = $this->check_parameters($parameters,"call_command");
				if ($call!=""){
					$this->call_command($call,$parameters);
				}
			}
			
			
			if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- non sitewizard products can define group and role access
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$total_number_of_group_lists= $this->check_parameters($parameters,"totalnumberofchecks_group_list",0);
				$mylist = Array();
				for($tnogl = 1; $tnogl <= $total_number_of_group_lists; $tnogl++){
					$ug = $this->check_parameters($parameters,"group_list_$tnogl",array());
					if (count($ug)>0){
						if (count($ug)>1){
							$ug_list = " in (" . join(",",$ug) . ")";
							$mylist = split(",",join(",",$mylist) . "," . join(",",$ug));
						} else {
							$ug_list = " = " . $ug[0];
							$mylist = split(",",join(",",$mylist) . "," . $ug[0]);
						}
						$sql = "select * from group_data where group_identifier $ug_list and group_client = $this->client_identifier";
						$result = $this->call_command("DB_QUERY",array($sql));
						while ($r= $this->call_command("DB_FETCH_ARRAY",array($result))){
							if ($r["group_type"]==2)
								$admin_group=1;
						}
						
						
					}
				}
				$user_identifier = $this->check_parameters($parameters,"user_identifier",$user_identifier);
				$this->call_command("GROUP_SET_BELONGING_TO_USER",Array("user_identifier" => $user_identifier, "group_list" => $mylist, "module"=>$this->webContainer));
			} else {
				$this->call_command("GROUP_SET_BELONGING_TO_USER",Array("user_identifier" => $user_identifier, "group_list" => $parameters["group_list"], "module"=>$this->webContainer));
			}
		}
		return $out;
	}
	
	/*****************************************************************************************************************************
	* user_form()
	*
	* This function will display the form for holding the user information in the system
	* the fucntion will communicate with the GROUP & CONTACT modules to allow the form to
	* expand with information from those forms thus allowing the form to expand with
	* requested information from different modules.
	****************************************************************************************************************************/
	function user_form($parameters){
		$error_username				= "";	
		$error_password				= "";
		$group						= Array();
		$user_confirm_login_pwd		= "";
		$user_identifier			= $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"user_identifier",-1));
		$prev_command 				= $this->check_parameters($parameters,"prev_command",$this->check_parameters($parameters,"command",""));
		$total_errors				= 0;
		$times_through 				= $this->check_parameters($parameters,"times_through",0);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_form",__LINE__,"[$identifier]"));
		}

		if ($times_through==0){
			$grouplevel			= -1;
			$current_status		=  1;
			$contact_identifier	= -1;
			$login_name 		= "";
			$stored_username	= "";
			$login_pwd 			= "";
			$grouplevel			= Array();
			$group_list			= Array();
			if ($user_identifier==-1){
				/**
				* if the identifer is empty then do not retrieve any information from the database
				*/ 
				$command="USERS_ADD";
				$label="Add a new user";
				$submit_label="ADD_NEW";
			} else {
				/**
				* if the identifer is not empty then do retrieve all the user information from the
				- database.
				*/ 
				$sql = "SELECT * FROM user_info where user_info.user_identifier = $user_identifier and user_client=$this->client_identifier";
				if($user_result = $this->call_command("DB_QUERY",array($sql))) {
					$r = $this->call_command("DB_FETCH_ARRAY",array($user_result));
					$login_name		= $r["user_login_name"];
					$stored_username= $login_name;
					$login_pwd 		= "__KEEP__";
					$user_confirm_login_pwd= "__KEEP__";
					$current_status	= $r["user_status"];
					$this->call_command("DB_FREE",array($user_result));
					$group_list = $this->call_command("GROUP_RETRIEVE_INFORMATION",Array("user_identifier" => $user_identifier));
				}
				//		$grouplevel		= $r["user_group"];
				$command		= "USERS_EDIT";
				$label			= LOCALE_USER_EDIT;
				$submit_label	= UPDATE_EXISTING;
			}
		}else{
			$label			= LOCALE_INVALID;
			$submit_label	= SAVE_DATA;
			$login_pwd 				= $this->check_parameters($parameters,"user_login_pwd");
			$user_confirm_login_pwd = $this->check_parameters($parameters,"user_confirm_login_pwd");
			$user_group				= $this->check_parameters($parameters,"user_group");
			$current_status			= $this->check_parameters($parameters,"user_status");
			$group_list				= $this->check_parameters($parameters,"group_list");
			$login_name 			= $this->check_locale_starter($this->check_parameters($parameters,"user_login_name"));
			$stored_username		= $login_name;
			$first_name 			= $this->check_locale_starter($this->check_parameters($parameters,"contact_first_name"));
			$last_name				= $this->check_locale_starter($this->check_parameters($parameters,"contact_last_name"));
			$email					= $this->check_locale_starter($this->check_parameters($parameters,"contact_email"));
			$email_confirm			= $this->check_locale_starter($this->check_parameters($parameters,"contact_confirm_email"));
			
			/**
			* define number of errors in form
			*/
			if ($login_name!="__KEEP__"){
				if ($this->check_parameters($parameters,"stored_username") != $login_name){
					$oname 				= $this->check_parameters($parameters,"other_login_names");
					if (($login_name=="") && ($oname!="")){
						$login_name=$oname;
					}
				}
				if ($login_name!=""){
					if ($this->check_parameters($parameters,"stored_username") != $login_name){
						if ($user_identifier!=""){
							$extra = "and user_identifier!=$user_identifier";
						}
						$sql="select * from user_info where user_login_name='$login_name' and user_client=$this->client_identifier $extra";
						$result  = $this->call_command("DB_QUERY",array($sql));
						if ($result){
							$counter = $this->call_command("DB_NUM_ROWS",Array($result));
						}else{
							$counter=0;
						}
						if ($counter>0){
							$error_username = 2;
							$total_errors++;
							$extra_options = $this->retrieve_unique_usernames($parameters);
							$login_name="";
						}else{
							$error_username = 0;
						}
					}
				}else{
					$error_username = 1;
					$total_errors++;
				}		
			}
			if ($login_pwd!="__KEEP__"){
				if ($login_pwd!=""){
					if ($login_pwd==$user_confirm_login_pwd){
						if (strlen($login_pwd)<6){
							$error_password = 1;
							$total_errors++;
							$login_pwd="";
						}else{
								$error_password = 0;
						}
					}else{
						$error_password = 1;
						$total_errors++;
						$login_pwd="";
						}
				}else{
					$error_password = 1;
					$total_errors++;
						$login_pwd="";
				}
			}

			$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"USERS_SHOW_REGISTER"));
			if ($this->check_parameters($form_restriction_list,"contact_first_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($this->check_parameters($form_restriction_list["contact_first_name"],"required","0")!="0"){
					$first_name 		= $this->check_locale_starter($this->check_parameters($parameters,"contact_first_name"));
					if ($first_name==""){
						$total_errors++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_last_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($this->check_parameters($form_restriction_list["contact_last_name"],"required","0")!="0"){
					$last_name			= $this->check_locale_starter($this->check_parameters($parameters,"contact_last_name"));
					if ($last_name==""){
						$total_errors++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_email","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($this->check_parameters($form_restriction_list["contact_email"],"required","0")!="0"){
					if (($email!=$email_confirm) || ($email=="")){
						$total_errors++;
					}
				}
			}
		}
		/**
		* generate the form for adding / editting the user details
		*/
		$client_information ="";
		if ($this->call_command("ENGINE_HAS_MODULE",Array("CONTACT_"))){
			$parameters["access_command"] ="USERS_REGISTRATION_SAVE";
			$parameters["user_identifier"] =$user_identifier;
			$client_information ="<section label='User Profile'>" . $this->call_command("CONTACT_REGISTER_FORM",$parameters)."</section>";
		}
		$out  ="";
		if (($times_through==0) || ($total_errors>0)){
		
		$out  ="<module name=\"users\" display=\"form\">";
		$out .="<page_options>";
		$out .="\t".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",array("CANCEL","USERS_LIST",LOCALE_CANCEL));
//		$out .="\t".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",array("ACCESS","USERS_SET_ADMIN_ACCESS","SET_ACCESS"));
		$out .= "<header><![CDATA[User Manager (".$label.")]]></header>";
		$out .="</page_options>";
		$out .="<form name=\"USERS_SHOW_REGISTER\" method=\"post\" label=\"".$this->get_constant($label)."\">";
		$times_through++;

		$out .="<input type=\"hidden\" name=\"stored_username\" ><![CDATA[$stored_username]]></input>";
		$out .="<input type=\"hidden\" name=\"times_through\" value=\"$times_through\"/>";
		$out .="<input type=\"hidden\" name=\"user_identifier\" value=\"$user_identifier\"/>";
		$out .="<input type=\"hidden\" name=\"prev_command\" value=\"$prev_command\"/>";
		$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_SAVE\"/>";
		$out .="<page_sections>";
		$out .="<section label='Login Details' name='detail'>";
		if($error_username==1){
			$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_USERNAME."]]></text>";
		}
		if($error_username==2){
			$out .="<text type=\"error\"><![CDATA[".LOCALE_REGISTER_USERNAME_EXISTS."]]></text>";
			$out .="<radio name=\"other_login_names\" label=\"".LOCALE_ENTRY_OPTIONAL_USERNAMES."\">$extra_options</radio>";
		}
		if ($this->check_parameters($parameters,"command","USERS_ADD")=="USERS_ADD"){
			$out .="<input type=\"text\" label=\"".ENTRY_USERNAME."\" size=\"20\" name=\"user_login_name\" required=\"YES\"><![CDATA[$login_name]]></input>";
		} else {
			$out .="<text label=\"".ENTRY_USERNAME."\" ><![CDATA[$login_name]]></text>";
			$out .="<input type='hidden' name=\"user_login_name\" ><![CDATA[$login_name]]></input>";
		}
		
		if($error_password==1){
			$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_PASSWORD."]]></text>";
		}
		$out .="<input type=\"password\" label=\"".ENTRY_PASSWORD."\" size=\"20\" name=\"user_login_pwd\" required=\"YES\"><![CDATA[$login_pwd]]></input>";
		$out .="<input type=\"password\" label=\"".ENTRY_PASSWORD_CONFIRM."\" size=\"20\" name=\"user_confirm_login_pwd\" required=\"user_login_pwd\"><![CDATA[$user_confirm_login_pwd]]></input>";
		/**
		* what is the status of the user
		*/
		$status = $this->get_status($current_status);
		$out .="<select label=\"".LOCALE_USER_STATUS."\" name=\"user_status\">$status</select>";
		$out .="</section>";
		/**
		* if the client has the group module then display the option
		*/
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$out .="<section label='Member of Group' name='usergroups' >";
			if ($this->has_module_group){
				/**
				* this code is for using a table to hold multiple entries 'group_admin_menu_access'
				*/ 
				if (count($group_list)==0 || $total_errors>0){
					$group_list = $this->call_command("GROUP_GET_DEFAULT",array("user_identifier" => $user_identifier));
				}
				$groups = $this->call_command("GROUP_RETRIEVE_BY_TYPE",array($group_list));
				$out .="<checkboxes type=\"horizontal\" label=\"".USER_MEMBER_OF_GROUP."\" name=\"group_list\" required=\"YES\" onclick=\"check_access\">$groups</checkboxes>\n";
			}
		$out .="</section>";
		}
		/**
		* if the client has the contact module then display the contact form
		*/
		$out .= $client_information;
		$parameters["user_identifier"] =$user_identifier;
		$extra = $this->call_command("ENGINE_RETRIEVE",array("ADD_TO_USER_PROFILE",$parameters));
		$length_of_extra = count($extra);
		for ($index=0;$index<$length_of_extra;$index++){
			if ($extra[$index][1]!=""){
				$out .= $extra[$index][1];
			}
		}
		/**
		* if the enterprise system is enabled then allow the specification of restricted
		* administrative access to menu locations.
		*/
		if ($this->parent->server[LICENCE_TYPE] == ECMS){
			$gt = 0;
			for ($index=0;$index < count($group_list);$index++){
				if ($group_list[$index]["TYPE"] == 2){
					$gt = 2;
				}
			}
			$out .="<section label='Administrator in these Locations' name='adminlocations' ";
			if ($gt!=2)
				$out .= "hidden='true'";
			$out .= ">";
			$menu_locations = Array();
			$grouplevel=-1;
			$sql = "SELECT user_info.user_group, relate_user_menu.* FROM user_info inner join relate_user_menu on user_info.user_identifier = relate_user_menu.user_identifier where user_info.user_identifier = $user_identifier and user_client=$this->client_identifier";
			if($user_result = $this->call_command("DB_QUERY",array($sql))) {
				while($r = $this->call_command("DB_FETCH_ARRAY",array($user_result))){
					$grouplevel	= $r["user_group"];
					$menu_locations[count($menu_locations)] = $r["menu_identifier"];
				}
				$this->call_command("DB_FREE",array($user_result));
			}
			//		$grouplevel		= $r["user_group"];
			$label			= "Edit User Access Restrictions";
			$submit_label	= "UPDATE_EXISTING";
			$channel_list 			= $this->call_command("ENGINE_RETRIEVE",array("RETURN_CHANNELS"));
			$access_list  			= $this->call_command("GROUP_GET_ACCESS",Array($grouplevel));
			$channels=array();
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations_indent_value",__LINE__,"[".count($channel_list)."]"));
			}
			for($index=0,$max=count($channel_list);$index<$max;$index++){
				for($aindex=0,$amax=count($access_list);$aindex<$amax;$aindex++){
					if (substr($access_list[$aindex],0,strlen($channel_list[$index][0])) == $channel_list[$index][0]){
						if (is_array($channel_list[$index][1])){
							$zmax=count($channel_list[$index][1]);
							for($zindex=0;$zindex<$zmax;$zindex++){
								$channels[count($channels)] = $channel_list[$index][1][$zindex];
							}
						}
					}
				}
			}
			$out .="<text><![CDATA[".MODIFY_USER_ADMIN_ACCESS_WARNING."]]></text>";
			$out .="<checkboxes type='vertical' label=\"".ADMIN_LOCATION_ACCESS."\" name=\"user_relates_to_menu_entries\">".$this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_locations,"channels"=>$channels))."</checkboxes>";
			$out .="</section>";
		}
		$out .="</page_sections>";
		$out .="<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
		$out .="</form>";
		$out .="</module>";
		} 
		return $out;
	}

	/*************************************************************************************************************************
    * save a user info as a new user or update
    *************************************************************************************************************************/
	function save_fba($parameters){
//		print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameters,true)."</p></li>";
//		$this->exitprogram();
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,print_r($parameters,true)));}
		$list 		= $this->check_parameters($parameters, "module_identifier");
		$identifier = $this->check_parameters($parameters, "ie_identifier",-1);
		/*************************************************************************************************************************
        * check to see if we are to set the current user to the new user details or just create an account
        *************************************************************************************************************************/
		$just_create= $this->check_parameters($parameters, "just_create",-1);
		if ($identifier==""){
			$identifier=-1;
		}
		$out = Array("error"=>Array());
		$override_suid		 = $this->check_parameters($parameters,"override_suid",0);
		if($just_create==-1){
			$suid			 = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
		} else {
			$suid			 = 0; // on admin submission don't be a user
		}
		$user_identifier	 = $this->check_parameters($parameters,"user_identifier",0);
		$user_status		 = $this->check_parameters($parameters,"user_status",1);
		$fbs_identifier	 	 = $this->check_parameters($parameters,"fbs_identifier",0);
		/*
		$fbs_expires_exists	 = $this->check_date($parameters,"__expires__", "__NOT_FOUND__");
		$fbs_expires		 = $this->check_date($parameters,"__expires__", "0000-00-00 00:00:00");
		$fbs_graces_exists	 = $this->check_date($parameters,"__grace__", "__NOT_FOUND__");
		$fbs_grace			 = $this->check_date($parameters,"__grace__", "0000-00-00 00:00:00");
		$fbs_review_exists	 = $this->check_date($parameters,"__review__", "__NOT_FOUND__");
		$fbs_review			 = $this->check_date($parameters,"__review__", "0000-00-00 00:00:00");
		$fbs_ex	 	 		 = $this->check_parameters($parameters,"__frm_expires__",0);
		*/
		$fbs_expires		 = $this->check_parameters($parameters,"__expires__",	"0000-00-00 00:00:00");
		$fbs_grace			 = $this->check_parameters($parameters,"__grace__",		"0000-00-00 00:00:00");
		$fbs_review			 = $this->check_parameters($parameters,"__review__",	"0000-00-00 00:00:00");
		
		$group_list			 = $this->check_parameters($parameters,"group_list",Array());
		$user_name			 = $this->check_parameters($parameters,"user_login_name");
		$user_pwd			 = $this->check_parameters($parameters,"user_login_pwd");
		$user_pwd_confirm	 = $this->check_parameters($parameters,"user_login_pwd");
		$error=0;
		if($user_pwd_confirm!=$user_pwd){
			$uid=0;
			$out["error"][count($out["error"])] =Array("user_login_pwd", "Passwords must match");
			$error++;
		} else {
			if(strlen($user_name)<6){
				$uid=0;
				$out["error"][count($out["error"])] =Array("user_login_name", "Your username must be at least 6 characters long");
				$error++;
			} 
			if(strlen($user_pwd)<6){
				$uid=0;
				$out["error"][count($out["error"])] =Array("user_login_pwd", "Passwords must be at least 6 characters long");
				$error++;
			} 
			if ($error==0){
				
			$uid=0;
			if($user_identifier==0){
					$sql = "select * from user_info where user_login_name = '$user_name' and user_client=$this->client_identifier";
					if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
//					print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
					$result  = $this->call_command("DB_QUERY",Array($sql));
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	            		$uid = $r["user_identifier"];
						$pwd = $r["user_login_pwd"];
//						print "[$pwd]";
						if($pwd!=md5($user_pwd)){
							$out["error"][count($out["error"])] =Array("user_login_name", "User name exists");
							$out["error"][count($out["error"])] =Array("user_login_name", "Your user/password incorrect");
							$uid=0;
							$error++;
						} else {
							// all fine 
						}
		            }
		            $this->call_command("DB_FREE",Array($result));
				}
//				print "<li>".__FILE__."@".__LINE__."<p>".print_r($out,true)."</p></li>";
//				print "<li>[$uid and $error and $suid]</li>";
                $user_date_expires		= "0000-00-00 00:00:00";
                $user_date_grace		= "0000-00-00 00:00:00";
                $user_date_review		= "0000-00-00 00:00:00";
				if($uid==0 && $error==0 && $suid==0){
//				print "<li>[$uid and $error and $suid]</li>";
					/*************************************************************************************************************************
                    * check for expiry dates
                    *************************************************************************************************************************/
					$user_creation_date = $this->libertasGetDate();
					$ok=0;
//					print "[".$this->parent->script." = $fbs_expires]";
					if($this->parent->script=="admin/index.php"){
						if ($fbs_expires!="0000-00-00 00:00:00"){
							$ok=1;
						}
					} else {
						$ok=1;
					}
//					print "[ok::$ok]";
						$sql = "select * from formbuilder_settings where fbs_identifier=$fbs_identifier and fbs_client=$this->client_identifier";
						$result  = $this->call_command("DB_QUERY",Array($sql));
        	            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							if ($fbs_expires!="0000-00-00 00:00:00"){
								$int_date_expires = strtotime($fbs_expires);
	            	        	$user_date_expires		= $fbs_expires;
							} else {
								$int_date_expires = strtotime($user_creation_date);
	            	        	$user_date_expires		= Date("Y/m/d H:i:s", mktime(0,0,0, Date("m",$int_date_expires), date("d",$int_date_expires)+$r["fbs_life"], Date("Y",$int_date_expires)));
							}
							//print "[int_date_expires :: $int_date_expires]";
							$fbs_g = $fbs_grace;
							$fbs_r = $fbs_review;
							if($fbs_g == "0000-00-00 00:00:00"){
	                	    	$user_date_grace		= Date("Y/m/d H:i:s", 
									mktime(
										date("H", strtotime($user_date_expires)),
										date("i", strtotime($user_date_expires)),
										date("s", strtotime($user_date_expires)),
										date("m", strtotime($user_date_expires)), 
										date("d", strtotime($user_date_expires))+$r["fbs_grace"], 
										date("Y", strtotime($user_date_expires))
									)
								);
							} else {
								$user_date_grace		= $fbs_g;
							}
							if($fbs_r == "0000-00-00 00:00:00"){
    	                		$user_date_review		= Date("Y/m/d H:i:s", 
									mktime(
										date("H", strtotime($user_date_expires)),
										date("i", strtotime($user_date_expires)),
										date("s", strtotime($user_date_expires)),
										Date("m", strtotime($user_date_expires)), 
										date("d", strtotime($user_date_expires))-$r["fbs_review"], 
										Date("Y", strtotime($user_date_expires))
									)
								);
	    					} else {
								$user_date_review			= $fbs_r;
							}
	                	}
						$this->call_command("DB_FREE",Array($result));
					if($ok==1){
					}
//					print "<li>".__FILE__."@".__LINE__."<p>$user_date_expires $user_date_grace $user_date_review</p></li>";
					// no user exists so we can create one and we are not logged in
					$uid = $this->getUID();
					$user_login_pwd = md5($user_pwd);
					$user_uid = substr($user_login_pwd,0,5);
					$out["user_uid"] = $user_uid;
					/*************************************************************************************************************************
	                * insert a new user and make as not approved
	                *************************************************************************************************************************/
					if($user_identifier==0){
						$sql = "insert into user_info 
									(user_identifier, user_login_name, user_login_pwd, user_status, user_creation_date, user_uid, user_client, user_date_expires, user_date_grace, user_date_review)
								values 
									('$uid', '$user_name', '$user_login_pwd', '$user_status', '$user_creation_date', '$user_uid', $this->client_identifier, '$user_date_expires', '$user_date_grace', '$user_date_review')";
						if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
						$this->call_command("DB_QUERY",Array($sql));
						/*************************************************************************************************************************
		                * retrieve the default group for new users and add this user to it unless specified by admin
		                *************************************************************************************************************************/
						if(count($group_list)>0){
							for($i=0;$i<count($group_list);$i++){
								$sql = "insert into groups_belonging_to_user (group_identifier, user_identifier, client_identifier) values ('".$group_list[$i]."', '$uid', '$this->client_identifier')";
//								print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
								if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
								$this->call_command("DB_QUERY",Array($sql));
								$sql = "insert into group_to_object (gto_identifier, gto_object, gto_client, gto_module, gto_rank) values ('".$group_list[$i]."', '$uid', '$this->client_identifier', '$this->webContainer',0)";
//								print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
								if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
								$this->call_command("DB_QUERY",Array($sql));
							}
						} else {
							$gid=-1;
							$sql = "select * from group_data 
									inner join group_to_object on gto_identifier=group_identifier and gto_client=group_client and gto_object=$fbs_identifier and gto_module ='FORMBUILDER_'
								where group_client=$this->client_identifier";
							if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
	//						print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
							$result  = $this->call_command("DB_QUERY",Array($sql));
			                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			                	$gid = $r["group_identifier"];
								$sql = "insert into groups_belonging_to_user (group_identifier, user_identifier, client_identifier) values ('$gid', '$uid', '$this->client_identifier')";
								if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
	//							print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
								$this->call_command("DB_QUERY",Array($sql));
								$sql = "insert into group_to_object (gto_identifier, gto_object, gto_client, gto_module, gto_rank) values ('$gid', '$uid', '$this->client_identifier', '$this->webContainer',0)";
								if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
								$this->call_command("DB_QUERY",Array($sql));
	//							print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			                }
		    	            $this->call_command("DB_FREE",Array($result));
						}
					} else {
						$extra="";
						if ($user_login_pwd!="__KEEP__" && $user_login_pwd!=md5("__KEEP__")){
							$extra ="user_login_pwd 		= '$user_login_pwd', ";
						}
						$sql = "update user_info 
									set $extra
										user_login_name 	= '$user_name', 
										user_status 		= '$user_status', 
										user_date_expires 	= '$user_date_expires', 
										user_date_grace 	= '$user_date_grace', 
										user_date_review 	= '$user_date_review'
									where user_identifier = $user_identifier and user_client = $this->client_identifier";
						$uid =$user_identifier;
						$this->call_command("DB_QUERY",Array($sql));
					}
				}
			}
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
//		$this->exitprogram();
			if($uid==0 && $suid!=0){
				$out["error"][count($out["error"])] =Array("user_login_name", "Your user/password incorrect");
				$uid=0;
				$error++;
			}
		}
		if($just_create==-1){
			$_SESSION["SESSION_USER_IDENTIFIER"] = $uid;
		}
		$sql = "select * from user_to_object where uto_client = $this->client_identifier and uto_module='FORMBUILDERADMIN_' and uto_object='$fbs_identifier'";
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
		$result		= $this->call_command("DB_QUERY",Array($sql));
		$num_rows	= $this->call_command("DB_NUMROWS",Array($result));
		if($num_rows==0){
			$sql = "insert into user_to_object (uto_identifier, uto_client, uto_module, uto_object) values ($uid, $this->client_identifier, 'FORMBUILDERADMIN_', '$fbs_identifier')";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$out["user_identifier"]= $uid;
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
			$this->call_command("DB_QUERY",Array($sql));
		}
		$out["errorCount"]= $error;
		return $out;
	}
	/*************************************************************************************************************************
    * retrieve the list of fields available
    *************************************************************************************************************************/
	function get_field_list($parameters){
		$as = $this->check_parameters($parameters,"as","XML");
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$out = Array();
		$out[count($out)] = Array("name"=>"user_identifier",	"label"=>"", 			"type"=>"hidden", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"user_login_name",	"label"=>"Username",	"type"=>"text", 	"map"=>"", "auto"=>"", "required"=>"yes");
		$out[count($out)] = Array("name"=>"user_login_pwd",		"label"=>"Password", 	"type"=>"password",	"map"=>"", "auto"=>"", "required"=>"yes");
		if($as == "Array"){
			return $out;
		} else {
			$outd   = "<module name=\"".$this->module_name."\" display=\"fields\">\n";
			$i = count($out);
			for ($index=0; $index<$i;$index++){
				$outd .= "<field>";	
				foreach($out[$index] as $key => $value){
					$outd .= "<$key><![CDATA[$value]]></$key>";	
				}
				$outd .= "</field>\n";	
			}
			$outd .= "</module>";	
			return $outd;
		}
	}
	/*************************************************************************************************************************
	* get a list of the options for a field
	*************************************************************************************************************************/
	function get_field_options($parameters){
		$as = $this->check_parameters($parameters,"as","XML");
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$field = $this->check_parameters($parameters,"field","");
		if($as == "Array"){
			return Array();
		} else {
			return "";
		}
	}
	/*************************************************************************************************************************
    * a gateway function to retrieve an advanced form builder form entry
    **************************************************************************************************************************/
	function load_fba($parameters){
//		print_r($parameters);
		$mod_fields	= $this->check_parameters($parameters, "mod_fields", Array());
		$user 		= $this->check_parameters($parameters, "user", -1);
		if($user==-1){
			$sql = "select * from user_info where user_client=$this->client_identifier and user_identifier = ".$_SESSION["SESSION_USER_IDENTIFIER"]."";
		} else {
			$sql = "select * from user_info where user_client=$this->client_identifier and user_identifier = ".$user."";
		}
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$values = Array();
		$status=0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if($status==0){
				$mod_fields[count($mod_fields)] = Array("user_status", "", "system", "", "", "USERS_::-1", "0", "value"=>$r["user_status"],"required"=>0);
			}
			$status++;
			for($i=0;$i<count($mod_fields);$i++){
				if($mod_fields[$i][5]=="USERS_::-1"){
					$mod_fields[$i]["value"] = $this->check_parameters($r, $mod_fields[$i][0]);
				}
			}
        }
        $this->call_command("DB_FREE",Array($result));
//		print_r($mod_fields);
		return $mod_fields;
	}
	/*************************************************************************************************************************
    * renew a user subscription
	*
	* this is a special function that is available to the web user so can be abused, to stop possible abuse of this function 
	* it will only accept one parameter, thus it must be called by another function as there will be more than one parameter 
	* if this is called from the address bar of a web browser.
	*
	* @param Array only one parameter ("uid" = user identifier)
	* return String returns an empty string.
    *************************************************************************************************************************/
	function renewal($parameters){
		if(count($parameters)!=1){
			return ""; // false can only be called with one parameter
		}
		$uid = $this->check_parameters($parameters,"uid");
		/*************************************************************************************************************************
        * get the form that specifies the expriy info for this user
        *************************************************************************************************************************/
		$sql = "select distinct * from user_info 
					inner join user_to_object on uto_identifier=user_identifier and user_client=uto_client
					where uto_client = $this->client_identifier and uto_identifier = $uid and uto_module = 'FORMBUILDERADMIN_'";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result  = $this->parent->db_pointer->database_query($sql);
		$fba_id = -1;
		$current_expires	= 0;
		$current_grace		= 0;
		$current_renewal	= 0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
       		$fba_id 			= $r["uto_object"];
			$current_expires	= $r["user_date_expires"];
			$current_grace		= $r["user_date_grace"];
			$current_renewal	= $r["user_date_review"];
			if($current_expires=="0000-00-00 00:00:00"){
				$current_expires	= 0;
			} else {
				$current_expires = strtotime($current_expires);
			}
			if($current_grace=="0000-00-00 00:00:00"){
				$current_grace		= 0;
			} else {
				$current_grace = strtotime($current_grace);
			}
			if($current_renewal=="0000-00-00 00:00:00"){
				$current_renewal	= 0;
			} else {
				$current_renewal = strtotime($current_renewal);
			}
//			print_R($r);
        }
        $this->parent->db_pointer->database_free_result($result);
//		print "<li>uto_object :: $fba_id</li>";
		$get_dates = $this->call_command("FORMBUILDERADMIN_GET_DATES", Array("identifier"=>$fba_id));
		$exp = $this->check_parameters($get_dates,"expires",0);
		$ren = $this->check_parameters($get_dates,"renewal",0);
		$gra = $this->check_parameters($get_dates,"grace",0);
		$new_expires	= 0;
		$new_grace		= 0;
		$new_renewal	= 0;
		if($exp!=0){
			if($current_expires!=0){
				/*************************************************************************************************************************
                * update renewals
                *************************************************************************************************************************/
				$new_expires	= date("Y-m-d H:i:s",strtotime("+$exp day", $current_expires));
				$new_grace		= date("Y-m-d H:i:s",strtotime("+$gra day", strtotime($new_expires)));
				$new_renewal	= date("Y-m-d H:i:s",strtotime("-$ren day", strtotime($new_expires)));
			}
		}
		if($new_expires!=0){
			$sql = "update 
						user_info 
					set user_date_expires='$new_expires', user_date_grace='$new_grace', user_date_review='$new_renewal' 
						where user_client = $this->client_identifier and user_identifier = $uid";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$this->parent->db_pointer->database_query($sql);
		}
		return "";
	}
}
?>