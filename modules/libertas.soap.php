<?PHP
/*************************************************************************************************************************
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.soap.php
* @date 08 Nov 2004
*************************************************************************************************************************/
/*************************************************************************************************************************
* This module will submit a soap bubble
*************************************************************************************************************************/
class soap {
	/*************************************************************************************************************************
	*  Class Variables (generic
	*************************************************************************************************************************/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_name_label			= "Soap Service (Presentation)";
	var $module_name				= "soap";
	var $module_admin				= "1";
	var $module_command				= "SOAP_"; 		// all commands specifically for this module will start with this token
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
		$this->load_locale("soap");
		parent::initialise();
		return 1;
	}
	/*************************************************************************************************************************
	*                         
	*************************************************************************************************************************/
	function get(){
		$body		= $this->check_parameters($parameters,"soap_bubble");
		$endpoint	= $this->check_parameters($parameters,"endpoint");
		$wsdl		= $this->check_parameters($parameters,"wsdl");
		$soap_ptr = new SoapClient($wsdl);
		
		$msg		= "<"."?xml version=\"1.0\" encoding=\"UTF-8\"?".">
		<soapenv:Envelope 
		xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" 
		xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\" 
		xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\">
			<soapenv:Body>
				 $body
			</soapenv:Body>
		</soapenv:Envelope>";

	}
}
/*
prod=funny_book,item_amount=18.50x1;prod=sad_book,item_amount=16.50x2
*/
?>