<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.users.php
* @date 09 Oct 2002
*/
/**
* this module contains functions for building reports
*/
class user_access extends module{
	/**
	*  Class Variables
	*/
	var $module_name			= "user_access";
	var $module_name_label		= "Site Stat log (Administration)";
	var $module_version 		= '$Revision: 1.14 $';
	var $module_grouping		= "LOCALE_MANAGEMENT_GROUP_REPORTS";
	var $module_admin			= "1";
	var $module_debug			= false;
	var $module_command			= "USERACCESS_"; 		// all commands specifically for this module will start with this token
	var $module_label			= "MANAGEMENT_LOG";
//	var $module_admin_options 	= array("<option command=\"user_access_LIST_WEEK\">Show stats for this week</option>","<option command=\"user_access_LIST_MONTH\">Show stats for this month</option>");

	var $module_display_options=Array();
	var $module_admin_options = array();
	var $module_admin_user_access = array();
	var $module_reports = Array();
	var $module_constants = Array(
		"__LOG_PAGE_ACCESS__" => 1,
		"__LOG_PAGE_EMAILED__" => 2,
		"__LOG_FILE_DOWNLOAD__" => 3,
		"__LOG_FILE_DOWNLOAD_NO_FILE__" => 4,
		"__LOG_LOGIN_FAILED__" => 5,
		"__LOG_PAGE_EMAIL_BLOCKED__" => 6,
		"__LOG_FILE_LOGIN_REQUIRED__" => 7
		
	);
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    - Module Preferences
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	var $preferences= Array(
			Array('sp_generate_site_logs'		,"LOCALE_SP_GENERATE_SITE_LOGS"						,'Yes'	, 'Yes:No'					, "USERACCESS_",	"ALL"),
			Array('sp_generate_admin_logs'		,"LOCALE_SP_GENERATE_ADMIN_LOGS"					,'No'	, 'Yes:No'					, "USERACCESS_",	"ALL")
	);
	var $admin_access=0;
	var $sql='';
	var $result='';
	var $r='';
	var $site_access_logs_generate='';
	
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
			if ($user_command==$this->module_command."GET_PREFS"){
				return $this->preferences;
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
				return $this->get_module_version();
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				/*** Written By Ali Imran Ahmad for system site logs prefrences 07-05-2007***/
			   $sql = "SELECT system_preference_value FROM system_preferences WHERE system_preference_client='".$this->parent->client_identifier."' AND system_preference_name='sp_generate_site_logs'";
				$result = $this->call_command("DB_QUERY",array($sql));
				if ($result){
						$r = $this->call_command("DB_FETCH_ARRAY",array($result));
						$site_access_logs_generate = $r['system_preference_value'];	
				}
				if($site_access_logs_generate != 'No')
					return $this->create_table();
			}
			if ($user_command==$this->module_command."LOG"){
				return $this->LogAccess($parameter_list);
			}
			if ($user_command==$this->module_command."UPDATE"){
				$this->update_user($parameter_list[0],$parameter_list[1]);
				return 1;
			}
			if ($this->admin_access==1){
				if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
					if ($user_command==$this->module_command."VIEW_TIMED"){
						return $this->retrieve_today_timed($parameter_list);
					}
					if ($user_command==$this->module_command."TRACE_SESSION"){
						return $this->retrieve_trace_session($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_MOST_ACTIVE_SESSIONS"){
						return $this->retrieve_most_active_sessions($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_COUNTRIES"){
						return $this->display_countries($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_FILTERED_COUNTRIES"){
						return $this->display_view_filtered_on_country($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_LANGUAGES"){
						return $this->display_language($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_FILTERED_LANGUAGE"){
						return $this->display_view_filtered_on_language($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_ENTRY_POINTS"){
						return $this->get_entry_exit_points($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_EXIT_POINTS"){
						return $this->get_entry_exit_points($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_THIS_DAY"){
						return $this->display_a_specific_day($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_FILTERED_REFERER"){
						return $this->display_view_filtered_on_referer($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_REFERER_LIST"){
						return $this->display_view_referer_list($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_CONTACT"){
						return $this->view_contact($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_USERS"){
						return $this->display_current_users($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_HOSTS"){
						return $this->display_hosts($parameter_list);
					}
					if ($user_command==$this->module_command."FILTER_HOST"){
						return $this->display_filtered_hosts($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_VISITORS"){
						return $this->display_visitors($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_FILTERED_BROWSER"){
						return $this->display_filtered_browsers($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_FILTERED_BROWSER_STRING"){
						return $this->display_filtered_browser_string($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_DOMAIN"){
						return $this->display_domain($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_FILE_DOWNLOADS"){
						return $this->display_file_downloads($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_BOTS"){
						return $this->display_bot_access($parameter_list);
					}
					if ($user_command==$this->module_command."VIEW_MOST_POPULAR"){
						return $this->display_most_popular($parameter_list);
					}
					if ($user_command==$this->module_command."DISPLAY_FILTER"){
						return $this->display_form($parameter_list);
					}
					if ($user_command==$this->module_command."EXTRACT_DATE_CONDITION"){
						return $this->display_date_condition($parameter_list);
					}
				}
				if ($user_command==$this->module_command."GENERATE_LINKS"){
					return $this->generate_links();
				}
				if ($user_command==$this->module_command."VIEW_SUMMARY"){
					/*** Written By Ali Imran Ahmad for system site logs prefrences 07-05-2007***/
					$sql = "SELECT system_preference_value FROM system_preferences WHERE system_preference_client='".$this->parent->client_identifier."' AND system_preference_name='sp_generate_site_logs'";
					$result = $this->call_command("DB_QUERY",array($sql));
					if ($result){
						$r = $this->call_command("DB_FETCH_ARRAY",array($result));
						$site_access_logs_generate = $r['system_preference_value'];	
					}
					if($site_access_logs_generate == 'No')
						return $this->retrieve_summary_serverlogs($parameter_list);
					else
						return $this->retrieve_summary_sitelogs($parameter_list);
				}
				if ($user_command==$this->module_command."VIEW_WEEK"){
					return $this->display_week($parameter_list);
				}
				if ($user_command==$this->module_command."VIEW_MONTH"){
					return $this->display_month($parameter_list);
				}
				if ($user_command==$this->module_command."VIEW_YEAR_STATS"){
					return $this->current_year();
				}
				if ($user_command==$this->module_command."VIEW_REFERERS"){
					return $this->display_referers($parameter_list);
				}
				if ($user_command==$this->module_command."VIEW_BROWSERS"){
					return $this->display_browsers($parameter_list);
				}
				if ($user_command==$this->module_command."VIEW_OS"){
					return $this->display_os($parameter_list);
				}
				if ($user_command==$this->module_command."VIEW_KEYWORDS"){
					return $this->display_search_keywords($parameter_list);
				}
				if ($user_command==$this->module_command."RECACHE_IP_LOOKUP"){
					$this->update_now($parameter_list);
				}
				if ($user_command==$this->module_command."RECACHE_REFERALS"){
					$this->update_referals_now($parameter_list);
				}
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				$out = $this->module_admin_access_options(0);
				return $out;
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
		$ALL=0;
		if ($this->parent->module_type=="admin") {
			for($i=0;$i < $max_grps; $i++){
				$access = $grp_info[$i]["ACCESS"];
				$access_length = count($access);
				$out = "";
				for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
					if (("ALL"==$access[$index]) || ("USERACCESS_ALL"==$access[$index])){
						$this->admin_access = 1;
					}
				}
			}
		}
		$this->client_identifier = $this->parent->client_identifier;
		$this->domain_identifier=$this->call_command("CLIENT_GET_DOMAIN_IDENTIFIER");
		/**
		* define some access functionality
		*/


		$this->module_admin_options	= array();
		
		/********Change by Ali Imran for site logs access ******////////
		$sql = "SELECT system_preference_value FROM system_preferences WHERE system_preference_client='".$this->parent->client_identifier."' AND system_preference_name='sp_generate_site_logs'";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($result){
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
				$site_access_logs_generate = $r['system_preference_value'];	
		}
		if($site_access_logs_generate == 'No'){
			$this->module_admin_options[count($this->module_admin_options)] = array($this->module_command."VIEW_SUMMARY", LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL);
		}
		else{
				$this->module_admin_options[count($this->module_admin_options)] = array($this->module_command."VIEW_SUMMARY", LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL);
				
				$this->module_admin_options[count($this->module_admin_options)] = array($this->module_command."VIEW_REFERERS", LOCALE_USER_ACCESS_REPORT_TYPE_ACCESS);
				$this->module_admin_options[count($this->module_admin_options)] = array($this->module_command."VIEW_BROWSERS", LOCALE_USER_ACCESS_REPORT_TYPE_PROFILE);
				if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
					$this->module_admin_options[count($this->module_admin_options)] = array("FILES_VIEW_DOWNLOAD_REPORT&amp;show=file", LOCALE_USER_ACCESS_REPORT_TYPE_FILES);
				}
				$this->module_admin_options[count($this->module_admin_options)] = array($this->module_command."VIEW_KEYWORDS", LOCALE_USER_ACCESS_REPORT_TYPE_SEARCH);
		}		
		
		$this->module_admin_user_access		= array(
			array($this->module_command."ALL","COMPLETE_ACCESS")/*,
			array($this->module_command."MOST_ACTIVE_SESSIONS","Most Active Sessions"),
			array($this->module_command."NUM_OF_DAYS","View this week"),
			array($this->module_command."VIEW_YEAR_STATS","View the last 12 Months"),
			array($this->module_command."VIEW_REFERERS","View the top 20 referal sites."),
			array($this->module_command."VIEW_USERS","View current users."),
			array($this->module_command."VIEW_BROWSERS","View active browsers."),
			array($this->module_command."VIEW_OS","View Operating System Report.")*/
		);
		
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
		* Table structure for table 'documents'
		*/
	
		$fields = array(
			array("user_access_identifier"			,"unsigned integer"	,"NOT NULL"	,"auto_increment"	,"key"),
			array("user_access_client"				,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("user_access_domain"				,"unsigned integer"	,"NOT NULL"	,"default '0'"		,"key"),
			array("user_access_browser"				,"varchar(255)"		,""			,"default ''"),
			array("user_access_platform"			,"varchar(50)"		,"NOT NULL"	,"default ''"),
			array("user_access_ip_address"			,"varchar(15)"		,""			,"default ''"		,"key"),
			array("user_access_reverse_dns_lookup"	,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("user_access_referer"				,"varchar(255)"		,"NOT NULL"	,"default ''"		,"key"),
			array("user_access_accept_language"		,"varchar(10)"		,""			,"default ''"),
			array("user_access_user_identifier"		,"integer"			,"NOT NULL"	,"default '-1'"		,"key"),
			array("user_access_session_identifier"	,"varchar(50)"		,"NOT NULL"	,"default '0'"),
			array("user_access_referal_qstring"		,"text"				,""			,"default ''"),
			array("user_access_bot_name"			,"varchar(50)"		,""			,"default ''")
			
		);
		$primary ="user_access_identifier";
		$tables[count($tables)] = array("user_access", $fields, $primary);

		$fields = array(
			array("access_log_owner"			,"unsigned integer"	,"NOT NULL"	,"default '0'","key"),
			array("access_log_client"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("access_log_date"				,"datetime"			,"NOT NULL"	,"default '0000-00-00 00:00:00'"),
			array("access_log_url"				,"varchar(255)"		,"NOT NULL"	,""),
			array("access_log_query_string"		,"text"				,""			,""),
			array("access_log_type"				,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		
		$primary ="";
		$tables[count($tables)] = array("user_access_log", $fields, $primary);

		$fields = array(
			array("access_ip"			,"varchar(15)"	,"NOT NULL"	,"default '0.0.0.0'"	,"key"),
			array("access_country"		,"varchar(2)"	,"NOT NULL"	,"default ''")
		);
		
		$primary ="";
		$tables[count($tables)] = array("user_access_ip_lookup", $fields, $primary);
		
		
		$fields = array(
			array("low_ip"			,"unsigned integer"	,"NOT NULL"	,"default ''"),
			array("high_ip"			,"unsigned integer"	,"NOT NULL"	,"default ''"),
			array("country_code"	,"varchar(2)"		,"NOT NULL"	,"default ''")
		);
		
		$primary ="";
		$tables[count($tables)] = array("user_access_ip_2_country", $fields, $primary);
		
		
		$fields = array(
			array("tld"				,"varchar(5)"		,"NOT NULL"	,"default ''"	,"key"),
			array("country"			,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("fips104"			,"varchar(5)"		,"NOT NULL"	,"default ''"),
			array("iso2"			,"varchar(5)"		,"NOT NULL"	,"default ''"),
			array("iso3"			,"varchar(5)"		,"NOT NULL"	,"default ''"),
			array("isono"			,"unsigned integer"	,"NOT NULL"	,"default ''"),
			array("capital"			,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("region"			,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("currency"		,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("currencycode"	,"varchar(5)"		,"NOT NULL"	,"default ''")
		);
		
		$primary ="tld";
		$tables[count($tables)] = array("user_access_countries", $fields, $primary);

		
		$fields = array(
			array("referal_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"	,"key"),
			array("referal_url"			,"varchar(255)"		,"NOT NULL"	,"default ''")
		);
		
		$primary ="referal_identifier";
		$tables[count($tables)] = array("user_access_referals", $fields, $primary);
		
		return $tables;
	}
	
	function update_now($parameters){
		$page = $this->check_parameters($parameters,"page",1);
//		////print "$page ";
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
//		////print "$page ";
		if ($page==1){
			$sql = "delete from user_access_referals;";
			$this->call_command("DB_QUERY",array($sql));
		}
		$sql = "select distinct user_access_referer from user_access";
//		////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($result){
			$num_of_rows = $this->call_command("DB_NUM_ROWS",array($result));
			if ($num_of_rows>0){
				$c=0;
				while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$c++;
					$url = $r["user_access_referer"];
					$referal = split("/",$url);
//					////print count($referal)." ";
					$sql = "insert into user_access_referals (referal_url) values ( '$url');";
					$this->call_command("DB_QUERY",array($sql));
				}
//				$page++;
//				$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=USERACCESS_RECACHE_IP_LOOKUP&page=$page"));
			}
		}
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
		$user_identifier				= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER");
		$session_identifier				= session_id();
		$session_id 					= $this->check_parameters($parameters[1],"my_session_identifier");
//		print md5("SearchBot - Google")." ";
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
			$bot_name ="";
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
		$user_access_identifier 		= $this->check_parameters($_SESSION,"SESSION_user_access_IDENTIFIER",$this->check_parameters($_COOKIE,"SESSION_user_access_IDENTIFIER"));
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
//		print $sql;
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$user_access_identifier = $this->check_parameters($r,"user_access_identifier");;
			}
		}
		unset($result);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_access_identifier",__LINE__,"$user_access_identifier"));
		}
		if ($user_identifier==0){
			$user_identifier=-1;
		}
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
				//$_SESSION["SESSION_user_access_IDENTIFIER"] = $user_access_identifier;
				//setcookie("SESSION_user_access_IDENTIFIER", $user_access_identifier, time()+10800,"/");
//				$_COOKIE["SESSION_user_access_IDENTIFIER"] = $user_access_identifier;
//				print $_SESSION["SESSION_user_access_IDENTIFIER"];
				//$this->call_command("SESSION_SET",Array("SESSION_user_access_IDENTIFIER",$user_access_identifier));
			}
		}
		$sql_string = $this->extract_array($qstring);
		$script = $_SERVER["PHP_SELF"];//$this->parent->script;
		//$_SESSION["SESSION_user_access_IDENTIFIER"] = $user_access_identifier;
//		setcookie("SESSION_user_access_IDENTIFIER", $user_access_identifier, 0,"/");
//		$_COOKIE["SESSION_user_access_IDENTIFIER"] = $user_access_identifier;
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
		$sql = "select * from user_access_ip_lookup where access_ip = '$ip'";
		//print $sql;
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
//			$_SESSION["YOUR_COUNTRY"] = strtolower($r["access_country"]);
		} else {
			$user_access_ip_address_number = $this->get_number_from_ip($ip);
			$sql="select country_code from user_access_ip_2_country where low_ip<=".$user_access_ip_address_number." and high_ip>=".$user_access_ip_address_number.";";
		//print $sql;
			$country_result = $this->call_command("DB_QUERY",array($sql));
			$country="";
			while($record = $this->call_command("DB_FETCH_ARRAY",array($country_result))){
				$country = $record["country_code"];
				$sql="insert into user_access_ip_lookup (access_ip, access_country) values ('$ip', '$country');";
				$this->call_command("DB_QUERY",array($sql));
			}
			$this->call_command("DB_FREE",array($country_result));
//			$_SESSION["YOUR_COUNTRY"]=strtolower($country);
		}
		$this->call_command("DB_FREE",array($result));
	}
	
	function clean($s){
		return strip_tags (implode ("''", explode ("'",$s)));
	}
	
	function update_user($user_identifier,$session_identifier){
		$sql="update user_access set user_access_user_identifier='$user_identifier' where user_access_session_identifier='$session_identifier'";
		$this->call_command("DB_QUERY",array($sql));
	}
	
	
	
	function retrieve_most_active_sessions($parameters){
		$limit=$this->check_parameters($parameters,"LIMIT",25);
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$form = $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$list_results=Array();
		$months = array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);
		$sql="
			SELECT
			    user_access.user_access_identifier, 
				user_access.user_access_user_identifier, 
				contact_data.contact_first_name, 
				contact_data.contact_last_name, 
                COUNT(user_access_log.access_log_owner) AS total,
				min(user_access_log.access_log_date) as min_date,
				max(user_access_log.access_log_date) as max_date,
				user_access_ip_address,
				user_access_session_identifier,
				language_code,
				language_label,
				access_country,
				country
			FROM user_access 
			INNER JOIN
            	user_access_log ON user_access_log.access_log_owner = user_access.user_access_identifier 
			inner join user_access_ip_lookup on user_access_ip_address = access_ip 
			inner join user_access_countries on tld = access_country 
			left outer join available_languages on user_access_accept_language = available_languages.language_code 
			LEFT OUTER JOIN
                      contact_data ON contact_data.contact_user = user_access.user_access_user_identifier
			WHERE
				(user_access_log.access_log_client = $this->client_identifier)
				and user_access_bot_name=''
				$date_condition
			GROUP BY 
				user_access_session_identifier, 
				user_access_log.access_log_owner, 
				user_access.user_access_identifier, 
				user_access.user_access_user_identifier, 
				contact_data.contact_first_name, 
				contact_data.contact_last_name,
				user_access_ip_address,

				language_code,
				language_label,
				access_country,
				country

			ORDER BY total DESC";
//		print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out="";
		$total =0;
		if ($result){
			$total =0;
			$c=0;
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($c<$limit)){
			$c++;
				$number_of_hits = $r["total"];
				$user			= $r["user_access_user_identifier"];
				$id 			= $r["user_access_identifier"];
				$language_code 			= strtolower($this->check_parameters($r,"language_code","N.A."));
				$country	 			= strtolower($this->check_parameters($r,"access_country","N.A."));
				$country_label 			= $this->check_parameters($r,"country","N.A.");
				$language_label 			= $this->check_parameters($r,"language_label","unknown");
				if (($this->check_parameters($r,"contact_first_name")!="")&&($this->check_parameters($r,"contact_last_name")!="")){
					$users_full_name = $r["contact_first_name"].", ".$r["contact_last_name"];
				} else {
					$users_full_name ="Undefined";
				}
				if (strpos($language_code,'-')>0){
					$list = split('-',$language_code);
					$language_code = $list[1];
				} else {
					$language_code=$language_code;
				}

				$users_ip = $r["user_access_ip_address"];
				$time = $this->timetodescription(strtotime($r["max_date"])-strtotime($r["min_date"]));
 				$out .= "
					<stat_entry>
						<attribute name=\"C\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_ADDRESS_COUNTRY." $country_label'><![CDATA[$country]]></attribute>
						<attribute name=\"L\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_LANGUAGE." $language_label'><![CDATA[$language_code]]></attribute>
					";
				if ($users_full_name=="Undefined"){
 				$out .= "
						<attribute name=\"Users Name\" show=\"YES\" link=\"NO\"><![CDATA[$users_full_name]]></attribute>
						";
				} else {
 				$out 		.= "
						<attribute name=\"Users Name\" show=\"YES\" link=\"user_link\"><![CDATA[$users_full_name]]></attribute>
						<attribute name=\"user_link\" show=\"NO\" link=\"NO\"><![CDATA[command=USERACCESS_VIEW_CONTACT&identifier=$user]]></attribute>
						";
				}
 				$out 		.= "
						<attribute name=\"IP Address\" show=\"YES\" link=\"NO\"><![CDATA[$users_ip]]></attribute>
						<attribute name=\"Length on site\" show=\"YES\" link=\"NO\"><![CDATA[$time]]></attribute>
						<attribute name=\"Total\" show=\"BAR\" link=\"access\"><![CDATA[$number_of_hits]]></attribute>
						<attribute name=\"access\" show=\"NO\" link=\"NO\"><![CDATA[command=USERACCESS_TRACE_SESSION&identifier=$id]]></attribute>
					</stat_entry>
				";
//				$out .="<stat_entry record=\"$id\" user=\"$user\" label='$users_full_name' value='$number_of_hits'/>";
				$total += $number_of_hits ;
			}
		}
$page_options ="<page_options><header></header></page_options>".$this->generate_links();		
return "<module name=\"user_access\" display=\"stats\">$page_options$form<stat_results label=\"Most Active Sessions\" total=\"$total\">".$out."</stat_results></module>";
	}
	
	function retrieve_trace_session($parameters){
		$owner=$this->check_parameters($parameters,"identifier",0);
$user_access_ip		 = "";
		$user_access_browser = "";
		$user_access_referer = "";		
		$sql="
			SELECT 
				user_access_log.access_log_url, user_access.user_access_browser, user_access.user_access_ip_address, user_access.user_access_referer, user_access_log.access_log_query_string, user_access_log.access_log_date, user_access.user_access_user_identifier
			FROM user_access_log 
				inner join user_access on user_access.user_access_identifier = user_access_log.access_log_owner
			where 
				access_log_owner=$owner and 
				access_log_client=$this->client_identifier
			order by user_access_log.access_log_date
			";
//////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out="";
		$total =0;
		$start_time =0;
		$end_time =0;
		$user_id=0;
		if ($result){
			$total =0;
			$c=0;
			$datetime=-1;
			$num_rows  = $this->call_command("DB_NUM_ROWS", Array($result));
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$c++;
				$url					= $r["access_log_url"];
				$user_access_browser	= $r["user_access_browser"];
				$user_access_ip			= $r["user_access_ip_address"];
				$user_access_referer	= $r["user_access_referer"];
				$qstr		 			= $r["access_log_query_string"];
				$prev_date 				= strtotime($datetime);
				$datetime				= $r["access_log_date"];
				if ($start_time==0){
					$start_time			= $datetime;
					$user_id			= $r["user_access_user_identifier"];
				}
				if ($c==1 || $c==$num_rows){
					$display_date 		= $datetime;
				}else{
					$cdate = strtotime($datetime);
					$display_date 		= $this->timetodescription($cdate-$prev_date);
				}
				
				$display_url =$url;
				if (strlen($url)>55){
					$display_url 		= substr($url,0,15)."........".substr($url,strlen($url)-25);
				}
				$out .= "
					<stat_entry>
						<attribute name=\"URL\" show=\"YES\" link=\"QUERY_STRING\"><![CDATA[$display_url]]></attribute>
						<attribute name=\"Date &amp; Time\" show=\"YES\" link=\"NO\"><![CDATA[$display_date]]></attribute>
						<attribute name=\"QUERY_STRING\" show=\"NO\" link=\"NO\"><![CDATA[http://".$this->parent->domain."$url?$qstr]]></attribute>
					</stat_entry>
				";
			}
			$end_time=$datetime;
		}
$page_options ="".$this->generate_links();
		if ($user_id==0){
			$user_fullname="Unknown";
		}else{
			$user_fullname=$this->call_command("CONTACT_GET_NAME",array("contact_user" => $user_id));
		}
		
		$page_options .= "<text><![CDATA[
			Session started by user ".$user_fullname." with ip address of <strong>".$user_access_ip."</strong><br/>
			Browser Information = <strong>".$user_access_browser."</strong><br/>
			Referer information = <strong>".$user_access_referer."</strong><br/>
			Started at <strong>$start_time</strong> and finished at <strong>$end_time.</strong><br/>
			Resulting in a total time on site of <strong>".$this->timetodescription(strtotime($end_time)-strtotime($start_time)).".</strong>
			]]></text>";
		return $this->call_command("CONTACT_VIEW_USER",Array("identifier"=>$user_id))."<module name=\"user_access\" display=\"stats\">$page_options<stat_results label=\"Tracing User Session\" total=\"$total\">".$out."</stat_results></module>";
	
	}
	

	function display_a_specific_day($parameters){
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		$form = $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$sql = "SELECT access_log_date, access_log_url, count(access_log_url) as total_hits FROM user_access_log  inner join user_access on user_access_identifier = access_log_owner 
			WHERE user_access_client=$this->client_identifier and user_access_bot_name='' $date_condition group by access_log_date, access_log_url ORDER BY access_log_url";
		//print "<li> $sql </li>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_a_specific_day",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		$list_results1 = Array("URL"=>Array());
		$list_results2 = Array();
		for ($i=0;$i<24;$i++){
			if($i<10){
				$list_results2["0".$i] = 0;
			} else {
				$list_results2["".$i] = 0;
			}
		}
		$biggest = 0;
		if ($result){
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$key = date("H",strtotime($r["access_log_date"]));
				if (empty($list_results1["URL"])){
					$list_results1["URL"] = Array($r["access_log_url"] => $r["total_hits"]);
				}else{
					if (empty($list_results1["URL"][$r["access_log_url"]])){
						$list_results1["URL"][$r["access_log_url"]] = $r["total_hits"];
					}else{
						$list_results1["URL"][$r["access_log_url"]] += $r["total_hits"];
					}
				}
				$list_results2[$key] += $r["total_hits"];
				if ($list_results2[$key]>$biggest){
					$biggest=$list_results2[$key];
				}
				$total+=$r["total_hits"];
			}
		}
		$out_2="";
		$out_1="";
		if ($total>0){
			foreach($list_results1["URL"] as $key => $total_hits){
				$out_1.= "<stat_entry>
							<attribute name=\"URL\" show=\"YES\" link=\"NO\"><![CDATA[$key]]></attribute>
							<attribute name=\"Total\" show=\"BAR\" link=\"NO\"><![CDATA[$total_hits]]></attribute>
						</stat_entry>";
			}
			$start=0;
			foreach($list_results2 as $key=>$total_hits){
				if (($key*1)!=$start){
					for ($index=$start+1;$index<($key*1);$index++){
						if ($index<10){
							$v = "0$index";
						}else{
							$v = "$index";
						}
						$out_2.= "<stat_entry>
							<attribute name=\"Time\" show=\"YES\" link=\"NO\"><![CDATA[$v:00]]></attribute>
							<attribute name=\"Total\" show=\"BAR\" link=\"NO\"><![CDATA[0]]></attribute>
						</stat_entry>";
					}
				}
				$start = ($key*1);
				$out_2.= "<stat_entry>
						<attribute name=\"Time\" show=\"YES\" link=\"NO\"><![CDATA[$key:00]]></attribute>
						<attribute name=\"Total\" show=\"BAR\" link=\"NO\"><![CDATA[$total_hits]]></attribute>
					</stat_entry>";
			}
			if ($start!=23){
				for ($index=$start+1;$index<24;$index++){
					if ($index<10){
						$v = "0$index";
					}else{
						$v = "$index";
					}
					$out_2.= "<stat_entry>
						<attribute name=\"Time\" show=\"YES\" link=\"NO\"><![CDATA[$v:00]]></attribute>
						<attribute name=\"Total\" show=\"BAR\" link=\"NO\"><![CDATA[0]]></attribute>
					</stat_entry>";
				}
			}
		} else {
			$out_2="<text><![CDATA[Sorry there are currently no visitors to your site]]></text>";
			$out_1="<text><![CDATA[Sorry there are currently no visitors to your site]]></text>";
			$out.="";
		}
$page_options ="".$this->generate_links();		
return "<module name=\"user_access\" display=\"stats\">$page_options$form
		<stat_results label=\"Web Site Traffic for this day by url\" total=\"$total\">".$out_1."</stat_results>
		</module>";
//		<stat_results label=\"Web Site Traffic for this day by hour\" total=\"$total\" show_counter='0'>".$out_2."</stat_results>

	}
	

	function display_referers($parameters){
		$_filter_year		= $this->check_parameters($parameters, "_filter_year");
		$_filter_month		= $this->check_parameters($parameters, "_filter_month");
		$_filter_day		= $this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		$form 				= $this->display_form($parameters);
		$date_condition 	= $this->display_date_condition($parameters);
		$months 			= array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec");
		
		$domain_list = $this->call_command("CLIENT_GET_DOMAINS");
		$max = count($domain_list);
		$cond = $date_condition;
		for($index=0;$index<$max;$index++){
			$cond .=" and user_access_referer not like '%".$domain_list[$index]."%'";
		}
		$sql = "SELECT 
						user_access_ip_address, 
						user_access_referer, 
						count(user_access_referer) as total
					FROM user_access 
					inner join user_access_log on access_log_owner = user_access_identifier
				where 
					user_access_client=$this->client_identifier and 
					user_access_referer not like '%://".$this->parent->domain."%'
					and user_access_bot_name='' 
					 $cond  
				GROUP BY user_access_identifier, user_access_referer, user_access_ip_address ORDER BY user_access_referer DESC";
		//print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_referers",__LINE__,"$sql"));
		}
		//////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		$result 	= $this->call_command("DB_QUERY", Array($sql));
		$out		= "";
		$total 		= 0;
		$c=0;
		$referer= Array();
		if ($this->call_command("DB_NUM_ROWS", Array($result))>0){
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result)))){
				$c++;
				
				if ($r["user_access_referer"]==-1 || $r["user_access_referer"]=="No Referer"){
					$dom = Array("","",$r["user_access_referer"],"");
					if (empty($referer['No Referer'])){
						$referer['No Referer']["counter"]=1;
						$referer['No Referer']["url"]= array();
					}else{
						$referer['No Referer']["counter"]++;
					}
				} else {	
					$dom = split("/",$r["user_access_referer"]);
					$d2 = $this->check_parameters($dom,2,"");
					if (empty($referer[$d2])){
						$referer[$d2]["counter"] = 1;
						$referer[$d2]["url"] = Array($r["user_access_referer"] => $r["total"]);
					}else{
						$referer[$d2]["counter"]++;
						if (empty($referer[$d2]["url"][$r["user_access_referer"]])){
							$referer[$d2]["url"][$r["user_access_referer"]]=$r["total"];
						}else{
							$referer[$d2]["url"][$r["user_access_referer"]]+=$r["total"];
						}
					}
				}
			}
		}else{
			$out.="<text><![CDATA[Sorry there are currently no Referals to your site this month]]></text>";
		}
		$total = $c;
		$filter = "&_filter_year=".$_filter_year."&_filter_month=".$_filter_month."&_filter_day=".$_filter_day;
		arsort($referer);
		foreach($referer as $key => $value){
			/*
			$summary ="";
			if (count($value["url"])){
				$summary ="<br/><ul><ul>";
				
				foreach($value["url"] as $url => $count){
					$start = strpos($url,"/",8);
					$info = substr($url,$start);
					if (strlen($info)>55){
						$summary.='<li><a target="_external" href="'.$url.'">'.substr($info,0,55).'...</a></li>';
					}else{
						$summary.='<li><a target="_external" href="'.$url.'">'.$info.'</a></li>';
					}
				}
				$summary .="</ul></ul>";
			}
			*/
			if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
			$out .= "
				<stat_entry>";
				if ($key=='No Referer'){
					$out .= "<attribute name=\"Referer\" show=\"YES\" link=\"NO\"><![CDATA[".$key."]]></attribute>";
//					<attribute name=\"Referer List\" show=\"NO\" link=\"NO\"><![CDATA[$summary]]></attribute>";
				} else {
					$out .= "<attribute name=\"Referer\" show=\"YES\" link=\"URL\"><![CDATA[http://".$key."]]></attribute>";
//					<attribute name=\"Referer List\" show=\"SHOW\" link=\"Referer\"><![CDATA[$summary]]></attribute>";
				}
			$out .= "<attribute name=\"Total Visits\" show=\"BAR\" link=\"PAGE_LINK\"><![CDATA[".$value["counter"]."]]></attribute>
					<attribute name=\"PAGE_LINK\" show=\"NO\" link=\"NO\"><![CDATA[command=USERACCESS_VIEW_FILTERED_REFERER&domain=".$key."$filter]]></attribute>
					<attribute name=\"URL\" show=\"NO\" link=\"NO\"><![CDATA[command=USERACCESS_VIEW_REFERER_LIST&domain=".$key."$filter]]></attribute>
				</stat_entry>
			";
			} else {
			$out .= "
				<stat_entry>";
				if ($key=='No Referer'){
					$out .= "<attribute name=\"Referer\" show=\"YES\" link=\"NO\"><![CDATA[".$key."]]></attribute>";
//					<attribute name=\"Referer List\" show=\"NO\" link=\"NO\"><![CDATA[$summary]]></attribute>";
				} else {
					$out .= "<attribute name=\"Referer\" show=\"YES\" link=\"NO\"><![CDATA[http://".$key."]]></attribute>";
//					<attribute name=\"Referer List\" show=\"SHOW\" link=\"Referer\"><![CDATA[$summary]]></attribute>";
				}
			$out .= "<attribute name=\"Total\" show=\"BAR\" link=\"NO\"><![CDATA[".$value["counter"]."]]></attribute>
				</stat_entry>
			";
			}
		}
		$page_options ="".$this->generate_links();		
		return "<module name=\"user_access\" display=\"stats\">$page_options$form<stat_results label=\"Top Referers\" total=\"$total\" >".$out."</stat_results></module>";
	}
	
	
	
	function display_current_users($parameters){
		$filter_minutes = $this->check_prefs(Array("sp_time_out_minutes"));
		
		$now = date("Y/m/d H:i:s",strtotime("-$filter_minutes minutes"));
	
		$sql = "select 
				user_access.user_access_identifier,
				user_access.user_access_ip_address,
				max(user_access_log1.access_log_date) as last_access, 
				min(user_access_log2.access_log_date) as first_access, 
				contact_data.contact_first_name, 
				contact_data.contact_last_name,
				language_code,
				language_label,
				country, access_country
			from 
				user_access 
					inner join user_access_ip_lookup on access_ip = user_access_ip_address 
					inner join user_access_countries on access_country = tld 
					inner join available_languages on user_access_accept_language = language_code
					inner join user_access_log as user_access_log1 on user_access_log1.access_log_owner = user_access_identifier 
					inner join user_access_log as user_access_log2 on user_access_log2.access_log_owner = user_access_identifier 
					left outer join contact_data on contact_data.contact_user = user_access_user_identifier 
			where 
				user_access_client=$this->client_identifier and
				user_access_log1.access_log_date >= '$now'
				and user_access_bot_name='' 
			group by 
				user_access_identifier,
				user_access_ip_address,
				contact_data.contact_first_name,
				contact_data.contact_last_name,
				language_code,
				language_label,
				country
			order by 
				last_access";

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_current_users",__LINE__,"$sql"));
		}
		$result 	= $this->call_command("DB_QUERY", Array($sql));
		$out		= "";
		$total 		= 0;
		if ($this->call_command("DB_NUM_ROWS", Array($result))>0){
			$out.="<text><![CDATA[Number of currently active users in the last $filter_minutes minutes and the number of minutes the user has been on.]]></text>";
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$last_access	= $r["last_access"];
				$first_access	= $r["first_access"];
				$name			= $this->check_parameters($r,"contact_first_name","undefined").", ".$this->check_parameters($r,"contact_last_name","undefined");
				$ip_address 	= $r["user_access_ip_address"];
				$user_id		= $r["user_access_identifier"];
				$country		= strtolower($r["access_country"]);
				$country_label	= $r["country"];
				$language_label = $this->check_parameters($r,"language_label","N.A.");
				if ((strpos($this->check_parameters($r,"language_code","N.A."),"-")-1)>=0){
						$list = split('-',strtolower($this->check_parameters($r,"language_code","N.A.")));
						$lang= $list[1];
					}else{
						$lang=strtolower($this->check_parameters($r,"language_code","N.A."));
					}

				$timing = floor((strtotime($last_access,0)- strtotime($first_access,0))/60);
				$out 		.= "
					<stat_entry>
						<attribute name=\"C\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_ADDRESS_COUNTRY." $country_label'><![CDATA[$country]]></attribute>
						<attribute name=\"L\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_LANGUAGE." $language_label'><![CDATA[$lang]]></attribute>
						<attribute name=\"identifier\" show=\"NO\" link=\"NO\"><![CDATA[identifier=$user_id]]></attribute>
						<attribute name=\"User\" show=\"YES\" link=\"identifier\"><![CDATA[$name]]></attribute>
						<attribute name=\"IP Address\" show=\"YES\" link=\"NO\"><![CDATA[$ip_address]]></attribute>
						<attribute name=\"Time\" show=\"YES\" link=\"NO\"><![CDATA[$timing minutes]]></attribute>
					</stat_entry>
				";
			}
		}else{
			$out.="<text><![CDATA[Sorry there are currently no Active Sessions on your site]]></text>";
		}
		
		$mins = "";
		
$page_options ="".$this->generate_links();		
return "<module name=\"user_access\" display=\"stats\">$page_options <stat_results label=\"User Sessions\" total=\"$total\" link=\"".$this->module_command."TRACE_SESSION\">".$out."</stat_results></module>";
	}



	function display_domain($parameters){
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		$form = $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$domain_list = $this->call_command("CLIENT_GET_DOMAINS");
		$max = count($domain_list);
		$cond="";
		for($index=0;$index<$max;$index++){
			$cond .=" and user_access_referer not like '%".$domain_list[$index]."%'";
		}
		$sql = "SELECT domain_name, COUNT(user_access_identifier) AS total 
					FROM user_access
						inner join domain  on domain_identifier = user_access_domain 
						inner join user_access_log on user_access_identifier = access_log_owner
					where user_access_client=$this->client_identifier and user_access_bot_name='' $date_condition GROUP BY domain_name ORDER BY total DESC";
		//print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_referers",__LINE__,"$sql"));
		}
		$result 	= $this->call_command("DB_QUERY", Array($sql));
		$out		= "";
		$total 		= 0;
		if ($this->call_command("DB_NUM_ROWS", Array($result))>0){
			$out.="<text><![CDATA[This report is only helpful when you have multiple domains pointing at your web site.<br />
			 It allows you to see at a glance which domains are being used and which are not.]]></text>";
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$total_hits = $r["total"];
				$domain_name =  $r["domain_name"];
				$out 		.= "
					<stat_entry>
						<attribute name=\"Domain\" show=\"YES\" link=\"NO\"><![CDATA[$domain_name]]></attribute>
						<attribute name=\"Total\" show=\"BAR\" link=\"NO\"><![CDATA[$total_hits]]></attribute>
					</stat_entry>
				";
				$total		+= $total_hits;
			}
		}else{
			$out.="<text><![CDATA[Sorry there are currently no Referals to your site]]></text>";
		}
		
$page_options ="".$this->generate_links();		
return "<module name=\"user_access\" display=\"stats\">$page_options$form<stat_results label=\"Number of pages viewed by domain name\" total=\"$total\">".$out."</stat_results></module>";
	}
	
	function display_search_keywords(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_search_keywords",__LINE__,""));
		}
		$total=0;
		$out		= "";
		$list = $this->call_command("ENGINE_RETRIEVE",Array("RETRIEVE_SEARCH_KEYWORDS"));
		for ($index=0,$max=count($list);$index<$max;$index++){
			if (strlen($list[$index][1])>0){
				$out.=$list[$index][1];
			}
		}
		$page_options ="".$this->generate_links();		
		return "<module name=\"user_access\" display=\"stats\">$page_options".$out."</module>";
	}
	
	function current_year(){
		$out		= "";
		$total 		= 0;
		$list_results = array();
		$months = array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);
		//$now = date("Y/m/d H:i:s",strtotime("-1 year"));
		$year = date("Y");
		for($month=1;$month<=count($months);$month++){
			$list_results[$year][$month]["LOCALE_STATS_PAGE_HITS"]	= 0;
			$list_results[$year][$month]["LOCALE_STATS_VISITORS"] 	= 0;
		}
		$sql = "
		select 
			count(user_access_log.access_log_owner) as times_this_user, 
			MIN(user_access_log.access_log_date) AS todays_uid,
			YEAR(user_access_log.access_log_date) as year_data,						
			MONTH(user_access_log.access_log_date) as month_data,			
			DAYOFMONTH(user_access_log.access_log_date) as day_data,
			HOUR(access_log_date) as hour_data  
		from user_access_log 
			inner join user_access on user_access_identifier = access_log_owner
		where 
			access_log_client=$this->client_identifier and 
			year(access_log_date) = $year 
 			and user_access_bot_name=''
 		group by user_access_log.access_log_owner,year_data,month_data,day_data,hour_data 
		order by todays_uid";

		//print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_previous_year",__LINE__,"$sql"));
		}
		$result 	= $this->call_command("DB_QUERY", Array($sql));
		if ($result){
			$total	= 0;
			$list = " ";
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$month = $r["month_data"];
				$list_results[$year][$month]["LOCALE_STATS_PAGE_HITS"] += $r["times_this_user"];
				$list_results[$year][$month]["LOCALE_STATS_VISITORS"]++;

			}
		}
	

		$start_year=0;
		$start_month=0;
		foreach ($list_results as $year => $month_list){
			$start_year = $year;
			foreach ($month_list as $m => $current_month){
				$start_month = $m;
				if (($list_results[$year][$m]["LOCALE_STATS_PAGE_HITS"]>0) && ($list_results[$year][$m]["LOCALE_STATS_VISITORS"])){
					$list_results[$year][$m]["LOCALE_STATS_AVERAGE"] = round($list_results[$year][$m]["LOCALE_STATS_PAGE_HITS"] / $list_results[$year][$m]["LOCALE_STATS_VISITORS"],2);
				}
				else {
					$list_results[$year][$m]["LOCALE_STATS_AVERAGE"] = round(0,2);					
				}
			}
		}
		$total_hits=0;
		$totals = array();
		foreach ($list_results as $year => $value){
			foreach ($value as $mth => $result_list){
				$out .= "<stat_entry>
					<attribute name=\"\" show=\"YES\" link=\"LINK\"><![CDATA[".$months[$mth-1].", $year]]></attribute>
					";
				foreach ($result_list as $name => $val){		
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
					if (empty($totals[$name])){
						$totals[$name] = $val;
					}else{
						$totals[$name] += $val;
					}
				}
				$out .= "
					<attribute name=\"LINK\" show=\"NO\" link=\"\"><![CDATA[?command=USERACCESS_VIEW_MONTH&_filter_year=$year&_filter_month=$mth]]></attribute>
				</stat_entry>";
			}
		}
		$biggest=Array();
		$out .= $this->publish_total($totals, "LOCALE_STATS_PAGE_HITS", "LOCALE_STATS_VISITORS");

		foreach ($totals as $name => $val){		
			if (empty($biggest[$name])){
				$biggest[$name] = $val;
				}else{
				if ($val > $biggest[$name] ){
					$biggest[$name] = $val;
				}
			}
		}
		
			$big= "<stat_biggest>
					<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
					";
			foreach ($biggest as $name => $val){		
				$big .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
			}
			$big .= "
					<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
					</stat_biggest>";

		$page_options ="".$this->generate_links();
		$page_options .= "<graphs><graph>2</graph><graph>3</graph><graph>4</graph></graphs>";
		return "<module name=\"user_access\" display=\"stats\">$page_options<stat_results label=\"".LOCALE_STATS_YEAR_BASED."\" total=\"$total\">".$out."$big</stat_results></module>";
	}

	/*
		Function to display the current week and the previous week
	*/
	
	function display_week($parameters){
		$out	= "";
		$list_results = Array();
		$day_list = array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
		$big="";
		$days = date("w");
		$now = date("Y/m/d H:i:s",strtotime("-$days days"));
		$now = date("Y/m/d 00:00:00",strtotime("-$days days"));
		$now_prev = date("Y/m/d 00:00:00",strtotime("-".($days+7)." days"));
		$sql = "
		SELECT	count(user_access_identifier) as total,
				min(access_log_date) as todays_uid
		FROM user_access_log 
			inner join user_access on user_access_identifier = access_log_owner 
		WHERE user_access_client=$this->client_identifier and access_log_date >= '$now' and user_access_bot_name=''
		GROUP BY user_access_identifier
		ORDER BY todays_uid
		";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$total 	= 0;
		if ($result){
			$total	= 0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$day = date("D, jS#\&\_\f\i\l\\t\e\\r\_\y\e\a\\r\=Y\&\_\f\i\l\\t\e\\r\_\m\o\\n\\t\h\=m\&\_\f\i\l\\t\e\\r\_\d\a\y\=d",strtotime($r["todays_uid"]));
				if (empty($list_results[$day]["LOCALE_STATS_PAGE_HITS"])){
					$list_results[$day]["LOCALE_STATS_PAGE_HITS"]	= $r["total"];
					$list_results[$day]["LOCALE_STATS_VISITORS"]	= 1;
				} else {
					$list_results[$day]["LOCALE_STATS_PAGE_HITS"]	+= $r["total"];
					$list_results[$day]["LOCALE_STATS_VISITORS"]	++;
				}
			}
		}
		$this->call_command("DB_FREE",Array($result));
        
		
		foreach($list_results as $key => $list){
			$list_results[$key]["LOCALE_STATS_AVERAGE"]= round($list_results[$key]["LOCALE_STATS_PAGE_HITS"] / $list_results[$key]["LOCALE_STATS_VISITORS"],2);
			
		}

		$total_hits=0;
		$totals = array();
		$biggest = array();
		foreach ($list_results as $day => $value){
			$list = split("#",$day);
			$out .= "<stat_entry>";
			if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
				$out .= "<attribute name=\"\" show=\"YES\" link=\"LINK\"><![CDATA[".$list[0]."]]></attribute>";
			} else {
				$out .= "<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[".$list[0]."]]></attribute>";
			}
			foreach ($value as $name => $val){		
				$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
				if (empty($totals[$name])){
					$totals[$name] = $val;
				}else{
					$totals[$name] += $val;
				}
				if (empty($biggest[$name])){
					$biggest[$name] = $val;
				}else{
					if ($val > $biggest[$name] ){
						$biggest[$name] = $val;
					}
				}
			}
			
			$out .= "
			<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[?command=USERACCESS_VIEW_THIS_DAY".$list[1]."]]></attribute>
			</stat_entry>";
		}
		if (strlen($out)>0){
			$val_average = round($this->check_parameters($totals,"LOCALE_STATS_PAGE_HITS",1) / $this->check_parameters($totals,"LOCALE_STATS_VISITORS",1),2);
			$out .= "<stat_total>
					<attribute name=\"\" show=\"YES\" link=\"\"><![CDATA[".LOCALE_STATS_TOTAL."]]></attribute>
					";
			foreach ($totals as $name => $val){		
				if ($name=="LOCALE_STATS_AVERAGE"){
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val_average]]></attribute>";
				} else {
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
				}
			}
			$out .= "		<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
				</stat_total>";
		}
		$last_7 = $out;
		$out="";
		$sql = "
		SELECT	count(user_access_identifier) as total, 
				min(access_log_date) as todays_uid

