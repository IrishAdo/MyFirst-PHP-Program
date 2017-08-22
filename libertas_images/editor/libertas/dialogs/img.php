<?php 
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/11/15 16:53:48 $
	-	$Revision: 1.5 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_root.'class/lang.class.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';
$theme = empty($_GET['theme'])?$libertas_default_theme:$_GET['theme'];
$theme_path = $libertas_dir.'lib/themes/'.$theme.'/';

$l = new LIBERTAS_Lang($_GET['lang']);
$l->setBlock('image_prop');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">

<html>
<head>
  <title>Libertas Solutions - - - - - - - - - - - - - - - - - - - - - - - - - </title>
  <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $l->getCharset()?>">
  <link rel="stylesheet" type="text/css" href="<?php echo $theme_path.'css/'?>dialog.css">
</head>

<body onLoad="Init()" dir="<?php echo $l->getDir();?>">
<P id=tableProps CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_image_prop.gif'/> Image Property Manager</P>
<form name="img_prop">
<input type="hidden" name="csrc" class="input" size="32">
<table border="0" cellspacing="0" cellpadding="2" width="336">
<tr>
  <td><?php echo LOCALE_ALT?>:</td>
  <td colspan="3"><input type="text" name="calt" class="input" size="32"></td>
</tr>
<tr>
  <td><?php echo LOCALE_ALIGN;?>:</td>
  <td align="left">
  <select name="calign" size="1" class="input">
    <option value="">None</option>
    <option value="left"><?php echo LOCALE_LEFT?></option>
    <option value="right"><?php echo LOCALE_RIGHT?></option>
  </select>
  </td>
  <td><?php echo LOCALE_BORDER?>:</td>
  <td align="left"><input type="text" name="cborder" class="input_small"></td>
</tr>
<tr>
  <td><?php echo LOCALE_WIDTH;?>:</td>
  <td nowrap>
    <input type="text" name="cwidth" size="3" maxlenght="3" class="input_small">
  </td>
  <td><?php echo LOCALE_HEIGHT;?>
:</td>
  <td nowrap>
    <input type="text" name="cheight" size="3" maxlenght="3" class="input_small">
  </td>
</tr>
<tr>
  <td><?php echo LOCALE_HSPACE?>:</td>
  <td nowrap>
    <input type="text" name="chspace" size="3" maxlenght="3" class="input_small">
  </td>
  <td><?php echo LOCALE_VSPACE?>:</td>
  <td nowrap>
    <input type="text" name="cvspace" size="3" maxlenght="3" class="input_small">
  </td>
</tr>
<tr>
<td colspan="4" nowrap>
<hr width="100%">
</td>
</tr>
<tr>
<td colspan="4" align="right" valign="bottom" nowrap>
<input type="button" value="<?php echo LOCALE_OK; ?>" onClick="okClick()" class="bt">
<input type="button" value="<?php echo LOCALE_CANCEL;?>" onClick="cancelClick()" class="bt">
</td>
</tr>
</table>
</form>

  <script language="javascript" src="utils.js"></script>
  
  <script language="javascript">
  <!--  
  function Init() {
    var iProps = window.dialogArguments;
    if (iProps)
    {
      // set attribute values
      if (iProps.width) {
        img_prop.cwidth.value = iProps.width;
      }
      if (iProps.height) {
        img_prop.cheight.value = iProps.height;
      }
      
      setAlign(iProps.align);
      
      if (iProps.src) {
        img_prop.csrc.value = iProps.src;
      }
      if (iProps.alt) {
        img_prop.calt.value = iProps.alt;
      }
      if (iProps.border) {
        img_prop.cborder.value = iProps.border;
      } else {
        img_prop.cborder.value = 0;
	  }
      if (iProps.hspace) {
        img_prop.chspace.value = iProps.hspace;
      } else {
        img_prop.chspace.value = 0;
	  }
      if (iProps.vspace) {
        img_prop.cvspace.value = iProps.vspace;
      } else {
        img_prop.cvspace.value = 0;
	  }
    }
    resizeDialogToContent();
  }
  
  function validateParams()
  {
    // check width and height
    if (isNaN(parseInt(img_prop.cwidth.value)) && img_prop.cwidth.value != '')
    {
      alert('<?php echo 'Error: '.$l->m('error_width_nan')?>');
      img_prop.cwidth.focus();
      return false;
    }
    if (isNaN(parseInt(img_prop.cheight.value)) && img_prop.cheight.value != '')
    {
      alert('<?php echo 'Error: '.$l->m('error_height_nan')?>');
      img_prop.cheight.focus();
      return false;
    }
    if (isNaN(parseInt(img_prop.cborder.value)) && img_prop.cborder.value != '')
    {
      alert('<?php echo 'Error: '.$l->m('error_border_nan')?>');
      img_prop.cborder.focus();
      return false;
    }
    if (isNaN(parseInt(img_prop.chspace.value)) && img_prop.chspace.value != '')
    {
      alert('<?php echo 'Error: '.$l->m('error_hspace_nan')?>');
      img_prop.chspace.focus();
      return false;
    }
    if (isNaN(parseInt(img_prop.cvspace.value)) && img_prop.cvspace.value != '')
    {
      alert('<?php echo 'Error: '.$l->m('error_vspace_nan')?>');
      img_prop.cvspace.focus();
      return false;
    }
    
    return true;
  }
  
  function okClick() {
    // validate paramters
    if (validateParams())    
    {
      var iProps = {};
      iProps.align = (img_prop.calign.options[img_prop.calign.selectedIndex].value)?(img_prop.calign.options[img_prop.calign.selectedIndex].value):'';
      iProps.width = (img_prop.cwidth.value)?(img_prop.cwidth.value):'';
      iProps.height = (img_prop.cheight.value)?(img_prop.cheight.value):'';
      iProps.border = (img_prop.cborder.value)?(img_prop.cborder.value):'';
      iProps.src = (img_prop.csrc.value)?(img_prop.csrc.value):'';
      iProps.alt = (img_prop.calt.value)?(img_prop.calt.value):'';
      iProps.hspace = (img_prop.chspace.value)?(img_prop.chspace.value):'';
      iProps.vspace = (img_prop.cvspace.value)?(img_prop.cvspace.value):'';
      window.returnValue = iProps;
      window.close();
    }
  }

  function cancelClick() {
    window.close();
  }
  
  
  function setAlign(alignment)
  {
    for (i=0; i<img_prop.calign.options.length; i++)  
    {
      al = img_prop.calign.options.item(i);
      if (al.value == alignment.toLowerCase()) {
        img_prop.calign.selectedIndex = al.index;
      }
    }
  }
  //-->
  </script>
</body>
</html>
