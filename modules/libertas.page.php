<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.page.php
* @date 03 Dec 2002
*/
/**
* This module is the module for managing pages on the web site.
*/
class page extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__ADMIN__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name_label			= "Page Manager Module (Administration)";
	var $module_name				= "page";
	var $module_admin				= "1";
	var $module_channels			= array("PRESENTATION_DISPLAY","PRESENTATION_LOCATION");
	var $searched					= 0;
	var $module_modify	 			= '$Date: 2005/02/09 12:06:50 $';
	var $module_version 			= '$Revision: 1.49 $';
	var $module_command				= "PAGE_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_PAGE";
	/**
	* SPECIAL PAGES
	*
	* Each special page will call a specific function as defined here
	*/
	var $specialPages			 	= array(
		array("-search.php"			,"PRESENTATION_SEARCH"	,"VISIBLE", "Search"),
		array("-whats-new.php"		,"PRESENTATION_LATEST"	,"VISIBLE", "What's New")
	);
	/**
	*  Management Reports that are available
	*/
	var $module_reports				= Array(
		array("PAGE_REPORT_LIST", LOCALE_PAGE_BASIC_REPORTS, LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL)
	);
	/**
	*  Management Menu entries
	*/
	var $module_admin_options 		= array();
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access	= array(
		array("PAGE_ALL", 			"COMPLETE_ACCESS",""),
		array("PAGE_AUTHOR", 		"ACCESS_LEVEL_AUTHOR"),
		array("PAGE_APPROVER", 		"ACCESS_LEVEL_APPROVER"),
		array("PAGE_PUBLISHER", 	"ACCESS_LEVEL_PUBLISHER"),
		array("PAGE_ARCHIVER", 		"ACCESS_LEVEL_ARCHIVEST"),
		array("PAGE_DISCUSSION", 	"ACCESS_LEVEL_DISCUSSION_ADMIN"),
		array("PAGE_FORCE_UNLOCK",	"ACCESS_LEVEL_FORCE_UNLOCK"),
		array("PAGE_GROUP_ADMIN",	"ACCESS_LEVEL_GROUP_PREMISSIONS"),
		array("PAGE_CLONER",		"ACCESS_LEVEL_CLONER")
	);
	
	
	/**
	*  Channel options
	*/
	var $module_display_options 	= array(
		array("PRESENTATION_SEARCH",	LOCALE_PAGE_DISPLAY_SEARCH_CHANNEL),
		array("PRESENTATION_DISPLAY",	LOCALE_PAGE_DISPLAY_AUTO_CHANNEL),
		array("PRESENTATION_LATEST",	LOCALE_PAGE_DISPLAY_NEWEST_CHANNEL)
	);
	/**
	*  filter options
	*/
	var $display_options			= array(
		array (0, FILTER_ORDER_DATE_NEWEST		,"trans_date_modified Desc"),
		array (1, FILTER_ORDER_DATE_OLDEST		,"trans_date_modified Asc"),
		array (2, FILTER_ORDER_TITLE_A_Z		,"trans_title Asc"),
		array (3, FILTER_ORDER_TITLE_Z_A		,"trans_title Desc")
	);
	/**
	* WebObject entries
	*
	* Each Array has (Type, Label, Command, All locations, Has label)
	*
	* Type: - 0 = User defined, 1 = Channel type Web object, 2 = XSL defined WEbObject)
	*
	* Channels extract information from the system wile XSl defined are functions in the 
	* XSL display. 
	*/
	var $WebObjects				 	= array(
		array(2,"Page options",			"WEBOBJECTS_SHOW_PAGEOPTIONS", 		0, 0, "Display all of the Page Option (printer friendly, Page Comments, Email A Friend"),
		array(2,"Printer Friendly Link","WEBOBJECTS_SHOW_PRINTERFRIENDLY",	0, 0, "Display the printer friendly icon and text link"),
		array(2,"Home page Link",	 	"WEBOBJECTS_SHOW_HOME",				0, 0, "Display a link to the Home page")//,
//		array(2,"Bookmark this page","WEBOBJECTS_SHOW_BOOKMARKPAGE", 0, 0)
	);
	/**
	*  access permissions
	*/
	var $author_access				= 0;
	var $approver_access			= 0;
	var $publisher_access			= 0;
	var $list_access				= 0;
	var $archiver_access			= 0;
	var $discussion_admin_access	= 0;
	var $force_unlock_access		= 0;
	var $approve_comments_access	= 0;
	var $clone_access				= 0;
	/**
	*  Class Methods
	*/
	function command($user_command, $parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call. 
		*/
		if (strpos($user_command,$this->module_command)===0){
			if ($user_command==$this->module_command."DEBUG_ON"){
				$this->module_debug=true;
			}
			if ($user_command==$this->module_command."DEBUG_OFF"){
				$this->module_debug=false;
			}
			if ($user_command==$this->module_command."GET_PREFS"){
				return $this->preferences;
			}
			if ($user_command==$this->module_command."GET_MODULE"){
				return $this->get_module_name();
			}
			if ($user_command==$this->module_command."GET_VERSION"){
				return $this->get_module_version();
			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->call_command("PRESENTATION_DISPLAY",$parameter_list);
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."SPECIAL_PAGES"){
				return $this->specialPages;
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			if (
				($this->parent->module_type=="admin") || 
				($this->parent->module_type=="view_comments") || 
				($this->parent->module_type=="preview") || 
				($this->parent->module_type=="files")
			   ){
				if ($user_command == $this->module_command."MENU_DISPLAY_OPTIONS"){
					return $this->display_channels($parameter_list);
				}
				if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
					return $this->module_admin_access_options(0);
				}
				if ($user_command==$this->module_command."ACCESS_OPTIONS"){
					return $this->module_admin_options(0);
				}
			}
			if ($this->module_admin_access==1){
				if ($user_command==$this->module_command."UPDATE_URLS"){
					return $this->update_url($parameter_list);
				}
				/*
				if ($user_command==$this->module_command."EMAIL_ALERTS"){
					return $this->email_alerts($parameter_list);
				}
				*/
				if ($user_command==$this->module_command."ADD_A2Z_ENTRIES"){
					return $this->add_A2Z_entries($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE_A2Z_ENTRIES"){
					return $this->remove_A2Z_entries($parameter_list);
				}
				if ($user_command==$this->module_command."DELETE_XML_FILES"){
					return $this->delete_xml_files($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE_PAGES_IN_LOCATION"){
					return $this->remove_pages_in_location($parameter_list);
				}
				if ($user_command==$this->module_command."MANAGE_IGNORE_LIST"){
					return $this->manage_keyword_ignore_list();
				}
				if ($user_command==$this->module_command."SAVE_IGNORE_LIST"){
					$this->save_keyword_ignore_list($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_LIST"));
				}
				if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
					if (($user_command==$this->module_command."DISCUSSION_ENABLE") || ($user_command==$this->module_command."DISCUSSION_DISABLE")){
						$this->discussion_available($user_command,$parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_LIST"));
					}
					if (($this->author_access==1) && ($this->approver_access==1) && ($user_command==$this->module_command."SAVE_AND_APPROVE")){
						$parameter_list["trans_doc_status"] = "__STATUS_APPROVED__";
						$trans_locs = trim($this->check_parameters($parameter_list,"trans_menu_locations"));
						$out = $this->save($parameter_list);
						if(!is_numeric($out)){
							return $out;
						} else {
							$trans_id=$out;
							if (is_array($trans_locs)){
								$folder = $trans_locs[0];
							} else {
								if (strlen($trans_locs)>0){
									if (strpos($trans_locs, ",")===0){
										$folder=$trans_locs;
									} else {
										$list = split("," ,$trans_locs);
										$folder = $list[0];
									}
								}
							}

							/* This was inserting duplicate entry in table page_trans_data (Comment By Muhammad Imran)*/
							/*
							if ($this->parent->server[LICENCE_TYPE]==ECMS){
								$id = $this->create_new_version($trans_id);
							} else {
								$id = $trans_id;
							}*/

							$id = $trans_id;
							
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_APPROVE_CONFIRM&amp;trans_doc_status=__STATUS_APPROVED__&amp;menu_location=$folder&amp;identifier=$id"));
	//						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_LIST&amp;menu_location=$folder"));
						}
					}
					if (($this->module_admin_access == 1) && ($user_command == $this->module_command."LOCK")){
						$this->lock_record("page_trans_data","trans",$this->check_parameters($parameter_list,"identifier"));
						$user_command = $this->module_command."EDIT";
					}
/*					if ($user_command == $this->module_command."MOVE_CONTENT"){
						$this->move_page_content($parameter_list);
					}*/
				}
				if ($user_command == $this->module_command."SUMMARY"){
					return $this->display_version_summary($parameter_list);
				}
				if ($user_command == $this->module_command."MY_WORKSPACE"){
					return $this->retrieve_my_docs($parameter_list);
				}
				if (($user_command == $this->module_command."REGENERATE_CACHE") || ($user_command == $this->module_command."RESTORE")){
					$this->regenerate_cache($parameter_list);
				}
				if (($user_command == $this->module_command."PREVIEW") || ($user_command==$this->module_command."PREVIEW_FORM")){
					return $this->display_preview($parameter_list);
				}
				if ($user_command == $this->module_command."SEND_TO_APPROVER"){
					$this->send_approver($parameter_list);
				}
				
				if (($this->author_access==1) && ($user_command==$this->module_command."SAVE")){
					$out = $this->save($parameter_list);
					if(!is_numeric($out)){
						return $out;
					} else {
						$trans_id=$out;
						$trans_locs = trim($this->check_parameters($parameter_list,"trans_menu_locations"));
						$folder=-1;
						if (is_array($trans_locs)){
							$folder = $trans_locs[0];
						} else {
							if (strlen($trans_locs)>0){
								if (strpos($trans_locs,",")===0){
									$folder=$trans_locs;
								} else {
									$list = split(",",$trans_locs);
									$folder = $list[0];
								}
							}
						}
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_LIST&amp;menu_location=$folder"));
					}
				}
				if (($this->author_access==1) && ($this->publisher_access==1) && ($user_command==$this->module_command."SAVE_AND_PUBLISH")){					
					$out = $this->save($parameter_list);
					if(!is_numeric($out)){
						return $out;
					} else {
						$trans_id=$out;
						$trans_locs = trim($this->check_parameters($parameter_list,"trans_menu_locations"));
						$arr_menu_location = split(",",$this->check_parameters($parameter_list,"trans_menu_location"));
						$mid = trim($arr_menu_location[0]);
						$folder=-1;
						if (is_array($trans_locs)){
							$folder = $trans_locs[0];
						} else {
							if (strlen($trans_locs)>0){
								if (strpos($trans_locs,",")===0){
									$folder=trim($trans_locs);
								} else {
									$list = split(",",trim($trans_locs));
									$folder = trim($list[0]);
								}
							}
						}
						// $_SESSION["cache_extra_ids"] .= "folder = $folder";
						
						/* This was inserting duplicate entry in table page_trans_data (Comment By Muhammad Imran)*/
						/*
						if ($this->parent->server[LICENCE_TYPE]==ECMS){ //||($this->parent->server[LICENCE_TYPE]==MECM)){
							$id = $this->create_new_version($trans_id);
						} else {
						*/
							$id = $trans_id;
							$sql = "
								select distinct ptd2.trans_identifier, ptd2.trans_published_version from page_trans_data as ptd1 
									inner join page_data on ptd1.trans_page = page_data.page_identifier 
									inner join page_trans_data as ptd2 on ptd2.trans_page = page_data.page_identifier 
								where 
									ptd2.trans_published_version = 1 and 
									ptd2.trans_client = $this->client_identifier and 
									ptd1.trans_client = $this->client_identifier and 
									ptd1.trans_identifier = $id
									and ptd2.trans_identifier != $id
							";
							$result = $this->call_command("DB_QUERY",Array($sql));
							$list	= "";
							if ($result){
								while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
									if($list != ""){
										$list .= ",";
									}
									$list .= $r["trans_identifier"];
								}
								$this->call_command("DB_FREE",Array($result));
								$sql = "update page_trans_data set trans_published_version=0 where trans_identifier in (".$list.");";
								$this->call_command("DB_QUERY",Array($sql));
							}
						//}by Imran
						if (is_array($trans_locs)){
						} else {
							$trans_locs = split(",",$trans_locs);
						}
						$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACTOR_UPDATE", 
							Array(
								"menu_list" => $trans_locs
							)
						);
						$this->call_command("ACCESSKEYADMIN_CACHE");
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_PUBLISH_CONFIRM&amp;trans_doc_status=__STATUS_PUBLISHED__&amp;menu_location=$folder&amp;identifier=$id&amp;cache_other=1&amp;mid=$mid"));
					}
				}
				
				if (($this->author_access==1) && ($user_command==$this->module_command."SEND_TO_PUBLISHER")){
					//$parameter_list["trans_doc_status"]="__STATUS_APPROVED__";
					$folder = $this->check_parameters($parameter_list,"menu_location",-1);
					$trans_id = $this->check_parameters($parameter_list,"identifier",-1);
					//print "command=PAGE_APPROVE_CONFIRM&amp;trans_doc_status=__STATUS_APPROVED__&amp;menu_location=$folder&amp;identifier=$trans_id";
					// $_SESSION["cache_extra_ids"] ="";
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_APPROVE_CONFIRM&amp;trans_doc_status=__STATUS_APPROVED__&amp;menu_location=$folder&amp;identifier=$trans_id"));
					//exit();
				}
				if (
						($this->module_admin_access == 1)
						 &&
						(
								($this->publisher_access	== 1)
								 || 
								($this->approver_access		== 1)
								 || 
								($this->author_access		== 1)
						)
						 &&
						(
							($user_command == $this->module_command."UNARCHIVE_CONFIRM")	||
							($user_command == $this->module_command."UNPUBLISH_CONFIRM")	||
							($user_command == $this->module_command."REWORK_CONFIRM")		||
							($user_command == $this->module_command."REJECT_CONFIRM") 	||
							($user_command == $this->module_command."REMOVE_CONFIRM")		||
							($user_command == $this->module_command."APPROVE_CONFIRM")	||
							($user_command == $this->module_command."PUBLISH_CONFIRM")
						)
					){
					if (!empty($trans_id)){
						$parameter_list["identifier"]=$trans_id;
					}
					$page_comment	= $this->check_parameters($parameter_list,"page_comment");
					$identifier		= $this->check_parameters($parameter_list,"identifier");
					$this->process_action($parameter_list);
					// email alerts
					if ($this->parent->server[LICENCE_TYPE] == ECMS){
						if (($this->check_parameters($parameter_list, "command") == "PAGE_REWORK_CONFIRM") || ($this->check_parameters($parameter_list, "command") == "PAGE_REJECT_CONFIRM")){
							$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_PAGE_AUTHOR__"], "identifier" => $identifier, "msg"=>$page_comment));
						}
						if ($this->check_parameters($parameter_list, "command") == "PAGE_PUBLISH_CONFIRM"){
							$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_PAGE__"], "identifier" => $identifier, "msg"=>$page_comment));
						}
						if ($this->check_parameters($parameter_list, "command") == "PAGE_APPROVE_CONFIRM"){
							$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_PAGE_PUBLISHER__"], "identifier" => $identifier, "msg"=>$page_comment));
						}
					}
					if ($user_command==$this->module_command."PUBLISH_CONFIRM"){
						if ($this->parent->server[LICENCE_TYPE]==MECM){
							// remove previous versions of this document when publishing new one
							$this->remove_previous_versions($parameter_list);
						}
						$prev_command = $user_command;
						$this->call_command("ACCESSKEYADMIN_CACHE");
						$user_command=$this->module_command."CACHE_PAGE";
					}else{
						if ($this->check_parameters($parameter_list,"next_command")!=""){
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->check_parameters($parameter_list,"next_command")."&amp;identifier=".$this->check_parameters($parameter_list,"id",-1)));
						} else {
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_LIST&amp;menu_location=".$this->check_parameters($parameter_list,"menu_location",-1)));
						}
					}
				}
				if ($user_command==$this->module_command."AUTHOR_ACCESS"){
					$user_command=$this->module_command."LIST";
				}
				if ($user_command==$this->module_command."APPROVER_ACCESS"){
					$user_command=$this->module_command."LIST";
				}
				if ($user_command==$this->module_command."PUBLISHER_ACCESS"){
					$user_command=$this->module_command."LIST";
				}

				if (($this->module_admin_access==1) && ($user_command==$this->module_command."UNLOCK")){
					$this->lock_record("page_trans_data","trans",$this->check_parameters($parameter_list,"identifier"),"UNLOCK");
					$id = $this->create_new_version($parameter_list["identifier"]);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_LIST"));
				}
				if (($this->module_admin_access==1) && ($user_command==$this->module_command."LIST")){
					if ($this->searched==0){
						$this->searched=1;
						return $this->page_list($parameter_list);
					}
				}
				if (($this->module_admin_access==1) && ($user_command==$this->module_command."LIST_ALL")){
					return $this->page_list_all($parameter_list);
				}
				if (($user_command==$this->module_command."LIST_DETAIL")){
					return $this->page_list_detail($parameter_list);
				}
				if (($this->author_access==1) && (($user_command==$this->module_command."EDIT_MENU")||($user_command==$this->module_command."ADD_MENU"))){
					$parameter_list["identifier"] = $this->get_page($parameter_list);
					return $this->page_form($parameter_list);
				}
				if (($this->author_access==1) && (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD") || ($user_command==$this->module_command."IMPORT"))){
					return $this->page_form($parameter_list);
				}
				if (($this->author_access==1) && ($user_command==$this->module_command."LIVE_EDIT")){
					return $this->page_form_editorial($parameter_list);
				}
				if (($this->author_access==1) && (($user_command==$this->module_command."REMOVE"))){
					return $this->confirm_screen(@$parameter_list["identifier"],$user_command);
				}
				if  (($this->approver_access==1) && (($user_command==$this->module_command."REJECT"))){
					return $this->confirm_screen(@$parameter_list["identifier"],$user_command);
				}
				if  (($this->approver_access==1) && (($user_command==$this->module_command."APPROVE"))){
					return $this->confirm_screen(@$parameter_list["identifier"],$user_command);
				}
				if  (($this->publisher_access==1) && (($user_command==$this->module_command."REWORK"))){
					return $this->confirm_screen(@$parameter_list["identifier"],$user_command);
				}
				if  (($this->publisher_access==1) && (($user_command==$this->module_command."UNPUBLISH"))){
					return $this->confirm_screen(@$parameter_list["identifier"],$user_command);
				}
				if  (($this->archiver_access==1) && (($user_command==$this->module_command."UNARCHIVE"))){
					return $this->confirm_screen(@$parameter_list["identifier"],$user_command);
				}
				if  (($this->publisher_access==1) && (($user_command==$this->module_command."PUBLISH"))){
					return $this->confirm_screen(@$parameter_list["identifier"],$user_command);
				}
				if ($user_command==$this->module_command."CACHE_PAGE"){
//					print_r($parameter_list);
					$id 			= $this->check_parameters($parameter_list, "identifier", $this->check_parameters($parameter_list,"trans_identifier",-1));
					$set_publish 	= $this->check_parameters($parameter_list, "set_publish", 1);
					$cache_other	= $this->check_parameters($parameter_list, "cache_other", 0);
					$mid	= $this->check_parameters($parameter_list, "mid", "");					
					$no_to_sublevel	= $this->check_parameters($parameter_list, "no_to_sublevel", "__NOT_FOUND__");
					
					$this->cache_this_page($id, $set_publish);
					// $_SESSION["cache_extra_ids"] .="<li>".print_r($parameter_list,true)."</li>";
					if ($no_to_sublevel=="__NOT_FOUND__"){
//						if ($cache_other==1){
							$parameter_list["dst"] = $id;														
							//if ($this->client_identifier != 17)
							$this->call_command("EMBED_CACHE_LINKS", $parameter_list);							
//						}

						/** Check if this page is published to location which generates internal  RSS feeds with zero freq. If so then recache RSS. */						
						if ($mid != ""){				
							$sql = "select mto_object from menu_to_object inner join rss_feed on rss_identifier=mto_object where mto_client = $this->client_identifier and mto_menu = $mid ";//and rss_external=0 and rss_frequency=0"					
							$rss_result  = $this->parent->db_pointer->database_query($sql);
							if ($this->call_command("DB_NUM_ROWS",array($rss_result)) > 0){
								$r = $this->parent->db_pointer->database_fetch_array($rss_result);
								$this->call_command("RSS_CACHE", Array("identifier"=>$r["mto_object"]));						
							}
							$this->parent->db_pointer->database_free_result($rss_result);
						}
						/**
						* redirect to the list so that refresh does not do action twice.
						*/
						if (1 == $this->check_parameters($parameter_list,"redirect",1)){
							$folder="";
							$trans_locs = trim($this->check_parameters($parameter_list,"trans_menu_locations",$this->check_parameters($parameter_list,"menu_location",-1)));
							if (is_array($trans_locs)){
								$folder = $trans_locs[0];
							}else{
								if (strlen($trans_locs)>0){
									if (strpos($trans_locs,",")===0){
										$folder=$trans_locs;
									} else {
										$list = split(",",$trans_locs);
										$folder = $list[0];
									}
								}
							}
							if ($prev_command == $this->module_command."PUBLISH_CONFIRM"){
								if (is_array($trans_locs)){
								} else {
									$trans_locs = split(",",$trans_locs);
								}
								$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACTOR_UPDATE", 
									Array(
										"menu_list" => $trans_locs
									)
								);
							}
							return $this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_LIST&amp;menu_location=$folder"));
						}
					}
				}
				if ($user_command==$this->module_command."COPY_VERSION"){
					$identifier = $this->page_copy($parameter_list);
//					if (($this->check_parameters($parameter_list,"next_command")!="PAGE_EDIT") && ($this->check_parameters($parameter_list,"next_command")!="PAGE_LOCK")){
//						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_VERSION_ARCHIVE_ACCESS&amp;successful=1"));
//					} else {
						if ($this->parent->server[LICENCE_TYPE]==MECM){
							// remove previous versions of this document when extracting should only be one entry before this next function call
							$this->remove_previous_versions($parameter_list);
						}
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_LOCK&identifier=$identifier"));
//					}
				}
				if ($user_command==$this->module_command."CLONE_VERSION"){
					$identifier = $this->page_clone($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_LOCK&identifier=$identifier"));
				}
				if ($user_command==$this->module_command."REPORT_LIST"){
					return $this->generate_reports("status");
				}
				if ($user_command==$this->module_command."REPORT_SITE_CONTENT"){
					return $this->generate_reports("site_content");
				}
				if ($user_command==$this->module_command."RETRIEVE_SEARCH_KEYWORDS"){
					return $this->retrieve_search_keywords($parameter_list);
				}
				if ($user_command==$this->module_command."RETURN_CHANNELS"){
					return $this->return_admin_channels();
				}
				if ($user_command==$this->module_command."VERSION_ARCHIVE_ACCESS"){
					return $this->extract_vesions($parameter_list);
				}
				if ($user_command==$this->module_command."LIST_VERSIONS"){
					return $this->extract_list_vesions($parameter_list);
				}
				if ($user_command==$this->module_command."UPDATE_REQUEST"){
					return $this->request_page_update($parameter_list);
				}
				if ($user_command==$this->module_command."SUBMIT_PAGE_UPDATE_REQUEST"){
					return $this->submit_page_update_request($parameter_list);
				}
				if ($user_command==$this->module_command."UPDATE_EMBED"){
					return $this->update_embed($parameter_list);
				}
				if ($user_command==$this->module_command."LATEST_LIST"){
					return $this->latest_pages_list($parameter_list);
				}
				if ($user_command==$this->module_command."LATEST_FORM"){
					return $this->latest_pages_form($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE_LIVE_EDIT"){
					return $this->remove_live_edit($parameter_list);
				}
			}
			if ($user_command==$this->module_command."LIST_FORM_FIELD_ACCESS"){
				return $this->list_available_fields();
			}
		}else{
			// wrong command sent to system
			return "";
		}
	}
	function list_available_fields(){
		$data = array();
		if (($this->parent->server[LICENCE_TYPE]==ECMS)){
			$data[count($data)] = Array("LOCALE_META_DEFAULT_FORM",
				Array(
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_audience', 'LOCALE_META_AUDIENCE', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_contributor', 'LOCALE_META_CONTRIBUTOR', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_creator', 'LOCALE_META_CREATOR', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_coverage_place', 'LOCALE_META_COVERAGE_PLACE', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_coverage_postcode', 'LOCALE_META_COVERAGE_POSTCODE', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_coverage_time', 'LOCALE_META_COVERAGE_TIME', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_doc_type', 'LOCALE_META_DOC_TYPES', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_rights', 'LOCALE_META_RIGHTS', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_rights_copyright', 'LOCALE_META_RIGHTS_LOC', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_source', 'LOCALE_META_SOURCE', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_subject_category', 'LOCALE_META_SUBJECT', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_subject_programme', 'LOCALE_META_SUBJECT_PROGRAMME', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_subject_project', 'LOCALE_META_SUBJECT_PROJECT', '0', '0');\";"
				)
			);
		}
		if (($this->parent->server[LICENCE_TYPE]!=ECMS)){
			$data[count($data)] = Array("LOCALE_PAGE_FORM",
				Array(
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'section_files', 'ENTRY_FILES_ASSOCIATED', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_summary', 'ENTRY_SUMMARY', '1', '1');\";"
				)
			);
		} else {
			$data[count($data)] = Array("LOCALE_PAGE_FORM",
				Array(
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_subject_keywords', 'LOCALE_SUBJECT_KEYWORDS', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_summary', 'ENTRY_SUMMARY', '1', '1');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'trans_dc_alt_title', 'LOCALE_META_ALT_TITLE', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'section_summary', 'LOCALE_SUMMARISATION', '1', '1');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'section_dates', 'LOCALE_DATES', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'section_groups','LOCALE_DEFAULT_GROUP_MSG', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'section_files', 'ENTRY_FILES_ASSOCIATED', '1', '0');\";"
				)
			);
		
		}
		return $data;
	}
	/**
	* Initialise function
	-----------------------
	- This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	- module::initialise() function allowing you to define any extra constructor
	- functionality.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* define the list of Editors in this module and define them as empty
		*/
		$this->editor_configurations = Array(
			"ENTRY_DESCRIPTION" => $this->generate_default_editor(),
			"ENTRY_SUMMARY"		=> $this->generate_default_editor()
		);

		/**
		* define some variables 
		*/
		$this->load_locale("page");
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		$this->can_log_search = false;
		$sql = "select page_status_constant, page_status_identifier from page_status";
		$result = $this->call_command("DB_QUERY",Array($sql));
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$this->module_constants[$r["page_status_constant"]]=$r["page_status_identifier"];
			}
		}
