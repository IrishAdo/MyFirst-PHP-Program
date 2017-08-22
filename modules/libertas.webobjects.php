<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.webobjects.php
* @date 27/12/03
*/
/**
* webobject management functions
*/
class webobjects extends module{
	/**
	*  Class Variables
	*/
	var $module_name				= "webobjects";							// name of module is used in configuration
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";		// what group does this module belong to
	var $module_label				= "MANAGEMENT_WEBOBJECTS";				// label describing the module 
	var $module_name_label			= "Web (Object and Layout) Management";	// label describing the module 
	var $module_creation			= "27/12/2003";							// date module was created
	var $module_modify		 		= '$Date: 2005/02/22 19:56:28 $';
	var $module_version 			= '$Revision: 1.29 $';					// Actual version of this module
	var $module_admin				= "1";									// does this system have an administrative section
	var $module_command				= "WEBOBJECTS_";						// what does this commad start with ie TEMP_ (use caps)
	var $webContainer				= "WEBOBJECTS_";						// the key for the menu_to_object relationship
	var $module_admin_options 		= array(
	);								// what options are available in the admin menu
	/**
	*  Channel options
	*/
	var $module_display_options 	= array(
		array("WEBOBJECTS_GET_THEMES",	"LOCALE_WEBOBJECTS_SET_THEME_CHANNEL"	),
		array("WEBOBJECTS_GET_CSS",		"LOCALE_WEBOBJECTS_SET_CSS_CHANNEL"		),
		array("WEBOBJECTS_GET_SIZE",	"LOCALE_WEBOBJECTS_SET_SIZE_CHANNEL"	)
	);
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	var $module_admin_user_access 	= array(
		array("WEBOBJECTS_ALL", "COMPLETE_ACCESS"),
		array("WEBOBJECTS_LAYOUT_LIST", "MANAGE_LAYOUT"),
		array("WEBOBJECTS_CONTAINER_LIST", "MANAGE_CONTAINERS"),
		array("WEBOBJECTS_LIST", "MANAGE_OBJECTS")
	);	// specify types of access for groups
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	
	var $web_object_counter			= 0;
	var $layout_access				= 0;
	var $object_access				= 0;
	
	/**
	*  Special Themes
	*/
	
	var $THEME_PDA					=-1; 
	var $THEME_PRINTERFRIENDLY		=-2; 
	var $THEME_TEXTONLY				=-3; 
	/**
	*  Class Methods
	*/
	
	function command($user_command,$parameter_list=array()){

		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,print_r($parameter_list,true),__LINE__,"command"));
		}
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*/
//		print "<li>$user_command</li>";
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
			if ($user_command==$this->module_command."GET_PROPERTIES"){
				return $this->get_webobject_properties($parameter_list);
			}
			if ($user_command==$this->module_command."GET_THEMES"){
				return $this->get_webobject_themes($parameter_list);
			}
			if ($user_command==$this->module_command."SET_THEME"){
				return $this->set_webobject_themes($parameter_list);
			}
			if ($user_command==$this->module_command."GET_SIZE"){
				return $this->get_webobject_size($parameter_list);
			}
			if ($user_command==$this->module_command."SET_SIZE"){
				return $this->set_webobject_size($parameter_list);
			}
			if ($user_command==$this->module_command."GET_CSS"){
				return $this->get_webobject_css($parameter_list);
			}
			if ($user_command==$this->module_command."SET_CSS"){
				return $this->set_webobject_css($parameter_list);
			}
			if ($user_command==$this->module_command."CREATE_NEW_CLIENT_DETAILS"){
				return $this->create_client_details($parameter_list);
			}
//			if (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
				if ($user_command == $this->module_command."MENU_DISPLAY_OPTIONS"){
//					return $this->display_channels($parameter_list);
				}
				if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
					return $this->module_admin_access_options(0);
				}
				if ($user_command==$this->module_command."ACCESS_OPTIONS"){
					return $this->module_admin_options(0);
				}
