<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Muhammad Imran Mirza
* @file libertas.email_admin.php
* @date 22 May 2007
*/
/**
* 
*/
class email_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_name				= "email_admin";								// name of module is used in configuration
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
//	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT|LOCALE_MANAGEMENT_GROUP_ACCESS|LOCALE_MANAGEMENT_GROUP_PREFS|LOCALE_MANAGEMENT_GROUP_REPORTS";		// what group does this module belong to
	var $module_name_label			= "Email Admin";
	var $module_label				= "MANAGEMENT_EMAIL_ADMIN";
	var $module_creation			= "22/05/2007";							// date module was created
	var $module_modify	 			= '$Date: 2007/05/22 17:01:12 $';
	var $module_version 			= '$Revision: 1.0 $';					// Actual version of this module
	var $module_admin				= "1";									// does this system have an administrative section
	var $module_command				= "EMAILADMIN_"; 		// all commands specifically for this module will start with this token
	var $module_display_options		= array();								// what output channels does this module have
	var $module_admin_options 		= array();								// what options are available in the admin menu
	var $module_admin_user_access 	= array();								// specify types of access for groups

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

	/**
	*  Class Methods
	*/
	
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
//		if ($this->module_debug || true){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*/
		if (strpos($user_command,$this->module_command)===0){
			/**
			* basic commands
			*/
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
				return $this->get_module_version();
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			/**
			* needed for administrative access
			*/
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/**
			* specific functions for this module
			*/
			if ($user_command == $this->module_command."LIST_DATABASES"){
				return $this->list_databases($parameter_list);
			}
			if ($user_command == $this->module_command."MEMBER_EMAIL_FIELDS_LIST"){
				return $this->member_email_fields_list($parameter_list);
			}
			if ($user_command == $this->module_command."PRINT_LABELS"){
				return $this->print_labels($parameter_list);
			}
			if ($user_command == $this->module_command."PRINT_LABELS_SAVE"){
				return $this->print_labels_save($parameter_list);
			}
			if ($user_command == $this->module_command."MEMBER_EMAIL_BODY"){
				return $this->member_email_body($parameter_list);
			}
			if ($user_command == $this->module_command."MEMBER_EMAIL_SEND"){
				return $this->member_email_send($parameter_list);
			}
			if ($user_command == $this->module_command."FIELDS_REMOVE_CONFIRM"){
				return $this->member_email_fields_remove($parameter_list);
			}
			
		}else{
			return "";// wrong command sent to system
		}
	}
	/**
	* call the initialisation function only when this module is created
	*/
	
	function initialise(){
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/*************************************************************************************************************************
		* retrieve the metadata fields
		*************************************************************************************************************************/
//		$this->metadata_fields					= $this->call_command("METADATAADMIN_GET_FIELDLIST", Array());
		$this->metadata_fields					= $this->call_command("METADATAADMIN_GET_FIELDLIST_EMAIL_ADMIN", Array());
//		print_r($this->metadata_fields);
		/*************************************************************************************************************************
		/**
		* define some access functionality
		*/
		/*	Comment By Muhammad Imran Mirza to Hide Dummy Menu Options to appear */
		/*
		$this->module_admin_options			= array(
			array($this->module_command."SELECTION", "Select Site Theme"),
			array($this->module_command."LIST", 	 "Manage Theme(s)")
		);
		$this->module_admin_user_access		= array(
			array($this->module_command."ALL", "COMPLETE_ACCESS")
		);
		*/
			/* Added By Muhammad Imran */
		if ($this->parent->db_pointer->database == 'cmstest' || $this->parent->db_pointer->database == 'system_cruise_new'){
			$this->module_admin_options[count($this->module_admin_options)] = array("INFORMATIONADMIN_PRINT_LABELS&amp;identifier=104411033734998109",LOCALE_INFORMATIONADMIN_PRINT_LABELS,"INFORMATIONADMIN_PRINT_LABELS","Reports/Print Labels");

			$this->module_admin_options[count($this->module_admin_options)] = array("INFORMATIONADMIN_PRINT_LABELS&amp;identifier=104411033734998109&amp;send_email=1",LOCALE_INFORMATIONADMIN_PRINT_LABELS,"INFORMATIONADMIN_PRINT_LABELS","Reports/Member Email");
		}

			$this->module_admin_options[count($this->module_admin_options)] = array("EMAILADMIN_MEMBER_EMAIL_FIELDS_LIST",LOCALE_EMAILADMIN_MEMBER_EMAIL_FIELDS_LIST,"EMAILADMIN_MEMBER_EMAIL_FIELDS_LIST","Reports/Advance Email Fields");

			$this->module_admin_options[count($this->module_admin_options)] = array("EMAILADMIN_MEMBER_EMAIL_BODY",LOCALE_EMAILADMIN_MEMBER_EMAIL_BODY,"EMAILADMIN_MEMBER_EMAIL_BODY","Reports/Advance Email");
			/* Added By Muhammad Imran */

	}

	/*************************************************************************************************************************
	* list_databases($parameters)
	*************************************************************************************************************************/
	function list_databases($parameters){
		/*
		if($this->manage_database_list==0){
			return "";
		}
		*/
		$sql = "select information_list.info_shop_enabled, information_list.info_identifier, information_list.info_label, information_list.info_summary_layout, information_list.info_status, menu_data.*, count(ie_parent) as total  from information_list 
					inner join menu_data on menu_identifier = info_menu_location and menu_client = info_client 
					left outer join information_entry on ie_list=info_identifier and ie_client=info_client and ie_version_wip=1
				where information_list.info_owner='INFORMATIONADMIN_' and information_list.info_client=$this->client_identifier group by info_identifier order by info_identifier desc";
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
			$i = 0;
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
//				$info_identifier		= $this->check_parameters($r,"info_identifier","Not Specified");
//				$menu_label		= $this->check_parameters($r,"menu_label","Not Specified");
				$this->list_databases[$i]["info_identifier"] = $r["info_identifier"];
				$this->list_databases[$i]["menu_label"] = $r["menu_label"];
				$i++;
			}
			return $this->list_databases;
		}
	}

	/*************************************************************************************************************************
	* member email fields list($parameters)
	*************************************************************************************************************************/
	function member_email_fields_list($parameters){  //
		$end_page 		= 1;
		$group_filter	= $this->check_parameters($parameters,"group_filter",0);
		$page			= $this->check_parameters($parameters,"page",1);
		$status			= $this->check_parameters($parameters,"status",$this->check_parameters($parameters,"identifier",-1));
		$order_filter	= $this->check_parameters($parameters,"order_filter",0);
		$filter_string	= str_replace(
							Array(" ","'"), 
							Array("%","&#39;"), 
							$this->check_parameters($parameters,"filter_string")
						  );
		$variables 		= array();	
		
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"member_email_fields_list filter parameters",__LINE__,print_r($parameters,true)));}
		
		/**
		* Procude the SQL command that will retrieve the information from the database
		*/
		$where = "";
		$w2="";
		$w="";
		$join="";
