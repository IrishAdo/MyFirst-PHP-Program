<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.micromenu_presentationistration.php
*/
/**
* This module is for displaying micro menus on the site.
*/
class micromenu extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name				= "micromenu_presentation";
	var $module_name_label			= "Micro Menu Module (Presentation)";
	var $module_admin				= "0";
	var $module_command				= "MICROMENU_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "micromenu_";
	var $module_label				= "MANAGEMENT_MICROMENU";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.12 $';
	var $module_creation 			= "13/08/2004";
	var $searched					= 0;
	
	var $admin_access				= 0;	
 	var $admin_function_access		= 0;	
	
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
			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
				return $this->webContainer;
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Display functions for this module
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->display($parameter_list);
			}
			if ($user_command==$this->module_command."JUMPTO"){
				return $this->jumpto($parameter_list);
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
		return 1;
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                         				P R E S E N T A T I O N   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function display($parameters){
		$__layout_position		= $this->check_parameters($parameters, "__layout_position");
		$cmd					= $this->check_parameters($parameters, "command");
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,print_r($parameters,true)));}
		if($__layout_position==2 && $cmd !=''){
			return "";
		}
		$current_menu_location	= $this->check_parameters($parameters,"current_menu_location",-1);
		$label 			= "";
		$list			= Array();
		$display_type	= "";
		$extract_type	= "";
		$menu_parent	= -1;
		$show_label		= 0;
		$out 			= "";
		$mm_show_type	= 0;
		$identifier		= $this->check_parameters($parameters,"wo_owner_id", $this->check_parameters($parameters,"identifier",-1));
		$no_wrapper		= $this->check_parameters($parameters,"no_wrapper",0);
		if ($identifier>0){
			$sql = "select * from micromenu_list 
						left outer join menu_to_object on mto_object = micromenu_identifier and mto_client=micromenu_client and mto_module='MICROMENU_'
					where micromenu_client = $this->client_identifier and micromenu_identifier = $identifier and micromenu_status=1 and
						((micromenu_all_locations=1 and mto_menu is null ) or mto_menu = $current_menu_location )";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,$sql));}
	        $result  = $this->call_command("DB_QUERY",Array($sql));
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$show_label		= $r["micromenu_show_label"];
	        	$label 			= "<label show='$show_label'>".$r["micromenu_label"]."</label>";
				$display_type	= $r["micromenu_display_type"];
				$menu_parent	= $r["micromenu_parent"];
				$mm_show_type	= $r["micromenu_show_type"];
				$extract_type	= $r["micromenu_extract_type"];
	        }
			if($display_type==0){
				$display_format="bulletList";
			} else if($display_type==1){
				$display_format="dropdownmanual";
			} else {
				$display_format="dropdownauto";
			}
	        $this->call_command("DB_FREE",Array($result));
			/*
			$sel = Array(
				Array(0,"Select complete level one menu structure"),
				Array(1,"Select non-hidden level one menu structure"),
				Array(2,"Select hidden level one menu structure"),
				Array(3,"Select list of links yourself")
			);
			*/
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::extract_type",__LINE__, $extract_type));}
			if ($extract_type < 3){
				$out  = "<menu_parent>$menu_parent</menu_parent>";
				$out .= "<extract_type>$extract_type</extract_type>";
        	} else if ($extract_type == 3){
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
	
				$sql = "select *, ".$body_parts["return_field"]." from micromenu_definition 
				".$body_parts["join"]."
				where mmur_client = $this->client_identifier and mmur_micro=$identifier ".$body_parts["where"]." order by mmur_identifier asc";
	            $result  = $this->call_command("DB_QUERY",Array($sql));
	
				$out ="";
	            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$list[count($list)] = Array(
						"title" => $r["mmur_title"],
						"label" => $r["mmur_label"],
						"url" => $r["mmur_url"]
					);
	            }
	            $this->call_command("DB_FREE",Array($result));
				$mm_show_type=0;
				for($i=0;$i<count($list);$i++){
					$out .= "<menulink>
								<title><![CDATA[".$list[$i]["title"]."]]></title>
								<label><![CDATA[".$list[$i]["label"]."]]></label>
								<url><![CDATA[".$list[$i]["url"]."]]></url>
							</menulink>";
				}
			}
			if($no_wrapper==0){
				$output  = "<module name='micromenu' display='LINKS'>
							<display_format>$display_format</display_format>
							<mm_display_type>$extract_type</mm_display_type>
							<mm_show_type>$mm_show_type</mm_show_type>
							<uid><![CDATA[mm_$identifier]]></uid>
							$label
							$out
						</module>";
			} else {
				$output  = "
							<display_format>$display_format</display_format>
							<mm_display_type>$extract_type</mm_display_type>
							<mm_show_type>$mm_show_type</mm_show_type>
							<uid><![CDATA[mm_$identifier]]></uid>
							$label
							$out
						";
			}
		} else {
			$output="";
		}
		return $output;
	}

	function jumpto($parameters){
		$url  = $this->check_parameters($parameters,"quicklink");
		if ($url=="-1"){
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$this->parent->script));
		} else {
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$url));
		}
	}

}

?>
