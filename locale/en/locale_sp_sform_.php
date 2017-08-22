<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- $Revision: 1.3 $, $Date: 2005/03/15 15:20:13 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

if (!defined("LOADED_SP_SFORM_LOCALE")){
	define ("LOADED_SP_SFORM_LOCALE"				, 1);
	define('LOCALE_SFORM_DEFAULT_EMAIL'				,'Default Email address forms should use when sending emails');
	define('LOCALE_SP_SFORM_SHOW_REFERER'				,'Show link to users previous page');
	define('LOCALE_SP_SFORM_SHOW_LANGUAGE'				,'Show users language');
	define('LOCALE_SP_SFORM_SHOW_TRACER'				,'Add link to pages viewed by this user');
	define('LOCALE_SP_SFORM_SHOW_SOURCE'				,'Show Site referral if one exists');
	define('LOCALE_SP_SFORM_SHOW_COUNTRY'				,'Show users country based on IP address');
	define('LOCALE_SP_SFORM_FROM_FIELD_REQUIRED'		,'If the user supplies an email address use it as the sender of the email- enables reply to in your email software');
	define('LOCALE_SP_WAI_FORMS'					,'Use Accessible Web form');
	define('LOCALE_SP_BLANK_FIELD_ON_CLICK'			,'If you have accessible web forms enabled do you want auto blanking on selection of a element?');
}

?>