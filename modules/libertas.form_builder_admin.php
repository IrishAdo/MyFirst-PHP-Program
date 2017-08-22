<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.form_builder_admin.php
* @date 09 Oct 2002
*/
/**
* 
*/
class formbuilder_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_name_label			= "Form Builder Management Module";
	var $module_name				= "formbuilder_admin";						// name of module is used in configuration
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";	// what group does this module belong to
	var $module_label				= "MANAGEMENT_FORM_BUILDER";				// label describing the module 
	var $module_creation			= "26/11/2004";								// date module was created
	var $module_modify	 			= '$Date: 2005/02/22 15:56:30 $';			
	var $module_version 			= '$Revision: 1.23 $';						// Actual version of this module
	var $module_admin				= "1";										// does this system have an administrative section
	var $module_command				= "FORMBUILDERADMIN_";						// what does this commad start with ie TEMP_ (use caps)
	var $webContainer				= "FORMBUILDER_";							// what is the webcontainer command
	var $module_display_options		= array();									// what output channels does this module have
	var $module_admin_options 		= array();

	var $module_admin_user_access	= array(
		array("FORMBUILDERADMIN_ALL", 			"COMPLETE_ACCESS"),
		array("FORMBUILDERADMIN_AUTHOR", 		"ACCESS_LEVEL_AUTHOR")
	);
	var $author_access				= 0;
	var $admin_access				= 0;
	var $metadata_fields			= Array();
