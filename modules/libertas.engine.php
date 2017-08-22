<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.engine.php
* Date :: 09 Oct 2002
*/
/**
* The engine module will be the module that will control and call all
* other modules.  You should examine the libertas-Solutions Document Warehouse about
* how modules can be created so that this module will knwo that it exists and will
* be able to use your modules
*
*/
define ("DATE_EXPIRES",	md5("EXPIRES"));
define ("IP_ADDRESS",	md5("IP"));
define ("LICENCE_TYPE",	md5("SETUP"));
define ("UNLIMITED",	md5("-1"));
define ("ECMS",			md5("ENTERPRISE"));
define ("MECM",			md5("LITE"));
define ("SITE_WIZARD",	md5("ULTRA"));

include_once $config_directory . "/settings.php";
include_once $module_directory . "/libertas.module.php";

class engine extends module{
	/**
	*  Class Variables
	*/
	var $DEV_SERVER			= "dev";
	var $session_parameter	= "";
	var $module_name		= "engine";
	var $module_name_label	= "Libertas Solutions Core Engine";
	var $module_creation	= "09/09/2002";
	var $module_modify	 	= '$Date: 2005/03/15 14:58:34 $';
	var $module_version 	= '$Revision: 1.82 $';
	var $module_debug 		= 0;
	var $product_name 		= "";
	var $module_command		= "ENGINE_"; 		// all commands specifically for this module will start with this token
	var $modules			= Array();		 				// A list of all the modules in the system.
	var $domain				= "";						// Domain Name that this system will use
	var $connection			= null;
	var $status				= 0;
	var $host				= "";
	var $pwd				= "";
	var $debug_access		= "";
	var $database			= "";
	var $user				= "";
	var $logs				= "";
	var $print_first 		= "";
	var $system_prefs		= Array();
	var $config_updated		= 0;
	var $script				= "";
	var $module_type		= "website"; // default type if none defined in page
	var $site_directories	= Array();
	var $page_output 		= "";
	var $page_clear			= 0;
	var $page_parameters	= "";
	var $menu_structure		= Array();
	var $server				= Array();
	var $base				= "/";
	var $real_script		= "";
	var $p_script			= ""; //tmp storage of script path to be used only in preview mode
	var $choosen_theme		= 0;
	var $previous_theme		= 0;
	var $is_bot				= 0;
	var $theme_stylesheet	= "";
	var $updated_date		= 0;
	var $theme_type_label	= ""; // XSL display option for this menu location
	var $qstr				= "";
	var $show_base_href		= 0; //
	var $sp_ssl_available	= "No";
	var $db_pointer			= null;
	var $real_choosen_theme = -1;
	/**
	* WebObject entries
	* Each Array has (Type, Label, Command, All locations, Has label)
	* 
	* Type: - 0 = User defined, 1 = Channel type Web object, 2 = XSL defined WEbObject)
	* 
	* Channels extract information from the system wile XSL defined are functions in the 
	* XSL display. 
	*/
	var $WebObjects				 	= array(
		array(2,"Display the Date (Site Updated)",	"WEBOBJECTS_SHOW_SITE_UPDATED_DATE",	0, 0, ""),
		array(2,"Display the Date (Todays)",		"WEBOBJECTS_SHOW_DATE", 				0, 0, "")
	);
	/**
	* SPECIAL PAGES
	* Each special page will call a specific function as defined here
	*/
	var $specialPages			 	= array(
		array("index.php"							,"ENGINE_CLOSED_DIRECTORY"					,"HIDDEN",	""),
		array("-reduce-font.php"					,"ENGINE_FONT_SMALLER"						,"HIDDEN",	""),
		array("-font-size-normal.php"				,"ENGINE_FONT_NORMAL"						,"HIDDEN",	""),
		array("-increase-font.php"					,"ENGINE_FONT_LARGER"						,"HIDDEN",	""),
		array("-toggle-printer-friendly-mode.php"	,"ENGINE_PRINTER_FRIENDLY_VERSION"			,"HIDDEN",	""),
		array("-toggle-text-only-mode.php"			,"ENGINE_TEXT_ONLY_VERSION"					,"HIDDEN",	""),
		array("-normal-mode.php"					,"ENGINE_NORMAL_VERSION"					,"HIDDEN",	""),
		array("-anti-spam.php"						,"ENGINE_ANTI_SPAM_MAIL"					,"HIDDEN",	"Anti Spam"),
		array("-anti-spam-confirm.php"				,"ENGINE_ANTI_SPAM_MAIL_CONFIRM"			,"HIDDEN",	"Anti Spam Confirm"),
		array("-feedback-form.php"					,"ENGINE_CONTACT_US_DEFAULT_PAGE"			,"HIDDEN",	"Feedback Form"),
		array("-feedback-form-confirm.php"			,"ENGINE_CONTACT_US_DEFAULT_PAGE_CONFIRM"	,"HIDDEN",	"Feedback Form"),
		array("-bookmark-page.php"					,"ENGINE_BOOKMARK_PAGE"						,"HIDDEN",	"myBookmarks"),
		array("-404.php"							,"ENGINE_404_PAGE"							,"HIDDEN",	"404 Error Page"),
		array("-file-download.php"					,"FILES_INFO"								,"HIDDEN",	""),
		array("-switch-css.php"						,"THEME_SESSION_SET"						,"HIDDEN",	"")
	);
	/**
	* Modules required by this Class these modules are not loaded by the system
	*/
	
