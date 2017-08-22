<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.forum_admin.php
* @date 09 Oct 2002
*/
/**
* Original Module split into two (admin and presentation)
* Note:  The forum is completely open to anonymous posting at this moment in time though it can be set that messages do not appear until approved.
* 		 To secure a forum to users only place in a restricted location.
*/
class forum_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__ADMIN__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name				= "forum_admin";
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_label				= "MANAGEMENT_FORUM";
	var $module_name_label			= "Forum Manager Admin Module";
	var $module_admin				= "1";
	var $can_log_search				= true;
	var $module_channels			= array("FORUM_DISPLAY");
	var $module_debug				= false;
	var $module_creation			= "13/09/2002";
	var $module_modify	 			= '$Date: 2005/03/21 15:00:28 $';
	var $module_version 			= '$Revision: 1.6 $';
	var $module_command				= "FORUMADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "FORUM_"; 		// key for Memo_info etc ...
	var $has_module_contact			= 0;
	var $has_module_group			= 0;
	var $display_options			= null;
	var $module_category_access		= 0;
	
	/*
	var $workflow_status			= Array(
		Array(0, "Registered Users can publish directly to the Forum"),
		Array(1, "All threads to be approved except for Forum Approver")
	); */
	
	var $workflow_status			= Array(
		Array(0, "Posts needs approval for this forum"),
		Array(1, "Auto approve posts for this forum")		
	);

	var $module_display_options 	= array(
		array("FORUM_DISPLAY","DISPLAY_FORUM_CHANNEL")
	);
	
	var $module_admin_options 		= array(
		array("FORUMADMIN_LIST","LOCALE_MANAGE_FORUM_LIST"),
		array("FORUMADMIN_MANAGE_CATEGORIES","LOCALE_MANAGE_FORUM_CATEGORIES"),
		array("FORUMADMIN_MANAGE_SWEAR_LIST","LOCALE_MANAGE_SWEAR_LIST","","Preferences/Ignore lists")
	);
	var $metadata_fields 	= Array();
	var $special_webobjects	= Array();
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
//		print "<li>$user_command</li>";
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
			if ($user_command==$this->module_command."GET_AUTHOR"){
				return $this->get_module_author();
			}
			if ($user_command==$this->module_command."GET_CREATION"){
				return $this->get_module_creation();
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/*
				administration options
			*/
			if ($this->parent->module_type == "admin"){
				/*************************************************************************************************************************
                * general admin functions
                *************************************************************************************************************************/
				if ($user_command == $this->module_command."MY_WORKSPACE"){
					return $this->my_workspace();
				}
				if ($user_command==$this->module_command."MANAGE_SWEAR_LIST"){
					return $this->manage_keyword_swear_list();
				}
				if ($user_command==$this->module_command."SAVE_SWEAR_LIST"){
					$this->save_keyword_swear_list($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
				}
				if ($user_command==$this->module_command."ACCESS_OPTIONS"){
					return $this->module_admin_options(0);
				}
				if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
					return $this->module_admin_access_options(0);
				}
				if ($user_command==$this->module_command."RETRIEVE_SEARCH_KEYWORDS1"){
					return $this->retrieve_search_keywords($parameter_list);
				}
				if ($user_command==$this->module_command."RETURN_CHANNELS"){
					return $this->return_admin_channels();
				}
				/*************************************************************************************************************************
                * Forum Category functions
                *************************************************************************************************************************/
				if($this->module_category_access==1){
					if ($user_command==$this->module_command."MANAGE_CATEGORIES"){
						return $this->forum_category_list($parameter_list);
					}
					if ($user_command==$this->module_command."CATEGORY_ADD" || $user_command==$this->module_command."CATEGORY_EDIT"){
						return $this->forum_category_modify($parameter_list);
					}
					if ($user_command==$this->module_command."CATEGORY_REMOVE"){
						//return $this->forum_category_remove($parameter_list);
						/**** Remove Category and redirect to category list(Added By Muhammad Imran Mirza ) ****/
						$this->forum_category_remove($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."MANAGE_CATEGORIES"));
					}
					if ($user_command==$this->module_command."CATEGORY_SAVE"){
						$this->forum_category_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."MANAGE_CATEGORIES"));
					}
				}
				/*************************************************************************************************************************
                * Forum setup
                *************************************************************************************************************************/
				if ($user_command==$this->module_command."LIST"){
					return $this->display_list($parameter_list);
				}
				if ($user_command==$this->module_command."SAVE"){
					$this->forum_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
				}
				if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
					return $this->forum_form($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE"){
					$this->forum_remove($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
				}
				if ($user_command==$this->module_command."REMOVE_CONFIRM"){
					$this->forum_remove_confirm($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
				}
				/*************************************************************************************************************************
                * Thread functions
                *************************************************************************************************************************/
				if ($user_command==$this->module_command."MANAGE_THREADS"){
					return $this->forum_manage_threads($parameter_list);
				}
				if (($user_command==$this->module_command."THREAD_EDIT")){
					return $this->forum_thread_form($parameter_list);
				}
				if (($user_command==$this->module_command."THREAD_REMOVE")){
					return $this->forum_thread_remove($parameter_list);
				}
				if (($user_command==$this->module_command."THREAD_REMOVE_CONFIRM")){
					$this->forum_thread_remove_confirm($parameter_list);
					$forum		= $this->check_parameters($parameter_list,"forum",-1);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."MANAGE_THREADS&amp;identifier=$forum"));
				}
				if (($user_command==$this->module_command."THREAD_EDIT_SAVE")){
					$this->forum_thread_form_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."MANAGE_THREADS&amp;identifier=" . $this->check_parameters($parameter_list,"forum")));
				}
				if (($user_command==$this->module_command."THREAD_APPROVE")){
					return $this->forum_thread_approve($parameter_list);
				}
				if (($user_command==$this->module_command."THREAD_APPROVE_CONFIRM")){
					$this->forum_thread_approve_confirm($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."MANAGE_THREADS&amp;identifier=" . $this->check_parameters($parameter_list,"forum")));
				}
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	
	/**
	* Initialise function
	*
	* This function will initialise some variables for this modules functions to use.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise",__LINE__,""));
		}
		$this->load_locale("forum");
		$this->page_size	= $this->check_prefs(Array("sp_page_size"));
		$this->check_prefs(Array("sp_forum_list_display_format", "LOCALE_SP_FORUM_LIST_DISPLAY_FORMAT"	,"default" => 'Full'	, "options" => 'Full:List'				, "module" => "FORUM_",	"ECMS"));
		$this->metadata_fields		= $this->call_command("METADATAADMIN_GET_FIELDLIST", Array());
		$this->editor_configurations = Array(
			"FORUM_DESCRIPTION" => Array(
				"status"=>"unlocked", 
				"locked_to" => "", 
				"identifier"=>0
			)
		);
		
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* define the filtering information that is available
		*/
		$this->display_options		= array(
			array (0,"Order by Date Created (oldest first)"	,"forum_creation_date Asc"),
			array (1,"Order by Date Created (newest first)"	,"forum_creation_date desc"),
			array (2,"Order by Title A -> Z"				,"forum_title asc"),
			array (3,"Order by Title Z -> A"				,"forum_title desc")
		);
		$this->module_admin_user_access	= array(
			array($this->module_command."ALL","COMPLETE_ACCESS"),
			array($this->module_command."CREATOR","LOCALE_CREATOR"),
			array($this->module_command."APPROVER","LOCALE_APPROVER")
		);
		/*************************************************************************************************************************
        * defien defalt settings ie no access
        *************************************************************************************************************************/
		$this->module_admin_access		= 0;
		$this->module_approver_access	= 0;
		$this->module_creator_access	= 0;
		$this->module_category_access	= 0;
		$this->module_admin_access		= 0;
		/*************************************************************************************************************************
        * correct way to check role access
        *************************************************************************************************************************/
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
					("FORUM_ALL" == $access[$index]) || 
					("ALL"==$access[$index]) || 
					("FORUM_CREATOR"==$access[$index])
				){
					$this->module_creator_access=1;
					$this->module_admin_access=1;
					$this->module_category_access=1;
				}
				if (
					("FORUM_ALL" == $access[$index]) || 
					("ALL"==$access[$index]) || 
					("FORUM_APPROVER"==$access[$index])
				){
					$this->module_approver_access=1;
					$this->module_admin_access=1;
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
		* Table structure for table 'group_data'
		*/
		$fields = array(
			array("forum_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("forum_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("forum_date_created"		,"datetime"					,"" 		,"default NULL"),
			array("forum_created_by"		,"unsigned integer"			,"NOT NULL"	,"default ''"),
			array("forum_status"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("forum_workflow"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("forum_location"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("forum_category"			,"integer"					,"NOT NULL"	,"default '-1'")
		);
		$primary ="forum_identifier";
		$tables[count($tables)] = array("forum", $fields, $primary);
		/**
		* Table structure for table 'group_access'
		*/
		$fields = array(
			array("forum_thread_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("forum_thread_forum"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("forum_thread_parent"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("forum_thread_starter"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("forum_thread_author"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("forum_thread_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("forum_thread_status"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("forum_thread_date"		,"datetime"					,"NOT NULL"	,"default ''"),
			array("forum_thread_blocked"	,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="forum_thread_identifier";
		$tables[count($tables)] = array("forum_thread", $fields, $primary);
		/**
		* Table structure for table 'pages'
		*/
		$fields = array(
			array("search_keyword"				,"varchar(50)"		,"NOT NULL"	,"default ''"),
			array("search_counter"				,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("search_client"				,"unsigned integer"	,"NOT NULL"	,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("forum_search_keys", $fields, $primary);
		/**
		* Table structure for table 'forum_category'
		*/
		$fields = array(
			array("fc_identifier"			,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("fc_client"				,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("fc_label"				,"varchar(255)"		,"NOT NULL"	,"default '0'")
		);
		$primary ="fc_identifier";
		$tables[count($tables)] = array("forum_category", $fields, $primary);
		/**
		* Table structure for table 'forum_thread_counter'
		*/
		$fields = array(
			array("ftc_identifier"			,"unsigned integer"	,"NOT NULL"	,"default ''"), // thread owner (starter) allows date and author of first post to be retrieved
			array("ftc_client"				,"unsigned integer"	,"NOT NULL"	,"default '0'"), // client id
			array("ftc_views"				,"unsigned integer"	,"NOT NULL"	,"default '0'"), // times viewed
			array("ftc_posts"				,"unsigned integer"	,"NOT NULL"	,"default '0'"), // total posts to this thread
			array("ftc_lastthread"			,"unsigned integer"	,"NOT NULL"	,"default '0'"), // last thread in list allows date and author of last post to be retrieved
			array("ftc_forum"				,"unsigned integer"	,"NOT NULL"	,"default '0'"), // forum belongs to 
			array("ftc_sticky"				,"unsigned integer"	,"NOT NULL"	,"default '0'") // Sticky thread 
		);
		
		$primary ="";
		$tables[count($tables)] = array("forum_thread_counter", $fields, $primary);
		/**
		* Table structure for table 'forum_thread_counter'
		*/
		$fields = array(
			array("fgr_group"			,"integer"	,"NOT NULL"	,"default ''"), // group to define settings for
			array("fgr_client"			,"unsigned integer"	,"NOT NULL"	,"default '0'"), // client id
			array("fgr_can_view"		,"unsigned integer"	,"NOT NULL"	,"default '0'"), // times viewed
			array("fgr_can_post"		,"unsigned integer"	,"NOT NULL"	,"default '0'"), // total posts to this thread
			array("fgr_auto_publish"	,"unsigned integer"	,"NOT NULL"	,"default '0'"), // last thread in list allows date and author of last post to be retrieved
			array("fgr_forum"			,"unsigned integer"	,"NOT NULL"	,"default '0'") // forum belongs to 
		);
		
		$primary ="";
		$tables[count($tables)] = array("forum_group_restrictions", $fields, $primary);
		return $tables;
	}
	/*************************************************************************************************************************
    * admin functions 
    *************************************************************************************************************************/
	/**
	* This function returns the list of forum that exists for this client.
	*/
	function display_list($page=1){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_list",__LINE__,"[$page]"));
		}
		$orderby=0;
		
		$sql = "Select forum.*, metadata_details.*, count(forum_thread.forum_thread_identifier) as total_threads, menu_data.menu_label, fc_label
				from forum 
					left outer join metadata_details on md_link_id=forum_identifier and md_client=$this->client_identifier and md_module='FORUM_'
					left outer join menu_data on forum_location=menu_identifier and menu_client=$this->client_identifier
					left outer join forum_thread on forum_identifier = forum_thread_forum and forum_thread_client=$this->client_identifier
					left outer join forum_category on forum.forum_category = fc_identifier and fc_client = $this->client_identifier
				where 
					forum_client = $this->client_identifier 
				group by 
					forum_identifier 
				order by 
					forum_identifier desc";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result = $this->parent->db_pointer->database_query($sql);
		$variables["PAGE_BUTTONS"] = Array(
			Array("ADD",$this->module_command."ADD",ADD_NEW)
		);
		
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			$number_of_records=0;
			$goto=0;
			$finish=0;
			$page=0;
			$num_pages=0;
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$this->page_size	= $this->check_prefs(Array("sp_page_size"));
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = intval($page);
			$goto = (($page-1) * $this->page_size);
			
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
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			$variables["HEADER"]			= "Forum Manager List";
			
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page+=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= "";//$this->user_filter($grouplevel,$orderby);
			
			$variables["ENTRY_BUTTONS"] =Array(
			Array("EDIT",$this->module_command."EDIT",EDIT_EXISTING),
			Array("REMOVE",$this->module_command."REMOVE_CONFIRM",REMOVE_EXISTING),
			Array("MANAGE",$this->module_command."MANAGE_THREADS",MANAGE_THREADS)
			);
			
			$variables["RESULT_ENTRIES"] =Array();
			
			while (($r = $this->parent->db_pointer->database_fetch_array($result))&&($counter<10)){
				$counter++;
				if (!empty($r["total"])){
					$total = $r["total"];
				}else{
					$total = 0;
				}
				$variables["RESULT_ENTRIES"][count($variables["RESULT_ENTRIES"])]=Array(
					"identifier"	=> $r["forum_identifier"],
					"attributes"	=> Array(
						Array(ENTRY_TITLE, $r["md_title"], "TITLE", "NO"),
						Array(LOCALE_FORUM_ENTRY_CATEGORY, $this->check_parameters($r,"fc_label","Not Specified")),
						Array(ENTRY_DATE_CREATION, $r["forum_date_created"]),
						Array("Total", $r["total_threads"]),
						Array(ENTRY_MENU_LOCATION, $this->check_parameters($r,"menu_label",NO_CHANNEL_DEFINED))
					)
				);
			}
		}
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["START"]				= $goto;
		$variables["as"]				= "table";
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$out = $this->generate_list($variables);
		return $out;
		
	}
	
	/*************************************************************************************************************************
    * Edit a forum description
    *************************************************************************************************************************/
	function forum_form($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"FORUM_form",__LINE__,"[]"));
		}
		$forum_identifier	= $this->check_parameters($parameters,"identifier",-1);
		$identifier 		= $forum_identifier;
		$forum_location		= -1;
		$forum_label		= "";
		$forum_status		= "";
		$forum_location		= "";
		$forum_description	= "";
		$forum_category		= -1;
		$forum_workflow		= 0;
		$label = "Add";
		if ($forum_identifier!=-1){
			$label = "Edit";
			$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", 
				Array(
					"table_as"			=> "forum_data",
					"field_as"			=> "forum_description",
					"identifier_field"	=> "forum.forum_identifier",
					"module_command"	=> $this->webContainer,
					"client_field"		=> "forum_client",
					"mi_field"			=> "forum_description"
				)
			);
		
			$sql = "Select ".$body_parts["return_field"].", forum.*, metadata_details.* from forum 
				inner join metadata_details on md_link_id=forum_identifier and md_client=$this->client_identifier and md_module='FORUM_'
				".$body_parts["join"]."
			where 
				forum_client = $this->client_identifier and 
				forum_identifier = $forum_identifier
				".$body_parts["where"]."";
//			$sql = "select * from forum 
//			where forum_client = $this->client_identifier and forum_identifier=".$parameters["identifier"];
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			if($result = $this->call_command("DB_QUERY",array($sql))) {
				while ($r=$this->parent->db_pointer->database_fetch_array($result)){
					$forum_label		= $r["md_title"];
					$forum_status		= $r["forum_status"];
					$forum_location		= $r["forum_location"];
					$forum_category		= $r["forum_category"];
					$forum_description	= $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>html_entity_decode($r["forum_description"])));
					$forum_workflow		= $r["forum_workflow"];
				}
				$this->parent->db_pointer->database_free_result($result);
			}
//			$sql = "select * from menu_to_object where mto_client=$this->client_identifier and mto_module = '".$this->webContainer."' and mto_object=$identifier";
//			$result  = $this->parent->db_pointer->database_query($sql);
 //           while($r = $this->parent->db_pointer->database_fetch_array($result)){
//            	$menu_id = $r["mto_menu"];
 //           }
 //           $this->parent->db_pointer->database_free_result($result);
		}
		/*************************************************************************************************************************
        * get the foprum categories
        *************************************************************************************************************************/
		$sql = "select * from forum_category where fc_client= $this->client_identifier";
		$cat_values= Array();
		$cat_labels= Array();
		$cat_values[0]="-1";
		$cat_labels[0]="No Category";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$cat_values[count($cat_values)] = $r["fc_identifier"];
        	$cat_labels[count($cat_labels)] = $r["fc_label"];
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$data = $this->call_command("LAYOUT_LIST_MENU_OPTIONS", Array($forum_location));
		$sql = "Select * from display_data inner join menu_data on menu_identifier=display_menu where display_client=$this->client_identifier and display_command='FORUM_DISPLAY'";
		
		$locations ="<option value=\"0\">No forum channels defined</option>";
		$result = $this->parent->db_pointer->database_query($sql);
		if($this->call_command("DB_NUM_ROWS",array($result))>0){
			$locations="";
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$locations .="<option value=\"".$r["menu_identifier"]."\"";
				if ($r["menu_identifier"]==$forum_location){
					$locations .=" selected=\"true\"";
				}
				$locations .="><![CDATA[".$r["menu_label"]."]]></option>";
			}
		}
		$previous_uri="";
		if($forum_label!=""){
			$previous_uri  = "-".$this->make_uri($forum_label);
		}
		$this->load_editors();
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		
		$out .= "<page_options>";
		$out .= "	<button command=\"".$this->module_command."LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"/>";
		$out .= "	<header>Forum Manager $label</header>";
		$out .= "</page_options>";
		$out .= "<form label=\"".FORUM_TITLE_LABEL."\" method=\"post\" name=\"forum_form\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."SAVE\"/>";
		$out .= "<input type=\"hidden\" name=\"forum_identifier\" value=\"$forum_identifier\"/>";
		$out .= "<input type=\"hidden\" name=\"previous_menu\" value=\"$forum_location\"/>";
		$out .= "<input type=\"hidden\" name=\"previous_uri\" value=\"$previous_uri\"/>";
		$out .= "<page_sections>";
		$display_tab= $this->check_parameters($parameters,"display_tab");
		$out .= "	<section label=\"Setup\">";
		$out .= "<input type=\"text\" label=\"".FORUM_TITLE."\" size=\"255\" name=\"forum_label\" required=\"YES\"><![CDATA[$forum_label]]></input>";
		$out .= "<select label=\"".FORUM_GOES_HERE."\" name=\"forum_location\"><option value=''>Select menu Location</option>$data</select>";
		$out .= "<select label=\"".FORUM_STATUS."\" name=\"forum_status\">".$this->gen_options(Array(0,2,1), Array(LOCALE_FORUM_STATUS_NOT_AVAILABLE,LOCALE_FORUM_STATUS_AVAILABLE_CLOSED,LOCALE_FORUM_STATUS_AVAILABLE_OPEN), $forum_status)."</select>";
		$out .= "<select label=\"".LOCALE_FORUM_WORKFLOW."\" name=\"forum_workflow\">";
		$out .= $this->gen_options2d($this->workflow_status, $forum_workflow);
/*		$m = count($this->workflow_status);
		for($i=0;$i<$m;$i++){
			$out .= "<option value='".$this->workflow_status[$i][0]."'";
			if($this->workflow_status[$i][0] == $forum_workflow){
				$out .= " selected='true' ";
			}
			$out .= ">".$this->workflow_status[$i][1]."</option>";
		}*/
		$out .= "</select>";
		$out .= "<select label=\"".LOCALE_FORUM_CHOOSE_CATEGORY."\" name=\"forum_category\">".$this->gen_options($cat_values, $cat_labels,$forum_category)."</select>";
			
		$this_editor = $this->check_parameters($this->editor_configurations,"FORUM_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .= "	</section>";
		$out .= "	<section label=\"".ENTRY_DESCRIPTION."\"";
			if ($display_tab=="description"){
				$out .= " selected='true'";
			}
			$out .= ">";
		$out .= "<textarea label=\"".FORUM_DESCRIPTION."\" size=\"55\" height=\"18\" name=\"forum_description\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$forum_description]]></textarea>";
		$out .= "</section>";
		/*************************************************************************************************************************
        * Restrictions
        *************************************************************************************************************************/
		$sql = "select * from forum_group_restrictions where fgr_client = $this->client_identifier and fgr_forum = $forum_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$groups = Array();
		$c =0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$c++;
			/*
        	$groups[$r["fgr_group"]] = Array(
				"read"		=> $r["fgr_can_view"],
				"reply"		=> $r["fgr_can_post"],
				"approve"	=> $r["fgr_auto_publish"]
			);
			*/
        	$groups[$r["fgr_group"]] = Array(				
				"reply"		=> $r["fgr_can_post"]				
			);

        }
        $this->parent->db_pointer->database_free_result($result);
		$out .= "<section label=\"Restrictions\">";
		$out .="<restrictions>
					<group id='-1' type ='0'>
						<label><![CDATA[Anonymous]]></label>";
		if($c==0){
			/*ZIA
			$out .= "	<definition name='read' value='1'><![CDATA[Read]]></definition>
						<definition name='reply' value='1'><![CDATA[Reply]]></definition>
						<definition name='approve' value='1'><![CDATA[Auto Approve]]></definition>";
			ZIA*/
			$out .= "	<definition name='reply' value='1'><![CDATA[Reply]]></definition>
						";

		} else {
			/*
			$out .= "	<definition name='read' value='".$this->check_parameters($this->check_parameters($groups,-1,Array()), "read",0)."'><![CDATA[Read]]></definition>
						<definition name='reply' value='".$this->check_parameters($this->check_parameters($groups,-1,Array()), "reply",0)."'><![CDATA[Reply]]></definition>
						<definition name='approve' value='".$this->check_parameters($this->check_parameters($groups,-1,Array()), "approve",0)."'><![CDATA[Auto Approve]]></definition>";
			*/
			$out .= "	<definition name='reply' value='".$this->check_parameters($this->check_parameters($groups,-1,Array()), "reply",0)."'><![CDATA[Reply]]></definition>
						";

		}
		$out .= "	</group>";
		$sql = "select * from group_data where group_client = $this->client_identifier order by group_type desc, group_label";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	/*ZIA
			$out .="<group id='".$r["group_identifier"]."' type ='".$r["group_type"]."'>
						<label><![CDATA[".$r["group_label"]."]]></label>
						<definition name='read' value='".$this->check_parameters($this->check_parameters($groups,$r["group_identifier"],Array()), "read",0)."'><![CDATA[Read]]></definition>
						<definition name='reply' value='".$this->check_parameters($this->check_parameters($groups,$r["group_identifier"],Array()), "reply",0)."'><![CDATA[Reply]]></definition>
						<definition name='approve' value='".$this->check_parameters($this->check_parameters($groups,$r["group_identifier"],Array()), "approve",0)."'><![CDATA[Auto Approve]]></definition>
					</group>";
			ZIA*/		
			$out .="<group id='".$r["group_identifier"]."' type ='".$r["group_type"]."'>
						<label><![CDATA[".$r["group_label"]."]]></label>
						<definition name='reply' value='".$this->check_parameters($this->check_parameters($groups,$r["group_identifier"],Array()), "reply",0)."'><![CDATA[Reply]]></definition>
					</group>";

        }
        $this->parent->db_pointer->database_free_result($result);
		$out .= "";
		$out .= "</restrictions>";
		$out .= "</section>";
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
//		$out .= $this->location_tab($all_locations, $set_inheritance,$menu_locations, $display_tab);
		$out .= "</page_sections>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".ENTRY_SAVE."\"/>";
		$out .= "</form>";
		$out .="</module>";
		return $out;
	}

	
	/**
	* save the content
	*/
	function forum_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_save",__LINE__,"".print_r($parameters,true).""));
		}
		$ok=0;
		$forum_identifier	= $this->check_parameters($parameters,"forum_identifier",-1);
		$forum_label		= htmlentities(strip_tags($this->validate($this->tidy($parameters["forum_label"]))));
		$forum_status		= $this->check_parameters($parameters,"forum_status",0);
		$forum_workflow		= $this->check_parameters($parameters,"forum_workflow",0);
		$forum_location		= $this->check_parameters($parameters,"forum_location",-1);
		$previous_menu		= $this->check_parameters($parameters,"previous_menu",-1);
		$previous_uri		= $this->check_parameters($parameters,"previous_uri");
		$read				= $this->check_parameters($parameters,"read",Array());
		$reply				= $this->check_parameters($parameters,"reply",Array());
		$approve			= $this->check_parameters($parameters,"approve",Array());
		
		if($forum_location==""){
			$forum_location=-1;
		}
		$forum_category		= $this->check_parameters($parameters,"forum_category",-1);
		$forum_description	= htmlentities($this->validate($this->tidy($parameters["forum_description"])));
		$user_identifier 	= $this->call_command("SESSION_GET",Array("SESSION_USER_IDENTIFIER"));
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		if ($forum_identifier==-1){
			/**
			* Add a new forum to the system
			*/
			$forum_identifier = $this->getUid();
			$fields = "forum_identifier, forum_workflow, forum_date_created, forum_created_by, forum_client, forum_location, forum_status, forum_category";
			$values = "$forum_identifier, '$forum_workflow', '$now', $user_identifier, $this->client_identifier, $forum_location, $forum_status, $forum_category";
			$sql = "insert into forum ($fields) values ($values)";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("MEMOINFO_INSERT",array("mi_type"=>$this->webContainer,"mi_memo"=>$forum_description,	"mi_link_id" => $forum_identifier, "mi_field" => "forum_description"));
			/*************************************************************************************************************************
	        * add metadata for this record
			*************************************************************************************************************************/
			$longDescription = $forum_description;
			$this->call_command("METADATAADMIN_MODIFY", 
				Array(
					"md_title" 			=> $forum_label,
					"md_link_group_id"	=> $forum_identifier, 
					"md_date_publish"	=> $this->libertasGetDate(), 
					"module"			=> "FORUM_", 
					"identifier"		=> $forum_identifier, 
					"command"			=> "ADD", 
					"longDescription"	=> $longDescription
				)
			);
		} else {
			/**
			* update an existing forum in the system
			*/
			$fields = "forum_workflow='$forum_workflow', forum_created_by='$user_identifier', forum_location='$forum_location', forum_status=$forum_status, forum_category='$forum_category'";
			$sql = "update forum set $fields where forum_client= $this->client_identifier and forum_identifier=$forum_identifier";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->webContainer,"mi_memo"=>$forum_description,	"mi_link_id" => $forum_identifier, "mi_field" => "forum_description"));
			/*************************************************************************************************************************
	        * add metadata for this record
			*************************************************************************************************************************/
			$longDescription = $forum_description;
			$this->call_command("METADATAADMIN_MODIFY", 
				Array(
					"md_title" 			=> $forum_label,
					"md_link_group_id"	=> $forum_identifier, 
					"md_date_publish"	=> $this->libertasGetDate() , 
					"identifier"		=> $forum_identifier, 
					"module"			=> "FORUM_", 
					"command"			=> "EDIT", 
					"longDescription"	=> $longDescription
				)
			);
		}
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
		}
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql = "delete from forum_group_restrictions where fgr_client = $this->client_identifier and fgr_forum = $forum_identifier";
		$this->parent->db_pointer->database_query($sql);
		$list = Array();
		/*
		for($i=0;$i<count($read);$i++){
			if("__NOT_FOUND__" == $this->check_parameters($list,$read[$i],"__NOT_FOUND__")){
				$list[$read[$i]] = Array("read"=>1,"approve"=>0,"reply"=>0);
			} else {
				$list[$read[$i]]["read"]=1;
			}
		}
		*/
		for($i=0;$i<count($reply);$i++){
			if("__NOT_FOUND__" == $this->check_parameters($list, $reply[$i],"__NOT_FOUND__")){
				$list[$reply[$i]] = Array("read"=>0,"approve"=>0,"reply"=>1);
			} else {
				$list[$reply[$i]]["reply"]=1;
			}
		}
		/*
		for($i=0;$i<count($approve);$i++){
			if("__NOT_FOUND__" == $this->check_parameters($list, $approve[$i],"__NOT_FOUND__")){
				$list[$approve[$i]] = Array("read"=>0,"approve"=>1,"reply"=>0);
			} else {
				$list[$approve[$i]]["approve"]=1;
			}
		}
		*/
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r($list, true)."</pre></li>";
		foreach($list as $key => $value){
			/* ZIA THIS IS OLD SQL
			$sql = "insert into forum_group_restrictions 
						(fgr_group, fgr_client, fgr_can_view, fgr_can_post, fgr_auto_publish, fgr_forum)
					values
						($key, $this->client_identifier, ".$value["read"].", ".$value["reply"].", ".$value["approve"].", $forum_identifier)";
						*/
			$sql = "insert into forum_group_restrictions 
						(fgr_group, fgr_client, fgr_can_view, fgr_can_post, fgr_auto_publish, fgr_forum)
					values
						($key, $this->client_identifier, 1, ".$value["reply"].", ".$value["approve"].", $forum_identifier)";

			//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$this->parent->db_pointer->database_query($sql);
		}
