<?php
$mode="install";
if (file_exists("../include.php")){
	require_once "../include.php";
	require_once $module_directory."/included_installer.php";
} else {
$domain = $_SERVER["HTTP_HOST"];	
$script = $_SERVER["PHP_SELF"];	
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
<h1>Sorry you have not defined proper paths yet</h1>
<a href='admin/install/index.php'>back to the installer</a><br>

</form></td></tr>
</table>
</body>
</html>
<?
}
?>