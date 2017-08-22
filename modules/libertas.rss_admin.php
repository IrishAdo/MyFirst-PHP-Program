<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.rss_admin.php
* @date 12 Feb 2004
*/
/**
* This module is the administration module for RSS feeds it will allow the user to 
* generate Internal and external Rss definitions
*/

class rss_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name_label			= "RSS Management Module (Administration)";
	var $module_name				= "rss_admin";
	var $module_admin				= "1";
	var $module_command				= "RSSADMIN_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_RSS";
	var $module_modify	 			= '$Date: 2005/03/02 09:52:55 $';
	var $module_version 			= '$Revision: 1.28 $';
	var $module_creation 			= "26/02/2004";
	var $searched					= 0;
	var $loadedcat					= Array();
	var $webContainer				= "RSS_";
	/**
	*  Management Menu entries
	*/
	
	var $module_admin_options 		= array();
	
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access = array(
		array("RSSADMIN_ALL",			"COMPLETE_ACCESS"),
		array("RSSADMIN_LIST_CREATOR",	"ACCESS_LEVEL_LIST_AUTHOR"),  // this will allow the user to add a new category to the system
		array("RSSADMIN_CREATOR",		"ACCESS_LEVEL_AUTHOR"),  // this will allow the user to add a new category to the system
		array("RSSADMIN_EDITOR",		"ACCESS_LEVEL_EDITOR"),  // this user role will allow the user to edit and remove categories.
		array("RSSADMIN_APPROVER",		"ACCESS_LEVEL_APPROVER") // this will allow the user to 
	);
	
	/**
	*  Frequency list
	*/
	var $freq_list = array();
	/**
	*  Channel options
	*/
	var $module_display_options 	= array(
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
	//	array(2,"Image Rotator","WEBOBJECTS_SHOW_GUESTBOOK",0,0)
	);
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    - Module Preferences
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	var $preferences= Array(
		Array("sp_rss_downloads"		,"LOCALE_SP_RSS_DOWNLOADS"		,'10'	, '1:2:3:4:5:6:7:8:9:10', "RSSADMIN_",	"ECMS")/*,
		Array("sp_open_rss_external"	,"LOCALE_SP_OPEN_RSS_EXTERNAL"	,'No'	, 'Yes:No'				, "RSSADMIN_",	"ECMS")*/
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
	var $add_information_lists		= 0;
	var $install_access				= 0;
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
			if ($user_command==$this->module_command."GET_PREFS"){
				return $this->preferences;
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
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return "";$this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
				return $this->webContainer;
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Create table function allow access if in install mode
			- calls directly by passes initialise function
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($this->parent->module_type=="install"){
				if ($user_command == $this->module_command."CREATE_TABLE"){
					return $this->create_table();
				}
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Administration Module commands
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($user_command == $this->module_command."EXTERNAL_CACHE"){
				return $this->cache_external_rss($parameter_list);
			}
			if ($user_command == $this->module_command."EXTERNAL_PREVIEW"){
				return $this->preview_external($parameter_list);
			}
			
			if ($this->admin_access==1){
				if ($user_command == $this->module_command."MENU_DISPLAY_OPTIONS"){
					return $this->display_channels($parameter_list);
				}
				if ($user_command == $this->module_command."MY_WORKSPACE"){
					return $this->retrieve_my_docs($parameter_list);
				}
				/**
				* EXTERNAL RSS FEEDS
				*/ 
				if ($user_command == $this->module_command."EXTERNAL_LIST"){
					return $this->module_external_list($parameter_list);
				}
				if (($user_command == $this->module_command."EXTERNAL_ADD_FEED") || ($user_command == $this->module_command."EXTERNAL_EDIT_FEED")){
					return $this->modify_external_entry($parameter_list);
				}
				if (($user_command == $this->module_command."EXTERNAL_REMOVE_FEED")){
					$this->remove_external_feed($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."EXTERNAL_LIST"));
				}
				if ($user_command == $this->module_command."EXTERNAL_SAVE_ENTRY"){
					$this->save_external_feed($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."EXTERNAL_LIST"));
				}
				if ($user_command == $this->module_command."EXTERNAL_CREATE"){
					$this->external_feed_creation($parameter_list); // used by modules to supply external RSS for inclusion
				}
				/**
				* INTERNAL RSS FEEDS
				*/ 
				if ($user_command == $this->module_command."INTERNAL_LIST"){
					return $this->module_internal_list($parameter_list);
				}
				if (($user_command == $this->module_command."INTERNAL_ADD_FEED") || ($user_command == $this->module_command."INTERNAL_EDIT_FEED")){
					return $this->modify_internal_entry($parameter_list);
				}
				if (($user_command == $this->module_command."INTERNAL_REMOVE_FEED")){
					$this->remove_internal_feed($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."INTERNAL_LIST"));
				}
				if ($user_command == $this->module_command."INTERNAL_SAVE_ENTRY"){
					$this->save_internal_feed($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."INTERNAL_LIST"));
				}
				if ($user_command == $this->module_command."INTERNAL_CACHE"){
					return $this->cache_internal_rss($parameter_list);
				}
				if ($user_command == $this->module_command."INTERNAL_PREVIEW"){
					return $this->preview_internal($parameter_list);
				}
			}
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                D I R E C T O R Y   S E T U P   F U N C T I O N S
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
		$this->load_locale("rss_admin");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		$this->admin_access				= 0;
		$this->author_admin_access		= 0;
		$this->editor_admin_access		= 0;
		$this->approve_admin_access		= 0;
		$this->add_information_lists	= 0;
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$this->page_size	= $this->check_prefs(Array("sp_page_size"));
//		$this->rss_external = $this->check_prefs(Array("sp_open_rss_external"	,"LOCALE_SP_OPEN_RSS_EXTERNAL"	,"default" => 'No'	, "options" => 'Yes:No'				, "module" => "RSSADMIN_",	"ECMS"));
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
					("RSSADMIN_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("RSSADMIN_CREATOR"==$access[$index])
				){
					$this->author_admin_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("RSSADMIN_ALL"==$access[$index]) ||
					("RSSADMIN_LIST_CREATOR"==$access[$index])
				){
					$this->add_information_lists=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("RSSADMIN_ALL"==$access[$index]) ||
					("RSSADMIN_EDITOR"==$access[$index])
				){
					$this->editor_admin_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("RSSADMIN_ALL"==$access[$index]) ||
					("RSSADMIN_APPROVER"==$access[$index])
				){
					$this->approve_admin_access=0;
				}
			}
		}
		if (($this->approve_admin_access || $this->editor_admin_access || $this->add_information_lists || $this->author_admin_access ) && (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files"))){
			$this->list_access=1;
			$this->admin_access=1;
		}
		if ($this->parent->module_type=="install"){
			$this->install_access=1;
		}
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$this->module_admin_options[count($this->module_admin_options)] = array("RSSADMIN_EXTERNAL_LIST", "MANAGE_RSSFEEDS_EXTERNAL","");
			$this->module_admin_options[count($this->module_admin_options)] = array("RSSADMIN_INTERNAL_LIST", "MANAGE_RSSFEEDS_INTERNAL","");
		}
		$this->module_display_options 	= array(
			array("RSS_DISPLAY",	LOCALE_DISPLAY_RSS_FEED)
		);
		$this->freq_list = array(
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_1_VAL, LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_1_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_2_VAL, LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_2_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_3_VAL, LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_3_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_4_VAL, LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_4_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_5_VAL, LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_5_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_6_VAL, LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_6_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_7_VAL, LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_7_TXT)
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
		* Table structure for table 'rss_feed'
		*/
		$fields = array(
			array("rss_identifier"				,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("rss_url"						,"text"						,"NOT NULL"	,"default ''"	),
			array("rss_label"					,"varchar(255)"				,"NOT NULL"	,"default '0'"	),
			array("rss_client"					,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("rss_downloaded"				,"unsigned integer"			,"NOT NULL"	,"default '0'"	),
			array("rss_frequency"				,"unsigned integer"			,"NOT NULL"	,"default '0'"	),
			array("rss_optout"					,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_created_by"				,"unsigned integer"			,"NOT NULL"	,"default '0'"	),
			array("rss_bulletlist"				,"unsigned small integer"	,"NOT NULL" ,"default '0'"	),
			array("rss_status"					,"unsigned small integer"	,"NOT NULL" ,"default '0'"	),
			array("rss_number_of_items"			,"unsigned small integer"	,"NOT NULL" ,"default '0'"	),
			array("rss_date_created"			,"datetime"					,"NOT NULL" ,"default ''"	),
			array("rss_set_inheritance"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_all_locations"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_digital_desktop"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_override_channel_title"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_error_count"				,"unsigned integer"			,"NOT NULL"	,"default '0'"	),
			array("rss_extract_set_inheritance"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_extract_all_locations"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_description"				,"text"						,"NOT NULL"	,"default ''"	),
			array("rss_external"				,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_type"					,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_extractable"				,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_new_window"				,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_needs_cached"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"	),
			array("rss_last_download_attempt"	,"unsigned integer"			,"NOT NULL"	,"default '0'"	),
			array("rss_channel_image"			,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key")
		);
		
		$primary ="rss_identifier";
		$tables[count($tables)] = array("rss_feed", $fields, $primary);
		/**
		* Table structure for table 'rss_feed_fields'
		*/
		$fields = array(
			array("rff_identifier"				,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("rff_field"					,"varchar(255)"				,"NOT NULL"	,"default '0'"),
			array("rff_client"					,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("rff_on"						,"unsigned integer"			,"NOT NULL"	,"default '1'"),
			array("rff_feed"					,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);
		
		$primary ="rff_identifier";
		$tables[count($tables)] = array("rss_feed_fields", $fields, $primary);
		return $tables;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                            R S S   F E E D   M A N A G E R   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	

	function retrieve_my_docs($parameters){
		$use=false;
		$out="";
		if($use==true){
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$sql = "select * from rss_feed where rss_client=$this->client_identifier and rss_optout=0 and rss_digital_desktop=1 and rss_status=1 order by rss_label";
		$result  = $this->parent->db_pointer->database_query($sql);
		$out ="";
		
    	while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$identifier					= $r["rss_identifier"];
			$filename 					= $r["rss_url"];
			$last_dowloaded				= $r["rss_downloaded"];
			$refresh					= $r["rss_frequency"];
			$bulletlist					= $r["rss_bulletlist"];
			$number_of_items			= $r["rss_number_of_items"];
			$rss_override_channel_title	= $r["rss_override_channel_title"];
			$rss_label					= $r["rss_label"];
			$download_now 	= 0;
			/*
				if not cached or inneed of refresh then download else use cache
			*/
			if (($last_dowloaded==0) || ($this->libertasGetTime()-$refresh>$last_dowloaded)){
				$download_now = 1;
			}
			if ($download_now==1){
				$file = @fopen ($filename, "r"); 
				$error=0;
				if (!$file) { 
				   $content ="<text><![CDATA[<p>Unable to find RSS Feed .\n<p>Please check the specified url <a href='$filename' target='_externalWindow'>$filename</a>.\n]]></text>";
				   $error=1;
				} else {
					$content ="";
					while (!feof ($file)) { 
					   $content .= fgets ($file, 1024); 
					} 
					$pos = strpos($content,"<rss");
					if ($pos===false){
						$pos = strpos($content,"<rdf");
					}
					if ($pos===false){
						//should not be here
						$content="";
					} else {
						$content = substr($content,$pos);
					}
					fclose($file); 
				}
				$pos = strpos($content,"<rss");
				if ($pos===false){
					$pos = strpos($content,"<rdf");
				}
				if ($pos===false){
					//should not be here
				} else {
					$content = substr($content,$pos);
				}
			$sql = "select * from rss_feed_fields where rff_feed = $identifier and rff_client = $this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			$fields="<fields>
						<field name='bulletlist'><![CDATA[$rss_bulletlist]]></field>
						<field name='number_of_items'><![CDATA[$rss_number_of_items]]></field>
						<field name='override_channel_title'><![CDATA[$rss_override_channel_title]]></field>
						<field name='label'><![CDATA[$rss_label]]></field>
					";
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
               	$fields .= "<field name='show'><![CDATA[".$r["rff_field"]."]]></field>";
            }
			$fields .= "</fields>";
                $this->call_command("DB_FREE", Array($result));
				$filecontent= "<module name=\"RSS\" label=\"\" display=\"RSS\"><feed identifier='$identifier'>".$fields."</feed>".$content."</module>";
				$fp = fopen($data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml", 'w');
				fwrite($fp, $filecontent);
				fclose($fp);
				$um = umask(0);
				@chmod($data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml", LS__FILE_PERMISSION);
				umask($um);
				$out .=$filecontent;
				if ($error==1){
					$sql = "update rss_feed set rss_error_count=rss_error_count+1, rss_downloaded = '".time()."' where rss_client = $this->client_identifier and rss_identifier = $identifier ";
				} else {
					$sql = "update rss_feed set rss_error_count=0, rss_downloaded = '".time()."' where rss_client = $this->client_identifier and rss_identifier = $identifier ";
				}
	//			$sql = "update rss_feed set rss_downloaded = '".time()."' where rss_client = $this->client_identifier and rss_identifier = $identifier ";
				$this->parent->db_pointer->database_query($sql);
			} else {
				if (file_exists($data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml")){
					$content_array = file($data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml");
					$out .= implode("", $content_array);
				}
			}
   	    }
       	$this->parent->db_pointer->database_free_result($result);
		}
		return $out;
	}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-											E X T E R N A L   F E E D   F U N C T I O N S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: module_list($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_external_list($parameters){
		$sql = "
select 
	distinct 
		rss_feed.*
	from rss_feed
	where 
		rss_feed.rss_client=$this->client_identifier and 
		rss_external=1
	order by rss_feed.rss_identifier desc
";

		$out = "";
		$result = $this->parent->db_pointer->database_query($sql);
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$this->page_size=50;
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
				Array("ADD",$this->module_command."EXTERNAL_ADD_FEED", ADD_NEW)
			);
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["HEADER"]			= "External RSS Feeds";
			$variables["as"]				= "table";
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
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				if ($r["rss_status"]==0)
					$status =  STATUS_NOT_LIVE;
				else 
					$status =  STATUS_LIVE;
				$rss_frequency = $r["rss_frequency"];
				$rss_frequency_label="Live";
				for ($i=0;$i<count($this->freq_list);$i++){
					if($rss_frequency==$this->freq_list[$i][0]){
						$rss_frequency_label = $this->freq_list[$i][1];
					}
				}

				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["rss_identifier"],
					"ENTRY_BUTTONS"	=> Array(
						Array("EDIT",$this->module_command."EXTERNAL_EDIT_FEED",EDIT_EXISTING),
						Array("REMOVE",$this->module_command."EXTERNAL_REMOVE_FEED",REMOVE_EXISTING)
					),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,			$this->check_parameters($r,"rss_label",""),"TITLE"),
						Array("Reload Frequency",	$rss_frequency_label),
						Array("Status",				$status),
						Array("Failed to load",		$r["rss_error_count"]." times"),
						Array("Open in External",   ($r["rss_new_window"]==0?LOCALE_NO:LOCALE_YES)),
						Array("Rss feed",			"<a href='".$r["rss_url"]."'><img border='0' src='/libertas_images/general/iconification/rssfeed.gif'/></a>")
					)
				);
			}
			$this->page_size = $prev;
			
			return $this->generate_list($variables);
		}
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: modify_external_entry($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function modify_external_entry($parameters){
		$identifier 				= $this->check_parameters($parameters,"identifier",-1);
		$form_label 				= LOCALE_ADD;
		$rss_label					= "";
		$rss_url					= "";
		$rss_status					= "0";
		$rss_optout					= 0;
		$rss_frequency				= 2419200;
		$menu_parent				= -1;
		$rss_bulletlist				= 0;
		$rss_number_of_items		= 10;
		$all_locations				= 0;
		$set_inheritance			= 0;
		$menu_locations				= Array();
		$rss_digital_desktop		= 0;
		$rss_override_channel_title	= 0;
		$rss_new_window				= 0;
		$display_tab		=$this->check_parameters($parameters,"display_tab");
//			"Extra"		=> Array("Bullet list at top" => 0),
		$rsslist=Array(
			"Channel"	=> Array(
				"Title"				=> 0,
				"Image"				=> 0,
				"Description"		=> 0,
				"Copyright"			=> 0,
				"Last Build Date"	=> 0,
				"Categories"		=> 0,
				"Managing Editor"	=> 0,
				"Web Master"		=> 0,
				"Publish Date"		=> 0
			),
			"Story"		=> Array(
				"Title"				=> 0,
				"Attachments"		=> 0,
				"Publish Date"		=> 0,
				"Author"			=> 0,
				"Categories"		=> 0,
				"Description"		=> 0,
				"Comments Url"		=> 0
			)
		);
		if ($identifier!=-1){
			$form_label 	= LOCALE_EDIT;
			$sql			= "select * from rss_feed where rss_client=$this->client_identifier and rss_identifier=$identifier";
			$result = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$rss_label					= $r["rss_label"];
				$rss_url					= $r["rss_url"];
				$rss_status					= $r["rss_status"];
				$rss_frequency				= $r["rss_frequency"];
				$rss_optout					= $r["rss_optout"];
				$rss_number_of_items		= $r["rss_number_of_items"];
				$rss_bulletlist				= $r["rss_bulletlist"];
				$all_locations				= $r["rss_all_locations"];
				$set_inheritance			= $r["rss_set_inheritance"];
				$rss_digital_desktop		= $r["rss_digital_desktop"];
				$rss_new_window				= $r["rss_new_window"];
				$rss_override_channel_title	= $r["rss_override_channel_title"];
			}
			$this->parent->db_pointer->database_free_result($result);
			$sql = "select * from rss_feed_fields where rff_client=$this->client_identifier and rff_feed=$identifier";

			$result  = $this->parent->db_pointer->database_query($sql);
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$field = $r["rff_field"];
				$sfield = split("_",$field);
				if (count($sfield)==2){
					$rsslist[$sfield[0]][$sfield[1]] = $r["rff_on"];
				}
			}
			$this->parent->db_pointer->database_free_result($result);
			$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
				Array(
					"module"		=> "RSS_",
					"identifier"	=> $identifier
				)
			);
		}
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[".MANAGE_RSSFEEDS." - ".$form_label."]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."EXTERNAL_LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\" method=\"POST\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."EXTERNAL_SAVE_ENTRY\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<page_sections>";
		$out .="			<section label='".LOCALE_RSS_FEED."'>";
		$out .="				<input type='text' required='YES' name='rss_feed_label' label='".LOCALE_LABEL."' size='255'><![CDATA[$rss_label]]></input>";
		$out .="				<input type='text' required='YES' name='rss_feed_url' label='".LOCALE_URL."' size='1000'><![CDATA[$rss_url]]></input>";
		$out .="				<select name='rss_feed_download_frequency' label='".LOCALE_RSS_DOWNLOAD_FREQUENCY."'>";
		for ($i=0;$i<count($this->freq_list);$i++){
			$out.="					<option value='".$this->freq_list[$i][0]."'";
			if($rss_frequency==$this->freq_list[$i][0]){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".$this->freq_list[$i][1]."]]></option>";
		}
		$out .="				</select>";
		
		
		$out .="				<input type='hidden' name='rss_optout' value='0'/>";
		
		
		$out .="				<radio name='rss_status' label='".LOCALE_STATUS."'>";
			$out.="					<option value='1'";
			if($rss_status==1){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".STATUS_LIVE."]]></option>";
			$out.="					<option value='0'";
			if($rss_status==0){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".STATUS_NOT_LIVE."]]></option>";
		$out .="				</radio>";

		$out .="				<radio name='rss_new_window' label='".LOCALE_OPEN_IN_NEW_WINDOW."'>";
		$out .="				".$this->gen_options(Array(0,1),Array(LOCALE_NO,LOCALE_YES),$rss_new_window);
		$out .="				</radio>";


		$out .="				<radio name='rss_override_channel_title' label='".LOCALE_OVERRIDE_CHANNEL_LABEL."'>";
			$out.="					<option value='1'";
			if($rss_override_channel_title==1){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".LOCALE_YES."]]></option>";
			$out.="					<option value='0'";
			if($rss_override_channel_title==0){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".LOCALE_NO."]]></option>";
		$out .="				</radio>";
		/**
		* Display type of list
		*/
		$web_containers = split("~----~",$this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module" => $this->webContainer, "identifier" => $identifier)));
		if ($web_containers[0]!=""){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='rss_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}
		$out .="			</section>";
		$extraout ="<input type='hidden' name='rss_digital_desktop' value='0'/>";
		$out .= $this->location_tab($all_locations, $set_inheritance,$menu_locations, $display_tab, $extraout);
		$out .="			<section label='".LOCALE_RSS_DISPLAY_OPTIONS."'>";
		$out .="				<input type='text' size='3' name='rss_number_of_items' label='".LOCALE_RSS_NUMBER_OF_ITEMS."' format='number'><![CDATA[$rss_number_of_items]]></input>";
		$out .="				<radio name='rss_bulletlist' label='".LOCALE_RSS_BULLET_LIST."'>";
			$out.="					<option value='0'";
			if($rss_bulletlist==0){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".LOCALE_NO."]]></option>";
			$out.="					<option value='1'";
			if($rss_bulletlist==1){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".LOCALE_YES."]]></option>";
		$out .="				</radio>";
		$out .="<text><![CDATA[".LOCALE_RSS_CHOOSE_FIELDS_MSG."]]></text>";
		$out .="				<checkboxes name='rss_display_options' label='".LOCALE_RSS_CHOOSE_FIELDS."' type='horizontal'>";
		foreach($rsslist as $key => $rlist){
			$out.="<options module='$key Fields'>";
			foreach($rlist as $rkey => $rval){
				
				$out.="					<option value='".$key."_".$rkey."'";
				$check = strtolower($key."_".$rkey);
				if("$rval"=="1" || (
						($identifier==-1)
							 && 
						($check == "channel_title" || $check == "channel_image" ||  $check == "story_description" ||  $key."_".$rkey == "story_title")
					)
				){
					$out.=					" selected='true'";
				}
				$out.=					"><![CDATA[".$rkey."]]></option>";
			}
			$out.="</options>";
		}
		$out .="				</checkboxes>";
		
		$out .="			</section>";
		$out .= $this->preview_section('RSSADMIN_EXTERNAL_PREVIEW');
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	
	function save_external_feed($parameters){
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fname = $data_files."/rss_feed_".$this->client_identifier."_preview_".$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1).".xml";
		if (file_exists($fname)){
			@unlink($fname);
		}
		unset($_SESSION["preview_url"]);
		unset($_SESSION["preview"]);
		$tnumberofrss_display_options	= $this->check_parameters($parameters,"totalnumberofchecks_rss_display_options",0);
		$identifier 				 	= $this->check_parameters($parameters,"identifier",-1);
		$rss_label						= htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"rss_feed_label"))));
		$rss_url						= htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"rss_feed_url"))));
		$rss_frequency					= $this->check_parameters($parameters	,"rss_feed_download_frequency",2419200);
		$rss_status						= $this->check_parameters($parameters	,"rss_status",0);
		$rss_optout						= $this->check_parameters($parameters	,"rss_optout",0);
		$rss_number_of_items			= $this->check_parameters($parameters	,"rss_number_of_items",10);
		$rss_bulletlist					= $this->check_parameters($parameters	,"rss_bulletlist",0);
		$rss_created_by 				= $this->check_parameters($_SESSION		,"SESSION_USER_IDENTIFIER",0);
		$rss_digital_desktop			= $this->check_parameters($parameters	,"rss_digital_desktop",0);
		$all_locations					= $this->check_parameters($parameters	,"all_locations",0);
		$menu_locations					= $this->check_parameters($parameters	,"menu_locations");
		$currentlyhave					= $this->check_parameters($parameters	,"currentlyhave");
		$set_inheritance				= $this->check_parameters($parameters	,"set_inheritance",0);
		$rss_override_channel_title		= $this->check_parameters($parameters	,"rss_override_channel_title",0);
		$rss_new_window					= $this->check_parameters($parameters	,"rss_new_window",0);
		$replacelist=Array();
		$count_rss_containers			= $this->check_parameters($parameters	,"totalnumberofchecks_rss_containers");
		$replacelist	= $this->check_parameters($parameters,"rss_containers",Array());
		$rss_date_created	= $this->libertasGetDate("Y/m/d H:i:s");
		$rss_downloaded		= $this->libertasGetTime();
		if ($identifier==-1){
			// Add
			$sql = "insert into rss_feed (rss_client, rss_frequency, rss_optout, rss_label, rss_status, rss_number_of_items ,rss_bulletlist, rss_url, rss_created_by, rss_date_created, rss_all_locations, rss_set_inheritance, rss_downloaded, rss_digital_desktop, rss_override_channel_title, rss_external, rss_new_window)
						 values
					($this->client_identifier, $rss_frequency, $rss_optout, '$rss_label', $rss_status, '$rss_number_of_items', $rss_bulletlist, '$rss_url', $rss_created_by, '$rss_date_created', '$all_locations', '$set_inheritance', '$rss_downloaded', '$rss_digital_desktop',$rss_override_channel_title, 1, '$rss_new_window')";
			$this->parent->db_pointer->database_query($sql);
			$sql = "select rss_identifier from rss_feed where 
						rss_set_inheritance			= '$set_inheritance' and 
						rss_all_locations			= '$all_locations' and 
						rss_frequency				= $rss_frequency and 
						rss_optout					= $rss_optout and 
						rss_label					= '$rss_label' and 
						rss_status					= $rss_status and  
						rss_number_of_items			= $rss_number_of_items and 
						rss_bulletlist				= $rss_bulletlist and 
						rss_url						= '$rss_url' and 
						rss_client 					= $this->client_identifier and 
						rss_date_created			= '$rss_date_created' and 
						rss_created_by				= $rss_created_by and
						rss_digital_desktop			= $rss_digital_desktop and
						rss_external				= 1 and
						rss_override_channel_title	= $rss_override_channel_title
					";

			$result  = $this->parent->db_pointer->database_query($sql);
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$identifier = $r["rss_identifier"];
			}
			$this->parent->db_pointer->database_free_result($result);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE", Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $rss_label,
					"wo_command"	=> "RSS_DISPLAY",
					"cmd"			=> "ADD",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
		} else {
			// Edit
			$sql = "update rss_feed set 
						rss_frequency				= $rss_frequency, 
						rss_optout					= $rss_optout, 
						rss_label					= '$rss_label', 
						rss_status					= $rss_status, 
						rss_number_of_items 		= '$rss_number_of_items',
						rss_bulletlist				= $rss_bulletlist, 
						rss_url						= '$rss_url',
						rss_all_locations			= '$all_locations',
						rss_set_inheritance			= '$set_inheritance',
						rss_digital_desktop			= '$rss_digital_desktop',
						rss_override_channel_title	= $rss_override_channel_title,
						rss_new_window				= $rss_new_window
					where 
						rss_external=1 and 
						rss_client = $this->client_identifier and
						rss_identifier = $identifier";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $rss_label,
					"wo_command"	=> "RSS_DISPLAY",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
		}
		$sql = "delete from rss_feed_fields where rff_feed=$identifier and rff_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		for ($i=1;$i<=$tnumberofrss_display_options;$i++){
			$rss_display_options		 	= $this->check_parameters($parameters,"rss_display_options_$i",Array());
			$l = count($rss_display_options);
			for ($index=0;$index<$l;$index++){
				$sql = "insert into rss_feed_fields (rff_feed, rff_client , rff_field, rff_on)values ($identifier, $this->client_identifier, '".$rss_display_options[$index]."', 1)";
				$this->parent->db_pointer->database_query($sql);
			}
		}
		if($rss_status==1){
			$this->call_command($this->module_command."EXTERNAL_CACHE",Array("identifier"=>$identifier));
		}
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier,
				"all_locations"	=> $all_locations
			)
		);
		
		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance("RSS_DISPLAY",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=>$child_locations,
					"module"		=>$this->webContainer,
					"identifier"	=>$identifier,
					"all_locations"	=>$all_locations,
					"delete"		=>0
				)
			);
			$this->set_inheritance(
				"RSS_DISPLAY",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"		=> $this->webContainer,
					"condition"		=> "rss_set_inheritance =1 and ",
					"client_field"	=> "rss_client",
					"table"			=> "rss_feed",
					"primary"		=> "rss_identifier"
					)
				).""
			);
		}
		$this->tidyup_display_commands($parameters);
	}
	/*************************************************************************************************************************
    * create and manage a link betwwen a module and an RSS FEED
    *************************************************************************************************************************/
	function external_feed_creation($parameters){
		$action		= $this->check_parameters($parameters, "action", 0); // remove (default)
		$module		= $this->check_parameters($parameters, "module");
		$ownerid	= $this->check_parameters($parameters, "identifier", -1);
		$label		= $this->check_parameters($parameters, "label");
		$frequency	= $this->check_parameters($parameters, "frequency", 2419200);
		$counter	= $this->check_parameters($parameters, "counter", 1);
		$sql = "select rss_identifier from rss_feed where rss_module= '$module' and rss_ownerid= $ownerid and rss_client= $this->client_identifier and rss_external= 1 ";
		$result  = $this->parent->db_pointer->database_query($sql);
		$identifier=-1;
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$identifier = $r["rss_identifier"];
		}
        $this->parent->db_pointer->database_free_result($result);
		$rss_created_by = $_SESSION["SESSION_USER_IDENTIFIER"];
		if($identifier==-1){
			if($action==1){
				$rss_date_created = date("Y/m/d H:i:s");
				$rss_identifier = $this->getUid();
				$sql = "insert into rss_feed (rss_identifier, rss_client, rss_frequency, rss_label, rss_status, rss_number_of_items ,rss_bulletlist, rss_url, rss_created_by, rss_date_created, rss_all_locations, rss_set_inheritance, rss_downloaded, rss_digital_desktop, rss_override_channel_title, rss_external, rss_module, rss_ownerid)
							 values
						($rss_identifier, $this->client_identifier, $frequency, '$label', 0, '$counter', 0, 'http://".$this->parent->domain.$this->parent->base."index.php?command=".$module."RSS_EXTRACT&amp;identifier=$ownerid', $rss_created_by, '$rss_date_created', '0', '0', '0', '0','', 1, '$module', $ownerid)";
				$this->parent->db_pointer->database_query($sql);
				$sql = "insert into web_objects (wo_client, wo_label, wo_type, wo_command, wo_all_locations, wo_show_label, wo_owner_module, wo_owner_id, wo_set_inheritance)
							 values
						($this->client_identifier, '$label', 1, 'RSS_DISPLAY', 0, 0, 'RSS_', $rss_identifier, 0 )";
				$this->parent->db_pointer->database_query($sql);
				$sql = "insert into rss_feed_fields (rff_feed, rff_client, rff_on, rff_field) values ($rss_identifier, $this->client_identifier, 1, 'Channel_Title')";
				$this->parent->db_pointer->database_query($sql);
				$sql = "insert into rss_feed_fields (rff_feed, rff_client, rff_on, rff_field) values ($rss_identifier, $this->client_identifier, 1, 'Story_Title')";
				$this->parent->db_pointer->database_query($sql);
				$sql = "insert into rss_feed_fields (rff_feed, rff_client, rff_on, rff_field) values ($rss_identifier, $this->client_identifier, 1, 'Story_Description')";
				$this->parent->db_pointer->database_query($sql);
			}
		} else {
			if($action==1){
				$sql = "update rss_feed set 
							rss_label='$label', rss_url='http://".$this->parent->domain.$this->parent->base."index.php?command=".$module."RSS_EXTRACT&amp;identifier=$ownerid'
						where rss_identifier = $identifier and rss_client = $this->client_identifier and rss_module ='$module' and rss_ownerid=$ownerid";
				$this->parent->db_pointer->database_query($sql);
			} else {
				$sql = "delete from rss_feed where rss_identifier = $identifier and rss_client = $this->client_identifier and rss_module ='$module' and rss_ownerid=$ownerid";
				$this->parent->db_pointer->database_query($sql);
				$sql = "delete from rss_feed_fields where rff_feed = $identifier and rss_client = $this->client_identifier";
				$this->parent->db_pointer->database_query($sql);
			}
		}
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function cache_external_rss($parameters){
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$identifier = $this->check_parameters($parameters,"identifier");
		$sql = "select * from rss_feed where rss_client=$this->client_identifier and rss_identifier=$identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$out ="";
    	while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$identifier					= $r["rss_identifier"];
			$label	 					= $r["rss_label"];
			$filename 					= $r["rss_url"];
			$last_dowloaded 			= $r["rss_downloaded"];
			$rss_new_window				= ($r["rss_new_window"]==1?"Yes":"No");
			$refresh					= $r["rss_frequency"];
			$bulletlist					= $r["rss_bulletlist"];
			$number_of_items			= $r["rss_number_of_items"];
			$rss_override_channel_title = $r["rss_override_channel_title"];
			$file = @fopen (str_replace("&amp;","&", $filename), "r"); 
			$error=0;
			
			if (!$file) { 				
			   $content ="<text><![CDATA[<p>Unable to find RSS Feed .\n<p>Please check the specified url <a href='$filename' target='_externalWindow'>$filename</a>.\n]]></text>";
			   $error=1;
			} else {				
				$content ="";
				while (!feof ($file)) { 
				   $content .= fgets ($file, 1024); 
				} 	
				/* By Imran to replace <![CDATA[*]]]>*/
				$content = str_replace("]]]","]]",$content);
				$content = str_replace("â€“","-",$content);

				//print "<li>".$content."</li>";							
				$pos = strpos($content,"<rss");
				if ($pos===false){
					$pos = strpos($content,"<rdf");
				}
				if ($pos===false){
					//should not be here
					$content="";
				} else {
					$content = substr($content,$pos);
				}
				/*************************************************************************************************************************
	            * strip tags but keep prefixes
	            *************************************************************************************************************************/
				$pos = strpos($content,">");
				$prefixes = $this->get_prefixes(substr($content,0,$pos));
				$content = $this->php_tidy(html_entity_decode($content));
				$tag_list= Array("title", "link", "description", "image", "language", "webMaster", "managingEditor", "pubDate", "lastBuildDate", "url", "width", "height", "rss", "channel", "item", "RDF", "language", "rights", "date", "creator", "items", "Seq", "li", "textinput", "publisher", "subject", "topic", "value", "name", "docs", "copyright");
				$keep_tags = "";
				for($i=0,$m=count($prefixes);$i<$m;$i++){
					$keep_tags .= "<".$prefixes[$i].join("><".$prefixes[$i],$tag_list).">";
				}
				$content = strip_tags($content, $keep_tags);
				$content = str_replace("&apos;", "'", $content);
				$content = str_replace("â€™","'", $content);
				$content = str_replace("â€˜","'", $content);
				$content = str_replace("‘", "'", $content);
				$content = str_replace("’", "'", $content);

	//			$content = strip_tags($content, $keep_tags);
				/*************************************************************************************************************************
	            * 
	            *************************************************************************************************************************/
				$sql = "select * from rss_feed_fields where rff_feed = $identifier and rff_client = $this->client_identifier";
				$result  = $this->parent->db_pointer->database_query($sql);
				$fields="<fields>
						<field name='bulletlist'><![CDATA[$bulletlist]]></field>
						<field name='number_of_items'><![CDATA[$number_of_items]]></field>
						<field name='override_channel_title'><![CDATA[$rss_override_channel_title]]></field>
						<field name='label'><![CDATA[$label]]></field>
					";
            	while($r = $this->parent->db_pointer->database_fetch_array($result)){
               		$fields .= "<field name='show'><![CDATA[".$r["rff_field"]."]]></field>";
	            }
				$fields .= "</fields>";
				$this->call_command("DB_FREE", Array($result));
				$filecontent= "<module name=\"RSS\" label=\"\" display=\"RSS\">
					<setting name='sp_open_rss_external'><![CDATA[$rss_new_window]]></setting>
				<feed identifier='$identifier'>".$fields."</feed>".$content."</module>";
				$filecontent = str_replace (Array("Â£", "£", '’',"&lt;","&gt;","&quot;"),Array("&#163;", "&#163;", "'","<",">",'"'),htmlentities($filecontent));
				if ($content==""){
					$domDoc = false;
				} else if (function_exists('domxml_open_mem')){
//					$domDoc = @domxml_open_mem($filecontent);
					$domDoc = true;
				} else {
					$domDoc = true;
				}
				if(!$domDoc) {
					$error = 1;
				} else {
					
					$f_name = $data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml";
					$fp = fopen($f_name, 'w');
					fwrite($fp, $filecontent);
					fclose($fp);
					$um = umask(0);
					@chmod($f_name, LS__FILE_PERMISSION);
					umask($um);
					$out .= $this->convert_xml_2_xhtml(Array("string"=>$filecontent, "label"=>$label));
					$out = str_replace(
						Array(
							'xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" xmlns:syn="http://purl.org/rss/1.0/modules/syndication/"',
							'src="'.$this->parent->base.'http://',
							'href="'.$this->parent->base.'http://',
							'’'
						), Array(
							'',
							'src="http://',
							'href="http://',
							"'"
						),$out);
					$str = str_replace(
							Array("<title>", "</title>", "<description>", "</description>", "<link>", "</link>", 
									"<language>", "</language>", "<webMaster>", "</webMaster>", "<managingEditor>",
									"</managingEditor>", "<pubDate>", "</pubDate>", "<lastBuildDate>", "</lastBuildDate>", 
									"<url>", "</url>", "<width>", "</width>", "<height>", "</height>"),
							Array("<title><![CDATA[", "]]></title>", "<description><![CDATA[", "]]></description>",
									"<link><![CDATA[", "]]></link>", "<language><![CDATA[", "]]></language>",
									"<webMaster><![CDATA[", "]]></webMaster>", "<managingEditor><![CDATA[", "]]></managingEditor>", 
									"<pubDate><![CDATA[", "]]></pubDate>", "<lastBuildDate><![CDATA[", "]]></lastBuildDate>", "<url><![CDATA[", "]]></url>",
									"<width><![CDATA[", "]]></width>", "<height><![CDATA[", "]]></height>"),
							$out);
					$out = str_replace(
							Array("<![CDATA[<![CDATA[", "]]>]]>"), 
							Array("<![CDATA[", "]]>"), 
							$str);							
					$f_name = $data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xhtml";
					$fp = fopen($f_name, 'w');
					fwrite($fp, $out);
					fclose($fp);
					$um = umask(0);
					@chmod($f_name, LS__FILE_PERMISSION);
					umask($um);
				}
			}
			if ($error==1){
				$sql = "update rss_feed set rss_last_download_attempt='".time()."', rss_error_count=rss_error_count+1, rss_downloaded = '".time()."' where rss_client = $this->client_identifier and rss_identifier = $identifier ";
				$out = "";
			} else {
				$sql = "update rss_feed set rss_last_download_attempt=0, rss_error_count=0, rss_downloaded = '".time()."' where rss_client = $this->client_identifier and rss_identifier = $identifier ";
			}
			$this->parent->db_pointer->database_query($sql);
   	    }
       	$this->parent->db_pointer->database_free_result($result);
		return $out;
	}
	
	function remove_external_feed($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		if ($identifier!=-1){
			$sql = "delete from rss_feed_fields where rff_feed=$identifier and rff_client = $this->client_identifier";
			$this->parent->db_pointer->database_query($sql);
			
			$sql = "select * from rss_feed where rss_identifier=$identifier and rss_client = $this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			$link_mod="";
			$link_id=0;
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$link_mod	= $r["rss_module"];
				$link_id	= $r["rss_ownerid"];
            }
            $this->parent->db_pointer->database_free_result($result);
			if (($link_mod!="") && ($link_id!=0)){
				$this->call_command($link_mod."UPDATE_RSS",Array("owner"=>$link_id));
			}
			$sql = "delete from rss_feed where rss_identifier=$identifier and rss_external=1 and rss_client = $this->client_identifier";
			$this->parent->db_pointer->database_query($sql);
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
			$this->call_command("LAYOUT_MENU_TO_OBJECT_REMOVE", Array(
					"module"		=>	$this->webContainer, 
					"identifier"	=>	$identifier
				)
			);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"cmd"			=> "REMOVE"
				)
			);
			@unlink($data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml");
			@unlink($data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xhtml");
		}
	}	

	function preview_external($parameters){
		$tnumberofrss_display_options	= $this->check_parameters($parameters,"totalnumberofchecks_rss_display_options",0);
		$identifier 				 	= $this->check_parameters($parameters,"identifier",-1);
		$rss_label						= htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"rss_feed_label"))));
		$rss_url						= strip_tags($this->validate($this->check_parameters($parameters,"rss_feed_url")));
		$rss_frequency					= $this->check_parameters($parameters,"rss_feed_download_frequency",2419200);
		$rss_status						= $this->check_parameters($parameters,"rss_status",0);
		$rss_optout						= $this->check_parameters($parameters,"rss_optout",0);
		$rss_number_of_items			= $this->check_parameters($parameters,"rss_number_of_items",10);
		$rss_bulletlist					= $this->check_parameters($parameters,"rss_bulletlist",0);
		$rss_created_by 				= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
		$rss_digital_desktop			= $this->check_parameters($parameters,"rss_digital_desktop",0);
		$all_locations					= $this->check_parameters($parameters,"all_locations",0);
		$menu_locations					= $this->check_parameters($parameters,"menu_locations");
		$currentlyhave					= $this->check_parameters($parameters,"currentlyhave");
		$count_rss_containers			= $this->check_parameters($parameters,"totalnumberofchecks_rss_containers");
		$set_inheritance				= $this->check_parameters($parameters,"set_inheritance",0);
		$rss_override_channel_title		= $this->check_parameters($parameters,"rss_override_channel_title",0);
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fname = $data_files."/rss_feed_".$this->client_identifier."_preview_".$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1).".xml";
		$fname1 = $data_files."/r2ss_feed_".$this->client_identifier."_preview_".$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1).".xml";
		$preview_url =$this->check_parameters($_SESSION,"preview_url","");
		if ($this->check_parameters($_SESSION,"preview","")=="" || !file_exists($fname1) || true ||($preview_url !=$rss_url)){
			$_SESSION["preview_url"]=$rss_url;
			$file = @fopen ($rss_url, "r"); 
			if (!$file) { 
				print "<style>
					body{font-size:0.9em;}
					.TableHeader{font-weigth:bold;border:1px solid #999999;background:#cccccc;padding:3px}
					.storyitem{margin:4px;xborder-bottom:2px solid #cccccc}
					.contentpos{margin:4px;}
				</style><table width=100%><tr><td><h1 class='tableHeader'>Unable to find RSS Feed .</h1>
				<p>Please check the specified url <a href='$rss_url' target='_externalWindow'>$rss_url</a>.\n</p>
					</td></tr></table>";
				$this->exitprogram();
			}
			$content ="";
			while (!feof ($file)) { 
			   $content .= fgets ($file, 1024); 
			} 
			fclose($file); 
			$pos = strpos($content,"<rss");
			if ($pos===false){
				$pos = strpos($content,"<rdf");
			}
			if ($pos===false){
				//should not be here
				$content="";
			} else {
				$content = substr($content,$pos);
			}
			/*************************************************************************************************************************
            * strip tags but keep prefixes
            *************************************************************************************************************************/
			$pos = strpos($content,">");
			$prefixes = $this->get_prefixes(substr($content,0,$pos));
			$content = $this->php_tidy(html_entity_decode($content));
			$tag_list= Array("title", "link", "description", "image", "language", "webMaster", "managingEditor", "pubDate", "lastBuildDate", "url", "width", "height", "rss", "channel", "item", "RDF", "language", "rights", "date", "creator", "items", "Seq", "li", "textinput", "publisher", "subject", "topic", "value", "name", "docs", "copyright");
			$keep_tags = "";
			for($i=0,$m=count($prefixes);$i<$m;$i++){
				$keep_tags .= "<".$prefixes[$i].join("><".$prefixes[$i],$tag_list).">";
			}
				$content = strip_tags($content, $keep_tags);
				$content = str_replace("&apos;", "'", $content);
				$content = str_replace("â€™","'", $content);
				$content = str_replace("â€˜","'", $content);
				$content = str_replace("‘", "'", $content);
				$content = str_replace("’", "'", $content);
			/*************************************************************************************************************************
            * 
            *************************************************************************************************************************/
			$pos = strpos($content,"</rss");
			if ($pos===false){
				$pos = strpos($content,"</rdf:RDF");
			}
			if ($pos===false){
			} else {
				$pos = strpos($content,">",$pos);
				$content = substr($content,0,$pos+1);
			}
			$str = str_replace(
				Array("<title>", "</title>", "<description>", "</description>", "<link>", "</link>", 
						"<language>", "</language>", "<webMaster>", "</webMaster>", "<managingEditor>",
						"</managingEditor>", "<pubDate>", "</pubDate>", "<lastBuildDate>", "</lastBuildDate>", 
						"<url>", "</url>", "<width>", "</width>", "<height>", "</height>"),
				Array("<title><![CDATA[", "]]></title>", "<description><![CDATA[", "]]></description>",
						"<link><![CDATA[", "]]></link>", "<language><![CDATA[", "]]></language>",
						"<webMaster><![CDATA[", "]]></webMaster>", "<managingEditor><![CDATA[", "]]></managingEditor>", 
						"<pubDate><![CDATA[", "]]></pubDate>", "<lastBuildDate><![CDATA[", "]]></lastBuildDate>", "<url><![CDATA[", "]]></url>",
						"<width><![CDATA[", "]]></width>", "<height><![CDATA[", "]]></height>"),
				$content);
			$content = str_replace(
				Array("<![CDATA[<![CDATA[", "]]>]]>"), 
				Array("<![CDATA[", "]]>"), 
				$str
			);
			$fp = fopen($fname, 'w');
			fwrite($fp, $content);
			fclose($fp);
			$um = umask(0);
			@chmod($fname, LS__FILE_PERMISSION);
			umask($um);
			$_SESSION["preview"] = $fname;
		} else {
			$fcontent = file($fname);
			$content 	= implode("",$fcontent);
		}
		$fields="<fields>
					<field name='bulletlist'><![CDATA[$rss_bulletlist]]></field>
					<field name='number_of_items'><![CDATA[$rss_number_of_items]]></field>
					<field name='override_channel_title'><![CDATA[$rss_override_channel_title]]></field>
					<field name='label'><![CDATA[$rss_label]]></field>";
		for ($i=1;$i<=$tnumberofrss_display_options;$i++){
			$rss_display_options		 	= $this->check_parameters($parameters,"rss_display_options_$i",Array());
			$l = count($rss_display_options);
			for ($index=0;$index<$l;$index++){
				$fields .= "<field name='show'><![CDATA[".$rss_display_options[$index]."]]></field>\n";
			}
		}
			$fields .= "</fields>";
			$filecontent= "<module name=\"RSS\" label=\"\" display=\"RSS\"><feed identifier='$identifier'>".$fields."</feed>\n".$content."\n</module>";
			$filecontent = str_replace (Array('£', '’'),Array("&#163;", "'"),$filecontent);
		if ($content==""){
			$domDoc = false;
		} else if (function_exists('domxml_open_mem')){
//			$domDoc = @domxml_open_mem($filecontent);
			$domDoc = true;
		} else {
			$domDoc = true;
		}
		
		if(!$domDoc) {
			print "<style>
				body{font-size:0.9em;}
				.TableHeader{font-weigth:bold;border:1px solid #999999;background:#cccccc;padding:3px}
				.storyitem{margin:4px;xborder-bottom:2px solid #cccccc}
				.contentpos{margin:4px;}
			</style><table width=100%><tr><td><h1 class='tableHeader'>Sorry Unable to use this Feed</h1>
			<p>Sorry there was a problem with the feed the form below is to an external RSS feed validator service. If you wish to check the validation of this feed.</p>
			<h2><a target=__externalrssfeedvaliditor href='http://feedvalidator.org'>Feed Validator.org</a></h2>
<form id=validation target=__externalrssfeedvaliditor  action=http://feedvalidator.org/check.cgi method=get>
<div>
<input name=url id=url type=text size=55 maxlength=255 value='$rss_url' />&nbsp;<input type=submit value=Validate />
</div>
</form>
			</td></tr></table>";
		} else {
			$out = $this->convert_xml_2_xhtml(Array(""=>0, "string"=>$filecontent, "label"=>$rss_label));
			$out = substr($out,strpos($out,'</label>')+8);
			$out = substr($out,0,strlen($out)-20);
			$out = str_replace(
				Array(
					'xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" xmlns:syn="http://purl.org/rss/1.0/modules/syndication/"',
					'src="'.$this->parent->base.'http://'
				), Array(
					'',
					'src="http://'
				),$out);
			print "<style>
			body{font-size:0.9em;}
			.TableHeader{font-weigth:bold;border:1px solid #999999;background:#cccccc;padding:3px}
			.storyitem{margin:4px;xborder-bottom:2px solid #cccccc}
			.contentpos{margin:4px;}
			</style><table width=250px><tr><td>$out</td></tr></table>";
		}
		print "<script>
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
		$this->exitprogram();
		return $filecontent;
	}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-											I N T E R N A L   F E E D   F U N C T I O N S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: module_internal_list($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_internal_list($parameters){
		$sql = "
select 
	distinct 
		rss_feed.*
	from rss_feed
	where 
		rss_feed.rss_client=$this->client_identifier and 
		rss_external=0
	order by rss_feed.rss_identifier desc
";

		$out = "";
		$result = $this->parent->db_pointer->database_query($sql);
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$this->page_size=50;
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
				Array("ADD",$this->module_command."INTERNAL_ADD_FEED", ADD_NEW)
			);
			
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["HEADER"]			= "Internal RSS Feeds";
			$variables["START"]				= $goto;
			$variables["as"]				= "table";
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
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
				$counter++;
				$identifier = $r["rss_identifier"];
				$index=count($variables["RESULT_ENTRIES"]);
				$rss_frequency = $r["rss_frequency"];
				$rss_frequency_label="Live";
				for ($i=0;$i<count($this->freq_list);$i++){
					if($rss_frequency==$this->freq_list[$i][0]){
						$rss_frequency_label = $this->freq_list[$i][1];
					}
				}
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["rss_identifier"],
					"ENTRY_BUTTONS"	=> Array(
						Array("EDIT",$this->module_command."INTERNAL_EDIT_FEED",EDIT_EXISTING),
						Array("REMOVE",$this->module_command."INTERNAL_REMOVE_FEED",REMOVE_EXISTING)
					),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		$this->check_parameters($r,"rss_label",""),"TITLE"),
						Array("Reload Frequency",	$rss_frequency_label),
						Array("Status",			$r["rss_status"]),
						//Array("Open in External",   ($r["rss_new_window"]==0?LOCALE_NO:LOCALE_YES)),
						Array("Failed to load",	$r["rss_error_count"]." times"),
						
						
					)
				);
				if($r["rss_extractable"]==1){
					$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array("Feed for 3rd party",	"<a href='http://".$this->parent->domain."".$this->parent->base."-rss/".$this->make_uri($this->check_parameters($r,"rss_label","")).".xml' target='_external'><img src='/libertas_images/general/iconification/rss20.gif' border='0'/></a>");
				} else {
					$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array("Feed for 3rd party",	"[[nbsp]]");
				}
			}
			$this->page_size = $prev;
			
			return $this->generate_list($variables);
		}
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: modify_internal_entry($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function modify_internal_entry($parameters){
		$identifier 				= $this->check_parameters($parameters,"identifier",-1);
		$form_label 				= LOCALE_ADD;
		$rss_label					= "";
		$rss_description			= "";
		$rss_url					= "";
		$rss_status					= "0";
		$rss_optout					= 0;
		$rss_frequency				= 2419200;
		$menu_parent				= -1;
		$rss_bulletlist				= 0;
		$rss_number_of_items		= 10;
		$all_locations				= 0;
		$set_inheritance			= 0;
		$menu_locations				= Array();
		$rss_extractable			= 0;
		$extract_all_locations		= 0;
		$extract_set_inheritance	= 0;
		$extract_menu_locations		= Array();
		$rss_digital_desktop		= 0;
		$rss_override_channel_title	= 0;
		$src						= "/libertas_images/themes/1x1.gif";
		$src_w						= 1;
		$src_h						= 1;
		$rss_type					= -1;
		$rss_channel_image			= -1;
		$menu_counter				= "";
		$display_tab				= $this->check_parameters($parameters,"display_tab");
//			"Extra"		=> Array("Bullet list at top" => 0),
		$rsslist=Array(
			"Channel"	=> Array(
				"Title"				=> 0,
				"Image"				=> 0,
				"Description"		=> 0,
				"Copyright"			=> 0,
				"Last Build Date"	=> 0,
//				"Categories"		=> 0,
//				"Managing Editor"	=> 0,
//				"Web Master"		=> 0,
//				"Publish Date"		=> 0
			),
			"Story"		=> Array(
				"Title"				=> 0,
//				"Attachments"		=> 0,
				"Publish Date"		=> 0,
//				"Author"			=> 0,
//				"Categories"		=> 0,
				"Description"		=> 0,
//				"Comments Url"		=> 0
			)
		);
		if ($identifier!=-1){
			$form_label 	= LOCALE_EDIT;
			$sql			= "select * from rss_feed where rss_client=$this->client_identifier and rss_identifier=$identifier";
			$result = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$rss_label					= $r["rss_label"];
				$rss_url					= $r["rss_url"];
				$rss_status					= $r["rss_status"];
				$rss_frequency				= $r["rss_frequency"];
				$rss_optout					= $r["rss_optout"];
				$rss_number_of_items		= $r["rss_number_of_items"];
				$rss_bulletlist				= $r["rss_bulletlist"];
				$all_locations				= $r["rss_all_locations"];
				$set_inheritance			= $r["rss_set_inheritance"];
				$extract_all_locations		= $r["rss_extract_all_locations"];
				$extract_set_inheritance	= $r["rss_extract_set_inheritance"];
				$rss_digital_desktop		= $r["rss_digital_desktop"];
				$rss_override_channel_title	= $r["rss_override_channel_title"];
				$rss_description			= $r["rss_description"];
				$rss_type					= $r["rss_type"];
				$rss_extractable			= $r["rss_extractable"];
				$rss_channel_image			= $r["rss_channel_image"];
			}
			$this->parent->db_pointer->database_free_result($result);
			$sql = "select * from rss_feed_fields where rff_client=$this->client_identifier and rff_feed=$identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$field = $r["rff_field"];
				$sfield = split("_",$field);
				if (count($sfield)==2){
					$rsslist[$sfield[0]][$sfield[1]] = $r["rff_on"];
				}
			}
			$this->parent->db_pointer->database_free_result($result);
			$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
				Array(
					"module"		=> "RSS_",
					"identifier"	=> $identifier,
					"publish"		=> 1
				)
			);
			$menus = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
				Array(
					"module"		=> "RSS_",
					"identifier"	=> $identifier,
					"publish"		=> 0
				)
			);
			$extract_menu_locations = $menus["menus"];
			for ($i=0;$i<count($extract_menu_locations);$i++){
				$menu_counter	.= "<counter menu='".$extract_menu_locations[$i]."'>".$menus["counters"][$i]."</counter>";
			}
		}
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[".MANAGE_RSSFEEDS." - ".$form_label."]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."INTERNAL_LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."INTERNAL_SAVE_ENTRY\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<page_sections>";
		$out .="			<section label='".LOCALE_RSS_FEED."'>";
		$rss_types = Array(LOCALE_RSS_INTERNAL_TYPE_1, LOCALE_RSS_INTERNAL_TYPE_2, LOCALE_RSS_INTERNAL_TYPE_3);
		$out .="				<select name='rss_type' label='".LOCALE_RSS_INTERNAL_TYPE."' onchange='manageMirrorTypes(this)'>";
		$out.="					<option value='0'><![CDATA[".LOCALE_CHOOSE_ONE."]]></option>";
		for ($i=0; $i<count($rss_types); $i++){
			$out.="					<option value='".($i+1)."'";
			if ($this->get_constant("LOCALE_RSS_INTERNAL_TYPE_$rss_type")==$rss_types[$i]){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".$rss_types[$i]."]]></option>";
		}
		$out .="				</select>";
		$out .="				<input type='text' required='YES' name='rss_feed_label' label='".LOCALE_LABEL."' size='255'><![CDATA[$rss_label]]></input>";
		$out .="				<textarea required='YES' name='rss_description' label='".LOCALE_DESCRIPTION."' width='40' height='5'><![CDATA[$rss_description]]></textarea>";
		$out .="				<input type='hidden' name='rss_optout' value='0'/>";
		$out .="				<select name='rss_feed_download_frequency' label='".LOCALE_RSS_DOWNLOAD_FREQUENCY."'>";
		$out.="					<option value='".LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_0_VAL."'><![CDATA[".LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_0_TXT."]]></option>";
		
		for ($i=0;$i<count($this->freq_list);$i++){
			$out.="					<option value='".$this->freq_list[$i][0]."'";
			if($rss_frequency==$this->freq_list[$i][0]){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".$this->freq_list[$i][1]."]]></option>";
		}
		$out .="				</select>";

		$out .="				<radio name='rss_status' label='".LOCALE_STATUS."'>";
			$out.="					<option value='1'";
			if($rss_status==1){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".STATUS_LIVE."]]></option>";
			$out.="					<option value='0'";
			if($rss_status==0){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".STATUS_NOT_LIVE."]]></option>";
		$out .="				</radio>";

		$out .="				<radio name='rss_extractable' label='".LOCALE_EXTRACTABLE."'>";
			$out.="					<option value='1'";
			if($rss_extractable==1){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".LOCALE_YES."]]></option>";
			$out.="					<option value='0'";
			if($rss_extractable==0){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".LOCALE_NO."]]></option>";
		$out .="				</radio>";
		/**
		* Display type of list
		*/
		$d = $this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>"RSS_", "identifier"=>$identifier));
		$web_containers = split("~----~",$d);
		if ($web_containers[0]!=""){
			$out .=	"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= "<checkboxes name='rss_containers' type='vertical' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}
		$out .="			</section>";
		$extraout ="<input type='hidden' name='rss_digital_desktop' value='0'/>";
