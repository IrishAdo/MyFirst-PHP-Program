<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.themes.php
* @date 09 Oct 2002
*/
class themes extends module{
	/**
	*  Class Variables
	*/
	var $module_name				= "themes";
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_label				= "MANAGEMENT_THEMES";
	var $module_name_label			= "Theme Management Module";	// label describing the module 
	var $module_creation			= "20/02/2003";
	var $module_modify	 		= '$Date: 2005/02/28 16:21:25 $';
	var $module_version 			= '$Revision: 1.15 $';
	var $module_admin				= "1";
	var $module_debug				= false;
	var $module_command				= "THEME_"; 		// all commands specifically for this module will start with this token
	var $module_display_options		= Array();
	var $module_admin_options 		= array();
	var $module_admin_user_access 	= array();


	/**
	* Theme display options default settings
	*/
	var $data_values = array(
		Array('LOCALE_THEME_001_TYPE_HOME_PAGE',				'', 	'PRESENTATION_DISPLAY', 	"default_home.xsl", 						"default_main.xsl"						,"all"),
		Array('LOCALE_THEME_002_TYPE_DEFAULT',					'', 	'PRESENTATION_DISPLAY', 	"default_main.xsl", 						"default_main.xsl"						,"all"),
		Array('LOCALE_THEME_003_TYPE_LIST',						'', 	'PRESENTATION_DISPLAY',		"default_list.xsl", 						"default_main.xsl"						,"title"),
		Array('LOCALE_THEME_004_TYPE_TITLE_SUMMARY',			'', 	'PRESENTATION_DISPLAY', 	"default_title_and_summary.xsl", 			"default_main.xsl"						,"summary"),
		Array('LOCALE_THEME_005_TYPE_DISPLAY',					'', 	'PRESENTATION_DISPLAY',		"default_display_all.xsl",					"default_main.xsl"						,"content"),
		Array('LOCALE_THEME_006_TYPE_ARTICLES',					'MECM', 'PRESENTATION_DISPLAY', 	"default_articles.xsl",						"default_articles.xsl"					,"content"),
		Array('LOCALE_THEME_007_TYPE_FAQ',						'', 	'PRESENTATION_DISPLAY', 	"default_faq.xsl", 							"default_main.xsl"						,"content"),
		Array('LOCALE_THEME_009_TYPE_2_COLUMNS',				'MECM', 'PRESENTATION_DISPLAY', 	"default_2_column.xsl", 					"default_main.xsl"						,"title"),
		Array('LOCALE_THEME_009_TYPE_2_COLUMNS_CONTENT',		'MECM', 'PRESENTATION_DISPLAY', 	"default_2_column_content.xsl",				"default_main.xsl"						,"content"),
		Array('LOCALE_THEME_009_TYPE_2_COLUMNS_SUMMARY',		'MECM', 'PRESENTATION_DISPLAY', 	"default_2_column_summary.xsl",				"default_main.xsl"						,"summary"),
		Array('LOCALE_THEME_010_TYPE_3_COLUMNS',				'MECM', 'PRESENTATION_DISPLAY', 	"default_3_column.xsl", 					"default_main.xsl"						,"title"),
		Array('LOCALE_THEME_010_TYPE_3_COLUMNS_SUMMARY',		'MECM', 'PRESENTATION_DISPLAY', 	"default_3_column_summary.xsl",				"default_main.xsl"						,"summary"),
		Array('LOCALE_THEME_010_TYPE_3_COLUMNS_CONTENT',		'MECM', 'PRESENTATION_DISPLAY',		"default_3_column_content.xsl",				"default_main.xsl"						,"content"),
		Array('LOCALE_THEME_010_TYPE_3_COLUMN_GRAPHICAL',		'ECMS', 'PRESENTATION_DISPLAY',		"default_3_column_summary_graphical.xsl",	"default_main.xsl"						,"title"),
		Array('LOCALE_THEME_011_TYPE_A_2_Z_TITLE_CONTENT',		'ECMS', 'PRESENTATION_ATOZ',		"default_atoz_title_content.xsl",			"default_main.xsl"						,"content"),
		Array('LOCALE_THEME_011_TYPE_A_2_Z_TITLE',				'ECMS',	'PRESENTATION_ATOZ',		"default_atoz_title.xsl", 					"default_main.xsl"						,"title"),
		Array('LOCALE_THEME_011_TYPE_A_2_Z_TITLE_SUMMARY',		'ECMS',	'PRESENTATION_ATOZ',		"default_atoz_title_summary.xsl",			"default_main.xsl"						,"summary"),
		Array('LOCALE_THEME_011_TYPE_A_2_Z_ALL_TITLE',			'ECMS',	'PRESENTATION_ATOZ_ALL',	"default_atoz_all_title.xsl",				"default_atoz_all_title_content.xsl"	,"title"),
		Array('LOCALE_THEME_011_TYPE_A_2_Z_ALL_TITLE_CONTENT',	'ECMS',	'PRESENTATION_ATOZ_ALL',	"default_atoz_all_title_content.xsl",		"default_atoz_all_title_content.xsl"	,"content"),
		Array('LOCALE_THEME_011_TYPE_A_2_Z_ALL_TITLE_SUMMARY',	'ECMS',	'PRESENTATION_ATOZ_ALL',	"default_atoz_all_title_summary.xsl",		"default_atoz_all_title_content.xsl"	,"summary"), 
		Array('LOCALE_THEME_012_TYPE_SLIDESHOW',				'ECMS', 'PRESENTATION_SLIDESHOW',	"default_slideshow.xsl", 					"default_slideshow.xsl"					,"all"),
		Array('LOCALE_THEME_013_TYPE_PERSISTANT_1_COLUMN',		'MECM', 'PRESENTATION_PERSISTANT',	"default_persistant.xsl", 					"default_persistant.xsl"				,"all"), // difference is label
		Array('LOCALE_THEME_013_TYPE_PERSISTANT_2_COLUMN',		'MECM', 'PRESENTATION_PERSISTANT',	"default_persistant.xsl", 					"default_persistant.xsl"				,"all"), // difference is label
		Array('LOCALE_THEME_013_TYPE_PERSISTANT_3_COLUMN',		'MECM', 'PRESENTATION_PERSISTANT',	"default_persistant.xsl",	 				"default_persistant.xsl"				,"all") // difference is label
	);
	/**
	*  Class Methods
	*/
	
