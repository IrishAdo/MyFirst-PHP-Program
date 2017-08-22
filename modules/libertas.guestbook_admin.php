<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.guestbook_admin.php
* @date 12 Feb 2004
*/
/**
* This module is the module for displaying any comments for a page
*/

class guestbook_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_name_label			= "GuestBook Module (Administration)";
	var $module_name				= "guestbook_admin";
	var $module_admin				= "1";
	var $module_command				= "GUESTBOOKADMIN_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_GUESTBOOKS";
	var $module_modify	 			= '$Date: 2005/02/26 10:50:00 $';
	var $module_version 			= '$Revision: 1.11 $';
	var $module_creation 			= "12/02/2004";
	var $searched					= 0;
	
	/**
	*  Management Menu entries
	*/
	
	var $module_admin_options 		= array(
		array("GUESTBOOKADMIN_LIST_BOOK", "MANAGE_GUESTBOOK","")
	);
	
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access = array(
		array("GUESTBOOKADMIN_ALL",			"COMPLETE_ACCESS"),
		array("GUESTBOOKADMIN_CREATOR",		"ACCESS_LEVEL_AUTHOR"),
		array("GUESTBOOKADMIN_APPROVER",	"ACCESS_LEVEL_APPROVER")
	);
	
	/**
	*  Channel options
	*/
	var $module_display_options 	= array(
		array("GUESTBOOK_DISPLAY",	LOCALE_DISPLAY_GUESTBOOK)
	);
	
	/**
	* WebObject entries
	*
	* Each Array has (Type, Label, Command, All locations, Has label)
	-
	- Type: - 0 = User defined, 1 = Channel type Web object, 2 = XSL defined WEbObject)
	-
	- Channels extract information from the system wile XSl defined are functions in the
	- XSL display.
	*/
	var $WebObjects				 	= array(
		array(2,"Display the Guestbook","WEBOBJECTS_SHOW_GUESTBOOK",0,0)
	);
	
	/**
	*  filter options
	*/
	var $display_options			= array();
	
	var $admin_access				= 0;
	var $approve_guestbook_access	= 0;
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
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Administration Module commands
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($this->admin_access==1){
				if ($user_command == $this->module_command."MENU_DISPLAY_OPTIONS"){
					return $this->display_channels($parameter_list);
				}
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- GuestBook Setup and management
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if  ($user_command==$this->module_command."LIST_BOOK"){
					return $this->book_list($parameter_list);
				}
				if (($user_command==$this->module_command."EDIT_BOOK") || ($user_command==$this->module_command."ADD_BOOK")){
					return $this->book_modify($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE_BOOK"){
					return $this->book_removal($parameter_list);
				}
				if ($user_command==$this->module_command."SAVE_BOOK"){
					return $this->book_save($parameter_list);
				}
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- GuestBook Entry Management
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($user_command==$this->module_command."CACHE"){
					return $this->cache_comment($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE_ENTRY_CONFIRM"){
					$this->comment_remove_confirm($parameter_list);
				}
				if  ($user_command==$this->module_command."VIEW_LIST"){
					return $this->view_notes($parameter_list);
				}
				// single html page returned;
				if  ($user_command==$this->module_command."VIEW"){
					return $this->view_comment($parameter_list);
				}
				if ($user_command==$this->module_command."APPROVE_ENTRY"){
					return $this->comment_approve($parameter_list);
				}
				if ($user_command==$this->module_command."APPROVE_ENTRY_CONFIRM"){
					return $this->comment_approve_confirm($parameter_list);
				}
				if ($user_command==$this->module_command."SAVE_ENTRY"){
					$this->save_entry($parameter_list);
					$book = $this->check_parameters($parameter_list,"book_id",-1);
//					print "<li>command=GUESTBOOKADMIN_VIEW_LIST&amp;identifier=$book</li>";
//					$this->exitprogram();
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=GUESTBOOKADMIN_VIEW_LIST&amp;identifier=$book"));
				}
				if ($user_command==$this->module_command."EDIT_ENTRY"){
					return $this->modify_entry($parameter_list);
				}
			}
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                G U E S T B O O K   S E T U P   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
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
		$this->load_locale("guestbook_admin");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier 	= $this->parent->client_identifier;
		$this->admin_access			=1;
		$this->editor_admin_access	= 1;
		/**
		* define the list of Editors in this module and define them as empty
		*/
		$this->editor_configurations = Array(
		"ENTRY_DESCRIPTION" => $this->generate_default_editor()
		);
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
		* Table structure for table 'guestbooks_list'
		-
		* the guest books_list table holds the list of guset books that are avaialble on the site
		*/
		$fields = array(
		array("gb_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
		array("gb_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
		array("gb_label"			,"varchar(255)"				,"NOT NULL"	,"default '0'"),
		array("gb_creation_date"	,"datetime"					,"NOT NULL"	,"default ''"),
		array("gb_status"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
		array("gb_workflow_status"	,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
		array("gb_menu_locations"	,"unsigned integer"			,"NOT NULL" ,"default '0'","key"),
		array("gb_display_format"	,"unsigned small integer"	,"NOT NULL" ,"default '1'","key")
		);
		
		$primary ="gb_identifier";
		$tables[count($tables)] = array("guestbooks_list", $fields, $primary);
		/**
		* Table structure for table 'guestbooks_status'
		*/
		$fields = array(
		array("gbs_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
		array("gbs_label"			,"varchar(255)"				,"NOT NULL"	,"default ''")
		);
		$data = array(
		'INSERT INTO guestbooks_status (gbs_label) VALUES ("Not Live");',
		'INSERT INTO guestbooks_status (gbs_label) VALUES ("Live (open)");',
		'INSERT INTO guestbooks_status (gbs_label) VALUES ("Live (closed)");'
		
		);
		
		$primary ="gbs_identifier";
		$tables[count($tables)] = array("guestbooks_status", $fields, $primary, $data);
		/**
		* Table structure for table 'guestbooks_workflow_status'
		-
		* 1. Everyone can publish directly to the site
		* 2. registered users can publish to the site directly, anonymous to be approved
		* 3. no anonymous acces to add functions
		* 4. all content to be approved unless user has approval status set.
		*/
		$fields = array(
		array("gbws_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
		array("gbws_label"			,"varchar(255)"				,"NOT NULL"	,"default '0'")
		);
		$data = array(
		'INSERT INTO guestbooks_workflow_status (gbws_label) VALUES ("LOCALE_GUESTBOOK_WORKFLOW_STATUS_FREE_ACCESS");',
		'INSERT INTO guestbooks_workflow_status (gbws_label) VALUES ("LOCALE_GUESTBOOK_WORKFLOW_STATUS_REG_PUB");',
		'INSERT INTO guestbooks_workflow_status (gbws_label) VALUES ("LOCALE_GUESTBOOK_WORKFLOW_STATUS_LOGGED_IN");',
		'INSERT INTO guestbooks_workflow_status (gbws_label) VALUES ("LOCALE_GUESTBOOK_WORKFLOW_STATUS_CLOSED");'
		);
		$primary ="gbws_identifier";
		$tables[count($tables)] = array("guestbooks_workflow_status", $fields, $primary, $data);
		/**
		* Table structure for table 'guestbooks_entry_status'
		*/
		$fields = array(
		array("gbes_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
		array("gbes_label"			,"varchar(255)"				,"NOT NULL"	,"default '0'")
		);
		$data = array(
		'INSERT INTO guestbooks_entry_status (gbes_label) VALUES ("Requires Approval");',
		'INSERT INTO guestbooks_entry_status (gbes_label) VALUES ("Approved");'
		);
		
		$primary ="gbes_identifier";
		$tables[count($tables)] = array("guestbooks_entry_status", $fields, $primary, $data);
		/**
		* Table structure for table 'guestbooks_entry'
		-
		* log_details is a relationship link to the users session logs record
		* gbes_book is a relationship with the guestbooks_list
		*/
		$fields = array(
		array("gbe_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment", "key"),
		array("gbe_book"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
		array("gbe_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
		array("gbe_user"			,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
		array("gbe_log_details"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
		array("gbe_creation_date"	,"datetime"					,"NOT NULL"	,"default ''"),
		array("gbe_label"			,"varchar(255)"				,"NOT NULL"	,"default '0'"),
		array("gbe_name"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
		array("gbe_status"			,"unsigned small integer"	,"NOT NULL" ,"default '0'", "key"),
		array("gbe_approved_by"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key")
		);
		
		$primary ="gbe_identifier";
		$tables[count($tables)] = array("guestbooks_entry", $fields, $primary);
		
		
		return $tables;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                G U E S T B O O K   B O O K   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: book_list($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function book_list($parameters){
		$sql = "
	select distinct 
		guestbooks_list.*, 
		menu_label, menu_identifier,
		gbs_label, gbws_label
	from guestbooks_list
		left outer join menu_data on guestbooks_list.gb_menu_locations = menu_data.menu_identifier
		left outer join guestbooks_status on guestbooks_status.gbs_identifier = gb_status
		left outer join guestbooks_workflow_status on guestbooks_workflow_status.gbws_identifier = gb_workflow_status
	where 
		guestbooks_list.gb_client=$this->client_identifier
	group by 
		guestbooks_list.gb_identifier
	order by guestbooks_list.gb_identifier desc
";
//		print $sql;
		$out = "";
		$result = $this->call_command("DB_QUERY",Array($sql));
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
			$this->page_size = $number_of_records+1;
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
			
			$variables["PAGE_BUTTONS"] = Array(
				Array("ADD","GUESTBOOKADMIN_ADD_BOOK", ADD_NEW)
			);
			
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
			$variables["FILTER"]			= "";
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			$page_discussion=0;
			$trans_page=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["gb_identifier"],
					"ENTRY_BUTTONS"	=> Array(
						Array("LIST","GUESTBOOKADMIN_VIEW_LIST",LOCALE_VIEW_COMMENTS),
						Array("EDIT","GUESTBOOKADMIN_EDIT_BOOK",EDIT_EXISTING),
						Array("REMOVE","GUESTBOOKADMIN_REMOVE_BOOK",REMOVE_EXISTING)
					),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		$this->check_parameters($r,"gb_label",""),"TITLE"),
						Array(LOCALE_LOCATIONS,	$this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL",Array("id"=>$r["menu_identifier"])),"SUMMARY"),
						Array("Status",			$r["gbs_label"]),
						Array("Workflow type",	$this->get_constant($r["gbws_label"]),"SUMMARY")
					)
				);
			}
			$this->page_size = $prev;
			
			return $this->generate_list($variables);
		}
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: book_modify($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function book_modify($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$form_label 		= LOCALE_ADD;
		$gb_label			= "";
		$gb_description		= "";
		$gb_status			= "1";
		$gb_workflow_status	= "4";
		$gb_display_format	= "1";
		$menu_parent		= -1;
		if ($identifier!=-1){
			$form_label 	= LOCALE_EDIT;
			$sql_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
					"identifier_field"	=> "gb_identifier",
					"module_command"	=> "GUESTBOOKADMIN_",
					"client_field"		=> "gb_client",
					"mi_field"			=> "gb_description"
				)
			);
			$sql= "select gb_status, gb_display_format, gb_menu_locations, gb_workflow_status, gb_label, ".$sql_parts["return_field"]." from guestbooks_list
								".$sql_parts["join"]."
							where gb_identifier = $identifier and gb_client= $this->client_identifier ".$sql_parts["where"]."";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$gb_label			= $r["gb_label"];
				$gb_description		= $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$this->check_parameters($r,"mi_memo")));

				$gb_status			= $r["gb_status"];
				$gb_workflow_status	= $r["gb_workflow_status"];
				$menu_parent		= $r["gb_menu_locations"];
				$gb_display_format	= $r["gb_display_format"];
			}
			$this->call_command("DB_FREE",array($result));
		}
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Guest Book Manager]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","GUESTBOOKADMIN_LIST_BOOK",LOCALE_CANCEL));
		//$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("PREVIEW","GUESTBOOKADMIN_PREVIEW",ENTRY_PREVIEW));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"GUESTBOOKADMIN_SAVE_BOOK\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<page_sections>";
		$out .="		<section label='Description'>";
		if ($this->parent->server[LICENCE_TYPE]==MECM){
			$data =  $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_parent,"use_useraccess_restrictions"=>"YES"));
		} else {
			$data =  $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_parent));
		}
		$out .= "	<select label=\"".LOCALE_DEFAULT_MENU_MSG."\" name=\"gb_menu_locations\" >$data</select>";
		$out .="		<input required=\"YES\" type=\"text\" name=\"gb_label\" label=\"Guest Book Label\" size=\"255\"><![CDATA[$gb_label]]></input>";
		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .= "		<textarea label=\"".ENTRY_DESCRIPTION."\" size=\"40\" height=\"15\" name=\"gb_description\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$gb_description]]></textarea>";
		$out .="		</section>";
		$out .="		<section label='Settings'>";
		/**
		* select the status's of guestbooks that are available
		*/
		$out .="		<radio name=\"gb_display_format\" label='Display format'>";
		$out .="		<option value='1'";
		if ($gb_display_format==1){
			$out .= 		" selected='true'";
		}
		$out .=				">Horizontal (default)</option>";
		$out .="		<option value='2'";
		if ($gb_display_format==2){
			$out .= 		" selected='true'";
		}
		$out .=				">Vertical</option>";
		$out .="		</radio>";
		$out .="		<radio name=\"gb_status\" label='Guest Book Status'>";
		$sql = "select * from guestbooks_status";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$gbs_identifier	= $r["gbs_identifier"];
			$gbs_label		= $r["gbs_label"];
			$out .="		<option value='$gbs_identifier'";
			if ("$gb_status"=="$gbs_identifier"){
				$out .= 		" selected='true'";
			}
			$out .=				"><![CDATA[$gbs_label]]></option>";
		}
		$this->call_command("DB_FREE",array($result));
		$out .="		</radio>";
		
		
		/**
		* select the status's of guestbooks workflows that are available
		*/
		$out .="		<radio name=\"gb_workflow_status\" label='Guest Book Workflow'>";
		$sql = "select * from guestbooks_workflow_status";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$gbws_identifier	= $r["gbws_identifier"];
			$gbws_label		= $r["gbws_label"];
			$out .="		<option value='$gbws_identifier'";
			if ("$gb_workflow_status"=="$gbws_identifier"){
				$out .= 		" selected='true'";
			}
			$out .=				"><![CDATA[".$this->get_constant($gbws_label)."]]></option>";
		}
		$this->call_command("DB_FREE",array($result));
		$out .="		</radio>";
		
		$out .="		</section>";
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: book_save()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is used to save any changes to a book defintion
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function book_save($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$gb_status			= $this->check_parameters($parameters,"gb_status");
		$gb_workflow_status	= $this->check_parameters($parameters,"gb_workflow_status");
		$gb_display_format	= $this->check_parameters($parameters,"gb_display_format");
		$gb_menu_locations	= $this->check_parameters($parameters,"gb_menu_locations");
		$gb_label			= htmlentities(trim($this->strip_tidy($this->check_parameters($parameters,"gb_label"))));
		$gb_description		= htmlentities(trim($this->call_command("EDITOR_CONVERT_FONT_TO_SPAN", Array("string" => $this->split_me( $this->tidy( $this->validate( $this->check_parameters($parameters, "gb_description"))),"'","&#39;")))));
		
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		if ($identifier ==-1){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- add new guest book to system
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql="insert into guestbooks_list
				(gb_creation_date, gb_status, gb_workflow_status, gb_label, gb_menu_locations, gb_client, gb_display_format) 
					values 
				('$now', $gb_status, $gb_workflow_status, '$gb_label', $gb_menu_locations, $this->client_identifier, $gb_display_format) ";
			$this->call_command("DB_QUERY",array($sql));
			$sql="select * from guestbooks_list where
				gb_creation_date='$now' and 
				gb_status=$gb_status and
				gb_workflow_status=$gb_workflow_status and
				gb_label='$gb_label' and
				gb_menu_locations=$gb_menu_locations and
				gb_display_format=$gb_display_format and
				gb_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$id = $r["gb_identifier"];
			}
			$this->call_command("MEMOINFO_INSERT",array("mi_type"=>$this->module_command,"mi_memo"=>$gb_description,"mi_link_id" => $id, "mi_field" => "gb_description"));
		} else {
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Edit an existing entry
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql="update guestbooks_list set
				gb_status=$gb_status,
				gb_workflow_status=$gb_workflow_status,
				gb_label='$gb_label',
				gb_menu_locations=$gb_menu_locations,
				gb_display_format=$gb_display_format
				where
				gb_client=$this->client_identifier and
				gb_identifier = $identifier";
			$this->call_command("DB_QUERY",array($sql));
			$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->module_command,"mi_memo"=>$gb_description,"mi_link_id" => $identifier, "mi_field" => "gb_description"));
			
		}
		/**
		* Add channel to system
		*/

		$sql = "select gb_menu_locations from guestbooks_list where gb_client = $this->client_identifier and gb_menu_locations != $gb_menu_locations";
//		print $sql;
		$list = "";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			if (strlen($list)>0){
				$list.=", ";	
			}
			$list .= $r["gb_menu_locations"];
		}
		$this->call_command("DB_FREE",array($result));
		if (strlen($list)>0){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- delete any channel entries for the guest book that are not needed
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql = "delete from display_data where display_command='GUESTBOOK_DISPLAY' and display_client=$this->client_identifier and display_menu not in ($list)";
			$this->call_command("DB_QUERY",array($sql));
 		} else {
 			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- delete any channel entries for the guest book that are not needed
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql = "delete from display_data where display_command='GUESTBOOK_DISPLAY' and display_client=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
 		}
		$sql = "insert into display_data (display_command, display_client, display_menu) values ('GUESTBOOK_DISPLAY', $this->client_identifier, $gb_menu_locations)";
		$this->call_command("DB_QUERY",array($sql));

		/**
		* redirect to the list of books
		*/
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=GUESTBOOKADMIN_LIST_BOOK"));
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: book_removal($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function will allow an administrator to delete a guest book and all of its entries
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function book_removal($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$sql = "select gbe_identifier from guestbooks_entry where gbe_client=$this->client_identifier and gbe_book=$identifier";
//		print "<p>$sql</p>";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$gbe_identifier_list = "";
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				if (strlen($gbe_identifier_list)>0){
					$gbe_identifier_list .= ", ";
				}
				$gbe_identifier_list .= $r["gbe_identifier"];
			}
		}
		$this->call_command("MEMOINFO_DELETE", 		array("mi_link_id"=>$identifier, "mi_field" => "gb_description", "mi_type" => "GUESTBOOKADMIN_"));
		$this->call_command("MEMOINFO_REMOVE_LIST", array("mi_list" => $gbe_identifier_list, "mi_field" => "gbe_message", "mi_type" => "GUESTBOOK_"));
		$sql = "delete from guestbooks_entry where gbe_client=$this->client_identifier and gbe_book=$identifier";
