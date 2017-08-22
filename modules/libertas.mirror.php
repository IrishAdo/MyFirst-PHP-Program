<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.mirror.php
* @date 09 Oct 2002
*/
/**
* This module is to allow the system administrator to mirror a single 
* location in what ever locations they wish.
*/
class mirror extends module{
	/**
	*  Class Variables
	*/
	var $module_name_label			= "Mirror Management Module";
	var $module_name				= "mirror";
	var $module_label				= "MANAGEMENT_MIRROR";
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_admin				= "1";
	var $module_debug				= false;
	var $module_creation			= "22/01/2003";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.10 $';
	var $module_command				= "MIRROR_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "MIRROR_"; 		// all commands specifically for this module will start with this token
	var $module_display_options		= Array();
	var $module_admin_options 		= array();
	var $module_admin_user_access 	= array();
	var $module_mirror_options 		= array(
		"TITLE" 						=> LOCALE_TITLE_ONLY,
		"TITLE,DATE" 					=> LOCALE_TITLE_AND_DATE,
		"TITLE,SUMMARY" 				=> LOCALE_TITLE_AND_SUMMARY,
		"TITLE,SUMMARY,READMORE"	 	=> LOCALE_TITLE_AND_SUMMARY_WITH_MORE,
		"TITLE,SUMMARY,DATE" 			=> LOCALE_TITLE_AND_SUMMARY_AND_DATE,
		"TITLE,SUMMARY,DATE,READMORE" 	=> LOCALE_TITLE_AND_SUMMARY_AND_DATE_WITH_MORE
	);
	/**
	*  Class Methods
	*/
	function command($user_command,$parameter_list=array()){
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
			if ($user_command==$this->module_command."GET_VERSION"){
				return $this->get_module_version();
			}
			if ($user_command==$this->module_command."GET_AUTHOR"){
				return $this->get_module_author();
			}
			if ($user_command==$this->module_command."GET_CREATION"){
				return $this->get_module_creation();
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/*
				module specific commands
			*/
			if (($user_command==$this->module_command."FORM") || ($user_command==$this->module_command."LIVE_EDIT")){
				return $this->module_form($parameter_list);
			}
			if ($user_command==$this->module_command."LIST"){
				return $this->module_list($parameter_list);
			}
			if ($user_command==$this->module_command."FILTER"){
				return $this->module_get_filter($parameter_list);
			}
			if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."MODIFY") || ($user_command==$this->module_command."ADD")){
				return $this->module_form($parameter_list);
			}
			if ($user_command==$this->module_command."SAVE"){
				return $this->module_save($parameter_list);
			}
			if ($user_command==$this->module_command."REMOVE"){
				return  $this->remove_screen($parameter_list);
			}
			if ($user_command==$this->module_command."REMOVE_CONFIRM"){
				$this->remove_confirmed($parameter_list);
				return $this->call_command($this->module_command."LIST");
			}
			if ($user_command==$this->module_command."RETRIEVE"){
				return $this->retrieve_mirror($parameter_list);
			}
			if ($user_command==$this->module_command."PAGE_HAS"){
				return $this->has_mirror($parameter_list);
			}
		}else{
			return ""; // wrong command sent to system
		}
	}
	/**
	* call the initialisation function only when this module is created
	*/
	
	function initialise(){
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* define some access functionality
		*/
		$this->module_display_options[0]	= array($this->module_command."RETRIEVE","LOCALE_MIRROR_CHANNEL_OPEN");
		$this->module_admin_options			= array(
			array($this->module_command."FORM",$this->module_label)
		);
		$this->module_admin_user_access		= array(
			array($this->module_command."ALL","COMPLETE_ACCESS")
		);
	}


	/**
	* a function to return a list of records for this module
	*/
	function module_list($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_list",__LINE__,""));
		}
	}
	/**
	* a function to return a filter for use in searches and in the list function.
	*/
	function module_filter($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_filter",__LINE__,""));
		}
	}
	/**
	* a function to allow the addition and modification of records.
	*/
	function module_form($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_form",__LINE__,""));
		}
		$mirror_counter	= 3;
		$all_locations	= 0;
		
		$sql			= "select * from display_data where display_command='MIRROR_RETRIEVE' and display_client=$this->client_identifier";
		$result 		= $this->call_command("DB_QUERY",array($sql));
		$locations		= Array();
		$source			= Array();
		if ($result){
			while($r 	= $this->call_command("DB_FETCH_ARRAY",array($result))){
				$locations[count($locations)]  = $r["display_menu"];
			}
			$this->call_command("DB_FREE",array($result));
		}
		$sql="select * from mirror_data where mirror_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($result){
			$r 						= $this->call_command("DB_FETCH_ARRAY",array($result));
			$source[count($source)] = $r["mirror_source"];
			$mirror_counter			= $r["mirror_counter"];
			$override				= $r["mirror_override_label"];
			$display_option			= $this->check_parameters($r,"mirror_display_option","TITLE");
			$all_locations			= $this->check_parameters($r,"mirror_all_locations",0);
			$this->call_command("DB_FREE",array($result));
		}
		$mirror_count="";
		for($index=1;$index<=10;$index++){
			$mirror_count.="<option";
			if ($index == $mirror_counter){
				$mirror_count.=" selected='true'";
			}
			$mirror_count.=" value='$index'";
			$mirror_count.=">$index</option>";
		}
		
		$mirror_display_options="";
		foreach ($this->module_mirror_options as $key => $value){
			$mirror_display_options .= "<option";
			if ($key == $display_option){
				$mirror_display_options .= " selected='true'";
			}
			$mirror_display_options .= " value='$key'";
			$mirror_display_options .= ">$value</option>\n";
		}
		$mirror_locations	= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",array($locations));
		$mirror_source		= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",array($source));
		$out  = "<module name=\"$this->module_name\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Mirror Manager]]></header>";
		$out .= "</page_options>";
		$out .=  "<form label=\"".MIRROR_MANAGEMENT_TITLE."\" method=\"post\" name=\"mirror_settings\">";
		$out .=   "<input type=\"hidden\" name=\"command\" value=\"MIRROR_SAVE\"/>";
		$out .=   "<page_sections>";
 		$out .=    "<section label='Mirror Set-up' name='mirror_setup'>";
		$out .="<input type=\"text\" label='".LOCALE_MIRROR_OVERRIDE_LABEL."' size='255' name=\"mirror_override_label\"><![CDATA[$override]]></input>";
		$out .="<select label=\"".LOCALE_MIRROR_DISPLAY_OPTION."\" name=\"mirror_display\">$mirror_display_options</select>";
		$out .="<select label=\"".LOCALE_MIRROR_LIMIT_PAGES."\" name=\"mirror_counter\">$mirror_count</select>";
		$out .= "</section>";
		$out .= "<section label='Mirror Locations' name='mirror_locations'>";
		$out .="<select label=\"".MIRROR_LOCATION_TO_MIRROR."\" name=\"mirror_source\"><option>".MIRROR_OPTION_NONE_SELECTED."</option>$mirror_source</select>";
		$out .="<radio label=\"".LOCALE_ALL_LOCATIONS."\" name=\"mirror_all_locations\" onclick='mirror_locations'>
					<option value=\"1\"";
				if 	($all_locations == '1'){
					$out .=" selected=\"true\"";
				}
				$out.= ">".ENTRY_YES."</option>
					<option value=\"0\"";
				if 	($all_locations == '0'){
					$out .=" selected=\"true\"";
				}
				$out.= ">".ENTRY_NO."</option>";
		$out .="</radio>";
		$out .="<checkboxes ";
		if 	($all_locations == '1'){
				$out .=" hidden=\"YES\"";
		}
		$out .= " sort='0' name=\"mirror_locations\" type=\"vertical\" label=\"".LOCALE_ALL_LOCATIONS_MSG."\"><options module='Menu Locations'>$mirror_locations</options></checkboxes>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","ENGINE_SPLASH","CANCEL"));

