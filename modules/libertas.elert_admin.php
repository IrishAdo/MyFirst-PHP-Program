<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.(ELERT) - Email Alerts - 
* @date 03 Dec 2002
*/
/**
* This module is the module for sending ELERTS through the system
*
* modules/ Roles to do
*	PAGE
*		Author
*		Approver
*		Publisher
*		Web user
*	GUESTBOOK
*		Approver
*		Web user
*	INFORMATION_DIRECTORY
*		Approver
*		Web user
*	FORUM
*		Approver
*		Web user
*	COMMENTS
*		Approver
*		Web user
*	
*<code>
*| wo_type | wo_label                                                | wo_created          | wo_command                                | wo_all_locations | wo_show_label | wo_owner_module | wo_owner_id |
*|       1 | Display the Elert SignUp Form here                      | 0000-00-00 00:00:00 | ELERT_DISPLAY                             |                0 |             0 |                 |           0 |
*</code>
*/

class elert_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_type				= "__ADMIN__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_name_label			= "Elert Manager Module (Administration)";
	var $module_name				= "elert_admin";
	var $module_admin				= "1";
	var $module_channels			= "";
	var $searched					= 0;
	var $module_modify	 		= '$Date: 2005/02/22 12:56:13 $';
	var $module_version 			= '$Revision: 1.17 $';
	var $module_command				= "ELERTADMIN_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_ELERTS";
	
	
	/**
	*  Management Reports that are available
	*/
	var $module_reports				= Array(
//										array("PAGE_REPORT_LIST", LOCALE_PAGE_BASIC_REPORTS, LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL)
									  );
	/**
	*  Elert Lists Email Messages
	*/
	var $module_email_msgs 			= array(
		Array("PAGE_AUTHOR",1),
		Array("PAGE_APPROVER",1),
		Array("PAGE_PUBLISHER",1),
		Array("WEB_USER_PAGE",0)
	);
	/**
	*  Management Menu entries
	*/
	var $module_admin_options 		= array();
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access	= array(
		array("ELERTADMIN_ALL", 			"COMPLETE_ACCESS",""),
		array("ELERTADMIN_OPTABLE", 		"LOCALE_ELERT_ENABLE_DISABLE_ADMINLISTS"),
		array("ELERTADMIN_SECTION",			"LOCALE_ELERT_SECTION_LISTS"),
		array("ELERTADMIN_SIGNUP",			"LOCALE_ELERT_SIGNUP_LISTS")
	);
	
	
	/**
	*  Channel options
	*/
	var $module_display_options 	= array(
		Array("ELERT_DISPLAY", "Display the Elert SignUp Form here")
	);
	/**
	*  filter options
	*/
	var $display_options			= array(
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
	*  access permissions
	*/
	var $admin_access				= 0;
	var $section_access				= 0;
	var $signup_access				= 0;
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
			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
				return "ELERT_";//$this->webContainer;
			}
			if ($user_command==$this->module_command."GET_VERSION"){
				return $this->get_module_version();
			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->call_command("PRESENTATION_DISPLAY",$parameter_list);
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."EMAIL"){
				return $this->email_alerts($parameter_list);
			}
			if (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
				if ($user_command == $this->module_command."MENU_DISPLAY_OPTIONS"){
					return $this->display_channels($parameter_list);
				}
				if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
					return $this->module_admin_access_options(0);
				}
				if ($user_command==$this->module_command."ACCESS_OPTIONS"){
					return $this->module_admin_options(0);
				}
			}
			if ($this->module_admin_access==1){
				if ($user_command==$this->module_command."CREATE_ENTRY"){
					$this->module_list_create($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$this->module_command."OPTABLE_LIST"));
				}
				if ($user_command==$this->module_command."OPTOUT_LIST"){
					return $this->module_list_optout($parameter_list);
				}
				if ($user_command==$this->module_command."OPTION_TOGGLE"){
					$this->module_list_option_toggle($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."OPTOUT_LIST"));
				}
				
				if ($user_command==$this->module_command."OPTABLE_LIST"){
					return $this->module_list_optouts($parameter_list);
				}
				if ($user_command==$this->module_command."OPTABLE_TOGGLE"){
					$this->module_list_opttoggle($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."OPTABLE_LIST"));
				}
				if ($user_command==$this->module_command."OPTABLE_LIST_USERS"){
					return $this->module_list_optusers($parameter_list);
				}
				
				if ($user_command==$this->module_command."INHERIT"){
					return $this->module_section_inherit($parameter_list);
				}
				if ($user_command==$this->module_command."SECTION_LIST"){
					return $this->module_section_list($parameter_list);
				}
				if (($user_command==$this->module_command."SECTION_ADD") || ($user_command==$this->module_command."SECTION_EDIT")){
					return $this->module_section_modify($parameter_list);
				}
				if ($user_command==$this->module_command."SECTION_REMOVE"){
					$this->module_section_remove($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."SECTION_LIST"));
				}
				
				if ($user_command==$this->module_command."SECTION_SAVE"){
					$this->module_section_save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."SECTION_LIST"));
				}
				
				if ($user_command==$this->module_command."DEFINITION_LIST"){
					return $this->module_list_email_defs($parameter_list);
				}
				if ($user_command==$this->module_command."DEFINITION"){
					return $this->email_defintion($parameter_list);
				}
				if ($user_command==$this->module_command."DEFINITION_SAVE"){
					return $this->email_defintion_save($parameter_list);
				}
				
			}
		}else{
			// wrong command sent to system
			return "";
		}
	}
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
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* load the elert locale
		*/
		$this->load_locale("elert_admin");
		/**
		* define some variables 
		*/
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		$this->admin_access				= 0;
		$this->signup_access			= 0;

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
					("ELERTADMIN_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("ELERTADMIN_OPTABLE"==$access[$index])
				){
					$this->admin_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("ELERTADMIN_ALL"==$access[$index]) ||
					("ELERTADMIN_SIGNUP"==$access[$index])
				){
					$this->signup_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("ELERTADMIN_ALL"==$access[$index]) ||
					("ELERTADMIN_SECTION"==$access[$index])
				){
					$this->section_access=1;
				}
			}
		}
		if (($this->signup_access==1 || $this->section_access==1 || $this->admin_access==1) && (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files"))){
			$this->list_access			=1;
			$this->module_admin_access	=1;
		}

		$this->module_admin_options[count($this->module_admin_options)] = array("ELERTADMIN_OPTABLE_LIST"	, "MANAGE_OPTINOUT_LISTS","");
		$this->module_admin_options[count($this->module_admin_options)] = array("ELERTADMIN_SECTION_LIST"	, "LOCALE_ELERT_SECTION_LISTS","");
		$this->module_admin_options[count($this->module_admin_options)] = array("ELERTADMIN_DEFINITION_LIST", "LOCALE_ELERT_EMAIL_MSG","");
		$this->module_admin_options[count($this->module_admin_options)] = array("ELERTADMIN_OPTOUT_LIST"	, "LOCALE_ELERT_ADMIN_OPTOUT_LISTS","");
		$this->module_display_options = array(
			array("ELERT_DISPLAY",	LOCALE_ELERT_DISPLAY_CHANNEL)
		);
		$c = count($this->module_email_msgs)-1;
		if($this->call_command("ENGINE_HAS_MODULE",array("GUESTBOOK_"))==1){
			$this->module_email_msgs[$c++] = Array("GUESTBOOK_APPROVER",1);
			$this->module_email_msgs[$c++] = Array("WEB_USER_GUESTBOOK",0);
		}
		if($this->call_command("ENGINE_HAS_MODULE",array("FORUM_"))==1){
			$this->module_email_msgs[$c++] = Array("FORUM_APPROVER",1);
			$this->module_email_msgs[$c++] = Array("WEB_USER_FORUM",0);
		}
		if($this->call_command("ENGINE_HAS_MODULE",array("INFORMATION_"))==1){
			$this->module_email_msgs[$c++] = Array("INFODIR_APPROVER",1);
			$this->module_email_msgs[$c++] = Array("WEB_USER_INFODIR",0);
		}
		if($this->call_command("ENGINE_HAS_MODULE",array("COMMENTS_"))==1){
			$this->module_email_msgs[$c++] = Array("COMMENTS_APPROVER",1);
			$this->module_email_msgs[$c++] = Array("WEB_USER_COMMENTS",0);
		}
		if($this->call_command("ENGINE_HAS_MODULE",array("SHOP_"))==1){
			$this->module_email_msgs[$c++] = Array("SHOP_ORDER_PROCESSOR",1);
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
		*  PRESENTATION TABLES
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Table structure for table 'elert_signup_urls'
		*/
		
		$fields = array(
			array("esurl_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("esurl_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("esurl_user"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("esurl_url"					,"text"						,"NOT NULL"	,"default ''"),
			array("esurl_label"					,"varchar(255)"				,"NOT NULL"	,"default ''")
		);
		
		$primary ="esurl_identifier";
		$tables[count($tables)] = array("elert_signup_urls", $fields, $primary);

		/**
		* Table structure for table 'elert_signup'
		*/
		$fields = array(
			array("esu_identifier"				,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("esu_client"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("esu_user"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("esu_section"					,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);
		
		$primary ="esu_identifier";
		$tables[count($tables)] = array("elert_signup", $fields, $primary);

		/**
		* Table structure for table 'elert_sections'
		*/
		$fields = array(
			array("es_identifier"				,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("es_client"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("es_label"					,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("es_set_inheritance"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("es_all_locations"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("es_date_created"				,"datetime"					,"NOT NULL"	,"default ''")
		);
		
		$primary ="es_identifier";
		$tables[count($tables)] = array("elert_sections", $fields, $primary);

		/**
		*  ADMIN TABLES
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Table structure for table 'elert_optinout'
		*/
		$fields = array(
			array("eoio_identifier"				,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("eoio_client"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("eoio_label"					,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("eoio_type"					,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("eoio_status"					,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		
		$primary ="eoio_identifier";
		$tables[count($tables)] = array("elert_optinout", $fields, $primary);

		/**
		* Table structure for table 'elert_optin_list'
		*/
		$fields = array(
			array("eoil_identifier"				,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("eoil_client"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("eoil_user"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("eoil_list"					,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);
		
		$primary ="eoil_identifier";
		$tables[count($tables)] = array("elert_optin_list", $fields, $primary);
		return $tables;
	}
	
	function module_list_optouts($parameters){
		$this->page_size=50;
		$page				= $this->check_parameters($parameters,"page",1);
		$number_of_records	= count($this->module_email_msgs);
		$total_admin		=0;
		$list				= "";
		for($index=0; $index < $number_of_records; $index++){
			if ($this->module_email_msgs[$index][1]==1){
				$total_admin++;
				if ($list !=""){
				$list.=", ";
				}
				$list .= "'__EMAIL_".$this->module_email_msgs[$index][0]."__'";
			}
		}
		if ($list==""){
			$sql	= "select * from elert_optinout where eoio_client=$this->client_identifier";
		} else {
			$sql	= "select * from elert_optinout where eoio_type in ($list) and eoio_client=$this->client_identifier";
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			for($index=0; $index < $number_of_records; $index++){
				if ("__EMAIL_".$this->module_email_msgs[$index][0]."__"==$r["eoio_type"]){
					$this->module_email_msgs[$index][2] = $r["eoio_identifier"];
					$this->module_email_msgs[$index][3] = $r["eoio_label"];
					$this->module_email_msgs[$index][4] = $r["eoio_status"];
				}
			}
		}
		$variables["PAGE_BUTTONS"] = Array(
			Array("CANCEL", "ENGINE_SPLASH",LOCALE_CANCEL)
		);
		$goto = ((--$page)*$this->page_size);
		
		if (($goto!=0)&&($total_admin>$goto)){
			$pointer = $goto;
		}
		if ($goto+$this->page_size>$total_admin){
			$finish = $total_admin;
		}else{
			$finish = $goto+$this->page_size;
		}
//		$goto++;
		$page++;
		
		$num_pages=floor($total_admin / $this->page_size);
		$remainder = $total_admin % $this->page_size;
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
		$variables["HEADER"] 			= MANAGEMENT_ELERTS." - ".LOCALE_LIST;
		$variables["RESULT_ENTRIES"] =Array();
		$counter=0;
		for ($counter=0; $counter < $this->page_size; $counter++){
			if (($goto+$counter) < $number_of_records){
				if ($this->module_email_msgs[$counter+$goto][1]==1){
					$i = count($variables["RESULT_ENTRIES"]);
					if ($this->check_parameters($this->module_email_msgs[$counter+$goto],2,-1)==-1){
						$this->module_email_msgs[$counter+$goto][2]=-1;
					} 
					$title	= $this->check_parameters($this->module_email_msgs[$counter+$goto]	,3,constant("LOCALE_ELERT_".$this->module_email_msgs[$counter+$goto][0]));
					$id 	= $this->check_parameters($this->module_email_msgs[$counter+$goto]	,2,-1);
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=> $id,
						"attributes"	=> Array(
							Array(ENTRY_TITLE, $title ,"TITLE","NO")
						)
					);
					if($this->module_email_msgs[$counter+$goto][2]==-1){
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"] = Array(
							Array("CREATE", $this->module_command."CREATE_ENTRY&amp;list=".$this->module_email_msgs[$counter+$goto][0], LOCALE_ELERT_CREATE)
						);
						
					}else{
						if ($this->module_email_msgs[$counter+$goto][4]==0){
							$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_ELERT_STATUS, LOCALE_NO);
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"] = Array(
								Array("TOGGLE", $this->module_command."OPTABLE_TOGGLE&amp;opt=1", LOCALE_ELERT_ENABLE_OPTOUT),
								Array("EMAIL", 	$this->module_command."DEFINITION&amp;list=".$this->module_email_msgs[$counter+$goto][0], LOCALE_ELERT_DEFINE_EMAILS)
							);
						} else {
							$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_ELERT_STATUS, LOCALE_YES);
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"] = Array(
								Array("TOGGLE", $this->module_command."OPTABLE_TOGGLE&amp;opt=0", LOCALE_ELERT_DISABLE_OPTOUT),
								Array("EMAIL", 	$this->module_command."DEFINITION&amp;list=".$this->module_email_msgs[$counter+$goto][0], LOCALE_ELERT_DEFINE_EMAILS),
								Array("LIST", 	$this->module_command."OPTABLE_LIST_USERS&amp;list=".$this->module_email_msgs[$counter+$goto][0], LOCALE_LIST_USERS)
							);
						}
					}
				}
				/*"ENTRY_BUTTONS" => Array(
						Array("EDIT", $this->module_command."DEFINITION", EDIT_EXISTING)
					)*/
			}
		}
/*		
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$i]=Array(
					"identifier"	=> $r["eoio_identifier"],
					"attributes"	=> Array(
						Array(ENTRY_TITLE,$r["eoio_label"],"TITLE","NO"),
					)
				);
			}
		}*/
		$variables["as"]				= "table";
		$variables["NUMBER_OF_ROWS"]	= $total_admin;
		$variables["START"]				= $goto+1;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$out = $this->generate_list($variables);
		return $out;
	}

	function module_list_opttoggle($parameters){
		$identifier  = $this->check_parameters($parameters,"identifier");
		$opt  = $this->check_parameters($parameters,"opt");
		$sql = "update elert_optinout set eoio_status = $opt where eoio_identifier = $identifier and eoio_client=$this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		if ($opt==1){
			$sql = "select * from elert_optinout where eoio_identifier=$identifier and eoio_client =$this->client_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$type = $r["eoio_type"];
            }
            $this->call_command("DB_FREE",Array($result));
			if ($type == $this->module_constants["__EMAIL_PAGE_AUTHOR__"]){
				$cmd = "PAGE_AUTHOR";
				$start = "PAGE_";
			} 
			if ($type == $this->module_constants["__EMAIL_PAGE_APPROVER__"]){
				$cmd = "PAGE_APPROVER";
				$start = "PAGE_";
			} 
			if ($type == $this->module_constants["__EMAIL_PAGE_PUBLISHER__"]){
				$cmd = "PAGE_PUBLISHER";
				$start = "PAGE_";
			}
			if ($type == $this->module_constants["__EMAIL_GUESTBOOK_APPROVER__"]){
				$cmd = "GUESTBOOKADMIN_APPROVER";
				$start = "GUESTBOOKADMIN_";
			}
			if ($type == $this->module_constants["__EMAIL_FORUM_APPROVER__"]){
				$cmd = "FORUM_APPROVER";
				$start = "FORUMADMIN_";
			}
			if ($type == $this->module_constants["__EMAIL_INFODIR_APPROVER__"]){
				$cmd = "INFORMATIONADMIN_APPROVER";
				$start = "INFORMATIONADMIN_";
			}
			if ($type == $this->module_constants["__EMAIL_COMMENTS_APPROVER__"]){
				$cmd = "COMMENTS_APPROVER";
				$start = "COMMENTSADMIN_";
			}
			if ($type == $this->module_constants["__EMAIL_SHOP_ORDER_PROCESSOR__"]){
				$cmd = "SHOP_ORDER_PROCESSOR";
				$start = "SHOP_";
			}
			/*
			if ($type == $this->module_constants["__EMAIL_USER_APPROVER__"]){
				$cmd = "USERS_APPROVER";
				$start = "USERS_";
			}
			*/
			$sql="select distinct groups_belonging_to_user.user_identifier, email_address from group_data  
  inner join group_access on access_group = group_data.group_identifier and access_code in ('$cmd',  'ALL', '".$start."ALL')
  inner join groups_belonging_to_user on groups_belonging_to_user.group_identifier= group_data.group_identifier and group_client=groups_belonging_to_user.client_identifier
  left outer  join contact_data on contact_user = groups_belonging_to_user.user_identifier and contact_client = groups_belonging_to_user.client_identifier
  left outer join email_addresses on contact_identifier = email_contact and email_client = contact_client
where group_client=$this->client_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$sql = "insert into elert_optin_list (eoil_list, eoil_client,eoil_user) values ($identifier, $this->client_identifier, ".$r["user_identifier"].")";
				$this->call_command("DB_QUERY",Array($sql));
			}
			$this->call_command("DB_FREE",Array($result));
		} else {
			$sql = "delete from elert_optin_list where eoil_list = $identifier and eoil_client=$this->client_identifier";
			$this->call_command("DB_QUERY",Array($sql));
		}
	}

	function module_list_optusers($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier",-1);
		$display_tab = "";
		$sql = "select * from elert_optin_list inner join contact_data on contact_user = eoil_user and contact_client = eoil_client where eoil_list = $identifier and eoil_client = $this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$list ="";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$list.="<option selected='true' value='".$r["eoil_user"]."'>".$r["contact_first_name"].", ".$r["contact_last_name"]."</option>";
        }
        $this->call_command("DB_FREE",Array($result));
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options><header>" . LOCALE_ELERTS_USER_MANAGER . "</header>";
		$out .= "	<button command=\"ELERTADMIN_OPTABLE_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"/>";
		$out .= "</page_options>";
		$out .= "<form name=\"elert_manager\" method=\"post\">";
		$out .= "<page_sections>";
		$out .= "	<section label=\"".LOCALE_LIST_USERS."\"";
		if ($display_tab=="content"){
			$out .= " selected='true'";
		}
		$out .= ">";
		$out .= "<list label=\"".LOCALE_CURRENT_LIST_USERS."\" name=\"eoil_users\">$list</list>";
		$out .= "</section>";
		$out .= "</page_sections>";
//		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
		$out .= "</form>";
		$out .="</module>";
		return $out;

	}
	function module_section_list($parameters){
		$page  = $this->check_parameters($parameters,"page",1);
		$sql = "select * from elert_sections where es_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables["PAGE_BUTTONS"] = Array(
//			Array("CANCEL", $this->module_command."GROUP_LIST",LOCALE_CANCEL),
			Array("ADD", $this->module_command."SECTION_ADD",ADD_NEW)
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
			$variables["HEADER"] 			= MANAGEMENT_ELERTS." - ".LOCALE_LIST;
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$i]=Array(
					"identifier"	=> $r["es_identifier"],
					"attributes"	=> Array(
						Array(ENTRY_TITLE,$r["es_label"],"TITLE","NO"),
					),
					"ENTRY_BUTTONS" => Array(
						Array("EDIT"	, $this->module_command."SECTION_EDIT", LOCALE_EDIT),
						Array("REMOVE"	, $this->module_command."SECTION_REMOVE", REMOVE_EXISTING)
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
	
	function module_section_modify($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier",-1);
		$display_tab	= $this->check_parameters($parameters,"display_tab","");
		$all_locations=0;
		$set_inheritance=1;
		$menu_locations="";
		$section_label = "";
		if($identifier !=-1){
			$section_label="";
			$sql = "select * from elert_sections where es_client = $this->client_identifier and es_identifier = '$identifier'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$section_label =$r["es_label"];
            	$set_inheritance =$r["es_set_inheritance"];
            	$all_locations =$r["es_all_locations"];
            }
		}
		$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
			Array(
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier
			)
		);

		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options><header>" . LOCALE_ELERTS_SECTION_MANAGER . "</header>";
		$out .= "	<button command=\"ELERTADMIN_SECTION_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"/>";
		$out .= "</page_options>";
		$out .= "<form name=\"elert_manager\" method=\"post\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"ELERTADMIN_SECTION_SAVE\"/>";
		$out .= "<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
		$out .= "<page_sections>";
		$extra = "<input type='text' size='255' label='".LOCALE_ELERT_SECTION_QUESTION."' name='section_label'><![CDATA[$section_label]]></input>";
		$out .= $this->location_tab($all_locations, $set_inheritance, $menu_locations, $display_tab, $extra, 
			Array(
				"label" => LOCALE_ELERT_SECTION_DEFINITION,
				"all_locations_label" => LOCALE_ELERT_ALL_LOCATIONS,
				"what_locations_label" => LOCALE_ELERT_WHAT_LOCATIONS,
				"inherit_locations_label" => LOCALE_ELERT_INHERIT_LOCATIONS
			)
		);
		$out .= "</page_sections>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
		$out .= "</form>";
		$out .="</module>";
		return $out;
		
	}
	function module_section_save($parameters){
		$identifier			= $this->check_parameters($parameters,"identifier",-1);
		$es_label 			= $this->check_parameters($parameters,"section_label",-1);
		$es_set_inheritance	= $this->check_parameters($parameters,"set_inheritance",-1);
		$es_all_locations	= $this->check_parameters($parameters,"all_locations",-1);
		$menu_locations		= $this->check_parameters($parameters,"menu_locations", Array());
		$es_date_created 	= $this->libertasGetDate("Y/m/d H:i:s");
		if ($identifier==-1){
			$sql ="insert into elert_sections (es_all_locations, es_set_inheritance, es_label, es_client, es_date_created) values ('$es_all_locations', '$es_set_inheritance', '$es_label', $this->client_identifier, '$es_date_created')";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "select * from elert_sections 
			where 
				es_all_locations='$es_all_locations' and 
				es_set_inheritance='$es_set_inheritance' and
				es_label='$es_label' and 
				es_client=$this->client_identifier and 
				es_date_created= '$es_date_created'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$identifier =$r["es_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			/*
			array("es_identifier"				,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("es_client"					,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("es_label"					,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("es_set_inheritance"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("es_all_locations"			,"unsigned integer"			,"NOT NULL"	,"default '0'")
			*/
		} else {
			$sql = "update elert_sections 
			set 
				es_all_locations='$es_all_locations',
				es_set_inheritance='$es_set_inheritance',
				es_label='$es_label'
				where 
				es_client=$this->client_identifier and 
				es_identifier= '$identifier'";
			$this->call_command("DB_QUERY",Array($sql));
		}
		
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier,
				"all_locations"	=> $es_all_locations
			)
		);
		if ($es_set_inheritance==1){
			$child_locations = $this->add_inheritance("POLLS_DISPLAY",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=> $child_locations,
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier,
					"all_locations"	=> $es_all_locations,
					"delete"		=> 0
				)
			);
			$this->set_inheritance(
				"ELERTADMIN_SECTION",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"=> $this->webContainer,
					"condition"=> "es_set_inheritance =1 and ",
					"client_field"=> "es_client",
					"table"	=> "elert_sections",
					"primary"=> "es_identifier"
					)
				).""
			);
		}
	}

	function module_section_inherit($parameters){
		$menu_id		= $this->check_parameters($parameters,"menu_identifier",-1);
		$menu_parent 	= $this->check_parameters($parameters,"menu_parent",-1);
		$this->call_command("LAYOUT_MENU_TO_OBJECT_INHERIT",Array(
			"menu_location"	=> $menu_id,
			"menu_parent"	=> $menu_parent,
			"module"		=> $this->webContainer,
			"condition"		=> "es_set_inheritance=1 and ",
			"client_field"	=> "es_client",
			"table"			=> "elert_sections",
			"primary"		=> "es_identifier"
			)
		);
	}

	function module_section_remove($parameters){
		$id		= $this->check_parameters($parameters,"identifier",-1);
		$sql	= "delete from elert_sections where es_identifier=$id and es_client = $this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$sql	= "delete from elert_signup	where esu_section=$id and esu_client = $this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$this->call_command("LAYOUT_MENU_TO_OBJECT_REMOVE", Array(
				"module"		=>	$this->webContainer, 
				"identifier"	=>	$id
			)
		);
	}

	function get_index($key){
		$id	= -1;
		$m	= count($this->module_email_msgs);
		for($index=0;$index<$m;$index++){
			if ($this->module_email_msgs[$index][0]==$key){
				$id=$index;
			}
		}
		return $id;
	}
	function email_alerts($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__, print_r($parameters, true)));
		}
		$type				= $this->check_parameters($parameters,"type","__SYSTEM__");
		$identifier			= $this->check_parameters($parameters,"identifier",-1);
		$msg				= $this->check_parameters($parameters,"msg");
		$emailbody			= $this->check_parameters($parameters,"emailbody");
		$url				= $this->check_parameters($parameters,"url");
		$wc					= $this->check_parameters($parameters,"webContainer");
		$cmd				= "";
		$disclaimer 		= "";
		$email_addresses	= Array();
		$pos 				= 0;
		$Default_Message	= "";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$msg		= "";
		if ($identifier == -1){
			return -1;
		} else {
			/*************************************************************************************************************************
			* 	EMAIL SETUP & DEFINITION
			*************************************************************************************************************************/
			
			/*************************************************************************************************************************
			* 												USERS
			*************************************************************************************************************************/
/*
			if ($type == $this->module_constants["__EMAIL_USER_APPROVER__"]){
				$pay	= $this->check_parameters($parameters,"payment","invoice");
				$cmd = "USER_APPROVER";
				$subject = "A user has updated thier profile.";
				$fname = "$data_files/email_notify_".$this->client_identifier."_USER_APPROVER.txt";
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				$access_type = "USER";
			} 
*/
			/*************************************************************************************************************************
			* 												S H O P
			*************************************************************************************************************************/
			if ($type == $this->module_constants["__EMAIL_SHOP_ORDER_PROCESSOR__"]){
				$pay	= $this->check_parameters($parameters,"payment","invoice");
				$cmd = "SHOP_ORDER_PROCESSOR";
				if($pay=="approved"){
					$subject = "Order submission noticifaction - Payment approved by credit card ($identifier).";
				} else if($pay=="deny"){
					$subject = "Order submission noticifaction - Payment denied by credit card ($identifier).";
				} else {
					$subject = "Order submission noticifaction - Request payment by invoice ($identifier).";
				}
				$fname = "$data_files/email_notify_".$this->client_identifier."_SHOP_ORDER_PROCESSOR.txt";
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if($msg=="") {
					$msg = "An order has been submitted reference number ($identifier)";
				}
				if($emailbody!=""){
					$msg = $emailbody;
				}
				$access_type = "SHOP";
			} 
			/*************************************************************************************************************************
			* 												F O R U M
			*************************************************************************************************************************/
			if ($type == $this->module_constants["__EMAIL_WEB_USER_FORUM__"]){
				$cmd = "";
				$subject = "Site update [".$this->parent->domain."] - Forum updated.";
				$fname = "$data_files/email_notify_".$this->client_identifier."_WEB_USER_FORUM.txt";
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if($msg=="") {
					$msg = "A new entry has been published to the forum.\nThe entry is called '[[title]]'\nYou can find it here\n[[url]]\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\nThis email was generated at the above web site for a service that you\nsigned up for. You can remove yourself from this Elert list at any time\nby logging into your account.";
				}
				$access_type = "FORUM";
			} 
			if ($type == $this->module_constants["__EMAIL_FORUM_APPROVER__"]){
				$cmd = "FORUM_APPROVER";
				$subject = "Site update [".$this->parent->domain."] - Forum update requires your approval";
				$fname = "$data_files/email_notify_".$this->client_identifier."_FORUM_APPROVER.txt";
//				$url = "http://".$this->parent->domain.$this->parent->base.$url
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if ($msg=="") {
					$msg = "A new entry in the forum has been sent for your approval\nThe entry is called '[[title]]'";
					$msg.="\nTo approve this entry login and then goto the following url\n\n[[url]]";
				}
				$access_type = "FORUM";
			} 
			/*************************************************************************************************************************
			* 								I N F O R M A T I O N   D I R E C T O R Y
			*************************************************************************************************************************/
			if ($type == $this->module_constants["__EMAIL_WEB_USER_INFODIR__"]){
				$cmd = "";
				$subject = "Site update [".$this->parent->domain."] - Information Directory updated.";
				$fname = "$data_files/email_notify_".$this->client_identifier."_WEB_USER_INFODIR.txt";
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if($msg=="") {
					$msg = "A new entry has been published to the information directory.\nThe entry is called '[[title]]'\nYou can find it here\n[[url]]\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\nThis email was generated at the above web site for a service that you\nsigned up for. You can remove yourself from this Elert list at any time\nby logging into your account.";
				}
				$access_type = "INFODIR";
			} 
			if ($type == $this->module_constants["__EMAIL_INFODIR_APPROVER__"]){
				$cmd = $wc."APPROVER";
				$subject = "Site update [".$this->parent->domain."] - Information Directory update requires your approval";
				$fname = "$data_files/email_notify_".$this->client_identifier."_INFODIR_APPROVER.txt";
//				$url = "http://".$this->parent->domain.$this->parent->base.$url
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if ($msg=="") {
					$msg = "A new entry in the information directory has been sent for your approval\nThe entry is called '[[title]]'";
					$msg.="\nTo approve this entry login and then goto the following url\n\n[[url]]";
				}
				if($emailbody!=""){
					$msg = $emailbody;
				}

				$access_type = $wc;
			} 
			/*************************************************************************************************************************
			* 											G U E S T B O O K
			*************************************************************************************************************************/
			if ($type == $this->module_constants["__EMAIL_WEB_USER_GUESTBOOK__"]){
				$cmd = "";
				$subject = "Site update [".$this->parent->domain."] - Guestbook updated.";
				$fname = "$data_files/email_notify_".$this->client_identifier."_WEB_USER_GUESTBOOK.txt";
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if($msg=="") {
					$msg = "A guestbook entry has been Published to the web site\nThe guest book entry is called '[[title]]'\nYou can find it here\n[[url]]\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\nThis email was generated at the above web site for a service that you\nsigned up for. You can remove yourself from this Elert list at any time\nby logging into your account.";
/*					if ($url!=""){
						$msg.="\nhttp://".$this->parent->domain.$this->parent->base.$url;
					}*/
				}
				$access_type = "GUESTBOOK";
			} 
			if ($type == $this->module_constants["__EMAIL_GUESTBOOK_APPROVER__"]){
				$cmd = "GUESTBOOKADMIN_APPROVER";
				$subject = "Site update [".$this->parent->domain."] - Guestbook updated sent for approval";
				$fname = "$data_files/email_notify_".$this->client_identifier."_GUESTBOOK_APPROVER.txt";
//				$url = "http://".$this->parent->domain.$this->parent->base.$url
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if ($msg=="") {
					$msg = "A guestbook entry has been sent for your approval\nThe guest book entry is called '[[title]]'";
					$msg.="\nTo approve this entry login and then goto the following url\n\n[[url]]";
				}
				$access_type = "GUESTBOOK";
			} 
			/*************************************************************************************************************************
			* 											C O M M E N T S
			*************************************************************************************************************************/
			if ($type == $this->module_constants["__EMAIL_WEB_USER_COMMENTS__"]){
				$cmd = "";
				$subject = "Site update [".$this->parent->domain."] - Page Comment updated.";
				$fname = "$data_files/email_notify_".$this->client_identifier."_WEB_USER_COMMENTS.txt";
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if($msg=="") {
					$msg = "A comment has been published to the web site\nThe comment entry is called '[[title]]'\nYou can find it here\n[[url]]\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\nThis email was generated at the above web site for a service that you\nsigned up for. You can remove yourself from this Elert list at any time\nby logging into your account.";
/*
					if ($url!=""){
						$msg.="\nhttp://".$this->parent->domain.$this->parent->base.$url;
					}
*/
				}
				$access_type = "COMMENTS";
			} 
			if ($type == $this->module_constants["__EMAIL_COMMENTS_APPROVER__"]){
				$cmd = "COMMENTSADMIN_APPROVER";
				$subject = "Site update [".$this->parent->domain."] - Comments updated sent for approval";
				$fname = "$data_files/email_notify_".$this->client_identifier."_COMMENTS_APPROVER.txt";
//				$url = "http://".$this->parent->domain.$this->parent->base.$url
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if ($msg=="") {
					$msg = "A comment has been sent for your approval\nThe comment entry is called '[[title]]'";
					$msg.="\nTo approve this entry login and then goto the following url\n\n[[url]]";
				}
				$access_type = "COMMENTS";
			} 
			/*************************************************************************************************************************
			* 												  P A G E
			*************************************************************************************************************************/
			if ($type == $this->module_constants["__EMAIL_PAGE_AUTHOR__"]){
				$cmd = "PAGE_AUTHOR";
				$subject = "Note to Author ";
				$fname = "$data_files/email_notify_".$this->client_identifier."_PAGE_AUTHOR.txt";
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
					$url = "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=PAGE_LIST&amp;status_filter=1&amp;identifier=$identifier";
				} 
				if ($msg=="") {
					$msg = "[[contact_first_name]],\n\nThe page entitled '[[title]]' has been sent back to you\nYou can find it here\n[[url]]";
				}
				$access_type = "PAGE";
			} 
			if ($type == $this->module_constants["__EMAIL_PAGE_APPROVER__"]){
				$cmd = "PAGE_APPROVER";
				$subject = "Site update [".$this->parent->domain."] - Page sent for approval";
				$fname = "$data_files/email_notify_".$this->client_identifier."_PAGE_APPROVER.txt";
				$url = "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=PAGE_LIST&amp;status_filter=2&amp;identifier=$identifier";
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if ($msg=="") {
					$msg = "A page has been sent for your approval\n\nThe page entitled '[[title]]' has been sent for Approval\nYou can find it here\n[[url]]";
				}
				$access_type = "PAGE";
			} 
			if ($type == $this->module_constants["__EMAIL_PAGE_PUBLISHER__"]){
				$cmd = "PAGE_PUBLISHER";
				$subject = "Site update [".$this->parent->domain."] - Page Approved";
				$fname = "$data_files/email_notify_".$this->client_identifier."_PAGE_PUBLISHER.txt";
				$url = "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=PAGE_LIST&amp;status_filter=3&amp;identifier=$identifier";
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if ($msg=="") {
					$msg = "A page has been approved for publishing\n\nThe page entitled '[[title]]' has been Approved\nYou can find it here\n[[url]]";
				}
				$access_type = "PAGE";
			}
			if ($type == $this->module_constants["__EMAIL_WEB_USER_PAGE__"]){
				$cmd = "";
				$subject = "Web Site Update [".$this->parent->domain."]";
				$msg="";
				$access_type = "PAGE";
				$fname = "$data_files/email_notify_".$this->client_identifier."_PAGE_AUTHOR.txt";
				if (file_exists($fname)){
					$fp = fopen($fname, 'r');
					$msg = fread($fp, filesize($fname));
					fclose($fp);
				} 
				if ($msg=="") {
					$msg = "\nThe page entitled '[[title]]' has been added to the site\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-\nThis email was generated at the above web site for a service that you\nsigned up for. You can remove yourself from this Elert list at any time\nby logging into your account.";
				}
			}
			/**************************************************************************************************************************
			* 	EMAIL SENDING
			**************************************************************************************************************************/
			
			/*************************************************************************************************************************
            * 															U S E R S
            *************************************************************************************************************************/
/*
			if ($access_type == "USER"){
				if($cmd!=""){
					$sql =   "select eoio_status, eoil_user from elert_optinout
									  left outer join elert_optin_list on eoio_client = eoil_client and eoio_identifier = eoil_list
								where eoio_client = $this->client_identifier and eoio_type = '$type' ";
					print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
					$result  		= $this->call_command("DB_QUERY",Array($sql));
					$list_users		= "";
					$eoio_status	= 0;
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
		            	if ($r["eoio_status"]==1){
							$eoio_status = $r["eoio_status"];
							if($list_users!=""){
								$list_users .= ", ";
							}
							$list_users .= $r["eoil_user"];
						}
		            }
		            $this->call_command("DB_FREE",Array($result));
					if ($eoio_status==1 && $list_users==""){
						// this is an optoutable list and everyone opted out
					}else{
						$label = "Updated User profile";
						if ($list_users!=""){
							$extra = " and groups_belonging_to_user.user_identifier in ($list_users) ";
						} else {
							$extra = "";
						}
						$sql = "select distinct groups_belonging_to_user.user_identifier, email_address , contact_first_name from group_data  
								  inner join group_access on access_group = group_data.group_identifier and access_code in ('$cmd',  'ALL', 'SHOP_ALL')
								  inner join groups_belonging_to_user on groups_belonging_to_user.group_identifier= group_data.group_identifier and group_client=groups_belonging_to_user.client_identifier
								  left outer  join contact_data on contact_user = groups_belonging_to_user.user_identifier and contact_client = groups_belonging_to_user.client_identifier
								  left outer join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where group_client=$this->client_identifier $extra and email_address is not null";
						$result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$pos = count($email_addresses);
							$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$label, "URL" =>$url);
				        }
				        $this->call_command("DB_FREE",Array($result));
						$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg,"format"=>"HTML"));
					}
				}
			}
*/			
			/*************************************************************************************************************************
			* 												 S H O P
			*************************************************************************************************************************/
			if ($access_type == "SHOP"){
				if($cmd!=""){
					$sql =   "select eoio_status, eoil_user from elert_optinout
									  left outer join elert_optin_list on eoio_client = eoil_client and eoio_identifier = eoil_list
								where eoio_client = $this->client_identifier and eoio_type = '$type' ";
					$result  		= $this->call_command("DB_QUERY",Array($sql));
					$list_users		= "";
					$eoio_status	= 0;
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
		            	if ($r["eoio_status"]==1){
							$eoio_status = $r["eoio_status"];
							if($list_users!=""){
								$list_users .= ", ";
							}
							$list_users .= $r["eoil_user"];
						}
		            }
		            $this->call_command("DB_FREE",Array($result));
					if ($eoio_status==1 && $list_users==""){
						// this is an optoutable list and everyone opted out
					}else{
						$label = "Shopping Basket ordered";
						if ($list_users!=""){
							$extra = " and groups_belonging_to_user.user_identifier in ($list_users) ";
						} else {
							$extra = "";
						}
						$sql = "select distinct groups_belonging_to_user.user_identifier, email_address , contact_first_name from group_data  
								  inner join group_access on access_group = group_data.group_identifier and access_code in ('$cmd',  'ALL', 'SHOP_ALL')
								  inner join groups_belonging_to_user on groups_belonging_to_user.group_identifier= group_data.group_identifier and group_client=groups_belonging_to_user.client_identifier
								  left outer  join contact_data on contact_user = groups_belonging_to_user.user_identifier and contact_client = groups_belonging_to_user.client_identifier
								  left outer join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where group_client=$this->client_identifier $extra and email_address is not null";
						$result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$pos = count($email_addresses);
							$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$label, "URL" =>$url);
				        }
				        $this->call_command("DB_FREE",Array($result));
						$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg,"format"=>"HTML"));
					}
				} else {
						/*
							if the web site elerts are to be sent then do it with this code.
							
							1. extract the sections that are to be emailed that hold this guest book
						*/
						/*
						$sql = "
select gbe_label, menu_url, menu_identifier from guestbooks_list 
	inner join guestbooks_entry on guestbooks_list.gb_identifier = guestbooks_entry.gbe_book and gb_client=gbe_client
	inner join menu_data on menu_data.menu_identifier = guestbooks_list.gb_menu_locations  and gb_client=menu_client
where guestbooks_entry.gbe_identifier=$identifier and gb_client=$this->client_identifier

";
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$menu_list_array = Array();
					$trans_title="";
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
						$title = $r["gbe_label"];
		            	$menu_list_array[count($menu_list_array)] = $r["menu_identifier"];
						$url   = "http://".$this->parent->domain.$this->parent->base.$r["menu_url"];
		            }
					$menu_list = join(",",$menu_list_array);
					$sql = $this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT", Array(
							"module"		=> $this->webContainer,
							"condition"		=> "((mto_menu in ($menu_list) and  mto_client=$this->client_identifier and mto_publish=1 and es_all_locations=0) or (mto_menu is null and es_client =$this->client_identifier and es_all_locations=1))",
							"client_field"	=> "es_client",
							"table"			=> "elert_sections",
							"primary"		=> "es_identifier",
							"join"			=> "right outer",
							"just_cond"		=> 1,
							"ex_field"		=> ", elert_sections.*"
							)
						);
						$result  = $this->call_command("DB_QUERY",Array($sql));
						$sections = Array();
		                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
		                	$sections[count($sections)] = $this->check_parameters($r,"mto_object",$this->check_parameters($r,"es_identifier"));
		                }
		                $this->call_command("DB_FREE",Array($result));
						if (count($sections)>0){
							$base = "http://".$this->parent->domain.$this->parent->base;
							$signup_length = strlen($base);
							$rest = substr($url,$signup_length);
							$pos = strpos($rest, '?');
							if ($pos===false){
								$check_url = $rest;
							} else {
								$check_url = substr($rest,0,$pos);
								$ps = SPLIT('&',substr($rest,$pos+1));
								$m = count($ps); 
								for($i=0;$i<$m;$i++){
									if (session_name()."=".session_id() == $ps[$i]){
										unset($ps[$i]);
									}
								}
								if (count($ps)>0){
									$check_url .= "?".join("&amp;",$ps);
								}
							}
							$list = join(",",$sections);
							$sql="(select email_address, contact_first_name from elert_sections 
									inner join elert_signup on es_identifier = esu_section and es_client=esu_client 
									inner join contact_data on contact_user = esu_user and contact_client = esu_client
								  	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where es_client = $this->client_identifier and es_identifier in ($list)
							) union (
								select email_address, contact_first_name from elert_signup_urls
									inner join contact_data on contact_user = esurl_user and contact_client = esurl_client
								 	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where 
								 	esurl_client = $this->client_identifier and esurl_url  = '$check_url'
							)";
//							print "$sql";
//							$this->exitprogram();
							$result  = $this->call_command("DB_QUERY",Array($sql));
		                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
								$pos = count($email_addresses);
								$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$title, "URL" => $url);
	            	        }
	        	            $this->call_command("DB_FREE",Array($result));
						}
					$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg.$disclaimer));
					*/
				}
			}
			/*************************************************************************************************************************
			* 												 F O R U M
			*************************************************************************************************************************/
			if ($access_type == "FORUM"){
				if($cmd!=""){
					$sql =   "select * from elert_optinout
									  left outer join elert_optin_list on eoio_client = eoil_client and eoio_identifier = eoil_list
								where eoio_client = $this->client_identifier and eoio_type = '$type' ";
					$result  		= $this->call_command("DB_QUERY",Array($sql));
					$list_users		= "";
					$eoio_status	= 0;
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
		            	if ($r["eoio_status"]==1){
							$eoio_status = $r["eoio_status"];
							if($list_users!=""){
								$list_users .= ", ";
							}
							$list_users .= $r["eoil_user"];
						}
		            }
		            $this->call_command("DB_FREE",Array($result));
					if ($eoio_status==1 && $list_users=""){
						// this is an optoutable list and everyone opted out
					}else{
						$sql ="
						select * from forum 
							inner join forum_thread on forum_thread_forum = forum_identifier  and forum_thread_client = forum_client
						where forum_thread_identifier = $identifier and forum_thread_client=$this->client_identifier";
//						print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
						}
						$result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$label = $r["forum_thread_title"];
				        }
				        $this->call_command("DB_FREE",Array($result));
						if ($list_users!=""){
							$extra = " and groups_belonging_to_user.user_identifier in ($list_users) ";
						} else {
							$extra = "";
						}
						$sql = "select distinct groups_belonging_to_user.user_identifier, email_address , contact_first_name from group_data  
								  inner join group_access on access_group = group_data.group_identifier and access_code in ('$cmd',  'ALL', 'INFORMATIONADMIN_ALL')
								  inner join groups_belonging_to_user on groups_belonging_to_user.group_identifier= group_data.group_identifier and group_client=groups_belonging_to_user.client_identifier
								  left outer  join contact_data on contact_user = groups_belonging_to_user.user_identifier and contact_client = groups_belonging_to_user.client_identifier
								  left outer join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where group_client=$this->client_identifier $extra ";
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
						}
//						print "$sql";
						$result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$pos = count($email_addresses);
							$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$label, "URL" =>$url);
				        }
				        $this->call_command("DB_FREE",Array($result));
						$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg));
					}
				} else {
						/*
							if the web site elerts are to be sent then do it with this code.
							
							1. extract the sections that are to be emailed that hold this guest book
						*/
						$sql = "
select forum_thread_title, forum_thread_starter, forum_thread_forum, forum_thread_identifier, menu_identifier, menu_url from forum
	inner join forum_thread on forum_thread.forum_thread_forum =forum.forum_identifier and forum_client = forum_thread_client and forum_thread_identifier=$identifier
	inner join menu_data on menu_data.menu_identifier = forum.forum_location and forum.forum_client=menu_client
where forum.forum_client=$this->client_identifier";
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
					}

