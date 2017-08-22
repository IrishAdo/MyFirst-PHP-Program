<?PHP
/*************************************************************************************************************************
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.banner_admin.php
* @date 08 Nov 2004
*************************************************************************************************************************/
/*************************************************************************************************************************
* This module is the administration module for the Banner manager
*************************************************************************************************************************/
class banner_admin extends module{
	/*************************************************************************************************************************
	*  Class Variables (generic
	*************************************************************************************************************************/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_name_label			= "Banner Manager (Administration)";
	var $module_name				= "banner_admin";
	var $module_admin				= "1";
	var $module_command				= "BANNERADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "BANNER_";
	var $module_label				= "MANAGEMENT_BANNERS";
	var $module_modify		 		= '$Date: 2005/02/21 16:00:24 $';
	var $module_version 			= '$Revision: 1.7 $';
	var $module_creation 			= "27/01/2005";
	/*************************************************************************************************************************
    * Available Roles
    *************************************************************************************************************************/
	var $admin_access				= 0;
	var $manage_banners				= 0;
	var $manage_types				= 0;
	var $manage_banner_groups		= 0;
	var $view_logs					= 0;
	/*************************************************************************************************************************
	* Management Menu entries
	*************************************************************************************************************************/
	var $module_admin_options 		= array(
//		array("BANNERADMIN_MANAGE_TYPES_LIST", LOCALE_BANNER_MANAGE_TYPES, "BANNERADMIN_MANAGE_TYPES", "Interactive tools/Banner Manager"),
		array("BANNERADMIN_LIST", LOCALE_BANNER_LIST_GROUPS, "BANNERADMIN_ACCOUNT_ADMIN", "Interactive tools/Banner Manager"),
		array("BANNERADMIN_MANAGE_BANNER_LIST", LOCALE_BANNER_LIST, "BANNERADMIN_BANNER_ADMIN", "Interactive tools/Banner Manager")
	);
	
	/*************************************************************************************************************************
	*  Group access Restrictions, restrict a group to these command sets
	*************************************************************************************************************************/
	var $module_admin_user_access = array(
		array("BANNERADMIN_ALL",			"COMPLETE_ACCESS"),
		array("BANNERADMIN_BANNER_ADMIN",	"LOCALE_CAN_MANAGE_BANNERS"),
		array("BANNERADMIN_ACCOUNT_ADMIN",	"LOCALE_CAN_MANAGE_LIST"),
		array("BANNERADMIN_MANAGE_TYPES",	"LOCALE_CAN_MANAGE_TYPES"),
		array("BANNERADMIN_VIEW_LOGS",		"LOCALE_CAN_VIEW_LOGS")
	);

	/*************************************************************************************************************************
	*  Channel options
	*************************************************************************************************************************/
	var $module_display_options 	= array();
	
	/*************************************************************************************************************************
	* SPECIAL PAGES
	*
	* Each special page will call a specific function as defined here
	*************************************************************************************************************************/
	var $specialPages			 	= array(
		array("_clickme.php"			,"BANNER_CLICK"			,"HIDDEN", "")
	);
	/*************************************************************************************************************************
	*  filter options
	*************************************************************************************************************************/
	var $display_options			= array();
	
	/*************************************************************************************************************************
	*  Class Methods
	*************************************************************************************************************************/
	