FROM user_access_log 
inner join user_access on user_access_identifier = access_log_owner 
WHERE user_access_client=$this->client_identifier and access_log_date >= '$now_prev'
and access_log_date < '$now'  and user_access_bot_name=''
GROUP BY user_access_identifier
ORDER BY todays_uid
";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$total 	= 0;
		$c=0;
		$list_results=Array();
		if ($result){
			$total	= 0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$c++;
				$day = date("D, jS#\&\_\f\i\l\\t\e\\r\_\y\e\a\\r\=Y\&\_\f\i\l\\t\e\\r\_\m\o\\n\\t\h\=m\&\_\f\i\l\\t\e\\r\_\d\a\y\=d",strtotime($r["todays_uid"]));
				if (empty($list_results[$day]["LOCALE_STATS_PAGE_HITS"])){
					$list_results[$day]["LOCALE_STATS_PAGE_HITS"] = $r["total"];
					$list_results[$day]["LOCALE_STATS_VISITORS"]=1;
				} else {
					$list_results[$day]["LOCALE_STATS_PAGE_HITS"] += $r["total"];
					$list_results[$day]["LOCALE_STATS_VISITORS"]++;
				}
			}
		}
		if ($c!=0){
			foreach($list_results as $key => $list){
				$list_results[$key]["LOCALE_STATS_AVERAGE"]= round($this->check_parameters($list,"LOCALE_STATS_PAGE_HITS",1) / $this->check_parameters($list,"LOCALE_STATS_VISITORS",1),2);
			}

			$total_hits=0;
			$totals = array();
			foreach ($list_results as $day => $value){
				$list = split("#",$day);
			$out .= "<stat_entry>";
			if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
				$out .= "<attribute name=\"\" show=\"YES\" link=\"LINK\"><![CDATA[".$list[0]."]]></attribute>";
			} else {
				$out .= "<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[".$list[0]."]]></attribute>";
			}
				foreach ($value as $name => $val){		
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
					if (empty($totals[$name])){
						$totals[$name] = $val;
					}else{
						$totals[$name] += $val;
					}
					if (empty($biggest[$name])){
						$biggest[$name] = $val;
					}else{
						if ($val > $biggest[$name] ){
							$biggest[$name] = $val;
						}
					}
				}
				
				$out .= "
				<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[?command=USERACCESS_VIEW_THIS_DAY&identifier=".$list[1]."]]></attribute>
				</stat_entry>";
			}
			
		}
		if (strlen($out)>0){
			$out .= $this->publish_total($totals, "LOCALE_STATS_PAGE_HITS", "LOCALE_STATS_VISITORS");
			$big= "<stat_biggest>
					<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
					";
			foreach ($biggest as $name => $val){		
				$big .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
			}
			$big .= "<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute></stat_biggest>";
		}
		$page_options ="".$this->generate_links();
		
		$page_options .= "<graphs><graph>2</graph><graph>3</graph><graph>4</graph></graphs>";

		$this_week="";
		if (strlen($last_7)>0){
			$this_week = "<stat_results label=\"Web Site Traffic for this week\" total=\"$total\" link=\"".$this->module_command."VIEW_THIS_DAY\">".$last_7."$big</stat_results>";
		}
		$prev_week = "<stat_results label=\"Previous week for comparison\" total=\"$total\" link=\"".$this->module_command."VIEW_THIS_DAY\">".$out."$big</stat_results></previous>";
		$output ="<module name=\"user_access\" display=\"stats\">$page_options".$this_week;
		if ($c!=0){
			$output .="	<previous><graphs><graph>2</graph><graph>3</graph><graph>4</graph></graphs>$prev_week";
		}
		$output .="</module>";
		return $output;
	}
	/*************************************************************************************************************************
    * display the specified months stats
    *************************************************************************************************************************/
	function display_month($parameters){
		$page_options ="";
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
		}else{
		$parameters["_filter_no_days"]=0;
		}
		$form = $this->display_form($parameters);
		$timestampformonth = mktime(1,1,1,$_filter_month,1,$_filter_year);
		$date_condition = $this->display_date_condition($parameters);
		$list_results= Array();
		$months = array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);

		$sql = "SELECT 
				count(access_log_owner) as total, 
				min(access_log_date) as todays_uid, 
				DAYOFMONTH(access_log_date) as day_data ,
				HOUR(access_log_date) as hour_data
			FROM user_access_log 
				inner join user_access on user_access_identifier = access_log_owner
			WHERE 
				access_log_client=$this->client_identifier 
				$date_condition
				and user_access_bot_name=''
			GROUP BY access_log_owner,day_data,hour_data order by todays_uid";
		//print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				if ($_filter_year==''){
					$index_key = date("D, jS M Y_Y/m/d",strtotime($r["todays_uid"]));
				}else if ($_filter_month==''){
					$index_key = date("D, jS M Y_Y/m/d",strtotime($r["todays_uid"]));
				}else{
					$index_key = date("D, jS_Y/m/d",strtotime($r["todays_uid"]));
				}
				if (empty($list_results[$index_key]["LOCALE_STATS_PAGE_HITS"])){
					$list_results[$index_key]["LOCALE_STATS_PAGE_HITS"] = $r["total"];
					$list_results[$index_key]["LOCALE_STATS_VISITORS"]=1;
				}else{				
					$list_results[$index_key]["LOCALE_STATS_PAGE_HITS"] += $r["total"];
					$list_results[$index_key]["LOCALE_STATS_VISITORS"]++;
				}
			}
		}

		foreach($list_results as $key => $list){
			$list_results[$key]["LOCALE_STATS_AVERAGE"] = round($list_results[$key]["LOCALE_STATS_PAGE_HITS"] / $list_results[$key]["LOCALE_STATS_VISITORS"],2);
		}
		$counter=0;
		$totals = array();
		$biggest = array();
		foreach ($list_results as $day => $value){
			$counter++;
			$list = split("_",$day);
			$date_filter = date("\&\_\f\i\l\\t\e\\r\_\y\e\a\\r\=Y\&\_\f\i\l\\t\e\\r\_\m\o\\n\\t\h\=m\&\_\f\i\l\\t\e\\r\_\d\a\y\=d", strtotime($list[1]." 00:00:00"));
			$out .= "<stat_entry>";
			if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
				$out .= "<attribute name=\"\" show=\"YES\" link=\"LINK\"><![CDATA[".$list[0]."]]></attribute>";
			} else {
				$out .= "<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[".$list[0]."]]></attribute>";
			}

			foreach ($value as $name => $val){		
				$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
				if (empty($totals[$name])){
					$totals[$name] = $val;
				}else{
					$totals[$name] += $val;
				}
				if (empty($biggest[$name])){
					$biggest[$name] = $val;
				}else{
					if ($val > $biggest[$name] ){
						$biggest[$name] = $val;
					}
				}
			}
			$out .= "<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[?command=USERACCESS_VIEW_THIS_DAY$date_filter]]></attribute>
			</stat_entry>";
		}
		if (strlen($out)>0){
		$out .= $this->publish_total($totals, "LOCALE_STATS_PAGE_HITS", "LOCALE_STATS_VISITORS");
		$out .= "<stat_biggest>
				<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
				";
		foreach ($biggest as $name => $val){		
				$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
		}
		$out .= "
					<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
				</stat_biggest>";
		$page_options = "<graphs><graph>2</graph><graph>3</graph><graph>4</graph></graphs>";
		}
		if ($counter==0){
			$out="<text><![CDATA[".LOCALE_STAT_NO_RESULTS."]]></text>";
		}
		
		$page_options .="".$this->generate_links();

		return "<module name=\"user_access\" display=\"stats\">$form$page_options<stat_results label=\"".LOCALE_STATS_DISPLAYING_MONTH."\" total=\"$total\" link=\"".$this->module_command."VIEW_THIS_DAY\">".$out."</stat_results></module>";
	}
	function display_language($parameters){
		$days=$this->check_parameters($parameters,"month",date("Y")."_".date("n"));
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}

		$form 			= $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$list_results=Array();

		$months = array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);

		$sql = "SELECT 
				user_access_identifier,
				count(*) as total, 
				language_label,
				language_code
			FROM user_access_log 
			 inner join user_access on user_access_identifier = access_log_owner 
			 left outer join available_languages on user_access_accept_language = available_languages.language_code
			WHERE 
				user_access_client=$this->client_identifier 
				and user_access_bot_name=''
				$date_condition
			GROUP BY user_access_session_identifier, language_label,
				user_access.user_access_identifier,
				available_languages.language_code
			order by language_label";