//					print "[$sql]";
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$menu_list_array = Array();
					$trans_title="";
					$clist=-1;
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
						$title		= $r["forum_thread_title"];
		            	$f_id 		= $r["forum_thread_forum"];
		            	$fs_id 		= $r["forum_thread_starter"];
		            	$t_id 		= $r["forum_thread_identifier"];
		            	$menu_list_array[count($menu_list_array)] = $r["menu_identifier"];
						/*
						if ($r["menu_url"] == "index.php"){
							$url   = "http://".$this->parent->domain.$this->parent->base;
						} else {
							$url   = "http://".$this->parent->domain.$this->parent->base.dirname($r["menu_url"]);
						}*/
						$url   = "http://".$this->parent->domain.$this->parent->base.$r["menu_url"];
		            }
					$signupurl = $url."?command=FORUM_THREAD_VIEW_ENTRY&forum_identifier=$f_id&thread=$fs_id";
					$url .= "?command=FORUM_THREAD_VIEW_ENTRY&amp;forum_identifier=$f_id&amp;thread_identifier=$t_id&amp;thread=$fs_id";
					// get real url
					$menu_list = join(",",$menu_list_array);
					$sql = $this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT", Array(
							"module"		=> $this->webContainer,
							"condition"		=> "((mto_menu in ($menu_list) and  mto_client=$this->client_identifier and mto_publish=1 and es_all_locations=0) or (mto_menu is null and es_client =$this->client_identifier and es_all_locations=1))",
							"client_field"	=> "es_client",
							"table"			=> "elert_sections",
							"primary"		=> "es_identifier",
							"join"			=> "right outer",
							"just_cond"		=> 1,
							"ex_field"		=> ", elert_sections.*"
							)
					);
