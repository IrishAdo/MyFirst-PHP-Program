<?php 
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/10/02 12:11:38 $
	-	$Revision: 1.3 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';
	function check_parameters($parameters,$name,$default=""){
		if (isset($parameters[$name])){
			$value = $parameters[$name];
		} else {
			$value = $default;
		}
		return $value;
	}
$domain 	= $_SERVER["HTTP_HOST"];
$base_href 	= "http://$domain".check_parameters($_GET,"base_href","/");
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<meta http-equiv="Pragma" content="no-cache">
	<title>Libertas Solutions - - - - - - - - - - - - - - - - - - - - - - - - - </title>
	<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
</head>

<body onLoad="Init()" dir="ltr">
<div style="border: 1 solid Black; padding: 5 5 5 5;background-color:white">
<P id=tableProps CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_slideshow.gif'/> Slideshow Manager</P>
<form name="image_browser">
<input type="hidden">
<table cellspacing=0 cellpadding=3 width="500">
<tr>
	<td id='imageone' class='tabularbuttonleft' width="50%" onclick="show('imageone');">Main Image</td>
	<td id='preview' class='tabularbuttonright' width="50%" onclick="show('preview');">Preview</td>
</tr>
<tr><td colspan='3' id='tabularlist' class='tabularcontent'><table>
		<tr><td><strong>Alternative text</strong></td><td colspan='3'><input name='imagealt' size='40' maxlength='255' value =''></td></tr>
		<tr>
			<td><strong>Select Image</strong></td>
			<td><select id='imagesrc' name='imagesrc' style='width:230px' onChange='parent.previewImage(this.document);'>
					<option>Please wait while I download the list of images.</option>
				</select></td>
		</tr>
		<tr>
			<td><img border="1" src='/libertas_images/themes/1x1.gif' width="130" height="130" id='imagepreview'></td>
			<td><strong>Alignment</strong><select name='imagealignment'>
	<option value=''>None</option>
	<option value='left'>Left</option>
	<option value='center'>Center</option>
	<option value='right'>Right</option>
	</select><br/><span id='imagesize'><strong>Width:</strong> <em>X px</em>&#160;<strong>Height:</strong> <em>Y px</em><br>
				<strong>Size:</strong><br>
				<strong>Approximate Download Speeds:</strong><br>
				<em>56k:</em> X secs &#160;<em>ISDN:</em> X sec</span><br/>
			</td>
		</tr>
		<tr><td colspan="2"><input type="button" value="Set as First Image" onClick="addClick(0)" class="bt" /> <input type="button" value="Set as Second Image" onClick="addClick(1)" class="bt"/></td></tr>
	</table></td></tr>
	<tr><td colspan='3' id='tabularpreview' class='tabularcontent' style="visibility:hidden;display:none"></td></tr>
<tr>
<td colspan=3 align="right" valign="bottom">
<input type="button" value="<?php echo LOCALE_OK; ?>" onClick="okClick()" class="bt">
<input type="button" value="<?php echo LOCALE_CANCEL;?>" onClick="cancelClick()" class="bt">
</td>
</tr>
</table>
</form>
</div>  
<script language="javascript" src="utils.js"></script>
<script src="/libertas_images/javascripts/slideshow/slideshow.js" ></script>
<script language="javascript">
<!--  
var list_of_images = Array('/libertas_images/themes/1x1.gif', '/libertas_images/themes/1x1.gif');
var winOpener = window.dialogArguments.document.parentWindow;
	winOpener.__extract_information('image');
setTimeout("get_images()",1000);
var base_href = "<?php print $base_href;?>";
var cur_color; // passed color
var current_tab = 'web';
var original ;

function Init() {
    args = window.dialogArguments;
	original = window.dialogArguments.original;
	if (original+''!='undefined'){
		original.alt		= set_field_default(original.alt+'', '');
		original.align		= set_field_default(original.align+'', '');
//		original.width		= set_field_default(original.width+'', -1);
//		original.height		= set_field_default(original.height+'', -1);

		checkid = original.id+''
		if (checkid.substring(0,9)=='mouseover'){
			tmp_src1 = original.onmouseover.split("'");
			tmp_src2 = original.onmouseout.split("'");
			list_of_images[0] = tmp_src1[1];
			list_of_images[1] = tmp_src2[1];
		}
	} else {
		original = {};
		original.align		= '';
		original.alt		= '';
//		original.width		= -1;
//		original.height		= -1;
		list_of_images[0] = '/libertas_images/themes/1x1.gif';
		list_of_images[1] = '/libertas_images/themes/1x1.gif';
	}
	if(""==original.align){
		document.image_browser.imagealignment.options[0].selected= true;
	}
	if("left"==original.align){
		document.image_browser.imagealignment.options[1].selected= true;
	}
	if("center"==original.align){
		document.image_browser.imagealignment.options[2].selected= true;
	}
	if("right"==original.align){
		document.image_browser.imagealignment.options[3].selected= true;
	}
	document.image_browser.imagealt.value	= original.alt+'';
//	document.image_browser.imagewidth.value	= original.width+'';
//	document.image_browser.imageheight.value= original.height+'';
    resizeDialogToContent(560,400);
}

