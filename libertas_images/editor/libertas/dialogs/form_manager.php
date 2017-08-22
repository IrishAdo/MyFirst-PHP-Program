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

/*
$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$base_href = check_parameters($_GET,"base_href","/");
*/

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
	<iframe src='about:blank' width='0' height='0' style='visibility:hidden' id='extract_cache2' name='extract_cache2'></iframe>
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
	/* IE7 Get Base Path and URL Porrion Starts */
	var base_href;
	var base_path;
	try{
		list = new String(pw.location+"").split("/");
//		list = new String(window.location+"").split("/");
		if (list[3].indexOf("~")!=-1){
			base_href = "http://"+list[2]+"/"+list[3]+"/"
			base_path = "/"+list[3]+"/"
			mystart=4
		} else {
			base_href = "http://"+list[2]+"/"
			base_path = "/"
			mystart=3
		}
	}catch(e){
		mystart=-1;
	}
	var session_url='<?php print session_name("LEI")."=".session_id(); ?>'
	if (mystart==-1){
	}else{
		for (i=mystart;i<list.length;i++){
			if (list[i].indexOf('<?php print session_name(); ?>')!=-1){
				l = list[i].split("?");
				file_name = l[0];
				qstring =l[1].split("&");
				for (z=0;z<qstring.length;z++){
					if (qstring[z].indexOf('<?php print session_name(); ?>')!=-1){

						session_url =  qstring[z]
					}
				}
			}
		}
	}

	/* IE7 Get Base Path and URL Porrion Ends */
	/* Inorder to cope with IE security restrictions this patch is added */
	iframeloc = base_href+"admin/load_cache.php?command=SFORM_FORM_EMBED&"+session_url;	
	extract_cache2.location.href= iframeloc;

	 /*IE7 security restrictions would not allow */
	//pw.__extract_information('forms');
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

	function _exec_function(s,field){
//		alert("_exec_function('"+field+"') = "+s)
		if (field=='menu'){
			window.dialogArguments.document.cache_data.frmDoc.menu_data.value = s;
		}
		if (field=='page'){
			window.dialogArguments.document.cache_data.frmDoc.page_data.value = s;
		}
		if (field=='file'){
			window.dialogArguments.document.cache_data.frmDoc.file_data.value = s;
		}
		if (field=='image'){
			window.dialogArguments.document.cache_data.frmDoc.image_data.value = s;

		}
		if (field=='flash'){
			window.dialogArguments.document.cache_data.frmDoc.flash_data.value = s;
		}
		if (field=='audio'){
			window.dialogArguments.document.cache_data.frmDoc.audio_data.value = s;
		}
		if (field=='movie'){
			window.dialogArguments.document.cache_data.frmDoc.movie_data.value = s;
		}
		if (field=='forms'){
			window.dialogArguments.document.cache_data.frmDoc.form_data.value = s;
		}
		if (field=='webobjects'){
			window.dialogArguments.document.cache_data.frmDoc.webobjects_data.value = s;
		}
		if (field=='category'){
			window.dialogArguments.document.cache_data.frmDoc.category_data.value = s;
		}
		if (field=='infodir'){
			window.dialogArguments.document.cache_data.frmDoc.infodir_data.value = s;
		}
		if (field=='query'){
			window.dialogArguments.document.cache_data.frmDoc.query_data.value = s;
		}
		if (field=='fields'){
			window.dialogArguments.document.cache_data.frmDoc.field_data.value = s;
		}
		if (field=='field_options'){
			window.dialogArguments.document.cache_data.frmDoc.field_options.value = s;
		}
//			alert(s);
	}


  //-->
  </script>
</body>
</html>
