<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.sitemap.php
* @date 12 Feb 2004
*/
/**
* This module is the presentation module for sitemap 
*/

class sitemap extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_grouping			= "";
	var $module_name_label			= "Site Map Mangement Module (Presentation)";
	var $module_name				= "sitemap";
	var $module_admin				= "0";
	var $module_command				= "SITEMAP_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_SITEMAP";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:13 $';
	var $module_version 			= '$Revision: 1.10 $';
	var $module_creation 			= "1/04/2004";
	/**
	*  XSLT display options
	*
	* This array has one extra entry compaired to the admin one it has the display option 
	- undefined.
	*/
	var $xsl_display_options 		= array(
		Array(0,"undefined","undefined"),
		Array(1,"sitemap_default","Display as a bullet list"),
		Array(2,"sitemap_columns","Display in 3 Columns")
	);
	/**
	* SPECIAL PAGES
	*
	* Each special page will call a specific function as defined here
	*/
	var $specialPages			 	= array(
		array("-site-map.php",	"SITEMAP_DISPLAY",	"VISIBLE",	"Site Map")
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
		array(2,"Display the Site Map","WEBOBJECTS_SHOW_SITEMAP_EXTRACT",0,0)
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
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,print_r($parameter_list,true)));
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
			if ($user_command==$this->module_command."SPECIAL_PAGES"){
				return $this->specialPages;
			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->module_display($parameter_list);
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
	
		return 1;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                 S I T E M A P   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: list()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- This function lists the site map form for the use there can only be one sitemap per site.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_display($parameters){
		$sql ="select * from sitemap_data 
left outer join menu_to_object on mto_object = sitemap_identifier and mto_client = sitemap_client and mto_module='SITEMAP_' 
left outer join menu_data on mto_menu = menu_identifier and menu_client = mto_client and menu_url = '".$this->parent->script."'
where sitemap_client=$this->client_identifier ";
		
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$display=-1;
		$show_hidden = 0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$display = $r["sitemap_display_options"];
			$show_hidden = $r["sitemap_showhidden"];
        }
        $this->call_command("DB_FREE",Array($result));
		$out  = "<module name=\"".$this->module_name."\" display=\"sitemap\">";
		$dis = $this->check_parameters($this->xsl_display_options,$display,Array());
		$dis_info = $this->check_parameters($dis,1);
		$out .= "	<display><![CDATA[".$dis_info."]]></display>";
		$out .= "	<show><![CDATA[".$show_hidden."]]></show>";
		$out .= "</module>";
		return $out;
	}

}
?>
