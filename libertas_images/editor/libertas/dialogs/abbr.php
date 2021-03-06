<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN"><?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:13 $
	-	$Revision: 1.2 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';

$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$domain 	= $_SERVER["HTTP_HOST"];
$base_href 	= "http://$domain".check_parameters($_GET,"base_href","/");

function check_parameters($arr,$ind,$def=""){
	if (isset($arr[$ind])){
		return $arr[$ind];
	} else {
		return $def;
	}
}

$product = check_parameters($_GET,"product","");
/*
	less than 
*/
?>
<html>
<head>
<title>Insert Image</title>
<meta http-equiv="Pragma" content="no-cache">
<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
</head>
<body onLoad="Init()">
	<form name="image_browser" method="post" action="">		
		<input type="hidden" name="theme" value="default">		
		<input type="hidden" name="lang" value="en">		
		<input type="hidden" name="images" value="">
		<div style="border: 1 solid Black; padding: 5 5 5 5;">
		<p id="tableProps" class="tablePropsTitle"><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_abbr.gif'/>Abbreviation Manager</p>
			<table width='450px' border="0">
				<tr>
					<td><strong>Please enter the description for this item</strong></td>
				</tr>
				<tr>
					<td><input type='text' name='attribute_value' value='' size=255 style='width:400px'/></td>
				</tr>
				<tr>
					<td><strong>Please enter the displayed Text for this item</strong></td>
				</tr>
				<tr>
					<td><input type='text' name='attribute_txt' value='' size=255 style='width:400px'/></td>
				</tr>
				<tr>
					<td>				
						<input ONCLICK="selectClick()" TYPE=button class="bt" ID=idSave  VALUE="<?php echo LOCALE_SELECT; ?>">				
						<input class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="<?php echo LOCALE_CANCEL;?>">
					</td>
				</tr>
			</table>
		</div>
	</form>
<script language="javascript" src="utils.js"></script>
<script language="javascript"><!--
    window.name = 'imglibrary';
	var myCategoryList = Array();
	var n_item = window.dialogArguments;
	document.image_browser.attribute_value.value	= n_item.title;
	document.image_browser.attribute_txt.value		= n_item.txt;

	var base_href ='<?php print $base_href; ?>';
	var session_url ='<?php print $session_url; ?>';
	Init();
    function selectClick(){
    	if (document.image_browser.attribute_value.value!=''){
		  	r_val 				= new Array();
			r_val.title			= document.image_browser.attribute_value.value;
			r_val.txt			= document.image_browser.attribute_txt.value;
	        window.returnValue	= r_val;
    	    window.close();
		} else {
      		alert('Please enter some abbreviation');
      	}
    }
    
    function Init(){
         resizeDialogToContent();
    }
	
//-->
</script>
</body>
</html>
