<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- $Revision: 1.5 $, $Date: 2005/01/31 08:16:43 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

if (!defined("LOADED_RSS_ADMIN_LOCALE")){
	define("LOADED_RSS_ADMIN_LOCALE"					, 1);
	define("MANAGEMENT_RSS"								, "RSS News Feeds");
	define("MANAGE_RSSFEEDS"							, "Manage Feeds");
	define("RSSADMIN_CONTAINER"							, "RSS Feeds");

	define("LOCALE_DISPLAY_RSS_FEED"					, "Display RSS Feed");
	define("LOCALE_RSS_FEED"							, "RSS Feed");
	define("LOCALE_RSS_OPT_OUT"							, "Can users opt-out of this feed");
	define("LOCALE_RSS_CHOOSE_FIELDS"					, "Please choose what fields are to be displayed if available");
	define("LOCALE_RSS_CHOOSE_FIELDS_MSG"				, "Below is a list of XML tags that you can choose to display as part of this feed, Notice that the title field of the stories is unavailble it is a required field");
	define("LOCALE_RSS_DISPLAY_OPTIONS"					, "Display Options");
	define("LOCALE_RSS_NUMBER_OF_ITEMS"					, "Return no more than the following number of stories");
	define("LOCALE_RSS_BULLET_LIST"						, "Add Bullet List to Rss Feed Results");
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY"				, "Retrieve this feed every");
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_0_VAL"	, 0);
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_0_TXT"	, "Live");
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_1_VAL"	, 1800);
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_1_TXT"	, "30 Minutes");
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_2_VAL"	, 3600);
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_2_TXT"	, "Hourly");
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_3_VAL"	, 21600);
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_3_TXT"	, "6 hours");
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_4_VAL"	, 43200);
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_4_TXT"	, "12 hours");
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_5_VAL"	, 86400);
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_5_TXT"	, "1 Day");
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_6_VAL"	, 604800);
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_6_TXT"	, "Weekly");
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_7_VAL"	, 2419200);
	define("LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_7_TXT"	, "Monthly");
	define("LOCALE_RSS_AVAILABLE_ON_DESKTOP"			, "Is this feed available on the Digital Desktop as well?");
	define("LOCALE_OVERRIDE_CHANNEL_LABEL"				, "Override the channel title with the RSS Label");
	define("MANAGE_RSSFEEDS_INTERNAL"					, "Internal RSS Feeds");
	define("MANAGE_RSSFEEDS_EXTERNAL"					, "External RSS Feeds");
	define("LOCALE_RSS_INTERNAL_TYPE"					, "What type of Rss feed do you want to produce?");
	define("LOCALE_RSS_INTERNAL_TYPE_1"					, "Latest pages");
	define("LOCALE_RSS_INTERNAL_TYPE_2"					, "Accumulitive pages");
	define("LOCALE_RSS_INTERNAL_TYPE_3"					, "Random pages");
	define("LOCALE_EXTRACT_ALL_LOCATIONS"				, "Extract from all locations");
	define("LOCALE_EXTRACT_SET_INHERITANCE"				, "Should the children of the following locations automatically inherit this setting");
	define("LOCALE_EXTRACT_WHAT_LOCATIONS"				, "Choose specific locations that should be extracted from");
	define("LOCALE_EXTRACTABLE"							, "Can external programs request this Feed");
	define("LOCALE_EXTRACTABLE_URL"						, "Extract URL");
	define("LOCALE_RSS_CHANNEL_IMAGE"					, "Channel image");
	define("LOCALE_RSSADMIN_CHOOSE_CHANNEL_IMAGE"		, "A channel image is an image that will be display with the RSS channel it has a file size restriction on the width of 144 pixels and on height of 400 pixels. When selecting the image to use we have filtered the results to only display the list of images that are within these boundaries.");
	define("LOCALE_OPEN_IN_NEW_WINDOW"					, "Open the links in a new window?");
}

?>