<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.embedScript.php
* @date 12 Feb 2004
*/
/**
* This module is the administration module for embedding 3rd party scripts in the web site
* 
* Caching 
* 	
* 
* AUTO CLEAN
* 0. Nothing
* 1. Remove fonts
* 2. Remove fonts and styles
* 3. Remove fonts, styles and classes
* 
*/
class embedscript_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name_label			= "Embed 3rd party Script Module (Administration)";
	var $module_name				= "embedscript_admin";
	var $module_admin				= "1";
	var $module_command				= "EMBEDSCRIPTADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "EMBEDSCRIPTADMIN_";
	var $module_label				= "MANAGEMENT_3RD_PARTY";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.13 $';
	var $module_creation 			= "16/07/2004";
	var $searched					= 0;
	var $loadedcat					= Array();
	/**
	*  Management Menu entries
	*/
	
	var $module_admin_options 		= array(
		array("EMBEDSCRIPTADMIN_LIST", "Embedded Script","","Content Manage/External Content")
	);
	
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access = array(
		array("EMBEDSCRIPTADMIN_ALL",			"COMPLETE_ACCESS"),
		array("EMBEDSCRIPTADMIN_LIST_CREATOR",	"ACCESS_LEVEL_LIST_AUTHOR"),  	// this will allow the user to add a new category to the system
		array("EMBEDSCRIPTADMIN_CREATOR",		"ACCESS_LEVEL_AUTHOR"), 	 	// this will allow the user to add a new category to the system
		array("EMBEDSCRIPTADMIN_EDITOR",		"ACCESS_LEVEL_EDITOR"), 	 	// this user role will allow the user to edit and remove categories.
		array("EMBEDSCRIPTADMIN_APPROVER",		"ACCESS_LEVEL_APPROVER") 		// this will allow the user to 
	);
	
	/**
	*  Channel options
	*/
	var $module_display_options 	= array(
		array("EMBEDSCRIPT_DISPLAY",	"LOCALE_DISPLAY_EMBEDSCRIPT")
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
	);
	
	/**
	*  filter options
	*/
	var $display_options			= array();
	
	/**
	*  Access options php 5 will allow these to become private variables.
	*/
	var $admin_access				= 0;
	var $author_admin_access		= 0;
	var $editor_admin_access		= 0;
	var $approve_admin_access		= 0;
	var $add_embedscripts_lists		= 0;
	var $install_access				= 0;
	/**
	*  Class Methods
	*/
	
	function command($user_command, $parameter_list=array()){
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
		if (strpos($user_command, $this->module_command)===0){
			if ($user_command==$this->module_command."TEST"){
				$this->test_temp();
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
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Create table function allow access if in install mode
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Administration Module commands
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($this->admin_access==1){
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- List Category Management Access
				- -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- this functionality will allow you to modify the category list details
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($this->add_embedscript_lists){
					if ($user_command==$this->module_command."LIST"){
						return $this->embedscript_list($parameter_list);
					}
					if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
						return $this->embedscript_modify($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE"){
						$this->embedscript_removal($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
					if ($user_command==$this->module_command."SAVE"){
						$this->embedscript_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
					
				}
			}
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                E M B E D S C R I P T   S E T U P   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- 
	- 
	*
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
		$this->load_locale("embedscript_admin");
		/**
		* define the list of Editors in this module and define them as empty
		*/
		$this->editor_configurations = Array(
			"ENTRY_CONFIRM_SCREEN" => $this->generate_default_editor()
		);
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		$this->admin_access				= 0;
		$this->author_admin_access		= 0;
		$this->editor_admin_access		= 0;
		$this->approve_admin_access		= 0;
		$this->add_embedscript_lists	= 0;
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		/**
		* define the admin access that this user has.
		*/
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
					("EMBEDSCRIPTADMIN_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("EMBEDSCRIPTADMIN_CREATOR"==$access[$index])
				){
					$this->author_admin_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("EMBEDSCRIPTADMIN_ALL"==$access[$index]) ||
					("EMBEDSCRIPTADMIN_LIST_CREATOR"==$access[$index])
				){
					$this->add_embedscript_lists=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("EMBEDSCRIPTADMIN_ALL"==$access[$index]) ||
					("EMBEDSCRIPTADMIN_EDITOR"==$access[$index])
				){
					$this->editor_admin_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("EMBEDSCRIPTADMIN_ALL"==$access[$index]) ||
					("EMBEDSCRIPTADMIN_APPROVER"==$access[$index])
				){
					$this->approve_admin_access=0;
				}
			}
		}
		if (($this->approve_admin_access || $this->editor_admin_access || $this->add_embedscript_lists || $this->author_admin_access ) && (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files"))){
			$this->list_access=1;
			$this->admin_access=1;
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
		* Table structure for table ''
		*/
		$fields = array(
			array("es_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("es_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("es_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("es_date_created"		,"datetime"					,"NOT NULL"	,"default '0000-00-00 00:00:00'"),
			array("es_status"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("es_menu"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("es_base_uri"			,"text"						,"NOT NULL"	,"default ''"),
			array("es_cache"			,"unsigned integer"			,"NOT NULL"	,"default ''"),
			array("es_auto_clean"		,"unsigned small integer"	,"NOT NULL"	,"default ''"),
			array("es_last_cached"		,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);
		$primary ="es_identifier";
		$tables[count($tables)] = array("embedscript_list", $fields, $primary);

		return $tables;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                         E M B E D S C R I P T   M A N A G E R   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function embedscript_list($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		$status_filter	= $this->check_parameters($parameters,"status_filter",-1);
		$sql = "select * from embedscript_list where es_client = $this->client_identifier";
		$out = "";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$variables["FILTER"]			= "";//$this->filter($parameters,"EMBEDSCRIPTADMIN_LIST");
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
			
			$variables["PAGE_BUTTONS"] = Array();
				$variables["PAGE_BUTTONS"][0] = Array("CANCEL","EMBEDSCRIPTADMIN_LIST", "Cancel");
				$variables["PAGE_BUTTONS"][1] = Array("ADD","EMBEDSCRIPTADMIN_ADD&amp;list_id=".$identifier, ADD_NEW);
			if ($this->add_embedscript_lists == 1){
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
			$lockable=1;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["es_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		$this->check_parameters($r,"es_label",""),"TITLE")
					)
				);
				if ($this->author_admin_access || $this->editor_admin_access){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT","EMBEDSCRIPTADMIN_EDIT",EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE","EMBEDSCRIPTADMIN_REMOVE",REMOVE_EXISTING);
				}
			}
			$this->page_size = $prev;
			
			return $this->generate_list($variables);
		}
	}
	
	function embedscript_modify($parameters){
		$label				= "";
		$es_status			= 0;
		$es_label			= "";
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$all_locations		= 0;
		$set_inheritance	= 0;
		$menu_locations		= "";
		$es_base_uri		= "";
		$es_cache			= 0;
		$es_auto_clean		= 0;
		$es_menu			=-1;
		if ($identifier!=-1){
			$sql = "select * from embedscript_list where es_client=$this->client_identifier and es_identifier=$identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$es_label		= $r["es_label"];
            	$es_status		= $r["es_status"];
				$es_menu		= $r["es_menu"];
				$es_base_uri	= $r["es_base_uri"];
				$es_cache		= $r["es_cache"];
				$es_auto_clean	= $r["es_auto_clean"];
            }
            $this->call_command("DB_FREE",Array($result));
		}
		$out  = "<module name=\"$this->module_name\" display=\"form\">";
		$out .= "<page_options>";
		$out .= 	$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL", $this->module_command."LIST", LOCALE_CANCEL));
		$out .= "</page_options>";
		$out .= "<form name=\"".$this->module_name."_form\" method=\"post\" label=\"$label\">";
		$out .= "	<input type=\"hidden\" name=\"identifier\" value=\"".$identifier."\"/>";
		$out .= "	<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."SAVE\"/>";
		$out .= "	<page_sections>";
		$out .= "		<section label=\"Definition of External Script\">";
		$out .= "			<input type=\"text\" label=\"Label\" name=\"es_label\" required=\"YES\"><![CDATA[$es_label]]></input>";
		$out .= "			<input type=\"text\" label=\"Script URL\" name=\"es_base_uri\" required=\"YES\"><![CDATA[$es_base_uri]]></input>";
		$data =  $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($es_menu));
		$out .= "	<select label=\"".LOCALE_DEFAULT_MENU_MSG."\" name=\"es_menu\">$data</select>";
		$out .= "<select label=\"".LOCALE_STATUS."\" name=\"es_status\">";
		$out .= "<option value=\"0\">".STATUS_NOT_LIVE."</option>";
		$out .= "<option value=\"1\"";
		if ($es_status==1){
			$out .=" selected=\"true\"";
		}
		$out .= ">".STATUS_LIVE."</option>";
		$out .= "</select>";

		$out .= "<select label=\"".LOCALE_CACHE."\" name=\"es_cache\">";
		$hr = (3600);
		$levels_of_cache = Array(
			Array(0,			LOCALE_NO_CACHE),
			Array(($hr*1),		"1 hour"),
			Array(($hr*2),		"2 hour"),
			Array(($hr*4),		"4 hour"),
			Array(($hr*8),		"8 hour"),
			Array(($hr*12),		"12 hour"),
			Array(($hr*24),		"1 day"),
			Array(($hr*24*7),	"1 week"),
			Array(($hr*24*30),	"1 month (30 Days)"),
			Array(($hr*24*60),	"2 months (60 Days)")
		);
		$m = count($levels_of_cache);
		for ($i=0; $i<$m; $i++){
			$out .= "<option value=\"".$levels_of_cache[$i][0]."\"";
			if ($es_cache==$levels_of_cache[$i][0]){
				$out .=" selected=\"true\"";
			}
			$out .= ">".$levels_of_cache[$i][1]."</option>";
		}
		$out .= "</select>";

		$out .= "<select label=\"".LOCALE_AUTO_CLEAN."\" name=\"es_auto_clean\">";
		$levels_of_clean = Array(
			Array(0, LOCALE_NO_CLEAN),
			Array(1, LOCALE_NO_FONT),
			Array(2, LOCALE_NO_STYLE),
			Array(3, LOCALE_NO_CLASS)
		);
		$m = count($levels_of_clean);
		for ($i=0; $i<$m; $i++){
			$out .= "<option value=\"".$levels_of_clean[$i][0]."\"";
			if ($es_auto_clean == $levels_of_clean[$i][0]){
				$out .=" selected=\"true\"";
			}
			$out .= ">".$levels_of_clean[$i][1]."</option>";
		}
		$out .= "</select>";

		$web_containers = split("~----~",$this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>$this->webContainer, "identifier"=>$identifier)));
		if ($web_containers[0]!=""){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}
		$out .= "		</section>";
		$out .= "	</page_sections>";
		$out .= "	<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	function embedscript_save($parameters){
		$identifier 			= $this->check_parameters($parameters	,"identifier",0);
		$es_status			 	= $this->check_parameters($parameters	,"es_status",0);
		$es_label	 			= $this->validate($this->check_parameters($parameters	,"es_label",0));
		$es_menu				= $this->check_parameters($parameters	,"es_menu");
		$es_base_uri			= $this->check_parameters($parameters	,"es_base_uri");
		$es_auto_clean			= $this->check_parameters($parameters	,"es_auto_clean",0);
		$es_cache				= $this->check_parameters($parameters	,"es_cache",0);
		//web objects
		$currentlyhave			= $this->check_parameters($parameters	,"currentlyhave");
		$replacelist			= $this->check_parameters($parameters	,"web_containers",Array());
		//print_r($parameters);
		if($identifier==-1){
			$now = $this->libertasGetDate("Y/m/d H:i:s");
			$sql = "insert into embedscript_list (es_client, es_label, es_status, es_date_created, es_menu, es_base_uri, es_cache, es_auto_clean, es_last_cached) values ($this->client_identifier, '$es_label', '$es_status', '$now', '$es_menu', '$es_base_uri', '$es_cache', '$es_auto_clean', 0)";
			//print $sql;
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "select es_identifier from embedscript_list where 
						es_label			= '$es_label' and 
						es_status			= $es_status and  
						es_client 			= $this->client_identifier and 
						es_date_created		= '$now' and  
						es_base_uri			= '$es_base_uri' and 
						es_cache			= '$es_cache' and 
						es_auto_clean		= '$es_auto_clean'
					";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$identifier = $r["es_identifier"];
			}
			$this->call_command("DB_FREE",Array($result));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE", Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $es_label,
					"wo_command"	=> "EMBEDSCRIPT_DISPLAY",
					"cmd"			=> "ADD",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);	
		} else {
			$sql = "update embedscript_list set 
					es_label		= '$es_label',
					es_status		= '$es_status',
					es_menu			= '$es_menu',
					es_base_uri		= '$es_base_uri',
					es_cache		= '$es_cache',
					es_auto_clean	= '$es_auto_clean'
				 where es_client= $this->client_identifier and es_identifier=$identifier";
			$this->call_command("DB_QUERY",array($sql));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $es_label,
					"wo_command"	=> "EMBEDSCRIPT_DISPLAY",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
		}
		
		$sql = "select distinct es_menu from embedscript_list 
			inner join display_data on display_menu = es_menu and display_command='EMBEDSCRIPT_DISPLAY'
		where es_client=$this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
       	$keep_list  = "";
		$found=0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if ($keep_list!=""){
        		$keep_list  .= ", ";
			}
        	$keep_list  .= $r["es_menu"];
			if($r["es_menu"]==$es_menu){
				$found=1;
			}
        }
        $this->call_command("DB_FREE",Array($result));
		if ($keep_list!=""){
			$sql = "delete from display_data where display_command='EMBEDSCRIPT_DISPLAY' and display_menu not in ($keep_list) and display_client=$this->client_identifier";
			$this->call_command("DB_QUERY",Array($sql));
		}
		if($found==0){
			$sql= "insert into display_data (display_menu, display_command, display_client) values ($es_menu, 'EMBEDSCRIPT_DISPLAY', $this->client_identifier)";
			$this->call_command("DB_QUERY",Array($sql));
		}
	}
	
	function retrieve(){
		$cu      = curl_init();
		$q_string = "command=PRESENTATION_SEARCH&advanced=0&associated_list=&page=1&search=0&page_search=e"  ;
		curl_setopt($cu, CURLOPT_URL,        "http://professor/~magherafelt_council/search/index.php");
		curl_setopt($cu, CURLOPT_SSL_VERIFYHOST,  2);
		curl_setopt($cu, CURLOPT_USERAGENT,      "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
		curl_setopt($cu, CURLOPT_POST,        6);
		curl_setopt($cu, CURLOPT_POSTFIELDS,    $q_string);
		curl_setopt($cu, CURLOPT_RETURNTRANSFER,  1);
		curl_setopt($cu, CURLOPT_TIMEOUT,      100);
		$output		= curl_exec($cu);
		$succeeded	= curl_errno($cu) == 0 ? true : false;
		curl_close($cu);
	}
	
	function embedscript_removal($parameters){
		$identifier  = $this->check_parameters($parameters,"identifier");
		if ($identifier!=-1){
			$sql ="delete from embedscript_list where es_identifier = $identifier and es_client = $this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"DB_Values",__LINE__,"[$sql]"));}
			$this->call_command("WEBOBJECTS_MANAGE_MODULE", 
				Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"cmd"			=> "REMOVE"
				)
			);	
		}
	}
}

?>