	function command($user_command,$parameter_list=array()){
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
			/**
			* basic commands
			*/
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
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
			if ($user_command==$this->module_command."SESSION_SET"){
				return $this->session_theme($parameter_list);
			}
//			if ($user_command==$this->module_command."SESSION_SET_SAVE"){
//				$this->session_theme_save($parameter_list);
//			}

			/**
			* specific functions for this module
			*/
			if ($user_command==$this->module_command."GET_STYLESHEET_OPTIONS"){
				return $this->select_stylesheet($parameter_list);
			}
			if ($user_command==$this->module_command."GET_STYLESHEET"){
				return $this->get_stylesheet($parameter_list);
			}
			if ($user_command==$this->module_command."GET_CSS"){
				return $this->get_css($parameter_list);
			}
			if ($user_command==$this->module_command."SELECT_THEME"){
				return $this->select_theme($parameter_list);
			}
			if ($user_command==$this->module_command."SAVE"){
				$ok = $this->save_theme($parameter_list);
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=THEME_SELECT_THEME&amp;success=$ok"));
			}
			if ($user_command==$this->module_command."RETRIEVE_LIST_OF"){
				return $this->retrieve_list_of_themes($parameter_list);
			}
			if ($user_command==$this->module_command."RETRIEVE_LIST_OF_FULL"){
				return $this->retrieve_list_of_themes_full_description($parameter_list);
			}
			if ($user_command==$this->module_command."GET_CURRENT"){
				return $this->get_current_theme();
			}
			if ($user_command==$this->module_command."GENERATE_DEFAULT_DB_RECORDS"){
				return $this->generate_themes();
			}
			if ($user_command==$this->module_command."ADD_NEW_ENTRY_SAVE"){
				return $this->add_entry_save($parameter_list);
			}
			if ($user_command==$this->module_command."ENTRY_IMPORT"){
				return $this->entry_import_save($parameter_list);
			}

			if ($user_command==$this->module_command."ADD_NEW_ENTRY"){
				return $this->add_entry($parameter_list);
			}
			if ($user_command==$this->module_command."UPDATE_DB"){
				return $this->update_db($parameter_list);
			}
			if ($user_command==$this->module_command."GET_STYLESHEET_FORMAT_IDENTIFIER"){
				return $this->get_stylesheet_format_identifier($parameter_list);
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
		$this->load_locale("theme");
		/**
		* define some access functionality
		*/
		$this->module_admin_options			= array(
			array($this->module_command."SESSION_SET","LOCALE_THEME_TEST"),
			array($this->module_command."SELECT_THEME","THEME_SELECT_DEFAULT")
		);
		$this->module_admin_user_access		= array(
			array($this->module_command."ALL","COMPLETE_ACCESS")
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
		* Table structure for table 'theme client has'
		*
		* this table should only ever hold one record per client on the server 
		*/
		
		$fields = array(
			array("client_identifier"					,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("theme_identifier"					,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		
		$primary ="";
		$tables[count($tables)] = array("theme_client_has", $fields, $primary);
		
		//$this->call_command("DB_QUERY",array("INSERT INTO theme_client_has (theme_identifier,client_identifier) VALUES (1,1);"));

		/**
		* Table structure for the 'theme' table
		*/
		$fields = array(
		/**
		* Basic connectivity fields
		* the page it belongs to 
		* the client that owns this data
		* the language that the document is written in default 'english' if not supplied
		*/
		array("theme_identifier"			,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
		array("theme_label"					,"varchar(50)"		,"NOT NULL"	,"default '0'"),
		array("theme_description"			,"text"				,"NULL"		,"default ''"),
		array("theme_directory"				,"varchar(50)"		,"NOT NULL"	,"default '0'"),
		array("theme_secure"				,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		
		$primary ="theme_identifier";
		
		
		$tables[count($tables)] = array("theme_data", $fields, $primary);
		/**
		* insert default themes 
		*/
		
		
		/**
		* Table structure for table 'theme_templates'
		* 
		* this will define the template stylesheet to load for a theme x template type
		*/
		$fields = array(
		array("template_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
		array("template_theme"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("template_xsl_file"		,"varchar(255)"		,"NOT NULL"	,"default '0'"),
		array("template_type"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("template_xsl_entry"		,"varchar(255)"		,"NOT NULL"	,"default '0'")
		);
		
		$primary ="template_identifier";
		$tables[count($tables)] = array("theme_templates", $fields, $primary);
		

		/**
		* Table structure for table 'theme_types'
		*/
		$fields = array(
			array("theme_type_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("theme_type_label"			,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("theme_type_product"			,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("theme_type_command"			,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("theme_type_branch"			,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("theme_type_leaf"				,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("theme_type_field_list"		,"varchar(255)"		,"NOT NULL"	,"default 'all'")
		);
		$primary ="theme_type_identifier";
		
		$tables[count($tables)] = array("theme_types", $fields, $primary,"THEME_GENERATE_DEFAULT_DB_RECORDS");
		
		/**
		* insert basic theme types 
		*/
		/*
		
*/
		return $tables;
	}
	
	function generate_themes(){
		$themes = Array(
			Array("Libertas Corporate","theme004"),
			Array("Belfast Door","belfastdoor")
		);
		$data = "";
		for($i=0,$m=count($this->data_values);$i<$m;$i++){
			$data = "
					INSERT INTO theme_types 
						(theme_type_label, theme_type_product, theme_type_command, theme_type_branch,  theme_type_leaf) 
					VALUES 
						('".$this->data_values[$i][0]."', '".$this->data_values[$i][1]."', '".$this->data_values[$i][2]."', '".$this->data_values[$i][3]."', '".$this->data_values[$i][4]."');
					";
			$this->call_command("DB_QUERY",array($data));
		}

		for ($index=0,$length_array=count($themes);$index<$length_array;$index++){
			$this->add_entry_save(Array("theme_name"=> $themes[$index][0],"theme_dir"=> $themes[$index][1] ));
		}
		
	}
	
	/**
	* Function get_stylesheet()
	* 
	* this function is used to retrieve the style sheet that the system administrator has 
	* choosen for this location based on the theme of choice
	*/
	function get_stylesheet($parameters){
		$theme_identifier	= $this->check_parameters($parameters,"theme_identifier",$this->check_parameters($_SESSION,"CHOOSEN_THEME",-1));
		$style_identifier	= $this->check_parameters($parameters,"style_identifier",-1);
		$override_script	= $this->check_parameters($parameters,"override_script","");
		$command 			= $this->check_parameters($parameters,"command");
		$fake_uri			= $this->check_parameters($parameters,"fake_uri");
		if ($theme_identifier==-1){
			if ($override_script==""){
				$script = $this->parent->script;
			} else {
				if (is_array($override_script)){
					$script = $override_script[0];
				} else {
					$script = $override_script;
				}
			}

			/********** Start To get selected theme for a specific menu (Added By Muhammad Imran) **********/
			if ($fake_uri!=""){
				$menu_url = $fake_uri;
			}else{
				$menu_url = $script;
			}
			$sql_menu_theme ="select menu_theme from menu_data where menu_data.menu_client = $this->client_identifier and menu_data.menu_url='$menu_url'";
			$result_menu_theme = $this->call_command("DB_QUERY",array($sql_menu_theme));
			$r_menu_theme = $this->call_command("DB_FETCH_ARRAY",array($result_menu_theme));
			$menu_theme_id = $r_menu_theme["menu_theme"];
			if ($menu_theme_id != 0){
				$sql ="
				select 
					theme_type_label, template_xsl_file, template_xsl_entry, theme_directory, theme_data.theme_identifier
				from theme_templates
					inner join theme_types on theme_types.theme_type_identifier =theme_templates.template_type 
					inner join theme_data on theme_data.theme_identifier =theme_templates.template_theme 
					inner join theme_client_has on theme_data.theme_identifier =theme_client_has.theme_identifier
					inner join menu_data on menu_data.menu_stylesheet = theme_types.theme_type_identifier  
					and menu_data.menu_theme = theme_data.theme_identifier
				where 
					menu_data.menu_client = $this->client_identifier and 
					";
			/********** End To get selected theme for a specific menu (Added By Muhammad Imran) **********/
			}else{
				$sql ="
				select 
					theme_type_label, template_xsl_file, template_xsl_entry, theme_directory, theme_data.theme_identifier
				from theme_templates
					inner join theme_types on theme_types.theme_type_identifier =theme_templates.template_type 
					inner join theme_data on theme_data.theme_identifier =theme_templates.template_theme 
					inner join theme_client_has on theme_data.theme_identifier =theme_client_has.theme_identifier
					inner join menu_data on menu_data.menu_stylesheet = theme_types.theme_type_identifier  
				where 
					theme_client_has.client_identifier = $this->client_identifier  and menu_data.menu_client = $this->client_identifier and 
					";
			}#menu_theme_id
			if ($fake_uri!=""){
				$sql .="menu_data.menu_url='$fake_uri'";
			}else{
				$sql .="menu_data.menu_url='$script'";
			}
		} else if ($theme_identifier != -1 && $style_identifier == -1){
			$sql ="
			select 
				theme_type_label, template_xsl_file, template_xsl_entry, theme_directory, theme_data.theme_identifier
			from theme_templates
				inner join theme_types on theme_types.theme_type_identifier =theme_templates.template_type 
				inner join theme_data on theme_data.theme_identifier =theme_templates.template_theme 
				inner join menu_data on menu_data.menu_stylesheet = theme_types.theme_type_identifier  
			where 
				menu_data.menu_client = $this->client_identifier and theme_data.theme_identifier=$theme_identifier and 
				";
			if ($fake_uri!=""){
				$sql .="menu_data.menu_url='$fake_uri'";
			}else{
				$sql .="menu_data.menu_url='".$this->parent->script."'";
			}
		} else if ($override_script!=""){
			$script = $override_script;
			$sql ="
			select 
				theme_type_label, template_xsl_file, template_xsl_entry, theme_directory, theme_data.theme_identifier
			from theme_templates
				inner join theme_types on theme_types.theme_type_identifier =theme_templates.template_type 
				inner join theme_data on theme_data.theme_identifier =theme_templates.template_theme 
				inner join theme_client_has on theme_data.theme_identifier =theme_client_has.theme_identifier
				inner join menu_data on menu_data.menu_stylesheet = theme_types.theme_type_identifier  
			where 
				theme_client_has.client_identifier = $this->client_identifier  and menu_data.menu_client = $this->client_identifier and ";
			if ($fake_uri!=""){
				$sql .="menu_data.menu_url='$fake_uri'";
			}else{
				$sql .="menu_data.menu_url='$script'";
			}
		}else{
			$sql ="
			select 
				theme_type_label, template_xsl_file, template_xsl_entry, theme_directory, theme_data.theme_identifier
			from theme_templates
				inner join theme_types on theme_types.theme_type_identifier =theme_templates.template_type 
				inner join theme_data on theme_data.theme_identifier = theme_templates.template_theme
			where 
				template_theme = $theme_identifier and template_type='$style_identifier'";
		}
		$sql .= "order by theme_data.theme_identifier desc limit 0,1";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}

		$result = $this->call_command("DB_QUERY",array($sql));
		$out="";	
		$t_id = $theme_identifier;	
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))) {
			$t_id = $r["theme_identifier"];
			$t_type = $r["theme_type_label"];
			if (substr($command,strlen($command)-7)=="DISPLAY"){
				if ($r["template_xsl_entry"].""==""){
					$out = "/stylesheets/themes/".$r["theme_directory"]."/default_main.xsl";
				} else {
					$out = "/stylesheets/themes/".$r["theme_directory"]."/".$r["template_xsl_entry"];
				}
			} else {
				if ($r["template_xsl_file"].""==""){
					$out = "/stylesheets/themes/".$r["theme_directory"]."/default_main.xsl";
				} else {
					$out = "/stylesheets/themes/".$r["theme_directory"]."/".$r["template_xsl_file"];
				}
			}
		}
		return Array($out, $t_id, $t_type);
	}

	/**
	* function select_stylesheet()
	* 
	* this function will return the list of template types (display options) available to the 
	* system.
	*/
	function select_stylesheet($parameters){
		$selected = $this->check_parameters($parameters,0,-1);
		if (strlen($this->check_parameters($parameters,"LOAD_LOCALE",""))>0){
//			$locale = $this->load_locale_file("module_themes");
			$locale = array();
		} else {
			$locale = array();
		} 
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$where =""; // no conditions
		} else if ($this->parent->server[LICENCE_TYPE]==MECM){
			$where ="where (theme_type_product = 'MECM' or theme_type_product = '')";
		} else {
			$where ="where (theme_type_product = '')";
		}
		$sql ="select distinct theme_types.* from theme_types 
		      $where order by theme_type_label asc";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$ss_result = $this->call_command("DB_QUERY",array($sql));
		$out="";		
		if($this->call_command("DB_NUM_ROWS",Array($ss_result))>0){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[building options]"));
			}
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($ss_result))) {
				$out.="<option value=\"".$r["theme_type_identifier"]."\"";
				if ($r["theme_type_identifier"]==$selected){
					$out.=" selected=\"true\" ";
				}
				if (strlen($this->check_parameters($parameters,"LOAD_LOCALE",""))>0){
					$out.=">".$this->check_parameters($locale, $r["theme_type_label"], $r["theme_type_label"])."</option>";
				} else {
					eval("\$style = ".$r["theme_type_label"].";");
					$out.=">".$style."</option>";
				}
			}
			$this->call_command("DB_FREE",array($ss_result));
		}
		return $out;
		
	}

	/**
	* function select_theme()
	* 
	* this function will allow the system administrator to select the default theme for the site.
	*/
	function select_theme($parameters){
		$update_complete = $this->check_parameters($parameters,"success",0);
		$sql = "select * from theme_client_has where client_identifier=$this->client_identifier";
		$theme_result = $this->call_command("DB_QUERY",array($sql));
		$theme_identifier = -1;
		if($this->call_command("DB_NUM_ROWS",Array($theme_result))>0){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($theme_result))) {
				$theme_identifier = $r["theme_identifier"];
			}
			$this->call_command("DB_FREE",array($theme_result));
		}
		$selection		= $this->call_command("THEME_RETRIEVE_LIST_OF_FULL",Array("theme_identifier"=>$theme_identifier));
		$out ="
		<module name=\"themes\" display=\"form\"><page_options><header><![CDATA[Select the site theme]]></header><button command=\"ENGINE_SPLASH\" value=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\" /></page_options>
			<form name=\"select_default_theme\" label=\"".LOCALE_THEME_PICKER."\" method=\"post\" width=\"100%\">
				<input type=\"hidden\" name=\"command\" value=\"THEME_SAVE\"/>
				<input type=\"hidden\" name=\"previous_theme\" value=\"$theme_identifier\" />";
		if($update_complete==1){
			$out .= "<text><![CDATA[".LOCALE_THEME_UPDATE_MSG."]]></text>";
		}
		$out .="<radio name=\"theme_identifier\" label=\"".LOCALE_THEME_CHOOSE."\">$selection</radio>
				<input type=\"submit\" name=\"SAVE\" value=\"".SAVE_DATA."\" iconify=\"SAVE\"/>
			</form>
		</module>";
		return $out;
	}

	/**
	* function save_theme()
	* 
	* this function will allow the system administrator to select the default theme for the site.
	*/
	function save_theme($parameters){
		$previous_theme 	= $this->check_parameters($parameters,"previous_theme",-1);
		$theme_identifier 	= $this->check_parameters($parameters,"theme_identifier",-1);
		
		if ($previous_theme==-1){
			/**
			* the client does not have a default theme then add record
			*/
			$sql = "insert into theme_client_has (client_identifier, theme_identifier) values($this->client_identifier, $theme_identifier);";
		}else{
			/**
			* update the clients record to point to the new theme
			*/
			$sql = "update theme_client_has  set theme_identifier=$theme_identifier where client_identifier=$this->client_identifier;";
		
		}
		$theme_result = $this->call_command("DB_QUERY",array($sql));
		return true;
	}
	
	/**
	* function retrieve_list_of_themes()
	* 
	* this function will allow the system administrator to select the default theme for the site.
	*/
	function retrieve_list_of_themes($parameters){
		$theme_identifier 	= $this->check_parameters($parameters,"theme_identifier",-1);
		$sql = "select * from theme_data where theme_secure = 0 or theme_secure = $this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[building options]"));
		}
		$selection="";
		if($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))) {
				$selected ="";
				if ($theme_identifier== $r["theme_identifier"]){
					$selected=" selected=\"true\"";
				}
				$selection.="<option value=\"".$r["theme_identifier"]."\" $selected>".$r["theme_label"]."</option>";
			}
			$this->call_command("DB_FREE",array($result));
		}
		return $selection;
	}
	/**
	* function retrieve_list_of_themes_full()
	* 
	* this function will allow the system administrator to select the default theme for the site.
	*/
	function retrieve_list_of_themes_full_description($parameters){
		$theme_identifier 	= $this->check_parameters($parameters,"theme_identifier",-1);
		$sql = "select * from theme_data where theme_secure = 0 or theme_secure = $this->client_identifier order by theme_label";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[building options]"));
		}
		$selection="";
		if($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))) {
				$selected ="";
				if ($theme_identifier== $r["theme_identifier"]){
					$selected=" selected=\"true\"";
				}
				$selection.="<option value=\"".$r["theme_identifier"]."\" $selected label=\"".$r["theme_label"]."\"><![CDATA[".$this->check_parameters($r,"theme_description")."]]></option>";
			}
			$this->call_command("DB_FREE",array($result));
		}
		return $selection;
	}

	function get_current_theme(){
		$sql ="select * from theme_client_has where client_identifier=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[building options]"));
		}
		if($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))) {
				return $r["theme_identifier"];
			}
		}
	
	}
	function add_entry($parameters){
		$xsl_dir		 = $this->parent->site_directories["XSL_THEMES_DIR"];
		$d = dir($xsl_dir."/stylesheets/themes");
		$themes = Array();
		while (false !== ($entry = $d->read())) {
			if (is_dir($xsl_dir."/stylesheets/themes/".$entry) && ($entry != "." ) && ($entry != ".." && $entry != "site_administration" && $entry != "printer_friendly" && $entry != "pda" && $entry != "textonly")){
				$themes[count($themes)]= Array("DIR"=> $entry, "CONTAINED"=>"NO", "LABEL"=>NULL);
			}
		}
		$d->close(); 
		
		$sql = "select * from theme_data";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[building options]"));
		}
		if($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))) {
				for ($index=0,$max = count($themes);$index<$max;$index++){
					if ($themes[$index]["DIR"] == $r["theme_directory"]){
						$themes[$index]["CONTAINED"] = "YES";
						$themes[$index]["LABEL"] = $r["theme_label"];
						
					}
				}
			}
		}
			$out ="
		<module name=\"themes\" display=\"form\">
			<form name=\"select_default_theme\" label=\"".LOCALE_ADD_THEME_FORM."\" method=\"post\" width=\"100%\">
				<input type=\"hidden\" name=\"command\" value=\"THEME_ENTRY_IMPORT\"/>
				<text><![CDATA[<table>
					<tr>
						<td>".LOCALE_THEME_LABEL."</td>
						<td>".LOCALE_THEME_DIR."</td>
						<td>".LOCALE_THEME_IMPORT."</td>
					</tr>";
			$output="";
			$num=0;
			for ($index=0,$max = count($themes);$index<$max;$index++){
					if ($themes[$index]["CONTAINED"]=="NO"){
					$num++;
				$output.="
					<tr>
						<td><input type='text' name='theme_name[]' size='25' value='".$this->check_parameters($themes[$index],"LABEL",$themes[$index]["DIR"])."'/></td>
						<td><input type='hidden' name='theme_dir[]' value='".$themes[$index]["DIR"]."'/>".$themes[$index]["DIR"]."</td>
						<td><input type='checkbox' name='theme_import[]' size='25' value='$num' checked='true'/></td>
					</tr>";
					}
			}
			$out.="$output'
				</table><input type='hidden' name='number_of' value='$num'/>]]></text>
				
				<input type=\"submit\" name=\"SAVE\" value=\"".SAVE_DATA."\" iconify=\"SAVE\"/>
			</form>
		</module>";
		return $out;
	}
	function entry_import_save($parameters){
		$theme_name 	= $this->check_parameters($parameters,"theme_name");
		$theme_dir		= $this->check_parameters($parameters,"theme_dir");
		$theme_import	= $this->check_parameters($parameters,"theme_import");
		$number_of 		= $this->check_parameters($parameters,"number_of");
		
		
		for ($z=0;$z<count($theme_import);$z++){
			$index = $theme_import[$z]-1;
			$sql = "insert into theme_data (theme_label, theme_directory) values ('".$theme_name[$index]."', '".$theme_dir[$index]."');";
			$this->call_command("DB_QUERY",array($sql));
			$theme_identifier=-1;
			$result = $this->call_command("DB_QUERY",array("select * from theme_data where theme_label = '".$theme_name[$index]."' and theme_directory='".$theme_dir[$index]."';"));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$theme_identifier = $r["theme_identifier"];
			}
			$sql ="select * from theme_types";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$sql = "insert into theme_templates (template_theme, template_xsl_file, template_type, template_xsl_entry) values ($theme_identifier, '".$r["theme_type_branch"]."', '".$r["theme_type_identifier"]."', '".$r["theme_type_leaf"]."');";
//				print "<p>$sql</p>";
				$this->call_command("DB_QUERY",array($sql));
			}
		}
