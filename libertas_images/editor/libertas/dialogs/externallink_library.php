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
$info = check_parameters($_GET,"url","");
if (substr(strtolower($info),0,7)!='http://'){
	$szURL = 'http://'.$info;
} else {
	$szURL = $info;
}
$szTitle = check_parameters($_GET,"title","");
$szExternal = check_parameters($_GET,"external","");
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
  <title>Insert External Links</title>
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
<P id=tableProps CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_externallink.gif'/> External Website Address Manager</P>
<table width='450px' border=0>
<tr><td align='right'>Web Address :: </td><td><input type=text ID='externalurlValue' name='externalurlValue' SIZE=35 VALUE="<?php print $szURL; ?>"></td></tr>
<tr><td align='right'>Alternative Title :: </td><td><input type=text	ID='externalurlTitle' name='externalurlTitle' SIZE=35 VALUE="<?php print $szTitle; ?>"></td></tr>
<tr><td align='right'>Open in new window :: </td><td><input type='checkbox' ID='externalWindow' name='externalWindow' VALUE="Yes"> <label for='externalWindow'>Yes,</label><br><small class='warning'>(warning some people use programs<br> to stop new windows being opened by <br>the browser)</small></td></tr>
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
	var externalWindow ='<?php print $szExternal; ?>';
	if (externalWindow=='Yes'){
		externalWindow.checked=true;
	}
	var loaded=0;
	var session_url ='<?php print $session_url; ?>';

	function resultData(szURL,szTitle,szType,szExternal){
		this.szURL 		= szURL;
		this.szTitle 	= szTitle;
		this.szType		= szType;
		this.szExternal	= szExternal;
	}  

    function selectClick(){
		if (document.all.externalurlValue.value.length!=0){
			var szURL	= document.all.externalurlValue.value.split("'").join("&#39;");
			while (szURL.substring(-1)==" "){
				szURL = szURL.substring(0,szURL.length-1)
			}
			var szTitle	= document.all.externalurlTitle.value.split("'").join("&#39;");
			szType = "http://";
			if (document.link_browser.externalWindow.checked){
				var szExternal = 'Yes';
			}
			var result = new resultData(szURL,szTitle,szType,szExternal);
    	 	window.returnValue = result;
	   		window.close();
      	} else {
        	alert('Please enter an external URL');
		}
    }
	function Init(){
         resizeDialogToContent();
    }

	// -->
	</script>
  <script language="javascript" src="utils.js"></script></body>
  <script language="javascript">
  <!--
	if (externalWindow=='Yes'){
		document.link_browser.externalWindow.checked=true;
	}
	// -->
	</script>
</html>