function set_field_default(field, default_value){
//	alert(field+'')
	if ((field+''=='null') || (field+''=='undefined') || (field+''=='')) {
		return default_value;
	} else {
		return field;
	}
	
}
  
  function random_digits(c){
		sz="";
		for(var i=0; i<c;i++){
			sz+= ""+Math.floor(Math.random() * 10)-1;
		}
		return sz;
	}
	
function okClick() {
	var returnObj = {};
	returnObj.alt 			 = document.image_browser.imagealt.value;
	returnObj.imagealignment = document.image_browser.imagealignment.options[document.image_browser.imagealignment.selectedIndex].value;
//	returnObj.width 		 = document.image_browser.imagewidth.value;
//	returnObj.height 		 = document.image_browser.imageheight.value;
	d = new Date();
	
	str = base_href+list_of_images[0];
//	+'" id="mouseover" align="'+ returnObj.imagealignment +'" alt="'+returnObj.alt+'" hspace="5" vspace="5" ';
	returnObj.source=str;
	returnObj.id="mouseover";
	returnObj.onmouseover=list_of_images[0];
	returnObj.onmouseout=list_of_images[1];
    window.returnValue = returnObj;
    window.close();
	
}

function cancelClick() {
	window.returnValue = null;
    window.close();
}
  
  
function show(identifier){
 	if(identifier!=current_tab){
		if(identifier=='imageone'){
			tabularpreview.style.visibility 		= 'hidden';
			tabularpreview.style.display 			= 'none';
			tabularpreview.style.background			= '#ffffff';
			imageone.className 					= 'tabularbuttonon';
			preview.className 						= 'tabularbuttonoff';
			tabularlist.style.visibility		 	= 'visible';
			tabularlist.style.display 				= '';
			tabularlist.style.background			= '#ffffff';
		}else {
			tabularpreview.style.visibility 		= 'visible';
			tabularpreview.style.display 			= '';
			tabularpreview.style.background			= '#ffffff';
			imageone.className 						= 'tabularbuttonoff';
			preview.className 						= 'tabularbuttonon';
			tabularlist.style.visibility		 	= 'hidden';
			tabularlist.style.display 				= 'none';
			tabularlist.style.background			= '#ffffff';
			previewSlideshow();
		}
		current_tab = identifier;
//		resizeDialogToContent();
	}
}
	
function get_images(){
	if (window.dialogArguments.document.cache_data.document.readyState=='complete'){
		if (window.dialogArguments.document.cache_data.frmDoc.image_data.value!=''){
			if (window.dialogArguments.document.cache_data.frmDoc.image_data.value!='__NOT_FOUND__'){
				tmp 			= new String(window.dialogArguments.document.cache_data.frmDoc.image_data.value);
				myArray 		= tmp.split("|1234567890|")
				l				= myArray.length-1;
				list="";
				document.image_browser.imagesrc.options.length=0
				for (i = 0;i<l;i+=2){
					document.image_browser.imagesrc.options[document.image_browser.imagesrc.options.length] = new Option(myArray[i], myArray[i+1]);
				}
			} else {
				alert("Sorry there are no images available");
			}
		} else {
			setTimeout("get_images()", 1000);
		}
	} else {
		setTimeout("get_images()", 1000);
	}
}
function previewImage(t){
	src = t.all.imagesrc.options[t.all.imagesrc.selectedIndex].value.split("::");
	t.all.imagepreview.src = base_href+src[0];
	size_value =src[4].split(" ");
	total_size=0;
	if (size_value[0]!='0'){
		if (size_value[1]=='MB'){
			total_size=size_value[0]*(1024*1024);
		}
		if (size_value[1]=='kb'){
			total_size=size_value[0]*1024;
		}
		if (size_value[1]=='bytes'){
			total_size=size_value[0];
		}
		speed_isdn= Math.ceil(total_size / 7000)+"";
		speed_56= Math.ceil(total_size / 5500)+"";
	}else{
		speed_isdn="";
		speed_56="";
	}
	str = "<strong>Width:</strong> <em> "+src[1]+" px</em>&#160;";
	str += "<strong>Height:</strong> <em>"+src[2]+" px</em><br>";
	str += "<strong>Size:</strong> <em>"+src[4]+"</em><br>";
	str += "<strong>Approximate Download Speeds:</strong><br>";
	str += "<em>56k:</em> "+speed_56+" <small>sec(s)</small>&#160;";
	str += "<em>ISDN:</em> "+speed_isdn+" <small>sec(s)</small>";
	t.all.imagesize.innerHTML = ""+str;
	tmp =src[3];
}

function addClick(i){
	if (document.image_browser.imagesrc.selectedIndex>0){
		src = document.image_browser.imagesrc.options[document.image_browser.imagesrc.selectedIndex].value.split("::");
		list_of_images[i] = src[0];
	}
}

function previewSlideshow(){
	sz = "<table>";
	sz += "<tr><td><img id='mouseover' src='" + base_href + list_of_images[0] +"' border=0 onmouseover=\"m_over(this, '" + base_href + list_of_images[1] +"');\" onmouseout=\"m_over(this,'" + base_href + list_of_images[0] +"');\"/></td>";
	sz += "</tr>";
	sz += "</table>";
	document.all['tabularpreview'].innerHTML=sz;
}

//-->
 </script>
<SCRIPT>
  function m_over(img, t){
    img.src=t;
  }
</SCRIPT>
</body>
</html>