	/**
	*  Class Construtor
	*/
	function engine($domain,$script,$type){
		$this->domain=$domain;
		if ($domain==$this->DEV_SERVER){
			$this->debug_access="enable_debug_options";
		}
		$this->module_type = $type;
		$this->parent = &$this;
		if(basename($script)==""){
			$script.="index.php";
		}
		if (strpos($_SERVER["PHP_SELF"],"~")===0){
			$this->base			= "/";
			$this->script		= substr($script,strlen($this->base));
			$this->real_script	= substr($_SERVER["PHP_SELF"],strlen($this->base));
		}else{
			$start= strpos($_SERVER["PHP_SELF"], "~");
			$end  = strpos($_SERVER["PHP_SELF"], "/",$start);
			$this->base = substr($_SERVER["PHP_SELF"], 0,$end+1);
			if ((strpos($script,"~")-1)==-1){
				if (substr($script,0,1)=="/"){
					$this->script = substr($script,1);
				}else{
					$this->script = $script;
				}
			}else{
				$this->script = substr($_SERVER["PHP_SELF"], strlen($this->base));
			}
			if ((strpos($_SERVER["PHP_SELF"],"~")-1)==-1){
				if (substr($_SERVER["PHP_SELF"],0,1)=="/"){
					$this->real_script = substr($_SERVER["PHP_SELF"],1);
				}else{
					$this->real_script = $_SERVER["PHP_SELF"];
				}
			}else{
				$this->real_script = substr($_SERVER["PHP_SELF"],strlen($this->base));
			}
		}
		$this->status = $this->engine_initalise();
		if ($this->status){
			if ($this->server[DATE_EXPIRES]!=UNLIMITED){
				$v = substr($this->server[DATE_EXPIRES],0,10);
				if ($v < strtotime($this->libertasGetDate("Y/m/d H:i:s"))){
				 return -1;
				}
			}
			//if ($_SERVER['REMOTE_ADDR'] == "202.154.241.147")		
			//print "$this->host,$this->user,$this->pwd,$this->database";
			$this->connection = $this->call_command("DB_CONNECT",array($this->host,$this->user,$this->pwd));
			if ($this->connection){
				$db_found = $this->call_command("DB_SELECT",array($this->database));
				if ($db_found){
					$this->client_identifier=$this->call_command("CLIENT_GET_IDENTIFIER");
					if ($this->client_identifier==-1){
						$this->status=-5;
					}
				} else{
					$this->status=-4;
				}
			}else{
				$this->call_command("ENGINE_ERROR",array(__FILE__,__LINE__,"The Engine could not connect to the database server [$this->host] with user [$this->user]"));
				$this->status=-3;
			}
		}
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$this->product_name = "Libertas ECMS";
		}else if ($this->parent->server[LICENCE_TYPE]==MECM){
			$this->product_name = "Libertas Content Manager";
		}else{
			$this->product_name = "Libertas Site Wizard";
		}
//		$this->product_name = "Libertas Solutions";
//		print "<li>".__FILE__."@".__LINE__."<p>$this->status</p></li>";
//		$this->exitprogram();
		return $this->status;
	}
	
	function start(){
		$test = $this->check_parameters($_SESSION, "client", -1);
		if ($test!=-1){
			if ($test!=$this->client_identifier){
				$this->call_command("SESSION_DESTROY");
			}
		}
		$this->session_parameter = session_name() ."=". session_id();
	}
	
	
	/**
	* command()
	* want to do anything with this module go through me simply create a condition for
	* the user command that you want to execute and hey presto I'll return the output of
	* that module
	*/
	function command($user_command,$parameter_list=array()){
		$this->debug_access==$this->check_parameters($parameter_list,"debug_system");
		if ($this->status==1){
		//print "<li>".__FILE__."@".__LINE__."<p>$user_command : ".print_R($parameter_list,true)."</p></li>";
			/**
			* This is the main function of the Module this function will call what ever function
			* you want to call.
			*/
			//print "<li>$user_command</li>";
			if (strpos($user_command,$this->module_command)===0){
				if ($user_command=="ENGINE_GET_SESSION"){
					return $this->show_session();
				}
				if ($user_command=="ENGINE_FONT_SMALLER"){
					$_SESSION["fontsize"]  = $this->check_parameters($_SESSION,"fontsize");
					if ($_SESSION["fontsize"]=="smaller"){
						$_SESSION["fontsize"]="smallest";
					}
					if ($_SESSION["fontsize"]==""){
						$_SESSION["fontsize"]="smaller";
					}
					if ($_SESSION["fontsize"]=="larger"){
						$_SESSION["fontsize"]="";
					}
					if ($_SESSION["fontsize"]=="largest"){
						$_SESSION["fontsize"]="larger";
					}
					$this->refresh_referal();
				}
				if ($user_command=="ENGINE_FONT_NORMAL"){
					$_SESSION["fontsize"]="";
					$this->refresh_referal();
				}
				if ($user_command=="ENGINE_FONT_LARGER"){
					$_SESSION["fontsize"]  = $this->check_parameters($_SESSION,"fontsize");
					if ($_SESSION["fontsize"]=="larger"){
						$_SESSION["fontsize"]="largest";
					}
					if ($_SESSION["fontsize"]==""){
						$_SESSION["fontsize"]="larger";
					}
					if ($_SESSION["fontsize"]=="smaller"){
						$_SESSION["fontsize"]="";
					}
					if ($_SESSION["fontsize"]=="smallest"){
						$_SESSION["fontsize"]="smaller";
					}
					$this->refresh_referal();
				}
				if ($user_command=="ENGINE_PRINTER_FRIENDLY_VERSION"){
					if ($this->check_parameters($_SESSION,"displaymode")!="printerfriendly"){
						$_SESSION["displaymode"] = "printerfriendly";
					} else {
						$_SESSION["displaymode"] = "";
					}
					$this->refresh_referal();
				}
				if ($user_command=="ENGINE_TEXT_ONLY_VERSION"){
					if ($this->check_parameters($_SESSION,"displaymode")!="textonly"){
						$_SESSION["displaymode"]="textonly";
					} else {
						$_SESSION["displaymode"] = "";
					}
					
					$this->refresh_referal();
				}
				if ($user_command=="ENGINE_PDA_VERSION"){
					$_SESSION["displaymode"]="pda";
					$this->refresh_referal();
				}
				if ($user_command=="ENGINE_NORMAL_VERSION"){
					$_SESSION["displaymode"]="";
					$this->refresh_referal();
				}
				if($user_command=="ENGINE_TOGGLE_ADMIN"){
					if($_SESSION["SESSION_USER_NAME"]=='l1b_adm1n_us3r'){
						$this->toggle_admin_access($parameter_list);
					}
				}
				if ($user_command=="ENGINE_REFRESH_SPECIAL"){
					$this->restore_special_pages($parameter_list);
				}
				if ($user_command=="ENGINE_404_PAGE"){
					return $this->error_404_page($parameter_list);
				}
				if ($user_command=="ENGINE_CONTACT_US_DEFAULT_PAGE"){
					return $this->engine_default_contact_us($parameter_list);
				}
				if ($user_command=="ENGINE_CONTACT_US_DEFAULT_PAGE_CONFIRM"){
					return $this->engine_default_contact_us_confirm($parameter_list);
				}
				if ($user_command=="ENGINE_ANTI_SPAM_MAIL"){
					return $this->antispam($parameter_list);
				}
				if ($user_command=="ENGINE_ANTI_SPAM_MAIL_CONFIRM"){
					return $this->antispam_confirm($parameter_list);
				}
				if ($user_command=="ENGINE_CLOSED_DIRECTORY"){
					$this->refresh(Array("url"=>$this->base."index.php"));
				}

				if ($user_command=="ENGINE_INIT"){
					return $this->engine_initalise();
				}
				if ($user_command=="ENGINE_HELP"){
					return "help";
				}
				if ($user_command=="ENGINE_GET_MODULE"){
					return $this->get_module_name();
				}
				if ($user_command=="ENGINE_GET_VERSION"){
					return $this->get_module_version();
				}
				if ($user_command=="ENGINE_DEBUG_ON"){
					//$this->module_debug=true;
				}
				if ($user_command=="ENGINE_DEBUG_OFF"){
					//$this->module_debug=false;
				}
				if ($user_command=="ENGINE_LIST"){
					return $this->engine_module_list();
				}
				if ($user_command=="ENGINE_CLOSE"){
					return $this->engine_close();
				}
				if ($user_command=="ENGINE_ERROR"){
					return $this->error_message($parameter_list[0],$parameter_list[1],$parameter_list[2]);
				}
				if ($user_command=="ENGINE_LOGS"){
					return $this->logs;
				}
				if ($user_command=="ENGINE_HAS_MODULE"){
					return $this->have_module($parameter_list[0]);
				}
				if ($user_command=="ENGINE_GET_MODULE_MENU"){
					return $this->get_admin_menus($parameter_list);
				}
				if ($user_command=="ENGINE_GET_DOMAIN"){
					return $this->domain;
				}
				if ($user_command=="ENGINE_CALL_COMMAND"){
					$cmd = $parameter_list[0];
					$params = $parameter_list[1];
					return $this->engine_call_command($cmd,$params);
				}
				
				if ($user_command=="ENGINE_SPLASH"){
					return $this->splash($parameter_list);
				}
				if ($user_command=="ENGINE_ILLEGAL_ACCESS"){
					return $this->illegal_access($parameter_list);
				}
				if ($user_command=="ENGINE_ACCESSKEYS"){
					return $this->accesskeys($parameter_list);
				}
				
				if ($user_command=="ENGINE_ABOUTUS"){
					return $this->splash($parameter_list);
				}
				if ($user_command=="ENGINE_VERSIONS"){
					return $this->versions($parameter_list);
				}
				if ($user_command=="ENGINE_RETRIEVE"){
					return $this->retrieve($this->check_parameters($parameter_list,0),$this->check_parameters($parameter_list,1));
				}
				if ($user_command=="ENGINE_GET_PATH"){
					return $this->site_directories[$parameter_list[0]];
				}
				if ($user_command=="ENGINE_LOGOUT"){
					$s = session_id();
					$this->call_command("SESSION_DESTROY");
					session_id(uniqid(rand()));
					$this->page_output = "";
					$this->script = "";
					$this->real_script = "";
					$this->page_clear=1;
					$this->refresh($parameter_list);
					return 1;
				}
				if ($user_command=="ENGINE_REFRESH_BUFFER"){
					$this->refresh($parameter_list);
				}
				if ($user_command=="ENGINE_REGEN_MENUS"){
					return $this->regen_menus();
				}
				if ($user_command=="ENGINE_RESTORE"){
					return $this->site_restore($parameter_list);
				}
				if ($user_command=="ENGINE_REPORT"){
//					print_r($this->parent->site_directories);
				}
			}else{
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- because this is the engine module which has a list of all modules
				- currently loaded we will see if the module that will execute the
				- command has been loaded yet if not we will load the module and call
				- the function.  if the module has been loaded then we can just use it
				- again.
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$start_position_search_for_starter = strpos($user_command,"_");
/*
				print "<li>$user_command</li>";
				if($user_command=="DB_QUERY"){
					print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameter_list,true)."</p></li>";
				}
*/
				if ($start_position_search_for_starter===false){
					
				}else {
					$index = substr($user_command,0,$start_position_search_for_starter+1);
					if ($this->check_parameters($this->parent->modules,$index,"__NOT_FOUND__")!="__NOT_FOUND__"){
						if ((file_exists(dirname(__FILE__)."/".$this->parent->modules[$index]["file"]))){
							if ($this->parent->modules[$index]["module"]==null){
								//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
								//- load the module
								//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
								if (file_exists(join("/",split("\\\\",dirname(__FILE__)))."/".$this->parent->modules[$index]["file"])){
									require_once dirname(__FILE__)."/".$this->modules[$index]["file"];
									$this->modules[$index]["module"] = new $this->modules[$index]["name"]($this,$parameter_list);
									$this->parent->modules[$index]["loaded"]=1;
									if($index=="DB_"){
										$this->parent->db_pointer = $this->parent->modules[$index]["module"];
									}
								}
							}
							if($this->parent->modules[$index]["loaded"]==1){
								return $this->parent->modules[$index]["module"]->command($user_command,$parameter_list);
							}
						}
					}
				}
			}
		}
	}
	/**
	* function to retrieve a specific command from all modules
	*/
	function retrieve($command,$parameter_list= Array()){
		$outputtype  = $this->check_parameters($parameter_list, "addtype", 1);
		$parameter_list["addtype"] = null;
		$out=array();
		foreach($this->parent->modules as $index => $module_to_load){
			if ($this->parent->modules[$index]["module"]==null){
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				//- load the module
				//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//				print "<li>[";
				if (file_exists(join("/",split("\\\\",dirname(__FILE__)))."/".$this->parent->modules[$index]["file"])){
					require_once dirname(__FILE__)."/".$this->parent->modules[$index]["file"];
					$this->parent->modules[$index]["module"] = new $this->parent->modules[$index]["name"]($this);
					$this->parent->modules[$index]["loaded"]=1;
				}
//				print "]";
			}
			if($this->parent->modules[$index]["loaded"]==1){
				if($outputtype==1){
					$out[count($out)]= Array($this->parent->modules[$index]["tag"], $this->parent->modules[$index]["module"]->command($this->parent->modules[$index]["tag"].$command,$parameter_list), $this->parent->modules[$index]["admin"]);
				} else {
					$out[count($out)]= Array($this->parent->modules[$index]["tag"], $this->parent->modules[$index]["module"]->command($this->parent->modules[$index]["tag"].$command,$parameter_list));
				}
			}
		}
		return $out;
		
	}
	/**
	* function to call the command function of this module. Overwrites the function in the
	* class Module as the class module calls this->parent while engine calls itsself
	*/
	
	function call_command($user_command,$parameter_list=array()){
		return $this->command($user_command,$parameter_list);
	}
	/**
	* initialise the engine module with all settings required.
	*/
	function engine_initalise(){
		$this->logs=256;
		$initialised = $this->load_config();
		$this->logs=256;
		if ($initialised){
			$initialised = $this->check_config();
		}
		return $initialised;
	}
	/**
	* load the configuration for this client
	*/
	function load_config(){
		$this->parent->modules = array();
		/**
		*
		* Initialise the engine load the configuration file and configure the system.
		*/

		if (file_exists(CONFIGURATION_DIRECTORY."/licence_data.php")){
			include(CONFIGURATION_DIRECTORY."/licence_data.php");
			$file = split("\n",LICENCE);
			$cmd="";
			for ($index=0,$length=count($file);$index<$length;$index++){
				if (strpos($file[$index],"#")===0){
					$cmd=trim($file[$index]);
					$index++;
				}
				if ($cmd=="#MODULES"){
					$pram=split(",",trim($file[$index]));
					$this->add_module($pram);
				}else if ($cmd=="#SERVER"){
					$param = split('::',trim($file[$index]));
					$tag= $param[0]."";
					$val= $param[1]."";
					$this->server[$tag]=$val;
/*				}else if ($cmd=="#DATABASE"){
//					print "\$this-".">".trim($file[$index]).";";
					eval("\$this-".">".trim($file[$index]).";");
				}else if ($cmd=="#LOGS"){
					eval("\$this-".">".trim($file[$index]).";");
*/
				}else {
				}
			}
			if (file_exists(dirname(__FILE__)."/libertas.upgrade.php")){
				$this->add_module(Array("libertas.upgrade.php","upgrade","UPGRADE_",0,1));
			}
			$this->call_command("SESSION_RETRIEVE",array(session_id()));
			if (file_exists(CONFIGURATION_DIRECTORY."/paths.php")){
				$file = file(CONFIGURATION_DIRECTORY."/paths.php");
				$cmd="";
				for ($index=0,$length=count($file);$index<$length;$index++){
					if (strpos($file[$index],"#")===0){
						$cmd=trim($file[$index]);
						$index++;
					}
					if ($cmd=="#PATHS"){
						$param = split(',',trim($file[$index]));
						$tag= $param[0]."";
						$val= $param[1]."";
						$str = "\$this-".">site_directories[\"".$tag."\"]=\"".$val."\";";
						eval($str);
					}else {
					}
				}
				$this->load_locale();
				$this->load_locale("general");
				$this->call_command("SESSION_RETRIEVE",array(session_id()));
			} else {

				$this->call_command("ENGINE_ERROR",array(__FILE__,__LINE__,"The Engine could not load the configuration file [paths.php]"));
				return 0;
			}
			if ($this->server[LICENCE_TYPE]==ECMS){
				$extra_mods = $this->site_directories["MODULE_DIR"]."/licences/ecms.ls";
			} else if ($this->server[LICENCE_TYPE]==MECM){
				$extra_mods = $this->site_directories["MODULE_DIR"]."/licences/wcm.ls";
			} else {
				$extra_mods = $this->site_directories["MODULE_DIR"]."/licences/sw.ls";
			}
//			print $extra_mods;
			if (file_exists($extra_mods)){
//			print "found";
				$list = file($extra_mods);
				for ($i=0,$m=count($list);$i<$m;$i++){
//					print "[$i]";
					$pram=split(",",trim($list[$i]));
//					print_r($pram);
					$this->add_module($pram);
				}
			}
			$filename = CONFIGURATION_DIRECTORY."/db_settings.php";
			if (file_exists($filename)){
				$rows = file($filename);
				for($i=0 , $m = count($rows); $i<$m ; $i++){
					if (strpos($rows[$i],',')===false){
					} else {
						$columns = split(",", chop($rows[$i]));
						if ($columns[0]=="db_host"){
							$this->host = $columns[1]; 
						}
						if ($columns[0]=="db_database"){
						 	$this->database = $columns[1];
						}
						if ($columns[0]=="db_username"){
						 	$this->user = $columns[1];
						}
						if ($columns[0]=="db_password"){
						 	$this->pwd = $columns[1];
						}
					}
				}
			}

			
			return 1;
		} else {
			$this->call_command("ENGINE_ERROR",array(__FILE__,__LINE__,"The Engine could not load the configuration file [licence.php]"));
			return 0;
		}
	}
	/**
	* add an entry to the modules array without loading the modules
	*/
	
	function add_module($module_array){
		$index=count($this->parent->modules);
		/*
		$this->parent->modules[$index] = array(
		"file" 		=> $module_array[0],
		"name" 		=> $module_array[1],
		"tag" 		=> $module_array[2],
		"table"		=> $module_array[3],
		"created"	=> $module_array[4],
		"admin"		=> $module_array[5],
		"module"	=> null,
		"loaded"	=> 0
		);
		*/
//		print "";
//		print_r($module_array); 
//		print "";
		$this->parent->modules[$module_array[2]] = array(
		"file" 		=> $module_array[0],
		"name" 		=> $module_array[1],
		"tag" 		=> $module_array[2],
		"table"		=> $module_array[3],
		"created"	=> 0,
		"admin"		=> $module_array[4],
		"module"	=> null,
		"loaded"	=> 0,
		"cc_code"	=> $module_array[5] // set if create client code exists
		);
		return 1;
	}
	
	function engine_module_list(){
		/**
		*
		* return the list of loaded modules
		*/
		$outText = "";
		$max_length=count($this->parent->modules);
		foreach($this->parent->modules as $index => $moduleEntry){
			if (empty($this->parent->modules[$index][2])){
				$loaded=0;
			}else{
				$loaded=1;
			}
			$outText .= "<module name=\"".$this->parent->modules[$index][0]." command_starter=\"".$this->parent->modules[$index][1]."\" loaded=\"$loaded\"/>";
		}
		return $outText;
	}
	
	
	/**
	* close down the engine properly
	*/
	function engine_close(){
		if (!empty($this->connection)){
			$this->command("DB_CLOSE",array($this->connection));
			return 1;
		}else{
			$this->call_command("ENGINE_ERROR",array(__FILE__,__LINE__,"Connection to database could not be closed as it was not found"));
			return 0;
		}
	}
	/**
	* does this client have a module???
	*/
	
	function have_module($module_name=""){
		if ($module_name==""){
			return 0;
		}else{
			$ok=0;
			//for($index=0,$max_length=count($this->parent->modules);$index<$max_length;$index++){
			foreach($this->parent->modules as $index => $module_information){
				if ($this->parent->modules[$index]["tag"]==$module_name){
					$ok=1;
				}
			}
			return $ok;
		}
	}
	
	/**
	* as part of the administration of the system we need to load all modules to generate
	* a navigation menu in the administration side.
	*/
	
	function get_admin_menus($parameters=Array()){
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$recache = $this->check_parameters($parameters,"recache",0);
		 		
		if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"cache_menu_structure",__LINE__,$data_files."/layout_".$this->client_identifier."_anonymous.xml"));
		}
	
		$file_to_use=$data_files."/layout_".$this->client_identifier."_admin.xml";
		if (file_exists($file_to_use) && ($recache==0)){
			$fp = fopen($file_to_use,"r");
			$out = fread ($fp, filesize ($file_to_use));
			fclose($fp);
		}else{
			$fp = fopen($file_to_use,"w");
			//echo "file_to_use ".$file_to_use;
			$module_list=Array();
			//print_r ($this->parent->modules)."<br>"."<br>"."<br>";
			foreach($this->parent->modules as $index=>$module_entry){
				if ($this->parent->modules[$index]["admin"]>=1 && $this->parent->modules[$index]["admin"]<3){
					print "<p>".$index." ".$this->parent->modules[$index]["admin"]."    ".$this->parent->modules[$index]["file"]."</p>";
					
					if (empty($this->parent->modules[$index]["module"])){
						require_once dirname(__FILE__)."/".$this->parent->modules[$index]["file"];
						$this->parent->modules[$index]["module"] = new $this->parent->modules[$index]["name"]($this);
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_admin_menus",__LINE__,"[$command_eval]"));
						}
					}
					
					//print_r($module_list);
					if (empty($module_list[$this->parent->modules[$index]["module"]->module_grouping])){
						$module_list[$this->parent->modules[$index]["module"]->module_grouping] = Array("children" => "", "items" => "");
					}
					
					$val = $this->parent->modules[$index]["module"]->get_admin_menu_option();
					print "[$val]";
					if ($val!=""){
//						$module_list[$this->parent->modules[$index]["module"]->module_grouping]["items"] .= $val;
					}
				}
			}
			$list="";
			ksort($this->menu_structure);
			foreach($this->menu_structure as $key => $vals){
				for($i=0;$i<count($vals);$i++){
					
					$paths = split("/",$vals[$i][3]);
					$first = $paths[0];
					$paths = join("]]></path>\n<path><![CDATA[",$paths);
					$list .= "<entry first='$first' cmd='".$vals[$i][0]."' name='".$vals[$i][4]."' ignore='".$vals[$i][5]."'>\n";
					$list .= "	<label><![CDATA[".$this->get_constant($vals[$i][1])."]]></label>\n";
					$list .= "	<roles><![CDATA[".$vals[$i][2]."]]></roles>\n";
					$list .= "	<paths>\n<original><![CDATA[".$vals[$i][3]."]]></original>\n<path><![CDATA[$paths]]></path>\n</paths>\n";
					$list .= "</entry>\n";
				}
			}