//		print "<p>$sql</p>";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from guestbooks_list where gb_client=$this->client_identifier and gb_identifier=$identifier";
//		print "<p>$sql</p>";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=GUESTBOOKADMIN_LIST_BOOK"));
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                       G U E S T B O O K   E N T R Y   A D M I N   F U N C T I O N S                        
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	
	
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: preview_comment()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is used preview the comment on the screen.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function preview_comment($parameters){
		$entry_type	= $this->check_parameters($parameters,"entry_type");
		$trans_id 	= $this->check_parameters($parameters,"trans_id");
		$edit_id 	= $this->check_parameters($parameters,"edit_id");
		$reply_id 	= $this->check_parameters($parameters,"reply_id");
		$page_id 	= $this->check_parameters($parameters,"page_id");
		
		$sql = "select * from page_comments where comment_identifier=$edit_id and comment_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$comment_title	= "";
		$comment_msg	= "";
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$comment_title	= $r["comment_title"];
			$comment_msg	= $r["comment_message"];
		}
		$label = LOCALE_COMMENTS_PREVIEW;
		$out   = "<module name=\"page\" display=\"form\">";
		$out  .= "<form method='post' name=\"process_form\" label=\"".$label."\">";
		$out .= "	<input type=\"hidden\" name=\"trans_id\"><![CDATA[$trans_id]]></input>";
		$out .= "	<input type=\"hidden\" name=\"reply_id\"><![CDATA[$reply_id]]></input>";
		$out .= "	<input type=\"hidden\" name=\"page_id\"><![CDATA[$page_id]]></input>";
		$out  .= "	<input type='hidden' name='edit_id' ><![CDATA[$edit_id]]></input>";
		$out  .= "	<input type='hidden' name='command' ><![CDATA[PAGE_SAVE_COMMENT_CONFIRMED]]></input>";
		$out  .= "	<input type='hidden' name='entry_type' ><![CDATA[$entry_type]]></input>";
		$out  .= "	<text ><![CDATA[".LOCALE_COMMENTS_PREVIEW_MSG."]]></text>";
		$out  .= "	<text label=\"Title\"><![CDATA[<strong>$comment_title</strong>]]></text>";
		$out  .= "	<text label=\"Message\"><![CDATA[$comment_msg]]></text>";
		$out  .= "	<input type='button' command='PAGE_EDIT_COMMENT&amp;entry_type=$entry_type&amp;identifier=$edit_id&amp;page_id=$page_id' iconify='CANCEL' value='".LOCALE_CANCEL."'/>";
		$out  .= "	<input type='submit' iconify='SAVE' value='".SAVE_DATA."'/>";
		$out  .= "</form>";
		$out  .= "</module>";
		return $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: modify_comment()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is used
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function modify_comment($parameters){
		$comment_message			= "";
		$comment_title				= "";
		$reply_id					= "-1";
		$edit_id					= "-1";
		$page_id  					= "-1";
		$trans_id					= "-1";
		$id							= $this->check_parameters($parameters,"identifier");
		if ($this->check_parameters($parameters,"command")=="PAGE_ADD_COMMENT"){
			$page_id 				= $this->check_parameters($parameters,"identifier","-1");
			$sql 					= "select trans_identifier from page_trans_data where trans_page = $page_id and trans_client= $this->client_identifier and trans_published_version =1";
			$result 				= $this->call_command("DB_QUERY",array($sql));
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r 					= $this->call_command("DB_FETCH_ARRAY",array($result));
				$trans_id 			= $r["trans_identifier"];
			}
			$label = LOCALE_ADD_COMMENT_FORM;
		} else if ($this->check_parameters($parameters,"command")=="PAGE_COMMENT_RESPOND"){
			$reply_id 				= $this->check_parameters($parameters,"reply_to","-1");
			$page_id 				= $this->check_parameters($parameters,"identifier","-1");
			$sql 					= "select * from page_comments inner join page_trans_data on trans_identifier = comment_translation where comment_identifier=$reply_id and comment_client=$this->client_identifier and trans_page=$page_id";
			$result					= $this->call_command("DB_QUERY",array($sql));
			$comment_translation 	= -1;
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r 					= $this->call_command("DB_FETCH_ARRAY",array($result));
				$comment_title 		= "RE: ".$r["comment_title"];
				$trans_id 			= $r["trans_identifier"];
			}
			$label = LOCALE_RESPOND_COMMENT_FORM;
		} else if ($this->check_parameters($parameters,"command")=="PAGE_EDIT_COMMENT"){
			$edit_id			 	= $this->check_parameters($parameters,"identifier","-1");
			$page_id			 	= $this->check_parameters($parameters,"page_id","-1");
			$label 					= LOCALE_EDIT_COMMENT_FORM;
			$sql 					= "select * from page_comments inner join page_trans_data on trans_identifier = comment_translation where comment_identifier=$edit_id and comment_client=$this->client_identifier";
			$result				 	= $this->call_command("DB_QUERY",array($sql));
			$comment_translation 	= -1;
			$comment_title			= "";
			$comment_message		="";
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
				$comment_title 			= $r["comment_title"];
				$comment_message 		= $this->html_2_txt($this->check_parameters($r,"comment_message"));
				if($page_id==-1){
					$page_id 			= $r["trans_page"];
				}
			}
		}
		
		$sp_comments_open = strtoupper($this->check_prefs(Array("sp_comments_open","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No")));
		if($sp_comments_open=="YES" || $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER","0")>0){
			$comment_type 	= strtoupper($this->check_parameters($parameters,"entry_type","WEB"));
			$out  = "<module name=\"page\" display=\"form\">";
			$out .= "<form method='post' name=\"process_form\" label=\"".$label."\">";
			$out .= "	<input type=\"hidden\" name=\"trans_id\"><![CDATA[$trans_id]]></input>";
			$out .= "	<input type=\"hidden\" name=\"edit_id\"><![CDATA[$edit_id]]></input>";
			$out .= "	<input type=\"hidden\" name=\"reply_id\"><![CDATA[$reply_id]]></input>";
			$out .= "	<input type=\"hidden\" name=\"page_id\"><![CDATA[$page_id]]></input>";
			$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[PAGE_PREVIEW_COMMENT]]></input>";
			$out .="	<input type=\"hidden\" name=\"entry_type\" value=\"$comment_type\"/>";
			if ($this->check_parameters($parameters,"command")=="PAGE_ADD_COMMENT"){
				$out .= "	<input type=\"text\" name=\"comment_title\" size=\"60\" label=\"".LOCALE_SUBJECT."\"><![CDATA[$comment_title]]></input>";
			} else {
				$out .="		<input type=\"hidden\" name=\"comment_title\" ><![CDATA[$comment_title]]></input>";
				$out .="		<text label=\"".LOCALE_SUBJECT."\"><![CDATA[$comment_title]]></text>";
			}
			$out .= "	<textarea name=\"page_comment\" label=\"".LOCALE_COMMENT."\" size=\"60\" height=\"10\" type=\"PLAIN-TEXT\"><![CDATA[$comment_message]]></textarea>";
			if ($comment_type!="WEB"){
				$out .="	<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"PAGE_LIST&amp;entry_type=$comment_type\"/>";
			}
			$out .= "	<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
			$out .= "</form>";
			$out .= "</module>";
			return $out;
		}else{
			$out = $this->call_command("USERS_SHOW_LOGIN",Array("redirect"=>"command=PAGE_ADD_COMMENT&identifier=$page_id"));
			return $out;
		}
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: save_comment()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is used
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function save_comment($parameters){
		$sp_comments_open = strtoupper($this->check_prefs(Array("sp_comments_open","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No")));
		$user		= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
		$command	= $this->check_parameters($parameters,"command");
		$entry_type	= $this->check_parameters($parameters,"entry_type","WEB");
		if($sp_comments_open=="YES" || $user>0){
			if ($command == "PAGE_PREVIEW_COMMENT"){
				$trans_id 	= $this->check_parameters($parameters,"trans_id");
				$edit_id 	= $this->check_parameters($parameters,"edit_id");
				$reply_id 	= $this->check_parameters($parameters,"reply_id");
				$page_id 	= $this->check_parameters($parameters,"page_id");
				if ($user==0){
					$user=-1;
				}
				if ($entry_type=="WEB"){
					$insert_type 	= 0;
				}else{
					$insert_type 	= 1;
				}
				$now 		= $this->libertasGetDate("Y/m/d H:i:s");
				$msg		= $this->validate($this->tidy($this->txt2html(strip_tags($this->check_parameters($parameters,"page_comment")))));
				$title		= $this->strip_tidy($this->validate(strip_tags($this->check_parameters($parameters,"comment_title"))));
				if ($edit_id==-1){
					if ($reply_id==-1){
						$reply_id=0;
					}
					$sql = "insert into page_comments (
								comment_translation, 
								comment_client, 
								comment_title, 
								comment_message, 
								comment_date, 
								comment_user, 
								comment_response_to, 
								comment_type,
								comment_status,
								comment_approved_by
							) values (
								'$trans_id',
								'$this->client_identifier',
								'".$title."',
								'".$msg."',
								'$now',
								$user,
								'$reply_id',
								'$insert_type',
								0,
								0
							)";
					$sql_retrieve = "select * from page_comments where comment_client=$this->client_identifier and comment_user = $user and comment_date = '$now'";
				} else {
					$sql_retrieve = "";
					//									comment_title = '".$title."',
					
					$sql = "update page_comments set
								comment_message = '".$msg."'
							where 
								comment_identifier = $edit_id and 
								comment_client=$this->client_identifier
						";
				}
				$this->call_command("DB_QUERY",array($sql));
				if ($sql_retrieve!=""){
					$result 	= $this->call_command("DB_QUERY",array($sql_retrieve));
					$r = $this->call_command("DB_FETCH_ARRAY",array($result));
					$edit_id	= $r["comment_identifier"];
				}
				
				$out = $this->call_command("PAGE_DISPLAY_PREVIEW_COMMENT",Array("entry_type"=>$entry_type, "trans_id" => $trans_id, "edit_id" => $edit_id, "reply_id" 	=> $reply_id, "page_id" 	=> $page_id));
			}
			if ($command == "PAGE_SAVE_COMMENT"){
				
			}
		}else{
			$out="";
		}
		return $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: cache_comments()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is used cahce the comments out into an XML file
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function cache_comments($parameters){
		$comment_id = $this->check_parameters($parameters,"comment_id");
		$trans_page = $this->check_parameters($parameters,"trans_page");
		$trans_identifier = $this->check_parameters($parameters,"trans_identifier");
		if (($comment_id!="") && ($trans_page=="") && ($trans_identifier=="")){
			$sql = "select trans_page from page_comments
						inner join page_trans_data on page_trans_data.trans_identifier = comment_translation
			where comment_identifier=$comment_id and comment_client=$this->client_identifier;";
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$trans_page=$r["trans_page"];
			}
		}
		if (($trans_page=="") && ($trans_identifier!="")){
			$sql = "select * from page_trans_data where page_trans_data.trans_identifier=$trans_identifier;";
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$trans_page=$r["trans_page"];
			}
		}
		
		$sql = "select
						page_comments.*, page_trans_data.trans_page 
					from page_comments 
						inner join page_trans_data on page_trans_data.trans_identifier = page_comments.comment_translation 
					where 
						page_trans_data.trans_page = $trans_page and 
						page_trans_data.trans_client = $this->client_identifier and 
						page_comments.comment_type != 1 and 
						page_comments.comment_status = 2
					";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$company ="";
		$out="";
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				if ($r["comment_user"]!=-1){
					$company = $this->call_command("CONTACT_GET_COMPANY",Array("user_identifier" => $r["comment_user"]));
				}
				$out .= "<comment identifier=\"".$r["comment_identifier"]."\" page=\"".$r["trans_page"]."\" translation=\"".$r["comment_translation"]."\" response_to=\"".$r["comment_response_to"]."\">\n";
				$out .= "	<date><![CDATA[".$r["comment_date"]."]]></date>\n";
				$out .= "	<title><![CDATA[".$r["comment_title"]."]]></title>\n";
				$out .= "	<body><![CDATA[".$r["comment_message"]."]]></body>\n";
				$out .= "	<user identifier=\"".$r["comment_user"]."\"><![CDATA[".$this->call_command("CONTACT_GET_NAME",Array("contact_user" => $r["comment_user"],"format"=>"not_indexing"))."]]></user>\n";
				$out .= "	<company><![CDATA[".$company."]]></company>\n";
				$out .= "</comment>\n";
			}
			if (strlen($out)>0){
				$out ="<comments>\n".$out."</comments>";
			}
		}
		if (strlen($out)>0){
			$root = $this->parent->site_directories["ROOT"];
			$lang = "en";
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
			$fp = fopen($data_files."/comments_".$this->client_identifier."_".$lang."_".$trans_page.".xml", 'w');
			fwrite($fp, $out);
			fclose($fp);
					$um = umask(0);
					@chmod($data_files."/comments_".$this->client_identifier."_".$lang."_".$trans_page.".xml", LS__FILE_PERMISSION);
					umask($um);
		}
	}
	
	function view_comment($parameters){
		$identifier 		= strtoupper($this->check_parameters($parameters,"identifier","-1"));
		if ($identifier==-1){
			return "";
		} else {
			/**
			* Produce the list of notes for this page, no options
			*/
			$has_contact = $this->call_command("ENGINE_HAS_MODULE",Array("CONTACT_"));
			$join = "";
			if ($has_contact==1){
				$join .= " inner join contact_data on comment_user=contact_user";
			}
			$has_email = $this->call_command("ENGINE_HAS_MODULE",Array("EMAIL_"));
			if ($has_email==1){
				$join .= " inner join email_addresses on email_contact=contact_identifier";
			}
			$sql = "select page_comments.*, contact_data.* from page_comments
							$join
						where comment_identifier = $identifier and 
							comment_client=$this->client_identifier";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$title	= strip_tags(html_entity_decode($this->check_parameters($r,"comment_title","No Title Supplied")));
					$content= $this->check_parameters($r,"comment_message");
					$author	= $this->check_parameters($r,"contact_first_name","unknown").", ".$this->check_parameters($r,"contact_last_name","unknown");
					$email	= $this->check_parameters($r,"email_address","unknown");
					$date	= $r["comment_date"];
				}
				return "
<html>
	<head>
		<title>$title</title>
		<meta http-equiv='Pragma' content='no-cache'>
 		<link rel='stylesheet' type='text/css' href='/libertas_images/editor/libertas/lib/themes/default/css/dialog.css'>
	</head>
	<body>
		<script language='javascript'>
		<!--
			window.name = 'key_generator';
		//-->
		</script>
		<div style='border:1 solid Black; padding: 5 5 5 5; height:100%'>
			<P id=tableProps CLASS=tablePropsTitle>$title</P>
			<center><input type='button' class='bt' value='Close Window' onclick='javascript:window.close()'/></center>
			<table width='390px' border=0>
				<tr><td><table width='100%'>
				<tr><td><p>Author :: $author</p>
					$content
				</td></tr>
				</table></td></tr>
			</table>
			<center><input type='button' class='bt' value='Close Window' style='vertical-align:bottom;' onclick='javascript:window.close()'/></center>
		</div>
	</body>
</html>";
			}
		}
	}
	
	function view_notes($parameters){
		$type 		= strtoupper($this->check_parameters($parameters,"entry_type","web"));
		$identifier = $this->check_parameters($parameters,"identifier","-1");
		/**
		* Produce the list of notes for this page, no options
		*/
		$has_contact = $this->call_command("ENGINE_HAS_MODULE",Array("CONTACT_"));
		if ($has_contact==1){
			$join = "LEFT OUTER join contact_data on guestbooks_entry.gbe_user=contact_data.contact_user";
		}
		$page_identifier = $identifier;
			$sql = "select 
						guestbooks_entry.*, 
						guestbooks_entry_status.*, 
						contact_data.*,
						mi1.mi_memo as gbe_description 
					from guestbooks_entry 
						left outer join memo_information as mi1 on 
							(mi1.mi_link_id = guestbooks_entry.gbe_identifier and mi1.mi_field = 'gbe_message' and mi1.mi_client = gbe_client) 
						left outer join guestbooks_entry_status on gbes_identifier = gbe_status
						left outer join contact_data on gbe_user = contact_identifier and contact_client=gbe_client
					where 
						guestbooks_entry.gbe_client=$this->client_identifier and 
						gbe_book = $identifier 
					order by guestbooks_entry.gbe_identifier desc";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
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
//			$prev = $this->page_size;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$this->page_size = 10;
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
			
			$variables["PAGE_BUTTONS"] = Array(
				Array("CANCEL","GUESTBOOKADMIN_LIST_BOOK","Return to List of GuestBooks"),
			);
			
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
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
			$variables["FILTER"]			= "";
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			$page_discussion=0;
//			$gb_status=1;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["gbe_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_AUTHOR,	$this->check_parameters($r,"gbe_name"),"TITLE"),
						Array(LOCALE_COMMENT,	$this->check_parameters($r,"gbe_description"),"SUMMARY"),
						Array(LOCALE_DATE,		$r["gbe_creation_date"]),
						Array(LOCALE_STATUS,	$this->check_parameters($r,"gbes_label",LOCALE_LOST_ENTRY))
					)
				);
