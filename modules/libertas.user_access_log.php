<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.users.php
* @date 09 Oct 2002
*/
/**
* This function logs user access on the site
*/
class user_access_log extends module{
	/**
	*  Class Variables
	*/
	var $module_name			= "user_access_log";
	var $module_name_label		= "Site Stat log (Presentation)";
	var $module_version 		= '$Revision: 1.7 $';
	var $module_grouping		= "";
	var $module_admin			= "0";
	var $module_debug			= false;
	var $module_command			= "USERACCESSLOG_"; 		// all commands specifically for this module will start with this token
	var $module_label			= "MANAGEMENT_LOG";
//	var $module_admin_options 	= array("<option command=\"user_access_LIST_WEEK\">Show stats for this week</option>","<option command=\"user_access_LIST_MONTH\">Show stats for this month</option>");

	var $module_display_options=Array();
	var $module_admin_options = array();
	var $module_admin_user_access = array();
	var $module_reports = Array();
	var $module_constants = Array(
		"__LOG_PAGE_ACCESS__" 			=> 1,
		"__LOG_PAGE_EMAILED__" 			=> 2,
		"__LOG_FILE_DOWNLOAD__" 		=> 3,
		"__LOG_FILE_DOWNLOAD_NO_FILE__" => 4,
		"__LOG_LOGIN_FAILED__" 			=> 5,
		"__LOG_PAGE_EMAIL_BLOCKED__" 	=> 6,
		"__LOG_FILE_LOGIN_REQUIRED__" 	=> 7,
		"__LOG_RSS_DOWNLOAD__"			=> 8,
		"__LOG_RSS_DOWNLOAD_NO_FILE__"	=> 9
		
	);
	