//			print $list;
/*			$this->exitprogram();
			foreach ($module_list as $key=>$val){
				eval ("\$k = '$key';");
				if ($k==""){
					$k = "Undefined";
					$grouping = "Undefined";
				} else {
					eval("\$grouping = $k;");
				}
				$list .= "<grouping name=\"$grouping\">";
				$list .= $val["items"];	
				$list .= "</grouping>";
			}
			*/
			$out="\t\t<module name=\"admin_menu\">\n<menu>".$list."\t\t</menu></module>\n";
			fwrite($fp, $out);
			fclose($fp);
			$old_umask = umask(0);
			@chmod($file_to_use,LS__FILE_PERMISSION);
			umask($old_umask);
		}
		return $out;
	}
	/**
	* as part of the administration of the system we need to load all modules to generate
	* a navigation menu in the administration side.
	*/
	
	function versions($parameters){
		$type = $this->check_parameters($parameters,"type",0);
		$system_list= "";
		$admin_list	= "";
		$presentation_list	= "";
		foreach ($this->parent->modules as $index=>$val){
			$fname = dirname(__FILE__)."/".$this->parent->modules[$index]["file"];
			if ($this->parent->modules[$index]["admin"]==2){
				//print "<li>".$this->parent->modules[$index]["file"]."</li>";
			}	
			if ($this->parent->modules[$index]["admin"] != 3){
				if (empty($this->parent->modules[$index]["module"])){
					if(file_exists($fname)){
						require_once dirname(__FILE__)."/".$this->parent->modules[$index]["file"];
						$this->parent->modules[$index]["module"] = new $this->parent->modules[$index]["name"]($this);
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"retrieve",__LINE__,"[$command_eval]"));
						}
//						print "<li>$index - $command_eval</li>";
					} else {
//						print "<li><font color='red'>not found $fname</font></li>";
					}
				} else {
//					print "<li><font color='green'>loaded $fname</font></li>";
				}
			$val = "<entry
						name=\"".$this->parent->modules[$index]["module"]->module_name_label."\" 
						version=\"".$this->parent->modules[$index]["module"]->get_module_version()."\" 
						creation=\"".$this->parent->modules[$index]["module"]->get_module_creation()."\" 
						modified=\"".$this->parent->modules[$index]["module"]->get_module_modify()."\"
						
					/>";
					//date ("d/m/Y", filemtime(dirname(__FILE__)."/".$this->parent->modules[$index]["file"]))
				if ($this->parent->modules[$index]["admin"] == 2){
					$system_list.= $val;
				}
				if ($this->parent->modules[$index]["admin"] == 1){
					$admin_list	.= $val;
				}
				if ($this->parent->modules[$index]["admin"] == 0){
					$presentation_list	.= $val;
				}
			}
		}
		$out="<module name=\"versions\" display=\"form\">
				<form name=\"module_types\" label=\"Module Type Definitions\" method=\"post\">
					<page_sections>
						<section label='Administration Modules'>$admin_list </section>
						<section label='Presentation Modules'>$presentation_list</section>
						<section label='System Modules'>$system_list</section>
					</page_sections>
				</form>
			</module>\n";
		return $out;
	}
	
	/**
	* Tell the engine to execute a specific command if the user is not logged in
	* then you can supply the login form for displayal on the web site.
	*/
	function engine_call_command($command,$params=Array()){
		$this->module_display_mode = $this->check_parameters($_SESSION,"displaymode","");
		if($this->real_script == "-/-toggle-printer-friendly-mode.php"){
			$this->command("ENGINE_PRINTER_FRIENDLY_VERSION",$params);
		}
		$this->parent->sp_ssl_available	= $this->check_prefs(Array("sp_ssl_available","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No"));
		$sp_page_options			= $this->check_prefs(Array("sp_page_options","default"=>"PTR|COM|EAF","module"=>"SYSPREFS_", "options"=>"PTR|COM|EAF:PTR|COM|EAF|TXT:PTR|COM|EAF|TXT|TOP:PTR|COM|EAF|TXT|TOP|HOME:PTR|COM|EAF|TXT|TOP|HOME"));
		$sp_use_antispam			= $this->check_prefs(Array("sp_use_antispam","default"=>"Yes","module"=>"SYSPREFS_", "options"=>"Yes:No"));
		$sp_page_title_is_caps		= $this->check_prefs(Array("sp_page_title_is_caps","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No"));
		$powerby_in_new_window		= $this->check_prefs(Array("sp_powerby_in_new_window","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No"));
		$sp_blank_field_on_click	= $this->check_prefs(Array("sp_blank_field_on_click","default"=>"Yes","module"=>"SFORM_", "options"=>"Yes:No"));
		$sp_wai_forms				= $this->check_prefs(Array("sp_wai_forms","default"=>"Yes","module"=>"SFORM_", "options"=>"Yes:No"));
		if ($this->server[LICENCE_TYPE]==ECMS){
			$sp_meta_dublin_core= $this->check_prefs(Array("sp_meta_06_dublin_core","default"=>"Yes","module"=>"METADATA_", "options"=>"Yes:No"));
		} else {
			$sp_meta_dublin_core="no";
		}
		if ($this->check_parameters($_SESSION,"displaymode")=="textonly"){
			$sp_wai_forms="Yes";
			$sp_blank_field_on_click="No";
		}
//		print "[$sp_wai_forms, $sp_blank_field_on_click]";
		$sql ="select * from menu_data where menu_url = '$this->script' and menu_client= $this->client_identifier";
//		$result  = $this->parent->db_pointer->database_query($sql);
		$result  = $this->parent->db_pointer->database_query($sql);
		$menu_ssl=0;
//        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$menu_ssl = $this->check_parameters($r,"menu_ssl",0);
        }
		$this->parent->db_pointer->database_free_result($result);
		if ($menu_ssl==1){
			// check if we are using ssl in this menu location
			// if not then refresh onto the ssl cert
			if($this->check_parameters($_SERVER,"HTTPS")=="on"){
				
			}else {
				$this->qstr ="";
				if(count($params)>0){
					foreach($params as $k => $v){
						if ("fake_uri"!=$k && "ignoreCommand"!=$k && "category"!=$k && "my_session_identifier"!=$k){
							if($this->qstr != ""){
								$this->qstr.="&amp;";
							}
							$this->qstr.="$k=$v";
						}
					}
				}
				$this->call_command("ENGINE_REFRESH_BUFFER",array("url"=>"https://".$this->domain.$this->base.$this->script."?".$this->qstr));
			}
		}
		$sp_privacy = strtoupper($this->check_prefs(Array("sp_privacy")));
		if ($sp_privacy!=""){
			header("P3P: CP='$sp_privacy'");
		}
		@$sp_compression = $this->check_parameters($_SESSION,"SESSION_COMPRESS_DATA","");
		if (empty($sp_compression)){
			$sp_compression = strtoupper($this->check_prefs(Array("sp_compression","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No")));
//			$sp_compression="no";
//		 	$_SESSION["SESSION_COMPRESS_DATA"]=$sp_compression;
		}

		if ($sp_compression=="YES"){
			ob_start("compress_output_option");
			ob_implicit_flush(0);
		}
		$date_time				= $this->check_parameters($_SESSION,"SESSION_DATE_TIME");
		$auto_log_out_minutes	= $this->check_prefs(Array("sp_time_out_minutes","default"=>"180","module"=>"SYSPREFS_", "options"=>"5:10:15:30:60:120:180"));
		$wai_forms				= strtoupper($this->check_prefs(Array("sp_wai_forms","default"=>"Yes","module"=>"SFORM_", "options"=>"Yes:No")));
		if (!empty($date_time)){
			if (floor((time()- strtotime($date_time,0))/60)>$auto_log_out_minutes){
				if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN")==1){
					$this->call_command("ENGINE_LOGOUT");
				}
			} else {
				$_SESSION["SESSION_DATE_TIME"]=$this->libertasGetDate("Y/m/d H:i:s");
			}
		}
		$display_time=$this->check_parameters($_SESSION,"SESSION_DISPLAY_TIME");
//$display_time="YES";
		if (
				(
					($display_time=="YES")
						&&
					(!$this->check_parameters($params,"LIBERTAS_XML"))
				) 
					|| 
				$this->domain==$this->DEV_SERVER
			){
			$time_start = $this->getmicrotime();
		}
		$code = LIBERTAS_LANG_CHARSET;
		$copy_of_parameters = $params;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"engine_call_command",__LINE__,"[$command,".join(",",$params)."]"));
		}

		if ($this->module_type=="website" || $this->module_type=="EXECUTE"){
			$if_not_found = $this->call_command("LAYOUTSITE_GET_THEME_ID");
			$theme_identifier = $this->check_parameters($_SESSION,"CHOOSEN_THEME",$if_not_found);
			if ($theme_identifier=="0"){
				$theme_identifier=$if_not_found;
			}
			/*************************************************************************************************************************
            * condition to only alow pda format on enterprise sites
            *************************************************************************************************************************/
//			if ($this->check_parameters($_SESSION,"displaymode")=="pda" && $this->server[LICENCE_TYPE]!=ECMS){
//				$_SESSION["displaymode"]="";
//			}
			if(empty($params["stylesheet"])){
//					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//					- need to add a 
//					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				if (!empty($command)){
					$theme_parameters = array("command" => $command,"theme_identifier" => $theme_identifier, "fake_uri" => $this->check_parameters($params,"fake_uri"));
				} else {
					$theme_parameters = array("theme_identifier" => $theme_identifier, "fake_uri" => $this->check_parameters($params,"fake_uri"));
				}
				$info = $this->call_command("THEME_GET_STYLESHEET",$theme_parameters);
//				print "<li>".__FILE__."@".__LINE__."<pre>".print_r($info, true)."</pre></li>";
				$stylesheet = $info[0];
				$this->theme_type_label = $info[2];
				$this->theme_stylesheet = $stylesheet;
				$theme_identifier = $info[1];
				$this->previous_theme = $theme_identifier;
			} else {
				$stylesheet = $params["stylesheet"];
			}
			$this->choosen_theme= $this->check_parameters($_SESSION,"CHOOSEN_THEME",0);
			if ($this->check_parameters($_SESSION, "displaymode")=="textonly"){
				$this->choosen_theme = -3;
			}else if ($this->check_parameters($_SESSION, "displaymode")=="printerfriendly"){
				$this->choosen_theme = -2;
			}else if ($this->check_parameters($_SESSION, "displaymode")=="pda"){// && $this->server[LICENCE_TYPE]==ECMS){
				$this->choosen_theme = -1;
				$this->real_choosen_theme = $theme_identifier;
			} else {
				if ($this->choosen_theme==0){
					$this->choosen_theme = $theme_identifier;
				}
			}
//			print __LINE__." [$this->choosen_theme]";
//			$this->exitprogram();
		}
