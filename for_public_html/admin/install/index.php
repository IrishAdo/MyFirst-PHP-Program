<?php
$mode ="install";
session_start();
define("LS__DIR_PERMISSION", 0775);
define("LS__FILE_PERMISSION", 0664);
$domain = $_SERVER["HTTP_HOST"];	
$script = $_SERVER["PHP_SELF"];	
define ("DATE_EXPIRES",md5("EXPIRES"));
define ("IP_ADDRESS",md5("IP"));
define ("LICENCE_TYPE",md5("SETUP"));
define ("UNLIMITED",md5("-1"));
define ("ECMS",md5("ENTERPRISE"));
define ("MECM",md5("LITE"));
define ("SITE_WIZARD",md5("ULTRA"));
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
		$real_script = substr($_SERVER["PHP_SELF"],strlen($base));
	}
}
?>
<!DOCTYPE HTML public "-//W3C//DTD HTML 4.01 Transitional//EN"><html lang="EN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<base href="http://<?php 
print $domain.$base;?>">
<title>Libertas-Solutions :: Administration
</title><link href="/~alliance/libertas_images/themes/site_administration/favicon.ico" rel="shortcut icon"><link rel="stylesheet" type="text/css" href="/libertas_images/themes/site_administration/style.css">
</head>
<body>
<table border="0" width="100%" cellspacing="0" cellpadding="0" summary="This table contains the company logo, search box and login,join now and logout links" class="headerbar"><tr>
<td valign="middle"><img width="217" height="57" alt="Libertas Site Wizard" src="/libertas_images/themes/site_administration/libertas.gif"></td>
<td align="right"></td>
</tr></table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="This table contains the first level menu for the site."><tr>
<td class="MenuNavigationCell" width="100%"><div align='right'>Libertas Solutions Installer v2.0 </div></td>
</tr></table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%" class="contentTable">
<tr><td valign='top'><form name='installer' method='post' action=''>
<?php 
/*
Libertas Solutions Installer v2

*/
function chk($array = Array(), $index = 0 ,$default=""){
	if (isset($array[$index])){
		return $array[$index];
	} else {
		return $default;
	}
}

	if (chk($_POST,"install_uname")!="" && chk($_POST,"install_pwd")!=""){
		if (md5(chk($_POST,"install_uname"))==md5("l1b_adm1n_1nstall3r") && md5(chk($_POST,"install_pwd"))==md5("l1b_tmp_admin_pwd")){
			$_SESSION["INSTALLER_LOGIN"]=1;
		}
	} 
	
	if (chk($_SESSION,"INSTALLER_LOGIN",0)==0){
		print "<center>
				<table>
					<tr><td colspan='2' class='tableheader'>Login </td></tr>
					<tr><td class='tablecell'>Username</td><td><input style='width:150px;' type='text' name='install_uname'/></td></tr>
					<tr><td class='tablecell'>Password</td><td><input style='width:150px;' type='password' name='install_pwd'/></td></tr>
					<tr><td colspan='2' align='right' class='tablecell'><input type='submit' value='login' class='bt'/></td></tr>
				</table>
			   </center>";
	}

