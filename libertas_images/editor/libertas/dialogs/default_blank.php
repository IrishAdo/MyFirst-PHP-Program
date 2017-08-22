<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:12 $
	-	$Revision: 1.2 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
?>
<html>
	<head>
		<title>Work in Progress</title>
		<meta http-equiv="Pragma" content="no-cache">
		<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
	</head>
	<body onload="init()">
	<div style='border: 1 solid Black; padding: 5 5 5 5;'>
	<P id=tableProps CLASS=tablePropsTitle>Libertas Solutions</P>
	<table width='450px' border=0>
	<tr><td><table width='100%'>
	<tr><td><center><h3>Work in Progress </h3>
	<img src='/libertas_images/editor/libertas/lib/themes/default/img/working.gif'></center></td></tr>
	</table></td></tr>
	</table></div>
	</body>
	<script>
		function init(){
			args = window.dialogArguments;
//			pw = window.dialogArguments.document.parentWindow;
			alert(args.sender);
			alert(args.editor);
//			alert(pw.name)
		}
	</script>
</html>