//		$this->exitprogram();
		/*************************************************************************************************************************
        * remove complete list
        *************************************************************************************************************************/
		$sql = "delete from display_data where display_command='FORUM_DISPLAY' and display_client= $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		/*************************************************************************************************************************
        * retrieve a list of all the locations for all forums for this client and add new display_command
        *************************************************************************************************************************/
		$sql = "select distinct forum_location from forum where forum_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$sql = "insert into display_data (display_command, display_client, display_menu) values ('FORUM_DISPLAY', '$this->client_identifier', '".$r["forum_location"]."')";
			$this->parent->db_pointer->database_query($sql);
        }
        $this->parent->db_pointer->database_free_result($result);
		
		
		$this->call_command("METADATAADMIN_CACHE", 
			Array(
				"identifier"	=> $forum_identifier,
				"module"		=> "FORUM_"
			)
		);
//		$this->exitprogram();
		/*************************************************************************************************************************
        * move a forums directory to a new menu location
        *************************************************************************************************************************/
		if($previous_menu != $forum_location){
			$forum_label_uri = "-".$this->make_uri($forum_label);
			$root 				= $this->parent->site_directories["ROOT"];
			$module_directory	= $this->parent->site_directories["MODULE_DIR"];
			/*************************************************************************************************************************
       	 * 
       	 *************************************************************************************************************************/
			$sql = "select * from menu_data where menu_identifier in ($previous_menu, $forum_location) and menu_client='$this->client_identifier' ";
			$result  = $this->parent->db_pointer->database_query($sql);
			$src_url="";
			$dest_url="";
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				if($previous_menu == $r["menu_identifier"]){
					$src_url	= $r["menu_url"];
				} else {
					$dest_url	= $r["menu_url"];
				}
			}
			$this->parent->db_pointer->database_free_result($result);
			$src	= $root."/".dirname($src_url)."/".$previous_uri;
			$dest	= $root."/".dirname($dest_url)."/".$forum_label_uri;
			@rename ($src,$dest);
		}
		// make the special files if new forum
		$this->make_special($forum_location, $forum_identifier, $forum_label, $forum_identifier);
	}
	/**
	* FORUM_remove function
	*/
	function forum_remove($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_remove",__LINE__,"".print_r($parameters,true).""));
		}
		
		$out  = "<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<form name='user_form' label=\"".LOCALE_CONFIRM_DELETE."\" method=\"post\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."REMOVE_CONFIRM\"/>";
		$out .= "<input type=\"hidden\" name=\"forum_identifier\" value=\"".$parameters["identifier"]."\"/>";
		$out .= "<text><![CDATA[".FORUM_REMOVE_CONFIRMATION_LABEL."]]></text>";
		$out .= "<input type=\"button\" iconify=\"NO\" value=\"".ENTRY_NO."\" name=\"action\"  command=\"".$this->module_command."LIST\"/>";
		$out .= "<input type=\"submit\" iconify=\"YES\" value=\"".ENTRY_YES."\" name=\"action\"/>";
		$out .= "</form>";
		$out .="</module>";
		
		return $out;
	}
	/***
	| FORUM_remove_confirm function |
	*/
	function forum_remove_confirm($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_remove_confirm",__LINE__,"".print_r($parameters,true).""));
		}
		$id = $this->check_parameters($parameters,"forum_identifier",$this->check_parameters($parameters,"identifier",-1));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"FORUM_remove_confirm",__LINE__,""));
		}
		$sql = "delete from forum where forum_identifier=".$id ." and forum_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$this->parent->db_pointer->database_query($sql);
		$sql = "delete from forum_thread where forum_thread_starter=".$id." and forum_thread_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$this->parent->db_pointer->database_query($sql);
		$this->call_command("METADATAADMIN_MODIFY", 
				Array(
					"identifier"		=> $id, 
					"module"			=> "FORUM_", 
					"command"			=> "REMOVE"
				)
			);
		return true;
	}
	
	
	
	function manage_keyword_swear_list(){
		$list="";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fname = $data_files."/swear_keyword_list_".$this->client_identifier.".txt";
		if (file_exists($fname)){
			$fp = fopen($fname, 'r');
			$list = fread($fp, filesize($fname));
			fclose($fp);
		}
		$out = "<module name=\"$this->module_name\" display=\"form\">";
		$out.= "<form name=\"user_form\" label=\"".LOCALE_SWEAR_KEYWORD_LIST."\" method=\"post\">";
		$out.= "<input type=\"hidden\" name=\"command\"><![CDATA[FORUM_SAVE_SWEAR_LIST]]></input>";
		$out.= "<textarea name=\"swear_keyword_list\" label=\"".LOCALE_HOW_TO_WRITE_SWEAR_LIST."\" height=\"25\"><![CDATA[$list]]></textarea>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" />";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	
	function save_keyword_swear_list($parameters){
		$swear_keyword_list	= $this->check_parameters($parameters,"swear_keyword_list");
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fname = $data_files."/swear_keyword_list_".$this->client_identifier.".txt";
		$fp = fopen($fname, 'w');
		fwrite($fp, $swear_keyword_list);
		fclose($fp);
		$um = umask(0);
		@chmod($data_files."/swear_keyword_list_".$this->client_identifier.".txt", LS__FILE_PERMISSION);
		umask($um);
	}
	
	function forum_manage_threads($parameters){
		$out="";
		$identifier  			= $this->check_parameters($parameters,"identifier",-1);
		$forum_thread_status	= $this->check_parameters($parameters,"forum_thread_status",-1);
		if ($identifier!=-1){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_view_thread",__LINE__, "".print_r($parameters,true).""));
			}
			if (empty($parameters["thread_parent"])){
				$parameters["thread_parent"]=0;
			}
			if ($forum_thread_status>-1){
				$forum_thread = "forum_thread_status = $forum_thread_status and ";
			}else {
				$forum_thread = "";
			}
			$sql = "Select forum_thread.*,forum.forum_location, forum.forum_identifier, forum_metadata.md_title as forum_label, 
						thread_metadata.md_title as forum_thread_title,  menu_data.menu_label 
					from forum
	 					inner join forum_thread on forum_identifier = forum_thread_forum and forum_thread_client = $this->client_identifier
	 					inner join metadata_details as thread_metadata on thread_metadata.md_link_id=forum_thread_identifier and thread_metadata.md_client=$this->client_identifier and thread_metadata.md_module='FORUMTHREAD_' 
	 					inner join metadata_details as forum_metadata on forum_metadata.md_link_id=forum_identifier and forum_metadata.md_client=$this->client_identifier and forum_metadata.md_module='FORUM_' 
						inner join menu_data on forum_location=menu_identifier and menu_client = forum_client
					where 
						$forum_thread
						forum_client = $this->client_identifier and 
						forum_status=1 and 
						forum_identifier=$identifier 
						order by forum_thread_status asc, forum_thread_identifier desc";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result = $this->parent->db_pointer->database_query($sql);
			$variables["GROUPING_IDENTIFIER"] = $identifier;
			$variables["HEADER"]="Manage Threads";
				$variables["PAGE_BUTTONS"] = Array(
//					Array("ADD",$this->module_command."THREAD_GENERATE&amp;identifier=","Add a new thread")
				);
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				$number_of_records	= 0;
				$goto				= 0;
				$finish				= 0;
				$page				= 0;
				$num_pages			= 0;
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				$this->page_size=50;
				$page =$this->check_parameters($parameters,"page",1);
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
				
				$start_page=intval($page/$this->page_size);
				$remainder = $page % $this->page_size;
				if ($remainder>0){
					$start_page++;
				}
				
				$variables["START_PAGE"]		= $start_page;
				
				if (($start_page+$this->page_size)>$num_pages)
				$end_page=$num_pages;
				else
				$end_page+=$this->page_size;
				
				$variables["END_PAGE"]			= $end_page;
				
				$variables["RESULT_ENTRIES"] =Array();
				while (($r = $this->parent->db_pointer->database_fetch_array($result))&&($counter<$this->page_size)){
					$counter++;
					if (!empty($r["total"])){
						$total = $r["total"];
					}else{
						$total = 0;
					}
					if ($r["forum_thread_status"] == 0){
						$status = "Requires Approval";
					} else {
						$status = "Approved";
					}
					$thread_link_author_name = "NO";					
					if ($r["forum_thread_author"] != 0) {
						$thread_link_author_name = "ENTRY_AUTHOR_LINK";
						$thread_author_name = str_replace(",","",$this->call_command("CONTACT_GET_NAME",array("contact_user" => $r["forum_thread_author"])));
					}	
					else
						$thread_author_name = (strlen($r["forum_thread_author_name"])>0)?$r["forum_thread_author_name"]:"Anonymous";

					$new_index = count($variables["RESULT_ENTRIES"]);
					$variables["RESULT_ENTRIES"][$new_index]=Array(
						"identifier"	=> $r["forum_thread_identifier"],
						"ENTRY_BUTTONS" => Array(),
						"attributes"	=> Array(
							Array(ENTRY_TITLE, $r["forum_thread_title"],"TITLE"),
							Array(LOCALE_STATUS, $status),
							Array(ENTRY_DATE_CREATION,$r["forum_thread_date"],"SUMMARY"),
							Array(ENTRY_AUTHOR, $thread_author_name,"Yes", $thread_link_author_name),
							Array("ENTRY_AUTHOR_LINK", "?command=CONTACT_VIEW_USER&identifier=".$r["forum_thread_author"] ,"NO","NO")
						)
					);
					
					$variables["RESULT_ENTRIES"][$new_index]["ENTRY_BUTTONS"][0] = Array("EDIT"		,$this->module_command."THREAD_EDIT&amp;forum=$identifier","Edit thread");
					$variables["RESULT_ENTRIES"][$new_index]["ENTRY_BUTTONS"][1] = Array("DELETE"	,$this->module_command."THREAD_REMOVE&amp;forum=$identifier","Delete thread");
					if ($r["forum_thread_status"]==0){
						$variables["RESULT_ENTRIES"][$new_index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$new_index]["ENTRY_BUTTONS"])] = Array("APPROVE"	,$this->module_command."THREAD_APPROVE&amp;forum=$identifier","Approve this thread");
					}

				}
			}
			$variables["PAGE_COMMAND"]		= "FORUM_VIEW_ENTRY";
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["as"]				= "table";
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			$variables["FILTER"]			= $this->filter_manage_threads($parameters);
			$out = $this->generate_list($variables);
		}
		return $out;
	}
	
	function filter_manage_threads($parameters){
		$identifier  			= $this->check_parameters($parameters,"identifier",-1);
		$search_title			= $this->check_parameters($parameters,"search_title",-1);
		$page					= $this->check_parameters($parameters,"page",0);
		$forum_thread_status			= $this->check_parameters($parameters,"forum_thread_status",-1);
		
		$out = "<module name=\"$this->module_name\" display=\"form\"><filter>";
		$out .= "<form name=\"thread_form\" method=\"post\" label=\"\">";
		$out .= "<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."MANAGE_THREADS\"/>";
		$out .= "<input type=\"hidden\" name=\"page\" value=\"$page\"/>";
		$out .= "<input type=\"text\" size=\"255\" name=\"search_title\" value=\"$search_title\" label=\"Keyword\"/>";
		$out .= "<select name=\"forum_thread_status\" label=\"Status\">";
		$out .= "	<option value='-1'><![CDATA[Show ALL]]></option>";
		$out .= "	<option value='0' ";
		if($forum_thread_status == 0){
			$out .= " selected='true'";
		}
		$out .= "><![CDATA[Requires Approval]]></option>";
		$out .= "	<option value='1'";
		if($forum_thread_status == 1){
			$out .= " selected='true'";
		}
		$out .= "><![CDATA[Approved]]></option>";
		$out .= "</select>";
		$out .= "<input type=\"submit\" iconify=\"SEARCH\" value=\"Search\"/>";
		$out .= "</form></filter>";
		$out .= "</module>";
		
		return $out;
	}
	function forum_thread_approve($parameters){
		$identifier  			= $this->check_parameters($parameters,"identifier",-1);
		$forum					= $this->check_parameters($parameters,"forum",-1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_thread_approve",__LINE__,"forum = $forum && identifier == $identifier"));
		}
		if ($forum!=-1 && $identifier!=-1){
        	$title		= "";
        	$body		= "";
			$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", 
				Array(
					"table_as"			=> "forum_thread_data",
					"field_as"			=> "forum_thread_description",
					"identifier_field"	=> "forum_thread.forum_thread_identifier",
					"module_command"	=> $this->webContainer,
					"client_field"		=> "forum_thread_client",
					"mi_field"			=> "forum_thread_description"
				)
			);
			$sql		= "select ".$body_parts["return_field"].", forum_thread.*, metadata_details.* from forum_thread 
				inner join metadata_details on md_link_id=forum_thread_identifier and md_client=" .$this->client_identifier ." and md_module='FORUMTHREAD_' 				
				".$body_parts["join"]." 
			where forum_thread_identifier = $identifier 
				".$body_parts["where"]."  and forum_thread_client= $this->client_identifier and forum_thread_forum = $forum";
//left outer join contact_data on contact_user = forum_thread_author and contact_client = forum_thread_client and forum_thread_author!=0
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result		= $this->parent->db_pointer->database_query($sql);
	        while($r	= $this->parent->db_pointer->database_fetch_array($result)){
	        	$title	= $r["md_title"];
	        	$body	= $r["forum_thread_description"];
	        	$date	= $r["forum_thread_date"];
				if ($r["forum_thread_author"] != 0) 
					$thread_author_name = str_replace(",","",$this->call_command("CONTACT_GET_NAME",array("contact_user" => $r["forum_thread_author"])));
				else
					$thread_author_name = (strlen($r["forum_thread_author_name"])>0)?$r["forum_thread_author_name"]:"Anonymous";
	        	
				//$author	= (is_null($r["contact_identifier"])?"Anonymous":$r["contact_first_name"].", ".$r["contact_last_name"]);
	        }
	        $this->parent->db_pointer->database_free_result($result);
	
			$out = "<module name=\"$this->module_name\" display=\"form\">";
			$out .= "	<page_options>
				<header>Thread Approval Screen</header>
				<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"FORUMADMIN_MANAGE_THREADS&amp;identifier=$forum\"/>
			</page_options>";
	
			$out .= "	<form name=\"thread_form\" method=\"post\" label=\"\">";
			$out .= "		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
			$out .= "		<input type=\"hidden\" name=\"forum\" value=\"$forum\"/>";
			$out .= "		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."THREAD_APPROVE_CONFIRM\"/>";
			$out .= "		<input type=\"submit\" iconify=\"APPROVE\" value=\"Approve\"/>";
			$out .= "		<text><![CDATA[The following thread requires approval.<hr/>]]></text>";
			$out .= "		<text><![CDATA[<strong>$title</strong>]]></text>";
			$out .= "		<text><![CDATA[<em>Posted : $date</em>]]></text>";
			$out .= "		<text><![CDATA[<em>Author : $thread_author_name</em>]]></text>";
			$out .= "		<text><![CDATA[$body<hr/>]]></text>";
			$out .= "	</form>";
			$out .= "</module>";
		} else {
			$out="";
		}
		return $out;
	}
	function forum_thread_approve_confirm($parameters){
		$identifier  			= $this->check_parameters($parameters,"identifier",-1);
		$forum					= $this->check_parameters($parameters,"forum",-1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_thread_approve",__LINE__,"forum = $forum && identifier == $identifier"));
		}
		if ($forum!=-1 && $identifier!=-1){
			$sql		= "update forum_thread 
				set forum_thread_status = 1 where forum_thread_identifier = $identifier and forum_thread_client= $this->client_identifier and forum_thread_forum = $forum";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_FORUM__"], "identifier" => $identifier, "url"=> ""));
		}
	}
	function forum_thread_form($parameters){
		$identifier  			= $this->check_parameters($parameters,"identifier",-1);
		$forum					= $this->check_parameters($parameters,"forum",-1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_thread_form",__LINE__, "".print_r($parameters,true).""));
		}
		if ($forum!=-1 && $identifier!=-1){
			$title = "";
			$label = "Edit a Thread";
			$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", 
				Array(
					"table_as"			=> "forum_thread_data",
					"field_as"			=> "forum_thread_description",
					"identifier_field"	=> "forum_thread.forum_thread_identifier",
					"module_command"	=> $this->webContainer,
					"client_field"		=> "forum_thread_client",
					"mi_field"			=> "forum_thread_description"
				)
			);
			$sql = "Select ".$body_parts["return_field"].", forum_thread.*, forum_thread_counter.*, metadata_details.* from forum_thread 
				inner join metadata_details on md_link_id=forum_thread_identifier and md_client=".$this->client_identifier." and md_module='FORUMTHREAD_' 
				".$body_parts["join"]." 
				inner join forum_thread_counter on ftc_identifier = forum_thread_starter and ftc_client =$this->client_identifier
			where 
				forum_thread_client = $this->client_identifier and
				forum_thread_identifier = ".$identifier." 
				".$body_parts["where"]."  and 
				forum_thread_forum = $forum";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			$result  = $this->parent->db_pointer->database_query($sql);
			$sticky = 0;
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
	        	$title					= str_replace(Array("&quot;","&#39;","&amp;#39;"),Array("[[quot]]","[[pos]]","[[pos]]"),$r["md_title"]);
	        	$body					= str_replace(Array("&quot;","&#39;","&lt;br/&gt;","&amp;#39;"),Array("[[quot]]","[[pos]]","\n","[[pos]]"),$r["forum_thread_description"]);
				$sticky 				= $r["ftc_sticky"];
				$forum_thread_starter	= $r["forum_thread_starter"];
			}
	        $this->parent->db_pointer->database_free_result($result);
			
			$out = "<module name=\"$this->module_name\" display=\"form\">";
			$out .= "<page_options><header>$label</header><input type=\"button\" command=\"FORUMADMIN_MANAGE_THREADS&amp;identifier=$forum\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\"/></page_options>";
			$out .= "<form name=\"thread_form\" method=\"post\" label=\"$label\">";
			$out .= "<input type=\"hidden\" name=\"forum\" value=\"$forum\"/>";
			$out .= "<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."THREAD_EDIT_SAVE\"/>";
			$out .= "<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
			$out .= "<input type=\"hidden\" name=\"forum_thread_starter\" value=\"$forum_thread_starter\"/>";
			$out .= "<input type=\"text\" size=\"255\" name=\"thread_title\" label=\"Subject\"><![CDATA[$title]]></input>";
			$out .= "<checkboxes name=\"thread_sticky\" label=\"Is this a sticky thread\" type='vertical'><option value='1'";
			if($sticky==1){
				$out .= " selected='true'";
			}
			$out.="><![CDATA[Yes]]></option></checkboxes>";
			$out .= "<textarea label=\"Message\" width=\"60\" height=\"40\" name=\"thread_body\" type=\"PLAIN-TEXT\"><![CDATA[$body]]></textarea>";
			$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
			$out .= "</form>";
			$out .= "</module>";
			
			return $out;
		}
	}
	function forum_thread_form_save($parameters){
		$thread_identifier		= $this->check_parameters($parameters,"identifier",-1);
		$thread_forum			= $this->check_parameters($parameters,"forum",-1);
		$thread_title 			= htmlentities($this->validate($this->call_command("UTILS_STRIP_SWEARWORDS",Array("source_string" => strip_tags($parameters["thread_title"])))));
		$thread_body			= htmlentities($this->split_me($this->validate($this->call_command("UTILS_STRIP_SWEARWORDS",Array("source_string" => strip_tags($parameters["thread_body"])))),"\n","<br/>"));
		$blank = Array();
		$blank[0] = 0;
		$thread_sticky			= $this->check_parameters($parameters,"thread_sticky",$blank);
		$thread_starter			= $this->check_parameters($parameters,"forum_thread_starter",0);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_thread_form",__LINE__, "".print_r($parameters,true).""));
		}
		if ($thread_forum!=-1 && $thread_identifier!=-1){
			$sql = "update forum_thread 
				set 
					forum_thread_title	= '$thread_title'
			where 
				forum_thread_identifier = $thread_identifier and 
				forum_thread_client		= $this->client_identifier and 
				forum_thread_forum 		= $thread_forum";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->webContainer,"mi_memo"=>$thread_body,	"mi_link_id" => $thread_identifier, "mi_field" => "forum_thread_description"));
			$sql = "update forum_thread_counter set
						ftc_sticky		= $thread_sticky[0]
					where 
						ftc_client = $this->client_identifier and 
						ftc_identifier = $thread_starter
					";
			$this->parent->db_pointer->database_query($sql);
		}
		
	}

	function my_workspace(){
		$sql = "
		SELECT forum_identifier, forum_label, count(forum_thread_forum) as total
			FROM forum
				inner join forum_thread on forum_thread_forum = forum_identifier and forum_client = forum_thread_client 
		where forum_thread_status = 0 and forum_client = $this->client_identifier
			group by forum_identifier
		";
		$sql = "SELECT md_title AS forum_label, forum_identifier, count( forum_thread.forum_thread_identifier ) AS unapproved_threads
		FROM forum
		INNER JOIN metadata_details ON md_link_id = forum_identifier
		AND md_client =". $this->client_identifier ."
		AND md_module = 'FORUM_'
		LEFT OUTER JOIN forum_thread ON forum_identifier = forum_thread_forum
		AND forum_thread_status =0
		AND forum_thread_client = forum_client
		WHERE forum_client =" .$this->client_identifier ."
		AND forum_status =1
		GROUP BY forum_identifier
		ORDER BY forum_label";
		
		$result  = $this->parent->db_pointer->database_query($sql);
		$out = "<module name='forum' label='Forum Manager' display='my_workspace'><label>Forums with entries requiring approval</label>";
		$c=0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	if ($r["unapproved_threads"] > 0){
	  			$out .= "	<text><![CDATA[ <a href='admin/index.php?command=FORUMADMIN_MANAGE_THREADS&amp;identifier=".$r["forum_identifier"]."'>".$r["forum_label"]." (".$r["unapproved_threads"].")</a>]]></text>";
        	}
        	else {
	  			$out .= "	<text><![CDATA[ ".$r["forum_label"]." (".$r["unapproved_threads"].")]]></text>";        		
        	}	
        	$c++;
        }
		if ($c==0){
	  		$out .= "	<text><![CDATA[ There are no Forum Entries to Approve]]></text>";
		}
        $this->parent->db_pointer->database_free_result($result);
  		$out .= "</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function forum_category_list($parameters){
		$out="";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_view_thread",__LINE__, "".print_r($parameters,true).""));
		}
		
		$sql = "Select distinct forum_category.*, count(forum_category) as total from forum_category 
			left outer join forum on forum_category = fc_identifier and forum_client = fc_client
		where fc_client = $this->client_identifier 
		group by forum_category
		order by fc_label asc";
	//	print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));}
		$result = $this->parent->db_pointer->database_query($sql);
		$variables["HEADER"]="Manage Forum Categories";
		$variables["PAGE_BUTTONS"] = Array(
			Array("ADD",$this->module_command."CATEGORY_ADD","Add a new Category")
		);
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			$number_of_records	= 0;
			$goto				= 0;
			$finish				= 0;
			$page				= 0;
			$num_pages			= 0;
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$this->page_size = 50;
			$page =$this->check_parameters($parameters,"page",1);
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
			
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page+=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			
			$variables["RESULT_ENTRIES"] =Array();
			while (($r = $this->parent->db_pointer->database_fetch_array($result))&&($counter<$this->page_size)){
				$counter++;
				$new_index = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$new_index]=Array(
					"identifier"	=> $r["fc_identifier"],
					"ENTRY_BUTTONS" => Array(),
					"attributes"	=> Array(
						Array(ENTRY_TITLE, $r["fc_label"],"TITLE"),
						Array(LOCALE_FORUM_CATEGORY_TOTAL, $r["total"], "SUMMARY")
					)
				);
				$variables["RESULT_ENTRIES"][$new_index]["ENTRY_BUTTONS"][0] = Array("EDIT"		,$this->module_command."CATEGORY_EDIT"	,LOCALE_EDIT);
				$variables["RESULT_ENTRIES"][$new_index]["ENTRY_BUTTONS"][1] = Array("REMOVE"	,$this->module_command."CATEGORY_REMOVE",LOCALE_REMOVE_ENTRY);
			}
		}
		$variables["PAGE_COMMAND"]		= "FORUMADMIN_MANAGE_CATEGORIES";
		$variables["as"]	= "table";
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$variables["FILTER"]			= $this->filter($parameters);
		$out = $this->generate_list($variables);
		return $out;
	}

	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function forum_category_modify($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$label 			= LOCALE_FORUM_CATEGORY_ADD;
		$category_label	= "";
		if($identifier!=-1){
			$label 			= LOCALE_FORUM_CATEGORY_EDIT;
			$sql = "select * from forum_category where fc_client = $this->client_identifier and fc_identifier = $identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$category_label	= $r["fc_label"];
            }
            $this->parent->db_pointer->database_free_result($result);
		}
			$out = "<module name=\"$this->module_name\" display=\"form\">";
			$out .= "<page_options><header>$label</header>
			<input type=\"button\" command=\"FORUMADMIN_MANAGE_CATEGORIES\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\"/></page_options>";
			$out .= "<form name=\"thread_form\" method=\"post\" label=\"$label\">";
			$out .= "<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."CATEGORY_SAVE\"/>";
			$out .= "<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
			$out.="<page_sections><section name='' label='".LOCALE_FORUM_DEFINITION."'>";
			$out .= "<input type=\"text\" size=\"255\" name=\"fc_label\" label=\"".LOCALE_FORUM_CATEGORY_LABEL."\"><![CDATA[$category_label]]></input>";
			$out.="</section></page_sections>";
			$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
			$out .= "</form>";
			$out .= "</module>";
			
			return $out;
	}
	
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function forum_category_remove($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$sql = "delete from forum_category where fc_client = $this->client_identifier and fc_identifier = $identifier";
		$this->parent->db_pointer->database_query($sql);
		$sql = "update forum set forum_category = -1 where forum_category = $identifier and forum_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function forum_category_save($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$fc_label = $this->validate($this->check_parameters($parameters, "fc_label"));
		if($identifier==-1){
			$identifier = $this->getUid();
			$sql = "insert into forum_category (fc_identifier, fc_client, fc_label) values ($identifier, $this->client_identifier, '$fc_label')";
		} else {
			$sql = "update forum_category set fc_label='$fc_label' where fc_identifier=$identifier and fc_client = $this->client_identifier";
		}
		$result  = $this->parent->db_pointer->database_query($sql);
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
//		$this->exitprogram();
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function forum_thread_remove($parameters){
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$forum		= $this->check_parameters($parameters,"forum",-1);
		if($identifier!=-1){
			$sql = "SELECT * FROM forum_thread where (forum_thread_identifier = $identifier or forum_thread_parent = $identifier) and forum_thread_client = $this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			$num_rows  = $this->parent->db_pointer->database_num_rows($result);
			$starter=0;
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$starter = $r["forum_thread_starter"];
            }
            $this->parent->db_pointer->database_free_result($result);
			$remove_single=0;
			if($num_rows==1){
				$remove_single=1;
			}
			if($num_rows>1){
				$sql = "select * from forum_thread where forum_thread_starter = $starter and forum_thread_client = $this->client_identifier order by forum_thread_parent asc, forum_thread_date desc";
				$result  = $this->parent->db_pointer->database_query($sql);
				//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				$list = Array();
                while($r = $this->parent->db_pointer->database_fetch_array($result)){
                	$list[count($list)] = Array("parent" => $r["forum_thread_parent"], "identifier" => $r["forum_thread_identifier"]);
                }
                $this->parent->db_pointer->database_free_result($result);
				//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($list, true)."</pre></li>";
				$list_of_thread = $this->get_list_of_threads($list,$identifier);
			}
		}
			$out  = "<module name=\"$this->module_name\" display=\"form\">";
			$out .= "<page_options>
						<header>Confirm Thread removal</header>
						<input type=\"button\" command=\"FORUMADMIN_MANAGE_THREADS&amp;identifier=$forum\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\"/>
					</page_options>";
			$out .= "<form name=\"thread_form\" method=\"post\" label=\"\">";
			$out .= "<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."THREAD_REMOVE_CONFIRM\"/>";
			$out .= "<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
			$out .= "<input type=\"hidden\" name=\"forum\" value=\"$forum\"/>";
			$out .= "<input type=\"hidden\" name=\"remove_single\" value=\"$remove_single\"/>";
			$out .= "<page_sections><section name='' label='Confirm'>";
			if ($remove_single==1){
				$out .= "<text><![CDATA[You are removing a single entry]]></text>";
			} else {
				$out .= "<text><![CDATA[This thread has children what do you want to do ]]></text>";
				$out .= "<input type='hidden' name='remove_ids' value='$list_of_thread'/>";
				$out .= "<select label='Manage children as follows' name='childaction'>
							<option value='0'>Just blank this thread do not touch child threads</option>
							<option value='1'>Remove this thread and any children.</option>
						 </select>";
			}
			$out .= "</section></page_sections>";
			$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
			$out .= "</form>";
			$out .= "</module>";
			
			return $out;
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function forum_thread_remove_confirm($parameters){
		$identifier		= $this->check_parameters($parameters,"identifier"		, 0);
		$remove_single	= $this->check_parameters($parameters,"remove_single"	, 0);
		$remove_ids		= $this->check_parameters($parameters,"remove_ids"		, "");
		$childaction	= $this->check_parameters($parameters,"childaction"		, 0);
		if($remove_single==1){
			$sql = "delete from forum_thread where forum_thread_identifier = $identifier and forum_thread_client = $this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
		} else {
			if($childaction==0){
				// blank this thread only
				$sql = "update forum_thread set forum_thread_blocked=1 where forum_thread_identifier = $identifier and forum_thread_client = $this->client_identifier";
				$result  = $this->parent->db_pointer->database_query($sql);
			} else {
				// delete this thread and all children
				if ($remove_ids!=""){
					$sql = "delete from forum_thread where forum_thread_identifier in ($identifier, $remove_ids) and forum_thread_client = $this->client_identifier";
					$result  = $this->parent->db_pointer->database_query($sql);
				}
			}
		}
	}
	
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function get_list_of_threads($list,$identifier){
		$list_of_entries = "";
		$found=0;
		for($i=0;$i<count($list);$i++){
			if($list[$i]["parent"]==$identifier){
				if($list_of_entries!=""){
					$list_of_entries .=", ";
				}
				$list_of_entries .= $list[$i]["identifier"];
				$loe = $this->get_list_of_threads($list, $list[$i]["identifier"]);
				if($loe!=""){
					$list_of_entries .= ", ".$loe;
				}
			}
		}
		return $list_of_entries;
	}
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
	function make_special($ml_id, $id, $forum_label, $forum_identifier){
		$forum_label_uri = "-".$this->make_uri($forum_label);
		$root 				= $this->parent->site_directories["ROOT"];
		$module_directory	= $this->parent->site_directories["MODULE_DIR"];
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql = "select * from menu_data where menu_identifier = '$ml_id' and menu_client='$this->client_identifier' ";
		$result  = $this->parent->db_pointer->database_query($sql);
		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			$ml_url	= $r["menu_url"];
		}
		$this->parent->db_pointer->database_free_result($result);
		$dir = $root."/".dirname($ml_url);
		
		$filename =$dir."/$forum_label_uri/index.php";
//		print "<li>".__FILE__."@".__LINE__."<p>$filename</p></li>";
//		$this->exitprogram();
		$dir = dirname($filename);
		if (!file_exists($filename)){
			$um =umask(0);
			@mkdir($dir,LS__DIR_PERMISSION);
			umask($um);
			$um =umask(0);
			@chmod($dir, LS__DIR_PERMISSION);
			umask($um);
		}
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$this->special_webobjects			= Array(
			"VIEW_THREADS" => Array(
				"owner_module" 	=> "",
				"label" 		=> "",
				"wo_command"	=> "FORUM_VIEW_THREADS",
				"file"			=> "index.php",
				"available"		=> 1
			),
			"READ_THREAD" => Array(
				"owner_module" 	=> "",
				"label" 		=> "",
				"wo_command"	=> "FORUM_THREAD_VIEW_ENTRY",
				"file"			=> "_read-topic.php",
				"available"		=> 1
			),
			"ADD" => Array(
				"owner_module" 	=> "",
				"label" 		=> "",
				"wo_command"	=> "FORUM_THREAD_GENERATE",
				"file"			=> "_new-topic.php",
				"available"		=> 1
			)
		);
		$max 				= count($this->special_webobjects);
		
		foreach($this->special_webobjects as $index => $value){
			$out ="<"."?php
\$script_file	= dirname(\$_SERVER[\"SCRIPT_FILENAME\"]);
\$root 			= '$root';
\$site_root		= \$_SERVER[\"DOCUMENT_ROOT\"];
\$script		= get(\$script_file, \$root, \$site_root);
\$mode			= \"EXECUTE\";
\$command	 	= \"".$value["wo_command"]."\";
\$extra = Array(\"forum_identifier\" =>\"$forum_identifier\");
require_once \"".$root."/admin/include.php\";
require_once \"\$module_directory/included_page.php\";

function get1(\$sfile, \$rt, \$sroot){
	if (strpos(\$sfile,\$rt)===false){
		return substr(\$sfile, strpos(\$sfile,\$sroot)+strlen(\$sroot)+1).\"/index.php\";
	} else {
		return substr(\$sfile, strpos(\$sfile,\$rt)+strlen(\$rt)+1).\"/index.php\";
	}
}
function get(\$sfile, \$rt, \$sroot){
	\$cat = \"$forum_label_uri\";
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


					?".">";

			$file_to_use = $dir."/".$value["file"];
			$fp = fopen($file_to_use,"w");
			fwrite($fp, $out);
			fclose($fp);
			$old_umask = umask(0);
			@chmod($file_to_use,LS__FILE_PERMISSION);
			umask($old_umask);
		}
	}
}
?>