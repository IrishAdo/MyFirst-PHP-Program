<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

if (!defined("LOADED_ELERT_LOCALE")){
	if (!defined("MANAGEMENT_ELERTS")){
		define ("MANAGEMENT_ELERTS"									, "Manage Elerts");
	}
	if (!defined("LOCALE_ELERT_DISPLAY_CHANNEL")){
		define ("LOCALE_ELERT_DISPLAY_CHANNEL"						, "Display the Elert Sign Up Form here");
	}
	define ("LOADED_ELERT_LOCALE"								, 1);
	define ("LOCALE_ELERT_CHOOSE_SECTION"						, "Notify you when ...");
	define ("LOCALE_ELERT_SORRY_LOGIN_REQUIRED"					, "Sorry you must be logged in to use this feature");
	define ("LOCALE_ELERT_SORRY_LOGIN_REQUIRED_LABEL"			, "Elert SignUp Warning");
	define ("LOCALE_ELERT_SIGNUP_LABEL"							, "Elert SignUp");
	define ("LOCALE_ELERT_SIGNUP_CONFIRM"						, "Thank you your signup options have been updated.");
	define ("LOCALE_ELERT_CHOOSEN_URLS"							, "Choosen Urls to Watch");
	define ("LOCALE_ELERT_SIGNUP_ALREADY_WATCHING"				, "You are already watching this url");
	define ("LOCALE_ELERT_SIGNUP_WATCHING_LABEL"				, "What Label do you wish to give this.");
	define ("LOCALE_ELERT_NO_WATCHES"							, "No watches Currently Assigned");
}

?>