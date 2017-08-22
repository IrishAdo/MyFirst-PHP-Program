<?PHP
/*************************************************************************************************************************
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.soap_admin.php
* @date 08 Nov 2004
*************************************************************************************************************************/
/*************************************************************************************************************************
* This module is the administration module for the Soap Service Server.
*************************************************************************************************************************/
class soap_admin {
	/*************************************************************************************************************************
	*  Class Variables (generic
	*************************************************************************************************************************/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_name_label			= "Soap Service (Administration)";
	var $module_name				= "soap_admin";
	var $module_admin				= "1";
	var $module_command				= "SOAPADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "SOAPADMIN_";
	var $module_label				= "MANAGEMENT_SOAP";
	var $module_modify		 		= '$Date: 2005/02/08 17:01:14 $';
	var $module_version 			= '$Revision: 1.2 $';
	var $module_creation 			= "26/02/2004";
	/*************************************************************************************************************************
    * Commands mapping for service
	*
	* To stop soap giving complete access to all of the functions of the LIBERTAS Content Management Service it has been 
	* deemed necessary to define a list of functions that are available to the soap server, these mappings will be stored inthis variable
	*************************************************************************************************************************/
	var $command_mapping			= Array();
	
	/*************************************************************************************************************************
	*                                          S O A P   S E T U P   F U N C T I O N S
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
		* load the required locale
		*************************************************************************************************************************/
		$this->load_locale("soap_admin");
		parent::initialise();
		return 1;
	}
	/*************************************************************************************************************************
	*                         
	*************************************************************************************************************************/
	
}

?>