//					print "[$sql]";
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$sections = Array();
	                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	                	$sections[count($sections)] = $this->check_parameters($r,"mto_object",$this->check_parameters($r,"es_identifier"));
	                }
	                $this->call_command("DB_FREE",Array($result));
					if (count($sections)>0){
						$base = "http://".$this->parent->domain.$this->parent->base;
						$signup_length = strlen($base);
						$rest = substr($signupurl,$signup_length);
						$pos = strpos($rest, '?');
						if ($pos===false){
							$check_url = $rest;
						} else {
							$check_url = substr($rest,0,$pos);
							$ps = SPLIT('&',substr($rest,$pos+1));
							$m = count($ps); 
							for($i=0;$i<$m;$i++){
								if (session_name()."=".session_id() == $ps[$i]){
									unset($ps[$i]);
								}
							}
							if (count($ps)>0){
								$check_url .= "?".join("&",$ps);
							}
						}
						$list = join(",",$sections);
						$sql="(select distinct email_address,contact_first_name from elert_sections 
									inner join elert_signup on es_identifier = esu_section and es_client=esu_client 
									inner join contact_data on contact_user = esu_user and contact_client = esu_client
								  	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where es_client = $this->client_identifier and es_identifier in ($list)
							) union (
								select email_address, contact_first_name from elert_signup_urls
									inner join contact_data on contact_user = esurl_user and contact_client = esurl_client
								 	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where 
								 	esurl_client = $this->client_identifier and esurl_url  = '$check_url'
							)";
