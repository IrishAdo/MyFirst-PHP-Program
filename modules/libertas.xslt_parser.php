<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.xslt_paraser.php
* @date 22 August 2003
*/
/**
* This parser is written to try to use the LibXML2 functionality with out using sablotron
*/
class xsl_parser extends module{
	/**
	*  Class Variables
	*/
	var $module_name				= "xsl_parser";
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_SYSTEM";
	var $module_label				= "XSL PARSER";
	var $module_name_label			= "LIB2XML XSL Transfromation Module";
	var $module_modify	 			= '$Date: 2005/03/14 09:34:51 $';
	var $module_version 			= '$Revision: 1.17 $';
	var $module_admin				= "0";

	var $xml_filename				= "";
	var $xml_file					= "";
	var $xml_page					= "";
	var $xml_page_type				= "";
	var $xml_page_loaded			= 0;

	var $xsl_filename				= "";
	var $xsl_file					= "";

	var $xsl_page_type				= "";
	var $xsl_page					= "transformed";
	var $xsl_page_loaded			= 0;

	var $module_debug				= false;
	var $windows					= false;
	var $module_command				= "XMLPARSER_"; 		// all commands specifically for this module will start with this token
	var $tmp_dir					= "";
	var $useFile 					= "";
	var $directory_seperation_tag	= "";
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
			/**
			* basic commands
			*/
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
			/**
			* specific functions for this module
			*/
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
				$this->xsl_file = $parameter_list[0];
				return $this->load_xsl_file($parameter_list[0]);
			}
			if ($user_command==$this->module_command."TRANSFORM"){
				return $this->transform_xml();
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	function initialise(){
		$this->client_identifier = $this->parent->client_identifier;
		$access = $this->check_parameters($_SESSION,"SESSION_GROUP_ACCESS");
		if (!is_array($access)){
			$access_array = Array();
			$access_array[0] = $access;
		} else {
			$access_array = $access;
		}
		for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
			if (("ALL"==$access_array[$index]) || ("XMLPARSER_ALL" == $access_array[$index]) || ("XMLPARSER_LIST" == $access_array[$index])){
				$this->module_admin_access=1;
			}
		}
		$this->phpversiondata = split("\.", phpversion()."");
		if($this->phpversiondata[0]>=5){
			
		} else {
			$this->tmp_dir 	= $this->call_command("ENGINE_GET_PATH",Array("TRANSFORM_TMP"));
			$this->windows  = stristr(php_uname(), 'windows') !== false;
			if (!$this->windows)
				$this->directory_seperation_tag = "/";
			else
				$this->directory_seperation_tag = "\\";
			$this->useFile = $this->tmp_dir .$this->directory_seperation_tag . uniqid(session_id());
		}
		return 1;
	}
	function load_xml_str($str=""){
		if ($str!=""){
			if($this->phpversiondata[0]>=5){
				$this->xml_page 		= $str;
				$this->xml_page_loaded	= 1;
				$this->xml_page_type	= "str";
			} else {
				$transform_root	= $this->parent->site_directories["TRANSFORM_TMP"];
				$this->useFile = uniqid("tmp_xml_file");
				$usingFile = $transform_root . "/". $this->useFile . '.xml';
				if ($fp = fopen($usingFile,'w')) {
					fwrite($fp, $str);
					fclose($fp);
				}
				$this->xml_filename 	= $usingFile;
				$this->xml_page_loaded	= 1;
				$this->xml_page_type	= "file";
			}
		}
	}
	function load_xsl_str($str=""){
		if ($str!=""){
			if($this->phpversiondata[0]>=5){
				$this->xsl_page 		= $str;
				$this->xsl_page_loaded	= 1;
				$this->xsl_page_type	= "str";
			} else {
				$useFile = $this->useFile.'.xslt';
				if ($fp = fopen($useFile,'w')) {
					fwrite($fp, $str);
					fclose($fp);
				}
				$this->xsl_filename = $useFile;
				$this->xsl_page_loaded=1;
				$this->xsl_page_type	= "file";
			}
		}
	}
	
	function load_xml_file($filename=""){
		$this->xml_filename 	= $filename;
		$this->xml_page_loaded	= 1;
		$this->xml_page_type			= "file";
	}
	function load_xsl_file($filename=""){
		$this->xsl_filename 	= $filename;
		$this->xsl_page_loaded	= 1;
		$this->xsl_page_type			= "file";
	}
	
	function transform_xml(){
		$msg  = "";
		$start = $this->get_number_from_ip("192.168.0.1");
		$end = $this->get_number_from_ip("192.168.0.255");
		$current = $this->get_number_from_ip($_SERVER["REMOTE_ADDR"]);
		$transform_root	= $this->check_parameters($this->parent->site_directories,"TMP",$this->check_parameters($this->parent->site_directories,"TRANSFORM_TMP"));
		$find_array    = Array("\r\n<li","\r\n<img","\r\n</td","\n\r<img","\n\r</td","\n<img","\n</td","\r<img","\r</td","&lt;","&gt;","\r\n<IMG","\r\n</TD","\n<IMG","\n</TD","&lt;","&gt;","\r<IMG","\r</TD", "[[nbsp]]", "[[rightarrow]]", "[[leftarrow]]","[[183]]" ,"[[return]]"	,"[[copy]]");
		$replace_array = Array("<li",    "<img",    "</td",    "<img",    "</td",    "<img",  "</td",  "<img",  "</td",  "<",   ">",   "<IMG",    "</TD",    "<IMG",  "</TD",  "<",   ">",   "<IMG",  "</TD"  , "&#160;"  , "&#187;"        , "&#171;"       ,"&#183;"	,"<br>"			,"&copy;");
		if ($this->parent->script != "admin/load_cache.php"){
			$find_array[count($find_array)] 		= "&amp;amp;quot;";
			$replace_array[count($replace_array)] 	= "\"";
			$find_array[count($find_array)] 		= "\t";
			$replace_array[count($replace_array)]	= "";
			$find_array[count($find_array)] 		= "&amp;amp;";
			$replace_array[count($replace_array)]	= "&";
			$find_array[count($find_array)] 		= "&amp;";
			$replace_array[count($replace_array)]	= "&";
			$find_array[count($find_array)] 		= "&quot;";
			$replace_array[count($replace_array)]	= "\"";
			$find_array[count($find_array)] 		= "&amp;quot;";
			$replace_array[count($replace_array)] 	= "\"";
			$find_array[count($find_array)] 		= "src=\"";
			$replace_array[count($replace_array)] 	= "src=\"".$this->parent->base;			
			$find_array[count($find_array)] 		= "[[quot]]";
			$replace_array[count($replace_array)] 	= "&#34;";
			$find_array[count($find_array)] 		= "[[apos]]";
			$replace_array[count($replace_array)] 	= "&#39;";
			$find_array[count($find_array)] 		= "[[pound]]";
			$replace_array[count($replace_array)] 	= "&#163;";
			
			
			
		}

		if (($this->xsl_page_loaded==1) && ($this->xml_page_loaded==1)){
			/*************************************************************************************************************************
            * parse xml and xsl with PHP5 code
            *************************************************************************************************************************/
			if ($this->phpversiondata[0]*1>=5){
				ob_start();
					$dom = new domDocument();
					if($this->xsl_page_type=="file"){
  						$dom->load($this->xsl_filename);
					} else {
						$dom->loadXML($this->xsl_page);
					}
					$proc = new xsltprocessor;
					$xsl = $proc->importStylesheet($dom);
					$document = new DomDocument();
    	            if($this->xml_page_type=="file"){
  						$document->load($this->xml_filename);
					} else {
//						$document->loadXML($this->xml_page);
						$arr = split("]]>",$this->xml_page);
						
						$val_str = "";
						foreach ($arr as $val){
							$pos = strpos($val, "<![CDATA[");
							if ($pos === false) {
								$val_str .= $val."<![CDATA[]]>";
								
						//		substr($val,$pos,strpos($str,"]]>"));
						//		echo "The string '$findme' was not found in the string '$mystring'";
							} else {
								$val_str .= $val."]]>";
						//		echo "The string '$findme' was found in the string '$mystring'";
						//		echo " and exists at position $pos";
							}
						
						}
						
						//echo $this->xml_page;
						//die;
						//$document->loadXML($this->xml_page);
						//$document->loadXML(str_replace("<![CDATA[]]>","",$val_str));
						$document->loadXML(str_replace("</optgroup></optgroup>","</optgroup>",str_replace("<![CDATA[]]>","",$val_str)));
					}
					$out = $proc->transformToXml($document);
					$errors = ob_get_contents();
				ob_end_clean();
				$out = str_replace($find_array ,$replace_array ,$out);
				if(strlen($errors)>0) {
					$msg = "<h1>XSLT transformation failure - Error Code #LS000012</h1><p><strong>I am sorry there seems to have been an error with the style sheet used to produce this page.</strong></p><p>Please contact webmaster</p><p>Transformation failed because of the following error code. #LS000012.<"."!-- $this->xsl_filename  --"."></p>";
					$extra ="<p><strong>Domain</strong> : ".$this->parent->domain." <br/>
					<strong>Location</strong> : ".$this->parent->real_script." <br/>
					<strong>Base</strong> : ".$this->parent->base." <br/>
					<strong>Query String</strong> : ".$this->parent->qstr." <br/>
					<strong>Complete</strong> : http://".$this->parent->domain.$this->parent->base.$this->parent->real_script."?".$this->parent->qstr." <br/>
					<strong>Page type</strong> : ".$this->parent->module_type." <br/>
					<strong>Browser string</strong> : ".$this->check_parameters($_SERVER,"HTTP_USER_AGENT")."<br />
					<hr />
					<strong>User</strong> : ".$this->check_parameters($_SESSION,"SESSION_USER_NAME")."<br />
					<strong>Name</strong> : ".$this->check_parameters($_SESSION,"SESSION_FIRST_NAME")." ".$this->check_parameters($_SESSION,"SESSION_LAST_NAME")."<br />
					<strong>User email</strong> : ".$this->check_parameters($_SESSION,"SESSION_EMAIL")."<br />
					<strong>User Details</strong> : <a href='http://".$this->parent->domain.$this->parent->base."admin/index.php?command=CONTACT_VIEW_USER&amp;uid_identifier=".$this->check_parameters($_SESSION,"SESSION_CONTACT_IDENTIFIER")."'>View Details</a><br />
					<strong>Users IP Address</strong> : ".$this->check_parameters($_SERVER,"REMOTE_ADDR")."<br />
					<strong>Users Session Id</strong> : ".session_id()."<br />
					<hr />
					</p><h2>Error Messages</h2>";
					if (($current < $start) || ($current>$end)){
						$this->call_command("EMAIL_QUICK_SEND",
							Array(
								"from" => "support@libertas-solutions.com", 
								"subject" => "XSLT transformation failure - Error Code #LS000012-???? on ".$this->parent->domain, 
								"body" => $extra.$msg.$errors."<br/><pre>-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n<strong>Get</strong>\n".print_r($_GET,true)."\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n<strong>POST</strong>\n".print_r($_POST,true)."\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n<strong>Session</strong>\n".print_r($_SESSION,true)."\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n</pre><strong>XML</strong><code>".$this->xml_page."</code>", 
								"format"=>"HTML", 
								"to" => "support@libertas-solutions.com" 
								//"cc" => Array("")
							)
						);
					}
					print "<html>
							<head>
								<meta name=\"robots\" content=\"noindex,nofollow\" /> 
								<meta name=\"revisit-after\" content=\"1 days\" />
								<meta name=\"robots\" content=\"nocache\" />
								<meta name=\"robots\" content=\"noarchive\" />
								<style>
									h1{font-size:16px;}
									body{font-size:12px;}
								</style>
							</head>
							<body>
								<center>
									<table width='500' style='border=1px solid #000000;filter:progid:dximagetransform.microsoft.gradient(gradienttype=0,startcolorstr=#cccccc,endcolorstr=#FFFFFF);'>
										<tr>
											<td>$msg<div style='display:none'>$errors</div></td>
										</tr>
									</table>
								</center>
							</body>
					   </html>"."<!-- $errors -->";
					 $this->exitprogram();
				} else if ($this->parent->script != "admin/load_cache.php"){
					return html_entity_decode($out);
				} else {
					return $out;
				}
			/*************************************************************************************************************************
            * parse xml and xsl with PHP4 code
            *************************************************************************************************************************/
			} else {
				$errors ="";
				if ($this->windows){
					$xslt_directory = $this->call_command("ENGINE_GET_PATH",Array("TRANSFORM_DIR"));
					$program ="xsltproc";
					if (strlen($xslt_directory)>0){
						$program =$xslt_directory.$this->directory_seperation_tag.$program;
					}
					$find = false;
					$find = file_exists($program.".exe");
					if ($find){
						if ($xslt_directory=="")
							$cmd = "xsltproc -o ".$transform_root."/".$this->useFile.".html ".$this->xsl_filename." ".$transform_root."/".$this->useFile.".xml" ;
						else
							$cmd = "".$xslt_directory.$this->directory_seperation_tag."xsltproc -o \"".$transform_root."/".$this->useFile.".html\" \"".$this->xsl_filename."\" \"".$transform_root."/".$this->useFile.".xml\"" ;
							$returnedValue = $this->execute_system_command($cmd, $this->windows);
						if (file_exists($transform_root."\\".$this->useFile.".html")){
							$fd = fopen ($transform_root."/".$this->useFile.".html", "r") or die;
							$out = "";
							if ($fd){
								$out .= fread ($fd, filesize ($transform_root."/".$this->useFile.".html"));
								fclose ($fd);
								@unlink($transform_root."/".$this->useFile.".html");
								@unlink($transform_root."/".$this->useFile.".xml");
							}
							$msg=true;
							$out = str_replace($find_array, $replace_array, $out);
							if ($this->parent->script != "admin/load_cache.php"){
								return html_entity_decode($out)."<!-- $errors -->";
							} else {
								return $out."<!-- $errors -->";
							}
						} else {
							$msg=false;
						}
						if(!$msg) {
							$msg = "<h1>XSLT transformation failure - Error Code #LS000012</h1><p><strong>I am sorry there seems to have been an error with the style sheet used to produce this page.</strong></p><p>Please contact webmaster</p><p>Transformation failed because of the following error code. #LS000012.<"."!-- $this->xsl_file --"."></p>";
							$extra ="<p><strong>Domain</strong> : ".$this->parent->domain." <br/>
							<strong>Location</strong> : ".$this->parent->real_script." <br/>
							<strong>Base</strong> : ".$this->parent->base." <br/>
							<strong>Page type</strong> : ".$this->parent->module_type." <br/>
							</p><h2>Error Messages</h2>".$this->txt2html($returnedValue);
							if (($current < $start) || ($current>$end)){
								$this->call_command("EMAIL_QUICK_SEND",Array("from" => "support@libertas-solutions.com", "subject" => "XSLT transformation failure - Error Code #LS000012-???? on ".$this->parent->domain, "body" => $msg.$extra,"format"=>"HTML", "to" => "support@libertas-solutions.com"));
							}
							print "<html>
									<style>
										h1{font-size:16px;}
										body{font-size:12px;}
									</style>
									<body>
										<center>
											<table width='500' style='border=1px solid #000000;filter:progid:dximagetransform.microsoft.gradient(gradienttype=0,startcolorstr=#cccccc,endcolorstr=#FFFFFF);'>
												<tr>
													<td>$msg.$extra</td>
												</tr>
											</table>
										</center>
									</body>
								   </html>";
						}
					}
				} else {
					ob_start();
					$xmldoc = domxml_open_file($this->xml_filename);
					$xsldoc = domxml_xslt_stylesheet_file($this->xsl_filename);
					$result =  $xsldoc->process($xmldoc);
					$out = $xsldoc->result_dump_mem($result);
					$errors = ob_get_contents();
					ob_end_clean();
					$out = str_replace($find_array, $replace_array, $out);
					if ($this->parent->script != "admin/load_cache.php"){
						return html_entity_decode($out)."<!-- $errors -->";
					} else {
						return $out."<!-- $errors -->";
					}
				}
			}
		} else {
			if ($this->xsl_page_loaded==0){
				if ($this->xsl_filename!=""){
					$subject = "Error Code #LS000008";
					$msg = "<h1>Server Stopped - Error Code #LS000008</h1><p><strong>Sorry I was unable to find the stylesheet to transform with.<br />".$this->xsl_filename."<br />This can be caused by mis-configuration of the PATH information.</strong></p><p>For support please contact your web master </p>";
				}else{
					$subject = "Error Code #LS000009";
					$msg = "<h1>Server Stopped - Error Code #LS000009</h1><p><strong>Sorry I was unable to find the stylesheet to transform with.<br />You did not supply a valid stylesheet.<br />This can be caused by mis-configuration of the PATH information.</strong></p><p>For support please contact your webmaster</p>";
				}
			} else if($this->xml_page_loaded==0){
				if ($this->xsl_filename!=""){
					$subject = "Error Code #LS0000010";
					$msg = "<h1>Server Stopped - Error Code #LS000010</h1><p><strong>Sorry I was unable to find any XML structure to transform.</strong></p><p>".$this->xml_filename."</p><p>For support please contact your webmaster</p>";
				}else{
					$subject = "Error Code #LS0000011";
					$msg = "<h1>Server Stopped - Error Code #LS000011</h1><p><strong>Sorry I was unable to find any XML structure to transform.</strong></p><p>You did not supply a valid XML structure</p><p>For support please contact your webmaster</p>";
				}
			}
			if (($current < $start) || ($current>$end)){
				$extra ="<p>Domain : ".$this->parent->domain." <br/>
				Location : ".$this->parent->real_script." <br/>
				Base : ".$this->parent->base." <br/>
				Page type : ".$this->parent->module_type." <br/>
				XSLT : $this->xsl_file
				</p>";
				$this->call_command("EMAIL_QUICK_SEND",Array("from" => "support@libertas-solutions.com", "subject" => "XSLT transformation failure - $subject on ".$this->parent->domain, "body" => $msg.$extra,"format"=>"HTML", "to" => "support@libertas-solutions.com"));
			}
			return $msg;
		}
	}

	
	
	function handle_xslt_error($errorNo, $level, $fields){
		print "$errorNo, $level, $fields";
		print count($errorNo).", ".count($level).", ".count($fields);
	}	

	function get_number_from_ip($ip="0.0.0.0"){
		$ip_values = split("\.",$ip);
		$value = bindec($this->make8(decbin($ip_values[0])).$this->make8(decbin($ip_values[1])).$this->make8(decbin($ip_values[2])).$this->make8(decbin($ip_values[3])));
		return $value;
	}
	function make8($str){
		$left = 8- strlen($str);
		if ($left>0){
			$out = str_repeat("0",$left).$str;
		} else {
			$out = $str;
		}
		return $out;
	}
	
	function execute_system_command($cmd, $running_on_windows = false) {
//print $cmd;
		$p=popen("($cmd)2>&1",'r');
//print $p;
		if (!($p)) return -1;
		$output = '';
		while(!feof($p)){
			$output .= fread($p,4096);
		}
		pclose($p);

		if ($running_on_windows && strstr($output, 'not recognized as an internal or external command')){
			return -2;
		}else if(strstr($output, 'Permission denied')){
			return -3;
		}else if (strstr($output, 'No such file or directory')){
			return -4;
		}
		return $output;

	}
function syscall($command){
if ($proc = popen("($command)2>&1","r")){
while (!feof($proc)) $result .= fgets($proc, 1000);
pclose($proc);
return $result; 
}
}


}
?>