//	var $available_forms 			= array();
	/**
	*  Class Variables
	*/
	var $preferences = Array();
	var $fbs_life_labels = Array("Does not expire",
			"1 Day", "1 week", "1 month (30 days)", "3 months (90 days)", 
			"6 months (180 days)", "9 months (270 days)", "1 Year", "2 Years", 
			"3 Years", "4 Years", "5 Years", "10 Years"
		);
	var $fbs_grace_labels = Array("Does not have grace period",
			"1 Day", "1 week", "1 month (30 days)", "3 months (90 days)", 
			"6 months (180 days)", "9 months (270 days)", "1 Year", "2 Years", 
			"3 Years", "4 Years", "5 Years", "10 Years"
		);
	var $fbs_review_labels = Array("Does not have review period",
			"1 Day", "1 week", "1 month (30 days)", "3 months (90 days)", 
			"6 months (180 days)", "9 months (270 days)", "1 Year", "2 Years", 
			"3 Years", "4 Years", "5 Years", "10 Years"
		);
	var $fbs_life_values = Array(0,
			1, 7, 30, 90, 
			180, 270, 365, 730, 
			1095, 1461, 1826, 3650
		);
	/**
	*  Class Methods
	*/
	
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*/
		if (strpos($user_command, $this->module_command)===0){
			/**
			* basic commands
			*/
			if ($user_command==$this->module_command."DEBUG_ON"){
				$this->module_debug=true;
			}
			if ($user_command==$this->module_command."DEBUG_OFF"){
				$this->module_debug=false;
			}
			if ($user_command==$this->module_command."GET_PREFS"){
				return $this->preferences;
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
			if ($user_command==$this->module_command."LIST_FORMS"){
				return $this->available_forms;
			}
			if ($user_command==$this->module_command."CREATE_NEW_CLIENT_DETAILS"){
//				return $this->create_client_details($parameter_list);
			}
			if ($user_command==$this->module_command."LOAD_PREFS"){
				return $this->load_prefs();
			}
			/**
			* needed for administrative access
			*/
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			if ($user_command==$this->module_command."CACHE_AVAILABLE_FORM"){
				return $this->cache_available_forms($parameter_list);
			}
			if ($user_command==$this->module_command."BUILDFORMS"){
				return $this->build_override_forms($parameter_list);
			}
			if ($user_command==$this->module_command."GET_DATES"){
				return $this->get_dates($parameter_list);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Admin functions
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($this->admin_access){
				/*************************************************************************************************************************
                * IF YOU HAVE ANY ROLE IN THIS MODULE AT ALL YOU CAN SEE THE LIST OF FORMS
                *************************************************************************************************************************/
				if ($user_command==$this->module_command."LIST"){
					return $this->module_list($parameter_list);
				}
				if ($user_command==$this->module_command."RESTORE"){
//					return $this->restore($parameter_list);
				}
				/*************************************************************************************************************************
                * AUTHOUR ACCESS ROLE ENABLED
                *************************************************************************************************************************/
				if ($this->author_access){
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- Author Access functions
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					if (($user_command==$this->module_command."MODIFY")){
						return $this->module_modify($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE"){
						$this->module_remove($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
					if ($user_command==$this->module_command."SAVE"){
						$this->module_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
				}
				/*************************************************************************************************************************
                * SUBMITTER ACCESS ROLE ENABLED
                *************************************************************************************************************************/
				if ($this->submitter_access){
					/**************************************************************************************************************************
					* has two functions submit new entry and save new entry only available on Type Save Content
					**************************************************************************************************************************/
					if (($user_command==$this->module_command."EXECUTE_ADD") || ($user_command==$this->module_command."EXECUTE_EDIT")){
						return $this->module_execute($parameter_list);
					}
					if (($user_command==$this->module_command."EXECUTE_SAVE")){
						return $this->module_execute_save($parameter_list);
					}

					/*	Execute to Delete from Members Database (Added By: Muhammad Imran Mirza) */
					if ($user_command==$this->module_command."EXECUTE_DELETE"){
						return $this->module_execute_delete($parameter_list);
					}
				}
			}
		}else{
			return "";// wrong command sent to system
		}
	}
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
		* lock down the access
		*************************************************************************************************************************/
		$this->admin_access						= 0; // access to the admin functions
		$this->author_access					= 0; // access to the author functions
		$this->submitter_access					= 0; // access to the submitter functions
		/*************************************************************************************************************************
		* load the required locale
		*************************************************************************************************************************/
		$this->load_locale("form_builder_admin");
		/*************************************************************************************************************************
		* define the list of Editors in this module and define them as empty
		*************************************************************************************************************************/
		$this->editor_configurations = Array(
			"ENTRY_CONFIRM_SCREEN" => $this->generate_default_editor()
		);
		/*************************************************************************************************************************
        * get metadata fields
        *************************************************************************************************************************/
		$this->metadata_fields	= $this->call_command("METADATAADMIN_GET_FIELDLIST", Array());
		/*************************************************************************************************************************
        * set up defaults
        *************************************************************************************************************************/
		$this->module_admin_options = array(
			array("FORMBUILDERADMIN_LIST", "Advanced Form Builder", "FORMBUILDERADMIN_AUTHOR", "Interactive tools/Standard Form Manager")
		);
		/*************************************************************************************************************************
		* request the page size 
		*************************************************************************************************************************/
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql = "select * from formbuilder_settings 
					inner join metadata_details on md_link_id = fbs_identifier and fbs_client = md_client and md_module='$this->webContainer'
				where fbs_client=$this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$c=0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if($c==0){
				$this->available_forms = Array();
			}
//        	$this->available_forms[count($this->available_forms)] = Array($r["fbs_label"],"FORMBUILDER_DISPLAY&amp;identifier=".$r["fbs_identifier"]);
			$this->available_forms[count($this->available_forms)] = Array("id"=>"libertas_fbs_".$r["fbs_identifier"], "label"=>$r["md_title"]);
			if ($r["fbs_in_admin"]==1){
				$this->module_admin_options[count($this->module_admin_options)] = array("FORMBUILDERADMIN_EXECUTE_ADD&amp;identifier=".$r["fbs_identifier"], $r["md_title"], "FORMBUILDERADMIN_AUTHOR", "Security/User Manager");
			}
        }
        $this->call_command("DB_FREE",Array($result));
		/*************************************************************************************************************************
		* define the admin access that this user has.
		*************************************************************************************************************************/
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		if ($this->parent->module_type=="admin"){
			for($i=0;$i < $max_grps; $i++){
				$access = $grp_info[$i]["ACCESS"];
				$length_of_array=count($access);
				for ($index=0;$index<$length_of_array;$index++){
					if (($this->module_command."ALL"==$access[$index]) || ("ALL"==$access[$index])){
						$this->admin_access					= 1; // can access the admin commands
						$this->author_access				= 1; // can access the admin commands
						$this->submitter_access				= 1;
					}
					if ($this->module_command."AUTHOR"==$access[$index]){
						$this->admin_access					= 1; // can access the admin commands
						$this->author_access				= 1; // can access the admin commands
					}
				}
			}
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
		* Table structure for table 'formbuilder_settings'
		*/
		$fields = array(
			array("fbs_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbs_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbs_label"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbs_ecommerce"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbs_pricingstructure"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbs_fixedprice"			,"double"					,"NOT NULL"	,"default '0'"),
			array("fbs_price_link"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbs_fieldcount"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbs_all_locations"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbs_set_inheritance"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbs_type"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbs_command"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbs_target_menu"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
//			array("fbs_override"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbs_life"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbs_grace"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbs_review"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbs_in_admin"			,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="fbs_identifier";
		$tables[count($tables)] = array("formbuilder_settings", $fields, $primary);
		/**
		* Table structure for table 'formbuilder_settings'
		*/
		$fields = array(
			array("fbo_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbo_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbo_command"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbo_owner"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
		);
		$primary ="fbo_identifier";
		$tables[count($tables)] = array("formbuilder_override", $fields, $primary);
		/**
		* Table structure for table 'formbuilder_merge_map'
		*/
		$fields = array(
			array("fbmm_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbmm_setting"	,"unsigned integer"			,"NOT NULL"	,"default '0'"), // link to a settings record
			array("fbmm_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbmm_mapping"	,"varchar(255)"				,"NOT NULL"	,"default ''")
		);
		$primary ="fbmm_identifier";
		$tables[count($tables)] = array("formbuilder_merge_map", $fields, $primary);
		/**
		* Table structure for table 'formbuilder_module_map'
		*/
		$fields = array(
			array("fbmm_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbmm_setting"	,"unsigned integer"			,"NOT NULL"	,"default '0'"), // link to a settings record
			array("fbmm_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbmm_link_id"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbmm_module"		,"varchar(255)"				,"NOT NULL"	,"default ''")
		);
		$primary ="fbmm_identifier";
		$tables[count($tables)] = array("formbuilder_module_map", $fields, $primary);
		/**
		* Table structure for table 'formbuilder_field_map'
		*/
		$fields = array(
			array("fbfm_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbfm_setting"	,"unsigned integer"			,"NOT NULL"	,"default '0'"), // link to a settings record
			array("fbfm_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbfm_fieldname"	,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbfm_label"		,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbfm_type"		,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbfm_belongs"	,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbfm_labelpos"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbfm_rank"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbfm_map"		,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbfm_auto"		,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbfm_required"	,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="fbfm_identifier";
		$tables[count($tables)] = array("formbuilder_field_map", $fields, $primary);
		/**
		* Table structure for table 'formbuilder_price'
		*/
		$fields = array(
			array("fbp_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("fbp_setting"		,"unsigned integer"			,"NOT NULL"	,"default '0'"), // link to a settings record
			array("fbp_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fbp_value"		,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fbp_price"		,"double"					,"NOT NULL"	,"default '0'")
		);
		$primary ="fbp_identifier";
		$tables[count($tables)] = array("formbuilder_price", $fields, $primary);
		
		return $tables;
	}
	/*************************************************************************************************************************
    * list the forms available
    *************************************************************************************************************************/
	function module_list($parameters){
		if ($this->page_size == 0){
			$this->page_size = 50;
		}
		$where = "";
		$join="";
		$order_by="";
		$status =array();
		
		$order_by .= "order by fb_identifier desc";
		$lang_of_choice = "en";
		if (empty($filter_translation)){
			$translation = $lang_of_choice;
		} else {
			$translation = $filter_translation;
		}
		
		$sql = "select * from formbuilder_settings 
					inner join metadata_details on md_module = '$this->webContainer' and md_client=fbs_client and fbs_identifier = md_link_id
				where fbs_client = $this->client_identifier order by fbs_label asc";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$variables = Array();
		$variables["FILTER"]			= "";
		$variables["as"]				= "table";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		if($this->parent->module_type=="admin"){
			if($this->author_access){
				$variables["PAGE_BUTTONS"] = Array(Array("ADD",$this->module_command."MODIFY",ADD_NEW));
			}
		}
		
			$result = $this->call_command("DB_QUERY",array($sql));
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
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
				
				if ($number_of_records>0){
					$num_pages=floor($number_of_records / $this->page_size);
					$remainder = $number_of_records % $this->page_size;
					if ($remainder>0){
						$num_pages++;
					}
				}else{
					$num_pages=0;
					$remainder=0;
				}
				$counter=0;
				
				$variables["NUMBER_OF_ROWS"]	= $number_of_records;
				$variables["START"]				= $goto;
				$variables["FINISH"]			= $finish;
				$variables["CURRENT_PAGE"]		= $page;
				$variables["NUMBER_OF_PAGES"]	= $num_pages;
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
				$variables["ENTRY_BUTTONS"] =Array();
				$variables["CONDITION"]= array();
				$variables["RESULT_ENTRIES"] =Array();
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
					$counter++;
					$i				= count($variables["RESULT_ENTRIES"]);
					if($r["fbs_type"]==0){
						for($zi=0; $zi<count($this->fbs_life_values); $zi++){
							if($r["fbs_life"]==$this->fbs_life_values[$zi]){
								$fbs_life	= $this->fbs_life_labels[$zi];
							}
						}
						/*************************************************************************************************************************
                        * list the forms that will be overridden buy this form
                        *************************************************************************************************************************/
						$sql = "select * from formbuilder_override where fbo_owner = ".$r["fbs_identifier"]." and fbo_client = $this->client_identifier and fbo_command!=''";
						$result_fbo  = $this->call_command("DB_QUERY",Array($sql));
						$fbs_override="";
                        while($r_fbo = $this->call_command("DB_FETCH_ARRAY",Array($result_fbo))){
                        	$fbs_override .= "<li>".$this->get_constant($r_fbo["fbo_command"])."</li>";
                        }
                        $this->call_command("DB_FREE",Array($result_fbo));
						if($fbs_override==""){
							$fbs_override	= "No override";
						} else {
							$fbs_override = "<ul style='margin:0px;padding:0px;'>$fbs_override</ul>";
						}
					} else {
						$fbs_override		= "Not applicable";
						$fbs_life			= "Not applicable";
					}
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=> $r["fbs_identifier"],
						"ENTRY_BUTTONS" => Array(),
						"attributes"	=> Array(
							Array(ENTRY_TITLE,		$r["md_title"], "TITLE", "NO"),
							Array(LOCALE_OVERRIDES,	$fbs_override,	""),
							Array(LOCALE_FBA_LIFE,	$fbs_life, 		"")
						)
					);
					if($r["fbs_type"]==0){
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array("Type","Save Content","Type","NO");
					} else {
						$variables["RESULT_ENTRIES"][$i]["attributes"][count($variables["RESULT_ENTRIES"][$i]["attributes"])] = Array("Type","Search","Type","NO");
					}
					if($this->author_access){
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])] = Array("EDIT", $this->module_command."MODIFY", EDIT_EXISTING);
						$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])] = Array("REMOVE", $this->module_command."REMOVE", REMOVE_EXISTING);
					}
					if($this->submitter_access){
						if($r["fbs_type"]==0){
							$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])] = Array("EXECUTEADD", $this->module_command."EXECUTE_ADD", EXECUTE_NEW);
						}
					}
				}
			}
		$out = $this->generate_list($variables);
		return $out;
	}
	/*************************************************************************************************************************
    * modify a form
    *************************************************************************************************************************/
	function module_modify($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		/*************************************************************************************************************************
        * Variable default definition
        *************************************************************************************************************************/
		$merged_fields			= Array();
		$module_fields			= Array();
		$merge					= 0; /// is there any merge fields
		$fbs_ecommerce			= 0;
		$fbs_label 				= "";
		$fbs_override			= "";
		$fbs_in_admin			= 0;
		$fbs_life				= 0;
		$fbs_grace				= 0;
		$fbs_review				= 0;
		$fbs_pricingstructure	= 1;
		$fbs_fixedprice			= "";
		$fbs_price_link			= "";
		$fbs_fieldcount			= 0;
		$charge_vat				= 0;
		$all_locations			= 0;
		$set_inheritance		= 0;
		$menu_locations			= Array();
		$confirm_screen			= "<p>Thank you the details have been submitted</p>";
		$fbs_type				= 0;
		$fbs_command			= "";
		$fbs_target_menu		= "";
		/*************************************************************************************************************************
        * is this an add new entry or a edit existing request -1 == NEW
        *************************************************************************************************************************/
		if ($identifier!=-1){
			/*************************************************************************************************************************
            * get the form the user wants to edit
            *************************************************************************************************************************/
			$sql = "select * from formbuilder_settings 
						left outer join memo_information on mi_type='$this->webContainer' and mi_link_id = fbs_identifier and mi_client=fbs_client and mi_field = 'fba_confirm'
						left outer join metadata_details on md_module='$this->webContainer' and md_link_id = fbs_identifier and md_client=fbs_client
					where fbs_identifier= $identifier and fbs_client = $this->client_identifier";
			$fbs_identifier = $identifier;
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$fbs_identifier			= $r["fbs_identifier"];
            	$fbs_label				= $r["md_title"];
            	$fbs_ecommerce			= $r["fbs_ecommerce"];
            	$fbs_pricingstructure	= $r["fbs_pricingstructure"];
            	$fbs_fixedprice			= $r["md_price"];
				$charge_vat				= $r["md_vat"];
            	$fbs_price_link			= $r["fbs_price_link"];
            	$fbs_fieldcount			= $r["fbs_fieldcount"];
				$all_locations			= $r["fbs_all_locations"];
				$set_inheritance		= $r["fbs_set_inheritance"];
				$fbs_type				= $r["fbs_type"];
				$fbs_command			= $r["fbs_command"];
				$fbs_target_menu		= $r["fbs_target_menu"];
				//$fbs_override			= $r["fbs_override"];
				$fbs_life				= $r["fbs_life"];
				$fbs_in_admin			= $r["fbs_in_admin"];
				$fbs_grace				= $r["fbs_grace"];
				$fbs_review				= $r["fbs_review"];
   				$confirm_screen			= $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$r["mi_memo"]));
    	    }
			$this->call_command("DB_FREE",Array($result));
			$sql = "select * from formbuilder_override where fbo_owner = ".$fbs_identifier." and fbo_client = $this->client_identifier";
			$result_fbo  = $this->call_command("DB_QUERY",Array($sql));
			$fbs_override=Array();
			$override_i=0;
			while($r_fbo = $this->call_command("DB_FETCH_ARRAY",Array($result_fbo))){
				$fbs_override[$override_i] = $r_fbo["fbo_command"];
				$override_i++;
			}
			$this->call_command("DB_FREE",Array($result_fbo));
			/*************************************************************************************************************************
    	    * retrieve the complete list of used fields
	        *************************************************************************************************************************/
			$sql = "select * from formbuilder_field_map where fbfm_client = $this->client_identifier and fbfm_setting=$identifier order by fbfm_rank";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				if($r["fbfm_belongs"]=="undefined"){
					$list = Array("FBA","-1");
				} else {
					$list = split("::", $r["fbfm_belongs"]);
					if($list[0]==""){
						$list[0]="FBA";
						$list[1]="-1";
					}
				}
				if (empty($module_fields[$list[0]])){
					$module_fields[$list[0]] = Array("link_id" => $list[1], "fieldlist" => Array(), "selected" => Array());
				}
				$fbfm_belongs = $r["fbfm_belongs"];
				if($fbfm_belongs=="undefined"){
					$fbfm_belongs="FBA::-1";
				}
				$module_fields[$list[0]]["selected"][count($module_fields[$list[0]]["selected"])] = Array($r["fbfm_fieldname"], $r["fbfm_label"], $r["fbfm_type"], $r["fbfm_map"], $r["fbfm_auto"], $fbfm_belongs, $r["fbfm_labelpos"], $r["fbfm_rank"], $r["fbfm_required"]);
            }
            $this->call_command("DB_FREE",Array($result));
			/*************************************************************************************************************************
    	    * retrieve the complete list of used merged fields
	        *************************************************************************************************************************/
			$sql = "select * from formbuilder_merge_map where fbmm_client = $this->client_identifier and fbmm_setting=$identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$merge=1;
				$merged_fields[count($merged_fields)] = $r["fbmm_mapping"];
            }
            $this->call_command("DB_FREE",Array($result));
		}
		/*************************************************************************************************************************
        * extract menu loactions that this formbuilder is available in
        *************************************************************************************************************************/ 
		$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST", Array("module"		=> $this->webContainer,"identifier"	=> $identifier));
		$target_menus	= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",	Array($fbs_target_menu));
		/*************************************************************************************************************************
        * Retrieve the list of Web Containers that this item can be put into
        *************************************************************************************************************************/
		$WebContainerList  = $this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"		=> $this->webContainer, "identifier"	=>$identifier));
		/*************************************************************************************************************************
   	    * retrieve the complete list of fields in modules used
        *************************************************************************************************************************/
		foreach ($module_fields as $key => $value ){
			if($key!="FBA"){
				$module_fields[$key]["fieldlist"] = $this->call_command($key."GET_FIELD_LIST", Array("identifier" => $module_fields[$key]["link_id"], "as" =>"Array"));
			}
		}
		
		/*************************************************************************************************************************
   	    * generate a list of modules that fields can be imported from
        *************************************************************************************************************************/
		$option = "<option value =''>Select One</option>";
       	$option .= "<option value ='USERS_::-1'><![CDATA[User Module]]></option>";
       	$option .= "<option value ='CONTACT_::-1'><![CDATA[Contact Module]]></option>";
		$sql = "select * from information_list where info_client = $this->client_identifier order by info_owner";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$listOfModules = Array("INFORMATIONADMIN_" => Array("INFORMATIONADMIN_", "Database Builder"), "EVENT_"=>Array("EVENTADMIN_", "Events Builder"));
		$prev="";
		$i=0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$owner	= $listOfModules[$r["info_owner"]][0];
			$db		= $listOfModules[$r["info_owner"]][1];
			if ($prev!=$owner){
				$i++;
				$prev = $owner;
			}
			$option .= "<option value ='$owner::".$r["info_identifier"]."'><![CDATA[$db :: ".$r["info_label"]."]]></option>";
        }
        $this->call_command("DB_FREE",Array($result));
		/*************************************************************************************************************************
        * generate XML structure for the form
        *************************************************************************************************************************/
		$out ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Form Builder]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"Form Builder\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\"><![CDATA[".$this->module_command."SAVE]]></input>";
		$out .="		<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>";
		$out .="		<showframe>1</showframe>";
		$out .="		<page_sections>";
		$out .="			<section label='Form Definition'>";
		$out .="				<input required=\"YES\" type=\"text\" name=\"fbs_label\" label=\"Form Label\" size=\"255\"><![CDATA[$fbs_label]]></input>";
		$out .="				<select name='fbs_type' label='".LOCALE_FBA_SELECT_TYPE."' onchange='my_toggle_type()'>";
		$out .= $this->gen_options(Array(0,1),Array("Save Content","Search Module"),$fbs_type);