	function command($user_command, $parameter_list = array()){
		/*************************************************************************************************************************
		* If debug is turned on then output the command sent and the parameter list too.
		*************************************************************************************************************************/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,print_r($parameter_list,true),__LINE__,"command"));
		}
		
		/*************************************************************************************************************************
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*************************************************************************************************************************/
		if (strpos($user_command, $this->module_command)===0){
			/*************************************************************************************************************************
			* Generic module functions
			*************************************************************************************************************************/
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
			if ($user_command==$this->module_command."SPECIAL_PAGES"){
				return $this->specialPages;
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			/*************************************************************************************************************************
			* Create table function allow access if in install mode
			*************************************************************************************************************************/
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			/*************************************************************************************************************************
			* Specific Module commands
			*************************************************************************************************************************/
			if($this->admin_access==1){
				if($this->manage_banner_groups==1){
					/*************************************************************************************************************************
                    * manage lists of banners
                    *************************************************************************************************************************/
					if ($user_command==$this->module_command."LIST"){
						return $this->module_list($parameter_list);
					}
					if ($user_command==$this->module_command."MANAGE_GROUPING_MODIFY"){
						return $this->modify_list_group($parameter_list);
					}
					if ($user_command==$this->module_command."MANAGE_GROUPING_REMOVE"){
						return $this->remove_list_group($parameter_list);
					}
					if ($user_command==$this->module_command."MANAGE_GROUPING_SAVE"){
						$this->modify_list_group_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST"));
					}
					if ($user_command==$this->module_command."MANAGE_GROUPING_MODIFY_SAVE_TAB"){
						$id = $this->modify_list_group_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."LIST"));
						//$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."MANAGE_GROUPING_MODIFY&amp;identifier=$id"));
					}
				}
				if($this->manage_banners==1){
					/*************************************************************************************************************************
                    * manage individual banners
					*************************************************************************************************************************/
					if ($user_command==$this->module_command."MANAGE_BANNER_LIST"){
						return $this->module_manage_banner_list($parameter_list);
					}
					if ($user_command==$this->module_command."MANAGE_BANNER_REMOVE"){
						$this->module_manage_banner_remove($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."MANAGE_BANNER_LIST"));
					}
					if ($user_command==$this->module_command."MANAGE_BANNER"){
						return $this->module_manage_banner($parameter_list);
					}
					if ($user_command==$this->module_command."MANAGE_BANNER_SAVE"){
						$this->module_manage_banner_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."MANAGE_BANNER_LIST"));
					}
				}
				/*************************************************************************************************************************
                * manage types of banner
				*
				* Text, Small , Medium, Large
                *************************************************************************************************************************/
				if($this->manage_types==1){
					if ($user_command==$this->module_command."MANAGE_TYPES_MODIFY"){
						return $this->module_manage_types($parameter_list);
					}
					if ($user_command==$this->module_command."MANAGE_TYPES_REMOVE"){
						$this->module_manage_types_remove($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."MANAGE_TYPES_LIST"));
					}
					if ($user_command==$this->module_command."MANAGE_TYPES_SAVE"){
						$this->module_manage_types_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."MANAGE_TYPES_LIST"));
					}
					if ($user_command==$this->module_command."MANAGE_TYPES_LIST"){
						return $this->module_list_types($parameter_list);
					}
					
				}
				if($this->view_logs==1){
					if ($user_command==$this->module_command."VIEW_LOG_WEEK"){
						return $this->display_banner_group_log($parameter_list);
					}
				}
			}
		}
		return "";
	}
	/*************************************************************************************************************************
	*                               B A N N E R   A D M I N   M A N A G E R   F U N C T I O N S
	*************************************************************************************************************************/
	
	/*************************************************************************************************************************
	* Initialise function
	* This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	* module::initialise() function allowing you to define any extra constructor
	* functionality.
	*************************************************************************************************************************/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		/*************************************************************************************************************************
		* request the client identifier once we use this variable often
		*************************************************************************************************************************/
		$this->client_identifier = $this->parent->client_identifier;
		/*************************************************************************************************************************
		* load the required locale
		*************************************************************************************************************************/
		$this->load_locale($this->module_name);
		/*************************************************************************************************************************
		* define the list of Editors in this module and define them as empty
		*************************************************************************************************************************/
		$this->editor_configurations = Array();
		/*************************************************************************************************************************
		* request the page size 
		*************************************************************************************************************************/
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		/*************************************************************************************************************************
		* define the admin access that this user has.
		*************************************************************************************************************************/
		$this->admin_access			= 0;
		$this->manage_banners		= 0;
		$this->manage_types			= 0;
		$this->manage_banner_groups = 0;
		$this->view_logs 			= 0;
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
/*
		$sql = "select * from banner_types where bt_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$this->module_admin_options[count($this->module_admin_options)] = array("BANNERADMIN_MANAGE_BANNER&amp;type=".$r["bt_identifier"], "Add ".$r["bt_label"], "BANNERADMIN_BANNER_ADMIN", "Interactive tools/Banner Manager");
        }
        $this->parent->db_pointer->database_free_result($result);
*/


		/*************************************************************************************************************************
		* define the admin access that this user has.
		*************************************************************************************************************************/
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$length_of_array=count($access);
			for ($index=0;$index<$length_of_array;$index++){
				if (($this->module_command."ALL"==$access[$index]) || ("ALL"==$access[$index])){
					$this->admin_access				= 1; // manage the database list
					$this->manage_banners			= 1; // manage the database list
					$this->view_logs		 		= 1;
					$this->manage_types				= 1;
					$this->manage_banner_groups 	= 1;
				} else {
					if ("BANNERADMIN_BANNER_ADMIN"==$access[$index]){
						$this->admin_access			= 1;
						$this->manage_banners		= 1;
					}
					if ("BANNERADMIN_ACCOUNT_ADMIN"==$access[$index]){
						$this->admin_access			= 1;
						$this->manage_banner_groups	= 1;
					}
					if ("BANNERADMIN_MANAGE_TYPES"==$access[$index]){
						$this->admin_access			= 1;
						$this->manage_types		 	= 1;
					}
					if ("BANNERADMIN_VIEW_LOGS"==$access[$index]){
						$this->admin_access			= 1;
						$this->view_logs		 	= 1;
					}
				}
			}
		}
		return 1;
	}
	/*************************************************************************************************************************
	* function produces abstract structure of modules db structure
	*
    * used to generate the table structure required abstract function that passes an abstract
	* representation of the desired table structure to the proper database module which will
	* interpet the abstract and convert it into a valid SQL Create table structure.
	*
	* @return Array list of abstract table definitions for this module
	*************************************************************************************************************************/
	function create_table(){
		$tables = array();
		/*************************************************************************************************************************
		* Table structure for table 'banner_list'
		*************************************************************************************************************************/
		$fields = array(
			array("bl_identifier"			,"unsigned integer"			,"NOT NULL"	,"default '0'" ,"key"),
			array("bl_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("bl_label"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("bl_status"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("bl_homepage_exception"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("bl_type"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("bl_open_in_window"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"), // 0 = No, 1 = Yes open in new window
			array("bl_direction"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"), // 0 = horizontal, 1 = Vertical
			array("bl_number_to_display"	,"unsigned small integer"	,"NOT NULL"	,"default '1'") // number between 1 and 4 banners
		);
		$primary ="bl_identifier";
		$tables[count($tables)] = array("banner_list", $fields, $primary);
		
		/*************************************************************************************************************************
		* Table structure for table 'banner_log'
		*************************************************************************************************************************/
		$fields = array(
			array("bnrlog_day"		,"datetime"					,"NOT NULL"	,"default ''","key"),
			array("bnrlog_banner"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("bnrlog_client"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("bnrlog_pages"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("bnrlog_clicks"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("bnrlog_format"	,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="";
		$tables[count($tables)] = array("banner_log", $fields, $primary);
		/*************************************************************************************************************************
		* Table structure for table 'banner_types'
		*************************************************************************************************************************/
		$fields = array(
			array("bt_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
	  		array("bt_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("bt_format"			,"unsigned integer"			,"NOT NULL"	,"default '0'"), // 0 = Image, 1 = flash, 2 = text
	  		array("bt_width"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("bt_height"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("bt_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
	  		array("bt_description"		,"text"						,"NOT NULL"	,"default ''")
		);
		$primary ="bt_identifier";
		$tables[count($tables)] = array("banner_types", $fields, $primary);
		/*************************************************************************************************************************
		* Table structure for table 'banner_entry'
		*************************************************************************************************************************/
		$fields = array(
			array("be_identifier"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
	  		array("be_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
	  		array("be_label"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
	  		array("be_status"				,"unsigned small integer"	,"NOT NULL"	,"default '0'","key"),
	  		array("be_url"					,"varchar(255)"				,"NOT NULL"	,"default ''"),
	  		array("be_type"					,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
//	  		array("be_file"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("be_max_page_impressions"	,"signed integer"			,"NOT NULL"	,"default '0'"),
	  		array("be_max_click_through"	,"signed integer"			,"NOT NULL"	,"default '0'"),
	  		array("be_cur_page_impressions"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
	  		array("be_cur_click_through"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("be_date_starts"			,"datetime"					,"NOT NULL"	,"default ''"),
	  		array("be_date_expires"			,"datetime"					,"NOT NULL"	,"default ''"),
			array("be_txt_des1"				,"varchar(40)"				,"NOT NULL"	,"default ''"),
			array("be_txt_des2"				,"varchar(40)"				,"NOT NULL"	,"default ''"),
			array("be_txt_url"				,"varchar(40)"				,"NOT NULL"	,"default ''"),
			array("be_txt_label"			,"varchar(40)"				,"NOT NULL"	,"default ''"),
			array("be_random_toggle"		,"unsigned small integer"	,"NOT NULL"	,"default '0'","key")
		);
		$primary ="be_identifier";
		$tables[count($tables)] = array("banner_entry", $fields, $primary);
		/*************************************************************************************************************************
		* Table structure for table 'banner_entry_grouping'
		* many to many relationship table
		*************************************************************************************************************************/
		$fields = array(
			array("beg_banner"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
	  		array("beg_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
	  		array("beg_list"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
	  		array("beg_type"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key")
		);
		$primary ="";
		$tables[count($tables)] = array("banner_entry_grouping", $fields, $primary);
		return $tables;
	}
	/*************************************************************************************************************************
	*                               B A N N E R   C O N T A I N E R   F U N C T I O N S
	*************************************************************************************************************************/

	/*************************************************************************************************************************
	* list the groups of banners that are on the site.
	*************************************************************************************************************************/
	function module_list($parameters){
		if($this->manage_banners==0){
			return "";
		}
		$sql = "select banner_list.*, banner_types.bt_label, count(be_identifier) as total  from banner_list 
					inner join banner_types on bt_identifier = bl_type and bl_client=bt_client
					left outer join banner_entry_grouping on beg_list=bl_identifier and beg_client=bl_client  
					left outer join banner_entry on beg_banner=be_identifier and be_client=beg_client 
				where bl_client=$this->client_identifier group by bl_identifier order by bl_identifier desc";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result = $this->parent->db_pointer->database_query($sql);
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));}
			$prev = $this->page_size;
			$number_of_records = $this->parent->db_pointer->database_num_rows($result);
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$this->call_command("DB_SEEK",array($result,$goto));
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
			$variables["as"]			= "table";
			$variables["PAGE_BUTTONS"]	= Array();
			if ($this->manage_banners == 1){
				$variables["PAGE_BUTTONS"][0] = Array("ADD",$this->module_command."MANAGE_GROUPING_MODIFY", ADD_NEW);
			}
			
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
//			$variables["as"]				= "table";
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			//			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
			$start_page=intval($page / $this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page + $this->page_size)>$num_pages){
				$end_page	 =	$num_pages;
			}else{
				$end_page	=	$this->page_size;
			}
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= $this->filter($parameters);
			$variables["HEADER"]			= LOCALE_BANNER_GROUPING_LIST;
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$label 			= $this->check_parameters($r,"bl_label","");
				$type_label		= $this->check_parameters($r,"bt_label","");
				$total			= $this->check_parameters($r,"total",0);
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["bl_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,								$label, "TITLE"),
						Array(LOCALE_BANNER_CONTAINER_TYPE,				$type_label, "SUMMARY"),
						Array(LOCALE_STATUS,							($r["bl_status"]==0?LOCALE_NOT_LIVE:LOCALE_LIVE), ""),
						Array(LOCALE_BANNER_CONTAINER_DISPLAY,			($r["bl_number_to_display"]==1?"One Single Banner":$r["bl_number_to_display"]." Bnr (".($r["bl_direction"]==0?"Horizontal":"Vertical").")"), ""),
						Array(LOCALE_BANNER_CONTAINER_TOTAL_BANNERS,	$r["total"], "")
					)
				);
				if ($this->manage_banners == 1){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."MANAGE_GROUPING_MODIFY",EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."MANAGE_GROUPING_REMOVE",REMOVE_EXISTING);
//					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("MANAGE",$this->module_command."MANAGE_BANNER_LIST",MANAGE_BANNERS);
				}
			}
			$this->page_size = $prev;
			return $this->generate_list($variables);
		}
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function remove_list_group($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		if($identifier==-1){
			return "";
		}
		$sql = "delete from banner_list where bl_identifier = $identifier and bl_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$sql = "delete from banner_entry_group where beg_list = $identifier and beg_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);

		$this->call_command("WEBOBJECTS_MANAGE_MODULE",
			Array(
				"owner_module" 	=> $this->webContainer,
				"owner_id" 		=> $identifier,
				"wo_command"	=> $this->webContainer."DISPLAY",
				"cmd"			=> "REMOVE"
			)
		);
		/**
		* removemenu locations
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier,
				"delete"		=> 1
			)
		);
	}
	/*************************************************************************************************************************
    * modify the list entry
    *************************************************************************************************************************/
	function modify_list_group($parameters){
		$identifier 			= $this->check_parameters($parameters,"identifier",-1);
		$Array_labels 			= Array();
		$Array_value 			= Array();
		$status					= 0;
		$all_locations 			= 0;
		$set_inheritance		= 0;
		$menu_locations			= Array();
		$display_tab	 		= "";
		$bl_label				= "";
		$bl_type				= -1;
		$bl_open_in_window		= 0;
		$bl_number_to_display	= 1;
		$bl_direction			= 0;
		$bl_homepage_exception	= 0;
		$txt_label				= "";
		if($identifier!=-1){
			$sql = "select * from banner_list 
						where bl_client =$this->client_identifier and bl_identifier = $identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			$identifier=-1; 
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$identifier				= $r["bl_identifier"];
				$bl_type				= $r["bl_type"];
				$bl_label				= $r["bl_label"];
				$bl_number_to_display	= $r["bl_number_to_display"];
				$bl_direction			= $r["bl_direction"];
				$bl_open_in_window		= $r["bl_open_in_window"];
				$status					= $r["bl_status"];
				$bl_homepage_exception	= $r["bl_homepage_exception"];
            }
            $this->parent->db_pointer->database_free_result($result);
		} 
			$sql = "select * from banner_types 
						where 
							bt_client =$this->client_identifier order by bt_label";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result  = $this->parent->db_pointer->database_query($sql);
			$type_table ="";
			$i=0;
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
//				$txt_label = "&lt;strong&gt;".$r["bt_label"]."&lt;/strong&gt;&lt;br&gt;".$r["bt_description"]."&lt;br&gt;Width x Height :: (".($r["bt_width"]=="0"?"Any width":$r["bt_width"])." x ".($r["bt_height"]=="0" ? "Any height" : $r["bt_height"]).")";
				$type_table .= "<tr>
						<td style='width:15px;'><input type='hidden' name='bt_identifier_".$i."' value='".$r["bt_identifier"]."'></input><input type='radio' name='bl_type' value='".$r["bt_identifier"]."'";
					if($bl_type==$r["bt_identifier"]){
						$type_table .= " checked='true'";
					}
					$type_table .= "/></td>";
					$bt_width 	= $r["bt_width"];
					if($bt_width<1){
						$bt_width=-1;
					}
					$bt_height	= $r["bt_height"];
					if($bt_height<1){
						$bt_height=-1;
					}
					$bt_label	= $r["bt_label"];
					$type_table .= "
					<td id='l_$i' style='width:300px;'>".$bt_label."</td>
					<td id='w_$i' style='width:90px;'>".($bt_width < 1 ? LOCALE_BANNER_UNLIMITED : $bt_width)."</td>
					<td id='h_$i' style='width:90px;'>".($bt_height < 1 ? LOCALE_BANNER_UNLIMITED : $bt_height)."</td>
					<td ><a style='width:65px;text-align:center;text-decoration:none;' href=\"javascript:banner_type_edit($i);\" class='bt'>Edit</a></td>
				</tr>";
				$type_table .= "<tr>
						<td colspan='5' id='banner_edit_$i' style='display:none;border:1px solid #666666;'>";
				$type_table .="<label for='bt_label_$i' style='width:200px'>".LOCALE_BANNER_TYPE_LABEL."</label> <input type=\"text\" name=\"bt_label_$i\" id=\"id_bt_label_$i\" value='".$r["bt_label"]."'><br>";
				$type_table .='<label for="bt_width_'.$i.'" style="width:200px">'.LOCALE_BANNER_TYPE_WIDTH.'</label> <select name="quantity_bt_width_'.$i.'"  id="bt_width_'.$i.'" onchange="javascript:setquantity(\'bt_width_'.$i.'\');"><option value="-1"';
				if ($bt_width<1){
					$type_table .=' selected';
				}
				$type_table .='>Unlimited</option>
					<option value="-2"';
				if ($bt_width>0){
					$type_table .=' selected';
				}
				$type_table .='>Defined</option>
				</select><input type="text" ';
				if ($bt_width<1){
					$type_table .=' style="display:none"';
				}
				$type_table .=' name="bt_width_'.$i.'" id="id_bt_width_'.$i.'" value="'.$bt_width.'" onchange="javascript:check_format(this,\'number\')"><br/>';
				$type_table .='<label for="bt_height_$i" style="width:200px">'.LOCALE_BANNER_TYPE_HEIGHT.'</label> <select name="quantity_bt_height_'.$i.'" id="bt_height_'.$i.'" onchange="javascript:setquantity(\'bt_height_'.$i.'\');"><option value="-1"';
				if ($bt_height < 1){
					$type_table .=' selected';
				}
				$type_table .='>Unlimited</option>
					<option value="-2"';
				if ($bt_height>0){
					$type_table .=' selected';
				}
				$type_table .='>Defined</option>
				</select><input type="text" ';
				if ($bt_height<1){
					$type_table .=' style="display:none"';
				}
				$type_table .=' name="bt_height_'.$i.'" id="id_bt_height_'.$i.'" value="'.$bt_height.'" onchange="javascript:check_format(this,\'number\')"><br/> ';
				$type_table .="<a style='width:65px;text-align:center;text-decoration:none;' href=\"javascript:banner_type_hide($i);\" class='bt'>Hide</a>";
				
//				$type_table .="		<input type=\"text\" name=\"bt_height_$i\" label='".LOCALE_BANNER_TYPE_HEIGHT."' format='unlimited'><![CDATA[-1]]></input>";
				$type_table .= "</td>
				</tr>";
				$i++;
            }
			$type_table .= "<tr>
				<td style='width:15px;'><input type='radio' name='bl_type' value='-1'";
			if($bl_type==$r["bt_identifier"]){
				$type_table .= " checked='true'";
			}
			$type_table .= "/></td>
				<td id='l_new'>Add New Type</td>
				<td id='w_new'></td>
				<td id='h_new'></td>
				<td ><a style='width:65px;text-align:center;text-decoration:none;' href=\"javascript:banner_type_edit('new');\" class='bt'>Edit</a></td>
			</tr>";
			$bt_width=-1;
			$bt_height=-1;
			$type_table .= "<tr>
					<td colspan='5' id='banner_edit_new' style='display:none;border:1px solid #666666;'>";
			$type_table .="<label for='bt_label_new' style='width:200px'>".LOCALE_BANNER_TYPE_LABEL."</label> <input type=\"text\" name=\"bt_label_new\" id=\"bt_label_new\" value=''><br>";
			$type_table .='<label for="bt_width_new" style="width:200px">'.LOCALE_BANNER_TYPE_WIDTH.'</label> <select name="quantity_bt_width_new"  id="bt_width_new" onchange="javascript:setquantity(\'bt_width_new\');"><option value="-1"';
			if ($bt_width<1){
				$type_table .=' selected';
			}
			$type_table .='>Unlimited</option>
				<option value="-2"';
			if ($bt_width>0){
				$type_table .=' selected';
			}
			$type_table .='>Defined</option>
			</select><input type="text" ';
			if ($bt_width<1){
				$type_table .=' style="display:none"';
			}
			$type_table .=' name="bt_width_new" id="id_bt_width_new" value="-1" onchange="javascript:check_format(this,\'number\')"><br/>';
			$type_table .='<label for="bt_height_new" style="width:200px">'.LOCALE_BANNER_TYPE_HEIGHT.'</label> <select name="quantity_bt_height_new" id="bt_height_new" onchange="javascript:setquantity(\'bt_height_new\');"><option value="-1"';
			if ($bt_height < 1){
				$type_table .=' selected';
			}
			$type_table .='>Unlimited</option>
				<option value="-2"';
			if ($bt_height>0){
				$type_table .=' selected';
			}
			$type_table .='>Defined</option>
			</select><input type="text" ';
			if ($bt_height<1){
				$type_table .=' style="display:none"';
			}
			$type_table .=' name="bt_height_new" id="id_bt_height_new" value="-1" onchange="javascript:check_format(this,\'number\')"><br/> ';
			$type_table .="<a style='width:65px;text-align:center;text-decoration:none;' href=\"javascript:banner_type_hide('new');\" class='bt'>Hide</a>";
			$type_table .= "</td>
			</tr>";
            $this->parent->db_pointer->database_free_result($result);
		
		$menu_locations		= $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
			Array(
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier
			)
		);

		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="<page_options>";
		$out .="<header><![CDATA[".LOCALE_BANNER_CONTAINER_MANAGE."]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".LOCALE_BANNER_GROUP_SETTINGS."\" width=\"100%\">";
		$out .= "		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		if($identifier==-1){
			$out .= "		<input type=\"hidden\" name=\"command\" value=\"BANNERADMIN_MANAGE_GROUPING_MODIFY_SAVE_TAB\" />";
		} else {
			$out .="		<input type=\"hidden\" name=\"bl_type\" ><![CDATA[$bl_type]]></input>";
			$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."MANAGE_GROUPING_SAVE\" />";
		}
		$out .="		<page_sections>";
		$out .="			<section label='".LOCALE_BANNER_CONTAINER_MODIFY."'>";
		$out .="				<input type=\"text\" name=\"bl_label\" label='".LOCALE_BANNER_SHORT."' required='YES'><![CDATA[$bl_label]]></input>";
		$out .="				<select name=\"bl_status\" label='".LOCALE_STATUS."'>".$this->gen_options(Array(0,1),Array(LOCALE_NOT_LIVE,LOCALE_LIVE),$status)."</select>";
		$out .="				<select name=\"bl_number_to_display\" label='".LOCALE_BANNER_NUMBER_DISPLAY."'>".$this->gen_options(Array(1,2,3,4,5,6,7,8),Array("1 Banner", "2 Banners", "3 Banners", "4 Banners", "5 Banners", "6 Banners", "7 Banners", "8 Banners"), $bl_number_to_display)."</select>";
		$out .="				<select name=\"bl_direction\" label='".LOCALE_BANNER_DIRECTION."'>".$this->gen_options(Array(0,1),Array("Horizontal","Vertical"), $bl_direction)."</select>";
		$out .="				<select name=\"bl_open_in_window\" label='".LOCALE_OPEN_NEW_WIN."'>".$this->gen_options(Array(0,1),Array(LOCALE_NO,LOCALE_YES), $bl_open_in_window)."</select>";
		
		/*************************************************************************************************************************
        * neils home page exception 
        *************************************************************************************************************************/
		$out .= "				<select name='bl_homepage_exception' label='Restrict homepage banners'>".$this->gen_options(Array(0,1), Array(LOCALE_NO, LOCALE_YES), $bl_homepage_exception)."</select>";
		/**
		* Display type of list
		**/
		$web_containers = split("~----~",$this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>$this->webContainer, "identifier"=>$identifier)));
		if ($web_containers[0]!=""){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}
		$out .="	</section>";
		$out .="	<section label='".LOCALE_BANNER_CONTAINER_SIZE."'>";
		/*************************************************************************************************************************
        * selec the size of the banner container
        *************************************************************************************************************************/
		$out .= "<input type='hidden' name='counter' value='$i'/>";
		$out .= "<text><![CDATA[<table width='auto' cellspacing='0' cellpadding='3'><tr><th>#</th><th style='width:90px;'>".LOCALE_BANNER_TYPE_LABEL."</th><th style='width:90px;'>".LOCALE_BANNER_TYPE_WIDTH."</th><th style='width:90px;'>".LOCALE_BANNER_TYPE_HEIGHT."</th><th>Options</th></tr>$type_table</table>]]></text>";
		$out .="	</section>";
			$out .= "		<section label='Choose banners'>";
			$sql = "select * from banner_entry 
						inner join banner_types on bt_identifier = be_type and be_client = bt_client
						left outer join banner_entry_grouping on ((beg_list = $identifier or beg_list is null) and beg_banner=be_identifier and beg_client = be_client)
						where be_client =$this->client_identifier";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result  = $this->parent->db_pointer->database_query($sql);
			$numrows = $this->parent->db_pointer->database_num_rows($result);
			$table		= "";
			if ($numrows == 0){
				$out .="<text><![CDATA[You have not added any banners of this type yet]]></text>";
			} else {
				while($r = $this->parent->db_pointer->database_fetch_array($result)){
					$table .= "<tr>
								<td style='width:15px;'><input type='checkbox' name='banner_list[]' value='".$r["be_identifier"]."'";
					if($this->check_parameters($r,"beg_list",0)!=0){
						$table .= " checked='true'";
					}
					$table .= "/></td>
								<td>" . $r["be_label"] . "</td>
								<td>" . ($r["be_status"]==0 ? LOCALE_NOT_LIVE : LOCALE_LIVE ) . "</td>
								<td>" . $r["be_cur_page_impressions"]. "</td>
								<td>" . ($r["be_max_page_impressions"]==-1?LOCALE_BANNER_UNLIMITED:$r["be_max_page_impressions"]). "</td>
								<td>" . $r["be_cur_click_through"]. "</td>
								<td>" . ($r["be_max_click_through"]==-1?LOCALE_BANNER_UNLIMITED:$r["be_max_click_through"]). "</td>
								<td>" . $r["bt_label"]. "</td>
							</tr>";
	            }
				$out .="<text><![CDATA[<table cellpadding='3' cellspacing='0'><tr>
										<th>#</th>
										<th>".LOCALE_BANNER_NAME."</th>
										<th>".LOCALE_STATUS."</th>
										<th>".LOCALE_BANNER_VIEWS."</th>
										<th>".LOCALE_BANNER_MVIEWS."</th>
										<th>".LOCALE_BANNER_CLICK."</th>
										<th>".LOCALE_BANNER_MCLICK."</th>
										<th>".LOCALE_BANNER_TYPE_LABEL."</th>
										</tr>$table</table>]]></text>";
			}
            $this->parent->db_pointer->database_free_result($result);
			$out .= "		</section>";
		$out .="		</page_sections>";
		if($identifier==-1){
			$out .="		<input type=\"submit\" iconify=\"NEXT\" value=\"".LOCALE_NEXT."\" />";
		} else {
			$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		}
		$out .="	</form>";
		$out .="</module>";
		/*************************************************************************************************************************
        * return the screen structure to the engine
        *************************************************************************************************************************/
		return $out;
	}
	/*************************************************************************************************************************
    * save the banner group infomration and set up the module dependances
    *************************************************************************************************************************/
	function modify_list_group_save($parameters){
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters,true)."</pre></li>";
//		$this->exitprogram();
		/*************************************************************************************************************************
        * get banner information
        *************************************************************************************************************************/
		$identifier 			= $this->check_parameters($parameters,"identifier",-1);
		$bl_label 				= $this->validate($this->check_parameters($parameters, "bl_label"));
		$bl_type 				= $this->check_parameters($parameters, "bl_type",-1);
		$bl_status 				= $this->check_parameters($parameters, "bl_status");
		$bl_number_to_display	= $this->check_parameters($parameters, "bl_number_to_display",1);
		$bl_direction			= $this->check_parameters($parameters, "bl_direction",0);
		$bl_homepage_exception	= $this->check_parameters($parameters, "bl_homepage_exception",0);
		$bl_open_in_window		= $this->check_parameters($parameters, "bl_open_in_window",0);
		/*************************************************************************************************************************
        * get web containers
        *************************************************************************************************************************/
		$replacelist			= $this->check_parameters($parameters, "web_containers",Array());
		$currentlyhave			= $this->check_parameters($parameters, "currentlyhave");
		/*************************************************************************************************************************
        * list of banners attached to this grouping
        *************************************************************************************************************************/
		$banner_list			= $this->check_parameters($parameters, "banner_list", Array());
		/*************************************************************************************************************************
        * get a new type definition if required
        *************************************************************************************************************************/
		$new_label				= $this->check_parameters($parameters, "new_label");
		/*************************************************************************************************************************
        * if select type is defined then just create new type but if other is selected then set this banenr to the new type
        *************************************************************************************************************************/
		if($bl_type!=-1){
			$this->module_manage_types_save_form($parameters);
		} else {
			$bl_type = $this->module_manage_types_save_form($parameters);
		}
		/*************************************************************************************************************************
        * save or update
        *************************************************************************************************************************/
		if($identifier==-1){
			$identifier = $this->getUid();
			$sql = "insert into banner_list 
						(bl_identifier, bl_homepage_exception, bl_client, bl_label, bl_type, bl_status, bl_number_to_display, bl_direction, bl_open_in_window) values 
						('$identifier', '$bl_homepage_exception', '$this->client_identifier', '$bl_label', '$bl_type', '$bl_status', '$bl_number_to_display', '$bl_direction', '$bl_open_in_window')";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",
				Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $bl_label,
					"wo_command"	=> $this->webContainer."DISPLAY",
					"cmd"			=> "INSERT",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist,
				)
			);
		} else {
			$sql = "update banner_list set bl_open_in_window='$bl_open_in_window', bl_type='$bl_type', bl_number_to_display='$bl_number_to_display', bl_direction='$bl_direction', bl_homepage_exception='$bl_homepage_exception', bl_label='$bl_label', bl_status='$bl_status' where bl_identifier='$identifier' and bl_client='$this->client_identifier'";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",
				Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $bl_label,
					"wo_command"	=> $this->webContainer."DISPLAY",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist,
				)
			);
			$cmd			= "UPDATE";
		}
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql = "delete from banner_entry_grouping where beg_client = $this->client_identifier and beg_list = $identifier";
		$this->parent->db_pointer->database_query($sql);
		$l = count($banner_list);
		for($i=0 ; $i<$l; $i++){
			$sql = "insert into banner_entry_grouping (beg_list, beg_client, beg_banner, beg_type) values ($identifier, $this->client_identifier, ".$banner_list[$i].", $bl_type)";
			$this->parent->db_pointer->database_query($sql);
		}
//		$this->exitprogram();
		return $identifier;
	}
	/*************************************************************************************************************************
	*		                               B A N N E R   T Y P E S   F U N C T I O N S
	*************************************************************************************************************************/
	
	/*************************************************************************************************************************
	* list the groups of banners that are on the site.
	*************************************************************************************************************************/
	function module_list_types($parameters){
		if($this->manage_banners==0){
			return "";
		}
		$sql = "select * from banner_types where bt_client=$this->client_identifier order by bt_label asc";
		$result = $this->parent->db_pointer->database_query($sql);
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));}
			$prev = $this->page_size;
			$number_of_records = $this->parent->db_pointer->database_num_rows($result);
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$this->call_command("DB_SEEK",array($result,$goto));
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
			$variables["as"]			= "table";
			$variables["PAGE_BUTTONS"]	= Array();
			if ($this->manage_banners == 1){
				$variables["PAGE_BUTTONS"][0] = Array("ADD",$this->module_command."MANAGE_TYPES_MODIFY", ADD_NEW);
			}
			
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
//			$variables["as"]				= "table";
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			//			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
			$start_page=intval($page / $this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page + $this->page_size)>$num_pages){
				$end_page	 =	$num_pages;
			}else{
				$end_page	=	$this->page_size;
			}
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= "";
			$variables["HEADER"]			= LOCALE_BANNER_TYPE_LIST;
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$label 			= $this->check_parameters($r,"bt_label","");
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["bt_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,					$label, "TITLE"),
						Array(LOCALE_BANNER_LIST_SIZE,		($r["bt_width"] < 1 ? LOCALE_BANNER_UNLIMITED : $r["bt_width"])." x ".($r["bt_height"] < 1 ? LOCALE_BANNER_UNLIMITED : $r["bt_height"]), "SUMMARY")
					)
				);
				if ($this->manage_banners == 1){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."MANAGE_TYPES_MODIFY",EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."MANAGE_TYPES_REMOVE",REMOVE_EXISTING);
				}
			}
			$this->page_size = $prev;
			return $this->generate_list($variables);
		}
	}
	/*************************************************************************************************************************
    * manage the list of banner types
    *************************************************************************************************************************/
	function module_manage_types($parameters){
		/*************************************************************************************************************************
        *  function variable definition
        *************************************************************************************************************************/
		$frm_label		= "Manage Form Types";
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		$list			= Array();
		$counter		= 0;
		/*************************************************************************************************************************
        * extract the list of existing types
        *************************************************************************************************************************/
		if($identifier==-1){
				$title				= "";
				$description		= "";
				$width				= -1;
				$height				= -1;
		} else {
			$sql = "select * from banner_types where bt_client =$this->client_identifier and bt_identifier = $identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$title				= $r["bt_label"];
				$description		= $r["bt_description"];
				$width				= $r["bt_width"];
				$height				= $r["bt_height"];
    	    }
	        $this->parent->db_pointer->database_free_result($result);
		}
		if($width==0)
			$width=-1;
		if($height==0)
			$height=-1;
		/*************************************************************************************************************************
        * generate the screen structure
        *************************************************************************************************************************/
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="<page_options>";
		$out .="<header><![CDATA[$frm_label]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."MANAGE_TYPES_LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"$frm_label\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."MANAGE_TYPES_SAVE\" />";
		$out .="		<input type=\"hidden\" name=\"bt_identifier\" ><![CDATA[$identifier]]></input>";
		$out .="		<page_sections>";
		$out .="			<section label='Define New'>";
		$out .="				<input type=\"text\" name=\"bt_label\" label='".LOCALE_BANNER_TYPE_LABEL."'><![CDATA[$title]]></input>";
		$out .="				<textarea name=\"bt_description\" label='".LOCALE_BANNER_TYPE_DES."' height='6' ><![CDATA[$description]]></textarea>";
		$out .="				<input type=\"text\" name=\"bt_width\" label='".LOCALE_BANNER_TYPE_WIDTH."' format='unlimited'><![CDATA[$width]]></input>";
		$out .="				<input type=\"text\" name=\"bt_height\" label='".LOCALE_BANNER_TYPE_HEIGHT."' format='unlimited'><![CDATA[$height]]></input>";
		$out .="			</section>";
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		/*************************************************************************************************************************
        * return the screen structure to the engine
        *************************************************************************************************************************/
		return $out;
	}
	/*************************************************************************************************************************
    * save the banner types
    *************************************************************************************************************************/
	function module_manage_types_save($parameters){
//		$counter 			= $this->check_parameters($parameters,"counter",0);
		$identifier			= $this->check_parameters($parameters,"bl_type",$this->check_parameters($parameters,"bt_identifier",-1));
		$bt_label 			= $this->validate($this->check_parameters($parameters,"bt_label"));
		$bt_description		= $this->validate($this->check_parameters($parameters,"bt_description"));
		$bt_width 			= $this->check_parameters($parameters,"bt_width", 0);
		$bt_height			= $this->check_parameters($parameters,"bt_height", 0);
		if($bt_width<0)
			$bt_width=0;
		if($bt_height<0)
			$bt_height=0;
		/*************************************************************************************************************************
        * save the new type if required
        *************************************************************************************************************************/
		if($identifier==-1){
			$identifier = $this->getUid();
			$sql= "insert into banner_types (bt_identifier, bt_client, bt_label, bt_description, bt_width, bt_height) values
			('$identifier', '$this->client_identifier', '$bt_label', '$bt_description', '$bt_width', '$bt_height')";
			$this->parent->db_pointer->database_query($sql);
		} else {
			$sql = "update banner_types set bt_label ='".$bt_label."', bt_description='".$bt_description."', bt_width='".$bt_width."', bt_height='".$bt_height."' where bt_identifier = ".$identifier." and bt_client = $this->client_identifier";
			$this->parent->db_pointer->database_query($sql);
		}
		return $identifier;
	}
	/*************************************************************************************************************************
    * save the banner types
    *************************************************************************************************************************/
	function module_manage_types_save_form($parameters){
		$counter 	= $this->check_parameters($parameters,"counter",0);
		$identifier = 1;
		for($i=0;$i<$counter;$i++){
			$bt_label 			= $this->validate($this->check_parameters($parameters,"bt_label_$i"));
			$bt_width 			= $this->check_parameters($parameters,"bt_width_$i", 0);
			$bt_height			= $this->check_parameters($parameters,"bt_height_$i", 0);
			$bt_identifier		= $this->check_parameters($parameters,"bt_identifier_$i", 0);
			if ( $bt_width < 0 )
				$bt_width=0;
			if ( $bt_height < 0 )
				$bt_height=0;
			/*************************************************************************************************************************
    	    * update all existing settings
        	*************************************************************************************************************************/
			$sql = "update banner_types set bt_label ='".$bt_label."', bt_width='".$bt_width."', bt_height='".$bt_height."' where bt_identifier = ".$bt_identifier." and bt_client = $this->client_identifier";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$this->parent->db_pointer->database_query($sql);
		}
		$i ='new';
		$bt_label 			= $this->validate($this->check_parameters($parameters,"bt_label_$i"));
		$bt_width 			= $this->check_parameters($parameters,"bt_width_$i", 0);
		$bt_height			= $this->check_parameters($parameters,"bt_height_$i", 0);
		$bt_identifier		= $this->check_parameters($parameters,"bl_type_$i", 0);
		if ( $bt_width < 0 )
			$bt_width=0;
		if ( $bt_height < 0 )
			$bt_height=0;
		/*************************************************************************************************************************
   	    * save the new type if required
       	*************************************************************************************************************************/
		$identifier = -1;
		if($bt_label!=""){
			$bt_identifier = $this->getUid();
			$identifier = $bt_identifier;
			$sql= "insert into banner_types (bt_identifier, bt_client, bt_label, bt_width, bt_height) values ('$bt_identifier', '$this->client_identifier', '$bt_label', '$bt_width', '$bt_height')";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$this->parent->db_pointer->database_query($sql);
		}
		return $identifier;
	}
	/*************************************************************************************************************************
    * remove a banner type
    *************************************************************************************************************************/
	function module_manage_types_remove($parameters){
		$bt_id				= $this->check_parameters($parameters,"bl_type",$this->check_parameters($parameters,"bt_identifier",-1));
		$sql = "delete from banner_types where bt_identifier = ".$bt_id." and bt_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		return 1;
	}
	/*************************************************************************************************************************
	*		                               B A N N E R   E N T R Y   F U N C T I O N S
	*************************************************************************************************************************/
	
	/*************************************************************************************************************************
    * manage a banners information
    *************************************************************************************************************************/
	function module_manage_banner($parameters){
		if($this->manage_banners==0){
			return "";
		}
		$identifier 			= $this->check_parameters($parameters,"identifier",-1);
		$list 					= $this->check_parameters($parameters,"list",-1);
		$be_type		 		= $this->check_parameters($parameters,"type",-1);
		$table					= "";
		if($list!=-1){
			$sql = "select * from banner_list where bl_identifier = $list and bl_client = $this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$be_type = $r["bl_type"];
            }
            $this->parent->db_pointer->database_free_result($result);
		}
		if ($be_type==-1){
//			return ""; // for new entries a type is required
		}
		$Array_labels 			= Array();
		$Array_value 			= Array();
		$Array_selected			= Array();
		$be_status				= 0;
		$all_locations 			= 0;
		$set_inheritance		= 0;
		$menu_locations 		= Array();
		$display_tab 			= "";
		$bl_label				= "";
		$bl_long				= "";
		$be_label				= "";
		$numrows				= 0;
		$be_max_impressions		= -1;
		$be_max_click_through	= -1;
		$be_cur_impressions		= 0;
		$be_url					= "";
		$be_cur_click_through	= 0;
		$be_date_starts			= "0000-00-00 00:00:00";
		$be_date_expires		= "0000-00-00 00:00:00";
		$be_txt_label			= "";
		$be_txt_des1			= "";
		$be_txt_des2			= "";
		$be_txt_url				= "";
		$be_random_toggle 		= 0;
		$file_associations_identifiers = "";
		$file_associations ="";
		$sql = "select * from file_to_object  
					inner join file_info on (fto_file = file_identifier and file_client= fto_client)
				where 
					fto_client=$this->client_identifier and 
					fto_object = $identifier and 
					fto_module='$this->webContainer' 
				order by 
					fto_rank";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result		= $this->parent->db_pointer->database_query($sql);
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r	= $this->parent->db_pointer->database_fetch_array($result)){
				$file_associations_identifiers .="".$r["fto_file"].",";
				$file_associations  .= "<file_info 
					logo='/libertas_images/icons/mime-images/".$this->call_command("FILES_GET_MIME_IMAGE",Array($r["file_name"])).".gif' 
					identifier='".$r["file_identifier"]."' 
					rank='".$this->check_parameters($r,"fto_rank")."'
					width='".$this->check_parameters($r,"file_width")."'
					height='".$this->check_parameters($r,"file_height")."'
					size='".$this->check_parameters($r,"file_size")."'
					md5='".$this->check_parameters($r,"file_md5_tag")."'
					ext='".$this->file_extension($r["file_name"])."'
					><![CDATA[".$r["file_label"]."]]></file_info>";
			}
			$ok = true;
		}
		$this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$be_type_out = "";
		if($be_type==-1){
			$be_type_out = "<radio name='be_type' label='Choose Banner type'>";
			$sql = "select * from banner_types where bt_client = $this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			$i=0;
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$be_type_out .= "<option value='".$r["bt_identifier"]."'";
				if ($i==0){
					$be_type_out .= " selected='true'";
				}
				$be_type_out .= "><![CDATA[".$r["bt_label"]."]]></option>";
				$i++;
            }
            $this->parent->db_pointer->database_free_result($result);
			$be_type_out .="</radio>";
		}
		if($identifier!=-1){
			/*************************************************************************************************************************
            * get a list of banner groups that we can add this type of banner too.
            *************************************************************************************************************************/
			$sql = "select * from banner_list 
						inner join banner_types on bt_identifier = bl_type and bt_client = bl_client
						left outer join banner_entry_grouping on beg_list=bl_identifier and beg_client=bl_client and beg_banner=$identifier
						where bl_client =$this->client_identifier and bl_type = $be_type ";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$numrows++;
				$table .= "<tr>
							<td style='width:15px;'><input type='checkbox' name='banner_list[]' value='".$r["bl_identifier"]."'";
				if($this->check_parameters($r,"beg_list",0)!=0){
					$table .= " checked='true'";
				}
				$table .= "/></td>
							<td>" . $r["bl_label"] . "</td>
							<td>" . ($r["bl_status"]==0 ? LOCALE_NOT_LIVE : LOCALE_LIVE ) . "</td>
							<td>" . ($r["bt_width"]==0 ? LOCALE_BANNER_UNLIMITED : $r["bt_width"] ) . "</td>
							<td>" . ($r["bt_height"]==0 ? LOCALE_BANNER_UNLIMITED : $r["bt_height"] ) . "</td>
						   </tr>";
/*
				$txt_label	= "&lt;strong&gt;".$r["bl_label"]."&lt;/strong&gt; :: status (".($r["bl_status"]==0?LOCALE_NOT_LIVE:LOCALE_LIVE).")";
            	$Array_labels[count($Array_labels)] = $txt_label;
				$Array_value[count($Array_value)] = $r["bl_identifier"];
				$Array_selected[count($Array_selected)] = $this->check_parameters($r,"beg_list",0);
*/
            }
            $this->parent->db_pointer->database_free_result($result);
			/*************************************************************************************************************************
            * get a list of banner groups that we can add this type of banner too.
            *************************************************************************************************************************/
			$sql = "select * from banner_entry where be_client =$this->client_identifier and be_identifier = $identifier ";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$numrows++;
				$be_identifier 			= $r["be_identifier"];
				$be_label				= $r["be_label"];
				$be_url					= $r["be_url"];
				$be_status				= $r["be_status"];
				$be_max_impressions		= $r["be_max_page_impressions"];
				$be_cur_impressions		= $r["be_cur_page_impressions"];
				$be_max_click_through	= $r["be_max_click_through"];
				$be_cur_click_through	= $r["be_cur_click_through"];
				$be_date_starts			= $r["be_date_starts"];
				$be_date_expires		= $r["be_date_expires"];
				$be_txt_label			= $r["be_txt_label"];
				$be_txt_des1			= $r["be_txt_des1"];
				$be_txt_des2			= $r["be_txt_des2"];
				$be_txt_url				= $r["be_txt_url"];
				$be_random_toggle 		= $r["be_random_toggle"];
			}
            $this->parent->db_pointer->database_free_result($result);
			/*************************************************************************************************************************
	        * check some settings
       		*************************************************************************************************************************/
			$mls = $this->call_command("LAYOUT_MENU_LOCATION_SETTINGS_GET", Array("identifier"=>$identifier, "module"=>$this->webContainer."ENTRY"));
			$all_locations		= $mls["all_locations"];
			$set_inheritance	= $mls["set_inheritance"];
		}
		if($identifier==-1){
			$sql = "select distinct * from banner_list 
						inner join banner_types on bt_identifier = bl_type and bt_client = bl_client
						where bl_client =$this->client_identifier";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$numrows++;
				$table .= "<tr>
							<td style='width:15px;'><input type='checkbox' name='banner_list[]' value='".$r["bl_identifier"]."'/></td>
							<td>" . $r["bl_label"] . "</td>
							<td>" . ($r["bl_status"]==0 ? LOCALE_NOT_LIVE : LOCALE_LIVE ) . "</td>
							<td>" . ($r["bt_width"]==0 ? LOCALE_BANNER_UNLIMITED : $r["bt_width"] ) . "</td>
							<td>" . ($r["bt_height"]==0 ? LOCALE_BANNER_UNLIMITED : $r["bt_height"] ) . "</td>
						   </tr>";
//				$txt_label	= ."".($r["bl_status"]==0 ? " This banner list is currently not live" : "") ." (W x H) = ".$r;
 //           	$Array_labels[count($Array_labels)] = $txt_label;
//				$Array_value[count($Array_value)] = $r["bl_identifier"];
            }
            $this->parent->db_pointer->database_free_result($result);
			
			$category_list = $this->call_command("CATEGORYADMIN_RETRIEVE_LIST", 
				Array(
					"module"=>"FILES_", 
					"label"	=>"Choose the Virtual folders this file is available in",
					"add"	=>"Add new Virtual Folder"
				)
			);
		} 
		$menu_locations		= $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
			Array(
				"module"		=> $this->webContainer."ENTRY",
				"identifier"	=> $identifier
			)
		);
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="<page_options>";
		$out .="<header><![CDATA[Manage banner entry]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."MANAGE_BANNER_LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"Define Banner Settings\" width=\"100%\">";
		$out .= "		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		if($be_type_out==""){
			$out .= "		<input type=\"hidden\" name=\"be_type\" value=\"$be_type\" />";
		}
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."MANAGE_BANNER_SAVE\" />";
		$out .="		<input type=\"hidden\" name=\"file_associations\" value=\"$file_associations_identifiers\" />";
		$out .="		<page_sections>";
		
		$out .= "		<section label=\"".LOCALE_BANNER_SELECT."\" name=\"file_associations_tab\" command=\"FILES_LIST&amp;file_mime=image&amp;lock_mime=1&amp;onlyone=1\" link='file_associations' return_command='FILES_LIST_FILE_DETAIL'";
			if ($display_tab=="files"){
				$out .= " selected='true'";
			}			
			$out .= ">
					<file_list report='detailed'>
						<option><cmd><![CDATA[FILES_LIST&amp;file_mime=image&amp;lock_mime=1&amp;onlyone=1]]></cmd><label><![CDATA[".LOCALE_BANNER_SELECT_FILE."]]></label></option>
						<option><cmd><![CDATA[FILES_ADD&amp;onlyone=1]]></cmd><label><![CDATA[".LOCALE_BANNER_UPLOAD_FILE."]]></label></option>
						$file_associations
					</file_list>
					<input type=\"text\" name=\"be_url\" label='".LOCALE_BANNER_URL."' required='YES'><![CDATA[$be_url]]></input>
					<text><![CDATA[".LOCALE_BANNER_TEXT_BANNER_MSG."]]></text>
					<input type='text' name='be_txt_label' size='40' label='".LOCALE_TEXT_LABEL."' required='YES'><![CDATA[$be_txt_label]]></input>
					<input type='text' name='be_txt_des1' size='40' label='".LOCALE_DESCRIPTION_1."' required='YES'><![CDATA[$be_txt_des1]]></input>
					<input type='text' name='be_txt_des2' size='40' label='".LOCALE_DESCRIPTION_2."'><![CDATA[$be_txt_des2]]></input>
					<input type='text' name='be_txt_url' size='40' label='".LOCALE_HOMEPAGE."' required='YES'><![CDATA[$be_txt_url]]></input>
					<text><![CDATA[".LOCALE_BANNER_TEXT_BANNER_EXAMPLE."]]></text>
			</section>";
		$out .="		<section label='".LOCALE_BANNER_SETTINGS."'>";