//		$sql = "SELECT * from email_member_fields where em_fields_client=$this->client_identifier order by ".$this->display_options[$order_filter][2]."";
		$sql = "SELECT * from email_member_fields where em_fields_client=$this->client_identifier";

//		print $sql;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		/**
		* what functionality options are available on this page
		*/
		$variables["PAGE_BUTTONS"] = Array(Array("ADD",$this->module_command."PRINT_LABELS",ADD_NEW));
		$variables["HEADER"] = "Member Email Fields Manager (List Fields)";
		if (!$result){
			/**
			* No Records were returned.
			*/
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			$number_of_records	= 0;
			$goto				= 0;
			$finish				= 0;
			$page				= 1;
			$num_pages			= 1;
			$start_page			= 1;
			$end_page			= 1;
		}else{
			/**
			* When some records are returned we will only return the page of results that the
			- user has requested or the first page if the user has not requested any page.
			*/
			
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$this->page_size =50;
			/**
			* Start to work out what posisition on the record set we are supposed to be at.
			*/
			$page = $this->check_parameters($parameters,"page",1);
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$goto = ((--$page)*$this->page_size);
			
			/**
			* jump down the results to the starting record for our consideration
			*/
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
			}
			/**

			* produce the variables that will be used to work out what information will be
			- displayed
			*/
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
			
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page+=$this->page_size;
			
			$counter=0;
			
			/**
			* Retrieve the actual results that are to be displayed
			*/
			$variables["RESULT_ENTRIES"] 	= Array();
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				/*
				$phone = $this->check_parameters($r,"contact_telephone","[[nbsp]]");
				if($phone==""){
					$phone="[[nbsp]]";
				}
				*/
				$em_fields_identifier = $r["em_fields_identifier"];

				$db_identifier = $r['em_fields_db_identifier'];
				
				/* Get Database Label portion starts */
				$sql_fields = "select info_label from information_list where info_client=$this->client_identifier and info_identifier ='$db_identifier'";
				$result_fields  = $this->parent->db_pointer->database_query($sql_fields);
				$r_fields = $this->parent->db_pointer->database_fetch_array($result_fields);
				$db_name = $r_fields['info_label'];
				/* Get Database Label portion ends */


				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $db_identifier,
