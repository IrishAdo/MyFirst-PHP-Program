<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:05 $
	-	$Revision: 1.2 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	

$session_url= "PHPSESSID=".check_parameters($_GET,"PHPSESSID","NA");
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
<iframe src='about:blank' width='450' height='0' style='visibility:hidden' id='extract_cache' name='extract_cache'></iframe>
	<Script>
		var base_href ='<?php print $base_href; ?>';
		var session_url ='<?php print $session_url; ?>';
		function _exec_function(s,field){
			if (field=='menu'){
				document.frmDoc.menu_data.value = s;
			}
			if (field=='page'){
				document.frmDoc.page_data.value = s;
			}
			if (field=='file'){
				document.frmDoc.file_data.value = s;
			}
			if (field=='image'){
				document.frmDoc.image_data.value = s;
				assign_data(s)
			}
			
		}
		function assign_data(s){
			tmp 			= new String(s);
			myArray 		 = tmp.split("|1234567890|")
			l				 = myArray.length-1;
			list="";
			document.image_browser.imagesrc.options.length=0
			for (i = 0;i<l;i+=2){
				document.image_browser.imagesrc.options[document.image_browser.imagesrc.options.length] = new Option(myArray[i], myArray[i+1]);
			}
		}
	</script>
  <script language="javascript" src="utils.js"></script>
  <script language="javascript">
  <!--
    function selectClick()
    {
      if (document.image_browser.imagesrc.selectedIndex>=0)
      {
        window.returnValue = save_image();
        window.close();
      }
      else
      {
        alert('Please select an image to insert');
      }
    }
    
    function Init()
    {
      resizeDialogToContent();
    }
	extract_cache.location = base_href+"admin/load_cache.php?command=FILES_FILTER&filter=image&"+session_url;
  //-->
  </script>
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
<div style="border: 1 solid Black; padding: 5 5 5 5;">

<table width='450px' border=0>
	<tr><td><strong>Select Image</strong></td><td><select id='imagesrc' name='imagesrc'  style='width:230px' onchange='parent.ShowImage(this.document);'><option>Please wait while I download the list of images.</option></select></td></tr>
	<tr><td><strong>Alt Tag information</strong></td><td><input type=text id='imagealt' size=255 style='width:230px'><input type=hidden id='imageexisting' value=''></td></tr>
	<tr><td valign=top><p>Preview</p><img border=1 src='/libertas_images/themes/1x1.gif' width=130 height=130 id ='imagepreview'></td>
	<td valign='top'><table >
	<tr><td><strong>Alignment </strong></td><td><select id='imagealign' style='width:150'><option value=''>None</option><option value='left'>Left</option><option value='center'>Middle</option><option value='right'>Right</option></select></td></tr>
	<tr><td><strong>Spacing </strong></td><td><select id='imagespacing' style='width:150'><option value='0'>None</option></select></td></tr>
	</table><span id='imagesize'><strong>Width:</strong> <em>X px</em>&#160;<strong>Height:</strong> <em>Y px</em><br><strong>Size:</strong><br><strong>Approximate Download Speeds:</strong><br><em>56k:</em> X secs &#160;<em>ISDN:</em> X sec</span></td></tr>
	<tr><td><INPUT ONCLICK="selectClick()" TYPE=button class="bt" ID=idSave  VALUE="<?php echo LOCALE_SELECT; ?>"> 
<INPUT class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="<?php echo LOCALE_CANCEL;?>">

</td></tr>
	<tr><td>
	  
</table>
</div>

</form>
	<form name=frmDoc>
	  <input type=hidden name="frmType" value="menu">
	  <input type=hidden name="image_data" value="">
	  <input type=hidden name="menu_data" value="">
	  <input type=hidden name="page_data" value="">
	  <input type=hidden name="file_data" value="">
	</form>
</body>
</html>
