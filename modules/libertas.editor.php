<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.editor.php
* @created 01 Oct 2002
*/
/**
* This module is the module for managing Editor functionality access for users. it allows you to
* manage a selection of editor configurations and to define that a group has access to a specific editor
*
* it also allows you to specify the default editor for a modules form.
*/
class editor extends module{
	/**#@+
	* Class Variables
    * @var string
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_name				= "editor";
	var $module_name_label			= "WYSIWYG Editor Management Module (Administration)";
	var $module_admin				= "1";
	var $module_channels			= "";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.26 $';
	var $module_command				= "EDITOR_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_EDITOR";

    var $searched					= 0;

	/**#@+
	*  Management Menu entries
	*/
	var $module_admin_options 		= array(
	);
	var $admin_access				= "";
	var $group_admin_access			= "";
	var $page_admin_access			= "";

	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	var $module_admin_user_access	= array(
		array("EDITOR_ALL", "COMPLETE_ACCESS","")
	);

    /**
	*  Channel options
	*/
	var $module_display_options 	= array();

    /**
	*  Class Methods
	*/

    /**
	* Call a module command
	*
	* This moudle will only execute commands that start with the module starter variable it will return an empty string if it
	* accesses this module but does not know the function or the user does not have role access to this function 
	*
    * @param String the Command to call in this module
    * @param Array of parameters to pass to the required function
	*
	* @return String data from function called (XML format)
	*/
	function command($user_command, $parameter_list=array()){
		/**
		*If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/**
		*This is the main function of the Module this function will call what ever function
		* you want to call.
		*/

		if (strpos($user_command,$this->module_command)===0){
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
			if ($user_command==$this->module_command."CREATE_NEW_CLIENT_DETAILS"){
				return $this->create_client_details($parameter_list);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($this->module_admin_access==1){
				if ($user_command==$this->module_command."ACCESS_OPTIONS"){
					return $this->module_admin_options(0);
				}
//				if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
					if ($user_command==$this->module_command."ADD" || $user_command==$this->module_command."EDIT"){
						return $this->data_form($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE"){
						return $this->data_remove($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE_CONFIRM"){
						return $this->data_remove_confirm($parameter_list);
					}
					if ($user_command==$this->module_command."SAVE"){
						$this->data_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=EDITOR_LIST"));
					}
					if ($user_command==$this->module_command."LIST"){
						return $this->list_information($parameter_list);
					}
//				}
				if ($user_command==$this->module_command."PALETTE"){
					return $this->modify_palette($parameter_list);
				}
				if ($user_command==$this->module_command."PALETTE_SAVE"){
					return $this->palette_save($parameter_list);
				}
				if ($user_command==$this->module_command."CONVERT_FONT_TO_SPAN" || $user_command==$this->module_command."CONVERT_FROM_EDITOR"){
					return $this->convert_font_to_span_antispam($parameter_list);
				}
				if ($user_command==$this->module_command."CONVERT_SPAN_TO_FONT" || $user_command==$this->module_command."CONVERT_FOR_EDITOR"){
					return $this->convert_span_to_font($parameter_list);
				}
			}
			if ($user_command==$this->module_command."CONVERT_DATA_TO_HTML"){
				return $this->data_to_html($parameter_list);
			}
			if ($user_command==$this->module_command."EMBED_IN_GROUP_EDIT"){
				return $this->embed_group_function($parameter_list);
			}
			if ($user_command==$this->module_command."SAVE_GROUP"){
				return $this->embed_group_save($parameter_list);
			}
			if ($user_command==$this->module_command."CACHE"){
				return $this->cache_editor($parameter_list[0]);
			}
			if ($user_command==$this->module_command."RESTORE"){
				return $this->restore($parameter_list);
			}
			if ($user_command==$this->module_command."LOAD_CACHE"){
				return $this->load_editor_config($parameter_list);
			}
			if ($user_command==$this->module_command."CONFIGURE_MODULES"){
				return $this->configure_modules($parameter_list);
			}
			if ($user_command==$this->module_command."CONFIGURE_MODULE_SAVE"){
				return $this->configure_module_save($parameter_list);
			}

		}else{
			// wrong command sent to system
			return "";
		}
	}
	/**
	* @function Initialise function
	*
	* This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	* module::initialise() function allowing you to define any extra constructor
	* functionality.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		/**
		*request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		*define some variables
		*/
		$this->admin_access			= 0;
		$this->module_admin_access	= 0;
		$this->group_admin_access	= 0;
		$this->page_admin_access	= 0;
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
					("EDITOR_ALL"==$access[$index]) ||
					("ALL"==$access[$index])
				){
					$this->admin_access=1;
				}
				if (
					("GROUP_ALL"==$access[$index]) ||
					("ALL"==$access[$index])
				){
					$this->group_admin_access	= 1;
				}
				if (
					("PAGE_AUTHOR"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("ALL"==$access[$index])
				){
					$this->page_admin_access	= 1;
				}
			}
		}
		if (($this->admin_access || $this->page_admin_access) && (($this->parent->module_type=="admin")||($this->parent->module_type=="preview")||($this->parent->module_type=="files"))){
			$this->module_admin_access=1;
		}
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$this->module_admin_options[count($this->module_admin_options)] = array("EDITOR_CONFIGURE_MODULES", "Configure Module Editors");
			$this->module_admin_options[count($this->module_admin_options)] = array("EDITOR_LIST", "MANAGE_EDITOR");
			$this->module_admin_options[count($this->module_admin_options)] = array("EDITOR_PALETTE", "MANAGE_EDITOR_PALETTE");
		}
		return 1;
	}
    /**
    * Create Client Details
    *
    * This is the only function that allows the specification of the client_identifier as all other know this value
    * this function will load the database with what ever structure / content that is required on module initialisation for a
    * specific client it is called as part of the installation of a new client.
    */
    function create_client_details($parameters){
		$client_identifier = $this->check_parameters($parameters,"client_identifier",$this->client_identifier);
		$insert_new = $this->check_parameters($parameters,"insert_new_client",1);
		$data = array(
			Array('LOCALE_CMD_AUTO_FORMAT'			, 'auto_tidy'),
			Array('LOCALE_CUT_COPY_PASTE'			, 'cut_copy_paste'),
			Array('LOCALE_PASTE_SPECIAL'			, 'paste_special'),
			Array('LOCALE_UNDO_REDO'				, 'undo_redo'),
			Array('LOCALE_JUSTICICATION'			, 'justification'),
			Array('LOCALE_BOLD_ITALIC_UNDERLINE'	, 'bold_italic_underline'),
			Array('LOCALE_INTERNAL_LINK'			, 'internal_links'),
			Array('LOCALE_EXTERNAL_LINK'			, 'external_links'),
			Array('LOCALE_EMAIL_LINK'				, 'email_links'),
			Array('LOCALE_FILE_LINK'				, 'file_links'),
			Array('LOCALE_INSERT_IMAGES'			, 'images'),
			Array('LOCALE_INSERT_SPECIAL_CHARACTER'	, 'special_character'),
			Array('LOCALE_INSERT_HORIZONTAL_LINE'	, 'hr'),
			Array('LOCALE_DEFINE_HEADING'			, 'headings'),
			Array('LOCALE_CMD_CAN_TOGGLE_DESIGN'	, 'toggle_design'),
			Array('LOCALE_TIDY'						, 'tidy'),
			Array('LOCALE_INDENT_UNINDENT'			, 'indent_unindent'),
			Array('LOCALE_CAN_BULLET'				, 'bullet'),
			Array('LOCALE_CAN_EMBED_FORM'			, 'embed_form'),
			Array('LOCALE_TABLE_BASIC'				, 'tables_basic'),
			Array('LOCALE_TABLE_CELL'				, 'tables_cell'),
			Array('LOCALE_TABLE_ROW_COLUMN'			, 'tables_row_column'),
			Array('LOCALE_SET_ZOOM'					, 'set_zoom'),
			Array('LOCALE_ONLINE_HELP'				, 'help'),
			Array('LOCALE_PAGE_PROPERTIES'			, 'page_properties'),
			Array('LOCALE_ACRONYMS'					, 'abbr_acronym')
		);
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
				$data[count($data)] = Array('LOCALE_DEFINE_SLIDESHOW'			, 'slideshow');

			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				$data[count($data)] = Array('LOCALE_SPELL_CHECK'			, 'spell_checker');
				// neil requested move
				$data[count($data)] = Array('LOCALE_ACRONYMS_AND_ABBR'		, 'abbr_acronym');
				$data[count($data)] = Array('LOCALE_CAN_SET_FT'				, 'font_face');
				$data[count($data)] = Array('LOCALE_CMD_TABLE_CELL_COLOUR'	, 'tables_cell_colour');
				$data[count($data)] = Array('LOCALE_CMD_TABLE_COLOUR'		, 'tables_colour');
				$data[count($data)] = Array('LOCALE_CAN_SET_FS'				, 'font_size');
				$data[count($data)] = Array('LOCALE_CAN_SET_FC'				, 'fore_colour');
				$data[count($data)] = Array('LOCALE_CAN_SET_BG'				, 'background_colour');
				$data[count($data)] = Array('LOCALE_SUB_SUPER_STRIKE'		, 'sub_super_strike');
				$data[count($data)] = Array('LOCALE_CAN_EMBED_FLASH'		, 'embed_flash');
				$data[count($data)] = Array('LOCALE_CAN_EMBED_MOVIE'		, 'embed_movie');
				$data[count($data)] = Array('LOCALE_CAN_EMBED_AUDIO'		, 'embed_audio');
				$data[count($data)] = Array('LOCALE_FIND_REPLACE'			, 'find_replace');
				$data[count($data)] = Array('LOCALE_TABLE_CELL_SPLIT_MERGE'	, 'tables_split_merge');
//				$data[count($data)] = Array('LOCALE_CMD_CONTEXT_SENSITIVE_MENU', 'context_sensitive');
			}
		}
		if ($insert_new==1){
			for ($i=0;$i<count($data);$i++){
				$this->call_command("DB_QUERY",array("INSERT INTO editor_button ( editor_button_client, editor_button_label, editor_button_id) VALUES('".$client_identifier."', '".$data[$i][0]."', '".$data[$i][1]."');"));
			}

			$now =$this->libertasGetDate("Y/m/d H:i:s");
			$sql = "INSERT INTO editor_config (editor_config_label, editor_config_client, editor_date_created, editor_created_by_user, editor_config_default) VALUES('Full Access', $client_identifier, '$now', '0', '1')";
			$result = $this->call_command("DB_QUERY",array($sql));
			$sql = "Select editor_config_identifier from editor_config where editor_config_label = 'Full Access' and editor_config_client = $client_identifier and editor_date_created =  '$now' and editor_created_by_user	= '0' and editor_config_default	= '1'";

			$result = $this->call_command("DB_QUERY",array($sql));
			$config=0;
			while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result)) ){
				$config=$r["editor_config_identifier"];
			}
			$sql = "select * from editor_button where editor_button_client = $client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			$index=0;
			while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result)) ){
				$sql = "INSERT INTO editor_settings (editor_setting_config, editor_setting_client, editor_setting_button) VALUES($config,$client_identifier,".$r["editor_button_identifier"].");";
				$this->call_command("DB_QUERY",array($sql));
			}
			$default = Array(
				"#ff0000","#00ff00",
				"#0000ff","#FFFF00",
				"#ff9900","#99cc99",
				"#99ff00","#ccffff",
				"#66cccc","#cc00ff",
				"#ff00cc","#993300"
				);
			for ($index=0;$index<count($default);$index++){
				$sql = "insert into editor_palette (editor_palette_client, editor_palette_colour) values ($this->client_identifier, '".$default[$index]."');";
				$this->call_command("DB_QUERY",array($sql));
			}
			$this->cache_editor($config);
		} else {
			$sql = "select distinct * from editor_button where editor_button_client = $this->client_identifier order by editor_button_id";
			$result = $this->call_command("DB_QUERY", array($sql));
			$searchlist = Array();
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$searchlist[count($searchlist)] = $r["editor_button_id"];
			}
			for ($i=0;$i<count($data);$i++){
				$found 		= 0;
				for ($s=0;$s<count($searchlist);$s++){
					if ($searchlist[$s] == $data[$i][1]){
						$found 		= 1;
					}
				}
				if ($found==0){
					$this->call_command("DB_QUERY", array("INSERT INTO editor_button ( editor_button_client, editor_button_label, editor_button_id) VALUES('".$client_identifier."', '".$data[$i][0]."', '".$data[$i][1]."')"));
				}
			}
		}
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
		*Table structure for table 'editor_config'
		*/

		$fields = array(
			array("editor_config_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("editor_config_label"			,"varchar(255)"				,"NOT NULL"	,"default '0'"),
			array("editor_config_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("editor_date_created"			,"datetime"					,"NOT NULL"	,"default ''"),
			array("editor_created_by_user"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("editor_config_default"		,"unsigned small integer"	,"NOT NULL"	,"default '0'","")
		);

		$primary ="editor_config_identifier";
		$tables[count($tables)] = array("editor_config", $fields, $primary);

		/**
		*Table structure for table 'editor_settings'
		*/

		$fields = array(
			array("editor_setting_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("editor_setting_config"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("editor_setting_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("editor_setting_button"		,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);

		$primary ="editor_setting_identifier";
		$tables[count($tables)] = array("editor_settings", $fields, $primary);


		/**
		*Table structure for table 'editor_belongs_to_group'
		*/

		$fields = array(
			array("editor_belongs_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("editor_belongs_config"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("editor_belongs_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("editor_belongs_group"		,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);

		$primary ="editor_belongs_identifier";
		$tables[count($tables)] = array("editor_belonging_to_group", $fields, $primary);

		/**
		*Table structure for table 'editor_buttons'
		*/

		$fields = array(
			array("editor_button_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("editor_button_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("editor_button_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("editor_button_id"			,"varchar(255)"				,"NOT NULL"	,"default ''")
		);

		$primary ="editor_button_identifier";
		$tables[count($tables)] = array("editor_button", $fields, $primary);
		/**
		*Table structure for table 'editor_buttons'
		*/

		$fields = array(
			array("editor_palette_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("editor_palette_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("editor_palette_colour"		,"varchar(255)"				,"NOT NULL"	,"default ''")
		);

		$primary ="editor_palette_identifier";
		$tables[count($tables)] = array("editor_palette", $fields, $primary);

		/**
		*Table structure for table 'editor_buttons'
		*/

		$fields = array(
			array("mate_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("mate_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("mate_name"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("mate_configuration"	,"unsigned integer"				,"NOT NULL"	,"default ''"),
			array("mate_status"			,"unsigned integer"				,"NOT NULL"	,"default ''"),
			array("mate_module"			,"varchar(50)"				,"NOT NULL"	,"default ''")
		);

		$primary ="mate_identifier";
		$tables[count($tables)] = array("module_access_to_editor", $fields, $primary);


		return $tables;
	}

    /**
    * Cache Editor
    *
    * This function will cache the editor definition to the hard disk to remove the need to check the database for details
    *
    * @param Integer the identifier of the record to be cached
    */

	function cache_editor($identifier){
		$out = $this->generate_editor_config(Array("identifier" => $identifier,"display"=>"none"));
		$lang="en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fp = fopen($data_files."/editor_".$this->client_identifier."_".$lang."_".$identifier.".xml", 'w');
		fwrite($fp, $out);
		fclose($fp);
	    $um = umask(0);
		@chmod($data_files."/editor_".$this->client_identifier."_".$lang."_".$identifier.".xml", LS__FILE_PERMISSION);
		umask($um);
	}

    /**
    * Generate Editor Configuration
    *
    * This function will build the data fro the cache function
    *
    * @param Array requiring the following keys to be defined ("identifier", "display")
	* @return String representing the xml structure for this configuration
    */

    function generate_editor_config($parameters){
		$out		= "";
        $left       = "";
        $right      = "";
		$label		= "";
        $btn_list= Array();

		$identifier = $this->check_parameters($parameters,"identifier");
		$display 	= $this->check_parameters($parameters,"display");
		$sql 		="select * from editor_settings
						inner join editor_button on editor_button.editor_button_identifier = editor_settings.editor_setting_button
						inner join editor_config on editor_config.editor_config_identifier = editor_settings.editor_setting_config
					  where editor_setting_config =$identifier and editor_setting_client=$this->client_identifier";
//		print "<p>".$sql."</p>";
		if($result = $this->call_command("DB_QUERY",array($sql))) {
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$label = $r["editor_config_label"];
				$out .="<btn id='".$r["editor_button_id"]."'><![CDATA[".$r["editor_button_label"]."]]></btn>";
                $btn_list[count($btn_list)] = $r["editor_button_id"];
			}
			$this->call_command("DB_FREE",array($result));
		}
        $top        = "";
        $browser    = "";
        $version    = 0;
        if (1==2){
            $browser = "IE" ;
            $version = 6;
        }
		if (in_array('auto_tidy', $btn_list)){
			$top .= "<img id='LIBERTAS_[[editor]]_tb_auto_tidy' name='LIBERTAS_[[editor]]_tb_auto_tidy' src='/libertas_images/themes/1x1.gif' width='1' height='1' alt='Auto tidy'/>";
		}
		if (in_array('tables_cell_colour', $btn_list)){
			$top .= "<img id='LIBERTAS_[[editor]]_tb_tables_cell_colour' name='LIBERTAS_[[editor]]_tb_tables_cell_colour' src='/libertas_images/themes/1x1.gif' width='1' height='1' alt='Table Cell Colour'/>";
		}
		if (in_array('tables_colour', $btn_list)){
			$top .= "<img id='LIBERTAS_[[editor]]_tb_tables_colour' name='LIBERTAS_[[editor]]_tb_tables_colour' src='/libertas_images/themes/1x1.gif' width='1' height='1' alt='Table Colour'/>";
		}
		if (in_array('toggle_design', $btn_list)){
			$top .= "<img id='LIBERTAS_[[editor]]_tb_toggle_design' name='LIBERTAS_[[editor]]_tb_toggle_design' src='/libertas_images/themes/1x1.gif' width='1' height='1' alt='Can toggle design'/>";
		}
        //$m      = count($btn_list);
        //for($i=0; $i< $m ;$i++){
            if (in_array('cut_copy_paste', $btn_list)){
            	$top .= $this->gen_button('tb_cut','Cut','LIBERTAS_on_click','Cut');
            	$top .= $this->gen_button('tb_copy','Copy','LIBERTAS_on_click','Copy');
            	if ($this->parent->server[LICENCE_TYPE]==SITE_WIZARD) {
            		$top .= $this->gen_button('tb_paste_special','Paste Special','LIBERTAS_paste_special_click','paste_special');
            	} else {
            		if ($this->parent->server[LICENCE_TYPE]==ECMS) {
            			if (!in_array('auto_tidy', $btn_list)){
            				$top .= $this->gen_button('tb_paste','Paste','LIBERTAS_on_click','Paste');
            			} else {
            				$top .= $this->gen_button('tb_paste_special','Paste Special','LIBERTAS_paste_special_click','paste_special');
            			}
            		} else {
            			$top .= $this->gen_button('tb_paste_special','Paste Special','LIBERTAS_paste_special_click','paste_special');
            		}
            		if (in_array('paste_special', $btn_list)){
            			$top .= $this->gen_button('tb_dropdown','Paste As...','LIBERTAS_dropdown_menu_click','paste');
            		}
            	}
            }
            if (in_array('tidy', $btn_list)){
            	$top .= $this->gen_button('tb_vertical_separator','','');
            	$top .= $this->gen_button('tb_cleanup','HTML cleanup (removes styles, spans and fonts)','LIBERTAS_cleanup_click');
            }
            if (in_array('paste_special', $btn_list) || in_array('cut_copy_paste', $btn_list) || in_array('tidy', $btn_list)){
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('undo_redo', $btn_list)){
            	$top .= $this->gen_button('tb_undo','Undo','LIBERTAS_on_click','Undo');
            	$top .= $this->gen_button('tb_redo','Redo','LIBERTAS_on_click','Redo');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('find_replace', $btn_list)){
            	$top .= $this->gen_button('tb_find', 'Find', 'LIBERTAS_find_click');
            	$top .= $this->gen_button('tb_replace', 'Replace', 'LIBERTAS_replace_click');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('internal_links', $btn_list)){
            	$top .= $this->gen_button('tb_hyperlink','Internal Hyperlink','LIBERTAS_hyperlink_click', 'hyper');
            }
            if (in_array('external_links', $btn_list)){
            	$top .= $this->gen_button('tb_externallink','External Hyperlink','LIBERTAS_hyperlink_click', 'external');
            }
            if (in_array('email_links', $btn_list)){
            	$top .= $this->gen_button('tb_emaillink','Email Address Link','LIBERTAS_hyperlink_click', 'email');
            }
            if (in_array('file_links', $btn_list)){
            	$top .= $this->gen_button('tb_filelink','Link to a file','LIBERTAS_hyperlink_click', 'file');
            }
            if (in_array('internal_links', $btn_list) || in_array('external_links', $btn_list) || in_array('email_links', $btn_list) || in_array('file_links', $btn_list)){
            	$top .= $this->gen_button('tb_unlink','Remove Hyperlink','LIBERTAS_unlink_click');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('emocs_icons', $btn_list)){
            	$top .= $this->gen_button('tb_emoc','Insert Emocs Icon','LIBERTAS_emocs_click');
            }
            if (in_array('embed_flash', $btn_list)){
            	$top .= $this->gen_button('tb_flash', 'Insert Flash Object', 'LIBERTAS_flash_click');
            }
            if (in_array('embed_movie', $btn_list)){
            	$top .= $this->gen_button('tb_movie', 'Insert Movie', 'LIBERTAS_movie_click');
            }
            if (in_array('embed_audio', $btn_list)){
            	$top .= $this->gen_button('tb_audio', 'Insert Audio', 'LIBERTAS_audio_click');
            }
            if (in_array('images', $btn_list)){
            	$top .= $this->gen_button('tb_image_insert','Insert Image','LIBERTAS_image_insert_click');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('sub_super_strike', $btn_list)){
            	$top .= $this->gen_button('tb_subscript', 'SubScript', 'LIBERTAS_on_click','Subscript');
            	$top .= $this->gen_button('tb_superscript', 'SuperScript', 'LIBERTAS_on_click','Superscript');
            	$top .= $this->gen_button('tb_strikethrough', 'Strike Through', 'LIBERTAS_on_click','strikethrough');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('hr', $btn_list)){
            	$top .= $this->gen_button('tb_hr','Horizontal Rule','LIBERTAS_on_click','inserthorizontalrule');
            }
            if (in_array('special_character', $btn_list)){
            	$top .= $this->gen_button('tb_special_character','Insert Special Characters','LIBERTAS_special_char_click');
            	$top .= $this->gen_button('tb_dropdown','Special Character Lookup','LIBERTAS_dropdown_menu_click','html_entity');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('embed_form', $btn_list)){
            	$top .= $this->gen_button('tb_form', 'Embed a web form', 'LIBERTAS_embed_form_click');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('embed_audio', $btn_list) || in_array('embed_movie', $btn_list) || in_array('embed_flash', $btn_list)){
            }

            if (in_array('spell_checker', $btn_list)){
            	$top .= $this->gen_button('tb_spell', 'Check Spelling', 'LIBERTAS_spell_click');
            }
            if (in_array('page_properties', $btn_list)){
            	$top .= $this->gen_button('tb_page_prop', 'Page Statictics', 'LIBERTAS_stats_click');
            }
            if (in_array('spell_checker', $btn_list) || in_array('page_properties', $btn_list)){
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            $top .= $this->gen_button('tb_acronym','Acronym','LIBERTAS_acronym');
            //			$top .= $this->gen_button('tb_abbr','Abbreviation','LIBERTAS_abbr');
            $top .= $this->gen_button('tb_vertical_separator','','');
            $top .= $this->gen_button('tb_help','Online Help','LIBERTAS_help_click');
            $top .= $this->gen_button( 'endRow', '', '');
            $top .= $this->gen_button( 'startRow', '', '');
            if ($this->parent->server[LICENCE_TYPE]==ECMS) {
            	if (in_array('set_zoom', $btn_list)){
            		if ($browser == "IE" && $version>=5.5){
            			// this function requires IE 5.5 and Above
            			$top .= $this->gen_button('set_zoom','','');
            		}
            	}
            }
            if (in_array('headings', $btn_list)){
            	$top .= $this->gen_button('select_paragraph','','');
            }
            if (in_array('font_face', $btn_list)){
            	$top .= $this->gen_button('select_font_face','','');
            }
            if (in_array('font_size', $btn_list)){
            	$top .= $this->gen_button('select_font_size','','');
            }
            if (in_array('font_face', $btn_list) || in_array('font_size', $btn_list)){
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('bold_italic_underline', $btn_list)){
            	$top .= $this->gen_button('tb_bold','Bold','LIBERTAS_on_click','Bold');
            	$top .= $this->gen_button('tb_italic','Italic','LIBERTAS_on_click','Italic');
            	$top .= $this->gen_button('tb_underline','Underline','LIBERTAS_on_click','Underline');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('justification', $btn_list)){
            	$top .= $this->gen_button('tb_left','Left','LIBERTAS_on_click','justifyleft');
            	$top .= $this->gen_button('tb_center','Center','LIBERTAS_on_click','justifycenter');
            	$top .= $this->gen_button('tb_right','Right','LIBERTAS_on_click','justifyright');
            	$top .= $this->gen_button('tb_justify','Justify','LIBERTAS_justify_click');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('indent_unindent', $btn_list)){
            	$top .= $this->gen_button('tb_indent','Indent','LIBERTAS_on_click','Indent');
            	$top .= $this->gen_button('tb_unindent','Unindent','LIBERTAS_on_click','Outdent');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('bullet', $btn_list)){
            	$top .= $this->gen_button('tb_ordered_list','Ordered List','LIBERTAS_on_click','InsertOrderedList');
            	$top .= $this->gen_button('tb_bulleted_list','Bulleted List','LIBERTAS_on_click','InsertUnorderedList');
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('fore_colour', $btn_list)){
            	$top .= $this->gen_button('tb_fore_color', 'Set fore color', 'LIBERTAS_fore_color_click');
            	$top .= $this->gen_button('tb_dropdown','Select Color...','LIBERTAS_dropdown_menu_click','color_fore');
            }
            if (in_array('background_colour', $btn_list)){
            	$top .= $this->gen_button('tb_bg_color', 'Set background color', 'LIBERTAS_bg_color_click');
            	$top .= $this->gen_button('tb_dropdown','Select Color...','LIBERTAS_dropdown_menu_click','color_bg');
            }
            if (in_array('background', $btn_list) || in_array('fore_colour', $btn_list)){
            	$top .= $this->gen_button('tb_vertical_separator','','');
            }
            if (in_array('slideshow', $btn_list)){
            	$top .= $this->gen_button('tb_slideshow','Slideshow','LIBERTAS_embed_slideshow_click','undefined');
            }
        //}
            if (in_array('tables_basic', $btn_list) || in_array('tables_cell', $btn_list) || in_array('tables_row_column', $btn_list)  || in_array('tables_split_merge', $btn_list)){
                $left .= $this->gen_button('tb_table_create','Create a new table','LIBERTAS_table_create_click');
            	$left .= $this->gen_button('tb_dropdown','Table Wizard','LIBERTAS_dropdown_menu_click','tableWizard')."<br />";
            	$left .= $this->gen_button('tb_table_prop','Table properties','LIBERTAS_table_prop_click')."<br />";
            }
            if (in_array('tables_cell', $btn_list) && ($this->parent->server[LICENCE_TYPE]!=SITE_WIZARD)){
            	$left .= $this->gen_button('tb_table_cell_prop','Table Cell properties','LIBERTAS_table_cell_prop_click')."<br />";
            }
            if (in_array('tables_row_column', $btn_list)){
            	$left .= $this->gen_button('tb_table_row_insert','Insert a row into this table','LIBERTAS_table_row_insert_click')."<br />";
            	$left .= $this->gen_button('tb_table_column_insert','Insert a column into this table','LIBERTAS_table_column_insert_click')."<br />";
            	$left .= $this->gen_button('tb_table_row_delete','Table Row Delete','LIBERTAS_table_row_delete_click')."<br />";
            	$left .= $this->gen_button('tb_table_column_delete','Delete a column for this table ','LIBERTAS_table_column_delete_click')."<br />";
            }
            if (in_array('tables_split_merge', $btn_list)){
            	$left .= $this->gen_button('tb_table_cell_merge_right', 'Merge table cell right', 'LIBERTAS_table_cell_merge_right_click')."<br />";
            	$left .= $this->gen_button('tb_table_cell_merge_down', 'Merge table cell down', 'LIBERTAS_table_cell_merge_down_click')."<br />";
            	$left .= $this->gen_button('tb_table_cell_split_horizontal', 'Split table cell horizontally', 'LIBERTAS_table_cell_split_horizontal_click')."<br />";
            	$left .= $this->gen_button('tb_table_cell_split_vertical', 'Split table cell vertically', 'LIBERTAS_table_cell_split_vertical_click')."<br />";
            }
            if (in_array('tables_basic', $btn_list)){
            	$left .= $this->gen_button('tb_toggle_borders','Toggle table borders','LIBERTAS_toggle_borders_click')."<br />";
            }
        $return_data ="";
		if ($display!="none"){
			$return_data = "<module name='".$this->module_name."' display='settings'>";
		}
        $top = htmlentities(htmlentities($top));
        $left = htmlentities(htmlentities($left));
        $out .= "<top><![CDATA[$top]]></top>";
		$out .= "<left><![CDATA[$left]]></left>";
		$return_data .= "<setting id='".$identifier."' name='".$label."'>$out</setting>";
		if ($display!="none"){
			$return_data .= "</module>";
		}
		return $return_data;
	}
	/**
    *
    */
	function load_editor_config($parameters){
		$out		= "";
		$label		= "";
		$identifier = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1);
		$display 	= $this->check_parameters($parameters,"display");
		$editors	= $this->check_parameters($parameters,"editors",Array());
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access = "";
		for($i=0;$i < $max_grps; $i++){
			if (strlen($access)>0){
				$access .= ', ';
			}
			$access .= $grp_info[$i]["IDENTIFIER"];
		}
		$max_modules = count($editors);
		$access_editors = "";
//		print " $max_modules ";
		for($i=0;$i < $max_modules; $i++){
			$len = count($editors[$i]);
//			print $len;
			foreach($editors[$i] as $key => $ed){
				if (strlen($access_editors)>0){
					$access_editors .= ', ';
				}
				$access_editors .= $ed["identifier"];
			}
		}
		$sql 		="select * from editor_config
						inner join editor_belonging_to_group on editor_belonging_to_group.editor_belongs_config = editor_config.editor_config_identifier
					 where editor_config_client=$this->client_identifier and editor_belonging_to_group.editor_belongs_group in ($access)";
		$sql = "select distinct editor_config.* from editor_config
					left outer join editor_belonging_to_group on editor_belonging_to_group.editor_belongs_config = editor_config.editor_config_identifier
				where
					editor_config_client=$this->client_identifier and (
						editor_belonging_to_group.editor_belongs_group in ($access)
							or
						editor_config_identifier in ($access_editors)
					)";

//						print "<!-- \n\n$sql \n\n-->";
		$lang="en";
		$palette="";

		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$result = $this->call_command("DB_QUERY",array($sql));
		if(($num_rows = $this->call_command("DB_NUM_ROWS",array($result)))>0) {
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$file_content = file($data_files."/editor_".$this->client_identifier."_".$lang."_".$r["editor_config_identifier"].".xml");
				$out .= join(" ",$file_content);
			}
			$this->call_command("DB_FREE",array($result));
		} else {
			$sql 		="select * from editor_config where editor_config_client=$this->client_identifier and editor_config_default=1";
			$result = $this->call_command("DB_QUERY",array($sql));
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				if (file_exists($data_files."/editor_".$this->client_identifier."_".$lang."_".$r["editor_config_identifier"].".xml")){
					$file_content = file($data_files."/editor_".$this->client_identifier."_".$lang."_".$r["editor_config_identifier"].".xml");
					$out .= join(" ",$file_content);
				}
			}
			$this->call_command("DB_FREE",array($result));
		}
		if ($display!="none"){
			$return_data = "<module name='".$this->module_name."' display='settings'>";
		}
			$return_data .= "$out";
		if ($display!="none"){
			$default = Array(
				"#ff0000","#00ff00",
				"#0000ff","#FFFF00",
				"#ff9900","#99cc99",
				"#99ff00","#ccffff",
				"#66cccc","#cc00ff",
				"#ff00cc","#993300"
			);
			$sql=  "select * from editor_palette where editor_palette_client = $this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			$index=0;
			while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result)) ){
				$default[$index] = $r["editor_palette_colour"];
				$index++;
			}

		$palette .= "<colours name='colour'>";
		$max = 12;
		if ($index<$max){
			$max=$index;
		}
		for($index=0;$index<$max;$index++){
			$palette .= "<colour value='".$default[$index]."'/>";
		}
		$palette .= "</colours>";
			$return_data .= $palette."</module>";
		}
		$palette = Array();
		$sql=  "select * from editor_palette where editor_palette_client = $this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$palette[count($palette)] = $r["editor_palette_colour"];
		}
		$_SESSION["CLIENT_PALETTE"] = $palette;
		return $return_data;
	}

	/**
	* list_information
	* @param Array requireing these keys ("group_filter", "page", "filter_string");
	* @return String and XML representation of list of results with options
	*/

	function list_information($parameters){  //
		$group_filter=$this->check_parameters($parameters,"group_filter",0);
		$page=$this->check_parameters($parameters,"page",1);
		$filter_string=$this->check_parameters($parameters,"filter_string","");
		$variables = array();

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_list filter parameters",__LINE__,"[$group_filter,$order_filter,$page,$user_filter_login_name]"));
		}

		/**
		*Procude the SQL command that will retrieve the information from the database
				*/
		$where = "";
		$w="";
		$join="";
		if ($filter_string!=""){
			$w = " and (editor_config_label like '%$filter_string%' )";
		}
		if ($group_filter>0){
			$join.="
			left outer join editor_belonging_to_group on editor_belonging_to_group.editor_belongs_group = groups_data.group_identifier
			";
			$where .=" and editor_belonging_to_group.editor_belongs_group = $group_filter";
		}
		$sql = "Select * from editor_config $join where editor_config.editor_config_client=$this->client_identifier $where $w ";

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		/**
		*what functionality options are available on this page
				*/
			$variables["PAGE_BUTTONS"] = Array(
				Array("ADD",$this->module_command."ADD",ADD_NEW)
			);
		if (!$result){
			/**
			* No Records were returned.
			*/

			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			$number_of_records = 0;
			$goto = 0;
			$finish = 0;
			$page = 1;
			$num_pages = 1;
			$start_page = 1;
			$end_page = 1;
		}else{
			/**
			* When some records are returned we will only return the page of results that the
			- user has requested or the first page if the user has not requested any page.
			*/

			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}

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
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<10)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["editor_config_identifier"],
					"ENTRY_BUTTONS" => Array(
						Array("EDIT",$this->module_command."EDIT",EDIT_EXISTING),
						Array("REMOVE",$this->module_command."REMOVE",REMOVE_EXISTING,"IGNORE")
					),
					"attributes"	=> Array(
						Array(ENTRY_TITLE,	$r["editor_config_label"],"TITLE","NO"),
						Array(ENTRY_DATE_CREATION,	$r["editor_date_created"])
					)
				);
			}
		}
		/**
		*retrieve the page spanning information
		*/
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$variables["START_PAGE"]		= $start_page;
		$variables["END_PAGE"]			= $end_page;
		/**
		*retrieve the XML information for building the filter form
		*/
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$variables["FILTER"]			= $this->filter($parameters);
		}else{
			$variables["FILTER"]			= "";
		}
		/**
		*produce the XML representation of the information above.
		*/
		$out = $this->generate_list($variables);
		/**
		*produce the XML representation of the information above.
		*/
		return $out;
	}
	/**
	* filter
	*/
	function filter($parameters){
		$group_filter=$this->check_parameters($parameters,"group_filter",0);
		$filter_string = $this->check_parameters($parameters,"filter_string");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_filter",__LINE__,"[$order_filter,$group_filter,$user_filter_login_name]"));
		}
		$out = "\t\t\t\t<form name=\"user_filter_form\" method=\"get\" label=\"".USER_TITLE_LABEL."\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\" value=\"EDITOR_LIST\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" value=\"1\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"text\" label=\"".SEARCH_KEYWORDS."\" name=\"filter_string\" size=\"20\"><![CDATA[$filter_string]]></input>\n";
		/**
		*retrieve the list of groups and display for selection
		*/
		$group_list = $this->call_command("GROUP_RETRIEVE",array($group_filter,"return"=>2));
		$out .= "\t\t\t\t\t<select name=\"group_filter\" label=\"".USER_GROUP_FILTER."\">\n";
		$out .= "\t\t\t\t\t\t<option value=\"-1\">".USER_DISPLAY_ALL_GROUPS."</option>\n";
		$out .= "$group_list";
		$out .= "\t\t\t\t\t</select>\n";
		$out .= "\t\t\t\t\t<input type=\"submit\" iconify=\"SEARCH\" value=\"".SEARCH_NOW."\"/>\n";
		$out .= "\t\t\t\t</form>";
		/**
		*return the filter XML document
		*/
		return $out;
	}

	function data_form($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$frm_label="Add a new configuration option";
		$cfg_label ="";
		$button_list =Array();
		$config_default = "";
		if ($identifier!=-1){
			$sql = "select * from editor_config left outer join editor_settings on editor_setting_config = editor_config_identifier where editor_config_identifier=$identifier and editor_config_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				if ($cfg_label==""){
					$cfg_label = $r["editor_config_label"];
					$config_default = $r["editor_config_default"];
				}
				$val = $this->check_parameters($r,"editor_setting_button");
				if ($val!=""){
					$button_list[$val]="SELECTED";
				}
			}
		}
		$out ="";
		$out .="<module name='".$this->module_name."' display='form'>";
		$out .="<page_options><button command=\"EDITOR_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\" parameters=\"\" /></page_options>";
		$out .="<form name='editor_configuration' label='$frm_label' method='POST'>";
				$out .= "<input type='hidden' name='command'><![CDATA[EDITOR_SAVE]]></input>";
				$out .= "<input type='hidden' name='identifier'><![CDATA[$identifier]]></input>";
				$out .= "<input type='text' name='config_label' label='".LOCALE_EDITOR_CONFIG_LABEL."'><![CDATA[$cfg_label]]></input>";
				$out .= "<radio name='config_default' label='".LOCALE_EDITOR_DEFAULT."'><option value='1'";
				if ($config_default==1){
					$out .= " selected='true'";
				}
				$out .= "><![CDATA[".LOCALE_YES."]]></option><option value='0'";
				if ($config_default==0){
					$out .= " selected='true'";
				}
				$out .= "><![CDATA[".LOCALE_NO."]]></option></radio>";
				$buttons = $this->extract_list_of_buttons(Array("type_of_value"=>"integer", "buttons"=>$button_list, "include_commands"=>0));
				$cmds	 = $this->extract_list_of_buttons(Array("type_of_value"=>"integer", "buttons"=>$button_list, "include_commands"=>2));
				$out .= "<checkboxes type='vertical' label='".LOCALE_EDITOR_CONFIG_CMDS."' name='config_button_cmd'>$cmds</checkboxes>";
				$out .= "<checkboxes type='horizontal' label='".LOCALE_EDITOR_CONFIG_OPTIONS."' name='config_button_opt'>$buttons</checkboxes>";
				$out .= "	<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
			$out .="</form>";
		$out .="</module>";
		return $out;
	}

	function extract_list_of_buttons($parameters){
		$type_of_value	= $this->check_parameters($parameters,"type_of_value","string");
		$include_commands	= $this->check_parameters($parameters,"include_commands","1");
		/**
		*	0 - no LOCALE_CMD results
		*	1 - LOCALE_CMD are included (default)
		*	2 - only LOCALE_CMD are included
		*/
		$where ="";
		if ($include_commands==0){
			$where =" and editor_button_label not like 'LOCALE_CMD%'";
		} else if ($include_commands==2){
			$where =" and editor_button_label like 'LOCALE_CMD%'";
		}
		$buttons		= $this->check_parameters($parameters,"buttons",Array());
		$sql = "select * from editor_button where editor_button_client = $this->client_identifier $where order by editor_button_label";
		$out="";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY", array($result))){
			$out .= "<option ";
			if ($type_of_value=='string'){
				$out .= " value='".$r["editor_button_id"]."'";
				if (false){
					$out .= " selected='true'";
				}
			} else {
				$out.=" value='".$r["editor_button_identifier"]."'";
				if ($this->check_parameters($buttons,$r["editor_button_identifier"]) == "SELECTED"){
					$out .= " selected='true'";
				}
			}
			$out .= "><![CDATA[".$r["editor_button_label"]."]]></option>";
		}
		return "$out";
		return "<options module='buttons'>".$out."</options>";
	}

	function data_save($parameters){
		$identifier				= $this->check_parameters($parameters,"identifier",-1);
		$editor_config_label	= $this->validate($this->check_parameters($parameters,"config_label"));
		$config_buttons 		= $this->check_parameters($parameters,"config_button_opt",Array());
		$config_commands 		= $this->check_parameters($parameters,"config_button_cmd",Array());
		$config_default			= $this->check_parameters($parameters,"config_default",0);
		$now 					= $this->libertasGetDate("Y/m/d H:i:s");
		if($identifier==-1){
			if ($config_default==1){
				$sql = "update editor_config set editor_config_default = 0 where editor_config_client = $this->client_identifier";
				$this->call_command("DB_QUERY",array($sql));
			}
			$sql = "insert into editor_config (
						editor_config_label, editor_config_client, editor_date_created, editor_created_by_user, editor_config_default
					) values (
						'$editor_config_label', '$this->client_identifier', '$now', '".$_SESSION["SESSION_USER_IDENTIFIER"]."', $config_default
					)";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "select editor_config_identifier from editor_config where editor_config_label='$editor_config_label'and editor_config_client='$this->client_identifier' and editor_date_created='$now' and editor_created_by_user='".$_SESSION["SESSION_USER_IDENTIFIER"]."'";
			$result = $this->call_command("DB_QUERY",array($sql));
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$identifier = $r["editor_config_identifier"];
		} else {
			if ($config_default==1){
				$sql = "update editor_config set editor_config_default = 0 where editor_config_client = $this->client_identifier";
				$this->call_command("DB_QUERY",array($sql));
			}
			$sql = "update editor_config set editor_config_default = $config_default, editor_config_label='$editor_config_label' where editor_config_client='$this->client_identifier' and editor_config_identifier=$identifier";
			$this->call_command("DB_QUERY",array($sql));
		}
		$sql= "delete from editor_settings where editor_setting_config='$identifier' and editor_setting_client = '$this->client_identifier'";
		$this->call_command("DB_QUERY",array($sql));
		for($index=0, $max = count($config_buttons); $index<$max; $index++){
			$sql= "insert into editor_settings (
						editor_setting_config, editor_setting_client, editor_setting_button
					) values (
						'$identifier', '$this->client_identifier', '".$config_buttons[$index]."'
					)";
			$this->call_command("DB_QUERY",array($sql));
		}
		for($index=0, $max = count($config_commands); $index<$max; $index++){
			$sql= "insert into editor_settings (
						editor_setting_config, editor_setting_client, editor_setting_button
					) values (
						'$identifier', '$this->client_identifier', '".$config_commands[$index]."'
					)";
			$this->call_command("DB_QUERY",array($sql));
		}
		$this->cache_editor($identifier);
	}

	function data_remove($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier",-1);
		$sql 		= "select group_identifier, group_label from editor_belonging_to_group
						inner join group_data on group_data.group_identifier = editor_belonging_to_group.editor_belongs_group
						where editor_belongs_config=$identifier and editor_belongs_client=$this->client_identifier";
		$result		= $this->call_command("DB_QUERY",array($sql));
		$total		= 0;
		$editors	= "";
			if ($this->group_admin_access==1){
				$editors 	.= LOCALE_EDITOR_CHANGE_GROUPS;
			} else {
				$editors 	.= LOCALE_EDITOR_SOMEONE_CHANGE_GROUPS;
			}
		while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result))){
			$total 		++;
			if ($this->group_admin_access==1){
				$editors 	.= "<li><a href='admin/index.php?command=GROUP_EDIT&identifier=".$r["group_identifier"]."'>".$r["group_label"]."</a></li>";
			} else {
				$editors 	.= "<li>".$r["group_label"]."</li>";
			}
		}
		if ($total>0){
			//groups using this setting?
			$out ="";
			$out .="<module name='".$this->module_name."' display='form'>";
			$out .="<page_options><button command=\"EDITOR_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\" parameters=\"\" /></page_options>";
			$out .="<form name='editor_configuration_removal' label='".LOCLAE_CURRENTLY_IN_USE."' method='POST'>";
			$out .= "<text><![CDATA[".LOCALE_EDITOR_CURRENT_GROUPS."]]></text>";
			$out .= "<text><![CDATA[$editors]]></text>";
			$out .="</form>";
			$out .="</module>";

		} else {
			//Are you sure?
			$out ="";
			$out .="<module name='".$this->module_name."' display='form'>";
			$out .="<page_options><button command=\"EDITOR_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\" parameters=\"\" /></page_options>";
			$out .="<form name='editor_configuration_removal' label='".LOCALE_PERMENTALLY_REMOVE."' method='POST'>";
			$out .= "<input type='hidden' name='identifier' value='$identifier'/>";
			$out .= "<input type='hidden' name='command' value='EDITOR_REMOVE_CONFIRM'/>";
			$out .= "<text><![CDATA[".LOCALE_REMOVE_EDITOR_CONFIG_OK."]]></text>";
			$out .= "<input iconify=\"YES\" type=\"submit\" value=\"".LOCALE_YES."\"/>";
			$out .="</form>";
			$out .="</module>";
		}
		return $out;
	}

	function embed_group_function($parameters){

		$group		= $this->check_parameters($parameters,"identifier",-1);
		$out 		= "";
		$sql 		= "select * from editor_belonging_to_group where editor_belongs_client=$this->client_identifier and editor_belongs_group = $group";
		$result		= $this->call_command("DB_QUERY",array($sql));
		$id = -1;
		while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result))){
			$id 	= $r["editor_belongs_config"];
		}
		$sql 		= "select * from editor_config where editor_config_client=$this->client_identifier";
		$result		= $this->call_command("DB_QUERY",array($sql));
		$total		= 0;
		$editors	= "";
		while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result)) ){
			$out 	.= "<option value='".$r["editor_config_identifier"]."'";
			if (($id == $r["editor_config_identifier"]) || ($id == -1 && $r["editor_config_default"]==1)){
				$out	.= " selected='true' ";
			}
			$out	.= ">".$r["editor_config_label"]."</option>";
		}
		return "<input type='hidden' name='modules[]' value='EDITOR_SAVE_GROUP'/><select name='editor_group_configuration' label='".LOCALE_WHAT_CONFIG_TO_USE."'>".$out."</select>";
	}

	function embed_group_save($parameters){
		$editor_group_configuration = $this->check_parameters($parameters,"editor_group_configuration",-1);
		$identifier = $this->check_parameters($parameters,"group_identifier",-1);
		$sql = "delete * from editor_belonging_to_group where editor_belongs_client=$this->client_identifier and editor_belongs_group=$identifier";
		$this->call_command("DB_QUERY",array($sql));
		$sql = "insert into editor_belonging_to_group (editor_belongs_client, editor_belongs_group, editor_belongs_config) values ($this->client_identifier, $identifier, $editor_group_configuration)";
		$this->call_command("DB_QUERY",array($sql));
	}

	function data_remove_confirm($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier",-1);
		$sql 		= "delete from editor_config where editor_config_identifier=$identifier and editor_config_client=$this->client_identifier";
		$result		= $this->call_command("DB_QUERY",array($sql));
		$sql 		= "delete from editor_settings where editor_setting_config=$identifier and editor_setting_client=$this->client_identifier";
		$result		= $this->call_command("DB_QUERY",array($sql));
		$out ="";
		$out .="<module name='".$this->module_name."' display='form'>";
		$out .= "<page_options><button command=\"EDITOR_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\" parameters=\"\" /></page_options>";
		$out .= "<form name='editor_configuration_removal' label='".LOCALE_EDITOR_REMOVED_LABEL."' method='POST'>";
		$out .= "<text><![CDATA[".LOCALE_EDITOR_REMOVED_MSG."]]></text>";
		$out .= "</form>";
		$out .="</module>";
		return $out;
	}

	function modify_palette($parameters){
		$default = Array(
			"#ff0000","#00ff00",
			"#0000ff","#FFFF00",
			"#ff9900","#99cc99",
			"#99ff00","#ccffff",
			"#66cccc","#cc00ff",
			"#ff00cc","#993300"
			);
		$sql=  "select * from editor_palette where editor_palette_client = $this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		$index=0;
		while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result)) ){
			$default[$index] = $r["editor_palette_colour"];
			$index++;
		}

		$out ="";
		$out .="<module name='".$this->module_name."' display='form'>";
		$out .= "<page_options><button command=\"EDITOR_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\" parameters=\"\" /></page_options>";
		$out .= "<form name='editor_palette' label='Palette Definition' method='POST'>";
		$out .= "<colours name='colour'>";
		for($index=0;$index<12;$index++){
			$out .= "<colour value='".$default[$index]."'/>";
		}
		$out .= "</colours>";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"EDITOR_PALETTE_SAVE\"/>";
		$out .= "<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
		$out .= "</form>";
		$out .="</module>";
		return $out;
	}

	function palette_save($parameters){
		$sql=  "delete from editor_palette where editor_palette_client = $this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		for($index=1;$index<13;$index++){
			$colour = $this->check_parameters($parameters,"colour_".$index);
			$sql = "insert into editor_palette (editor_palette_client, editor_palette_colour) values ($this->client_identifier,'$colour')";
			$this->call_command("DB_QUERY",array($sql));
		}
		$out ="";
		$out .="<module name='".$this->module_name."' display='form'>";
		$out .= "<page_options><button command=\"EDITOR_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\" parameters=\"\" /></page_options>";
		$out .= "<form name='editor_configuration_removal' label='".LOCALE_EDITOR_PALETTE_SAVED_LABEL."' method='POST'>";
		$out .= "<text><![CDATA[".LOCALE_EDITOR_PALETTE_SAVED_MSG."]]></text>";
		$out .= "</form>";
		$out .="</module>";
		return $out;
	}

	function convert_font_to_span_antispam($parameters){
//		print "".$this->check_parameters($parameters,"string",-1)."";
		$string				= str_replace(Array("</font>","href=\"mailto:"," </acronym>"), Array("</span>","href=\"-/-anti-spam.php?to=","</acronym> "), $this->check_parameters($parameters,"string",-1));
//		print "<h1>striped</h1>\n".$string."";
		$found				= true;
		$pos_of_tag_start	= 0;
		$pos_of_tag_end		= -1;
		while ($found){
			$found=false;
			$pos_of_tag_start = strpos($string,"<font",$pos_of_tag_start);
			$left_hand = substr($string,0,$pos_of_tag_start);
			if ($pos_of_tag_start === false){
				// not found
			} else {
				$pos_of_tag_end = strpos($string,">",$pos_of_tag_start+2);
				if ($pos_of_tag_end===false){
					// should never happen as there is always a finished tag.
				} else {
					$tag_structure = substr($string, $pos_of_tag_start+6, $pos_of_tag_end - ($pos_of_tag_start+6));
					$attribute_list = split("\"", $tag_structure);
					$style="";
					$found=true;
					for($index = 0,$max = count($attribute_list);$index<$max;$index+=2){
						$l = $this->check_parameters($attribute_list, $index);
						$r = $this->check_parameters($attribute_list, $index+1);
						if (strpos($attribute_list[$index],"=")===false){
							$att="";
							$val="";
						} else {
							$att_name = split("=",$attribute_list[$index]);
							$att = trim($att_name[0]);
							$val = $attribute_list[$index+1];
						}
						$attribute=Array($att,$val);
						if (strtolower(trim($attribute[0]))=="size"){
							if (trim($attribute[1])==1){
								$attribute[1]="0.6em";
							}
							if (trim($attribute[1])==2){
								$attribute[1]="0.7em";
							}
							if (trim($attribute[1])==3){
								$attribute[1]="0.8em";
							}
							if (trim($attribute[1])==4){
								$attribute[1]="1em";
							}
							if (trim($attribute[1])==5){
								$attribute[1]="1.1em";
							}
							if (trim($attribute[1])==6){
								$attribute[1]="1.2em";
							}
							if (trim($attribute[1])==7){
								$attribute[1]="1.3em";
							}
							$style.="font-size:".$attribute[1].";";
						}
						if (strtolower(trim($attribute[0]))=="face"){
							$style.="font-family:".$attribute[1].";";
						}
						if (strtolower(trim($attribute[0]))=="color"){
							$style.="color:".$attribute[1].";";
						}
						if (strtolower(trim($attribute[0]))=="style"){
							$style.=$attribute[1].";";
						}
					}
//					print "<p>[$style]</p>";
				}
				$string = $left_hand."<span style='$style'>".substr($string, $pos_of_tag_end+1);
			}

		}
		$string				= str_replace("'","&#39;",$string);
		$string				= $this->convert_slideshow_image_to_object($string);
		return $string;
	}

	function convert_span_to_font($parameters){
		$string = $this->check_parameters($parameters,"string","");
		$string	= $this->convert_slideshow_object_to_image($string);
		$string	= str_replace(Array("</span>","href=\"-/-anti-spam.php?to=","</acronym> ","</a>"), Array(" </font>","href=\"mailto:"," </acronym>&nbsp;"," </a>"),$string);
//		$string	= str_replace(Array("</span>","href=\"-/-anti-spam.php?to=","</acronym> "),Array("</font>","href=\"mailto:"," </acronym>&nbsp;"),$this->check_parameters($parameters,"string",-1));
		$string	= str_replace("<span","<font",$string);
		$string	= str_replace("&#39;","'",$string);
		return $string;
	}

	function restore($parameters){
		$sql = "select * from editor_config where editor_config_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result)) ){
			$this->cache_editor($r["editor_config_identifier"]);
		}
	}

	function configure_modules($parameters){
		$editors = array();
		foreach($this->parent->modules as $index => $moduleEntry){
			if ($this->parent->modules[$index]["admin"]=="1"){
				if ($this->parent->modules[$index]["module"]==null){
					/**
					* load the module
					*/
					if (file_exists(join("/",split("\\\\",dirname(__FILE__)))."/".$this->parent->modules[$index]["file"])){
						$command_eval	 = "require_once \"".dirname(__FILE__)."/".$this->parent->modules[$index]["file"]."\";\n";
						$command_eval	.= "\$this->parent->modules[\$index][\"module\"] = new ".$this->parent->modules[$index]["name"]."(\$this->parent);";
						$this->parent->modules[$index]["loaded"]=1;
//						print $command_eval;
						eval ($command_eval);
					}
				}
//				print $index." ";
				$this->parent->modules[$index]["module"]->load_editors();
				if (count($this->parent->modules[$index]["module"]->editor_configurations)>0){
					$editors[count($editors)] = Array($this->parent->modules[$index]["module"]->module_label,$this->parent->modules[$index]["module"]->editor_configurations,$this->parent->modules[$index]["module"]->module_command);
				}
			}
		}
		$editor_list="";
		$sql ="select * from editor_config where editor_config_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result)) ){
			$editor_list .="<editor identifier='".$r["editor_config_identifier"]."'><![CDATA[".$r["editor_config_label"]."]]></editor>";
		}

		$out ="";
		$out .="<module name='".$this->module_name."' display='form'>";
		$out .= "<page_options><button command=\"EDITOR_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\" parameters=\"\" /></page_options>";
		$out .= "<form name='editor_module_configuration' label='Module Configuration' method='POST'>";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"EDITOR_CONFIGURE_MODULE_SAVE\"/>
					<editors>
						$editor_list
					</editors>
					<page_sections>
		";
		$length = count($editors);
		for($index=0;$index<$length;$index++){
			$out .="<section name='".$editors[$index][0]."' label='".constant($editors[$index][0])."'><configs>";
			foreach($editors[$index][1] as $name => $myArray){
				$out .= "<config locale='$name' name='".constant($name)."' module='".$editors[$index][2]."' identifier='".$this->check_parameters($myArray,"identifier",0)."' ";
				$out .= "status='".$this->check_parameters($myArray,"status")."' ";
				$out .= "locked_to='".$this->check_parameters($myArray,"locked_to")."' ";
				$out .= "/>";
			}
			$out .="</configs></section>";
		}
		$out .= "
						</page_sections>
					<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
		$out .= "</form>";
		$out .="</module>";
		return $out;
	}
	function configure_module_save($parameters){
		$modules		 	= $this->check_parameters($parameters,"modules",Array());
		$list				= $this->check_parameters($parameters,"list",Array());
		$editor_name		= $this->check_parameters($parameters,"editor_name",Array());
		$editor_cfg			= array();
		$length_of_list 	= count($list);
		$length_of_modules	= count($modules);
		$sql = "delete from module_access_to_page where mate_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		if ($length_of_list == $length_of_modules){
			for($index=0;$index<$length_of_modules;$index++){
	//			print $modules[$index] . " " . $list[$index] . " = " . $this->check_parameters($parameters,$list[$index],"") . "<br>";
				$config = $this->check_parameters($parameters,$list[$index],"0")."";
				if ($config=="0"){
					$status = 0;
				} else {
					$status = 1;
				}
				$sql ="insert into module_access_to_editor (
							mate_client,
							mate_name,
							mate_configuration,
							mate_status,
							mate_module
						) values (
							$this->client_identifier,
							'".$editor_name[$index]."',
							$config,
							$status,
							'".$modules[$index]."'
						)";
				$this->call_command("DB_QUERY",array($sql));
			}
			$msg = "<text><![CDATA[Thankyou the configuration has been updated]]></text>";
		} else {
			$msg = "<text><![CDATA[Sorry there was a problem witht he information that was submitted your action has not been completed.]]></text>";
		}
		$out ="";
		$out .="<module name='".$this->module_name."' display='form'>";
		$out .= "<page_options><button command=\"EDITOR_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\" parameters=\"\" /></page_options>";
		$out .= "<form name='editor_module_configuration' label='Module Configuration' method='POST'>";
		$out .= "$msg";
		$out .= "</form>";
		$out .="</module>";
		return $out;
	}
	/**
    * fn convert_slideshow_image_to_object([string])
	*
	* return modified string removes extra parameters from image and places them in an object
    */
	function convert_slideshow_image_to_object($str){
		$pos = strpos($str,"<img id=\"slideshow");
		if($pos===false){
			// do nothing to the string
		} else {
			$endpos = strpos($str,">",$pos);
			/**
            * get everything before this image
            */
			$start = substr($str, 0, $pos);
			/**
            * get everything after this image
            */
			$finish = substr($str, $endpos+1);
			/**
            * extract the Image from the content  tokenise the image to get the details and rebuild image followed by span tag
            */
			$img = substr($str, $pos, $endpos - $pos);
			$extra_parameters="";
			$image_id="";
			$formatted_image ="<img";
			$value = strtok($img,"<> \n\t\"");
			//echo "Word=$tok<br>";
			$img_atts = Array("src","longdesc","alt","width","height","id","class","style","align");
			while ($value) {
				$attribute = strtok("= \"\n\t");
				$value = strtok("\"\n\t");
				if ($attribute=="id"){
					$image_id =$value;
				}

				if ($attribute!="/"){ // is not a closing / on a element xHTML
					if (in_array($attribute,$img_atts)){
						$formatted_image .= " $attribute=\"$value\"";
					}
					if (!in_array($attribute,$img_atts)){
						$extra_parameters .= " $attribute=\"$value\"";
					}
				}
			}
			/* For Slideshow (Added By Muhammad Imran) */

			$paramtitle_str = substr($img,strpos($img,'paramtitle'));
			$paramtitle_quote_arr = split('"',$paramtitle_str);
			$paramtitle_arr = split('::',$paramtitle_quote_arr[1]);
			//$paramtitle_count = strlen($paramtitle_arr[0]);
			$paramtitle = $paramtitle_arr[0];
			
			$formatted_image .=" /><div id=ttl_layer>$paramtitle</div><script type=\"text/javascript\" id=\"js".$image_id."\">// ".trim($extra_parameters)."</script>";
			$str = $start . $formatted_image . $this->convert_slideshow_image_to_object($finish);
		}
		return $str;
	}

	function convert_slideshow_object_to_image($str){
//	print $str."\n\n\n\n";
		$pos = strpos($str,"<img id=\"slideshow");
		if($pos===false){
			// do nothing to the string
		} else {
			$endpos = strpos($str,"</script>",$pos);
			if($endpos===false){
				$endpos = strpos($str,"</span>",$pos);
				if($endpos===false){
					// then I dont know
				} else {
					/**
	                * can't find corresponding script tag look for previous version which used a span tag
	                */
					/**
		            * get everything before this image/span combo
		            */
					$start = substr($str, 0, $pos);
					/**
		            * get everything after this image/span combo
		            */
					$finish = substr($str, $endpos+7);
					/**
		            * extract the Image from the content, then get the scripttag and the contents of it is extra parameters for iamge
					* while in the editor
		            */
					$imgspan = substr($str, $pos, $endpos - $pos);
					$span_pos = strpos($imgspan,"/><span");
					$formatted_image = substr($imgspan,0,$span_pos - 1);
					$rest = substr($imgspan,$span_pos+10);
					$extra_parameters="";
					$image_id="";
					$formatted_image .= " ".substr($rest,strpos($rest,">")+1,strlen($rest)-7);
					$formatted_image .=" />";
					$str = $start . $formatted_image . $this->convert_slideshow_object_to_image($finish);
				}
			} else {
				/**
	            * get everything before this image/span combo
	            */
				$start = substr($str, 0, $pos);
				/**
	            * get everything after this image/span combo
	            */
				$finish = substr($str, $endpos+9);
				/**
	            * extract the Image from the content, then get the scripttag and the contents of it is extra parameters for iamge
				* while in the editor
	            */
				
				$imgspan = substr($str, $pos, $endpos - $pos);
				
				/* For Slideshow (Added By Muhammad Imran) */
//				paramtitle="Test pic2::Test pic 3::Meta Tucker, Clayton Love and Marjorie Baker::Test Pic" 
				
				$paramtitle_str = substr($imgspan,strpos($imgspan,'paramtitle'));
				$paramtitle_quote_arr = split('"',$paramtitle_str);
				$paramtitle_arr = split('::',$paramtitle_quote_arr[1]);
				$paramtitle_count = strlen($paramtitle_arr[0]);
				$paramtitle = $paramtitle_arr[0];
				
				
				$span_pos = strpos($imgspan,"/><div id=ttl_layer>$paramtitle</div><script");
				$formatted_image = substr($imgspan,0,$span_pos - 1);
//				$rest = substr($imgspan,$span_pos+10);
				$rest = substr($imgspan,$span_pos+36+$paramtitle_count);
				$extra_parameters="";
				$image_id="";
				$formatted_image .= " ".substr($rest,strpos($rest,">")+1,strlen($rest)-9); // was +1 now +3 added two slashed for comment
				$formatted_image .=" />";
				$str = $start . $formatted_image . $this->convert_slideshow_object_to_image($finish);
			}
		}
		return $str;
	}

	function data_to_html($parameters){
		$string = html_entity_decode($this->check_parameters($parameters,"string"));
		return htmlentities(
			stripslashes(
				$this->add_root_dir_to_paths(
					$this->convert_span_to_font(
						Array("string"=>$string)
					)
				)
			)
		);
	}
	function html_to_data($parameters){

	}
    /**
    * @access private;
    * generate a button for the editor
    * @param string the icon to use
    * @param string the mouseover label text for the button
    * @param string the Javascript function to call
    * @param string extra parameters required???
	*
	* @return String returns a string containing a HTML IMG tag with all required settings set
    */
    function gen_button($img,$alt,$fn,$exParam =""){
        $editor ="[[editor]]";
        $out="";
        if ($img=='startRow'){
            $out .="";
    	} else if ($img=='endRow'){
            $out .="<br />";
    	} else if ($img=='tb_vertical_separator') {
    		$out .="<img alt='' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_vertical_separator.gif' width='3' height='24'/>";
    	} else if ($img=='select_paragraph') {
    		$out .= "<select id='chooseHeading' onChange=\"LIBERTAS_change_paragraph_click('[[editor]]', this);\" style='width:100px;font-size:0.8em;margin-bottom:5px'>";
    		$out .= "<option value='P'>Headings</option>";
    		$out .= "<option value='P'>Normal</option>";
            for($i=1; $i<7;$i++){
            	    $out.="<option value='H".$i."'>Heading $i</option>";
            }
    		$out .= "</select>";
    	} else if ($img=='select_font_face') {
    		$out .= "<select id='chooseFont' onChange=\"LIBERTAS_set_font_face('[[editor]]', this);\" style='width:100px;font-size:0.8em;margin-bottom:5px'>";
  			$fonts = Array(
                Array("Font name",		""),
                Array("Default", 		""),
    		    Array("Times Roman",	"Times Roman"),
    		    Array("Arial", 		    "Arial"),
    		    Array("Arial Black",	"Arial Black"),
    		    Array("Arial Narrow",	"Arial Narrow"),
    		    Array("Georgia", 		"Georgia"),
    		    Array("Terminal", 		"Terminal"),
    		    Array("Verdana", 		"Verdana")
            );
            for($i=1; $i<count($fonts);$i++){
                $out.="<option value='".$fonts[$i][1]."'>".$fonts[$i][0]."</option>";
            }
    		$out .= "</select>";
        } else if ($img=='select_font_size') {
            $out .= "<select id='chooseFontSize' onChange=\"LIBERTAS_set_font_size('[[editor]]', this);\" style='width:100px;font-size:0.8em;margin-bottom:5px'>";
  			$fonts = Array(
			    Array("Font Size",		""),
    		    Array("Default",		"3"),
    		    Array("x-small",		"1"),
    		    Array("small",			"2"),
    		    Array("normal",			"3"),
    		    Array("big",			"4"),
                Array("bigger",			"5"),
    		    Array("large",			"6"),
    		    Array("x-large",		"7")
            );
            for($i=1; $i<count($fonts);$i++){
                $out.="<option value='".$fonts[$i][1]."'>".$fonts[$i][0]."</option>";
            }
    		$out .= "</select>";
        } else if ($img=='set_zoom') {
            $size = Array(
                Array("Zoom",		""),
    			Array("Default",	"100"),
    			Array("75%",		"75"),
    			Array("100%",		"100"),
    			Array("125%",		"125"),
    			Array("150%",		"150"),
    			Array("200%",		"200"),
    			Array("300%",		"300")
            );
            $out .= "<select id='chooseZoom' onChange=\"LIBERTAS_setZoom('[[editor]]', this);\" style='width:100px;font-size:0.8em;margin-bottom:5px'>";
            for($i=1; $i<count($size);$i++){
                $out.="<option value='".$size[$i][1]."'>".$size[$i][0]."</option>";
            }
    		$out .= "</select>";
        } else {
            if ($exParam.''!=''){
    			$exParam = ",'".$exParam."'";
    		} else {
    			$exParam ='';
    		}
    		if ($img=='tb_dropdown'){
    			$image_width = 11;
    		} else {
    			$image_width = 24;
    		}
        	$out .= "<img class='LIBERTAS_default_tb_out' unselectable='on' id='LIBERTAS_".$editor."_".$img."' name='LIBERTAS_".$editor."_".$img."' alt='".$alt."' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/".$img.".gif' width='".$image_width."' height='24' onclick=\"".$fn."('".$editor."',this ". $exParam .")\" onmouseover='LIBERTAS_default_bt_over(this);' onmouseout='LIBERTAS_default_bt_out(this);' onmousedown='LIBERTAS_default_bt_down(this);' onmouseup='LIBERTAS_default_bt_up(this);'>";
    	}
        return $out;
    }
}
?>