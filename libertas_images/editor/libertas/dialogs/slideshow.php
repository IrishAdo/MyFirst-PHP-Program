<?php 
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/09/07 07:28:31 $
	-	$Revision: 1.4 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';

$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$domain 	= $_SERVER["HTTP_HOST"];
$base_href 	= "http://$domain".check_parameters($_GET,"base_href","/");

function check_parameters($parameters,$name,$default=""){
	if (isset($parameters[$name])){
		$value = $parameters[$name];
	} else {
		$value = $default;
	}
	return $value;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<meta http-equiv="Pragma" content="no-cache">
	<title>Libertas Solutions - - - - - - - - - - - - - - - - - - - - - - - - - </title>
	<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
</head>

<body onLoad="Init()" dir="ltr">
	<iframe src='about:blank' width='0' height='0' style='visibility:hidden' id='extract_cache2' name='extract_cache2'></iframe>
<div style="border: 1 solid Black; padding: 5 5 5 5;background-color:white">
<P id=tableProps CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_slideshow.gif'/> Slideshow Manager</P>
<form name="image_browser">
<table cellspacing=0 cellpadding=3 width="500">
<tr>
	<td id='managelist' class='tabularbuttonleft' width="50%" onClick="show('managelist');">Manage List</td>
	<td id='manageproperties' class='tabularbuttonright' width="50%" onClick="show('manageproperties');">Properties</td>
	<td id='preview' class='tabularbuttonright' width="50%" onClick="show('preview');">Preview</td></tr>
	<tr><td colspan='3' id='tabularlist' class='tabularcontent'><table>
		<tr><td><strong>Alternative text</strong></td><td colspan='3'><input name='imagealt' size='40' maxlength='255' value =''></td></tr>
		<tr>
			<td><strong>Select Image</strong></td>
			<td><select id='imagesrc' name='imagesrc' style='width:230px' onChange='parent.previewImage(this.document);'>
					<option>Please wait while I download the list of images.</option>
				</select></td>
			</tr>
<tr><td><img border="1" src='/libertas_images/themes/1x1.gif' width="130" height="130" id='imagepreview'><br/><input type="button" value="Add to Slideshow" onClick="addClick()" class="bt" style="width:130px"></td><td><span id='imagesize'><strong>Width:</strong> <em>X px</em>&#160;<strong>Height:</strong> <em>Y px</em><br>
<strong>Size:</strong><br>
<strong>Approximate Download Speeds:</strong><br>
<em>56k:</em> X secs &#160;<em>ISDN:</em> X sec</span><br/></td></tr>
	</table>
	</td></tr>
	<tr><td colspan='3' id='tabularpreview' class='tabularcontent' style="visibility:hidden;display:none"></td></tr>
	<tr><td colspan='3' id='tabularproperties' class='tabularcontent' style="visibility:hidden;display:none"><table>
	<tr><td><strong>Time Delay</strong></td><td><select name='time_delay'>
	<option value='1'>1 second</option>
	<option value='2'>2 seconds</option>
	<option value='3'>3 seconds</option>
	<option value='4'>4 seconds</option>
	<option value='5'>5 seconds</option>
	<option value='6'>6 seconds</option>
	<option value='7'>7 seconds</option>
	<option value='8'>8 seconds</option>
	<option value='9'>9 seconds</option>
	<option value='10'>10 seconds</option>
	</select></td><td><strong>Alignment</strong></td><td><select name='imagealignment'>
	<option value=''>None</option>
	<option value='left'>Left</option>
	<option value='right'>Right</option>
	</select></td></tr>
	<tr><td><strong>Width</strong></td><td><input name='imagewidth' size='4' maxlength='4' value =''></td>
	<td><strong>Height</strong></td><td><input name='imageheight' size='4' maxlength='4' value =''></td></tr>
</table>
	</td></tr>
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
var list_of_slideshow_images = Array();
var list_of_slideshow_titles = Array();
var winOpener = window.dialogArguments.document.parentWindow;

var session_url ='<?php print $session_url; ?>';
var base_href = "<?php print $base_href;?>";
var cur_color; // passed color
var current_tab = 'web';
var original ;

	/* Inorder to cope with IE security restrictions this patch is added */
	iframeloc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&"+session_url;	
	extract_cache2.location.href= iframeloc;

	 /*IE7 security restrictions would not allow */
	//winOpener.__extract_information('image');

	setTimeout("get_images()",1000);


function Init() {
    args = window.dialogArguments;
	original = window.dialogArguments.original;
	if (original+''!='undefined'){
//	alert(myfield)
		myfield = original.time_delay+'';
		original.time_delay	= set_field_default(myfield, 3);
		myfield = original.alt+'';
		original.alt		= set_field_default(myfield+'', '');
		myfield = original.align+'';
		original.align		= set_field_default(myfield+'', '');
		myfield = original.width+'';
		original.width		= set_field_default(myfield+'', -1);
		myfield = original.height+'';
		original.height		= set_field_default(myfield+'', -1);
		checkid = original.id+''
		if (checkid.substring(0,9)=='slideshow'){
			if (original.parameters.length>0){
				if (original.parameters.indexOf("::")>0){
					list_of_slideshow_images = original.parameters.split("::");
					list_of_slideshow_titles = original.paramtitle.split("::");
				} else {
					list_of_slideshow_images[list_of_slideshow_images.length] = original.parameters;
					list_of_slideshow_titles[list_of_slideshow_titles.length] = original.paramtitle;
				}
			}
		}
	} else {
		original = {};
		original.time_delay	= 5;
		original.align		= '';
		original.alt		= '';
		original.width		= -1;
		original.height		= -1;
	}
	document.image_browser.time_delay.options[original.time_delay-1].selected= true;
	if(""==original.align){
		document.image_browser.imagealignment.options[0].selected= true;
	}
	if("left"==original.align){
		document.image_browser.imagealignment.options[1].selected= true;
	}
	if("right"==original.align){
		document.image_browser.imagealignment.options[2].selected= true;
	}
	document.image_browser.imagealt.value	= original.alt+'';
	document.image_browser.imagewidth.value	= original.width+'';
	document.image_browser.imageheight.value= original.height+'';
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
	returnObj.alt 							=	new String(escape(document.image_browser.imagealt.value)).split("%20").join(" ").split('%22').join('&#34;').split('%27').join('&#39;');
	returnObj.time_delay	 				=	document.image_browser.time_delay.options[document.image_browser.time_delay.selectedIndex].value;
	if (document.image_browser.imagealignment.selectedIndex!=0){
		returnObj.styleDefinitionfloat 		= document.all.imagealignment.options[document.image_browser.imagealignment.selectedIndex].value;
	} else {
		returnObj.styleDefinitionfloat 		= "";
	}
	returnObj.width 						= "";
	returnObj.height				 		= "";
	if (document.image_browser.imagewidth.value>0 && document.image_browser.imageheight.value>0 ){
		returnObj.width 					= document.image_browser.imagewidth.value;
		returnObj.height 					= document.image_browser.imageheight.value;
	}
	returnObj.styleDefinitionmarginleft 	=	"5px";
	returnObj.styleDefinitionmarginright	=	"5px";
	returnObj.styleDefinitionmargintop		=	"5px";
	returnObj.styleDefinitionmarginbottom	=	"5px";

//alert(list_of_slideshow_images[0]+'mg');
//alert(list_of_slideshow_titles[0]+'ttl');
/*
	list_of_slideshow_images_file = list_of_slideshow_images[0].split("::");
	list_of_slideshow_images[0] = list_of_slideshow_images_file[0]; 
	list_of_slideshow_images[1] = list_of_slideshow_images_file[1]; 
	alert(list_of_slideshow_images[0]);
	alert(list_of_slideshow_images[1]);
*/
	returnObj.source						=	base_href+list_of_slideshow_images[0];
	returnObj.parameters					=	list_of_slideshow_images.join("::");
	returnObj.paramtitle					=	list_of_slideshow_titles.join("::");
	
	//alert(returnObj.parameters+'parm_mg');
	//alert(returnObj.paramtitle+'parm_title');
	
	if (args.original+''!='undefined'){
		checkid = original.id+''
		if (checkid.substring(0,9)=='slideshow'){
			if (list_of_slideshow_images.length>0){
				returnObj.id				=	original.id;
				returnObj.type				=	'update';
			} else {
				returnObj 					=	"null"; // blank it 
			}
		} 
	} else {
		if (list_of_slideshow_images.length>0){
			returnObj.type					=	'insert';
			d = new Date();
			returnObj.id					=	"slideshow"+d.getTime()+"_"+random_digits(3);
		} else {
			returnObj 						=	"null";
		}
	}
//	imageitem.source 						=	base_href+list_of_images[0];
//	imageitem.longdesc						=	"?command=FILES_INFO&identifier="+md5;
	window.returnValue = returnObj;
    window.close();
}

function cancelClick() {
	window.returnValue = "null";
    window.close();
}
  
  
function show(identifier){
 	if(identifier!=current_tab){
		if(identifier=='managelist'){
			tabularpreview.style.visibility 		= 'hidden';
			tabularpreview.style.display 			= 'none';
			tabularpreview.style.background			= '#ffffff';
			managelist.className 					= 'tabularbuttonon';
			manageproperties.className 				= 'tabularbuttonoff';
			preview.className 						= 'tabularbuttonoff';
			tabularlist.style.visibility		 	= 'visible';
			tabularlist.style.display 				= '';
			tabularlist.style.background			= '#ffffff';
			tabularproperties.style.visibility		= 'hidden';
			tabularproperties.style.display 		= 'none';
			tabularproperties.style.background		= '#ffffff';
		} else if(identifier=='manageproperties'){
			tabularpreview.style.visibility 		= 'hidden';
			tabularpreview.style.display 			= 'none';
			tabularpreview.style.background			= '#ffffff';
			tabularlist.style.visibility		 	= 'hidden';
			tabularlist.style.display 				= 'none';
			tabularlist.style.background			= '#ffffff';
			managelist.className 					= 'tabularbuttonoff';
			manageproperties.className 				= 'tabularbuttonon';
			preview.className 						= 'tabularbuttonoff';
			tabularproperties.style.visibility		 	= 'visible';
			tabularproperties.style.display 				= '';
			tabularproperties.style.background			= '#ffffff';
//			show_properties();
		}else {
			tabularproperties.style.visibility		= 'hidden';
			tabularproperties.style.display 		= 'none';
			tabularproperties.style.background		= '#ffffff';
			tabularpreview.style.visibility 		= 'visible';
			tabularpreview.style.display 			= '';
			tabularpreview.style.background			= '#ffffff';
			managelist.className 					= 'tabularbuttonoff';
			manageproperties.className 				= 'tabularbuttonoff';
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
					document.image_browser.imagesrc.options[document.image_browser.imagesrc.options.length] = new Option(myArray[i], myArray[i+1]+'::'+myArray[i]);
//				alert('img:'+myArray[i+1]);
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

//alert(t.all.imagesrc.options[t.all.imagesrc.selectedIndex].value);

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

function addClick(){
//alert(document.image_browser.imagesrc.options[document.image_browser.imagesrc.selectedIndex].value);

	if (document.image_browser.imagesrc.selectedIndex>0){
		src = document.image_browser.imagesrc.options[document.image_browser.imagesrc.selectedIndex].value.split("::");
		found = false;
		for (var i =0; i<list_of_slideshow_images.length;i++){
			if (list_of_slideshow_images[i] == src[0]){
				found=true;
			}
		}
		if (!found){
			list_of_slideshow_images[list_of_slideshow_images.length] = src[0];
			list_of_slideshow_titles[list_of_slideshow_titles.length] = src[6];
			
			if (list_of_slideshow_images.length==1){
				original.width								= src[1];
				original.height								= src[2];
				document.image_browser.imagewidth.value		= src[1];
				document.image_browser.imageheight.value	= src[2];
			}
		}
	}
}

function previewSlideshow(){
	showRank = true;
	if (document.image_browser.switchnode+''=='undefined'){
		showRank= true;
	} else {
		if (document.image_browser.switchnode[0].checked){
			showRank= true;
		} else {
			showRank=false;
		}
	}
	sz = "<input type=radio name=switchnode id=switchnode1 onclick='javascript:previewSlideshow()' value='rank' ";
	if (showRank){
		sz += "checked";
	}
	sz += "> <label for=switchnode1>Configure Ranking</label> ";
	sz += "<input type=radio name=switchnode id=switchnode2 onclick='javascript:previewSlideshow()' value='preview'";
	if (!showRank){
		sz += "checked";
	}
	sz += "> <label for=switchnode2>Watch Preview</label> ";
	sz += "<table>";
	if (showRank){
		if (list_of_slideshow_images.length>0){
			for (var i =0; i<list_of_slideshow_images.length;i++){
				sz += "<tr><td><img src='" + base_href + list_of_slideshow_images[i] +"' width='"+original.width+"' height='"+original.width+"' border=0/></td><td valign=middle><input type=button class=bt onclick='javascript:rm("+i+");' value='Remove'/></td>";
				if(i>0)
					sz += "<td><input type=button class=bt onclick='javascript:mv("+i+",-1);' value='Up' style='width:50px'/></td>";
				else 
						sz += "<td width='50'>&nbsp;</td>";
				
				if (i<list_of_slideshow_images.length-1)
					sz += "<td><input type=button class=bt onclick='javascript:mv("+i+",1);' value='Down' style='width:50px'/></td>";
				else 
					sz += "<td width='50'>&nbsp;</td>";
				sz += "</tr>";

			//	sz += "<tr><td><div id=ttl_layer><div></td></tr>";
			}
		} else {
			sz += "<tr><td>Sorry there is currently no images choosen for the slide show.</td></tr>";
		}
	} else {
		if (list_of_slideshow_images.length>0){
			image_index = -1;
			slides[0] = new SlideShow('slideshow_image', 'null', 0,image_index );
			for (var img_index =0; img_index<list_of_slideshow_titles.length;img_index++){
				slides[0].imageList[slides[0].imageList.length] = base_href + list_of_slideshow_images[img_index] ;
				slides[0].imageListTitle[slides[0].imageListTitle.length] = list_of_slideshow_titles[img_index] ;
			}

			slides[0].cache_images();
			sz += "<tr><td><img src='/libertas_images/themes/1x1.gif' width='"+original.width+"' height='"+original.width+"' id=slideshow_image /></td></tr>";
//				sz += "<tr><td><div id=ttl_layer><div></td></tr>";
				sz += "<tr><td><div id=ttl_layer><div></td></tr>";
		} else {
			sz += "<tr><td>Sorry there is currently no images choosen for the slide show.</td></tr>";
		}
	
	}
	sz += "</table>";
	document.all['tabularpreview'].innerHTML=sz;
	if (!showRank && list_of_slideshow_images.length>0){
		for (index=0;index<document.images.length;index++){
				if (document.images[index].id=="slideshow_image"){
					image_index = index;
				}
		}
		if (image_index>-1){
			slides[0].DOMimageIndex = image_index;
			slides[0].start();
		} else {
			alert("Could not find preview image in HTML");
		}
	}
}

function rm(index){
	if (index < list_of_slideshow_images.length){
		list_of_slideshow_images.splice(index,1);
		previewSlideshow();
	}
}

function mv(index,dir){
	if (dir<0){
		if (index>0){
			tmp = list_of_slideshow_images[index];
			list_of_slideshow_images[index] = list_of_slideshow_images[index-1];
			list_of_slideshow_images[index-1] = tmp;
		}
	}
	if (dir>0){
		if (index<(list_of_slideshow_images.length-1)){
			tmp = list_of_slideshow_images[index];
			list_of_slideshow_images[index] = list_of_slideshow_images[index+1];
			list_of_slideshow_images[index+1] = tmp;

		}
	}
	previewSlideshow();
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