//		$group_access = $this->check_parameters($_SESSION,"SESSION_GROUP_ACCESS");
		
		$rnotes = $this->check_prefs(Array("sp_can_save_notes","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No"));
		if ($rnotes=="YES"){
			$this->record_notes			= 1;
		} else {
			$this->record_notes			= 0;
		}
		$this->page_group_access		= 0;
		$this->super_user_access		= 0;
		$this->author_access			= 0;
		$this->approver_access			= 0;
		$this->publisher_access			= 0;
		$this->list_access				= 0;
		$this->module_admin_access		= 0;
		$this->archiver_access 			= 0;
		$this->discussion_admin_access 	= 0;
		$this->force_unlock_access		= 0;
		$this->approve_comments_access	= 0;
		$this->clone_access				= 0;
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (
					("PAGE_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("PAGE_AUTHOR"==$access[$index]) ||
					("PAGE_AUTHOR_ACCESS"==$access[$index])
				){
					$this->author_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("PAGE_APPROVER"==$access[$index]) ||
					("PAGE_APPROVER_ACCESS"==$access[$index])
				){
					$this->approver_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("PAGE_PUBLISHER"==$access[$index])
				){
					$this->publisher_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("PAGE_ARCHIVER"==$access[$index])
				){
					$this->archiver_access=0;
				}
				if (
					("ALL"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("PAGE_DISCUSSION"==$access[$index]) ||
					("COMMENTSADMIN_ALL"==$access[$index]) ||
					("COMMENTSADMIN_APPROVER"==$access[$index])
				){
					$this->discussion_admin_access = 1;	
				}
				if (
					("ALL"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("PAGE_FORCE_UNLOCK"==$access[$index])
				){
					$this->force_unlock_access = 1;	
				}
				if (
					("ALL"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("PAGE_COMMENT_ADMIN"==$access[$index])
				){
					$this->approve_comments_access =1;
				}
				if (
					("ALL"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("PAGE_GROUP_ADMIN"==$access[$index])
				){
					$this->page_group_access =1;
				}
				if (
					("ALL"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("PAGE_CLONER"==$access[$index])
				){
					$this->clone_access = 1;
				}
				
			}
		}
		if (($this->publisher_access || $this->clone_access || $this->approver_access || $this->author_access || $this->archiver_access ) && (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files"))){
			$this->list_access=1;
			$this->module_admin_access=1;
		}
		
		$this->module_admin_options[count($this->module_admin_options)] = array("PAGE_ADD", "PAGE_ADDNEW","PAGE_AUTHOR|PAGE_PUBLISHER|PAGE_FORCE_UNLOCK");
		$this->module_admin_options[count($this->module_admin_options)] = array("PAGE_LIST", "PAGE_LIST","PAGE_AUTHOR|PAGE_APPROVER|PAGE_PUBLISHER|PAGE_ARCHIVER|PAGE_DISCUSSION|PAGE_FORCE_UNLOCK");
		
		if (($this->parent->server[LICENCE_TYPE]==ECMS)){
//			$this->module_admin_options[count($this->module_admin_options)] = array("PAGE_VERSION_ARCHIVE_ACCESS", "LOCALE_VERSION_ARCHIVE_ACCESS","PAGE_AUTHOR|PAGE_APPROVER|PAGE_PUBLISHER|PAGE_ARCHIVER");
			$this->module_admin_options[count($this->module_admin_options)] = array("PAGE_REPORT_LIST", "LOCALE_PAGE_REPORTS","PAGE_AUTHOR|PAGE_APPROVER|PAGE_PUBLISHER|PAGE_ARCHIVER","Reports/Page Management");
		}		
		return 1;
	}
	
	/**
	* function produces abstract structure of modules db structure
	*
    * used to generate the table structure required abstract function that passes an abstract
	* representation of the desired table structure to the proper database module which will
	* interpet the abstract and convert it into a valid SQL Create table structure.
	*
	* @return Array list of abstract table definitions for this module
	*/
	function create_table(){
		$tables = array();

		/**
		* Table structure for table 'pages'
		*/
		
		$fields = array(
			array("page_identifier"				,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("page_is_title_doc"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("page_client"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("page_overall_status"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("page_date_creation"			,"datetime"					,"NOT NULL"	,"default ''"),
			array("page_created_by_user"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("page_admin_discussion"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("page_web_discussion"			,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		
		$primary ="page_identifier";
		$tables[count($tables)] = array("page_data", $fields, $primary);

		/**
		* Table structure for the translations and version control of page content
		*/
		$fields = array(
		/**
		* Basic connectivity fields
		* the page it belongs to 
		* the client that owns this data
		* the language that the document is written in default 'english' if not supplied
		*/
		array("trans_identifier"			,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
		array("trans_page"					,"unsigned integer"	,"NOT NULL"	,"default '0'","key"),
		array("trans_client"				,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("trans_language"				,"varchar(10)"		,"NOT NULL"	,"default 'en'"),
		/**
		* editable content
		*/
		array("trans_title"					,"varchar(255)"		,"NOT NULL"	,"default ''"),
//		array("trans_summary"				,"text"				,"NOT NULL"	,"default ''"),
//		array("trans_body"					,"text"				,"NOT NULL"	,"default ''"),
		/**
		* is this document published to the site yet
		* the version numbers of this document Major . Minor
		* Major gets updated when fully approved,
		* Minor gets updated when sent for approval,
		* translation locked to one user when in edit mode.
		* trans_current_working_version on saving a new versionthis is set to 1 previous version 
		* set to 0
		*/
		array("trans_doc_status"				,"small integer"	,"NOT NULL"	,"default '1'","key"),
		array("trans_doc_version_major"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("trans_doc_version_minor"			,"unsigned integer"	,"NOT NULL"	,"default '1'"),
		array("trans_doc_lock_to_user"			,"unsigned integer"	,"NOT NULL"	,"default '0'","key"),
		array("trans_doc_author_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'","key"),
		array("trans_current_working_version"	,"small integer"	,"NOT NULL"	,"default '0'"),

		/**
		* Translation Date information
		-
		* creation date  :: is the creation date of that language version of the page and will not
		* 				    change through versions but will change for each new language used
		* modified date  :: is the date the translation was created.
		* review date    :: is the date that this document has to be reviewed.
		* publish date	 :: is the date that this version was published to the site.
		* remove date    :: is the date that this document should be automatically removed from the 
		-				   	site.
		* available date :: is the date that this document will be avaliable onthe site can be equal 
		*                   to or greater than the publish date 
		*/
		array("trans_date_creation"			,"datetime"			,""			,"default ''"),
		array("trans_date_modified"			,"datetime"			,""			,"default ''"),
		array("trans_date_review"			,"datetime"			,""			,"default ''"),
		array("trans_date_publish"			,"datetime"			,""			,"default ''"),
		array("trans_date_remove"			,"datetime"			,""			,"default ''"),
		array("trans_date_available"		,"datetime"			,""			,"default ''"),
		/**
		* dublin core meta data and extra generated by us
		-
		* best bets :: the ability to store search phrases for a document to help in search results
		* DC_*		:: dublin core metadata.
		*/
		array("trans_best_bets"				,"text"				,"NOT NULL"	,"default ''"),
		array("trans_dc_keywords"			,"text"				,"NOT NULL"	,"default ''"),
		array("trans_dc_alt_title"			,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_audience"			,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_contributor"		,"text"				,"NULL"		,"default ''"),
		array("trans_dc_creator"			,"text"				,"NULL"		,"default ''"),
		array("trans_dc_coverage_place"		,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_coverage_postcode"	,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_coverage_time"		,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_doc_type"			,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_publisher"			,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_rights"				,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_rights_copyright"	,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_source"				,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_subject_category"	,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_subject_keywords"	,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_subject_programme"	,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_subject_project"	,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_dc_url"				,"varchar(255)"		,"NULL"		,"default ''"),
		array("trans_published_version"		,"small integer"	,"NULL"		,"default '0'")
		);
		
		$primary ="trans_identifier";
		$tables[count($tables)] = array("page_trans_data", $fields, $primary);
		/**
		* Table structure for table 'page_status'
		*/
		$fields = array(
		array("page_status_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
		array("page_status_label"			,"varchar(25)"	,"NOT NULL"	,"default ''"),
		array("page_status_constant"		,"varchar(25)"	,"NOT NULL"	,"default ''")
		);
		
		/**
		* insert page_status data
		*/
		
		$data = array(
		'INSERT INTO page_status (page_status_label, page_status_constant) VALUES("LOCALE_AUTHORING", "__STATUS_AUTHOR__");',
		'INSERT INTO page_status (page_status_label, page_status_constant) VALUES("LOCALE_READY", "__STATUS_READY__");',
		'INSERT INTO page_status (page_status_label, page_status_constant) VALUES("LOCALE_UNPUBLISHED", "__STATUS_APPROVED__");',
		'INSERT INTO page_status (page_status_label, page_status_constant) VALUES("LOCALE_PUBLISHED", "__STATUS_PUBLISHED__");',
		'INSERT INTO page_status (page_status_label, page_status_constant) VALUES("LOCALE_ARCHIVED", "__STATUS_ARCHIVED__");',
		'INSERT INTO page_status (page_status_label, page_status_constant) VALUES("LOCALE_REMOVED", "__STATUS_REMOVED__");'
		);
		$primary ="page_status_identifier";
		
		$tables[count($tables)] = array("page_status", $fields, $primary, $data);

		/**
		* Table structure for table 'page_search_keys'
		*/
		
		$fields = array(
		array("search_keyword"			,"varchar(50)"		,"NOT NULL"	,"default ''"),
		array("search_counter"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("search_client"			,"unsigned integer"	,"NOT NULL"	,"default ''")
		);
		
		$primary ="";
		$tables[count($tables)] = array("page_search_keys", $fields, $primary);
		/**
		* Table structure for table 'page_latest'
		*/
		
		$fields = array(
			array("page_latest_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
			array("page_latest_label"		,"varchar(255)"		,"NOT NULL"	,"default '0'"),
			array("page_latest_type"		,"unsigned integer"	,"NOT NULL"	,"default ''"),
			array("page_latest_client"		,"unsigned integer"	,"NOT NULL"	,"default ''")
		);
		
		$primary ="";
		$tables[count($tables)] = array("page_latest", $fields, $primary);
		/**
		* Table structure for table 'page_latest_locations'
		*/
		
		$fields = array(
			array("page_latest_locations_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
			array("page_latest_locations_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("page_latest_locations_menu"			,"unsigned integer"	,"NOT NULL"	,"default ''")
		);
		
		$primary ="";
		$tables[count($tables)] = array("page_latest_locations", $fields, $primary);

		return $tables;
	}
	
	

	function page_list($parameters){
		if ($this->page_size==0){
			$this->page_size=10;
		}
		$searched_for	= "";
		$where 			= "";
		$join			= "";
		$order_by		= "";
		$status 		= array();
		$header_label 	= "";
		
		$restricted_access		= $this->check_parameters($_SESSION,	"SESSION_MANAGEMENT_ACCESS",	Array());
		$user_identifier		= $this->check_parameters($_SESSION,	"SESSION_USER_IDENTIFIER",	Array());
		$restricted_access_CSV	= " ".join(", ",$restricted_access).",";
		$access_type			= $this->check_parameters($_SESSION,	"access_type","AUTHOR_ACCESS");
		$status_filter 			= $this->check_parameters($parameters,	"status_filter",-2);
		$filter 				= $this->check_parameters($parameters,	"filter");
		$lang_of_choice 		= $this->check_parameters($_SESSION,	"SESSION_USER_LANGUAGE","en");
		$group_filter 			= $this->check_parameters($parameters,	"group_filter",-1);
		$menu_location 			= $this->check_parameters($parameters,	"menu_location",-1);
		$user_filter			= $this->check_parameters($parameters,	"user",-1);
		$order_filter 			= $this->check_parameters($parameters,	"order_filter",0);
		$page_boolean			= $this->check_parameters($parameters,	"page_boolean");
		$page_search			= $this->validate($this->check_parameters($parameters,	"page_search"));
		$join 					= "";
		$access_levels=0;		
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
		  if ($status_filter==-2){
			if ($this->author_access==1){
				$status_filter="1";
				$access_levels++;
			}
			if ($this->approver_access==1){
				$status_filter="2";
				$access_levels++;
			}
			if ($this->publisher_access==1){
				$status_filter="3";
				$access_levels++;
			}
			if ($this->archiver_access==1){
				$status_filter="5";
				$access_levels++;
			}
		  }
		}
		if (($status_filter==-2) || ($access_levels>1)){
			$status_filter="-1";
		}
		$parameters["status_filter"]= $status_filter;		
		$_SESSION["SESSION_USER_LANGUAGE"]="en";

		if ($this->module_admin_access==1){
			if($status_filter=="1" || $status_filter==-1){
				$status[count($status)] ="1";
			}
			if($status_filter==2 || $status_filter==-1){
				$status[count($status)] ="2";
			}
			if($status_filter==3 || $status_filter==-1){
				$status[count($status)] ="3";
			}
			if($status_filter==4 || $status_filter==-1){
				$status[count($status)] ="4";
			}
			if($status_filter==5 || $status_filter==-1){
				$status[count($status)] ="5";
			}
		}
		$c_status = count($status);
		if ($c_status>0){
			$cond="";
			for($index=0;$index<$c_status;$index++){
				if ($index>0){
					$cond .=" or ";
				}
				$cond .= "trans_doc_status = ".$status[$index];
			}
			$where = "and ($cond)";
		}
		if ($filter!=""){
			if ($filter=="review"){
				$now_csv = date("Y,m,d,H,i,s");
				$date_list = split(",",$now_csv);
				if ($date_list[1]==12){
					$date_list[1]=1;
					$date_list[0]++;
				} else {
					$date_list[1]++;
				}
				$nowdate = $date_list[0]."/".$date_list[1]."/".$date_list[2]." ".$date_list[3].":".$date_list[4].":".$date_list[5];
				$where = " and (trans_date_review <= '$nowdate' and trans_date_review != '0000/00/00 00:00:00') ";
			}
			if ($filter=="available"){
				$now = $this->libertasGetDate("Y/m/d H:i:s");
				$where = " and (trans_date_available > '$now') ";
			}
		}
		$search=0;
		$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd1",
				"field_as"			=> "trans_body1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "body",
				"join_type"			=> "inner"
			)
		);
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary",
				"join_type"			=> "inner"
			)
		);
				$where_title = "";
				$where_body = "";
				$where_summary = "";
				$where_first_name = "";
				$where_last_name = "";
		if ($page_search!=""){
			if ($menu_location != -2){
				$search=1;
				$words = split(" ",$page_search);
				if ($page_boolean=='exact'){
					$where_title .= " trans_title like '%".$page_search."%'";
					$where_body .= " ".$body_parts["where_field"]." like '%".$page_search."%'";
					$where_summary .= " ".$summary_parts["where_field"]." like '%".$page_search."%'";
					$where_first_name .= " contact_first_name like '%".$page_search."%'";
					$where_last_name .= " contact_last_name like '%".$page_search."%'";
				}else{
					for($index=0,$len=count($words);$index<$len;$index++){
						if ($index>0){
							$where_title .= " $page_boolean";
							$where_body .= " $page_boolean";
							$where_summary .= " $page_boolean";
							$where_first_name .= " $page_boolean";
							$where_last_name .= " $page_boolean";
						}
						$where_title .= " trans_title like '%".$words[$index]."%'";
						$where_body .= " ".$body_parts["where_field"]." like '%".$words[$index]."%'";
						$where_summary .= " ".$summary_parts["where_field"]." like '%".$words[$index]."%'";
						$where_first_name .= " contact_first_name like '%".$words[$index]."%'";
						$where_last_name .= " contact_last_name like '%".$words[$index]."%'";
					}
				}
				$where .= " and (($where_title) or ($where_body) or ($where_summary) or ($where_first_name) or ($where_last_name))";
				$header_label = "(Filtered)";
				$searched_for = " when searching for '".$page_search."'";
			}
		}

		if ($group_filter>0){
			if ($menu_location!=-2){
				$search=1;
				$where .= " and group_identifier = ".$group_filter."";
				$join .= " inner join group_access_to_page on group_access_to_page.trans_identifier = page_trans_data.trans_identifier ";
				$header_label = "(Filtered)";
			}
		}
		if ($menu_location>0){
			$search=1;
			$where .= " and menu_identifier = ".$menu_location."";
			$join .= " left outer join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier ";
			$header_label = "(Filtered)";
			$searched_for .= "[[return]]Filter location :: ".$this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL", Array("id" => $menu_location, "qstring" => "command=PAGE_LIST&amp;page=1&amp;search=1&amp;menu_location=" ) );
		}
		if ($menu_location == -3){
			$search=1;
			$where .= " and menu_identifier is null";
			$join .= " left outer join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier ";
			$header_label = "(Filtered)";
			$searched_for .= "[[return]]Filter location :: ".LOCALE_ORPHANED;
		}
		if ($user_filter!=-1 && $user_filter!=""){
			$where .= " and trans_doc_lock_to_user = $user_filter ";
		}
		
		if (empty($parameters["order_filter"])){
			$parameters["order_filter"] = 0;
		}
		$order_by .= "order by ".$this->display_options[$order_filter][2];
		$lang_of_choice = "en";
		if (empty($filter_translation)){
			$translation = $lang_of_choice;
		} else {
			$translation = $filter_translation;
		}
		$userRestricted=0;
		$session_management_access	= $this->check_parameters($_SESSION, "SESSION_MANAGEMENT_ACCESS", Array());
		$session_man_access="";
		for($index=0,$max=count($session_management_access);$index<$max;$index++){
			$userRestricted=1;
			if ($index>0){
				$session_man_access .= ",";
			}
			$session_man_access .= " ".$session_management_access[$index];
		}
		if ($this->check_parameters($parameters,"lock",0)==1){
			if ($menu_location==-1){
				$join .= " left outer join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier ";
			}
			if ($userRestricted){
				$where .= " and menu_access_to_page.menu_identifier in ($session_man_access)";
			}
		}
		$id	 = $this->check_parameters($parameters,"identifier",-1);
		if($id!=-1){
			$where .=" and trans_identifier = $id ";
		}
/*
					inner join page_trans_data on page_identifier = trans_page 
*/

		/*
		Added in SQL by Muhammad Imran
		(group by page_data.page_identifier)
		*/

		$sql = "
				select distinct
					page_data.*,
					page_trans_data.*,
					page_status.*, 
					user_info.*, 
					contact_data.contact_first_name, 
					contact_data.contact_last_name,";
		if ($where_body!=""){
//			$sql .= $body_parts["return_field"].",";
		}
			$sql .= $summary_parts["return_field"]."
				from page_data 
					inner join page_trans_data on page_overall_status=1 and trans_language='en' and trans_current_working_version=1 and page_client=trans_client and page_identifier = trans_page 
					inner join page_status on page_status_identifier = trans_doc_status 
					left outer join user_info on trans_doc_author_identifier=user_info.user_identifier 
					left outer join contact_data on contact_data.contact_user = user_info.user_identifier and contact_data.contact_client=$this->client_identifier
					$join ";
//		if ($where_body!=""){
			$sql .= $body_parts["join"];
//		}
			$sql .= $summary_parts["join"]."
				where 
					page_data.page_client=$this->client_identifier and 
					page_trans_data.trans_client=$this->client_identifier and 
					trans_language='$translation' and 
					trans_current_working_version = 1 and
					page_overall_status = 1
					";
//		if ($where_body!=""){
			$sql .= $body_parts["where"];
//		}
			$sql .= $summary_parts["where"]."
					$where 
				group by page_data.page_identifier 					
				$order_by
		";
		/*
		It was getting too many records than actual for paging (Comment By by Muhammad Imran)
		*/
/*
$counter_sql = "select distinct
count(*) as total
from page_data 
inner join page_trans_data on page_overall_status=1 and trans_language='$translation' and trans_current_working_version=1 and page_client=trans_client and page_identifier = trans_page 
inner join page_status on page_status_identifier = trans_doc_status 
left outer join user_info on user_client=trans_client and trans_doc_author_identifier=user_info.user_identifier 
left outer join contact_data on contact_data.contact_user = user_info.user_identifier 
					$join
					".$body_parts["join"]."
					".$summary_parts["join"]."
where 
page_overall_status=1 and page_data.page_client=page_trans_data.trans_client and page_data.page_identifier = page_trans_data.trans_page  and 
page_data.page_client=$this->client_identifier
					".$body_parts["where"]."
					".$summary_parts["where"]."
					$where 
order by trans_date_modified Desc";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$counter_sql]"));
		}
*/		
//		print $sql;
//		$this->exitprogram();
		$variables = Array();
		$variables["FILTER"]			= $this->filter($parameters,"PAGE_LIST")."<menus selected=\"$menu_location\"/>";
		$variables["MENU_LINKS"]		= "?command=PAGE_LIST&page=1&search=1&page_boolean=".$page_boolean."&page_search=".urlencode($page_search)."&order_filter=".$order_filter."&status_filter=-1&menu_location=";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		
		if ($this->module_admin_access==1 || $search==1){
			
			/*
			Starts Get number of records (Comment and added By by Muhammad Imran)
			*/
			
//			$counter_result = $this->call_command("DB_QUERY",array($counter_sql));
			$counter_result = $this->call_command("DB_QUERY",array($sql));

			if (!$counter_result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				$number_of_records=0;
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				$number_of_records = $this->call_command("DB_NUM_ROWS",array($counter_result));
				/*
                while($r = $this->call_command("DB_FETCH_ARRAY",Array($counter_result))){
					$number_of_records = $r["total"];
                }
				*/
				
				
              	$this->call_command("DB_FREE",Array($counter_result));
			/*
			Ends Get number of records (Comment and added By by Muhammad Imran)
			*/

				$page = $this->check_parameters($parameters,"page",1);
				$goto = ((--$page)*$this->page_size);
				if (($goto!=0)&&($number_of_records>$goto)){
//					$pointer = $this->call_command("DB_SEEK",array($result,$goto));
					$sql .= " limit $goto,$this->page_size ";
				} else {
					$sql .= " limit $this->page_size ";
				}
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
				}

				$result = $this->call_command("DB_QUERY",array($sql));
				if ($goto+$this->page_size>$number_of_records){
					$finish = $number_of_records;
				}else{
					$finish = $goto+$this->page_size;
				}
				$goto++;
				$page++;
				
				if ($number_of_records>0){
					$num_pages=floor($number_of_records / $this->page_size);
					$remainder = $number_of_records % $this->page_size;
					if ($remainder>0){
						$num_pages++;
					}
				}else{
					$num_pages=0;
					$remainder=0;
				}
				$counter=0;
				if($this->author_access==1 && $access_type=="AUTHOR_ACCESS" && $this->parent->module_type=="admin"){
					$variables["PAGE_BUTTONS"] = Array(Array("ADD","PAGE_ADD",ADD_NEW,"menu_location=$menu_location"));
				}
				$variables["HEADER"] = "Page Manager List " . $header_label ;
				$variables["SEARCHFILTER"] = $searched_for;
				$variables["NUMBER_OF_ROWS"]	= $number_of_records;
				$variables["START"]				= $goto;
				$variables["FINISH"]			= $finish;
				$variables["CURRENT_PAGE"]		= $page;
				$variables["NUMBER_OF_PAGES"]	= $num_pages;
				$start_page=intval($page / $this->page_size);
				$remainder = $page % $this->page_size;
				if ($remainder>0){
					$start_page++;
				}
				
				$variables["START_PAGE"]		= $start_page;
				
				if (($start_page+$this->page_size)>$num_pages){
					$end_page	 =	$num_pages;
				}else{
					$end_page	=	$this->page_size;
				}
				$user_identifier 		= $_SESSION["SESSION_USER_IDENTIFIER"];
				$variables["END_PAGE"]			= $end_page;
				$variables["ENTRY_BUTTONS"] =Array();
				$variables["CONDITION"]= array();
				$variables["RESULT_ENTRIES"] =Array();
				
				$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"LOCALE_PAGE_FORM"));
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
					$counter++;
					$shownedit=false;
					$i = count($variables["RESULT_ENTRIES"]);
					$entry = date("d<\s\u\p>S</\s\u\p> M, Y", strtotime($r["trans_date_modified"]));
					$trans_date_available = date("d<\s\u\p>S</\s\u\p> M, Y", strtotime($r["trans_date_available"]));
					$admin_discussion = $this->check_parameters($r,"page_admin_discussion",0);
					if ($admin_discussion==0){
						$admin_discussion =LOCALE_NO_DISCUSSION;
					}else{
						$admin_discussion =LOCALE_VIEW_DISCUSSION;
					}
					$web_discussion = 	$this->check_parameters($r,"page_web_discussion",0);
					if ($web_discussion==0){
						$web_discussion =LOCALE_NO_DISCUSSION;
					}else{
						$web_discussion =LOCALE_VIEW_DISCUSSION;
					}
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=> $r["trans_identifier"],
						"ENTRY_BUTTONS" => Array(
						),
						"attributes"	=> Array()
					);
					$loc_sql = "SELECT * FROM menu_data inner join menu_access_to_page on menu_access_to_page.menu_identifier = menu_data.menu_identifier where menu_access_to_page.trans_identifier=".$r["trans_identifier"]." and menu_client=$this->client_identifier ";
					$editable = 0;
					$menu_location_list="";
					$mid =0;
					$first_menu =0;
					if($loc_result = $this->call_command("DB_QUERY",array($loc_sql))) {
						while($row = $this->call_command("DB_FETCH_ARRAY",array($loc_result))){
							if ($mid==0){
								$first_menu = $row["menu_identifier"];
								$mid=1;
							}
							$menu_location_list			.= $this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL",Array("id"=>$row["menu_identifier"]))."<br/>";
							if (strpos(" ".$restricted_access_CSV.","," ".$row["menu_identifier"].",") === false){
							}else{
								$editable = 1;
							}
						}
						$this->call_command("DB_FREE",array($loc_result));
					}
					if (strlen($restricted_access_CSV)==2){
						$editable = 1;
					}
					if($menu_location_list==""){
						$menu_location_list=LOCALE_ORPHANED;
						$editable = 1;
					}
					$checkin=false;
					eval("\$str = LOCALE_STATUS_TYPE_".$r["trans_doc_status"].";");
					$contact = $this->check_parameters($r,"contact_first_name")." ".$this->check_parameters($r,"contact_last_name");
					if($contact ==" "){
						$contact = $r["user_login_name"];
					}
					if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
						if ($this->parent->server[LICENCE_TYPE]==ECMS){
							if ($r["trans_doc_lock_to_user"]>0){
								$locked =ENTRY_LOCKED;
							}else{
								$locked =ENTRY_UNLOCKED;
							}
							$variables["RESULT_ENTRIES"][$i]["attributes"]	=	Array(
								Array("PAGE_IDENTIFIER",$r["trans_page"]."","NO","NO"),
								Array("ENTRY_CHECK_OUT", "ENTRY_CHECK_OUT_IN","COMMAND","NO"),
								Array(ENTRY_TITLE, $r["trans_title"],"TITLE","EDIT_DOCUMENT"),
								Array(ENTRY_AUTHOR, $contact, "YES", "ENTRY_AUTHOR_LINK"),
								Array("ENTRY_AUTHOR_LINK", "?command=CONTACT_VIEW_USER&identifier=".$r["trans_doc_author_identifier"],"NO","NO"),
								Array(MAJOR_VERSION_MINOR_VERSION, $r["trans_doc_version_major"].".".$r["trans_doc_version_minor"],"YES","GET_VERSION"),
								Array("GET_VERSION", "?command=PAGE_LIST_VERSIONS&identifier=".$r["trans_page"],"NO","NO"),
								Array(ENTRY_DATE_MODIFIED, $entry,"YES","NO"),
								// required by filter.
								Array(LOCALE_DATE_AVAILABLE, $trans_date_available,"YES","NO"),
								Array("EDIT_DOCUMENT","?command=PAGE_EDIT&identifier=".$r["trans_identifier"]."","NO","NO"),
								/* Modify by Ali Imran ENTRY_STATUS to LOCALE_PAGE_BASIC_REPORTS*/
								Array(LOCALE_PAGE_BASIC_REPORTS,$str),
								Array("ENTRY_ADMIN_DISCUSSION_LINK","?command=NOTESADMIN_VIEW_LIST&identifier=".$r["trans_page"],	"NO","NO"),
								Array("ENTRY_WEB_DISCUSSION_LINK",	"?command=COMMENTSADMIN_VIEW_LIST&identifier=".$r["trans_page"],	"NO","NO"),
								Array(ENTRY_ADMIN_DISCUSSION,		$admin_discussion,	"YES", "ENTRY_ADMIN_DISCUSSION_LINK"),
								Array(ENTRY_WEB_DISCUSSION,			$web_discussion,	"YES", "ENTRY_WEB_DISCUSSION_LINK")
							);
							/* End modification by Ali Imran */
							if ($r["trans_doc_lock_to_user"]>0){
								$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(ENTRY_LOCKED,$locked,"YES","VIEW_USER");
								$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array("VIEW_USER","?command=CONTACT_VIEW_USER&identifier=".$r["trans_doc_lock_to_user"]."","NO","NO");
							} else {
								$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(ENTRY_LOCKED,$locked,"YES","NO");
							}
						}else{
							/* Modify by Ali Imran Ahmad ENTRY_SATUS to LOCALE_PAGE_BASIC_REPORTS*/
							$variables["RESULT_ENTRIES"][$i]["attributes"]	=	Array(
								Array(ENTRY_TITLE, $r["trans_title"],"TITLE","EDIT_DOCUMENT"),
								Array(ENTRY_AUTHOR, $contact,"YES","ENTRY_AUTHOR_LINK"),
								Array("ENTRY_AUTHOR_LINK", "?command=CONTACT_VIEW_USER&identifier=".$r["trans_doc_author_identifier"],"NO","NO"),
								Array(ENTRY_DATE_MODIFIED, $entry,"YES","NO"),
								Array("EDIT_DOCUMENT","?command=PAGE_EDIT&identifier=".$r["trans_identifier"]."","NO","NO"),
								Array(LOCALE_PAGE_BASIC_REPORTS,$str)
							);
							/* End Modification of Ali Imran */
						}
						if ($this->check_parameters($form_restriction_list,"trans_dc_alt_title","__NOT_FOUND__")!="__NOT_FOUND__"){
							if (strlen($r["trans_dc_alt_title"])>0){
								$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])]	=	Array("LOCALE_META_ALT_TITLE", $r["trans_dc_alt_title"],"SUMMARY","");
							}
						}
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]		=	Array("PREVIEW","PAGE_PREVIEW&amp;trans_menu_locations=$first_menu",ENTRY_PREVIEW,"admin/preview.php");
						if ($this->parent->server[LICENCE_TYPE]==ECMS){
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("SUMMARY","PAGE_SUMMARY",ENTRY_DOC_SUMMARY,"");
						}
						/**
						* A U T H O R   A C C E S S
						*/
						if ($editable==1){
							if ($this->author_access){
								if ($r["trans_doc_status"]==1){
									if ($this->parent->server[LICENCE_TYPE] == ECMS){
										if ($r["trans_doc_lock_to_user"]."" == "$user_identifier"){
											$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT","PAGE_EDIT",EDIT_EXISTING);
											$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE","PAGE_REMOVE_CONFIRM",REMOVE_EXISTING);
											$shownedit=true;
											if ($menu_location_list!=LOCALE_ORPHANED){
												$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("UNLOCK","PAGE_UNLOCK",LOCALE_CHECKIN);
											}
										} else {
											if ($menu_location_list==LOCALE_ORPHANED){
												$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT","PAGE_EDIT",EDIT_EXISTING);
												$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE","PAGE_REMOVE_CONFIRM",REMOVE_EXISTING);
												$shownedit=true;
											}
										}
										if ($menu_location_list!=LOCALE_ORPHANED){
											if ($r["trans_doc_lock_to_user"].''=='0'){
												if ($this->parent->server[LICENCE_TYPE]==ECMS){
													$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE","PAGE_REMOVE_CONFIRM",REMOVE_EXISTING);
													$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("NEXT_STAGE","PAGE_SEND_TO_APPROVER",SEND_TO_APPROVER);	
													$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("LOCK","PAGE_LOCK",LOCALE_CHECKOUT);
												} else {
													$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("NEXT_STAGE","PAGE_SEND_TO_PUBLISHER",SEND_TO_PUBLISHER);	
												}
											}
										}
									} else {
										$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT","PAGE_EDIT",EDIT_EXISTING);
										$shownedit=true;
										if ($menu_location_list!=LOCALE_ORPHANED){
											$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE","PAGE_REMOVE_CONFIRM",REMOVE_EXISTING);
											$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("NEXT_STAGE","PAGE_SEND_TO_PUBLISHER",SEND_TO_PUBLISHER);	
											if ($this->publisher_access){
												$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("PUBLISH","PAGE_PUBLISH_CONFIRM",LOCALE_SAVE_DATA_SITE);
											}
										}
									}
								}
								if ($r["trans_doc_status"]==4){
									if ($this->parent->server[LICENCE_TYPE]==MECM){
										$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT","PAGE_COPY_VERSION&amp;next_command=PAGE_LOCK",EDIT_EXISTING);
										$shownedit=true;
									}
								}
								if ($menu_location_list!=LOCALE_ORPHANED){
									if ($this->parent->server[LICENCE_TYPE]==ECMS){
										if (($r["trans_doc_status"]==4) && (($this->publisher_access==1) || ($this->author_access==1))){
											$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("COPY","PAGE_COPY_VERSION&amp;next_command=PAGE_LOCK",EDIT_EXISTING);
										}
										if (($this->force_unlock_access==1) && ($r["trans_doc_lock_to_user"].''!='0') && ($checkin==false)){
											$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("UNLOCK","PAGE_UNLOCK",LOCALE_FORCE_UNLOCK);
										}
									}
								}
							}
						/**
						* A P P R O V E R   A C C E S S
						*/
							if ($this->parent->server[LICENCE_TYPE]==ECMS){
								if($this->approver_access==1 ){
									if ($r["trans_doc_status"]==2){
										$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("APPROVE","PAGE_APPROVE",ENTRY_APPROVE);
										$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REJECT","PAGE_REJECT",ENTRY_REJECT);
									}
								}
							}
						/**
						* P U B L I S H E R   A C C E S S
						*/
							if($this->publisher_access==1){
								if ($menu_location_list==LOCALE_ORPHANED){
									if($shownedit==false){
										$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT","PAGE_EDIT",EDIT_EXISTING);
										$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE","PAGE_REMOVE_CONFIRM",REMOVE_EXISTING);
									}
								} else {
									if ($r["trans_doc_status"]==3){
										$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("PUBLISH","PAGE_PUBLISH",ENTRY_PUBLISH);
										if ($this->parent->server[LICENCE_TYPE]==ECMS){
											$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REWORK","PAGE_REWORK",ENTRY_REWORK);
										} else {
											$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REWORK","PAGE_REWORK_CONFIRM&amp;trans_doc_status=__STATUS_AUTHOR__",ENTRY_REWORK);
										}
									}
									if ($r["trans_doc_status"]==4){
										if ($this->parent->server[LICENCE_TYPE]==ECMS){
											$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("UNPUBLISH","PAGE_UNPUBLISH",ENTRY_UNPUBLISH);
										} else {
											$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("UNPUBLISH","PAGE_UNPUBLISH_CONFIRM&amp;trans_doc_status=__STATUS_AUTHOR__",ENTRY_UNPUBLISH);
										}
									}
								}
							}
						}
					} else{
						/* Modify by Ali Imran Ahmad ENTRY_SATUS to LOCALE_PAGE_BASIC_REPORTS*/
						$variables["RESULT_ENTRIES"][$i]["attributes"]	=	Array(
							Array(ENTRY_TITLE, $r["trans_title"],"TITLE","EDIT_DOCUMENT"),
							Array(ENTRY_DATE_MODIFIED, $entry,"YES","NO"),
//							Array(ENTRY_PAGE_WEIGHT, $this->get_file_size(strlen($r["trans_title"].$r["trans_summary1"].$this->check_parameters($r,"trans_body1"))),"YES","NO"),
							Array("EDIT_DOCUMENT","?command=PAGE_EDIT&identifier=".$r["trans_identifier"]."","NO","NO"),
							Array(LOCALE_PAGE_BASIC_REPORTS,$str)
						);
						/* End Modification by Ali Imran*/
						if ($editable==1){
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT","PAGE_EDIT",EDIT_EXISTING);
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE","PAGE_REMOVE_CONFIRM",REMOVE_EXISTING);
							if ($r["trans_doc_status"]!=4){
								$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("PUBLISH","PAGE_PUBLISH_CONFIRM",ENTRY_PUBLISH);
							}
							if ($r["trans_doc_status"]==4){
								$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("UNPUBLISH","PAGE_UNPUBLISH_CONFIRM",ENTRY_UNPUBLISH);
							}
						}
					}
					$summary = strip_tags(html_entity_decode($this->check_parameters($r,"trans_summary1","")));
					if (($this->check_parameters($form_restriction_list,"trans_summary","__NOT_FOUND__")!="__NOT_FOUND__") && (strlen($summary)>0)){
						if (strlen($summary)>255){
							$str = $this->return_start($summary,255)."...";
						} else {
							$str = $summary;
						}
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])]	=	Array(ENTRY_SUMMARY, $str, "SUMMARY","");
					}
					$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])]	=	Array(ENTRY_MENU_LOCATION,"$menu_location_list","SUMMARY","");
					if ($menu_location_list!=LOCALE_ORPHANED){
						if ($this->clone_access==1 && $this->parent->server[LICENCE_TYPE]==ECMS){
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("COPY","PAGE_CLONE_VERSION&amp;next_command=PAGE_LOCK",LOCALE_CLONE_ENTRY);
						}
					}
				}
			}
		}
		$out = $this->generate_list($variables).$this->call_command("LAYOUT_WEB_MENU");
		return $out;
	}
	
	function filter($parameters,$type){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_filter",__LINE__,"[]"));
		}
		$advanced 		= $this->check_parameters($parameters,"advanced",0);
		$page_author	= $this->check_parameters($parameters,"page_author","");
		$page_boolean	= $this->check_parameters($parameters,"page_boolean","or");
		$search 		= $this->check_parameters($parameters,"search",0);
		$page_search 	= $this->XSSbasicClean($this->check_parameters($parameters,"page_search"));
		$user		 	= $this->check_parameters($parameters,"user");
		$group_filter 	= $this->check_parameters($parameters,"group_filter",-1);
		$menu_location 	= $this->check_parameters($parameters,"menu_location",-1);
		$order_filter 	= $this->check_parameters($parameters,"order_filter",-1);
		$status_filter 	= $this->check_parameters($parameters,"status_filter",-1);
		$search++;
		$group_list 	= $this->call_command("GROUP_RETRIEVE",array(@$parameters["group_filter"]));
		$menu 			= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_location));
		
		$out  = "\t\t\t\t<form name=\"filter_form\" label=\"".FILTER_RESULTS."\" method=\"get\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"user\"><![CDATA[$user]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\"><![CDATA[$type]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"advanced\"><![CDATA[$advanced]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"associated_list\" value=\"".$this->check_parameters($parameters,"associated_list")."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" ><![CDATA[1]]></input>\n";
		$str = join("&#39;",split("\\\'",htmlspecialchars($page_search)));
		$page_search = join("&#34;",split("\\\&quot;",$str));
