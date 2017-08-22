<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.module_template.php
* @date 
*/
/**
* 
*/
class temp extends module{
	/**
	*  Class Variables
	*/
	var $module_name				= "temp";								// name of module is used in configuration
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT|LOCALE_MANAGEMENT_GROUP_ACCESS|LOCALE_MANAGEMENT_GROUP_PREFS|LOCALE_MANAGEMENT_GROUP_REPORTS";		// what group does this module belong to
	var $module_name_label			= "";									// label describing the module 
	var $module_label				= "MANAGEMENT_THEMES";					// label describing the module 
	var $module_creation			= "20/02/2003";							// date module was created
	var $module_modify	 		= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.5 $';					// Actual version of this module
	var $module_admin				= "1";									// does this system have an administrative section
	var $module_command				= ""; 									// what does this commad start with ie TEMP_ (use caps)
	var $module_display_options		= array();								// what output channels does this module have
	var $module_admin_options 		= array();								// what options are available in the admin menu
	var $module_admin_user_access 	= array();								// specify types of access for groups

	/**
	*  Class Methods
	*/
	
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug || true){
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
			if ($user_command==$this->module_command."GET_MODULE"){
				return $this->get_module_name();
			}
			if ($user_command==$this->module_command."GET_VERSION"){
				return $this->get_module_version();
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
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
		* define some access functionality
		*/
		$this->module_admin_options			= array(
			array($this->module_command."SELECTION", "Select Site Theme"),
			array($this->module_command."LIST", 	 "Manage Theme(s)")
		);
		$this->module_admin_user_access		= array(
			array($this->module_command."ALL", "COMPLETE_ACCESS")
		);
	}
	
		

}
?>