//					"identifier"	=> $uid,
//					"db_identifier"	=> $db_identifier,
					"ENTRY_BUTTONS" => Array(),
					"attributes"	=> Array(
						Array('Field Label',	$this->check_parameters($r,"em_fields_label")),
						Array('Data Base',		$db_name)
//						Array(LOCALE_EMAIL,		$this->check_parameters($r,"email_address","[[nbsp]]"))
					)
				);

				$override_edit =0;
				if($override_edit==0){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."PRINT_LABELS&amp;edit_status=1&amp;em_fields_identifier=".$em_fields_identifier,EDIT_EXISTING);
				}
				$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."FIELDS_REMOVE_CONFIRM&amp;em_fields_identifier=".$em_fields_identifier,REMOVE_EXISTING);
			}
			/**
			* produce the XML representation of the information above.
			*/
		}
		/**
		* retrieve the page spanning information
		*/
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["as"]	= "table";
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$variables["START_PAGE"]		= $start_page;
		$variables["END_PAGE"]			= $end_page;
		/**
		* retrieve the XML information for building the filter form
		*/
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
//			$variables["FILTER"]			= $this->user_filter($parameters);
			$variables["FILTER"]			= "";
		}else{
			$variables["FILTER"]			= "";
		}
			
		$out = $this->generate_list($variables);
		
		return $out;
	}


	/*************************************************************************************************************************
	* member email fields form($parameters)
	*************************************************************************************************************************/
	function print_labels($parameters){
//		$identifier 	= $this->check_parameters($parameters,"identifier");
		$identifier 			= $this->check_parameters($parameters,"identifier",-1);
		$em_fields_identifier	= $this->check_parameters($parameters,"em_fields_identifier");
		$edit_status 			= $this->check_parameters($parameters,"edit_status",-1);
//		$db_identifier 			= $this->check_parameters($parameters,"db_identifier",-1);

		//$download_file 	= $this->check_parameters($parameters,"download_file");
		//$file_name 	= $this->check_parameters($parameters,"file_name");
		//$send_email 	= $this->check_parameters($parameters,"send_email");
		$message_label 	= $this->check_parameters($parameters,"message_label");
		$error 	= $this->check_parameters($parameters,"error");



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
//		if ($identifier!=-1 && $edit_status!=-1)
		if ($identifier!=-1){
				/* Get Database Label portion starts */
				/*
				$sql_fields = "select em_fields_db_identifier from email_member_fields where em_fields_client=$this->client_identifier and em_fields_identifier ='$identifier'";
				$result_fields  = $this->parent->db_pointer->database_query($sql_fields);
				$r_fields = $this->parent->db_pointer->database_fetch_array($result_fields);
				$db_identifier = $r_fields['em_fields_db_identifier'];
				*/
				/* Get Database Label portion ends */
		
			$form_label 	= "Edit";
			$sql = "select information_list.*, 
				mi1.mi_memo as info_confirm_screen
				from information_list 
					left outer join memo_information as mi1 on (info_client=mi1.mi_client and mi1.mi_link_id = info_identifier and mi1.mi_type='INFORMATIONADMIN_' and mi1.mi_field='confirmscreen')
			where 
				(mi1.mi_type='INFORMATIONADMIN_' or mi1.mi_type is NULL) and 
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
			$sql_mem_fields = "SELECT * from email_member_fields where em_fields_client=$this->client_identifier and em_fields_db_identifier=$identifier and em_fields_identifier = $em_fields_identifier";
			$result_mem_fields  = $this->parent->db_pointer->database_query($sql_mem_fields);
			$r_mem_fields = $this->parent->db_pointer->database_fetch_array($result_mem_fields);
			if ($edit_status == 1){
				$em_fields_label = $r_mem_fields["em_fields_label"];
				$em_fields_identifier = $r_mem_fields["em_fields_identifier"];
				if ($message_label == "")
					$message_label = $em_fields_label;
			}
			
			$em_fields_first_name = $r_mem_fields["em_fields_first_name"];
			$em_fields_surname = $r_mem_fields["em_fields_surname"];
			$em_fields_email = $r_mem_fields["em_fields_email"];
			$em_fields_region = $r_mem_fields["em_fields_region"];

//			$em_fields_arr = array($em_fields_first_name,$em_fields_surname,$em_fields_email,$em_fields_region,"Surname");
//			print_r($em_fields_arr);

			$sql = "select * from information_fields where if_client = $this->client_identifier and if_list = $identifier order by  if_screen asc, if_rank asc";
			//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				if ($edit_status == 1){
					if ($r["if_name"]=="ie_title" && $r["if_screen"]==0){
						$found = 1;
					}
					$generalKey[$r["if_screen"]][$r["if_name"]] = $r["if_name"]."-".$r["if_rank"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][0] = $r["if_label"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][1] = $current_rank;
					if ($r["if_name"] == $em_fields_first_name)
						$mapped_field = "md_first_name";
					elseif ($r["if_name"] == $em_fields_surname)
						$mapped_field = "md_surname";
					elseif ($r["if_name"] == $em_fields_email)
						$mapped_field = "md_email";
					elseif ($r["if_name"] == $em_fields_region)
						$mapped_field = "md_region";
					else
						$mapped_field = "";
						
					
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["map"] = $mapped_field;
				}else{
					if ($r["if_name"]=="ie_title" && $r["if_screen"]==0){
						$found = 1;
					}
					$generalKey[$r["if_screen"]][$r["if_name"]] = $r["if_name"]."-".$r["if_rank"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][0] = $r["if_label"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][1] = $current_rank;
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][2] = $r["if_type"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][3] = $r["if_link"];
	//				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][4] = $r["if_search_form"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["value"] = Array();
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["duplicate"] = $r["if_duplicate"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["filter"] = $r["if_filterable"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["map"] = $r["if_map"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["sumlabel"] = $r["if_sumlabel"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["conlabel"] = $r["if_conlabel"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["special"] = $r["if_special"];
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["add_to_title"] = $r["if_add_to_title"];				
					$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["url_linkfield"] = $r["if_url_linkfield"];
	//				print "<li>".$r["if_label"]." - ".$r["if_special"]."</li>";
					$current_rank++;
				}#if entry
/*
				$generalKey[$r["if_screen"]][$r["if_name"]] = $r["if_name"]."-".$r["if_rank"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][0] = $r["if_label"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][1] = $current_rank;
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][2] = $r["if_type"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][3] = $r["if_link"];
//				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]][4] = $r["if_search_form"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["value"] = Array();
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["duplicate"] = $r["if_duplicate"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["filter"] = $r["if_filterable"];
				
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["map"] = $r["if_map"];
				
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["sumlabel"] = $r["if_sumlabel"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["conlabel"] = $r["if_conlabel"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["special"] = $r["if_special"];
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["add_to_title"] = $r["if_add_to_title"];				
				$this->fields[$r["if_screen"]][$r["if_name"]."-".$r["if_rank"]]["url_linkfield"] = $r["if_url_linkfield"];
//				print "<li>".$r["if_label"]." - ".$r["if_special"]."</li>";
*/
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

//		$info_category = '105618259582940124';
//		$category_listing = $this->call_command("EMAILADMIN_DATABASE_LIST",Array("identifier"=>$info_category));
//		Get Database List
		$list_databases = $this->call_command("EMAILADMIN_LIST_DATABASES");

		$metadatafields	= "";

//		if ($download_file == 1)
//			$this->downloadFILE($file_name);
		
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
		$out .= "<header><![CDATA[Member Email Fields]]></header>";
		$out .="</page_options>";
//		$out .="<form name=\"PRINT_LABELS\" method=\"post\" label=\"".$this->get_constant($label)."\">";
		$form_label 	= "Member Email";
		$out .="	<form id=\"user_form\" name=\"user_form\" label=\"".$form_label."\" width=\"100%\">";

		$times_through++;

//		$out .="<input type=\"hidden\" name=\"user_identifier\" value=\"$user_identifier\"/>";
		$out .="<input type=\"hidden\" name=\"prev_command\" value=\"$prev_command\"/>";
		$out .="<input type=\"hidden\" id=\"command\" name=\"command\" value=\"EMAILADMIN_PRINT_LABELS_SAVE\"/>";
//		$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_SAVE\"/>";
		$out .="<input type=\"hidden\" id=\"identifier\" name=\"identifier\" value=\"$identifier\"/>";
		$out .="<input type=\"hidden\" name=\"max_number_of_fields\" value=\"".count($fields)."\" />";
//		$out .="<textarea hidden='YES' required=\"YES\" label=\"hidden_label\" size=\"10\" height=\"10\" name=\"ody\" type=\"RICH-TEXT\" >sss</textarea>";

		$out .="<page_sections>";
/*
		$out .="<section label='Choose Database' name='detail'>";
		$out .= "\t\t\t\t\t<select name=\"choose_email_database\" label=\"".$choose_email_database."\" onchange='show_hidden_label_group(this)'>\n";
		$out  .= "<option value='1' selected='true'>Members</option>";
		$out  .= "<option value='2'>Photo Gallery</option>";
		$out .= "\t\t\t\t\t</select>\n";
		$out .="</section>";
		*/

/*
			$out .="<section label='Choose Database ss' name='detail5'>";
			$out .="</section>";
*/

			$out .="<section label='Choose Database' name='choose_database'>";
			
//			if ($error== 'supply_email')
			if ($error>0){
				$out .="<text type=\"error\"><![CDATA[".LOCALE_SUPPLY_DATABASE_EMAIL."]]></text>";			
			}
				
/*			
			$out .= "<radio name='menu_archiving' label='Enable Archiving on this menu location?' type='horizontal' onclick='get_dbfields' tag='section_button_archive_btn'>";
			$out .= "<option value='103892517412984546'";
			if($identifier==103892517412984546){
				$out .= " selected='true'";
			}
			$out .= ">Member</option>";
			$out .= "<option value='105618259584238898'";
			if($identifier==105618259584238898){
				$out .= " selected='true'";
			}
			$out .= ">Photo Gallery</option>";
			$out .= "</radio>";
*/
			$out .= 			"<select name='info_shop_enabled' label='".LOCALE_INFODIR_ECOMMERCE_ENABLED."' onchange='toggle_ecommerce();' hidden='YES'>";
			$out.= $this->gen_options(Array(0,1), Array(LOCALE_NO, LOCALE_YES), $info_shop_enabled);
			$out .= 			"</select>";

		$out .="<input required= \"YES\" label =\"".LOCALE_MESSAGE_LABEL."\" type=\"text\" name=\"message_label\"><![CDATA[".$message_label."]]></input>";
		if ($edit_status == 1){
			$i=0;
			while (list(, $value) = each($list_databases)) {
				if ($list_databases[$i]['info_identifier'] == $identifier){
					//$out .="<input label =\"".choose_database."\" readonly=\"1\" type=\"text\" name=\"choose_database\"><![CDATA[".$list_databases[$i][menu_label]."]]></input>";
					$out.="<label><![CDATA[<b>".$list_databases[$i][menu_label]."</b>]]></label>";
				}
				$i++;
			}
			$out .="<input type=\"hidden\" id=\"hedit_status\" name=\"hedit_status\" value=\"$edit_status\"/>";
			$out .="<input type=\"hidden\" id=\"hem_fields_identifier\" name=\"hem_fields_identifier\" value=\"$em_fields_identifier\"/>";
		}else{
			$out .= "\t\t\t\t\t<select required= \"YES\" name=\"choose_database\" label=\"".LOCALE_CHOOSE_DATABASE_LABEL."\" onchange='get_dbfields_group(this)'>\n";
			$out  .= "<option value='-1'>Select one</option>";
	
	//		print_r($list_databases);
	//		echo '<br><br>';
	//		echo $list_databases[0]['info_identifier'];
	
			$i=0;
			while (list(, $value) = each($list_databases)) {
				if ($list_databases[$i][info_identifier] != ""){
					$out  .= "<option value='".$list_databases[$i][info_identifier]."'";
	
					if ($list_databases[$i]['info_identifier'] == $identifier)
						$out .= " selected='true'";
	
					$out  .= ">".$list_databases[$i][menu_label]."</option>";
					$i++;
				}
			}
			$out .= "\t\t\t\t\t</select>\n";
		}#edit_status
			$out .="</section>";

		/*           Archiving starts*/
/*
//			if($menu_external==0){
				$out .= "<section label='Archive Settings' name='archive'";
				if($menu_archiving==0){
					$out .= " hidden='true'";
				}
				$out.=">";

				$archive_array = Array(
					Array(1,"When article is 1 year old","DATE"),
					Array(2,"When article is 1 month old","DATE")
				);

				$out .= "<select name='menu_archive_display_by_date' label='When should archiving take place'><option value='0'>Choose one </option>";
				$aac= count($archive_array);
				for($i=0;$i<$aac;$i++){
					if($archive_array[$i][2]=="DATE"){
						$out .= "<option value='".$archive_array[$i][0]."'";
						if($menu_archive_display==$archive_array[$i][0]){
							$out .= " selected='true'";
						}
						$out .= ">".$archive_array[$i][1]."</option>";
					}
				}
				$out .= "</select>";
	

				$out .= "</section>";
*/
		/*           Archiving ends*/

/*		$out .="<section label='Print Labels' name='detail'>";
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
		
		$out .= "<option value='North' selected='true'>North</option>
		<option value='South'>South</option>
		<option value='East'>East</option>
		<option value='West'>West</option>
		</radio>\n";
		$out .="</section>";
*/
/*

		$out .="		<section label='Choose Category'>";
		$out .= 			"<choose_email_admin_category can_add='1' parent='$cat_parent' identifier='$info_category' name='cat_parent'>
								<add><![CDATA[Add new Category]]></add>
								<label><![CDATA[Select Category that this belongs to.]]></label>
								$category_listing
							</choose_email_admin_category>";
		$out .="			$cat_list";
		$out .="		</section>";

		$out .="		<section label='Define Summary Format' name='summary_display' onclick='show_output'><parameters><field>summary_display</field></parameters>";

//		$out_dbname = "\t\t\t\t\t<select name=\"select_cat\" label=\"select_cat\">\n";
		
		$out .="		<multilist>";
		$out .="		<multi>";
		$out .="		</multi>";
		$out .="		</multilist>";
		$out .="		<fieldlist>";
*/		

/*		$out .="		<section label='Map Fields'>";
		$out .= 			"<choose_email_database name=''>
								<databases>";
									
		if(is_array($fields)){
			foreach($fields as $keydata => $list){
//				$sfield .= "			<field name='$key' type='$type' rank='$rank' $link><label><![CDATA[$label]]></label></field>";
				$info_identifier	= $this->check_parameters($list, "info_identifier","");
				$menu_label	= $this->check_parameters($list, "menu_label","");
								$out .= "<database Name='$menu_label' ID='$info_identifier' />";
//				echo $info_identifier.'<br>';
//				echo $menu_label.'<br>';
//				$out_dbname  .= "<option value='$info_identifier'>$menu_label</option>";
			}
		}
		$out .= 			"
								</databases>
							</choose_email_database>";
		$out .="		</section>";
*/

/*				$out .="		</fieldlist>";
		
//		$out_dbname .= "\t\t\t\t\t</select>\n";


		$out .="			<screen>$sfield</screen>";
		$out .="		</section>";
*/
/*
			$out .= "<section label='ssss' name='d'>";
				$out .="<radio label=\"sqqq\" name=\"menu_inher\" onclick='show_hidden'>";
				$out .="<option value='1'";
				$out .=">yes</option>";
				$out .="<option value='0'";
				$out .=">no</option>";
				$out .="</radio>\n";
		$out .="<checkboxes label=\"aa\" id=\"menu_display\" name=\"menu_display\"></checkboxes>\n";
		$out .= "</section>";
*/		

			$out .="		<section label='MetaData' name='metadatascreen' onclick='viewmetadatamaping'><parameters><field></field></parameters>
			
			<text hidden='YES'><![CDATA[
				<div class=\"column\" id=\"fieldForm\" name=\"fieldForm\" style=\"border:1px dashed #999999;padding:3px 3px 3px 3px;width:450px;display:inline;\"></div>
				<div id=\"fieldRank\" name=\"fieldRank\" style=\"border:1px dashed #999999;padding:3px 3px 3px 3px;width:450px;display:inline;vertical-align:top;\"></div>
			]]></text>
			";


			/* Add field list to map portion starts */
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
								$link="link = '0'";
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

			/* Add field list to map portion ends */
			
			
			$out .="			<metadatamapfields>";
			for($i=0;$i<count($this->metadata_fields);$i++){
				$out .="			<metadata_tag name='".$this->metadata_fields[$i]["key"]."'><![CDATA[".$this->metadata_fields[$i]["label"]."]]></metadata_tag>";
			}
			$out .="			</metadatamapfields>";
			$out .="		</section>";

		
		$out .="</page_sections>";
		
			$out .= "	<input iconify=\"EMAIL\" type=\"submit\" command=\"EMAILADMIN_PRINT_LABELS_SAVE\" value=\"".PRINT_LABELS_SAVE."\"/>";
		
		$out .= "\t\t\t\t</form>\n";
	/* Form Potion Ends*/
	
	$out = "<module name=\"information_admin\" display=\"form\">$out</module>";
	
	}//ok
	return $out;;
	}


	/*************************************************************************************************************************
    * Save Email Message Fields	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function print_labels_save($parameters){
		$identifier 			= $this->check_parameters($parameters,"identifier");
		$hem_fields_identifier 	= $this->check_parameters($parameters,"hem_fields_identifier");
		$hedit_status 			= $this->check_parameters($parameters,"hedit_status",-1);
		$mdmap 					= $this->check_parameters($parameters,"mdmap");

		while (list(, $value) = each($mdmap)) {
			$arr_val = explode("::",$value);
			if ($arr_val[0] == 'md_surname')
				$surname = $arr_val[1];
			if ($arr_val[0] == 'md_first_name')
				$first_name = $arr_val[1];
			if ($arr_val[0] == 'md_email')
				$email = $arr_val[1];
			if ($arr_val[0] == 'md_region')
				$region = $arr_val[1];
		}

		$request_arr = array(
		"message_label" => trim($this->strip_tidy($this->check_parameters($parameters,"message_label")))
		);
		
		$now 		= $this->libertasGetDate("Y/m/d H:i:s");

		$message_label = $request_arr["message_label"];
/*		$field_surname = $request_arr["field_surname"];
		$field_first_name = $request_arr["field_first_name"];
		$field_email = $request_arr["field_email"];
		$field_region = $request_arr["field_region"];
*/		

/*
//		$identifier 	= $this->check_parameters($parameters,"identifier");
		$identifier 			= $this->check_parameters($parameters,"identifier",-1);
		$em_fields_identifier	= $this->check_parameters($parameters,"em_fields_identifier",-1);
		$edit_status 			= $this->check_parameters($parameters,"edit_status",-1);
//		$db_identifier 			= $this->check_parameters($parameters,"db_identifier",-1);

		//$download_file 	= $this->check_parameters($parameters,"download_file");
		//$file_name 	= $this->check_parameters($parameters,"file_name");
		//$send_email 	= $this->check_parameters($parameters,"send_email");
		$message_label 	= $this->check_parameters($parameters,"message_label");
		$error 	= $this->check_parameters($parameters,"error");

*/
		if ($email == "" || $message_label == "")
			$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=EMAILADMIN_PRINT_LABELS&edit_status=$hedit_status&em_fields_identifier=$hem_fields_identifier&identifier=$identifier&message_label=$message_label&error=1"));
		
		if ($hedit_status == 1){
			$sql="update email_member_fields set 
					em_fields_label = '$message_label', 
					em_fields_first_name = '$first_name', 
					em_fields_surname = '$surname', 
					em_fields_email = '$email', 
					em_fields_region = '$region'
				  where
					em_fields_identifier 		= $hem_fields_identifier and
					em_fields_client 			= $this->client_identifier and
					em_fields_db_identifier		= $identifier
					";
		}else{
			$sql="insert into email_member_fields
						(em_fields_client, em_fields_db_identifier, em_fields_label, em_fields_first_name, em_fields_surname, 
						em_fields_email, em_fields_region, em_fields_date_created)
					values 
						('$this->client_identifier', '$identifier','$message_label','$first_name', '$surname',
						 '$email', '$region', '$now')";
		}
		$this->parent->db_pointer->database_query($sql);

//		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=EMAILADMIN_MEMBER_EMAIL_BODY"));
		
		
		$out= "	<module name='email_admin' display='form'>";
		$out.="		<form name='MEMBER_email_confirm' label='".LOCALE_MEMBER_EMAIL_FIELDS."'><text><![CDATA[".LOCALE_MEMBER_EMAIL_FIELDS_MSG."<p></p>]]></text></form>";
		$out.="	</module>";

		return $out;		
		
	}

	/*************************************************************************************************************************
    * Remove Email Message Fields	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function member_email_fields_remove($parameters){
		$identifier 			= $this->check_parameters($parameters,"identifier");
		$em_fields_identifier 	= $this->check_parameters($parameters,"em_fields_identifier");

		$sql="delete from email_member_fields 
			  where
				em_fields_identifier 		= $em_fields_identifier and
				em_fields_client 			= $this->client_identifier and
				em_fields_db_identifier		= $identifier
				";
		$this->parent->db_pointer->database_query($sql);
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=EMAILADMIN_MEMBER_EMAIL_FIELDS_LIST"));
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

/*
		$mdmap 			= $this->check_parameters($parameters,"mdmap");

		while (list(, $value) = each($mdmap)) {
			$arr_val = explode("::",$value);
			if ($arr_val[0] == 'md_title')
				$surname = $arr_val[1];
			if ($arr_val[0] == 'md_first_name')
				$first_name = $arr_val[1];
			if ($arr_val[0] == 'md_email')
				$email = $arr_val[1];
			if ($arr_val[0] == 'md_region')
				$region = $arr_val[1];
		}

		if ($email == "")
			$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=EMAILADMIN_PRINT_LABELS&error=1"));

*/		
//		print_r($arr_val);
		/*
		foreach ($mdmap as $mdvalues){
			
		}
		*/
		

		/*
		$request_arr = array(
		"region_type" => trim($this->strip_tidy($this->check_parameters($parameters,"region_type"))),
		"label_type" => trim($this->strip_tidy($this->check_parameters($parameters,"label_type"))),
		);
		*/
/*
		"surname" => trim($this->strip_tidy($this->check_parameters($parameters,"md_title"))),
		"first_name" => trim($this->strip_tidy($this->check_parameters($parameters,"md_first_name"))),
		"email" => trim($this->strip_tidy($this->check_parameters($parameters,"md_email"))),
		"region" => trim($this->strip_tidy($this->check_parameters($parameters,"md_region")))

*/
/*		$label_type = $request_arr["label_type"];
		$region_type = $request_arr["region_type"];
*/		
		
		$out = '';

		$out  ="<module name=\"MEMBER_EMAIL\" display=\"form\">";
		$out .="<form name=\"user_form\" method=\"post\" label=\"".LOCALE_MEMBER_EMAIL."\">";
		$out .="<input type=\"hidden\" name=\"command\" value=\"EMAILADMIN_MEMBER_EMAIL_SEND\"/>";
//		$out .="<input type=\"hidden\" name=\"label_type\" value=\"$label_type\"/>";
//		$out .="<input type=\"hidden\" name=\"region_type\" value=\"$region_type\"/>";		
		$out .="<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";		
		$out .="<input type=\"hidden\" name=\"field_surname\" value=\"$surname\"/>";		
		$out .="<input type=\"hidden\" name=\"field_first_name\" value=\"$first_name\"/>";		
		$out .="<input type=\"hidden\" name=\"field_email\" value=\"$email\"/>";		
		$out .="<input type=\"hidden\" name=\"field_region\" value=\"$region\"/>";		
		if ($error>0){
			$out .="<text type=\"error\"><![CDATA[".LOCALE_SUPPLY_EMAIL_SUBJECT_BODY."]]></text>";			
		}

		$out .="<page_sections>";

		$out .="<section label='Members' name='detail'>";
		$out .= "\t\t\t\t\t<select name=\"label_type\" label=\"".$label_type_label."\" onchange='show_hidden_label_group(this)'>\n";
		$out  .= "<option value='1' selected='true'>All Members</option>";
		$out  .= "<option value='2'>By Region</option>";
		$out .= "\t\t\t\t\t</select>\n";
		$out .= "\t\t\t\t\t<radio type='vertical' label='".LOCALE_LIST_OF_REGIONS_AVAILABLE_TO_PRINT."' id='region_type' name='region_type'";

		$hide_sub = -1;
		if ($hide_sub==-1){
			$out .=" hidden='YES'";
		}

		$out .= ">";
		
		$out .= "<option value='North' selected='true'>North</option>
		<option value='South'>South</option>
		<option value='East'>East</option>
		<option value='West'>West</option>
		</radio>\n";
		$out .="</section>";


		$out .="<section label='Message' name='email_body_section'>";
		$out .="<input required=\"YES\" label =\"".LOCALE_MEMBER_EMAIL_FROM_NAME."\" type=\"text\" name=\"email_from_name\"><![CDATA[]]></input>";
		$out .="<input required=\"YES\" label =\"".LOCALE_MEMBER_EMAIL_FROM_EMAIL."\" type=\"text\" name=\"email_from_email\"><![CDATA[]]></input>";
		$out .="<input required=\"YES\" label =\"".LOCALE_META_SUBJECT."\" type=\"text\" name=\"email_subject\"><![CDATA[]]></input>";
		$out .= "<input type=\"file\" file_size=\"$file_size\" label=\"".LOCALE_FILE_NAME."\" size=\"20\" name=\"file_name\" value=\"$file_tag\" $required>$choices</input>";
//		$out .="<textarea required=\"YES\" label =\"".LOCALE_MESSAGE."\" width=\"30\" size=\"60\" height=\"12\" name=\"email_body\"><![CDATA[]]></textarea>";
		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .="<textarea required=\"YES\" label =\"".LOCALE_MESSAGE."\" size=\"40\" height=\"18\" name=\"email_body\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'></textarea>";
//			$out .= "	<textarea required=\"YES\" label=\"".LOCALE_PAGE_CONTENT."\" size=\"40\" height=\"18\" name=\"trans_body\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$content_value]]></textarea>";

		$out .="</section>";

		$out .="<section label='Email Fields' name='email_fields_category'>";

//		$sql = "select * from email_member_fields where em_fields_client=$this->client_identifier and em_fields_db_identifier = $identifier order by em_fields_label";
		$sql = "select * from email_member_fields where em_fields_client=$this->client_identifier order by em_fields_label";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->parent->db_pointer->database_query($sql);
		$out .= "\t\t\t\t\t<radio required=\"YES\" type='vertical' label='".LOCALE_LIST_OF_FIELDS_CATEGORY."' id='email_field_cat' name='email_field_cat'>";
		
		$count_fields_cat = $this->call_command("DB_NUM_ROWS",array($result));
		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			$em_fields_identifier = $r['em_fields_identifier'];
			$em_fields_label = $r['em_fields_label'];
			$db_identifier = $r['em_fields_db_identifier'];
			
			$sql_fields = "select info_label from information_list where info_client=$this->client_identifier and info_identifier ='$db_identifier'";
			$result_fields  = $this->parent->db_pointer->database_query($sql_fields);
			$r_fields = $this->parent->db_pointer->database_fetch_array($result_fields);
			$db_name = $r_fields['info_label'];

			$out .= "<option value='$db_identifier-$em_fields_identifier'";
			if ($em_fields_identifier == $em_field || $count_fields_cat == 1)
				$out .= " selected='true'";
			else
				$out .= " selected='false'";
		$out .= ">$em_fields_label ( $db_name )</option>";
		}
		$out .= "</radio>\n";

		$out .="</section>";

		$out .="</page_sections>";

		$out .="<input iconify=\"EMAIL\" type=\"submit\" value=\"".LOCALE_SEND_MAIL."\"/>";
		$out .="</form>";
		$out .="</module>";
/*
		$out .="<input required=\"YES\" type=\"text\" name=\"email_subject\" label=\"".LOCALE_DIRECTORY_LABEL."\" size=\"255\"><![CDATA[$info_label]]></input>";
		$out .="<textarea required=\"YES\" label=\"hidden_label\" size=\"10\" height=\"10\" name=\"email_body\" type=\"RICH-TEXT\" >sss</textarea>";
*/		
		return $out;
	}

	/*************************************************************************************************************************
    * Send email to selected members all/by region	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function member_email_send($parameters){
/*
	$e = Array();

	foreach($fieldlist as $key => $val ){
		$index=$key;
		if ($fieldlist[$index][1] == "fileupload"){
			$field = $this->check_parameters($_FILES,$fieldlist[$index][0],Array());
			if (file_exists($this->check_parameters($field,"tmp_name"))){
				if ($this->check_parameters($e,"attachments","__NOT_FOUND__")=="__NOT_FOUND__"){
					$e["attachments"] =Array();
				}
				$e["attachments"][count($e["attachments"])] = Array(
					"actual_filename"	=> $this->check_parameters($field,"tmp_name"), 
					"original_filename"	=> $this->check_parameters($field,"name"), 
					"file_size"			=> $this->check_parameters($field,"size"), 
					"mime_type"			=> $this->check_parameters($field,"type")
				);
			}
		}
	}
*/
	
		$identifier 	= $this->check_parameters($parameters,"identifier");
