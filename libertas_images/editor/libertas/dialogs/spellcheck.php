<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:03 $
	-	$Revision: 1.2 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$base_href = check_parameters($_GET,"base_href","/");
$info = split($base_href,check_parameters($_GET,"url",""));

if (count($info)==2){
	$szURL = $info[1];
} else {
	$szURL = $info[0];
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
  <title>Checking Spelling</title>
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css"/>
  <script>
	function save(pos){
		window.returnValue = '/libertas_images/icons/emocs/'+list[pos][0]+'.gif" alt="'+list[pos][1]+'"';
		window.close()
	}
  function Init() {
	current_document_to_check = window.dialogArguments;
	txt = current_document_to_check.innerText
//    resizeDialogToContent();
  }
	</script>
</head>

<body onLoad="Init()">
<form name="link_browser" method="post" action="">
<input type="hidden" name="theme" value="default">
<input type="hidden" name="lang" value="en">
<input type="hidden" name="images" value="">
<div style="border: 1 solid Black; padding: 5 5 5 5;">

<DIV ID='internalprop1'>
<P id=tableProps CLASS=tablePropsTitle>Checking Spelling</P>
<table>
	<tr><td align='right' style='font-size:10px'>Not found:</td><td><input type=text style='width:200px;' size=20 value='' name='misspeltword' id='misspeltword'></td><td></td></tr>
	<tr><td align='right' style='font-size:10px'>Change to:</td><td><input type=text style='width:200px;' size=20 value='' name='changeto' id='changeto'></td><td></td></tr>
	<tr><td align='right' style='font-size:10px'>Suggestions:</td><td><select size=5 style='width:200px;height:80px' name='suggestedwords' id='suggestedwords'></td><td><table>
	<tr><td><input type='button' class='bt' onClick='ignore(0)' value='Ignore' style='width:50px;'></td><td>
	<input type='button' class='bt' onClick='ignore(1)' value='Ignore All' style='width:50px;'></td></tr>
	<tr><td><input type='button' class='bt' onClick='change(0)' value='Change' style='width:50px;'></td><td>
	<input type='button' class='bt' onClick='change(1)' value='Change All' style='width:50px;'></td></tr>
	<tr><td></td><td><input type='button' class='bt' onClick='window.close()' value='Cancel' style='width:50px;'></td></tr>
	</table></td></tr>
</table>
</DIV>
</div>

</form>
</body>
</html>