//		$code ="ISO-8859-1";
//		$code ="UTF-8";
		$extra="";
		$this->page_output = "<?xml version=\"1.0\" encoding=\"$code\" ?".">\n
			<xml_document ";
		$this->page_output .= ">\n";

		if ($this->module_type=="preview"){
			$extra .= "<setting name='script'><![CDATA[".$this->call_command("LAYOUTSITE_GET_LOCATION_URL",Array("id"=>$this->check_parameters($params,"trans_menu_locations",$this->check_parameters($params,"irl_menu_locations",-1))))."]]></setting> ";
		} else {
			$fake_uri = $this->check_parameters($params,"fake_uri");
			if($fake_uri != ""){
				$this->script = $fake_uri;
			}
			$extra .= "<setting name='script'><![CDATA[$this->script]]></setting> ";
			
		}
		if(file_exists($this->check_parameters($this->site_directories,"ROOT")."/_view-cart.php")){
			$extra .= "<setting name='shopping'><![CDATA[1]]></setting> ";
		}
		
		$extra .= "<setting name='powerby_in_new_window'><![CDATA[$powerby_in_new_window]]></setting> ";
		$extra .= "<setting name='real_script'><![CDATA[$this->real_script]]></setting> ";
		$extra .= "<setting name='sp_meta_dublin_core'><![CDATA[$sp_meta_dublin_core]]></setting> ";
		$extra .= "<setting name='real_url'><![CDATA[".$_SERVER["REQUEST_URI"]."]]></setting> ";
		$dir = dirname($this->real_script);
		if ($dir=="."){
			$dir="";
		}
		$extra .= "<setting name='fake_script'><![CDATA[".$dir."]]></setting> ";
		$extra .= "<setting name='fake_category'><![CDATA[".substr($dir,strlen(dirname($this->script))+1)."]]></setting> ";
		$fake_title = $this->check_parameters($params,"fake_title","");
		$extra .= "<setting name='fake_title'><![CDATA[".$fake_title."]]></setting> ";
		
		$css = $this->check_parameters($_SESSION,"CHOOSEN_CSS","default");
		$extra .= "<setting name='css'><![CDATA[".$css."]]></setting> ";
		$css_override = $this->check_parameters($_SESSION,"css_override","");
		$extra .= "<setting name='overridecss'><![CDATA[".$css_override."]]></setting> ";
		$extra .= "<setting name='sp_use_antispam'><![CDATA[".$sp_use_antispam."]]></setting> ";
		if ($this->server[DATE_EXPIRES]!=UNLIMITED){
			$v = substr($this->server[DATE_EXPIRES],0,10);
			$extra .= "<setting name='expires'><![CDATA[".date("Y/m/d H",$v).":00:00\"]]></setting> ";
		}
		if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!="on") {
			$extra .= "<setting name='SSL'><![CDATA[no]]></setting>";
		} else {
			$extra .= "<setting name='SSL'><![CDATA[yes]]></setting>";
		}

		$extra .= $this->call_command("WEBOBJECTS_EXTRACT_SETTINGS");
		if ($this->module_type=="website" || $this->module_type=="EXECUTE"){
			$extra .= $this->presentation_get_dates();
		}
		if (file_exists($this->check_parameters($this->site_directories,"ROOT")."/images/favicon.ico")){
			$extra .= "<setting name='favicon'><![CDATA[1]]></setting>";
		}
		$fontsize  = $this->check_parameters($_SESSION,"fontsize",$this->check_parameters($_SESSION,"CHOOSEN_SIZE"));
		$fformat = strtoupper($this->check_prefs(Array("sp_file_list_format", "default" => "Title Only", "module" => "FILES_", "options" => "Table:List:Title and Summary:Title Only")));
		$dateformat = $this->check_prefs(Array("sp_default_time_format", "default" => "DDxx MMMM YYYY", "module" => "SYSPREFS_", "options" => "DDxx MMMM YYYY:d, DD MMM YYYY"));
		$secured_page_redirect = $this->check_prefs(Array("sp_secured_page_redirect", "default" => "Home", "module" => "SYSPREFS_", "options" => "Home:Login Screen"));
		
		$extra .= "<setting name='file_list_format'><![CDATA[".$fformat."]]></setting> ";
		$extra .= "<setting name='isbot'><![CDATA[".$this->check_parameters($_SESSION,"IS_BOT",0)."]]></setting>";
		$extra .= "<setting name='cookieset'><![CDATA[".$this->check_parameters($_COOKIE,"LEI")."]]></setting>";
		$extra .= "<setting name='fontsize'><![CDATA[".$fontsize."]]></setting>";
		$extra .= "<setting name='displaymode'><![CDATA[".$this->module_display_mode."]]></setting>";
		$extra .= "<setting name='year'><![CDATA[".gmdate ("Y",time())."]]></setting>";
		$extra .= "<setting name='month'><![CDATA[".gmdate ("m",time())."]]></setting>";
		$extra .= "<setting name='day'><![CDATA[".gmdate ("d",time())."]]></setting>";
		$dfmt = "jS F Y";
		if($dateformat=="DDxx MMMM YYYY"){
			$dfmt = "jS F Y";
		} else if($dateformat=="d, DD MMM YYYY"){
			$dfmt = "D, d M Y";
		}
		$extra .= "<setting name='date'><![CDATA[".gmdate ($dfmt,time())."]]></setting>";

		$extra .= "<setting name='time'><![CDATA[".gmdate ("H:i:s",time())."]]></setting>";
		$extra .= "<setting name='domain'><![CDATA[".$this->domain."]]></setting>";
		$extra .= "<setting name='base'><![CDATA[".$this->base."]]></setting>";
		$extra .= "<setting name='browser'><![CDATA[".$this->getValidHTTPAgent($this->check_parameters($_SERVER,"HTTP_USER_AGENT"))."]]></setting>";
		$extra .= "<setting name='theme_directory'><![CDATA[".$this->call_command("THEME_GET_CSS")."]]></setting>";
		$extra .= "<setting name='sp_blank_field_on_click'><![CDATA[".$sp_blank_field_on_click."]]></setting>";
		$extra .= "<setting name='sp_wai_forms'><![CDATA[".$sp_wai_forms."]]></setting>";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($this->module_display_mode=='textonly'){
			$fname = $data_files."/accesskeys_".$this->client_identifier."_visible.xml";
		} else {
			$fname = $data_files."/accesskeys_".$this->client_identifier.".xml";
		}
		if (file_exists($fname)){
			$extra .= implode("",file($fname));
		}
		
		$this->qstr =$this->check_parameters($_SERVER,"QUERY_STRING");
		$find 		= Array ("'".session_name()."=".session_id()."'","'&$'si","'&&'si");
		$replace 	= Array ('','','');
		
		$this->qstr = preg_replace (
			$find,
			$replace,
			$this->qstr);
		$fc = count($find);
		$rc = count($replace);
		if ($fc!=$rc){
			print "Error code #LS000006 - $fc find attributes versus $rc replace attributes in function make_uri ".__FILE__." line ".__LINE__."<br>";
		}
		$extra .= "<setting name='remote_addr'><![CDATA[".$this->check_parameters($_SERVER,"REMOTE_ADDR")."]]></setting>";
		$extra .= "<setting name='qstring'><![CDATA[".$this->qstr."]]></setting>";
		$this->page_output .= "\t<modules>\n";
		$this->page_output .= $this->call_command("SYSPREFS_EXTRACT_ALL_SETTINGS",Array("extra"=>$extra));
		$this->page_output .= $this->call_command("CLIENT_DISPLAY_DETAIL");
		$execute_user_commands=$this->check_parameters($_SESSION,"SESSION_USER_COMMANDS");
		if (!empty($execute_user_commands)){
			$execute_list = split(",",$execute_user_commands);
			$max = count($execute_list);
			if($max>0){
				for($index=0;$index<$max;$index++){
					$this->page_output .= $this->call_command($execute_list[$index]);
				}
			}
		}
		if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==1){
			/*************************************************************************************************************************
            * if the user has logged in then check the expiry infomration
			*	SESSION_GROUP_TYPE === 3 means expired
            *************************************************************************************************************************/
			$acc_expire	= $this->check_parameters($_SESSION, "SESSION_ACCOUNT_DATE_EXPIRES", 0);
			$acc_grace	= $this->check_parameters($_SESSION, "SESSION_ACCOUNT_DATE_GRACE", 0);
			$now 		= strtotime(date("r"));
//			print "[acc_expire::$acc_expire]";
//			print "[acc_grace::$acc_grace]";
//			print "[now::$now]";
			if($acc_expire>0 && $acc_expire<$now){
				if ($acc_grace>0 && $acc_grace<$now){
					$_SESSION["SESSION_GROUP_TYPE"]=3;
					$grace='Account Expired';
				}else{
					if ($acc_grace==0){
						$_SESSION["SESSION_GROUP_TYPE"] =3; 
						$grace='Account Expired';
					}else {
						$days = ceil(($acc_grace - $now) / 86400);
						$grace='Account expires in '.$days.' days';
					}
				}
				$this->page_output .= "<module name='system_prefs' display='settings'><setting name='graceperiod'><![CDATA[$grace]]></setting></module>\n";
			}
		}
		if ($this->module_type=="view_comments"){
			print $this->call_command("NOTESADMIN_VIEW",Array("identifier"=>$this->check_parameters($params,"identifier","-1")));
			$this->exitprogram();		
		} else if ($this->module_type=="keyword_gen"){
			if ($this->check_parameters($params,"source","_NOT_SUPPLIED_")=="_NOT_SUPPLIED_"){
				print "
				<html>
					<body>
						<form method=post name='keyGen'>
							<textarea name='source'></textarea>
							<textarea name='extraIgnoreList'></textarea>
						</form>
					</body>
				</html>";
				$this->exitprogram();
			} else {
				$keys	= $this->call_command("UTILS_REGENERATE_KEYWORDS",Array($this->check_parameters($params,"source",""),6, $this->check_parameters($params,"extraIgnoreList")));
				print "<html><body>[<span id='returnedValue'>$keys</span>]</body><script>
				window.parent.listen(this['returnedValue'].innerHTML);
				</script></html>";
				$this->exitprogram();
			}
		}else if (($this->module_type=="admin") || ($this->module_type=="preview")){
			$params["show_anyway"]=0;
			$this->page_output  .= $this->call_command("USERS_CHECK_LOGIN",$params);

//			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//			- as part of the administration of the system we need to have the user login to access
//			- this functionality.
//			- If this is the first screen after logging into the sytem then show the splash screen
//			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==1){

				if ($_SESSION["SESSION_GROUP_TYPE"]==2){
					if (ADMIN_OPEN==1 || $_SESSION["SESSION_USER_NAME"]=='l1b_adm1n_us3r'){
						if (($command=="ENGINE_LOGIN")||($command=="")){
							$command="ENGINE_SPLASH";
	//						$params=Array();
						}
						$this->page_output .= "\t\t".$this->call_command("SESSION_DISPLAY")."\n";
						if ($this->module_type!="preview"){
							$this->page_output .= $this->call_command($command,@$params);
						}
						if ($this->module_type=="admin"){
							$this->page_output .= $this->get_admin_menus();
							$stylesheet="/stylesheets/themes/site_administration/main_template.xsl";
						}
						if ($this->module_type=="preview"){
							if($command!="RSSADMIN_INTERNAL_PREVIEW" && $command!="RSSADMIN_EXTERNAL_PREVIEW"){
								$this->page_output .= $this->call_command("LAYOUT_WEB_MENU",$params);
								$this->p_script = $this->script;
								$locs = $this->check_parameters($params,"trans_menu_locations",$this->check_parameters($params,"irl_menu_locations"));
								$id ="";
								if (is_array($locs)){
									$id = $this->check_parameters($locs,0);
									$this->script = $this->call_command("LAYOUT_GET_LOCATION_URL",Array("id"=>$id));
								} else {
									if (is_array($locs)){
										$id = $locs[0];
										$this->script = $this->call_command("LAYOUT_GET_LOCATION_URL",Array("id"=>$id));
									} else {
										$id_list = split(",",$locs);
										$id = trim($id_list[0]);
										$this->script = $this->call_command("LAYOUT_GET_LOCATION_URL",Array("id"=>$id));
									}
								}
								$overridescript = $this->script;
								$this->page_output .= $this->call_command("LAYOUTSITE_GET_PAGE",$params);
								$this->script=$this->p_script;
								$theme_identifier = $this->call_command("THEME_GET_CURRENT");
								$info = $this->call_command("THEME_GET_STYLESHEET",Array("theme_identifier" => $theme_identifier, "style_identifier" => 2, "override_script" => $overridescript));
								$stylesheet = $info[0];
								$theme_identifier = $info[1];
								//|-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-|
								//| check to see if this location has a mirror								  |
								//|-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-|
								//$this->page_output .= $this->call_command("MIRROR_PAGE_HAS",$params);
							} else {
								$this->call_command("LAYOUTSITE_GET_PAGE",$params);
							}
						}
					} else {
						$this->page_output .= "<module name='engine' display='form'><form name='admin_closed'><text><![CDATA[Maintenance in progress please login later]]></text></form></module>";
					}
				}else{
					if ($command!="ENGINE_LOGOUT"){
						if($_SESSION["SESSION_GROUP_TYPE"]==3){
					    	$this->page_output .= "<module name=\"engine\" display=\"form\"><form label=\"Sorry your account has expired\" method=\"post\" name=\"illegal_acecss\"></form></module>";
						}else{ 
					    	$this->page_output .= "<module name=\"engine\" display=\"form\"><form label=\"Sorry you do not have access privigles to this location.\" method=\"post\" name=\"illegal_acecss\"><text><![CDATA[This location is for Administrators only.<br /> You have two options you can :- <br /><ol><li>return to the main site by <a href=\"".$this->base."index.php\">clicking here</a></li><br />or <li>you can log out by <a href=\"".$this->base."?command=ENGINE_LOGOUT\">clicking here</a></li></ol>]]></text></form></module>";
						}
					} else {
						$this->page_output .= $this->call_command($command);
					}
				}
			}
			if ($this->module_type=="admin"){
				if ($this->script=="admin/load_cache.php"){
					$stylesheet = "/stylesheets/themes/site_administration/filelist.xsl";
				}else{
					$stylesheet="/stylesheets/themes/site_administration/main_template.xsl";
				}
			} else {
				if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==0){
					if ($this->script!="admin/index.php"){	
						$this->refresh(Array($this->base."admin/index.php?"));
					}
				}
			}
			$this->page_output = $this->check_editor_requirement($this->page_output);
		}
		if ($this->module_type=="files"){
			$this->page_output  .= $this->call_command("USERS_CHECK_LOGIN",array($command,@$params["login_user_name"],@$params["login_user_pwd"]));
//			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//			- as part of the administration of the system we need to have the user login to access
//			- this functionality.
//			- If this is the first screen after logging into the sytem then show the splash screen
//			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==1){
				$this->page_output .= $this->call_command($command,@$params);
				$this->page_output .= "\t\t".$this->call_command("SESSION_DISPLAY")."\n";
			}
			$stylesheet="/stylesheets/themes/site_administration/file_associate.xsl";
//			print "<li>$stylesheet</li>";
			$this->page_output = $this->check_editor_requirement($this->page_output);
		}
		/*************************************************************************************************************************
        * normal website (default mode)
        *************************************************************************************************************************/
		if ($this->module_type=="website" || $this->module_type=="EXECUTE"){
			if ($this->module_type == "EXECUTE"){
				if ($command!="INFORMATION_DISPLAY"){
					$params["command"] = "$command";
					if(!isset($parameters["ignore_commands"])){
						$params["ignore_commands"]=Array();
						$params["ignore_commands"][count($params["ignore_commands"])]="INFORMATION_DISPLAY";
					} else {
						$params["ignore_commands"][count($params["ignore_commands"])]="INFORMATION_DISPLAY";
					}
				}
			}
			$have_access = $this->call_command("LAYOUTSITE_MENU_HAS_ACCESS",$params);
			if ($have_access==0){
				if($secured_page_redirect=="Home"){
					Header("Location: http://".$this->domain.$this->base."index.php");
					$this->exitprogram();
				}
				
				if($secured_page_redirect=="Login Screen"){
					$_SESSION["referal_script"] = $this->real_script;
					if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)==0){
						$this->refresh(Array("url"=>"http://".$this->domain.$this->base."-/-login.php"));
					} else {
						$this->refresh(Array("url"=>"http://".$this->domain.$this->base."index.php?command=ENGINE_ILLEGAL_ACCESS"));
					}
				}
			}
//				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//				- if it is the web site then login is not necessarly required so just display the
//				- page.  The modules that will be supplying the page should use the session
//				- information to produce a desired output from the function that will match what
//				- the user should see.
//				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				$params["SCRIPT_NAME"]=$_SERVER["SCRIPT_NAME"];
				if ($command.""!=""){

					if ($command=="ENGINE_LOGIN"){
						$this->page_output  .= $this->call_command("USERS_CHECK_LOGIN",$params);
					}
				}
				$this->page_output .= "\t\t".$this->call_command("SESSION_DISPLAY")."\n";
				$this->page_output .= $this->call_command("LAYOUTSITE_GET_PAGE",$params);
