<?php
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- libertas-Solutions
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Class :: users.php
- Written By :: Adrian Sweeney
- Date :: 09 Oct 2002
-
- Description ::
-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Function List
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Command($user_command) want to do anything this is the function to call
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
class sax_parser extends module{
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-  Class Variables
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	var $module_name="sax_parser";
	var $module_modify	 		= '$Date: 2004/07/31 11:59:58 $';
	var $module_version 			= '$Revision: 1.2 $';
	var $module_admin="0";
	var $xml_page="";
	var $xsl_page="transformed";
	var $xml_page_loaded=0;
	var $xsl_page_loaded=0;
	var $xml_parser;
	var $depth=0;
	var $module_command="XMLPARSER_"; 		// all commands specifically for this module will start with this token
	var $output = Array();
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-  Class Methods
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	function command($user_command,$parameter_list=array()){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- If debug is turned on then output the command sent and the parameter list too.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- This is the main function of the Module this function will call what ever function
		- you want to call.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
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
			if ($user_command==$this->module_command."LOAD_XML_STR"){
				return $this->load_xml_str($parameter_list[0]);
			}
			if ($user_command==$this->module_command."LOAD_XML_FILE"){
				return $this->load_xml_file($parameter_list[0]);
			}
			if ($user_command==$this->module_command."LOAD_XSL_STR"){
				return $this->load_xsl_str($parameter_list[0]);
			}
			if ($user_command==$this->module_command."LOAD_XSL_FILE"){
				return $this->load_xsl_file($parameter_list[0]);
			}
			if ($user_command==$this->module_command."TRANSFORM"){
				return $this->transform_xml();
			}
			if ($user_command==$this->module_command."TEST"){
				return $this->test();
			}
		}else{
			// wrong command sent to system
		}
	}
	
	function load_xml_str($str=""){
		//$this->xml_page=xmldoc($str);
		$this->xml_page=$str;
		$this->xml_page_loaded=1;
	}
	function load_xsl_str($str=""){
		//$this->xsl_page=domxml_xslt_stylesheet($str);
		$this->xsl_page=$str;
		$this->xsl_page_loaded=1;
	}
	
	function load_xml_file($filename=""){
		$this->xml_page=domxml_open_file($filename);
		$this->xml_page_loaded=1;
	}
	function load_xsl_file($filename=""){
		$fd = fopen ($filename, "r");
		$this->xsl_page = fread ($fd, filesize ($filename));
		fclose ($fd);
		$this->xsl_page_loaded=1;
	}
	function transform_xml(){
		$html="";
		if ($this->xml_page_loaded==1){
			$this->call_command("XMLPARSER_ERROR",array(__FILE__,__LINE__,"There is no XML document available"));
		}
		if (($this->xsl_page_loaded==1) && ($this->xml_page_loaded==1)){
			$xp = xml_parser_create();
			$this->xml_parser = $xp;
			xml_set_object($this->xml_parser, $this);
			xml_set_element_handler($this->xml_parser, "startElement", "endElement");
			xml_set_character_data_handler($this->xml_parser, "characterData");
			xml_set_external_entity_ref_handler($this->xml_parser, "externalEntityHandler");
			xml_parser_set_option($this->xml_parser, XML_OPTION_CASE_FOLDING, false);
			print "<ul>";
			xml_parse($this->xml_parser, $this->xml_page);
			print "</ul>";
		}
		for ($index=0,$max=count($this->output);$index<$max;$index++){
			for ($x=0,$maxx=count($this->output[$index]);$x<$maxx;$x++){
					print "<li>";
				for ($y=0,$maxy=count($this->output[$index][$x]);$y<$maxy;$y++){
					
					if (is_array($this->output[$index][$x][$y])){
						$z=0;
						foreach ($this->output[$index][$x][$y] as $key => $value){
							print "<br>$index $x $y $z(".$this->output[$index][$x][$y][$key].")";
							$z++;
						}
					} else {
						print "<br>$index $x $y ".$this->output[$index][$x][$y];
					}
				}
					print "</li>";	
			}
		}
		return "";
	}
	
	function startElement($parser, $name, $attributes){
		$this->output[$this->depth][count($this->output[$this->depth])]=Array($name,$attributes);
		$this->depth ++;
		//print "<li>startElement ($this->depth):: $name, ".join($attributes,", ")."<br><ul>";
		
	}
	
	function endElement($parser, $name){
		$this->depth --;
		//print "</ul>endElement :: $name <br></li>";
	}
	
	function characterData($parser, $data){
	//	print "<li>$this->depth characterData :: $data</li><br>";
	}
	function externalEntityHandler($parser, $entityName, $base, $systemId, $publicId){
		print "externalEntityHandler, $entityName, $base, $systemId, $publicId<br>";
	}
	
	
}
?>