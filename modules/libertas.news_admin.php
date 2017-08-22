<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Muhammad Imran Mirza
* @file libertas.news_admin.php
* @date 22 July 2007
*/
/**
* 
*/
class news_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_name				= "news_admin";								// name of module is used in configuration
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";		// what group does this module belong to
	var $module_name_label			= "News Admin";
	var $module_label				= "MANAGEMENT_NEWS_ADMIN";
	var $module_creation			= "22/05/2007";							// date module was created
	var $module_modify	 			= '$Date: 2007/05/22 17:01:12 $';
	var $module_version 			= '$Revision: 1.0 $';					// Actual version of this module
	var $module_admin				= "1";									// does this system have an administrative section
	var $module_command				= "NEWSADMIN_"; 		// all commands specifically for this module will start with this token
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
			if ($user_command == $this->module_command."LIST"){
				return $this->news_list($parameter_list);
			}
			if ($user_command == $this->module_command."ADD" || $user_command == $this->module_command."EDIT"){
				return $this->news_entry_modify($parameter_list);
			}
			if ($user_command == $this->module_command."SAVE"){
				return $this->news_save($parameter_list);
			}
			if ($user_command == $this->module_command."REMOVE"){
				return $this->news_remove($parameter_list);
			}
			if ($user_command == $this->module_command."VIEW"){
				return $this->news_view($parameter_list);
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
		$this->metadata_fields					= $this->call_command("METADATAADMIN_GET_FIELDLIST", Array());
//		$this->metadata_fields					= $this->call_command("METADATAADMIN_GET_FIELDLIST_EMAIL_ADMIN", Array());
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

//		if ($this->parent->domain == 'imranmirza' && $this->client_identifier == 41)
		if ($this->parent->domain == 'www.libertas-solutions.com' && $this->client_identifier == 1)
			$this->module_admin_options[count($this->module_admin_options)] = array($this->module_command."LIST", "","");

	}

	/*************************************************************************************************************************
	* show admin desktop news detail
	*************************************************************************************************************************/
	function news_view($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");

		$data_files_path=$this->call_command("ENGINE_GET_PATH",Array("DATA_FILES_DIR"));
		while (substr ($data_files_path, -1) != '/'){ 
	  		$data_files_path = substr( $data_files_path, 0, -1);
		}
		$file_to_use	= $data_files_path."libertas/newsadmin_libertas.xml";
		$out = "";
		if (file_exists($file_to_use) && $myxml=simplexml_load_file($file_to_use)){
			foreach($myxml as $news_data){
				if($news_data->uniqueid == $identifier){	
					
					$out .= "<module name=\"page\" display=\"form\"><page_options>";
					$out .="\t".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",array("CANCEL","",LOCALE_CANCEL));
					$out .= "<header>".LOCALE_NEWSADMIN."</header></page_options>
							<form label=\"".$news_data->news_title."\"><text><![CDATA[<p>";
					$out .= $news_data->news_body;
					
					$out.="</p>	]]></text></form></module>";
				}
			}
			
		}
		return $out;
		
	}
	/*************************************************************************************************************************
	* show list of news
	*************************************************************************************************************************/
	function news_list($parameters){  //
		$sql="select * from news_admin 
				where news_admin_client = $this->client_identifier 
				order by news_admin_date_created DESC";
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$r = $this->call_command("DB_FETCH_ARRAY",array($result));
		$result = $this->call_command("DB_QUERY",array($sql));
				
		$variables = Array();
		$variables["FILTER"]			= "";// $this->filter($parameters);
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["as"]				= "table";
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		
		$variables["HEADER"]			= MANAGE_NEWSADMIN ." ".strip_tags(html_entity_decode($r['newsletter_label']));
		$variables["PAGE_BUTTONS"] = Array(
			Array("ADD",$this->module_command."ADD",ADD_NEW)
		);
		if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$page = $this->check_parameters($parameters,"page","1");
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
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
			$variables["PAGE_COMMAND"] 		= $this->module_command."NEWSADMIN_LIST";
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) && ($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				
				$variables["RESULT_ENTRIES"][$i]=Array(
				"identifier"		=> $r["news_admin_identifier"],
				"ENTRY_BUTTONS" 	=> Array(),
				"attributes"		=> Array(
						Array(LOCALE_NEWSADMIN_LABEL,$r["news_admin_title"],"TITLE","NO")
					)
				);
				
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT","NEWSADMIN_EDIT",EDIT_EXISTING);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE","NEWSADMIN_REMOVE",REMOVE_EXISTING);
								
			
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}
	
	/*************************************************************************************************************************
	* add/edit news form($parameters)
	*************************************************************************************************************************/
	function news_entry_modify($parameters){
		$identifier 			= $this->check_parameters($parameters,"identifier",-1);
//		$edit_status 			= $this->check_parameters($parameters,"edit_status",-1);
		$error 	= $this->check_parameters($parameters,"error");
		$form_label 			= "Add";
		/*************************************************************************************************************************
		* if the user is adding a new entry or editing a valid entry then ok should be true;
		*************************************************************************************************************************/
		$ok = true;
		if ($identifier!=-1){
			$form_label 	= LOCALE_EDIT;
			$sql_sel_news_admin = "SELECT * from news_admin where news_admin_identifier = $identifier and news_admin_client = $this->client_identifier";
			$result_sel_news_admin  = $this->parent->db_pointer->database_query($sql_sel_news_admin);
			$r_sel_news_admin = $this->parent->db_pointer->database_fetch_array($result_sel_news_admin);
			$identifier = $r_sel_news_admin['news_admin_identifier'];
			$news_title = $r_sel_news_admin['news_admin_title'];
			$news_body = $r_sel_news_admin['news_admin_body'];
			
			/*
			$DATA_FILES_DIR=$this->call_command("ENGINE_GET_PATH",Array("DATA_FILES_DIR"));
			$filename = $DATA_FILES_DIR."/newsadmin_".$this->client_identifier.".xml";

			if (file_exists($filename)){
				$fp = fopen($filename, 'r');
				$contents = fread($fp, filesize($filename));
				
				
				if ($myxml=simplexml_load_file($filename)){
					foreach($myxml as $news_data){
						$counter++;
						$unique_val = $news_data->uniqueid;
						if ($identifier == $unique_val){
							$news_title = $news_data->news_title;
							$news_body = $news_data->news_body;
						}
					}
				}
				
								
			}
			*/
		}

		$out 	  = "";
		if ($ok){
		/* Form Portion Starts */
	
		/**
		* generate the form for adding / editting the user details
		*/
		$out  ="";
//		$label_type_label = 'Select Label';
//		$out  ="<module name=\"users\" display=\"form\">";
		$out ="<page_options>";
		$out .="\t".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",array("CANCEL","NEWSADMIN_LIST",LOCALE_CANCEL));
		$out .= "<header><![CDATA[Admin News]]></header>";
		$out .="</page_options>";
		//$form_label 	= "Admin News";
		$out .="	<form id=\"news_admin_form\" name=\"news_admin_form\" label=\"".$form_label."\" width=\"100%\">";

		$times_through++;

		$out .="<input type=\"hidden\" name=\"prev_command\" value=\"$prev_command\"/>";
		$out .="<input type=\"hidden\" id=\"command\" name=\"command\" value=\"NEWSADMIN_SAVE\"/>";
//		$out .="<input type=\"hidden\" name=\"command\" value=\"USERS_SAVE\"/>";
		$out .="<input type=\"hidden\" id=\"identifier\" name=\"identifier\" value=\"$identifier\"/>";
		$out .="<input type=\"hidden\" name=\"max_number_of_fields\" value=\"".count($fields)."\" />";
		$out .="<page_sections>";

		$out .="<section label='Admin News' name='admin_news'>";
		
		if ($error>0){
			$out .="<text type=\"error\"><![CDATA[".LOCALE_SUPPLY_DATABASE_EMAIL."]]></text>";			
		}
	$out .="<input required= \"YES\" label =\"".LOCALE_NEWS_TITLE."\" type=\"text\" name=\"news_title\"><![CDATA[".$news_title."]]></input>";
		
		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .="<textarea required=\"YES\" label =\"".LOCALE_NEWS_BODY."\" size=\"40\" height=\"18\" name=\"news_body\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[".$news_body."]]></textarea>";

			$out .="</section>";

		$out .="</page_sections>";
		
			$out .= "	<input iconify=\"SAVE\" type=\"submit\" command=\"NEWSADMIN_SAVE\" value=\"".SAVE."\"/>";
		
		$out .= "\t\t\t\t</form>\n";
	/* Form Potion Ends*/
	
	$out = "<module name=\"news_admin\" display=\"form\">$out</module>";
	
	}//ok
	return $out;;
	}


	/*************************************************************************************************************************
    * Save Email Message Fields	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function news_save($parameters){
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		$hedit_status 	= $this->check_parameters($parameters,"hedit_status",-1);
		
		$request_arr = array(
		"news_title" => trim($this->strip_tidy($this->check_parameters($parameters,"news_title"))),
		"news_body" => $this->check_parameters($parameters,"news_body")
		);
		
		$now 		= $this->libertasGetDate("Y/m/d H:i:s");

		$news_title = $request_arr["news_title"];
		$news_body = $request_arr["news_body"];
/*
		if ($news_title == "" || $news_body == "")
			$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=NEWSADMIN_ADD&edit_status=$hedit_status&em_fields_identifier=$hem_fields_identifier&identifier=$identifier&news_title=$news_title&news_body=$news_body&error=1"));
*/			

		/* News Data Starts */
		$DATA_FILES_DIR=$this->call_command("ENGINE_GET_PATH",Array("DATA_FILES_DIR"));
		$filename = $DATA_FILES_DIR."/newsadmin_libertas.xml";

		if ($identifier == -1){
			$sql="insert into news_admin
						(news_admin_client, news_admin_title, news_admin_body, news_admin_date_created)
					values 
						('$this->client_identifier', '$news_title', '$news_body', '$now')";
			$this->parent->db_pointer->database_query($sql);

			$sql_insert_id = "SELECT LAST_INSERT_ID() as news_admin_identifier";
			$result_insert_id  = $this->parent->db_pointer->database_query($sql_insert_id);
			$r_insert_id = $this->parent->db_pointer->database_fetch_array($result_insert_id);
			$identifier = $r_insert_id['news_admin_identifier'];
			//$identifier = $this->getUid();

			$out ="";
			if (!file_exists($filename)){
				$out .="<news>";
			}
			$out .="<news_data>";
			$out .="<uniqueid><![CDATA[$identifier]]></uniqueid>";
			$out .="<news_title><![CDATA[$news_title]]></news_title>";
			$out .="<news_body><![CDATA[$news_body]]></news_body>";
			$out .="</news_data>";
			if (!file_exists($filename)){
				$out .="</news>";
				$fp = fopen($filename, 'w');
			}else{
				$fp = fopen($filename, 'r+');
				$contents = fread($fp, filesize($filename));
				fseek($fp, 0);			
				//$pos = strpos($contents, "</news>");
				
				$contents_dat = str_replace("</news_data></news>","</news_data>",$contents);
				fclose($fp);
				$fp = fopen($filename, 'w');
				fwrite($fp, $contents_dat);
				fclose($fp);
	
				$fp = fopen($filename, 'a');
				$out .="</news>";
			}
		}else{
			$sql="update news_admin set 
					news_admin_title = '$news_title', 
					news_admin_body = '$news_body' 
				  where
					news_admin_identifier 		= $identifier and
					news_admin_client 			= $this->client_identifier
					";
			$this->parent->db_pointer->database_query($sql);
		
			/*	Get news data to Regenerate file portion starts */
			$out ="<news>";
			$sql_sel_news_admin = "SELECT * from news_admin where news_admin_client = $this->client_identifier order by news_admin_identifier";
			$result_sel_news_admin  = $this->parent->db_pointer->database_query($sql_sel_news_admin);
			while($r_sel_news_admin = $this->parent->db_pointer->database_fetch_array($result_sel_news_admin)){
				$identifier = $r_sel_news_admin['news_admin_identifier'];
				$news_title = $r_sel_news_admin['news_admin_title'];
				$news_body = $r_sel_news_admin['news_admin_body'];

				$out .="<news_data>";
				$out .="<uniqueid><![CDATA[$identifier]]></uniqueid>";
				$out .="<news_title><![CDATA[$news_title]]></news_title>";
				$out .="<news_body><![CDATA[$news_body]]></news_body>";
				$out .="</news_data>";

			}
			$out .="</news>";
			/*	Get news data to Regenerate file portion ends */
			

			$fp = fopen($filename, 'w');
		}
		


		fwrite($fp, $out);
		fclose($fp);
		$um = umask(0);
		@chmod($filename, LS__FILE_PERMISSION);
		umask($um);
		$out		= "<module name='news_admin' display='form'>".$out."</module>";
		/* News Data Ends */
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=NEWSADMIN_LIST"));

		return $out;		
	}

	/*************************************************************************************************************************
    * Remove Email Message Fields	( Added By Muhammad Imran Mirza )
    *************************************************************************************************************************/
	function news_remove($parameters){
		$identifier 			= $this->check_parameters($parameters,"identifier");

		$sql="delete from news_admin 
			  where
				news_admin_identifier 		= $identifier and
				news_admin_client 			= $this->client_identifier
				";
		$this->parent->db_pointer->database_query($sql);

		/*	Get news data to Regenerate file portion starts */
		$out ="<news>";
		$sql_sel_news_admin = "SELECT * from news_admin where news_admin_client = $this->client_identifier order by news_admin_identifier";
		$result_sel_news_admin  = $this->parent->db_pointer->database_query($sql_sel_news_admin);
		while($r_sel_news_admin = $this->parent->db_pointer->database_fetch_array($result_sel_news_admin)){
			$identifier = $r_sel_news_admin['news_admin_identifier'];
			$news_title = $r_sel_news_admin['news_admin_title'];
			$news_body = $r_sel_news_admin['news_admin_body'];

			$out .="<news_data>";
			$out .="<uniqueid><![CDATA[$identifier]]></uniqueid>";
			$out .="<news_title><![CDATA[$news_title]]></news_title>";
			$out .="<news_body><![CDATA[$news_body]]></news_body>";
			$out .="</news_data>";

		}
		$out .="</news>";

		$DATA_FILES_DIR=$this->call_command("ENGINE_GET_PATH",Array("DATA_FILES_DIR"));
		$filename = $DATA_FILES_DIR."/newsadmin_libertas.xml";

		$fp = fopen($filename, 'w');
		fwrite($fp, $out);
		fclose($fp);
		$um = umask(0);
		@chmod($filename, LS__FILE_PERMISSION);
		umask($um);

		/*	Get news data to Regenerate file portion ends */

		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=NEWSADMIN_LIST"));
	}


}
?>