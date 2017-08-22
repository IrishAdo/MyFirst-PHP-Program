<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
define("LOCALE_MENU_SETTING_SYSPREFS_","System Settings");
define("LOCALE_MENU_SETTING_SFORM_","Form Settings");
define("LOCALE_MENU_SETTING_FILES_","File Settings");
define("LOCALE_MENU_SETTING_COMMENTSADMIN_","Comment Settings");
define("LOCALE_MENU_SETTING_PAGE_","PAGE Settings");
define("LOCALE_MENU_SETTING_RSSADMIN_","RSS Settings");
define("LOCALE_MENU_SETTING_USERACCESS_","Access Log Settings");
define("MANAGEMENT_FILTER","Filter Manager");
define("MANAGEMENT_CONTENTTABEL","Table of Content Manager");

if (!defined("LOADED_GENERAL_LOCALE")){
	define ("LOADED_GENERAL_LOCALE"					, 1);
	define ("LOCALE_MANAGEMENT_GROUP_INTERACTIVE"	, "Interactive tools");
	define ("LOCALE_MANAGEMENT_GROUP_CONTENT"		, "Content Manage");
	define ("LOCALE_MANAGEMENT_GROUP_SECURITY"		, "Security");
	define ("LOCALE_MANAGEMENT_GROUP_REPORTS"		, "Reports");
	define ("LOCALE_MANAGEMENT_GROUP_EXTENSION"		, "Extensions");
	define ("LOCALE_MANAGEMENT_GROUP_PREFS"			, "Preferences");
	define ("LOCALE_MANAGEMENT_GROUP_SYSTEM"		, "System");
	
	define ("LOCALE_RESULTS"				, "Results");
	define ("LOCALE_MSGS"					, "Messages");
	define ("LOCALE_ELERT_SIGNUP"			, "Alert me when someone posts a new message");
	define ("ENTRY_MENU_LOCATION"			, "Site location");
	define ("ENTRY_FORMAT_RICHTEXT"			, "Richtext version");
	define ("ENTRY_FORMAT_HTML"				, "HTML version");
	define ("ENTRY_TITLE"					, "Title");
	define ("EDIT_EXISTING"					, "Edit");
	define ("ADD_NEW"						, "Add new entry");
	define ("ENTRY_COMMENTS"				, "Read comments");
	define ("ENTRY_DELETE"					, "Delete");
	define ("LOCALE_WORKFLOW"				, "Workflow for this item");
	define ("ENTRY_DESCRIPTION"				, "Description.");
	define ("ENTRY_NO"						, "No");
	define ("ENTRY_PREVIEW"					, "Preview");
	define ("ENTRY_SAVE"					, "Save");
	define ("ENTRY_SUMMARY"					, "Summary");
	define ("ENTRY_YES"						, "Yes");
	define ("ENTRY_DATE_MODIFIED"			, "Last modified");
	define ("NO_KEEP"						, "No keep this.");
	define ("SAVE_DATA"						, "Save");
	define ("ONE_RESULT"					, "Displaying 1 result");
	define ("REMOVE_EXISTING"				, "Delete");
	define ("RESULT"						, "results.");
	define ("NO_CHANNEL_DEFINED"			, "No channel defined.");
	define ("ENTRY_STATUS"					, "Version Status");
	define ("LOCALE_STATUS"					, "Status");
	define ("LOCALE_CANCEL"					, "Cancel");
	define ("LOCALE_REQUIRED_FIELDS"		, " Denotes required fields");
	define ("RETURN_TO_LIST"				, "Return to the list of entries.");
	define ("SHORT_DESCRIPTION"				, "Please enter a short description.");
	define ("SORRY_NO_RESULTS"				, "Sorry there were no results returned");
	define ("UPDATE_EXISTING"				, "Update this entry.");
	define ("YES_REMOVE"					, "Yes remove this.");
	define ("OPTION_DISPLAY_ALL"			, "Display all entries");
	define ("LOCALE_OPTION_DISPLAY_ALL_LOCATIONS", "All Locations");
	define ("STATUS_NOT_LIVE"				, "Not live");
	define ("STATUS_LIVE"					, "Live");
	define ("LOCALE_SEARCH_LABEL"			, "Site Search");
	define ("LOCALE_SEARCH_BOX_DEFAULT_TXT"	, "Search phrase");
	define ("LOCALE_LIST"					, "List");
	define ("LOCALE_UNDEFINED"				, "Not defined");
	define ("LOCALE_DATE_REVIEW"			, "Review");
	define ("LOCALE_DATE_PUBLISHED"			, "Published");
	define ("LOCALE_DATE_MODIFIED"			, "Last modified");
	define ("LOCALE_DATE_AVAILABLE"			, "Available");
	define ("LOCALE_LABEL"					, "Label");
	define ("LOCALE_SETTINGS"				, "Settings");
	define ("LOCALE_DISPLAY_OPTIONS"		, "Choose display option");
	define ("LOCALE_MANAGE_LIST"			, "Manage list");
	define ("LOCALE_DESCRIPTION"			, "Description");
	define ("LOCALE_ACCESSKEYS"				, "Thankyou for updating your AccessKeys Configuration options.");
	define ("LOCALE_PROPERTIES"				, "Properties");
	define ("LOCALE_SET_INHERITANCE"		, "Should the children of these locations automatically inherit this setting");
	define ("LOCALE_WHAT_LOCATIONS"			, "What locations of the site should this appear");
	define ("LOCALE_URL"					, "URL");
	define ("LOCALE_YOURNAME"				, "Your name");
	define ("LOCALE_LOGIN_TO_USE"			, "You must login first");
	define ("LOCALE_LOST_ENTRY"				, "Lost Entry");
	define ("LOCALE_LOCATIONS"				, "Published Locations");
	define ("LOCALE_EXTRACT_LOCATIONS"		, "Extract Locations");
	define ("LOCALE_LOCATION_TAB"			, "Location Tab");
	define ("LOCALE_PREVIEW_LOADING"		, "<p align='center'>&#160;</p><p align='center'>&#160;</p><p align='center'>&#160;</p><p align='center'>Please wait while the preview loads</p><p align='center'><img src='/libertas_images/editor/libertas/lib/themes/default/img/working.gif'></p><p align='center'>&#160;</p><p align='center'>&#160;</p><p align='center'>&#160;</p><p align='center'>&#160;</p>");
	define ("LOCALE_LIST_USERS"				, "List Users");
	define ("LOCALE_CREATOR"				, "Can Create");
	define ("LOCALE_APPROVER"				, "Can Approver");
	define ("LOCALE_USE_SSL"				, "Use Secure Socket Layer (SSL) Note:: only set if you have purchased an SSL Layer");
	define ("LOCALE_ALIGN_CENTER"			, "Center");
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -	Access level Roles (Generic labels)
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	define ("ACCESS_LEVEL_AUTHOR"			, "Author Access");
	define ("ACCESS_LEVEL_GROUP_PREMISSIONS", "User can restrict item to specific groups");
	define ("ACCESS_LEVEL_APPROVER"			, "Approver Access");
	define ("ACCESS_LEVEL_PUBLISHER"		, "Publisher Access");
	define ("ACCESS_LEVEL_ARCHIVEST"		, "Archive Access");
	define ("ACCESS_LEVEL_CLONER"			, "Clone Access");
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -	Locale Clone
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	define("LOCALE_CLONE_ENTRY"				, "Make a copy");
	define("LOCALE_TITLE_EXISTS"			, "Please change the title of this page as a page with this title already exists.");
	define("LOCALE_SESSION","Session");
	define ("LOCALE_FORM_SETTINGS"				, "Email Form Settings");

	define ("LOCALE_WEBOBJECT_CONTAINERS"				, "Manage site containers");
	define ("LOCALE_WEBOBJECT_CONTAINER_POSITIONING"	, "Manage site layout");
	define ("LOCALE_DISPLAY_BASIC_SEARCH"									,"Display a basic search");
	define ("LOCALE_DISPLAY_ADVANCED_SEARCH"								,"Display an advanced search");
	define ("LOCALE_DISPLAY_FEATURES"										,"Show a featured list");
	define("LOCALE_DISPLAY_A2Z_WIDGET"										,"Display the A to Z widget");
	
	/* Modify by Ali Imran*/
	define ("SUBSCRIBE_EXISTING"					, "Subscribe");
	define ("UNSUBSCRIBE_EXISTING"					, "Unsubscribe");
	/* End Modification of Ali Imran*/

}

?>