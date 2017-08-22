<?php 
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/10/28 14:04:13 $
	-	$Revision: 1.3 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_root.'class/lang.class.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';

function check_parameters($arr,$ind,$def=""){
	if (isset($arr[$ind])){
		return $arr[$ind];
	} else {
		return $def;
	}
}
//$wai_compliant = check_parameters($_GET,"wai_compliance","-1");
$colour_definition	= check_parameters($_GET,"colour_definition","false");
$session_url		= check_parameters($_GET,"PHPSESSID","");
$theme = empty($_GET['theme'])?$libertas_default_theme:$_GET['theme'];
$theme_path = $libertas_dir.'lib/themes/'.$theme.'/';

$l = new LIBERTAS_Lang($_GET['lang']);
$l->setBlock('table_cell_prop');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<meta http-equiv="Pragma" content="no-cache">
	<title>Libertas Solutions - - - - - - - - - - - - - - - - - - - - - - - - - </title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo LIBERTAS_LANG_CHARSET ?>">
	<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
</head>

<body onLoad="Init()" dir="ltr">
<P id=tableProps CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_table_cell_prop.gif'/> Table Cell Properties</P>
<table border="0" cellspacing="0" cellpadding="2" width="336">
<form name="td_prop">
<tr>
	<td colspan="2"><?php echo LOCALE_HORIZONTAL_ALIGN;?>:</td>
	<td colspan="2" align="right"><input type="hidden" name="chalign">
	<img id="ha_left" src="/libertas_images/editor/libertas/lib/themes/default/img/tb_left.gif" class="align_off" onClick="setHAlign('left');" alt="<?php echo $l->m('left')?>">
	<img id="ha_center" src="/libertas_images/editor/libertas/lib/themes/default/img/tb_center.gif" class="align_off" onClick="setHAlign('center');" alt="<?php echo $l->m('center')?>">
	<img id="ha_right" src="/libertas_images/editor/libertas/lib/themes/default/img/tb_right.gif" class="align_off" onClick="setHAlign('right');" alt="<?php echo $l->m('right')?>">
	</td>
</tr>
<tr>
	<td colspan="2"><?php echo LOCALE_VERTICAL_ALIGN;?>:</td>
	<td colspan="2" align="right"><input type="hidden" name="cvalign">
	<img id="ha_top" src="/libertas_images/editor/libertas/lib/themes/default/img/tb_top.gif" class="align_off" onClick="setVAlign('top');" alt="<?php echo $l->m('top')?>">
	<img id="ha_middle" src="/libertas_images/editor/libertas/lib/themes/default/img/tb_middle.gif" class="align_off" onClick="setVAlign('middle');" alt="<?php echo $l->m('middle')?>">
	<img id="ha_bottom" src="/libertas_images/editor/libertas/lib/themes/default/img/tb_bottom.gif" class="align_off" onClick="setVAlign('bottom');" alt="<?php echo $l->m('bottom')?>">
	<img id="ha_baseline" src="/libertas_images/editor/libertas/lib/themes/default/img/tb_baseline.gif" class="align_off" onClick="setVAlign('baseline');" alt="<?php echo $l->m('baseline')?>">
	</td>
</tr>
<tr>
	<td><?php echo LOCALE_WIDTH;?>:</td>
	<td nowrap>
		<input type="text" name="cwidth" size="3" maxlenght="3" class="input_small">%
		<input type="hidden" name="cwunits" value="%"/>
	</td>
	<td><?php echo LOCALE_HEIGHT;?>
:</td>
	<td nowrap>
		<input type="text" name="cheight" size="3" maxlenght="3" class="input_small">%
		<input type="hidden" name="chunits" value="%"/>
	</td>
</tr>
<!--<tr>
	<td nowrap>CSS:</td>
	<td nowrap colspan="3">
		<select id="ccssclass" name="ccssclass" size="1" class="input">
	<option value=orange>Orange</option>
	<option value=Purple>The colour Purple</option>
		</select>
	</td>
</tr>-->
<tr>
	<td colspan="2">&nbsp;</td>