//				$gb_status	= $r["gb_status"];
				if ($this->check_parameters($r,"gbe_name")!=""){
						$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array("Name" ,$this->check_parameters($r,"gbe_name"));
				} else {
					if ($has_contact==1){
						$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array("Contact" ,$this->check_parameters($r,"contact_last_name").", ".$this->check_parameters($r,"contact_first_name"));
					}
				}
				$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT", "GUESTBOOKADMIN_EDIT_ENTRY&amp;book=$identifier", EDIT_EXISTING);
				$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE", "GUESTBOOKADMIN_REMOVE_ENTRY_CONFIRM&amp;book=$identifier", REMOVE_EXISTING);
				if ($r["gbe_status"]==1){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("VALIDATE","GUESTBOOKADMIN_APPROVE_ENTRY_CONFIRM&amp;book=$identifier",LOCALE_APPROVE);
				}
			}
			return $this->generate_list($variables);
		}
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: comment_approve_confirm($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function comment_approve_confirm($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier");
		$book				= $this->check_parameters($parameters,"book");
		$sql 				= "update guestbooks_entry set guestbooks_entry.gbe_status=2 where guestbooks_entry.gbe_identifier=$identifier and guestbooks_entry.gbe_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		$this->cache_comments(Array("book" => $book, "identifier" => $identifier));
		$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_GUESTBOOK__"], "identifier" => $identifier, "url" => ""));
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=GUESTBOOKADMIN_VIEW_LIST&identifier=$book"));
	}
	
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: comment_delete_confirm($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function comment_remove_confirm($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier");
		$book				= $this->check_parameters($parameters,"book");
		$sql 				= "delete from guestbooks_entry where guestbooks_entry.gbe_identifier=$identifier and guestbooks_entry.gbe_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("MEMOINFO_DELETE",array("mi_type"=>$this->module_command,"mi_link_id" => $identifier, "mi_field" => "gbe_description"));
		$this->cache_comments(Array("book" => $book, "identifier" => $identifier));
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=GUESTBOOKADMIN_VIEW_LIST&identifier=$book"));
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: modify_comment()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is used
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function modify_entry($parameters){
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		$comment_message			= "";
		$comment_title				= "";
		$reply_id					= "-1";
		$book_id					= $this->check_parameters($parameters,"book",-1);
		$edit_id					= $this->check_parameters($parameters,"identifier",-1);
		if($book_id==-1 || $edit_id==-1){
			return "";
		}
		$label 					= LOCALE_EDIT_COMMENT_FORM;
		$sql 					= "select * from guestbooks_entry 
										left outer join memo_information on mi_link_id = gbe_identifier
									where gbe_identifier = $edit_id and gbe_book = $book_id and gbe_client= $this->client_identifier and (mi_client=$this->client_identifier or mi_client is null) and (mi_type='GUESTBOOK_' or mi_type is null) and (mi_field='gbe_message' or mi_field is null)";
		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result				 	= $this->call_command("DB_QUERY",array($sql));
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			$r 						= $this->call_command("DB_FETCH_ARRAY",array($result));
			$gbe_name	 			= $r["gbe_name"];
			$comment_message 		= strip_tags($this->html_2_txt(html_entity_decode($this->check_parameters($r,"mi_memo"))));
			$gbe_name				= $this->check_parameters($r,"gbe_name");
		}
		
		$sp_comments_open = strtoupper($this->check_prefs(Array("sp_comments_open","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No")));
		//if($sp_comments_open=="YES" || $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER","0")>0){
		$comment_type 	= strtoupper($this->check_parameters($parameters,"entry_type","WEB"));
		$out  = "<module name=\"page\" display=\"form\">";
		$out .= "<form method='post' name=\"process_form\" label=\"".$label."\">";
		$out .= "	<input type=\"hidden\" name=\"book_id\"><![CDATA[$book_id]]></input>";
		$out .="	<input type=\"hidden\" name=\"edit_id\" value=\"$edit_id\"/>";
		$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[GUESTBOOKADMIN_SAVE_ENTRY]]></input>";
		
		$out .="<page_sections><section label='Edit Comment'>";
		$out .= "	<input type=\"text\" name=\"gbe_name\" size=\"60\" label=\"".LOCALE_YOURNAME."\"><![CDATA[$gbe_name]]></input>";
		$out .= "	<textarea name=\"comment\" label=\"".LOCALE_COMMENT."\" size=\"60\" height=\"30\" type=\"PLAIN-TEXT\"><![CDATA[$comment_message]]></textarea>";
		$out .="</section></page_sections>";
		$out .= "	<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"GUESTBOOKADMIN_VIEW_LIST&amp;identifier=$book_id\"/>";
		$out .= "	<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
		//		}else{
		//		$out = $this->call_command("USERS_SHOW_LOGIN",Array("redirect"=>"command=GUESTBOOK_ADD&identifier=$page_id"));
		//	return $out;
		//}
	}
	function save_entry($parameters){
		$book_id = $this->check_parameters($parameters,"book_id",-1);
		$edit_id = $this->check_parameters($parameters,"edit_id",-1);
		if ($book_id == -1 || $edit_id == -1){
			return "";
		}
		$comment		= $this->validate($this->tidy($this->txt2html(strip_tags($this->check_parameters($parameters,"comment")))));
		$gbe_name		= $this->strip_tidy($this->validate(strip_tags($this->check_parameters($parameters,"gbe_name"))));
		$sql = "update guestbooks_entry set
					gbe_name	= '$gbe_name'
				where 
					gbe_identifier = $edit_id and 
					gbe_book = $book_id and 
					gbe_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("MEMOINFO_UPDATE",
			array(
				"mi_type"		=> $this->module_command,
				"mi_memo"		=> $comment,
				"mi_link_id"	=> $edit_id, 
				"mi_field" 		=> "gbe_message"
			)
		);
	}
}
?>
