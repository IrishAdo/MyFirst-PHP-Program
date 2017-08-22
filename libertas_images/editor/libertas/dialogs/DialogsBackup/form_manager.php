<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:09 $
	-	$Revision: 1.2 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';

$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$base_href = check_parameters($_GET,"base_href","/");

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
  <title>Embed Form Manager</title>
	<meta http-equiv="Pragma" content="no-cache">
  <link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
</head>

<body onLoad="Init()">
  <script language="javascript">
  <!--
    window.name = 'imglibrary';
  //-->
  </script>

<form name="image_browser" method="post" action="">
<input type="hidden" name="theme" value="default">
<input type="hidden" name="lang" value="en">
<input type="hidden" name="images" value="">
<div style="border: 1 solid Black; padding: 5 5 5 5;">
<P id=tableProps CLASS=tablePropsTitle>Embedding Form Manager</P>
<table width='450px' border=0>
	<tr><td><strong>Select Form</strong></td><td><select id='imagesrc' name='imagesrc'  style='width:230px'><option style='color:#ff0000'>Loading list of available forms ...</option></select></td></tr>
	<tr><td colspan='2'><INPUT ONCLICK="selectClick()" TYPE=button class="bt" ID=idSave  VALUE="<?php echo LOCALE_SELECT; ?>"> 
<INPUT class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="<?php echo LOCALE_CANCEL;?>">

</td></tr>
</table>
</div>

</form>
  <script language="javascript" src="utils.js"></script>
  <script language="javascript">
	
  <!--
  	var pw = window.dialogArguments.document.parentWindow;
	var base_href ='<?php print $base_href; ?>';
	var session_url ='<?php print $session_url; ?>';
	pw.__extract_information('forms');
	setTimeout("get_forms()",1000);

  	function get_forms(){
		var findme;
		if (window.dialogArguments.document.cache_data.frmDoc.form_data.value!=''){
			if (window.dialogArguments.document.cache_data.frmDoc.form_data.value!='__NOT_FOUND__'){
				var selection = pw.range(pw.myEditor);
				if (selection.item){
					if (selection.item(0).tagName=="IMG"){
						if(selection.item(0).id=='libertas_form'){
							findme = selection.item(0).frm_identifier;
						}
					}
				}
				tmp 			= new String(window.dialogArguments.document.cache_data.frmDoc.form_data.value);
				myArray 		= tmp.split("|1234567890|")
				l				= myArray.length-2;
				list			= "";
				document.image_browser.imagesrc.options.length=0
				for (i = 0;i<l;i+=2){
					myIndex = document.image_browser.imagesrc.options.length
					if (myArray[i+1].split("::")[0] != "SFORM_DISPLAY_CONTACT_US"){
						document.image_browser.imagesrc.options[myIndex] = new Option(myArray[i],myArray[i+1]);
						// problem here first 4 characters are '----' not '- - ' why does this work only with '- - ' ???
						if (i==0){
							document.image_browser.imagesrc.options[myIndex].style.color="#ff0000";
						}
						if (myArray[i].substr(0,4)=="- - "){
							document.image_browser.imagesrc.options[myIndex].style.color="#ff9900";
						}
						if (myArray[i+1].split("::")[0]==findme){
							document.image_browser.imagesrc.options[myIndex].selected=true;
						}
					}
				}
			} else {
				document.image_browser.imagesrc.options.length=0
				document.image_browser.imagesrc.options[0] = new Option("Sorry no results were returned");
				alert("Sorry no results were returned");
			}
		} else {
			setTimeout("get_forms()",1000);
		}
	}

    function selectClick(){
		field = document.image_browser.imagesrc
	  	if (field.selectedIndex > 0 && field[field.selectedIndex].value!=''){
			forms_info = ''+document.image_browser.imagesrc.options[document.image_browser.imagesrc.selectedIndex].value;
    		window.returnValue = forms_info;
        	window.close();
		} else {
        	alert('Please select an forms file to insert');
      	}
    }
    
    function Init(){
    resizeDialogToContent();
    }
  //-->
  </script>
</body>
</html>