</tr>
</table>
<table border="0" cellspacing="0" cellpadding="2" width="380" id="Advanced" style='visibility:hidden;display:none'>
<tr>
	<td colspan="4" width="380">
	<P width="95%" id=tableProps CLASS=tablePropsTitle>Advanced Options</P>
	<table width="95%" CELLSPACING="0" border="0">
		<tr>
			<td nowrap><?php echo LOCALE_NOWRAP;?>:</td>
			<td nowrap><input type="checkbox" name="cnowrap"></td>
			<td rowspan="2" colspan="2"><table id=colourPicker style='visibility:hidden;display:none'>
				<tr>
					<td><?php echo LOCALE_BACKGROUND_COLOUR;?>: </td><td colspan="3"><img src="/libertas_images/themes/1x1.gif" id="color_sample" border="1" width="30" height="22" align="top"><img id='colour_dropDown' name='colour_dropDown' src="/libertas_images/editor/libertas/lib/themes/default/img/tb_dropdown.gif" border="0" onClick="draw_palette(this)" align="top"><input type="hidden" name="cbgcolor"></td>
				</tr>
			</table></td>
		</tr>
		<tr>
			<td></td>
			<td></td>
		</tr>
	</table>
	</td>
</tr>
</table>
<table border="0" cellspacing="0" cellpadding="2" width="380" >
<tr>
<td colspan="4" nowrap>
<hr width="100%">
</td>
</tr>
<tr>
<td colspan="4" align="right" valign="bottom">
<input type="button" value="Advanced" onClick="toggleAdvancedClick(this)" class="bt">
<input type="button" value="<?php echo LOCALE_OK; ?>" onClick="okClick()" class="bt">
<input type="button" value="<?php echo LOCALE_CANCEL;?>" onClick="cancelClick()" class="bt">
</td>
</tr>
</form>
</table>
<div id='iFrmWindow' name='iFrmWindow' style='position:absolute;left:0;top:161'></div>
<script language="javascript" src="utils.js"></script>
<script language="javascript" src="../js/grid.js"></script>
<script language="javascript">
	<!--	
	var completed_loading = false;
	var CloseTableParameter ;
	var version;
	var oPopup;
	var session_url ='PHPSESSID=<?php print $session_url; ?>';
	var colour_definition = <?php print $colour_definition; ?>;

	//showColorPicker(cbgcolor.value)

	function draw_palette(sender){
		var grid 			= new Grid();	
		var myOptions		= new Array();
		var palette			= window.dialogArguments.palette;
		var product_version = window.dialogArguments.product_version;
		var browser_version	= window.dialogArguments.browser_version;
		version = browser_version;
		var browser_type 	= window.dialogArguments.browser_type;
		if(browser_version>5){
			PopupObject = window.createPopup();
			PopupObject.document.oncontextmenu = function(){return false;};
			oPopBody = PopupObject.document.body;
			oPopBody.style.backgroundColor	= "#ebebeb";
			oPopBody.style.border			= "solid #666666 1px";
			oPopup = PopupObject;
		}
		gridType= "color";
		editor=''
		for (i=0;i<palette.length;i++){
			myOptions[myOptions.length] = Array("'"+palette[i]+"'","LIBERTAS_set_color(\""+palette[i]+"\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
		}
		myOptions[myOptions.length] = Array("'#000000'","LIBERTAS_set_color(\"#000000\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
		myOptions[myOptions.length] = Array("'#333333'","LIBERTAS_set_color(\"#333333\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
		myOptions[myOptions.length] = Array("'#666666'","LIBERTAS_set_color(\"#666666\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
		myOptions[myOptions.length] = Array("'#999999'","LIBERTAS_set_color(\"#999999\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
		myOptions[myOptions.length] = Array("'#cccccc'","LIBERTAS_set_color(\"#cccccc\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
		myOptions[myOptions.length] = Array("'#FFFFFF'","LIBERTAS_set_color(\"#FFFFFF\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
		myOptions[-1] = Array('__REMOVE__',"LIBERTAS_set_color(\"__REMOVE__\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
		myOptions[-2] = Array('__REMOVE__',"LIBERTAS_set_color(\"__PICK__\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
		width=120;
		type="grid";
		sz  = "<table border='0' cellspacing='3' cellpadding='0' id='popupTable' name='popupTable' bgcolor='#ebebeb'>\n"
		sz += "<tr><td bgcolor='#ebebeb'>";
		this.grid = new Grid();
		for(index=0;index < myOptions.length;index++){
			this.grid.addItem(new gridItem(myOptions[index][0], myOptions[index][1], myOptions[index][3], myOptions[index][2], gridType));
		}
		this.grid.overwrite(new gridItem("", myOptions[-1][1], "", ""),-1);
		this.grid.overwrite(new gridItem("", myOptions[-2][1], "", ""),-2);
		sz += this.grid.draw();// = "Click outside <B>popup</B> to close.";
		l = myOptions.length + 1;
				width = 154;
		sz += "</td></tr>";
		if (product_version=="ECMS"){
		sz += "	<tr><td id='morecolour' name='morecolour' bgcolor='#ebebeb' style='height:20px;font-size:12px;text-align:center' onclick='javascript:parent.grid_execute_event(-2);parent.PopupObject.hide();event.cancelBubble=true;' onmouseover=\""
					+"morecolour.style.background='#9999cc';"
					+"morecolour.style.borderTop='1px solid #993399';"
					+"morecolour.style.borderRight='1px solid #993399';"
					+"morecolour.style.borderBottom='1px solid #993399';"
					+"morecolour.style.borderLeft='1px solid #993399';"
					+"\""
					+" onmouseout=\""
					+"morecolour.style.background='#ebebeb';"
					+"morecolour.style.borderTop='1px solid #ebebeb';"
					+"morecolour.style.borderRight='1px solid #ebebeb';"
					+"morecolour.style.borderBottom='1px solid #ebebeb';"
					+"morecolour.style.borderLeft='1px solid #ebebeb';"
					+"\" "
					+">More Colours ...</td></tr>";
		}
		sz += "</table>";
		height = (Math.round(l / 6)*33)+25;
		if(browser_version>5){
			oPopBody.innerHTML	= sz;// = "Click outside <B>popup</B> to close.";
			PopupObject.show(11-width,20,width,height,document.colour_dropDown);
		} else {
			onMenu(null,sender,sz,110,200,height,width);
		}
	}
		
	function LIBERTAS_set_color(colour){
		if ((colour!='__REMOVE__') && (colour!='__PICK__')){
			td_prop.cbgcolor.value = colour;
			td_prop.color_sample.style.backgroundColor = colour;
		} else {
			if (colour=='__REMOVE__'){
				td_prop.cbgcolor.value = '';
				td_prop.color_sample.style.backgroundColor = '';
			} else {
				showColorPicker(td_prop.cbgcolor.value);
			}
		}
	}

	function showColorPicker(curcolor) {
		var newcol = showModalDialog('colorpicker.php?theme=<?php echo $theme?>&lang=<?php echo $l->lang?>&'+session_url, curcolor, 
			'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');	
		try {
		if (newcol=="__REMOVE__"){
			td_prop.cbgcolor.value = newcol;
			td_prop.color_sample.style.backgroundColor = '#ffffff';
		} else {
			td_prop.cbgcolor.value = newcol;
			td_prop.color_sample.style.backgroundColor = td_prop.cbgcolor.value;
		}
		}
		catch (excp) {}
	}

	function Init() {
		var cProps = window.dialogArguments;
		if (colour_definition){
			document.all.colourPicker.style.visibility = 'visible';
			document.all.colourPicker.style.display = '';
		} 
		if (cProps){
			// set attribute values
			if (cProps.bgColor+""!="undefined"){
				if (cProps.bgColor.indexOf("#")!=-1){
					td_prop.cbgcolor.value = cProps.bgColor;
					td_prop.color_sample.style.backgroundColor = td_prop.cbgcolor.value;
				}
			}
			if (cProps.width) {
				if (!isNaN(cProps.width) || (cProps.width.substr(cProps.width.length-2,2).toLowerCase() == "px"))
				{
					// pixels
					if (!isNaN(cProps.width))
						td_prop.cwidth.value = cProps.width;
					else
						td_prop.cwidth.value = cProps.width.substr(0,cProps.width.length-2);
//					td_prop.cwunits.options[0].selected = false;
//					td_prop.cwunits.options[1].selected = true;
				}
				else
				{
					// percents
					td_prop.cwidth.value = cProps.width.substr(0,cProps.width.length-1);
//					td_prop.cwunits.options[0].selected = true;
//					td_prop.cwunits.options[1].selected = false;
				}
			}
			if (cProps.width) {
				if (!isNaN(cProps.height) || (cProps.height.substr(cProps.height.length-2,2).toLowerCase() == "px"))
				{
					// pixels
					if (!isNaN(cProps.height))
						td_prop.cheight.value = cProps.height;
					else
						td_prop.cheight.value = cProps.height.substr(0,cProps.height.length-2);
//					td_prop.chunits.options[0].selected = false;
//					td_prop.chunits.options[1].selected = true;
				}
				else
				{
					// percents
					td_prop.cheight.value = cProps.height.substr(0,cProps.height.length-1);
//					td_prop.chunits.options[0].selected = true;
//					td_prop.chunits.options[1].selected = false;
				}
			}
			
			setHAlign(cProps.align);
			setVAlign(cProps.vAlign);
			
			if (cProps.noWrap)
				td_prop.cnowrap.checked = true;
			
			
/*			if (cProps.styleOptions) {
				for (i=1; i<cProps.styleOptions.length; i++)
				{
					var oOption = document.createElement("OPTION");
					td_prop.ccssclass.add(oOption);
					oOption.innerText = cProps.styleOptions[i].innerText;
					oOption.value = cProps.styleOptions[i].value;
	
					if (cProps.className) {
						td_prop.ccssclass.value = cProps.className;
					}
				}
			}*/
		}
		resizeDialogToContent();
	}
	
	function validateParams()
	{
		// check width and height
		if (isNaN(parseInt(td_prop.cwidth.value)) && td_prop.cwidth.value != '')
		{
			alert('<?php echo 'Error: '.$l->m('error_width_nan')?>');
			td_prop.cwidth.focus();
			return false;
		}
		if (isNaN(parseInt(td_prop.cheight.value)) && td_prop.cheight.value != '')
		{
			alert('<?php echo 'Error: '.$l->m('error_height_nan')?>');
			td_prop.cheight.focus();
			return false;
		}
		
		return true;
	}
	
	function okClick() {
		// validate paramters
		if (validateParams()){
			var cprops = {};
			cprops.align = (td_prop.chalign.value)?(td_prop.chalign.value):'';
			cprops.vAlign = (td_prop.cvalign.value)?(td_prop.cvalign.value):'';
			cprops.width = (td_prop.cwidth.value)?(td_prop.cwidth.value + td_prop.cwunits.value):'';
			cprops.height = (td_prop.cheight.value)?(td_prop.cheight.value + td_prop.chunits.value):'';
			if (td_prop.cbgcolor.value!="__REMOVE__"){
				cprops.bgColor = td_prop.cbgcolor.value;
			} else {
				cprops.bgColor = null;
			}
//			cprops.className = (td_prop.ccssclass.value != 'default')?td_prop.ccssclass.value:'';
			cprops.noWrap = (td_prop.cnowrap.checked)?true:false;

			window.returnValue = cprops;
			window.close();
		}
	}

	function cancelClick() {
		window.close();
	}
	
	function setSample()
	{
		try {
			td_prop.color_sample.style.backgroundColor = td_prop.cbgcolor.value;
		}
		catch (excp) {}
	}
	
	function setHAlign(alignment)
	{
		switch (alignment) {
			case "left":
				td_prop.ha_left.className = "align_on";
				td_prop.ha_center.className = "align_off";
				td_prop.ha_right.className = "align_off";
				break;
			case "center":
				td_prop.ha_left.className = "align_off";
				td_prop.ha_center.className = "align_on";
				td_prop.ha_right.className = "align_off";
				break;
			case "right":
				td_prop.ha_left.className = "align_off";
				td_prop.ha_center.className = "align_off";
				td_prop.ha_right.className = "align_on";
				break;
		}
		td_prop.chalign.value = alignment;
	}

	function setVAlign(alignment)
	{
		switch (alignment) {
			case "middle":
				td_prop.ha_middle.className = "align_on";
				td_prop.ha_baseline.className = "align_off";
				td_prop.ha_bottom.className = "align_off";
				td_prop.ha_top.className = "align_off";
				break;
			case "baseline":
				td_prop.ha_middle.className = "align_off";
				td_prop.ha_baseline.className = "align_on";
				td_prop.ha_bottom.className = "align_off";
				td_prop.ha_top.className = "align_off";
				break;
			case "bottom":
				td_prop.ha_middle.className = "align_off";
				td_prop.ha_baseline.className = "align_off";
				td_prop.ha_bottom.className = "align_on";
				td_prop.ha_top.className = "align_off";
				break;
			case "top":
				td_prop.ha_middle.className = "align_off";
				td_prop.ha_baseline.className = "align_off";
				td_prop.ha_bottom.className = "align_off";
				td_prop.ha_top.className = "align_on";
				break;
		}
		td_prop.cvalign.value = alignment;
	}

	function toggleAdvancedClick(t){
		if (t.value=="Advanced"){
			t.value="Basic";
			Advanced.style.display='';
			Advanced.style.visibility='visible';
		} else {
			t.value="Advanced";
			Advanced.style.display='none';
			Advanced.style.visibility='hidden';
		}
		resizeDialogToContent();
	}

function onMenu(editor, sender, myData, overRideX, overRideY, overRideHeight, overRideWidth) {
	for (x=0;x<table_prop.elements.length;x++){
		if (table_prop.elements[x].tagName == 'SELECT'){
			table_prop.elements[x].style.visible = 'hidden';
			table_prop.elements[x].style.display = 'none';
		}
	}
	var str = "<div id=\"tblsel\" style=\"position:absolute;z-index:-1;border:1px solid #666666;background-color:#ebebeb;\">";
		str += myData;
		str += "</div>";
	var ifrm = document.getElementById("iFrmWindow");
	
	var x=0;
	var y=0;
	ifrm.innerHTML			= str;
	ifrm.style.visibility	= "visible";
	ifrm.style.display 		= "";
	ifrm.onmouseout			= startTimeTable;	
	ifrm.onmouseover		= endTimeTable;	
	ifrm.style.position 	= 'absolute';
	ifrm.style.left 		= 175;
	ifrm.style.top 			= 210;
	ifrm.oncontextmenu 		= function(){return false;}
	ifrm.onselectstart 		= new Function("return false;");
	if (sender!=null){
		event.cancelBubble	= true;
	} else {
	}
}
function cancelTable(a) {

	document.onmousedown=null;
	var ifrm = document.getElementById("iFrmWindow");
	ifrm.style.visibility = "hidden";
	ifrm.style.display = "none";

	ifrm.style.pixelWidth = 0;
	ifrm.style.pixelHeight = 0;

	if(a==false) return;

	if(typeof(ae_olddocmd)=="function") {
		ae_olddocmd(false);
		document.onmousedown = ae_olddocmd;
	}
	ae_olddocmd = null;

	//Set DropDownTable IFrame to small
	ifrm.style.pixelWidth = 10;
	ifrm.style.pixelHeight = 10;
	for (x=0;x<td_prop.elements.length;x++){
		if (td_prop.elements[x].tagName == 'SELECT'){
			td_prop.elements[x].style.visible = 'visible';
			td_prop.elements[x].style.display = '';
		}
	}
}
function startTimeTable(){
	clearTimeout(CloseTableParameter);
	CloseTableParameter = setTimeout("parent.close_the_dropdown()",2000);
}
function endTimeTable(){
	clearTimeout(CloseTableParameter);
}
function close_the_dropdown(){
	clearTimeout(CloseTableParameter);
	cancelTable();
}
	//-->
	</script>
</body>
</html>
