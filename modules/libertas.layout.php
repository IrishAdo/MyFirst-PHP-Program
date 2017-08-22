<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.layout.php
* @date 09 Oct 2002
*/
/**
* This module is for managing the site structure layout.
*
* This module will produce a n-D Array of the structure of the site layout.
* It will store the root directory for the client as each client will be considered
* to be on a virtual host.
*/

class layout extends module {
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__ADMIN__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name_label			= "Site Structure Management Module (Administration)";
	var $module_name				= "layout";
	var $module_admin				= "1";
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_debug				= false;
	var $module_modify	 			= '$Date: 2005/02/26 20:22:47 $';
	var $module_version 			= '$Revision: 1.46 $';
	var $module_creation			= "26/10/2002";
	var $module_command				= "LAYOUT_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "LAYOUT_";
	var $menu_structure 			= array();
	var $directories 				= array();
	var $parentIDlist 				= "";
	var $displayed 					= "";
	var $debug_count				= 0;
	var $max_depth					= 0;
	var $module_label				= "MANAGEMENT_LAYOUT";
	var $page_new_menu_access		= 0;
	var $menu_group_access			= 0;
	var $menu_channel_access		= 0;
	var $menu_advanced_access		= 0;
	var $module_admin_options		= array();
	var $module_admin_user_access	= array(
		array("LAYOUT_ALL"						, "COMPLETE_ACCESS"),
		array("LAYOUT_CAN_MANAGE_MENU"			, "MANAGE_MENU"),
		array("LAYOUT_AUTHOR_CAN_MANAGE_MENU"	, "MANAGE_MENU_AUTHOR"),
//		array("LAYOUT_CAN_MANAGE_DIRECTORY"		, "MANAGE_DIRECTORY"),
		array("LAYOUT_CAN_MANAGE_GROUP"			, "MANAGE_GROUP_ACCESS"),
		array("LAYOUT_CAN_MANAGE_CHANNEL"		, "MANAGE_CHANNEL"),
		array("LAYOUT_CAN_MANAGE_ADVANCED" 		, "MANAGE_ADVANCED")
	);
	var $module_display_options = array();
	
	var $admin_access				= 0;
	var $menu_access				= 0;
	var $directory_access			= 1;
	var $module_type_admin_access	= 0;
	
	var $title_pages				= Array();
	var $keylist					= Array();
	/**
	* command()
	- want to do anything with this module go through me simply create a condition for
	- the user command that you want to execute and hey presto I'll return the output of
	- that module
	*/
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
//		print "[$user_command][$this->menu_access][$this->module_type_admin_access]";
//		$this->exitprogram();
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*/
		if (strpos($user_command,$this->module_command)===0){
			if ($user_command == $this->module_command."DEBUG_ON"){
				$this->module_debug=true;
			}
			if ($user_command == $this->module_command."DEBUG_OFF"){
				$this->module_debug=false;
			}
			if ($user_command == $this->module_command."GET_MODULE"){
				return $this->get_module_name();
			}
			if ($user_command == $this->module_command."GET_VERSION"){
				return $this->get_module_version();
			}
			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
				return $this->webContainer;
			}
			if ($user_command == $this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command == $this->module_command."ROBOTS"){
				$this->generate_robots_txt_file();
			}
			if ($user_command == $this->module_command."DISPLAY_IDS"){
				return $this->display_id($this->check_parameters($parameter_list,0,-1));
			}
			if ($user_command == $this->module_command."ANONYMOUS_DISPLAY_IDS"){
				return $this->display_id($this->check_parameters($parameter_list,0,-1),1);
			}
			if ($user_command == $this->module_command."DISPLAY_CHILD_IDS"){
				return $this->display_child_id($parameter_list);
			}
			if ($user_command==$this->module_command."GET_PAGE"){
				return $this->call_command("LAYOUTSITE_GET_PAGE",$parameter_list);
			}
			if ($user_command==$this->module_command."GET_DIRECTORY_PATH"){
				return $this->retrieve_directory_path($parameter_list[0]);
			}
			if ($user_command==$this->module_command."GET_DIRECTORY_PATH_FROM_MENU"){
				return $this->retrieve_directory_path_from_menu($parameter_list[0]);
			}
			if ($user_command==$this->module_command."GET_THEME_ID"){
				return $this->layout_retrieve_theme();
			}
			if ($user_command==$this->module_command."MENU_HAS_ACCESS"){
				return $this->have_access();
			}
			if ($user_command==$this->module_command."MENU_LOCATION_SETTINGS_GET"){
				return $this->get_menu_location_settings($parameter_list);
			}
			if ($user_command==$this->module_command."MENU_LOCATION_SETTINGS_SAVE"){
				return $this->save_menu_location_settings($parameter_list);
			}
			if ($user_command==$this->module_command."WEB_MENU"){
				return $this->web_generate_menu();
			}
			if ($user_command==$this->module_command."GET_LOCATION_ID"){
				return $this->layout_retrieve_location_id($parameter_list);
			}
			if ($user_command==$this->module_command."GET_LOCATION_URL"){
				return $this->layout_retrieve_location_url($parameter_list);
			}
			if ($user_command == $this->module_command."MENU_TO_OBJECT_EXTRACT"){
				return $this->menu_to_object_extract($parameter_list);
			}
			if ($user_command==$this->module_command."LIST_MENU_OPTIONS"){
				$val = $this->check_parameters($parameter_list,0,-1);
				return $this->list_all_menu_locations_indent_value($val,-1,0,$parameter_list);
			}
			
			/**
			* Semi Secured functions Must be logged in to administrative section of site do not
			* need to have role access to call functions (typically these functions are called
			* from inside another module)
			*/
			if ($this->module_type_admin_access){
				/**
				* Menu Management
				* Not all commands are available on the web site side, the following commands are
				* only available to the administrators of the site that have access to the appropraite
				* functionality.
				*/ 
				if ($this->page_new_menu_access){
					if ($user_command == $this->module_command."MENU_INSERT"){
						return $this->save($parameter_list);
					}
				}
				if ($user_command==$this->module_command."RETRIEVE_LIST_MENU_OPTIONS"){
					$val = $this->check_parameters($parameter_list,0,-1);
					return $this->retrieve_list_all_menu_locations_indent_value($val,-1,0,$parameter_list);
				}
				
				if ($user_command==$this->module_command."RETRIEVE_LIST_MENU_OPTIONS_DETAIL"){
					return $this->retrieve_list_all_menu_locations_indent_value_detail($parameter_list);
				}
				if ($user_command==$this->module_command."DISPLAY_DIRECTORY_OPTIONS"){
					return $this->display_directories_options($parameter_list[0],$parameter_list[2],$parameter_list[1]);
				}
				if ($user_command==$this->module_command."DISPLAY_UPLOAD_DIRECTORY"){
					return $this->display_upload_directories($parameter_list[0],$parameter_list[1]);
				}
				if ($user_command==$this->module_command."GET_BREAD_CRUMB_TRAIL"){
					return $this->retrieve_bc_trail($parameter_list);
				}
				if ($user_command==$this->module_command."GET_DIRECTORIES"){
					return $this->get_directories();
				}
				if ($user_command==$this->module_command."GET_MENUS"){
					return $this->load_menu();
				}
				if ($user_command==$this->module_command."GET_MENU_WITH_COMMAND"){
					return $this->find_menu_with_command($parameter_list);
				}
				if ($user_command==$this->module_command."ACCESS_OPTIONS"){
					return $this->module_admin_options(0);
				}
				if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
					return $this->module_admin_access_options(0);
				}
				if ($user_command==$this->module_command."DISPLAY_DIRECTORY"){
					return $this->display_directories(-1);
				}
				if ($user_command==$this->module_command."SET_GLOBAL_COMMAND"){
					return $this->set_global_command_status($parameter_list);
				}
				// why 2 spellings? A. most common misspelling 
				if (($user_command == $this->module_command."LIST_MENU_OPTIONS_HIERARCHY") || ($user_command == $this->module_command."LIST_MENU_OPTIONS_HEIRARCHY")){
					return $this->generate_hierarchy($parameter_list);
				}
				if ($user_command == $this->module_command."MENU_TO_OBJECT_EXTRACTOR_UPDATE"){
					return $this->menu_to_object_extract_update($parameter_list);
				}
				if ($user_command == $this->module_command."MENU_TO_OBJECT_UPDATE"){
					return $this->menu_to_object_update($parameter_list);
				}
				if ($user_command == $this->module_command."MENU_TO_OBJECT_LIST"){
					return $this->menu_to_object_list($parameter_list);
				}
				if ($user_command == $this->module_command."MENU_TO_OBJECT_REMOVE"){
					return $this->menu_to_object_remove($parameter_list);
				}
				if ($user_command == $this->module_command."MENU_TO_OBJECT_INHERIT"){
					return $this->menu_to_object_inherit($parameter_list);
				}
				/*************************************************************************************************************************
                *									Menu List, Add, Edit, remove, save, etc
                *************************************************************************************************************************/
				if ($this->menu_access){
					if ($user_command == $this->module_command."LIST_MENU"){
						return $this->list_menus($parameter_list);
					}
					if (($user_command == $this->module_command."EDIT_MENU_REDO") ||($user_command == $this->module_command."ADD_MENU_REDO") ||($user_command == $this->module_command."EDIT_MENU") || ($user_command == $this->module_command."ADD_MENU")){
						return $this->menu_modify_form($parameter_list);
					}
					if ($user_command == $this->module_command."REMOVE_TREE"){
						$old_path = $this->check_parameters($parameter_list,"path");
						if ($old_path!=""){
							$this->terminate_directory($old_path);
							$root  = $this->check_parameters($this->parent->site_directories,"ROOT");
							$um =umask(0);
							@chmod($root."/".$old_path,LS__DIR_PERMISSION);
							umask($um);
							@rmdir($root."/".$old_path); //remove this directory
							$this->cache_menu_structure();
							if ($this->check_parameters($_SESSION,"redirect_on_recache")==""){
								$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=LAYOUT_LIST_MENU"));
							}
						}
					}
					if ($user_command == $this->module_command."SAVE_MENU"){
						$id = $this->save($parameter_list);
						$this->call_command("ACCESSKEYADMIN_CACHE");
						//$this->restore_directories();
						//$this->restore();
						//$this->call_command("INFORMATIONADMIN_RESTORE",Array("command=LAYOUT_LIST_MENU&amp;folder=".$parameter_list["menu_parent"]));
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=LAYOUT_LIST_MENU&amp;folder=".$parameter_list["menu_parent"]));
					}
					if ($user_command == $this->module_command."MENU_REMOVED_CONFIRMED"){
						$this->remove_confirm($parameter_list);
					}
					if ($user_command == $this->module_command."PAGE_REMOVAL_COMPLETE"){
						$this->page_remove_confirm($parameter_list);
					}
					if ($user_command == $this->module_command."REMOVE_MENU"){
						$out =  $this->remove($parameter_list);
						if ($out==""){
							$this->cache_menu_structure();
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=LAYOUT_LIST_MENU"));
						} else {
							return $out;
						}
					}
					if ($user_command == $this->module_command."CHANGE_ORDER"){
						$this->changeorder(
							$this->check_parameters($parameter_list,"menu_identifier",-2),
							$this->check_parameters($parameter_list,"menu_parent",-2),
							$this->check_parameters($parameter_list,"menu_pos",-2)
						);
						
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=LAYOUT_LIST_MENU&amp;folder=".$this->check_parameters($parameter_list,"menu_parent",-1)));
					}
					if ($user_command == $this->module_command."SAVE_CHANNELS"){
						$this->save_channels($parameter_list);
						$this->cache_menu_structure();
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=LAYOUT_LIST_MENU&amp;folder=".$this->check_parameters($parameter_list,"menu_parent",-1)));
					}
					if ($user_command == $this->module_command."SAVE_GROUPS"){
						$this->save_groups($parameter_list);
						$this->cache_menu_structure();
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=LAYOUT_LIST_MENU&amp;folder=".$this->check_parameters($parameter_list,"menu_parent",-1)));
					}
				}
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				-	Ranking
				*/ 
				if ($user_command==$this->module_command."SET_PAGE_RANKING"){
					$this->rank_page($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=LAYOUT_LIST_MENU&folder=".$this->check_parameters($parameter_list,"folder")));
				}
				if ($user_command==$this->module_command."HIDE_SET_PAGE_RANKING"){
					$this->hide_rank_page($parameter_list);
					if ($this->check_parameters($parameter_list,"NO_REDIRECT",0)==0){
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=LAYOUT_PAGE_RANKING"));
					}
				}
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				-	System Restore / Cache
				*/ 
				if ($user_command==$this->module_command."RESTORE"){
					$this->restore_directories();
					return $this->restore();
				}
				if ($user_command==$this->module_command."RESTORE_DIRECTORIES"){
					$this->restore_directories();
				}
				if ($user_command==$this->module_command."CACHE_MENU_STRUCTURE"){
					$this->cache_menu_structure();
				}
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			-	Directory Management Restricted commands
			*/
			if ($this->directory_access){
				if ($user_command==$this->module_command."SAVE_DIRECTORY"){
					$this->save_directory($parameter_list);
					$this->cache_menu_structure();
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=LAYOUT_LIST_DIRECTORY"));
				}
				if (($user_command==$this->module_command."REMOVE_DIRECTORY")){
					$this->remove_directory($parameter_list["directory_identifier"],$parameter_list["directory_path"]);
					$this->cache_menu_structure();
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=LAYOUT_LIST_DIRECTORY"));
				}
				if (($user_command==$this->module_command."LIST_DIRECTORY")||($user_command==$this->module_command."DIRECTORY_ADD")){
					return $this->generate_directory();
				}
				if ($user_command==$this->module_command."UPDATE_DIRECTORIES"){
					$this->update_all_menu_uris($parameter_list);
				}
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			-	Functions Secured to the administration users with one or more roles
			*/
			if ($user_command==$this->module_command."CREATE_NEW_CLIENT_DETAILS"){
				return $this->create_client_details($parameter_list);
			}
	}else{
		// wrong command sent to system
		return "";
	}
}

function initialise(){
	$this->client_identifier = $this->parent->client_identifier;
	$this->keylist = Array();
	$this->load_locale("layout_admin");
	if ($this->client_identifier == -1){
		$this->client_identifier=$this->check_parameters($_SESSION,"client_identifier",-1);
	}
	$this->menu_access			= 0;
	$this->menu_group_access	= 0;
	$this->menu_channel_access	= 0;
	$this->menu_advanced_access	= 0;
	$this->page_new_menu_access = 0;
	$this->directory_access		= 1;
	if ($this->parent->server[LICENCE_TYPE]==ECMS){

		$this->module_admin_options	= array(
//			array("PAGE_LIST", "MANAGE_PAGE","PAGE_AUTHOR|PAGE_APPROVER|PAGE_PUBLISHER|PAGE_ARCHIVER|PAGE_DISCUSSION|PAGE_FORCE_UNLOCK"),
//			array("PAGE_MANAGE_IGNORE_LIST", "MANAGE_PAGE_IGNORE_LIST","PAGE_AUTHOR|PAGE_APPROVER|PAGE_PUBLISHER|PAGE_ARCHIVER|PAGE_DISCUSSION|PAGE_FORCE_UNLOCK")
		array("LAYOUT_LIST_MENU",MANAGEMENT_MENU,"LAYOUT_AUTHOR_CAN_MANAGE_MENU|LAYOUT_CAN_MANAGE_MENU|LAYOUT_ALL")/*,
		array("LAYOUT_LIST_DIRECTORY",MANAGEMENT_DIRECTORY,"LAYOUT_CAN_MANAGE_DIRECTORY")*/
		);
	} else {
		$this->module_admin_options	= array(
		array("LAYOUT_LIST_MENU",MANAGEMENT_MENU,"LAYOUT_AUTHOR_CAN_MANAGE_MENU|LAYOUT_CAN_MANAGE_MENU|LAYOUT_ALL")
		);
	}
	$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
	$max_grps = count($grp_info);
	$access_array = array();
	if ($this->parent->script=="admin/install.php"){
		$this->menu_access=1;
	}
	for($i=0;$i < $max_grps; $i++){
		$access_array = $grp_info[$i]["ACCESS"];
		$access_length = count($access_array);
		for ($index=0,$access_length=count($access_array);$index<$access_length;$index++){
			if (("ALL" == $access_array[$index]) || ("LAYOUT_ALL" == $access_array[$index]) || ("LAYOUT_LIST_MENU" == $access_array[$index]) || ("LAYOUT_CAN_MANAGE_MENU" == $access_array[$index])){
				$this->menu_access				= 1;
			}
			if (("ALL" == $access_array[$index]) || ("LAYOUT_ALL" == $access_array[$index]) || ("LAYOUT_AUTHOR_CAN_MANAGE_MENU" == $access_array[$index])){
				$this->menu_access=1;
				$this->page_new_menu_access		= 1;
			}
			if (("ALL" == $access_array[$index]) || ("LAYOUT_ALL" == $access_array[$index]) || ("LAYOUT_CAN_MANAGE_GROUP" == $access_array[$index])){
				$this->menu_group_access		= 1;
			}
			if (("ALL" == $access_array[$index]) || ("LAYOUT_ALL" == $access_array[$index]) || ("LAYOUT_CAN_MANAGE_CHANNEL" == $access_array[$index])){
				$this->menu_channel_access		= 1;
			}
			if (("ALL" == $access_array[$index]) || ("LAYOUT_ALL" == $access_array[$index]) || ("LAYOUT_CAN_MANAGE_ADVANCED" == $access_array[$index])){
				$this->menu_advanced_access		= 1;
			}
			

			if (
			(($this->parent->server[LICENCE_TYPE]==ECMS) && (("ALL"==$access_array[$index]) || ("LAYOUT_ALL"==$access_array[$index]))) ||
			("LAYOUT_LIST_DIRECTORY"==$access_array[$index])
			){
				$this->directory_access=1;
			}
		}
	}

	/*
	check that the user is accessing functions from the proper mode.
	*/
	if ((($this->parent->module_type=="admin")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")||($this->parent->module_type=="install"))){
		$this->module_type_admin_access=1;
	}
	if (($this->directory_access || $this->menu_access || $this->page_new_menu_access)&& (($this->parent->module_type=="admin")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")||($this->parent->module_type=="install"))){
		$this->module_admin_access=1;
	}

		return 1;
	}

	function create_client_details($parameters){
		$client_identifier = $this->check_parameters($parameters,"client_identifier",-1);
		/**
		* Table data for menu_data, directory_data and display_data tables 
		* insert new directories, extract directory identifiers, insert menus extract menu 
		* identifiers then insert display data for menus.
		*/
		$sql="insert into directory_data (directory_client, directory_parent, directory_name, directory_can_upload, directory_can_spider) values ($client_identifier,-1,'home',0,0);";
		$this->parent->db_pointer->database_query($sql);
		$sql="insert into directory_data (directory_client, directory_parent, directory_name, directory_can_upload, directory_can_spider) values ($client_identifier,-1,'uploads',1,0);";
		$this->parent->db_pointer->database_query($sql);
		$sql="insert into directory_data (directory_client, directory_parent, directory_name, directory_can_upload, directory_can_spider) values ($client_identifier,-1,'search',0,1);";
		$this->parent->db_pointer->database_query($sql);
//		$sql="insert into directory_data (directory_client, directory_parent, directory_name, directory_can_upload, directory_can_spider) values ($client_identifier,-1,'contact-us',0,1);";
//		$this->parent->db_pointer->database_query($sql);
		$sql="insert into directory_data (directory_client, directory_parent, directory_name, directory_can_upload, directory_can_spider) values ($client_identifier,-1,'site-map',0,1);";
		$this->parent->db_pointer->database_query($sql);

		$sql = "Select * from directory_data where directory_client = $client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$site_id=-1;
		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			if ($r["directory_name"]=="search"){
				$search_id = $r["directory_identifier"];
			}
//			if ($r["directory_name"]=="contact-us"){
//				$contact_id = $r["directory_identifier"];
//			}
			if ($r["directory_name"]=="site-map"){
				$site_id = $r["directory_identifier"];
			}
		}
		$sql="insert into menu_data (menu_client, menu_label, menu_url, menu_stylesheet, menu_order, menu_directory) values ($client_identifier, 'Home', 'index.php', 1, 1, 0);";
		$this->parent->db_pointer->database_query($sql);
		$order=2;
		if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
//			$sql="insert into menu_data (menu_client, menu_label, menu_url, menu_stylesheet, menu_order, menu_directory) values ($client_identifier, 'Administration', 'admin/index.php',2 ,2 ,0);";
//			$this->parent->db_pointer->database_query($sql);
//			$order++;
		}
		$sql="insert into menu_data (menu_client, menu_label, menu_url, menu_stylesheet, menu_order, menu_directory, menu_hidden) values ($client_identifier, 'Search', 'search/index.php', 2, $order, $search_id,0);";
		$this->parent->db_pointer->database_query($sql);
		$order++;
		$sql="insert into menu_data (menu_client, menu_label, menu_url, menu_stylesheet, menu_order, menu_directory, menu_hidden) values ($client_identifier, 'Site Map', 'site-map/index.php', 14, $order, $site_id,0);";
		$this->parent->db_pointer->database_query($sql);

		$sql = "Select * from menu_data where menu_client = $client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			if ($r["menu_url"]=="index.php"){
				$home_id = $r["menu_identifier"];
			}
			if ($r["menu_url"]=="search/index.php"){
				$search_id = $r["menu_identifier"];
			}
//			if ($r["menu_url"]=="contact_us/index.php"){
//				$contact_id = $r["menu_identifier"];
//			}
			if ($r["menu_url"]=="site_map/index.php"){
				$site_id = $r["menu_identifier"];
			}
			/*if ($r["menu_url"]=="admin/index.php"){
				$admin_id = $r["menu_identifier"];
			}*/
		}
		$sql = "insert into display_data (display_client, display_menu, display_command) values ($client_identifier,$home_id,'PRESENTATION_DISPLAY');";
		$this->parent->db_pointer->database_query($sql);
		$sql = "insert into display_data (display_client, display_menu, display_command) values ($client_identifier,$search_id,'PRESENTATION_DISPLAY');";
		$this->parent->db_pointer->database_query($sql);
		$sql = "insert into display_data (display_client, display_menu, display_command) values ($client_identifier,$search_id,'PRESENTATION_SEARCH');";
		$this->parent->db_pointer->database_query($sql);
