<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.accesskey.php
* @date 10 May 2004
*/
/**
* this module will allow the presentation of the accesskey list for this website
*/
class accesskey extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type		= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name			= "accesskey";
	var $module_name_label		= "Access Key Manager Module (Presentation)";
	var $module_admin			= "1";
	var $module_debug			= false;
	var $module_creation		= "10/05/2004";
	var $module_version 		= '$Revision: 1.9 $';
	var $module_command			= "ACCESSKEY_"; 		// all commands specifically for this module will start with this token
	var $has_module_contact		= 0;
	var $has_module_group		= 0;
	var $display_options		= null;
		
	var $module_display_options = array(
	);
	
	var $defined_list = Array(
			Array("0", "Accesskey detailsa"					,1,"",""),
			Array("1", "Home page"							,0,"",""),
			Array("2", "Whats new"							,1,"",""),
			Array("3", "Site map"							,0,"",""),
			Array("4", "Search"								,0,"",""),
			Array("5", "Frequently Asked Questions (FAQ)"	,1,"",""),
			Array("6", "Help"								,1,"",""),
			Array("7", "Complaints procedure"				,1,"",""),
			Array("8", "Terms and conditions"				,1,"",""),
			Array("9", "Feedback form"						,1,"",""),
			Array("m", "Toggle between graphical and text only mode" ,0,"",""),
			Array("s", "Skip navigation"					,0,"","")
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
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->display($parameter_list);
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	/**
	* Initialise function
	*
	* This function will initialise some variables for this modules functions to use.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise",__LINE__,""));
		}
		$this->load_locale("accesskey");
		/**
		* request the client identifier once we use this variable often						-
		*/
		$this->client_identifier = $this->parent->client_identifier;
	}
	
	/**
	* display
	-----------------------
	- This function is to dispaly the access keys that have been defined for the site
	*/
	function display($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_form",__LINE__,print_r($parameters,true)));
		}
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$url_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
					"table_as"			=> "akey1",
					"field_as"			=> "accesskey_url",
					"identifier_field"	=> "accesskey_data.accesskey_identifier",
					"module_command"	=> "ACCESSKEYADMIN_",
					"client_field"		=> "accesskey_client",
					"mi_field"			=> "accesskey_url"
				)
			);
	
			$sql = "select distinct *, ".$url_parts["return_field"]." from accesskey_data ".$url_parts["join"]." where accesskey_client = $this->client_identifier ".$url_parts["where"]." order by accesskey_key desc";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			// find the length of the defined list as we will check these only additional one can be ignored.
			$max = count($this->defined_list);
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$found=0;
	        	for ($index=0; $index<$max;$index++){
					if ($this->defined_list[$index][0] == $r["accesskey_key"]){
						$title = $r["accesskey_title"];
						$url = $r["accesskey_url"];
						if($r["accesskey_key"]=="0" && $url==""){
							$url = "DEFINED";
						}
						if($r["accesskey_key"]=="1" && $url==""){
							$url = "DEFINED";
						}
						if($r["accesskey_key"]=="3" && $url==""){
							$url = "DEFINED";
						}
						if($r["accesskey_key"]=="4" && $url==""){
							$url = "DEFINED";
						}
						if($r["accesskey_key"]=="m" && $url==""){
							$url = "DEFINED";
						}
						if($r["accesskey_key"]=="s" && $url==""){
							$url = "DEFINED";
						}
						if($r["accesskey_key"]=="-" && $url==""){
							$url = "DEFINED";
						}
						if($r["accesskey_key"]=="=" && $url==""){
							$url = "DEFINED";
						}
						$this->defined_list[$index][3] = $url;
						$this->defined_list[$index][4] = $title;
						$found=1;
					}
				}
				if ($found==0){
					// add on to the end of the defined_list
					$this->defined_list[count($this->defined_list)] = Array($r["accesskey_key"],$r["accesskey_label"],2,$r["accesskey_url"],$r["accesskey_title"]);
				}
	        }
	        $this->call_command("DB_FREE",Array($result));
			$out  ="<module name=\"".$this->module_name."\" display=\"accesskey\">";
			
			$out .= "<text clear='1'><![CDATA[".LOCALE_ACCESSKEY_DEFINTION."]]></text>";
			$out .= "<access_list>";
			$max = count($this->defined_list);
			for ($i=0; $i<$max; $i++){
				$out .= "<accesskey letter='" . $this->defined_list[$i][0] . "' type='".$this->defined_list[$i][2] . "'>\n";
				$out .= "	<label><![CDATA[".$this->defined_list[$i][1] . "]]></label>\n";
				$out .= "	<title><![CDATA[".$this->check_parameters($this->defined_list[$i],4) . "]]></title>\n";
				$out .= "	<url><![CDATA[".$this->check_parameters($this->defined_list[$i],3) . "]]></url>\n";
				$out .= "</accesskey>\n";
			}
				$out .= "<accesskey letter='-' type='0'>\n";
				$out .= "	<label><![CDATA[Reduce Font Size]]></label>\n";
				$out .= "	<title><![CDATA[Reduce Font Size]]></title>\n";
				$out .= "	<url><![CDATA[-/-reduce-font.php]]></url>\n";
				$out .= "</accesskey>\n";
				$out .= "<accesskey letter='=' type='0'>\n";
				$out .= "	<label><![CDATA[Increase Font Size]]></label>\n";
				$out .= "	<title><![CDATA[Increase Font Size]]></title>\n";
				$out .= "	<url><![CDATA[-/-increase-font.php]]></url>\n";
				$out .= "</accesskey>\n";
			$out .= "</access_list>";
			$out .= "<text clear='1'><![CDATA[".LOCALE_ACCESSKEY_DEFINTION_BROWSERS."]]></text>";
			$out .="</module>";
			return $out;
		} else {
			return $this->parent->accesskeys($parameters);
		}
	}
	

}
?>