//				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//				- get the menu of the site
//				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				$this->page_output .= $this->call_command("LAYOUTSITE_WEB_MENU",$params);
				if ((empty($stylesheet)) && ($this->script!="index.php")){
//					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//					- retrieve the default style sheet if none specified
//					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					$stylesheet = "/stylesheets/themes/".$this->call_command("THEME_GET_CSS")."/default_main.xsl";
				}
		}
		if ($this->page_clear==0){
			$this->page_output .= "\t</modules>\n";
			if($this->show_base_href==1 || $this->real_script=="_process.php"){
				$this->page_output .= "<setting name='showbasehref'><![CDATA[http://".$this->domain.$this->base."]]></setting>";
			}
			$debug  = $this->call_command("UTILS_DISPLAY_DEBUG");
			if ($this->check_parameters($_SESSION,"debug_list","__NOT_FOUND__")!="__NOT_FOUND__"){
				$debug .= $this->check_parameters($_SESSION,"debug_list");
				unset($_SESSION["debug_list"]);
			}
			$errors = $this->call_command("UTILS_DISPLAY_ERRORS");
			$this->page_output .= $this->load_translation();
			if ($this->module_type=="website" || $this->module_type=="EXECUTE"){
				if ($this->check_parameters($_SESSION,"displaymode")=="printerfriendly" || $this->choosen_theme==-2){
					$style = substr($stylesheet,strrpos($stylesheet,"/"));
					$rest = substr($stylesheet,0,strrpos($stylesheet,"/"));
					$s = strrpos($rest,"/")+1;
					$f = strrpos(substr($stylesheet,$s),"/");
					$prev_style = substr($stylesheet,$s ,$f);
					$stylesheet = "/stylesheets/themes/printer_friendly"."$style";
					$this->page_output .="<prev_style>$prev_style</prev_style>";
					$this->choosen_theme = -2;
				}
				if ($this->check_parameters($_SESSION,"displaymode")=="textonly" || $this->choosen_theme==-3){
					$style = substr($stylesheet,strrpos($stylesheet,"/"));
					$rest = substr($stylesheet,0,strrpos($stylesheet,"/"));
					$s = strrpos($rest,"/")+1;
					$f = strrpos(substr($stylesheet,$s),"/");
					$prev_style = substr($stylesheet,$s ,$f);
					$stylesheet = "/stylesheets/themes/textonly"."$style";
					$this->page_output .="<prev_style>$prev_style</prev_style>";
					$this->choosen_theme = -3;
				}
				if ($this->check_parameters($_SESSION,"displaymode")=="pda" || $this->choosen_theme==-1){
					$style = substr($stylesheet,strrpos($stylesheet,"/"));
					$rest = substr($stylesheet,0,strrpos($stylesheet,"/"));
					$s = strrpos($rest,"/")+1;
					$f = strrpos(substr($stylesheet,$s),"/");
					$prev_style = substr($stylesheet,$s ,$f);
					$stylesheet = "/stylesheets/themes/pda"."$style";
					$this->page_output .="<prev_style>$prev_style</prev_style>";
					$this->choosen_theme = -3;
				}
			}
			$this->page_output .= "</xml_document>";
//				print "<li>".__FILE__."@".__LINE__."<p>Exit point</p></li>".$stylesheet." ".$this->page_output;
//				$this->exitprogram();
			if (
				(
					($display_time=="YES")
						&&
					(!$this->check_parameters($copy_of_parameters,"LIBERTAS_XML"))
				) 
					|| 
				$this->domain==$this->DEV_SERVER
			){
				$time_xsl = $this->getmicrotime();
			}
			if (($this->check_parameters($copy_of_parameters,"LIBERTAS_XML")=="OPEN_AND_DISPLAY") ){// && (($this->domain == "newdawn" || $this->domain == $this->DEV_SERVER ) || ($this->call_command("SESSION_GET",Array("SESSION_DEBUG_XML_ENABLED"))=="YES"))){
				header("Content-Type: text/plain");
				$output=$this->page_output;
				print $output;
				$this->exitprogram();
			} else if (($this->check_parameters($copy_of_parameters,"LIBERTAS_XML")=="LIST_AND_DISPLAY")){
				header("Content-Type: text/plain");
				$xsl_dir = $this->site_directories["XSL_THEMES_DIR"];
				$html = implode ('', file ($xsl_dir.$stylesheet));
				print $html;
				$this->exitprogram();
			} else {
				$this->call_command("XMLPARSER_LOAD_XML_STR",array(str_replace(null,"",$this->page_output)));
				$xsl_dir = $this->site_directories["XSL_THEMES_DIR"];
				$file = $xsl_dir."".$stylesheet;
				$this->call_command("XMLPARSER_LOAD_XSL_FILE",array($file));
				$output= $this->call_command("XMLPARSER_TRANSFORM");
				$output = join("content=\"text/html; charset=$code\"",split("content=\"text/html; charset=UTF-8\"",$output));
			}
		} else {
			$time_xsl = $this->getmicrotime();
			 
			if($_SERVER["SERVER_PORT"]!="80"){
				$loc = "http://".$this->domain.":".$_SERVER["SERVER_PORT"]."".$this->base."".$this->real_script;
				if (strlen($this->page_parameters)>0){
					$loc .= "?".$this->split_me($this->page_parameters,"&amp;","&")."&".$this->session_parameter;
				} else {
					$loc .= "?".$this->session_parameter;
				}
			}else{
				$loc = "http://".$this->domain."".$this->base."".$this->real_script;
				if (strlen($this->page_parameters)>0){
					$loc .= "?".$this->split_me($this->page_parameters,"&amp;","&")."&".$this->session_parameter;
				} else {
					$loc .= "?".$this->session_parameter;
				}
			}
			header ("Location: $loc");
			$this->exitprogram();
		}
		if (
				(
                    (
					    (
						    ($display_time=="YES")
                            							&&
						    ($this->check_parameters($copy_of_parameters,"LIBERTAS_XML")!="OPEN_AND_DISPLAY")
					    )
				    )
				    &&
				    ($this->module_type!="preview")
                )
                || ($this->domain==$this->DEV_SERVER)
			){
			$time_end = $this->getmicrotime();
			$xml_time = $time_xsl - $time_start;
			$transform_time = $time_end - $time_xsl;
			$time = $time_end - $time_start;	
			$output .= "<div style='text-align:center;clear:both;'>XML Generated in <strong>$xml_time</strong> and transformed in <strong>$transform_time</strong> total time = <strong>$time</strong>";
			$fsize = strlen($output);
			$output .= "<br />Total HTML size (".$fsize.") bytes</div>";
		}
		/*******************Change by Ali to sp_generate_site_logs default value No instead of Yes****************************/
		$sp_generate_site_logs = strtoupper($this->check_prefs(Array("sp_generate_site_logs","default"=>"No","module"=>"USERACCESS_", "options"=>"Yes:No")));
		$sp_generate_admin_logs = strtoupper($this->check_prefs(Array("sp_generate_admin_logs","default"=>"No","module"=>"USERACCESS_", "options"=>"Yes:No")));
		
		if ((($this->module_type=="website")  && ($sp_generate_site_logs=="YES")) || (($this->module_type!="website")  && ($sp_generate_admin_logs=="YES"))){
			/*******************Uncommit & Commit by Ali to Remove User Access Logs****************************/
			$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_PAGE_ACCESS__",$copy_of_parameters));
					                           /*****************************************************************************/
		}
		$this->session_parameter="";

		$o = $this->checkBot($this->fix_A($output),$params);
		$o = str_replace("table style=\"BACKGROUND: none transparent scroll repeat 0% 0%; WIDTH: 100%\"", "table style=\"BACKGROUND: none transparent scroll repeat 0% 0%; WIDTH: auto\"", $o);
		$xstr = "";
		if ($this->updated_date>0){
			header("Last-Modified: ".$this->libertasGetDate("r",$this->updated_date));
		}
		if ($this->check_parameters($copy_of_parameters,"LIBERTAS_XML")!="OPEN_AND_DISPLAY"){
			print $this->print_first;
		}
		if ($this->script!="admin/load_cache.php"){
//			print str_replace(Array("&","&amp;amp;","[[script]]","amp;quot;","amp;#","&#39;","printer_friendly=1&amp;LEI"),Array("&amp;","&amp;",$this->script,"quot;","#","'","printer_friendly=1&LEI"),$o);
			if ($this->module_type=="website" || $this->module_type=="EXECUTE"){
				if ($this->check_parameters($_SESSION,"displaymode")=="textonly"){
					$extract = preg_replace("'<object[^>]*?".">.*?</object>'si", "", $o);
				} else {
					$pos = strpos($o, "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd");
					if(!(strpos($o,' rel="_libertasExternalWindow"')===false)){
						//$o = str_replace("</body>", "<script type=\"text/javascript\" src=\"/libertas_images/javascripts/open_in_external.js\"></script></body>",$o);
					}
					
					if(!$pos){
						$extract = $o;
					} else {
						$findlist		= Array(" </div",	" <div",	" </td", " <table", " </table");
						$replacelist	= Array("</div",	"<div",		"</td",	 "<table", "</table");
						$l = count($findlist);
//						print "[".strlen($o)."]";
						for ($i=0;$i<$l;$i++){
							while (!(strpos($o,$findlist[$i])===false)){
//								print "<li>[".htmlentities($findlist[$i])."], [".htmlentities($replacelist[$i])."]</li>";
								$o = str_replace($findlist[$i], $replacelist[$i],$o);
							}
						}
//						print "[".strlen($o)."]";
						$extract = $o;
					}
				}
			} else {
				$extract = $o;
			}
			$ssl="s";
			if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!="on") {
			$ssl="";
			}
			$m = strToLower($this->module_type);
			if (($m=="website") || ($m=="preview") || ($m=="execute")){
				if ($this->check_parameters($_SESSION,"displaymode")=="pda"){
					$find = Array("[[lt]]","[[gt]]",
						"&#10;","&#9;","&AMP;AMP;","&amp;AMP;",
						"selected=\"true\"",
						"\r\n","\r","\n",chr(32).chr(32),"[[euro]]","[[copy]]","<\\/APPLET>","&","&amp;&","&amp;amp;","[[script]]","amp;quot;",
						"amp;#","&#39;","[[apos]]","[[pos]]","[[amp]]",$this->base."about:blank","<div></div>","<div id=\"webobject\"></div>",
						"<hr id=\"null\" />","<hr>","%2C","".$this->base."http","</li>\n",
						"&#160;&#187;&#160;","\";"," </td","td> ","/>[[anchor]]", "<!doctype","[[anchor]]","<![CDATA[", "]]>","> <img","<p></p>","<p><p","</p></p>",
						// required for page options
						"   ", "  ","</span> </span> </a> </li>"," <span class=\"icon\"> <span class=\"text\">","[[gbp]]","[[eur]]","[[usd]]","[[altreturn]]"
					);
					
					$replace = Array(
						"&#60;","&#62;",
						"","","&amp;","&amp;",
						"selected=\"selected\"",
						" "," "," "," ", "€","&#164;","</APPLET>","&amp;","&amp;","&amp;","http".$ssl."://".$this->domain.$this->base.$this->script,"quot;",
						"#","'","&#39;","&#39;","&","about:blank","","",
						"<hr alt='line'>","<hr alt='line'>",",","http","</li>"," &#187; ","\"","</td","td>","></a>","<!DOCTYPE","","","","><img","","<p","</p>",
						// required for page options
						" "," ","</span></span></a></li>","<span class=\"icon\"><span class=\"text\">","£","€","\$","\n"
					);
				} else {
					$find = Array("[[lt]]","[[gt]]",
						"&#10;","&#9;","&AMP;AMP;","&amp;AMP;",
						"selected=\"true\"",
						"align=center", "align=\"center\"", 
						"align=left", "align=\"left\"", 
						"align=right", "align=\"right\"", 
						"align=justify", "align=\"justify\"",
						"\r\n","\r","\n",chr(32).chr(32),"[[euro]]","[[copy]]","<\\/APPLET>","&","&amp;&","&amp;amp;","[[script]]","amp;quot;",
						"amp;#","&#39;","[[apos]]","[[pos]]","[[amp]]",$this->base."about:blank","<div></div>","<div id=\"webobject\"></div>",
						"<hr id=\"null\" />","<hr>","%2C","".$this->base."http","</li>\n",
						"&#160;&#187;&#160;","\";"," </td","td> ","/>[[anchor]]", "<!doctype","[[anchor]]","<![CDATA[", "]]>","> <img","<p></p>","<p><p","</p></p>",
						// required for page options
						"   ", "  ","</span> </span> </a> </li>"," <span class=\"icon\"> <span class=\"text\">","[[gbp]]","[[eur]]","[[usd]]","[[altreturn]]"
					);
					
					$replace = Array(
						"&#60;","&#62;",
						"","","&amp;","&amp;",
						"selected=\"selected\"",
						"class=\"aligncenter\"", "class=\"aligncenter\"", 
						"class=\"alignleft\"", "class=\"alignleft\"", 
						"class=\"alignright\"", "class=\"alignright\"", 
						"class=\"alignjustify\"", "class=\"alignjustify\"", 
						" "," "," "," ", "€","&#164;","</APPLET>","&amp;","&amp;","&amp;","http".$ssl."://".$this->domain.$this->base.$this->script,"quot;",
						"#","'","&#39;","&#39;","&","about:blank","","",
						"<hr alt='line'>","<hr alt='line'>",",","http","</li>"," &#187; ","\"","</td","td>","></a>","<!DOCTYPE","","","","><img","","<p","</p>",
						// required for page options
						" "," ","</span></span></a></li>","<span class=\"icon\"><span class=\"text\">","£","€","\$","\n"
					);
				}
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					-  find and replace for javascript (does not remove returns
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					$jsfind = Array(
						"align=center", "align=\"center\"", 
						"align=left", "align=\"left\"", 
						"align=right", "align=\"right\"", 
						"align=justify", "align=\"justify\"",
						chr(32).chr(32),"[[euro]]","[[copy]]","<\\/APPLET>","&","&amp;&","&amp;amp;","[[script]]","amp;quot;",
						"amp;#","&#39;","[[apos]]","[[pos]]","[[amp]]",$this->base."about:blank","<div></div>","<div id=\"webobject\"></div>",
						"<hr>","%2C","".$this->base."http","</li>\n",
						"&#160;&#187;&#160;","\";"," </td","td> ","/>[[anchor]]", "<!doctype","[[anchor]]","<![CDATA[", "]]>","<!--", "-->"
					);
					$jsreplace = Array(
						"class=\"aligncenter\"", "class=\"aligncenter\"", 
						"class=\"alignleft\"", "class=\"alignleft\"", 
						"class=\"alignright\"", "class=\"alignright\"", 
						"class=\"alignjustify\"", "class=\"alignjustify\"", 
						" ", "€","&#164;","</APPLET>","&amp;","&amp;","&amp;","http".$ssl."://".$this->domain.$this->base.$this->script,"quot;",
						"#","'","&#39;","&#39;","&","about:blank","","",
						"<hr alt='line'>",",","http","</li>"," &#187; ","\"","</td","td>","></a>","<!DOCTYPE","","","","\n<!--\n", "\n-->\n"
					);

				$rest="";
				if (!(($pos = strpos($extract,"<!-- TERMINATE CLEAN -->"))===false)){
					$extract	= substr($extract, 0, $pos);
					$rest		= substr($extract, $pos);
				}
				if (strpos($extract,"<script")){
					$offpos=0;
					$working = $extract;
					$newstr = "";
					while ($pos = strpos($working,"<script", $offpos)){
						$closeScript1 = strpos($working,"/>",$pos);
						$closeScript2 = strpos($working,">",$pos);
						if ($closeScript1+1==$closeScript2){
							$str = substr($working, $offpos, ($pos-$offpos));
							$newstr .= str_replace(Array("[[jsreturn]]", "<![CDATA[", "]]>"), Array("\n", "", ""), str_replace(
								$find,
								$replace,
								$str)
							);
							$exitpos = $closeScript2+1;
							$newstr .= substr($working, $pos, ($exitpos-$pos));
						}else{
							$str = substr($working, $offpos, ($pos-$offpos));
							$newstr .= str_replace(Array("[[jsreturn]]", "<![CDATA[", "]]>"), Array("\n", "", ""), str_replace(
								$find,
								$replace,
								$str)
							);
							$exitpos = strpos($working,"</script>", $pos);
							$newstr .= str_replace(
								$jsfind,
								$jsreplace,
								substr($working, $pos, ($exitpos-$pos))
							);
						}
						$working = substr($working, $exitpos);
					}
					$working = str_replace(
						$find,
						$replace,
						$working
					);
					$newstr .= $working;
					if($this->real_script=="-/-404.php"){
						$find		= "</body>";
						$replace	= "<!--1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890-->\n</body>";
						print str_replace($find,$replace,$newstr);
					} else {
						print $newstr;
					}
				}else {
					if($this->real_script=="-/-404.php"){
						print str_replace(Array("[[jsreturn]]", "<![CDATA[", "]]>", "</body>"), 
						Array("\n", "", "", "<!--1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890-->\n</body>"), str_replace(
							$find,
							$replace,
							$extract)
						);
					} else {
						print str_replace(Array("[[jsreturn]]", "<![CDATA[", "]]>" ), Array("\n", "", ""), str_replace($find, $replace, $extract));
					}
				}
				print $rest;
			} else {
				$find = Array("[[euro]]", "[[copy]]", "<\\/APPLET>", "&","&amp;&", "&amp;amp;", "[[script]]", "amp;quot;","amp;#", "&#39;", "[[apos]]","[[pos]]","[[amp]]", $this->base."about:blank", "<div></div>", "<div id=\"webobject\"></div>","<hr>","%2C", "".$this->base."http","</li>\n", "&#160;&#187;&#160;", "\";", "  ","<![CDATA[", "]]>", "[[gbp]]", "[[eur]]", "[[usd]]");
				$replace= Array("€", "&#164;", "</APPLET>", "&amp;", "&amp;", "&amp;", "http".$ssl."://".$this->domain.$this->base.$this->script, "quot;", "#", "'", "&#39;", "&#39;", "&", "about:blank", "", "", "<hr alt='line'>", ",", "http", "</li>", " &#187; ", "\"", " ","", "","£","€","\$");
				if($this->real_script=="-/-404.php"){
					$find[count($find)] = "</body>";
					$replace[count($replace)] = "<!--1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890-->\n</body>";
				}
				print str_replace($find,$replace,$extract);
			}
		} else {
			if($this->real_script=="-/-404.php"){
			$o = str_replace("</body>", "<!--
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
1234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890
-->
</body>",$o);
			}
			print "$o";
		}
		if ($this->check_parameters($_SESSION,"displaymode")=="printerfriendly"){
			$_SESSION["displaymode"]="";
		}
		
		if ((strlen($debug)>0) || (strlen($errors)>0)){
			print "$debug$errors";
			unset($_SESSION["debug_list"]);
		}
	}
	
 	function splash($parameters){
		$confirm = $this->check_parameters($parameters,"confirm","");
		$LEI = $this->check_parameters($parameters,"LEI","");
		$list = $this->call_command("ENGINE_RETRIEVE",array("MY_WORKSPACE"));
		$out="<module name=\"splash\"><page_options><header><![CDATA[Digital Desktop]]></header><text><![CDATA[".$this->get_constant($confirm)."]]></text></page_options>";
		foreach ($list as $key=>$val){
			if (strlen($val[1])>0){
				$out .= $val[1];
			}
		}

		/* Show News on Admin Desktop for all sites portion starts (Added By Ali Imran) */		
		//$out .="</module>";

		$data_files_path=$this->call_command("ENGINE_GET_PATH",Array("DATA_FILES_DIR"));
		while (substr ($data_files_path, -1) != '/'){ 
	  		$data_files_path = substr( $data_files_path, 0, -1);
		}
		$file_to_use	= $data_files_path."libertas/newsadmin_libertas.xml";
		if (file_exists($file_to_use) && $myxml=simplexml_load_file($file_to_use)){
			$out .= "<module name=\"NewsTitle\" label=\"".LOCALE_NEWSADMIN."\" display=\"newstitles\">";
			foreach($myxml as $news_data){
				$out .= "<text><![CDATA[ <a href='admin/index.php?command=NEWSADMIN_VIEW&identifier=$news_data->uniqueid'>".$news_data->news_title."</a>]]></text>";
			}
			$out .= "</module>";
		}
		$out .="</module>";
		/* Show News on Admin Desktop for all sites portion ends (Added By Ali Imran) */		

		return $out;
	}
	
	
	function check_config(){
		$ok=-1;
		$v = substr(strtotime(date("Y/m/d H",$this->server[DATE_EXPIRES]).":00:00"),0,10);
		$n = strtotime($this->libertasGetDate("Y/m/d H:i:s"));
		if ((($n<$v) || ($this->server[DATE_EXPIRES]==UNLIMITED)) && (md5($this->check_parameters($_SERVER,"SERVER_ADDR",$this->check_parameters($_SERVER,"LOCAL_ADDR"))) && (($this->server[LICENCE_TYPE]==ECMS)||($this->server[LICENCE_TYPE]==MECM)||($this->server[LICENCE_TYPE]==SITE_WIZARD)))){
			$ok=1;
		}
		return $ok;
	}
	function getfile($fn){
		$fp = file($fn);
		return join("",$fp);
	}
	
	function site_restore($parameters){
		$blank  = $this->check_parameters($parameters,"blank",0);
		if($blank==1){
			$_SESSION["ENGINE_RESTORE_INDEX"]=-1;
		}
		$mods = Array();
		$list = $this->check_parameters($_SESSION,"RECACHE");
		if($list!=""){
//			print $list;
//			$this->exitprogram();
		}
		$index	= $this->check_parameters($_SESSION,"ENGINE_RESTORE_INDEX",-1);
		if ($index==-1){
			$index = 0;
			$_SESSION["ENGINE_RESTORE_INDEX"]=0;
		}
		if ($index==0){
			$this->restore_special_pages($parameters);
		}
		$length	= count($this->parent->modules) - 1;
		if ($length >= $index){
			$counter=0;
			if($list!=""){
				$sql = "select distinct display_command from display_data where display_menu in ($list -1) and display_client = $this->client_identifier";
				$result  = $this->parent->db_pointer->database_query($sql);
                while($r = $this->parent->db_pointer->database_fetch_array($result)){
					$starter = substr($r["display_command"],0,strpos($r["display_command"],"_"));
                	$mods[count($mods)] = $starter."_";
                }
                $this->parent->db_pointer->database_free_result($result);
			}
			foreach($this->parent->modules as $key => $moduleEntry){
				if ($counter>=$index){
					if($list!=""){
						$_SESSION["ENGINE_RESTORE_INDEX"]++;
						if(in_array($this->parent->modules[$key]["tag"],$mods)){
							$d = $this->call_command($this->parent->modules[$key]["tag"]."RESTORE");
							if ($d==""){
								$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=ENGINE_RESTORE"));
							} else {
							}
						}
					} else {
						if ($counter == $index){
							$_SESSION["ENGINE_RESTORE_INDEX"]++;
							$d = $this->call_command($this->parent->modules[$key]["tag"]."RESTORE");
							if ($d==""){
								$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=ENGINE_RESTORE"));
							} else {
							}
						}
					}
				}
				$counter++;
			}
			$_SESSION["ENGINE_RESTORE_INDEX"]	= -1;

			$_SESSION["RECACHE"]				= "";
			if ($this->check_parameters($_SESSION,"ENGINE_RESTORE_REDIRECT","__NOT_FOUND__")=="__NOT_FOUND__"){
				$out="
					<module name=\"splash\" >
						<text><![CDATA[Thankyou the system has been restored.]]></text>
					</module>";
				return $out;
			} else {
				$dst = $this->check_parameters($_SESSION,"ENGINE_RESTORE_REDIRECT","command=LAYOUT_LIST_MENU");
				unset($_SESSION["ENGINE_RESTORE_REDIRECT"]);
				$this->call_command("ENGINE_REFRESH_BUFFER",Array($dst));
			}
		} else {
			$_SESSION["ENGINE_RESTORE_INDEX"]	= -1;
			$_SESSION["RECACHE"]				= "";
			// Thankyou screen required
			if ($this->check_parameters($_SESSION,"ENGINE_RESTORE_REDIRECT","__NOT_FOUND__")=="__NOT_FOUND__"){
				$out="
					<module name=\"splash\" >
						<text><![CDATA[Thankyou the system has been restored.]]></text>
					</module>";
				return $out;
			} else {
				$dst = $this->check_parameters($_SESSION,"ENGINE_RESTORE_REDIRECT","command=LAYOUT_LIST_MENU");
				unset($_SESSION["ENGINE_RESTORE_REDIRECT"]);
				$this->call_command("ENGINE_REFRESH_BUFFER",Array($dst));
			}
		}
	}
	
	function fix_A($str){
		$str = join("amp",split('amp;amp',$str));
		$str = $this->recurse_longdesc($str);
		return $str;
	}
	
	function recurse_longdesc($str){
		$loc = strpos(strtolower($str),"longdesc=");
		if ($loc===false){
			return $str;
		} else {
			$found=true;
			$newstr = substr($str,0,$loc+9);
			$rest = substr($str,$loc+9);
			$question_mark = strpos($rest,"?");
			if ($question_mark===false){
				return $str;
			} else {
				$check = substr($rest,0,$question_mark);
				$quote="";
				if(strlen($check)>0){
					$chars = preg_split('//', $check, -1, PREG_SPLIT_NO_EMPTY); 
					if ($chars[0] == '"'){
						$quote = '"';
					} else if ($chars[0] == "'"){
						$quote = "'";
					} else {
						$quote = "";
					}
				}
				if($this->module_type=="website"){
					$fixer = substr($rest,$question_mark + strlen("command=FILES_INFO&a"));
					return $newstr.$quote.$this->base."-/-file-download.php?".$this->recurse_longdesc($fixer);
				} else {
					$fixer = substr($rest,$question_mark);
					return $newstr.$quote.$this->script.$this->recurse_longdesc($fixer);
				}
			}
		}
		
	}
	function fix_uri($str,$parameters){
		$str = str_replace(
			Array(
				"      ",
				"longdesc= ",
				"href=\"/admin/index.php?command=FILES_INFO",
				"longdesc=\"?",
				'action="',
				"href='",
				'href="',
				'src="',	
				'href="'.$this->base."".$this->base,
				'src="'.$this->base."".$this->base,
				'href="'.$this->base."http",
				'src="'.$this->base."http",
				'href="'.$this->base."javascript",
				'href="'.$this->base."mailto",
				'href="'.$this->base."ftp",
				'href="'.$this->base."/",
				'src="'.$this->base."/",
				'action="'.$this->base."/",
				'http:/',
				'http:///',
				"href=\"/http"
			),
			Array(
				"",
				"longdesc=",
				"href=\"?command=FILES_INFO",
				"longdesc=\"".$this->base.$this->script."?",
				"action=\"".$this->base,
				"href='".$this->base,
				"href=\"".$this->base,
				"src=\"".$this->base,
				"href=\"".$this->base,
				"src=\"".$this->base,
				"href=\"http",
				"src=\"http",
				"href=\"javascript",
				"href=\"mailto",
				"href=\"ftp",
				"href=\"/",
				'src="/',
				'action="/',
				"http://",
				"http://",
				"href=\"http"

			),
			$str
		);

		$x1 = strpos($str,"<body");
		$x2 = strpos($str,"</body");
		$start_of_body	= substr($str,0,$x1);
		$end_of_body	= substr($str,$x2);
		$content		= substr($str,$x1,$x2-$x1);
		return $start_of_body.$this->fix_all_session_uris($this->fix_all_session_uris($content,'href',$parameters),'longdesc').$end_of_body;
	}

	function fix_javascript($str, $point = 0){
		return $str;
		$lstr = strtolower($str);
		$pos = strpos($lstr,'<script',$point);
		if ($pos === false){
			$str = str_replace(Array("//&lt;![CDATA[", "//]]&gt;"), Array("", ""), $str);
			return $str;
		}else{
			$start = strpos($lstr,'>',$pos)+1;
			$end = strpos($lstr,'</script>',$start);
			$script_info = substr($str, $start, $end-$start);
			$s = join(">",split("&gt;",join("<",split("&lt;",$script_info))));
			$string = substr($str,0,$start).$s.substr($str,$end);
			return $this->fix_javascript($string,$end+10);
		}
	}

	/*
		fix href and longDesc links to contain the PHPSESSID attribute.
	*/
	function fix_all_session_uris($html,$attribute,$parameters=Array()){
		$add_textonly 			= 0;
		$add_printerfriendly	= 0;
/*		if ($attribute=="href" && $this->check_parameters($parameters,"displaymode")=="textonly"){
			$add_textonly =1;
		}
		if ($attribute=="href" && $this->check_parameters($parameters,"displaymode")=="printerfriendly"){
			$add_printerfriendly =1;
		}
*/
		$search_attribute=$attribute.'="';
		if (strlen($html)>0){
			$start = strpos($html,$search_attribute);
		} else {
			$start = false;
		}
		$mode="";
		if ($start){
			$end = strpos($html,'"',$start+strlen($attribute)+3);
			$searchstr = substr($html,$start,$end-$start);
			$first = substr($html,0,$start);
			$second = substr($html,$end);
//			print "<p>[".strpos($searchstr,$this->session_parameter)."] [$this->session_parameter]::[$searchstr]</p>";
				if (strpos($searchstr,$this->base)>0){
					if ($this->module_display_mode != ""){
						if (strpos($searchstr,"displaymode=")===false){
							if ($this->module_display_mode != "pda"){
							$mode="&amp;displaymode=".$this->module_display_mode."&amp;";
							$mode='';
							}
						}
					}
					if (strpos($searchstr,'?')){
						if (strpos($searchstr,'javascript:') || strpos($searchstr,'mailto:') || strpos($searchstr,'ftp:')){
						} else if ($hash = strpos($searchstr,'#')){
							$before = substr($searchstr,0,$hash);
							$after = substr($searchstr,$hash);
							if ($this->session_parameter!="" && strpos($searchstr,$this->session_parameter)===false){
								$searchstr = $before."&".$this->session_parameter.$mode.$after;
							} else {
								$searchstr = $before.$mode.$after;
							}
						} else {
							if ($this->session_parameter!="" && strpos($searchstr,$this->session_parameter)===false){
								$searchstr .= "&amp;".$this->session_parameter.$mode;
							} else {
								$searchstr .= $mode;
							}
							
						}
					} else  {
						if (strpos($searchstr,'javascript:') || strpos($searchstr,'mailto:') || strpos($searchstr,'ftp:')){
						} else if ($hash = strpos($searchstr,'#')){
							$before = substr($searchstr,0,$hash);
							$after = substr($searchstr,$hash);
//							print "[".$this->session_parameter.$mode.$after."]";
							if ("?".$this->session_parameter.$mode.$after=="?$after"){
								$searchstr = $before.$after;
							} else {
								$searchstr = $before."?".$this->session_parameter.$mode.$after;
							}
						} else {
							if ("?".$this->session_parameter.$mode=="?"){
								$searchstr .= "";
							} else {
								$searchstr .= "?".$this->session_parameter.$mode;
							}
							
						}
	//					print "<p><strong>[$searchstr]</strong></p>";
//						print "[$searchstr]";
					}
				}
			return $first.$searchstr.$this->fix_all_session_uris($second,$attribute,$parameters);
		} else {
			return $html;

		}
	}

	function fix_all_session_forms($html){
		if (strlen($html)>0){
			$start = strpos($html,"<form");
		} else {
			$start = false;
		}
		if ($start){
			$end = strpos($html,'>',$start);
			$first = substr($html,0,$end+1);
			$second = substr($html,$end+1);
			if ($this->session_parameter!="" ){
				return $first."<input type='hidden' name='".session_name()."' value='".session_id()."'/>".$this->fix_all_session_forms($second);
			} else {
				return $first.$this->fix_all_session_forms($second);
			}
		} else {
			return $html;
		}
	}

	function refresh($parameter_list){
		$url  = $this->split_me($this->check_parameters($parameter_list,"url"),"&amp;","&");
		if($this->check_parameters($_COOKIE,"LEI","__NOT_FOUND__")!="__NOT_FOUND__"){
			$this->session_parameter = "";
		} else {
			$this->session_parameter = "LEI=" . session_id();
		}
		
		if ($url==""){
			$this->page_output 		= "";
			$this->page_parameters	= $this->split_me($this->check_parameters($parameter_list,0),"&amp;","&");
			$this->page_clear		= 1;
			$s = "s";
			if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!="on") {
				$s = "";
			}
			if (strpos($this->page_parameters,$this->base)===false){
				$url = $this->base."".$this->real_script."?";
			}else {
				$url = "";
			}
			if($_SERVER["SERVER_PORT"]!="80"){
				if ($_SERVER["SERVER_PORT"]!="443"){
					$loc = "http".$s."://".$this->domain.":".$_SERVER["SERVER_PORT"]."".$url;
				} else {
					$loc = "http".$s."://".$this->domain."".$url;
				}
				if (strlen($this->page_parameters)>0){
					$loc .= $this->split_me($this->page_parameters,"&amp;","&")."&".$this->session_parameter;
				} else {
					$loc .= $this->session_parameter;
				}
			}else{
				$loc = "http".$s."://".$this->domain."".$url;
				if (strlen($this->page_parameters)>0){
					$loc .= $this->split_me($this->page_parameters,"&amp;","&")."&".$this->session_parameter;
				} else {
					$loc .= $this->session_parameter;
				}
			}
		} else {
			$loc = $url;
		}
		$_SESSION["debug_list"] = $this->call_command("UTILS_DISPLAY_DEBUG");
