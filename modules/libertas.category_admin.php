<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.category_admin.php
* @date 12 Feb 2004
*/
/**
* This module is the administration module for Categories it will allow the user to
* generate Category LISTS which will contain lists of categories that could be used
* by one or more modules.
*/

class category_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_name_label			= "Categorization Module (Administration)";
	var $module_name				= "category_admin";
	var $module_admin				= "1";
	var $module_command				= "CATEGORYADMIN_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_CATEGORY";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.37 $';
	var $module_creation 			= "25/02/2004";
	var $searched					= 0;
	/**
	* loaded Category list this holds the current defined category list that you have loaded
	*/
	var $loadedCatList 				= Array();
	/**
	*  Management Menu entries
	*/

	var $module_admin_options 		= array(
	);
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access = array(
		array("CATEGORYADMIN_ALL",			"COMPLETE_ACCESS"),
		array("CATEGORYADMIN_LIST_CREATOR",	"ACCESS_LEVEL_LIST_AUTHOR"),  // this will allow the user to add a new category to the system
		array("CATEGORYADMIN_CREATOR",		"ACCESS_LEVEL_AUTHOR"),  // this will allow the user to add a new category to the system
		array("CATEGORYADMIN_EDITOR",		"ACCESS_LEVEL_EDITOR"),  // this user role will allow the user to edit and remove categories.
		array("CATEGORYADMIN_APPROVER",		"ACCESS_LEVEL_APPROVER") // this will allow the user to 
	);
	
	/**
	*  Channel options
	*/
	var $module_display_options 	= array(
//		array("CATEGORY_DISPLAY",	LOCALE_DISPLAY_CATEGORY)
	);
	
	/**
	* WebObject entries
	*
	* Each Array has (Type, Label, Command, All locations, Has label)
	*
	* Type: - 0 = User defined, 1 = Channel type Web object, 2 = XSL defined WEbObject)
	*
	* Channels extract information from the system wile XSl defined are functions in the
	* XSL display.
	*/
	var $WebObjects				 	= array();

	/**
	*  filter options
	*/
	var $display_options			= array();

	/**
	*  Access options php 5 will allow these to become private variables.
	*/
	var $admin_access				= 0;
	var $author_admin_access		= 0;
	var $editor_admin_access		= 0;
	var $approve_admin_access		= 0;
	var $add_category_lists			= 0;
	var $install_access				= 0;

	/**
	*  loaded category list
	*/
	var $clist 						= Array();
	var $clist_id					= 0;
	var $clist_locked				= 0;
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
		if (strpos($user_command, $this->module_command)===0){
			if ($user_command==$this->module_command."CREATE_NEW_CLIENT_DETAILS"){
				return $this->create_client_details($parameter_list);
			}
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
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			/**
			* Administration Module commands that do not require Admin access level to them.
			*/
			if ($user_command == $this->module_command."LIST_PRIMARY"){
				return $this->category_list_primary($parameter_list);
			}
			if ($user_command == $this->module_command."LOAD"){
				return $this->category_list_load($parameter_list);
			}
			/**
			* Administration Module commands
			*/
			if ($this->parent->module_type=="install"){
				/**
				* Create table function allow access if in install mode
				*/
				if ($user_command==$this->module_command."CREATE_TABLE"){
					return $this->create_table();
				}
			}
			if ($user_command == $this->module_command."TO_OBJECT_TIDY"){
				$this->tidy();
			}
			if ($user_command == $this->module_command."TO_OBJECT_UPDATE"){
				return $this->category_to_object_update($parameter_list);
			}
			if ($user_command == $this->module_command."TO_OBJECT_IMPORT_PATH"){
				return $this->category_to_object_import_path($parameter_list);
			}
			if ($user_command == $this->module_command."TO_OBJECT_IMPORT"){
				return $this->category_to_object_import($parameter_list);
			}
			if ($user_command == $this->module_command."TO_OBJECT_CHECK_PATH"){
				return $this->category_to_object_check_path($parameter_list);
			}

			if ($this->admin_access==1){
				/*************************************************************************************************************************
                *  add this category list to a menu
                *************************************************************************************************************************/
				if ($user_command == $this->module_command."CACHE_MENU"){
					return $this->category_cache_menu($parameter_list);
				}
				/**
                * What channels are available to the system
				*/
				if ($user_command == $this->module_command."TO_OBJECT_EXTRACTOR_UPDATE"){
					return $this->category_to_object_extract_update($parameter_list);
				}
				if ($user_command == $this->module_command."TO_OBJECT_EXTRACT"){
					return $this->category_to_object_extract($parameter_list);
				}
				if ($user_command == $this->module_command."TO_OBJECT_LIST"){
					return $this->category_to_object_list($parameter_list);
				}
				if ($user_command == $this->module_command."TO_OBJECT_REMOVE"){
					return $this->category_to_object_remove($parameter_list);
				}
				if ($user_command==$this->module_command."CREATE_CATEGORY"){
					return $this->category_create_list($parameter_list);
				}
				if ($user_command==$this->module_command."ENABLE_VFOLDERS"){
					return $this->update_modules_add_v_folders();
				}
				/**
                * What channels are available to the system
				*/
				if ($user_command == $this->module_command."MENU_DISPLAY_OPTIONS"){
					return $this->display_channels($parameter_list);
				}
				/**
				* Category List Setup and management
				*/
				if  ($user_command==$this->module_command."LIST"){
					return $this->category_list($parameter_list);
				}
				if  ($user_command==$this->module_command."EXTRACT_LIST"){
					return $this->category_extract_list($parameter_list);
				}
				if  ($user_command==$this->module_command."RETRIEVE_LIST"){
					return $this->category_retrieve_list($parameter_list);
				}
				if  ($user_command==$this->module_command."RETRIEVE_OPTION_LIST"){
					return $this->category_retrieve_option_list($parameter_list);
				}

				/**
				* List Category Management Access
				*
                * this functionality will allow you to modify the category list details
				*/
				if ($this->add_category_lists){
					if (($user_command==$this->module_command."LIST_EDIT") || (($this->parent->server[LICENCE_TYPE]==ECMS) && $user_command==$this->module_command."LIST_ADD")){
						return $this->category_list_modify($parameter_list);
					}
					if ($user_command==$this->module_command."LIST_REMOVE"){
						return $this->category_list_removal($parameter_list);
					}
					if ($user_command==$this->module_command."LIST_SAVE"){
						$list_id = $this->category_save($parameter_list);
						$cat	= $this->check_parameters($parameter_list,"cat");
						$next	= $this->check_parameters($parameter_list,"next");
						if ($next!=""){
							if ($this->check_parameters($parameter_list,"list_id")!=-1){
								$list_id = $this->check_parameters($parameter_list,"list_id");
							}
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$next."&amp;identifier=".$cat."&amp;list_id=".$list_id));
						} else {
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=CATEGORYADMIN_LIST"));
						}
					}
					if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
						return $this->category_modify($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE"){
						return $this->category_removal($parameter_list);
					}
					if ($user_command==$this->module_command."SAVE"){
						$this->category_save($parameter_list);
						$list_id = $this->check_parameters($parameter_list, "list_id", -1);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=CATEGORYADMIN_LIST_EDIT&amp;identifier=$list_id&recache=1"));
					}
					if ($user_command==$this->module_command."GET_BREADCRUMBTRAILS"){
						return $this->get_bctrails($parameter_list);
					}
				}
			}
		}
		return "";
	}
	/**
	*                                C A T E G O R Y   S E T U P   F U N C T I O N S
	*/

	/**
	* This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic module::initialise() function
	* allowing the ability to define any extra constructor functionality required by this module.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		$this->admin_access				= 0;
		$this->author_admin_access		= 0;
		$this->editor_admin_access		= 0;
		$this->approve_admin_access		= 0;
		$this->add_category_lists		= 0;
		/**
		* define the admin access that this user has.
		*/
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$all=0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$length_of_array = count($access);
			for ($index=0;$index<$length_of_array;$index++){
				if (
					("ALL"==$access[$index])
				){
					$all=1;
				}
				if (
					("CATEGORYADMIN_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("CATEGORYADMIN_CREATOR"==$access[$index])
				){
					$this->author_admin_access=1;
				}

				if (
					("ALL"==$access[$index]) ||
					("CATEGORYADMIN_ALL"==$access[$index]) ||
					("CATEGORYADMIN_LIST_CREATOR"==$access[$index])
				){
					$this->add_category_lists=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("CATEGORYADMIN_ALL"==$access[$index]) ||
					("CATEGORYADMIN_EDITOR"==$access[$index])
				){
					$this->editor_admin_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("CATEGORYADMIN_ALL"==$access[$index]) ||
					("CATEGORYADMIN_APPROVER"==$access[$index])
				){
					$this->approve_admin_access=0;
				}
			}
		}
		if (($all==1 || $this->approve_admin_access || $this->editor_admin_access || $this->add_category_lists || $this->author_admin_access ) && (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files"))){
			$this->list_access=1;
			$this->admin_access=1;
		}
		$this->module_admin_options[count($this->module_admin_options)] = array("CATEGORYADMIN_LIST", "MANAGE_CATEGORIES","");
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
		* Table structure for table 'category'
		*
		* the category table holds the list of categories
		*/
		$fields = array(
			array("cat_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("cat_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("cat_label"			,"varchar(255)"				,"NOT NULL"	,"default '0'"),
			array("cat_parent"			,"integer"					,"NOT NULL"	,"default '0'","key"),
			array("cat_list_id"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key")
		);

		$primary ="cat_identifier";
		$tables[count($tables)] = array("category", $fields, $primary);

		/**
		* Table structure for table 'category_belongs_to_module'
		*
		* the category_belongs_to_module table allows a module to access specific lists of categories
		*/
		$fields = array(
			array("cbtm_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("cbtm_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("cbtm_module"			,"varchar(255)"				,"NOT NULL"	,"default '0'"),
			array("cbtm_clist"			,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);

		$primary ="cbtm_identifier";
		$tables[count($tables)] = array("category_belongs_to_module", $fields, $primary);
		/**
		* Table structure for table 'category_to_object'
		*
		* the category_to_object table allows a module to store lists of categories that belong to
		* an object in that module
		*/
		$fields = array(
			array("cto_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"	,"key"),
			array("cto_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"		,"key"),
			array("cto_object"			,"unsigned integer"			,"NOT NULL"	,"default '0'"		,"key"),
			array("cto_module"			,"varchar(255)"				,"NOT NULL"	,"default '0'"		,"key"),
			array("cto_clist"			,"unsigned integer"			,"NOT NULL"	,"default '0'"		,"key")
		);

		$primary ="cto_identifier";
		$tables[count($tables)] = array("category_to_object", $fields, $primary);
		/**
		* Table structure for table 'category_ranking'
		*
		* the category_ranking table allows a module to store a ranked order of categories
		*/
		$fields = array(
			array("cr_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"	,"key"),
			array("cr_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("cr_cat"			,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("cr_rank"			,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("cr_link"			,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("cr_module"		,"varchar(25)"				,"NOT NULL"	,"default ''"	,"key")
		);

		$primary ="cr_identifier";
		$tables[count($tables)] = array("category_ranking", $fields, $primary);
		/**
		* Table structure for table 'category_list_settings'
		*
		* the category_ranking table allows a module to store a ranked order of categories
		*/
		$fields = array(
			array("cls_identifier"	,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("cls_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("cls_cat"			,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key"),
			array("cls_rank_type"	,"unsigned integer"			,"NOT NULL"	,"default '0'"	,"key")
		);

		$primary ="cls_identifier";
		$tables[count($tables)] = array("category_list_settings", $fields, $primary);


		return $tables;
	}
	/**
	*                                     C A T E G O R Y   F U N C T I O N S
	*/

	/**
	* display list of defined categories for this client span at ten entries
    */
	function category_list($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"".print_r($parameters,true).""));}
		$sql = "select category.cat_identifier, category.cat_label from category where category.cat_client=$this->client_identifier and category.cat_parent = -1";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result = $this->call_command("DB_QUERY",Array($sql));
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$prev = $this->page_size;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Number of Records",__LINE__,"$number_of_records"));}
			$this->page_size = $number_of_records+1;
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
			
			$variables["PAGE_BUTTONS"] = Array();
			if (($this->parent->server[LICENCE_TYPE]==ECMS) && $this->add_category_lists == 1){
				$variables["PAGE_BUTTONS"][0] = Array("ADD","CATEGORYADMIN_LIST_ADD", ADD_NEW);
			}
			
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			//			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
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
			$variables["FILTER"]			= "";
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["cat_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		$this->check_parameters($r,"cat_label",""),"TITLE")
					)
				);
				if ($this->author_admin_access || $this->editor_admin_access){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT","CATEGORYADMIN_LIST_EDIT",EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE","CATEGORYADMIN_LIST_REMOVE",REMOVE_EXISTING);
				}
			}
			$this->page_size = $prev;
			
			return $this->generate_list($variables);
		}
	}
	
	
	/**
	* this function will load a category list from the cache and create the file if it does not
	* exist.
    *
	* @param ARRAY identifier, returntype, list, rank, recache
	*/
	function category_list_load($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"category_list_load",__LINE__,"".print_r($parameters,true).""));}
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$returntype 		= $this->check_parameters($parameters,"returntype",-1);
		$list				= $this->check_parameters($parameters,"list", -1);
		$rank				= $this->check_parameters($parameters,"rank", 0);
		$recache 			= $this->check_parameters($parameters,"recache",-1);
		$optionList			= $this->check_parameters($parameters,"optionList",0);
		$selected			= $this->check_parameters($parameters,"selected",0);
		$limit 				= $this->check_parameters($parameters,"limit","");
		$data_files 		= $this->parent->site_directories["DATA_FILES_DIR"];
		$file 				= $data_files."/category_".$this->client_identifier."_".$identifier.".xml";
		$out 				= "";
		$this->loadedCatList= Array(); // reset array and load with they following details
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"category_list_load",__LINE__,"load $recache, $identifier, $returntype, $list"));}
		
		if ($returntype != -1){
			if ($rank==0){
				$sql		= "select * from category
								where cat_client = $this->client_identifier and cat_list_id = $list
								order by cat_parent, cat_label, cat_identifier";
			} else {
				$sql		= "select category.*, category_ranking.cr_rank from category
									left outer join category_ranking on cr_cat = cat_identifier and cr_client = cat_client
								where cat_client= $this->client_identifier and cat_list_id = $list
								order by cat_parent, cr_rank, cat_label, cat_identifier";
			}
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result = $this->call_command("DB_QUERY",array($sql));
			$pos = 0; // start with empty array
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$this->loadedCatList[$pos]= Array(
					"cat_label"		=> $r["cat_label"],
					"cat_parent"	=> $r["cat_parent"],
					"cat_identifier"=> $r["cat_identifier"],
					"cat_list_id"	=> $r["cat_list_id"]
				);
				$pos++;
			}
			$this->call_command("DB_FREE",array($result));
			return $this->loadedCatList;
		} else {
			if (file_exists($file) && $recache==-1){
				$out = join("",file($file));
			} else {
				if ($identifier!=-1){
					if ($rank==0){
						$sql		= "select * from category
										where cat_client= $this->client_identifier and cat_list_id = $identifier
										order by cat_parent, cat_label, cat_identifier";
					} else {
						$sql		= "select category.*, category_ranking.cr_rank from category
											left outer join category_ranking on cr_cat = cat_identifier and cr_client = cat_client
										where cat_client= $this->client_identifier and cat_list_id = $identifier
										order by cat_parent, cr_rank, cat_label, cat_identifier";
					}
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					$result = $this->call_command("DB_QUERY",array($sql));
					$pos = 0; // start with empty array
					while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
						$this->loadedCatList[$pos]= Array(
							"cat_label"		=> $r["cat_label"],
							"cat_parent"	=> $r["cat_parent"],
							"cat_identifier"=> $r["cat_identifier"],
							"cat_url"		=> ""
						);
						$pos++;
					}
					if($optionList==1){
						return $this->array_to_options($this->loadedCatList, $identifier, "", $limit, $selected);
					} else {
						$this->call_command("DB_FREE",array($result));
						$out = $this->cache_categories($identifier,$identifier,$data_files."/category_".$this->client_identifier."_".$identifier);
//						$out = $this->return_categories($identifier);
						$fp = fopen($data_files."/category_".$this->client_identifier."_".$identifier.".xml","w");
						fwrite($fp, $out);
						fclose($fp);
						$um = umask(0);
						@chmod($file, LS__FILE_PERMISSION);
						umask($um);
	                    $out = join("",file($file));
					}
				} else {
					if($optionList==1){
						if ($rank==0){
							$sql		= "select * from category
											where cat_client= $this->client_identifier and cat_list_id = $identifier
											order by cat_parent, cat_label, cat_identifier";
						} else {
							$sql		= "select category.*, category_ranking.cr_rank from category
												left outer join category_ranking on cr_cat = cat_identifier and cr_client = cat_client
											where cat_client= $this->client_identifier and cat_list_id = $identifier
											order by cat_parent, cr_rank, cat_label, cat_identifier";
						}
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
						$result = $this->call_command("DB_QUERY",array($sql));
						$pos = 0; // start with empty array
						while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
							$this->loadedCatList[$pos]= Array(
								"cat_label"		=> $r["cat_label"],
								"cat_parent"	=> $r["cat_parent"],
								"cat_identifier"=> $r["cat_identifier"],
								"cat_url"		=> ""
							);
							$pos++;
						}
						return $this->array_to_options($this->loadedCatList, $identifier, "", $limit, $selected);
					}
				}
			}
		}
		return $out;
	}
	
	
	/**
	* Page each category
	*
	* Uses the $this->loadedCatList category list and caches the tree structure into seperate files one for each branch
	*
	* @return integer number of children in category
	*/
	function cache_categories2($parameters){
		$parent		= $this->check_parameters($parameters,"parent",-1); 
		$list		= $this->check_parameters($parameters,"list",-1); 
		$file		= $this->check_parameters($parameters,"file","");
		$crumb_path = $this->check_parameters($parameters,"crumb_path","");
		$crumb		= $this->check_parameters($parameters,"crumb","");
		$sql ="
		select * from category
		";
	}
	/**
	* Cache the categories structure one section at a time
	*
	* Uses the $this->loadedCatList category list and caches the tree structure into seperate files one for each branch
	*
	* @param $parent integer the parent identifier for the category list
	* @param $list integer the identifier of the category list
	* @param $file string the file name to save under
	* @param $crumb_path
	* @return integer number of children in category
	*/
	function cache_categories($parent = -1, $list = -1, $file="", $crumb_path ="", $crumb ="" ,$z=0){
		$children = Array();
		$children_data="";
		$list_name="";
		$sql = "SELECT cat_parent, count(*) as total
					FROM category 
				where cat_client = $this->client_identifier and cat_list_id = $list and cat_parent !=-1
				group by cat_parent";
		$complete ="";
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$children[$r["cat_parent"]] = $r["total"];
        }
        $this->call_command("DB_FREE",Array($result));
		$out	 = "<categorylist ";
		$out	.= "parent='$parent' ";
		$out	.= ">";
		$m = count($this->loadedCatList);
	    $found =0;
		for($i=0;$i<$m;$i++){
			if (($this->loadedCatList[$i]["cat_identifier"] == $parent) && ($list != $parent)){
				$list_name = $this->loadedCatList[$i]["cat_label"];
				$crumb_path .= $this->make_uri($this->loadedCatList[$i]["cat_label"])."/";
				$crumb	.= "<crumb>";
				$crumb	.= "	<label><![CDATA[".str_replace(Array("'","&amp;amp;#39;","&amp;#39;","&quot;","&amp;#163;"), Array("[[pos]]","[[pos]]","[[amp]]#39;","[[quot]]","[[pound]]"), $this->loadedCatList[$i]["cat_label"])."]]></label>";
				$crumb	.= "	<path><![CDATA[".$crumb_path."index.php]]></path>\n";
				$crumb	.= "</crumb>";
			}
			if ($this->loadedCatList[$i]["cat_parent"]."" == "".$parent){
				$found++;
			}
		}
		if ($found>0){
			$zm = count($this->loadedCatList);
			for($zi=0;$zi<$zm;$zi++){
				if ($this->loadedCatList[$zi]["cat_parent"]==$parent){
					$children_data="";
					$out .= "<category ";
					$out .= "identifier='".$this->loadedCatList[$zi]["cat_identifier"]."' ";
					$id = $this->loadedCatList[$zi]["cat_identifier"]."";
					$children_count  = $this->check_parameters($children,$id,0);
					$out .= "children='".$children_count."'";
					if ($children_count>0){
						$children_data = $this->cache_categories($id, "$list", $file, $crumb_path, $crumb, 1);
					}
					$out .= ">\n";
					$out .= "	<label><![CDATA[".str_replace(Array("'","&amp;amp;#39;","&amp;#39;","&quot;","&amp;#163;"), Array("[[pos]]","[[pos]]","[[amp]]#39;","[[quot]]","[[pound]]"), $this->loadedCatList[$zi]["cat_label"])."]]></label>\n";
					$this->loadedCatList[$zi]["cat_url"] = $crumb_path."".$this->make_uri($this->loadedCatList[$zi]["cat_label"]);
					$out .= "	<uri><![CDATA[".$this->loadedCatList[$zi]["cat_url"]."/index.php]]></uri>\n";
					$out .= "</category>\n";

					$complete .= "<category parent='".$parent."' identifier='".$this->loadedCatList[$zi]["cat_identifier"]."'>\n<label><![CDATA[".
						str_replace(
							Array("'","&amp;amp;#39;","&amp;#39;","&quot;","&amp;#163;"),
							Array("[[pos]]","[[pos]]","[[amp]]#39;","[[quot]]","[[pound]]"),
							$this->loadedCatList[$zi]["cat_label"]
						)."]]></label>
					<uri><![CDATA[".$this->loadedCatList[$zi]["cat_url"] ."/index.php]]></uri>
					";
					if ($children_data!=""){
						$complete .= "<children>\n".$children_data."</children>\n";
					}
					$complete .= "</category>\n";
				}
			}
//$this->exitprogram();
			$out	.= "<bread>";
			$out	.= $crumb;
			$out	.= "</bread>";
			$out	 .= "</categorylist>";
			$fp = fopen($file."_".$parent.".xml","w");
			fwrite($fp, $out);
			fclose($fp);
			$um = umask(0);
			@chmod($file, LS__FILE_PERMISSION);
			umask($um);
		}
		if (strlen($complete)>0)
			return "<list rank='0' identifier='$parent'><![CDATA[$list_name]]></list>".$complete;
		else
			return "";
	}
	/**
	* a function to return the complete category block <strong>warning</strong> this can be a large amount of data
	* this function recursivly looks through trying to build up a complete structure
	*
	* @param integer parent identifier to find
    * @param String the crumb trail for this category level
    * @return String either XML or empty
	*/
	function return_categories($id=-2, $crumb=""){
        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"return_categories",__LINE__,"[$id]"));}
		$out = "";
		$pos = count($this->loadedCatList);
		for($index = 0; $index<$pos; $index++){
			if ($this->loadedCatList[$index]["cat_identifier"] == $id){
				$list_name = $this->loadedCatList[$index]["cat_label"];
				$list_id   = $id;
			}
			if ($this->loadedCatList[$index]["cat_parent"] == $id){
                if ($crumb==""){
                    $crumb_path = $this->make_uri($this->loadedCatList[$index]["cat_label"]);
                } else {
                    $crumb_path = $crumb."/".$this->make_uri($this->loadedCatList[$index]["cat_label"]);
                }
				$out .= "<category parent='".$id."' identifier='".$this->loadedCatList[$index]["cat_identifier"]."'>\n<label><![CDATA[".
				str_replace(
					Array("'","&amp;amp;#39;","&amp;#39;","&quot;","&amp;#163;"),
					Array("[[pos]]","[[pos]]","[[amp]]#39;","[[quot]]","[[pound]]"),
					$this->loadedCatList[$index]["cat_label"]
				)."]]></label>\n<uri><![CDATA[".$crumb_path ."/index.php]]></uri>\n";
				$children="";
				$children = $this->return_categories($this->loadedCatList[$index]["cat_identifier"], $crumb_path);
				if ($children!=""){
					$out .= "<children>\n".$children."</children>\n";
				}
				$out .= "</category>\n";
			}
		}
		if (strlen($out)>0)
			return "<list rank='0' identifier='$list_id'><![CDATA[$list_name]]></list>".$out;
		else
			return "";
	}
	/**
    *
	*/
	function category_list_modify($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"".print_r($parameters,true).""));}
		$rank=0;
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$form_label 		= LOCALE_ADD;
		$category_listing	= "";
		$cat_label			= "";
		$cat_parent			= -1;
		$info_identifier	= -1;
		if ($identifier!=-1){
			$join = "";
			if ($this->call_command("ENGINE_HAS_MODULE",array("INFORMATIONADMIN_"))==1){
				$join = "	left outer join information_list on info_category = cat_list_id  and info_client=cat_client";
			}
			$join = "	left outer join category_list_settings on cls_cat = cat_list_id and cls_client=cat_client";
			$form_label 	= LOCALE_EDIT;
			$sql = "select * from category $join where cat_parent=-1 and cat_client=$this->client_identifier and cat_identifier=$identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result = $this->call_command("DB_QUERY",array($sql));
			$rank = 0;
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$cat_label			= $r["cat_label"];
				$cat_parent			= $r["cat_parent"];
				$info_identifier	= $this->check_parameters($r,"info_identifier",-1);
				$rank 				= $r["cls_rank_type"];
			}
			$category_listing = $this->category_list_load(Array("identifier"=>$identifier,"recache"=>1,"rank"=>$rank)); //$recache
		}
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Category List Manager - $form_label]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","CATEGORYADMIN_LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"CATEGORYADMIN_LIST_SAVE\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<input type=\"hidden\" name=\"list_id\" value=\"$identifier\" />";
		$out .="		<input type=\"hidden\" name=\"next\" value=\"\" />";
		$out .="		<input type=\"hidden\" name=\"cat\" value=\"\" />";
		$out .="		<input type=\"hidden\" name=\"category_parent_prev\" value=\"$cat_parent\" />";
		$out .="		<input type=\"hidden\" name=\"info_identifier\" value=\"$info_identifier\" />";
		$out .="		<page_sections>";
		$out .="		<section label='Information'>";
		$out .="			<input type=\"hidden\" name=\"cat_label_prev\"><![CDATA[$cat_label]]></input>";
		$out .="			<input type=\"text\" name=\"cat_label\" label=\"Category Label\" size=\"255\"><![CDATA[$cat_label]]></input>";
		$opt = "<option value='0'";
        if ($rank==0){
      			$opt .= " selected='true'";
        }
        $opt .= "><![CDATA[Alphabetical Order]]></option>";
		$opt .= "<option value='1'";
        if ($rank==1){
      			$opt .= " selected='true'";
        }
        $opt .= "><![CDATA[Rank Order]]></option>";
		$out .="			<radio name=\"cat_ranking\" label=\"Category Ranking\" onclick='update_category_sorting'>$opt</radio>";
		/**
		* Display categories
		*/
		$out .="		</section>";
		$out .="		<section label='Categories'>";
		$out .= 			"<categories>$category_listing</categories>";
		$out .="		</section>";
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	/**
    * this function is used to save any changes to a book defintion
	*/
	function category_save($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"".print_r($parameters,true).""));}
		$identifier 			= $this->check_parameters($parameters,"identifier",-1);
		$cat_parent 			= $this->check_parameters($parameters,"cat_parent",-1);
		$list_id				= $this->check_parameters($parameters,"list_id",-1);
		$info_identifier		= $this->check_parameters($parameters,"info_identifier",-1);
		$category_parent_prev	= $this->check_parameters($parameters,"category_parent_prev",-1);
		$cat_label_prev			= $this->check_parameters($parameters,"cat_label_prev");
		$ranking				= $this->check_parameters($parameters,"ranking",Array());
		$cat_ranking			= $this->check_parameters($parameters,"cat_ranking",0);
		$cat_merge				= $this->check_parameters($parameters,"cat_merge");
		$cat_label				= htmlentities(trim($this->strip_tidy($this->check_parameters($parameters,"cat_label"))));
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"$cat_label"));}
		if ($identifier ==-1){
			$cat_identifier = $this->getUid();
			$prev_list_id = $list_id;
			if($list_id==-1){
				$list_id=$cat_identifier;
			}
			$sql="insert into category
				(cat_identifier, cat_label, cat_parent, cat_client, cat_list_id)
					values
				($cat_identifier, '$cat_label', '$cat_parent', '$this->client_identifier', '$list_id') ";
			$this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}

			// if you are modifiying an existing category then check to see if it belongs to a list directory.
			if($prev_list_id!=-1){
				if ($info_identifier !=-1 ){
					// add a new entry to an existing category simply add directory
					$this->call_command("INFORMATIONADMIN_RENAMEMOVE",
						Array(
							"cmd" 				=> "new",
							"info_identifier" 	=> $info_identifier,
							"cat_id" 			=> $cat_identifier,
							"cat_parent" 		=> $cat_parent,
							"cat_label" 		=> $cat_label,
							"list_id"			=> $list_id
						)
					);
				}
			}
			return $list_id;
		} else {
			/*
			*
			- Edit an existing entry
			*
			*/
			$sql="delete from category_list_settings where cls_cat = '$list_id' and cls_client=$this->client_identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY",array($sql));
			if ($list_id == $identifier){
				// only update ranking on primary save
				$cls_identifier = $this->getUid();
				$sql="insert into category_list_settings (cls_identifier, cls_cat, cls_client, cls_rank_type) values('$cls_identifier', '$list_id', $this->client_identifier, $cat_ranking)";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->call_command("DB_QUERY",array($sql));
				$sql = "delete from category_ranking where cr_client=$this->client_identifier and cr_link = '$list_id'";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->call_command("DB_QUERY",array($sql));
				if ($cat_ranking == 1){
					$m = count($ranking);
					$rank = array();
					for ($i=0;$i<$m;$i++){
						$info = split(",",$ranking[$i]);
						$r=0;
						$rc = count($rank);
						for ($z=0;$z<$rc;$z++){
							if ($rank[$z]["parent"] == $info[0]){
								$r++;
							}
						}
						$rank[$i] = Array(
							"parent"	=> $info[0],
							"item"		=> $info[1],
							"rank"		=> $r
						);
					}
					for ($i=0;$i<$m;$i++){
						$sql = "insert into category_ranking (cr_client, cr_link, cr_cat, cr_rank) values ($this->client_identifier, '$list_id', ".$rank[$i]["item"].", ".$rank[$i]["rank"].")";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
						$this->call_command("DB_QUERY",array($sql));
					}
				}
			}
			$sql="update category set
				cat_label  = '$cat_label', 
				cat_parent = '$cat_parent'
				where
				cat_client  	= $this->client_identifier and
				cat_list_id		= $list_id and
				cat_identifier	= $identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY",array($sql));
			// if you are modifiying an existing category then check to see if it belongs to a list directory.
			if ($info_identifier != -1){
				// if parent and previous parent are the same then check to see if the label has changed
				if ($cat_merge!=""){
					// merge requested
					$this->merge_categories(
						Array(
							"info_identifier" 	=> $info_identifier,
							"cat_src" 			=> $identifier,
							"cat_dest"	 		=> $cat_merge,
							"list_id"			=> $list_id
						)
					);
				} else {
					if ($cat_label_prev == $cat_label){
						if ($category_parent_prev == $cat_parent){
								// do nothing nothing has changed
						} else {
							// the category has moved
							$this->call_command("INFORMATIONADMIN_RENAMEMOVE",
								Array(
									"cmd" 				=> "move",
									"info_identifier" 	=> $info_identifier,
									"cat_id" 			=> $identifier,
									"cat_parent" 		=> $cat_parent,
									"cat_parent_prev" 	=> $category_parent_prev,
									"cat_label" 		=> $cat_label,
									"cat_label_prev" 	=> $cat_label_prev,
									"list_id"			=> $list_id
								)
							);
						}
					} else {
						// the category has been re named check to see if it has been moved as well
						if ($category_parent_prev == $cat_parent){
							// just renamed
							$this->call_command("INFORMATIONADMIN_RENAMEMOVE",
								Array(
									"cmd" 				=> "rename",
									"info_identifier" 	=> $info_identifier,
									"cat_id" 			=> $identifier,
									"cat_parent" 		=> $cat_parent,
									"cat_parent_prev" 	=> $category_parent_prev,
									"cat_label" 		=> $cat_label,
									"cat_label_prev" 	=> $cat_label_prev,
									"list_id"			=> $list_id
								)
							);
						} else {
							// renamed and moved
							$this->call_command("INFORMATIONADMIN_RENAMEMOVE",
								Array(
									"cmd" 				=> "rename_move",
									"info_identifier" 	=> $info_identifier,
									"cat_id" 			=> $identifier,
									"cat_parent" 		=> $cat_parent,
									"cat_parent_prev" 	=> $category_parent_prev,
									"cat_label" 		=> $cat_label,
									"cat_label_prev" 	=> $cat_label_prev,
									"list_id"			=> $list_id
								)
							);
						}
					}
				}
			}
			return $list_id;
		}
	}
	/**
	* create an empty category list
	*@param Array keys are "cat_label"
	*/

	function category_create_list($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"<pre>".print_r($parameters,true)."</pre>"));}
		$cat_label	= $this->check_parameters($parameters,"cat_label");
		$identifier = $this->getUid();
		$sql="insert into category
			(cat_identifier, cat_label, cat_parent, cat_client, cat_list_id) 
				values 
			($identifier, '$cat_label', '-1', '$this->client_identifier', '$identifier') ";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
		$this->call_command("DB_QUERY",array($sql));
		// starting a new category 
		return $identifier;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: category_list_modify($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function category_modify($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,print_r($parameters,true)));}
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$list_id	 		= $this->check_parameters($parameters,"list_id",-1);
		$info_identifier	= -1;
		$command 			= $this->check_parameters($parameters,"command","");
		$form_label 		= LOCALE_ADD;
		$cat_label			= "";
		$join = "";
		if ($identifier!=-1){
			if ($this->call_command("ENGINE_HAS_MODULE",array("INFORMATIONADMIN_"))==1){
				$join = "	left outer join information_list on info_category = cat_list_id";
			}
			$sql = "select * from category $join where cat_client=$this->client_identifier and cat_identifier=$identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$list_id	= $r["cat_list_id"];
				$info_identifier = $this->check_parameters($r,"info_identifier",-1);
				if ($command == $this->module_command."ADD"){
					$category_listing = $this->category_list_load(Array("identifier"=>$list_id, "list" => $info_identifier));
					$cat_parent = $identifier;
					$identifier = -1;
				} else {
					$form_label 	= LOCALE_EDIT;
					$cat_label = $r["cat_label"];
					$cat_parent = $r["cat_parent"];
					$category_listing = $this->category_list_load(Array("identifier"=>$r["cat_list_id"]));
				}
			}
		} else {
			$cat_parent = $list_id;
			$identifier = -1;
			if ($this->call_command("ENGINE_HAS_MODULE",array("INFORMATIONADMIN_"))==1){
				$sql = "select * from information_list where info_category = $list_id and info_client = $this->client_identifier";
                $result  = $this->call_command("DB_QUERY",Array($sql));
                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
                	$info_identifier = $r["info_identifier"];
                }
                $this->call_command("DB_FREE",Array($result));
			}
			$category_listing = $this->category_list_load(Array("identifier"=>$list_id));
		}
		
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Category Manager - $form_label]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","CATEGORYADMIN_LIST_EDIT&amp;identifier=$list_id",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"CATEGORYADMIN_SAVE\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<input type=\"hidden\" name=\"list_id\" value=\"$list_id\" />";
		$out .="		<input type=\"hidden\" name=\"category_parent_prev\" value=\"$cat_parent\" />";
		$out .="		<input type=\"hidden\" name=\"info_identifier\" value=\"$info_identifier\" />";
		$out .="		<page_sections>";
		$out .="		<section label='Category Manager'>";
		$out .="			<input type=\"hidden\" name=\"cat_label_prev\"><![CDATA[$cat_label]]></input>";
		$out .="			<input type=\"text\" name=\"cat_label\" label=\"Category Label\" size=\"255\"><![CDATA[$cat_label]]></input>";
		/**
		* Display categories 
		*/
		$out .= 			"<category_location parent='$cat_parent' identifier='$identifier' name='cat_parent'>
								<label><![CDATA[Is a sub-category of]]></label>
								$category_listing
							</category_location>";
		$out .="		</section>";
		if ($identifier!=-1){
			$sql = "select count(*) as total from category_to_object where cto_client=$this->client_identifier and cto_clist = $identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$total=0;
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$total = $r["total"];
            }
            $this->call_command("DB_FREE",Array($result));
			$out .="		<section label='Merge'>";
			$out .="			<text><![CDATA[Merge Categories	<br>On a merge of categories all of the entries associated with this category will be moved into the new category as selected below.  The original category is then deleted unless it contains a child node in whihc case it is left alone.]]></text>";
			$out .="			<text><![CDATA[<p>There are currently (<strong>$total</strong>) items associated with this category</p>]]></text>";
			$out .= 			"<category_location parent='-2' identifier='-2' name='cat_merge'>
								<label><![CDATA[Target category to recieve entries.]]></label>
								$category_listing
							</category_location>";
			$out .="		</section>";
		}
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: category_removal($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function will allow an administrator to delete a category if it is a leaf node
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function category_removal($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$list_id	 		= $this->check_parameters($parameters,"list_id",-1);
		$sql = "delete from category where cat_client=$this->client_identifier and cat_identifier=$identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=CATEGORYADMIN_LIST_EDIT&amp;identifier=$list_id&amp;recache=1"));
	}
		
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: category_list_primary($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function will return the primary category lists
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
		
	function category_list_primary($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$sql = "
		select 
			cat_label, cat_identifier 
		from category 
			where cat_client=$this->client_identifier and cat_parent =-1
		order by cat_label";
//		print "<p>$sql</p>";
		$result = $this->call_command("DB_QUERY",array($sql));
		$out ="";
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$cat_label			= $r["cat_label"];
			$cat_id 			= $r["cat_identifier"];
			$out .="<option value='$cat_id' ";
			if ($identifier == $cat_id){
				$out .=" selected='true'";
			}
			$out .="><![CDATA[$cat_label ]]></option>";
		}
		return $out;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: restore($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function will recache the directory structure of the directory manager
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function restore(){
		//when written
	}
	function category_extract_list($parameters){
		$module 	= $this->check_parameters($parameters,"module");
		$identifier = $this->check_parameters($parameters,"identifier");
		$data_files 	= $this->parent->site_directories["DATA_FILES_DIR"];
		$id = Array();
		if ($identifier==""){
			$sql = "select * from category_belongs_to_module 
					inner join category on (cat_identifier = cbtm_clist and cbtm_client = cat_client)
				where cbtm_client=$this->client_identifier and cbtm_module= '$module'";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$id[count($id)] = $r["cat_list_id"];
			}
            $this->call_command("DB_FREE",Array($result));
		} else {
			$id[0] = $identifier;
		}
		$out  = "<module name=\"categories\" display=\"completelist\">";
		for($index=0;$index<count($id);$index++){
			$file = $data_files."/category_".$this->client_identifier."_".$id[$index].".xml";
			$content = file($file);
			$out .= implode("",$content);
		}
		$out .= "</module>";
		return $out;
	}
	
	function category_retrieve_list($parameters){
		$module 		= $this->check_parameters($parameters,"module");
		$label  		= $this->check_parameters($parameters,"label","Select categories that this belongs to.");
		$add	  		= $this->check_parameters($parameters,"Add","Add new category");
		$output			= $this->check_parameters($parameters,"output","");
		$cat_identifier	= -1;
//		$returnType  = $this->check_parameters($parameters,"returnType","checkboxes");
		$sql = "select * from category_belongs_to_module 
				inner join category on (cat_identifier = cbtm_clist and cbtm_client = cat_client)
			where cbtm_client=$this->client_identifier and cbtm_module= '$module'";
		$result = $this->call_command("DB_QUERY",array($sql));
		$out ="";
		$outlist="";
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$cat_identifier= $r["cat_identifier"];
			$outlist .= $this->category_list_load(Array("identifier" => $r["cat_identifier"],"list"=>$r["cat_list_id"]));
		}
		if($cat_identifier!=-1){
		$out .= "
				<choose_categories can_add='".$this->add_category_lists."' parent='' identifier='$cat_identifier' name='cat_parent' output='$output'>
					<add><![CDATA[$add]]></add>
					<label><![CDATA[$label]]></label>
					$outlist
				</choose_categories>
				";
		}
		return $out;
	}
	function category_retrieve_option_list($parameters){
		$module = $this->check_parameters($parameters,"module");
		$sql = "select * from category_belongs_to_module 
				inner join category on (cat_identifier = cbtm_clist and cbtm_client = cat_client)
			where cbtm_client=$this->client_identifier and cbtm_module= '$module'";
		$cat=Array();
		$result = $this->call_command("DB_QUERY",array($sql));
		$out ="";
		$cat_identifier=-1;
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$cat_identifier= $r["cat_identifier"];
			$cat =$this->category_list_load(Array("identifier" => $r["cat_identifier"], "list"=>$r["cat_list_id"], "returntype"=>1));
		}
		if (is_array($cat)){
			$out = $this->array_to_options($cat, $cat_identifier);
		}
		return $out;
	}
	function array_to_options($c, $parent=-1,$depth="",$limit="", $selected=""){
		$out ="";
		$m=count($c);
		for ($i=0;$i<$m;$i++){	
			if ($c[$i]["cat_parent"]==$parent){
				$out .= "<option value='".$c[$i]["cat_identifier"]."'";
				if($c[$i]["cat_identifier"] == $selected){
					$out .= " selected='true'";
				}
				$out .= "><![CDATA[$depth".$c[$i]["cat_label"]."]]></option>";
				if($limit==""){
					$out .= $this->array_to_options($c, $c[$i]["cat_identifier"],$depth."[[nbsp]][[nbsp]][[nbsp]]", $selected);
				}
			}
		}
		return $out;
	}
	function category_list_removal($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$next = $this->check_parameters($parameters,"next_command","CATEGORYADMIN_LIST");
		$sql = "delete from category where cat_client=$this->client_identifier and cat_list_id=$identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$this->call_command("CATEGORYADMIN_TO_OBJECT_TIDY");
		$data_files 	= $this->parent->site_directories["DATA_FILES_DIR"];
		@unlink($data_files."/category_".$this->client_identifier."_".$identifier."_*.xml");
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$next));
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	 						C A T E G O R Y   T O   O B J E C T
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function category_to_object_update($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"category_to_object_update",__LINE__,"".print_r($parameters,true).""));}
		$newCategories	= $this->check_parameters($parameters,	"new_categories","");
		$newCategories	= htmlentities(strip_tags($this->validate($newCategories)));
		$identifier 	= $this->check_parameters($parameters,	"identifier"	,-1);
		$module 		= $this->check_parameters($parameters,	"module"		,-1);
		$data_list 		= $this->check_parameters($parameters,	"data_list"		,-1);
		$delete_entries	= $this->check_parameters($parameters,	"delete"		,1);
		//print_r($parameters);
		$newCats		= "";
		$newCatsOwner	= -1;
		if ($newCategories!=""){
			$info 			= $this->save_new_categories($newCategories);
			$newCats 		= $info[0];
			$newCatsOwner	= $info[1];
		}
		if ($delete_entries==1){
			$sql = "delete from category_to_object where cto_client=$this->client_identifier and cto_object=$identifier and cto_module='$module'";
			$this->call_command("DB_QUERY",Array($sql));
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		}
		if (is_array($data_list)){
			$max = count($data_list);
			for($index=0; $index<$max;$index++){
				if (is_array($newCats)){
					if (substr($data_list[$index]."",0,3)=="new"){
						for($i=0;$i<count($newCats)-1;$i++){
							if ($data_list[$index]==$newCats[$i][1]){
								$data_list[$index]=$newCats[$i][4];
							}
						}
						$sql = "insert into category_to_object (cto_client, cto_object, cto_clist, cto_module) values ($this->client_identifier, $identifier, ".$data_list[$index].", '$module')";
						$this->call_command("DB_QUERY",Array($sql));
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					} else {
						$sql = "insert into category_to_object (cto_client, cto_object, cto_clist, cto_module) values ($this->client_identifier, $identifier, ".$data_list[$index].", '$module')";
						$this->call_command("DB_QUERY",Array($sql));
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					}
				} else {
					$sql = "insert into category_to_object (cto_client, cto_object, cto_clist, cto_module) values ($this->client_identifier, $identifier, ".$data_list[$index].", '$module')";
					$this->call_command("DB_QUERY",Array($sql));
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				}
			}
		} else {
			//data list is not an array
			$sql = "insert into category_to_object (cto_client, cto_object, cto_clist, cto_module) values ($this->client_identifier, $identifier, ".$data_list.", '$module')";
			$this->call_command("DB_QUERY",Array($sql));
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			
		}
		return Array($data_list,$newCatsOwner);
	}
	
	function category_to_object_extract($parameters){
		$condition		= $this->check_parameters($parameters,"condition");
		$module			= $this->check_parameters($parameters,"module");
		$table			= $this->check_parameters($parameters,"table");
		$primary		= $this->check_parameters($parameters,"primary");
		$client_field	= $this->check_parameters($parameters,"client_field");
		return "select distinct cto_clist as menu_id from category_to_object 
					inner join $table on 
						(cto_object = $primary and cto_client=$client_field and cto_module='$module')
					where $condition cto_client=$this->client_identifier";
	}
	
	function category_to_object_list($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,print_r($parameters,true)));}
		$module		= $this->check_parameters($parameters,"module"		,"");
		$identifier	= $this->check_parameters($parameters,"identifier"	,"");
		$returnType	= $this->check_parameters($parameters,"returntype"	,0);
		$data_list	= Array();
		$sql 		= "select distinct * from category_to_object where cto_client=$this->client_identifier and cto_object = $identifier and cto_module='$module' ";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result		= $this->call_command("DB_QUERY",Array($sql));
		$cat_list 	= "";
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r	= $this->call_command("DB_FETCH_ARRAY",array($result))){
				$data_list[count($data_list)]	= $r["cto_clist"];
				$cat_list .= "<specified_categorys identifier='".$r["cto_clist"]."'/>";
			}
		}
		$this->call_command("DB_FREE",Array($result));
		if ($returnType==0)
			return $data_list;
		else 
			return $cat_list;
	}

	function category_to_object_remove($parameters){
		$identifier 	= $this->check_parameters($parameters,	"identifier"	,-1);
		$module 		= $this->check_parameters($parameters,	"module"		,-1);
		$sql = "delete from category_to_object where cto_client=$this->client_identifier and cto_object=$identifier and cto_module='$module'";
		$this->call_command("DB_QUERY",Array($sql));
	}

	function save_new_categories($nCats){
//		print "$nCats";
		$newCatlist = split("\n",$nCats);
		for($index=0; $index<count($newCatlist);$index++){
			$newCatlist[$index] = split("::",$newCatlist[$index]);
		}
		$sql			= "select cat_list_id from category where cat_identifier='".$newCatlist[0][0]."' and cat_client=$this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  		= $this->call_command("DB_QUERY",Array($sql));
		$cat_list_id	= -1;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$cat_list_id = $r["cat_list_id"];
        }
		
        $this->call_command("DB_FREE",Array($result));
		
		for($index=0; $index<count($newCatlist)-1;$index++){
			$cat_label = $newCatlist[$index][2];
			$cat_parent = $newCatlist[$index][0];
			if (substr($cat_parent."",0,3)=="new"){
				for($i=0;$i<count($newCatlist)-1;$i++){
					if ($cat_parent==$newCatlist[$i][1]){
						$cat_parent=$newCatlist[$i][4];
					}
				}
				$cat_identifier = $this->getUid();
				$sql="insert into category
					(cat_identifier, cat_label, cat_parent, cat_client, cat_list_id) 
						values 
					($cat_identifier, '$cat_label', '$cat_parent', '$this->client_identifier', '$cat_list_id')";
				$this->call_command("DB_QUERY",Array($sql));
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			} else {
				$cat_identifier = $this->getUid();
				$sql="insert into category
					(cat_identifier, cat_label, cat_parent, cat_client, cat_list_id) 
						values 
					($cat_identifier, '$cat_label', '$cat_parent', '$this->client_identifier', '$cat_list_id')";
				$this->call_command("DB_QUERY",Array($sql));
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			}
			$newCatlist[$index][4] = $cat_identifier;
		}
		$this->category_list_load(Array("recache" => 1, "identifier"=>$cat_list_id));
		return array($newCatlist, $cat_list_id);
	}
	function create_client_details($parameters){
		$client_identifier = $this->check_parameters($parameters,"client_identifier",-1);
		/**
		* create reference for enterprise clients for FILES_ module to have specific category list
		*/
		
		$cat_identifier = $this->getUid();
		$sql="insert into category (cat_identifier, cat_client, cat_label, cat_parent, cat_list_id) values ($cat_identifier, $client_identifier, 'File Uploads (Virtual Folder List)',-1, $cat_identifier);";
		$this->call_command("DB_QUERY",array($sql));

		/**
		* create two virtual folders for starters
		-
		* we do not need to extract these as they are in the root of the category list
		* File uploaders can add new sub folders to these two at will.
		*/
		$clist = $cat_identifier;
		$cat_identifier = $this->getUid();
		$sql="insert into category (cat_identifier, cat_client, cat_label, cat_parent, cat_list_id) values ($cat_identifier, $client_identifier, 'Images', $clist, $clist);";
		$this->call_command("DB_QUERY",array($sql));
		$cat_identifier = $this->getUid();
		$sql="insert into category (cat_identifier, cat_client, cat_label, cat_parent, cat_list_id) values ($cat_identifier, $client_identifier, 'Documents', $clist, $clist);";
		$this->call_command("DB_QUERY",array($sql));
		/**
		* tell the File Manager to use this category list as virtual folders
		*/
		$sql="insert into category_belongs_to_module (cbtm_client, cbtm_clist, cbtm_module) values ($client_identifier,$clist,'FILES_');";
		$this->call_command("DB_QUERY",array($sql));
	}
	/**
    * import a new category
	* 
	* imports the label as a new category as a root level category ie the root category identifier is the parent of this new location
	* @param Array keys are ("label, "module", "identifier", "category_root")
	* @return Integer returns 1 when finished
    */
	function category_to_object_import($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,print_r($parameters,true)));}
		$label			=	$this->validate($this->check_parameters($parameters,"label"));
		$module			=	$this->check_parameters($parameters,"module");
		$identifier		=	$this->check_parameters($parameters,"identifier");
		$category_root	=	$this->check_parameters($parameters,"category_root");
		if($label==""){
		} else {
			$sql = "select * from category where cat_client = $this->client_identifier and cat_label='$label' and cat_list_id=$category_root";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->call_command("DB_QUERY",Array($sql));
			if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
				//insert new
				$cat_identifier = $this->getUid();
				$sql="insert into category (cat_identifier, cat_client, cat_label, cat_parent, cat_list_id) values ($cat_identifier, $this->client_identifier, '$label', $category_root, $category_root)";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->call_command("DB_QUERY",Array($sql));
				$this->clist[count($this->clist)] = Array(
					"cat_label"				=> $label, 
					"cat_parent"			=> $category_root, 
					"cat_identifier"		=> $cat_identifier, 
					"cat_list_id"			=> $category_root
				);
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::category length",__LINE__,"Category length :: ".count($this->clist)));}
			} else {
				// extract old
		        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	        		$cat_identifier = $r["cat_identifier"];
	    	    }
			}
			$this->call_command("DB_FREE",Array($result));
			$sql = "insert into category_to_object (cto_client, cto_object, cto_module, cto_clist) values ($this->client_identifier, $identifier, '$module', $cat_identifier)";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY",Array($sql));
		}
		return 1;
	}
	
	/**
    * get the bread crumb trail of this category
	*
	* use this function to produce a 
	* @param Array keys are ("object", "list", "category_list", "split_categories")
	* @return Array of paths (entries can belong to more than one category)
    */
	function get_bctrails($parameters){
		$bct = Array(); // bread crumb trails return array
		$object		= $this->check_parameters($parameters,"object");
		$list 		= $this->check_parameters($parameters,"list");
		$cat_list	= $this->check_parameters($parameters,"category_list");
		$split_categories	= $this->check_parameters($parameters,"split_categories");
		if ($this->clist_id == 0 || $this->clist_id != $cat_list){
			if ($this->clist_locked == 0){
				$this->clist_locked = 1;
				$this->clist = $this->category_list_load(
					Array(
						"identifier" => $cat_list,
						"returntype" => 0,
						"list" => $cat_list ,
						"rank" => 0
					)
				);
				$this->clist_id = $cat_list;
			}
		}
		if ($this->clist_id == $cat_list && $this->clist_locked == 0){
			$this->clist_locked = 1;
		}
		if ($this->clist_id == $cat_list && $this->clist_locked == 1){
			$sql = "select * from category_to_object 
					inner join information_entry on ie_client = cto_client and cto_object = ie_identifier and ie_list = $list
				where 
					cto_client=$this->client_identifier and cto_object = $object and cto_module='INFORMATIONADMIN_'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$paths_to_retrieve = Array();
			$m=0;
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        		$paths_to_retrieve[count($paths_to_retrieve)] = $r["cto_clist"];
				$m++;
    	    }
	        $this->call_command("DB_FREE",Array($result));
			for($i=0; $i<$m ;$i++){
				$bct[$i]= $this->get_path($paths_to_retrieve[$i], $cat_list, $split_categories);
			}
			$this->clist_locked=0;
		}
		return $bct;
	}
	/**
    * extract the path (breadcrumb) for a specific item
	*
	* if the categories parent is equal to the category list identifier then return the label
	* if the categories parent is not equal to the category list identifier then call this function recursivly until it is
	*
	* @param Integer Parent identifier
	* @param Integer category list identifier
	* @param String split the path with this character
	* @return String the label of the categories in the path concationated together with the split categories string
    */
	function get_path($p, $list, $split_categories=""){
		if ($split_categories==""){
			$split_categories=chr(187);	
		}
		/*
			"cat_label"		
			"cat_parent"	
			"cat_identifier"
			"cat_list_id"	
		*/
		$max = count($this->clist);
		for ($iz=0; $iz<$max; $iz++){
			if ($this->clist[$iz]["cat_identifier"] == $p){
				if ($this->clist[$iz]["cat_parent"] == $list){
					return $this->clist[$iz]["cat_label"];
				} else {
					return $this->get_path($this->clist[$iz]["cat_parent"], $list, $split_categories) . $split_categories . $this->clist[$iz]["cat_label"];
				} 
			}
		}
	}
	/**
    * import a string as a category path
	*
	* splits the "label" via the "split_categories" value and imports
	* @param Array keys are ("label", "module", "identifier", "category_root", "split_categories")
	* @return 1 on completion
    */
	function category_to_object_import_path($parameters){
		$label				=	$this->validate($this->check_parameters($parameters,"label"));
		$module				=	$this->check_parameters($parameters,"module");
		$identifier			=	$this->check_parameters($parameters,"identifier");
		$parent				=	$this->check_parameters($parameters,"category_root");
		$split_categories	=	$this->check_parameters($parameters,"split_categories",chr(187));
		if(!(strpos($label,$split_categories)===false)){
			$listofCategories  = split($split_categories, $label);
			$parent_existed=1;
			if ($this->clist_id == 0 || $this->clist_id != $parent){
				if ($this->clist_locked == 0){
					$this->clist_locked = 1;
					$this->clist = $this->category_list_load(
						Array(
							"identifier" => $parent,
							"returntype" => 0,
							"list" => $parent,
							"rank" => 0
						)
					);
					$this->clist_id = $parent;
				}
			}
			if ($this->clist_id == $parent && $this->clist_locked == 0){
				$this->clist_locked = 1;
			}
			if ($this->clist_id == $parent && $this->clist_locked == 1){
				$max_cat_count	= count($this->clist);
				$root			= $parent;
				$parent_existed	=1;
				$m				= count($listofCategories);
				for($i=0; $i<$m ; $i++){
					$listofCategories[$i] = trim($listofCategories[$i]);
					/**
					* 1. check each path section for existance
					* 2. if not exists then create and mark that parent did not exist (don't check any more just add children nodes)
					*/
					if ($parent_existed==1){
						$ok = 0;
						for ($index=0; $index<$max_cat_count; $index++){
							if($ok == 0){
								if ($this->clist[$index]["cat_label"] == $listofCategories[$i] && $this->clist[$index]["cat_parent"] == $parent){
									$ok = 1;
									$parent = $this->clist[$index]["cat_identifier"];
								}
							}
						}
						if ($ok == 0){
							$parent_existed = 0;
						}
					}
					if ($parent_existed == 0){
						//create new category entry
						// on last entry create new category_to_object reference
						$cat_identifier = $this->getUid();
						$sql = "insert into category (cat_identifier,cat_client, cat_label, cat_parent, cat_list_id) values ($cat_identifier, $this->client_identifier, '".$listofCategories[$i]."', $parent, $root)";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
						$this->call_command("DB_QUERY",Array($sql));
						$prev_parent = $parent;
                       	$parent = $cat_identifier;
						$this->clist[count($this->clist)] = Array(
							"cat_label"				=> $listofCategories[$i], 
							"cat_parent"			=> $prev_parent, 
							"cat_identifier"		=> $parent, 
							"cat_list_id"			=> $root
						);
						$max_cat_count	= count($this->clist);
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"clist",__LINE__,"".print_r($this->clist).""));}
					} else {

					}
				}
				$sql = "insert into category_to_object (cto_module, cto_client, cto_object, cto_clist) values ('".$module."', $this->client_identifier, $identifier, $parent)";
				$this->call_command("DB_QUERY",Array($sql));
				$this->clist_locked=0;
				/// return what ???
				return 1;
			}
		} else {
			return $this->category_to_object_import($parameters);
		}
	}

	
	function merge_categories($parameters){
		$info_identifier	= $this->check_parameters($parameters,"info_identifier");
		$identifier			= $this->check_parameters($parameters,"cat_src");
		$cat_merge			= $this->check_parameters($parameters,"cat_dest");
		$list_id  			= $this->check_parameters($parameters,"list_id");
		
		$sql = "update category_to_object set cto_clist=$cat_merge where cto_clist=$identifier and cto_client=$this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		// merge together
		$sql 	= "select * from category where cat_parent = $identifier and cat_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$rows = $this->call_command("DB_NUM_ROWS",Array($result));
		if ($rows==0){
			$this->call_command("INFORMATIONADMIN_RENAMEMOVE",
				Array(
					"cmd" 				=> "merge_remove",
					"info_identifier" 	=> $info_identifier,
					"cat_id" 			=> $identifier,
					"cat_parent" 		=> $cat_merge,
					"list_id"			=> $list_id
				)
			);
			$sql = "delete from category where cat_identifier=$identifier and cat_client=$this->client_identifier";
			$this->call_command("DB_QUERY",Array($sql));
		} else {
			$sql = "select * from category where cat_parent = $cat_merge and  cat_client=$this->client_identifier";
			$existing  = $this->call_command("DB_QUERY",Array($sql));
			$i=0;
			$list = Array();
            while($er = $this->call_command("DB_FETCH_ARRAY",Array($existing))){
            	$list[$i] = Array("id"=>$er["cat_identifier"], "label"=>$er["cat_label"], "parent"=>$er["cat_parent"]);
				$i++;
            }
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$ok_to_merge=0;
				$remove_uri="";
				$found=-1;
				for($z=0;$z<$i;$z++){
					if ($list[$z]["label"] == $r["cat_label"]){
						//merge = 1
						$ok_to_merge=1;
						$found=$z;
					}
				}
				if ($ok_to_merge==1){
					//merge
					$this->merge_categories(
						Array(
							"info_identifier" 	=> $info_identifier,
							"cat_src" 			=> $r["cat_identifier"],
							"cat_dest"	 		=> $list[$found]["id"],
							"list_id"			=> $list_id
						)
					);
					$parray = Array(
						"cmd" 				=> "merge",
						"info_identifier" 	=> $info_identifier,
						"cat_id" 			=> $r["cat_identifier"],
						"cat_parent" 		=> $list[$found]["parent"],
						"cat_parent_prev" 	=> $r["cat_parent"],
						"cat_label" 		=> $list[$found]["label"],
						"cat_label_prev" 	=> $list[$found]["label"],
						"list_id"			=> $list_id
					);
					#print_r($parray);
					$remove_uri = $this->call_command("INFORMATIONADMIN_RENAMEMOVE",$parray);
					$sql = "delete from category where cat_identifier=".$r["cat_identifier"]." and cat_client = $this->client_identifier";
					$this->call_command("DB_QUERY",Array($sql));
					//delete the (physical directory, the index page and the category record)
					@unlink($remove_uri);
					@rmdir(dirname($remove_uri));
				} else {
					// move
					$parray = Array(
						"cmd" 				=> "move",
						"info_identifier" 	=> $info_identifier,
						"cat_id" 			=> $r["cat_identifier"],
						"cat_parent" 		=> $cat_merge,
						"cat_parent_prev" 	=> $r["cat_parent"],
						"cat_label" 		=> $r["cat_label"],
						"cat_label_prev" 	=> $r["cat_label"],
						"list_id"			=> $list_id
					);
					$this->call_command("INFORMATIONADMIN_RENAMEMOVE",$parray);
					$sql ="update category set cat_parent = $cat_merge where cat_identifier = ".$r["cat_identifier"]." and cat_client=".$this->client_identifier;
					$this->call_command("DB_QUERY",Array($sql));
				}
            }
            $this->call_command("DB_FREE",Array($result));
	        $this->call_command("DB_FREE",Array($existing));
   		}
		$sql	= "select menu_url from menu_data inner join information_list on info_menu_location = menu_identifier where info_identifier=$info_identifier and info_category = $list_id";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$fake_uri = dirname($r["menu_url"]);
			$m_uri = dirname($r["menu_url"]);
		}
		$root  	= $this->check_parameters($this->parent->site_directories,"ROOT");
		$uri	= $root ."/".$fake_uri. $this->find_path($identifier);
		$m_uri	= $root ."/".$m_uri;
		if($m_uri!=$uri){
			@unlink($uri."/index.php");
			@rmdir($uri);
		}
		$sql 	= "delete from category where cat_identifier=$identifier and cat_client=$this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
	}
	function find_path($id){
		if ($id !=-1){
			$m = count($this->clist);
			for ($index=0; $index< $m ; $index++){
				if ($this->clist[$index]["cat_identifier"] == $id && $this->clist[$index]["cat_list_id"] != $id){
					if ($id !=-1){
						return $this->find_path($this->clist[$index]["cat_parent"]) ."/". $this->make_uri($this->clist[$index]["cat_label"]);
					} else {
						return "";
					}
				}
			}
		}
	}
	/**
	* checks to see if the path defined exists return Zero or One
	*
	* @return INTEGER
	*/
	function category_to_object_check_path($parameters){
		$label				=	$this->validate($this->check_parameters($parameters,"label"));
		$parent				=	$this->check_parameters($parameters,"category_root");
		$split_categories	=	$this->check_parameters($parameters,"split_categories",chr(187));

		if(strpos($label,$split_categories)===false){
			$listofCategories  = Array($label);
		} else {
			$listofCategories  = split($split_categories, $label);
		}
		$parent_existed=1;
		$m = count($listofCategories);
		if ($this->clist_id == 0 || $this->clist_id != $parent){
			if ($this->clist_locked == 0){
				$this->clist_locked = 1;
				$this->clist = $this->category_list_load(
					Array(
						"identifier" => $parent,
						"returntype" => 0,
						"list" => $parent,
						"rank" => 0
					)
				);
				$this->clist_id = $parent;
			}
		}
		if ($this->clist_id == $parent && $this->clist_locked == 0){
			$this->clist_locked = 1;
		}
		if ($this->clist_id == $parent && $this->clist_locked == 1){
			$max_cat_count = count($this->clist);
			$parent_existed=1;
			for($i=0; $i<$m ; $i++){
				$listofCategories[$i] = trim($listofCategories[$i]);
				/*
					1 check each path section for existance
					2. if not exists then create and mark that parent did not exist (don't check just add children)
				*/
				if ($parent_existed==1){
					$ok = 0;
					for ($index=0; $index<$max_cat_count; $index++){
						if($ok == 0){
							if (strtolower($this->clist[$index]["cat_label"]) == strtolower($listofCategories[$i]) && $this->clist[$index]["cat_parent"] == $parent){
								$ok = 1;
								$parent = $this->clist[$index]["cat_identifier"];
							}
						}
					}
					if ($ok == 0){
						$parent_existed = 0;
					}
				}
				if ($parent_existed == 0){
					return 1;
				}
			}
			$this->clist_locked=0;
		}
		return 0;
	}

	function tidy(){
		/**
	   	* get list of id's that do not link to a category anymore
	   	*/
		$sql = "SELECT cto_identifier
			FROM `category_to_object` 
			left outer join category on cat_identifier = cto_clist and cto_client = cat_client
			where cat_identifier is null and cto_client = $this->client_identifier";
        $result  = $this->call_command("DB_QUERY",Array($sql));
		$list ="";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if($list!=""){
				$list.=",";
			}
        	$list .=" ".$r["cto_identifier"];
        }
		$this->call_command("DB_FREE",Array($result));
		if($list!=""){
			$sql = "delete from category_to_object where cto_client = $this->client_identifier and cto_identifier in ($list)";
	        $result  = $this->call_command("DB_QUERY",Array($sql));
		}
	}

	/**
	* Add virtual Folders to clients
    *
    * if the client does not have any virtual folders define this will create some.
    */
	function update_modules_add_v_folders(){
		$sql = "SELECT * FROM client left outer join category_belongs_to_module on cbtm_client = client_identifier and cbtm_module='FILES_' where cbtm_client is null";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$this->create_client_details(
				Array("client_identifier" => $r["client_identifier"])
			);
        }
        $this->call_command("DB_FREE",Array($result));
	}
	/*************************************************************************************************************************
    * cache the defined category as part of the menu
    *************************************************************************************************************************/
	function category_cache_menu($parameters){
		$menu_identifier	= $this->check_parameters($parameters,"menu_identifier",-1);
		$list				= $this->check_parameters($parameters,"cat_root",-1);
		$info_in_menu		= $this->check_parameters($parameters,"info_in_menu",-1);
		if($list==-1 || $menu_identifier==-1){
			return "";
		}
		if($info_in_menu==1){
			$sql = "select * from menu_data where menu_identifier = $menu_identifier and menu_client = $this->client_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$depth= 0;
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
    	    	$depth = $r["menu_depth"];
        		$menu_url	= dirname($r["menu_url"]);
	        }
			$depth = count(split("/",$menu_url));
        	$this->call_command("DB_FREE",Array($result));
			$len = count($this->loadedCatList);
			if($len ==0){
				$this->category_list_load(Array("identifier"=>$identifier, "returntype"=>"__ARRAY__", "list"=>$list, "rank"=>1));
				$len = count($this->loadedCatList);
			}
			/*************************************************************************************************************************
	        * work out the parent identifiers
        	*************************************************************************************************************************/
			$p_ids = Array();
			for($i=0;$i<$len;$i++){
				if(!in_array($this->loadedCatList[$i]["cat_parent"],$p_ids)){
					$p_ids[count($p_ids)] = $this->loadedCatList[$i]["cat_parent"];
				}
			}
			/*************************************************************************************************************************
	        * now that we have a list of the categories build the bullet list
        	*************************************************************************************************************************/
			$depth++;
			$out = "<fake_menu url='$menu_url'><![CDATA[";
			$out .= "<ul class='level".$depth."'>";
			$depth++;
			for($i=1;$i<$len;$i++){
				if ($this->loadedCatList[$i]["cat_parent"]==$list){
					$uri = $this->make_uri($this->loadedCatList[$i]["cat_label"]);
					if (in_array($this->loadedCatList[$i]["cat_identifier"],$p_ids)){
	//					$this->loadedCatList[$zi]["cat_url"] = $crumb_path."".$this->make_uri($this->loadedCatList[$zi]["cat_label"]);
						$out .= "<li class='folder'><a class='submenu' href='$menu_url/$uri/index.php' title='".$this->loadedCatList[$i]["cat_label"]."'>".$this->loadedCatList[$i]["cat_label"]."</a><ul class='level".$depth."'>	
								".$this->bullet($depth+1,$this->loadedCatList[$i]["cat_identifier"],$p_ids, $menu_url."/$uri/")."
							</ul></li>";
					} else{
						$out .= "<li><a href='$menu_url/$uri/index.php' title='".$this->loadedCatList[$i]["cat_label"]."'>".$this->loadedCatList[$i]["cat_label"]."</a></li>";
					}
				}
			}
			$out .= "</ul>]]></fake_menu>";
			$data_files 	= $this->parent->site_directories["DATA_FILES_DIR"];
			$fp = fopen($data_files."/cat_menu_".$this->client_identifier."_".$menu_identifier.".xml","w");
			fwrite($fp, $out);
			fclose($fp);
			$um = umask(0);
			@chmod($file, LS__FILE_PERMISSION);
			umask($um);
			$sql ="update menu_data set menu_plus=1 where menu_client = $this->client_identifier and menu_identifier = $menu_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
		} else {
			$data_files 	= $this->parent->site_directories["DATA_FILES_DIR"];
			@unlink($data_files."/cat_menu_".$this->client_identifier."_".$menu_identifier.".xml");
			$sql ="update menu_data set menu_plus=0 where menu_client = $this->client_identifier and menu_identifier = $menu_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
		}
	}
	
	function bullet($d,$p,$plist, $menu_url){
		$len = count($this->loadedCatList);
		$out = "";
		for($i=1;$i<$len;$i++){
			if ($this->loadedCatList[$i]["cat_parent"]==$p){
				$uri = $this->make_uri($this->loadedCatList[$i]["cat_label"]);
				if (in_array($this->loadedCatList[$i]["cat_identifier"],$plist)){
					$out .= "<li class='folder'><a class='submenu' href='$menu_url".$uri."/index.php' title='".$this->loadedCatList[$i]["cat_label"]."'>".$this->loadedCatList[$i]["cat_label"]."</a><ul class='level".$depth."'>";
					$out .= $this->bullet($depth+1,$this->loadedCatList[$i]["cat_identifier"],$plist, $menu_url."$uri/");
					$out .= "</ul></li>";
				} else{
					$out .= "<li><a href='$menu_url".$uri."/index.php' title='".$this->loadedCatList[$i]["cat_label"]."'>".$this->loadedCatList[$i]["cat_label"]."</a></li>";
				}
			}
		}
		return $out;
	}
}
?>