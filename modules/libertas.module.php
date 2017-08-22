<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.users.php
* @date 09 Oct 2002
*/
/**
* Base module all other modules inherit form here
*/
class module {
	/**
	*  Class Variables
	*/
	var $module_type				= "__SYSTEM__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name				= "";
	var $module_name_label			= "";
	var $module_displays_web_output	= 0; // set to one for the engine to
	var $module_label				= "Base module please define the variable module_label in your module.";
	var $module_author				= "Adrian Sweeney";
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_bug_email_address	= "issues@libertas-solutions.com";
	var $module_admin				= "0";
	var $module_modify	 			= '$Date: 2005/03/18 14:54:20 $';
	var $module_version 			= '$Revision: 1.60 $';
	var $module_creation 			= "09/09/2002";
	var $webContainer				= "";
	var $page_size					= 10;
	var $locale_starter				= "";
	var $editor_configurations 		= Array();
	var $module_type_admin_access	= 0;
	var $debug_script				= "";
	var $error_messages				= "";
	var $parent						= null;
	var $module_command				= ""; 		// all commands specifically for this module will start with this token
	var $modules 					= array(); 				// A list of all the modules in the system.
	var $works_with_modules 		= array();

	var $client_identifier			= -1;
	var $domain_identifier			= -1;
	var $can_log_search				= false;
	var $module_debug = 0;
	var $translation_language_codex = "EN";
	var $translation_language 		= "English";

	var $module_admin_options 		= array();
	var $module_admin_user_access 	= array();
	var $module_display_options 	= array();
	/**
	*  
	*/
	var $preferences= Array();
	var $module_channels			= array();
	var $available_forms			= "";
	var $module_constants			= Array(
		"__REMOVE_ALL_HISTORY__"		=> -2,
		"__REMOVE__"					=> -1,
		"__REMOVE_REVERT__"				=> -3,
		"__NO_CHANGE__"					=> 0,
		"__EMAIL_PAGE_AUTHOR__"			=> "__EMAIL_PAGE_AUTHOR__",
		"__EMAIL_PAGE_APPROVER__"		=> "__EMAIL_PAGE_APPROVER__",
		"__EMAIL_PAGE_PUBLISHER__"		=> "__EMAIL_PAGE_PUBLISHER__",
		"__EMAIL_WEB_USER_PAGE__"		=> "__EMAIL_WEB_USER_PAGE__",
		"__EMAIL_GUESTBOOK_APPROVER__"	=> "__EMAIL_GUESTBOOK_APPROVER__",
		"__EMAIL_WEB_USER_GUESTBOOK__"	=> "__EMAIL_WEB_USER_GUESTBOOK__",
		"__EMAIL_FORUM_APPROVER__"		=> "__EMAIL_FORUM_APPROVER__",
		"__EMAIL_WEB_USER_FORUM__"		=> "__EMAIL_WEB_USER_FORUM__",
		"__EMAIL_INFODIR_APPROVER__"	=> "__EMAIL_INFODIR_APPROVER__",
		"__EMAIL_WEB_USER_INFODIR__"	=> "__EMAIL_WEB_USER_INFODIR__",
		"__EMAIL_COMMENTS_APPROVER__"	=> "__EMAIL_COMMENTS_APPROVER__",
		"__EMAIL_WEB_USER_COMMENTS__"	=> "__EMAIL_WEB_USER_COMMENTS__",
		"__EMAIL_WEB_USER_COMMENTS__"	=> "__EMAIL_WEB_USER_COMMENTS__",
		"__EMAIL_SHOP_ORDER_PROCESSOR__"=> "__EMAIL_SHOP_ORDER_PROCESSOR__"
	);

	
	/**
	*  Access Variables
	*/
	var $list_access				= 0;
	var $module_admin_access		= 0;
	var $running_as_installer		= 0;
	
	/**
	*  Class Construtor
	*
	*  This function will create all connections that are required by the module
	*/
	function module(&$parent , $parameters=Array()){
		$this->parent = &$parent;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"initialise()",__LINE__,"[]"));
		}
		$this->module_debug= false;
		if ($this->check_parameters($parameters,"initialise",true)){
			/**
			*  if the engine is creating the tables then do not initialise the module
			*/
			$this->initialise();
		}
		if($this->module_name_label == ""){
			$this->module_name_label = $this->module_name;
		}
		if($this->webContainer == ""){
			$this->webContainer = $this->module_command;
		}
		if ($this->client_identifier==-1){
			$this->client_identifier=$this->parent->client_identifier;
		}
	}
	/**
	* initialise()
	- this funciton initialises the module over write this function in your module to
	- specify functionality that is to be executed.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"initialise",__LINE__,""));
		}
		
		return 0;
	}
	/**
	* get_module_name()
	- this funciton returns the name of the module
	*/
	function get_module_name(){
		return $this->module_name_label;
//		return $this->module_name;
	}
	
	/**
	* get_module_version()
	- this funciton returns the version of the module
	*/
	function get_module_version(){
		if (strpos($this->module_version,"Revision:")===false){
			return $this->module_version;
		} else {
			$l = split(" ",$this->module_version);
			return $l[1];
		}
	}
	/**
	* get_module_modify()
	- this funciton returns the version of the module
	*/
	function get_module_modify(){
		if (strpos($this->module_modify,"Date:")===false){
			return $this->module_modify;
		} else {
			$l = split(" ",$this->module_modify);
			return $l[1];
		}
	}

	/**
	* @returns the author of the module
	*/
	function get_module_author(){
		return $this->module_author;
	}
	/**
	* @returns the creation time of the module
	*/
	function get_module_creation(){
		return $this->module_creation;
	}
	/**
	* @returns the command starter for this module
	*/
	function get_module_command(){
		return $this->module_command;
	}
	/**
	* this function is used to execute any commands for this module
	*/
	function command($user_command,$parameter_list=array()){return null;}
	/**
	* this function is used to specify the display options for this module in a specific location
	*/
	function display_options($user_command,$parameter_list=array()){return null;}
	/**
	* this function is used to call the engines command module which will call another module
    *
    * you should never use this function to call a function in the module you are in as you have
    * complete access to this module therefore you can call the command function directly
	*/
	function call_command($user_command,$parameter_list=array()){
		return $this->parent->command($user_command,$parameter_list);
	}

	/**
	* this function is used to supply error messages to the debugging screen
	*/
	function error_message($file,$line,$msg){
		$outtext = "<error file=\"".$file."\" line=\"".$line."\"><![CDATA[".$msg."]]></error>\n";
		$this->error_messages.=$outtext;
		return 1;
	}
	
	/**
	* create_table()
	- this function is used to generate any tables that this module requires.
	- This module must return 1 for successfully creatingn tables and 0 for failure.
	*/
	function create_table(){
		return "";
	}
	/**
	* retrieve the admin menu
	*/
	function get_admin_menu_option(){
		if ($this->module_admin==1){
			$out="";
			if ($this->module_grouping==""){
				$group = "undefined group - [$this->module_name]";
			} else {
				eval("\$group = $this->module_grouping;");
			}
			if ($this->module_label==""){
				$label = "undefined label - [$this->module_name]";
			} else {
				eval("\$label = $this->module_label;");
			}
			$val = $this->module_admin_options_fn(2);
			
			for($i=0;$i<count($val);$i++){
				$path = $this->check_parameters($val[$i],3,$this->get_constant($this->module_grouping)."/".$this->get_constant($this->module_label));
				$l = count($val[$i]);
				$val[$i][1] = $this->get_constant($val[$i][1]);
				if($l==2){
					$val[$i][2]="";
					$val[$i][3]="$path";
					$val[$i][4]=$this->module_name;
					$val[$i][5]=$this->module_command."ALL";
				}
				if($l==3){
					$val[$i][3]="$path";
					$val[$i][4]=$this->module_name;
					$val[$i][5]=$this->module_command."ALL";
				}
				if($l==4){
					$val[$i][4]=$this->module_name;
					$val[$i][5]=$this->module_command."ALL";
				}
				if($l==5){
					$val[$i][5]=$this->module_command."ALL";
				}
				if ("__NOT_FOUND__" == $this->check_parameters($this->parent->menu_structure,$path,"__NOT_FOUND__")){
//					print "<li>$this->module_name $path</li>";
					$this->parent->menu_structure[$path] = Array();
				}
				$this->parent->menu_structure[$path][count($this->parent->menu_structure[$path])] = $val[$i];
			}
			return "";
			if (($this->parent->server[LICENCE_TYPE]==ECMS)){
				if($this->module_name=="user_access"){
					$out  = "<mod label=\"Management reports\" name=\"page\" grouping=\"".$group."\" ignore=\"PAGE_ALL\" management=\"1\" version=\"na\">";
					$out .= "	<options module=\"Management reports\" tag=\"PAGE_\">";
					$out .= "		<option value=\"PAGE_REPORT_STATUS\">Workflow status report</option>";
					$out .= "		<option value=\"PAGE_REPORT_SITE_CONTENT\">Site last update report</option>";
					$out .= "	</options>";
					$out .= "</mod>\n";
				}
			}
			if ($val!=""){
				return "\t\t\t<mod label=\"".$label."\" name=\"".$this->module_name."\" grouping=\"".$group."\" ignore=\"".$this->module_command."ALL\" management=\"".$this->module_admin."\" version=\"".$this->get_module_version()."\">".$val."</mod>\n$out";
			} else {
				return "";
			}
		}else{
			return "";
		}
	}
	/**
	* this function is used to retrieve a specific key from the parameter list
	*/
	function get_parameter($params,$key,$default=""){
		if (!empty($params[$key])){
			return $params[$key];
		}else{
			return $default;
		}
	}

	/**
	* this function is used to retrieve the list of functions available to the site administrator
	*/
	function module_admin_options_fn($restrict=1){
		if($restrict==2){
			return $this->module_admin_options;
		}
		$options_length = count($this->module_admin_options);
		$grp_info = $_SESSION["SESSION_GROUP"];
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		$ALL=1;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			if  ($access_length>0){
				for($access_index=0;$access_index<$access_length;$access_index++){
					if (($access[$access_index]==$this->module_command."ALL") || ($access[$access_index]=="ALL")){
						$ALL=1;
					}
					if(strpos($access_list,"[".$access[$access_index]."]")===false){
						$access_list .= "[".$access[$access_index]."]";
						$access_array[count($access_array)] = $access[$access_index];
					}
				}
			}
		}
		$access_length = count($access_array);
		if (($options_length>0) && ($access_length>0)){
			/**
			* if there are any options then return them
			*/
			$out.="<options module=\"".$this->get_constant($this->module_label)."\" tag=\"".$this->module_command."\">";
			$found=0;
			for($index=0;$index<$options_length;$index++){
				if($ALL==0){
					$found=0;
					for($access_index=0;$access_index<$access_length;$access_index++){
						$list_grps =split("\|",$this->check_parameters($this->module_admin_options[$index],2,""));
						$list_grps_length = count($list_grps);
						for($list_index=0;$list_index<$list_grps_length;$list_index++){
							if ($access_array[$access_index] == $list_grps[$list_index]){
								$found=1;
							}
						}
					}
				}
				if (($restrict==0)||($found)||($ALL)){
					$option_label = $this->get_constant($this->module_admin_options[$index][1]);
					$out.="<option value=\"".$this->module_admin_options[$index][0]."\" grouping=\"".$this->check_parameters($this->module_admin_options[$index],2)."\">".$this->get_constant($option_label)."</option>";
				}
			}	
			$out.="</options>";
		}
		return $out;
	}
	/**
	*
	*/
	function module_admin_access_options($restrict=1){
		$out="";
		$options_length = count($this->module_admin_user_access);
		$grp_info = $_SESSION["SESSION_GROUP"];
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		for($i=0;$i < $max_grps; $i++){
			$access 		= $grp_info[$i]["ACCESS"];
			$access_length	= count($access);
			$out			= "";
			if  ($access_length>0){
				for($access_index=0;$access_index<$access_length;$access_index++){
					if (($access[$access_index]==$this->module_command."ALL") || ($access[$access_index]=="ALL")){
						$ALL=1;
					}
					if(strpos($access_list,"[".$access[$access_index]."]")===false){
						$access_list .= "[".$access[$access_index]."]";
						$access_array[count($access_array)] = $access[$access_index];
					}
				}
			}
		}
		$access_length = count($access_array);
		if  ($access_length>0){
			if ($options_length>0){
				/**
				* if there are any options then return them
				*/ 
				$out.="<options module=\"".$this->get_constant($this->module_label)."\" tag=\"".$this->module_command."\">";
				for($index=0;$index<$options_length;$index++){
					$found=0;
					for($access_index=0;$access_index<$access_length;$access_index++){
						if (
								(
									($ALL==1) || 
									($access_array[$access_index]==$this->module_admin_user_access[$index][0])
								)
									&&
								($access_array[$access_index] != $this->module_command."ALL")
							){
							$found=1;
						}
					}
					if (($restrict==0)||($found)){
						$out.="<option value=\"".$this->module_admin_user_access[$index][0]."\">".$this->get_constant($this->module_admin_user_access[$index][1])."</option>";
					}
				}
				$out.="</options>";
			}
		}
		return $out;
	}
	
	function generate_list($parameters){
		if ($this->module_debug){
//			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"generate_list()",__LINE__,$parameters));
		}
		$as						= $this->check_parameters($parameters,"as","list");
		$number_of_records		= $this->check_parameters($parameters,"NUMBER_OF_ROWS");
		$finish 				= $this->check_parameters($parameters,"FINISH");
		$start 					= $this->check_parameters($parameters,"START");
		$num_pages				= $this->check_parameters($parameters,"NUMBER_OF_PAGES");
		$current_page 			= $this->check_parameters($parameters,"CURRENT_PAGE");
		$start_page				= $this->check_parameters($parameters,"START_PAGE");
		$end_page				= $this->check_parameters($parameters,"END_PAGE");
		$filter 				= $this->check_parameters($parameters,"FILTER");
		$GROUPING_IDENTIFIER	= $this->check_parameters($parameters,"GROUPING_IDENTIFIER");
		$menu_links 			= $this->check_parameters($parameters,"MENU_LINKS");
		$header		 			= $this->check_parameters($parameters,"HEADER");
		$top_text 	 			= $this->check_parameters($parameters,"TOP_TEXT");
		$searchfilter	 		= $this->check_parameters($parameters,"SEARCHFILTER");
		$label					= $this->check_parameters($parameters,"LABEL");
		$description			= $this->check_parameters($parameters,"DESCRIPTION");
		$onlyone				= $this->check_parameters($parameters,"ONLY_ONE");
		if (!empty($GROUPING_IDENTIFIER)){
			$grouping = "grouping=\"$GROUPING_IDENTIFIER\"";
		} else {
			$grouping="";
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"",__LINE__,"starting"));
		}
		$out  = "\n\t\t<module name=\"".$this->module_name."\" display=\"results\" $grouping>\n";
		if($onlyone!=""){
		 	$out .= "<onlyone>".$onlyone."</onlyone>";
		}
		if (strlen($label)>0){
			$out .= "\n\t\t\t<label><![CDATA[$label]]></label>\n";
		}
		if (strlen($top_text)>0){
			$out .= "\t\t\t<text><![CDATA[" . $top_text . "]]></text>";
		}
		
		if (strlen($filter)>0){
		$out .= "\t\t\t<filter>\n";
		$out .= $filter;
		$out .= "\n\t\t\t</filter>\n";
		}
		$out .= "\n\t\t\t<menulinks><![CDATA[$menu_links]]></menulinks>\n";
		
		$out .= "\n\t\t\t<page_options>";
		$out .= "<header><![CDATA[$header]]></header>";
		$out .= $this->get_button($parameters,"PAGE_BUTTONS");
		$out .= "</page_options>\n";
		if($as=="list"){
			$out .= "\t\t\t<data_list command=\"";
		} else {
			$out .= "\t\t\t<table_list command=\"";
		}
		$out .= $this->check_parameters($parameters,"PAGE_COMMAND");
		$out .= "\" number_of_records=\"$number_of_records\" start=\"$start\" finish=\"$finish\" current_page=\"$current_page\" number_of_pages=\"$num_pages\" page_size=\"".$this->page_size."\">\n";
		$out .= "<searchfilter><![CDATA[$searchfilter]]></searchfilter>";
		$out .= "\t\t\t\t<pages>\n";
		//print "[$start,$finish]";
		if ($current_page<6){
			$s = 1;
		} else {
			$s = $current_page - 5;
		}
		$e=$s+10;
		if($e>$num_pages){
			$e=$num_pages;
		}
