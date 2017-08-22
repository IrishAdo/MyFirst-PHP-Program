<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2005/02/26 17:27:49 $
	-	$Revision: 1.3 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';

$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$base_href = check_parameters($_GET,"base_href","/");
$info = split($base_href,check_parameters($_GET,"url",""));
if (count($info)==2){
	$szURL = $info[1];
} else {
	$szURL = $info[0];
}
$info = split("\?",$szURL);
if (count($info)==2){
	$szURL 		= $info[0];
	$subject 	= split("=", $info[1]);
	$szSubject	= $subject[1];
} else {
	$szURL 		= $info[0];
	$szSubject	= "";
}
$szTitle = check_parameters($_GET,"title","");

function check_parameters($arr,$ind,$def=""){
	if (isset($arr[$ind])){
		return $arr[$ind];
	} else {
		return $def;
	}
}

?>
<html>
<head>
  <title>Insert Links</title>
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
</head>

<body onLoad=Init()>
<form name="link_browser" method="post" action="">
<input type="hidden" name="theme" value="default">
<input type="hidden" name="lang" value="en">
<input type="hidden" name="images" value="">
<div style="border: 1 solid Black; padding: 5 5 5 5;">

<DIV ID='internalprop1'>
<P id=tableProps CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_emaillink.gif'/> Insert / Manage an email link.</P>
<table width='450px' border=0>
<tr><td>Email Address :: </td><td><input type=text ID='emailurlValue' name='emailurlValue' SIZE=35 VALUE="<?php print $szURL; ?>"></td></tr>
<tr><td>Title :: </td><td><input type=text ID='emailurlTitle' name='emailurlTitle' SIZE=35 VALUE="<?php print $szTitle; ?>"></td></tr>
<tr><td>Subject :: </td><td><input type=text ID='emailurlSubject' name='emailurlSubject' SIZE=35 VALUE="<?php print $szSubject; ?>"></td></tr>
<tr><td colspan='2' align='center'><INPUT ONCLICK="selectClick()" TYPE=button class="bt" ID=idSave  VALUE="<?php echo LOCALE_INSERT; ?>"> 
<INPUT class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="<?php echo LOCALE_CANCEL;?>">

</td></tr></table>
</DIV>
</div>

</form>
  <script language="javascript">
  <!--
	var winOpener = window.dialogArguments.document.parentWindow;
	var base_href ='<?php print $base_href; ?>';
	var URLTitle ='<?php print $szTitle; ?>';
	var loaded=0;
	var session_url ='<?php print $session_url; ?>';

	
	function resultData(szURL,szTitle,szType,szExternal){
		this.szURL 		= szURL;
		this.szTitle 	= szTitle;
		this.szType		= szType;
		this.szExternal	= szExternal;
	}  

    function selectClick(){
		if (check_email()){
			var szURL		= document.all.emailurlValue.value.split("'").join("&#39;");
			var szTitle		= document.all.emailurlTitle.value.split("'").join("&#39;");
			var szSubject	= document.all.emailurlSubject.value.split("'").join("&#39;");
			szType = "mailto:";
			var szExternal = 'No';
			var result = new resultData(szURL+"?subject="+szSubject,szTitle,szType,szExternal);
    	 	window.returnValue = result;
	   		window.close();
      	} else {
			if (document.all.emailurlValue.value.indexOf(" ")!=-1){
				alert('you are not allowed spaces in an email address');
			} else {
        		alert('Please enter a valid email address');
			}
		}
    }
    function Init(){
         resizeDialogToContent();
    }
	function check_email(){
		if (document.all.emailurlValue.value.indexOf(" ")!=-1){
			return false; 
		}
		if (document.all.emailurlValue.value.length==0){
			return false; 
		}
		if (document.all.emailurlValue.value.indexOf("@")==-1){
			return false;
		} else {
			if (document.all.emailurlValue.value.indexOf(".",document.all.emailurlValue.value.indexOf("@"))==-1){
				return false;
			} else {
				return true;
			}
		}
		return false;
	}
	// -->
	</script>
  <script language="javascript" src="utils.js"></script>
</body>
</html>
