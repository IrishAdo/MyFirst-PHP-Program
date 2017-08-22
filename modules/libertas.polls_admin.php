<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.polls_admin.php
* @date 02 April 2004
*/

/**
* Enterprise Poll administration
* 
* This class is the Enterprise version of the Poll module it is split into two parts Admin and Presentation, 
* this is the presentation  part of the module, you have the ability to create groups of polls and to display them.
*/
class polls_admin extends module{
	/**#@+
	* Class Variables
    * @var string
	*/
	var $module_load_type				= "__ADMIN__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name					= "polls_admin";
	var $module_name_label				= "Poll Manager Module (Administration - Advanced Version)";
	var $module_label					= "MANAGEMENT_POLL";
	var $module_grouping				= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_admin					= "1";
	var $module_creation				= "02/04/2004";
	var $module_version 				= '$Revision: 1.9 $';
	var $module_command					= "POLLSADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer					= "POLLS_";

	/**#@+
	* Class Variables
    * @var Integer
	*/
	var $has_module_contact=0;
	var $has_module_group=0;
	
	/**#@+
	* Class Variables
    * @var Array
	*/
	var $display_options 			= null;
	var $list_num_votes 			= Array(0, 3, 5, 10, 25, 50, 100);
	var $module_display_options 	= Array(Array("POLLS_DISPLAY","DISPLAY_POLL_CHANNEL_OPEN"));
	var	$security_list_labels 		= Array();
	var	$security_list_values 		= Array(0, 1, 3, 2, 4);	
	/**
	* call a specific modules command
	*
	* this function allows other modules to call this modules commands
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
			/*
			* General module functions
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
			if ($user_command==$this->module_command."GET_AUTHOR"){
				return $this->get_module_author();
			}
			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
				return $this->webContainer;
			}
			if ($user_command==$this->module_command."GET_CREATION"){
				return $this->get_module_creation();
			}
			/*
			* get the generic defintion of the database tables required for this module
			*/

			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			/*
			* preview a group of polls
			*/
			