//@ob_end_clean();
//			ob_implicit_flush();
		header ("GET $loc HTTP\/1.1");
		header ("Host: ".$this->parent->domain);
		header ("Connection: close");
		header ("User-Agent: Libertas Engine Redirect v$this->module_version");
		header ("Allow: GET, HEAD, PUT");
		header ("Location: $loc");
		$this->exitprogram();
	}
	
	function checkBot($str,$parameters){
		//system requires all javascript to be fixed. &lt; becomes < and &gt; becomes >  effectivly fixes most javascript.
//		return $this->fix_javascript($str,0);
		$ok =false;
		if ((
			(session_id()==md5("SearchBot - Google")) || 
			(session_id()==md5("SearchBot - Crawler")) || 
			(session_id()==md5("SearchBot - Walker")) || 
			(session_id()==md5("SearchBot - Scrub")) || 
			(session_id()==md5("SearchBot - Internetseer.com")) || 
			(session_id()==md5("SearchBot - Scooter")) || 
			(session_id()==md5("SearchBot - Spider")) || 
			(session_id()==md5("SearchBot - Slurp")) || 
			(session_id()==md5("SearchBot - IA Archiver")) ||
			(session_id()==md5("SearchBot"))
			) || 
			(session_id()=="")
		){
//			print "here {{{{".session_id()."}}}}";
//			print ini_get("session.use_trans_sid");
			$ok =0;
			$this->session_parameter="";
		} else {
			$ok = 1;
			if($this->check_parameters($_COOKIE,"LEI","__NOT_FOUND__")!="__NOT_FOUND__"){
				$this->session_parameter="";
			}

		}
//		print "[Not a Bot $ok, SESSION :$this->session_parameter]";
		if($ok==1){
			return $this->fix_all_session_forms($this->fix_uri($this->fix_javascript($str,0),$parameters));
		} else {
			return $this->fix_uri($this->fix_javascript($str,0),$parameters);
		}
	}
	
	function check_editor_requirement($str){
		if (strpos($str,"RICH-TEXT")===false){
		}else{
			$editors = array();
			foreach($this->parent->modules as $index => $module_entry){
			//for($index=0,$max_length=count($this->parent->modules);$index<$max_length;$index++){
				if ($this->parent->modules[$index]["module"]!=null){
					if (count($this->parent->modules[$index]["module"]->editor_configurations)>0){
						$editors[count($editors)] = $this->parent->modules[$index]["module"]->editor_configurations;
					}
				}
			}
			$str .= $this->call_command("EDITOR_LOAD_CACHE",Array("editors"=> $editors));
		}
		return $str;
	}
	function accesskeys($parameters){
		$out  ="<module name=\"".$this->module_name."\" display=\"accesskey\">";
		$this->qstr =$this->check_parameters($_SERVER,"QUERY_STRING");
		
		$find 		= Array ("'".session_name()."=".session_id()."'","'&$'si","'&&'si");
		$replace 	= Array ('','','');
		
		$this->qstr = preg_replace (
			$find,
			$replace,
			$this->qstr);
		$accesskey = preg_replace (
			Array("'command=ENGINE_ACCESSKEYS'si"),
			Array(''),
			$this->qstr);
		if (strpos($this->qstr,"displaymode=")===false){
			$togglekey =$this->qstr . "&amp;displaymode=textonly";
		} else {
			$togglekey = preg_replace (
				Array("'displaymode=textonly'si","'displaymode=printerfriendly'si"),
				Array('displaymode=','displaymode='),
				$this->qstr
			);
		}
		$defined_list = Array(
			Array("0", "Access Key Definition", "1", "-access-key-defintion.php"	,"Access key definitions"),
			Array("1", "Home Page", 			"1", "index.php"					,"Home Page"),
			Array("2", "Whats New", 			"1", "-whats-new.php"				,"Whats new"),
			Array("3", "Site Map", 				"1", "-site-map.php"				,"Site Map"),
			Array("9", "Feedback Form", 		"1", "-feedback-form.php"			,"Site Map"),
			Array("m", "Toggle Text only mode", "1", "-/-toggle-text-only-mode.php"	,"Text Only"),
			Array("p", "Toggle Printer Friendly mode", "1", "-/-toggle-printer-friendly-mode.php","Printer Friendly"),
			Array("s", "Skip to Content", 		"1", $this->script."?".$this->qstr."#content","Skip Content"),
			Array("-", "Reduce Font size", 		"1", "-/-font-size-smallest.php","Font size Smallest"),
			Array("=", "Increase Font size", 	"1", "-/-font-size-largest.php","Font size Largest")
		);
		$out .= "<text><![CDATA[<h1 class='entrylocation'><span>".LOCALE_ENGINE_ACCESSKEY_DEFINTION."</span></h1>]]></text>";
/*		$out .= "<access_list>";
		$max = count($defined_list);
		for ($i=0; $i<$max; $i++){
			$out .= "<accesskey letter='" . $defined_list[$i][0] . "' type='".$defined_list[$i][2] . "'>\n";
			$out .= "	<label><![CDATA[".$defined_list[$i][1] . "]]></label>\n";
			$out .= "	<title><![CDATA[".$defined_list[$i][4] . "]]></title>\n";
			$out .= "	<url><![CDATA[".$defined_list[$i][3] . "]]></url>\n";
			$out .= "</accesskey>\n";
		}
		$out .= "</access_list>";*/
		$out .= "<text><![CDATA[<div class='contentpos'><p>We believe it is important that everyone should have access to information on the Internet and with this in mind our website has been designed to provide accessibility for people with disabilities.</p> <h2>Accessible Code</h2> <p>Each page on this website meets the industry standard <acronym title=\"Web Accessability Initiative\">WAI</acronym> guidelines, meeting <acronym title=\"World Wide Web Consortium\">W3C</acronym>, requirements for accessibility for people with disabilities.</p> <p>Alternative text and long descriptions are available for each image on this site, and each link has meaningful text to aid navigation. Any tables used in the construction of this website have a table summary, scope and where appropriate, a caption. Acronyms and abbreviations have been correctly labeled to ensure screen reader compatibility in line with the W3C guidelines and all pages have been validated in <acronym title=\"Extensible Hyper Text Markup Language\">XHTML</acronym> and <acronym title=\"Cascading StyleSheet\">CSS</acronym>.</p> <p>Text only version of the website</p> <p>As an extra aid to visitors with dyslexia or visual impairments, a separate <a title=\"Toggle between the graphicial and text only versions of this site\" href=\"-/-toggle-text-only-mode.php\">Text only version</a> exists of every page on this site. In addition to clicking on the <a title=\"Toggle between the graphicial and text only versions of this site\" href=\"-/-toggle-text-only-mode.php\">Text Only</a> link this can be accessed by holding down the <strong>Alt</strong> key and the <strong>M</strong> key together, then pressing the <strong>Enter</strong> key.</p> <p>Alt text, normally available on an image as a small yellow box with writing within it when the mouse pointed is moved over it, is available as a link to the \"long description\" of the image; offering a more detailed explanation of the image for the blind who are otherwise unable to see it.</p> <p> </p> <h2>Access Keys</h2> <p>The following access keys are defined to help visitors who have difficulty using a mouse to navigate site:</p> <table cellspacing=\"1\" cellpadding=\"0\" border=\"0\"><tbody><tr><td><p class=\"aligncenter\"><strong>Access Key</strong></p></td><td><p class=\"aligncenter\"><strong>Page</strong></p></td></tr><tr><td><p>1</p></td><td><p><a title=\"Libertas Solutions home page\" href=\"/index.php\">Home page</a></p></td></tr><tr><td><p>2</p></td><td><p><a title=\"List of the 10 newest items updated on this site\" href=\"-whats-new.php\">What's new</a></p></td></tr><tr><td><p>3</p></td><td><p><a title=\"Map of all the various areas within this site\" href=\"-site-map.php\">Sitemap</a></p></td></tr><tr><td><p>4</p></td><td><p><a title=\"Use site search to find what you are looking for\" href=\"-search.php\">Search</a></p></td></tr><tr><td><p>9</p></td><td><p><a title=\"Contact Libertas Solutions with your comments and feedback\" href=\"-/-feedback-form.php\">Feedback</a></p></td></tr><tr><td><p>0</p></td><td><p><a title=\"Details of access keys used on this site (this page)\" href=\"-access-key-defintion.php\">Accesskey details</a></p></td></tr><tr><td><p>-</p></td><td><p><a title=\"Reduce the size of font used throughout this site\" href=\"-/-reduce-font.php\">Reduce font size</a></p></td></tr><tr><td><p>+</p></td><td><p><a title=\"Increase the size of font used throughout this site\" href=\"-/-increase-font.php\">Increase font size</a></p></td></tr><tr><td><p>m</p></td><td><p><a title=\"Toggle between the graphicial and text only versions of this site\" href=\"-/-toggle-text-only-mode.php\">Text Only</a></p></td></tr><tr><td><p>S</p></td><td><p><a title=\"Skip directly to content\" href=\"#page1\">Skip directly to content</a></p></td></tr><tr><td><p>P</p></td><td><p><a title=\"Load printer friendly version of site\" href=\"-/-toggle-printer-friendly-mode.php\">Printer friendly version</a></p></td></tr></tbody> </table>
".LOCALE_ENGINE_ACCESSKEY_DEFINTION_BROWSERS."<h2>Additional Information</h2> <p>For additional information on the technical features included to increase the accessibility of this website please refer to <a href=\"http://www.libertas-solutions.com/solutions/web-accessibility/\">www.libertas-solutions.com/solutions/web-accessibility/</a> </p></div>]]></text>";
		$out .="</module>";
		return $out;
		
	}
	function regen_menus(){
 		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$file1_to_use=$data_files."\\layout_".$this->client_identifier."_admin.xml";
		if (file_exists($file1_to_use)){
			$um = umask(0);
			@chmod($file1_to_use,LS__FILE_PERMISSION);
			umask($um);
			@unlink($file1_to_use);
		}
		$file2_to_use=$data_files."\\layout_".$this->client_identifier."_anonymous.xml";
		if (file_exists($file2_to_use)){
			$um = umask(0);
			@chmod($file2_to_use,LS__FILE_PERMISSION);
			umask($um);
			@unlink($file2_to_use);
		}
		$file3_to_use=$data_files."\\layout_".$this->client_identifier."_restricted.xml";
		if (file_exists($file3_to_use)){
			$um = umask(0);
			@chmod($file3_to_use,LS__FILE_PERMISSION);
			umask($um);
			@unlink($file3_to_use);
		}
		$f1 = (file_exists($file1_to_use))? "Exists" : "Removed";
		$f2 = (file_exists($file2_to_use))? "Exists" : "Removed";
		$f3 = (file_exists($file3_to_use))? "Exists" : "Removed";
		$this->get_admin_menus(Array("recache" => 1));
	}
	/*************************************************************************************************************************
    * restore special pages
    *************************************************************************************************************************/
	function restore_special_pages($parameters){
		$root = $this->parent->site_directories["ROOT"];
		$module_dir = $this->parent->site_directories["MODULE_DIR"];
		$length_of_special_pages = count($this->specialPages);
		//create each of these pages on an engine Restore
		$um = umask(0);
		@mkdir($root."/-/",LS__DIR_PERMISSION);
		umask($um);
		for($i=0;$i<$length_of_special_pages;$i++){
			$page= "<"."?php
\$script	=\"index.php\";
\$mode 		=\"EXECUTE\";
\$command	=\"".$this->specialPages[$i][1]."\";
\$fake_title=\"".$this->specialPages[$i][3]."\"; \r\n";
				if ($this->specialPages[$i][2]=="VISIBLE"){
					$page.= "require_once \"admin/include.php\"; \r\n";
				} else {
					$page.= "require_once \"../admin/include.php\"; \r\n";
				}
				$page.= "require_once \"$module_dir/included_page.php\"; 
?".">";
			if ($this->specialPages[$i][2]=="VISIBLE"){
				$file = $root."/".$this->specialPages[$i][0];
			} else {
				$file = $root."/-/".$this->specialPages[$i][0];
			}
//			print "<li>".__FILE__."@".__LINE__."<p>$file</p></li>";
			$fp = fopen($file,"w");
			fwrite($fp, $page);
			fclose($fp);
			$um = umask(0);
			@chmod($file, LS__FILE_PERMISSION);
			umask($um);
		}
		/*************************************************************************************************************************
        * build any forms that are required
        *************************************************************************************************************************/
		$extra = $this->call_command("ENGINE_RETRIEVE", array("SPECIAL_PAGES"));
		foreach ($extra as $key=>$val){
			$entry = $this->check_parameters($val,1,"");
			if ($entry !=""){
				for($i=0;$i<count($entry);$i++){
					$page= "<"."?php
\$script	=\"index.php\";
\$mode 		=\"EXECUTE\";
\$command	=\"".$entry[$i][1]."\";
\$fake_title=\"".$entry[$i][3]."\";\r\n";
					if ($entry[$i][2]=="VISIBLE"){
						$page.= "require_once \"admin/include.php\"; \r\n";
						$file = $root."/".$entry[$i][0];
					} else {
						$page.= "require_once \"../admin/include.php\"; \r\n";
						$file = $root."/-/".$entry[$i][0];
					}
					$page.= "require_once \"$module_dir/included_page.php\"; 
?".">";
					$fp = fopen($file,"w");
					fwrite($fp, $page);
					fclose($fp);
					$um = umask(0);
					@chmod($root."/".$entry[$i][0], LS__FILE_PERMISSION);
					umask($um);
				}
			}
		}
		/*************************************************************************************************************************
        * if the formbuilder exists then build override any forms that require to be overwritten
        *************************************************************************************************************************/
		$this->call_command("FORMBUILDERADMIN_BUILDFORMS");

	}
	function refresh_referal(){
		$referer = $this->check_parameters($_SERVER,"HTTP_REFERER","__NOT_FOUND__");
//		print $referer;
		if ($referer == "__NOT_FOUND__"){
			$this->refresh(Array("url" => $this->base."index.php"));
		} else {
			if(strpos($referer,$this->domain)===false){
				$this->refresh(Array("url" => $this->base."index.php"));
			} else {
				$pos = strpos($referer,$this->domain) + strlen($this->domain.$this->base);
				$referal_url = substr($referer, $pos);
				if ($referal_url == "" || $referal_url == "-toggle-text-only-mode.php"){
					$referal_url = "index.php";
				}
				$this->refresh(Array("url" => $this->base.$referal_url));
			}
		}
		$this->exitprogram();
	}
	
	function engine_default_contact_us($parameters){
		$_SESSION["referer"] = $this->check_parameters($_SESSION,"referer",$this->check_parameters($_SERVER,"HTTP_REFERER","__NOT_FOUND__"));
		$your_email		= $this->check_parameters($parameters,"your_email");
		$subject 		= $this->check_parameters($parameters,"your_subject");
		$msg 			= $this->check_parameters($parameters,"your_content");
		$tparam = $this->check_parameters($_SESSION,"to_param");
		if ($tparam!=""){
			$list = split("&",$tparam);
			for ($i=0;$i<count($list);$i++){
				list($key,$val) = split("=",$list[$i]);
				if ($key=="subject"){
					$subject = $val;
				}
			}
		}
		$error = $this->check_parameters($parameters,"error");
		$label = LOCALE_CONTACT_US;
		$to_email		= $this->check_parameters($_SESSION,"to_email");
		if($to_email!=""){
			$label="Email $to_email";
		}
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<form name='standard_contact_us' label='".$label."' action='-/-feedback-form-confirm.php' method='post'>";
		if($error=="email"){
			$out.="<text type='error'><![CDATA[Please supply a valid email address]]></text>";
		}
		$out .= "<input type='text' name='your_email' label='".LOCALE_YOUR_EMAIL."' size='40' value='$your_email'/>
			<input type='text' name='your_subject' label='".LOCALE_SUBJECT."' size='40' value='$subject'/>
			<textarea name='your_content' label='".LOCALE_MESSAGE."' size='40' height='8'><![CDATA[$msg]]></textarea>
			<input type='submit' iconify='SAVE' value='".SAVE_DATA."'/>
		</form>";
		$out .="</module>";
		return $out;
	}
	
	function engine_default_contact_us_confirm($parameters){
		$settings = $this->call_command("SFORM_LOAD_PREFS");
		$default_email	 = $this->check_parameters($settings,"sp_from_email");
//		$default_email	= $this->check_prefs(Array("sp_from_email"));
		if ($default_email==""){
			$default_email = "info@".$this->parseDomain($this->parent->domain);
		}
		$to_email		= $this->check_parameters($_SESSION,"to_email");
		if ($to_email==""){
			$to_email=$default_email;
		}
		$your_email		= $this->check_parameters($parameters,"your_email");
		$subject 		= $this->check_parameters($parameters,"your_subject");
		$msg 			= $this->check_parameters($parameters,"your_content");
		$email_ok = $this->check_email_address($your_email);
		if (!$email_ok){
			$parameters["error"] = "email";
			$out = $this->engine_default_contact_us($parameters);
		} else {
			if ($this->check_parameters($_SESSION,"referer","__NOT_FOUND__")=="__NOT_FOUND__"){
				$url="http://".$this->parent->domain.$this->parent->base."index.php";
				$linktext="Home";
			} else {
				$url=$_SESSION["referer"];
				$linktext=$_SESSION["referer"];
			}
			$out  ="<module name=\"".$this->module_name."\" display=\"TEXT\">";
			$out .="<text><![CDATA[<h1>Your message has been sent </h1>
					<p>Please click the following link to return to the page you were originally on <br>
					<ul>
						<li><a href='".$url."'>".$linktext."</a></li>
					</ul>
					</p>]]></text>";
			$out .="</module>";
			if($your_email==""){
				$your_email=$default_email;
			}
			$msg_extra = "";
			if ($this->check_parameters($settings,"sp_sform_show_country","No")=="Yes"){
				$msg_extra .= "\r\nCountry : ".$this->check_parameters($_SESSION,"SESSION_COUNTRY","NA");
			}
			if ($this->check_parameters($settings,"sp_sform_show_language","No")=="Yes"){
				$msg_extra .= "\r\nLanguage : ".$this->check_parameters($_SESSION,"SESSION_LANGUAGE","NA");
			}
			if ($this->check_parameters($settings,"sp_sform_show_tracer","No")=="Yes"){
				$msg_extra .= "\r\nLog : http://".$this->domain.$this->base."admin/index.php?command=USERACCESS_TRACE_SESSION&identifier=".$_SESSION["SESSION_USER_ACCESS_IDENTIFIER"];
			}
			if ($this->check_parameters($settings,"sp_sform_show_source","No")=="Yes"){
				$msg_extra .= "\r\nSource : ".$this->check_parameters($_SESSION,"SESSION_SOURCE","NA");
			}
			if ($this->check_parameters($settings,"sp_sform_show_referer","No")=="Yes"){
				$msg_extra .= "\r\nReferer : ".$url;
			}
			if ($msg_extra!=""){
				$msg_extra = "\r\n\r\n-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\r\n".$msg_extra;
			}
			
			$this->call_command("EMAIL_QUICK_SEND",
				Array(
					"from"		=> $your_email,
					"subject"	=> $subject,
					"body"		=> $msg.$msg_extra,
					"to"		=> $to_email
				)
			);
			unset($_SESSION["referer"]);
			unset($_SESSION["to_email"]);
		}
		return $out;
	}
	
	function antispam($parameters){
		if($this->check_parameters($_SESSION,"IS_BOT",0)==0){
			$referer		= $this->check_parameters($_SERVER,"HTTP_REFERER","__NOT_FOUND__");
			$_SESSION["antispam"] = $referer;
			if ($referer=="__NOT_FOUND__" || (strpos($referer, $this->parent->domain)===false)){
				$out="";
				$this->refresh(Array("url" => $this->base."index.php"));
			}else{
				$to 					= $this->check_parameters($parameters,"to");
				$_SESSION["to_email"]	= $this->check_parameters($parameters,"to");
				$your_email				= $this->check_parameters($parameters,"your_email");
				$subject 				= $this->check_parameters($parameters,"your_subject");
				$msg 					= $this->check_parameters($parameters,"your_content");
				if (strpos($to, "?")===false){
					$to_email 	= $to;
					$tparam		= "";
				} else {
					list($to_email, $tparam) = split("\?", $to);
					if ($tparam!=""){
						$list = split("&",$tparam);
						for ($i=0;$i<count($list);$i++){
							list($key,$val) = split("=",$list[$i]);
							if ($key=="subject"){
								$subject = $val;
							}
						}
					}
				}
				$error = $this->check_parameters($parameters,"error");
				$label = LOCALE_CONTACT_US;
				if($to_email!=""){
					$label="Email ".str_replace("'","&amp;#39;",stripslashes($to_email));
				}
				$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
				$out .= "<form name='anti-spam-form' action='-/-anti-spam-confirm.php' method='post'>";
				if($error=="email"){
					$out.="<text type='error'><![CDATA[Please supply a valid email address]]></text>";
				}
/*
				return "<setting name='site_updated'><![CDATA[".$this->libertasGetDate("jS F Y",strtotime($site_updated2))."]]></setting>";
				$out.="<text>".$label."</text>";
*/
				$out .="<text><![CDATA[<br><br><h4>".$label."</h4>]]></text>";
				
				$out .= "<input type='text' name='your_email' label='".LOCALE_YOUR_EMAIL."' size='40'><![CDATA[$your_email]]></input>
						<input type='text' name='your_subject' label='".LOCALE_SUBJECT."' size='40'><![CDATA[$subject]]></input>
						<textarea name='your_content' label='".LOCALE_MESSAGE."' size='40' height='8'><![CDATA[$msg]]></textarea>
						<input type='submit' iconify='SAVE' value='".LOCALE_SEND_MAIL."'/>
					</form>";
				$out .="</module>";
				return $out;
			}
		} else {
		}
	}
	function antispam_confirm($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,print_r($parameters,true)));}
		$referer = $this->check_parameters($_SESSION,"antispam");
		if ($referer=="__NOT_FOUND__" || (strpos($referer, $this->parent->domain)===false)){
			$out="";
			$this->refresh(Array("url" => $this->base."index.php"));
		}else{
			$settings		= $this->call_command("SFORM_LOAD_PREFS");
			$default_email	= $this->check_parameters($settings,"sp_from_email");
			if ($default_email==""){
				$default_email = "info@".$this->parseDomain($this->parent->domain);
			}
			$to_email		= $this->check_parameters($_SESSION,"to_email");
			if (strpos($to_email,"?subject")===false){
			} else {
				$spi = split("\?subject",$to_email);
				$to_email=$spi[0];
			}
			if ($to_email==""){
				$to_email=$default_email;
			}
			$your_email		= $this->check_parameters($parameters,"your_email");
			$subject 		= $this->check_parameters($parameters,"your_subject");
			$msg 			= $this->check_parameters($parameters,"your_content");
			$email_ok = $this->check_email_address($your_email);
			if (!$email_ok){
				$parameters["error"] = "email";
				$out = $this->antispam($parameters);
			} else {
				if ($this->check_parameters($_SESSION,"referer","__NOT_FOUND__")=="__NOT_FOUND__"){
					$url="http://".$this->parent->domain.$this->parent->base."index.php";
					$linktext="Home";
				} else {
					$url=$_SESSION["referer"];
					$linktext=$_SESSION["referer"];
				}
				$out  ="<module name=\"".$this->module_name."\" display=\"TEXT\">";
				$out .="<text><![CDATA[<h1>Your message has been sent </h1>]]></text>";
				$out .="</module>";
				if($your_email==""){
					$your_email=$default_email;
				}
				
				$msg_extra = "";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::your_email",__LINE__,"$your_email"));}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::to",__LINE__,"$to_email"));}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::subject",__LINE__,"$subject"));}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::msg",__LINE__,"$msg $msg_extra"));}
				$this->call_command("EMAIL_QUICK_SEND",
					Array(
						"from"		=> $your_email,
						"subject"	=> $subject,
						"body"		=> $msg.$msg_extra,
						"to"		=> $to_email
					)
				);
				unset($_SESSION["referer"]);
				unset($_SESSION["to_email"]);
			}
		}
		return $out;
	}
	
	function error_404_page($parameter_list){
		$referer = $this->check_parameters($_SERVER,"HTTP_REFERER","");
//		$referer = "http://www.libertas-solutions.com/contact-us/libertas_solutions_contact_details.php";
		$out	 = "<module name=\"".$this->module_name."\" display=\"DISPLAY\">";
//		$out	.= "<form name='standard_contact_us' label='File not found' action='-feedback-form-confirm.php' method='post'>";
		$refererlist  = split("/",$referer);
		$file = $refererlist[count($refererlist)-1];
		$tok = strtok($file, "-_\. \n\t"); 
		$title= "";
		$words = Array();
		while ($tok) { 
			$val = $tok;
			$tok = strtok("-_\. \n\t"); 
			if ($tok){
				$words[count($words)] = $val;
				if ($title!=""){
					$title .= " and ";
				}
				$title .= "trans_title like '%$val%'";
			}
		}
		$title_search = urlencode(join(" ", $words));
		$out	.= "	<text ><![CDATA[
<div class=\"page\" id=\"page1\">
<h1 class=\"entrylocation\" id=\"pageheader1\"><span >The page you tried to access was not found.</span></h1>
<div class=\"contentpos\">
<h2>Here are some possible reasons why: </h2>
<ol>

<li>You may have typed the page address incorrectly.</li> 
<li>The link you clicked, or the URL you typed into your browser is wrong. </li>
<li>It is possible that the page you were looking for may have been moved, updated or deleted.</li>
<li>We have a \"bad\" link on our site, if so please <a href=\"-/-feedback-form.php\">contact the webmaster</a> with details so it can be fixed. </li>
</ol>
<h2>You might find what you're looking for in one of these areas:</h2>
<ol>
<li><a href='-search.php' title='Search our site for what you are looking for'>Search site</a></li>
<li><a href='-site-map.php' title='Try using our site map to find the information you are looking for.'>View the site map</a></li>
<li><a href='index.php' title='Try our home page'>Visit home page</a></li> 
<li><a href='-/-feedback-form.php' title='Contact us directly if you still can not find what you are looking for'>Contact us directly</a></li> 
</ol>
</div>
</div>
]]></text>";