//		$out .="			<input type=\"text\" name=\"be_label\" label='".LOCALE_BANNER_LABEl."' required='YES'><![CDATA[$be_label]]></input>";
		$out .="			<select name=\"be_status\" label='".LOCALE_STATUS."'>".$this->gen_options(Array(0,1),Array(LOCALE_NOT_LIVE,LOCALE_LIVE), $be_status)."</select>";
		/*************************************************************************************************************************
        * display file (image) association program
        *************************************************************************************************************************/
//		$out .="			<summary_file label='Image for Banner' filter='image'>";
//		$out .="				<showframe>1</showframe>";
//		$out .= $this->call_command("FILES_GET_OBJECT",Array("module"=>$this->webContainer, "owner_id"=>$identifier));
//		$out .="			</summary_file>";
		/*************************************************************************************************************************
        * fields
        *************************************************************************************************************************/
		$out .="<text><![CDATA[".LOCALE_BANNER_CAMPAIGN_SETTINGS."]]></text>";
		$out .= $be_type_out;
		$out .="<select name=\"be_random_toggle\" label='".LOCALE_BANNER_TOGGLE."'>".$this->gen_options(Array(0,1),Array(LOCALE_NO,LOCALE_YES), $be_random_toggle)."</select>";
		$out .="<input type=\"text\" name=\"be_max_impressions\" label='".LOCALE_BANNER_NOVIEWS."' format='unlimited'><![CDATA[$be_max_impressions]]></input>";
		if($be_cur_impressions!=0){
			$out .="<text><![CDATA[".LOCALE_BANNER_TOTAL_VIEWS." <strong>$be_cur_impressions</strong>]]></text>";
		}
		$out .="<input type=\"text\" name=\"be_max_click_through\" label='".LOCALE_BANNER_NOCLICKS."' format='unlimited'><![CDATA[$be_max_click_through]]></input>";
		if($be_cur_click_through!=0){
			$out .="<text><![CDATA[".LOCALE_BANNER_TOTAL_CLICKS." <strong>$be_cur_click_through</strong>]]></text>";
		}
		$out .="		<input type=\"date_time\" name=\"be_date_starts\"  label='".LOCALE_BANNER_DATE_FROM."' value='$be_date_starts' size=\"255\" year_start='".(date("Y"))."' year_end='".(date("Y")+2)."' />";
		$out .="		<input type=\"date_time\" name=\"be_date_expires\" label='".LOCALE_BANNER_DATE_TO."' value='$be_date_expires' size=\"255\" year_start='".(date("Y"))."' year_end='".(date("Y")+2)."' />";

		/**
		* Display type of list
		**/
		$out .="		</section>";

		$out .= "		<section label='".LOCALE_BANNER_GROUPING_TAB."'>";
		if ($numrows == 0){
			$out .="<text><![CDATA[You have not added any banner groups of this type yet]]></text>";
		} else {
			$out .="<text><![CDATA[<table cellpadding='3' cellspacing='0'><tr><th>#</th><th>Container</th><th>Status</th><th>Width</th><th>Height</th></tr>$table</table>]]></text>";
//			$out .= "<checkboxes type='vertical' name='banner_list' label='The following are banner groups of this type are available, choose which groups to add this banner to.'>";
//			$out .= $this->gen_options($Array_value,$Array_labels,$Array_selected);
//			$out .= "</checkboxes>";
		}
        $this->parent->db_pointer->database_free_result($result);
		$out .="			</section>";
		$out .= $this->location_tab($all_locations, $set_inheritance,$menu_locations, "locate_tab");
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		/*************************************************************************************************************************
        * return the screen structure to the engine
        *************************************************************************************************************************/
		return $out;
	}
	/*************************************************************************************************************************
    * save the banner types
    *************************************************************************************************************************/
	function module_manage_banner_save($parameters){
		if($this->manage_banners==0){
			return "";
		}
		/*************************************************************************************************************************
        * get parameters from from
        *************************************************************************************************************************/
		$be_identifier				= $this->check_parameters($parameters, "identifier"					, -1);
		$be_label 					= htmlentities($this->check_parameters($parameters, "be_txt_label"	, ""));
		$be_url 					= htmlentities($this->check_parameters($parameters, "be_url"		, ""));
		$be_date_starts				= $this->check_date($parameters, "be_date_starts"					, "0000-00-00 00:00:00");
		$be_date_expires			= $this->check_date($parameters, "be_date_expires"					, "0000-00-00 00:00:00");
		$be_status					= $this->check_parameters($parameters, "be_status"					, -1);
		$banner_list				= $this->check_parameters($parameters, "banner_list" 				, Array());
		$file_attached				= $this->check_parameters($parameters, "id"							, Array());
		$be_max_click_through		= $this->check_parameters($parameters, "be_max_click_through"		, -1);
		$be_max_impressions			= $this->check_parameters($parameters, "be_max_impressions" 		, -1);
		$be_type					= $this->check_parameters($parameters, "be_type"			 		, -1);
		$be_txt_label				= $this->check_parameters($parameters, "be_txt_label"			 	, "");
		$be_txt_des1				= $this->check_parameters($parameters, "be_txt_des1"			 	, "");
		$be_txt_des2				= $this->check_parameters($parameters, "be_txt_des2"			 	, "");
		$be_txt_url					= $this->check_parameters($parameters, "be_txt_url"			 		, "");
		$be_random_toggle			= $this->check_parameters($parameters, "be_random_toggle"			, 0);
		/*************************************************************************************************************************
        * get publish locations
        *************************************************************************************************************************/
		$all_locations			= $this->check_parameters($parameters, "all_locations");
		$set_inheritance		= $this->check_parameters($parameters, "set_inheritance");
		$menu_locations			= $this->check_parameters($parameters, "menu_locations", Array());
		/*************************************************************************************************************************
        * save data
        *************************************************************************************************************************/
		if($be_identifier==-1){
			$be_identifier = $this->getUid();
			$sql = "insert into banner_entry (be_identifier, be_client, be_random_toggle, be_url, be_status, be_max_page_impressions, be_max_click_through, be_type, be_label, be_date_starts, be_date_expires, be_txt_label, be_txt_des1, be_txt_des2, be_txt_url) 
						values
						('$be_identifier', '$this->client_identifier', '$be_random_toggle', '$be_url', $be_status, $be_max_impressions, $be_max_click_through, $be_type, '$be_label', '$be_date_starts', '$be_date_expires', '$be_txt_label', '$be_txt_des1', '$be_txt_des2', '$be_txt_url')";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("LAYOUT_MENU_LOCATION_SETTINGS_SAVE", Array(
					"module"			=> $this->webContainer."ENTRY",
					"identifier"		=> $be_identifier,
					"set_inheritance"	=> $set_inheritance,
					"all_locations"		=> $all_locations,
					"cmd"				=> "ADD"
				)
			);
		} else {
			$sql = "update banner_entry set 
						be_label					= '$be_label',
						be_random_toggle			= '$be_random_toggle',
						be_url						= '$be_url',
						be_status					= '$be_status',
						be_max_page_impressions		= '$be_max_impressions',
						be_max_click_through		= '$be_max_click_through',
						be_date_expires				= '$be_date_expires',
						be_date_starts				= '$be_date_starts', 
						be_txt_label				= '$be_txt_label', 
						be_txt_des1				= '$be_txt_des1', 
						be_txt_des2				= '$be_txt_des2', 
						be_txt_url				= '$be_txt_url'
					where 
						be_identifier = '$be_identifier' and 
						be_client = $this->client_identifier
					";
			$this->parent->db_pointer->database_query($sql);
			$this->call_command("LAYOUT_MENU_LOCATION_SETTINGS_SAVE", Array(
					"module"			=> $this->webContainer."ENTRY",
					"identifier"		=> $be_identifier,
					"set_inheritance"	=> $set_inheritance,
					"all_locations"		=> $all_locations,
					"cmd"				=> "EDIT"
				)
			);
		}
		$this->call_command("FILES_MANAGE_MODULE", Array("file_identifier" => $file_attached, "owner_id"=>$be_identifier, "owner_module"=>$this->webContainer));
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->webContainer."ENTRY",
				"identifier"	=> $be_identifier,
				"all_locations"	=> $all_locations
			)
		);
		/**
		* Save inheritance
		*/
		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance($this->webContainer."DISPLAY",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=>$child_locations,
					"module"		=> $this->webContainer."ENTRY",
					"identifier"	=> $be_identifier,
					"all_locations"	=> $all_locations,
					"delete"		=> 0
				)
			);
			$this->set_inheritance(
				$this->webContainer."DISPLAY",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"			=> $this->webContainer,
					"condition"			=> "mls_set_inheritance =1 and ",
					"client_field"		=> "mls_client",
					"table"				=> "menu_location_settings",
					"primary"			=> "mls_identifier"
					)
				).""
			);
		}
		$sql = "delete from banner_entry_grouping where beg_client = $this->client_identifier and beg_banner = $be_identifier";
		$this->parent->db_pointer->database_query($sql);
		$l = count($banner_list);
		for($i=0 ; $i<$l; $i++){
			$sql = "insert into banner_entry_grouping (beg_banner, beg_client, beg_list, beg_type) values ($be_identifier, $this->client_identifier, ".$banner_list[$i].", $be_type)";
			$this->parent->db_pointer->database_query($sql);
		}
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$this->tidyup_display_commands(
			Array(
				"seperate" => 1, 
				"all_locations" => $set_inheritance, 
				"tidy_table" => "banner_entry", 
				"tidy_field_starter" => "bl_", 
				"tidy_webobj" => $this->webContainer."DISPLAY", 
				"tidy_module" => $this->webContainer
			)
		);

		//$this->exitprogram();
	}
	/*************************************************************************************************************************
    * save the banner types
    *************************************************************************************************************************/
	function module_manage_banner_remove($parameters){
		if($this->manage_banners==0){
			return "";
		}
		/*************************************************************************************************************************
        * get banner id to delete
        *************************************************************************************************************************/
		$be_identifier	= $this->check_parameters($parameters, "identifier", -1);
		/*************************************************************************************************************************
        * save data
        *************************************************************************************************************************/
		if($be_identifier==-1){
			return "";
		} 
		/*************************************************************************************************************************
        * remove the banner
        *************************************************************************************************************************/
		$sql = "delete from banner_entry where be_identifier='$be_identifier' and be_client=$this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		 
		/*************************************************************************************************************************
        * remove banner to group list association
        *************************************************************************************************************************/
		$sql = "delete from banner_entry_grouping where beg_banner=$be_identifier and beg_client=$this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		/*************************************************************************************************************************
        * remove banner log
        *************************************************************************************************************************/
		$sql = "delete from banner_log where bnrlog_banner=$be_identifier and bnrlog_client=$this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		/*************************************************************************************************************************
        * remove file association
        *************************************************************************************************************************/
		$this->call_command("FILES_MANAGE_MODULE", 
			Array(
				"file_identifier" => Array(),  // empty array removes association
				"owner_id"=>$be_identifier, 
				"owner_module"=>$this->webContainer
			)
		);
	}
	/*************************************************************************************************************************
    * list the banner and filter as appropriate
    *************************************************************************************************************************/
	function module_manage_banner_list($parameters){
		if($this->manage_banners==0){
			return "";
		}
		$list = $this->check_parameters($parameters,"identifier",-1);
		if($list==-1){
			$sql = "select * from banner_entry 
						inner join banner_types on bt_identifier = be_type and be_client=bt_client
					where be_client=$this->client_identifier";
		} else {
			$sql = "select * from banner_entry 
						inner join banner_types on bt_identifier = be_type and be_client=bt_client
						inner join banner_entry_grouping on beg_banner = be_identifier and bl_identifier=beg_list
						inner join banner_list on bt_identifier = bl_type and bl_client=bt_client
					where be_client=$this->client_identifier and bl_identifier = $list";
		}
		$result = $this->parent->db_pointer->database_query($sql);
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));}
			$this->page_size=20;
			$prev = $this->page_size;
			$number_of_records = $this->parent->db_pointer->database_num_rows($result);
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$this->call_command("DB_SEEK",array($result,$goto));
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
			$variables["as"]			= "table";
			$variables["PAGE_BUTTONS"]	= Array();