//print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
					$flag="";
					if ((strpos($this->check_parameters($r,"language_code","N.A."),"-")-1)>=0){
						$list = split('-',strtolower($this->check_parameters($r,"language_code","N.A.")));
						$flag= $list[1];
					}else{
						$flag=strtolower($this->check_parameters($r,"language_code","N.A."));
					}
					$accept=strtolower($this->check_parameters($r,"language_code","N.A."));
//					////print "$flag <strong>".$r["user_access_identifier"]."</strong> <br>";
					$list_results[$this->check_parameters($r,"language_label","N.A.")]["LOCALE_STATS_FLAG"] = $flag;
					if (empty($list_results[$this->check_parameters($r,"language_label","N.A.")]["LOCALE_STATS_PAGE_HITS"])){
						$list_results[$this->check_parameters($r,"language_label","N.A.")]["LOCALE_STATS_PAGE_HITS"] = $r["total"];
						$list_results[$this->check_parameters($r,"language_label","N.A.")]["LOCALE_STATS_VISITORS"]=1;
					} else {
						$list_results[$this->check_parameters($r,"language_label","N.A.")]["LOCALE_STATS_PAGE_HITS"] += $r["total"];
						$list_results[$this->check_parameters($r,"language_label","N.A.")]["LOCALE_STATS_VISITORS"]++;
					}
					$hidden[$this->check_parameters($r,"language_label","N.A.")] = $accept;
			}
		}
		
		$total_hits=0;
		$totals  = array();
		$biggest = array();
		
		foreach ($list_results as $day => $value){
			$list_results[$day]["LOCALE_STATS_AVERAGE"] = round($list_results[$day]["LOCALE_STATS_PAGE_HITS"] / $list_results[$day]["LOCALE_STATS_VISITORS"],2);
		}
		foreach ($list_results as $day => $value){
			$out .= "<stat_entry>
				<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[$day]]></attribute>";
			foreach ($value as $name => $val){		
				if ($name == "LOCALE_STATS_FLAG"){
					$out .= "<attribute name=\"L\" show=\"FLAG\" alt='".LOCALE_LANGUAGE." ".$day."' link=\"NO\"><![CDATA[$val]]></attribute>";
				}else if ($name == "LOCALE_STATS_PAGE_HITS"){
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"LINK\"><![CDATA[$val]]></attribute>";
				}else{
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
				}
				if (isset($totals[$name])){
					$totals[$name] += $val;
				}else{
					$totals[$name] = $val;
				}
				if (empty($biggest[$name])){
					$biggest[$name] = $val;
				}else{
					if ($val >= $biggest[$name]){
						$biggest[$name] = $val;
					}
				}
			}
			$out .= "
				<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[identifier=".$hidden[$day]."]]></attribute>
				</stat_entry>";
		}
		$big= "<stat_biggest>
				<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
				";
		foreach ($biggest as $name => $val){		
			if (is_numeric($val)){
				$big .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
			}else{
				$big .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[0]]></attribute>";
			}
		}
		$big .= "</stat_biggest>";
		$out .= $this->publish_total($totals, "LOCALE_STATS_PAGE_HITS", "LOCALE_STATS_VISITORS",3).$big;
		$page_options ="".$this->generate_links();
		return "<module name=\"user_access\" display=\"stats\">$form$page_options<stat_results label=\"".LOCALE_STATS_DISPLAYING_LANGUAGES."\" total=\"$total\" link=\"".$this->module_command."VIEW_FILTERED_LANGUAGE&amp;_filter_year=$_filter_year&amp;_filter_month=$_filter_month&amp;_filter_day=$_filter_day\">".$out."</stat_results></module>";
	}
	function display_countries($parameters){
		$days=$this->check_parameters($parameters,"month",date("Y")."_".date("n"));
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		$form 			= $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$list_results=Array();

		$months = array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);

		$sql = "SELECT 
				user_access_identifier,
				count(*) as total, 
				access_country,country,tld
			FROM user_access_log 
			 left outer join user_access on user_access_identifier = access_log_owner 
			 left outer join user_access_ip_lookup on user_access_ip_address = access_ip
			 left outer join user_access_countries on access_country = tld
			WHERE 
				user_access_client=$this->client_identifier 
				$date_condition
			GROUP BY user_access_ip_address,
			user_access.user_access_identifier,
			user_access_ip_lookup.access_country,
			user_access_countries.country,
			tld
			order by country";