//		print "[$s,$e, $start,$finish, $number_of_records, $current_page, $num_pages]";
		for($index=$s;$index<=$e;$index++){
			$out .= "<page>$index</page>\n";
		}
		$out .= "\t\t\t\t</pages>\n";
		
//		$out .= "\t\t\t\t<entry_options>\n";
//		$out .= $this->get_button($parameters,"ENTRY_BUTTONS");
//		$out .= "\t\t\t\t</entry_options>\n";
		$out .= $this->get_result_entries($parameters);
		if($as=="list"){
			$out .= "</data_list>";
		} else {
			$out .= "</table_list>";
		}
		$out .= "\n\t\t</module>\n";
		return $out;
	}
	/*************************************************************************************************************************
    * check date
	*
	* this function will extract the date parts that have been submitted and convert them to a single date string returning 
	* that string or an empty string if no date segments were found;
	* @param Array all parameters to hunt through
	* @param String the field index to look for
	* return String in a date format (YYYY-MM-DD HH:00:00)
    *************************************************************************************************************************/
	function check_date($parameters,$field, $default=""){
		$my_date 	 = $this->check_parameters($parameters, $field."_date_year")	. "-";
		$my_date 	.= $this->check_parameters($parameters, $field."_date_month")	. "-";
		$my_date 	.= $this->check_parameters($parameters, $field."_date_day") 	. " ";
		$hr = $this->check_parameters($parameters, $field."_date_hour","00");
		if($hr==""){
			$hr="00";
		}
		$my_date 	.= $hr	. ":";
		$my_date 	.= $this->check_parameters($parameters, $field."_date_minute","00")	. ":00";
		if (($my_date == "-- 0:00:00") || ($my_date == "-- 00:00:00")) {
			$my_date = $default;
		}
		return $my_date;
	}
	/*************************************************************************************************************************
    * check parameters
	* @param Array all an associative array of parameters to hunt through
	* @param String the field index to look for
	* @param Mixed the default value to return if index is not found (default is empty String)
	* return Mixed the value held at index $name
    *************************************************************************************************************************/
	function check_parameters($parameters,$name,$default=""){
		if (@isset($parameters[$name])){
			$value = $parameters[$name];
		} else {
			$value = $default;
		}
		return $value;
	}
	
	function generate_search($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"generate_list()",__LINE__,"[".$parameters."]"));
		}
		$number_of_records	= $this->check_parameters($parameters,"NUMBER_OF_ROWS");
		$finish 			= $this->check_parameters($parameters,"FINISH");
		$start 				= $this->check_parameters($parameters,"START");
		$num_pages			= $this->check_parameters($parameters,"NUMBER_OF_PAGES");
		$current_page 		= $this->check_parameters($parameters,"CURRENT_PAGE");
		$start_page			= $this->check_parameters($parameters,"START_PAGE");
		$end_page			= $this->check_parameters($parameters,"END_PAGE");
		$filter 			= $this->check_parameters($parameters,"FILTER");
		$GROUPING_IDENTIFIER = $this->check_parameters($parameters,"GROUPING_IDENTIFIER");
		$extra				= $this->check_parameters($parameters,"EXTRA");
		$out  = "\n\t\t<module name=\"".$this->module_name."\" display=\"search_results\">\n\t\t\t<filter>\n";
		$out .= $filter;
		$out .= "\n\t\t\t</filter>\n";
		$out .= "\n\t\t\t<search_keywords>\n";
		$words = split(" ",$this->check_parameters($parameters,"PAGE_SEARCH"));
		for($index=0,$len=count($words);$index<$len;$index++){
			$out .= "\n\t\t\t\t<search_keyword><![CDATA[".$words[$index]."]]></search_keyword>\n";
		}
		$out .= "\n\t\t\t</search_keywords>\n";
		$out .= "\n\t\t\t<page_options>";
		$out .= $this->get_button($parameters,"PAGE_BUTTONS");
		$out .= "</page_options>\n";
		$out .= "\t\t\t<data_list command=\"";
		$out .= $this->check_parameters($parameters,"PAGE_COMMAND");
		$out .= "\" page_size=\"".$this->page_size."\" number_of_records=\"$number_of_records\" start=\"$start\" finish=\"$finish\" current_page=\"$current_page\" number_of_pages=\"$num_pages\">\n";
		$out .= "\t\t\t\t<pages>\n";
		for($index=1;$index<=$num_pages;$index++){
			$out .= "<page>$index</page>\n";
		}
		$out .= "\t\t\t\t</pages>\n";
		$out .= "<results>";		
		$out .= $this->get_search_result_entries($parameters);
		$out .= "</results>\t\t\t</data_list>\n\t\t$extra</module>\n";
		
		return $out;
	}

	function get_button($parameters,$SECTION, $identifier=-1){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY", array($this->module_name, "get_button()", __LINE__, "$SECTION"));
		}
		$out="";
		if (!empty($parameters[$SECTION])){
			$list_length = count ($parameters[$SECTION]);
			for ($index=0; $index<$list_length;$index++){
				$iconify	= $this->check_parameters($parameters[$SECTION][$index],0);
				$cmd  		= $this->check_parameters($parameters[$SECTION][$index],1);
				$alt		= $this->check_parameters($parameters[$SECTION][$index],2);
				if (count($parameters[$SECTION][$index])>3){
					$params = $this->check_parameters($parameters[$SECTION][$index],3);
					$lockable = $this->check_parameters($parameters[$SECTION][$index],4);
				}else {
					$params = "";
					$lockable ="";
				}
				if ($identifier>-1 && $lockable!=""){
					$f = -1;
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- how many attributes are we to check
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array($iconify,$cmd,$alt,$params));
				}else{
					$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array($iconify,$cmd,$alt,$params));
				}
			}
		}
		return $out;
	}
	function get_attribute($name, $value, $show="YES", $link="NO", $alt = ""){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"get_attribute()",__LINE__,"[$name,$value]"));
		}
		if ($value==""){
			$value="[[nbsp]]";
		}
		$out = $this->call_command("XMLTAG_GENERATE_XML_ATTRIBUTE",Array($name, $value, $show, $link, $alt));
		return $out;
	}
	
	function get_result_entries($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"get_result_entries()",__LINE__,"[".$parameters."]"));
		}
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		$out="";
		$SECTION="RESULT_ENTRIES";
		if (!empty($parameters[$SECTION])){
			$list_length = count ($parameters[$SECTION]);
			for ($index=0; $index<$list_length;$index++){
				$out .="\t\t\t\t<entry ";
				$out .="identifier=\"".$parameters[$SECTION][$index]["identifier"]."\">\n";
				$att_len = count($parameters[$SECTION][$index]["attributes"]);
				for($att_index=0; $att_index<$att_len;$att_index++){
					$name	 = $this->check_parameters($parameters[$SECTION][$index]["attributes"][$att_index],0);
					$value   = $this->check_parameters($parameters[$SECTION][$index]["attributes"][$att_index],1);
					$show	 = $this->check_parameters($parameters[$SECTION][$index]["attributes"][$att_index],2,"YES");
					$link	 = $this->check_parameters($parameters[$SECTION][$index]["attributes"][$att_index],3,"NO");
					$alt	 = $this->check_parameters($parameters[$SECTION][$index]["attributes"][$att_index],4,"");
					$out 	.= $this->get_attribute($name, $value, $show, $link, $alt);

				}
				$out .= "\t\t\t\t\t<entry_options>\n";
				if (!empty($parameters[$SECTION][$index]["ENTRY_BUTTONS"])){
					$out .= $this->get_button($parameters[$SECTION][$index],"ENTRY_BUTTONS",$index);
				}else{
					$out .= $this->get_button($parameters,"ENTRY_BUTTONS",$index);
				}
				$out .= "\t\t\t\t\t</entry_options>\n";
				$out .= "\t\t\t\t</entry>\n";
			}
		}
		return $out;
	}

	function get_search_result_entries($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"get_result_entries()",__LINE__,"[".$parameters."]"));
		}
		$lang="en";
		$out="";
		$SECTION="RESULT_ENTRIES";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fextra = $this->check_parameters($parameters,"FILE_ATTACH");
		if (!empty($parameters[$SECTION])){
			$list_length = count ($parameters[$SECTION]);
			for ($index=0; $index<$list_length;$index++){
//				print $data_files."/".$this->module_name."_".$this->client_identifier."_".$lang."_".$parameters[$SECTION][$index]["identifier"].".xml<br>";
$file = $this->check_parameters($parameters,"FILE","__NOT_FOUND__");
$xid = $this->check_parameters($parameters,"XID","");
if ($file !="__NOT_FOUND__"){
	$fname = $data_files."/".$this->module_name."_".$file."_".$this->client_identifier."_".$xid.$lang."_".$parameters[$SECTION][$index]["identifier"]."$fextra.xml";
} else {
	$fname = $data_files."/".$this->module_name."_".$this->client_identifier."_".$xid.$lang."_".$parameters[$SECTION][$index]["identifier"]."$fextra.xml";
}
//print "<li>".$fname."</li> ";
				if(file_exists($fname)){
//print $fname." ";
					$fp = fopen($fname, 'r');
					$out .= fread($fp, filesize($fname));
					if ($this->check_parameters($parameters[$SECTION][$index],"rank")!=""){
						$out .= '<ranking identifier="'.$parameters[$SECTION][$index]["identifier"].'" rank="'.$parameters[$SECTION][$index]["rank"].'"/>';
					}
					fclose($fp);
				}else{
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"get_search_result_entries()",__LINE__,$data_files."/".$this->module_name."_".$this->client_identifier."_".$lang."_".$parameters[$SECTION][$index]["identifier"].".xml not found"));
					}
				}
			}
		}
		return $out;
	}
	/**
	* works_with()
	- this function returns true if this module works with the calling module
	*/
	function works_with($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"works_with()",__LINE__,"[".join($parameters,", ")."]"));
		}
		$ok=0;
		for($index=0,$length_of_array=count($this->works_with_modules);$index<$length_of_array;$index++){
			if ($this->works_with_modules[$index]==$parameters["WORK_WITH_THIS_MODULE"]){
				$ok=1;
			}
		}
		return $ok;
	}
	/**
	* validate()
	- this function returns a validated string
	*/
	function validate($str){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"validate()",__LINE__,"[$str]"));
		}
		$find = Array(
				"&amp;amp;",
				"?&amp;",
				"id=libertas_form",
				"&quot",
				"<IMG ",
				"src=\\\"http://".$this->parent->domain."/libertas_images",
				"src=\"http://".$this->parent->domain.$this->parent->base,
				"src=\"http://".$this->parent->domain.$this->parent->base."http://".$this->parent->domain.$this->parent->base,
				"src=\"http://".$this->parent->domain.$this->parent->base."admin/",
				"href=\"http://".$this->parent->domain.$this->parent->base."http://".$this->parent->domain.$this->parent->base,
				"href=\"http://".$this->parent->domain.$this->parent->base."admin/",
				"href=\"http://".$this->parent->domain.$this->parent->base."http://",
				"href=\"http://".$this->parent->domain.$this->parent->base,
				"href=\"http://".$this->parent->domain."/",
				"href=\"http://".$this->parent->domain."/http://",
				"href=\"https://".$this->parent->domain.$this->parent->base."admin/",
				"href=\"https://".$this->parent->domain.$this->parent->base."http://",
				"href=\"https://".$this->parent->domain.$this->parent->base,
				"href=\"https://".$this->parent->domain."/",
				"href=\"https://".$this->parent->domain."/http://",
				"href=\"https://".$this->parent->domain.$this->parent->base."https://",
				"href=\"https://".$this->parent->domain."/https://",
				"src=\\\"http://".$this->parent->domain.$this->parent->base,
				"src=\\\"http://".$this->parent->domain.$this->parent->base."http://".$this->parent->domain.$this->parent->base,
				"src=\\\"http://".$this->parent->domain.$this->parent->base."admin/",
				"href=\\\"http://".$this->parent->domain.$this->parent->base."http://".$this->parent->domain.$this->parent->base,
				"href=\\\"http://".$this->parent->domain.$this->parent->base."admin/",
				"href=\\\"http://".$this->parent->domain.$this->parent->base."http://",
				"href=\\\"http://".$this->parent->domain.$this->parent->base,
				"href=\\\"http://".$this->parent->domain."/",
				"href=\\\"http://".$this->parent->domain."/http://",
				"href=\\\"https://".$this->parent->domain.$this->parent->base."admin/",
				"href=\\\"https://".$this->parent->domain.$this->parent->base."http://",
				"href=\\\"https://".$this->parent->domain.$this->parent->base,
				"href=\\\"https://".$this->parent->domain."/",
				"href=\\\"https://".$this->parent->domain."/http://",
				"href=\\\"https://".$this->parent->domain.$this->parent->base."https://",
				"href=\\\"https://".$this->parent->domain."/https://",
//				"\"".$this->parent->base,
				"richtext_editor.php",
				"/".session_name().'='.session_id()."/",
				"\\''",
				"£",
				"\\'",
				"\\\"",
				"../",
				"'",
				"/libertas_images/editor/original/richtext_editor.html",
				"/libertas_images/editor/original/",
				"libertas_images/editor/original/richtext_editor.html",
				"libertas_images/editor/original/",
				"FILES_INFO&amp;identifier",
				"href=\"/",
				"src=\"/",
				"?".session_name().'='.session_id()."",
				"?&amp;".session_name().'='.session_id()."",
				"&amp;".session_name().'='.session_id()."",
				"&".session_name().'='.session_id()."",
				"".session_name().'='.session_id()."",
				"longdesc=\"admin/index.php?",
				"?&&",
				"?&",
				"?\"",
				"src=\"libertas_images",
				"href=\"\"",
				"href=\"?"
			);
		$replace = Array(
				"&amp;",
				"?",
				"id=\"libertas_form\"",
				"\"",
				"<img ",
				"src=\"/libertas_images",
				"src=\"",
				"src=\"",
				"src=\"",
				"href=\"",
				"href=\"",
				"href=\"http://",
				"href=\"",
				"href=\"",
				"href=\"http://",
				"href=\"",
				"href=\"https://",
				"href=\"",
				"href=\"",
				"href=\"https://",
				"href=\"https://",
				"href=\"https://",
				"src=\"",
				"src=\"",
				"src=\"",
				"href=\"",
				"href=\"",
				"href=\"http://",
				"href=\"",
				"href=\"",
				"href=\"http://",
				"href=\"",
				"href=\"https://",
				"href=\"",
				"href=\"",
				"href=\"https://",
				"href=\"https://",
				"href=\"https://",
//				"\"/",
				"",
				"",
				"''",
				"&#163;",
				"&#39;",
				"\"",
				"",
				"&#39;",
				"",
				"",
				"",
				"",
				"FILES_INFO&identifier",
				"href=\"",
				"src=\"",
				"?",
				"?",
				"",
				"",
				"",
				"longdesc=\"?",
				"?",
				"?",
				"\"",
				"src=\"/libertas_images",
				"href=\"index.php\"",
				"href=\"index.php?"
			);
		$fc = count($find);
		$rc = count($replace);
		if ($fc!=$rc){
			print "Error code #LS000006 - $fc find attributes versus $rc replace attributes in function validate ".__FILE__." line ".__LINE__."<br>";
		}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"String",__LINE__,"$str"));}