//		$out .="<input type=\"button\" value=\"".LOCALE_CANCEL."\" command=\"ENGINE_SPLASH\" iconify=\"CANCEL\"/>";
		$out .= "</section>";
		$out .= "</page_sections>";
		$out .="<input type=\"submit\" value=\"".ENTRY_SAVE."\" command=\"MIRROR_SAVE\" iconify=\"SAVE\"/>";
		$out .="</form>";
		$out .="</module>";
		
//		print $out;
		return $out;
	}
	/**
	* a function to save the information form the form
	*/
	function module_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_save",__LINE__,""));
		}
		$mirror_locations		= $this->check_parameters($parameters,"mirror_locations",Array());
		$mirror_source			= $this->check_parameters($parameters,"mirror_source");
		$mirror_counter			= $this->check_parameters($parameters,"mirror_counter",3);
		$mirror_display			= $this->check_parameters($parameters,"mirror_display","TITLE");
		$mirror_override_label	= trim($this->strip_tidy($this->check_parameters($parameters,"mirror_override_label")));
		$mirror_all_locations	= $this->check_parameters($parameters,"mirror_all_locations");
		//print_r($parameters);
		$identifier=-1;
		$sql = "select * from mirror_data where mirror_client=$this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$identifier = $r["mirror_identifier"];
        }
        $this->call_command("DB_FREE",Array($result));
		$sql = Array();
		if ($identifier==-1){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- create a new instance of the mirror record
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql[0] = "delete from mirror_data where mirror_client=$this->client_identifier;";
			$sql[1] = "insert into mirror_data (mirror_source, mirror_client, mirror_counter, mirror_override_label, mirror_display_option, mirror_all_locations) values ($mirror_source,$this->client_identifier, $mirror_counter, '$mirror_override_label', '$mirror_display', '$mirror_all_locations');";
		} else {
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- create a new instance of the mirror record
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql[0] = "update mirror_data 
						set 
							mirror_source = $mirror_source, 
							mirror_counter= $mirror_counter, 
							mirror_override_label= '$mirror_override_label', 
							mirror_display_option='$mirror_display', 
							mirror_all_locations='$mirror_all_locations'	
						where 
							mirror_client=$this->client_identifier
					";
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* delete all occurances of the mirror channel for this client 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$sql[count($sql)] = "delete from display_data where display_client=$this->client_identifier and display_command='MIRROR_RETRIEVE';";
		if ($mirror_all_locations==0){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if the user has selected no for all locations then insert any locations that they chose specifically.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			for($index=0,$max=count($mirror_locations);$index<$max;$index++){
				$sql[count($sql)] = "insert into display_data (display_client, display_command, display_menu) values ('$this->client_identifier', 'MIRROR_RETRIEVE', '".$mirror_locations[$index]."');";
			}
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>"MIRROR_RETRIEVE","status"=>"OFF"));
		} else {
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>"MIRROR_RETRIEVE","status"=>"ON"));
		}

		for($index=0,$max=count($sql);$index<$max;$index++){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"module_save",__LINE__,$sql[$index]));
			}
			$s = $sql[$index];
