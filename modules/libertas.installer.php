<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.installer2.php
*/
/**
* Installer Script for Libertas Solutions
*
* IMPORTANT NOTE ::
* WHEN USING ON SERVERS WERE A DIFFERENT DATABASE IS USED FOR EACH CLIENT RATHER THAN IN AN ASP MODEL
* WHICH WOULD USE A SINGLE DATABASE TO CONTAIN ALL CLIENT DATA IT IS VITAL THAT THE MODULE DIRECTORY
* IS NOT USED IN A SHARED FORMAT BUT RATHER EACH OF THESE CLIENTS HAS ITS OWN MODULES DIRECTORY AND
* THE INSTALLER SHOULD BE USED TO UPGRADE EACH IN TURN.
*/

include_once $config_directory."/settings.php";
include_once $module_directory."/libertas.module.php";

class installer extends module{
	/**
	*  Class Variables
    */
	var $session_parameter 	= "";
	var $module_name		="installer";
	var $module_creation	="09/09/2002";
	var $module_version 	= '3.0';
	var $module_debug 		= 0;
	var $product_name 		="";
	var $module_command="INSTALLER_"; // all commands specifically for this module will start with this token
	var $modules; // A list of all the modules in the system.
	var $domain;// Domain Name that this system will use
	var $connection;
	var $status=0;
	var $host="";
	var $pwd="";
	var $debug_access="";
	var $database="";
	var $db_pointer			= null;
	var $system_prefs	= Array();
	var $user="";
	var $logs="";
	var $config_updated=0;
	var $script="";
	var $module_type="install";
	var $site_directories	= Array();
	var $page_output = "";
	var $page_clear	= 0;
	var $page_parameters	= "";
	var $server= Array();
	var $base= "/";
	var $page_menu	="";
	var $breadcrumb = "";
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Modules required by this Class these modules are not loaded by the system
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-  Class Construtor
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function installer($domain,$session,$script,$type){
		$this->domain=$domain;
		$this->module_type = $type;
		$this->parent = &$this;
		$this->session_parameter = session_name() ."=". session_id();
		if(basename($script)==""){
			$script.="index.php";
		}
		if (strpos($_SERVER["PHP_SELF"],"~")===0){
			$this->base ="/";
			$this->script = substr($script,strlen($this->base));
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
		}
		$this->status = $this->engine_initalise();
		if ($this->status){
			//$this->call_command("SESSION_RETRIEVE",array($session));

		}
		return $this->status;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- command()
	- want to do anything with this module go through me simply create a condition for
	- the user command that you want to execute and hey presto I'll return the output of
	- that module
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function command($user_command, $parameter_list=array()){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- This is the main function of the Module this function will call what ever function
		- you want to call.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($user_command=="ENGINE_RESTORE"){
			return $this->site_restore();
		}
		if ($user_command=="ENGINE_RETRIEVE"){
			return $this->retrieve($this->check_parameters($parameter_list,0),$this->check_parameters($parameter_list,1));
		}
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
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."CALL_COMMAND"){
				
				$cmd = $parameter_list[0];
				$params = $parameter_list[1];
				return $this->installer_call_command($cmd,$params);
				
			}
			if ($user_command==$this->module_command."LOGIN"){
				if ($this->installer_login($parameter_list)==1){
					$user_command=$this->module_command."PAGE_TWO";
				} else {
					$user_command=$this->module_command."PAGE_ONE";
				}
			}
			if (($user_command==$this->module_command."PAGE_TWO")){
				$this->page_output .= $this->installer_page_two($params);
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
			for($index=0,$max_length=count($this->modules);$index<$max_length;$index++){
				if ($this->module_debug){
					//	$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"command",__LINE__,"[".$user_command ." ".$this->modules[$index]["tag"]."]"));
				}
				if ((strpos($user_command,$this->modules[$index]["tag"])===0) && (file_exists(dirname(__FILE__)."/".$this->modules[$index]["file"]))){
					if (is_null($this->modules[$index]["module"])){
						//print "<li>".__FILE__." ".__LINE__." loading ".$this->modules[$index]["file"];
						if (file_exists(join("/",split("\\\\",dirname(__FILE__)))."/".$this->modules[$index]["file"])){
							require_once dirname(__FILE__)."/".$this->modules[$index]["file"];
							$this->modules[$index]["module"] = new $this->modules[$index]["name"]($this,$parameter_list);
							if($index=="DB_"){
								$this->parent->db_pointer = $this->parent->modules[$index]["module"];
							}
						}
					}
					return $this->modules[$index]["module"]->command($user_command,$parameter_list);
				}
			}
		}
	}

