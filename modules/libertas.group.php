<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.groups.php
* @date 25 Nov 2002
*/
define ("GROUP_BUTTON_BACK","<button command=\"GROUP_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"></button>");
define ("GROUP_BUTTON_ADD","<button command=\"GROUP_ADD\" alt=\"".ADD_NEW."\" iconify=\"ADD\"></button>");
class group extends module{
	
	/**
	*  Class Variables
	*/
	var $module_name_label		= "Group Manager Administration Module";
	var $module_grouping		= "LOCALE_MANAGEMENT_GROUP_SECURITY";
	var $module_name			= "group";
	var $module_version 		= '$Revision: 1.11 $';
	var $module_admin			= "1";
	var $module_debug			= false;
	var $module_creation		= "25/11/2002";
	var $module_command			= "GROUP_"; 		// all commands specifically for this module will start with this token
	var $module_label			= "MANAGEMENT_GROUP";
	
	var $module_admin_options 	= array();
	var $module_admin_user_access = array();
	var $list_of_groups_returned = "";
	var $groups_returned_array = "";
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
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."RETRIEVE"){
				return $this->get_groups($parameter_list[0],$parameter_list);
			}
			if ($user_command==$this->module_command."RETRIEVE_BY_TYPE"){
				return $this->get_groups_by_type($parameter_list,"OBJECT");
			}
			if ($user_command==$this->module_command."RETRIEVE_BY_GROUP_TYPE"){
				return $this->get_groups_by_type($parameter_list,"GROUP");
			}
			/*************************************************************************************************************************
            * get group to object functions
            *************************************************************************************************************************/
			if ($user_command==$this->module_command."GET_OBJECT"){
				return $this->get_groups_to_object($parameter_list);
			}
			if ($user_command==$this->module_command."SET_OBJECT"){
				return $this->set_groups_to_object($parameter_list);
			}
			/*************************************************************************************************************************
            * 
            *************************************************************************************************************************/
			if ($user_command==$this->module_command."MENU_RELATE"){
				return $this->get_groups_related_to_menu($parameter_list[0]);
			}
			if ($user_command==$this->module_command."GET_MENUS"){
				return $this->get_menus_related_to_group($parameter_list[0]);
			}
			if ($user_command==$this->module_command."REMOVE"){
				$this->remove($parameter_list);
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
			}
			if ($user_command==$this->module_command."SAVE"){
				$ok = $this->save($parameter_list);
				if ($ok==1){
					$this->call_command("LAYOUT_CACHE_MENU_STRUCTURE");
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
				} else {
					return $ok;
				}
			}
			if ($user_command==$this->module_command."LIST"){
				return $this->display_list($parameter_list);
			}
			if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
				return $this->group_edit($parameter_list);
			}
			if ($user_command==$this->module_command."GET_TYPES"){
				return $this->get_group_types($this->check_parameters($parameter_list,0));
			}
			if ($user_command==$this->module_command."GET_ACCESS"){
				return $this->getAccess($parameter_list[0]);
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."SET_ACCESS_TO_PAGE"){
				return $this->set_group_access_to_page($parameter_list);
			}
			if ($user_command==$this->module_command."SELECT"){
				return $this->select_groups($parameter_list);
			}
			if ($user_command==$this->module_command."SELECTED"){
				return $this->selected_groups($parameter_list);
			}
			
			if ($user_command==$this->module_command."RETRIEVE_INFORMATION"){
				return $this->retrieve_group_information($parameter_list);
			}
			if ($user_command==$this->module_command."SET_BELONGING_TO_USER"){
				return $this->modify_group_belonging_to_user($parameter_list);
			}
			if ($user_command==$this->module_command."SET_DEFAULT_FOR_USER"){
				return $this->set_default_group($parameter_list);
			}
			if ($user_command==$this->module_command."GET_DEFAULT"){
				return $this->get_default_group($parameter_list);
			}
			if ($user_command==$this->module_command."CREATE_NEW_CLIENT_DETAILS"){
				return $this->create_client_details($parameter_list);
			}
			if ($user_command==$this->module_command."UPGRADE_FROM_SW_TO_MECM"){
				return $this->update_sitewizard_to_mecm();
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
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		$this->load_locale("group");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$this->module_admin_options = array(
				array("GROUP_ADD", "ADD_NEW","GROUP_CREATOR"),
				array("GROUP_LIST","LOCALE_LIST")
			);
			$this->module_admin_user_access = array(
				array("GROUP_ALL","COMPLETE_ACCESS"),
				array("GROUP_CREATOR","ACCESS_LEVEL_AUTHOR")
			);

		}
		if ($this->parent->server[LICENCE_TYPE]==MECM){
			$this->module_admin_options = array(
				array("GROUP_LIST","MANAGEMENT_GROUP")
			);
			$this->module_admin_user_access = array(
				array("GROUP_ALL","COMPLETE_ACCESS")
			);
		}
		$this->page_size =50;
	}
	function system_default_data($type=""){
		$sql = array();
		$i=0;
		if ($type=="TYPE"){
		$sql[$i++] = "INSERT INTO group_type (group_type_label) VALUES('LOCAL_GROUP_BASIC');";
		$sql[$i++] = "INSERT INTO group_type (group_type_label) VALUES('LOCAL_GROUP_ADMIN');";
		}
		return $sql;
	}
	
	function create_client_details($parameters){

		$client_identifier = $this->check_parameters($parameters,"client_identifier",-1);
		/**
		* Table data for table 'group_data'
		*/
		$this->call_command("DB_QUERY",array("INSERT INTO group_data (group_client, group_label, group_type, group_default) VALUES('$client_identifier', 'Registered',1,1);"));
		$this->call_command("DB_QUERY",array("INSERT INTO group_data (group_client, group_label, group_type, group_default) VALUES('$client_identifier', 'Administrator',2,0);"));
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$this->call_command("DB_QUERY",array("INSERT INTO group_data (group_client, group_label, group_type, group_default) VALUES('$client_identifier', 'Author',2,0);"));
			$this->call_command("DB_QUERY",array("INSERT INTO group_data (group_client, group_label, group_type, group_default) VALUES('$client_identifier', 'Approver',2,0);"));
			$this->call_command("DB_QUERY",array("INSERT INTO group_data (group_client, group_label, group_type, group_default) VALUES('$client_identifier', 'Level 1',1,0);"));
			$this->call_command("DB_QUERY",array("INSERT INTO group_data (group_client, group_label, group_type, group_default) VALUES('$client_identifier', 'Level 2',1,0);"));
		}
		$sql = "select * from group_data where group_client = $client_identifier and group_type=2;";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$list = Array();
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$list[$r["group_label"]] = $r["group_identifier"];
		}
		/**
		* Table data for table 'group_access'
		*/
		if ($this->check_parameters($list,"Administrator","__NOT_FOUND__")!="__NOT_FOUND__"){
			$this->call_command("DB_QUERY",array("INSERT INTO group_access (access_group,access_code) VALUES('".$list["Administrator"]."', 'ALL');"));
		}
		if ($this->check_parameters($list,"Author","__NOT_FOUND__")!="__NOT_FOUND__"){
			$this->call_command("DB_QUERY",array("INSERT INTO group_access (access_group,access_code) VALUES('".$list["Author"]."', 'PAGE_AUTHOR');"));
			$this->call_command("DB_QUERY",array("INSERT INTO group_access (access_group,access_code) VALUES('".$list["Author"]."', 'FILES_ALL');"));
		}
		if ($this->check_parameters($list,"Approver","__NOT_FOUND__")!="__NOT_FOUND__"){
			$this->call_command("DB_QUERY",array("INSERT INTO group_access (access_group,access_code) VALUES('".$list["Approver"]."', 'PAGE_PUBLISHER');"));
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
		array("group_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
		array("group_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
		array("group_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
		array("group_type"			,"unsigned small integer"	,"NOT NULL"	,"default '1'"),
		array("group_default"		,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="group_identifier";
		$tables[count($tables)] = array("group_data", $fields, $primary);
		/**
		* Table structure for table 'group_access'
		*/
		$fields = array(
		array("access_group"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
		array("access_code"			,"varchar(255)"				,"NOT NULL"	,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("group_access", $fields, $primary);
		
		/**
		* Table structure for table 'group_types'
		*/
		$fields = array(
		array("group_type_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
		array("group_type_label"		,"varchar(255)"				,"NOT NULL"	,"default ''")
		);
		$primary ="group_type_identifier";
		$tables[count($tables)] = array("group_type", $fields, $primary, $this->system_default_data("TYPE"));
		/**
		* Table data for table 'group'
		*/
		
		/**
		* Table structure for table 'menu_groups'
		*/
		$fields = array(
		array("group_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("menu_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="";
		$tables[count($tables)] = array("relate_menu_groups", $fields, $primary);
		/**
		* Table structure for table 'document_groups'
		*/
		$fields = array(
		array("group_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("document_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="";
		$tables[count($tables)] = array("relate_document_groups", $fields, $primary);
		/**
		* Table structure for table 'group_data'
		*/
		$fields = array(
		array("group_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("menu_identifer"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("user_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("client_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="";
		$tables[count($tables)] = array("group_admin_menu_access", $fields, $primary);
		/**
		* Table structure for table 'group_access_to_page'
		-
		* This is a relationship table. 
		-
		* This table is used to specify the groups that can see a particular version of a document
		* this table works with the version control software to mark the groups that can see each 
		* version, there fore if you roll back a document you will still have the groups that were
		* assigned to that version of the document in that lanugage
		*/
		$fields = array(
		array("group_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("trans_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("client_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="";
		$tables[count($tables)] = array("group_access_to_page", $fields, $primary);

		/**
		* Table structure for table 'groups_belonging_to_user'
		-
		* This is a relationship table. 
		-
		* This table is used to specify the groups that a user belongs to
		*/
		$fields = array(
		array("uid"					,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
		array("group_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("user_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		array("client_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="uid";
		$tables[count($tables)] = array("groups_belonging_to_user", $fields, $primary);
		/**
		* Table structure for table 'groups_containing_group'
		*
		* This is a relationship table. 
		*
		* This table is used to specify the groups that contain other groups
		*/
		$fields = array(
			array("uid"					,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("group_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("contains_group"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("client_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="uid";
		$tables[count($tables)] = array("groups_containing_group", $fields, $primary);
		/**
		* Table structure for table 'groups_to_object'
		*
		* This table is used to specify the objects that are contained in a group
		*/
		$fields = array(
			array("gto_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("gto_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("gto_object"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("gto_module"		,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("gto_rank"		,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="gto_identifier";
		$tables[count($tables)] = array("group_to_object", $fields, $primary);
		
		return $tables;
	}
	
	
	/**
	* get_groups()
	*/
	function get_groups($grouplevel=-1,$parameters = Array()){
		$type = $this->check_parameters($parameters,"return","ALL");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_groups()",__LINE__,"[$grouplevel]"));
		}
		if ($type=="ALL"){
			$where="";
		}else{
			$where = "and group_type=$type";
		}
		$sql = "Select * from group_data where group_data.group_client=$this->client_identifier $where";
		$result = $this->call_command("DB_QUERY",array($sql));
		$group_list = "";
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$group_list .="\t\t\t\t\t\t<option value=\"".$r["group_identifier"]."\"";
			if (!is_array($grouplevel)){
				if ($grouplevel==$r["group_identifier"]){
					$group_list .=" selected=\"true\"";
				}
			} else {
				$max = count($grouplevel);
				for ($index=0;$index<$max;$index++){
					if (" ".$grouplevel[$index]."," == " ".$r["group_identifier"].","){
						$group_list .=" selected=\"true\"";
					}
				}
			}
			$group_list .=">".$r["group_label"]."</option>\n";
			
		}
		$this->call_command("DB_FREE",array($result));
		return $group_list;
	}
	
	/**
	* get_groups_by_type()
	*/
	function get_groups_by_type($parameters,$check=""){
		$debug = $this->debugit(false,$parameters);
		$grouplevel = $parameters[0];
		$len_of_array = count($grouplevel);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_groups()",__LINE__,"[$grouplevel]"));
		}
		
		$sql = "Select * from group_data inner join group_type on group_type_identifier = group_type where group_data.group_client=$this->client_identifier order by group_type_label, group_label";
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$result = $this->call_command("DB_QUERY",array($sql));
		$g_type ="";
		$group_list = "";
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			if ($g_type!=$r["group_type"]){
				if ($g_type!=""){
					$group_list .="</options>";
				}
				eval("\$type = ".$r["group_type_label"].";");
				$group_list .="<options module=\"".$type."\">";
				$g_type=$r["group_type"];
			}
			$group_list .="\t\t\t\t\t\t<option value=\"".$r["group_identifier"]."\"";
			for ($index=0;$index<$len_of_array;$index++){
				if ($check=="OBJECT"){
					if ($grouplevel[$index]["IDENTIFIER"] == $r["group_identifier"]){
						$group_list .=" selected=\"true\"";
					}
				} else {
					if ($grouplevel[$index] == $r["group_identifier"]){
						$group_list .=" selected=\"true\"";
					}
				} 
			}
			$group_list .=">".$r["group_label"]."</option>\n";
		}
		if (strlen($group_list)>0){
			$group_list .="</options>";
		}
		$this->call_command("DB_FREE",array($result));
		return $group_list;
	}
	/**
	* get_groups_related_to_menu()
	*/
	function get_groups_related_to_menu($identifier){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_groups_related_to_menu()",__LINE__,"[$identifier]"));
		}
		$out = Array();
		$sql = "select * from relate_menu_groups where menu_identifier=$identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_groups_related_to_menu",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$out[count($out)] =$r["group_identifier"];
		}
		$this->call_command("DB_FREE",array($result));
		
		return $out;
		
	}
	
	function filter($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_filter",__LINE__,"[]"));
		}
		$command 		= $this->check_parameters($parameters,"command",0);
		$out = "\t\t\t\t<form name=\"filter_form\" label=\"".FILTER_RESULTS."\" method=\"get\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\"><![CDATA[$command]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" ><![CDATA[1]]></input>\n";
		$out .= "\t\t\t\t</form>";
		return $out;
	}

	function get_menus_related_to_group($group){
		if (is_array($group)){
			$grp_lst = "in (".join(",",$group).",-1)";
		} else {
			$grp_lst = " = ". $group;
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_menus_related_to_group()",__LINE__,"[$identifier]"));
		}
		$outtext = "";
		$sql = "select distinct * from relate_menu_groups where group_identifier $grp_lst";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_menus_related_to_group",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$count=0;
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			if ($count>0){
				$outtext .=", ";
			}
			$outtext .=$r["menu_identifier"];
			$count=1;
		}
		$this->call_command("DB_FREE",array($result));
		return $outtext;
	}
	
	function display_list($parameters){
		$end_page 		= 1;
		$page=$this->check_parameters($parameters,"page",1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_list",__LINE__,"[$page]"));
		}
		$orderby=0;
		


		$group_filter	= $this->check_parameters($parameters,"group_filter",0);
		$page			= $this->check_parameters($parameters,"page",1);
		$status			= $this->check_parameters($parameters,"status",$this->check_parameters($parameters,"identifier",-1));
		$order_filter	= $this->check_parameters($parameters,"order_filter",0);
		$filter_string	= str_replace(
							Array(" ","'"), 
							Array("%","&#39;"), 
							$this->check_parameters($parameters,"filter_string")
						  );
		$variables 		= array();	


		
		$sql = "SELECT 
	group_data.*, 
	group_type.group_type_label, 
	COUNT(groups_belonging_to_user.user_identifier) AS total 
FROM group_data 
	INNER JOIN group_type ON group_type.group_type_identifier = group_data.group_type 
	LEFT OUTER JOIN groups_belonging_to_user ON group_data.group_identifier = groups_belonging_to_user.group_identifier
WHERE 
	group_client=$this->client_identifier 
GROUP BY 
	group_data.group_identifier, 
	group_data.group_client, 
	group_data.group_label, 
	group_data.group_type, 
	group_type.group_type_label
";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}

			$number_of_records	= 0;
			$goto				= 0;
			$finish				= 0;
			$page				= 1;
			$num_pages			= 1;
			$start_page			= 1;
			$end_page			= 1;

			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			
			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				$variables["PAGE_BUTTONS"] = Array(
					Array("ADD",$this->module_command."ADD",ADD_NEW)
				);
			}else {
				$variables["PAGE_BUTTONS"] = Array();
			}
			
			/* Start Paging portion (Added by Muhammad Imran)*/

			$this->page_size =10;
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
			/* Start Paging portion (Added by Muhammad Imran)*/
			/* Comment by Muhammad Imran*/
/*
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["HEADER"]			= "Group Management List";
			$variables["as"]				= "table";
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			
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
			$variables["FILTER"]			= $this->filter($parameters);
*/			
			/* Comment by Muhammad Imran*/
			
			$variables["ENTRY_BUTTONS"] =Array(
				Array("EDIT",$this->module_command."EDIT",EDIT_EXISTING)
			
			);
			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				$variables["ENTRY_BUTTONS"][count($variables["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."REMOVE",REMOVE_EXISTING);
			}
			$variables["RESULT_ENTRIES"] =Array();
			
			/* To fix paging Comment and modified by Muhammad Imran */
//			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<10)){
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){//imran
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				eval("\$type = ".$r["group_type_label"].";");
				$total = $this->check_parameters($r,"total",0);
				$variables["RESULT_ENTRIES"][count($variables["RESULT_ENTRIES"])]=Array(
				"identifier"	=> $r["group_identifier"],
				"attributes"		=> Array(
					Array(ENTRY_GROUP_NAME,$r["group_label"],"TITLE","NO"),
					Array(ENTRY_GROUP_TYPE_LABEL,$type),
					Array(LOCALE_NUMBER_USERS_IN_GROUP,$total),
				)
				);
			}
			/**
			* start retrieve the page spanning information (Added By Muhammad Imran)
			*/
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["as"]	= "table";
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			$variables["START_PAGE"]		= $start_page;
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= $this->filter($parameters);
			/**
			* end retrieve the page spanning information (Added By Muhammad Imran)
			*/

			$out = $this->generate_list($variables);
		}
		
		return $out;
		
	}
	
	
	function group_edit($parameters){
		$identifier=$this->check_parameters($parameters,"identifier",-1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"group_edit()",__LINE__,"[$identifier]"));
		}
		$group_label="";
		$group_access="";
		$group_default =0;
		$group_access_to_all=0;
		$label="Add";
		if ($identifier>0){
			$label="Edit";
			$sql = "Select * from group_data where group_client=$this->client_identifier and group_identifier=$identifier";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"group_edit",__LINE__,"$sql"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($result){
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
				$group_label  = $r["group_label"];
				$group_default= $r["group_default"];
				$group_access = join($this->getAccess($identifier),"|");
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					$group_type   = $this->call_command("GROUP_GET_TYPES",array($r["group_type"]));
				}else{
					$group_type   = $r["group_type"];
				}
				$this->call_command("DB_FREE",array($result));
			}
			$group_menu  = split(", ",$this->call_command("GROUP_GET_MENUS",array($identifier)));
			$sql = "select * from group_access where access_group = $identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	if ($r["access_code"] == "ALL"){
					$group_access_to_all=1;
				}
            }
            $this->call_command("DB_FREE",Array($result));
		}else{
			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				$group_type = $this->call_command("GROUP_GET_TYPES");
			} else {
				$group_type =1;
			}
			$group_menu  = Array();
			
			$identifier="";
		}
		$menu_groups = $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($group_menu, "can_restrict_home_page"=>"1", "can_restrict_admin"=>0));
		$group_menu_joined = join (",",$group_menu);
		$out  = "\n\t\t<module name=\"groups\" display=\"form\">\n\t\t\t\n";
		$out .= "\t\t\t<page_options>";
		$out .= GROUP_BUTTON_BACK;
		$out .="<header><![CDATA[Group Management - $label]]></header></page_options>\n";
		
		$out .= "\t\t\t\t<form name=\"group_administration_form\" label=\"".ENTRY_ADMINISTER_GROUP."\" width=\"100%\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\" value=\"GROUP_SAVE\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"group_identifier\" value=\"$identifier\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"prev_group_menus\" value=\"$group_menu_joined\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"prev_group_admin_access\" value=\"$group_access\"/>\n";
		$out .= "<page_sections>";
		$out .= "<section label='Group Properties' name='Group Properties'>";
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$out .= "\t\t\t\t\t<select name=\"group_type\" label=\"".ENTRY_GROUP_TYPE."\">\n";
			$out .= "$group_type";
			$out .= "\t\t\t\t\t</select>\n";
		} else {
			$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"group_type\" value=\"$group_type\"/>\n";
		}
		$out .= "\t\t\t\t\t<input required='YES' type=\"text\" name=\"group_label\" label=\"".ENTRY_GROUP_NAME."\"><![CDATA[$group_label]]></input>\n";
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
				if ($group_default==1){
				$yes_selected =" selected =\"true\"";
				$no_selected  ="";
			}else{
				$yes_selected ="";
				$no_selected  =" selected =\"true\"";
			}
			$out .= "\t\t\t\t\t<radio name=\"group_default\" label=\"".ENTRY_GROUP_DEFAULT."\">
									<option value=\"1\" $yes_selected><![CDATA[".ENTRY_YES."]]></option>
									<option value=\"0\" $no_selected><![CDATA[".ENTRY_NO."]]></option>
								</radio>\n";
		}
		$list = $this->call_command("ENGINE_RETRIEVE",Array("EMBED_IN_GROUP_EDIT",$parameters));
		for($index=0,$max=count($list);$index<$max;$index++){
		$out.=	$list[$index][1];
		}
		$out .= "</section>";
		$out .= "<section label='Menu Restrictions' name='menu_options'>";
		$out .= "\t\t\t\t\t<checkboxes type=\"vertical\" name=\"menu_options\" label=\"".ENTRY_GROUP_MENU_ACCESS."\">";
		$out .= $menu_groups;
		$out .= "\t\t\t\t\t</checkboxes>\n";
		$out .= "</section>";
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$out .="<section name='group_access' label='Administrative Access Privigles'>";
			$access_to_group_array =$this->call_command("ENGINE_RETRIEVE",Array("ACCESS_DISPLAY_OPTIONS"));
			$access_to_groups="";
			for($index=0,$length_of_array=count($access_to_group_array);$index<$length_of_array;$index++){
				$access_to_groups .= $access_to_group_array[$index][1];
			}

			if ($group_access_to_all==1){
				$yes_selected =" selected =\"true\"";
				$no_selected  ="";
				$hidden=" hidden='YES'";
			}else{
				$yes_selected ="";
				$no_selected  =" selected =\"true\"";
				$hidden="";
			}
			$out .= "\t\t\t\t\t<radio name=\"group_complete_access\" label=\"".ENTRY_GROUP_COMPLETE_ACCESS_TO_ALL_MODULES."\" onclick=\"group_toggle_access\">
									<option value=\"1\" $yes_selected><![CDATA[".ENTRY_YES."]]></option>
									<option value=\"0\" $no_selected><![CDATA[".ENTRY_NO."]]></option>
								</radio>\n";
			$out .= "\t\t\t\t\t<checkboxes $hidden type=\"horizontal\" name=\"group_access\" label=\"".ENTRY_GROUP_ADMIN_PRIVILEGES."\" onclick=\"check\">";
			$out .= $access_to_groups;
			$out .= "\t\t\t\t\t</checkboxes>\n";
			$out .="</section>";
		} else {
			$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"group_access\" value=\"__IGNORE__\"/>\n";
		}
		$out .="</page_sections>";
		$out .= "\t\t\t\t\t<input iconify=\"SAVE\" type=\"submit\" name=\"\" value=\"".SAVE_DATA."\"/>\n";
		$out .= "\t\t\t\t</form>\n</module>\n";
		
		return $out;
	}
	
	function get_group_types($group_type=""){
		$sql = "Select * from group_type";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_group_type",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$out = "";
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$out  .= "<option value='".$r["group_type_identifier"]."'";
				if ($group_type==$r["group_type_identifier"]){
					$out .= " selected='true'";
				}
				$out .= ">".$this->get_constant($r["group_type_label"])."</option>";
			}
			$this->call_command("DB_FREE",array($result));
		}
		return $out;
	}
	
	function get_menu_list($group_identifier=-1){
		$outtext = "";
		
		$sql_menus = "select * from menu_data where menu_client=$this->client_identifier";
		$sql_group = "select * from relate_menu_groups where group_identifier=$group_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations",__LINE__,"$sql"));
		}
		$group_result = $this->call_command("DB_QUERY",array($sql_group));
		$menu_result = $this->call_command("DB_QUERY",array($sql_menus));
		
		
		$group_access = Array();
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($group_result))){
			$group_access[count($group_access)] = $r["menu_identifier"];
		}
		if ($group_result){
			$this->call_command("DB_FREE",array($group_result));
		}
		$length_of_array = count($group_access);
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($menu_result))){
			$outtext .="\t\t\t\t\t\t<option value=\"".$r["menu_identifier"]."\"";
			for ($index=0;$index<$length_of_array;$index++){
				if ($group_access[$index]==$r["menu_identifier"]){
					$outtext .=" selected=\"true\"";
				}
			}
			$outtext .=">".$r["menu_label"]."</option>\n";
		}
		if ($menu_result){
			$this->call_command("DB_FREE",array($menu_result));
		}
		
		return $outtext;
		
	}
	
	function save($parameter_array){
		$debug = $this->debugit(false,$parameter_array);
		$error=0;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations",__LINE__,$parameter_array));
		}
		
		$table = array(
		"group_type"  => $this->check_parameters($parameter_array,"group_type"),
		"group_label" => trim($this->strip_tidy($this->check_parameters($parameter_array,"group_label")))
		);
		$commands_to_execute = $this->check_parameters($parameter_array,"modules",Array());
		$identifier		= $this->check_parameters($parameter_array,"group_identifier",-1);
		if ($identifier==""){
			$identifier = -1;
		}
		$sql = "select * from group_data where group_label ='".$table["group_label"]."' and group_client = $this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$existing_gid = -1;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$existing_gid = $r["group_identifier"];
        }
        $this->call_command("DB_FREE",Array($result));
		if ($identifier==-1){
			//add
			if ($existing_gid == -1){
				if ($parameter_array["group_type"]==1){
					$group_access= "";
				}
				$group_default= $this->check_parameters($parameter_array,"group_default",0);
				if ($group_default==1){
					$sql = "update group_data set group_default=0 where group_client=$this->client_identifier and group_type=1";
					$result = $this->call_command("DB_QUERY",array($sql));
				}
				$sql ="insert into group_data (group_type,group_label,group_client, group_default) values ('".$table["group_type"]."','".$table["group_label"]."',$this->client_identifier, $group_default)";
				$result = $this->call_command("DB_QUERY",array($sql));
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations",__LINE__,$sql));
				}
				$sql = "select * from group_data where group_type='".$table["group_type"]."' and group_label='".$table["group_label"]."' and group_client=$this->client_identifier";
				$result = $this->call_command("DB_QUERY",array($sql));
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations",__LINE__,$sql));
				}
				if ($result){
					$r = $this->call_command("DB_FETCH_ARRAY",array($result));
					$group_identifier = $r["group_identifier"];
					if ($result){
						$this->call_command("DB_FREE",array($result));
					}
				}
				$parameter_array["group_identifier"] = $group_identifier;
				for( $index = 0, $max = count($commands_to_execute) ; $index < $max;$index++){
					$this->call_command($commands_to_execute[$index],$parameter_array);
				}
			} else {
				$error=1;
			}
		} else{
			// edit
			if ($existing_gid == $identifier || $existing_gid == -1){
				$group_default =0;
				if ($parameter_array["group_type"]==1){
					$group_access= "";
					$group_default= $parameter_array["group_default"];
					if ($group_default==1){
						$sql = "update group_data set group_default=0 where group_client=$this->client_identifier and group_type=1";
						$result = $this->call_command("DB_QUERY",array($sql));
					}
				}
				$sql ="update group_data set group_type='".$table["group_type"]."', group_label='".$table["group_label"]."', group_default='$group_default' where group_client=$this->client_identifier and group_identifier=".$parameter_array["group_identifier"];
				$result = $this->call_command("DB_QUERY",array($sql));
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations",__LINE__,$sql));
				}
				$group_identifier = $parameter_array["group_identifier"];
				for( $index = 0, $max = count($commands_to_execute) ; $index < $max;$index++){
					$this->call_command($commands_to_execute[$index],$parameter_array);
				}
			} else {
				$error=1;
			}
		}
		if ($error==0){
			$num = $this->check_parameters($parameter_array,"totalnumberofchecks_group_access",0);
			
			$ga = $this->check_parameters($parameter_array,"group_access",Array());
			$group_complete_access = $this->check_parameters($parameter_array,"group_complete_access",0);
	//		print_r()
			if ($ga!="__IGNORE__"){
				$sql= "delete from group_access where access_group=".$group_identifier."";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations",__LINE__,$sql));
				}
				$result = $this->call_command("DB_QUERY",array($sql));
				if ($group_complete_access==1){
					$sql= "insert into group_access (access_group,access_code) values (".$group_identifier.",'ALL')";
					if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
					$this->call_command("DB_QUERY",array($sql));
				} else {
					$group_access_array=Array();
					for ($n=1;$n <= $num;$n++){
						$ga = $this->check_parameters($parameter_array,"group_access_$n",Array());
						$length_of_array = count($ga);
						for ($index=0;$index<$length_of_array;$index++){
							if (strpos($ga[$index],"|")>-1){
								$list = split("\|",$ga[$index]);
								for ($list_index=0,$list_length_of_array=count($list);$list_index<$list_length_of_array;$list_index++){
									$group_access_array[count($group_access_array)]=$list[$list_index];
								}
							}else{
								$group_access_array[count($group_access_array)]=$ga[$index];
							}
						}
					}
					for ($index=0,$length_of_array=count($group_access_array);$index<$length_of_array;$index++){
						$sql= "insert into group_access (access_group,access_code) values (".$group_identifier.",'".$group_access_array[$index]."')";
						if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
						$this->call_command("DB_QUERY",array($sql));
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations",__LINE__,$sql));
						}
					}
				}
			}
			$test="";
			if(isset($parameter_array["menu_options"])){
				if (count($parameter_array["menu_options"])>0){
					$test = join($parameter_array["menu_options"],", ");
				}else{
					$test = $parameter_array["menu_options"];
				}
				if ($parameter_array["prev_group_menus"]!=$test){
					$sql= "delete from  relate_menu_groups where group_identifier=".$group_identifier;
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations",__LINE__,$sql));
					}
					$result = $this->call_command("DB_QUERY",array($sql));
					for ($index=0,$length_of_array=count($parameter_array["menu_options"]);$index<$length_of_array;$index++){
						$sql= "insert into relate_menu_groups (group_identifier,menu_identifier) values (".$group_identifier.",".$parameter_array["menu_options"][$index].")";
						$result = $this->call_command("DB_QUERY",array($sql));
					}
				}
			}
			if ($debug) $this->exitprogram();
			return 1;
		} else {
			$out  = "\t\t\t<module name=\"$this->module_name\" display=\"form\">\n";
			$out .= "\t\t\t\t<form name='associated_form' label='".LOCALE_GROUP_SELECTION."' method='get'>\n";
			$out .= "<text><![CDATA[<p>Sorry you have choosen an existing group name</p><p><a href='javascript:history.back();'>Return to the previous screen</a></p>]]></text>";
			$out .= "</form>";
			
			$out .= "\t\t\t</module>\n";	
			return $out;	
		}
	}
	
	function remove($parameter_array){
		
		if ($parameter_array["identifier"]!=""){
			//add
			$sql ="delete from group_data where group_identifier=".$parameter_array["identifier"]." and group_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			
			$sql= "delete from relate_menu_groups where group_identifier=".$parameter_array["identifier"];
			$result = $this->call_command("DB_QUERY",array($sql));
		}
		return 1;
	}
	
	function getAccess($group){
		if (is_array($group)){
			$grp_lst = "in (".join(",",$group).",-1)";
		} else {
			$grp_lst = " = ". $group;
		}
		$result_array=Array();
		$sql = "Select * from group_access where access_group $grp_lst";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$result_array[count($result_array)]=$r["access_code"];
		}
		return $result_array;
	}
	
	function set_group_access_to_page($parameters){
		$translation_identifier = $this->check_parameters($parameters,"trans_id",-1);
		$groups		 			= $this->check_parameters($parameters,"group_list");
		
		
		$group_list = split(",",join("",split(" ",$groups)));
		if ($translation_identifier>-1){
			$sql = "delete * from group_access_to_page where client_identifier=$this->client_identifier and trans_identifier=$translation_identifier";
			if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations",__LINE__,$sql));
			}
			for($index=0,$max=count($group_list);$index<$max;$index++){
				if (!empty($group_list[$index])){
					$sql = "insert into group_access_to_page (client_identifier, trans_identifier, group_identifier) values ($this->client_identifier, $translation_identifier, ".$group_list[$index].")";
					$this->call_command("DB_QUERY",Array($sql));	
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations",__LINE__,$sql));
					}			
				}
			}
		}
	}
	
	function select_groups($parameters){
		$grps = trim($this->check_parameters($parameters,"page_groups",$this->check_parameters($parameters,"associated_list",-1)));
		$grps = split(",",join("",split(" ",$grps)));
		$out  = "\t\t\t<module name=\"$this->module_name\" display=\"form\">\n";
		$out .= "\t\t\t\t<form name='associated_form' label='".LOCALE_GROUP_SELECTION."' method='get'>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"associated_list\" value=\"".$this->check_parameters($parameters,"associated_list")."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"return_hidden\" value=\"".$this->check_parameters($parameters,"return_hidden")."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"return_note\" value=\"".$this->check_parameters($parameters,"return_note")."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\" value=\"".$this->check_parameters($parameters,"return_command")."\"/>\n";
		$out .= "\t\t\t\t\t<checkboxes type='vertical' label='".LOCALE_LIST_OF_GROUPS_AVAILABLE_TO_SEE."' name='file_list' onclick='a'><options label='LOCALE_LIST_OF_AVAILABLE_GROUPS'>".$this->get_groups($grps)."</options></checkboxes>\n";
		$out .= "\t\t\t\t\t<input type='submit' iconify='SAVE' value='".SAVE_DATA."'/>\n";
		$out .= "\t\t\t\t</form>\n";
		$out .= "\t\t\t</module>\n";	
		return $out;	
	}
	function selected_groups($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$return_note		= $this->check_parameters($parameters,"return_note",-1);
		$return_hidden		= $this->check_parameters($parameters,"return_hidden",-1);
		$associations		= $this->check_parameters($parameters,"associated_list",-1);
		$file_list			= $this->check_parameters($parameters,"file_list",-1);
		$out ="<module name=\"$this->module_name\" display=\"data\">";
		$out.="<hidden><![CDATA[$return_hidden]]></hidden>";
		$out.="<note><![CDATA[$return_note]]></note>";
//		$out.="<list><![CDATA[$associations]]></list>";
		$out.="<list><![CDATA[ ".join(", ",$file_list).",]]></list>";
		
		$out .= "<groups>";
//		$out .= $this->get_groups($associations);
		$out .= $this->get_groups($file_list);
		$out .= "</groups></module>";
		
		return $out;	
	}
	
	
	function modify_group_contains_group($parameters){
		$group_id = $this->check_parameters($parameters,"group_identifier",-1);
		$group_list = $this->check_parameters($parameters,"group_list",array());
		if ($group_id>-1){
			$sql = "delete * from groups_containing_group where client_identifier=$this->client_identifier and group_identifier = $group_id";
			$result = $this->call_command("DB_QUERY",array($sql));
			$max = count($group_list);
			for ($index=0;$max <$index;$index++){
				$sql = "insert into groups_containing_group (group_identifier, client_identifier, contains_group) values ($group_id, $this->client_identifier, ".$group_list[$index].")";
				$result = $this->call_command("DB_QUERY",array($sql));
			}
		}
	}

	function modify_group_belonging_to_user($parameters){
		$user_id = $this->check_parameters($parameters,"user_identifier",-1);
		$group_list = $this->check_parameters($parameters,"group_list",array());
		$module = $this->check_parameters($parameters,"module","");
		$max = count($group_list);
		if ($max>0){
			$sql = "delete from groups_belonging_to_user where client_identifier=$this->client_identifier and user_identifier = $user_id";
			$result = $this->call_command("DB_QUERY",array($sql));
			$sql = "delete from group_to_object where gto_object=$user_id and gto_client=$this->client_identifier and gto_module='$module'";
			$this->call_command("DB_QUERY",Array($sql));
			for ($index=0;$index<$max ;$index++){
				$sql = "insert into groups_belonging_to_user (user_identifier, client_identifier, group_identifier) values ($user_id, $this->client_identifier, ".$group_list[$index].")";
				$result = $this->call_command("DB_QUERY",array($sql));
				$sql = "insert into group_to_object (gto_identifier, gto_object, gto_client, gto_module, gto_rank) values  (".$group_list[$index].", $user_id, $this->client_identifier, '$module',0)";
				$this->call_command("DB_QUERY",Array($sql));
			}
		}
	}
	
	

	function retrieve_group_information($parameters){
		$user_id = $this->check_parameters($parameters,"user_identifier",$_SESSION["SESSION_USER_IDENTIFIER"]);
		
		$sql = "select * from groups_belonging_to_user where client_identifier=$this->client_identifier and user_identifier = $user_id";
		$result = $this->call_command("DB_QUERY",array($sql));
		$this->groups_returned_array = Array();
		$this->list_of_groups_returned ="";
		$grp = Array();
		while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$grp[count($grp)] = $r["group_identifier"];
		}
		$this->extract_group_information(Array("group_list" => $grp));
		return $this->groups_returned_array;
	}

	function extract_group_information($parameters){
		$group_list = join(",",$this->check_parameters($parameters,"group_list",-1));
		
			$sql = "select * from group_data
					where group_client=$this->client_identifier and group_data.group_identifier in ($group_list)";
			$result = $this->call_command("DB_QUERY",array($sql));
			if($result){
				while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$pos = count($this->groups_returned_array);
					$this->groups_returned_array[$pos] = Array(
						"IDENTIFIER" 	=> $r["group_identifier"], 
						"LABEL" 		=> $r["group_label"], 
						"TYPE" 			=> $r["group_type"], 
						"ACCESS" 		=> $this->getAccess(Array($r["group_identifier"]))
					);
					$this->list_of_groups_returned .= "[".$r["group_identifier"]."]";
				}
			}
	}
	
	function set_default_group($parameters){
		$user = $this->check_parameters($parameters,"identifier",-1);
		if ($user!=-1){
			$sql = "select * from group_data where group_client = $this->client_identifier and group_default=1 and group_type=1";
			$result  = $this->call_command("DB_QUERY",array($sql));
			$group_identifier = 0;
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$group_identifier = $r["group_identifier"];
			}
			$this->modify_group_belonging_to_user(array("user_identifier"=>$user,"group_list"=>Array($group_identifier)));
		}
	}
	function get_default_group(){
		$grp  = array();
		$sql = "select * from group_data where group_client = $this->client_identifier and group_default=1 and group_type=1";
		$result  = $this->call_command("DB_QUERY",array($sql));
		$group_identifier = 0;
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$grp[count($grp)] = Array(
				"IDENTIFIER" 	=> $r["group_identifier"], 
				"LABEL" 		=> $r["group_label"], 
				"TYPE" 			=> $r["group_type"], 
				"ACCESS" 		=> $this->getAccess(Array($r["group_identifier"]))
			);
		}
		return $grp;
	}
	
	function update_sitewizard_to_mecm(){
		$sql ="select * from group_data where group_client = $this->client_identifier";
        $result  = $this->call_command("DB_QUERY",Array($sql));
		$grps = Array();
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$grps[count($grps)] = Array(
				"id" => $r["group_identifier"],
				"type" => $r["group_type"],
				"label" => $r["group_label"]
			);	
        }
		$this->call_command("DB_FREE",Array($result));
		$author		= 0;
		$approver	= 0;
		$admin		= 0;
		$reg1		= 0;
		$reg2		= 0;
		$reg3		= 0;
		$regc		=0;
		$m			= count($grps);
		for($i=0;$i<$m;$i++){
			if($grps[$i]["type"]==2){
				if($grps[$i]["label"]=="Administrator"){
					$admin=1;
				}
				if($grps[$i]["label"]=="Author"){
					$author=1;
				}
				if($grps[$i]["label"]=="Approver"){
					$approver=1;
				}
			}
			if($grps[$i]["type"]==1){
				if($grps[$i]["label"]=="Registered"){
					$reg1=1;
				} else if($reg2==0){
					$reg2=1;
				}else if($reg3==0){
					$reg3=1;
				}
			}
		}
		if($author==0){
			$sql = "insert into group_data (group_label,group_type,group_client, group_default) values ('Author', 2, $this->client_identifier, 0 )";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "select * from group_data where group_client = $this->client_identifier and group_label='Author' and group_type=2";
            $result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$grp_id = $r["group_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			$sql ="INSERT INTO group_access (access_group, access_code) VALUES ($grp_id, 'FILES_ALL')";
			$this->call_command("DB_QUERY",Array($sql));
			$sql ="INSERT INTO group_access (access_group, access_code) VALUES ($grp_id, 'PAGE_AUTHOR')";
			$this->call_command("DB_QUERY",Array($sql));
		} 
		if($approver==0){
			$sql = "insert into group_data (group_label,group_type,group_client, group_default) values ('Approver', 2, $this->client_identifier, 0 )";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "select * from group_data where group_client = $this->client_identifier and group_label='Author' and group_type=2";
            $result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$grp_id = $r["group_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			$sql ="INSERT INTO group_access (access_group, access_code) VALUES ($grp_id, 'PAGE_PUBLISHER')";
			$this->call_command("DB_QUERY",Array($sql));
		} 
		if($reg2==0){
			$sql = "insert into group_data (group_label,group_type,group_client, group_default) values ('Group 1', 1, $this->client_identifier, 0 )";
			$this->call_command("DB_QUERY",Array($sql));
		} 
		if($reg3==0){
			$sql = "insert into group_data (group_label,group_type,group_client, group_default) values ('Group 2', 1, $this->client_identifier, 0 )";
			$this->call_command("DB_QUERY",Array($sql));
		}
		
		/*
		INSERT INTO group_access (access_group, access_code) VALUES (82, 'FILES_ALL')
		*/
	}

	/**
	* get the list of groups and select any that have the specified object as a member
	*
	* @param Array keys are "object" Integer and "module" String
	* @return String option tags
	*/
	function get_groups_to_object($parameters){
		$object			 = $this->check_parameters($parameters, "object", -1);
		$module			 = $this->check_parameters($parameters, "module", "");
		if ($module==""){
			return "";
		}
		$sql = "Select * from group_data 
					inner join group_type on group_type_identifier = group_type 
					left outer join group_to_object on gto_identifier=group_identifier and gto_client=group_client and gto_object=$object and gto_module ='$module'
				where group_data.group_client=$this->client_identifier order by group_type_label, group_label";
		$result = $this->call_command("DB_QUERY",array($sql));
		$g_type ="";
		$group_list = "";
		$gdata = Array();
		$set_default=1;
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			if ("__NOT_FOUND__" == $this->check_parameters($gdata,$r["group_type_label"],"__NOT_FOUND__")){
				$gdata[$r["group_type_label"]] = Array();
			}
			$gdata[$r["group_type_label"]][count($gdata[$r["group_type_label"]])] = Array(
				"label" 	=> $r["group_label"], 
				"default"	=> $r["group_default"],
				"identifier"=> $r["group_identifier"],
				"selected"	=> $this->check_parameters($r,"gto_object",0)
			);
			if($this->check_parameters($r,"gto_object",0)>0){
				$set_default=0;
			}
		}
		$this->call_command("DB_FREE",array($result));
		foreach($gdata as $key =>$list){
			$type 		 = $this->get_constant($key);
			$group_list .="<options module=\"".$type."\">";
			$m=count($gdata[$key]);
			for($i=0;$i<$m;$i++){
				$group_list .="<option value=\"".$gdata[$key][$i]["identifier"]."\"";
				if ($gdata[$key][$i]["selected"]!=0 || ($set_default==1 && $gdata[$key][$i]["default"]==1)){
					$group_list .=" selected=\"true\"";
				}
				$group_list .=">".$gdata[$key][$i]["label"]."</option>";
			}
			$group_list .="</options>";
		}
		return $group_list;
	}
	
	/**
	* manage the groups that belong to an object
	*
	* @param Array keys are "object" Integer and "module" String
	* @return String option tags
	*/
	function set_groups_to_object($parameters){
		$object			 = $this->check_parameters($parameters, "object", -1);
		$module			 = $this->check_parameters($parameters, "module", "");
		$params			 = $this->check_parameters($parameters, "params", Array());
		$tnoc_grps		 = $this->check_parameters($params, "totalnumberofchecks_group_list",0);
		$list=Array();
		for($i=1;$i<=$tnoc_grps;$i++){
			$grp=$this->check_parameters($params, "group_list_".$i, Array());
		    for($c=0;$c<count($grp);$c++){
				$list[count($list)] = $grp[$c];
			}
		}
		if ($module==""){
			return "";
		}
		$sql = "delete from group_to_object where gto_client=$this->client_identifier and gto_object=$object and gto_module ='$module'";
		$this->call_command("DB_QUERY",array($sql));
		for($i=0;$i<count($list);$i++){
			$sql = "insert into group_to_object (gto_client, gto_object, gto_module, gto_identifier, gto_rank) values ($this->client_identifier, $object, '$module', '".$list[$i]."', 0)";
			$this->call_command("DB_QUERY",array($sql));
		}
	}
	

}
?>