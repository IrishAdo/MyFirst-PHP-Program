<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.information_admin.php
* @date 12 Feb 2004
*/
/**
* This module is the administration module for Categories it will allow the user to 
* generate Category LISTS which will contain lists of categories that could be used
* by one or more modules.
*/

class imagerotator_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name_label			= "Image Rotator Manager Module (Administration)";
	var $module_name				= "imagerotator_admin";
	var $module_admin				= "1";
	var $module_command				= "IMAGEROTATORADMIN_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_IMAGEROTATOR";
	var $module_modify	 			= '$Date: 2005/02/09 12:06:50 $';
	var $module_version 			= '$Revision: 1.16 $';
	var $module_creation 			= "26/02/2004";
	var $searched					= 0;
	var $loadedcat					= Array();
	var $webContainer				= "IMAGEROTATOR_";
	/**
	*  Management Menu entries
	*/
	
	var $module_admin_options 		= array(
	);
	
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access = array(
		array("IMAGEROTATORADMIN_ALL",			"COMPLETE_ACCESS"),
		array("IMAGEROTATORADMIN_LIST_CREATOR",	"ACCESS_LEVEL_LIST_AUTHOR"),  // this will allow the user to add a new category to the system
		array("IMAGEROTATORADMIN_CREATOR",		"ACCESS_LEVEL_AUTHOR"),  // this will allow the user to add a new category to the system
		array("IMAGEROTATORADMIN_EDITOR",		"ACCESS_LEVEL_EDITOR"),  // this user role will allow the user to edit and remove categories.
		array("IMAGEROTATORADMIN_APPROVER",		"ACCESS_LEVEL_APPROVER") // this will allow the user to 
	);
	
	/**
	*  Channel options
	*/
	var $module_display_options 	= array(
//		array("IMAGEROTATOR_DISPLAY",	LOCALE_DISPLAY_IMAGE_ROTATOR)
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
	//	array(2,"Image Rotator","WEBOBJECTS_SHOW_GUESTBOOK",0,0)
	);
	
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
	var $add_information_lists		= 0;
	var $install_access				= 0;
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
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return "";$this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
/*			if ($user_command==$this->module_command."WEBOBJECT_LIST"){
				return $this->listwebobjects($parameters);
			}*/
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Create table function allow access if in install mode
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Administration Module commands
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($this->admin_access==1){
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- What channels are available to the system
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($user_command == $this->module_command."MENU_DISPLAY_OPTIONS"){
					return $this->display_channels($parameter_list);
				}
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- Category List Setup and management
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($user_command == $this->module_command."LIST"){
					return $this->module_list($parameter_list);
				}
				if ($user_command==$this->module_command."INHERIT"){
					$this->inherit($parameter_list);
				}
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- List Category Management Access
				- -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- this functionality will allow you to modify the category list details
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($this->add_information_lists){
					if (($user_command==$this->module_command."LIST_EDIT") || ($user_command==$this->module_command."LIST_ADD")){
						return $this->module_list_modify($parameter_list);
					}
					if ($user_command==$this->module_command."LIST_REMOVE"){
						$this->module_list_removal($parameter_list);
						$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
								"owner_module" 	=> $this->webContainer,
								"owner_id" 		=> $this->check_parameters($parameter_list,"identifier"),
								"cmd"			=> "REMOVE"
							)
						);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
					if ($user_command==$this->module_command."LIST_SAVE"){
						$identifier = $this->module_list_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
				}
			}
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                D I R E C T O R Y   S E T U P   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	/**
	* Initialise function
	*
	* This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	* module::initialise() function allowing you to define any extra constructor
	* functionality.
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
		$this->add_information_lists	= 0;
	
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
	
		/**
		* define the admin access that this user has.
		*/
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
					("IMAGEROTATORADMIN_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("IMAGEROTATORADMIN_CREATOR"==$access[$index])
				){
					$this->author_admin_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("IMAGEROTATORADMIN_ALL"==$access[$index]) ||
					("IMAGEROTATORADMIN_LIST_CREATOR"==$access[$index])
				){
					$this->add_information_lists=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("IMAGEROTATORADMIN_ALL"==$access[$index]) ||
					("IMAGEROTATORADMIN_EDITOR"==$access[$index])
				){
					$this->editor_admin_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("IMAGEROTATORADMIN_ALL"==$access[$index]) ||
					("IMAGEROTATORADMIN_APPROVER"==$access[$index])
				){
					$this->approve_admin_access=0;
				}
			}
		}
		if (($this->approve_admin_access || $this->editor_admin_access || $this->add_information_lists || $this->author_admin_access ) && (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files"))){
			$this->list_access=1;
			$this->admin_access=1;
		}
		$this->module_admin_options[count($this->module_admin_options)] = array("IMAGEROTATORADMIN_LIST", "MANAGE_IMAGEROTATOR","");
/*
		$sql ="select * from imagerotator_list";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	
        }
        $this->call_command("DB_FREE",Array($result));
*/
		$this->module_display_options 	= array(
			array("IMAGEROTATOR_DISPLAY",	LOCALE_DISPLAY_IMAGE_ROTATOR)
		);
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
		* Table structure for table 'guestbooks_list'
		-
		* the guest books_list table holds the list of guset books that are avaialble on the site
		*/
		$fields = array(
			array("irl_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("irl_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("irl_number"			,"unsigned small integer"	,"NOT NULL"	,"default '0'", "key"),
			array("irl_direction"		,"unsigned small integer"	,"NOT NULL" ,"default '0'", "key"),
			array("irl_type"			,"unsigned small integer"	,"NOT NULL" ,"default '0'", "key"),
			array("irl_created"			,"datetime"					,"NOT NULL" ,"default ''"),
			array("irl_created_by"		,"unsigned integer"			,"NOT NULL" ,"default ''"),
			array("irl_label"			,"varchar(255)"				,"NOT NULL" ,"default '0'", "key"),
			array("irl_all_locations"	,"unsigned small integer"	,"NOT NULL" ,"default '1'"),
			array("irl_hspace"			,"unsigned small integer"	,"NOT NULL" ,"default ''"),
			array("irl_vspace"			,"unsigned small integer"	,"NOT NULL" ,"default ''"),
			array("irl_width"			,"unsigned integer"	,"NOT NULL" ,"default ''"),
			array("irl_height"			,"unsigned integer"	,"NOT NULL" ,"default ''"),
			array("irl_set_inheritance"	,"unsigned small integer"	,"NOT NULL" ,"default '1'"),
			array("irl_align"			,"unsigned small integer"	,"NOT NULL" ,"default '0'")
		);
		
		$primary ="irl_identifier";
		$tables[count($tables)] = array("imagerotate_list", $fields, $primary);
		
		
		$fields = array(
			array("irlf_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("irlf_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("irlf_list"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("irlf_rank"		,"unsigned small integer"	,"NOT NULL"	,"default '0'", "key"),
			array("irlf_file"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key")
		);
		$primary ="irlf_identifier";
		$tables[count($tables)] = array("imagerotate_filelist", $fields, $primary);
/*		
		$fields = array(
			array("irlm_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("irlm_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("irlm_list"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key"),
			array("irlm_menu"		,"unsigned integer"			,"NOT NULL"	,"default '0'", "key")
		);
		$primary ="irlm_identifier";
		$tables[count($tables)] = array("imagerotate_menus", $fields, $primary);
*/
		return $tables;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-              I N F O R M A T I O N   D I R E C T O R Y   M A N A G E R   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: module_list($parameters) 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_list($parameters){
		$debug = $this->debugit(false,$parameters);
		$sql = "select * from imagerotate_list where irl_client = $this->client_identifier order by irl_identifier desc";
		if ($debug) print "<p>:: ".__FILE__." @ ".__LINE__." ::<br/>$sql</p>";
		$out = "";
		$result = $this->call_command("DB_QUERY",Array($sql));
		if (!$result){
			if ($debug) print "<p>:: ".__FILE__." @ ".__LINE__." ::<br/> no result pointer returned</p>";
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
			if ($debug) print "<p>:: ".__FILE__." @ ".__LINE__." ::<br/>$number_of_records</p>";
			$page = $this->check_parameters($parameters,"page",1);
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
			
			$variables["PAGE_BUTTONS"] = Array();
				$variables["PAGE_BUTTONS"][0] = Array("ADD",$this->module_command."LIST_ADD", ADD_NEW);
			if ($this->add_information_lists == 1){
			}
			if ($debug) print "<p>:: ".__FILE__." @ ".__LINE__." ::<br/> here </p>";
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
			
			if (($start_page + $this->page_size)>$num_pages){
				$end_page	 =	$num_pages;
			}else{
				$end_page	=	$this->page_size;
			}
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= "";
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			$page_discussion=0;
			$trans_page=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["irl_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		$this->check_parameters($r,"irl_label",""), "TITLE")
					)
				);
				if ($this->author_admin_access || $this->editor_admin_access){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."LIST_EDIT",EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."LIST_REMOVE",REMOVE_EXISTING);
				}
			}
			$this->page_size = $prev;
			
			return $this->generate_list($variables);
		}
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: module_list_modify($parameters) 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	$ok defines if the user can edit this 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_list_modify($parameters){
		$debug 							= $this->debugit(false,$parameters);
		$ok								= true;
		$identifier 					= $this->check_parameters($parameters,"identifier",-1);
		$display_tab					= $this->check_parameters($parameters, "display_tab", "");
		$menu_locations 				= Array();
		$out							= "";
		$form_label						= "";
		$file_list						= "";
		$irl_number						= 1;
		$irl_direction					= 1;
		$irl_label						= "";
		$irl_type						= "";
		$file_associations				= "";
		$file_associations_identifiers	= "";
		$all_locations					= 1;
		$irl_vspace						= 0;
		$irl_hspace						= 0;
		$irl_width						= "";
		$irl_height						= "";
		$set_inheritance				= 1;
		$irl_align						= 0;
		if ($identifier!=-1){
			$ok = false;
			$sql ="select * from imagerotate_list where irl_client = $this->client_identifier and irl_identifier = '$identifier'";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result		= $this->call_command("DB_QUERY",Array($sql));
			if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
				while ($r	= $this->call_command("DB_FETCH_ARRAY",array($result))){
					$irl_number		= $r["irl_number"];
					$irl_direction	= $r["irl_direction"];
					$irl_label		= $r["irl_label"];
					$irl_type		= $r["irl_type"];
					$all_locations	= $r["irl_all_locations"];
					$irl_vspace		= $r["irl_vspace"];
					$irl_hspace		= $r["irl_hspace"];
					$irl_width		= $r["irl_width"];
					$irl_height		= $r["irl_height"];
					$set_inheritance= $r["irl_set_inheritance"];
					$irl_align		= $r["irl_align"];
				}
				$ok = true;
			}
			$this->call_command("DB_FREE",Array($result));
			
			$sql = "select distinct irlf_file, file_info.* from imagerotate_filelist 
						inner join file_info on (irlf_file = file_identifier and file_client= irlf_client)
				where irlf_client=$this->client_identifier and irlf_list = $identifier order by irlf_rank";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result		= $this->call_command("DB_QUERY",Array($sql));
			if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
				while ($r	= $this->call_command("DB_FETCH_ARRAY",array($result))){
					$file_associations_identifiers .="".$r["irlf_file"].",";
					$file_associations  .= "<file_info logo='/libertas_images/icons/mime-images/".$this->call_command("FILES_GET_MIME_IMAGE",Array($r["file_name"])).".gif' identifier='".$r["file_identifier"]."' rank='".$this->check_parameters($r,"irlf_rank")."'><![CDATA[".$r["file_label"]."]]></file_info>";
				}
				$ok = true;
			}
			$this->call_command("DB_FREE",Array($result));
			
			$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
				Array(
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier
				)
			);
		}
		if ($ok){
			$out .="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .= "<page_options>";
			$out .= "<header><![CDATA[Image Rotator - List Manager - $form_label]]></header>";
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
			$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("PREVIEW","IMAGEROTATOR_DISPLAY_PREVIEW",ENTRY_PREVIEW));
			$out .="</page_options>";
			$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
			$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."LIST_SAVE\" />";
			$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
			$out .="		<input type=\"hidden\" name=\"file_associations\" value=\"$file_associations_identifiers\" />";
			
			$out .="		<page_sections>";
			$out .="		<section label='List Setup'>";
			$out .="			<input type=\"text\" name=\"irl_label\" label=\"Image List Label\" size=\"255\" required='YES'><![CDATA[$irl_label]]></input>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Display type of list
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$web_containers = split("~----~",$this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>$this->webContainer, "identifier"=>$identifier)));
			if ($web_containers[0]!=""){
				$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
				$out .= 			"<checkboxes type='vertical' name='irl_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Display menu locations
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .= "</section>";
			$out .= $this->location_tab($all_locations, $set_inheritance,$menu_locations, $display_tab);
			$out .= "		<section label=\"Image List\" name=\"file_associations_tab\" command=\"FILES_LIST&amp;file_mime=image&amp;lock_mime=1\" link='file_associations' return_command='FILES_LIST_FILE_DETAIL'";
			if ($display_tab=="files"){
				$out .= " selected='true'";
			}
			$file_associations ="
					<file_list report='normal'>
						<option><cmd><![CDATA[FILES_LIST&amp;file_mime=image&amp;lock_mime=1]]></cmd><label><![CDATA[".LOCALE_SELECT_FILE."]]></label></option>
						<option><cmd><![CDATA[FILES_ADD]]></cmd><label><![CDATA[".LOCALE_UPLOAD_FILE."]]></label></option>
						$file_associations
					</file_list>
			";
			$out .= ">$file_associations</section>";
			$out .="		<section label='Advanced'>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Display how many images
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .= 			"<select name='irl_number' label='Display # number of images'>";
			for($index=1;$index<=6;$index++){
				$out .= 			"<option value='$index'";
				if ($index==$irl_number){
					$out .= " selected ='true'";
				}
				$out .= 				">$index images</option>";
			}
			$out .= 				"</select>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Display display direction
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .= 			"<select name='irl_direction' label='Direction of images'>
									<option value='0'";
			if($irl_direction==0){
				$out .= " selected='true'";
			}
			$out .=					"><![CDATA[Left to right (row)]]></option>
									<option value='1'";
			if($irl_direction==1){
				$out .= " selected='true'";
			}
			$out .=					"><![CDATA[One above the other (column)]]></option>
								 </select>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Display type of list
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .= 			"<select name='irl_type' label='Define List behaviour'>
									<option value='0'";
			if($irl_type==0){
				$out .= " selected='true'";
			}
			$out .=					"><![CDATA[Display different random images after every page refresh]]></option>
									<option value='1'";
			if($irl_type==1){
				$out .= " selected='true'";
			}
			$out .=					"><![CDATA[Use same images for life of users visit to site]]></option>
									<option value='2'";
			if($irl_type==2){
				$out .= " selected='true'";
			}
			$out .=					"><![CDATA[Cycle through list in ranked order]]></option>
								 </select>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- What Hspace
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .= 			"<select name='irl_hspace' label='".LOCALE_HSPACE."'>";
			for($index=0;$index<=10;$index++){
				$out .= 			"<option value='$index'";
				if ($index==$irl_hspace){
					$out .= " selected ='true'";
				}
				$out .= 				">$index px</option>";
			}
			$out .= 				"</select>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- What Vspace
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .= 			"<select name='irl_vspace' label='".LOCALE_VSPACE."'>";
			for($index=0;$index<=10;$index++){
				$out .= 			"<option value='$index'";
				if ($index==$irl_vspace){
					$out .= " selected ='true'";
				}
				$out .= 				">$index px</option>";
			}
			$out .= 				"</select>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- What Width
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .= 			"<input type='text' maxlength='5' name='irl_width' label='".LOCALE_WIDTH."'><![CDATA[$irl_width]]></input>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- What Height
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .= 			"<input type='text' maxlength='5' name='irl_height' label='".LOCALE_HEIGHT."'><![CDATA[$irl_height]]></input>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- What Height
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .= 			"<select name='irl_align' label='".LOCALE_ALIGN."'>";
			$out .=				"<option value='0' ";
			if ($irl_align=="0"){
				$out .=				" selected='test'";
			}
			$out .=				">".LOCALE_ALIGN_LEFT."</option>";
			$out .=				"<option value='1' ";
			if ($irl_align=="1"){
				$out .=				" selected='test'";
			}
			$out .=				">".LOCALE_ALIGN_CENTER."</option>";
			$out .=				"<option value='2' ";
			if ($irl_align=="2"){
				$out .=				" selected='test'";
			}
			$out .=				">".LOCALE_ALIGN_RIGHT."</option>";
			$out .=				"</select>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- end of Advanced Section
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .="		</section>";
			
			$out .="		</page_sections>";
			$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
			$out .="	</form>";
			$out .="</module>";
		} 
		return $out;
	}
	
	function module_list_save($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"".print_r($parameters,true).""));}
		$debug 					= $this->debugit(false, $parameters);
		$identifier				= $this->check_parameters($parameters,"identifier",-1);
		$file_associations		= $this->check_parameters($parameters,"file_associations");
		$irl_label				= htmlentities(trim($this->strip_tidy($this->check_parameters($parameters,"irl_label"))));
		$irl_number				= $this->check_parameters($parameters,"irl_number",3);
		$irl_direction			= $this->check_parameters($parameters,"irl_direction",0);
		$irl_type				= $this->check_parameters($parameters,"irl_type",0);
		$irl_vspace				= $this->check_parameters($parameters,"irl_vspace",0);
		$irl_hspace				= $this->check_parameters($parameters,"irl_hspace",0);
		$irl_width				= $this->check_parameters($parameters,"irl_width");
		$irl_height				= $this->check_parameters($parameters,"irl_height");
		$irl_all_locations		= $this->check_parameters($parameters,"all_locations",0);
		$irl_menu_locations		= $this->check_parameters($parameters,"menu_locations");
		$irl_align				= $this->check_parameters($parameters,"irl_align",0);
		$currentlyhave			= $this->check_parameters($parameters,"currentlyhave");
		$count_irl_containers	= $this->check_parameters($parameters,"totalnumberofchecks_irl_containers");
		$set_inheritance		= $this->check_parameters($parameters,"set_inheritance",0);
		$replacelist=Array();
		/*
		for($index=1 ; $index <= $count_irl_containers; $index++){
			$irl_containers	= $this->check_parameters($parameters,"irl_containers_$index",Array());
			$len = count($irl_containers);
			for($i=0;$i < $len; $i++){
				$replacelist[count($replacelist)] = $irl_containers[$i];
			}
		}*/
		$replacelist	= $this->check_parameters($parameters,"irl_containers",Array());
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>id :: '".$identifier	."',<br/>files :: '".$file_associations	."',<br/>
							label :: '".$irl_label	."',
							<br/>number :: '". $irl_number	."',<br> dir :: '". $irl_direction ."',<br> type :: '". $irl_type ."',<br> locations ::'".$irl_menu_locations	."'</p>";
