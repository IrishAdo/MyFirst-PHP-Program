<?php 
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/10/28 14:04:13 $
	-	$Revision: 1.5 $
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

$wai_compliant 		= check_parameters($_GET,"wai_compliance","-1");
$colour_definition	= check_parameters($_GET,"colour_definition","false");
$session_url		= check_parameters($_GET,"PHPSESSID","");
$theme 				= check_parameters($_GET,"theme",$libertas_default_theme);
$theme_path 		= $libertas_dir.'lib/themes/'.$theme.'/';

$l = new LIBERTAS_Lang(check_parameters($_GET,'lang','en'));
$l->setBlock('table_prop');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head id="headofpage">
	<meta http-equiv="Pragma" content="no-cache">
	<title>Table Manager - - - - - - - - - - - - - - - - - - - - - - - - - - - - - </title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo LIBERTAS_LANG_CHARSET ?>">
	<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
	
</head>

<body onLoad="Init()" dir="ltr">
<P id=tableProps width="90%" CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_table_create.gif' alt='Table creation'/> Add New Table</P>
<form name="table_prop">
<table border="0" cellspacing="0" cellpadding="2" width="380">
<tr>
	<td valign="top" colspan="4">Table Summary:</td>
</tr>
<tr>
	<td colspan="4"><textarea name="tsum" rows="5" cols="40" style='width:380px'></textarea></td>
</tr>
<tr>
	<td valign="top" colspan="4">Table Caption:</td>
</tr>
<tr>
	<td colspan="4"><textarea name="tcaption" rows="2" cols="40" style='width:380px'></textarea></td>
</tr>

<tr>
	<td><?php echo LOCALE_ROWS;?>:</td>
	<td ><input type="text" name="trows" size="3" maxlenght="3" class="input_small"></td>
	<td><?php echo LOCALE_COLUMNS;?>:</td>
	<td ><input type="text" name="tcols" size="3" maxlenght="3" class="input_small"></td>
</tr>
<tr>
	<td>Border:</td>
	<td ><input name="tborder" size="3" maxlength="3" class="input_small" value="0"></td>
			<td>Table header:</td>
			<td ><select name='tableScope' id='tableScope' class="input">
			<option value=''>No Headers</option>
			<option value='col'>First Row</option>
			<option value='row'>First Column</option>
		</select></td></tr>
</table>
<table border="0" cellspacing="0" cellpadding="2" width="380" id="Advanced" style='visibility:hidden;display:none'>
<tr>
	<td colspan="4" width="380">
	<P width="95%" id=tableProps1 CLASS=tablePropsTitle>Advanced Options</P>
	<table width="100%" CELLSPACING="0" cellpadding="0" border="0">
<tr>
	<td><?php echo LOCALE_ALIGN;?>:</td>
	<td><select name="talign" size="1" class="input">
		<option value=""><?php echo LOCALE_CHOOSE_ONE;?></option>
		<option value="left"><?php echo LOCALE_LEFT;?></option>
		<option value="center"><?php echo LOCALE_MIDDLE;?></option>
		<option value="right"><?php echo LOCALE_RIGHT;?></option>
	</select></td>

</tr>
		<tr>
			<td width="100px">CellSpacing:</td>
			<td><input type="text" name="tcspc" size="3" maxlenght="3" class="input_small"></td>
			<td rowspan="2" colspan="2"><table id=colourPicker style='visibility:hidden;display:none'>
				<tr>
					<td><?php echo LOCALE_BACKGROUND_COLOUR;?>: </td><td colspan="3"><img src="/libertas_images/themes/1x1.gif" id="color_sample" border="1" width="30" height="22" align="top"><img id='colour_dropDown' name='colour_dropDown' src="/libertas_images/editor/libertas/lib/themes/default/img/tb_dropdown.gif" border="0" onClick="draw_palette(this)" align="top"><input type="hidden" name="tbgcolor"></td>
				</tr>
			</table></td>
		</tr>
		<tr>
			<td>CellPadding:</td>
			<td><input type="text" name="tcpad" size="3" maxlenght="3" class="input_small"></td>
		</tr>
		<tr>
		<td>Width:</td><td>
			<input type="text" name="twidth" value="100" size="3" maxlenght="3" class="input_small"><select name="twunits" class="input" style="width:40px">
				<option value="%" selected>%</option>
				<option value="px">px</option>
			</select>
		</td>
		<td style="display:none">Height:</td><td style="display:none">
			<input type="text" name="theight" value="" size="3" maxlenght="3" class="input_small"><select name="thunits" class="input" style="width:40px">
				<option value="%">%</option>
				<option value="px">px</option>
			</select>
		</td>
		</tr>