//		$this->call_command("DB_QUERY",array("update theme_client_has set theme_identifier= $theme_identifier where client_identifier = $this->client_identifier"));
	}
	function add_entry_save($parameters){
		$theme_name 	= $this->check_parameters($parameters,"theme_name");
		$theme_dir		= $this->check_parameters($parameters,"theme_dir");
		$theme_import	= $this->check_parameters($parameters,"theme_import");
		$number_of 		= $this->check_parameters($parameters,"number_of");
		$xslt_dir 		 = $this->check_parameters($this->parent->site_directories,"XSL_THEMES_DIR");
		if (file_exists($xslt_dir."/stylesheets/themes/".$theme_dir."/available_styles.data")){
			$sql = "insert into theme_data (theme_label, theme_directory) values ('".$theme_name."', '".$theme_dir."');";
			$this->call_command("DB_QUERY",array($sql));
			$theme_identifier=-1;
			$result = $this->call_command("DB_QUERY",array("select * from theme_data where theme_label = '".$theme_name."' and theme_directory='".$theme_dir."';"));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$theme_identifier = $r["theme_identifier"];
			}
			$sql ="select * from theme_types";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$sql = "insert into theme_templates (template_theme, template_xsl_file, template_type, template_xsl_entry) values ($theme_identifier, '".$r["theme_type_branch"]."', '".$r["theme_type_identifier"]."', '".$r["theme_type_leaf"]."');";
				$this->call_command("DB_QUERY",array($sql));
			}
