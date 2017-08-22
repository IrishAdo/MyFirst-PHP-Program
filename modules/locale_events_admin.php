<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	define ("MANAGEMENT_EVENTS"												,"Events");
	define ("LOCALE_EVENTADMIN_DATABASE"									, "Event Database");

if (!defined("LOADED_INFODIR_ADMIN_LOCALE")){
	define ("LOADED_INFODIR_ADMIN_LOCALE"									, 1);
	define ("LOCALE_INFODIR_STATUS"											,"Status of Events Directory");
	define ("LOCALE_INFODIR_DISPLAY_OPTIONS"								,"Category display options");
	define ("LOCALE_INFODIR_DISPLAY_COLUMNS"								,"How many columns");
	define ("LOCALE_INFODIR_HIDE_CATEGORIES"								,"Hide Category Paths");
	define ("LOCALE_INFODIR_DISPLAY_1_LVL"									,"Show only one level at a time");
	define ("LOCALE_INFODIR_DISPLAY_2_LVL"									,"Show two levels at a time");
	define ("MANAGE_INFORMATION_EXPORT"										,"Export Directory");
	define ("LOCALE_INFO_DIR_EXPORT_HEADER"									,"Export directory informtion screen");
	define ("LOCALE_INFO_DIR_EXPORT_PROGRESS_HEADER"						,"Export directory progress screen");
	define ("LOCALE_INFODIR_SUMMARY_STRUCTURE"								,"What way do you want to display the summary screen");
	define ("LOCALE_INFODIR_ECOMMERCE_ENABLED"								,"Enable Ecommerce on this directory");
	define ("LOCALE_INFODIR_INMENU"											,"Display category list in Menu");
	define ("LOCALE_INFODIR_RESTRICT_TO_SUMMARY"							, "Restrict access to summary screen?");
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	define ("LOCALE_DIRECTORY_LABEL"										,"Label");
	define ("LOCALE_INFODIR_SEARCHRESULT_SPANS"								,"When should spanning take effect on search results?");
	define ("LOCALE_DIRECTORY_ECOMMERCE_ADD_LABEL"							,"Override the 'Add to basket' label");
	define ("LOCALE_DIRECTORY_ECOMMERCE_NOSTOCK_LABEL"						,"Override the 'Out of Stock' label");
	define ("LOCALE_DIRECTORY_ECOMMERCE_ADD_LABEL_DEFAULT"					,"Book Now");
	define ("LOCALE_DIRECTORY_ECOMMERCE_NOSTOCK_LABEL_DEFAULT"				,"Fully Booked");
	define ("LOCALE_INFODIR_ECOMMERCE_NO_STOCK_DISPLAY"						,"Should stock with a quantity of zero be displayed?");
	define ("LOCALE_CHOOSE_MENU"											,"Choose a menu location");
	define ("LOCALE_UPDATE_NO_EDIT_FROM_SITE"								,"Administrator only");
	define ("LOCALE_UPDATE_ALL_USERS"										,"Anyone");
	define ("LOCALE_UPDATE_AUTHOR_ONLY"										,"Owner");
	
	define ("LOCALE_DISABLED"												, "Disabled");
	define ("LOCALE_KEEP_ALL"												, "Keep All");
	define ("LOCALE_ONLY_1"													, "Keep 1 version");
	define ("LOCALE_ONLY_2"													, "Keep 2 versions");
	define ("LOCALE_ONLY_3"													, "Keep 3 versions");
	define ("LOCALE_ONLY_4"													, "Keep 4 versions");
	define ("LOCALE_ONLY_5"													, "Keep 5 versions");
	define ("LOCALE_ONLY_6"													, "Keep 6 versions");
	define ("LOCALE_ONLY_7"													, "Keep 7 versions");
	define ("LOCALE_ONLY_8"													, "Keep 8 versions");
	define ("LOCALE_ONLY_9"													, "Keep 9 versions");
	define ("LOCALE_ONLY_10"												, "Keep 10 versions");
	
	define("LOCALE_ADMIN_ONLY","Administrators Only");
	define("LOCALE_REGUSERS_ONLY","Registered Users and Admin");
	define("LOCALE_ANYONE","Anyone");

	define("LOCALE_INFODIR_WHO_CAN_SUBMIT"									,"Who can submit a new entry");
	define("LOCALE_INFODIR_REQUIRES_APPROVAL"								,"Needs Approved");
	define("LOCALE_INFODIR_WHO_CAN_UPDATE"									,"Who can update this entry");
	define("LOCALE_INFO_DIRECTORY_HEADER"									,"Events");
	define("LOCALE_AS_RSS"													,"Available as RSS");
	define("LOCALE_LABEL_SHOW"												,"Show Label");
	if(!defined("LOCALE_NOTLIVE")){
		define("LOCALE_NOTLIVE"													,"Not Live");
	}
}

?>