//		$page_search = $this->convert_amps($page_search);
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"search\" ><![CDATA[$search]]></input>\n";
		$out .= "\t\t\t\t\t<input size='15' length='255' type=\"text\" name=\"page_search\" label=\"".SEARCH_KEYWORDS."\" ><![CDATA[$page_search]]></input>\n";
		if ($advanced==1){
			$out .="\t\t\t\t\t<select label=\"".ENTRY_MENU_LOCATION."\" name=\"menu_location\"><option value=\"-1\">".OPTION_DISPLAY_ALL."</option>$menu</select>";
		} else {
/*			if ($menu_location==-2){
				$out .="\t\t\t\t\t<input type=\"hidden\" name=\"menu_location\" value='-2'/>";
			}*/
				$out .="\t\t\t\t\t<input type=\"hidden\" name=\"menu_location\" value='$menu_location'/>";
		}
		if ($this->module_admin_access==1){
			$out .= "\t\t\t\t\t<select name=\"order_filter\" label=\"".ENTRY_ORDER_FILTER."\">\n";
			for ($index=0,$max=count($this->display_options);$index<$max;$index++){
				$out .="\t\t\t\t\t\t<option value=\"".$this->display_options[$index][0]."\"";
				if ($order_filter==$this->display_options[$index][0]){
					$out .=" selected=\"true\"";
				}
				$out .=">".$this->display_options[$index][1]."</option>\n";
			}
			$out .= "\t\t\t\t\t</select>\n";
				$out .= "\t\t\t\t\t<select name=\"status_filter\" label=\"".ENTRY_STATUS_FILTER."\"><option value=\"-1\">".OPTION_DISPLAY_ALL."</option>\n";
				$out .= $this->get_status($status_filter);
				$out .= "\t\t\t\t\t</select>\n";
		}
		if ($advanced==1){
//			$out .= "\t\t\t\t\t<input type=\"text\" name=\"page_author\" label=\"".LOCALE_SEARCH_AUTHOR."\" ><![CDATA[$page_author]]></input>\n";
			$out .= "\t\t\t\t\t<select name=\"page_boolean\" label=\"".LOCALE_SEARCH_BOOLEAN."\">";
			if ($page_boolean=='or'){
				$or_selected ="selected='true'";
				$and_selected ="";
				$exact_selected="";
			}else if ($page_boolean=='and'){
				$or_selected ="";
				$and_selected ="selected='true'";
				$exact_selected="";
			}else{
				$or_selected ="";
				$and_selected ="";
				$exact_selected="selected='true'";
			}
			$out .= "<option value='or' $or_selected>Any keyword</option>";
			$out .= "<option value='and' $and_selected>All keywords</option>";
			$out .= "<option value='exact' $exact_selected>Exact Phrase</option>";
			$out .= "</select>\n";
		
		} else {
			$out .= "\t\t\t\t\t<input type=\"hidden\" name='page_boolean' value='exact' />";
		}
		if (($this->parent->server[LICENCE_TYPE]==ECMS) && false){
			if ($this->module_admin_access==0){
				if ($advanced==0){
					$out .= "\t\t\t\t\t<input type=\"button\" iconify=\"ADVANCED\" name=\"switch_to_advanced\" command=\"PAGE_SEARCH&amp;advanced=1\"/>\n";
				}else{
					$out .= "\t\t\t\t\t<input type=\"button\" iconify=\"BASIC\" name=\"switch_to_normal\" command=\"PAGE_SEARCH&amp;advanced=0\"/>\n";
				}
			}
		}
		$out .= "\t\t\t\t\t<input type=\"submit\" iconify=\"SEARCH\" name=\"\" value=\"".FILTER_RESULTS."\"/>\n";
		$out .= "\t\t\t\t</form>";
		return $out;
	}
	
	function remove_dud($str){
		return str_replace(Array("\'", "\"", "<p>&#65533;</p>"),Array("","",""),$str);
	}
	
	/**
	* Page_form()
	-
	- This function will produce the form for editting a page.
	*/
	function page_form($parameters){
		$debug = $this->debugit(false,$parameters);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_form",__LINE__,""));
		}
		$identifier					=	$this->check_parameters($parameters, "identifier","-1");
		$menu_parent				=	$this->check_parameters($parameters, "folder",$this->check_parameters($parameters,"menu_location","-1"));
		$display_tab				=	$this->check_parameters($parameters, "display_tab", "content");
		$live_edit					=	$this->check_parameters($parameters, "live_edit",0);
		$keys						=	"";
		if ($debug) print $identifier;
		/**
		* over write default settings for editors with user defined settings				-
		*/
		$this->load_editors();

		if ($identifier==""){
			$identifier=-1;
		}
		/**
		* Lock this record so that only I can edit it.
		*/
		$out  = "<module name=\"".$this->module_name."\" display=\"form\">";
		$prev_menu_location					= -1;
		$page_admin_notes					= 0;
		$page_web_notes						= 0;
		$documentation_summary				= "";
		$file_associations_identifiers		= "";
		$can_edit_ok 						= 1;
		$group_value						= -1;
		$current_status						= -1;
		$page_id							= -1;
		$contact_identifier					= -1;
		$minor_version						= 1;
		$page_title							= "";
		$page_alt_title						= "";
		$summary_value						= "";
		$content_value						= "";
		$group_value						= "";
		$group_identifiers					= "";
		$group_list							= "";
		$keywords_value						= "";
		$best_bets_value					= "";
		$menu_locations						= $this->check_parameters($parameters, "menu_identifier", "-0");
		$menu_location_identifiers			= "";
		$menu_location_list					= "";
		$file_identifiers					= "";
		$file_list							= "";
		$trans_date_available				= "";
		$trans_date_review					= "";
		$trans_date_remove					= "";
		$current_status						= 0;
		$trans_dc_subject_keywords 			= "";
		$trans_published_version			= 0;
		$trans_dc_subject_keywords			= "";
		$trans_summary						= "";
		$trans_doc_author_identifier		= "";
		$trans_current_working_version		= "";
		$trans_date_review					= "";
		$trans_date_remove					= "";
		$trans_date_available				= "";
		$trans_dc_audience					= "";
		$trans_dc_contributor				= "";
		$trans_dc_creator					= "";
		$trans_dc_coverage_place			= "";
		$trans_dc_coverage_postcode			= "";
		$trans_dc_coverage_time				= "";
		$trans_dc_doc_type					= "";
		$trans_dc_publisher					= "";
		$trans_dc_rights					= "";
		$trans_dc_rights_copyright			= "";
		$trans_dc_source					= "";
		$trans_dc_subject_category			= "";
		$trans_dc_subject_keywords			= "";
		$trans_dc_subject_programme			= "";
		$trans_dc_subject_project			= "";
		$metadata_contributor_associations	= "";
		$metadata_creator_associations		= "";
		$smd_publisher_contact_information	= "";
		$trans_page							= -1;
		$file_module_exists 				= $this->call_command("ENGINE_HAS_MODULE",array("FILES_"));
		$file_associations					= "";
		$file_associations_identifiers		= "";
		/*
			check the access rights of the user before allowing the information to be editted.
		*/
		$sql = "select * from system_metadata_defaults where smd_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,$sql));
		}
		$smd_result = $this->call_command("DB_QUERY",array($sql));
		while($r = $this->call_command("DB_FETCH_ARRAY",array($smd_result))){
			$smd_audience						= $r["smd_audience"];
			$smd_subject						= $r["smd_subject"];
			$smd_doctypes						= $r["smd_doctypes"];
			$smd_copy_location					= $r["smd_copy_location"];
			$smd_publisher_contact_information	= $r["smd_publisher_contact_information"];
		}

		$doc_summary = Array();
		if ($debug) print "module ".$this->parent->server[LICENCE_TYPE]." ".ECMS." ".MECM;
		if ($can_edit_ok==1){
			if ($this->check_parameters($parameters,"command")=="SAVE_ERROR"){
				//print_r($parameters);
				$identifier 							= $this->check_parameters($parameters,"trans_identifier","");
				$trans_page 							= $this->check_parameters($parameters,"trans_page","");
				$trans_language 						= $this->check_parameters($parameters,"trans_language","");
				$trans_doc_author_identifier 			= $this->check_parameters($parameters,"trans_doc_author_identifier","");
				$trans_current_working_version 			= $this->check_parameters($parameters,"trans_current_working_version","");
				$trans_date_review					 	= $this->check_parameters($parameters,"metadata_date_revalidate_date_year") . "-" . $this->check_parameters($parameters, "metadata_date_revalidate_date_month") . "-" . $this->check_parameters($parameters, "metadata_date_revalidate_date_day") . " " . $this->check_parameters($parameters, "metadata_date_revalidate_date_hour").":00:00";
				$trans_date_available				 	= $this->check_parameters($parameters,"metadata_date_available_date_year") . "-" . $this->check_parameters($parameters, "metadata_date_available_date_month") . "-" . $this->check_parameters($parameters, "metadata_date_available_date_day") . " " . $this->check_parameters($parameters, "metadata_date_available_date_hour").":00:00";
				$trans_date_remove					 	= $this->check_parameters($parameters,"metadata_date_remove_date_year") . "-" . $this->check_parameters($parameters, "metadata_date_remove_date_month") . "-" . $this->check_parameters($parameters, "metadata_date_remove_date_day") . " " . $this->check_parameters($parameters, "metadata_date_remove_date_hour").":00:00";
				$trans_dc_audience 						= $this->check_parameters($parameters,"trans_dc_audience","");
				$trans_dc_contributor 					= $this->check_parameters($parameters,"trans_dc_contributor","");
				$trans_dc_creator 						= $this->check_parameters($parameters,"trans_dc_creator","");
				$trans_dc_coverage_place 				= $this->check_parameters($parameters,"trans_dc_coverage_place","");
				$trans_dc_coverage_postcode 			= $this->check_parameters($parameters,"trans_dc_coverage_postcode","");
				$trans_dc_coverage_time 				= $this->check_parameters($parameters,"trans_dc_coverage_time","");
				$trans_dc_doc_type 						= $this->check_parameters($parameters,"trans_dc_doc_type","");
				$trans_dc_publisher 					= $this->check_parameters($parameters,"trans_dc_publisher","");
				$trans_dc_rights 						= $this->check_parameters($parameters,"trans_dc_rights","");
				$trans_dc_rights_copyright 				= $this->check_parameters($parameters,"trans_dc_rights_copyright","");
				$trans_dc_source 						= $this->check_parameters($parameters,"trans_dc_source","");
				$trans_dc_subject_category 				= $this->check_parameters($parameters,"trans_dc_subject_category","");
				$trans_dc_subject_programme 			= $this->check_parameters($parameters,"trans_dc_subject_programme","");
				$trans_dc_subject_project				= $this->check_parameters($parameters,"trans_dc_subject_project","");
				$minor_version							= $this->check_parameters($parameters,"trans_version_minor");
				$keys 									= $this->check_parameters($parameters,"trans_keywords","");
				$page_title								= $this->check_parameters($parameters,"trans_title");
				$page_alt_title							= $this->check_parameters($parameters,"trans_dc_alt_title");
				$page_id								= $this->check_parameters($parameters,"page_identifier");
				$content_value							= $this->check_parameters($parameters,"trans_body");
				$trans_summary							= $this->check_parameters($parameters,"trans_summary");
				$menu_location_identifiers				= $this->check_parameters($parameters,"trans_menu_locations");
				$menu_data_array						= split(",",$this->check_parameters($parameters,"trans_menu_location"));
				$file_ids								= $this->check_parameters($parameters,"id");
				$trans_groups							= $this->check_parameters($parameters,"trans_groups");
				$menu_parent 							= $menu_data_array[0];
				if ($identifier==-1){
						$command="PAGE_ADD";
						$label="Add a new Page";
				} else {
					$command="PAGE_EDIT";
					$label=EDIT_EXISTING;
				}
				$ml_ids = split(",",$menu_location_identifiers);
				$mx = count($ml_ids);
				$menu_location_list="";
				for($i=0;$i<$mx;$i++){
					$val = trim($ml_ids[$i]);
					if($val!=""){
						$menu_location_list			.= "<option value='".$val."'><![CDATA[".$this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL",Array("id"=>trim($val)))."]]></option>";
					}
				}
				$prev_menu_location = $menu_location_identifiers;
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					$sql = "SELECT * FROM group_data where group_identifier in ($trans_groups -1) and group_client=$this->client_identifier";
					if($result = $this->call_command("DB_QUERY",array($sql))) {
						while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
							$group_identifiers	.= " ".$r["group_identifier"].",";
							$group_list			.= "<li>".$r["group_label"]."</li>";
						}
						$this->call_command("DB_FREE",array($result));
					}
				}

				if ($file_module_exists){
					if(is_array($file_ids)){
						$file_list = join(",",$file_ids);
						$sql = "SELECT * FROM file_info where file_client=$this->client_identifier and file_identifier in ($file_list)";
							if($result = $this->call_command("DB_QUERY",array($sql))) {
							while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
								$file_associations_identifiers .="".$r["file_identifier"].",";
								$file_associations  .="<file_info logo='/libertas_images/icons/mime-images/".$this->call_command("FILES_GET_MIME_IMAGE",Array($r["file_name"])).".gif' identifier='".$r["file_identifier"]."' rank='".$this->check_parameters($r,"file_rank")."'><![CDATA[".$r["file_label"]."]]></file_info>";
							}
						}
					}
				}
			} else {
				if ($identifier==-1){
					$trans_identifier=-1;
					if ($this->parent->server[LICENCE_TYPE]==ECMS){
						$doc_summary["version"] ="0.1";
					}
					if ($this->check_parameters($parameters,"command")=="PAGE_IMPORT"){
						$page_title	= $this->check_parameters($parameters,"page_title");
						$content_value	= $this->check_parameters($parameters,"page_body");
						$command="PAGE_ADD";
						$label=LOCALE_ADD;
					} else {
						$command="PAGE_ADD";
						$label="Add a new Page";
					}
					$menu_location = $this->check_parameters($parameters,"menu_location","");
					if ($menu_location!=""){
						/*
							if this is an add then check for a filtered menu location on the page list screen and auto select that location
							as the publish to location.
						*/
						$sql = "SELECT * FROM menu_data where menu_identifier=$menu_location and menu_client=$this->client_identifier ";
						if($result = $this->call_command("DB_QUERY",array($sql))) {
							while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
								$menu_location_identifiers	.= " ".$r["menu_identifier"].",";
								$menu_location_list			.= "<li>".$this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL",Array("id"=>$r["menu_identifier"]))."</li>";
							}
							$this->call_command("DB_FREE",array($result));
						}
					}
					$keys ="";
				} else {
					$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
							"table_as"			=> "ptd1",
							"field_as"			=> "trans_body1",
							"identifier_field"	=> "trans_identifier",
							"module_command"	=> "PAGE_",
							"client_field"		=> "trans_client",
							"mi_field"			=> "body"
						)
					);
					$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
							"table_as"			=> "ptd2",
							"field_as"			=> "trans_summary1",
							"identifier_field"	=> "trans_identifier",
							"module_command"	=> "PAGE_",
							"client_field"		=> "trans_client",
							"mi_field"			=> "summary"
						)
					);
					$sql = "SELECT page_data.*, page_trans_data.*, contact_data.*, 
					".$body_parts["return_field"].", ".$summary_parts["return_field"]."
					 FROM page_data inner join page_trans_data on page_identifier = trans_page 
								left outer join contact_data on contact_user = trans_doc_author_identifier 
								".$body_parts["join"]." ".$summary_parts["join"]."
								where trans_language='en' and 
								page_trans_data.trans_identifier = $identifier and 
								trans_client=$this->client_identifier ".$body_parts["where"]." ".$summary_parts["where"]."";
					if ($debug) print "<p>$sql</p>";
					$can_edit_ok=0;
					$manage = "<h1>Links in Description</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "body"));
					$manage .= "<h1>Links in Summary</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "summary"));
					if($result = $this->call_command("DB_QUERY",array($sql))) {
						if ($num = $this->call_command("DB_NUM_ROWS",Array($result))>0){
							$can_edit_ok 							= 1;
							$r 										= $this->call_command("DB_FETCH_ARRAY",array($result));
							$trans_identifier 						= $this->check_parameters($r,"trans_identifier","");
							$trans_page 							= $this->check_parameters($r,"trans_page","");
							$trans_language 						= $this->check_parameters($r,"trans_language","");
							$trans_doc_author_identifier 			= $this->remove_dud($this->check_parameters($r,"trans_doc_author_identifier",""));
							$trans_current_working_version 			= $this->remove_dud($this->check_parameters($r,"trans_current_working_version",""));
							$trans_date_review 						= $this->remove_dud($this->check_parameters($r,"trans_date_review",""));
							$trans_date_remove 						= $this->remove_dud($this->check_parameters($r,"trans_date_remove",""));
							$trans_date_available 					= $this->remove_dud($this->check_parameters($r,"trans_date_available",""));
							$trans_dc_audience 						= $this->remove_dud($this->check_parameters($r,"trans_dc_audience",""));
							$trans_dc_contributor 					= $this->remove_dud($this->check_parameters($r,"trans_dc_contributor",""));
							$trans_dc_creator 						= $this->remove_dud($this->check_parameters($r,"trans_dc_creator",""));
							$trans_dc_coverage_place 				= $this->remove_dud($this->check_parameters($r,"trans_dc_coverage_place",""));
							$trans_dc_coverage_postcode 			= $this->remove_dud($this->check_parameters($r,"trans_dc_coverage_postcode",""));
							$trans_dc_coverage_time 				= $this->remove_dud($this->check_parameters($r,"trans_dc_coverage_time",""));
							$trans_dc_doc_type 						= $this->remove_dud($this->check_parameters($r,"trans_dc_doc_type",""));
							$trans_dc_publisher 					= $this->remove_dud($this->check_parameters($r,"trans_dc_publisher",""));
							$trans_dc_rights 						= $this->remove_dud($this->check_parameters($r,"trans_dc_rights",""));
							$trans_dc_rights_copyright 				= $this->remove_dud($this->check_parameters($r,"trans_dc_rights_copyright",""));
							$trans_dc_source 						= $this->remove_dud($this->check_parameters($r,"trans_dc_source",""));
							$trans_dc_subject_category 				= $this->remove_dud($this->check_parameters($r,"trans_dc_subject_category",""));
							$trans_dc_subject_programme 			= $this->remove_dud($this->check_parameters($r,"trans_dc_subject_programme",""));
							$trans_dc_subject_project				= $this->remove_dud($this->check_parameters($r,"trans_dc_subject_project",""));
							$keys 									= $this->check_parameters($r,"trans_dc_keywords","");
							$page_title								= join("&#34;",split('"',html_entity_decode(html_entity_decode($r["trans_title"]))));//stripslashes($this->split_me($this->split_me($r["trans_title"],"&#39;","'"),"&quot;",'"'));
							$page_alt_title							= stripslashes($this->split_me($r["trans_dc_alt_title"],"&#39;","'"));
							$page_id								= $r["trans_page"];
							$content_value							= $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$r["trans_body1"]));
							$trans_summary							= $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$r["trans_summary1"]));
							if ($this->parent->server[LICENCE_TYPE]==ECMS){
								$minor_version							= $r["trans_doc_version_minor"]+1;
								$doc_summary["LOCALE_VERSION_NUMBER"] 	= $r["trans_doc_version_major"].".".$r["trans_doc_version_minor"];
							}
							$doc_summary["LOCALE_AUTHOR"]	 		= $this->check_parameters($r,"contact_first_name").", ".$this->check_parameters($r,"contact_last_name");
							$modified 								= split(" ",$r["trans_date_modified"]);
							$publish 								= split(" ",$r["trans_date_publish"]);
							$trans_published_version				= $r["trans_published_version"];
							$trans_date_review						= $r["trans_date_review"];
							$trans_date_remove						= $r["trans_date_remove"];
							$trans_date_available					= $r["trans_date_available"];
							$page_web_notes							= $r["page_web_discussion"];
							$page_admin_notes						= $r["page_admin_discussion"];
							$trans_dc_subject_keywords 				= $this->check_parameters($r,"trans_dc_subject_keywords","");
							$doc_summary["LOCALE_DATE"] 			= Array("LOCALE_DATE_MODIFIED"	=> $modified[0]);
							if ($publish[0]!='0000/00/00'){
								$doc_summary["LOCALE_DATE"]["LOCALE_DATE_PUBLISHED"] = $publish[0];
							}
							if (isset($r["page_keywords"])){
								$keywords_value	= $r["trans_dc_keywords"];
							}
		//					$best_bets_value	= join(split(",",$r["trans_best_bets"]),"\n");
						}
		
						$this->call_command("DB_FREE",array($result));
					}
					if ($can_edit_ok==1){
						/*
							ON EDIT RETRIEVE ALL LOCATIONS THIS IS PUBLISHED TO.
						*/
						$sql = "SELECT distinct menu_data.* FROM menu_access_to_page inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier where trans_identifier=$identifier and client_identifier=$this->client_identifier";
						//print $sql;
						if($result = $this->call_command("DB_QUERY",array($sql))) {
							while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
								$menu_location_identifiers	.= " ".$r["menu_identifier"].",";
								$menu_location_list			.= "<option value='".$r["menu_identifier"]."'><![CDATA[".$this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL",Array("id"=>$r["menu_identifier"]))."]]></option>";
								$menu_parent				= $r["menu_identifier"];
							}
							$this->call_command("DB_FREE",array($result));
							$prev_menu_location = $menu_location_identifiers;
						}
						if ($this->parent->server[LICENCE_TYPE]==ECMS){
							$sql = "SELECT group_data.* FROM group_access_to_page inner join group_data on group_data.group_identifier = group_access_to_page.group_identifier where trans_identifier=$identifier and client_identifier=$this->client_identifier";
							if($result = $this->call_command("DB_QUERY",array($sql))) {
								while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
									$group_identifiers	.= " ".$r["group_identifier"].",";
									$group_list			.= "<li>".$r["group_label"]."</li>";
								}
								$this->call_command("DB_FREE",array($result));
							}
						}
					}
					$command="PAGE_EDIT";
					$label=EDIT_EXISTING;
					if ($file_module_exists){
//						$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("FILES","FILES_ASSOCIATE_FILES","ENTRY_FILES_ASSOCIATE"));
						if ($identifier!=""){
							$sql = "SELECT * FROM file_access_to_page inner join file_info on file_access_to_page.file_identifier=file_info.file_identifier where file_access_to_page.trans_identifier = $identifier and client_identifier=$this->client_identifier order by file_rank";
							if($result = $this->call_command("DB_QUERY",array($sql))) {
								while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
									$file_associations_identifiers .="".$r["file_identifier"].",";
									$file_associations  .="<file_info logo='/libertas_images/icons/mime-images/".$this->call_command("FILES_GET_MIME_IMAGE",Array($r["file_name"])).".gif' identifier='".$r["file_identifier"]."' rank='".$this->check_parameters($r,"file_rank")."'><![CDATA[".$r["file_label"]."]]></file_info>";
								}
							}
							//$file_associations = $this->call_command("FILES_RETRIEVE_PAGE_ASSOCIATION",Array($identifier));
						}
					}
				}
			}
		}
		// can we still edit this record
		if ($can_edit_ok==1){
			$groups = $this->call_command("GROUP_RETRIEVE",array($group_value));
//			$menu 	= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_location));
			$out .= "<page_options>";
			$out .= "<header><![CDATA[$label]]></header>";
				if ($live_edit==1 || $this->check_parameters($parameters, "command")=="PAGE_LOCK"){
					$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL", "PAGE_REMOVE_LIVE_EDIT&amp;identifier=$identifier&amp;trans_page=$trans_page", LOCALE_CANCEL));
				} else {
					$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL", "PAGE_LIST", LOCALE_CANCEL));
				}
//				$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("PREVIEW","PAGE_PREVIEW",ENTRY_PREVIEW));
			$out .= "</page_options>";			
			
//							$file_associations  .="Array('/libertas_images/icons/mime-images/".$this->call_command("FILES_GET_MIME_IMAGE",Array($r["file_name"])).".gif','".$r["file_label"]."',,'".$r["file_identifier"]."','".$r["file_rank"]."')";
			$file_associations ="
					<file_list report='normal'>
						<option><cmd><![CDATA[FILES_LIST]]></cmd><label><![CDATA[".LOCALE_SELECT_FILE."]]></label></option>
						<option><cmd><![CDATA[FILES_ADD]]></cmd><label><![CDATA[".LOCALE_UPLOAD_FILE."]]></label></option>
						$file_associations
					</file_list>
			";

//			<file_list>$file_associations</file_list>";
			foreach($doc_summary as $key => $value){
				if (is_array($value)){
					foreach($value as $refinement => $val){
						if ($val!="0000-00-00"){
							$documentation_summary .= "<key label=\"".$this->get_constant($key)."\" refinement=\"".$this->get_constant($refinement)."\"><![CDATA[$val]]></key>";
						} else {
							$documentation_summary .= "<key label=\"".$this->get_constant($key)."\" refinement=\"".$this->get_constant($refinement)."\"><![CDATA[".LOCALE_UNDEFINED."]]></key>";
						}
					}
				}else{
					$refinement="";
					$documentation_summary .= "<key label=\"".$this->get_constant($key)."\" refinement=\"".$this->get_constant($refinement)."\"><![CDATA[$value]]></key>";
				}
			}
			
			
			$comment_list="";
			$note_list="";
			$language_list="<key><![CDATA[English]]></key>";
			$keys	= "<keywords>$keys</keywords>";
			$phrase = Array("","","","","","","","","","","","","","","");
			$p = split("; ",$trans_dc_subject_keywords);
			for ($i=0;$i<count($p);$i++){
				$phrase[$i] = $p[$i];
			}
			$phrases ="
			<phrases>
				<phrase id='phrase1'><![CDATA[".$phrase[0]."]]></phrase>
				<phrase id='phrase2'><![CDATA[".$phrase[1]."]]></phrase>
				<phrase id='phrase3'><![CDATA[".$phrase[2]."]]></phrase>
				<phrase id='phrase4'><![CDATA[".$phrase[3]."]]></phrase>
				<phrase id='phrase5'><![CDATA[".$phrase[4]."]]></phrase>
				<phrase id='phrase6'><![CDATA[".$phrase[5]."]]></phrase>
				<phrase id='phrase7'><![CDATA[".$phrase[6]."]]></phrase>
				<phrase id='phrase8'><![CDATA[".$phrase[7]."]]></phrase>
				<phrase id='phrase9'><![CDATA[".$phrase[8]."]]></phrase>
				<phrase id='phrase10'><![CDATA[".$phrase[9]."]]></phrase>
				<phrase id='phrase11'><![CDATA[".$phrase[10]."]]></phrase>
				<phrase id='phrase12'><![CDATA[".$phrase[11]."]]></phrase>
				<phrase id='phrase13'><![CDATA[".$phrase[12]."]]></phrase>
				<phrase id='phrase14'><![CDATA[".$phrase[13]."]]></phrase>
				<phrase id='phrase15'><![CDATA[".$phrase[14]."]]></phrase>
			</phrases>
			";
			$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"LOCALE_PAGE_FORM"));
			$out .= "<form name=\"user_form\" method=\"post\" label=\"$label\">";
			$out .= "	<input type=\"hidden\" name=\"trans_identifier\"><![CDATA[$identifier]]></input>";
			$out .= "	<input type=\"hidden\" name=\"page_identifier\"><![CDATA[$page_id]]></input>";
			$out .= "	<input type=\"hidden\" name=\"prev_command\"><![CDATA[$command]]></input>";
			$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[PAGE_SAVE]]></input>";
			$out .= "	<input type=\"hidden\" name=\"trans_keywords\"><![CDATA[$keywords_value]]></input>";
			$out .= "	<input type=\"hidden\" name=\"trans_version_minor\"><![CDATA[$minor_version]]></input>";
			$out .= "	<input type=\"hidden\" name=\"trans_doc_status\"><![CDATA[__NO_CHANGE__]]></input>";
			$out .= "	<input type=\"hidden\" name=\"file_associations\"><![CDATA[$file_associations_identifiers]]></input>";
			$out .= "	<input type=\"hidden\" name=\"trans_groups\"><![CDATA[$group_identifiers]]></input>";
			$out .= "	<input type=\"hidden\" name=\"publisher_info\"><![CDATA[$smd_publisher_contact_information]]></input>";
			$out .= "	<input type=\"hidden\" name=\"previous_page_title\"><![CDATA[$page_title]]></input>";
			$out .= "	<input type=\"hidden\" name=\"prev_trans_menu_locations\"><![CDATA[$prev_menu_location]]></input>";
			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				$out .= "	<input type=\"hidden\" name=\"trans_menu_locations\" hidden_label='".LOCALE_DEFAULT_MENU_MSG."'><![CDATA[$menu_location_identifiers]]></input>";
			}
			$out .= "<page_sections>
			";
			$out .= "	<section label=\"".LOCALE_PAGE_CONTENT."\"";
			if ($display_tab=="content"){
				$out .= " selected='true'";
			}
			$out .= ">";
			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				$data =  $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_parent,"use_useraccess_restrictions"=>"YES","add_parent"=>"YES"));
				$out .= "	<selection name=\"trans_menu_location\" required=\"YES\">
								<label><![CDATA[".LOCALE_DEFAULT_MENU_MSG."]]></label>";
				/*
					command=\"LAYOUT_RETRIEVE_LIST_MENU_OPTIONS\" 
					link='trans_menu_locations' 
					return_command='LAYOUT_RETRIEVE_LIST_MENU_OPTIONS' 
				";
				*/
				$out .= "	<data>$data</data>";
				$out .= "	<clist>$menu_location_list</clist></selection>";
			}else{ //
				if ($this->parent->server[LICENCE_TYPE]==MECM){
					$data =  $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_parent,"use_useraccess_restrictions"=>"YES"));
				} else {
					$data =  $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_parent));
				}
				$out .= "	<select label=\"".LOCALE_DEFAULT_MENU_MSG."\" name=\"trans_menu_locations\" special=\"page_add_new_menu\" required=\"YES\">$data</select>";
			}
			if ($this->check_parameters($parameters,"command")=="SAVE_ERROR"){
			$out .= "	<text class='error'><![CDATA[".LOCALE_TITLE_EXISTS."]]></text>";
			}
			$page_title=		str_replace(Array("&quot;"),Array("&amp;&amp;quot;"),$page_title);
