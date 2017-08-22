<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

if (!defined("LOADED_PAYMENTGATEWAY_ADMIN_LOCALE")){
	define ("LOADED_PAYMENTGATEWAY_ADMIN_LOCALE"	, 1);
	define ("LOCALE_PAYMENT_SETUP"					, "Setup Payment Account");
	define ("LOCALE_PAYMENT_URI"					, "Url to the Payment Gateway (specifiy http:// or https:// or other)");
	define ("MANAGE_PAYMENT_SETUP"					, "Manage Payment Setup");
	define ("MANAGEMENT_PAYMENTGATEWAY"				, "Payment System");
	define ("LOCALE_NO_TEST_MODE"					, "Test mode disabled");
	define ("LOCALE_TEST_MODE_FAIL_ALL"				, "Test mode enabled (Always Fail)");
	define ("LOCALE_TEST_MODE_PASS_ALL"				, "Test mode enabled (Always Pass)");
	define ("LOCALE_CONFIRM_MSG"					, "What message do you wish to display on successful purchase");
	define ("LOCALE_DENY_MSG"						, "What message do you wish to display on a failed purchase");
//	define ("LOCALE_CONFIRM_MSG_TAB"				, "Confirm Msg");
//	define ("LOCALE_DENY_MSG_TAB"					, "Fail Msg");
/*	
	define (""			,"");
	define (""			,"");
	define (""			,"");
	define (""			,"");
	define (""			,"");
	define (""			,"");
	define (""			,"");
	define (""			,"");
*/
	if(!defined("MANAGE_PAYMENT_ORDERS")){
		define ("MANAGE_PAYMENT_ORDERS"					, "Manage Payment Orders");
	}
	define ("ACCESS_LEVEL_SETUP"				, "Administer Purchase Gateway");
	define ("ACCESS_LEVEL_ORDER_MANAGER"		, "Order Manager");

}

?>