//		$sql = "insert into display_data (display_client, display_menu, display_command) values ($client_identifier,$contact_id,'PRESENTATION_DISPLAY');";
//		$this->parent->db_pointer->database_query($sql);
//		$sql = "insert into display_data (display_client, display_menu, display_command) values ($client_identifier,$contact_id,'SFORM_DISPLAY_CONTACT_US');";
//		$this->parent->db_pointer->database_query($sql);
		$sql = "insert into display_data (display_client, display_menu, display_command) values ($client_identifier,$site_id,'SITEMAP_DISPLAY');";
		$this->parent->db_pointer->database_query($sql);
		$sql = "insert into display_data (display_client, display_menu, display_command) values ($client_identifier,$site_id,'PRESENTATION_DISPLAY');";
		$this->parent->db_pointer->database_query($sql);
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
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* menu data table
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$fields = array(
			array("menu_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"	,"key"),
			array("menu_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("menu_parent"				,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("menu_label"				,"varchar(255)"				,""			,"default NULL"),
			array("menu_alt_text"			,"varchar(255)"				,""			,"default NULL"),
			array("menu_url"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("menu_order"				,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("menu_ssl"				,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("menu_stylesheet"			,"unsigned integer"			,"NOT NULL"	,"default '2'"	,"key"),
			array("menu_sort"				,"unsigned integer"			,"NOT NULL"	,"default '1'"	,"key"),
			array("menu_theme"				,"unsigned integer"			,""			,"default '0'"	,"key"),
			array("menu_directory"			,"unsigned integer"			,""			,"default '0'"	,"key"),
			array("menu_hidden"				,"unsigned integer"			,""			,"default '0'"),
			array("menu_image_display"		,"unsigned small integer"	,""			,"default '0'"), // 0 left, 1 right, 2 alternatve 
			array("menu_headline_all"		,"unsigned small integer"	,""			,"default '0'"),
			array("menu_headline_content"	,"unsigned small integer"	,""			,"default '0'"),
			array("menu_headline"			,"unsigned small integer"	,""			,"default '0'"),
			array("menu_images"				,"unsigned small integer"	,""			,"default '1'"),
			array("menu_image_inherit"		,"unsigned small integer"	,""			,"default '1'"),
			array("menu_headline_label"		,"unsigned small integer"	,""			,"default '1'"),
			array("menu_headline_counter"	,"unsigned small integer"	,""			,"default '3'"),
			array("menu_icon"				,"unsigned small integer"	,""			,"default '0'"),
			array("menu_headline_title_pages","unsigned small integer"	,""			,"default '0'"),
			array("menu_external"			,"unsigned small integer"	,""			,"default '0'"),
			array("menu_archiving"			,"unsigned small integer"	,""			,"default '0'"),
			array("menu_archive_on"			,"unsigned small integer"	,""			,"default '0'"),
			array("menu_archive_display"	,"unsigned small integer"	,""			,"default '0'"),
			array("menu_archive_access"		,"unsigned small integer"	,""			,"default '0'"),
			array("menu_archive_label"		,"varchar(255)"				,""			,"default ''"),
			array("menu_archive_page_label"	,"varchar(255)"				,""			,"default ''"),
			array("menu_plus"				,"unsigned small integer"	,""			,"default '0'")
		);
		$primary="menu_identifier";
		$tables[count($tables)] = array("menu_data", $fields, $primary);
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* menu_global_commands
		-
		* This table holds commands that users have defined that are to be global ie they 
		* have specified that the mirror is to appear on all menu locations, or the image 
		* rotator.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$fields = array(
			array("mgc_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"	,"key"),
			array("mgc_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("mgc_command"		,"varchar(255)"				,""			,"default NULL")
		);
		$primary="mgc_identifier";
		$tables[count($tables)] = array("menu_global_commands", $fields, $primary);
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* directory table data
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$fields = array(
			array("directory_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"	,"key"),
			array("directory_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("directory_parent"		,"integer"					,"NOT NULL"	,"default '-1'"	,"key"),
			array("directory_name"			,"varchar(255)"				,""			,"default NULL"),
			array("directory_can_upload"	,"unsigned small integer"	,""			,"default '1'"),
			array("directory_can_spider"	,"unsigned small integer"	,""			,"default '1'")
		);
		$primary="directory_identifier";
		$tables[count($tables)] = array("directory_data", $fields, $primary);
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* menu_sort table data
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$fields = array(
			array("menu_sort_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"	,"key"),
			array("menu_sort_label"			,"varchar(255)"				,""			,"default 'null'"),
			array("menu_sort_tag_value"		,"varchar(255)"				,""			,"default NULL")
		);
		$primary="menu_sort_identifier";
		$sql = Array(
			"insert into menu_sort (menu_sort_label,menu_sort_tag_value) values ('Sort by Page Rank','menu_access_to_page.page_rank');",
			"insert into menu_sort (menu_sort_label,menu_sort_tag_value) values ('Sort by Date Newest First','page_trans_data.trans_date_available desc');",
			"insert into menu_sort (menu_sort_label,menu_sort_tag_value) values ('Sort by Date Oldest First','page_trans_data.trans_date_available asc');",
			"insert into menu_sort (menu_sort_label,menu_sort_tag_value) values ('Sort in Alphabetic order A to Z','page_trans_data.trans_title asc');",
			"insert into menu_sort (menu_sort_label,menu_sort_tag_value) values ('Sort in Alphabetic order Z to A','page_trans_data.trans_title desc');"
			);
		
		$tables[count($tables)] = array("menu_sort", $fields, $primary,$sql);
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* directory table data
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$fields = array(
			array("display_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"	,"key"),
			array("display_menu"		,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("display_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("display_command"		,"varchar(255)"				,""			,"default ''")
		);
		$primary ="display_identifier";
		$tables[count($tables)] = array("display_data",$fields,$primary);
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* directory table data
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		
		$fields = array(
			array("menu_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"	,"key"),
			array("trans_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"	,"key"),
			array("client_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"	,"key"),
			array("page_rank"			,"integer"			,"NOT NULL"	,"default '0'"),
			array("title_page"			,"small integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="";
		$tables[count($tables)] = array("menu_access_to_page", $fields, $primary);

		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* directory table data
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		
		$fields = array(
			array("mci_menu"		,"unsigned integer"	,"NOT NULL"	,"default '0'"	,"key"),
			array("mci_command"		,"varchar(255)"		,"NOT NULL"	,"default ''"	,""),
			array("mci_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"	,"key")
		);
		$primary ="";
		$tables[count($tables)] = array("menu_channel_inheritance", $fields, $primary);
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* menu_to_object relationship table data
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		
		$fields = array(
			array("mto_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"	,"key"),
			array("mto_menu"		,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("mto_object"		,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("mto_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("mto_module"		,"varchar(255)"				,"NOT NULL"	,"default ''"	,""),
			array("mto_publish"		,"unsigned small integer"	,"NOT NULL"	,"default '1'"	,""),
			array("mto_extract_num"	,"unsigned integer"			,"NOT NULL"	,"default '0'","")
		);
		$primary ="mto_identifier";
		$tables[count($tables)] = array("menu_to_object", $fields, $primary);

		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$fields = array(
			array("mls_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"	,"key"),
			array("mls_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"		,"key"),
			array("mls_module"			,"varchar(255)"				,"NOT NULL"	,"default ''"		,""),
			array("mls_link_id"			,"unsigned integer"			,"NOT NULL"	,"default '0'"		,""),
			array("mls_all_locations"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"		,""),
			array("mls_set_inheritance"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"		,"")
		);
		$primary ="mls_identifier";
		$tables[count($tables)] = array("menu_location_settings", $fields, $primary);
		return $tables;
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Generate the list of menu locations for the administrators to use.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function list_menus($parameters){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-	
		* retrieve the folder that we are currently in
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$folder = $this->check_parameters($parameters,"folder",-1);

		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-	
		* load the menu from the cached file restricted instead
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$struct="";
		$file_to_use=$data_files."/layout_".$this->client_identifier."_restricted.xml";
		if (file_exists($file_to_use)){
			$struct = join("",file($file_to_use));
		}

		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* load the menu structure
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
//		$this->load_menu();
//		$this->displayed="";
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* return the list of menu locations and folder identifier XSLT can then decide to display 
		* the menu in what ever way it wants.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
//		reset ($this->menu_structure);
//		$struct = $this->display_level(-1,1,-1,0);
		$type="";
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$type="ENTERPRISE";
		}
		if ($this->parent->server[LICENCE_TYPE]==MECM){
			$type="LITE";
		}
		if ($this->parent->server[LICENCE_TYPE]==SITE_WIZARD){
			$type="WIZARD";
		}
		$str= "<module name=\"".$this->module_name."\" display=\"list_menu\"><page_options>";
		$str .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("ADD_INTERNAL","LAYOUT_ADD_MENU",ADD_NEW,"folder=$folder"));
		$str .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("ADD_EXTERNAL","LAYOUT_ADD_MENU",ADD_NEW_EXTERNAL,"external=1&amp;folder=$folder"));
		$str .= "<header><![CDATA[Structure Manager (Menu List)]]></header>";
		if ($this->check_parameters($_SESSION,"MENU_CREATION_ERROR")!=""){
			$str .= "<alert><![CDATA[".$_SESSION["MENU_CREATION_ERROR"]."]]></alert>";
			unset($_SESSION["MENU_CREATION_ERROR"]);
		}
		$str .= "</page_options><infolder type=\"$type\">$folder</infolder><menulinks><![CDATA[admin/index.php?command=LAYOUT_LIST_MENU&folder=]]></menulinks>
		".$struct."
		</module>";
		return $str;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Generate the menu used in the web site.
	- this is based on the menu sturcture that has been produced by the system
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function web_generate_menu(){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* if the user is not logged in then try to load the anonymous access cached menu.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN","0")=="0"){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- load the unrestricted menu structure
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
		
			$file_to_use=$data_files."/layout_".$this->client_identifier."_anonymous.xml";
		}else{
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- load the restricted menu structure
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$file_to_use=$data_files."/layout_".$this->client_identifier."_restricted.xml";
		}
		if (!file_exists($file_to_use)){
			$this->cache_menu_structure();
		}
		$cached_file = "";
		$cached_file .= join("",file($file_to_use));
		
		return "<module name=\"".$this->module_name."\" display=\"menu\">".$cached_file."</module>";
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Generate the form used to edit menu locations.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function menu_modify_form($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"admin_generate_menu",__LINE__,""));
		}
		$headlines = 0; // only available on an edit if this location has children.
		$headline_children = Array();
		$menu_identifier	= $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"menu_identifier",-1));
		$menu_parent 		= $this->check_parameters($parameters,"folder",-1);
		$folder 			= $this->check_parameters($parameters,"menu_parent",$this->check_parameters($parameters,"folder",-1));
		$btn 				= $this->check_parameters($parameters,"btn","");
		$command 			= $this->check_parameters($parameters,"command");
		$menu_external		= $this->check_parameters($parameters,"external",0);
		$error="";
		$list_of_current_channels	= array("PRESENTATION_DISPLAY");
		$list_of_current_groups		= array();
		$menu_image_inherit			= 1;
		$menu_images				= 1;
		$menu_headline_all			= 0;
		$menu_headline				= 0;
		$menu_headline_label		= 1;
		$menu_headline_counter		= 3;
		$menu_headline_title_pages	= 0;
		$menu_image_display			= 0;
		$menu_icon					= 0;
		$menu_archiving				= 0;
		$menu_archive_on			= 0;
		$menu_archive_display		= 0;
		$menu_archive_access		= 0;
		$menu_archive_label			= "";
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Generate the form used to edit menu locations.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$has_theme_module	= $this->call_command("ENGINE_HAS_MODULE",array("THEME_"));
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* reset the displayed variable to blank ie we have not displayed any content yet
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$this->displayed="";
		$menu_label			= "";
		$menu_url			= "";
		$menu_sort_by		= -1;
		$menu_hidden		= 0;
		$menu_stylesheet 	= $this->call_command("THEME_GET_STYLESHEET_FORMAT_IDENTIFIER",Array("LOCALE_THEME_005_TYPE_DISPLAY"));
		$menu_order			= 0;
		$menu_alt_text		= "";
		$menu_ssl			= 0;
		$headertxt			= LOCALE_ADD;
		if (($command=="LAYOUT_EDIT_MENU_REDO") || ($command=="LAYOUT_ADD_MENU_REDO")){
			$menu_label					= $this->check_parameters($parameters,"menu_label");
			$menu_parent				= $this->check_parameters($parameters,"menu_parent");
			$menu_sort_by				= $this->check_parameters($parameters,"menu_sort");
			$menu_stylesheet			= $this->check_parameters($parameters,"menu_stylesheet");
			$menu_order					= $this->check_parameters($parameters,"menu_order");
			$menu_hidden				= $this->check_parameters($parameters,"menu_hidden");
			$menu_alt_text				= $this->check_parameters($parameters,"menu_alt_text");
			$menu_url					= $this->check_parameters($parameters,"menu_url");
			$menu_ssl					= $this->check_parameters($parameters,"menu_ssl",0);
			$menu_headline				= $this->check_parameters($parameters,"menu_headline",0);
			$menu_images				= $this->check_parameters($parameters,"menu_images",1);
			$menu_image_inherit			= $this->check_parameters($parameters,"menu_image_inherit",1);
			$menu_headline_title_pages	= $this->check_parameters($parameters,"menu_headline_title_pages",0);
			$menu_archiving				= $this->check_parameters($parameters,"menu_archiving",0);
			$menu_archive_on			= $this->check_parameters($parameters,"menu_archive_on",0);
			$menu_archive_display		= $this->check_parameters($parameters,"menu_archive_display",0);
			$menu_archive_access		= $this->check_parameters($parameters,"menu_archive_access",0);
			$menu_archive_label			= $this->check_parameters($parameters,"menu_archive_label","");
			$menu_archive_display_number_of_pages			= $this->check_parameters($parameters,"menu_archive_display_number_of_pages",0);
			$menu_archive_display_by_date			= $this->check_parameters($parameters,"menu_archive_display_by_date",0);
			if($menu_archive_display_number_of_pages>0){
				$menu_archive_display = $menu_archive_display_number_of_pages;
			}
			if($menu_archive_display_by_date>0){
				$menu_archive_display = $menu_archive_display_by_date;
			}
			$menu_headline_all		= $this->check_parameters($parameters,"menu_headlines_all",0);
			$menu_headline_label	= $this->check_parameters($parameters,"menu_headline_label",1);
			$menu_headline_counter	= $this->check_parameters($parameters,"menu_headline_counter",3);
			$menu_external			= $this->check_parameters($parameters,"external",0);
			$headertxt				= LOCALE_REEDIT_OF_MENU;
		} else {
			if ($menu_identifier>-1){
				if($menu_external==0){
					/*
	                -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	                - get headline information
	                -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	                */
					$sql = "Select * from menu_data 
						left outer join menu_to_object on mto_module='LAYOUT_' and mto_menu=menu_identifier and mto_client=menu_client and mto_object = menu_parent
					where menu_client=$this->client_identifier and (menu_parent=$menu_identifier or menu_identifier = $menu_identifier) order by menu_order";
					$result  = $this->parent->db_pointer->database_query($sql);
					$headline_children_counter =0;
					$menu_url="";
	                while($r = $this->parent->db_pointer->database_fetch_array($result)){
						if($r["menu_identifier"]!=$menu_identifier){
	        	        	$headline_children[count($headline_children)] = Array($r["menu_identifier"], $r["menu_label"],$this->check_parameters($r,"mto_identifier",0));
							$headline_children_counter ++;
							$headlines=1;
						} else {
							$menu_url = $r["menu_url"];
						}
	                }
	                $this->call_command("DB_FREE",Array($result));
					if ($headline_children_counter==0 && $menu_url=="index.php"){
						$sql = "Select * from menu_data 
							left outer join menu_to_object on mto_module='LAYOUT_' and mto_menu=menu_identifier and mto_client=menu_client and mto_object = menu_parent
						where menu_client=$this->client_identifier and menu_parent=-1 and menu_url!='index.php' and menu_url!='admin/index.php' order by menu_order";
						$result  = $this->parent->db_pointer->database_query($sql);
						$headline_children_counter =0;
	                	while($r = $this->parent->db_pointer->database_fetch_array($result)){
	            	    	$headline_children[count($headline_children)] = Array($r["menu_identifier"], $r["menu_label"],$this->check_parameters($r,"mto_identifier",0));
							$headline_children_counter ++;
							$headlines=1;
		                }
	                	$this->call_command("DB_FREE",Array($result));
					}
				}
				$headertxt = LOCALE_EDIT;
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- get this menu record
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$sql = "Select * from menu_data where menu_client=$this->client_identifier and menu_identifier=$menu_identifier ";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
				}
				$result  = $this->parent->db_pointer->database_query($sql);
				if ($result){
					while($r = $this->parent->db_pointer->database_fetch_array($result)){
						$menu_label					= $this->convert_amps($r["menu_label"]);
						$menu_parent				= $r["menu_parent"];
						$menu_url					= $r["menu_url"];
						$menu_sort_by				= $r["menu_sort"];
						$menu_stylesheet			= $r["menu_stylesheet"];
						$menu_order					= $r["menu_order"];
						$menu_hidden				= $r["menu_hidden"];
						$menu_url					= $r["menu_url"];
						$menu_external				= $this->check_parameters($r,"menu_external",0);
						$menu_ssl					= $this->check_parameters($r,"menu_ssl",0);
						$menu_alt_text				= $this->check_parameters($r,"menu_alt_text");
						$menu_image_display			= $this->check_parameters($r,"menu_image_display",0);
						$menu_headline_all 			= $this->check_parameters($r,"menu_headline_all",0);
						$menu_headline_content 		= $this->check_parameters($r,"menu_headline_content",0);
						$menu_headline				= $this->check_parameters($r,"menu_headline",0);
						$menu_images				= $this->check_parameters($r,"menu_images",1);
						$menu_image_inherit			= $this->check_parameters($r,"menu_image_inherit",1);
						$menu_headline_title_pages	= $this->check_parameters($r,"menu_headline_title_pages",0);
						$menu_headline_label		= $this->check_parameters($r,"menu_headline_label",1);
						$menu_headline_counter		= $this->check_parameters($r,"menu_headline_counter",3);
						$menu_icon					= $this->check_parameters($r,"menu_icon");
						$menu_archiving				= $this->check_parameters($r,"menu_archiving",0);
						$menu_archive_on			= $this->check_parameters($r,"menu_archive_on",0);
						$menu_archive_display		= $this->check_parameters($r,"menu_archive_display",0);
						$menu_archive_label			= $this->check_parameters($r,"menu_archive_label","");
						$menu_archive_access		= $this->check_parameters($r,"menu_archive_access",0);
					}
				}
				$folder = $menu_parent;
				$sql = "select display_command from display_data where display_menu = $menu_identifier and display_client = $this->client_identifier";
				
				$result  = $this->parent->db_pointer->database_query($sql);
				while ($r = $this->parent->db_pointer->database_fetch_array($result)){
					$list_of_current_channels[count($list_of_current_channels)] = $r["display_command"];
				}
			}
		}
			if($menu_image_inherit==0){
				$menu_image_inherit=1;
			}else{
				$menu_image_inherit = 0;
			}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* get the sort types
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$sql = "Select * from menu_sort ";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$result  = $this->parent->db_pointer->database_query($sql);
		$sort_array="";
		if ($result){
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$sort_array	.= "<option value='".$r["menu_sort_identifier"]."'";
				if ($menu_sort_by==$r["menu_sort_identifier"]){
					$sort_array	.= " selected=\"true\" ";
				}
				$sort_array	.= "><![CDATA[".$r["menu_sort_label"]."]]></option>";
			}
		}
		$prev_menu_parent = $menu_parent;
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* retrieve the list of menu locations that are able to be selected
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$data =  $this->list_all_menu_locations_indent_value(array($menu_parent),-1,0,array("hide_locations"=>$menu_identifier));

		$outtext  = "\t\t<module name=\"layout_menu_manager\" display=\"form\">\n";
		$outtext .= "\t\t\t<page_options>\n";
		$outtext .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","LAYOUT_LIST_MENU&amp;folder=$folder",LOCALE_CANCEL));
		$outtext .= "<header><![CDATA[".LOCALE_MENU_WIZARD." - ".$headertxt."]]></header>";
		$outtext .= "</page_options>\n";
		if ($command=="LAYOUT_EDIT_MENU_REDO"){
			$command ="LAYOUT_EDIT_MENU";
		}
		if ($command=="LAYOUT_ADD_MENU_REDO"){
			$command ="LAYOUT_ADD_MENU";
		}
		$out  = "<form name=\"layout_form\" method=\"post\" label=\"".LOCALE_MENU_WIZARD."\">\n";
		$out .= "<loadcache></loadcache>";
		$out .= "<input type=\"hidden\" name=\"menu_identifier\" value=\"$menu_identifier\"/>\n";
		$out .= "<input type=\"hidden\" name=\"prev_url\" value=\"$menu_url\"/>\n";
		$out .= "<input type=\"hidden\" name=\"access_command\" value=\"$command\"/>\n";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"LAYOUT_SAVE_MENU\"/>\n";
		$out .= "<input type=\"hidden\" name=\"folder\" value=\"$folder\"/>\n";
		$out .= "<input type=\"hidden\" name=\"menu_external\" value=\"$menu_external\"/>
		<page_sections>";
		$session_management_access	= $this->check_parameters($_SESSION,"SESSION_MANAGEMENT_ACCESS",Array());
		$hide_advanced =0;
		if (count($session_management_access)>0){
			$hide_advanced =1;
		}
		$group_info					= $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max = count($group_info);
		
		for ($index=0; $index < $max; $index++){
			$list = $group_info[$index]["ACCESS"];
			if (in_array("ALL",$list) || in_array("LAYOUT_ALL",$list) || in_array("LAYOUT_CAN_MANAGE_MENU",$list)){
				$hide_advanced =0;
			}
		}
		
		if ($menu_url!='admin/index.php' ){
			$out .= "<section label='".LOCALE_PROPERTIES."'>$error";
			if ($menu_url!='index.php' && $menu_url!='admin/index.php' ){
				$out .= "<input type=\"hidden\" name=\"prev_menu_label\"><![CDATA[$menu_label]]></input>\n";
				$out .= "<input type=\"text\" label=\"".LOCALE_LAYOUTADMIN_MENU_LABEL."\" size=\"255\" required=\"YES\"  name=\"menu_label\"><![CDATA[$menu_label]]></input>\n";
			} else {
				$out .= "<input type=\"hidden\" name=\"menu_parent\" value=\"$menu_parent\"/>\n";
				$out .= "<input type=\"hidden\" name=\"prev_menu_label\"><![CDATA[$menu_label]]></input>\n";
				$out .= "<input type=\"hidden\" name=\"menu_label\"><![CDATA[$menu_label]]></input>\n";
			}
			$out .= "<input type=\"hidden\" name=\"prev_menu_parent\" value=\"$prev_menu_parent\"/>\n";
			if ($menu_url!='index.php' && $menu_url!='admin/index.php' ){
				$root_location = (defined('ROOT_LOCATION')?ROOT_LOCATION:$this->parent->domain);
				$out .= "<select label=\"".LOCALE_LAYOUTADMIN_WHERE_IN_SITE_STRUCTURE."\" name=\"menu_parent\" onsave=\"menu_check_parent()\"><option value=\"-1\">".$root_location."</option>$data</select>\n";
			}
			$out .= "<input type=\"hidden\" name=\"menu_order\" value=\"$menu_order\"/>\n";
			if($menu_external==0){
				if ($has_theme_module){
					$stylesheet = $this->call_command("THEME_GET_STYLESHEET_OPTIONS", array($menu_stylesheet));
					if (strlen($stylesheet)>0){
						$out .= "<select label=\"".LOCALE_LAYOUTADMIN_CHOOSE_LOCATION_DISPLAY_FORMAT."\" name=\"menu_stylesheet\">$stylesheet</select>\n";
						$out .= "<input type='hidden' name='prev_menu_stylesheet' value='$menu_stylesheet'/>\n";
					}
				}
				$menu_image_display_array = Array(
					Array(0, LOCALE_LAYOUTADMIN_CHOOSE_SUMMARY_IMAGE_DISPLAY_LEFT), 
					Array(1, LOCALE_LAYOUTADMIN_CHOOSE_SUMMARY_IMAGE_DISPLAY_RIGHT), 
					Array(2, LOCALE_LAYOUTADMIN_CHOOSE_SUMMARY_IMAGE_DISPLAY_ALT)
				);
				$out .= "<select label=\"".LOCALE_LAYOUTADMIN_CHOOSE_SUMMARY_IMAGE_DISPLAY."\" name=\"menu_image_display\">";
				for($sumindex=0;$sumindex<3;$sumindex++){
					$out .="<option value='".$menu_image_display_array[$sumindex][0]."'";
					if($menu_image_display==$menu_image_display_array[$sumindex][0]){
						$out.=" selected='true'";
					}
					$out .="><![CDATA[".$menu_image_display_array[$sumindex][1]."]]></option>";
				}
				$out .= "</select>\n";
			} else {
				if($menu_url==""){
					$out .= "<input type=\"text\" label=\"".LOCALE_LAYOUTADMIN_MENU_URL."\" size=\"255\" required=\"YES\"  name=\"menu_url\"><![CDATA[http://]]></input>\n";
				} else {
					$out .= "<input type=\"text\" label=\"".LOCALE_LAYOUTADMIN_MENU_URL."\" size=\"255\" required=\"YES\"  name=\"menu_url\"><![CDATA[$menu_url]]></input>\n";
				}
			}
			/*
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            - Archiving (enterprise only)
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            */
			if($menu_external==0){
				if (($this->parent->server[LICENCE_TYPE]==ECMS)){// || ($this->parent->server[LICENCE_TYPE]==MECM) ){
					$out .= "<radio name='menu_archiving' label='Enable Archiving on this menu location?' type='horizontal' onclick='subsection_display' tag='section_button_archive_btn'>";
					$out .= "<option value='0'";
					if($menu_archiving==0){
						$out .= " selected='true'";
					}
					$out .= ">No</option>";
					$out .= "<option value='1'";
					if($menu_archiving==1){
						$out .= " selected='true'";
					}
					$out .= ">Yes</option>";
					$out .= "</radio>";
				} else {
					$out .= "<input type=\"hidden\" name=\"menu_archiving\" value=\"0\"/>\n";		
				}
			}
			$out .= "</section>";
		} else {
			
		}
		if(($menu_external==0) && ($this->parent->server[LICENCE_TYPE] == ECMS || $this->parent->server[LICENCE_TYPE] == MECM)){
			$out .= "<section label='".LOCALE_ADVANCED_OPTIONS."' name='access_advanced'";
			if ($btn == "access_advanced"){
				$out .= " selected='true'";
			}			
			if($this->menu_advanced_access ==0){
				$out .= " hidden='true'";
			}
			$out .= ">
			<radio label='".LOCALE_LOCATION_VISIBLE."' name='menu_hidden'><options><option value='0'";
			if ($menu_hidden.""=="0"){
				$out .= " selected='true'";
			}
			$out .= ">".LOCALE_YES."</option><option value='1'";
			if ($menu_hidden.""=="1"){
				$out .= " selected='true'";
			}
			$out .= ">".LOCALE_NO."</option></options></radio>";
			$out .= "<radio label='".LOCALE_USE_SSL."' name='menu_ssl'><options><option value='0'";
			if ($menu_ssl.""=="0"){
				$out .= " selected='true'";
			}
			$out .= ">".LOCALE_NO."</option><option value='1'";
			if ($menu_ssl.""=="1"){
				$out .= " selected='true'";
			}
			$out .= ">".LOCALE_YES."</option></options></radio>";
			$out .= "<input type=\"text\" label=\"".LOCALE_LAYOUT_ADMIN_MENU_ALT_TEXT."\" size=\"255\" name=\"menu_alt_text\"><![CDATA[$menu_alt_text]]></input>\n";
			$out .= "</section>";
		}
		if(($menu_external==0) && ($menu_url!='index.php' ) && ($this->parent->server[LICENCE_TYPE] == ECMS || $this->parent->server[LICENCE_TYPE] == MECM)){
			$out.="<section label='".LOCALE_LAYOUTADMIN_ACCESS_RESTRICTIONS."' name='access_restrictions'";
			if ($btn == "access_restrictions"){
				$out .= " selected='true'";
			}
			if ($this->menu_group_access==0){
				$out .= " hidden='true'";
			}
			$out .= ">";
			$sql  = "select DISTINCT * from relate_menu_groups where menu_identifier = $menu_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$list_of_current_groups[count($list_of_current_groups)] = $r["group_identifier"];
			}
			$module_group_options = $this->call_command("GROUP_RETRIEVE_BY_GROUP_TYPE",array($list_of_current_groups));
			$out .= "<checkboxes type=\"horizontal\" label=\"".LOCALE_LAYOUTADMIN_MENU_ACCESS_RESTRICTIONS."\" name=\"menu_group_access\">$module_group_options</checkboxes>\n";
			$out .= "</section>";
		}
		if(($menu_external==0) && ($menu_url!='admin/index.php' ) && ($this->parent->server[LICENCE_TYPE] == ECMS || $this->parent->server[LICENCE_TYPE] == MECM)){
			$out .= "<section label='".LOCALE_LAYOUTADMIN_CHANNEL_MANAGER."' name='channel_manager'";
			if ($btn == "channel_manager"){
				$out .= " selected='true'";
			}
			if($this->menu_channel_access ==0){
				$out .= " hidden='true'";
			}
			$out .= ">";
				$out .="<radio label=\"".LOCALE_LAYOUTADMIN_CHOOSE_CHANNEL_INHERITANCE."\" name=\"menu_inherit\" onclick='show_hidden'>";
				$out .="<option value='1'";
				if ($menu_identifier==-1){
					$out .=" selected='true'";
				}
				$out .=">".ENTRY_YES."</option>";
				$out .="<option value='0'";
				if ($menu_identifier!=-1){
					$out .=" selected='true'";
				}
				$out .=">".ENTRY_NO."</option>";
				$out .="</radio>\n";
//			}
			$module_display_options_array = $this->call_command("ENGINE_RETRIEVE",array("MENU_DISPLAY_OPTIONS",array($list_of_current_channels)));
			$module_display_options="";
			$length_of_array=count($module_display_options_array);
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"length of display options",__LINE__,"$length_of_array"));
			}
			for($index=0;$index<$length_of_array;$index++){
				if (strlen($module_display_options_array[$index][1])>0){
					$module_display_options .= $module_display_options_array[$index][1];
				}
			}
			$out .="<checkboxes ";
			if ($menu_identifier==-1){
				$out .="hidden='YES' ";
			}
			$out .="type=\"horizontal\" label=\"".LOCALE_LAYOUTADMIN_CHOOSE_CHANNEL."\" id=\"menu_display\" name=\"menu_display\">$module_display_options</checkboxes>\n";


/*			$out .= "</section><section label='Metadata' name='metadata'";
			if ($btn == "metadata"){
				$out .= " selected='true'";
			}
			$out .= ">";*/
/*			$out .= "</section><section label='WorkFlow' name='workflow'";
			if ($btn == "workflow"){
				$out .= " selected='true'";
			}
			$out .= ">";
			*/
		$out .= "</section>";
		}
		if(($menu_external==0) && ($menu_url!='admin/index.php' )){
		$out .="<section label='Rank Pages' name='rank_pages'";
			if ($btn == "rank_pages"){
				$out .= " selected='true'";
			}
			$out .= ">";
			$sql = "
			Select menu_data.menu_sort, menu_access_to_page.*, page_trans_data.trans_title, page_trans_data.trans_date_available from menu_data 
				inner join menu_access_to_page on menu_data.menu_identifier = menu_access_to_page.menu_identifier 
				inner join page_trans_data on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
			where 
				page_trans_data.trans_current_working_version = 1 and 
				menu_access_to_page.client_identifier = $this->client_identifier and 
				menu_access_to_page.menu_identifier = $menu_identifier
			order by 
				menu_access_to_page.title_page desc, 
				menu_access_to_page.page_rank";
			$result 		 = $this->parent->db_pointer->database_query($sql);
			$c 				 = 0;
			$menu_sort_by	 = 1;
			$ranks			 = "";
			$title_page		 = 0;
			while ($r 		 = $this->parent->db_pointer->database_fetch_array($result)) {
				$c++;
				if ($r["title_page"]==1){
					$title_page = $r["trans_identifier"];;
				}
				$rank  		 = $r["page_rank"];
				$page  		 = $r["trans_identifier"];
				$menu  		 = $r["menu_identifier"];
				$title 		 = $this->split_me($r["trans_title"],"\n","");
				$available	 = $r["trans_date_available"];
				$menu_sort_by= $r["menu_sort"];
				$ranks  	.= "<ranking identifier=\"$page\" rank=\"$rank\" menu=\"$menu\" available=\"$available\"><![CDATA[$title]]></ranking>";
			}
			/*	
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- get the sort types
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql = "Select * from menu_sort ";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			$result  = $this->parent->db_pointer->database_query($sql);
			$sort_array="";
			if ($result){
				while($r = $this->parent->db_pointer->database_fetch_array($result)){
					$sort_array	.= "<option value='".$r["menu_sort_identifier"]."'";
					if ($menu_sort_by==$r["menu_sort_identifier"]){
						$sort_array	.= " selected=\"true\" ";
					}
					$sort_array	.= ">".$r["menu_sort_label"]."</option>";
				}
			}
			$out .="<select name='menu_sort' onchange='bubble(this)' label='".ENTRY_ORDER_BY."'>$sort_array</select>";
			$out .="<ranks title='$title_page'>$ranks</ranks>";
			$out .= "</section>";
		}
/*		$list = $this->call_command("ENGINE_RETRIEVE",Array("EMBED_IN_MENU_EDIT",$parameters));
		$max=count($list);
		$outextra="";
		for($index=0;$index<$max;$index++){
				$outextra.=	$list[$index][1];
		}

		if ($outextra!=""){
			$out .="<section label='Extra Settings' name='extrasettings'>";
			$out .= $outextra;
			$out .= "</section>";
		}
		*/
		/**
		* Menu images
	*/ 
		if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM) ){
			if($menu_external==0){
				$listOfContainer = $this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("restrict"=>1, "module"=>"LAYOUT_", "identifier"=> $menu_identifier, "returnType"=>1));
				$m = count($listOfContainer[0]);
				if($m>0){
					$out .="<section label='Menu Images' name='attached_image'>";
					$out .= "<radio name='menu_image_inherit' label='Inherit from parent' type='horizontal' onclick='subsection_display' tag='image_options_2'>";
					$out .= "<option value='1'";
					if($menu_image_inherit==1){
						$out .= " selected='true'";
					}
					$out .= ">No</option>";
					$out .= "<option value='0'";
					if($menu_image_inherit==0){
						$out .= " selected='true'";
					}
					$out .= ">Yes</option>";
					$out .= "</radio>";

					$out .= "<subsection label=\"Inherit Options\" name=\"image_options\" tag=''";
					if($menu_image_inherit==0){
						$out.=" hidden='true'";
					}
					$out .= ">";
					$out .= "<radio name='menu_images' label='Enable images in this menu location' type='horizontal'  onclick='subsection_display' tag='menu_container_images_2' >";
					$out .= "<option value='0'";
					if($menu_images==0){
						$out .= " selected='true'";
					}
					$out .= ">No</option>";
					$out .= "<option value='1'";
					if($menu_images==1){
						$out .= " selected='true'";
					}
					$out .= ">Yes</option>";
					$out .= "</radio>";
					$id_list = "'LAYOUT_menu_icon'";
					for($i=0;$i<$m;$i++){
						if($id_list!=""){
							$id_list .= ",";
						}
						$id_list .= "'LAYOUT_".$listOfContainer[0][$i]["id"]."'";
					}
					$sql = "select * from file_to_object 
					inner join file_info on fto_file = file_identifier and file_client = fto_client
					where fto_object=$menu_identifier and fto_client=$this->client_identifier and fto_module in ($id_list)";
					$result  = $this->parent->db_pointer->database_query($sql);
					while($r = $this->parent->db_pointer->database_fetch_array($result)){
						$found=0;
						for($i=0; $i<$m ;$i++){
							if("LAYOUT_".$listOfContainer[0][$i]["id"]==$r["fto_module"]){
								$listOfContainer[0][$i]["file"]="
									<file>
										<label><![CDATA[".$r["file_label"]."]]></label>
										<id><![CDATA[".$r["file_identifier"]."]]></id>
										<md5><![CDATA[".$r["file_md5_tag"]."]]></md5>
										<path><![CDATA[".$this->retrieve_directory_path($r["file_directory"])."]]></path>
										<extension><![CDATA[".$this->file_extension($r["file_name"])."]]></extension>
									</file>
								";
								$found=1;
							}
						} 
						if($found==0){
							$menu_icon = "
								<file>
									<label><![CDATA[".$r["file_label"]."]]></label>
									<id><![CDATA[".$r["file_identifier"]."]]></id>
									<md5><![CDATA[".$r["file_md5_tag"]."]]></md5>
									<path><![CDATA[".$this->retrieve_directory_path($r["file_directory"])."]]></path>
									<extension><![CDATA[".$this->file_extension($r["file_name"])."]]></extension>
								</file>
							";
						}
					}
					$menu_container_images=1;
					$out .= "<subsection label=\"Define Image for loaction\" name=\"menu_container_images\" tag='menu_container_images'";
					if($menu_images==0){
						$out.=" hidden='true'";
					}
					$out .= ">";
					$m = count($listOfContainer[0]);
					$out .= "<attached_files>";
	/*
					$out .= 	"<attached_file label='icon for menu location' id='menu_icon'>";
					$out .= 		$menu_icon;
					$out .= 	"</attached_file>";
	*/
					for($i=0;$i<$m;$i++){
						$out .= "<attached_file label='".$listOfContainer[0][$i]["label"]."' id='".$listOfContainer[0][$i]["id"]."'>";
						$out .= $this->check_parameters($listOfContainer[0][$i],"file");
						$out .= "</attached_file>";
					}
					$out .= "</attached_files>";
					
					$out .= "</subsection>";
					$out .= "</subsection>";
									
		    		$this->call_command("DB_FREE",Array($result));
					$out .= "</section>";
				}
			}
		}
		/**
		* Menu images
	*/ 
		if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM) ){
			if(($menu_external==0) && $headlines==1 && $headline_children_counter!=0){
				$out .= "<section label='Auto headline' name='headliner'>";
				$out .= "<radio name='menu_headline' label='Extract Headlines' type='horizontal' onclick='subsection_display' tag='head_line_options_2'>";
				$out .= "<option value='0'";
				if($menu_headline==0){
					$out .= " selected='true'";
				}
				$out .= ">No</option>";
				$out .= "<option value='1'";
				if($menu_headline==1){
					$out .= " selected='true'";
				}
				$out .= ">Yes</option>";
				$out .= "</radio>";
				$out .= "<subsection label=\"Options\" name=\"head_line_options\"";
				if($menu_headline==0){
					$out.=" hidden='true'";
				}
				$out .= ">";
				
				$out .= "<text><![CDATA[If generating headlines from the sub menu loactions then select your options.]]></text>";
				
				$out .= "<radio name='menu_headline_title_pages' label='Should title pages be included in the headlines that are generated' type='horizontal' >";
				$out .= "<option value='0'";
				if($menu_headline_title_pages==0){
					$out .= " selected='true'";
				}
				$out .= ">No</option>";
				$out .= "<option value='1'";
				if($menu_headline_title_pages==1){
					$out .= " selected='true'";
				}
				$out .= ">Yes</option>";
				$out .= "</radio>";
				
				
				$out .= "<radio name='menu_headline_all' label='Extract from all children' type='horizontal' onclick='subsection_display' tag='head_line_menu_locations_7'>";
	//			print $menu_headline_all;
				$out .= "<option value='1'";
				if($menu_headline_all==1){
					$out .= " selected='true'";
				}
				$out .= ">No</option>";
				$out .= "<option value='0'";
				if($menu_headline_all==0){
					$out .= " selected='true'";
				}
				$out .= ">Yes</option>";
				$out .= "</radio>";
				$out .= "<select name='menu_headline_content' label='Display format for the headlines' >";
	//			print $menu_headline_all;
				$list_of_displays = Array(
					Array(0, 'Title Only'),
					Array(2, 'Title Only (2 column)'),
					Array(3, 'Title Only (3 column)'),
					Array(1, 'Title and Summary'),
					Array(4, 'Title and Summary (2 column)'),
					Array(5, 'Title and Summary (3 column)')
				);
				$max_list = count($list_of_displays);
				for($i=0;$i<$max_list;$i++){
					$out .= "<option value='".$list_of_displays[$i][0]."'";
					if($menu_headline_content==$list_of_displays[$i][0]){
						$out .= " selected='true'";
					}
					$out .= ">".$list_of_displays[$i][1]."</option>";
				}
				$out .= "</select>";
				/*
				headline yes/no
				headline all children
				show sub menu labels
				choose locations
				#number of page to extract
				*/
				$out .= "<radio name='menu_headline_label' label='Show labels of sub levels' type='horizontal'>";
				$out .= "<option value='0'";
				if($menu_headline_label==0){
					$out .= " selected='true'";
				}
				$out .= ">No</option>";
				$out .= "<option value='1'";
				if($menu_headline_label==1){
					$out .= " selected='true'";
				}
				$out .= ">Yes</option>";
				$out .= "</radio>";
				
				$out .= "<select name='menu_headline_counter' label='How many entries should be pulled from each sub menu location' type='horizontal'>";
				for($i=0;$i<10;$i++){
				$out .= "<option value='$i'";
				if($menu_headline_counter==$i){
					$out .= " selected='true'";
				}
				$out .= ">$i</option>";
				}
				$out .= "</select>";
				$out .= "<subsection label=\"Select Specific menu locations\" name=\"head_line_menu_locations\"";
				if($menu_headline_all==0){
					$out.=" hidden='true'";
				}
				$out .= ">";
				$out .= "<checkboxes name='menu_headline_locations' label='Choose only the menu location you want to extract from' type='vertical'>";
				
				for($i=0;$i<$headline_children_counter;$i++){
				$out .= "<option value='".$headline_children[$i][0]."'";
				if($headline_children[$i][2]>0){
					$out .= " selected='true'";
				}
				$out .= "><![CDATA[".$headline_children[$i][1]."]]></option>";
				}
				$out .= "</checkboxes>";
				
				
				$out .= "</subsection>";
				
				$out .= "</subsection>";
				
				$out .= "</section>";
			}
		}
		/*
	    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	    - Archiving
	    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	    */
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			if($menu_external==0){
				$out .= "<section label='Archive Settings' name='archive'";
				if($menu_archiving==0){
					$out .= " hidden='true'";
				}
				$out.=">";
	/*
				$menu_archiving
				$menu_archive_on
				$menu_archive_display
				$menu_archive_access
	*/
				$archive_array = Array(
					Array(1,"When article is 1 year old","DATE"),
					Array(2,"When article is 1 month old","DATE")/*,
					Array(5,"After five entries","NUM"),
					Array(10,"After ten entries","NUM"),
					Array(25,"After twenty-five entries","NUM"),
					Array(50,"After fifty entries","NUM")*/
				);
	/*
				$out .= "<select name='menu_archive_display_number_of_pages' label='How many entries should be displayed before archiving takes place'><option value='0'>Choose one </option>";
				$aac= count($archive_array);
				for($i=0;$i<$aac;$i++){
					if($archive_array[$i][2]=="NUM"){
						$out .= "<option value='".$archive_array[$i][0]."'";
						if($menu_archive_display==$archive_array[$i][0]){
							$out .= " selected='true'";
						}
						$out .= ">".$archive_array[$i][1]."</option>";
					}
				}
				$out .= "</select>";
	*/
				$out .= "<select name='menu_archive_display_by_date' label='When should archiving take place'><option value='0'>Choose one </option>";
				$aac= count($archive_array);
				for($i=0;$i<$aac;$i++){
					if($archive_array[$i][2]=="DATE"){
						$out .= "<option value='".$archive_array[$i][0]."'";
						if($menu_archive_display==$archive_array[$i][0]){
							$out .= " selected='true'";
						}
						$out .= ">".$archive_array[$i][1]."</option>";
					}
				}
				$out .= "</select>";
	
	/*			$out .= "<select name='menu_archive_access' label='How should users access archived articles'>";
				$archive_array = Array(
					"Use Site Search",
					"Use Tab grouping articles via year published",
					"Use Tab grouping articles via year and month published",
					"Use link to archive page"
				);
				$aac= count($archive_array);
				for($i=0;$i<$aac;$i++){
					$out .= "<option value='".$i."'";
					if($menu_archive_access==$i){
						$out .= " selected='true'";
					}
					$out .= ">".$archive_array[$i]."</option>";
				}
				$out .= "</select>";
	*/
				$out .= "</section>";
			}
		}
		

		$out .= "</page_sections>\n";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".ENTRY_SAVE."\"/>\n";
		$out .= "</form>\n";

		$outtext .="$out\t\t</module>\n";

		return $outtext;
	}

	function check_this_directory_for_this_file($parent, $label, $existing_id){
		$uri = $this->make_uri($label);
		$sql = "select 
					menu_data.menu_label, 
					directory_data.directory_name 
				from menu_data 
					left outer join directory_data on menu_directory = directory_identifier 
				where 
					menu_parent = $parent and 
					menu_client=$this->client_identifier and
					directory_name='$uri' and
					menu_identifier != $existing_id
					";
		$result  = $this->parent->db_pointer->database_query($sql);
		$row_counter = $this->call_command("DB_NUM_ROWS",array($result));
		if ($row_counter>0){
			return 1;
		}else {
			return 0;
		}
	}
	/**
	* save the menu wizards data
	*
	* function to save the menu information
	*/
	function save($parameters){
		$menu_directory=-1;
		$found_existing = 0;
		$administration=0;
		$command =$this->check_parameters($parameters,"access_command",$this->check_parameters($parameters,"command"));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save",__LINE__,"[".print_r($parameters,true)."]"));
		}
		$client = $this->client_identifier;
		/**
		* menu insert is from page add/edit
		*/ 
		if ($command!="LAYOUT_MENU_INSERT"){
			$old_path="";
			$commands_to_execute	= $this->check_parameters($parameters,"modules",Array());
			$menu_inherit 			= $this->check_parameters($parameters,"menu_inherit",1);
			$prev_menu_stylesheet	= $this->check_parameters($parameters,"prev_menu_stylesheet",-1);
			$table = array();
			$table["menu_client"] 				= $client;
			$table["menu_label"]  				= trim($this->strip_tidy($this->check_parameters($parameters,"menu_label")));
			$table["menu_parent"]  				= $this->check_parameters($parameters, "menu_parent"				,-1);
			$table["menu_sort"]  				= $this->check_parameters($parameters, "menu_sort"					,"1");
			$table["menu_order"]  				= $this->check_parameters($parameters, "menu_order"					,"0");
			$table["menu_hidden"]				= $this->check_parameters($parameters, "menu_hidden"				,"0");
			$table["menu_alt_text"]				= $this->check_parameters($parameters, "menu_alt_text"				,"");
			$table["menu_ssl"]					= $this->check_parameters($parameters, "menu_ssl"					,"0");
			$table["menu_external"]				= $this->check_parameters($parameters, "menu_external"				,0);
			$table["menu_image_display"]		= $this->check_parameters($parameters, "menu_image_display"			,0);
			$table["menu_images"]				= $this->check_parameters($parameters, "menu_images"				,0);
			$table["menu_image_inherit"]		= $this->check_parameters($parameters, "menu_image_inherit"			,1);
			$table["menu_headline"]				= $this->check_parameters($parameters, "menu_headline"				,0);
			$table["menu_headline_all"]			= $this->check_parameters($parameters, "menu_headline_all"			,1);
			$table["menu_headline_label"]		= $this->check_parameters($parameters, "menu_headline_label"		,1);
			$table["menu_headline_counter"]		= $this->check_parameters($parameters, "menu_headline_counter"		,3);
			$table["menu_headline_content"]		= $this->check_parameters($parameters, "menu_headline_content"		,0);
			$table["menu_headline_title_pages"]	= $this->check_parameters($parameters, "menu_headline_title_pages"	,0);
			$table["menu_archiving"]			= $this->check_parameters($parameters, "menu_archiving"				,0);
			if($table["menu_archiving"]==0){
				$table["menu_archive_on"]			= 0;
				$table["menu_archive_display"]		= 0;
				$table["menu_archive_access"]		= 0;
			} else {
				$table["menu_archive_on"]				= $this->check_parameters($parameters, "menu_archive_on"			,0);
				$menu_archive_display_number_of_pages	= $this->check_parameters($parameters,"menu_archive_display_number_of_pages",0);
				$menu_archive_display_by_date			= $this->check_parameters($parameters,"menu_archive_display_by_date",0);

				$table["menu_archive_display"]			= 0;
				if($menu_archive_display_number_of_pages>0){
					$table["menu_archive_display"] 				= $menu_archive_display_number_of_pages;
				}
				if($menu_archive_display_by_date>0){
					$table["menu_archive_display"] 				= $menu_archive_display_by_date;
				}
				$table["menu_archive_access"]			= $this->check_parameters($parameters, "menu_archive_access"		,0);
			}
			if($table["menu_image_inherit"]==0){
				$table["menu_image_inherit"]=1;
			}else{
				$table["menu_image_inherit"] = 0;
			}
			
			if($table["menu_external"]==1){
				$table["menu_url"] 				=  $this->check_parameters($parameters, "menu_url"	,0);
			}
			//$table["menu_headline_locations"]	= $this->check_parameters($parameters, "menu_headline_locations"	,0);
			
			$menu_identifier 			= $this->check_parameters($parameters, "menu_identifier"	,-1);
			$prev_url 					= $this->check_parameters($parameters, "prev_url"			,"");
			if ($prev_url == 'admin/index.php'){
				$administration = 1;	
			}
			$has_theme_module		= $this->call_command("ENGINE_HAS_MODULE",array("THEME_"));
			if ($has_theme_module){
				$table["menu_stylesheet"]  = $this->check_parameters($parameters,"menu_stylesheet","14");
			}
			if($table["menu_external"]==0){
				if (strtolower($table["menu_label"])!=strtolower($this->check_parameters($parameters,"prev_menu_label"))){
					$menu_change=1;
				} else {
					$menu_change=0;
				}
				if ($table["menu_parent"]!=$this->check_parameters($parameters,"prev_menu_parent",-1)){
					$dir_change =1;
				} else {
					$dir_change=0;
				}
				if ($menu_change==1 || $dir_change==1){
					$found_existing = $this->check_this_directory_for_this_file($table["menu_parent"],$table["menu_label"], $menu_identifier);
				}
			} else {
				$found_existing = 0;
			}
		}
		/*Modified by Ali Imran Ahmad for archiving section*/
		if($table["menu_archiving"]!=0){
			$sql_archive = "Select * from web_objects WHERE wo_client=$this->client_identifier and wo_command='WEBOBJECTS_SHOW_ARCHIVE_OPTIONS' and wo_type=2";
			$result_archive = $this->call_command("DB_QUERY",array($sql_archive));
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result_archive));
			if ($number_of_records<1){
				$sql_archive = "insert into web_objects (wo_client,wo_type,wo_label,wo_command,wo_all_locations,wo_show_label) values ($this->client_identifier,2,'Show archive access point','WEBOBJECTS_SHOW_ARCHIVE_OPTIONS',1,0);";
				$this->call_command("DB_QUERY",array($sql_archive));
			}
		}
		/*End modifications by Ali Imran Ahmad*/
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* if adding then we want to insert the information into
		* the database
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($found_existing==0){
			/*
	        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        	- menu insert is from page add/edit
    	    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	        */
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"COMMAND",__LINE__,"$command"));}
			if ($command=="LAYOUT_MENU_INSERT"){
				$parent	= $this->check_parameters($parameters,"parent");
				$label	= strip_tags(str_replace(Array("&lt;","&gt;"),Array("<",">"),$this->tidy_parameter($parameters,"label")));
				$m_id =-1;
				$m_dir_parent=-1;
				$m_url = $this->make_uri($label)."/index.php";
				$menu_image_display			= 0;
				$menu_images				= 0;
				$menu_image_inherit			= 1;
				$menu_headline				= 0;
				$menu_headline_all			= 1;
				$menu_headline_label		= 0;
				$menu_headline_counter		= 3;
				$menu_headline_content		= 0;
				$menu_headline_title_pages	= 0;
				
				$params = Array();
				$params["directory_name"]=$label;
				$params["directory_identifier"]="";
				$params["directory_parent"]=-1;
				$params["directory_can_upload"] = "0";
				if($parent!=-1){
					$sql 	= "select md1.*, count(md2.menu_parent) as num_of_children , md2.menu_parent as children_parent from menu_data as md1
								left outer join menu_data as md2 on md1.menu_identifier = md2.menu_parent
							where md1.menu_identifier=$parent and md1.menu_client =$this->client_identifier
							group by md2.menu_parent";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					$result  = $this->parent->db_pointer->database_query($sql);
	                while($r = $this->parent->db_pointer->database_fetch_array($result)){
                		$m_url		= dirname($r["menu_url"])."/".$m_url;
						$m_order	= $r["menu_order"];
						$m_sort		= $r["menu_sort"];
						$m_parent	= $r["menu_parent"];
						$params["directory_name"]=$label;
						$params["directory_identifier"]="";
						$params["directory_parent"]=$r["menu_directory"];
						$params["directory_can_upload"] = "0";
						$this->save_directory($params);
						$m_dir_parent = $r["menu_directory"];
    	            }
	                $this->call_command("DB_FREE",Array($result));
				} else {
					$sql 	= "select count(menu_parent) as num_of_children , menu_parent as children_parent from menu_data
								where menu_parent = -1 and menu_client =$this->client_identifier
								group by menu_parent";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					$result  = $this->parent->db_pointer->database_query($sql);
	                while($r = $this->parent->db_pointer->database_fetch_array($result)){
						$m_order						= $r["num_of_children"] + 1;
						$m_sort							= 1;
						$m_parent						= -1;
						$params["directory_name"]		= $label;
						$params["directory_identifier"]	= "";
						$params["directory_parent"]		= -1;
						$params["directory_can_upload"] = "0";
						$this->save_directory($params);
						$m_dir_parent 					= -1;
    	            }
	                $this->call_command("DB_FREE",Array($result));
				}
				$sql ="select * from directory_data where directory_client=$this->client_identifier and directory_parent=".$m_dir_parent." and directory_name='".$this->make_uri($params["directory_name"])."'";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
				}
				$dir_result = $this->parent->db_pointer->database_query($sql);
				$m_dir = -1;
				while ($dir_r= $this->call_command("DB_FETCH_ARRAY",array($dir_result))){
					$m_dir = $dir_r["directory_identifier"];
				}
                $this->call_command("DB_FREE",Array($dir_result));
                /** by default menu_stylesheet is 5 */
				$sql = "insert into menu_data
					(menu_label, menu_parent, menu_client, menu_url, menu_alt_text, menu_order, menu_stylesheet, menu_sort, menu_theme, menu_directory, menu_hidden, menu_ssl, menu_image_display, menu_images, menu_image_inherit, menu_headline, menu_headline_all, menu_headline_label, menu_headline_counter, menu_headline_content, menu_headline_title_pages)
						values
					('$label', $parent, $this->client_identifier, '$m_url', '', $m_order, 5, $m_sort, 0, $m_dir, 0, 0,".$menu_image_display.", ".$menu_images.", ".$menu_image_inherit.", ".$menu_headline.", ".$menu_headline_all.", ".$menu_headline_label.", ".$menu_headline_counter.", ".$menu_headline_content.", ".$menu_headline_title_pages.")";
				$this->parent->db_pointer->database_query($sql);
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$sql = "select * from menu_data
					where
						menu_label 		= '$label' and
						menu_parent		= '$parent' and
						menu_client		= $this->client_identifier and
						menu_url		= '$m_url' and
						menu_alt_text	= '' and
						menu_order		= $m_order and
						menu_stylesheet	= 5 and
						menu_sort		= $m_sort and
						menu_theme		= 0 and
						menu_directory	= $m_dir and
						menu_hidden		= 0 and
						menu_ssl		= 0
					";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