//			$this->exitprogram();
			$out .= "	<input type=\"text\" label=\"".ENTRY_TITLE."\" size=\"255\" name=\"trans_title\" required=\"YES\"><![CDATA[$page_title]]></input>";
			if ($this->check_parameters($form_restriction_list,"trans_dc_alt_title","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "	<input type=\"text\" label=\"".LOCALE_META_ALT_TITLE."\" size=\"255\" name='trans_dc_alt_title'";
				if ($this->check_parameters($form_restriction_list["trans_dc_alt_title"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$page_alt_title]]></input>";
			}
			$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_DESCRIPTION",Array());
			$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
			$locked_to  = $this->check_parameters($this_editor,"locked_to","");
			$out .= "	<textarea required=\"YES\" label=\"".LOCALE_PAGE_CONTENT."\" size=\"40\" height=\"18\" name=\"trans_body\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$content_value]]></textarea>
					</section>
			";
			$file_label ="test file";
			$file_id	= "1";
			$page_summary="";
			$type = "RICH-TEXT";
			$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_SUMMARY",Array());
			$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
			$locked_to  = $this->check_parameters($this_editor,"locked_to","");
			$page_summary .= "	<textarea label=\"".ENTRY_SUMMARY."\" size=\"100\" height=\"18\" name=\"trans_summary\" type=\"$type\" config_type='$config_status_of_editor' locked_to='$locked_to' ";
			$page_summary .= " required=\"YES\"";
			$page_summary .= "><![CDATA[$trans_summary]]></textarea>";
			$page_summary .= "<text><![CDATA[".LOCALE_SELECT_KEYS."]]></text>";
			$page_summary .= "$keys";
			$page_summary .= "<text><![CDATA[".LOCALE_SUBJECT_KEYWORDS."]]></text>";
			$page_summary .= "$phrases";
			if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
				$page_summary.="<summary_file label='Image for summary' filter='image'>";
				$sql = "select * from file_to_object 
				inner join file_info on fto_file = file_identifier and file_client = fto_client
				where fto_object=$identifier and fto_client=$this->client_identifier and fto_module='PAGE_SUMMARY'";
//				print $sql;
    	        $result  = $this->call_command("DB_QUERY",Array($sql));
	            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){ 
   					$page_summary.="<file>
						<label><![CDATA[".$r["file_label"]."]]></label>
						<id><![CDATA[".$r["file_identifier"]."]]></id>
						<md5><![CDATA[".$r["file_md5_tag"]."]]></md5>
						<path><![CDATA[".$this->call_command("LAYOUT_GET_DIRECTORY_PATH", array($r["file_directory"]))."]]></path>
						<extension><![CDATA[".$this->file_extension($r["file_name"])."]]></extension>
					</file>";
        	    }
    	        $this->call_command("DB_FREE",Array($result));
				$page_summary.="</summary_file>";
			}
			$out .= "		<section label=\"".LOCALE_PAGE_SUMMARY."\" name=\"page_summary\" onclick='LIBERTAS_generate_summary' ><parameters><field>trans_body</field><field>trans_summary</field></parameters>
			
			$page_summary
			</section>";
			if (($this->parent->server[LICENCE_TYPE]==ECMS)){
				if ($this->check_parameters($form_restriction_list,"section_groups","__NOT_FOUND__")!="__NOT_FOUND__"){
					$out .= "		<section label=\"".LOCALE_DEFAULT_GROUP_MSG."\" name=\"trans_group_information\" command=\"GROUP_SELECT\" link='trans_groups' return_command='GROUP_SELECTED'";
					if ($display_tab=="groups"){
						$out .= " selected='true'";
					}
					if ($this->page_group_access==0){
						// if role access to define group restrictions for pages
						$out .= " hidden='true'";
					}
					$out .= "><![CDATA[$group_list]]></section>";
				}
			}
			if ($this->check_parameters($form_restriction_list,"section_files","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "		<section label=\"".ENTRY_FILES_ASSOCIATED."\" name=\"trans_file_associations\" command=\"FILES_LIST\" link='file_associations' return_command='FILES_LIST_FILE_DETAIL'";
					if ($display_tab=="files"){
						$out .= " selected='true'";
					}
					$out .= ">$file_associations</section>";
			}
			if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
				if ($this->check_parameters($form_restriction_list,"section_languages","__NOT_FOUND__")!="__NOT_FOUND__"){
					/*
						$out .= "		<section label=\"LOCALE_LANGUAGES\" name=\"languages\" command=\"LANGUAGES_SELECT\"";
						if ($display_tab=="language"){
							$out .= " selected='true'";
						}
						$out .= ">$language_list</section>";
					*/
				}
			}
			if (($this->parent->server[LICENCE_TYPE]==ECMS)){
				$meta_form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"LOCALE_META_DEFAULT_FORM"));
				if (($this->check_parameters($form_restriction_list,"section_dates","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($meta_form_restriction_list,"trans_dc_source","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($meta_form_restriction_list,"trans_dc_rights","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($meta_form_restriction_list,"trans_dc_rights_copyright","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($meta_form_restriction_list,"trans_dc_audience","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($meta_form_restriction_list,"trans_dc_doc_type","__NOT_FOUND__")!="__NOT_FOUND__") || (($this->check_parameters($meta_form_restriction_list,"trans_dc_subject_category","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($form_restriction_list,"trans_dc_subject_programme","__NOT_FOUND__")!="__NOT_FOUND__") ||($this->check_parameters($form_restriction_list,"trans_dc_subject_PROJECT","__NOT_FOUND__")!="__NOT_FOUND__")) || ($this->check_parameters($meta_form_restriction_list,"trans_dc_subject_programme","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($meta_form_restriction_list,"trans_dc_contributor","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($meta_form_restriction_list,"trans_dc_creator","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($meta_form_restriction_list,"trans_dc_coverage_place","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($meta_form_restriction_list,"trans_dc_coverage_postcode","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($meta_form_restriction_list,"trans_dc_coverage_time","__NOT_FOUND__")!="__NOT_FOUND__")){

					$out.= "<section label=\"".LOCALE_META_DEFAULT_FORM."\"";
					if ($display_tab=="metadata"){
						$out .= " selected='true'";
					}
					$out .= ">";
					$out.= "<text><![CDATA[<b>Please fill in the appropriate metadata fields</b>]]></text>";
					if ($this->check_parameters($form_restriction_list,"section_dates","__NOT_FOUND__")!="__NOT_FOUND__"){
						$year_start = $this->check_prefs(Array("sp_combo_year"));
						$year_finish = Date("Y")+5;
						if ($trans_date_available==""){
							$date_to_use = "";
						}else{
							$date_to_use = $trans_date_available;
						}
						$out.= "<input type=\"date_time\" label=\"".LOCALE_DATE_AVAILABLE_FROM."\" name=\"metadata_date_available\" value=\"$date_to_use\" size=\"255\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
						$out.= "<input type=\"date_time\" label=\"".LOCALE_DATE_VALID_UNTIL."\" name=\"metadata_date_revalidate\" value=\"$trans_date_review\" size=\"255\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
						$out.= "<input type=\"date_time\" label=\"".LOCALE_DATE_REMOVE_FROM."\" name=\"metadata_date_remove\" value=\"$trans_date_remove\" size=\"255\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
					}
					$pos = strpos($trans_dc_audience, "|");
					if ($pos === false) { // note: three equal signs
					   // not found...
					   $trans_dc_audience = array($trans_dc_audience);
					} else{
						$trans_dc_audience = split("\|",$trans_dc_audience);
					}
					$pos = strpos($trans_dc_subject_category, "|");
					if ($pos === false) { // note: three equal signs
					   // not found...
					   $trans_dc_subject_category = array($trans_dc_subject_category);
					} else{
						$trans_dc_subject_category = split("\|",$trans_dc_subject_category);
					}
					/**
					* blank the default metadata
					*/
					$smd_audience						= "";
					$smd_subject						= "";
					$smd_doctypes						= "";
					$smd_copy_location					= "";
					$smd_publisher_contact_information	= "";
			
					/**
					* update the blanks with data from the basic default metadata stored in the database
					*/
						

					$list = $this->call_command("CONTACT_GET_DESCRIPTIONS",Array($trans_dc_creator,"field" => "contact_identifier"));
					
					$max = count($list);
					$creators = "";
					for($index=0;$index<$max;$index++){
						$creators .= "<li>".$list[$index]."</li>";
					}
					$list = $this->call_command("CONTACT_GET_DESCRIPTIONS",Array($trans_dc_contributor,"field" => "contact_identifier"));
					$max = count($list);
					$contributors = "";
					for($index=0;$index<$max;$index++){
						$contributors .= "<li>".$list[$index]."</li>";
					}
						if ($this->check_parameters($meta_form_restriction_list,"trans_dc_source","__NOT_FOUND__")!="__NOT_FOUND__"){
							$out.= "<input type=\"text\" label=\"Where did you get your source for the document\" name=\"metadata_source\" size=\"255\"><![CDATA[$trans_dc_source]]></input>";
						}
						if ($this->check_parameters($meta_form_restriction_list,"trans_dc_rights","__NOT_FOUND__")!="__NOT_FOUND__"){
							$out.= "<input type=\"text\" label=\"What rights are with this document (view, copy, redistribute, republish)\" name=\"metadata_rights\" size=\"255\"><![CDATA[$trans_dc_rights]]></input>";
						}
						if ($this->check_parameters($meta_form_restriction_list,"trans_dc_rights_copyright","__NOT_FOUND__")!="__NOT_FOUND__"){
							if ($trans_dc_rights_copyright!=""){
								$copy_right = $trans_dc_rights_copyright;
							} else {
								$copy_right = $smd_copy_location;
							}
							$out.= "<input type=\"text\" label=\"Where is the copyright information for this document?\" name=\"metadata_rights_copyright\" size=\"255\"><![CDATA[$copy_right]]></input>";
						}
						if ($this->check_parameters($meta_form_restriction_list,"trans_dc_audience","__NOT_FOUND__")!="__NOT_FOUND__"){
							$out.= "<checkboxes name=\"metadata_audience\" label=\"Select the Audience that this document is for.\" type=\"horizontal\">";
							/**
							* take the list of audience types and return a option list
							*/ 
							$out .=$this->optionise_metadata_choice($smd_audience,$trans_dc_audience);
							$out .="</checkboxes>";
						}
						if ($this->check_parameters($meta_form_restriction_list,"trans_dc_doc_type","__NOT_FOUND__")!="__NOT_FOUND__"){
							$out.= "<select name=\"metadata_doc_type\" label=\"Select the type of document.\">";
							/**
							* take the list of audience types and return a option list
							*/ 
							$out .=$this->optionise_metadata_choice($smd_doctypes,$trans_dc_doc_type);
							$out .="</select>";
						}
						
						if (($this->check_parameters($meta_form_restriction_list,"trans_dc_subject_category","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($form_restriction_list,"trans_dc_subject_programme","__NOT_FOUND__")!="__NOT_FOUND__") ||($this->check_parameters($form_restriction_list,"trans_dc_subject_PROJECT","__NOT_FOUND__")!="__NOT_FOUND__")){
							$out.= "<text><![CDATA[<b>Subject</b>]]></text>";
							if ($this->check_parameters($meta_form_restriction_list,"trans_dc_subject_category","__NOT_FOUND__")!="__NOT_FOUND__"){
								$out.= "<checkboxes name=\"metadata_subject_category\" label=\"metadata_subject_category\" type=\"horizontal\">";
								/**
								* take the list of audience types and return a option list
								*/								$out .= $this->optionise_metadata_choice($smd_subject,$trans_dc_subject_category);
								
								$out .= "</checkboxes>";
							}
						if ($this->check_parameters($meta_form_restriction_list,"trans_dc_subject_programme","__NOT_FOUND__")!="__NOT_FOUND__"){
								$out .= "<input type=\"text\" label=\"Programme\" name=\"metadata_subject_programme\" size=\"255\"><![CDATA[$trans_dc_subject_programme]]></input>";
							}
							if ($this->check_parameters($meta_form_restriction_list,"trans_dc_subject_PROJECT","__NOT_FOUND__")!="__NOT_FOUND__"){
								$out .= "<input type=\"text\" label=\"Project\" name=\"metadata_subject_project\" size=\"255\"><![CDATA[$trans_dc_subject_project]]></input>";
							}
						}
						if ($this->check_parameters($meta_form_restriction_list,"trans_dc_contributor","__NOT_FOUND__")!="__NOT_FOUND__"){
							$out .= "<input type=\"hidden\" name=\"metadata_contributor_associations\"><![CDATA[$trans_dc_contributor]]></input>";
							$out .= "<text><![CDATA[<b>Contributers</b> (one entry per new line)]]></text>";
							$out .= "<subsection label=\"LOCALE_META_EXPLAIN_CONTRIBUTOR\" name=\"metadata_contributor\" command=\"CONTACT_LIST_SELECTION\" link='metadata_contributor_associations' return_command='CONTACT_LIST_SELECTION_DETAILS'><![CDATA[$contributors]]></subsection>";
						}
						if ($this->check_parameters($meta_form_restriction_list,"trans_dc_creator","__NOT_FOUND__")!="__NOT_FOUND__"){
							$out .= "<input type=\"hidden\" name=\"metadata_creator_associations\" value=\"$trans_dc_creator\"/>";
							$out .= "<text><![CDATA[<b>Creator(s)</b> (one entry per new line)]]></text>";
							$out .= "<subsection label=\"".LOCALE_META_EXPLAIN_AUTHOR."\" name=\"metadata_creator\" command=\"CONTACT_LIST_SELECTION\" link='metadata_creator_associations' return_command='CONTACT_LIST_SELECTION_DETAILS'><![CDATA[$creators]]></subsection>";
						}
						if (($this->check_parameters($meta_form_restriction_list,"trans_dc_coverage_place","__NOT_FOUND__")!="__NOT_FOUND__")
						|| ($this->check_parameters($meta_form_restriction_list,"trans_dc_coverage_postcode","__NOT_FOUND__")!="__NOT_FOUND__")
						|| ($this->check_parameters($meta_form_restriction_list,"trans_dc_coverage_time","__NOT_FOUND__")!="__NOT_FOUND__")){
							$out .= "<text><![CDATA[<b>Coverage</b>]]></text>";
							if ($this->check_parameters($meta_form_restriction_list,"trans_dc_coverage_place","__NOT_FOUND__")!="__NOT_FOUND__"){
								$out .= "<input type=\"text\" label=\"Place\" name=\"metadata_coverage_place\" size=\"255\"><![CDATA[$trans_dc_coverage_place]]></input>";
							}
							if ($this->check_parameters($meta_form_restriction_list,"trans_dc_coverage_postcode","__NOT_FOUND__")!="__NOT_FOUND__"){
								$out .= "<input type=\"text\" label=\"Postcode\" name=\"metadata_coverage_postcode\" size=\"255\"><![CDATA[$trans_dc_coverage_postcode]]></input>";
							}
							if ($this->check_parameters($meta_form_restriction_list,"trans_dc_coverage_time","__NOT_FOUND__")!="__NOT_FOUND__"){
								$out .= "<input type=\"text\" label=\"Time\" name=\"metadata_coverage_time\" size=\"50\"><![CDATA[$trans_dc_coverage_time]]></input>";
							}
						}
					$out.= "</section>";
				}
			}
/*
			if ($this->check_parameters($form_restriction_list,"section_summary","__NOT_FOUND__")!="__NOT_FOUND__"){
				if (strlen($documentation_summary)>0){
					$out .= "		<section label=\"".LOCALE_SUMMARISATION."\" name=\"trans_document_information\" >$documentation_summary</section>";
				}
			}
*/
/*			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				if (strlen($comment_list)>0){
					$out .= "		<section label=\"Admin Notes\" name=\"trans_comments\" ";
					if ($display_tab=="comments"){
						$out .= " selected='true'";
					}
					$out .= "></section>";
				}
			}*/
			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				if ($identifier!=""){
					/*
					$sql = "select * from page_comments 
							left outer join contact_data on comment_user = contact_user
							where comment_translation = $identifier and comment_client=$this->client_identifier";
					*/
					$sql = "select page_comments.*, trans_2.trans_identifier, contact_data.* from page_data
								inner join page_trans_data as trans_1 on page_identifier = trans_1.trans_page 
								left outer join page_trans_data as trans_2 on page_identifier = trans_2.trans_page 
								inner join page_comments on comment_translation = trans_2.trans_identifier
								LEFT OUTER join contact_data on comment_user=contact_user 
								where trans_1.trans_identifier = $identifier and comment_client=$this->client_identifier order by comment_identifier desc;";
					if($result = $this->call_command("DB_QUERY",array($sql))) {
						while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
							$cid			= $this->check_parameters($r,"comment_identifier");
							$comment_title	= $this->check_parameters($r,"comment_title");
							$full_name  	= $this->check_parameters($r,"contact_first_name").", ".$this->check_parameters($r,"contact_last_name");
							$date 			= $this->check_parameters($r,"comment_date");
							if ($this->check_parameters($r,"comment_type")){
								$note_list		.= "<comment comment_identifier='$cid' url=\"?command=COMMENTSADMIN_VIEW&amp;entry_type=ADMIN&amp;identifier=$identifier&amp;\"><title><![CDATA[$comment_title]]></title><author><![CDATA[$full_name]]></author><date><![CDATA[$date]]></date></comment>";
							} else {
								$comment_list	.= "<comment comment_identifier='$cid' url=\"?command=COMMENTSADMIN_VIEW&amp;entry_type=ADMIN&amp;identifier=$identifier&amp;\"><title><![CDATA[$comment_title]]></title><author><![CDATA[$full_name]]></author><date><![CDATA[$date]]></date></comment>";
							}
						}
					}
				}
				if($note_list!=""){
					$out.= "<section label=\"Admin Notes\"";
						if ($display_tab=="admin_notes"){
							$out .= " selected='true'";
						}
					$out .= ">
						<input type='hidden' name=\"page_admin_notes\" value=\"1\"/>
						<text><![CDATA[List of Comments]]></text>
						<comments>$note_list</comments>
					</section>";
				} else {
					$out.="<input type='hidden' name=\"page_admin_notes\" value=\"1\"/>";
				}
				if($this->discussion_admin_access==0){
					$out.="<input type='hidden' name='page_web_notes' value='$page_web_notes'/>";
				} else {
				$out.= "<section label=\"Web Notes\"";
				if ($display_tab=="web_notes"){
					$out .= " selected='true'";
				}
				$out .= ">
						<radio label=\"".LOCALE_COMMENT_WEB_MSG."\" name=\"page_web_notes\">
							<option value=\"1\"";
				if 	($page_web_notes == '1'){
					$out .=" selected=\"true\"";
				}
				$out.= ">".ENTRY_YES."</option>
							<option value=\"0\"";
				if 	($page_web_notes == '0'){
					$out .=" selected=\"true\"";
				}
				$out.= ">".ENTRY_NO."</option>
						</radio>
						<comments>$comment_list</comments>
					</section>";
				}
			}
/*			$out.= "<section label=\"Page Properties\"";
					if ($display_tab=="page_properties"){
						$out .= " selected='true'";
					}
					$out .= ">";
					if ($identifier==-1){
					
					}else {
						$out .= "<text><![CDATA[".$manage."]]></text>";
					}
					$out .="</section>";
*/			
			$out .= $this->preview_section('PAGE_PREVIEW_FORM');
			
			$out .= "	</page_sections>";
			if ($this->parent->server[LICENCE_TYPE]==SITE_WIZARD){
				if ($trans_published_version==0){
					$out .= "	<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
				}
			} else {
				$out .= "	<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
			}
			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				if ($this->approver_access ){
					$out .= "	<input iconify=\"APPROVE\" type=\"submit\" command=\"PAGE_SAVE_AND_APPROVE\" value=\"".LOCALE_SAVE_DATA_APPROVER."\"/>";
				}
			}
			if ($this->parent->server[LICENCE_TYPE]==MECM){
				$out .= "	<input iconify=\"READY\" type=\"submit\" command=\"PAGE_SAVE_AND_APPROVE\" value=\"".LOCALE_SAVE_DATA_APPROVER."\"/>";
			}
			if ($this->publisher_access){
				$out .= "	<input iconify=\"PUBLISH\" type=\"submit\" command=\"PAGE_SAVE_AND_PUBLISH\" value=\"".LOCALE_SAVE_DATA_SITE."\"/>";
			}
			$out .= "</form>";
		} else {
			$out .= "<form name=\"user_form\" method=\"post\" label=\"".LOCALE_WARNING_NO_ACCESS."\">";
			$out .= "<text><![CDATA[".LOCALE_WARNING_ENTRY_BLOCKED."]]></text>";
			$out .= "<input iconify=\"CANCEL\" type=\"button\" value=\"LOCALE_CANCEL\" command=\"PAGE_LIST\"/>";
			$out .= "</form>";
		}
		$out .= "</module>";
		return $out;
	}
	
	function get_status($c_status){
		$out  = "<option value=\"1\"";
		if ($c_status==1){
			$out .= " selected=\"true\"";
		}
		$out .= ">".LOCALE_AUTHORING."</option>";
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$out .= "<option value=\"2\"";
			if ($c_status==2){
				$out .= " selected=\"true\"";
			}
			$out .= ">".LOCALE_READY."</option>";
			$out .= "<option value=\"3\"";
			if ($c_status==3){
				$out .= " selected=\"true\"";
			}
			$out .= ">".LOCALE_APPROVED."</option>";
		} else if ($this->parent->server[LICENCE_TYPE]==MECM){
			$out .= "<option value=\"3\"";
			if ($c_status==3){
				$out .= " selected=\"true\"";
			}
			$out .= ">".SEND_TO_PUBLISHER."</option>";
		}
		$out .= "<option value=\"4\"";
		if ($c_status==4){
			$out .= " selected=\"true\"";
		}
		$out .= ">".LOCALE_PUBLISHED."</option>";
		/*	
		$out .= "<option value=\"5\"";
		if ($c_status==5){
			$out .= " selected=\"true\"";
		}
		$out .= ">".LOCALE_ARCHIVED."</option>";
		*/
		return $out;
	}
	
	function process_action($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"process_action",__LINE__,"[]"));
		}
		$cmd 					= $this->check_parameters($parameters,"command","");
		$user_identifier 		= $_SESSION["SESSION_USER_IDENTIFIER"];
		$identifier 			= $this->check_parameters($parameters,"identifier",1);
		$page_comment 			= $this->check_parameters($parameters,"page_comment");
		$lang 					= 'en';
		$translation_page		= -1;
		$found_same_language	= false;
		$remove_completely 		= false;
		$translation_page 		= -1;
		$delete_translation 	= -1;
		$update_translation 	= -1;
		$trans_doc_status = $this->check_parameters($parameters, "trans_doc_status");
		if ($this->check_parameters($parameters, "command")=="PAGE_REMOVE_CONFIRM"){
			if ($trans_doc_status=="")
				$trans_doc_status ="__REMOVE__";
		}
		if ($this->check_parameters($parameters, "command")=="PAGE_PUBLISH_CONFIRM"){
			if ($trans_doc_status=="")
				$trans_doc_status ="__STATUS_PUBLISHED__";
		}
		if (($this->parent->server[LICENCE_TYPE]!=ECMS) && ($this->parent->server[LICENCE_TYPE]!=MECM) ){
			if ($this->check_parameters($parameters, "command")=="PAGE_UNPUBLISH_CONFIRM"){
				if ($trans_doc_status=="")
					$trans_doc_status ="__STATUS_AUTHOR__";
			}
			if ($this->check_parameters($parameters, "command")=="PAGE_REMOVE_CONFIRM"){
				if ($trans_doc_status=="")
					$trans_doc_status = "__REMOVE__";
			}
		}



		if ($this->module_constants[$trans_doc_status] == $this->module_constants["__REMOVE_REVERT__"]){
			$sql="select page_data.page_identifier, ptd2.trans_identifier, ptd2.trans_doc_version_major as ptd2_major, ptd2.trans_doc_version_minor as ptd2_minor
				  from page_data 
					inner join page_trans_data as ptd1 on ptd1.trans_page = page_identifier 
					inner join page_trans_data as ptd2 on ptd2.trans_page = page_identifier 
				  where 
					ptd1.trans_identifier=$identifier and page_client=$this->client_identifier and
					ptd2.trans_doc_version_major >= (ptd1.trans_doc_version_major-1)
			 	  order by 
					ptd2.trans_doc_version_major asc,
					ptd2.trans_doc_version_minor asc";
			$result 		= $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$revert_identifier = -1;
			$list_to_delete = array();
			$c =0;
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$page_identifier  = $r["page_identifier"];
				if($c==0){
					$revert_identifier = $r["trans_identifier"];
				} else {
					$list_to_delete[count($list_to_delete)] = $r["trans_identifier"];
				}
				$c++;
			}
			$list = join(",", $list_to_delete);
			$sql = "delete from page_trans_data where trans_identifier in ($list) and trans_client=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from menu_access_to_page where trans_identifier in ($list) and client_identifier=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from file_access_to_page where trans_identifier in ($list) and client_identifier=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from group_access_to_page where trans_identifier in ($list) and client_identifier=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from file_to_object where fto_object in ($list) and fto_client=$this->client_identifier and fto_module='PAGE_SUMMARY'";
			$this->call_command("DB_QUERY",array($sql));
			$sql ="update page_trans_data set trans_current_working_version=1 where trans_identifier = $revert_identifier and trans_client = $this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$this->call_command("EMBED_REMOVE_INFO", Array("trans_list"=>$list_to_delete,"action"=>"remove_list"));
		} else if ($this->module_constants[$trans_doc_status] == $this->module_constants["__REMOVE_ALL_HISTORY__"]){
			$sql ="
			select page_data.page_identifier, ptd2.trans_identifier from page_data 
				inner join page_trans_data as ptd1 on ptd1.trans_page = page_identifier 
				inner join page_trans_data as ptd2 on ptd2.trans_page = page_identifier 
			where 
				ptd1.trans_identifier=$identifier and page_client=$this->client_identifier
			";
			$result 		= $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$list_to_delete = array();
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$page_identifier  						= $r["page_identifier"];
				$list_to_delete[count($list_to_delete)] = $r["trans_identifier"];
			}
			$list = join(",", $list_to_delete);
			$sql = "delete from page_data where page_identifier = $page_identifier and page_client=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from page_trans_data where trans_identifier in ($list) and trans_client=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from menu_access_to_page where trans_identifier in ($list) and client_identifier=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from file_access_to_page where trans_identifier in ($list) and client_identifier=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from group_access_to_page where trans_identifier in ($list) and client_identifier=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from memo_information where mi_link_id in ($list) and mi_type='PAGE_' and mi_client=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$this->call_command("EMBED_REMOVE_INFO", Array("trans_list"=>$list_to_delete,"identifier"=>$identifier, "action"=>"remove_list"));
		} else if ($this->module_constants[$trans_doc_status]==$this->module_constants["__REMOVE__"]){
			/**
			* delete page from system and delete all comments and all associations with any files
			* do not remove the file as a file might be associated with more than one page
			* well it might
			*/
			$sql = "select 
						first_table.trans_identifier as identifier,
						second_table.trans_identifier,
						second_table.trans_client,
						second_table.trans_current_working_version,
						second_table.trans_page,
						second_table.trans_doc_version_major,
						second_table.trans_doc_version_minor
					from 
						page_trans_data as first_table
					inner join 
						page_trans_data as second_table
						on first_table.trans_page = second_table.trans_page
					where 
						first_table.trans_identifier = $identifier and 
						second_table.trans_client = $this->client_identifier and 
						second_table.trans_language = '$lang' and 
						second_table.trans_page
					order by 
						second_table.trans_doc_version_major desc,	second_table.trans_doc_version_minor desc
					limit 0,2";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$number_of_rows = $this->call_command("DB_NUM_ROWS",array($result));
			if ($number_of_rows==1){
				/**
				* if only one record is return then the assumption is that 
				* there is only one document in that translation 
				* ::NOTICE::
				* if you remove the only version of a page it should delete the page_data entry only if it is the only lanugage
				*
				* retrieve the record information
				*/
				$row_one = $this->call_command("DB_FETCH_ARRAY",Array($result));
				/**
				* generate default variables for the algorithm
				*/
				$translation_page 		= $row_one["trans_page"];
				$delete_translation 	= $row_one["identifier"];
				$update_translation 	= -1;
				$version_being_removed 	= $row_one["trans_doc_version_major"].".".$row_one["trans_doc_version_minor"];
				
				/**
				* check to see if this is the only translation for this page
				*/
				$sql = "select * from page_data left outer join page_trans_data on trans_page=page_identifier where page_identifier =$translation_page";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				$other_trans_result = $this->call_command("DB_QUERY",array($sql));
				$otr_number_of_rows = $this->call_command("DB_NUM_ROWS",array($other_trans_result));
				if ($otr_number_of_rows==1){
					/**
					* if the number of results returned are equal to 1 ie the page_data and the translation we are
					* removing.  Then we can remove entries from both tables
					* we will store that there are no documents of the same language and
					* we will store that we are to remove completly as well.
					*/
					$found_same_language = false;
					$remove_completly = true;
				} else {
					/**
					* if the number of results returned are not equal to 1 ie the page has several translations
					* don't forget that the first sql statement only looks for the language that is being removed
					*/
					$found_same_language = false;
					while ($record = $this->call_command("",Array())){
						if ($record["trans_language"]==$lang){
							$found_same_language=true;
						}
					}
					$remove_completely = false;
				}
			} else if ($number_of_rows==2){
				/**
				* if two records is return then the assumption is that 
				* the first record will contain the document you are removing.
				* the second will retrieve the record that will be updated to have the current working version value set to 1
				*/
				$row_one = $this->call_command("DB_FETCH_ARRAY",Array($result));
				$row_two = $this->call_command("DB_FETCH_ARRAY",Array($result));
				/**
				* generate default variables for the algorithm
				*/
				$translation_page 		= $row_one["trans_page"];
				$delete_translation 	= $row_one["identifier"];
				$version_being_removed 	= $row_one["trans_doc_version_major"].".".$row_one["trans_doc_version_minor"];
				$update_translation 	= $row_two["trans_identifier"];
				$found_same_language 	= true;
				/**
				* if the number of results returned are not equal to 1 ie the page has several translations
				* don't forget that the first sql statement only looks for the language that is being removed
				*/
				$remove_completely = false;
			} else{
				/// ERROR SHOULD NEVER GET HERE
			}
			/**
			* ok delete the actual record from the system
			*/
			$sql = "delete from page_trans_data where trans_client=$this->client_identifier and trans_identifier = ".$delete_translation;
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			/**
			* update the previous version to be the working version for the system
			*/
			
			if ($update_translation!=-1){
				$sql = "update page_trans_data set trans_current_working_version=1 where trans_identifier=$update_translation";
				$result = $this->call_command("DB_QUERY",array($sql));
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
			}
			
			/**
			* if we are not removing all information from the system then add a commetn to the page notes 
			- for the language that is having the translation removed
			*/
			if (!$remove_completely){
				/**
				* Only add a comment to the comments to explain why the version was removed if the system is not
				* going to remove the complete page entry.
				*
				* only add a comment if there are other entries in the
				*/
				if ($found_same_language){
					$now = $this->libertasGetDate("Y/m/d H:i:s");
					$command			= $parameters["command"];
//					$contact_full_name	= $this->call_command("CONTACT_GET_NAME",Array("contact_user" => $user_identifier));
					$msg		 		= $this->validate($this->check_parameters($parameters,"page_comment",""));
					$comment_title		= strip_tags($this->validate($this->tidy($this->check_parameters($parameters,"comment_title",""))));
					if ($msg!=""){
					$sql			 	= "insert into page_comments(
											comment_translation, 
											comment_client, 
											comment_message, 
											comment_date, 
											comment_user, 
											comment_response_to, 
											comment_type,
											comment_title
										) values (
											'$update_translation',
											'$this->client_identifier',
											'".$msg."',
											'$now',
											$user_identifier,
											'-1',
											'1',
											'$comment_title'
										)";
						$result = $this->call_command("DB_QUERY",array($sql));
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
						}
					}
				}
			} else {
				/**
				* we are to remove the page_data information too.
				*/
				$sql = "delete from page_data where page_identifier=$translation_page";
				$result = $this->call_command("DB_QUERY",array($sql));
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				/**
				* we are to remove all comments for this page in the specific language.
				*/
				$sql = "delete from page_comments where comment_page=$translation_page and comment_language='$lang'";
				$result = $this->call_command("DB_QUERY",array($sql));
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				/**
				* delete file
				*/
				
			}
			/**
			* file associations 
			*/
			$sql ="delete from file_access_to_page where trans_identifier=$delete_translation and client_identifier =$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			/**
			* group associations 
			*/
			$sql ="delete from group_access_to_page where trans_identifier=$delete_translation and client_identifier =$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			/**
			* menu associations 
			*/
			$sql ="delete from menu_access_to_page where trans_identifier=$delete_translation and client_identifier =$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			/**
			* remove the cached page if it exists
			*/
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
			if (file_exists($data_files."/page_".$lang."_".$parameters["identifier"].".xml")){
				unlink($data_files."/page_".$lang."_".$parameters["identifier"].".xml");
			}
			$this->call_command("EMBED_REMOVE_INFO", Array("identifier"=>$parameters["identifier"],"action"=>"remove_item"));
		} else {
			/**
			* we are not removing but changing the document in some way.
			*
			* when new versions become available
			* 1. when an author sends to approver
			* 2. when publisher sends back to author copy is made ie version 1.1
			*
			* number one is done on document save
			* number 2 here.
			*/
			
			$now = $this->libertasGetDate("Y/m/d H:i:s");
			$command			= $parameters["command"];
			$user				= $_SESSION["SESSION_USER_IDENTIFIER"];
			$contact_full_name	= $this->call_command("CONTACT_GET_NAME",Array("contact_user" => $user));

			if (strlen($this->check_parameters($parameters,"page_comment",""))>0){
				$msg		 	= $this->validate($this->check_parameters($parameters,"page_comment",""));
				$comment_title	= strip_tags($this->validate($this->tidy($this->check_parameters($parameters,"comment_title",""))));
				$insertsql		= "insert into page_comments (
											comment_translation, 
											comment_client, 
											comment_message, 
											comment_date, 
											comment_user, 
											comment_response_to, 
											comment_type,
											comment_title
										) values (
											'$identifier',
											'$this->client_identifier',
											'".$msg."',
											'$now',
											$user,
											'-1',
											'1',
											'$comment_title'
										)";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"[$insertsql]"));
				}
				$result	= $this->call_command("DB_QUERY",array($insertsql));
			}
			$extra ="";
			if (($cmd == $this->module_command."APPROVE_CONFIRM") || ($cmd == $this->module_command."PUBLISH_CONFIRM")){
				$extra .=", trans_doc_lock_to_user=0";
			}
			if ($cmd == $this->module_command."UNPUBLISH_CONFIRM"){
				$extra .=", trans_published_version=0";
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					if ($this->module_constants[$trans_doc_status] == $this->module_constants["__STATUS_AUTHOR__"]){
				//		$identifier = $this->create_new_version($identifier);
					}
				}
			}
			if ($cmd == $this->module_command."EXTRACT_COPY_CONFIRM"){
				if ($this->module_constants[$trans_doc_status]==$this->module_constants["__STATUS_AUTHOR__"]){
						$identifier = $this->create_new_version($identifier);
				}
			}
			$sql ="update page_trans_data set 
						trans_doc_status=".$this->module_constants[$trans_doc_status]." $extra
					where 
						trans_identifier = $identifier and 
						trans_client	 = $this->client_identifier and 
						trans_current_working_version=1
				";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}

///
//			print $identifier." ".$this->parent->server[LICENCE_TYPE]." ".MECM;
			if ($this->parent->server[LICENCE_TYPE]==MECM){
				$this->remove_previous_versions(Array("identifier"=>$identifier));
			}
//			$this->exitprogram();
		}
	}
	
	function save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save",__LINE__,"[".print_r($parameters,true)."]"));
		}
		$command						= $this->check_parameters($parameters, "command");
		
		$user							= $_SESSION["SESSION_USER_IDENTIFIER"];
		$prev_trans_menu_locations		= $this->check_parameters($parameters, "prev_trans_menu_locations");
		$trans_title					= htmlentities(trim($this->strip_tidy($this->check_parameters($parameters,"trans_title"))));
		$trans_identifier				= $this->check_parameters($parameters, "trans_identifier",$this->check_parameters($parameters,"identifier",-1));
		$trans_menu_locations			= $this->check_parameters($parameters, "trans_menu_locations");
		$file_summary_label				= $this->check_parameters($parameters, "file_attached_label");
		$file_summary_id				= $this->check_parameters($parameters, "file_attached_identifier");
		
		
		$tml 							= split(",",$this->check_parameters($parameters, "trans_menu_location"));
		$trans_menu_locations .= $tml[0].",";
		$page_id						= $this->check_parameters($parameters, "page_identifier",-1);
//		print_r($parameters);
		$check = $this->check_title($trans_title, $trans_identifier, $trans_menu_locations, $page_id);
