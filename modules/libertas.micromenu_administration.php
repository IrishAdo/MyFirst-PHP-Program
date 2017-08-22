<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.micromenu_administration.php
*/
/**
*
* This module is for producing micro menus for displaying ont he site.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
class micromenu_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_name				= "micromenu_admin";
	var $module_name_label			= "Micro Menu Module (Adminsitration)";
	var $module_admin				= "1";
	var $module_command				= "MICROMENUADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "MICROMENU_";
	var $module_label				= "MANAGEMENT_MICROMENU";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.17 $';
	var $module_creation 			= "13/08/2004";
	var $searched					= 0;
	
	var $admin_access				= 0;	
	var $admin_function_access		= 0;	
	var $available_forms			= array();

	/**
	* WebObject entries
	*/
	var $WebObjects				 	= array();
	
	/**
	*  display options
	*/
	var $display_options			= array();
	/**
	*  module channel display options
	*/
	var $module_display_options 	= array(
		array("MICROMENU_DISPLAY", "MICROMENU_DISPLAY_CHANNEL")
	);
	
	/**
	*  Administrative Menu Commands
	*
	*  format :
	- 	0 => Command
	-	1 => Label
	-	2 => Roles empty for all roles have access
	-	3 => Menu Path 
				ie LOCALE1/LOCALE2/LOCALE3/LOCALE4 will create a tree structure 4 levels
				deep
	*/
	var $module_admin_options 		= array(
		Array("MICROMENUADMIN_LIST", "LOCALE_MICROMENU_LIST","")
	);
	
	// no role access
	var $module_admin_user_access = array();
	/**
	*  Class Methods
	*/
	
	function command($user_command, $parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,print_r($parameter_list,true),__LINE__,"command"));
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
			if ($user_command==$this->module_command."GET_WEB_CONTAINER1"){
				return $this->webContainer;
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Create table function allow access if in install mode
			- non secure as all this will attempt to do is return an array that contains the definition of the table 
			- structures does not execute them.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Secure Administrative functions requires mode ADMIN 
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($this->admin_function_access==1){
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- Secure Administrative functions requires mode ADMIN and Role Access
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($this->admin_access) {
					if ($user_command==$this->module_command."LIST_FORMS"){
						return $this->available_forms;
					}
					if ($user_command==$this->module_command."LIST"){
						return $this->manage_list($parameter_list);
					}
					if ($user_command==$this->module_command."ADD" || $user_command==$this->module_command."EDIT"){
						return $this->module_form($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE_CONFIRM"){
						$this->module_remove_confirm($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
					if ($user_command==$this->module_command."SAVE"){
						$this->module_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
					if ($user_command==$this->module_command."RESTORE"){
						$this->module_restore($parameter_list);
					}
				}
			}
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                			S E T U P   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- 
	- 
	*
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
		$this->load_locale($this->module_name);
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		$this->page_size				= $this->check_prefs(Array("sp_page_size"));
		/**
		* define the admin access that this user has.
		*/
		$this->admin_function_access	= 0;
		$this->admin_access				= 0;
		
		$this->available_forms = Array();
		
		$sql = "select * from micromenu_list where micromenu_status =1 and micromenu_client = $this->client_identifier";
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$this->available_forms[count($this->available_forms)] = Array("id"=>"libertas_mm_".$r["micromenu_identifier"], "label"=>$r["micromenu_label"]);
        }
        $this->call_command("DB_FREE",Array($result));
		/**
		* define the admin access that this user has.
		*/
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_array = array();
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (($this->module_command."ALL"==$access[$index]) || ("ALL"==$access[$index])){
					$this->admin_access=1;
				}
			}
		}
		if ($this->parent->module_type=="admin" || $this->parent->module_type=="preview" || $this->parent->module_type=="files"){
			$this->admin_function_access = 1;
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
		* Table structure for table 'micromenu_list'
		*/
		$fields = array(
			array("micromenu_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("micromenu_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("micromenu_label"				,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("micromenu_creation_date"		,"datetime"					,"NOT NULL"	,"default '0000-00-00 00:00:00'"),
			array("micromenu_status"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("micromenu_display_type"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("micromenu_extract_type"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("micromenu_all_locations"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("micromenu_set_inheritance"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("micromenu_show_label"		,"unsigned small integer"	,"NOT NULL"	,"default '1'"),
			array("micromenu_parent"			,"signed integer"			,"NOT NULL"	,"default '-1'"),
			array("micromenu_show_type"			,"unsigned small integer"	,"NOT NULL"	,"default '0'")
			
		);
		$primary ="micromenu_identifier";
		$tables[count($tables)] = array("micromenu_list", $fields, $primary);
		/**
		* Table structure for table 'micromenu_url'
		*/
		$fields = array(
			array("mmur_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("mmur_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("mmur_micro"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("mmur_title"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("mmur_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("mmur_rank"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("mmur_datecreated"	,"datetime"					,"NOT NULL"	,"default '0000-00-00 00:00:00'")
		);
		$primary ="mmur_identifier";
		$tables[count($tables)] = array("micromenu_definition", $fields, $primary);
		return $tables;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                         					A D M I N   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: manage_list(Arary())
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- used to generate a list of spannable results.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function manage_list($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"manage_layout_list",__LINE__,print_r($parameters,true)));
		}
		$sql = "Select * from micromenu_list where micromenu_client = $this->client_identifier order by micromenu_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables = Array();
		$variables["FILTER"]			= $this->filter($parameters);
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		$variables["HEADER"]			= "MicroMenu Management";
		$variables["PAGE_BUTTONS"] = Array(
			Array("ADD", $this->module_command."ADD", ADD_NEW)
		);
		if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$page = $this->check_parameters($parameters,"page","1");
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
			$variables["PAGE_COMMAND"] 		= $this->module_command."LIST";
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
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
				$end_page=$this->page_size;
			$variables["END_PAGE"]			= $end_page;
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) && ($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				$variables["RESULT_ENTRIES"][$i]=Array(
					"identifier"		=> $r["micromenu_identifier"],
					"ENTRY_BUTTONS" 	=> Array(),
					"attributes"		=> Array(
						Array(LOCALE_LABEL, $r["micromenu_label"], "TITLE", "NO")
					)
				);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT",   $this->module_command."EDIT"			, EDIT_EXISTING);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE", $this->module_command."REMOVE_CONFIRM"	, REMOVE_EXISTING);
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: module_form(Arary())
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- used to displayt he managment for forthis item ( add and remove)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_form($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		/**
		* default settings
	*/ 
		$mm_label			= "";
		$selection			= "";
		$WebContainerList	= "";
		$display			= "";
		$mm_extract_type	= -1;
		$mm_all_locations	= 0;
		$mm_set_inheritance	= 0;
		$mm_menu_locations	= "";
		$mm_status			= 0;
		$mm_display_type	= 0;
		$mm_show_label		= 1;
		$mm_show_type		= 0;
		$mm_parent			= -1;
		$micromenu_defs		= Array();
		/**
		* load settings on an edit
	*/ 
		if($identifier!=-1){
			$sql = "select * from micromenu_list where micromenu_identifier = $identifier and micromenu_client =$this->client_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$mm_label			= $r["micromenu_label"];
            	$mm_show_label		= $r["micromenu_show_label"];
				$mm_extract_type	= $r["micromenu_extract_type"];
				$mm_display_type	= $r["micromenu_display_type"];
				$mm_all_locations	= $r["micromenu_all_locations"];
				$mm_set_inheritance	= $r["micromenu_set_inheritance"];
				$mm_status			= $r["micromenu_status"];
				$mm_parent			= $r["micromenu_parent"];
				$mm_show_type		= $r["micromenu_show_type"];
			}
            $this->call_command("DB_FREE",Array($result));
			if ($mm_extract_type==3){
				$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", 
					Array(
						"table_as"			=> "mmur1",
						"field_as"			=> "mmur_url",
						"identifier_field"	=> "micromenu_definition.mmur_identifier",
						"module_command"	=> $this->webContainer,
						"client_field"		=> "mmur_client",
						"mi_field"			=> "url",
						"join_type"			=> "inner"
					)
				);
				$sql = "				
select micromenu_definition.*, ".$body_parts["return_field"]."
from micromenu_definition
".$body_parts["join"]."
where mmur_client = $this->client_identifier and mmur_micro=$identifier  
".$body_parts["where"]."

order by mmur_identifier asc
";

//print $sql;
                $result  = $this->call_command("DB_QUERY",Array($sql));
                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
                	$micromenu_defs[count($micromenu_defs)] = Array($r["mmur_url"],$r["mmur_label"],$r["mmur_title"]);
                }
                $this->call_command("DB_FREE",Array($result));
//				print_r($micromenu_defs);
			}
		}
		/**
		* define the list of extraction options
	*/ 
		$sel = Array(
			Array(0,"Select a menu location to display"),
			Array(3,"Select list of links yourself")
		);
		$selection .= "<option value='".$sel[0][0]."'";
			if ($mm_extract_type<3){
				$selection .= " selected='true'";
			}
			$selection .= ">".$sel[0][1]."</option>";
			$selection .= "<option value='".$sel[1][0]."'";
			if ($mm_extract_type==$sel[1][0]){
				$selection .= " selected='true'";
			}
			$selection .= ">".$sel[1][1]."</option>";
		/**
		* define the list of display options
	*/ 
		$sel = Array(
			Array(0,"Display as a bullet list"),
			Array(1,"Select as a dropdown list (manually select to jump)"),
			Array(2,"Select as a dropdown list (Automatically jump to link after 2 seconds)")
		);
		for($i=0;$i<count($sel);$i++){
			$display .= "<option value='".$sel[$i][0]."'";
			if ($mm_display_type==$sel[$i][0]){
				$display .= " selected='true'";
			}
			$display .= ">".$sel[$i][1]."</option>";
		}
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        -	Set status drop down
	*/ 
		$status="<option value='1'";
		if($mm_status==1){
			$status.=" selected='true'";
		}
		$status.=">".LOCALE_LIVE."</option>";
		$status.="<option value='0'";
		if($mm_status==0){
			$status.=" selected='true'";
		}
		$status.=">".LOCALE_NOT_LIVE."</option>";
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        -	Set show label drop down
	*/ 
		$show_label = "<option value='1'";
		if($mm_show_label==1){
			$show_label .= " selected='true'";
		}
		$show_label .= ">".LOCALE_YES."</option>";
		$show_label .= "<option value='0'";
		if($mm_show_label==0){
			$show_label .= " selected='true'";
		}
		$show_label .= ">".LOCALE_NO."</option>";
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        -	Set show label drop down
	*/ 
		$label_list = Array(LOCALE_SHOW_TYPE_1,LOCALE_SHOW_TYPE_2);
		$num_list	= Array(1,2);
		$show_type = "";
		for ($i=0;$i<count($label_list);$i++){
			$show_type .= "<option value='".$num_list[$i]."'";
			if($mm_show_type==$num_list[$i]){
				$show_type .= " selected='true'";
			}
			$show_type .= ">".$label_list[$i]."</option>";
		}
		
		/**
		* extract menu loactions that this micro menu is available in
		*/ 
		$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
			Array(
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier
			)
		);
		/**
		* Retrieve the list of Web Containers that this item can be put into
		*/ 
		$WebContainerList  = $this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",
			Array(
				"module"		=> $this->webContainer, 
				"identifier"	=>$identifier
			)
		);
		/**
		* display this form
		*/ 
		$out  = "<module name='".$this->module_name."' display='form'>";
		$out .="	<page_options>";
		$out .="		<header><![CDATA[Micromenu Defintion]]></header>";
		$out .=			$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL", "MICROMENUADMIN_LIST", LOCALE_CANCEL));
		$out .="	</page_options>";
		$out .= "	<form name='frm' method='post'>
						<showframe>1</showframe>";
		$out .= "		<input type='hidden' name='command' value='".$this->module_command."SAVE'/>";
		$out .= "		<input type='hidden' name='identifier' value='$identifier'/>";
		$out .= "		<page_sections>";
		$out .= "			<section label='Details' name='details'>";
		$out .= "				<input type='text' name='mm_label' label='".LOCALE_LABEL."'><![CDATA[$mm_label]]></input>";
		$out .= "				<select name='mm_show_label' label='".LOCALE_MM_SHOW_LABEL."' >$show_label</select>";
		$out .= "				<select name='mm_display_type' label='".LOCALE_MM_DISPLAY_TYPE."'>$display</select>";
		$out .= "				<select name='mm_status' label='".LOCALE_STATUS."'>$status</select>";
		
		$web_containers = split("~----~",$WebContainerList);
		if ( $web_containers[0] != "" ){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}

		$out .= "			</section>";
		$out .= "			<section label='Content Selection' name='contentselection'>";
		$out .= "				<select name='mm_extract_type_format' label='".LOCALE_MM_EXTRACT_TYPE."' onchange='javascript:check_extraction();'>$selection</select>";
		if ($mm_extract_type<3){
			$out .= "				<subsection id='automatic_selection' name='automatic_selection'>";
		} else{
			$out .= "				<subsection id='automatic_selection' name='automatic_selection' hidden='1'>";
		}
		$my_menu_locations	= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($mm_parent));
		$out .= 			"<select name='pmenu_locations' label=\"".LOCALE_CHOOSE_MENU_LOCATION_LABEL."\"><option value='-1'>".LOCALE_USE_ROOT."</option>$my_menu_locations</select>";

		$out .= "				<select name='mm_show_type' label='".LOCALE_SHOW_TYPE."'>$show_type</select>";
		$out .= "				<select name='mm_extract_type' label='".LOCALE_MM_EXTRACT_TYPE."'>";
		$sel = Array(
			Array(0,LOCALE_MM_SHOW_ALL),
			Array(1,LOCALE_MM_SHOW_VISIBLE),
			Array(2,LOCALE_MM_SHOW_HIDE)
		);
		for($i=0;$i<count($sel);$i++){
			$out .= "<option value='".$sel[$i][0]."'";
			if ($mm_extract_type==$sel[$i][0]){
				$out .= " selected='true'";
			}
			$out .= ">".$sel[$i][1]."</option>";
		}
		$out .= "</select>";
		$out .= "				</subsection>";
		if ($mm_extract_type==3){
			$out .= "				<subsection id='manual_selection' name='manual_selection'>";
		} else{
			$out .= "			<subsection id='manual_selection' name='manual_selection' hidden='1'>";
		}
		$out .= "<menulinks>";
		for($i=0; $i < count($micromenu_defs); $i++){
			$out .= "<menulink>";
			$out .= "	<url><![CDATA[".$micromenu_defs[$i][0]."]]></url>";
			$out .= "	<label><![CDATA[".$micromenu_defs[$i][1]."]]></label>";
			$out .= "	<title><![CDATA[".$micromenu_defs[$i][2]."]]></title>";
			$out .= "</menulink>";
		}
		$out .= "</menulinks>";
		$out .= "				</subsection>";
		$out .= "			</section>";
		$out .= $this->location_tab($mm_all_locations, $mm_set_inheritance,$menu_locations, "");
		$out .= "		</page_sections>";
		$out .= "		<input type='submit' iconify='SAVE' value='Save for Session'/>";
		$out .= "	</form>";
		$out .= "</module>";
		return $out;
	}
	
	function module_save($parameters){
		$identifier				= $this->check_parameters($parameters, "identifier");
		$mm_label				= $this->check_parameters($parameters, "mm_label");
		$mm_show_label			= $this->check_parameters($parameters, "mm_show_label");
		$mm_extract_type		= $this->check_parameters($parameters, "mm_extract_type");
		$mm_display_type		= $this->check_parameters($parameters, "mm_display_type");
		$mm_status				= $this->check_parameters($parameters, "mm_status");
		$mm_extract_type_format	= $this->check_parameters($parameters, "mm_extract_type_format");
		$mm_show_type			= $this->check_parameters($parameters, "mm_show_type","0");
		$linkblock = $this->check_parameters($parameters,"linkblock");
//	locatiosn tab	
		$all_locations			= $this->check_parameters($parameters, "all_locations");
		$menu_locations			= $this->check_parameters($parameters, "menu_locations", Array());
		$pmenu_locations		= $this->check_parameters($parameters, "pmenu_locations",-1);
		$set_inheritance		= $this->check_parameters($parameters, "set_inheritance");
// web container list
		$replacelist			= $this->check_parameters($parameters, "web_containers",Array());
		$currentlyhave			= $this->check_parameters($parameters, "currentlyhave");
		$count_rss_containers	= $this->check_parameters($parameters, "totalnumberofchecks_web_containers");

		$now = $this->libertasGetDate("Y/m/d H:i:s");
		
		if($mm_extract_type_format==3){
			$mm_extract_type = 3;
		}
		if ($identifier==-1){
			$sql = "insert into micromenu_list 
						(micromenu_label, micromenu_client, micromenu_status, micromenu_extract_type, micromenu_display_type, micromenu_creation_date, micromenu_all_locations, micromenu_set_inheritance, micromenu_parent, micromenu_show_label, micromenu_show_type)
					values
						('".$mm_label."','".$this->client_identifier."', '".$mm_status."','".$mm_extract_type."','".$mm_display_type."','".$now."','".$all_locations."','".$set_inheritance."', '$pmenu_locations', '$mm_show_label', '$mm_show_type')";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "select * from micromenu_list where
				micromenu_label					= '".$mm_label."'
				and micromenu_client			= '".$this->client_identifier."'
				and micromenu_status			= '".$mm_status."'
				and micromenu_extract_type		= '".$mm_extract_type."'
				and micromenu_display_type		= '".$mm_display_type."'
				and micromenu_creation_date		= '".$now."'
				and micromenu_all_locations		= '".$all_locations."'
				and micromenu_set_inheritance	= '".$set_inheritance."'
				and micromenu_show_label		= '".$mm_show_label."'
				and micromenu_parent			= '$pmenu_locations'
				and micromenu_show_type			= '$mm_show_type'
			";
			//print $sql;
            $result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$identifier = $r["micromenu_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",
				Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $mm_label,
					"wo_command"	=> $this->webContainer."DISPLAY",
					"cmd"			=> "ADD",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
			$cmd			= "INSERT";
		}else{
			$sql = "update micromenu_list set
				micromenu_label				= '".$mm_label."', 
				micromenu_show_label		= '".$mm_show_label."',
				micromenu_status			= '".$mm_status."',
				micromenu_extract_type		= '".$mm_extract_type."',
				micromenu_display_type		= '".$mm_display_type."',
				micromenu_all_locations		= '".$all_locations."',
				micromenu_set_inheritance	= '".$set_inheritance."',
				micromenu_parent			= '$pmenu_locations',
				micromenu_show_type			= '$mm_show_type'
 				where micromenu_identifier	= '$identifier' and micromenu_client = $this->client_identifier
			";
            $this->call_command("DB_QUERY",Array($sql));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",
				Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $mm_label,
					"wo_command"	=> $this->webContainer."DISPLAY",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
			$cmd			= "UPDATE";
		}
		/**
		* Save menu locations
		*/
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier,
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
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier,
					"all_locations"	=> $all_locations,
					"delete"		=> 0
				)
			);
			$this->set_inheritance(
				$this->webContainer."DISPLAY",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"			=> $this->webContainer,
					"condition"			=> "micromenu_set_inheritance =1 and ",
					"client_field"		=> "micromenu_client",
					"table"				=> "micromenu_list",
					"primary"			=> "micromenu_identifier"
					)
				).""
			);
		}
		$this->tidyup_display_commands($parameters);
		/**
		* save manual defintions
		-
		* get the list of existing defintions and delete them :)
		-
	*/ 
		$block = split(":0987654321:",$linkblock);
		$sql ="select * from micromenu_definition where mmur_client = $this->client_identifier and mmur_micro = $identifier";
	    $result  = $this->call_command("DB_QUERY",Array($sql));
		$idlist="";
    	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if($idlist!=""){
        		$idlist .= ", ";
			}
			$idlist .= $r["mmur_identifier"];
	    }
        $this->call_command("DB_FREE",Array($result));
		$sql = "delete from micromenu_definition where mmur_client = $this->client_identifier and mmur_micro = $identifier";
	    $this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from memo_information where mi_client = $this->client_identifier and mi_link_id in ($idlist) and mi_type='$this->webContainer' and mi_field='url'";
	    $this->call_command("DB_QUERY",Array($sql));
		if($linkblock!=""){
			foreach ($block as $index => $value){
				$linkInfo = split(":1234567890:",$value);
				$sql = "insert into micromenu_definition (mmur_client, mmur_label, mmur_title, mmur_micro, mmur_datecreated) values
							($this->client_identifier, '".$linkInfo[0]."', '".$linkInfo[1]."', $identifier, '$now')";
				$this->call_command("DB_QUERY",Array($sql));
				$sql = "select * from micromenu_definition where mmur_client = $this->client_identifier and mmur_label='".$linkInfo[0]."' and  mmur_title = '".$linkInfo[1]."' and mmur_micro = $identifier and mmur_datecreated='$now'";
	            $result  = $this->call_command("DB_QUERY",Array($sql));
				$id=-1;
        	    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            		$id = $r["mmur_identifier"];
        	    }
    	        $this->call_command("DB_FREE",Array($result));
				/*
            	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        	    - insert url into memo information table
    	        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	            */
				if($id!=-1){
					$this->call_command("MEMOINFO_INSERT",array("mi_type"=>$this->webContainer,"mi_memo"=>$linkInfo[2],	"mi_link_id" => $id, "mi_field" => "url"));
				}
			}
		}
		$this->cache(Array("identifier"=>$identifier));
	}

	function module_remove_confirm($parameters){
		$identifier			= $this->check_parameters($parameters, "identifier");
		$sql = "delete from micromenu_list where micromenu_identifier = '$identifier' and micromenu_client = $this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$sql ="select * from micromenu_definition where mmur_client = $this->client_identifier and mmur_micro = $identifier";
	    $result  = $this->call_command("DB_QUERY",Array($sql));
		$idlist="";
    	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if($idlist!=""){
        		$idlist .= ", ";
			}
			$idlist .= $r["mmur_identifier"];
	    }
        $this->call_command("DB_FREE",Array($result));
		$sql = "delete from micromenu_definition where mmur_client = $this->client_identifier and mmur_micro = $identifier";
	    $this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from memo_information where mi_client = $this->client_identifier and mi_link_id in ($idlist) and mi_type='$this->webContainer' and mi_field='url'";
	    $this->call_command("DB_QUERY",Array($sql));
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
		/**
		* tidy
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$this->tidyup_display_commands($parameters);
	}
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    - 
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	function tidyup_display_commands($parameters){
		$all_locations = $this->check_parameters($parameters, "all_locations", 0);
		$sql ="select * from micromenu_list where micromenu_client=$this->client_identifier and micromenu_all_locations = 1";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->call_command("DB_QUERY",Array($sql));
    	$num = $this->call_command("DB_NUM_ROWS",Array($result));
		if ($num==0){
			$sql ="select distinct menu_to_object.mto_menu as m_id from menu_to_object where mto_client=$this->client_identifier and mto_module='".$this->webContainer."'";
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>$this->webContainer."DISPLAY","status"=>"OFF"));
		} else {
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>$this->webContainer."DISPLAY","status"=>"ON"));
			$sql ="select distinct menu_identifier as m_id from menu_data where menu_client=$this->client_identifier";
		}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from display_data where display_client=$this->client_identifier and display_command='".$this->webContainer."DISPLAY'";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$this->call_command("DB_QUERY",Array($sql));
		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if ($r["m_id"]>0){
				$sql = "insert into display_data (display_client, display_command, display_menu) values ($this->client_identifier, '".$this->webContainer."DISPLAY', ".$r["m_id"].")";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$this->call_command("DB_QUERY",Array($sql));
			}
   	   	}
		$this->call_command("DB_FREE",Array($result));
	}

	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    - cache (used for embed object
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	function cache($parameters){
		$id = $this->check_parameters($parameters,"identifier",-1);
		$lang="en";
		if($id==-1){
			return "";
		} else {
			$out = $this->call_command("MICROMENU_DISPLAY",Array("identifier"=>$id,"no_wrapper"=>0));
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
			$fname = "form_".$this->client_identifier."_".$lang."_libertas_mm_".$id.".xml";
			$fp = fopen($data_files."/$fname", 'w');
			fwrite($fp, $out);
			fclose($fp);
			$um = umask(0);
			@chmod($data_files."/$fname", LS__FILE_PERMISSION);
			umask($um);
		}
	}
    /**
    * restores the cached micromenu data
    **/
    function module_restore($parameters){
        $sql = "select * from micromenu_list where micromenu_client = $this->client_identifier and micromenu_status=1";
		$this->call_command("DB_QUERY",Array($sql));
		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            $this->cache(Array("identifier" => $r["micromenu_identifier"]));
   	   	}
		$this->call_command("DB_FREE",Array($result));
    }
}
?>