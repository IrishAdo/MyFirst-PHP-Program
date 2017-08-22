<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.upgrade.php
* Date Created :: 25 April 2004
*/
/**
* This module is a module for upgrading a system from a specific version to the latest
* it is specifically written to do tasks once.
*/

class upgrade extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "";
	var $module_name_label			= "UpGrade module";
	var $module_name				= "upgrade";
	var $module_admin				= "0";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:14 $';
	var $module_version 			= '$Revision: 1.5 $';
	var $module_creation 			= "25/04/2004";

	var $module_command				= "UPGRADE_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_UPGRADE";
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
			/*
				scripts written for this upgrade
			*/
			if ($user_command==$this->module_command."MOVE_PAGE_CONTENT"){
				return $this->move_page_content($parameter_list);
			}
			if ($user_command==$this->module_command."MOVE_GENERIC_MENU"){
				return $this->move_to_generic_menu($parameter_list);
			}
			if ($user_command==$this->module_command."MOVE_WEB_CONTAINER"){
				return $this->move_webcontainer_relationship();
			}
		}else{
			// wrong command sent to system
			return "";
		}
	}

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
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		return 1;
	}


	function move_page_content($parameters){
		$page = $this->check_parameters($parameters,"page",0);
		if ($page==0){
			$sql = "delete from memo_information where mi_type='PAGE_'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
		}
		$sql = "select trans_identifier, trans_body, trans_summary, trans_client from page_trans_data order by trans_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$num 	 = $this->call_command("DB_NUM_ROWS",Array($result));
		if ($num>$page*50){
			$pointer = $this->call_command("DB_SEEK",Array($result, $page*50));
			$c=0;
        	while(($r = $this->call_command("DB_FETCH_ARRAY",Array($result))) && $c<50){
				$c++;
	        	$this->call_command("MEMOINFO_INSERT",
					array(
						"mi_type" => "PAGE_", 
						"mi_memo" => $r["trans_body"],	
						"mi_link_id" => $r["trans_identifier"], 
						"mi_field" => "body", 	 
						"set_client" => $r["trans_client"]
						)
					);
        		$this->call_command("MEMOINFO_INSERT",
					array(
						"mi_type" => "PAGE_", 
						"mi_memo" => $r["trans_summary"],	
						"mi_link_id" => $r["trans_identifier"], 
						"mi_field" => "summary", 
						"set_client" => $r["trans_client"]
						)
					);
        	}
    	    $this->call_command("DB_FREE",Array($result));
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=UPGRADE_MOVE_PAGE_CONTENT&amp;page=".($page+1)));
		}
	}

	function move_to_generic_menu($parameters){
		$page = $this->check_parameters($parameters,"page",0);
		if ($page==0){
			$sql = "delete from menu_to_object where mto_module='WEBOBJECTS_'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
		}
		$sql = "select * from web_layout_to_menu order by wor_menu";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$num 	 = $this->call_command("DB_NUM_ROWS",Array($result));
		if ($num>$page*50){
			$pointer = $this->call_command("DB_SEEK",Array($result, $page*50));
			$c=0;
        	while(($r = $this->call_command("DB_FETCH_ARRAY",Array($result))) && $c<50){
				$c++;
				$sql = "insert into menu_to_object (mto_module, mto_client, mto_publish, mto_extract_num, mto_object, mto_menu) values ('WEBOBJECTS_',".$r["wor_client"].",1,0, ".$r["wor_layout"].", ".$r["wor_menu"].")";
				$this->call_command("DB_QUERY",Array($sql));
        	}
    	    $this->call_command("DB_FREE",Array($result));
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=UPGRADE_MOVE_GENERIC_MENU&amp;page=".($page+1)));
		}
	}
		
	function move_webcontainer_relationship(){
		$sql="select * from web_containers";
		print "<li>$sql</li>";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$sql = "insert into web_container_to_layout (wctl_client, wctl_position, wctl_layout, wctl_container, wctl_rank ) values (".$r["wc_client"].", '".$r["wc_position"]."', ".$r["wc_layout_identifier"].", ".$r["wc_identifier"].", ".$r["wc_rank"].")";
			print "<li>$sql</li>";
			$this->call_command("DB_QUERY",Array($sql));
        }
        $this->call_command("DB_FREE",Array($result));
		/*
			"wctl_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("wctl_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wctl_layout"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wctl_container"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("wctl_position"		,"unsigned integer"		,"NOT NULL"	,"default '0'"),
			array("wctl_rank"		,"unsigned integer"			,"NOT NULL"	,"default '0'")
		);
		$primary = "wctl_identifier";
		$tables[count($tables)] = array("web_container_to_layout"*/
	}

}
?>