/*
		$extraout ="				<radio name='rss_digital_desktop' label='".LOCALE_RSS_AVAILABLE_ON_DESKTOP."' type='horizontal'>";
			$extraout.="					<option value='1'";
			if($rss_digital_desktop==1){
				$extraout.=					" selected='true'";
			}
			$extraout.=					"><![CDATA[".LOCALE_YES."]]></option>";
			$extraout.="					<option value='0'";
			if($rss_digital_desktop==0){
				$extraout.=					" selected='true'";
			}
			$extraout.=					"><![CDATA[".LOCALE_NO."]]></option>";
		$extraout .="				</radio>";
*/
		
		$override = Array(
			"label"		=> LOCALE_EXTRACT_LOCATIONS,
			"name"		=> "extract",
			"locale"	=> "EXTRACT_",
			"hidden"	=> (($rss_type==0)?1:0),
			"type"		=> $rss_type,
			"counter"	=> $menu_counter
		);
		$out .= $this->location_tab($extract_all_locations, $extract_set_inheritance, $extract_menu_locations, $display_tab, "", $override);
		$out .= $this->location_tab($all_locations, $set_inheritance,$menu_locations, $display_tab, $extraout);
		
		
		
		
		$out .="			<section label='".LOCALE_RSS_DISPLAY_OPTIONS."'>";
		$out .="				<input type='text' size='3' name='rss_number_of_items' label='".LOCALE_RSS_NUMBER_OF_ITEMS."' format='number'";
		if ($rss_type==2){
			$out .= " hidden='YES'";
		}
		$out.="><![CDATA[$rss_number_of_items]]></input>";
		$out .="				<radio name='rss_bulletlist' label='".LOCALE_RSS_BULLET_LIST."'>";
			$out.="					<option value='0'";
			if($rss_bulletlist==0){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".LOCALE_NO."]]></option>";
			$out.="					<option value='1'";
			if($rss_bulletlist==1){
				$out.=					" selected='true'";
			}
			$out.=					"><![CDATA[".LOCALE_YES."]]></option>";
		$out .="				</radio>";
		$out .="<text><![CDATA[".LOCALE_RSS_CHOOSE_FIELDS_MSG."]]></text>";
		$out .="				<checkboxes name='rss_display_options' label='".LOCALE_RSS_CHOOSE_FIELDS."' type='horizontal'>";
		foreach($rsslist as $key => $rlist){
			$out.="<options module='$key Fields'>";
			foreach($rlist as $rkey => $rval){
				$out.="					<option value='".$key."_".$rkey."'";
				if($rval==1 || ($identifier==-1 && ($key."_".$rkey == "Channel_Title" || $key."_".$rkey == "Channel_Image" ||  $key."_".$rkey == "Story_Description" ||  $key."_".$rkey == "Story_Title"))){
					$out.=					" selected='true'";
				}
				$out.=					"><![CDATA[".$rkey."]]></option>";
			}
			$out.="</options>";
		}
		$out .="				</checkboxes>";
		$out .="			</section>";
		
		$out .="			<section label='".LOCALE_RSS_CHANNEL_IMAGE."'>";
		$sql = "select * from file_info where file_client=$this->client_identifier and file_identifier=$rss_channel_image";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$src_w = $r["file_width"];
        	$src_h = $r["file_height"];
			$dir_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"]));
        	$src = $this->parent->base.$dir_path.$r["file_md5_tag"].$this->file_extension($r["file_name"]);
        }
        $this->parent->db_pointer->database_free_result($result);
		$out .=	"<text><![CDATA[".LOCALE_RSSADMIN_CHOOSE_CHANNEL_IMAGE."]]></text>
							<input type='hidden' name='rss_channel_image' value='$rss_channel_image'/>
							<cache cache_command='FILES_FILTER' cache_type='image' cache_format='RSS'>
								<cache_img src='$src' width='$src_w' height='$src_h'/>
								<filters>
									<setting show='no'>type=image</setting>
									<setting show='no'>maxwidth=144</setting>
									<setting show='no'>maxheight=400</setting>
								</filters>
							</cache>
				";
		$out .="			</section>";
		$out .= $this->preview_section('RSSADMIN_INTERNAL_PREVIEW');
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	
	function save_internal_feed($parameters){
		$tnumberofrss_display_options	= $this->check_parameters($parameters,"totalnumberofchecks_rss_display_options",0);
		$identifier 				 	= $this->check_parameters($parameters,"identifier",-1);
		$rss_label						= htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"rss_feed_label"))));
		$rss_description				= htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"rss_description"))));
		$rss_type						= $this->check_parameters($parameters,"rss_type"					,0);
		$rss_status						= $this->check_parameters($parameters,"rss_status"					,0);
		$rss_optout						= $this->check_parameters($parameters,"rss_optout"					,0);
		$rss_number_of_items			= $this->check_parameters($parameters,"rss_number_of_items"			,10);
		$rss_created_by 				= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER"		,0);
		$rss_digital_desktop			= $this->check_parameters($parameters,"rss_digital_desktop"			,0);
		$rss_frequency					= $this->check_parameters($parameters,"rss_feed_download_frequency"	,LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_6_VAL);
		$all_locations					= $this->check_parameters($parameters,"all_locations"				,0);
		$menu_locations					= $this->check_parameters($parameters,"menu_locations");
		$set_inheritance				= $this->check_parameters($parameters,"set_inheritance"				,0);
		$extract_all_locations			= $this->check_parameters($parameters,"extractall_locations"		,0);
		$extract_set_inheritance		= $this->check_parameters($parameters,"extractset_inheritance"		,0);
		$currentlyhave					= $this->check_parameters($parameters,"currentlyhave");
		$count_rss_containers			= $this->check_parameters($parameters,"totalnumberofchecks_rss_containers");
		$rss_override_channel_title		= $this->check_parameters($parameters,"rss_override_channel_title"	,0);
		$extractlocations_type			= $this->check_parameters($parameters,"extractlocations_type"		,0);
		$rss_bulletlist					= $this->check_parameters($parameters,"rss_bulletlist"				,0);
		$rss_extractable				= $this->check_parameters($parameters,"rss_extractable"				,0);
		$rss_channel_image				= $this->check_parameters($parameters,"rss_channel_image"			,0);
		if ($rss_channel_image<0){
			$rss_channel_image=0;
		}
		$extractmenu_locations			= $this->check_parameters($parameters,"extractmenu_locations");
		$extractmenu_locations_numbers	= Array();
		if ($rss_type==2){
			$l = count($extractmenu_locations);
			for ($i = 0; $i<$l; $i++){
				$extractmenu_locations_numbers["number_".$extractmenu_locations[$i]] = $this->check_parameters($parameters,"ecml_".$extractmenu_locations[$i]."number");
			}
		}
		$replacelist=Array();
		$replacelist	= $this->check_parameters($parameters,"rss_containers",Array());
		/*
		for($index=1 ; $index <= $count_rss_containers; $index++){
			$rss_containers	= $this->check_parameters($parameters,"rss_containers_$index",Array());
			$len = count($rss_containers);
			for($i=0;$i < $len; $i++){
				$replacelist[count($replacelist)] = $rss_containers[$i];
			}
		}
		*/
		$rss_date_created	= $this->libertasGetDate("Y/m/d H:i:s");
		$rss_downloaded		= $this->libertasGetTime();
		if ($identifier==-1){
			// Add
			$sql = "insert into rss_feed (rss_client, rss_optout, rss_label, rss_description, rss_status, rss_number_of_items , rss_created_by, rss_date_created, rss_all_locations, rss_set_inheritance, rss_extract_all_locations, rss_extract_set_inheritance, rss_downloaded, rss_external, rss_type, rss_bulletlist, rss_extractable, rss_channel_image, rss_frequency)
						 values
				($this->client_identifier, $rss_optout, '$rss_label', '$rss_description', $rss_status, '$rss_number_of_items', $rss_created_by, '$rss_date_created', '$all_locations', '$set_inheritance', '$extract_all_locations', '$extract_set_inheritance', '$rss_downloaded', 0, $rss_type, $rss_bulletlist, $rss_extractable, $rss_channel_image, $rss_frequency)";
			$this->parent->db_pointer->database_query($sql);
			$sql = "select rss_identifier from rss_feed where 
						rss_set_inheritance			= '$set_inheritance' and 
						rss_bulletlist				= '$rss_bulletlist' and 
						rss_extractable				= '$rss_extractable' and 
						rss_all_locations			= '$all_locations' and 
						rss_extract_set_inheritance	= '$extract_set_inheritance' and 
						rss_extract_all_locations	= '$extract_all_locations' and 
						rss_optout					= $rss_optout and 
						rss_label					= '$rss_label' and 
						rss_description				= '$rss_description' and 
						rss_status					= $rss_status and  
						rss_number_of_items			= $rss_number_of_items and 
						rss_client 					= $this->client_identifier and 
						rss_date_created			= '$rss_date_created' and 
						rss_created_by				= $rss_created_by and
						rss_frequency				= '$rss_frequency' and
						rss_external				= 0
			";

			$result  = $this->parent->db_pointer->database_query($sql);
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$identifier = $r["rss_identifier"];
			}
			$this->parent->db_pointer->database_free_result($result);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $rss_label,
					"wo_command"	=> "RSS_DISPLAY",
					"cmd"			=> "ADD",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);

		} else {
			// Edit
			$sql = "update rss_feed set 
						rss_optout					= $rss_optout, 
						rss_label					= '$rss_label', 
						rss_description				= '$rss_description', 
						rss_extractable				= '$rss_extractable',
						rss_bulletlist				= '$rss_bulletlist',
						rss_status					= $rss_status, 
						rss_number_of_items 		= '$rss_number_of_items',
						rss_all_locations			= '$all_locations',
						rss_set_inheritance			= '$set_inheritance',
						rss_extract_all_locations	= '$extract_all_locations',
						rss_extract_set_inheritance	= '$extract_set_inheritance',
						rss_digital_desktop			= '$rss_digital_desktop',
						rss_override_channel_title	= $rss_override_channel_title,
						rss_type					= $rss_type,
						rss_channel_image			= $rss_channel_image,
						rss_frequency				= $rss_frequency
					where 
						rss_client = $this->client_identifier and
						rss_external=0 and 
						rss_identifier = $identifier";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $rss_label,
					"wo_command"	=> "RSS_DISPLAY",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
		}
		$sql = "delete from rss_feed_fields where rff_feed=$identifier and rff_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		for ($i=1;$i<=$tnumberofrss_display_options;$i++){
			$rss_display_options		 	= $this->check_parameters($parameters,"rss_display_options_$i",Array());
			$l = count($rss_display_options);
			for ($index=0;$index<$l;$index++){
				$sql = "insert into rss_feed_fields (rff_feed, rff_client , rff_field, rff_on)values ($identifier, $this->client_identifier, '".$rss_display_options[$index]."', 1)";
				$this->parent->db_pointer->database_query($sql);
			}
		}
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> "RSS_",
				"identifier"	=> $identifier,
				"all_locations"	=> $all_locations,
				"publish"		=> 1
			)
		);
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $extractmenu_locations,
				"module"		=> "RSS_",
				"identifier"	=> $identifier,
				"all_locations"	=> $extract_all_locations,
				"publish"		=> 0,
				"numbers"		=> $extractmenu_locations_numbers
			)
		);