//		print_r($str);
		$new_str = str_replace($find, $replace, html_entity_decode(html_entity_decode($str)));
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"new_str",__LINE__,"$new_str"));}
		return $new_str;
	}	

	function validate_with_url($str){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"validate()",__LINE__,"[$str]"));
		}
		$new_string = str_replace(
			Array(
				"href=\"/",
				"src=\"/",
				"â€“",
				"\n",
				"\r",
				"<oNULL></oNULL>",
				"<o:p></o:p>",
				"&nbsp;",
				"class=MsoNormal",
				session_name()."=".session_id(),
				"'",
				"\\''",
				"\\\"",
				"’"
			), 
			Array(
				"href=\"",
				"src=\"",
				"&#39;",
				"",
				"",
				"",
				"",
				" ",
				"&#32;",
				"",
				"''",
				"''",
				"\"",
				"&rsquo;"
			),
			$str);
		/*
		$new_string = join(spliti(,$str),);
		$new_string = join(spliti(,$new_string),"-");
		$new_string = join(spliti(,$new_string),"");
		$new_string = join(spliti("\r",$new_string),"");
		*/
	
		return $new_string;
	}	
	
	/**
	* lock_record()
	- this function will marks record for this module as locked.
	*/
	function lock_record($table,$field,$identifier,$action="LOCK"){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"validate()",__LINE__,"[$table,$field,$identifier]"));
		}
		$user = $_SESSION["SESSION_USER_IDENTIFIER"];
		if ($action=="LOCK"){
			$sql = "update $table set ".$field."_doc_lock_to_user=$user where ".$field."_doc_lock_to_user=0 and ".$field."_client=$this->client_identifier and ".$field."_identifier=$identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql =  "select * from $table where ".$field."_doc_lock_to_user=$user and ".$field."_client=$this->client_identifier and ".$field."_identifier=$identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($result){
				return 1;
			}else{
				return 0;
			}
		} else {
			$sql = "update $table set ".$field."_doc_lock_to_user=0 where ".$field."_client=$this->client_identifier and ".$field."_identifier=$identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
		}
	}	
	
	/**
	* display_channels()
	- this function will marks record for this module as locked.
	*/
	
	function display_channels($parameters){
		$selected_channels = $this->check_parameters($parameters,0,array());
		$return_array = $this->check_parameters($parameters,1,0);
		$channel_count = count($selected_channels);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"validate()",__LINE__,"[".count($this->module_display_options)."]"));
		}
		if (count($this->module_display_options)>0){
			if ($return_array==0){
				$out  = "<options module=\"".$this->get_constant($this->module_label)."\">";
				for ($index=0,$max=count($this->module_display_options);$index<$max;$index++){
					$out .= "<option value='".$this->module_display_options[$index][0]."'";
					$found=0;
					for ($i=0;$i<$channel_count;$i++){
						if (($found==0) && ($selected_channels[$i]==$this->module_display_options[$index][0])){
							$out.=" selected='true' ";
							$found=1;
						}
					}
					$out .= ">".$this->get_constant($this->module_display_options[$index][1])."</option>";
				}
				$out .= "</options>";
				return $out;
			} else {
				return $this->module_display_options;
			}
		} else {
			return "";
		}
	}

	function getmicrotime(){ 
		list($usec, $sec) = explode(" ",microtime()); 
		return ((float)$usec + (float)$sec); 
	}

	function extract_array($qstr){
		$str="";
		$counter=0;
		foreach ($qstr as $key => $value){
			if($counter>0){
				$str .= "&amp;";
			}
			if (is_array($qstr[$key])){
				$str .= $this->extract_array($qstr[$key]);
			}else{
				$str .= $key."=".$value;
			}
			$counter++;
		}
		return $str;
	}

	function update_search_keys($words){
		$table = ($this->module_name=="presentation")? "page" : $this->module_name;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"update_search_keys",__LINE__, print_r($words,true)));
		}
		if ($this->can_log_search){
			$sql = "select * from ".$table."_search_keys where search_client=$this->client_identifier and search_keyword ='".$words."'";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$found=0;
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$found =1;
			}
			if ($found==1){
				$sql = "update ".$table."_search_keys set ".$table."_search_keys.search_counter = ".$table."_search_keys.search_counter+1 where search_client=$this->client_identifier and search_keyword = '".$words."';";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"sql",__LINE__,"[$sql]"));
				}
				$result = $this->call_command("DB_QUERY",array($sql));
			}else{
				$sql = "insert into ".$table."_search_keys (search_keyword, search_client, search_counter) values ('".$words."',$this->client_identifier,1);";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"sql",__LINE__,"[".$sql."]"));
				}
				$this->call_command("DB_QUERY",Array($sql));
			}
		}
	}
	
	function retrieve_search_keywords($parameters){
		$table = $this->module_name;
		$sql = "SELECT * from ".$table."_search_keys where search_client=$this->client_identifier ORDER BY search_counter DESC";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"retrieve_search_keywords",__LINE__,"$sql"));
		}
		$out="";
		$result 	= $this->call_command("DB_QUERY", Array($sql));
		$total 		= 0;
		eval("\$label = $this->module_label;");
		$out="<text><![CDATA[The following are the top 20 search words used in the $label of your site.]]></text>";
		if ($this->call_command("DB_NUM_ROWS", Array($result))>0){
		$c=0;
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result))) && $c<20){
				$c++;
				$total_hits  = $r["search_counter"];
				$keyword 	 = $r["search_keyword"];
				$out 		.= "<stat_entry>
						<attribute name=\"".LOCALE_SEARCH_PHRASE."\" show=\"YES\" link=\"NO\"><![CDATA[$keyword]]></attribute>
						<attribute name=\"".LOCALE_TOTAL."\" show=\"YES\" link=\"NO\"><![CDATA[$total_hits]]></attribute>
					</stat_entry>";
				$total		+= $total_hits;
			}
			
		}
		$out="<stat_results label=\"Top keywords used on site for $label\" total=\"$total\">$out</stat_results>";
		return $out;
	}

	function return_admin_channels(){
		return $this->module_channels;
	}

	function convert_amps($str=""){
//		print "\n<!-- $str : ";

//		$s = str_replace("& ","&amp; ",$str);
		$s = str_replace("&","&amp;",$str);
		$s = str_replace("&amp;amp; ","&amp; ",$s);
		$s = str_replace("&amp;amp;","&amp;",$s);
		$s = str_replace("&amp;#","&#",$s);
		$s = str_replace("&amp;#153;","&#8482;",$s);
		$s = str_replace("&amp;quot;","&quot;",$s);
		$s = str_replace("\"","&quot;",$s);
//	print " $s -->\n";
		return $s;
	}

    /**
    * split a string and replace with a pattern
    *
    * @param $str string to be searched
    * @param $pat string pattern to find
    * @param $rep string pattern to insert
    * @return string
    */
	function split_me($str,$pat,$rep){
		if (strpos("_".$str,$pat)>0){
			$out = str_replace ($pat,$rep,$str);
		} else {
			$out = $str;
		}
		return $out;
	}
    /**
    * turn a string into a safe uri string
    *
    * @param $str string to be converted
    * @param $id string to add to end of url if required incase of duplicate filename
    * @return string URL safe string
    */
	function make_uri($str, $id=""){
		$url_str="";
        $str = trim(preg_replace("/&[a-z]+;/me", "", strtolower(str_replace(Array("&amp;amp;amp;", "&amp;amp;", "&amp;","'"),Array("&", "&", "&",""),html_entity_decode(html_entity_decode($str))))));
		
		/* Added By Muhammad Imran to replace apostophe, before this, it was replacing apostophe sign with number 3*/
		$str = str_replace("&#39;"," ",$str);
		
        $l = strlen($str);
		$keep ='';
		for($i=0; $i<$l; $i++){
			$v = substr($str,$i,1);
			$c = ord($v);
			// only keep numbers and valid characters
			//		0 to 9 				A  to Z					a to z
			if (($c>=48 && $c<=56) || ($c>=65 && $c<=90) || ($c>=97 && $c<=122)){
				$keep .= $v;
				//drop
			} else {
				$keep .= "-";
			}
		}
		//remove double minus signs
		$url_str = $keep;
		while (!(strpos($keep,"--")===false)){
			$url_str = str_replace(Array("--"), Array("-"), $keep);
			$keep = $url_str;
		}

		//remove minus signs from start
		$url_str = $keep;
		while (substr($keep,0,1)=="-"){
			$url_str = substr($keep,1);
			$keep = $url_str;
		}
		if (strlen($url_str)>130){
			$url_str = substr($url_str,0,130)."-".$id;
		}
		if (strlen($url_str)==0){
			$url_str = "na-".$id;
		}
        return $url_str;
	}
	/*************************************************************************************************************************
    * convert number of bytes into readable file size
    *************************************************************************************************************************/
	function get_file_size($size_bytes){
		$one_k 	= 1024;
		$one_mb = ($one_k * $one_k);
		if (($size_bytes / $one_mb)>=1){
			$size_value = ($size_bytes / $one_mb);
			$size_des = "".round($size_value,1)." MB";								
		}else if (($size_bytes / $one_k)>=1){
			$size_value = $size_bytes / $one_k;
			$size_des = "".round($size_value)." kb";
		}else{
			$size_des = "".$size_bytes." bytes";
		}
		return $size_des;
	}

	function add_root_dir_to_paths($s){
		$new_string = str_replace(
		Array(
			"src=\"".$this->parent->base."http:",
			"href=\"".$this->parent->base."http:",
			"href=\"".$this->parent->base."mailto:",
			"href=\"".$this->parent->base."/",
			"src=\"".$this->parent->base."/"
		), 
		Array(
			"src=\"http:",
			"href=\"http:",
			"href=\"mailto:",
			"href=\"/",
			"src=\"/"
		),str_replace(
		Array(
			"href=\"",
			"src=\"",
			"href= \"",
			"src= \"",
			"href=\n\"",
			"src=\n\"",
			"href=\r\n\"",
			"src=\r\n\"",
			"href=\n\r\"",
			"src=\n\r\"",
			"href=\r\"",
			"src=\r\"",
			"href=\"".$this->parent->base.$this->parent->base,
			"src=\"".$this->parent->base.$this->parent->base,
			"src=\"".$this->parent->base."http:",
			"href=\"".$this->parent->base."http:",
			"href=\"".$this->parent->base."mailto:",
			"href=\"".$this->parent->base."/"
		), 
		Array(
			"href=\"".$this->parent->base,
			"src=\"".$this->parent->base,
			"href=\"".$this->parent->base,
			"src=\"".$this->parent->base,
			"href=\"".$this->parent->base,
			"src=\"".$this->parent->base,
			"href=\"".$this->parent->base,
			"src=\"".$this->parent->base,
			"href=\"".$this->parent->base,
			"src=\"".$this->parent->base,
			"href=\"".$this->parent->base,
			"src=\"".$this->parent->base,
			"href=\"".$this->parent->base,
			"src=\"".$this->parent->base,
			"src=\"http:",
			"href=\"http:",
			"href=\"mailto:",
			"href=\"/"
		),html_entity_decode($s)));
//		print "\n\n\n<!-- $s \n\n\n $new_string -->\n\n\n";
//		print "\n\n\n<!-- $new_string -->\n\n\n";
		return $new_string;
	}

	function generate_random_text($size=6){
		$letterstr = "ABCEFGHJKMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz2456789";
		$letters = preg_split('//', $letterstr, -1, PREG_SPLIT_NO_EMPTY);
		$out ="";
		$num_letters = count($letters)-1;
		for($index=0;$index<$size;$index++){
			$pos = rand(0,$num_letters);
			$out .= $letters[$pos];
		}
		return $out;
	}

	function check_locale_starter($str){
		if(is_array($str)){
			return $str;
		} else {
			$first = substr($str,0,1);
			$last = substr($str,-1);
			if (($first == "<") && ($last == ">")){
				return "";
			} else {
				return $str;
			}
		}
	}
	function xcheck_locale_starter($str){
		if ($str.""!="Array" && $str!=""){
			if (strlen($this->locale_starter)==0){
				$list= Array();
				$locale_dir		= $this->check_parameters($this->parent->site_directories,"LOCALE_FILES_DIR");
				$xsl_dir 		= $this->check_parameters($this->parent->site_directories,"XSL_THEMES_DIR");
				$file_name 		= $locale_dir."/".strtolower($this->translation_language_codex)."/general.xml";
				$fd = fopen ($file_name, "r") or die;
				$xml_page = "";
				if ($fd){
					$xml_page = fread ($fd, filesize ($file_name));
					fclose ($fd);
				}
				$this->call_command("XMLPARSER_LOAD_XML_STR",array($xml_page));
				$root 			= $this->parent->site_directories["ROOT"];
				$this->call_command("XMLPARSER_LOAD_XSL_FILE",array($xsl_dir."/stylesheets/themes/site_administration/extract_from_locale.xsl"));
				$output= $this->call_command("XMLPARSER_TRANSFORM");
				$output = str_replace("&gt;",">",$output);
				$output = substr($output,strpos($output,'$list'));
				if (strpos($output,"Error Code")===false)
					if (strlen($output)>0){
						eval($output);
						$this->locale_starter = $this->check_parameters($list,"LOCALE_DEFAULT_STRING");
					}
			}
			if (substr($str,(strlen($this->locale_starter) *-1))==$this->locale_starter){
				return "";
			}else{
				return $str;
			}
		} else {
			return $str;
		}
	}
	
	function load_locale_file($str){
		$list=Array();
		$locale_dir		= $this->check_parameters($this->parent->site_directories,"LOCALE_FILES_DIR");
		$xsl_dir 		= $this->check_parameters($this->parent->site_directories,"XSL_THEMES_DIR");
		$file_name 		= $locale_dir."/".strtolower($this->translation_language_codex)."/".$str.".xml";
//		print "[".$file_name."]";
//		exit();
		$root 			= $this->parent->site_directories["ROOT"];
		$this->call_command("XMLPARSER_LOAD_XML_FILE",array($file_name));
		$this->call_command("XMLPARSER_LOAD_XSL_FILE",array($xsl_dir."/stylesheets/themes/site_administration/extract_from_locale.xsl"));
		$output = $this->call_command("XMLPARSER_TRANSFORM");
		$output = substr($output,strlen('<?xml version="1.0" encoding="UTF-8"?>'));
		eval($output);
		return $list;
	}
	
	function load_translation(){
		$content="";
		$locale_dir		= $this->check_parameters($this->parent->site_directories,"LOCALE_FILES_DIR");
		if (file_exists($locale_dir."/".strtolower($this->translation_language_codex)."/locale.xml")){
			$content_array = file($locale_dir."/".strtolower($this->translation_language_codex)."/locale.xml");
			$content .= implode("", $content_array);
		}
	
		return $content;
		$max = count($this->parent->modules);
		/*
			load the general Locale files 
		*/
		if (file_exists($locale_dir."/".strtolower($this->translation_language_codex)."/general.xml")){
			$content_array = file($locale_dir."/".strtolower($this->translation_language_codex)."/general.xml");
			$content .= implode("", $content_array);
		}
		if (strtolower($this->parent->module_type)!="website"){
			if (file_exists($locale_dir."/".strtolower($this->translation_language_codex)."/management.xml")){
				$content_array = file($locale_dir."/".strtolower($this->translation_language_codex)."/management.xml");
				$content .= implode("", $content_array);
			}
		}
		/*
			Load the locale forthe modules that have been loaded
		*/
		//for ($i =0; $i<$max; $i++){
		foreach($this->parent->modules as $i => $moduleEntry){
			/*
				Load the Administration menu if in admin mode
			*/
//			print "<p>loaded: ".$this->parent->modules[$i]["tag"]." :: ".$this->parent->modules[$i]["loaded"]."</p>";

			if (strtolower($this->parent->module_type)=="website"){
				if ($this->parent->modules[$i]["loaded"]==1){
					if (file_exists($locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_module.xml")){
						$content_array = file($locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_module.xml");
						$content .= implode("", $content_array);
					}
					if (file_exists($locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_general.xml")){
						$content_array = file($locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_general.xml");
						$content .= implode("", $content_array);
					}
				}
			}else{
				/*
					Load the Administration menu if in admin mode then load the file
					menu_XXXXX.xml
				*/

				if (file_exists($locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_menu.xml")){
					$content_array = file($locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_menu.xml");
					$content .= implode("", $content_array);
				}
				/*
					If in admin mode then if the module is loaded we will load the file
					admin_XXXXX.xml
				*/
				if ($this->parent->modules[$i]["loaded"]==1){
					if (file_exists($locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_general.xml")){
						$content_array = file($locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_general.xml");
						$content .= implode("", $content_array);
					}
					if (file_exists($locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_admin.xml")){
						$content_array = file($locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_admin.xml");
						$content .= implode("", $content_array);
		//				print "<br/>found ".$locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_admin.xml";
		//			} else {
		//				print "<br/>Not found ".$locale_dir."/".strtolower($this->translation_language_codex)."/".$this->parent->modules[$i]["name"]."_admin.xml";
					}
				}
			}
		}
		//;
/*		if ($dir = @opendir($locale_dir."/".strtolower($this->translation_language_codex)."/")) {
			// This is the correct way to loop over the directory. 
			while (false !== ($file = readdir($dir))) { 
				if ($file != "." && $file != "..") { 
					$content_array = file($locale_dir."/".strtolower($this->translation_language_codex)."/".$file);
					$content .= implode("", $content_array);
       			} 
	      	}
			closedir($dir);
		}*/
		return $content;
	}

	function moduletidy($str){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"tidy(",__LINE__,"<li>$str</li>"));}
		$versionData = split("\.", phpversion()."");
		if ($versionData[0]*1>=5){
			$replace = Array("[[lt]]","[[gt]]","'","'","\"","\"","[[eur]]","[[pound]]");
			$find	 = Array("&lt;","&gt;","’","‘","“","”","€","£");
			$replace = Array("[[lt]]","[[gt]]");
			$find	 = Array("&lt;","&gt;");
			$str = htmlentities(
					tidy_repair_string($this->validate(preg_replace("'<([\/\?a-zA-Z0-9\#]+:)(.*?)>'si",'', str_replace($find, $replace, $str."<p>&nbsp;</p>"))), 
						Array(
//		"tidy-mark" => 0,
		"enclose-text" => 1,
		"enclose-block-text" => 1,
		"drop-font-tags" => 0,
		"logical-emphasis" => 1,
		"word-2000" => 1,
		"output-xml" => 1,
		"output-xhtml" => 1,
		"numeric-entities" => 1,
		"wrap-asp" => 0,
		"wrap-jste" => 0,
		"wrap-php" => 0,
		"wrap" => 0,
//		"input-encoding" => "ascii",
//		"output-encoding" => "ascii",
		"literal-attributes" => 1,
		"indent" => 0,
		"add-xml-decl" => 1,
		"input-xml" => 0,
		"assume-xml-procins" => 0,
		"clean" => 0,
		"add-xml-space" => 1,
		"bare" => 1,
		"lower-literals" => 1,						
							"quote-marks" => 1,
							"quote-nbsp" => 1,
							"show-body-only" => 1
						)
					)
				);
	/*************************************************************************************************************************
	Array(
		"tidy-mark" => 0,
		"enclose-text" => 1,
		"enclose-block-text" => 1,
		"drop-font-tags" => 0,
		"logical-emphasis" => 1,
		"word-2000" => 1,
		"output-xml" => 1,
		"output-xhtml" => 1,
		"numeric-entities" => 1,
		"wrap-asp" => 0,
		"wrap-jste" => 0,
		"wrap-php" => 0,
		"wrap" => 0,
		"show-body-only" => 1,
		"input-encoding" => "ascii",
		"output-encoding" => "ascii",
		"literal-attributes" => 1,
		"indent" => 0,
		"add-xml-decl" => 1,
		"input-xml" => 0,
		"assume-xml-procins" => 0,
		"clean" => 0,
		"add-xml-space" => 1,
		"bare" => 1,
		"lower-literals" => 1
	)
	*************************************************************************************************************************/
			$str_array = split("&lt;p&gt; &lt;/p&gt;",trim($str));
			$str="";
			$len = count($str_array);
			for($i=0;$i<$len;$i++){
				if($i!=0 && $i!=$len-1){
					$str .= "&lt;p&gt; &lt;/p&gt;";
				}
				$str .= $str_array[$i];
			}
			$str_array = split("<p>&nbsp;</p>",trim($str));
			$str="";
			$len = count($str_array);
			for($i=0;$i<$len;$i++){
				if($i!=0 && $i!=$len-1){
					$str .= "<p>&nbsp;</p>";
				}
				$str .= $str_array[$i];
			}
			
			

			$tidyerror="";
		} else {
			include_once (dirname(__FILE__)."/mytidy/mytidy.php");
			$tidy  							= new myTidy();
			$tidy_dir						= $this->parent->site_directories["TIDY_DIR"];
			$tidy_tmp						= $this->parent->site_directories["TIDY_TMP_DIR"];
			$tidy_cfg						= $this->parent->site_directories["TIDY_CFG"];
			$str = htmlentities($tidy->executetidy(str_replace(Array("&lt;","&gt;"),Array("[[lt]]","[[gt]]"),$str),$tidy_dir, $tidy_tmp, $tidy_cfg));
			$tidyerror						= $tidy->error();
		}
		$find 		= Array ("'&#10;'si","'&#13;'si","'\\n'","'\\r'");
		$replace 	= Array ('','','','');
		
		$url_str = preg_replace (
			$find,
			$replace,
			$str
		);

		if ($tidyerror==""){
			return $str;
		} else {
			$this->call_command("ENGINE_ERROR",array(__FILE__,__LINE__,"Warning error - <strong>#LS000007</strong> - problem with tidy (".$tidyerror.")<br>"));
			return $str;
		}
	}
	function tidy($str){
		return $this->moduletidy($str);
	}

	function strip_tidy($str){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"strip_tidy(",__LINE__,"<li>$str</li>"));}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"strip_tidy(",__LINE__,"<li>".$this->validate($str)."</li>"));}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"strip_tidy(",__LINE__,"<li>".$this->moduletidy($this->validate($str))."</li>"));}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"strip_tidy(",__LINE__,"<li>".html_entity_decode($this->moduletidy($this->validate($str)))."</li>"));}
		$str = htmlentities(rtrim(strip_tags(html_entity_decode($this->moduletidy($this->validate($str))))));
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"strip_tidy(",__LINE__,"<li>$str</li>"));}
		$find = Array(
				"\r\n",
				"\n",
				"\r"
		);
		$replace = Array(
				" ",
				" ",
				" "
			);
		$new_str = str_replace($find, $replace, $str);
