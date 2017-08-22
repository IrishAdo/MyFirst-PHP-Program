<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
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

<body>
<form name="image_browser" method="post" action="">
<input type="hidden" name="theme" value="default">
<input type="hidden" name="lang" value="en">
<input type="hidden" name="images" value="">
<div style="border: 1 solid Black; padding: 5 5 5 5;">
<P id=tableProps CLASS=tablePropsTitle>Embedding Movie Files Manager</P>

<table width='450px' border=0>
	<tr><td><strong>Select movie file</strong></td><td><select id='imagesrc' name='imagesrc'  style='width:230px'><option>Loading Movie list...</option></select></td></tr>
	<tr><td >Filter:: </td><td><select id='filter_files' style='width:230px' name='filter_files' onchange='javascript:window.self.filter_file()'><option>*.* All types of Files</option></select></td></tr>
	<tr><td colspan='2'><INPUT ONCLICK="selectClick()" TYPE=button class="bt" ID=idSave  VALUE="<?php echo LOCALE_SELECT; ?>"> 
<INPUT class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="<?php echo LOCALE_CANCEL;?>">

</td></tr>
</table>
</div>

</form>
	<Script>
		var base_href ='<?php print $base_href; ?>';
		var session_url ='<?php print $session_url; ?>';
		get_movie();
		window.opener.__extract_information('movie');

	function get_movie(){
		if (window.opener.cache_data.frmDoc.movie_data.value!=''){
			if (window.opener.cache_data.frmDoc.movie_data.value!='__NOT_FOUND__'){
				tmp 			= new String(window.opener.cache_data.frmDoc.movie_data.value);
				myArray 		= tmp.split("|1234567890|")
				l				= myArray.length-1;
				list			= "";
				filter_list 	= new Array();
				document.image_browser.imagesrc.options.length=0
				for (i = 0;i<l;i+=2){
					document.image_browser.imagesrc.options[document.image_browser.imagesrc.options.length] = new Option(convert_special_characters(myArray[i],false), myArray[i+1]);
					ext = myArray[i+1].split("::")[0].split(".");
					if (ext[1]+''!='undefined'){
						if (filter_list[ext[1]+'']+''=='undefined')
							filter_list[ext[1]+''] = 1;
						else 
							filter_list[ext[1]+''] ++;
					}
				}

				document.all.image_browser.filter_files.options.length=1;
				for (key in filter_list){
					document.all.image_browser.filter_files.options[document.all.image_browser.filter_files.options.length] = new Option("*."+key+" "+filter_list[key]+" file(s)", key, "");
				}
			} else {
				document.image_browser.imagesrc.options.length=0
				document.image_browser.imagesrc.options[0] = new Option("Sorry none available");
				//alert("Sorry there are no movies available to embed");
			}
		} else {
			setTimeout("get_movie()",1000);
		}
	}
    function selectClick(){
	  	if (document.image_browser.imagesrc.selectedIndex>0){
			movie_info = ''+document.image_browser.imagesrc.options[document.image_browser.imagesrc.selectedIndex].value;
    		window.opener.__insert_movie(movie_info);
        	window.close();
		} else {
        	alert('Please select an movie file to insert');
      	}
    }
function filter_file(){
	myFilenameExt = document.all.link_browser.filter_files.options[document.all.link_browser.filter_files.selectedIndex].value;
	f = window.opener.cache_data.frmDoc.file_data.value;
	found			= 0;
	tmp 			= new String(f);
	myArray 		= tmp.split("|1234567890|");
	len				= myArray.length-1;
	document.all.link_browser.list_of_files.options.length=0
	document.all.link_browser.list_of_files.options[document.all.link_browser.list_of_files.options.length] = new Option("--- Select a File to link to ---");
	for (i = 0;i<len;i+=2){
		if (myArray[i+1].split("::")[0].split(".")[1]==myFilenameExt || ''==myFilenameExt)
			document.all.link_browser.list_of_files.options[document.all.link_browser.list_of_files.options.length] = new Option(convert_special_characters(myArray[i],false), myArray[i+1], "");
	}
		
}
	</script>
  <script language="javascript" src="utils.js"></script>
</body>
</html>