//		//print "<p>".__FILE__." line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$list_results[$this->check_parameters($r,"country","N.A.")]["LOCALE_STATS_FLAG"] = strtolower($this->check_parameters($r, "tld", "N.A."));
				if (empty($list_results[$this->check_parameters($r,"country","N.A.")]["LOCALE_STATS_PAGE_HITS"])){
					$list_results[$this->check_parameters($r,"country","N.A.")]["LOCALE_STATS_PAGE_HITS"] = $r["total"];
					$list_results[$this->check_parameters($r,"country","N.A.")]["LOCALE_STATS_VISITORS"]=1;
				} else {
					$list_results[$this->check_parameters($r,"country","N.A.")]["LOCALE_STATS_PAGE_HITS"] += $r["total"];
					$list_results[$this->check_parameters($r,"country","N.A.")]["LOCALE_STATS_VISITORS"]++;
				}
			}
		}
		
		$total_hits=0;
		$totals  = array();
		$biggest = array();
		foreach ($list_results as $day => $value){
//			if (($c>=$start) &&($c<$start+25)){
				$list_results[$day]["LOCALE_STATS_AVERAGE"] = round($list_results[$day]["LOCALE_STATS_PAGE_HITS"] / $list_results[$day]["LOCALE_STATS_VISITORS"],2);
				$out .= "<stat_entry><attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[$day]]></attribute>";
				foreach ($value as $name => $val){		
					if ($name == "LOCALE_STATS_FLAG"){
						$out .= "<attribute name=\"C\" show=\"FLAG\" alt='".LOCALE_ADDRESS_COUNTRY." ".$day."' link=\"NO\"><![CDATA[$val]]></attribute>";
					}else if ($name == "LOCALE_STATS_PAGE_HITS"){
						$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"LINK\"><![CDATA[$val]]></attribute>";
					}else{
						$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
					}
					if (isset($totals[$name])){
						$totals[$name] += $val;
					}else{
						$totals[$name] = $val;
					}
					if (empty($biggest[$name])){
						$biggest[$name] = $val;
					}else{
						if ($val >= $biggest[$name]){
							$biggest[$name] = $val;
						}
					}
				}
				$out .= "
					<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[identifier=".$list_results[$day]["LOCALE_STATS_FLAG"]."]]></attribute>
					</stat_entry>";
	//		}

		}
		$big= "<stat_biggest>
				<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
				<attribute name=\"\" show=\"NO\" link=\"\"><![CDATA[]]></attribute>
				";
		foreach ($biggest as $name => $val){		
			if (is_numeric($val)){
				$big .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
			}else{
				$big .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[0]]></attribute>";
			}
		}
		$big .= "</stat_biggest>";
		$out .= $this->publish_total($totals, "LOCALE_STATS_PAGE_HITS", "LOCALE_STATS_VISITORS",3).$big;
		$page_options ="".$this->generate_links();
		return "<module name=\"user_access\" display=\"stats\">$form$page_options<stat_results label=\"".LOCALE_STATS_DISPLAYING_COUNTRIES."\" total=\"$total\" link=\"".$this->module_command."VIEW_FILTERED_COUNTRIES&amp;_filter_year=$_filter_year&amp;_filter_month=$_filter_month&amp;_filter_day=$_filter_day\">".$out."</stat_results></module>";
	}
	
	function publish_total($totals,$page_hits="",$visitors="",$blank=2){
		if ($this->check_parameters($totals,$page_hits,1)>0 && $this->check_parameters($totals,$visitors,1)>0){
			$val_average = round($this->check_parameters($totals,$page_hits,1) / $this->check_parameters($totals,$visitors,1),2);
			$out = "<stat_total>
				<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[".LOCALE_STATS_TOTAL."]]></attribute>
				";
			for ($i = 0; $i<$blank-2;$i++){
				$out .= "<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[]]></attribute>";
			}
			foreach ($totals as $name => $val){		
				if ($name == "LOCALE_STATS_FLAG"){
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"NO\" link=\"NO\"><![CDATA[$val]]></attribute>";
				}else if ($name=="LOCALE_STATS_AVERAGE"){
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val_average]]></attribute>";
				} else {
					$out .= "<attribute name=\"".$this->get_constant($name)."\" show=\"YES\" link=\"NO\"><![CDATA[$val]]></attribute>";
				}
			}
			$out .= "</stat_total>";
		} else {
			$out="";
		}
		return $out;
	}
	

	function display_os($parameters){
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		$form = $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		
		$sql = "SELECT count(user_access_browser) as total, user_access_browser FROM user_access 
			inner join user_access_log on access_log_owner = user_access_identifier
			where user_access_client=$this->client_identifier $date_condition 
				and user_access_bot_name=''
				group by user_access_browser";
//		//print $sql;
		$result 	= $this->call_command("DB_QUERY", Array($sql));
		$Windows=0;
		$Windows95=0;
		$Windows98=0;
		$WindowsNT50=0;
		$WindowsNT51=0;
		$WindowsME=0;
		$WindowsNT40=0;
		$Linux=0;
		$Mac=0;
		$Unix=0;
		$SunOS=0;
		$FreeBSD=0;
		$IRIX=0;
		$HP=0;
		$OSF=0;
		$AIX=0;
		$spider=0;
		$bot=0;
		$overall=0;
		
		$testArray = array("Windows 95","Windows 98","Windows NT 5.0","Windows NT 5.1","Windows ME","Windows NT 4.0","Linux","Macintosh","Unix","SunOS","FreeBSD","IRIX","OSF","HP-UX","AIX","spider","bot","unknown");
		$displayArray = array("Windows 95","Windows 98","Windows 2000","Windows XP","Windows ME","Windows NT 4.0","Linux","Macintosh","Unix","SunOS","FreeBSD","IRIX","OSF","HP-UX","AIX","spider","bot","unknown");
		$varArray = array("Windows95","Windows98","WindowsNT50","WindowsNT51","WindowsME","WindowsNT40","Linux","Mac","Unix","SunOS","FreeBSD","IRIX","OSF","HP","AIX","spider","bot","unknown");
		$os_icon = array("win95","win98","win2000","winxp","winme","winnt","linux","macintosh","unix","sunos","freebsd","irix","osf","hpux","aix","unknown","unknown","unknown");
		$unknown =0;
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($row = $this->call_command("DB_FETCH_ARRAY",array($result))) {
				$found=0;
				for ($index=0;$index<count($testArray);$index++){
					if (strpos($row["user_access_browser"],$testArray[$index])){
						$evalcommand="\$".$varArray[$index]." += ".$row["total"].";";
						eval($evalcommand);
						$found=1;
					}
				}
				if($found==0){
					$unknown += $row["total"];
				}
				$overall += $row["total"];
			}
		}
		$total=0;
		for ($index=0;$index<count($testArray);$index++){
			$evalcommand="\$total += \$".$varArray[$index].";";
			eval($evalcommand);
		}
		$other = ($overall - $total);
		$out = "";
		for ($index=0;$index<count($testArray);$index++){
			eval("\$condition = \$".$varArray[$index].";");
			if ($condition>0){
				$evalcommand="\$out .= \"<stat_entry><attribute name=\\\"\\\" show=\\\"OS\\\" link=\\\"NO\\\"><![CDATA[".$os_icon[$index]."]]></attribute><attribute name=\\\"Operating System\\\" show=\\\"YES\\\" link=\\\"NO\\\"><![CDATA[".$displayArray[$index]."]]></attribute><attribute name=\\\"Total\\\" show=\\\"BAR\\\" link=\\\"NO\\\"><![CDATA[\$".$varArray[$index]."]]></attribute></stat_entry>\";";
				eval($evalcommand);
			}
		}
		$page_options ="".$this->generate_links();		
		return "<module name=\"user_access\" display=\"stats\">$page_options $form <stat_results label=\"Operating System users used when accessing your site\" total=\"$total\" link=\"".$this->module_command."TRACE_SESSION\">".$out."</stat_results></module>";
	}
 /*********Change by Ali Imran for Site access logs*********/
 function retrieve_summary_sitelogs($parameters){
		//echo $parameters;
		$limit=$this->check_parameters($parameters,"LIMIT",10);
		$sql="select user_access_ip_address, user_access_identifier, count(*) as page_total, min(access_log_date) as min_date , max(access_log_date) as max_date, 
			DAYOFMONTH(access_log_date) as day_data,
			HOUR(access_log_date) as hour_data								
				from user_access
				left outer join user_access_log on access_log_owner = user_access.user_access_identifier
				where user_access_client = $this->client_identifier and
					user_access_bot_name=''
				group by user_access_identifier,day_data,hour_data";
		//print "<p>line :: ".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out="";
		$total =0;
		$total_visitors=0;
		$average=0;
		$ip_address=array();
		if ($result){
			$total =0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$number_of_hits = $r["page_total"];
				$min = strtotime($this->check_parameters($r,"min_date",$this->libertasGetDate("Y/m/d H:i:s")));
				$max = strtotime($this->check_parameters($r,"max_date",$this->libertasGetDate("Y/m/d H:i:s")));
				if (empty($ip_address[$r["user_access_ip_address"]])){
					$ip_address[$r["user_access_ip_address"]]=1;
				}else{
					$ip_address[$r["user_access_ip_address"]]++;
				}
				$average += $max-$min;
				$total += $number_of_hits ;
				$total_visitors++;
			}
		}
	
		$total_unique=0;
		foreach ($ip_address as $key=>$value){
			$total_unique++;
		}
//		////print date("Y/m/d H:i:s",$average);
		if ($total_visitors>0){
			$average = $average / $total_visitors;
		} else {
			$average = 0;
		}
		if ($total==0 || $total_visitors==0){
			$avg=0;
		} else {
			$avg = round(($total / $total_visitors),2);
		}
			
		$out 		.= "
			<stat_entry>
				<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[".LOCALE_USER_ACCESS_REPORT_COMPLETE."]]></attribute>
				<attribute name=\"".LOCALE_STAT_NO_VISITORS."\" show=\"YES\" link=\"NO\"><![CDATA[$total_visitors]]></attribute>
				<attribute name=\"".LOCALE_STAT_TOTAL_PAGES."\" show=\"YES\" link=\"NO\"><![CDATA[$total]]></attribute>
				<attribute name=\"".LOCALE_STAT_UNIQUE_VISITS."\" show=\"YES\" link=\"NO\"><![CDATA[".$total_unique."]]></attribute>
				<attribute name=\"".LOCALE_STAT_AVERAGE."\" show=\"YES\" link=\"NO\"><![CDATA[".$avg."]]></attribute>
				<attribute name=\"".LOCALE_STAT_AVERAGE_SESSION_LENGTH."\" show=\"YES\" link=\"NO\"><![CDATA[".$this->timetodescription($average)."]]></attribute>
			</stat_entry>
		";
		$year = Date("Y");
		$month = Date("m");
		$day = Date("day");
		$ip_address=array();
		/*$sql="select user_access_ip_address, user_access_identifier, count(*) as page_total, min(access_log_date) as min_date , max(access_log_date) as max_date from user_access
				left outer join user_access_log on access_log_owner = user_access.user_access_identifier
				where user_access_client = $this->client_identifier and 
				(year(access_log_date) = $year or access_log_date is NULL)
				group by user_access_identifier, user_access.user_access_ip_address ";
*/		
		/* CHANGED BY SHAHZAD TO MATCH THE REPORT OUTPUT*/		
		/*
		$sql="select user_access_ip_address, user_access_identifier, count(*) as page_total, min(access_log_date) as min_date , max(access_log_date) as max_date from user_access_log 
		inner join user_access on user_access_identifier = access_log_owner 
				where user_access_client = $this->client_identifier and 
				(year(access_log_date) = $year or access_log_date is NULL) and user_access_bot_name='' 
				group by user_access_identifier, user_access.user_access_ip_address ";
		*/
		/* CHANGED BY ZIA TO MATCH THE REPORT OUTPUT*/		
		
		$sql="select user_access_ip_address, user_access_identifier, count(*) as page_total, min(access_log_date) as min_date , max(access_log_date) as max_date, 
		MONTH(user_access_log.access_log_date) as month_data, 
		DAYOFMONTH(user_access_log.access_log_date) as day_data,
		HOUR(access_log_date) as hour_data 		
		from user_access_log 
		inner join user_access on user_access_identifier = access_log_owner 
		where user_access_client = $this->client_identifier and 
		(year(access_log_date) = $year or access_log_date is NULL) and user_access_bot_name='' 
		group by user_access_identifier,month_data,day_data,hour_data ";
		
		//print "$sql</p>";
		
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$total =0;
		$average_time=0;
		$total_visitors=0;
		
		if ($result){
			$total =0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$number_of_hits = $r["page_total"];
				$total += $number_of_hits ;
				
				$min = strtotime($this->check_parameters($r,"min_date",$this->libertasGetDate("Y/m/d H:i:s")));
				$max = strtotime($this->check_parameters($r,"max_date",$this->libertasGetDate("Y/m/d H:i:s")));
				if (empty($ip_address[$r["user_access_ip_address"]])){
					$ip_address[$r["user_access_ip_address"]]=1;
				}else{
					$ip_address[$r["user_access_ip_address"]]++;
				}
				$average_time += $max-$min;
				$total_visitors++;
		
			}
		
		}
		$total_unique=0;
		foreach ($ip_address as $key=>$value){
			$total_unique++;
		}
		if ($total_visitors>0){
			$average_time = $average_time / $total_visitors;
		} else {
			$average_time = 0;
		}
		$average_time = $this->timetodescription($average_time);
		
		if ($total>0 && $total_visitors>0)
			$average = round(($total / $total_visitors),2);
		else
			$average = 0;
			
		
		$out 		.= "
			<stat_entry>
				<attribute name=\"\" show=\"YES\" link=\"jump_to_year\"><![CDATA[".LOCALE_USER_ACCESS_REPORT_YEAR."]]></attribute>
				<attribute name=\"".LOCALE_STAT_NO_VISITORS."\" show=\"YES\" link=\"NO\"><![CDATA[$total_visitors]]></attribute>
				<attribute name=\"".LOCALE_STAT_TOTAL_PAGES."\" show=\"YES\" link=\"NO\"><![CDATA[$total]]></attribute>
				<attribute name=\"".LOCALE_STAT_UNIQUE_VISITS."\" show=\"YES\" link=\"NO\"><![CDATA[".$total_unique."]]></attribute>
				<attribute name=\"".LOCALE_STAT_AVERAGE."\" show=\"YES\" link=\"NO\"><![CDATA[$average]]></attribute>
				<attribute name=\"".LOCALE_STAT_AVERAGE_SESSION_LENGTH."\" show=\"YES\" link=\"NO\"><![CDATA[$average_time]]></attribute>
				<attribute name=\"jump_to_year\" show=\"NO\" link=\"NO\"><![CDATA[command=USERACCESS_VIEW_YEAR_STATS]]></attribute>
			</stat_entry>";
		$ip_address=array();

		$sql="select user_access_ip_address, user_access_identifier, count(*) as page_total, min(access_log_date) as min_date , max(access_log_date) as max_date  from user_access
				inner join user_access_log on access_log_owner = user_access.user_access_identifier
				where user_access_client = $this->client_identifier and 
				year(access_log_date) = $year and
				month(access_log_date) = $month
				group by user_access_identifier, user_access.user_access_ip_address";
		//print "<p>line :: ".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$total =0;
		$total_visitors=0;
		$average_time=0;
		if ($result){
			$total =0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$number_of_hits = $r["page_total"];
				$total += $number_of_hits ;
				$min = strtotime($this->check_parameters($r,"min_date",$this->libertasGetDate("Y/m/d H:i:s")));
				$max = strtotime($this->check_parameters($r,"max_date",$this->libertasGetDate("Y/m/d H:i:s")));
				if (empty($ip_address[$r["user_access_ip_address"]])){
					$ip_address[$r["user_access_ip_address"]]=1;
				}else{
					$ip_address[$r["user_access_ip_address"]]++;
				}
				$average_time += $max-$min;
				$total_visitors++;
			}
		}
		$total_unique=0;
		foreach ($ip_address as $key=>$value){
			$total_unique++;
		}
		if ($total_visitors>0){
			$average_time = $average_time / $total_visitors;
		} else {
			$average_time = 0;
		}
		if ($total>0 && $total_visitors>0)
			$average = round(($total / $total_visitors),2);
		else
			$average = 0;
		$average_time = $this->timetodescription($average_time);
		$out 		.= "
			<stat_entry>
				<attribute name=\"\" show=\"YES\" link=\"jump_to_month\"><![CDATA[".LOCALE_USER_ACCESS_REPORT_MONTH."]]></attribute>
				<attribute name=\"".LOCALE_STAT_NO_VISITORS."\" show=\"YES\" link=\"NO\"><![CDATA[$total_visitors]]></attribute>
				<attribute name=\"".LOCALE_STAT_TOTAL_PAGES."\" show=\"YES\" link=\"NO\"><![CDATA[$total]]></attribute>
				<attribute name=\"".LOCALE_STAT_UNIQUE_VISITS."\" show=\"YES\" link=\"NO\"><![CDATA[".$total_unique."]]></attribute>
				<attribute name=\"".LOCALE_STAT_AVERAGE."\" show=\"YES\" link=\"NO\"><![CDATA[".$average."]]></attribute>
				<attribute name=\"".LOCALE_STAT_AVERAGE_SESSION_LENGTH."\" show=\"YES\" link=\"NO\"><![CDATA[$average_time]]></attribute>
				<attribute name=\"jump_to_month\" show=\"NO\" link=\"NO\"><![CDATA[command=USERACCESS_VIEW_MONTH]]></attribute>
			</stat_entry>
		";
		$start_date = $this->get_week();
		$ip_address=array();
		$sql="select user_access_ip_address, user_access_identifier, count(*) as page_total, min(access_log_date) as min_date , max(access_log_date) as max_date  from user_access
				inner join user_access_log on access_log_owner = user_access.user_access_identifier
				where user_access_client = $this->client_identifier and 
				year(access_log_date) = $year and
				access_log_date > '$start_date'
				group by user_access_identifier, user_access.user_access_ip_address";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$total =0;
		$total_visitors=0;
		$average_time=0;
		if ($result){
			$total =0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$number_of_hits = $r["page_total"];
				$total += $number_of_hits ;
				$min = strtotime($r["min_date"]);
				if (empty($ip_address[$r["user_access_ip_address"]])){
					$ip_address[$r["user_access_ip_address"]]=1;
				}else{
					$ip_address[$r["user_access_ip_address"]]++;
				}
				$max = strtotime($r["max_date"]);
				$average_time += $max-$min;
				$total_visitors++;
			}
		}
		if (($average_time!=0) && ($total_visitors!=0)){
			$average_time = $this->timetodescription($average_time / $total_visitors);
		} else {
			$average_time = "1s";
		}
		if (($total!=0) && ($total_visitors!=0)){
			$average_page = round(($total / $total_visitors),2);
		} else {
			$average_page = 0;
		}
		$total_unique=0;
		foreach ($ip_address as $key=>$value){
			$total_unique++;
		}
		$out 		.= "
			<stat_entry>
				<attribute name=\"\" show=\"YES\" link=\"jump_to_week\"><![CDATA[".LOCALE_USER_ACCESS_REPORT_WEEK."]]></attribute>
				<attribute name=\"LOCALE_STAT_NO_VISITORS\" show=\"YES\" link=\"NO\"><![CDATA[$total_visitors]]></attribute>
				<attribute name=\"LOCALE_STAT_TOTAL_PAGES\" show=\"YES\" link=\"NO\"><![CDATA[$total]]></attribute>
				<attribute name=\"LOCALE_STAT_UNIQUE_VISITS\" show=\"YES\" link=\"NO\"><![CDATA[".$total_unique."]]></attribute>
				<attribute name=\"LOCALE_STAT_AVERAGE\" show=\"YES\" link=\"NO\"><![CDATA[$average_page]]></attribute>
				<attribute name=\"LOCALE_STAT_AVERAGE_SESSION_LENGTH\" show=\"YES\" link=\"NO\"><![CDATA[$average_time]]></attribute>
				<attribute name=\"jump_to_week\" show=\"NO\" link=\"NO\"><![CDATA[command=USERACCESS_VIEW_WEEK]]></attribute>
			</stat_entry>
		";
		