//		print "[$check]";
//		$this->exitprogram();
		if($check==0){
			$parameters["command"]="SAVE_ERROR";
			return $this->page_form($parameters);
		} else {
			$trans_menu_locations			= $this->check_parameters($parameters, "trans_menu_locations");
			$tml 							= $this->check_parameters($parameters, "trans_menu_location",-1);
			$p_trans_identifier				= $trans_identifier;
			$prev							= $this->check_parameters($parameters, "prev_command");
			$previous_page_title			= $this->check_parameters($parameters, "previous_page_title");
			$page_admin_notes				= $this->check_parameters($parameters, "page_admin_notes",0);
			$page_web_notes					= $this->check_parameters($parameters, "page_web_notes",0);
			$trans_group					= $this->check_parameters($parameters, "trans_groups","");
			$temp_val 						= $this->check_parameters($parameters, "trans_body");
			$trans_body						= $this->check_editor($parameters, "trans_body");
			$trans_summary					= $this->check_editor($parameters, "trans_summary");
	//		print "[$trans_summary], [$trans_body]";
	//		$this->exitprogram();
			$trans_dc_alt_title				= trim($this->strip_tidy($this->check_parameters($parameters,"trans_dc_alt_title")));
			$trans_doc_status				= $this->check_parameters($parameters, "trans_doc_status");
			$file_assoc 					= $this->check_parameters($parameters, "file_associations");
//			$metadata_date_revalidate		= $this->check_parameters($parameters, "metadata_date_revalidate");
			$my_metadata_date_available		= $this->check_date($parameters, "metadata_date_available","-- :00:00");
			$my_metadata_date_revalidate	= $this->check_date($parameters, "metadata_date_revalidate","-- :00:00");
			$my_metadata_date_remove		= $this->check_date($parameters, "metadata_date_remove","-- :00:00");
			$metadata_date_remove 			= $this->check_parameters($parameters, "metadata_date_remove");
			$new_minor_version				= $this->check_parameters($parameters, "trans_version_minor");
			$new_trans_id					= -1;
			$keyword_ignore_list			= $this->check_parameters($parameters, "keyword_ignore_list");
			$keywords						= $this->check_parameters($parameters, "keywords");
			$phrase_ignore_list				= $this->check_parameters($parameters, "phrase_ignore_list");
			$trans_dc_subject_keywords 		= join("",split("; ; ",join("; ",$this->check_parameters($parameters,"phrase",Array()))));
			$publisher_info					= $this->tidy_parameter($parameters,"publisher_info");
			$metadata_source				= $this->tidy_parameter($parameters,"metadata_source");
			$metadata_rights				= $this->tidy_parameter($parameters,"metadata_rights");
			$metadata_rights_copyright		= $this->tidy_parameter($parameters,"metadata_rights_copyright");
			$metadata_audience				= $this->tidy_parameter($parameters,"metadata_audience");
			if (is_array($metadata_audience)){
				$metadata_audience 			= join("|",$metadata_audience);
			} 
			$metadata_doc_type				= $this->tidy_parameter($parameters,"metadata_doc_type");
			$metadata_subject_category		= $this->tidy_parameter($parameters,"metadata_subject_category");
			if (is_array($metadata_subject_category)){
				$metadata_subject_category	= join("|",$metadata_subject_category);
			}
			$metadata_subject_programme		= $this->tidy_parameter($parameters,"metadata_subject_programme");
			$metadata_subject_project		= $this->tidy_parameter($parameters,"metadata_subject_project");
			$metadata_contributor			= $this->tidy_parameter($parameters,"metadata_contributor_associations");
			$metadata_creator				= $this->tidy_parameter($parameters,"metadata_creator_associations");
			$metadata_coverage_place		= $this->tidy_parameter($parameters,"metadata_coverage_place");
			$metadata_coverage_postcode		= $this->tidy_parameter($parameters,"metadata_coverage_postcode");
			$metadata_coverage_time			= $this->tidy_parameter($parameters,"metadata_coverage_time");
			
			if($keyword_ignore_list!=""){
				$this->add_to_ignore_list($keyword_ignore_list);
			}
			
			$keys  = "";
			if (is_array($keywords)){
				$length_of_keys = count($keywords);
				for($index=0;$index<$length_of_keys;$index++){
					$key = split(", ",$keywords[$index]);
					$keys .= "<keyword count=\"".$key[0]."\"><![CDATA[".strtolower($key[1])."]]></keyword>";
				}
			} else {
				$check = substr($keywords,0,strlen("<keywords>"));
				if ($check=="<keywords>"){
					$l = strlen($keywords);
					$keys = substr($keywords, strlen("<keywords>"), ($l - strlen("<keywords></keywords>") ) );
				}else{
					if ($keywords!=""){
						$length_of_keys = count($keywords);
						for($index=0;$index<$length_of_keys;$index++){
							$key = split(", ",$keywords[$index]);
							$keys .= "<keyword count=\"".$key[0]."\"><![CDATA[".strtolower($key[1])."]]></keyword>";
						}
					}
				}
			}
			$list_of_embedded_information			= $this->call_command("EMBED_EXTRACT_INFO" , Array("str" => $trans_body) );
			$list_of_embedded_information_summary	= $this->call_command("EMBED_EXTRACT_INFO" , Array("str" => $trans_summary) );
			/**
			* when to create a new version of the document 
			*/
			$version_control_new_on_save	= false;
			$new_version_control			= false;
			/**
			* if the enterprise user has selected a menu location but not added to list then add to list
			*/
			if ($tml!="-1"){
				$list = split(",",$tml);
				$trans_menu_locations .=" ".$list[0].",";
			}
			$new_menu_parent	= $this->check_parameters($parameters,"new_menu_parent","");
			$new_menu_label		= $this->check_parameters($parameters,"new_menu_label","");
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"conditions",__LINE__,"[$new_menu_parent], [$new_menu_label]"));}
			if ($new_menu_parent!=""){
			 	$new_menu_parent_list = split(",",$new_menu_parent);
				$parameter_data = Array("parent" => $new_menu_parent_list[0], "label" => $new_menu_label, "command"=>"LAYOUT_MENU_INSERT");
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"parameters ",__LINE__,"".print_r($parameter_data,true).""));}
			 	$new_trans_loc = $this->call_command("LAYOUT_MENU_INSERT", $parameter_data);
				if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"CALL ",__LINE__,":: new_trans_loc => $new_trans_loc"));}
				if ($new_trans_loc != -1){
					if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"CALL ",__LINE__,"::  ".ECMS." => [".$this->parent->server[LICENCE_TYPE]."]"));}
					if ($this->parent->server[LICENCE_TYPE]==ECMS){
						$trans_menu_locations .=" ".$new_trans_loc.",";
					} else {
						if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"CALL ",__LINE__,":: trans_menu_locations => [$new_trans_loc]"));}
						$trans_menu_locations = $new_trans_loc;
					}
				}
			}
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"CALL ",__LINE__,":: trans_menu_locations => [$trans_menu_locations]"));}
			/**
			* get the current date time stamp for all of the queries that will require to use the same datetime for verification.
			*/
			$now = $this->libertasGetDate("Y/m/d H:i:s");
			if (empty($trans_group)){
				$trans_group=0;
			}
			$extra_update="";
			/**
			* On edit of a page we want to do one of the follwoing 
			* 
			* 1. Update the current minor version of the document
			* 2. Create a new minor version of the document
			* 
			* and posibly set that this document must be approved. 
			*/
			if ($prev=="PAGE_EDIT"){
				if ($trans_doc_status!="__NO_CHANGE__"){
					$extra_update = "trans_doc_status=$trans_doc_status,";
				}
				$this->call_command("EMBED_SAVE_INFO",Array("list_of_results" => $list_of_embedded_information, 		"id" => $trans_identifier, "editor"=>"body", 	"module"=>$this->module_command,"previous_title"=>$previous_page_title));
				$this->call_command("EMBED_SAVE_INFO",Array("list_of_results" => $list_of_embedded_information_summary, "id" => $trans_identifier, "editor"=>"summary", "module"=>$this->module_command,"previous_title"=>$previous_page_title));
				/**
				* if we are to create a new version only on approval then do the following
				*/
				if ($my_metadata_date_available=="-- :00:00"){
					$my_metadata_date_available = "";
				}
				if ($my_metadata_date_revalidate=="-- :00:00"){
					$my_metadata_date_revalidate = "";
				}
				if ($my_metadata_date_remove=="-- :00:00"){
					$my_metadata_date_remove = "";
				}
				$url = $this->make_uri($trans_title).".php";
				$sql = "update page_trans_data set 
							$extra_update
							trans_title					= '$trans_title',  
							trans_dc_alt_title			= '$trans_dc_alt_title',  
							trans_doc_author_identifier	= '$user',  
							trans_dc_keywords			= '$keys', 
							trans_dc_subject_keywords	= '$trans_dc_subject_keywords',
							trans_dc_publisher			= '$publisher_info',
							trans_dc_source				= '$metadata_source',
							trans_dc_rights				= '$metadata_rights',
							trans_dc_rights_copyright	= '$metadata_rights_copyright',
							trans_dc_audience			= '$metadata_audience',
							trans_dc_doc_type			= '$metadata_doc_type',
							trans_dc_subject_category	= '$metadata_subject_category',
							trans_dc_subject_programme	= '$metadata_subject_programme',
							trans_dc_subject_project	= '$metadata_subject_project',
							trans_dc_contributor		= '$metadata_contributor',
							trans_dc_creator			= '$metadata_creator',
							trans_dc_coverage_place		= '$metadata_coverage_place',
							trans_dc_coverage_postcode	= '$metadata_coverage_postcode',
							trans_dc_coverage_time		= '$metadata_coverage_time',
							trans_dc_url				= '$url',
							trans_date_modified			= '$now'";
	
				if ($my_metadata_date_available!=''){
					$sql .= ", trans_date_available='$my_metadata_date_available'";
				}
				if ($my_metadata_date_revalidate!=''){
					$sql .= ", trans_date_review='$my_metadata_date_revalidate'";
				}
				if ($my_metadata_date_remove!=''){
					$sql .= ", trans_date_remove='$my_metadata_date_remove'";
				}
				$sql .= " where 
							trans_identifier=$trans_identifier and 
							trans_client =$this->client_identifier and 
							trans_current_working_version=1";
				$result = $this->call_command("DB_QUERY",array($sql));
				$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->module_command,"mi_memo"=>$trans_body,	"mi_link_id" => $trans_identifier, "mi_field" => "body"));
				$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->module_command,"mi_memo"=>$trans_summary,	"mi_link_id" => $trans_identifier, "mi_field" => "summary"));
				
				if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save",__LINE__,"[$sql]"));}
				$sql = "update page_data set page_admin_discussion='$page_admin_notes',  page_web_discussion='$page_web_notes' where page_client=$this->client_identifier and page_identifier='$page_id'";
				$result = $this->call_command("DB_QUERY",array($sql));
				$new_trans_id = $trans_identifier;
					/**
					* if we have successfully inserted a new version of this document into the system then
					- we want to tell the group module to update the version records or add new entries
					- to the database to define the groups that will be able to see the version.
					*/
				$this->set_access_to_page(Array("trans_id"=>$new_trans_id,"list"=>$trans_group),"group");
					/**
					* then do the same with menus
					*/
				if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_access_to_page",__LINE__,"[ :: set_access_to_page(".print_r(Array("trans_id"=>$new_trans_id,"list"=>$trans_menu_locations),true).",'menu');]"));}
				$this->set_access_to_page(Array("trans_id"=>$new_trans_id,"list"=>$trans_menu_locations),"menu");
					/**
					* and finally the same with files
					*/
				$this->set_access_to_page(Array("trans_id"=>$new_trans_id,"list"=>$file_assoc),"file");	
				if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
					$this->call_command("FILES_MANAGE_MODULE", 
						Array(
							"owner_module" 		=> "PAGE_SUMMARY",
							"label" 			=> $file_summary_label[0],
							"owner_id"			=> $new_trans_id,
							"file_identifier"	=> $file_summary_id[0]
						)
					);
				}
					/**
					* if we are to create a new version on every save then we do the following
					*/
				
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					// with the ability to publish to multiple locations link checking must take place on a save
					// if the page is published to one location and that changes 
					/*
					$tarray = split(",",str_replace(Array(" "), Array(""), $trans_menu_locations));
					$sql =  "select * from `embed_libertas_link` where dst_identifier = $new_trans_id and client_identifier=$this->client_identifier ";
					$result  = $this->call_command("DB_QUERY",Array($sql));
	                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
						if(in_array($r["menu_identifier"],$tarray)){
						} else {
							$sql = "select menu_url, menu_directory from menu_data where menu_identifier = ".$tarray[0]." and menu_client = $this->client_identifier";
							//print $sql;
							//$this->exitprogram();
							$resultmenu  = $this->call_command("DB_QUERY",Array($sql));
							$m_dir=-1;
	                        while($rmenu = $this->call_command("DB_FETCH_ARRAY",Array($resultmenu))){
	                        	$m_dir = $rmenu["menu_directory"];
	                        	$m_url = str_replace(Array("index.php"), Array(""), $rmenu["menu_url"]);
	                        }
	                        $this->call_command("DB_FREE",Array($resultmenu));
							$menu_dst_path	= $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($m_dir));
							$prev_title_uri = $this->make_uri($previous_page_title);
							$curr_title_uri = $this->make_uri($trans_title);
							$this->call_command("EMBED_FIX_PAGES",	
								Array(
									"page"					 		=> $r["dst_identifier"],
									"menu_source_identifier" 		=> $r["menu_identifier"],
									"menu_destination_identifier"	=> $m_dir,
									"replace" 						=> $r["destination_url"],
									"with" 							=> $m_url.$curr_title_uri.".php"
								)
							);
						}
	                }
	                $this->call_command("DB_FREE",Array($result));
					*/
				} else {
					if (strpos(" ".$prev_trans_menu_locations, " ".$trans_menu_locations.",")>0){
					} else {
						$sql = "select menu_directory, menu_identifier from menu_data where menu_identifier in ($prev_trans_menu_locations $trans_menu_locations)";
						$menu_result = $this->call_command("DB_QUERY",array($sql));
						$menu_src_id	= -1;
						$menu_src_path	= "";
						$menu_dst_id	= -1;
						$menu_dst_path	= "";
						while ($r = $this->call_command("DB_FETCH_ARRAY",array($menu_result))){
							if (" ".$r["menu_identifier"].","==$prev_trans_menu_locations){
								$menu_src_id	= $r["menu_identifier"];
								$menu_src_path	= $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["menu_directory"]));
							} else {
								$menu_dst_id	= $r["menu_identifier"];
								$menu_dst_path	= $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["menu_directory"]));
							}
						}
						$prev_title_uri = $this->make_uri($previous_page_title);
						$curr_title_uri = $this->make_uri($trans_title);
						if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"CALL ",__LINE__,"[EMBED_FIX_PAGES]"));}
						$this->call_command("EMBED_FIX_PAGES",	
							Array(
								"page"					 		=> $page_id,
								"menu_source_identifier" 		=> $menu_src_id,
								"menu_destination_identifier"	=> $menu_dst_id,
								"replace" 						=> $menu_src_path.$prev_title_uri.".php",
								"with" 							=> $menu_dst_path.$curr_title_uri.".php"
							)
						);
					}
				}
			}
			/**
			* On Adding a page we want to do one of the following 
			- 
			- 1. insert record into the page_data and retrieve the page_identifier
			- 2. insert record into the page_trans_data
			-
			*/
			if ($prev=="PAGE_ADD"){
				$now = $this->libertasGetDate("Y/m/d H:i:s");

				$url = $this->make_uri($trans_title).".php";
				$trans_doc_status = "1";
				$sql  = "insert into page_data ( page_date_creation, page_overall_status,page_client, page_created_by_user, page_admin_discussion, page_web_discussion) values ( '$now', 1, $this->client_identifier, $user, $page_admin_notes, $page_web_notes);";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save",__LINE__,"[$sql]"));
				}
				$result = $this->call_command("DB_QUERY",array($sql));
				$sql ="select * from page_data where page_client=$this->client_identifier and page_date_creation = '$now' and page_created_by_user = $user;";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save",__LINE__,"[$sql]"));
				}
				$result = $this->call_command("DB_QUERY",array($sql));
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$page_id = $r["page_identifier"];
				}
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					$extra_fields=",trans_doc_lock_to_user";
					$extra_value =",$user";
				} else {
					$extra_fields="";
					$extra_value ="";
				}
				
				if (($my_metadata_date_available=="-- :00:00") || ($my_metadata_date_available=="-- ::00") || ($my_metadata_date_available=="-- 00::00")){
					$my_metadata_date_available = $this->libertasGetDate("Y-m-d H:i:s");
				}
				if (($my_metadata_date_revalidate=="-- :00:00") || ($my_metadata_date_revalidate=="-- ::00") || ($my_metadata_date_revalidate=="-- 00::00")){
					$my_metadata_date_revalidate = NULL;
				}
				if (($my_metadata_date_remove=="-- :00:00") || ($my_metadata_date_remove=="-- ::00") || ($my_metadata_date_remove=="-- 00::00")){
					$my_metadata_date_remove = NULL;
				}
				$sql ="insert into page_trans_data (
							trans_dc_publisher,
							trans_dc_source,
							trans_dc_rights,
							trans_dc_rights_copyright,
							trans_dc_audience,
							trans_dc_doc_type,
							trans_dc_subject_category,
							trans_dc_subject_programme,
							trans_dc_subject_project,
							trans_dc_contributor,
							trans_dc_creator,
							trans_dc_coverage_place,
							trans_dc_coverage_postcode,
							trans_dc_coverage_time,
							trans_dc_alt_title,
							trans_page,
							trans_client,
							trans_title,
							trans_doc_status,
							trans_doc_author_identifier,
							trans_date_creation,
							trans_date_modified,
							trans_current_working_version,
							trans_doc_version_minor,
							trans_date_available,
							trans_date_review,
							trans_date_remove, 
							trans_dc_keywords, 
							trans_dc_url,
							trans_dc_subject_keywords
							$extra_fields
						) values (
							'$publisher_info',
							'$metadata_source',
							'$metadata_rights',
							'$metadata_rights_copyright',
							'$metadata_audience',
							'$metadata_doc_type',
							'$metadata_subject_category',
							'$metadata_subject_programme',
							'$metadata_subject_project',
							'$metadata_contributor',
							'$metadata_creator',
							'$metadata_coverage_place',
							'$metadata_coverage_postcode',
							'$metadata_coverage_time',
							'$trans_dc_alt_title',
							$page_id,
							$this->client_identifier,
							'$trans_title',
							$trans_doc_status,
							$user,
							'$now',
							'$now',
							1,
							'1',
							'$my_metadata_date_available',
							'$my_metadata_date_revalidate',
							'$my_metadata_date_remove',
							'$keys', 
							'$url',
							'$trans_dc_subject_keywords'
							$extra_value
						)";

				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result = $this->call_command("DB_QUERY",array($sql));
				$sql ="select trans_identifier from page_trans_data where trans_page = $page_id and trans_doc_author_identifier=$user and trans_date_creation = '$now'";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save",__LINE__,"[$sql]"));
				}
				$result = $this->call_command("DB_QUERY",array($sql));
				while ($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
					$new_trans_id = $r["trans_identifier"];
				}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->call_command("MEMOINFO_INSERT",array("mi_type"=>$this->module_command,"mi_memo"=>$trans_body,	"mi_link_id" => $new_trans_id, "mi_field" => "body"));
				$this->call_command("MEMOINFO_INSERT",array("mi_type"=>$this->module_command,"mi_memo"=>$trans_summary,	"mi_link_id" => $new_trans_id, "mi_field" => "summary"));
				/**
				* if we have successfully inserted a new version of this document into the system then
				- we want to update the records of the embedded information.
				*/
				$this->call_command("EMBED_SAVE_INFO",Array("list_of_results" => $list_of_embedded_information, 		"id"=>$new_trans_id, "editor"=>"body",		"module"=>$this->module_command, "previous_title"=>$previous_page_title));
				$this->call_command("EMBED_SAVE_INFO",Array("list_of_results" => $list_of_embedded_information_summary, "id"=>$new_trans_id, "editor"=>"summary",	"module"=>$this->module_command, "previous_title"=>$previous_page_title));
				/**
				* if we have successfully inserted a new version of this document into the system then
				- we want to tell the group module to update the version records or add new entries
				- to the database to define the groups that will be able to see the version.
				*/
				$this->set_access_to_page(Array("trans_id"=>$new_trans_id,"list"=>$trans_group),"group");
				$this->set_access_to_page(Array("trans_id"=>$new_trans_id,"list"=>$trans_menu_locations),"menu");
				$this->set_access_to_page(Array("trans_id"=>$new_trans_id,"list"=>$file_assoc),"file");			
				$this->call_command("FILES_MANAGE_MODULE", 
					Array(
						"owner_module" 		=> "PAGE_SUMMARY",
						"label" 			=> $file_summary_label,
						"owner_id"			=> $new_trans_id,
						"file_identifier"	=> $file_summary_id
					)
				);
				
			}
			$trans_identifier	= $new_trans_id;
	//		$trans_menu_locations = $this->check_parameters($parameters,"trans_menu_locations");
			$prev_trans_menu_locations = trim($this->check_parameters($parameters,"prev_trans_menu_locations"));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Link updater",__LINE__,"[". $previous_page_title ."], [". $trans_title ."], [ ". $trans_menu_locations .",], [ ". $prev_trans_menu_locations ."]"));
			}
			if (($previous_page_title != $trans_title) || (trim(" ".$trans_menu_locations.",") != trim($prev_trans_menu_locations))){
				/*
					update any records that require ro be updated  for new links
				*/
	//			print_r($parameters);
				if (strpos($trans_menu_locations,",")===false){
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Link updater",__LINE__,"[option 1 :: comma <strong>not</strong> found]"));
					}
					$tpath = $this->call_command("LAYOUT_GET_DIRECTORY_PATH_FROM_MENU",Array($trans_menu_locations));
					$t_menu = $trans_menu_locations;
				} else {
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Link updater",__LINE__,"[option 1 :: comma found]"));
					}
					$trans_menu_locations =split(",",$trans_menu_locations);
					$c = count($trans_menu_locations);
					for($i=0;$i<$c;$i++){
						$trans_menu_locations[$i] =trim($trans_menu_locations[$i]);
					}
					$tpath = $this->call_command("LAYOUT_GET_DIRECTORY_PATH_FROM_MENU",Array($trans_menu_locations[0]));
					$t_menu = $trans_menu_locations[0];
				}
				if ($prev_trans_menu_locations!=""){
					$list = split(",",$prev_trans_menu_locations);
					$mid = trim($list[0]);
					$prev_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH_FROM_MENU",Array($mid));
					$o_menu = $mid;
				} else {
					// orphaned pages
					$mid ="";
					$prev_path="";
					$o_menu=-1;
				}
				if ($o_menu!=-1){
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Link updater",__LINE__,"FIX ($mid = $prev_path, $tpath)"));
					}
					$this->call_command("EMBED_FIX",
						Array(
							"o_menu" => $o_menu,
							"n_menu" => $t_menu,
							"old" => $previous_page_title, 
							"current" => $trans_title,
							"op"=>$prev_path,
							"np"=>$tpath
						)
					);
				}
			} else {
				
			}
			$this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$trans_identifier, "editor"=> "body"));
			$this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$trans_identifier, "editor"=> "summary"));
			$sql ="update page_trans_data 
					set trans_current_working_version=0 
					where 
						trans_identifier!=$trans_identifier and trans_page = $page_id and trans_client=$this->client_identifier and trans_current_working_version=1";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY",array($sql));
			return $trans_identifier;
		}
	}
	
	
	function display_preview($parameters){
		$debug = $this->debugit(false,$parameters);
		$out="";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[".print_r($parameters,true)."]"));
		}
		$page_ids = Array();
		$page_id 	= $this->check_parameters($parameters,"unset_identifier",$this->check_parameters($parameters,"identifier",-1));
		$id			= "";
		$list		= "";
		$title		= "";
		$summary	= "";
		$body		= "";
		$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd1",
				"field_as"			=> "trans_body1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "body"
			)
		);
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary"
			)
		);		
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>".$parameters["command"]."</p>";
		if($parameters["command"]=="PAGE_PREVIEW"){
			$sql = "select 
						*, ".$body_parts["return_field"]." , ".$summary_parts["return_field"]." 
					from 
						page_data 
					inner join page_trans_data on page_identifier = trans_page
					 ".$body_parts["join"]." ".$summary_parts["join"]." 
					where 
						page_client=$this->client_identifier 
						".$body_parts["where"]." ".$summary_parts["where"]." and 
						page_trans_data.trans_identifier=".$page_id;
//			print "<p>$sql</p>";
			if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",Array($sql));
			$page_documents= Array();
			if ($result){
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$id							= $r["trans_page"];
					$title						= $r["trans_title"];
					$summary					= $r["trans_summary1"];
					$body						= $r["trans_body1"];
					$page_ids[count($page_ids)] = $id;
				}
				$result = $this->call_command("DB_FREE",Array($result));
			}
			$forms = $this->call_command("SFORM_LOAD_CACHE",Array("list_of_trans"=>$page_ids));
			$file_sql = "select * from file_access_to_page inner join file_info on file_info.file_identifier=file_access_to_page.file_identifier where trans_identifier=".$page_id;
			if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$file_sql</p>";
		}
//		print "<!-- ".$parameters["command"]." -->";
		if($parameters["command"]=="PAGE_PREVIEW_FORM"){
			$page_documents = Array();
			$id			= $this->check_parameters($parameters,"trans_identifier");
			$title		= $this->strip_tidy($this->check_parameters($parameters,"trans_title"));
//			$summary	= $this->strip_tidy(htmlentities($this->check_parameters($parameters,"trans_summary")));
//			$summary	= $this->check_editor($this->check_parameters($parameters,"trans_summary"));
//			$body 		= $this->check_editor($this->check_parameters($parameters,"trans_body"));
			$body						= $this->check_editor($parameters, "trans_body");
//			print $body;
			$summary					= $this->check_editor($parameters, "trans_summary");
			//			$this->fix_javascript(join("'",split("&#39;",$this->call_command("EDITOR_CONVERT_FONT_TO_SPAN", Array("string"=>$this->validate(htmlentities($this->tidy($this->check_parameters($parameters,"trans_body")))))))));
			
			$list_of_embedded_information	= $this->call_command("EMBED_EXTRACT_INFO",Array("str"=>$body));
			$list_of_forms 					= $this->check_parameters($list_of_embedded_information,"libertas_form",Array());
			$file_assoc						= $this->check_parameters($parameters,"file_associations","__NOT_FOUND__");
			if (($file_assoc != "__NOT_FOUND__") && ($file_assoc != "")){
				$file_sql 					= "select * from file_info where file_identifier in (".$file_assoc."-1)";
			} else {
				$file_sql 	= "";
			}
			$forms = $this->call_command("SFORM_LOAD_CACHE",Array("list_of_forms"=>$list_of_forms));
			$this->parent->print_first .="<script>
		try{
			if(parent.document){
				if (parent.document.all.preview_loaded.value==0){
					parent.document.all.preview.style.display='';
					parent.document.all.preview_loading.style.display='none';
				}
			}
		} catch (e){
			alert('Sorry currently unable to show preview screen');
		}
		</script>";
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__," :: [$file_sql]"));
		}
		$files="";
		if ($file_sql!=""){
			$result = $this->call_command("DB_QUERY",Array($file_sql));
			$page_documents= Array();
			if ($result){
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$file_id		 = $r["file_identifier"];
					$list .="$file_id, ";
				}
				$result = $this->call_command("DB_FREE",Array($result));
				if (strlen($list)>0){
					$files = $this->call_command("FILES_LIST_FILE_DETAIL",Array("file_associations"=> $list, "embed"=>false));
				}else{
					$files="";
				}
			}
		}
		$out = "<module name=\"".$this->module_name."\" display=\"ENTRY\">
					<page identifier=\"$id\">
						<title><![CDATA[".$title."]]></title>
						<summary><![CDATA[".$summary."]]></summary>
						<content><![CDATA[".$body."]]></content>
						<files>$files</files>
					</page>
				</module>". $forms;
		return $out;
	}
	
	function confirm_screen($identifier,$cmd){
		$label ="";
		$msg			= "";
		switch ($cmd) {
			case $this->module_command."REMOVE":
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					$label			= LOCALE_PAGE_REMOVE_LABEL;
					$page_status	= "<option value=\"__REMOVE__\">Just remove this version of this page</option><option value=\"__REMOVE_ALL_HISTORY__\">Remove all versions from system completely </option>";
//					$manage  = "<h1>Removing a Page?</h1><p>If you remove this document then you should be aware that the following pages have links to it, Confirming the removal of this page may cause these links to be broken.</p>";
//					$manage .= "<h1>Links in Description</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "body","direction"=>"in", "action" => "email_authors"));
//					$manage .= "<h1>Links in Summary</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "summary","direction"=>"in", "action" => "email_authors"));
//					$msg			= $manage;
					$status_type	= "SELECT";
					$msgbox			= 0;
				} else {
					$label			= LOCALE_PAGE_REMOVE_LABEL;
					if ($this->parent->server[LICENCE_TYPE]==MECM){
						$page_status	= "__REMOVE_ALL_HISTORY__";
					} else {
						$page_status	= "__REMOVE__";
					}
					$msg			= LOCALE_PAGE_REMOVE_CONFIRM;
					$status_type	= "INPUT";
					$msgbox			= 0;
				}
				break;
			case $this->module_command."PUBLISH":
				$label			= LOCALE_PAGE_CONFIRM_PUBLISH_LABEL;
				$page_status	= "__STATUS_PUBLISHED__";
				$msg			= LOCALE_PAGE_PUBLISH_ADD_NOTE;
//				$manage  = "<h1>Approving a Page?</h1><p>You have choosen to approve this page.<br />Below is a list of internal links if any, that this page has</p>";
//				$manage .= "<h1>Links in Description</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "body","direction"=>"out", "action" => ""));
//				$manage .= "<h1>Links in Summary</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "summary","direction"=>"out", "action" => ""));
//				$msg			= $manage;
				$status_type	= "INPUT";
				$msgbox			= 1;
				break;
			case $this->module_command."REJECT":
				$label			= LOCALE_PAGE_REJECT_DRAFT;
				$page_status	= "__STATUS_AUTHOR__";
				$msg			= LOCALE_PAGE_REJECT_DRAFT_ADD_NOTE;
				$status_type	= "INPUT";
				$msgbox			= 1;
				break;
			case $this->module_command."APPROVE":
				$label			= "Approve this version of the page?";
				$msg			= "";
				$page_status	= "__STATUS_APPROVED__";
//				$manage  = "<h1>Approving a Page?</h1><p>You have choosen to approve this page.<br />Below is a list of internal links if any, that this page has</p>";
//				$manage .= "<h1>Links in Description</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "body","direction"=>"out", "action" => ""));
//				$manage .= "<h1>Links in Summary</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "summary","direction"=>"out", "action" => ""));
//				$msg			= $manage;
				$status_type	= "INPUT";
				$msgbox			= 1;
				break;
			case $this->module_command."REWORK":
				$label			= "Rework this version of the page?";
				$page_status	= "__STATUS_AUTHOR__";
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					$msg			= "You are about to send this page back to an Author so that it can be editted.</p><p><strong>Are you sure about this?</strong></p><p>If you are sure please use the note box below to explain why this document needs to be changed";
				}
				$status_type	= "INPUT";
				$msgbox			= 1;
				break;
			case $this->module_command."UNPUBLISH":
				$label			= "Take this page off-line?";
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					$page_status	= "<option value=\"__STATUS_AUTHOR__\">Send Back to Authors Desk (Editable)</option>
										<option value=\"__STATUS_APPROVED__\">Keep on my publishers Desk (Not Editable)</option>
										<option value=\"__REMOVE__\">Remove from system completely</option>
										<option value=\"__REMOVE_ALL_HISTORY__\">Remove all versions from system completely</option>
										<option value=\"__REMOVE_REVERT__\">Unpublish this page and revert to the previous published version if available.</option>
										";
//										<option value=\"__STATUS_ARCHIVED__\">Place this page in the archive</option>

					$msg			= "";
