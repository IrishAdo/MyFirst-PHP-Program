<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- $Revision: 1.3 $, $Date: 2005/01/17 15:40:32 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

if (!defined("LOADED_POLLADMIN_LOCALE")){
	if (!defined("LOCALE_VOTES")){
		define ("LOCALE_VOTES"							, "Votes");
	}
	if (!defined("LOCALE_VIEW_RESULTS")){
		define ("LOCALE_VIEW_RESULTS"					, "View Results");
	}
	if (!defined("LOCALE_VOTE_NOW")){
		define ("LOCALE_VOTE_NOW"							, "Vote");
	}
	define ("LOADED_POLLADMIN_LOCALE"					, 1);
	define ("MANAGEMENT_POLL"						, "Poll Manager");
	define ("POLLSADMIN_CONTAINER"					, "Poll Groups");
	define ("LOCALE_POLL_MANAGER"					, "Poll Management");
	define ("LOCALE_POLL_TITLE_LABEL"				, "Poll information");
	define ("LOCALE_POLL_WHERE_ON_SITE_TO_PUBLISH"	, "Which areas of the site should poll be displayed?");
	define ("LOCALE_POLL_QUESTION"					, "Question");
	define ("LOCALE_POLL_ANSWER"					, "Answer ......");
	define ("LOCALE_POLL_REMOVE_CONFIRMATION_LABEL"	, "Are you sure you wish to permanently remove this poll?");
	define ("LOCALE_POLL_ANSWER_STATEMENT"			, "Please list possible voting options to your poll");
	define ("LOCALE_POLL_CHANNEL_OPEN"				, "Place a poll channel here.");
	define ("LOCALE_SORRY_POLL_MSG"					, "<p>The system administrator has not supplied locations to publish a poll to.</p><p>Please contact your System Administrator about creating a poll channel.</p>");
	define ("LOCALE_POLL_REMOVE_LABEL"				, "Remove Poll");
	define ("DISPLAY_POLL_CHANNEL_OPEN"				, "Allow a Poll to be displayed in this location");
	define ("LOCALE_MANAGE_POLL"					, "Manage Poll");
	define ("LOCALE_QUESTION_AND_ANSWERS"			, "Q &amp; A");
	define ("LOCALE_QUESTION"						, "Q.");
	define ("LOCALE_ANSWER"							, "A.");
	define ("LOCALE_RESULTS_AVAILABLE"				, "Are site visitors allowed to view results?");
	define ("LOCALE_RESULTS_AVAILABLE_AFTER"		, "If so, how many votes must be cast before results are available?");
	define ("LOCALE_VOTE_SECURITY_LABEL"			, "How do you wish to prevent visitor voting twice on same poll?");
	define ("LOCALE_RESULTS_DISPLAY_SETTING"		, "How should poll results be displayed?");
	define ("LOCALE_VOTE_SECURITY_OPTION_1"			, "Allow multiple voting");
	define ("LOCALE_VOTE_SECURITY_OPTION_2"			, "1 vote per site visit (show results)");
	define ("LOCALE_VOTE_SECURITY_OPTION_3"			, "1 vote per registed user (Requires Login - show results)");
	define ("LOCALE_VOTE_SECURITY_OPTION_2_A"		, "1 vote per site visit (return to poll screen)");
	define ("LOCALE_VOTE_SECURITY_OPTION_3_A"		, "1 vote per registed user (Requires Login - return to poll screen)");
	define ("LOCALE_DISPLAY_AS_BAR_GRAPH"			, "Bar graph");
	define ("LOCALE_DISPLAY_AS_PERCENTAGE"			, "Percentage");
	define ("LOCALE_DISPLAY_AS_NUM_VOTES"			, "Number of votes cast");
	define ("LOCALE_CONFIRM_MESSAGE"				, "Thank you message (255 characters max)");
	define ("LOCALE_ALL_READY_VOTED_MESSAGE"		, "You have already voted in this poll message (255 characters)");
	define ("LOCALE_RESULTS_SAME_PAGE"				, "Display results below poll answer");
	define ("LOCALE_LIST_OF_POLLS"					, "Poll List");
	define ("LOCALE_POLL_CHOOSE"					, "Choose Polls to display in group");
	define ("LOCALE_DISPLAY_SETTINGS_RANDOM"		, "Randomly choose different poll upon page load");
	define ("LOCALE_DISPLAY_SETTINGS_CYCLE"			, "Same poll throughout visit");
	define ("LOCALE_DISPLAY_SETTINGS_PER_SESSION"	, "Cycle to next poll in list upon page load");
	define ("LOCALE_POLLS_IN_GROUP"					, "Contains Polls");
	define ("LOCALE_DISPLAY_SETTINGS"				, "Display Settings");
	define ("LOCALE_GROUP_LIST"						, "Group List");
	define ("LOCALE_POLL_LIST_MSG_ALREADY_VOTED"	, "You have already voted on this poll");
	define ("LOCALE_POLL_LIST_MSG_THANKYOU"			, "Thank you for voting");
	define ("LOCALE_MANAGE_POLL_GROUPS"				, "Poll Groups");
	define ("LOCALE_MANAGE_POLL_GROUPS_OPTIONS"		, "What poll groups does this poll belong to.");
	define ("LOCALE_MANAGE_POLL_LIST"				, "Poll List");
	define ("LOCALE_MANAGE_POLL_LIST_OPTIONS"		, "What poll lists does this poll group contain.");
	define ("LOCALE_NO_USE_DIFFERENT_PAGE"			, "No, display on seperate page");
	define ("LOCALE_DISPLAY_RESULTS"				, "Display Results");
	define ("LOCALE_DISPLAY_OPTION"					, "Display Option");
	define ("LOCALE_VOTE_SECURITY"					, "Vote option");
}

?>