//			}
//			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
//				return $this->module_admin_access_options(0);
//			}
			if($this->check_parameters($parameter_list,"no_redirect",0)==1){
				// the installer is the only thing that calls this save all other saves are via tha admin
				if ($user_command==$this->module_command."CONTAINER_SAVE"){
					$this->manage_container_save($parameter_list);
				}
			}
			/**
			* needed for administrative access
			*/
			if (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
				if ($user_command==$this->module_command."ACCESS_OPTIONS"){
					return $this->module_admin_options(0);
				}
			}
			if ($this->module_type_admin_access){
				if ($user_command==$this->module_command."MANAGE_MODULE"){
					return $this->manage_module_webobjects($parameter_list);
				}
				if ($user_command==$this->module_command."RETRIEVE_OBJECT"){
					return $this->module_retrieve_object($parameter_list);
				}
				if ($user_command==$this->module_command."MANAGE_MODULE_WO"){
					return $this->manage_module_webobjects_wo($parameter_list);
				}
				if ($user_command==$this->module_command."INHERIT"){
					$this->inherit($parameter_list);
				}
				if ($this->layout_access == 1){
					if ($user_command==$this->module_command."EXTRACT_OBJECTS"){
						return $this->extract_objects($parameter_list);
					}
					if ($user_command==$this->module_command."CONTAINER_LIST"){
						return $this->manage_container_list($parameter_list);
					}
					if (($user_command==$this->module_command."CONTAINER_ADD") || ($user_command==$this->module_command."CONTAINER_EDIT")){
						return $this->container_form($parameter_list);						
					}
					if ($user_command==$this->module_command."CONTAINER_REMOVE_CONFIRM"){
						$this->remove_container($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=WEBOBJECTS_CONTAINER_LIST"));
					}
					if ($user_command==$this->module_command."CONTAINER_SAVE"){
						$this->manage_container_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=WEBOBJECTS_CONTAINER_LIST"));
					}
					if ($user_command==$this->module_command."LAYOUT_LIST"){
						return $this->manage_layout_list($parameter_list);
					}
					
					
					/* Starts Sections to set default layout (Added By Muhammad Imran) */
					if ($user_command==$this->module_command."LIST_LAYOUTS"){
						return $this->list_layouts($parameter_list);
					}
					if ($user_command==$this->module_command."SET_DEFAULT_LAYOUT"){
						return $this->set_default_layout($parameter_list);
					}
					if ($user_command==$this->module_command."SET_DEFAULT_LAYOUT_SAVE"){
						return $this->set_default_layout_save($parameter_list);
					}
					/* Ends Sections to set default layout (Added By Muhammad Imran) */
					
					
					if (($user_command==$this->module_command."LAYOUT_ADD") || ($user_command==$this->module_command."LAYOUT_EDIT")){
						return $this->layout_form($parameter_list);
					}
					if (($user_command==$this->module_command."LAYOUT_SAVE")){
						$this->layout_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=WEBOBJECTS_LAYOUT_LIST"));
					}
					if ($user_command==$this->module_command."LAYOUT_REMOVE_CONFIRM"){
						$this->layout_remove_confirm($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=WEBOBJECTS_LAYOUT_LIST"));
					}
					if ($user_command==$this->module_command."STORE_CHANNELS"){
						$this->store_channels();
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=WEBOBJECTS_LAYOUT_LIST"));
					}
					if ($user_command==$this->module_command."LAYOUT_COPY_LAYOUT"){
						$this->copy_layout($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=WEBOBJECTS_LAYOUT_LIST"));
					}
				}
				if ($this->object_access == 1){
					if ($user_command==$this->module_command."LIST"){
						return $this->list_details($parameter_list);
					}
					if (($user_command==$this->module_command."ADD") || ($user_command==$this->module_command."EDIT")){
						return $this->detail_form($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE_CONFIRM"){
						$this->remove_confirm($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=WEBOBJECTS_LIST"));
					}
					if (($user_command==$this->module_command."SAVE")){
						$this->save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=WEBOBJECTS_LIST"));
					}
				}
			}
			if ($user_command==$this->module_command."EXTRACT_SETTINGS"){
				return $this->extract_settings();
			}
			if ($user_command==$this->module_command."EXTRACT"){
				return  $this->extract_webobject($parameter_list);
			}
			if ($user_command==$this->module_command."EXTRACT_TYPE_2_CONTAINERS"){
				return  $this->list_layouts_and_containers($parameter_list);
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
		
		//print "hello" . $this->parent->client_identifier;
		
//		$this->client_identifier = $this->parent->client_identifier;
//		if ($this->client_identifier == -1){
//			$this->client_identifier=$this->check_parameters($_SESSION,"client_identifier",-1);
//		}
		$this->load_locale("webobjects");
		$this->page_size=50;
		$this->layout_access	= 0;
		$this->object_access	= 0;
		if (($this->parent->server[LICENCE_TYPE]==ECMS)){ // || ($this->parent->server[LICENCE_TYPE]==MECM)
			$this->module_admin_options	= array(
				array("WEBOBJECTS_LIST",LOCALE_WEBOBJECT_LAYOUT_OBJECTS,"WEBOBJECTS_LIST","Content Manage/Web Boxes"),
				array("WEBOBJECTS_LAYOUT_LIST",LOCALE_WEBOBJECT_CONTAINER_POSITIONING,"WEBOBJECTS_LAYOUT_LIST","Preferences/Design"),
				array("WEBOBJECTS_CONTAINER_LIST",LOCALE_WEBOBJECT_CONTAINERS,"WEBOBJECTS_CONTAINER_LIST","Preferences/Design"),
				array("WEBOBJECTS_SET_DEFAULT_LAYOUT",LOCALE_SET_DEFAULT_LAYOUT,"WEBOBJECTS_SET_DEFAULT_LAYOUT","Preferences/Design")
			);
		} else {
			$this->module_admin_options	= array(
//				array("WEBOBJECTS_LIST",MANAGEMENT_WEBOBJECTS,"LAYOUT_CAN_MANAGE_OBJECTS")
			);
		}
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
/*		if ($this->parent->script=="admin/install.php"){
			$this->layout_access=1;
		}*/
		for($i=0;$i < $max_grps; $i++){
			$access_array = $grp_info[$i]["ACCESS"];
			$access_length = count($access_array);
			for ($index=0,$access_length=count($access_array);$index<$access_length;$index++){
				if (
					("ALL"==$access_array[$index]) ||
					("WEBOBJECTS_LAYOUT_ALL"==$access_array[$index]) ||
					("WEBOBJECTS_LAYOUT_LIST"==$access_array[$index])
				){
					$this->layout_access=1;
				}
				if (
					("ALL"==$access_array[$index]) || 
					("WEBOBJECTS_ALL"==$access_array[$index]) ||
					("WEBOBJECTS_LIST"==$access_array[$index])
				){
					$this->object_access=1;
				}
			}
		}
		/*
		check that the user is accessing functions from the proper mode.
		*/
		if ((($this->parent->module_type=="admin")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")||($this->parent->module_type=="install"))){
			$this->module_type_admin_access=1;
		}

		/**
		* define the list of Editors in this module and define them as empty
		*/
		$this->editor_configurations = Array(
			"ENTRY_DESCRIPTION" => Array(
				"status"=>"unlocked", 
				"locked_to" => "", 
				"identifier"=>0
			)
		);
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
		* Table structure for table 'web_objects'
		*/
		$fields = array(
			array("wo_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("wo_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wo_type"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wo_label"			,"varchar(255)"				,"NULL"		,"default ''"),
			array("wo_created"			,"datetime"					,"NULL"		,"default ''"),
			array("wo_command"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("wo_all_locations"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("wo_set_inheritance"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("wo_show_label"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("wo_owner_module"		,"varchar(255)"				,"NULL"		,"default ''"),
			array("wo_owner_id"			,"unsigned integer"			,"NULL"		,"default ''"),
			array("wo_display"			,"unsigned integer"			,"NULL"		,"default '0'")
		);
		$primary = "wo_identifier";
		$tables[count($tables)] = array("web_objects",$fields,$primary);
		/**
		* Table structure for table 'web_container_to_layout' 
		*/
		$fields = array(
			array("wctl_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("wctl_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wctl_layout"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wctl_container"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wctl_position"	,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("wctl_rank"		,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);
		$primary = "wctl_identifier";
		$tables[count($tables)] = array("web_container_to_layout",$fields,$primary);
		/**
		* Table structure for table 'web_objects_in_container' 
		*/
		$fields = array(
			array("woic_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("woic_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("woic_container"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("woic_object"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("woic_rank"		,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);
		$primary = "woic_identifier";
		$tables[count($tables)] = array("web_objects_in_container",$fields,$primary);
		/**
		* Table structure for table 'web_objects_to_menu' 
		* replaced with MENU_TO_OBJECT table in LAYOUT MODULE
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		$fields = array(
			array("wotm_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("wotm_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wotm_menu"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wotm_object"		,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);
		$primary = "wotm_identifier";
		$tables[count($tables)] = array("web_objects_to_menu",$fields,$primary);
		*/
		/**
		* Table structure for table 'web_layouts' a list of people that have subscribed
		* to a particular mailing list
		*/
		$fields = array(
			array("wol_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("wol_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wol_status"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("wol_version"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("wol_live"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("wol_complete"		,"unsigned small integer"	,"NOT NULL"	,"default '1'"),
			array("wol_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("wol_layout_design"	,"varchar(4)"				,"NOT NULL"	,"default '1111'"),
			array("wol_all_locations"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("wol_set_inheritance"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("wol_theme"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wol_created"			,"datetime"					,"NOT NULL"	,"default ''")
		);
		$primary = "wol_identifier";
		$tables[count($tables)] = array("web_layouts",$fields,$primary);
		/**
		* Table structure for table 'web_layout_to_menu' a list of people that have subscribed
		* to a particular mailing list
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		$fields = array(
			array("wor_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("wor_client"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("wor_layout"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("wor_menu"			,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary = "wor_identifier";
		$tables[count($tables)] = array("web_layout_to_menu",$fields,$primary);
		*/
		/**
		* Table structure for table 'web_properties' a list of people that have subscribed
		* to a particular mailing list
		*/
		$fields = array(
			array("wop_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("wop_client"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("wop_container_object","unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("wop_name"			,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("wop_value"			,"varchar(255)"		,"NOT NULL"	,"default ''")
		);
		$primary = "wop_identifier";
		$tables[count($tables)] = array("web_properties",$fields,$primary);
		/**
		* Table structure for table 'web_containers' a list of people that have subscribed
		* to a particular mailing list
		*/
		$fields = array(
			array("wc_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("wc_layout_identifier"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wc_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wc_label"				,"varchar(255)"				,""			,"default ''"),
			array("wc_position"				,"varchar(255)"				,""			,"default ''"),
			array("wc_rank"					,"unsigned integer"			,""			,"default ''"),
			array("wc_layout_type"			,"unsigned small integer"	,""			,"default '0'"),
			array("wc_layout_columns"		,"unsigned integer"			,""			,"default '1'"),
			array("wc_name"					,"varchar(255)"				,""			,"default ''"),
			array("wc_width"				,"varchar(5)"				,""			,"default ''"),
			array("wc_created"				,"datetime"					,""			,"default ''"),
			array("wc_type"					,"varchar(255)"				,""			,"default '1'")
		);
		$primary = "wc_identifier";
		$tables[count($tables)] = array("web_containers",$fields,$primary);
		return $tables;
	}
	
	function list_details($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_details",__LINE__,print_r($parameters,true)));
		}
		$sql = "Select * from web_objects where wo_type=0 and wo_client = $this->client_identifier order by wo_label";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables = Array();
		$variables["FILTER"]			= "";// $this->filter($parameters);
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["as"]				= "table";
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		$variables["HEADER"]			= "Web Boxes";
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
			$variables["PAGE_COMMAND"] = "WEBOBJECTS_LIST";
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
					"identifier"		=> $r["wo_identifier"],
					"ENTRY_BUTTONS" 	=> Array(),
					"attributes"		=> Array(
						Array(LOCALE_FILE_LABEL,$this->convert_amps($r["wo_label"]),"TITLE","NO"),
						Array("Show label",($r["wo_show_label"]==0?LOCALE_NO:LOCALE_YES),"SUMMARY","NO")
					)
				);
//				$dir_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"]));
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT",$this->module_command."EDIT",EDIT_EXISTING);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE",$this->module_command."REMOVE_CONFIRM",REMOVE_EXISTING);
			
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}
	
	function manage_layout_list($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"manage_layout_list",__LINE__,print_r($parameters,true)));
		}
		$sql = "Select * from web_layouts 
					left outer join theme_data on wol_theme = theme_identifier and (theme_secure = 0 or theme_secure = wol_client)
					where wol_client = $this->client_identifier";
//		print "<p>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables = Array();
		$variables["FILTER"]			= "";// $this->filter($parameters);
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		$variables["HEADER"]			= "Layout Structure Management";
		$variables["PAGE_BUTTONS"] = Array(
			Array("ADD",$this->module_command."LAYOUT_ADD",ADD_NEW)
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
			$variables["PAGE_COMMAND"] 		= $this->module_command."LAYOUT_LIST";
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
				if ($r["wol_theme"]<1){
					if($r["wol_theme"]==0){
						$theme = LOCALE_WEBOBJECTS_ANY_THEME;
					} else if($r["wol_theme"]==-1){
						$theme = LOCALE_WEBOBJECTS_PDA_THEME;
					} else if($r["wol_theme"]==-2){
						$theme = LOCALE_WEBOBJECTS_PRINTERFRIENDLY_THEME;
					}
				} else {
					$theme = $r["theme_label"];
				}
				$variables["RESULT_ENTRIES"][$i]=Array(
				"identifier"		=> $r["wol_identifier"],
				"ENTRY_BUTTONS" 	=> Array(),
				"attributes"		=> Array(
						Array(LOCALE_FILE_LABEL,$this->convert_amps($r["wol_label"]),"TITLE","NO"),
						Array(LOCALE_WEBOBJECTS_THEME,$this->convert_amps($theme),"SUMMARY","NO"),
						
						Array(LAYOUT,"<img src='/libertas_images/themes/site_administration/".$r["wol_layout_design"].".gif' width='80' height='60' />","YES","NO")
					)
				);
				if($r["wol_all_locations"]==1){
					$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_LOCATIONS, "All undefined locations","SUMMARY","NO");
				} else {
					$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_LOCATIONS, $this->get_menu_locations($r["wol_identifier"]),"SUMMARY","NO");
				}

//				$dir_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"]));
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT",$this->module_command."LAYOUT_EDIT",EDIT_EXISTING);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE",$this->module_command."LAYOUT_REMOVE_CONFIRM",REMOVE_EXISTING);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EXTRACT",$this->module_command."LAYOUT_COPY_LAYOUT",LOCALE_NEW_COPY);
				
			
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}
	function detail_form($parameters){
		$command=$this->check_parameters($parameters,"command");
		$wo_identifier=$this->check_parameters($parameters,"identifier",-1);
		$wo_label			= "";
		$wo_description		= "";
		$all_locations		= '1';
		$label				= "Layout Object Manager - ".ADD_NEW;
		$locations			= Array();
		$all_locations		= 0;
		$set_inheritance	= 0;
		$menu_locations		= Array();
		$wo_show_label		= 0;
		$display_tab		= $this->check_parameters($parameters,"display_tab");

		if ($wo_identifier!=-1){
			$this->call_command("EMBED_MANAGE_LINKS",Array("identifier"=>$wo_identifier, "editor"=> "wo_description"));
			$sql = "Select * from web_objects left outer join memo_information on (mi_link_id = wo_identifier and mi_type='$this->module_command') where (mi_type='$this->module_command' or mi_type is NULL) and wo_identifier=".$wo_identifier." and wo_client=$this->client_identifier";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$wo_label			= stripslashes($this->split_me($this->split_me($r["wo_label"],"&#39;","'"),"&quot;",'"'));
					$wo_description		= $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$r["mi_memo"]));
					
					$all_locations		= $r["wo_all_locations"];
					$set_inheritance	= $r["wo_set_inheritance"];
					$wo_show_label		= $r["wo_show_label"];
				}
			}
			$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
				Array(
					"module"		=> $this->webContainer."OBJECT",
					"identifier"	=> $wo_identifier
				)
			);

/*
			$sql = "Select * from web_objects_to_menu where wotm_object=".$wo_identifier." and wotm_client=$this->client_identifier";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$locations[count($locations)]	= $r["wotm_menu"];
				}
			}
*/
			$label="Layout Object Manager - ".EDIT_EXISTING;
		}
		
	//	$directories = $this->call_command("LAYOUT_DISPLAY_UPLOAD_DIRECTORY",array(-1,$file_directory));
		$out  = "<module name=\"web_objects\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[$label]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","WEBOBJECTS_LIST",LOCALE_CANCEL));
//		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("PREVIEW","WEBOBJECTS_PREVIEW",ENTRY_PREVIEW));
		$out .= "</page_options>";
		$out .= "<form name=\"webobj_form\" method=\"post\" label=\"$label\">";
		$out .= "<input type=\"hidden\" name=\"wo_identifier\" value=\"".$wo_identifier."\"/>";
		$out .= "<input type=\"hidden\" name=\"prev_command\" value=\"$command\"/>";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"WEBOBJECTS_SAVE\"/>";
		$out .= "<page_sections>";
		$out .= "<section label='Description' name='content'>";
		$out .= "<input type=\"text\" label=\"".WHAT_LABEL."\" name=\"wo_label\" required=\"YES\"><![CDATA[$wo_label]]></input>";
		$out .= "<select label=\"".LOCALE_WEBOBJECTS_SHOW_LABEL."\" name=\"wo_show_label\">
					<option value='0' ";
		if ($wo_show_label == 0){
			$out .= " selected='true'";
		}
		$out .= "><![CDATA[".LOCALE_NO."]]></option>
					<option value='1'";
		if ($wo_show_label == 1){
			$out .= " selected='true'";
		}
		$out .= "><![CDATA[".LOCALE_YES."]]></option>
				</select>";
		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .= "<textarea required=\"YES\" label=\"".SHORT_DESCRIPTION."\" size=\"40\" height=\"15\" name=\"wo_description\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$wo_description]]></textarea>";
		
			$val = $this->list_layouts_and_containers(Array("module"=> $this->webContainer, "identifier"=>$wo_identifier)); //
			$web_containers = split("~----~",$val);
			if ($web_containers[0]!=""){
				$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
				$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
			}
		$out .= "</section>";	
		$out .= $this->location_tab($all_locations, $set_inheritance,$menu_locations, $display_tab);
		$out .= "</page_sections>";
		$out .= "<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	function layout_form($parameters){
		$command					= $this->check_parameters($parameters,"command");
		$display_tab				= $this->check_parameters($parameters,"display_tab");
		$menu_locations				= Array();
		$identifier					= $this->check_parameters($parameters,"identifier",-1);
		$wol_label					= "";
		$wol_theme					= 0;
		$wol_description			= "";
		$header 					= "";
		$column1 					= "";
		$column2 					= "";
		$column3 					= "";
		$column4 					= "";
		$footer 					= "";
		$site_defination_layout		= "1111";
		$site_defination_label		= "";
		$user_defined_web_objects	= "";
		$system_defined_web_objects	= "";
		$themes						= Array();
		$wol_set_inheritance		= 0;
		$label						= "Layout Structure Manager - ".ADD_NEW;
		$wol_all_locations			= 0;
		$this->type_list			= Array(
										Array("System Object"	,"__SYSTEM__"),
										Array("General Object"	,"__OPEN__"),
										Array("User Defined"	,"__UD__")
									  );
		$menu_list 					= Array();
		$data = $this->call_command("ENGINE_RETRIEVE",Array("GET_WEB_CONTAINER"));
		$m = count($data);
		for ($i=0;$i<$m;$i++){
			if ($data[$i][1]!=""){
				$this->type_list[count($this->type_list)] = Array($this->get_constant($data[$i][0]."CONTAINER"),$data[$i][1]);
			}
		}
		$default_theme=0;
		$sql = "select theme_data.*, theme_client_has.theme_identifier as theme_id from theme_data left outer join theme_client_has on theme_client_has.theme_identifier = theme_data.theme_identifier and client_identifier = $this->client_identifier where theme_secure =0 or theme_secure = $this->client_identifier";
		
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$themes[count($themes)] = Array($r["theme_identifier"],$r["theme_label"]);
			if ($this->check_parameters($r,"theme_id","__NOT_FOUND__")!="__NOT_FOUND__"){
				$default_theme = $r["theme_identifier"];
			}
        }
        $this->call_command("DB_FREE",Array($result));
		$sql = "Select * from web_containers where wc_client=$this->client_identifier order by wc_type, wc_label";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$container ="";
		$max = count($this->type_list);
		$prev = "";
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$label = "";
			if ($prev!=$r["wc_type"]){
				$prev=$r["wc_type"];
				if ($container!=""){
					$container .= "</optgroup>";
				}
				for($index=0;$index<$max;$index++){
					$type = $this->check_parameters($this->type_list[$index], 1, "__SYSTEM__");
					if ($type == $r["wc_type"]){
						$label = "".$this->check_parameters($this->type_list[$index], 0)."";
					}
				}
				$container .= "<optgroup><label><![CDATA[$label]]></label>";
			}
			$container .= "<option value='".$r["wc_identifier"]."'><![CDATA[".$r["wc_label"]."]]></option>";
		}
		if ($container!=""){
			$container .= "</optgroup>";
		}
		if ($identifier!=-1){
			$sql = "select * from web_layouts 
						left outer join web_container_to_layout on wctl_layout = wol_identifier
						left outer join web_containers on wctl_container = wc_identifier
					where wol_client=$this->client_identifier and wol_identifier=$identifier
					order by web_container_to_layout.wctl_position asc, web_container_to_layout.wctl_rank asc";
//			print $sql;
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}
			if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$site_defination_layout	= $r["wol_layout_design"];
				$wol_all_locations		= $r["wol_all_locations"];
				$wol_theme				= $r["wol_theme"];
				$wol_set_inheritance	= $r["wol_set_inheritance"];
				$site_defination_label	= $this->stripquotes($r["wol_label"]);
				if ($this->check_parameters($r,"wc_identifier","__NOT_FOUND__")!="__NOT_FOUND__"){
					$placeholder = "
						<placeholder id='".$r["wc_identifier"]."' type='".$r["wc_type"]."' rank='".$r["wctl_rank"]."' width='".$r["wc_width"]."'>
							<label><![CDATA[".$this->stripquotes($r["wc_label"])."]]></label>
							<layout numCols='".$r["wc_layout_columns"]."'>rows</layout>
						</placeholder>";
					if ($r["wctl_position"]=="header"){
						$header .= $placeholder;
					}
					if ($r["wctl_position"]=="1"){
						$column1 .= $placeholder;
					}
					if ($r["wctl_position"]=="2"){
						$column2 .= $placeholder;
					}
					if ($r["wctl_position"]=="3"){
						$column3 .= $placeholder;
					}
					if ($r["wctl_position"]=="4"){
						$column4 .= $placeholder;
					}
					if ($r["wctl_position"]=="footer"){
						$footer .= $placeholder;
					}
				}
			}
			$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
				Array(
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier
				)
			);
			
		/*
			$sql = "select * from web_layout_to_menu where wor_layout=$identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$menu_list[count($menu_list)] = $r["wor_menu"];
			}
		*/
			$label="Layout Structure Manager - ".EDIT_EXISTING;
		}
		
	//	$directories = $this->call_command("LAYOUT_DISPLAY_UPLOAD_DIRECTORY",array(-1,$file_directory));
		$out  = "<module name=\"web_objects\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[".$this->stripquotes($label)."]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","WEBOBJECTS_LAYOUT_LIST",LOCALE_CANCEL));
//		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("PREVIEW","WEBOBJECTS_LAYOUT_PREVIEW",ENTRY_PREVIEW));
		$out .= "</page_options>";
		$out .= "<form name=\"webobjects_form\" method=\"post\" label=\"$label\">";
		$out .= "<showframe>1</showframe>";
		$out .= "<input type=\"hidden\" name=\"identifier\" value=\"".$identifier."\"/>";
		$out .= "<input type=\"hidden\" name=\"prev_command\" value=\"$command\"/>";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"WEBOBJECTS_LAYOUT_SAVE\"/>";
		$out .= "<input type=\"hidden\" name=\"layout_structure\" value=\"\"/>";
		$out .= "<page_sections>";
//		if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
			$out .= "<section label='".LOCALE_MAIN."' name='setup' onclick='display_columns'>";
			$out .= "<input type=\"text\" label=\"".WHAT_LABEL."\" name=\"wol_label\" required=\"YES\"><![CDATA[$site_defination_label]]></input>";
			$out .= "<select label=\"".LOCALE_WEBOBJECTS_WHAT_THEME."\" name=\"wol_theme\" onchange='javascript:check_theme(this)'>";
			for($i = 0 ; $i< count($themes);$i++){
				if ($themes[$i][0]==$wol_theme && $wol_theme != 0){
					$selected = " selected='true'";
				}else if ($themes[$i][0] == $default_theme && $wol_theme == 0){
					$selected = " selected='true'";
				}else{
					$selected = "";
				}
				$out .= "<option value='".$themes[$i][0]."' $selected><![CDATA[".$themes[$i][1]."]]></option>";
			}
			// special themes that user can toggle to from any theme not selectable as default theme theme ids are negative
			$out .= "<option value='".$this->THEME_PDA."'";
			if ($wol_theme==$this->THEME_PDA){
				$out .= " selected='true'";
			}
			$out .= "><![CDATA[".LOCALE_WEBOBJECTS_PDA_THEME."]]></option>";
			$out .= "<option value='".$this->THEME_PRINTERFRIENDLY."'";
			if ($wol_theme==$this->THEME_PRINTERFRIENDLY){
				$out .= " selected='true'";
			}
			$out .= "><![CDATA[".LOCALE_WEBOBJECTS_PRINTERFRIENDLY_THEME."]]></option>";
			$out .= "<option value='".$this->THEME_TEXTONLY."'";
			if ($wol_theme==$this->THEME_TEXTONLY){
				$out .= " selected='true'";
			}
			$out .= "><![CDATA[".LOCALE_WEBOBJECTS_TEXTONLY_THEME."]]></option>";
			
			$out .= "</select>";
//		} else {
//			$out .= "<input type=\"hidden\" name=\"wol_all_locations\" value=\"1\"/>";
//			$out .= "<input type=\"hidden\" name=\"wol_label\"><![CDATA[$wol_label]]></input>";
//		}
//		$out .= "<section label='".LOCALE_OBJECT_WIZARD."' name='objects' onclick='display_columns' type='horizontal'><parameters></parameters>";
		$out .= "<site_defination layout='$site_defination_layout'>
					<label><![CDATA[$site_defination_label]]></label>
					<placeholders counter='5'>
						<row id='header'>$header</row>
						<row id='content'>
							<column id='1'>$column1</column>
							<column id='2'>$column2</column>
							<column id='3'>$column3</column>
							<column id='4'>$column4</column>
						</row>
						<row id='footer'>$footer</row>
					</placeholders>
					<containers>$container</containers>
				</site_defination>
				<web_objects>
					$user_defined_web_objects
					$system_defined_web_objects
				</web_objects>
				<webTypes>";
				
		for ($i=0,$m=count($this->type_list);$i<$m;$i++){
			$out .= "<webType module='".$this->type_list[$i][1]."'><![CDATA[".$this->type_list[$i][0]."]]></webType>";
		}
		$out .= "	</webTypes>
				 </section>";
		$out .= $this->location_tab($wol_all_locations, $wol_set_inheritance, $menu_locations, $display_tab);
		if ($wol_theme == -1){
			$hidden =" hidden='true'";
		} else {
			$hidden ="";
		}
		$out .= "<section label='".LAYOUT."' name='layout' $hidden>";
		$wog_layout_array = Array(
			"1111"	=> "<img src='/libertas_images/themes/site_administration/1111.gif' width='80' height='60' />",
			"112"	=> "<img src='/libertas_images/themes/site_administration/112.gif' width='80' height='60' />",
			"121"	=> "<img src='/libertas_images/themes/site_administration/121.gif' width='80' height='60' />",
			"211"	=> "<img src='/libertas_images/themes/site_administration/211.gif' width='80' height='60' />",
			"22"	=> "<img src='/libertas_images/themes/site_administration/22.gif' width='80' height='60' />",
			"13"	=> "<img src='/libertas_images/themes/site_administration/13.gif' width='80' height='60' />",
			"31"	=> "<img src='/libertas_images/themes/site_administration/31.gif' width='80' height='60' />",
			"4"		=> "<img src='/libertas_images/themes/site_administration/4.gif' width='80' height='60' />"
		);
		$out .="<radio label=\"".LOCALE_ALL_LOCATIONS."\" name=\"wol_layout\">";
//		print $site_defination_layout;
		foreach ($wog_layout_array as $key =>$val){
			$out .="	<option value=\"$key\"";
			if 	($site_defination_layout."" == $key.""){
				$out .=" selected=\"true\"";
			}
			$out.= "><![CDATA[$val]]></option>";
		}
		$out .="</radio>";
		$out .= "</section>";
		$out .= "</page_sections>";
		$out .= "<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	
	
	function extract_list_of_webobjects($parameters){
		$container_identifier = $this->check_parameters($parameters,"container_identifier",-1);
		$id = $this->web_object_counter;
		$sql = 	"select * from web_objects_in_container 
					inner join web_objects on wo_identifier = woic_object and wo_client = woic_client
				where woic_client=$this->client_identifier and woic_container = $container_identifier order by woic_rank;";
		$web_objects ="";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$label 	= $r["wo_label"];
			$type 	= $r["wo_type"];
			$rank 	= $r["woic_rank"];
			$uid	= $r["wo_identifier"];
			$id++;
			$properties = $this->extract_properties(Array("wc_id"=> $r["woic_identifier"]));
			$web_objects .="<web_object type='$type' rank='$rank' id='$id' unique_id='$uid'>
				<label><![CDATA[".$this->stripquotes($label)."]]></label>
				<properties>
					$properties
				</properties>
			</web_object>";
		}
		$this->web_object_counter = $id;
		return $web_objects;
	}
	
	function extract_properties($parameters){
		$wc_id = $this->check_parameters($parameters,"wc_id");
		$sql = "select * from web_properties inner join web_objects_in_container on wop_container_object = woic_identifier and wop_client = woic_client where wop_client = $this->client_identifier and woic_identifier = $wc_id";
//		print "<p>$sql</p>";
		$result = $this->call_command("DB_QUERY",array($sql));
		$out ="";
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$out .= "<option><name><![CDATA[".$r["wop_name"]."]]></name><value><![CDATA[".$r["wop_value"]."]]></value></option>";
		}
		return $out;
		/*
		
		db holds key paires (property => $value)
		
		
			width
			halignment
			valignment
			height
			margin
		*/
	}
	
	
	function save($parameters){
		$wo_identifier		= $this->check_parameters($parameters,"wo_identifier",-1);
//		$wo_locations		= $this->check_parameters($parameters,"wo_locations",1);
//		$wo_all_locations	= $this->check_parameters($parameters,"wo_all_locations",0);
		$menu_locations		= $this->check_parameters($parameters, "menu_locations", Array());
		$wo_all_locations	= $this->check_parameters($parameters, "all_locations");
		$set_inheritance	= $this->check_parameters($parameters, "set_inheritance");
		$wo_show_label		= $this->check_parameters($parameters, "wo_show_label",0);
//		$wo_description		= trim($this->call_command("EDITOR_CONVERT_FONT_TO_SPAN", Array("string" => $this->split_me( $this->tidy( $this->validate($this->check_parameters($parameters,"wo_description") ) ),"'","&#39;") ) ));
		$replacelist		= $this->check_parameters($parameters,"web_containers",Array());
		$wo_description		= htmlentities(
								$this->split_me(
									$this->call_command("EDITOR_CONVERT_FONT_TO_SPAN", 
										Array("string" =>  
											$this->validate( 
												htmlentities(
													$this->tidy( 
														$this->check_parameters($parameters, "wo_description") 
													)
												)
											)
										)
									),
									"'",
									"&#39;"
									)
								);
		$wo_label 			= trim($this->strip_tidy($this->check_parameters($parameters,"wo_label")));
		$list_of_embedded_information			= $this->call_command("EMBED_EXTRACT_INFO",Array("str" => $wo_description));
		if ($wo_identifier==-1){
			$wo_created = $this->libertasGetDate("Y/m/d H:i:s");
			$sql ="insert into web_objects (wo_client, wo_type, wo_label, wo_all_locations, wo_command, wo_created, wo_owner_module, wo_set_inheritance, wo_show_label) 
						values 
					($this->client_identifier, 0, '".$wo_label."', '$wo_all_locations', 'WEBOBJECTS_EXTRACT', '$wo_created', 'WEBOBJECTS_', '$set_inheritance', '$wo_show_label');";
			$this->call_command("DB_QUERY",array($sql));
			$sql ="select * from web_objects where
				wo_client= $this->client_identifier and 
				wo_type = 0 and 
				wo_label = '".$wo_label."' and 
				wo_all_locations = '$wo_all_locations' and
				wo_created = '$wo_created' and 
				wo_owner_module = 'WEBOBJECTS_' and
				wo_command = 'WEBOBJECTS_EXTRACT';";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$wo_identifier = $r["wo_identifier"];
			}
//			$this->call_command("DB_FREE",Array($result));
			$sql = "update web_objects set wo_owner_id = $wo_identifier where wo_identifier=$wo_identifier and wo_client=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			
			$this->call_command("EMBED_SAVE_INFO",Array("list_of_results" => $list_of_embedded_information, 		"id" => $wo_identifier, "editor"=>"wo_description", 	"module"=>$this->module_command));
			if ($wo_description){
				$this->call_command("MEMOINFO_INSERT",array("mi_type"=>$this->module_command,"mi_memo"=>$wo_description,"mi_link_id" => $wo_identifier, "mi_field" => "wo_description"));
			}
		} else {
			$this->call_command("EMBED_SAVE_INFO",Array("list_of_results" => $list_of_embedded_information, 		"id" => $wo_identifier, "editor"=>"wo_description", 	"module"=>$this->module_command));
			$sql ="update web_objects set wo_show_label='$wo_show_label', wo_all_locations='$wo_all_locations', wo_label='".$wo_label."', wo_set_inheritance= '$set_inheritance' where wo_client = $this->client_identifier and wo_type=0 and wo_identifier=$wo_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
//			if ($wo_description!=''){ 
				$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->module_command,"mi_memo"=>$wo_description, "mi_link_id" => $wo_identifier, "mi_field" => "wo_description", "debug" =>0));