//			print "<li>[".$s."]</li>";
			$this->call_command("DB_QUERY",array($s));
		}
		if ($identifier==-1){
			$sql = "select * from mirror_data where mirror_client=$this->client_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        		$identifier = $r["mirror_identifier"];
    	    }
	        $this->call_command("DB_FREE",Array($result));
		}
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $mirror_locations,
				"module"		=> $this->webContainer,
				"identifier"	=> $identifier,
				"all_locations"	=> $mirror_all_locations
			)
		);


		$this->tidyup_display_commands($parameters);
		$out  = "<module name=\"$this->module_name\" display=\"form\">";
		$out .= "<form label=\"Mirror Manager Confirm\" method=\"post\" name=\"mirror_settings\">";
		$out .= "<text><![CDATA[Thankyou the mirror has been updated]]></text>";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	/**
	* a function to request the removal of a record form the system.
	*/
	function remove_screen($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"remove_screen",__LINE__,""));
		}
	}
	/**
	* a function to remove the desired record once the confirmation has been completed.
	*/
	function remove_confirmed($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"remove_confirmed",__LINE__,""));
		}
	}
	/**
	* a function to remove the desired record once the confirmation has been completed.
	*/
	function retrieve_mirror($parameters){
		$cmd = $this->check_parameters($parameters,"command");
		$pos = $this->check_parameters($parameters,"__layout_position");
		if (($pos==2 || $pos==3) && $cmd!=""){
			return "";
		}

		$sql ="select * from mirror_data 
				inner join menu_data on mirror_source = menu_identifier where mirror_client = $this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"retrieve_mirror",__LINE__,$sql));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$label	= "";
		$size	= "";
		if ($result){
			$r 								= $this->call_command("DB_FETCH_ARRAY",array($result));
			$parameters["override_script"]	= $r["menu_url"];
			$label							= $this->check_parameters($r,"mirror_override_label","");
			$size							= $this->check_parameters($r,"mirror_counter","3");
			$display						= $this->check_parameters($r,"mirror_display_option","TITLE");
			$label							= (strlen(trim($label)) == 0 ) ? $this->check_parameters($r,"menu_label","") : $label;
			$this->call_command("DB_FREE",array($result));
		}
		$parameters["display_fields"] = Array("date"=>1,"title"=>1,"summary"=>1,"url"=>1,"return_number"=>$size);
		$parameters["ignore_commands"] = Array();
		$parameters["ignore_commands"][0] = "MIRROR_RETRIEVE";
		$parameters["ignore_commands"][1] = "MIRROR_PAGE_HAS";
		unset($parameters["identifier"]);
		//print_r($parameters);
		return "<module name=\"$this->module_name\" display=\"mirror\">
					<label><![CDATA[".$label."]]></label>
					<display><![CDATA[".$display."]]></display>
					<size><![CDATA[".$size."]]></size>
					<menulocation>".$parameters["override_script"]."</menulocation>
					".$this->call_command("PRESENTATION_DISPLAY",$parameters)."
				</module>";
	}
	/**
	* a function to return the mirror if this location has one 
	*/
	function has_mirror($parameters){
		$out="";
		$sql = "select * from mirror_data where mirror_client=$this->client_identifier";
//		print $sql;
		$result = $this->call_command("DB_QUERY",array($sql));
		$all=0;
		if ($result){
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$all = $this->check_parameters($r,"mirror_all_locations",0);
			$this->call_command("DB_FREE",	array($result));
		}
		if ($all==0){
			if (isset($parameters["page_menu_location"])){
				$sql ="select * from menu_data inner join display_data on display_menu = menu_identifier where menu_client = $this->client_identifier and menu_identifier ='".$parameters["page_menu_location"]."' and display_command='MIRROR_RETRIEVE'";
			}else{
				$sql ="select * from menu_data inner join display_data on display_menu = menu_identifier where menu_client = $this->client_identifier and menu_url ='".$this->parent->script."' and display_command='MIRROR_RETRIEVE'";
			}
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"has_mirror",__LINE__,$sql));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($result){
				if ($this->call_command("DB_NUM_ROWS",array($result))>0){
					$out .= $this->retrieve_mirror($parameters);
				}
				$this->call_command("DB_FREE",	array($result));
			}
		} else {
			$out .= $this->retrieve_mirror($parameters);
		}
		return $out;
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
		* Table structure for table 'user_info'
		*/
		
		$fields = array(
			array("mirror_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment", "key"),
			array("mirror_client"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("mirror_source"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("mirror_override_label"	,"varchar(255)"		,""			,"default ''"),
			array("mirror_display_option"	,"varchar(255)"		,""			,"default ''"),
			array("mirror_counter"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("mirror_all_locations"	,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="mirror_identifier";
		$tables[count($tables)] = array("mirror_data", $fields, $primary);
		return $tables;
	}
	
	function tidyup_display_commands($parameters){
		$debug = $this->debugit(false, $parameters);
		$all_locations = $this->check_parameters($parameters, "mirror_all_locations",0);
		$sql ="select * from mirror_data where mirror_client=$this->client_identifier and mirror_all_locations=1";
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$result  = $this->call_command("DB_QUERY",Array($sql));
    	$num = $this->call_command("DB_NUM_ROWS",Array($result));
		if ($num==0){
			$sql ="select distinct menu_to_object.mto_menu as m_id from menu_to_object where mto_client=$this->client_identifier and mto_module='$this->webContainer'";
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>$this->webContainer."DISPLAY","status"=>"OFF"));
		} else {
			$this->call_command("LAYOUT_SET_GLOBAL_COMMAND", Array("cmd"=>$this->webContainer."DISPLAY","status"=>"ON"));
			$sql ="select distinct menu_identifier as m_id from menu_data where menu_client=$this->client_identifier";
		}

		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from display_data where display_client=$this->client_identifier and display_command='MIRROR_RETRIEVE'";
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
		$this->call_command("DB_QUERY",Array($sql));
		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if ($r["m_id"]>0){
				$sql = "insert into display_data (display_client, display_command, display_menu) values ($this->client_identifier, 'MIRROR_RETRIEVE', ".$r["m_id"].")";
				if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
				$this->call_command("DB_QUERY",Array($sql));
			}
   	   	}
		$this->call_command("DB_FREE",Array($result));
	}

}
?>