/*
		$out .="					<option value='0'>Save Content</option>";
		$out .="					<option value='1'";
		if ($fbs_type==1){
			$out .=" selected='true'";
		}
		$out .=">Search Module</option>";
*/
		$out .="				</select>";
		$out .=" 				<import_fields name='import_module' label='Import fields from'>$option</import_fields>";
		$out .="				<toggle_type>";
		$out .="				<toggle type='charge' name=\"fbs_ecommerce\" label=\"Is there a charge for this form\" vat='$charge_vat'>
									<option value='0'";
		if ($fbs_ecommerce==0){
			$out .=" selected='true'";
		}
		$out .= ">No</option>
									<option value='1'";
		if ($fbs_ecommerce==1){
			$out .=" selected='true'";
		}
		$out .=	">Yes</option>";
		if ($fbs_ecommerce==1){
			$out.="<pricestructure val='".$fbs_pricingstructure."'>";
			if($fbs_pricingstructure == 1){
				$out .= "<input type='text' name='fixedprice' label='Fixed Price'><![CDATA[$fbs_fixedprice]]></input>";
			} else {
				$sql= "select * from formbuilder_price where fbp_setting =$identifier and fbp_client = $this->client_identifier";
				$result  = $this->call_command("DB_QUERY",Array($sql));
				$out .= "<fields link_id='$fbs_price_link'>";
                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$out .= "<field price='".$r["fbp_price"]."'>".$r["fbp_value"]."</field>";
                }
                $this->call_command("DB_FREE", Array($result));
				$out .= "</fields>";
			}
			$out.="</pricestructure>";
		} else {
			$out.="<pricestructure val='1'/>";
		}
		$out .= "</toggle>";
		$out .="				<toggle type='search'>";