//				print "<li>$sql</li>";
				$getresult  = $this->parent->db_pointer->database_query($sql);
				while($getr = $this->call_command("DB_FETCH_ARRAY",Array($getresult))){
					$m_id = $getr["menu_identifier"];
				}
				$this->call_command("DB_FREE",Array($getresult));
				if($parent!=-1){
					$sql = "select * from display_data where display_menu = $parent and display_command not in ('PRESENTATION_SEARCH','SITEMAP_DISPLAY')";
				} else {
					$sql = "select * from display_data
								inner join menu_data on menu_identifier = display_menu and display_client= menu_client
								where
									menu_url = 'index.php' and
									display_command not in ('PRESENTATION_SEARCH','SITEMAP_DISPLAY') and menu_client = $this->client_identifier ";
				}
				$result  = $this->parent->db_pointer->database_query($sql);
				if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
            	    while($r = $this->parent->db_pointer->database_fetch_array($result)){
						if ($r["display_command"]!="SITEMAP_DISPLAY" && $r["display_command"]!="PRESENTATION_SEARCH"){
    	    	        	$sql = "insert into display_data (display_command, display_menu, display_client) values ('".$r["display_command"]."',".$m_id.", $this->client_identifier)";
							$this->parent->db_pointer->database_query($sql);
						}
	                }
				} else {
       	        	$sql = "insert into display_data (display_command, display_menu, display_client) values ('PRESENTATION_DISPLAY',".$m_id.", $this->client_identifier)";
					$this->parent->db_pointer->database_query($sql);
				}
                $this->call_command("DB_FREE",Array($result));
				$sql = "select * from menu_channel_inheritance where mci_menu = $parent and mci_command not in ('PRESENTATION_SEARCH','SITEMAP_DISPLAY')";
				$result  = $this->parent->db_pointer->database_query($sql);
                while($r = $this->parent->db_pointer->database_fetch_array($result)){
                	$sql = "insert into menu_channel_inheritance (mci_command, mci_menu) values ('".$r["mci_command"]."', ".$m_id.")";
					$this->parent->db_pointer->database_query($sql);
                }
                $this->call_command("DB_FREE",Array($result));
				$session_management_access	= $this->check_parameters($_SESSION,"SESSION_MANAGEMENT_ACCESS",Array());
				if (count($session_management_access)>0){
					$_SESSION["SESSION_MANAGEMENT_ACCESS"][count($_SESSION["SESSION_MANAGEMENT_ACCESS"])] = $m_id;
				}
				$sql = "select distinct user_identifier from relate_user_menu where menu_identifier = $parent";
				$result  = $this->parent->db_pointer->database_query($sql);
                while($r = $this->parent->db_pointer->database_fetch_array($result)){
                  	$sql = "insert into relate_user_menu (user_identifier, menu_identifier) values (".$r["user_identifier"].", $m_id)";
					$this->parent->db_pointer->database_query($sql);
                }
                $this->call_command("DB_FREE",Array($result));

                $ok = $this->web_location("CREATE",$this->check_parameters($this->parent->site_directories,"ROOT")."/".$m_url);
				/*
                -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
                - save images to containers
                -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
                */
				if ($menu_images==0 || $menu_image_inherit==1){
					$fcontainer 	= $this->check_parameters($parameters,"file_attached_container");
					$fidentifiers	= Array();
					$flabels		= Array();
				} else {
					$fcontainer 	= $this->check_parameters($parameters,"file_attached_container");
					$fidentifiers	= $this->check_parameters($parameters,"file_attached_identifier");
					$flabels		= $this->check_parameters($parameters,"file_attached_label");
				}
				$m_fid = count($fidentifiers);
				for($i=0; $i<$m_fid;$i++){
					if($this->check_parameters($fidentifiers,$i)!=""){
						$this->call_command("FILES_MANAGE_MODULE",
							Array(
								"owner_module" 		=> "LAYOUT_".$fcontainer[$i],
								"label" 			=> $flabels[$i],
								"owner_id"			=> $m_id,
								"file_identifier"	=> $fidentifiers[$i]
							)
						);
					}
				}
                if ($ok==1){
                    $this->cache_menu_structure();
					return $m_id;
				}
			}else {
				/*
		        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        		- menu insert is from page add/edit
		        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		        */
				if ($command=="LAYOUT_ADD_MENU"){
					$count=0;
					$field_list ="";
					$value_list ="";
					if($table["menu_external"]==0){
						/*
						-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						- Directory
						-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						*/
						$table["menu_directory"] = -1;
						$dir_identifier="";
						if ($table["menu_parent"]!=-1){
							$sql ="select menu_directory from menu_data where menu_client=$this->client_identifier and menu_identifier=".$table["menu_parent"];
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
							}
							$result  = $this->parent->db_pointer->database_query($sql);
							while ($r= $this->parent->db_pointer->database_fetch_array($result)){
								$table["menu_directory"] = $r["menu_directory"];
							}
						}
						// if not admin/index.php then create directory entry
						if($administration==0){
							$params = Array();
							$params["directory_name"]=$table["menu_label"];
							$params["directory_identifier"]=$dir_identifier;
							$params["directory_parent"]=$table["menu_directory"];
							$params["directory_can_upload"] = "0";
							$this->save_directory($params);
							$sql ="select * from directory_data where directory_client=$this->client_identifier and directory_parent=".$table["menu_directory"]." and directory_name='".$this->make_uri($params["directory_name"])."'";
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
							}
							$result  = $this->parent->db_pointer->database_query($sql);
							//$directory_identifier = -1;
							while ($r= $this->parent->db_pointer->database_fetch_array($result)){
								$table["menu_directory"] = $r["directory_identifier"];
							}
			                $this->call_command("DB_FREE",Array($result));
							$table["menu_url"] = $this->retrieve_directory_path($table["menu_directory"])."index.php";
						}
					}
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- select the number of locations belonging to the parent Identifier and add
					- this as the last entry in the list
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					$sql = "select count(menu_parent) as total from menu_data where menu_parent=".$table["menu_parent"]." and menu_client=$this->client_identifier group by menu_parent";
					$result  = $this->parent->db_pointer->database_query($sql);
					$table["menu_order"] = 0;
					while ($r= $this->parent->db_pointer->database_fetch_array($result)){
						$table["menu_order"] = $r["total"];
					}
					$table["menu_order"]++;
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- build the sql statement to insert the user information
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					if($table["menu_external"]==1){
						unset($table["menu_parent"]);
					}
					reset($table);
					$request="";
					foreach ($table as $key => $val) {
						if (!(strpos($key,"menu_")===false)){
							if ($count>0){
								$field_list .= ", ";
								$value_list .= ", ";
								$request .= " and ";
							}
							$field_list .= $key;
							$value_list .= "'$val'";
							$request .= " $key ='$val'";
							$count++;
						}
					}
					$sql="insert into menu_data ($field_list) values ($value_list)";					
					$this->parent->db_pointer->database_query($sql);
					
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
					}
					$sql = "select * from menu_data where $request";
		//				print  $sql;
					$result  = $this->parent->db_pointer->database_query($sql);
					while ($r= $this->call_command("DB_FETCH_ARRAY", array($result))){
						$menu_identifier = $r["menu_identifier"];
					}
					/** Add a to z web-object if menu_stylesheet is 19(A to Z - Title only, single page) */					
					/* Start adding webobject */
					if ($table["menu_stylesheet"] == 19){
						//print "<li>".$table["menu_url"]."</li>";
						$this->call_command("PAGE_ADD_A2Z_ENTRIES",array("menu_url" => $table["menu_url"]));						
						$special_webobjects			= Array(
							"A2Z_WIDGET" => Array(
							"owner_module" 	=> $table["menu_url"],
							"label" 		=> "A to Z Widget (".$table["menu_label"].")",
							"wo_command"	=> "PRESENTATION_GET_A2Z",
							"file"			=> "_a2zwidget.php",
							"available"		=> 1,
							"type"			=> 2
							)
						);						
						$this->call_command("WEBOBJECTS_MANAGE_MODULE_WO",
							Array(
								"webobjects"	=> $special_webobjects,
								"owner_id" 		=> $menu_identifier,
								"starter"		=> $this->module_command,
								"label"			=> "A to Z Widget (".$table["menu_label"].")",
								"cmd"			=> "ADD"
							)
						);
						
					}	
					/* End adding webobject */									
					$menu_id = $menu_identifier;
					if($table["menu_external"]==0){
						$parameters["menu_identifier"] = $menu_identifier;
						if ($menu_inherit==1){
							$this->save_inherit_channels($parameters);
						} else {
							$this->save_channels($parameters);
						}
						$this->manage_inheritance(Array("menu_identifier" => $menu_id, "menu_parent" => $table["menu_parent"], "cmd"=>"ADD"));
						$this->save_groups($parameters);
						$this->rank_page($parameters);
						for( $index = 0, $max = count($commands_to_execute) ; $index < $max;$index++){
							$this->call_command($commands_to_execute[$index],$parameters);
						}
		//				$sql="insert into display_data (display_menu,display_client,display_command) values ('$menu_identifier','$this->client_identifier','PRESENTATION_DISPLAY');";
		//				$this->parent->db_pointer->database_query($sql);
						$this->call_command("WEBOBJECTS_INHERIT",Array("menu_identifier"=> $menu_id, "menu_parent"=>$table["menu_parent"]));
						$session_management_access	= $this->check_parameters($_SESSION,"SESSION_MANAGEMENT_ACCESS",Array());
						if (count($session_management_access)>0){
							$_SESSION["SESSION_MANAGEMENT_ACCESS"][count($_SESSION["SESSION_MANAGEMENT_ACCESS"])] = $menu_id;
						}
						$sql = "select distinct user_identifier from relate_user_menu where menu_identifier = ".$table["menu_parent"];
						$result  = $this->parent->db_pointer->database_query($sql);
	                    while($r = $this->parent->db_pointer->database_fetch_array($result)){
	                   		$sql = "insert into relate_user_menu (user_identifier, menu_identifier) values (".$r["user_identifier"].", $menu_id)";
							$this->parent->db_pointer->database_query($sql);
	           	        }
	       	            $this->call_command("DB_FREE",Array($result));
						$ok =$this->web_location("CREATE",$this->check_parameters($this->parent->site_directories,"ROOT")."/".$table["menu_url"]);

						/*
	    	            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		                - save images to containers
	                	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	            	    */
						$fcontainer 	= $this->check_parameters($parameters,"file_attached_container");
						$fidentifiers	= $this->check_parameters($parameters,"file_attached_identifier");
						$flabels		= $this->check_parameters($parameters,"file_attached_label");
						$m_fid = count($fidentifiers);
						for($i=0; $i<$m_fid;$i++){
							if($this->check_parameters($fidentifiers,$i)!=""){
								$this->call_command("FILES_MANAGE_MODULE",
									Array(
										"owner_module" 		=> "LAYOUT_".$fcontainer[$i],
										"label" 			=> $flabels[$i],
										"owner_id"			=> $menu_identifier,
										"file_identifier"	=> $fidentifiers[$i]
									)
								);
							}
						}
					} else {
						$ok=1;
					}
					if ($ok==1){
						$this->cache_menu_structure();
					}
				}
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if editting then we want to update the information in the database
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($command=="LAYOUT_EDIT_MENU"){
					$count=0;
					$field_list ="";
					$value_list ="";
					$menu_identifier  	= $this->check_parameters($parameters,"menu_identifier");
					$prev_menu_parent  	= $this->check_parameters($parameters,"prev_menu_parent");
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- if the previous menu parent is the same as the current then we are not
					- moving the location but we might be renaming
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					$renaming		= 0;
					$moving 		= 0;
					$dir_identifier = -1;
					$dir_name 		= "";
					if($administration==0){
						if($table["menu_external"]==0){
							$new_label		= $this->make_uri($table["menu_label"]);
							$sql ="select * from menu_data inner join directory_data on menu_directory = directory_identifier where menu_identifier = $menu_identifier";
							$result  = $this->parent->db_pointer->database_query($sql);
							if ($this->module_debug ){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
							}	
							while ($r = $this->parent->db_pointer->database_fetch_array($result)){
								$dir_identifier = $r["directory_identifier"];
								$dir_name 		= $r["directory_name"];
							}
							if ($prev_menu_parent != $table["menu_parent"]){
								$moving = 1;
							}
							if (($dir_name != $new_label ) && $dir_name != 'admin' && $dir_name!="" && $new_label!='home'){
								$renaming = 1;
							}
							$menu_directory = -1;
							if ($table["menu_parent"]!=-1){
								$sql = "select menu_directory from menu_data where menu_client=$this->client_identifier and menu_identifier=".$table["menu_parent"];
								if ($this->module_debug){
									$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
								}
								$result  = $this->parent->db_pointer->database_query($sql);
								$menu_directory=-1;
								while ($r= $this->parent->db_pointer->database_fetch_array($result)){
									$menu_directory = $r["menu_directory"];
								}
							}
							if ($renaming==1 || $moving==1){
								$old_path = $this->retrieve_directory_path($dir_identifier);
								$sql ="update directory_data set directory_name='$new_label', directory_parent='$menu_directory' where directory_identifier=$dir_identifier";
								if ($this->module_debug){
									$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
								}
								$this->parent->db_pointer->database_query($sql);
								/*
								-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
								- destroy the cached directories
								-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
								*/
								$this->directories = array();
								$new_path = $this->retrieve_directory_path($dir_identifier);
								$this->rebuild_menu_uris($old_path ,$new_path);
							}
							/*
							-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
							- build the sql statement to update the layout information
							-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
							*/
							if ($moving==1){
								$table["menu_order"]=$this->check_parameters($table,"menu_order",0);
								if ($table["menu_order"]==0){	
									$sql ="select count(menu_order) as total from menu_data where menu_client=$this->client_identifier and menu_parent=".$table["menu_parent"]." and menu_identifier != ".$menu_identifier." order by menu_parent";
									if ($this->module_debug){
										$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
									}
									$result  = $this->parent->db_pointer->database_query($sql);
									while ($r= $this->parent->db_pointer->database_fetch_array($result)){
										$table["menu_order"] = $r["total"];
									}
									$table["menu_order"]++;
								}
							}
							if ($renaming==1){
								$table["menu_url"] = $new_path."index.php";
							} else {
								unset($table["menu_url"]);
							}
						}
					} else {
						// if the admin menu location is being editted then unset the order ifentifier as we do not want to change this
						unset($table["menu_label"]);	
						unset($table["menu_order"]);	
					}
					reset($table);
					foreach ($table as $key => $val) {
						if (!(strpos($key,"menu_")===false)){
							if ($count>0){
								$field_list .= ", ";
							}
							$field_list .= $key;
							$field_list .= "= '$val'";
							$count++;
						}
					}

					$menu_id = $parameters["menu_identifier"];
					$sql="update menu_data set $field_list where menu_client=$this->client_identifier and menu_identifier=".$menu_id;
					/** Add a to z web-object if menu_stylesheet is 19(A to Z - Title only, single page) */					
					/* Start adding webobject */
					$special_webobjects			= Array(
						"A2Z_WIDGET" => Array(
						"owner_module" 	=> $table["menu_url"],
						"label" 		=> "A to Z Widget (".$table["menu_label"].")",
						"wo_command"	=> "PRESENTATION_GET_A2Z",
						"file"			=> "_a2zwidget.php",
						"available"		=> 1,
						"type"			=> 2
						)
					);						

					if ($table["menu_stylesheet"] == 19 && $parameters["prev_menu_stylesheet"] != 19){
						$this->call_command("PAGE_ADD_A2Z_ENTRIES",array("menu_url" => $table["menu_url"]));						
						$this->call_command("WEBOBJECTS_MANAGE_MODULE_WO",
							Array(
								"webobjects"	=> $special_webobjects,
								"owner_id" 		=> $menu_id,
								"starter"		=> $this->module_command,
								"label"			=> "A to Z Widget (".$table["menu_label"].")",
								"cmd"			=> "ADD"
							)
						);
						
					}	
					if ($table["menu_stylesheet"] == 19 && $parameters["prev_menu_stylesheet"] == 19){
						$this->call_command("WEBOBJECTS_MANAGE_MODULE_WO",
							Array(
								"webobjects"	=> $special_webobjects,
								"owner_id" 		=> $menu_id,
								"starter"		=> $this->module_command,
								"label"			=> "A to Z Widget (".$table["menu_label"].")",
								"cmd"			=> "UPDATE"
							)
						);
						
					}	
					
					if ($table["menu_stylesheet"] != 19 && $parameters["prev_menu_stylesheet"] == 19){	
						$this->call_command("PAGE_REMOVE_A2Z_ENTRIES",array("menu_url" => $table["menu_url"]));						
						$this->call_command("WEBOBJECTS_MANAGE_MODULE_WO",
							Array(
								"webobjects"	=> $special_webobjects,
								"owner_id" 		=> $menu_id,
								"starter"		=> $this->module_command,
								"label"			=> "A to Z Widget (".$table["menu_label"].")",
								"cmd"			=> "REMOVE"
							)
						);
					}	

					/* End adding webobject */									

					$this->parent->db_pointer->database_query($sql);
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
					}
					if($table["menu_external"]==0){
						if ($menu_change==1 || $dir_change==1){
							$this->call_command("EMBED_FIX_MENU" ,
								Array(
									"menu_identifier"	=> $menu_id,
									"menu_label"		=> strtolower($table["menu_label"]),
									"prev_menu_label"	=> strtolower($this->check_parameters($parameters,"prev_menu_label")),
									"menu_parent"		=> $table["menu_parent"],
									"prev_menu_parent"	=> $this->check_parameters($parameters,"prev_menu_parent",-1)
								)
							);
						}
						/*
	                    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	                    - use the menu to object table to store headline locations
	                    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	                    */
						$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE",
							Array(
								"menu_locations" => $this->check_parameters($parameters,"menu_headline_locations",Array()),
								"module"		 => "LAYOUT_",
								"identifier"	 => $menu_id
							)
						);
						/*
							remove any files in this directory.
						*/
						$parameters["menu_identifier"]=$menu_id;
						if ($menu_inherit==1){
							$this->save_inherit_channels($parameters);
						} else {
							$this->save_channels($parameters);
						}
		//				$this->save_channels($parameters);
						$this->save_groups($parameters);
						$this->rank_page($parameters);
						
						/*
	    	            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		                - save images to containers
	                	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	            	    */
						$fcontainer 	= $this->check_parameters($parameters,"file_attached_container");
						$fidentifiers	= $this->check_parameters($parameters,"file_attached_identifier");
						$flabels		= $this->check_parameters($parameters,"file_attached_label");
						$m_fid = count($fcontainer);
						for($i=0; $i<$m_fid;$i++){
							$this->call_command("FILES_MANAGE_MODULE", 
								Array(
									"owner_module" 		=> "LAYOUT_".$fcontainer[$i],
									"label" 			=> $flabels[$i],
									"owner_id"			=> $menu_identifier,
									"file_identifier"	=> $fidentifiers[$i]
								)
							);
						}
						for( $index = 0, $max = count($commands_to_execute) ; $index < $max;$index++){
							$this->call_command($commands_to_execute[$index],$parameters);
						}
						
						if (strlen($old_path)>0){
							$_SESSION["ENGINE_RESTORE_REDIRECT"] = "command=LAYOUT_LIST_MENU&folder=".$table["menu_parent"];
							$this->restore($menu_id,$old_path,$new_path);
						} else {
							$this->cache_menu_structure();
						}
					} else {
						$this->cache_menu_structure();
					}
				}
				if($table["menu_external"]==0){
					if ($prev_menu_stylesheet != $table["menu_stylesheet"]){
						$sql = "SELECT 
									theme_type_identifier, 
									theme_type_command, 
									menu_url FROM theme_types
										left outer  join  menu_data on menu_stylesheet = theme_type_identifier 
									where ((menu_data.menu_client=$this->client_identifier and menu_identifier = $menu_id )or menu_identifier is null) and (theme_type_identifier = $prev_menu_stylesheet or theme_type_identifier =".$table["menu_stylesheet"].")";
					} else {
						$sql = "SELECT 
									theme_type_identifier, 
									theme_type_command, 
									menu_url
								FROM menu_data 
									inner join theme_types on menu_stylesheet = theme_type_identifier 
								where 
									menu_data.menu_client=$this->client_identifier and 
									menu_identifier = $menu_id and 
									theme_type_identifier =".$table["menu_stylesheet"];
					}
					$result  = $this->parent->db_pointer->database_query($sql);
					$cmd = "";
					$pcmd= "";
					$menu_url="";
					$pmenu_url="";
		           	while($r = $this->parent->db_pointer->database_fetch_array($result)){
						if ($table["menu_stylesheet"]==$r["theme_type_identifier"]){
			           		$cmd		= $r["theme_type_command"];
							$menu_url	= $r["menu_url"];
						}
						if ($prev_menu_stylesheet==$r["theme_type_identifier"]){
			           		$pcmd		= $r["theme_type_command"];
							$pmenu_url	= $r["menu_url"];
						}
		   	        }
		       	    $this->call_command("DB_FREE",Array($result));
		       	    /** menu_stylesheet execute themes command */
					if ($cmd != "PRESENTATION_DISPLAY" || $pcmd != "PRESENTATION_DISPLAY"){
						if ($prev_menu_stylesheet != $table["menu_stylesheet"]){
							$this->call_command($pcmd."_REMOVE",Array("menu_url"=>"$menu_url"));
						}
						if ($cmd != "PRESENTATION_DISPLAY"){
							$this->call_command($cmd."_ADD",Array("menu_url"=>"$menu_url"));
						}
					}
					$mlist = Array();
					$mlist[0] = $menu_id;
					$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACTOR_UPDATE", 
						Array(
							"menu_list" => $mlist
						)
					);
				}
				return $menu_id;
			}
		} else {
			if ($command=="LAYOUT_MENU_INSERT"){
				return -1;
			} else {
				$list  = "&amp;menu_identifier=".$this->check_parameters($parameters,"menu_identifier");
				$list .= "&amp;menu_stylesheet=".$this->check_parameters($parameters,"menu_stylesheet");
				$list .= "&amp;menu_label=".$table["menu_label"];
				$list .= "&amp;menu_parent=".$table["menu_parent"];
				$list .= "&amp;menu_sort=".$table["menu_sort"];
				$list .= "&amp;menu_order=".$table["menu_order"];
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$command."_REDO$list"));
				return -99;
			}
		}
	}
	

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Restore the directory structure and re-cache any files required
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function restore($menu_id=-1,$old="",$new =""){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"$menu_id, $old, $new"));}
		$num_occurs = count($this->directories);
		if ($num_occurs==0){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if there are no specified directories then check to see if the array has been filled.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$this->get_directories();
			$num_occurs = count($this->directories);
		}

		$path_to_use=$this->check_parameters($this->parent->site_directories,"ROOT");
		$this->make_new_dir(-1,$path_to_use);
		$this->web_location("CREATE", $path_to_use."/index.php");
		$this->load_menu();
		$list ="";
		
		for ($index=0,$num_occurs=count($this->menu_structure);$index<$num_occurs;$index++){
			$d=$path_to_use;
			
			$ds = split("/",$this->menu_structure[$index]["URL"]);
			for ($i =0; $i< count($ds)-1;$i++){
				$d .= "/".$ds[$i];
			}
			if (($this->menu_structure[$index]["URL"]!="admin/index.php") && ($this->menu_structure[$index]["EXTERNAL"]!=1)){
				$this->make_new_dir($d);
				$this->web_location("CREATE", $d."/index.php");
				if ($this->menu_structure[$index]["IDENTIFIER"] == $menu_id){
					$list = $this->restore_dir($menu_id,$index);
				}
			}
		}
		$_SESSION["RECACHE"] = $list;
		$this->generate_robots_txt_file();
		$this->cache_menu_structure();
		if ($old!=""){
			$_SESSION["redirect_on_recache"] ="LAYOUT_LIST_MENU";
			if ($new!=""){
				$this->call_command("FILES_MOVE_TO_MENU", array("menu_locations"=>$list,"old_path"=>$old,"new_path"=>$new));
				$this->call_command("LAYOUT_REMOVE_TREE",Array("path"=>$old));
			}
			$this->call_command("PAGE_REGENERATE_CACHE");
			$this->exitprogram();
		} else {
			if ($this->check_parameters($_SESSION,"INSTALL_RESTORE","0")=="0"){
				$this->call_command("ENGINE_RESTORE");
				$this->exitprogram();
			}
		}

	}
	function restore_dir($menu_id,$index){
		$str="";
		$path_to_use=$this->check_parameters($this->parent->site_directories,"ROOT");
		$this->web_location("CREATE", $path_to_use."/".$this->menu_structure[$index]["URL"]);
		for ($index=0,$num_occurs=count($this->menu_structure);$index<$num_occurs;$index++){
			if (strpos($path_to_use.$this->menu_structure[$index]["URL"],"admin")){
			}else{
				if ($this->menu_structure[$index]["PARENT"] == $menu_id){
					$str .= $this->restore_dir($this->menu_structure[$index]["IDENTIFIER"],$index);
				}
			}
		}
		return $menu_id .",". $str;
	}
	
	function restore_directories(){
		$sql = "select directory_identifier, directory_parent, directory_name from directory_data left outer join menu_data on menu_directory = directory_identifier where menu_identifier is null and directory_client = $this->client_identifier order by directory_parent";
		$root=$this->check_parameters($this->parent->site_directories,"ROOT");
		$dir_result = $this->parent->db_pointer->database_query($sql);
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($dir_result))) {
			$path = join("\\",split("/",$root."/".$this->retrieve_directory_path($r["directory_identifier"])))."index.php";
			if (!file_exists($path)){
				$um =umask(0);
				@mkdir(dirname($path),LS__DIR_PERMISSION);
				umask($um);
			}
			$this->web_location("CREATE", $path);
		}
		$this->call_command("DB_FREE", array($dir_result));
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- make a directory functionality
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function make_dir($parent=-1,$current_path){
		$num_occurs=count($this->directories);
		for ($index=0;$index<$num_occurs;$index++){
			if ($this->directories[$index]["PARENT"]==$parent){
				if (!file_exists($current_path."/".$this->directories[$index]["NAME"]."/.")){
					$um =umask(0);
					@mkdir($current_path."/".$this->directories[$index]["NAME"], LS__DIR_PERMISSION); // or even 01777 so you get the sticky bit set
					umask($um);
					$this->web_location("CREATE", $current_path."/".$this->directories[$index]["NAME"]."/index.php");
				}
				$this->make_dir($this->directories[$index]["IDENTIFIER"],$current_path."/".$this->directories[$index]["NAME"]);
			}
		}

	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- make a single directory
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function make_new_dir($path){
		if (!file_exists($path)){
			$um =umask(0);
			@mkdir($path, LS__DIR_PERMISSION); // or even 01777 so you get the sticky bit set
			umask($um);
		}
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Function to retrieve the path of a specified directory.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function retrieve_directory_path($id=-2){
		$out ="";
		if ($id>-1){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if there are no specified directories then check to see if the array has been filled.
			- causes the array being used to be built only once in this recursive function call.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$num_occurs = count($this->directories);
			if ($num_occurs==0){
				$this->get_directories();
				$num_occurs = count($this->directories);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- get the path.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			for ($index=0;$index<$num_occurs;$index++){
				if ($this->directories[$index]["IDENTIFIER"] == $id){
					$out .= $this->retrieve_directory_path($this->directories[$index]["PARENT"]).$this->directories[$index]["NAME"]."/";
				}
			}
		} else {
			$out="";
		}
		return $out;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Function to retrieve the path of a specified directory. from the menu id
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function retrieve_directory_path_from_menu($id=-2){
		$this->directories=Array();
		$num_occurs = count($this->menu_structure);
		if ($num_occurs==0){
			$this->load_menu();
			$num_occurs = count($this->menu_structure);
		}
		for($i=0;$i<$num_occurs;$i++){
			if ($id == $this->menu_structure[$i]["IDENTIFIER"]){
				return $this->retrieve_directory_path($this->menu_structure[$i]["DIRECTORY"]);
			}
		}
		return "";
	}
	
	function retrieve_bc_trail($parameters){
		$id 		= $this->check_parameters($parameters,"id",-2);
		$qstring	= $this->check_parameters($parameters,"qstring");
		$splitter	= $this->check_parameters($parameters,"splitter"," [[rightarrow]] ");

		$out ="";
		if ($id>-1){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if there are no specified directories then check to see if the array has been filled.
			- causes the array being used to be built only once in this recursive function call.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$this->displayed="";
			$num_occurs = count($this->menu_structure);
			if ($num_occurs==0){
				$this->load_menu();
				$num_occurs = count($this->menu_structure);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- get the path.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			for ($index=0;$index<$num_occurs;$index++){
				if ($this->menu_structure[$index]["IDENTIFIER"] == $id){
					if ($qstring==""){
						$out .= $this->retrieve_bc_trail(Array("id"=>$this->menu_structure[$index]["PARENT"], "splitter"=>$splitter))."$splitter".$this->menu_structure[$index]["LABEL"];
					} else {
						$out .= $this->retrieve_bc_trail(Array("id"=>$this->menu_structure[$index]["PARENT"], "splitter"=>$splitter, "qstring"=>$qstring))."$splitter <a href='".$this->parent->script."?".$qstring."".$this->menu_structure[$index]["IDENTIFIER"]."'>".$this->menu_structure[$index]["LABEL"]."</a>";
					}
				}
			}
		} else {
			$out="";
		}
//		print $out;
		return $out;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Function builds up XML representation of the menu structure for the XSLT to translate
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function display_level($level,$depth,$location_identifier,$restricted=0){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* This function will display the Array of menu data in a html representation that will be
		* used for display purposes on the website.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_level",__LINE__,"prams=$level, $depth, $location_identifier, $restricted"));
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Global variables that are updated in this function
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/

		$outtext="";
		$user_access = $this->check_parameters($_SESSION,"SESSION_GROUP_IDENTIFIER");
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_level",__LINE__,count($this->menu_structure)." "));
		}
		for ($index=0,$length=count($this->menu_structure);$index<$length;$index++){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if the parent identifier of the entry is equal to the menu location that we are looking to
			- display then we will display the menu
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if (($pos = strpos($this->displayed," $index,"))===false){
				if ($this->menu_structure[$index]["PARENT"].""==$level."") {
					$accesslength=count($this->menu_structure[$index]["GROUPS"]);
					$ok=0;
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_level",__LINE__,"session_user_access=$user_access,a: $accesslength,r: $restricted"));
					}
					if (($accesslength==0) || ($restricted==0)){
						$ok=1;
					}else{
						if ($restricted==2){
							$max = count($this->menu_structure[$index]["GROUPS"]);
							if ($max>0){
								$ok=1;
								for($i=0;$i<$max;$i++){
									if (strlen($this->check_parameters($this->menu_structure[$index]["GROUPS"],$i))>0){
										$ok=0;
									}
								}
							}else{
								$ok=1;
							}
						}else{
							for ($access_index=0;$access_index<$accesslength;$access_index++){
								if ($this->menu_structure[$index]["GROUPS"][$access_index]==$user_access){
									$ok=1;
								}
							}
						}
					}
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"ok",__LINE__," [".$ok."]"));
					}
					if($ok){
						$this->displayed .= " ".$index.",";
						$outtext	.=	"\n\t\t\t";
						for($depth_index=0;$depth_index<$depth;$depth_index++){
							$outtext	.=	"\t";
						}
						$outtext	.=	"<menu ";
						if(count($this->keylist)>0){
							if (in_array($this->menu_structure[$index]["URL"], $this->keylist)){
								foreach ($this->keylist as $key => $value){
									if ($value == $this->menu_structure[$index]["URL"]){
										$outtext	.=	"accesskey=\"".$key."\" ";
									}
								}
							}
						}
						$ext = $this->menu_structure[$index]["EXTERNAL"];
						$outtext	.=	"identifier = \"".$this->menu_structure[$index]["IDENTIFIER"]."\" ";
						$outtext	.=	"parent = \"".$this->menu_structure[$index]["PARENT"]."\" ";
						$outtext	.=	"depth = \"$depth\" ";
						$outtext 	.=	"order = \"".$this->menu_structure[$index]["ORDER"]."\" ";
						$outtext 	.=	"children = \"".$this->menu_structure[$index]["CHILDREN"]."\" ";
						$outtext 	.=	"siblings = \"".$this->menu_structure[$index]["SIBLINGS"]."\" ";
						$outtext 	.=	"stylesheet = \"".$this->menu_structure[$index]["STYLESHEET"]."\" ";
						$outtext 	.=	"theme = \"".$this->menu_structure[$index]["THEME"]."\" ";
						$outtext 	.=	"hidden = \"".$this->menu_structure[$index]["HIDDEN"]."\" ";
						$outtext 	.=	"summaryImgDisplay = \"".$this->menu_structure[$index]["SUMMARYIMAGEDISPLAY"]."\" ";
						$outtext 	.=	"title_page = \"".$this->check_parameters($this->title_pages,$this->menu_structure[$index]["IDENTIFIER"],"0")."\" ";
						$outtext	.=	">\n";
						if ($this->menu_structure[$index]["ARCHIVE"]==1){
							$outtext	.=	"<archive display='".$this->menu_structure[$index]["ARCHIVE_DISPLAY"]."' access='".$this->menu_structure[$index]["ARCHIVE_ACCESS"]."' />";
						}
						$outtext	.=	"<alt_text><![CDATA[".$this->validate($this->menu_structure[$index]["ALT_TEXT"])."]]></alt_text>";
						$outtext	.=	"<url external='$ext'><![CDATA[".$this->validate($this->menu_structure[$index]["URL"])."]]></url>";
						$outtext	.=	"<label><![CDATA[".htmlentities($this->validate($this->convert_amps(join("[[copy]]",split("&#169;",$this->menu_structure[$index]["LABEL"])))))."]]></label>";
						if($restricted!=2){
							$outtext 	.=	"<sort><![CDATA[".$this->validate($this->menu_structure[$index]["SORT"])."]]></sort>";
						}
						$outtext	.=	"<display_options>";
						$max = count($this->menu_structure[$index]["DISPLAY_OPTIONS"]);
						$keepArray = Array("PRESENTATION_SEARCH", "SITEMAP_DISPLAY");
						for($display_index=0;$display_index<$max;$display_index++){
							if(in_array($this->menu_structure[$index]["DISPLAY_OPTIONS"][$display_index],$keepArray)){
								$outtext	.=	"<display>".$this->menu_structure[$index]["DISPLAY_OPTIONS"][$display_index]."</display>";
							}
						}
						$outtext	.=	"</display_options>";
						
						/*
						-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						- retrieve the groups that are related to this location through a relationship
						-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						*/
						if($restricted!=2){
							$outtext	.= "<groups>";
							
							$accesslength=count($this->menu_structure[$index]["GROUPS"]);
							for ($access_index=0;$access_index<$accesslength;$access_index++){
								if (strlen($this->menu_structure[$index]["GROUPS"][$access_index])>0){
									$outtext .= "<option value=\"".$this->menu_structure[$index]["GROUPS"][$access_index]."\">".$this->menu_structure[$index]["GROUPS"][$access_index]."</option>";
								}
							}
							
							$outtext .= "</groups>\n";
						}
						/*
						-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						- Call this function again requesting a html representation of the sub levels of the menu
						-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						*/
						$outtext	.= "<children>".$this->display_level($this->menu_structure[$index]["IDENTIFIER"],$depth+1,$location_identifier,$restricted)."</children>\n";
						if ($this->max_depth<$depth){
							$this->max_depth=$depth;
						}
						$outtext	.= "\t\t\t\t</menu>";
					}else{
						$this->displayed .= " ".$index.",";
					}
				}
			}
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Return this html representation  to the calling function that will allow the concatation of
		* the html to take place.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
				
		return $outtext;
		
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- cache the anonymous menu locations
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function cache_menu_structure(){
		$this->load_menu();
		$this->load_title_page_ids();
		$this->keylist = $this->call_command("ACCESSKEYADMIN_GET_LIST");
		if ($this->keylist==""){
			$this->keylist=Array();
		}

		$this->displayed="";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"cache_menu_structure",__LINE__,$data_files."/layout_".$this->client_identifier."_anonymous.xml"));
		}

		$file_to_use=$data_files."/layout_".$this->client_identifier."_anonymous.xml";
		$fp = fopen($file_to_use,"w");
		$out = $this->display_level(-1, 1, 0, 2);
		fwrite($fp, $out);
		fclose($fp);
		$old_umask = umask(0);
		@chmod($file_to_use,LS__FILE_PERMISSION);
		umask($old_umask);
		$this->displayed="";
		if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"cache_menu_structure",__LINE__,$data_files."/layout_".$this->client_identifier."_restricted.xml"));
		}
		$file_to_use=$data_files."/layout_".$this->client_identifier."_restricted.xml";
		$fp = fopen($file_to_use,"w");
		$out = $this->display_level(-1, 1, 0, 0);
		fwrite($fp, $out);
		fclose($fp);

		$old_umask = umask(0);
		@chmod($file_to_use,LS__FILE_PERMISSION);
		umask($old_umask);
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Load the menu structure into the array only if the array is empty ie do not recreate the 
	- menu structure. bar making the code faster this will reduce the possibilities of load twice
	- the information into the array.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function load_menu(){
		if (count($this->menu_structure)==0){

			$this->menu_structure = array();
			$sql_menus = "select 
				menu_data.*, menu_sort.*, relate_menu_groups.group_identifier, display_data.display_command
			from menu_data 
				inner join menu_sort on menu_data.menu_sort = menu_sort.menu_sort_identifier 
				left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier
				left outer join display_data on display_data.display_menu = menu_data.menu_identifier
			where 
				menu_client=$this->client_identifier
			order by 
				menu_data.menu_parent, 
				menu_data.menu_order, 
				menu_data.menu_identifier, 
				relate_menu_groups.group_identifier, 
				display_data.display_command";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_menu",__LINE__,"$sql_menus"));
			}
			$menu_result = $this->call_command("DB_QUERY",array($sql_menus));
			$prev_menu=-1;
			$pos=-1;
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($menu_result))) {
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_menu",__LINE__,"Adding entries to arrays. index=".count($this->menu_structure)));
				}
				if ($prev_menu!=$r["menu_identifier"]){
					$prev_menu=$r["menu_identifier"];
					$pos =count($this->menu_structure);
					$this->menu_structure[$pos]=array(
						"LABEL" 				=> $this->convert_amps($r["menu_label"]),
						"ORDER" 				=> $r["menu_order"],
						"URL" 					=> $this->convert_amps($r["menu_url"]),
						"IDENTIFIER"			=> $r["menu_identifier"],
						"PARENT" 				=> $r["menu_parent"],
						"SORT" 					=> $r["menu_sort_tag_value"],
						"CHILDREN" 				=> 0,
						"SIBLINGS" 				=> 0,
						"THEME" 				=> $r["menu_theme"],
						"STYLESHEET"			=> $r["menu_stylesheet"],
						"GROUPS" 				=> Array(),
						"DISPLAY_OPTIONS"		=> Array(),
						"DIRECTORY"				=> $r["menu_directory"],
						"HIDDEN"				=> $this->check_parameters($r,"menu_hidden"				, 0),
						"ALT_TEXT"				=> $this->check_parameters($r,"menu_alt_text"			, ""),
						"SUMMARYIMAGEDISPLAY"	=> $this->check_parameters($r,"menu_image_display"		, "0"),
						"EXTERNAL"				=> $this->check_parameters($r,"menu_external"			, "0"),
						"ARCHIVE"				=> $this->check_parameters($r,"menu_archiving"			, "0"),
						"ARCHIVE_DISPLAY"		=> $this->check_parameters($r,"menu_archive_display"	, "0"),
						"ARCHIVE_ACCESS"		=> $this->check_parameters($r,"menu_archive_access"		, "0")
					);
					$prev_group=-1;
					$prev_cmd=-1;
				}
				$g_id = $this->check_parameters($r,"group_identifier");
				if ($prev_group!=$g_id){
					$this->menu_structure[$pos]["GROUPS"][count($this->menu_structure[$pos]["GROUPS"])]=$g_id;
					$prev_group=$g_id;
				}
				$dis_cmd = $this->check_parameters($r,"display_command");
				if ($prev_cmd!=$dis_cmd && !(in_array($dis_cmd,$this->menu_structure[$pos]["DISPLAY_OPTIONS"]))){
					$this->menu_structure[$pos]["DISPLAY_OPTIONS"][count($this->menu_structure[$pos]["DISPLAY_OPTIONS"])]=$dis_cmd;
					$prev_cmd=$dis_cmd;
				}
				
			}
			$this->call_command("DB_FREE",array($menu_result));
			$length_of_array = count($this->menu_structure);
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_menu",__LINE__,"number of entries =".$length_of_array));
			}
			for($index=0;$index<$length_of_array;$index++){
				$this->menu_structure[$index]["CHILDREN"]=0;
				$this->menu_structure[$index]["SIBLINGS"]=0;
				for($second_index=0;$second_index<$length_of_array;$second_index++){
					if ($this->menu_structure[$second_index]["PARENT"]==$this->menu_structure[$index]["IDENTIFIER"]){
						$this->menu_structure[$index]["CHILDREN"]++;
					}
					if (($this->menu_structure[$second_index]["PARENT"]==$this->menu_structure[$index]["PARENT"])&&($this->menu_structure[$second_index]["IDENTIFIER"]!=$this->menu_structure[$index]["IDENTIFIER"])){
						$this->menu_structure[$index]["SIBLINGS"]++;
					}
				}
			}		
		}
		return $this->menu_structure;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Generate the robots text file for the site.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function generate_robots_txt_file(){
		$sql = "select * from directory_data where directory_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$disallow="";
		$disallow .="Disallow:/-/\r\n";
		while ($r = $this->parent->db_pointer->database_fetch_array($result)) {
			if ($r["directory_can_spider"]==0){
				$disallow .="Disallow:/".$this->retrieve_directory_path($r["directory_identifier"])."\r\n";
			} else {
//				$disallow .="Allow:/".$this->retrieve_directory_path($r["directory_identifier"])."\r\n";
			}
		}
		$date = $this->libertasGetDate("Y/m/d H:i:s");
		$out ="# -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
#	Robots.txt file for informing search engines what directories can NOT be searched
# -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
# Last modified [$date] (date format YYYY/MM/DD HH:MM:SS if you are intrested)
User-agent: *
$disallow

User-agent: Googlebot-Image
Disallow:
";
	
		$root = $this->parent->site_directories["ROOT"];
		$fp = fopen($root."/robots.txt","w");
		fputs($fp,$out);
		fclose($fp);
		$um = umask(0);
		@chmod($root."/robots.txt", LS__FILE_PERMISSION);
		umask($um);
	}
	
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- generate the file that will represent the menu location.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function web_location($cmd, $src_file, $orginal_file=""){
		$src_file = join("/",split("\\\\",$src_file));
		$root = $this->check_parameters($this->parent->site_directories,"ROOT");
		if($cmd == "MOVE"){
			@unlink($orginal_file);
			$cmd = "CREATE";
		}
		if($cmd == "CREATE"){
			$dir = substr_replace(dirname($src_file), '', 0, strlen($root)) ;
			$directories = split('/',$dir);
			$directorycount = count($directories);
			$directory_to_root="";
			$parent_directory="";
			for($index=0;$index<$directorycount-1;$index++){
				$directory_to_root.="../";
				$parent_directory .= $directories[$index]."/";
			}
			$ok =1;
			if ($parent_directory!="/"){
				if (!file_exists($root.$parent_directory."/.")){
					$old_umask = umask(0);
					$ok = @mkdir($root.$parent_directory, LS__FILE_PERMISSION);					
					umask($old_umask);
				}
			}
			if (!file_exists(dirname($src_file)."/.")){
					$old_umask = umask(0);
					$ok = @mkdir(dirname($src_file), LS__FILE_PERMISSION);
					umask($old_umask);					
			}
			
			$um =umask(0);
			@chmod(dirname($src_file), LS__DIR_PERMISSION);
			umask($um);
			if (file_exists($src_file)){
				$um =umask(0);
				@chmod($src_file, LS__FILE_PERMISSION);
				umask($um);
			}
			if ($ok==1){
				$fp = fopen($src_file,"w");
				fputs($fp,"<"."?php\nrequire_once \"".$root."/admin/include.php\"; \r\nrequire_once \"\$module_directory/included_page.php\";\n?".">");
				fclose($fp);
				$um = umask(0);
				@chmod($file, LS__FILE_PERMISSION);
				umask($um);
			} else {
				$dmn = $this->parent->domain;
				$from = "info@".$this->parseDomain($this->parent->domain);
				$this->call_command("EMAIL_QUICK_SEND",
					Array(
						"from"		=> $from,
						"to"		=> "support@libertas-solutions.com",
						"subject"	=> "Attempted creation of menu location failed on [".$this->parent->domain."]",
						"body"		=> "
Please note that this client tried to: 

**** create a new menu location ****

But had an error from within the system 

The following url was used in the attempt to create the web location
$src_file"
					)
				);
				$_SESSION["MENU_CREATION_ERROR"] ="Im sorry we had an error trying to create the menu location[[return]]Libertas Solutions has been emailed to this problem.[[return]]Please call to confirm that Libertas Solutions is aware of the problem and that we are working on it.";
			}
			return $ok;
		}
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Change the order of the menu options
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function changeorder($MenuID,$Parent,$pos=1){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"changeorder",__LINE__,"[".$MenuID.",".$Parent."]"));
		}
		$client = $this->client_identifier;
		$sql = "select * from menu_data where menu_client=$client and menu_parent = '$Parent' and menu_identifier!=$MenuID order by menu_order";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[".$sql."]"));
		}
		$result  = $this->parent->db_pointer->database_query($sql);
		$sql = array();
		$counter=1;
		while ($r = $this->parent->db_pointer->database_fetch_array($result)) {
			if ($pos == $counter)
				$counter++;
			$sql[count($sql)] = "update menu_data set menu_order=$counter where menu_identifier=".$r["menu_identifier"]." and menu_client=$client";
			$counter++;
		}
		$sql[count($sql)] = "update menu_data set menu_order=$pos where menu_identifier=".$MenuID." and menu_client=$client and menu_parent = '$Parent'";		
		for ($index=0,$len=count($sql);$index<$len;$index++){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[".$sql[$index]."]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql[$index]));
		}
		$this->cache_menu_structure();
	}

	/**
	* retrieve_list_all_menu_locations_indent_value()
	*
	* This function will return all of the menu locations available to this client.
	- The results will be indented as a tree structure
	*/
	function retrieve_list_all_menu_locations_indent_value($choice,$menu_identifier=-1,$depth=0,$parameters){
		$parameters["use_useraccess_restrictions"]="YES";
		$out  = "<module name=\"$this->module_name\" display=\"form\"><form name='associated_form' label=\"".ENTRY_SELECT_MENU_LOCATIONS."\" method=\"GET\">\t\t\t\n";
		$out .= "<input type='hidden' name='command' value='LAYOUT_RETRIEVE_LIST_MENU_OPTIONS_DETAIL'/>";
		$out .= "<input type='hidden' name='associated_list' value='".$this->check_parameters($parameters,"associated_list")."'/>";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"return_hidden\" value=\"".$this->check_parameters($parameters,"return_hidden")."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"return_note\" value=\"".$this->check_parameters($parameters,"return_note")."\"/>\n";
		$menus		 	= $this->check_parameters($parameters,"page_menu_locations","");
		$groups			= $this->check_parameters($parameters,"page_groups","");
		$len = join("",split(" ",$menus));
		$menu_access  = split(",",$len);
		$out .= "<input type='hidden' name='page_groups'><![CDATA[$groups]]></input>";
		$out .= "\n<checkboxes type=\"vertical\" name='file_list' label=\"".ENTRY_CHOOSE_MENU_LOCATIONS."\"><options>";
		$out .= $this->list_all_menu_locations_indent_value($menu_access,$menu_identifier,$depth,$parameters);
		$out .= "</options></checkboxes><input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/></form></module>";
		return $out;
	}
	/**
	* retrieve_list_all_menu_locations_indent_value_detail()
	*/
	function retrieve_list_all_menu_locations_indent_value_detail($parameters){
		$publish_option_list 	= $this->check_parameters($parameters,"file_list",Array("-2"));
		$return_note		= $this->check_parameters($parameters,"return_note",-1);
		$return_hidden		= $this->check_parameters($parameters,"return_hidden",-1);
		//$menus		 	= $this->check_parameters($parameters,"page_menu_locations","");
		$groups		 	= $this->check_parameters($parameters,"page_groups","");
		if ($groups!=""){
		$len = join("",split(" ",$groups));
			$group_access  = split(",",$len);
		}else{
			$group_access = array();
		}
		$max=count($group_access);
		$str = join(", ",$publish_option_list);
		$sql = "select menu_data.*, group_data.group_identifier,group_label from menu_data 
left outer join relate_menu_groups on menu_data.menu_identifier = relate_menu_groups.menu_identifier 
left outer join group_data on group_data.group_identifier = relate_menu_groups.group_identifier 
where menu_data.menu_identifier in ($str, -1) and menu_data.menu_client= $this->client_identifier 
order by menu_data.menu_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);

		$out  = "<module name=\"$this->module_name\" display=\"list\"><bread_crumbs>";
		//$prev_group = "";
		$prev_menu ="";
		$g= array();
		$group_list_selected = "selected=\"true\"";
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){

			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				if ($prev_menu!=$r["menu_identifier"]){
					if ($prev_menu!=""){
						$out .="</groups></menu>";
					}
					$out .="<menu identifier=\"".$r["menu_identifier"]."\">
					<label><![CDATA[".htmlentities($r["menu_label"])."]]></label>\n<url><![CDATA[".$r["menu_url"]."]]></url>
					<groups>";
					$prev_menu=$r["menu_identifier"];
				}
				if (!empty($r["group_identifier"])){
					if ($max==0){
						$group_list_selected = "selected=\"true\"";
						$selected = "";
					}else{
						$group_list_selected = "";
						$selected = "";
						for($index=0;$index<$max;$index++){
							if($group_access[$index]==$r["group_identifier"]){
								$selected = "selected=\"true\"";
							}
						}
					}
					$out .= "<group identifier=\"".$r["group_identifier"]."\"/>";
					$g[$r["group_identifier"]]="<group_name identifier=\"".$r["group_identifier"]."\" $selected><![CDATA[".$r["group_label"]."]]></group_name>";
				}
			}
			$out .= "</groups></menu>";
		}
		$glist = join("",$g);
		$out .= "<group_list $group_list_selected>".$glist."</group_list></bread_crumbs><menu_structure>";
		$this->load_menu();
		$out .= $this->display_level(-1,1,-1,0);
		$out .= "</menu_structure>";
		$out .= "<hidden><![CDATA[$return_hidden]]></hidden>";
		$out .= "<note><![CDATA[$return_note]]></note>";
		$out .= "</module>";
		return $out;
	}
	/**
	* list_all_menu_locations_indent_value
	*
	* This function will return all of the menu locations available to this client.
	- The results will be indented as a tree structure
	*/
	function list_all_menu_locations_indent_value($choice,$menu_identifier=-1,$depth=0,$parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations_indent_value",__LINE__,$choice."[".count($choice).", $menu_identifier, $depth"));
		}
		$channels 						= $this->check_parameters($parameters,"channels",Array());
		$hide_locations					= $this->check_parameters($parameters,"hide_locations","-2");
		$use_useraccess_restrictions	= $this->check_parameters($parameters,"use_useraccess_restrictions","NO");
		$user_restricted_locations		= $this->check_parameters($_SESSION,"SESSION_MANAGEMENT_ACCESS");
		$add_parent						= $this->check_parameters($parameters,"add_parent","NO");
		$can_restrict_home_page			= $this->check_parameters($parameters,"can_restrict_home_page","0");
		$can_restrict_admin			= $this->check_parameters($parameters,"can_restrict_admin","1");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"list_all_menu_locations_indent_value",__LINE__,"[".count($channels)."]"));
		}
		$outtext = "";
		$this->displayed="";
		if ($menu_identifier==-1){
			$this->load_menu();
		}
		if (!is_array($choice)){
			$choice=Array($choice);
		}
		if (!is_array($hide_locations)){
			$hide_locations=array($hide_locations);
		}
		$max_hide = count($hide_locations);
		$disabled=0;
		$number_of_menus=count($this->menu_structure) ;
		for ($index=0; $index < $number_of_menus ; $index++){
			$show_level_and_children=true;
			for ($i=0;$i<$max_hide;$i++){
				if ($this->menu_structure[$index]["IDENTIFIER"]==$hide_locations[$i]){
					$show_level_and_children=false;
				}
			}
			if (($this->menu_structure[$index]["PARENT"]==$menu_identifier) && ($show_level_and_children) && ($this->menu_structure[$index]["EXTERNAL"]==0)){
				if ($can_restrict_admin==0){
					$outtext .= "\t\t\t\t\t\t<option value=\"".$this->menu_structure[$index]["IDENTIFIER"];
					if($add_parent=="YES"){
						$outtext .= ",".$this->menu_structure[$index]["PARENT"];
					}
					$outtext .= "\"";
					if (count($choice)>0){
						$found=0;
						
						for($cindex=0,$max=count($choice);$cindex<$max;$cindex++){
							if ($choice[$cindex]==$this->menu_structure[$index]["IDENTIFIER"]){
								$found=1;
							}
						}
						if($found==1){
							$outtext .= " selected=\"true\"";
						}
					}else{
						/*
						if ($parameters["identifier"]==$this->menu_structure[$index]["GROUPS"][0]){
							$outtext .= " selected=\"true\"";
						}*/
					}
					if (count($channels)>0){
						$max=count($channels);
						if ($max>0){
							$disabled=1;
							for($channel_index=0;$channel_index<$max;$channel_index++){
								foreach ($this->menu_structure[$index]["DISPLAY_OPTIONS"] as $key => $value){
									if ($channels[$channel_index]==$value){
										$disabled=0;
									}
								}
							}
						} else {
							$disabled=0;
						}
					}else{
					}
					if ($use_useraccess_restrictions=="YES"){
						if (is_array($user_restricted_locations)){
							$max = count($user_restricted_locations);
						} else {
							$max = 0;
						}
						if ($max > 0){
							$disabled=1;
							for($user_index=0;$user_index<$max;$user_index++){
								if ($user_restricted_locations[$user_index]==$this->menu_structure[$index]["IDENTIFIER"]){
									$disabled=0;
								}
							}
						} else {
							$disabled=0;
						}
					}
					if (($disabled==1) || (($can_restrict_home_page=="1") && ($this->menu_structure[$index]["URL"]=="index.php"))){
						$outtext .= " disabled=\"true\"";
					}
					$outtext .= ">";
					for ($d=0;$d<$depth;$d++){
						if ($d<$depth-1){
							$outtext .= "[[nbsp]][[nbsp]][[nbsp]]";
						}else{
							if ($this->menu_structure[$index]["CHILDREN"]==0){
								$outtext .= "[[nbsp]][[nbsp]][[nbsp]]";
							}else{
								$outtext .= "[[nbsp]]-[[nbsp]]";
							}
						}
					}
					$outtext .= $this->menu_structure[$index]["LABEL"]."</option>\n";
				}else{
					if ($this->menu_structure[$index]["PARENT"]==$menu_identifier){
						if ($this->menu_structure[$index]["URL"]!="admin/index.php"){
							$outtext .= "\t\t\t\t\t\t<option value=\"".$this->menu_structure[$index]["IDENTIFIER"];
							if($add_parent=="YES"){
								$outtext .= ",".$this->menu_structure[$index]["PARENT"];
							}
							$outtext .= "\"";
							if (count($choice)>0){
								$found=0;
								for($cindex=0,$max=count($choice);$cindex<$max;$cindex++){
									if ($choice[$cindex]==$this->menu_structure[$index]["IDENTIFIER"]){
										$found=1;
									}
								}
								if (($can_restrict_home_page=="1") && ($this->menu_structure[$index]["URL"]=="index.php")){
									$outtext .= " disabled=\"true\"";
								}
								if($found==1){
									$outtext .= " selected=\"true\"";
								}
							}else{
								/*
								if ($parameters["identifier"]==$this->menu_structure[$index]["GROUPS"][0]){
									$outtext .= " selected=\"true\"";
								}*/
							}
							if (count($channels)>0){
								$max=count($channels);
								if ($max>0){
									$disabled=1;
									for($channel_index=0;$channel_index<$max;$channel_index++){
										foreach ($this->menu_structure[$index]["DISPLAY_OPTIONS"] as $key => $value){
											if ($channels[$channel_index]==$value){
												$disabled=0;
											}
										}
									}
								} else {
									$disabled=0;
								}
							}else{
							}
							if ($use_useraccess_restrictions=="YES"){
								if (is_array($user_restricted_locations)){
									$max = count($user_restricted_locations);
								} else {
									$max = 0;
								}
								if ($max > 0){
									$disabled=1;
									for($user_index=0;$user_index<$max;$user_index++){
										if ($user_restricted_locations[$user_index]==$this->menu_structure[$index]["IDENTIFIER"]){
											$disabled=0;
										}
									}
								} else {
									$disabled=0;
								}
							}
							if (($disabled==1) || (($can_restrict_home_page=="1") && ($this->menu_structure[$index]["URL"]=="index.php"))){
								$outtext .= " disabled=\"true\"";
							}
							$outtext .= "><![CDATA[";
							for ($d=0;$d<$depth;$d++){
								if ($d<$depth-1){
									$outtext .= "[[nbsp]][[nbsp]][[nbsp]]";
								}else{
									if ($this->menu_structure[$index]["CHILDREN"]==0){
										$outtext .= "[[nbsp]][[nbsp]][[nbsp]]";
									}else{
										$outtext .= "[[nbsp]]-[[nbsp]]";
									}	
								}	
							}
							$outtext .= $this->menu_structure[$index]["LABEL"]."]]></option>\n";
						}
					}
				}
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				-
				*/ 
				$outtext .= $this->list_all_menu_locations_indent_value($choice,$this->menu_structure[$index]["IDENTIFIER"],$depth+1,$parameters);
			}
		}
		return $outtext;
	}
	
	function layout_retrieve_theme(){
		$url = $this->parent->script;
		$len = count($this->menu_structure);
		if ($len==0){
			$this->load_menu();
			$len = count($this->menu_structure);
		}
		$found_theme=-1;
		for ($index=0;$index<$len;$index++){
			if ($this->menu_structure[$index]["URL"]==$url){
				if ($this->menu_structure[$index]["THEME"]>0){
					$found_theme = $this->menu_structure[$index]["THEME"];
					break;
				} else {
					if ($this->menu_structure[$index]["PARENT"]>-1){
						$found_theme = $this->get_parent_theme($this->menu_structure[$index]["PARENT"]);
					}
				}
			}
		}
		return $found_theme;
	}
	
	function layout_retrieve_location_id($parameters){
		$url = $this->check_parameters($parameters,"url",$this->parent->script);
		$len = count($this->menu_structure);
		if ($len==0){
			$this->load_menu();
			$len = count($this->menu_structure);
		}
		$found=-1;
		for ($index=0;$index<$len;$index++){
			if ($this->menu_structure[$index]["URL"]==$url){
				$found  = $this->menu_structure[$index]["IDENTIFIER"];
			}
		}
		return $found;
	}
	function layout_retrieve_location_url($parameters){
		$id = $this->check_parameters($parameters,"id",-1);
		if (strpos($id,",")===false){
		} else {
			$id_list = split(",",$id);
			$id = trim($id_list[0]);
		}
		$len = count($this->menu_structure);
		if ($len==0){
			$this->load_menu();
			$len = count($this->menu_structure);
		}
		$found="";
		if ($id > -1){
			for ($index=0;$index<$len;$index++){
	//			print "<br/>[$id] = ";
//				print $this->menu_structure[$index]["IDENTIFIER"];
				if ($this->menu_structure[$index]["IDENTIFIER"]==$id){
					$found  = $this->menu_structure[$index]["URL"];
//					print " ding $found";
				}
			}
		}
		return $found;
	}

	function get_parent_theme($parent){
		$found_theme=-1;
		$len = count($this->menu_structure);
		if ($len==0){
			$this->load_menu();
			$len = count($this->menu_structure);
		}
		for ($index=0;$index<$len;$index++){
			if ($this->menu_structure[$index]["IDENTIFIER"]==$parent){
				if ($this->menu_structure[$index]["THEME"]>0){
					$found_theme = $this->menu_structure[$index]["THEME"];
					break;
				} else {
					if ($this->menu_structure[$index]["PARENT"]>-1){
						$found_theme = $this->get_parent_theme($this->menu_structure[$index]["PARENT"]);
					}
				}
			}
		}
		return $found_theme;
	}
	
	function possible_extrenal_link($url){
		if (strpos($url,"://")!==0){
			return false;
		}
		return true;
	}
	
	function blank_directory($path){
		$root		= $this->parent->site_directories["ROOT"];
		$dir 		= $root.$path;
		$filename =$dir."/index.php";
		if (!file_exists($filename)){
			$directories = split('/',$path);
			$directorycount = count($directories);
			$directory_to_root="";
			for($index=0;$index<$directorycount;$index++){
				$directory_to_root.="../";
			}
			$um =umask(0);
			@chmod($dir, LS__DIR_PERMISSION);
			umask($um);
			$fp = fopen($filename,"w");
			fwrite($fp, "<"."?php\r\n\$mode=\"error\";\r\n\$command=\"ERRORS_NO_ACCESS\";\r\n\require_once \"modules/included_page.php\"; \r\n?".">");
			fclose($fp);
			$um = umask(0);
			@chmod($filename, LS__FILE_PERMISSION);
			umask($um);
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- here is the functionality to remove the directory information from the
	- database and to remove the directory completly.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function remove_directory($directory_id, $full_path=""){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"remove_directory",__LINE__,"[$directory_id,$full_path]"));
		}
		$is_there_any_files = 0;
		$file				= $this->check_parameters($this->parent->site_directories,"ROOT").$full_path;
		$handle 			= @opendir($file."/");
		if ($handle){
			while($filename = readdir($handle)) {
				if ($filename != "." && $filename != "..") {
					$is_there_any_files =1;
				}
			}
			closedir($handle);
		}
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"is there any files in direcotry",__LINE__,"[".$is_there_any_files."]"));
		}
		if ($is_there_any_files==0){
			$client = $this->client_identifier;
			$sql	= "delete from directory_data where directory_identifier=$directory_id and directory_client=$client;";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[".$sql."]"));
			}
			$this->parent->db_pointer->database_query($sql);
			$um =umask(0);
			chmod($file,LS__DIR_PERMISSION);
			umask($um);
			@rmdir($file);
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"rm_dir",__LINE__,"[".$file."]"));
			}
			return 1;
		}else{
			$this->call_command("ENGINE_ERROR",array(__FILE__,__LINE__,"Unable to remove the directory [$file] as there are existing files"));
			return 0;
		}
	}
	
	
	function get_directories($can_upload=-1){
		$where="";
		$sql = "select * from directory_data where directory_client = $this->client_identifier $where order by directory_parent, directory_name";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_directories",__LINE__,"$sql"));
		}
		$dir_result = $this->parent->db_pointer->database_query($sql);
		if ($dir_result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($dir_result))) {
				$this->directories[count($this->directories)]= array(
				"IDENTIFIER" 	=> $r["directory_identifier"],
				"CLIENT" 		=> $r["directory_client"],
				"PARENT" 		=> $r["directory_parent"],
				"NAME" 			=> $r["directory_name"],
				"CAN_UPLOAD"	=> $r["directory_can_upload"],
				"CAN_SPIDER"	=> $r["directory_can_spider"]
				);
			}
			return 1;
		} else {
			return 0;
		}
	}

	function rebuild_menu_uris($from,$to){
		$sql ="update menu_data set menu_url = concat('$to', substring(menu_url from character_length('$from')+1)) where menu_url like '".$from."%' and menu_client=$this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
	}
	
	function terminate_directory($Dir,$safe=0){
		$root  = $this->check_parameters($this->parent->site_directories,"ROOT")."/";
		$total = 0;
		if ($safe==0){
			$sql= "select count(menu_identifier) as total from menu_data where menu_url like '".$Dir."%' order by menu_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			while ($r=$this->parent->db_pointer->database_fetch_array($result)){
				$total = $r["total"];
			}
		}
		if($total==0){
			if ($handle = @opendir($root.$Dir)) {
				while (($file = readdir($handle)) !== false) {
					if ($file == "." || $file == "..") {
						continue;
					}
					if (is_dir($root.$Dir.$file)){
						// call self for this directory
						$this->terminate_directory($Dir.$file."/",1);
						$um = umask(0);
						@chmod($root.$Dir.$file,LS__DIR_PERMISSION);
						umask($um);
						@rmdir($root.$Dir.$file); //remove this directory
					} else {
						$um = umask(0);
						@chmod($root.$Dir.$file,LS__FILE_PERMISSION);
						umask($um);
						@unlink($root.$Dir.$file); // remove this file
					}
				}
			}
			@closedir($handle);
		}
	}



	function save_channels($parameters){
		$debug = $this->debugit(false,$parameters);
		$menu_id		= $this->check_parameters($parameters,"menu_identifier",-1);
		$menu_display 	= $this->check_parameters($parameters,"menu_display",Array());
		$menu_stylesheet= $this->check_parameters($parameters,"menu_stylesheet",'');
		$num			= $this->check_parameters($parameters,"totalnumberofchecks_menu_display",0);
		$command 		= $this->check_parameters($parameters,"access_command");
		$list 			= Array();
		if ($command=="LAYOUT_ADD_MENU"){
			$sql = "select distinct mgc_command from menu_global_commands where mgc_client=$this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$list[count($list)] = $r["mgc_command"];
            }
            $this->call_command("DB_FREE",Array($result));
		}
		$sql = "select * from theme_types";
		$result  = $this->parent->db_pointer->database_query($sql);
		$theme_command = Array();
		// only one of these command per location
		$default = 'PRESENTATION_DISPLAY';
		while ($r=$this->parent->db_pointer->database_fetch_array($result)){
			$theme_command[$r["theme_type_command"]] = 1;
			if ($r["theme_type_identifier"]==$menu_stylesheet){
				$default = $r["theme_type_command"];
			}
		}
		if ($debug) print "<p><em>$menu_stylesheet</em><strong>$default</strong></p>";
		
		$sql 			= array();
		$sql[0]			= "delete from display_data where display_menu=$menu_id and display_client=$this->client_identifier";
		$dis = 0;
		$mylist = count($list);
		for ($n=1;$n <= $num;$n++){
			$ga = $this->check_parameters($parameters,"menu_display_$n",Array());
			$count 			= count($ga);
			for ($index=0; $index<$count; $index++){
				if ($this->check_parameters($theme_command,$ga[$index],"__NOT_FOUND__")=="__NOT_FOUND__"){
					if ($mylist>0){
						// remove menu global command from default insert list if you are inserting here.
						for ($i =0; $i<$mylist;$i++){
							if ($list[$i]==$ga[$index]){
								$list[$i]="";
							}
						}
					}
					$sql[count($sql)] = "insert into display_data (display_menu,display_client,display_command) values ('$menu_id', '$this->client_identifier', '".$ga[$index]."');";
				} else {
				}
			}
		}
		for ($i =0; $i<$mylist;$i++){
			if ($list[$i]!=""){
				$sql[count($sql)] = "insert into display_data (display_menu,display_client,display_command) values ('$menu_id', '$this->client_identifier', '".$list[$i]."');";
			}
		}

		if ($dis==0){
			$sql[count($sql)]="insert into display_data (display_menu,display_client,display_command) values ('$menu_id','$this->client_identifier','$default');";
		}
		for ($index=0,$len=count($sql);$index<$len;$index++){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[".$sql[$index]."]"));
			}
			if ($debug) print "<li>".$sql[$index]."</li>";
			$this->call_command("DB_QUERY",array($sql[$index]));
		}
		if ($debug) $this->exitprogram();
	}

	function save_groups($parameters){
		$menu_id								= $this->check_parameters($parameters,"menu_identifier",-1);
		$totalnumberofchecks_menu_group_access	= $this->check_parameters($parameters,"totalnumberofchecks_menu_group_access",-1);
		
		$sql 									= array();
		$sql[0]="delete from relate_menu_groups where menu_identifier=$menu_id;";
		for($y=1; $y<=$totalnumberofchecks_menu_group_access; $y++){
			$menu_group_access 					= $this->check_parameters($parameters,"menu_group_access_$y",Array());
			$count 									= count($menu_group_access);
			for ($index=0;$index<$count;$index++){
				$sql[count($sql)] ="insert into relate_menu_groups (group_identifier,menu_identifier) values ('".$menu_group_access[$index]."','$menu_id');";
			}
		}
		for ($index=0,$len=count($sql);$index<$len;$index++){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[".$sql[$index]."]"));
			}
			$this->call_command("DB_QUERY",array($sql[$index]));
		}

	}
	
	function get_group_relationship($menu_identifier){
		$sql="select * from relate_menu_groups where menu_identifier='$menu_identifier'";
		$result  = $this->parent->db_pointer->database_query($sql);
		$result_array = array();
		while ($r = $this->parent->db_pointer->database_fetch_array($result)) {
			$result_array[count($result_array)]=$r["group_identifier"];
		}
		$this->call_command("DB_FREE",array($result));
		return $result_array;
	}
		
	
	
	function display_directories_options($parent_identifier,$current_dir=-2,$directory_starter="/"){
		$out ="";
		$num_occurs = count($this->directories);
		if ($num_occurs==0){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if there are no specified directories then check to see if the array has been filled.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$this->get_directories();
			$num_occurs = count($this->directories);
		}
		for ($index=0;$index<$num_occurs;$index++){
			if ($this->directories[$index]["PARENT"] == $parent_identifier){
				$out .="<option value='".$this->directories[$index]["IDENTIFIER"]."'";
				if ($this->directories[$index]["IDENTIFIER"]==$current_dir){
					$out .=" selected=\"true\"";
				}
				$out .=">";
				$out .=$this->convert_amps($directory_starter . $this->directories[$index]["NAME"])."/</option>";
				$out .=$this->display_directories_options($this->directories[$index]["IDENTIFIER"],$current_dir,$directory_starter.$this->directories[$index]["NAME"]."/");
			}
		}
		return $out;
	}
	
	function display_upload_directories($parent_identifier,$current_dir=-1,$directory_starter="/"){
		$out ="";
		$num_occurs = count($this->directories);
		if ($num_occurs==0){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if there are no specified directories then check to see if the array has been filled.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$this->get_directories(0);
			$num_occurs = count($this->directories);
		}
		$total=0;
		$z=-1;
		for ($index=0;$index<$num_occurs;$index++){
			if ($this->directories[$index]["CAN_UPLOAD"]==1){
				
				$total++;
				$z = $this->directories[$index]["IDENTIFIER"];
				$out .= "<option value='".$this->directories[$index]["IDENTIFIER"]."'";
				if ($this->directories[$index]["IDENTIFIER"]==$current_dir){
					$out .= " selected=\"true\"";
				}
				$out .= ">";
				$out .= $this->retrieve_directory_path($this->directories[$index]["IDENTIFIER"]);
				$out .= "</option>";
//				$out .= $this->display_upload_directories($this->directories[$index]["IDENTIFIER"],$current_dir=-1,$directory_starter.$this->directories[$index]["NAME"]."/");
			}
		}
		if (($total==1) && ($z != -1)){
			return $z;
		}else{
			return $out;
		}
		
	}
	
	function display_directories($parent_identifier,$depth=1){
		$out ="";
		$num_occurs = count($this->directories);
		if ($num_occurs==0){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if there are no specified directories then check to see if the array has been filled.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$this->get_directories();
			$num_occurs = count($this->directories);
		}
		for ($index=0;$index<$num_occurs;$index++){
			if ($this->directories[$index]["PARENT"] == $parent_identifier){
				$out .="<directory identifier=\"".$this->directories[$index]["IDENTIFIER"]."\" client=\"".$this->directories[$index]["CLIENT"]."\" parent=\"".$this->directories[$index]["PARENT"]."\" name=\"".$this->convert_amps($this->directories[$index]["NAME"])."\" depth=\"$depth\" can_upload=\"".$this->directories[$index]["CAN_UPLOAD"]."\" can_spider=\"".$this->directories[$index]["CAN_SPIDER"]."\">";
				$out .=$this->display_directories($this->directories[$index]["IDENTIFIER"],$depth+1);
				$out .="</directory>";
			}
		}
		return $out;
	}
	
	function get_display_options($menu_id){
		$sql = "select * from display_data where display_client = $this->client_identifier and display_menu=$menu_id order by display_command";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_display_options",__LINE__,"$sql"));
		}
		$result  = $this->parent->db_pointer->database_query($sql);
		$list = array();
		if ($result){
			while ($r = $this->parent->db_pointer->database_fetch_array($result)) {
				$list[count($list)]= $r["display_command"];
			}
		}
		return $list;
	}
	
	function remove($parameters){
		$menu_id		= $this->check_parameters($parameters,"identifier",-1);
		$folder		= $this->check_parameters($parameters,"folder",-1);
		$sql ="select count(trans_identifier) as total from menu_access_to_page where menu_identifier=$menu_id and client_identifier=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$total=0;

		while ($r =$this->parent->db_pointer->database_fetch_array($result)){
			$total=$r["total"];
		}
		if ($total==0){
			$this->page_remove_confirm($parameters);
			return "";
		}else{
			$outtext  = "\t\t<module name=\"layout_menu_manager\" display=\"form\">\n";
			$outtext .= "\t\t\t<page_options>\n";
			$outtext .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","LAYOUT_LIST_MENU&amp;folder=$folder","CANEL"));
			$outtext .= "</page_options>\n";
			$out  = "<form name=\"layout_form\" method=\"post\" label=\"".LOCALE_LAYOUT_REMOVE_PUBLISHED_PAGES."\">\n";
			$out .= "<input type=\"hidden\" name=\"identifier\" value=\"$menu_id\"/>\n";
			$out .= "<input type=\"hidden\" name=\"folder\" value=\"$folder\"/>\n";
			$out .= "<input type=\"hidden\" name=\"command\" value=\"LAYOUT_MENU_REMOVED_CONFIRMED\"/>\n";
			$out .= "<text><![CDATA[".LOCALE_LAYOUT_PAGES_EXIST_REMOVE."]]></text>";
			$out .= "<input type=\"submit\" iconify=\"YES\" value=\"ENTRY_SAVE\"/>\n";
			$out .= "</form>\n";
			$outtext .="$out\t\t</module>\n";
			return $outtext;
		}
	}
	function remove_confirm($parameters){
		$menu_id		= $this->check_parameters($parameters,"identifier",-1);
		$_SESSION["redirect_on_recache"]="LAYOUT_PAGE_REMOVAL_COMPLETE";
		$_SESSION["parameter_identifier"]=$menu_id;
//		if ($this->parent->server[LICENCE_TYPE]==SITE_WIZARD){
//			$this->call_command("PAGE_REMOVE_PAGES_IN_LOCATION",Array("menu_identifier"=>$menu_id));
//		}
		$this->page_remove_confirm($parameters);
	}
	function page_remove_confirm($parameters){
		$menu_id		= $this->check_parameters($parameters,"identifier",-1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"remove",__LINE__,"[$menu_id]"));
		}
		$menu_url="";
		$menu_parent=-1;
		$sql ="select * from menu_data where menu_identifier=$menu_id and menu_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		while ($r =$this->parent->db_pointer->database_fetch_array($result)){
			$menu_url=$r["menu_url"];
			$menu_dir=$r["menu_directory"];
			$menu_parent=$r["menu_parent"];
		}
		$sql = array();
		$sql[0]	= "delete from menu_data where menu_client=$this->client_identifier and menu_identifier=$menu_id;";
		$sql[1]	= "delete from relate_menu_groups where menu_identifier=$menu_id;";
		$sql[1]	= "delete from menu_access_to_page where client_identifier = $this->client_identifier and menu_identifier=$menu_id;";
		$sql[2]	= "delete from display_data where display_menu=	$menu_id and display_client=$this->client_identifier;";
		$sql[2]	= "delete from directory_data where directory_identifier=$menu_dir and directory_client=$this->client_identifier;";
		for ($index=0,$len=count($sql);$index<$len;$index++){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[".$sql[$index]."]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql[$index]));

		}
		$file_uri = $this->check_parameters($this->parent->site_directories,"ROOT")."/".$menu_url;
		$dir = dirname($menu_url);
		if ($file_uri!= $this->check_parameters($this->parent->site_directories,"ROOT")."/"){
			if (file_exists($file_uri)){
				$um = umask(0);
				@chmod ($file_uri,LS__FILE_PERMISSION);
				umask($um);
				@unlink($file_uri);
				@chmod (dirname($file_uri),LS__DIR_PERMISSION);
				@rmdir(dirname($file_uri));
			}
			$this->call_command("LAYOUT_REMOVE_TREE",Array("path"=>$dir."/"));
		}
