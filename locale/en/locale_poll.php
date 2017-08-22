<?php
/* 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Define the locales for this module
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

if (!defined("LOADED_POLL_LOCALE")){
 	if (!defined("LOCALE_VOTES")){
 		define ("LOCALE_VOTES"							, "Votes");
 	}
 	if (!defined("LOCALE_VIEW_RESULTS")){
 		define ("LOCALE_VIEW_RESULTS"					, "View the results of this poll.");
 	}
 	if (!defined("LOCALE_VOTE_NOW")){
 		define ("LOCALE_VOTE_NOW"						, "Vote");
 	}
 	define ("LOADED_POLL_LOCALE"						, 1);
 	define ("LOCALE_CHOOSE_ANSWER"						, "You have not selected an option");
 	define ("LOCALE_BACK_TO_VOTING"						, "Return to voting screen.");
 	define ("LOCALE_LOGIN_REQUIRED"						, "Requires <a href='index.php?command=USERS_SHOW_LOGIN'>login</a>");
 	define ("POLLS_CONTAINER"							, "Poll Groups");
}

?>