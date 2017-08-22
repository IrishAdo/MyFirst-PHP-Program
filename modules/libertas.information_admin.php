<?PHP
/*************************************************************************************************************************
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.information_admin.php
* @date 12 Feb 2004
*************************************************************************************************************************/
/*************************************************************************************************************************
* This module is the administration module for the information directory.
*************************************************************************************************************************/
class information_admin extends module{
	/*************************************************************************************************************************
	*  Class Variables
	*************************************************************************************************************************/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name_label			= "Database Manager Module (Administration)";
	var $module_name				= "information_admin";
	var $module_admin				= "1";
	var $module_command				= "INFORMATIONADMIN_"; 		// all commands specifically for this module will start with this token
	var $module_presentation		= "INFORMATION_"; 		// all commands specifically for this module will start with this token
	var $module_presentation_name	= "information_presentation";
	var $webContainer				= "INFORMATIONADMIN_";
	var $module_label				= "MANAGEMENT_INFORMATION";
	var $module_modify		 		= '$Date: 2005/03/02 12:38:58 $';
	var $module_version 			= '$Revision: 1.95 $';
	var $module_creation 			= "26/02/2004";
	var $searched					= 0;
	var $loadedcat					= Array();
	var $module_setup				= "Database Builder";
	/*************************************************************************************************************************
    * extra commands defined by
    *************************************************************************************************************************/
	var $extra_commands 			= Array();
	/*************************************************************************************************************************
    * define list of display options
    *************************************************************************************************************************/
	var $info_summary_layout_options_values = Array(0, 1, 3, 2 );
	var $info_summary_layout_options_labels = Array("Display in defined structure", "Display in a table (sortable)", "Display in a table (non-sortable)", "Display an A to Z formatted directory");

	/*************************************************************************************************************************
	* List of available fields
	*
	* Set Defaults for fields.
	*  0 = Field label,
	*  1 = Rank,
	*  2 = Description,
	*  3 = Selected,
	*  4 = Type
	*************************************************************************************************************************/
	var $fields = Array();
	/*************************************************************************************************************************
    * define database tab one fields status
    *************************************************************************************************************************/
	var $setup_screen = Array(
		"ADDTOBASKET"	=> Array("visible"=>"Yes", "default"=>"LOCALE_DIRECTORY_ECOMMERCE_ADD_LABEL_DEFAULT", "LOCALE"=>1),
		"FULLYBOOKED"	=> Array("visible"=>"Yes", "default"=>"LOCALE_DIRECTORY_ECOMMERCE_NOSTOCK_LABEL_DEFAULT", "LOCALE"=>1),
		"NOSTOCK"		=> Array("visible"=>"Yes", "default"=>1, "LOCALE"=>0),
		"INMENU"		=> Array("visible"=>"Yes", "default"=>0, "LOCALE"=>0)
	);
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	var $user_fields = Array(
		/*
			closed fields (you can have only one)
		*/
		"ie_title"		=> Array("Title", 				-1, "Title of Entry", 											0, "text",			"value"=>"", "type"=>"defined", "group" => "General List", 		"searchable" => 1, "map" => "md_title"),
		"ie_summary"	=> Array("Summary", 			-1, "Short Description try to keep it below (200 characters)",	0, "smallmemo",		"value"=>"", "type"=>"defined", "group" => "General List", 		"searchable" => 1, "map" => "md_description"),
		"ie_content"	=> Array("Description",			-1, "Long Description", 										0, "memo",			"value"=>"", "type"=>"defined", "group" => "General List", 		"searchable" => 1, "map" => ""),
		"ie_uri"		=> Array("Web URL",				-1, "Web page Url (include http://)",							0, "URL",			"value"=>"", "type"=>"defined", "group" => "General List", 		"searchable" => 1, "map" => ""),
//		"ie_entries"	=> Array("Associate Directory Entries",
//														-1, "Associate a list of directory entries with this entry",	0, "associated_entries",	"value"=>"", "type"=>"defined", "group" => "General List", "searchable"=>0, "map"=>""),
		"ie_files"		=> Array("Associate files",		-1, "Associate a list of files with this entry",				0, "associations",	"value"=>"", "type"=>"defined", "group" => "General List", 		"searchable" => 1,	"map" => ""),
//		"ie_embedimage"	=> Array("Associate Image",		-1,
//			"Associate a thumbanil and a main image, which will be displayed embedded on the screen. <strong>NOTE:</strong> If you do not specify a thumbnail then no popup image is available",
//				0, "imageembed",	"value"=>"", "type"=>"defined", "group" => "General List", "searchable"=>0, "map"=>""),
		/* Ecommerce */
		"ie_price"		=> Array("Price",				-1, "Price of item", 											0, "double",	 	"value"=>"", "type"=>"metadata", "group" => "Ecommerce Fields", "searchable" => 0,	"map"=>"md_price"),
		"ie_vat"		=> Array("Charge for VAT/Sales tax",
														-1, "Charge VAT on this item (Y/N)",							0, "boolean",	 	"value"=>"", "type"=>"metadata", "group" => "Ecommerce Fields", "searchable" => 0,	"map"=>"md_vat"),
		"ie_discount"	=> Array("Discount",			-1, "Discount available for this item",		 					0, "double", 		"value"=>"", "type"=>"metadata", "group" => "Ecommerce Fields", "searchable" => 0,	"map"=>"md_discount"),
		"ie_weight"		=> Array("Weight",				-1, "Weigth of item",						 					0, "double", 		"value"=>"", "type"=>"metadata", "group" => "Ecommerce Fields", "searchable" => 0,	"map"=>"md_weight"),
		"ie_quantity"	=> Array("Number available",	-1, "Quantity of items in stock",				 				0, "integer", 		"value"=>"", "type"=>"metadata", "group" => "Ecommerce Fields", "searchable" => 0,	"map"=>"md_quantity"),
		"ie_canbuy"		=> Array("Accept online payment",-1, "Can this item be added to a basket",				 		0, "boolean", 		"value"=>"", "type"=>"metadata", "group" => "Ecommerce Fields", "searchable" => 0,	"map"=>"md_canbuy"),
		/* open fields (you can have as many as you want) */
		"ie_otext"		=> Array("Free text", 			-1, "Text input box",	 										0, "text",			"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_odateonly"	=> Array("Date field", 			-1, "Request a Date",	 										0, "date",			"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_odate"		=> Array("Date and Time field", -1, "Request a Date and Time",									0, "datetime",		"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_otime"		=> Array("Time field", 			-1, "Request a Time",	 										0, "time",			"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_email"		=> Array("Email address", 		-1, "An email address",	 										0, "email",			"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_osmallmemo"	=> Array("Short memo", 			-1, "Ideal for short info like address",						0, "smallmemo",		"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_omemo"		=> Array("Long memo", 			-1, "Ideal for long information",								0, "memo",			"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_oradio"		=> Array("Radio option list", 	-1, "Radio option List", 										0, "radio",			"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_oselect"	=> Array("Drop down list", 		-1, "Single select drop down",									0, "select",		"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_ocheckbox"	=> Array("Checkbox list",		-1, "Check box List", 											0, "check",			"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_olist"		=> Array("Select list combo",	-1, "List of options Multi Select",								0, "list",			"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
//		"ie_olinks"		=> Array("Multi Links", 		-1, "Define a list of clickable urls",							0, "links",			"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_oURL"		=> Array("URL",			 		-1, "Define a URL",												0, "URL",			"value"=>"", "type"=>"open", "group" => "Extra Fields List", 	"searchable" => 1,	"map"=>""),
		"ie_splitterCol"=> Array("Column splitter", 	-1, "Add new Column in table row",								0, "colsplitter",	"value"=>"", "type"=>"open", "group" => "Formatting List",		"searchable" => 0, 	"map"=>"__NOT__"),
		"ie_splitterRow"=> Array("Row splitter", 		-1, "Add new Row in table row",									0, "rowsplitter",	"value"=>"", "type"=>"open", "group" => "Formatting List",		"searchable" => 0, 	"map"=>"__NOT__"),
		"ie_image"      => Array("Embedded image", 		-1, "Add an image to this record",								0, "image",			"value"=>"", "type"=>"open", "group" => "Extra Fields List",	"searchable" => 0, 	"map"=>"")
	);

	var $metadata_fields = Array();

	var $prev_import_row = Array();
	var $current_import_row = Array();
	var $duplicate_import_row = Array();
	
	/*************************************************************************************************************************
	* Management Menu entries
	*************************************************************************************************************************/
	var $module_admin_options 		= array();
	/*************************************************************************************************************************
	*  Group access Restrictions, restrict a group to these command sets
	*************************************************************************************************************************/
	var $module_admin_user_access	= array();
	/*************************************************************************************************************************
	*  Channel options
	*************************************************************************************************************************/
	var $module_display_options 	= array();
	/*************************************************************************************************************************
	* WebObject entries
	*
	* Each Array has (Type, Label, Command, All locations, Has label)
	*
	* Type: - 0 = User defined, 1 = Channel type Web object, 2 = XSL defined WEbObject)
	*
	* Channels extract information from the system wile XSl defined are functions in the
	* XSL display.
	*************************************************************************************************************************/
	var $WebObjects				 	= Array();
	var $special_webobjects			= Array();
	/*************************************************************************************************************************
	*  filter options
	*************************************************************************************************************************/
	var $display_options			= array();
	
	/*************************************************************************************************************************
	*  Access options php 5 will allow these to become private variables.
	*************************************************************************************************************************/
	var $admin_access						= 0; // can access admin
	var $manage_database_list				= 0; // can manage defined databases (ADD, EDIT, REMOVE)
	var $manage_database_history			= 0; // manage the history of the database entries
	var $manage_database_filters			= 0; // manage the database filters
	var $manage_database_searches		 	= 0; // can manage the search locations for databases
	var $install_access						= 0; 
	var $database_import_access				= 0; // can import/export data to a dababase
	var $author_admin_access				= 0; // can add entries to a database
	var $approve_admin_access				= 0; // can approve entries on database
	var $manage_database_field_protection	= 0; // define field protection
	/*************************************************************************************************************************
	*  Class Methods
	*************************************************************************************************************************/
	
	function command($user_command, $parameter_list = array()){
		/*************************************************************************************************************************
		* If debug is turned on then output the command sent and the parameter list too.
		*************************************************************************************************************************/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,print_r($parameter_list,true),__LINE__,"command"));
		}
		/*************************************************************************************************************************
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*************************************************************************************************************************/
		if (strpos($user_command, $this->module_command)===0){
			if ($user_command==$this->module_command."DEBUG_ON"){
				$this->module_debug=true;
			}
			if ($user_command==$this->module_command."DEBUG_OFF"){
				$this->module_debug=false;
			}
			if ($user_command==$this->module_command."GET_MODULE"){
				return $this->get_module_name();
			}
			if ($user_command==$this->module_command."GET_VERSION"){
				return $this->module_version;
			}
			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
				return $this->webContainer;
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."FIND_PATH"){
				return $this->find_path($parameter_list[0]);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/*************************************************************************************************************************
			* Create table function allow access if in install mode
			*************************************************************************************************************************/
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."CACHE_ENTRY"){
				return $this->cache_entry($parameter_list);
			}
			if($user_command==$this->module_command."EXTRACT_COPY"){
				$id = $this->copy_entry($parameter_list);
			}
			if($user_command==$this->module_command."EXTRACT_LIST"){
				return $this->extract_entry_list($parameter_list); // cache extract function
			}
			if ($user_command == $this->module_command."GET_FIELD_LIST"){
				return $this->get_field_list($parameter_list);
			}
			if ($user_command == $this->module_command."GET_FIELD_OPTIONS"){
				return $this->get_field_options($parameter_list);
			}
			if ($user_command == $this->module_command."COPY_LIST_INTO_CONTACT"){
				return $this->copy_list_into_contact($parameter_list);
			}
			if ($user_command == $this->module_command."COPY_LIST_INTO_CONTACT_UUP"){
				return $this->copy_list_into_contact_uup($parameter_list);
			}
			
			if ($user_command == $this->module_command."PRINT_LABELS"){
				return $this->print_labels($parameter_list);
			}
			if ($user_command == $this->module_command."PRINT_LABELS_EXPORT_HANDBOOK"){
				return $this->print_labels_export_handbook($parameter_list);
			}
			if ($user_command == $this->module_command."PRINT_LABELS_EXPORT"){
				return $this->print_labels_export($parameter_list);
				//return $this->print_labels_export_complete_members_database($parameter_list);
			}
			if ($user_command == $this->module_command."PRINT_LABELS_YACHTS"){
				return $this->print_labels_yachts($parameter_list);
			}
			if ($user_command == $this->module_command."PRINT_LABELS_YEARBOOK"){
				return $this->print_labels_yearbook($parameter_list);
			}
			if ($user_command == $this->module_command."MEMBER_EMAIL_BODY"){
				return $this->member_email_body($parameter_list);
			}
			if ($user_command == $this->module_command."MEMBER_EMAIL_SEND"){
				return $this->member_email_send($parameter_list);
			}
			if ($user_command == $this->module_command."COPY_USER_INFO_INTO_CONTACT_DATA"){
				return $this->copy_user_info_into_contact_data($parameter_list);
			}
			
			
			/*************************************************************************************************************************
			* Administration Module commands
			*************************************************************************************************************************/
			if ($this->admin_access==1){
				/*************************************************************************************************************************
				* What channels are available to the system
				*************************************************************************************************************************/
				if ($user_command == $this->module_command."MENU_DISPLAY_OPTIONS"){
					return $this->display_channels($parameter_list);
				}
				if ($user_command==$this->module_command."TEST_QUERY"){
					return $this->test_query($parameter_list);
				}
				if ($user_command==$this->module_command."LIST_DATABASES"){
					return $this->module_command;
				}
				/*************************************************************************************************************************
				* Category List Setup and management
				*************************************************************************************************************************/
				if ($this->manage_database_field_protection == 1){
					if ($user_command==$this->module_command."FIELD_PROTECTION"){
						return $this->protect_fields($parameter_list);
					}
					if ($user_command==$this->module_command."FIELD_PROTECTION_SAVE"){
						return $this->protect_fields_save($parameter_list);
					}
				}
				/*************************************************************************************************************************
                * 												Entry management functions 
                *************************************************************************************************************************/
				if($this->author_admin_access == 1 || $this->approve_admin_access == 1){
					if ($user_command==$this->module_command."LIST_ENTRIES"){
						return $this->list_entries($parameter_list);
					}
					if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
						return $this->information_entry_modify($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE"){
						return $this->information_removal($parameter_list);
					}
					if ($user_command==$this->module_command."SAVE_ENTRY"){
						return $this->save_entries($parameter_list);
					}
					if ($user_command==$this->module_command."CACHE_ENTRIES"){
						$this->cache_entries($parameter_list);
					}
					if ($user_command==$this->module_command."RESTORE_ENTRIES"){
						$this->restore_entries($parameter_list);
					}
				}
					if ($user_command==$this->module_command."SAVE_A2Z"){
						$this->widget_atoz($parameter_list);
					}
				
				/*************************************************************************************************************************
				* List Import/Export  Management Access
                *
                * this functionality will allow you to modify import or export too / from a specific database
				*************************************************************************************************************************/
				if($this->database_import_access){
					/*************************************************************************************************************************
                    * Export Content functions
                    *************************************************************************************************************************/
					if (($user_command==$this->module_command."EXPORT")){
						return $this->export($parameter_list);
					}
					if (($user_command==$this->module_command."EXPORT_CONFIRMED")){
						return $this->export_data($parameter_list);
					}
					if (($user_command==$this->module_command."EXPORT_DATA_HIDDEN")){
						return $this->export_data_hidden($parameter_list);
					}
					if (($user_command==$this->module_command."EXPORT_DOWNLOAD")){
						return $this->export_download($parameter_list);
					}
					/*************************************************************************************************************************
                    * Import Content functions
                    *************************************************************************************************************************/
					if (($user_command==$this->module_command."IMPORT")){
						return $this->importform($parameter_list);
					}
					if ($user_command==$this->module_command."IMPORT_FILE"){
						return $this->importfile($parameter_list);
					}
					if ($user_command==$this->module_command."EXAMINE_IMPORT_FILE"){
						return $this->examine_import($parameter_list);
					}
					
					/***")){
						return $this->importform($parameter_list);
					}
					if ($user_command==$this->module_command."IMPORT_FILE"){
						return $this->importfile($parameter_list);
					}
					if ($user_command==$this->module_command."EXAMINE_IMPORT_FILE"){
						return $this->examine_import($parameter_list);
					}
					
					/*************************************************************************************************************************
                    * IMPORT / EXPORT
                    *************************************************************************************************************************/
					if ($user_command==$this->module_command."IMPORT_CONFIRM"){
						return $this->import_confirm($parameter_list);
					}
				}
				/*************************************************************************************************************************
				* Database  Management Access
                *
                * this functionality will allow you to modify the Database list details
				*************************************************************************************************************************/
				if ($this->manage_database_list){
					/*************************************************************************************************************************
                    * Entry functions
                    *************************************************************************************************************************/
					if ($user_command==$this->module_command."CACHE_ENTRY_CATEGORY"){
						return $this->cache_entry_category($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE_ALL_ENTRIES"){
						return $this->remove_all_entries($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE_ALL_ENTRIES_CONFIRMED"){
						$this->remove_all_entries_confirmed($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
					/*************************************************************************************************************************
                    *											manage the directory setup
                    *************************************************************************************************************************/
					if ($user_command==$this->module_command."LIST"){
						return $this->information_list($parameter_list);
					}
					if ($user_command==$this->module_command."PREVIEWFORM"){
						return $this->previewform($parameter_list);
					}
					if (($user_command==$this->module_command."LIST_EDIT") || ($user_command==$this->module_command."LIST_ADD")){
						return $this->information_list_modify($parameter_list);
					}
					if ($user_command==$this->module_command."LIST_REMOVE"){
						$this->information_list_removal($parameter_list);
						$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
						@unlink("$data_files/layout_".$this->client_identifier."_admin.xml");
						$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST"));
					}
					if ($user_command==$this->module_command."LIST_SAVE"){
						$id = $this->check_parameters($parameter_list, "identifier",-1);
						$this->information_save($parameter_list);
						if($id==-1){ // add then update admin_menu
							$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
							@unlink("$data_files/layout_".$this->client_identifier."_admin.xml");
						}
						$next = $this->check_parameters($parameter_list,"next");
						if ($next!=""){
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$next."&amp;identifier=".$this->check_parameters($parameter_list,"cat")."&amp;list_id=".$this->check_parameters($parameter_list,"list_id")));
						} else {
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
						}
					}
					if ($user_command==$this->module_command."SAVE"){
						$this->information_save($parameter_list);
						$list_id = $this->check_parameters($parameter_list, "list_id", -1);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST_EDIT&amp;identifier=$list_id&recache=1"));
					}
					/*************************************************************************************************************************
                    * system functions 
                    *************************************************************************************************************************/
					if ($user_command==$this->module_command."RESTORE"){
						$this->restore($parameter_list);
					}
					if ($user_command==$this->module_command."RENAMEMOVE"){
						$this->rename_or_move($parameter_list);
					}
				}
				/*************************************************************************************************************************
				* history Management Access
                *
                * this functionality will allow you to manage the version controled data 
				*************************************************************************************************************************/
				if ($this->manage_database_history){
					/*************************************************************************************************************************
					* History
					*************************************************************************************************************************/
					if($user_command==$this->module_command."PREVIEW_HISTORY"){
						return $this->information_preview_history($parameter_list);
					}
					if($user_command==$this->module_command."ENTRY_HISTORY"){
						return $this->history($parameter_list);
					}
					if($user_command==$this->module_command."TIDYUP_HISTORY"){
						return $this->tidyup_history($parameter_list);
					}
					if($user_command==$this->module_command."COPY_HISTORY"){
						$id = $this->copy_entry($parameter_list);
						$list_id  = $this->check_parameters($parameter_list,"list_id");
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST_ENTRIES&amp;identifier=$list_id&recache=1"));
					}
					if($user_command==$this->module_command."TIDYUP_HISTORY_PROCESS"){
						return $this->tidyup_history_process($parameter_list);
					}
					/*************************************************************************************************************************
					*
					*************************************************************************************************************************/
					if($user_command==$this->module_command."REMOVE_LOST"){
						return $this->tidyup_lost($parameter_list);
					}
					if($user_command==$this->module_command."REMOVE_LOST_PROCESS"){
						return $this->tidyup_lost_process($parameter_list);
					}
					if ($user_command==$this->module_command."ENTRY_USERS"){
						return $this->entry_users($parameter_list);
					}
					if ($user_command==$this->module_command."ENTRY_USERS_SAVE"){
						return $this->entry_users_save($parameter_list);
					}
				}
				/*************************************************************************************************************************
				* Filter Management Access
                *
                * this functionality will allow you to manage the database filters
				*************************************************************************************************************************/
				if ($this->manage_database_filters){
					/*************************************************************************************************************************
                    * Featured list functions 
                    *************************************************************************************************************************/
					if (($user_command==$this->module_command."FEATURE_EDIT") || ($user_command==$this->module_command."FEATURE_ADD")){
						return $this->featured_modify($parameter_list);
					}
					if ($user_command==$this->module_command."FEATURE_REMOVE"){
						$this->featured_remove($parameter_list);
						$list_id  = $this->check_parameters($parameter_list,"list");
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."FEATURE_LIST&amp;identifier=$list_id"));
					}
					if ($user_command==$this->module_command."FEATURE_LIST"){
						return $this->featured_list($parameter_list);
					}
					if ($user_command==$this->module_command."FEATURE_SAVE"){
						$this->featured_save($parameter_list);
						$list_id  = $this->check_parameters($parameter_list,"ifeature_list");
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."FEATURE_LIST&amp;identifier=$list_id"));
					}
					if ($user_command==$this->module_command."GEN_SQL_CACHE"){
						return $this->gen_sql_cache($parameter_list);
					}
				}
				if ($this->manage_database_searches){
					/*************************************************************************************************************************
                    * BASIC Search management functions
                    *************************************************************************************************************************/
					if ($user_command==$this->module_command."BASIC_SEARCH"){
						return $this->manage_basic_search($parameter_list);
					}
					if ($user_command==$this->module_command."BASIC_SEARCH_SAVE"){
						$this->manage_basic_search_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
					/*************************************************************************************************************************
                    * Advanced Search management functions
                    *************************************************************************************************************************/
					if ($user_command==$this->module_command."ADVANCED_SEARCH"){
						return $this->manage_advanced_search($parameter_list);
					}
					if ($user_command==$this->module_command."ADVANCED_SEARCH_SAVE"){
						$this->manage_advanced_search_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
				}
			}
			$len = count($this->extra_commands);
			for($i=0;$i<$len;$i++){
				if($user_command==$this->extra_commands[$i][0]){
//					print "return \$this->".$this->extra_commands[$i][1]."(\$parameter_list);";
					$out="";
					eval("\$out =  \$this->".$this->extra_commands[$i][1]."(\$parameter_list);");
					return $out;
				}
			}
		}
		return "";
	}
	/*************************************************************************************************************************
	*                                D I R E C T O R Y   S E T U P   F U N C T I O N S
	*************************************************************************************************************************/
	
	/*************************************************************************************************************************
	* Initialise function
	* This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	* module::initialise() function allowing you to define any extra constructor
	* functionality.
	*************************************************************************************************************************/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		/*************************************************************************************************************************
		* request the client identifier once we use this variable often
		*************************************************************************************************************************/
		$this->client_identifier = $this->parent->client_identifier;
		/*************************************************************************************************************************
        * setup module independant commands and access
        *************************************************************************************************************************/
		$module_admin_user_access = Array();
		$module_admin_user_access[0] = array($this->module_command."ALL",			"COMPLETE_ACCESS");
		$module_admin_user_access[1] = array($this->module_command."LIST_CREATOR",	"ACCESS_LEVEL_LIST_AUTHOR");
		
		$this->module_display_options[count($this->module_display_options)]=  array($this->module_presentation."DISPLAY",			LOCALE_DISPLAY_INFORMATION);
		$this->module_display_options[count($this->module_display_options)]=  array($this->module_presentation."SEARCH",			LOCALE_DISPLAY_BASIC_SEARCH);
		$this->module_display_options[count($this->module_display_options)]=  array($this->module_presentation."ADVANCED_SEARCH",	LOCALE_DISPLAY_ADVANCED_SEARCH);
		$this->module_display_options[count($this->module_display_options)]=  array($this->module_presentation."FEATURES",			LOCALE_DISPLAY_FEATURES);
//		$this->module_display_options[count($this->module_display_options)]=  array($this->module_presentation."GET_A2Z",			LOCALE_DISPLAY_A2Z_WIDGET);
		/*************************************************************************************************************************
		* retrieve the metadata fields
		*************************************************************************************************************************/
		$this->metadata_fields					= $this->call_command("METADATAADMIN_GET_FIELDLIST", Array());
//		print_r($this->metadata_fields);
		/*************************************************************************************************************************
		* lock down the access
		*************************************************************************************************************************/
		$this->admin_access						= 0; // access to the admin functions
		$this->manage_database_list				= 0; // manage the database list
		$this->manage_database_history			= 0; // manage the history of items
		$this->manage_database_filters			= 0; // can manage the database filters
		$this->manage_database_searches 		= 0; // can manage the search locations for databases
		$this->manage_database_field_protection	= 0; // 
		$this->database_import_access			= 0; // can have access to the import/export functions
		$this->author_admin_access				= 0; // can add new entries in the admin of the database module
		$this->approve_admin_access				= 0; 
		$this->install_access					= 0; // access in the install mode
		/*************************************************************************************************************************
		* load the required locale
		*************************************************************************************************************************/
		$this->load_locale($this->module_name);
		/*************************************************************************************************************************
		* define the list of Editors in this module and define them as empty
		*************************************************************************************************************************/
		$this->editor_configurations = Array(
			"ENTRY_DESCRIPTION" => $this->generate_default_editor(),
			"ENTRY_CONFIRM_SCREEN" => $this->generate_default_editor()
		);
		/*************************************************************************************************************************
        * set up defaults
        *************************************************************************************************************************/
		$this->module_admin_options = array(
			array($this->module_command."LIST", $this->module_setup,$this->module_command."ALL","Preferences/Database Setup")
		);
		$this->module_admin_user_access = Array(
			array($this->module_command."ALL",		"COMPLETE_ACCESS",			""),
			array($this->module_command."AUTHOR",	"LOCALE_AUTHOR_ACCESS",		""),
			array($this->module_command."APPROVER",	"LOCALE_APPROVER_ACCESS",	"")
		);
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$this->special_webobjects			= Array(
			"ADD" => Array(
				"owner_module" 	=> "",
				"label" 		=> "Add",
				"wo_command"	=> $this->module_presentation."ADD_ENTRY",
				"file"			=> "_add.php",
				"available"		=> 1
			),
			"EDIT" => Array(
				"owner_module" 	=> "",
				"label" 		=> "Edit",
				"wo_command"	=> $this->module_presentation."ADD_ENTRY",
				"file"			=> "_edit.php",
				"available"		=> 1
			),
			"ADVANCEDSEARCH" => Array(
				"owner_module" 	=> $this->module_presentation."ADVANCED_SEARCH",
				"label" 		=> "Search",
				"wo_command"	=> $this->module_presentation."ADVANCED_SEARCH",
				"file"			=> "_search.php",
				"available"		=> 1
			),
			"A2Z" => Array(
				"owner_module" 	=> $this->module_presentation."A2Z",
				"label" 		=> "A to Z",
				"wo_command"	=> $this->module_presentation."A2Z",
				"file"			=> "_a2z.php",
				"available"		=> 1
			),
			"A2Z_WIDGET" => Array(
				"owner_module" 	=> $this->webContainer."A2Z_WIDGET",
				"label" 		=> "A to Z - Widget",
				"wo_command"	=> $this->module_presentation."GET_A2Z",
				"file"			=> "_a2zwidget.php",
				"available"		=> 1,
				"type"			=> 2
			),
			"RANKED" => Array(
				"owner_module" 	=> "",//$this->webContainer."_RANKED",
				"label" 		=> "Ranked",
				"wo_command"	=> $this->module_presentation."RANKED",
				"file"			=> "_ranked.php",
				"available"		=> 0
			),
			"REGISTER" => Array(
				"owner_module" 	=> "",
				"label" 		=> "Register",
				"wo_command"	=> $this->module_presentation."USER_REGISTER",
				"file"			=> "_user_reg.php",
				"available"		=> 2
			),
			"POPULAR" => Array(
				"owner_module" 	=> "",//$this->webContainer."_POPULAR",
				"label" 		=> "Most popular entries",
				"wo_command"	=> $this->module_presentation."POPULAR",
				"file"			=> "_popular.php",
				"available"		=> 1
			),
			"FEATURE" => Array()
		);

		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql = "select * from information_list where info_client=$this->client_identifier and info_owner = '$this->webContainer'";
        $result  = $this->parent->db_pointer->database_query($sql);
		$ids = Array();
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			// manage entries
        	$this->module_admin_options[count($this->module_admin_options)] 		= array($this->module_command."LIST_ENTRIES&amp;identifier=".$r["info_identifier"], $r["info_label"]."","".$this->module_command."APPROVER_".$r["info_identifier"]."|".$this->module_command."AUTHOR_".$r["info_identifier"],"Content Manage/".$this->get_constant($this->module_label));
			$this->module_admin_user_access[count($this->module_admin_user_access)] = array($this->module_command."AUTHOR_".$r["info_identifier"]			 , $r["info_label"]." - ".constant("ACCESS_LEVEL_AUTHOR")); 	 	// this will allow the user to add a new entry to the database
			$this->module_admin_user_access[count($this->module_admin_user_access)] = array($this->module_command."APPROVER_".$r["info_identifier"]			 , $r["info_label"]." - ".constant("ACCESS_LEVEL_APPROVER")); 		// this will allow the user to approve an entry for the database
			// manage field protection
			$this->module_admin_user_access[count($this->module_admin_user_access)] = array($this->module_command."FIELD_PROTECTION_".$r["info_identifier"]	 , $r["info_label"]." - ".constant("ACCESS_LEVEL_PROTECTOR")); 		// this will allow the user to approve an entry for the database
			// add feature list
			$this->special_webobjects["FEATURE"][count($this->special_webobjects["FEATURE"])] = Array(
				"owner_module" 	=> $this->module_presentation."SHOW_IT",
				"label" 		=> "Featured Company",
				"wo_command"	=> $this->module_presentation."SHOW_IT",
				"file"			=> "-/_feature-".$this->make_uri($r["info_label"]).".php",
				"available"		=> 1,
				"extra"			=> Array(
					"information_list" => $r["info_identifier"]
				)
			);
			// list ids
			$ids[count($ids)] = $r["info_identifier"];
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
		* request the page size
		*************************************************************************************************************************/
		
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		/*************************************************************************************************************************
		* define the admin access that this user has.
		*************************************************************************************************************************/
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$length_of_array=count($access);
			for ($index=0;$index<$length_of_array;$index++){
				if (($this->module_command."ALL"==$access[$index]) || ("ALL"==$access[$index])){
						$this->manage_database_list				= 1; // manage the database list
						$this->manage_database_history			= 1; // manage the history of items
						$this->manage_database_filters			= 1; // can manage the database filters
						$this->manage_database_searches			= 1; // can manage the search locations for databases
						$this->database_import_access			= 1; // access available to import/export
						$this->author_admin_access				= 1; // can add new entries in the admin of the database module
						$this->approve_admin_access				= 1; // can approve new entries in the admin of the database module
						$this->manage_database_field_protection	= 1; // can set field protection
						if ($this->parent->module_type=="install"){
							$this->install_access				= 1; // access in the install mode
						}
						if (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
							$this->admin_access					= 1; // can access the admin commands
						}
				} else {
					$options = split("_", $access[$index]);
					$identifier = $options[count($options)-1];
					unset($options[count($options)-1]);
					$accesscommand = join("_",$options);
					if(in_array($identifier,$ids)){
						if($this->module_command."AUTHOR"==$accesscommand){
							$this->author_admin_access		= 1; 
							if (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
								$this->admin_access				= 1; // can access the admin commands
							}
						}
						if ($this->module_command."APPROVER"==$accesscommand){
							$this->approve_admin_access		= 1; 
							if (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
								$this->admin_access				= 1; // can access the admin commands
							}
						}
						if ($this->module_command."FILTERMANAGER"==$accesscommand){
							$this->manage_database_filters		= 1; 
							if (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
								$this->admin_access				= 1; // can access the admin commands
							}
						}
						if ($this->module_command."HISTORYMANAGER"==$accesscommand){
							$this->manage_database_history		= 1; 
							if (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
								$this->admin_access				= 1; // can access the admin commands
							}
						}
						if ($this->module_command."SEARCHMANAGER"==$accesscommand){
							$this->manage_database_searches		= 1; 
							if (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
								$this->admin_access				= 1; // can access the admin commands
							}
						}
						if ($this->module_command."FIELD_PROTECTION"==$accesscommand){
							$this->manage_database_field_protection	= 1; // can set field protection
							if (($this->parent->module_type == "admin") || ($this->parent->module_type == "view_comments") || ($this->parent->module_type=="preview") || ($this->parent->module_type=="files")){
								$this->admin_access				= 1; // can access the admin commands
							}
						}
					}
				}
			}
		}
		return 1;
	}
	/*************************************************************************************************************************
	* function produces abstract structure of modules db structure
	*
    * used to generate the table structure required abstract function that passes an abstract
	* representation of the desired table structure to the proper database module which will
	* interpet the abstract and convert it into a valid SQL Create table structure.
	*
	* @return Array list of abstract table definitions for this module
	*************************************************************************************************************************/
	function create_table(){
		$tables = array();
		/*************************************************************************************************************************
		* Table structure for table 'information_list'
		*************************************************************************************************************************/

		$fields = array(
			array("info_identifier"			,"unsigned integer"			,"NOT NULL"	,"default '0'" ,"key"),
			array("info_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("info_category"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("info_label"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("info_creation_date"		,"datetime"					,"NOT NULL"	,"default '0000-00-00 00:00:00'"),
			array("info_status"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("info_workflow_status"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("info_display"			,"varchar(25)"				,"NOT NULL"	,"default 'display_2_lvl'"),
			array("info_columns"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("info_summary_layout"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("info_atoz_layout"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("info_update_access"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("info_vcontrol"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("info_menu_location"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("info_searchresults"		,"unsigned small integer"	,"NOT NULL"	,"default '10'"),
			array("info_shop_enabled"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("info_in_menu"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("info_cat_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("info_owner"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("info_add_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("info_no_stock_label"		,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("info_summary_only"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("info_no_stock_display"	,"unsigned small integer"	,"NOT NULL"	,"default '1'")
		);
		$primary ="info_identifier";
		$tables[count($tables)] = array("information_list", $fields, $primary);
		
		/*************************************************************************************************************************
		* Table structure for table 'information_data'
		*************************************************************************************************************************/
		$fields = array(
			array("id_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
	  		array("id_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("id_field"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
	  		array("id_value"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
	  		array("id_list"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("id_entry"			,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);
		$primary ="id_identifier";

		$tables[count($tables)] = array("information_data", $fields, $primary);

		$fields = array(
	  		array("ie_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
	  		array("ie_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
	  		array("ie_list"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
	  		array("ie_status"			,"unsigned small integer"	,"NOT NULL"	,"default '0'","key"),
	  		array("ie_uri"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
	  		array("ie_date_created"		,"datetime"					,"NOT NULL"	,"default '0000-00-00 00:00:00'"),
			array("ie_user"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ie_published"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ie_version_major"	,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ie_version_minor"	,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ie_version_wip"		,"unsigned integer"			,"NOT NULL"	,"default '1'","key"),
			array("ie_parent"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ie_cached"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ie_counter"			,"integer"					,"NOT NULL"	,"default '-1'","key")
		);
		$primary ="ie_identifier";
		$tables[count($tables)] = array("information_entry", $fields, $primary);

		$fields = array(
			array("iev_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("iev_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("iev_entry"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("iev_field"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("iev_value"			,"text"						,"NOT NULL"	,"default ''"),
			array("iev_list"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key")
		);
		$primary ="iev_identifier";
		$tables[count($tables)] = array("information_entry_values", $fields, $primary);

		$fields = array(
			array("ievl_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ievl_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ievl_entry"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ievl_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("ievl_value"			,"text"						,"NOT NULL"	,"default ''"),
			array("ievl_list"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ievl_rank"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key")
		);
		$primary ="ievl_identifier";
		$tables[count($tables)] = array("information_entry_links", $fields, $primary);


	
		$fields = array(
			array("if_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("if_name"				,"varchar(255)"				,"NOT NULL"	,"default ''", "key"),
			array("if_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("if_list"				,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("if_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("if_rank"				,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("if_screen"			,"unsigned small integer"	,"NOT NULL"	,"default '0'", "key"),
//			array("if_search_form"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("if_duplicate"		,"varchar(20)"				,"NOT NULL"	,"default ''"),
			array("if_filterable"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("if_sumlabel"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("if_conlabel"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("if_link"				,"unsigned small integer"	,"NOT NULL"	,"default '0'", "key"),
			array("if_type"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("if_map"				,"varchar(255)"				,"NOT NULL"	,"default ''"), // map to a specific metadata field
			array("if_special"			,"unsigned small integer"	,"NOT NULL"	,"default '0'", "key")
		);
		$primary ="if_identifier";
		$tables[count($tables)] = array("information_fields", $fields, $primary);

		$fields = array(
			array("ifp_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ifp_list"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ifp_field"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ifp_group"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key")
		);
		$primary ="";
		$tables[count($tables)] = array("information_field_protection", $fields, $primary);

	
		$fields = array(
			array("io_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("io_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("io_field"			,"varchar(255)"				,"NOT NULL"	,"default ''", "key"),
			array("io_value"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("io_list"				,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("io_rank"				,"unsigned small integer"	,"NOT NULL"	,"default '0'", "key")
		);
		$primary ="io_identifier";
		$tables[count($tables)] = array("information_options", $fields, $primary);


		$fields = array(
			array("ier_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ier_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ier_source_id"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ier_source_cat"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ier_dest_id"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ier_dest_cat"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ier_rank"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key")
		);
		$primary ="ier_identifier";
		$tables[count($tables)] = array("information_entry_relationship", $fields, $primary);


		$fields = array(
			array("iua_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("iua_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("iua_user"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("iua_entry"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("iua_list"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key")
		);
		$primary ="iua_identifier";
		$tables[count($tables)] = array("information_update_access", $fields, $primary);


		$fields = array(
			array("ifeature_identifier"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ifeature_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ifeature_status"				,"unsigned small integer"	,"NOT NULL"	,"default '0'", "key"),
			array("ifeature_list_type"			,"unsigned small integer"	,"NOT NULL"	,"default '0'", "key"), // 0 = manual, 1 = automatic
			array("ifeature_list"				,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"), // belongs to directory
			array("ifeature_display_format"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"), // 0 = summary, 1 = summary
			array("ifeature_display_rotation"	,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"), // 0 = per visit, 1 = per page
			array("ifeature_label"				,"varchar(255)"				,"NOT NULL"	,"default '0'", "key"), // 
			array("ifeature_auto_counter"		,"unsigned integer"			,"NOT NULL"	,"default '1'", "key"), // number of entries to display
			array("ifeature_date_created"		,"datetime"					,"NOT NULL"	,"default ''", ""),
			array("ifeature_date_start"			,"datetime"					,"NOT NULL"	,"default ''", ""),
			array("ifeature_date_finish"		,"datetime"					,"NOT NULL"	,"default ''", ""),
			array("ifeature_all_locations"		,"unsigned integer"			,"NOT NULL"	,"default '0'",""), // all menu loactions
			array("ifeature_set_inheritance"	,"unsigned integer"			,"NOT NULL"	,"default '0'",""), // child location inherit
			array("ifeature_as_rss"				,"unsigned small integer"	,"NOT NULL"	,"default '0'", "key")
		);
		$primary ="ifeature_identifier";
		$tables[count($tables)] = array("information_features", $fields, $primary);

		$fields = array(
			array("ifl_identifier"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ifl_client"					,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ifl_owner"					,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ifl_entry"					,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ifl_cat"						,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("ifl_rank"					,"unsigned integer"			,"NOT NULL"	,"default '0'", "key")
		);
		$primary ="";
		$tables[count($tables)] = array("information_feature_list", $fields, $primary);

		$fields = array(
			array("fieldname"		,"varchar(255)"			,"NOT NULL"	,"default ''"),
			array("counter"			,"unsigned integer"		,"NOT NULL"	,"default '0'"),
			array("useridentifier"	,"unsigned integer"		,"NOT NULL"	,"default '0'"),
			array("session"			,"varchar(32)"			,"NOT NULL"	,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("tmp_rowdata", $fields, $primary);
		/*************************************************************************************************************************
		* Search tables (basic and advanced
	*************************************************************************************************************************/ 
		$fields = array(
			array("ibs_identifier"		,"unsigned integer"		,"NOT NULL"	,"default '0'","key"),
			array("ibs_client"			,"unsigned integer"		,"NOT NULL"	,"default '0'","key"),
			array("ibs_list"			,"unsigned integer"		,"NOT NULL"	,"default '0'","key"),
			array("ibs_label"			,"varchar(255)"			,"NOT NULL"	,"default ''"),
			array("ibs_date_created"	,"datetime"				,"NOT NULL"	,"default ''"),
			array("ibs_all_locations"	,"unsigned integer"		,"NOT NULL"	,"default '0'","key"),
			array("ibs_set_inheritance"	,"unsigned integer"		,"NOT NULL"	,"default '0'","key")
		);
		$primary ="";
		$tables[count($tables)] = array("information_search", $fields, $primary);
		$fields = array(
			array("ias_identifier"		,"unsigned integer"		,"NOT NULL"	,"default '0'","key"),
			array("ias_client"			,"unsigned integer"		,"NOT NULL"	,"default '0'","key"),
			array("ias_list"			,"unsigned integer"		,"NOT NULL"	,"default '0'","key"),
			array("ias_label"			,"varchar(255)"			,"NOT NULL"	,"default ''"),
			array("ias_date_created"	,"datetime"				,"NOT NULL"	,"default ''"),
			array("ias_all_locations"	,"unsigned integer"		,"NOT NULL"	,"default '0'","key"),
			array("ias_set_inheritance"	,"unsigned integer"		,"NOT NULL"	,"default '0'","key")
		);
		$primary ="";
		$tables[count($tables)] = array("information_advanced_search", $fields, $primary);
		
		$fields = array(
			array("iffr_owner"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("iffr_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("iffr_list"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("iffr_rank"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("iffr_label_display"	,"unsigned small integer"	,"NOT NULL"	,"default '0'","key"),
			array("iffr_label_override"	,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("iffr_field"			,"varchar(255)"				,"NOT NULL"	,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("information_feature_field_rank", $fields, $primary);
		
		return $tables;
	}
	/*************************************************************************************************************************
	*              I N F O R M A T I O N   D I R E C T O R Y   M A N A G E R   F U N C T I O N S
	*************************************************************************************************************************/
	
	/*************************************************************************************************************************
	* information_list($parameters)
	*************************************************************************************************************************/
	function information_list($parameters){
		if($this->manage_database_list==0){
			return "";
		}
		$sql = "select information_list.info_shop_enabled, information_list.info_identifier, information_list.info_label, information_list.info_summary_layout, information_list.info_status, menu_data.*, count(ie_parent) as total  from information_list 
					inner join menu_data on menu_identifier = info_menu_location and menu_client = info_client 
					left outer join information_entry on ie_list=info_identifier and ie_client=info_client and ie_version_wip=1
				where information_list.info_owner='$this->webContainer' and information_list.info_client=$this->client_identifier group by info_identifier order by info_identifier desc";
		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result  = $this->parent->db_pointer->database_query($sql);
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$prev = $this->page_size;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$this->call_command("DB_SEEK",array($result,$goto));
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
			
			$variables["PAGE_BUTTONS"] = Array();
			if ($this->manage_database_list == 1){
				$variables["PAGE_BUTTONS"][0] = Array("ADD",$this->module_command."LIST_ADD", ADD_NEW);
			}
			
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
//			$variables["as"]				= "table";
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			$variables["PAGE_COMMAND"]		= $this->module_command."LIST";
			$start_page=intval($page / $this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page + $this->page_size)>$num_pages){
				$end_page	 =	$num_pages;
			}else{
				$end_page	=	$this->page_size;
			}
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= "";
			$variables["HEADER"]			= LOCALE_INFO_DIRECTORY_HEADER." - List";
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			$display_format=Array();
			for($di=0;$di<count($this->info_summary_layout_options_values);$di++){ 
				$display_format[$this->info_summary_layout_options_values[$di]] = $di;
			}
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$shop_enabled 		= $r["info_shop_enabled"];
				$status				= $r["info_status"];
				$label 				= $this->check_parameters($r,"info_label","");
				if($shop_enabled==1){
					$ecommerce=LOCALE_YES;
				} else {
					$ecommerce=LOCALE_NO;
				}
				if($status==1){
					$status = LOCALE_LIVE;
				} else {
					$status = LOCALE_NOT_LIVE;
				}
				$menu_label		= $this->check_parameters($r,"menu_label","Not Specified");
				$menu_url		= $this->check_parameters($r,"menu_url","index.php");
				$total			= $this->check_parameters($r,"total",0);
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["info_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		$label, "TITLE"),
						Array("Menu Location",	"<a title='open in external window' target='_external' href='$menu_url'>$menu_label</a>", "SUMMARY"),
						Array("Ecommerce",	$ecommerce),
						Array("Status",	$status),
						Array("Entries", "<a href='admin/index.php?command=".$this->module_command."LIST_ENTRIES&amp;identifier=".$r["info_identifier"]."' title='Manage Entries'>$total</a>"),
						Array("Display Format", $this->info_summary_layout_options_labels[$display_format[$r["info_summary_layout"]]],"SUMMARY")
					)
				);
				if ($this->manage_database_list){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."LIST_EDIT",EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."LIST_REMOVE",REMOVE_EXISTING);
//					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("MANAGE",$this->module_command."LIST_ENTRIES","Manage entries");
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EMPTY",$this->module_command."REMOVE_ALL_ENTRIES","Empty Directory");
//					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("TIDYUP",$this->module_command."REMOVE_LOST","Tidy Lost Entries");
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("FEATURELIST",$this->module_command."FEATURE_LIST","Feature List");
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("IMPORT",$this->module_command."IMPORT","Import");
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EXPORT",$this->module_command."EXPORT","Export");
//					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = array("BASICSEARCH",$this->module_command."BASIC_SEARCH",	"Basic Search");
//					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = array("ADVSEARCH",$this->module_command."ADVANCED_SEARCH",	"Advanced Search");
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = array("PROTECT",$this->module_command."FIELD_PROTECTION",	"Protect Fields");
					$len = count($this->extra_commands);
					for($i=0;$i<$len;$i++){
						if($this->extra_commands[$i][2]==1){
							$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = array($this->extra_commands[$i][4],$this->extra_commands[$i][0],	$this->extra_commands[$i][3]);
						}
					}
				}
			}
			$this->page_size = $prev;
			
			return $this->generate_list($variables);
		}
	}
	
	
	/*************************************************************************************************************************
	* this function allows the modification of the display options for a specific directory
	*
	* @param Array keys ("identifier","recache")
	* @return String XML representaiton of the form required to be filled out by the user
	*************************************************************************************************************************/
	function information_list_modify($parameters){
		if($this->manage_database_list==0){
			return "";
		}
		$identifier 			= $this->check_parameters($parameters,"identifier",-1);
		$form_label 			= "Add";
		$info_in_menu			= 0;
		$info_menu_location		= -1;
		$info_label				= "";
		$info_confirm_screen	= "Thank You,<br/><br/>Your entry has been submitted.";
		$info_status			= "0";
		$info_workflow_status	= "4";
		$info_category			= 0;
		$info_columns			= 1;
		$info_vcontrol			= 0;
		$info_update_access		= 0;
		$info_display			= "hide_categories";
		$info_verify_email		= "";
		$info_summary_layout	= 0;
		$info_searchresults		= 10;
		$info_summary_only		= 0;
		$info_shop_enabled		= 0;
		$info_cat_label			= "Category";
		$info_add_label			= $this->get_constant($this->setup_screen["ADDTOBASKET"]["default"]);
		$info_no_stock_label	= $this->get_constant($this->setup_screen["FULLYBOOKED"]["default"]);
		$info_no_stock_display	= 1;
		$generalKey = Array(Array(),Array(),Array());
		/*************************************************************************************************************************
		* if the user is adding a new entry or editing a valid entry then ok should be true;
		*************************************************************************************************************************/
		$ok = true;
		$found = 0;
		// mi2.mi_memo as info_verify_email 
		// left outer join memo_information as mi2 on (info_client=mi2.mi_client and mi2.mi_link_id = info_identifier and mi2.mi_type='$this->webContainer' and mi2.mi_field='verifyemail') 
		// (mi2.mi_type='$this->webContainer' or mi2.mi_type is NULL) and 
		$current_rank =1;
		if ($identifier!=-1){
			$form_label 	= "Edit";
			$sql = "select information_list.*, 
				mi1.mi_memo as info_confirm_screen
				from information_list 
					left outer join memo_information as mi1 on (info_client=mi1.mi_client and mi1.mi_link_id = info_identifier and mi1.mi_type='$this->webContainer' and mi1.mi_field='confirmscreen')
			where 
				(mi1.mi_type='$this->webContainer' or mi1.mi_type is NULL) and 
				info_identifier=$identifier and info_client=$this->client_identifier
			";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			$ok = false;
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$info_category			= $r["info_category"];
				$info_label				= $r["info_label"];
				$info_menu_location		= $r["info_menu_location"];
				$info_workflow_status	= $r["info_workflow_status"];
				$info_status			= $r["info_status"];
				$info_confirm_screen	= $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$r["info_confirm_screen"]));
//				$info_verify_email		= $r["info_verify_email"];
				$info_columns			= $r["info_columns"];
				$info_display			= $r["info_display"];
				$info_vcontrol			= $r["info_vcontrol"];
				$info_update_access		= $r["info_update_access"];
				$info_summary_layout	= $r["info_summary_layout"];
				$info_searchresults		= $r["info_searchresults"];
				$info_shop_enabled		= $r["info_shop_enabled"];
				$info_cat_label			= $r["info_cat_label"];
				$info_in_menu			= $r["info_in_menu"];
				$info_hideemptycat		= $r["info_hideemptycat"];								
				$info_add_label			= $r["info_add_label"];
				$info_no_stock_label	= $r["info_no_stock_label"];
				$info_no_stock_display	= $r["info_no_stock_display"];
				$info_summary_only		= $r["info_summary_only"];
				$ok						= true;
			}
			$sql = "select * from information_fields where if_client = $this->client_identifier and if_list = $identifier order by  if_screen asc, if_rank asc";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				if ($r["if_name"]=="ie_title" && $r["if_screen"]==0){
					$found = 1;
				}
				$generalKey[$r["if_screen"]][$r["if_name"]] = $r["if_name"]."-".$r["if_rank"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][0] = $r["if_label"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][1] = $current_rank;
//				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][2] = $r["if_type"];
				/* To Show Event Form textboxes for admin instead of showing only form labels (Unmarked by Muhammad Imran Mirza)*/
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][2] = $r["if_type"];

				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][3] = $r["if_link"];
//				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][4] = $r["if_search_form"];
//				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["value"] = Array();
//				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["duplicate"] = $r["if_duplicate"];
//				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["filter"] = $r["if_filterable"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["map"] = $r["if_map"];
/*				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["sumlabel"] = $r["if_sumlabel"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["conlabel"] = $r["if_conlabel"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["special"] = $r["if_special"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["add_to_title"] = $r["if_add_to_title"];				
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["url_linkfield"] = $r["if_url_linkfield"];
*/
//				print "<li>".$r["if_label"]." - ".$r["if_special"]."</li>";
				$current_rank++;
			}
			if ($found==0){
				$this->fields[0]["ie_title-0"][0] = "Title";
				$this->fields[0]["ie_title-0"][1] = $current_rank;
				$this->fields[0]["ie_title-0"][2] = "text";
				$this->fields[0]["ie_title-0"][3] = 0;
				$this->fields[0]["ie_title-0"][4] = 1;
				$this->fields[0]["ie_title-0"]["value"] = "";
				$this->fields[0]["ie_title-0"]["duplicate"] = "exact";
				$this->fields[0]["ie_title-0"]["filter"] = 0;
				$this->fields[0]["ie_title-0"]["map"] = "";
				$this->fields[0]["ie_title-0"]["sumlabel"] = 2;
				$this->fields[0]["ie_title-0"]["conlabel"] = 2;
				$this->fields[0]["ie_title-0"]["special"] = 0;
				$found=1;
			}
//			print_r($this->fields[0]);
//			print_r($this->fields[0]["ie_oselect1-0"]);
//			print_r($generalKey);
			if($r["if_screen"]==0){
				$sql = "select * from information_options inner join information_fields on if_client=io_client and if_list = io_list and io_field = if_name and if_screen=0 where io_client=$this->client_identifier and io_list = $identifier order by io_field, io_rank";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
				$current_rank =1;
				while ($r = $this->parent->db_pointer->database_fetch_array($result)){
//					print "<li>".$r["io_field"]." = ".$generalKey[0][$r["io_field"]]." ".$r["if_name"]."-".$r["if_rank"]."=".urldecode($r["io_value"])."</li>";
					$c = count( $this->fields[0][$generalKey[0][$r["if_name"]]]["value"] );
					$this->fields[0][$generalKey[0][$r["if_name"]]]["value"][$c] = urldecode($r["io_value"]);
					$current_rank++;
				}
				$sql = "select * from information_entry_links where ievl_client=$this->client_identifier and ievl_list = $identifier";
				$result  = $this->parent->db_pointer->database_query($sql);
				$current_rank =1;
				while ($r = $this->parent->db_pointer->database_fetch_array($result)){
					$this->fields[0][$generalKey[0][$r["ievl_field"]]]["value"][count($this->fields[0][$generalKey[0][$r["ievl_field"]]]["value"])] = Array($r["ievl_mapped"],$r["ievl_screen"]);
					$current_rank++;
				}

			}
		}
		if ($found==0){
			for($i=0;$i<=3;$i++){
				$this->fields[$i]["ie_title-0"][0] = "Title";
				$this->fields[$i]["ie_title-0"][1] = $current_rank;
				$this->fields[$i]["ie_title-0"][2] = "text";
				$this->fields[$i]["ie_title-0"][3] = 0;
				$this->fields[$i]["ie_title-0"][4] = 1;
				$this->fields[$i]["ie_title-0"]["value"] = "";
				$this->fields[$i]["ie_title-0"]["duplicate"] = "exact";
				$this->fields[$i]["ie_title-0"]["filter"] = 0;
				$this->fields[$i]["ie_title-0"]["map"] = "";
				$this->fields[$i]["ie_title-0"]["sumlabel"] = 2;
				$this->fields[$i]["ie_title-0"]["conlabel"] = 2;
				$this->fields[$i]["ie_title-0"]["special"] = 0;
			}
		}
		$out 	  = "";
		if ($ok){
			$info_menu_locations	= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($info_menu_location));
			
			$out .="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .= "<page_options>";
			$out .= "<header><![CDATA[".LOCALE_INFO_DIRECTORY_HEADER." - $form_label]]></header>";
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("PREVIEW",$this->module_command."PREVIEWFORM",ENTRY_PREVIEW));
			$out .="</page_options>";
			$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
			$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."LIST_SAVE\" />";
			$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
			$out .="		<input type=\"hidden\" name=\"modified_entry_screen\" value=\"0\" />";
			$out .="		<input type=\"hidden\" name=\"modified_summary_screen\" value=\"0\" />";
			$out .="		<input type=\"hidden\" name=\"modified_content_screen\" value=\"0\" />";
			$out .="		<input type=\"hidden\" name=\"prev_menu\" value=\"$info_menu_location\" />";
			$out .="		<input type=\"hidden\" name=\"prev_in_menu\" value=\"$info_in_menu\" />";
			$out .="		<input type=\"hidden\" name=\"max_number_of_fields\" value=\"".count($this->fields)."\" />";
			$out .="		<page_sections>";
			$out .="		<section label='Setup'>";
			$out .="			<input required=\"YES\" type=\"text\" name=\"info_label\" label=\"".LOCALE_DIRECTORY_LABEL."\" size=\"255\"><![CDATA[$info_label]]></input>";
			/**************************************************************************************************************************
			* Display categories 
			**************************************************************************************************************************/
			$out .= 			"<input type='hidden' name='info_category' value='$info_category' />";
			/*************************************************************************************************************************
			* Display menu locations
			*************************************************************************************************************************/
			$out .= 			"<select name='info_menu_location' label='".LOCALE_CHOOSE_MENU."'>$info_menu_locations</select>";
			/**************************************************************************************************************************
			* Display status
			**************************************************************************************************************************/
			$out .= 			"<select name='info_status' label='".LOCALE_STATUS."'>";
			$out.= $this->gen_options(Array(0,1), Array(LOCALE_NOT_LIVE, LOCALE_LIVE), $info_status);
			$out .= 				"</select>";
			/*************************************************************************************************************************
			* Summary to be tabular or structured
			*************************************************************************************************************************/
			$out .= 			"<select name='info_summary_layout' label='".LOCALE_INFODIR_SUMMARY_STRUCTURE."'>";
			$out.= $this->gen_options($this->info_summary_layout_options_values, $this->info_summary_layout_options_labels, $info_summary_layout);
			$out .= "			 </select>";
			/**************************************************************************************************************************
			* Display status
			**************************************************************************************************************************/
			$out .= 			"<select name='info_shop_enabled' label='".LOCALE_INFODIR_ECOMMERCE_ENABLED."' onchange='toggle_ecommerce();'>";
			$out.= $this->gen_options(Array(0,1), Array(LOCALE_NO, LOCALE_YES), $info_shop_enabled);
			$out .= 			"</select>";
			/*************************************************************************************************************************
            * restrict view to summary only (no link to full content
            *************************************************************************************************************************/
			$out .= 			"<select name='info_summary_only' label='".LOCALE_INFODIR_RESTRICT_TO_SUMMARY."'>";
			$out.= $this->gen_options(Array(0,1), Array(LOCALE_NO, LOCALE_YES), $info_summary_only);
			$out .= 			"</select>";
			/*************************************************************************************************************************
            * if ADDTOBASKET is visible then display option
            *************************************************************************************************************************/
			if ($this->setup_screen["ADDTOBASKET"]["visible"]=="Yes"){
				$out .="			<input type=\"text\" name=\"info_add_label\" label=\"".LOCALE_DIRECTORY_ECOMMERCE_ADD_LABEL."\" size=\"255\"><![CDATA[$info_add_label]]></input>";
			} else {
				$out .="			<input type=\"hidden\" name=\"info_add_label\"><![CDATA[$info_add_label]]></input>";
			}
			/*************************************************************************************************************************
            * if FULLYBOOKED is visible then display option
            *************************************************************************************************************************/
			if ($this->setup_screen["FULLYBOOKED"]["visible"]=="Yes"){
				$out .="			<input type=\"text\" name=\"info_no_stock_label\" label=\"".LOCALE_DIRECTORY_ECOMMERCE_NOSTOCK_LABEL."\" size=\"255\"><![CDATA[$info_no_stock_label]]></input>";
			} else {
				$out .="			<input type=\"hidden\" name=\"info_no_stock_label\"><![CDATA[$info_no_stock_label]]></input>";
			}
			/*************************************************************************************************************************
            * Should entries with zero stock be visible?
            *************************************************************************************************************************/
			if ($this->setup_screen["NOSTOCK"]["visible"]=="Yes"){
				$out .= 			"<select name='info_no_stock_display' label='".LOCALE_INFODIR_ECOMMERCE_NO_STOCK_DISPLAY."'>";
				$out .=				$this->gen_options(Array(0,1), Array(LOCALE_NO, LOCALE_YES), $info_no_stock_display);
				$out .= 			"</select>";
			} else {
				$out .="			<input type=\"hidden\" name='info_no_stock_display'><![CDATA[$info_no_stock_display]]></input>";
			}
			/*************************************************************************************************************************
            * Category Options
            *************************************************************************************************************************/
			$out .="			<input type=\"text\" name=\"info_cat_label\" label=\"Category Label\" size=\"255\"><![CDATA[$info_cat_label]]></input>";
			if ($this->setup_screen["INMENU"]["visible"]=="Yes"){
				$out .= 			"<select name='info_in_menu' label='".LOCALE_INFODIR_INMENU."'>";
				$out .=				$this->gen_options(Array(0,1), Array(LOCALE_NO, LOCALE_YES), $info_in_menu);
				$out .= 			"</select>";
			} else {
				$out .="			<input type=\"hidden\" name='info_in_menu'><![CDATA[$info_in_menu]]></input>";
			}
			
			$out .= 			"<select name='info_hideemptycat' label='".LOCALE_INFODIR_HIDEEMPTYCAT."'>";
			$out .=				$this->gen_options(Array(0,1), Array(LOCALE_NO, LOCALE_YES), $info_hideemptycat);
			$out .= 			"</select>";
			
			
			
			$out .= 			"<select name='info_display' label='".LOCALE_INFODIR_DISPLAY_OPTIONS."'>";
			$out .=	$this->gen_options(
						Array('hide_categories','display_1_lvl','display_2_lvl'), 
						Array(LOCALE_INFODIR_HIDE_CATEGORIES, LOCALE_INFODIR_DISPLAY_1_LVL, LOCALE_INFODIR_DISPLAY_2_LVL), 
						$info_display
					);
			$out .= 				"</select>";
			$out .= 			"<select name='info_columns' label='".LOCALE_INFODIR_DISPLAY_COLUMNS."'>";
			for ($i=1; $i<4; $i++){
				$out .= "			<option value='$i'";
				if ($info_columns==$i){
					$out .= " selected ='true'";
				}
				$out .= 			">Display in ($i) column(s)</option>";
			}
			$out .= 			"</select>";
			
			/**************************************************************************************************************************
			* Display Versions to keep
			*************************************************************************************************************************/
			$out .= 			"<select name='info_vcontrol' label='Version Control setting'>";
			$out .=				$this->gen_options(Array(11,0,1,2,3,4,5,6,7,8,9,10), Array(LOCALE_DISABLED, LOCALE_KEEP_ALL, LOCALE_ONLY_1, LOCALE_ONLY_2, LOCALE_ONLY_3, LOCALE_ONLY_4, LOCALE_ONLY_5, LOCALE_ONLY_6, LOCALE_ONLY_7, LOCALE_ONLY_8, LOCALE_ONLY_9, LOCALE_ONLY_10), $info_vcontrol);
			$out .= 			"</select>";
			/*************************************************************************************************************************
			*	Display workflow
			*************************************************************************************************************************/
			if($info_workflow_status==0){
				$info_workflow_status_a=0;
				$info_workflow_status_b=0;
			} else if($info_workflow_status==1){
				$info_workflow_status_a=2;
				$info_workflow_status_b=0;
			} else if($info_workflow_status==2){
				$info_workflow_status_a=2;
				$info_workflow_status_b=1;
			} else if($info_workflow_status==3){
				$info_workflow_status_a=1;
				$info_workflow_status_b=0;
			} else if($info_workflow_status==4){
				$info_workflow_status_a=1;
				$info_workflow_status_b=1;
			} else if($info_workflow_status==5){
				$info_workflow_status_a=2;
				$info_workflow_status_b=1;
			}
			
			$out .= 			"<select name='info_workflow_status_a' label='".LOCALE_INFODIR_WHO_CAN_SUBMIT."'>";
			$out .=				$this->gen_options(
				Array(0,1,2), 
				Array(LOCALE_ADMIN_ONLY, LOCALE_REGUSERS_ONLY, LOCALE_ANYONE), 
				$info_workflow_status_a
			);
			$out .= 			"</select>";
			$out .= 			"<select name='info_workflow_status_b' label='".LOCALE_INFODIR_REQUIRES_APPROVAL."'>";
			$out .=				$this->gen_options(
				Array(0,1), 
				Array(LOCALE_NO, LOCALE_YES), 
				$info_workflow_status_b
			);
			$out .= 			"</select>";
			/*************************************************************************************************************************
			* Display editorial privigilies
			*************************************************************************************************************************/
			$out .= 			"<select name='info_update_access' label='".LOCALE_INFODIR_WHO_CAN_UPDATE."'>";
			$out .=				$this->gen_options(Array(0,1,2), Array(LOCALE_UPDATE_NO_EDIT_FROM_SITE, LOCALE_UPDATE_ALL_USERS, LOCALE_UPDATE_AUTHOR_ONLY), $info_update_access);
			$out .= 			"</select>";
			/**************************************************************************************************************************
			* Span Results after X entries
			**************************************************************************************************************************/
			$span_array = Array(10,20,50,100);
			$out .= 			"<select name='info_searchresults' label='".LOCALE_INFODIR_SEARCHRESULT_SPANS."'>";
			for($i=0;$i<count($span_array);$i++){
				$out .= 				"<option value='".$span_array[$i]."'";
				if ($info_searchresults==$span_array[$i]){
					$out .= " selected ='true'";
				}
				$out .= 				">".$span_array[$i]."</option>";
			}
			$out .= 			"</select>";
			
			/*************************************************************************************************************************
			* site containers
			*************************************************************************************************************************/
			$web_containers = split("~----~",$this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>$this->webContainer, "identifier"=>$identifier)));
			if ($web_containers[0]!=""){
				$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
				$out .= 			"<checkboxes type='vertical' name='rss_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
			}
			$out .="		</section>";
			$out .="		<section label='Define Entry Form Format'><text><![CDATA[
				<div class=\"column\" id=\"fieldForm\" name=\"fieldForm\" style=\"border:1px dashed #999999;padding:3px 3px 3px 3px;width:450px;display:inline;\"></div>
				<div id=\"fieldRank\" name=\"fieldRank\" style=\"border:1px dashed #999999;padding:3px 3px 3px 3px;width:450px;display:inline;vertical-align:top;\"></div>
			]]></text>";
			$out .="	<multilist>";
			$current_rank=0;
			$values		= "";
			
			foreach($this->user_fields as $key=>$list){
				$label		= $this->check_parameters($list,0);
				$fieldType	= $this->check_parameters($list,4);
				$type		= $this->check_parameters($list,"type");
				$group		= $this->check_parameters($list,"group");
				$description= $this->check_parameters($list,2);
				$searchable = $this->check_parameters($list,"searchable");
				$map 		= $this->check_parameters($list,"map");
				$special	= $this->check_parameters($list,"special");
				$out.="		<multi name='$key' group='$group' type='$fieldType' map='$map' accesstype='$type' searchable='$searchable'><label><![CDATA[$label]]></label><description><![CDATA[$description]]></description></multi>";
			}
			$out .="		</multilist>";
			$out .="		<fieldlist>";
			$current_rank 	= 0;
			$sfield 		= "";
			$searchfield	= "";
			$metadatafields	= "";
			$cfield 		= "";
//			print_r($this->fields);
			for($screen=0; $screen < 3; $screen++){
				if(is_array($this->fields[$screen])){
					foreach($this->fields[$screen] as $keydata => $list){
						$keyArray = split("-",$keydata);
						$key 		= $keyArray[0];
						$rankindb 	= $keyArray[1];
						$values		= "";
						$rank 		= $this->check_parameters($list, 1, -1);
						$type		= $this->check_parameters($list, 2, -1);
						$search_form= $this->check_parameters($list, 4, 0);
						$link		= $this->check_parameters($list, 3, -1);
						$duplicate	= $this->check_parameters($list, "duplicate","");
						$filter		= $this->check_parameters($list, "filter","0");
						$sumlabel	= $this->check_parameters($list, "sumlabel","1");
						$conlabel	= $this->check_parameters($list, "conlabel","1");
						$map		= $this->check_parameters($list, "map","");
						$special	= $this->check_parameters($list, "special","0");
						$add_to_title= $this->check_parameters($list, "add_to_title","0");
						$url_linkfield= $this->check_parameters($list, "url_linkfield","0");
						
						if($rank==-1){
							$current_rank++;
							$rank = $current_rank;
						}
						$label		= $this->check_parameters($list,0);
						if (is_array($this->check_parameters($list,"value"))){
							//examine
							for ($index_of_value = 0, $mx = count($list["value"]); $index_of_value < $mx ; $index_of_value++){
								if ($type!='URL'){
									$values		.= "<value><![CDATA[".$list["value"][$index_of_value]."]]></value>";
								}else{
									$values		.= "<value screen='".$list["value"][$index_of_value][1]."'><![CDATA[".$list["value"][$index_of_value][0]."]]></value>";
								}
							}
							// join("[[javareturn]]",$list["value"]);
						} else {
		//					$values		= "<value><![CDATA[" . $list["value"] . "]]></value>";
						}
						if($screen==0 && $url_linkfield != 127){ // URL link field has $url_linkfield 127. 
							$out.="		<field name='$key' special='$special' type='$type' rank='$rank' selected='1' duplicate='$duplicate' search_form='$search_form'  filter='$filter' sumlabel='$sumlabel' conlabel='$conlabel' map='$map' add_to_title='$add_to_title' url_linkfield='$url_linkfield'><label><![CDATA[$label]]></label><values>$values</values></field>";
						}
						if($screen==1){
							if ($link==1){
								$link="link = '1'";
							} else {
								$link="link = '4'";
							}
							$sfield .= "			<field name='$key' type='$type' rank='$rank' $link><label><![CDATA[$label]]></label></field>";
						}
						if($screen==2){
							$cfield .= "			<field name='$key' type='$type' rank='$rank'><label><![CDATA[$label]]></label></field>";
						}
						if($screen==3){
							$searchfield .="		<field name='$key' type='$type' rank='$rank'><label><![CDATA[$label]]></label></field>";
						}
					}
				}
			}
			$out .="		</fieldlist>";
			$out .="		</section>";
			$out .="		<section label='Define Summary Format' name='summary_display' onclick='show_output'><parameters><field>summary_display</field></parameters>";
			$out .="			<screen>$sfield</screen>";
			
			$out .="		</section>";
			$out .="		<section label='Define Content Format' name='content_display' onclick='show_output'><parameters><field>content_display</field></parameters>";
			$out .="			<screen>$cfield</screen>";
			$out .="		</section>";
/*
			$out .="		<section label='Advanced Search' name='advancedsearch' onclick='show_output'><parameters><field>advancedsearch</field></parameters>";
			$out .="			<screen>$searchfield</screen>";
			$out .="		</section>";
*/
			$out .="		<section label='Confirm Screen'>";
			$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_DESCRIPTION",Array());
			$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
			$locked_to  = $this->check_parameters($this_editor,"locked_to","");
			$out .="			<textarea required=\"YES\" label=\"".ENTRY_DESCRIPTION."\" size=\"40\" height=\"15\" name=\"confirm_screen\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$info_confirm_screen]]></textarea>";
			$out .="		</section>";
/*
			$out .="		<section label='Verify Email'>";
			$out .="		<text><![CDATA[If you have choosen the \"Allow user to Add Entry Automatically - anonymous access must verify via email\" workflow model you can specify some content to appear at the to of the verify email]]></text>";
			$out .="			<textarea label='Supply a message to be included in the verification email' size=\"40\" height=\"15\" name=\"info_verify_email\" ><![CDATA[$info_verify_email]]></textarea>";
			$out .="		</section>";
*/
			$out .="		<section label='MetaData' name='metadatascreen' onclick='viewmetadatamaping'><parameters><field></field></parameters>";
			$out .="			<metadatamapfields>";
			for($i=0;$i<count($this->metadata_fields);$i++){
				$out .="			<metadata_tag name='".$this->metadata_fields[$i]["key"]."'><![CDATA[".$this->metadata_fields[$i]["label"]."]]></metadata_tag>";
			}
			$out .="			</metadatamapfields>";
			$out .="		</section>";
			$out .="		</page_sections>";
			$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
			$out .="	</form>";
			$out .="</module>";
		}
		return $out;
	}
	/*************************************************************************************************************************
	* this function is used to save any changes to a information directory structure
	*
    * @parameter Array    ("identifier", "info_category", "info_label", "confirm_screen", "info_category", "info_verify_email", "info_menu_location", "info_workflow_status", "info_status", "info_summary_layout", "info_display", "info_columns", "prev_menu", "currentlyhave", "info_vcontrol", "info_update_access", "info_searchresults", "totalnumberofchecks_rss_containers", "rss_containers")
    *************************************************************************************************************************/
	function information_save($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"<pre>".print_r($parameters,true)."</pre>"));}
		$identifier 			= $this->check_parameters($parameters ,"identifier",-1);
		$info_label				= htmlentities(trim($this->strip_tidy($this->check_parameters($parameters,"info_label"))));
		$info_cat_label			= htmlentities(trim($this->strip_tidy($this->check_parameters($parameters,"info_cat_label"))));
		$info_confirm_screen	= trim($this->call_command("EDITOR_CONVERT_FONT_TO_SPAN", Array("string" => $this->split_me( $this->tidy( $this->validate($this->check_parameters($parameters,"confirm_screen") ) ),"'","&#39;") ) ));
		$info_category			= $this->check_parameters($parameters ,"info_category",-1);
//		$info_verify_email		= $this->check_parameters($parameters ,"info_verify_email");
		$prev_menu				= $this->check_parameters($parameters ,"prev_menu",-1);
		$info_menu_location 	= $this->check_parameters($parameters ,"info_menu_location",-1);
		$info_summary_only		= $this->check_parameters($parameters ,"info_summary_only",0);
		$info_workflow_status_a	= $this->check_parameters($parameters ,"info_workflow_status_a",0);
		$info_workflow_status_b	= $this->check_parameters($parameters ,"info_workflow_status_b",0);
		$info_status			= $this->check_parameters($parameters ,"info_status",0);
		$info_summary_layout	= $this->check_parameters($parameters ,"info_summary_layout",0);
		$info_display			= $this->check_parameters($parameters ,"info_display","hide_categories");
		$info_columns			= $this->check_parameters($parameters ,"info_columns",1);
		$currentlyhave			= $this->check_parameters($parameters ,"currentlyhave");
		$info_vcontrol			= $this->check_parameters($parameters ,"info_vcontrol");
		$info_update_access		= $this->check_parameters($parameters ,"info_update_access", 0);
		$info_searchresults		= $this->check_parameters($parameters ,"info_searchresults", 10);
		$info_shop_enabled		= $this->check_parameters($parameters ,"info_shop_enabled", 0);
		$info_in_menu			= $this->check_parameters($parameters ,"info_in_menu",0);
		$prev_in_menu			= $this->check_parameters($parameters ,"prev_in_menu",0);
		$info_hideemptycat		= $this->check_parameters($parameters ,"info_hideemptycat",0);		
		$info_add_label			= $this->check_parameters($parameters ,"info_add_label","");
		$info_no_stock_label	= $this->check_parameters($parameters ,"info_no_stock_label","");
		$info_no_stock_display	= $this->check_parameters($parameters ,"info_no_stock_display",0);
		$replacelist			= Array();
		$replacelist			= $this->check_parameters($parameters ,"rss_containers",Array());

		if($info_workflow_status_a==0){
			$info_workflow_status = 0;
		} else if($info_workflow_status_a==2 && $info_workflow_status_b==0){
			$info_workflow_status = 1;
		} else if($info_workflow_status_a==2 && $info_workflow_status_b==1){
			$info_workflow_status = 2;
		} else if($info_workflow_status_a==1 && $info_workflow_status_b==0){
			$info_workflow_status = 3;
		} else if($info_workflow_status_a==1 && $info_workflow_status_b==1){
			$info_workflow_status = 4;
		}
//		$info_workflow_status	= $this->check_parameters($parameters ,"info_workflow_status",4);

/*
$info_workflow_status_a
$info_workflow_status_b

$info_workflow_status==0 admin only (00)
$info_workflow_status==1 Allow user to Add Entry Automatically (20)
$info_workflow_status==5 Allow user to Add Entry Automatically - anonymous access must verify via email (21)
$info_workflow_status==2 Allow user to Add Entry Requires Approval by Administrator (21)
$info_workflow_status==3 Allow Registered User to Add Entry Automatically (10)
$info_workflow_status==4 Allow Registered User to Add Entry Requires Approval by Administrator (11)
*/


		if ($identifier == -1){
			/*************************************************************************************************************************
			* add new entry to system
			*************************************************************************************************************************/
			$info_creation_date = $this->libertasGetDate("Y/m/d H:i:s");
			$info_category  = $this->call_command("CATEGORYADMIN_CREATE_CATEGORY", Array("cat_label" => $this->check_parameters($parameters,"info_label"), "info_identifier" => -1));
			$identifier = $this->getUid();
			$parameters["identifier"] = $identifier;
			
			$sql="insert into information_list
						(info_in_menu, info_hideemptycat, info_cat_label, info_shop_enabled, info_identifier, info_searchresults, info_update_access, info_vcontrol, info_label, info_client, info_category, info_creation_date, info_menu_location, info_workflow_status, info_status, info_display, info_columns, info_summary_layout, info_owner, info_add_label, info_no_stock_label, info_no_stock_display, info_summary_only)
					values 
						('$info_in_menu','$info_hideemptycat', '$info_cat_label', $info_shop_enabled, $identifier, $info_searchresults, $info_update_access, $info_vcontrol, '$info_label', '$this->client_identifier', '$info_category', '$info_creation_date', '$info_menu_location', '$info_workflow_status', '$info_status', '$info_display', '$info_columns', '$info_summary_layout', '$this->webContainer', '$info_add_label', '$info_no_stock_label', '$info_no_stock_display', '$info_summary_only') ";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("MEMOINFO_INSERT", array("mi_type"=>$this->webContainer, "mi_memo"=>$info_confirm_screen, "mi_link_id" => $identifier, "mi_field" => "confirmscreen", "debug" =>0));
//			$this->call_command("MEMOINFO_INSERT", array("mi_type"=>$this->webContainer, "mi_memo"=>$info_verify_email, "mi_link_id" => $identifier, "mi_field" => "verifyemail", "debug" =>0));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE", 
				Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $info_label,
					"wo_command"	=> $this->module_presentation."DISPLAY",
					"cmd"			=> "ADD",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE_WO",
				Array(
					"webobjects"	=> $this->special_webobjects,
					"owner_id" 		=> $identifier,
					"starter"		=> $this->module_command,
					"label"			=> $info_label,
					"cmd"			=> "ADD"
				)
			);
			
//			$this->restore(Array("list"=>$identifier));
			$this->widget_atoz(Array("identifier"=>$identifier));
		} else {
			/*************************************************************************************************************************
			* Edit an existing entry
			*************************************************************************************************************************/
			$sql="update information_list set 
					info_cat_label		 = '$info_cat_label',
					info_update_access	 = '$info_update_access',
					info_label  		 = '$info_label', 
					info_category		 = '$info_category', 
					info_menu_location	 = '$info_menu_location', 
					info_workflow_status = '$info_workflow_status', 
					info_status			 = '$info_status', 
					info_display		 = '$info_display', 
					info_columns		 = '$info_columns', 
					info_vcontrol		 = '$info_vcontrol', 
					info_summary_layout	 = '$info_summary_layout',
					info_searchresults	 = '$info_searchresults',
					info_shop_enabled	 = '$info_shop_enabled',
					info_in_menu		 = '$info_in_menu',
					info_hideemptycat	 = '$info_hideemptycat',										
					info_add_label		 = '$info_add_label', 
					info_no_stock_label	 = '$info_no_stock_label', 
					info_no_stock_display= '$info_no_stock_display',
					info_summary_only	 = '$info_summary_only'
				where
					info_client 	 	= $this->client_identifier and
					info_identifier		= $identifier
				";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("MEMOINFO_UPDATE", array("mi_type"=>$this->webContainer, "mi_memo"=>$info_confirm_screen, "mi_link_id" => $identifier, "mi_field" => "confirmscreen", "debug" =>0));
//			$this->call_command("MEMOINFO_UPDATE", array("mi_type"=>$this->webContainer, "mi_memo"=>$info_verify_email, "mi_link_id" => $identifier, "mi_field" => "verifyemail", "debug" =>0));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE", Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $info_label,
					"wo_command"	=> $this->module_presentation."DISPLAY",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE_WO",
				Array(
					"webobjects"	=> $this->special_webobjects,
					"owner_id" 		=> $identifier,
					"starter"		=> $this->module_command,
					"label"			=> $info_label,
					"cmd"			=> "UPDATE"
				)
			);
			$this->loadedcat = $this->call_command("CATEGORYADMIN_LOAD", Array("identifier"=>$info_category, "returntype"=>1,"recache"=>1));
		}
//		print $sql;
		/*************************************************************************************************************************
		*	There is no "Select one" option in the menu location so there is always a location supplied*=-=-
		*************************************************************************************************************************/
		if($info_summary_layout!=2){
			$this->set_channel($this->module_presentation."DISPLAY",$info_menu_location);
		} else {
			$this->set_channel($this->module_presentation."A2Z",$info_menu_location);
		}
		$this->save_form_fields($parameters);
		$this->make_special($info_menu_location, $identifier, $info_label, $info_summary_layout);
		if($info_in_menu == 1){
			$this->call_command("CATEGORYADMIN_CACHE_MENU",Array("cat_root"=>$info_category, "menu_identifier"=>$info_menu_location, "info_in_menu" => $info_in_menu));
		}
		if($prev_menu!=$info_menu_location){
			$this->move_info_dir($prev_menu,$info_menu_location, $identifier);
		}
	}
	
	/*************************************************************************************************************************
    * save the field information from the list managment tool
	* @access private
    *************************************************************************************************************************/
	function save_form_fields($parameters){
		$lang="en";			
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save_form_fields",__LINE__,"".print_r($parameters,true).""));}
		$identifier 			= $this->check_parameters($parameters,"identifier");
		$max_number_of_fields	= $this->check_parameters($parameters,"max_number_of_fields");
		$tmp = Array();
		$sql = "select * from information_fields 
					inner join information_field_protection on ifp_field=if_identifier and ifp_client=if_client and ifp_list = if_list
					where if_client = $this->client_identifier and if_list = $identifier";
        $result  = $this->parent->db_pointer->database_query($sql);
		$tmplength=0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$tmp[count($tmp)] = Array("f"=>$r["if_identifier"], "s"=>$r["if_screen"], "n"=>$r["if_name"], "g"=>$r["ifp_group"]);
			$tmplength++;
        }
        $this->parent->db_pointer->database_free_result($result);
		$sql = "delete from information_field_protection where ifp_client = $this->client_identifier and ifp_list = $identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from information_fields where if_client = $this->client_identifier and if_list = $identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from information_options where io_client = '$this->client_identifier' and io_list='$identifier'";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from information_entry_links where ievl_client = '$this->client_identifier' and ievl_list='$identifier'";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		$max_zero = $max_number_of_fields ;
		$settings = $this->call_command("SHOP_GET_SETTINGS");
		/*************************************************************************************************************************
        * metadata mapping
        *************************************************************************************************************************/
		$md_array 			= Array();
		$mdcount			= $this->check_parameters($parameters,"mdcount",0);
		$md_import_array	= $this->check_parameters($parameters,"mdmap",Array());
		for($index=0;$index<$mdcount;$index++){
			$s = split("::", $md_import_array[$index]);
			$md_array[$s[1]] = $s[0];
		}
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		for ($index=0; $index<$max_number_of_fields ; $index++){
			$visible = $this->check_parameters($parameters,"visiblefields_".$index,"__NOT_FOUND__");
			$rank	= $this->check_parameters($parameters,"rank_".$index);
			if ($visible == "1"){
				$rank			= $this->check_parameters($parameters,"rank_".$index);
				$hfield			= $this->check_parameters($parameters,"hfield_".$index);
				$vfield			= html_entity_decode($this->check_parameters($parameters,"vfield_".$index));
//				$search_form	= $this->check_parameters($parameters,"search_".$index,0);
				$duplicate		= $this->check_parameters($parameters,"duplicate_".$index,0);
				$sumlabel		= $this->check_parameters($parameters,"sumlabel_".$index,0);
				$conlabel		= $this->check_parameters($parameters,"conlabel_".$index,0);
				if($sumlabel==""){
					$sumlabel=1;
				}
				if($conlabel==""){
					$conlabel=1;
				}
				if($duplicate=="undefined"){
					$duplicate="";
				}
				$options		= $this->check_parameters($parameters,"options_".$index);
				$type			= $this->check_parameters($parameters,"type_".$index);
				$if_filterable	= $this->check_parameters($parameters,"filter_".$index,0);
				$mdmap			= $this->check_parameters($md_array,$hfield,"");
				$special		= $this->check_parameters($parameters,"special_".$index,0);
				$addtotitle		= $this->check_parameters($parameters,"addtotitle_".$index,0);	
				$addurlfield	= $this->check_parameters($parameters,"urlfield_".$index,0);			
				//print "<li> Title [$addtotitle]  $index  </li>";

				$if_identifier = $this->getUid();
				$sql = "
				insert into information_fields 
					(if_identifier, if_name, if_client, if_list, if_label, if_rank, if_type, if_screen, if_link,  if_duplicate, if_filterable, if_sumlabel, if_conlabel, if_map ,if_special,if_add_to_title, if_url_linkfield) 
				values 
					($if_identifier, '$hfield', '$this->client_identifier', '$identifier', '$vfield', '$rank', '$type', 0, 0, '$duplicate', $if_filterable, $sumlabel, $conlabel, '$mdmap', '$special', $addtotitle, $addurlfield)
				";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->parent->db_pointer->database_query($sql);
				for($field_index=0;$field_index<$tmplength;$field_index++){
					if ($hfield == $tmp[$field_index]["n"]){
						$sql = "insert into information_field_protection (ifp_client, ifp_field, ifp_list, ifp_group) values ($this->client_identifier, $if_identifier, $identifier, ".$tmp[$field_index]["g"].")";
						$this->parent->db_pointer->database_query($sql);
					}
				}
				if ($type=="URL"){
					if ($addurlfield == 1) {
						$if_identifier = $this->getUid();
						// URL link field has url_linkfield 127 and it should have if_name = nameoffield._link. 
						$sql = "insert into information_fields 
							(if_identifier, if_name, if_client, if_list, if_label, if_rank, if_type, if_screen, if_link,  if_duplicate, if_filterable, if_sumlabel, if_conlabel, if_map ,if_special,if_add_to_title, if_url_linkfield) 
						values 
							($if_identifier, '".$hfield."_link', '$this->client_identifier', '$identifier', '".$vfield." Link', '$rank', 'text', 0, 0, '', 0, 1, 1, '', 0, 0, 127)
						";
						$this->parent->db_pointer->database_query($sql);
					}
				} else {
					if (count($options)!=0){
						$optionlist = split("::ls_option::", $options);

						for ($opt =0, $max = count($optionlist); $opt <$max ;$opt++){
							if ($optionlist[$opt]!=''){
								$io_identifier = $this->getUid();
								$sql = "insert into information_options 
											(io_identifier, io_field, io_client, io_list, io_value, io_rank) 
										values 
											($io_identifier, '$hfield', '$this->client_identifier', '$identifier', '" . urldecode(html_entity_decode($optionlist[$opt])) . "', '" . ($opt+1) . "')";
								if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
								$this->parent->db_pointer->database_query($sql);
							}
						}
					}
				}
			} else {
				// if ie_title
				$hfield				= $this->check_parameters($parameters,"hfield_".$index);
				if ($hfield=="ie_title"){
					$rank			= $this->check_parameters($parameters,"rank_".$index);
					$vfield			= $this->check_parameters($parameters,"vfield_".$index);
					$sumlabel		= $this->check_parameters($parameters,"sumlabel_".$index,0);
					$conlabel		= $this->check_parameters($parameters,"conlabel_".$index,0);
					if($sumlabel==""){
						$sumlabel=1;
					}
					if($conlabel==""){
						$conlabel=1;
					}
					//if_search_form
					//0
					$if_map			= $this->check_parameters($parameters,"mdmap_".$index);
					$if_identifier	= $this->getUid();
					$sql = "insert into information_fields (if_identifier, if_name, if_client, if_list, if_label, if_rank, if_type, if_screen, if_link, if_filterable, $if_sumlabel, $if_conlabel, if_map, if_special) values ('$hfield', '$this->client_identifier', '$identifier', '$vfield', '$rank', '', 0, 0, 0, $sumlabel, $conlabel, $if_map, $special)";
					if ($this->module_debug){ 
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));
					}
					$this->parent->db_pointer->database_query($sql);
				}
			}
		}
		$field_list = Array("summary_display_","content_display_");
		$max_screens = count($field_list);
		for($i=0 ; $i < $max_screens ;$i++){
  			$max_number_of_fields	= $this->check_parameters($parameters, $field_list[$i]."num_of_fields");
			$link					= $this->check_parameters($parameters, $field_list[$i]."link_field",-1);
			$fields = "<seperator_row>\n<seperator>\n";
			for ($index=0; $index<$max_number_of_fields ; $index++){
				$name				= $this->check_parameters($parameters, $field_list[$i]."name_".$index);
				$label				= $this->check_parameters($parameters, $field_list[$i]."label_".$index);
				$rank				= $this->check_parameters($parameters, $field_list[$i]."rank_".$index);
				$type				= $this->check_parameters($parameters, $field_list[$i]."type_".$index);
				$url				= $this->check_parameters($parameters, $field_list[$i]."url_".$index);
				$if_filterable		= $this->check_parameters($parameters, $field_list[$i]."filter_".$index,0);
				if($if_filterable!="1"){
					$if_filterable = 0;
				}
				$if_conlabel		= $this->check_parameters($parameters, $field_list[$i]."conlabel_".$index,0);
				$if_sumlabel		= $this->check_parameters($parameters, $field_list[$i]."sumlabel_".$index,0);
				if($if_sumlabel=="" || $if_sumlabel=="undefined"){
					$if_sumlabel=1;
				}
				if($if_conlabel=="" || $if_conlabel=="undefined"){
					$if_conlabel=1;
				}
//				$search_form		= $this->check_parameters($parameters,"search_".$index,0);
				$l=0;
				if($link == $index){
					$l = 1;
				}
				//, if_search_form
				//, $search_form
				$if_map="";
				for($z=0;$z<$max_zero;$z++){
					$hfield			= $this->check_parameters($parameters,"hfield_".$z);
					if($hfield == $name ){
//						print "<li>".$field_list[$i]." $hfield==$name</li>";
						$if_map			= $this->check_parameters($parameters,"mdmap_".$z);
					}
				}
				$if_identifier = $this->getUid();
				$sql = "insert into information_fields (if_identifier, if_name, if_client, if_list, if_label, if_rank, if_type, if_screen, if_link, if_filterable, if_sumlabel, if_conlabel, if_map, if_special) values ($if_identifier,'$name', '$this->client_identifier', '$identifier', '$label', '$rank', '$type', ".($i+1).", $l, $if_filterable, $if_sumlabel, $if_conlabel, '$if_map', '$special')";
				$this->parent->db_pointer->database_query($sql);
				if($type=="URL"){
					if($url!=""){
						$ievl_identifier = $this->getUid();
						$sql = "insert into information_entry_links ($ievl_identifier, ievl_client, ievl_field, ievl_screen, ievl_mapped, ievl_list) values ($ievl_identifier, $this->client_identifier, '$name', ".($i+1).", '$url', $identifier)";
						$this->parent->db_pointer->database_query($sql);
					}
				}
				/*************************************************************************************************************************
                * build the display form xml block (summary and content
                *************************************************************************************************************************/
				if($type=="rowsplitter"){
					$fields .= "		</seperator>\n	</seperator_row>\n	<seperator_row>\n		<seperator>\n";
				} else if($type=="colsplitter"){
					$fields .= "		</seperator>\n		<seperator>\n";
				} else{
					if($i==2){
						if($type=="text" || $type=="smallmemo" || $type=="memo"){
							$fields .= "<input type='text' name='$name'><label><![CDATA[".$label."]]></label><value></value></input>\n";
						}else{
							$mnof	= $this->check_parameters($parameters,"max_number_of_fields");
							$options ="";
							if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"[Options]",__LINE__,"$mnof - $name"));}
							for($findIndex=0;$findIndex<$mnof;$findIndex++){
								if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"[Options]",__LINE__,"hfield_".$index." = ".$this->check_parameters($parameters,"hfield_".$index).""));}
								if($this->check_parameters($parameters,"hfield_".$findIndex)==$name){
									$options = $this->check_parameters($parameters,"options_".$findIndex);
									if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"[Options]",__LINE__,"options_".$index." - $options"));}
								}
							}
							$optionlist = split("::ls_option::", $options);
							$fields .= "<select name='$name'><label><![CDATA[".$label."]]></label>";
							$fields .= "<option value=''><![CDATA[Choose one]]></option>";
							for ($opt =0, $max = count($optionlist); $opt <$max ;$opt++){
								if ($optionlist[$opt]!=''){
									$fields .= "<option><![CDATA[".$optionlist[$opt]."]]></option>";
								}
							}
							$fields .= "</select>\n";
						}
					}else {
						if ($i==0 && $name=='ie_title'){
						 	$fields .= "<field id='$name' link='$l' sumlabel='$if_sumlabel' conlabel='$if_conlabel'><label><![CDATA[".$label."]]></label></field>\n";
						} else if($type=="URL"){
							$fields .= "<field id='$name' link='$l' sumlabel='$if_sumlabel' conlabel='$if_conlabel'><label><![CDATA[".$label."]]></label></field>\n";
						} else if($name=="ie_price"){
							$fields .= "<field link='$l' id='$name' sumlabel='$if_sumlabel' conlabel='$if_conlabel' currency='[[".strtolower($this->check_parameters($settings,"ss_currency","gbp"))."]]'><label><![CDATA[".$label."]]></label></field>\n";
						} else {
							//print "[$if_filterable = [".$r["if_label"]."]";
							if ($if_filterable==1){
								$filteroptions="";
								$sql = "select * from information_list
											inner join information_fields on info_identifier = if_list and if_client = info_client and if_filterable=1 and if_name='$name'
											inner join information_options on io_list = info_identifier and io_field = if_name and io_client = info_client
										where info_identifier = $identifier and info_client=$this->client_identifier and if_screen=0";
						        $result  = $this->parent->db_pointer->database_query($sql);
						        while($r = $this->parent->db_pointer->database_fetch_array($result)){
									$filteroptions .= "<option value='_filter-".$this->make_uri(urldecode($r["if_label"]))."-".$this->make_uri(urldecode($r["io_value"])).".php'><![CDATA[".urldecode($r["io_value"])."]]></option>";
							    }
								$fields .= "<field id='$name' link='$l' filter='1' sumlabel='$if_sumlabel' conlabel='$if_conlabel'><label><![CDATA[".$label."]]></label><filteroptions>$filteroptions</filteroptions></field>\n";
							} else {
								if($name=='__add_to_basket__'){
									$pres = substr($this->module_presentation,0,strlen($this->module_presentation)-1);
									$fields .= "<field id='$name' link='$l' webcontainer='$pres' sumlabel='$if_sumlabel' conlabel='$if_conlabel'><label><![CDATA[".$label."]]></label></field>\n";
								} else {
									$fields .= "<field id='$name' link='$l' sumlabel='$if_sumlabel' conlabel='$if_conlabel'><label><![CDATA[".$label."]]></label></field>\n";
								}
							}
						}
					}
				} 
			}
			$fields .= "</seperator>\n</seperator_row>\n";
			/*************************************************************************************************************************
			* Save this screens field layout
			*************************************************************************************************************************/
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
			if ($i==0){
				$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$identifier."_summary.xml";
			} else if ($i==1){
				$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$identifier."_content.xml";
			} else {
				$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$identifier."_search.xml";
			}
//			print "<li>$fname</li>";
			$fp = @fopen($fname,"w");
			if($fp){
				fputs($fp,$fields);
				fclose($fp);
			}
			$um = umask(0);
			@chmod($fname, LS__FILE_PERMISSION);
			umask($um);
//			print "[$fields]\n\n\n\n\n";
		}
//		$this->exitprogram();
	}
	

	
	/*************************************************************************************************************************
	* remove any unused display options for the directory manager INFORMATION_DISPLAY
	*************************************************************************************************************************/
	function set_channel($channel,$location){
		$sql = "select * from display_data
					left outer join information_list on (info_menu_location = display_menu and display_client=info_client)
				where display_client=$this->client_identifier and display_command in ('INFORMATION_A2Z', 'INFORMATION_DISPLAY') and info_identifier is null";
		$result  = $this->parent->db_pointer->database_query($sql);
		$list_to_remove = "";
		if ($this->call_command("DB_NUM_ROWS",array($result)) > 0){
			$list_to_remove = "display_identifier in (";
			$c=0;
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			$c++;
				if ($c!=1){
					$list_to_remove .= ", ";
				}
				$list_to_remove .= $r["display_identifier"];
			}
			$list_to_remove .= ") or ";
		}
		$this->parent->db_pointer->database_free_result($result);
		
		$sql = "delete from display_data where display_client=$this->client_identifier and display_command in ('INFORMATION_A2Z', 'INFORMATION_DISPLAY') and ($list_to_remove display_menu=$location)";
		$this->parent->db_pointer->database_query($sql);
		$sql = "insert into display_data (display_client, display_command, display_menu) values ($this->client_identifier, '$channel', $location);";
		$this->parent->db_pointer->database_query($sql);
	}
	/*************************************************************************************************************************
	* call the system restore of the information directories
	* <strong>warning</strong> this can take some time
	*************************************************************************************************************************/
	function restore($parameters){
		$restore_page			= $this->check_parameters($parameters, "restore_page", 0);
		$refresh				= $this->check_parameters($parameters, "refresh", -1);
		$identifier				= $this->check_parameters($parameters, "list", -1);
		$show_import_confirm	= $this->check_parameters($parameters, "show_import_confirm", -1);		
//		$category_id			= $this->check_parameters($parameters, "category_id", -1);
//		$category_parent		= $this->check_parameters($parameters, "category_parent", -1);
//		$category_parent_prev	= $this->check_parameters($parameters, "category_parent_prev", -1);
		$_SESSION["restore_page"]="$restore_page";
		if($refresh!=-1){
		} else {
			$_SESSION["list"]=-1;
			if (isset($_SESSION["list"])){ // second time through reset for previous list (condition futher down will catch that we are on second directory;
				$identifier = $_SESSION["list"];
			}
		}
		$_SESSION["list"]="$identifier";
		
		$cat = Array();
		if (($identifier==-1) || ($identifier=="")){
			$w = "";
		}else {
			$w = "information_list.info_identifier = $identifier and ";
		}
		$sql = "select
					information_list.info_category,
					information_list.info_identifier,
					information_list.info_label,
					information_list.info_in_menu,					
					information_list.info_summary_layout,
					menu_data.menu_identifier,
					menu_data.menu_url,
					menu_data.menu_directory
				from
					information_list
				inner join
				menu_data on menu_identifier = info_menu_location
				where $w information_list.info_client = $this->client_identifier and info_owner='$this->webContainer'
				order by information_list.info_identifier ";

		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->parent->db_pointer->database_query($sql);
		$numrows = $this->call_command("DB_NUM_ROWS",array($result));
		$starter=-1;
		if ($numrows > 0 && $numrows > $restore_page){
			if ($restore_page>0){
				
				$this->call_command("DB_SEEK",array($result,));
			}
			$c=0;
			$found=0;
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) && $found==0){
				if($restore_page==$c){
					$found==1;
					$list 					= $r["info_identifier"];
				    $info_category			= $r["info_category"];
	                $info_label   	 		= $r["info_label"];
					$start_path     		= dirname($r["menu_url"])."/";
					$fake_uri				= $r["menu_url"];
					$mid 					= $r["menu_identifier"];
					$info_in_menu			= $r["info_in_menu"];					
					$info_summary_layout	= $r["info_summary_layout"];
	                $this->make_special($mid, $list, $info_label, $info_summary_layout);
					$sql		= "select * from category
									where cat_client= $this->client_identifier and cat_list_id = $info_category
									order by cat_parent, cat_identifier, cat_label";
					//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
					/** If category needs to be displayed in menu then cache menu file */
					if ($info_in_menu ==1){
						$this->call_command("CATEGORYADMIN_CACHE_MENU",Array("cat_root"=>$info_category, "menu_identifier"=>$mid, "info_in_menu" => $info_in_menu));			
					}	
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					$result_cat = $this->parent->db_pointer->database_query($sql);
					$pos = 0; // start with empty array
					while ($cat_r = $this->parent->db_pointer->database_fetch_array($result_cat)){
						if ($cat_r["cat_parent"]==-1){
							$starter = $cat_r["cat_identifier"];
						}
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__, "<li>".$pos .") ".$cat_r["cat_label"]."</li>"));}
						$cat[$pos]= Array(
							"cat_label"		=> $cat_r["cat_label"],
							"cat_parent"	=> $cat_r["cat_parent"],
							"cat_identifier"=> $cat_r["cat_identifier"],
							"cat_url"		=> $this->make_uri($cat_r["cat_label"])
						);
						$pos++;
					}
                    $this->parent->db_pointer->database_free_result($result_cat);
//					print_r($cat);
					//print "[$cat,$starter,$start_path, $fake_uri, $list]";
				    $this->recurse_build($cat,$starter,$start_path, $fake_uri, $list);
				    if($show_import_confirm != 1) {
						$this->parent->db_pointer->database_query("Update information_entry set ie_uri='' where ie_list = $list and ie_client=$this->client_identifier");
				    }	
				}
				$c++;
            }
            $this->parent->db_pointer->database_free_result($result);
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"restore",__LINE__,"command=".$this->module_command."RESTORE_ENTRIES&amp;list=$list&amp;page=1&amp;restore_page=".($restore_page+1).""));}
//			print "<li>".__FILE__."@".__LINE__."<p>"."command=".$this->module_command."RESTORE_ENTRIES&amp;list=$list&amp;page=1&amp;restore_page=".($restore_page+1)."</p></li>";
            //$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."RESTORE_ENTRIES&amp;list=$list&amp;page=1&amp;restore_page=".($restore_page+1)));
            $this->restore_entries(array("list"=>$list,"page"=>1,"restore_page"=>$restore_page+1));
            if($show_import_confirm == 1) {
            	$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."IMPORT_CONFIRM"));            	
            }            
            
		} else {
			$index	= $this->check_parameters($_SESSION,"ENGINE_RESTORE_INDEX",-1);
			if ($index==-1){
				$dst = $this->check_parameters($_SESSION, "REDIRECT_AFTER", "__NOT_FOUND__");
				if ($dst=="__NOT_FOUND__"){
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST"));
				} else {
					unset($_SESSION["REDIRECT_AFTER"]);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=$dst"));
				}
			} else {
				$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=ENGINE_RESTORE"));
			}
		}
	}

	function recurse_build($categories, $parent, $start_path, $fake_uri, $list, $prevCatPath=""){
		$root  	= $this->check_parameters($this->parent->site_directories,"ROOT");
		$max = count($categories);
		for($index=0; $index<$max;$index++){
			//print 'cat parent='.$categories[$index]["cat_parent"].'   par='.$parent;
			if ($categories[$index]["cat_parent"]==$parent){
				$categories[$index]["url"] = $start_path . $this->make_uri($categories[$index]["cat_label"]);
				if ($this->module_debug) {$this->call_command("UTILS_DEBUG_ENTRY", array($this->module_name, "SQL", __LINE__, "<LI> - " . $categories[$index]["url"] . "</LI>"));}
				if($prevCatPath!=""){
					$CatPath = $prevCatPath."/".$this->make_uri($categories[$index]["cat_label"]);
				} else {
					$CatPath = $this->make_uri($categories[$index]["cat_label"]);
				}

				$this->web_entry(
					"CREATE",
					$root . "/".$categories[$index]["url"] . "/index.php",
					$fake_uri, 
					$list, 
					$categories[$index]["cat_identifier"], "",
					$CatPath
				);
				$this->recurse_build($categories, $categories[$index]["cat_identifier"], $categories[$index]["url"]."/", $fake_uri, $list, $CatPath);
			}
		}
	}

	function make_new_dir($path){
		if (!file_exists($path)){
			$oldumask = umask(0);
			@mkdir($path, LS__DIR_PERMISSION); // or even 01777 so you get the sticky bit set
 			umask($oldumask);
		}
	}

	function web_entry($cmd, $src_file, $fake_uri, $list_id, $cat_id, $orginal_file="", $cat_path=""){
		$src_file = join("/",split("\\\\",$src_file));
		$root = $this->check_parameters($this->parent->site_directories,"ROOT");
		if($cmd == "MOVE"){
			@unlink($orginal_file);
			$cmd = "CREATE";
		}
		if($cmd == "CREATE"){
			$dir = substr_replace(dirname($src_file), '', 0, strlen($root)) ;
			$directories = split('/',$dir);
			$directorycount = count($directories);
			$directory_to_root="";
			$parent_directory="";
			for($index=0;$index<$directorycount-1;$index++){
				$directory_to_root.="../";
				$parent_directory .= $directories[$index]."/";
			}
			if ($parent_directory!="/"){
				if (!file_exists($root.$parent_directory."/.")){
					$um = umask(0);
					@mkdir($root.$parent_directory, LS__DIR_PERMISSION);
					umask($um);
				}
			}
			if (!file_exists(dirname($src_file)."/.")){
				$um = umask(0);
				@mkdir(dirname($src_file), LS__DIR_PERMISSION);
				umask($um);
			}
			$um = umask(0);
			@chmod(dirname($src_file), LS__DIR_PERMISSION);
			umask($um);
			if (file_exists($src_file)){
				$um = umask(0);
				@chmod($src_file, LS__FILE_PERMISSION);
				umask($um);
			}
			//print '<li> file '.$src_file.'<li>';
			$fp = fopen($src_file,"w");
//			$module_directory = $this->check_parameters($this->parent->site_directories,"MODULE_DIR",$directory_to_root);
			if($fp){
//\$fake_uri	= substr(\$script_file, strpos(\$script_file,\$site_root)+strlen(\$site_root)+1, - strlen(\"".$cat_path."\")).\"index.php\";
				fputs($fp,"<"."?php
\$script_file	= dirname(\$_SERVER[\"SCRIPT_FILENAME\"]);
\$root 			= '$root';
\$site_root		= \$_SERVER[\"DOCUMENT_ROOT\"];
\$fake_uri		= get(\$script_file, \$root, \$site_root);
\$category 	= \"$cat_id\";
\$mode		= \"EXECUTE\";
\$command	= \"".$this->module_presentation."DISPLAY\";
\$identifier= \"$list_id\";
require_once \"".$root."/admin/include.php\";
require_once \"\$module_directory/included_page.php\";
function get(\$sfile, \$rt, \$sroot){
	\$cat = \"$cat_path\";
	if (strpos(\$sfile,\$rt)===false){
		return substr(\$sfile, strpos(\$sfile,\$sroot)+strlen(\$sroot)+1, - strlen(\$cat)).\"index.php\";
	} else {
		\$l = split(\$rt.\"/\",\$sfile);
		if(strlen(\$cat)==0){
			return \$l[1].\"/index.php\";
		} else {
			return substr(\$sfile, strpos(\$sfile,\$rt)+strlen(\$rt)+1, - strlen(\$cat)).\"index.php\";
		}
	}
}
?".">");				fclose($fp);
			}
			$um =umask(0);
			@chmod($src_file, LS__FILE_PERMISSION);
			umask($um);
		}
	}
	

	
	function find_path($id){
		if ($id !=-1){
			for ($index=0, $m = count($this->loadedcat); $index< $m ; $index++){
				if ($this->loadedcat[$index]["cat_identifier"] == $id && $this->loadedcat[$index]["cat_list_id"] != $id){
					if ($id !=-1){
						return $this->find_path($this->loadedcat[$index]["cat_parent"]) ."/". $this->make_uri($this->loadedcat[$index]["cat_label"]);
					} else {
						return "";//$this->make_uri($this->loadedcat[$index]["cat_label"]);
					}
				}
			}
		}
	}

	function restore_entries($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"".print_r($parameters,true).""));}
//		$identifier 						= $this->check_parameters($parameters,"identifier",0);
		$page 								= $this->check_parameters($parameters,"page",1);
		$list 								= $this->check_parameters($parameters,"list",-1);
		$build_directory_files				= $this->check_parameters($parameters,"build_directory_files",1);
		$restore_page						= $this->check_parameters($parameters,"restore_page",0);
		$cache_type							= $this->check_parameters($parameters,"cache_type","ALL");		
		$ie_identifier						= $this->check_parameters($parameters,"ie_identifier",-1);				
		
		if($cache_type == "ALL"){
			if($list!=-1){
				$sql = "select distinct ie_identifier, menu_url from information_entry
							inner join information_list on ie_list = info_identifier and ie_client = info_client
							inner join menu_data on menu_identifier = info_menu_location and menu_client = info_client
					 	where
							ie_uri=''  and ie_published=1 and ie_version_wip=1 and ie_list = $list and ie_status = 1 and ie_client = $this->client_identifier and info_owner='$this->webContainer'
				";

				//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";

	//			$this->exitprogram();
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->page_size=50000;
				$result  = $this->parent->db_pointer->database_query($sql);
				if ($result){
					$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
					if ($number_of_records>0){
						$c =0;
						while (($r = $this->parent->db_pointer->database_fetch_array($result)) && ($c<$this->page_size)){
							$this->cache_entry(Array("identifier" => $r["ie_identifier"], "list" => $list, "url" => $r["menu_url"], "build_directory_files" => $build_directory_files));
							$c++;
							//print "<li>$c</li>";
						}
						$this->parent->db_pointer->database_free_result($result);					
						//$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."RESTORE_ENTRIES&amp;list=$list&amp;restore_page=$restore_page"));
					}
				}
			}
		}		
		elseif ($cache_type == "SINGLE" && $ie_identifier != -1)	{
			$sql = "select d.ie_identifier,b.info_identifier,c.menu_url from information_list as b,menu_data as c, information_entry as d where d.ie_identifier=$ie_identifier and b.info_identifier=d.ie_list and c.menu_identifier=b.info_menu_location and d.ie_version_wip=1";				
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			if ($number_of_records>0){
				$r = $this->parent->db_pointer->database_fetch_array($result);
				$this->cache_entry(Array("identifier" => $r["ie_identifier"], "list" =>$r["info_identifier"], "url" => $r["menu_url"], "build_directory_files" => $build_directory_files));
				$this->parent->db_pointer->database_free_result($result);					
			}
			
		}
//		print "<li>".__FILE__."@".__LINE__."<p>command=".$this->module_command."RESTORE&amp;restore_page=$restore_page</p></li>";
//		$this->exitprogram();
		//$this->restore(array("list"=>$list));
		//$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."RESTORE&amp;restore_page=".$restore_page."&amplist=".$list));
	}

	function translate_to_filename($url, $title, $id, $fake_uri, $cat, $show_id=1, $file_name=""){
		$root				= $this->parent->site_directories["ROOT"];
//		print "root ".$root."fake uri = ".$fake_uri . " url =". $url. " ";				
//		$dir 				= dirname($root."/".$url);
		$dir 				= dirname(dirname($root."/".$fake_uri).$url);
		if($file_name==""){
			if ($show_id==1){
				$filename		 	= $title."-$id.php";
			} else {
				$filename		 	= $title.".php";
			}
		} else {
			$filename		 	= $file_name;
		}
//		print "<li>$filename</li>";
		$directories 		= split('/',$url);
		$directorycount		= count($directories)-1;
		$directory_to_root	= "";

		if ($directorycount>0){
 			for($index=0;$index<$directorycount;$index++){
				$directory_to_root .= "../";
			}
		}

		if (!is_dir($dir)){
			$um = umask(0);
			mkdir($dir, LS__DIR_PERMISSION,true);
			umask($um);
		}
		
		$um = umask(0);
		chmod($dir, LS__DIR_PERMISSION);
		umask($um);
		
		
		$fp = fopen($dir."/".$filename, 'w');
		$module_directory = $this->check_parameters($this->parent->site_directories,"MODULE_DIR",$directory_to_root);
///		print $dir."/".$filename;
		fwrite($fp, "<"."?php
".'$'."script	= \"$url\";
".'$'."category	= \"$cat\";
					\$script_file	= dirname(\$_SERVER[\"SCRIPT_FILENAME\"]);
					\$root 			= '$root';
					\$site_root		= \$_SERVER[\"DOCUMENT_ROOT\"];
					\$fake_uri		= get(\$script_file, \$root, \$site_root);
/"."* cut *"."/
".'$'."mode 		=\"EXECUTE\";
".'$'."identifier= \"$id\";\n
".'$'."command	=\"".$this->module_presentation."SHOW\";
require_once \"".$root."/admin/include.php\"; \r\n
require_once \"".$module_directory."/included_page.php\"; \r\n 
function get(\$sfile, \$rt, \$sroot){
	\$cat = \"".substr(dirname($url),1)."\";
	if (strpos(\$sfile,\$rt)===false){
		return substr(\$sfile, strpos(\$sfile,\$sroot)+strlen(\$sroot)+1, - strlen(\$cat)).\"index.php\";
	} else {
		\$l = split(\$rt.\"/\",\$sfile);
		if(strlen(\$cat)==0){
			return \$l[1].\"/index.php\";
		} else {
			return substr(\$sfile, strpos(\$sfile,\$rt)+strlen(\$rt)+1, - strlen(\$cat)).\"index.php\";
		}
	}
}
?".">");

		fclose($fp);
		$um =umask(0);
		@chmod($dir."/".$filename, LS__FILE_PERMISSION);
		umask($um);
		clearstatcache();
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
	
	function cp($wf, $wto){ // it moves $wf to $wto
		$um =umask(0);
		mkdir($wto,LS__DIR_PERMISSION);
		umask($um);
		$arr=ls_a($wf);
		foreach ($arr as $fn){
			if($fn){
				$fl="$wf/$fn";
				$flto="$wto/$fn";
				if(is_dir($fl)) 
					cp($fl,$flto);
				else 
					copy($fl,$flto);
			}
		}
	}

	function ls_a($wh, $dir=1){ 
		$files="";
		if ($listhandle = opendir($wh)) {
			while (false !== ($file = readdir($listhandle))) { 
				if ($file != "." && $file != ".." ) { 
					if ($dir==1){
						if($files=="")
							$files="$file";
						else
							$files="$file\n$files"; 
					} else {
						if (!is_dir($file)){
							if($files=="")
								$files="$file";
							else
								$files="$file\n$files"; 
						}
					}
				} 
			}
			closedir($listhandle); 
		}
		$arr=explode("\n",$files);
		return $arr;
	}
	
	function rename_or_move($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"rename_or_move",__LINE__,"".print_r($parameters,true).""));}
//		print "rename_or_move(".print_r($parameters,true).")";
		$cmd					= $this->check_parameters($parameters,"cmd");
		$info_identifier		= $this->check_parameters($parameters,"info_identifier",-1);
		$identifier				= $this->check_parameters($parameters,"cat_id",-1);
		$cat_parent				= $this->check_parameters($parameters,"cat_parent",-1);
		$cat_parent_prev		= $this->check_parameters($parameters,"cat_parent_prev",-1);
		$cat_label				= $this->check_parameters($parameters,"cat_label");
		$cat_label_prev 		= $this->check_parameters($parameters,"cat_label_prev");
		$list_id				= $this->check_parameters($parameters,"list_id");
		$root  					= $this->check_parameters($this->parent->site_directories,"ROOT");
		if (count($this->loadedcat)==0){
			$this->loadedcat = $this->call_command("CATEGORYADMIN_LOAD", Array("returntype"=>1, "list" => $list_id));
		}
		if ($cmd == "rename" || $cmd == "move" || $cmd == "rename_move"){
			$sql	= "select menu_url,info_category, info_menu_location, info_in_menu from menu_data inner join information_list on info_menu_location = menu_identifier where info_identifier=$info_identifier and info_category = $list_id";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$fake_uri 			= dirname($r["menu_url"]);
				$info_menu			= $r["info_in_menu"];
				$info_category		= $r["info_category"];
				$info_menu_location	= $r["info_menu_location"];
				$info_in_menu		= $r["info_in_menu"];								
			}
			/** Cache menu if directory is set to 'Display category in menu' */
			$this->call_command("CATEGORYADMIN_CACHE_MENU",Array("cat_root"=>$info_category, "menu_identifier"=>$info_menu_location, "info_in_menu" => $info_in_menu));			
			
			$uri					= $root ."/".$fake_uri. $this->find_path($cat_parent)."/".$this->make_uri($cat_label);
			$uri_prev 				= $root ."/".$fake_uri. $this->find_path($cat_parent_prev)."/".$this->make_uri($cat_label_prev);
//			print "renaming::->[$uri_prev] to [$uri]";
			if (file_exists($uri_prev."/.")){
				$um =umask(0);
				chmod($uri_prev, LS__DIR_PERMISSION);
				umask($um);
				@rename($uri_prev,$uri);
			}
			else {
				// rename did not work
				$this->cp($uri_prev,$uri);
			}
			
			//print "<li>curr=" .$this->make_uri(htmlentities(trim($this->strip_tidy($cat_label))))."  prev=" .$this->make_uri($cat_label_prev). "  </li>";
			$this->replaceStringinDir($uri,$this->make_uri($cat_label_prev),$this->make_uri(htmlentities(trim($this->strip_tidy($cat_label)))));
			
		} else if ($cmd == "new"){
			$sql	= "select menu_url, info_identifier,info_category, info_menu_location, info_in_menu from menu_data inner join information_list on info_menu_location = menu_identifier where info_category = $list_id and info_client = $this->client_identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			$fake_uri="";
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$fake_uri 			= $r["menu_url"];
				$list_id 			= $r["info_identifier"];
				$info_category		= $r["info_category"];
				$info_menu_location	= $r["info_menu_location"];
				$info_in_menu		= $r["info_in_menu"];				
			}
			$this->parent->db_pointer->database_free_result($result);
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::create",__LINE__,"CREATE, ".$root."/".dirname($fake_uri).$this->find_path($identifier)."/index.php,	$fake_uri, $list_id, $identifier ,$cat_label"));}
			$cat_path				= $this->find_path($cat_parent)."/".$this->make_uri($cat_label);
			$cat_path				= substr($cat_path ,strpos($cat_path,"/")+1);
			//print "<li> $cat_path <li>";
			$this->web_entry("CREATE", $root."/".dirname($fake_uri).$this->find_path($identifier)."/index.php",	$fake_uri, $list_id, $identifier ,"",$cat_path);
			/** Cache menu if directory is set to 'Display category in menu' */
			$this->call_command("CATEGORYADMIN_CACHE_MENU",Array("cat_root"=>$info_category, "menu_identifier"=>$info_menu_location, "info_in_menu" => $info_in_menu));			
		} else if (($cmd=="merge") || ($cmd=="merge_remove")){
			$sql	= "select menu_url from menu_data inner join information_list on info_menu_location = menu_identifier where info_identifier=$info_identifier and info_category = $list_id";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$fake_uri = dirname($r["menu_url"]);
				$muri = $r["menu_url"];
			}
			$muri					= $root ."/".$muri;
			$uri					= $root ."/".$fake_uri. $this->find_path($identifier);
			$uri_merge 				= $root ."/".$fake_uri. $this->find_path($cat_parent);
			if ($mergehandle = opendir($uri)) {
				while (false !== ($file = readdir($mergehandle))) {
					if ($file != "." && $file != ".." && $file != "index.php"){
						if(!is_dir($file)){
							@rename($uri."/".$file, $uri_merge."/".$file);
						}
					}
				}
			}
			@closedir($mergehandle); 
			$this->cache_entry_category(Array("list_id"	=>$info_identifier,"to"=>$cat_parent));
			if ($cmd=="merge_remove"){
				if ($muri!=$uri){
					@unlink ($uri);
				}
			} else {
				if ($muri!=$uri){
					return $uri;
				}
			}
		}
	}

	/*************************************************************************************************************************
    * 									FUNCTION TO REPLACE STRING IN ALL FILES OF DIRECTORY
    ************************************************************************************************************************** */
	function replaceStringinDir($dir, $find_str, $rep_str){
		if (is_dir($dir)){
			$d = dir($dir);
			while (false !== ($entry = $d->read())) { 
				if (substr($entry,-4)==".php"){
					//print "<li>". $uri."/".$entry. "</li>";
					$file_to_open = $dir."/".$entry;
					$file_contents = file_get_contents($file_to_open);
					$file_contents = str_replace($find_str,$rep_str,$file_contents);
					$fp = fopen($file_to_open,"w");
					fwrite($fp,$file_contents);
					fclose($fp);
				}
			} 
		}	
	}
	/*************************************************************************************************************************
    * 									E N T R Y   M A N A G E M E N T   F U N C T I O N S 
    **************************************************************************************************************************
	*
	* A series of functions for the administrative managment of entries in the system
	*
	*************************************************************************************************************************/
	
	/*************************************************************************************************************************
	* filter the entries for this directory entries
	*************************************************************************************************************************/
	function filter_entries($parameters,$type){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_filter",__LINE__,"[]"));
		}
		$identifier 	= $this->check_parameters($parameters,"identifier",0);
		$keywords		= $this->check_parameters($parameters,"keywords","");
//		$page_boolean	= $this->check_parameters($parameters,"page_boolean","or");
		$search 		= $this->check_parameters($parameters,"search",0);
//		$page_search 	= $this->check_parameters($parameters,"page_search");
		$status_filter 	= $this->check_parameters($parameters,"status_filter",-1);
		$search++;
		
		$out  = "\t\t\t\t<form name=\"filter_form\" label=\"".FILTER_RESULTS."\" method=\"get\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\"><![CDATA[$type]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" ><![CDATA[1]]></input>\n";
		$out .="<input type=\"text\" name=\"keywords\" label=\"Search phrase\" size=\"255\"><![CDATA[$keywords]]></input>";
		$out .="\t\t\t\t\t<select label=\"Status of Entries\" name=\"status_filter\">
			<option value=\"-1\">All </option>
			<option value=\"0\"";
		if ($status_filter==0){
			$out .= " selected='true'";
		}
		$out .= ">Requiring Approval</option>
			<option value=\"1\"";
		if ($status_filter==1){
			$out .= " selected='true'";
		}
		$out .= ">Approved</option>
		</select>";
		$out .= "\t\t\t\t\t<input type=\"submit\" iconify=\"SEARCH\" name=\"\" value=\"".FILTER_RESULTS."\"/>\n";
		$out .= "\t\t\t\t</form>";
		return $out;
	}
	
	/*************************************************************************************************************************
	* list of entries that can be managed
	*
	* @param Array ("identifier", "status_filter", "keywords")
	* @return String XML formated string holding the representation of the list of entries
	*************************************************************************************************************************/
	function list_entries($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		$status_filter	= $this->check_parameters($parameters,"status_filter",-1);
		$keywords		= str_replace(Array(" ","'"), Array("%","&#39;"), $this->check_parameters($parameters,"keywords"));
		$status_sql		= "";
		/*
			$sql = "select distinct * from user_to_object where uto_client = $this->client_identifier and uto_identifier = $uid";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result_obj  = $this->parent->db_pointer->database_query($sql);
            while($r_obj = $this->call_command("DB_FETCH_ARRAY",Array($result_obj))){
     			$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("edit" , $r_obj["uto_module"]."EXECUTE_EDIT&amp;module_identifier=".$r_obj["uto_object"],"edit associated");
            }
            $this->call_command("DB_FREE",Array($result_obj));
		*/

	/** For Cruise or Mind associates Site to change Order By Column if field name is ' Order ' portion starts (Added By Muhammad Imran Mirza) **/
	
		$order_by_column = '';
		$iev_entry_values_str =  '';
	
		if (($this->parent->db_pointer->database == 'system_cruise_new' && $identifier == '105367289483993633') || ($this->parent->db_pointer->database == 'system_libertas' && $identifier == '120082049030309504')){
			$sql_order = "select information_fields.* from information_fields 
									inner join information_list on info_identifier = if_list 
									where if_client = $this->client_identifier and if_label = 'Order' and info_status=1 and if_screen=0";
				$result_order = $this->parent->db_pointer->database_query($sql_order);
				$number_of_records_order = $this->call_command("DB_NUM_ROWS",array($result_order));
				if ($number_of_records_order >= 1) {
					
					if ($this->parent->db_pointer->database == 'system_cruise_new' && $identifier == '105367289483993633')
						$order_by_field_name = 'ie_otext1';
					elseif ($this->parent->db_pointer->database == 'system_libertas' && $identifier == '120082049030309504')
						$order_by_field_name = 'ie_otext5';
						
					$iev_entry_values_str = " left outer join information_entry_values as iev on iev_entry = ie_identifier and iev_field = '$order_by_field_name' ";
//					$order_by_column = " CAST( SUBSTRING( iev_value, 4, length( iev_value ) -7 ) AS SIGNED )";
					$order_by_column = " SUBSTRING( iev_value, 4, length( iev_value ) -7 )";
				}
		}//if cmstest
			
			if ($order_by_column == "")
				$order_by_column = 'md_title';
				
	/** For Cruise Site or Mind associates to change Order By Column if field name is ' Order ' portion ends (Added By Muhammad Imran Mirza) **/


		if ($status_filter!=-1){
			$status_sql .= " ie_status = $status_filter and ";
		} 
		if($keywords!=""){
			$status_sql .= " md_title like '%$keywords%' and ";
		}
		$sql = "select distinct ie_identifier, user_to_object.*, ie_parent, ie_status, md_title, md_link_group_id, info_update_access,  formbuilder_module_map.*, ie_user from information_entry 
					inner join metadata_details on md_module = '$this->webContainer' and md_client=ie_client and ie_identifier = md_link_id
					inner join information_list on info_identifier = ie_list and info_client = ie_client
					left outer join user_to_object on uto_client = ie_client and uto_identifier = ie_user and uto_module='FORMBUILDERADMIN_'
					left outer join formbuilder_settings on fbs_identifier = uto_object and uto_client = fbs_client
					left outer join formbuilder_module_map on fbmm_module = '$this->webContainer'  and fbmm_client = ie_client and fbmm_link_id = ie_list and fbmm_setting = fbs_identifier
					$iev_entry_values_str
				where $status_sql ie_client = $this->client_identifier and ie_list = $identifier and ie_version_wip =1 order by $order_by_column"; //,ie_status asc,md_date_remove desc
		$result  = $this->parent->db_pointer->database_query($sql);
		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$variables = Array();
		$variables["FILTER"]			= $this->filter_entries($parameters,$this->module_command."LIST_ENTRIES");
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["HEADER"]			= LOCALE_INFO_DIRECTORY_HEADER." - Entry manager";

		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned] <pre>$sql</pre>"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$this->page_size = 50;
			$prev = $this->page_size;
			
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$this->call_command("DB_SEEK",array($result,$goto));
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
			$variables["PAGE_BUTTONS"] = Array();
			if($this->manage_database_list==1){
				$variables["PAGE_BUTTONS"][0] = Array("CANCEL",$this->module_command."LIST", "Cancel");
			}
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			//			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
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
			$counter=0;
//			$page_discussion=0;
//			$trans_page=0;
			$lockable=1;
			$add_normal=1;
			$add_button=Array();
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$lockable = $r["info_update_access"];
				if ($this->check_parameters($r,"ie_status","0")=="0"){
					$ie_status = "Requires Approval";
				} else {
					$ie_status = "Approved";
				}

		/************* Only for Cruise Members Database Get Fname portion starts (Added By Muhammad Imran Mirza) *************/
				
				if ($this->parent->db_pointer->database == 'system_cruise_new' && $identifier == '104411033734998109'){
					$iev_entry = $this->check_parameters($r,'ie_identifier','');
					
					$sql_entry_values = "select * from information_entry_values
										where iev_client = $this->client_identifier
										and iev_entry = $iev_entry 
										and iev_field = 'ie_otext2'";
					$result_entry_values = $this->parent->db_pointer->database_query($sql_entry_values);
					$number_of_records_entry_values = $this->call_command("DB_NUM_ROWS",array($result_entry_values));
					if ($number_of_records_entry_values >= 1){
						$r_entry_values = $this->parent->db_pointer->database_fetch_array($result_entry_values);
						$fname = strip_tags($r_entry_values["iev_value"]);
					}
					$md_title_fname = $this->check_parameters($r,"md_title","").', '.$fname;
				}else{
					$md_title_fname = $this->check_parameters($r,"md_title","");
				}// if cruise Site and Members Database
				
		/************* Only for Cruise Members Database Get Fname portion ends (Added By Muhammad Imran Mirza) *************/
				
				//print "[".$this->check_parameters($r,"md_title","")."]";
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["ie_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		htmlentities($md_title_fname),"TITLE"),
						Array("Status",	$ie_status, "YES")
					)
				);
				if ($this->author_admin_access || $this->approve_admin_access){
					if($this->check_parameters($r,"fbmm_module","__NOT_FOUND__")=="__NOT_FOUND__"){
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."EDIT&amp;list_id=".$identifier,EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."REMOVE&amp;list_id=".$identifier,REMOVE_EXISTING);
					} else {
//						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."EDIT&amp;list_id=".$identifier,EDIT_EXISTING);
                    	$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT" , $r["uto_module"]."EXECUTE_EDIT&amp;module_identifier=".$r["uto_object"]."&amp;next_command=".$this->module_command."LIST_ENTRIES&amp;next_id=$identifier&amp;user=".$r["ie_user"],"Edit User");
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$r["uto_module"]."EXECUTE_DELETE&amp;list_id=$identifier&amp;user_id=".$r["ie_user"]."&amp;module_identifier=".$r["uto_object"],"Delete User");
					
						$add_button = Array("ADD" , $r["uto_module"]."EXECUTE_ADD&amp;identifier=".$r["uto_object"]."&amp;next_command=".$this->module_command."LIST_ENTRIES&amp;next_id=$identifier",ADD_NEW);
						$add_normal = 0;
					}
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("HISTORY",$this->module_command."ENTRY_HISTORY&amp;list_id=".$identifier."&amp;parent_id=".$r["ie_parent"],"View History");
					if($lockable==2){
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("HISTORY",$this->module_command."ENTRY_USERS&amp;list_id=".$identifier."&amp;parent_id=".$r["ie_parent"],"View User Lock");
					}
				}
				$len = count($this->extra_commands);
				for($i=0;$i<$len;$i++){
					if($this->extra_commands[$i][2]==2){
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = array($this->extra_commands[$i][4],$this->extra_commands[$i][0]."&amp;group_id=".$r["md_link_group_id"],	$this->extra_commands[$i][3]);
					}
				}
			}
			if ($this->author_admin_access == 1){
				if($add_normal==1){
					$variables["PAGE_BUTTONS"][count($variables["PAGE_BUTTONS"])] = Array("ADD",$this->module_command."ADD&amp;list_id=".$identifier, ADD_NEW);
				} else {
					$variables["PAGE_BUTTONS"][count($variables["PAGE_BUTTONS"])] = $add_button;
				}
			}
			$this->page_size = $prev;
			$variables["as"]="table";
			return $this->generate_list($variables);
		}
	}
	
	/**************************************************************************************************************************
    * Save entry data and add new categories if defined
    *************************************************************************************************************************/
	function save_entries($parameters){
//		print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameters,true)."</p></li>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"".print_r($parameters,true).""));}
		$identifier 		= $this->check_parameters($parameters, "identifier", 	-1);
		$list_id			= $this->check_parameters($parameters, "list_id", 		-1);
		$ie_status			= $this->check_parameters($parameters, "ie_status", 	-1);
		$newCategories		= $this->check_parameters($parameters, "newCategories");
		/**
		* get metadata record info
		*/
		$sql ="select * from metadata_details where md_module = '$this->webContainer' and md_client=$this->client_identifier and md_link_id = $identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$md_fields = Array();
		$len = count($this->metadata_fields);
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			for($i=0; $i<$len;$i++){
				$md_fields[$this->metadata_fields[$i]["key"]] = $r[$this->metadata_fields[$i]["key"]];
			}
		}
		$this->parent->db_pointer->database_free_result($result);
		/**
        * cache data
        */
		$longDescription	= "";
		$fake_uri			= "";
		$info_vcontrol		= 0;
		$parameters["import"] = 0;
		$ok = $this->checkDuplicates($parameters);
		if ($ok==1){
			$copy_identifier = $this->copy_entry($parameters);
			//$pid = $identifier;
			if ($copy_identifier!=-1){
				$identifier = $copy_identifier;
			}
			/** Merge fields data to title */
			/*
			$result = $this->parent->db_pointer->database_query("select if_name from information_fields where if_client = $this->client_identifier and if_list = $list_id and if_add_to_title = 1");
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$parameters['ie_title'] = $parameters['ie_title'] ." ".trim($this->check_parameters($parameters, $r["if_name"], ""));
			}
			*/
			$sql = "select information_list.info_vcontrol, information_list.info_category, information_fields.*, menu_data.menu_url from information_fields
						inner join information_list on info_identifier = if_list and info_client = if_client and if_screen=0
						inner join menu_data on menu_identifier = info_menu_location and info_client = menu_client
					where if_client = $this->client_identifier and if_list = $list_id order by if_rank";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			$current_rank =1;
			$this->fields = Array();
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$fake_uri = $r["menu_url"];
//				$info_category = $r["info_category"];
				$info_vcontrol = $r["info_vcontrol"];
//				print "<li>".__FILE__."@".__LINE__."<p>".print_r($r,true)."</p></li>";
				$this->fields[$r["if_name"]]["map"] = $r["if_map"];
				$this->fields[$r["if_name"]][0] = $r["if_label"];
				$this->fields[$r["if_name"]][1] = $current_rank;
				$this->fields[$r["if_name"]][2] = $r["if_type"];
				$this->fields[$r["if_name"]]["special"] = $r["if_special"];
				

				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<li> type [".$r["if_type"]. "]</li>"));}
//				print "<li>".$r["if_type"]." = ".$r["if_map"]."</li>";
				if ($r["if_type"]=="datetime"){
					$this->fields[$r["if_name"]]["value"] = $this->check_date($parameters,$r["if_name"],"0000-00-00 00:00:00");
				}else if ($r["if_type"]=="date"){
					$this->fields[$r["if_name"]]["value"] = $this->check_date($parameters,$r["if_name"],"0000-00-00 00:00:00");
				}else if ($r["if_type"]=="time"){
					$this->fields[$r["if_name"]]["value"] = $this->check_date($parameters,$r["if_name"],"0000-00-00 00:00:00");
				}else if ($r["if_type"] == "list" || $r["if_type"]=="radio" || $r["if_type"]=="check" || $r["if_type"]=="select" ){
					$this->fields[$r["if_name"]]["value"] = $this->check_parameters($parameters, $r["if_name"]);
				}else if ($r["if_type"] == "double"  || $r["if_type"] == "integer" ){
					$this->fields[$r["if_name"]]["value"] = $this->check_parameters($parameters, $r["if_name"]);
				}else if ($r["if_type"] == "associations"){
					$file_associations	= $this->check_parameters($parameters, "file_associations_ie_files");
					if($file_associations!=""){
						$file_associations = split(",", $file_associations);
					} else {
						$file_associations = Array();
					}
					$this->fields[$r["if_name"]]["value"] = $file_associations;
				}else if ($r["if_type"]=="associated_entries"){
					$er_id 			= $this->check_parameters($parameters, "er_id", Array());
				    $er_rank		= $this->check_parameters($parameters, "er_rank", Array());
				    $er_src_id		= $this->check_parameters($parameters, "er_src_id", Array());
				    $er_src_cat		= $this->check_parameters($parameters, "er_src_cat", Array());
				    $er_dst_id		= $this->check_parameters($parameters, "er_dst_id", Array());
				    $er_dst_cat		= $this->check_parameters($parameters, "er_dst_cat", Array());
					if ($this->check_parameters($er_id,0,"__NOT_FOUND__")!="__NOT_FOUND__"){
						$er_id_list = join(",", $er_id);
						$sql = "delete FROM information_entry_relations where ier_identifier not in ($er_id_list) and ier_client = $this->client_identifier and (ier_source_id = $identifier or ier_dest_id = $identifier)";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
						$this->parent->db_pointer->database_query($sql);
						$sql = "select * from information_entry_relations where ier_identifier in ($er_id_list) and ier_client = $this->client_identifier ";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
						$result  = $this->parent->db_pointer->database_query($sql);
						$ignore_array_list = Array();
	                    while($r = $this->parent->db_pointer->database_fetch_array($result)){

	                    	$ignore_array_list[count($ignore_array_list)] = $r["ier_identifier"];
	                    }
	                    $this->parent->db_pointer->database_free_result($result);
	                    for ($i=0;$i < count($er_id); $i++){
							if(!in_array($er_id[$i], $ignore_array_list)){
								$ier_identifier = $this->getUid();
								$sql = "insert into information_entry_relations 
										(ier_identifier, ier_client, ier_source_id, ier_source_cat, ier_dest_id, ier_dest_cat, ier_rank)
										values
										($ier_identifier, $this->client_identifier, ".$er_src_id[$i].", ".$er_src_cat[$i].", ".$er_dst_id[$i].", ".$er_dst_cat[$i].", ".$er_rank[$i].")";
								if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
							} else {
								$sql = "update information_entry_relations set ier_rank = ".$er_rank[$i]." where 
								ier_identifier = ".$er_id[$i]." and
								ier_client = $this->client_identifier and 
								ier_source_id = ".$er_src_id[$i]." and 
								ier_source_cat = ".$er_src_cat[$i]." and 
								ier_dest_id = ".$er_dst_id[$i]." and 
								ier_dest_cat = ".$er_dst_cat[$i].";";
								if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
							}
							$this->parent->db_pointer->database_query($sql);
						}
					}
				}else if ($r["if_type"] == "imageembed"){
					$file_associations		= split(",", $this->check_parameters($parameters, "file_associations_ie_embedimage"));
					$file_associations_main	= split(",", $this->check_parameters($parameters, "file_associations_ie_embedimage_main"));
					$this->fields[$r["if_name"]]["value"] = $file_associations[0]."::".$file_associations_main[0];
				}else if ($r["if_type"] == "image"){
					$file_associations		= split(",", $this->check_parameters($parameters, "file_associations_".$r["if_name"]));
					$this->fields[$r["if_name"]]["value"] = $file_associations[0];
				}else {
					if ($r["if_type"] == "memo" || $r["if_type"] == "smallmemo"){
//						print "<li><strong>".$r["if_type"]."</strong></li>";
						if($this->fields[$r["if_name"]]["special"]==0){
							$this->fields[$r["if_name"]]["value"] = $this->moduletidy($this->txt2html($this->validate($this->check_parameters($parameters, $r["if_name"]))));
						} else {
							$this->fields[$r["if_name"]]["value"] = $this->check_editor($parameters, $r["if_name"]);
						}
//						print "<li><strong>".$this->fields[$r["if_name"]]["value"]."</strong></li>";
					} else if ($r["if_name"] == "ie_quantity"){
						$quantity_type						  = $this->check_parameters($parameters, "quantity_type",-1);
						if($quantity_type==-1){
							$this->fields[$r["if_name"]]["value"] = -1;
						} else {
							$this->fields[$r["if_name"]]["value"] = $this->validate($this->check_parameters($parameters, $r["if_name"]));
						}
					} else {
						$this->fields[$r["if_name"]]["value"] = $this->validate($this->moduletidy($this->check_parameters($parameters, $r["if_name"])));
					}
				}
				if($r["if_map"]!=""){
					if ($r["if_type"]=="list" || $r["if_type"]=="radio" || $r["if_type"]=="check" || $r["if_type"]=="select" || $r["if_type"]=="URL" || $r["if_type"]=="text"  || $r["if_type"]=="boolean" ){
						$md_fields[$r["if_map"]] = strip_tags($this->fields[$r["if_name"]]["value"]);
					} else {
						$md_fields[$r["if_map"]] = $this->fields[$r["if_name"]]["value"];
					}
				}
				if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY", array($this->module_name,"SQL",__LINE__,$r["if_name"]." - ".$this->fields[$r["if_name"]]["value"]."</p>"));}
				$current_rank++;
			}
			$this->parent->db_pointer->database_free_result($result);
//			print_r($md_fields);
//			print_r($this->fields);
//			$this->exitprogram();
			$cat_id_list 		= $this->check_parameters($parameters, "cat_id_list", -1);
			
			/** Check if this entry is featured list and has an RSS associated. If so then recache RSS. */						
			if ($list_id != ""){				
				$sql = "select rss_feed.rss_identifier from information_list
				inner join information_features on ifeature_list = info_identifier 
				inner join rss_feed on rss_ownerid = ifeature_identifier and rss_status = 1 
				where info_identifier = $list_id";
				$rss_result  = $this->parent->db_pointer->database_query($sql);
				if ($this->call_command("DB_NUM_ROWS",array($rss_result)) > 0){
					$rss_r = $this->parent->db_pointer->database_fetch_array($rss_result);
					$this->call_command("RSSADMIN_EXTERNAL_CACHE",Array("identifier"=>$rss_r["rss_identifier"]));
					//$this->call_command("RSS_CACHE", Array("identifier"=>$rss_r["rss_identifier"]));						

				}
				$this->parent->db_pointer->database_free_result($rss_result);
			}

			if ($identifier==-1){
				$ie_date_created = $this->libertasGetDate("Y/m/d H:i:s");
				if ($ie_status == 1){
					$ie_published		= 1;
					$ie_version_major	= 1;
					$ie_version_minor	= 0;
				} else {
					$ie_published		= 0;
					$ie_version_major	= 0;
					$ie_version_minor	= 1;
				}
				$ie_identifier = $this->getUid();
				$identifier = $ie_identifier;
				$sql = "insert into information_entry (ie_identifier, ie_parent, ie_client, ie_list, ie_date_created, ie_status, ie_user, ie_published, ie_version_major, ie_version_minor, ie_version_wip, ie_cached) values
									($ie_identifier, $ie_identifier, $this->client_identifier, '$list_id', '$ie_date_created', '$ie_status', '".$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0)."', $ie_published, $ie_version_major, $ie_version_minor, 1, 0)";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->parent->db_pointer->database_query($sql);
				$parent_identifier = $ie_identifier;
			} else {
				$version="";
				if ($ie_status==1){
					$version = ", ie_version_major=ie_version_major+1, ie_version_minor=0, ie_published=1";
				} else {
					$version = ", ie_published=0";
				}
				$sql = "update information_entry set ie_uri='', ie_status='$ie_status' $version where ie_client = $this->client_identifier and ie_identifier=$identifier and ie_list = $list_id";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->parent->db_pointer->database_query($sql);
				/*************************************************************************************************************************
                *  get parent identifier
                *************************************************************************************************************************/
				$parent_identifier = -1;
				$sql = "select ie_parent from information_entry where ie_client = $this->client_identifier and ie_identifier=$identifier and ie_list = $list_id";
				$result  = $this->parent->db_pointer->database_query($sql);
                while($r = $this->parent->db_pointer->database_fetch_array($result)){
                	$parent_identifier = $r["ie_parent"];
                }
                $this->parent->db_pointer->database_free_result($result);
			}
			/*************************************************************************************************************************
            * add metadata for this record
            *************************************************************************************************************************/
//			print_r($md_fields);
			if ($ie_status == 1){
				$this->call_command("METADATAADMIN_MODIFY", Array("md_link_group_id" => $parent_identifier, "md_date_publish"=>$this->libertasGetDate() , "identifier"=>$identifier, "module"=> $this->webContainer, "fields" => $md_fields, "command"=>"EDIT", "longDescription" => $longDescription));
			} else {
				$this->call_command("METADATAADMIN_MODIFY", Array("md_link_group_id" => $parent_identifier, "identifier"=>$identifier, "module"=> $this->webContainer, "fields" => $md_fields, "command"=>"EDIT", "longDescription" => $longDescription));
			}
			/*************************************************************************************************************************
            * 
            *************************************************************************************************************************/
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::entry",__LINE__,print_r(Array("new_categories"	=> $newCategories,"data_list"			=> $cat_id_list,"module"			=> $this->webContainer,"identifier"		=> $identifier,"information_list"	=> $list_id), true)));}
			$clist = $this->call_command("CATEGORYADMIN_TO_OBJECT_UPDATE",
				Array(
					"new_categories"	=> $newCategories,
					"data_list"			=> $cat_id_list,
					"module"			=> $this->webContainer,
					"identifier"		=> $identifier,
					"information_list"	=> $list_id
				)
			);
			$num_of_entries = count($cat_id_list);
//			print "[$newCategories]";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::newCategories",__LINE__, $newCategories));}
			if($newCategories!=""){
				if(strpos("\n",$newCategories)===false){
					$newCategoriesList =Array();
					$newCategoriesList[0] = $newCategories;
				} else {
					$newCategoriesList = split("\n",$newCategories);
				}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::newCategoriesList",__LINE__,print_r($newCategoriesList, true)));}
				$mz = count($newCategoriesList);
				for($i=0;$i<$num_of_entries;$i++){
					if (substr($cat_id_list[$i],0,3)=="new"){
						for($z=0;$z<$mz;$z++){
							$test = split("::",$newCategoriesList[$z]);
							if ($test[1]==$cat_id_list[$i]){
								$entry= split("::",$newCategoriesList[$z]);
							}
						}
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::entry",__LINE__,print_r($entry, true)));}
						$this->rename_or_move(
							Array(
								"cmd" 				=> "new",
								"info_identifier" 	=> $list_id,
								"cat_id" 			=> $clist[0][$i],
								"cat_parent" 		=> $entry[0],
								"cat_label" 		=> $entry[2],
								"list_id"			=> $clist[1]
							)
						);
					}
				}
			}
			$sql = "delete from information_entry_values where iev_client=$this->client_identifier and iev_entry=$identifier ";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->parent->db_pointer->database_query($sql);
//			print "<li>".__LINE__."</li>";
//			print_r($this->fields);
			foreach($this->fields as $key => $list){
				$iev_field	= $key;
//				if($this->check_parameters($list,2,"")=="memo"){
//					$list_value = $this->check_parameters($list,"value");
//				} else {
					$list_value = $this->check_parameters($list,"value");
//				}
				$mapto		= $this->check_parameters($list,"map");
				if($mapto==""){
					if (is_array($list_value)){
						foreach($list_value as $k => $v){
							if($v!=""){
								$iev_identifier = $this->getUid();
								$sql = "insert into information_entry_values (iev_identifier, iev_client, iev_entry, iev_field, iev_value, iev_list) values ($iev_identifier, '$this->client_identifier', '$identifier', '$iev_field', '$v', '$list_id');";
//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
								if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
								$this->parent->db_pointer->database_query($sql);
							}
						}
					} else {
						$iev_value = $list_value;
						$iev_identifier = $this->getUid();
						$sql = "insert into information_entry_values (iev_identifier, iev_client, iev_entry, iev_field, iev_value, iev_list) values ($iev_identifier, '$this->client_identifier', '$identifier', '$iev_field', '$iev_value', '$list_id');";
//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
						$this->parent->db_pointer->database_query($sql);
						if ($key=="ie_summary"){
							$longDescription = $iev_value." ".$longDescription;
						} else if ($key=="ie_title"){
							$longDescription = $iev_value." ".$longDescription;
							$md_title 		= $iev_value;
						} else if ($key=="ie_description"){
							$longDescription = $iev_value." ".$longDescription;
						} else {
							$longDescription = $longDescription." ".$iev_value;
						}
					}
				}
			}
			if($ie_status == 1){
				$sql = "select ie_parent from information_entry where ie_identifier = $identifier and ie_list = '$list_id' and ie_client = $this->client_identifier";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
                while($r = $this->parent->db_pointer->database_fetch_array($result)){
                	$parent = $r["ie_parent"];
                }
                $this->parent->db_pointer->database_free_result($result);
				$sql = "update information_entry set ie_published=0 where ie_list = '$list_id' and ie_published = '1' and ie_parent=$parent and ie_identifier != $identifier";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->parent->db_pointer->database_query($sql);
			}
			if ($info_vcontrol!=0){
				$sql = "select * from information_entry where ie_identifier = $identifier and ie_client=$this->client_identifier";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
	            while($r = $this->parent->db_pointer->database_fetch_array($result)){
	            	$parent = $r["ie_parent"];
	            }
	            $this->parent->db_pointer->database_free_result($result);
				$sql = "select * from information_entry where ie_parent = $parent and ie_identifier != $identifier and ie_client = $this->client_identifier order by ie_version_major desc, ie_version_minor desc";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
				$list='';
				$kept_counter=0;
	            while($r = $this->parent->db_pointer->database_fetch_array($result)){
					if ($info_vcontrol==11){
						if ($list!=''){
							$list.=", ";
						}
		            	$list .= $r["ie_identifier"];
					} else {
						if ($kept_counter<$info_vcontrol){
							if ($r["ie_version_minor"]!=0){
								if ($list!=''){
									$list.=", ";
								}
				            	$list .= $r["ie_identifier"];
							} else {
								$kept_counter++;
							}
						} else {
							if ($list!=''){
								$list.=", ";
							}
			            	$list .= $r["ie_identifier"];
						}
					}
	            }
	            $this->parent->db_pointer->database_free_result($result);
				$sql = "delete from information_entry_values where iev_client=$this->client_identifier and iev_entry in ($list);";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->parent->db_pointer->database_query($sql);
				$sql = "delete from information_entry where ie_client=$this->client_identifier and ie_identifier in ($list);";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->parent->db_pointer->database_query($sql);
			}
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::Status",__LINE__,"$ie_status"));}
			if ($ie_status==1){
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY", array($this->module_name,__FUNCTION__."::cache with paramters",__LINE__, print_r(Array("identifier" => $identifier, "list" => $list_id , "url"=>$fake_uri),true)));}
				$this->cache_entry(Array("identifier" => $identifier, "list" => $list_id , "url"=>$fake_uri));
				$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_INFODIR__"], "identifier" => $identifier, "url"=> $fake_uri, "webContainer" => $this->webContainer));
			}
			$this->widget_atoz(Array("identifier"=>$list_id));
//			print "<li>".__LINE__."</li>";
//			$this->exitprogram();
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST_ENTRIES&amp;identifier=$list_id&recache=1"));
		} else {
			$parameters["errorarray"] = $ok;
			return $this->information_entry_modify($parameters);
		}
		
	}

	/*************************************************************************************************************************
	* administation function for adding a new entry to the Information directory
	*************************************************************************************************************************/
	function information_entry_modify($parameters){
		if ($this->author_admin_access == 0 && $this->approve_admin_access==0){
			return "";
		}
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"".print_r($parameters,true).""));}
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$list_id	 		= $this->check_parameters($parameters,"list_id",-1);
		$command 			= $this->check_parameters($parameters,"command","");
		$prevcommand		= $this->check_parameters($parameters,"prevcommand",$command);
		$form_label 		= LOCALE_ADD;
		$cat_list			= "";
		$status				= 0;
		$current_rank 		= 1;
		$info_category		= -1;
		$ie_parent			= 0;
		$len = count($this->metadata_fields);
		
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"$identifier"));}
		
		if ($identifier!=-1){
			$form_label 	= LOCALE_EDIT;
			/**
            * get metadata record
            */
			$sql ="select * from metadata_details where md_module = '$this->webContainer' and md_client=$this->client_identifier and md_link_id = $identifier";
            $result  = $this->parent->db_pointer->database_query($sql);
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				for($i=0; $i<$len;$i++){
					$this->metadata_fields[$i]["value"] = $r[$this->metadata_fields[$i]["key"]];
				}
            }
            $this->parent->db_pointer->database_free_result($result);
			/**
            * get content
            */
			$sql="select * from information_fields 
					left outer join information_entry_values on (iev_entry =$identifier and iev_field = if_name and if_list= iev_list and if_client=iev_client) 
					left outer join information_entry on (iev_entry = ie_identifier and ie_client = iev_client) 
					inner join information_list on (info_identifier = if_list)
				  where 
					if_client=$this->client_identifier and 
					if_screen=0 and 
					if_list = $list_id
					order by if_rank";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			$c=0;
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				if($c==0){
					if("null" != $this->check_parameters($r,"ie_status","null")){
						$status = $r["ie_status"];
						$c++;
					}
					$info_category = $r["info_category"];
					if ($ie_parent==0){
						$ie_parent=$r["ie_parent"];
					}
				}
				// define field setup
				$map = $r["if_map"];
				if( $this->check_parameters($this->fields,$r["if_name"],"__NOT_FOUND__")=="__NOT_FOUND__"){
//					print "<li>".$r["if_name"]." = ".$r["if_label"]." = ".$r["iev_value"]."</li>";
					$this->fields[$r["if_name"]] = array();
					$this->fields[$r["if_name"]][0] = $r["if_label"];
					$this->fields[$r["if_name"]][1] = $current_rank;
					$this->fields[$r["if_name"]][3] = 1;
					$this->fields[$r["if_name"]][4] = $r["if_type"];
					$this->fields[$r["if_name"]]["special"] = $r["if_special"];
					$this->fields[$r["if_name"]]["error"] =0;
					$this->fields[$r["if_name"]]["value"] = "";
					$this->fields[$r["if_name"]]["associated_file_ids"]="";
					$this->fields[$r["if_name"]]["set"] = 0;
					$this->fields[$r["if_name"]]["addtotitle"] = $r['if_add_to_title'];
					if($r["if_name"]=="ie_embedimage"){
						$this->fields[$r["if_name"]]["specified"] = Array("thumb"=>-1,"main"=>-1);
					} else {
						$this->fields[$r["if_name"]]["specified"] = Array();
					}
				}
				if ($map != ""){
					for($i=0; $i<$len;$i++){
						if($this->metadata_fields[$i]["key"] == $map){
							$txt = htmlentities($this->html_2_txt(html_entity_decode(trim($this->metadata_fields[$i]["value"]))));
						}
					}
//					print "<li>$map -> $txt</li>";
				} else {
					$txt = htmlentities($this->html_2_txt(html_entity_decode(trim($r["iev_value"]))));
				} 
				if($r["if_name"]=="ie_embedimage"){
					if(strpos($r["iev_value"],"::")===false){
					} else {
						$l = split("::",$r["iev_value"]);
						$this->fields[$r["if_name"]]["specified"] = Array(
							"thumb"=>$l[0],
							"main"=>$l[1]
						);
					}
//				} else if($r["if_type"]=="links"){
//					$this->fields[$r["if_name"]]["specified"][0]= "";
				} else if($r["if_name"]=="ie_image"){
					$this->fields[$r["if_name"]]["specified"] = $txt;
				} else {
					$this->fields[$r["if_name"]]["specified"][count($this->fields[$r["if_name"]]["specified"])]= $txt;
				}
			}
			$this->parent->db_pointer->database_free_result($result);
			if($ie_parent==null){
				// if all fields are in metadata then extract the information_entry record seperatly as it will not have been linked to the field table
				$sql="select * from information_entry where ie_identifier = $identifier and ie_client=$this->client_identifier and ie_list = $list_id";
				$result  = $this->parent->db_pointer->database_query($sql);
                while($r = $this->parent->db_pointer->database_fetch_array($result)){
                	$ie_parent = $r["ie_parent"];
					$status = $r["ie_status"];
                }
                $this->parent->db_pointer->database_free_result($result);
			}
			foreach($this->fields as $key => $list){
				if (($list[4] == "radio") || ($list[4] == "select") || ($list[4] == "list") || ($list[4] == "check")){
					$sql = "select * from information_options where io_client=$this->client_identifier and io_field='".$key."' and io_list= ".$list_id." order by io_rank";
					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$this->fields[$key]["value"] .= "<option";
						foreach($this->fields[$key]["specified"] as $k => $v){
							if ($v == urldecode($option_r["io_value"])){ // && $this->fields[$key]["set"]==0
								$this->fields[$key]["value"] .= " selected='true'";
							}
						}
						$this->fields[$key]["value"] .= "><![CDATA[".urldecode($option_r["io_value"])."]]></option>";
					}
				} else if (($list[4] == "boolean")){
					$sql = "select * from information_options where io_client=$this->client_identifier and io_field='".$key."' and io_list= ".$list_id." order by io_rank";
					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					$v = $this->fields[$key]["specified"][0];
					
					$i=0;
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$this->fields[$key]["value"] .= "<option value='".(1-$i)."' ";
						if ($v == (1-$i)){ // if value is rank 1 or 2
							$this->fields[$key]["value"] .= " selected='true'";
						}
						$i++;
						$this->fields[$key]["value"] .= "><![CDATA[".urldecode($option_r["io_value"])."]]></option>";
					}
				} else if (($list[4] == "associations")){
					$myfiles = $this->call_command("FILES_LIST_ITEMS", Array("list" => $list["specified"],"type"=>"associate"));
				 	$this->fields[$key]["value"] .= $myfiles[0];
				 	$this->fields[$key]["associated_file_ids"] .= $myfiles[1];
				} else if ($list[4] == "associated_entries"){
					$sql = "
							SELECT * FROM information_entry_relations
								inner join information_entry on ((ie_identifier = ier_source_id and ier_source_id !=$identifier) or (ie_identifier = ier_dest_id  and ier_dest_id  !=$identifier)) and ie_client = ier_client
								inner join information_entry_values on iev_list = ie_list and iev_field = 'ie_title' and iev_entry = ie_identifier
							 where (ier_source_id  = $identifier or ier_dest_id  = $identifier) and ier_client = $this->client_identifier and ie_list = $list_id";
					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$this->fields[$key]["value"] .= "
						<entry id='".$option_r["ier_identifier"]."' src_id='".$option_r["ier_source_id"]."' src_cat='".$option_r["ier_source_cat"]."' dst_id='".$option_r["ier_dest_id"]."' dst_cat='".$option_r["ier_dest_cat"]."'><title><![CDATA[".$option_r["iev_value"]."]]></title></entry>";
					}
				} else if (($list[4] == "imageembed")){
					$myfiles = $this->call_command("FILES_LIST_ITEMS", Array("list" => Array($list["specified"]["thumb"]),"type"=>"associate"));
					$this->fields[$key]["value"]					= Array();
					$this->fields[$key]["associated_file_ids"]		= Array();
				 	$this->fields[$key]["value"][0] 				= $myfiles[0];
				 	$this->fields[$key]["associated_file_ids"][0]	= $myfiles[1];
					$myfiles = $this->call_command("FILES_LIST_ITEMS", Array("list" => Array($list["specified"]["main"]),"type"=>"associate"));
				 	$this->fields[$key]["value"][1] 				= $myfiles[0];
				 	$this->fields[$key]["associated_file_ids"][1] 	= $myfiles[1];
				} else if (($list[4] == "image")){
					$myfiles = $this->call_command("FILES_LIST_ITEMS", Array("list" => $list["specified"],"type"=>"associate"));
					$this->fields[$key]["value"]					= Array();
					$this->fields[$key]["associated_file_ids"]		= Array();
				 	$this->fields[$key]["value"] 					= $myfiles[0];
				 	$this->fields[$key]["associated_file_ids"]		= $myfiles[1];
				} else if ($list[4]=="links"){
					$sql = "select * from information_entry_links where ievl_client=$this->client_identifier and ievl_field='".$key."' and ievl_list= ".$r["if_list"]." and ievl_entry = $identifier order by ievl_rank";
					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					$index=0;
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$this->fields[$r["if_name"]]["value"] .= "<link id='$index'>
							<label><![CDATA[".$option_r["ievl_label"]."]]></label>
							<uri><![CDATA[".$option_r["ievl_uri"]."]]></uri>
						</link>";
						$index++;
					}
				}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name, "list", __LINE__, "".$list[4]." ".print_r($list["specified"],true)." ".$this->fields[$key]["value"].""));}
			}
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"this->fields",__LINE__,"".print_r($this->fields, true).""));}
			$cat_list = $this->call_command("CATEGORYADMIN_TO_OBJECT_LIST", 
				Array(
					"module"		=>	$this->webContainer,
					"identifier"	=>	$identifier,
					"returntype"	=>	1
				)
			);
//			print_r($this->fields);
		} else {
			$identifier = -1;
			$sql = "select * from information_fields 
						inner join information_list on (info_identifier = if_list and info_client = if_client)
					where if_client = $this->client_identifier and if_list = $list_id 
					and if_screen=0
					order by if_rank";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$info_category = $r["info_category"];
				$this->fields[$r["if_name"]][0] = $r["if_label"];
				$this->fields[$r["if_name"]][1] = $current_rank;
				$this->fields[$r["if_name"]][3] = 1;
				$this->fields[$r["if_name"]][4] = $r["if_type"];
				$this->fields[$r["if_name"]]["special"] = $r["if_special"];
				$this->fields[$r["if_name"]]["value"] = "";
				$this->fields[$r["if_name"]]["error"] =0;
				$this->fields[$r["if_name"]]["specified"] = Array();
				$this->fields[$r["if_name"]]["addtotitle"] = $r['if_add_to_title'];
				if (($r["if_type"] == "radio") || ($r["if_type"] == "select") || ($r["if_type"] == "list") || ($r["if_type"] == "check")){
					$sql = "select * from information_options where io_client=$this->client_identifier and io_field='".$r["if_name"]."' and io_list= ".$r["if_list"]." order by io_rank";
					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$this->fields[$r["if_name"]]["value"] .= "<option><![CDATA[".urldecode($option_r["io_value"])."]]></option>";
					}
				}
				if ($r["if_type"] == "boolean"){
					$sql = "select * from information_options where io_client=$this->client_identifier and io_field='".$r["if_name"]."' and io_list= ".$r["if_list"]." order by io_rank";
					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					$o = Array();
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$o[count($o)] = urldecode($option_r["io_value"]);
					}
					$this->fields[$r["if_name"]]["value"] .= "<option value='1'><![CDATA[".$o[0]."]]></option>";
					$this->fields[$r["if_name"]]["value"] .= "<option value='0'><![CDATA[".$o[1]."]]></option>";
				}
				if ($r["if_type"]=="links"){
//					$sql = "select * from information_entry_links where ievl_client=$this->client_identifier and ievl_field='".$r["if_name"]."' and ievl_list= ".$r["if_list"]." and ievl_entry = ".$r["if_list"]." order by io_rank";
//					$option_result = $this->parent->db_pointer->database_query($sql);
//					$current_rank = 1;
//					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
//					for($i=1; $i<=10;$i++){
//						$this->fields[$r["if_name"]]["value"] .= "<link id='".$i."'>
//							<label><![CDATA[]]></label>
//							<uri><![CDATA[]]></uri>
//						</link>";
//					}
				}
				if ($r["if_type"] == "associated_entries"){
					$sql = "
						SELECT * FROM information_entry_relations
							inner join information_entry on ((ie_identifier = ier_source_id and ier_source_id !=$identifier) or (ie_identifier = ier_dest_id  and ier_dest_id  !=$identifier)) and ie_client = ier_client
							inner join information_entry_values on iev_list = ie_list and iev_field = 'ie_title' and iev_entry = ie_identifier
						where (ier_source_id  = $identifier or ier_dest_id  = $identifier) and ier_client = $this->client_identifier and ie_list = $list_id";
					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$this->fields[$r["if_name"]]["value"] .= "
						<entry id='" . $option_r["ier_identifier"] . "' src_id='".$option_r["ier_source_cat"]."' src_cat='".$option_r["ier_source_cat"]."' dst_id='".$option_r["ier_dest_cat"]."' dst_cat='".$option_r["ier_dest_cat"]."'><title><![CDATA[".$option_r["iev_value"]."]]></title></entry>";
					}
				}
				$current_rank++;
			}
		}
		if ($prevcommand!=$command){
			$errorarray = $this->check_parameters($parameters, "errorarray");
			foreach($this->fields as $key => $list){
				$this->fields[$key]["specified"] = Array(0 => $this->check_parameters($parameters,"$key",$this->fields[$key]["value"]));
				if (in_array($key,$errorarray)){
					$this->fields[$key]["error"] =1;
				}
			}
		}
		$cat_parent = $list_id;
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"print",__LINE__,"<p>list identifier $list_id category $info_category</p>"));}
		
		$category_listing = $this->call_command("CATEGORYADMIN_LOAD",Array("identifier"=>$info_category));
//		print_r($category_listing);
//		$this->exitprogram();
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Directory - $form_label]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST_ENTRIES&amp;identifier=$list_id",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."SAVE_ENTRY\" />";
		$out .="		<input type=\"hidden\" name=\"prevcommand\" value=\"$command\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<input type=\"hidden\" name=\"list_id\" value=\"$list_id\" />";
		$out .="		<input type=\"hidden\" name=\"parent_id\" value=\"$ie_parent\" />";
		$out .="		<page_sections>";
		$out .="		<section label='Entry Details' name='entryinfo'>";
		if (($this->author_admin_access == 1 && $this->approve_admin_access == 1) || ($this->approve_admin_access == 1)){
			$out .="			<select label=\"Status of this Entry\" name=\"ie_status\">
									<option value=\"0\"";
			if ($status==0){
				$out .= " selected='true'";
			}
			$out .= ">Not Approved</option>
				<option value=\"1\"";
			if ($status==1){
				$out .= " selected='true'";
			}
			$out .= ">Approved</option>
			</select>";
		} else {
			$out .="			<input type=\"hidden\" name=\"ie_status\" value='0'/>";
		}
		/*************************************************************************************************************************
		*  List of available fields indexes
		*************************************************************************************************************************
		* Set Defaults for fields.
		*  0 = Field label, 
		*  1 = Rank, 
		*  2 = Description, 
		*  3 = Selected, 
		*  4 = Type
		*
		*************************************************************************************************************************/
		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		
		$out .="			<seperator_row><seperator>\n";
		foreach($this->fields as $key => $list){ 
			if($list["error"]==1){
				$error=" error='1' ";
			} else {
				$error="";
			}
			if ($list[3]==1){
//			print "<li>$key -> $list[4]</li>";
					if($key=="ie_quantity"){
						$out .="			<input $error type=\"quantity\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\"><![CDATA[".$this->check_parameters($list["specified"],0)."]]></input>\n";
					} else if($list[4]=="links"){
						$out .= "<entrylinks number=''>".$list["value"]."</entrylinks>";
					} else if($list[4]=="text" || $list[4]=="URL" || $list[4]=="email" || $list[4]=="double" || $list[4]=="integer"){
						if($key=="ie_title"){
							$out .="			<input $error type=\"text\" name=\"".$key."\" label=\"".$list[0]."\" required=\"YES\" size=\"255\"><![CDATA[".$this->check_parameters($list["specified"],0)."]]></input>\n";
						}else{
							$out .="			<input $error type=\"text\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\" addtotitle=\"".$list["addtotitle"]."\"><![CDATA[".$this->check_parameters($list["specified"],0)."]]></input>\n";
						}
					} else if($list[4]=="smallmemo"){
						if($list["special"]==1){
							$out .="<textarea $error name=\"".$key."\" label=\"".$list[0]."\" size=\"10\" height=\"12\"";
							$out .=" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'";
							$out .="><![CDATA[".$this->check_parameters($list["specified"],0)."]]></textarea>\n";
						} else {
							$out .="<textarea $error name=\"".$key."\" label=\"".$list[0]."\" size=\"10\" height=\"6\"><![CDATA[".$this->check_parameters($list["specified"],0)."]]></textarea>\n";
						}
					} else if($list[4]=="memo"){
						if($list["special"]==1){
							$out .="			<textarea $error name=\"".$key."\" label=\"".$list[0]."\" size=\"10\" height=\"12\"";
							$out .=" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'";
							$out .="><![CDATA[".$this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$this->check_parameters($list["specified"],0)))."]]></textarea>\n";
						} else {
							$out .="			<textarea $error name=\"".$key."\" label=\"".$list[0]."\" size=\"10\" height=\"12\"><![CDATA[".$this->check_parameters($list["specified"],0)."]]></textarea>\n";
						}
					} else if($list[4]=="boolean" || $list[4]=="radio"){
						$out .="			<radio $error type='vertical' name=\"".$key."\" label=\"".$list[0]."\">".$list["value"]."</radio>\n";
//						print_r($list["value"]);
					} else if($list[4]=="select"){
						$out .="			<select $error name=\"".$key."\" label=\"".$list[0]."\" addtotitle=\"".$list["addtotitle"]."\">".$list["value"]."</select>\n";
					} else if($list[4]=="check"){
						$out .="			<checkboxes $error type='vertical' name=\"".$key."\" label=\"".$list[0]."\">".$list["value"]."</checkboxes>\n";
					} else if($list[4]=="list"){
						$out .="			<select $error multiple='1' size='10' name=\"".$key."\" label=\"".$list[0]."\" addtotitle=\"".$list["addtotitle"]."\">".$list["value"]."</select>\n";
					} else if($list[4]=="associated_entries"){
						$out .= "			<entry_associate type='associated_entries' name='$key' visible='yes'><label><![CDATA[".$list[0]."]]></label>".$list["value"]."</entry_associate>\n";
					} else if($list[4]=="associations"){
						$assoc_ids = $this->check_parameters($list,"associated_file_ids");
						$out .= "			<input type=\"hidden\" name=\"file_associations_$key\"><![CDATA[".$assoc_ids."]]></input>";
						$out .= "			<file_associate type='associations' name='$key' visible='yes'><label><![CDATA[".$list[0]."]]></label>".$list["value"]."</file_associate>\n";
					} else if($list[4]=="imageembed"){
					$image = $this->check_parameters($list["associated_file_ids"],0);
						$out .= "			<input type=\"hidden\" name=\"file_associations_".$key."\"><![CDATA[". $image."]]></input>";
						$out .= "			<file_associate type='imageembed' name='$key' visible='yes'><label><![CDATA[Thumbnail]]></label>".$this->check_parameters($list["value"],0)."</file_associate>\n";
						$out .= "			<input type=\"hidden\" name=\"file_associations_".$key."_main\"><![CDATA[". $this->check_parameters($list["associated_file_ids"],1)."]]></input>";
						$out .= "			<file_associate type='imageembed' name='".$key."_main' visible='yes'><label><![CDATA[Main Image]]></label>".$this->check_parameters($list["value"],1)."</file_associate>\n";
					} else if($list[4]=="image"){
						$image = $this->check_parameters($list,"associated_file_ids");
						$out .= "			<input type=\"hidden\" name=\"file_associations_".$key."\"><![CDATA[". $image."]]></input>";
						$out .= "			<file_associate type='imageembed' name='$key' visible='yes'><label><![CDATA[".$list[0]."]]></label>".$list["value"]."</file_associate>\n";
					} else if($list[4]=="colsplitter"){
						$out .="			</seperator><seperator>\n";
					} else if($list[4]=="rowsplitter"){
						$out .="			</seperator></seperator_row><seperator_row><seperator>\n";
					} else if ($list[4]=="datetime"){
						$year_start = 1900;//$this->check_prefs(Array("sp_combo_year"));
						$year_finish = Date("Y")+5;
						$out.= "            <input $error type=\"date_time\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\" value=\"".$this->check_parameters($list["specified"],0,$this->libertasGetDate("Y-m-d H:i:s"))."\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
//						print "            <input $error type=\"date_time\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\" value=\"".$this->check_parameters($list["specified"],0,$this->libertasGetDate("Y-m-d H:i:s"))."\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
					} else if ($list[4]=="date"){
						$year_start = 1900;//$this->check_prefs(Array("sp_combo_year"));
	//					$year_start = $this->check_prefs(Array("sp_combo_year"));
						$year_finish = Date("Y")+5;
//						print_r($list);
						$out.= "            <input $error type=\"date\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\" value=\"".date("Y-m-d H:i:s", strtotime($this->check_parameters($list["specified"],0,$this->libertasGetDate("Y-m-d H:i:s"))))."\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
//						print "            <input $error type=\"date\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\" value=\"".date("Y-m-d H:i:s", strtotime($this->check_parameters($list["specified"],0,$this->libertasGetDate("Y-m-d H:i:s"))))."\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
					} else if ($list[4]=="time"){
						$year_start = $this->check_prefs(Array("sp_combo_year"));
						$year_finish = Date("Y")+5;
						$out.= "            <input $error type=\"time\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\" value=\"".$this->check_parameters($list["specified"],0,$this->libertasGetDate("Y-m-d H:i:s"))."\" year_start=\"$year_start\" year_end=\"$year_finish\"/>";
					} else {
						$out .="			<input $error type=\"".$list[4]."\" name=\"".$key."\" label=\"".$list[0]."\" size=\"255\"><![CDATA[".$this->check_parameters($list["specified"],0)."]]></input>\n";
					}
				}
			}
		$out .="			</seperator></seperator_row>";
		$out .="		</section>";
		/*************************************************************************************************************************
		* Display categories 
		*************************************************************************************************************************/
		$out .="		<section label='Choose Categories'>";
		$out .= 			"<choose_categories can_add='1' parent='$cat_parent' identifier='$info_category' name='cat_parent'>
								<add><![CDATA[Add new Category]]></add>
								<label><![CDATA[Select Categories that this belongs to.]]></label>
								$category_listing
							</choose_categories>";
		$out .="			$cat_list";
		$out .="		</section>";
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}


	function cache_entry($parameters){
//		print_r($parameters);
//		die();
		$old_ie_identifier 		= $this->check_parameters($parameters, "old_ie_identifier",$this->check_parameters($parameters, "old_ie_identifier",		-1));
		
		$identifier 			= $this->check_parameters($parameters,"identifier",0);
		$list 					= $this->check_parameters($parameters,"list",-1);
		$fake_uri 				= $this->check_parameters($parameters,"url","");
		$build_directory_files	= $this->check_parameters($parameters,"build_directory_files",1);
		$file_name				= "";
		$parent_identifier		= -1;
		$entry_user				= -1;
		$info_summary_only 		= 0;
		/**
		* get metadata record
		*/
		$sql ="select * from metadata_details 
				inner join information_entry on md_link_id = ie_identifier and ie_client=md_client
			where md_module = '$this->webContainer' and md_client=$this->client_identifier and md_link_id = $identifier";

		$result  = $this->parent->db_pointer->database_query($sql);
		$len = count($this->metadata_fields);
		$md_title_index = -1;
		$md_price_index = -1;
		$real_id = $identifier;
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$parent_identifier	= $r["ie_parent"];
			$real_id			= $r["ie_identifier"];
			$entry_user 		= $r["ie_user"];
			for($i=0; $i<$len;$i++){
				$this->metadata_fields[$i]["value"] = $r[$this->metadata_fields[$i]["key"]];
				if($this->metadata_fields[$i]["key"]=="md_title"){
					$md_title_index=$i;
				}
				if($this->metadata_fields[$i]["key"]=="md_price"){
					$md_price_index=$i;
				}
			}
		}
		$this->parent->db_pointer->database_free_result($result);
//		print_r($this->metadata_fields);
//		$this->exitprogram();

		$this->fields = Array();

		/**
        * cache data
        */
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"parameters",__LINE__,"".print_r($parameters,true).""));}
		// RETRIEVE  RECORDS
		$category_list	= -1;
		$sql = "select * from information_fields where if_list = $list and if_screen=0 and if_client=$this->client_identifier order by if_rank ";

		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result  = $this->parent->db_pointer->database_query($sql);
		$current_rank = 0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$this->fields[$r["if_name"]] = array();
			$this->fields[$r["if_name"]][0] = $r["if_label"];
			$this->fields[$r["if_name"]][1] = $current_rank;
			$this->fields[$r["if_name"]][3] = 1;
			$this->fields[$r["if_name"]][4] = $r["if_type"];
			$this->fields[$r["if_name"]][5] = $r["if_link"];
			$this->fields[$r["if_name"]]["value"] = "";
			$this->fields[$r["if_name"]]["specified"] = Array();
			$this->fields[$r["if_name"]]["filter"] = $r["if_filterable"];
			$current_rank ++;
        }
        $this->parent->db_pointer->database_free_result($result);
		$screen=0;
			$sql = "
					select 
						if_name, if_label, if_filterable,
						if_type, if_list, if_map,if_conlabel,
						if_link, info_category,
						iev_value, info_summary_only
					from information_fields
						left outer join information_entry_values on (iev_field = if_name and if_list = iev_list and if_client = iev_client and iev_entry=$identifier) 
						inner join information_list on (info_identifier = if_list and info_client = if_client and info_owner = '$this->webContainer') 
					where if_client=$this->client_identifier 
						and if_screen=0 and if_list = $list
					order by if_rank 
			";
			//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";

			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			$current_rank = 0;
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$info_summary_only = $r["info_summary_only"];
				$category_list	= $r["info_category"];
//				print "<li>print $category_list</li>";
//				if(($screen==0 && ($r["if_name"]=='ie_title'  || $r["if_name"]=='ie_summary')) || ($screen!=0)){
				$map = $r["if_map"];
					if ($this->check_parameters($this->fields,$r["if_name"],"__NOT_FOUND__")=="__NOT_FOUND__"){
						$this->fields[$r["if_name"]] = array();
						$this->fields[$r["if_name"]]["specified"] = Array();
					}
					$this->fields[$r["if_name"]][0] = $r["if_label"];
					$this->fields[$r["if_name"]][1] = $current_rank;
					$this->fields[$r["if_name"]][3] = 1;
					$this->fields[$r["if_name"]][4] = $r["if_type"];
					$this->fields[$r["if_name"]][5] = $r["if_link"];
					$this->fields[$r["if_name"]][6] = $r["if_conlabel"];					
					$this->fields[$r["if_name"]]["value"] = "";
					$this->fields[$r["if_name"]]["filter"] = $r["if_filterable"];
					$this->fields[$r["if_name"]][9] = $r["if_url_linkfield"];
					$current_rank++;
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name, "label", __LINE__, "".$r["if_label"].""));}
				//}
//				print "<li>$current_rank::map[$map]</li>";
				if ($map != ""){
					for($i=0; $i<$len;$i++){
						if($this->metadata_fields[$i]["key"] == $map){
							$txt = $this->metadata_fields[$i]["value"];
						}
					}
				} else {
					$txt = $this->check_parameters($r,"iev_value");
				}
				if($txt == "<p></p>" || $txt == "<p><br></p>"){
					$txt = "";
				}
				if($r["if_name"] == "ie_embedimage"){
					if(strpos($txt,"::") === false){
					} else {
						$l = split("::",$txt);
						$this->fields[$r["if_name"]]["specified"] = Array(
							"thumb"	=> $l[0],
							"main"	=> $l[1]
						);
					}
				} else {
					$this->fields[$r["if_name"]]["specified"][count($this->fields[$r["if_name"]]["specified"])] = $txt;
				}
			}
			$this->parent->db_pointer->database_free_result($result);
			// BUILD OPTION LIST 
			foreach($this->fields as $key => $list_entries){
				if ($list_entries[4] == "datetime"){
                   	$this->fields[$key]["value"] = date("r",strtotime($list_entries["specified"][0]));
				}
				if ($list_entries[4] == "date"){
                   	$this->fields[$key]["value"] = date("D, d M Y",strtotime($list_entries["specified"][0]));
				}
				if ($list_entries[4] == "time"){
                   	$this->fields[$key]["value"] = date("H:i:s",strtotime($list_entries["specified"][0]));
				}
				if ($list_entries[4] == "email"){
                   	$this->fields[$key]["value"] = strip_tags($list_entries["specified"][0]);
				}
				/**
                * embed a single image (main)
                */
				if ($list_entries[4] == "image"){
					$file  = $this->check_parameters($list_entries["specified"],"0");
					$myfiles = $this->call_command("FILES_LIST_ITEMS", Array("list" => Array($file)));
				 	$this->fields[$key]["value"]	 				= $myfiles;
				}
				/**
                * embedded images (thumb nail / main image pairing)
                */
				if ($list_entries[4] == "imageembed"){
					$thumb  = $this->check_parameters($list_entries["specified"],"thumb");
					$main  = $this->check_parameters($list_entries["specified"],"main");
					$myfiles = $this->call_command("FILES_LIST_ITEMS", Array("list" => Array($thumb)));
					//print_r($myfiles);
					$this->fields[$key]["value"]					= "";
				 	$this->fields[$key]["value"]	 				.= "<thumb>".$myfiles."</thumb>";
					$myfiles = $this->call_command("FILES_LIST_ITEMS", Array("list" => Array($main)));
					//print_r($myfiles);
				 	$this->fields[$key]["value"] 					.= "<main>".$myfiles."</main>";
				}
				/*
				
				*/
				if ($list_entries[4] == "associated_entries"){
					$sql = "
							SELECT * FROM information_entry_relations
							inner join information_entry on ((ie_identifier = ier_source_id and ier_source_id !=$identifier) or (ie_identifier = ier_dest_id  and ier_dest_id  !=$identifier)) and ie_client = ier_client
							inner join information_entry_values on iev_list = ie_list and iev_field = 'ie_title' and iev_entry = ie_identifier
							 where (ier_source_id  = $identifier or ier_dest_id  = $identifier) and ier_client = $this->client_identifier and ie_list = $list";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}

					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$this->fields[$key]["value"] .= "
						<entry id='".$option_r["ier_identifier"]."' src_id='".$option_r["ier_source_id"]."' src_cat='".$option_r["ier_source_cat"]."' dst_id='".$option_r["ier_dest_id"]."' dst_cat='".$option_r["ier_dest_cat"]."'><title><![CDATA[".$option_r["iev_value"]."]]></title></entry>";
					}
					$this->call_command("DB_FREE", array($option_result));
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"",__LINE__,"<p>$key -> ".$this->fields[$key]["value"]."</p>"));}
				}
				/*************************************************************************************************************************
				*	URL
				*************************************************************************************************************************/
				if ($list_entries[4] == "URL"){
					$sql = "select * from information_entry_links where ievl_client=$this->client_identifier and ievl_list = $list and ievl_field='$key' and ievl_screen=$screen";
//					print $sql;
					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$v = $option_r["ievl_mapped"];
						if (!is_array($this->fields[$key]["value"])){
							$this->fields[$key]["value"]= Array();
						}
						$this->fields[$key]["value"][$screen] = $v;
					}
					$this->call_command("DB_FREE", array($option_result));
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"",__LINE__,"<p>$key -> ".$this->fields[$key]["value"]."</p>"));}
				}
				/*************************************************************************************************************************
				* URL
				*************************************************************************************************************************/
				if ($list_entries[4] == "associations"){
                   	$this->fields[$key]["value"] .= $this->call_command("FILES_LIST_ITEMS", Array("list" => $list_entries["specified"]));
					if ($this->fields[$key]["value"]=="Array"){
						$this->fields[$key]["value"]="";
					}
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"",__LINE__,"<p>$key -> ".$this->fields[$key]["value"]."</p>"));}
				}
				if (($list_entries[4] == "radio") || ($list_entries[4] == "select") || ($list_entries[4] == "list") || ($list_entries[4] == "check")){
					foreach($list_entries["specified"] as $k => $v){
						$this->fields[$key]["value"] .= "<option><![CDATA[".strip_tags($v)."]]></option>";
					}
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"",__LINE__,"<p>$key -> ".$this->fields[$key]["value"]."</p>"));}
				}
			}
			if(count($this->loadedcat)==0){
				$this->loadedcat = $this->call_command("CATEGORYADMIN_LOAD", Array("returntype"=>1, "list" => $category_list));
			}
			$sql = "select cto_clist from information_entry 
					inner join category_to_object on (cto_object = ie_identifier and ie_client = cto_client and cto_module='".$this->webContainer."')
				 where 
				 	ie_list = $list and ie_status = 1 and ie_client = $this->client_identifier and ie_identifier= $identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}

			$result  = $this->parent->db_pointer->database_query($sql);
			$num = $this->call_command("DB_NUM_ROWS",Array($result));
			
			$fields ="";
			$category_list="";
			$id=$parent_identifier;//$identifier;
			$c=0;
			$title="";
			$cat = Array();
			if ($num==0){
				/*************************************************************************************************************************
				* Does not have any category assigned
                *************************************************************************************************************************/
				$cat[0]= $category_list;
			} else {
				/*************************************************************************************************************************
                * has been assigned to multiple categories
                *************************************************************************************************************************/
	            while($r = $this->parent->db_pointer->database_fetch_array($result)){
					$cat[count($cat)]	= $r["cto_clist"]; // category listing
                }
			}
            $this->parent->db_pointer->database_free_result($result);;
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::Cat array",__LINE__,print_r($cat,true)));}
			$make_uri = "";
//			print_r($this->fields);
			foreach($this->fields as $key => $value){
				//print "<li>[$key]  [$value]</li>";
				if ($key == "ie_title"){
					$title = $this->check_parameters($value["specified"],1,$value["specified"][0]);
//					print "[$title]";
					if ($title!=""){
						$make_uri = $this->make_uri(htmlentities(strip_tags(html_entity_decode($title))));
						if ($old_ie_identifier == -1)
							$make_uri = $make_uri.'_'.$this->getUid();
					} else {
						$make_uri = "undefined";
					}
				}
				if($value[5]=="1"){
					$link = " link='".$value[5]."'";
				} else {
					$link = "";
				}
				//print "<li>".$value[4]."</li>";
				if($value[4]=="rowsplitter"){
//							$fields .= "		</seperator>\n	</seperator_row>\n	<seperator_row>\n		<seperator>\n";
				} else if($value[4]=="colsplitter"){
//						$fields .= "		</seperator>\n		<seperator>\n";
				} else if($value[4]=="smallmemo"){
					$fields .= "		<field $link type='CDATA' name='$key' visible='yes'><![CDATA[".$this->fields[$key]["specified"][0]."]]></field>\n";
				} else if($value[4]=="memo"){
					$fields .= "		<field $link type='CDATA' name='$key' conlabel='".$value[6]."' visible='yes'><![CDATA[".$this->fields[$key]["specified"][0]."]]></field>\n";
				} else if($value[4]=="radio" || $value[4]=="select" || $value[4]=="list" || $value[4]=="check"){
					$fields .= "		<field $link type='LIST' name='$key' visible='yes'>".$value["value"]."</field>\n";
//				} else if($value[4]=="url"){
//					$fields .= "		<field $link type='url' name='$key' visible='yes'><![CDATA[".$this->fields[$key]["specified"][0]."]]></field>\n";
				} else if (
					($value[4]=="email") || 
					($value[4]=="associated_entries") || 
					($value[4]=="associations") || 
					($value[4]=="imageembed") || 
					($value[4]=="image") || 
					($value[4]=="date") || 
					($value[4]=="datetime") || 
					($value[4]=="time")
				){
					$fields .= "		<field type='".$value[4]."' name='$key' visible='yes'>".$value["value"]."</field>\n";
				} else if($value[4]=="URL"){					
					$scr = " screen='$screen'";
					$fields .= "			<field $link type='URL' name='$key' visible='yes' $scr>
												<label><![CDATA[".$value[0]."]]></label>
												<value><![CDATA[".trim($this->html_2_txt($this->fields[$key]["specified"][0]))."]]></value>
												<uri><![CDATA[".trim($this->html_2_txt($this->fields[$key.'_link']["specified"][0]))."]]></uri>
												<maps><![CDATA[".$this->check_parameters($this->fields[$key]["value"],$screen)."]]></maps>
											</field>\n";
				} else {
				//<label><![CDATA[".$value[0]."]]></label>
					$fields .= "			<field $link type='CDATA' name='$key' visible='yes'><![CDATA[".trim($this->html_2_txt($this->fields[$key]["specified"][0]))."]]></field>\n";
				}
			}

			for($index=0 ; $index < count($cat) ; $index++){
				$category_list  .= "	<choosencategory identifier='".$cat[$index]."'/>\n";
			}
			if($screen==0){
				if ($build_directory_files==1){
					for($index=0 ; $index < count($cat) ; $index++){
						$uri = $this->find_path($cat[$index]);

						if ($old_ie_identifier != -1){
							$sql_old_ie = "select * from information_entry where ie_client = $this->client_identifier and  ie_identifier='$old_ie_identifier'";
							$result_old_ie 	= $this->parent->db_pointer->database_query($sql_old_ie);
							$r_old_ie = $this->parent->db_pointer->database_fetch_array($result_old_ie);
							$db_ie_counter = $r_old_ie['ie_counter'];
							$db_ie_uri = $r_old_ie['ie_uri'];
							$make_uri_arr = split(".php",$db_ie_uri);
							$make_uri = $make_uri_arr[0];

							$file_name	= $make_uri.".php";

							if($info_summary_only == 0){
								$this->translate_to_filename($uri."/index.php", $make_uri, $id, $fake_uri, $cat[$index], 1, $file_name);
							}

							$update = "update information_entry set ie_counter='$db_ie_counter' where ie_client = $this->client_identifier and ie_list = $list and ie_identifier=$id";
							$this->parent->db_pointer->database_query($update);
							$this->parent->db_pointer->database_free_result($result);

						}else{
						
						/**
                        * look for duplication of the field name and add the ID if required
                        */
						$sql = "
						select 
							cat1.cto_clist, 
							ie.ie_identifier as owner_id, 
							ie.ie_parent as owner_parent,
							ie.ie_uri as owner_uri,
							ie.ie_counter as owner_counter,
							
							ie1.ie_identifier as other_id, 
							ie1.ie_parent as other_parent,
							ie1.ie_uri as other_uri,
							ie1.ie_counter as other_counter
							
						from information_entry as ie
							inner join metadata_details as md on md.md_link_id = ie.ie_identifier and md.md_module = '$this->webContainer' and md.md_client=ie.ie_client
							inner join category_to_object as cat1 on cat1.cto_module='$this->webContainer' and cat1.cto_object = md.md_link_id and cat1.cto_client = ie.ie_client
							inner join metadata_details as md1 on md1.md_title = md.md_title and md1.md_link_group_id != md.md_link_group_id and md1.md_module = '$this->webContainer' and md.md_client=ie.ie_client
							inner join information_entry as ie1 on md1.md_link_id = ie1.ie_identifier and ie1.ie_client=ie.ie_client and ie.ie_list = ie1.ie_list
							left outer join category_to_object as cat2 on cat2.cto_module='$this->webContainer' and cat2.cto_object = md1.md_link_id and cat2.cto_client = ie.ie_client
						where ie.ie_client= $this->client_identifier and ie.ie_list = $list and ie.ie_identifier = $identifier and ie.ie_parent != ie1.ie_parent 
						";

//						print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
//						$this->exitprogram();
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
						$result 	= $this->parent->db_pointer->database_query($sql);
	                    $c 			= $this->call_command("DB_NUM_ROWS",Array($result));
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::number of records",__LINE__,"$c"));}
						$data_details= Array();
						$max = 1;
						$owner_counter=0;
                        while($r = $this->parent->db_pointer->database_fetch_array($result)){
                        	$data_details[count($data_details)] = Array($r["other_id"], $r["other_parent"],$r["other_uri"],$r["other_counter"]);
							$owner_counter = $r["owner_counter"];
							if ($owner_counter>$max){
								$max = $owner_counter;
							}
							if ($r["other_counter"]>$max){
								$max = $r["other_counter"];
							}
                        }
                        $this->parent->db_pointer->database_free_result($result);
//						print_r($data_details);
//						print "<li>".__FILE__."@".__LINE__."<p>$c, $owner_counter, $max</p></li>";
						if ($c==0){

							if($owner_counter==-1){
								$owner_counter=0;
							}
							$file_name	= $make_uri.".php";
							if($info_summary_only == 0){
								$this->translate_to_filename($uri."/index.php", $make_uri, $id, $fake_uri, $cat[$index], 0, $file_name);
							}
							$update = "update information_entry set ie_counter=0 where ie_client = $this->client_identifier and ie_list = $list and ie_identifier=$id";
	//						print "<li>".__FILE__."@".__LINE__."<p>$update</p></li>";
							$this->parent->db_pointer->database_query($update);
						} else {
							if($owner_counter==-1){
								$file_name	= $make_uri."_".($max+1).".php";
								$update = "update information_entry set ie_counter=" . ( $max + 1 ) . " where ie_client = $this->client_identifier and ie_list = $list and ie_identifier=$id";
//								print "<li>".__FILE__."@".__LINE__."<p>$update</p></li>";
								$this->parent->db_pointer->database_query($update);
							} else {
								if($owner_counter==0){
									$file_name	= $make_uri.".php";
								} else {
									$file_name	= $make_uri."_".$owner_counter.".php";
								}
							}
							if($info_summary_only == 0){
								$this->translate_to_filename($uri."/index.php", $make_uri, $id, $fake_uri, $cat[$index], 1, $file_name);
							}
						}
	                    $this->parent->db_pointer->database_free_result($result);
	//					print "[$uri/index.php, $make_uri, $id, $fake_uri, ".$cat[$index]." ] = $file_name";
	//					$this->exitprogram();
//	*/
					}//else if ($old_ie_identifier != -1)
				  }
				}
			}
			//$this->exitprogram();
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::filename",__LINE__,"$file_name"));}
			$lang="en";
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
			$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$id.".xml";
			//print"<li>$fname</li>";
			$fp = fopen($fname, 'w');
			$out = "<entry identifier='$id' user='$entry_user' real_id='$real_id'>\n";
			if($screen==1){
				$v	 = $this->check_parameters($this->fields,"ie_title",Array());
				$t	 = $this->check_parameters($v,"specified",Array());
				$tmp = $this->check_parameters($t,0);
				if ($tmp!=""){
					$fname = $this->make_uri($tmp);
				} else {
					$fname = "undefined";
				}
			}
//			print_r(Array("module"=>$this->webContainer, "identifier"=>$identifier ));
			$this->call_command("METADATAADMIN_CACHE", Array("module"=>$this->webContainer, "identifier"=>$identifier ));
			$out .= "<field name='uri' link='no' visible='no'><value><![CDATA[$file_name]]></value></field>\n";
//			$out .= "	<seperator_row>\n		<seperator>\n";
			$out .= "$fields";
//			$out .= "		</seperator>\n	</seperator_row>\n";
			$out .= "$category_list";
			$out .= "</entry>\n";

			fwrite($fp, $out);

			fclose($fp);
			$um = umask(0);
			@chmod($fname, LS__FILE_PERMISSION);
			umask($um);
			$sql ="update information_entry set ie_cached = 0 where ie_parent=$id and ie_identifier !=$identifier and ie_client=$this->client_identifier";
			//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
            $this->parent->db_pointer->database_query($sql);
			$sql ="update information_entry set ie_uri = '$file_name', ie_cached=1 where ie_parent=$id and ie_identifier=$identifier and ie_client=$this->client_identifier";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
            $this->parent->db_pointer->database_query($sql);
			
		//}
//		print "<li>$fname</li>";
//		$this->exitprogram();


		
	}

	function previewform($parameters){

		$max_number_of_fields	= $this->check_parameters($parameters,"max_number_of_fields");
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<form name=\"process_form\" label=\"Preview of Form\" width=\"100%\">";
		/*************************************************************************************************************************
		*  List of available fields indexes
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Set Defaults for fields.
		*  0 = Field label, 
		*  1 = Rank, 
		*  2 = Description, 
		*  3 = Selected, 
		*  4 = Type
		*
		*************************************************************************************************************************/
		$out .="			<seperator_row><seperator>\n";
		
		for ($index=0; $index<$max_number_of_fields ; $index++){
//			$visible = $this->check_parameters($parameters,"visiblefields_".$index,"__NOT_FOUND__");
//			$rank	= $this->check_parameters($parameters,"rank_".$index);
			$hfield	= $this->check_parameters($parameters,"hfield_".$index);
			$vfield	= html_entity_decode($this->check_parameters($parameters,"vfield_".$index));
			$options= $this->check_parameters($parameters,"options_".$index);
			$type	= $this->check_parameters($parameters,"type_".$index);
			if($type=="text"){
				$out .="			<input type=\"text\" name=\"".$hfield."\" label=\"".$vfield."\" size=\"255\"><![CDATA[]]></input>\n";
			}
			if($type=="smallmemo"){
				$out .="			<textarea type=\"text\" name=\"".$hfield."\" label=\"".$vfield."\" size=\"10\" height=\"6\"><![CDATA[]]></textarea>\n";
			}
			if($type=="memo"){
				$out .="			<textarea type=\"text\" name=\"".$hfield."\" label=\"".$vfield."\" size=\"10\" height=\"12\"><![CDATA[]]></textarea>\n";
			}
			if($type=="radio"){
				$out .="			<radio type='vertical' name=\"".$hfield."\" label=\"".$vfield."\">";
				if (count($options)!=0){
					$optionlist = split("::ls_option::", $options);

					for ($opt =0, $max = count($optionlist); $opt <$max ;$opt++){
						if ($optionlist[$opt]!=''){
							$out .= "<option><![CDATA[".urldecode($optionlist[$opt])."]]></option>";
						}
					}
				}
				$out .= "</radio>\n";
			}
			if($type=="select"){
				$out .="			<select name=\"".$hfield."\" label=\"".$vfield."\">";
				if (count($options)!=0){
					$optionlist = split("::ls_option::", $options);

					for ($opt =0, $max = count($optionlist); $opt <$max ;$opt++){
						if ($optionlist[$opt]!=''){
							$out .= "<option><![CDATA[".urldecode($optionlist[$opt])."]]></option>";
						}
					}
				}
				$out .= "</select>\n";
			}
			if($type=="check"){
				$out .="			<checkboxes type='vertical' name=\"".$hfield."\" label=\"".$vfield."\">";
				if (count($options)!=0){
					$optionlist = split("::ls_option::", $options);
					for ($opt =0, $max = count($optionlist); $opt <$max ;$opt++){
						if ($optionlist[$opt]!=''){
							$out .= "<option value='".urldecode($optionlist[$opt])."'><![CDATA[".urldecode($optionlist[$opt])."]]></option>";
						}
					}
				}
				$out .= "</checkboxes>\n";
			}
			if($type=="list"){
			
				$out .="			<select multiple='1' size='10' name=\"".$hfield."\" label=\"".$vfield."\">";
				if (count($options)!=0){
					$optionlist = split("::ls_option::", $options);

					for ($opt =0, $max = count($optionlist); $opt <$max ;$opt++){
						if ($optionlist[$opt]!=''){
							$out .= "<option><![CDATA[".urldecode($optionlist[$opt])."]]></option>";
						}
					}
				}
				$out .= "</select>\n";
			}
			if($type=="colsplitter"){
				$out .="			</seperator><seperator>\n";
			}
			if($type=="rowsplitter"){
				$out .="			</seperator></seperator_row><seperator_row><seperator>\n";
			}
		}
		$out .="			</seperator></seperator_row>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: information_removal($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function will remove a directory completly from the system
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*************************************************************************************************************************/
	function information_removal($parameters){
		$root  					= $this->check_parameters($this->parent->site_directories,"ROOT");

		$identifier				= $this->check_parameters($parameters,"identifier");
		$list_id	 			= $this->check_parameters($parameters,"list_id",-1);
		$redirect_status	 	= $this->check_parameters($parameters,"redirect_status",-1);

		$sql = "select iev_value, cto_clist, info_category, menu_url from information_entry_values 
					inner join category_to_object on (iev_client = cto_client and cto_object = iev_entry and cto_module='$this->webContainer')
					inner join information_list on (info_client= iev_client and iev_list = info_identifier)
					inner join menu_data on (info_client= menu_client and menu_identifier = info_menu_location)
				where 
					iev_entry	=	$identifier and 
					iev_client	=	$this->client_identifier and 
					iev_list	=	$list_id and 
					iev_field	=	'ie_title'
			";
		$result  = $this->parent->db_pointer->database_query($sql);
		$c = $this->call_command("DB_NUM_ROWS",Array($result));
		if ($c==0){
			$sql = "select iev_value, info_category, menu_url from information_entry_values 
					inner join information_list on (info_client= iev_client and iev_list = info_identifier)
					inner join menu_data on (info_client= menu_client and menu_identifier = info_menu_location)
				where 
					iev_entry	=	$identifier and 
					iev_client	=	$this->client_identifier and 
					iev_list	=	$list_id and 
					iev_field	=	'ie_title'
			";
			$result  = $this->parent->db_pointer->database_query($sql);
		}
        $list_of_files= Array();
		$pos =0;
		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			$list_of_files[$pos] 	= Array();
			$list_of_files[$pos][0] = $r["iev_value"];
			$list_of_files[$pos][1] = $this->check_parameters($r,"cto_clist",0);
			$pos++;
			$info_category			= $r["info_category"];
			$fake_uri 				= $r["menu_url"];
		}
		if (count($this->loadedcat)==0){

			$this->loadedcat = $this->call_command("CATEGORYADMIN_LOAD", Array("returntype"=>1, "list" => $info_category));
		}
		
		for($index=0;$index<$pos;$index++){
			$filename = $this->make_uri($list_of_files[$index][0]);
			$uri = $this->find_path($list_of_files[$index][1]);
			$complete_filename = $root."/".dirname($fake_uri)."$uri/$filename"."-"."$identifier".".php";

			if (file_exists($complete_filename)){
				@unlink($complete_filename);

			}
		}
		if ($list_id != -1) {
			$sql = "select if_duplicate,iev_value from information_fields 
			inner join information_entry_values on (iev_client=if_client and iev_list = if_list and iev_entry = $identifier and iev_field='ie_image1') 
			where if_list =$list_id and if_client=$this->client_identifier  and if_type = 'image' and if_screen = 0";			
			$result  = $this->parent->db_pointer->database_query($sql);
			$r = $this->parent->db_pointer->database_fetch_array($result);
			/** Check if deletion of embedded images is need. remember if_duplicate field carries the image deletion flag */
			if ($r['if_duplicate'] == 'Yes' && $r['iev_value'] != ''){		
				$this->call_command("FILES_LIST_REMOVE_NO_REFRESH", Array("identifier"=>$r['iev_value']));
			}
		}
		
		$sql = "delete from information_entry where ie_client=$this->client_identifier and ie_identifier = $identifier";
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from information_entry_values where iev_client=$this->client_identifier and iev_entry = $identifier";
		$this->parent->db_pointer->database_query($sql);
		$this->call_command("CATEGORYADMIN_TO_OBJECT_REMOVE", Array("identifier"=>$identifier,"module"=>$this->webContainer));
			
		/*
		$sql = "delete from information_category_relationship where icr_client=$this->client_identifier and icr_entry = $identifier";
		$this->parent->db_pointer->database_query($sql);
		*************************************************************************************************************************/
		if ($redirect_status == -1){
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST_ENTRIES&amp;identifier=$list_id&amp;recache=1"));
		}
	}
	
	function information_list_removal($parameters){
		$identifier				= $this->check_parameters($parameters,"identifier",-1);
		/*************************************************************************************************************************
		* get menu location and category
		*************************************************************************************************************************/ 
		$sql = "select * from information_list 
			inner join menu_data on menu_identifier = info_menu_location and info_client= menu_client
		where info_client = $this->client_identifier and info_identifier=$identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$info_category 	= $r["info_category"];
			$menu_url 		= $r["menu_url"];
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
		* remove the info directory
		*************************************************************************************************************************/ 
		$sql = "delete from information_list where info_identifier = $identifier and info_client= $this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		/*************************************************************************************************************************
		* remove fields
		*************************************************************************************************************************/ 
		$sql = "delete from information_fields where if_list = $identifier and if_client= $this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		/*************************************************************************************************************************
		* Remove options
		*************************************************************************************************************************/ 
		$sql = "delete from information_options where io_client=$this->client_identifier and io_list = $identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		/*************************************************************************************************************************
		* Remove Searchs
		*************************************************************************************************************************/ 
		$sql = "delete from information_search where ibs_client=$this->client_identifier and ibs_list = $identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from information_advanced_search where ias_client=$this->client_identifier and ias_list = $identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		
		
		/*************************************************************************************************************************
		* Remove update access if required
	*************************************************************************************************************************/ 
		$sql = "delete from information_update_access where iua_client=$this->client_identifier and iua_list = $identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		/*************************************************************************************************************************
		* remove features
		*************************************************************************************************************************/ 
		$sql = "select * from information_features where ifeature_client = $this->client_identifier and ifeature_list = $identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
        $result  = $this->parent->db_pointer->database_query($sql);
		$features="";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if($features!=""){
        		$features .= ", ";
			}
        	$features .= $r["ifeature_identifier"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$sql = "delete from information_features where ifeature_list = $identifier and ifeature_client= $this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		if($features!=""){
			$sql = "delete from information_feature_list where ifl_owner in ($features) and ifl_client= $this->client_identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->parent->db_pointer->database_query($sql);
		}
		/*************************************************************************************************************************
		* remove from entries
		*************************************************************************************************************************/ 
		$sql = "delete from information_entry_values where iev_list = $identifier and iev_client= $this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from information_entry_links where ievl_list = $identifier and ievl_client= $this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from information_entry where ie_list = $identifier and ie_client= $this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from information_entry_relationship where ier_list = $identifier and ier_client= $this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		/*************************************************************************************************************************
		* remove from info data table
		*************************************************************************************************************************/ 
		$sql = "delete from information_data where id_list = $identifier and id_client= $this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->parent->db_pointer->database_query($sql);
		/*************************************************************************************************************************
		* remove formatting from the cache directory*
		*************************************************************************************************************************/
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];

		$file =$data_files."/"."information_presentation_".$this->client_identifier."_en_".$identifier."_content.xml"; 
		$um =umask(0);
		@chmod($file, LS__FILE_PERMISSION);
		umask($um);
		@unlink($file); // remove this file

		$file =$data_files."/"."information_presentation_".$this->client_identifier."_en_".$identifier."_summary.xml"; 
		$um =umask(0);
		@chmod($file, LS__FILE_PERMISSION);
		umask($um);
		@unlink($file); // remove this file

		$file =$data_files."/"."information_presentation_".$this->client_identifier."_en_".$identifier."_search.xml"; 
		$um =umask(0);
		@chmod($file, LS__FILE_PERMISSION);
		umask($um);
		@unlink($file); // remove this file
		/*************************************************************************************************************************
		* remove entries from the cache directory
		*************************************************************************************************************************/ 
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"removed file",__LINE__,"".$data_files."/".$file.""));}
		if ($handle = @opendir($data_files)) {
			while (($file = readdir($handle)) !== false) {
				if ($file == "." || $file == "..") {
					continue;
				}
				if(substr($file,0,strlen("information_presentation_".$this->client_identifier."_".$identifier."_"))=="information_presentation_".$this->client_identifier."_".$identifier."_"){
					$um =umask(0);
					@chmod($data_files."/".$file, LS__FILE_PERMISSION);
					umask($um);
					@unlink($data_files."/".$file); // remove this file
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"removed file",__LINE__,"".$data_files."/".$file.""));}
				}
			}
		}
		@closedir($handle);
		/*************************************************************************************************************************
		* step 3 - delete the directory structure*
		*************************************************************************************************************************/
		$sql = "select * from category where cat_parent = cat_list_id and cat_list_id = $info_category";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->parent->db_pointer->database_query($sql);
		$root  = $this->check_parameters($this->parent->site_directories,"ROOT")."/";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$path = dirname($menu_url)."/".$this->make_uri($r["cat_label"]);
			$this->terminate_directory($path);
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Terminate directory",__LINE__,"".$path.""));}
			$um =umask(0);
			@chmod($root.$path, LS__DIR_PERMISSION);
			umask($um);
			@rmdir($root.$path);
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
		* remove category completely
		*************************************************************************************************************************/ 
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"CATEGORYADMIN_LIST_REMOVE",__LINE__,"info_category"));}
		$this->call_command("CATEGORYADMIN_LIST_REMOVE",Array("identifier"=>$info_category, "next_command"=>$this->module_command."LIST"));
/*
		$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> "".$this->module_presentation."SEARCH",
					"owner_id" 		=> $identifier,
					"label" 		=> $label,
					"wo_command"	=> "".$this->module_presentation."SEARCH",
					"cmd"			=> "REMOVE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
*************************************************************************************************************************/
	}
	
	function importform($parameters){
//		$directory_options="";
		
		$identifier  = $this->check_parameters($parameters,"import_into_directory",$this->check_parameters($parameters,"identifier",-1));
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";

		$out .="	<form name=\"process_form\" label=\"Import Directory\" width=\"100%\">";
		$out .= "<page_options>
					<header><![CDATA[Import data into this directory]]></header>";
		if($this->manage_database_list==1){
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		} else {
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEXT","ENGINE_SPLASH",LOCALE_CANCEL));
		}
		$out .="</page_options>";
		/*************************************************************************************************************************
		* get the list of files from the upload directory that have the proper file extension (txt, csv)
	*************************************************************************************************************************/ 
		$file_list="";
		if (file_exists($this->parent->site_directories["TMP_UPLOAD_DIR"]."/.")){
			$d = dir($this->parent->site_directories["TMP_UPLOAD_DIR"]); 
			while (false !== ($entry = $d->read())) { 
				if($entry!="." && $entry!=".."){
					if ((substr($entry,-4)==".txt") || (substr($entry,-4)==".csv")){
						$file_list .= "<option value='/$entry'>$entry</option>";
					}
				}
			} 
			$d->close();
		}
		if($identifier!=-1){
			$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."IMPORT_FILE\" />";
			$out .="		<input type=\"hidden\" name=\"identifier\" value=\"1\" />";
			$out .="		<input type=\"hidden\" name=\"import_into_directory\" value=\"$identifier\" />";
			$out .="		<page_sections>";
			$out .="			<section label='Import details'>";
			$out .="				<text><![CDATA[Please select a file (Tab or Comma Seperated format)to import for best results a Tab Seperated file format is recommended]]></text>";
			$out .="				<input type='file' name='import_file_format_tab' label='File to import'/>";
			if ($file_list!=""){
				$file_list="<option value='__NOT_FOUND__'>Select One</option>".$file_list;
				$out .="				<select name='import_file_selection' label='Choose an uploaded file' >$file_list</select>";
			}
			$out .="			</section>";
			$out .="		</page_sections>";
			$out .="		<input type=\"submit\" iconify=\"NEXT\" value=\"Import\" />";
		} else {
			$out .="		<text><![CDATA[You have not defined a Information directory to import into. You must create a new Information Directory before you can import information.]]></text>";
		}
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * import file for user to import
    *************************************************************************************************************************/
	function importfile($parameters){
//		print_r($parameters);
		$f = $this->check_parameters($_FILES,"import_file_format_tab", "__NOT_FOUND__");
		$ifs = $this->check_parameters($parameters,"import_file_selection", "__NOT_FOUND__");
		$import_into_directory	 = $this->check_parameters($parameters,"import_into_directory",-1);
//		print "[$f ,$ifs ,$import_into_directory]";
		$tmp_name = $f["tmp_name"];
		if(file_exists($tmp_name)){
			/*************************************************************************************************************************
            * remove old file if it exists (escaped previous upload)
            *************************************************************************************************************************/
			if(file_exists($this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.txt")){
				$um =umask(0);
				@chmod($this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.txt", LS__FILE_PERMISSION);
				umask($um);
				@unlink($this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.txt");
			}
			move_uploaded_file($tmp_name, $this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.txt");
			$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."EXAMINE_IMPORT_FILE&amp;import_into_directory=$import_into_directory"));
		} 
		if ($ifs != "__NOT_FOUND__"){
			if (file_exists($this->parent->site_directories["TMP_UPLOAD_DIR"].$ifs)){
				if(file_exists($this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.txt")){
					@unlink($this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.txt");
				}
				$um =umask(0);
				@chmod($this->parent->site_directories["TMP_UPLOAD_DIR"].$ifs, LS__FILE_PERMISSION);
				umask($um);
				rename ($this->parent->site_directories["TMP_UPLOAD_DIR"].$ifs, $this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.txt");
				$um =umask(0);
				@chmod($this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.txt", LS__FILE_PERMISSION);
				umask($um);
				$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."EXAMINE_IMPORT_FILE&amp;import_into_directory=$import_into_directory"));
			}
		}
		$out  = $this->importform($parameters);
		return $out;
	}
	/*************************************************************************************************************************
    * examine the import file and import into the system
    *************************************************************************************************************************/
	function examine_import($parameters){
		$page 					= $this->check_parameters($parameters, "page", 1);
		$importpage 			= $this->check_parameters($parameters, "importpage", 1);
		$import_into_directory	= $this->check_parameters($parameters,"import_into_directory",-1);
		$max 					= 20;
		$path 					= $this->parent->site_directories["TMP"]."/infodir_import_".$_SESSION["SESSION_USER_IDENTIFIER"]."_$import_into_directory.txt";
		$uploadpath 			= $this->parent->site_directories["TMP_UPLOAD_DIR"];
		$upload_dir_path		= $this->check_parameters($_SESSION,"upload_dir_path");
		$upload_dir_id			= $this->check_parameters($_SESSION,"upload_dir_id");
		if ($page==1){
			$_SESSION["if_lookup_merge"] = Array();
			$_SESSION["upload_dir_path"] = "";
			$_SESSION["upload_dir_id"]	 = "";
			$sql = "select * from directory_data where directory_can_upload=1 and directory_client=$this->client_identifier";
            $result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	if ($r["directory_name"]=="uploads"){
					$_SESSION["upload_dir_path"] = "uploads";
					$_SESSION["upload_dir_id"]	 = $r["directory_identifier"];
				}
            }
            $this->parent->db_pointer->database_free_result($result);
			$sql= "select * from information_list where info_identifier=$import_into_directory and info_client=$this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			$category_root=-1;
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        		$category_root = $r["info_category"];
    	    }
	        $this->parent->db_pointer->database_free_result($result);
			// examine file display (first 3 rows)
			if (file_exists($path)){
				$lines = file($path);
				$m = count($lines);
				if ($m>$max){
					$m = $max;
				}
				$result = "";
				$first_row = "";
				$t="";
				$error=0;
				if($m!=0){
					$splitter="\t";
					if(strpos($lines[0],$splitter)===false){
						$splitter=",";
						if(strpos($lines[0],$splitter)===false){
							$error=1;
						}
					}
				}
				if($error==0){
					// get fields from info directory 
					$dirfield="";
					$sql 	 = "select if_name, if_label, if_type, if_duplicate from information_fields 
						where 
							if_list=$import_into_directory and 
							if_client=$this->client_identifier and 
							if_screen=0 and
							if_type not in ('rowsplitter','colsplitter')
					order by if_rank";
					$result  = $this->parent->db_pointer->database_query($sql);
					$_SESSION["if_duplicate_list"]	= Array();
					$_SESSION["if_lookup_merge"]	= Array();
	                while($r = $this->parent->db_pointer->database_fetch_array($result)){
	                	$dirfield .= ",new Array('".$r["if_name"]."','".$r["if_label"]."','".$r["if_type"]."')";
						if ($r["if_duplicate"]!=""){
							$_SESSION["if_duplicate_list"][count($_SESSION["if_duplicate_list"])] = Array(
								"name"	=> $r["if_name"],
								"type"	=> $r["if_duplicate"]
							);
						}
						if ($r["if_type"]=="check" || $r["if_type"]=="radio" || $r["if_type"]=="select" || $r["if_type"]=="list"){
							$_SESSION["if_lookup_merge"][count($_SESSION["if_lookup_merge"])] = Array(
								"name"	=> $r["if_name"],
								"type"	=> $r["if_type"]
							);
						}
	                }
	                $this->parent->db_pointer->database_free_result($result);
					$result="";
					$field_js="";
					$delimiter='"';
					if ($m>0){
						$first = split($splitter,$lines[0]);
						for($i=0;$i<count($first);$i++){
							if ((substr($first[$i],0,1)=='"') && (substr($first[$i],strlen($first[$i])-1)=='"')){
								$delimiter = '"';
							}
						}
					}
					for ($i=0;$i<$m;$i++){
						$fields  	 = split($splitter,$lines[$i]);
						$result 	.= "<tr><td class='bt' align='right'>#".($i+1)."</td>";
						$fmax 		 = count($fields);
						if($i<3){
							if($field_js!=""){
								$field_js .=",\n";
							}
							$field_js	.= "new Array(";
						}
						for($findex  = 0; $findex<$fmax;$findex++){
							$result .= "<td bgcolor=#ffffff>".str_replace(
											Array("$delimiter"),
											Array(""),
											trim($fields[$findex])
										)."</td>";
							$t		.= "<td class='bt'><strong>Field ".($findex+1)."</strong></td>";
							if($i<3){
								if($findex!=0){
									$field_js .=",";
								}
								$field_js .= "'".str_replace("'","\'",str_replace(Array("$delimiter"),Array(""),trim($fields[$findex])))."'";
							}
						}
						$result		.= "</tr>";
						if($i<3){
							$field_js	.= ")";
						}
						if($first_row==""){
							$first_row = "<tr><td class='bt'>[[nbsp]]</td>$t</tr>";
						}
					}
					$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
					$out .="	<page_options>";
					$out .="		<header><![CDATA[Examining Import File]]></header>";
					$out .= 		$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
					$out .="	</page_options>";
					$out .="	<form name=\"importform\" label=\"Import Directory\" width=\"100%\">";
					$out .="		<input type='hidden' name='command' value='INFORMATIONADMIN_EXAMINE_IMPORT_FILE'/>";
					$out .="		<input type='hidden' name='import_into_directory' value='$import_into_directory'/>";
					$out .="		<input type='hidden' name='page' value='2'/>";
					$out .="		<input type='hidden' name='category_root' value='$category_root'/>";
					$out .="		<input type='hidden' name='delimiter' value='".ord($delimiter)."'/>";
					$out .="		<page_sections>";
					$out .="			<section label='Import preview' name='previewtab'>";
					$out .="				<text><![CDATA[
					<script>
						var field_data; 
						var directory_fields;
						function start_mapping(){
							field_data = new Array(\n$field_js\n);
							directory_fields = new Array(\nnew Array('category','Categorisation','category')$dirfield\n);
							import_tool = new information_directory_import_tool(field_data,directory_fields);
							import_tool.display();
						}
					</script>]]></text>";
					$out .="				<radio name='first_contains_fieldnames' label='Does the first row contain the Field names?'><option value='yes' selected='true'>Yes, do not import the first row as it represents the columns labels</option><option value='no'>No, import all records in the file</option></radio>";
					$out .="				<radio name='import_status' label='Do you want to mark all of these entries as approved'><option value='yes' selected='true'>Yes</option><option value='no'>No</option></radio>";
					$out .="				<input type='text' name='split_categories' size='5' label='Categories may appear as a path structure what character should the category path be split on.'><![CDATA[[[rightarrow]]]]></input>";
					$out .="				<text><![CDATA[First $m lines of your import file]]></text>";
					$out .="				<text><![CDATA[<table border='0' cellpadding=3 cellspacing=1 bgcolor=#cccccc>$first_row$result</table>]]></text>";
					$out .="			</section>";
					$out .="			<section label='Import Mapping' name='maptab' onclick='importmapping'>";
					$out .="			</section>";
	//				$out .="			<section label='Duplicate Checking' name='duptab' onclick='duplicatemapping'>";
	//				$out .="			</section>";
					$out .="		</page_sections>";
					$out .="		<input type=\"submit\" iconify=\"NEXT\" value=\"Import\" />";
					$out .="	</form>";
					$out .="</module>";
				} else {
					$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
					$out .="	<form name=\"process_form\" label=\"Import Directory\" width=\"100%\">";
					$out .="		<input type='hidden' name='command' value='INFORMATIONADMIN_IMPORT_FILE'/>";
					$out .="		<input type='hidden' name='import_into_directory' value='$import_into_directory'/>";
					$out .="		<page_sections>";
					$out .="			<section label='Import Error'>";
					$out .="				<text><![CDATA[Sorry I was unable to determine the format of the import file.]]></text>";
					$out .="			</section>";
					$out .="		</page_sections>";
					$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
					$out .="	</form>";
					$out .="</module>";
				}
			} else {
				$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
				$out .="	<form name=\"process_form\" label=\"Import Directory\" width=\"100%\">";
				$out .="		<input type='hidden' name='command' value='INFORMATIONADMIN_IMPORT_FILE'/>";
				$out .="		<input type='hidden' name='import_into_directory' value='$import_into_directory'/>";
				$out .="		<page_sections>";
				$out .="			<section label='Import Error'>";
				$out .="				<text><![CDATA[Sorry I was unable to retrieve the import file. This is usually caused by comming back to this screen after running the import]]></text>";
				$out .="			</section>";
				$out .="		</page_sections>";
				$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
				$out .="	</form>";
				$out .="</module>";
			}
			$_SESSION["IMPORT_COUNTER"] = 0 ;
//			print_r($_SESSION["if_lookup_merge"]);
			return $out;
		}
		if($page==2){
			/*************************************************************************************************************************  	
			print "<p></p>";
			print $_SESSION["upload_dir_path"];
			print $_SESSION["upload_dir_id"];
			print "<p></p>";
			*************************************************************************************************************************/
			$_SESSION["IMPORT_ERROR_ROWS"]	= Array();
			$fieldindex						= $this->check_parameters($parameters,"fieldindex",			$this->check_parameters($_SESSION,"fieldindex",		Array()));
			$import_status					= $this->check_parameters($parameters,"import_status",		$this->check_parameters($_SESSION,"import_status",	"yes"));
			$importindex					= $this->check_parameters($parameters,"importindex",		$this->check_parameters($_SESSION,"importindex",	Array()));
			$category_root					= $this->check_parameters($parameters,"category_root",		$this->check_parameters($_SESSION,"category_root",	-1));
			$delimiter						= $this->check_parameters($parameters,"delimiter",			$this->check_parameters($_SESSION,"delimiter"		));
			$split_categories				= $this->check_parameters($parameters,"split_categories",	$this->check_parameters($_SESSION,"split_categories"));
			if("__NOT_FOUND__" == $this->check_parameters($_SESSION,"StartImport","__NOT_FOUND__")){
				$_SESSION["StartImport"] = $this->getmicrotime();
			}
			$_SESSION["fieldindex"]			= $fieldindex;
			$_SESSION["import_status"]		= $import_status;
			$_SESSION["importindex"]		= $importindex;
			$_SESSION["category_root"]		= $category_root;
			$_SESSION["delimiter"]			= $delimiter;
			$_SESSION["split_categories"]	= $split_categories;
			
			$sql = "select * from information_fields where if_client = $this->client_identifier and if_list = $import_into_directory and if_screen=0";
            $result  = $this->parent->db_pointer->database_query($sql);
			$md = Array();
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$md[$r["if_name"]] = $r["if_map"];
            }
            $this->parent->db_pointer->database_free_result($result);
			
			$first_contains_fieldnames	= $this->check_parameters($parameters,"first_contains_fieldnames","no");
			if (file_exists($path)){
				$lines 					= file($path);
			} else {
				$lines					= Array();
			}
			$m 							= count($lines);
			$result 					= "";
			$first_row 					= "";
			$t							= "";
			$error						= 0;
			if($m != 0){
				$splitter = "\t";
				if(strpos($lines[0],$splitter)===false){
					$splitter = ",";
					if(strpos($lines[0],$splitter)===false){
						$error = 1;
					}
				}
			} else {
				$error=1;
			}
			if($error==0){
				if ($importpage==1){
					if ($first_contains_fieldnames=="yes"){
						$s = 1;
					} else {
						$s = 0;
					}
					$end = 50;
				} else {
					$s = ($importpage*50)-50 ;
					$end = $s+50;
				}
				if ($end>$m){
					$end = $m;
				}
				if($import_status == "yes"){
					$import_status = "1";
				}else {
					$import_status = "0";
				}
				$importable=1;
				/*************************************************************************************************************************
				* for each row in the file starting at index $s and finishing at index $end
				*************************************************************************************************************************/
				for ($i=$s;$i<$end;$i++){
					$now			= $this->libertasGetDate();
					$fields  		= split($splitter,$lines[$i]);
					$fmax 		 	= count($fieldindex);
					
					$duplicate_sql="";
					/*************************************************************************************************************************
                    * for each field
                    *************************************************************************************************************************/
					$this->current_import_row = Array();
//					print_r($fieldindex);
					for($findex  = 0; $findex < $fmax; $findex++){
						/*************************************************************************************************************************
                        * get value in field (remove delimiter if necessary
                        *************************************************************************************************************************/
						if ($delimiter!=""){
							$field_index = str_replace(Array(chr($delimiter)), Array(""), $fieldindex[$findex]);
							$value 		= $this->validate(str_replace(Array(chr($delimiter),"  "), Array("", " "), trim($this->check_parameters($fields, $field_index, ""))));
						}else {
							$field_index = $fieldindex[$findex];
							$value 		= $this->validate(str_replace(Array("  "), Array(" "), trim($this->check_parameters($fields, $field_index, ""))));
						}
						if ("__NOT_FOUND__" != $this->check_parameters($importindex,$field_index,"__NOT_FOUND__")){
							$this->current_import_row[$importindex[$findex]] = Array(
								"type"				=>	"", 
								"value"				=>	$value,
								"duplicate_check"	=>	""
							);
						}
						/*************************************************************************************************************************
                        * get field name that this will be imported into.
                        *************************************************************************************************************************/
						$field_name = $this->check_parameters($importindex,$findex,"");
						if($field_name!='category'){
							if($value != ""){
								$maxdup = count($_SESSION["if_duplicate_list"]);
								$dlist = $this->check_parameters($_SESSION, "if_duplicate_list", Array());
								for($start = 0; $start < $maxdup ; $start++){
									$duplicate_check = $this->check_parameters($dlist,$start,Array());
									$dupname = $this->check_parameters($duplicate_check,"name");
									$duptype = $this->check_parameters($duplicate_check,"type");
									if ($dupname==$field_name){
										$this->current_import_row[$field_name]["duplicate_check"] = $duptype;
										if ($duplicate_sql!=""){
											$duplicate_sql .= " or ";
										}
										if ($duptype=='exact'){
											$duplicate_sql .= "(iev_field='$field_name' and iev_value='$value') ";
										}
										if ($duptype=='contains'){
											$duplicate_sql .= "(iev_field='$field_name' and iev_value like '%$value%') ";
										}
										if ($duptype=='startswith'){
											$duplicate_sql .= "(iev_field='$field_name' and iev_value like '$value%') ";
										}
									}
								}
							}
						} else {
							if ($field_name!=""){
								$this->current_import_row[$field_name]["duplicate_check"] = "";
								$this->current_import_row[$field_name]["type"] = "category";
							}
						}
					}
					/*************************************************************************************************************************
                    * execute this sql to check for duplications based on setting by adminsitrator of Directory
                    *************************************************************************************************************************/
					if(count($_SESSION["if_duplicate_list"])>0){
						$sql ="SELECT distinct ie_parent  from information_entry 
								inner join information_entry_values on ie_list = iev_list and iev_entry = ie_identifier
								where 
								($duplicate_sql) and 
								(ie_version_wip=1) and ie_client=$this->client_identifier and ie_list = $import_into_directory
							  ";
						/*************************************************************************************************************************
						* default is import automatically unless number of records found > 0 
						*************************************************************************************************************************/
						$importable=1;
						$result  = $this->parent->db_pointer->database_query($sql);
						$c = $this->call_command("DB_NUM_ROWS",Array($result));
						
						$merged = 0;
						$all_dup_checks_match=0;
	                    if($c>0){
							$importable=0;
	                        while($r = $this->parent->db_pointer->database_fetch_array($result)){
								$checkid 					= $r["ie_parent"];
								$this->duplicate_import_row = Array();
								$all_dup_checks_match = $this->extractlatest($checkid);
								if($all_dup_checks_match>0){
									// merge lookup fields
									foreach($this->duplicate_import_row as $key => $entry){
										if ($entry["type"]=="list" || $entry["type"]=="radio" || $entry["type"]=="check" || $entry["type"]=="select" ){
											foreach ($this->current_import_row[$key]["value"] as $kdex => $val){
												if (!in_array($val, $entry["value"])){
													$iev_identifier = $this->getUid();
													$msql = "insert into information_entry_values (iev_identifier, iev_client,iev_field,iev_list,iev_entry,iev_value) values ($iev_identifier, $this->client_identifier, '$key', $import_into_directory, $all_dup_checks_match, '$val')";
													$this->parent->db_pointer->database_query($msql);
													$msql="";
													$merged = 1;
												}
											}
										}
									}
								}
	                        }
	                        $this->parent->db_pointer->database_free_result($result);
							// was not merged then add file row id to session for report
							if ($merged==0){
								if (empty($_SESSION["IMPORT_ERROR_ROWS"])){
									$_SESSION["IMPORT_ERROR_ROWS"] = Array();
								}
								$_SESSION["IMPORT_ERROR_ROWS"][count($_SESSION["IMPORT_ERROR_ROWS"])] = $i;
							}
	                    }
					} else {
						$importable=1;
						$merged=0;
						$all_dup_checks_match=0;
					}
					$missing = 0; 
					/*************************************************************************************************************************
					* category code
					*************************************************************************************************************************/
					for($findex  = 0; $findex<$fmax;$findex++){
						if ($delimiter!=""){
							$field_index = str_replace(Array(chr($delimiter)), Array(""), $fieldindex[$findex]);
							$value 		= $this->validate(str_replace(Array(chr($delimiter)), Array(""), trim($this->check_parameters($fields, $field_index, ""))));
						}else {
							$field_index = $fieldindex[$findex];
							$value 		= $this->validate(trim($this->check_parameters($fields, $field_index, "")));
							
						}
						$firstchar		= substr($value,0,1);
						$lastchar		= substr($value,-1);
						$endswith		= 0;
						$startswith		= 0;
						if ($firstchar == "\"" || $firstchar == "'"){
							$startswith=1;
						}
						if ($lastchar  == "\"" || $lastchar  == "'"){
							$endswith=1;
						}
						if (($endswith==1) || ($startswith==1)){
							if ($startswith==1)
								$s = 1;
							else 
								$s = 0;
							if ($endswith==1)
								$e = strlen($value) - 1;
							else 
								$e = strlen($value);
							$value = substr($value = $s, $e-$s);
						}
						$field_name = $this->check_parameters($importindex,$findex,"");
						if($field_name=='category'){
    		                //$this->parent->db_pointer->database_free_result($result);
							if($value!=""){
								$missing = $this->call_command("CATEGORYADMIN_TO_OBJECT_CHECK_PATH", 
									Array(
										"label"				=> $value,
										"module"			=> $this->webContainer,
										"identifier"		=> "",
										"category_root"		=> $category_root,
										"split_categories"	=> $split_categories
									)
								);
//								print_r($missing);
							}
						}
					}
					if ($importable==1 || $missing==1){
						$longDescription = "";
						$md_fields = Array();
						if ($importable==1 && $merged==0){
							$ie_identifier = $this->getUid();
							$entry = $ie_identifier;
							$sql = "insert into information_entry (ie_identifier, ie_parent, ie_client, ie_date_created, ie_list, ie_status, ie_user ,ie_uri, ie_version_wip, ie_version_minor, ie_version_major, ie_published)
										values
									($ie_identifier, $ie_identifier, $this->client_identifier, '$now', $import_into_directory, $import_status, '".$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0)."', '', 1, 1 ,0, $import_status)";
							$result  = $this->parent->db_pointer->database_query($sql);
							$_SESSION["IMPORT_COUNTER"]++;


			
/********** Insertion into category_to_object for Import Database portion Starts ( Added By Muhammad Imran Mirza )***********/

						$identifier = $ie_identifier;
						$cat_id_list 		= $this->check_parameters($parameters, "cat_id_list", -1);
						$newCategories		= $this->check_parameters($parameters, "newCategories");
						$list_id			= $this->check_parameters($parameters, "list_id", 		-1);
			
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::entry",__LINE__,print_r(Array("new_categories"	=> $newCategories,"data_list"			=> $cat_id_list,"module"			=> $this->webContainer,"identifier"		=> $identifier,"information_list"	=> $list_id), true)));}
						$clist = $this->call_command("CATEGORYADMIN_TO_OBJECT_UPDATE",
							Array(
								"new_categories"	=> $newCategories,
								"data_list"			=> $cat_id_list,
								"module"			=> $this->webContainer,
								"identifier"		=> $identifier,
								"information_list"	=> $list_id
							)
						);
						$num_of_entries = count($cat_id_list);
			//			print "[$newCategories]";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::newCategories",__LINE__, $newCategories));}

						if($newCategories!=""){
							if(strpos("\n",$newCategories)===false){
								$newCategoriesList =Array();
								$newCategoriesList[0] = $newCategories;
							} else {
								$newCategoriesList = split("\n",$newCategories);
							}
							if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::newCategoriesList",__LINE__,print_r($newCategoriesList, true)));}
							$mz = count($newCategoriesList);
							for($i=0;$i<$num_of_entries;$i++){
								if (substr($cat_id_list[$i],0,3)=="new"){
									for($z=0;$z<$mz;$z++){
										$test = split("::",$newCategoriesList[$z]);
										if ($test[1]==$cat_id_list[$i]){
											$entry= split("::",$newCategoriesList[$z]);
										}
									}
									if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::entry",__LINE__,print_r($entry, true)));}
									$this->rename_or_move(
										Array(
											"cmd" 				=> "new",
											"info_identifier" 	=> $list_id,
											"cat_id" 			=> $clist[0][$i],
											"cat_parent" 		=> $entry[0],
											"cat_label" 		=> $entry[2],
											"list_id"			=> $clist[1]
										)
									);
								}
							}
						}
			
/********** Insertion into category_to_object for Import Database portion Ends ( Added By Muhammad Imran Mirza )***********/

			
						}
						$ie_title 	= "";
						$ie_summary	= "";
						for($findex  = 0; $findex<$fmax;$findex++){
							$field_index = $fieldindex[$findex];
							if ($importindex[$findex] == "ie_title")
								$ie_title 	= $this->validate(trim($this->check_parameters($fields, $field_index, "")));
							if ($importindex[$findex] == "ie_summary")
								$ie_summary	= $this->validate(trim($this->check_parameters($fields, $field_index, "")));
						}
						$md_description =""; // description for this import 
						$md_title 		=""; // title for metadata
						for($findex  = 0; $findex<$fmax;$findex++){
							if ($delimiter!=""){
								$field_index = str_replace(Array(chr($delimiter)), Array(""), $fieldindex[$findex]);
								$value 		= $this->validate(str_replace(Array(chr($delimiter),"  "), Array(""," "), trim($this->check_parameters($fields, $field_index, ""))));
							}else {
								$field_index = $fieldindex[$findex];
								$value 		= $this->validate(str_replace(Array("  "), Array(" "), trim($this->check_parameters($fields, $field_index, ""))));
							}
							$field_name = $this->check_parameters($importindex,$findex,"");
							if($field_name!='category'){
								if($value != "" && $importable==1){
									if (substr($field_name,0,8) == "ie_image"){
										if (file_exists($uploadpath."/".$value)){
											$value = $this->call_command("FILES_IMPORT_IMAGE_FROM_DIRECTORY_IMPORT", 
												Array(
													"path" 			=> $uploadpath."/",
													"source"		=> $value,
													"label"			=> $ie_title,
													"description"	=> $ie_summary,
													"uppath"		=> $upload_dir_path,
													"upid"			=> $upload_dir_id
												)
											);
										}
									}
									$mdfind  = $this->check_parameters($md, $field_name, "__NOT_FOUND__");
									if (($mdfind == "__NOT_FOUND__") || ($mdfind == "")){
										$iev_identifier = $this->getUid(); 
										$sql 	= 	"insert into information_entry_values (iev_identifier, iev_client, iev_entry, iev_field, iev_value, iev_list) 
														values 
													($iev_identifier, $this->client_identifier, $entry, '$field_name', '$value', $import_into_directory)";
										$result  = $this->parent->db_pointer->database_query($sql);
									} else {
										if ($field_name=="ie_vat"){
											$md_fields[$mdfind] = $this->check_truth($value);
										} else {
											$md_fields[$mdfind] = $value;
										}
										if ($field_name=="ie_summary"){
											$longDescription = $value." ".$longDescription;
										} else if ($field_name=="ie_title"){
											$longDescription = $value." ".$longDescription;
											$md_title 		= $value;
										} else if ($field_name=="ie_description"){
											$longDescription = $value." ".$longDescription;
										} else {
											$longDescription = $longDescription." ".$value;
										}
									}
									
								}
							} else {
								if ($importable==1 || $merged==1){
									if($value!=""){
										/*************************************************************************************************************************
	                                    * a category label might hold "cat 1.0/cat 1.1/cat 1.1.1"
										* the split_categories should contian "/"
										* the following command will split on the "/" character and produce a three level deep category index.
	                                    *************************************************************************************************************************/
										$this->call_command("CATEGORYADMIN_TO_OBJECT_IMPORT_PATH", 
											Array(
												"label"				=> $value,
												"module"			=> $this->webContainer,
												"identifier"		=> ($all_dup_checks_match>0)?$all_dup_checks_match:$entry,
												"category_root"		=> $category_root,
												"split_categories"	=> $split_categories
											)
										);
									}
								}
							}
						}
						/**
                        * add metadata for this record
                        */
						$this->call_command("METADATAADMIN_MODIFY", Array("identifier"=>$entry, "module"=> $this->webContainer, "fields" => $md_fields, "command"=>"ADD", "longDescription" => $longDescription));
					}
				}
//				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::end of import",__LINE__,"importpage ($importpage * 50) >= $m"));}
				if ($importpage*50 >= $m){
					$_SESSION["StartProcess"] = $this->getmicrotime();
					$sql = "SELECT distinct iev_field, iev_value, io_value, io_field FROM information_entry_values 
						left outer join information_options on io_field = iev_field and io_value = iev_value and io_list = iev_list and io_client = iev_client
						where iev_client=$this->client_identifier and iev_list = $import_into_directory and (iev_field  like 'ie_oradio%' or iev_field  like 'ie_oselect%' or iev_field  like 'ie_ocheckbox%' or iev_field  like 'ie_olist%')
						order by io_rank, iev_field ";
					$result  = $this->parent->db_pointer->database_query($sql);
					$prev_field ="";
        	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
    	            	$ok  = $this->check_parameters($r, "io_value", "__NOT_FOUND__");
						if ($ok == "__NOT_FOUND__"){
							if ($prev_field != $r["iev_field"]){
								$rank=0;
								$prev_field = $r["iev_field"];
							}
							$io_identifier = $this->getUid();
							$sql = "insert into information_options (io_identifier, io_client, io_field, io_value,io_list, io_rank)
										values
									($io_identifier, $this->client_identifier, '".$r["iev_field"]."', '".$r["iev_value"]."', '".$import_into_directory."', $rank)";
							if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
							$this->parent->db_pointer->database_query($sql);
							$rank++;
						}
        	        }
    	            $this->parent->db_pointer->database_free_result($result);
					// re generate category list.
					$_SESSION["StartCatCache"] = $this->getmicrotime();
					$this->call_command("CATEGORYADMIN_LOAD", Array("identifier"=>$category_root, "returntype"=>-1,"recache"=>1));
					$this->rebuild_search_cache(Array("info_identifier"=>$import_into_directory));
					@unlink($path);
				} else {
					$this->call_command("ENGINE_REFRESH_BUFFER", 
						Array("command=".$this->module_command."EXAMINE_IMPORT_FILE&amp;page=2&amp;importpage=".($importpage+1).
							  "&amp;import_into_directory=$import_into_directory")
					);
					
				}
			}
			$_SESSION["fieldindex"]			= "";
			$_SESSION["import_status"]		= "";
			$_SESSION["importindex"]		= "";
			$_SESSION["category_root"]		= "";
			$_SESSION["delimiter"]			= "";
			$_SESSION["split_categories"]	= "";
			$_SESSION["REDIRECT_AFTER"]		= $this->module_command."IMPORT_CONFIRM";
			$_SESSION["list_id"]			= $import_into_directory;
			$sql = "update information_entry set ie_version_minor=1, ie_version_wip=1, ie_parent = ie_identifier where ie_parent=0";
			$this->parent->db_pointer->database_query($sql);
			$_SESSION["StartRestore"] = $this->getmicrotime();
			$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."RESTORE&amp;list=$import_into_directory&refresh=1&show_import_confirm=1"));
		}
	}
	
	function remove_all_entries($parameters){
		$identifier  = $this->check_parameters($parameters,"identifier");
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options><header><![CDATA[Remove all Entries from this directory]]></header>".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL))."</page_options>";
		$out .="	<form name=\"process_form\" label=\"Import Directory\" width=\"100%\">";
		$out .="		<input type='hidden' name='command' value='INFORMATIONADMIN_REMOVE_ALL_ENTRIES_CONFIRMED'/>";
		$out .="		<input type='hidden' name='identifier' value='$identifier'/>";
		$out .="		<page_sections>";
		$out .="			<section label='Empty Information Directory'>";
		$out .="				<text><![CDATA[You have requested to delete all of the entries in the information directory.]]></text>";
		$out .="				<radio name='keep_existing_categories' label='Keep the existing Category list???'>
									<option value='yes'><![CDATA[".LOCALE_YES."]]></option>
									<option value='no' selected='true'><![CDATA[".LOCALE_NO."]]></option>
								</radio>";
		$out .="				<radio name='keep_existing_options' label='Keep any option lists that are defined'>
									<option value='yes'><![CDATA[".LOCALE_YES."]]></option>
									<option value='no' selected='true'><![CDATA[".LOCALE_NO."]]></option>
								</radio>";
		$out .="			</section>";
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}

	function remove_all_entries_confirmed($parameters){
		$identifier  				= $this->check_parameters($parameters,"identifier");
		$keep_existing_categories	= $this->check_parameters($parameters,"keep_existing_categories","yes");
		$keep_existing_options		= $this->check_parameters($parameters,"keep_existing_options","yes");
		$info_category				= -1;
		/*************************************************************************************************************************
		* step 1 - get the Category List identifier
		*************************************************************************************************************************/
		$sql = "select information_list.*, menu_url from information_list 
		inner join menu_data on menu_identifier = info_menu_location and menu_client = info_client
		where info_identifier = $identifier and info_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$info_category		= $r["info_category"];
			$menu_url			= $r["menu_url"];
			$info_in_menu		= $r["info_in_menu"];		
			$info_menu_location	= $r["info_menu_location"];
		}
		$this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
		* step 2 - remove entries cache information
		*************************************************************************************************************************/
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($handle = @opendir($data_files)) {
			while (($file = readdir($handle)) !== false) {
				if ($file == "." || $file == "..") {
					continue;
				}
				$pattern = "information_presentation_".$this->client_identifier."_".$identifier."_";
				if(substr($file,0,strlen($pattern)) == $pattern){
					@chmod($data_files."/".$file, LS__FILE_PERMISSION);
					@unlink($data_files."/".$file); // remove this file
				}
			}
		}
		@closedir($handle);
		
		/*************************************************************************************************************************
		* step 3 - delete the directory structure
		*************************************************************************************************************************/
		$sql = "select * from category where cat_parent = cat_list_id and cat_list_id = $info_category";
		$result  = $this->parent->db_pointer->database_query($sql);
		$root  = $this->check_parameters($this->parent->site_directories,"ROOT")."/";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$path = dirname($menu_url)."/".$this->make_uri($r["cat_label"]);
			$this->terminate_directory($path);
			$um =umask(0);
			@chmod($root.$path, LS__FILE_PERMISSION);
			umask($um);
			@rmdir($root.$path);
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
		* step 4 - delete entries 
		*
		* 4.1 removed metadata
		*************************************************************************************************************************/
		$sql = "select ie_identifier,ie_parent, md_identifier from information_entry 
					inner join metadata_details on md_link_id = ie_identifier and md_module= '$this->webContainer' and md_client= ie_client
				where ie_client=$this->client_identifier and ie_list = $identifier";
        $result  = $this->parent->db_pointer->database_query($sql);
		$metadata_links = "";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if ($metadata_links!=""){
				$metadata_links .= ",";
			}
			$metadata_links .= $r["md_identifier"];
        }
        $this->parent->db_pointer->database_free_result($result);
		if ($metadata_links!=""){
			$sql = "delete from metadata_details where md_client = $this->client_identifier and md_identifier in ($metadata_links)";
			$this->parent->db_pointer->database_query($sql);
			$sql = "delete from metadata_keyword_relationship where mdkr_client = $this->client_identifier and mdkr_identifier in ($metadata_links)";
			$this->parent->db_pointer->database_query($sql);
			$sql = "delete from metadata_properties where mdp_client = $this->client_identifier and mdp_identifier in ($metadata_links)";
			$this->parent->db_pointer->database_query($sql);
		}
		/**
        * remove entries
        */
		$sql = "delete from information_entry where ie_client=$this->client_identifier and ie_list = $identifier";
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from information_entry_values where iev_client=$this->client_identifier and iev_list = $identifier";
		$this->parent->db_pointer->database_query($sql);
		/**
        * remove the option list for fields if required
        */
		if($keep_existing_options=="no"){
			$sql = "delete from information_options where io_client=$this->client_identifier and io_list = $identifier";
			$this->parent->db_pointer->database_query($sql);
		}
		/*************************************************************************************************************************
		* step 5 - either delete the categories or rebuild 
		*************************************************************************************************************************/
		if($keep_existing_categories=="no"){
			$sql = "select * from category where cat_parent !=-1 and cat_list_id = $info_category and cat_client = $this->client_identifier";
			$rmlist = "";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				if($rmlist!=""){
					$rmlist.=", ";
				}
            	$rmlist .= $r["cat_identifier"];
            }
            $this->parent->db_pointer->database_free_result($result);
			$sql = "delete from category where cat_parent != -1 and cat_list_id = $info_category and cat_client = $this->client_identifier";
			$this->parent->db_pointer->database_query($sql);
			$sql = "delete from category_to_object where cto_list = in ($rmlist) and cto_client = $this->client_identifier and cto_module='$this->webContainer'";
			$this->parent->db_pointer->database_query($sql);
			@unlink($data_files."/category_".$this->client_identifier."_".$info_category.".xml"); // remove this file
			@unlink($data_files."/category_".$this->client_identifier."_".$info_category."_*.xml"); // remove the cached category files
			/** If directory has CATEGORY IN MENU OPTION then delete menu file */
			if ($info_in_menu == 1){
				$this->call_command("CATEGORYADMIN_CACHE_MENU",Array("cat_root" => $info_category,"menu_identifier"=>$info_menu_location, "info_in_menu" => 0));						
			}
		} else {
			$sql = "select * from category where cat_parent !=-1 and cat_list_id = $info_category and cat_client = $this->client_identifier";
			$rmlist = "";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				if($rmlist!=""){
					$rmlist.=", ";
				}
            	$rmlist .= $r["cat_identifier"];
            }
            $this->parent->db_pointer->database_free_result($result);
			$sql = "delete from category_to_object where cto_list = in ($rmlist) and cto_client = $this->client_identifier and cto_module='$this->webContainer'";
			$this->parent->db_pointer->database_query($sql);
			$this->restore(Array("list"=>$identifier));
			//@unlink($data_files."/category_".$this->client_identifier."_".$info_category.".xml"); // remove this file
		}

		$this->call_command("CATEGORYADMIN_TO_OBJECT_TIDY");
	}
	/*************************************************************************************************************************
    * terminate_directory([String] Dir, [int] safe)
    *************************************************************************************************************************/
	function terminate_directory($Dir,$safe=0){
		$root  = $this->check_parameters($this->parent->site_directories,"ROOT")."/";
			if ($handle = @opendir($root.$Dir)) {
				while (($file = readdir($handle)) !== false) {
					if ($file == "." || $file == "..") {
						continue;
					}
					if (is_dir($root.$Dir."/".$file)){
						// call self for this directory
						$this->terminate_directory($Dir."/".$file."/",1);
						$um =umask(0);
						@chmod($root.$Dir."/".$file, LS__FILE_PERMISSION);
						umask($um);
						@rmdir($root.$Dir."/".$file); //remove this directory
					} else {
						$um =umask(0);
						@chmod($root.$Dir."/".$file, LS__FILE_PERMISSION);
						umask($um);
						@unlink($root.$Dir."/".$file); // remove this file
					}
				}
			}
			@closedir($handle);
	}
	/*************************************************************************************************************************
	*	export a information directoy
	*************************************************************************************************************************/
	function export($parameters){
		$dir = $this->check_parameters($parameters,"identifier",-1);
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		//$out .= "<page_options><header><![CDATA[".LOCALE_INFO_DIR_EXPORT_HEADER."]]></header></page_options>";
		$out .= "<page_options>
					<header><![CDATA[".LOCALE_INFO_DIR_EXPORT_HEADER."]]></header>";
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"Import Directory\" width=\"100%\">";
		$out .="		<input type='hidden' name='command' value='".$this->module_command."EXPORT_CONFIRMED'/>";
		$out .="		<page_sections>";
		$out .="			<section label='Export Information Directory'>";
		$out .="				<text><![CDATA[You have requested to export the entries in the information directory.]]></text>";
		$out .="				<input type='hidden' name='directory_id' value='$dir'/>";
/*
		$out .="				<select name='directory_id' label='Which directory do you wish to export'>";
		$directory_options="";
		$sql= "select * from information_list where info_owner ='$this->webContainer' and info_client=$this->client_identifier";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$directory_options .= "<option value='" . $r["info_identifier"] . "'";
        	if ($dir == $r["info_identifier"]){
        		$directory_options .= " selected='true'";
        	}
        	$directory_options .= ">" . $r["info_label"] . "</option>";
        }
        $this->parent->db_pointer->database_free_result($result);
		
		$out .="					$directory_options";
		$out .="				</select>";
*/
		$out .="				<input type='text' name='split_categories' size='5' label='Categories may appear as a path structure what character should the category path be split on.'><![CDATA[/]]></input>";
		$out .="				<input type='hidden' name='export_format' value='csv_delimiter'/>";
/*		$out .="				<radio name='export_format' label='What format do you wish to export in'>
									<option value='tab_delimiter' selected='true'><![CDATA[Tab Seperated list (*.txt) with delimiter (flat structure) &#134;]]></option>
									<option value='tab'><![CDATA[Tab Seperated list (*.txt) without delimiter (flat structure) &#134;]]></option>
									<option value='csv_delimiter'><![CDATA[Comman Seperated (*.csv) with delimiter (flat structure) &#134;]]></option>
									<option value='csv'><![CDATA[Comman Seperated (*.csv) without delimiter (flat structure) &#134;]]></option>
								</radio>";
//									<option value='xml'><![CDATA[XML (*.xml)]]></option>
*/
//		$out .="				<text><![CDATA[&#134; <em>a delimiter is required by some programs to import this format of data correctly. The delimiter used is double quotes <strong>[[quot]]</strong></em>]]></text>";
		/*************************************************************************************************************************
        * get list of fields
        *************************************************************************************************************************/
		$vals = Array();
		$labs = Array();
		$sql ="select * from information_fields where if_screen=0 and if_list= $dir and if_client = $this->client_identifier order by if_rank";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$vals[count($vals)] = $r["if_name"];
			$labs[count($labs)] = $r["if_label"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$_SESSION["EXPORT_FIELDS"] = "";
		// auto select all fields
		$out .="				<checkboxes name='export_fields' label='What fields do you wish to export'>".$this->gen_options($vals,$labs, $vals)."</checkboxes>";
		$out .="			</section>";
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"NEXT\" value=\"".LOCALE_EXPORT."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * export data request form
    *************************************************************************************************************************/
	function export_data($parameters){
		$_SESSION["EXPORT_FIELDS"]	= $this->check_parameters($parameters,"export_fields"		, Array());
		$directory_id				= $this->check_parameters($parameters,"directory_id"		, -1);
		$export_format				= $this->check_parameters($parameters,"export_format"		, "tab");
		$split_categories			= $this->check_parameters($parameters,"split_categories"	, "/");
		
		$out  = "<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "	<page_options>
					<header><![CDATA[".LOCALE_INFO_DIR_EXPORT_PROGRESS_HEADER."]]></header>";
//		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."EXPORT",LOCALE_CANCEL));
		$out .= "	</page_options>";
		$out .= "	<form name=\"process_form\" label=\"Import Directory\" width=\"100%\">";
		$out .= "		<input type='hidden' name='command' value='INFORMATIONADMIN_EXPORT_CONFIRMED'/>";
		$out .= "		<page_sections>";
		$out .= "			<section label='Export Processing'>";
//		$out .= "				<text><![CDATA[You have requested to export the entries in the information directory.]]></text>";
		$out .= "				<hiddenframe id='export_database' src='admin/index.php?command=".$this->module_command."EXPORT_DATA_HIDDEN&amp;directory_id=$directory_id&amp;export_format=$export_format&amp;split_categories=$split_categories'></hiddenframe>";
		$out .= "			</section>";
		$out .= "		</page_sections>";
//		$out .= "		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .= "	</form>";
		$out .= "</module>";
		return $out;
	}

	function export_data_hidden($parameters){
		$list_id			= $this->check_parameters($parameters, "directory_id"		, -1);
		$export_format		= $this->check_parameters($parameters, "export_format"		, "tab");
		$current_page		= $this->check_parameters($parameters, "npage"				, 0);
		$split_categories	= $this->check_parameters($parameters, "split_categories"	, "/");
		$info_cat			=-1;
		$page_size 			= 1000; //records at a time
		// hunting options is an extra sql option not necessary if there are no multi choice options in directory
		$hunt_down_options	= 0;
		$row = "";
//		print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameters,true)."</p></li>";
//		$this->exitprogram();
		if(($export_format=="tab_delimiter") || ($export_format=="csv_delimiter")){
			if ($export_format=="tab_delimiter"){
				$export_format="tab";
			}
			if ($export_format=="csv_delimiter"){
				$export_format="csv";
			}
			$delimiter='"';
		} else {
			$delimiter='';
		}
		/*************************************************************************************************************************
        * hard code delimiter and format
        *************************************************************************************************************************/
//			$export_format="csv";
//			$delimiter='"';
		/*************************************************************************************************************************
        * build sql
        *************************************************************************************************************************/
		$list_of_fields = array();
		if ($list_id!=-1){
			$export_fields = $this->check_parameters($_SESSION,"EXPORT_FIELDS"		, Array());
			$fieldlist = join("', '",  $export_fields);
			if($fieldlist==""){
				$sql ="select * from information_fields where if_screen=0 and if_list= $list_id and if_client = $this->client_identifier order by if_rank";
			} else {
				$sql ="select * from information_fields where if_screen=0 and if_list= $list_id and if_client = $this->client_identifier and if_name in ('$fieldlist') order by if_rank";
			}
			$result  = $this->parent->db_pointer->database_query($sql);
			$c=0;
			$join="";
			$fields="";
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				if ($r["if_type"] != "list" && $r["if_type"] != "check"){
					if($fields!=""){
						$fields .= ", \n";
					}
					if($r["if_map"]=="" || $r["if_map"]=="md_description"){
						$fields .= "iev".$c.".iev_value as ".$r["if_name"];
			        	$join .="left outer join information_entry_values as iev".$c." on iev".$c.".iev_field = '".$r["if_name"]."' and iev".$c.".iev_entry = ie_identifier  and iev".$c.".iev_client = $this->client_identifier\n";
					} else {
						$fields .= "metadata_details.".$r["if_map"]." as ".$r["if_name"];
					}
				} else {
					$hunt_down_options = 1;
				}
				$list_of_fields[$c] = Array("name" => $r["if_label"], "field" => $r["if_name"], "type" => $r["if_type"]);
				$c++;
	        }
			$list_of_fields[count($list_of_fields)] = Array("name" => "Categorisation", "field" => "Categorisation", "type" => "categorisation");
	        $this->parent->db_pointer->database_free_result($result);
			$sql = "delete from tmp_rowdata where useridentifier='".$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0)."' and session='".session_id()."'";
			$this->parent->db_pointer->database_query($sql);
			$sql = "insert into tmp_rowdata ( fieldname, counter, useridentifier, session) select if_name, count(iev_field ) as total, '".$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0)."', '".session_id()."' from information_fields inner join information_entry_values on if_name=iev_field and iev_list = if_list and if_client=if_client where if_type in ('check', 'list') and if_client=$this->client_identifier and if_list = $list_id group by iev_entry, iev_field";
			$this->parent->db_pointer->database_query($sql);
			$sql = "select fieldname, max(counter) as total from tmp_rowdata group by fieldname";
			$result  = $this->parent->db_pointer->database_query($sql);
			$field_count = Array();
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$field_count[$r["fieldname"]] = $r["total"];
			}
			$this->parent->db_pointer->database_free_result($result);
			$sql = "delete from tmp_rowdata where useridentifier='".$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0)."' and session='".session_id()."'";
			$this->parent->db_pointer->database_query($sql);
			/*************************************************************************************************************************
            * get the maximum number of categories that any record is mapped to ( minimum 1 )
            *************************************************************************************************************************/
			$sql = "select count(cto_object) as total from category_to_object 
					inner join information_entry on cto_object = ie_identifier and ie_client=cto_client
				where ie_list = $list_id and cto_module='$this->webContainer' and ie_client = $this->client_identifier
					group by cto_object order by total desc limit 1";
			$result  = $this->parent->db_pointer->database_query($sql);
        	while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$field_count["Categorisation"] = $r["total"];
        	}
        	$this->parent->db_pointer->database_free_result($result);
			$sql = "SELECT ie_identifier as record_number, info_category, 
					$fields
				FROM information_entry 
					inner join information_list on info_client = $this->client_identifier and info_identifier = ie_list
					inner join metadata_details on md_client = $this->client_identifier and ie_identifier = md_link_id and md_module='$this->webContainer'
					$join
				where 
					ie_published 	= 1 and 
					ie_list		 	= $list_id and 
					ie_client	 	= $this->client_identifier and 
					ie_version_wip	= 1
					";
			$result  	 = $this->parent->db_pointer->database_query($sql);
			$num_records = $this->call_command("DB_NUM_ROWS",Array($result));
			$num_pages 	 = ($num_records / $page_size);
			$goto=0;
			$m = count($list_of_fields);
			$f_mime="";
			$export_file_format = $export_format;
			if($num_records==0){
				print "<html><script>alert('Sorry there are no records to export');</script><body></body></html>";
				$this->exitprogram();
			} else {
				if($export_format=="tab" || $export_format=="csv"){
					$f_mime = "application/ms-excel";
					if($export_format=="tab"){
						$field_splitter ="\t";
						$export_file_format="txt";
					} else {
						$field_splitter =",";
					}
					$row 	= "";
					if ($current_page==0){
						for ($i=0; $i<$m; $i++){
							if($i!=0){
								$row.=$field_splitter;
							}
							if ($list_of_fields[$i]["type"]=="list" || $list_of_fields[$i]["type"]=="check" || $list_of_fields[$i]["type"]=="categorisation"){
								$counter = $this->check_parameters($field_count,$list_of_fields[$i]["field"],0);
								for ($z=0;$z<$counter;$z++){
									$row.=$delimiter.html_entity_decode($list_of_fields[$i]["name"], ENT_QUOTES).$delimiter;
									if($i!=0 && $z<$counter-1){
										$row.=$field_splitter;
									}
								}
							} else {
								$row.=$delimiter.html_entity_decode($list_of_fields[$i]["name"], ENT_QUOTES).$delimiter;
							}
						}
						$row.="\r\n";
					} else {
						$goto = ($current_page * $page_size);
						$this->call_command("DB_SEEK",array($result,$goto));
					}
					if($goto>=($num_records)){
					}else{
						$c=0;
				        while(($r = $this->parent->db_pointer->database_fetch_array($result)) && $c<$page_size){
							$option_values = Array();
							$info_cat = $r["info_category"];
							if ($hunt_down_options == 1){
								// execute statement once per record
								$sql = "SELECT information_entry_values.iev_field, information_entry_values.iev_value
										FROM `information_entry_values`
										inner join information_fields on iev_field = if_name and if_client=iev_client and if_screen=0
										where if_type in ('check','list') and iev_client=$this->client_identifier and iev_entry=".$r["record_number"]." order by iev_field desc";
								$optionresult  = $this->parent->db_pointer->database_query($sql);
								while($optionr = $this->call_command("DB_FETCH_ARRAY",Array($optionresult))){
									if ($this->check_parameters($option_values,$optionr["iev_field"],"__NOT_FOUND__")=="__NOT_FOUND__"){
										$option_values[$optionr["iev_field"]] = Array();
									}
									$option_values[$optionr["iev_field"]][count($option_values[$optionr["iev_field"]])] = $optionr["iev_value"];
	                           	}
	                            $this->call_command("DB_FREE",Array($optionresult));
							}
							/*************************************************************************************************************************
							*	get category breadcrumbtrails
							*************************************************************************************************************************/
							$option_values["Categorisation"] = $this->call_command("CATEGORYADMIN_GET_BREADCRUMBTRAILS",
								Array(
									"object"			=> $r["record_number"], 
									"list"				=> $list_id,
									"category_list"		=> $info_cat,
									"split_categories"	=> $split_categories
								)
							);
			    	    	/*************************************************************************************************************************
							*	output row 
							*************************************************************************************************************************/
							for ($i=0; $i<$m; $i++){
								if($i!=0){
									$row .= $field_splitter;
								}
								if ($list_of_fields[$i]["type"]!="list" && $list_of_fields[$i]["type"]!="check" && $list_of_fields[$i]["type"]!="categorisation"){
									if($delimiter!=""){
										$row .= $delimiter. trim(strip_tags(str_replace(Array($delimiter),Array(""),html_entity_decode($this->check_parameters($r,$list_of_fields[$i]["field"]), ENT_QUOTES)))).$delimiter;
									} else {
										$row .= trim(strip_tags(html_entity_decode($this->check_parameters($r,$list_of_fields[$i]["field"]), ENT_QUOTES)));
									}
								} else {
	//								print "<li>".$list_of_fields[$i]["type"]."</li>";
									$l = $this->check_parameters($option_values,$list_of_fields[$i]["field"],Array());
									$mc =count($l);
									if ($mc>0){
										for ($z=0;$z < $mc;$z++){
											if($z!=0){
												$row .= $field_splitter;
											}
											if($delimiter!=""){
												$row .= $delimiter . trim(strip_tags(str_replace(Array($delimiter),Array(""),html_entity_decode($l[$z])))) . $delimiter;
											} else {
												$row .= trim(strip_tags(html_entity_decode($l[$z])));
											}
										}
									}
									$counter = $this->check_parameters($field_count,$list_of_fields[$i]["field"]);;
									if ($mc == 0){
										$fin = $counter - 1 ;
									} else {
										$fin = ($counter - $mc);
									}
									for ($z=0; $z<$fin; $z++){
										$row .= $field_splitter;
										$row .= $delimiter.$delimiter;
									}
	//								print "[$mc, $counter, $fin]<br>";
								}
							}
							$row.="\r\n";
							$c++;
				        }
					}
			        $this->parent->db_pointer->database_free_result($result);
					if($export_format=="xml"){
						if ($current_page==0){
							$row = "<directory><field_labels>";
							for ($i=0; $i<$m; $i++){
								$row .= "<field id='".$list_of_fields[$i]["field"]."'><![CDATA[".strip_tags(html_entity_decode($list_of_fields[$i]["name"], ENT_QUOTES))."]]></field>";
							}
							$row .= "</field_labels>\n";
						} else {
							$goto = ($current_page * $page_size);
							$this->call_command("DB_SEEK",array($result,$goto));
						}
						if($goto>=($num_records)){
							$row .= "</directory>";
						}else{
							$c=0;
					        while(($r = $this->parent->db_pointer->database_fetch_array($result)) && $c<$page_size){
								$row .= "<field_record number='".$r["record_number"]."'>";
			    		    	for ($i=0; $i<$m; $i++){
									$row .= "<field id='".$list_of_fields[$i]["field"]."'><![CDATA[".strip_tags(html_entity_decode(html_entity_decode($this->check_parameters($r,$list_of_fields[$i]["field"]), ENT_QUOTES)))."]]></field>";
								}
								$row .= "</field_record>\n";
								$c++;
				    	    }
						}
			        	$this->parent->db_pointer->database_free_result($result);
//						$f_mime = "text/xml";
					}
					$path  		= $this->check_parameters($this->parent->site_directories,"DATA_FILES_DIR");
					$filename	= $path."/export_".$this->client_identifier."_".$_SESSION["SESSION_USER_IDENTIFIER"].".txt";
//					print "[$export_file_format]";
//					print "[$goto >= $num_records]";
					if ($goto>=$num_records){
						if($row!=""){
							if (!file_exists($filename)){
								$fhandle = fopen($filename,"w");
							} else {
								$fhandle = fopen($filename,"a");
							}
							fwrite($fhandle, $row);
							fclose($fhandle);
							$um = umask(0);
							@chmod($filename, LS__FILE_PERMISSION);
							umask($um);
						}
						if (file_exists($filename)){
							$loc = "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=".$this->module_command."EXPORT_DOWNLOAD&amp;export_format=$export_format&amp;split_categories=$split_categories";
							print "
								<html><head>
								<script>
									window.parent.statusBarUpdate(100);
									window.parent.location ='$loc';
								</script>
								</head>
								<body></body></html>
							";
						} else {
							print "
								<html><head>
								<script>
									alert('Sorry there was a problem with exporting the information');
								</script>
								</head>
									<body> $c </body></html>
								";
						}
						$this->exitprogram();
					} else {
						if ($goto==0){
							$fhandle = fopen($filename,"w");
						} else {
							$fhandle = fopen($filename,"a");
						}
						fwrite($fhandle, $row);
						fclose($fhandle);
						$um = umask(0);
						@chmod($filename, LS__FILE_PERMISSION);
						umask($um);
						$current_page++;
						if($export_format=="txt"){
							$export_format="tab";
						}
						if ($delimiter!=""){
							$loc = "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=".$this->module_command."EXPORT_DATA_HIDDEN&npage=$current_page&directory_id=$list_id&export_format=".$export_format."_delimiter";
						} else {
							$loc = "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=".$this->module_command."EXPORT_DATA_HIDDEN&npage=$current_page&directory_id=$list_id&export_format=$export_format";
						}
						print "
							<html><head>
							<script>
								window.parent.statusBarUpdate(".(100 * round($current_page / $num_pages ,2)).");
								window.location ='$loc';
							</script>
							</head>
							<body>$current_page</body></html>
						";
						$this->exitprogram();
					}
				}
			}
		}
	}

	function export_download($parameters){
		$path  		= $this->check_parameters($this->parent->site_directories,"DATA_FILES_DIR");
		$filename	= $path."/export_".$this->client_identifier."_".$_SESSION["SESSION_USER_IDENTIFIER"].".txt";
		$export_format = $this->check_parameters($parameters,"export_format");
		if($export_format=="csv"){
			$f_mime ="application/ms-excel";
		}
		if($export_format=="tab" || $export_format=="txt"){
			$export_format="txt";
			$f_mime ="text/plain";
		}
		if($export_format=="xml"){
			$export_format="xml";
			$f_mime ="text/xml";
		}
		header("Pragma: public");
		header("Expires: 0"); // set expiration time
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		//header("Content-Type: $f_mime");
//		header("Content-Type: force/download");
		// use the Content-Disposition header to supply a recommended filename and 
		// force the browser to display the save dialog. 
		header("Content-Disposition: filename=export.$export_format;");
		/*
		The Content-transfer-encoding header should be binary, since the file will be read 
		directly from the disk and the raw bytes passed to the downloading computer.
		The Content-length header is useful to set for downloads. The browser will be able to 
		show a progress meter as a file downloads. The content-length can be determines by 
		filesize function returns the size of a file. 
		*************************************************************************************************************************/
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($filename));
		$fp = fopen("$filename","rb");
		fpassthru($fp); 
		fclose($fp);
		@unlink("$filename");
	}
	
	/*************************************************************************************************************************
	* make a copy of an entry
	*
	* @param Integer identifier to copy
	* @param Integer information directory it belongs to
	* @param Integer parent identifier it belongs to 
	* @param Integer status
	*
	* @return Integer the new UID
	*************************************************************************************************************************/
	function copy_entry($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",		-1);
		$list_id	= $this->check_parameters($parameters, "list_id", 		-1);
		$parent_id	= $this->check_parameters($parameters, "parent_id", 	0);
		$ie_status	= $this->check_parameters($parameters, "ie_status", 	-1);
		$new_id=-1;
		if ($parent_id>0){
			$sql="select * from information_entry
					where ie_parent=$parent_id and ie_client=$this->client_identifier and ie_list = $list_id
					order by ie_version_major desc,  ie_version_minor desc";
			
			$presult  = $this->parent->db_pointer->database_query($sql);
	       	$major = 0;
			$minor = 1;
	        if($this->call_command("DB_NUM_ROWS",Array($presult))>0){
				$r = $this->call_command("DB_FETCH_ARRAY",Array($presult));
	        	$major = $r["ie_version_major"];
				$minor = $r["ie_version_minor"] + 1;
	        }
	        $this->call_command("DB_FREE",Array($presult));
			$sql		= "select * from information_entry where information_entry.ie_identifier = $identifier and ie_client=$this->client_identifier";
			$result 	= $this->parent->db_pointer->database_query($sql);
			$now 		= $this->libertasGetDate("Y/m/d H:i:s");
			while($r	= $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$new_id = $this->getUid();
				$insertsql 	= "insert into information_entry 
					(ie_identifier, ie_client, ie_list, ie_status, ie_uri, ie_date_created, ie_user, ie_published, ie_version_major, ie_version_minor, ie_version_wip, ie_parent)
					values 
					($new_id, $this->client_identifier, ".$r["ie_list"].", ".$r["ie_status"].", '".$r["ie_uri"]."', '".$now."', ".$r["ie_user"].", ".$r["ie_published"].", ".$major.", ".$minor.", 1, ".$r["ie_parent"].")";
					$this->parent->db_pointer->database_query($insertsql);
				$this->call_command("METADATAADMIN_CLONE", Array(
						"source"		=> $identifier,
						"module"		=> $this->webContainer,
						"destination"	=> $new_id
					)
				);
			}
			$this->parent->db_pointer->database_free_result($result);
			$sql ="update information_entry set ie_version_wip=0 where ie_identifier = $identifier and ie_client = $this->client_identifier";
			$this->parent->db_pointer->database_query($sql);
			// select All other tables and duplicate entry
			$sql = "select * from information_entry_values where iev_entry = $identifier and iev_client = $this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$iev_identifier = $this->getUid();
	           	$sql ="insert into information_entry_values
					(iev_identifier, iev_client, iev_entry, iev_field, iev_value, iev_list)
					values 
					($iev_identifier, $this->client_identifier, $new_id, '".$r["iev_field"]."','".$r["iev_value"]."', ".$r["iev_list"].")
				";
				$this->parent->db_pointer->database_query($sql);
	        }
	        $this->parent->db_pointer->database_free_result($result);
			$sql = "select * from category_to_object where cto_object = $identifier and cto_client = $this->client_identifier and cto_module = $this->webContainer";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$sql ="insert into category_to_object
					(cto_client, cto_object, cto_module, cto_clist)
					values 
					($this->client_identifier, $new_id, '".$this->webContainer."', ".$r["cto_clist"].")
				";
				$this->parent->db_pointer->database_query($sql);
           	}
       	    $this->parent->db_pointer->database_free_result($result);
		}
		return $new_id;
	}
	
	/*************************************************************************************************************************
    * show the history of a record in the directory
	*
	* @param Integer identifier to copy
	* @param Integer parent identifier it belongs to 
	* @param Integer information directory it belongs to
	* @param Integer status filter (-1 = default no filter)
	*
	* @return String XML structure that represents a page in the life of this entry
    *************************************************************************************************************************/
	function history($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$parent = $this->check_parameters($parameters,"parent_id");
		$list_id	= $this->check_parameters($parameters, "list_id", 		-1);
		$status_filter	= $this->check_parameters($parameters,"status_filter",-1);
		$status_sql = "";
		if ($status_filter!=-1){
			$status_sql = "ie_status = $status_filter and ";
		} 
		$sql = "select ie_identifier, ie_status, iev_value, ie_version_minor, ie_version_major from information_entry 
					left outer join information_entry_values on iev_field='ie_title' and iev_entry = ie_identifier and ie_client = iev_client and iev_list = ie_list
				where $status_sql ie_client = $this->client_identifier and ie_list = $list_id and ie_parent=$parent order by ie_version_major desc,  ie_version_minor desc,  ie_status asc, ie_identifier desc";
		$out = "";
		$result  = $this->parent->db_pointer->database_query($sql);
		$variables = Array();
		$variables["FILTER"]			= $this->filter_entries($parameters,$this->module_command."LIST_ENTRIES");
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$prev = $this->page_size;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$this->call_command("DB_SEEK",array($result,$goto));
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
			
			$variables["PAGE_BUTTONS"] = Array();
				$variables["PAGE_BUTTONS"][0] = Array("CANCEL",$this->module_command."LIST", "Cancel");
				$variables["PAGE_BUTTONS"][1] = Array("TIDYUP", $this->module_command."TIDYUP_HISTORY&amp;list_id=$list_id&amp;parent_id=$parent","Tidy up");
				$variables["PAGE_BUTTONS"][2] = Array("ADD",$this->module_command."ADD&amp;list_id=".$identifier, ADD_NEW);
			if ($this->manage_database_list == 1){
			}
			
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			//			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
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
			$counter=0;
			$page_discussion=0;
			$trans_page=0;
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				if ($this->check_parameters($r,"ie_status","0")=="0"){
					$ie_status = "Requires Approval";
				} else {
					$ie_status = "Approved";
				}
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["ie_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		$this->check_parameters($r,"iev_value",""),"TITLE"),
						Array("Status",	$ie_status,"YES"),
						Array("Version",$r["ie_version_major"].".".$r["ie_version_minor"],"YES")
					)
				);
				if ($this->author_admin_access){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("PREVIEW",$this->module_command."PREVIEW_HISTORY&amp;list_id=$list_id","Preview");
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("COPY",$this->module_command."COPY_HISTORY&amp;list_id=$list_id&amp;parent_id=$parent","Copy");
				}
			}
			$this->page_size = $prev;
			return $this->generate_list($variables);
		}
	}

	function information_preview_history($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$list_id	 		= $this->check_parameters($parameters,"list_id",-1);
//		$command 			= $this->check_parameters($parameters,"command","");
		$form_label 		= LOCALE_ADD;
		$cat_list			= "";
		$status				= 0;
		$current_rank 		= 1;
		$info_category		= -1;
		$associated_file_ids= "";

		if ($identifier!=-1){
			$form_label 	= LOCALE_EDIT;

			$sql="select * from information_fields 
					left outer join information_entry_values on (iev_entry =$identifier and iev_field = if_name and if_list= iev_list and if_client=iev_client) 
					left outer join information_entry on (iev_entry = ie_identifier and ie_client = iev_client) 
					inner join information_list on (info_identifier = if_list)
				  where 
					if_client=$this->client_identifier and
					if_screen=0 and 
					if_list = $list_id
					order by if_rank";
//			print $sql;
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			
			$result  = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$status = $r["ie_status"];
				$info_category = $r["info_category"];
				$ie_list = $r["ie_list"];
				if ($this->check_parameters($this->fields,$r["if_name"],"__NOT_FOUND__")=="__NOT_FOUND__"){
					$this->fields[$r["if_name"]] = array();
					$this->fields[$r["if_name"]][0] = $r["if_label"];
					$this->fields[$r["if_name"]][1] = $current_rank;
					$this->fields[$r["if_name"]][3] = 1;
					$this->fields[$r["if_name"]][4] = $r["if_type"];
					$this->fields[$r["if_name"]]["value"] = "";
					$this->fields[$r["if_name"]]["associated_file_ids"]="";
					$this->fields[$r["if_name"]]["set"] = 0;
					if($r["if_name"]=="ie_image"){
						$this->fields[$r["if_name"]]["specified"] = Array("thumb"=>-1,"main"=>-1);
					} else {
						$this->fields[$r["if_name"]]["specified"] = Array();
					}
				}
				$txt = $r["iev_value"];
				if($r["if_name"]=="ie_image"){
					if(strpos($r["iev_value"],"::")===false){
					} else {
						$l = split("::",$r["iev_value"]);
						$this->fields[$r["if_name"]]["specified"] = Array(
							"thumb"=>$l[0],
							"main"=>$l[1]);
					}
				} else {
					$this->fields[$r["if_name"]]["specified"][count($this->fields[$r["if_name"]]["specified"])]= $txt;
				}
			}
			foreach($this->fields as $key => $list){
				if (($list[4] == "URL")){
					$this->fields[$key]["value"] .= "<ul>";
					foreach($this->fields[$key]["specified"] as $k => $v){
						$this->fields[$key]["value"] .= "<li>$v</li>";
					}
					$this->fields[$key]["value"] .= "</ul>";
				}
				if (($list[4] == "radio") || ($list[4] == "select") || ($list[4] == "list") || ($list[4] == "check")){
					$this->fields[$key]["value"] .= "<ul>";
					foreach($this->fields[$key]["specified"] as $k => $v){
						$this->fields[$key]["value"] .= "<li>$v</li>";
					}
					$this->fields[$key]["value"] .= "</ul>";
				}
				if (($list[4] == "associations")){
					$myfiles = $this->call_command("FILES_LIST_ITEMS", Array("list" => $list["specified"],"type"=>"associate"));
				 	$this->fields[$key]["value"] .= $myfiles[0];
				 	$this->fields[$key]["associated_file_ids"] .= $myfiles[1];
				}
				if ($list[4] == "associated_entries"){
					$sql = "
SELECT * FROM information_entry_relations
inner join information_entry on ((ie_identifier = ier_source_id and ier_source_id !=$identifier) or (ie_identifier = ier_dest_id  and ier_dest_id  !=$identifier)) and ie_client = ier_client
inner join information_entry_values on iev_list = ie_list and iev_field = 'ie_title' and iev_entry = ie_identifier
 where (ier_source_id  = $identifier or ier_dest_id  = $identifier) and ier_client = $this->client_identifier and ie_list = $list_id";
					
					$option_result = $this->parent->db_pointer->database_query($sql);
					$current_rank =1;
					while ($option_r = $this->parent->db_pointer->database_fetch_array($option_result)){
						$this->fields[$key]["value"] .= "
						<entry id='".$option_r["ier_identifier"]."' src_id='".$option_r["ier_source_id"]."' src_cat='".$option_r["ier_source_cat"]."' dst_id='".$option_r["ier_dest_id"]."' dst_cat='".$option_r["ier_dest_cat"]."'><title><![CDATA[".$option_r["iev_value"]."]]></title></entry>";
					}
				}
				if (($list[4] == "imageembed")){
					$myfiles = $this->call_command("FILES_LIST_ITEMS", Array("list" => Array($list["specified"]["thumb"]),"type"=>"associate"));

					
					$this->fields[$key]["value"]					= Array();
					$this->fields[$key]["associated_file_ids"]		= Array();
					
				 	$this->fields[$key]["value"][0] 				= $myfiles[0];
				 	$this->fields[$key]["associated_file_ids"][0]	= $myfiles[1];
					$myfiles = $this->call_command("FILES_LIST_ITEMS", Array("list" => Array($list["specified"]["main"]),"type"=>"associate"));
				 	$this->fields[$key]["value"][1] 				= $myfiles[0];
				 	$this->fields[$key]["associated_file_ids"][1] 	= $myfiles[1];
				}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name, "list", __LINE__, "".$list[4]." ".print_r($list["specified"],true)." ".$this->fields[$key]["value"].""));}
			}
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"this->fields",__LINE__,"".print_r($this->fields, true).""));}
				$cat_list = $this->call_command("CATEGORYADMIN_TO_OBJECT_LIST", 
					Array(
						"module"		=>	$this->webContainer,
						"identifier"	=>	$identifier,
						"returntype"	=>	1
					)
				);
		} else {
		}
		$cat_parent = $list_id;

		$category_listing = $this->call_command("CATEGORYADMIN_LOAD",Array("identifier"=>$info_category));
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Category Manager - $form_label]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST_ENTRIES&amp;identifier=$list_id",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."SAVE_ENTRY\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<input type=\"hidden\" name=\"list_id\" value=\"$list_id\" />";
		$out .="		<page_sections>";
		$out .="		<section label='Entry Preview' name='entryinfo'>";
		$max_fields = count($this->fields);
		$out .="			<seperator_row><seperator>\n";
//		for($i = 1 ; $i<$max_fields; $i++){
		foreach($this->fields as $key => $list){
				if ($list[3]==1){
					if (($list[4]=="text") || ($list[4]=="smallmemo") || ($list[4]=="memo") || ($list[4]=="radio") || ($list[4]=="select") || ($list[4]=="check") || ($list[4]=="list") || ($list[4]=="URL")){
						$val = $this->check_parameters($list,"value");
						$specified = $this->check_parameters($list,"specified",Array());
						
						if ($val=="" || $val =="<ul><li></li></ul>"){
							$val = $specified[0];
						}
						if ($val!="" && $val !="<ul><li></li></ul>"){
							$out .="			<text><![CDATA[<strong>".$list[0]."</strong>]]></text><text><![CDATA[".$val."]]></text>\n";
						}
					}
/*
					if($list[4]=="associated_entries"){
						$out .= "			<entry_associate type='associated_entries' name='$key' visible='yes'><label><![CDATA[".$list[0]."]]></label>".$list["value"]."</entry_associate>\n";
					}
					if($list[4]=="associations"){
						$out .= "			<input type=\"hidden\" name=\"file_associations_$key\"><![CDATA[".$list["associated_file_ids"]."]]></input>";
						$out .= "			<file_associate type='associations' name='$key' visible='yes'><label><![CDATA[".$list[0]."]]></label>".$list["value"]."</file_associate>\n";
					}
					if($list[4]=="imageembed"){
						$out .= "			<input type=\"hidden\" name=\"file_associations_".$key."\"><![CDATA[". $this->check_parameters($list["associated_file_ids"],0)."]]></input>";
						$out .= "			<file_associate type='imageembed' name='$key' visible='yes'><label><![CDATA[Thumbnail]]></label>".$this->check_parameters($list["value"],0)."</file_associate>\n";
						$out .= "			<input type=\"hidden\" name=\"file_associations_".$key."_main\"><![CDATA[". $this->check_parameters($list["associated_file_ids"],1)."]]></input>";
						$out .= "			<file_associate type='imageembed' name='".$key."_main' visible='yes'><label><![CDATA[Main Image]]></label>".$this->check_parameters($list["value"],1)."</file_associate>\n";
					}
*/
					if($list[4]=="colsplitter"){
						$out .="			</seperator><seperator>\n";
					}
					if($list[4]=="rowsplitter"){
						$out .="			</seperator></seperator_row><seperator_row><seperator>\n";
					}
				}
			}
	//	}
		$out .="			</seperator></seperator_row>";
		$out .="		</section>";
		/*************************************************************************************************************************
		* Display categories 
		*************************************************************************************************************************/
		$out .="		<section label='Locations Available'>";
		$out .= 			"<display_categories parent='$cat_parent' identifier='$info_category' name='cat_parent'>
								<label><![CDATA[Select Categories that this belongs to.]]></label>
								$category_listing
							</display_categories>";
		$out .="			$cat_list";
		$out .="		</section>";
		$out .="		</page_sections>";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	function import_confirm($parameters){
		$list_id  = $this->check_parameters($_SESSION,"list_id"-1);
		$counter  = $this->check_parameters($_SESSION,"IMPORT_COUNTER");
		unset($_SESSION["IMPORT_COUNTER"]);
		unset($_SESSION["list_id"]);
		unset($_SESSION["REDIRECT_AFTER"]);
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<page_options>";
		$out .="		<header><![CDATA[Import Confirmation]]></header>";
		$out .=			$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		$out .="	</page_options>";
		$out .="	<form name=\"process_form\" label=\"Import Complete\" width=\"100%\">";
		$n = $this->getmicrotime();
		$out .= "<text><![CDATA[<ul>
			<li>Import into database ::".($_SESSION["StartProcess"] - $_SESSION["StartImport"])." seconds</li>
			<li>Update options ::".($_SESSION["StartCatCache"] - $_SESSION["StartProcess"])." seconds</li>
			<li>Build Category Cache ::".($_SESSION["StartRestore"] - $_SESSION["StartCatCache"])." seconds</li>
			<li>Restore System ::".($n - $_SESSION["StartRestore"])." seconds</li>
			<li>Total time taken ::".($n - $_SESSION["StartImport"])." seconds</li>
			<li>Total time taken ::".floor(($n - $_SESSION["StartImport"])/60)." minutes</li>
		</ul>]]></text>";
		$out .=" 	<text><![CDATA[Thankyou there were [<strong>$counter</strong>] entries imported into the directory.]]></text>";
		$out .="	</form>";
		$out .="</module>";
		unset($_SESSION["StartImport"]);
		unset($_SESSION["StartProcess"]);
		unset($_SESSION["StartCatCache"]);
		unset($_SESSION["StartRestore"]);
		return $out;
	}

	function cache_entry_category($parameters){
		// open directory load all files one at a time parse and update category
		$list_id			= $this->check_parameters($parameters,"list_id",-1);
		$dst_category		= $this->check_parameters($parameters,"to",-1);
		$root  				= $this->check_parameters($this->parent->site_directories,"ROOT");
		$sql = "select * from information_list
		inner join menu_data on info_menu_location = menu_identifier and menu_client = info_client
		where info_client=$this->client_identifier and info_identifier=$list_id";
		//print $sql;
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$info_cat			 		= $r["info_category"];
			$info_url 					= dirname($r["menu_url"]);
        	$info_menu_location 		= $r["info_menu_location"];
			$info_in_menu				= $r["info_in_menu"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$this->loadedcat	= $this->call_command("CATEGORYADMIN_LOAD", Array("returntype"=>1, "list" => $info_cat));
		$this->call_command("CATEGORYADMIN_CACHE_MENU",Array("cat_root"=>$info_cat, "menu_identifier"=>$info_menu_location, "info_in_menu" => $info_in_menu));
		$directory 			= $this->find_path($dst_category);
		$path = $root."/".$info_url.$directory;
		$list = $this->ls_a($path,0);
		$fake_uri = $info_url."/index.php";
		for ($i=0, $m=count($list); $i < $m; $i++){
			
			if ($list[$i]!="index.php"){
				if(is_dir($path."/".$list[$i])){
				
				} else {
					$lines = file($path."/".$list[$i]);
					$datafile="";
					$fnd = 0;
					for ($index=0; $index < count($lines); $index++){
						if (trim($lines[$index])=="/"."* cut *"."/"){
							$fnd=1;
						}
						if($fnd==1){
							$datafile .= trim($lines[$index])."\r\n";
						}
					}
					$fp = fopen($path."/".$list[$i],"w");
					if($fp){
					
		fwrite($fp, "<"."?php
".'$'."script	= \"$directory/index.php\";
".'$'."category	= $dst_category;
".'$'."fake_uri	= \"$fake_uri\";
");
						fwrite($fp, $datafile);
						fclose($fp);
						$um = umask(0);
						@chmod($path."/".$list[$i], LS__FILE_PERMISSION);
						umask($um);

					}
				}
			}
		}
	}
	/*************************************************************************************************************************
    * tidy up history of a entry
    *************************************************************************************************************************/
	function tidyup_history($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$parent		= $this->check_parameters($parameters,"parent_id");
		$list_id  		= $this->check_parameters($parameters,"list_id");

		$sql = "select ie_identifier, ie_status, iev_value, ie_version_minor, ie_version_major from information_entry 
				left outer join information_entry_values on iev_field='ie_title' and iev_entry = ie_identifier and ie_client = iev_client and iev_list = ie_list
				where ie_client = $this->client_identifier and ie_list = $list_id and ie_parent=$parent 
				order by ie_version_major desc,  ie_version_minor desc,  ie_status asc, ie_identifier desc";
		$result  = $this->parent->db_pointer->database_query($sql);
       	$sz = "<div class='row'>
						<div class='bt' style='display:inline;width:350px;padding-left:5px;padding-right:5px;'>Title</div>
						<div class='bt' style='display:inline;width:50px;padding-left:5px;padding-right:5px;'>Version</div>
						<div class='bt' style='display:inline;width:100px;padding-left:5px;padding-right:5px;text-align:center;' id='formaction'><a href='javascript:tidy_toggle_entries();'>Remove All</a></div>
					</div>";
		$i=0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$sz .= "<div class='row'>
						<div style='display:inline;width:350px;padding-left:5px;padding-right:5px;'>".$r["iev_value"]."</div>
						<div style='display:inline;width:50px;padding-left:5px;padding-right:5px;'>".$r["ie_version_major"].".".$r["ie_version_minor"]."</div>
						<div style='display:inline;width:100px;padding-left:5px;padding-right:5px;text-align:center;'><input type=checkbox name='id_to_remove[]' id='id_to_remove_".$i."' value='".$r["ie_identifier"]."'/></div>
					</div>";
			$i++;
        }
        $this->parent->db_pointer->database_free_result($result);

		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<page_options>";
		$out .="		<header><![CDATA[Tidy up entry versions]]></header>";
		$out .=			$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL", $this->module_command."ENTRY_HISTORY&amp;list_id=$list_id&amp;parent_id=$parent&amp;identifier=$parent", LOCALE_CANCEL));
		$out .="	</page_options>";
		$out .="	<form name=\"process_form\" label=\"Version Control Tidy Up \" width=\"100%\">";
		$out .= "		<input type='hidden' name='command' value='".$this->module_command."TIDYUP_HISTORY_PROCESS'/>";
		$out .= "		<input type='hidden' name='parent' value='$parent'/>";
		$out .= "		<input type='hidden' name='list' value='$list_id'/>";
		$out .="		<page_sections>";
		$out .="			<section label='Tidy Controls' name='tidycontrols'>";
		$out .="		 		<text><![CDATA[$sz
		
		<script>
			var tidy_toggle = 1;
			function tidy_toggle_entries(){
				total_entries = $i;
				doc = document.getElementById('formaction');
				f=get_form();
				if (tidy_toggle == 1){
					tidy_toggle = 0
					doc.innerHTML =\"<a href='javascript:tidy_toggle_entries();'>Keep All</a>\";
					
				} else {
					tidy_toggle = 1
					doc.innerHTML =\"<a href='javascript:tidy_toggle_entries();'>Remove All</a>\";
				}
				for(var i=0;i<total_entries;i++){
					element = document.getElementById('id_to_remove_'+i);
					if (tidy_toggle == 1){
						element.checked = false;
					} else {
						element.checked = true;
					}
				}
			}
		</script>
		]]></text>";
		$out .="			</section>";
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"NEXT\" value=\"Tidy\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	function tidyup_history_process($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		$parent			= $this->check_parameters($parameters,"parent");
		$list_id  		= $this->check_parameters($parameters,"list");
		$id_to_remove	= $this->check_parameters($parameters,"id_to_remove");
		$out="";
		if (is_array($id_to_remove)){
			$list = join(",",$id_to_remove);	
			$sql = "delete from information_entry_values where iev_entry in ($list)";
			$this->parent->db_pointer->database_query($sql);
			$sql = "delete from information_entry where ie_identifier in ($list)";
			$this->parent->db_pointer->database_query($sql);
			$sql = "select * from information_entry where ie_parent= $parent and ie_client=$this->client_identifier order by ie_version_wip desc, ie_version_major desc, ie_version_minor desc";
			$result  = $this->parent->db_pointer->database_query($sql);
			if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
	            $r = $this->parent->db_pointer->database_fetch_array($result);
				$ie_version_wip = $r["ie_version_wip"];
				$identifier = $r["ie_identifier"];
				if ($ie_version_wip==0){
					$sql = "update information_entry set ie_version_wip=1 where ie_identifier=$identifier and ie_client = $this->client_identifier";
					$this->parent->db_pointer->database_query($sql);
				}
            }
            $this->parent->db_pointer->database_free_result($result);
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST_ENTRIES&identifier=$list_id"));
		} else {
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .="	<page_options>";
			$out .="		<header><![CDATA[Tidy up entry versions]]></header>";
			$out .=			$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."ENTRY_HISTORY&amp;list_id=$list_id&amp;parent_id=$parent&amp;identifier=$parent", LOCALE_CANCEL));
			$out .="	</page_options>";
			$out .="	<form name=\"process_form\" label=\"Version Control Tidy Up \" width=\"100%\">";
			$out .="		<page_sections>";
			$out .="			<section label='Tidy Up Failed' name='tidycontrols'>";
			$out .="		 		<text><![CDATA[Sorry the system was unable to process any actions]]></text>";
			$out .="			</section>";
			$out .="		</page_sections>";
			$out .="	</form>";
			$out .="</module>";
		return $out;
		
		}
	}
	function copy_history($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		$parent			= $this->check_parameters($parameters,"parent");
		$list_id  		= $this->check_parameters($parameters,"list");
		$id_to_remove	= $this->check_parameters($parameters,"id_to_remove");
		$out="";
	}
	
	function tidyup_lost($parameters){
		$out ="";
		$numdays=5;
		$now = $this->libertasGetDate("Y/m/d H:i:s",mktime (0,0,0,date("m"),date("d")-5,  date("Y")));
		$sql ="select * from information_entry where ie_uri !='' and ie_date_created < '$now' and ie_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
        $c = $this->call_command("DB_NUM_ROWS",Array($result));
        $this->parent->db_pointer->database_free_result($result);
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .="	<page_options>";
			$out .="		<header><![CDATA[Checking for entries that have not been verified by the author within specified timeframe]]></header>";
			$out .=			$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST", LOCALE_CANCEL));
			$out .="	</page_options>";
			$out .="	<form name=\"process_form\" label=\"\" width=\"100%\">";
			if ($c==0){
				$out .="		<page_sections>";
				$out .="			<section label='Tidy Up' name='tidycontrols'>";
				$out .=		 		"	<text><![CDATA[There were no entries found,  This directory is up to date]]></text>";
				$out .="			</section>";
				$out .="		</page_sections>";
			} else {
				$out .="		<input type=\"hidden\" name=\"command\" value='INFORMATIONADMIN_REMOVE_LOST_PROCESS'>";
				$out .="		<page_sections>";
				$out .="			<section label='Tidy Up' name='tidycontrols'>";
				$out .= "				<text><![CDATA[There were $c entries found that were submited more than $numdays days ago are you sure you want to remove them]]></text>";
				$out .="			</section>";
				$out .="		</page_sections>";
				$out .="		<input type=\"submit\" iconify=\"NEXT\" value=\"Tidy\" />";
			}
			$out .="	</form>";
			$out .="</module>";
		return $out;
	}
	function tidyup_lost_process($parameters){
		$out ="";
		$now = $this->libertasGetDate();
		$sql ="select * from information_entry where ie_uri !='' and ie_date_created < '$now' and ie_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$list ="";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if ($list!=""){
        		$list .= ", ";
			}
			$list .= $r["ie_identifier"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$sql = "delete from information_entry where ie_identifier in ($list) and ie_client=$this->client_identifier ";
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from information_entry_values where ie_entry in ($list) and ie_client=$this->client_identifier ";
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from category_to_object where cto_object in ($list) and cto_client=$this->client_identifier and cto_module='INFORMATIONADMIN_'";
		$this->parent->db_pointer->database_query($sql);
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<page_options>";
		$out .="		<header><![CDATA[Removing entries that have not been verified by the author]]></header>";
		$out .=			$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST", LOCALE_CANCEL));
		$out .="	</page_options>";
		$out .="	<form name=\"process_form\" label=\"\" width=\"100%\">";
		$out .="		<page_sections>";
		$out .="			<section label='Tidy Up' name='tidycontrols'>";
		$out .=		 		"	<text><![CDATA[This directory is up to date]]></text>";
		$out .="			</section>";
		$out .="		</page_sections>";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	
	function entry_users($parameters){
		$list		= $this->check_parameters($parameters,"list_id");
		$identifier = $this->check_parameters($parameters,"identifier");
		$opt="";

		$sql = "select ie_parent from information_entry where ie_identifier=$identifier and ie_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
		   	$parent = $r["ie_parent"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$sql = "
		select distinct user_info.*, contact_data.*, information_update_access.* from user_info 
			left outer join information_update_access on iua_client = user_client and user_identifier = iua_user and iua_entry = $parent
			left outer join contact_data on contact_user = user_identifier and contact_client = user_client 
		where user_client = $this->client_identifier
		order by contact_last_name, contact_first_name, user_login_name";
		$result  = $this->parent->db_pointer->database_query($sql);
		$previous ="";
		$opt = "<options module='Undefined contact name'>";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$cfname = $this->check_parameters($r,"contact_first_name");
			$csname = $this->check_parameters($r,"contact_last_name");
			
			$username = $r["user_login_name"];
			if ($cfname=="" && $csname==""){
				$uname = $username;
				$letter = "";
			} else {
				$uname = $cfname.", ".$csname;
				$letter = substr($csname,0,1);
				if ($previous!=$letter){
					$opt .= "</options><options module='$letter'>";
					$previous=$letter;
				}
			}
			$opt .= "<option value='".$r["user_identifier"]."'";
			if ($this->check_parameters($r,"iua_user","__NOT_FOUND__")!="__NOT_FOUND__"){
				$opt .= " selected='true'";
			}
			$opt .= "><![CDATA[$uname]]></option>";
        }
		$opt .= "</options>";
        $this->parent->db_pointer->database_free_result($result);

		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<page_options>";
		$out .="		<header><![CDATA[User editorail access - definition]]></header>";
		$out .=			$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST_ENTRIES&amp;identifier=$list", LOCALE_CANCEL));
		$out .="	</page_options>";
		$out .="	<form name=\"process_form\" label=\"\" width=\"100%\">";
		$out .= "		<input type='hidden' name='command' value='INFORMATIONADMIN_ENTRY_USERS_SAVE'/>";
		$out .= "		<input type='hidden' name='parent' value='$parent'/>";
		$out .= "		<input type='hidden' name='identifier' value='$identifier'/>";
		$out .= "		<input type='hidden' name='list' value='$list'/>";
		$out .="		<page_sections>";
		$out .="			<section label='Available Users' name='userlist'>";
		$out .=		 		"	<checkboxes name='lock_to_user_list' type='horizontal'>$opt</checkboxes>";
		$out .="			</section>";
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	function entry_users_save($parameters){
		$list				= $this->check_parameters($parameters,"list");
		$identifier			= $this->check_parameters($parameters,"identifer",-1);
		$parent				= $this->check_parameters($parameters,"parent",-1);
		$totalnumberofchecks= $this->check_parameters($parameters,"totalnumberofchecks_lock_to_user_list",0);
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<page_options>";
		$out .="		<header><![CDATA[User editorail access - confirmation]]></header>";
		$out .=			$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST_ENTRIES&amp;identifier=$list", LOCALE_CANCEL));
		$out .="	</page_options>";
		$out .="	<form name=\"process_form\" label=\"\" width=\"100%\">";
		$out .="		<page_sections>";
		$out .="			<section label='Confirmation' name='userconfirm'>";
		if ($parent==-1){
			$out .=		 		"	<text><![CDATA[Sorry there was a problem with setting the user lock defintion]]></text>";
		} else {
			$sql = "delete from information_update_access where iua_entry=$parent and iua_list=$list and iua_client=$this->client_identifier";
			$this->parent->db_pointer->database_query($sql);
			for($z=1;$z<=$totalnumberofchecks;$z++){
				$lock_to_user_list	= $this->check_parameters($parameters,"lock_to_user_list_".$z,Array());
				$max = count($lock_to_user_list);
				for($i=0; $i<$max; $i++){
					$iua_identifier = $this->getUid();
					$sql = "insert into information_update_access (iua_identifier, iua_user, iua_entry, iua_list, iua_client) values ($iua_identifier, ".$lock_to_user_list[$i].", $parent, $list, $this->client_identifier)";
					$this->parent->db_pointer->database_query($sql);
					//
				}
			}
			$out .=		 		"	<text><![CDATA[The list of users locked to this entry has been updated]]></text>";
		}
		$out .="			</section>";
		$out .="		</page_sections>";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	
	function checkDuplicates($parameters){
		$duplicate_sql		= "";
		$list 				= $this->check_parameters($parameters,"list_id");
		$parent_id			= $this->check_parameters($parameters,"parent_id");
		$dlist = Array();
		$sql 	 = "select if_name, if_label, if_type, if_duplicate, if_add_to_title from information_fields 
					where 
						if_list=$list and 
						if_client=$this->client_identifier and 
						if_screen=0 and
						if_type not in ('rowsplitter','colsplitter')
				order by if_rank";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	/*
			if($r['if_add_to_title'] == 1)  {
				$parameters['ie_title'] = $parameters['ie_title'] ." ".$this->validate(trim($this->check_parameters($parameters, $r["if_name"], "")));
			}
			*/
			if ($r["if_duplicate"]!=""){
				$dlist[count($dlist)] = Array(
					"name"	=> $r["if_name"],
					"type"	=> $r["if_duplicate"]
				);
			}
		}
		$this->parent->db_pointer->database_free_result($result);
		$maxdup = count($dlist);
		for($start = 0; $start < $maxdup ; $start++){
			$duplicate_check = $this->check_parameters($dlist,$start,Array());
			$dupname = $this->check_parameters($duplicate_check,"name");
			$duptype = $this->check_parameters($duplicate_check,"type");
			$value 		= $this->validate(trim($this->check_parameters($parameters, $dupname, "")));
//			$field_name = $this->check_parameters($parameters,);
			if ($duplicate_sql!=""){
				$duplicate_sql .= " or ";
			}
			if ($duptype=='exact'){
				$duplicate_sql .= "(iev_field='$dupname' and iev_value='$value') ";
			}
			if ($duptype=='contains'){
				$duplicate_sql .= "(iev_field='$dupname' and iev_value like '%$value%') ";
			}
			if ($duptype=='startswith'){
				$duplicate_sql .= "(iev_field='$dupname' and iev_value like '$value%') ";
			}
		}

		$sql = "SELECT distinct iev_field from information_entry 
				inner join information_entry_values on ie_list = iev_list and iev_entry = ie_identifier 
				where ($duplicate_sql) and ie_parent!=$parent_id and (ie_version_wip=1) and ie_client=$this->client_identifier and ie_list = $list";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->parent->db_pointer->database_query($sql);
		$errorarray = Array();
		$error=0;
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$errorarray[count($errorarray)]=$r["iev_field"];
			$error=1;
		}
		$this->parent->db_pointer->database_free_result($result);
		if ($error==1){
			return $errorarray;
		} else {
			return 1;
		}
	}


	
	function extractlatest($identifier){
	// used as part of import tool only
		$this->duplicate_import_row = Array();
		$sql = "select * from information_entry 
				inner join information_entry_values on iev_client=ie_client and iev_entry=ie_identifier
				inner join information_fields on if_client = iev_client and iev_field = if_name and if_screen =0
				where ie_parent = $identifier and ie_client = $this->client_identifier
				order by ie_identifier desc ";
		//
		
		$result  = $this->parent->db_pointer->database_query($sql);
		$ie = -1;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if ($ie == -1){
				$ie = $r["ie_identifier"];
			}
			if ($ie == $r["ie_identifier"]){
				$this->current_import_row[$r["if_name"]]["type"]	= $r["if_type"];
				if ($r["if_type"]=="list" || $r["if_type"]=="radio" || $r["if_type"]=="check" || $r["if_type"]=="select" ){
					if (""!=$this->check_parameters($this->current_import_row,$r["if_name"])){
						if ($this->check_parameters($this->current_import_row[$r["if_name"]],"value")==""){
							$this->current_import_row[$r["if_name"]]["value"]		= Array();
						}
							$tmp = $this->current_import_row[$r["if_name"]]["value"];
							if (!is_array($this->current_import_row[$r["if_name"]]["value"])){
								$this->current_import_row[$r["if_name"]]["value"]		= Array();
								$this->current_import_row[$r["if_name"]]["value"][0]	= $tmp;
							}
					}
					if ($this->check_parameters($this->duplicate_import_row,$r["if_name"],"__NOT_FOUND__")=="__NOT_FOUND__"){
						$this->duplicate_import_row[$r["if_name"]]	= Array("type"=>$r["if_type"], "value"=>Array());
					}
					$this->duplicate_import_row[$r["if_name"]]["value"][count($this->duplicate_import_row[$r["if_name"]]["value"])] = $r["iev_value"];
				} else {
					$this->duplicate_import_row[$r["if_name"]]		= Array("type"=>$r["if_type"], "value"=>$r["iev_value"], "ado"=>0);
      			}
			}
        }
        $this->parent->db_pointer->database_free_result($result);
//		print "<li>".__LINE__." - current_import_row - ".print_r($this->current_import_row,true)."</li>";
//		print "<li>Duplicate Import Row<br>";
//		print_r($this->duplicate_import_row);
//		print "</li>";
		$match = 1;
		foreach($this->current_import_row as $key => $entry){
			if ($this->check_parameters($entry,"duplicate_check")!=""){
	//			print $entry["value"]." != ".$this->duplicate_import_row[$key]["value"]."<br>";
				if ($entry["type"]=="check"){
					if (!in_array($this->duplicate_import_row[$key]["value"],$entry["value"])){
						$match =0;
					}
				} else {
					if ($entry["value"] != $this->duplicate_import_row[$key]["value"]){
						$match =0;
					}
				}
			}
		}
//		print $match;
//		$this->exitprogram();
		return $ie;
	}
	/*************************************************************************************************************************
    * 				 				 			F E A T U R E D   L I S T S
    *************************************************************************************************************************/


	/*
    *
    *************************************************************************************************************************/
	function featured_list($parameters){
		$identifier  = $this->check_parameters($parameters,"identifier",-1);
		$sql = "select * from information_list where info_identifier = $identifier and info_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$info_label = $r["info_label"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$sql = "select * from information_features where ifeature_list = $identifier and ifeature_client = $this->client_identifier";
		$out = "";
		$result  = $this->parent->db_pointer->database_query($sql);
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$prev = $this->page_size;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$this->call_command("DB_SEEK",array($result,$goto));
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
			
			$variables["PAGE_BUTTONS"] = Array();
			if ($this->manage_database_list == 1){
				$variables["PAGE_BUTTONS"][0] = Array("CANCEL",$this->module_command."LIST", LOCALE_CANCEL);
				$variables["PAGE_BUTTONS"][1] = Array("ADD",$this->module_command."FEATURE_ADD&amp;list=$identifier", ADD_NEW);
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
			
			if (($start_page + $this->page_size)>$num_pages){
				$end_page	 =	$num_pages;
			}else{
				$end_page	=	$this->page_size;
			}
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= "";
			$variables["as"]			= "table";
			$variables["HEADER"]			= "Information Directory - Feature List - ".$info_label;
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			$page_discussion=0;
			$trans_page=0;
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				if($this->check_parameters($r,"ifeature_as_rss",0)==0){
					$rss = LOCALE_NO;
				} else {
					$rss = LOCALE_YES;
				}
				if($r["ifeature_status"]==0){
					$status = LOCALE_NOTLIVE;
				} else {
					$status = LOCALE_LIVE;
				}
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["ifeature_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		$this->check_parameters($r,"ifeature_label",""), "TITLE"),
						Array(LOCALE_AS_RSS,	$rss, "SUMMARY"),
						Array(LOCALE_STATUS,	$status, "SUMMARY")
					)
				);
				if ($this->author_admin_access){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."FEATURE_EDIT&amp;list=$identifier",EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."FEATURE_REMOVE&amp;list=$identifier",REMOVE_EXISTING);
				}
			}
			$this->page_size = $prev;
			
			return $this->generate_list($variables);
		}
	}
	/*************************************************************************************************************************
	* modify featured list
	*************************************************************************************************************************/
	function featured_modify($parameters){
	//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"parameters",__LINE__,print_r($parameters,true)));}
		$identifier 				= $this->check_parameters($parameters,"identifier",-1);
		$list						= $this->check_parameters($parameters,"list",-1);
		$ifeature_label				= "";
		$ifeature_status			= 0;
		$ifeature_list_type			= 1;
		$ifeature_display_format	= 0;
		$ifeature_display_rotation	= 0;
		$ifeature_display_number	= 1;
		$ifeature_as_rss			= 0;
		$manual_list				= "";
		$manual_info				= "";
		$field_list					= Array();
		$all_locations				= 0;
		$set_inheritance			= 0;
		$menu_locations				= 0;
		
		$sql = "select if_map, if_name, if_label, if_type, info_category from information_fields inner join information_list on info_identifier = if_list and if_client=info_client where if_screen=0 and if_list=$list and if_client=$this->client_identifier";
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,$sql));}
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$info_category = $r["info_category"];
			if ($r["if_type"]=="check" || $r["if_type"]=="list" || $r["if_type"]=="select" || $r["if_type"]=="radio"){
				$type="list";
				$orderable = 1;
			} else {
				$type="text";
				if($r["if_type"]=="memo" || $r["if_type"]=="smallmemo"){
					$orderable = "";
				} else if ($r["if_map"]!=""){
					$orderable = $r["if_map"];
				} else {
					$orderable = "";
				}
			}
        	$field_list[count($field_list)] = Array("field"=>$r["if_name"], "label" => $r["if_label"], "type"=>$type, "options" => Array(), "orderable"=>$orderable);
        }
        $this->parent->db_pointer->database_free_result($result);
		foreach($field_list as $key => $val){
			if ($field_list[$key]["type"]!="check" || $field_list[$key]["type"]=="list" || $field_list[$key]["type"]=="select" || $field_list[$key]["type"]=="radio"){
				$sql = "select io_value from information_options where io_list=$list and io_client=$this->client_identifier and io_field='".$field_list[$key]["field"]."'";
				$oresult  = $this->parent->db_pointer->database_query($sql);
				$field_list[$key]["options"]=Array();
                while($r = $this->call_command("DB_FETCH_ARRAY",Array($oresult))){
                	$field_list[$key]["options"][count($field_list[$key]["options"])]= $r["io_value"];
                }
                $this->call_command("DB_FREE",Array($oresult));
			}
		}
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"identifier",__LINE__,$identifier));}
		if ($identifier!=-1){
			$sql = "select * from information_features where ifeature_list=$list and ifeature_identifier = $identifier and ifeature_client = $this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$ifeature_label				= $r["ifeature_label"];
				$ifeature_status			= $r["ifeature_status"];
				$ifeature_list_type			= $r["ifeature_list_type"];
				$ifeature_display_format	= $r["ifeature_display_format"];
				$ifeature_display_rotation	= $r["ifeature_display_rotation"];
				$ifeature_display_number	= $r["ifeature_auto_counter"];
				$ifeature_date_start		= $r["ifeature_date_start"];
				$ifeature_date_finish		= $r["ifeature_date_finish"];
				$all_locations				= $r["ifeature_all_locations"];
				$set_inheritance			= $r["ifeature_set_inheritance"];
				$ifeature_as_rss			= $r["ifeature_as_rss"];
            }
            $this->parent->db_pointer->database_free_result($result);
			
			if ($ifeature_list_type == 0){
				$sql = "select * from information_feature_list 
					inner join information_entry on ifl_entry = ie_parent and ie_client = ifl_client and ie_list = $list 
					inner join metadata_details on md_link_id = ie_identifier and md_client=ie_client and md_module ='$this->webContainer'
				where ifl_client = $this->client_identifier and ifl_owner = $identifier order by ifl_rank";
				if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,$sql));}
				$result  = $this->parent->db_pointer->database_query($sql);
                while($r = $this->parent->db_pointer->database_fetch_array($result)){
					if ($manual_list!=""){
						$manual_list.=", ";
					}
					$manual_list .= $r["ifl_entry"];
                	$manual_info .= "<option value='".htmlentities($r["ifl_entry"], ENT_QUOTES)."' category='".$r["ifl_cat"]."'><![CDATA[".htmlentities($r["md_title"], ENT_QUOTES)."]]></option>";
                }
                $this->parent->db_pointer->database_free_result($result);
			}
			$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
				Array(
					"module"		=> $this->webContainer."FEATURES",
					"identifier"	=> $identifier
				)
			);
		}
			/*************************************************************************************************************************
            * get the list of available fields,
            *************************************************************************************************************************/
			$sql = "select * from information_fields 
						where if_list=$list and if_screen = 0 and if_client = $this->client_identifier order by if_rank";
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,$sql));}
			$result  = $this->parent->db_pointer->database_query($sql);
			$field_array = Array();
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$position = count($field_array);
				$field_array[$position] = Array();
				foreach($r as $key =>$val){
					if(!is_int($key)){
						$field_array[$position][$key] = $val;
					}
				}
            }
            $this->parent->db_pointer->database_free_result($result);
			$sql = "select * from information_feature_field_rank 
						left outer join information_fields on iffr_field = if_name and iffr_list = if_list and if_screen = 0 and if_client=iffr_client
					where iffr_client= $this->client_identifier and iffr_list = $list order by iffr_rank";
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,$sql));}
			$result  = $this->parent->db_pointer->database_query($sql);
			$selected_array = Array();
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$position = count($selected_array);
				$selected_array[$position] = Array();
				foreach($r as $key =>$val){
					if(!is_int($key)){
						$selected_array[$position][$key] = $val;
					}
				}
            }
            $this->parent->db_pointer->database_free_result($result);
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<page_options>";
		$out .="		<header><![CDATA[Feature List modification / creation]]></header>";
		$out .=			$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."FEATURE_LIST&amp;identifier=$list", LOCALE_CANCEL));
		$out .="	</page_options>";
		$out .="	<form name=\"process_form\" label=\"\" width=\"100%\">";
		$out .="	<showframe>1</showframe>";
		$out .="	<input type='hidden' name='ifeature_identifier' ><![CDATA[$identifier]]></input>";
		$out .="	<input type='hidden' name='command' ><![CDATA[".$this->module_command."FEATURE_SAVE]]></input>";
		$out .="	<input type='hidden' name='ifeature_list' ><![CDATA[$list]]></input>";
		$out .="	<input type='hidden' name='ifeature_manual_list' ><![CDATA[$manual_list]]></input>";
		$out .="		<page_sections>";
		$out .="			<section label='Settings' name='settingstab'>";
		$out .="	<input type='text' name='ifeature_label' label='Label' required=\"YES\" ><![CDATA[$ifeature_label]]></input>";
		$out .="	<select name='ifeature_status' label='".LOCALE_STATUS."'>
									<option value='0'";
		if ($ifeature_status==0){
			$out .= " selected ='true'";
		}
		$out .= 				">Not live</option>
			<option value='1'";
		if ($ifeature_status==1){
			$out .= " selected ='true'";
		}
		$out .= 				">Live</option>
				</select>";
		$out .="	<radio name='ifeature_as_rss' label='".LOCALE_AS_RSS."'>
									<option value='0'";
		if ($ifeature_as_rss==0){
			$out .= " selected ='true'";
		}
		$out .= 				">".LOCALE_NO."</option>
			<option value='1'";
		if ($ifeature_as_rss==1){
			$out .= " selected ='true'";
		}
		$out .= 				">".LOCALE_YES."</option>
				</radio>";
				
		$out .="	<select name='ifeature_list_type' label='Manual or Automatic extraction of entries' hidden='yes' onchange='toggle_tab()'>";
		$out .=$this->gen_options(
			Array(0,1),
			Array("Manually define list of entries to be displayed","Automatically, select a list of entries from the directory"),
			$ifeature_list_type
		);
		$out .="</select>";
		$out .="	<select name='ifeature_display_format' label='Choose content to be displayed'>";
		$out .=$this->gen_options(
			Array(0,1),
			Array("Display the summary Screen", "Display the content Screen"),
			$ifeature_display_format
		);
		$out .="</select>";
		$out .="	<select name='ifeature_display_rotation' label='What way should this Featured list work?'>";
		$out .=$this->gen_options(
			Array(0,1,2,3),
			Array("Display the same featured list for the users session", "Cycle through the list of entries", "Randomly, select a list of entries from the directory", "Static, Display the same list over and over again"),
			$ifeature_display_rotation
		);
		$out .="</select>";
		$out .= "	<select name='ifeature_auto_counter' label='How many entries should be displayed at a time'>";
		for($i=1;$i<=10;$i++){
			$out .= "<option value='$i'";
			if ($ifeature_display_number==$i){
				$out .= " selected ='true'";
			}
			$out .= 				">Display $i entry";
			if ($i>1){
				$out .= "s";
			}
			$out.="</option>";
		}
		$out .="</select>";
//		$out .=" <input type=\"date_time\" name=\"start_datetime\" value=\"$ifeature_date_start\" label=\"Available from\"/>";
//		$out .=" <input type=\"date_time\" name=\"finish_datetime\" value=\"$ifeature_date_finish\" label=\"Available until\"/>";
		$val = $this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>$this->module_presentation."", "identifier"=>$identifier));
		$web_containers = split("~----~",$val);
		if ($web_containers[0]!=""){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}

		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";

		$out .="			</section>";
		$out .= $this->location_tab($all_locations, $set_inheritance, $menu_locations, "");
		$out .="			<section label='Manual List' name='manualtab'";
		if ($ifeature_list_type!=0){
			$out .= " hidden='true'";
		}
		$out .=">";
		$out .="				<directory_entry list='$list' category='$info_category' >$manual_info</directory_entry>";
		$out .="			</section>";
		$hide = 0; 
		if ($ifeature_list_type==0){
			$hide = 1;
		}
		$out .= $this->call_command("FILTERADMIN_EMBED", 
			Array(
				"hide"		 =>	$hide,
				"field_list" => Array(), 
				"identifier" => $identifier, 
				"module"	 => $this->webContainer,
				"field_list" => $field_list,
				"extratags"	 => Array("list"=>$list)
			)
		);
		$out .="			<section label='Display' name='displaytab'><features>";
		//print_r($field_array);
		for ($index=0, $max=count($field_array); $index<$max; $index++){
			$label = $field_array[$index]["if_label"];
			$out .="<feature_fields name='".$field_array[$index]["if_name"]."'>
						<label><![CDATA[$label]]></label>
					</feature_fields>";
		}
		for ($index=0, $max=count($selected_array); $index<$max; $index++){
			$label 				= $selected_array[$index]["if_label"];
			$iffr_label_display = $selected_array[$index]["iffr_label_display"];
			if($iffr_label_display==""){
				$iffr_label_display=0;
			}
			$rank = $selected_array[$index]["iffr_rank"];
			$out .="<selected_field name='".$selected_array[$index]["iffr_field"]."' rank='$rank'>
						<label><![CDATA[$label]]></label>
						<label_display setting='$iffr_label_display'/>
					</selected_field>";
		}
		$out .="			</features></section>";
		$out .= $this->preview_section($this->module_presentation."FEATURE_PREVIEW", 1, 0, Array("igCmd"=>$this->module_presentation."FEATURES"));
		$out .="		</page_sections>";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	
	function featured_save($parameters){
//		print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameters,true)."</p></li>";
//		$this->exitprogram();
		$block					= $this->check_parameters($parameters,"filter_builder_blockinfo");
		$identifier				= $this->check_parameters($parameters,"ifeature_identifier");
		$status					= $this->check_parameters($parameters,"ifeature_status");
		$ltype					= $this->check_parameters($parameters,"ifeature_list_type");
		$list					= $this->check_parameters($parameters,"ifeature_list");
		$display_format			= $this->check_parameters($parameters,"ifeature_display_format");
		$display_rotation		= $this->check_parameters($parameters,"ifeature_display_rotation");
		$ifeature_label			= $this->check_parameters($parameters,"ifeature_label");
		$counter				= $this->check_parameters($parameters,"ifeature_auto_counter");
		$ifeature_as_rss		= $this->check_parameters($parameters,"ifeature_as_rss",0);
		$fields					= $this->check_parameters($parameters,"fields",Array());
		$iffr_label_display		= $this->check_parameters($parameters,"iffr_label_display",Array());
		$label					= $this->check_parameters($parameters,"label",Array());
//	locatiosn tab	
		$all_locations			= $this->check_parameters($parameters, "all_locations");
		$menu_locations			= $this->check_parameters($parameters, "menu_locations", Array());
		$set_inheritance		= $this->check_parameters($parameters, "set_inheritance");
// web container list
		$replacelist			= $this->check_parameters($parameters, "web_containers",Array());
		$currentlyhave			= $this->check_parameters($parameters, "currentlyhave");
		$count_rss_containers	= $this->check_parameters($parameters, "totalnumberofchecks_web_containers");
// Manual List  Identifiers and Categories
		$ManualEntryId			= $this->check_parameters($parameters, "ManualEntryId",Array());
		$ManualEntryCat			= $this->check_parameters($parameters, "ManualEntryCat",Array());
		// filter order options
		$cof = $this->check_parameters($parameters, "choosen_order_field");
		$ro = $this->check_parameters($parameters, "rank_order",0);
		
		$now = $this->LibertasGetDate();
		if ($identifier==-1){
			$identifier = $this->getUid();
			$cmd		= "INSERT";
			$sql 		= "
					insert into information_features (
						ifeature_identifier,
						ifeature_client,
						ifeature_status,
						ifeature_list_type,
						ifeature_list,
						ifeature_display_format,
						ifeature_display_rotation,
						ifeature_label,
						ifeature_auto_counter,
						ifeature_date_created,
						ifeature_all_locations,
						ifeature_set_inheritance,
						ifeature_as_rss
					) values (
						$identifier,
						'$this->client_identifier',
						'$status',
						'$ltype',
						'$list',
						'$display_format',
						'$display_rotation',
						'$ifeature_label',
						'$counter',
						'$now',
						'$all_locations', 
						'$set_inheritance',
						'$ifeature_as_rss'
				)";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 			=> $this->module_presentation."",
					"owner_id" 				=> $identifier,
					"label" 				=> $ifeature_label,
					"wo_command"			=> $this->module_presentation."FEATURES",
					"cmd"					=> "ADD",
					"previous_list"			=> $currentlyhave,
					"new_list"				=> $replacelist
				)
			);
		} else {
			$sql 	= "
					update information_features set
						ifeature_status				= '$status',
						ifeature_list_type			= '$ltype',
						ifeature_list				= '$list',
						ifeature_display_format		= '$display_format',
						ifeature_display_rotation	= '$display_rotation',
						ifeature_label				= '$ifeature_label',
						ifeature_auto_counter		= '$counter',
						ifeature_all_locations		= '$all_locations',
						ifeature_set_inheritance	= '$set_inheritance',
						ifeature_as_rss				= '$ifeature_as_rss'
					where 
						ifeature_client				= '$this->client_identifier' and 
						ifeature_identifier 		= $identifier";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 				=> $this->module_presentation."",
					"owner_id" 					=> $identifier,
					"label" 					=> $ifeature_label,
					"wo_command"				=> $this->module_presentation."FEATURES",
					"cmd"						=> "UPDATE",
					"previous_list" 			=> $currentlyhave,
					"new_list"					=> $replacelist
				)
			);
			$cmd			= "UPDATE";
		}
		/*************************************************************************************************************************
        * fields
        *************************************************************************************************************************/
		$this->save_feature_fields($identifier, $list, $fields,$iffr_label_display,$label);
		/*************************************************************************************************************************
		* Save menu locations
		*************************************************************************************************************************/
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->webContainer."FEATURES",
				"identifier"	=> $identifier,
				"all_locations"	=> $all_locations
			)
		);
		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance($this->module_presentation."FEATURES",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=>$child_locations,
					"module"		=> $this->webContainer."FEATURES",
					"identifier"	=> $identifier,
					"all_locations"	=> $all_locations,
					"delete"		=> 0
				)
			);
			$this->set_inheritance(
				$this->module_presentation."FEATURES",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"=> $this->webContainer."FEATURES",
					"condition"=> "ifeature_set_inheritance =1 and ",
					"client_field"=> "ifeature_client",
					"table"	=> "information_features",
					"primary"=> "ifeature_identifier"
					)
				)."");

		}
		$this->tidyup_display_commands(
			Array(
				"tidy_table" 		=> "information_features",
				"tidy_field_starter"=> "ifeature_",
				"tidy_webobj"		=> $this->module_presentation."FEATURES",
				"tidy_module"		=> $this->webContainer."FEATURES",
				"all_locations"		=> $all_locations
			)
		);
		/*************************************************************************************************************************
		*  manage the filter
		*************************************************************************************************************************/ 
		if($ltype!=0){
				$sql = "select * from information_fields  
							inner join information_list on if_list = info_identifier and if_client = info_client
						where if_client = $this->client_identifier and if_screen =0 and if_list = $list	";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				$result  = $this->parent->db_pointer->database_query($sql);
				$keymap = Array();
		        while($r = $this->parent->db_pointer->database_fetch_array($result)){
		        	$keymap[count($keymap)] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"]);
		        }
			    $this->parent->db_pointer->database_free_result($result);
			$this->call_command("FILTERADMIN_MANAGE_OBJECT",
				Array(
					"filter_builder_blockinfo"	=> $block,
					"module"					=> $this->webContainer,
					"owner"						=> $identifier,
					"cmd"						=> $cmd,
					"maps"						=> $keymap,
					"choosen_order_field"		=> $cof,
					"rank_order"				=> $ro

				)
			);
		} else {
			/*************************************************************************************************************************
            * remove filter if it exists
            *************************************************************************************************************************/
			$this->call_command("FILTERADMIN_MANAGE_OBJECT",
				Array(
					"filter_builder_blockinfo"	=> $block,
					"module"					=> $this->webContainer,
					"owner"						=> $identifier,
					"cmd"						=> "REMOVE"
				)
			);
			/*************************************************************************************************************************
            *	add entries to table
            *************************************************************************************************************************/
			$len = count($ManualEntryCat);
			$sql = "delete from information_feature_list where ifl_client=".$this->client_identifier." and ifl_owner = ".$identifier."";
			$this->parent->db_pointer->database_query($sql);
			for ($i=0; $i<$len; $i++){
				$ifl_identifier = $this->getUid();
				$sql = "insert into information_feature_list (ifl_identifier, ifl_client, ifl_owner, ifl_entry, ifl_cat, ifl_rank) values ($ifl_identifier, ".$this->client_identifier.", ".$identifier.", '".$ManualEntryId[$i]."', '".$ManualEntryCat[$i]."', '".$i."' )";
//				print "<li>$sql</li>";
                $this->parent->db_pointer->database_query($sql);
			}
		}
//		if($ifeature_as_rss==1){
			// "index.php?command=".$this->webContainer."EXTRACT_RSS&amp;identifier=$identifier",
			$this->call_command("RSSADMIN_EXTERNAL_CREATE", 
				Array(
					"action"	 => $ifeature_as_rss,
					"module"	 => $this->webContainer,
					"identifier" => $identifier,
					"label"		 => $ifeature_label
				)
			);
//		}
	}
	
	function gen_sql_cache($parameters){
		$match_list	= $this->check_parameters($parameters,"match_list");
		$block		= $this->check_parameters($parameters,"block");
		$list		= $this->check_parameters($parameters,"identifier", -1);
		$maps		= $this->check_parameters($parameters,"maps", Array());
		$order		= $this->check_parameters($parameters,"order", Array("field"=>"","dir"=>0));
		if ($this->module_debug){ 
			print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		}
		//		$maps = Array();
		if($list !=-1){
			$sql = "select * from information_fields 
inner join information_list on if_list = info_identifier and if_client = info_client 
inner join information_features on ifeature_list = if_list and if_client = ifeature_client
where if_client = $this->client_identifier and if_screen =0 and ifeature_identifier = $list";
//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result  = $this->parent->db_pointer->database_query($sql);
        	while($r = $this->parent->db_pointer->database_fetch_array($result)){
        		$maps[count($maps)] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"]);
	        }
		    $this->parent->db_pointer->database_free_result($result);
		}
		$blocklist	= split("\r\n",$block);
//		print_r($maps);
//		print_r($match_list);
		$where	= "";
		$join	= "";
		$max = count($blocklist)-1;
		$mc = count($maps);
		for($index = 0 ; $index <$max ; $index++){
			$blocklist[$index] = split(":::",$blocklist[$index]);
			$i = $blocklist[$index][2]*1;
			$ok =0;
			for($zi = 0; $zi<$mc;$zi++){
				if ($blocklist[$index][1]==$maps[$zi][0] && $maps[$zi][1]!=""){
//					print "<li>$blocklist[$index][1]==$maps[$zi][0]</li>";
					$ok =1;
					$z = $zi;
					break;
				}
			}
			if($ok==1){
				if($blocklist[$index][3]==0){
					$where .= " and ";
				} else {
					$where .= " or ";
				}
				$where .= " metadata_details.".$maps[$z][1]." ". str_replace(Array("[[value]]"), Array($blocklist[$index][4]) , trim($match_list[$i][1]))." ";		
			}else{
				if($blocklist[$index][3]==0){
					$join .= "inner join ";
				} else {
					$join .= "left outer join ";
				}
				$join .= " information_entry_values as iev".$index." on iev".$index.".iev_list = ie_list and ie_identifier = iev".$index.".iev_entry and ie_client=iev".$index.".iev_client and (iev".$index.".iev_field = '".$blocklist[$index][1]."' and iev".$index.".iev_value ". str_replace(Array("[[value]]"), Array($blocklist[$index][4]) , trim($match_list[$i][1])).") ";		
			}
		}
//$this->exitprogram();
		return Array("join"=>$join,"where"=>$where, "order"=>$order);
	}
	/*************************************************************************************************************************
	* remove a featured list
	*************************************************************************************************************************/
	function featured_remove($parameters){
		$identifier	= $this->check_parameters($parameters, "identifier",-1);
		$list		= $this->check_parameters($parameters, "list",-1);
		if($identifier!=-1){
			$this->call_command("FILTERADMIN_MANAGE_OBJECT",
				Array(
					"module"					=> $this->webContainer,
					"owner"						=> $identifier,
					"cmd"						=> "REMOVE"
				)
			);
			$sql = "delete from information_features where 
						ifeature_list				= '$list' and 
						ifeature_client				= '$this->client_identifier' and 
						ifeature_identifier 		= $identifier";
			$this->parent->db_pointer->database_query($sql);
			$sql = "delete from information_feature_list where ifl_client=".$this->client_identifier." and ifl_owner = ".$identifier."";
			$this->parent->db_pointer->database_query($sql);
		}
		return "";
	}

	
	function test_query($parameters){
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		$block 			= $this->check_parameters($parameters,"block");
		$owner 			= $this->check_parameters($parameters,"owner");
		$list			= $this->check_parameters($parameters,"list");
		$match_list		= $this->check_parameters($parameters,"match_list");
		$order			= $this->check_parameters($parameters,"order");
		$order_dir		= $this->check_parameters($parameters,"order_dir",0);
		$block			= join("\r\n",split(":-:",$block));
		$keymap 		= Array();
		$out 			= "";
		$sql = "select * from information_fields  
					inner join information_list on if_list = info_identifier and if_client = info_client
				where if_client = $this->client_identifier and if_screen =0 and if_list = $list	";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$keymap[count($keymap)] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"]);
        }
	    $this->parent->db_pointer->database_free_result($result);

		$condition		= $this->gen_sql_cache(Array("identifier"=>$list, "block"=>$block, "match_list"=>$match_list, "maps" => $keymap, "order"=>Array("field"=>"$order","dir"=>$order_dir)));
		/*************************************************************************************************************************
        * retrieve order def
        *************************************************************************************************************************/
		$ord = $this->check_parameters($condition,"order",Array("field"=>"","dir"=>0));
		$ord_value="";
		if($ord["field"]!=""){
//			print_r($ord);
			$ord_value .= $ord["field"]. (($ord["dir"]==0)?" asc,":" desc,");
		}
		$sql = "select distinct ie_parent, menu_url, md_title from information_entry 
			inner join metadata_details on md_link_id = ie_identifier and ie_client = md_client and md_module='".$this->webContainer."'
			".$condition["join"]."
			inner join information_list on info_identifier = ie_list and ie_client = info_client
			inner join menu_data on menu_identifier = info_menu_location and info_client = menu_client 
			where ie_published=1 
			".$condition["where"]." and 
			ie_status =1 and ie_list = $list and ie_client=$this->client_identifier order by $ord_value ie_identifier desc";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->parent->db_pointer->database_query($sql);
		$counter =0;
		$ids = Array();
		$num_rows = $this->call_command("DB_NUM_ROWS",Array($result));
	    while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if ($counter<10){
	           	$ids[$counter] = $r["ie_parent"];
	        	$out.= "<entry><title><![CDATA[".htmlentities($r["md_title"])."]]></title></entry>";
			}
			$counter++;
        }
        $this->parent->db_pointer->database_free_result($result);
//		print_r($ids);
/*
		$sql = "select * from information_entry 
inner join information_entry_values on iev_list = ie_list and iev_client=ie_client and iev_entry = ie_identifier and iev_field = 'ie_title'
where ie_published=1 and ie_parent in (".join(", ",$ids).") and ie_client=$this->client_identifier and ie_status =1 and ie_list = $list
order by ie_identifier ";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        }
        $this->parent->db_pointer->database_free_result($result);
		print "<module name='".$this->module_name."' display='TEST_QUERY'><numrows>$num_rows</numrows>".$out."</module>";
*/
		return "<module name='".$this->module_name."' display='TEST_QUERY'><numrows>$num_rows</numrows>".$out."</module>";
	}
	
	function extract_entry_list($parameters){
		$cat	 = $this->check_parameters($parameters,"cat"); // filter category
		$dirlist= $this->check_parameters($parameters,"list"); // directory belongs to
		$catlist= $this->check_parameters($parameters,"catlist"); // category list used
	
		$sql = "select ie_parent as id, md_title as title from information_entry
					inner join metadata_details on md_link_id = ie_identifier and md_module='$this->webContainer' and md_client = ie_client
					inner join category_to_object on cto_object = ie_identifier and cto_client= ie_client and cto_module='INFORMATIONADMIN_' and cto_clist =$cat
				where ie_client = $this->client_identifier and ie_list = $dirlist and ie_published = 1";
        $result  = $this->parent->db_pointer->database_query($sql);
		$out="";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$out .="<entry identifier='".$r["id"]."'>
				<title><![CDATA[".$r["title"]."]]></title>
			</entry>";
        }
        $this->parent->db_pointer->database_free_result($result);
		return "<module name='".$this->module_name."' display='CACHE'>".$out."</module>";
	}
	/*************************************************************************************************************************
    *									B A S I C   S E A R C H   S E T T I N G S 
    *************************************************************************************************************************/
	
	
	function manage_basic_search($parameters){
		$list		  		= $this->check_parameters($parameters,"identifier",-1);
		$identifier  		= -1;
		$all_locations		= 0;
		$set_inheritance	= 0;
		$display_tab		= "";
		$label				= "";
		
		$sql = "select * from information_search where ibs_client=$this->client_identifier and ibs_list=$list";
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
            $identifier			= $r["ibs_identifier"];
		    $label				= $r["ibs_label"];
		    $all_locations		= $r["ibs_all_locations"];
			$set_inheritance	= $r["ibs_set_inheritance"];
        }
		$this->parent->db_pointer->database_free_result($result);
		$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
			Array(
			"module"		=> $this->module_presentation."SEARCH",
			"identifier"	=> $identifier
			)
		);
		$out ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Search Manager - Basic]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"Manage basic search\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."BASIC_SEARCH_SAVE\" />";
		$out .="		<input type=\"hidden\" name=\"list\" value=\"$list\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<page_sections>";
		$out .="			<section label='Setup'>";
		$out .="				<input required=\"YES\" type=\"text\" name=\"label\" label=\"Search Label\" size=\"255\"><![CDATA[$label]]></input>";
		$web_containers = split("~----~",$this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>$this->module_presentation."SEARCH", "identifier"=>$identifier)));
		if ($web_containers[0]!=""){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}
		$out .="			</section>";
		$out .= 			$this->location_tab($all_locations, $set_inheritance,$menu_locations, $display_tab);
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	
	function manage_basic_search_save($parameters){
		$identifier				= $this->check_parameters($parameters,"identifier");
		$list					= $this->check_parameters($parameters,"list");
		$label					= $this->validate($this->check_parameters($parameters,"label"));
		//	locations tab	
		$all_locations			= $this->check_parameters($parameters, "all_locations");
		$menu_locations			= $this->check_parameters($parameters, "menu_locations", Array());
		$set_inheritance		= $this->check_parameters($parameters, "set_inheritance");
		// web container list
		$replacelist			= $this->check_parameters($parameters, "web_containers",Array());
		$currentlyhave			= $this->check_parameters($parameters, "currentlyhave");
		$count_rss_containers	= $this->check_parameters($parameters, "totalnumberofchecks_web_containers");


		$now = $this->LibertasGetDate();
		if ($identifier==-1){
			$ibs_identifier = $this->getUid();
			$cmd			= "INSERT";
			$sql 			= "
					insert into information_search (
						ibs_identifier,
						ibs_client,
						ibs_list,
						ibs_label,
						ibs_date_created,
						ibs_all_locations,
						ibs_set_inheritance 
					) values (
						$ibs_identifier,
						'$this->client_identifier',
						'$list',
						'$label',
						'$now',
						'$all_locations', 
						'$set_inheritance'
				)";
			$this->parent->db_pointer->database_query($sql);
           	$identifier = $ibs_identifier;
            $this->parent->db_pointer->database_free_result($result);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->module_presentation."SEARCH",
					"owner_id" 		=> $identifier,
					"label" 		=> $label,
					"wo_command"	=> $this->module_presentation."SEARCH",
					"cmd"			=> "ADD",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);

		} else {
			$sql 			= "
					update information_search set
						ibs_list				= '$list',
						ibs_label				= '$label',
						ibs_all_locations		= $all_locations,
						ibs_set_inheritance		= $set_inheritance
					where 
						ibs_client				= '$this->client_identifier' and 
						ibs_identifier 			= $identifier";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->module_presentation."SEARCH",
					"owner_id" 		=> $identifier,
					"label" 		=> $label,
					"wo_command"	=> $this->module_presentation."SEARCH",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
			$cmd= "UPDATE";
		}
		/*
		Save menu locations
		*************************************************************************************************************************/
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->module_presentation."SEARCH",
				"identifier"	=> $identifier,
				"all_locations"	=> $all_locations
			)
		);
		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance($this->module_presentation."SEARCH",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=>$child_locations,
					"module"		=> $this->module_presentation."SEARCH",
					"identifier"	=> $identifier,
					"all_locations"	=> $all_locations,
					"delete"		=> 0
				)
			);
			$this->set_inheritance(
				$this->module_presentation."SEARCH",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"=> $this->module_presentation."SEARCH",
					"condition"=> "ibs_set_inheritance =1 and ",
					"client_field"=> "ibs_client",
					"table"	=> $this->module_presentation."SEARCH",
					"primary"=> "ibs_identifier"
					)
				)."");

		}
		
		$this->tidyup_display_commands(
			Array(
				"tidy_table" 		=> "information_search",
				"tidy_field_starter"=> "ibs_",
				"tidy_webobj"		=> $this->module_presentation."SEARCH",
				"tidy_module"		=> $this->module_presentation."SEARCH",
				"all_locations"		=> $all_locations
			)
		);
	}
	/*************************************************************************************************************************
    *										A D V A N C E D   S E A R C H 
    *************************************************************************************************************************/
	
	
    /*************************************************************************************************************************
    * manage advanced search functionality
    *************************************************************************************************************************/
	function manage_advanced_search($parameters){
		$list		  		= $this->check_parameters($parameters,"identifier",-1);
		$all_locations		= 0;
		$set_inheritance	= 0;
		$display_tab		= "";
		$label				= "";
		$identifier			= -1;
		$sql = "select * from information_advanced_search where ias_client=$this->client_identifier and ias_list=$list";
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
            $identifier			= $r["ias_identifier"];
            $label				= $r["ias_label"];
           	$all_locations		= $r["ias_all_locations"];
			$set_inheritance	= $r["ias_set_inheritance"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
			Array(
			"module"		=> $this->module_presentation."ADVANCED_SEARCH",
			"identifier"	=> $identifier
			)
		);
		$out ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Search Manager - Basic]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"Manage basic search\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."ADVANCED_SEARCH_SAVE\" />";
		$out .="		<input type=\"hidden\" name=\"list\" value=\"$list\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<page_sections>";
		$out .="			<section label='Setup'>";
		$out .="				<input required=\"YES\" type=\"text\" name=\"label\" label=\"Search Label\" size=\"255\"><![CDATA[$label]]></input>";
		$web_containers = split("~----~",$this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>$this->module_command."ADVANCED_SEARCH", "identifier"=>$identifier)));
		if ($web_containers[0]!=""){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}
		$out .="			</section>";
		/*************************************************************************************************************************
		* select the menu locatiosn for this to appear
	    *************************************************************************************************************************/
		$out .= 			$this->location_tab($all_locations, $set_inheritance,$menu_locations, $display_tab);
		/*************************************************************************************************************************
		* select the fields that are to appear in the advanced search
	    *************************************************************************************************************************/
        /*
       	$out .="			<section label='Select fields'>";
		$sql = "select distinct if_name, if_label from information_fields where if_client = $this->client_identifier and if_screen =3 and if_list = $list";
   	    $result  = $this->parent->db_pointer->database_query($sql);
   		$out.="<checkboxes label='Choose the fields that are to be made available via the Advanced search' name='Searchfields'>";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
       		$out.="<option value='".$r["if_name"]."'";
			$out.=" selected='true'";
			$out.="><![CDATA[".$r["if_label"]."]]></option>";
   	    }
   		$out.="</checkboxes>";
        $this->parent->db_pointer->database_free_result($result);
	    $out .="			</section>";
	    *************************************************************************************************************************/
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;

	}
	function manage_advanced_search_save($parameters){
		$identifier				= $this->check_parameters($parameters,"identifier",-1);
		$list					= $this->check_parameters($parameters,"list",-1);
		$label					= $this->validate($this->check_parameters($parameters,"label"));
		//	locations tab
		$all_locations			= $this->check_parameters($parameters, "all_locations");
		$menu_locations			= $this->check_parameters($parameters, "menu_locations", Array());
		$set_inheritance		= $this->check_parameters($parameters, "set_inheritance");
		// web container list
		$replacelist			= $this->check_parameters($parameters, "web_containers",Array());
		$currentlyhave			= $this->check_parameters($parameters, "currentlyhave");
		$count_rss_containers	= $this->check_parameters($parameters, "totalnumberofchecks_web_containers");


		$now = $this->LibertasGetDate();
		//print $identifier;
		//print_r($parameters);
		if ($identifier==-1){
			$ias_identifier = $this->getUid();
			$cmd			= "INSERT";
			$sql 			= "
					insert into information_advanced_search (
						ias_identifier,
						ias_client,
						ias_list,
						ias_label,
						ias_date_created,
						ias_all_locations,
						ias_set_inheritance 
					) values (
						$ias_identifier,
						'$this->client_identifier',
						'$list',
						'$label',
						'$now',
						'$all_locations', 
						'$set_inheritance'
				)";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		//
			$this->parent->db_pointer->database_query($sql);
           	$identifier = $ias_identifier;
            $this->parent->db_pointer->database_free_result($result);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->module_command."ADVANCED_SEARCH",
					"owner_id" 		=> $identifier,
					"label" 		=> $label,
					"wo_command"	=> $this->module_presentation."ADVANCED_SEARCH",
					"cmd"			=> "ADD",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);

		} else {
			$sql 			= "
					update information_advanced_search set
						ias_label				= '$label',
						ias_all_locations		= $all_locations,
						ias_set_inheritance		= $set_inheritance
					where 
						ias_client				= '$this->client_identifier' and 
						ias_list				= '$list' and
						ias_identifier 			= $identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		//
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->module_command."ADVANCED_SEARCH",
					"owner_id" 		=> $identifier,
					"label" 		=> $label,
					"wo_command"	=> $this->module_presentation."ADVANCED_SEARCH",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
			$cmd= "UPDATE";
		}
		/*************************************************************************************************************************
		* save updates to search form partipication as defined in this form
		*************************************************************************************************************************/ 
		$Searchfields  = $this->check_parameters($parameters,"Searchfields", Array());
		$max = count($Searchfields);
		/*************************************************************************************************************************
		* Save menu locations
		*************************************************************************************************************************/ 
		
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->module_presentation."ADVANCED_SEARCH",
				"identifier"	=> $identifier,
				"all_locations"	=> $all_locations
			)
		);
		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance($this->module_presentation."ADVANCED_SEARCH",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE",
				Array(
					"menu_locations"=>$child_locations,
					"module"		=> $this->module_presentation."ADVANCED_SEARCH",
					"identifier"	=> $identifier,
					"all_locations"	=> $all_locations,
					"delete"		=> 0
				)
			);
			$this->set_inheritance(
				$this->module_presentation."ADVANCED_SEARCH",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",
					Array(
						"module"=> $this->module_presentation."ADVANCED_SEARCH",
						"condition"=> "ias_set_inheritance =1 and ",
						"client_field"=> "ias_client",
						"table"	=> "information_advanced_search",
						"primary"=> "ias_identifier"
					)
				)."");
		}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::tidy",__LINE__,print_r(Array("tidy_table" 		=> "information_advanced_search","tidy_field_starter"=> "ias_","tidy_webobj"		=> $this->module_presentation."ADVANCED_SEARCH","all_locations"		=> $all_locations),true)));}
		$this->tidyup_display_commands(
			Array(
				"tidy_table" 		=> "information_advanced_search",
				"tidy_field_starter"=> "ias_",
				"tidy_webobj"		=> $this->module_presentation."ADVANCED_SEARCH",
				"all_locations"		=> $all_locations,
				"tidy_module"		=> $this->module_presentation."ADVANCED_SEARCH"
			)
		);
	}
	/*************************************************************************************************************************
    *										S P E C I A L   P A G E S
    *************************************************************************************************************************/

	/*************************************************************************************************************************
    * builds special pages for the information directory
	*
	* <strong>Note::</strong> only creates a2z pages when display layout is = 2
    *
    * @param string path on site to the file
    * @param integer id of information directory this will use
    * @param string path on site to the file
	* @param Integer $summary_layout
    *************************************************************************************************************************/
	function make_special($ml_id, $id, $f_title, $summary_layout){
		$root 				= $this->parent->site_directories["ROOT"];
		$module_directory	= $this->parent->site_directories["MODULE_DIR"];
		$max 				= count($this->special_webobjects);
		$sql = "select * from menu_data where menu_identifier = '$ml_id' and menu_client='$this->client_identifier' ";
		$result  = $this->parent->db_pointer->database_query($sql);
		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			$ml_url	= $r["menu_url"];
		}
		$this->parent->db_pointer->database_free_result($result);
		foreach($this->special_webobjects as $index => $value){
			if($index=="FEATURE"){
				for($i=0;$i<count($this->special_webobjects["FEATURE"]);$i++){
					$out ="<"."?php
						\$script_file	= dirname(\$_SERVER[\"SCRIPT_FILENAME\"]);
						\$site_root		= \"$root\";
						\$script		= \"index.php\";
						\$mode		 = \"EXECUTE\";
						\$extra		 = Array(";
					$ex = "";
					foreach ($this->special_webobjects["FEATURE"][$i]["extra"] as $k =>$val){
						if($ex!=""){
							$ex .=", ";
						}
						$ex .= "\"$k\"=>\"".$val."\"";
					}
					$out .= "$ex);
						\$command	 = \"".$this->special_webobjects["FEATURE"][$i]["wo_command"]."\";
						\$fake_title = \"".$this->special_webobjects["FEATURE"][$i]["label"]."\";
						require_once \"".$root."/admin/include.php\";
						require_once \"\$module_directory/included_page.php\";
					?".">";
					$file_to_use = $root."/".$this->special_webobjects["FEATURE"][$i]["file"];
//					print "<li>$file_to_use</li>";
					$fp = fopen($file_to_use,"w");
					fwrite($fp, $out);
					fclose($fp);
					$old_umask = umask(0);
					@chmod($file_to_use,LS__FILE_PERMISSION);
					umask($old_umask);
				}
			} else if($value["available"]==1){
				$out ="<"."?php
\$script_file	= dirname(\$_SERVER[\"SCRIPT_FILENAME\"]);
\$root 			= '$root';
\$site_root		= \$_SERVER[\"DOCUMENT_ROOT\"];
\$script		= get(\$script_file, \$root, \$site_root);
\$mode		= \"EXECUTE\";
\$command	 = \"".$value["wo_command"]."\";
\$identifier = \"$id\";
\$fake_title = \"".$value["label"]."\";
require_once \"".$root."/admin/include.php\";
require_once \"\$module_directory/included_page.php\";

function get(\$sfile, \$rt, \$sroot){
	if (strpos(\$sfile,\$rt)===false){
		return substr(\$sfile, strpos(\$sfile,\$sroot)+strlen(\$sroot)+1).\"/index.php\";
	} else {
		return substr(\$sfile, strpos(\$sfile,\$rt)+strlen(\$rt)+1).\"/index.php\";
	}
}
					?".">";
				$file_to_use = $root."/".dirname($ml_url)."/".$value["file"];
				$fp = fopen($file_to_use,"w");
				fwrite($fp, $out);
				fclose($fp);
				$old_umask = umask(0);
				@chmod($file_to_use,LS__FILE_PERMISSION);
				umask($old_umask);
				if($index=="A2Z"){
					/*************************************************************************************************************************
                    * index letters
                    *************************************************************************************************************************/
					$letters = Array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","#");
					for($i=0;$i<27;$i++){
						$out ="<"."?php
\$script_file	= dirname(\$_SERVER[\"SCRIPT_FILENAME\"]);
\$root 			= '$root';
\$site_root		= \$_SERVER[\"DOCUMENT_ROOT\"];
\$script		= get(\$script_file, \$root, \$site_root);

\$mode		 = \"EXECUTE\";
\$command	 = \"".$value["wo_command"]."\";
\$extra		 = Array(
	\"letter\" 	 => \"".$letters[$i]."\"
);
\$identifier = \"$id\";
\$fake_title = \"".$value["label"]." - ".strtoupper($letters[$i])."\";
require_once \"".$root."/admin/include.php\";
require_once \"\$module_directory/included_page.php\";

function get(\$sfile, \$rt, \$sroot){
	if (strpos(\$sfile,\$rt)===false){
		return substr(\$sfile, strpos(\$sfile,\$sroot)+strlen(\$sroot)+1).\"/index.php\";
	} else {
		return substr(\$sfile, strpos(\$sfile,\$rt)+strlen(\$rt)+1).\"/index.php\";
	}
}
?".">";
						if ($letters[$i]=="#"){
							$file_to_use = $root."/".dirname($ml_url)."/_undefined.php";
						} else {
							$file_to_use = $root."/".dirname($ml_url)."/_".$letters[$i].".php";
						}
						$fp = fopen($file_to_use,"w");
						fwrite($fp, $out);
						fclose($fp);
						$old_umask = umask(0);
						@chmod($file_to_use,LS__FILE_PERMISSION);
						umask($old_umask);
					}
				}
			}
		}
		/*************************************************************************************************************************
		* is any fields filterable ??? if so create filter files
		*************************************************************************************************************************/ 
		$sql = "select * from information_list
					inner join information_fields on info_identifier = if_list and if_client = info_client and if_filterable=1
					inner join information_options on io_list = info_identifier and io_field = if_name and io_client = info_client
		where info_identifier = $id and info_client=$this->client_identifier and if_screen=0";
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$file_to_use = $root."/".dirname($ml_url)."/_filter-".$this->make_uri(urldecode($r["if_label"]))."-".$this->make_uri(urldecode($r["io_value"])).".php";
			$out ="<"."?php
\$script_file	= dirname(\$_SERVER[\"SCRIPT_FILENAME\"]);
\$root 			= '$root';
\$site_root		= \$_SERVER[\"DOCUMENT_ROOT\"];
\$script		= get(\$script_file, \$root, \$site_root);
\$mode		 = \"EXECUTE\";
\$command	 = \"".$this->module_presentation."ADVANCED_SEARCH\";
\$extra		 = Array(
	\"search\" 	 => \"1\",
	\"field\" 	 => \"".$r["if_name"]."\",
	\"filter\" 	 => \"".urldecode($r["io_value"])."\"
);
\$identifier = \"$id\";
\$fake_title = \"Filter '".$r["if_label"]." - ".urldecode($r["io_value"])."'\";
require_once \"".$root."/admin/include.php\";
require_once \"\$module_directory/included_page.php\";
function get(\$sfile, \$rt, \$sroot){
	if (strpos(\$sfile,\$rt)===false){
		return substr(\$sfile, strpos(\$sfile,\$sroot)+strlen(\$sroot)+1).\"/index.php\";
	} else {
		return substr(\$sfile, strpos(\$sfile,\$rt)+strlen(\$rt)+1).\"/index.php\";
	}
}
?".">";
			$fp = fopen($file_to_use,"w");
			fwrite($fp, $out);
			fclose($fp);
			$old_umask = umask(0);
			@chmod($file_to_use,LS__FILE_PERMISSION);
			umask($old_umask);
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
		* user registration
		*************************************************************************************************************************/ 
		foreach($this->special_webobjects as $index => $value){
			if($this->check_parameters($value,"available",0)==2){
				$out ="<"."?php
\$script_file	= dirname(\$_SERVER[\"SCRIPT_FILENAME\"]);
\$root 			= '$root';
\$site_root		= \$_SERVER[\"DOCUMENT_ROOT\"];
\$script		= get(\$script_file, \$root, \$site_root);
\$mode		 = \"EXECUTE\";
\$command	 = \"".$value["wo_command"]."\";
\$identifier = \"$id\";
\$fake_title = \"".$value["label"]."\";
require_once \"".$root."/admin/include.php\";
require_once \"\$module_directory/included_page.php\";
function get(\$sfile, \$rt, \$sroot){
	if (strpos(\$sfile,\$rt)===false){
		return substr(\$sfile, strpos(\$sfile,\$sroot)+strlen(\$sroot)+1).\"/index.php\";
	} else {
		return substr(\$sfile, strpos(\$sfile,\$rt)+strlen(\$rt)+1).\"/index.php\";
	}
}
?".">";
				$file_to_use = $root."/".dirname($ml_url)."/".$value["file"];
				$fp = fopen($file_to_use,"w");
				fwrite($fp, $out);
				fclose($fp);
				$old_umask = umask(0);
				@chmod($file_to_use,LS__FILE_PERMISSION);
				umask($old_umask);
	        }
		}
		/*************************************************************************************************************************
		* is any directory a shop
		*************************************************************************************************************************/ 
		$sql = "select * from information_list
				where info_identifier = $id and info_shop_enabled=1 and info_client=$this->client_identifier";
        $result  = $this->parent->db_pointer->database_query($sql);
		$c=0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if($c==0){
				$c++;
				$this->call_command("SHOP_CREATE_SPECIALS");
    	   	}
        }
        $this->parent->db_pointer->database_free_result($result);
	}
	/*************************************************************************************************************************
    * remove special pages form the specified menu url
	*
	* <strong>Note::</strong> only creates a2z pages when display layout is = 2
    *
    * @param string path on site to the special pages to remove
    *************************************************************************************************************************/
	function remove_special($ml_url, $info_dir){
		$root 				= $this->parent->site_directories["ROOT"];
		$module_directory	= $this->parent->site_directories["MODULE_DIR"];
		$max 				= count($this->special_webobjects);
		foreach($this->special_webobjects as $index => $value){
			$file_to_use = $root."/".$ml_url."/".$value["file"];
			@unlink($file_to_use);
		}
		/*************************************************************************************************************************
        * remove a2z
        *************************************************************************************************************************/
		$sql = "select * from information_list
					inner join information_fields on info_identifier = if_list and if_client = info_client and if_filterable=1
					inner join information_options on io_list = info_identifier and io_field = if_name and io_client = info_client
		where info_identifier = $info_dir and info_client=$this->client_identifier  and if_screen=0";
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$file_to_use = $root."/".$ml_url."/_filter-".$this->make_uri(urldecode($r["if_label"]))."-".$this->make_uri(urldecode($r["io_value"])).".php";
			@unlink($file_to_use);
	    }
	}
	
	/*************************************************************************************************************************
	*
	*************************************************************************************************************************/	
	
	
	/*************************************************************************************************************************
    * rebuid the search cache 
	*
	* @param Array keys "info_identifier"
	* @return String empty string
    *************************************************************************************************************************/
	function rebuild_search_cache($parameters){
		$info_identifier = $this->check_parameters($parameters,"info_identifier");
		$sql = "select * from information_list 
			inner join information_fields on if_list = info_identifier and if_screen=3
		where info_identifier =$info_identifier and info_client = $this->client_identifier";
        $result  = $this->parent->db_pointer->database_query($sql);
		$fields = "<seperator_row>\n<seperator>\n";
		$i=2;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$type = $r["if_type"];
			$name = $r["if_name"];
			$label = $r["if_label"];
			if($type=="rowsplitter"){
				$fields .= "		</seperator>\n	</seperator_row>\n	<seperator_row>\n		<seperator>\n";
			} else if($type=="colsplitter"){
				$fields .= "		</seperator>\n		<seperator>\n";
			} else if($type=="URL"){
			} else{
				if($i==2){
					if($type=="text" || $type=="smallmemo" || $type=="memo"){
						$fields .= "<input type='text' name='$name'><label><![CDATA[".$label."]]></label><value></value></input>\n";
					}else{
						$mnof	= $this->check_parameters($parameters,"max_number_of_fields");
						$options ="";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"[Options]",__LINE__,"$mnof - $name"));}
						for($findIndex=0;$findIndex<$mnof;$findIndex++){
							if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"[Options]",__LINE__,"hfield_".$findIndex." = ".$this->check_parameters($parameters,"hfield_".$findIndex).""));}
							if($this->check_parameters($parameters,"hfield_".$findIndex)==$name){
								$options = $this->check_parameters($parameters,"options_".$findIndex);
								if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"[Options]",__LINE__,"options_".$findIndex." - $options"));}
							}
						}
						$optionlist = split("::ls_option::", $options);
						$fields .= "<select name='$name'><label><![CDATA[".$label."]]></label>";
						$fields .= "<option value=''><![CDATA[Choose one]]></option>";
						for ($opt =0, $max = count($optionlist); $opt <$max ;$opt++){
							if ($optionlist[$opt]!=''){
								$fields .= "<option><![CDATA[".$optionlist[$opt]."]]></option>";
							}
						}
						$fields .= "</select>\n";
					}
				}else {
					if ($i==0 && $name=='ie_title'){
						$fields .= "<field id='$name' link='1'><label><![CDATA[".$label."]]></label></field>\n";
					} else {
						$fields .= "<field id='$name'><label><![CDATA[".$label."]]></label></field>\n";
					}
				}
			} 
			$fields .= "</seperator>\n</seperator_row>\n";
        }
		$lang="en";
        $this->parent->db_pointer->database_free_result($result);
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$info_identifier."_search.xml";
		$fp = @fopen($fname,"w");
		if($fp){
			fputs($fp,$fields);
			fclose($fp);
			$um = umask(0);
			@chmod($fname, LS__FILE_PERMISSION);
			umask($um);
		}
		return "";
	}
	/*************************************************************************************************************************
    * move an information directorys files to a new loaction
	*
	* @param Integer Previous menu identifier
	* @param Integer New menu identifier
	* @param Integer Information Directory identifier
    *************************************************************************************************************************/
	function move_info_dir($prev_menu_location, $current_menu_location, $info_directory){
		$root	= $this->check_parameters($this->parent->site_directories,"ROOT");
		/*************************************************************************************************************************
        * get the category list id
        *************************************************************************************************************************/
		$category=-1;
		$sql = "select * from information_list where info_client = $this->client_identifier and info_identifier = $info_directory";
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$category = $r["info_category"];
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * if a category was retrieved then move all items should be impossible not to have a category
        *************************************************************************************************************************/
		if($category!=-1){
			/*************************************************************************************************************************
	        * get the paths of the source and destination
	        *************************************************************************************************************************/
			$sql = "select * from menu_data where menu_client = $this->client_identifier and menu_identifier in ($prev_menu_location, $current_menu_location)";
	        $result  = $this->parent->db_pointer->database_query($sql);
			$source_directory 		="";
			$destination_directory	="";
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
	        	if ($r["menu_identifier"]==$prev_menu_location){
					$source_directory 		= dirname($r["menu_url"]);
				} else {
					$destination_directory	= dirname($r["menu_url"]);
				}
	        }
	        $this->parent->db_pointer->database_free_result($result);
			/*************************************************************************************************************************
			* rename all first level category directories to destination
	        *************************************************************************************************************************/
			$sql = "select * from category where cat_client = $this->client_identifier and cat_parent = $category";
	        $result  = $this->parent->db_pointer->database_query($sql);
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$caturi = $this->make_uri($r["cat_label"]); 
				@rename("$root/".$source_directory."/".$caturi, "$root/".$destination_directory."/".$caturi);
	        }
	        $this->parent->db_pointer->database_free_result($result);
			$this->remove_special($source_directory, $info_directory);
		}
	}
	
	/**
    * list the security for the fields of this database
	*
	* @param Array keys are "identifier"
	* @returns String xml representation of a form
    */
	function protect_fields($parameters){
		$identifier  = $this->check_parameters($parameters,"identifier",-1);
		if($identifier ==-1 || $this->manage_database_field_protection == 0){
			return "";
		}
		$out ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Field Protection]]></header>";
		if($this->manage_database_list==1){
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		} else {
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEXT","ENGINE_SPLASH",LOCALE_CANCEL));
		}
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"Manage field protection\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."FIELD_PROTECTION_SAVE\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<page_sections>";
		$out .="			<section label='Secure Fields'><list_of_groups>";
		$fields = Array();
		/**
        * get list of groups for this client
        */
		$sql = "select * from group_data where group_client =$this->client_identifier";
    	$result  = $this->parent->db_pointer->database_query($sql);
	    while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$out .= "<group value='".$r["group_identifier"]."'><![CDATA[".$r["group_label"]."]]></group>";
    	}
	    $this->parent->db_pointer->database_free_result($result);
		$out .= "</list_of_groups><list_of_fields>";
		/**
        * get list of fields and wether they are secured or not to any groups for this client
        */
		$sql = "select * from information_fields 
			left outer join information_field_protection on ifp_client=if_client and ifp_list=if_list  and ifp_field=if_identifier
		where if_screen=0 and if_client = $this->client_identifier and if_list = $identifier";
//		print $sql;
    	$result  = $this->parent->db_pointer->database_query($sql);
	    while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if("__NOT_FOUND__" == $this->check_parameters($fields,$r["if_name"],"__NOT_FOUND__")){
				$fields[$r["if_name"]] = Array("label" => $r["if_label"], "contains"=> Array());
			}
			$fields[$r["if_name"]]["contains"][count($fields[$r["if_name"]]["contains"])] = $r["ifp_group"];
    	}
		/**
        * get list of groups for this client
        */
		foreach ($fields as $key => $value){
			$out.="<secure_field name='$key'>
						<label><![CDATA[".$value["label"]."]]></label>";
			$m = count($fields[$key]["contains"]);
			for ($i=0;$i<$m;$i++){
				$out.="	<group><![CDATA[".$fields[$key]["contains"][$i]."]]></group>";
			}
			$out.="</secure_field>";
		}
	    $this->parent->db_pointer->database_free_result($result);
		$out .="			</list_of_fields></section>";
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";	
		return $out;
	}
	

	/**
    * save the security list for the fields of this database
	*
	* @param Array keys are "identifier"
	* @returns String xml representation of a form
    */
	function protect_fields_save($parameters){
		$identifier  = $this->check_parameters($parameters,"identifier",-1);
		if($identifier ==-1 || $this->manage_database_field_protection == 0){
			return "";
		}
		$sql = "delete from information_field_protection where ifp_client = $this->client_identifier and ifp_list=$identifier";
		$this->parent->db_pointer->database_query($sql);
		$sql= "select * from information_fields where if_screen=0 and if_list = $identifier and if_client = $this->client_identifier";
    	$result  = $this->parent->db_pointer->database_query($sql);
	    while($r = $this->parent->db_pointer->database_fetch_array($result)){
//			print "<li>checking ".$r["if_name"]."</li>";
			$list  = $this->check_parameters($parameters,$r["if_name"]);
			if($list!=""){
				$m = count($list);
//				print_r($list);
				for($index = 0; $index < $m;$index++){
					$sql = "insert into information_field_protection (ifp_client, ifp_list, ifp_group, ifp_field) values ($this->client_identifier, $identifier, ".$list[$index].", ".$r["if_identifier"].")";
					$this->parent->db_pointer->database_query($sql);
				}
			}
    	}
	    $this->parent->db_pointer->database_free_result($result);
		
		$out ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Field Protection - Saved]]></header>";
		if($this->manage_database_list==1){
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		} else {
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("NEXT","ENGINE_SPLASH",LOCALE_CANCEL));
		}
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"Manage field protection\" width=\"100%\">";
		$out .="		<page_sections>";
		$out .="			<section label='Confirm'>";
		$out .="				<text><![CDATA[Thank you the protection values were set correctly]]></text>";
		$out .="			</section>";
		$out .="		</page_sections>";
		$out .="	</form>";
		$out .="</module>";	
		return $out;
	}
	/*************************************************************************************************************************
    *  check this value truthness
	*
	* available true values are (1, yes, y, true, t)
	* @param String value representing true or false
	* @return Boolean returns 1 on true otherwise it returns 0 (false)
    *************************************************************************************************************************/
	function check_truth($value){
		$v = strtolower($value);
		if($v=="1"){
			return 1;
		} else if($v=="yes"){
			return 1;
		} else if($v=="y"){
			return 1;
		} else if($v=="true"){
			return 1;
		} else if($v=="t"){
			return 1;
		} else 
			return 0;
	}
	/*************************************************************************************************************************
    * retrieve the list of fields available
    *************************************************************************************************************************/
	function get_field_list($parameters){
		$out = Array();
		$as = $this->check_parameters($parameters,"as","XML");
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		if($identifier==-1){
			return Array();
		}
		$sql = "Select * from information_fields where if_list = $identifier and if_screen=0 and if_client = $this->client_identifier order by if_rank";
		$result  = $this->parent->db_pointer->database_query($sql);
		$i=0;
		$out[$i] = Array("name"=>"__category__", "label"=>"Category", "type"=>"__category__", "map"=>"", "auto"=>"", "required"=>"no");
		$i++;
		$out[$i] = Array("name"=>"__user__", "label"=>"", "type"=>"hidden", "map"=>"", "auto"=>"user_identifier", "required"=>"no");
		$i++;
		$out[$i] = Array("name"=>"ie_identifier", "label"=>"", "type"=>"hidden", "map"=>"", "auto"=>"", "required"=>"no");
		$i++;
		$out[$i] = Array("name"=>"ie_parent", "label"=>"", "type"=>"hidden", "map"=>"", "auto"=>"", "required"=>"no");
		$i++;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$auto = "";
			$required = "no";
			if($r["if_name"]=="ie_title"){
				$required = "yes";
			} else if($r["if_name"]=="ie_description"){
				$auto = "contact_profile";
			} else if ($r["if_name"]=="ie_uri") {
				$auto = "company_web_site";
			} else if ($r["if_name"]=="ie_email") {
				$auto = "email_address";
			} else {
				$auto = "";
			}
        	$out[$i] = Array("name"=>$r["if_name"], "label"=>$r["if_label"], "type"=>$r["if_type"], "map"=>$r["if_map"], "auto"=>$auto, "required"=>"$required");
			$i++;
        }
        $this->parent->db_pointer->database_free_result($result);
		if($as == "Array"){
			return $out;
		} else {
			$outd   = "<module name=\"".$this->module_name."\" display=\"fields\">";
			for ($index=0; $index<$i;$index++){
				$outd .= "<field>";	
				foreach($out[$index] as $key => $value){
					$outd .= "<$key><![CDATA[$value]]></$key>";	
				}
				$outd .= "</field>";	
			}
			$outd .= "</module>";	
			return $outd;
		}
	}
	/*************************************************************************************************************************
	* get a list of the options for a field
	*************************************************************************************************************************/
	function get_field_options($parameters){
		$selected  			= $this->check_parameters($parameters,"selected");
		$as 				= $this->check_parameters($parameters,"as","XML");
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		if($identifier==-1){
			return Array();
		}
//		print_r($selected);
		$field = $this->check_parameters($parameters,"field","");
		$limit = $this->check_parameters($parameters,"limit","");
		if($field!="__category__"){
			$sql = "Select * from information_options 
						inner join information_fields on if_screen=0 and if_name = io_field and if_client=io_client and io_list = if_list
					where io_list = $identifier and io_field='$field' and io_client = $this->client_identifier order by io_rank";
			$out = Array();
			$result  = $this->parent->db_pointer->database_query($sql);
			$i=0;
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$selectedValue="false";
				if(!is_array($selected)){
					if($selected==$r["io_value"]){
						$selectedValue="true";
					}
				} else {
					if(in_array($r["io_value"], $selected)){
						$selectedValue="true";
					}
				}
	        	$out[$i] = Array("name"=>$r["io_field"], "value"=>$r["io_value"], "label"=>$r["io_value"],"section"=>"","selected"=>$selectedValue);
				$i++;
	        }
	        $this->parent->db_pointer->database_free_result($result);
		} else {
			$sql = "Select * from information_list where info_identifier = $identifier and info_client = $this->client_identifier";
			$out = Array();
			$result  = $this->parent->db_pointer->database_query($sql);
			$i=0;
			$cat_id=-1;
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$cat_id 		= $r["info_category"];
				$info_cat_label	= $r["info_cat_label"];
			}
			if (count($this->loadedcat)==0){
				$data = $this->call_command("CATEGORYADMIN_LOAD", Array("list" => $cat_id, "identifier" => $cat_id, "rank"=>1,"recache"=>1, "optionList"=>1, "limit"=>$limit, "selected"=>$selected));
			}
        	$out[0] = Array("name"=>"__category__", "value"=>$data, "label"=>$info_cat_label, "section"=>"","selected"=>"false");
	        $this->parent->db_pointer->database_free_result($result);
		}
		if($as == "Array"){
			return $out;
		} else {
			$outd   = "<module name=\"".$this->module_name."\" display=\"fieldoptions\">";
			for ($index=0; $index<$i;$index++){
				$outd .= "<option>";	
				foreach($out[$index] as $key => $value){
					$outd .= "<$key><![CDATA[$value]]></$key>";	
				}
				$outd .= "</option>";	
			}
			$outd .= "</module>";	
			return $outd;
		}
	}

	/*************************************************************************************************************************
    * copy members into contact
    *************************************************************************************************************************/
	function copy_list_into_contact($parameters){
/*
			$iev_list_val = '103892517412984546';//live=102679677357981009, local=102773476623942637, 
												//local=103542500390616043 for client=41, //local=278
			$group_identifier = 278;//live=44,local=258, 
*/

/***** For Live Server Starts *****/

			$iev_list_val = '104411033734998109';//live=102679677357981009, local=102773476623942637, 
												//local=103542500390616043 for client=41, //local=278
			$group_identifier = 50;//live=44,local=258, 
			//$fbs_identifier = 104414092511592767 from formbuilder_settings	'Member Update'
			$fbs_identifier = '104414092511592767';
			$address_country = '169';//UK
			
/***** For Live Server Ends *****/
/***** For Local Server Starts *****/
/*
			$iev_list_val = '103892517412984546';//live=102679677357981009, local=102773476623942637, 
												//local=103542500390616043 for client=41, //local=278
			$group_identifier = 278;//live=44,local=258, 
			//$fbs_identifier = 104414092511592767 from formbuilder_settings	'Member Update'
			$fbs_identifier = '103896377297922250';
			$address_country = '169';//UK
*/
/***** For Local Server Ends *****/


			$sql="SELECT iev_entry,iev_field,iev_value FROM information_entry_values
					WHERE iev_client=$this->client_identifier
					AND iev_list='$iev_list_val'
					ORDER BY iev_identifier";
					
			$result 	= $this->parent->db_pointer->database_query($sql);
			$now 		= $this->libertasGetDate("Y/m/d H:i:s");
			$i = 0;
			$j = 0;
			$k = 0;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			
			$j++;
			$iev_entry = $r["iev_entry"];
			/** Count when records should be inserted portion starts*/
			if ($i==0){
				$sql_count="SELECT iev_entry,iev_field,iev_value FROM information_entry_values
						WHERE iev_client=$this->client_identifier
						AND iev_list='$iev_list_val'
						AND iev_entry='$iev_entry'
						ORDER BY iev_identifier";
				$result_count	= $this->parent->db_pointer->database_query($sql_count);
				$number_of_records_same = $this->call_command("DB_NUM_ROWS",array($result_count));
			}
			/** Count when records should be inserted portion starts*/
		/*
				if ($r["iev_field"] == "ie_otext3")
					$address_1 = $r["iev_value"];
				elseif ($r["iev_field"] == "ie_otext4")
					$address_2 = $r["iev_value"];
				elseif ($r["iev_field"] == "ie_otext5")
					$address_3 = $r["iev_value"];
				elseif ($r["iev_field"] == "ie_email1")
					$email = $r["iev_value"];
				elseif ($r["iev_field"] == "ie_otext1")
					$contact_first_name = $r["iev_value"];
				elseif ($r["iev_field"] == "ie_otext2")
					$contact_initials = $r["iev_value"];
//				elseif ($r["iev_field"] == "ie_title")
//					$surname = $r["iev_value"];
				elseif ($r["iev_field"] == "ie_otext9")
					$contact_office_tel = $r["iev_value"];
		*/

				if ($r["iev_field"] == "ie_otext3")
					$address_1 = strip_tags($r["iev_value"]);
				elseif ($r["iev_field"] == "ie_otext4")
					$address_2 = strip_tags($r["iev_value"]);
				elseif ($r["iev_field"] == "ie_otext5")
					$address_3 = strip_tags($r["iev_value"]);
				elseif ($r["iev_field"] == "ie_email1")
					$email = strip_tags($r["iev_value"]);
				elseif ($r["iev_field"] == "ie_otext2")
					$contact_first_name = strip_tags($r["iev_value"]);
				elseif ($r["iev_field"] == "ie_otext31")
					$contact_initials = strip_tags($r["iev_value"]);
//				elseif ($r["iev_field"] == "ie_title")
//					$surname = $r["iev_value"];
				elseif ($r["iev_field"] == "ie_otext9")
					$contact_office_tel = strip_tags($r["iev_value"]);
/*
				elseif ($r["iev_field"] == "ie_otext30")
					$user_login = $r["iev_value"];//Password for Information Entry Value
*/
				$i++;
				if ($i == $number_of_records_same){//if ($i == $number_of_records_same - 1)
					$user_login = $email;
					$user_login_pwd = 'libertas';
					//$password = $this->generate_random_text(12);
					$encrypt_pwd = md5($user_login_pwd);
					$i = 0;
//					$new_id = $this->getUid();
//					$sql ="delete from contact_address where ie_client = $this->client_identifier";
//					$this->parent->db_pointer->database_query($sql);

					$contact_address_uid = $this->getUID();
					$insertsql 	= "insert into contact_address 
						(address_identifier, address_1, address_2, address_3, address_created, address_client, address_country)
						values 
						('$contact_address_uid', '$address_1', '$address_2', '$address_3', '$now', '$this->client_identifier', '$address_country')";
						$this->parent->db_pointer->database_query($insertsql);
/*					$sql_sel_addr = "select max(address_identifier) from contact_address 
							where address_client = $this->client_identifier";
*/
					/*
					$sql_sel_addr = "SELECT LAST_INSERT_ID() as address_identifier";
					$result_sel_addr  = $this->parent->db_pointer->database_query($sql_sel_addr);
					$r_sel_addr = $this->parent->db_pointer->database_fetch_array($result_sel_addr);
					$contact_address = $r_sel_addr['address_identifier'];
					*/
					$contact_address = $contact_address_uid;


					$uid = $this->getUID();
					$user_uid = substr($encrypt_pwd,0,5);
/*
insert into user_info (user_identifier, user_login_name, user_login_pwd, user_status, user_creation_date, user_uid, user_client, user_date_expires, user_date_grace, user_date_review) values ('104425920086692300', 'kamranpco', '40aa886c5b51d9f100320bd695c2b5e6', '2', '2007/04/23 16:12:00', '40aa8', 2, '2007/04/23 00:00:00', '2007/04/23 00:00:00', '2007/04/23 00:00:00')

					$insert_user_info 	= "insert into user_info 
						(user_login_name, user_login_pwd, user_creation_date, user_group, user_status, user_client)
						values 
						('$user_login', '$encrypt_pwd', '$now', '0', '2', '$this->client_identifier')";
						
*/						
						if ($user_login == ""){
							$user_counter = $k + 1;
							$user_login = 'member'.$user_counter.'@irishcruisingclub.com';
							$email = $user_login;
						}
						
						/* Check User Name if alrady exists portions starts */
						$sql_sel_user_info = "select user_login_name from user_info where user_login_name = '$user_login'";
						$result_sel_user_info  = $this->parent->db_pointer->database_query($sql_sel_user_info);
						$count_user_login_name = $this->call_command("DB_NUM_ROWS",array($result_sel_user_info));
						if ($count_user_login_name >= 1){
							$user_login = substr($contact_first_name, 0, 1).$user_login;
							$email = $user_login;
						}
						/* Check User Name if alrady exists portions ends */
						$insert_user_info 	= "insert into user_info 
									(user_identifier, user_login_name, user_login_pwd, user_status, user_creation_date, user_uid, user_client, user_date_expires, user_date_grace, user_date_review)
								values 
									('$uid', '$user_login', '$encrypt_pwd', '2', '$now', '$user_uid', $this->client_identifier, '$now', '$now', '$now')";
									
						$this->parent->db_pointer->database_query($insert_user_info);

/*					$sql_sel_user_info = "select max(user_identifier) from user_info 
							where user_client = $this->client_identifier";
*/
					
					/*
					$sql_sel_user_info = "SELECT LAST_INSERT_ID() as user_identifier";
					$result_sel_user_info  = $this->parent->db_pointer->database_query($sql_sel_user_info);
					$r_sel_user_info = $this->parent->db_pointer->database_fetch_array($result_sel_user_info);
					echo $contact_user = $r_sel_user_info['user_identifier'];
					*/
					$contact_user = $uid;
					$sql_sel_metadata = "select md_title from metadata_details where md_identifier = $iev_entry";
					$result_sel_metadata  = $this->parent->db_pointer->database_query($sql_sel_metadata);
					$r_sel_metadata = $this->parent->db_pointer->database_fetch_array($result_sel_metadata);
					$surname = $r_sel_metadata['md_title'];


					$contact_data_uid = $this->getUID();
					$insert_contact_data 	= "insert into contact_data 
						(contact_identifier, contact_client, contact_user, contact_first_name, contact_last_name, contact_initials, contact_telephone, contact_date_created, contact_address)
						values 
						('$contact_data_uid','$this->client_identifier', '$contact_user', '$contact_first_name', '$surname', '$contact_initials', '$contact_office_tel', '$now', '$contact_address')";
						$this->parent->db_pointer->database_query($insert_contact_data);

/*					$sql_sel_contact_data = "SELECT LAST_INSERT_ID() as contact_identifier";
					$result_sel_contact_data  = $this->parent->db_pointer->database_query($sql_sel_contact_data);
					$r_sel_contact_data = $this->parent->db_pointer->database_fetch_array($result_sel_contact_data);
					$contact_identifier = $r_sel_contact_data['contact_identifier'];
*/
					$contact_identifier = $contact_data_uid;

					$insert_email_addresses 	= "insert into email_addresses 
						(email_client, email_address, email_rtf, email_codex, email_verified, email_date, email_contact)
						values 
						('$this->client_identifier', '$email', '0', '', '0', '$now','$contact_identifier')";
						$this->parent->db_pointer->database_query($insert_email_addresses);


					$insert_contact_company = "insert into contact_company 
						(company_client, company_address)
						values 
						('$this->client_identifier','$contact_address')";
						$this->parent->db_pointer->database_query($insert_contact_company);



/*					$insert_groups_belonging_to_user 	= "insert into groups_belonging_to_user 
						(user_identifier, client_identifier, group_identifier)
						values 
						('$contact_user', '$this->client_identifier', '$group_identifier')";
*/
					$insert_groups_belonging_to_user 	= "insert into groups_belonging_to_user 
						(group_identifier, user_identifier, client_identifier)
						values 
						('', '$contact_user', '$this->client_identifier')";
						$this->parent->db_pointer->database_query($insert_groups_belonging_to_user);

					$insert_groups_belonging_to_user 	= "insert into groups_belonging_to_user 
						(group_identifier, user_identifier, client_identifier)
						values 
						('$group_identifier', '$contact_user', '$this->client_identifier')";
						$this->parent->db_pointer->database_query($insert_groups_belonging_to_user);

/*					$insert_group_to_object = "insert into group_to_object 
						(gto_identifier, gto_object, gto_client, gto_module, gto_rank)
						values 
						('$group_identifier','$contact_user', '$this->client_identifier', 'USERS_', '0')";
*/
					$insert_group_to_object = "insert into group_to_object 
						(gto_identifier, gto_object, gto_client, gto_module, gto_rank)
						values 
						('','$contact_user', '$this->client_identifier', 'USERS_', '0')";
						$this->parent->db_pointer->database_query($insert_group_to_object);

					$insert_group_to_object = "insert into group_to_object 
						(gto_identifier, gto_object, gto_client, gto_module, gto_rank)
						values 
						('$group_identifier','$contact_user', '$this->client_identifier', 'USERS_', '0')";
						$this->parent->db_pointer->database_query($insert_group_to_object);

					$insert_user_to_object = "insert into user_to_object (uto_identifier, uto_client, uto_module, uto_object) values ($contact_user, '$this->client_identifier', 'FORMBUILDERADMIN_', '$fbs_identifier')";
						$this->parent->db_pointer->database_query($insert_user_to_object);

/*
					$ie_identifier = $this->getUid();
					$parent = $ie_identifier;
					$insert_information_entry = "insert into information_entry (ie_identifier, ie_parent, ie_client, ie_list, ie_date_created, ie_status, ie_user, ie_version_minor, ie_version_major, ie_version_wip, ie_published, ie_uri) values 
						($ie_identifier, $parent, $this->client_identifier, '$iev_list_val', '$now', '1', '$contact_user', '1', '0', '1', '1', '')";
						$this->parent->db_pointer->database_query($insert_information_entry);

*/


/*
					$insert_metadata_details = "insert into metadata_details (md_title, md_price, md_vat, md_discount, md_weight, md_quantity, md_canbuy, md_date_creation, md_date_modified, md_identifier, md_link_id, md_client, md_uri, md_module, md_link_group_id) values ('ss', '0', '0', '0', '0', '0', '0', '2007/04/23 16:12:00', '2007/04/23 16:12:00', 104425920243027766, 104425920243027766, 2, '', 'INFORMATIONADMIN_', '104425920243027766')";
						$this->parent->db_pointer->database_query($insert_metadata_details);
*/


					$update_information_entry = "update information_entry set ie_user = '$contact_user', ie_published = '1', ie_version_major = '0', ie_version_minor = '1', ie_version_wip = '1' where ie_identifier = '$iev_entry'";
						$this->parent->db_pointer->database_query($update_information_entry);


						/* Make All values Blank Portion Starts */
							$address_1 = '';
							$address_2 = '';
							$address_3 = '';
							$email = '';
							$contact_first_name = '';
							$contact_initials = '';
							$surname = '';
							$contact_office_tel = '';
							$user_login = '';
						/* Make All values Blank Portion Ends */
						$k++;
						/*if ($k >= 6)
							die();
						*/
				}
				//$i++;
			}
			$this->parent->db_pointer->database_free_result($result);
		return '';
	}

	/*************************************************************************************************************************
    * copy members into contact for uup
    *************************************************************************************************************************/
	function copy_list_into_contact_uup($parameters){
/***** For Live Server Starts *****/
			$iev_list_val = '133092434427990609';
			$group_identifier = 382;
			//$fbs_identifier = 104414092511592767 from formbuilder_settings	'Member Update'
			$fbs_identifier = '136420001524607231';
			$address_country = '169';//UK
/***** For Live Server Ends *****/
			$sql="SELECT iev_entry,iev_field,iev_value FROM information_entry_values
					WHERE iev_client=$this->client_identifier
					AND iev_list='$iev_list_val'
					ORDER BY iev_identifier";
			$result 	= $this->parent->db_pointer->database_query($sql);
			$now 		= $this->libertasGetDate("Y/m/d H:i:s");
			$i = 0;
			$j = 0;
			$k = 0;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			
			$j++;
			$iev_entry = $r["iev_entry"];
			/** Count when records should be inserted portion starts*/
			if ($i==0){
				$sql_count="SELECT iev_entry,iev_field,iev_value FROM information_entry_values
						WHERE iev_client=$this->client_identifier
						AND iev_list='$iev_list_val'
						AND iev_entry='$iev_entry'
						ORDER BY iev_identifier";
				$result_count	= $this->parent->db_pointer->database_query($sql_count);
				$number_of_records_same = $this->call_command("DB_NUM_ROWS",array($result_count));
			}
			/** Count when records should be inserted portion starts*/

				/*if ($r["iev_field"] == "ie_otext3")
					$address_1 = strip_tags($r["iev_value"]);
				elseif ($r["iev_field"] == "ie_otext4")
					$address_2 = strip_tags($r["iev_value"]);
				elseif ($r["iev_field"] == "ie_otext5")
					$address_3 = strip_tags($r["iev_value"]);
				else*/
				if ($r["iev_field"] == "ie_email1")
					$email = strip_tags($r["iev_value"]);
				elseif ($r["iev_field"] == "ie_otext2")
					$contact_first_name = strip_tags($r["iev_value"]);
				//elseif ($r["iev_field"] == "ie_otext31")
					//$contact_initials = strip_tags($r["iev_value"]);
//				elseif ($r["iev_field"] == "ie_title")
//					$surname = $r["iev_value"];
				//elseif ($r["iev_field"] == "ie_otext9")
					//$contact_office_tel = strip_tags($r["iev_value"]);
/*
				elseif ($r["iev_field"] == "ie_otext30")
					$user_login = $r["iev_value"];//Password for Information Entry Value
*/
				$i++;
				if ($i == $number_of_records_same){//if ($i == $number_of_records_same - 1)
					$user_login = $email;
					$user_login_pwd = 'libertas';
					//$password = $this->generate_random_text(12);
					$encrypt_pwd = md5($user_login_pwd);
					$i = 0;
//					$new_id = $this->getUid();
//					$sql ="delete from contact_address where ie_client = $this->client_identifier";
//					$this->parent->db_pointer->database_query($sql);

					$contact_address_uid = $this->getUID();
					$insertsql 	= "insert into contact_address 
						(address_identifier, address_created, address_client, address_country)
						values 
						('$contact_address_uid', '$now', '$this->client_identifier', '$address_country')";
						$this->parent->db_pointer->database_query($insertsql);
/*					$sql_sel_addr = "select max(address_identifier) from contact_address 
							where address_client = $this->client_identifier";
*/
					$contact_address = $contact_address_uid;
					$uid = $this->getUID();
					$user_uid = substr($encrypt_pwd,0,5);
/*
insert into user_info (user_identifier, user_login_name, user_login_pwd, user_status, user_creation_date, user_uid, user_client, user_date_expires, user_date_grace, user_date_review) values ('104425920086692300', 'kamranpco', '40aa886c5b51d9f100320bd695c2b5e6', '2', '2007/04/23 16:12:00', '40aa8', 2, '2007/04/23 00:00:00', '2007/04/23 00:00:00', '2007/04/23 00:00:00')

					$insert_user_info 	= "insert into user_info 
						(user_login_name, user_login_pwd, user_creation_date, user_group, user_status, user_client)
						values 
						('$user_login', '$encrypt_pwd', '$now', '0', '2', '$this->client_identifier')";
						
*/						
						if ($user_login == ""){
							$user_counter = $k + 1;
							$user_login = 'member'.$user_counter.'@uup.com';
							$email = $user_login;
						}
						
						/* Check User Name if alrady exists portions starts */
						$sql_sel_user_info = "select user_login_name from user_info where user_login_name = '$user_login'";
						$result_sel_user_info  = $this->parent->db_pointer->database_query($sql_sel_user_info);
						$count_user_login_name = $this->call_command("DB_NUM_ROWS",array($result_sel_user_info));
						if ($count_user_login_name >= 1){
							$user_login = substr($contact_first_name, 0, 1).$user_login;
							$email = $user_login;
						}
						/* Check User Name if alrady exists portions ends */
						$insert_user_info 	= "insert into user_info 
									(user_identifier, user_login_name, user_login_pwd, user_status, user_creation_date, user_uid, user_client, user_date_expires, user_date_grace, user_date_review)
								values 
									('$uid', '$user_login', '$encrypt_pwd', '2', '$now', '$user_uid', $this->client_identifier, '$now', '$now', '$now')";
									
						$this->parent->db_pointer->database_query($insert_user_info);
					$contact_user = $uid;
					$sql_sel_metadata = "select md_title from metadata_details where md_identifier = $iev_entry";
					$result_sel_metadata  = $this->parent->db_pointer->database_query($sql_sel_metadata);
					$r_sel_metadata = $this->parent->db_pointer->database_fetch_array($result_sel_metadata);
					$surname = $r_sel_metadata['md_title'];


					$contact_data_uid = $this->getUID();
					$insert_contact_data 	= "insert into contact_data 
						(contact_identifier, contact_client, contact_user, contact_first_name, contact_last_name, contact_date_created, contact_address)
						values 
						('$contact_data_uid','$this->client_identifier', '$contact_user', '$contact_first_name', '$surname', '$now', '$contact_address')";
						$this->parent->db_pointer->database_query($insert_contact_data);

					$contact_identifier = $contact_data_uid;
					$insert_email_addresses 	= "insert into email_addresses 
						(email_client, email_address, email_rtf, email_codex, email_verified, email_date, email_contact)
						values 
						('$this->client_identifier', '$email', '0', '', '0', '$now','$contact_identifier')";
						$this->parent->db_pointer->database_query($insert_email_addresses);
					$insert_contact_company = "insert into contact_company 
						(company_client, company_address)
						values 
						('$this->client_identifier','$contact_address')";
						$this->parent->db_pointer->database_query($insert_contact_company);
					$insert_groups_belonging_to_user 	= "insert into groups_belonging_to_user 
						(group_identifier, user_identifier, client_identifier)
						values 
						('', '$contact_user', '$this->client_identifier')";
						$this->parent->db_pointer->database_query($insert_groups_belonging_to_user);
					$insert_groups_belonging_to_user 	= "insert into groups_belonging_to_user 
						(group_identifier, user_identifier, client_identifier)
						values 
						('$group_identifier', '$contact_user', '$this->client_identifier')";
						$this->parent->db_pointer->database_query($insert_groups_belonging_to_user);
					$insert_group_to_object = "insert into group_to_object 
						(gto_identifier, gto_object, gto_client, gto_module, gto_rank)
						values 
						('','$contact_user', '$this->client_identifier', 'USERS_', '0')";
						$this->parent->db_pointer->database_query($insert_group_to_object);
					$insert_group_to_object = "insert into group_to_object 
						(gto_identifier, gto_object, gto_client, gto_module, gto_rank)
						values 
						('$group_identifier','$contact_user', '$this->client_identifier', 'USERS_', '0')";
						$this->parent->db_pointer->database_query($insert_group_to_object);
					$insert_user_to_object = "insert into user_to_object (uto_identifier, uto_client, uto_module, uto_object) values ($contact_user, '$this->client_identifier', 'FORMBUILDERADMIN_', '$fbs_identifier')";
						$this->parent->db_pointer->database_query($insert_user_to_object);
/*
					$ie_identifier = $this->getUid();
					$parent = $ie_identifier;
					$insert_information_entry = "insert into information_entry (ie_identifier, ie_parent, ie_client, ie_list, ie_date_created, ie_status, ie_user, ie_version_minor, ie_version_major, ie_version_wip, ie_published, ie_uri) values 
						($ie_identifier, $parent, $this->client_identifier, '$iev_list_val', '$now', '1', '$contact_user', '1', '0', '1', '1', '')";
						$this->parent->db_pointer->database_query($insert_information_entry);
					$insert_metadata_details = "insert into metadata_details (md_title, md_price, md_vat, md_discount, md_weight, md_quantity, md_canbuy, md_date_creation, md_date_modified, md_identifier, md_link_id, md_client, md_uri, md_module, md_link_group_id) values ('ss', '0', '0', '0', '0', '0', '0', '2007/04/23 16:12:00', '2007/04/23 16:12:00', 104425920243027766, 104425920243027766, 2, '', 'INFORMATIONADMIN_', '104425920243027766')";
						$this->parent->db_pointer->database_query($insert_metadata_details);
*/
					$update_information_entry = "update information_entry set ie_user = '$contact_user', ie_published = '1', ie_version_major = '0', ie_version_minor = '1', ie_version_wip = '1' where ie_identifier = '$iev_entry'";
						$this->parent->db_pointer->database_query($update_information_entry);
						/* Make All values Blank Portion Starts */
							$email = '';
							$contact_first_name = '';
							$surname = '';
							$user_login = '';
						/* Make All values Blank Portion Ends */
						$k++;
						/*if ($k >= 6)
							die();
						*/
				}
				//$i++;
			}
			$this->parent->db_pointer->database_free_result($result);
		return '';
	}

	/*************************************************************************************************************************
    * Print Labels Select to Download/Print	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function print_labels($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		$download_file 	= $this->check_parameters($parameters,"download_file");
		$file_name 	= $this->check_parameters($parameters,"file_name");
		$send_email 	= $this->check_parameters($parameters,"send_email");
		if ($download_file == 1)
			$this->downloadFILE($file_name);
			/* Form Portion Starts */
		/**
		* generate the form for adding / editting the user details
		*/
		$out  ="";
//		$label_type_label = 'Select Label';
//		$out  ="<module name=\"users\" display=\"form\">";
		$out ="<page_options>";
//		$out .="\t".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",array("CANCEL","USERS_LIST",LOCALE_CANCEL));
//		$out .="\t".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",array("ACCESS","USERS_SET_ADMIN_ACCESS","SET_ACCESS"));
		$out .= "<header><![CDATA[Member Email]]></header>";
		$out .="</page_options>";
//		$out .="<form name=\"PRINT_LABELS\" method=\"post\" label=\"".$this->get_constant($label)."\">";
		$form_label 	= "Members";
		$out .="	<form name=\"user_form\" label=\"".$form_label."\" width=\"100%\">";

		$times_through++;

//		$out .="<input type=\"hidden\" name=\"user_identifier\" value=\"$user_identifier\"/>";
		$out .="<input type=\"hidden\" name=\"prev_command\" value=\"$prev_command\"/>";
		$out .="<input type=\"hidden\" name=\"command\" value=\"INFORMATIONADMIN_PRINT_LABELS_EXPORT_HANDBOOK\"/>";
//		$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_SAVE\"/>";
		$out .="<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
		$out .="<textarea hidden='YES' required=\"YES\" label=\"hidden_label\" size=\"10\" height=\"10\" name=\"ody\" type=\"RICH-TEXT\" >sss</textarea>";

		$out .="<page_sections>";
		$out .="<section label='Members' name='detail'>";
		$out .= "\t\t\t\t\t<select name=\"label_type\" label=\"".$label_type_label."\" onchange='show_hidden_label_group(this)'>\n";
		$out  .= "<option value='1' selected='true'>All Members</option>";
		$out  .= "<option value='2'>Print By Region</option>";
		$out .= "\t\t\t\t\t</select>\n";
		$out .= "\t\t\t\t\t<radio type='vertical' label='".LOCALE_LIST_OF_REGIONS_AVAILABLE_TO_PRINT."' id='region_type' name='region_type'";
		$hide_sub = -1;
		if ($hide_sub==-1){
			$out .=" hidden='YES'";
		}
		$out .= ">";
		$sql_region = "select distinct iev_value from information_entry_values where iev_client=$this->client_identifier
						and iev_field='ie_oselect2' and iev_list = '$identifier' order by iev_value";
		$result_region = $this->parent->db_pointer->database_query($sql_region);

		$i = 0;
		while ($r = $this->parent->db_pointer->database_fetch_array($result_region)){
			if ($r["iev_value"] != ""){
				$out .= "<option value='".$r["iev_value"]."'";
				if ($i==0)
					$out .= " selected='true'";
				$out .= ">".$r["iev_value"]."</option>";
				$i++;
			}
		}
		$out .= "</radio>\n";
		$out .="</section>";
		$out .="</page_sections>";
		
		if ($send_email == 1){
			$out .= "	<input iconify=\"EMAIL\" type=\"submit\" command=\"INFORMATIONADMIN_MEMBER_EMAIL_BODY\" value=\"".MEMBER_EMAIL_BODY."\"/>";
		}else{
			$out .= "	<input iconify=\"PRINT_LABELS\" type=\"submit\" command=\"INFORMATIONADMIN_PRINT_LABELS_EXPORT\" value=\"".PRINT_LABELS."\"/>";
			$out .= "\t\t\t\t\t<input iconify=\"HANDBOOK\" type=\"submit\" name=\"\" command=\"INFORMATIONADMIN_PRINT_LABELS_EXPORT_HANDBOOK\" value=\"".HANDBOOK_LABELS."\"/>\n";
			$out .= "\t\t\t\t\t<input iconify=\"YACHTS\" type=\"submit\" name=\"\" command=\"INFORMATIONADMIN_PRINT_LABELS_YACHTS\" value=\"".YACHTS_LABELS."\"/>\n";
			$out .= "\t\t\t\t\t<input iconify=\"YEARBOOK\" type=\"submit\" name=\"\" command=\"INFORMATIONADMIN_PRINT_LABELS_YEARBOOK\" value=\"".YEARBOOK_LABELS."\"/>\n";
		}
		$out .= "\t\t\t\t</form>\n";
	/* Form Potion Ends*/
	$out = "<module name=\"information_admin\" display=\"form\">$out</module>";

	return $out;;


	}

	/*************************************************************************************************************************
    * Export complete members data into word document as TAB Seperated	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function print_labels_export_complete_members_database($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		$request_arr = array(
		"region_type" => trim($this->strip_tidy($this->check_parameters($parameters,"region_type"))),
		"label_type" => trim($this->strip_tidy($this->check_parameters($parameters,"label_type")))
		);
		$label_type = $request_arr["label_type"];
		$region_type = $request_arr["region_type"];
		$tmp_dir = $this->check_parameters($this->parent->site_directories,"TMP_UPLOAD_DIR","__NOT_DEFINED__");
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$data = "";
		$sql = "select distinct ie_identifier from information_entry 
					inner join metadata_details on md_module = '$this->webContainer' and md_client=ie_client and ie_identifier = md_link_id
					inner join information_list on info_identifier = ie_list and info_client = ie_client
					left outer join user_to_object on uto_client = ie_client and uto_identifier = ie_user and uto_module='FORMBUILDERADMIN_'
					left outer join formbuilder_settings on fbs_identifier = uto_object and uto_client = fbs_client
					left outer join formbuilder_module_map on fbmm_module = '$this->webContainer'  and fbmm_client = ie_client and fbmm_link_id = ie_list and fbmm_setting = fbs_identifier
				where $status_sql ie_client = $this->client_identifier and ie_list = $identifier and ie_version_wip =1 order by md_title"; //,ie_status asc,md_date_remove desc
		$result 	= $this->parent->db_pointer->database_query($sql);
		$out = '';
		$tel_start = '';	$tel_home = '';	
		$tel_slash = '';	$tel_office = '';
		$tel_end = '';
		$flag = 1;
		while($row = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$iev_entry = $row['ie_identifier'];
				$sql_iev = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' order by iev_identifier";
			$result_iev	= $this->parent->db_pointer->database_query($sql_iev);
			/* Region */
			if ($label_type == "2"){#by_region
				$sql_iev_region = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' and iev_field = 'ie_oselect2' and iev_value = '$region_type' order by iev_identifier";
				$result_iev_region	= $this->parent->db_pointer->database_query($sql_iev_region);
				$count_region = $this->call_command("DB_NUM_ROWS",array($result_iev_region));
				if ($count_region >= 1)
					$flag = 1;
				else
					$flag = 0;
			}
			/* Region */
			if ($flag == 1){
				while($r = $this->call_command("DB_FETCH_ARRAY",Array($result_iev))){
					$iev_entry = $r["iev_entry"];
			
					if ($r["iev_field"] == "ie_otext1")
						$title = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_oselect1")
						$status = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext2")
						$fname = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext31")
						$initials = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext3")
						$address_1 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext4")
						$address_2 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext5")
						$address_3 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext6")
						$address_4 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext7")
						$address_5 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_oselect2")
						$region = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext9")
						$home_tel = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext8")
						$office_tel = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext10")
						$mobile_tel = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext11")
						$fax = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_email1")
						$email = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext12")
						$partner = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext13")
						$dob = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext14")
						$year_elected = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext16")
						$yacht_club = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext15")
						$membership_type = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext17")
						$boat_name = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext18")
						$home_port = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext19")
						$mmsi = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext20")
						$call_sign = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext21")
						$loa = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext22")
						$rig = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext23")
						$class = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext24")
						$colour = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext25")
						$sail_no = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext26")
						$designer = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext27")
						$year_built = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext28")
						$member_id = strip_tags($r["iev_value"]);
	
					$sql_sel_metadata = "select md_title from metadata_details where md_identifier = $iev_entry";
					$result_sel_metadata  = $this->parent->db_pointer->database_query($sql_sel_metadata);
					$r_sel_metadata = $this->parent->db_pointer->database_fetch_array($result_sel_metadata);
					$surname = $r_sel_metadata['md_title'];
			
				 }#while($r = $db_conn->fetchArray($result_iev)) 
				$seperator = "\t";

				$data .= $title.$seperator;
				$data .= $status.$seperator;
				$data .= $fname.$seperator;
				$data .= $initials.$seperator;
				$data .= $surname.$seperator;
				$data .= $address_1.$seperator;
				$data .= $address_2.$seperator;
				$data .= $address_3.$seperator;
				$data .= $address_4.$seperator;
				$data .= $address_5.$seperator;
				$data .= $region.$seperator;
				$data .= $office_tel.$seperator;
				$data .= $home_tel.$seperator;
				$data .= $mobile_tel.$seperator;
				$data .= $fax.$seperator;
				$data .= $email.$seperator;
				$data .= $partner.$seperator;
				$data .= $dob.$seperator;
				$data .= $year_elected.$seperator;
				$data .= $yacht_club.$seperator;
				$data .= $membership_type.$seperator;
				$data .= $boat_name.$seperator;
				$data .= $home_port.$seperator;
				$data .= $mmsi.$seperator;
				$data .= $call_sign.$seperator;
				$data .= $loa.$seperator;
				$data .= $rig.$seperator;
				$data .= $class.$seperator;
				$data .= $colour.$seperator;
				$data .= $sail_no.$seperator;
				$data .= $designer.$seperator;
				$data .= $year_built.$seperator;
				$data .= $member_id."\n";
		$out .= $data;
		unset($title,$status,$fname,$initials,$address_1,$address_2,$address_3,$address_4,$address_5,$region,$home_tel,$office_tel,$mobile_tel,$fax,$email,$partner,$dob,$year_elected,$yacht_club,$membership_type,$boat_name,$home_port,$mmsi,$call_sign,$loa,$rig,$class,$colour,$sail_no,$designer,$year_built,$member_id,$data);
		}#flag 
	}#while($row = $db_conn->fetchArray($result)) 
//		$out .= '</table>';

		
		$file_name = "exported_".$this->client_identifier."_members_backup.txt";
		$filepnt = fopen($tmp_dir.'/'.$file_name, 'w+');

		$content = "$out";
		fwrite($filepnt, $content);
		fclose($filepnt);

	$this->loadedcat = $this->call_command("INFORMATIONADMIN_PRINT_LABELS", Array("identifier"=>$identifier, "download_file"=>1, "file_name"=>$file_name));

	}


	/*************************************************************************************************************************
    * Export members into word document as Handbook Layout	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function print_labels_export_handbook($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		$request_arr = array(
		"region_type" => trim($this->strip_tidy($this->check_parameters($parameters,"region_type"))),
		"label_type" => trim($this->strip_tidy($this->check_parameters($parameters,"label_type")))
		);
		$label_type = $request_arr["label_type"];
		$region_type = $request_arr["region_type"];
		$tmp_dir = $this->check_parameters($this->parent->site_directories,"TMP_UPLOAD_DIR","__NOT_DEFINED__");

		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];

		$data = "";

		$sql = "select distinct ie_identifier from information_entry 
					inner join metadata_details on md_module = '$this->webContainer' and md_client=ie_client and ie_identifier = md_link_id
					inner join information_list on info_identifier = ie_list and info_client = ie_client
					left outer join user_to_object on uto_client = ie_client and uto_identifier = ie_user and uto_module='FORMBUILDERADMIN_'
					left outer join formbuilder_settings on fbs_identifier = uto_object and uto_client = fbs_client
					left outer join formbuilder_module_map on fbmm_module = '$this->webContainer'  and fbmm_client = ie_client and fbmm_link_id = ie_list and fbmm_setting = fbs_identifier
				where $status_sql ie_client = $this->client_identifier and ie_list = $identifier and ie_version_wip =1 order by md_title"; //,ie_status asc,md_date_remove desc

		$result 	= $this->parent->db_pointer->database_query($sql);
		$out = '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
		$tel_start = '';	$tel_home = '';	
		$tel_slash = '';	$tel_office = '';
		$tel_end = '';		$tel_mobile = '';
		$tel_slash_two = '';
		$flag = 1;
		while($row = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$iev_entry = $row['ie_identifier'];
				$sql_iev = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' order by iev_identifier";
			$result_iev	= $this->parent->db_pointer->database_query($sql_iev);
			
			/* Region */
			if ($label_type == "2"){#by_region
				$sql_iev_region = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' and iev_field = 'ie_oselect2' and iev_value = '$region_type' order by iev_identifier";
				$result_iev_region	= $this->parent->db_pointer->database_query($sql_iev_region);
				$count_region = $this->call_command("DB_NUM_ROWS",array($result_iev_region));
				if ($count_region >= 1)
					$flag = 1;
				else
					$flag = 0;
//				$r_region = $this->call_command("DB_FETCH_ARRAY",Array($result_iev_region));
			}
			/* Region */
			if ($flag == 1){
				while($r = $this->call_command("DB_FETCH_ARRAY",Array($result_iev))){
					$iev_entry = $r["iev_entry"];
			
					if ($r["iev_field"] == "ie_otext1")
						$title = strip_tags($r["iev_value"]);
					if ($r["iev_field"] == "ie_otext2")
						$fname = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext31")
						$initials = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext3")
						$address_1 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext4")
						$address_2 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext5")
						$address_3 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext6")
						$address_4 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext7")
						$address_5 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_oselect2")
						$region = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext9")
						$home_tel = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext8")
						$office_tel = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext10")
						$mobile_tel = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext14")
						$year_elected = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext17")
						$boat = strip_tags($r["iev_value"]);
	//				elseif ($r["iev_field"] == "ie_otext30")
	//					$passwd = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext12")
						$partner = strip_tags($r["iev_value"]);
					$sql_sel_metadata = "select md_title from metadata_details where md_identifier = $iev_entry";
					$result_sel_metadata  = $this->parent->db_pointer->database_query($sql_sel_metadata);
					$r_sel_metadata = $this->parent->db_pointer->database_fetch_array($result_sel_metadata);
					$surname = $r_sel_metadata['md_title'];
			
				 }#while($r = $db_conn->fetchArray($result_iev)) 
				if ($title != '')
					$title = $title;
					
				if ($surname != '')
					$data .= $surname.", ";
				if ($fname != '')
					$data .= $fname." ";
				if ($initials != '')
					$data .= $initials." ";
				if ($partner != '')
					$data .= "(".$partner."), ";
				if ($address_1 != '')
					$data .= $address_1.", ";
				if ($address_2 != '')
					$data .= $address_2.", ";
				if ($address_3 != '')
					$data .= $address_3.", ";
				if ($address_4 != '')
					$data .= $address_4.", ";
				if ($address_5 != '')
					$data .= $address_5.", ";
				/*** Client requested to remove the region (Comment By Muhammad Imran) ***/
				if (($home_tel != '' || $office_tel != '' || $mobile_tel != '') && $tel_start == '')
					$tel_start = "(";
				if ($home_tel != '')
					$tel_home = "H: ".$home_tel;
				if (($home_tel != '' && $office_tel != '') && $tel_slash == '')
					$tel_slash = " / ";
				if ($office_tel != '')
					$tel_office = "W: ".$office_tel;
				if (($home_tel != '' || $office_tel != '')  && $mobile_tel != '' && $tel_slash_two == '')
					$tel_slash_two = " / ";
				if ($mobile_tel != '')
					$tel_mobile = "M: ".$mobile_tel;
				if (($home_tel != '' || $office_tel != '' || $mobile_tel != '') && $tel_end == '')
					$tel_end = ")";
				
				$tel = $tel_start.$tel_home.$tel_slash.$tel_office.$tel_slash_two.$tel_mobile.$tel_end;
					
				$data .= $tel;
				if ($year_elected != '')
					$year_elected = $year_elected;
				if ($boat != '')
					$boat = $boat;
				$data = trim($data,", ");
				$data = str_replace("&#39;","'",str_replace("&amp;","&",$data));
				$title = str_replace("&#39;","'",str_replace("&amp;","&",$title));
				$boat = str_replace("&#39;","'",str_replace("&amp;","&",$boat));

				if ($surname != '')
					$surname = $surname;
				if ($fname != '')
					$fname = $fname;

				$surname = str_replace("&#39;","'",str_replace("&amp;","&",$surname));
				$fname = str_replace("&#39;","'",str_replace("&amp;","&",$fname));

				$sql_handbook = 'insert into information_handbook_temp(ihandtemp_client,ihandtemp_title,ihandtemp_year,ihandtemp_data,ihandtemp_boat,ihandtemp_surname,ihandtemp_fname)
				values("'.$this->client_identifier.'","'.$title.'","'.$year_elected.'","'.$data.'","'.$boat.'","'.$surname.'","'.$fname.'")';
		//		die;
				$this->parent->db_pointer->database_query($sql_handbook);
/*			
			  $out .= '<tr>
				<td width="10%"><font style="font-family:Univers Condensed;font-size:11px">'.$year_elected.'</font></td>
				<td width="80%"><font style="font-family:Univers Condensed;font-size:11px">'.$data.'</td>
				<td width="10%" align="right"><font style="font-family:Univers Condensed;font-size:11px">'.$boat.'</font></td>
			  </tr>';
*/
		unset($title,$fname,$initials,$address_1,$address_2,$address_3,$address_4,$address_5,$region,$home_tel,$office_tel,$mobile_tel,$year_elected,$boat,$partner,$data);
		unset($tel_start,$tel_home,$tel_slash,$tel_office,$tel_slash_two,$tel_mobile,$tel_end);
		}#flag 
	}#while($row = $db_conn->fetchArray($result)) 

		/* Starts Document portion2 (Added By Muhammad Imran) */
//		$sql_yearbook_sel = "select * from information_yearbook_temp where ihandtemp_entry='$iev_entry' and ihandtemp_client = '$this->client_identifier' order by ihandtemp_surname,ihandtemp_fname";
		$sql_handbook_sel = "select * from information_handbook_temp where ihandtemp_client = '$this->client_identifier' order by ihandtemp_surname,ihandtemp_fname";
		$result_handbook_sel = $this->parent->db_pointer->database_query($sql_handbook_sel);
		$count_trow = $this->call_command("DB_NUM_ROWS",array($result_handbook_sel));
		while($r_handbook_sel = $this->call_command("DB_FETCH_ARRAY",Array($result_handbook_sel))){
			$db_title = $r_handbook_sel['ihandtemp_title'];
			$db_year_elected = $r_handbook_sel['ihandtemp_year'];
			$db_data = $r_handbook_sel['ihandtemp_data'];
			$db_boat = $r_handbook_sel['ihandtemp_boat'];
/*
			if ($title != '')
				$data = $title." ";
			if ($surname != '')
				$data .= $surname;
			if ($fname != '')
				$data .= ", ".$fname;
			if ($email != '')
				$data .= "\line ".$email;
*/
			  $out .= '<tr>
				<td width="5%"><font style="font-family:Univers Condensed;font-size:11px">'.$db_title.'</font></td>
				<td width="5%"><font style="font-family:Univers Condensed;font-size:11px">'.$db_year_elected.'</font></td>
				<td width="80%"><font style="font-family:Univers Condensed;font-size:11px">'.$db_data.'</td>
				<td width="10%" align="right"><font style="font-family:Univers Condensed;font-size:11px">'.$db_boat.'</font></td>
			  </tr>';
				
			unset($db_title,$db_year_elected,$db_data,$db_boat);
		}

		/* Starts Document portion2 (Added By Muhammad Imran) */

		$out .= '</table>';

		
		$file_name = "exported_".$this->client_identifier."_members_handbook.doc";
		$filepnt = fopen($tmp_dir.'/'.$file_name, 'w+');
		$content = "<html><head></head><body>$out</body></html>";
		fwrite($filepnt, $content);
		fclose($filepnt);

		$sql_handbook_del = "delete from information_handbook_temp";
		$this->parent->db_pointer->database_query($sql_handbook_del);

	$this->loadedcat = $this->call_command("INFORMATIONADMIN_PRINT_LABELS", Array("identifier"=>$identifier, "download_file"=>1, "file_name"=>$file_name));

	}

	/*************************************************************************************************************************
    * Export members into word document as Print Labels	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function print_labels_export($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
//		print_r($parameters);
//		die();

		$request_arr = array(
		"region_type" => trim($this->strip_tidy($this->check_parameters($parameters,"region_type"))),
		"label_type" => trim($this->strip_tidy($this->check_parameters($parameters,"label_type")))
		);
		$label_type = $request_arr["label_type"];
		$region_type = $request_arr["region_type"];

//		$commands_to_execute = $this->check_parameters($parameter_array,"modules",Array());
//		$identifier		= $this->check_parameters($parameter_array,"group_identifier",-1);


		$tmp_dir = $this->check_parameters($this->parent->site_directories,"TMP_UPLOAD_DIR","__NOT_DEFINED__");

//		$root  = $this->check_parameters($this->parent->site_directories,"ROOT")."/";
//		$path		= $this->check_parameters($parameters,"path");
//		$source		= $this->check_parameters($parameters,"source");
//		$uploadsPath= $this->check_parameters($parameters,"uppath");
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];

		$data = "";
/*
		if ($label_type == "by_region"){
			$iev_entry_values_str = " left outer join information_entry_values as iev on iev_entry = ie_identifier and iev_field = 'ie_oselect1' ";
		}else{
			$iev_entry_values_str = "";
		}
*/

		$sql = "select distinct ie_identifier from information_entry 
					inner join metadata_details on md_module = '$this->webContainer' and md_client=ie_client and ie_identifier = md_link_id
					inner join information_list on info_identifier = ie_list and info_client = ie_client
					left outer join user_to_object on uto_client = ie_client and uto_identifier = ie_user and uto_module='FORMBUILDERADMIN_'
					left outer join formbuilder_settings on fbs_identifier = uto_object and uto_client = fbs_client
					left outer join formbuilder_module_map on fbmm_module = '$this->webContainer'  and fbmm_client = ie_client and fbmm_link_id = ie_list and fbmm_setting = fbs_identifier
				where $status_sql ie_client = $this->client_identifier and ie_list = $identifier and ie_version_wip =1 order by md_title"; //,ie_status asc,md_date_remove desc

/*
		$sql = "select distinct ie_identifier, user_to_object.*, ie_parent, ie_status, md_title, md_link_group_id, info_update_access,  formbuilder_module_map.*, ie_user from information_entry 
					inner join metadata_details on md_module = '$this->webContainer' and md_client=ie_client and ie_identifier = md_link_id
					inner join information_list on info_identifier = ie_list and info_client = ie_client
					left outer join user_to_object on uto_client = ie_client and uto_identifier = ie_user and uto_module='FORMBUILDERADMIN_'
					left outer join formbuilder_settings on fbs_identifier = uto_object and uto_client = fbs_client
					left outer join formbuilder_module_map on fbmm_module = '$this->webContainer'  and fbmm_client = ie_client and fbmm_link_id = ie_list and fbmm_setting = fbs_identifier
				where $status_sql ie_client = $this->client_identifier and ie_list = $identifier and ie_version_wip =1 order by md_title"; //,ie_status asc,md_date_remove desc
*/
		$result 	= $this->parent->db_pointer->database_query($sql);

        /* Starts Document portion (Added By Muhammad Imran) */
		
		require_once('libertas.rtfwriter.php');
        $rtf = new RTFWriterDoc('Member Detail', 'Muhammad Imran Mirza', 'Libertas Solutions');

        $rtf -> setPageSize('a4');
        $rtf -> setPageMargins('0.7cm', '0.9cm', '0.0cm', '0.0cm');//setPageMargins( $left, $right, $top, $bottom )

		// Fonts
        $fnt['arial'] = $rtf->newFont('Arial');

		// Colors
        $clr['blk'] = $rtf->newColor(0,0,0);                 // Red, green, and blue in decimal form
        $clr['gry'] = $rtf->newColor(128,128,128);          // Red, green, and blue in decimal form
        $clr['wht'] = $rtf->newColor(0xFF, 0xFF, 0xFF);      // Red, green, and blue in hexadecimal form
        $clr['red'] = $rtf->newColor(0xFF0000);                // RGB in a string

		// Formats
		$fmt['TableTitle'] =& $rtf->newFormat();
		$fmt['TableTitle'] -> setFont($fmt['arial'], 11, 'b');
		$fmt['TableTitle'] -> setSpace('4pt', '4pt');
        $fmt['TableTitle'] -> setCharSpacing('-0.5pt');

		$fmt['TableText'] =& $rtf->newFormat();
		$fmt['TableText'] -> setFont($fmt['arial'], 11);
//		$fmt['TableText'] -> setTab('14.5cm', 'r');
//		$fmt['TableText'] -> setIndent('0.2cm','0.2cm');
		$fmt['TableText'] -> setSpace('17pt', '0pt');
//        $fmt['TableText'] -> setLineSpace('12pt');
        $fmt['TableText'] -> setCharSpacing('-0.8pt','-0.8pt');

		$fmt['TableSpace'] =& $rtf->newFormat();
		$fmt['TableSpace'] -> setFont($fmt['arial'], 10);
		$fmt['TableSpace'] -> setSpace('0pt', '0pt');
        $fmt['TableSpace'] -> setLineSpace('6pt');
/*
        $fmt['Footer'] =& $rtf->newFormat();
        $fmt['Footer'] -> setFont($fnt['arial'], 8);
        $fmt['Footer'] -> setTab('14.5cm', 'r');

        $fmt['ListBullet'] =& $rtf->newFormat();
        $fmt['ListBullet'] -> setFont($fnt['verdana'], 10);
        $fmt['ListBullet'] -> setSpace('4pt', '4pt');        
        $fmt['ListBullet'] -> setBulleted();
		$fmt['ListBullet'] -> setLevelFont(1, $fnt['verdana'], 8);
        $fmt['ListBullet'] -> setLevelIndent(1, '0.63cm', '-0.63cm', '0.96cm');

        // Footer
        $rtf -> newFooter($fmt['Footer'], '1.5cm');
        $rtf -> write('Microsoft Office Word 2003 Rich Text Format (RTF) Specification');
        $rtf -> write('\t ');
        $rtf -> writeField('PAGE');
*/
        // Content
		//	Initialize table
        $rtf -> tableStart();
        	$rtf->setTablePadding(2,2,0,1);//setTablePadding( $top, $bottom, $left, $right )


//		$out = '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
		
		$tel_start = '';	$tel_home = '';	
		$tel_slash = '';	$tel_office = '';
		$tel_end = '';
		
		$flag = 1;
		$counter_tr = 0;
		$count_trow = $this->call_command("DB_NUM_ROWS",array($result));
		while($row = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$iev_entry = $row['ie_identifier'];
/*		
			if ($label_type == "all"){
			}elseif ($label_type == "by_region"){
			}
*/
				$sql_iev = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' order by iev_identifier";
			$result_iev	= $this->parent->db_pointer->database_query($sql_iev);
			
			/* Region */
			if ($label_type == "2"){#by_region
				$sql_iev_region = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' and iev_field = 'ie_oselect2' and iev_value = '$region_type' order by iev_identifier";
				$result_iev_region	= $this->parent->db_pointer->database_query($sql_iev_region);
				$count_region = $this->call_command("DB_NUM_ROWS",array($result_iev_region));
				if ($count_region >= 1)
					$flag = 1;
				else
					$flag = 0;
//				$r_region = $this->call_command("DB_FETCH_ARRAY",Array($result_iev_region));
			}
			/* Region */
			if ($flag == 1){
				while($r = $this->call_command("DB_FETCH_ARRAY",Array($result_iev))){
					$iev_entry = $r["iev_entry"];
			
					if ($r["iev_field"] == "ie_otext2")
						$fname = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext31")
						$initials = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext3")
						$address_1 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext4")
						$address_2 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext5")
						$address_3 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext6")
						$address_4 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext7")
						$address_5 = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_oselect2")
						$region = strip_tags($r["iev_value"]);
/*
	//				elseif ($r["iev_field"] == "ie_email1")
	//					$email = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext9")
						$home_tel = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext8")
						$office_tel = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext14")
						$year_elected = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext17")
						$boat = strip_tags($r["iev_value"]);
	//				elseif ($r["iev_field"] == "ie_otext30")
	//					$passwd = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext12")
						$partner = strip_tags($r["iev_value"]);
	//				elseif ($r["iev_field"] == "ie_otext28")
	//					$member_id = strip_tags($r["iev_value"]);
*/	
	
					$sql_sel_metadata = "select md_title from metadata_details where md_identifier = $iev_entry";
					$result_sel_metadata  = $this->parent->db_pointer->database_query($sql_sel_metadata);
					$r_sel_metadata = $this->parent->db_pointer->database_fetch_array($result_sel_metadata);
					$surname = $r_sel_metadata['md_title'];
			
				 }#while($r = $db_conn->fetchArray($result_iev)) 
			
			
			// 	$data .= $name.",".$city.",".$address."\n";
/*
				if ($surname != '')
					$data .= $surname.", ";
				if ($fname != '')
					$data .= $fname." ";
				if ($initials != '')
					$data .= $initials." (";
				if ($partner != '')
					$data .= $partner."), ";
				if ($address_1 != '')
					$data .= $address_1.", ";
				if ($address_2 != '')
					$data .= $address_2.", ";
				if ($address_3 != '')
					$data .= $address_3.", ";
				if ($address_4 != '')
					$data .= $address_4.", ";
				if ($address_5 != '')
					$data .= $address_5.", ";
				if ($region != '')
					$data .= $region.", ";
				
				if (($home_tel != '' || $office_tel != '') && $tel_start == '')
					$tel_start = "(";
				if ($home_tel != '')
					$tel_home = "H: ".$home_tel;
				if (($home_tel != '' && $office_tel != '') && $tel_slash == '')
					$tel_slash = " / ";
				if ($office_tel != '')
					$tel_office = "W: ".$office_tel;
				if (($home_tel != '' || $office_tel != '') && $tel_end == '')
					$tel_end = ")";
				
				$tel = $tel_start.$tel_home.$tel_slash.$tel_office.$tel_end;
					
				$data .= $tel;


				if ($year_elected != '')
					$year_elected = $year_elected;
				if ($boat != '')
					$boat = $boat;
		
		//		if ($email != '')
		//			$data .= $email.",";
*/

				if ($fname != '')
					$data .= $fname." ";
				if ($initials != '')
					$data .= $initials." ";
				if ($surname != '')
					$data .= $surname." ";
				if ($address_1 != '')
					$data .= "\line ".$address_1;
				if ($address_2 != '')
					$data .= "\line ".$address_2;
				if ($address_3 != '')
					$data .= "\line ".$address_3;
				if ($address_4 != '')
					$data .= "\line ".$address_4;
				if ($address_5 != '')
					$data .= "\line ".$address_5;
/*				if ($region != '')
					$data .= "<br>".$region."";
*/			

//				$data = trim($data,", ");
/*			
			  if ($counter_tr == 0){
				  $out .= '<tr>';
			  }
				$out .= '<td width="33%" valign="top" height="80"><font style="font-family:Univers Condensed;font-size:10px">'.$data.'</font></td>';
			  if ($counter_tr == 2){
				  $out .= '</tr>';
				  $out .= '<tr><td>&nbsp;</td></tr>';
			  }
*/

			// Add new Row in table
			  if ($counter_tr == 0){
				$rtf -> tableRowStart(5);                  // 5 columns in row
			  }
					$rtf->setTableBorders('t', '1.5pt');
					$rtf->setTableBorders('tblri', 0);
					$rtf->setTableRowHeight('3.7cm');

					$rtf->tableColStart('6.03cm', 't', $fmt['TableText']);

					$data = str_replace("&amp;","&",$data);
					$data = str_replace("&#39;","'",$data);

					$rtf -> write($data);
					$rtf->tableColEnd();


			  $count_trow--;
			  if ($counter_tr == 2 || $count_trow == 0){
				$rtf->tableRowEnd();
				//second row
/*				$rtf -> tableRowStart(5);                  // 5 columns in row
				$rtf->setTableBorders('tblri', 0);
				$rtf->setTableRowHeight('0.08cm');

				$rtf->tableColStart('6.02cm', 't', $fmt['TableText']);
				$rtf->tableColEnd();
				$rtf->tableColStart('6.02cm', 't', $fmt['TableText']);
				$rtf->tableColEnd();
				$rtf->tableColStart('6.02cm', 't', $fmt['TableText']);
				$rtf->tableColEnd();
				$rtf->tableColStart('6.02cm', 't', $fmt['TableText']);
				$rtf->tableColEnd();
				$rtf->tableColStart('6.02cm', 't', $fmt['TableText']);
				$rtf->tableColEnd();

				$rtf->tableRowEnd();
*/			  }else{
				$rtf->tableColStart('0.7cm', 't', $fmt['TableText']);
					$rtf -> write("");
				$rtf->tableColEnd();
			  }

			  $counter_tr++;

			  if ($counter_tr >=3)
			  	$counter_tr = 0;
				

			  
		unset($fname,$initials,$address_1,$address_2,$address_3,$address_4,$address_5,$region,$home_tel,$office_tel,$year_elected,$boat,$partner,$data);
		unset($tel_start,$tel_home,$tel_slash,$tel_office,$tel_end);
		}#flag 
	}#while($row = $db_conn->fetchArray($result)) 

		/*
				$rtf -> tableRowStart(5);                  // Three columns in row
					$rtf->setTableBorders('tblri', 0);
					$rtf->setTableRowHeight('3.7cm');

					$rtf->tableColStart('6.03cm', 't', $fmt['TableText']);
						$rtf -> write('Microsoft Technical Support');
					$rtf->tableColEnd();

					$rtf->tableColStart('0.7cm', 't', $fmt['TableText']);// seperator column 
						$rtf -> write("");
					$rtf->tableColEnd();

					$rtf->tableColStart('6.03cm', 't', $fmt['TableText']);
						$rtf -> write('Microsoft Technical Support2');
					$rtf->tableColEnd();

					$rtf->tableColStart('0.7cm', 't', $fmt['TableText']);// seperator column 
						$rtf -> write("");
					$rtf->tableColEnd();
					

					$rtf->tableColStart('6.03cm', 't', $fmt['TableText']);
						$rtf -> write('Microsoft Technical Support3');
					$rtf->tableColEnd();
				$rtf->tableRowEnd();

				$rtf -> tableRowStart(5);                  // Three columns in row
					$rtf->setTableBorders('tblri', 0);
					$rtf->setTableRowHeight('3.7cm');

					$rtf->tableColStart('6.03cm', 't', $fmt['TableText']);
						$rtf -> write('Microsoft Technical 1');
					$rtf->tableColEnd();

					$rtf->tableColStart('0.7cm', 't', $fmt['TableText']);// seperator column 
						$rtf -> write("");
					$rtf->tableColEnd();

					$rtf->tableColStart('6.03cm', 't', $fmt['TableText']);
						$rtf -> write('Microsoft Technical 2');
					$rtf->tableColEnd();

					$rtf->tableColStart('0.7cm', 't', $fmt['TableText']);// seperator column 
						$rtf -> write("");
					$rtf->tableColEnd();

					$rtf->tableColStart('6.03cm', 't', $fmt['TableText']);
						$rtf -> write('Microsoft \par Technical 3 \line a');
					$rtf->tableColEnd();
				$rtf->tableRowEnd();
			*/
				
				
		
		// end table
//		$out .= '</table>';
        $rtf->tableEnd();
		$file_name = "exported_".$this->client_identifier."_members.rtf";
        $rtf -> sendRTF($file_name);

/*		
		$file_name = "exported_".$this->client_identifier."_members.doc";
		$filepnt = fopen($tmp_dir.'/'.$file_name, 'w+');

//		$filepnt = fopen("$data_files/exported_".$this->client_identifier."_members.doc", 'w+');

		$content = "<html><head></head><body>$out</body></html>";
//		echo $content;
//		die();
		
		fwrite($filepnt, $content);
		fclose($filepnt);
*/
/*
		$out = '';
		$var_member_list = 'Download Members';
		$out 		= "
			<stat_entry>
				<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[".$var_member_list."]]></attribute>
			</stat_entry>
		";
*/

		/* Get Domain Name Portion Starts */
/*		
		$sql="select domain_name, domain_identifier				
				from domain
				where domain_client = $this->client_identifier";
		//print "<p>line :: ".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		if ($result){
			$total =0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$domain_name = $r["domain_name"];
			}
		}
*/		
		/* Get Domain Name Portion Ends */
/*
//		$root  = $this->check_parameters($this->parent->site_directories,"ROOT")."/";
//		$domain_url = $root.$uploadsPath."exported_".$this->client_identifier."_members.doc";
		$ip_addr = trim(gethostbyname($domain_name)," /");
		$domain_url = 'uploads/exported_'.$this->client_identifier.'_members.doc';
		//$domain_url = "$data_files/exported_".$this->client_identifier."_members.doc";
		//$out .= "<a href='http://$domain_url'>$domain_name</a>";
		 $out 		.= "
			<stat_entry><attribute name=\"jump_to_domain\" show=\"YES\" link=\"jump_to_domain\"><![CDATA[http://".$ip_addr."/~cruise/$domain_url]]></attribute>
			</stat_entry>";

//		echo '<a href="'.$data_files.'/exported_'.$this->client_identifier.'_members.doc">Download as .doc</a>';
		
	$show_text = 'Download Exported Members';
//	$page_options ="".$this->generate_links();	
*/	

/*
	return "<module name=\"information_admin\" display=\"stats\">$page_options
			<stat_results label=\"".$show_text."\" total=\"0\" >".$out."</stat_results>
			</module>";
		return "<script>location.href='INFORMATIONADMIN_COPY_MEMBER_INTO_WORD_DOCUMENT'</script>";
*/

//	$this->loadedcat = $this->call_command("INFORMATIONADMIN_PRINT_LABELS", Array("identifier"=>$identifier, "download_file"=>1, "file_name"=>$file_name));
	$this->loadedcat = $this->call_command("INFORMATIONADMIN_PRINT_LABELS", Array("identifier"=>$identifier));

	}

	/*************************************************************************************************************************
    * Export members into word document as Print Labels	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function print_labels_yachts($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		$request_arr = array(
		"region_type" => trim($this->strip_tidy($this->check_parameters($parameters,"region_type"))),
		"label_type" => trim($this->strip_tidy($this->check_parameters($parameters,"label_type")))
		);
		$label_type = $request_arr["label_type"];
		$region_type = $request_arr["region_type"];

		$tmp_dir = $this->check_parameters($this->parent->site_directories,"TMP_UPLOAD_DIR","__NOT_DEFINED__");
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$data = "";
		$sql = "select distinct ie_identifier from information_entry 
					inner join metadata_details on md_module = '$this->webContainer' and md_client=ie_client and ie_identifier = md_link_id
					inner join information_list on info_identifier = ie_list and info_client = ie_client
					left outer join user_to_object on uto_client = ie_client and uto_identifier = ie_user and uto_module='FORMBUILDERADMIN_'
					left outer join formbuilder_settings on fbs_identifier = uto_object and uto_client = fbs_client
					left outer join formbuilder_module_map on fbmm_module = '$this->webContainer'  and fbmm_client = ie_client and fbmm_link_id = ie_list and fbmm_setting = fbs_identifier
				where $status_sql ie_client = $this->client_identifier and ie_list = $identifier and ie_version_wip =1 order by md_title"; //,ie_status asc,md_date_remove desc
		$result 	= $this->parent->db_pointer->database_query($sql);
		$out = '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
		$out .= '<tr><td width="100%" colspan="8" valign="top" height="80"><font style="font-family:Univers Condensed;font-size:35px"><b>List of Yachts</b></font></td></tr>';
//				<td width="15%"><font style="font-family:Univers Condensed;font-size:12px"><b>Title</b></font></td>
		$out .= '<tr>
				<td width="15%"><font style="font-family:Univers Condensed;font-size:12px"><b>Yacht</b></font></td>
				<td width="15%"><font style="font-family:Univers Condensed;font-size:12px"><b>Owner</b></font></td>
				<td width="10%"><font style="font-family:Univers Condensed;font-size:12px"><b>LOA</b></font></td>
				<td width="10%"><font style="font-family:Univers Condensed;font-size:12px"><b>Hull Colour</b></font></td>
				<td width="10%"><font style="font-family:Univers Condensed;font-size:12px"><b>Sail No.</b></font></td>
				<td width="10%"><font style="font-family:Univers Condensed;font-size:12px"><b>Rig/Built</b></font></td>
				<td width="15%"><font style="font-family:Univers Condensed;font-size:12px"><b>Designer</b></font></td>
				<td width="15%"><font style="font-family:Univers Condensed;font-size:12px"><b>Class</b></font></td>
				</tr>';
		$tel_start = '';	$tel_home = '';	
		$tel_slash = '';	$tel_office = '';
		$tel_end = '';
		$flag = 1;
		$counter_tr = 0;$iia = 0;
		$arr_yachts = array();
		while($row = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$iev_entry = $row['ie_identifier'];
				$sql_iev = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' order by iev_identifier";
			$result_iev	= $this->parent->db_pointer->database_query($sql_iev);
			/* Region */
			if ($label_type == "2"){#by_region
				$sql_iev_region = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' and iev_field = 'ie_oselect2' and iev_value = '$region_type' order by iev_identifier";
				$result_iev_region	= $this->parent->db_pointer->database_query($sql_iev_region);
				$count_region = $this->call_command("DB_NUM_ROWS",array($result_iev_region));
				if ($count_region >= 1)
					$flag = 1;
				else
					$flag = 0;
			}
			/* Region */
			if ($flag == 1){
				while($r = $this->call_command("DB_FETCH_ARRAY",Array($result_iev))){
					$iev_entry = $r["iev_entry"];

					if ($r["iev_field"] == "ie_otext1")
						$title = strip_tags($r["iev_value"]);
					if ($r["iev_field"] == "ie_otext17")
						$boat = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext2")
						$fname_initials = substr(strip_tags($r["iev_value"]),0,1);
					elseif ($r["iev_field"] == "ie_otext31")
						$initials = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext22")
						$rig = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext27")
						$year_built = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext26")
						$designer = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext23")
						$class = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext21")
						$loa = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext24")
						$colour = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_otext25")
						$sailno = strip_tags($r["iev_value"]);
					$sql_sel_metadata = "select md_title from metadata_details where md_identifier = $iev_entry";
					$result_sel_metadata  = $this->parent->db_pointer->database_query($sql_sel_metadata);
					$r_sel_metadata = $this->parent->db_pointer->database_fetch_array($result_sel_metadata);
					$surname = $r_sel_metadata['md_title'];
				 }#while($r = $db_conn->fetchArray($result_iev)) 
				if ($title != '')
					$title = $title;
				if ($boat != '')
					$boat = $boat;
				if ($fname_initials != '')
					$fname_initials = $fname_initials." ";
				if ($initials != '')
					$initials = $initials." ";
				if ($surname != '')
					$surname = $surname." ";
				if ($rig != '')
					$rig = $rig;
				if ($year_built != '')
					$year_built = $year_built;
				if ($designer != '')
					$designer = $designer;
				if ($class != '')
					$class = $class;
				if ($loa != '')
					$loa = $loa;
				if ($colour != '')
					$colour = $colour;
				if ($sailno != '')
					$sailno = $sailno;
			$title = str_replace("&#39;","'",str_replace("&amp;","&",$title));
			$boat = str_replace("&#39;","'",str_replace("&amp;","&",$boat));
			$initials = str_replace("&#39;","'",str_replace("&amp;","&",$initials));
			$surname = str_replace("&#39;","'",str_replace("&amp;","&",$surname));
			$loa = str_replace("&#39;","'",str_replace("&amp;","&",$loa));
			$colour = str_replace("&#39;","'",str_replace("&amp;","&",$colour));
			$sailno = str_replace("&#39;","'",str_replace("&amp;","&",$sailno));
			$rig = str_replace("&#39;","'",str_replace("&amp;","&",$rig));
			$year_built = str_replace("&#39;","'",str_replace("&amp;","&",$year_built));
			$designer = str_replace("&#39;","'",str_replace("&amp;","&",$designer));
			$class = str_replace("&#39;","'",str_replace("&amp;","&",$class));

			if ($boat != ""){
			$iia++;
				$boa = $boat;
				$arr_yachts[$boa.$iia]  = array(
					"title"=>$title,
					"boat"=>$boat,
					"owner"=>$fname_initials.$initials.$surname,
					"loa"=>$loa,
					"colour"=>$colour,
					"sailno"=>$sailno,
					"rig_year_built"=>$rig.$year_built,
					"designer"=>$designer,
					"class"=>$class
					);
			}

			  $counter_tr++;
		unset($title,$boat,$fname_initials,$initials,$rig,$year_built,$designer,$class,$loa,$colour,$sailno);
		}#flag 
	}#while($row = $db_conn->fetchArray($result)) 

		ksort($arr_yachts);
//print_r($arr_yachts);


		/* Get boat array to show one record for more than one boat */
		$i = 0;
		foreach ($arr_yachts as $key => $val) {
			if ($val["boat"])
				$boat_arr[$i] = $val["boat"];
//			if ($val["owner"])
//				$owner_arr[] = $val["owner"];
			$i++;
		}
//		asort($boat_arr);
		/* Get boat array to show one record for more than one boat */

		$i = 0;
		$owner_stack = "";
		$loa_stack = "";
		$colour_stack = "";
		$sailno_stack = "";$rig_year_built_stack = "";
		$designer_stack = "";$class_stack = "";
		foreach ($arr_yachts as $key => $val) {
			if ($val["boat"] != $boat_arr[$i+1]){
			//Filter to show only first record for more than one row (-1 would get for first val but this would work fine only if we don't sort values)
//				  <td valign="top"><font style="font-family:Univers Condensed;font-size:12px">'.$val["title"].'</font></td>
				  $out .= '<tr>
				  <td valign="top"><font style="font-family:Univers Condensed;font-size:12px"><i>'.$val["boat"].'</i></font></td>
				  <td valign="top"><font style="font-family:Univers Condensed;font-size:12px">'.$owner_stack.$val["owner"].'</font></td>
				  <td valign="top"><font style="font-family:Univers Condensed;font-size:12px">'.$loa_stack.$val["loa"].'</font></td>
				  <td valign="top"><font style="font-family:Univers Condensed;font-size:12px">'.$colour_stack.$val["colour"].'</font></td>
				  <td valign="top"><font style="font-family:Univers Condensed;font-size:12px">'.$sailno_stack.$val["sailno"].'</font></td>
				  <td valign="top"><font style="font-family:Univers Condensed;font-size:12px">'.$rig_year_built_stack.$val["rig_year_built"].'</font></td>
				  <td valign="top"><font style="font-family:Univers Condensed;font-size:12px">'.$designer_stack.$val["designer"].'</font></td>
				  <td valign="top"><font style="font-family:Univers Condensed;font-size:12px">'.$class_stack.$val["class"].'</font></td>
				</tr>';
				$owner_stack = "";
				$loa_stack = "";
				$colour_stack = "";
				$sailno_stack = "";$rig_year_built_stack = "";
				$designer_stack = "";$class_stack = "";
			}else{
				  $owner_stack .= $val["owner"]."<br>";
				  $loa_stack .= $val["loa"]."<br>";
				  $colour_stack .= $val["colour"]."<br>";
				  $sailno_stack .= $val["sailno"]."<br>";
				  $rig_year_built_stack .= $val["rig_year_built"]."<br>";
				  $designer_stack .= $val["designer"]."<br>";
				  $class_stack .= $val["class"]."<br>";
			}
			$i++;
		}

		$out .= '</table>';
		$file_name = "exported_".$this->client_identifier."_members_yachts.doc";
		$filepnt = fopen($tmp_dir.'/'.$file_name, 'w+');

		$content = "<html><head></head><body>$out</body></html>";
		fwrite($filepnt, $content);
		fclose($filepnt);
	$this->loadedcat = $this->call_command("INFORMATIONADMIN_PRINT_LABELS", Array("identifier"=>$identifier, "download_file"=>1, "file_name"=>$file_name));

	}
	/*************************************************************************************************************************
    * Export members into word document as Year book containing only Name and Email	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function print_labels_yearbook($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");

		$request_arr = array(
		"region_type" => trim($this->strip_tidy($this->check_parameters($parameters,"region_type"))),
		"label_type" => trim($this->strip_tidy($this->check_parameters($parameters,"label_type")))
		);
		$label_type = $request_arr["label_type"];
		$region_type = $request_arr["region_type"];

		$tmp_dir = $this->check_parameters($this->parent->site_directories,"TMP_UPLOAD_DIR","__NOT_DEFINED__");

		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];

		$data = "";
		$sql = "select distinct ie_identifier from information_entry 
					inner join metadata_details on md_module = '$this->webContainer' and md_client=ie_client and ie_identifier = md_link_id
					inner join information_list on info_identifier = ie_list and info_client = ie_client
					left outer join user_to_object on uto_client = ie_client and uto_identifier = ie_user and uto_module='FORMBUILDERADMIN_'
					left outer join formbuilder_settings on fbs_identifier = uto_object and uto_client = fbs_client
					left outer join formbuilder_module_map on fbmm_module = '$this->webContainer'  and fbmm_client = ie_client and fbmm_link_id = ie_list and fbmm_setting = fbs_identifier
				where $status_sql ie_client = $this->client_identifier and ie_list = $identifier and ie_version_wip =1 order by md_title"; //,ie_status asc,md_date_remove desc
		$result 	= $this->parent->db_pointer->database_query($sql);

        /* Starts Document portion 1 (Added By Muhammad Imran) */
/*		require_once('libertas.rtfwriter.php');
        $rtf = new RTFWriterDoc('Year Book Document', 'Muhammad Imran Mirza', 'Libertas Solutions');

        $rtf -> setPageSize('a4');
        $rtf -> setPageMargins('0.7cm', '0.9cm', '0.0cm', '0.0cm');//setPageMargins( $left, $right, $top, $bottom )

		// Fonts
        $fnt['arial'] = $rtf->newFont('Arial');

		// Colors
        $clr['blk'] = $rtf->newColor(0,0,0);                 // Red, green, and blue in decimal form
        $clr['gry'] = $rtf->newColor(128,128,128);          // Red, green, and blue in decimal form
        $clr['wht'] = $rtf->newColor(0xFF, 0xFF, 0xFF);      // Red, green, and blue in hexadecimal form
        $clr['red'] = $rtf->newColor(0xFF0000);                // RGB in a string

		// Formats
		$fmt['TableTitle'] =& $rtf->newFormat();
		$fmt['TableTitle'] -> setFont($fmt['arial'], 11, 'b');
		$fmt['TableTitle'] -> setSpace('4pt', '4pt');
        $fmt['TableTitle'] -> setCharSpacing('-0.5pt');

		$fmt['TableText'] =& $rtf->newFormat();
		$fmt['TableText'] -> setFont($fmt['arial'], 11);
//		$fmt['TableText'] -> setTab('14.5cm', 'r');
//		$fmt['TableText'] -> setIndent('0.2cm','0.2cm');
		$fmt['TableText'] -> setSpace('17pt', '0pt');
//        $fmt['TableText'] -> setLineSpace('12pt');
        $fmt['TableText'] -> setCharSpacing('-0.8pt','-0.8pt');

		$fmt['TableSpace'] =& $rtf->newFormat();
		$fmt['TableSpace'] -> setFont($fmt['arial'], 10);
		$fmt['TableSpace'] -> setSpace('0pt', '0pt');
        $fmt['TableSpace'] -> setLineSpace('6pt');

        // Content
		//	Initialize table
        $rtf -> tableStart();
        	$rtf->setTablePadding(2,2,0,1);//setTablePadding( $top, $bottom, $left, $right )
*/
		$tel_start = '';	$tel_home = '';	
		$tel_slash = '';	$tel_office = '';
		$tel_end = '';
		
		$flag = 1;
		$counter_tr = 0;
        /* Ends Document portion 1 (Added By Muhammad Imran) */
		while($row = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$iev_entry = $row['ie_identifier'];
				$sql_iev = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' order by iev_identifier";
			$result_iev	= $this->parent->db_pointer->database_query($sql_iev);
			
			/* Region */
			if ($label_type == "2"){#by_region
				$sql_iev_region = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' and iev_field = 'ie_oselect2' and iev_value = '$region_type' order by iev_identifier";
				$result_iev_region	= $this->parent->db_pointer->database_query($sql_iev_region);
				$count_region = $this->call_command("DB_NUM_ROWS",array($result_iev_region));
				if ($count_region >= 1)
					$flag = 1;
				else
					$flag = 0;
			}
			/* Region */
			if ($flag == 1){
				while($r = $this->call_command("DB_FETCH_ARRAY",Array($result_iev))){
					$iev_entry = $r["iev_entry"];
			
					if ($r["iev_field"] == "ie_otext1")
						$title = strip_tags($r["iev_value"]);
					if ($r["iev_field"] == "ie_otext2")
						$fname = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_email1")
						$email = strip_tags($r["iev_value"]);

					$sql_sel_metadata = "select md_title from metadata_details where md_identifier = $iev_entry";
					$result_sel_metadata  = $this->parent->db_pointer->database_query($sql_sel_metadata);
					$r_sel_metadata = $this->parent->db_pointer->database_fetch_array($result_sel_metadata);
					$surname = $r_sel_metadata['md_title'];
			
				 }#while($r = $db_conn->fetchArray($result_iev)) 
			
				if ($title != '')
					$title = $title;
				if ($surname != '')
					$surname = $surname;
				if ($fname != '')
					$fname = $fname;
				if ($email != '')
					$email = $email;
				$title = str_replace("&#39;","'",str_replace("&amp;","&",$title));
				$surname = str_replace("&#39;","'",str_replace("&amp;","&",$surname));
				$fname = str_replace("&#39;","'",str_replace("&amp;","&",$fname));
				$email = str_replace("&#39;","'",str_replace("&amp;","&",$email));
				$sql_yearbook = 'insert into information_yearbook_temp(itemp_client,itemp_entry,itemp_title,itemp_surname,itemp_fname,itemp_email)
				values("'.$this->client_identifier.'","'.$iev_entry.'","'.$title.'","'.$surname.'","'.$fname.'","'.$email.'")';
				$this->parent->db_pointer->database_query($sql_yearbook);
		unset($title,$surname,$fname,$email,$data);
		}#flag 
	}#while($row = $db_conn->fetchArray($result)) 

		/* Starts Document portion2 (Added By Muhammad Imran) */
//		$sql_yearbook_sel = "select * from information_yearbook_temp where itemp_entry='$iev_entry' and itemp_client = '$this->client_identifier' order by itemp_surname,itemp_fname";
		$sql_yearbook_sel = "select * from information_yearbook_temp where itemp_client = '$this->client_identifier' and itemp_email != '' order by itemp_surname,itemp_fname";
		$result_yearbook_sel = $this->parent->db_pointer->database_query($sql_yearbook_sel);
//		$count_trow = $this->call_command("DB_NUM_ROWS",array($result_yearbook_sel));
//		$data_out = "";
		$out = '<table width="100%" cellspacing="0" cellpadding="0" border="0">';
		while($r_yearbook_sel = $this->call_command("DB_FETCH_ARRAY",Array($result_yearbook_sel))){
			//$title = $r_yearbook_sel['itemp_title'];
			$surname = $r_yearbook_sel['itemp_surname'];
			$fname = $r_yearbook_sel['itemp_fname'];
			$email = $r_yearbook_sel['itemp_email'];
//			if ($title != '')
//				$data = $title." ";
			if ($surname != '')
				$data = $surname;
			if ($fname != '')
				$data .= ", ".$fname;
			if ($email != '')
				$email = " ".$email;
//			$data .= "<br>";
/*				$data .= "\line ".$email;
			// Add new Row in table
			  if ($counter_tr == 0){
				$rtf -> tableRowStart(5);                  // 5 columns in row
			  }
					$rtf->setTableBorders('t', '1.5pt');
					$rtf->setTableBorders('tblri', 0);
					$rtf->setTableRowHeight('3.7cm');

					$rtf->tableColStart('6.03cm', 't', $fmt['TableText']);
*/
					$data = str_replace("&amp;","&",$data);
					$data = str_replace("&#39;","'",$data);
					$email = str_replace("&amp;","&",$email);
					$email = str_replace("&#39;","'",$email);
					
//					$out .= $data;
			  $out .= '<tr>
				<td width="20%"><font style="font-family:Univers Condensed;font-size:11px">'.$data.'</font></td>
				<td width="80%"><font style="font-family:Univers Condensed;font-size:11px">'.$email.'</font></td>
			  </tr>';
//echo $data;die;
/*					$rtf -> write($data);
					$rtf->tableColEnd();

			  $count_trow--;
			  if ($counter_tr == 2 || $count_trow == 0){
				$rtf->tableRowEnd();
				//second row
			  }else{
				$rtf->tableColStart('0.7cm', 't', $fmt['TableText']);
					$rtf -> write("");
				$rtf->tableColEnd();
			  }
			  $counter_tr++;

			  if ($counter_tr >=3)
			  	$counter_tr = 0;
*/
			unset($title,$surname,$fname,$email,$data);
		}
		$out .= '</table>';
		
//		$rtf->tableEnd();
		/* Ends Document portion2 (Added By Muhammad Imran) */
		$file_name = "exported_".$this->client_identifier."_members_yearbook.doc";
		$filepnt = fopen($tmp_dir.'/'.$file_name, 'w+');

		$content = "<html><head></head><body>$out</body></html>";
		fwrite($filepnt, $content);
		fclose($filepnt);


		$sql_yearbook_del = "delete from information_yearbook_temp";
		$this->parent->db_pointer->database_query($sql_yearbook_del);


//	$this->loadedcat = $this->call_command("INFORMATIONADMIN_PRINT_LABELS", Array("identifier"=>$identifier));
	$this->loadedcat = $this->call_command("INFORMATIONADMIN_PRINT_LABELS", Array("identifier"=>$identifier, "download_file"=>1, "file_name"=>$file_name));

	}


	/*************************************************************************************************************************
    * Type email body to send selected members all/by region	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function member_email_body($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		/**
		* over write default settings for editors with user defined settings				-
		*/
		$this->load_editors();
		$request_arr = array(
		"region_type" => trim($this->strip_tidy($this->check_parameters($parameters,"region_type"))),
		"label_type" => trim($this->strip_tidy($this->check_parameters($parameters,"label_type")))
		);
		$label_type = $request_arr["label_type"];
		$region_type = $request_arr["region_type"];
		$out = '';

		$out  ="<module name=\"MEMBER_EMAIL\" display=\"form\">";
		$out .="<form name=\"user_form\" method=\"post\" label=\"".LOCALE_MEMBER_EMAIL."\">";
		$out .="<input type=\"hidden\" name=\"command\" value=\"INFORMATIONADMIN_MEMBER_EMAIL_SEND\"/>";
		$out .="<input type=\"hidden\" name=\"label_type\" value=\"$label_type\"/>";
		$out .="<input type=\"hidden\" name=\"region_type\" value=\"$region_type\"/>";		
		$out .="<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";		
		if ($error>0){
			$out .="<text type=\"error\"><![CDATA[".LOCALE_SUPPLY_EMAIL_SUBJECT_BODY."]]></text>";			
		}

		$out .="<page_sections>";
		$out .="<section label='Message' name='email_body_section'>";
		
		$out .="<input label =\"".LOCALE_MEMBER_EMAIL_FROM_NAME."\" type=\"text\" name=\"email_from_name\"><![CDATA[]]></input>";
		$out .="<input label =\"".LOCALE_MEMBER_EMAIL_FROM_EMAIL."\" type=\"text\" name=\"email_from_email\"><![CDATA[]]></input>";
		$out .="<input label =\"".LOCALE_META_SUBJECT."\" type=\"text\" name=\"email_subject\"><![CDATA[]]></input>";

		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .="<textarea label =\"".LOCALE_MESSAGE."\" size=\"40\" height=\"18\" name=\"email_body\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'></textarea>";
//			$out .= "	<textarea required=\"YES\" label=\"".LOCALE_PAGE_CONTENT."\" size=\"40\" height=\"18\" name=\"trans_body\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$content_value]]></textarea>";
		$out .="</section>";
		$out .="</page_sections>";

		$out .="<input iconify=\"EMAIL\" type=\"submit\" value=\"".LOCALE_SEND_MAIL."\"/>";
		$out .="</form>";
		$out .="</module>";
		return $out;
	}

	/*************************************************************************************************************************
    * Send email to selected members all/by region	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function member_email_send($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		
//		print_r($parameters);
//		die();

		$request_arr = array(
		"region_type" => trim($this->strip_tidy($this->check_parameters($parameters,"region_type"))),
		"label_type" => trim($this->strip_tidy($this->check_parameters($parameters,"label_type"))),
		"email_from_name" => trim($this->strip_tidy($this->check_parameters($parameters,"email_from_name"))),
		"email_from_email" => trim($this->strip_tidy($this->check_parameters($parameters,"email_from_email"))),
		"email_subject" => trim($this->strip_tidy($this->check_parameters($parameters,"email_subject"))),
		"email_body" => trim(str_replace("\\","",$this->check_parameters($parameters,"email_body")))
		);
		

		$label_type = $request_arr["label_type"];
		$region_type = $request_arr["region_type"];
		$email_from_name = $request_arr["email_from_name"];
		$email_from_email = $request_arr["email_from_email"];
		$email_subject = $request_arr["email_subject"];
		$email_body = $request_arr["email_body"];

		$email_from_arr["from"]		= '"'.$email_from_name.'" <'.$email_from_email.'>';

		$data = "";

		$sql = "select distinct ie_identifier from information_entry 
					inner join metadata_details on md_module = '$this->webContainer' and md_client=ie_client and ie_identifier = md_link_id
					inner join information_list on info_identifier = ie_list and info_client = ie_client
					left outer join user_to_object on uto_client = ie_client and uto_identifier = ie_user and uto_module='FORMBUILDERADMIN_'
					left outer join formbuilder_settings on fbs_identifier = uto_object and uto_client = fbs_client
					left outer join formbuilder_module_map on fbmm_module = '$this->webContainer'  and fbmm_client = ie_client and fbmm_link_id = ie_list and fbmm_setting = fbs_identifier
				where $status_sql ie_client = $this->client_identifier and ie_list = $identifier and ie_version_wip =1 order by md_title"; //,ie_status asc,md_date_remove desc
		$result 	= $this->parent->db_pointer->database_query($sql);
		$out = '';
		$flag = 1;
		$counter_lop = 0;
		while($row = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$iev_entry = $row['ie_identifier'];

				$sql_iev = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' and iev_field in ('ie_otext2','ie_email1') order by iev_identifier";
			$result_iev	= $this->parent->db_pointer->database_query($sql_iev);
			/* Region */
			if ($label_type == "2"){#by_region
				$sql_iev_region = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' and iev_field = 'ie_oselect2' and iev_value = '$region_type' order by iev_identifier";
				$result_iev_region	= $this->parent->db_pointer->database_query($sql_iev_region);
				$count_region = $this->call_command("DB_NUM_ROWS",array($result_iev_region));
				if ($count_region >= 1)
					$flag = 1;
				else
					$flag = 0;
//				$r_region = $this->call_command("DB_FETCH_ARRAY",Array($result_iev_region));
			}
			/* Region */
			if ($flag == 1){
				while($r = $this->call_command("DB_FETCH_ARRAY",Array($result_iev))){
					$iev_entry = $r["iev_entry"];

					if ($r["iev_field"] == "ie_otext2")
						$fname = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_email1")
						$email = strip_tags($r["iev_value"]);
					$sql_sel_metadata = "select md_title from metadata_details where md_identifier = $iev_entry";
					$result_sel_metadata  = $this->parent->db_pointer->database_query($sql_sel_metadata);
					$r_sel_metadata = $this->parent->db_pointer->database_fetch_array($result_sel_metadata);
					$surname = $r_sel_metadata['md_title'];

				 }#while($r = $db_conn->fetchArray($result_iev)) 
			
//		$body = "Dear [[contact_first_name]],\n\n<br><br>$email_body\n\n<br><br>Cheers\n<br>".$email_from_name;
		$body = "[[contact_first_name]],\n\n<br><br>$email_body\n<br>";
/*		
		echo 'Bd:'.$body.'<br>';
		echo 'FEmail:'.$email_from_email.'<br>';
		echo 'Subject:'.$email_subject.'<br>';
		echo 'To:'.$email.'<br>';
*/
		$email_html[count($email_html)] = Array("EMAIL"=>$email, "NAME"=>$fname." ".$surname);
		$body_html									= $body;
		unset($fname,$email,$surname);
		
		}#flag 
	}#while($row = $db_conn->fetchArray($result)) 
		$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_html, "subject"=>$email_subject, "body"=>$body_html,"from"=>$email_from_arr["from"],"format"=>"HTML"));

		$out= "	<module name='information_admin' display='form'>";
		$out.="		<form name='MEMBER_email_confirm' label='".LOCALE_MEMBER_EMAIL."'><text><![CDATA[".LOCALE_SENT_MSG."<p></p>]]></text></form>";
		$out.="	</module>";

		return $out;		
	}

	/*************************************************************************************************************************
    * Download Exported Word Document	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function downloadFILE($file_name)
	{
		$tmp_dir = $this->check_parameters($this->parent->site_directories,"TMP_UPLOAD_DIR","__NOT_DEFINED__");
		$dir = $tmp_dir.'/';
		$file = $file_name;
		if ((isset($file))&&(file_exists($dir.$file))) { 
			header("Content-type: application/force-download");
			header('Content-Disposition: inline; filename="' . $dir.$file . '"');
			header("Content-Transfer-Encoding: Binary");
			header("Content-length: ".filesize($dir.$file));
			header('Content-Type: application/xml');
			header('Content-Disposition: attachment; filename="' . $file . '"'); 
			readfile($dir.$file); 
			
		}else
			echo 'There is no file at the server.';
	}
	
	/*************************************************************************************************************************
    * copy user info into contact data [To Fix forgot user/password][only for cruise site]	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function copy_user_info_into_contact_data($parameters){
			$sql="SELECT user_identifier, user_login_name FROM user_info
					WHERE user_client=$this->client_identifier
					AND user_identifier >= '105285032469919860'
					ORDER BY user_identifier";
					
			$result 	= $this->parent->db_pointer->database_query($sql);
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$contact_user = $r["user_identifier"];
				$user_login_name = $r["user_login_name"];				
				
				$sql_contact_data="SELECT contact_identifier FROM contact_data
						WHERE contact_client=$this->client_identifier
						AND contact_user='$contact_user'
						ORDER BY contact_identifier";
						
				$result_contact_data 	= $this->parent->db_pointer->database_query($sql_contact_data);
				$number_of_records_contact_data = $this->call_command("DB_NUM_ROWS",array($result_contact_data));
				$r_contact_data = $this->call_command("DB_FETCH_ARRAY",Array($result_contact_data));
				$contact_identifier = $r_contact_data["contact_identifier"];
				//echo $number_of_records_contact_data.'id'.$contact_identifier.'<br>';
				/*Email*/
				
				$sql_email_addresses="SELECT email_identifier,email_address FROM email_addresses
						WHERE email_client=$this->client_identifier
						AND email_contact='$contact_identifier'
						ORDER BY email_identifier";
						
				$result_email_addresses 	= $this->parent->db_pointer->database_query($sql_email_addresses);
				$number_of_records_email_addresses = $this->call_command("DB_NUM_ROWS",array($result_email_addresses));
				$r_email_addresses = $this->call_command("DB_FETCH_ARRAY",Array($result_email_addresses));
				$email_identifier = $r_email_addresses["email_identifier"];
				$email_address = $r_email_addresses["email_address"];
				echo $number_of_records_email_addresses.'id'.$email_identifier.'mai'.$email_address.'login'.$user_login_name.'<br>';
				
				/*
					$update_email_addresses = "update email_addresses set email_address = '$user_login_name' 
												WHERE email_client=$this->client_identifier
												AND email_contact='$contact_identifier'
												";
					$this->parent->db_pointer->database_query($update_email_addresses);
				*/
				
			}
	}

	/*************************************************************************************************************************
    * save the stored xml for the widget
    *************************************************************************************************************************/
	function widget_atoz($parameters){
        $dirid                 = $this->check_parameters($parameters,"identifier",-1);
        $complete_extract_list = "";
        $out                   = "";
	    $a2zout                = "";
        /**
        * Default setting for letters counting array
        */
        $letters= Array(
            "undefined"=>0,
            "a"=>0,
            "b"=>0,
            "c"=>0,
            "d"=>0,
            "e"=>0,
            "f"=>0,
            "g"=>0,
            "h"=>0,
            "i"=>0,
            "j"=>0,
            "k"=>0,
            "l"=>0,
            "m"=>0,
            "n"=>0,
            "o"=>0,
            "p"=>0,
            "q"=>0,
            "r"=>0,
            "s"=>0,
            "t"=>0,
            "u"=>0,
            "v"=>0,
            "w"=>0,
            "x"=>0,
            "y"=>0,
            "z"=>0
        );
		/**
        * SQL - get the information directory at this location
        */
		$sql ="select * from information_list inner join menu_data on info_menu_location=menu_identifier and menu_client=info_client where info_client = $this->client_identifier and info_identifier =$dirid";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
           	$dirid		 		  	= $r["info_identifier"];
			$cat_label			  	= $r["info_cat_label"];
			$menu_url				= $r["menu_url"];
			$label					= $r["info_label"];
        }
        $this->parent->db_pointer->database_free_result($result);
//		print "<li>".__FILE__."@".__LINE__."<p>$dirid $cat_label</p></li>";
		
        /**
        * SQL - get letter usage
        */
		$a2zout .= "<uri><![CDATA[".dirname($menu_url)."/]]></uri>";
		$a2zout .= "<label><![CDATA[$label]]></label>";
        if($dirid!=-1){
			$sql = "select * from information_fields where if_client = $this->client_identifier and if_list = $dirid and if_screen=1 order by if_rank";
            $result  = $this->parent->db_pointer->database_query($sql);
			$keymap = Array();
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$keymap[count($keymap)] = Array($r["if_name"], $r["if_map"], $r["if_label"], $r["if_rank"]);
            }
            $this->parent->db_pointer->database_free_result($result);
			$sql = "select mid(ie_uri,1,1) as letter, count(mid(ie_uri,1,1)) as total from information_entry
	                    where ie_status =1 and ie_list = $dirid and ie_client = $this->client_identifier
	                group by mid(ie_uri,1,1)
	                order by letter";
	        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
	        while ($r = $this->parent->db_pointer->database_fetch_array($result)){
	            $letter = $r["letter"];
	            $total  = $r["total"];
	            //print "<li> $letter = $total </li>";
	            if ($letter>="a" && $letter<="z"){
	                $letters[$letter] = $total;
	            } else {
	                $letters["undefined"] += $total;
	            }
	        }
	        $this->parent->db_pointer->database_free_result($result);
//			print "<li>".__FILE__."@".__LINE__."<pre>".print_r($letters,true)."</pre></li>";
	        /**
	        * output the a2z letters
	        */
	        $a2zout .= "<letters choosenletter=''>";
	        $a2zout .= "<letter count='".$letters["undefined"]."' lcase='undefined'>#</letter>";
			for ($index = 1 ; $index<=13;$index++){
				$a2zout .= "<letter count='".$letters[chr($index+96)]."' lcase='".chr($index+96)."'>".chr($index+64)."</letter>";
			}
			$a2zout .= "</letters>";
			$a2zout .= "<letters>";
			for ($index = 14 ; $index<=26;$index++){
				$a2zout .= "<letter count='".$letters[chr($index+96)]."' lcase='".chr($index+96)."'>".chr($index+64)."</letter>";
			}
			$a2zout .= "</letters>";
		}
		$lang="en";
		$out  =	"<module name=\"".$this->module_name."\" display=\"ATOZ_WIDGET\">$a2zout</module>";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$dirid."_a2z.xml";
		$fp = @fopen($fname,"w");
		if($fp){
			fputs($fp,$out);
			fclose($fp);
		}
		$um = umask(0);
		@chmod($fname, LS__FILE_PERMISSION);
		umask($um);
	}
	
	
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function save_feature_fields($identifier, $list, $fields, $iffr_label_display, $label){
		$max_number_of_fields = count($fields);
		/*************************************************************************************************************************
        * insert into database
        *************************************************************************************************************************/
		$sql = "delete from information_feature_field_rank where iffr_owner = $identifier and iffr_list = $list and iffr_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		for($i=0; $i < $max_number_of_fields; $i++){
			$sql = "insert into information_feature_field_rank 
				(iffr_owner, iffr_client, iffr_rank, iffr_field, iffr_list, iffr_label_display) values
				($identifier, $this->client_identifier, $i, '".$fields[$i]."', $list, '".$iffr_label_display[$i]."');
			";
			$this->parent->db_pointer->database_query($sql);
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		}
		/*************************************************************************************************************************
        * get the list of available fields,
        *************************************************************************************************************************/
		$sql = "select * from information_fields 
				where if_list=$list and if_screen = 0 and if_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$field_array = Array();
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$position = $r["if_name"];
			$field_array[$position] = Array();
			foreach($r as $key =>$val){
				if(!is_int($key)){
					$field_array[$position][$key] = $val;
				}
			}
        }
        $this->parent->db_pointer->database_free_result($result);
		$out_fields = "<seperator_row>\n<seperator>\n";
		for ($index=0; $index < $max_number_of_fields ; $index++){
			$field_name			= $fields[$index];
			/*************************************************************************************************************************
			* build the display form xml block (summary and content
			***********************************************************************************************************************/
//			print "<li>$field_name</li>";
			if($field_name=="__new_row__"){
				$out_fields .= "		</seperator>\n	</seperator_row>\n	<seperator_row>\n		<seperator>\n";
			} else if($field_name=="__new_column__"){
				$out_fields .= "		</seperator>\n		<seperator>\n";
			} else{
				$label				= $this->check_parameters($field_array[$field_name],"if_label");
				$label_display		= $this->check_parameters($iffr_label_display,$index,0);
				if($label_display=="undefined"){
					$label_display=1;
				}
				if ($field_name=='ie_title'){
					$out_fields .= "<field id='$field_name' link='1' displaylabel='$label_display' ><label><![CDATA[".$label."]]></label></field>\n";
				} else if($field_name=="URL"){
					$out_fields .= "<field id='field_$name' displaylabel='$label_display'><label><![CDATA[".$label."]]></label></field>\n";
				} else {
					if ($field_array[$field_name]["if_filterable"]==1){
						$filteroptions="";
						$sql = "select * from information_list
									inner join information_fields on info_identifier = if_list and if_client = info_client and if_filterable=1 and if_name='$field_name'
									inner join information_options on io_list = info_identifier and io_field = if_name and io_client = info_client
								where info_identifier = $list and info_client=$this->client_identifier and if_screen=0";
						$result  = $this->parent->db_pointer->database_query($sql);
						while($r = $this->parent->db_pointer->database_fetch_array($result)){
							$filteroptions .= "<option value='_filter-".$this->make_uri(urldecode($r["if_label"]))."-".$this->make_uri(urldecode($r["io_value"])).".php'><![CDATA[".urldecode($r["io_value"])."]]></option>";
						}
						$out_fields .= "<field id='$field_name' filter='1' displaylabel='$label_display'>";
						$out_fields .= "	<label><![CDATA[".$label."]]></label>";
						$out_fields .= "	<filteroptions>$filteroptions</filteroptions>";
						$out_fields .= "</field>\n";
					} else {
						if($field_name=='__add_to_basket__'){
							$pres = substr($this->module_presentation,0,strlen($this->module_presentation)-1);
							$out_fields .= "<field id='$field_name' webcontainer='$pres' displaylabel='$label_display'><label><![CDATA[".$label."]]></label></field>\n";
						} else {
							$out_fields .= "<field id='$field_name' displaylabel='$label_display'><label><![CDATA[".$label."]]></label></field>\n";
						}
					}
				} 
			}
		}
		$out_fields .= "</seperator>\n</seperator_row>\n";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$lang='en';
		$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$lang."_".$list."_feature_".$identifier.".xml";
		$fp = @fopen($fname,"w");
		if($fp){
			fputs($fp,$out_fields);
			fclose($fp);
		}
		$um = umask(0);
		@chmod($fname, LS__FILE_PERMISSION);
		umask($um);
//		$this->exitprogram();
	}
	
}

?>