//		$this->changeorder($menu_id,$menu_parent);
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=LAYOUT_LIST_MENU&amp;folder=".$menu_parent));
//		exit();
	}
	
	function display_id($parent=-1, $anonymous =0){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* This function will display the Array of menu data in a comma seperated list that will be
		* used for display purposes on the website.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_level",__LINE__,"$parent"));
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Global variables that are updated in this function
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		
		if (count($this->menu_structure)==0){
			$this->displayed="";
			$this->load_menu();
		}
		$outtext="";
		if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==0){
			$user_access ="";
			$useraccess_length=0;
		}else{
			$user_access = $this->check_parameters($_SESSION,"SESSION_GROUP");
			$useraccess_length = count($user_access);
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_level",__LINE__,count($this->menu_structure)." "));
		}
		for ($index=0,$length=count($this->menu_structure);$index<$length;$index++){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if the parent identifier of the entry is equal to the menu location that we are looking to
			- display then we will display the menu
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$found = 0;
//			print "<!-- $index ".$this->check_parameters($this->menu_structure[$index]["GROUPS"],0) ."-->\n";
			if ($parent==$this->menu_structure[$index]["PARENT"]){
				if ($anonymous==0){
					if ($useraccess_length>0){
						if (empty($this->menu_structure[$index]["GROUPS"][0])){
							$found=1;
						} else {
							$accesslength=count($this->menu_structure[$index]["GROUPS"]);
							for ($access_index=0;$access_index<$accesslength;$access_index++){
								for($useraccess_index=0;$useraccess_index<$useraccess_length;$useraccess_index++){
									if ($this->menu_structure[$index]["GROUPS"][$access_index] == $user_access[$useraccess_index]["IDENTIFIER"]){
										$found =1;
									}
								}
							}
						}
					}else{
						if (empty($this->menu_structure[$index]["GROUPS"][0])){
							$found=1;
						}
					}
				} else {
					if (empty($this->menu_structure[$index]["GROUPS"][0])){
						$found=1;
					}
				}
				if ($found==1){
					if ((strlen($outtext)>0) || ($parent!=-1)){
						$outtext .= ", ";
					}
					$outtext .= $this->menu_structure[$index]["IDENTIFIER"];
					$outtext .= $this->display_id($this->menu_structure[$index]["IDENTIFIER"], $anonymous);
				}
			}
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Return this html representation  to the calling function that will allow the concatation of
		* the html to take place.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		return $outtext;
		
	}
	function display_child_id($parameters){
		$parent = $this->check_parameters($parameters,"parent",-1);
		$second_time = $this->check_parameters($parameters,"second_time",0);
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* This function will display the Array of menu data in a html representation that will be
		* used for display purposes on the website.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_level",__LINE__,"$parent"));
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Global variables that are updated in this function
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if (count($this->menu_structure)==0){
			$this->load_menu();
		}
		$outtext="";
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_level",__LINE__,count($this->menu_structure)." "));
		}
		if($second_time==0){
			for ($index=0,$length=count($this->menu_structure);$index<$length;$index++){
				if ($this->menu_structure[$index]["IDENTIFIER"]==$parent && $this->menu_structure[$index]["URL"]=="index.php"){
					$parent=-1;
				}
			}
		}
		for ($index=0,$length=count($this->menu_structure);$index<$length;$index++){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if the parent identifier of the entry is equal to the menu location that we are looking to
			- display then we will display the menu
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$found = 0;
			if ($parent==$this->menu_structure[$index]["PARENT"]){
				$accesslength=count($this->menu_structure[$index]["GROUPS"]);
				if ($accesslength>0){
					for ($access_index=0;$access_index<$accesslength;$access_index++){
						if ($max_grps>0){
							for ($mg_index=0;$mg_index<$max_grps;$mg_index++){
								if ($this->menu_structure[$index]["GROUPS"][$access_index]==$max_grps[$mg_index]["IDENTIFIER"]){
									$found=1;
								}
							}
						}else{
							if (empty($this->menu_structure[$index]["GROUPS"][0])){
								$found=1;
							}
						}
					}
				}else{
					if (empty($this->menu_structure[$index]["GROUPS"][0])){
						$found=1;
					}
				}
				if ($found==1){
					if (strlen($outtext)>0){
						$outtext .= ", ";
					}
					$outtext .= $this->menu_structure[$index]["IDENTIFIER"];
					$extra =$this->display_child_id(array("parent" => $this->menu_structure[$index]["IDENTIFIER"], "second_time"=>1));
					if (strlen($extra)>0){
						$outtext .= ", ".$extra;
					}
				} else {
				}
			}
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Return this html representation  to the calling function that will allow the concatation of
		* the html to take place.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		return $outtext;
		
	}
	function have_access(){
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		if ($max_grps==0){
			$group_identifier_list = " is null";
		} else if ($max_grps==1){
			$group_identifier_list = " = ".$grp_info[0]["IDENTIFIER"]." or relate_menu_groups.group_identifier is null";
		} else {
			$group_identifier_list = " in (";
			for($i=0;$i < $max_grps; $i++){
				if ($i>0){
					$group_identifier_list .= ",";
				}
				$group_identifier_list .= $grp_info[$i]["IDENTIFIER"];
			}
			$group_identifier_list .= ") or relate_menu_groups.group_identifier is null";
		}
		$script = $this->parent->script;
		$sql ="select * from menu_data 
					left outer join relate_menu_groups on menu_data.menu_identifier = relate_menu_groups.menu_identifier
				where
					menu_url='".$script."' and 
					(relate_menu_groups.group_identifier $group_identifier_list)";

		$result  = $this->parent->db_pointer->database_query($sql);
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			return 1;
		}else{
			return 0;
		}
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Display the list of pages in this menu location by thier rank and allow ranking to be changed.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Change the rank of a page.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function rank_page($parameters){
		$menu_identifier= $this->check_parameters($parameters,"menu_identifier",0);
		$menu_sort_type = $this->check_parameters($parameters,"menu_sort",0);
		$has_title		= $this->check_parameters($parameters,"has_title",0);
		$number_of 		= $this->check_parameters($parameters,"number_of",0);
		$identifier	 	= $this->check_parameters($parameters,"id", array());
		$rank	 		= $this->check_parameters($parameters,"rank", array());
		$c=1;
		if (count($identifier)>0){
			$sql ="update menu_data set menu_sort=$menu_sort_type where menu_client=$this->client_identifier and menu_identifier = $menu_identifier";
			$this->parent->db_pointer->database_query($sql);

			if (($menu_sort_type=='1') || ($menu_sort_type=='menu_access_to_page.page_rank')){
				$sql ="delete from menu_access_to_page where menu_identifier = $menu_identifier and client_identifier=$this->client_identifier and trans_identifier in (".join(",", $identifier).")";
				$this->parent->db_pointer->database_query($sql);
				for ($i=0;$i<$number_of; $i++){
					$page_title = 0;
					$count = $c;
					if ($identifier[$i] == $has_title){
						$page_title = 1;
						$count =0;
					} else {
						$c++;
					}
					$sql = "insert into menu_access_to_page ( 
						menu_identifier, client_identifier, trans_identifier, page_rank, title_page
					) values ($menu_identifier, $this->client_identifier, ".$identifier[$i].", ".$count.", $page_title)"; //rank[$i]
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					
					$this->parent->db_pointer->database_query($sql);
				}
			} else {
				$sql ="update menu_access_to_page set title_page=0 where client_identifier=$this->client_identifier and menu_identifier = $menu_identifier and trans_identifier in (".join(",", $identifier).")";
				$this->parent->db_pointer->database_query($sql);
				if ($has_title!=''){
					$sql ="update menu_access_to_page set title_page=1 where client_identifier=$this->client_identifier and menu_identifier = $menu_identifier and trans_identifier = $has_title";
					$this->parent->db_pointer->database_query($sql);
				}
			}
		}
	}
	
	function hide_rank_page($parameters){
		$current 			= split("_",$this->check_parameters($parameters, "current_rank", "0_0"));
		$set_title 			= $this->check_parameters($parameters, "set_title", "0");
		$num	 			= $this->check_parameters($parameters, "num", "-1");
		$current_rank		= $current[1];
		$page_identifier	= $current[0];
		$new_rank = $this->check_parameters($parameters,"new_rank",-1);
		$menu_identifier = $this->check_parameters($parameters,"menu_identifier",-1);
		if (($menu_identifier!=-1) && ($page_identifier!=-1) && ($current_rank!=$new_rank)){
			$sql = "update menu_access_to_page set page_rank=-1, title_page = $set_title where page_rank=$current_rank and client_identifier = $this->client_identifier and menu_identifier = $menu_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
			if ($current_rank>0){
				if ($current_rank>$new_rank){
					$sql = "update menu_access_to_page set page_rank=page_rank+1 where page_rank>=$new_rank and trans_identifier != $page_identifier and page_rank < $current_rank and client_identifier = $this->client_identifier and menu_identifier = $menu_identifier";
				}else{
					$sql = "update menu_access_to_page set page_rank=page_rank-1 where page_rank<=$new_rank and trans_identifier != $page_identifier and page_rank > $current_rank and client_identifier = $this->client_identifier and menu_identifier = $menu_identifier";
				}
			} else {
				$sql ="update menu_access_to_page set page_rank=page_rank+1 where page_rank>=$new_rank and trans_identifier!=$page_identifier and client_identifier = $this->client_identifier and menu_identifier = $menu_identifier";
			}
			$result  = $this->parent->db_pointer->database_query($sql);
			$sql = "update menu_access_to_page set page_rank=$new_rank where (page_rank=-1 or page_rank=-2) and client_identifier = $this->client_identifier and menu_identifier = $menu_identifier";
			$this->parent->db_pointer->database_query($sql);
		}
	}
	
	function update_all_menu_uris($parameters){
		$ok  = $this->check_parameters($parameters,"ok",0);
		$_SESSION["uri_ok"]=$ok;
		$ok = 1;
		if($ok==0){
			$find = "-";
			$replace = "_";
		}else {
			$find = "_";
			$replace = "-";
		}
		$sql = "select directory_identifier, directory_name from directory_data where directory_name like '%$find%' and directory_client = $this->client_identifier";
//		$sql = "select directory_identifier, directory_name from directory_data where directory_name like '%$find%'";
		//print "<li>$sql</li>";
		$result  = $this->parent->db_pointer->database_query($sql);
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$new_path	= join("$replace",split("$find",$r["directory_name"]));
				$sql 		= "update directory_data set directory_name='$new_path' where directory_identifier = ".$this->check_parameters($r,"directory_identifier")." and directory_client = $this->client_identifier";
//				$sql 		= "update directory_data set directory_name='$new_path' where directory_identifier = ".$this->check_parameters($r,"directory_identifier")."";
//				print "<li>$sql</li>";
				$this->parent->db_pointer->database_query($sql);
			}
		}
		$sql = "select menu_identifier, menu_directory from menu_data where menu_client = $this->client_identifier and menu_url like '%$find%';";
