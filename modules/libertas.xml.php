<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.xml.php
* @date 09 Oct 2002
*/
/**
* Generate generic XML data tags for output.
* This is a module with no administration section.
*/
class xml_tag extends module{
	/**
	*  Class Variables
	*/
	var $module_name="xml_tag";
	var $module_name_label="XML Tag Generator (SYSTEM)";
	var $module_modify	 		= '$Date: 2005/03/18 14:55:41 $';
	var $module_version 			= '$Revision: 1.7 $';
	var $module_admin="0";
	var $xml_page="";
	var $xsl_page="transformed";
	var $xml_page_loaded=0;
	var $xsl_page_loaded=0;
	var $module_command="XMLTAG_"; 		// all commands specifically for this module will start with this token
	
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
			if ($user_command==$this->module_command."GENERATE_XML_TAG"){
				return $this->generate_element($parameter_list[0], $parameter_list[1], $parameter_list[2], $parameter_list[3], $parameter_list[4]);
			}
			if ($user_command==$this->module_command."GENERATE_XML_BUTTON"){
				return $this->generate_button($parameter_list);
			}
			if ($user_command==$this->module_command."GENERATE_XML_ATTRIBUTE"){
				return $this->generate_attribute($parameter_list[0], $parameter_list[1], $parameter_list[2], $parameter_list[3], $parameter_list[4]);
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	
	/**
	* generate single XML tag for form field
	*/
	
	function generate_element($type,$name,$label,$value,$entries=null){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"generate_element()",__LINE__,"[$type,$name,$label,$value,$entries]"));
		}
		$field_type = strtolower($type);
		if (($field_type=="hidden")||($field_type=="text")){
			return "<input type=\"$type\" name=\"$name\" label=\"$label\" value=\"$value\"/>";
		}
		
		if ($field_type=="checkbox"){
			$out  =  "<checkbox type=\"$value\" name=\"$name\[]\" label=\"$label\">";
			if ($entries!=null){
				if (count($entries[0])>0){
					for ($entry_index = 0, $entry_length = count($entries); $entry_index<$entry_length;$entry_index++){
						$out  = "<options module=\"".$entries[$entry_index][0]."\">";
						for ($element_index = 0, $element_length = count($entries[$entry_index][1]) ; $element_index<$element_length ; $element_index++){
							$out .= "<option value=\"".$entries[$entry_index][1][$element_index][0]."\">".$entries[$entry_index][1][$element_index][1]."</option>";
						}
						$out .= "</options>";
					}
				}else{
					$out  = "<options module=\"\">";
					for ($element_index = 0, $element_length = count($entries[$entry_index][1]) ; $element_index<$element_length ; $element_index++){
						$out .= "<option value=\"".$entries[$entry_index][1][$element_index][0]."\">".$entries[$entry_index][1][$element_index][1]."</option>";
					}
					$out .= "</options>";
				}
					
			}
			$out .= "</checkboxes>";
			return $out;
		}
	}
	/**
	* generate single button
	*/
	
	function generate_button($parameters){
		$iconify = $this->check_parameters($parameters,0);
		$cmd	 = $this->check_parameters($parameters,1);
		$alt	 = $this->check_parameters($parameters,2);
		$param	 = $this->check_parameters($parameters,3);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"generate_button()",__LINE__,"[$iconify,$cmd,$alt,$param]"));
		}
		return "<button command=\"$cmd\" alt=\"$alt\" iconify=\"$iconify\" parameters=\"$param\" />\n";
	}
	
	function generate_attribute($name, $value, $show="YES", $link="NO", $alt=""){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"generate_atribute()",__LINE__,"[$name,$value]"));
		}
		return "<attribute name=\"$name\" show=\"$show\" link=\"$link\" alt='$alt'><![CDATA[$value]]></attribute>\n";

	}
}
?>