//			}
		}

		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->webContainer."OBJECT",
				"identifier"	=> $wo_identifier,
				"all_locations"	=> $wo_all_locations
			)
		);
		$cond = join(",",$replacelist);
		if ($cond==""){
			$sql = "delete from web_objects_in_container where woic_client=$this->client_identifier and woic_object='$wo_identifier'";
		} else {
			$sql = "delete from web_objects_in_container where woic_client=$this->client_identifier and woic_object='$wo_identifier' and woic_container not in (".$cond.") ";
		}
		$this->call_command("DB_QUERY",array($sql));
		for($i=0,$m=count($replacelist);$i<$m;$i++){
			$sql = "select * from web_objects_in_container where woic_client=$this->client_identifier and woic_container='".$replacelist[$i]."'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$woic_rank =0;
			$found=0;
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				if ($r["woic_rank"]>$woic_rank){
            		$woic_rank = $r["woic_rank"];
				}
				if ($r["woic_object"]==$wo_identifier){
					$found =1;
				}
            }
            $this->call_command("DB_FREE",Array($result));
			$woic_rank++;
			if ($found==0){
				$sql = "insert into web_objects_in_container (woic_client, woic_container, woic_object, woic_rank) values($this->client_identifier, ".$replacelist[$i].", $wo_identifier, $woic_rank)";
			}
			$this->call_command("DB_QUERY",array($sql));
		}

		
		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance("WEBOBJECTS_EXTRACT",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=>$child_locations,
					"module"		=> $this->webContainer."OBJECT",
					"identifier"	=> $wo_identifier,
					"all_locations"	=> $all_locations,
					"delete"		=>0
				)
			);
			$this->set_inheritance(
				"WEBOBJECTS_EXTRACT",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"		=> $this->webContainer."OBJECT",
					"condition"		=> "wo_set_inheritance =1 and ",
					"client_field"	=> "wo_client",
					"table"			=> "web_objects",
					"primary"		=> "wo_identifier"
					)
				).""
			);
		}
	}
	
	function remove_confirm($parameters){
		$wo_identifier = $this->check_parameters($parameters,"identifier",-1);
//		$sql ="delete from web_objects_to_menu where wotm_client=$this->client_identifier and wotm_object=$wo_identifier";
//		$this->call_command("DB_QUERY",array($sql));
		$sql ="delete from web_objects where wo_client=$this->client_identifier and wo_identifier=$wo_identifier";
		$this->call_command("DB_QUERY",array($sql));
		$sql ="delete from web_objects_in_container where woic_client=$this->client_identifier and woic_identifier=$wo_identifier";
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("LAYOUT_MENU_TO_OBJECT_REMOVE", Array(
				"module"		=>	$this->webContainer."OBJECT", 
				"identifier"	=>	$wo_identifier
			)
		);

	}
	
	function layout_remove_confirm($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$sql = "select * from web_containers where wc_client=$this->client_identifier and wc_layout_identifier=$identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		$list = Array();
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$list[count($list)] = $r["wc_identifier"];
		}
		$sql ="delete from web_container_to_layout where wctl_client=$this->client_identifier and wctl_layout=$identifier";
		$this->call_command("DB_QUERY",array($sql));
		$sql ="delete from web_layouts where wol_client = $this->client_identifier and wol_identifier = $identifier";
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("LAYOUT_MENU_TO_OBJECT_REMOVE", Array(
				"module"		=>	$this->webContainer, 
				"identifier"	=>	$identifier
			)
		);
	}
	
	function layout_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name, "layout_save", __LINE__, print_r($parameters,true)));
		}

		// if the web objects index is not the last inthe row there will be a problem. add a new field to the container increment this counter.
		$web_objects_index = 9;
		
		$layout 			= $this->check_parameters($parameters,"layout_structure");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$layout]"));
		}
		$identifier			= $this->check_parameters($parameters,"identifier",-1);
		$wol_layout			= $this->check_parameters($parameters,"wol_layout","1111");
		$wol_label			= $this->check_parameters($parameters,"wol_label");
		$wol_theme			= $this->check_parameters($parameters,"wol_theme",0);
		$all_locations		= $this->check_parameters($parameters,"all_locations",0);
		$set_inheritance	= $this->check_parameters($parameters,"set_inheritance",0);
		$menu_locations		= $this->check_parameters($parameters,"menu_locations",Array());
		$list_of_placeholders_to_keep = "";
		$now = Date ("Y/m/d H:i:s");
		
		if ($all_locations==1){
			$sql = "update web_layouts set wol_all_locations=0 where wol_client=$this->client_identifier and wol_theme= $wol_theme";
			$this->call_command("DB_QUERY",array($sql));
		}
		if ($identifier == -1){
			$sql = "insert into web_layouts 
						(wol_client, wol_status, wol_version, wol_live, wol_complete, wol_label, wol_layout_design, wol_all_locations, wol_created, wol_set_inheritance, wol_theme) 
					values 
						($this->client_identifier, 1, 1, 1, 1, '$wol_label', '$wol_layout', '$all_locations', '$now', $set_inheritance, $wol_theme)";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "select * from web_layouts where wol_created='$now' and wol_client= $this->client_identifier and wol_label='$wol_label' and  wol_layout_design='$wol_layout' and wol_theme=$wol_theme";
			if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($result){
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$identifier = $r["wol_identifier"];
				}
			}
			
		} else {
			$sql = "update web_layouts 
						set 
						wol_label 		  	= '$wol_label',
						wol_layout_design 	= '$wol_layout',
						wol_live 			= 1,
						wol_all_locations	= '$all_locations',
						wol_set_inheritance = '$set_inheritance',
						wol_theme			= '$wol_theme'
					where 
						wol_client 		  	= $this->client_identifier and 
						wol_identifier    	= $identifier
				";
			if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
			$this->call_command("DB_QUERY",array($sql));
		}
		

		/*********************	Start Apply Theme to a specific menu portion (Added by Muhammad Imran)*****************/
		if (is_array($menu_locations)){
			/*	Start Update Menu theme and layout columns to zero for old entry portion */
			/*
			$sql = "select * from menu_data where menu_client= $this->client_identifier and menu_layout=$identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($result){
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$menu_identifier = $r["menu_identifier"];
					$menu_theme = $r["menu_theme"];
					$menu_layout = $r["menu_layout"];
				}
			}
			$sql_menu_data = "update menu_data 
					set 
					menu_theme 		  	= '0'
				where 
					menu_client 		= $this->client_identifier and 
					menu_theme 			= $wol_theme and 
					menu_identifier    	= $identifier
			";
			$this->call_command("DB_QUERY",array($sql_menu_data));
			*/
			$max_menus = count($menu_locations);
			if ($max_menus > 0){
				$sql_menu_data = "update menu_data 
						set 
						menu_theme 		  	= '0',
						menu_layout		  	= '0'
					where 
						menu_client			= $this->client_identifier and 
						menu_layout    		= $identifier
				";
				$this->call_command("DB_QUERY",array($sql_menu_data));
			}
			/*	End Update Menu theme and layout columns to zero for old entry portion */
			/*	Start Set Menu theme and layout according to selected theme and menu location portion */

			for($index=0; $index<$max_menus;$index++){
				$sql_menu_data = "update menu_data 
						set 
						menu_theme 		  	= '$wol_theme',
						menu_layout    		= $identifier
					where 
						menu_client 		= $this->client_identifier and 
						menu_identifier    	= $menu_locations[$index]
				";
				$this->call_command("DB_QUERY",array($sql_menu_data));
			}
			/*	End Set Menu theme and layout according to selected theme and menu location portion */
		}
		/*********************	End Apply Theme to a specific menu portion (Added by Muhammad Imran)*****************/



		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier,
				"all_locations"	=> $all_locations
			)
		);
		$this->tidy_menu($wol_theme,$identifier);
		if ($set_inheritance==1){
		}

		/*
			start to split up the containers (called placeholders here)
			delete any from layout that are no longer listed
		*/
		if ($layout!=""){
			$placeholders = split(":~~~~:",$layout);
			$max_placeholders = count($placeholders);
			if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$max_placeholders]"));
			}
			for ($index=0; $index < $max_placeholders ;$index++){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[".$placeholders[$index]."]"));
				}
				$placeholders[$index] = split(":~:",$placeholders[$index]);
				if (strpos($placeholders[$index][1],"newplaceholder")===false){
					$val = $this->get_column($placeholders[$index][1],"placeholder");
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[".$val."]"));
					}
					if ($index>0){
						$list_of_placeholders_to_keep .=",";
					}
					$list_of_placeholders_to_keep .=$val;
				}
				$placeholders[$index][$web_objects_index] = split(":____:",$placeholders[$index][$web_objects_index]);
				if ($placeholders[$index][$web_objects_index]!=''){
					/*
						was index 8 ?????
					*/
					$max_widgets = count($placeholders[$index][$web_objects_index]);
					for($i=0; $i < $max_widgets ;$i++){
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[($i)::<B>[".$placeholders[$index][$web_objects_index][$i]."]</B>]"));
						}
						$placeholders[$index][$web_objects_index][$i] = split(":_:", $placeholders[$index][$web_objects_index][$i]);
					}
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[6::<B>[".$this->check_parameters($placeholders[$index],6,"6__NA__")."]</B>]"));
					}
				}
			}
		} else {
			$placeholders= Array();
		}
		$wctl_layout=$identifier;
		$max = count($placeholders);
		$sql ="delete from web_container_to_layout where wctl_client = $this->client_identifier and wctl_layout=$wctl_layout";
		$this->call_command("DB_QUERY",array($sql));
		for ($index=0; $index<$max;$index++){
			$container_id = substr($placeholders[$index][1],11);
			$position_id = substr($placeholders[$index][2],6);
			$rank = $placeholders[$index][3];
			$sql ="insert into web_container_to_layout (wctl_container, wctl_client ,wctl_rank ,wctl_position, wctl_layout) values ($container_id, $this->client_identifier ,$rank, '$position_id', $wctl_layout)";
			$this->call_command("DB_QUERY",array($sql));
		}