$page_options ="".$this->generate_links();		
return "<module name=\"user_access\" display=\"stats\">$page_options
		<stat_results label=\"".LOCALE_USER_ACCESS_REPORT_SUMMARY."\" total=\"0\" >".$out."</stat_results>
		</module>";
	}
	
	function retrieve_summary_serverlogs($parameters){
		//echo $parameters;
		$limit=$this->check_parameters($parameters,"LIMIT",10);
		$sql="select domain_name, domain_identifier				
				from domain
				where domain_client = $this->client_identifier";
		//print "<p>line :: ".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		if ($result){
			$total =0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$domain_name = $r["domain_name"];
			}
		}
		$var_domain = 'Domain';
		$out 		.= "
			<stat_entry>
				<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[".$var_domain."]]></attribute>
			</stat_entry>
		";
		
			/*$sql="select user_access_ip_address, user_access_identifier, count(*) as page_total, min(access_log_date) as min_date , max(access_log_date) as max_date from user_access
					left outer join user_access_log on access_log_owner = user_access.user_access_identifier
					where user_access_client = $this->client_identifier and 
					(year(access_log_date) = $year or access_log_date is NULL)
					group by user_access_identifier, user_access.user_access_ip_address ";
	*/		
			/* CHANGED BY SHAHZAD TO MATCH THE REPORT OUTPUT*/		
			
			
			/* CHANGED BY ZIA TO MATCH THE REPORT OUTPUT*/	
			//$domain_name = "http://".$domain_name.":2222/CMD_WEBALIZER/".$domain_name."/index.html";
			
			$ip_addr = trim(gethostbyname($domain_name)," /");
			$domain_url = ':2222/CMD_WEBALIZER/'.$domain_name.'/index.html';
			//$out .= "<a href='http://$domain_url'>$domain_name</a>";
			 $out 		.= "
				<stat_entry><attribute name=\"jump_to_domain\" show=\"YES\" link=\"jump_to_domain\"><![CDATA[http://".$ip_addr."$domain_url]]></attribute>
					
				</stat_entry>";
					
			$show_text = 'View Your Site Logs By Clicking on your Domain.';
		
	$page_options ="".$this->generate_links();		
	return "<module name=\"user_access\" display=\"stats\">$page_options
		<stat_results label=\"".$show_text."\" total=\"0\" >".$out."</stat_results>
		</module>";
	}
	
	function display_form($parameters){
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$_filter_all_months	=$this->check_parameters($parameters, "_filter_all_months","YES");
		$_filter_all_days	=$this->check_parameters($parameters, "_filter_all_days","YES");
		$_filter_no_days	=$this->check_parameters($parameters, "_filter_no_days","1");
		$identifier			=$this->check_parameters($parameters, "identifier","");
		$cmd 				=$this->check_parameters($parameters,"command");
		$list_results		=Array();
		$months 			=Array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);
		$max_year = date("Y");
	//	////print $max_year;
		$form = "
		<filter>
		<form method='get' name='datefilter' label='".LOCALE_STATS_SELECT_TIME_FRAME."'>
			<input type='hidden' name='command' value='$cmd'/>
			<input type='hidden' name='identifier' value='$identifier'/>";
			
			if ($max_year>2003){
				$form .="<select label='".LOCALE_STATS_SELECT_YEAR."' name='_filter_year'>";
				for ($y=2003;$y<=$max_year;$y++){
					$form .="<option value='$y'";
					if ($_filter_year==$y){
						$form .=" selected='true'";
					}
					$form .=">$y</option>";
				}
				$form .= "</select>";
			} else {
				$form .= "<input type='hidden' name='_filter_year' value='2003'/>";
				
			}
			$form .= "<select label='".LOCALE_STATS_SELECT_MONTH."' name='_filter_month'>";
			if($_filter_all_months=="YES"){
				$form .= "<option value='-1'>All Months</option>";
			}
			for ($m=1;$m<=12;$m++){
				$form .="<option value='$m'";
				if ($_filter_month==$m){
					$form .=" selected='true'";
				}
				$form .=">".$months[$m-1]."</option>";
			}
			$form .= "</select>";
			if($_filter_no_days=="1"){
				$form .= "<select label='".LOCALE_STATS_SELECT_DAY."' name='_filter_day'>";
				if($_filter_all_days=="YES"){
					$form .= "<option value='-1'>All days</option>";
				}
				for ($d=1;$d<=31;$d++){
					$form .="<option value='$d'";
					if ($_filter_day==$d){
						$form .=" selected='true'";
					}
					$form .=">$d</option>";
				}
				$form .= "</select>";
			}
			$form .= "<input type='submit' iconify='SEARCH' value='".SEARCH_NOW."'/>
		</form></filter>";
//		////print $form;
		return $form;
	}
	
	function display_date_condition($parameters){
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$sql="";
//		////print "[$_filter_year,$_filter_month,$_filter_day]";
		if (($_filter_year!='') && ($_filter_year!='-1')){
			$sql .=" and year(access_log_date) = $_filter_year ";
		}
		if (($_filter_month!='') && ($_filter_month!='-1')){
			$sql .=" and month(access_log_date) = $_filter_month ";
		}
		if (($_filter_day!='') && ($_filter_day!='-1')){
			$sql .=" and dayofmonth(access_log_date) = $_filter_day ";
		}
		return $sql;
	}
	
	
	function get_week($week = "__GET_CURRENT__"){
		if($week == "__GET_CURRENT__"){
			$week = (date("W")-1);
		}
		$y = date("Y");
		$year_start = strtotime("$y/01/01 00:00:00");
		$first_day_of_year = (Date("w",$year_start));
		$start_of_week1 = Date("Y/m/d H:i:s",strtotime("-$first_day_of_year days",$year_start));
		$start_date = Date("Y/m/d H:i:s",strtotime("-$first_day_of_year day",strtotime("+$week weeks",$year_start)));
		return $start_date;
	}
	
	function display_view_filtered_on_country($parameters){
		$flag				=$this->check_parameters($parameters, "identifier", "");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$page				=$this->check_parameters($parameters, "page",1);
		$cmd				=$this->check_parameters($parameters, "command");
		$parameters["_filter_all_months"]="NO";
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == "" || $_filter_month==-1){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		$cmd.="&amp;_filter_year=$_filter_year&amp;_filter_month=$_filter_month&amp;_filter_day=$_filter_day&amp;identifier=$flag";
		$form 			= $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$list_results=Array();

		if ($flag=="n.a."){
			$tld = "tld is NULL and ";
		} else {
			$tld = "tld ='$flag' and ";
		}
		$months = array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);

		$sql = "
			SELECT 
	user_access_referer,
	user_access_ip_address, 
	user_access_user_identifier, 
	user_access_identifier, 
	count(access_log_owner) as total_pages,
	access_country,country,
	language_label,
	language_code	
			FROM user_access_log 
			inner join user_access on user_access_identifier = access_log_owner 
			left outer join user_access_ip_lookup on user_access_ip_address = access_ip
			left outer join user_access_countries on access_country = tld
			left outer join available_languages on user_access_accept_language = available_languages.language_code 
WHERE 
	$tld
	user_access_client=$this->client_identifier 
	$date_condition
group by 
	access_log_owner, 
	user_access_user_identifier,
	user_access_referer";
	////print "<p>".__LINE__." $sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
			$num_rows = $this->call_command("DB_NUM_ROWS",Array($result));
			$start=(($page-1)*25);
			if ($start<$num_rows){
				$this->call_command("DB_SEEK",Array($result,$start));
			}
			$num_of_pages = ceil($num_rows / 25);
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($c<25)){
				$c++;
				if ((strpos($this->check_parameters($r,"language_code","N.A."),"-")-1)>=0){
					$list = split('-',strtolower($this->check_parameters($r,"language_code","N.A.")));
					$flag= $list[1];
				}else{
					$flag=strtolower($this->check_parameters($r,"language_code","N.A."));
				}
				$out.= "<stat_entry>";
				$out.= "<attribute name=\"C\" show=\"FLAG\" alt='".LOCALE_ADDRESS_COUNTRY." ".$this->check_parameters($r,"country","N.A.")."' link=\"NO\"><![CDATA[".strtolower($r["access_country"])."]]></attribute>";
				$out.= "<attribute name=\"L\" show=\"FLAG\" alt='".LOCALE_LANGUAGE." ".$this->check_parameters($r,"language_label","N.A.")."' link=\"NO\"><![CDATA[".$flag."]]></attribute>";
				$out.= "<attribute name=\"IP Address\" show=\"YES\" link=\"NO\"><![CDATA[".$r["user_access_ip_address"]."]]></attribute>";
				if ($r["user_access_referer"]=="No Referer"){
					$out.= "<attribute name=\"Referer\" show=\"YES\" link=\"NO\"><![CDATA[No Referer]]></attribute>";
				}else {
					$out.= "<attribute name=\"Referer\" show=\"YES\" link=\"LINK\"><![CDATA[".substr($r["user_access_referer"],0,55)."]]></attribute>";
				}
				$out.= "<attribute name=\"Total\" show=\"BAR\" link=\"session\"><![CDATA[".$r["total_pages"]."]]></attribute>";
				
				$out.= "<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[".$r["user_access_referer"]."]]></attribute>";
				$out.= "<attribute name=\"session\" show=\"NO\" link=\"NO\"><![CDATA[identifier=".$r["user_access_identifier"]."]]></attribute>";
				$out.= "</stat_entry>";
				$total +=$r["total_pages"];
			}
	//		////print $result;
		}
		$page_options ="".$this->generate_links();		
		$pages = "<pages>";
		for ($i=1;$i<=$num_of_pages;$i++){
			$pages .= "<page>$i</page>";
		}
		$pages .= "</pages>";
		return "<module name=\"user_access\" display=\"stats\">
					$form
					$page_options 
					<stat_results 
						page='$page' total_pages='$num_of_pages'
						label='Access from Filtered Country' total='$total' link='user_access_TRACE_SESSION' show_counter='0'
						report='$cmd'
						>$pages".$out."</stat_results>
				</module>";
	}
	function display_view_filtered_on_language($parameters){
		$flag				=$this->check_parameters($parameters, "identifier", "");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$parameters["_filter_all_months"]="NO";
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == "" || $_filter_month==-1){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		$form 			= $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$list_results=Array();

		$months = array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);

		$sql = "SELECT 
					user_access_referer,
					user_access_ip_address, 
					user_access_user_identifier, 
					user_access_identifier, 
					language_label,
					language_code,
					access_country,country,
					count(access_log_owner) as total_pages
				FROM user_access_log 
					inner join user_access on user_access_identifier = access_log_owner 
					inner join user_access_ip_lookup on user_access_ip_address = access_ip
					inner join user_access_countries on access_country = tld
					left outer join available_languages on user_access_accept_language = available_languages.language_code 
				WHERE 
					language_code like '%$flag' and 
					user_access_client=$this->client_identifier 
					$date_condition
				group by 
					access_log_owner, 
					user_access_user_identifier,
					user_access_referer";
////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
			//////print $this->call_command("DB_NUM_ROWS",Array($result));
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
			$c++;
					if ((strpos($this->check_parameters($r,"language_code","N.A."),"-")-1)>=0){
						$list = split('-',strtolower($this->check_parameters($r,"language_code","N.A.")));
						$flag= $list[1];
					}else{
						$flag=strtolower($this->check_parameters($r,"language_code","N.A."));
					}
				
				$out.= "<stat_entry>";
				$out.= "<attribute name=\"C\" show=\"FLAG\" alt='".LOCALE_ADDRESS_COUNTRY." ".$r["country"]."' link=\"NO\"><![CDATA[".strtolower($r["access_country"])."]]></attribute>";
				$out.= "<attribute name=\"L\" show=\"FLAG\" alt='".LOCALE_LANGUAGE." ".$this->check_parameters($r,"language_label","N.A.")."' link=\"NO\"><![CDATA[".$flag."]]></attribute>";
				$out.= "<attribute name=\"IP Address\" show=\"YES\" link=\"NO\"><![CDATA[".$r["user_access_ip_address"]."]]></attribute>";
				if ($r["user_access_referer"]=="No Referer"){
					$out.= "<attribute name=\"Referer\" show=\"YES\" link=\"NO\"><![CDATA[No Referer]]></attribute>";
				}else {
					$out.= "<attribute name=\"Referer\" show=\"YES\" link=\"LINK\"><![CDATA[".substr($r["user_access_referer"],0,55)."]]></attribute>";
				}
				$out.= "<attribute name=\"Total\" show=\"BAR\" link=\"session\"><![CDATA[".$r["total_pages"]."]]></attribute>";
				
				$out.= "<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[".$r["user_access_referer"]."]]></attribute>";
				$out.= "<attribute name=\"session\" show=\"NO\" link=\"NO\"><![CDATA[identifier=".$r["user_access_identifier"]."]]></attribute>";
				$out.= "</stat_entry>";
				$total +=$r["total_pages"];
			}
	//		////print $result;
		}
		$page_options ="".$this->generate_links();		
		return "<module name=\"user_access\" display=\"stats\">
					$form
					$page_options 
					<stat_results label='Access from Filtered Language' total='$total' link='user_access_TRACE_SESSION' show_counter='0'>".$out."</stat_results>
				</module>";
	}
	
	function display_view_filtered_on_referer($parameters){
		$domain				=$this->check_parameters($parameters, "domain", "");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$form 				= $this->display_form($parameters);
		$date_condition 	= $this->display_date_condition($parameters);
		$list_results=Array();

		$months = array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);
		$domain_list = $this->call_command("CLIENT_GET_DOMAINS");
		$max = count($domain_list);
		$cond = $date_condition;
		for($index=0;$index<$max;$index++){
			$cond .=" and user_access_referer not like '%".$domain_list[$index]."%'";
		}

		$sql = "SELECT 
	user_access_referer,
	user_access_ip_address, 
	user_access_user_identifier, 
	user_access_identifier, 
	count(access_log_owner) as total_pages,
	min(access_log_date) as access_date,
	access_country
	
FROM user_access_log 
	inner join user_access on user_access_identifier = access_log_owner 
	inner join user_access_ip_lookup on access_ip = user_access_ip_address 
WHERE 
	user_access_referer like 'http://$domain%' and 
	user_access_client=$this->client_identifier 
	$cond
group by 
	access_log_owner, 
	user_access_user_identifier,
	user_access_referer";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
//			////print $this->call_command("DB_NUM_ROWS",Array($result));
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
			$c++;
				$out.= "<stat_entry>
 						<attribute name=\"LOCALE_STATS_FLAG\" show=\"NO\" link=\"NO\"><![CDATA[".strtolower($r["access_country"])."]]></attribute>";
				$out.= "
							<attribute name=\"".LOCALE_STAT_IP_ADDRESS."\" show=\"YES\" link=\"NO\"><![CDATA[".$r["user_access_ip_address"]."]]></attribute>
							<attribute name=\"".LOCALE_STAT_TIME."\" show=\"YES\" link=\"NO\"><![CDATA[".$r["access_date"]."]]></attribute>
							<attribute name=\"".LOCALE_STAT_REFERER."\" show=\"YES\" link=\"LINK\"><![CDATA[".substr($r["user_access_referer"],0,55)."]]></attribute>
							<attribute name=\"".LOCALE_STAT_TOTAL_PAGES."\" show=\"BAR\" link=\"session\"><![CDATA[".$r["total_pages"]."]]></attribute>
				";
				$out.= "<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[".$r["user_access_referer"]."]]></attribute>";
				$out.= "<attribute name=\"session\" show=\"NO\" link=\"NO\"><![CDATA[identifier=".$r["user_access_identifier"]."]]></attribute>";
				$out.= "</stat_entry>";
				$total +=$r["total_pages"];
			}
	//		////print $result;
		}