//					$manage  = "<h1>Unpublishing a Page?</h1><p>If you unpublish this document then you should be aware that the following pages have links to it, Confirming the unpublish of this page may cause these links to be broken.</p>";
//					$manage .= "<h1>Links in Description</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "body","direction"=>"in", "action" => "email_authors"));
//					$manage .= "<h1>Links in Summary</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "summary","direction"=>"in", "action" => "email_authors"));
//					$msg			= $manage;
					$status_type	= "SELECT";
					$msgbox			= 1;
				} else {
					$page_status	= "__STATUS_AUTHOR__";
					$msg			= "";
					$status_type	= "INPUT";
					$msgbox			= 0;
				}
				break;
			case $this->module_command."UNARCHIVE":
				$label			= "Un-Archive this page?";
				$page_status	= "<option value=\"__STATUS_AUTHOR__\">Send Back to Authors Desk (Editable)</option><option value=\"__STATUS_APPROVED__\">Keep on my publishers Desk (Not Editable)</option><option value=\"__STATUS_ARCHIVED__\">Place this page in the archive</option><option value=\"__REMOVE__\">Remove from system completely</option>";
				$msg			= "You have choosen to Unarchive a document please choose what action should be taken with this document";
				$status_type	= "SELECT";
				$msgbox			= 1;
				break;
		   default:
		       break;
		}
		if (strlen($label)==0){
			/**
			* Cause the function to return a error.
			*/
			$result=false;
		} else {
			/**
			* Check to see if the page belongs to the client
			*/
			$sql = "select * from page_data inner join page_trans_data on page_trans_data.trans_page = page_data.page_identifier where page_client = $this->client_identifier and trans_identifier=$identifier";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
		}
		$out ="<module name=\"".$this->module_name."\" display=\"remove_form\">";
		if ($result){
			$out .="<form name=\"process_form\" label=\"$label\" width=\"100%\">";
			$out .="<input type=\"hidden\" name=\"comment_title\"><![CDATA[$label]]></input>";
			$out .="<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>";
			$out .="<input type=\"hidden\" name=\"command\"><![CDATA[".$cmd."_CONFIRM]]></input>";
			if ($status_type	== "INPUT"){
				$out .="<input type=\"hidden\" name=\"trans_doc_status\"><![CDATA[$page_status]]></input>";
			}else{
				$out .="<select label=\"What will I do with the page\" name=\"trans_doc_status\">$page_status</select>";
			}
			$out .="<text><![CDATA[$msg]]></text>";
			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				if ($msgbox	== 1){
					$out .= "<textarea required=\"YES\" name=\"page_comment\" label=\"Notes\" size=\"50\" height=\"10\" type=\"PLAIN-TEXT\"></textarea>";
				}
			}
			$out .="<input type=\"button\" iconify=\"CANCEL\" value=\"".ENTRY_NO."\" command=\"PAGE_LIST\"/>";
			$out .="<input type=\"submit\" iconify=\"YES\" 	  value=\"".ENTRY_YES."\"/>";
			$out .="</form>";
			$this->call_command("DB_FREE",array($result));
		} else {
			$out .= "<text><![CDATA[Sorry you do not have access to this $this->module_name]]></text>";
		}
		$out .="</module>";
		return $out;
	}

	function set_access_to_page($parameters,$table){
		$translation_identifier = $this->check_parameters($parameters,"trans_id",-1);
		$list		 			= $this->check_parameters($parameters,"list");
		$list_array = split(",",join("",split(" ",$list)));
		$rank_data=Array();
		if ($table=="menu"){
			if ($translation_identifier>-1){
				if ($this->parent->server[LICENCE_TYPE]!=ECMS){
					if (strlen($list)>0){
						$list.=",";	
					}
				}
				$sql = "delete from menu_access_to_page where menu_identifier not in ($list -1) and client_identifier=$this->client_identifier and trans_identifier=$translation_identifier";
				if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_menu_access_to_page",__LINE__,$sql));
				}
				$this->call_command("DB_QUERY",Array($sql));	
				$sql = "
				select * from 
					menu_access_to_page 
				where 
					menu_identifier in ($list -1) and 
					client_identifier = $this->client_identifier and 
					trans_identifier = $translation_identifier";
				$result = $this->call_command("DB_QUERY",Array($sql));	
				$num_results=$this->call_command("DB_NUM_ROWS",Array($result));
				if ($num_results>0){
					$found_list = Array();
					while ($r=$this->call_command("DB_FETCH_ARRAY",Array($result))){
						$found_list[count($found_list)] = $r["menu_identifier"];
					}
					for($index=0,$max=count($list_array);$index<$max;$index++){
						$found=-1;
						for($i=0,$m=count($found_list);$i<$m;$i++){
							if ($this->check_parameters($list_array,$index)==$found_list[$i]){
								$found = $index;
							}
						}
						if ($found==-1 && trim($this->check_parameters($list_array,$index))!=""){
							$found_list[count($found_list)] = $list_array[$index];
							$sql = "insert into menu_access_to_page (client_identifier, trans_identifier, menu_identifier, page_rank) values ($this->client_identifier, $translation_identifier, ".$list_array[$index].", -2)";
							$this->call_command("DB_QUERY",Array($sql));	
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_menu_access_to_page",__LINE__,$sql));
							}			
							$sql = "select * from menu_access_to_page where menu_identifier=".$list_array[$index];
							$result = $this->call_command("DB_QUERY",Array($sql));	
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_menu_access_to_page",__LINE__,$sql));
							}	
							$num = $this->call_command("DB_NUM_ROWS",Array($result));
							$rank_data["current_rank"]	= $translation_identifier."_-2";
							$rank_data["set_title"]	= 0;
							$rank_data["num"]	= $num;
							if ($num==1){
								$rank_data["new_rank"]	= 1;
								$rank_data["set_title"]	= 1;
							}else{
								$rank_data["new_rank"]	= 2;
							}
							$rank_data["menu_identifier"] = $list_array[$index];
							$rank_data["NO_REDIRECT"] = 1;
							$this->call_command("LAYOUT_HIDE_SET_PAGE_RANKING",$rank_data);
						}
					}	
				}else {
					for($index=0,$max=count($list_array);$index<$max;$index++){
						if ($this->check_parameters($list_array,$index)!=""){
							$sql = "insert into ".$table."_access_to_page (client_identifier, trans_identifier, ".$table."_identifier, page_rank) values ($this->client_identifier, $translation_identifier, ".$list_array[$index].", -2)";
							$this->call_command("DB_QUERY",Array($sql));	
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_menu_access_to_page",__LINE__,$sql));
							}			
							$sql = "select * from menu_access_to_page where menu_identifier=".$list_array[$index];
							$result = $this->call_command("DB_QUERY",Array($sql));	
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_menu_access_to_page",__LINE__,$sql));
							}	
							$num = $this->call_command("DB_NUM_ROWS",Array($result));
							$rank_data["current_rank"]	= $translation_identifier."_-2";
							$rank_data["set_title"]	= 0;
							$rank_data["num"]	= $num;
							if ($num==1){
								$rank_data["new_rank"]	= 1;
								$rank_data["set_title"]	= 1;
							}else{
								$rank_data["new_rank"]	= 2;
							}
							$rank_data["menu_identifier"] = $list_array[$index];
							$rank_data["NO_REDIRECT"] = 1;
							$this->call_command("LAYOUT_HIDE_SET_PAGE_RANKING",$rank_data);
						}
					}
				}
			}
		} else if ($table=="file"){
			if ($translation_identifier>-1){
				$sql = "delete from file_access_to_page where client_identifier=$this->client_identifier and trans_identifier=$translation_identifier";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_menu_access_to_page (file)",__LINE__,$sql));
				}
				$this->call_command("DB_QUERY",Array($sql));	
				$file_rank=1;
				for($index=0,$max=count($list_array);$index<$max;$index++){
					if (!empty($list_array[$index])){
						$sql = "insert into file_access_to_page (client_identifier, trans_identifier, file_identifier, file_rank) values ($this->client_identifier, $translation_identifier, ".$list_array[$index].", $file_rank)";
						$this->call_command("DB_QUERY",Array($sql));	
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_menu_access_to_page",__LINE__,$sql));
						}
						$file_rank++;
					}
				}
			}
		}else{
			if ($translation_identifier>-1){
				$sql = "delete from ".$table."_access_to_page where client_identifier=$this->client_identifier and trans_identifier=$translation_identifier";
				if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_menu_access_to_page",__LINE__,$sql));
				}
				$this->call_command("DB_QUERY",Array($sql));	
				for($index=0,$max=count($list_array);$index<$max;$index++){
					if (!empty($list_array[$index])){
						$sql = "insert into ".$table."_access_to_page (client_identifier, trans_identifier, ".$table."_identifier) values ($this->client_identifier, $translation_identifier, ".$list_array[$index].")";
						$this->call_command("DB_QUERY",Array($sql));	
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_menu_access_to_page",__LINE__,$sql));
						}			
					}
				}
			}
		}
	}
	function copy_access_to_page($parameters,$table){
		$from_translation_identifier 	= $this->check_parameters($parameters,"from_trans_id",-1);
		$to_translation_identifier		= $this->check_parameters($parameters,"to_trans_id",-1);
		$user 							= $_SESSION["SESSION_USER_IDENTIFIER"];	
		
		$sql = "select distinct * from ".$table."_access_to_page where trans_identifier  = $from_translation_identifier and client_identifier = $this->client_identifier;";
		$copy_result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_access_to_page",__LINE__,"[$copy_result :: $sql]"));
		}	
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($copy_result))){
			if ($table=="menu"){
				$extra_values = ", ".$r["page_rank"].", ".$r["title_page"];
				$extra_fields = ", page_rank, title_page";
			} else if ($table=="file"){
				$extra_values = ", ".$r["file_rank"];
				$extra_fields = ", file_rank";
			} else {
				$extra_values = "";
				$extra_fields = "";
			}
			$sql ="insert into ".$table."_access_to_page (trans_identifier, client_identifier, ".$table."_identifier $extra_fields) values ($to_translation_identifier, $this->client_identifier, ".$r[$table."_identifier"]."$extra_values)";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_access_to_page",__LINE__,"[$copy_result :: $sql]"));
			}	
			$this->call_command("DB_QUERY",array($sql));
		}
	}
	function create_new_version($trans_identifier,$destination_major=-1,$destination_minor=-1){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"create_new_version",__LINE__," $trans_identifier, $destination_major, $destination_minor"));}
		$sql = "SELECT * FROM page_trans_data where trans_identifier = $trans_identifier and trans_client=$this->client_identifier;";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"create_new_version",__LINE__,"[$sql]"));
		}
		$fields = "";
		$values = "";
		$retrieve ="";
		$counter=0;
		if($this->call_command("DB_NUM_ROWS",Array($result))>0){
			$r = $this->call_command("DB_FETCH_ARRAY",Array($result));
			foreach ($r as $key => $val){
				if($key=="trans_page"){
					$trans_page = $val;
				}
				if($key=="trans_language"){
					$trans_language = $val;
				}
				if (is_int($key)){
				}else{
					if ($key=="trans_identifier"){
					}else if ($key=="trans_current_working_version"){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						if ($destination_major!=-1){
							$values .= "'1'";
							$retrieve .="$key = '1'";
						}else{
							$values .= "'".$val."'";
							$retrieve .="$key = '".$val."'";
						}
						$counter++;
					}else if ($key=="trans_doc_version_major"){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						if ($destination_major!=-1){
							$values .= "'$destination_major'";
							$retrieve .="$key = '$destination_major'";
						}else{
							$values .= "'".$val."'";
							$retrieve .="$key = '".$val."'";
						}
						$counter++;
					}else if ($key=="trans_doc_version_minor"){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						if ($destination_minor!=-1){
							$values .= "'$destination_minor'";
							$retrieve .="$key='$destination_minor'";
						}else{
							$values .= "'".($val+1)."'";
							$retrieve .="$key='".($val+1)."'";
						}
						$counter++;
					} else if ($key=="trans_doc_status"){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						$values .= "'1'"; // set document back to authors desk if new version created
						$retrieve .="$key='".$this->validate($val)."'";
						$counter++;
					} else if ($key=="trans_body" || $key=="trans_title" || $key=="trans_summary"){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						$values .= "'$val'"; // set document back to authors desk if new version created
						$retrieve .="$key='".$val."'";
						$counter++;
					} else {
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						$values .= "'".$this->validate($val)."'";
						$retrieve .="$key = '".$this->validate($val)."'";
						$counter++;
					}
				}
			}
			$sql 	= "insert into page_trans_data ($fields) values ($values);";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"create_new_version",__LINE__,"[$result :: $sql]"));
			}	
			$sql 	= "select max(trans_identifier) as trans_identifier from page_trans_data where trans_page='$trans_page' and trans_client='$this->client_identifier' and trans_language='$trans_language'";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$tresult = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"create_new_version",__LINE__,"[$tresult :: $sql]"));
			}	
			while ($trans 	= $this->call_command("DB_FETCH_ARRAY",Array($tresult))){
				$new_trans_id = $trans["trans_identifier"];
			}
			$sql ="update page_trans_data 
					set trans_current_working_version=0 
					where 
						trans_identifier=$trans_identifier and 
						trans_current_working_version=1";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY",array($sql));
			$this->copy_access_to_page(Array("from_trans_id" => $trans_identifier,"to_trans_id" => $new_trans_id),"group");
			$this->copy_access_to_page(Array("from_trans_id" => $trans_identifier,"to_trans_id" => $new_trans_id),"menu");
			$this->copy_access_to_page(Array("from_trans_id" => $trans_identifier,"to_trans_id" => $new_trans_id),"file");
			$this->call_command("FILES_TO_OBJECT_COPY", Array("from_id" => $trans_identifier,"to_id" => $new_trans_id,"module"=>"PAGE_SUMMARY"));
			$this->call_command("EMBED_COPY_INFO",Array("from_trans_id" => $trans_identifier,"to_trans_id" => $new_trans_id));
			$this->call_command("MEMOINFO_COPY_INFO",Array("mi_type" => $this->module_command, "old_link_id" => $trans_identifier,"new_link_id" => $new_trans_id));
			return $new_trans_id;
		} else {
			return $trans_identifier;
		}
	}

	function clone_new_version($trans_identifier,$destination_major=-1,$destination_minor=-1){
		$sql = "SELECT * FROM page_trans_data where trans_identifier = $trans_identifier and trans_client=$this->client_identifier;";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"create_new_version",__LINE__,"[$sql]"));
		}
		$fields = "";
		$values = "";
		$retrieve ="";
		$counter=0;
		if($this->call_command("DB_NUM_ROWS",Array($result))>0){
			$r = $this->call_command("DB_FETCH_ARRAY",Array($result));
			foreach ($r as $key => $val){
				if($key=="trans_page"){
					$trans_page = $val;
				}
				if($key=="trans_language"){
					$trans_language = $val;
				}
				if (is_int($key)){
				}else{
					if ($key=="trans_identifier"){
					/**
                    * if cloning in progress then overide settings
                    */
					}else if ($key=="trans_page" && $destination_major==-2 ){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						$values .= "'$destination_minor'";
						$retrieve .="$key = '$destination_minor'";
						$counter++;
					}else if ($key=="trans_current_working_version"){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						if ($destination_major>-1){
							$values .= "'1'";
							$retrieve .="$key = '1'";
						}else{
							$values .= "'".$val."'";
							$retrieve .="$key = '".$val."'";
						}
						$counter++;
					}else if ($key=="trans_doc_version_major"){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						if ($destination_major != -2){
							$fields .= ", trans_doc_version_minor";
							$values .= "'0','1'";
							$retrieve .="$key = '0' and trans_doc_version_minor = '1'";
						} else if ($destination_major != -1){
							$values .= "'$destination_major'";
							$retrieve .="$key = '$destination_major'";
						} else {
							$values .= "'".$val."'";
							$retrieve .="$key = '".$val."'";
						}
						$counter++;
					}else if ($key=="trans_doc_version_minor" && $destination_major!=-2){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						if ($destination_minor>-1){
							$values .= "'$destination_minor'";
							$retrieve .="$key='$destination_minor'";
						}else{
							$values .= "'".($val+1)."'";
							$retrieve .="$key='".($val+1)."'";
						}
						$counter++;
					} else if ($key=="trans_doc_status"){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						$values .= "'1'"; // set document back to authors desk if new version created
						$retrieve .="$key='".$this->validate($val)."'";
						$counter++;
					} else if ($key=="trans_title"){
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						if ($destination_major==-2){
							$fields .= "$key";
							$values .= "'$val (Clone)'"; 
							$retrieve .="$key='".$val." (Clone)'";
						}else{
							$fields .= "$key";
							$values .= "'$val'"; 
							$retrieve .="$key='".$val."'";
						}
						$counter++;
					} else {
						if ($counter>0){
							$fields .= ", ";
							$values .= ", ";
							$retrieve .=" and ";
						}
						$fields .= "$key";
						$values .= "'".$this->validate($val)."'";
						$retrieve .="$key = '".$this->validate($val)."'";
						$counter++;
					}
				}
			}
			$sql 	= "insert into page_trans_data ($fields) values ($values);";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"create_new_version",__LINE__,"[$result :: $sql]"));}	
			
			if ($destination_major==-2 ){
				$sql 	= "select max(trans_identifier) as trans_identifier from page_trans_data where trans_page='$destination_minor' and trans_client='$this->client_identifier' and trans_language='$trans_language'";
			}else {
				$sql 	= "select max(trans_identifier) as trans_identifier from page_trans_data where trans_page='$trans_page' and trans_client='$this->client_identifier' and trans_language='$trans_language'";
			}
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$tresult = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"create_new_version",__LINE__,"[$tresult :: $sql]"));
			}	
			while ($trans 	= $this->call_command("DB_FETCH_ARRAY",Array($tresult))){
				$new_trans_id = $trans["trans_identifier"];
			}
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"$new_trans_id"));}
			$this->copy_access_to_page(Array("from_trans_id" => $trans_identifier,"to_trans_id" => $new_trans_id),"group");
			$this->copy_access_to_page(Array("from_trans_id" => $trans_identifier,"to_trans_id" => $new_trans_id),"menu");
			$this->copy_access_to_page(Array("from_trans_id" => $trans_identifier,"to_trans_id" => $new_trans_id),"file");
			$this->call_command("FILES_TO_OBJECY_COPY", Array("from_id" => $trans_identifier,"to_id" => $new_trans_id,"module"=>"PAGE_SUMMARY"));
			$this->call_command("EMBED_COPY_INFO",Array("from_trans_id" => $trans_identifier,"to_trans_id" => $new_trans_id));
			$this->call_command("MEMOINFO_COPY_INFO",Array("mi_type" => $this->module_command, "old_link_id" => $trans_identifier,"new_link_id" => $new_trans_id));
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"destination_major",__LINE__,"$destination_major"));}
			if ($destination_major!=-2){
				$sql ="update page_trans_data 
						set trans_current_working_version=0 
						where 
							trans_identifier=$trans_identifier and 
							trans_current_working_version=1";
				$tresult = $this->call_command("DB_QUERY",array($sql));
			}
			return $new_trans_id;
		} else {
			return $trans_identifier;
		}
	}
	
	
	function optionise_metadata_choice($list,$select_me=Array()){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"optionise_metadata_choice",__LINE__,$list.",".$select_me));
		}
		$out = "";
		$entries = split("\n",join("",split("\r",$list)));
		$length = count($entries);
		$max = count($select_me);
		for($index=0;$index<$length;$index++){
			if ($entries[$index]!=""){
				$value=$entries[$index];
				$selected="";
				for($i=0;$i<$max;$i++){
					if (is_array($select_me)){
						if ($value==$this->check_parameters($select_me,$i,"-1")){
							$selected="selected=\"true\"";
						}
					}
				}
				$out .= "<option value=\"$value\" $selected>$value</option>";
			}
		}
		return $out;
	}
	
	function send_approver($parameters){
		$trans_doc_status = $this->module_constants["__STATUS_READY__"];
		$identifier = $parameters["identifier"];
		$sql ="update page_trans_data set trans_doc_status=$trans_doc_status, trans_doc_lock_to_user=0 where trans_identifier=".$identifier." and trans_client=$this->client_identifier";
		
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_PAGE_APPROVER__"], "identifier" => $identifier));
		$this->call_command("ENGINE_REFRESH_BUFFER",array("command=PAGE_LIST"));
	}
	
	
	function extract_vesions($parameters){
		/**
		* this function will allow the user to access previously archived versions of documents in the system.
		*
		* the user will be given a list of page titles which they can then select to view archived version of the document
		* the title of the document will be the current working version.
		* entries will only be displayed if there are move than one document with the same trans_page field.  
		* in other words you can not restore a version less than v0.1 cause it is the first of the list
		*/	
		$variables=Array();
		$sql= "select 
					t1.trans_page,  
					t1.trans_title,
					count(t2.trans_page) number_of_versions
				from page_trans_data as t1 
					left outer join page_trans_data as t2 on t1.trans_page = t2.trans_page
				where 
					t1.trans_current_working_version = 1 and
					t1.trans_client = $this->client_identifier
				group by 
					t2.trans_page";
		$result = $this->call_command("DB_QUERY", array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"extract_vesions",__LINE__,$sql));
		}
		$success = $this->check_parameters($parameters,"successful");
		if ($success==1){
			$variables["FILTER"]		= "<form label=\"LOCALE_EXTRACTION_MSG_BOX\"><text><![CDATA[".LOCALE_EXTRACTION_MSG_CONTENT."]]></text></form>";
		} else {
			$variables["FILTER"]		= "";
		}
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		$variables["PAGE_COMMAND"]		= "PAGE_LIST_VERSIONS";
		$variables["PAGE_BUTTONS"] 		= Array(Array("CANCEL","PAGE_VERSION_ARCHIVE_ACCESS","LOCALE_CANCEL",""));
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
			}
			if ($goto+$this->page_size>$number_of_records){
				$finish = $number_of_records;
			}else{
				$finish = $goto+$this->page_size;
			}
			$goto++;
			$page++;
			
			$num_pages=floor($number_of_records / $this->page_size);
			$remainder = $number_of_records % $this->page_size;
			if ($remainder>0){
				$num_pages++;
			}
			
			$counter=0;
		
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			
			$start_page=intval($page / $this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+$this->page_size)>$num_pages){
				$end_page	 =	$num_pages;
			}else{
				$end_page	=	$this->page_size;
			}
			
			$variables["END_PAGE"]			= $end_page;
			
			$variables["ENTRY_BUTTONS"] =Array();
			$variables["ENTRY_BUTTONS"][count($variables["ENTRY_BUTTONS"])]	=	Array("LIST_VERSIONS","PAGE_LIST_VERSIONS",ENTRY_LIST_VERSIONS);
			$variables["RESULT_ENTRIES"] =Array();
			$c=0;
			while (($r=$this->call_command("DB_FETCH_ARRAY", array($result))) && ($c<$this->page_size)){
				$c++;
				$i = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$i]=Array(
					"identifier"	=> $r["trans_page"],
					"attributes"	=> Array(
						Array(ENTRY_TITLE,$r["trans_title"],"TITLE"),
						Array(LOCALE_NUMBER_OF_VERSIONS, $r["number_of_versions"])
					)
				);
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}
	
	
	function page_copy($parameters){
		$debug = $this->debugit(false,$parameters);
		$next_command = $this->check_parameters($parameters,"next_command","__NOT_DEFINED__");
		$id = $this->check_parameters($parameters,"identifier",-1);
		$page_id=-1;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"page_copy",__LINE__,$id));
		}
		if ($id!=-1){
			$sql ="select 
						t1.trans_doc_version_major as ver_max,
						t1.trans_doc_version_minor as ver_min,
						t1.trans_page as page_id
					from page_trans_data as t1
						inner join page_trans_data as t2 on t1.trans_page = t2.trans_page
					where 
						t1.trans_client = $this->client_identifier and 
						t2.trans_identifier = $id
					order by 
						t1.trans_doc_version_major desc,
						t1.trans_doc_version_minor desc";
			//print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"page_copy",__LINE__,$sql));
			}
			$result = $this->call_command("DB_QUERY", array($sql));
			$max_major = -1;
			$max_minor = -1;
			$c=0;
			while (($r=$this->call_command("DB_FETCH_ARRAY", array($result))) && ($c<1)){
				$max_major	= $r["ver_max"];
				$page_id	= $r["page_id"];
				$max_minor	= $r["ver_min"] + 1 ;
				$c++;
			}
			
			if (($max_major != -1 ) &&($max_minor != -1)){
				$new_trans_id = $this->create_new_version($id, $max_major, $max_minor);
				if ($next_command == "__NOT_DEFINED__"){
					$sql = "update page_trans_data set trans_current_working_version = 0 where trans_client=$this->client_identifier and trans_page = $page_id";
					$this->call_command("DB_QUERY", array($sql));
					$sql = "update page_trans_data set trans_current_working_version = 1 where trans_client=$this->client_identifier and trans_identifier = $new_trans_id";
					$this->call_command("DB_QUERY", array($sql));
				}
			}
			return $new_trans_id;
		}
	}
	function generate_reports($report){
		/**
		* this function will allow the user to access previously archived versions of documents in the system.
		-
		* the user will be given a list of page titles which they can then select to view archived version of the document
		* the title of the document will be the current working version.
		* entries will only be displayed if there are move than one document with the same trans_page field.  
		* in other words you can not restore a version less than v0.1 cause it is the first of the list
		*/	
		$where = "";
		$join="";
		$order_by="";
		$status =array();
		$_SESSION["SESSION_USER_LANGUAGE"]="en";
		$lang_of_choice = $_SESSION["SESSION_USER_LANGUAGE"];
		$out ="";	
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		if($report=="status"){
		$sql = "
			select 
				trans_doc_status,
				(to_days(trans_date_review) - to_days('$now')) as review,
				(to_days(trans_date_available) - to_days('$now')) as available,
				(to_days(trans_date_remove) - to_days('$now')) as removed
			from page_trans_data 
			where 
				trans_published_version = 1 and trans_client = $this->client_identifier
		";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result 			= $this->call_command("DB_QUERY", array($sql));
		$authors_desk 		= 0;
		$approvers_desk 	= 0;
		$publishers_desk 	= 0;
		$published_desk 	= 0;
		$archived_desk 		= 0;
		$available 			= 0;
		$removed 			= 0;
		$review_one_week	= 0;
		$review_one_month	= 0;
		$review_one_passed	= 0;
		while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
			$review_status = $this->check_parameters($r,"review","IGNORE");
			$available_status = $this->check_parameters($r,"available",0);
			$removed_status = $this->check_parameters($r,"removed",0);
			if ($review_status!="IGNORE"){
				if ($review_status<0){
					$review_one_passed++;
				} else if ($review_status>0 && $review_status<7){
						$review_one_week++;
				} else if ($review_status>=7 && $review_status<30){
					$review_one_month++;
				}
			}
			if ($available_status>0){
				$available++;
			}
			if ($removed_status<0){
				$removed++;
			}
			if ($r["trans_doc_status"]==1){
				$authors_desk++;
			}
			if ($r["trans_doc_status"]==2){
				$approvers_desk++;
			}
			if ($r["trans_doc_status"]==3){
				$publishers_desk++;
			}
			if ($r["trans_doc_status"]==4){
				$published_desk++;
			}
			if ($r["trans_doc_status"]==5){
				$archived_desk++;
			}
		}
			$out = "<module name=\"page\" display=\"form\">
			<page_options>".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","PAGE_LIST",LOCALE_CANCEL)) . "<header>" . LOCALE_PAGE_BASIC_REPORTS . "</header></page_options>
			<form label=\"Workflow Status Report\"><text><![CDATA[
			<p><strong>Documents and their locations </strong><br>
			The authors desk has $authors_desk document(s) being worked on.<br>";
			if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
				$out .="The approvers desk has $approvers_desk document(s).<br>
				There are $publishers_desk document(s) waiting to be published.<br>";
			}
			$out .="There are $published_desk document(s) published to the site.<br>";
			if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
				$out .="
				<p><strong>Document Activity</strong><br>
				There are $available document(s) waiting specific dates to become available<br>
				There are $removed document(s) past their remove dates<br>
				There are $review_one_week document(s) up for review in the next week<br>
				There are $review_one_month document(s) up for review in the next month<br>
				There are $review_one_passed document(s) currently passed their review dates<br>
			";
			}
			$out.="</p>
			]]></text></form></module>";
		} 
		if ($report=="site_content"){
			$sql ="select 
				menu_data.menu_label,
				menu_data.menu_url,
				menu_data.menu_identifier,
				menu_data.menu_parent,
				menu_data.menu_headline
			from menu_data
			where 
				menu_client=$this->client_identifier
			order by 
				menu_parent, 
				menu_order";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$list = Array();
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$list[$r["menu_identifier"]] = Array(
					"parent" 		=> $r["menu_parent"],
					"Menu" 			=> $r["menu_label"],
					"URL" 			=> $r["menu_url"],
					"Headline" 		=> $r["menu_headline"],
					"Pages" 		=> 0,
					"Last Update"	=> "[[nbsp]]",
					"Updated By"	=> "[[nbsp]]",
					"Access"		=> Array(),
					"uid"			=> ""
				);
			}
			$sql = "
			select  
				user_info.user_identifier, 
				contact_data.contact_first_name, contact_data.contact_last_name, 
				user_info.user_login_name, 
				page_trans_data.trans_doc_author_identifier,
				menu_data.menu_label,
				menu_data.menu_url,
				menu_data.menu_identifier,
				menu_data.menu_parent,
				menu_data.menu_headline,
				 count(trans_page) as total_pages , 
				max(trans_date_publish) as last_update 
			from menu_data
				inner join menu_access_to_page on menu_access_to_page.menu_identifier = menu_data.menu_identifier and client_identifier = menu_data.menu_client
				inner join page_trans_data on (menu_access_to_page.trans_identifier = page_trans_data.trans_identifier and trans_client=client_identifier and trans_doc_status=4 and trans_published_version=1 ) 
				left outer join user_info on user_info.user_identifier = page_trans_data.trans_doc_author_identifier 
				left outer join contact_data on contact_data.contact_user = user_info.user_identifier
			where 
				menu_client=$this->client_identifier  
			group by 
				menu_access_to_page.menu_identifier 
			order by 
				menu_parent, 
				menu_order
			";
			
			$result  = $this->call_command("DB_QUERY",Array($sql));
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$name_f		= $this->check_parameters($r,"contact_first_name");
				$name_l 	= $this->check_parameters($r,"contact_last_name");
				$name_usr	= $this->check_parameters($r,"user_login_name");
				$contact="[[nbsp]]";
				$access	="Public";
				if($name_f=="" && $name_l==""){
					$contact = $name_usr;
				} else {
					$contact = $name_f." ".$name_l;
				}
				$list[$r["menu_identifier"]]["Pages"]=$r["total_pages"];
				$list[$r["menu_identifier"]]["Last Update"]=$this->check_parameters($r,"last_update","[[nbsp]]");
				$list[$r["menu_identifier"]]["Updated By"]=$contact;
				$list[$r["menu_identifier"]]["uid"]=$this->check_parameters($r,"user_identifier",-1);
			}


			$sql="select 
					menu_data.menu_label,
					menu_data.menu_url,
					menu_data.menu_identifier,
					menu_data.menu_parent,
					menu_data.menu_headline,
					group_label
				from menu_data
					inner join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier
					inner join group_data on relate_menu_groups.group_identifier = group_data.group_identifier
				where 
					menu_client=$this->client_identifier
				order by 
					menu_parent, 
					menu_order";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$list[$r["menu_identifier"]]["Access"][count($list[$r["menu_identifier"]]["Access"])] = $r["group_label"];
			}
			$tablelist = $this->generate_menu_table($list,-1,1);
	//		print_r($list);
			$this->call_command("DB_FREE",Array($result));
	
			$out = "<module name=\"page\" display=\"form\">
			<page_options>".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","PAGE_LIST",LOCALE_CANCEL)) . "<header>" . LOCALE_PAGE_BASIC_REPORTS . "</header></page_options>
			<form label=\"Site last updated\"><text><![CDATA[
			<table class='sortable' cellspacing='0' cellpadding='2' width='100%'>
			<tr>
				<th style='width:30px'>#</th>
				<th style='text-align:left;'>Menu</th>
				<th style='text-align:left;'>Location</th>
				<th style='text-align:left;width:60px'>Pages</th>
				<th style='text-align:left;width:80px'>Headline</th>
				<th style='text-align:left;width:140px'>Last update</th>
				<th style='text-align:left;width:115px'>Updated By</th>
				<th style='text-align:left;width:80px'>Access</th>
			</tr>
			".$tablelist[0]."</table>
			]]></text></form></module>";
		}
		return $out;
	}

	function generate_menu_table($l,$parent,$index){
		$m=count($l);
		$sRow ="";
		foreach($l as $key => $val){
			if($val["parent"]==$parent){
				if($val["Headline"]==1){
					$headline ="Yes";
				} else{
					$headline ="No";
				}
				if(count($val["Access"])==0){
					$access= "Public";
				} else {
					$access= "<span title='Restricted to : \n - ".join("\n - ",$val["Access"])."'>Secure</span>";
				}
				$sRow .= "<tr>
					<td>".$index."</td>
					<td><a href='admin/index.php?command=PAGE_LIST&amp;menu_location=".$key."' title='Show Administrative view of pages currently in this menu location'>".$val["Menu"]."</a></td>
					<td><a href='".$val["URL"]."' title='open this location on the web site' target='_external'>".$this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL",Array("id"=>$key))."</a></td>
					<td>".$val["Pages"]."</td>
					<td>".$headline."</td>
					<td>".$val["Last Update"]."</td>
					<td>".$val["Updated By"]."</td>
					<td>".$access."</td>
				</tr>";
				$index++;
				$out = $this->generate_menu_table($l,$key,$index);
				$sRow .= $out[0];
				$index = $out[1];
			}
		}
		return Array($sRow,$index);
	}
	function regenerate_cache($parameters=Array()){
		$page 			= intval($this->check_parameters($parameters,"page","1"));
		$list 			= $this->check_parameters($_SESSION,"RECACHE");
		$page_list		= $this->check_parameters($parameters,"page_list");
		$auto_summarise = intval($this->check_parameters($parameters,"auto_summarise","0"));
		if (strlen($list)>0){
			$sql  		= "
				select 
					page_trans_data.trans_identifier 
				from 
					page_trans_data 
				inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
				where 
					page_trans_data.trans_doc_status = 4 and 
					page_trans_data.trans_client=$this->client_identifier and 
					page_trans_data.trans_published_version=1 and 
					menu_access_to_page.menu_identifier in ($list -2)
				order by 
					page_trans_data.trans_identifier asc";
		}else{
			if (strlen($page_list)>0){
				$sql  		= "
					select 
						page_trans_data.trans_identifier 
					from 
						page_trans_data 
					where 
						page_trans_data.trans_doc_status = 4 and 
						page_trans_data.trans_client=$this->client_identifier and 
						page_trans_data.trans_published_version=1 and 
						page_trans_data.trans_page in ($page_list -2)
					order by 
						page_trans_data.trans_identifier asc";
			}else{
				$sql  		= "
					select 
						page_trans_data.trans_identifier 
					from 
						page_trans_data 
					where 
						page_trans_data.trans_doc_status = 4 and 
						page_trans_data.trans_client=$this->client_identifier and 
						page_trans_data.trans_published_version=1
					order by 
						page_trans_data.trans_identifier asc";
			}
		}
		//print $sql;
		//$this->exitprogram();
		$size 		= 30;
		$result 	= $this->call_command("DB_QUERY", array($sql));
		$num_rows 	= $this->call_command("DB_NUM_ROWS", array($result));
		$goto = (($page-1)*$size);
		if (($goto!=0) && ($goto<$num_rows)){
			$pointer = $this->call_command("DB_SEEK",array($result,$goto));
		}
		$c=0;
		if ($num_rows>$goto){
			while (($r=$this->call_command("DB_FETCH_ARRAY", array($result))) && ($c<$size)){
				$this->cache_this_page(intval($r["trans_identifier"]),0,$auto_summarise);
				$c++;
			}
			$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=PAGE_REGENERATE_CACHE&auto_summarise=$auto_summarise&".SID."&list=$list&page_list=$page_list&page=".($page+1)));
		} else {
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=ENGINE_RESTORE"));
		}
	}
	
	function translate_to_filename($url,$title,$id,$menu_id){
		$root				= $this->parent->site_directories["ROOT"];
		$dir 				= dirname($root."/".$url);
		$filename		 	= $this->make_uri($title).".php";
		$directories 		= split('/',$url);
		$directorycount		= count($directories)-1;
		$directory_to_root	= "";
		
		if ($directorycount>0){
 			for($index=0;$index<$directorycount;$index++){
				$directory_to_root .= "../";
			}
		}
		$fp = fopen($dir."/".$filename, 'w');
		$module_directory = $this->check_parameters($this->parent->site_directories,"MODULE_DIR",$directory_to_root);
		fwrite($fp, "<"."?php\r\n\$identifier=$id;\r\n\$script=\"$url\";\r\n\$command=\"PRESENTATION_DISPLAY\";\r\nrequire_once \"".$root."/admin/include.php\"; \r\nrequire_once \"".$module_directory."/included_page.php\"; \r\n?".">");
		fclose($fp);
		$um = umask(0);
		chmod($dir."/".$filename, LS__FILE_PERMISSION);
		umask($um);
		
		$pos =strlen($root);
		if ($pos+1 < strlen($dir)){
			$pos++;
		}
		$url = substr($dir,$pos);
		if ($url==""){
			return $filename;
		}else{
			return $url."/".$filename;
		}
	}
	
	function discussion_available($cmd,$parameters){
		if ($cmd!=""){
			$id		= $this->check_parameters($parameters,"identifier",0);
			$entry_type	= strtolower($this->check_parameters($parameters,"entry_type",""));
			if ($cmd=="PAGE_DISCUSSION_ENABLE"){
				$sql ="update page_data set page_".$entry_type."_discussion=1 where page_identifier=$id and page_client=$this->client_identifier";
			} else{
				$sql ="update page_data set page_".$entry_type."_discussion=0 where page_identifier=$id and page_client=$this->client_identifier";
			}
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "
			select * from 
				page_trans_data 
			where 
				trans_client=$this->client_identifier and 
				trans_page=$id and 
				trans_published_version = 1";
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$identifier= $r["trans_identifier"];
				
			}
			$this->cache_this_page($identifier,0,"NO");
		}
	}
	
	function display_version_summary($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$sql = "Select * from page_trans_data inner join available_languages on trans_language = language_code where trans_identifier=$identifier and trans_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$out  = "<page_options>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","PAGE_LIST",LOCALE_CANCEL));
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("PREVIEW","PAGE_PREVIEW",ENTRY_PREVIEW,"identifier=$identifier"));
		$out .= "</page_options>";
			
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
				$out .= "<header><![CDATA[".LOCALE_DOC_DETAILS."]]></header>";
				$out .= "<row label=\"".LOCALE_TITLE."\"><![CDATA[".$r["trans_title"]."]]></row>";
				$status 	= 	$r["trans_doc_status"];
				$page 		= 	$r["trans_page"];
				$keywords	= 	$r["trans_dc_keywords"];
				$locked 	= 	$r["trans_doc_lock_to_user"];
				/* Modify by Ali Imran ENTRY_STATUS to LOCALE_PAGE_BASIC_REPORTS*/
				$out .= "<row label=\"".LOCALE_PAGE_BASIC_REPORTS."\"><![CDATA[".$this->get_constant("LOCALE_STATUS_TYPE_".$r["trans_doc_status"])."]]></row>";
				/* End Modification by Ali Imran */
				if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
					if ($this->parent->server[LICENCE_TYPE]==ECMS){
						if ($r["trans_dc_alt_title"]!=""){
							$out .= "<row label=\"".LOCALE_META_ALT_TITLE."\"><![CDATA[".$r["trans_dc_alt_title"]."]]></row>";
						}
						$out .= "<row label=\"".ENTRY_SUMMARY."\"><![CDATA[".$r["trans_summary"]."]]></row>";
						$out .= "<row label=\"".MAJOR_VERSION_MINOR_VERSION."\"><![CDATA[".$r["trans_doc_version_major"].".".$r["trans_doc_version_minor"]."]]></row>";
						$out .= "<row label=\"".LOCALE_LANGUAGE."\"><![CDATA[".$r["language_label"]."]]></row>";
						if ($r["trans_dc_audience"]!=""){
							$out .= "<row label=\"".LOCALE_META_AUDIENCE."\"><![CDATA[".$r["trans_dc_audience"]."]]></row>";
						}
						if ($r["trans_dc_doc_type"]!=""){
							$out .= "<row label=\"".LOCALE_META_DOC_TYPES."\"><![CDATA[".$r["trans_dc_doc_type"]."]]></row>";
						}
						if ($this->check_parameters($r,"trans_dc_contributor")!=""){
							$out .= "<row label=\"".LOCALE_META_CONTRIBUTOR."\"><![CDATA[".$this->check_parameters($r,"trans_dc_contributor")."]]></row>";
						}
						if ($r["trans_dc_rights"]!=""){
							$out .= "<row label=\"".LOCALE_META_RIGHTS."\"><![CDATA[".$r["trans_dc_rights"]."]]></row>";
							if ($r["trans_dc_rights_copyright"]!=""){
								$out .= "<row label=\"".LOCALE_META_RIGHTS_LOC."\"><![CDATA[".$r["trans_dc_rights_copyright"]."]]></row>";
							}
						}
						if ($r["trans_dc_subject_category"]!=""){
							$out .= "<row label=\"".LOCALE_META_SUBJECT."\"><![CDATA[".$r["trans_dc_subject_category"]."]]></row>";
						}
						if ($r["trans_date_creation"]!=""){
							$out .= "<row label=\"".ENTRY_DATE_CREATION."\"><![CDATA[".$r["trans_date_creation"]."]]></row>";
						}
						if ($r["trans_date_modified"]!=""){
							$out .= "<row label=\"".LOCALE_DATE_MODIFIED."\"><![CDATA[".$r["trans_date_modified"]."]]></row>";
						}
						if ($r["trans_date_publish"]!='0000-00-00 00:00:00'){
						$out .= "<row label=\"".LOCALE_DATE_PUBLISHED."\"><![CDATA[".$r["trans_date_publish"]."]]></row>";
						}
						if ($r["trans_date_available"]!='0000-00-00 00:00:00'){
						$out .= "<row label=\"".LOCALE_DATE_AVAILABLE."\"><![CDATA[".$r["trans_date_available"]."]]></row>";
						}
						if ($r["trans_date_remove"]!='0000-00-00 00:00:00'){
						$out .= "<row label=\"".LOCALE_DATE_REMOVE."\"><![CDATA[".$r["trans_date_remove"]."]]></row>";
						}
						if ($r["trans_date_review"]!='0000-00-00 00:00:00'){
						$out .= "<row label=\"".LOCALE_DATE_REVIEW."\"><![CDATA[".$r["trans_date_review"]."]]></row>";
						}
						if ($keywords!=''){
						$out .= "<row label=\"".LOCALE_KEYWORDS."\"><![CDATA[".$this->extract_cdata($keywords,", ")."]]></row>";
						}
					}
				}
			}
		}
		$out .= "<header><![CDATA[".ENTRY_MENU_LOCATIONS."]]></header>";
		$sql = "Select * from menu_access_to_page inner join menu_data on menu_access_to_page.menu_identifier = menu_data.menu_identifier where trans_identifier=$identifier and client_identifier=$this->client_identifier";
		$menu_identifiers =" ";
		$result = $this->call_command("DB_QUERY",Array($sql));
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
				$out .= "<row><![CDATA[".$this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL",Array("id"=>$r["menu_identifier"]))."]]></row>";
				$menu_identifiers =" ".$r["menu_identifier"].",";
			}
		}else{
			$out .= "<row><![CDATA[".LOCALE_NO_MENU_LOCATIONS."]]></row>";
		}
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$out .= "<header><![CDATA[".ENTRY_GROUP_ACCESS."]]></header>";
			$sql = "Select * from group_access_to_page inner join group_data on group_access_to_page.group_identifier = group_data.group_identifier where trans_identifier=$identifier and client_identifier=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",Array($sql));
			if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
				while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
					$out .= "<row><![CDATA[".$r["group_label"]."]]></row>";
				}
			}else{
				$out .= "<row><![CDATA[".LOCALE_USE_MENU_RESTRICTIONS."]]></row>";
				$sql = "Select DISTINCT group_label from relate_menu_groups inner join group_data on relate_menu_groups.group_identifier = group_data.group_identifier where menu_identifier in ($menu_identifiers -1) and group_client=$this->client_identifier";
				$result = $this->call_command("DB_QUERY",Array($sql));
				$out .= "<row><![CDATA[<ul>";
				while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
					$out.="<li>".$r["group_label"]."</li>";
				}
				$out .= "</ul>]]></row>";
			}
		}		
		$out .= "";
		$manage = "<h1>Links in Description</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "body"));
		$manage .= "<h1>Links in Summary</h1>" . $this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$identifier, "editor"=> "summary"));
		$out .= "<text><![CDATA[".$manage."]]></text>";

		return "<module name=\"pages\" display=\"table\"><table label=\"".LOCALE_DOCUMENT_SUMMARY."\">".$out."</table></module>";
	}
	
	
	function page_get_metadata($parameters){
		$trans_id	 				= $parameters["trans_identifier"];
		$trans_doc_status			= $parameters["trans_doc_status"];
		$trans_identifier			= "";
		$trans_page					= "";
		$trans_client				= "";
		$trans_language				= "";
		$trans_doc_author_identifier="";
		$trans_current_working_version="";
		$trans_date_review="";
		$trans_date_remove="";
		$trans_date_available="";
		$trans_dc_audience="";
		$trans_dc_contributor="";
		$trans_dc_creator="";
		$trans_dc_coverage_place="";
		$trans_dc_coverage_postcode="";
		$trans_dc_coverage_time="";
		$trans_dc_doc_type="";
		$trans_dc_publisher="";
		$trans_dc_rights="";
		$trans_dc_rights_copyright="";
		$trans_dc_source="";
		$trans_dc_subject_category="";
		$trans_dc_subject_keywords="";
		$trans_dc_subject_programme="";
		$trans_dc_subject_project="";
		$metadata_contributor_associations="";
		$metadata_creator_associations="";

		$sql = "select * from page_trans_data where trans_identifier = $trans_id and trans_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,$sql));
		}
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$trans_identifier = $this->check_parameters($r,"trans_identifier","");
			$trans_page = $this->check_parameters($r,"trans_page","");
			$trans_client = $this->check_parameters($r,"trans_client","");
			$trans_language = $this->check_parameters($r,"trans_language","");
			$trans_doc_author_identifier = $this->check_parameters($r,"trans_doc_author_identifier","");
			$trans_current_working_version = $this->check_parameters($r,"trans_current_working_version","");
			$trans_date_review = $this->check_parameters($r,"trans_date_review","");
			$trans_date_remove = $this->check_parameters($r,"trans_date_remove","");
			$trans_date_available = $this->check_parameters($r,"trans_date_available","");
			$trans_dc_audience = $this->check_parameters($r,"trans_dc_audience","");
			$trans_dc_contributor = $this->check_parameters($r,"trans_dc_contributor","");
			$trans_dc_creator = $this->check_parameters($r,"trans_dc_creator","");
			$trans_dc_coverage_place = $this->check_parameters($r,"trans_dc_coverage_place","");
			$trans_dc_coverage_postcode = $this->check_parameters($r,"trans_dc_coverage_postcode","");
			$trans_dc_coverage_time = $this->check_parameters($r,"trans_dc_coverage_time","");
			$trans_dc_doc_type = $this->check_parameters($r,"trans_dc_doc_type","");
			$trans_dc_publisher = $this->check_parameters($r,"trans_dc_publisher","");
			$trans_dc_rights = $this->check_parameters($r,"trans_dc_rights","");
			$trans_dc_rights_copyright = $this->check_parameters($r,"trans_dc_rights_copyright","");
			$trans_dc_source = $this->check_parameters($r,"trans_dc_source","");
			$trans_dc_subject_category = $this->check_parameters($r,"trans_dc_subject_category","");
			$trans_dc_subject_programme = $this->check_parameters($r,"trans_dc_subject_programme","");
			$trans_dc_subject_project = $this->check_parameters($r,"trans_dc_subject_project","");
		}
		$pos = strpos($trans_dc_audience, "|");
		if ($pos === false) { // note: three equal signs
		   // not found...
		   $trans_dc_audience = array($trans_dc_audience);
		} else{
			$trans_dc_audience = split("\|",$trans_dc_audience);
		}
		$pos = strpos($trans_dc_subject_category, "|");
		if ($pos === false) { // note: three equal signs
		   // not found...
		   $trans_dc_subject_category = array($trans_dc_subject_category);
		} else{
			$trans_dc_subject_category = split("\|",$trans_dc_subject_category);
		}
		/**
		* blank the default metadata
		*/
		$smd_audience						= "";
		$smd_subject						= "";
		$smd_doctypes						= "";
		$smd_copy_location					= "";
		$smd_publisher_contact_information	= "";

		/**
		* update the blanks with data from the basic default metadata stored in the database
		*/

		$sql = "select * from system_metadata_defaults where smd_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,$sql));
		}
		$smd_result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,$this->call_command("DB_NUM_ROWS",array($smd_result))));
		}
	
		while($r = $this->call_command("DB_FETCH_ARRAY",array($smd_result))){
			$smd_audience						= $r["smd_audience"];
			$smd_subject						= $r["smd_subject"];
			$smd_doctypes						= $r["smd_doctypes"];
			$smd_copy_location					= $r["smd_copy_location"];
			$smd_publisher_contact_information	= $r["smd_publisher_contact_information"];
		}
		
		$list = $this->call_command("CONTACT_GET_DESCRIPTIONS",Array($trans_dc_creator));
		
		$max = count($list);
		$creators = "";
		for($index=0;$index<$max;$index++){
			$creators .= "<li>".$list[$index]."</li>";
		}
		$list = $this->call_command("CONTACT_GET_DESCRIPTIONS",Array($trans_dc_contributor));
		$max = count($list);
		$contributors = "";
		for($index=0;$index<$max;$index++){
			$contributors .= "<li>".$list[$index]."</li>";
		}
		$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"LOCALE_META_DEFAULT_FORM"));
		$trans_menu_locations = $this->check_parameters($parameters,"trans_menu_locations");
		if (count($form_restriction_list)==0){
			$trans_locs = $trans_menu_locations;
			if (is_array($trans_locs)){
				$folder = $trans_locs[0];
			}else{
				if (strlen($trans_locs)>0){
					if (strpos($trans_locs,",")===0){
						$folder=$trans_locs;
					} else {
						$list = split(",",$trans_locs);
						$folder = $list[0];
					}
				}
			}
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_GET_METADATA_SAVE&amp;trans_identifier=$trans_id&amp;trans_doc_status=$trans_doc_status&amp;publisher_info=$smd_publisher_contact_information&amp;trans_menu_locations=$trans_menu_locations"));
		}else{
			//$author 	= $this->call_command("CONTACT_GET_METADATA_AUTHOR_DETAILS",Array("contact_user" => $trans_doc_author_identifier));
			$out = "<module name=\"$this->module_name\" display=\"form\">";
			$out .= "<page_options>";
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","PAGE_LIST",LOCALE_CANCEL));
			$out .= "</page_options>";
			$out.= "<form name=\"user_form\" label=\"Meta Data for your document.\" method=\"post\">";
			$out .= "<input type=\"hidden\" name=\"trans_menu_locations\"><![CDATA[$trans_menu_locations]]></input>";
			$out.= "<input type=\"hidden\" name=\"command\"><![CDATA[PAGE_GET_METADATA_SAVE]]></input>";
			$out.= "<input type=\"hidden\" name=\"trans_identifier\"><![CDATA[$trans_id]]></input>";
			$out.= "<input type=\"hidden\" name=\"trans_doc_status\"><![CDATA[$trans_doc_status]]></input>";
			$out.= "<input type=\"hidden\" name=\"publisher_info\"><![CDATA[$smd_publisher_contact_information]]></input>";
			$out.= "<text><![CDATA[<b>Please fill in the appropriate metadata fields</b>]]></text>";
			if ($this->check_parameters($form_restriction_list,"trans_dc_source","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out.= "<input type=\"text\" label=\"Where did you get your source for the document\" name=\"metadata_source\" size=\"255\"><![CDATA[$trans_dc_source]]></input>";
			}
			if ($this->check_parameters($form_restriction_list,"trans_dc_rights","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out.= "<input type=\"text\" label=\"What rights are with this document (view, copy, redistribute, republish)\" name=\"metadata_rights\" size=\"255\"><![CDATA[$trans_dc_rights]]></input>";
			}
			if ($this->check_parameters($form_restriction_list,"trans_dc_rights_copyright","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($trans_dc_rights_copyright!=""){
					$copy_right = $trans_dc_rights_copyright;
				} else {
					$copy_right = $smd_copy_location;
				}
				$out.= "<input type=\"text\" label=\"Where is the copyright information for this document?\" name=\"metadata_rights_copyright\" size=\"255\"><![CDATA[$copy_right]]></input>";
			}
			if ($this->check_parameters($form_restriction_list,"trans_dc_audience","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out.= "<checkboxes name=\"metadata_audience\" label=\"Select the Audience that this document is for.\" type=\"horizontal\">";
				/**
				* take the list of audience types and return a option list
				*/ 
				$out .=$this->optionise_metadata_choice($smd_audience,$trans_dc_audience);
				$out .="</checkboxes>";
			}
			if ($this->check_parameters($form_restriction_list,"trans_dc_doc_type","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out.= "<select name=\"metadata_doc_type\" label=\"Select the type of document.\">";
				/**
				* take the list of audience types and return a option list
				*/ 
				$out .=$this->optionise_metadata_choice($smd_doctypes,$trans_dc_doc_type);
				$out .="</select>";
			}
			
			if (($this->check_parameters($form_restriction_list,"trans_dc_subject_category","__NOT_FOUND__")!="__NOT_FOUND__") || ($this->check_parameters($form_restriction_list,"trans_dc_subject_programme","__NOT_FOUND__")!="__NOT_FOUND__") ||($this->check_parameters($form_restriction_list,"trans_dc_subject_PROJECT","__NOT_FOUND__")!="__NOT_FOUND__")){
				$out.= "<text><![CDATA[<b>Subject</b>]]></text>";
				if ($this->check_parameters($form_restriction_list,"trans_dc_subject_category","__NOT_FOUND__")!="__NOT_FOUND__"){
					$out.= "<checkboxes name=\"metadata_subject_category\" label=\"metadata_subject_category\" type=\"horizontal\">";
					/**
					* take the list of audience types and return a option list
					*/
					$out .= $this->optionise_metadata_choice($smd_subject,$trans_dc_subject_category);
					
					$out .= "</checkboxes>";
				}
			if ($this->check_parameters($form_restriction_list,"trans_dc_subject_programme","__NOT_FOUND__")!="__NOT_FOUND__"){
					$out .= "<input type=\"text\" label=\"Programme\" name=\"metadata_subject_programme\" size=\"255\"><![CDATA[$trans_dc_subject_programme]]></input>";
				}
				if ($this->check_parameters($form_restriction_list,"trans_dc_subject_PROJECT","__NOT_FOUND__")!="__NOT_FOUND__"){
					$out .= "<input type=\"text\" label=\"Project\" name=\"metadata_subject_project\" size=\"255\"><![CDATA[$trans_dc_subject_project]]></input>";
				}
			}
			if ($this->check_parameters($form_restriction_list,"trans_dc_contributor","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "<input type=\"hidden\" name=\"metadata_contributor_associations\"><![CDATA[$trans_dc_contributor]]></input>";
				$out .= "<text><![CDATA[<b>Contributers</b> (one entry per new line)]]></text>";
				$out .= "<section label=\"LOCALE_META_EXPLAIN_CONTRIBUTOR\" name=\"metadata_contributor\" command=\"CONTACT_LIST_SELECTION\"><![CDATA[$contributors]]></section>";
			}
			if ($this->check_parameters($form_restriction_list,"trans_dc_creator","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "<input type=\"hidden\" name=\"metadata_creator_associations\" value=\"$trans_dc_creator\"/>";
				$out .= "<text><![CDATA[<b>Creator(s)</b> (one entry per new line)]]></text>";
				$out .= "<section label=\"LOCALE_META_EXPLAIN_AUTHOR\" name=\"metadata_creator\" command=\"CONTACT_LIST_SELECTION\"><![CDATA[$creators]]></section>";
			}
			if (($this->check_parameters($form_restriction_list,"trans_dc_coverage_place","__NOT_FOUND__")!="__NOT_FOUND__")
			|| ($this->check_parameters($form_restriction_list,"trans_dc_coverage_postcode","__NOT_FOUND__")!="__NOT_FOUND__")
			|| ($this->check_parameters($form_restriction_list,"trans_dc_coverage_time","__NOT_FOUND__")!="__NOT_FOUND__")){
				$out .= "<text><![CDATA[<b>Coverage</b>]]></text>";
				if ($this->check_parameters($form_restriction_list,"trans_dc_coverage_place","__NOT_FOUND__")!="__NOT_FOUND__"){
					$out .= "<input type=\"text\" label=\"Place\" name=\"metadata_coverage_place\" size=\"255\"><![CDATA[$trans_dc_coverage_place]]></input>";
				}
				if ($this->check_parameters($form_restriction_list,"trans_dc_coverage_postcode","__NOT_FOUND__")!="__NOT_FOUND__"){
					$out .= "<input type=\"text\" label=\"Postcode\" name=\"metadata_coverage_postcode\" size=\"255\"><![CDATA[$trans_dc_coverage_postcode]]></input>";
				}
				if ($this->check_parameters($form_restriction_list,"trans_dc_coverage_time","__NOT_FOUND__")!="__NOT_FOUND__"){
					$out .= "<input type=\"text\" label=\"Time\" name=\"metadata_coverage_time\" size=\"50\"><![CDATA[$trans_dc_coverage_time]]></input>";
				}
			}
			$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
			$out .= "</form>";
			$out .= "</module>";
			return $out;
		}
	}
	
	function page_get_metadata_save($parameters){
		$trans_identifier			= $this->check_parameters($parameters,"trans_identifier");
		$trans_doc_status			= $this->check_parameters($parameters,"trans_doc_status");

		$publisher_info				= $this->tidy($this->check_parameters($parameters,"publisher_info"));
		$metadata_source			= $this->tidy($this->check_parameters($parameters,"metadata_source"));
		$metadata_rights			= $this->tidy($this->check_parameters($parameters,"metadata_rights"));
		$metadata_rights_copyright	= $this->tidy($this->check_parameters($parameters,"metadata_rights_copyright"));
		$metadata_audience			= $this->tidy($this->check_parameters($parameters,"metadata_audience"));
		if (is_array($metadata_audience)){
			$metadata_audience = join("|",$metadata_audience);
		} 
		$metadata_doc_type			= $this->tidy($this->check_parameters($parameters,"metadata_doc_type"));
		$metadata_subject_category	= $this->tidy($this->check_parameters($parameters,"metadata_subject_category"));
		if (is_array($metadata_subject_category)){
			$metadata_subject_category = join("|",$metadata_subject_category);
		}
		$metadata_subject_programme	= $this->tidy($this->check_parameters($parameters,"metadata_subject_programme"));
		$metadata_subject_project	= $this->tidy($this->check_parameters($parameters,"metadata_subject_project"));
		$metadata_contributor		= $this->tidy($this->check_parameters($parameters,"metadata_contributor_associations"));
		$metadata_creator			= $this->tidy($this->check_parameters($parameters,"metadata_creator_associations"));
		$metadata_coverage_place	= $this->tidy($this->check_parameters($parameters,"metadata_coverage_place"));
		$metadata_coverage_postcode	= $this->tidy($this->check_parameters($parameters,"metadata_coverage_postcode"));
		$metadata_coverage_time		= $this->tidy($this->check_parameters($parameters,"metadata_coverage_time"));
	
		$sql ="
		update page_trans_data set
			trans_dc_publisher			= '$publisher_info',
			trans_dc_source				= '$metadata_source',
			trans_dc_rights				= '$metadata_rights',
			trans_dc_rights_copyright	= '$metadata_rights_copyright',
			trans_dc_audience			= '$metadata_audience',
			trans_dc_doc_type			= '$metadata_doc_type',
			trans_dc_subject_category	= '$metadata_subject_category',
			trans_dc_subject_programme	= '$metadata_subject_programme',
			trans_dc_subject_project	= '$metadata_subject_project',
			trans_dc_contributor		= '$metadata_contributor',
			trans_dc_creator			= '$metadata_creator',
			trans_dc_coverage_place		= '$metadata_coverage_place',
			trans_dc_coverage_postcode	= '$metadata_coverage_postcode',
			trans_dc_coverage_time		= '$metadata_coverage_time'
		where
			trans_client 				= $this->client_identifier and 
			trans_identifier			= $trans_identifier
		";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_menu_access_to_page",__LINE__,$sql));
		}
		$result = $this->call_command("DB_QUERY",Array($sql));
	}

	function get_page($parameters){
		$menu_identifier = $this->check_parameters($parameters,"menu_identifier");
		$sql = "select * from menu_access_to_page where menu_identifier = $menu_identifier and client_identifier = $this->client_identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$identifier=-1;
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$identifier =$r["trans_identifier"];
		}
		return $identifier;
	}
	
	function manage_keyword_ignore_list(){
		$list="";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if (file_exists("$data_files/remove_keyword_list_".$this->client_identifier.".txt")){
			$fp = fopen("$data_files/remove_keyword_list_".$this->client_identifier.".txt", 'r');
			$list = fread($fp, filesize("$data_files/remove_keyword_list_".$this->client_identifier.".txt"));
			fclose($fp);
		}
		$out  = "<module name=\"$this->module_name\" display=\"form\">";
		$out .= "<page_options>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","PAGE_LIST",LOCALE_CANCEL));
		$out .= "</page_options>";
		$out .= "<form name=\"user_form\" label=\"".LOCALE_IGNORE_KEYWORD_LIST."\" method=\"post\">";
		$out .= "<input type=\"hidden\" name=\"command\"><![CDATA[PAGE_SAVE_IGNORE_LIST]]></input>";
		$out .= "<textarea name=\"ignore_keyword_list\" label=\"".LOCALE_HOW_TO_WRITE_LIST."\" height=\"25\"><![CDATA[$list]]></textarea>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	
	function save_keyword_ignore_list($parameters){
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$ignore_keyword_list	= $this->check_parameters($parameters,"ignore_keyword_list");
		$fp = fopen("$data_files/remove_keyword_list_".$this->client_identifier.".txt", 'w');
		fwrite($fp, $ignore_keyword_list);
		fclose($fp);
		
		$um = umask(0);
		@chmod($data_files."/remove_keyword_list_".$this->client_identifier.".txt", LS__FILE_PERMISSION);
		umask($um);
	}
	
	function page_list_detail($parameters){
		$list_of_pages = $this->check_parameters($parameters,"page_associations","__NOT_FOUND__");
		$out="";
		if ($list_of_pages!="__NOT_FOUND__"){
			$sql = "select distinct
						page_trans_data.trans_page, 
						page_trans_data.trans_title, 
						menu_access_to_page.menu_identifier 
					from 
						page_trans_data 
					inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
					where 
						trans_client = $this->client_identifier and 
						page_trans_data.trans_page in ($list_of_pages -1) and 
						page_trans_data.trans_published_version=1";
			$result = $this->call_command("DB_QUERY",array($sql));
			$out="<module name='page' display='associated_pages'>";
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$out.="<associate page='".$r["trans_page"]."' location='".$r["menu_identifier"]."'><![CDATA[".$r["trans_title"]."]]></associate>";
			}
			$out.="</module>";
			$out.=$this->call_command("LAYOUT_WEB_MENU");
		}
		return $out;
	}

	// note this function was written to remove all pages published to a specific location in the site
	function remove_pages_in_location($parameters){
		$menu_id = $this->check_parameters($parameters,"menu_identifier",-1);
		$this->call_command("DB_QUERY",Array("delete from menu_access_to_page where menu_identifier = $menu_id and client_identifier=$this->client_identifier"));
/*		$page_list=Array();
		$keep_page=Array();
		
		$sql = "select 
					menu_access_to_page.trans_identifier, 
					page_trans_data.trans_page
				from 
					menu_access_to_page 
				inner join page_trans_data on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier 
				where 
					menu_identifier = $menu_id and 
					client_identifier = $this->client_identifier
				";
		$result = $this->call_command("DB_QUERY",Array($sql));
		
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
				$key = "".$this->check_parameters($r,"trans_page");
				if ($this->check_parameters($page_list,$key,"__NOT_FOUND__")=="__NOT_FOUND__"){
					$page_list[$key] = Array();
					$page_list[$key][count($page_list[$key])] = $this->check_parameters($r,"trans_identifier");
				}else{
					$page_list[$key][count($page_list[$key])] = $this->check_parameters($r,"trans_identifier");
				}
			}
		}
		if($result){
			$this->call_command("DB_FREE",Array($result));
		}
		$page_list_str ="";
		foreach($page_list as $key =>$list){
			$page_list_str .="$key, ";
		}
		$sql = "
		select 
			menu_access_to_page.trans_identifier, 
			page_trans_data.trans_page
		from 
			menu_access_to_page 
		inner join page_trans_data on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier 
		where 
			menu_identifier != $menu_id and 
			trans_page in ($page_list_str -2) and 
			client_identifier=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
				$key = "".$this->check_parameters($r,"trans_page");
				if ($this->check_parameters($keep_page,$key,"__NOT_FOUND__")=="__NOT_FOUND__"){
					$keep_page[$key] = Array();
					$keep_page[$key][count($keep_page[$key])] = $this->check_parameters($r,"trans_identifier");
				}else{
					$keep_page[$key][count($keep_page[$key])] = $this->check_parameters($r,"trans_identifier");
				}
			}
		}
		if($result){
			$this->call_command("DB_FREE",Array($result));
		}
		$lang = "en";
		$page_ids="";
		$trans_ids="";
		$keep_trans ="";
		foreach($page_list as $key =>$list){
			if ($this->check_parameters($keep_page,$key,"__NOT_FOUND__")=="__NOT_FOUND__"){
				// Remove the page completly
				$page_ids .=$key.",";
				$trans_page=$key;
				for($i=0,$m=count($list);$i<$m;$i++){
					$trans_ids .=$list[$i].",";
				}
			} else {
				// Remove trans_identifiers that we have listed in that location only
				for($i=0,$m=count($list);$i<$m;$i++){
					$trans_ids .=$list[$i].",";
				}
				$keep_trans .=$key.", ";
			}
		}
		$this->call_command("DB_QUERY",Array("delete from group_access_to_page where trans_identifier in ($trans_ids -2) and client_identifier=$this->client_identifier"));
		$this->call_command("DB_QUERY",Array("delete from menu_access_to_page where trans_identifier in ($trans_ids -2) and client_identifier=$this->client_identifier"));
		$this->call_command("DB_QUERY",Array("delete from file_access_to_page where trans_identifier in ($trans_ids -2) and client_identifier=$this->client_identifier"));
		$this->call_command("DB_QUERY",Array("delete from page_trans_data where trans_identifier in ($trans_ids -2) and trans_client=$this->client_identifier"));
		$this->call_command("DB_QUERY",Array("delete from page_comments where comment_translation in ($trans_ids -2) and comment_client=$this->client_identifier"));
		$this->call_command("DB_QUERY",Array("delete from page_data where page_identifier in ($page_ids -2) and page_data.page_client=$this->client_identifier"));
		
		$this->call_command("PAGE_DELETE_XML_FILES",Array("list"=>$page_ids,"keep_trans"=>$keep_trans));
		//now recache pages that need updating and remove cached files that have been updated.
		*/
	}
	
	function delete_xml_files($parameters){
		$list 			= $this->check_parameters($parameters,"list");
		$keep_trans		= $this->check_parameters($parameters,"keep_trans");

		if (strlen($list)>0){
			$complete_list = split(",",$list);
			$c=0;
			for($i=0,$max=count($complete_list);$max<$i;$i++){
				$trans_page = $complete_list[$i];
				if($c<10){
					$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
					if (file_exists($data_files."/page_".$this->client_identifier."_".$lang."_".$trans_page.".xml")){
						@unlink ($data_files."/page_".$this->client_identifier."_".$lang."_".$trans_page.".xml");
					}
					if (file_exists($data_files."/comments_".$this->client_identifier."_".$lang."_".$trans_page.".xml")){
						@unlink ($data_files."/comments_".$this->client_identifier."_".$lang."_".$trans_page.".xml");
					}
					$ignore_list .="$trans_page,";
					$complete_list[$i]=-1;
				}
				$c++;
			}
			$list="";
			for($i=0,$max=count($complete_list);$max<$i;$i++){
				if ($complete_list[$i]!=-1){
					$list.=$complete_list[$i].",";
				}
			}
			if (strlen($list)>0){
				$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=PAGE_DELETE_XML_FILES&list=$list&keep_trans=$keep_trans&".SID));
				$this->programexit();
			}
		}
		
		if (strlen($keep_trans)>0){
			$this->regenerate_cache(Array("page_list"=>$keep_trans));
		}
	}
	

	function extract_cdata($xml,$seperator){
		$start	= strpos($xml,"[CDATA[");
		$str	= "";
		if ($start){
			$end		= strpos($xml,"]]>",$start);
			$cdata_str	= substr($xml, $start+7, $end-($start+7));
			$str 		= $this->extract_cdata(substr($xml, $end+3),$seperator); 
			if ($str==""){
				return $cdata_str;
			} else {
				return $cdata_str.$seperator.$str;
			}
		} else {
			return $str;
		}
	}
	
	function page_list_all($parameters){
		$loc = $this->check_parameters($parameters,"menu_url");
		$locid = $this->check_parameters($parameters,"menu_identifier");
		if ($locid!=""){
			$sql ="select trans_title, menu_url from page_trans_data
					inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
					inner join menu_data on menu_access_to_page.menu_identifier = menu_data.menu_identifier
					where menu_data.menu_identifier ='$locid' and trans_doc_status=4 and trans_published_version = 1 and trans_client=$this->client_identifier";
		}else if (strlen($loc)>0){
			$sql ="select trans_title, menu_url from page_trans_data
					inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
					inner join menu_data on menu_access_to_page.menu_identifier = menu_data.menu_identifier
					where menu_url ='$loc' and trans_doc_status=4 and trans_published_version = 1 and trans_client=$this->client_identifier";
		} else {
			$sql = "select trans_title, menu_url from page_trans_data
						inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
						inner join menu_data on menu_access_to_page.menu_identifier = menu_data.menu_identifier
						left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier
						left outer join group_access_to_page on group_access_to_page.trans_identifier = page_trans_data.trans_identifier
						where 
							trans_doc_status = 4 and 
							relate_menu_groups.group_identifier is null and 
							group_access_to_page.group_identifier is null and 
							trans_published_version = 1 and 
							trans_client = $this->client_identifier
						order by menu_url";
		}
		$result = $this->call_command("DB_QUERY",Array($sql));
		$out="";
		$start="";
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$loc =$r["menu_url"];
				$len = strlen($loc);
				if (strlen("index.php") == $len  && $loc="index.php"){
					$start = "";
				} else {
					$start = substr($loc, 0, strlen($loc)- strlen("index.php"));
				}
				$location	= $start.$this->make_uri($r["trans_title"]).".php";
				$out .= "<page>\n";
				$page_title								= join("&#34;",split('"',html_entity_decode(html_entity_decode($r["trans_title"]))));
				$out .= "	<title><![CDATA[".str_replace(Array("\r\n","\n","\r","'","\""),Array("","","","\\[[apos]]","[[quot]]"),strip_tags($page_title))."]]></title>\n";
				$out .= "	<menu_location><![CDATA[".$location."]]></menu_location>\n";
				$out .= "</page>\n";
			}
			if (strlen($out)>0){
				$out ="<pages menu='$loc' results=\"1\">\n".$out."</pages>";
			}
		}
			if (strlen($out)==0){
				$out = "<pages results=\"1\"></pages>";
			}
		$out ="<module name=\"".$this->module_name."\" display=\"form\">".$out."</module>";

		return $out;
	}
	
	function fix_javascript($str, $point = 0){
	
		$lstr = strtolower($str);
		$pos = strpos($lstr,'<script',$point);
		if ($pos===false){
			$str = str_replace(Array("//&lt;![CDATA[", "//]]&gt;"), Array("", ""), $str);
			return $str;
		}else{
			$start = strpos($lstr,'>',$pos)+1;
			$end = strpos($lstr,'</script>',$start);
			$script_info = substr($str, $start, $end-$start);
			$s = join("&gt;",split(">",join("&lt;",split("<",$script_info))));
			$string = substr($str,0,$start).$s.substr($str,$end);
			return $this->fix_javascript($string,$end+10);
		}
	}

	function add_to_ignore_list($str){
		$ignore_list = split(",",$str);
		$list="";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if (file_exists("$data_files/remove_keyword_list_".$this->client_identifier.".txt")){
			$fp = fopen("$data_files/remove_keyword_list_".$this->client_identifier.".txt", 'r');
			$list = fread($fp, filesize("$data_files/remove_keyword_list_".$this->client_identifier.".txt"));
			fclose($fp);
		}
		$fp = fopen("$data_files/remove_keyword_list_".$this->client_identifier.".txt", 'w');
		$list.="\n".join("\n",$ignore_list);
		fwrite($fp, $list);
		fclose($fp);
		$um = umask(0);
		@chmod($data_files."/remove_keyword_list_".$this->client_identifier.".txt", LS__FILE_PERMISSION);
		umask($um);
	}


	function return_start($str,$len){
		
		$cropped = substr(strip_tags($str),0,$len);
		$lastpos = $this->get_last_version($cropped,"<");
		if ($lastpos!=-1){
			$cropped = substr($cropped,0,$lastpos);
		}
		return $cropped;
	}
	
	function get_last_version($haystack, $needle="", $offset=0, $found_previous=-1){
		$pos = strpos(strtolower($haystack), strtolower($needle), $offset);
		if ($pos===false){
			return $found_previous;
		}else{
			return $this->get_last_version($haystack, $needle, $pos+1, $found_previous=$pos);
		}
	}
	
	
	/**
	*  latest pages does not order by rank it orders by date modified.
	*/
		
	function latest_pages_list($parameters){
		$sql = "select * from page_latest where page_latest_client = $this->client_identifier order by page_latest_label";
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"latest_pages",__LINE__," :: [$sql]"));
		}
		$variables = Array();
		$variables["FILTER"]			= $this->filter($parameters,"PAGE_LIST")."<menus selected=\"$menu_location\"/>";
		$variables["MENU_LINKS"]		= "?command=PAGE_LIST&page=1&search=1&menu_location=";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		
		if ($this->module_admin_access==1 || $search==1){
			$result = $this->call_command("DB_QUERY",array($sql));
			
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
				$page = $this->check_parameters($parameters,"page",1);
				$goto = ((--$page)*$this->page_size);
				if (($goto!=0)&&($number_of_records>$goto)){
					$pointer = $this->call_command("DB_SEEK",array($result,$goto));
				}
				if ($goto+$this->page_size>$number_of_records){
					$finish = $number_of_records;
				}else{
					$finish = $goto+$this->page_size;
				}
				$goto++;
				$page++;
				
				if ($number_of_records>0){
					$num_pages=floor($number_of_records / $this->page_size);
					$remainder = $number_of_records % $this->page_size;
					if ($remainder>0){
						$num_pages++;
					}
				}else{
					$num_pages=0;
					$remainder=0;
				}
				$counter=0;
				if($this->author_access==1 && $access_type=="AUTHOR_ACCESS" && $this->parent->module_type=="admin"){
					$variables["PAGE_BUTTONS"] = Array(Array("ADD","PAGE_ADD",ADD_NEW,"menu_location=$menu_location"));
				}
				
				$variables["NUMBER_OF_ROWS"]	= $number_of_records;
				$variables["START"]				= $goto;
				$variables["FINISH"]			= $finish;
				$variables["CURRENT_PAGE"]		= $page;
				$variables["NUMBER_OF_PAGES"]	= $num_pages;
				$start_page=intval($page / $this->page_size);
				$remainder = $page % $this->page_size;
				if ($remainder>0){
					$start_page++;
				}
				
				$variables["START_PAGE"]		= $start_page;
				
				if (($start_page+$this->page_size)>$num_pages){
					$end_page	 =	$num_pages;
				}else{
					$end_page	=	$this->page_size;
				}
				$user_identifier 		= $_SESSION["SESSION_USER_IDENTIFIER"];
				$variables["END_PAGE"]			= $end_page;
				$variables["ENTRY_BUTTONS"] =Array();
				$variables["CONDITION"]= array();
				$variables["RESULT_ENTRIES"] =Array();
				
				$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"LOCALE_PAGE_FORM"));
				while (($r = $this->call_command("DB_FETCH_ARRAY", array($result))) && ($counter<$this->page_size)){
					$counter++;
					$i = count($variables["RESULT_ENTRIES"]);
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=> $r["trans_identifier"],
						"ENTRY_BUTTONS" => Array(
							Array("TITLE","PAGE_PREVIEW",ENTRY_PREVIEW,"admin/preview.php")
						),
						"attributes"	=> Array()
					);
					$variables["RESULT_ENTRIES"][$i]["attributes"]	=	Array(
						Array("TITLE",$r["page_latest_label"]."","NO","NO")
					);
				}
			}
		}
		$out = $this->generate_list($variables).$this->call_command("LAYOUT_WEB_MENU");
		return $out;
	}
	
	function latest_pages_form($parameters){
		$result = $this->call_command("DB_QUERY",Array($sql));
		$page_documents= Array();
		$out  = "<module name=\"".$this->module_name."\" display=\"LATEST\" command=\"PAGE_DISPLAY\" call=\"page_list\">";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($result){
			$c=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",Array($result))) && ($c<10)){
				$id			= $r["trans_page"];
				if (file_exists($data_files."/page_".$this->client_identifier."_".$lang."_$id.xml")){
					$fp = fopen($data_files."/page_".$this->client_identifier."_".$lang."_$id.xml", 'r');
					$out .= fread($fp, filesize($data_files."/page_".$this->client_identifier."_".$lang."_$id.xml"));
					fclose($fp);
				}
				$c++;
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
		$out .= "</module>";
		$lang = "en";
		return $out;
		
	}
	
	function remove_previous_versions($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$list_to_delete = Array();
		if ($identifier != -1){
			$sql = "
				select distinct
					ptd2.trans_identifier, ptd2.trans_published_version 
				from page_trans_data as ptd1
					inner join page_data on ptd1.trans_page = page_data.page_identifier
					inner join page_trans_data as ptd2 on ptd2.trans_page = page_data.page_identifier
				where 
					ptd2.trans_published_version =0 and 
					ptd2.trans_client = $this->client_identifier and 
					ptd1.trans_client = $this->client_identifier and 
					ptd2.trans_identifier != $identifier
			";
//			print $sql;
			$result = $this->call_command("DB_QUERY",Array($sql));
			if ($result){
				$c=0;
				while (($r = $this->call_command("DB_FETCH_ARRAY",Array($result))) && ($c<10)){
					$list_to_delete[count($list_to_delete)] = $r["trans_identifier"];
				}
				$result = $this->call_command("DB_FREE",Array($result));
				$list = join(",", $list_to_delete);
				$sql = "delete from page_trans_data where trans_identifier in ($list) and trans_client=$this->client_identifier";
				$this->call_command("DB_QUERY",array($sql));
				$sql = "delete from menu_access_to_page where trans_identifier in ($list) and client_identifier=$this->client_identifier";
				$this->call_command("DB_QUERY",array($sql));
				$sql = "delete from file_access_to_page where trans_identifier in ($list) and client_identifier=$this->client_identifier";
				$this->call_command("DB_QUERY",array($sql));
				$sql = "delete from group_access_to_page where trans_identifier in ($list) and client_identifier=$this->client_identifier";
				$this->call_command("DB_QUERY",array($sql));
				$sql ="update page_trans_data set trans_current_working_version=1 where trans_identifier = $identifier and trans_client = $this->client_identifier";
				$this->call_command("DB_QUERY",array($sql));
				$this->call_command("EMBED_REMOVE_INFO", Array("trans_list"=>$list_to_delete,"action"=>"remove_list"));
			}
		}
	}


	function cache_this_page($identifier,$set_publish=1,$auto_summary = "0"){
//		print "$identifier, $set_publish, $auto_summary";
		$trans_identifier="";
		$trans_client="";
		$trans_language="";
		$trans_page="";
		$trans_title="";
		$trans_summary="";
		$trans_body="";
		
		$trans_date_creation="";
		$trans_date_publish="";
		$trans_date_review="";
		$trans_date_modified="";
		$trans_date_remove="";
		$trans_date_available="";
		$trans_doc_author_identifier="";
		$trans_version_number="";
		
		$trans_best_bets="";
		$trans_dc_keywords="";
		$trans_dc_alt_title="";
		$trans_dc_audience="";
		$trans_dc_contributor="";
		$trans_dc_creator="";
		$trans_dc_coverage_place="";
		$trans_dc_coverage_postcode="";
		$trans_dc_coverage_time="";
		$trans_dc_doc_type="";
		$trans_dc_publisher="";
		$trans_dc_rights="";
		$trans_dc_rights_copyright="";
		$trans_dc_source="";
		$trans_dc_subject_category="";
		$trans_dc_subject_keywords="";
		$trans_dc_subject_programme="";
		$trans_dc_subject_project="";
		$web_notes="";
		$out="";
		$lang = "en";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[".$identifier."]"));
		}
		/**
		* publishing causes major verion to be incremented
		*/
		$now = Date("Y-m-d H:i:s");
		
		/**
		* retrieve translation information from database
		*/
		$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd1",
				"field_as"			=> "trans_body1",
				"identifier_field"	=> "trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "body"
			)
		);
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary"
			)
		);
		/*
		$sql = "SELECT page_data.*, page_trans_data.*, contact_data.*, 
		".$body_parts["return_field"].", ".$summary_parts["return_field"]."
		 FROM page_data inner join page_trans_data on page_identifier = trans_page 
					left outer join contact_data on contact_user = trans_doc_author_identifier 
					".$body_parts["join"]." ".$summary_parts["join"]."
					where trans_language='en' and 
					page_trans_data.trans_identifier = $identifier and 
					trans_client=$this->client_identifier ".$body_parts["where"]." ".$summary_parts["where"]."";
		*/
		$sql = "select page_data.*, page_trans_data.*, 
				".$body_parts["return_field"].", ".$summary_parts["return_field"]."
				from page_data 
				inner join page_trans_data on page_data.page_identifier = page_trans_data.trans_page 
				".$body_parts["join"]." ".$summary_parts["join"]."
				where 
					page_trans_data.trans_client = $this->client_identifier and 
					page_trans_data.trans_identifier = ".$identifier ." 
					". $body_parts["where"]." ".$summary_parts["where"]."";