//print_r($irl_menu_locations);
//		$this->exitprogram();
		if ($identifier==-1){
			$sql = "insert into imagerotate_list (irl_client, irl_label, irl_number, irl_type, irl_direction, irl_created, irl_created_by, irl_all_locations, irl_vspace, irl_hspace, irl_width, irl_height, irl_set_inheritance, irl_align) values ('".$this->client_identifier."', '".$irl_label."', '".$irl_number."', '".$irl_type."', '".$irl_direction."', '$now', '".$_SESSION["SESSION_USER_IDENTIFIER"]."', '$irl_all_locations', '$irl_vspace', '$irl_hspace', '$irl_width', '$irl_height', $set_inheritance, $irl_align)";
			$this->call_command("DB_QUERY",Array($sql));
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$sql = "select * from imagerotate_list where irl_client = $this->client_identifier and irl_created = '$now' and irl_label='$irl_label' and irl_type='$irl_type' and irl_align='$irl_align' and irl_created_by='".$_SESSION["SESSION_USER_IDENTIFIER"]."'";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result		= $this->call_command("DB_QUERY",Array($sql));
			if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
				while ($r	= $this->call_command("DB_FETCH_ARRAY",array($result))){
					$identifier	= $r["irl_identifier"];
				}
			}
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $irl_label,
					"wo_command"	=> "IMAGEROTATOR_DISPLAY",
					"cmd"			=> "ADD",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
		} else {
			$sql = "update imagerotate_list set irl_align='$irl_align', irl_label='".$irl_label."', irl_number='".$irl_number."', irl_type='".$irl_type."', irl_direction='".$irl_direction."', irl_all_locations='$irl_all_locations', irl_vspace='$irl_vspace', irl_hspace='$irl_hspace', irl_width='$irl_width', irl_height='$irl_height' where irl_client='".$this->client_identifier."' and irl_identifier=$identifier";
			
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			//print $sql;
			$this->call_command("DB_QUERY",Array($sql));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $irl_label,
					"wo_command"	=> "IMAGEROTATOR_DISPLAY",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
		}
		
		$file_associations_list = split(",",$file_associations);
		$max_files = count($file_associations_list);
		$sql = "delete from  imagerotate_filelist where irlf_client=$this->client_identifier and irlf_list=$identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->call_command("DB_QUERY",Array($sql));
		for($index=0; $index<$max_files;$index++){
			if (trim($file_associations_list[$index])!=""){
				$sql = "insert into imagerotate_filelist (irlf_client, irlf_list, irlf_file, irlf_rank) values ($this->client_identifier, $identifier, ".$file_associations_list[$index].",$index)";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->call_command("DB_QUERY",Array($sql));
			}
		}
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $irl_menu_locations,
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier,
				"all_locations"	=> $irl_all_locations
			)
		);