//						print "$sql";
						$result  = $this->call_command("DB_QUERY",Array($sql));
//						print $result;
	                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
//							print_r($r); 
							$pos = count($email_addresses);
							$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$title, "URL" => $url);
            	        }
        	            $this->call_command("DB_FREE",Array($result));
					}
//					print_r($email_addresses);
					$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg.$disclaimer));
				}
			}
			/*************************************************************************************************************************
			* 								I N F O R M A T I O N   D I R E C T O R Y
			*************************************************************************************************************************/
			if ($access_type == "INFORMATIONADMIN_" || $access_type == "EVENT_"){
				if($cmd!=""){
					$sql =   "select * from elert_optinout
									  left outer join elert_optin_list on eoio_client = eoil_client and eoio_identifier = eoil_list
								where eoio_client = $this->client_identifier and eoio_type = '$type' ";
					$result  		= $this->call_command("DB_QUERY",Array($sql));
					$list_users		= "";
					$eoio_status	= 0;
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
		            	if ($r["eoio_status"]==1){
							$eoio_status = $r["eoio_status"];
							if($list_users!=""){
								$list_users .= ", ";
							}
							$list_users .= $r["eoil_user"];
						}
		            }
		            $this->call_command("DB_FREE",Array($result));
					if ($eoio_status==1 && $list_users=""){
						// this is an optoutable list and everyone opted out
					}else{
						$sql ="
						select * from information_entry 
							inner join information_entry_values on iev_entry = ie_identifier  and iev_field='ie_title' and iev_client = ie_client
						where ie_identifier = $identifier and ie_client=$this->client_identifier";
//						print "$sql";
						$result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$label = $r["iev_value"];
				        }
				        $this->call_command("DB_FREE",Array($result));
						if ($list_users!=""){
							$extra = " and groups_belonging_to_user.user_identifier in ($list_users) ";
						} else {
							$extra = "";
						}
						$sql = "select distinct groups_belonging_to_user.user_identifier, email_address , contact_first_name from group_data  
								  inner join group_access on access_group = group_data.group_identifier and access_code in ('$cmd',  'ALL', 'INFORMATIONADMIN_ALL')
								  inner join groups_belonging_to_user on groups_belonging_to_user.group_identifier= group_data.group_identifier and group_client=groups_belonging_to_user.client_identifier
								  left outer  join contact_data on contact_user = groups_belonging_to_user.user_identifier and contact_client = groups_belonging_to_user.client_identifier
								  left outer join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where group_client=$this->client_identifier $extra ";
//						print "$sql";
						$result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$pos = count($email_addresses);
							$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$label, "URL" =>$url);
				        }
				        $this->call_command("DB_FREE",Array($result));
						$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg));
					}
				} else {
						/*
							if the web site elerts are to be sent then do it with this code.
							
							1. extract the sections that are to be emailed that hold this guest book
						*/
						$sql = "
select iev_value, menu_identifier, menu_url, cto_clist from information_list
	inner join information_entry on information_entry.ie_list =information_list.info_identifier and ie_client = info_client and ie_identifier=$identifier
	inner join metadata_details on information_entry.ie_identifier =metadata_details.md_link_id and ie_client = md_client and md_module='$access_type'
	inner join information_entry_values on 
		information_entry_values.iev_list = information_list.info_identifier and 
		information_entry_values.iev_entry = information_entry.ie_identifier and 
		information_entry.ie_client = information_entry_values.iev_client and information_entry_values.iev_field='ie_title'
	inner join menu_data on menu_data.menu_identifier = information_list.info_menu_location and information_list.info_client=menu_client
	inner join category_to_object on cto_object = ie_identifier and cto_module='INFORMATIONADMIN_' 
where info_client=$this->client_identifier";

//					print "[$sql]";
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$menu_list_array = Array();
					$trans_title="";
					$clist=-1;
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
		            	$title 		 = $r["iev_value"];
		            	$menu_list_array[count($menu_list_array)] = $r["menu_identifier"];
						if ($r["menu_url"]=="index.php"){
							$url   = "http://".$this->parent->domain.$this->parent->base;
						} else {
							$url   = "http://".$this->parent->domain.$this->parent->base.dirname($r["menu_url"]);
						}
						$clist = $r["cto_clist"];
		            }
					$path = $this->call_command("INFORMATIONADMIN_FIND_PATH", Array($clist));