//			if($list!=-1){
				$variables["PAGE_BUTTONS"][0] = Array("ADD",$this->module_command."MANAGE_BANNER&amp;$list=$list", ADD_NEW);
//			}
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
//			$variables["as"]				= "table";
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			//			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
			$start_page=intval($page / $this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page + $this->page_size)>$num_pages){
				$end_page	 =	$num_pages;
			}else{
				$end_page	=	$this->page_size;
			}
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= $this->filter($parameters);
			$variables["HEADER"]			= LOCALE_BANNER_ENTRY_LIST;
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$label 			= $this->check_parameters($r,"be_label","");
				$type_label		= $this->check_parameters($r,"bt_label","");
				$total			= $this->check_parameters($r,"total",0);
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["be_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,				$label, "TITLE"),
						Array(LOCALE_STATUS,			($r["be_status"]==1?LOCALE_LIVE:LOCALE_NOT_LIVE), "SUMMARY"),
						Array(LOCALE_BANNER_LIST_SIZE,	($r["bt_width"]==0?LOCALE_BANNER_UNLIMITED:$r["bt_width"])." x ".($r["bt_height"]==0?LOCALE_BANNER_UNLIMITED:$r["bt_height"]), "SUMMARY"),
						Array(LOCALE_BANNER_PAGES,		$r["be_cur_page_impressions"], ""),
						Array(LOCALE_BANNER_MAX_PAGES,	($r["be_max_page_impressions"]==-1?LOCALE_BANNER_UNLIMITED:$r["be_max_page_impressions"]), ""),
						Array(LOCALE_BANNER_CLICKS,		$r["be_cur_click_through"], ""),
						Array(LOCALE_BANNER_MAX_CLICKS,	($r["be_max_click_through"]==-1?LOCALE_BANNER_UNLIMITED:$r["be_max_click_through"]), ""),
						Array(LOCALE_BANNER_START,		($r["be_date_starts"]=="0000-00-00 00:00:00"?LOCALE_BANNER_NO_STARTS:Date("d M Y",strtotime($r["be_date_starts"]))), ""),
						Array(LOCALE_BANNER_END,		($r["be_date_expires"]=="0000-00-00 00:00:00"?LOCALE_BANNER_NO_EXPIRY:Date("d M Y",strtotime($r["be_date_expires"]))), "")
					)
				);
				if ($this->manage_banners == 1){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."MANAGE_BANNER&amp;type=".$r["be_type"],EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."MANAGE_BANNER_REMOVE&amp;type=".$r["be_type"],REMOVE_EXISTING);
					$this->view_logs=0;
					if($this->view_logs==1){
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("LOG",$this->module_command."VIEW_LOG_WEEK",LOCALE_VIEW_LOG_WEEK);
					}
				}
			}
			$this->page_size = $prev;
			
			return $this->generate_list($variables);
		}
	}
	/*************************************************************************************************************************
    * 								B A N N E R   L O G   M A N A G E M E N T   F U N C T I O N S
    *************************************************************************************************************************/
	
	/*************************************************************************************************************************
    * display the log for a banner group
    *************************************************************************************************************************/
	function display_banner_group_log($parameters){
//		$start = Date("Y-m-d", mktime (0,0,0,date("m")  ,date("d")-30,date("Y"));
		$identifier 	= $this->check_parameters($parameters,"identifier",-1);
		$type 			= $this->check_parameters($parameters,"type",-1);
		$days 			= $this->check_parameters($parameters,"days",7);
		$out			= "";
		$total 			= 0;
		$list_results	= Array();
		$day_list		= array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
		$big			= "";
		$days			= date("w");
		$now 			= date("Y/m/d H:i:s",strtotime("-$days days"));
		$now			= date("Y/m/d 00:00:00",strtotime("-$days days"));
		$now_prev 		= date("Y/m/d 00:00:00",strtotime("-".($days+7)." days"));
		/*************************************************************************************************************************
        * get this weeks logs for this banner
        *************************************************************************************************************************/
		if($identifier != -1){
			$sql = "select bnrlog_day as todays_uid, bnrlog_clicks, bnrlog_pages from banner_log where bnrlog_day >= '$now' and bnrlog_banner=$identifier and bnrlog_client = $this->client_identifier";
		}
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$total 	= 0;
		if ($result){
			$total	= 0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$day = date("D, jS#\&\_\f\i\l\\t\e\\r\_\y\e\a\\r\=Y\&\_\f\i\l\\t\e\\r\_\m\o\\n\\t\h\=m\&\_\f\i\l\\t\e\\r\_\d\a\y\=d",strtotime($r["todays_uid"]));
				$list_results[$day]["LOCALE_STATS_PAGE_HITS"]	= $r["bnrlog_pages"];
				$list_results[$day]["LOCALE_STATS_CLICKS"]		= $r["bnrlog_clicks"];
			}
		}
		$this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
	    * generate xml
	    *************************************************************************************************************************/
		$total_hits=0;
		$totals = array();
		$biggest = array();
		foreach ($list_results as $day => $value){
			$list = split("#",$day);
			$out .= "<stat_entry>";
			$out .= "<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[".$list[0]."]]></attribute>";
			foreach ($value as $name => $val){		
				$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
				if (empty($totals[$name])){
					$totals[$name] = $val;
				}else{
					$totals[$name] += $val;
				}
				if (empty($biggest[$name])){
					$biggest[$name] = $val;
				}else{
					if ($val > $biggest[$name] ){
						$biggest[$name] = $val;
					}
				}
			}
			
			$out .= "
			<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[?command=USERACCESS_VIEW_THIS_DAY".$list[1]."]]></attribute>
			</stat_entry>";
		}
		if (strlen($out)>0){
			$val_average = round($this->check_parameters($totals,"LOCALE_STATS_PAGE_HITS",1) / $this->check_parameters($totals,"LOCALE_STATS_VISITORS",1),2);
			$out .= "<stat_total>
					<attribute name=\"\" show=\"YES\" link=\"\"><![CDATA[".LOCALE_STATS_TOTAL."]]></attribute>
					";
			foreach ($totals as $name => $val){		
				if ($name=="LOCALE_STATS_AVERAGE"){
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val_average]]></attribute>";
				} else {
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
				}
			}
			$out .= "</stat_total>";
		}
		
		if (strlen($out)>0){
			//$out .= $this->publish_total($totals, "LOCALE_STATS_PAGE_HITS", "LOCALE_STATS_VISITORS");
			$big= "<stat_biggest>
					<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
					";
			foreach ($biggest as $name => $val){		
				$big .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
			}
			$big .= "<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute></stat_biggest>";
		}
		$thisweek = $out;
		/*************************************************************************************************************************
        * get last weeks logs for this banner
        *************************************************************************************************************************/
		$total 	= 0;
		$out 	= "";
		$c		= 0;
		$list_results=Array();
		if($identifier != -1){
			$sql = "select bnrlog_day as todays_uid, bnrlog_clicks, bnrlog_pages from banner_log where bnrlog_day >= '$now_prev' and bnrlog_day < '$now' and bnrlog_banner=$identifier and bnrlog_client = $this->client_identifier";
		}
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$total 	= 0;
		if ($result){
			$total	= 0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$c++;
				$day = date("D, jS#\&\_\f\i\l\\t\e\\r\_\y\e\a\\r\=Y\&\_\f\i\l\\t\e\\r\_\m\o\\n\\t\h\=m\&\_\f\i\l\\t\e\\r\_\d\a\y\=d",strtotime($r["todays_uid"]));
				$list_results[$day]["LOCALE_STATS_PAGE_HITS"]	= $r["bnrlog_pages"];
				$list_results[$day]["LOCALE_STATS_CLICKS"]		= $r["bnrlog_clicks"];
			}
		}
		$this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
	    * generate xml
	    *************************************************************************************************************************/
		$total_hits=0;
		$totals = array();
		$biggest = array();
		foreach ($list_results as $day => $value){
			$list = split("#",$day);
			$out .= "<stat_entry>";
			$out .= "<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[".$list[0]."]]></attribute>";
			foreach ($value as $name => $val){		
				$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
				if (empty($totals[$name])){
					$totals[$name] = $val;
				}else{
					$totals[$name] += $val;
				}
				if (empty($biggest[$name])){
					$biggest[$name] = $val;
				}else{
					if ($val > $biggest[$name] ){
						$biggest[$name] = $val;
					}
				}
			}
			
			$out .= "
			<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[?command=USERACCESS_VIEW_THIS_DAY".$list[1]."]]></attribute>
			</stat_entry>";
		}
		if (strlen($out)>0){
			$val_average = round($this->check_parameters($totals,"LOCALE_STATS_PAGE_HITS",1) / $this->check_parameters($totals,"LOCALE_STATS_VISITORS",1),2);
			$out .= "<stat_total>
					<attribute name=\"\" show=\"YES\" link=\"\"><![CDATA[".LOCALE_STATS_TOTAL."]]></attribute>
					";
			foreach ($totals as $name => $val){		
				if ($name=="LOCALE_STATS_AVERAGE"){
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val_average]]></attribute>";
				} else {
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
				}
			}
			$out .= "</stat_total>";
		}
		if (strlen($out)>0){
//			$out .= $this->publish_total($totals, "LOCALE_STATS_PAGE_HITS", "LOCALE_STATS_VISITORS");
			$big= "<stat_biggest>
					<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
					";
			foreach ($biggest as $name => $val){		
				$big .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
			}
			$big .= "<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute></stat_biggest>";
		}
		$prev_week = $out;
		
		$page_options = "<page_options>";
		$page_options .= "<header><![CDATA[Banner advert week report]]></header>";
