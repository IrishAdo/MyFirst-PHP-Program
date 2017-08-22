<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.polls.php
* @date 09 Oct 2002
*/
/**
* WCM product only
*/
class polls extends module{
	/**#@+
	* Class Variables
    * @var string
	*/
	var $module_name						= "polls";
	var $module_label						= "MANAGEMENT_POLL";
	var $module_name_label					= "Poll Manager Module ";
	var $module_grouping					= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_admin						= "1";
	var $module_debug						= false;
	var $module_creation					= "13/09/2002";
	var $module_version						= '$Revision: 1.9 $';
	var $module_command						= "POLLS_"; 		// all commands specifically for this module will start with this token
	var $webContainer						= "POLLS_";
	
	
	/**#@+
	* Class Variables
    * @var Integer
	*/
	var $has_module_contact					= 0;
	var $has_module_group					= 0;


	/**#@+
	* Class Variables
    * @var Array
	*/
	var $display_options					= null;
	var $module_display_options 			= array(array("POLLS_DISPLAY","LOCALE_POLL_CHANNEL_OPEN","POLL_MANAGER"));
	var $module_admin_options 				= array(array("POLLS_LIST","MANAGEMENT_POLL"));

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
			if ($this->module_admin_access){
				if ($user_command==$this->module_command."SAVE"){
					$this->polls_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST"));
				}
				if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
					return $this->polls_form($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE"){
					return $this->polls_remove($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE_CONFIRM"){
					$this->polls_remove_confirm($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST"));
				}
				if ($user_command==$this->module_command."LIST"){
					return $this->display_list($parameter_list);
				}
			}
			if ($user_command==$this->module_command."VOTE"){
				$vote = $this->polls_vote($parameter_list);
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("poll_vote=$vote&amp;poll_id=".$parameter_list["poll_identifier"]));
			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->polls_display($parameter_list);
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
		$this->load_locale("poll");
		$this->load_locale("poll_admin");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		
		/**
		* define the filtering information that is available
		*/
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
			array("poll_info_answer"		,"varchar(30)"		,"NOT NULL"	,"default ''"),
			array("poll_info_ip_address"	,"varchar(15)"		,"NOT NULL"	,"default ''"),
			array("poll_info_session_id"	,"varchar(32)"		,"NOT NULL"	,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("poll_info", $fields, $primary);
		return $tables;
	}
	/**
	* display_list function
	-----------------------
	- This function returns the list of polls that exists for this client.
	*/
	function display_list($parameters){
		$page = $this->check_parameters($parameters,"page",1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_list",__LINE__,"[$page]"));
		}
		$orderby=0;
		
		$sql = "Select poll_list.*, count(poll_info_identifier) as total_votes from poll_list left outer join poll_info on poll_list_identifier = poll_info_identifier where poll_list_client= $this->client_identifier group by poll_list_identifier order by poll_list_identifier desc";
		
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
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$goto = ((--$page)*PAGE_SIZE);
			
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
			}
			if ($goto+PAGE_SIZE>$number_of_records){
				$finish = $number_of_records;
			}else{
				$finish = $goto+PAGE_SIZE;
			}
			$goto++;
			$page++;
			
			$num_pages=floor($number_of_records / PAGE_SIZE);
			$remainder = $number_of_records % PAGE_SIZE;
			if ($remainder>0){
				$num_pages++;
			}
			
			$counter=0;
			
			
			
			$start_page=intval($page/PAGE_SIZE);
			$remainder = $page % PAGE_SIZE;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+PAGE_SIZE)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page+=PAGE_SIZE;
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= "";//$this->user_filter($grouplevel,$orderby);
			$variables["HEADER"] 			= MANAGEMENT_POLL." - ".LOCALE_LIST;

			$variables["ENTRY_BUTTONS"] =Array(
			Array("EDIT",$this->module_command."EDIT",EDIT_EXISTING),
			Array("REMOVE",$this->module_command."REMOVE_CONFIRM",REMOVE_EXISTING)
			);
			
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				$counter++;
				if (!empty($r["total"])){
					$total = $r["total"];
				}else{
					$total = 0;
				}
				$variables["RESULT_ENTRIES"][count($variables["RESULT_ENTRIES"])]=Array(
					"identifier"	=> $r["poll_list_identifier"],
					"attributes"	=> Array(
						Array(ENTRY_TITLE,$r["poll_list_label"],"TITLE","NO"),
						Array(ENTRY_DATE_CREATION,$r["poll_list_date_created"])
					)
				);
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
	-----------------------
	- This function will generate the proper table structure in the choosen database
	- format.
	*/
	function polls_form($parameters){
		$debug = $this->debugit(false,$parameters);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_form",__LINE__,"[]"));
		}
		$poll_identifier=$this->check_parameters($parameters,"identifier");
		$display_tab=$this->check_parameters($parameters,"display_tab");
		$poll_label="";
		$poll_answer= Array();
		for($index=1;$index<=10;$index++){
			$poll_answer["answer_$index"] = "";
		}
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
		$poll_list_number_of_votes			= 0;
		$menu_locations						= Array();
		$poll_list_results_available		= 0;
		$poll_list_number_of_votes			= 0;
		$poll_list_security_option			= 0;
		$poll_list_results_on_same_page		= 0;
		$poll_list_msg_already_voted		= LOCALE_POLL_LIST_MSG_ALREADY_VOTED;
		$poll_list_msg_thankyou				= LOCALE_POLL_LIST_MSG_THANKYOU;
		if (!empty($poll_identifier)){
			$sql = "select * from poll_list where poll_list_client = $this->client_identifier and poll_list_identifier=".$parameters["identifier"];
			$result = $this->call_command("DB_QUERY",array($sql));
			if($this->call_command("DB_NUM_ROWS",array($result))>0){
				while ($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
					$poll_label							= $r["poll_list_label"];
					$poll_question						= $r["poll_list_question"];
					for($index=1;$index<=10;$index++){
						$poll_answer["answer_$index"]	= $r["poll_list_answer$index"];
					}
					$poll_status						= $r["poll_list_status"];
					$poll_list_msg_already_voted		= $this->check_parameters($r, "poll_list_msg_already_voted", LOCALE_POLL_LIST_MSG_ALREADY_VOTED);
					$poll_list_msg_thankyou				= $this->check_parameters($r, "poll_list_msg_thankyou", LOCALE_POLL_LIST_MSG_THANKYOU);
				}
				$this->call_command("DB_FREE",array($result));
			}
			$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
				Array(
					"module"		=> $this->webContainer,
					"identifier"	=> $poll_identifier
				)
			);
			/*
			$sql = "select * from poll_menu where pm_poll=$poll_identifier and pm_client=$this->client_identifier";
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			$result = $this->call_command("DB_QUERY",array($sql));
			if($this->call_command("DB_NUM_ROWS",array($result))>0){
				while ($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
					$menu_locations[count($menu_locations)] = $r["pm_menu"];
				}
			}
			*/
		}
		$menu_locations =$this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_locations));
		
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		
		$out .= "<page_options><header>".LOCALE_POLL_MANAGER."</header>";
		$out .= "	<button command=\"POLLS_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"/>";
		$out .= "</page_options>";
		$out .= "<form name=\"poll_manager\" label=\"".LOCALE_POLL_TITLE_LABEL."\" method=\"post\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"POLLS_SAVE\"/>";
		$out .= "<input type=\"hidden\" name=\"poll_identifier\" value=\"$poll_identifier\"/>
		<page_sections>";
		$out .= "	<section label=\"".LOCALE_SETTINGS."\"";
		if ($display_tab=="content"){
			$out .= " selected='true'";
		}
		$out .= ">";
		
		$out .= "<input type=\"text\" label=\"".LOCALE_POLL_QUESTION."\" size=\"255\" name=\"poll_label\"><![CDATA[$poll_label]]></input>";
		$out .= "<select label=\"".LOCALE_STATUS."\" name=\"poll_status\">";
		$out .= "<option value=\"0\">".STATUS_NOT_LIVE."</option>";
		$out .= "<option value=\"1\"";
		if ($poll_status==1){
			$out .=" selected=\"true\"";
		}
		$out .= ">".STATUS_LIVE."</option>";
		$out .= "</select>";
		$out .= "<select label=\"".LOCALE_POLL_WHERE_ON_SITE_TO_PUBLISH."\" name=\"poll_location\">$menu_locations</select>";

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
	//	$out .= "<input type=\"text\" label=\"".LOCALE_POLL_QUESTION."\" size=\"255\" name=\"poll_question\"><![CDATA[$poll_question]]></input>";
		$out .= "<text><![CDATA[".LOCALE_POLL_ANSWER_STATEMENT."]]></text>";
		for($index=1;$index<=10;$index++){
			$out .= "<input type=\"text\" label=\"".LOCALE_ANSWER." $index\" size=\"255\" name=\"poll_answer$index\"><![CDATA[".$poll_answer["answer_$index"]."]]></input>";
		}
		$out_result = "";
		for($index=1;$index<=10;$index++){
			if ($this->check_parameters($answer_counter,"answer_$index",-1)!=-1){
				$a = $answer_counter["answer_$index"];
				$out_result.="<tr><th align='left'>".$poll_answer["answer_$index"]."</th><td><img src='/libertas_images/general/graphs/bar_left.gif' width='5' height='20' alt='".(round(($a/$total)*100,2))."%'/>";
				$out_result.="<img src='/libertas_images/general/graphs/bar_middle.gif' width='".(round(($a/$total)*100,2)*2)."' height='20' alt='".(round(($a/$total)*100,2))."%'/>";
				$out_result.="<img src='/libertas_images/general/graphs/bar_right.gif' width='5' height='20' alt='".(round(($a/$total)*100,2))."%'/> (".$answer_counter["answer_$index"].") votes</td></tr>";
			}
		}
		if ($out_result!=""){
		
			$out .= "	</section><section label=\"".LOCALE_RESULTS."\"";
			if ($display_tab=="content"){
				$out .= " selected='true'";
			}
			$out .= ">";
			$out .= "<text><![CDATA[<table>$out_result</table>]]></text>";
		}
		$out .= "	</section><section label=\"".LOCALE_MSGS."\"";
		if ($display_tab=="content"){
			$out .= " selected='true'";
		}
		$out .= ">";
		$out .="<input type='text' name='poll_list_msg_thankyou' size='255' label='".LOCALE_CONFIRM_MESSAGE."'><![CDATA[$poll_list_msg_thankyou]]></input>";
		$out .="<input type='text' name='poll_list_msg_already_voted' size='255' label='".LOCALE_ALL_READY_VOTED_MESSAGE."'><![CDATA[$poll_list_msg_already_voted]]></input>";

		$out .= "	</section><section label=\"".LOCALE_PREVIEW."\" onclick='preview_poll' ";
		if ($display_tab=="poll_preview"){
			$out .= " selected='true'";
		}
		$out .= "><div id='pollPreview'></div>";
		$out .= "</section></page_sections>";
		
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>
		
		</form>";
		$out .="</module>";
		return $out;
	}
	
	/**
	* polls_save function
	-----------------------
	- This function will generate the proper table structure in the choosen database
	- format.
	*/
	function polls_save($parameters){
		$debug = $this->debugit(false,$parameters);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_save",__LINE__,"[]"));
		}
		
		$ok=0;
		$poll_list_identifier			=	$this->check_parameters($parameters,"poll_identifier");
		$poll_list_status				=	$this->check_parameters($parameters,"poll_status");
		$poll_list_label				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_label")));
		$poll_list_question 			=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_question")));
		$poll_list_answer1				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_answer1")));
		$poll_list_answer2				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_answer2")));
		$poll_list_answer3				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_answer3")));
		$poll_list_answer4				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_answer4")));
		$poll_list_answer5				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_answer5")));
		$poll_list_answer6				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_answer6")));
		$poll_list_answer7				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_answer7")));
		$poll_list_answer8				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_answer8")));
		$poll_list_answer9				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_answer9")));
		$poll_list_answer10				=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_answer10")));
		$poll_list_msg_already_voted	=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_list_msg_already_voted")));
		$poll_list_msg_thankyou			=	trim($this->strip_tidy($this->check_parameters($parameters,"poll_list_msg_thankyou")));
		$poll_list_location				=	$this->check_parameters($parameters,"poll_location",-1);
		$user_identifier = $this->call_command("SESSION_GET",Array("SESSION_USER_IDENTIFIER"));
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		if (empty($poll_list_identifier)){
			/**
			* Add a new poll to the system
			*/

			$fields = "poll_list_question, poll_list_label, poll_list_answer1, poll_list_answer2, poll_list_answer3, poll_list_answer4, poll_list_answer5, poll_list_answer6, poll_list_answer7, poll_list_answer8, poll_list_answer9, poll_list_answer10, poll_list_date_created, poll_list_created_by, poll_list_client, poll_list_status, poll_list_msg_already_voted, poll_list_msg_thankyou";
			$values = "'$poll_list_question','$poll_list_label', '$poll_list_answer1', '$poll_list_answer2', '$poll_list_answer3', '$poll_list_answer4', '$poll_list_answer5', '$poll_list_answer6', '$poll_list_answer7', '$poll_list_answer8', '$poll_list_answer9', '$poll_list_answer10', '$now', $user_identifier, $this->client_identifier, $poll_list_status , '$poll_list_msg_already_voted', '$poll_list_msg_thankyou'";
			$sql = "insert into poll_list ($fields) values ($values)";
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			
			$this->call_command("DB_QUERY",array($sql));
			$sql = "select * from poll_list where poll_list_date_created='$now' and poll_list_created_by=$user_identifier and poll_list_client=$this->client_identifier and poll_list_status=$poll_list_status";
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$poll_list_identifier = $r["poll_list_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
		} else {
			/**
			* update an existing poll in the system
			*/
			$fields = "poll_list_question='$poll_list_question', poll_list_label='$poll_list_label', poll_list_answer1='$poll_list_answer1',
			poll_list_answer2='$poll_list_answer2',	poll_list_answer3='$poll_list_answer3',	poll_list_answer4='$poll_list_answer4',	poll_list_answer5='$poll_list_answer5',	poll_list_answer6='$poll_list_answer6',	poll_list_answer7='$poll_list_answer7', poll_list_answer8='$poll_list_answer8', poll_list_answer9='$poll_list_answer9', poll_list_answer10='$poll_list_answer10', poll_list_status=$poll_list_status, poll_list_msg_already_voted='$poll_list_msg_already_voted', poll_list_msg_thankyou ='$poll_list_msg_thankyou' ";
			$sql = "update poll_list set $fields where poll_list_client= $this->client_identifier and poll_list_identifier=$poll_list_identifier";
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			$this->call_command("DB_QUERY",array($sql));
		}
		/*
		Save menu locations
		*/
		$menu_locations = Array();
		$menu_locations[0] =$poll_list_location;
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->webContainer,
				"identifier"	=> $poll_list_identifier,
				"all_locations"	=> 0
			)
		);
		$sql ="insert into display_data (display_menu, display_client, display_command) values ($poll_list_location, $this->client_identifier, 'POLLS_DISPLAY')";
		$this->call_command("DB_QUERY",array($sql));
		$this->tidyup_display_commands($parameters);
		if ($debug) $this->exitprogram();
	}
	/**
	* polls_remove function
	-----------------------
	- This function will generate the proper table structure in the choosen database
	- format.
	*/
	function polls_remove($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_remove",__LINE__,""));
		}
		
		$out  = "<module name=\"".$this->module_name."\" display=\"remove_form\">";
		$out .= " <form label=\"".POLL_REMOVE_LABEL."\" name=\"".$this->module_name."_remove_form\" method=\"post\">";
		$out .= "  <input type=\"hidden\" name=\"command\" value=\"POLLS_REMOVE_CONFIRM\"/>";
		$out .= "  <input type=\"hidden\" name=\"poll_identifier\" value=\"".$parameters["identifier"]."\"/>";
		$out .= "  <text><![CDATA[".POLL_REMOVE_CONFIRMATION_LABEL."]]></text>";
		$out .= "  <input type=\"button\" iconify=\"NO\" value=\"".ENTRY_NO."\" command=\"POLLS_LIST\"/>";
		$out .= "  <input type=\"submit\" iconify=\"YES\"  value=\"".ENTRY_YES."\" />";
		$out .= " </form>";
		$out .= "</module>";
		
		return $out;
	}
	/***
	| polls_remove_confirm function |
	+-------------------------------+
	| This function will generate the proper table structure in the choosen database
	| format.
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
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
	
	/***
	| polls_display function	    |
	+-------------------------------+
	| This function will generate the proper table structure in the choosen database
	| format.
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function polls_display($parameters){
		$debug = $this->debugit(false,$parameters);
		$out="";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_display",__LINE__,""));
		}
		$sql = "Select * from poll_list 
				inner join menu_to_object on 
					(mto_client = poll_list_client and mto_object=poll_list_identifier and mto_module='$this->webContainer' and 
						(mto_menu=".$parameters["current_menu_location"]." 
						) and mto_publish=1
					) 
				where poll_list_client=$this->client_identifier and poll_list_status=1 and mto_menu=".$parameters["current_menu_location"];
		if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		if($result = $this->call_command("DB_QUERY",array($sql))) {
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$locations="";
				$show_results=0;
				$answer_counter = array();
				$identifier 				= $r["poll_list_identifier"];
				$poll_list_label 			= $r["poll_list_label"];
				$show_results 				= $r["poll_list_results_available"];
				$security_options 			= $r["poll_list_security_option"];
				$poll_question	 			= $r["poll_list_question"];
				$results_after_num 			= $r["poll_list_number_of_votes"];
				$poll_list_msg_thankyou		= $r["poll_list_msg_thankyou"];
				$poll_list_msg_already_voted= $r["poll_list_msg_already_voted"];
				$Query_String_data 			= "select poll_info_answer, count(poll_info_answer) as total from poll_info where poll_info_identifier = $identifier group by poll_info_answer";
				
				if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$Query_String_data</p>\n";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$Query_String_data"));
				}
				$total=0;
				if ($Data_Result = $this->call_command("DB_QUERY",array($Query_String_data))){
					while ($row = $this->call_command("DB_FETCH_ARRAY",array($Data_Result))){
						$answer_counter["answer_".$row["poll_info_answer"]] = $row["total"];
						$total += $row["total"];
					}
				}
				$answers  = "";
				$answers .= "<radio name=\"poll_answer\" type='vertical'>";
				for($index=1;$index<=10;$index++){
					if (!empty($r["poll_list_answer$index"])){
						if (($total>0) && (!empty($answer_counter["answer_$index"]))){
							$value = round(($answer_counter["answer_$index"]/$total)*100,2);
						}else{
							$value=0;
						}
						if ($show_results==1 && ($total > $this->list_num_votes[$results_after_num])){
							$a = $this->check_parameters($answer_counter,"answer_$index",0);
							$answers .= "	<option value=\"$index\"><![CDATA[".$r["poll_list_answer$index"]."]]></option>";
						} else {
							$answers .= "	<option value=\"$index\"><![CDATA[".$r["poll_list_answer$index"]."]]></option>";
						}
					}
				}
				$answers .= "</radio>";
				
				$out .="<module name=\"".$this->module_name."\" display=\"form\">";
				$out .= "<form name=\"poll_submission_form_$identifier\" method=\"post\">";
				$out .= "<label><![CDATA[Poll]]></label>";
				$out .= "<text class='label'><![CDATA[$poll_list_label]]></text>";				
				$out .= "<input type=\"hidden\" name=\"command\" value=\"POLLS_VOTE\"/>";
				$out .= "<input type=\"hidden\" name=\"poll_identifier\" value=\"$identifier\"/>";
				$out .= $answers;
				$list 	= $this->check_parameters($_SESSION,"voted_on",Array());
				$found = 0;
				for($index=0;$index<count($list);$index++){
					if($list[$index] == $identifier){
						$found=1;
					}
				}
				if($found){
					$out .= "<text><![CDATA[$poll_list_msg_thankyou]]></text>";
				}
				if($this->check_parameters($parameters,"poll_vote")==2 && $this->check_parameters($parameters,"poll_id")==$identifier){
					$out .= "<text><![CDATA[You have not selected an answer]]></text>";
				}
				$vote		= 0;
				$already	= 0;
				if($this->check_parameters($parameters,"poll_vote")==0 && $this->check_parameters($parameters,"poll_id")==$identifier){
					$already= 1;
				}
				if ($security_options==3 && $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1)>0){
					$vote 	= 1;
				}
				if ($security_options==2){
					$list 	= $this->check_parameters($_SESSION,"voted_on",Array());
					$vote	= 1;
					for($index=0;$index<count($list);$index++){
						if($list[$index] == $identifier){
							$vote		=	0;
							$already	=	1;
						}
					}
				}
				if ($security_options==0){
					$vote = 1;
				}
				if ($vote == 1){
					$out .= "<input type=\"submit\" iconify=\"VOTE\" value=\"Vote Now\"/>";
				} else {
					if ( $already == 1 ){
						$out .= "<text><![CDATA[$poll_list_msg_already_voted]]></text>";
					}
				}
				$out .= "</form>";
				$out .="</module>";
			}
		}
		return $out;
	}
	/***
	| polls_vote function	        |
	+-------------------------------+
	| This function will generate the proper table structure in the choosen database
	| format.
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function polls_vote($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_vote",__LINE__,""));
		}
		$ok=0;
		if ($this->check_parameters($parameters,"poll_answer","__NOT_FOUND__")!="__NOT_FOUND__"){
			$sql = "Select poll_info.*, poll_list.poll_list_security_option from poll_info 
						inner join poll_list on poll_list_identifier = poll_info_identifier and poll_list_client= poll_info_client
					where poll_info_client=$this->client_identifier and poll_info_session_id='".session_id()."' and poll_info_ip_address='".$_SERVER["REMOTE_ADDR"]."' and  poll_info_identifier=".$parameters["poll_identifier"];
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			if($result = $this->call_command("DB_QUERY",array($sql))) {
				$num_rows= $this->call_command("DB_NUM_ROWS",array($result));
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
				
				if ($num_rows==0 || $r["poll_list_security_option"]==0){
					/*
					+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					| add the vote to the database
					+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					$sql = "insert into poll_info (poll_info_answer, poll_info_client, poll_info_session_id, poll_info_ip_address, poll_info_identifier) values (".$parameters["poll_answer"].",$this->client_identifier,'".session_id()."', '".$_SERVER["REMOTE_ADDR"]."', ".$parameters["poll_identifier"].");";
					
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
					}
					$this->call_command("DB_QUERY",array($sql));
					$ok=1;
					if(!is_array($this->check_parameters($_SESSION,"voted_on"))){
						$_SESSION["voted_on"]=Array();
					}
					$_SESSION["voted_on"][count($_SESSION["voted_on"])]=$parameters["poll_identifier"];
					
				}
			}
		} else {
			$ok =2;
		}
		return $ok;
	}
	function tidyup_display_commands($parameters){
		$debug = $this->debugit(false, $parameters);
		$sql ="select DISTINCT pm_menu as m_id from poll_menu where pm_client=$this->client_identifier";
		$sql = "select distinct mto_menu as m_id from menu_to_object where mto_client = $this->client_identifier and mto_module='POLLS_'";
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$result  = $this->call_command("DB_QUERY",Array($sql));
   		$sql = "delete from display_data where display_client=$this->client_identifier and display_command='POLLS_DISPLAY'";
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$this->call_command("DB_QUERY",Array($sql));
   		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if ($r["m_id"]>0){
				$sql = "insert into display_data (display_client, display_command, display_menu) values ($this->client_identifier, 'POLLS_DISPLAY', ".$r["m_id"].")";
				if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
				$this->call_command("DB_QUERY",Array($sql));
			}
       	}
		$this->call_command("DB_FREE",Array($result));
	}

}
?>