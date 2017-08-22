<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.sitemap_admin.php
* @date 12 Feb 2004
*/
/**
* This module is the administration module for sitemap 
*/

class sitemap_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_name_label			= "Site Map Mangement Module (Administration)";
	var $module_name				= "sitemap_admin";
	var $module_admin				= "1";
	var $module_command				= "SITEMAPADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "SITEMAP_";
	var $module_label				= "MANAGEMENT_SITEMAP";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:13 $';
	var $module_version 			= '$Revision: 1.10 $';
	var $module_creation 			= "01/04/2004";
	var $searched					= 0;
	var $loadedcat					= Array();
	var $complete_admin_access 		= 0;
	/**
	*  Management Menu entries
	*/
	
	var $module_admin_options 		= array(
	);
	
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access = array(
		array("SITEMAPADMIN_ALL",			"COMPLETE_ACCESS")
	);
	
	/**
	*  Channel options
	*/
	var $module_display_options 	= array(
			array("SITEMAP_DISPLAY",	"LOCALE_DISPLAY_SITEMAP"),
	);
	/**
	*  XSLT display options
	*/
	var $xsl_display_options 		= array(
		Array(1,"sitemap_default","Display as a bullet list"),
		Array(2,"sitemap_columns","Display in 3 Columns")
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
		array(2,"Display the Site Map","SITEMAP_DISPLAY",0,0)
	);
	
	/**
	*  filter options
	*/
	var $display_options			= array();
	
	/**
	*  Access options php 5 will allow these to become private variables.
	*/
	var $admin_access				= 0;
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
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return "";$this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command == $this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
/*			if ($user_command==$this->module_command."WEBOBJECT_LIST"){
				return $this->listwebobjects($parameters);
			}*/
				if ($user_command==$this->module_command."CREATE_TABLE"){
					return $this->create_table();
				}
			if ($this->install_access==1){
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- Create table function allow access if in install mode
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Administration Module commands
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($this->admin_access==1){
				if ($user_command == $this->module_command."LIST"){
					return $this->module_list($parameter_list);
				}
				if ($user_command == $this->module_command."SAVE"){
					return $this->module_save($parameter_list);
				}
			}
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                S I T E M A P   S E T U P   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
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
		$this->load_locale("sitemap");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		$this->admin_access				= 0;
		$this->install_access			= 0;
		$this->complete_admin_access 	= 0;
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
					("SITEMAPADMIN_ALL"==$access[$index]) ||
					("ALL"==$access[$index])
				   ){
					$this->complete_admin_access = 1;
				}
			}
		}
		if (
			($this->complete_admin_access) && 
			(
				($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files"))
			){
			$this->admin_access=1;
		}
		$this->module_admin_options[count($this->module_admin_options)] = array("SITEMAPADMIN_LIST", "MANAGE_SITEMAP","");
		$this->module_display_options 	= array(
			array("SITEMAP_DISPLAY",	LOCALE_DISPLAY_SITEMAP)
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
		* Table structure for 'sitemap_data'
		*/
		
		$fields = array(
			array("sitemap_identifier"					,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("sitemap_client"						,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("sitemap_display_options"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("sitemap_created"						,"DATETIME"					,"NOT NULL"	,"default ''"),
			array("sitemap_showhidden"					,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		
		$primary ="sitemap_identifier";
		$tables[count($tables)] = array("sitemap_data", $fields, $primary);
		/**
		* Table structure for 'sitemap_menu'
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		
		$fields = array(
			array("sitemapmenu_identifier"					,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("sitemapmenu_client"						,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("sitemapmenu_menu"						,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		
		$primary ="sitemapmenu_identifier";
		$tables[count($tables)] = array("sitemap_menu", $fields, $primary);
		*/
		return $tables;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                            S I T E M A P   M A N A G E R   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: list()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- This function lists the site map form for the use there can only be one sitemap per site.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_list($parameters){
		$display_tab = $this->check_parameters($parameters,"display_tab");
		$sql = "select * from sitemap_data where sitemap_client=$this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$out ="";
		$menu_locations = Array();
		$identifier=-1;
		$display=1;
		$sitemap_showhidden =0;
    	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$identifier								= $r["sitemap_identifier"];
			$display								= $r["sitemap_display_options"];
			$sitemap_showhidden						= $r["sitemap_showhidden"];
		}
		$this->call_command("DB_FREE",Array($result));
		$menu_locations	= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
				Array(
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier
				)
			)));
//		$xsl_display_options
		$out  = "<module name=\"".$this->module_name."\" display=\"form\">";
			$out .= "<page_options>";
			$out .= "<header><![CDATA[".LOCALE_SITEMAP_MANAGEMENT."]]></header>";
//				$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("PREVIEW","PAGE_PREVIEW",ENTRY_PREVIEW));
			$out .= "</page_options>";			
			$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>"LOCALE_PAGE_FORM"));
			$out .= "<form name=\"user_form\" method=\"post\" label=\"".LOCALE_SITEMAP_MANAGEMENT."\">";
			$out .= "	<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>";
			$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[".$this->module_command."SAVE]]></input>";
			$out .= "<page_sections>
			";
			$out .= "	<section label=\"".LOCALE_SETUP."\"";
			if ($display_tab=="content"){
				$out .= " selected='true'";
			}
			$out .= ">";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Display menu locations
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$out .= 			"<select name='menu_locations' label=\"".LOCALE_CHOOSE_MENU_LOCATION."\"><option value='-1'>".LOCALE_NOT_CURRENTLY_ON_SITE."</option>$menu_locations</select>";
			$out .= 			"<select name='display_option' label=\"".LOCALE_CHOOSE_DISPLAY_OPTION."\">";
			$max = count($this->xsl_display_options);
			for($index=0;$index<$max;$index++){
				if ($this->xsl_display_options[$index][0] == $display){
					$out .= "<option value='" . $this->xsl_display_options[$index][0] . "' selected='true'>".$this->xsl_display_options[$index][2]."</option>";
				} else {
					$out .= "<option value='" . $this->xsl_display_options[$index][0] . "' >".$this->xsl_display_options[$index][2]."</option>";
				}
			}
			$out .="			</select>";
			$out .="<select name='sitemap_showhidden' label=\"".LOCALE_CHOOSE_SHOW_HIDDEN."\">";
			$out .=		"<option value='0'";
			if ($sitemap_showhidden==0) {
				$out .=" selected='true'";
			}
			$out .=">".LOCALE_NO."</option>";
			$out .=		"<option value='1'";
			if ($sitemap_showhidden==1) {
				$out .=" selected='true'";
			}
			$out .=">".LOCALE_YES."</option>";
			$out .="			</select>";
			$out .=	"		</section>";
			
			$out .= "	</page_sections>";
			$out .= "	<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
			$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: save()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- This function saves the site map settings
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_save($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$display_option		= $this->check_parameters($parameters,"display_option",-1);
		$menu_location		= Array( 0 => $this->check_parameters($parameters,"menu_locations",-1));
		$sitemap_showhidden	= $this->check_parameters($parameters,"sitemap_showhidden",0);
		
		if ($identifier==-1){
			$now = $this->libertasGetDate("Y/m/d H:i:s");
			$sql = "insert into sitemap_data (sitemap_display_options,sitemap_client,sitemap_created, sitemap_showhidden) values ('$display_option', '$this->client_identifier', '$now', '$sitemap_showhidden')";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "select sitemap_identifier from sitemap_data where sitemap_client=$this->client_identifier and sitemap_display_options='$display_option' and sitemap_created='$now' and sitemap_showhidden='$sitemap_showhidden'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$out ="";
			$menu_locations = Array();
			$identifier=-1;
			$display=-1;
    		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$identifier								= $r["sitemap_identifier"];
			}
			$this->call_command("DB_FREE",Array($result));
			/*
			if (is_array($menu_location)){
				$max = count($menu_location);
				for($index=0;$index<$max;$index++){
					$sql = "insert into sitemap_menu (sitemapmenu_menu, sitemapmenu_client, sitemapmenu_sitemap) values ('".$menu_location[$index]."', '$this->client_identifier', '$identifier')";
					$this->call_command("DB_QUERY",Array($sql));
				}
			} else {
				$sql = "insert into sitemap_menu (sitemapmenu_menu, sitemapmenu_client, sitemapmenu_sitemap) values ('".$menu_location."', '$this->client_identifier', '$identifier')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			*/
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=> $menu_location,
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier
				)
			);

		} else {
			$sql = "update sitemap_data set sitemap_showhidden='$sitemap_showhidden', sitemap_display_options = '$display_option' where sitemap_identifier = $identifier and sitemap_client = '$this->client_identifier'";
			$this->call_command("DB_QUERY",Array($sql));
//			$sql = "delete from sitemap_menu where sitemapmenu_client = '$this->client_identifier' and sitemapmenu_sitemap = '$identifier'";
//			print "<p>$sql</p>";
//			$this->call_command("DB_QUERY",Array($sql));
			
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=> $menu_location,
					"module"		=> $this->webContainer,
					"identifier"	=> $identifier
				)
			);
