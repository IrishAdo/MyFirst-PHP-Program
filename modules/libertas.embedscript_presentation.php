<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.embedScript.php
* @date 12 Feb 2004
*/
/**
* This module is the administration module for Categories it will allow the user to 
* generate Category LISTS which will contain lists of categories that could be used
* by one or more modules.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
class embedscript extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name				= "embedscript";
	var $module_name_label			= "Embed 3rd party Script Module (Presentation)";
	var $module_admin				= "0";
	var $module_command				= "EMBEDSCRIPT_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "EMBEDSCRIPTADMIN_";
	var $module_label				= "MANAGEMENT_3RD_PARTY";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.11 $';
	var $module_creation 			= "16/07/2004";
	var $searched					= 0;
	var $loadedcat					= Array();
	
	
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
	);
	
	/**
	*  filter options
	*/
	var $display_options			= array();
	
	/**
	*  Access options php 5 will allow these to become private variables.
	*/
	var $admin_access				= 0;
	var $author_admin_access		= 0;
	var $editor_admin_access		= 0;
	var $approve_admin_access		= 0;
	var $add_embedscripts_lists		= 0;
	var $install_access				= 0;
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
			if ($user_command==$this->module_command."TEST"){
				$this->test_temp();
			}
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
//			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
//				return $this->webContainer;
//			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->display($parameter_list);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Create table function allow access if in install mode
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                E M B E D S C R I P T   S E T U P   F U N C T I O N S
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
		$this->load_locale("embedscript");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		/**
		* define the admin access that this user has.
		*/
		return 1;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                         		E M B E D S C R I P T   S I T E   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/

	function display($parameters){
		$store					= 0;
		$current_menu_location  = $this->check_parameters($parameters,"current_menu_location",-1);
		$es_type				= $this->check_parameters($parameters,"es_type",1);
		$embedscriptFields		= $this->check_parameters($parameters,"libertas_embeded_field",Array());
		$wo_owner_id			= $this->check_parameters($parameters,"wo_owner_id",-1);
		$base_uri 				= "";
		$uri					= "";
		$label					= "";
		$fields					= "";
		$root	= $this->parent->site_directories["DATA_FILES_DIR"];
		$fname	= $root."/embedscript_".$this->client_identifier."_$wo_owner_id.xml";
		$now  					= strtotime($this->libertasGetDate());
		//"http://www.domainregireland.com/orders/index.php";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"parameters",__LINE__,"".print_r($parameters,true).""));}
		$sql = "select embedscript_list.* from embedscript_list 
					inner join display_data on es_menu = display_menu and es_client=display_client
				where
					display_command = 'EMBEDSCRIPT_DISPLAY' and es_client = $this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$label			= $r["es_label"];
			$identifier		= $r["es_identifier"];
			$base_uri		= $r["es_base_uri"];
			$cache			= $r["es_cache"];
			$clean			= $r["es_auto_clean"];
			$last_cache		= $r["es_last_cached"];
        }
		if ($cache!=0){
			$hr = (360000);
			//$levels_of_cache = Array(0,($hr*1),($hr*2),($hr*4),($hr*8),($hr*12),($hr*24),($hr*24*7),($hr*24*30),($hr*24*60));
			$cache_time = $cache;
			$cache=0;
			if ($now - $last_cache > $cache_time){
				$store	= 1;
				$cache	= 1;
			} else {
				if (file_exists($fname)){
					$lines	= file($fname);
					return implode($lines);
				} else {
					$store	= 1;
					$cache	= 1;
				}
			}
		} else {
			$cache=1;
		}
		if($cache==1){
			$uri 	 = $this->check_parameters($parameters, "libertas_destination_url", $this->check_parameters($parameters, "les_uri", ""));
			if ($uri==""){
				$uri = $base_uri;
			} else {
				if(strpos($uri,"http")===false){
					$uri = dirname($base_uri)."/$uri";
				} 
			}
			$puri	 = $uri;
			if ($fields!=""){
				$field_list = split(",",$fields);
			} else {
				$field_list= Array();
			}
	        $this->call_command("DB_FREE",Array($result));
	//		$q_string = "sharedsession=".session_id();
			$q_string = "";
			$m = count($embedscriptFields);
			if ($m>0){
				for ($i=0; $i<$m;$i++){
					$f = $embedscriptFields[$i];
					if ($f!="es_embedscriptFields"){
						$v = $this->split_me($this->check_parameters($parameters, $f)," ","%20");
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"uri",__LINE__,"[$f] = [$v]"));}
						if ($v!="" || substr($f,0,4)=="dns_"){
							if ($q_string != ""){
								$q_string .="&";
							}
							$q_string .=$f."=".$v;
						}
					}
				}
			}
			for($i=0;$i<count($field_list);$i++){
				$v = $this->check_parameters($parameters,$field_list[$i]);
				if ($v!=""){
					if ($q_string != ""){
						$q_string .="&";
					}
					$q_string .=$field_list[$i]."=".$v;
				}
			}
			$module_version = split(" ",$this->module_version);
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"uri",__LINE__,"$uri?$q_string"));}
			$curlHandler      = curl_init();
			//curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, 0);
			//curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curlHandler, CURLOPT_COOKIEJAR, "cookie_".session_id().".txt");  //initiates cookie file if needed
			curl_setopt($curlHandler, CURLOPT_COOKIEFILE, "cookie_".session_id().".txt");  // Uses cookies from previous session if exist
			//curl_setopt($curlHandler, CURLOPT_COOKIE, session_name()."=".session_id());  // 
			curl_setopt($curlHandler, CURLOPT_SSL_VERIFYHOST,	2);
			if($this->check_parameters($_SERVER,"HTTPS")=="on"){
				$prefix="https://";
			} else {
				$prefix="http://";
			}
			curl_setopt($curlHandler, CURLOPT_HTTPHEADER,			Array("referer: $prefix".$this->parent->domain.$this->parent->base.$this->parent->script));
			curl_setopt($curlHandler, CURLOPT_USERAGENT,			"Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
			//curl_setopt($curlHandler, CURLOPT_USERAGENT,			"Libertas Solutions 3rd Party script includer v".$module_version[1]);
			if ($es_type=="get" || (!(strpos($uri,"htm")===false))){
				curl_setopt($curlHandler, CURLOPT_URL,        		$uri."?".$q_string);
				curl_setopt($curlHandler, CURLOPT_POST,				0	);
			} else {
				curl_setopt($curlHandler, CURLOPT_URL,        		$uri);
				curl_setopt($curlHandler, CURLOPT_POST,				"application/x-www-form-urlencoded");
				curl_setopt($curlHandler, CURLOPT_POSTFIELDS,		$q_string);
			}
			curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER,	1);
			curl_setopt($curlHandler, CURLOPT_TIMEOUT,			100);
			$data = $this->extract_data(curl_exec($curlHandler), $base_uri);

			if ($data == "" && file_exists($base_uri)){
				$fp = fopen($base_uri, 'r');
				$data = fread($fp, filesize($base_uri));
			}
			
			$info = curl_getinfo($curlHandler);
			$http_codes = parse_ini_file($this->parent->site_directories["MODULE_DIR"]."/ini/curl.ini");
			//echo "The server responded: <br />";
			//echo $info['http_code'] . " " . $http_codes[$info['http_code']];
			//$output		= "<module name='embedscript'><form name='embeded_script_$rank' label='$label'><text><![CDATA[".$data."]]></text></form></module>";
			$output		= "<module name='embedscript'><text clear='1'><![CDATA[".htmlentities($data)."]]></text></module>";
			$succeeded	= curl_errno($curlHandler) == 0 ? true : false;
			curl_close($curlHandler);
			if ($clean!=0){
				$output = $this->clean($output, $clean);
			}
			if ($store==1){
				$fp = fopen($fname,"w");
				fwrite($fp,$output);
				fclose($fp);
				$um = umask(0);
				@chmod($fname, LS__FILE_PERMISSION);
				umask($um);
				$sql = "update embedscript_list set es_last_cached='$now' where es_client=$this->client_identifier and es_identifier=$wo_owner_id";
				$this->call_command("DB_QUERY",Array($sql));
			}
			return $output;
	//		print $this->extract_data("<form name='asd' action='http://professor/' method=get><input type='hidden' name='freedy' value='asdfgew'/></form>");
	//		$this->exitprogram();
		}
	}
	
	function extract_data($str, $base_uri){
		$list_of_parts = split("/",$base_uri);
		$parts = split("<body",$str);
		$head = $this->get_scripts($parts[0]);
		$str= $parts[1];
		$pos = strpos($parts[1],">");
		if($pos){
			$posend = strpos($parts[1],"</body>");
			$str = substr($parts[1],$pos+1,$posend - $pos);
		}
		if ($list_of_parts[0]=="http:" || $list_of_parts[0]=="https:" || $list_of_parts[0]=="ftp:"){
			$dom 			= "http://".$list_of_parts[2];
		} else {
			$dom 			= "http://".$list_of_parts[0];
		}
		unset($list_of_parts[count($list_of_parts)-1]);
		$uri_dir		= join("/",$list_of_parts);
//		print "[$uri_dir]";
		$lcv 			= strtolower($str); // Lower Case Version
		$action 		= "";
		$form_structure = Array();
		$form_identifier= 0;
		$frm 			= strpos($lcv,"<form");
		$form_links		= Array();
		$form_images	= Array();
		if ($frm===false){
			// fix any links supplied
			$frm_data	= $str;
			$tags 		= preg_split ("/</", $frm_data);
			$m=count($tags);
			for($i=0;$i<$m;$i++){
				$closingtag = strpos($tags[$i],">",0);
				if(!($closingtag===false)){
					$tag_data = substr($tags[$i], 0, $closingtag);
					$cdata 	= strtolower(strtok($tag_data, " \n\t\"='")); 
					if ($cdata == "a"){
						$att= $cdata;
						while($att){
							$att = strtok(" \n\t\"='");
							$val = strtok("\n\t\"'");
							if (($att=="href") || ($att=="href=")){
								if (!in_array($val, $form_links)){
								$form_links[count($form_links)]=$val;
									if (substr($val,0,5)=="http:"){
									}else if(strpos($val,"[[script]]")===false){
										$str = str_replace(Array($val), Array("[[script]]?les_uri=".urlencode($val)), $str);
									} else {
									}
								}
							}
						}
					}
					if ($cdata == "img"){
						$att= $cdata;
						while($att){
							$att = strtok(" \n\t\"='");
							$val = strtok("\n\t\"'");
							if (($att=="src") || ($att=="src=")){
								if (!in_array($val, $form_images)){
									$form_images[count($form_images)]=$val;
									if(substr($val,0,1)=="/"){
//									print "<li style='background:#ffffff'>att = $val becomes $dom$val</li>";
										$str = str_replace(Array($val), Array($dom.$val), $str);
									} else if (substr($val,0,5)=="http:") {
									} else {
//									print "<li style='background:#ffffff'>att = $val becomes ".$uri_dir."/$val</li>";
										$str = str_replace(Array($val), Array($uri_dir."/".$val), $str);
									}
								}
							}
						}
					}
				}
			}
		} else {
			while (!($frm===false)){
				$endtag 	= strpos($lcv, ">", $frm);
				$tag		= substr($str, $frm, ($endtag-$frm)+1);
				/*
				****************************************************************************************
				* find from structure
				****************************************************************************************
				*/
				$cdata 		= strtok($tag," \n\t\"='"); 
				$action 	="";
				$method		="GET";
				$name 		="";
				$id 		="";
				while($cdata){
					if (strtolower($cdata)=="action"){$action = strtok(" \n\t\"='");}
					if (strtolower($cdata)=="method"){$method = strtok(" \n\t\"='");}
					if (strtolower($cdata)=="name"){$name = strtok(" \n\t\"='");}
					if (strtolower($cdata)=="id"){$id = strtok(" \n\t\"='");}
					$cdata = strtok(" \n\t\"='");
				}
				$endoffrm	= strpos($lcv,"</form>",$endtag)+7;
				$form_identifier = count($form_structure);
				$form_structure[$form_identifier] = Array(
					"form_tag"	=>Array(
						"start"		=> $frm,
						"end" 		=> $endtag,
						"action"	=> $action,
						"method"	=> $method,
						"name"		=> $name,
						"id"		=> $id,
						"endform"	=> $endoffrm
					),
					"fields" 	=> Array(),
					"links" 	=> Array()
				);
				// now parse the content tag at a time
				$frm_data	= "";
				if (!($endoffrm===false)){
					$frm_data	= substr($str, $endtag+1, ($endoffrm-$endtag)-1);
				}
				$tags 		= preg_split ("/</", $frm_data);
				$m = count($tags);
				for($i=0;$i<$m;$i++){
					$closingtag = strpos($tags[$i],">",0);
					if(!($closingtag===false)){
						$tag_data = substr($tags[$i], 0, $closingtag);
						$cdata 	= strtolower(strtok($tag_data, " \n\t\"='")); 
						if (($cdata=="input") || ($cdata=="select")){
							while($cdata){
								$cdata = strtok(" \n\t\"='");
								if ($cdata=="name"){
									$v = strtok(" \n\t\"='");
									if (!in_array($v, $form_structure[$form_identifier]["fields"])){
										$form_structure[$form_identifier]["fields"][count($form_structure[$form_identifier]["fields"])] = $v;
									}
								}
								$cdata = strtok(" \n\t\"='");
							}
						}
						if ($cdata == "a"){
							$att= $cdata;
							while($att){
								$att = strtok(" \n\t\"='");
								$val = strtok("\n\t\"'");
								if (($att=="href") || ($att=="href=")){
									if (!in_array($val, $form_structure[$form_identifier]["links"])){
										$form_structure[$form_identifier]["links"][count($form_structure[$form_identifier]["links"])] = $val;
										if (substr($val,0,5)=="http:"){
										}else if(strpos($val,"[[script]]")===false){
											$str = str_replace(Array($val), Array("[[script]]?les_uri=".urlencode($val)), $str);
										} else {
										}
									}
								}
							}
						}
					}
				}
				$frm = strpos($lcv,"<form",$endoffrm);
			}
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"uri",__LINE__,"".print_r($form_structure,true).""));}
			$newstr = "";
			$number_of_forms = count($form_structure);
	//		print "".print_r($form_structure,true)."";
			$currentindex=0;
			for($form_index=0;$form_index<$number_of_forms;$form_index++){
				$newstr .= substr($str, $currentindex, $form_structure[$form_index]["form_tag"]["start"] - $currentindex);
				$newstr .= "<form ";
				if($form_structure[$form_index]["form_tag"]["method"]!=""){
					$newstr .= "method='".$form_structure[$form_index]["form_tag"]["method"]."' ";
				}
				if($form_structure[$form_index]["form_tag"]["action"]!=""){
					$newstr .= "action='[[script]]' ";
				}
				if($form_structure[$form_index]["form_tag"]["name"]!=""){
					$newstr .= "name='".$form_structure[$form_index]["form_tag"]["name"]."' ";
				}
				if($form_structure[$form_index]["form_tag"]["id"]!=""){
					$newstr .= "id='".$form_structure[$form_index]["form_tag"]["id"]."' ";
				}
				$newstr .= ">\n";
				$field_count = count($form_structure[$form_index]["fields"]);
				for($field_index=0;$field_index<$field_count;$field_index++){
					$newstr .= "<input type='hidden' name='libertas_embeded_field[]' value='".$form_structure[$form_index]["fields"][$field_index]."'/>\n";
				}
				$newstr .= "<input type='hidden' name='libertas_destination_url' value='".$form_structure[$form_index]["form_tag"]["action"]."' />\n";
				$newstr .= substr($str, $form_structure[$form_index]["form_tag"]["end"] + 1, ($form_structure[$form_index]["form_tag"]["endform"]) - $form_structure[$form_index]["form_tag"]["end"] - 1);
				$currentindex = $form_structure[$form_index]["form_tag"]["endform"];
			}
			$newstr .= substr($str, $currentindex);
		}
		$str = $head . $str;
		$str = str_replace(
				Array(
//					'<form name="cart" method="post" action="checkout.php">',
//					'<a href="checkout.php"><img src="themes/libertas_embedded/images/next.gif" border="0"></a>',
					'<img src="themes/test/images/warning.gif" align="absmiddle">',
					'<br><center><font size="-2" face="Verdana, Arial, Helvetica, sans-serif"><a href="checkout.php">Return to checkout and modify order</a></font></center></p>',
					'€',
					'href="/',
					'[[script]]?les_uri=[[script]]',
					'[[script]]?les_uri=http%3A%2F%2F'
				), Array(
//					"<form name='cart' method='post' action='[[script]]'><input type='hidden' name='es_rank' value='6'>",
//					'<a href="[[script]]?command=EMBEDSCRIPT_DISPLAY&es_rank=5" class="bt" style="padding:0 5px 0 5px">Next</a>',
					'[[nbsp]]',
					'<a href="[[script]]?les_uri=checkout.php" class="bt" style="padding:0 5px 0 5px">Return to checkout and modify order</a>',
					'[[euro]]',
					'href="[[script]]?les_uri='.$dom.'/',
					'[[script]]',
					'http://'
				), $str);