//		print_r($placeholders);
		/*
		$sql = "select * from web_containers where wc_identifier not in ($list_of_placeholders_to_keep) and wc_client=$this->client_identifier and wc_layout_identifier = $identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		$remove_list = "";
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				if ($remove_list!=""){
					$remove_list.=", ";
				}
				$remove_list .= $r["wc_identifier"];
			}
		}
		$sql = "delete from web_containers where wc_identifier in ($remove_list) and wc_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		$sql = "delete from web_object_in_container where woic_container in ($remove_list) and woic_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		*/
//		$this->exitprogram();
		$this->save_container_css();
	}
	
	function get_column($str, $strip){
		return substr($str, strlen($strip));
	}
	
	function extract_webobject($parameters){
		$cmd = $this->check_parameters($parameters,"command");
		$pos = $this->check_parameters($parameters,"position");
		if ($pos==2 && $cmd!=""){
			return "";
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"extract_webobject",__LINE__,print_r($parameters,true)));
		}
		$identifier = $this->check_parameters($parameters, "identifier", -1);
		$sql ="select * from web_objects 
				left outer join memo_information on (mi_link_id = wo_identifier and mi_type='".$this->module_command."')
				where wo_client=$this->client_identifier and wo_identifier = $identifier";
		$sql ="select * from web_objects 
					left outer join memo_information on (mi_link_id = wo_identifier and mi_type='WEBOBJECTS_')
					left outer join menu_to_object on (mto_object = wo_identifier  and menu_to_object.mto_module = 'WEBOBJECTS_OBJECT'  and wo_client=mto_client)
					left outer join menu_data on menu_identifier = mto_menu and menu_client=mto_client
				where 
					wo_client=$this->client_identifier and 
					wo_identifier = $identifier and 
					(
						(wo_all_locations =0 and menu_data.menu_url='".$this->parent->script."') or 
						(wo_all_locations=1 and mto_object is null)
					)";


		//print "".__LINE__." ".__FILE__." $sql";
//		$this->exitprogram();
		$result		= $this->call_command("DB_QUERY",Array($sql));
		$wo_label		= "";
		$wo_cdata		= "";
		$wo_show_label	= 0;
		if ($result){
			$c=0;
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$c++;
				$wo_identifier 	= $r["wo_identifier"];
				$wo_label		= $r["wo_label"];
				$wo_cdata		= $r["mi_memo"];
				$wo_command		= $r["wo_command"];
				$wo_show_label	= $r["wo_show_label"];
			}
			$this->call_command("DB_FREE",Array($result));
			/*
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            - this table uses trans_identifier as the link identifier (legacy code)
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            */
			if($c>0){
				$sql = "select distinct * from embed_libertas_form where client_identifier = $this->client_identifier and module_starter='WEBOBJECTS_' and trans_identifier=$identifier";
				$result  = $this->call_command("DB_QUERY",Array($sql));
				$list_of_forms=Array();
				$out="";
		        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					if ($r["form_int_identifier"]>0){
        				$list_of_forms[count($list_of_forms)] = "libertas_form_".$r["form_int_identifier"];
					} else {
						$list_of_forms[count($list_of_forms)] = $r["form_str_identifier"];
					}
    		    }
				$out.= $this->call_command("SFORM_LOAD_CACHE",Array("list_of_forms"=>$list_of_forms));
	        	$this->call_command("DB_FREE",Array($result));
				if ($wo_show_label==0){
					$str = "<data><![CDATA[".$wo_cdata."]]></data>";
				}else {
					$str = "<label><![CDATA[".$wo_label."]]></label><data><![CDATA[".$wo_cdata."]]></data>";
				}
				if($out!=""){
					$out .= "<uid>".md5(uniqid(rand(), true))."</uid>";
				}
				return $str.$out;
			} else {
				return "";
			}
		} else {
			return "";
		}
	}
	
	function copy_layout($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_layout",__LINE__,print_r($parameters,true)));
		}
		$identifier = $this->check_parameters($parameters,"identifier");
		$new_id		= -1;
		$sql = "select * from web_layouts where wol_client= $this->client_identifier and wol_identifier=$identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$wol_label			= $r["wol_label"];
				$wol_layout_design	= $r["wol_layout_design"];
				$wol_theme			= $r["wol_theme"];
				$sql = "insert into web_layouts (wol_theme, wol_client, wol_label, wol_status, wol_version, wol_live, wol_complete, wol_layout_design, wol_all_locations, wol_set_inheritance, wol_created) 
							values 
						($wol_theme, $this->client_identifier, 'Copy of $wol_label', 1, 1, 0, 1, $wol_layout_design, 0, 0, '$now')
						";
				$this->call_command("DB_QUERY",array($sql));
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
				}
				$sql = "Select * from web_layouts where wol_label = 'Copy of $wol_label' and wol_layout_design ='$wol_layout_design' and wol_client= $this->client_identifier and wol_created='$now'";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
				}
				$insert_result = $this->call_command("DB_QUERY",array($sql));
				while ($insert_r = $this->call_command("DB_FETCH_ARRAY",array($insert_result))){
					$new_id = $insert_r["wol_identifier"];
					$sql = "select * from web_container_to_layout where wctl_client = $this->client_identifier and wctl_layout = $identifier";
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
					}
					$wc_result = $this->call_command("DB_QUERY",array($sql));
					while ($wc_r = $this->call_command("DB_FETCH_ARRAY",array($wc_result))){
						$wctl_container		= $this->check_parameters($wc_r,"wctl_container");
						$wctl_position		= $this->check_parameters($wc_r,"wctl_position");
						$wctl_rank			= $this->check_parameters($wc_r,"wctl_rank");
						$sql ="insert into web_container_to_layout (wctl_client, wctl_layout, wctl_container, wctl_position, wctl_rank) 
									values
								($this->client_identifier, $new_id, '$wctl_container', '$wctl_position', $wctl_rank)";
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
						}
						$this->call_command("DB_QUERY",array($sql));
						/*
						$sql = "select * from web_containers where wc_client=$this->client_identifier and wc_layout_identifier='$new_id' and wc_label='$wc_label' and wc_position='$wc_position' and wc_rank='$wc_rank' and wc_layout_type='$wc_layout_type' and wc_layout_columns='$wc_layout_columns' and wc_created='$now'";
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
						}
						$insert_wc_result = $this->call_command("DB_QUERY",array($sql));
						while ($insert_wc_r = $this->call_command("DB_FETCH_ARRAY",array($insert_wc_result))){
							$new_wc_identifier = $insert_wc_r["wc_identifier"];
							$sql ="select * from web_objects_in_container where woic_client=$this->client_identifier and woic_container=$wc_identifier";
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
							}
							$woic_result = $this->call_command("DB_QUERY",array($sql));
							while ($woic_r = $this->call_command("DB_FETCH_ARRAY",array($woic_result))){
								$woic_object	= $woic_r["woic_object"];
								$woic_rank		= $woic_r["woic_rank"];
								$sql = "insert into web_objects_in_container (woic_client, woic_container, woic_object, woic_rank) values ($this->client_identifier, $new_wc_identifier, $woic_object, $woic_rank)";
								if ($this->module_debug){
									$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
								}
								$this->call_command("DB_QUERY",array($sql));
							}
						}
						*/
					}
				}
			}
		}
		return $new_id;
	}
	

	function get_menu_locations($id){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_menu_locations",__LINE__,"id:$id"));
		}
		$sql = "select * from menu_to_object inner join menu_data on menu_data.menu_identifier = menu_to_object.mto_menu where mto_client = $this->client_identifier and mto_object=$id and mto_module='$this->webContainer' order by menu_url";
		$result = $this->call_command("DB_QUERY",array($sql));
		$out="";
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$out .= $this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL", Array("id"=>$r["mto_menu"]))."<br/>";
			}
		}
		return $out;
	}
			function strlastpos($haystack, $needle) { 
		# flip both strings around and search, then adjust position based on string lengths 
		return strlen($haystack) - strlen($needle) - strpos(strrev($haystack), strrev($needle)); 
		} 

    /* Function added by Muhammad Imran to get file name from a url*/
	function getfname($url){ 
        $pos = strrpos($url, "/"); 
        if ($pos === false) { 
            // not found / no filename in url... 
            return false; 
        } else { 
            // Get the string length 
            $len = strlen($url); 
            if ($len < $pos){ 
                        print "$len / $pos"; 
                // the last slash we found belongs to http:// or it is the trailing slash of a URL 
                return false; 
            } else { 
                $filename = substr($url, $pos+1, $len-$pos-1); 
            } 
        } 
        return $filename; 
    } 
	
	
	function extract_settings(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"extract_settings",__LINE__,""));
		}
		$display_layout ="121";
		$condition	= " wol_theme = theme_client_has.theme_identifier and ";
		//$join 		= " inner join theme_client_has on client_identifier = wol_client and wol_theme = theme_client_has.theme_identifier";
		$join 		= " left outer join theme_client_has on wol_theme = theme_client_has.theme_identifier";
		if (($this->parent->choosen_theme!=0) && ($this->parent->choosen_theme!=-2) && ($this->parent->choosen_theme!=-3)){
			$condition ="wol_theme = ".$this->parent->choosen_theme." and ";
			$join="";
		}
		
		/* Get Basket to other than home page layout portion starts (Added By Muhammad Imran Mirza) */	
		$path = $_SERVER["REQUEST_URI"];
		$path = "/".$this->getfname($path,'/');