$page_options ="".$this->generate_links();		
		return "<module name=\"user_access\" display=\"stats\">
					$form
					$page_options 
					<stat_results label='".LOCALE_STAT_FILTER_DOMAIN_REFERER."' total='$total' link='user_access_TRACE_SESSION' show_counter='0'>".$out."</stat_results>
				</module>";
	}
	function display_browsers($parameters){
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		
		$form = $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$list_results=Array();
		

		$sql = "SELECT 
					count(user_access_browser) as total,
					user_access_browser
				FROM user_access
				inner join user_access_log on user_access_log.access_log_owner = user_access.user_access_identifier
				where 
					user_access_client=$this->client_identifier
					$date_condition
				group by
					user_access_browser
				order by	
				user_access_browser
					";
//		////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		$result 	= $this->call_command("DB_QUERY", Array($sql));
		while ($row = $this->call_command("DB_FETCH_ARRAY",array($result))) {
			$uab 	= " ".$row["user_access_browser"];
			if (
					(!strpos(strtoupper($uab), "MSIE")) &&(
					(($pos = strpos(strtoupper($uab), "MOZILLA"))==1) ||
					(($pos = strpos(strtoupper($uab), "NETSCAPE"))>0) ||
					(($pos = strpos(strtoupper($uab), "GECKO"))>0) ||
					(($pos = strpos(strtoupper($uab), "GALEON"))>0))
				){
				$version = substr($uab,strlen('Mozilla/ '),strpos($uab," ",strlen('Mozilla/'))-strlen('Mozilla/ '));
				if (empty($list_results["Netscape $version"])){
					$list_results["Netscape $version"]  = $row["total"];
				}else{
					$list_results["Netscape $version"] += $row["total"];
				}
				$icon_results["Netscape $version"] ="netscape";
			} else if (($pos = strpos(strtoupper($uab), "OPERA"))>0){
				if (empty($list_results["Opera"])){
					$list_results["Opera"]  = $row["total"];
				}else{
					$list_results["Opera"] += $row["total"];
				}
				$icon_results["Opera"] ="opera";
			} else if (($pos = strpos(strtoupper($uab), "KONQUEROR"))>0){
				if (empty($list_results["Konqueror"])){
					$list_results["Konqueror"]  = $row["total"];
				}else{
					$list_results["Konqueror"] += $row["total"];
				}
				$icon_results["Konqueror"] ="konqueror";
			} else if (($pos = strpos(strtoupper($uab), "ICAB"))>0){
				if (empty($list_results["iCab"])){
					$list_results["iCab"]  = $row["total"];
				}else{
					$list_results["iCab"] += $row["total"];
				}
				$icon_results["iCab"] ="icab";
			} else if (
					(($pos = strpos(strtoupper($uab), "BOT"))>0) ||
					(($pos = strpos(strtoupper($uab), "CRAWLER"))>0) ||
					(($pos = strpos(strtoupper($uab), "WALKER"))>0) ||
					(($pos = strpos(strtoupper($uab), "SCRUB"))>0) ||
					(($pos = strpos(strtoupper($uab), "INTERNETSEER.COM"))>0) ||
					(($pos = strpos(strtoupper($uab), "SCOOTER"))>0) ||
					(($pos = strpos(strtoupper($uab), "SPIDER"))>0) ||
					(($pos = strpos(strtoupper($uab), "SLURP@INKTOMI.COM"))>0)
					)
				{
				if (empty($list_results["Bots / Spider"])){
					$list_results["Bots / Spider"]  = $row["total"];
				}else{
					$list_results["Bots / Spider"] += $row["total"];
				}
				$icon_results["Bots / Spider"] ="unknown";
			} else if (($pos = strpos(strtoupper($uab), "LYNX"))>0){
				if (empty($list_results["Lynx"])){
					$list_results["Lynx"]  = $row["total"];
				}else{
					$list_results["Lynx"] += $row["total"];
				}
				$icon_results["Lynx"] ="lynx";
			} else if (($pos = strpos(strtoupper($uab), "MSIE"))>0){
				$version = substr($uab,
								strpos(strtoupper($uab),'MSIE ')+5,
								strpos($uab,";",strpos(strtoupper($uab),'MSIE ')+5) - (strpos(strtoupper($uab),'MSIE ')+5)
							);
				if (empty($list_results["Microsoft Internet Explorer $version"])){
					$list_results["Microsoft Internet Explorer $version"]  = $row["total"];
				}else{
					$list_results["Microsoft Internet Explorer $version"] += $row["total"];
				}
				$icon_results["Microsoft Internet Explorer $version"] ="msie";
				$search_results["Microsoft Internet Explorer $version"] ="msie $version";
			} else {
				if (empty($list_results["Other / Unknown"])){
					$list_results["Other / Unknown"]  = $row["total"];
				}else{
					$list_results["Other / Unknown"] += $row["total"];
				}
				$icon_results["Other / Unknown"] ="unknown";
				$search_results["Other / Unknown"] ="unknown";
			}
		}
		arsort($list_results);
	
		$out		= "";
//		$out.="<text><![CDATA[Visitors by browser on your site.]]></text>";
		$total=0;
		$filter="&_filter_year=$_filter_year&_filter_month=$_filter_month&_filter_day=$_filter_day";
		foreach($list_results as $key => $value){
			$out .= "
				<stat_entry>";
			$out .= "<attribute name=\"\" show=\"BROWSER\" link=\"NO\"><![CDATA[".$icon_results[$key]."]]></attribute>";
			if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
				$out .= "<attribute name=\"Browser\" show=\"YES\" link=\"NO\"><![CDATA[".$key."]]></attribute>";
			}else {
				$out .= "<attribute name=\"Browser\" show=\"YES\" link=\"NO\"><![CDATA[".$key."]]></attribute>";
			}
				$out .= "<attribute name=\"Total\" show=\"BAR\" link=\"NO\"><![CDATA[".$value."]]></attribute>
					<attribute name=\"_filter_browser\" show=\"NO\" link=\"NO\"><![CDATA[_filter_browser=$key$filter]]></attribute>
				</stat_entry>
			";
			$total+=$value;
		}
		$page_options ="".$this->generate_links();		
		return "<module name=\"user_access\" display=\"stats\">$page_options $form <stat_results label=\"Page Impressions by browser\" total=\"$total\" link=\"".$this->module_command."VIEW_FILTERED_BROWSER\">".$out."</stat_results></module>";
	}

	function display_filtered_browsers($parameters){
		$_filter_browser	= $this->check_parameters($parameters, "_filter_browser");
		$_filter_year		= $this->check_parameters($parameters, "_filter_year");
		$_filter_month		= $this->check_parameters($parameters, "_filter_month");
		$_filter_day		= $this->check_parameters($parameters, "_filter_day");
		$form 				= $this->display_form($parameters);
		$date_condition 	= $this->display_date_condition($parameters);
		$list_results		= Array();
		$L = Array("5.0","4.79","4.75","4.51","4.5","4.8","4.05","4.0","3.02","3.0","2.0");
		for ($i=0 ; $i<count($L);$i++){
			$must_results["Netscape ".$L[$i]]							= Array("MOZILLA/".$L[$i]."%");
			$list_results["Netscape ".$L[$i]]							= Array("MOZILLA%","%NETSCAPE%", "%GECKO%", "%GALEON%");
			$ignore_results["Netscape ".$L[$i]]							= Array("%MSIE%");
		}
		$L = Array("3.02","4.01","5.01","5.05","5.0","5.15","5.16","5.22","5.5","5.55","6.0");
		for ($i=0 ; $i<count($L);$i++){
//			$must_results["Netscape ".$L[$i]]							= Array("MOZILLA/".$L[$i]."%");
//			$list_results["Netscape ".$L[$i]]							= Array("MOZILLA%","%NETSCAPE%", "%GECKO%", "%GALEON%");
			$list_results["Microsoft Internet Explorer ".$L[$i]]			= Array("MOZILLA%MSIE ".$L[$i].";%");
//			$ignore_results["Netscape ".$L[$i]]							= Array("%MSIE%");
		}
//		////print "[".empty($list_results[$_filter_browser])."]";

		$list_results["Opera"]								= Array("%OPERA%");
		$list_results["Konqueror"]							= Array("%KONQUEROR%");
		$list_results["iCab"]								= Array("%ICAB%");
		$list_results["Bots / Spider"]						= Array("%BOT%","%CRAWLER%","%WALKER%","%SCRUB%","%INTERNETSEER.COM%","%SCOOTER%","%SPIDER%");
		$list_results["Lynx"]								= Array("%LYNX%");
		$list_results["Lynx"]								= Array("%LYNX%");

		$sql = "SELECT 
					count(user_access_browser) as total,
					user_access_browser
				FROM user_access
				inner join user_access_log on user_access_log.access_log_owner = user_access.user_access_identifier
				where 
					user_access_client=$this->client_identifier
					$date_condition ";
		if (empty($list_results[$_filter_browser])){
		}else{
			$sql .= " and (";
			for ($index=0,$m=count($list_results[$_filter_browser]);$index<$m;$index++){
				if ($index>0){
				$sql .= " or ";
				}
				$sql .= "user_access_browser like '".$list_results[$_filter_browser][$index]."'";
			}
			$sql .= ")";
			if (!empty($ignore_results[$_filter_browser])){
				$sql .= " and ( ";
				for ($index=0,$m=count($ignore_results[$_filter_browser]);$index<$m;$index++){
					if ($index>0){
						$sql .= " or ";
					}
					$sql .= "user_access_browser not like '".$ignore_results[$_filter_browser][$index]."'";
				}
				$sql .= ")";
			}
			if (!empty($must_results[$_filter_browser])){
				$sql .= " and ( ";
				for ($index=0,$m=count($must_results[$_filter_browser]);$index<$m;$index++){
					if ($index>0){
						$sql .= " and ";
					}
					$sql .= "user_access_browser like '".$must_results[$_filter_browser][$index]."'";
				}
				$sql .= ")";
			}
		}
		$sql .= "
				group by
					user_access_browser
				order by	
				user_access_browser";
//		////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		$result 	= $this->call_command("DB_QUERY", Array($sql));
		$out		= "";
		$out.="<text><![CDATA[Visitors by browser on your site.]]></text>";
		$total=0;
		$filter="&_filter_year=$_filter_year&_filter_month=$_filter_month&_filter_day=$_filter_day";
		while ($row = $this->call_command("DB_FETCH_ARRAY",array($result))) {
			$key 	= $row["user_access_browser"];
			$value	= $row["total"];
			$out   .= "
				<stat_entry>
					<attribute name=\"Browser\" show=\"YES\" link=\"browser_string\"><![CDATA[".$key."]]></attribute>
					<attribute name=\"Total\" show=\"BAR\" link=\"NO\"><![CDATA[".$value."]]></attribute>
					<attribute name=\"browser_string\" show=\"NO\" link=\"NO\"><![CDATA[$filter&browser=".$key."]]></attribute>
				</stat_entry>
			";
			$total+=$value;
		}
	$page_options ="".$this->generate_links();		
	return "<module name=\"user_access\" display=\"stats\">$page_options $form <stat_results label=\"User Sessions\" total=\"$total\" link=\"".$this->module_command."VIEW_FILTERED_BROWSER_STRING\">".$out."</stat_results></module>";
	}

	function display_filtered_browser_string($parameters){
		$_filter_browser	= $this->check_parameters($parameters, "browser");
		$_filter_year		= $this->check_parameters($parameters, "_filter_year");
		$_filter_month		= $this->check_parameters($parameters, "_filter_month");
		$_filter_day		= $this->check_parameters($parameters, "_filter_day");
		$form = $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$list_results=Array();
		
		
		$list_results["Netscape"]							= Array("%NETSCAPE%", "%GECKO%", "%GALEON%");
		$list_results["Opera"]								= Array("%OPERA%");
		$list_results["Konqueror"]							= Array("%KONQUEROR%");
		$list_results["iCab"]								= Array("%ICAB%");
		$list_results["Bots / Spider"]						= Array("%BOT%","%CRAWLER%","%WALKER%","%SCRUB%","%INTERNETSEER.COM%","%SCOOTER%","%SPIDER%");
		$list_results["Lynx"]								= Array("%LYNX%");
		$list_results["Microsoft Internet Explorer"]		= Array("MOZILLA%MSIE%");

		$sql = "SELECT 
					count(user_access_browser) as total,
					user_access_ip_address,
					user_access_identifier
				FROM user_access
				inner join user_access_log on user_access_log.access_log_owner = user_access.user_access_identifier
				where 
					user_access_client=$this->client_identifier
					$date_condition and 
					user_access_browser ='$_filter_browser'
				group by
					user_access_ip_address
				order by	
				total desc";
//////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		$result 	= $this->call_command("DB_QUERY", Array($sql));
		$out		= "";
		$out.="<text><![CDATA[Visitors by browser on your site.]]></text>";
		$total=0;
		$filter="&_filter_year=$_filter_year&_filter_month=$_filter_month&_filter_day=$_filter_day";
		while ($row = $this->call_command("DB_FETCH_ARRAY",array($result))) {
			$key 	= $row["user_access_ip_address"];
			$value	= $row["total"];
			$id		= $row["user_access_identifier"];
			$out   .= "
				<stat_entry>
					<attribute name=\"Accessing Ip Address\" show=\"YES\" link=\"owner\"><![CDATA[".$key."]]></attribute>
					<attribute name=\"Total\" show=\"BAR\" link=\"NO\"><![CDATA[".$value."]]></attribute>
					<attribute name=\"owner\" show=\"NO\" link=\"NO\"><![CDATA[$filter&identifier=".$id."]]></attribute>
				</stat_entry>
			";
			$total+=$value;
		}
	
		$page_options ="".$this->generate_links();
		return "<module name=\"user_access\" display=\"stats\">$page_options $form <stat_results label=\"User Sessions\" total=\"$total\" link=\"".$this->module_command."TRACE_SESSION\">".$out."</stat_results></module>";
	}

	function generate_links(){
		$sql = "SELECT system_preference_value FROM system_preferences WHERE system_preference_client='".$this->parent->client_identifier."' AND system_preference_name='sp_generate_site_logs'";
					$result = $this->call_command("DB_QUERY",array($sql));
					if ($result){
						$r = $this->call_command("DB_FETCH_ARRAY",array($result));
						$site_access_logs_generate = $r['system_preference_value'];	
					}
		if($site_access_logs_generate != 'No')
			$this->load_reports();
					
		$groups = array();
		for ($i = 0, $max =count($this->module_reports) ;$i < $max; $i++){
			if (empty($groups[$this->module_reports[$i][2]])){
				$groups[$this->module_reports[$i][2]] = Array();
				$groups[$this->module_reports[$i][2]][$this->module_reports[$i][0]] = $this->module_reports[$i][1];
			} else {
				$groups[$this->module_reports[$i][2]][$this->module_reports[$i][0]] = $this->module_reports[$i][1];
			}
		}
		$page_options="";
		foreach($groups as $key=>$list){
			$page_options .= "<links label=\"".$key."\">";
			foreach($list as $list_key=>$list_value){
				$page_options .= "<link command=\"".$list_key."\"><![CDATA[".$list_value."]]></link>";
			}
			$page_options .= "</links>";
		}
		return $page_options;
		
	}
	
	function retrieve_today_timed($parameters){
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		$date_of_report = $_filter_day ."/".$_filter_month ."/".$_filter_year;
		$form = $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);

		$sql = "SELECT count(access_log_owner) as total_hits,access_log_owner,access_log_url, access_log_date,
			DAYOFMONTH(access_log_date) as day_data,
			HOUR(access_log_date) as hour_data			
			FROM user_access_log  inner join user_access on user_access_identifier = access_log_owner 
			WHERE user_access_client=$this->client_identifier and user_access_bot_name='' 
			$date_condition
			group by 
				access_log_owner,day_data,hour_data 
			ORDER BY 
				access_log_date, 
				access_log_url";
		//print "<li>".$sql."</li>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_a_specific_day",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));

		$total2 	= 0;
		$list_results = Array();
		if ($result){
			$total =0;
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$key = (date("G",strtotime($r["access_log_date"]))*1);
				if (empty($list_results[$key])){
					$list_results[$key] = Array("Pages Viewed" => 1, "Visitors"=>Array(), "Average" => 1);
					$list_results[$key]["Visitors"][$r["access_log_owner"]]=1;
					$list_results[$key]["Pages Viewed"] = $r["total_hits"]; 					
				}else{
					$list_results[$key]["Pages Viewed"] += $r["total_hits"]; 
					if (empty($list_results[$key]["Visitors"][$r["access_log_owner"]])){
						$list_results[$key]["Visitors"][$r["access_log_owner"]]=1;
					}else{
						$list_results[$key]["Visitors"][$r["access_log_owner"]]++;
					}
				}
			}
		}
		$out_2		= "";
		$start		= 0;
		$biggest	= 0;
		$biggest2	= 0;
		$biggest3	= 0;
		$total1 	= 0;
		$total2 	= 0;
		$total3 	= 0;
		asort($list_results);
		foreach($list_results as $key=>$list){
			$list_results[$key]["Visitor Count"]=1;
			$x=0;
			foreach($list["Visitors"] as $entry){
				$x++;
			}
			$list_results[$key]["Visitor Count"]=$x;
		}
		for($key=0,$m = 24;$key<$m;$key++){
			if (!empty($list_results["".$key]) && !empty($list_results["".$key])){
			$list_results["".$key]["Average"] = round($list_results["".$key]["Pages Viewed"] / $list_results["".$key]["Visitor Count"],2);
			$start = ($key*1);
			if ($list_results["".$key]["Pages Viewed"]>$biggest){
				$biggest=$list_results["".$key]["Pages Viewed"];
			}
			if ($list_results["".$key]["Visitor Count"]>$biggest2){
				$biggest2=$list_results["".$key]["Visitor Count"];
			}
			if ($list_results["".$key]["Average"]>$biggest3){
				$biggest3=ceil($list_results["".$key]["Average"]);
			}
			$total1 += $list_results["".$key]["Pages Viewed"];
			$total2 += $list_results["".$key]["Visitor Count"];
			$total3 += $list_results["".$key]["Average"];
			
			$out_2.= "<stat_entry>
						<attribute name=\"Time\" show=\"YES\" link=\"NO\"><![CDATA[$key]]></attribute>
						<attribute name=\"Pages Viewed\" show=\"YES\" link=\"NO\"><![CDATA[". $list_results["".$key]["Pages Viewed"] ."]]></attribute>
						<attribute name=\"Visitor Count\" show=\"YES\" link=\"NO\"><![CDATA[". $list_results["".$key]["Visitor Count"] ."]]></attribute>
						<attribute name=\"Average\" show=\"YES\" link=\"NO\"><![CDATA[". $list_results["".$key]["Average"] ."]]></attribute>
					</stat_entry>";
			}else {
			$out_2.= "<stat_entry>
						<attribute name=\"Time\" show=\"YES\" link=\"NO\"><![CDATA[$key]]></attribute>
						<attribute name=\"Pages Viewed\" show=\"YES\" link=\"NO\"><![CDATA[0]]></attribute>
						<attribute name=\"Visitor Count\" show=\"YES\" link=\"NO\"><![CDATA[0]]></attribute>
						<attribute name=\"Average\" show=\"YES\" link=\"NO\"><![CDATA[0]]></attribute>
					</stat_entry>";

			}
		}
		$out_2.= "<stat_biggest>
					<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[]]></attribute>
					<attribute name=\"Pages Viewed\" show=\"YES\" link=\"NO\"><![CDATA[$biggest]]></attribute>
					<attribute name=\"Visitor Count\" show=\"YES\" link=\"NO\"><![CDATA[$biggest2]]></attribute>
					<attribute name=\"Average\" show=\"YES\" link=\"NO\"><![CDATA[$biggest3]]></attribute>
				</stat_biggest>";
		$out_2.= "<stat_total>
					<attribute name=\"\" show=\"YES\" link=\"NO\"><![CDATA[Totals]]></attribute>
					<attribute name=\"Pages Viewed\" show=\"YES\" link=\"NO\"><![CDATA[$total1]]></attribute>
					<attribute name=\"Visitor Count\" show=\"YES\" link=\"NO\"><![CDATA[$total2]]></attribute>
					<attribute name=\"Average\" show=\"YES\" link=\"NO\"><![CDATA[$total3]]></attribute>
				</stat_total>";
		$page_options ="".$this->generate_links();		
		return "<module name=\"user_access\" display=\"stats\">$page_options
		<stat_results label=\"Access per hour for ".$date_of_report."\" split_on='24' show_counter='0'>".$out_2."</stat_results>
		<graphs><graph>3</graph></graphs>$form
		</module>";
		return $page_options;
	}
	function get_entry_exit_points($parameters){
		$command = $this->check_parameters($parameters,"command");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$total =0;
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		
		$form = $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$list_results =Array();
		if ($command=="user_access_VIEW_ENTRY_POINTS"){
		$label=LOCALE_USER_ACCESS_REPORT_ENTRY;
		$sql="
		SELECT 
			access_log_owner, 
			access_log_url as entry_point , 
			min(access_log_date) as min_date 
		FROM user_access 
			inner join user_access_log on access_log_owner = user_access_identifier 
		where 
			user_access_client = 1
			$date_condition
		group by 
			access_log_owner,
			access_log_url, 
			access_log_date
		order by 
			access_log_owner, 
			access_log_date";
	////print $sql;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_a_specific_day",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$list_results = Array();
		if ($result){
			$total =0;
			$prev_owner="";
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				if ($prev_owner!=$r["access_log_owner"]){
					$prev_owner=$r["access_log_owner"];
					if (empty($list_results[$r["entry_point"]])){
						$list_results[$r["entry_point"]]=Array();
						$list_results[$r["entry_point"]]["Entry Point"] =1;
					}else{
						$list_results[$r["entry_point"]]["Entry Point"] ++;
					}
				}
				$total ++;
			}
		}
		} else {
		$label=LOCALE_USER_ACCESS_REPORT_EXIT;
		$sql="
		SELECT 
			access_log_owner, 
			access_log_url as exit_point , 
			min(access_log_date) as min_date 
		FROM user_access 
			inner join user_access_log on access_log_owner = user_access_identifier 
		where 
			user_access_client = 1
			$date_condition
		group by 
			access_log_owner,
			access_log_url, 
			access_log_date
			
		order by 
			access_log_owner, 
			access_log_date desc";
			//print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
			if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_a_specific_day",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		if ($result){
			$total =0;
			$prev_owner="";
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				if ($prev_owner!=$r["access_log_owner"]){
					$prev_owner=$r["access_log_owner"];
					if (empty($list_results[$r["exit_point"]])){
						$list_results[$r["exit_point"]]=Array();
						$list_results[$r["exit_point"]]["Exit Point"] =1;
					}else{
						$list_results[$r["exit_point"]]["Exit Point"] ++;
					}
				}
				$total ++;
			}
		}
		}
		$out="";
		$start=0;

		foreach($list_results as $key=>$result_data){
			$out.= "<stat_entry>
						<attribute name=\"URL\" show=\"YES\" link=\"NO\"><![CDATA[$key]]></attribute>";
//			////print $result_data["Entry Point"]." ".$result_data["Exit Point"];
			foreach($result_data as $type=>$total_hits){
				$out.="<attribute name=\"$type\" show=\"BAR\" link=\"NO\"><![CDATA[$total_hits]]></attribute>";
			}
			$out.="</stat_entry>";
		}
		$out ="<stat_results label='".$label."' total='$total' show_counter='0'>".$out."</stat_results>";
		$page_options ="".$this->generate_links();		
		return "<module name=\"user_access\" display=\"stats\">$page_options$form
			$out
			</module>";
	}

	function display_visitors($parameters){
		$page				=$this->check_parameters($parameters, "page",1);
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$cmd				=$this->check_parameters($parameters, "command");
		$parameters["_filter_all_months"] = "NO";
//		$parameters["_filter_all_days"] = "NO";
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == "" || $_filter_month == "-1" ){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == "" ){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		$form 			= $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$list_results=Array();
		$sql = "
SELECT 
	user_access_identifier, 
	user_access_ip_address, 
	count(*) as total, 
	language_label, 
	language_code,
	max(access_log_date) as max_date,
	contact_first_name,
	contact_last_name,
	user_access_browser,
	user_access_referer,
	user_access_user_identifier,
	contact_identifier,
	access_country,
	country
FROM user_access_log 
	inner join user_access on user_access_identifier = access_log_owner 
	left outer join user_access_ip_lookup on user_access_ip_address = access_ip 
	left outer join user_access_countries on tld = access_country 
	left outer join available_languages on user_access_accept_language = available_languages.language_code 
	left outer join contact_data on contact_user = user_access_user_identifier
WHERE 
	user_access_client=$this->client_identifier 
	$date_condition
GROUP BY 
	user_access_identifier, language_label, 
	user_access_ip_address, 
	language_code,
	contact_first_name,
	contact_last_name,
	user_access_browser,
	user_access_referer,
	user_access_user_identifier,
	contact_identifier,
	access_country,
	country	
order by max_date desc, language_label 
";
//print $sql;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		$num_of_pages = 0;
			$curr_total=0;
		if ($result){
			$total	= 0;
			$c=0;
			$rows = $this->call_command("DB_NUM_ROWS", Array($result));
			$start = ($page-1)*25;
			$num_of_pages = ceil($rows / 25);
			if ($start<$rows){
				$this->call_command("DB_SEEK", Array($result,$start));
			}
			$curr_total=0;
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($c<25)){
				$c++;
				$flag="";
				$user_access_identifier = $this->check_parameters($r,"user_access_identifier");
				$user_access_user_identifier = $this->check_parameters($r,"user_access_user_identifier");
				$user_access_ip_address = $this->check_parameters($r,"user_access_ip_address");
				$total 					= $this->check_parameters($r,"total");
				$curr_total+=$total;
				$language_label 		= $this->check_parameters($r,"language_label","Unknown");
				$language_code 			= strtolower($this->check_parameters($r,"language_code","N.A."));
				$country	 			= strtolower($this->check_parameters($r,"access_country","N.A."));
				$country_label 			= $this->check_parameters($r,"country");
				$max_date 				= $this->check_parameters($r,"max_date");
				$contact_first_name 	= $this->check_parameters($r,"contact_first_name");
				$contact_last_name 		= $this->check_parameters($r,"contact_last_name");
				$contact_identifier		= $this->check_parameters($r,"contact_identifier");
				$user_access_browser	= $this->check_parameters($r,"user_access_browser");
				$contact_details 		= $contact_last_name.", ".$contact_first_name;
				if ($contact_details==", "){
					$contact_details=LOCALE_STATS_NOT_REGISTERED;
				}
				if (strpos($language_code,'-')>0){
					$list = split('-',$language_code);
					$language_code = $list[1];
				} else {
					$language_code=$language_code;
				}
				$dom_url = $this->check_parameters($r,"user_access_referer");
				if (strpos(" ".$dom_url,"/")>0){
					$dom_list = split("/",$dom_url);
					$dom_display = $dom_list[2];
				} else {
					$dom_display = $dom_url;
					$dom_url ="";
				}
				
				$out .= "<stat_entry>
					<attribute name=\"C\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_ADDRESS_COUNTRY." $country_label'><![CDATA[$country]]></attribute>
					<attribute name=\"L\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_LANGUAGE." $language_label'><![CDATA[$language_code]]></attribute>
					<attribute name=\"Ip Address\" show=\"YES\" link=\"NO\"><![CDATA[$user_access_ip_address]]></attribute>
					<attribute name=\"Last on Site\" show=\"YES\" link=\"NO\"><![CDATA[$max_date]]></attribute>";
				if ($contact_details!=LOCALE_STATS_NOT_REGISTERED){
				$out .= "<attribute name=\"Contact Details\" show=\"YES\" link=\"contact_tracer\"><![CDATA[$contact_details]]></attribute>
						<attribute name=\"contact_tracer\" show=\"NO\" link=\"NO\"><![CDATA[command=USERACCESS_VIEW_CONTACT&identifier=$user_access_user_identifier]]></attribute>";
				}else {
					$out .= "<attribute name=\"Contact Details\" show=\"YES\" link=\"NO\"><![CDATA[$contact_details]]></attribute>";
				}
				if ($dom_url!=""){
				$out .= "<attribute name=\"Referer Domain\" show=\"YES\" link=\"dom_url\"><![CDATA[$dom_display]]></attribute>
					<attribute name=\"dom_url\" show=\"NO\" link=\"NO\"><![CDATA[$dom_url]]></attribute>";
				}else {
					$out .= "<attribute name=\"Referer Domain\" show=\"YES\" link=\"NO\"><![CDATA[$dom_display]]></attribute>";
				}
				$out .= "
					<attribute name=\"owner\" show=\"NO\" link=\"NO\"><![CDATA[command=USERACCESS_TRACE_SESSION&identifier=$user_access_identifier]]></attribute>
					<attribute name=\"Page Access\" show=\"BAR\" link=\"owner\"><![CDATA[$total]]></attribute>
				</stat_entry>";
			}
		}
		$cmd.="&amp;_filter_year=$_filter_year&amp;_filter_month=$_filter_month&amp;_filter_day=$_filter_day";
		$page_options ="".$this->generate_links();
		$pages= "<pages>";
		for($i=1;$i<=$num_of_pages;$i++){
			$pages.= "<page>$i</page>";
		}
		$pages.= "</pages>";
		return "<module name=\"user_access\" display=\"stats\">$form$page_options
		<stat_results 
			page='$page' total_pages='$num_of_pages'
			label=\"".LOCALE_STATS_DISPLAYING_VISITOR_REPORT."\" 
			total=\"$curr_total\" 
			report='$cmd' 
		>$pages
		".$out."</stat_results>
		</module>";
	}
	function display_hosts($parameters){
		$out="";
		$total=0;
		$list_results=Array();
		$page				=$this->check_parameters($parameters, "page",1);
		$cmd				=$this->check_parameters($parameters, "command");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		$filter="&_filter_year=$_filter_year&_filter_month=$_filter_month&_filter_day=$_filter_day";
		$cmd.="&amp;_filter_year=$_filter_year&amp;_filter_month=$_filter_month&amp;_filter_day=$_filter_day";

		$parameters["_filter_all_months"] = "NO";
		$form 			= $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$sql = "
			SELECT 
				language_code, 
				language_label, 
				user_access_identifier, 
				user_access_ip_address, 
				count(user_access_ip_address) as total, 
				max(access_log_date) as max_date,
				access_country,
				country
			FROM user_access_log 
				inner join user_access on user_access_identifier = access_log_owner 
				left outer join available_languages on user_access_accept_language = available_languages.language_code 
				inner join user_access_ip_lookup on user_access_ip_address = access_ip 
				inner join user_access_countries on tld = access_country 
			WHERE 
				user_access_client=$this->client_identifier 
				$date_condition
				and user_access_referer not like '%www.libertas-solutions.com%' and user_access_referer not like '%localhost%' and user_access_referer not like '%www.libertassolutions.co.uk%' and user_access_referer not like '%www.libertassolutions.com%' and user_access_referer not like '%www.libertas-solutions.co.uk%' and user_access_referer not like '%libertas-solutions.com%' and user_access_referer not like '%libertassolutions.com%' and user_access_referer not like '%libertassolutions.co.uk%' and user_access_referer not like '%libertas-solutions.co.uk%' and user_access_referer not like '%professor%' and user_access_referer not like '%caplo%' and user_access_referer not like '%localhost%' and user_access_referer not like '%127.0.0.1%'
			GROUP BY language_code,  user_access_identifier,
				language_label, 
				user_access_ip_address, 
				access_log_date,
				access_country,
				country
			order by max_date desc";
		//if ($_SERVER['REMOTE_ADDR'] == "61.5.139.12")		
		//	print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		$hidden = Array();
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
			
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$language_code 			= strtolower($this->check_parameters($r,"language_code","N.A."));
				$language_label			= $this->check_parameters($r,"language_label");
				$user_access_identifier = $this->check_parameters($r,"user_access_identifier");
				$user_access_ip_address = $this->check_parameters($r,"user_access_ip_address");
				$total 					= $this->check_parameters($r,"total");
				$max_date 				= $this->check_parameters($r,"max_date");
				$access_country			= strtolower($this->check_parameters($r,"access_country","N.A."));
				$country				= $this->check_parameters($r,"country");
//				////print "<p>$user_access_ip_address</p>";
				if (empty($list_results[$user_access_ip_address])){
					$list_results[$user_access_ip_address]= Array(
						"language" => $language_code,
						"Pages Viewed" => $total,
						"No of Visits"=>1,
						"Last On" => $max_date,
						"code" => $access_country,
						"country" => $country
					);
					$hidden[$user_access_ip_address] = Array(
						"user_access_identifier" => $user_access_identifier,
						"language_label" => $language_label
					);
				} else {
					$list_results[$user_access_ip_address]["Pages Viewed"]+=$total;
					$list_results[$user_access_ip_address]["No of Visits"]++;
				}
			}
		}
		$totals = array(
							"Pages Viewed"	=> 0,
							"No of Visits"	=> 0,
							"Average"		=> 1
		);
		$biggest = array(
							"Pages Viewed"	=> 1,
							"No of Visits"	=> 1,
							"Average"		=> 1
						);
		$c=0;
		
		$total_pages = ceil(count($list_results) / 25);
		$start= ($page-1)* 25;
		foreach($list_results as $key => $list){
			$average = round($list["Pages Viewed"] / $list["No of Visits"],2	);
			if (($c>=$start) && ($c<$start + 25)){
				$language_code = $list["language"];
				if (strpos($language_code,'-')>0){
					$l = split('-',$language_code);
					$language_code = $l[1];
				} else {
					$language_code=$language_code;
				}
				$language_label = $hidden[$key]["language_label"];
				$access_country = $list["code"];
				$country = $list["country"];
				$out .= "<stat_entry>
						<attribute name=\"C\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_ADDRESS_COUNTRY." $country'><![CDATA[$access_country]]></attribute>
						<attribute name=\"L\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_LANGUAGE." $language_label'><![CDATA[$language_code]]></attribute>
						<attribute name=\"Ip Address\" show=\"YES\" link=\"NO\"><![CDATA[$key]]></attribute>
						<attribute name=\"Last On\" show=\"YES\" link=\"NO\"><![CDATA[".Date("l, jS, F \a\\t H:i:s",strtotime($list["Last On"]))."]]></attribute>
						<attribute name=\"Pages Viewed\" show=\"YES\" link=\"NO\"><![CDATA[".$list["Pages Viewed"]."]]></attribute>
				";
				if($list["No of Visits"]==1){
					$out .= "<attribute name=\"No of Visits\" show=\"YES\" link=\"trace_session\"><![CDATA[".$list["No of Visits"]."]]></attribute>";
				} else{
					$out .= "<attribute name=\"No of Visits\" show=\"YES\" link=\"list_sessions\"><![CDATA[".$list["No of Visits"]."]]></attribute>";
				}
				$out .= "<attribute name=\"Average\" show=\"YES\" link=\"NO\"><![CDATA[$average]]></attribute>
						<attribute name=\"trace_session\" show=\"NO\" link=\"NO\"><![CDATA[?command=USERACCESS_TRACE_SESSION&identifier=".$hidden[$key]["user_access_identifier"]."]]></attribute>
						<attribute name=\"list_sessions\" show=\"NO\" link=\"NO\"><![CDATA[?command=USERACCESS_FILTER_HOST&identifier=".$key."$filter]]></attribute>
					</stat_entry>";
				if (isset($totals["Pages Viewed"])){
					$totals["Pages Viewed"] += $list["Pages Viewed"];
				}else{
					$totals["Pages Viewed"] = $list["Pages Viewed"];
				}
				if (isset($totals["No of Visits"])){
					$totals["No of Visits"] += $list["No of Visits"];
				}else{
					$totals["No of Visits"] = $list["No of Visits"];
				}
			}
			if ($biggest["Pages Viewed"] < $list["Pages Viewed"]){
				$biggest["Pages Viewed"] = $list["Pages Viewed"];
			}
			if ($biggest["No of Visits"] <$list["No of Visits"]){
				$biggest["No of Visits"] = $list["No of Visits"];
			}
			if ($biggest["Average"]<$average){
				$biggest["Average"] = $average;
			}
			if (($totals["Pages Viewed"]!=0) && ($totals["No of Visits"]!=0)){
			$totals["Average"] = round($totals["Pages Viewed"] / $totals["No of Visits"],2);
			} else {
			$totals["Average"]=0;
			}
			$c++;
		}
		$big= "<stat_biggest>
					<attribute name=\"LOCALE_STATS_FLAG\" show=\"NO\" link=\"NO\"><![CDATA[]]></attribute>
					<attribute name=\"Ip Address\" show=\"YES\" link=\"NO\"><![CDATA[]]></attribute>
					<attribute name=\"Last On\" show=\"YES\" link=\"owner\"><![CDATA[]]></attribute>
					<attribute name=\"Pages Viewed\" show=\"YES\" link=\"NO\"><![CDATA[".$biggest["Pages Viewed"]."]]></attribute>
					<attribute name=\"No of Visits\" show=\"YES\" link=\"NO\"><![CDATA[".$biggest["No of Visits"]."]]></attribute>
					<attribute name=\"Average\" show=\"YES\" link=\"NO\"><![CDATA[".$biggest["Average"]."]]></attribute>
					<attribute name=\"owner\" show=\"NO\" link=\"NO\"><![CDATA[]]></attribute>
				</stat_biggest>";	
		$out .= $this->publish_total($totals, "LOCALE_STATS_PAGE_HITS", "LOCALE_STATS_VISITORS",5).$big;
		$page_options ="
						
		".$this->generate_links();
		$page_list="";
		for ($i=1;$i<=$total_pages;$i++){
			$page_list.="<page>$i</page>";
		}
		return "<module name=\"user_access\" display=\"stats\">$form$page_options
		<stat_results page='$page' total_pages='$total_pages' report='$cmd' label=\"".LOCALE_STATS_DISPLAYING_HOSTS."\"  split_on='15' show_counter='1'><pages>$page_list</pages>".$out."</stat_results>
		</module>";
	}

	function display_filtered_hosts($parameters){
		$out="";
		$total=0;
		$list_results=Array();
		$page				=$this->check_parameters($parameters, "page",1);
		$cmd				=$this->check_parameters($parameters, "command");
		$ip					=$this->check_parameters($parameters, "identifier");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		$filter="&_filter_year=$_filter_year&_filter_month=$_filter_month&_filter_day=$_filter_day";
		$cmd.="&amp;_filter_year=$_filter_year&amp;_filter_month=$_filter_month&amp;_filter_day=$_filter_day";

		$parameters["_filter_all_months"] = "NO";
		$form 			= $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$sql = "
			SELECT 
				language_code, 
				language_label, 
				user_access_identifier, 
				user_access_ip_address, 
				count(user_access_ip_address) as total, 
				max(access_log_date) as max_date,
				access_country,
				country
			FROM user_access_log 
				inner join user_access on user_access_identifier = access_log_owner 
				left outer join available_languages on user_access_accept_language = available_languages.language_code 
				left outer join user_access_ip_lookup on user_access_ip_address = access_ip 
				left outer join user_access_countries on tld = access_country 
			WHERE 
				user_access_client=$this->client_identifier and 
				user_access_ip_address='$ip'
				$date_condition
			GROUP BY language_code,  user_access_identifier,
				language_label, 
				user_access_identifier, 
				user_access_ip_address, 
				access_log_date,
				access_country,
				country
			order by max_date desc";
		////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		$hidden = Array();
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
					

			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($c<200)){
				$language_code 			= strtolower($this->check_parameters($r,"language_code","N.A."));
				$language_label			= $this->check_parameters($r,"language_label","unknown");
				$user_access_identifier = $this->check_parameters($r,"user_access_identifier");
				$user_access_ip_address = $this->check_parameters($r,"user_access_ip_address");
				$total 					= $this->check_parameters($r,"total");
				$max_date 				= $this->check_parameters($r,"max_date");
				$access_country			= strtolower($this->check_parameters($r,"access_country","N.A."));
				$country				= $this->check_parameters($r,"country");
//				////print "<p>$user_access_ip_address</p>";
				if (empty($list_results[$user_access_identifier])){
					$list_results[$user_access_identifier]= Array(
						"language" => $language_code,
						"Pages Viewed" => $total,
						"No of Visits"=>1,
						"Last On" => $max_date,
						"code" => $access_country,
						"country" => $country
					);
					$hidden[$user_access_identifier] = Array(
						"user_access_ip_address" => $user_access_ip_address,
						"language_label" => $language_label
					);
				} else {
					$list_results[$user_access_identifier]["Pages Viewed"]+=$total;
					$list_results[$user_access_identifier]["No of Visits"]++;
				}
			}
		}
		$totals = array(
							"Pages Viewed"	=> 0,
							"No of Visits"	=> 0,
							"Average"		=> 1
		);
		$biggest = array(
							"Pages Viewed"	=> 1,
							"No of Visits"	=> 1,
							"Average"		=> 1
						);
		$c=0;
		
		$total_pages = ceil(count($list_results) / 25);
		$start= ($page-1)* 25;
		foreach($list_results as $key => $list){
			$average = round($list["Pages Viewed"] / $list["No of Visits"],2	);
			if (($c>=$start) && ($c<$start + 25)){
				$language_code = $list["language"];
				if (strpos($language_code,'-')>0){
					$l = split('-',$language_code);
					$language_code = $l[1];
				} else {
					$language_code=$language_code;
				}
				$language_label = $hidden[$key]["language_label"];
				$access_country = $list["code"];
				$country = $list["country"];
				$out .= "<stat_entry>
						<attribute name=\"C\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_ADDRESS_COUNTRY." $country'><![CDATA[$access_country]]></attribute>
						<attribute name=\"L\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_LANGUAGE." $language_label'><![CDATA[$language_code]]></attribute>
						<attribute name=\"Last On\" show=\"YES\" link=\"NO\"><![CDATA[".Date("l, jS, F \a\\t H:i:s",strtotime($list["Last On"]))."]]></attribute>
						<attribute name=\"Pages Viewed\" show=\"YES\" link=\"trace_session\"><![CDATA[".$list["Pages Viewed"]."]]></attribute>
						<attribute name=\"trace_session\" show=\"NO\" link=\"NO\"><![CDATA[?command=USERACCESS_TRACE_SESSION&identifier=$key]]></attribute>
					</stat_entry>";
				if (isset($totals["Pages Viewed"])){
					$totals["Pages Viewed"] += $list["Pages Viewed"];
				}else{
					$totals["Pages Viewed"] = $list["Pages Viewed"];
				}
			}
			if ($biggest["Pages Viewed"] < $list["Pages Viewed"]){
				$biggest["Pages Viewed"] = $list["Pages Viewed"];
			}
			$c++;
		}
		$page_options ="".$this->generate_links();
		$page_list="";
		for ($i=1;$i<=$total_pages;$i++){
			$page_list.="<page>$i</page>";
		}
		return "<module name=\"user_access\" display=\"stats\">$form$page_options
		<stat_results page='$page' total_pages='$total_pages' report='$cmd' label=\"".LOCALE_STATS_DISPLAYING_FILTER_HOSTS."\"  split_on='15' show_counter='1'><pages>$page_list</pages>".$out."</stat_results>
		</module>";
	}
	
	function view_contact($parameters){
		$out="";
		$total=0;
		$list_results=Array();
		$identifier			=$this->check_parameters($parameters, "identifier");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
/*		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		*/
		$parameters["_filter_all_months"] = "NO";
		$form 			= $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$sql = "
SELECT contact_data.*, user_access.*, count(access_log_owner) as total, max(access_log_date) as max_date, access_country
FROM contact_data 
	inner join user_access on user_access_user_identifier = contact_user 
	inner join user_access_log on user_access_identifier = access_log_owner 
	inner join user_access_ip_lookup on user_access_ip_address = access_ip 
WHERE 
	user_access_client = $this->client_identifier and contact_client=$this->client_identifier 
	and contact_user = $identifier
	$date_condition
	group by access_log_owner
	order by max_date desc
";
//////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
$user_access_identifier=-2;
//		////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($c<200)){
				$user_access_identifier = $this->check_parameters($r,"user_access_identifier");
				$user_access_ip_address = $this->check_parameters($r,"user_access_ip_address");
				$max_date 				= $this->check_parameters($r,"max_date");
				
				$out .= "<stat_entry>
						<attribute name=\"C\" show=\"FLAG\" link=\"NO\"><![CDATA[".strtolower($r["access_country"])."]]></attribute>
						<attribute name=\"Ip Address\" show=\"YES\" link=\"NO\"><![CDATA[$user_access_ip_address]]></attribute>
						<attribute name=\"Last On\" show=\"YES\" link=\"NO\"><![CDATA[".$max_date."]]></attribute>
						<attribute name=\"Pages Viewed\" show=\"YES\" link=\"owner\"><![CDATA[".$r["total"]."]]></attribute>
						<attribute name=\"owner\" show=\"NO\" link=\"NO\"><![CDATA[identifier=$user_access_identifier]]></attribute>
					</stat_entry>";
			}
		}

		$page_options ="".$this->generate_links();