/*
		for ($i=0,$len_array=count($this->data_values);$i<$len_array;$i++){
			$this->call_command("DB_QUERY",array("insert into theme_templates (template_theme, template_xsl_file, template_type, template_xsl_entry) values ($theme_identifier, '".$this->data_values[$i][3]."', '".$i."', '".$this->data_values[$i][4]."');"));
		}
*/
			$this->call_command("DB_QUERY",array("update theme_client_has set theme_identifier= $theme_identifier where client_identifier = $this->client_identifier"));
		}
	}

	function xadd_entry_save($parameters){
		$theme_name 	= $this->check_parameters($parameters,"theme_name");
		$theme_dir		= $this->check_parameters($parameters,"theme_dir");
		$theme_import	= $this->check_parameters($parameters,"theme_import");
		$number_of 		= $this->check_parameters($parameters,"number_of");
		$sql = "insert into theme_data (theme_label, theme_directory) values ('".$theme_name."', '".$theme_dir."');";
		$this->call_command("DB_QUERY",array($sql));
		$theme_identifier=-1;
		$result = $this->call_command("DB_QUERY",array("select * from theme_data where theme_label = '".$theme_name."' and theme_directory='".$theme_dir."';"));
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$theme_identifier = $r["theme_identifier"];
		}
		$sql ="select * from theme_types";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$sql = "insert into theme_templates (template_theme, template_xsl_file, template_type, template_xsl_entry) values ($theme_identifier, '".$r["theme_type_branch"]."', '".$r["theme_type_identifier"]."', '".$r["theme_type_leaf"]."');";
			$this->call_command("DB_QUERY",array($sql));
		}
