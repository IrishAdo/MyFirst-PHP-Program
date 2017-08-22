<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- $Revision: 1.1 $, $Date: 2004/04/14 19:37:37 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
if (!defined("LOADED_GUESTBOOKADMIN_LOCALE")){
	define ("LOADED_GUESTBOOKADMIN_LOCALE"					, 1);
	define ("LOCALE_GUESTBOOK_WORKFLOW_STATUS_LOGGED_IN"	, "User must register first no anonymous access to add functions");
	define ("LOCALE_GUESTBOOK_WORKFLOW_STATUS_FREE_ACCESS"	, "Everyone can publish directly to the site");
	define ("LOCALE_GUESTBOOK_WORKFLOW_STATUS_REG_PUB"		, "Registered users can publish to the site directly, anonymous comments to be approved");
	define ("LOCALE_GUESTBOOK_WORKFLOW_STATUS_CLOSED"		, "All content to be approved unless user has approval status set.");
}

?>