/*
		$sql = "delete from imagerotate_menus where irlm_client=$this->client_identifier and irlm_list=$identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->call_command("DB_QUERY",Array($sql));
		if ($irl_all_locations==0){
			if (is_array($irl_menu_locations)){
				$max_menus = count($irl_menu_locations);
				for($index=0; $index<$max_menus;$index++){
					$sql = "insert into imagerotate_menus (irlm_client, irlm_list, irlm_menu) values ($this->client_identifier, $identifier, ".$irl_menu_locations[$index].")";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					$this->call_command("DB_QUERY",Array($sql));
				}
			}
		}
*/		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance("IMAGEROTATOR_DISPLAY",$irl_menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=> $child_locations,
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier,
					"all_locations"	=> $irl_all_locations,
					"delete"		=> 0
				)
			);
			$this->set_inheritance(
				"IMAGEROTATOR_DISPLAY",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"		=> $this->webContainer,
					"condition"		=> "irl_set_inheritance=1 and ",
					"client_field"	=> "irl_client",
					"table"			=> "imagerotate_list",
					"primary"		=> "irl_identifier"
					)
				).""
			);
/*			$len = count($child_locations);
			for($i=0;$i<$len;$i++){
				$sql = "insert into imagerotate_menus (irlm_client, irlm_list, irlm_menu) values ($this->client_identifier, $identifier, ".$child_locations[$i].")";
				$this->call_command("DB_QUERY",array($sql));
			}
			$this->set_inheritance(
				"IMAGEROTATOR_DISPLAY",
				"select distinct irlm_menu as menu_id from imagerotate_menus 
					inner join imagerotate_list on 
								(irlm_list = irl_identifier and 
								irlm_client=irl_client and irl_set_inheritance =1)
					where irlm_client=$this->client_identifier"
			);
*/
		}
		$this->tidyup_display_commands($parameters);
		if ($debug) $this->exitprogram();
		return $identifier;
	}
	
	function module_list_removal($parameters){
		$debug = $this->debugit(false,$parameters);
		$identifier			= $this->check_parameters($parameters,"identifier",-1);
		$sql = "delete from imagerotate_list where irl_identifier = $identifier and irl_client=$this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->call_command("DB_QUERY",Array($sql));
		$this->call_command("LAYOUT_MENU_TO_OBJECT_REMOVE", Array(
				"module"		=>	$this->webContainer, 
				"identifier"	=>	$identifier
			)
		);
/*
		$sql = "delete from imagerotate_menus where irlm_list = $identifier and irlm_client=$this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->call_command("DB_QUERY",Array($sql));
*/
		$sql = "delete from imagerotate_filelist where irlf_list = $identifier and irlf_client=$this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->call_command("DB_QUERY",Array($sql));
	}
	
	function tidyup_display_commands($parameters){
		$debug = $this->debugit(false, $parameters);
		$irl_all_locations = $this->check_parameters($parameters,"irl_all_locations",0);
		$sql ="select * from imagerotate_list where irl_client=$this->client_identifier and irl_all_locations=1";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->call_command("DB_QUERY",Array($sql));
    	$num = $this->call_command("DB_NUM_ROWS",Array($result));
/*
		if ($num==0){
			$sql ="select irlm_menu as m_id from imagerotate_menus where irlm_client=$this->client_identifier";
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>"IMAGEROTATOR_DISPLAY","status"=>"OFF"));
		} else {
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>"IMAGEROTATOR_DISPLAY","status"=>"ON"));
			$sql ="select menu_identifier as m_id from menu_data where menu_client=$this->client_identifier";
		}
*/
		if ($num==0){
			$sql ="select distinct menu_to_object.mto_menu as m_id from menu_to_object where mto_client=$this->client_identifier and mto_module='$this->webContainer'";
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>$this->webContainer."DISPLAY","status"=>"OFF"));
		} else {
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>$this->webContainer."DISPLAY","status"=>"ON"));
			$sql ="select distinct menu_identifier as m_id from menu_data where menu_client=$this->client_identifier";
		}
		
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->call_command("DB_QUERY",Array($sql));
   		$sql = "delete from display_data where display_client=$this->client_identifier and display_command='IMAGEROTATOR_DISPLAY'";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->call_command("DB_QUERY",Array($sql));
   		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$sql = "insert into display_data (display_client, display_command, display_menu) values ($this->client_identifier, 'IMAGEROTATOR_DISPLAY', ".$r["m_id"].")";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY",Array($sql));
       	}
		$this->call_command("DB_FREE",Array($result));
	}

	function inherit($parameters){
		$menu_id		= $this->check_parameters($parameters,"menu_identifier",-1);
		$menu_parent 	= $this->check_parameters($parameters,"menu_parent",-1);
		$this->call_command("LAYOUT_MENU_TO_OBJECT_INHERIT",Array(
			"menu_location"	=> $menu_id,
			"menu_parent"	=> $menu_parent,
			"module"		=> $this->webContainer,
			"condition"		=> "irl_set_inheritance =1 and ",
			"client_field"	=> "irl_client",
			"table"			=> "imagerotate_list",
			"primary"		=> "irl_identifier"
			)
		);
	}

}
?>
