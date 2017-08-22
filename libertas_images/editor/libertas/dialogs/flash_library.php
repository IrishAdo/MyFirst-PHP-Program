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
<P id=tableProps CLASS=tablePropsTitle>Embedding Flash Files Manager</P>

<table width='450px' border=0>
	<tr><td><strong>Select flash file</strong></td><td><select id='imagesrc' name='imagesrc'  style='width:230px'><option>Loading flash list...</option></select></td></tr>
	<tr><td colspan='2'><INPUT ONCLICK="selectClick()" TYPE=button class="bt" ID=idSave  VALUE="<?php echo LOCALE_SELECT; ?>"> 
<INPUT class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="<?php echo LOCALE_CANCEL;?>">

</td></tr>
</table>
</div>

</form>
  <script language="javascript">
  <!--
    window.name = 'imglibrary';
  //-->
  </script>	<Script>
	
		var base_href ='<?php print $base_href; ?>';
		var session_url ='<?php print $session_url; ?>';
		window.opener.__extract_information('flash');
		setTimeout("get_flash()",1000);
	</script>
  <script language="javascript" src="utils.js"></script>
  <script language="javascript">
  <!--
  	function get_flash(){
	
		if (window.opener.cache_data.frmDoc.flash_data.value!=''){
			if (window.opener.cache_data.frmDoc.flash_data.value!='__NOT_FOUND__'){
				tmp 			= new String(window.opener.cache_data.frmDoc.flash_data.value);
				myArray 		= tmp.split("|1234567890|")
				l				= myArray.length-1;
				list			= "";
				document.image_browser.imagesrc.options.length=0
				for (i = 0;i<l;i+=2){
					document.image_browser.imagesrc.options[document.image_browser.imagesrc.options.length] = new Option(convert_special_characters(myArray[i],false), myArray[i+1]);
				}
			} else {
				document.image_browser.imagesrc.options.length=0
				document.image_browser.imagesrc.options[0] = new Option("Sorry no results were returned");
				//alert("Sorry no results were returned");
			}
		} else {
			setTimeout("get_flash()",1000);
		}
	}
	
    function selectClick(){
	  	if (document.image_browser.imagesrc.selectedIndex>0){
			flash_info = ''+document.image_browser.imagesrc.options[document.image_browser.imagesrc.selectedIndex].value;
			flash_label = ''+document.image_browser.imagesrc.options[document.image_browser.imagesrc.selectedIndex].text;
    		window.opener.__insert_flash(flash_info,flash_label);
        	window.close();
		} else {
        	alert('Please select an flash file to insert');
      	}
    }
    
    function Init()
    {
     
    }
  //-->
  </script>
</body>
</html>