//					print "[$path]";
					$checkurl = $url.$path."/index.php?category=$clist";
					$url .= $path."/".$this->make_uri($title)."-".$identifier.".php";
					// get real url
					$menu_list = join(",",$menu_list_array);
					$sql = $this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT", Array(
							"module"		=> $this->webContainer,
							"condition"		=> "((mto_menu in ($menu_list) and  mto_client=$this->client_identifier and mto_publish=1 and es_all_locations=0) or (mto_menu is null and es_client =$this->client_identifier and es_all_locations=1))",
							"client_field"	=> "es_client",
							"table"			=> "elert_sections",
							"primary"		=> "es_identifier",
							"join"			=> "right outer",
							"just_cond"		=> 1,
							"ex_field"		=> ", elert_sections.*"
						)
					);
//					print "[$sql]";
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$sections = Array();
	                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	                	$sections[count($sections)] = $this->check_parameters($r,"mto_object",$this->check_parameters($r,"es_identifier"));
	                }
	                $this->call_command("DB_FREE",Array($result));
					if (count($sections)>0){
						$base = "http://".$this->parent->domain.$this->parent->base;
						$signup_length = strlen($base);
						$rest = substr($checkurl,$signup_length);
						$pos = strpos($rest, '?');
						if ($pos===false){
							$check_url = $rest;
						} else {
							$check_url = substr($rest,0,$pos);
							$ps = SPLIT('&',substr($rest,$pos+1));
							$m = count($ps); 
							for($i=0;$i<$m;$i++){
								if (session_name()."=".session_id() == $ps[$i]){
									unset($ps[$i]);
								}
							}
							if (count($ps)>0){
								$check_url .= "?".join("&amp;",$ps);
							}
						}
						$list = join(",",$sections);
						$sql="(select distinct email_address,contact_first_name from elert_sections 
								inner join elert_signup on es_identifier = esu_section and es_client=esu_client 
								inner join contact_data on contact_user = esu_user and contact_client = esu_client
							  	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
							where es_client = $this->client_identifier and es_identifier in ($list)
) union (
select email_address, contact_first_name from elert_signup_urls
	inner join contact_data on contact_user = esurl_user and contact_client = esurl_client
 	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
where 
 	esurl_client = $this->client_identifier and esurl_url  = '$check_url'
)
							";
