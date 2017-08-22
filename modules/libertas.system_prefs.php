<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.mirror.php
* @date 09 Oct 2002
*/
/**
* This module is to allow the system administrator to manage the system preferences for different modules
*/
class system_prefs extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__ADMIN__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name				= "system_prefs";
	var $module_name_label			= "System Preference Management Tool";
	var $module_label				= "MANAGEMENT_SYS_PREF";
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_admin				= "1";
	var $module_debug				= false;
	var $module_creation			= "22/01/2003";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:14 $';
	var $module_version 			= '$Revision: 1.19 $';
	var $module_command				= "SYSPREFS_"; 		// all commands specifically for this module will start with this token
	var $module_display_options		= Array();
//	var $module_admin_options 		= array();
	var $module_admin_user_access	= array(
		array("SYSPREFS_ALL"			, "COMPLETE_ACCESS","")
	);
	var $module_admin_options		= array();
	
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
			if ($user_command==$this->module_command."DEBUG_ON"){
				$this->module_debug=true;
			}
			if ($user_command==$this->module_command."GET_PREFS"){
				return $this->preferences;
			}
			if ($user_command==$this->module_command."CHECKPREFS"){
				return $this->check_preferences($parameter_list);
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
			if ($user_command==$this->module_command."GET_AUTHOR"){
				return $this->get_module_author();
			}
			if ($user_command==$this->module_command."GET_CREATION"){
				return $this->get_module_creation();
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
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			if ($user_command==$this->module_command."EXTRACT_ALL_SETTINGS"){
				return $this->extract_all_settings($parameter_list);
			}
			
			/*
				module specific commands
			*/
			if ($this->parent->module_type == "admin"){
				if ($user_command==$this->module_command."UPDATE_DEBUG"){
					$this->debug_update($parameter_list);
					$user_command=$this->module_command."DEBUG_ADMIN";
				}
				if ($user_command==$this->module_command."DEBUG_ADMIN"){
					return $this->debug_admin($parameter_list);
				}
				if ($user_command==$this->module_command."UPDATE_SYSTEM_PREFS"){
					return $this->system_admin_save($parameter_list);
				}
				if ($user_command==$this->module_command."SYSTEM_ADMIN"){
					return $this->system_admin($parameter_list);
				}
				if ($user_command==$this->module_command."SPLASH"){
					return $this->splash($parameter_list);
				}
				if ($user_command==$this->module_command."SYSTEM_METADATA_SAVE"){
					return $this->metadata_admin_save($parameter_list);
				}
				if ($user_command==$this->module_command."SYSTEM_METADATA"){
					return $this->metadata_admin($parameter_list);
				}
			}
			if ($user_command==$this->module_command."EXTRACT_SYSTEM_PREFERENCE"){
				return $this->extract_system_preference($parameter_list);
			}
			if ($user_command==$this->module_command."CREATE_NEW_CLIENT_DETAILS"){
				return $this->create_client_details($parameter_list);
			}
			if ($user_command==$this->module_command."LOAD_SYSTEM_PREFERENCE"){
				return $this->load_system_prefs($parameter_list);
			}
			
		}else{
			return ""; // wrong command sent to system
		}
	}
	/**
	* call the initialisation function only when this module is created
	*/
	function create_client_details($parameters){
		$client_identifier = $this->check_parameters($parameters,"client_identifier",-1);
		/**
		* insert available_fields data
		*/
		$max = count($this->preferences);
		for ($i=0;$i<$max;$i++){
			$ok=0;
			if (($this->parent->server[LICENCE_TYPE]==ECMS)){
				$ok =1;
			} else if (($this->parent->server[LICENCE_TYPE]==MECM)){
				if ($this->preferences[$i][5]=="ALL" || $this->preferences[$i][5]=="MECM"){
					$ok =1;
				}
			} else{
				if ($this->preferences[$i][5]=="ALL"){
					$ok =1;
				}
			} 
			if ($ok==1){
				$sql ="insert into system_preferences (system_preference_name, system_preference_label, system_preference_client, system_preference_value, system_preference_options, system_preference_module) 
					values 
					('".$this->preferences[$i][0]."', 'LOCALE_".strtoupper($this->preferences[$i][0])."', $client_identifier, '".$this->preferences[$i][2]."', '".$this->preferences[$i][3]."', '".$this->preferences[$i][4]."');";
				$this->parent->db_pointer->database_query($sql);
			}
		}
//		$this->call_command("DB_QUERY",Array("INSERT INTO system_metadata_defaults VALUES(\"1\", \"1\", \"Children\r\nExpert\r\nUK Citizens\r\nScotish Citizens\r\nN.Ireland Citizens\r\nWelish Citizens\", \"Heart disease/Stroke\r\nCrime/crime reduction; Young People\r\nCrime/crime reduction; Adults\r\nCrime/crime reduction; OAPs\", \"Act of Parliament\r\nAdvertisement\r\nAgenda\r\nArticle\r\nAnnual report\r\nAtlas\r\nBriefing note\r\nBudget\r\nCall for expressions of interest\r\nCall for tenders\r\nCall for papers\", \"www.libertas-solutions.com/copyright/index.php\", \"libertas-Solutions, 130 Markethill Rd, Armagh, N.Ireland, email info@libertas-solutions.com\");"));
		
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- check preferences
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function check_preferences($parameters){
		$data = $this->call_command("ENGINE_RETRIEVE", Array("GET_PREFS"));
		$wipe = $this->check_parameters($parameters,"wipe",0);
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - get the list of existing preference tags
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */
		if ($wipe ==1){
			$sql = "delete from system_preferences where system_preference_client = $this->client_identifier";
			$this->parent->db_pointer->database_query($sql);
		}
		$sql = "Select * from system_preferences where system_preference_client = $this->client_identifier";
		$result = $this->parent->db_pointer->database_query($sql);
		$settings=Array();
		while($r= $this->parent->db_pointer->database_fetch_array($result)){
			$settings[count($settings)] = $r["system_preference_name"];
		}
		$this->parent->db_pointer->database_free_result($result);
		$zmax = count($data);
		for ($z=0;$z<$zmax;$z++){
			if (is_array($data[$z][1])){
				$max = count($data[$z][1]);
				for ($i=0;$i<$max;$i++){
					$ok=0;
					if (!in_array($data[$z][1][$i][0],$settings)){
						if (($this->parent->server[LICENCE_TYPE]==ECMS)){
							$ok =1;
						} else if (($this->parent->server[LICENCE_TYPE]==MECM)){
							if ($data[$z][1][$i][5]=="ALL" || $data[$z][1][$i][5]=="MECM"){
								$ok =1;
							}
						} else{
							if ($data[$z][1][$i][5]=="ALL"){
								$ok =1;
							}
						} 
						if ($ok==1){
							$sql ="insert into system_preferences (system_preference_name, system_preference_label, system_preference_client, system_preference_value, system_preference_options, system_preference_module) 
								values 
								('".$data[$z][1][$i][0]."', '".$data[$z][1][$i][1]."', $this->client_identifier, '".$data[$z][1][$i][2]."', '".$data[$z][1][$i][3]."', '".$data[$z][1][$i][4]."');";
							$this->parent->db_pointer->database_query($sql);
						}
					}
				}
			}
		}
	}
	
	function initialise(){
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		$this->preferences = Array(
			Array('sp_time_out_minutes'			,'LOCALE_SP_TIME_OUT_MINUTES'						,'180'	, '5:10:15:30:60:120:180'	, "SYSPREFS_",	"ALL"),
			Array('sp_page_size'				,'LOCALE_SP_PAGE_SIZE'								,'10'	, '5:10:20:50:100'			, "SYSPREFS_",	"ALL"),
			Array('sp_combo_year'				,'LOCALE_SP_COMBO_YEAR'								,'1990'	, 'TEXT'					, "SYSPREFS_",	"ALL"),
			Array('sp_compression'				,'LOCALE_SP_COMPRESSION'							,'Yes'	, 'Yes:No'					, "SYSPREFS_",	"ALL"),
			Array('sp_privacy'					,'LOCALE_SP_PRIVACY'								,''		, 'TEXT'					, "SYSPREFS_",	"ALL"),
			Array('sp_auto_summarise'			,'LOCALE_SP_AUTO_SUMMARISE'							,'Yes'	, 'Yes:No'					, "SYSPREFS_",	"ALL"),
			Array('sp_edit_summary'				,'LOCALE_SP_EDIT_SUMMARY'							,'No'	, 'Yes:No'					, "SYSPREFS_",	"ALL"),
			Array("sp_use_antispam"				,"LOCALE_SP_USE_ANTISPAM"							,"Yes"	, "Yes:No"					, "SYSPREFS_",	"ALL"),
			Array("sp_page_title_is_caps"		,"LOCALE_SP_PAGE_TITLE_IS_CAPS"						,"No"	, "Yes:No"					, "SYSPREFS_", 	"ALL")
		);
/*
		if (($this->parent->server[LICENCE_TYPE]==ECMS)){
			$this->preferences[count($this->preferences)] = Array('sp_comment_require_approval', 'Should Comments be approved before being published?'		, 'Yes'	, 'Yes:No'	, "SYSPREFS_",	"ECMS");
			$this->preferences[count($this->preferences)] = Array('sp_comments_open', 'Can anonymous users on the site publish a comment'					, 'Yes'	, 'Yes:No'	, "SYSPREFS_",	"ECMS");
			$this->preferences[count($this->preferences)] = Array('sp_rss_downloads', 'How many download attempts should be allowed on external rss feeds'	, '10'	, 'TEXT'	, "SYSPREFS_",	"ECMS");
		}
*/
/*
			Array('sp_log_page_searchs'		,'Do you wish to log the page searches (Yes/No)?'							,'Yes'	, 'Yes:No'					, "PAGE_",		"ALL"),
//			Array('sp_log_forum_searchs'	,'Do you wish to log the forum searches (Yes/No)?'							,'Yes'	, 'Yes:No'					, "FORUM_",		"ALL"),

*/
		$sql = "SELECT distinct system_preference_module FROM `system_preferences` where system_preference_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		
		$this->module_admin_options = Array();
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$this->load_locale("sp_".strtolower($r["system_preference_module"]));
        	$this->module_admin_options[count($this->module_admin_options)] = Array("SYSPREFS_SYSTEM_ADMIN&amp;module=".$r["system_preference_module"], $this->get_Constant("LOCALE_MENU_SETTING_".$r["system_preference_module"]),"","Preferences/General Settings");
        }
		$this->module_admin_options[count($this->module_admin_options)] =array("CLIENT_FORM","LOCALE_CLIENT_DETAILS","","Preferences/General Settings");
		$this->module_admin_options[count($this->module_admin_options)] =array("CLIENT_PARK_DOMAINS","LOCALE_PARK_DOMAINS","","Preferences/General Settings");
		$this->module_admin_options[count($this->module_admin_options)] =array("PAGE_MANAGE_IGNORE_LIST", "MANAGE_PAGE_IGNORE_LIST","PAGE_AUTHOR|PAGE_APPROVER|PAGE_PUBLISHER|PAGE_ARCHIVER|PAGE_DISCUSSION|PAGE_FORCE_UNLOCK","Preferences/Ignore lists");


//		print LOCALE_SP_FORCE_DOWNLOAD;
//		print_r($this->module_admin_options);

        $this->parent->db_pointer->database_free_result($result);
		/**
		* define some access functionality
		*/
		if ($this->parent->debug_access=="enable_debug_options"){
			$this->module_admin_options[count($this->module_admin_options)] = array($this->module_command."DEBUG_ADMIN","LOCALE_DEBUG_MANAGE","","Preferences/General Settings");
		}
		if (($this->parent->server[LICENCE_TYPE]==ECMS)){
			$this->module_admin_options[count($this->module_admin_options)] = array($this->module_command."SYSTEM_METADATA","Meta Data Settings","","Preferences/General Settings");
		}
	}


	/**
	* a function to return a list of records for this module
	*/
	function debug_admin($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,""));
		}
		$session_user_cmds		= split(",",$this->check_parameters($_SESSION,"SESSION_USER_COMMANDS"));
		$session_display_time	= $this->check_parameters($_SESSION,"SESSION_DISPLAY_TIME");
		$session_xml_enabled 	= $this->check_parameters($_SESSION,"SESSION_DEBUG_XML_ENABLED");
		$list	 = $this->call_command("ENGINE_RETRIEVE",Array(0=>"GET_MODULE", "addtype" => 1));
		
		$out	 = "<module name=\"$this->module_name\" display=\"form\">\n";
		$out	.= "<form name=\"module_debug_options\" label=\"Debug Preferences for this session\" method=\"POST\" width=\"100%\">\n";
		$out	.= "<input name=\"command\" type=\"hidden\" value=\"".$this->module_command."UPDATE_DEBUG\"/>\n";
		$out	.= "<select name=\"display_time\" label=\"Display the length of time to build page\">\n";
		$out 	.= "<option value='NO'";
		if ($session_display_time=="NO"){
			$out .=" selected=\"true\"";
		}
		$out	.= ">No, Do not display the time taken to build</option>";
			$out .="<option value='YES'";
		if ($session_display_time=="YES"){
			$out .=" selected=\"true\"";
		}
		$out	.= ">Yes, Display the time taken to build</option>";
		$out	.= "</select>\n";
		$out	.= "<select name=\"display_xml\" label=\"Enable XML views\">\n";
		$out 	.= "<option value='NO'";
		if ($session_xml_enabled=="NO"){
			$out .=" selected=\"true\"";
		}
		$out	.= ">No, Do not allow XML=1 in QueryString</option>";
			$out .="<option value='YES'";
		if ($session_xml_enabled=="YES"){
			$out .=" selected=\"true\"";
		}
		$out	.= ">Yes, Allow XML=1 in QueryString</option>";
		$out	.= "</select>\n";
		
		$out	.= "<checkboxes name=\"debug_options\"  label=\"Please select the modules you wish to debug\" type=\"horizontal\">\n";
		$out	.= "<options module='Presentation Modules' sort='yes'>";
		for ($index=0,$max=count($list);$index<$max;$index++){
			if ($list[$index][2]==0){
			$out .="<option value='".$list[$index][0]."DEBUG_ON'";
			for ($session_index=0,$session_user_cmds_max=count($session_user_cmds);$session_index<$session_user_cmds_max;$session_index++){
				if($session_user_cmds[$session_index]==$list[$index][0]."DEBUG_ON"){
					$out .=" selected=\"true\"";
				}
			}
			$out .=">".$list[$index][1]."</option>";
			}
		}
		$out	.= "</options>\n";
		$out	.= "<options module='Administrative Modules' sort='yes'>";
		for ($index=0,$max=count($list);$index<$max;$index++){
			if ($list[$index][2]==1){
			$out .="<option value='".$list[$index][0]."DEBUG_ON'";
			for ($session_index=0,$session_user_cmds_max=count($session_user_cmds);$session_index<$session_user_cmds_max;$session_index++){
				if($session_user_cmds[$session_index]==$list[$index][0]."DEBUG_ON"){
					$out .=" selected=\"true\"";
				}
			}
			$out .=">".$list[$index][1]."</option>";
			}
		}
		$out	.= "</options>\n";
		$out	.= "<options module='System' sort='yes'>";
		for ($index=0,$max=count($list);$index<$max;$index++){
			if ($list[$index][2]==2){
			$out .="<option value='".$list[$index][0]."DEBUG_ON'";
			for ($session_index=0,$session_user_cmds_max=count($session_user_cmds);$session_index<$session_user_cmds_max;$session_index++){
				if($session_user_cmds[$session_index]==$list[$index][0]."DEBUG_ON"){
					$out .=" selected=\"true\"";
				}
			}
			$out .=">".$list[$index][1]."</option>";
			}
		}
		$out	.= "</options>\n";
		$out	.= "</checkboxes>\n";
		$out	.= "<input type=\"submit\" iconify=\"SAVE\"/>\n";
		$out	.= "</form>\n";
		$out	.= "</module>";
		return $out;
	}

	/**
	* a function to return a list of records for this module
	*/
	function debug_update($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,count($this->check_parameters($parameters,"debug_options",Array()))));
		}
		$deoptions = join(",",
			Array(
				join(",",$this->check_parameters($parameters,"debug_options_1",Array())),
				join(",",$this->check_parameters($parameters,"debug_options_2",Array())),
				join(",",$this->check_parameters($parameters,"debug_options_3",Array()))
			)
		);
		
		$this->call_command("SESSION_SET",Array("SESSION_USER_COMMANDS",		$deoptions));
		$this->call_command("SESSION_SET",Array("SESSION_DISPLAY_TIME",			$this->check_parameters($parameters,"display_time","NO")));
		$this->call_command("SESSION_SET",Array("SESSION_DEBUG_XML_ENABLED",	$this->check_parameters($parameters,"display_xml","NO")));
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
		* Table structure for table 'user_info'
		*/
		
		$fields = array(
		array("system_preference_name"		,"varchar(255)"		,"NOT NULL"	,"default ''"),
		array("system_preference_label"		,"varchar(255)"		,"NOT NULL"	,"default ''"),
		array("system_preference_client"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("system_preference_value"		,"varchar(255)"		,"NOT NULL"	,"default ''"),
		array("system_preference_options"	,"varchar(255)"		,"NOT NULL"	,"default ''"),
		array("system_preference_module"	,"varchar(255)"		,"NOT NULL"	,"default 'SYSPREFS_'")
		);
		$primary ="";
		$tables[count($tables)] = array("system_preferences", $fields, $primary);
		/**
		* Table structure for table 'user_info'
		*/
		
		$fields = array(
			array("smd_identifier"						,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("smd_client"							,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("smd_audience"						,"TEXT"				,"NOT NULL"	,"default ''"),
			array("smd_subject"							,"TEXT"				,"NOT NULL"	,"default ''"),
			array("smd_doctypes"						,"TEXT"				,"NOT NULL"	,"default ''"),
			array("smd_copy_location"					,"VARCHAR(255)"		,"NOT NULL"	,"default ''"),
			array("smd_publisher_contact_information"	,"VARCHAR(255)"		,"NOT NULL"	,"default ''")
		);

		$primary ="smd_identifier";
		$tables[count($tables)] = array("system_metadata_defaults", $fields, $primary);

		return $tables;
	}
	
	function system_admin($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,""));
		}
		$module = $this->check_parameters($parameters,"module","SYSPREFS_");
		$sql = "select * from system_preferences where system_preference_client=$this->client_identifier and system_preference_module='$module' order by system_preference_label";
		$result = $this->parent->db_pointer->database_query($sql);

		$sp =Array("name" => Array(),"label" => Array(),"value" => Array());
		$lindex=0;

		while($r= $this->parent->db_pointer->database_fetch_array($result)){
			$sp["name"][$lindex]	=	$r["system_preference_name"];
			$sp["label"][$lindex]	=	$r["system_preference_label"];
			$sp["value"][$lindex]	=	$r["system_preference_value"];
			$sp["options"][$lindex]	=	$r["system_preference_options"];
			$lindex++;
		}

		$out	 = "<module name=\"$this->module_name\" display=\"form\"><page_options>
					".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","INFORMATIONADMIN_LIST",LOCALE_CANCEL))."
					<header><![CDATA[System Preferences]]></header></page_options>\n";
		$out	.= "<form name=\"sys_prefs\" label=\"Preferences\" method=\"POST\" width=\"500\">\n";
		$out	.= "<input name=\"command\" type=\"hidden\" value=\"".$this->module_command."UPDATE_SYSTEM_PREFS\"/>\n";
		$out .= "<page_sections>";
			$out .= "	<section label=\"".$this->get_constant("LOCALE_MENU_SETTING_".$module)."\">";

		if($module=="SYSPREFS"){
			$out	.= "<text><![CDATA[Welcome to the system preferences section of the administration section.  Modifing these settings will effect system wide settings.]]></text>\n";
		}
