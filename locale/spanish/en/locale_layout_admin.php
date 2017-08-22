<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

if (!defined("LOADED_LAYOUT_ADMIN_LOCALE")){
	define ("LOADED_LAYOUT_ADMIN_LOCALE"									, 1);
	define ("LOCALE_LAYOUTADMIN_CHOOSE_CHANNEL_INHERITANCE"					, "Should this menu item use the same channels as its parent?");
	define ("LOCALE_LAYOUTADMIN_INHERIT_YES"								, "Yes, Inherit channels from the location I will publish to");
	define ("LOCALE_LAYOUTADMIN_INHERIT_NO"									, "No I will define the channels that are in this location");
	define ("LOCALE_LAYOUTADMIN_CHANNEL_MANAGER"							, "Channel Manager");
	define ("LOCALE_LAYOUTADMIN_MENU_ACCESS_RESTRICTIONS"					, "Select user groups you wish only to be able to view this menu location, leave blank for no restrictions.");
	define ("LOCALE_LAYOUTADMIN_CHOOSE_CHANNEL"								, "Choose the channel(s) that will be published to this location");
	define ("LOCALE_LAYOUTADMIN_CHOOSE_LOCATION_DISPLAY_FORMAT"				, "How would you like content published to this location displayed?");
	define ("LOCALE_LAYOUTADMIN_ACCESS_RESTRICTIONS"						, "Access restrictions");
	define ("LOCALE_ADVANCED_OPTIONS"										, "Advanced Options");
	define ("LOCALE_LOCATION_VISIBLE"										, "Is this menu location visible");
	define ("LOCALE_LAYOUT_ADMIN_MENU_ALT_TEXT"								, "Short description");
	define ("LOCALE_LAYOUTADMIN_MENU_LABEL"									, "What would you like to call this menu item?");
	define ("LOCALE_LAYOUTADMIN_WHERE_IN_SITE_STRUCTURE"					, "Where should this appear within your site structure?");
	define ("MANAGE_MENU_AUTHOR"											, "Page Author can manage locations");
	define ("MANAGE_INFORMATION_IMPORT"										, "Import Data");
	if (!defined("MANAGEMENT_LAYOUT")){
		define ("MANAGEMENT_LAYOUT"											, "Menu");
		define ("MANAGEMENT_DIRECTORY"										, "Directory Manager");
		define ("MANAGEMENT_MENU"											, "Structure Manager");
		define ("MANAGE_LAYOUT"												, "Manage Layout");
		define ("MANAGE_DIRECTORY"											, "Manage Directories");
		define ("MANAGE_MENU"												, "Manage Menus");
		define ("MANAGE_GROUP_ACCESS"										, "Manage Group access");
		define ("MANAGE_CHANNEL"											, "Manage Channels");
		define ("MANAGE_ADVANCED"											, "Manage Advanced");
	}
	define("LOCALE_LAYOUTADMIN_CHOOSE_SUMMARY_IMAGE_DISPLAY"				, "What way should Summary images be displayed?");
	define("LOCALE_LAYOUTADMIN_CHOOSE_SUMMARY_IMAGE_DISPLAY_LEFT"			, "Display to the left");
	define("LOCALE_LAYOUTADMIN_CHOOSE_SUMMARY_IMAGE_DISPLAY_RIGHT"			, "Display to the right");
	define("LOCALE_LAYOUTADMIN_CHOOSE_SUMMARY_IMAGE_DISPLAY_ALT"			, "Display on alternative sides");
	define("LOCALE_LAYOUTADMIN_MENU_URL"									, "What URL should this go to");
	define("ADD_NEW_EXTERNAL"												, "Add External link");
}

?>