//							print "$sql";
						$result  = $this->call_command("DB_QUERY",Array($sql));
	                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$pos = count($email_addresses);
							$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$title, "URL" => $url);
            	        }
        	            $this->call_command("DB_FREE",Array($result));
					}
//					print_r($email_addresses);
					$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg.$disclaimer));
				}
			}
			/*************************************************************************************************************************
			* 											 C O M M E N T S
			*************************************************************************************************************************/
			if ($access_type == "COMMENTS"){
				if($cmd!=""){
					$sql =   "select * from elert_optinout
									  left outer join elert_optin_list on eoio_client = eoil_client and eoio_identifier = eoil_list
								where eoio_client = $this->client_identifier and eoio_type = '$type' ";
					$result  		= $this->call_command("DB_QUERY",Array($sql));
					$list_users		= "";
					$eoio_status	= 0;
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
		            	if ($r["eoio_status"]==1){
							$eoio_status = $r["eoio_status"];
							if($list_users!=""){
								$list_users .= ", ";
							}
							$list_users .= $r["eoil_user"];
						}
		            }
		            $this->call_command("DB_FREE",Array($result));
					if ($eoio_status==1 && $list_users=""){
						// this is an optoutable list and everyone opted out
					}else{
						$sql ="select * from page_comments where comment_identifier = $identifier and comment_client=$this->client_identifier and comment_type=0";
						$result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$label = $r["comment_title"];
				        }
				        $this->call_command("DB_FREE",Array($result));
						if ($list_users!=""){
							$extra = " and groups_belonging_to_user.user_identifier in ($list_users) ";
						} else {
							$extra = "";
						}
						$sql = "select distinct groups_belonging_to_user.user_identifier, email_address , contact_first_name from group_data  
								  inner join group_access on access_group = group_data.group_identifier and access_code in ('$cmd',  'ALL', 'COMMENTSADMIN_ALL')
								  inner join groups_belonging_to_user on groups_belonging_to_user.group_identifier= group_data.group_identifier and group_client=groups_belonging_to_user.client_identifier
								  left outer  join contact_data on contact_user = groups_belonging_to_user.user_identifier and contact_client = groups_belonging_to_user.client_identifier
								  left outer join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where group_client=$this->client_identifier $extra ";
	//					print "$sql";
						$result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$pos = count($email_addresses);
							$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$label, "URL" =>$url);
				        }
				        $this->call_command("DB_FREE",Array($result));
						$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg));
					}
				} else {
						/*
							if the web site elerts are to be sent then do it with this code.
							
							1. extract the sections that are to be emailed that hold this guest book
						*/
						$sql = "
select comment_title, menu_url, menu_access_to_page.menu_identifier, trans_title from page_comments 
	inner join menu_access_to_page on comment_translation = menu_access_to_page.trans_identifier and client_identifier = comment_client
	inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier and client_identifier=menu_client
	inner join page_trans_data on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier and client_identifier=menu_client
where comment_identifier=$identifier and comment_client=$this->client_identifier
";
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$menu_list_array = Array();
					$trans_title="";
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
						$trans_title = $r["trans_title"];
		            	$title 		 = $r["comment_title"];
		            	$menu_list_array[count($menu_list_array)] = $r["menu_identifier"];
						if ($r["menu_url"]=="index.php"){
							$url   = "http://".$this->parent->domain.$this->parent->base;
						} else {
							$url   = "http://".$this->parent->domain.$this->parent->base.dirname($r["menu_url"])."/";
						}
		            }
					$url .= $this->make_uri($trans_title).".php";
					// get real url
					$menu_list = join(",",$menu_list_array);
					$sql = $this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT", Array(
							"module"		=> $this->webContainer,
							"condition"		=> "((mto_menu in ($menu_list) and  mto_client=$this->client_identifier and mto_publish=1 and es_all_locations=0) or (mto_menu is null and es_client =$this->client_identifier and es_all_locations=1))",
							"client_field"	=> "es_client",
							"table"			=> "elert_sections",
							"primary"		=> "es_identifier",
							"join"			=> "right outer",
							"just_cond"		=> 1,
							"ex_field"		=> ", elert_sections.*"
							)
					);
//					print "[$sql]";
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$sections = Array();
	                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	                	$sections[count($sections)] = $this->check_parameters($r,"mto_object",$this->check_parameters($r,"es_identifier"));
	                }
	                $this->call_command("DB_FREE",Array($result));
					if (count($sections)>0){
						$base = "http://".$this->parent->domain.$this->parent->base;
						$signup_length = strlen($base);
						$rest = substr($url,$signup_length);
						$pos = strpos($rest, '?');
						if ($pos===false){
							$check_url = $rest;
						} else {
							$check_url = substr($rest,0,$pos);
							$ps = SPLIT('&',substr($rest,$pos+1));
							$m = count($ps); 
							for($i=0;$i<$m;$i++){
								if (session_name()."=".session_id() == $ps[$i]){
									unset($ps[$i]);
								}
							}
							if (count($ps)>0){
								$check_url .= "?".join("&amp;",$ps);
							}
						}

						$list = join(",",$sections);
/*						$sql="select * from elert_sections 
								inner join elert_signup on es_identifier = esu_section and es_client=esu_client 
								inner join contact_data on contact_user = esu_user and contact_client = esu_client
							  	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
							where es_client = $this->client_identifier and es_identifier in ($list)";
*/
$sql = "
(
select email_address, contact_first_name from elert_sections 
	inner join elert_signup on es_identifier = esu_section and es_client=esu_client 
	inner join contact_data on contact_user = esu_user and contact_client = esu_client
 	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
where 
 	es_client = $this->client_identifier and es_identifier in ($list)
) union (
select email_address, contact_first_name from elert_signup_urls
	inner join contact_data on contact_user = esurl_user and contact_client = esurl_client
 	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
where 
 	esurl_client = $this->client_identifier and esurl_url  = '$check_url'
)
";


						$result  = $this->call_command("DB_QUERY",Array($sql));
	                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$pos = count($email_addresses);
							$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$title, "URL" => $url);
            	        }
        	            $this->call_command("DB_FREE",Array($result));
