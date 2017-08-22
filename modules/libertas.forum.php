<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.forum.php
* @date 09 Oct 2002
*/
/**
*
*/
class forum extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name				= "forum";
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_label				= "MANAGEMENT_FORUM";
	var $module_name_label			= "Forum Manager Module";
	var $module_admin				= "1";
	var $can_log_search				= true;
	var $module_channels			= array("FORUM_DISPLAY");
	var $module_debug				= false;
	var $module_creation			= "13/09/2002";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:11 $';
	var $module_version 			= '$Revision: 1.15 $';
	var $module_command				= "FORUM_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "FORUM_"; 		// key for Memo_info etc ...
	var $has_module_contact			= 0;
	var $has_module_group			= 0;
	var $display_options			= null;
	
	var $workflow_status			= Array(
		Array(0, "Registered Users can publish directly to the Forum"),
		Array(1, "All threads to be approved except for Forum Approver")
	);
	
	var $module_display_options 	= array(
		array("FORUM_DISPLAY","DISPLAY_FORUM_CHANNEL")
	);
	
	var $module_admin_options 		= array(
		array("FORUM_LIST","MANAGEMENT_FORUM"),
		array("FORUM_MANAGE_SWEAR_LIST","LOCALE_MANAGE_SWEAR_LIST","","Preferences/Ignore lists")
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
			if ($this->parent->module_type=="admin"){
				
				if ($user_command==$this->module_command."MY_WORKSPACE"){
					return $this->my_workspace();
				}
				if ($user_command==$this->module_command."MANAGE_SWEAR_LIST"){
					return $this->manage_keyword_swear_list();
				}
				if ($user_command==$this->module_command."SAVE_SWEAR_LIST"){
					$this->save_keyword_swear_list($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
				}
				if ($user_command==$this->module_command."SAVE"){
					$this->forum_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
				}
				if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
					return $this->forum_form($parameter_list);
				}
				if (($user_command==$this->module_command."THREAD_EDIT")){
					return $this->forum_thread_form($parameter_list);
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
				if ($user_command==$this->module_command."REMOVE"){
					return $this->forum_remove($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE_CONFIRM"){
					$this->forum_remove_confirm($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
				}
				if ($user_command==$this->module_command."ACCESS_OPTIONS"){
					return $this->module_admin_options(0);
				}
				if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
					return $this->module_admin_access_options(0);
				}
				if ($user_command==$this->module_command."LIST"){
					return $this->display_list($parameter_list);
				}
				if ($user_command==$this->module_command."RETRIEVE_SEARCH_KEYWORDS1"){
					return $this->retrieve_search_keywords($parameter_list);
				}
				if ($user_command==$this->module_command."RETURN_CHANNELS"){
					return $this->return_admin_channels();
				}
				if ($user_command==$this->module_command."MANAGE_THREADS"){
					return $this->forum_manage_threads($parameter_list);
				}
			}
			if ($user_command==$this->module_command."THREAD_SAVE"){
				$forum = $this->forum_thread_save($parameter_list);
				$parameter_list["forum_identifier"] = $forum;
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=FORUM_VIEW_THREADS&amp;forum_identifier=$forum&amp;page=1"));
			}
			if ($user_command==$this->module_command."VIEW_THREADS"){
				return $this->forum_view_thread($parameter_list);
			}
			if ($user_command==$this->module_command."THREAD_VIEW_ENTRY"){
				return $this->thread_display($parameter_list);
			}
			if ($user_command==$this->module_command."THREAD_GENERATE"){
				return $this->forum_thread_generation($parameter_list);
			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->forum_display($parameter_list);
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
			array (0,"Order by Date Created (oldest first)"	,"FORUM_creation_date Asc"),
			array (1,"Order by Date Created (newest first)"	,"FORUM_creation_date desc"),
			array (2,"Order by Title A -> Z"				,"FORUM_title asc"),
			array (3,"Order by Title Z -> A"				,"FORUM_title desc")
		);
		$this->module_admin_user_access	= array(
			array($this->module_command."ALL","COMPLETE_ACCESS"),
			array($this->module_command."CREATOR","LOCALE_CREATOR"),
			array($this->module_command."APPROVER","LOCALE_APPROVER")
		);
		
		$group_access = $this->check_parameters($_SESSION,"SESSION_GROUP_ACCESS");
		
		$this->module_admin_access		= 0;
		$access_list = $group_access;
		if (!is_array($access_list)){
			$access = Array();
			$access[0] = $access_list;
		}else{
			$access = $access_list;
		}
		$this->module_approver_access=0;
		$this->module_creator_access=0;
		$this->module_admin_access=0;
		
		for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
			if (
					("FORUM_ALL" == $access[$index]) || 
					("ALL"==$access[$index]) || 
					("FORUM_CREATOR"==$access[$index])
				){
				$this->module_creator_access=1;
				$this->module_admin_access=1;
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
	
	/**
	* This function returns the list of forum that exists for this client.
	*/
	function display_list($page=1){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_list",__LINE__,"[$page]"));
		}
		$orderby=0;
		
		$sql = "Select forum.*, count(forum_thread.forum_thread_identifier) as total_threads, menu_data.menu_label 
				from forum 
					left outer  join menu_data on forum_location=menu_identifier 
					left outer join forum_thread on forum_identifier = forum_thread_identifier 
				where 
					forum_client = $this->client_identifier 
				group by 
					forum_identifier 
				order by 
					forum_identifier desc";
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
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
			
			//$variables["RESULT_ENTRIES"] =Array();
			
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<10)){
				$counter++;
				if (!empty($r["total"])){
					$total = $r["total"];
				}else{
					$total = 0;
				}
				$variables["RESULT_ENTRIES"][count($variables["RESULT_ENTRIES"])]=Array(
				"identifier"	=> $r["forum_identifier"],
				"attributes"	=> Array(
				Array(ENTRY_TITLE,$r["forum_label"],"TITLE","NO"),
				Array(ENTRY_DATE_CREATION,$r["forum_date_created"]),
				Array(ENTRY_MENU_LOCATION,$this->check_parameters($r,"menu_label",NO_CHANNEL_DEFINED))
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
		array("forum_label"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
		array("forum_date_created"		,"datetime"					,"" 		,"default NULL"),
		array("forum_created_by"		,"unsigned integer"			,"NOT NULL"	,"default ''"),
		array("forum_status"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
		array("forum_workflow"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
		array("forum_location"			,"unsigned integer"			,"NOT NULL"	,"default '0'")
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
		array("forum_thread_title"		,"varchar(255)"				,"NOT NULL"	,"default ''"),
		array("forum_thread_blocked"	,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="forum_thread_identifier";
		$tables[count($tables)] = array("forum_thread", $fields, $primary);
		/**
		* Table structure for table 'pages'
		*/
		
		$fields = array(
		array("search_keyword"			,"varchar(50)"		,"NOT NULL"	,"default ''"),
		array("search_counter"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("search_client"			,"unsigned integer"	,"NOT NULL"	,"default ''")
		);
		
		$primary ="";
		$tables[count($tables)] = array("forum_search_keys", $fields, $primary);
		return $tables;
	}
	/**
	* FORUM_form function
	-----------------------
	- This function will generate the proper table structure in the choosen database
	- format.
	*/
	function forum_form($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"FORUM_form",__LINE__,"[]"));
		}
		$forum_identifier	= $this->check_parameters($parameters,"identifier");
		$identifier 		= $forum_identifier;
		$forum_location		= -1;
		$forum_label		= "";
		$forum_status		= "";
		$forum_location		= "";
		$forum_description	= "";
		$forum_workflow		= 0;
		$label = "Add";
		if (!empty($forum_identifier)){
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
		
			$sql = "Select ".$body_parts["return_field"].", forum.* from forum 
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
				$forum_identifier=$parameters["identifier"];
				while ($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
					$forum_label		= $r["forum_label"];
					$forum_status		= $r["forum_status"];
					$forum_location		= $r["forum_location"];
					$forum_description	= $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$r["forum_description"]));
					$forum_workflow		= $r["forum_workflow"];
				}
				$this->call_command("DB_FREE",array($result));
			}
//			$sql = "select * from menu_to_object where mto_client=$this->client_identifier and mto_module = '".$this->webContainer."' and mto_object=$identifier";
//			$result  = $this->call_command("DB_QUERY",Array($sql));
 //           while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
//            	$menu_id = $r["mto_menu"];
 //           }
 //           $this->call_command("DB_FREE",Array($result));
		}
		$data = $this->call_command("LAYOUT_LIST_MENU_OPTIONS", Array($forum_location));
		
		$sql = "Select * from display_data inner join menu_data on menu_identifier=display_menu where display_client=$this->client_identifier and display_command='FORUM_DISPLAY'";
		
		$locations ="<option value=\"0\">No forum channels defined</option>";
		$result = $this->call_command("DB_QUERY",array($sql));
		if($this->call_command("DB_NUM_ROWS",array($result))>0){
			$locations="";
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$locations .="<option value=\"".$r["menu_identifier"]."\"";
				if ($r["menu_identifier"]==$forum_location){
					$locations .=" selected=\"true\"";
				}
				$locations .="><![CDATA[".$r["menu_label"]."]]></option>";
			}
		}
		$this->load_editors();
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		
		$out .= "<page_options>";
		$out .= "	<button command=\"FORUM_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"/>";
		$out .= "	<header>Forum Manager $label</header>";
		$out .= "</page_options>";
		$out .= "<form label=\"".FORUM_TITLE_LABEL."\" method=\"post\" name=\"forum_form\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"FORUM_SAVE\"/>";
		$out .= "<input type=\"hidden\" name=\"forum_identifier\" value=\"$forum_identifier\"/>";
		$out .= "<page_sections>";
		$display_tab= $this->check_parameters($parameters,"display_tab");
		$out .= "	<section label=\"".ENTRY_DESCRIPTION."\"";
			if ($display_tab=="description"){
				$out .= " selected='true'";
			}
			$out .= ">";
		$out .= "<select label=\"".FORUM_GOES_HERE."\" name=\"forum_location\"><option value=''>Select menu Location</option>$data</select>";
		$out .= "<select label=\"".FORUM_STATUS."\" name=\"forum_status\">";
		$out .= "<option value=\"0\">".STATUS_NOT_LIVE."</option>";
		$out .= "<option value=\"1\"";
		if ($forum_status==1){
			$out .=" selected=\"true\"";
		}
		$out .= ">".STATUS_LIVE."</option>";
		$out .= "</select>";
		$out .= "<select label=\"".LOCALE_WORKFLOW."\" name=\"forum_workflow\">";
		$m = count($this->workflow_status);
		for($i=0;$i<$m;$i++){
			$out .= "<option value='".$this->workflow_status[$i][0]."'";
			if($this->workflow_status[$i][0] == $forum_workflow){
				$out .= " selected='true' ";
			}
			$out .= ">".$this->workflow_status[$i][1]."</option>";
		}
		$out .= "</select>";
		$out .= "<input type=\"text\" label=\"".FORUM_TITLE."\" size=\"255\" name=\"forum_label\" required=\"YES\"><![CDATA[$forum_label]]></input>";
		$this_editor = $this->check_parameters($this->editor_configurations,"FORUM_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .= "<textarea label=\"".FORUM_DESCRIPTION."\" size=\"55\" height=\"18\" name=\"forum_description\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$forum_description]]></textarea>";
		$out .= "</section>";
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
		$forum_identifier	= $parameters["forum_identifier"];
		$forum_label		= htmlentities(strip_tags($this->validate($this->tidy($parameters["forum_label"]))));
		$forum_status		= $this->check_parameters($parameters,"forum_status");
		$forum_workflow		= $this->check_parameters($parameters,"forum_workflow");
		$forum_location		= $this->check_parameters($parameters,"forum_location");
		$forum_description	= htmlentities($this->validate($this->tidy($parameters["forum_description"])));
		$user_identifier 	= $this->call_command("SESSION_GET",Array("SESSION_USER_IDENTIFIER"));
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		if (empty($parameters["forum_identifier"])){
			/**
			* Add a new forum to the system
			*/
			$fields = "forum_workflow, forum_label, forum_date_created, forum_created_by, forum_client, forum_location, forum_status";
			$values = "'$forum_workflow', '$forum_label', '$now', $user_identifier, $this->client_identifier, $forum_location, $forum_status";
			$sql = "insert into forum ($fields) values ($values)";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "select * from forum where 
			forum_workflow ='$forum_workflow' and 
			forum_label='$forum_label' and 
			forum_date_created='$now' and  
			forum_created_by='$user_identifier' and 
			forum_client='$this->client_identifier' and 
			forum_location='$forum_location' and 
			forum_status='$forum_status'
			";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$forum_identifier = $r["forum_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			$this->call_command("MEMOINFO_INSERT",array("mi_type"=>$this->webContainer,"mi_memo"=>$forum_description,	"mi_link_id" => $forum_identifier, "mi_field" => "forum_description"));
		} else {
			/**
			* update an existing forum in the system
			*/
			$fields = "forum_workflow='$forum_workflow', forum_label='$forum_label', forum_created_by='$user_identifier', forum_location='$forum_location', forum_status=$forum_status";
			$sql = "update forum set $fields where forum_client= $this->client_identifier and forum_identifier=$forum_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->webContainer,"mi_memo"=>$forum_description,	"mi_link_id" => $forum_identifier, "mi_field" => "forum_description"));
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
		}
		/*************************************************************************************************************************
        * remove complete list
        *************************************************************************************************************************/
		$sql = "delete from display_data where display_command='FORUM_DISPLAY' and display_client= $this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		/*************************************************************************************************************************
        * retrieve a list of all the locations for all forums for this client and add new display_command
        *************************************************************************************************************************/
		$sql = "select distinct forum_location from forum where forum_client = $this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$sql = "insert into display_data (display_command, display_client, display_menu) values ('FORUM_DISPLAY', '$this->client_identifier', '".$r["forum_location"]."')";
			$this->call_command("DB_QUERY",Array($sql));
        }
        $this->call_command("DB_FREE",Array($result));
		
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
		$out .= "<input type=\"hidden\" name=\"command\" value=\"FORUM_REMOVE_CONFIRM\"/>";
		$out .= "<input type=\"hidden\" name=\"forum_identifier\" value=\"".$parameters["identifier"]."\"/>";
		$out .= "<text><![CDATA[".FORUM_REMOVE_CONFIRMATION_LABEL."]]></text>";
		$out .= "<input type=\"button\" iconify=\"NO\" value=\"".ENTRY_NO."\" name=\"action\"  command=\"FORUM_LIST\"/>";
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
		$this->call_command("DB_QUERY",array($sql));
		$sql = "delete from forum_thread where forum_thread_starter=".$id." and forum_thread_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$this->call_command("DB_QUERY",array($sql));
		
		return true;
	}
	
	/***
	| FORUM_display function	    |
	+-------------------------------+
	| This function will generate the list of forums that are available in this location
	| if there is only one forum it will automatically open that forum.
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function forum_display($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_display",__LINE__,"".print_r($parameters,true).""));
		}
		if($this->check_parameters($parameters,"command","")==""){
			$out="";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"FORUM_display",__LINE__,""));
			}
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
		
			$sql = "Select ".$body_parts["return_field"].", forum_label, forum_identifier ,  count(forum_thread.forum_thread_identifier) as total_threads, menu_data.menu_label from forum 
				".$body_parts["join"]."
				inner join menu_data on forum_location=menu_identifier 
				left outer join forum_thread on forum_identifier = forum_thread_forum 
			where 
				forum_client = $this->client_identifier and 
				forum_status=1 and 
				forum_location=".$parameters["current_menu_location"]." 
				".$body_parts["where"]."
			group by forum_identifier order by forum_identifier desc";

			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			
			$result 	= $this->call_command("DB_QUERY",array($sql));
			$num_rows	= $this->call_command("DB_NUM_ROWS",array($result));
			if($num_rows>1) {
				$locations="";
				$out .="<module name=\"".$this->module_name."\" display=\"forum_list\">";
				$out .="<forum_list command=\"\">";
				$threads="";
				$c=0;
				while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$identifier		= $r["forum_identifier"];
					$total_threads	= $r["total_threads"];
					$out .= "<forum identifier=\"$identifier\"><title><![CDATA[".$r["forum_label"]."]]></title><description><![CDATA[".$r["forum_description"]."]]></description><threads total_threads=\"$total_threads\">$threads</threads></forum>";
				}
				$out .="</forum_list>";
				$out .="</module>";
			} else if ($num_rows==1){
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
				$parameters["forum_identifier"]=$r["forum_identifier"];
				$out = $this->forum_view_thread($parameters);
			}else {
			
			}
		} else {
			$out ="";
		}
		return $out;
	}
	/**
	* This function will generate the list of threads for a specific forum.
	*/
	function forum_view_thread($parameters){
		
		$out="";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_view_thread",__LINE__,"".print_r($parameters,true).""));
		}
		if (empty($parameters["thread_parent"])){
			$parameters["thread_parent"]=0;
		}
		$sql = "Select ft1.*, max(ft2.forum_thread_date) as last_post, ft2.forum_thread_author as last_author, forum.forum_location,  forum.forum_identifier,   forum.forum_label,  menu_data.menu_label, count(ft2.forum_thread_starter) as total from forum
					inner join menu_data on forum_location=menu_identifier 
					left outer join forum_thread as ft1 on forum_identifier = ft1.forum_thread_forum and ft1.forum_thread_client = forum_client
					inner join forum_thread as ft2 on ft2.forum_thread_starter = ft1.forum_thread_identifier and ft2.forum_thread_client = forum_client
				where 
					forum_client = $this->client_identifier and 
					forum_status = 1 and forum_identifier = ".$parameters["forum_identifier"]." and 
					(ft1.forum_thread_status = 1 or ft1.forum_thread_status is null)  and 
					ft1.forum_thread_starter = ft1.forum_thread_identifier and 
					menu_url = '".$this->parent->script."'
				group by ft2.forum_thread_starter
				 order by last_post desc";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		
		
		
		$variables["GROUPING_IDENTIFIER"] = $parameters["forum_identifier"];
		if ($this->call_command("SESSION_GET",array("SESSION_USER_IDENTIFIER"))>0){
			$variables["PAGE_BUTTONS"] = Array(
				Array("ADD",$this->module_command."THREAD_GENERATE","Add a new thread")
			);
		}
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
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page =$this->check_parameters($parameters,"page",1);
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
			$variables["FILTER"]			= "";//$this->user_filter($grouplevel,$orderby);
			$variables["RESULT_ENTRIES"] =Array();
			define("FORUM_ENTRY_TOTAL"									,"Total");
			define("ENTRY_DATE_UPDATED"									,"Last Reply");
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				if ($this->check_parameters($r,"forum_thread_identifier","__NOT_FOUND__")!="__NOT_FOUND__"){
					$counter++;
					if (!empty($r["total"])){
						$total = $r["total"];
					}else{
						$total = 0;
					}
					$variables["RESULT_ENTRIES"][count($variables["RESULT_ENTRIES"])]=Array(
						"identifier"	=> $r["forum_thread_identifier"],
						"ENTRY_BUTTONS" => Array(),
						"attributes"	=> Array(
							Array(ENTRY_TITLE, $r["forum_thread_title"],"TITLE","EDIT_DOCUMENT"),
							Array(ENTRY_DATE_UPDATED,$r["last_post"],"SUMMARY",""),
							Array(FORUM_ENTRY_TOTAL,$total,"",""),
							Array(ENTRY_AUTHOR,$this->call_command("CONTACT_GET_NAME",array("contact_user" => $r["forum_thread_author"])),"SUMMARY","")
						)
					);
				} else {
					$number_of_records --;
				}
				$forum_label = $r["forum_label"];
			}
		}
		$variables["PAGE_COMMAND"]		= "FORUM_VIEW_ENTRY";
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$variables["LABEL"]				= "$forum_label";
		$out = $this->generate_list($variables);
		
		return $out;
	}
	/*
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	| forum_thread_generation function  |
	+-----------------------------------+
	| This function will allow the Adding or replying to an existing message
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function forum_thread_generation($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_view_thread",__LINE__,"".print_r($parameters,true).""));
		}
		$title = "";
		$label = "Add a new Thread";
		$forum_identifier = $parameters["forum_identifier"];
		$parent = $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"unset_identifier",0));
		if (empty($parent)){
			$parent 	= 0;
		} else {
			$label="Reply";
			$sql = "select * from forum_thread where forum_thread_client=$this->client_identifier and forum_thread_identifier=$parent";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				if(substr($r["forum_thread_title"],0,3)=="re:"){
					$title = str_replace(Array("&quot;","&#39;","&amp;#39;"),Array("[[quot]]","[[pos]]","[[pos]]"),$r["forum_thread_title"]);
				} else {
            		$title = "re: ".str_replace(Array("&quot;","&#39;","&amp;#39;"),Array("[[quot]]","[[pos]]","[[pos]]"),$r["forum_thread_title"]);
				}
            }
            $this->call_command("DB_FREE",Array($result));
			
		}
		
		$out = "<module name=\"$this->module_name\" display=\"form\">";
		$out .= "<form name=\"thread_form\" method=\"post\" label=\"$label\">";
		$out .= "<input type=\"hidden\" name=\"thread_parent\" value=\"$parent\"/>";
		$out .= "<input type=\"hidden\" name=\"thread_starter\" value=\"$starter\"/>";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"FORUM_THREAD_SAVE\"/>";
		$out .= "<input type=\"hidden\" name=\"forum_identifier\" value=\"$forum_identifier\"/>";
		$out .= "<input type=\"text\" size=\"255\" name=\"thread_title\" label=\"Subject\"><![CDATA[$title]]></input>"; 
		$out .= "<textarea label=\"Message\" size=\"60\" height=\"10\" name=\"thread_body\" type=\"PLAIN-TEXT\"><![CDATA[]]></textarea>";
		$out .= "<input type=\"button\" command=\"\" iconify=\"CANCEL\" value=\"Cancel this post\"/>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"Submit this post\"/>";
		
		$out .= "</form>";
		$out .= "</module>";
		
		return $out;
	}
	/*
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	| forum_thread_save function  |
	+-----------------------------------+
	| This function will allow the saving of a new thread
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function forum_thread_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_thread_save",__LINE__, "".print_r($parameters,true).""));
		}
		$thread_forum 	= $parameters["forum_identifier"];
		$thread_parent 	= $parameters["thread_parent"];
		$thread_title 	= htmlentities($this->validate($this->call_command("UTILS_STRIP_SWEARWORDS",Array("source_string" => strip_tags($parameters["thread_title"])))));
		$thread_body	= htmlentities($this->split_me($this->validate($this->call_command("UTILS_STRIP_SWEARWORDS",Array("source_string" => strip_tags($parameters["thread_body"])))),"\n","<br/>"));
		$thread_author  = $this->call_command("SESSION_GET",array("SESSION_USER_IDENTIFIER"));
		$thread_starter = 0;
		$ok 			= 0;
		if ($thread_parent==0){
			$thread_starter	=	0;
		}else{
			$sql = "select forum_thread_identifier, forum_thread_starter from forum_thread where forum_thread_identifier=$thread_parent and forum_thread_forum=$thread_forum and forum_thread_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($result){
				$r 				= $this->call_command("DB_FETCH_ARRAY",array($result));
				if ($r["forum_thread_starter"]>0){
					$thread_starter = $r["forum_thread_starter"];
				}else{
					$thread_starter = $r["forum_thread_identifier"];
				}
			}
		}
		$workflow = -1;
		$sql ="select * from forum where forum_identifier = $thread_forum and forum_client = $this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$workflow = $r["forum_workflow"];
        }
		if ($workflow==1){
			if($this->module_approver_access==1){
				$forum_thread_status = 1;
			} else {
				$forum_thread_status = 0;
			}
		} else {
			$forum_thread_status = 1;
		}
        $this->call_command("DB_FREE",Array($result));
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		$sql = "insert into forum_thread
		(forum_thread_forum, forum_thread_status, forum_thread_starter, forum_thread_parent, forum_thread_title, forum_thread_date, forum_thread_client, forum_thread_author)
		values
		($thread_forum, $forum_thread_status, $thread_starter, $thread_parent, '$thread_title', '$now', $this->client_identifier, $thread_author)
		";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_thread_save",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$sql = "select * from forum_thread where 
					forum_thread_forum	= '$thread_forum' and 
					forum_thread_status = '$forum_thread_status' and 
					forum_thread_starter= '$thread_starter' and 
					forum_thread_parent = '$thread_parent' and 
					forum_thread_title	= '$thread_title' and 
					forum_thread_date	= '$now' and 
					forum_thread_client = '$this->client_identifier' and 
					forum_thread_author	= '$thread_author'";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$forum_thread_identifier = $r["forum_thread_identifier"];
			$forum_thread_starter 	 = $r["forum_thread_starter"];
        }
        $this->call_command("DB_FREE",Array($result));
		if ($forum_thread_starter==0){
			$sql = "update forum_thread set forum_thread_starter = $forum_thread_identifier where forum_thread_client = $this->client_identifier and forum_thread_identifier=$forum_thread_identifier";
			$this->call_command("DB_QUERY",Array($sql));
		}
		$this->call_command("MEMOINFO_INSERT",array("mi_type"=>$this->webContainer, "mi_memo"=>$thread_body,	"mi_link_id" => $forum_thread_identifier, "mi_field" => "forum_thread_description"));
		
		if ($workflow==1){
			if($this->module_approver_access==1){
				$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_FORUM__"], "identifier" => $forum_thread_identifier, "url"=> $this->parent->script));
			} else {
				$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_FORUM_APPROVER__"], "identifier" => $forum_thread_identifier, "url"=> "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=FORUM_MANAGE_THREADS&identifier=$thread_forum"));
			}
		} else {
			$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_FORUM__"], "identifier" => $forum_thread_identifier, "url"=> $this->parent->script));
		}

		return $thread_forum;
	}
	/***
	| thread_display function	    |
	+-------------------------------+
	|
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function thread_display($parameters){
		$out="";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"thread_display",__LINE__,""));
		}
			$starter=0;
		$thread =$this->check_parameters($parameters,"thread_identifier",0);
		$forum =$this->check_parameters($parameters,"forum_identifier",0);
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
		$sql = "Select ".$body_parts["return_field"].", forum_thread.*, forum.*  from forum_thread 
			".$body_parts["join"]." 
			inner join forum on forum_identifier = forum_thread_forum 
		where 
			forum_thread_client = $this->client_identifier and 
			forum_status=1 and 
			forum_thread_starter = ".$thread." 
			".$body_parts["where"]." and 
			forum_identifier = ".$forum;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$this->thread_size	= $this->check_prefs(Array("sp_thread_size", "default"=>20,"module"=>"FORUM_", "options"=>"10:20:30:50"));
//		$this->thread_size=2;
//		print "[$this->thread_size]";
		$result = $this->call_command("DB_QUERY",array($sql));
		$num_rows = $this->call_command("DB_NUM_ROWS",Array($result));
		if($num_rows > 0) {
			$locations = "";
			$out .= "<module name=\"".$this->module_name."\" display=\"display\" grouping=\"".$parameters["forum_identifier"]."\">";
			$out .= "	<thread_entry command=\"".$parameters["command"]."\">";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			/*
			if($num_rows>$this->thread_size){
				$page = $this->check_parameters($parameters,"page");
				$page_count = $num_rows / $this->thread_size;
				
				$page_count = intval($page/$this->page_size);
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
				
			}
			*/
			$counter=0;
            while(($r = $this->call_command("DB_FETCH_ARRAY",Array($result))) && ($counter<$this->thread_size)){
				$counter++;
				$identifier 	= $r["forum_thread_identifier"];
				$parent			= $r["forum_thread_parent"];
				$starter		= $r["forum_thread_starter"];
				if ($parent>0){
					$sql = "select forum_thread_title from forum_thread where forum_thread_client = $this->client_identifier and forum_thread_identifier=$parent";
					$parent_result = $this->call_command("DB_QUERY",array($sql));
					$parent_row = $this->call_command("DB_FETCH_ARRAY",array($parent_result));
					$parent_title	= $parent_row["forum_thread_title"];
					$this->call_command("DB_FREE",array($parent_result));
				}else{
					$parent_title="";
				}
				$out .="<thread identifier=\"$identifier\" starter=\"$starter\">";
				$out .="<label><![CDATA[".$r["forum_thread_title"]."]]></label>";
				if ($parent!=0){
					$out .="<parent identifier=\"$parent\"><title><![CDATA[$parent_title]]></title></parent>";
				}
				$out .="<description><![CDATA[".$r["forum_thread_description"]."]]></description><date>".$r["forum_thread_date"]."</date><author><![CDATA[".$this->call_command("CONTACT_GET_NAME",array("contact_user" => $r["forum_thread_author"]))."]]></author></thread>";
	        }
    		$out .="</thread_entry>";
            $this->call_command("DB_FREE",Array($result));
			
			$out .="</module>";
		}
		
		
		return $out;
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
			$sql = "Select forum_thread.*,forum.forum_location, forum.forum_identifier, forum.forum_label, menu_data.menu_label from forum
	 					inner join forum_thread on forum_identifier = forum_thread_forum and forum_thread_client = forum_client
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
			$result = $this->call_command("DB_QUERY",array($sql));
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
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
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
					$new_index = count($variables["RESULT_ENTRIES"]);
					$variables["RESULT_ENTRIES"][$new_index]=Array(
						"identifier"	=> $r["forum_thread_identifier"],
						"ENTRY_BUTTONS" => Array(),
						"attributes"	=> Array(
							Array(ENTRY_TITLE, $r["forum_thread_title"],"TITLE","EDIT_DOCUMENT"),
							Array(LOCALE_STATUS, $status),
							Array(ENTRY_DATE_CREATION,$r["forum_thread_date"],"SUMMARY",""),
							Array(ENTRY_AUTHOR,$this->call_command("CONTACT_GET_NAME",array("contact_user" => $r["forum_thread_author"])),"SUMMARY","")
						)
					);
					
					$variables["RESULT_ENTRIES"][$new_index]["ENTRY_BUTTONS"][0] = Array("EDIT"	,$this->module_command."THREAD_EDIT&amp;forum=$identifier","Edit thread");
					if ($r["forum_thread_status"]==0){
						$variables["RESULT_ENTRIES"][$new_index]["ENTRY_BUTTONS"][1] = Array("APPROVE"	,$this->module_command."THREAD_APPROVE&amp;forum=$identifier","Approve this thread");
					}

				}
			}
			$variables["PAGE_COMMAND"]		= "FORUM_VIEW_ENTRY";
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
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
		$out .= "<input type=\"hidden\" name=\"command\" value=\"FORUM_MANAGE_THREADS\"/>";
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
			$sql		= "select ".$body_parts["return_field"].", forum_thread.* from forum_thread 
				inner join contact_data on contact_user = forum_thread_author and contact_client = forum_thread_client
				".$body_parts["join"]." 
			where forum_thread_identifier = $identifier 
				".$body_parts["where"]."  and forum_thread_client= $this->client_identifier and forum_thread_forum = $forum";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			$result		= $this->call_command("DB_QUERY",Array($sql));
	        while($r	= $this->call_command("DB_FETCH_ARRAY",Array($result))){
	        	$title	= $r["forum_thread_title"];
	        	$body	= $r["forum_thread_body"];
	        	$date	= $r["forum_thread_date"];
				$author	= $r["contact_first_name"].", ".$r["contact_last_name"];
	        }
	        $this->call_command("DB_FREE",Array($result));
	
			$out = "<module name=\"$this->module_name\" display=\"form\">";
			$out .= "	<page_options>
				<header>Thread Approval Screen</header>
				<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"FORUM_MANAGE_THREADS&amp;identifier=$forum\"/>
			</page_options>";
	
			$out .= "	<form name=\"thread_form\" method=\"post\" label=\"\">";
			$out .= "		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
			$out .= "		<input type=\"hidden\" name=\"forum\" value=\"$forum\"/>";
			$out .= "		<input type=\"hidden\" name=\"command\" value=\"FORUM_THREAD_APPROVE_CONFIRM\"/>";
			$out .= "		<input type=\"submit\" iconify=\"APPROVE\" value=\"Approve\"/>";
			$out .= "		<text><![CDATA[The following thread requires approval.<hr/>]]></text>";
			$out .= "		<text><![CDATA[<strong>$title</strong>]]></text>";
			$out .= "		<text><![CDATA[<em>Posted : $date</em>]]></text>";
			$out .= "		<text><![CDATA[<em>Author : $author</em>]]></text>";
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
			$this->call_command("DB_QUERY",Array($sql));
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
			$sql = "Select ".$body_parts["return_field"].", forum_thread.* from forum_thread 
				".$body_parts["join"]." 
			where 
				forum_thread_client = $this->client_identifier and
				forum_thread_identifier = ".$identifier." 
				".$body_parts["where"]."  and 
				forum_thread_forum = $forum";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			$result  = $this->call_command("DB_QUERY",Array($sql));
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	        	$title = str_replace(Array("&quot;","&#39;","&amp;#39;"),Array("[[quot]]","[[pos]]","[[pos]]"),$r["forum_thread_title"]);
	        	$body  = str_replace(Array("&quot;","&#39;","&lt;br/&gt;","&amp;#39;"),Array("[[quot]]","[[pos]]","\n","[[pos]]"),$r["forum_thread_description"]);
			}
	        $this->call_command("DB_FREE",Array($result));
			
			$out = "<module name=\"$this->module_name\" display=\"form\">";
			$out .= "<page_options><header>$label</header><input type=\"button\" command=\"FORUM_MANAGE_THREADS&amp;identifier=$forum\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\"/></page_options>";
			$out .= "<form name=\"thread_form\" method=\"post\" label=\"$label\">";
			$out .= "<input type=\"hidden\" name=\"forum\" value=\"$forum\"/>";
			$out .= "<input type=\"hidden\" name=\"command\" value=\"FORUM_THREAD_EDIT_SAVE\"/>";
			$out .= "<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
			$out .= "<input type=\"text\" size=\"255\" name=\"thread_title\" label=\"Subject\"><![CDATA[$title]]></input>";
			$out .= "<textarea label=\"Message\" size=\"60\" height=\"10\" name=\"thread_body\" type=\"PLAIN-TEXT\"><![CDATA[$body]]></textarea>";
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
			$this->call_command("DB_QUERY",Array($sql));
			$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->webContainer,"mi_memo"=>$thread_body,	"mi_link_id" => $thread_identifier, "mi_field" => "forum_thread_description"));
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
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$out = "<module name='forum' label='Forum Manager' display='my_workspace'><label>Forums with entries requiring approval</label>";
		$c=0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	  		$out .= "	<text><![CDATA[ <a href='admin/index.php?command=FORUM_MANAGE_THREADS&amp;identifier=".$r["forum_identifier"]."'>".$r["forum_label"]." (".$r["total"].")</a>]]></text>";
        	$c++;
        }
		if ($c==0){
	  		$out .= "	<text><![CDATA[ There are no Forum Entries to Approve]]></text>";
		}
        $this->call_command("DB_FREE",Array($result));
  		$out .= "</module>";
		return $out;
	}
}
?>