/*
		for ($i=0,$len_array=count($this->data_values);$i<$len_array;$i++){
			$this->call_command("DB_QUERY",array("insert into theme_templates (template_theme, template_xsl_file, template_type, template_xsl_entry) values ($theme_identifier, '".$this->data_values[$i][3]."', '".$i."', '".$this->data_values[$i][4]."');"));
		}
*/
		$this->call_command("DB_QUERY",array("update theme_client_has set theme_identifier= $theme_identifier where client_identifier = $this->client_identifier"));
	}
	
	function get_css($parameters){
		$sql ="select theme_directory from theme_data inner join theme_client_has on  theme_client_has.theme_identifier = theme_data.theme_identifier where theme_client_has.client_identifier = $this->client_identifier ";
		$ss_result = $this->call_command("DB_QUERY",array($sql));
		$out="";		
		if($this->call_command("DB_NUM_ROWS",Array($ss_result))>0){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[building options]"));
			}
		
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($ss_result))) {
				$out=$r["theme_directory"];
			}
			$this->call_command("DB_FREE",array($ss_result));
		}
		return $out;
	}
	
	function update_db($parameters){
		$sql = "select * from theme_types";
//		print "<li>$sql</li>";
		$result  = $this->call_command("DB_QUERY",Array($sql));
/*
0		'LOCALE_THEME_005_TYPE_DISPLAY',					
1		'', 	
2		'PRESENTATION_DISPLAY',		
3		"default_display_all.xsl",					
4		"default_main.xsl"						
5		,"content"
*/

		$m = count($this->data_values);
		$updated=0;
		$look_for = Array();
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$found=0;
//			print "<li> checking for ".$r["theme_type_label"]."</li>";
			for($i=0; $i<$m;$i++){
				if($r["theme_type_label"]==$this->data_values[$i][0]){
					$found=1;
					$this->data_values[$i][6]=1;
				}
			}
        }
		for($i=0; $i<$m;$i++){
        	if($this->check_parameters($this->data_values[$i],6,0)==0){
				$sql = "insert into theme_types (theme_type_label, theme_type_product, theme_type_command, theme_type_branch, theme_type_leaf, theme_type_field_list)
						values 
					('".$this->data_values[$i][0]."', '".$this->data_values[$i][1]."', '".$this->data_values[$i][2]."', '".$this->data_values[$i][3]."', '".$this->data_values[$i][4]."', '".$this->data_values[$i][5]."')";
//				print "<li>$sql</li>";
				$this->call_command("DB_QUERY",array($sql));
				$look_for[count($look_for)] = Array($this->data_values[$i][3] , $this->data_values[$i][4]);
				$updated=1;
			}
		}
        $this->call_command("DB_FREE",Array($result));
			
        // -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        // - check directories for required files
        // -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            
		$sql = "select * from theme_types 
				left outer join theme_templates on theme_types.theme_type_identifier = template_type
				right outer join theme_data on theme_data.theme_identifier = theme_templates.template_theme
			where template_theme  is null
				order by theme_directory, template_type";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$m = count($look_for);
		$xslt_dir 		 = $this->check_parameters($this->parent->site_directories,"XSL_THEMES_DIR");
    	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$theme_dir	= $r["theme_directory"];
			$theme_id	= $r["theme_identifier"];
			print "<li>".$xslt_dir."/stylesheets/themes/".$theme_dir."/".$r["theme_type_leaf"]."</li>";
			if (file_exists($xslt_dir."/stylesheets/themes/".$theme_dir."/".$r["theme_type_leaf"]) && file_exists($xslt_dir."/stylesheets/themes/".$theme_dir."/".$r["theme_type_branch"])){
				$sql = "insert into theme_templates (
						template_theme, 
						template_xsl_file, 
						template_type, 
						template_xsl_entry
					) values (
						'".$r["theme_identifier"]."', 
						'".$r["theme_type_branch"]."', 
						'".$r["theme_type_identifier"]."', 
					'".$r["theme_type_leaf"]."'
					);";
				$this->call_command("DB_QUERY",array($sql));
			}
        }
		$this->call_command("DB_FREE",Array($result));
	}
	
	function session_theme($parameters){
		$theme_identifier 	= $this->check_parameters($parameters,"CHOOSEN_THEME",$this->check_parameters($_SESSION,"CHOOSEN_THEME",-1));
		$choosen_theme 		= $this->check_parameters($_SESSION,"CHOOSEN_THEME",-1);
		$css_override		= $this->check_locale_starter($this->check_parameters($parameters,"css_override",$this->check_parameters($_SESSION,"css_override","Enter your Override the detaulf css for this theme?")));
		$ok =0;
		if ($css_override != "Enter your Override the detaulf css for this theme?"){
			if ($css_override!=$this->check_parameters($_SESSION,"css_override")){
				$_SESSION["css_override"] = $css_override;
				$ok = 1;
			}
		}
		if ($theme_identifier != $choosen_theme){
			$_SESSION["CHOOSEN_THEME"] = $theme_identifier;
			$ok = 1;
		}
		if($ok==1 && $theme_identifier !=-1){
			$this->parent->refresh(Array($this->parent->base.$this->parent->script."?command=THEME_SESSION_SET"));
		}
		$sql = "select distinct theme_identifier, theme_label from theme_data 
			inner join web_layouts on wol_theme = theme_identifier 
		where (theme_secure = 0 or theme_secure = $this->client_identifier) and wol_client = $this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[building options]"));
		}
		$selection="";
		if($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))) {
				$selected ="";
				if ($theme_identifier== $r["theme_identifier"]){
					$selected=" selected=\"true\"";
				}
				$selection.="<option value=\"".$r["theme_identifier"]."\" $selected>".$r["theme_label"]."</option>";
			}
			$this->call_command("DB_FREE",array($result));
		}
		return "<module name='theme' display='form'><form name='frm' label='Select a theme for this session' method='post'><input type='hidden' name='command' value='THEME_SESSION_SET'/>
		<input type='text' name='css_override' value='$css_override' label='Override the detaulf css for this theme?'/>
		<select name='CHOOSEN_THEME' label='Choose the theme for your session'>$selection</select><input type='submit' iconify='SAVE' value='Save for Session'/></form></module>";
	}
	
	function get_stylesheet_format_identifier($parameters){
		$code  = $this->check_parameters($parameters,0);
		$id=5;
		$sql = "select * from theme_types where theme_type_label='$code'";
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$id = $r["theme_type_identifier"];
        }
        $this->call_command("DB_FREE",Array($result));
		return $id;
	}
	
	
//	function session_theme_save($parameters){
		
//	}
}
?>