//							print_r($email_addresses);
					}
//					print $msg;
					$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg.$disclaimer));
				}
			}
			/*************************************************************************************************************************
			* 											G U E S T B O O K
			*************************************************************************************************************************/
			if ($access_type == "GUESTBOOK"){
				if($cmd!=""){
					$sql =   "select * from elert_optinout
									  left outer join elert_optin_list on eoio_client = eoil_client and eoio_identifier = eoil_list
								where eoio_client = $this->client_identifier and eoio_type = '$type' ";
					$result  		= $this->call_command("DB_QUERY",Array($sql));
					$list_users		= "";
					$eoio_status	= 0;
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
		            	if ($r["eoio_status"]==1){
							$eoio_status = $r["eoio_status"];
							if($list_users!=""){
								$list_users .= ", ";
							}
							$list_users .= $r["eoil_user"];
						}
		            }
		            $this->call_command("DB_FREE",Array($result));
					if ($eoio_status==1 && $list_users=""){
						// this is an optoutable list and everyone opted out
					}else{
						$sql ="select * from guestbooks_entry where gbe_identifier = $identifier and gbe_client=$this->client_identifier";
						$result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$label = $r["gbe_label"];
				        }
				        $this->call_command("DB_FREE",Array($result));
						if ($list_users!=""){
							$extra = " and groups_belonging_to_user.user_identifier in ($list_users) ";
						} else {
							$extra = "";
						}
						$sql = "select distinct groups_belonging_to_user.user_identifier, email_address , contact_first_name from group_data  
								  inner join group_access on access_group = group_data.group_identifier and access_code in ('$cmd',  'ALL', 'GUESTBOOKADMIN_ALL')
								  inner join groups_belonging_to_user on groups_belonging_to_user.group_identifier= group_data.group_identifier and group_client=groups_belonging_to_user.client_identifier
								  left outer  join contact_data on contact_user = groups_belonging_to_user.user_identifier and contact_client = groups_belonging_to_user.client_identifier
								  left outer join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where group_client=$this->client_identifier $extra ";
	//					print "$sql";
						$result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$pos = count($email_addresses);
							$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$label, "URL" =>$url);
				        }
				        $this->call_command("DB_FREE",Array($result));
						$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg));
					}
				} else {
						/*
							if the web site elerts are to be sent then do it with this code.
							
							1. extract the sections that are to be emailed that hold this guest book
						*/
						$sql = "
select gbe_label, menu_url, menu_identifier from guestbooks_list 
	inner join guestbooks_entry on guestbooks_list.gb_identifier = guestbooks_entry.gbe_book and gb_client=gbe_client
	inner join menu_data on menu_data.menu_identifier = guestbooks_list.gb_menu_locations  and gb_client=menu_client
where guestbooks_entry.gbe_identifier=$identifier and gb_client=$this->client_identifier

";
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$menu_list_array = Array();
					$trans_title="";
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
						$title = $r["gbe_label"];
		            	$menu_list_array[count($menu_list_array)] = $r["menu_identifier"];
						$url   = "http://".$this->parent->domain.$this->parent->base.$r["menu_url"];
		            }
					$menu_list = join(",",$menu_list_array);
					$sql = $this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT", Array(
							"module"		=> $this->webContainer,
							"condition"		=> "((mto_menu in ($menu_list) and  mto_client=$this->client_identifier and mto_publish=1 and es_all_locations=0) or (mto_menu is null and es_client =$this->client_identifier and es_all_locations=1))",
							"client_field"	=> "es_client",
							"table"			=> "elert_sections",
							"primary"		=> "es_identifier",
							"join"			=> "right outer",
							"just_cond"		=> 1,
							"ex_field"		=> ", elert_sections.*"
							)
						);
						$result  = $this->call_command("DB_QUERY",Array($sql));
						$sections = Array();
		                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
		                	$sections[count($sections)] = $this->check_parameters($r,"mto_object",$this->check_parameters($r,"es_identifier"));
		                }
		                $this->call_command("DB_FREE",Array($result));
						if (count($sections)>0){
							$base = "http://".$this->parent->domain.$this->parent->base;
							$signup_length = strlen($base);
							$rest = substr($url,$signup_length);
							$pos = strpos($rest, '?');
							if ($pos===false){
								$check_url = $rest;
							} else {
								$check_url = substr($rest,0,$pos);
								$ps = SPLIT('&',substr($rest,$pos+1));
								$m = count($ps); 
								for($i=0;$i<$m;$i++){
									if (session_name()."=".session_id() == $ps[$i]){
										unset($ps[$i]);
									}
								}
								if (count($ps)>0){
									$check_url .= "?".join("&amp;",$ps);
								}
							}
							$list = join(",",$sections);
							$sql="(select email_address, contact_first_name from elert_sections 
									inner join elert_signup on es_identifier = esu_section and es_client=esu_client 
									inner join contact_data on contact_user = esu_user and contact_client = esu_client
								  	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where es_client = $this->client_identifier and es_identifier in ($list)
							) union (
								select email_address, contact_first_name from elert_signup_urls
									inner join contact_data on contact_user = esurl_user and contact_client = esurl_client
								 	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where 
								 	esurl_client = $this->client_identifier and esurl_url  = '$check_url'
							)";
//							print "$sql";
//							$this->exitprogram();
							$result  = $this->call_command("DB_QUERY",Array($sql));
		                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
								$pos = count($email_addresses);
								$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$title, "URL" => $url);
	            	        }
	        	            $this->call_command("DB_FREE",Array($result));
						}
					$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg.$disclaimer));
				}
			}
			/*************************************************************************************************************************
			* 												  P A G E
			*************************************************************************************************************************/
			if ($access_type == "PAGE"){
				if ($cmd=="PAGE_AUTHOR"){
					$sql = "select * from page_trans_data 
						left outer join contact_data on contact_user = page_trans_data.trans_doc_author_identifier and contact_client = page_trans_data.trans_client
						left outer join email_addresses on contact_identifier = email_contact and email_client = contact_client
					where trans_identifier=$identifier and trans_client =$this->client_identifier";
					$result  = $this->call_command("DB_QUERY",Array($sql));
                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
						$author_name 		= $this->check_parameters($r,"contact_first_name");
						$trans_title		= $this->check_parameters($r,"trans_title");
                    	$author 			= $this->check_parameters($r,"email_address");
                    }
					
                    $this->call_command("DB_FREE",Array($result));
					 
					$this->call_command("EMAIL_QUICK_SEND",Array(
							"to"		=> "$author",
							"subject"	=> $subject,
							"body"		=> str_replace(
									Array(
										"[[contact_first_name]]", 
										"[[title]]",
										"[[url]]"
									), Array(
										$author_name, 
										$trans_title,
										$url
									), $msg.$disclaimer)
						)
					);
				} else {
					$sql = "select menu_access_to_page.*, page_trans_data.trans_title from menu_access_to_page 
					inner join page_trans_data on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier 
					where page_trans_data.trans_identifier=$identifier and client_identifier=$this->client_identifier";
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$menu_list_array = Array();
					$trans_title="";
		            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
						$trans_title = $r["trans_title"];
		            	$menu_list_array[count($menu_list_array)] = $r["menu_identifier"];
		            }
					$menu_list = join(",",$menu_list_array);
					$this->call_command("DB_FREE",Array($result));
					if($cmd!=""){
						/*
							if the web site elerts are to be sent then do not use this section as this will email the administators that the
							workflow has changed.
						 	
							get a list of users that are signed up on any optout list
						*/
						if ($menu_list==""){
							// this should never happen as for a page to be sent through the workflow process without a menu location mmmm
						} else {
							$sql =   "select * from elert_optinout
											  left outer join elert_optin_list on eoio_client = eoil_client and eoio_identifier = eoil_list
										where eoio_client = $this->client_identifier and eoio_type = '$type' ";
							$result  		= $this->call_command("DB_QUERY",Array($sql));
							$list_users		= "";
							$eoio_status	= 0;
				            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				            	if ($r["eoio_status"]==1){
									$eoio_status = $r["eoio_status"];
									if($list_users!=""){
										$list_users .= ", ";
									}
									$list_users .= $r["eoil_user"];
								}
				            }
				            $this->call_command("DB_FREE",Array($result));
							if ($eoio_status==1 && $list_users=""){
								// this is an optoutable list and everyone opted out
							}else{
								if ($list_users!=""){
									$extra = " and groups_belonging_to_user.user_identifier in ($list_users) ";
								} else {
									$extra = "";
								}
								$sql = "select distinct groups_belonging_to_user.user_identifier, email_address , contact_first_name from group_data  
										  inner join group_access on access_group = group_data.group_identifier and access_code in ('$cmd',  'ALL', 'PAGE_ALL')
										  inner join groups_belonging_to_user on groups_belonging_to_user.group_identifier= group_data.group_identifier and group_client=groups_belonging_to_user.client_identifier
										  left outer join relate_user_menu on relate_user_menu.user_identifier= groups_belonging_to_user.user_identifier
										  left outer  join contact_data on contact_user = groups_belonging_to_user.user_identifier and contact_client = groups_belonging_to_user.client_identifier
										  left outer join email_addresses on contact_identifier = email_contact and email_client = contact_client
										where group_client=$this->client_identifier $extra and (relate_user_menu.menu_identifier is NULL or relate_user_menu.menu_identifier in ($menu_list))";
								$result  = $this->call_command("DB_QUERY",Array($sql));
					        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
									$pos = count($email_addresses);
									$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$trans_title, "URL"=>$url);
						        }
						        $this->call_command("DB_FREE",Array($result));
							}
						}
					} else {
						/*
							if the web site elerts are to be sent then do it with this code.
							
							1. extract the sections that are to be emailed
						*/
						$sql = $this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT", Array(
							"module"=> $this->webContainer,
							"condition"=> "mto_menu in ($menu_list) and ",
							"client_field"=> "es_client",
							"table"	=> "elert_sections",
							"primary"=> "es_identifier"
							)
						);
						$result  = $this->call_command("DB_QUERY",Array($sql));
						$sections = Array();
		                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
		                	$sections[count($sections)] = $r["mto_object"];
		                }
		                $this->call_command("DB_FREE",Array($result));
						if (count($sections)>0){
							$list = join(",",$sections);
							$sql="select * from elert_sections 
									inner join elert_signup on es_identifier = esu_section and es_client=esu_client 
									inner join contact_data on contact_user = esu_user and contact_client = esu_client
								  	inner join email_addresses on contact_identifier = email_contact and email_client = contact_client
								where es_client = $this->client_identifier and es_identifier in ($list)";
							$result  = $this->call_command("DB_QUERY",Array($sql));
		                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
									$pos = count($email_addresses);
									$email_addresses[$pos] = Array("EMAIL"=>$r["email_address"], "NAME"=>$r["contact_first_name"], "TITLE"=>$trans_title, "URL"=>$url);
	            	        }
	        	            $this->call_command("DB_FREE",Array($result));
						}
					}
					/*
						should have a list of emails now??
					*/
					if ($msg == ""){
						$msg  = $Default_Message;
					}
					//print_r(Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg.$disclaimer));
					$this->call_command("EMAIL_BULK_SEND",Array("EMAIL_LIST"=>$email_addresses, "subject"=>$subject, "body"=>$msg.$disclaimer));
				}
			}
		}