//		print "<li>$new_str</li>";
//		$this->exitprogram();
		return $new_str;
	}
	function text_tidy($str){
		$str = str_replace(Array("\r\n","<br>","<BR>"),Array("::o0OO0o::","::o0OO0o::","::o0OO0o::"),$str);
		$str = htmlentities(rtrim(strip_tags(html_entity_decode($this->tidy($this->validate($str))))));
		$find = Array(
				"\r\n",
				"\n",
				"\r",
				"::o0OO0o::"
		);
		$replace = Array(
				" ",
				" ",
				" ",
				"\r\n"
			);
		$new_str = str_replace($find, $replace, $str);
		return $new_str;
	}

	function file_extension($file_name){
		$ext = "";
		$pos = strrpos($file_name, ".");
		if ($pos === false) { // note: three equal signs
		   	// not found...
		} else {
			$ext = substr($file_name,$pos);
		}
		return $ext;
	}
	
	function get_constant($str){
		if (defined($str)){
			eval("\$s= $str;");
			return $s;
		} else {
			return $str;
		}
	}
	
	function export_format($data_list,$format="CSV"){
		$out="";
		for ($index=0, $max=count($data_list); $index<$max; $index++){
			if ($index==0){
				$pos=0;
				foreach ($data_list[$index] as $key => $value){
					if (!is_int($key)){
						if($pos!=0){
							if ($format=="CSV"){
								$out.=",";
							}
							if ($format=="TAB"){
								$out.="	";
							}
						}
						$out.="[$key]";
						$pos++;
					}
				}
				$out.="\r\n";
			}
			$pos=0;
			foreach ($data_list[$index] as $key => $value){
				if (!is_int($key)){
					if($pos!=0){
						if ($format=="CSV"){
							$out.=",";
						}
						if ($format=="TAB"){
							$out.="	";
						}
					}
					$out.="$value";
					$pos++;
				}
			}
			$out.="\r\n";
		}
		print $out;
		$this->exitprogram();
	}
	
	function timetodescription($time){
		if ($time == 0){
			return "0s";
		}else{
		$days	= floor($time / (3600*24));
		$hour	= floor(($time - ($days * (3600*24)))  / 3600);
		$min	= floor(($time - (($days * (3600*24)) + ($hour *3600))) / 60);
		$sec	= ($time % 60);
		$out	= "";
		if ($days>0)
			$out = $days."d ";
		if ($hour>0)
			$out .= $hour."h ";
		if ($min>0)
			$out .= $min."m ";
		if ($sec>0)
			$out .= $sec."s";
		return $out;
		}
	}
	/*************************************************************************************************************************
    * check a string to see if it is a email address
	*
	* @param String string representing an email address
	* @return Boolean is it a valid email address 
    *************************************************************************************************************************/
	function check_email_address($email){
		$index_of_at = strpos($email,"@");
		if ($index_of_at){
			$index_of_dot= strpos($email,".",$index_of_at);
			if ($index_of_dot){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	function html_2_txt($str){
		$str =  preg_replace("'\r'","", $str);
		$str =  preg_replace("'\n'","", $str);
		$str =  preg_replace("'</p><p>'","\n\n", $str);
		$str =  preg_replace("'<br />'","\n", $str);
		$str =  preg_replace("'<br/>'","\n", $str);
		$str =  preg_replace("'<br>'","\n", $str);
		$str =  preg_replace("'<p>'","\n", $str);
		$str =  preg_replace("'</p>'","\n", $str);
		$str =  preg_replace("'  '"," ", $str);
		$str =  preg_replace("'\n \n'","\n\n", $str);
		$str =  preg_replace("'\n\n\n'","\n\n", $str);
		$str =  preg_replace("'\n\n\n'","\n\n", $str);
		$str =  preg_replace("'\n\n\n'","\n\n", $str);
		/*
		$str =  preg_replace("'</p><p>'","\r\n\r\n", $str);
		$str =  preg_replace("'<br />'","\r\n", $str);
		$str =  preg_replace("'<br/>'","\r\n", $str);
		$str =  preg_replace("'<br>'","\r\n", $str);
		$str =  preg_replace("'<p>'","\r\n", $str);
		$str =  preg_replace("'</p>'","\r\n", $str);
		$str =  preg_replace("'  '"," ", $str);
		$str =  preg_replace("'\r\n \r\n'","\r\n\r\n", $str);
		$str =  preg_replace("'\r\n\r\n\r\n'","\r\n\r\n", $str);
		$str =  preg_replace("'\r\n\r\n\r\n'","\r\n\r\n", $str);
		$str =  preg_replace("'\r\n\r\n\r\n'","\r\n\r\n", $str);
		*/
//		print "<pre>".$str."</pre>";
		return strip_tags($str);
	}
	function txt2html($str){
//		print "<li>$str</li>";
//		$str =  preg_replace("&#65533;", "", $str);
//
		$str =  preg_replace("'\r'","", $str);
		$str =  preg_replace("'\n'","<br>", $str);
		$str =  preg_replace("'<br><br>'", "</p><p>", $str);
		$str =  preg_replace("'<br>  <br>'", "</p><p>", $str);
		$str =  preg_replace("'<br> <br>'", "</p><p>", $str);
		$str =  preg_replace("'<p>  </p>'", "</p><p>", $str);
		$str =  preg_replace("'<p> </p>'", "</p><p>", $str);
		$str ="<p>".$str."</p>";
		$str = str_replace("'<p></p>'","",$str);
		$str = $this->generate_links($str);
		return $str;
	}
	
	function generate_links($str, $start=1,$find = ">http://"){
		if (strtolower(chr(ord($find))) != "h"){
			$plus_pos = 1;
		} else {
			$plus_pos = 0;
		}
		if (strlen($str)>$start){
			$pos = strpos($str,$find,$start);
			if ($pos===false){
				if (strpos($str," http://")){
					return $this->generate_links($str, 0," http://");
				} else {
					return $this->generate_www_links($this->generate_www_links($str),0,">www.");
				}
			} else {
				$space   = strpos($str," ",$pos+$plus_pos);
				$angle   = strpos($str,"<",$pos+$plus_pos);
				if (!$space){
					if ($angle){
						$end_angle=1;
						$end = $angle;
					}else{
						$end = strlen($str);
					}
				} else {
					if ($angle){
						if ($angle<$space){
							$end = $angle;
						}else{
							$end = $space;
						}
					} else{
						$end = $space;
					}
				}
				$s		 = substr($str, 0, $pos+$plus_pos);
				$link	 = substr($str, $pos+$plus_pos, $end-($pos+1+$plus_pos));
				if (strpos(" ".$link,"http://")===false){
					$link = "http://".$link;
				}
				$s	   	.= '<a href="'.$link.'" target="_external" title="Links supplied by a user will open in an external window">'.$link.'</a> ';
				$l		 = strlen($s);
				if (strlen($str)>$end+1){
					$s		.= substr($str,$end);
				}
				return $this->generate_links($s, $l);
			}
		} else {
			return $str;
		}
	}
	function generate_www_links($str, $start=0, $find=" www."){
		if (strtolower(chr(ord($find))) != "h"){
			$plus_pos = 1;
		} else {
			$plus_pos = 0;
		}
		$start_angle = strpos($find,">");
		if (strlen($str)>$start){
			
			$pos = strpos($str,$find,$start);
			if ($pos===false){
			
				return $str;
			} else {
				$space = strpos($str," ",$pos+$plus_pos);
				$angle = strpos($str,"<",$pos+$plus_pos);
				if (!$space){
					if ($angle){
						$end_angle=1;
						$end = $angle;
					}else{
						$end = strlen($str);
					}
				} else {
					if ($angle){
						if ($angle<$space){
							$end = $angle;
						}else{
							$end = $space;
						}
					} else{
						$end = $space;
					}
				}
				$s		 = substr($str, 0, $pos+$plus_pos);
				$link	 = substr($str, $pos+$plus_pos, $end-($pos+1));
				$s	   	.= '<a href="http://'.$link.'" target="_external" title="Links supplied by a user will open in an external window">'.$link.'</a> ';
				$l		 = strlen($s);
				if (strlen($str)>$end+1){
					$s		.= substr($str,$end);
				}
				return $this->generate_www_links($s, $l);
			}
		} else {
			return $str;
		}
	}

	
	function tidy_parameter($parameters,$index,$default=""){
		if (strlen(trim($this->check_parameters($parameters,$index,$default)))>0)
			return trim(strip_tags($this->tidy($this->check_parameters($parameters,$index,$default))));
		else 
			return $this->check_parameters($parameters,$index,$default);
	}
	
	function load_editors(){
		/**
		* initialise the editors that this module requires.
		*/
		$sql ="select module_access_to_editor.*, editor_config.editor_config_label from module_access_to_editor 
		left outer join editor_config on editor_config.editor_config_identifier = module_access_to_editor.mate_configuration
		where mate_module = '$this->module_command' and mate_client=".$this->client_identifier;
//		print "<p>$sql</p>";
		$result = $this->call_command("DB_QUERY",array($sql));
		while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result)) ){
			$status = ($r["mate_status"])? "locked" : "unlocked" ;
			$this->editor_configurations[$r["mate_name"]] = Array(
				"status" => $status, 
				'locked_to' => $this->check_parameters($r,"editor_config_label"), 
				'identifier' =>  $this->check_parameters($r,"mate_configuration",0)
			);
		}
		
	}
	function show_255($str){
		$str = substr(strip_tags($this->convert_amps($str)),0,255);
		$pos = strrpos($str," ");
		if ($pos){
			return substr($str,0,$pos);
		} else {
			return $str;
		}
	}
	function convert128($str){

		$list = split(".",$str);
		for ($index=0,$max=count($list);$index<$max;$index++){
			if (ord($list[$index])>=128){
				$list[$index] = "&#".ord($list[$index]).";";
			}
		}
		return join("",$list);
	}

    /**
    * exit program properly calls engine shut down
    */
	function exitprogram(){
		session_write_close();
		$this->call_command("ENGINE_CLOSE");
		exit();
	}

    /**
    *
    */
	function generate_default_editor(){
		return Array(
				"status"=>"unlocked",
				"locked_to" => "",
				"identifier"=>0
			);
	}

    /**
    *
    */
	function sorry_no_access_to_this_functionality($parameters){
		return "<module name='$this->module_name' display='form'><form name='noaccess' label='NO Acccess Available'><text><![CDATA[Sorry you do not have access to this function]]></text></form></module>";
	}

    /**
    *
    */
	function debugit($bool,$parameters){
		if ($this->check_parameters($parameters,"LIBERTAS_XML")=="OPEN_AND_DISPLAY"){
			$bool = false;
		}
		return $bool;
	}

    /**
    *
    */
	function load_locale($fname=""){
		//$_SESSION["locale_path"] = $this->check_parameters($this->parent->site_directories,"LOCALE_FILES_DIR");
		$locale_path = $this->check_parameters($this->parent->site_directories,"LOCALE_FILES_DIR");
	    if ($fname==""){
            if (file_exists($locale_path."/en/locale.php")){
                include_once ($locale_path."/en/locale.php");
			}
		}else{
			if (file_exists($locale_path."/en/locale_$fname.php")){
                include_once ($locale_path."/en/locale_$fname.php");
			}
		}
	}
    /**
    * return the current time
    */
	function libertasGetTime(){
		return time();
	}
    /**
    * return the date/time in a desired format
    *
    * if the 2nd parameter (timestamp) is supplied it will use that specified timestamp instead of current time stamp
    * @param $strFormat String format to apply to date  default("Y/m/d H:i:s")
    * @param $timestamp integer
    */
	function libertasGetDate($strFormat = "Y/m/d H:i:s",$timestamp=""){
		if ($timestamp=="")
		 	return Date($strFormat);
		else
			return Date($strFormat, $timestamp);
	}

    /**
    *
    */
	function location_tab($all_locations, $set_inheritance, $original_menu_locations, $display_tab="", $extraout="", $override=Array()){
		$label		= $this->check_parameters($override,"label",LOCALE_LOCATIONS);
		$locale		= $this->check_parameters($override,"locale","");
		$type		= $this->check_parameters($override,"type","0");
		$name		= $this->check_parameters($override,"name","");
		$hidden		= $this->check_parameters($override,"hidden","0");
		$counter	= $this->check_parameters($override,"counter","");
		$allabel	= $this->check_parameters($override,"all_locations_label","");
		$wllabel	= $this->check_parameters($override,"what_locations_label","");
		$shlabel	= $this->check_parameters($override,"inherit_locations_label","");
		$inheritable= $this->check_parameters($override,"inheritable",1);
		$out = "<section name ='".$name."locations' label=\"".$label."\"";
		if ($display_tab==$name."locations"){
			$out .= " selected='true'";
		}
		if ($hidden==1){
			$out .= " hidden='true'";
		}
		$out .= ">";
/*		$out .= "<set name='multiple_".$name."_location' ";
		if ($type!="multi"){
			$out .= " hidden='true'";
		}
		$out .= ">";*/
		$out .= $extraout;
		if ($allabel!=""){
			$l = $allabel;
		} else {
			$l = $this->get_constant("LOCALE_".$locale."ALL_LOCATIONS");
		}
		$out .="<radio label=\"".$l."\" name=\"".$name."all_locations\" onclick='menu_locations'";
		if 	($counter != ""){
			$out .=" hidden=\"YES\"";
		}
		$out.= ">
				<option value=\"1\"";
		if 	($all_locations == '1'){
			$out .=" selected=\"true\"";
		}
		$out.= ">".ENTRY_YES."</option>
			<option value=\"0\"";
		if 	($all_locations == '0'){
			$out .=" selected=\"true\"";
		}
		$out.= ">".ENTRY_NO."</option>";
		$out .="</radio>";
		if ($shlabel!=""){
			$l = $shlabel;
		} else {
			$l = $this->get_constant("LOCALE_".$locale."SET_INHERITANCE");
		}
		if ($inheritable==1){
			$out .="<radio label=\"".$l."\" name=\"".$name."set_inheritance\" onclick='toggle_inheritance'";
			if($all_locations==1){
				$out .= " hidden='YES' ";
			}
			$out.=">
					<option value=\"1\"";
			if 	($set_inheritance == '1'){
				$out .=" selected=\"true\"";
			}
			$out.= ">".ENTRY_YES."</option>
				<option value=\"0\"";
			if 	($set_inheritance == '0'){
				$out .=" selected=\"true\"";
			}
			$out .= ">".ENTRY_NO."</option>";
			$out .= "</radio>";
		} else {
			$out.="<input type='hidden' name='".$name."set_inheritance' value='0'/>";
		}
/*
 // old style
		$out .= "<checkboxes ";
		if($all_locations==1){
			$out .= " hidden='YES' ";
		}
		$out.="label=\"".LOCALE_WHAT_LOCATIONS."\" name=\"menu_locations\">$menu_locations</checkboxes>";
*/		
		$menu_locations =$this->call_command("LAYOUT_LIST_MENU_OPTIONS_HEIRARCHY",Array("list"=>$original_menu_locations));

		$out .= "<checkboxes ";
		if($all_locations==1){
			$out .= " hidden='YES' ";
		}
		if($type==2){
			$out .= " shownumber='YES' ";
		}
		if ($wllabel!=""){
			$l = $wllabel;
		} else {
			$l = $this->get_constant("LOCALE_".$locale."WHAT_LOCATIONS");
		}
		$out .= "label=\"".$l."\" name=\"".$name."menu_locations\">$menu_locations</checkboxes>";
		if ($counter!=""){
			$out .= "<counters>$counter</counters>";
		}
/*		$out .="</set>";
		$out .= "<set name='single_".$name."_location' ";
		if ($type=="multi"){
			$out .= " hidden='true'";
		}
		$out .= ">";
		$menu_locations =$this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($original_menu_locations));
		$out .="	<select name='single_".$name."menu_locations' label='".$this->get_constant("LOCALE_".$locale."WHAT_LOCATIONS")."'>$menu_locations</select>";
		$out .="</set>";
*/
	$out .= "	</section>";
		return $out;
	}
    /**
    *
    */
	function set_inheritance($cmd, $select_sql_str){
		$this->call_command("DB_QUERY",Array("delete from menu_channel_inheritance where mci_command='$cmd' and mci_client = $this->client_identifier"));
		$result  = $this->call_command("DB_QUERY",Array($select_sql_str));


		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$menu = $r["menu_id"];
			$sql = "insert into menu_channel_inheritance (mci_menu,mci_command,mci_client) values ($menu, '$cmd', $this->client_identifier)";
			$this->call_command("DB_QUERY",Array($sql));
		}
		$this->call_command("DB_FREE",Array($result));
	}

    /**
    * This function will get a list of menu locations that are children of this menu location
    */
	function add_inheritance($cmd,$list_of_menu_locations){
		$my_menus = $this->call_command("LAYOUT_GET_MENUS");
		$mml = count($my_menus);
		$mll = count($list_of_menu_locations);
		$my_list_of_inheritance_locations = Array();
		$insert_index=0;
		if($list_of_menu_locations.""!=""){
			for($x=0; $x<$mll; $x++){
				for($index=0; $index<$mml; $index++){
					if ($my_menus[$index]["IDENTIFIER"] == $list_of_menu_locations[$x]){
						$list =$this->define_inheritance($list_of_menu_locations[$x],$my_menus,$list_of_menu_locations);
						$l = count($list);
						if ($l>0){
							for ($li = 0; $li < $l ;$li++){
								$my_list_of_inheritance_locations[$insert_index] = $list[$li];
								$insert_index++;
							}
						}
					}
				}
			}
		}
		return $my_list_of_inheritance_locations;
	}

    /**
    * This function will get a list of menu locations that are children of this menu location
    */
	function define_inheritance($id,$mm,$loml){
		$mmml=count($mm);
		$my_list_of_inheritance_locations = Array();
		$insert_index=0;
		for($i=0; $i<$mmml; $i++){
			if ($mm[$i]["PARENT"]==$id){
				$list = $this->define_inheritance($mm[$i]["IDENTIFIER"],$mm,$loml);
				$c= count($loml);
				$f = 0;
				for($index=0;$index<$c;$index++){
					if ($loml[$index] == $mm[$i]["IDENTIFIER"]){
						$f=1;
					}
				}
				if ($f==0){
					$my_list_of_inheritance_locations[$insert_index] = $mm[$i]["IDENTIFIER"];
					$insert_index++;
				}
				$l = count($list);
				if ($l>0){
					for ($li = 0; $li < $l ;$li++){
						$my_list_of_inheritance_locations[$insert_index] = $list[$li];
						$insert_index++;
					}
				}
			}
		}
		return $my_list_of_inheritance_locations;
	}
    /**
    *
    */
	function preview_section($preview_command, $inNewTab=1 , $refreshBtn=0, $plist= Array()){
		$out ="";
		$igCmd  = $this->check_parameters($plist,"igCmd");
		if ($inNewTab==1){
			$out .="<section label='".LOCALE_PREVIEW."' onclick='previewFrame' accesskey='p'>";
			$out .="	<input type='hidden' name='preview_command' value='$preview_command'/>";
			$out .="	<input type='hidden' name='preview_loaded' value='0'/>";
			if ($igCmd!=""){
				$out .="	<input type='hidden' name='igCmd' value='$igCmd'/>";
			}
			if($refreshBtn==1){
				$out .="	<btn class='bt' id='refreshBtn' value='Refresh' onclick='previewFrame' hidden='YES'/>";
			}

			$out .="	<frame name='preview' width='100%' height='700' hidden='YES'></frame>";
			$out .="	<text id='preview_loading' class='msg'><![CDATA[".LOCALE_PREVIEW_LOADING."]]></text>";
			$out .="</section>";
		} else {
			if ($igCmd!=""){
				$out .="	<input type='hidden' name='igCmd' value='$igCmd'/>";
			}
			$out .="	<input type='hidden' name='preview_command' value='$preview_command'/>";
			$out .="	<frame name='preview' width='200' height='100%'></frame>";
		}
		return $out;
	}
    /**
	* interface function write your own to overwrite this one.
    */
	function filter($parameters){
		$cmd	= $this->check_parameters($parameters,"command","");
		$page	= $this->check_parameters($parameters,"page",1);
		$str ="
		<form name='defaultfilter' method='post'>
			<input type='hidden' name='command' value='$cmd'/>
			<input type='hidden' name='page' value='$page'/>
		</form>";
		return $str;
	}

    /**
    *
    */
	function check_editor($myArray=Array(), $editor=""){
		$editor_content  = $this->check_parameters($myArray,$editor);
/*
		print "ed ::".$editor_content ;
		$tidy = $this->tidy($editor_content);
		print "\n\n\nentities :: ".htmlentities($tidy);
		print "\n\n\nvalidate :: ".$this->validate(htmlentities($tidy));
		print "\n\n<strong>ecfe::</strong>\n".$this->call_command("EDITOR_CONVERT_FROM_EDITOR", Array("string" => $this->validate(htmlentities($tidy))));
		print "\n\n\n".$tidy;
		$this->exitprogram();
*/
		return htmlentities($this->split_me($this->call_command("EDITOR_CONVERT_FROM_EDITOR", Array("string" => $this->validate(htmlentities($this->tidy($editor_content))))),"'","&#39;"));
	}
    /**
    *
    */
	function parseDomain($domain){
		$list = split("\.",$domain);
		$str="";
		if ($list[0]=="www"){
			$start = 1;
		} else {
			$start = 0;
		}
		for ($i=$start; $i<count($list);$i++){
			if ($i>$start)
				$str .= ".";
			$str .= $list[$i];
		}
		return $str;
	}

    /**
    *
    */
	function decodeHTML($string) {
		$string = strtr($string, array_flip(get_html_translation_table(HTML_ENTITIES)));
		$string = preg_replace("/&#([0-9]+);/me", "chr('\\1')", $string);
		if (strpos($string,"&amp;")===false){
			return $string;
		} else {
			$string = $this->decodeHTML(html_entity_decode($string));
			return $string;
		}
	}

    /**
    *
    */
	function mystrip_tags($text){
		return htmlentities(strip_tags(str_replace(
		Array("\r\n"	,"\n"	,"\r"	,"\t"	,"  "	,"	"	,"&quot;"	,"&#39;"	,"'"	,"\""	,"&#34;"	,"&lt;"	,"&gt;"),
		Array(" "		," "	," "	," "	,""		,""		,""			,""			,""		,""		,""			,"<"	,">"),
		$this->decodeHTML(html_entity_decode($text)))));
	}
    /**
    *
    */
	function gen_sql_cache($parameters){
		$match_list	= $this->check_parameters($parameters,"match_list");
		$block		= $this->check_parameters($parameters,"block");
		$blocklist	= split("\r\n",$block);

		$where ="";
		$max = count($blocklist)-1;
		for($index = 0 ; $index <$max ; $index++){
			$blocklist[$index] = split(":::",$blocklist[$index]);
			$i = $blocklist[$index][2]*1;
			$where .= $blocklist[$index][1]." ". str_replace(Array("[[value]]"), Array($blocklist[$index][4]) , trim($match_list[$i][1]));
			if ($index<$max-1){
				if($blocklist[$index][3]==0){
					$where .= " and ";
				} else {
					$where .= " or ";
				}
			}
		}
		$where .="";
		if ($where ==""){
			return "";
		} else {
			return " ($where)";
		}
	}


    /**
    *
    */
	function tidyup_display_commands($parameters){
		$identifier		= $this->check_parameters($parameters, "identifier",0);
		$seperate		= $this->check_parameters($parameters, "seperate",0);
		$all_locations	= $this->check_parameters($parameters, "all_locations",0);
		$table 			= $this->check_parameters($parameters, "tidy_table","information_features");
		$field_starter  = $this->check_parameters($parameters, "tidy_field_starter","ifeature_");
		$webobj			= $this->check_parameters($parameters, "tidy_webobj",$this->webContainer."FEATURES");
		$module			= $this->check_parameters($parameters, "tidy_module",$this->webContainer."FEATURES");

		/**
		* extract the number of entries that have all locations selected if one has selected all locations then make sure 
		* that all locations are set.
	    */

		if($seperate==0){
			$sql ="select * from $table where ".$field_starter."client=$this->client_identifier and ".$field_starter."all_locations=1";
//			print "<li>$sql</li>";
//			$this->exitprogram();
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"SQL",__LINE__,"$sql"));}
			$result  = $this->call_command("DB_QUERY",Array($sql));
	    	$num = $this->call_command("DB_NUM_ROWS",Array($result));
		} else {
			$mls = $this->call_command("LAYOUT_MENU_LOCATION_SETTINGS_GET", Array("identifier"=>$identifier, "module"=>$this->webContainer));
			$saved_all_locations	= $mls["all_locations"];
			$saved_set_inheritance	= $mls["set_inheritance"];
			if ($saved_all_locations==1){
				$num = 1;
			} else {
				$num = 0;
			}
		}
		/**
		* turn the global command on or off Global commands are used on menu creation to automatically select a series of
		* display commands for a new location.
	    */
		if ($num==0){
			$sql ="select distinct menu_to_object.mto_menu as m_id from menu_to_object where mto_client=$this->client_identifier and mto_module='".$module."'";
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>$webobj,"status"=>"OFF"));
		} else {
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>$webobj,"status"=>"ON"));
			$sql ="select distinct menu_identifier as m_id from menu_data where menu_client=$this->client_identifier";
		}