//		print "\n\n\n\n\n\n".$str;
//		$this->exitprogram();
		return $str;
	}


	function clean($str, $clean){
		$str = html_entity_decode($str);
		$strip_array	= Array();
		$e				= strpos($str,"]]></text></module>");
		$s				= strpos($str,"<module name='embedscript'><text clear='1'><![CDATA[")+strlen("<module name='embedscript'><text clear='1'><![CDATA[");
		$data			= substr($str, $s,$e- $s);
		$tags			= split("<", $data);
		$m				= count($tags);
		for($i=0;$i<$m;$i++){
			$closingtag = strpos($tags[$i],">",0);
			if(!($closingtag===false)){
				$d = split('>', $tags[$i]);
				$tag_data = $d[0];// substr($tags[$i], 0, $closingtag);
				$cdata 	= strtolower(strtok($tag_data, " \n\t\"='")); 
				if (($cdata=="font") || ($cdata=="/font")){
					//print "<li>$cdata</li>";
					if(!in_array("<".$tag_data.">", $strip_array)){
						$str = str_replace(Array("<".$tag_data.">"), Array(""), $str);
						//$strip_array[count($strip_array)] = "<".$tag_data.">";
					}
				}
			}
		}
		$e				= strpos($str,"]]></text></module>");
		$s				= strpos($str,"<module name='embedscript'><text clear='1'><![CDATA[")+strlen("<module name='embedscript'><text clear='1'><![CDATA[");
		$data			= substr($str, $s,$e- $s);
		$tags			= split("<", $data);
		$m				= count($tags);
		for($i=0;$i<$m;$i++){
			$closingtag = strpos($tags[$i],">",0);
			if(!($closingtag===false)){
				$d = split('>', $tags[$i]);
				$tag_data = $d[0];// substr($tags[$i], 0, $closingtag);
				$cdata 	= strtolower(strtok($tag_data, " \n\t\"='")); 
				while($cdata){
					$cdata 	= strtolower(strtok(" \n\t\"='")); 
					if($cdata=="class" && $clean == 3){
						//print "<li>$cdata</li>";
						$s = strpos($tags[$i],"class");
						if(!($s === false)){
							$val = strtok(" \n\t\"='");
							$e = strpos($tags[$i], $val)+strlen($val)+1;
							if(!in_array(substr($tags[$i], $s, $e - $s), $strip_array)){
								$strip_array[count($strip_array)] = substr($tags[$i], $s, $e - $s);
							}
						}
					}
					if($cdata=="style" && $clean >= 2){
						//print "<li>$cdata</li>";
						$s = strpos($tags[$i],"style");
						if(!($s === false)){
							$val = strtok(" \n\t\"='");
							$e = strpos($tags[$i], $val)+strlen($val)+1;
							if(!in_array(substr($tags[$i], $s, $e - $s), $strip_array)){
								$strip_array[count($strip_array)] = substr($tags[$i], $s, $e - $s);
							}
						}
					}
				}
			}
		}
		$max=count($strip_array);
		$replace_list = Array();
		for($index=0;$index<$max;$index++){
			$replace_list[count($replace_list)] = "";
		}
		$output= str_replace($strip_array, $replace_list, $str);
		for($index=0;$index<$max;$index++){
			$strip_array[$index] = $strip_array[$index];
		}
		$output= str_replace($strip_array, $replace_list, $str);
		return $output;
	}
	
	function get_scripts($str){
		$lcv 			= strtolower($str); // Lower Case Version
		$scripts		= Array();
		$script_position= strpos($lcv,"<script");
		if($script_position===false){
			return "";
		} else {
			while($script_position){
//				$prev_script_position=$script_position; // store start
				$autoclosed 	= strpos($lcv,"/>",$script_position);
				$manualclosed 	= strpos($lcv,">",$script_position);
				if ($autoclosed+1==$manualclosed){
					 // auto closed
					 $scripts[count($scripts)] = substr($str, $script_position, $manualclosed-$script_position); 
					 $endpos = $manualclosed+1;
				} else {
					$closescripttag = strpos($lcv,"</script>",$script_position);
					if ($closescripttag===false){
					} else {
						$scripts[count($scripts)] = substr($str, $script_position, ($closescripttag + 9)-$script_position); 
					}
					 $endpos = $closescripttag + 9;
				}
				$script_position= strpos($lcv,"<script", $endpos);
			}
		
		}
		if(count($scripts)>0){
			return join("",$scripts);
		} else {
			return "";
		}
	}
}

?>