	var $admin_access=0;
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
			if ($user_command==$this->module_command."ACCESS"){
				return $this->LogAccess($parameter_list);
			}
			
		}else{
			// wrong command sent to system
			return "";
		}
	}
	
	function initialise(){
		/**
		* request the client identifier once we use this variable often
		*/
		$this->admin_access=0;
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$this->client_identifier = $this->parent->client_identifier;
		$this->domain_identifier=$this->call_command("CLIENT_GET_DOMAIN_IDENTIFIER");
		
		
	}	
	

	function LogAccess($parameters){
		$keep_logs						= $this->call_command("ENGINE_LOGS");
		$browser_string					= $this->check_parameters($_SERVER,"HTTP_USER_AGENT");
		$ip								= $this->check_parameters($_SERVER,"REMOTE_ADDR");
		$user_access_accept_language 	= strtolower($this->check_parameters($_SERVER,"HTTP_ACCEPT_LANGUAGE","N.A."));
		$referer					 	= $this->check_parameters($_SERVER,"HTTP_REFERER","No Referer");
		$log_type 						= $this->check_parameters($parameters,0);
		$qstring 						= $this->check_parameters($parameters,1);
		$log_string 					= $this->check_parameters($_SERVER,"REQUEST_URI");
		$user_identifier				= "";//$this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER");
		$session_identifier				= session_id();
		$session_id 					= $this->check_parameters($parameters[1],"my_session_identifier");
		if ($session_id == md5("SearchBot - Google")){
			$bot_name = "SearchBot - Google";
		} else if ($session_id == md5("SearchBot - IA Archiver")){
			$bot_name = "SearchBot - Alexa Archiver";
		} else if ($session_id == md5("SearchBot - Crawler")){
			$bot_name = "SearchBot - Crawler";
		} else if ($session_id == md5("SearchBot - Walker")){
			$bot_name = "SearchBot - Walker";
		} else if ($session_id == md5("SearchBot - Scrub")){
			$bot_name = "SearchBot - Scrub";
		} else if ($session_id == md5("SearchBot - Internetseer.com")){
			$bot_name = "SearchBot - Internetseer.com";
		} else if ($session_id == md5("SearchBot - Scooter")){
			$bot_name = "SearchBot - Scooter";
		} else if ($session_id == md5("SearchBot - Spider")){
			$bot_name = "SearchBot - Spider";
		} else if ($session_id == md5("SearchBot - Slurp")){
			$bot_name = "SearchBot - Slurp";
		} else if ($session_id == md5("SearchBot")){
			$bot_name = "SearchBot";
		} else {
			$bot_name = "";
		}

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Log Access",__LINE__,"[$keep_logs] [$log_type] [".($keep_logs ."&&". $log_type)."] [$session_identifier]"));
		}
		$now	= $this->libertasGetDate("Y/m/d H:i:s");

		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Log Access to a specific type of log.
		* only if the Config.php file has been told to include this type of log in the stats table
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$user_access_identifier 		= $this->check_parameters($_SESSION,"SESSION_USER_ACCESS_IDENTIFIER",$this->check_parameters($_COOKIE,"SESSION_USER_ACCESS_IDENTIFIER"));
		$aMaxLifeTime = ini_get('session.gc_maxlifetime');
		$sql = "select * from user_access 
					inner join user_access_log on user_access.user_access_identifier = user_access_log.access_log_owner
				where 
					user_access_bot_name = '$bot_name' and 
					user_access_ip_address = '$ip' and 
					user_access_browser = '$browser_string' and 
					user_access_client=$this->client_identifier and 
					user_access_session_identifier = '".session_id()."' and ";

		if (strlen($bot_name)>0){
			$n = Date("Y/m/d H:i:s",strtotime("-30 minutes"));
			$sql .= " (access_log_date >= '$n')";
//			$sql .= " ('$n' -  UNIX_TIMESTAMP(access_log_date))";
		} else {
			$n = Date("Y/m/d H:i:s",strtotime("-$aMaxLifeTime seconds"));
			$sql .= " (access_log_date >= '$n')";
	//		DATEDIFF('$n',access_log_date) < 0  ";
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$user_access_identifier = $this->check_parameters($r,"user_access_identifier");;
				$user_identifier = $this->check_parameters($r,"user_access_user_identifier");;
			}
		}
		unset($result);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_access_identifier",__LINE__,"$user_access_identifier"));
		}
		if ($user_identifier==0){
			$user_identifier=-1;
		}
		//print $user_identifier ." ". session_id()."]";;
		if ($user_access_identifier==""){
			$sql= "insert into user_access (
				user_access_client,
				user_access_browser,
				user_access_domain,
				user_access_ip_address,
				user_access_referer,
				user_access_user_identifier,
				user_access_session_identifier,
				user_access_accept_language,
				user_access_bot_name
			) values (
				'$this->client_identifier', 
				'$browser_string',
				'".$this->domain_identifier."', 
				'$ip', 
				'$referer', 
				'$user_identifier',
				'".session_id()."',
				'$user_access_accept_language',
				'$bot_name'
			)";
			
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Log Access",__LINE__,"$sql"));
			}
			$this->call_command("DB_QUERY",array($sql));
			$sql = "select * from user_access where 
				user_access_client='$this->client_identifier' and
				user_access_browser='$browser_string' and
				user_access_domain='$this->domain_identifier' and 
				user_access_ip_address='$ip' and
				user_access_referer='$referer' and
				user_access_user_identifier='$user_identifier' and
				user_access_session_identifier='".session_id()."'";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($result){
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
				$user_access_identifier = $this->check_parameters($r,"user_access_identifier");
				
				$_SESSION["SESSION_USER_ACCESS_IDENTIFIER"] = $user_access_identifier;
				$_SESSION["SESSION_SOURCE"] 				= $referer;
				$_SESSION["SESSION_LANGUAGE"] 				= $user_access_accept_language;
				
			}
		}
		$sql_string = $this->extract_array($qstring);
		$script = $_SERVER["PHP_SELF"];//$this->parent->script;
		//$_SESSION["SESSION_USER_ACCESS_IDENTIFIER"] = $user_access_identifier;