/*
		$out .="					<fbs_command>$fbs_command</fbs_command>";
		$out .="					<fbs_target_menu id='$fbs_target_menu'>$target_menus</fbs_target_menu>";
*/
		$out .="				</toggle>";
		$out .="				</toggle_type>";
		$web_containers = split("~----~",$WebContainerList);
		if ( $web_containers[0] != "" ){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}
		$out .="			</section>";
		/*************************************************************************************************************************
        * Over ride
        *************************************************************************************************************************/
		$out .="			<section label='Override'>";
		$out .="				<select name='fbs_in_admin' label='".LOCALE_AVAIABLE_IN_ADMIN."'>";
		$out .= $this->gen_options(Array(0,1),Array(LOCALE_NO, LOCALE_YES), $fbs_in_admin);
		$out .="				</select>";
		$out .="				<checkboxes name='fbs_override' label='".LOCALE_FBA_SELECT_OVERRIDE."'>";
		$labels					 = Array(USERS_SHOW_REGISTER, USERS_SHOW_PROFILE_FORM);
		$values					 = Array("USERS_SHOW_REGISTER", "USERS_SHOW_PROFILE_FORM");
		$out .=					 $this->gen_options($values, $labels, $fbs_override);
		$out .="				</checkboxes>";
		$out .="			</section>";
		/*************************************************************************************************************************
        * dates are to have some labels
        *************************************************************************************************************************/
		$out .="			<section label='Actions'>";
		$out .="				<select name='fbs_life' label='".LOCALE_FBA_SELECT_LIFE."'>";
		$out .= $this->gen_options($this->fbs_life_values, $this->fbs_life_labels, $fbs_life);
		$out .="				</select>";
		$out .="				<select name='fbs_review' label='".LOCALE_FBA_SELECT_REVIEW."'>";
		$out .= $this->gen_options($this->fbs_life_values, $this->fbs_review_labels, $fbs_review,6);
		$out .="				</select>";
		$out .="				<select name='fbs_grace' label='".LOCALE_FBA_SELECT_GRACE."'>";
		$out .= $this->gen_options($this->fbs_life_values, $this->fbs_grace_labels, $fbs_grace,8);
		$out .="				</select>";
			/**
			* this code is for using a table to hold multiple entries 'group_admin_menu_access'
			*/ 
		$groups = $this->call_command("GROUP_GET_OBJECT",array("object" => $identifier, "module" => $this->webContainer));
		$out .="<checkboxes type=\"horizontal\" label=\"After approval what groups should this user be a member of\" name=\"group_list\" required=\"YES\" onclick=\"check_access\">$groups</checkboxes>\n";
		$out .="			</section>";
		$out .="			<section label='Structure'>";
		$out .=" 				<imported_fields name='import_module' label='Imported fields'>";
		foreach ($module_fields as $key => $value ){
			$len	= count($module_fields[$key]["fieldlist"]);
			for($i=0;$i<$len;$i++){
				$out   .= "<option value='".$key."::".$module_fields[$key]["link_id"]."'>".join("::",$module_fields[$key]["fieldlist"][$i])."</option>";
			}
		}
		$out .="				</imported_fields>";
		$out .=" 				<used_fields>";
		foreach ($module_fields as $key => $value ){
			$len	= count($module_fields[$key]["selected"]);
			$list	= $module_fields[$key]["selected"];
			for($i=0;$i<$len;$i++){
				$out   .= "<option>".join("::",$list[$i])."</option>";
			}
		}
		$out .="				</used_fields>";
		$out .="			</section>";
		$out .="			<section label='Merge'>";
		$out .=" 				<merge_fields>";
		if($merge==1){
			$out .= "<option>";
			$out .= join("</option><option>", $merged_fields);
			$out .= "</option>";
		}
		$out .="				</merge_fields>";
		$out .="			</section>";
		$out .= $this->location_tab($all_locations, $set_inheritance,$menu_locations, "");
		$out .="		<section label='Confirm Screen'>";
		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_CONFIRM_SCREEN",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .="			<textarea required=\"YES\" label=\"Confirm Screen\" size=\"40\" height=\"15\" name=\"confirm_screen\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'><![CDATA[$confirm_screen]]></textarea>";
		$out .="		</section>";
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";	
		return $out;
	}
	/*************************************************************************************************************************
    * save form structure and merge data
    *************************************************************************************************************************/
	function module_save($parameters){
		$fbs_identifier			= $this->check_parameters($parameters, "identifier",-1);
		$fbs_label				= $this->check_parameters($parameters, "fbs_label");
		$fbs_ecommerce			= $this->check_parameters($parameters, "fbs_ecommerce",0);
		$fbs_charge_vat			= $this->check_parameters($parameters, "charge_vat",0);
		$fbs_pricingstructure	= $this->check_parameters($parameters, "pricingStructure");
		$fbs_fieldcount			= $this->check_parameters($parameters, "form_num_of_fields",0);
		$hidden					= $this->check_parameters($parameters, "hidden", Array());
		$belongs				= $this->check_parameters($parameters, "belongs", Array());
		$fbs_type				= $this->check_parameters($parameters, "fbs_type");
		$fbs_command			= $this->check_parameters($parameters, "fbs_command");
		$fbs_target_menu		= $this->check_parameters($parameters, "fbs_target_menu");
		// 
		$fbs_life				= $this->check_parameters($parameters, "fbs_life");
		$fbs_review				= $this->check_parameters($parameters, "fbs_review");
		$fbs_grace				= $this->check_parameters($parameters, "fbs_grace");
		$fbs_in_admin			= $this->check_parameters($parameters, "fbs_in_admin", 0);
		//
		$fbs_override			= $this->check_parameters($parameters, "fbs_override");
		// locations tab	
		$all_locations			= $this->check_parameters($parameters, "all_locations");
		$menu_locations			= $this->check_parameters($parameters, "menu_locations", Array());
		$pmenu_locations		= $this->check_parameters($parameters, "pmenu_locations", -1);
		$set_inheritance		= $this->check_parameters($parameters, "set_inheritance");
		// web container list
		$replacelist			= $this->check_parameters($parameters, "web_containers", Array());
		$currentlyhave			= $this->check_parameters($parameters, "currentlyhave");
		$count_rss_containers	= $this->check_parameters($parameters, "totalnumberofchecks_web_containers");
		
		$confirm_screen	= trim($this->call_command("EDITOR_CONVERT_FONT_TO_SPAN", Array("string" => $this->split_me( $this->tidy( $this->validate($this->check_parameters($parameters,"confirm_screen") ) ),"'","&#39;") ) ));
		$longDescription		= $fbs_label;
		/*************************************************************************************************************************
        * set price
        *************************************************************************************************************************/
		$price					= Array();
		if ($fbs_pricingstructure==1){
			$price[0]			= $this->check_parameters($parameters,"fixed_price");
			$fbs_price			= $price[0];
		} else {
			$fbs_price_link		= $this->check_parameters($parameters,"priceArray");
			$price				= $this->check_parameters($parameters,$fbs_price_link);
			$label				= $this->check_parameters($parameters,"label");
			$fbs_price			= 0;
		}
		/*************************************************************************************************************************
		* get metadata record info
        *************************************************************************************************************************/
		$sql ="select * from metadata_details where md_module = '$this->webContainer' and md_client=$this->client_identifier and md_link_id = $fbs_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$md_fields = Array();
		$len = count($this->metadata_fields);
		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			for($i=0; $i<$len;$i++){
				$md_fields[$this->metadata_fields[$i]["key"]] = $r[$this->metadata_fields[$i]["key"]];
			}
		}
		$this->call_command("DB_FREE",Array($result));
		$md_fields["md_title"]	= $fbs_label;
		$md_fields["md_price"]	= $fbs_price;
		$md_fields["md_vat"]	= $fbs_charge_vat;
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$field_data		= Array();
		$search_index	= -1;
		$lbt 			= "";
		$mods = Array();
		for($i=0 ; $i < $fbs_fieldcount ; $i++){
			$field_data[count($field_data)] = Array(
			    "name"		=> $this->check_parameters($parameters,"form_name_".$i),
			    "label"		=> $this->check_parameters($parameters,"form_label_".$i),
			    "type"		=> $this->check_parameters($parameters,"form_type_".$i),
			    "rank"		=> $this->check_parameters($parameters,"form_rank_".$i),
			    "belongs"	=> $this->check_parameters($parameters,"form_belongs_".$i),
			    "labelpos"	=> $this->check_parameters($parameters,"form_labelpos_".$i),
			    "required"	=> ((strtolower($this->check_parameters($parameters,"form_required_".$i,"no"))=="yes")?1:0)
			);
			$modlist = split("::", $this->check_parameters($parameters,"form_belongs_".$i,"::"));
			$mods[$this->check_parameters($parameters,"form_belongs_".$i,"::")] = Array($modlist[0],$modlist[1]);
		}
		if($fbs_identifier==-1){
			/*************************************************************************************************************************
            * create a new form entry inthe database
            *************************************************************************************************************************/
			$fbs_identifier = $this->getUID(); // get the id that this record will be saved with.
			if($fbs_ecommerce==0){
				$fbs_fixedprice =0;
			} else {
				if($fbs_pricingstructure==1){
					$fbs_fixedprice = $price[0];
					$fbs_price_link='';
				} else {
					$fbs_fixedprice = 0;
					for($i=0;$i<count($price);$i++){
						$fbp_identifier = $this->getUID(); 
						$sql = "insert into formbuilder_price (fbp_identifier, fbp_setting, fbp_client, fbp_value, fbp_price)
								values ('$fbp_identifier', '$fbs_identifier', '$this->client_identifier', '".$label[$i]."', '".$price[$i]."')";
						$this->call_command("DB_QUERY",Array($sql));
					}
				}
			}
			$sql = "insert into formbuilder_settings (fbs_identifier, fbs_client, fbs_ecommerce, fbs_pricingstructure , fbs_fieldcount, fbs_price_link, fbs_type, fbs_command, fbs_target_menu, fbs_life, fbs_grace, fbs_review, fbs_in_admin)
					values ('$fbs_identifier', '$this->client_identifier', '$fbs_ecommerce', '$fbs_pricingstructure', '$fbs_fieldcount', '$fbs_price_link', '$fbs_type', '$fbs_command', '$fbs_target_menu', '$fbs_life', '$fbs_grace', '$fbs_review', '$fbs_in_admin')";
			$this->call_command("DB_QUERY",Array($sql));
			for($i=0 ; $i < $fbs_fieldcount ; $i++){
				$fbfm_identifier = $this->getUID();
				$sql = "insert into formbuilder_field_map (fbfm_identifier, fbfm_setting, fbfm_client, fbfm_fieldname, fbfm_label, fbfm_type, fbfm_belongs, fbfm_labelpos, fbfm_rank, fbfm_required)
							values ('$fbfm_identifier', '$fbs_identifier', '$this->client_identifier', '".$field_data[$i]["name"]."', '".$field_data[$i]["label"]."', '".$field_data[$i]["type"]."', '".$field_data[$i]["belongs"]."', '".$field_data[$i]["labelpos"]."', '".$field_data[$i]["rank"]."', '".$field_data[$i]["required"]."')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			for($i=0 ; $i < count($hidden) ; $i++){
				$fbfm_identifier = $this->getUID();
				$sql = "insert into formbuilder_field_map (fbfm_identifier, fbfm_setting, fbfm_client, fbfm_fieldname, fbfm_label, fbfm_type, fbfm_belongs, fbfm_labelpos, fbfm_rank)
							values ('$fbfm_identifier', '$fbs_identifier', '$this->client_identifier', '".$hidden[$i]."', '', 'hidden', '".$belongs[$i]."', '', '0')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			foreach($mods as $key => $mods){
				$fbmm_identifier	= $this->getUID();
				$sql = "insert into formbuilder_module_map (fbmm_identifier, fbmm_setting, fbmm_client, fbmm_module, fbmm_link_id) values ('$fbmm_identifier', '$fbmm_setting', '$fbmm_client', '".$mods[0]."', '".$mods[1]."')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			/*************************************************************************************************************************
            * store any merged fields
            *************************************************************************************************************************/
			$fbmm_setting		= $fbs_identifier;
			$fbmm_client		= $this->client_identifier;
			$fbmm_mapping		= $this->check_parameters($parameters,"merge");
			for($i = 0; $i<count($fbmm_mapping);$i++){
				$fbmm_identifier	= $this->getUID();
				$sql = "insert into formbuilder_merge_map (fbmm_identifier, fbmm_setting, fbmm_client, fbmm_mapping) values ('$fbmm_identifier', '$fbmm_setting', '$fbmm_client', '".$fbmm_mapping[$i]."')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			/*************************************************************************************************************************
            * store the confirm screen
            *************************************************************************************************************************/
			$this->call_command("MEMOINFO_INSERT", array("mi_type"=>$this->webContainer, "mi_memo"=>$confirm_screen, "mi_link_id" => $fbs_identifier, "mi_field" => "fba_confirm", "debug" =>0));
			/*************************************************************************************************************************
            * create a webobject for this entry
            *************************************************************************************************************************/
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array("owner_module" 	=> $this->webContainer,"owner_id" 		=> $fbs_identifier,"label" 		=> $fbs_label,"wo_command" => $this->webContainer."DISPLAY","cmd" => "ADD","previous_list" => $currentlyhave,"new_list" => $replacelist));
		} else {
			/*************************************************************************************************************************
            * Update an existing form entry
            *************************************************************************************************************************/
			$sql = "delete from formbuilder_price where fbp_setting = '$fbs_identifier' and fbp_client = '$this->client_identifier'";
			$this->call_command("DB_QUERY",Array($sql));
			if($fbs_ecommerce==0){
				$fbs_fixedprice =0;
			} else {
				if($fbs_pricingstructure==1){
					$fbs_fixedprice = $price[0];
					$fbs_price_link='';
				} else {
					$fbs_fixedprice = 0;
					for($i=0;$i<count($price);$i++){
						$fbp_identifier = $this->getUID(); 
						$sql = "insert into formbuilder_price (fbp_identifier, fbp_setting, fbp_client, fbp_value, fbp_price)
								values ('$fbp_identifier', '$fbs_identifier', '$this->client_identifier', '".$label[$i]."', '".$price[$i]."')";
						$this->call_command("DB_QUERY",Array($sql));
					}
				}
			}
			$sql = "update formbuilder_settings set	
						fbs_grace = '$fbs_grace', 
						fbs_review = '$fbs_review', 
						fbs_in_admin = '$fbs_in_admin',
						fbs_label = '$fbs_label',  fbs_ecommerce='$fbs_ecommerce', 
						fbs_pricingstructure='$fbs_pricingstructure', fbs_fixedprice='$fbs_fixedprice', 
						fbs_fieldcount='$fbs_fieldcount', fbs_price_link='$fbs_price_link', 
						fbs_type='$fbs_type', fbs_command='$fbs_command', fbs_target_menu='$fbs_target_menu', 
						fbs_life='$fbs_life'
					where fbs_identifier='$fbs_identifier' and fbs_client='$this->client_identifier'";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "delete from formbuilder_field_map where fbfm_setting = '$fbs_identifier' and fbfm_client = $this->client_identifier";
			$this->call_command("DB_QUERY",Array($sql));
			for($i=0 ; $i < $fbs_fieldcount ; $i++){
				if ($field_data[$i]["name"]!=""){
					$fbfm_identifier = $this->getUID();
					$sql = "insert into formbuilder_field_map (fbfm_identifier, fbfm_setting, fbfm_client, fbfm_fieldname, fbfm_label, fbfm_type, fbfm_belongs, fbfm_labelpos, fbfm_rank, fbfm_required)
						values ('$fbfm_identifier', '$fbs_identifier', '$this->client_identifier', '".$field_data[$i]["name"]."', '".$field_data[$i]["label"]."', '".$field_data[$i]["type"]."', '".$field_data[$i]["belongs"]."', '".$field_data[$i]["labelpos"]."', '".$field_data[$i]["rank"]."', '".$field_data[$i]["required"]."')";
					$this->call_command("DB_QUERY",Array($sql));
				}
			}
			for($i=0 ; $i < count($hidden) ; $i++){
				$fbfm_identifier = $this->getUID();
				$sql = "insert into formbuilder_field_map (fbfm_identifier, fbfm_setting, fbfm_client, fbfm_fieldname, fbfm_label, fbfm_type, fbfm_belongs, fbfm_labelpos, fbfm_rank)
							values ('$fbfm_identifier', '$fbs_identifier', '$this->client_identifier', '".$hidden[$i]."', '', 'hidden', '".$belongs[$i]."', '', '0')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			/*************************************************************************************************************************
            * store any merged fields
            *************************************************************************************************************************/
			$fbmm_setting		= $fbs_identifier;
			$fbmm_client		= $this->client_identifier;
			$fbmm_mapping		= $this->check_parameters($parameters,"merge",Array());
			$sql = "delete from formbuilder_merge_map where fbmm_setting = '$fbs_identifier' and fbmm_client = '$this->client_identifier'";
			$this->call_command("DB_QUERY",Array($sql));
				for($i = 0; $i<count($fbmm_mapping);$i++){
				$fbmm_identifier	= $this->getUID();
				$sql = "insert into formbuilder_merge_map (fbmm_identifier, fbmm_setting, fbmm_client, fbmm_mapping) values ('$fbmm_identifier', '$fbmm_setting', '$fbmm_client', '".$fbmm_mapping[$i]."')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			$sql = "delete from formbuilder_module_map where fbmm_setting = '$fbs_identifier' and fbmm_client = '$this->client_identifier'";
			$this->call_command("DB_QUERY",Array($sql));
			foreach($mods as $key => $mods){
				$fbmm_identifier	= $this->getUID();
				$sql = "insert into formbuilder_module_map (fbmm_identifier, fbmm_setting, fbmm_client, fbmm_module, fbmm_link_id) values ('$fbmm_identifier', '$fbmm_setting', '$fbmm_client', '".$mods[0]."', '".$mods[1]."')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			/*************************************************************************************************************************
            * Update the confirm screen
            *************************************************************************************************************************/
			$this->call_command("MEMOINFO_UPDATE", array("mi_type"=>$this->webContainer,"mi_memo"=>$confirm_screen, "mi_link_id" => $fbs_identifier, "mi_field" => "fba_confirm", "debug" =>0));
			/*************************************************************************************************************************
            * Update the webobject
            *************************************************************************************************************************/
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array("owner_module" 	=> $this->webContainer,"owner_id"=> $fbs_identifier, "label"=> $fbs_label, "wo_command"	=> $this->webContainer."DISPLAY", "cmd"=> "UPDATE", "previous_list" => $currentlyhave, "new_list"=> $replacelist ));
		}
		/*************************************************************************************************************************
		* get existing settings and save new settings and recache special pages if needed
		*************************************************************************************************************************/
		$sql = "select * from formbuilder_override where fbo_owner = ".$fbs_identifier." and fbo_client = $this->client_identifier";
		$result_fbo  = $this->call_command("DB_QUERY",Array($sql));
		$fbs_override_existings=Array();
		$override_i=0;
		while($r_fbo = $this->call_command("DB_FETCH_ARRAY",Array($result_fbo))){
			$fbs_override_existings[$override_i] = $r_fbo["fbo_command"];
			$override_i++;
		}
		$this->call_command("DB_FREE",Array($result_fbo));
		$change=0;
		if ($override_i>0){ // if no exist then none to delete
			$sql = "delete from formbuilder_override where fbo_command not in ('".join("', '", $fbs_override_existings)."') and fbo_client=$this->client_identifier and fbo_owner=$fbs_identifier";
			$this->call_command("DB_QUERY",Array($sql));
			$change=1;
		}
		for($i=0 ; $i < count($fbs_override) ; $i++){
			if(!in_array($fbs_override[$i], $fbs_override_existings)){
				$sql = "insert into formbuilder_override (fbo_client, fbo_owner, fbo_command) values ($this->client_identifier, '$fbs_identifier', '".$fbs_override[$i]."')";
				$this->call_command("DB_QUERY",Array($sql));
				$change=1; //if a new one is entered then we need to restore the special pages
			}
		}
		/**
        * add metadata for this record
        */
		$this->call_command("METADATAADMIN_MODIFY", Array("identifier"=>$fbs_identifier, "module"=> $this->webContainer, "fields" => $md_fields, "command"=>"EDIT", "longDescription" => $longDescription));
		/**
		* Save menu locations
		*/
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", Array("menu_locations"=> $menu_locations, "module"=> $this->webContainer, "identifier"=> $fbs_identifier, "all_locations"	=> $all_locations));
		/**
		* Save group access
		*/
		$this->call_command("GROUP_SET_OBJECT", Array("module"=>$this->webContainer, "object"=>$fbs_identifier, "params"=>$parameters));
		/**
		* Save inheritance
		*/
		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance($this->webContainer."DISPLAY",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations" => $child_locations,
					"module"		 => $this->webContainer,
					"identifier"	 => $fbs_identifier,
					"all_locations"	 => $all_locations,
					"delete"		 => 0
				)
			);
			$this->set_inheritance(
				$this->webContainer."DISPLAY",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",
					Array(
						"module"			=> $this->webContainer,
						"condition"			=> "micromenu_set_inheritance =1 and ",
						"client_field"		=> "micromenu_client",
						"table"				=> "micromenu_list",
						"primary"			=> "micromenu_identifier"
					)
				).""
			);
		}
		$this->tidyup_display_commands(
			Array(
				"all_locations"=>$all_locations,
				"tidy_table"=>"formbuilder_settings",
				"tidy_field_starter"=>"fbs_",
				"tidy_webobj"=>$this->webContainer."DISPLAY",
				"tidy_module"=>$this->webContainer
			)
		);
		$this->cache(Array("identifier"=>$fbs_identifier));
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		@unlink("$data_files/layout_".$this->client_identifier."_admin.xml");
		$this->call_command("ENGINE_REFRESH_SPECIAL");
	}
	
	/*************************************************************************************************************************
    * remove a form from the system
    *************************************************************************************************************************/
	function module_remove($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$sql = "delete from formbuilder_price where fbp_setting = '$identifier' and fbp_client = '$this->client_identifier'";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from formbuilder_settings where fbs_identifier='$identifier' and fbs_client='$this->client_identifier'";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from formbuilder_field_map where fbfm_setting = '$identifier' and fbfm_client = $this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fname = $data_files."/formbuilder_".$this->client_identifier."_".$lang."_".$identifier.".xml";
		@unlink($fname);
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		@unlink("$data_files/layout_".$this->client_identifier."_admin.xml");

	}
	
	/*************************************************************************************************************************
    * cache a form by its id
    *************************************************************************************************************************/
	function cache($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$lang="en";
		$sql = "select * from formbuilder_settings 
					inner join metadata_details on md_link_id = fbs_identifier and fbs_client = md_client and md_module='$this->webContainer'
				where fbs_client=$this->client_identifier and fbs_identifier = $identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$label="";
		$id="";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$id 	= "libertas_fbs_".$r["fbs_identifier"];
        }
        $this->call_command("DB_FREE",Array($result));
		$out = $this->call_command("FORMBUILDER_DISPLAY", Array("identifier"=>$identifier ,"show_module"=>0, "show_anyway"=>1, "formname" => $id));
		$fname = $data_files."/form_".$this->client_identifier."_".$lang."_".$id.".xml";
		$fp = fopen($fname, 'w');
		fwrite($fp, $out);
		fclose($fp);
		$um = umask(0);
		@chmod($fname, LS__FILE_PERMISSION);
		umask($um);
	}
	/*************************************************************************************************************************
    * cache the available forms
    *************************************************************************************************************************/
	function cache_available_forms($parameters){
		$frms = $this->available_forms;
		$lang="en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($frms!=""){
			for ($index=0,$max=count($frms);$index<$max;$index++){
				$out = $this->call_command("FORMBUILDER_DISPLAY", Array("identifier"=>join("",split("libertas_fbs_",$frms[$index]["id"])) ,"show_module"=>0, "show_anyway"=>1, "formname" => $frms[$index]["id"]));
				$fname = $data_files."/form_".$this->client_identifier."_".$lang."_".$frms[$index]["id"].".xml";
				$fp = fopen($fname, 'w');
				fwrite($fp, $out);
				fclose($fp);
				$um = umask(0);
				@chmod($fname, LS__FILE_PERMISSION);
				umask($um);
			}
		}
	}
	/*************************************************************************************************************************
    * build override forms
    *************************************************************************************************************************/
	function build_override_forms($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$override = Array();
		$form_list = $this->call_command("ENGINE_RETRIEVE", array("OVERRIDE_FORMS"));
		$sql= "select * from formbuilder_settings 
					inner join formbuilder_override on fbo_owner = fbs_identifier and fbo_client=fbs_client
				where fbs_client = $this->client_identifier and fbs_type=0";
		$result  = $this->call_command("DB_QUERY", Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY", Array($result))){
        	$fbs_override = $r["fbo_command"];
			for($i=0; $i < count($form_list); $i++){
				if($form_list[$i][1]!=""){
					for($find=0; $find<count($form_list[$i][1]); $find++){
						if($fbs_override==$form_list[$i][1][$find][1]){
							$override_position = count($override);
							$override[$override_position] = Array(
								$form_list[$i][1][$find][0],
								"FORMBUILDER_DISPLAY",
								$form_list[$i][1][$find][2],
								$r["fbs_label"],
								Array(
									"identifier"=>$r["fbs_identifier"],
									"show_anyway" => "1",
									"show_module" => "1"
								)
							);
						}
					}
				}
			}
        }
        $this->call_command("DB_FREE",Array($result));
		$root = $this->parent->site_directories["ROOT"];
		$module_dir = $this->parent->site_directories["MODULE_DIR"];
		for($i=0;$i<count($override);$i++){
			$page= "<"."?php
\$script	=\"index.php\";
\$mode 		=\"EXECUTE\";
\$command	=\"".$override[$i][1]."\";
\$fake_title=\"".$override[$i][3]."\";
\$extra		 = Array(";
$c=0;
foreach($override[$i][4] as $key =>$val){
	if($c!=0){
		$page .= ", ";
	}
	$page .= "\"$key\"=>\"$val\"";
	$c++;
}
$page .= ");
";
			if ($override[$i][2]=="VISIBLE"){
				$page.= "require_once \"admin/include.php\"; \r\n";
				$file = $root."/".$override[$i][0];
			} else {
				$page.= "require_once \"../admin/include.php\"; \r\n";
				$file = $root."/-/".$override[$i][0];
			}
			$page.= "require_once \"$module_dir/included_page.php\"; 
?".">";
			$fp = fopen($file,"w");
			fwrite($fp, $page);
			fclose($fp);
			$um = umask(0);
			@chmod($file, LS__FILE_PERMISSION);
			umask($um);
		}
	}
	
	/*************************************************************************************************************************
    * execute the defined form use section seperators instead of pages
    *************************************************************************************************************************/
	function module_execute($parameters){
		$identifier = $this->check_parameters($parameters, "user",$this->check_parameters($parameters, "identifier", -1));
		if ($identifier == -1){
			return "";
		}
		$cmd = $this->check_parameters($parameters, "command", "FORMBUILDERADMIN_EXECUTE_ADD");
		$user=-1;
		if ($cmd=="FORMBUILDERADMIN_EXECUTE_ADD"){
			$fbs_identifier = $identifier;
		} else {
			$fbs_identifier = $this->check_parameters($parameters, "module_identifier", -1);
			$user 	= $this->check_parameters($parameters, "user",$this->check_parameters($parameters, "identifier", -1));
		}
		$next_command	= $this->check_parameters($parameters, "next_command");
		$next_id	= $this->check_parameters($parameters, "next_id",-1);
		$lang="en";
		$module_fields			= Array();
		$data					= Array("errorCount"=>0);
		$sql 	= "select * from formbuilder_settings 
						inner join metadata_details on md_module = '$this->webContainer' and md_client=fbs_client and fbs_identifier = md_link_id
					where fbs_identifier = $fbs_identifier and fbs_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$id=-1;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
           	$fbs_label				= $r["md_title"];
           	$fbs_ecommerce			= $r["fbs_ecommerce"];
           	$fbs_pricingstructure	= $r["fbs_pricingstructure"];
           	$fbs_fixedprice			= $r["md_price"];
           	$fbs_price_link			= $r["fbs_price_link"];
           	$fbs_fieldcount			= $r["fbs_fieldcount"];
			$all_locations			= $r["fbs_all_locations"];
			$set_inheritance		= $r["fbs_set_inheritance"];
			$id 					= $r["fbs_identifier"];
			$life 					= $r["fbs_life"];
			$fbs_grace				= $r["fbs_grace"];
			$fbs_review				= $r["fbs_review"];
			$fbs_type				= $r["fbs_type"];
        }
		$this->call_command("DB_FREE",Array($result));
		$this->display=1;
		$error="";
		$sql = "select * from formbuilder_field_map where fbfm_client = $this->client_identifier and fbfm_setting=$fbs_identifier order by fbfm_rank";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$out ="";
		$total_pages=1;
		$errorCount=0;
		$error=Array();
		$module_list = Array();
		$useraccount_details = 0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$pos = count($module_fields);
			if(!in_array($r["fbfm_belongs"],$module_list)){
				$module_list[count($module_list)] = $r["fbfm_belongs"];
			}
			if($r["fbfm_type"]=="pagesplitter"){
				$seperator_page=1;
				$total_pages++;
			}
			$module_fields[$pos] = Array($r["fbfm_fieldname"], $r["fbfm_label"], $r["fbfm_type"], $r["fbfm_map"], $r["fbfm_auto"], $r["fbfm_belongs"], $r["fbfm_labelpos"], "value"=>$this->check_locale_starter($this->check_parameters($parameters,$r["fbfm_fieldname"])), "required"=>$r["fbfm_required"]);
			if($module_fields[$pos]["required"]=="yes" && ($page-1)==$total_pages && $page!=1){
				if($module_fields[$pos]["value"]==""){
					$errorCount++;
					$error[count($error)] = Array($module_fields[$pos][0], "<li>You did not fill in this field (".$module_fields[$pos][1].")</li>");
				}
				if($module_fields[$pos][2]=="password"){
					if($module_fields[$pos]["value"]!=$this->check_locale_starter($this->check_parameters($parameters,$r["fbfm_fieldname"]."_confirm"))){
						$errorCount++;
						$error[count($error)] = Array($module_fields[$pos][0], "<li>Your passwords do not match</li>");
					}
					if(strlen($module_fields[$pos]["value"])<6){
						$errorCount++;
						$error[count($error)] = Array($module_fields[$pos][0], "<li>Your passwords must be longer than 6 characters</li>");
					}
				}
			}
        }
		/*************************************************************************************************************************
        * edit means LOAD_FBA on each module
        *************************************************************************************************************************/
		$hidden = "";
		if ($cmd!="FORMBUILDERADMIN_EXECUTE_ADD"){
			if ($fbs_type==0){
				if($user !=-1){
					$hidden = "<input type='hidden' name='frm_action' value='EDIT'></input>";
					$values = Array();
					for($i=0;$i<count($module_list);$i++){
						$data = split("::",$module_list[$i]);
						$command = str_replace("ADMIN","",$data[0]);
						$new_fields = $this->call_command($command."LOAD_FBA", Array("module_identifier"=>$data[1], "mod_fields" =>$module_fields, "user"=>$user));
						if($new_fields!=""){
							$module_fields = $new_fields;
						}
					}
				}
			}
		}