//		print "<li>$sql</li>";
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        -	execute one of the two sql statements which will return a list of menu ids
        */
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"SQL",__LINE__,"$sql"));}
		$result  = $this->call_command("DB_QUERY",Array($sql));

		/**
		* remove all occurances of this webobj command from the display_data table for this client
	    */
		$sql = "delete from display_data where display_client=$this->client_identifier and display_command='$webobj'";
//		print "<li>$sql</li>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"SQL",__LINE__,"$sql"));}
		$this->call_command("DB_QUERY",Array($sql));
		/**
		* for each menu location listed add the command to the table
	    */
		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if ($r["m_id"]>0){
				$sql = "insert into display_data (display_client, display_command, display_menu) values ($this->client_identifier, '$webobj', ".$r["m_id"].")";
//				print "<li>$sql</li>";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array("module::".$this->module_name,"SQL",__LINE__,"$sql"));}
				$this->call_command("DB_QUERY",Array($sql));
			}
   	   	}
		$this->call_command("DB_FREE",Array($result));
//		$this->exitprogram();
	}

	function check_prefs($parameters){
		$find_preference  = $this->check_parameters($parameters,0,"");
		if ($find_preference==""){
			return "";
		} else {
			if ($this->check_parameters($this->parent->system_prefs,$find_preference,"")!=""){
				return $this->parent->system_prefs[$find_preference];
			} else {
				return $this->call_command("SYSPREFS_EXTRACT_SYSTEM_PREFERENCE",$parameters);
			}
		}
	}
	
	function makeSpecial($command, $uri, $savefile, $extra, $fake_title=""){
		$root		= $this->check_parameters($this->parent->site_directories,"ROOT");
		$extraline ="";
		foreach($extra as $key => $value){
			if($extraline!=""){
				$extraline .=", ";
			}
			$extraline .= '"'.$key.'" => "'.$value.'"';
		}
		
		$fp = fopen($savefile,"w");
		fputs($fp,"<"."?php\n
			\$mode			= \"EXECUTE\";
			\$command		= \"$command\";
			\$fake_uri 		= \"$uri\";
			\$fake_title	= \"$fake_title\";
			\$extra			= Array($extraline);
			require_once \"".$root."/admin/include.php\"; 
			require_once \"\$module_directory/included_page.php\";
			?".">");
		fclose($fp);
		$um = umask(0);
		@chmod($savefile, LS__FILE_PERMISSION);
		umask($um);
	}

	/**
	* This is a list of the forms available to the website only NOT forms that the Administration 
	* can use.
	*/	
	function list_forms(){
		return $this->available_forms;
	}
	
	function generate_report_links($list){
		print_r($list);
	}
	/**
    * check if a file exists and create it not found
	*
	* @param String $file the file name 
	* @param String $uri the web path to the file
	* @param Array $extracmd A list of Associated parameters to be called ie Array("key1" => "value1", "key2" => "value2", ... "keyn" => "valuen")
	* @param String $command the command that should get called.
	* @param String $fake_title $replace When instances found replace with this string
	* 
	* @return bool
    */
	function checkPage($file, $uri, $extracmd, $command, $fake_title){
		$root = $this->parent->site_directories["ROOT"];
		$savefile = $root."/".$uri."/".$file;
		if (!file_exists($savefile)){
			$fp = fopen($savefile,"w");
			$str = "<"."?php\n
				\$mode			= \"EXECUTE\";
				\$command		= \"$command\";
				\$fake_uri 		= \"".$this->parent->script."\";
				\$fake_title	= \"$fake_title\";
				\$extra			= Array(";
			$c=0;
			foreach($extracmd as $key => $value){
				if ($c!=0){
					$str .= ",";
				}
				$str .= "\"$key\"=>\"$value\"";
				$c++;
			}
			$str .= ");
				require_once \"".$root."/admin/include.php\"; 
				require_once \"\$module_directory/included_page.php\";
				?".">";
			fputs($fp,$str);
			fclose($fp);
			$um = umask(0);
			@chmod($savefile, LS__FILE_PERMISSION);
			umask($um);
			return false;
		} else {
			return true;
		}
	}
	
	/**
	* create a unique_id for inserting into database
	*
	* this function will generate a 19 digit number for use as identifier in fields
	* why 19 digits when MySql allows an integer to be 20 digits, simple it allows 
	* 20 digit numbers but the first digit of a twenty digit number can only be the
	* digit one.
	* 
	* It takes no parameter as it will generate the Identifier as follows in reverse order 
	* 1. the last three digits are 3 random digits
	* 2. the next six digits are the milliseconds on that second
	* 3. the first ten digits are the number of seconds since 01/01/1970, minus the number of seconds from "01/01/2004"
	*
	* @uses $this->getUid();
	* @return Integer 19 digit number breaks 
	*/
	function getUid(){
		list($usec, $sec) = explode(" ",microtime());
		// remove number of seconds since 01/01/2004
		$guid = "".($sec-1072915200) . substr("".$usec,2,6);
		for($i=0;$i<3;$i++){
			$guid .= "".rand(0,9);
		}
		return $guid;
	}
 	/*************************************************************************************************************************
    * return a list of option tags
 	*
 	*
 	* @param Array list of values
 	* @param Array list of labels
 	* @param Mixed string or array of selected values
 	* @return String XML Option Tags for for elements
    *************************************************************************************************************************/
 	function gen_options($list_of_values, $list_of_labels, $list_of_selected, $max_number=-1){
 		if(!is_array($list_of_selected)){
 			$tmp = $list_of_selected;
 			$list_of_selected = Array();
 			$list_of_selected[0] = $tmp;
 		}
 		$len_index = count($list_of_values);
 		$len_select= count($list_of_selected);
 		$out = "";
		if($max_number!=-1){
			if($max_number<$len_index){
				$len_index = $max_number;
			}
		}
		if($len_index > count($list_of_values)){
			$len_index = count($list_of_values);
		}
		if($len_index > count($list_of_labels)){
			$len_index = count($list_of_labels);
		}
 		for($i=0;$i<$len_index;$i++){
 			$selected="";
			if(in_array($list_of_values[$i], $list_of_selected)){
				$selected=" selected='true'";
			}
 			$out .="<option value=\"".$list_of_values[$i]."\" ".$selected.">".$list_of_labels[$i]."</option>\n";
 		}
 		return $out;
 	}
 	/*************************************************************************************************************************
    * return a list of option tags
 	*
 	*
 	* @param Array list of values and labels
 	* @param Mixed string or array of selected values
 	* @return String XML Option Tags for for elements
    *************************************************************************************************************************/
 	function gen_options2d($list_of_values, $list_of_selected, $max_number=-1){
 		if(!is_array($list_of_selected)){
 			$tmp = $list_of_selected;
 			$list_of_selected = Array();
 			$list_of_selected[0] = $tmp;
 		}
 		$len_index = count($list_of_values);
 		$len_select= count($list_of_selected);
 		$out = "";
		if($max_number!=-1){
			if($max_number<$len_index){
				$len_index = $max_number;
			}
		}
		if($len_index > count($list_of_values)){
			$len_index = count($list_of_values);
		}
		$len_selected = count($list_of_selected);
 		for($i=0;$i<$len_index;$i++){
 			$selected="";
			for($z=0;$z<$len_selected;$z++){
				if($list_of_values[$i][0]==$list_of_selected[$z]){
					$selected=" selected='true'";
				}
			}
 			$out .="<option value=\"".$list_of_values[$i][0]."\" ".$selected.">".$list_of_values[$i][1]."</option>\n";
 		}
 		return $out;
 	}	
	/*************************************************************************************************************************
    * convert an Ip address into a number
    *************************************************************************************************************************/
	function get_number_from_ip($ip="0.0.0.0"){
		$ip_values = split("\.",$ip);
		$value = bindec($this->make8(decbin($ip_values[0])).$this->make8(decbin($ip_values[1])).$this->make8(decbin($ip_values[2])).$this->make8(decbin($ip_values[3])));
		return $value;
	}
	/*************************************************************************************************************************
    * convert 8bit number to binary
    *************************************************************************************************************************/
	function make8($str){
		$left = 8- strlen($str);
		if ($left>0){
			$out = str_repeat("0",$left).$str;
		} else {
			$out = $str;
		}
		return $out;
	}
	/*************************************************************************************************************************
    * generic jumpto function
    *************************************************************************************************************************/
	function module_jumpto($parameters){
		$url  = $this->check_parameters($parameters,"url",-1);
		if ($url=="-1"){
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$this->parent->script));
		} else {
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$url));
		}
	}
	/*************************************************************************************************************************
    * check each character of the string for an ascii value greater than 127 
    *************************************************************************************************************************/
	function php_tidy($str){
/*		$l = strlen($content);
		$cArray = preg_split('//', $content, -1, PREG_SPLIT_NO_EMPTY); 
		$o="";
		for($i=0;$i<$l;$i++){
			if(ord($cArray[$i])>127){
				$o .= "&amp;#".ord($cArray[$i]).";";
			} else {
				$o .= $cArray[$i];
			}
		}
		return $o;
*/
			$tidy_parameters = Array(
				"enclose-text" => 1,
				"enclose-block-text" => 1,
				"drop-font-tags" => 0,
				"logical-emphasis" => 1,
				"word-2000" => 1,
				"output-xml" => 1,
				"output-xhtml" => 0,
				"numeric-entities" => 1,
				"wrap-asp" => 0,
				"wrap-jste" => 0,
				"wrap-php" => 0,
				"wrap" => 0,
				"literal-attributes" => 1,
				"indent" => 0,
				"add-xml-decl" => 1,
				"input-xml" => 1,
				"assume-xml-procins" => 0,
				"clean" => 0,
				"add-xml-space" => 1,
				"bare" => 1,
				"lower-literals" => 1,
				"quote-marks" => 1,
				"quote-nbsp" => 1,
				"show-body-only" => 1
			);
//			$str = str_replace(Array("&lt;","&gt;",""), Array("<",">"), tidy_repair_string($str."<p>&nbsp;</p>", $tidy_parameters, "utf8"));
			$str = str_replace(Array("&lt;","&gt;",""), Array("<",">"), tidy_repair_string($str."<p>&nbsp;</p>", $tidy_parameters));
			$str_array = split("&lt;p&gt; &lt;/p&gt;",trim($str));
			$str="";
			$len = count($str_array);
			for($i=0;$i<$len;$i++){
				if($i!=0 && $i!=$len-1){
					$str .= "&lt;p&gt; &lt;/p&gt;";
				}
				$str .= $str_array[$i];
			}
			$str_array = split("<p>&nbsp;</p>",trim($str));
			$str="";
			$len = count($str_array);
			for($i=0;$i<$len;$i++){
				if($i!=0 && $i!=$len-1){
					$str .= "<p>&nbsp;</p>";
				}
				$str .= $str_array[$i];
			}
			$tidyerror="";
		$find 		= Array ("'&#10;'si","'&#13;'si","'\\n'","'\\r'");
		$replace 	= Array ('','','','');
		
		$url_str = preg_replace (
			$find,
			$replace,
			$str
		);

		if ($tidyerror==""){
			return $str;
		} else {
			$this->call_command("ENGINE_ERROR",array(__FILE__,__LINE__,"Warning error - <strong>#LS000007</strong> - problem with tidy (".$tidyerror.")<br>"));
			return $str;
		}

	}
	function makeCleanOutputforXSL($str)
	{
		$str = strip_tags($str);
		$str = stripslashes($str);		
		$str = htmlentities($str, ENT_QUOTES);		
		return preg_replace('/[^!-%\x27-;=?-~ ]/e', '"&#".ord("$0").chr(59)', $str);
	}
	
	
    function XSSbasicClean($sourceParam) {
         return  preg_replace('/^[A-Za-z]([A-Za-z0-9\s_]*[A-Za-z0-9])*$/', '', $str);    	
    }
	
	function makeTimeStamp($str)
	{
		$arrstr  = explode(" ",$str);
		$arrdate = explode("-",$arrstr[0]);
		$arrtime = explode(":",$arrstr[1]);
		if ($arrdate[0]==0 && $arrdate[1]==0 && $arrdate[0]==0)
			return 0;
		else
			return mktime($arrtime[0],$arrtime[1],$arrtime[2],$arrdate[1],$arrdate[2],$arrdate[0]);		
	}	
	
	function getValidHTTPAgent($str)
	{
		$str = (ctype_print($str))?$str:'Unknown';
		return $str;
	}

}
?>