/*		while (substr ($path, -1) != '/'){
		  $path = substr( $path, 0, -1);
		}
*/		

		if ($path == "/_view-cart.php" || $path == "/_purchase-cart.php")
			$extra_condition = " ";
		else
			$extra_condition = " or menu_url = '".$this->parent->script."'";
		/* Get Basket to other than home page layout portion ends (Added By Muhammad Imran Mirza) */	
	
		$sql ="select distinct 
					wol_all_locations, wol_layout_design
				from web_layouts 
					left outer join menu_to_object on mto_object = wol_identifier and mto_client=wol_client and mto_module='WEBOBJECTS_' 
					left outer join menu_data on menu_identifier = mto_menu 
					left outer join web_container_to_layout on wctl_layout = wol_identifier
					left outer join web_containers on wctl_container = wc_identifier
					left outer join web_objects_in_container on woic_container = wc_identifier 
					left outer join web_objects on wo_identifier = woic_object 
					left outer join display_data on display_command = wo_command 
					$join 
				where 
					$condition
					(
						 wol_client=$this->client_identifier and wol_live = 1 
					)and 
					(
						menu_url is null $extra_condition) 
					and (wol_all_locations=1 or 
						(wol_all_locations=0 and 
							mto_menu is not null
						)
					)
				order by 
					wol_all_locations desc, wol_layout_design";
					

		/* Starts Apply Default Layout to Basket or all System Generated Root level Pages (Added By Muhammad Imran)*/
		if ($path == "/_view-cart.php" || $path == "/_purchase-cart.php"){
			$sql_web_layouts = "select * from web_layouts where wol_client = $this->client_identifier and wol_default=1";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql_web_layouts\n\n\n".$this->check_parameters($_SESSION,"displaymode")));
			}
			$web_layouts_results = $this->parent->db_pointer->database_query($sql_web_layouts);
			$number_of_rows_returned = $this->call_command("DB_NUM_ROWS",Array($web_layouts_results));
			if ($number_of_rows_returned > 0)
				$sql = $sql_web_layouts;//override above query
		}
		/* Ends Apply Default Layout to Basket or all System Generated Root level Pages (Added By Muhammad Imran)*/

//		print $sql;
//		$this->exitprogram();
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[".$sql."]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$display_layout = $r["wol_layout_design"];
			}
		}
		return "<setting name='display_layout'><![CDATA[".$display_layout."]]></setting>";
	}

	function save_container_css(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save_container_css",__LINE__,""));
		}
		$sql = "select distinct wc_width from web_containers where wc_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$out="";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$width = $r["wc_width"];
				$pos = strpos($width,"%");
				if ($pos===false){
					$width		= "width"."$width";
				} else {
					$percent	= substr($width,0,$pos);
					$width 		= "width"."$percent"."percent";
				}
				$out .= ".$width { width:".$r["wc_width"]." }\n";
			}
		}
		$root		 = $this->parent->site_directories["ROOT"];
		$fp = @fopen($root."/container.css","w");
		@fputs($fp,$out);
		@fclose($fp);
		
	}
	
	function get_webobject_properties($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_webobject_properties",__LINE__,print_r($parameters,true)));
		}
		$woic_id = $this->check_parameters($parameters,"identifier");
		$sql = "select wop_name, wop_value from web_properties where wop_container_object = $woic_id and wop_client = $this->client_identifier";
//		print $sql;
		$result = $this->call_command("DB_QUERY",array($sql));
		$out  = "<property id=\"$woic_id\">";
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$name = $r["wop_name"];
				$value= $r["wop_value"];
				if (strlen($name)!=0)
					$out .= "<option ><name><![CDATA[$name]]></name><value><![CDATA[$value]]></value></option>";
			}
		}
		$out .= "</property>";
		return $out;	
	}

	function store_channels(){
		$module_display_options_array = $this->call_command("ENGINE_RETRIEVE",array("MENU_DISPLAY_OPTIONS",array(array(),1)));
		$module_display_options="";
		$length_of_array=count($module_display_options_array);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"length of display options",__LINE__,"$length_of_array"));
		}
		for($index=0;$index<$length_of_array;$index++){
			if (count($module_display_options_array[$index][1])>0){
				for($i=0;$i<count($module_display_options_array[$index][1]);$i++){
					if ($module_display_options_array[$index][1][$i][0]!=""){
						$sql ="insert into web_objects (wo_client, wo_type, wo_label, wo_command, wo_all_locations, wo_show_label) values ($this->client_identifier, 1, '".$this->get_constant($module_display_options_array[$index][1][$i][1])."', '".$module_display_options_array[$index][1][$i][0]."',0,0);";
						$this->call_command("DB_QUERY",array($sql));
					}
				}
			}
		}
//$module_display_options
//			Array('Display the SiteMap','WEBOBJECTS_SHOW_SITEMAP_EXTRACT',1,0),
		$list = Array(
			Array('Show Page Options',			'WEBOBJECTS_SHOW_PRINTER_FRIENDLY',1,0),
			Array('Show Todays Date',			'WEBOBJECTS_SHOW_DATE',1,0),
			Array('Show Site Updated Date',		'WEBOBJECTS_SHOW_SITE_UPDATED_DATE',1,0),
			Array('Show Page Updated Date',		'WEBOBJECTS_SHOW_PAGE_UPDATED_DATE',1,0),
			Array('Show simple login form',		'WEBOBJECTS_SHOW_LOGIN',1,0),
			Array('Show Bread Crumb Trail',		'WEBOBJECTS_SHOW_BREADCRUMB',1,0),
			Array('Show main Menu here',		'WEBOBJECTS_SHOW_MAIN_MENU',1,0),
			Array('Show Sub level menu if any here','WEBOBJECTS_SHOW_SUB_MENU',1,0),
			Array('Show Search in (column)',	'WEBOBJECTS_SHOW_SEARCH_BOX_COLUMN',1,0),
			Array('Show Search in (row)',		'WEBOBJECTS_SHOW_SEARCH_BOX_ROW',1,0),
			Array('Show Textural Menu Level 1',	'WEBOBJECTS_SHOW_TEXT_BASED_LEVEL_ONE_MENU',0,0),
			Array('Show Top of page option',	'WEBOBJECTS_SHOW_TOP_OF_PAGE',1,0),
			Array("Home page Link",		 		"WEBOBJECTS_SHOW_HOME",			1, 0),
			Array("Show archive access point",	"WEBOBJECTS_SHOW_ARCHIVE_OPTIONS",	1, 0)
		);
		$m = count($list);
		for($i = 0 ; $i < $m ; $i ++ ){
			$sql = "insert into web_objects (wo_client,wo_type,wo_label,wo_command,wo_all_locations,wo_show_label) values ($this->client_identifier,2,'".$list[$i][0]."','".$list[$i][1]."',".$list[$i][2].",".$list[$i][3].");";
			$this->call_command("DB_QUERY",array($sql));
		}
		$m = count($this->module_display_options);
		for($i = 0 ; $i < $m ; $i ++ ){
			$sql = "insert into web_objects (wo_client,wo_type,wo_label,wo_command,wo_all_locations,wo_show_label) values ($this->client_identifier, 1, '".$this->get_constant($this->module_display_options[$i][1])."', '".$this->module_display_options[$i][0]."', 0, 0);";
			$this->call_command("DB_QUERY",array($sql));
		}
	}
	/**
	* function manage_module_webobjects(...)
	* this function will allow the system to manage the webobject list by being able to 
	* add multiple modules of one type on a page with different identifiers :)
	*/
	function manage_module_webobjects($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"manage_module_webobjects",__LINE__,print_r($parameters,true)));
		}

		$wo_owner_mod	= $this->check_parameters($parameters,"owner_module");
		$wo_label		= $this->check_parameters($parameters,"label");
		$wo_owner_id	= $this->check_parameters($parameters,"owner_id");
		$wo_command		= $this->check_parameters($parameters,"wo_command");
		$props			= $this->check_parameters($parameters,"property",Array());
		$cmd			= $this->check_parameters($parameters,"cmd","ADD");
		/*************************************************************************************************************
		* these two parameters tell the function to insert the webobject into the following 
		* containers.
		* 
		* 1. on add we want to extract the new webobject identifier and simply insert onto end
		*	 of each container
		* 2. on remove nothing simplier just remove all references
		* 3. On Update we have a more complex route to take.
		*   	1. remove records not in new list
		*		2. extract list of existing objects (which we can ignore which will leave rank in place) 
		*		3. insert onto end of container web object.
		*************************************************************************************************************/
		$previous_list	= $this->check_parameters($parameters,"previous_list");
		$new_list		= $this->check_parameters($parameters,"new_list",Array());
		
		$wo_show_label = $this->check_parameters($props,"show_label",0);
		if ($cmd == "ADD" || $cmd=="INSERT"){
			$now = $this->libertasGetDate("Y/m/d H:i:s");
			$wo_id = $this->getUID();
			/* altered 01-01-2006  wo_identifier should be auto increment so we dont want to be putting in our own -steve b
			$sql = "insert into web_objects 
							(wo_identifier, wo_client, wo_type, wo_label, wo_command, wo_all_locations, wo_show_label, wo_owner_module, wo_owner_id, wo_created) 
						values 
							($wo_id, $this->client_identifier, 1, '$wo_label', '$wo_command', 0, $wo_show_label, '$wo_owner_mod', '$wo_owner_id', '$now');";

							*/
$sql = "insert into web_objects 
							(wo_client, wo_type, wo_label, wo_command, wo_all_locations, wo_show_label, wo_owner_module, wo_owner_id, wo_created) 
						values 
							($this->client_identifier, 1, '$wo_label', '$wo_command', 0, $wo_show_label, '$wo_owner_mod', '$wo_owner_id', '$now');";
							/* end of alteration */

			$this->call_command("DB_QUERY",array($sql));
			if (is_array($new_list)){
				$this->addtoendoflist($wo_id, $new_list);
			}
		}
		if ($cmd == "UPDATE"){
			$sql = "Update web_objects set wo_label = '$wo_label', wo_show_label= '$wo_show_label' where wo_client = $this->client_identifier and wo_type = 1 and wo_owner_module = '$wo_owner_mod' and wo_owner_id = '$wo_owner_id'";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[".$sql."]"));
			}
			$this->call_command("DB_QUERY",array($sql));
			if (is_array($new_list)){
				$sql = "select * from web_objects where wo_client = $this->client_identifier and wo_type = 1 and wo_owner_module = '$wo_owner_mod' and wo_owner_id = '$wo_owner_id'";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
				}
				$result  = $this->call_command("DB_QUERY",Array($sql));
				$id = -1;
	            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	            	$id = $r["wo_identifier"];
	            }
	            $this->call_command("DB_FREE",Array($result));
				if ($id!=-1){
					$str = join(",",$new_list);
					$sql = "select wc_identifier from web_containers where wc_client = $this->client_identifier"; //  and wc_type!='__SYSTEM__'
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[".$sql."]"));
					}
					$my_list = "";
					$result  = $this->call_command("DB_QUERY",Array($sql));
                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
                    	if ($my_list!=""){
							$my_list.=",";
						}
						$my_list.=$r["wc_identifier"];
					}
                    $this->call_command("DB_FREE",Array($result));
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[".print_r($my_list,true)."]"));
					}
					if($my_list!=""){
						if ($str==""){
							$sql = "delete from web_objects_in_container where woic_client = $this->client_identifier and woic_object = $id and woic_container in (".$my_list.")";
						} else {
							$sql = "delete from web_objects_in_container where woic_client = $this->client_identifier and woic_object = $id and woic_container in (".$my_list.") and woic_container not in (".$str.")";
						}
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
						}
						$this->call_command("DB_QUERY",array($sql));
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
						}
						if ($str==""){
						} else {
							$sql = "select woic_container from web_objects_in_container 
									where woic_container in (".$str.") and woic_client = $this->client_identifier and woic_object = $id 
									group by woic_container
									order by woic_container";
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
							}
							$result  = $this->call_command("DB_QUERY",Array($sql));
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[".print_r($new_list,true)."]"));
							}
							$max = count($new_list);
							while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
								for ($index=0; $index<$max; $index++){
									if ($r["woic_container"] == $new_list[$index]){
										if ($this->module_debug){
											$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"KEEP [".$new_list[$index]."]"));
										}
										// if we are to keep this entry then reset to minus one as no web container can have a value of minus 1
										$new_list[$index]=-1;
									}
								}
	        		        }
	    		            $this->call_command("DB_FREE",Array($result));
							$this->addtoendoflist($id, $new_list);
						}
					}
				}
			}
		}
		if ($cmd == "REMOVE"){
			$sql = "select * from web_objects where wo_client = $this->client_identifier and wo_type = 1 and wo_owner_module = '$wo_owner_mod' and wo_owner_id = '$wo_owner_id'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$wo_identifier =-1;
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$wo_identifier = $r["wo_identifier"];
            }
			if ($wo_identifier!=-1){
				// if there is no record then nothing to delete
	            $this->call_command("DB_FREE",Array($result));
				$sql = "delete from web_objects where wo_client = $this->client_identifier and wo_type = 1 and wo_owner_module = '$wo_owner_mod' and wo_owner_id = '$wo_owner_id' and wo_identifier=$wo_identifier";
				$this->call_command("DB_QUERY",array($sql));
				$sql = "delete from web_objects_in_container where woic_client = $this->client_identifier and woic_object = $wo_identifier";
				$this->call_command("DB_QUERY",array($sql));
			}
		}
	}

	function addtoendoflist($id, $new_list){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"addtoendoflist",__LINE__,"id:$id, new_list:$new_list"));
		}
		$sql = "select woic_container, max(woic_rank) as max_rank from web_objects_in_container 
					where woic_container in (".join(",",$new_list).") and woic_client = $this->client_identifier 
				group by woic_container
				order by woic_container";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$max = count($new_list);
		$rank_list = Array();
		for ($index=0; $index<$max; $index++){
			$rank_list[$index]=1;
		}
		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			for ($index=0; $index<$max; $index++){
				if ($r["woic_container"]==$new_list[$index]){
					$rank_list[$index]=$r["max_rank"] + 1;
				}
			}
		}
		$this->call_command("DB_FREE",Array($result));
		for ($index=0; $index<$max; $index++){
			if ($new_list[$index]!=-1){
				$sql = "insert into web_objects_in_container (woic_client, woic_container, woic_object, woic_rank) values ($this->client_identifier, ".$new_list[$index].", ".$id." ,".$rank_list[$index].")";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
				}
				$result  = $this->call_command("DB_QUERY",Array($sql));
			}
		}
		
	}
	
	function list_layouts_and_containers($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_layouts_and_containers",__LINE__,print_r($parameters,true)));
		}
		$module		= $this->check_parameters($parameters,"module");
		$restrict	= $this->check_parameters($parameters,"restrict",0); // restrict to only the module type
		$identifier = $this->check_parameters($parameters,"identifier");
		$returnType	= $this->check_parameters($parameters,"returnType",0);