/*
		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance("RSS_DISPLAY",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"	=> $child_locations,
					"module"			=> "RSS_",
					"identifier"		=> $identifier,
					"all_locations"		=> $all_locations,
					"delete"			=> 0,
					"publish"			=> 0
				)
			);
			$this->set_inheritance(
				"RSS_DISPLAY",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"		=> "RSS_",
					"condition"		=> "rss_set_inheritance =1 and ",
					"client_field"	=> "rss_client ",
					"table"			=> "rss_feed",
					"primary"		=> "rss_identifier"
					)
				).""
			);
		}
		*/
		$this->tidyup_display_commands($parameters);
		$this->call_command($this->module_command."INTERNAL_CACHE",Array("identifier"=>$identifier,"extractable"=>$rss_extractable));
	}
	
	function cache_internal_rss($parameters){
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$identifier = $this->check_parameters($parameters,"identifier");
		$this->call_command("RSS_CACHE", Array("identifier"=>$identifier));

	}
	
	function remove_internal_feed($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$rss_extractable	= 0;
		$rss_label			= "";
		$sql = "select * from rss_feed where rss_identifier=$identifier and rss_external=0 and rss_client = $this->client_identifier";
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$rss_label = $r["rss_label"];
        	$rss_extractable = $r["rss_extractable"];
        }
        $this->parent->db_pointer->database_free_result($result);

		$sql = "delete from rss_feed_fields where rff_feed=$identifier and rff_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from rss_feed where rss_identifier=$identifier and rss_external=0 and rss_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$this->call_command("LAYOUT_MENU_TO_OBJECT_REMOVE", Array(
				"module"		=>	"RSS_", 
				"identifier"	=>	$identifier
			)
		);
		$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
				"owner_module" 	=> $this->webContainer,
				"owner_id" 		=> $identifier,
				"cmd"			=> "REMOVE"
			)
		);
		@unlink($data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml");
		@unlink($data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xhtml");
		if ($rss_extractable!=0 and $rss_label != ""){
			$root = $this->parent->site_directories["ROOT"];
			@unlink($root."/-rss/".$this->make_uri($rss_label).".xml");
		}
	}	

	function preview_internal($parameters){
		$tnumberofrss_display_options	= $this->check_parameters($parameters	,"totalnumberofchecks_rss_display_options"	,0);
		$rss_type						= $this->check_parameters($parameters	,"rss_type"									,0);
		$identifier 				 	= $this->check_parameters($parameters	,"identifier"								,-1);
		$rss_label						= html_entity_decode(strip_tags($this->validate($this->check_parameters($parameters		,"rss_feed_label"))));
		$rss_url						= htmlentities(strip_tags($this->validate($this->check_parameters($parameters		,"rss_feed_url"))));
		$rss_description				= htmlentities(strip_tags($this->validate($this->check_parameters($parameters		,"rss_description"))));
		$rss_frequency					= $this->check_parameters($parameters	, "rss_feed_download_frequency"				,2419200);
		$rss_status						= $this->check_parameters($parameters	, "rss_status"					,0);
		$rss_optout						= $this->check_parameters($parameters	, "rss_optout"					,0);
		$rss_number_of_items			= $this->check_parameters($parameters	, "rss_number_of_items"			,10);
		$rss_bulletlist					= $this->check_parameters($parameters	, "rss_bulletlist"				,0);
		$rss_created_by 				= $this->check_parameters($_SESSION		, "SESSION_USER_IDENTIFIER"		,0);
		$rss_digital_desktop			= $this->check_parameters($parameters	, "rss_digital_desktop"			,0);
		$all_locations					= $this->check_parameters($parameters	, "all_locations"				,0);
		$menu_locations					= $this->check_parameters($parameters	, "menu_locations"				,"");
		$currentlyhave					= $this->check_parameters($parameters	, "currentlyhave"				,"");
		$count_rss_containers			= $this->check_parameters($parameters	, "totalnumberofchecks_rss_containers");
		$set_inheritance				= $this->check_parameters($parameters	, "set_inheritance"				,0);
		$rss_override_channel_title		= $this->check_parameters($parameters	, "rss_override_channel_title"	,0);
		$rss_channel_image				= $this->check_parameters($parameters	, "rss_channel_image"			,0);
		$extract_all_locations			= $this->check_parameters($parameters	,"extractall_locations"		,0);
		$extractmenu_locations			= $this->check_parameters($parameters	,"extractmenu_locations");
//		$this->call_command("RSS_CACHE",Array("store"=>0));
		$option_list = Array();
		for ($i=1;$i<=$tnumberofrss_display_options;$i++){
			$rss_display_options		 	= $this->check_parameters($parameters,"rss_display_options_$i",Array());
			$l = count($rss_display_options);
			for ($index=0;$index<$l;$index++){
				$option_list[count($option_list)] = $rss_display_options[$index];
			}
		}
		$img="";
		$channel="";
		if (in_array("Channel_Title",$option_list)){
			$channel ="<div class='TableHeader'><a href='http://".$this->parent->domain.$this->parent->base."'>$rss_label</a></div>";
		}
		if (in_array("Channel_Image",$option_list)){
			if ($rss_channel_image>0){
				$sql = "select * from file_info where file_client=$this->client_identifier and file_identifier=$rss_channel_image";
				$result  = $this->parent->db_pointer->database_query($sql);
		        while($r = $this->parent->db_pointer->database_fetch_array($result)){
		        	$src_w = $r["file_width"];
		        	$src_h = $r["file_height"];
					$dir_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"]));
		        	$src = $this->parent->base.$dir_path.$r["file_md5_tag"].$this->file_extension($r["file_name"]);
		        }
				$channel .= "<img src='$src' width='$src_w' height='$src_h' align='right'/>";
			}
		}
		if (in_array("Channel_Description",$option_list)){
			$channel .="<div class='contentpos'>$rss_description</div>";
		}
		if (in_array("Channel_Last Build Date",$option_list)){
		$channel.="<div class='contentpos'>Last Build :: ".$this->libertasgetDate("Y/m/d H:i:s")."</div>";
		}
		if(strlen($channel)!=0){
		//	$channel = "<div class='channel'>$channel</div>";
		}
				$filecontent="<module name='RSS' label='' display='TEXT'>
		<text><![CDATA[
		";
		//"<div class='channel'><div class='TableHeader'><a href='http://".$this->parent->domain.$this->parent->base."'>$rss_label</a></div>$img<div class='contentpos'>$rss_description</div><div class='contentpos'>Last Build :: ".$this->libertasgetDate("Y/m/d H:i:s")."</div></div>",
		if ($rss_type==3){
			$filecontent.=$this->load_random(
				Array(
					"xlocations" 		=> $extractmenu_locations,
					"xchannel"			=> $channel,
					"xfields"			=> $option_list,
					"number_of_items"	=> $rss_number_of_items,
					"extract_all"		=> $extract_all_locations,
					"extra"				=> $parameters
				)
			);
		} else if ($rss_type==2){
			$filecontent.=$this->load_accumulitive(Array(
					"xlocations" 		=> $extractmenu_locations,
					"xchannel"			=> $channel,
					"xfields"			=> $option_list,
					"number_of_items"	=> $rss_number_of_items,
					"extract_all"		=> $extract_all_locations,
					"extra"				=> $parameters
				)
			);
		} else{
			$filecontent.=$this->load_latest(Array(
					"xlocations" 		=> $extractmenu_locations,
					"xchannel"			=> $channel,
					"xfields"			=> $option_list,
					"number_of_items"	=> $rss_number_of_items,
					"extract_all"		=> $extract_all_locations,
					"extra"				=> $parameters
				)
			);
		}
/*	for($i=0;$i<10;$i++){
		$title			 = "asdf";
		$date			 = $this->libertasgetDate("Y/m/d H:i:s");
		$description	 = "asdfasdfasdfasdf asdf asdfs";
		$filecontent 	.= "<div class='storyitem'><a name='#jump_to_fake_$i'></a><a href='http://".$this->parent->domain.$this->parent->base."/'><div class='StoryHeader'>".$title."</div></a>
			<div>Date :: $date</div>
			<div>$description</div>
			<div class='readmore'><a class='headlines' href='[[script]]#jump_to_fake'>Back to top</a></div>
		</div>";
	}*/
	$filecontent.="]]></text></module>";
	return $filecontent;
	return "";
	}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-													O T H E R   F U N C T I O N S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function tidyup_display_commands($parameters){
		$debug = $this->debugit(false, $parameters);
		$all_locations = $this->check_parameters($parameters,"all_locations",0);
		$sql ="select * from rss_feed where rss_client=$this->client_identifier and rss_all_locations=1";
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$result  = $this->parent->db_pointer->database_query($sql);
    	$num = $this->call_command("DB_NUM_ROWS",Array($result));
		if ($num==0){
			$sql ="select distinct menu_to_object.mto_menu as m_id from menu_to_object where mto_client=$this->client_identifier and mto_module='RSS_'";
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>"RSS_DISPLAY","status"=>"OFF"));
		} else {
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>"RSS_DISPLAY","status"=>"ON"));
			$sql ="select distinct menu_identifier as m_id from menu_data where menu_client=$this->client_identifier";
		}
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$result  = $this->parent->db_pointer->database_query($sql);
   		$sql = "delete from display_data where display_client=$this->client_identifier and display_command='RSS_DISPLAY'";
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$this->parent->db_pointer->database_query($sql);
   		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$sql = "insert into display_data (display_client, display_command, display_menu) values ($this->client_identifier, 'RSS_DISPLAY', ".$r["m_id"].")";
			if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
			$this->parent->db_pointer->database_query($sql);
       	}
		$this->parent->db_pointer->database_free_result($result);
	}

	function convert_xml_2_xhtml($parameters){
		$out="";
		$label  = $this->check_parameters($parameters,"label");
		$dateformat = $this->check_prefs(Array("sp_default_time_format", "default" => "DDxx MMMM YYYY", "module" => "SYSPREFS_", "options" => "DDxx MMMM YYYY:d, DD MMM YYYY"));
		$string = str_replace(Array("&lt;","&gt;","&quot;","<feed"), Array("<",">",'"',"<settings name='sp_default_time_format'><![CDATA[$dateformat]]></settings><feed"),htmlentities($this->check_parameters($parameters,"string","")));
		if ($string!=""){
			$this->call_command("XMLPARSER_LOAD_XML_STR",array($string));
			$xsl_dir = $this->parent->site_directories["XSL_THEMES_DIR"];
			$file = $xsl_dir."/stylesheets/themes/site_administration/rss2xhtml.xsl";
			//print $file;
			$this->call_command("XMLPARSER_LOAD_XSL_FILE",array($file));
			$txt = $this->call_command("XMLPARSER_TRANSFORM");
			if (strlen($txt)>1){
				$x = strpos($txt,'<',2);
				$out ='<module name="RSS" display="TEXT"><label>'.$label.'</label><text><![CDATA['.substr($txt,$x-1).']]></text></module>';
			}
		} 
		return $out;
	}

	function load_latest($parameters){
		$xlocations				= $this->check_parameters($parameters,"xlocations");
		$xchannel				= $this->check_parameters($parameters,"xchannel");
		$xfields				= $this->check_parameters($parameters,"xfields");
		$number_of_items		= $this->check_parameters($parameters,"number_of_items");
		$extract_all			= $this->check_parameters($parameters,"extract_all");
		$extra					= $this->check_parameters($parameters,"extra");
		$rss_bulletlist			= $this->check_parameters($extra	, "rss_bulletlist"				,0);
		$extractmenu_locations	= $this->check_parameters($extra,"extractmenu_locations",Array());
		$out 					= "";
		$xout 					= "";
		$xlist 					= "";
		$join					= "";
		$where 					= "";
		$summary_parts 			= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", 
			Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary"
			)
		);
		if ($extract_all==0){
			$menu_list = join(", ",$extractmenu_locations);
			if (strlen($menu_list)>0){
				$where .= " menu_access_to_page.menu_identifier in ($menu_list) and ";
			}
			$sql = "
			select distinct menu_data.menu_identifier,menu_data.menu_url, page_trans_data.*, ".$summary_parts["return_field"]." 
				from menu_access_to_page 
				inner join menu_data on menu_data.menu_identifier= menu_access_to_page.menu_identifier and menu_data.menu_client = menu_access_to_page.client_identifier 
				inner join page_trans_data on 
					menu_access_to_page.trans_identifier = page_trans_data.trans_identifier and 
					trans_client = menu_access_to_page.client_identifier and 
					trans_published_version =1 and 
					trans_doc_status =4 
				".$summary_parts["join"]."
			where 
				$where
				menu_access_to_page.client_identifier=$this->client_identifier 
				".$summary_parts["where"]."
				order by trans_date_available desc 				
			";
		} else {
			$sql = "select distinct menu_data.menu_identifier,menu_data.menu_url, page_trans_data.*,".$summary_parts["return_field"]." from page_trans_data 
					inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier and trans_client = menu_access_to_page.client_identifier and trans_published_version =1 and trans_doc_status =4
					inner join menu_data on menu_data.menu_identifier= menu_access_to_page.menu_identifier and menu_data.menu_client = menu_access_to_page.client_identifier
					".$summary_parts["join"]."
					$join
				where $where trans_client=$this->client_identifier ".$summary_parts["where"]."
				order by trans_date_available desc";
		}
		$c=0;
		//if($_SERVER['REMOTE_ADDR'] == "202.154.241.147")
		//print "<li>".__FILE__."@".__LINE__."<pre>$sql</pre></li>";
		
		$result 	= $this->parent->db_pointer->database_query($sql);
		$d			= "";
		$url		= "";
		$have 		= Array();
		$blistitem="";
		$x="";
		$d = $this->libertasGetDate("Y/m/d H:i:s");
		while(($r	= $this->parent->db_pointer->database_fetch_array($result)) && count($have)<$number_of_items){
			if($c==0){
				$d = $r["trans_date_publish"];
				$c++;
			}
			
			$trans_title	= chop($this->check_parameters($r,"trans_title",""));
//			$trans_id	 	= $this->check_parameters($r,"trans_identifier","");
			$trans_page 	= $this->check_parameters($r,"trans_page","");
			$menu_url 		= $this->check_parameters($r,"menu_url");
			$menu_id 		= $this->check_parameters($r,"menu_identifier",-1);
			if (!in_array($trans_page, $have)){
	//			$url = $this->translate_to_filename($menu_url,$trans_title,$trans_page,$menu_id);
	
				$have[count($have)] = $trans_page;
				$url = "";
    	   		$out.= "<item>\n";
	       		$out.= "	<title>".html_entity_decode($r["trans_title"])."</title>\n";
       			$out.= "	<link>http://".$this->parent->domain.$this->parent->base.$url."</link>\n";
				$out.= "	<description>".$r["trans_summary1"]."</description>\n";
				$out.= "	<pubDate>".$this->libertasGetDate("r",strtotime($r["trans_date_publish"]))."</pubDate>\n";
				$out.= "</item>\n";
				if ($rss_bulletlist==1){
					$blistitem	.=	"<li><a href='[[script]]#jump_to_fake_".$c."'>".html_entity_decode($r["trans_title"])."</a></li>";
				}
				$x	.=	"<div class='storyitem'><a name='#jump_to_fake_".$c."'></a>";
				if (in_array("Story_Title",$xfields)){
					$x .=	"	<a href='http://".$this->parent->domain.$this->parent->base.$url."'><div class='StoryHeader'>".html_entity_decode($r["trans_title"])."</div></a>";
				}
				if (in_array("Story_Publish Date",$xfields)){
					$x .=	"	<div>Date ::".$this->libertasGetDate("r",strtotime($r["trans_date_publish"]))."</div>";
				}
				if (in_array("Story_Description",$xfields)){
					$x .=	"	<div>".html_entity_decode($r["trans_summary1"])."</div>";
				}
				$x .=	"</div>";
			}
		}
		if ($this->check_parameters($xfields,"Channel_Last Build Date","__NOT_FOUND__")!="__NOT_FOUND__"){
			$xchannel	.= "<div class='contentpos'>Last Build :: $d</div>";
		}
	    $this->parent->db_pointer->database_free_result($result);
		print "<style>
		body{font-size:0.9em;}
		.TableHeader{font-weigth:bold;border:1px solid #999999;background:#cccccc;padding:3px}
		.storyitem{margin:4px; xborder-bottom:2px solid #cccccc}
		.contentpos{margin:4px;}
		</style><table width=250px><tr><td>".$xchannel."<a name='#jump_to_fake'></a>$blistitem".$x."</td></tr></table>";
		print "<script>
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
		$this->exitprogram();
	}

	function load_accumulitive($parameters){
		$xlocations			= $this->check_parameters($parameters,"xlocations");
		$xchannel			= $this->check_parameters($parameters,"xchannel");
		$xfields			= $this->check_parameters($parameters,"xfields");
		$number_of_items	= $this->check_parameters($parameters,"number_of_items");
		$extract_all		= $this->check_parameters($parameters,"extract_all");
		$extra				= $this->check_parameters($parameters,"extra");
		$rss_bulletlist		= $this->check_parameters($extra	 ,"rss_bulletlist", 0);
		$out 		= "";
		$join		= "";
		$where 		= "";
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary"
			)
		);
		$extractmenu_locations			= $this->check_parameters($extra,"extractmenu_locations");
		$extractmenu_locations_numbers	= Array();
		$l = count($extractmenu_locations);
		for ($i = 0; $i<$l; $i++){
			$extractmenu_locations_numbers["number_".$extractmenu_locations[$i]] = $this->check_parameters($extra,"ecml_".$extractmenu_locations[$i]."number");
		}
		$menu_list = $this->call_command("LAYOUT_DISPLAY_IDS");
		if (strlen($menu_list)>0){
			$where .= " menu_access_to_page.menu_identifier in ($menu_list) and ";
		}
		$sql = "
			SELECT distinct menu_data.menu_url, page_trans_data.*, menu_access_to_page.*, ".$summary_parts["return_field"]." 
				FROM page_trans_data
					inner join menu_access_to_page on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier
					inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier
					".$summary_parts["join"]."
					$join
				where $where trans_published_version = 1 and trans_doc_status=4 ".$summary_parts["where"]."
				order by  menu_access_to_page.menu_identifier, page_trans_data.trans_date_publish desc
			";
		$c=0;
		$result 	= $this->parent->db_pointer->database_query($sql);
		$d			= "";
		$url		= "";
		$have 		= Array();
		$hmenu		= Array();
		$items 		= Array();
		$xitems 	= Array();
		$blist 		= Array();
		while(($r	= $this->parent->db_pointer->database_fetch_array($result))){
			if($c==0){
				$d = $r["trans_date_publish"];
				$c++;
			}
			$menu_id 		= $this->check_parameters($r,"menu_identifier",-1);
			$mto_extract_num= $this->check_parameters($extractmenu_locations_numbers,"number_".$menu_id,1);
			$trans_title	= chop($this->check_parameters($r,"trans_title",""));
			$trans_page 	= $this->check_parameters($r,"trans_page","");
			$menu_url 		= $this->check_parameters($r,"menu_url");
			if (!in_array($trans_page, $have) && $this->check_parameters($hmenu,"menu_$menu_id",-1)<$mto_extract_num){
				$have[count($have)] = $trans_page;
				$url="";
				if ($this->check_parameters($hmenu,"menu_$menu_id",-1)==-1){
					$hmenu["menu_$menu_id"] = 0;
				}
				$hmenu["menu_$menu_id"] ++;
    	   		$o= "<item>\n";
	       		$o.= "	<title>".html_entity_decode($r["trans_title"])."</title>\n";
       			$o.= "	<link>http://".$this->parent->domain.$this->parent->base.$url."</link>\n";
				$o.= "	<description>".$r["trans_summary1"]."</description>\n";
				$o.= "	<pubDate>".$this->libertasGetDate("r",strtotime($r["trans_date_publish"]))."</pubDate>\n";
				$o.= "</item>\n";
				$blistitem="";
				if ($rss_bulletlist==1){
					$blistitem	=	"<li><a href='[[script]]#jump_to_fake_".$c."'>".html_entity_decode($r["trans_title"])."</a></li>";
				}
				$x	=	"<div class='storyitem'><a name='#jump_to_fake_".$c."'></a>";
				if (in_array("Story_Title",$xfields)){
					$x .=	"	<a href='http://".$this->parent->domain.$this->parent->base.$url."'><div class='StoryHeader'>".html_entity_decode($r["trans_title"])."</div></a>";
				}
				if (in_array("Story_Publish Date",$xfields)){
					$x .=	"	<div>Date ::".$r["trans_date_publish"]."</div>";
				}
				if (in_array("Story_Description",$xfields)){
					$x .=	"	<div>".html_entity_decode($r["trans_summary1"])."</div>";
				}
				$x .=	"</div>";
				if ($this->check_parameters($xitems,$r["trans_date_publish"],"")==""){
					$xitems[$r["trans_date_publish"]]="";
				}
				$xitems[$r["trans_date_publish"]] .= $x;
				if ($this->check_parameters($items,$r["trans_date_publish"],"")==""){
					$items[$r["trans_date_publish"]]="";
				}
				$items[$r["trans_date_publish"]] .= $o;
				if ($rss_bulletlist==1){
					if ($this->check_parameters($blist,$r["trans_date_publish"],"")==""){
						$blist[$r["trans_date_publish"]]="";
					}
					$blist[$r["trans_date_publish"]] .= $blistitem;
				}
			}
		}
		krsort($items);
		krsort($xitems);
		$xlist	= "";
		if ($this->check_parameters($xfields,"bulletlist",0)!=0){
			krsort($blist);
			$xlist	= "<div>".implode("",$blist)."</div>";
		}
		$out	= implode("",$items);
		$xout	= implode("",$xitems);
		if ($rss_bulletlist==1){
			$blistout	= implode("",$blist)."<hr/>";
		} else {
			$blistout ="";
		}
		if ($this->check_parameters($xfields,"Channel_Last Build Date","__NOT_FOUND__")!="__NOT_FOUND__"){
			$xchannel	.= "<div class='contentpos'>Last Build :: $d</div>";
		}
	    $this->parent->db_pointer->database_free_result($result);

		print "<style>
		body{font-size:0.9em;}
		.TableHeader{font-weigth:bold;border:1px solid #999999;background:#cccccc;padding:3px}
		.storyitem{margin:4px; xborder-bottom:2px solid #cccccc}
		.contentpos{margin:4px;}
		</style><table width=250px><tr><td>".$xchannel."</div><a name='#jump_to_fake'></a>$blistout$xlist".$xout."</td></tr></table>";
		print "<script>
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
		$this->exitprogram();
	}
	
	function get_prefixes($string){
		if($string==""){
			return Array("");
		} else {
			$prefixes = Array();
			$prefixes[0] = "";
			$list = split("xmlns",$string);
			for($index = 1; $index < count($list); $index++){
				$test = substr($list[$index],0,1);
				if ($test==":"){
					$pos = strpos($list[$index],"=");
					$prefixes[count($prefixes)] = substr($list[$index],1,$pos-1).":";
				}
			}
			return $prefixes;
		}
	}

	/*************************************************************************************************************************
    * generate random entries
    *************************************************************************************************************************/
	function load_random($parameters){
	//	$identifier,$channel, $xchannel, $xfields, $number_of_items, $extract_all, $store, $preview=0
		$xlocations			= $this->check_parameters($parameters,"xlocations");
		$xchannel			= $this->check_parameters($parameters,"xchannel");
		$xfields			= $this->check_parameters($parameters,"xfields");
		$number_of_items	= $this->check_parameters($parameters,"number_of_items");
		$extract_all		= $this->check_parameters($parameters,"extract_all");
		$extra				= $this->check_parameters($parameters,"extra");
		$rss_bulletlist		= $this->check_parameters($extra	 ,"rss_bulletlist", 0);
		$data_files	= $this->parent->site_directories["DATA_FILES_DIR"];
	
//		$fname		= $data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml";
		$out 		= "";
		$xout 		= "";
		$xlist 		= "";
		$join		= "";
		$where 		= "";
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary"
			)
		);
		/*************************************************************************************************************************
        * get menu locations
        *************************************************************************************************************************/
		$extractmenu_locations			= $this->check_parameters($extra,"extractmenu_locations");
		$extractmenu_locations_numbers	= Array();
		$l = count($extractmenu_locations);
		for ($i = 0; $i<$l; $i++){
			$extractmenu_locations_numbers["number_".$extractmenu_locations[$i]] = $this->check_parameters($extra,"ecml_".$extractmenu_locations[$i]."number");
		}
		$menu_list = $this->call_command("LAYOUT_DISPLAY_IDS");
		if (strlen($menu_list)>0){
			$where .= " menu_access_to_page.menu_identifier in ($menu_list) and ";
		}
		
		if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp)){
				$grp_list ="";
				for($i=0,$m=count($grp);$i<$m;$i++){
					$grp_list .= $grp[$i]["IDENTIFIER"].", ";
				}
				$where .= "
					(
						(group_access_to_page.group_identifier is null or group_access_to_page.group_identifier in ($grp_list -1)) or 
						(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1))
					) and ";
			} else {
				$where .= " (group_access_to_page.group_identifier is null) and ";
			}
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier and group_access_to_page.client_identifier = page_trans_data.trans_client
					 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_access_to_page.menu_identifier ";
		}
		if ($extract_all==0){
			$menu_list = $this->call_command("LAYOUT_ANONYMOUS_DISPLAY_IDS");
			if (strlen($menu_list)>0){
				$where .= " menu_access_to_page.menu_identifier in ($menu_list) and ";
			}
			$sql = "select distinct menu_data.menu_identifier,menu_data.menu_url, page_trans_data.*,".$summary_parts["return_field"]." from menu_to_object 
					inner join menu_access_to_page on menu_access_to_page.menu_identifier = mto_menu and menu_access_to_page.client_identifier = mto_client
					inner join menu_data on menu_data.menu_identifier= mto_menu and menu_data.menu_client = mto_client
					inner join page_trans_data on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier and trans_client = menu_access_to_page.client_identifier and trans_published_version =1 and trans_doc_status =4
					".$summary_parts["join"]."
					$join
				where $where mto_client=$this->client_identifier and mto_publish=0 ".$summary_parts["where"]."
				order by trans_date_publish desc
			";
		} else {
			$sql = "select distinct menu_data.menu_identifier,menu_data.menu_url, page_trans_data.*,".$summary_parts["return_field"]." from page_trans_data 
					inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier and trans_client = menu_access_to_page.client_identifier and trans_published_version =1 and trans_doc_status =4
					inner join menu_data on menu_data.menu_identifier= menu_access_to_page.menu_identifier and menu_data.menu_client = menu_access_to_page.client_identifier
					".$summary_parts["join"]."
					$join
				where $where trans_client=$this->client_identifier ".$summary_parts["where"]."
				order by trans_date_publish desc";
		}
		$c=0;
		$result 	= $this->parent->db_pointer->database_query($sql);
		$d			= "";
		$url		= "";
		$have 		= Array();
		$blistitem="";
		$x="";
		$x	.=	"<ul class='rss'>";
		$d = $this->libertasGetDate("Y/m/d H:i:s");
		$dataArray= Array();
		$keepArray= Array();
		while($r	= $this->parent->db_pointer->database_fetch_array($result)){
			$dataArray[count($dataArray)] = $r["trans_identifier"];
		}
		$max = count($dataArray);
		$ptr	= $this->call_command("DB_SEEK",Array($result,0));
		for ($i=0;$i<$number_of_items;$i++){
			$randomIndex = rand(0,$max);
			if(!in_array($randomIndex,$keepArray)){
				$keepArray[count($keepArray)] = $randomIndex;
			}
		}
		$chooseIndex = 0;
		while($r	= $this->parent->db_pointer->database_fetch_array($result)){
			if(in_array($chooseIndex, $keepArray)){
				$trans_title	= chop($this->check_parameters($r,"trans_title",""));
				$trans_page 	= $this->check_parameters($r,"trans_page","");
				$menu_url 		= $this->check_parameters($r,"menu_url");
				$menu_id 		= $this->check_parameters($r,"menu_identifier",-1);
					if ($this->check_parameters($xfields,"Story_Description","__NOT_FOUND__")!="__NOT_FOUND__"){
						$x .=	"<li class='withsummary'>";
						$title = strip_tags(html_entity_decode(html_entity_decode($r["trans_title"])));
					} else {
						$x .=	"<li class='storyitem'>";
						$title = str_replace("'","",strip_tags(html_entity_decode(html_entity_decode($r["trans_summary1"]))));
					}
					$x .=   	"<a href='http://".$this->parent->domain.$this->parent->base.$url."' title='$title'><span>".html_entity_decode(html_entity_decode($r["trans_title"]))."</span></a>";
					if ($this->check_parameters($xfields,"Story_Publish Date","__NOT_FOUND__")!="__NOT_FOUND__"){
						$x .=	"<div class='contentpos'><span>".Date("d/m/Y",strtotime($r["trans_date_publish"]))."</span></div>";
					}
					if ($this->check_parameters($xfields,"Story_Description","__NOT_FOUND__")!="__NOT_FOUND__"){
						$x .=	"<div class='contentpos'><span>".html_entity_decode($r["trans_summary1"])."</span></div>";
					}
					$x .=	"</li>\n";
			}
			$chooseIndex++;
		}
		$x .=	"</ul>";
		if ($this->check_parameters($xfields,"Channel_Last Build Date","__NOT_FOUND__")!="__NOT_FOUND__"){
			$xchannel	= "<li class='contentpos'><span>Last Build :: $d</span></li>";
		}
	    $this->parent->db_pointer->database_free_result($result);
		print "<style>
		body{font-size:0.9em;}
		.TableHeader{font-weigth:bold;border:1px solid #999999;background:#cccccc;padding:3px}
		.storyitem{margin:4px; xborder-bottom:2px solid #cccccc}
		.contentpos{margin:4px;}
		</style><table width=250px><tr><td>".$xchannel."</div><a name='#jump_to_fake'></a>$xlist".$x."</td></tr></table>";
		print "<script>
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
		$this->exitprogram();
	}

}
?>