//		print_r($sp);
		for($index=0;$index<$lindex;$index++){
			$val = $sp["options"][$index];
			if ($val!="TEXT"){
				if (strpos($val,":")>0){
					$list = split(":",$sp["options"][$index]);
				}else{
					$list = Array($sp["options"][$index]);
				}
				$l = count($list);
//				print "<li>".$sp["label"][$index]." - ". $this->get_constant($sp["label"][$index]) ."</li>";
				if ($l!=2){
					$out	.= "<select name=\"".$sp["name"][$index]."\" label=\"".$this->get_constant($sp["label"][$index])."\">";
					for ($i=0; $i<$l; $i++){
						$out	.= "<option value='".$list[$i]."'";
						if ($list[$i]==$sp["value"][$index]){
							$out.=" selected=\"true\"";
						}
						$out	.= ">".$this->get_constant($list[$i])."</option>";
					}
					$out	.= "</select>\n";
				} else {
//					print "<li>".__LINE__." ".$sp["label"][$index]." - ".$this->get_constant($sp["label"][$index])."</li>";
					$out	.= "<radio type=\"horizontal\" name=\"".$sp["name"][$index]."\" label=\"".$this->get_constant($sp["label"][$index])."\">";
					for ($i = 0 ; $i<$l ; $i++){
					//print $list[$i];
						$out	.= "<option value='".$list[$i]."'";
						if ($list[$i]==$sp["value"][$index]){
							$out.=" selected=\"true\"";
						}
						$out	.= ">".$this->get_constant($list[$i])."</option>";
					}
					$out	.= "</radio>\n";
				}
			} else {
				$out	.= "<input type=\"text\" size=\"255\" name=\"".$sp["name"][$index]."\" label=\"".$this->get_constant($sp["label"][$index])."\"><![CDATA[".$sp["value"][$index]."]]></input>\n";
			}
		}
		$out .= "	</section>";
		$out .= "</page_sections>";
		$out	.= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>\n";
		$out	.= "</form>\n";
		$out	.= "</module>";

		return $out;
	}

	function system_admin_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"system_admin_save",__LINE__,""));
		}
		$sql = array();
		foreach ($parameters as $key => $value){
			if (strpos($key,"sp_")===0){
				$sql[count($sql)] ="update system_preferences set system_preference_value='".trim($this->strip_tidy($value))."' where system_preference_name = '$key' and system_preference_client = $this->client_identifier";
			}
		}

		for ($index=0,$max=count($sql);$index<$max;$index++){
			$this->parent->db_pointer->database_query($sql[$index]);
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,$sql[$index]));
			}
		}
			$out	 = "<module name=\"$this->module_name\" display=\"form\"><page_options><button command=\"SYSPREFS_SYSTEM_ADMIN\" alt=\"LOCALE_CANCEL\" iconify=\"CANCEL\" /></page_options>\n";
			$out	.= "<form name=\"sys_prefs\" label=\"".LOCALE_SYS_PREF_FORM_CONFIRM."\" method=\"POST\">\n";
			$out	.= "<text><![CDATA[".LOCALE_SYS_PREF_CONFIRM."]]></text>\n";
			$out	.= "</form>\n";
			$out	.= "</module>";
		return $out;
	}

	function extract_system_preference($parameters){
		$val = $this->check_parameters($this->parent->system_prefs,$this->check_parameters($parameters,0),"**NOT_FOUND**");
		if($val!="**NOT_FOUND**"){
			return $val;
		} else {
			$variable = $this->check_parameters($parameters,0);
			$default = $this->check_parameters($parameters,"default");
			$value ="";
			$sql = "select system_preference_value from system_preferences where system_preference_client=$this->client_identifier and system_preference_name='$variable'";
			$result = $this->parent->db_pointer->database_query($sql);
			$num = $this->parent->db_pointer->database_num_rows($result);
			if ($num > 0){
				while($r= $this->parent->db_pointer->database_fetch_array($result)){
					$value	=	$r["system_preference_value"];
				}
			}else {
	//			if ($default!=""){
					$module		= $this->check_parameters($parameters,"module","SYSPREFS_");
					$options	= $this->check_parameters($parameters,"options","TEXT");
					$sql = "insert into system_preferences 
					(system_preference_client, system_preference_name, system_preference_module, system_preference_options, system_preference_label, system_preference_value)
					values
					($this->client_identifier, '$variable', '$module', '$options', 'LOCALE_".strToupper($variable)."', '$default')";
					$result = $this->parent->db_pointer->database_query($sql);
					$value = $default;
	//			}
				$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
				@unlink ($data_files."/layout_".$this->client_identifier."_admin.xml");
			}
			$this->parent->system_prefs[$variable] = $value;
			return $value;
		}
	}
	
	function metadata_admin($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,""));
		}
		$meta_type		= "";
		$meta_subject	= "";
		$meta_audience	= "";
		$meta_publisher = "";
		$meta_rights= "";
		$exists=0;
		$sql = "select * from system_metadata_defaults where smd_client= $this->client_identifier";
		$result = $this->parent->db_pointer->database_query($sql);
		if ($result){
			while($r= $this->parent->db_pointer->database_fetch_array($result)){
				$meta_type		= $r["smd_doctypes"];
				$meta_subject	= $r["smd_subject"];
				$meta_audience	= $r["smd_audience"];
				$meta_publisher = $r["smd_publisher_contact_information"];
				$meta_rights	= $r["smd_copy_location"];
				$exists			= $r["smd_identifier"];
			}
		}
		$out	 = "<module name=\"$this->module_name\" display=\"form\"><page_options><button command=\"ENGINE_SPLASH\" alt=\"LOCALE_CANCEL\" iconify=\"CANCEL\" /></page_options>\n";
		$out	.= "<form name=\"sys_prefs\" label=\"".LOCALE_META_DEFAULT_FORM."\" method=\"POST\" width=\"100%\">\n";
		$out	.= "<input name=\"exists\" type=\"hidden\" ><![CDATA[$exists]]></input>";
		$out	.= "<text><![CDATA[".LOCALE_META_DEFAULT_MSG1."]]></text>\n";
		$out	.= "<input type=\"text\" label=\"".LOCALE_META_COPY_RIGHT_LOCATION."\" name=\"meta_rights\" size=\"255\"><![CDATA[$meta_rights]]></input>";
		$out	.= "<input type=\"text\" name=\"meta_publisher\" label=\"".LOCALE_META_PUBLISHER."\" size=\"255\" ><![CDATA[$meta_publisher]]></input>\n";
		$out	.= "<input name=\"command\" type=\"hidden\" value=\"".$this->module_command."SYSTEM_METADATA_SAVE\"/>\n";
		$out	.= "<text><![CDATA[".LOCALE_META_AUDIENCE."]]></text>\n";
		$out	.= "<textarea name=\"meta_audience\" label=\"".LOCALE_META_WHAT_AUDIENCE."\" size=\"60\" height=\"5\" type=\"PLAIN-TEXT\"><![CDATA[$meta_audience]]></textarea>\n";
		$out	.= "<text><![CDATA[".LOCALE_META_SUBJECT."]]></text>\n";
		$out	.= "<textarea name=\"meta_subject\" label=\"".LOCALE_META_WHAT_SUBJECT."\" size=\"60\" height=\"5\" type=\"PLAIN-TEXT\"><![CDATA[$meta_subject]]></textarea>\n";
		$out	.= "<text><![CDATA[".LOCALE_META_DOC_TYPES."]]></text>\n";
		$out	.= "<textarea name=\"meta_type\" label=\"".LOCALE_META_WHAT_DOC_TYPE."\"  size=\"60\" height=\"5\" type=\"PLAIN-TEXT\"><![CDATA[$meta_type]]></textarea>\n";
		$out	.= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>\n";
		$out	.= "</form>\n";
		$out	.= "</module>";
		return $out;
	}
	function metadata_admin_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,""));
		}
		$exists			= $this->check_parameters($parameters,"exists","0");
		$meta_type		= join("\n",split("__NEW_LINE__",trim($this->strip_tidy(join("__NEW_LINE__",split("\n",$this->check_parameters($parameters,"meta_type")))))));
		$meta_subject	= join("\n",split("__NEW_LINE__",trim($this->strip_tidy(join("__NEW_LINE__",split("\n",$this->check_parameters($parameters,"meta_subject")))))));
		$meta_audience	= join("\n",split("__NEW_LINE__",trim($this->strip_tidy(join("__NEW_LINE__",split("\n",$this->check_parameters($parameters,"meta_audience")))))));
		$meta_publisher = join("\n",split("__NEW_LINE__",trim($this->strip_tidy(join("__NEW_LINE__",split("\n",$this->check_parameters($parameters,"meta_publisher")))))));
		$meta_rights	= join("\n",split("__NEW_LINE__",trim($this->strip_tidy(join("__NEW_LINE__",split("\n",$this->check_parameters($parameters,"meta_rights")))))));
		if ($exists=="0"){
			$sql = "insert into system_metadata_defaults (smd_client, smd_doctypes, smd_subject, smd_audience, smd_publisher_contact_information, smd_copy_location) values ('$this->client_identifier', '$meta_type', '$meta_subject', '$meta_audience', '$meta_publisher', '$meta_rights');";
		} else {
			$sql = "
			update 
				system_metadata_defaults 
			set 
				smd_doctypes ='$meta_type',
				smd_subject = '$meta_subject', 
				smd_audience = '$meta_audience', 
				smd_publisher_contact_information = '$meta_publisher', 
				smd_copy_location = '$meta_rights'
			where
				smd_client = $this->client_identifier and 
				smd_identifier=$exists;";
		}
		$result = $this->parent->db_pointer->database_query($sql);
		$out	 = "<module name=\"$this->module_name\" display=\"form\"><page_options><button command=\"SYSPREFS_SYSTEM_METADATA\" alt=\"LOCALE_CANCEL\" iconify=\"CANCEL\" /></page_options>\n";
		$out	.= "<form name=\"sys_prefs\" label=\"".LOCALE_META_DEFAULT_FORM_CONFIRM."\" method=\"POST\">\n";
		$out	.= "<text><![CDATA[".LOCALE_META_SAVE_CONFIRM."]]></text>\n";
		$out	.= "</form>\n";
		$out	.= "</module>";
	return $out;
	}

	function extract_all_settings($parameters){
		$extra	= $this->check_parameters($parameters,"extra");
		$sql 	= "Select system_preference_name, system_preference_value from system_preferences where system_preference_client = $this->client_identifier and system_preference_module = 'SYSPREFS_'";
		$out	= "<module name=\"$this->module_name\" display=\"settings\">\n$extra";
//		$result = $this->parent->db_pointer->database_query($sql);
		$result  = $this->parent->db_pointer->database_query($sql);
		if ($result){
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$name	 = $r["system_preference_name"];
				$val	 = $r["system_preference_value"];
				$out	.= "<setting name='$name'><![CDATA[$val]]></setting>\n";
			}
		}
		$this->parent->db_pointer->database_free_result($result);
		$out	.= "</module>";
		return $out;
	}
	
	function load_system_prefs($parameters){
		$list	= Array();
		$sql 	= "Select system_preference_name,  system_preference_value from system_preferences where system_preference_client = $this->client_identifier order by system_preference_name";
		$result  = $this->parent->db_pointer->database_query($sql);
		if ($result){
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$list[$r["system_preference_name"]] = $r["system_preference_value"];
			}
		}
		$this->parent->db_pointer->database_free_result($result);
		return $list;
	}
}
?>