	function load_locale(){
		$locale_path = $this->site_directories["LOCALE_FILES_DIR"];
		include_once ($locale_path."/en/locale.php");
		include_once ($locale_path."/en/locale_general.php");
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- function to retrieve a specific command from all modules
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function retrieve($command,$parameter_list){
		
		$out=array();
		for($index=0,$max_length=count($this->modules);$index<$max_length;$index++){
			//print "<li>".__FILE__." ".__LINE__." Retrieving module : ".$this->modules[$index]["name"];
			if ($this->modules[$index]["module"]==null && $this->modules[$index]["cc_code"]==1){
				//print "<li>".__FILE__." ".__LINE__." Loading ". $this->modules[$index]["name"];
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- load the module
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				require_once dirname(__FILE__)."/".$this->modules[$index]["file"];
				$this->modules[$index]["module"] = new $this->modules[$index]["name"]($this);
				$this->parent->modules[$index]["loaded"]=1;
				$out[count($out)]= Array($this->modules[$index]["tag"],$this->modules[$index]["module"]->command($this->modules[$index]["tag"].$command,$parameter_list));
			} else {
				//print "<li>".__FILE__." ".__LINE__." Exists ". $this->modules[$index]["name"];
			}
			//print "<li>".$this->modules[$index]["tag"].$command;
		}
		return $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- function to call the command function of this module. Overwrites the function in the
	- class Module as the class module calls this->parent while engine calls itsself
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	function call_command($user_command,$parameter_list=array()){
		return $this->command($user_command,$parameter_list);
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- initialise the engine module with all settings required.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function engine_initalise(){
		$initialised = $this->load_config();
		if ($initialised){
			$initialised = $this->check_config();
		}
		return $initialised;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- load the configuration for this client
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function load_config(){
		$this->parent->modules = array();
		$this->modules = array();
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- Initialise the engine load the configuration file and configure the system.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
//		print CONFIGURATION_DIRECTORY."/licence_data.php";
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
				}else {
				}
			}
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
			if (file_exists($extra_mods)){
				$list = file($extra_mods);
				for ($i=0,$m=count($list);$i<$m;$i++){
					$pram=split(",",trim($list[$i]));
					$this->add_module($pram);
				}
			}
			return 1;
		} else {
			$this->call_command("ENGINE_ERROR",array(__FILE__,__LINE__,"The Engine could not load the configuration file [licence.php]"));
			return 0;
		}
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- add an entry to the modules array without loading the modules
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	function add_module($module_array){
		$index=count($this->modules);
		
		$this->modules[$index] = array(
			"file" 		=> $module_array[0],
			"name" 		=> $module_array[1],
			"tag" 		=> $module_array[2],
			"table"		=> $module_array[3],
			"created"	=> 0,
			"admin"		=> $module_array[4],
			"module"	=> null,
			"loaded"	=> 0,
			"cc_code"	=> $this->check_parameters($module_array,5,0) // set if create client code exists
		);
		return 1;
	}
	
	function engine_module_list(){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- return the list of loaded modules
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$outText = "";
		$max_length=count($this->modules);
		for($index=0;$index<$max_length;$index++){
			if (empty($this->modules[$index][2])){
				$loaded=0;
			}else{
				$loaded=1;
			}
			$outText .= "<module name=\"".$this->modules[$index][0]." command_starter=\"".$this->modules[$index][1]."\" loaded=\"$loaded\"/>";
		}
		return $outText;
	}
	
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- close down the engine properly
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
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
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- does this client have a module???
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	function have_module($module_name=""){
		if ($module_name==""){
			return 0;
		}else{
			$ok=0;
			for($index=0,$max_length=count($this->modules);$index<$max_length;$index++){
				if ($this->modules[$index]["tag"]==$module_name){
					$ok=1;
				}
			}
			return $ok;
		}
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- as part of the administration of the system we need to load all modules to generate
	- a navigation menu in the administration side.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	function get_admin_menus(){
		$module_list=Array();
		for($index=0,$max_length=count($this->modules);$index<$max_length;$index++){
			if ($this->modules[$index]["admin"]==1){
				if (empty($this->modules[$index]["module"])){
					$command_eval	 = "require_once \"".dirname(__FILE__)."/".$this->modules[$index]["file"]."\";\n";
					$command_eval	.= "\$this->modules[\$index][\"module\"] = new ".$this->modules[$index]["name"]."(\$this);";
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_admin_menus",__LINE__,"[$command_eval]"));
					}
					eval ($command_eval);
				}
				if (empty($module_list[$this->modules[$index]["module"]->module_grouping])){
					$module_list[$this->modules[$index]["module"]->module_grouping]="";
				}
				$module_list[$this->modules[$index]["module"]->module_grouping] .= $this->modules[$index]["module"]->get_admin_menu_option();
			}
		}
		$list="";
		foreach ($module_list as $key=>$val){
			$list .= "<grouping name=\"$key\">";
			$list .= $val;
			$list .= "</grouping>";
		}
		$out="\t\t<module name=\"admin_menu\">\n".$list."\t\t</module>\n";
		return $out;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- as part of the administration of the system we need to load all modules to generate
	- a navigation menu in the administration side.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	function versions(){
		$out="\t\t<module name=\"versions\">\n";
		for($index=0,$max_length=count($this->modules);$index<$max_length;$index++){
			if (empty($this->modules[$index]["module"])){
				$command_eval	 = "require_once \"".dirname(__FILE__)."/".$this->modules[$index]["file"]."\";\n";
				$command_eval	.= "\$this->modules[\$index][\"module\"] = new ".$this->modules[$index]["name"]."(\$this);";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"retrieve",__LINE__,"[$command_eval]"));
				}
				eval ($command_eval);
			}
			$out.="<entry name=\"".$this->modules[$index]["module"]->get_module_name()."\" version=\"".$this->modules[$index]["module"]->get_module_version()."\" author=\"".$this->modules[$index]["module"]->get_module_author()."\" creation=\"".$this->modules[$index]["module"]->get_module_creation()."\" command=\"".$this->modules[$index]["module"]->get_module_command()."\"/>";
		}
		return $out."\t\t</module>\n";
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Tell the engine to execute a specific command if the user is not logged in
	- then you can supply the login form for displayal on the web site.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function installer_call_command($command,$params=Array()){
//	print $command;
//	print_r($params);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"engine_call_command",__LINE__,"[$command,".join(",",$params)."]"));
		}
		$this->page_output = "";
		$this->page_menu="";
		
		$qstr =$this->check_parameters($_SERVER,"QUERY_STRING");
		$find = Array ("'".session_name()."=".session_id()."'","'printerfriendly=1'","'&$'si","'&&'si");
		$replace 	= Array ('','','','');
		
		$qstr = preg_replace ($find, $replace, $qstr);
		$fc = count($find);
		$rc = count($replace);
		if ($fc!=$rc){
			print "Error code #LS000006 - $fc find attributes versus $rc replace attributes in function make_uri ".__FILE__." line ".__LINE__."<br>";
		}
		//print $this->check_parameters($_SESSION,"INSTALLER_LOGIN",0);
//		print "[$command][$this->module_type]";
		if ($this->module_type=="install"){
//			if ($this->check_parameters($_SESSION,"INSTALLER_LOGIN",0)==1){
/*				if ((time()-300) > ($this->check_parameters($_SESSION,"INSTALLER_STAMP"))){
					$_SESSION["INSTALLER_LOGIN"] = 0;
					$this->page_output .= $this->installer_page_one($params);
				} else {
*/					$_SESSION["INSTALLER_STAMP"] = time();	
//					$this->page_menu = $this->installer_menu($params);
					if (($command==$this->module_command."LOGIN")
					 || ($command==$this->module_command."PAGE_ONE")
					  || ($command=="SETUPNEWCLIENTSTEP4")
					){
						$command=$this->module_command."PAGE_TWO";
					}
					if (($command==$this->module_command."PAGE_TWO")){
						$this->page_output .= $this->installer_page_two($params);
					}
					if (($command==$this->module_command."HELP_CONFIGURE_WEBSERVER")){
						$this->page_output .= $this->installer_help_configure($params);
					}
					
					if (($command==$this->module_command."CREATE_DB_STEP_ONE")){
						$this->page_output .= $this->create_db_step_one($params);
					}
					if (($command==$this->module_command."CREATE_DB_STEP_TWO")){
						$this->page_output .= $this->create_db_step_two($params);
					}
					if ($command==$this->module_command."CREATE_DB_STEP_THREE"){
						$this->page_output .= $this->create_db_step_three($params);
					}
					if (($command==$this->module_command."MANAGE_CLIENT_STEP_ONE")){
						$this->page_output .= $this->create_client_step_one($params);
					}
					if (($command==$this->module_command."MANAGE_CLIENT_STEP_TWO")){
						$this->page_output .= $this->create_client_step_two($params);
					}
					if (($command==$this->module_command."MANAGE_CLIENT_STEP_THREE")){
						$this->page_output .= $this->create_client_step_three($params);
					}
					if (($command==$this->module_command."MANAGE_CLIENT_STEP_FOUR")){
						$this->page_output .= $this->create_client_step_four($params);
					}
					if (($command==$this->module_command."MANAGE_CLIENT_STEP_FIVE")){
						$this->page_output .= $this->create_client_step_five($params);
					}
					if (($command==$this->module_command."MANAGE_THEMES")){
						$this->page_output .= $this->manage_themes($params);
					}
					if (($command==$this->module_command."THEME_ENTRY_IMPORT_SAVE")){
						$this->page_output .= $this->entry_import_save($params);
					}
					if (($command==$this->module_command."THEME_ENTRY_REMOVE")){
						$this->page_output .= $this->remove_themes($params);
					}
					if (($command==$this->module_command."THEME_ENTRY_REMOVE_SAVE")){
						$this->page_output .= $this->remove_themes_save($params);
					}
					
//				}
			/*
			} else {
				if ($command==""){
					$command=$this->module_command."PAGE_ONE";
				}
				if ($command==$this->module_command."LOGIN"){
					$this->page_output .= $this->installer_login($params);
					header("Location: http://".$this->domain.$this->base."admin/install.php?command=".$this->module_command."PAGE_TWO&".session_name()."=".session_id());
				}
				if (($command==$this->module_command."PAGE_ONE") || ($command==$this->module_command."PAGE_TWO")){
					$this->page_output .= $this->installer_page_one($params);
				}
			}*/
		} else {
		}
		$now = date("d M Y");
$mode ="install";
$domain = $_SERVER["HTTP_HOST"];	
$script = $_SERVER["PHP_SELF"];	
if(!defined("DATE_EXPIRES")){
	define ("DATE_EXPIRES",md5("EXPIRES"));
	define ("IP_ADDRESS",md5("IP"));
	define ("LICENCE_TYPE",md5("SETUP"));
	define ("UNLIMITED",md5("-1"));
	define ("ECMS",md5("ENTERPRISE"));
	define ("MECM",md5("LITE"));
	define ("SITE_WIZARD",md5("ULTRA"));
}
$modules = array();
$server_data = Array();	
$base="";
if(basename($script)==""){
	$script.="index.php";
}
$paths = Array();
if (strpos($_SERVER["PHP_SELF"],"~")===0){
	$base ="/";
	$script = substr($script,strlen($base));
	$real_script = substr($_SERVER["PHP_SELF"],strlen($base));
}else{
	$start= strpos($_SERVER["PHP_SELF"], "~");
	$end  = strpos($_SERVER["PHP_SELF"], "/",$start);
	$base = substr($_SERVER["PHP_SELF"], 0,$end+1);
	if ((strpos($script,"~")-1)==-1){
		if (substr($script,0,1)=="/"){
			$script = substr($script,1);
		}else{
			$script = $script;
		}
	}else{
		$script = substr($_SERVER["PHP_SELF"], strlen($base));
	}
	if ((strpos($_SERVER["PHP_SELF"],"~")-1)==-1){
		if (substr($_SERVER["PHP_SELF"],0,1)=="/"){
			$real_script = substr($_SERVER["PHP_SELF"],1);
		}else{
			$real_script = $_SERVER["PHP_SELF"];
		}
	}else{
		$real_script = substr($_SERVER["PHP_SELF"],strlen($this->base));
	}
}		
		
		print "<html lang='EN'>
<head>
<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
<base href='http://$domain$base'>
<title>Libertas-Solutions :: Administration
</title>
<link href='/libertas_images/themes/site_administration/favicon.ico' rel='shortcut icon'>
<link rel='stylesheet' type='text/css' href='/libertas_images/themes/site_administration/style.css'>
<style>
 h1 {padding:10px}
</style>
</head>
<body>
<div class='headerbar'>&#160;</div>
<div class='MenuNavigationCell'><div align='right'>Libertas Solutions Installer v".$this->module_version."</div></div>
<form name='installer' method='post' action=''>
<table width='100%' cellspacing='0' cellpadding='0' height='100%' class='contentTable' border='0' style='top:0px; padding-top:129px;position:absolute'>
<tr><td valign='top' width='200px' style='background-color:#e7eef5;border-right:1px solid black'>
<ul>
	<li><a href='admin/install/index.php'>Home</a></li>
</ul>
<strong>Configuration</strong>
<ul>
	<li><a href='admin/install/index.php?command=SETUPNEWCLIENTSTEP1'>Define Paths</a></li>
	<li><a href='admin/install/index.php?command=SETUPNEWCLIENTSTEP2'>Database Connection</a></li>
	<li><a href='admin/install/index.php?command=SETUPNEWCLIENTSTEP3'>Licence Defintion</a></li>
</ul>
<strong>Database Defintion</strong>
<ul>
	<li><a href='admin/install/install.php?command=INSTALLER_CREATE_DB_STEP_ONE'>Check DB Structure</a></li>
</ul>
<strong>Theme Defintion</strong>
<ul>
	<li><a href='admin/install/install.php?command=INSTALLER_MANAGE_THEMES'>Import themes</a></li>
	<li><a href='admin/install/install.php?command=INSTALLER_THEME_ENTRY_REMOVE'>Remove Themes</a></li>
</ul>
<strong>Clients</strong>
<ul>
<li><a href='admin/install/install.php?command=INSTALLER_MANAGE_CLIENT_STEP_ONE'>Client Definition</a></li>
</ul>
</td>
		<td valign='top'>".$this->page_output."</td>
	</tr>
</table></form></td></tr>
</table>
</body>
</html>";
		
	}
	
