<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.include_page.php
*/
/**
* This is the main page it is loaded by all pages except for the installer / other special engine modes 
* it loads the core engine
*/
require_once("admin_status.php");
$uab=" ";
if(isset($_SERVER['HTTP_USER_AGENT'])){
	$uab = strtoupper(" ".$_SERVER['HTTP_USER_AGENT']);
}
//$uab = "sfda asd fas d fasdCSE HTML VALIDATOR asdasd";
//if (($pos = strpos($uab,"CSE HTML VALIDATOR"))>0){
//	print "<h1>Sorry this robot is currently blocked from this site</h1>";
//	exit();
//}
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
$CLUSTER_SESSION 	= 1;
$qstring			= Array();
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- if auto_start is set to zero we can give our sessions the Libertas Name of LEI
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
$debug_output ="";
ini_set("max_execution_time",90);
if (ini_get("session.auto_start").""=="0"){
	session_name("LEI");
}
//ini_set("session.save_handler","user");
if (ini_get("session.use_trans_sid").""=="1"){
	ini_set("session.use_trans_sid","0");
}
ini_set("session.use_trans_sid","0");
if (ini_get("session.use_cookies").""=="0"){
	ini_set("session.use_cookies","1");
}
include_once (dirname(__FILE__)."/libertas.engine.php");
$refer_new = false;
$domain = "";	
$my_script = "";	
$domain = $_SERVER["HTTP_HOST"];	
$my_script = $_SERVER["PHP_SELF"];	
define("ROOT_LOCATION",$domain);
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
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* Get system information based on the version of the php engine being used.  
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
	if(empty($qstring["identifier"])){
		$qstring["identifier"]=$identifier;
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* load the engine set mode
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
if (empty($mode)){
	$mode = "website";
}
if (empty($fake_uri)){
	$fake_uri="";
} 
if (empty($fake_title)){
	$fake_title="";
} 
if (empty($ignoreCommand)){
	$ignoreCommand="";
} 
$qstring["fake_uri"]=$fake_uri;
$qstring["fake_title"]=$fake_title;
$qstring["ignoreCommand"]=$ignoreCommand;
if (empty($category)){
	$category="";
} 
$qstring["category"] 	= $category;

if (empty($qstring["letter"])){
	if (!empty($letter)){
		$qstring["letter"] 	= $letter;	
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* starting to use an array called "extra" for extra parameters from special files stops overriding by passing 
* parameters
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
if(!empty($extra)){
	foreach($extra as $key => $value){
		$qstring[$key] = $value;
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* Create new instance of the Engine 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
$GLOBALS["engine"] = new engine($domain,$my_script,$mode);
if ($CLUSTER_SESSION==1){
	ini_set("session.save_handler","files");
	include_once (dirname(__FILE__)."/libertas.clusterSession.php");
}
$bot=0;
//$uab=" GOOGLEBOT";
//$uab = "MOZILLA/4.0 (COMPATIBLE; MSIE 4.01; WINDOWS CE; PPC; 240X320)";
if (($pos = strpos($uab, "GOOGLEBOT"))>0){
	$session_id = md5("SearchBot - Google");
	$bot=1;
} else if (($pos = strpos($uab, "IA_ARCHIVER"))>0){
	$session_id = md5("SearchBot - IA Archiver"); // Alexa Archiver
	$bot=1;
} else if (($pos = strpos($uab, "CRAWLER"))>0){
	$session_id = md5("SearchBot - Crawler");
	$bot=1;
} else if (($pos = strpos($uab, "WALKER"))>0){
	$session_id = md5("SearchBot - Walker");
	$bot=1;
} else if (($pos = strpos($uab, "SCRUB"))>0){
	$session_id = md5("SearchBot - Scrub");
	$bot=1;
} else if (($pos = strpos($uab, "INTERNETSEER.COM"))>0){
	$session_id = md5("SearchBot - Internetseer.com");
	$bot=1;
} else if (($pos = strpos($uab, "SCOOTER"))>0){
	$session_id = md5("SearchBot - Scooter");
	$bot=1;
} else if (($pos = strpos($uab, "SPIDER"))>0){
	$session_id = md5("SearchBot - Spider");
	$bot=1;
} else if (($pos = strpos($uab, "SLURP@INKTOMI.COM"))>0){
	$session_id = md5("SearchBot - Slurp");
	$bot=1;
} else if (($pos = strpos($uab, "BOT"))>0){
	$session_id = md5("SearchBot");
	$bot=1;
} else {
	$session_id = "";
}

if (strlen($session_id)==0){
	session_start();
//	if ((session_id()=="") || ($refer_new)){
//		session_id(uniqid(rand()));
//	}
	$session_id = session_id();
//	print $session_id;
} else {
	session_id($session_id);
}
//print "[$session_id][".session_id()."]";
if ($bot==1){
	$_SESSION["IS_BOT"]=$bot;
}
$version = phpversion();
// list of browser string keys for PDAs / phones 
$pdalist = Array(
		"AVANTGO", "PROXINET", "DANGER HIPTOP", "FTXBROWSER", "WINDOWS CE", "NETFRONT", "PDA", 
		"PALMOS", "XIINO", "BLACKBERRY", "BSQUARE", "NOKIA", "SMARTPHONE", "REGKING", "PSION", 
		"EPOC", "SAMSUNG", "AU-MIC/1.1.4.0 20722 MMP/2.0", "SIEMENS", "SHARP-TQ-GX10I", 
		"REQWIRELESSWEB", "SONY/ERICSSON", "SONYERICSSON", "PALMSOURCE", "BLAZER", "ELAINE"
	);

if (preg_match("(".implode("|",$pdalist).")",$uab)){
	$_SESSION["displaymode"]="pda";
} else {
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Start this instance of te engine.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

$GLOBALS["engine"]->start();

/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- if Engine Loaded then get screen information
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
$out="";
/*
	print "";
	print_r($qstring);
	print_r($_GET);
	print_r($_POST);
	print_r($HTTP_SERVER_VARS);
	print "";
*/
if ($GLOBALS["engine"]->status==1){
	$GLOBALS["engine"]->call_command("SESSION_RETRIEVE",array(session_id()));
	$qstring["my_session_identifier"]=$session_id;
	$GLOBALS["engine"]->call_command("ENGINE_CALL_COMMAND",array($command,$qstring));
}else{
	$msg="";
	if ($GLOBALS["engine"]->status==-1){
		$msg = "<html>
				<style>
					h1{font-size:16px;}
					body{font-size:12px;}
					
				</style><body>
					<center><table width='500' style='border=1px solid #000000;filter:progid:dximagetransform.microsoft.gradient(gradienttype=0,startcolorstr=#cccccc,endcolorstr=#FFFFFF);'><tr><td>
					<h1><img src='/libertas_images/themes/title_bullet.gif'/>Server Stopped - Error code #LS000001</h1><p><strong>Your licence has expired</strong></p><p>For support please contact your libertas Account Manager at <a href=\"http://www.libertas-solutions.com\">http://www.libertas-solutions.com</a></p>
					</td></tr></table></center></body></html>";
	}else if ($GLOBALS["engine"]->status==-2){
		$msg = "<html>
				<style>
					h1{font-size:16px;}
					body{font-size:12px;}
					
				</style><body>
					<center><table width='500' style='border=1px solid #000000;filter:progid:dximagetransform.microsoft.gradient(gradienttype=0,startcolorstr=#cccccc,endcolorstr=#FFFFFF);'><tr><td>
					<h1><img src='/libertas_images/themes/title_bullet.gif'/>Server Stopped - Error code #LS000002</h1><p><strong>I am sorry the server was unable to find the licence file.</strong></p><p>For support please contact your libertas Account Manager at <a href=\"http://www.libertas-solutions.com\">http://www.libertas-solutions.com</a></p>
					</td></tr></table></center></body></html>";
	}else if ($GLOBALS["engine"]->status==-3){
		$msg = "<html>
				<style>
					h1{font-size:16px;}
					body{font-size:12px;}
					
				</style><body>
					<center><table width='500' style='border=1px solid #000000;filter:progid:dximagetransform.microsoft.gradient(gradienttype=0,startcolorstr=#cccccc,endcolorstr=#FFFFFF);'><tr><td>
					<h1><img src='/libertas_images/themes/title_bullet.gif'/>Server Stopped - Error code #LS000003</h1><p><strong>I am sorry the server was unable to connect to the database server.</strong></p><p>For support please contact your libertas Account Manager at <a href=\"http://www.libertas-solutions.com\">http://www.libertas-solutions.com</a></p>
					</td></tr></table></center></body></html>";
	}else if ($GLOBALS["engine"]->status==-4){
		$msg = "<html>
				<style>
					h1{font-size:16px;}
					body{font-size:12px;}
					
				</style><body>
					<center><table width='500' style='border=1px solid #000000;filter:progid:dximagetransform.microsoft.gradient(gradienttype=0,startcolorstr=#cccccc,endcolorstr=#FFFFFF);'><tr><td>
					<h1><img src='/libertas_images/themes/title_bullet.gif'/>Server Stopped - Error code #LS000004</h1><p><strong>I am sorry although the server was able to connect to the database server.  It was unable to find the specified Database</strong></p><p>For support please contact your libertas Account Manager at <a href=\"http://www.libertas-solutions.com\">http://www.libertas-solutions.com</a></p>
					</td></tr></table></center></body></html>";
	}else{
		$msg = "<html>
				<style>
					h1{font-size:16px;}
					body{font-size:12px;}
					
				</style><body>
					<center><table width='500' style='border=1px solid #000000;filter:progid:dximagetransform.microsoft.gradient(gradienttype=0,startcolorstr=#cccccc,endcolorstr=#FFFFFF);'><tr><td>
					<h1><img src='/libertas_images/themes/title_bullet.gif'/>Server Stopped - Error code #LS000005</h1><p><strong>I am sorry the server was unable to find the domain in the licence file.</strong></p><p>For support please contact your System Administrator and inform them of the domain you used.</p>
					<p>You used the domain :: <strong>$domain</strong></p></td></tr></table></center></body></html>";
	}
	print $msg;
}
$now = date("Y/m/d H:i:s");
session_write_close();
$GLOBALS["engine"]->call_command("ENGINE_CLOSE");
?>