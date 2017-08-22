<?PHP
/*************************************************************************************************************************
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.payment_gateway_worldpay_admin.php
* @date 08 Nov 2004
*************************************************************************************************************************/
/*************************************************************************************************************************
* This module is the administration module for the Payment Gateway.
*************************************************************************************************************************/
require_once dirname(__FILE__)."/libertas.payment_gateway_admin.php";
class paymentgateway_worldpay_admin extends paymentgateway_admin{
	/*************************************************************************************************************************
	*  Class Variables (generic
	*************************************************************************************************************************/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_name_label			= "Payment Gateway - WorldPay (Administration)";
	var $module_name				= "paymentgateway_admin";
	var $module_admin				= "1";
	var $module_command				= "PAYGATEADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "PAYGATEADMIN_";
	var $module_label				= "MANAGEMENT_PAYMENTGATEWAY";
	var $module_modify		 		= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.2 $';
	var $module_creation 			= "26/02/2004";
	/*************************************************************************************************************************
    * Lists to be used by payment systems 
    *************************************************************************************************************************/
	var $testModes = Array(
		Array("101", "LOCALE_TEST_MODE_FAIL_ALL"),
		Array("100", "LOCALE_TEST_MODE_PASS_ALL")
	);
	/**
    * field, label and type 
	*
	* this array is used in the account setup functions to define the fields that are to be sent with the order 
	* these fields are defined once for the client, and used in all orders
    */
	var $setupProperties			= Array(
		Array("instId",		"Your Unique WorldPay Installation ID Number","text"),
		Array("currency",	"Select the currency your prices are in","__CURRENCY__"),
		Array("testMode",	"Enable Test Mode","__TEST__")
	);
	/**
    * field and type 
	*
	* this array is used in the account setup functions to define the fields that are to be sent with the order 
	* these fields are defined per order
    */
	var $generatedProperties		= Array(
		Array("cartId",	"__SYS_REFERENCE__"),
		Array("desc",	"__USR_REFERENCE__")
	); // to be defined per instance
	
	/*************************************************************************************************************************
	*                                D I R E C T O R Y   S E T U P   F U N C T I O N S
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
		$this->load_locale("payment_gateway_worldpay_admin");
		parent::initialise();
		return 1;
	}
	/*************************************************************************************************************************
	*                         P A Y M E N T   M A N A G E R   F U N C T I O N S   F O R   W O R L D P A Y
	*************************************************************************************************************************/
	
}

?>