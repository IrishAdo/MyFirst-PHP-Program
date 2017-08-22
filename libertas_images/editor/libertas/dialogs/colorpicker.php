<?php 
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:12 $
	-	$Revision: 1.2 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
// include wysiwyg config
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
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
	<meta http-equiv="Pragma" content="no-cache">
  <title>Libertas Solutions - - - - - - - - - - - - - - - - - - - - - - - - - </title>
<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
</head>

<body onLoad="Init()" dir="ltr">


<table cellspacing=0 cellpadding=3 width="500">
<tr>
	<td id='defined' class='tabularbuttonleft' width="50%">Defined Web Safe Colours</td>
	<td id='web' class='tabularbuttonright' width="50%">&nbsp;</td></tr>
<tr><td colspan='2' class='tabularcontent'>
<table border="0" cellspacing="0" cellpadding="0" id='web_palette' >
<?php
function hexColor($color_red, $color_green, $color_blue) {
	return sprintf("%02X%02X%02X",$color_red, $color_green, $color_blue);
}
$add =51;
	for ($red = 0;$red<256;$red += $add){

			$colour = hexColor($red,$red,$red);
?>
<tr>
    <td bgcolor="#<?php print $colour;?>"><img id="img<?php print $colour;?>" src="/libertas_images/themes/1x1.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('<?php print $colour;?>')" onDblClick="returnColor('<?php print $colour;?>')"></td>
<?php
		for ($green = 0;$green<256;$green += $add){
			for ($blue = 0;$blue<256;$blue += $add){
			$colour = hexColor($red,$green,$blue);
?>
    <td bgcolor="#<?php print $colour;?>"><img id="img<?php print $colour;?>" src="/libertas_images/themes/1x1.gif" class="img_pick" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('<?php print $colour;?>')" onDblClick="returnColor('<?php print $colour;?>')"></td>
<?php	
			}
		}
?>
</tr><?php	
	}
?>
</table>
</td></tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" width="336">
<form name="colorpicker">
<tr>
<td id="sample" align="left" width="80"><img src="/libertas_images/themes/1x1.gif" border="1" width="80" height="30" hspace="0" vspace="0"></td>
</td>
<td align="right" valign="bottom" width="80%" nowrap>
<input type="hidden" id="color" name="color" >
<input type="button" value="<?php echo LOCALE_REMOVE; ?>" onClick="okRemove()" class="bt">
<input type="button" value="<?php echo LOCALE_OK; ?>" onClick="okClick()" class="bt">
<input type="button" value="<?php echo LOCALE_CANCEL;?>" onClick="cancelClick()" class="bt">
</td>
</tr>
</form>
</table>
  <script language="javascript" src="utils.js"></script>
  
  <script language="javascript">
  <!--  
  var cur_color; // passed color
  var current_tab = 'web';

  function Init() {
    cur_color = window.dialogArguments;
    if (cur_color != null && cur_color!='__REMOVE__')
    {
      colorpicker.color.value = cur_color;
      sample.bgColor = cur_color;
    }
    resizeDialogToContent();
  }
  
  function okClick() {
    window.returnValue = colorpicker.color.value;
    window.close();
  }

  function cancelClick() {
    window.close();
  }
  
  function imgOn(imgid)
  {
    imgid.className = 'img_pick_over';
  }
  function imgOff(imgid)
  {
    imgid.className = 'img_pick';
  }
  function imgOn_palette(imgid)
  {
    imgid.className = 'img_pick_palette_over';
  }
  function imgOff_palette(imgid)
  {
    imgid.className = 'img_pick_palette';
  }
  function selColor(colorcode)
  {
    sample.bgColor = '#'+colorcode;
    colorpicker.color.value = '#'+colorcode;
  }
  function returnColor(colorcode)
  {
    window.returnValue = '#'+colorcode;
    window.close();
  }
  function setSample()
  {
    sample.bgColor = colorpicker.color.value;
  }
  
	function show(identifier){
  		if(identifier!=current_tab){
			if(identifier=='defined'){
				web_palette.style.visibility 		= 'hidden';
				web_palette.style.display 			= 'none';
				defined_palette.style.visibility 	= 'visible';
				defined_palette.style.display 		= '';
				defined.style.background			= '#ebebeb';
				web.style.background				= '#cccccc';
			} else {
				defined_palette.style.visibility 	= 'hidden';
				defined_palette.style.display 		= 'none';
				web_palette.style.visibility 		= 'visible';
				web_palette.style.display 			= '';
				defined.style.background			= '#cccccc';
				web.style.background				= '#ebebeb';
			}
			current_tab = identifier;
		}
	}
	function okRemove(){
	    window.returnValue = '__REMOVE__';
    	window.close();
  	}
  //-->
  </script>

</body>
</html>