			if ($user_command==$this->module_command."GROUP_PREVIEW"){
				$this->preview($parameter_list);
			}
			/**
            * functions locked to admin access role, calling these functions without premission will results in an empty string being returned
            */
			if ($this->module_admin_access){
				/**
                * Poll (single) - Functions 
                */
				if ($user_command==$this->module_command."LIST"){
					return $this->display_list($parameter_list);
				}
				if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
					return $this->polls_form($parameter_list);
				}
				if ($user_command==$this->module_command."SAVE"){
					$group_list = $this->polls_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST&amp;group_list=$group_list"));
				}
				if ($user_command==$this->module_command."REMOVE"){
					return $this->polls_remove($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE_CONFIRM"){
					$this->polls_remove_confirm($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST"));
				}
				/**
                * Poll (group) - Functions 
                */
				if ($user_command==$this->module_command."GROUP_LIST"){
					return $this->poll_group_list($parameter_list);
				}
				if (($user_command==$this->module_command."GROUP_ADD") || ($user_command==$this->module_command."GROUP_EDIT")){
					return $this->polls_group_form($parameter_list);
				}
				if ($user_command==$this->module_command."GROUP_SAVE"){
					$this->polls_group_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."GROUP_LIST"));
				}
				if ($user_command==$this->module_command."GROUP_REMOVE"){
					return $this->polls_group_remove($parameter_list);
				}
				if ($user_command==$this->module_command."GROUP_REMOVE_CONFIRM"){
					$this->polls_remove_group_confirm($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."GROUP_LIST"));
				}
				/**
				* inheritance function
				*/
				if ($user_command==$this->module_command."INHERIT"){
					$this->inherit($parameter_list);
				}
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
		$this->load_locale("poll_admin");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		
		/**
		* define the filtering information that is available
		*/
		$this->security_list_labels 		= Array(LOCALE_VOTE_SECURITY_OPTION_1, LOCALE_VOTE_SECURITY_OPTION_2, LOCALE_VOTE_SECURITY_OPTION_2_A, LOCALE_VOTE_SECURITY_OPTION_3, LOCALE_VOTE_SECURITY_OPTION_3_A);
		$this->module_admin_options 		= array(
			array("POLLSADMIN_GROUP_LIST","LOCALE_MANAGE_POLL_GROUPS"),
			array("POLLSADMIN_LIST","LOCALE_MANAGE_POLL_LIST")
		);
		$this->display_options		= array(
		array (0,"Order by Date Created (oldest first)"	,"poll_creation_date Asc"),
		array (1,"Order by Date Created (newest first)"	,"poll_creation_date desc"),
		array (2,"Order by Title A -> Z"				,"poll_title asc"),
		array (3,"Order by Title Z -> A"				,"poll_title desc")
		);

		$this->module_admin_user_access	= array(
			array($this->module_command."ALL","COMPLETE_ACCESS")
		);
		
		$this->module_admin_access		= 0;
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
				if (($this->module_command."ALL" == $access[$index]) || ("ALL"==$access[$index]) || ($this->module_command==substr($access[$index],0,strlen($this->module_command)))){
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
			array("poll_list_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("poll_list_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("poll_list_label"					,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("poll_list_answer1"				,"varchar(255)"				,""			,"default ''"),
			array("poll_list_answer2"				,"varchar(255)"				,""			,"default ''"),
			array("poll_list_answer3"				,"varchar(255)"				,""			,"default ''"),
			array("poll_list_answer4"				,"varchar(255)"				,""			,"default ''"),
			array("poll_list_answer5"				,"varchar(255)"				,""			,"default ''"),
			array("poll_list_answer6"				,"varchar(255)"				,""			,"default ''"),
			array("poll_list_answer7"				,"varchar(255)"				,""			,"default ''"),
			array("poll_list_answer8"				,"varchar(255)"				,""			,"default ''"),
			array("poll_list_answer9"				,"varchar(255)"				,""			,"default ''"),
			array("poll_list_answer10"				,"varchar(255)"				,""			,"default ''"),
			array("poll_list_date_created"			,"datetime"					,"" 		,"default NULL"),
			array("poll_list_created_by"			,"unsigned integer"			,"NOT NULL"	,"default ''"),
			array("poll_list_status"				,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("poll_list_question"				,"varchar(255)"				,""			,"default ''"),
			 // from here on is not used in the Enterprise version of the code they are here 
			 // so that if a second client is added to the database which is not running a Enterprise Licence
			 // then the table structure is set up to use the same structure as basic poll
			array("poll_list_results_available"		,"unsigned small integer"			,"NOT NULL"	,"default '0'"),
			array("poll_list_number_of_votes"		,"unsigned small integer"			,"NOT NULL"	,"default '0'"),
			array("poll_list_settings_for_votes"	,"unsigned small integer"			,"NOT NULL"	,"default '0'"),
			array("poll_list_display_settings"		,"unsigned small integer"			,"NOT NULL"	,"default '0'"),
			array("poll_list_security_option"		,"unsigned small integer"			,"NOT NULL"	,"default '0'"),
			array("poll_list_msg_already_voted"		,"varchar(255)"				,""			,"default ''"),
			array("poll_list_msg_thankyou"			,"varchar(255)"				,""			,"default ''"),
			array("poll_list_results_on_same_page"	,"unsigned small integer"			,"NOT NULL"	,"default '0'")
		);
		$primary ="poll_list_identifier";
		$tables[count($tables)] = array("poll_list", $fields, $primary);
		/**
		* Table structure for table 'group_access'
		*/
		$fields = array(
			array("poll_info_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("poll_info_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("poll_info_user"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("poll_info_date_voted"	,"datetime"			,"NOT NULL"	,"default ''"),
			array("poll_info_answer"		,"varchar(30)"		,"NOT NULL"	,"default ''"),
			array("poll_info_ip_address"	,"varchar(15)"		,"NOT NULL"	,"default ''"),
			array("poll_info_session_id"	,"varchar(32)"		,"NOT NULL"	,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("poll_info", $fields, $primary);
		/**
		* Table structure for table 'poll_group'
		*/
		$fields = array(
			array("pg_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("pg_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("pg_label"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("pg_display_option"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("pg_created_by"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("pg_date_created"			,"datetime"					,"NOT NULL"	,"default ''"),
			array("pg_results_available"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("pg_number_of_votes"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("pg_display_settings"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("pg_security_option"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("pg_msg_already_voted"	,"varchar(255)"				,""			,"default ''"),
			array("pg_msg_thankyou"			,"varchar(255)"				,""			,"default ''"),
			array("pg_results_on_same_page"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("pg_results_settings"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("pg_all_locations"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("pg_status"				,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("pg_set_inheritance"		,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="pg_identifier";
		$tables[count($tables)] = array("poll_group", $fields, $primary);
		/**
		* Table structure for table 'poll_group_list' relationship between group and polls
		*/
		$fields = array(
			array("pgl_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("pgl_client"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("pgl_group"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("pgl_poll"			,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="pgl_identifier";
		$tables[count($tables)] = array("poll_group_list", $fields, $primary);
		return $tables;
	}
	/**
    *									POLL (Administrative) FUNCTIONS
    */
	
	/**
	* display_list function
	*
	* This function returns the list of polls that exists for this client.
	* @uses $this->call_command("POLLSADMIN_LIST");
	* @param Array Keys =  "page"=> 1, "group_list" => -1
	* @return String a String containing an XML representation 
	*/
	function display_list($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"<pre>".print_r($parameters,true)."</pre>"));}
		$page 		= $this->check_parameters($parameters,"page",1);
		$group_list	= $this->check_parameters($parameters,"group_list",-1);
		$orderby=0;
		$gl = "";
		$this->page_size=50;
		if ($group_list > -1){
			$gl = " and pgl_group		= $group_list";
		}
		$sql = "Select *
				from poll_list 
					inner join poll_group_list on pgl_poll = poll_list_identifier 
				where 
					poll_list_client	= $this->client_identifier 
					$gl
				group by poll_list_identifier order by poll_list_identifier desc";
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables["PAGE_BUTTONS"] = Array(
			Array("CANCEL", $this->module_command."GROUP_LIST",LOCALE_CANCEL),
			Array("ADD", $this->module_command."ADD&amp;group_list=$group_list",ADD_NEW)
		);
		
		if (!$result){
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));}
			$number_of_records=0;
			$goto=0;
			$finish=0;
			$page=0;
			$num_pages=0;
		}else{
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));}
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
			$variables["as"]				= "table";
			
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page+=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= "";//$this->user_filter($grouplevel,$orderby);
			$variables["HEADER"] 			= MANAGEMENT_POLL." - ".LOCALE_LIST;
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				$counter++;
				if (!empty($r["total"])){
					$total = $r["total"];
				}else{
					$total = 0;
				}
				$i = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$i]=Array(
					"identifier"	=> $r["poll_list_identifier"],
					"attributes"	=> Array(
						Array(ENTRY_TITLE,$r["poll_list_question"],"TITLE","NO"),
						Array(ENTRY_DATE_CREATION,$r["poll_list_date_created"])
					),
					"ENTRY_BUTTONS" => Array(
						Array("EDIT", $this->module_command."EDIT&amp;group_list=$group_list", EDIT_EXISTING),
						Array("REMOVE", $this->module_command."REMOVE_CONFIRM&amp;group_list=$group_list", REMOVE_EXISTING)
					)
				);
				if($total>0){
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])] = Array("VIEW", $this->module_command."EDIT&amp;group_list=$group_list&amp;display_tab=show_results", LOCALE_VIEW_RESULTS);
				}
			}
		}
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$out = $this->generate_list($variables);
		return $out;
		
	}
	/**
	* polls_form function
	*
	* This will allow you to add/edit new/existing polls
	* @uses $this->call_command("POLLSADMIN_ADD");
	* @uses $this->call_command("POLLSADMIN_EDIT", Array("identifier"=>$poll_id));
	*/
	function polls_form($parameters){
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_form",__LINE__,"[]"));
		}
		$poll_groups						= Array();
		$poll_answer						= Array();
		$poll_identifier					= $this->check_parameters($parameters,"identifier",-1);
		$display_tab						= $this->check_parameters($parameters,"display_tab");
		$group_list							= $this->check_parameters($parameters,"group_list");
		$pg_list 							= "";
		$poll_list_results_available		= 0;
		$poll_question						= "";
		$poll_answer1						= "";
		$poll_answer2						= "";
		$poll_answer3						= "";
		$poll_answer4						= "";
		$poll_answer5						= "";
		$poll_answer6						= "";
		$poll_answer7						= "";
		$poll_answer8						= "";
		$poll_answer9						= "";
		$poll_answer10						= "";
		$poll_status						= 0;
		$poll_location						= -1;
		$menu_locations						=Array();
		for($index=1;$index<=10;$index++){
			$poll_answer["answer_$index"] 	= "";
		}
		if ($poll_identifier!=-1){
			$sql = "select * from poll_list where poll_list_client = $this->client_identifier and poll_list_identifier=$poll_identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
			$result = $this->call_command("DB_QUERY",array($sql));
			if($this->call_command("DB_NUM_ROWS",array($result))>0){
				while ($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
					$poll_question						= $r["poll_list_question"];
					for($index=1;$index<=10;$index++){
						$poll_answer["answer_$index"]	= $r["poll_list_answer$index"];
					}
					$poll_status						= $r["poll_list_status"];
				}
				$this->call_command("DB_FREE",array($result));
			}
			$sql = "select * from poll_group 
						left outer join poll_group_list  on pg_identifier = pgl_group and pg_client = pgl_client
					where pgl_poll = $poll_identifier and pg_client=$this->client_identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
			$result = $this->call_command("DB_QUERY", array($sql));
			if($this->call_command("DB_NUM_ROWS",array($result))>0){
				while ($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
					$poll_groups[count($poll_groups)] = $r["pg_identifier"];
				}
			}
			$this->call_command("DB_FREE",array($result));
		}
		$sql = "select * from poll_group where pg_client=$this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
		$result = $this->call_command("DB_QUERY", array($sql));
		if($this->call_command("DB_NUM_ROWS",array($result))>0){
			$max = count($poll_groups);
			while ($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
				$found=0;
				for ($index=0; $index< $max;$index++){
					if ($poll_groups[$index]==$r["pg_identifier"]){
						$found=1;
					}
				}
				$pg_list .= "<option value='".$r["pg_identifier"]."'";
				if ($found==1){
					$pg_list .= " selected='true'";
				}
				$pg_list .= "><![CDATA[".$r["pg_label"]."]]></option>";
			}
		}
		$this->call_command("DB_FREE",array($result));
//		$menu_locations =$this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_locations));
		
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		
		$out .= "<page_options><header>".LOCALE_POLL_MANAGER."</header>";
		$out .= "	<button command=\"POLLSADMIN_LIST&amp;group_list=$group_list\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"/>";
		$out .= "</page_options>";
		$out .= "<form name=\"poll_manager\" label=\"".LOCALE_POLL_TITLE_LABEL."\" method=\"post\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"POLLSADMIN_SAVE\"/>";
		$out .= "<input type=\"hidden\" name=\"group_list\" value=\"$group_list\"/>";
		$out .= "<input type=\"hidden\" name=\"poll_identifier\" value=\"$poll_identifier\"/>
		<page_sections>
		";
		$out .= "	<section label=\"".LOCALE_SETTINGS."\"";
		if ($display_tab=="content"){
			$out .= " selected='true'";
		}
		$out .= ">";
		
		$out .= "<input type=\"text\" label=\"".LOCALE_POLL_QUESTION."\" size=\"255\" name=\"poll_question\"><![CDATA[$poll_question]]></input>";
		$out .= "<select label=\"".LOCALE_STATUS."\" name=\"poll_status\">";
		$out .= "<option value=\"0\">".STATUS_NOT_LIVE."</option>";
		$out .= "<option value=\"1\"";
		if ($poll_status==1){
			$out .=" selected=\"true\"";
		}
		$out .= ">".STATUS_LIVE."</option>";
		$out .= "</select>";
		$answer_counter = array();
		$total=0;
		if (!empty($poll_identifier)){
			$Query_String_data = "select poll_info_answer, count(poll_info_answer) as total from poll_info where poll_info_identifier = $poll_identifier group by poll_info_answer";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$Query_String_data"));
			}

			if ($Data_Result = $this->call_command("DB_QUERY",array($Query_String_data))){
				while ($row = $this->call_command("DB_FETCH_ARRAY",array($Data_Result))){
					$answer_counter["answer_".$row["poll_info_answer"]] = $row["total"];
					$total += $row["total"];
				}
			}
		}
		$out .= "<text><![CDATA[".LOCALE_POLL_ANSWER_STATEMENT."]]></text>";
		for($index=1;$index<=10;$index++){
			$out .= "<input type=\"text\" label=\"".LOCALE_ANSWER." $index\" size=\"255\" name=\"poll_answer$index\"><![CDATA[".$poll_answer["answer_$index"]."]]></input>";
		}
		$out .= "	</section><section label=\"".LOCALE_MANAGE_POLL_GROUPS."\"";
		if ($display_tab=="poll_groups"){
			$out .= " selected='true'";
		}
		$out .= ">";
		$out .= "<checkboxes label=\"".LOCALE_MANAGE_POLL_GROUPS_OPTIONS."\" name=\"poll_groups\">$pg_list</checkboxes>";
		$out_result = "";
		for($index=1;$index<=10;$index++){
			$answer = $this->check_parameters($poll_answer,"answer_$index","");
			if ($answer!=""){
				$a 			= $this->check_parameters($answer_counter,"answer_$index",0);
				if ($a==0 || $total==0){
					$per_value=0;
				} else {
					$per_value	= round(($a/$total)*100,2);
				}
				$out_result.="<tr><th align='left'>".$answer."</th><td><img src='/libertas_images/general/graphs/bar_left.gif' width='5' height='20' alt='".$per_value."%'/>";
				$out_result.="<img src='/libertas_images/general/graphs/bar_middle.gif' width='".($per_value)."' height='20' alt='".$per_value."%'/>";
				$out_result.="<img src='/libertas_images/general/graphs/bar_right.gif' width='5' height='20' alt='".$per_value."%'/> ".$a." votes with ".($per_value)."% of the vote</td></tr>";
			}
		}
		if ($out_result!=""){
			$out .= "	</section><section label=\"".LOCALE_RESULTS."\"";
			if ($display_tab=="show_results"){
				$out .= " selected='true'";
			}
			$out .= ">";
			$out .= "<text><![CDATA[<table>";
			$out .= $out_result;
			$out .= "</table>]]></text>";
		}
		$out .= "	</section><section label=\"".LOCALE_PREVIEW."\" onclick='preview_poll' ";
		if ($display_tab=="poll_preview"){
			$out .= " selected='true'";
		}
		$out .= "><div id='pollPreview'></div>";
		$out .= "</section>";
		$out .= "</page_sections>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>
		</form>";
		$out .="</module>";
		return $out;
	}
	
	/**
	* polls_save function
	*
	* save the information from the poll 
	*/
	function polls_save($parameters){
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_save",__LINE__,"[]"));
		}
		
		$ok=0;
		$group_list						=	$this->check_parameters($parameters,"group_list");
		$poll_list_identifier			=	$this->check_parameters($parameters,"poll_identifier",-1);
		$poll_list_status				=	$this->check_parameters($parameters,"poll_status");
		$poll_groups					=	$this->check_parameters($parameters,"poll_groups",Array());
		$poll_list_question 			=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_question"))));
		$poll_list_answer1				=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_answer1"))));
		$poll_list_answer2				=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_answer2"))));
		$poll_list_answer3				=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_answer3"))));
		$poll_list_answer4				=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_answer4"))));
		$poll_list_answer5				=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_answer5"))));
		$poll_list_answer6				=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_answer6"))));
		$poll_list_answer7				=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_answer7"))));
		$poll_list_answer8				=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_answer8"))));
		$poll_list_answer9				=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_answer9"))));
		$poll_list_answer10				=	htmlentities(strip_tags($this->validate($this->check_parameters($parameters,"poll_answer10"))));
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$poll_list_location	=$this->check_parameters($parameters,"poll_location",Array());
		} else {
			$poll_list_location	=$this->check_parameters($parameters,"poll_location",-1);
		}
		$user_identifier = $this->call_command("SESSION_GET",Array("SESSION_USER_IDENTIFIER"));
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		if ($poll_list_identifier==-1){
			/**
			* Add a new poll to the system
			*/

			$fields = " poll_list_question, poll_list_answer1, poll_list_answer2, poll_list_answer3, poll_list_answer4, poll_list_answer5, poll_list_answer6, poll_list_answer7, poll_list_answer8, poll_list_answer9, poll_list_answer10, poll_list_date_created, poll_list_created_by, poll_list_client, poll_list_status";
			$values = "'$poll_list_question', '$poll_list_answer1', '$poll_list_answer2', '$poll_list_answer3', '$poll_list_answer4', '$poll_list_answer5', '$poll_list_answer6', '$poll_list_answer7', '$poll_list_answer8', '$poll_list_answer9', '$poll_list_answer10', '$now', $user_identifier, $this->client_identifier, $poll_list_status";
			$sql = "insert into poll_list ($fields) values ($values)";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
			
			$this->call_command("DB_QUERY",array($sql));
			$sql = "select * from poll_list where poll_list_date_created='$now' and poll_list_created_by=$user_identifier and poll_list_client=$this->client_identifier and poll_list_status=$poll_list_status";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$poll_list_identifier = $r["poll_list_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			$max = count($poll_groups);
			$sql = "delete from poll_group_list where pgl_poll = '$poll_list_identifier' and pgl_client= '$this->client_identifier'";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
			$this->call_command("DB_QUERY",array($sql));
			for ($index=0; $index<$max; $index++){
				$sql = "insert into poll_group_list ( pgl_group, pgl_poll, pgl_client) values ('".$poll_groups[$index]."','$poll_list_identifier', '$this->client_identifier')";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
				$this->call_command("DB_QUERY",array($sql));
			}
		} else {
			/**
			* update an existing poll in the system
			*/
			$fields = "poll_list_question='$poll_list_question', poll_list_answer1='$poll_list_answer1',
			poll_list_answer2='$poll_list_answer2',	poll_list_answer3='$poll_list_answer3',	poll_list_answer4='$poll_list_answer4',	poll_list_answer5='$poll_list_answer5',	poll_list_answer6='$poll_list_answer6',	poll_list_answer7='$poll_list_answer7', poll_list_answer8='$poll_list_answer8', poll_list_answer9='$poll_list_answer9', poll_list_answer10='$poll_list_answer10', poll_list_status=$poll_list_status";
			$sql = "update poll_list set $fields where poll_list_client= $this->client_identifier and poll_list_identifier=$poll_list_identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
			$this->call_command("DB_QUERY",array($sql));
			$max = count($poll_groups);
			$sql = "delete from poll_group_list where pgl_poll = '$poll_list_identifier' and pgl_client= '$this->client_identifier'";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
			$this->call_command("DB_QUERY",array($sql));
			for ($index=0; $index<$max; $index++){
				$sql = "insert into poll_group_list ( pgl_group, pgl_poll, pgl_client) values ('".$poll_groups[$index]."','$poll_list_identifier', '$this->client_identifier')";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
				$this->call_command("DB_QUERY",array($sql));
			}
		}
		return $group_list;
	}
	/**
	* polls_remove function
	*
	* display the confirm screen
	*/
	function polls_remove($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"<pre>".print_r($parameters,true)."</pre>"));}
		$out  = "<module name=\"".$this->module_name."\" display=\"remove_form\">";
		$out .= " <form label=\"".POLL_REMOVE_LABEL."\" name=\"".$this->module_name."_remove_form\" method=\"post\">";
		$out .= "  <input type=\"hidden\" name=\"command\" value=\"POLLSADMIN_REMOVE_CONFIRM\"/>";
		$out .= "  <input type=\"hidden\" name=\"poll_identifier\" value=\"".$parameters["identifier"]."\"/>";
		$out .= "  <text><![CDATA[".POLL_REMOVE_CONFIRMATION_LABEL."]]></text>";
		$out .= "  <input type=\"button\" iconify=\"NO\" value=\"".ENTRY_NO."\" command=\"POLLSADMIN_LIST\"/>";
		$out .= "  <input type=\"submit\" iconify=\"YES\"  value=\"".ENTRY_YES."\" />";
		$out .= " </form>";
		$out .= "</module>";
		
		return $out;
	}
	/***
	* polls_remove_confirm function
	*
	* This function will generate the proper table structure in the choosen database
	* format.
	*/
	function polls_remove_confirm($parameters){
		$id = $this->check_parameters($parameters,"identifier",-1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_remove_confirm",__LINE__,""));
		}
		$sql = "delete from poll_list where poll_list_identifier=".$id." and poll_list_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$this->call_command("DB_QUERY",array($sql));
		$sql = "delete from poll_info where poll_info_identifier=".$id." and poll_info_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$this->call_command("DB_QUERY",array($sql));
		return true;
	}
	function poll_group_list($parameters){
		$page = $this->check_parameters($parameters,"page",1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_list",__LINE__,"[$page]"));
		}
		$orderby=0;
		
		$sql = "
			Select poll_group.*, count(pgl_identifier) as total 
				from poll_group 
					left outer join poll_group_list on pgl_group = pg_identifier and pgl_client= pg_client 
				where 
					pg_client= $this->client_identifier
				group by pg_identifier
				order by pg_identifier desc
		";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables["PAGE_BUTTONS"] = Array(
			Array ("ADD",$this->module_command."GROUP_ADD",ADD_NEW)
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
			$this->page_size=50;
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
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
			$variables["as"]				= "table";
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page+=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= "";//$this->user_filter($grouplevel,$orderby);
			$variables["HEADER"] 			= MANAGEMENT_POLL." - ".LOCALE_GROUP_LIST;

			
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			$list_settings = Array(LOCALE_DISPLAY_SETTINGS_RANDOM, LOCALE_DISPLAY_SETTINGS_CYCLE, LOCALE_DISPLAY_SETTINGS_PER_SESSION);
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				if (!empty($r["total"])){
					$total = $r["total"];
				}else{
					$total = 0;
				}
				$pg_security_option_label="";
				$pg_identifier	= $r["pg_identifier"];
				$pg_security_option = $r["pg_security_option"];
				for($security =0; $security < count($this->security_list_values);$security++){
					if($this->security_list_values[$security] == $pg_security_option){
						$pg_security_option_label = $this->security_list_labels[$security];
					}
				}
				$variables["RESULT_ENTRIES"][$i] = Array(
					"identifier"	=> $r["pg_identifier"],
					"attributes"	=> Array(
						Array(ENTRY_TITLE			,$r["pg_label"],			"TITLE","NO"),
						Array(ENTRY_DATE_CREATION	,$r["pg_date_created"]),
						Array(LOCALE_POLLS_IN_GROUP	,$total),
						Array(LOCALE_DISPLAY_OPTION	,$list_settings[$r["pg_display_settings"]]),
						Array(LOCALE_DISPLAY_RESULTS,($r["pg_results_settings"]==0) ? LOCALE_NO : LOCALE_YES),
						Array(LOCALE_VOTE_SECURITY	,$pg_security_option_label)
					),
					"ENTRY_BUTTONS" => Array(
						Array("EDIT",	$this->module_command."GROUP_EDIT",EDIT_EXISTING),
						Array("REMOVE",	$this->module_command."GROUP_REMOVE_CONFIRM",REMOVE_EXISTING)
					)
				);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	= Array("LIST",$this->module_command."LIST&amp;group_list=$pg_identifier",LOCALE_MANAGE_LIST);
			}
		}
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$out = $this->generate_list($variables);
		return $out;
	}

