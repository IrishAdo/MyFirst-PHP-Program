<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.embededinfo.php
* @date 03 Dec 2002
*/
/**
* Task manager module NOT complete
*/

class taskmanager extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name				= "task_manager";
	var $module_name_label			= "Task Management Module (SYSTEM)";
	var $module_admin				= "1";
	var $module_channels			= Array();
	var $searched					= 0;
	var $module_modify	 		= '$Date: 2005/02/08 17:01:14 $';
	var $module_version 			= '$Revision: 1.7 $';
	var $module_command				= "TASK_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_TASKS";
	/**
	*  Management Menu entries
	*/
	var $module_admin_options 		= array();
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access	= array();
	
	
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
	
	function command($user_command, $parameter_list=array()){
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
			if ($this->module_admin_access==1){
				if ($user_command==$this->module_command."SUBMIT"){
					$this->submit_task($parameter_list);
				}
			}
		}else{
			// wrong command sent to system
			return "";
		}
	}
	function list_available_fields(){}
	/**
	* Initialise function
	-----------------------
	- This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	- module::initialise() function allowing you to define any extra constructor
	- functionality.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* define if access is allowed 
		*/
		if (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
			$this->module_admin_access=1;
		}
		return 1;
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
		* Table structure for table 'embed_libertas_form'
		*/
		/*
		$fields = array(
			array("embed_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
			array("trans_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("client_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("form_int_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("form_str_identifier"		,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("module_starter"			,"varchar(50)"		,"NOT NULL"	,"default ''")
		);
		$primary ="embed_identifier";
		$tables[count($tables)] = array("embed_libertas_form", $fields, $primary);
		*/
		return $tables;
	}
	
	function submit_task($parameters){
		$to 		= $this->check_parameters($parameters, "to", "");
		$from 		= $this->check_parameters($parameters, "from", $this->check_parameters($_SESSION,"SESSION_EMAIL",""));
		$msg	 	= $this->check_parameters($parameters, "msg", "");
		$user		= $this->check_parameters($parameters, "author", $this->check_parameters($parameters, "user_identifier", -1));
		$subject	= $this->check_parameters($parameters, "subject", "no subject supplied");
		if ($user!=-1){
			$sql ="select email_address from email_addresses
				inner join contact_data on email_contact = contact_identifier
			where 
				contact_user = $user and email_client=$this->client_identifier";
			$destination_email = "";
			$result 	= $this->call_command("DB_QUERY",Array($sql));
			if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
				while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$destination_email = $r["email_address"];
				}
			}
		} else {
			if ($to!=''){
				$destination_email = $to;
			} else {
				$destination_email = "";
			}
		}
		if ($destination_email!=""){
			$this->call_command("EMAIL_QUICK_SEND",Array(
				"from" => $from,
				"subject" => $subject,
				"body" => $msg,
				"to" => "$destination_email")
			);
		}
	}
	
}
?>