if (chk($_SESSION,"INSTALLER_LOGIN",0)==1){
	$menu = Array(
		Array("Home","admin/install/index.php", null),
		Array("Configuration", "", 
			Array(
				Array("Define Paths","admin/install/index.php?command=SETUPNEWCLIENTSTEP1"),
				Array("Database Connection","admin/install/index.php?command=SETUPNEWCLIENTSTEP2"),
				Array("Licence Defintion","admin/install/index.php?command=SETUPNEWCLIENTSTEP3")
			)
		),
		Array("Database Defintion",	"",Array(Array("Check DB Structure","admin/install/install.php?command=INSTALLER_CREATE_DB_STEP_ONE"))),
		Array("Theme Definition",	"",
			Array(
				Array("Import Themes","admin/install/install.php?command=INSTALLER_MANAGE_THEMES"),
				Array("Remove Themes","admin/install/install.php?command=INSTALLER_THEME_ENTRY_REMOVE")
			)
		),
		
		Array("Clients",			"",Array(Array("Client Definition", "admin/install/install.php?command=INSTALLER_MANAGE_CLIENT_STEP_ONE")))
	);

	print "<table height='100%'><tr><td valign='top' width='200px' style='background-color:#e7eef5;border-right:1px solid black'><ul>";
	for ($i=0,$m=count($menu);$i<$m;$i++){
		if (is_array($menu[$i][2])){
			print "</ul>\n";
			print "<strong>".$menu[$i][0]."</strong>\n";
			print "<ul>\n";
			for ($x=0,$xm=count($menu[$i][2]);$x<$xm;$x++){
				print "<li><a href='".$menu[$i][2][$x][1]."'>".$menu[$i][2][$x][0]."</a></li>\n";
			}
			print "</ul>\n<ul>\n";
		} else {
			print "<li><a href='".$menu[$i][1]."'>".$menu[$i][0]."</a></li>\n";
		}
	}
	print"</ul></td><td valign='top'>";
	// display content 
	if (chk($_GET,"command")==""){
		print "<h1>Welcome to the Libertas Installer version 2</h1>";
	} else {
		$error=0;
		$error_list=Array();
		if (chk($_POST,"command")=="SETUPNEWCLIENTSTEP1SAVE"){
			if (check_file(chk($_POST,'dir_modules'),'included_page.php')){
			}else {
				$error=1;
				$error_list[count($error_list)]="dir_modules";
			}
			if (check_dir(chk($_POST,'dir_config'))){
				
			}else {
				@mkdir(chk($_POST,'dir_config'));
				$um = umask(0);
        		@chmod(chk($_POST,'dir_config'), LS__DIR_PERMISSION);
        		umask($um);
				if (check_dir(chk($_POST,'dir_config'))){
				}else {
					$error=1;
					$error_list[count($error_list)]="dir_config";
				}
			}
			if (check_dir(chk($_POST,'dir_web_root'))){
			}else {
				$error=1;
				$error_list[count($error_list)]="dir_web_root";
			}
			if (check_dir(chk($_POST,'dir_web_admin'))){
			}else {
				$error=1;
				$error_list[count($error_list)]="dir_web_admin";
			}
/*
			if (check_file(chk($_POST,'dir_tidy_cfg','body.cfg'))){
			}else {
				$error=1;
				$error_list[count($error_list)]="dir_tidy_cfg";
			}

			if (check_dir(chk($_POST,'dir_tidy_tmp'))){
			}else {
				$error=1;
				$error_list[count($error_list)]="dir_tidy_tmp";
			}

			if (check_dir(chk($_POST,'dir_tidy'))){
			}else {
				$error=1;
				$error_list[count($error_list)]="dir_tidy";
			}
*/
			if (check_dir(chk($_POST,'dir_data'))){
			}else {
				@mkdir(chk($_POST,'dir_data'));
				$um = umask(0);
        		@chmod(chk($_POST,'dir_data'), LS__DIR_PERMISSION);
        		umask($um);
				if (check_dir(chk($_POST,'dir_data'))){
				}else {
					$error=1;
					$error_list[count($error_list)]="dir_data";
				}
			}
			if (check_dir(chk($_POST,'dir_locale','en'))){
			}else {
				$error=1;
				$error_list[count($error_list)]="dir_locale";
			}
			if (check_dir(chk($_POST,'dir_xsl'))){
			}else {
				$error=1;
				$error_list[count($error_list)]="dir_xsl";
			}
			if (check_dir(chk($_POST,'dir_trans'))){
			}else {
				$error=1;
				$error_list[count($error_list)]="dir_trans";
			}
			if (check_dir(chk($_POST,'dir_trans_tmp'))){
			}else {
				$error=1;
				$error_list[count($error_list)]="dir_trans_tmp";
			}
			if (check_dir(chk($_POST,'dir_upload'))){
			}else {
				@mkdir(chk($_POST,'dir_upload'));
				$um = umask(0);
        		@chmod(chk($_POST,'dir_upload'), LS__DIR_PERMISSION);
        		umask($um);
				if (check_dir(chk($_POST,'dir_upload'))){
				}else {
					$error=1;
					$error_list[count($error_list)]="dir_upload";
				}
			}
			if ($error==0){
				$str="<"."?"."PHP
/"."*
#PATHS
ROOT," . chk($_POST,'dir_web_root','') . "
ADMIN," . chk($_POST,'dir_web_admin','') . "
DATA_FILES_DIR," . chk($_POST,'dir_data','') . "
LOCALE_FILES_DIR," . chk($_POST,'dir_locale','') . "
XSL_THEMES_DIR," . chk($_POST,'dir_xsl','') . "
SYSTEM_CONFIG_DIR," . chk($_POST,'dir_config','') . "
MODULE_DIR," . chk($_POST,'dir_modules','') . "
TMP," . chk($_POST,'dir_trans_tmp','') . "
TMP_UPLOAD_DIR," . chk($_POST,'dir_upload','') . "
#END
*"."/
?".">";
				$config_file = chk($_POST,'dir_config')."/paths.php";
				$fp 		 = fopen($config_file, "w");
				fwrite($fp, $str);
				fclose($fp);
				$um = umask(0);
        		@chmod($config_file, LS__FILE_PERMISSION);
        		umask($um);
				$setting_file = chk($_POST,'dir_config')."/settings.php";
				$fp 		 = fopen($setting_file, "w");
				$str ="<"."?PHP
define (\"CONFIGURATION_DIRECTORY\",dirname(__FILE__));
?".">";
				fwrite($fp, $str);
				fclose($fp);
				$um = umask(0);
        		@chmod($setting_file, LS__FILE_PERMISSION);
        		umask($um);
				$fp 		 = fopen("../include.php", "w");
				fwrite($fp, "<"."?php
	\$module_directory = \"".chk($_POST,'dir_modules')."\";
	\$config_directory = \"".chk($_POST,'dir_config')."\";
?".">");
				fclose($fp);
				$um = umask(0);
        		@chmod("../include.php", LS__FILE_PERMISSION);
        		umask($um);
				print "<h1>Paths Updated</h1>";
				print "<p>You may now continue to <a href='admin/install/index.php?command=SETUPNEWCLIENTSTEP2'>Step 2 &#187;</a></p>";
			}
		}
		
		if ((chk($_GET,"command")=="SETUPNEWCLIENTSTEP1" && (chk($_POST,"command")!="SETUPNEWCLIENTSTEP1SAVE") ) || (chk($_POST,"command")=="SETUPNEWCLIENTSTEP1SAVE" && $error==1)){
			$include_defined = file_exists("../include.php");
			$paths["MODULE_DIR"] = "/home/system/cms/modules";
			$paths["SYSTEM_CONFIG_DIR"] = "/home/system/cms/config/########";
			if ($include_defined){
				$um = umask(0);
        		@chmod("../include.php", LS__FILE_PERMISSION);
        		umask($um);
				require_once ("../include.php");
				$paths["MODULE_DIR"] = $module_directory;
				$paths["SYSTEM_CONFIG_DIR"] = $config_directory;
				if ($config_directory != ""){
					if (file_exists($config_directory."/paths.php")){
						$rows =file($config_directory."/paths.php");
						for ($i=0,$m=count($rows);$i<$m;$i++){
							if (strpos($rows[$i],',')===false){
							} else {
								$columns = split(",", $rows[$i]);
								$paths[$columns[0]] = $columns[1];
							}
						}
					}
				}
				print "<h1>Step 1 Define Paths - using existing settings</h1>";
			} else {
				print "<h1>Step 1 Define Paths</h1>";
			}
			print "<input type='hidden' name='command' value='SETUPNEWCLIENTSTEP1SAVE'>";
			print "<table>";
			print "<tr><td colspan='2' class='tableheader'>Define directories</td></tr>";
			print "<tr><td class='tablecell'>Web Root Directory</td><td><input type='text' name='dir_web_root' style='width:400px;' value='".chk($_POST,"dir_web_root",chk($paths,"ROOT",'/home/########/public_html'))."'/></td></tr>";
			if (in_array("dir_web_root",$error_list)){
				print "<tr><td colspan='2'>Sorry directory not found</td></tr>";
			}
			print "<tr><td class='tablecell'>Web Admin Directory</td><td><input type='text' name='dir_web_admin' style='width:400px;' value='".chk($_POST,"dir_web_admin",chk($paths,"ADMIN",'/home/########/public_html/admin'))."'/></td></tr>";
			if (in_array("dir_web_admin",$error_list)){
				print "<tr><td colspan='2'>Sorry directory not found</td></tr>";
			}
			print "<tr><td class='tablecell'>Configuration Directory</td><td><input type='text' name='dir_config' value='".chk($_POST,"dir_config",chk($paths,"SYSTEM_CONFIG_DIR",'/home/system/cms/config/########'))."' style='width:400px;'/></td></tr>";
			if (in_array("dir_config",$error_list)){
				print "<tr><td colspan='2'>Sorry directory not found</td></tr>";
			}
			print "<tr><td class='tablecell'>Data Storage Directory</td><td><input type='text' name='dir_data' style='width:400px;' value='".chk($_POST,"dir_data",chk($paths,"DATA_FILES_DIR",'/home/########/data'))."'/></td></tr>";
			if (in_array("dir_data",$error_list)){
				print "<tr><td colspan='2'>Sorry directory not found</td></tr>";
			}
			print "<tr><td class='tablecell'>Temp Upload Directory</td><td><input type='text' name='dir_upload' style='width:400px;' value='".chk($_POST,"dir_upload",chk($paths,"TMP_UPLOAD_DIR",'/home/########/uploads'))."'/></td></tr>";
			if (in_array("dir_upload",$error_list)){
				print "<tr><td colspan='2'>Sorry directory not found</td></tr>";
			}
			print "<tr><td colspan='2'><hr/></td></tr>";
			print "<tr><td class='tablecell'>Modules Directory</td><td><input type='text' name='dir_modules' value='".chk($_POST,"dir_modules",chk($paths,"MODULE_DIR",'/home/system/cms/modules'))."' style='width:400px;'/></td></tr>";
			if (in_array("dir_modules",$error_list)){
				print "<tr><td colspan='2'>Sorry directory not found</td></tr>";
			}
			print "<tr><td class='tablecell'>Locale Directory</td><td><input type='text' name='dir_locale' style='width:400px;' value='".chk($_POST,"dir_locale",chk($paths,"LOCALE_FILES_DIR",'/home/system/cms/locale'))."'/></td></tr>";
			if (in_array("dir_locale",$error_list)){
				print "<tr><td colspan='2'>Sorry directory not found</td></tr>";
			}
			print "<tr><td class='tablecell'>XSLT Directory</td><td><input type='text' name='dir_xsl' style='width:400px;' value='".chk($_POST,"dir_xsl",chk($paths,"XSL_THEMES_DIR",'/home/system/cms'))."'/></td></tr>";
			if (in_array("dir_xsl",$error_list)){
				print "<tr><td colspan='2'>Sorry directory not found</td></tr>";
			}
			print "<tr><td class='tablecell'>Temp Directory</td><td><input type='text' name='dir_trans_tmp' style='width:400px;' value='".chk($_POST,"dir_trans_tmp",chk($paths,"TRANSFORM_TMP",'/home/system/cms/transform'))."'/></td></tr>";
			if (in_array("dir_trans_top",$error_list)){
				print "<tr><td colspan='2'>Sorry directory not found</td></tr>";
			}
			print "<tr><td colspan='2' align='right' class='tablecell'><input type='submit' class='bt' value='next &#187;'/></td></tr>";
			print "</table>";
		} 
		if (chk($_POST,"command")=="SETUPNEWCLIENTSTEP2SAVE"){
			$db_type 		= chk($_POST,"db_type");
			$db_host 		= chk($_POST,"db_host");
			$db_database	= chk($_POST,"db_database");
			$db_username	= chk($_POST,"db_username");
			$db_password	= chk($_POST,"db_password");
			if ($db_type==""){
				$error=1;
			}
			if ($db_type =="mySQL"){
				$connection = @mysql_pconnect("$db_host", "$db_username", "$db_password");
				if($connection){
					$db = @mysql_select_db("$db_database",$connection);
					if ($db){
					}else {
						$error=1;
						$error_list[count($error_list)] = "db_database";
					}
				} else {
					$error=1;
					$error_list[count($error_list)] = "db_connection";
				}
			}
			if ($db_type =="msSQL"){
				$connection = @mssql_pconnect("$db_host", "$db_username", "$db_password");
				if($connection){
					$db = @mssql_select_db("$db_database",$connection);
					if ($db){
					}else {
						$error=1;
						$error_list[count($error_list)] = "db_database";
					}
				} else {
					$error=1;
					$error_list[count($error_list)] = "db_connection";
				}
			}
			
			if ($error==0){
				require_once ("../include.php");
				$paths["MODULE_DIR"] 		= $module_directory;
				$paths["SYSTEM_CONFIG_DIR"] = $config_directory;
				$filename = $paths["SYSTEM_CONFIG_DIR"]."/db_settings.php";
				$fp 		 = fopen($filename, "w");
				fwrite($fp, "<"."?php
/"."*
db_host,$db_host
db_database,$db_database
db_username,$db_username
db_password,$db_password
*"."/
?".">");
				fclose($fp);
				
			$_SESSION["db"] = Array("type"=>$db_type,"host"=>$db_host, "username"=>$db_username, "password"=>$db_password, "database"=>$db_database);
				print "<h1>Database Config Updated</h1>";
				print "<p>You may now continue to <a href='admin/install/index.php?command=SETUPNEWCLIENTSTEP3'>Step 3 &#187;</a></p>";
			}
		}
		if ((chk($_GET,"command")=="SETUPNEWCLIENTSTEP2" && chk($_POST,"command")!="SETUPNEWCLIENTSTEP2SAVE") || (chk($_POST,"command")=="SETUPNEWCLIENTSTEP2SAVE" && $error==1)){
			global $modules;
			if (file_exists("../include.php")){
			require_once ("../include.php");
			$db_type 		= "";
			$db_host 		= "";
			$db_database	= "";
			$db_username	= "";
			$db_password	= "";
			$paths["MODULE_DIR"] 		= $module_directory;
			$paths["SYSTEM_CONFIG_DIR"] = $config_directory;
			$filename = $paths["SYSTEM_CONFIG_DIR"]."/db_settings.php";
			if (file_exists($filename)){
				$rows = file($filename);
				for($i=0 , $m = count($rows); $i<$m ; $i++){
					if (strpos($rows[$i],',')===false){
					} else {
						$columns = split(",", $rows[$i]);
						if ($columns[0]=="db_host"){
						 	$db_host = $columns[1];
						}
						if ($columns[0]=="db_database"){
						 	$db_database = $columns[1];
						}
						if ($columns[0]=="db_username"){
						 	$db_username = $columns[1];
						}
						if ($columns[0]=="db_password"){
						 	$db_password = $columns[1];
						}
					}
				}
			}
			$db_type 		= chk($_POST,"db_type",$db_type);
			$db_host 		= chk($_POST,"db_host",$db_host);
			$db_database	= chk($_POST,"db_database",$db_database);
			$db_username	= chk($_POST,"db_username",$db_username);
			$db_password	= chk($_POST,"db_password",$db_password);
			$server_types = Array(Array("mySQL","My SQL"),Array("msSQL","Microsoft SQL Server"));
			load_config();
			if ($db_type==""){
				if (is_array(chk($modules,"DB_"))){
					$mod = chk($modules,"DB_");
					$db_type = substr(chk($mod,"name"),9);
				}
			}
			print "<h1>Step 2 - Database Definition</h1>";
			print "<input type='hidden' name='command' value='SETUPNEWCLIENTSTEP2SAVE'/>";
			print "<table>";
			print "<tr><td>Database Server Type</td><td><select name='db_type'>";
			for ($i=0,$m=count($server_types);$i<$m;$i++){
				print "<option value='" . $server_types[$i][0] . "'";
				if (strtolower($db_type)==strtolower($server_types[$i][0])){
					print " selected";
				}
				print ">" . $server_types[$i][1] . "</option>";
			}
			print "</select></td></tr>";
			if ($db_type=="" && $error==1){
				print "<tr><td colspan='2'>You must select a Database Type</td></tr>";
			}
			print "<tr><td>Database Server</td><td><input type='text' name='db_host' value='".$db_host."'></td></tr>";
			if (in_array("db_connection",$error_list)){
				print "<tr><td colspan='2'>Unable to connect to this database server please check the database server  and the username na dpassword you used</td></tr>";
			}
			print "<tr><td>Database Name</td><td><input type='text' name='db_database' value='".$db_database."'></td></tr>";
			if (in_array("db_database",$error_list)){
				print "<tr><td colspan='2'>Cound not find this database</td></tr>";
			}
			print "<tr><td>Database Username</td><td><input type='text' name='db_username' value='".$db_username."'></td></tr>";
			print "<tr><td>Database Password</td><td><input type='text' name='db_password' value='".$db_password."'></td></tr>";
			print "<tr><td align='right' colspan='2'><input type='submit' class='bt' value='Next &#187;'/></td></tr>";
			print "</table>";
			} else {
				print "<h1>Sorry you have not defined proper paths yet</h1>";
			}
		}
		if (chk($_POST,"command")=="SETUPNEWCLIENTSTEP3SAVE"){
			$licence	= strtolower(chk($_POST,"licence"));
			$ip_address	= strtolower(chk($_POST,"ip_address"));
			$error_list=Array();
			if ($licence=="" || strlen($licence)!=39){
				$error=1;
				$error_list[count($error_list)] = "licence";
			} else {
				$licence_list = join(split("-",$licence),"");
			}
			if ($ip_address=="" ||  $ip_address=="localhost"){
				$error=1;
				$error_list[count($error_list)] = "ip_address";
			}
			if ($error==0){
				require_once ("../include.php");
				$paths["MODULE_DIR"] 		= $module_directory;
				$paths["SYSTEM_CONFIG_DIR"] = $config_directory;
				$filename = $paths["SYSTEM_CONFIG_DIR"]."/licence_data.php";
				$fp 		 = fopen($filename, "w");
				if($_SERVER["SERVER_NAME"]=="caplo" || $_SERVER["SERVER_NAME"]=="professor" || $_SERVER["SERVER_NAME"]=="newdawn"){
			$str ="<"."?PHP
define(\"LICENCE\",\"#SERVER
a12a3079e14ced46e69ba52b8a90b21a::\".md5(\$_SERVER[\"SERVER_ADDR\"]).\"
ddbb81a9e3aab6cfa19ad8eb2389efd4::".$licence_list."
135d1eb39a6170de3a1c7613ce615832::6bb61e3b7bce0931da574d19d1d82c88
#MODULES\n";
				} else {
			$str ="<"."?PHP
define(\"LICENCE\",\"#SERVER
a12a3079e14ced46e69ba52b8a90b21a::".md5($ip)."
ddbb81a9e3aab6cfa19ad8eb2389efd4::".$licence_list."
135d1eb39a6170de3a1c7613ce615832::6bb61e3b7bce0931da574d19d1d82c88
#MODULES\n";
}
if (chk($_SESSION["db"],"type")=="mySQL"){
	$str .="libertas.database_mysql.php,database_mysql,DB_,0,2\n";
}
if (chk($_SESSION["db"],"type")=="msSQL"){
	$str .="libertas.database_mssql.php,database_mssql,DB_,0,2\n";
}
$str.="#END\");
?".">";
				fwrite($fp, $str);
				fclose($fp);
				print "<h1>Licence Config Updated</h1>";
				print "<p>You may now continue to <a href='admin/install/install.php?command=INSTALLER_CREATE_DB_STEP_ONE'>Step 4 &#187;</a></p>";
			}
		
		}
		if ((chk($_GET,"command")=="SETUPNEWCLIENTSTEP3" && chk($_POST,"command")!="SETUPNEWCLIENTSTEP3SAVE") || (chk($_POST,"command")=="SETUPNEWCLIENTSTEP3SAVE" && $error==1)){
			$ok = load_config();
			if($ok==0){
				$licence_type = chk($server_data,LICENCE_TYPE);
				if ($licence_type!=""){
					$licence_type = substr($licence_type,0,4)."-".substr($licence_type,4,4)."-".substr($licence_type,8,4)."-".substr($licence_type,12,4)."-".substr($licence_type,16,4)."-".substr($licence_type,20,4)."-".substr($licence_type,24,4)."-".substr($licence_type,28,4);
				}
				print "<h1>Step 3 - Licence defintion</h1><input type='hidden' name='command' value='SETUPNEWCLIENTSTEP3SAVE'/>";
				print "<table>";
				print "<tr><td>Licence Serial No.</td><td><input type='text' size='40' maxlength=\"40\" name='licence' value='".strtolower(chk($_POST,"licence",$licence_type))."'></td></tr>";
				if (in_array("licence",$error_list)){
					print "<tr><td colspan=2>There seems to be a problem with the serial number you specified</td></tr>";
				}
				print "<tr><td>Ip Address of Web Server<br><em>do not use 127.0.0.1 or localhost<br>use only ip numbers.</em></td><td valign='top'><input type='text' name='ip_address' value='".$_SERVER["SERVER_ADDR"]."'></td></tr>";
				if (in_array("ip_address",$error_list)){
					print "<tr><td colspan=2>Either you have not specified an IP address or you have specified 127.0.0.1 or localhost</td></tr>";
				}
				print "<tr><td align='right' colspan='2'><input type='submit' class='bt' value='Next &#187;'/></td></tr>";
				print "</table>";
			} else {
				print "<h1>Sorry you have not defined proper paths yet</h1>";
			}
		}
		if (substr(chk($_GET,"command"),0,10)=="INSTALLER_"){
			if(file_exists("../include.php")){
				require_once ("../include.php");
				$paths["MODULE_DIR"] 		= $module_directory;
				$paths["SYSTEM_CONFIG_DIR"] = $config_directory;
				require_once ($paths["MODULE_DIR"]."/included_installer.php");
				print $command;
//		$ok = load_config();
//print "<h1>Step 4 - Database Structure</h1><input type='hidden' name='command' value='setupnewclientstep4save'/>";
/*print "<table>";
			print "<tr><td>Licence Serial No.</td><td><input type='text' size='40' maxlength=\"40\" name='licence' value='".strtolower(chk($_POST,"licence"))."'></td></tr>";
			if (in_array("licence",$error_list)){
				print "<tr><td colspan=2>There seems to be a problem with the serial number you specified</td></tr>";
			}
			print "<tr><td>Ip Address of Web Server<br><em>do not use 127.0.0.1 or localhost<br>use only ip numbers.</em></td><td valign='top'><input type='text' name='ip_address' value='".$_SERVER["SERVER_ADDR"]."'></td></tr>";
			if (in_array("ip_address",$error_list)){
				print "<tr><td colspan=2>Either you have not specified an IP address or you have specified 127.0.0.1 or localhost</td></tr>";
			}
			print "<tr><td align='right' colspan='2'><input type='submit' class='bt' value='Next &#187;'/></td></tr>";

print "</table>";
*/
			} else {
				print "<h1>Sorry you have not defined proper paths yet</h1>";
			}
		}
		
	}
	print "</td></tr></table>";
}

function check_file($p,$f="."){
	return file_exists($p."/".$f);
}
function check_dir($p,$f="."){
	return file_exists($p."/".$f);
}

	function load_config(){
		global $modules;
		global $server_data;
		global $paths;
		$error = 0;
		if (chk($paths,"MODULE_DIR")==""){
			if (file_exists("../include.php")){
				require_once (dirname(__FILE__)."/../include.php");
				$paths["MODULE_DIR"] 		= $module_directory;
				$paths["SYSTEM_CONFIG_DIR"] = $config_directory;
			} else {
				$error = 1;
			}
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- Initialise the engine load the configuration file and configure the system.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if($error==0){
			if (file_exists($paths["SYSTEM_CONFIG_DIR"]."/licence_data.php")){
				include($paths["SYSTEM_CONFIG_DIR"]."/licence_data.php");
				$file = split("\n",LICENCE);
				$cmd="";
				for ($index=0,$length=count($file);$index<$length;$index++){
					if (strpos($file[$index],"#")===0){
						$cmd=trim($file[$index]);
						$index++;
					}
					if ($cmd=="#MODULES"){
						$pram=split(",",trim($file[$index]));
						add_module($pram);
					}else if ($cmd=="#SERVER"){
						$param = split('::',trim($file[$index]));
						$tag= $param[0]."";
						$val= $param[1]."";
						$server_data[$tag]=$val;
					}else {
					}
				}
				if ($server_data[LICENCE_TYPE]==ECMS){
					$extra_mods = $paths["MODULE_DIR"]."/licences/ecms.ls";
				} else if ($server_data[LICENCE_TYPE]==MECM){
					$extra_mods = $paths["MODULE_DIR"]."/licences/wcm.ls";
				} else {
					$extra_mods = $paths["MODULE_DIR"]."/licences/sw.ls";
				}
				if (file_exists($extra_mods)){
					$list = file($extra_mods);
					for ($i=0,$m=count($list);$i<$m;$i++){
						$pram=split(",",trim($list[$i]));
						add_module($pram);
					}
				}
				/*
	define ("ECMS",md5("ENTERPRISE"));
	define ("MECM",md5("LITE"));
	define ("SITE_WIZARD",md5("ULTRA"));
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
					$this->call_command("SESSION_RETRIEVE",array(session_id()));
					return 1;
				} else {
					$this->call_command("ENGINE_ERROR",array(__FILE__,__LINE__,"The Engine could not load the configuration file [paths.php]"));
					return 0;
				}*/
			} else {
				return 0;
			}
		} else {
			return 1;
		}
	}
	function add_module($module_array){
		global $modules;
		$modules[$module_array[2]] = array(
		"file" 		=> $module_array[0],
		"name" 		=> $module_array[1],
		"tag" 		=> $module_array[2],
		"table"		=> $module_array[3],
		"created"	=> 0,
		"admin"		=> $module_array[4],
		"module"	=> null,
		"loaded"	=> 0
		);
		return 1;
	}

?>
</form></td></tr>
</table>
</body>
</html>