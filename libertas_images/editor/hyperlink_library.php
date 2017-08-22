<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
include '../config/libertas_control.config.php';
//$libertas_locale = "/wip/locale/";
include $libertas_locale.'en/locale.php';

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
</head>

<body onLoad=Init()>
<form name="link_browser" method="post" action="">
<input type="hidden" name="theme" value="default">
<input type="hidden" name="lang" value="en">
<input type="hidden" name="images" value="">
<input type=hidden ID='internalurlValue' name='internalurlValue' SIZE=35 VALUE="<?php print $szURL; ?>">
<div style="border: 1 solid Black; padding: 5 5 5 5;">

<DIV ID='internalprop1'>
<P id=tableProps CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_hyperlink.gif'/> Internal WebSite Link Manager</P>
<table width='450px' border=0><tr><td align='right'>Location ::</td><td><select style="width:350px" name='menu_locations' id='menu_locations' onchange='javascript:extract_url(this.document)'><option>--- Loading site structure ---</option></select></td></tr>
<tr style='display:none;visibility:hidden' id='listbutton'><td align='right'>Pages :: </td><td align='center' id='btn'><input type='button' value='Retrieve List of Pages' class='bt' onclick='javascript:extract_page_url(this.document,"__RETRIEVE__")'></td></tr>
<tr style='display:none;visibility:hidden' id='listpages'><td align='right'>Pages :: </td><td><select id='pages' style="width:350px" name='pages' onchange='javascript:extract_page_url(this.document)'><option>--- Select a menu Location first ---</option></select></td></tr>
<tr><td align='right'>Title :: </td><td><input type=text ID='internalurlTitle' name='internalurlTitle' style="width:350px"  SIZE=35 VALUE="<?php print $szTitle; ?>"></td></tr>
<tr><td align='right'>Open in new window :: </td><td><input type='checkbox' ID='externalWindow' name='externalWindow' VALUE="Yes"> <label for='externalWindow'>Yes,</label><br><small class='warning'>(warning some people use programs<br> to stop new windows being opened by <br>the browser)</small></td></tr>
<tr><td colspan='2' align='center'><INPUT ONCLICK="selectClick()" TYPE=button class="bt" ID=idSave  VALUE="<?php echo LOCALE_INSERT; ?>"> 
<INPUT class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="<?php echo LOCALE_CANCEL;?>"></td></tr></table>
<!--parent._CLinkPopupRenderer_AddLink(this.document,'internal','"+base_href+"')-->
</DIV>
</div>

</form>
  <script language="javascript" src="utils.js"></script>
  <script language="javascript">
 <!--
	var winOpener = window.dialogArguments.document.parentWindow;
	var base_href ='<?php print $base_href; ?>';
	urlinfo = '<?php print $szURL; ?>';
	var externalWindow ='<?php print $szExternal; ?>';
	var URLdata=urlinfo;
	if (URLdata.indexOf("index.php")==-1){
		fakelist = URLdata.split("/");
		fakelist[fakelist.length-1] ="index.php";
		var fakeURLdata = fakelist.join("/");
	} else {
		var fakeURLdata = URLdata;
	}
	var URLTitle ='<?php print $szTitle; ?>';
	var loaded=0;
	var session_url ='<?php print $session_url; ?>';
	winOpener.__extract_information('menu','');
	window.self.menu_list_data();
	
	
	function get_links(){
		f = window.dialogArguments.document.cache_data.frmDoc.menu_data.value;
		var prev_style="",identifier=-1 ,parent_identifier=-1;
		var mystyle ="";
		if (f!=''){
			if (f!='__NOT_FOUND__'){
				found			= 0;
				tmp 			= new String(f);
				myArray 		= tmp.split("|1234567890|");
				len				= myArray.length-1;
				list			= "";
				document.all.link_browser.menu_locations.options.length=0
				document.all.link_browser.menu_locations.options[0] = new Option("--- Select a menu location ---");
				document.all.link_browser.menu_locations.options[0].style.color="#993399";
				for (var i = 0; i<len; i+=2){
					var split_list = myArray[i+1].split("::");
					if (split_list[3]+''=='1'){
						mystyle				= "#ff0000";
						parent_identifier	= split_list[1];
						identifier 			= split_list[0];
					} else {
						if (split_list[1]==identifier){
							
						} else {
							mystyle = "#000000";
							parent_identifier	= -1;
							identifier 			= -1;
						}
					}
					pos = document.all.link_browser.menu_locations.options.length;
					label_str = myArray[i].split("&#39;").join("'").split("&#160;").join("&nbsp;");
					document.all.link_browser.menu_locations.options[pos] = new Option(fix(label_str), fix(myArray[i+1]), "");
//					document.all.link_browser.menu_locations.options[pos].style.color = mystyle;
				}
			}
		}
	}
    function selectClick(){
		if (document.link_browser.menu_locations.selectedIndex>0){
			var szURL	= document.all.internalurlValue.value.split("'").join("&#39;").split("&#160;").join("&nbsp;");
			var szTitle	= document.all.internalurlTitle.value.split("'").join("&#39;").split("&#160;").join("&nbsp;");
			szType = base_href;
//			alert("insert link :" + szURL + ", " + szTitle + ", " + szType);
			if (document.link_browser.externalWindow.checked){
				var szExternal = 'Yes';
			}
			var result = new resultData(szURL,szTitle,szType,szExternal);
	     	window.returnValue = result;
     		window.close();
      	} else {
        	alert('Please select an internal link to insert');
		}
    }
    

