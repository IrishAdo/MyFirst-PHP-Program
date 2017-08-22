<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.Session.php
* @date 4 Dec 2002
*/
/** 
* A class to allow the management of session information in what ever way the 
* developer wishes replacing this function will allow future developers to change
* the way that sessions are managed to conform to future security features.
*
* NOTE do NOT confuse this with {@link libertas.clusterSession.php} which is a 
* different technology 
*/
class session extends module{
	/**
	*  Class Variables
	*/
	var $module_name			= "session";
	var $module_name_label		= "Session Management Module";
	var $module_label			= "LOCALE_SESSION";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:13 $';
	var $module_version 		= '$Revision: 1.9 $';
	var $module_debug			= false;
	var $module_creation		= "04/12/2002";
	var $module_command			= "SESSION_";
	var $module_admin			= "1";
	// all commands specifically for this module will start with this token
	
	/**
	*  Class Methods
	*/
	
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			//			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
			
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"command",__LINE__,"[".count($parameter_list)."]"));
			
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
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			if ($user_command==$this->module_command."RETRIEVE"){
				return $this->session_retrieve($parameter_list[0]);
			}
			if ($user_command==$this->module_command."GET"){
				return $this->session_variable("GET",$parameter_list[0]);
			}
			if ($user_command==$this->module_command."SET"){
				return $this->session_variable("SET",$parameter_list[0],$parameter_list[1]);
			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->display_session();
			}
			if ($user_command==$this->module_command."DESTROY"){
				session_destroy();
				return 1;
			}
			return "";
		}else{
			return "";
			// wrong command sent to module
		}
	}
	
	/**
	* This is the function to set or get a specific session variable
	*/
	function session_variable($access,$variable,$value="undefined"){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Session Varaible",__LINE__,"[$access,$variable,$value]"));
		}
		
		if ($access=="GET"){
			$ret = "";
			if (!empty($_SESSION[$variable])){
				$ret = $_SESSION[$variable];
			}
			$return = $ret;
		}else{
			$_SESSION[$variable]=$value;
			$return = 1;
		}
		return $return;
	}
	
	/**
	* This is the function to retrieve the session variables
	- You must have accessed a page within the last five minutes
	*/
	function session_retrieve($session_identifier){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Session Retrieve",__LINE__,"[$session_identifier]"));
		}
	}

	function display_session(){
		$client 					= $this->parent->client_identifier;
		$user_identifier 			= $this->check("SESSION_USER_IDENTIFIER","");
		$session_management_access	= $this->check("SESSION_MANAGEMENT_ACCESS",Array());
		$email 						= $this->check("SESSION_EMAIL","");
		$loggedin					= $this->check("SESSION_LOGGED_IN","0");
		$first_name					= $this->check("SESSION_FIRST_NAME","");
		$last_name					= $this->check("SESSION_LAST_NAME","");
		$group_info					= $this->check("SESSION_GROUP",Array());
		$group_type					= $this->check("SESSION_GROUP_TYPE",0);
		$user_cmds					= $this->check("SESSION_USER_COMMANDS","");
		$user_date					= $this->check("SESSION_DATE_TIME","");
		$session_func_access		= $this->check("SESSION_FUNC_ACCESS","");
		
		$session_man_access="";
		for($index=0,$max=count($session_management_access);$index<$max;$index++){
			$session_man_access .= "<location>".$session_management_access[$index]."</location>";
		}
		$out_text="\n<session name=\"".session_name()."\" user_identifier=\"$user_identifier\" session_identifier=\"".session_id()."\" ip_address=\"".$_SERVER["REMOTE_ADDR"]."\" logged_in=\"$loggedin\" client=\"$client\">";
		//Modified By Ali Imran to check page address from XSL
		$out_text="\n<session name=\"session1\" user_identifier=\"$user_identifier\" session_identifier=\"".session_id()."\" page_address=\"".$_SERVER['PHP_SELF']."\" logged_in=\"$loggedin\" client=\"$client\">";
		//End Modifiaction By Ali Imran
		$out_text.="\n<name><first_name>$first_name</first_name><last_name>$last_name</last_name></name>";
		$out_text.="\n<groups type=\"$group_type\">";
		$max = count($group_info);
		for ($index=0; $index < $max; $index++){
			$access = "";
			$list = $group_info[$index]["ACCESS"];
			$lenght_of_array=count($list);
			for($i=0;$i < $lenght_of_array;$i++){
				$access .= "<access>".$list[$i]."</access>";
			}
			$group_id 		= $group_info[$index]["IDENTIFIER"];
			$group_label	= $group_info[$index]["LABEL"];
			$group_type		= $group_info[$index]["TYPE"];
			$out_text.="\n<group identifier=\"$group_id\" label=\"$group_label\" type=\"$group_type\">".$access."</group>";
		}
		$out_text.="\n</groups>";
		
		$out_text.="\n<email>".$email."</email>";
		$out_text.="\n<admin_restriction><locations>$session_man_access</locations><functionality>$session_func_access</functionality></admin_restriction>";
		$out_text.="\n<user_commands>".$user_cmds."</user_commands>";
		$out_text.="\n<last_date>".$user_date."</last_date>";
			$out_text.="\n<editorial>Yes</editorial>";
		if (true){
		}
		$out_text.="\n</session>\n";
		return $out_text;
	}
	
	function check($test,$default=""){
		if (isset($_SESSION[$test])){
			$value = $_SESSION[$test];
		} else {
			$value = $default;
		}
		return $value;
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
		* Table structure for table 'File_info'
		*/
		
		$fields = array(
			array("SessionID"			,"varchar(255)"		,"NOT NULL"	,""),
			array("LastUpdated"			,"datetime"			,"NOT NULL"	,""),
			array("Client"				,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("user_id"				,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("ipaddress"			,"varchar(15)"		,"NOT NULL"	,""),
			array("DataValue"			,"text"				,""			,"default ''")
		);
		$tables[count($tables)] = array("sessions", $fields, "SessionID");
		return $tables;
		//file_associations
	}

}
?>