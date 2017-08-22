<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
if (!defined("LOADED_ELERTADMIN_LOCALE")){
	define ("LOADED_ELERTADMIN_LOCALE"					, 1);
	define ("MANAGE_OPTINOUT_LISTS"						, "Manage opt in/out lists");
	define ("LOCALE_ELERTS_USER_MANAGER"				, "Manage List of Users");
	define ("LOCALE_ELERTS_SECTION_MANAGER"				, "Section Manager");
	define ("LOCALE_ELERT_ENABLE_DISABLE_ADMINLISTS"	, "User can manage opt in/out lists");
	define ("LOCALE_ELERT_SIGNUP_LISTS"					, "Manage Signups");
	define ("LOCALE_ELERT_SECTION_LISTS"				, "Manage Sections");
	define ("LOCALE_ELERT_MANAGE_YOUR_LIST"				, "Manage your Elerts");
	define ("LOCALE_ELERT_ENABLE_OPTOUT"				, "Enable optout");
	define ("LOCALE_ELERT_DISABLE_OPTOUT"				, "Disable optout");
	define ("LOCALE_ELERT_STATUS"						, "Can users opt out");
	define ("LOCALE_CURRENT_LIST_USERS"					, "Current List of Users");
	define ("LOCALE_ELERT_SECTION_DEFINITION"			, "Section Definition");
	define ("LOCALE_ELERT_WHAT_LOCATIONS"				, "Choose the locations that this Elert section should watch");
	define ("LOCALE_ELERT_ALL_LOCATIONS"				, "Should this Section notify people if any location is modified");
	define ("LOCALE_ELERT_INHERIT_LOCATIONS"			, "Should child locations be included in the notification list");
	define ("LOCALE_ELERT_SECTION_QUESTION"				, "Notify me if ...");
	if (!defined("MANAGEMENT_ELERTS")){
		define ("MANAGEMENT_ELERTS"									, "Manage Elerts");
	}
	if (!defined("LOCALE_ELERT_DISPLAY_CHANNEL")){
		define ("LOCALE_ELERT_DISPLAY_CHANNEL"						, "Display the Elert Sign Up Form here");
	}
	define ("LOCALE_ELERT_EMAIL_MSG"					, "Default Email Messages");
	define ("LOCALE_ELERTS_MESSAGES_CONFIRM"			, "Email Message Saved");
	define ("LOCALE_ELERTS_MESSAGES"					, "Email Message to Signed up users");
	define ("LOCALE_ELERTS_MESSAGES_CONFIRM_MSG"		, "Thankyou the body fo the email that will be sent to recipients has been saved.");
	define ("LOCALE_ELERT_SIGNED_UP"					, "Signed up yet?");
	define ("LOCALE_ELERT_ENABLE_EMAIL"					, "Subscribe for email Notification");
	define ("LOCALE_ELERT_DISABLE_EMAIL"				, "Unsubscribe form email Notification");
	
	define ("LOCALE_ELERT_PAGE_AUTHOR"					, "Page Author");
	define ("LOCALE_ELERT_PAGE_APPROVER"				, "Page Approver");
	define ("LOCALE_ELERT_PAGE_PUBLISHER"				, "Page Publisher");
	define ("LOCALE_ELERT_WEB_USER_PAGE"				, "Web Users - Page Update");
	define ("LOCALE_ELERT_GUESTBOOK_APPROVER"			, "Guest Book Approver");
	define ("LOCALE_ELERT_WEB_USER_GUESTBOOK"			, "Web Users - Guestbook Update");
	define ("LOCALE_ELERT_FORUM_APPROVER"				, "Forum Approver");
	define ("LOCALE_ELERT_WEB_USER_FORUM"				, "Web Users - Forum Update");
	define ("LOCALE_ELERT_INFODIR_APPROVER"				, "Information Directory Approver");
	define ("LOCALE_ELERT_WEB_USER_INFODIR"				, "Web Users - Information Directory Update");
	define ("LOCALE_ELERT_COMMENTS_APPROVER"			, "Comments Approver");
	define ("LOCALE_ELERT_WEB_USER_COMMENTS"			, "Web Users - Page Comments Update");
	define ("LOCALE_ELERT_CREATE"						, "Create List");
	define ("LOCALE_ELERT_DEFAULT_EMAIL"				, "Default Email Msg for");
	define ("LOCALE_ELERT_DEFINE_EMAILS"				, "Define default email");
 	define ("LOCALE_ELERT_ADMIN_OPTOUT_LISTS"			, "Check your Elert sign up status");
	define ("ELERTADMIN_CONTAINER"						, "Elert Configuration Container");
	define ("LOCALE_ELERT_SHOP_ORDER_PROCESSOR"			, "Email when shop order recieved");
}

?>