function extract_page_url(t,p){
	if (p+"" == "__RETRIEVE__"){
		window.dialogArguments.document.cache_data.frmDoc.page_data.value = "";
		document.all['btn'].innerHTML = '<font color="#ff0000">Retrieving List of Pages</font>';
		winOpener.__extract_information('page',"menu_url="+escape(split_list[2]));
		window.self._page_list_data(myindex);
	} else {
		document.link_browser.internalurlValue.value = document.link_browser.pages.options[document.link_browser.pages.selectedIndex].value;
		document.link_browser.internalurlTitle.value = document.link_browser.pages.options[document.link_browser.pages.selectedIndex].text;
	}
}
function extract_url(t){
	if (document.link_browser.menu_locations.selectedIndex==0){
		document.link_browser.internalurlValue.value = "";
		document.link_browser.internalurlTitle.value = "";

		document.all['btn'].innerHTML = "<input type='button' value='Retrieve List of Pages' class='bt' onclick='javascript:extract_page_url(this.document,\"__RETRIEVE__\")'>";
		document.all['listbutton'].style.visibility='hidden';
		document.all['listbutton'].style.display='none';
		document.all['listpages'].style.visibility='hidden';
		document.all['listpages'].style.display='none';

		document.link_browser.pages.options.length			= 1;
		document.link_browser.pages.options[0].text	 		= "--- Select a menu Location first ---";
		document.link_browser.pages.options[0].value		= "";
		document.link_browser.pages.options[0].selected 	= true;
		document.link_browser.pages.options[0].style.color	= "#990000";
	}else{
		split_list = document.link_browser.menu_locations.options[document.link_browser.menu_locations.selectedIndex].value.split("::");
		myindex = split_list[2].split("/").join("_").split(".").join("_");

		document.link_browser.internalurlValue.value = split_list[2];
		document.link_browser.internalurlTitle.value = document.link_browser.menu_locations.options[document.link_browser.menu_locations.selectedIndex].text;

		document.all['btn'].innerHTML = "<input type='button' value='Retrieve List of Pages' class='bt' onclick='javascript:extract_page_url(this.document,\"__RETRIEVE__\")'>";
		document.all['listbutton'].style.visibility='visible';
		document.all['listbutton'].style.display='';
		document.all['listpages'].style.visibility='hidden';
		document.all['listpages'].style.display='none';
	
		document.link_browser.pages.options.length			= 2;
		document.link_browser.pages.options[0].text	 		= "--- Link to this menu location ---";
		document.link_browser.pages.options[0].value		= split_list[2];
		document.link_browser.pages.options[0].selected 	= true;
		document.link_browser.pages.options[0].style.color	= "#009900";
/*		document.link_browser.pages.options[1].text			= "--- Retrieve list of pages in this location. ---";
		document.link_browser.pages.options[1].value		= "__RETRIEVE__";
		document.link_browser.pages.options[1].style.color	= "#0000ff";
*/
	}
	resizeDialogToContent();
}

function menu_list_data(){
	f = window.dialogArguments.document.cache_data.frmDoc.menu_data.value;
	if (f.length==0){
		setTimeout("window.self.menu_list_data()",100);
	} else {
		setTimeout("window.self.get_links()",100);
	}
}
function _page_list_data(myindex){
	var frm = window.dialogArguments.document.cache_data.frmDoc;
	if (frm.page_data.name+''=='page_data'){	
		if ("data:"+frm.page_data.value!="data:"){
//			alert(frm.page_data.value);
			winOpener.cachedata.pages[myindex] = new String(frm.page_data.value);
			window.self._Show("InternalLink",myindex);
//			alert(window.opener.cachedata.pages["company_news_index_php"])
		} else {
			setTimeout("_page_list_data('"+myindex+"');",500);
		}
	}
}
function _Show(szID,myindex){
	if (winOpener.cachedata.pages[myindex]+"" == "undefined"){
//	alert("undefined");
	}else{
		document.all['listbutton'].style.visibility='hidden';
		document.all['listbutton'].style.display='none';
		document.all['listpages'].style.visibility='visible';
		document.all['listpages'].style.display='';
		document.all.link_browser.pages.options.length=0;
		document.all.link_browser.pages.options[document.all.link_browser.pages.options.length] = new Option("--- Link to this menu location ---");
		document.link_browser.pages.options[0].style.color="#009900";
		if (winOpener.cachedata.pages[myindex]!="__NOT_FOUND__"){
			page_listing = winOpener.cachedata.pages[myindex].split("|1234567890|");
			for(var index = 0; index<page_listing.length-1; index+=2){
				label_str = page_listing[index].split("&#39;").join("'");
				document.all.link_browser.pages.options[document.all.link_browser.pages.options.length] = new Option(fix(label_str), fix(page_listing[index+1]));
			}
		} else {
			alert('Sorry there is no pages published to this location');
		}
		resizeDialogToContent();
	}
}
function resultData(szURL,szTitle,szType,szExternal){
	this.szURL 		= szURL;
	this.szTitle 	= szTitle;
	this.szType		= szType;
	this.szExternal	= szExternal;
}  

function Init(){
    resizeDialogToContent();
}

function fix(str){
	str = str.replace('nbsp;',' ')
	point 	= str.indexOf("&");
	
	if (point>-1){
		tmp 	= str.substring(0,point);
		rest 	= str.substring(str.indexOf("&")+1);
		semi 	= rest.indexOf(";");
		space 	= rest.indexOf(" ");
		if (semi>-1 && semi<space){
			str = fix(tmp+rest.substring(semi+1));
		}else{
			if (space>-1 && ((space<semi) || semi==-1)){
				str = fix(tmp+rest.substring(space));
			}else {
			 	str = tmp+rest;
			}
		}
	}
	return str;
}

	if (externalWindow=='Yes'){
		document.link_browser.externalWindow.checked=true;
	}
	// -->
	</script>
</body>
</html>