//		$this->exitprogram();
	}
	function module_list_email_defs($parameters){
		$this->page_size=50;
		$page	= $this->check_parameters($parameters,"page",1);
		$variables["PAGE_BUTTONS"] = Array(
		);
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
		}
		$number_of_records = count($this->module_email_msgs);
		$goto = ((--$page)*$this->page_size);
		
		if (($goto!=0)&&($number_of_records>$goto)){
			$pointer = $goto;
		}
		if ($goto+$this->page_size>$number_of_records){
			$finish = $number_of_records;
		}else{
			$finish = $goto+$this->page_size;
		}
//		$goto++;
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
		$parameters["command"] = "ELERTADMIN_DEFINITION_LIST";
		$variables["FILTER"]			= $this->def_filter($parameters);
		$variables["HEADER"] 			= MANAGEMENT_ELERTS." - ".LOCALE_LIST;
		$variables["RESULT_ENTRIES"] =Array();
		$counter=0;
		for ($counter=0; $counter < $this->page_size; $counter++){
			if (($goto+$counter) < $number_of_records){
				$i = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$i]=Array(
					"identifier"	=> ($counter + $goto),
					"attributes"	=> Array(
						Array(ENTRY_TITLE, constant("LOCALE_ELERT_".$this->module_email_msgs[$counter+$goto][0]) ,"TITLE","NO")
					),
					"ENTRY_BUTTONS" => Array(
						Array("EDIT", $this->module_command."DEFINITION", EDIT_EXISTING)
					)
				);
			}
		}
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["as"]				= "table";
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$out = $this->generate_list($variables);
		return $out;
	}

	function email_defintion($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier",-1);
		$list		= $this->check_parameters($parameters,"list",-1);
		if ($list ==-1){
			$id=$identifier;
		} else {
			$id =-1;
			for($index=0;$index<count($this->module_email_msgs);$index++){
				if ($this->module_email_msgs[$index][0]==$list){
					$id=$index;
				}
			}
		}
		$msg		= "";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fname = "$data_files/email_notify_".$this->client_identifier."_".$this->module_email_msgs[$id][0].".txt";
		if (file_exists($fname)){
			$fp = fopen($fname, 'r');
			$msg = fread($fp, filesize($fname));
			fclose($fp);
		}
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options><header>" . LOCALE_ELERTS_MESSAGES . "</header>";
		if($list!=-1){
			$out .= "	<button command=\"ELERTADMIN_OPTABLE_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"/>";
		} else {
			$out .= "	<button command=\"ELERTADMIN_DEFINITION_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"/>";
		}
		$out .= "</page_options>";
		$out .= "<form name=\"elert_manager\" method=\"post\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"ELERTADMIN_DEFINITION_SAVE\"/>";
		$out .= "<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
		$out .= "<page_sections><section name='definition' label='Email to signup users'><seperator_row><seperator>";
		$label = constant("LOCALE_ELERT_".$this->module_email_msgs[$id][0]);
		$out .= "<textarea label='$label' type='PLAIN' width='40' height='18' name='message'><![CDATA[$msg]]></textarea>";
		$out .= "</seperator>";
		/*
		$out .= "<seperator>";
		$out .= "<text><![CDATA[".LOCALE_ELERT_EMAIL_MSG_EXAMPLE."]]></text>";
		$out .= "</seperator>";
		*/
		$out .= "</seperator_row></section></page_sections>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
		$out .= "</form>";
		$out .="</module>";
		return $out;
	}
	function email_defintion_save($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier",-1);
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fname = "$data_files/email_notify_".$this->client_identifier."_".$this->module_email_msgs[$identifier][0].".txt";
		$message = $this->check_parameters($parameters,"message");
		$fp = fopen($fname, 'w');
		fwrite($fp, $message);
		fclose($fp);
		$um = umask(0);
		@chmod($fname, LS__FILE_PERMISSION);
		umask($um);
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options><header>" . LOCALE_ELERTS_MESSAGES_CONFIRM . "</header>";
		$out .= "	<button command=\"ELERTADMIN_DEFINITION_LIST\" alt=\"".LOCALE_CANCEL."\" iconify=\"CANCEL\"/>";
		$out .= "</page_options>";
		$out .= "<form name=\"elert_manager\" method=\"post\">";
		$out .= "<text><![CDATA[".LOCALE_ELERTS_MESSAGES_CONFIRM_MSG."]]></text>";
		$out .= "</form>";
		$out .="</module>";
		return $out;
	}
	
	function module_list_optout($parameters){
		$page	= $this->check_parameters($parameters,"page",1);
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		$approver=0;
		$publisher=0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (
					("PAGE_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("PAGE_APPROVER"==$access[$index])
				){
					$approver=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("PAGE_PUBLISHER"==$access[$index])
				){
					$publisher=1;
				}
			}
		}		
		$cond ="";
		if ($approver==1){
			$cond .= "eoio_type='__EMAIL_PAGE_APPROVER__'";
		}
		if ($publisher==1){
			if ($cond!=""){
				$cond.= " or ";
			}
			$cond .= "eoio_type='__EMAIL_PAGE_PUBLISHER__'";
		}
		if ($cond!=""){
			$cond = " and ($cond)";
		}
		$uid = $_SESSION["SESSION_USER_IDENTIFIER"];
		$sql	= "select * from elert_optinout 
left outer join elert_optin_list on eoil_list = eoio_identifier and eoil_user = $uid
where eoio_client=$this->client_identifier $cond";
//print "$sql";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables["PAGE_BUTTONS"] = Array(
//			Array("CANCEL", $this->module_command."GROUP_LIST",LOCALE_CANCEL),
//			Array("ADD", $this->module_command."ADD&amp;group_list=$group_list",ADD_NEW)
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
			$variables["HEADER"] 			= MANAGEMENT_ELERTS." - ".LOCALE_LIST;
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$i]=Array(
					"identifier"	=> $r["eoio_identifier"],
					"attributes"	=> Array(
						Array(ENTRY_TITLE,$r["eoio_label"],"TITLE","NO"),
					)
				);
				if ($this->check_parameters($r,"eoil_user","null")=="null"){
					if ($r["eoio_status"]==0){
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_ELERT_SIGNED_UP, LOCALE_YES);
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_ELERT_STATUS, LOCALE_NO);
					} else {
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_ELERT_SIGNED_UP, LOCALE_NO);
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_ELERT_STATUS, LOCALE_YES);
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"] = Array(
							Array("TOGGLE", $this->module_command."OPTION_TOGGLE&amp;opt=1", LOCALE_ELERT_ENABLE_EMAIL)
						);
					}
				} else {
					if ($r["eoio_status"]==0){
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_ELERT_SIGNED_UP, LOCALE_YES);
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_ELERT_STATUS, LOCALE_NO);
					} else {
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_ELERT_SIGNED_UP, LOCALE_YES);
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array(LOCALE_ELERT_STATUS, LOCALE_YES);
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"] = Array(
							Array("TOGGLE", $this->module_command."OPTION_TOGGLE&amp;opt=0", LOCALE_ELERT_DISABLE_EMAIL)
						);
					}
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

	function module_list_option_toggle($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier");
		$opt		= $this->check_parameters($parameters,"opt");
		$uid		= $_SESSION["SESSION_USER_IDENTIFIER"];
		if ($opt==0){
			$sql = "delete from elert_optin_list where eoil_client = $this->client_identifier and eoil_user = $uid and eoil_list = $identifier";
			$this->call_command("DB_QUERY",array($sql));
		} else {
			$sql = "insert into elert_optin_list (eoil_client, eoil_user, eoil_list) values ($this->client_identifier, $uid, $identifier)";
			$this->call_command("DB_QUERY",array($sql));
		}
	}
	function module_list_create($parameters){
		$list	= $this->check_parameters($parameters,"list");
		$sql 	= "insert into elert_optinout (eoio_client, eoio_type, eoio_label, eoio_status) values($this->client_identifier, '__EMAIL_".$list."__', 'Email Alerts for ".constant("LOCALE_ELERT_".$list)."', 0)";
		$this->call_command("DB_QUERY",array($sql));
	}
	
	/*************************************************************************************************************************
    * defintionlist filter (for spanning) 
    *************************************************************************************************************************/
	function def_filter($parameters){
		$cmd		= $this->check_parameters($parameters,"command");
		$ref 		= $this->check_parameters($parameters,"reference");
		$type		= $this->check_parameters($parameters,"identifier");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"filter",__LINE__,join($parameters,", ")));
		}
		$out = "\t\t\t\t<form name=\"order_filter_form\" label=\"Shopping Order filter\" method=\"GET\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\" value=\"$cmd\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" value=\"1\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"search\" value=\"1\"/>\n";
		/**
		* retrieve the list of groups and display for selection
		*/
		$out .= "\t\t\t\t</form>";
		/**
		* return the filter XML document
		*/
		return $out;
	}
}
?>