//		$sql = "select menu_identifier, menu_directory from menu_data where menu_url like '%$find%';";
		//print "<li>$sql</li>";
		$result  = $this->parent->db_pointer->database_query($sql);
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$new_path	= $this->retrieve_directory_path($r["menu_directory"])."index.php";
//				$sql 		= "update menu_data set menu_url='$new_path' where menu_identifier = ".$this->check_parameters($r,"menu_identifier")." and menu_client = $this->client_identifier";
				$sql 		= "update menu_data set menu_url='$new_path' where menu_identifier = ".$this->check_parameters($r,"menu_identifier")."";
//				print "<li>$sql</li>";
				$this->parent->db_pointer->database_query($sql);
			}
		}
		//$this->exitprogram();
//		$this->call_command("ENGINE_RESTORE");
	
	}
	
	function load_title_page_ids(){
		$sql = "
		select menu_access_to_page.* from menu_access_to_page 
			inner join page_trans_data on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier 
		where client_identifier=$this->client_identifier and title_page=1 and page_trans_data.trans_doc_status=4 and trans_published_version=1
		";
		$this->title_pages = Array();
		$result  = $this->parent->db_pointer->database_query($sql);
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$this->title_pages[$r["menu_identifier"]]=1;
			}
		}
		$this->call_command("DB_FREE", array($sql));
		
	}
	function generate_directory(){
		$data ="";
		$num_occurs = count($this->directories);
		if ($num_occurs==0){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if there are no specified directories then check to see if the array has been filled.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$this->get_directories();
			$num_occurs = count($this->directories);
		}
		$data="";
		if ($num_occurs >0){
			$data = $this->display_directories(-1);
		}
		
		$options="<option value=\"0\">".ENTRY_NO."</option>";
		$options.="<option value=\"1\">".ENTRY_YES."</option>";
		$spider_options="<option value=\"0\">".ENTRY_NO."</option>";
		$spider_options.="<option value=\"1\">".ENTRY_YES."</option>";
		$location_identifier=-1;
		$out ="\t\t<module name=\"layout_directory_manager\">\n";
		$out .="\t\t\t<menu_data max_depth=\"$this->max_depth\" current_location=\"$location_identifier\">$data\n\t\t\t</menu_data>\n";
		$out .="<form name=\"layout_form\" method=\"get\" label=\"Properties\" width=\"100%\">\n";
		$out .="<input type=\"hidden\" name=\"directory_identifier\" value=\"\"/>\n";
		$out .="<input type=\"hidden\" name=\"directory_path\" value=\"\"/>\n";
		$out .="\t\t\t<page_options>\n";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("ADD","LAYOUT_DIRECTORY_ADD",ADD_NEW));
		$out .= "<header><![CDATA[Structure Manager (Directory Listing)]]></header>";
		$out .="</page_options>\n";
		$out .="<select name=\"directory_parent\" label=\"".DIRECTORY_PATH."\" value=\"\"><option value=\"-1\">HTTP://".ROOT_LOCATION."</option></select>\n";
		$out .="<input type=\"text\" name=\"directory_name\" label=\"".DIRECTORY_NAME."\"/>\n";
		$out .="<select name=\"directory_can_upload\" label=\"".CAN_DIRECTORY_UPLOAD."\">$options</select>\n";
		$out .="<select name=\"directory_can_spider\" label=\"".CAN_DIRECTORY_SPIDERED."\">$spider_options</select>\n";
		$out .="<input type=\"hidden\" name=\"command\" value=\"LAYOUT_SAVE_DIRECTORY\"/>\n";
		$out .="<input type=\"submit\" iconify=\"SAVE\" command=\"LAYOUT_SAVE_DIRECTORY\"  value=\"".ENTRY_SAVE."\"/>\n";
		$out .="<input type=\"button\" iconify=\"REMOVE\" command=\"LAYOUT_REMOVE_DIRECTORY\" value=\"".ENTRY_DELETE."\"/>\n";
		$out .="</form>\n";
		$out .="\t\t</module>\n";
		return $out;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Save the directory information
	- REQUIRED INFOMRATION
	- directory_name
	- directory_identifier (empty string if unknown)
	- directory_parent	
	- directory_can_upload
	- directory_can_spider
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function save_directory($parameters){
		$ok=0;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save_directory",__LINE__,"[".join($parameters,", ")."]"));
		}