//		setcookie("SESSION_USER_ACCESS_IDENTIFIER", $user_access_identifier, 0,"/");
//		$_COOKIE["SESSION_USER_ACCESS_IDENTIFIER"] = $user_access_identifier;
		if (strlen($script)==0){
			$script="/index.php";
		}
		$sql = "
				INSERT INTO user_access_log (
					access_log_client,
			 		access_log_owner, 

					access_log_date, 
					access_log_url, 
					access_log_query_string, 
					access_log_type
				) VALUES (
					'$this->client_identifier', 
					'$user_access_identifier', 
					'$now', 
					'".$script."', 
					'$sql_string',
					'".$this->module_constants[$log_type]."'
				)";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Log Access",__LINE__,"here2"));
		}
		$this->call_command("DB_QUERY",array($sql));
		$sql = "select * from user_access_ip_lookup inner join user_access_countries on tld = access_country where access_ip = '$ip'";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$_SESSION["SESSION_COUNTRY"]	= $r["Country"];
		} else {
			$user_access_ip_address_number = $this->get_number_from_ip($ip);
			$sql="select country_code, country from user_access_ip_2_country 
				inner join user_access_countries on tld = country_code
			where low_ip<=".$user_access_ip_address_number." and high_ip>=".$user_access_ip_address_number.";";
			$country_result = $this->call_command("DB_QUERY",array($sql));
			$country="";
			while($record = $this->call_command("DB_FETCH_ARRAY",array($country_result))){
				$country 						= $record["country_code"];
				$_SESSION["SESSION_COUNTRY"]	= $this->check_parameters($r,"country");
				$sql="insert into user_access_ip_lookup (access_ip, access_country) values ('$ip', '$country');";
				$country_result = $this->call_command("DB_QUERY",array($sql));
			}
//			$_SESSION["YOUR_COUNTRY"]=strtolower($country);
		}
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
function update_now($parameters){
		$page = $this->check_parameters($parameters,"page",1);
		if ($page==1){
			$sql = "delete from user_access_ip_lookup;";
			$this->call_command("DB_QUERY",array($sql));
		}
		$sql = "select distinct user_access_ip_address, user_access_ip_lookup.* from user_access left outer join user_access_ip_lookup on access_ip = user_access_ip_address where access_ip is null";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($result){
			$num_of_rows = $this->call_command("DB_NUM_ROWS",array($result));
			if ($num_of_rows>0){
				$c=0;
				while(($r = $this->call_command("DB_FETCH_ARRAY",array($result))) && ($c<$this->page_size)){
					$c++;
					$user_access_ip_address = $r["user_access_ip_address"];
					$user_access_ip_address_number = $this->get_number_from_ip($r["user_access_ip_address"]);
					$sql="select country_code from user_access_ip_2_country where low_ip<=".$user_access_ip_address_number." and high_ip>=".$user_access_ip_address_number.";";
					$country_result = $this->call_command("DB_QUERY",array($sql));
					$country = "N.A.";
					while($record = $this->call_command("DB_FETCH_ARRAY",array($country_result))){
						$country = $this->check_parameters($record,"country_code","N.A.");
					}
					$sql="insert into user_access_ip_lookup (access_ip, access_country) values ('$user_access_ip_address', '$country');";
					$country_result = $this->call_command("DB_QUERY",array($sql));
				}
				$page++;
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=USERACCESS_RECACHE_IP_LOOKUP&page=$page"));
			}
		}
	}
	function update_referals_now($parameters){
		$page = $this->check_parameters($parameters,"page",1);
		if ($page==1){
			$sql = "delete from user_access_referals;";
			$this->call_command("DB_QUERY",array($sql));
		}
		$sql = "select distinct user_access_referer from user_access";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($result){
			$num_of_rows = $this->call_command("DB_NUM_ROWS",array($result));
			if ($num_of_rows>0){
				$c=0;
				while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$c++;
					$url = $r["user_access_referer"];
					$referal = split("/",$url);
					$sql = "insert into user_access_referals (referal_url) values ( '$url');";
					$this->call_command("DB_QUERY",array($sql));
				}
			}
		}
	}
	
}
?>