//		print_r($parameters);
		if ($module=="WEBOBJECTS_"){
			$sql = "select distinct woic_container from web_objects
					inner join web_objects_in_container on woic_object=wo_identifier
				where wo_client=$this->client_identifier and (wo_owner_module='$module' and wo_identifier = $identifier)";
		} else {
			$sql = "select distinct woic_container from web_objects
					inner join web_objects_in_container on woic_object=wo_identifier
				where wo_client=$this->client_identifier and (wo_owner_module = '$module' and wo_owner_id = $identifier)";
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_webobject_size",__LINE__,"[$sql]"));
		}
		$list = Array();
		$szlist =Array();
        $result  = $this->call_command("DB_QUERY",Array($sql));
		$c=0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$c++;
			$list[count($list)] = $r["woic_container"];
        }
		$this->call_command("DB_FREE",Array($result));
		if($c>0){
			$container_list = join(", ",$list);
		} else {
			$container_list = "";
	    }
		/*
		$sql = "select wc_type, wol_label, wc_label, wc_identifier from web_layouts
				inner join web_container_to_layout on wctl_layout = wol_identifier and wctl_client = wol_client
				inner join web_containers on wc_identifier = wctl_container and wc_client = wctl_client
				where wol_client = $this->client_identifier and wc_type in('__OPEN__','$module')
					order by wc_type asc, wol_label, wc_label, wc_identifier";
		*/
		$mod_parts = split("_",$module);
		if($mod_parts[1]!=""){
			$module=$mod_parts[0]."_";
		}
		if ($module == "WEBOBJECTS_"){
			$module = "__UD__";
		}
		if($container_list==""){
			if($restrict==0){
				$sql = "select wc_type, wc_label, wc_identifier from web_containers 
						where wc_client = $this->client_identifier and wc_type in('__OPEN__','$module')
							order by wc_type asc, wc_label, wc_identifier";
			} else {
				$sql = "select wc_type, wc_label, wc_identifier from web_containers 
						where wc_client = $this->client_identifier and wc_type = '$module'
							order by wc_type asc, wc_label, wc_identifier";
			}
		}else{
			if($restrict==0){
				$sql = "select wc_type, wc_label, wc_identifier from web_containers 
						where wc_client = $this->client_identifier and (wc_type in('__OPEN__','$module') or wc_identifier in ($container_list))
							order by wc_type asc, wc_label, wc_identifier";
			} else {
				$sql = "select wc_type, wc_label, wc_identifier from web_containers 
						where wc_client = $this->client_identifier and (wc_type = '$module' or wc_identifier in ($container_list))
							order by wc_type asc, wc_label, wc_identifier";
			}
		}
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_webobject_size",__LINE__,"[$sql]"));
		}
//		print "<p>$sql</p>";
		$wc_result  = $this->call_command("DB_QUERY",Array($sql));
		$sz = "";
		$previous ="";
		$c=0;
		$closed=0;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"count",__LINE__,"[".$this->call_command("DB_NUM_ROWS",Array($wc_result))."]"));
		}
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($wc_result))){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"label ",__LINE__,"[".$r["wc_label"]."]"));
			}
			if ($c==0){
				if ($r["wc_type"]!="__OPEN__"){
//					$closed= 1;
				}
			}
			$c++;
			if ($r["wc_type"]=="__OPEN__" && $closed==1){
			
			} else {
/*
		    	if ($previous!=$r["wol_label"]){
					if ($sz!=""){
						$sz.="</options>";
					}
					$sz.="<options label='WebLayout - ".$r["wol_label"]."'>";
					$previous = $r["wol_label"];
				}
*/
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"check",__LINE__,"[".$r["wc_identifier"]." against ".print_r($list,true)."]"));
				}
				$szlist[count($szlist)] = Array("id"=>$r["wc_identifier"], "label"=>$r["wc_label"]);
				$sz.="<option value='".$r["wc_identifier"]."'";
				if (in_array($r["wc_identifier"], $list)){
					$sz.=" selected='true'";
				}
				$sz.="><![CDATA[".$r["wc_label"]."]]></option>";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_webobject_size",__LINE__,"<li>".$r["wc_identifier"]." - ".$r["wc_label"]."</li>"));
				}
			}
        }
//		if ($sz!=""){
//			$sz.="</options>";
//		}
        $this->call_command("DB_FREE",Array($wc_result));
		if($returnType==0){
			return $sz."~----~".join(",",$list);
		} else {
			return Array($szlist,$list);
		}
	}

	function stripquotes($str){
		return str_replace(Array("'",'"',"&#39;","&amp;amp;#39;"),Array("","","",""),$str);
	}
	
	function extract_objects($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"extract_objects",__LINE__,print_r($parameters,true)));
		}
		$filter= $this->check_parameters($parameters,"filter");
		if ($filter=="__SYSTEM__" ){
			$sql="select * from web_objects where wo_client=$this->client_identifier and wo_owner_module=''";
		}else if ($filter=="__OPEN__" ){
			$sql="select * from web_objects where wo_client=$this->client_identifier and wo_owner_module!=''";
		} else {
			$sql="select * from web_objects where wo_client=$this->client_identifier and wo_owner_module like '$filter%'";
		}
		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_webobject_size",__LINE__,"[$sql]"));
		}

		$result  = $this->call_command("DB_QUERY",Array($sql));
		$out ="";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$out .= "<web_object type='".$r["wo_type"]."' identifier='".$r["wo_identifier"]."'><label><![CDATA[".$this->stripquotes($r["wo_label"])."]]></label></web_object>";
        }
        $this->call_command("DB_FREE",Array($result));
//		$this->exitprogram();
		$out ="<module name=\"".$this->module_name."\" display=\"list\">".$out."</module>";
		return $out;
	}
	
	function inherit($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"inherit",__LINE__,print_r($parameters,true)));
		}
		$menu_id		= $this->check_parameters($parameters,"menu_identifier",-1);
		$menu_parent 	= $this->check_parameters($parameters,"menu_parent",-1);
		$this->call_command("LAYOUT_MENU_TO_OBJECT_INHERIT",Array(
			"menu_location"	=> $menu_id,
			"menu_parent"	=> $menu_parent,
			"module"		=> $this->webContainer,
			"condition"		=> "wol_set_inheritance =1 and ",
			"client_field"	=> "wol_client",
			"table"			=> "web_layouts",
			"primary"		=> "wol_identifier"
			)
		);
	}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
-													C O N T A I N E R   M E T H O D S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
*/
	function manage_container_list($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"manage_container_list",__LINE__,print_r($parameters,true)));
		}
		$sql = "Select * from web_containers where wc_client = $this->client_identifier order by wc_label";
//		print "<p>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables = Array();
		$variables["FILTER"]			= "";// $this->filter($parameters);
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["as"]				= "table";
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		$variables["HEADER"]			= MANAGE_CONTAINERS;
		$variables["PAGE_BUTTONS"] = Array(
			Array("ADD",$this->module_command."CONTAINER_ADD",ADD_NEW)
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
			$variables["PAGE_COMMAND"] 		= $this->module_command."CONTAINER_LIST";
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
				"identifier"		=> $r["wc_identifier"],
				"ENTRY_BUTTONS" 	=> Array(),
				"attributes"		=> Array(
						Array(LOCALE_CONTAINER_LABEL,$this->convert_amps($r["wc_label"]),"TITLE","NO")
					)
				);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT"   , $this->module_command."CONTAINER_EDIT",			EDIT_EXISTING);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE" , $this->module_command."CONTAINER_REMOVE_CONFIRM",	REMOVE_EXISTING);
				
			
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}

	function container_form($parameters){	
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"container_form",__LINE__,print_r($parameters,true)));
		}
			
		$command					= $this->check_parameters($parameters,"command");
		$display_tab				= $this->check_parameters($parameters,"display_tab");
		$identifier					= $this->check_parameters($parameters,"identifier",-1);		
		$wc_label					= "";
		$wc_type					= "";
		$wc_width					= "100%";
		$wc_columns					= "1";
		$list						= Array();
		$form_label 				= MANAGE_CONTAINERS." - ". ADD_NEW;
		//****************************************************************************
		//**** Fetching Container on identifier base.
		if ($identifier!=-1){
			$form_label 				= MANAGE_CONTAINERS." - ". EDIT_EXISTING;
			$sql = "select * from web_containers where wc_client = $this->client_identifier and wc_identifier= $identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$wc_label	= $r["wc_label"];
				$wc_type	= $r["wc_type"];
				$wc_width	= $r["wc_width"];
				$wc_columns	= $r["wc_layout_columns"];
            }
            $this->call_command("DB_FREE",Array($result));
		//*****************************************************************************
		//**** Fetching Web_Object_In_Container , whether on idenetifier based or not.
			$sql = "select * from web_objects_in_container
				inner join web_objects on wo_identifier = woic_object and woic_client=wo_client
			where woic_client=$this->client_identifier and woic_container= $identifier 
			order by woic_rank";			
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$list[count($list)] = Array($r["wo_identifier"], $r["wo_label"], $r["woic_rank"], $r["woic_identifier"]);
            }
            $this->call_command("DB_FREE",Array($result));
		}		
		//*****************************************************************************
		//** Fetching Container Type Not From Database.
		$this->type_list = Array(
			Array("System Object"	,"__SYSTEM__"),
			Array("General Object"	,"__OPEN__"),
			Array("User Defined"	,"__UD__")
		);
		
		$data = $this->call_command("ENGINE_RETRIEVE",Array("GET_WEB_CONTAINER"));		
		$m = count($data);
		for ($i=0;$i<$m;$i++){
			if ($data[$i][1]!=""){
				$this->type_list[count($this->type_list)] = Array($this->get_constant($data[$i][0]."CONTAINER"),$data[$i][1]);
			}
		}
		//*****************************************************************************
	//	$directories = $this->call_command("LAYOUT_DISPLAY_UPLOAD_DIRECTORY",array(-1,$file_directory));
		$out  = "<module name=\"web_objects\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[".$this->stripquotes($form_label)."]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","WEBOBJECTS_CONTAINER_LIST",LOCALE_CANCEL));
		$out .= "</page_options>";
		$out .= "<form name=\"webobjects_form\" method=\"post\" label=\"$form_label\">";
		$out .="	<showframe>1</showframe>";
		$out .= "<input type=\"hidden\" name=\"identifier\" value=\"".$identifier."\"/>";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"WEBOBJECTS_CONTAINER_SAVE\"/>";
		$out .= "<page_sections>";
		$out .= "<section label='".LOCALE_MAIN."' name='setup'>";
		$out .= "	<input type=\"text\" label=\"".WHAT_LABEL."\" name=\"wc_label\" required=\"YES\"><![CDATA[$wc_label]]></input>";
//		if ($identifier==-1){
			$out .= "	<select name='wc_type' label=\"".LOCALE_CONTAINER_TYPE."\" onchange='javascript:wcupdatefilter()'> ";
			$max = count($this->type_list);
			for($index=0;$index < $max;$index++){
				$out .= "		<option value='".$this->type_list[$index][1]."'";
				if($wc_type == $this->type_list[$index][1]){
					$out .=" selected='true'";
				}
				$out .= ">".$this->type_list[$index][0]."</option>";
			}
			$out .= "	</select>";
