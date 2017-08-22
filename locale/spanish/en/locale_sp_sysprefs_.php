<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- $Revision: 1.7 $, $Date: 2005/02/08 14:35:37 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

if (!defined("LOADED_SP_SYSPREFS_LOCALE")){
	define ("LOADED_SP_SYSPREFS_LOCALE"					, 1);
	define('LOCALE_SP_TIME_OUT_MINUTES'			,'Time before system logs out users (minutes)');
	define('LOCALE_SP_PAGE_SIZE'				,'How many items should be listed before spanning takes effect.');
	define('LOCALE_SP_COMBO_YEAR'				,'What year should select combo boxes start.');
	define('LOCALE_SP_COMPRESSION'				,'Do you wish to send compressed data (Yes/No)?');
	define('LOCALE_SP_AUTO_SUMMARISE'			,'Do you wish to auto generate summaries if the user forgets (Yes/No)?');
	define('LOCALE_SP_PRIVACY'					,'Do you wish to Supply a Privacy String');
	define('LOCALE_SP_EDIT_SUMMARY'				,'Do you want to be able to edit summaries and keywords.');
	define('LOCALE_SP_USE_ANTISPAM'				,'Enable spam protection');
	define('LOCALE_SP_PAGE_TITLE_IS_CAPS'		,'Should the page label be forced into upper case on all pages');
	define('LOCALE_SP_CAN_SAVE_NOTES'			,'Can save notes');
	define('LOCALE_SP_COMMENTS_OPEN'			,'Comments available');
	define('LOCALE_SP_DEFAULT_TIME_FORMAT'		,'Choose the time format');
	define('LOCALE_SP_SECURED_PAGE_REDIRECT'	,'If link goes to a secured location redirect he user to what');
	define('LOCALE_SP_PAGE_OPTIONS'				,"What page option keys should be used&lt;br&gt;
	&lt;ul&gt;
		&lt;li&gt;PTR - Printer Friendly&lt;/li&gt;
		&lt;li&gt;COM - Page comments&lt;/li&gt;
		&lt;li&gt;EAF - Email A Friend&lt;/li&gt;
		&lt;li&gt;TXT - Text Only&lt;/li&gt;
		&lt;li&gt;TOP - Top of Page&lt;/li&gt;
		&lt;li&gt;HOME- Home&lt;/li&gt;
	&lt;ul&gt;");
	define ('LOCALE_SP_SSL_AVAILABLE'			,'Is a Secure Socket Layer (SSL) in place on this site?');
	define ('LOCALE_SP_POWERBY_IN_NEW_WINDOW'	,'Should the Power By link open in a new window');
}

?>