//		$out="";
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* if adding then we want to insert the information into
		* the database
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($this->check_parameters($parameters,"directory_identifier")==""){
			$count=0;
			$dir_name = $this->make_uri($this->check_parameters($parameters,"directory_name"));
			if (($dir_name=="admin") || ($dir_name=="modules") || ($dir_name=="kernal")){
			} else {
				$ok=1;
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if the user_identifier exists then get rid of it (ie on an add it
				- is blank.
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$table = array();
				$table["directory_client"] = $this->client_identifier;
				$table["directory_name"]  = $dir_name;
				$table["directory_parent"]  = $this->check_parameters($parameters,"directory_parent");
				$table["directory_can_upload"]  = $this->check_parameters($parameters,"directory_can_upload","0");
				$table["directory_can_spider"]  = $this->check_parameters($parameters,"directory_can_spider","1");
				$field_list ="";
				$value_list ="";
				$check_sql = "select * from directory_data where directory_name='". $table["directory_name"] ."' and directory_parent=". $table["directory_parent"] ." and directory_client = $this->client_identifier;";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$check_sql]"));
				}
				$result = $this->call_command("DB_QUERY",array($check_sql));
				if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- build the sql statement to insert the user information
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					reset($table);
					foreach ($table as $key => $val) {
						if (strpos($key,"directory_")===0){
							if ($count>0){
								$field_list .= ", ";
								$value_list .= ", ";
							}
							$field_list .= $key;
							if ($val!="now()"){
								$value_list .= "'$val'";
							}else{
								$now = $this->libertasGetDate("Y/m/d H:i:s");
								$value_list .= "'$now'";
							}
							$count++;
						}
					}

					$sql="insert into directory_data ($field_list) values ($value_list)";

					$this->parent->db_pointer->database_query($sql);
					if ($this->module_debug ){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
					}
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- build the new directory
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					if (empty($parameters["directory_path"])){
						$parameters["directory_path"] = "/".$this->retrieve_directory_path($table["directory_parent"]);
					}
					$path_to_use=$this->check_parameters($this->parent->site_directories,"ROOT").strtolower($parameters["directory_path"]."/".$table["directory_name"]);
					if ($this->module_debug ){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"make directory",__LINE__,"[$path_to_use]"));
					}
					$oldumask = umask(0);
		 			$ok = @mkdir($path_to_use, LS__DIR_PERMISSION); // or even 01777 so you get the sticky bit set
		 			umask($oldumask);
				} else {

				}
			}
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* if editting then we want to update the information in the database
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* this code is here for future expansion this code should never be in this
		* version of the module.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($parameters["directory_identifier"]!=""){
			$count=0;
			$p_name = strtolower($this->check_parameters($parameters,"directory_name"));
			if (($p_name=="admin") || ($p_name=="modules") || ($p_name=="")){
			} else {
				$ok=1;
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if the user_identifier exists then get rid of it (ie on an add it
				- is blank.
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$table = array();
				$table["directory_client"] 		= $this->client_identifier;
				$table["directory_name"]  		= $this->check_parameters($parameters,"directory_name");
				$table["directory_parent"]  	= $this->check_parameters($parameters,"directory_parent");
				$table["directory_can_upload"]  = $this->check_parameters($parameters,"directory_can_upload","0");
				$table["directory_can_spider"]  = $this->check_parameters($parameters,"directory_can_spider","1");
				$field_list ="";
				$value_list ="";
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- build the sql statement to insert the user information
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				//reset($table);
				foreach ($table as $key => $val) {
					if (strpos($key,"directory_")===0){
						if ($count>0){
							$field_list .= ", ";
						}
						$field_list .= $key;
						if ($val!="now()"){
							$field_list .= "= '$val'";
						}else{
							$now = $this->libertasGetDate("Y/m/d H:i:s");
							$field_list .= "= '$now'";
						}
						$count++;
					}
				}
				$dir_id = $this->check_parameters($parameters,"directory_identifier");
				$sql="update directory_data set $field_list where directory_identifier=".$dir_id;
				$this->parent->db_pointer->database_query($sql);
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[$sql]"));
				}
			}			
		}
		$this->generate_robots_txt_file();
		return $ok;
	}
	
	function set_global_command_status($parameters){
		$debug = $this->debugit(false,$parameters);
		$cmd  = $this->check_parameters($parameters,"cmd");
		$status  = $this->check_parameters($parameters,"status","IGNORE");
		if ($status=="IGNORE"){
			// do nothing
		} else if ($status=="ON"){
			// Add if it does not exist in list
			$sql = "select distinct mgc_command from menu_global_commands where mgc_client='$this->client_identifier' and mgc_command='$cmd'";
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			$result  = $this->parent->db_pointer->database_query($sql);
			$num  	= $this->call_command("DB_NUM_ROWS",Array($result));
            $this->call_command("DB_FREE",Array($result));
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$num</p>\n";
			if ($num==0){
				$sql = "insert into menu_global_commands (mgc_client, mgc_command) values ('$this->client_identifier', '$cmd')";
    	        $this->parent->db_pointer->database_query($sql);
			}
		} else if ($status=="OFF"){
			// Revove from list completly
			$sql = "delete from menu_global_commands where mgc_client='$this->client_identifier' and mgc_command='$cmd'";
            $this->parent->db_pointer->database_query($sql);
		}
	}
	
	function find_menu_with_command($parameters){
		$cmd = $this->check_parameters($parameters,"cmd");
		$sql = "select * from menu_data inner join display_data on display_client = menu_client and menu_identifier = display_menu where menu_client = $this->client_identifier and display_command='$cmd'";
		$result  = $this->parent->db_pointer->database_query($sql);
		$menu_list = Array();
		$i=0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$menu_list[$i]=$r["menu_url"];
			$i++;
        }
        $this->call_command("DB_FREE",Array($result));
		return $menu_list;
	}
	
	function save_inherit_channels($parameters){
		$debug = $this->debugit(false,$parameters);
		$menu_id		= $this->check_parameters($parameters,"menu_identifier",-1);
		$menu_parent 	= $this->check_parameters($parameters,"menu_parent",-1);
		$menu_stylesheet= $this->check_parameters($parameters,"menu_stylesheet",'');
		$list 			= Array();
		$sql = "select * from menu_global_commands where mgc_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if (!in_array($r["mgc_command"], $list)){
          		$list[count($list)] = $r["mgc_command"];
			}
		}
        $this->call_command("DB_FREE",Array($result));

		$sql = "select * from theme_types";
		$result  = $this->parent->db_pointer->database_query($sql);
		$theme_command = Array();
		// only one of these command per location
		$default = 'PRESENTATION_DISPLAY';
		while ($r=$this->parent->db_pointer->database_fetch_array($result)){
			$theme_command[$r["theme_type_command"]] = 1;
			if ($r["theme_type_identifier"]==$menu_stylesheet){
				$default = $r["theme_type_command"];
			}
		}
		$found=0;
		$mylist = count($list);
		for($i=0;$i<$mylist;$i++){
			if ($list[$i]==$default){
				$found=1;
			}
		}
		if ($found==0){
			$list[count($list)] = $default;
		}
		if ($debug) print "<p><em>$menu_stylesheet</em><strong>$default</strong></p>";
		
		$dis = 0;
		$mylist = count($list);
		if ($menu_parent==-1){
			$sql = "select * from display_data inner join menu_data on menu_identifier = display_menu and display_client=menu_client where display_client =$this->client_identifier and menu_url = 'index.php'";
		} else {
			$sql = "select * from display_data where display_client =$this->client_identifier and display_menu = $menu_parent";
		}
		if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$found =0;
			foreach ($theme_command as $key => $value){
				//print "[".$key." = ".$r["display_command"]."]<br>";
				if ($key==$r["display_command"]){
					$found = 1;
				}
			}
			for($i=0;$i<$mylist;$i++){
				if ($list[$i]==$r["display_command"]){
					$found=1;
				}
			}
			if ($found==0){
				$list[count($list)] = $r["display_command"];
			}
        }