	function check_config(){
		$ok=-1;
		if (
				(
					($this->server[DATE_EXPIRES]==UNLIMITED)
				) && (
					($this->server[LICENCE_TYPE]==ECMS) ||
					($this->server[LICENCE_TYPE]==MECM) ||
					($this->server[LICENCE_TYPE]==SITE_WIZARD)
				)
			){
			$ok=1;
		}
		return $ok;
		
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Initialise function
	-----------------------
	- This function will initialise some variables for this modules functions to use.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is called by the constructor it over writes the basic
	- module::initialise() function allowing you to define any extra constructor
	- functionality.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- define some variables
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$this->super_user_access = 0;
		$this->system_prefs = load_system_prefs(Array());
		return 1;
	}
	
	function installer_menu($parameters){
		$menu = "
		<ul>
			<li><a href='admin/install.php?command=INSTALLER_PAGE_TWO&amp;".session_name()."=".session_id()."'>Home</a></li>
			<li><a href='admin/install.php?command=INSTALLER_CREATE_DB_STEP_ONE&amp;".session_name()."=".session_id()."'>Examine DB Structure</a></li>
			<li><a href='admin/install.php?command=INSTALLER_MANAGE_CLIENT_STEP_ONE&amp;".session_name()."=".session_id()."'>Add/List Clients</a></li>
		</ul>
		<hr>
		<ul>
			<li><a href='admin/install.php?command=INSTALLER_HELP_CONFIGURE_WEBSERVER&amp;".session_name()."=".session_id()."'>Help on Configuring the web server.</a></li>
		</ul>
		";
		return $menu;
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Login into the installer seperate username and password from main system.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function installer_login($parameters){
		$username = $this->check_parameters($parameters,"installer_username");
		$password = $this->check_parameters($parameters,"installer_password");
		$cfg_dir = $this->check_parameters($this->parent->site_directories,"SYSTEM_CONFIG_DIR",".");
		
		if (file_exists($cfg_dir."/installer.cfg")){
			$lines = file($cfg_dir."/installer.cfg");
//			print $cfg_dir."/installer.cfg";
//			print md5("LIBERTAS_INSTALLER_$username")."==". chop($lines[0])."<br>";
//			print md5("LIBERTAS_INSTALLER_$password")."==". chop($lines[1]);
			if ((md5("LIBERTAS_INSTALLER_$username") == chop($lines[0])) && (md5("LIBERTAS_INSTALLER_$password") == chop($lines[1]))){
				$_SESSION["INSTALLER_LOGIN"] = 1;
				$_SESSION["INSTALLER_STAMP"] = time();
				return 1;
			}
		} else {
		}
		return 0;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Page one will display the login screen
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function installer_page_one($parameters){
		$this->breadcrumb = "Login Screen";	
		$out =  "";
		$out .= "<h1>Login</h1>";
		$out .= "<form name=\"installer_login\" method=\"post\" >";
		$out .= "<input type=\"hidden\" name=\"command\" value='INSTALLER_LOGIN'>";
		$out .= "<table border=0>";
		$out .= "<tr><td>User name </td><td><input type=\"text\" name=\"installer_username\" ></td></tr>";
		$out .= "<tr><td>Password </td><td><input type=\"password\" name=\"installer_password\" ></td></tr>";
		$out .= "<tr><td colspan=2 align=right><input type=\"submit\" class='bt' class='bt' iconify=\"LOGIN\" value=\"Login\"/></td></tr>";
		$out .= "</table>";
		$out .= "</form>";
		$this->page_output .= $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Page one will display the login screen
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function installer_page_two($parameters){
		$this->breadcrumb = "Welcome Screen";	
		$out  = "
		<h1>Welcome to the Libertas Solutions System installer</h1>
		<p><strong>Before</strong> running this installer please check you have configured your Operating System, Web server and Database Server properly.</p>
		<p>You will need to make sure that the web user that is running the web server has write access to the appropraite directories and that the database server has a database for you to work on and that you know the username and password of the user account you will use to connect to the database.</p>
		";
		$this->page_output .= $out;
	}


	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Help on configuring the web server
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function installer_help_configure(){
		$out  = "
		<h1>Configuring your web server</h1>
		<ul>
			<li><A href='#windows_iis'>Windows running IIS</A></li>
			<li><A href='#windows_apache'>Windows running Apache</A></li>
		</ul>
		<ul>
			or
		</ul>
		<ul>
			<li><A href='#linux_apache'>Linux running Apache</A></li>
		</ul>
		<hr>
		<h2>Windows</h2>
		<a name='windows_iis'/>
		<h3>Running IIS</h3>
		<p>blah</p>
		<a name='windows_apache'/>
		<h3>Running Apache</h3>
		<p>blah</p>
		<h2>Linux</h2>
		<a name='linux_apache'/>
		<h3>Running Apache</h3>
		<p>blah</p>
		";
		$this->page_output .= $out;
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Step 1 of creating the database structure.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function create_db_step_one($parameters){
		$test = $this->connection();
//		print $test;
		if ($test!=1){
		} else {
			$mods = Array();
			$this->breadcrumb = "Test Database Structure <small>&gt;&gt;</small> Define Connection Details <small>&gt;&gt;</small> Testing Connection <small>&gt;&gt;</small> Test Database structure";	
			for($index=0,$length=count($this->modules);$index<$length;$index++){
				if ($this->modules[$index]["table"]==1){
					$tables =  $this->call_command($this->modules[$index]["tag"]."CREATE_TABLE",Array("initialise" => false));
//					print "<li>".$this->modules[$index]["tag"]."</li>";
					$m = count($tables);
					if ($m>0){
					for ($i=0;$i<$m;$i++){
						if(is_array($tables[$i])){
							if (strlen($tables[$i][0])>0){
								$mods[count($mods)] = $tables[$i];
							}
						} else {
							if (strlen($tables[$i])>0){
								$mods[count($mods)] = $tables[$i];
							}
						}
					}
					}
				}
			}
			$out = "
			<h1>Examining the database structure.</h1>
			<h2>Report</h2>";
			
			$temp="<table cellspacing=5>";
			$count=0;
			$found = 0;
			for ($index=0,$max = count($mods);$index<$max;$index++){
				$label	 = $mods[$index][0];
				if ($this->call_command("DB_TABLE_EXISTS",Array($label))){
					$exists	 = "<font color=green>Found</font>";
				} else {
					$found=1;
					$this->call_command("DB_CREATE_TABLE",Array($label, $mods[$index][1], $mods[$index][2]));
					if (!empty($mods[$index][3])){
						if (is_array($mods[$index][3])){
							for ($i=0,$m = count($mods[$index][3]);$i<$m;$i++){
								$this->call_command("DB_QUERY",array($mods[$index][3][$i]));
							}
						} else {
							$this->call_command($mods[$index][3]);
						}
					}
					$exists	 = "<font color=red>Created</font>";
				}
				if ($count==3){
					$temp 	.= "</tr>";
					$count=0;
				}
				if ($count==0){
					$temp 	.= "<tr>";
				}
				$count++;
				$temp 	.= "<td>$label</td><td>$exists</td>";
			}
			if ($count==1){
				$temp 	.= "</tr>";
				$count=0;
			}
			$temp.="</table>";
			if ($found==1){
				$out.="<h3>Thankyou, Some changes have been made to the database structure as noted below.</h3>".$temp;
			} else {
				$out.="<h3>Thankyou, The the following tables were found.</h3>".$temp;
			}
		}
		$this->page_output .= $out;
	}


	function create_client_step_one($parameters){
		$test = $this->connection();
		if ($test!=1){
//			$out = create_db_step_one($parameters);
		} else {
			$this->breadcrumb = "Client Management <small>&gt;&gt;</small> Define Connection Details <small>&gt;&gt;</small> Testing Connection <small>&gt;&gt;</small> List Clients";
			$sql = "select * from client 
						inner join theme_client_has on theme_client_has.client_identifier = client.client_identifier
						inner join theme_data on theme_client_has.theme_identifier = theme_data.theme_identifier
					order by client.client_identifier desc
			";
			$result = $this->call_command("DB_QUERY",Array($sql));
			$c=0;
			$clients="";
			$counter=0;
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$counter++;
				$domain  ="";
				$domain_sql = "select * from domain where domain_client = ".$r["client_identifier"];
				$domain_result = $this->call_command("DB_QUERY",Array($domain_sql));
				while ($d = $this->call_command("DB_FETCH_ARRAY",Array($domain_result))){
					$domain.="<li>".$d["domain_name"]."</li>";
				}
				$clients .= "<tr ";
				if ($c==0){
					$c=1;
					$clients .= "bgcolor=#FFffff";
				}else{
					$c=0;
					$clients .= "bgcolor=#d5d5e2";
				}
				$clients .= "><td valign=top>".$r["client_identifier"]."</td><td valign=top>".$r["client_name"]."</td><td valign=top>".$r["theme_label"]."</td><td valign=top>".$domain."</td></tr>";
			}

			$out = "
			<h1>Existing Clients</h1>
			<h2>Add a new client</h2>
			<form method=post>
			<input type=hidden name=command value='INSTALLER_MANAGE_CLIENT_STEP_FOUR'>
			<input type=hidden name=action_type value='ADD'>
			<input type='submit' class='bt' value='Add a new Client'>
			</form>
			<h2>Existing clients on this database ($counter clients defined)</h2>
			<table cellpadding=5 cellspacing=3 width=100%>
			<tr bgcolor=#a2b6ce><td class='bt'>#</td><td class='bt'>Client Name</td><td class='bt'>Choosen Theme</td><td class='bt'>Domains Listed</td></tr>
			$clients
			</table>
			";
		}

		$this->page_output .= $out;
	}
	function create_client_step_four($parameters){
		$test = $this->connection();
		if ($test!=1){
			$out = $this->create_db_step_one($parameters);
		} else {
			$action_type = $this->check_parameters($parameters,"action_type");
			if ($action_type=="ADD"){
				$type = "Add new Client";
				$client_contact = -1;
				$theme=-1;
			} else {
				$type = "Edit Existing Client";
			}
			$this->breadcrumb = "Client Management <small>&gt;&gt;</small> Define Connection Details <small>&gt;&gt;</small> Testing Connection <small>&gt;&gt;</small> List Clients <small>&gt;&gt;</small> $type";	
			$udomain = $this->domain;
			if ($this->base!="/"){
				$udomain .=$this->base;
			}
			$out="
				<h1>$type</h1>
				<form method=post>
				<input type=hidden name='command' value='INSTALLER_MANAGE_CLIENT_STEP_FIVE'>
				<input type=hidden name=action_type value='$action_type'>
				<input type=hidden name='client_contact' value='client_contact'>
				<table>
				<tr><td>Company/Client Name</td><td><input type='text' name='company_client' value='' size=30 border='0'/></td></tr>
				<tr><td colspan=2><hr></td></tr>
				<tr><td colspan='2'><strong>Administrator Details</strong></td></tr>
				<tr><td>Username</td><td><input type='text' name='uname' value='l1b_adm1n_us3r' size=30 border='0'/></td></tr>
				<tr><td>Password</td><td><input type='password' name='upwd' value='l1b_tmp_adm1n_pwd' size=30 border='0'/></td></tr>
				<tr><td>First name</td><td><input type='text' name='firstname' value='System' size=30 border='0'/></td></tr>
				<tr><td>Surname</td><td><input type='text' name='surname' value='Administrator' size=30 border='0'/></td></tr>
				<tr><td colspan='2'>The default email address that will be used for any emails being sent by the system</td></tr>
				<tr><td>Email Address</td><td><input type='text' name='email_address' value='info@".$this->parseDomain($this->domain)."' size=30 border='0'/></td></tr>
				<tr><td colspan=2><hr></td></tr>
				<tr><td colspan=2>The primary <strong>domain</strong> is the domain you will currently be using to work with this clients details.  if avalid domain is available and pointed at the web site you can use it, though it is recommended for you to use the predefined setting below and to log in to the system a park any extra domains you may require.</td></tr>
				<tr><td>Primary Domain</td><td><input type='text' name='domain_name' value='$udomain' size=30 border='0'/></td></tr>
				<tr><td colspan=2><hr></td></tr>
				<tr><td colspan=2>Here you will find a list of the themes currently in this database. You may use one of these until such time as a client specific theme is created.</td></tr>
				<tr><td>Themes to choose from</td><td><select name=theme_identifier>".$this->call_command("THEME_RETRIEVE_LIST_OF",Array($theme,"LOAD_LOCALE"=>1))."</select></td></tr>
				<tr><td colspan=2 align=right><input type='submit' class='bt' value='Save' border='0'/></td></tr>
				</table>
				</form>
			";
		}

		$this->page_output .= $out;
	}
	
	function create_client_step_five($parameters){
		$out="";
		$test = $this->connection();
		if ($test!=1){
//			$out = create_db_step_one($parameters);
		} else {
			$action_type= $this->check_parameters($parameters,"action_type","ADD");
			$uname 		= $this->check_parameters($parameters,"uname","l1b_adm1n_us3r");
			$upwd 		= $this->check_parameters($parameters,"upwd","l1b_tmp_adm1n_pwd");
			if ($action_type=="ADD"){
				$type = "Add new Client";
			} else {
				$type = "Edit Existing Client";
			}
			$this->breadcrumb = "Client Management <small>&gt;&gt;</small> Define Connection Details <small>&gt;&gt;</small> Testing Connection <small>&gt;&gt;</small> List Clients <small>&gt;&gt;</small> Managing Client";	
			
			$email 					= $this->check_parameters($parameters,"email_address");
			$contact_identifier 	= -1;
			$strapline				= $this->check_parameters($parameters,"strapline");
			$company_client			= $this->check_parameters($parameters,"company_client");
			$surname				= $this->check_parameters($parameters,"surname");
			$firstname				= $this->check_parameters($parameters,"firstname");
			$domain_name			= $this->check_parameters($parameters,"domain_name");
			$theme					= $this->check_parameters($parameters,"theme_identifier");
			if ($action_type=="ADD"){
				$now = Date ("Y/m/d H:i:s");
				$sql = "
					insert into client 
						(client_name, client_date_created) 
					values 
						('$company_client','$now')
				";
				$this->call_command("DB_QUERY",Array($sql));
				////print "<li>".__LINE__." :: $sql</li>";
				$sql ="select * from client where client_name='$company_client' and client_date_created ='$now'";
				//print "<li>".__LINE__." :: $sql</li>";
				$result = $this->call_command("DB_QUERY",Array($sql));
				$client_identifier= -1;
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$client_identifier				= $r["client_identifier"];
					$this->client_identifier		= $client_identifier;
					$_SESSION["client_identifier"]	= $r["client_identifier"];
				}
				$this->parent->system_prefs			= $this->call_command("SYSPREFS_LOAD_SYSTEM_PREFERENCE");
				/*
					get the address identifier
				*/
				
				$sql = "insert into contact_address (address_client) values ($client_identifier)";
				//print "<li>".__LINE__." :: $sql</li>";
				$this->call_command("DB_QUERY",Array($sql));
				
				$sql ="select * from contact_address where address_client=$client_identifier;";
				//print "<li>".__LINE__." :: $sql</li>";
				$result = $this->call_command("DB_QUERY",Array($sql));
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$address = $r["address_identifier"];
				}
				$sql = "insert into contact_data 
							(contact_client, contact_first_name, contact_last_name, contact_address, contact_date_created, contact_user)
						values
							($client_identifier, '$firstname', '$surname',$address, '$now', 0 );";
				$this->call_command("DB_QUERY",Array($sql));
				$sql = "select * from contact_data where contact_date_created='$now' and contact_address=$address and contact_client=$client_identifier";
				//print "<li>".__LINE__." :: $sql</li>";
				$result = $this->call_command("DB_QUERY",Array($sql));
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$contact_identifier = $r["contact_identifier"];
				}
				
				$email_id = $this->call_command ("EMAIL_INSERT_ADDRESS",Array("verified" => 1, "email_address" => $email, "email_contact" => $contact_identifier, "email_client" => $client_identifier));
				$sql = "update client set client_contact = $contact_identifier where client_identifier =$client_identifier;";
				//print "<li>".__LINE__." :: $sql</li>";
				$this->call_command("DB_QUERY",Array($sql));
				
				$sql = "insert into contact_company 
							(company_client, company_address, company_name)
						values
							($client_identifier, $address, '$company_client' );";
				//print "<li>".__LINE__." :: $sql</li>";
				$this->call_command("DB_QUERY",Array($sql));
				$sql = "insert into domain 
							(domain_client, domain_name)
						values
							($client_identifier, '$domain_name');";
				//print "<li>".__LINE__." :: $sql</li>";
				$this->call_command("DB_QUERY", Array($sql));
				$sql	= "insert into theme_client_has (client_identifier, theme_identifier) values ($client_identifier, $theme)";
				//print "<li>".__LINE__." :: $sql</li>";
				$this->call_command("DB_QUERY", Array($sql));
				//print count($this->modules);
				for($index=0,$length=count($this->modules);$index<$length;$index++){
					
					
					if ($this->modules[$index]["table"]==1 && $this->modules[$index]["cc_code"]==1){
						//print "<li>".__FILE__." ".__LINE__." ".$this->modules[$index]["tag"]."CREATE_NEW_CLIENT_DETAILS";
						$this->call_command($this->modules[$index]["tag"]."CREATE_NEW_CLIENT_DETAILS",Array("client_identifier" => $client_identifier, "email_address" => $email));
					}
				}
				//exit();
				$l_props= "";
				$l_id	= "";
				$ids 	= Array();
				$menu_id= "";
				$ranks	= Array();
				$sql = "select * from web_objects where (wo_type =1 or wo_command in ('WEBOBJECTS_SHOW_BREADCRUMB', 'WEBOBJECTS_SHOW_MAIN_MENU') )and wo_client=$client_identifier";
				$result = $this->call_command("DB_QUERY",Array($sql));
				$rank=3;
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					if ($r["wo_command"]!="WEBOBJECTS_SHOW_MAIN_MENU") {
						$ids[count($ids)] = $r["wo_identifier"];
						if ($r["wo_command"]=="WEBOBJECTS_SHOW_BREADCRUMB"){
							$ranks[count($ranks)] = 1;
							$rank++;
						} else if($r["wo_command"]=="PRESENTATION_DISPLAY_PAGE"){
							$ranks[count($ranks)] = 2;
							$rank++;
						} else {
							$ranks[count($ranks)] = $rank;
							$rank++;
						}
						$l_props	.= "text-align~~left,width~~100%,label~~";
						if ($l_id!=""){
							$l_id	.= ",";
						}
						$l_id		.= $r["wo_identifier"];
					} else {
						$menu_id = $r["wo_identifier"];
					}
				}
				$this->call_command("WEBOBJECTS_CONTAINER_SAVE", Array(
					"wc_type"					=> "__SYSTEM__",
					"wc_label"					=> "Menu Container",
					"wc_width"					=> "100%",
					"wc_columns"				=> "1",
					"webobject_list"			=> "$menu_id",
					"webobject_list_properties"	=> "text-align~~left,width~~100%,label~~",
					"number_of"					=> 1,
					"id"						=> Array($menu_id),
					"rank"						=> Array(1),
					"no_redirect"				=> 1
					)
				);
				
				$this->call_command("WEBOBJECTS_CONTAINER_SAVE", Array(
					"wc_type"					=> "__SYSTEM__",
					"wc_label"					=> "Main Container",
					"wc_width"					=> "100%",
					"wc_columns"				=> "1",
					"webobject_list"			=> $l_id,
					"webobject_list_properties"	=> $l_props,
					"number_of"					=> count($ids),
					"id"						=> $ids,
					"rank"						=> $ranks,
					"no_redirect"				=> 1
					)
				);
				$this->call_command("WEBOBJECTS_CONTAINER_SAVE", Array(
					"wc_type"					=> "__UD__",
					"wc_label"					=> "Company Logo Container",
					"wc_width"					=> "100%",
					"wc_columns"				=> "1",
					"webobject_list"			=> "",
					"webobject_list_properties"	=> "",
					"number_of"					=> 0,
					"id"						=> Array(),
					"rank"						=> Array(),
					"no_redirect"				=> 1
					)
				);
				$sql = "insert into web_layouts 
						(wol_client, wol_status, wol_version, wol_live, wol_complete, wol_label, wol_layout_design, wol_all_locations, wol_created, wol_set_inheritance, wol_theme) 
					values 
						($client_identifier, 1, 1, 1, 1, 'Default', '13', '1', '$now', 1, $theme)";
				//print "<li>".__LINE__." :: $sql</li>";
				$this->call_command("DB_QUERY",Array($sql));
				$sql = "select * from web_containers where wc_client = $client_identifier";
				$result = $this->call_command("DB_QUERY",Array($sql));
				$wc_ids = Array();
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$wc_ids[count($wc_ids)] = Array("id"=> $r["wc_identifier"], "label"=> $r["wc_label"]);
				}
				$sql = "select * from web_layouts where wol_client = $client_identifier";
				//print "<li>".__LINE__." :: $sql</li>";
				$result = $this->call_command("DB_QUERY",Array($sql));
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$wol_id = $r["wol_identifier"];
				}
				for ($i=0,$m=count($wc_ids); $i<$m; $i++){
					if ($wc_ids[$i]["label"]=="Main Container"){
						$pos=2;
					} else {
						$pos=1;
					}
					$sql = "insert into web_container_to_layout 
								(wctl_client, wctl_layout, wctl_container, wctl_position, wctl_rank)
							values
								($client_identifier, $wol_id, ".$wc_ids[$i]["id"].", $pos, 1)";
					$this->call_command("DB_QUERY",Array($sql));
				}
//				exit();
				$sql ="insert into user_info 
							(user_login_name, user_login_pwd, user_creation_date, user_status, user_client, user_uid)
						values
							('$uname', '".md5($upwd)."', '$now', 2, $client_identifier, '')";
				//print "<li>".__LINE__." :: $sql</li>";
				$this->call_command("DB_QUERY",Array($sql));
				$sql = "select * from user_info where 
							user_login_name = '$uname' and 
							user_login_pwd = '".md5($upwd)."' and 
							user_creation_date = '$now' and 
							user_status = 2 and 
							user_client = '$client_identifier'
						";
				//print "<li>".__LINE__." :: $sql</li>";
				$result = $this->call_command("DB_QUERY",Array($sql));
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$user_identifier = $r["user_identifier"];
				}
				$sql = "update contact_data set contact_user = $user_identifier where contact_identifier = $contact_identifier and contact_client=$client_identifier";
				$this->call_command("DB_QUERY",Array($sql));
				$sql = "select * from group_data where group_client = $client_identifier and group_type=2 and group_label='Administrator';";
				//print "<li>".__LINE__." :: $sql</li>";
				$result = $this->call_command("DB_QUERY",Array($sql));
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$group_identifier = $r["group_identifier"];
				}
				$sql ="insert into groups_belonging_to_user 
							(group_identifier, user_identifier, client_identifier)
						values
							(".$group_identifier.", $user_identifier, $client_identifier)";
				//print "<li>".__LINE__." :: $sql</li>";
				$this->call_command("DB_QUERY",Array($sql));
				if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
					$sql = "insert into user_admin_functions (function_tag, function_value, client_identifier, user_identifier) values ('can_use_editor','CAN_SWITCH_EDITOR', $this->client_identifier, $user_identifier)";
					//print "<li>".__LINE__." :: $sql</li>";
					$this->call_command("DB_QUERY",Array($sql));
				}
			}
			$_SESSION["INSTALLER"]="true";
			$_SESSION["INSTALL_RESTORE"]=1;
			$this->call_command("LAYOUT_RESTORE");
			
		}
		$this->page_output .= "<h1>Client set up completed</h1><p>Return to the list of clients <a href='admin/install/install.php?command=INSTALLER_MANAGE_CLIENT_STEP_ONE'>Back</a></p>";
		return "";
	}

	function general_test(){
		//print "<h1></h1>";
	}
		
	function connection(){
		$dbfile = CONFIGURATION_DIRECTORY."/db_settings.php";
		if (file_exists($dbfile)){
			$list = file($dbfile);
			$m=count($list);
			if ($m==8){
				for ($i=2;$i<7;$i++){
					$items = split(",",chop($list[$i]));
					if ($items[0]=="db_host"){
						$_SESSION["installer_host"] = $items[1];
					}
					if ($items[0]=="db_database"){
						$_SESSION["installer_db"] = $items[1];
					}
					if ($items[0]=="db_username"){
						$_SESSION["installer_user"] = $items[1];
					}
					if ($items[0]=="db_password"){
						$_SESSION["installer_pwd"] = $items[1];
					}
				}
			}
		}
//		print_r($_SESSION);
		$host	= $this->check_parameters($_SESSION,"installer_host");
		$user	= $this->check_parameters($_SESSION,"installer_user");
		$pwd	= $this->check_parameters($_SESSION,"installer_pwd");
		$this->database 	= $this->check_parameters($_SESSION,"installer_db");
		$status = 1;
		$this->connection = $this->call_command("DB_CONNECT",array($host,$user,$pwd));
		if ($this->connection){
			$db_found = $this->call_command("DB_SELECT",array($this->database));
			if (!$db_found){
				$status=-1;
			}
		}else{
			$status=-2;
		}
		return $status;
	}
	function site_restore(){
		$index	= $this->check_parameters($_SESSION,"ENGINE_RESTORE_INDEX",-1);
		if ($index==-1){
			$index = 0;
			$_SESSION["ENGINE_RESTORE_INDEX"]=0;
		}
		$length	= count($this->parent->modules) - 1;
		if ($length >= $index){
			$_SESSION["ENGINE_RESTORE_INDEX"]++;
			$counter=0;
			foreach($this->parent->modules as $key => $moduleEntry){
				if ($index==$counter){
					$d = $this->call_command($this->parent->modules[$key]["tag"]."RESTORE");
					if ($d==""){
						$this->call_command("INSTALLER_REFRESH_BUFFER",Array("command=ENGINE_RESTORE"));
					} else {
					}
				}
				$counter++;
			}
		} else {
			$_SESSION["ENGINE_RESTORE_INDEX"]=-1;
			// Thankyou screen required
			if ($this->check_parameters($_SESSION,"ENGINE_RESTORE_REDIRECT","__NOT_FOUND__")=="__NOT_FOUND__"){
				$out="
					<module name=\"splash\" >
						<text><![CDATA[Thankyou the system has been restored.]]></text>
					</module>";
				return $out;
			} else {
				$this->call_command("INSTALLER_REFRESH_BUFFER",Array($this->check_parameters($_SESSION,"ENGINE_RESTORE_REDIRECT","command=LAYOUT_LIST_MENU")));
			}
		}
	}
	function manage_themes($parameters){
	$test = $this->connection();
		$sql = "select * from theme_data";
		$existing_dirs = Array(".","..","site_administration","printer_friendly","pda","textonly","CVS","netplosion");
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			//print "[".$r["theme_directory"]."]";
//			print "<li>".$r["theme_directory"]."</li>";
        	$existing_dirs[count($existing_dirs)] = $r["theme_directory"];
        }
        $this->call_command("DB_FREE",Array($result));
		$xsl_dir		= $this->parent->site_directories["XSL_THEMES_DIR"];
		$d 				= dir($xsl_dir."/stylesheets/themes");
		$themes 		= Array();
		while (false !== ($entry = $d->read())) {
			if (is_dir($xsl_dir."/stylesheets/themes/".$entry) && (!in_array($entry,$existing_dirs))){
				$themes[count($themes)]= Array("DIR"=> $entry, "CONTAINED"=>"NO", "LABEL"=>NULL);
			}
		}
		$d->close(); 
		
		$sql = "select * from theme_data";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[building options]"));
		}
		if($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))) {
				for ($index=0,$max = count($themes);$index<$max;$index++){
					if ($themes[$index]["DIR"] == $r["theme_directory"]){
						$themes[$index]["CONTAINED"] = "YES";
						$themes[$index]["LABEL"] = $r["theme_label"];
					}
				}
			}
		}
		$out ="
			<form name=\"select_default_theme\"  method=\"post\" width=\"100%\">
				<h1>".LOCALE_ADD_THEME_FORM."</h1>
				<input type=\"hidden\" name=\"command\" value=\"INSTALLER_THEME_ENTRY_IMPORT_SAVE\"/>
				<table>
					<tr>
						<td>".LOCALE_THEME_LABEL."</td>
						<td>".LOCALE_THEME_DIR."</td>
						<td>".LOCALE_THEME_IMPORT."</td>
					</tr>";
			$output="";
			$num=0;
			for ($index=0,$max = count($themes);$index<$max;$index++){
					if ($themes[$index]["CONTAINED"]=="NO"){
					$num++;
				$output.="
					<tr>
						<td><input type='text' name='theme_name[]' size='25' value='".$this->check_parameters($themes[$index],"LABEL",$themes[$index]["DIR"])."'/></td>
						<td><input type='hidden' name='theme_dir[]' value='".$themes[$index]["DIR"]."'/>".$themes[$index]["DIR"]."</td>
						<td><input type='checkbox' name='theme_import[]' size='25' value='$num' /></td>
					</tr>";
					}
			}
			$out.="$output
				</table><input type='hidden' name='number_of' value='$num'/>
				
				<input class='bt' type=\"submit\" name=\"SAVE\" value=\"".SAVE_DATA."\" iconify=\"SAVE\"/>
			</form>";
		return $out;

	}
	function entry_import_save($parameters){
		$test = $this->connection();
		$theme_name 	= $this->check_parameters($parameters,"theme_name");
		$theme_dir		= $this->check_parameters($parameters,"theme_dir");
		$theme_import	= $this->check_parameters($parameters,"theme_import");
		$number_of 		= $this->check_parameters($parameters,"number_of");
		$li="";
		for ($z=0;$z<count($theme_import);$z++){
			$index = $theme_import[$z]-1;
			$sql = "insert into theme_data (theme_label, theme_directory) values ('".$theme_name[$index]."', '".$theme_dir[$index]."');";
			$this->call_command("DB_QUERY",array($sql));
			$theme_identifier=-1;
			$result = $this->call_command("DB_QUERY",array("select * from theme_data where theme_label = '".$theme_name[$index]."' and theme_directory='".$theme_dir[$index]."';"));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$theme_identifier = $r["theme_identifier"];
			}
			$sql ="select * from theme_types";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$sql = "insert into theme_templates (template_theme, template_xsl_file, template_type, template_xsl_entry) values ($theme_identifier, '".$r["theme_type_branch"]."', '".$r["theme_type_identifier"]."', '".$r["theme_type_leaf"]."');";
//				print "<p>$sql</p>";
				$this->call_command("DB_QUERY",array($sql));
			}
			$li .= "<li>".$theme_name[$z]."</li>";
		}
