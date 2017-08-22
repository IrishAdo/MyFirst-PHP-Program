<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<title>Untitled</title>
	<link rel="stylesheet" type="text/css" href="/libertas_images/themes/site_administration/dialog.css">
</head>

<body onload="javascript:init();">
<P id=tableProps CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_form.gif'/> Web Object Insert Manager</P>
<form name="webObject">
<table>
	<tr>
		<td colspan="2">Select Web Object to insert</td>
		<td colspan="2"><select id='webElement' name='webElement'></select></td>
	</tr>
<tr>
<td colspan="4" align="right" valign="bottom" nowrap>
<input type="button" value="Ok" onClick="okClick()" class="bt">
<input type="button" value="Cancel" onClick="cancelClick()" class="bt">
</td>
</tr>
</table>
</form>
<script language="javascript" src="utils.js"></script>
<script language="javascript">
<!--  
	function init(){
		var web_objects = window.dialogArguments.__get_web_objects();
		web_objects.sort(sort_objects);
		document.webObject.webElement.options.length=0;
		for (var i =0; i < web_objects.length ; i++){
			document.webObject.webElement.options[document.webObject.webElement.options.length] = Option(web_objects[i][2], web_objects[i][0]+','+web_objects[i][1]+','+web_objects[i][2]);
		}
	}
	
	function sort_objects(a,b){
		if (a[2]==b[2]){
			return 0;
		}
		if (a[2]<b[2]){
			return -1;
		}
		if (a[2]>b[2]){
			return 1;
		}
	}
	function okClick() {
		window.returnValue = webObject.webElement.options[webObject.webElement.selectedIndex].value;
		window.close();
	}
	function cancelClick() {
		window.close();
	}
//-->
</script>

</body>
</html>