//		print "<li>$sql</li>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",Array($sql));
		$page_documents= Array();
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$trans_identifier 				= $this->check_parameters($r,"trans_identifier","");
				$trans_client 					= $this->check_parameters($r,"trans_client","");
				$trans_language 				= $this->check_parameters($r,"trans_language","");
				$trans_page 					= chop($this->check_parameters($r,"trans_page",""));
				$trans_title					= chop($this->check_parameters($r,"trans_title",""));
				$trans_summary					= chop($this->check_parameters($r,"trans_summary1",""));
				$trans_body						= chop($this->check_parameters($r,"trans_body1",""));
				$trans_doc_author_identifier	= chop($this->check_parameters($r,"trans_doc_author_identifier",""));
				$trans_date_creation			= $this->libertasGetDate("r",strtotime(chop($this->check_parameters($r,"trans_date_creation",""))));
				if ($set_publish==1){
					$trans_date_publish				= $this->libertasGetDate("r",strtotime(chop($now)));
				}else{
					$trans_date_publish				= $this->libertasGetDate("r",strtotime(chop($this->check_parameters($r,"trans_date_publish",""))));
				}
				if ($this->check_parameters($r,"trans_date_review","") != "0000-00-00 00:00:00"){
					$trans_date_review			= $this->libertasGetDate("r",strtotime(chop($this->check_parameters($r,"trans_date_review",""))));
				}
				if ($this->check_parameters($r,"trans_date_remove","") != "0000-00-00 00:00:00"){	
					$trans_date_remove			= $this->libertasGetDate("r",strtotime(chop($this->check_parameters($r,"trans_date_remove",""))));
				}
				$trans_date_modified			= $this->libertasGetDate("r",strtotime(chop($this->check_parameters($r,"trans_date_modified",""))));
				$trans_date_available			= $this->libertasGetDate("r",strtotime(chop($this->check_parameters($r,"trans_date_available",""))));
				$trans_version_number			= chop(($this->check_parameters($r,"trans_doc_version_major",1)+1).".0");
				$trans_best_bets				= chop($this->check_parameters($r,"trans_best_bets",""));
				$trans_dc_keywords				= chop($this->check_parameters($r,"trans_dc_keywords",""));
				$trans_dc_alt_title 			= chop($this->check_parameters($r,"trans_dc_alt_title",""));
				$trans_dc_audience 				= chop($this->check_parameters($r,"trans_dc_audience",""));
				$trans_dc_contributor 			= chop($this->check_parameters($r,"trans_dc_contributor",""));
				$trans_dc_creator 				= chop($this->check_parameters($r,"trans_dc_creator",""));
				$trans_dc_coverage_place 		= chop($this->check_parameters($r,"trans_dc_coverage_place",""));
				$trans_dc_coverage_postcode		= chop($this->check_parameters($r,"trans_dc_coverage_postcode",""));
				$trans_dc_coverage_time 		= chop($this->check_parameters($r,"trans_dc_coverage_time",""));
				$trans_dc_doc_type 				= chop($this->check_parameters($r,"trans_dc_doc_type",""));
				$trans_dc_publisher 			= chop($this->check_parameters($r,"trans_dc_publisher",""));
				$trans_dc_rights 				= chop($this->check_parameters($r,"trans_dc_rights",""));
				$trans_dc_rights_copyright 		= chop($this->check_parameters($r,"trans_dc_rights_copyright",""));
				$trans_dc_source 				= chop($this->check_parameters($r,"trans_dc_source",""));
				$trans_dc_subject_category 		= chop($this->check_parameters($r,"trans_dc_subject_category",""));
				$trans_dc_subject_keywords 		= chop($this->check_parameters($r,"trans_dc_subject_keywords",""));
				$trans_dc_subject_programme 	= chop($this->check_parameters($r,"trans_dc_subject_programme",""));
				$trans_dc_subject_project 		= chop($this->check_parameters($r,"trans_dc_subject_project",""));
				$web_notes						= chop($this->check_parameters($r,"page_web_discussion","0"));
			}