/*
		if ($email == "" || $message_label == "")
			$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=EMAILADMIN_PRINT_LABELS&edit_status=$hedit_status&em_fields_identifier=$hem_fields_identifier&identifier=$identifier&message_label=$message_label&error=1"));
*/		
//		print_r($parameters);

		$request_arr = array(
		"region_type" => trim($this->strip_tidy($this->check_parameters($parameters,"region_type"))),
		"label_type" => trim($this->strip_tidy($this->check_parameters($parameters,"label_type"))),
		"email_field_cat" => trim($this->strip_tidy($this->check_parameters($parameters,"email_field_cat"))),
		"email_from_name" => trim($this->strip_tidy($this->check_parameters($parameters,"email_from_name"))),
		"email_from_email" => trim($this->strip_tidy($this->check_parameters($parameters,"email_from_email"))),
		"email_subject" => trim($this->strip_tidy($this->check_parameters($parameters,"email_subject"))),
		"email_body" => trim(str_replace("\\","",$this->check_parameters($parameters,"email_body")))
		);
		
		$email_field_cat = $request_arr["email_field_cat"];
		$email_field_arr = split("-",$email_field_cat);
		$db_identifier = $email_field_arr[0];
		$identifier = $db_identifier;
		$em_fields_identifier = $email_field_arr[1];
	
		$label_type = $request_arr["label_type"];
		if ($label_type == 2)
			$region_type = $request_arr["region_type"];
		else
			$region_type = '';


		$sql_fields = "select * from email_member_fields where em_fields_client='$this->client_identifier' and em_fields_identifier ='$em_fields_identifier'";
		$result_fields  = $this->parent->db_pointer->database_query($sql_fields);
		$r_fields = $this->parent->db_pointer->database_fetch_array($result_fields);
		$field_first_name = $r_fields['em_fields_first_name'];
		$field_surname = $r_fields['em_fields_surname'];
		$field_email = $r_fields['em_fields_email'];
		$field_region = $r_fields['em_fields_region'];
