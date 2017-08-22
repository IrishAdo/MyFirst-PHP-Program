<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.accesskey_admin.php
* @date 03 April 2004
*/
/**
* this module will allow the administration of the accesskey list for 
* this website
*/
class accesskeyadmin extends module{
	/**
	*  Class Variables
	*/
	var $module_name			= "accesskeyadmin";
	var $module_name_label		= "Access Key Manager Module (Administration)";
	var $module_label			= "MANAGEMENT_ACCESSKEYS";
	var $module_grouping		= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_admin			= "1";
	var $module_debug			= false;
	var $module_creation		= "03/04/2004";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:48 $';
	var $module_version 			= '$Revision: 1.17 $';
	var $module_command			= "ACCESSKEYADMIN_"; 		// all commands specifically for this module will start with this token
	var $has_module_contact		= 0;
	var $has_module_group		= 0;
	var $display_options		= null;
		
	var $module_display_options = array(
	);
	
	var $defined_list = Array(
			Array("0", "saAccesskey details"							,1,"-access-key-defintion.php",""),
			Array("1", "Home page"									,0,"index.php","Home"),
			Array("2", "What's new"									,1,"-whats-new.php","What's New"),
			Array("3", "Site map"									,0,"-site-map.php","Site Map"),
			Array("4", "Search this site"							,0,"-search.php","Search this site"),
			Array("5", "Frequently Asked Questions (FAQ)"			,1,"",""),
			Array("6", "Help"										,1,"",""),
			Array("7", "Complaints procedure"						,1,"",""),
			Array("8", "Terms and conditions"						,1,"",""),
			Array("9", "Feedback form"								,1,"-/-feedback-form.php","Feedback Form"),
			Array("m", "Toggle between graphical and text only mode",0,"",""),
			Array("p", "Print Page"									,0,"",""),
			Array("s", "Skip navigation"							,0,"",""),
			Array("=", "Increase font size"							,0,"",""),
			Array("-", "Reduce font size" 							,0,"","")
		);
	/**
	* SPECIAL PAGES
	*
	* Each special page will call a specific function as defined here
	*/
	var $specialPages			 	= array(
		array("-access-key-defintion.php", "ACCESSKEY_DISPLAY"	,"VISIBLE",	"Site access keys defintion")
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
			if ($user_command==$this->module_command."SPECIAL_PAGES"){
				return $this->specialPages;
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
			if ($this->module_admin_access){
				if ($user_command==$this->module_command."SAVE"){
					$group_list = $this->module_save($parameter_list);
					$this->call_command("LAYOUT_CACHE_MENU_STRUCTURE");
					$this->call_command("ENGINE_REFRESH_BUFFER", Array("confirm=LOCALE_ACCESSKEYS"));
				}
				if ($user_command==$this->module_command."EDIT"){
					return $this->module_form($parameter_list);
				}
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
			if ($user_command==$this->module_command."CACHE"){
				return $this->cache($parameter_list);
			}
			if ($user_command==$this->module_command."GET_LIST"){
				return $this->get_list();
			}
		}else{
			return "";// wrong command sent to system
		}
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
		* Table structure for table 'accesskey_data'
		*/
		$fields = array(
			array("accesskey_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("accesskey_client"				,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("accesskey_label"					,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("accesskey_key"					,"varchar(1)"				,""			,"default ''"),
			array("accesskey_title"					,"varchar(255)"				,""			,"default ''"),
			array("accesskey_type"					,"unsigned small integer"	,""			,"default ''")
		);
		$primary ="accesskey_identifier";
		$tables[count($tables)] = array("accesskey_data", $fields, $primary);
		return $tables;
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
		$this->load_locale("accesskey_admin");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		
		/**
		* define the filtering information that is available
		*/
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$this->module_admin_options 		= array(
				array("ACCESSKEYADMIN_EDIT","MANAGEMENT_ACCESSKEYS")
			);
		}

		$this->module_admin_user_access	= array(
			array($this->module_command."ALL","COMPLETE_ACCESS")
		);
		
		$this->module_admin_access		= 0;
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (($this->module_command."ALL" == $access[$index]) || ("ALL"==$access[$index]) || ($this->module_command==substr($access[$index],0,strlen($this->module_command)))){
					$this->module_admin_access=1;
				}
			}
		}

	}
	
	/**
	* module_form function
	-----------------------
	- This function will generate the proper table structure in the choosen database
	- format.
	*/
	function module_form($parameters){
		$debug = $this->debugit(false,$parameters);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_form",__LINE__,"[]"));
		}
		$display_tab = $this->check_parameters($parameters,"display_tab","");
		$url_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "akey1",
				"field_as"			=> "accesskey_url",
				"identifier_field"	=> "accesskey_data.accesskey_identifier",
				"module_command"	=> $this->module_command,
				"client_field"		=> "accesskey_client",
				"mi_field"			=> "accesskey_url"
			)
		);

		$sql = "select *, ".$url_parts["return_field"]." from accesskey_data ".$url_parts["join"]." where accesskey_client = $this->client_identifier ".$url_parts["where"]." order by accesskey_key ";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		// find the length of the defined list as we will check these only additional one can be ignored.
		$max = count($this->defined_list);
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$found=0;
        	for ($index=0; $index<$max;$index++){
				if ($this->defined_list[$index][0] == $r["accesskey_key"]){
					if (strpos($r["accesskey_url"],"[[script]]")===false){
						$this->defined_list[$index][3] = $r["accesskey_url"];
						$this->defined_list[$index][4] = $r["accesskey_title"];
					} else {
						$this->defined_list[$index][3] = "";
						$this->defined_list[$index][4] = "";
					}
					$found=1;
				}
			}
			if ($found==0){
				// add on to the end of the defined_list
				$this->defined_list[count($this->defined_list)] = Array($r["accesskey_key"],$r["accesskey_label"],2,$r["accesskey_url"],$r["accesskey_title"]);
			}
        }
        $this->call_command("DB_FREE",Array($result));
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options><header>".LOCALE_ACCESSKEY_MANAGER."</header></page_options>";
		$out .= "<form name=\"AccessKeys\" label=\"".LOCALE_ACCESSKEY_TITLE_LABEL."\" method=\"post\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"ACCESSKEYADMIN_SAVE\"/>
				<input type=\"hidden\" name=\"numberOfAccessKeys\" value=\"\"/>
				<page_sections>
					<section label=\"".LOCALE_SETTINGS."\"";
		if ($display_tab=="content"){
			$out .= " selected='true'";
		}
		$out .= ">";
		$out .= "<access_list>";
		$max = count($this->defined_list);
		for ($i=0; $i<$max; $i++){
			$out .= "<accesskey letter='" . $this->defined_list[$i][0] . "' type='".$this->defined_list[$i][2] . "'>\n";
			$out .= "	<label><![CDATA[".$this->defined_list[$i][1] . "]]></label>\n";
			$out .= "	<title><![CDATA[".$this->check_parameters($this->defined_list[$i],4) . "]]></title>\n";
			$out .= "	<url><![CDATA[".$this->check_parameters($this->defined_list[$i],3) . "]]></url>\n";
			$out .= "</accesskey>\n";
		}
		$out .= "</access_list>";
		$out .= "</section></page_sections>";
		
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>
			</form>";
		$out .="</module>";
		return $out;
	}
	
	/**
	* module_save function
	-----------------------
	- This function will generate the proper table structure in the choosen database
	- format.
	*/
	function module_save($parameters){
		$debug = $this->debugit(false,$parameters);
//		print "<!-- ";
//		print_r($parameters);
//		print " -->";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"polls_save",__LINE__, print_r($parameters,true)));
		}
		$link = "";
		$list_of_results = Array("libertas_links_menu" => Array(), "libertas_links_page" => Array());
		$ok=0;
		$numberOfAccessKeys				=	$this->check_parameters($parameters,"numberOfAccessKeys");
		$listOfkeys						=	$this->check_parameters($parameters,"listOfkeys");
		$identifier						=	$this->check_parameters($parameters,"identifier");
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		$sql = "delete from accesskey_data where accesskey_client=$this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$this->call_command("MEMOINFO_DELETE_ALL_CLIENT_MODULE",array("mi_type"=>$this->module_command));
		$this->call_command("EMBED_DELETE_ALL_CLIENT_MODULE",array("mi_type"=>$this->module_command));
			
		$find = Array(
				"http://".$this->parent->domain."/libertas_images",
				"http://".$this->parent->domain.$this->parent->base,
				"http://".$this->parent->domain.$this->parent->base."admin/",
				"http://".$this->parent->domain."/",
				"https://".$this->parent->domain."/",
				"https://".$this->parent->domain.$this->parent->base,
				"https://".$this->parent->domain.$this->parent->base."admin/"
		);
		$replace = Array(
			"/libertas_images",
			"",
			"admin/",
			"",
			"",
			"",
			"admin/"
		);
		$fc = count($find);
		$rc = count($replace);
		if ($fc!=$rc){
			print "Error code #LS000006 - $fc find attributes versus $rc replace attributes in function validate ".__FILE__." line ".__LINE__."<br>";
		}