/*		} else {
					$wc_type_label = $this->type_list[$index][0];
				}
			}
			$out .= "<text><![CDATA[<label>".LOCALE_CONTAINER_TYPE."</label><ul>" . $wc_type_label . "</ul>]]></text>";
			$out .= "<input type='hidden' name='wc_type' value='$wc_type'/>";
		}
*/
		$out .= "	<input type=\"text\" label=\"".LOCALE_WIDTH."\" size=\"5\" name=\"wc_width\" required=\"YES\"><![CDATA[$wc_width]]></input>";
		$out .= "	<select name='wc_columns' label=\"".LOCALE_CONTAINER_COLUMNS."\">";
		for($index=1;$index<6;$index++){
			$out .= "		<option value='".$index."'";
			if ($wc_columns==$index){
				$out .= " selected='true'";
			}
			$out .= ">".$index." Column</option>";
		}
			$out .= "	</select>";
		$out .= "</section>";
		$out .= "<section label='".LOCALE_CONTAINER_ITEMS."' name='container_items'>";
		$max = count($list);
		$out .= "<wo_list type='$wc_type'>";
		for ($index=0;$index<$max;$index++){
			$properties = $this->get_webobject_properties(Array("identifier" => $list[$index][3]));
			$out .= "<item identifier='".$list[$index][0]."' rank='".$list[$index][2]."'><value><![CDATA[".$list[$index][1]."]]></value><properties>$properties</properties></item>";
		}
		$max = count($this->type_list);
		for ($index=0;$index<$max;$index++){
			$out .= "
					<type>
						<label><![CDATA[".$this->type_list[$index][0]."]]></label>
						<value><![CDATA[".$this->type_list[$index][1]."]]></value>
					</type>
				";
		}
		$out .= "</wo_list>";
		$out .= "</section>";
		$out .= "</page_sections>";
		$out .= "<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	
	function manage_container_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"manage_container_save",__LINE__,print_r($parameters,true)));
		}
		$identifier					= $this->check_parameters($parameters,"identifier",-1);
		$webobject_list				= $this->check_parameters($parameters,"webobject_list");
		$wc_type 					= $this->check_parameters($parameters,"wc_type");
		$wc_label					= $this->check_parameters($parameters,"wc_label");
		$wc_width					= $this->check_parameters($parameters,"wc_width");
		$wc_columns					= $this->check_parameters($parameters,"wc_columns");
		$webobject_list				= $this->check_parameters($parameters,"webobject_list");
		$webobject_list_properties	= $this->check_parameters($parameters,"webobject_list_properties");
		$number_of					= $this->check_parameters($parameters,"number_of");
		$id							= $this->check_parameters($parameters,"id",Array());
		$rank						= $this->check_parameters($parameters,"rank",Array());

		$webobject_list_properties	= split('~OO~',$webobject_list_properties);
		$max 						= count($webobject_list_properties);
		for($index = 0; $index < $max; $index++){
			$webobject_list_properties[$index] = split(',',$webobject_list_properties[$index]);
			$m = count($webobject_list_properties[$index]);
			for($i = 0; $i < $m ; $i++){
				$webobject_list_properties[$index][$i] = split('~~',$webobject_list_properties[$index][$i]);
			}
		}
//		print_r($parameters);
//		$this->exitprogram();
		/*
		
Alignment[::]left,Width[::]100%[::::]Alignment[::]left,Width[::]100%
		    [identifier] => 42
		    [wc_type] => __SYSTEM__
		    [wc_label] => Mirror
		    [wc_width] => 100%
		    [wc_columns] => 1
		    [webobject_list] => 6,72,1,1,
		    [number_of] => 4
		    [id] => Array(
	            [0] => 6
    	        [1] => 72
        	    [2] => 1
            	[3] => 1
	        )
		    [rank] => Array(
	            [0] => 1
    	        [1] => 2
        	    [2] => 3
            	[3] => 4
	        )
		    [fake_uri] => 
		    [category] => 
		    [my_session_identifier] => 
		    [show_anyway] => 0
		*/
		$now = $this->libertasGetDate();
		//********************************************************************************
		//*** Adding a new container. [arafat]
		if ($identifier==-1){
			$sql = "insert into web_containers (wc_created, wc_label, wc_type, wc_width, wc_layout_columns, wc_client) values ('$now', '$wc_label', '$wc_type', '$wc_width', '$wc_columns', $this->client_identifier)";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "select * from web_containers where wc_label= '$wc_label' and  wc_type='$wc_type' and wc_width='$wc_width' and wc_layout_columns='$wc_columns' and wc_client= $this->client_identifier and wc_created='$now'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$identifier = $r["wc_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
		//********************************************************************************
		//** Updating the existing container. [arafat]
		} else {
			$sql = "update web_containers set wc_label= '$wc_label', wc_type='$wc_type', wc_width='$wc_width', wc_layout_columns='$wc_columns' where wc_client= $this->client_identifier and wc_identifier=$identifier";
			$this->call_command("DB_QUERY",Array($sql));
		}
		//********************************************************************************
		if($identifier==-1){
			// problem should never get here
		} else {
			$sql = "select * from web_objects_in_container where woic_container = $identifier and woic_client = $this->client_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$myobjectlist="";
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				if ($myobjectlist!=""){
					$myobjectlist.=",";
				}
            	$myobjectlist .= $r["woic_identifier"];
            }			
            $this->call_command("DB_FREE",Array($result));
			if ($myobjectlist!=""){
				$sql = "delete from web_properties where wop_client = $this->client_identifier and wop_container_object in ($myobjectlist)";
				$this->call_command("DB_QUERY",Array($sql));
			}
			$sql ="delete from web_objects_in_container where woic_container = $identifier and woic_client = $this->client_identifier";
			$this->call_command("DB_QUERY",Array($sql));
			$max = count($id);
			for ($index=0;$index<$max;$index++){
				$sql = "insert into web_objects_in_container (woic_object, woic_container, woic_rank, woic_client) values (".$id[$index].",$identifier,".$rank[$index].",$this->client_identifier)";
				$this->call_command("DB_QUERY",Array($sql));
			}
			$sql = "select * from web_objects_in_container where woic_container = $identifier and woic_client = $this->client_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$myobjectlist="";
			$m = count($id);
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$woic_id = $r["woic_identifier"];
				for ($i=0;$i<$m;$i++){
					if ($id[$i] == $r["woic_object"]){
						$list   = $this->check_parameters($webobject_list_properties, $i, Array());
						$zm = count($list);
						for ($prop=0;$prop<$zm ;$prop++){
							$wop_name	= $list[$prop][0];
							$wop_value	= $list[$prop][1];
							$sql = "insert into web_properties (wop_client, wop_container_object, wop_name, wop_value) 
										values 
									($this->client_identifier, $woic_id, '$wop_name', '$wop_value')";
							$this->call_command("DB_QUERY",Array($sql));
						}
					}
				}
            }
			
		}
	}
	
	function remove_container($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"remove_container",__LINE__,print_r($parameters,true)));
		}
		$identifier  = $this->check_parameters($parameters,"identifier",-1);
		if ($identifier!=-1){
			$sql = "delete from web_containers where wc_client = $this->client_identifier and wc_identifier = $identifier";
			$this->call_command("DB_QUERY",Array($sql));
			$sql ="delete from web_objects_in_container where woic_container = $identifier and woic_client = $this->client_identifier";
			$this->call_command("DB_QUERY",Array($sql));
		}
	}
	
	function tidy_menu($theme, $id){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"tidy_menu",__LINE__,"theme : $theme, id : $id"));
		}
		$sql = "SELECT wol_identifier , mto_identifier, mto_menu
					FROM `web_layouts`
					  inner join menu_to_object on mto_object = wol_identifier and wol_client = mto_client and mto_module='WEBOBJECTS_' 
					where 
					  wol_theme=$theme and wol_client=$this->client_identifier
					order by  wol_identifier , mto_menu";
		$remove = "";
		$mylist = Array();
		$keep	= Array();
		$result	= $this->call_command("DB_QUERY",Array($sql));
		$pos=0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	if ($r["wol_identifier"]==$id){
				$keep[count($keep)] = $r["mto_menu"];
			} else {
				$mylist[$pos] = Array($r["wol_identifier"], $r["mto_identifier"], $r["mto_menu"]);
				$pos++;
        	}
		}
		$this->call_command("DB_FREE",Array($result));
		
		// have two lists now remove any menu in the $list array that exists in the $keep array
		for ($i=0; $i<count($mylist); $i++){
			if (in_array($mylist[$i][2],$keep)){
				if ($remove!=""){
					$remove.=",";
				}	
				$remove .= $mylist[$i][1];
			}
		}
		$sql ="delete from menu_to_object where 
		mto_client = $this->client_identifier and mto_module='WEBOBJECTS_' and mto_identifier in (".$remove.")";
		$this->call_command("DB_QUERY",Array($sql));
		 
	}
	
	function get_webobject_themes($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_webobject_themes",__LINE__,print_r($parameters,true)));
		}
		$form_label = LOCALE_WEBOBJECTS_CHOOSE_THEME;
		$sql = "select distinct theme_data.theme_identifier, theme_data.theme_label from web_layouts inner join theme_data on wol_theme = theme_identifier and wol_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,$sql));
		}
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$list="";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$selected="";
        	$list.="<option value='".$r["theme_identifier"]."' $selected>".$r["theme_label"]."</option>";
        }
        $this->call_command("DB_FREE",Array($result));
		$out  = "<module name=\"web_objects\" display=\"form\">";
		$out .= "<form name=\"webobjects_form\" method=\"post\" label=\"$form_label\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"WEBOBJECTS_SET_THEME\"/>";
		$out .= "<select name=\"choosen_theme\" label=\"".LOCALE_CHOOSE_THEME."\">$list</select>";
		$out .= "<input iconify=\"SAVE\" type=\"submit\" value=\"".LOCALE_OK."\"/>";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	function set_webobject_themes($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_webobject_themes",__LINE__,print_r($parameters,true)));
		}
		$_SESSION["CHOOSEN_THEME"] = $this->check_parameters($parameters,"choosen_theme",0);
		$_SESSION["CHOOSEN_CSS"] = "default";
		$_SESSION["CHOOSEN_SIZE"] = "";
//		print $_SESSION["CHOOSEN_THEME"];
		$this->call_command("ENGINE_REFRESH_BUFFER");
	}	
	
	function get_webobject_size($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_webobject_size",__LINE__,print_r($parameters,true)));
		}

		$form_label = LOCALE_WEBOBJECTS_CHOOSE_SIZE;

		$list="";
		$size = $this->check_parameters($_SESSION,"CHOOSEN_SIZE");
       	$list.="<option value='smallest'";
		if ($size=='smallest'){
		$list.=" selected='true'";
		}
		$list.=">Smallest Font</option>";
       	$list.="<option value='smaller'";
		if ($size=='smaller'){
		$list.=" selected='true'";
		}
		$list.=">Smaller Font</option>";
       	$list.="<option value=''";
		if ($size==''){
		$list.=" selected='true'";
		}
		$list.=">Normal Font</option>";
       	$list.="<option value='larger'";
		if ($size=='larger'){
		$list.=" selected='true'";
		}
		$list.=">Larger Font</option>";
       	$list.="<option value='largest'";
		if ($size=='largest'){
		$list.=" selected='true'";
		}
		$list.=">Largest Font</option>";
//print "[$size]";
		$out  = "<module name=\"web_objects\" display=\"form\">";
		$out .= "	<form name=\"webobjects_form_size\" method=\"post\" label=\"$form_label\">";
		$out .= "		<input type=\"hidden\" name=\"command\" value=\"WEBOBJECTS_SET_SIZE\"/>";
		$out .= "		<radio type='vertical' name=\"choosen_font_size\">$list</radio>";
		$out .= "		<input iconify=\"SAVE\" type=\"submit\" value=\"".LOCALE_OK."\"/>";
		$out .= "	</form>";
		$out .= "</module>";
		return $out;
	}
	
	function set_webobject_size($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_webobject_size",__LINE__,print_r($parameters,true)));
		}
		$fsize = $this->check_parameters($parameters,"choosen_font_size");
		$_SESSION["CHOOSEN_SIZE"] = $fsize;
		$this->call_command("ENGINE_REFRESH_BUFFER");
	}

	function get_webobject_css($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_webobject_css",__LINE__,print_r($parameters,true)));
		}
		$form_label = LOCALE_WEBOBJECTS_CHOOSE_CSS;
		$xsl_files = $this->parent->site_directories["XSL_THEMES_DIR"];
//		print_r($this->parent->site_directories);
		$dir = dirname($this->parent->theme_stylesheet);
		$fname= $xsl_files.$dir."/available_styles.data";
