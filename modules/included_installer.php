<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.include_page.php
*/
/**
* This is the main page it is loaded by installer only 
* it loads the install engine
*/
require_once("admin_status.php");

if (!defined("GENERAL_TEXT_BACK")){
	define("LS__DIR_PERMISSION", 0775);
	define("LS__FILE_PERMISSION", 0664);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Global definitions
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	define ("GENERAL_TEXT_BACK","Back");
	define ("MSG_ILLEGAL_ACCESS","<p><b>Illegal Access Attempted</b></p><p>Your Access attempt has been logged.<br/>The reason for this error might be that you are required to be logged in for the page you are trying to access</p>");
	define ("GENERAL_BUTTON_BACK","<button command=\"GENERAL_BACK\" alt=\"LOCALE_CANCEL\" iconify=\"CANCEL\">Cancel</button>");
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- define the what to log
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	define ("LOG_FUNCTION_ACCESS",1);
	define ("LOG_DB_ACCESS",2);
	define ("LOG_USER_ACCESS",4);
	define ("LOG_PAGE_ACCESS",8);
	define ("LOG_ILLEGAL_ACCESS",16);
	define ("LOG_IT",LOG_ILLEGAL_ACCESS+LOG_PAGE_ACCESS+LOG_USER_ACCESS);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- define the curreny symbol to use
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	define ("CURRENCY_SYMBOL","&pound;");
	define ("PAGE_SIZE",10);
	define ("FORUM_PAGE_SIZE",50);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
}
//include_once (dirname(__FILE__)."/mytidy/mytidy.php");
$refer_new = false;
$domain = "";	
$my_script = "";	
$domain = $_SERVER["HTTP_HOST"];	
define("ROOT_LOCATION",$domain);
$my_script = $_SERVER["PHP_SELF"];	
define ("DATE_EXPIRES",md5("EXPIRES"));
define ("IP_ADDRESS",md5("IP"));
define ("LICENCE_TYPE",md5("SETUP"));
define ("UNLIMITED",md5("-1"));
define ("ECMS",md5("ENTERPRISE"));
define ("MECM",md5("LITE"));
define ("SITE_WIZARD",md5("ULTRA"));

if (!empty($_SERVER["HTTP_REFERER"])){
	$referer = "REFERER :: ".strtolower($_SERVER["HTTP_REFERER"]);
	$find = strtolower("http://".$domain);
	if (strpos($referer,$find)===false){
		$refer_new = true;
	}
} else {
	$refer_new = true;
}
$refer_new = false;
session_start();
if ((session_id()=="") || ($refer_new)){
	session_id(uniqid(rand()));
} 
include_once (dirname(__FILE__)."/libertas.installer.php");
$version = phpversion();
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Get system information based on the version of the php engine being used.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
if ($_POST){
	$qstring = $_POST;
} else {
	$qstring = $_GET;
}
if (empty($qstring["command"])){
	if (empty($command)){
		$command="";	
	}
}else {
	$command=$qstring["command"];
}

if (!empty($script)){
	$my_script=$script;
}
if (!empty($identifier)){
	$qstring["identifier"]=$identifier;
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- load the engine set mode
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
if (empty($mode)){
	$mode = "website";
}
$engine = new installer($domain,session_id(),$my_script,$mode);
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- if Engine Loaded then get screen information
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
$out="";
if ($engine->status==1){
	$engine->call_command("SESSION_RETRIEVE",array(session_id()));
	$engine->call_command("INSTALLER_CALL_COMMAND",array($command,$qstring));
}else{
	if ($engine->status==-1){
		print "<h1>Server Stopped - Error code #LS000001</h1><p><strong>Your licence has expired</strong></p><p>For support please contact your libertas Account Manager at <a href=\"http://www.libertas-solutions.com\">http://www.libertas-solutions.com</a></p>";
	}else if ($engine->status==-2){
		print "<h1>Server Stopped - Error code #LS000002</h1><p><strong>I am sorry the server was unable to find the licence file.</strong></p><p>For support please contact your libertas Account Manager at <a href=\"http://www.libertas-solutions.com\">http://www.libertas-solutions.com</a></p>";
	}else if ($engine->status==-3){
		print "<h1>Server Stopped - Error code #LS000003</h1><p><strong>I am sorry the server was unable to connect to the database server.</strong></p><p>For support please contact your libertas Account Manager at <a href=\"http://www.libertas-solutions.com\">http://www.libertas-solutions.com</a></p>";
	}else if ($engine->status==-4){
		print "<h1>Server Stopped - Error code #LS000004</h1><p><strong>I am sorry although the server was able to connect to the database server.  It was unable to find the specified Database</strong></p><p>For support please contact your libertas Account Manager at <a href=\"http://www.libertas-solutions.com\">http://www.libertas-solutions.com</a></p>";
	}else{
		print "<h1>Server Stopped - Error code #LS000005</h1><p><strong>I am sorry the server was unable to find the domain in the licence file.</strong></p><p>For support please contact your System Administrator and inform them of the domain you used.</p>";
	}
}
$engine->call_command("INSTALLER_CLOSE");
?>