//			$result = $this->call_command("DB_FREE",Array($result));
			if ($auto_summary=="1"){
				$len = 197;
				$str = trim(strip_tags($this->split_me($this->split_me($this->split_me($this->split_me($trans_body,">",">  "),"<"," <"),"\n"," \n"),"\r"," \r")));
				if (strlen($str)<$len){
					$len = strlen($str);
				}
				$trans_summary				= substr($str,0,$len)."...";
				$sql ="update page_trans_data set trans_summary='$trans_summary' where trans_identifier = $identifier and trans_client=$this->client_identifier";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[$sql]"));
				}
				$result = $this->call_command("DB_QUERY",Array($sql));
			}
			if ($set_publish==1){
				$sql ="update page_trans_data set trans_published_version=0 where trans_page = $trans_page and trans_client=$this->client_identifier";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[$sql]"));
				}
				$result = $this->call_command("DB_QUERY",Array($sql));
				if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
					$sql ="update page_trans_data set trans_doc_lock_to_user=0, trans_doc_version_major=trans_doc_version_major+1, trans_date_publish='$now', trans_doc_version_minor=0, trans_published_version=1 where trans_identifier = $identifier and trans_client=$this->client_identifier";
				}else{
					$sql ="update page_trans_data set trans_doc_lock_to_user=0, trans_date_publish='$now', trans_published_version=1 where trans_identifier = $identifier and trans_client=$this->client_identifier";
				}
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[$sql]"));
				}
				$result = $this->call_command("DB_QUERY",Array($sql));
			}
			if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
			}
		}
		
		if (strlen($trans_dc_contributor)>0){
			$trans_dc_contributor 	= $this->call_command("CONTACT_GET_DESCRIPTIONS",Array($trans_dc_contributor));
		} else {
			$trans_dc_contributor=Array();
		}
		
		$creators="";
		if (strlen($trans_dc_creator)==0){
			$creators = $trans_doc_author_identifier . "";
		} else {
			$creators = $trans_dc_creator." $trans_doc_author_identifier ";
		}
		$trans_dc_creator 		= $this->call_command("CONTACT_GET_DESCRIPTIONS",Array($creators));
		/**
		* retrieve anyfiles associated with this version
		*/
		
		$file_list = $this->call_command("FILES_LIST_FILE_DETAIL", Array("identifier" => $identifier));
		
		/**
		* retrieve any images that are associated with the summary
	*/ 
		$summary_files = $this->call_command("FILES_LIST_SUMMARY_FILE_DETAIL", Array("identifier" => $trans_identifier, "module" => "PAGE_SUMMARY"));
		/**
		* retrieve any group that can see this version
		*/
		
		$sql = "select group_data.* from group_data inner join group_access_to_page on group_data.group_identifier=group_access_to_page.group_identifier where group_access_to_page.trans_identifier=$identifier and group_access_to_page.client_identifier=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[$sql]"));
		}
		
		$result = $this->call_command("DB_QUERY",Array($sql));
		$page_documents= Array();
		
		$group_list="";
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$group_id		 = $r["group_identifier"];
				$group_label	 = $this->convert_amps($r["group_label"]);
				$group_type		 = $r["group_type"];
				$group_list		.= "<group identifier=\"$group_id\"  label=\"$group_label\" type=\"$group_type\"/>\n";
			}
			$this->call_command("DB_FREE",Array($result));
		}
		/**
		* retrieve any menu that this version is to be published to.
		*/
		$sql = "select menu_data.* from menu_data inner join menu_access_to_page on menu_data.menu_identifier=menu_access_to_page.menu_identifier where menu_access_to_page.trans_identifier=$identifier and menu_access_to_page.client_identifier=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[$sql]"));
		}
		$menu_result = $this->call_command("DB_QUERY",Array($sql));
		$page_documents= Array();
		
		$menu_list="";
		$identifiers="";
		if ($this->call_command("DB_NUM_ROWS",Array($menu_result))>0){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($menu_result))){
				$menu_id		 = $r["menu_identifier"];
				$menu_label		 = $this->convert_amps($r["menu_label"]);
//				if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
				$menu_url		 = $this->convert_amps($r["menu_url"]);
/*				}else{
					$root		 = $this->parent->site_directories["ROOT"];
					$menu_url	 = "content/index.php";
					$path_list 	 = $root.$menu_url;
					if (file_exists($path_list)){
						if (!is_dir(dirname($path_list))){
							$oldumask = umask(0);
							@mkdir(dirname($path_list), LS__FILE_PERMISSION);
				 			umask($oldumask);
						}
					} else {
						$oldumask = umask(0);
						@mkdir(dirname($path_list), LS__FILE_PERMISSION);
			 			umask($oldumask);
					}
				}*/
				$file_path		 = $this->translate_to_filename($menu_url,$trans_title,$trans_page,$menu_id);
				$menu_list		.= "<location identifier=\"$menu_id\" label=\"$menu_label\" url=\"$menu_url\"><![CDATA[".$file_path."]]></location>\n";
				$identifiers 	.= "<identifier><![CDATA[".$file_path."]]></identifier>\r\n";
			}
			$this->call_command("DB_FREE",Array($result));
		}
		$description_cont="";
		$description_create="";
		
		$max = count($trans_dc_contributor);
		for($index=0;$index<$max;$index++){
			$description_cont .="<contributor><![CDATA[".$trans_dc_contributor[$index]."]]></contributor>";
		}
		
		$max = count($trans_dc_creator);
		for($index=0;$index<$max;$index++){
			$description_create .="<creator><![CDATA[".$trans_dc_creator[$index]."]]></creator>";
		}
		
		$pos = strpos($trans_dc_audience, "|");
		if ($pos === false) { // note: three equal signs
		   $trans_dc_audience = "<audience><![CDATA[$trans_dc_audience]]></audience>";
		   // not found...
		} else{
			$list = split("\|",$trans_dc_audience);
			$max = count($list);
			$trans_dc_audience = "";
			for($index=0;$index<$max;$index++){
				$trans_dc_audience .= "<audience><![CDATA[".$list[$index]."]]></audience>";
			}
		}
		
		$pos = strpos($trans_dc_subject_category, "|");
		if ($pos === false) { // note: three equal signs
		   $trans_dc_subject_category = "<subject refinement=\"category\"><![CDATA[$trans_dc_subject_category]]></subject>";
		   // not found...
		} else{
			$list = split("\|",$trans_dc_subject_category);
			$max = count($list);
			$trans_dc_subject_category = "";
			for($index=0;$index<$max;$index++){
				$trans_dc_subject_category .= "<subject refinement=\"category\"><![CDATA[".$list[$index]."]]></subject>";
			}
		}


		$std_time = strtotime($trans_date_publish);
		$out = "<page identifier=\"$trans_page\" translation_identifier=\"$trans_identifier\" client_identifier=\"$trans_client\" version=\"$trans_version_number\" language=\"$trans_language\" web_notes=\"$web_notes\">
			<metadata>
	  			<keywords>".$this->makeCleanOutputforXSL($trans_dc_keywords)."</keywords>
				<title><![CDATA[$trans_title]]></title>
				$description_cont
				$description_create
				$trans_dc_audience
				<alternative><![CDATA[$trans_dc_alt_title]]></alternative>
				$trans_dc_subject_category
				<subject refinement=\"keywords\"><![CDATA[$trans_dc_subject_keywords]]></subject>
				<subject refinement=\"project\"><![CDATA[$trans_dc_subject_project]]></subject>
				<subject refinement=\"programme\"><![CDATA[$trans_dc_subject_programme]]></subject>
				<description><![CDATA[".substr($this->mystrip_tags($trans_summary),0,200)."]]></description>
				<publisher><![CDATA[$trans_dc_publisher]]></publisher>
				<source><![CDATA[$trans_dc_source]]></source>
				<date refinement=\"publish\" seconds=\"".$std_time."\"><![CDATA[$trans_date_publish]]></date>
				<date refinement=\"creation\"><![CDATA[$trans_date_creation]]></date>";
				/** SET REVIEW, REMOVE DATE ONLY IF IT GIVEN */
				if ($trans_date_review !="") {
					$out .= "<date refinement=\"review\"><![CDATA[$trans_date_review]]></date>";
				}
				if ($trans_date_remove !="") {
					$out .= "<date refinement=\"remove\"><![CDATA[$trans_date_remove]]></date>";
				}
				$out .= "<date refinement=\"modified\"><![CDATA[$trans_date_modified]]></date>
				<date refinement=\"available\"><![CDATA[$trans_date_available]]></date>
				<type><![CDATA[$trans_dc_doc_type]]></type>
				<format><![CDATA[text/html]]></format>
				$identifiers
				<relation><![CDATA[]]></relation>
				<coverage><![CDATA[$trans_dc_coverage_place]]></coverage>
				<coverage refinement=\"postcode\"><![CDATA[$trans_dc_coverage_postcode]]></coverage>
				<coverage refinement=\"time\"><![CDATA[$trans_dc_coverage_time]]></coverage>
				<rights><![CDATA[$trans_dc_rights]]></rights>
				<rights refinement=\"copyright\"><![CDATA[$trans_dc_rights_copyright]]></rights>
				<language><![CDATA[$trans_language]]></language>
			</metadata>
			$trans_best_bets
			<locations>
				$menu_list
			</locations>
				$file_list
			<groups>
				$group_list
			</groups>
			<title><![CDATA[$trans_title]]></title>
			<summary><![CDATA[$trans_summary]]></summary>
			<content><![CDATA[".$this->split_me($trans_body,"&#39;","'")."]]></content>
			<summary_files>$summary_files</summary_files>
		</page>\n";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$trans_dc_common_keywords = str_replace(array(";;",";"),array("",","),$trans_dc_subject_keywords);
		// $_SESSION["cache_extra_ids"] .="<li>".$data_files."/presentation_all_".$this->client_identifier."_".$lang."_".$trans_page.".xml</li>";

		$fp = fopen($data_files."/presentation_all_".$this->client_identifier."_".$lang."_".$trans_page.".xml", 'w');
		fwrite($fp, $out);
		fclose($fp);
		$um = umask(0);
		@chmod($data_files."//presentation_all_".$this->client_identifier."_".$lang."_".$trans_page.".xml", LS__FILE_PERMISSION);
		umask($um);
		$out = "<page identifier=\"$trans_page\" translation_identifier=\"$trans_identifier\" language=\"$trans_language\">
			<metadata>
				<date refinement=\"modified\"><![CDATA[$trans_date_modified]]></date>
				<description><![CDATA[".substr($this->mystrip_tags($trans_summary),0,200)."]]></description>
				<keywords>".$this->makeCleanOutputforXSL($trans_dc_common_keywords)."</keywords>								
			</metadata>
			<title><![CDATA[$trans_title]]></title>
			<locations>
				$menu_list
			</locations>
		</page>\n";
		$fp = fopen($data_files."/presentation_title_".$this->client_identifier."_".$lang."_".$trans_page.".xml", 'w');
		fwrite($fp, $out);
		fclose($fp);
		$um = umask(0);
		@chmod($data_files."//presentation_title_".$this->client_identifier."_".$lang."_".$trans_page.".xml", LS__FILE_PERMISSION);
		umask($um);
		$out = "<page identifier=\"$trans_page\" translation_identifier=\"$trans_identifier\" client_identifier=\"$trans_client\" version=\"$trans_version_number\" language=\"$trans_language\" web_notes=\"$web_notes\">
			<metadata>
				$description_create
				<description><![CDATA[".substr($this->mystrip_tags($trans_summary),0,200)."]]></description>
				<date refinement=\"available\"><![CDATA[$trans_date_available]]></date>
				<keywords>".$this->makeCleanOutputforXSL($trans_dc_common_keywords)."</keywords>				
			</metadata>
			<locations>
				$menu_list
			</locations>
			<title><![CDATA[$trans_title]]></title>
			<summary><![CDATA[$trans_summary]]></summary>
			<summary_files>$summary_files</summary_files>
		</page>\n";
		$fp = fopen($data_files."/presentation_summary_".$this->client_identifier."_".$lang."_".$trans_page.".xml", 'w');
		fwrite($fp, $out);
		fclose($fp);
		$um = umask(0);
		@chmod($data_files."//presentation_summary_".$this->client_identifier."_".$lang."_".$trans_page.".xml", LS__FILE_PERMISSION);
		umask($um);
		$out = "<page identifier=\"$trans_page\" translation_identifier=\"$trans_identifier\" client_identifier=\"$trans_client\" version=\"$trans_version_number\" language=\"$trans_language\" web_notes=\"$web_notes\">
			<metadata>
				$description_create
				<date refinement=\"available\"><![CDATA[$trans_date_available]]></date>
				<keywords>".$this->makeCleanOutputforXSL($trans_dc_common_keywords)."</keywords>								
				<description><![CDATA[".substr($this->mystrip_tags($trans_summary),0,200)."]]></description>				
			</metadata>
			<locations>
				$menu_list
			</locations>
			$file_list
			<title><![CDATA[$trans_title]]></title>
			<content><![CDATA[".$this->split_me($trans_body,"&#39;","'")."]]></content>
		</page>\n";
		$fp = fopen($data_files."/presentation_content_".$this->client_identifier."_".$lang."_".$trans_page.".xml", 'w');
		fwrite($fp, $out);
		fclose($fp);
		$um = umask(0);
		@chmod($data_files."//presentation_content_".$this->client_identifier."_".$lang."_".$trans_page.".xml", LS__FILE_PERMISSION);
		umask($um);


		$this->call_command("COMMENTSADMIN_CACHE",Array("trans_page" => $trans_page, "trans_identifier" => $trans_identifier));
	/*
		if ($this->parent->server[LICENCE_TYPE]==SITE_WIZARD){
			$this->call_command("LAYOUT_CREATE_MENU_BASED_ON_PAGE",Array("trans_title"=>$trans_title, "file_path"=>$file_path, "trans_identifier" => $identifier));
		}
	*/
		return 1;
	}


	function page_form_editorial($parameters){
		$id		= $this->check_parameters($parameters,"identifier",-1);
		$out	= "";
		if ($id != -1 && $id != ""){
			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				$sql = "select trans_identifier from page_trans_data where trans_page = $id and trans_client=$this->client_identifier order by trans_identifier desc";
			} else{
				$sql = "select trans_identifier from page_trans_data where trans_identifier = $id and trans_client=$this->client_identifier order by trans_identifier desc";
			}
			$result 	= $this->call_command("DB_QUERY", Array($sql));
			$i=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($i<1)){
				$i++;
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					$newid	= $this->page_copy(Array("identifier"=>$r["trans_identifier"]));
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=PAGE_EDIT&amp;identifier=$newid&amp;live_edit=1"));
				} else {
					$out	= $this->page_form(Array("identifier"=>$r["trans_identifier"]));
				}
			}
			$this->call_command("DB_FREE", Array($result));
		}
		return $out;
	}
	
	function remove_live_edit($parameters){
		$debug = $this->debugit(false,$parameters);
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$trans_page	= $this->check_parameters($parameters,"trans_page",-1);
		if ($identifier !=-1){
			$sql = "delete from page_trans_data where trans_client=$this->client_identifier and trans_identifier = $identifier and trans_page = $trans_page";
			$this->call_command("DB_QUERY", Array($sql));
			if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
			$sql = "delete from file_access_to_page where client_identifier=$this->client_identifier and trans_identifier = $identifier";
			$this->call_command("DB_QUERY", Array($sql));
			if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
			$sql = "delete from menu_access_to_page where client_identifier=$this->client_identifier and trans_identifier = $identifier";
			$this->call_command("DB_QUERY", Array($sql));
			if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
			$sql = "delete from group_access_to_page where client_identifier=$this->client_identifier and trans_identifier = $identifier";
			$this->call_command("DB_QUERY", Array($sql));
			$this->call_command("MEMOINFO_DELETE", 
				Array(
					"mi_type"		=> 'PAGE_',
					"mi_link_id"	=> $identifier,
					"mi_field"		=> "body"
				)
			);
			$this->call_command("MEMOINFO_DELETE", 
				Array(
					"mi_type"		=> 'PAGE_',
					"mi_link_id"	=> $identifier,
					"mi_field"		=> "summary"
				)
			);
			$this->call_command("EMBED_REMOVE_INFO", 
				Array(
					"type"		=> 'PAGE_',
					"identifier"	=> $identifier
				)
			);
			if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
			$sql = "select max(trans_identifier) as trans_identifier from page_trans_data where trans_page=$trans_page and trans_client=$this->client_identifier group by trans_page order by trans_identifier desc";
			$result = $this->call_command("DB_QUERY", Array($sql));
			if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
			while ($r = $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$trans_identifier = $r["trans_identifier"];
				if ($debug) print "found <strong>$trans_identifier</strong>";
				$sql = "update page_trans_data set trans_current_working_version = 1 where trans_client=$this->client_identifier and trans_identifier = $trans_identifier and trans_page = $trans_page";
				$this->call_command("DB_QUERY", Array($sql));
				if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
			}
			$this->call_command("DB_FREE", Array($result));
		}
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=PAGE_LIST"));
	}

	function retrieve_my_docs($parameters){
		$debug = $this->debugit(false, $parameters);
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$user   = $_SESSION["SESSION_USER_IDENTIFIER"];
		$access_list = "";
		$access_array = array();
		$ALL=0;
		$open = 0;
		$userRestricted = 0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$max = count($access);
			for($index=0;$index<$max;$index++){
				if ((substr($access[$index],0,strlen($this->module_command))==$this->module_command)||($access[$index]=="ALL")){
					$open=1;
				}
			}
		}
		$session_management_access	= $this->check_parameters($_SESSION, "SESSION_MANAGEMENT_ACCESS", Array());
		$session_man_access="";
		for($index=0,$max=count($session_management_access);$index<$max;$index++){
			$userRestricted=1;
			if ($index>0){
				$session_man_access .= ",";
			}
			$session_man_access .= " ".$session_management_access[$index];
		}
		$join1 ="";
		$join2 ="";
		$where = "";
		if ($userRestricted){
			$join1  = " inner join menu_access_to_page on (page_trans_data.trans_identifier = menu_access_to_page.trans_identifier and page_trans_data.trans_client = menu_access_to_page.client_identifier) ";
			$join1 .= " left outer join relate_user_menu on (relate_user_menu.menu_identifier = menu_access_to_page.menu_identifier and relate_user_menu.user_identifier = page_trans_data.trans_doc_lock_to_user) ";
			$join2  = " inner join menu_access_to_page on (page_trans_data.trans_identifier = menu_access_to_page.trans_identifier and page_trans_data.trans_client = menu_access_to_page.client_identifier) ";
			$join2 .= " left outer join relate_user_menu on (relate_user_menu.menu_identifier = menu_access_to_page.menu_identifier) ";
			$where = " and relate_user_menu.menu_identifier in ($session_man_access)";
			$link_user = $user;
		} else {
			$link_user = -1;
		}
		if ($open==1){
			if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
				if ($this->module_admin_access){
					$out="<cmd label=\"".LOCALE_LIST."\">PAGE_LIST</cmd>";
				}
				if($this->author_access){
					/**
					* has this user checked out any documents
					*/
					$sql    = "select * from page_trans_data inner join available_languages on trans_language = language_code $join1 
					where trans_client =$this->client_identifier and trans_current_working_version=1 and trans_doc_lock_to_user=$user $where";
					if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
					$result = $this->call_command("DB_QUERY",Array($sql));
					$out .= "<cmd label=\"".ADD_NEW."\">PAGE_ADD</cmd>";
					$out .= "<text><![CDATA[";
					$num = $this->call_command("DB_NUM_ROWS", array($result));
					if ($num>0){
						$out .= "<a href='admin/index.php?command=PAGE_LIST&amp;status_filter=1&amp;user=$link_user&amp;lock=1'>";
						$out .= "Pages in checked out/draft ($num)" ;
						$out .= "</a>";
					} else {
						$out .= "Pages in checked out/draft (0)";
					}
					$out .= "]]></text>";
					if ($this->parent->server[LICENCE_TYPE]==ECMS){
						/**
						* any documents to review ???
						*/
						$now_csv = date("Y,m,d,H,i,s");
						$date_list = split(",",$now_csv);
						if ($date_list[1]==12){
							$date_list[1]=1;
							$date_list[0]++;
						} else {
							$date_list[1]++;
						}
						$now = $date_list[0]."/".$date_list[1]."/".$date_list[2]." ".$date_list[3].":".$date_list[4].":".$date_list[5];
						// should not need 'trans_current_working_version=1 and ' in sql
						$sql    = "select * from page_trans_data 
									inner join available_languages on trans_language = language_code 
									$join2 
									where 
									trans_current_working_version=1 and trans_client =$this->client_identifier and trans_published_version=1 and (trans_date_review <= '$now' and trans_date_review != '0000/00/00 00:00:00') $where";
						if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
						$result = $this->call_command("DB_QUERY",Array($sql));
						$out .= "<text><![CDATA[";
						$num = $this->call_command("DB_NUM_ROWS", array($result));
						if ($num>0){
							$out .= "<a href='admin/index.php?command=PAGE_LIST&amp;filter=review'&amp;lock=1>";
							$out .= "Pages requiring review ($num)";
							$out .= "</a>";
						} else {
							$out .= "Pages requiring review (0)";
						}
						$out .= "]]></text>";
					}
				}
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					if($this->approver_access){
						/**
						* any documents to approve
						*/
						$sql    = "select * from page_trans_data inner join available_languages on trans_language = language_code $join2 where trans_client =$this->client_identifier and trans_current_working_version=1 and trans_doc_status=2 $where";
						if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
						$result = $this->call_command("DB_QUERY",Array($sql));
						$out .= "<text><![CDATA[";
						$num = $this->call_command("DB_NUM_ROWS", array($result));
						if ($num>0){
							$out .= "<a href='admin/index.php?command=PAGE_LIST&amp;status_filter=2&amp;lock=1'>";
							$out .= "Pages awaiting your approval ($num)";
							$out .= "</a>";
						} else {
							$out .= "Pages awaiting your approval (0)";
						}
						$out .= "]]></text>";
					}
				}
				if($this->publisher_access){
					$sql    = "select * from page_trans_data inner join available_languages on trans_language = language_code $join2 where trans_client =$this->client_identifier and trans_current_working_version=1 and trans_doc_status=3 $where";
					if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
					$result = $this->call_command("DB_QUERY",Array($sql));
					$out .= "<text><![CDATA[";
					$num = $this->call_command("DB_NUM_ROWS", array($result));
					if ($num>0){
						$out .= "<a href='admin/index.php?command=PAGE_LIST&amp;status_filter=3&amp;lock=1'>";
						$out .= "Pages awaiting publisher approval ($num)";
						$out .= "</a>";
					} else {
						$out .= "Pages awaiting publisher approval (0)";
					}
					$out .= "]]></text>";

				}
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					/**
					* any documents to review ???
					*/
					$now = $this->libertasGetDate("Y/m/d H:i:s");
					// should not need 'trans_current_working_version=1 and ' in sql
					$sql    = "select * from page_trans_data 
								inner join available_languages on trans_language = language_code 
								$join2 
								where 
								trans_current_working_version=1 and trans_client =$this->client_identifier and trans_published_version=1 and (trans_date_available > '$now') $where";
					if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
					$result = $this->call_command("DB_QUERY",Array($sql));
					$out .= "<text><![CDATA[";
					$num = $this->call_command("DB_NUM_ROWS", array($result));
					if ($num>0){
						$out .= "<a href='admin/index.php?command=PAGE_LIST&amp;filter=available&amp;lock=1'>";
						$out .= "Pages with date embargo ($num)";
						$out .= "</a>";
					} else {
						$out .= "Pages with date embargo (0)";
					}
					$out .= "]]></text>";
				}
				return "<module name=\"pages\" label=\"".MANAGEMENT_PAGE."\" display=\"my_workspace\">".$out."1</module>";
			} else {
				$user   = $_SESSION["SESSION_USER_IDENTIFIER"];
				if ($this->module_admin_access){
					$out="<cmd label=\"".LOCALE_LIST."\">PAGE_LIST</cmd>";
				}
				$sql    = "select * from page_trans_data inner join available_languages on trans_language = language_code where trans_client =$this->client_identifier and trans_current_working_version=1 and trans_doc_status = 1";
				if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
				$result = $this->call_command("DB_QUERY",Array($sql));
				$out.="<cmd label=\"".ADD_NEW."\">PAGE_ADD</cmd>";
				$out .= "<text><![CDATA[";
					$num = $this->call_command("DB_NUM_ROWS", array($result));
					if ($num>0){
						$out .= "<a href='admin/index.php?command=PAGE_LIST&amp;status_filter=1&amp;user=$link_user'>";
						$out .= "Pages in checked out/draft ($num)" ;
						$out .= "</a>";
					} else {
						$out .= "Pages in checked out/draft (0)";
					}
					$out .= "]]></text>";
				return "<module name=\"pages\" label=\"".MANAGEMENT_PAGE."\" display=\"my_workspace\">".$out."</module>";
			}
		}else{
			return "";
		}
	}

	function extract_list_vesions($parameters){
		/**
		* this function will allow the user to access previously archived versions of a specific document
		-
		* the user will be given a list of page titles and their versions in order from newest to oldest 
		* which they can then select to view or extract as the new working copy
		*/	
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$variables=Array();
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary"
			)
		);
		$sql= "select 
					trans_identifier, 
					trans_page, 
					trans_title,
					".$summary_parts["return_field"].",
					trans_doc_version_major,
					trans_doc_version_minor,
					trans_date_modified,
					trans_published_version,
					trans_current_working_version,
					trans_doc_lock_to_user
				from page_trans_data 
				".$summary_parts["join"]."
				where 
					
					trans_client = $this->client_identifier and 
					trans_page = $identifier 
					".$summary_parts["where"]."
				order by 
					trans_doc_version_major desc,
					trans_doc_version_minor desc
					";
		$result = $this->call_command("DB_QUERY", array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"extract_vesions",__LINE__,$sql));
		}
		$variables["FILTER"]			= "";//$this->filter($parameters,"PAGE_LIST");
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		$variables["PAGE_COMMAND"]		="PAGE_LIST_VERSIONS&amp;identifier=$identifier";
		$variables["PAGE_BUTTONS"] = Array(Array("CANCEL","PAGE_LIST","LOCALE_CANCEL",""));
		
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
			}
			if ($goto+$this->page_size>$number_of_records){
				$finish = $number_of_records;
			}else{
				$finish = $goto+$this->page_size;
			}
			$goto++;
			$page++;
			
			$num_pages=floor($number_of_records / $this->page_size);
			$remainder = $number_of_records % $this->page_size;
			if ($remainder>0){
				$num_pages++;
			}
			
			$counter=0;
		
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			
			$start_page=intval($page / $this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+$this->page_size)>$num_pages){
				$end_page	 =	$num_pages;
			}else{
				$end_page	=	$this->page_size;
			}
			
			$variables["END_PAGE"]			= $end_page;
			
			
			$variables["RESULT_ENTRIES"] =Array();
			$c=0;
			while (($r=$this->call_command("DB_FETCH_ARRAY", array($result))) && ($c<$this->page_size)){
				$c++;
				$i = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$i]=Array(
					"identifier"	=> $r["trans_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(ENTRY_TITLE,$r["trans_title"],"TITLE","NO"),
						Array(ENTRY_SUMMARY,$r["trans_summary1"],"SUMMARY","NO"),
						Array(MAJOR_VERSION_MINOR_VERSION,$r["trans_doc_version_major"].".".$r["trans_doc_version_minor"]),
						Array(ENTRY_DATE_MODIFIED,$r["trans_date_modified"])
					)
				);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	= Array("PREVIEW","PAGE_PREVIEW",ENTRY_PREVIEW,"admin/preview.php");
				if ($r["trans_current_working_version"]!=1) {
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	= Array("LOCALE_COPY_THIS_VERSION","PAGE_COPY_VERSION",LOCALE_COPY_THIS_VERSION);
				}
				if (($this->publisher_access==1) && ($r["trans_doc_version_minor"]==0) && ($r["trans_published_version"]==0)){
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	= Array("PUBLISH","",ENTRY_PUBLISH);
				}
				if (($r["trans_published_version"]!=1) && ($this->publisher_access==1)){
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])] = Array("REMOVE", "PAGE_REMOVE_CONFIRM&amp;next_command=PAGE_LIST_VERSIONS&amp;id=$identifier", REMOVE_EXISTING);
				}
				if($r["trans_published_version"]==1){
					if($this->publisher_access==1){
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])]=Array(ENTRY_PUBLISHED,LOCALE_REMOVE_FROM_SITE,"YES","UNPUBLISH_THIS_DOCUMENT");
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])]=Array("UNPUBLISH_THIS_DOCUMENT","?command=PAGE_UNPUBLISH_COMPLETELY&identifier=".$r["trans_identifier"],"No","NO");
					} else {
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])]=Array(ENTRY_PUBLISHED,LOCALE_CURRENTLY_ON_SITE,"YES","NO");
					}
				}
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}

	
	function add_A2Z_entries($parameters){
		$root		= $this->check_parameters($this->parent->site_directories,"ROOT");
		$menu_url	= $this->check_parameters($parameters,"menu_url");
		$directory	= dirname($menu_url);
		for( $index = 97 ; $index < 123 ; $index++ ){
			$fname = $root."/".$directory."/_".chr($index).".php";
			$fp = fopen($fname,"w");
			fputs($fp,"<"."?php\n
				\$mode			= \"EXECUTE\";
				\$command		= \"PRESENTATION_ATOZ\";
				\$fake_uri 		= \"$menu_url\";
				\$fake_title	= \"".strtoupper(chr($index))."\";
				\$extra	=Array(\"letter\"	=> \"".chr($index)."\");
				require_once \"".$root."/admin/include.php\"; 
				require_once \"\$module_directory/included_page.php\";
				?".">");
			fclose($fp);
			$um = umask(0);
			@chmod($fname, LS__FILE_PERMISSION);
			umask($um);

		}
		$fname = $root."/".$directory."/_undefined.php";
		$fp = fopen($fname,"w");
		fputs($fp,"<"."?php\n
			\$mode			= \"EXECUTE\";
			\$command		= \"PRESENTATION_ATOZ\";
			\$fake_uri 		= \"$menu_url\";
			\$extra	=Array(\"letter\"	=> \"#\");
			require_once \"".$root."/admin/include.php\"; 
			require_once \"\$module_directory/included_page.php\";
			?".">");
		fclose($fp);
		$um = umask(0);
		@chmod($fname, LS__FILE_PERMISSION);
		umask($um);

	}
	
	function remove_A2Z_entries($parameters){
		$root		= $this->check_parameters($this->parent->site_directories,"ROOT");
		$menu_url	= $this->check_parameters($parameters,"menu_url");
		$directory	= dirname($menu_url);
		for( $index = 97 ; $index < 123 ; $index++ ){
			$fname = $root."/".$directory."/_".chr($index).".php";
			@unlink($fname);
		}
		$fname = $root."/".$directory."/_undefined.php";
		@unlink($fname);
	}
	
	function email_alerts($parameters){
/*	define("__EMAIL_APPROVER__",	"__EMAIL_APPROVER__");
		define("__EMAIL_PUBLISHER__",	"__EMAIL_PUBLISHER__");

		$type		= $this->check_parameters($parameters,"type");
		$identifier	= $this->check_parameters($parameters,"identifier",-1);
		if ($identifier==-1){
			return -1;
		} else {
			$sql = "select * from menu_access_to_page where trans_identifier=$identifier and client_identifier =$this->client_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$menu_list = ",".$r["menu_identifier"];
            }

            $this->call_command("DB_FREE",Array($result));
			if ($type == __EMAIL_APPROVER__){
				$cmd = "PAGE_APPROVER";
			} 
			if ($type == __EMAIL_PUBLISHER__){
				$cmd = "PAGE_PUBLISHER";
			}
			$sql = "select distinct groups_belonging_to_user.user_identifier, email_address from group_data  
  inner join group_access on access_group = group_data.group_identifier and access_code in ('$cmd',  'ALL', 'PAGE_ALL')
  inner join groups_belonging_to_user on groups_belonging_to_user.group_identifier= group_data.group_identifier and group_client=groups_belonging_to_user.client_identifier
  left outer join relate_user_menu on relate_user_menu.user_identifier= groups_belonging_to_user.user_identifier and menu_identifier in (NULL $menu_list)
  left outer  join contact_data on contact_user = groups_belonging_to_user.user_identifier and contact_client = groups_belonging_to_user.client_identifier
  left outer join email_addresses on contact_identifier = email_contact and email_client = contact_client
where group_client=$this->client_identifier";
			print $sql;
			
		}
		*/
	}
	function recache_this_page($id){
		
	}
	function page_clone($parameters){
		$debug = $this->debugit(false,$parameters);
		$next_command = $this->check_parameters($parameters,"next_command","__NOT_DEFINED__");
		$id = $this->check_parameters($parameters,"identifier",-1);
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		$page_id=-1;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"page_copy",__LINE__,$id));
		}
		if ($id!=-1){
			$sql ="select 
						t1.trans_doc_version_major as ver_max,
						t1.trans_doc_version_minor as ver_min,
						t1.trans_page as page_id
					from page_trans_data as t1
						inner join page_trans_data as t2 on t1.trans_page = t2.trans_page
					where 
						t1.trans_client = $this->client_identifier and 
						t2.trans_identifier = $id
					order by 
						t1.trans_doc_version_major desc,
						t1.trans_doc_version_minor desc";
			if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"page_copy",__LINE__,$sql));
			}
			$result = $this->call_command("DB_QUERY", array($sql));
			$max_major = -1;
			$max_minor = -1;
			$c=0;
			while (($r=$this->call_command("DB_FETCH_ARRAY", array($result))) && ($c<1)){
				$max_major	= $r["ver_max"];
				$page_id	= $r["page_id"];
				$max_minor	= $r["ver_min"] + 1 ;
				$c++;
			}
			$sql = "select * from page_data where page_identifier = $page_id and page_client=$this->client_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$page_overall_status	= $r["page_overall_status"];
				$page_is_title_doc		= $r["page_is_title_doc"];
				$page_admin_discussion	= $r["page_admin_discussion"];
				$page_web_discussion	= $r["page_web_discussion"];
            }
            $this->call_command("DB_FREE",Array($result));
			$page_date_creation			=	$now;
			$page_created_by_user		=	$_SESSION["SESSION_USER_IDENTIFIER"];
			$insert = "insert into page_data (page_client, page_overall_status, page_date_creation, page_created_by_user, page_is_title_doc, page_admin_discussion, page_web_discussion) values($this->client_identifier, $page_overall_status, '$page_date_creation', $page_created_by_user, $page_is_title_doc, $page_admin_discussion, $page_web_discussion)";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"$insert"));}
			$this->call_command("DB_QUERY",Array($insert));
			$select = "select * from page_data 
						where 
							page_client				= $this->client_identifier and 
							page_overall_status		= $page_overall_status and 
							page_is_title_doc		= $page_is_title_doc and 
							page_admin_discussion	= $page_admin_discussion and 
							page_web_discussion		= $page_web_discussion and 
							page_date_creation		= '$now' and 
							page_created_by_user	= ".$_SESSION["SESSION_USER_IDENTIFIER"]."
					  ";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"$select"));}
			$result  = $this->call_command("DB_QUERY",Array($select));
			$new_page_identifier = -1;
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$new_page_identifier = $r["page_identifier"];
            }
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"new_page_identifier",__LINE__,"$new_page_identifier"));}
            $this->call_command("DB_FREE",Array($result));
			$new_trans_id = $this->clone_new_version($id, -2,$new_page_identifier);
			$sql = "update page_trans_data set trans_doc_version_major = 0, trans_doc_version_minor = 1, trans_current_working_version = 1, trans_published_version = 0, trans_page = $new_page_identifier where trans_client = $this->client_identifier and trans_identifier = $new_trans_id";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY", array($sql));
			return $new_trans_id;
		}
	}
	
	function check_title($t, $tid, $menu_locations, $pid){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"$t, $tid, $menu_locations, $pid"));}
//		print "$t, $tid, $menu_locations, $pid";
		$url = $this->make_uri($t).".php";
		$sql = "select distinct trans_page 
				 from page_trans_data 
					inner join menu_access_to_page on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier
				where 
					menu_identifier in ($menu_locations -1) and 
					(trans_current_working_version='1' or trans_published_version=1)and 
					trans_dc_url='$url' and 
					trans_client=$this->client_identifier and 
					trans_page != $pid";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
//		print $sql;
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$ok = 1;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$ok = 0;
        }
        $this->call_command("DB_FREE",Array($result));
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"check if title exists",__LINE__,"$ok"));}
		return $ok;
	}
	
	function update_url($parameters){
		$page  = $this->check_parameters($parameters,"page",0);
		$sql = "select * from page_trans_data";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$url = $this->make_uri($r["trans_title"]);
        	$sql = "update page_trans_data set trans_dc_url = '$url.php' where trans_identifier=".$r["trans_identifier"];
			$this->call_command("DB_QUERY",Array($sql));
        }
        $this->call_command("DB_FREE",Array($result));
		return "";
	}
}
?>