//		<graphs><graph>4</graph><graph>5</graph><graph>6</graph></graphs>
		return $this->call_command("CONTACT_VIEW_USER",Array("identifier"=>$identifier))."<module name=\"user_access\" display=\"stats\">$form$page_options
		<stat_results label=\"".LOCALE_STATS_DISPLAYING_HOSTS."\"  link=\"".$this->module_command."TRACE_SESSION\" split_on='15' show_counter='1'>".$out."</stat_results></module>";
	}
	
	function display_most_popular($parameters){
			$out="";
		$total=0;
		$list_results=Array();
		$identifier			=$this->check_parameters($parameters, "identifier");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		if ($_filter_year == ""){
			$_filter_year = Date("Y");
			$parameters["_filter_year"] = Date("Y");
		}
		if ($_filter_month == ""){
			$_filter_month = Date("m");
			$parameters["_filter_month"] = Date("m");
		}
		if ($_filter_day == ""){
			$_filter_day = Date("d");
			$parameters["_filter_day"] = Date("d");
		}
		$parameters["_filter_all_months"] = "NO";
		$form 			= $this->display_form($parameters);
		$date_condition = $this->display_date_condition($parameters);
		$sql = "
SELECT user_access_log.access_log_url, count(user_access_log.access_log_url) as total
FROM user_access_log
WHERE 
	access_log_client = $this->client_identifier 
	$date_condition
	group by access_log_url
	order by total desc
";

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($c<50)){
				$c++;
				$url 			= $this->check_parameters($r,"access_log_url");
				if (strlen($url)>55){
					$display_url = substr($url,0,15).".....".substr($url,strlen($url)-25);
				} else {
					$display_url = $url;
				}
				$out .= "<stat_entry>
						<attribute name=\"URL\" show=\"YES\" link=\"owner\"><![CDATA[$display_url]]></attribute>
						<attribute name=\"Pages Viewed\" show=\"YES\" link=\"NO\"><![CDATA[".$r["total"]."]]></attribute>
						<attribute name=\"owner\" show=\"NO\" link=\"NO\"><![CDATA[http://".$this->parent->domain."$url]]></attribute>
					</stat_entry>";
			}
		}

		$page_options ="".$this->generate_links();