/*
		$field_surname = $request_arr["field_surname"];
		$field_first_name = $request_arr["field_first_name"];
		$field_email = $request_arr["field_email"];
		$field_region = $request_arr["field_region"];
*/		
		$email_from_name = $request_arr["email_from_name"];
		$email_from_email = $request_arr["email_from_email"];
		$email_subject = $request_arr["email_subject"];
		$email_body = $request_arr["email_body"];

		$email_from_arr["from"]		= '"'.$email_from_name.'" <'.$email_from_email.'>';

		$data = "";
/*
		if ($label_type == "by_region"){
			$iev_entry_values_str = " left outer join information_entry_values as iev on iev_entry = ie_identifier and iev_field = 'ie_oselect1' ";
		}else{
			$iev_entry_values_str = "";
		}
*/

		$sql = "select distinct ie_identifier from information_entry 
					inner join metadata_details on md_module = 'INFORMATIONADMIN_' and md_client=ie_client and ie_identifier = md_link_id
					inner join information_list on info_identifier = ie_list and info_client = ie_client
					left outer join user_to_object on uto_client = ie_client and uto_identifier = ie_user and uto_module='FORMBUILDERADMIN_'
					left outer join formbuilder_settings on fbs_identifier = uto_object and uto_client = fbs_client
					left outer join formbuilder_module_map on fbmm_module = 'INFORMATIONADMIN_'  and fbmm_client = ie_client and fbmm_link_id = ie_list and fbmm_setting = fbs_identifier
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

		$out = '';
		$flag = 1;
		$counter_lop = 0;
		while($row = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$iev_entry = $row['ie_identifier'];
/*		
			if ($label_type == "all"){
			}elseif ($label_type == "by_region"){
			}
*/
			$iev_field_list = "'$field_first_name','$field_email'";
			$iev_field_list = trim($iev_field_list,",");
			$sql_iev = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' and iev_field in ($iev_field_list) order by iev_identifier";
			$result_iev	= $this->parent->db_pointer->database_query($sql_iev);
			
			/* Region */
			if ($label_type == "2" && $field_region != ""){#by_region
				$sql_iev_region = "select * from information_entry_values where iev_client = $this->client_identifier and iev_entry = '$iev_entry' and iev_field = '$field_region' and iev_value = '$region_type' order by iev_identifier";
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

					if ($r["iev_field"] == $field_first_name)
						$fname = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == $field_email)
						$email = strip_tags($r["iev_value"]);
/*
					elseif ($r["iev_field"] == $field_surname)
						$surname = strip_tags($r["iev_value"]);
*/

					/*
					if ($r["iev_field"] == "ie_otext2")
						$fname = strip_tags($r["iev_value"]);
					elseif ($r["iev_field"] == "ie_email1")
						$email = strip_tags($r["iev_value"]);
					*/
					
/*			
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
	
	
					if ($field_surname != ""){
						$sql_sel_metadata = "select md_title from metadata_details where md_identifier = $iev_entry";
						$result_sel_metadata  = $this->parent->db_pointer->database_query($sql_sel_metadata);
						$r_sel_metadata = $this->parent->db_pointer->database_fetch_array($result_sel_metadata);
						$surname = $r_sel_metadata['md_title'];
					}
				 }#while($r = $db_conn->fetchArray($result_iev)) 
			
/*			
			// 	$data .= $name.",".$city.",".$address."\n";
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
			
				$data = trim($data,", ");
			
			  $out .= '<tr>
				<td width="10%"><font style="font-family:Univers Condensed;font-size:11px">'.$year_elected.'</font></td>
				<td width="80%"><font style="font-family:Univers Condensed;font-size:11px">'.$data.'</td>
				<td width="10%" align="right"><font style="font-family:Univers Condensed;font-size:11px">'.$boat.'</font></td>
			  </tr>';
*/
/*		$body = "Dear [".$fname." ".$surname."],
		
		".$email_body;
*/
//		$body = "Dear [".$fname." ".$surname."],\n<br>$email_body\n\n<br><br>Cheers\n<br>".$email_from_name;
//		$body = "Dear [[contact_first_name]],\n\n<br><br>$email_body\n\n<br><br>Cheers\n<br>".$email_from_name;
		$body = "[[contact_first_name]],\n\n<br><br>$email_body\n<br>";

		$email_html[count($email_html)] = Array("EMAIL"=>$email, "NAME"=>$fname." ".$surname);

		$body_html									= $body;
		
		
		$now 		= $this->libertasGetDate("Y/m/d H:i:s");

		if ($counter_lop == 0){
			$sql="insert into email_member_group(em_group_client,em_group_fields,em_group_from_name,em_group_from_email,em_group_subject,em_group_body,em_group_label_type,em_group_region_type,em_group_sent_date) values('$this->client_identifier','$em_fields_identifier','$email_from_name','$email_from_email','$email_subject','$email_body','$label_type','$region_type','$now')";
			$this->parent->db_pointer->database_query($sql);

			$sql_sel_email_group = "select max(em_group_identifier) as em_group_identifier from email_member_group";
			$result_email_group = $this->parent->db_pointer->database_query($sql_sel_email_group);
			$r_email_group = $this->parent->db_pointer->database_fetch_array($result_email_group);
			$em_group_identifier = $r_email_group['em_group_identifier'];
			
		}
		

		$sql="insert into email_member(em_client,em_group,em_email,em_first_name,em_surname,em_sent_date) values('$this->client_identifier','$em_group_identifier','$email','$fname','$surname','$now')";
		$this->parent->db_pointer->database_query($sql);
		
/*		echo 'Subject:  '.$email_subject.'<br>';
		echo 'Email:  '.$email.'<br>';
		echo 'Email Body:  '.$body.'<br><br><br>';
*/		
		unset($fname,$email,$surname);

		$counter_lop++;
/*		
			if ($counter_lop >= 2)
				die();
*/		
		}#flag 
	}#while($row = $db_conn->fetchArray($result)) 

		$e = Array();
		$field = $this->check_parameters($_FILES,'file_name',Array());
		if (file_exists($this->check_parameters($field,"tmp_name"))){
			if ($this->check_parameters($e,"attachments","__NOT_FOUND__")=="__NOT_FOUND__"){
				$e["attachments"] =Array();
			}
			$e["attachments"][count($e["attachments"])] = Array(
				"actual_filename"	=> $this->check_parameters($field,"tmp_name"), 
				"original_filename"	=> $this->check_parameters($field,"name"), 
				"file_size"			=> $this->check_parameters($field,"size"), 
				"mime_type"			=> $this->check_parameters($field,"type")
			);
		}