//		print $fname;
		$list="";
		if (file_exists($fname)){
			$fdata = file($fname);
			$m = count($fdata);
			for ($i=0;$i<$m;$i++){
				$info = split(",",$fdata[$i]);
				if ($info[0] == $this->check_parameters($_SESSION,"CHOOSEN_CSS","default")){
					$list .= "<option value='" . $info[0] . "' selected='true'>" . $info[1] . "</option>";
				} else {
					$list .= "<option value='" . $info[0] . "'>" . $info[1] . "</option>";
				}
			}
		} else {
		}
		$out  = "";
		if (strlen($fdata)>0){
			$out  = "<module name=\"web_objects\" display=\"form\">";
			$out .= "	<form name=\"webobjects_form_css\" method=\"post\" label=\"$form_label\">";
			$out .= "		<input type=\"hidden\" name=\"command\" value=\"WEBOBJECTS_SET_CSS\"/>";
			$out .= "		<radio type='vertical' name=\"choosen_css\" label='asdf'>$list</radio>";
			$out .= "		<input iconify=\"SAVE\" type=\"submit\" value=\"".LOCALE_OK."\"/>";
			$out .= "	</form>";
			$out .= "</module>";
		}
		return $out;
	}
	function set_webobject_css($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"set_webobject_css",__LINE__,print_r($parameters,true)));
		}
		$css = $this->check_parameters($parameters,"choosen_css");
		$_SESSION["CHOOSEN_CSS"] = $css;
		$this->call_command("ENGINE_REFRESH_BUFFER");
	}
	
	function create_client_details($parameters){
		$client_identifier = $this->check_parameters($parameters,"client_identifier",$this->client_identifier);
/*
		$length=count($this->parent->modules);
//		print "length $length";
		for($index=0;$index<$length;$index++){
			if ($this->parent->modules[$index]["table"]==1){
//				print $this->parent->modules[$index]["tag"]."MENU_DISPLAY_OPTIONS<br>";
				$this->call_command($this->parent->modules[$index]["tag"]."MENU_DISPLAY_OPTIONS",array(array(),1));
			}
		}*/

		$module_display_options_array = $this->call_command("ENGINE_RETRIEVE",array("MENU_DISPLAY_OPTIONS",array(array(),1)));
		//print_r( $module_display_options_array);
		$module_display_options="";
		$length_of_array=count($module_display_options_array);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"length of display options",__LINE__,"$length_of_array"));
		}
		for($index=0;$index<$length_of_array;$index++){
			if (count($module_display_options_array[$index][1])>0){
				if ($module_display_options_array[$index][1]!=""){
					for($i=0;$i<count($module_display_options_array[$index][1]);$i++){
						if ($module_display_options_array[$index][1][$i][0]!=""){
							if ($module_display_options_array[$index][1][$i][0]=='PRESENTATION_DISPLAY'){
								$module_display_options_array[$index][1][$i][0]='PRESENTATION_DISPLAY_PAGE';
							}
							$sql ="insert into web_objects (wo_client, wo_type, wo_label, wo_command, wo_all_locations, wo_show_label) values 
							($client_identifier, 1, '".$this->get_constant($module_display_options_array[$index][1][$i][1])."', '".$module_display_options_array[$index][1][$i][0]."',0,0);";
							$this->call_command("DB_QUERY",array($sql));
						}
					}
				}
			}
		}
//$module_display_options
//			Array('Display the SiteMap','WEBOBJECTS_SHOW_SITEMAP_EXTRACT',1,0),
		$list = Array(
			Array('Display the Page Option buttons','WEBOBJECTS_SHOW_PRINTER_FRIENDLY',1,0),
			Array('Show Date (Todays)','WEBOBJECTS_SHOW_DATE',1,0),
			Array('Show Date (Site Last Updated)','WEBOBJECTS_SHOW_SITE_UPDATED_DATE',1,0),
//			Array('Show Date (page Updated)','WEBOBJECTS_SHOW_PAGE_UPDATED_DATE',1,0),
			Array('Show breadcrumb trail','WEBOBJECTS_SHOW_BREADCRUMB',1,0),
			Array('Show menu (primary)','WEBOBJECTS_SHOW_MAIN_MENU',1,0),
			Array('Show menu (secondary)','WEBOBJECTS_SHOW_SUB_MENU',1,0),
			Array('Show menu (footer format)','WEBOBJECTS_SHOW_TEXT_BASED_LEVEL_ONE_MENU',0,0),
			Array('Show Search in (column)','WEBOBJECTS_SHOW_SEARCH_BOX_COLUMN',1,0),
			Array('Show Search in (row)','WEBOBJECTS_SHOW_SEARCH_BOX_ROW',1,0),
			Array('Show Top of page option','WEBOBJECTS_SHOW_TOP_OF_PAGE',1,0)
		);
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$list[count($list)] = Array('Show login form (default)','WEBOBJECTS_SHOW_LOGIN',1,0);
		}
		$m = count($list);
		for($i = 0 ; $i < $m ; $i ++ ){
			$sql = "insert into web_objects (wo_client,wo_type,wo_label,wo_command,wo_all_locations,wo_show_label) values ($client_identifier,2,'".$list[$i][0]."','".$list[$i][1]."',".$list[$i][2].",".$list[$i][3].");";
			$this->call_command("DB_QUERY",array($sql));
		}
		$m = count($this->module_display_options);
		for($i = 0 ; $i < $m ; $i ++ ){
			$sql = "insert into web_objects (wo_client,wo_type,wo_label,wo_command,wo_all_locations,wo_show_label) values ($client_identifier, 1, '".$this->get_constant($this->module_display_options[$i][1])."', '".$this->module_display_options[$i][0]."', 0, 0);";
			$this->call_command("DB_QUERY",array($sql));
		}
		return "";
	}

	/**
	* function manage_module_webobjects_wo(...)
	- tihs function is used to create web objects and assign them to an object does not 
	- place them in containers or manage anything to do with containers
	- required for Information Directory
	*/
	function manage_module_webobjects_wo($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"manage_module_webobjects",__LINE__,print_r($parameters,true)));
		}
		/*
			// fn parameters
			"webobjects"	=> $this->special_webobjects,
			"owner_id" 		=> $identifier,
			"cmd"			=> "UPDATE"
					
			// webobject array structure
			"owner_module" 	=> "",//$this->webContainer."_POPULAR",
			"label" 		=> " most popular entries",
			"wo_command"	=> "INFORMATION_POPULAR",
			"file"			=> "-popular.php",
			"available"		=> 0

		*/
		$webobjects		= $this->check_parameters($parameters,"webobjects",Array());
		$wo_owner_id	= $this->check_parameters($parameters,"owner_id");
		$starter		= $this->check_parameters($parameters,"starter");
		$label			= $this->check_parameters($parameters,"label");
		$cmd			= $this->check_parameters($parameters,"cmd","ADD");
		$tmp_cmd 		= $cmd; // used to fool the update
		$woexists			= Array();
		if($cmd=="UPDATE"){
			$sql ="select * from web_objects where wo_owner_id = $wo_owner_id and wo_client=$this->client_identifier and wo_owner_module like '$starter%' ";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$woexists[count($woexists)] = $r["wo_command"];
            }
            $this->call_command("DB_FREE",Array($result));
		}
		foreach($webobjects as $key => $value){
			
			if($key!="FEATURE"){
				if($value["available"]==1 && $value["owner_module"]!=""){
					$wo_label		= $value["label"];
					$wo_command		= $value["wo_command"];
					$wo_owner_mod 	= $value["owner_module"];
					$type			= $this->check_parameters($value,"type",1);
					if ($cmd == "UPDATE"){
						if(in_array($wo_command, $woexists)){
							if($label == $wo_label){
								$wo_label = "$wo_label";
							}else{
								$wo_label = "$label - $wo_label";
							}
							$sql = "Update web_objects set wo_label = '$wo_label' where wo_client = $this->client_identifier and wo_type = 1 and wo_owner_module = '$wo_owner_mod' and wo_owner_id = '$wo_owner_id'";
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[".$sql."]"));
							}
							$this->call_command("DB_QUERY",array($sql));
						} else {
							$tmp_cmd = $cmd;
							$cmd ="ADD";
						}
					}
					if ($cmd == "ADD"){
						$now = $this->libertasGetDate("Y/m/d H:i:s");
						if($label == $wo_label){
							$wo_label = "$wo_label";
						}else{
							$wo_label = "$label - $wo_label";
						}
						$sql = "insert into web_objects 
									(wo_client, wo_type, wo_label, wo_command, wo_all_locations, wo_show_label, wo_owner_module, wo_owner_id, wo_created) 
								values 
									($this->client_identifier, $type, '$wo_label', '$wo_command', 0, 0, '$wo_owner_mod', '$wo_owner_id', '$now');";
						$this->call_command("DB_QUERY",array($sql));
						$cmd = $tmp_cmd;
					}
					if ($cmd == "REMOVE"){
						$sql = "select * from web_objects where wo_client = $this->client_identifier and wo_type = $type and wo_owner_module = '$wo_owner_mod' and wo_owner_id = '$wo_owner_id'";
						$result  = $this->call_command("DB_QUERY",Array($sql));
						$wo_identifier =-1;

	        		    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	    	    	    	$wo_identifier = $r["wo_identifier"];
							// if there is no record then nothing to delete
							$sql = "delete from web_objects where wo_client = $this->client_identifier and wo_identifier=$wo_identifier";
							$this->call_command("DB_QUERY",array($sql));
							$sql = "delete from web_objects_in_container where woic_client = $this->client_identifier and woic_object = $wo_identifier";
							$this->call_command("DB_QUERY",array($sql));	    	    	    	
		            	}
		            	
					}
				}
			}
		}
	}
	/*************************************************************************************************************************
    * module_retrieve_object
    *************************************************************************************************************************/
	function module_retrieve_object($parameters){
		$identifier		= $this->check_parameters($parameters, "identifier");
		$owner_module	= $this->check_parameters($parameters, "owner_module");
		$wo_command		= $this->check_parameters($parameters, "wo_command");
		$default_label	= $this->check_parameters($parameters, "default_label");
		$sql = "select * from web_objects 
					inner join web_objects_in_container on wo_identifier = woic_object and woic_client=wo_client
					inner join web_properties on wop_container_object  = woic_identifier and woic_client=wop_client
				where 
					wo_command='$wo_command' and 
					wo_owner_module='$owner_module' and 
					wo_client=$this->client_identifier and 
					wo_owner_id=$identifier
			";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$props = Array("label"=>$default_label, "show_label"=>0);
		$label ="";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$props["label"]			= $r["wo_label"];
			$props["show_label"]	= $r["wo_show_label"];
        }
        $this->call_command("DB_FREE",Array($result));
		return $props;
	}


	/*************************************************************************************************************************
	* list_layouts($parameters)
	*************************************************************************************************************************/
	function list_layouts($parameters){
		/*
		if($this->manage_database_list==0){
			return "";
		}
		*/
/*		$sql = "select information_list.info_shop_enabled, information_list.info_identifier, information_list.info_label, information_list.info_summary_layout, information_list.info_status, menu_data.*, count(ie_parent) as total  from information_list 
					inner join menu_data on menu_identifier = info_menu_location and menu_client = info_client 
					left outer join information_entry on ie_list=info_identifier and ie_client=info_client and ie_version_wip=1
				where information_list.info_owner='INFORMATIONADMIN_' and information_list.info_client=$this->client_identifier group by info_identifier order by info_identifier desc";
*/
		$sql = "Select * from web_layouts 
					left outer join theme_data on wol_theme = theme_identifier and (theme_secure = 0 or theme_secure = wol_client)
					where wol_client = $this->client_identifier";

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
				$this->list_layouts[$i]["wol_identifier"] = $r["wol_identifier"];
				$this->list_layouts[$i]["wol_label"] = $r["wol_label"];
				$i++;
			}
			return $this->list_layouts;
		}
	}

	function set_default_layout($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,""));
		}

		$list_layouts = $this->call_command("WEBOBJECTS_LIST_LAYOUTS");

		$meta_type		= "";
//		$sql = "select * from web_layouts where smd_client= $this->client_identifier";

		$sql = "Select wol_identifier from web_layouts 
					left outer join theme_data on wol_theme = theme_identifier and (theme_secure = 0 or theme_secure = wol_client)
					where wol_client = $this->client_identifier and wol_default = 1";

		$result = $this->parent->db_pointer->database_query($sql);
		if ($result){
			$r= $this->parent->db_pointer->database_fetch_array($result);
			$wol_identifier		= $r["wol_identifier"];
		}
//		$out	 = "<module name=\"$this->module_name\" display=\"form\"><page_options><button command=\"ENGINE_SPLASH\" alt=\"LOCALE_CANCEL\" iconify=\"CANCEL\" /></page_options>\n";

		$out	 = "<module name=\"$this->module_name\" display=\"form\">\n";
		
		$out	.= "<form name=\"set_default_layout_frm\" label=\"".LOCALE_SET_DEFAULT_LAYOUT."\" method=\"POST\" width=\"100%\">\n";
		$out	.= "<input name=\"command\" type=\"hidden\" value=\"".$this->module_command."SET_DEFAULT_LAYOUT_SAVE\"/>\n";

			$out .= "\t\t\t\t\t<select required= \"YES\" name=\"choose_layout\" label=\"".LOCALE_CHOOSE_LAYOUT_LABEL."\" >\n";
			$out  .= "<option value='-1'>Select one</option>";
	
	//		print_r($list_layouts);
	//		echo '<br><br>';
	//		echo $list_layouts[0]['info_identifier'];
	
			$i=0;
			while (list(, $value) = each($list_layouts)) {
				if ($list_layouts[$i][wol_identifier] != ""){
					$out  .= "<option value='".$list_layouts[$i][wol_identifier]."'";
	
					if ($list_layouts[$i]['wol_identifier'] == $wol_identifier)
						$out .= " selected='true'";
	
					$out  .= ">".$list_layouts[$i][wol_label]."</option>";
					$i++;
				}
			}
			$out .= "\t\t\t\t\t</select>\n";

		$out	.= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>\n";
		$out	.= "</form>\n";
		$out	.= "</module>";
		return $out;
	}
	
	function set_default_layout_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,""));
		}
		$choose_layout	= $this->check_parameters($parameters,"choose_layout","0");
		$sql = "
			update 
				web_layouts 
			set 
				wol_default ='0'
			where
				wol_client = $this->client_identifier and 
				wol_default=1";
		$result = $this->parent->db_pointer->database_query($sql);
		$sql = "
			update 
				web_layouts 
			set 
				wol_default =1
			where
				wol_client = $this->client_identifier and 
				wol_identifier=$choose_layout;";
		$result = $this->parent->db_pointer->database_query($sql);
/*
		$out	 = "<module name=\"$this->module_name\" display=\"form\"><page_options><button command=\"SYSPREFS_SYSTEM_METADATA\" alt=\"LOCALE_CANCEL\" iconify=\"CANCEL\" /></page_options>\n";
		$out	.= "<form name=\"sys_prefs\" label=\"".LOCALE_META_DEFAULT_FORM_CONFIRM."\" method=\"POST\">\n";
		$out	.= "<text><![CDATA[".LOCALE_META_SAVE_CONFIRM."]]></text>\n";
		$out	.= "</form>\n";
		$out	.= "</module>";
	return $out;
*/
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=WEBOBJECTS_SET_DEFAULT_LAYOUT"));
	}

}
?>
