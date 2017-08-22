<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:11 $
	-	$Revision: 1.2 $
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
  <title>Icon Selection Manager</title>
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css"/>
</head>

<body>
<form name="link_browser" method="post" action="">
<input type="hidden" name="theme" value="default">
<input type="hidden" name="lang" value="en">
<input type="hidden" name="images" value="">
<div style="border: 1 solid Black; padding: 5 5 5 5;">
<DIV ID='internalprop1'>
<P id=tableProps CLASS=tablePropsTitle><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_emoc.gif'/> Icon Selection Manager</P>
<span id="specialcharacterboard" name="specialcharacterboard"></span>

</DIV>
</div>

</form>
  <script>
	var list = new Array();
	for (index=1;index<184;index++){
		if (index<10){
			list[list.length] = "emocs_0"+index;
		}else if(index<100) {
			list[list.length] = "emocs_"+index;
		}else {
			val = (index-100)+1
			if (val<10){
				val="0"+val
			}
			list[list.length] = "emocs_1_"+val;
		}
	}
	function save(pos){
		if(pos>=100) {
			val = (pos-100)
			if (val<10){
				val="0"+val
			}
			imgsource = "emocs_1_"+val;
		} else {
			imgsource = list[pos]
		}
		window.returnValue = '/libertas_images/icons/emocs/'+imgsource+'.gif" alt="'+imgsource+'"';
		window.close()
	}
	function display_page(page){
		var out = "<p align='center'><table border=0>";
		c_pos=-1;
		for(pos=0;pos<list.length;pos++){
			character_of_choice = list[pos];
			if (c_pos==-1){
				out += "<tr>";
			}
			c_pos++;
			out +='<td style="border:1px solid #cccccc" width="20" align="center" name="cell_'+ pos +'" id="cell_'+ pos +'" height="20" onmouseover="javascript:highlight(\'cell_'+ pos +'\',true);return false;" onmouseout="javascript:highlight(\'cell_'+ pos +'\',false);return false;"><a onclick="javascript:save('+pos+');" class="character"><img src="/libertas_images/icons/emocs/'+character_of_choice+'.gif" border="0" width="20" height="20"></a></td>';
			if (c_pos>=16){
				out += "</tr>";
				c_pos=-1;
			}
		}
		out += '<tr><td align="center" colspan="15">';
		out += '<INPUT class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="Cancel"> ';
		out += '</td></tr>';
		out += "</table></p>";
		document.all.specialcharacterboard.innerHTML = out;
	}
	setTimeout("display_page(0)",100);

function highlight(id, myStatus){
	if (myStatus){
		this.document.all[id].style.borderLeft		= "1px solid #ff0000";	
		this.document.all[id].style.borderRight		= "1px solid #ff0000";	
		this.document.all[id].style.borderTop		= "1px solid #ff0000";	
		this.document.all[id].style.borderBottom	= "1px solid #ff0000";	
	} else {
		this.document.all[id].style.borderLeft		= "1px solid #cccccc";	
		this.document.all[id].style.borderRight		= "1px solid #cccccc";	
		this.document.all[id].style.borderTop		= "1px solid #cccccc";	
		this.document.all[id].style.borderBottom	= "1px solid #cccccc";	
	}
}
	</script>
</body>
</html>