//		print_r($e);
//		die();

		$this->call_command("EMAIL_BULK_SEND",
						Array("EMAIL_LIST"=>$email_html, 
						"subject"=>$email_subject, 
						"body"=>$body_html, 
						"from"=>$email_from_arr["from"], 
						"attachments"=>$e["attachments"],
						"format"=>"HTML"
						)
						);
/*
		$sent = $this->call_command("EMAIL_QUICK_SEND",Array(
			"subject"	=> $email_subject,
			"body"		=> $body,
			"from"		=> $email_from_email,
			"to"		=> $email,
			"format"	=> "HTML"
			)
		);
*/

		$out= "	<module name='information_admin' display='form'>";
		$out.="		<form name='MEMBER_email_confirm' label='".LOCALE_MEMBER_EMAIL."'><text><![CDATA[".LOCALE_SENT_MSG."<p></p>]]></text></form>";
		$out.="	</module>";

/*
		$out .= '</table>';

		
		$file_name = "exported_".$this->client_identifier."_members_handbook.doc";
		$filepnt = fopen($tmp_dir.'/'.$file_name, 'w+');

//		$filepnt = fopen($tmp_dir."/exported_".$this->client_identifier."_members_handbook.doc", 'w+');
//		$filepnt = fopen("$data_files/exported_".$this->client_identifier."_members.doc", 'w+');

		$content = "<html><head></head><body>$out</body></html>";
		fwrite($filepnt, $content);
		fclose($filepnt);

	$this->loadedcat = $this->call_command("INFORMATIONADMIN_PRINT_LABELS", Array("identifier"=>$identifier, "download_file"=>1, "file_name"=>$file_name));
*/
		return $out;		
	}


}
?>