/*		$sql = "select * from page_trans_data where trans_title like '%$title_search%' and trans_client=$this->client_identifier";
		print "<li>$sql</li>";
		$result  = $this->parent->db_pointer->database_query($sql);
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        		
    	    }
	        $this->parent->db_pointer->database_free_result($result);
		} else {
			$sql = "select * from page_trans_data where $title and trans_client=$this->client_identifier";
			print "<li>$sql</li>";
			$result  = $this->parent->db_pointer->database_query($sql);
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				
			}
			$this->parent->db_pointer->database_free_result($result);
		}
		*/
	//	$out	.= "</form>";
		$out	.= "</module>";
		return $out;
	}
	
	function toggle_admin_access($parameters){
		$file_to_use = $this->site_directories["MODULE_DIR"]."/admin_status.php";
		if(ADMIN_OPEN==0){
			$out = "<"."?php\ndefine(\"ADMIN_OPEN\",1);\n?".">";
		}else{
			$out = "<"."?php\ndefine(\"ADMIN_OPEN\",0);\n?".">";
		}
		$fp = fopen($file_to_use,"w");
		fwrite($fp, $out);
		fclose($fp);
		$um = umask(0);
		@chmod($file_to_use, LS__FILE_PERMISSION);
		umask($um);
		if(ADMIN_OPEN==0){
			return "<module name='engine' display='form'><form name='admin_closed'><text><![CDATA[Admin Now Available]]></text></form></module>";
		} else {
			return "<module name='engine' display='form'><form name='admin_closed'><text><![CDATA[Admin Closed]]></text></form></module>";
		}
	}
	
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function illegal_access($parameters){
			return "<module name='engine' display='form'><form name='admin_closed'><text><![CDATA[Sorry, You do not have access privigiles to this content]]></text></form></module>";

	}
	
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function presentation_get_dates(){
		$sql = "select max(trans_date_modified) as site_updated from page_trans_data where trans_client=$this->client_identifier and trans_published_version=1 and trans_doc_status = 4";
		// if this has thrown up an error either the table is corrupt or you have no records.
		$result = $this->call_command("DB_QUERY",Array($sql));
		$site_updated="";
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$site_updated =$r["site_updated"];
		}
		$sql = "select max(md_date_publish) as site_updated from metadata_details 
					where md_client=$this->client_identifier";
		// print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		// if this has thrown up an error either the table is corrupt or you have no records.
		$result = $this->call_command("DB_QUERY",Array($sql));
		$site_updated2 ="";
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$site_updated2 =$r["site_updated"];
			if($site_updated2=="0000-00-00 00:00:00"){
				$site_updated2="";
			}
		}
		//print "[$site_updated, $site_updated2]";
		if ($site_updated == ""){
			if($site_updated2 ==""){
				return "<setting name='site_updated'><![CDATA[]]></setting>";
			} else {
				return "<setting name='site_updated'><![CDATA[".$this->libertasGetDate("jS F Y",strtotime($site_updated2))."]]></setting>";
			}
		} else {
			if($site_updated2 == ""){
				return "<setting name='site_updated'><![CDATA[".$this->libertasGetDate("jS F Y",strtotime($site_updated))."]]></setting>";
			} else {
				$st1 = strtotime($site_updated);
				$st2 = strtotime($site_updated2);
				if($st1>$st2){
					return "<setting name='site_updated'><![CDATA[".$this->libertasGetDate("jS F Y", $st1)."]]></setting>";
				} else {
					return "<setting name='site_updated'><![CDATA[".$this->libertasGetDate("jS F Y", $st2)."]]></setting>";
				}
			}
		}
	}

	function show_session(){
		print "<pre>";
		print_r($_SESSION);
		print "</pre>";
		$this->exitprogram();
	}
}
?>