//		print_r($list);
        $this->call_command("DB_FREE",Array($result));
		$mylist = count($list);
		$sql 			= array();
		for ($i =0; $i<$mylist;$i++){
			if ($list[$i]!=""){
				$sql[count($sql)] = "insert into display_data (display_menu,display_client,display_command) values ('$menu_id', '$this->client_identifier', '".$list[$i]."');";
			}
		}

		if ($dis==0){
//			$sql[count($sql)]="insert into display_data (display_menu,display_client,display_command) values ('$menu_id','$this->client_identifier','$default');";
		}
	//	print_r($sql);
		for ($index=0,$len=count($sql);$index<$len;$index++){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[".$sql[$index]."]"));
			}
			if ($debug) print "<li>".$sql[$index]."</li>";
			$this->call_command("DB_QUERY",array($sql[$index]));
		}
		
		if ($debug) $this->exitprogram();
	}
	
	function manage_inheritance($parameters){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* FN::manage_inheritance()
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* on a Menu Add/Edit the system needs to check if any of the channels are in the menu channel 
		* inheritance table if so then the system. if so the n we need to call each module and tell it to 
		* insert any and all records that are required, ie just because the channel is on doesn't mean an 
		* object is displayed there. for example any poll groups published tothe parent location need to be
		* found and inherited.
		-
		* This function should only be called on a Addition of a new menu location.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- 
		*/
		$cmd			= $this->check_parameters($parameters,"cmd","");
		$menu_id		= $this->check_parameters($parameters,"menu_identifier",-1);
		$menu_parent 	= $this->check_parameters($parameters,"menu_parent",-1);
		$list = Array();
		$i=0;
		if ($cmd == "ADD"){
			$sql = "select * from menu_channel_inheritance where mci_client=$this->client_identifier and mci_menu=$menu_parent";
			$result  = $this->parent->db_pointer->database_query($sql);
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$list[$i] = $r["mci_command"];
				$sql = "insert into menu_channel_inheritance (mci_menu,mci_command,mci_client) values ($menu_id, '".$r["mci_command"]."', $this->client_identifier)";
				$this->parent->db_pointer->database_query($sql);
				$i++;
    	    }
        	$this->call_command("DB_FREE",Array($result));
		}
		
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- 
		* Find and update any module tables
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- 
		* Since all modules command starters will follow the same format we can use the channel that is 
		* inherited to work out what modules to call instead of calling every module we only call the
		* required ones (faster). also due to the fact that our command starter could be calling the 
		* presentation module we could end up loading two modules Presentation calls Adinistrative.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- 
		*/
		for ($list_index =0; $list_index<$i; $list_index++){
			$pos = strpos($list[$list_index],"_");
			$starter = substr($list[$list_index],0,$pos);
			$this->call_command($starter."_INHERIT",$parameters);
		}
	}
	
	function generate_hierarchy($parameters){
		/**
		* list_all_menu_locations_indent_value
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* This function will return a simple XML structure for the definition of the site 
		* menu structure 
		*/
		$sz="";
		$parent	= $this->check_parameters($parameters,"parent",-1);
		$mylist	= $this->check_parameters($parameters,"list",Array());
		if (!is_array($mylist)){
		$mylist=Array();
		}
		$length = count($this->menu_structure);
		if ($length==0){
			$this->load_menu();
			$length = count($this->menu_structure);
		}
		for ($index=0; $index < $length ; $index++){
			if ($this->menu_structure[$index]["PARENT"]==$parent && $this->menu_structure[$index]["EXTERNAL"]==0 && $this->menu_structure[$index]["URL"]!="admin/index.php"){
				$sz .= "<menu ";
				$sz .= "id='".$this->menu_structure[$index]["IDENTIFIER"]."' ";
				$sz .= "parent='".$this->menu_structure[$index]["PARENT"]."' ";
				
				if (in_array($this->menu_structure[$index]["IDENTIFIER"],$mylist)){
					$sz .= "selected='true' ";
				}
				$sz .= "> ";
				$sz .= "<label><![CDATA[".$this->menu_structure[$index]["LABEL"]."]]></label> ";
				$sz .= "<children>".$this->generate_hierarchy(Array("parent"=>$this->menu_structure[$index]["IDENTIFIER"],"list"=>$mylist))."</children> ";
				$sz .= "</menu>";
			}
		}
		return $sz;
	}
	
	function menu_to_object_update($parameters){
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql_statement",__LINE__,"[".print_r($parameters,true)."]"));}
		$identifier 	= $this->check_parameters($parameters,	"identifier"	,-1);
		$module 		= $this->check_parameters($parameters,	"module"		,-1);
		$menu_locations = $this->check_parameters($parameters,	"menu_locations",-1);
		$all_locations	= $this->check_parameters($parameters,	"all_locations"	,0);
		$delete_entries	= $this->check_parameters($parameters,	"delete"		,1);
		$publish		= $this->check_parameters($parameters,	"publish"		,1);
		$numbers		= $this->check_parameters($parameters,	"numbers"		,Array());
		if ($delete_entries==1){
			$sql = "delete from menu_to_object where mto_client=$this->client_identifier and mto_object=$identifier and mto_module='$module' and mto_publish=$publish";
			$this->parent->db_pointer->database_query($sql);
		}
		if ($all_locations==0){
			if (is_array($menu_locations)){
				$max_menus = count($menu_locations);
				for($index=0; $index<$max_menus;$index++){
					$sql = "insert into menu_to_object (mto_client, mto_object, mto_menu, mto_module, mto_publish, mto_extract_num) values ($this->client_identifier, $identifier, ".$menu_locations[$index].", '$module', $publish, ".$this->check_parameters($numbers,	"number_".$menu_locations[$index],0).")";
//					print "<li>$sql</li>";
					$this->parent->db_pointer->database_query($sql);
				}
			}
		}
	}
	
	function menu_to_object_extract($parameters){
		$condition		= $this->check_parameters($parameters,"condition");
		$module			= $this->check_parameters($parameters,"module");
		$table			= $this->check_parameters($parameters,"table");
		$primary		= $this->check_parameters($parameters,"primary");
		$client_field	= $this->check_parameters($parameters,"client_field");
		$publish		= $this->check_parameters($parameters,"publish"		,1);
		$join 			= $this->check_parameters($parameters,"join"		,"inner");
		$just_cond		= $this->check_parameters($parameters,"just_cond"	,"0");
		$ex_field		= $this->check_parameters($parameters,"ex_field"	,"");
		$out = "select distinct mto_menu as menu_id, mto_object $ex_field from menu_to_object 
					$join join $table on 
						(mto_object = $primary and mto_client=$client_field and mto_module='$module')
					where $condition ";
		if ($just_cond==0){
			$out .= "mto_client=$this->client_identifier and mto_publish=$publish";
		}
		return $out;
	}
	
	function menu_to_object_list($parameters){
		$module	= $this->check_parameters($parameters,"module");
		$identifier	= $this->check_parameters($parameters,"identifier");
		$publish	= $this->check_parameters($parameters,"publish"		,1);
		$menu_locations= Array();
		$menu_counter = Array();
		$sql = "select distinct * from menu_to_object where mto_client=$this->client_identifier and mto_object = $identifier and mto_module='$module' and mto_publish=$publish";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result		= $this->parent->db_pointer->database_query($sql);
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r	= $this->parent->db_pointer->database_fetch_array($result)){
				$menu_locations[count($menu_locations)]	= $r["mto_menu"];
				if ($publish==0){
				$menu_counter[count($menu_counter)]	= $r["mto_extract_num"];
				}
			}
			$ok = true;
		}
		$this->call_command("DB_FREE",Array($result));
		if ($publish==0){
			return Array("menus" => $menu_locations, "counters" => $menu_counter);
		} else {
			return $menu_locations;
		}
	}
	
	function menu_to_object_extract_update($parameters){
		$menu_list = implode(",", $this->check_parameters($parameters,"menu_list",Array()));
		if ($menu_list!=""){
			$sql = "select distinct mto_object, mto_module from menu_to_object where mto_menu in ($menu_list -1) and mto_publish =0 and mto_client=$this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$this->call_command($r["mto_module"]."CACHE", Array("identifier" => $r["mto_object"]));
            }
            $this->call_command("DB_FREE",Array($result));
		}
	}
	function menu_to_object_remove($parameters){
		$identifier 	= $this->check_parameters($parameters,	"identifier"	,-1);
		$module 		= $this->check_parameters($parameters,	"module"		,-1);
		$sql = "delete from menu_to_object where mto_client=$this->client_identifier and mto_object=$identifier and mto_module='$module'";
		$this->parent->db_pointer->database_query($sql);
	}
	function menu_to_object_inherit($parameters){
		$condition		= $this->check_parameters($parameters,"condition");
		$menu_location	= $this->check_parameters($parameters,"menu_location");
		$menu_parent	= $this->check_parameters($parameters,"menu_parent");
		$module			= $this->check_parameters($parameters,"module");
		$table			= $this->check_parameters($parameters,"table");
		$primary		= $this->check_parameters($parameters,"primary");
		$client_field	= $this->check_parameters($parameters,"client_field");
		$sql = "select distinct * from menu_to_object 
					inner join $table on 
						(mto_object = $primary and mto_client=$table.$client_field and mto_module='$module')
					where $condition mto_menu = $menu_parent and mto_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$sql = "insert into menu_to_object 
						(mto_client, mto_object, mto_menu, mto_module, mto_publish, mto_extract_num) values 
						($this->client_identifier, ".$r["mto_object"].", $menu_location, '$module', ".$r["mto_publish"].", ".$r["mto_extract_num"].")";
			$this->parent->db_pointer->database_query($sql);
        }
        $this->call_command("DB_FREE",Array($result));
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function get_menu_location_settings($parameters){
		$module 		= $this->check_parameters($parameters,"module");
		$identifier		= $this->check_parameters($parameters,"identifier");
		if($identifier==-1){
			return Array("mls_id"=>-1, "all_locations"=>0,"set_inheritance"=>0);
		}
		$mls_id				= 0;
		$all_locations		= 0;
		$set_inheritance	= 0;
		/*************************************************************************************************************************
        * check some settings
		*************************************************************************************************************************/
		$sql = "select * from menu_location_settings where mls_client=$this->client_identifier and mls_link_id =$identifier and mls_module='$module'";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result  = $this->parent->db_pointer->database_query($sql);
	    while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$mls_id				= $r["mls_identifier"];
			$all_locations		= $r["mls_all_locations"];
			$set_inheritance	= $r["mls_set_inheritance"];
       	}
	    $this->call_command("DB_FREE",Array($result));
		return Array(
			"mls_id"			=> $mls_id,
			"all_locations"		=> $all_locations, 
			"set_inheritance"	=> $set_inheritance
		);
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function save_menu_location_settings($parameters){
		$module 		= $this->check_parameters($parameters,"module"			, "");
		$identifier		= $this->check_parameters($parameters,"identifier"		, -1);
		$set_inheritance= $this->check_parameters($parameters,"set_inheritance"	, 0);
		$all_locations	= $this->check_parameters($parameters,"all_locations"	, 0);
		$cmd 			= $this->check_parameters($parameters,"cmd"				, "ADD");
		$ok =0;
		if($cmd=="ADD"){
			$mls_identifier = $this->getUID();
			$sql = "insert into menu_location_settings (mls_identifier, mls_set_inheritance, mls_all_locations, mls_link_id, mls_module, mls_client)
						values 
					('$mls_identifier', '$set_inheritance', '$all_locations', '$identifier', '$module', $this->client_identifier)";
			$this->parent->db_pointer->database_query($sql);
			$ok =1;
		} else if($cmd=="EDIT"){
			$sql = "update menu_location_settings set mls_set_inheritance= '$set_inheritance', mls_all_locations= '$all_locations' where mls_link_id = '$identifier' and mls_module = '$module' and mls_client = $this->client_identifier";
            $this->parent->db_pointer->database_query($sql);
			$ok =1;
		} else  if($cmd=="REMOVE"){
			$sql = "delete from  menu_location_settings where mls_link_id = '$identifier' and mls_module = '$module' and mls_client = $this->client_identifier";
            $this->parent->db_pointer->database_query($sql);
			$ok =1;
		}
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
//		$this->exitprogram();
		return $ok;
	}
}
// out site class on purpose
	function webObjSort($a,$b){
		if ($a[0]==$b[0]){
			return 0;
		} else {
			return ($a[0] < $b[0]) ? -1 : 1; 
		}
	}


	
?>