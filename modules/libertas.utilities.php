<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.utilities.php
* @date 09 Oct 2002
*/
/**
* A list of Utilities for use with Engine
*/
class utilities extends module{
	/**
	*  Class Variables
	*/
	var $module_name				= "utilities";
	var $module_name_label 			= "Utility Function Module ";
	var $module_modify	     		= '$Date: 2005/02/08 17:01:14 $';
	var $module_version 			= '$Revision: 1.15 $';
	var $module_command				= "UTILS_"; 		// all commands specifically for this module will start with this token
	var $modules 					= array(); 				// A list of all the modules in the system.
	/**
	*  Class Methods
	*/

	function command($user_command,$parameter_list=array()){


		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			$this->utilities_debug_command_parameters($this->module_name,$user_command,$parameter_list,__LINE__,"command");
		}
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call. but only if it starts wiht the value held in module_command
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
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."DEBUG_COMMAND_PARAMETERS"){
				return $this->utilities_debug_command_parameters($parameter_list[0],$parameter_list[1],$parameter_list[2],$parameter_list[3],$parameter_list[4]);
			}
			if ($user_command==$this->module_command."GENERATE_KEYWORDS"){
				return $this->generate_keywords($parameter_list[0],$parameter_list[1],$parameter_list[2]);
			}
			if ($user_command==$this->module_command."STRIP_SWEARWORDS"){
				return $this->strip_swearwords($parameter_list);
			}
			if ($user_command==$this->module_command."EVALUATE_KEYWORDS"){
				return $this->evaluate_keywords($parameter_list[0],$parameter_list[1],$parameter_list[2]);
			}
			if ($user_command==$this->module_command."DEBUG_ENTRY"){
				return $this->utilities_debug_entry($parameter_list[0],$parameter_list[1],$parameter_list[2],$parameter_list[3]);
			}
			if ($user_command==$this->module_command."DISPLAY_DEBUG"){
				return $this->display_debug();
			}
			if ($user_command==$this->module_command."DISPLAY_ERRORS"){
				return $this->display_errors();
			}
			if ($user_command==$this->module_command."REGENERATE_KEYWORDS"){
				return $this->generate_keywords($parameter_list[0],$parameter_list[1],"HTML",$parameter_list[2]);
			}
            if ($user_command==$this->module_command."SHOW_DEBUG"){
                $this->print_debug();
            }
		}else{
			return -1;
			// wrong command sent to system
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

		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;

	}
    /**
    * adds a debug line to the output
    *
    * @param String module executing functionaility
    * @param String Command called
    * @param String Parameters
    * @param String line number executing code
    * @param String string representing function data
    */
	function utilities_debug_command_parameters($module,$cmd,$param,$line,$fn){
        $max_length=count($param);
        $time = $this->libertasGetTime();
		$outtext = "<TR>
			            <TD valign='top' style='color:black;background-color:#fffff;font-size:14px'>
				            <strong>Module </strong>$module<br/>
				            <strong>Line </strong>$line<br/>
                            <strong>Function </strong>$fn<br/>
				            <strong>Command </strong>$cmd<br/>
                            <strong>Max length </strong>$max_length<br/>
				            <strong>Time </strong>$time
                        </TD>
				        <TD valign='top'><pre  style='color:black;background-color:#fffff;font-size:14	px'>";
		$outtext .= print_r($param,true);
		$outtext .= "   </pre></TD>
                    </TR>";
		$this->debug_script.=$outtext;
		return 1;
	}

    /**
    * adds a debug line to the output
    *
    * @param String module
    * @param String fn
    * @param String line
    * @param String description
    */
	function utilities_debug_entry($module,$fn,$line,$description){
        $time = $this->libertasGetTime();
		$outtext = "<TR>
			<TD valign='top' style='color:black;background-color:#fffff;font-size:10px'>
				<strong>Module </strong>$module<br/>
				<strong>Line </strong>$line<br/>
				<strong>Function </strong>$fn<br/>
				<strong>Time </strong>$time</TD>
				<TD valign='top' style='color:black;background-color:#fffff;font-size:10px'><pre style='color:black;background-color:#fffff;font-size:10px'>";
		$outtext .= print_r($description, true);
		$outtext .= "</pre></TD></TR>";
		$this->debug_script .=$outtext;
		return 1;
	}

    /**
    * generates a number of keywords from a string
    *
    *
    * @param String str mixed HTML or plain text
    * @param Integer number of keywords
    * @param String type of output default = "__XML__", other options "__ARRAY__" and 
    * @param String extra ignore keyword list
    */
	function generate_keywords($str, $number_of_keywords, $type="__XML__", $extraIgnoreList=""){
		$this->client_identifier = $this->parent->client_identifier;
		$return_array=Array();
		$str = join(" <",split("<",html_entity_decode($str)));
		$str = join(" >",split(">",$str));
		$str = strtolower($this->validate(htmlentities(strip_tags($str,""))));
		$str = join("</span> ",split("</span>",join(" <span",split("<span",$str))));
		$find = array("/\b\w{1,4}\b/","'  '","'  '","/\W/","'&nbsp;'");
		$replace = array(" ", " ", " ", " ", " ");
		$data_dir	 = $this->parent->site_directories["DATA_FILES_DIR"];

		if (file_exists($data_dir."/remove_keyword_list_".$this->client_identifier.".txt")){
			$file = file($data_dir."/remove_keyword_list_".$this->client_identifier.".txt");
			for($index=0,$max=count($file);$index<$max;$index++){
				$find[count($find)] = "' ".trim($file[$index])." '";
				$replace[count($replace)] = " ";
			}
		}
		if ($extraIgnoreList!=""){
			$extra = split(",", $extraIgnoreList);
			for($index=0,$max=count($extra);$index<$max;$index++){
				$find[count($find)] = "' ".trim($extra[$index])." '";
				$replace[count($replace)] = " ";
			}
		}
		$str = preg_replace($find,$replace,$str);
		$str = trim($str); 
		$list= split(" ",$str);
		$keywords = array();
		for ($index=0,$max=count($list);$index<$max;$index++){
			$key = trim($list[$index]);
			if (strlen($key)>0){
				if (empty($keywords[$key])){
					$keywords[$key]=0;
				}
				$keywords[$key]++;
			}
		}
		arsort($keywords);
		$return_str="";
		$count=0;
		$col=0;
		foreach ($keywords as $key => $value){
			if($type=="__XML__"){
				$return_str .= "<keyword count=\"$value\"><![CDATA[".strtolower($key)."]]></keyword>";
			} else if($type=="__ARRAY__"){
				$return_array[count($return_array)] = Array("key"=>strtolower($key), "count"=>$value);
			} else {
				if ($col == 0){
					$return_str .= "<tr>";
				}
				$return_str .= "<td><input checked='true' type='checkbox' name='keywords[]' value='$value, ".strtolower($key)."' id='keyword_".$count."' onclick='javascript:ignore_keyword(this)'/><label for='keyword_".$count."' >".strtolower($key)." ($value)</label></td>";
				$col++;
				if ($col == 3){
					$return_str .= "</tr>";
					$col=0;
				}
			}
			$count++;
			if ($count>=$number_of_keywords){
				break;
			}
		}
		if($type=="__XML__"){
			return "<keywords>$return_str</keywords>";
		} else if($type=="__ARRAY__"){
			return $return_array;
		}else {
			if ($col !=0)
				$return_str .= "</tr>";
			if ($count!=0){
				return "<table width='100%' border='0'>".$return_str."</table>";
			} else {
				return "";
			}
		}

	}

    /**
    * evaluates a document and scores it
    *
    *
    * @param String phrase to search for
    * @param String Comma seperated list of keywords
    * @param String The complete document
    *
    * @return Integer score the result
    */
	function evaluate_keywords($search_phrase,$keyword_list,$document){
		$search_words=split(" ",$search_phrase);
		$keyword_list=split(", ",$keyword_list);
		$compare=array();
		for ($index=0,$number_of_elements=count($keyword_list);$index<$number_of_elements;$index++){
			$keyword=split(" = ",$keyword_list[$index]);
			$compare[$keyword[0]]=$keyword[1];
		}
		$score=0;
		for ($index=0,$number_of_elements=count($search_words);$index<$number_of_elements;$index++){
			if(!empty($compare[$search_words[$index]])){
				$score+=$compare[$search_words[$index]];
			}
		}
		$max_pow =32;
		($v=count(split(" ",$search_phrase)))<$max_pow ? $r=$v : $r=$max_pow ;
		$times =pow(2,$r);
		$score+= (substr_count($document,"$search_phrase")*$times);
		return $score;
	}
	

    /**
    * display Errors
    *
    * @return String Error message
    */
	function display_errors(){
		if (strlen($this->error_messages)>0){
			return $this->error_messages;
		}else{
			return "";
		}
	}

    /**
    * output the debug data table
    *
    * @return String a table hiolding all the debug info
    */
	function display_debug(){
		$str="";
		if (strlen($this->debug_script)>0){
		    $headstr = "<TR>
		    	        <TH width='20%'>Info</TH>
			            <TH width='80%'>Data</TH>
		            </TR>";
			if (strlen($this->check_parameters($_SESSION,"debug_list"))>0){
				$str .= $_SESSION["debug_list"];
				$_SESSION["debug_list"]="";
			}
			return "$str<table style='clear:both;' width=100% border=1 background='#ffffff'>$headstr".$this->debug_script."</table>";
		}else{
			return "";
		}
	}

    /**
    * strip swear words from content
    *
    *
    * @param Array (String "source_string") 
    * @return String modified content
    */
	function strip_swearwords($parameters){
		$string			= $this->check_parameters($parameters,"source_string");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_thread_form",__LINE__, $string));
		}
		$data_files		= $this->parent->site_directories["DATA_FILES_DIR"];
		$fname			= $data_files."/swear_keyword_list_".$this->client_identifier.".txt";
		$find_list		= Array();
		$replace_list	= Array();
		if (file_exists($fname)){
			$find_list = file($fname);
		}
		$m = count($find_list);
		for ($i=0;$i<$m;$i++){
			$replace_list[$i] = "[******]";
			$find_list[$i] = trim(chop($find_list[$i]));
		}
		$string = str_replace($find_list, $replace_list, $string);
		return $string;
	}
    /**
    * print the debug info
    *
    */
    function print_debug(){
    }
}
?>