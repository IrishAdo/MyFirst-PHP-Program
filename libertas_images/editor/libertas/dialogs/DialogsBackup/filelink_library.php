<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:10 $
	-	$Revision: 1.3 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';

$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$base_href = check_parameters($_GET,"base_href","/");
$info = split($base_href,check_parameters($_GET,"url",""));
if (count($info)==2){
	$szURL = $info[1];
} else {
	$szURL = $info[0];
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
  <title>Insert Links</title>
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">

  <script language="javascript" src="utils.js"></script>
</head>

<body onLoad=Init()>
<form name="link_browser" method="post" action="">
<input type="hidden" name="theme" value="default">
<input type="hidden" name="lang" value="en">
<input type="hidden" name="images" value="">
<input type=hidden ID='fileurlValue' name='fileurlValue' SIZE=35 VALUE="<?php print $szURL; ?>">
<div style="border: 1 solid Black; padding: 5 5 5 5;">

<DIV ID='internalprop1'>
<P id=tableProps CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_filelink.gif'/> File Download Link Manager</P>
<table width='450px' border=0>
<tr><td align='right'>Filter:: </td><td><select id='filter_files' style='width:350px' name='filter_files' onchange='javascript:window.self.filter_file()'><option>*.* All types of Files</option></select></td></tr>
<tr><td align='right'>Files :: </td><td><select id='list_of_files' style='width:350px' name='list_of_files' onchange='javascript:window.self.extract_file()'><option>--- Loading list of available files ---</option></select></td></tr>
<tr><td align='right'>Title :: </td><td><input type=text	ID='fileurlTitle' name='fileurlTitle' style="width:350px"  SIZE=35 VALUE="<?php print $szTitle; ?>"></td></tr>
<tr><td align='right'>Open in new window :: </td><td><input type='checkbox' ID='externalWindow' name='externalWindow' VALUE="Yes"> <label for='externalWindow'>Yes,</label><br><small class='warning'>(warning some people use programs<br> to stop new windows being opened by <br>the browser)</small></td></tr>
<tr><td colspan='2' align='center'><INPUT ONCLICK="selectClick()" TYPE=button class="bt" ID=idSave  VALUE="<?php echo LOCALE_INSERT; ?>"> 
<INPUT class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="<?php echo LOCALE_CANCEL;?>">

</td></tr></table>
<!--parent._CLinkPopupRenderer_AddLink(this.document,'internal','"+base_href+"')-->
</DIV>
</div>

</form>
<script language="javascript">
<!--

	var winOpener = window.dialogArguments.document.parentWindow;

	var base_href ='<?php print $base_href; ?>';
	var externalWindow ='<?php print $szExternal; ?>';
	urlinfo = '<?php print $szURL; ?>';
	var URLTitle ='<?php print $szTitle; ?>';
	var loaded=0;
	var session_url ='<?php print $session_url; ?>';
	
	winOpener.__extract_information('file','');
	window.self.file_list_data();
	if (externalWindow=='Yes'){
		document.link_browser.externalWindow.checked=true;
	}
	function resultData(szURL,szTitle,szType,szExternal){
		this.szURL 		= szURL;
		this.szTitle 	= szTitle;
		this.szType		= szType;
		this.szExternal	= szExternal;
	}  
    function selectClick(){
		if (document.all.link_browser.list_of_files.selectedIndex>0){
			var szURL	= document.all.link_browser.fileurlValue.value.split("'").join("&#39;");
			var szTitle	= document.all.link_browser.fileurlTitle.value.split("'").join("&#39;");
			szType = base_href;
			if (document.link_browser.externalWindow.checked){
				var szExternal = 'Yes';
			}
			var result = new resultData(szURL,szTitle,szType,szExternal);
    	 	window.returnValue = result;
      		window.close();
      	} else {
        	alert('Please select an file for downloading');
		}
    }
	function get_links(){
		f = window.dialogArguments.document.cache_data.frmDoc.file_data.value;

		if (f!=''){
			if (f!='__NOT_FOUND__'){
//				alert(f.substr(0,6000));
//				alert(f.substr(6000));
				found			= 0;
				tmp 			= new String(f);
				var myArray 	= tmp.split("|1234567890|");
				var len			= myArray.length-1;
				list			= "";
				filter_list 	= new Array();
				document.all.link_browser.list_of_files.options.length=0
				document.all.link_browser.list_of_files.options[document.all.link_browser.list_of_files.options.length] = new Option("--- Select a File to link to ---");
				for (i = 0 ; i < len ; i += 2){
					var itemdata = myArray[i+1].split("::")[0];
					
					if(itemdata.indexOf("/") != -1 && itemdata.indexOf(".") != -1)
					{
						var myExt = itemdata.split("/")[1];
						if (myExt+"" != "undefined"){
							ext = myExt.split(".")[1];
						} else {
							ext = new Array("", "");
						}
						if (ext[1]+''!='undefined'){
							document.all.link_browser.list_of_files.options[document.all.link_browser.list_of_files.options.length] = new Option(convert_special_characters(myArray[i], false) + " (."+ext[1]+")", myArray[i+1], "");
							if (filter_list[ext[1]+'']+''=='undefined')
								filter_list[ext[1]+''] = 1;
							else 
								filter_list[ext[1]+''] ++;
						} else {
							// don't have an extension?
							var list = myArray[i+1].split("::");
							
							if (filter_list[list[3]+'']+''=='undefined')
								filter_list[list[3]+''] = 1;
							else 
								filter_list[list[3]+''] ++;
							document.all.link_browser.list_of_files.options[document.all.link_browser.list_of_files.options.length] = new Option(convert_special_characters(myArray[i], false), myArray[i+1], "");
						}
					}
				}
				document.all.link_browser.filter_files.options.length=1;
				for (key in filter_list){
					document.all.link_browser.filter_files.options[document.all.link_browser.filter_files.options.length] = new Option("*."+key+" "+filter_list[key]+" file(s)", key, "");
				}
			} else {
				document.all.link_browser.list_of_files.options.length=0
				document.all.link_browser.list_of_files.options[0] = new Option("Sorry none available");
			}
		}
	}
	function file_list_data(){
		f = window.dialogArguments.document.cache_data.frmDoc;
		//	alert(document.frmDoc.menu_data.value);
		winOpener.cachedata.files  = f.file_data.value;
		
		if (winOpener.cachedata+'' !='undefined'){
			if (winOpener.cachedata.files.length==0){
				setTimeout("window.self.file_list_data()",100);
			} else {
				setTimeout("window.self.get_links()",100);
			}
		}
	}
	function extract_file(){
		var val = document.all.link_browser.list_of_files.options[document.all.link_browser.list_of_files.selectedIndex].value
		var myFilename = val.split("::")[0].split("/");
		fn_id = myFilename[myFilename.length-1].split(".")[0];
		document.all.link_browser.fileurlValue.value = "?command=FILES_DOWNLOAD&download=" + fn_id;
		document.all.link_browser.fileurlTitle.value = document.all.link_browser.list_of_files.options[document.all.link_browser.list_of_files.selectedIndex].text;
	}
	function filter_file(){
		myFilenameExt = document.all.link_browser.filter_files.options[document.all.link_browser.filter_files.selectedIndex].value;
		f = window.dialogArguments.document.cache_data.frmDoc.file_data.value;
		found			= 0;
		tmp 			= new String(f);
		myArray 		= tmp.split("|1234567890|");
		len				= myArray.length-1;
		document.all.link_browser.list_of_files.options.length=0;
		document.all.link_browser.list_of_files.options[document.all.link_browser.list_of_files.options.length] = new Option("--- Select a File to link to ---");
		for (i = 0;i<len;i+=2){
			if (myArray[i+1].split("::")[3]==myFilenameExt || ''==myFilenameExt){
				document.all.link_browser.list_of_files.options[document.all.link_browser.list_of_files.options.length] = new Option(convert_special_characters(myArray[i], false), myArray[i+1], "");
			}
		}
	}
	
	
    function Init(){
         resizeDialogToContent();
    }
//-->
</script></body>
</html>