//		<graphs><graph>4</graph><graph>5</graph><graph>6</graph></graphs>

		return "<module name=\"user_access\" display=\"stats\">$form$page_options
		<stat_results label=\"".LOCALE_STATS_MOST_POPULAR."\"   split_on='15' show_counter='1'>".$out."</stat_results></module>";
	}
	
	function display_view_referer_list($parameters){
		$page				=$this->check_parameters($parameters, "page",1);
		$domain				=$this->check_parameters($parameters, "domain", "");
		$cmd				=$this->check_parameters($parameters, "command");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$form 				= $this->display_form($parameters);
		$date_condition 	= $this->display_date_condition($parameters);
		$list_results=Array();

		$months = array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);
		$domain_list = $this->call_command("CLIENT_GET_DOMAINS");
		$max = count($domain_list);
		$cond = $date_condition;
		for($index=0;$index<$max;$index++){
			$cond .=" and user_access_referer not like '%".$domain_list[$index]."%'";
		}
		$filter = "&_filter_year=$_filter_year&_filter_month=$_filter_month&_filter_day=$_filter_day";
		$cmd.="&amp;_filter_year=$_filter_year&amp;_filter_month=$_filter_month&amp;_filter_day=$_filter_day&amp;domain=$domain";

		$sql = "SELECT 
	user_access_referer,
	user_access_ip_address, 
	user_access_user_identifier, 
	user_access_identifier, 
	count(user_access_ip_address) as total_pages,
	max(access_log_date) as access_date,
	access_country,
	country,
	language_code, language_label
FROM user_access_log 
	inner join user_access on user_access_identifier = access_log_owner 
	left outer join available_languages on user_access_accept_language = available_languages.language_code 
	left outer join user_access_ip_lookup on access_ip = user_access_ip_address 
	left outer join user_access_countries on access_country = tld 
WHERE 
	user_access_referer like 'http://$domain%' and 
	user_access_client=$this->client_identifier 
	$cond
group by 
	user_access_referer,
	user_access_ip_address,
	user_access_identifier
order by user_access_identifier desc
	";

////print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
			$start=($page-1)*25;
			$num_of_rows = $this->call_command("DB_NUM_ROWS",Array($result));
			$num_of_pages  = ceil($num_of_rows/25);
			if ($start<$num_of_rows){
				$this->call_command("DB_SEEK", Array($result,$start));
			}
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($c<25)){
			$c++;
				$language_code = strtolower($this->check_parameters($r,"language_code","N.A."));
				if (strpos($language_code,'-')>0){
					$l = split('-',$language_code);
					$language_code = $l[1];
				} else {
					$language_code=$language_code;
				}
				if (empty($list[$r["user_access_ip_address"]])){
					$list[$r["user_access_ip_address"]] = Array(
							"counter"				=> 1,
							"access_country"			=> strtolower($this->check_parameters($r,"access_country","n.a.")),
							"country"					=> $this->check_parameters($r,"country"),
							"access_date"				=> $r["access_date"],
							"user_access_ip_address"	=> $r["user_access_ip_address"],
							"user_access_referer"		=> substr($r["user_access_referer"],0,55),
							"total_pages" 				=> $r["total_pages"],
							"user_access_referer"		=> $r["user_access_referer"],
							"language_code"				=> $language_code,
							"language_label"			=> $this->check_parameters($r,"language_label")
						);
				} else {
					$list[$r["user_access_ip_address"]]["counter"]++;
				}
			}
			
			foreach($list as $key=>$results){
				$out.= "<stat_entry>
							<attribute name=\"C\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_ADDRESS_COUNTRY." ".$results["country"]."'><![CDATA[".$results["access_country"]."]]></attribute>
							<attribute name=\"L\" show=\"FLAG\" link=\"NO\" alt='".LOCALE_LANGUAGE." ".$results["language_label"]."'><![CDATA[".$results["language_code"]."]]></attribute>
							<attribute name=\"".LOCALE_STAT_TIME."\" show=\"YES\" link=\"NO\"><![CDATA[".$results["access_date"]."]]></attribute>
							<attribute name=\"".LOCALE_STAT_IP_ADDRESS."\" show=\"YES\" link=\"host\"><![CDATA[".$results["user_access_ip_address"]."]]></attribute>
							<attribute name=\"".LOCALE_STAT_REFERER."\" show=\"YES\" link=\"LINK\"><![CDATA[".substr($results["user_access_referer"],0,55)."]]></attribute>
							<attribute name=\"".LOCALE_STAT_TOTAL_SESSIONS."\" show=\"BAR\" link=\"NO\"><![CDATA[".$results["counter"]."]]></attribute>
							<attribute name=\"LINK\" show=\"NO\" link=\"NO\"><![CDATA[".$results["user_access_referer"]."]]></attribute>
							<attribute name=\"host\" show=\"NO\" link=\"NO\"><![CDATA[?command=USERACCESS_FILTER_HOST&identifier=".$results["user_access_ip_address"]."$filter]]></attribute>";
//				$out.= "<attribute name=\"session\" show=\"NO\" link=\"NO\"><![CDATA[identifier=".$r["user_access_identifier"]."]]></attribute>";
				$out.= "</stat_entry>";
				$total +=$results["counter"];
			}
	//		////print $result;
		}
		$page_options ="".$this->generate_links();		
		$pages= "<pages>";
		for($i=1;$i<=$num_of_pages;$i++){
			$pages.= "<page>$i</page>";
		}
		$pages.= "</pages>";
		return "<module name=\"user_access\" display=\"stats\">
					$form
					$page_options 
		<stat_results 
			page='$page' total_pages='$num_of_pages'
			label=\"".LOCALE_STAT_FILTER_DOMAIN_REFERER_BY_IP."\" 
			total=\"$total\" 
			report='$cmd' 
		>$pages".$out."</stat_results>
				</module>";
	}
	
	function load_reports(){
		$this->module_reports	= array();
		$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_SUMMARY", LOCALE_USER_ACCESS_REPORT_SUMMARY, LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL);
		$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_YEAR_STATS", LOCALE_USER_ACCESS_REPORT_YEAR, LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL);
		$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_MONTH", LOCALE_USER_ACCESS_REPORT_MONTH, LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL);
		$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_WEEK", LOCALE_USER_ACCESS_REPORT_WEEK, LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL);
		$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_REFERERS", LOCALE_USER_ACCESS_REPORT_REFERALS, LOCALE_USER_ACCESS_REPORT_TYPE_ACCESS);
		$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_BOTS", LOCALE_STAT_BASED_ON_BOTS, LOCALE_USER_ACCESS_REPORT_TYPE_ACCESS);

		if (($this->parent->server[LICENCE_TYPE]==ECMS) || ($this->parent->server[LICENCE_TYPE]==MECM)){
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_LANGUAGES", LOCALE_USER_ACCESS_REPORT_LANGUAGE, LOCALE_USER_ACCESS_REPORT_TYPE_PROFILE);
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_COUNTRIES", LOCALE_USER_ACCESS_REPORT_COUNTRIES, LOCALE_USER_ACCESS_REPORT_TYPE_PROFILE);
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_ENTRY_POINTS", LOCALE_USER_ACCESS_REPORT_ENTRY, LOCALE_USER_ACCESS_REPORT_TYPE_ACCESS);
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_EXIT_POINTS", LOCALE_USER_ACCESS_REPORT_EXIT, LOCALE_USER_ACCESS_REPORT_TYPE_ACCESS);
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_HOSTS", LOCALE_USER_ACCESS_REPORT_HOST, LOCALE_USER_ACCESS_REPORT_TYPE_PROFILE);
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_THIS_DAY", LOCALE_USER_ACCESS_REPORT_DAY, LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL);
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_TIMED", LOCALE_USER_ACCESS_REPORT_TODAY, LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL);
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_VISITORS", LOCALE_USER_ACCESS_REPORT_VISITOR, LOCALE_USER_ACCESS_REPORT_TYPE_PROFILE);
			$this->module_reports[count($this->module_reports)] = array("FILES_VIEW_DOWNLOAD_REPORT&amp;show=file", LOCALE_USER_ACCESS_REPORT_FILE_BY_FILENAME, LOCALE_USER_ACCESS_REPORT_TYPE_FILES);
			$this->module_reports[count($this->module_reports)] = array("FILES_VIEW_DOWNLOAD_REPORT&amp;show=user", LOCALE_USER_ACCESS_REPORT_FILE_BY_USER, LOCALE_USER_ACCESS_REPORT_TYPE_FILES);
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_MOST_POPULAR", LOCALE_USER_ACCESS_REPORT_POPULAR, LOCALE_USER_ACCESS_REPORT_TYPE_ACCESS);
		}
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_MOST_ACTIVE_SESSIONS",LOCALE_USER_ACCESS_REPORT_ACTIVE_SESSIONS,LOCALE_USER_ACCESS_REPORT_TYPE_PROFILE);
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_DOMAIN",LOCALE_USER_ACCESS_REPORT_DOMAINS,LOCALE_USER_ACCESS_REPORT_TYPE_ACCESS);
			$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_USERS",LOCALE_USER_ACCESS_REPORT_USERS,LOCALE_USER_ACCESS_REPORT_TYPE_GENERAL);
		}
		$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_BROWSERS", LOCALE_USER_ACCESS_REPORT_BROWSERS, LOCALE_USER_ACCESS_REPORT_TYPE_PROFILE);
		$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_OS", LOCALE_USER_ACCESS_REPORT_OS, LOCALE_USER_ACCESS_REPORT_TYPE_PROFILE);
		$this->module_reports[count($this->module_reports)] = array($this->module_command."VIEW_KEYWORDS", LOCALE_USER_ACCESS_REPORT_SEARCH, LOCALE_USER_ACCESS_REPORT_TYPE_SEARCH);
//		$this->module_reports[count($this->module_reports)] = array("PAGE_REPORT_LIST", LOCALE_PAGE_BASIC_REPORTS, LOCALE_USER_ACCESS_REPORT_TYPE_PAGE);

	}

	function display_bot_access($parameters){
		$page				=$this->check_parameters($parameters, "page",1);
		$cmd				=$this->check_parameters($parameters, "command");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$form 				= $this->display_form($parameters);
		$date_condition 	= $this->display_date_condition($parameters);
		$list_results=Array();
		$list = Array();

		$months = array(
			"Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec"
		);
		$cond = $date_condition;
		$filter = "&_filter_year=$_filter_year&_filter_month=$_filter_month&_filter_day=$_filter_day";
		$cmd.="&amp;_filter_year=$_filter_year&amp;_filter_month=$_filter_month&amp;_filter_day=$_filter_day";
		$num_of_pages = 0;
		$sql = "
SELECT 
	user_access_bot_name,
	count(user_access_bot_name) as total 
FROM 
	user_access_log 
inner join user_access on user_access_identifier = access_log_owner 
WHERE 
	user_access_bot_name != '' and 
	user_access_client=$this->client_identifier 
	$cond
group by 
user_access_bot_name
order by user_access_bot_name asc 
";

//print "<p>".__FILE__." Line::".__LINE__."<br>$sql</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
			$start=($page-1)*25;
			$num_of_rows = $this->call_command("DB_NUM_ROWS",Array($result));
			$num_of_pages  = ceil($num_of_rows/25);
			if ($start<$num_of_rows){
				$this->call_command("DB_SEEK", Array($result,$start));
			}
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($c<25)){
			$c++;
				$language_code = strtolower($this->check_parameters($r,"language_code","N.A."));
				if (strpos($language_code,'-')>0){
					$l = split('-',$language_code);
					$language_code = $l[1];
				} else {
					$language_code=$language_code;
				}
				if (empty($list[$r["user_access_bot_name"]])){
					$list[$r["user_access_bot_name"]] = Array(
							"counter"		=> $this->check_parameters($r,"total",0),
							"bot"			=> $this->check_parameters($r,"user_access_bot_name","n.a."),
							"total"			=> $this->check_parameters($r,"total",0)
						);
				} else {
					$list[$r["user_access_ip_address"]]["counter"]++;
				}
			}
			
			foreach($list as $key=>$results){
				$out.= "<stat_entry>
							<attribute name=\"Name of Robot \" show=\"YES\" link=\"NO\"><![CDATA[".$results["bot"]."]]></attribute>
							<attribute name=\"".LOCALE_TOTAL."\" show=\"YES\" link=\"host\"><![CDATA[".$results["total"]."]]></attribute>
						</stat_entry>";
				$total +=$results["counter"];
			}
	//		////print $result;
		}
$page_options ="".$this->generate_links();		
		$pages= "<pages>";
		for($i=1;$i<=$num_of_pages;$i++){
			$pages.= "<page>$i</page>";
		}
		$pages.= "</pages>";
		return "<module name=\"user_access\" display=\"stats\">
					$form
					$page_options 
		<stat_results 
			page='$page' total_pages='$num_of_pages'
			label=\"".LOCALE_STAT_BASED_ON_BOTS."\" 
			total=\"$total\" 
			report='$cmd' 
		>$pages".$out."</stat_results>
				</module>";
	}

}
?>