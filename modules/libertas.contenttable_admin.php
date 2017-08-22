<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.contenttable_admin.php
*/
/**
* This module is for producing micro menus for displaying ont he site.
*/
class contenttable_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name				= "contenttable_admin";
	var $module_name_label			= "Table of Contents (Adminsitration)";
	var $module_admin				= "1";
	var $module_command				= "CONTENTTABLEADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "CONTENTTABLE_";
	var $module_label				= "MANAGEMENT_CONTENTTABLE";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.11 $';
	var $module_creation 			= "13/08/2004";
	var $searched					= 0;
	
	var $admin_access				= 0;	
	var $admin_function_access		= 0;	
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
		array("CONTENTTABLE_DISPLAY", "LOCALE_CONTENTTABLE_DISPLAY_CHANNEL")
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
		Array("CONTENTTABLEADMIN_EDIT", "Table of contents","","Preferences/Table of Contents")
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
					if ($user_command==$this->module_command."ADD" || $user_command==$this->module_command."EDIT"){
						return $this->module_form($parameter_list);
					}
					if ($user_command==$this->module_command."CONFIRM"){
						return $this->module_confirm($parameter_list);
					}
					if ($user_command==$this->module_command."SAVE"){
						$this->module_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."CONFIRM"));
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
		* Table structure for table 'content_table'
		*/
		$fields = array(
			array("ct_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("ct_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ct_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("ct_creation_date"	,"datetime"					,"NOT NULL"	,"default '0000-00-00 00:00:00'"),
			array("ct_status"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("ct_display_type"		,"unsigned small integer"	,"NOT NULL"	,"default '0'"), // 0 = list, 1 = dropdown
			array("ct_all_locations"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("ct_set_inheritance"	,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
			array("ct_show_label"		,"unsigned small integer"	,"NOT NULL"	,"default '1'"),
			array("ct_show_folders"		,"unsigned small integer"	,"NOT NULL"	,"default '1'"),
			array("ct_show_menu"		,"unsigned small integer"	,"NOT NULL"	,"default '1'"),
			array("ct_show_home"		,"unsigned small integer"	,"NOT NULL"	,"default '1'")
		);
		$primary ="ct_identifier";
		$tables[count($tables)] = array("content_table", $fields, $primary);
		return $tables;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                         					A D M I N   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: module_form(Arary())
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- used to displayt he managment for forthis item ( add and remove)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_form($parameters){
		/**
		* default settings
	*/ 
		$ct_label			= "";
		$selection			= "";
		$WebContainerList	= "";
		$display			= "";
		$identifier			= -1;
		$ct_all_locations	= 0;
		$ct_set_inheritance	= 0;
		$ct_menu_locations	= "";
		$ct_status			= 0;
		$ct_display_type	= 0;
		$ct_show_label		= 1;
		$ct_show_folders	= 1;
		$ct_show_menu		= 1;
		$ct_show_home		= 1;
		/**
		* load settings on an edit
	*/ 
		$sql = "select * from content_table where ct_client =$this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->call_command("DB_QUERY",Array($sql));
		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
           	$identifier			= $r["ct_identifier"];
           	$ct_label			= $r["ct_label"];
        	$ct_status			= $r["ct_status"];
			$ct_display_type	= $r["ct_display_type"];
			$ct_all_locations	= $r["ct_all_locations"];
			$ct_set_inheritance	= $r["ct_set_inheritance"];
		   	$ct_show_label		= $r["ct_show_label"];
			$ct_show_folders	= $r["ct_show_folders"];
			$ct_show_menu		= $r["ct_show_menu"];
			$ct_show_home		= $r["ct_show_home"];
		}
		$this->call_command("DB_FREE",Array($result));
		/**
		* define the list of display options
		*/ 
		$sel = Array(
			Array(0,"Display as a bullet list"),
			Array(1,"Select as a dropdown list")// (manually select to jump)"),
//			Array(2,"Select as a dropdown list (Automatically jump to link after 2 seconds)")
		);
		for($i=0;$i<count($sel);$i++){
			$display .= "<option value='".$sel[$i][0]."'";
			if ($ct_display_type==$sel[$i][0]){
				$display .= " selected='true'";
			}
			$display .= ">".$sel[$i][1]."</option>";
		}
		/*
        *	Set status drop down
		*/ 
		$status="<option value='1'";
		if($ct_status==1){
			$status.=" selected='true'";
		}
		$status.=">".LOCALE_LIVE."</option>";
		$status.="<option value='0'";
		if($ct_status==0){
			$status.=" selected='true'";
		}
		$status.=">".LOCALE_NOT_LIVE."</option>";
		/*
        *	Set show label drop down
		*/ 
		$show_label = "<option value='1'";
		if($ct_show_label==1){
			$show_label .= " selected='true'";
		}
		$show_label .= ">".LOCALE_YES."</option>";
		$show_label .= "<option value='0'";
		if($ct_show_label==0){
			$show_label .= " selected='true'";
		}
		$show_label .= ">".LOCALE_NO."</option>";
		/*
        *	Set show home drop down
		*/ 
		$show_home = "<option value='1'";
		if($ct_show_home==1){
			$show_home .= " selected='true'";
		}
		$show_home .= ">".LOCALE_YES."</option>";
		$show_home .= "<option value='0'";
		if($ct_show_home==0){
			$show_home .= " selected='true'";
		}
		$show_home .= ">".LOCALE_NO."</option>";
		/*
        *	Set show menu drop down
		*/ 
		$show_menu = "<option value='1'";
		if($ct_show_menu==1){
			$show_menu .= " selected='true'";
		}
		$show_menu .= ">".LOCALE_YES."</option>";
		$show_menu .= "<option value='0'";
		if($ct_show_menu==0){
			$show_menu .= " selected='true'";
		}
		$show_menu .= ">".LOCALE_NO."</option>";
		
		
		/*
        *	Set show label drop down
		*/ 
		$show_folders = "<option value='0'";
		if($ct_show_folders==0){
			$show_folders .= " selected='true'";
		}
		$show_folders .= ">".LOCALE_NO."</option>";
		$show_folders .= "<option value='1'";
		if($ct_show_folders==1){
			$show_folders .= " selected='true'";
		}
		$show_folders .= ">".LOCALE_YES_ABOVE."</option>";
		$show_folders .= "<option value='2'";
		if($ct_show_folders==2){
			$show_folders .= " selected='true'";
		}
		$show_folders .= ">".LOCALE_YES_BELOW."</option>";
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
				"identifier"	=> $identifier
			)
		);
		/**
		* display this form
		*/ 
		$out  = "<module name='".$this->module_name."' display='form'>";
		$out .="	<page_options>";
		$out .="		<header><![CDATA[Content Table - Add/Edit]]></header>";
//		$out .=			$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL", "".$this->webContainer."LIST", LOCALE_CANCEL));
		$out .="	</page_options>";
		$out .= "	<form name='frm' method='post'>";
		$out .= "		<input type='hidden' name='command' value='".$this->module_command."SAVE'/>";
		$out .= "		<input type='hidden' name='identifier' value='$identifier'/>";
		$out .= "		<page_sections>";
		$out .= "			<section label='Details' name='details'>";
		$out .= "				<input type='text' name='ct_label' label='".LOCALE_LABEL."'><![CDATA[$ct_label]]></input>";
		$out .= "				<select name='ct_show_label' label='".LOCALE_SHOW_LABEL."' >$show_label</select>";
		$out .= "				<select name='ct_display_type' label='".LOCALE_DISPLAY_TYPE."'>$display</select>";
		$out .= "				<select name='ct_show_folders' label='".LOCALE_SHOW_FOLDERS."'>$show_folders</select>";
		$out .= "				<select name='ct_show_menu' label='".LOCALE_SHOW_MENU."'>$show_menu</select>";
		$out .= "				<select name='ct_show_home' label='".LOCALE_SHOW_HOME."'>$show_home</select>";
		$out .= "				<select name='ct_status' label='".LOCALE_STATUS."'>$status</select>";
		$web_containers = split("~----~",$WebContainerList);
		if ( $web_containers[0] != "" ){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}
		$out .= "			</section>";
		$out .= 			$this->location_tab($ct_all_locations, $ct_set_inheritance,$menu_locations, "");
		$out .= "		</page_sections>";
		$out .= "		<input type='submit' iconify='SAVE' value='Save for Session'/>";
		$out .= "	</form>";
		$out .= "</module>";
		return $out;
	}
	
	function module_save($parameters){
		$identifier				= $this->check_parameters($parameters, "identifier");
		$ct_label				= $this->validate($this->check_parameters($parameters, "ct_label"));
		$ct_show_label			= $this->check_parameters($parameters, "ct_show_label");
		$ct_show_folders		= $this->check_parameters($parameters, "ct_show_folders");
		$ct_display_type		= $this->check_parameters($parameters, "ct_display_type");
		$ct_status				= $this->check_parameters($parameters, "ct_status",0);
		$ct_show_menu			= $this->check_parameters($parameters, "ct_show_menu",1);
		$ct_show_home			= $this->check_parameters($parameters, "ct_show_home",0);
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
		
		if ($identifier==-1){
			$sql = "insert into content_table 
					(ct_label, ct_client, ct_status, ct_show_folders, ct_display_type, ct_creation_date, ct_all_locations, ct_set_inheritance, ct_show_menu, ct_show_home)
					values
					('".$ct_label."','".$this->client_identifier."', '".$ct_status."','".$ct_show_folders."','".$ct_display_type."','".$now."','".$all_locations."','".$set_inheritance."', '$ct_show_menu', '$ct_show_home')";
//			print "<p>$sql</p>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "select * from content_table where
				ct_label				= '".$ct_label."'
				and ct_client			= '".$this->client_identifier."'
				and ct_status			= '".$ct_status."'
				and ct_show_folders		= '".$ct_show_folders."'
				and ct_display_type		= '".$ct_display_type."'
				and ct_creation_date	= '".$now."'
				and ct_all_locations	= '".$all_locations."'
				and ct_set_inheritance	= '".$set_inheritance."'
				and ct_show_menu		= '".$ct_show_menu."'
				and ct_show_home		= '".$ct_show_home."'
			";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
//			print "<p>$sql</p>";
            $result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$identifier = $r["ct_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			if ($identifier!=-1){
				$this->call_command("WEBOBJECTS_MANAGE_MODULE",
					Array(
						"owner_module" 	=> $this->webContainer,
						"owner_id" 		=> $identifier,
						"label" 		=> $ct_label,
						"wo_command"	=> $this->webContainer."DISPLAY",
						"cmd"			=> "ADD",
						"previous_list" => $currentlyhave,
						"new_list"		=> $replacelist
					)
				);
			}
			$cmd			= "INSERT";
		}else{
			$sql = "update content_table set
				ct_label			= '".$ct_label."', 
				ct_status			= '".$ct_status."',
				ct_show_folders		= '".$ct_show_folders."',
				ct_display_type		= '".$ct_display_type."',
				ct_all_locations	= '".$all_locations."',
				ct_set_inheritance	= '".$set_inheritance."',
				ct_show_menu		= '".$ct_show_menu."',
				ct_show_home		= '".$ct_show_home."'
 				where ct_identifier	= '$identifier' and ct_client = $this->client_identifier
			";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
//		print "<p>$sql</p>";
            $this->call_command("DB_QUERY",Array($sql));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",
				Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $ct_label,
					"wo_command"	=> $this->webContainer."DISPLAY",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
			$cmd					= "UPDATE";
		}
		if ($identifier!=-1){
			/*
			*
			- Save menu locations
			*
			*/
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=> $menu_locations,
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier,
					"all_locations"	=> $all_locations
				)
			);
			/*
			*
			- Save inheritance
			*
			*/
			if ($set_inheritance==1){
				$child_locations = $this->add_inheritance($this->webContainer."DISPLAY",$menu_locations);
				$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
					Array(
						"menu_locations"=> $child_locations,
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
						"condition"			=> "ct_set_inheritance =1 and ",
						"client_field"		=> "ct_client",
						"table"				=> "ct_list",
						"primary"			=> "ct_identifier"
						)
					).""
				);
			}
			$this->tidyup_display_commands(
				Array(
					"tidy_table" 		=> "content_table",
					"tidy_field_starter"=> "ct_",
					"tidy_webobj"		=> $this->webContainer."DISPLAY",
					"all_locations"		=> $all_locations
				)
			);
		}
	}

	function module_confirm($paramters){
		$out  = "<module name='".$this->module_name."' display='form'>";
		$out .="	<page_options>";
		$out .="		<header><![CDATA[Content Table - Confirmation]]></header>";
		$out .="	</page_options>";
		$out .= "	<form name='frm' method='post'>";
		$out .= "		<text><![CDATA[Saved Successfully]]></text>";
		$out .= "	</form>";
		$out .= "</module>";
		return $out;
	}
}
?>