//		$page_options .= $this->call_command("USERACCESS_GENERATE_LINKS");
		$page_options .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."MANAGE_BANNER_LIST",LOCALE_CANCEL));
		$page_options .= "</page_options>";
		$page_options .= "<graphs><graph>2</graph><graph>3</graph></graphs>";
		$this_week="";
		if (strlen($thisweek)>0){
			$this_week = "<stat_results label=\"Web Site Traffic for this week\" total=\"$total\" link=\"".$this->module_command."VIEW_THIS_DAY\">".$thisweek."$big</stat_results>";
		}
		$prev_week ="";
		$prev_week = "<stat_results label=\"Previous week for comparison\" total=\"$total\" link=\"".$this->module_command."VIEW_THIS_DAY\">".$out."$big</stat_results></previous>";
		$output ="<module name=\"user_access\" display=\"stats\">$page_options".$this_week;
		if ($c!=0){
			$output .="	<previous><graphs><graph>2</graph><graph>3</graph></graphs>$prev_week";
		}
		$output .="</module>";
		return $output;
	}
	function publish_total($totals,$page_hits="",$visitors="",$blank=2){
		if ($this->check_parameters($totals,$page_hits,1)>0 && $this->check_parameters($totals,$visitors,1)>0){
			$val_average = round($this->check_parameters($totals,$page_hits,1) / $this->check_parameters($totals,$visitors,1),2);
			$out = "<stat_total>
				<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[".LOCALE_STATS_TOTAL."]]></attribute>
				";
			for ($i = 0; $i<$blank-2;$i++){
				$out .= "<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[]]></attribute>";
			}
			foreach ($totals as $name => $val){		
				if ($name == "LOCALE_STATS_FLAG"){
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"NO\" link=\"NO\"><![CDATA[$val]]></attribute>";
				}else if ($name=="LOCALE_STATS_AVERAGE"){
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val_average]]></attribute>";
				} else {
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
				}
			}
			$out .= "</stat_total>";
		} else {
			$out="";
		}
		return $out;
	}

}

?>