/*
	*
	* polls_form function
	-----------------------
	- This function will generate the proper table structure in the choosen database
	- format.
	*/
	function polls_group_form($parameters){
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_form",__LINE__,"[]"));
		}
		$identifier							= $this->check_parameters($parameters,"identifier",-1);
		$display_tab						= $this->check_parameters($parameters,"display_tab");
		$pg_label							= "";
		$menu_locations						= Array();
		$poll_list							= "";
		$poll_list_results_available		= 0;
		$pg_status							= 0;
		$poll_list_number_of_votes			= 0;
		$all_locations						= 0;
		$poll_list_results_settings			= 0;
		$poll_list_security_option			= 0;
		$poll_list_results_on_same_page		= 0;
		$poll_list_msg_already_voted		= "You have already voted on this poll";
		$poll_list_msg_thankyou				= "Thankyou for voting on our poll";
		$pg_display_settings				= 0;
		$set_inheritance					= 0;
		if (!empty($identifier)){
			$sql = "select * from poll_group where pg_client=$this->client_identifier and pg_identifier=".$identifier;
			$result = $this->call_command("DB_QUERY",array($sql));
			if($this->call_command("DB_NUM_ROWS",array($result))>0){
				while ($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
					$pg_label							= $r["pg_label"];
					$poll_list_results_available		= $r["pg_results_available"];
					$poll_list_number_of_votes			= $r["pg_number_of_votes"];
					$pg_status							= $r["pg_status"];
					$pg_display_settings				= $r["pg_display_settings"];
					$poll_list_results_settings			= $r["pg_results_settings"];
					$poll_list_security_option			= $r["pg_security_option"];
					$all_locations						= $r["pg_all_locations"];
					$poll_list_msg_already_voted		= $this->check_parameters($r, "pg_msg_already_voted", LOCALE_POLL_LIST_MSG_ALREADY_VOTED);
					$poll_list_msg_thankyou				= $this->check_parameters($r,"pg_msg_thankyou", LOCALE_POLL_LIST_MSG_THANKYOU);
					$poll_list_results_on_same_page		= $this->check_parameters($r,"pg_results_on_same_page");
					$set_inheritance					= $r["pg_set_inheritance"];;
					if ($poll_list_msg_already_voted==""){
						$poll_list_msg_already_voted=LOCALE_POLL_LIST_MSG_ALREADY_VOTED;
					}
					if ($poll_list_msg_thankyou==""){
						$poll_list_msg_thankyou=LOCALE_POLL_LIST_MSG_THANKYOU;
					}
				}
				$this->call_command("DB_FREE",array($result));
			}
			$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
				Array(
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier
				)
			);