//		$this->call_command("DB_QUERY",array("update theme_client_has set theme_identifier= $theme_identifier where client_identifier = $this->client_identifier"));
		return "<h1>Thankyou the following themes have been imported</h1><ul>$li</ul>";
	}
	function remove_themes($parameters){
		$test = $this->connection();
		$sql = "select theme_data.* from theme_data 
left outer join theme_client_has on theme_client_has.theme_identifier = theme_data.theme_identifier
where client_identifier is null";
//print "$sql";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$existing_dirs = Array();
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	    	$existing_dirs[count($existing_dirs)] = Array(
				"id" => $r["theme_identifier"],
				"label"=>$r["theme_label"]
			);
	    }
        $this->call_command("DB_FREE",Array($result));
		$out ="
			<form name=\"select_default_theme\"  method=\"post\" width=\"100%\">
				<h1>Remove existing themes from the system</h1>
				<input type=\"hidden\" name=\"command\" value=\"INSTALLER_THEME_ENTRY_REMOVE_SAVE\"/>
				<table>
					<tr><td>Please choose the themes that you wish to remove from this database. <strong>Note</strong> this will not physically remove any files but will remove the ability to switch to this theme for <strong>ANY</strong> clients held in this database</td></tr>
					<tr>
						<td><strong>".LOCALE_THEME_LABEL."</strong></td>
					</tr>";
			$output="";
			$num=0;
			for ($index=0,$max = count($existing_dirs);$index<$max;$index++){
				$output.="
					<tr>
						<td><input type='checkbox' name='theme_remove[]' value='".$this->check_parameters($existing_dirs[$index],"id")."' /> ".$this->check_parameters($existing_dirs[$index],"label")."</td>
					</tr>";
			}
			$out.="$output
				</table><input type='hidden' name='number_of' value='$num'/>
				
				<input class='bt' type=\"submit\" name=\"SAVE\" value=\"Remove\" iconify=\"SAVE\"/>
			</form>";
		return $out;
	}
	function remove_themes_save($parameters){
		$test = $this->connection();
		$theme_remove = $this->check_parameters($parameters,"theme_remove",Array());
		if (count($theme_remove)>0){
			$theme_list = join(",",$theme_remove);
			$sql = "delete from theme_templates where theme_template in ($theme_list)";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "delete from theme_data where theme_identifier  in ($theme_list)";
			$this->call_command("DB_QUERY",Array($sql));
		}
		return "<h1>Remove confirmation </h1><p>The choosen themes have been removed from the system.</p>";
	}
}
?>