/*		
		echo $user.'user<br>';
		echo print_r($new_fields).'new_fields<br>';
		echo print_r($module_fields).'module_fields<br>';
*/		
        $this->call_command("DB_FREE",Array($result));
		$hidden = "";// not sure why this is here
		$values = Array();
		$sp_counter=1;
		$oFile  = "<form label='$fbs_label' method='post' name='sform_execute_add' action=''>\n";
		$oFile  .= "<input type='hidden' name='command' value='FORMBUILDERADMIN_EXECUTE_SAVE'/>\n";
		$oFile  .= "<input type='hidden' name='__frm_identifier__' value='$fbs_identifier'/>\n";
		$oFile  .= $hidden;
		if($next_command != ""){
			$hidden .= "<input type='hidden' name='next_command' value='$next_command'></input>";
			$hidden .= "<input type='hidden' name='next_id' value='$next_id'></input>";
		}
		$user_status= 2;
		$ie_status  = 0;
		$user_identifier =-1;
		for ($i=0;$i<count($module_fields);$i++){
			if($module_fields[$i][2]=="hidden"){
				$key 	= $module_fields[$i][0];
				$label	= $module_fields[$i][1];
				if($key=="user_identifier"){
					$user_identifier = $module_fields[$i]["value"];
				}
				$oFile  .= "<input type='hidden' name='$key' value='".$module_fields[$i]["value"]."'/>\n";
			}
		}
		for ($i=0;$i<count($module_fields);$i++){
			if($module_fields[$i][2]=="system"){
				$key 	= $module_fields[$i][0];
				$label	= $module_fields[$i][1];
				if($key=="user_status"){
					$user_status  = $module_fields[$i]["value"];
				}
				if($key=="ie_status"){
					$ie_status  = $module_fields[$i]["value"];
				}
			}
		}
		$user_status_list = $this->call_command("USERS_STATUS_RETRIEVE",Array($user_status));
		$oFile  .= "<input type='hidden' name='identifier' value='$fbs_identifier'/>\n
					<page_sections>
					<section label='Settings'>
					<select label='User Status' name='__frm_user_status__'>
						".$user_status_list."
					</select>
					<select label='Entry Status' name='__frm_ie_status__'>
						".$this->gen_options(Array(0,1),Array(LOCALE_NOT_LIVE, LOCALE_LIVE),$ie_status)."
					</select>
					";
		if($life!=0){
			$user_date_expires = date("Y-m-d H:i:s", mktime (0,0,0,date("m"),date("d")+$life,date("Y")));
			$year_start = date("Y");
			$year_finish = date("Y")+5;
			$oFile  .= "<input type='date_time' label='Expires' name='__expires__'  value=\"$user_date_expires\" size=\"255\" year_start=\"$year_start\" year_end=\"$year_finish\"></input>";
			for($i = 0;$i<count($this->fbs_life_values);$i++){
				if($this->fbs_life_values[$i]==$fbs_grace){
					$fbs_grace_index = $i;
				}
				if($this->fbs_life_values[$i]==$fbs_review){
					$fbs_review_index = $i;
				}
				
			}
			$user_date_grace		= Date("Y-m-d H:i:s", 
				mktime(
					date("H", strtotime($user_date_expires)), 
					date("i", strtotime($user_date_expires)), 
					date("s", strtotime($user_date_expires)), 
					Date("m", strtotime($user_date_expires)), 
					Date("d", strtotime($user_date_expires)) + $fbs_grace, 
					Date("Y", strtotime($user_date_expires)) 
				)
			);
            $user_date_review		= Date("Y-m-d H:i:s", 
				mktime(
					date("H", strtotime($user_date_expires)), 
					date("i", strtotime($user_date_expires)), 
					date("s", strtotime($user_date_expires)), 
					Date("m", strtotime($user_date_expires)), 
					date("d", strtotime($user_date_expires))-$fbs_review, 
					Date("Y", strtotime($user_date_expires)) 
				)
			);
			$oFile  .= "<text><![CDATA[".LOCALE_MSG_DEFAULT_GRACE." ".$this->fbs_grace_labels[$fbs_grace_index]." ".LOCALE_MSG_DEFAULT_REVIEW." ".$this->fbs_grace_labels[$fbs_review_index]." ".LOCALE_MSG_DEFAULT_SPECIFIED."]]></text>";
			$oFile  .= "<input type='date_time' label='".LOCALE_REVIEW_STARTS."' name='__review__'  value=\"$user_date_review\" size=\"255\" year_start=\"$year_start\" year_end=\"$year_finish\"></input>";
			$oFile  .= "<input type='date_time' label='".LOCALE_GRACE_ENDS."' name='__grace__'  value=\"$user_date_grace\" size=\"255\" year_start=\"$year_start\" year_end=\"$year_finish\"></input>";
			$oFile  .= "<radio label='".LOCALE_SEND_EMAIL."' name='__send_email__'>".$this->gen_options(
							Array(0,1),
							Array(LOCALE_NO, LOCALE_YES),
						0)."</radio>";
/*
			if ($cmd=="FORMBUILDERADMIN_EXECUTE_ADD"){
				$oFile  .= "<radio label='".LOCALE_SEND_EMAIL."' name='__send_email__'>".$this->gen_options(
							Array(0,1,2),
							Array(LOCALE_SEND_EMAIL_NONE, LOCALE_SEND_EMAIL_VALIDATE, LOCALE_SEND_EMAIL_YOUR_USR_PWD),
						0)."</radio>";
			} else {
				$oFile  .= "<radio label='".LOCALE_SEND_EMAIL."' name='__send_email__'>".$this->gen_options(
							Array(0,1),
							Array(LOCALE_SEND_EMAIL_NONE, LOCALE_SEND_EMAIL_VALIDATE),
						0)."</radio>";
			}
*/
		}
		$oFile .= "</section>";
		/**
		* if the client has the group module then display the option
		*/
		if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
			$oFile .="<section label='".LOCALE_MEMBER_GROUP."' name='usergroups' >";
			/**
			* this code is for using a table to hold multiple entries 'group_admin_menu_access'
			*/ 
			$group_list = $this->call_command("GROUP_RETRIEVE_INFORMATION",Array("user_identifier" => $user_identifier));
			if (count($group_list)==0){
				if($user==-1){
					$sql = "select * from group_data 
							inner join group_to_object on gto_identifier=group_identifier and gto_client=group_client and gto_object=$fbs_identifier and gto_module ='FORMBUILDER_'
							where group_client=$this->client_identifier";
					if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
					$result  = $this->call_command("DB_QUERY",Array($sql));
	                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	                	$group_list[count($group_list)] = Array("IDENTIFIER"=>$r["group_identifier"]);
	                }
    	            $this->call_command("DB_FREE",Array($result));
				} else {

					$group_list = $this->call_command("GROUP_GET_DEFAULT",array("user_identifier" => $user_identifier));
				}
			}
			$groups = $this->call_command("GROUP_RETRIEVE_BY_TYPE",array($group_list));
			$oFile .="<checkboxes type=\"horizontal\" label=\"".USER_MEMBER_OF_GROUP."\" name=\"group_list\" required=\"YES\" onclick=\"check_access\">$groups</checkboxes>\n";
			$oFile .="</section>";
		}
		$oFile .= "<section label='Page1'>";
		for ($i=0;$i<count($module_fields);$i++){
			$key 	= $module_fields[$i][0];
			$label	= $module_fields[$i][1];
			if($module_fields[$i][2]=="__search__"){
				$out .="<input type=\"text\" name=\"search_phrase\" label=\"".$label."\" size=\"255\"";
				if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
					$out .= ' required="YES"';
				}
				$out.="><![CDATA[]]></input>\n";
			}
			if($module_fields[$i][2]=="label"){
				$out .="<text class='textlabel'><![CDATA[";
				$out .= $this->html_2_txt(html_entity_decode($module_fields[$i][1]));
				$out .= "]]></text>\n";
			}
			if($module_fields[$i][2]=="colsplitter"){
//				$out .="</seperator><seperator>\n";
			}
			if($module_fields[$i][2]=="rowsplitter"){
//				$out .="</seperator></seperator_row><seperator_row><seperator>\n";
			}
			if($module_fields[$i][2]=="pagesplitter"){
				$sp_counter++;
//				$out .="</seperator></seperator_row></section><section label='Page $sp_counter'><seperator_row><seperator>\n";
				$out .="</section><section label='Page $sp_counter'>\n";
			}
			if($module_fields[$i][2]=="text" || $module_fields[$i][2]=="URL" || $module_fields[$i][2]=="email" || $module_fields[$i][2]=="double"){
				$format = "";
				if($key=="user_login_name"){
					if($user==-1){
						$label .= " - (Minimum six characters)";
					}
					$format = "format='string::6'";
				}
				/*
				if($user!=-1 && $key=="user_login_name"){
					$out .="<input type=\"hidden\" name=\"user_login_name\" value='".strip_tags(html_entity_decode($module_fields[$i]["value"]))."'/><text><![CDATA[$label<br/>".strip_tags(html_entity_decode($module_fields[$i]["value"]))."]]></text>\n";
				} else {
				*/
					$out .="<input type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"255\" $format";
					if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
						$out .= ' required="YES"';
					}
					$out.="><![CDATA[".strip_tags(html_entity_decode($module_fields[$i]["value"]))."]]></input>\n";
				//}
			}
			if($module_fields[$i][2]=="password"){
				if ($cmd!="FORMBUILDERADMIN_EXECUTE_ADD"){
					$out .="<input type=\"password\" name=\"".$key."\" label=\"".$label." - (Minimum six characters)\" size=\"255\" required='YES' format='password'><![CDATA[__KEEP__]]></input>\n";
					$out .="<input type=\"password\" name=\"".$key."_confirm\" label=\"Please confirm password\" size=\"255\" required='$key'><![CDATA[__KEEP__]]></input>\n";
				} else {
					$out .="<input type=\"password\" name=\"".$key."\" label=\"".$label." - (Minimum six characters)\" size=\"255\" required='YES' format='password'><![CDATA[]]></input>\n";
					$out .="<input type=\"password\" name=\"".$key."_confirm\" label=\"Please confirm password\" size=\"255\" required='$key'><![CDATA[]]></input>\n";
				}
			}
			if($module_fields[$i][2]=="smallmemo"){
				$out .="<textarea type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"20\" height=\"6\"";
				if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
					$out .= ' required="YES"';
				}
				$out .= "><![CDATA[";
				$out .= $this->html_2_txt(html_entity_decode($module_fields[$i]["value"]));
				$out .= "]]></textarea>\n";
			}
			if($module_fields[$i][2]=="memo"){
				$out .="<textarea ";
				if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
					$out .= ' required="YES"';
				}
				$out .= " type=\"text\" name=\"".$key."\" label=\"".$label." Confirm\" size=\"40\" height=\"12\"><![CDATA[";
				$out .= htmlentities($this->html_2_txt(html_entity_decode($module_fields[$i]["value"])));
				$out .= "]]></textarea>\n";
			}
			
			if (($module_fields[$i][2]=="radio") || ($module_fields[$i][2]=="select") || ($module_fields[$i][2]=="check") || ($module_fields[$i][2]=="list")){
				$details  = split("::",$module_fields[$i][5]);
				$fdata = $this->call_command($details[0]."GET_FIELD_OPTIONS", Array("identifier" => $details[1], "field"=>$module_fields[$i][0] ,"as" =>"Array", "selected"=>$module_fields[$i]["value"]));
//				$val = $module_fields[$i]["value"];
				if (($module_fields[$i][2]!="check") && ($module_fields[$i][2]!="list")){
					$val = strip_tags(html_entity_decode($module_fields[$i]["value"]));
				} else {
					for($is=0,$ism = count($module_fields[$i]["value"]);$is<$ism;$is++){
						$val = strip_tags(html_entity_decode($this->check_parameters($module_fields[$i]["value"],$is,"")));
					}
				}
				if($module_fields[$i][2]=="select"){
					$module_fields[$i]["value"] = "<option value=''>Select one</option>";
				}
				$c=0;
				$m = count($fdata);
				if ($m>0){
					$prevSection = "";
					for($z=0;$z<$m;$z++){
						if ($fdata[$z]["section"] != $prevSection){
							if ($c != 0){
								$module_fields[$i]["value"] .= "</optgroup>";
							}
							$c++;
							$prevSection = $fdata[$z]["section"];
							$module_fields[$i]["value"] .= "<optgroup label=\"".$prevSection."\">";
						}
						$module_fields[$i]["value"] .= "<option value=\"".$fdata[$z]["value"]."\"";
						if ($fdata[$z]["selected"] == "true"){
							$module_fields[$i]["value"] .= " selected='true'";
						}
						$module_fields[$i]["value"] .= "><![CDATA[".$fdata[$z]["label"]."]]></option>";
					}
					if ($fdata[0]["section"]!=""){
						$module_fields[$i]["value"] .= "</optgroup>";
					}
				}
			}
			if($module_fields[$i][2]=="radio"){
				$out .="<radio ";
				if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
					$out .= ' required="YES"';
				}
				$out.=" type='vertical' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</radio>\n";
			}
			if($module_fields[$i][2]=="select"){
				$out .="<select ";
				if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
					$out .= ' required="YES"';
				}
				$out.=" name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
			}
			if($module_fields[$i][2]=="check"){
				$out .="<checkboxes ";
				if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
					$out .= ' required="YES"';
				}
				$out.=" type='vertical' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</checkboxes>\n";
			}
			if($module_fields[$i][2]=="list"){
				$out .="<select multiple='1' size='10' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
			}
			if($module_fields[$i][2]=="__category__"){
				$details  = split("::",$module_fields[$i][5]);
				$v = $this->call_command($details[0]."GET_FIELD_OPTIONS", Array("identifier" => $details[1], "field"=>$module_fields[$i][0] ,"as" =>"Array", "selected" => $module_fields[$i]["value"], "limit"=>"first"));
				$module_fields[$i]["value"] = $v[0]["value"];
				$out .="<select name=\"".$key."\" label=\"".$label."\" required=\"YES\"><option value=''>Select one</option>".$module_fields[$i]["value"]."</select>\n";
			}
			for($z=0;$z<$errorCount;$z++){
				if($error[$z][0] == "$key"){
					$out .="<text type=\"error\"><![CDATA[".$error[$z][1]."]]></text>";
				}
			}
		
		}