/*
			$sql = "select poll_group_menu.pgm_menu as m_id from poll_group_menu where pgm_client=$this->client_identifier and pgm_group=".$identifier;
			$result = $this->call_command("DB_QUERY",array($sql));
			if($this->call_command("DB_NUM_ROWS",array($result))>0){
				while ($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
					$menu_locations[count($menu_locations)]	= $r["m_id"];
				}
				$this->call_command("DB_FREE",array($result));
			}
			*/
		}

		$sql ="select * from poll_list inner join poll_group_list on pgl_client = poll_list_client and poll_list_identifier = pgl_poll where poll_list_client = $this->client_identifier and pgl_group = $identifier";

		$poll_list_array = Array();
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$poll_list_array[count($poll_list_array)] = $r["poll_list_identifier"];
        }
        $this->call_command("DB_FREE",Array($result));
		$max = count($poll_list_array);
		$sql ="select * from poll_list where poll_list_client = $this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		if($this->call_command("DB_NUM_ROWS",array($result))>0){
			while ($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
				$poll_list .= "<option value='".$r["poll_list_identifier"]."' ";
				$found=0;
				for ($index=0;$index<$max;$index++){
					if ($poll_list_array[$index] == $r["poll_list_identifier"]){
						$found=1;
					}
				}
				if ($found==1){
					$poll_list .= " selected='true'";
				}
				$poll_list .= "><![CDATA[" . $r["poll_list_question"] . "]]></option>";
			}
			$this->call_command("DB_FREE",array($result));
		}
		//$menu_locations =$this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_locations));

		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options><header>".LOCALE_POLL_MANAGER."</header>";
		$out .= "	<button command=\"POLLSADMIN_GROUP_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"/>";
		$out .= "</page_options>";
		$out .= "<form name=\"poll_manager\" label=\"".LOCALE_POLL_TITLE_LABEL."\" method=\"post\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"POLLSADMIN_GROUP_SAVE\"/>";
		$out .= "<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>
		<page_sections>
		";
		$out .= "	<section label=\"".LOCALE_SETTINGS."\"";
		if ($display_tab=="content"){
			$out .= " selected='true'";
		}
		$out .= ">";
		
		$out .= "<input type=\"text\" label=\"".LOCALE_LABEL."\" size=\"255\" required='YES' name=\"pg_label\"><![CDATA[$pg_label]]></input>";

		$out .= "<select label=\"".LOCALE_STATUS."\" name=\"pg_status\">";
			$out .= "<option value=\"0\">".STATUS_NOT_LIVE."</option>";
			$out .= "<option value=\"1\"";
			if ($pg_status==1){
				$out .=" selected=\"true\"";
			}
			$out .= ">".STATUS_LIVE."</option>";
			$out .= "</select>";

		
		$out .= "<select label=\"".LOCALE_DISPLAY_OPTIONS."\" name=\"pg_display_settings\">";
		$list_settings = Array(LOCALE_DISPLAY_SETTINGS_RANDOM, LOCALE_DISPLAY_SETTINGS_CYCLE, LOCALE_DISPLAY_SETTINGS_PER_SESSION);
		for($index=0;$index<count($list_settings);$index++){
			$out .= "<option value=\"$index\"";
			if ($index==$pg_display_settings){
				$out .=" selected=\"true\"";
			}
			$out .= ">".$list_settings[$index]."</option>";
		}
		$out .= "</select>";
		$out .= "<select label=\"".LOCALE_RESULTS_AVAILABLE."\" name=\"pg_results_available\">";
		$out .= "<option value=\"0\">".LOCALE_NO."</option>";
		$out .= "<option value=\"1\"";
			if ($poll_list_results_available==1){
				$out .=" selected=\"true\"";
			}
			$out .= ">".LOCALE_YES."</option>";
			$out .= "</select>";
				
			$out .= "<select label=\"".LOCALE_RESULTS_SAME_PAGE."\" name=\"pg_results_on_same_page\">";
			$out .= "<option value=\"0\">".LOCALE_NO_USE_DIFFERENT_PAGE."</option>";
			$out .= "<option value=\"1\"";
			if ($poll_list_results_on_same_page==1){
				$out .=" selected=\"true\"";
			}
			$out .= ">".LOCALE_YES."</option>";
			$out .= "</select>";
			
			$out .= "<select label=\"".LOCALE_RESULTS_AVAILABLE_AFTER."\" name=\"pg_number_of_votes\">";
			
			for($index=0;$index<count($this->list_num_votes);$index++){
				$out .= "<option value=\"$index\"";
				if ($index==$poll_list_number_of_votes){
					$out .=" selected=\"true\"";
				}
				$out .= ">".$this->list_num_votes[$index]." ".LOCALE_VOTES."</option>";
			}
			$out .= "</select>";

			$out .= "<select label=\"".LOCALE_RESULTS_DISPLAY_SETTING."\" name=\"pg_results_settings\">";
			$list_settings = Array(LOCALE_DISPLAY_AS_BAR_GRAPH, LOCALE_DISPLAY_AS_PERCENTAGE, LOCALE_DISPLAY_AS_NUM_VOTES);
			for($index=0;$index<count($list_settings);$index++){
				$out .= "<option value=\"$index\"";
				if ($index==$poll_list_results_settings){
					$out .=" selected=\"true\"";
				}
				$out .= ">".$list_settings[$index]."</option>";
			}
			$out .= "</select>";

			
			$out .= "<select label=\"".LOCALE_VOTE_SECURITY_LABEL."\" name=\"pg_security_option\">";
			$out .= $this->gen_options($this->security_list_values, $this->security_list_labels, $poll_list_security_option);
			$out .= "</select>";
			$val = $this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>$this->webContainer, "identifier"=>$identifier));
			$web_containers = split("~----~",$val);
			if ($web_containers[0]!=""){
				$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
				$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
			}

			$out .= "	</section><section label=\"".LOCALE_MANAGE_POLL_LIST."\"";
			if ($display_tab=="poll_list"){
				$out .= " selected='true'";
			}
			$out .= ">";
			$out .= "<checkboxes label=\"".LOCALE_MANAGE_POLL_LIST_OPTIONS."\" name=\"poll_list\">$poll_list</checkboxes>";

			$out .= "	</section>";
			
		$out .= $this->location_tab($all_locations, $set_inheritance,$menu_locations, $display_tab);
		$out .= "<section label=\"".LOCALE_MSGS."\"";
		if ($display_tab=="content"){
			$out .= " selected='true'";
		}
		$out .= ">";
		$out .="<input type='text' name='pg_msg_thankyou' size='255' label='".LOCALE_CONFIRM_MESSAGE."'><![CDATA[$poll_list_msg_thankyou]]></input>";
		$out .="<input type='text' name='pg_msg_already_voted' size='255' label='".LOCALE_ALL_READY_VOTED_MESSAGE."'><![CDATA[$poll_list_msg_already_voted]]></input>";
		$out .= "</section>";
		$out .= $this->preview_section("POLLSADMIN_GROUP_PREVIEW",1,1);
		$out .= "</page_sections>";
		
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>
		
		</form>";
		$out .="</module>";
		return $out;
	}
	function polls_group_save($parameters){
		$debug = $this->debugit(false, $parameters);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_save",__LINE__,"[]"));
		}
		
		$ok=0;
		$identifier								= $this->check_parameters($parameters,"identifier");
		$menu_locations							= $this->check_parameters($parameters, "menu_locations", Array());
		$user_identifier 						= $this->call_command("SESSION_GET",Array("SESSION_USER_IDENTIFIER"));
		$poll_list								= $this->check_parameters($parameters, "poll_list",Array());
		
		$field = Array();
		$field["pg_label"]						= trim($this->strip_tidy($this->check_parameters($parameters,"pg_label")));
		$field["pg_results_available"]			= $this->check_parameters($parameters, "pg_results_available");
		$field["pg_number_of_votes"]			= $this->check_parameters($parameters, "pg_number_of_votes");
		$field["pg_display_settings"]			= $this->check_parameters($parameters, "pg_display_settings");
		$field["pg_security_option"]			= $this->check_parameters($parameters, "pg_security_option");
		$field["pg_msg_already_voted"]			= trim($this->strip_tidy($this->check_parameters($parameters, "pg_msg_already_voted")));
		$field["pg_msg_thankyou"]				= trim($this->strip_tidy($this->check_parameters($parameters, "pg_msg_thankyou")));
		$field["pg_results_on_same_page"]		= $this->check_parameters($parameters, "pg_results_on_same_page");
		$field["pg_results_settings"]			= $this->check_parameters($parameters, "pg_results_settings");
		$field["pg_all_locations"]				= $this->check_parameters($parameters, "all_locations");
		$field["pg_status"]						= $this->check_parameters($parameters, "pg_status");
		$field["pg_set_inheritance"]			= $this->check_parameters($parameters, "set_inheritance");

		$replacelist=Array();
		$currentlyhave							= $this->check_parameters($parameters	,"currentlyhave");
		$count_rss_containers					= $this->check_parameters($parameters	,"totalnumberofchecks_web_containers");