<tr>
	<td colspan="2">Display this caption:</td>
	<td colspan="2"><select id="caption_position" name="caption_position">
		<option value='bottom'>Display below the table</option>
		<option value='top'>Display above the table</option>
	</select></td>
</tr>
	</table>
	</td>
</tr>
</table>
<table border="0" cellspacing="0" cellpadding="2" width="380" >
<tr>
	<td colspan="4" nowrap><hr width="100%"></td>
</tr>
<tr>
<td colspan="4" align="right" valign="bottom" nowrap id="buttonlist" style="display:none">
<input type="button" value="Advanced" onClick="toggleAdvancedClick(this)" class="bt">
<input type="button" value="<?php echo LOCALE_OK; ?>" onClick="okClick()" class="bt">
<input type="button" value="<?php echo LOCALE_CANCEL;?>" onClick="cancelClick()" class="bt">
</td>
</tr>
</table>
</form>
<div id='iFrmWindow' name='iFrmWindow' style='position:absolute;left:0;top:161;width:0px;display:none;'></div>
	<script language="javascript" src="utils.js"></script>
	<script language="javascript" src="../js/grid.js"></script>
<script language="javascript">
//showColorPicker(tbgcolor.value)
var completed_loading = false;
var CloseTableParameter ;
var version;
var oPopup;
	function Init() {
		var tProps = window.dialogArguments;
		if (colour_definition){
			document.all.colourPicker.style.visibility = 'visible';
			document.all.colourPicker.style.display = '';
		} 
		if (tProps.rows){
			tableProps.innerHTML ="<img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_table_prop.gif'/> Edit Table"
			// set attribute values
			table_prop.tcaption.value = tProps.captiontxt.split("&amp;").join("&").split("&nbsp;").join(" ");
			if(tProps.captionalign==''){
				table_prop.caption_position.options[0].selected=false;
				table_prop.caption_position.options[1].selected=true;
			} else if(tProps.captionalign=='bottom'){
				table_prop.caption_position.options[0].selected=true;
				table_prop.caption_position.options[1].selected=false;
			}
			table_prop.trows.value = tProps.rows;
			table_prop.trows.disabled = true;
			table_prop.tcols.value = tProps.columns;
			table_prop.tcols.disabled = true;

			table_prop.tborder.value = tProps.border;
			table_prop.tcpad.value = tProps.cellPadding;
			table_prop.tcspc.value = tProps.cellSpacing;
			table_prop.tsum.value = tProps.summary;
			if (colour_definition){
				if (tProps.bgColor+""!="undefined"){
					if (tProps.bgColor.indexOf("#")!=-1){
						table_prop.tbgcolor.value = tProps.bgColor;
						if (tProps.bgColor)
							table_prop.color_sample.style.backgroundColor = table_prop.tbgcolor.value;
						colourPicker.style.display='';
						colourPicker.style.visibility='visible';
					}
				}
			} else {
				colourPicker.style.display='none';
				colourPicker.style.visibility='hidden';
			}
			if (tProps.scope) {
				if (tProps.scope=='') {
					table_prop.tableScope.options[0].selected=true;
					table_prop.tableScope.options[1].selected=false;
					table_prop.tableScope.options[2].selected=false;
				}
				if (tProps.scope=='col') {
					table_prop.tableScope.options[0].selected=false;
					table_prop.tableScope.options[1].selected=true;
					table_prop.tableScope.options[2].selected=false;
				}
				if (tProps.scope=='row') {
					table_prop.tableScope.options[0].selected=false;
					table_prop.tableScope.options[1].selected=false;
					table_prop.tableScope.options[2].selected=true;
				}
			}

			if (tProps.align) {
				if(tProps.align==''){
					table_prop.talign.options[0].selected=true;
					table_prop.talign.options[1].selected=false;
					table_prop.talign.options[2].selected=false;
					table_prop.talign.options[3].selected=false;
					table_prop.talign.selectedIndex=0;
				}
				if(tProps.align=='left'){
					table_prop.talign.options[0].selected=false;
					table_prop.talign.options[1].selected=true;
					table_prop.talign.options[2].selected=false;
					table_prop.talign.options[3].selected=false;
					table_prop.talign.selectedIndex=1;
				}
				if(tProps.align=='center'){
					table_prop.talign.options[2].selected=true;
					table_prop.talign.options[1].selected=false;
					table_prop.talign.options[0].selected=false;
					table_prop.talign.options[3].selected=false;
					table_prop.talign.selectedIndex=2;
				}
				if(tProps.align=='right'){
					table_prop.talign.options[3].selected=true;
					table_prop.talign.options[2].selected=false;
					table_prop.talign.options[1].selected=false;
					table_prop.talign.options[0].selected=false;
					table_prop.talign.selectedIndex=3;
				}
			}

			if (tProps.width) {
				if (!isNaN(tProps.width) || (tProps.width.substr(tProps.width.length-2,2).toLowerCase() == "px")){
					// pixels
					if (!isNaN(tProps.width))
						table_prop.twidth.value = tProps.width;
					else
						table_prop.twidth.value = tProps.width.substr(0,tProps.width.length-2);
					table_prop.twunits.options[0].selected = false;
					table_prop.twunits.options[1].selected = true;
				} else {
					 // percents
					table_prop.twidth.value = tProps.width.substr(0,tProps.width.length-1);
					table_prop.twunits.options[0].selected = true;
					table_prop.twunits.options[1].selected = false;
				}
			}
			if (tProps.height) {
				if (!isNaN(tProps.height) || (tProps.height.substr(tProps.height.length-2,2).toLowerCase() == "px")){
					// pixels
					if (!isNaN(tProps.height))
						table_prop.theight.value = tProps.height;
					else
						table_prop.theight.value = tProps.height.substr(0,tProps.height.length-2);
					table_prop.thunits.options[0].selected = false;
					table_prop.thunits.options[1].selected = true;
				} else {
					// percents
					table_prop.theight.value = tProps.height.substr(0,tProps.height.length-1);
					table_prop.thunits.options[0].selected = true;
					table_prop.thunits.options[1].selected = false;
				}
			}
		} else {
			// set default values
			table_prop.twidth.value 	= '100'
			table_prop.twunits.value	='%';
			table_prop.thunits.value	='%';
			
			if (tProps.createRows){
				table_prop.trows.value 		= tProps.createRows;
				table_prop.tcols.value 		= tProps.createCols;
			} else {
				table_prop.trows.value 		= '3';
				table_prop.tcols.value 		= '3';
			}
			table_prop.tcspc.value 		= '1';
			table_prop.tcpad.value 		= '3';
			table_prop.tborder.value 	= '0';
			table_prop.tableScope.options[0].selected =false;
			table_prop.tableScope.options[1].selected =true;
			table_prop.tableScope.options[2].selected =false;
		}
		h=370;
		resizeDialogToContent(426,h);
		completed_loading = true;
		buttonlist.style.display='';
	}

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
		sz  = "<table border='0' cellspacing='0' cellpadding='0' id='popupTable' name='popupTable' bgcolor='#ebebeb'>\n"
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
			if(browser_version>5){
				str ="javascript:parent.grid_execute_event(-2);parent.PopupObject.hide();event.cancelBubble=true;";
			} else {
				str ="javascript:parent.grid_execute_event(-2);parent.cancelTable();event.cancelBubble=true;";
			}
			sz += "	<tr><td id='morecolour' name='morecolour' bgcolor='#ebebeb' style='height:20px;font-size:12px;text-align:center' onclick='"+str+"' onmouseover=\""
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
			table_prop.tbgcolor.value = colour;
			table_prop.color_sample.style.backgroundColor = colour;
		} else {
			if (colour=='__REMOVE__'){
				table_prop.tbgcolor.value = '';
				table_prop.color_sample.style.backgroundColor = '';
			} else {
				showColorPicker(table_prop.tbgcolor.value);
			}
		}
	}
	function toggleAdvancedClick(t){
		if (t.value=="Advanced"){
			t.value="Basic";
			Advanced.style.display='';
			Advanced.style.visibility='visible';
			h = 503;
		} else {
			t.value="Advanced";
			Advanced.style.display='none';
			Advanced.style.visibility='hidden';
			h = 375;
		}
		resizeDialogToContent(426,h);
	}
	
	var session_url ='PHPSESSID=<?php print $session_url; ?>';
	var colour_definition = <?php print $colour_definition; ?>;
	function showColorPicker(curcolor) {
			var newcol = showModalDialog('colorpicker.php?theme=<?php echo $theme?>&lang=<?php echo $l->lang?>&'+session_url, curcolor, 
					'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');	
			try {
				if (newcol=="__REMOVE__"){
					table_prop.tbgcolor.value = newcol;
					table_prop.color_sample.style.backgroundColor = '#ffffff';
				} else {
					table_prop.tbgcolor.value = newcol;
					table_prop.color_sample.style.backgroundColor = table_prop.tbgcolor.value;
				}
	 		}catch (excp) {
			}
		}



	function validateParams(){
		// check whether rows and cols are integers
		if (isNaN(parseInt(table_prop.trows.value))){
			alert('<?php echo 'Error: '.$l->m('error_rows_nan')?>');
			table_prop.trows.focus();
			return false;
		}
		if (isNaN(parseInt(table_prop.tcols.value))){
			alert('<?php echo 'Error: '.$l->m('error_columns_nan')?>');
			table_prop.tcols.focus();
			return false;
		}
		// check width and height
		if (isNaN(parseInt(table_prop.twidth.value)) && table_prop.twidth.value != ''){
			alert('<?php echo 'Error: '.$l->m('error_width_nan')?>');
			table_prop.twidth.focus();
			return false;
		}
		if (isNaN(parseInt(table_prop.theight.value)) && table_prop.theight.value != ''){
			alert('<?php echo 'Error: '.$l->m('error_height_nan')?>');
			table_prop.theight.focus();
			return false;
		}
		// check border, padding and spacing
		if (isNaN(parseInt(table_prop.tborder.value)) && table_prop.tborder.value != ''){
			alert('<?php echo 'Error: '.$l->m('error_border_nan')?>');
			table_prop.tborder.focus();
			return false;
		}
		if (isNaN(parseInt(table_prop.tcpad.value)) && table_prop.tcpad.value != ''){
			alert('<?php echo 'Error: '.$l->m('error_cellpadding_nan')?>');
			table_prop.tcpad.focus();
			return false;
		}
		if (isNaN(parseInt(table_prop.tcspc.value)) && table_prop.tcspc.value != ''){
			alert('<?php echo 'Error: '.$l->m('error_cellspacing_nan')?>');
			table_prop.tcspc.focus();
			return false;
		}
		if (table_prop.tsum.value == ''){
			alert('<?php echo 'You need to supply a short summary for this table for WAI compliance';?>');
			table_prop.tsum.focus();
			return false;
		}
		return true;
	}

	function okClick() {
		
		if (completed_loading==false){
			setTimeout("okClick()",100);
			return;
		} else {
			// validate parameters
			if (validateParams()){
				var newtable 				= {};
				newtable.captiontxt			= table_prop.tcaption.value;
				newtable.captionalign		= table_prop.caption_position.options[table_prop.caption_position.selectedIndex].value;
				newtable.width				= (table_prop.twidth.value)?(table_prop.twidth.value + table_prop.twunits.value):'';
				newtable.height				= (table_prop.theight.value)?(table_prop.theight.value + table_prop.thunits.value):'';
				newtable.border				= table_prop.tborder.value;
				newtable.cols				= table_prop.tcols.value;
				newtable.rows				= table_prop.trows.value
				newtable.align				= table_prop.talign.value
				newtable.cellPadding		= table_prop.tcpad.value;
				newtable.cellSpacing		= table_prop.tcspc.value;
				newtable.summary			= table_prop.tsum.value;
				newtable.scope				= table_prop.tableScope.value;
				
				if (table_prop.tbgcolor.value != "__REMOVE__"){
			 			newtable.bgColor = table_prop.tbgcolor.value;
					} else {
						newtable.bgColor = null;
				}
				window.returnValue = newtable;
				window.close();
			}
		}
	}

	function cancelClick() {
		window.close();
	}

	function setSample(){
		try {
			table_prop.color_sample.style.backgroundColor = table_prop.tbgcolor.value;
		} catch (excp) {
		}
	}
	
function onMenu(editor, sender, myData, overRideX, overRideY, overRideHeight, overRideWidth) {
//alert("["+editor+", "+sender+", "+overRideX+", "+overRideY+", "+overRideHeight+", "+overRideWidth+"]");
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
//	ifrm.style.width 		= 220;
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
	for (x=0;x<table_prop.elements.length;x++){
		if (table_prop.elements[x].tagName == 'SELECT'){
			table_prop.elements[x].style.visible = 'visible';
			table_prop.elements[x].style.display = '';
		}
	}
}
function startTimeTable(){
	clearTimeout(CloseTableParameter);
	CloseTableParameter = setTimeout("parent.close_the_dropdown()",500);
}
function endTimeTable(){
	clearTimeout(CloseTableParameter);
}
function close_the_dropdown(){
	clearTimeout(CloseTableParameter);
	cancelTable();
}


	</script>
</body>
</html>