//		$new_str = html_entity_decode(html_entity_decode($str)));
//		print "<!-- $listOfkeys -->";
		$keyList = split(", ",$listOfkeys);
		
		$textonly_version="<h1>Access key definitions</h1>";
		for ($index =0; $index< $numberOfAccessKeys ; $index ++){
			if($this->check_parameters($keyList,$index)!=""){
				$accesskey_key		= htmlentities(strip_tags($this->validate(str_replace($find, $replace, $this->check_parameters($parameters,"letter_".$keyList[$index])))));
				$accesskey_title	= htmlentities(strip_tags($this->validate(str_replace($find, $replace, $this->check_parameters($parameters,"title_".$keyList[$index])))));
				$accesskey_label	= htmlentities(strip_tags($this->validate(str_replace($find, $replace, $this->check_parameters($parameters,"label_".$keyList[$index])))));
				$accesskey_url		= htmlentities(strip_tags($this->validate(str_replace($find, $replace, $this->check_parameters($parameters,"url_".$keyList[$index])))));
				$accesskey_type		= $this->check_parameters($parameters,"type_".$keyList[$index]);
				if ($accesskey_title==""){
					$m = count($this->defined_list);
					for($i=0;$i<$m ;$i++){
						if($this->defined_list[$i][0]==$accesskey_key){
							$accesskey_title = $this->defined_list[$i][1];
						}
					}
				}
					if ($accesskey_type==0){
						if ($accesskey_key=='1'){
							$accesskey_url="index.php";
						} else {
							$cmd ="";
							if ($accesskey_key=='m'){
								$accesskey_url = "-/-toggle-text-only-mode.php";
							}
							if ($accesskey_key=='0' && strlen($accesskey_url)==0){
								$accesskey_url = "-access-key-defintion.php";
							}
							if ($accesskey_key=='2'){
								$accesskey_url ="-whats-new.php";
							}
							if ($accesskey_key=='p'){
								$accesskey_url ="-/-toggle-printer-friendly-mode.php";
							}
							if ($accesskey_key=='3'){
								$accesskey_url ="-site-map.php";
							}
							if ($accesskey_key=='4'){
								$accesskey_url ="-search.php";
							}
							if ($accesskey_key=='9'){
								$accesskey_url ="-/-feedback-form.php";
							}
							if($cmd!=""){
								$list = $this->call_command("LAYOUT_GET_MENU_WITH_COMMAND",Array("cmd"=>$cmd));
								if (count($list)>0){
									$accesskey_url = $list[0];
								} else {
									$accesskey_url = "index.php?command=$cmd";
								}
							}
						}
					} else {
						if ($accesskey_key=='0' && strlen($accesskey_url)==0){
							$cmd ="ACCESSKEY_DISPLAY";
							$list = $this->call_command("LAYOUT_GET_MENU_WITH_COMMAND",Array("cmd"=>$cmd));
							if (count($list)>0){
								$accesskey_url = $list[0];
							} else {
								$accesskey_url = "-access-key-defintion.php";
							}
						}
						if ($accesskey_key=='2' && strlen($accesskey_url)==0){
							$cmd ="PRESENTATION_LATEST";
							$list = $this->call_command("LAYOUT_GET_MENU_WITH_COMMAND",Array("cmd"=>$cmd));
							if (count($list)>0){
								$accesskey_url = $list[0];
							} else {
								$accesskey_url ="-whats-new.php";
							}
						}
					}
					if ($accesskey_key!='' && strlen($accesskey_url)>0){
						if ($accesskey_key=='p'){
								$link .= "<li><a accesskey='p' title='Print page [p]' href='-/-toggle-printer-friendly-mode.php' ><span class=\"icon\"><span class=\"text\">Print page [p]</span></span></a></li>";
						} else {
							if ($accesskey_key!='s' && $accesskey_key!='m' && $accesskey_key!='-' && $accesskey_key!='='){
								$link .= "<li><a accesskey=\"".$accesskey_key."\" title=\"".str_replace("#39","#32",$accesskey_title)." [".$accesskey_key."]\" href=\"".$accesskey_url."\" ><span class=\"icon\"><span class=\"text\">".$accesskey_title." [".$accesskey_key."]</span></span></a></li>";
							}
							if ($accesskey_key!='s' && $accesskey_key!='m' && $accesskey_key!='-' && $accesskey_key!='='){
								$textonly_version .= "$accesskey_key :: <a accesskey=\"".$accesskey_key."\" href=\"".$accesskey_url."\" title=\"".$accesskey_title." [".$accesskey_key."]\">".$accesskey_title."</a><br/>";
							}
						}
						if ($accesskey_key=='m'){
							$qstr =$this->check_parameters($_SERVER,"QUERY_STRING");
							if (strpos($accesskey_url,"?")===false){
								$textonly_version .= "$accesskey_key :: <a accesskey=\"".$accesskey_key."\" href=\"".$accesskey_url."?$qstr\" title=\"".$accesskey_title." [".$accesskey_key."]\">".$accesskey_title."</a><br/>";
							} else {
								$textonly_version .= "$accesskey_key :: <a accesskey=\"".$accesskey_key."\" href=\"".$accesskey_url."&amp;$qstr\" title=\"".$accesskey_title." [".$accesskey_key."]\">".$accesskey_title."</a><br/>";
							}
							$qstr ="";
						}
						$extra = "";
						$sql = "insert into accesskey_data (accesskey_key, accesskey_label, accesskey_title, accesskey_type, accesskey_client) values ('$accesskey_key', '$accesskey_label', '$accesskey_title', '$accesskey_type', $this->client_identifier)";
						$this->call_command("DB_QUERY",Array($sql));
						$sql = "select accesskey_identifier from accesskey_data where accesskey_key='$accesskey_key' and 
								accesskey_label='$accesskey_label' and 
								accesskey_title='$accesskey_title' and 
								accesskey_type='$accesskey_type' and 
								accesskey_client=$this->client_identifier";
						$result  = $this->call_command("DB_QUERY",Array($sql));
		                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	    	            	$accesskey_identifier = $r["accesskey_identifier"];
	        	        }
	            	    $this->call_command("DB_FREE",Array($result));
						$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->module_command,"mi_memo"=>$accesskey_url,	"mi_link_id" => $accesskey_identifier, "mi_field" => "accesskey_url"));
						$list_of_embedded_information	= $this->call_command("EMBED_EXTRACT_INFO",Array("str" => 'waste of text <a href="'.$accesskey_url.'">link</a> more waste'));
						$this->call_command("EMBED_SAVE_INFO",Array("list_of_results" => $list_of_embedded_information, "id"=>$accesskey_identifier, "editor"=>"accesskey_url",		"module"=>$this->module_command, "previous_title"=>""));
					}
			}
		}
		$link.="";
		$filestr = "<setting name='accesskeys'><![CDATA[$link]]></setting>";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fp = fopen($data_files."/accesskeys_".$this->client_identifier.".xml", 'w');
		fwrite($fp, $filestr);
		fclose($fp);
		$um = umask(0);
		@chmod($data_files."/accesskeys_".$this->client_identifier.".xml", LS__FILE_PERMISSION);
		umask($um);
		$filestr = "<setting name='accesskeys'><![CDATA[".LOCALE_ACCESSKEY_DEFINTION."$textonly_version".LOCALE_ACCESSKEY_DEFINTION_BROWSERS."]]></setting>";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fp = fopen($data_files."/accesskeys_".$this->client_identifier."_visible.xml", 'w');
		fwrite($fp, $filestr);
		fclose($fp);
		$um = umask(0);
		@chmod($data_files."/accesskeys_".$this->client_identifier."_visible.xml", LS__FILE_PERMISSION);
		umask($um);


		return "";
	}

	function cache($parameters){
		$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ak1",
				"field_as"			=> "accesskey_url",
				"identifier_field"	=> "accesskey_data.accesskey_identifier",
				"module_command"	=> $this->module_command,
				"client_field"		=> "accesskey_client",
				"mi_field"			=> "accesskey_url"
			)
		);

		$sql = "select ".$body_parts["return_field"].", accesskey_data.* from accesskey_data 
						".$body_parts["join"]."
					where 
						 accesskey_client = $this->client_identifier ".$body_parts["where"]."";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$link="";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$accesskey_key		=	$r["accesskey_key"];
			$accesskey_title	=	$r["accesskey_title"];
			$accesskey_url		= 	$r["accesskey_url"];
        	if ($accesskey_key	!=	's'){
				$link .= "<li><a accesskey=\"".$accesskey_key."\" title=\"".str_replace(Array("'"),Array(""),$accesskey_title)."\" href=\"".$accesskey_url."\" ><span class=\"icon\"><span class=\"text\">".$accesskey_title."</span></span></a></li>";
			}
        }
		
		$link .= "";
		$filestr = "<setting name='accesskeys'><![CDATA[$link]]></setting>";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$fp = fopen($data_files."/accesskeys_".$this->client_identifier.".xml", 'w');
		fwrite($fp, $filestr);
		fclose($fp);
		$um = umask(0);
		@chmod($data_files."/accesskeys_".$this->client_identifier.".xml", LS__FILE_PERMISSION);
		umask($um);

        $this->call_command("DB_FREE",Array($result));
	}
	
	function get_list(){
		$url_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "akey1",
				"field_as"			=> "accesskey_url",
				"identifier_field"	=> "accesskey_data.accesskey_identifier",
				"module_command"	=> $this->module_command,
				"client_field"		=> "accesskey_client",
				"mi_field"			=> "accesskey_url"
			)
		);
		$sql = "select *, ".$url_parts["return_field"]." from accesskey_data ".$url_parts["join"]." where accesskey_client = $this->client_identifier ".$url_parts["where"]." order by accesskey_key ";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		// find the length of the defined list as we will check these only additional one can be ignored.
		$max = count($this->defined_list);
		$list = Array();
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$found=0;
        	for ($index=0; $index<$max;$index++){
				$list[$r["accesskey_key"]] = $r["accesskey_url"];
			}
        }
		return $list;
	}
}
?>