/*		for($index=1 ; $index <= $count_rss_containers; $index++){
			$rss_containers	= $this->check_parameters($parameters,"web_containers_$index",Array());
			$len = count($rss_containers);
			for($i=0;$i < $len; $i++){
				$replacelist[count($replacelist)] = $rss_containers[$i];
			}
		}*/
		$replacelist	= $this->check_parameters($parameters,"web_containers",Array());
//		if ($identifier==-1)
		
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		$fields = "";
		$values = "";
		$joined	= "";

		if ($identifier==-1){
			/**
			* Add a new poll group to the system
			*/
			foreach ($field as $key => $value){
				$fields .= "$key, ";
				$values .= "'$value', ";
			}
			$sql = "insert into poll_group ($fields pg_date_created, pg_created_by, pg_client) values ($values '$now', $user_identifier, '$this->client_identifier')";
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "select * from poll_group where pg_date_created='$now' and pg_created_by=$user_identifier and pg_client=$this->client_identifier";
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$identifier = $r["pg_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $field["pg_label"],
					"wo_command"	=> "POLLS_DISPLAY",
					"cmd"			=> "ADD",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
			
		} else {
			/**
			* update an existing poll in the system
			*/
			foreach ($field as $key => $val) {
				if ($joined!=""){
					$joined .= ", ";
				}
				$joined .= "$key = '$val'";
			}
			$sql = "update poll_group set $joined
				 where pg_client= $this->client_identifier and pg_identifier=$identifier";
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			$this->call_command("DB_QUERY",array($sql));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $field["pg_label"],
					"wo_command"	=> "POLLS_DISPLAY",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
		}
		/*
		Save menu locations
		*/
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier,
				"all_locations"	=> $field["pg_all_locations"]
			)
		);

		$max = count($poll_list);
		$sql = "delete from poll_group_list where pgl_group = '$identifier' and pgl_client= '$this->client_identifier'";
		if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
		$this->call_command("DB_QUERY",array($sql));
		for ($index=0; $index<$max; $index++){
			$sql = "insert into poll_group_list ( pgl_group, pgl_poll, pgl_client) values ('$identifier', '".$poll_list[$index]."', '$this->client_identifier')";
			$this->call_command("DB_QUERY",array($sql));
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
		}
		if ($field["pg_set_inheritance"]==1){
			$child_locations = $this->add_inheritance("POLLS_DISPLAY",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=>$child_locations,
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier,
					"all_locations"	=> $field["pg_all_locations"],
					"delete"		=>0
				)
			);
			$this->set_inheritance(
				"POLLS_DISPLAY",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"=> $this->webContainer,
					"condition"=> "pg_set_inheritance =1 and ",
					"client_field"=> "pg_client",
					"table"	=> "poll_group",
					"primary"=> "pg_identifier"
					)
				)."
				"
				
			);

		}
		
		$this->tidyup_display_commands(Array(
			"all_locations" 		=> $field["pg_all_locations"],
			"tidy_table"			=> "poll_group",
			"tidy_field_starter"	=> "pg_",
			"tidy_webobj"			=> $this->webContainer."DISPLAY",
			"tidy_module"			=> $this->webContainer
		));
		if ($debug) $this->exitprogram();
	}


	/**
	* polls_remove function
	*/
	function polls_remove_group($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_remove",__LINE__,""));
		}
		
		$out  = "<module name=\"".$this->module_name."\" display=\"remove_form\">";
		$out .= " <form label=\"".POLL_REMOVE_LABEL."\" name=\"".$this->module_name."_remove_form\" method=\"post\">";
		$out .= "  <input type=\"hidden\" name=\"command\" value=\"POLLSADMIN_REMOVE_GROUP_CONFIRM\"/>";
		$out .= "  <input type=\"hidden\" name=\"poll_identifier\" value=\"".$parameters["identifier"]."\"/>";
		$out .= "  <text><![CDATA[".POLL_REMOVE_CONFIRMATION_LABEL."]]></text>";
		$out .= "  <input type=\"button\" iconify=\"NO\" value=\"".ENTRY_NO."\" command=\"POLLSADMIN_GROUP_LIST\"/>";
		$out .= "  <input type=\"submit\" iconify=\"YES\"  value=\"".ENTRY_YES."\" />";
		$out .= " </form>";
		$out .= "</module>";
		
		return $out;
	}
	/**
	* polls_remove_confirm function
	*
	* This function will remove the Poll grouping
	*/
	function polls_remove_group_confirm($parameters){
		
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_remove_confirm",__LINE__,""));
		}
		$sql = "delete from poll_group where pg_identifier= $identifier and pg_client = $this->client_identifier";
		if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("LAYOUT_MENU_TO_OBJECT_REMOVE", Array(
				"module"		=>	$this->webContainer, 
				"identifier"	=>	$identifier
			)
		);
		$sql = "select * from poll_group_list where pgl_group = $identifier and pgl_client = $this->client_identifier";
		if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$polls = Array();
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$polls[count($polls)] = $r["pgl_poll"];
        }
        $this->call_command("DB_FREE",Array($result));
		$sql = "delete from poll_group_list where pgl_group = $identifier and pgl_client = $this->client_identifier";
		if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
		$this->call_command("DB_QUERY",Array($sql));
		$max = count($polls);
		for($index=0;$index<$max;$index++){
			$id = $polls[$index];
			$sql = "delete from poll_list where poll_list_identifier=".$id." and poll_list_client=$this->client_identifier";
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from poll_info where poll_info_identifier=".$id." and poll_info_client=$this->client_identifier";
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			$this->call_command("DB_QUERY",array($sql));
		}
		if ($debug) $this->exitprogram();
		return true;
	}

	function inherit($parameters){
		$menu_id		= $this->check_parameters($parameters,"menu_identifier",-1);
		$menu_parent 	= $this->check_parameters($parameters,"menu_parent",-1);
		$this->call_command("LAYOUT_MENU_TO_OBJECT_INHERIT",Array(
			"menu_location"	=> $menu_id,
			"menu_parent"	=> $menu_parent,
			"module"		=> $this->webContainer,
			"condition"		=> "pg_set_inheritance =1 and ",
			"client_field"	=> "pg_client",
			"table"			=> "poll_group",
			"primary"		=> "pg_identifier"
			)
		);
	}
	
	function preview($parameters){
		$poll_list 				= $this->check_parameters($parameters,"poll_list");
		$pg_display_settings	= $this->check_parameters($parameters,"pg_display_settings");
		$pg_label				 = $this->check_parameters($parameters,"pg_label");
		$pos = rand(1,count($poll_list));
		$identifier  = $this->check_parameters($poll_list,$pos-1,-1);
		$sql = "select * from poll_list where poll_list_identifier =$identifier and poll_list_client=$this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$out = "<h1 class='tableheader'>".$pg_label."</h1>";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$out  		.= "<strong>".$this->check_parameters($r, "poll_list_question")."</strong><br/>"; 
			for($index=1; $index<11; $index++){
				$ans = $this->check_parameters($r, "poll_list_answer$index");
				if($ans!=""){
	        		$out .= "<input type='radio' >".$ans."<br/>"; 
				}
			}
       		$out .= "<input type='button' class='bt' value='".LOCALE_VOTE_NOW."' xonclick='alert(\"You can not submit information on this poll in Preview mode\");'>"; 
        }
        $this->call_command("DB_FREE",Array($result));
		print "<link rel='stylesheet' type='text/css' href='/libertas_images/themes/site_administration/style.css'><table><tr><td>$out</td></tr></table><script>
		try{
			if(parent.document){
				if (parent.document.all.preview_loaded.value==0){
					parent.document.all.preview.style.display='';
					parent.document.all.preview_loading.style.display='none';
					parent.document.all.refreshBtn.style.display='';
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