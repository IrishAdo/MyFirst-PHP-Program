<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

if (!defined("LOADED_LAYOUT_LOCALE")){
	define ("LOADED_LAYOUT_LOCALE"											, 1);
	if (!defined("MANAGEMENT_LAYOUT")){
		define ("MANAGEMENT_LAYOUT"												, "Menu");
		define ("MANAGEMENT_DIRECTORY"											, "Directory Manager");
		define ("MANAGEMENT_MENU"												, "Menu");
		define ("MANAGE_LAYOUT"													, "Manage Layout");
		define ("MANAGE_DIRECTORY"												, "Manage Directories");
		define ("MANAGE_MENU"													, "Manage Menus");
		define ("MANAGE_GROUP_ACCESS"											, "Manage Group access");
		define ("MANAGE_CHANNEL"												, "Manage Channels");
		define ("MANAGE_ADVANCED"												, "Manage Advanced");
		define ("LAYOUT_CONTAINER"												, "Layout Objects");
	}
}

?>