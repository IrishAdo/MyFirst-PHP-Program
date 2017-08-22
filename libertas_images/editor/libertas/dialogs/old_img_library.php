<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN"><?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:06 $
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
<p id="tableProps" class="tablePropsTitle"><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_image_insert.gif'/>Image Insertion Manager</p><table width='450px' border="0">
			<tr>
				<td><strong>Select Image</strong></td>
				<td>
				<select id='imagesrc' name='imagesrc' style='width:230px' onChange='parent.ShowImage(this.document);'>
					<option>Please wait while I download the list of images.</option>
				</select></td>
			</tr>
			<tr>
				<td><strong>Alt Tag information</strong></td>
				<td>				
				<input type=text id='imagealt' size=255 style='width:230px'>				
				<input type=hidden id='imageexisting' value=''></td>
			</tr>
			<tr>
				<td valign="top">
<p>Preview</p><img border="1" src='/libertas_images/themes/1x1.gif' width="130" height="130" id='imagepreview'></td>
				<td valign='top'><table>
				<tr>
					<td><strong>Alignment </strong></td>
					<td>
					<select id='imagealign' style='width:150'>
						<option value=''>None</option>
						<option value='left'>Left</option>
						<option value='center'>Center</option>
						<option value='right'>Right</option>
					</select></td>
				</tr>
				<tr>
					<td><strong>Spacing </strong></td>
					<td>
					<select id='imagespacing' style='width:150'>
						<option value='0'>None</option>
						<option value='1'>1px</option>
						<option value='2'>2px</option>
						<option value='3' selected='true'>3px</option>
						<option value='4'>4px</option>
						<option value='5'>5px</option>
						<option value='6'>6px</option>
						<option value='7'>7px</option>
						<option value='8'>8px</option>
						<option value='9'>9px</option>
						<option value='10'>10px</option>
					</select></td>
				</tr></table><span id='imagesize'><strong>Width:</strong> <em>X px</em>&#160;<strong>Height:</strong> <em>Y px</em><br>
<strong>Size:</strong><br>
<strong>Approximate Download Speeds:</strong><br>
<em>56k:</em> X secs &#160;<em>ISDN:</em> X sec</span>

</td>
			</tr>
			<tr>
				<td>				
				<input ONCLICK="selectClick()" TYPE=button class="bt" ID=idSave  VALUE="<?php echo LOCALE_SELECT; ?>">				
				<input class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="<?php echo LOCALE_CANCEL;?>"></td>
			</tr></table>
		</div>
	</form>
<script language="javascript"><!--
    window.name = 'imglibrary';
  //-->
</script><script>
	var winOpener = window.dialogArguments.document.parentWindow;
	var base_href ='<?php print $base_href; ?>';
	var session_url ='<?php print $session_url; ?>';
	winOpener.__extract_information('image');
	setTimeout("get_images()",1000);
</script>
<script language="javascript" src="utils.js"></script>
<script language="javascript"><!--
  	function get_images(){
		if (window.dialogArguments.document.cache_data.frmDoc.image_data.value!=''){
			if (window.dialogArguments.document.cache_data.frmDoc.image_data.value!='__NOT_FOUND__'){
				tmp 			= new String(window.dialogArguments.document.cache_data.frmDoc.image_data.value);
				myArray 		= tmp.split("|1234567890|")
				l				= myArray.length-1;
				list="";
				document.image_browser.imagesrc.options.length=0
				for (i = 0;i<l;i+=2){
					document.image_browser.imagesrc.options[document.image_browser.imagesrc.options.length] = new Option(convert_special_characters(myArray[i]), convert_special_characters(myArray[i+1]));
				}
			} else {
				alert("Sorry there are no images available");
			}
		} else {
			setTimeout("get_images()",100);
		}
	}
    function selectClick()
    {
      if (document.image_browser.imagesrc.selectedIndex>0){
        window.returnValue = save_image();
        window.close();
      } else {
        alert('Please select an image to insert');
      }
    }
    
    function Init()
    {
         resizeDialogToContent();
    }
	//	extract_cache.location = base_href+"admin/load_cache.php?command=FILES_FILTER&filter=image&"+session_url;
//-->
</script>
</body>
</html>