//		$oFile .= "$hidden<seperator_row><seperator>$out</seperator></seperator_row></section></page_section>";
		$oFile .= "$hidden$out</section></page_sections>";
		$oFile .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$oFile .= "</form>";
		$out =" <module name=\"".$this->module_name."\" display=\"form\">							<page_options>
								<header><![CDATA[$fbs_label]]></header>";
				$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON", Array("CANCEL", "USERS_LIST", LOCALE_CANCEL));
				$out .= "</page_options>$oFile</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * save the content of this form to multiple modules based on the fields used
    *************************************************************************************************************************/
	function module_execute_save($parameters){
		$frm_action 	 = "add";
		$user 			 = -1;
		$msg			 = "";
		$form_identifier = $this->check_parameters($parameters, "__frm_identifier__", -1);
		$form_status	 = $this->check_parameters($parameters, "__frm_status__", 0);
		$frm_expires	 = $this->check_date($parameters, "__expires__", "0000-00-00 00:00:00");
		$frm_grace		 = $this->check_date($parameters, "__grace__", "0000-00-00 00:00:00");
		$frm_review		 = $this->check_date($parameters, "__review__", "0000-00-00 00:00:00");
		if($form_identifier==-1){
			return "";
		}
		/*************************************************************************************************************************
        * get groups
        *************************************************************************************************************************/
		$total_number_of_group_lists= $this->check_parameters($parameters,"totalnumberofchecks_group_list",0);
		$mygrouplist = Array();
		for($tnogl = 1; $tnogl <= $total_number_of_group_lists; $tnogl++){
			$ug = $this->check_parameters($parameters,"group_list_$tnogl",array());
			if (count($ug)>0){
				if (count($ug)>1){
					$mygrouplist = split(",",join(",",$mygrouplist) . "," . join(",",$ug));
				} else {
					$mygrouplist = split(",",join(",",$mygrouplist) . "," . $ug[0]);
				}
			}
		}
		/*************************************************************************************************************************
        * get groups
        *************************************************************************************************************************/


		$out ="";
		$sql = "select * from formbuilder_settings 
					inner join memo_information on mi_type ='$this->webContainer' and mi_link_id = fbs_identifier and mi_client=fbs_client and mi_field = 'fba_confirm'
					inner join metadata_details on md_module='$this->webContainer' and md_link_id = fbs_identifier and md_client=fbs_client
				where fbs_identifier = $form_identifier and fbs_client=$this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$module_fields			= Array();
		$id=$form_identifier;
		$msg = "Thank You,<br/><br/>Your entry has been submitted.";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
//        	$id 			= $r["fbs_identifier"];
			$stock_title	= $r["md_title"];
			$fbs_ecommerce 	= $r["fbs_ecommerce"];
			$fbs_price_link	= $r["fbs_price_link"];
			$fbs_fixedprice	= $r["md_price"];
			$msg			= $r["mi_memo"];
        }
        $this->call_command("DB_FREE",Array($result));
		/*************************************************************************************************************************
        * extract mapped information 
        *************************************************************************************************************************/
		$map_results=Array();
		$sql = "select * from formbuilder_merge_map where fbmm_setting = $form_identifier and fbmm_client = $this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$param_results = Array();
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if($r["fbmm_mapping"]!=""){
	        	$map = split("::",$r["fbmm_mapping"]);
				$map_results[count($map_results)] = $map;
				if($map[6]==1){
					$parameters[$map[1]] = $this->check_parameters($parameters,$map[0]);
					if(empty($param_results[$map[9]."::".$map[10]])){
						$param_results[$map[9]."::".$map[10]] = Array();
						$param_results[$map[9]."::".$map[10]]["module_command"] = $map[9];
						$param_results[$map[9]."::".$map[10]]["parameters"] = Array("module_identifier" => $map[10]);
						$param_results[$map[9]."::".$map[10]]["parameters"][$map[1]] = $this->check_parameters($parameters,$map[0]);
						$param_results[$map[9]."::".$map[10]]["map"] = Array();
					}
					$param_results[$map[9]."::".$map[10]]["map"][count($param_results[$map[9]."::".$map[10]]["map"])] = Array("from"=>$map[0],"to"=>$map[1], "attempt"=>0);
				} else {
					if($this->check_parameters($parameters,$map[1])=="" && $this->check_parameters($parameters,$map[0])!=""){
						$parameters[$map[1]] = $this->check_parameters($parameters,$map[0]);
					} else {
						$parameters[$map[0]] = $this->check_parameters($parameters,$map[1]);
					}
					if(empty($param_results[$map[7]."::".$map[8]])){
						$param_results[$map[7]."::".$map[8]] = Array();
						$param_results[$map[7]."::".$map[8]]["module_command"] = $map[7];
						$param_results[$map[7]."::".$map[8]]["parameters"] = Array("module_identifier" => $map[8]);
						$param_results[$map[7]."::".$map[8]]["parameters"][$map[0]] = $this->check_parameters($parameters,$map[1]);
						$param_results[$map[7]."::".$map[8]]["map"] = Array();
					}
					$param_results[$map[7]."::".$map[8]]["map"][count($param_results[$map[7]."::".$map[8]]["map"])] = Array("from"=>$map[1],"to"=>$map[0], "attempt"=>0);
	    		}
			}
        }
        $this->call_command("DB_FREE", Array($result));
		/*************************************************************************************************************************
        * extract the fields from the parameters passed to the function and the updated parameters as defined by mapping
        *************************************************************************************************************************/
		$sql = "select * from formbuilder_field_map where fbfm_setting = $form_identifier and fbfm_client = $this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
	    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$field = $r["fbfm_fieldname"];
        	$key = split("::",$r["fbfm_belongs"]);
			$key[0] = str_replace("ADMIN","",$key[0]);
			if(empty($param_results[$r["fbfm_belongs"]])){
				$param_results[$r["fbfm_belongs"]] = Array();
				$param_results[$r["fbfm_belongs"]]["module_command"] = $key[0];
				$param_results[$r["fbfm_belongs"]]["parameters"] = Array(
					"module_identifier" => $key[1], 
					"__frm_status__"=> $form_status, 
					"just_create" 	=> 1, 
					"fbs_identifier"=> $id, 
					"__expires__"	=> $frm_expires,
					"__grace__"		=> $frm_grace,
					"__review__"	=> $frm_review
					);
			}
			/*************************************************************************************************************************
            * if the user is logged in then reset the user details if not set to the users login session id
            *************************************************************************************************************************/
			if (($field=="contact_user") || ($field=="user_identifier")){
				$sval = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
				$fval = $this->check_parameters($parameters,$field);
				if($this->check_parameters($parameters,$field)==""){
					if($fval==""){
//						$parameters[$field] = $sval;
					} else {
						$parameters[$field] = $fval;
					}
				}
			}
			$param_results[$r["fbfm_belongs"]]["parameters"]["$field"] = $this->check_parameters($parameters,$field);
        }
        $this->call_command("DB_FREE",Array($result));
		/*************************************************************************************************************************
        * if user form exists then call USERS_SAVE_FBA  which will return the user id for new / existing
        *************************************************************************************************************************/
		$data = Array("user_identifier"=>0,"errorCount"=>0);
		if ("__NOT_FOUND__" != $this->check_parameters($param_results,"USERS_::-1","__NOT_FOUND__")){
			$param_results["USERS_::-1"]["parameters"]["fbs_identifier"] = $id;
			$param_results["USERS_::-1"]["parameters"]["user_status"]	 = $this->check_parameters($parameters, "__frm_user_status__", 2);
			$param_results["USERS_::-1"]["parameters"]["just_create"]	 = 1;
			$param_results["USERS_::-1"]["parameters"]["__expires__"]	 = $frm_expires;
			$param_results["USERS_::-1"]["parameters"]["__grace__"]		 = $frm_grace;
			$param_results["USERS_::-1"]["parameters"]["__review__"]	 = $frm_review;
			$param_results["USERS_::-1"]["parameters"]["group_list"]	 = $mygrouplist;
			$data  = $this->call_command($param_results["USERS_::-1"]["module_command"]."SAVE_FBA",$param_results["USERS_::-1"]["parameters"]);
			foreach ($param_results as $key => $module){
				$param_results[$key]["parameters"]["user_identifier"] = $data["user_identifier"];
			}
		}
		/*************************************************************************************************************************
        * merge data
        *************************************************************************************************************************/
		foreach($param_results as $key => $value){
			if("__NOT_FOUND__" != $this->check_parameters($param_results[$key], "map", "__NOT_FOUND__")){
				$m=count($param_results[$key]["map"]);
					for($i = 0; $i < $m; $i++ ){
						$param_results[$key]["parameters"][$param_results[$key]["map"][$i]["to"]] = $this->check_parameters($parameters, $param_results[$key]["map"][$i]["to"]);
					}
			}
		}
		/*************************************************************************************************************************
        * check required fields
        *************************************************************************************************************************/
		$sql = "select * from formbuilder_field_map where fbfm_client = $this->client_identifier and fbfm_setting=$id order by fbfm_rank";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$total_pages	= 1;
		$errorCount		= 0;
		$error 			= Array();
		$seperator_page = 0;
		$page 		= $this->check_parameters($parameters,"page",1); 
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$pos = count($module_fields);
           	$module_fields[$pos] = Array($r["fbfm_fieldname"], $r["fbfm_label"], $r["fbfm_type"], $r["fbfm_map"], $r["fbfm_auto"], $r["fbfm_belongs"], $r["fbfm_labelpos"], "value"=>$this->check_locale_starter($this->check_parameters($parameters,$r["fbfm_fieldname"])), "required"=>$r["fbfm_required"]);
			if($r["fbfm_type"]=="pagesplitter"){
				$seperator_page=1;
				$total_pages++;
			}
			if($module_fields[$pos]["required"]=="yes" && ($page-1)==$total_pages && $page!=1){
				if($user==-1 || ($user!=-1 && $module_fields[$pos][5]!="USERS_::-1")){
					if($module_fields[$pos]["value"]==""){
						$errorCount++;
						$error[count($error)] = Array($module_fields[$pos][0], "<li>You did not fill in this field (".$module_fields[$pos][1].")</li>");
					}
					if($module_fields[$pos][2]=="password"){
						if($module_fields[$pos]["value"]!=$this->check_locale_starter($this->check_parameters($parameters,$r["fbfm_fieldname"]."_confirm"))){
							$errorCount++;
							$error[count($error)] = Array($module_fields[$pos][0], "<li>Your passwords do not match</li>");
						}
						if(strlen($module_fields[$pos]["value"])<6){
							$errorCount++;
							$error[count($error)] = Array($module_fields[$pos][0], "<li>Your passwords must be longer than 6 characters</li>");
						}
					}
				}
			}
        }
        $this->call_command("DB_FREE",Array($result));
		if($this->check_parameters($data,"errorCount",0)==0 && $errorCount==0){
			/*************************************************************************************************************************
    	    * call the save function for each module in turn
        	*************************************************************************************************************************/
			foreach ($param_results as $key => $module){
				if($module["module_command"]!="undefined" && $key !="USERS_::-1"){
					$module["parameters"]["ie_status"]	 	= $this->check_parameters($parameters, "__frm_ie_status__", 2);
					$module["parameters"]["just_create"]	= 1;
					$module["parameters"]["__expires__"]	= $frm_expires;
					$module["parameters"]["__grace__"]		= $frm_grace;
					$module["parameters"]["__review__"]	 	= $frm_review;
					// INFORMATIONADMIN_SAVE_FBA
					$module["module_command"]= str_replace("ADMIN","",$module["module_command"]);
					$param_results[$key]["uid"] = $this->call_command($module["module_command"]."SAVE_FBA",$module["parameters"]);
					
				}
			}
			$next_cmd = $this->check_parameters($parameters,"next_command");
			$next_id = $this->check_parameters($parameters,"next_id");
			if($next_cmd==""){
				$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=USERS_LIST"));
			} else {
				$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=".$next_cmd."&amp;identifier=".$next_id));
			}
			$this->exitprogram();
		} else {
		}
		return $out;
	}

	/*************************************************************************************************************************
    * get database and list identifier to delete related record		(Added By: Muhammad Imran Mirza)
    *************************************************************************************************************************/
	function module_execute_delete($parameters){
//	print_r($parameters);
//	die();

		$identifier = $this->check_parameters($parameters, "user",$this->check_parameters($parameters, "identifier", -1));
		$list_id = $this->check_parameters($parameters, "list_id",$this->check_parameters($parameters, "list_id", -1));
		$user = $this->check_parameters($parameters, "user_id",$this->check_parameters($parameters, "user_id", -1));

		if ($identifier == -1){
			return "";
		}
		
		$from_member = 0;
		if ($list_id == -1){
			/*if coming from User List*/
			$fbs_identifier = $this->check_parameters($parameters, "module_identifier", -1);
			$user 	= $this->check_parameters($parameters, "user",$this->check_parameters($parameters, "identifier", -1));
	
			$sql = "select fbfm_belongs from formbuilder_field_map where fbfm_client = $this->client_identifier and fbfm_setting=$fbs_identifier and fbfm_fieldname = 'ie_identifier' order by fbfm_rank";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$r = $this->call_command("DB_FETCH_ARRAY",Array($result));
			$data = split("::",$r["fbfm_belongs"]);
			$list_id = $data[1];
	
			$sql = "select ie_identifier from information_entry 
						where ie_version_wip=1 and ie_client=$this->client_identifier and ie_user = ".$user." and ie_list = $list_id";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$r = $this->call_command("DB_FETCH_ARRAY",Array($result));
			$ie_identifier = $r["ie_identifier"];
		}else{
			/*if coming from Member List*/
			$ie_identifier = $identifier;
			$from_member = 1;
		}
		
//	die();
		if ($ie_identifier != -1 && $list_id != -1){
			$this->call_command("USERS_REMOVE_CONFIRM_MEMBER", Array("identifier"=>$user));
			$this->call_command("INFORMATIONADMIN_REMOVE", Array("identifier"=>$ie_identifier, "list_id"=>$list_id, "redirect_status"=>"1"));
		}

		if ($from_member == 1){
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=INFORMATIONADMIN_LIST_ENTRIES&amp;identifier=$list_id&amp;recache=1"));
		}else{
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=USERS_LIST"));
		}

//		$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."REMOVE&amp;list_id=".$list_id,REMOVE_EXISTING);
		
		
	}
	/*************************************************************************************************************************
    * get the date modifiers for renewals
    *************************************************************************************************************************/
	function get_dates($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$array_info= Array("expires"=>0, "renewal"=>0, "grace"=>0);
		$sql = "SELECT * FROM formbuilder_settings where fbs_identifier = $identifier and fbs_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$array_info["expires"]	= $r["fbs_life"];
        	$array_info["renewal"]	= $r["fbs_review"];
        	$array_info["grace"]	= $r["fbs_grace"];
        }
        $this->parent->db_pointer->database_free_result($result);
		return $array_info;
	}
}
?>