/*
			if (is_array($menu_location)){
				$max = count($menu_location);
				for($index=0;$index<$max;$index++){
					$sql = "insert into sitemap_menu (sitemapmenu_menu, sitemapmenu_client, sitemapmenu_sitemap) values ('".$menu_location[$index]."', '$this->client_identifier', '$identifier')";
					$this->call_command("DB_QUERY",Array($sql));
				}
			} else {
				$sql = "insert into sitemap_menu (sitemapmenu_menu, sitemapmenu_client, sitemapmenu_sitemap) values ('".$menu_location."', '$this->client_identifier', '$identifier')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			*/
		}
		$this->tidyup_display_commands($parameters);
		$out  = "<module name=\"".$this->module_name."\" display=\"form\">";
			$out .= "<page_options>";
			$out .= "<header><![CDATA[".LOCALE_SITEMAP_MANAGEMENT."]]></header>";
			$out .= "</page_options>";			
			$out .= "<form name=\"user_form\" method=\"post\" label=\"".LOCALE_SITEMAP_CONFIRM_LABEL."\">";
			$out .= "<text><![CDATA[".LOCALE_SITEMAP_SAVE_MSG."]]></text>";
			$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	
	function tidyup_display_commands($parameters){
		$debug = $this->debugit(false, $parameters);
		$sql ="select DISTINCT * from menu_to_object where mto_module='".$this->webContainer."' and mto_client=$this->client_identifier";
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$result  = $this->call_command("DB_QUERY",Array($sql));
   		$sql = "delete from display_data where display_client=$this->client_identifier and display_command='SITEMAP_DISPLAY'";
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$this->call_command("DB_QUERY",Array($sql));
   		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if ($r["mto_menu"]>0){
				$sql = "insert into display_data (display_client, display_command, display_menu) values ($this->client_identifier, 'SITEMAP_DISPLAY', ".$r["mto_menu"].")";
				if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
				$this->call_command("DB_QUERY",Array($sql));
			}
       	}
		$this->call_command("DB_FREE",Array($result));
	}

}
?>
