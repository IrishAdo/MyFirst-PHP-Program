<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/10/28 14:04:25 $
	-	$Revision: 1.4 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';

$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$base_href = check_parameters($_GET,"base_href","/");

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
  <title>Regenerate Keywords</title>
	<meta http-equiv="Pragma" content="no-cache">
  <link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
</head>

<body onLoad="Init()">
<div style="border: 1 solid Black; padding: 5 5 5 5;">
<P id=tableProps CLASS=tablePropsTitle>Regenerating Keywords</P>
<table width='450px' border=0>
	<tr><td><table width='100%'>
		<tr><td><center><h3>Work in Progress </h3>
		<img src='/libertas_images/editor/libertas/lib/themes/default/img/working.gif'></center></td></tr>
	</table></td></tr>
</table>
</div>
<iframe src='<?php print $base_href;?>admin/gen_keys.php?<?php print $session_url;?>' style="display:none;visibility:hide;width:0px;height:0px" id='keygenerator' name='keygenerator'></iframe>
  <script language="javascript">
  <!--
    window.name = 'key_generator';
  //-->
  </script>
  <script language="javascript" src="utils.js"></script>
  <script language="javascript">
  <!--
	 var myObject = window.dialogArguments
//  	var pw = window.dialogArguments.document.parentWindow;
	var base_href ='<?php print $base_href; ?>';
	var session_url ='<?php print $session_url; ?>';
	setTimeout("get_data_from_form();",2000);
	function get_data_from_form(){
		if (document.frames['keygenerator'].document.readyState != 'complete'){
			setTimeout("get_data_from_form();",2000);
			return -1;
		}

		document.frames['keygenerator'].document.keyGen.source.value 			= keyGeneration(myObject);
		document.frames['keygenerator'].document.keyGen.extraIgnoreList.value	= myObject.extraIgnoreList;
		document.frames['keygenerator'].document.keyGen.submit();
	}
	function listen(str){
		window.returnValue = str;
		window.close();
	}
    function Init(){
  		resizeDialogToContent(500,220);
    }
	
//	keyGeneration(myObject);

	function keyGeneration(myObj){
		
//		document.all["wip"].innerHTML = "<font size=2>Preparing Libertas Word Matrix .</font>";
		find_list = Array("\\W", "\\b\\w{1,4}\\b", "\\n", "\\r\\n", "\\n\\r", "\\r", "  ", "  ", "  ", "  ", "  ");
		replace_list = Array(" ", " ", " ", " ", " ", " ", " ", " ", " ", " ", " ");
		if(myObj.extraIgnoreList.length!=0){
			extra = myObj.extraIgnoreList.split(",");
			for(i=0;i<extra.length;i++){
				find_list[find_list.length] = extra[i];
				replace_list[replace_list.length] = " ";
			}
		}
		var str = convert_special_characters(myObj.src.toLowerCase());
		s=".";
		for (i=0;i<find_list.length;i++){
			str = str.replace(new RegExp(find_list[i],"g"), replace_list[i]);
			s +="."; 
		}
		return str;
/*		str = str.split(" ");
//		document.all["wip"].innerHTML = "<font size=2>Libertas Word Matrix Initialised.</font>";
//		alert(str.length)
		str.sort(doCompareAlpha);
//		alert(str.length)
		words = Array();
		count=0;
		for (i=0;i<str.length;i++){
			if (words[str[i]]+""=="undefined"){
				words[str[i]] = 1;
				count++;
			}else {
				words[str[i]]++;
			}
		}
		s="";
		words = bubble_sort(words);
		count=0;
		if (words.length>15)
			l = 15;
		else 
			l =  words.length;
		for (i=0;i<l;i++){
			if (count==0){
				s += "	<tr>\n";
			}	
			s += words[i].toString(i);
			count ++ ;
			if (count == 3){
				s += "</tr>";
				count=0;
			}
		}
		if (count==0 || count<3){
				s += "	</tr>\n";
		
		}
		window.returnValue = "<table width='100%' border='0'>\n"+s+"</table>";
		window.close()
		*/
	}
	function doCompareItem(a,b){
		return b.value - a.value ;
	}
	
	function doCompareAlpha(a,b){
		if (a < b){
			return 1;
		} else if (a > b){
			return -1;
		} else 
			return 0;
	}
	function worditem(myIndex, myValue){
		this.index = myIndex;
		this.value = myValue;
		this.toString = itemToString;
	}
	function itemToString(i){
		return "		<TD><INPUT id=keyword_"+i+" onclick=\"javascript:ignore_keyword('keyword_"+i+"')\" type=checkbox CHECKED value=\"" + this.value + ", "+ this.index + "\" name=keywords[]><LABEL for=keyword_"+ i + ">"+ this.index + " ("+ this.value + ")</LABEL></TD>\n";
	}

	function bubble_sort(myArray){
		list = Array()
		for(var word in words){
			list[list.length] = new worditem(word,words[word]);
		}
		return list.sort(doCompareItem);
	}
  //-->
  </script>
</body>
</html>
