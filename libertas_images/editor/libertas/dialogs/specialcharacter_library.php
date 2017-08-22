<!doctype html public "-//W3C//DTD HTML 4.0 Transitional//EN"><?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:04 $
	-	$Revision: 1.4 $
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
<!-- Libertas -->
<style>A.character:visited, A.character:active, A.character:link{
		text-decoration:none;color:#000000;font-size:14px;width:20px height:20px;
	}
	A.character:hover{
		text-decoration:none;color:#000000;font-size:14px;width:20px height:20px;
		border-top: 1px solid #ffffff;
		border-left: 1px solid #ffffff;
		border-right: 1px solid #333333;
		border-bottom: 1px solid #333333;
	}
	.speciacharacter{
		border:1px solid #cccccc;
	}
	</style>
<title>Special characters</title>
<meta http-equiv="Pragma" content="no-cache"><link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
<script language="javascript" src="utils.js"></script>
<script>
/*var list = new Array('8364','8482','169','174','131','132','133','134','135','136','137','138','139','140','145','146','147','148',
						 '149','150','151','152','153','154','155','156','159','160','161','162','163','164',
						 '165','166','167','168','170','171','172','175','176','177','178',
						 '179','180','181','183','184','185','186','187','188','189','190','191','192',
						 '193','194','195','196','197','198','199','200','201','202','203','204','205','206',
						 '207','208','209','210','211','212','213','214','215','216','217','218','219','220',
						 '221','222','223','224','225','226','227','228','229','230','231','232','233','234',
						 '235','236','237','238','239','240','241','242','243','244','245','246','247','248',
						 '249','250','251','252','253','254','255', '402', '913', '914', '915', '916', '917', 
						 '918', '919', '920', '921', '922', '923', '924', '925', '926', '927', '928', '929', 
						 '931', '932', '933', '934', '935', '936', '937', '945', '946', '947', '948', '949', 
						 '950', '951', '952', '953', '954', '955', '956', '957', '958', '959', '960', '961', 
						 '962', '963', '964', '965', '966', '967', '968', '969', '977');
*/
	function save(myChar){
		window.returnValue = "&#"+myChar+";";
		window.close()
	}
    function Init(){
		document.getElementById("divshowtable").style.display='';
		document.getElementById("divhidetable").style.display='none';
	    resizeDialogToContent();
    }

function highlight(id, myStatus){
	try{
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
	} catch	(e) {
	
	}
}
</script>
</head>
<body onLoad="Init()">
	<form name="link_browser" method="post" action="">		
		<input type="hidden" name="theme" value="default">		
		<input type="hidden" name="lang" value="en">		
		<input type="hidden" name="images" value="">
		<div style="border: 1 solid Black; padding: 5 5 5 5;">
			<div id='internalprop1'>
				<p id="tableProps" class="tablePropsTitle"><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_special_character.gif'/>Special Character Selection Manager</p><span id="specialcharacterboard" name="specialcharacterboard">
				<p align='center'>Please click the character you wish to add to the page.<br/>
				<div id='divhidetable' style="text-align:center;color:#ff0000;">LOADING ....</div>
				<div id='divshowtable' style="display:none">
				<table border="0">
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_0_0' id='cell_0_0' height='20' onMouseOver='javascript:highlight("cell_0_0",true);return false;' onMouseOut='javascript:highlight("cell_0_0",false);return false;'><a onClick='javascript:save(8364);' class='character'>&#8364;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_1' id='cell_0_1' height='20' onMouseOver='javascript:highlight("cell_0_1",true);return false;' onMouseOut='javascript:highlight("cell_0_1",false);return false;'><a onClick='javascript:save(8482);' class='character'>&#8482;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_2' id='cell_0_2' height='20' onMouseOver='javascript:highlight("cell_0_2",true);return false;' onMouseOut='javascript:highlight("cell_0_2",false);return false;'><a onClick='javascript:save(169);' class='character'>&#169;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_3' id='cell_0_3' height='20' onMouseOver='javascript:highlight("cell_0_3",true);return false;' onMouseOut='javascript:highlight("cell_0_3",false);return false;'><a onClick='javascript:save(174);' class='character'>&#174;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_4' id='cell_0_4' height='20' onMouseOver='javascript:highlight("cell_0_4",true);return false;' onMouseOut='javascript:highlight("cell_0_4",false);return false;'><a onClick='javascript:save(131);' class='character'>&#131;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_5' id='cell_0_5' height='20' onMouseOver='javascript:highlight("cell_0_5",true);return false;' onMouseOut='javascript:highlight("cell_0_5",false);return false;'><a onClick='javascript:save(132);' class='character'>&#132;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_6' id='cell_0_6' height='20' onMouseOver='javascript:highlight("cell_0_6",true);return false;' onMouseOut='javascript:highlight("cell_0_6",false);return false;'><a onClick='javascript:save(133);' class='character'>&#133;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_7' id='cell_0_7' height='20' onMouseOver='javascript:highlight("cell_0_7",true);return false;' onMouseOut='javascript:highlight("cell_0_7",false);return false;'><a onClick='javascript:save(134);' class='character'>&#134;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_8' id='cell_0_8' height='20' onMouseOver='javascript:highlight("cell_0_8",true);return false;' onMouseOut='javascript:highlight("cell_0_8",false);return false;'><a onClick='javascript:save(135);' class='character'>&#135;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_9' id='cell_0_9' height='20' onMouseOver='javascript:highlight("cell_0_9",true);return false;' onMouseOut='javascript:highlight("cell_0_9",false);return false;'><a onClick='javascript:save(136);' class='character'>&#136;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_10' id='cell_0_10' height='20' onMouseOver='javascript:highlight("cell_0_10",true);return false;' onMouseOut='javascript:highlight("cell_0_10",false);return false;'><a onClick='javascript:save(137);' class='character'>&#137;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_11' id='cell_0_11' height='20' onMouseOver='javascript:highlight("cell_0_11",true);return false;' onMouseOut='javascript:highlight("cell_0_11",false);return false;'><a onClick='javascript:save(138);' class='character'>&#138;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_12' id='cell_0_12' height='20' onMouseOver='javascript:highlight("cell_0_12",true);return false;' onMouseOut='javascript:highlight("cell_0_12",false);return false;'><a onClick='javascript:save(139);' class='character'>&#139;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_13' id='cell_0_13' height='20' onMouseOver='javascript:highlight("cell_0_13",true);return false;' onMouseOut='javascript:highlight("cell_0_13",false);return false;'><a onClick='javascript:save(140);' class='character'>&#140;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_0_14' id='cell_0_14' height='20' onMouseOver='javascript:highlight("cell_0_14",true);return false;' onMouseOut='javascript:highlight("cell_0_14",false);return false;'><a onClick='javascript:save(145);' class='character'>&#145;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_1_0' id='cell_1_0' height='20' onMouseOver='javascript:highlight("cell_1_0",true);return false;' onMouseOut='javascript:highlight("cell_1_0",false);return false;'><a onClick='javascript:save(146);' class='character'>&#146;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_1' id='cell_1_1' height='20' onMouseOver='javascript:highlight("cell_1_1",true);return false;' onMouseOut='javascript:highlight("cell_1_1",false);return false;'><a onClick='javascript:save(147);' class='character'>&#147;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_2' id='cell_1_2' height='20' onMouseOver='javascript:highlight("cell_1_2",true);return false;' onMouseOut='javascript:highlight("cell_1_2",false);return false;'><a onClick='javascript:save(148);' class='character'>&#148;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_3' id='cell_1_3' height='20' onMouseOver='javascript:highlight("cell_1_3",true);return false;' onMouseOut='javascript:highlight("cell_1_3",false);return false;'><a onClick='javascript:save(149);' class='character'>&#149;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_4' id='cell_1_4' height='20' onMouseOver='javascript:highlight("cell_1_4",true);return false;' onMouseOut='javascript:highlight("cell_1_4",false);return false;'><a onClick='javascript:save(150);' class='character'>&#150;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_5' id='cell_1_5' height='20' onMouseOver='javascript:highlight("cell_1_5",true);return false;' onMouseOut='javascript:highlight("cell_1_5",false);return false;'><a onClick='javascript:save(151);' class='character'>&#151;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_6' id='cell_1_6' height='20' onMouseOver='javascript:highlight("cell_1_6",true);return false;' onMouseOut='javascript:highlight("cell_1_6",false);return false;'><a onClick='javascript:save(152);' class='character'>&#152;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_7' id='cell_1_7' height='20' onMouseOver='javascript:highlight("cell_1_7",true);return false;' onMouseOut='javascript:highlight("cell_1_7",false);return false;'><a onClick='javascript:save(153);' class='character'>&#153;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_8' id='cell_1_8' height='20' onMouseOver='javascript:highlight("cell_1_8",true);return false;' onMouseOut='javascript:highlight("cell_1_8",false);return false;'><a onClick='javascript:save(154);' class='character'>&#154;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_9' id='cell_1_9' height='20' onMouseOver='javascript:highlight("cell_1_9",true);return false;' onMouseOut='javascript:highlight("cell_1_9",false);return false;'><a onClick='javascript:save(155);' class='character'>&#155;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_10' id='cell_1_10' height='20' onMouseOver='javascript:highlight("cell_1_10",true);return false;' onMouseOut='javascript:highlight("cell_1_10",false);return false;'><a onClick='javascript:save(156);' class='character'>&#156;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_11' id='cell_1_11' height='20' onMouseOver='javascript:highlight("cell_1_11",true);return false;' onMouseOut='javascript:highlight("cell_1_11",false);return false;'><a onClick='javascript:save(159);' class='character'>&#159;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_12' id='cell_1_12' height='20' onMouseOver='javascript:highlight("cell_1_12",true);return false;' onMouseOut='javascript:highlight("cell_1_12",false);return false;'><a onClick='javascript:save(160);' class='character'>&#160;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_13' id='cell_1_13' height='20' onMouseOver='javascript:highlight("cell_1_13",true);return false;' onMouseOut='javascript:highlight("cell_1_13",false);return false;'><a onClick='javascript:save(161);' class='character'>&#161;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_1_14' id='cell_1_14' height='20' onMouseOver='javascript:highlight("cell_1_14",true);return false;' onMouseOut='javascript:highlight("cell_1_14",false);return false;'><a onClick='javascript:save(162);' class='character'>&#162;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_2_0' id='cell_2_0' height='20' onMouseOver='javascript:highlight("cell_2_0",true);return false;' onMouseOut='javascript:highlight("cell_2_0",false);return false;'><a onClick='javascript:save(163);' class='character'>&#163;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_1' id='cell_2_1' height='20' onMouseOver='javascript:highlight("cell_2_1",true);return false;' onMouseOut='javascript:highlight("cell_2_1",false);return false;'><a onClick='javascript:save(164);' class='character'>&#164;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_2' id='cell_2_2' height='20' onMouseOver='javascript:highlight("cell_2_2",true);return false;' onMouseOut='javascript:highlight("cell_2_2",false);return false;'><a onClick='javascript:save(165);' class='character'>&#165;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_3' id='cell_2_3' height='20' onMouseOver='javascript:highlight("cell_2_3",true);return false;' onMouseOut='javascript:highlight("cell_2_3",false);return false;'><a onClick='javascript:save(166);' class='character'>&#166;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_4' id='cell_2_4' height='20' onMouseOver='javascript:highlight("cell_2_4",true);return false;' onMouseOut='javascript:highlight("cell_2_4",false);return false;'><a onClick='javascript:save(167);' class='character'>&#167;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_5' id='cell_2_5' height='20' onMouseOver='javascript:highlight("cell_2_5",true);return false;' onMouseOut='javascript:highlight("cell_2_5",false);return false;'><a onClick='javascript:save(168);' class='character'>&#168;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_6' id='cell_2_6' height='20' onMouseOver='javascript:highlight("cell_2_6",true);return false;' onMouseOut='javascript:highlight("cell_2_6",false);return false;'><a onClick='javascript:save(170);' class='character'>&#170;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_7' id='cell_2_7' height='20' onMouseOver='javascript:highlight("cell_2_7",true);return false;' onMouseOut='javascript:highlight("cell_2_7",false);return false;'><a onClick='javascript:save(171);' class='character'>&#171;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_8' id='cell_2_8' height='20' onMouseOver='javascript:highlight("cell_2_8",true);return false;' onMouseOut='javascript:highlight("cell_2_8",false);return false;'><a onClick='javascript:save(172);' class='character'>&#172;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_9' id='cell_2_9' height='20' onMouseOver='javascript:highlight("cell_2_9",true);return false;' onMouseOut='javascript:highlight("cell_2_9",false);return false;'><a onClick='javascript:save(175);' class='character'>&#175;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_10' id='cell_2_10' height='20' onMouseOver='javascript:highlight("cell_2_10",true);return false;' onMouseOut='javascript:highlight("cell_2_10",false);return false;'><a onClick='javascript:save(176);' class='character'>&#176;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_11' id='cell_2_11' height='20' onMouseOver='javascript:highlight("cell_2_11",true);return false;' onMouseOut='javascript:highlight("cell_2_11",false);return false;'><a onClick='javascript:save(177);' class='character'>&#177;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_12' id='cell_2_12' height='20' onMouseOver='javascript:highlight("cell_2_12",true);return false;' onMouseOut='javascript:highlight("cell_2_12",false);return false;'><a onClick='javascript:save(178);' class='character'>&#178;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_13' id='cell_2_13' height='20' onMouseOver='javascript:highlight("cell_2_13",true);return false;' onMouseOut='javascript:highlight("cell_2_13",false);return false;'><a onClick='javascript:save(179);' class='character'>&#179;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_2_14' id='cell_2_14' height='20' onMouseOver='javascript:highlight("cell_2_14",true);return false;' onMouseOut='javascript:highlight("cell_2_14",false);return false;'><a onClick='javascript:save(180);' class='character'>&#180;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_3_0' id='cell_3_0' height='20' onMouseOver='javascript:highlight("cell_3_0",true);return false;' onMouseOut='javascript:highlight("cell_3_0",false);return false;'><a onClick='javascript:save(181);' class='character'>&#181;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_1' id='cell_3_1' height='20' onMouseOver='javascript:highlight("cell_3_1",true);return false;' onMouseOut='javascript:highlight("cell_3_1",false);return false;'><a onClick='javascript:save(183);' class='character'>&#183;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_2' id='cell_3_2' height='20' onMouseOver='javascript:highlight("cell_3_2",true);return false;' onMouseOut='javascript:highlight("cell_3_2",false);return false;'><a onClick='javascript:save(184);' class='character'>&#184;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_3' id='cell_3_3' height='20' onMouseOver='javascript:highlight("cell_3_3",true);return false;' onMouseOut='javascript:highlight("cell_3_3",false);return false;'><a onClick='javascript:save(185);' class='character'>&#185;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_4' id='cell_3_4' height='20' onMouseOver='javascript:highlight("cell_3_4",true);return false;' onMouseOut='javascript:highlight("cell_3_4",false);return false;'><a onClick='javascript:save(186);' class='character'>&#186;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_5' id='cell_3_5' height='20' onMouseOver='javascript:highlight("cell_3_5",true);return false;' onMouseOut='javascript:highlight("cell_3_5",false);return false;'><a onClick='javascript:save(187);' class='character'>&#187;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_6' id='cell_3_6' height='20' onMouseOver='javascript:highlight("cell_3_6",true);return false;' onMouseOut='javascript:highlight("cell_3_6",false);return false;'><a onClick='javascript:save(188);' class='character'>&#188;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_7' id='cell_3_7' height='20' onMouseOver='javascript:highlight("cell_3_7",true);return false;' onMouseOut='javascript:highlight("cell_3_7",false);return false;'><a onClick='javascript:save(189);' class='character'>&#189;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_8' id='cell_3_8' height='20' onMouseOver='javascript:highlight("cell_3_8",true);return false;' onMouseOut='javascript:highlight("cell_3_8",false);return false;'><a onClick='javascript:save(190);' class='character'>&#190;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_9' id='cell_3_9' height='20' onMouseOver='javascript:highlight("cell_3_9",true);return false;' onMouseOut='javascript:highlight("cell_3_9",false);return false;'><a onClick='javascript:save(191);' class='character'>&#191;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_10' id='cell_3_10' height='20' onMouseOver='javascript:highlight("cell_3_10",true);return false;' onMouseOut='javascript:highlight("cell_3_10",false);return false;'><a onClick='javascript:save(192);' class='character'>&#192;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_11' id='cell_3_11' height='20' onMouseOver='javascript:highlight("cell_3_11",true);return false;' onMouseOut='javascript:highlight("cell_3_11",false);return false;'><a onClick='javascript:save(193);' class='character'>&#193;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_12' id='cell_3_12' height='20' onMouseOver='javascript:highlight("cell_3_12",true);return false;' onMouseOut='javascript:highlight("cell_3_12",false);return false;'><a onClick='javascript:save(194);' class='character'>&#194;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_13' id='cell_3_13' height='20' onMouseOver='javascript:highlight("cell_3_13",true);return false;' onMouseOut='javascript:highlight("cell_3_13",false);return false;'><a onClick='javascript:save(195);' class='character'>&#195;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_3_14' id='cell_3_14' height='20' onMouseOver='javascript:highlight("cell_3_14",true);return false;' onMouseOut='javascript:highlight("cell_3_14",false);return false;'><a onClick='javascript:save(196);' class='character'>&#196;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_4_0' id='cell_4_0' height='20' onMouseOver='javascript:highlight("cell_4_0",true);return false;' onMouseOut='javascript:highlight("cell_4_0",false);return false;'><a onClick='javascript:save(197);' class='character'>&#197;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_1' id='cell_4_1' height='20' onMouseOver='javascript:highlight("cell_4_1",true);return false;' onMouseOut='javascript:highlight("cell_4_1",false);return false;'><a onClick='javascript:save(198);' class='character'>&#198;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_2' id='cell_4_2' height='20' onMouseOver='javascript:highlight("cell_4_2",true);return false;' onMouseOut='javascript:highlight("cell_4_2",false);return false;'><a onClick='javascript:save(199);' class='character'>&#199;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_3' id='cell_4_3' height='20' onMouseOver='javascript:highlight("cell_4_3",true);return false;' onMouseOut='javascript:highlight("cell_4_3",false);return false;'><a onClick='javascript:save(200);' class='character'>&#200;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_4' id='cell_4_4' height='20' onMouseOver='javascript:highlight("cell_4_4",true);return false;' onMouseOut='javascript:highlight("cell_4_4",false);return false;'><a onClick='javascript:save(201);' class='character'>&#201;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_5' id='cell_4_5' height='20' onMouseOver='javascript:highlight("cell_4_5",true);return false;' onMouseOut='javascript:highlight("cell_4_5",false);return false;'><a onClick='javascript:save(202);' class='character'>&#202;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_6' id='cell_4_6' height='20' onMouseOver='javascript:highlight("cell_4_6",true);return false;' onMouseOut='javascript:highlight("cell_4_6",false);return false;'><a onClick='javascript:save(203);' class='character'>&#203;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_7' id='cell_4_7' height='20' onMouseOver='javascript:highlight("cell_4_7",true);return false;' onMouseOut='javascript:highlight("cell_4_7",false);return false;'><a onClick='javascript:save(204);' class='character'>&#204;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_8' id='cell_4_8' height='20' onMouseOver='javascript:highlight("cell_4_8",true);return false;' onMouseOut='javascript:highlight("cell_4_8",false);return false;'><a onClick='javascript:save(205);' class='character'>&#205;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_9' id='cell_4_9' height='20' onMouseOver='javascript:highlight("cell_4_9",true);return false;' onMouseOut='javascript:highlight("cell_4_9",false);return false;'><a onClick='javascript:save(206);' class='character'>&#206;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_10' id='cell_4_10' height='20' onMouseOver='javascript:highlight("cell_4_10",true);return false;' onMouseOut='javascript:highlight("cell_4_10",false);return false;'><a onClick='javascript:save(207);' class='character'>&#207;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_11' id='cell_4_11' height='20' onMouseOver='javascript:highlight("cell_4_11",true);return false;' onMouseOut='javascript:highlight("cell_4_11",false);return false;'><a onClick='javascript:save(208);' class='character'>&#208;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_12' id='cell_4_12' height='20' onMouseOver='javascript:highlight("cell_4_12",true);return false;' onMouseOut='javascript:highlight("cell_4_12",false);return false;'><a onClick='javascript:save(209);' class='character'>&#209;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_13' id='cell_4_13' height='20' onMouseOver='javascript:highlight("cell_4_13",true);return false;' onMouseOut='javascript:highlight("cell_4_13",false);return false;'><a onClick='javascript:save(210);' class='character'>&#210;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_4_14' id='cell_4_14' height='20' onMouseOver='javascript:highlight("cell_4_14",true);return false;' onMouseOut='javascript:highlight("cell_4_14",false);return false;'><a onClick='javascript:save(211);' class='character'>&#211;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_5_0' id='cell_5_0' height='20' onMouseOver='javascript:highlight("cell_5_0",true);return false;' onMouseOut='javascript:highlight("cell_5_0",false);return false;'><a onClick='javascript:save(212);' class='character'>&#212;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_1' id='cell_5_1' height='20' onMouseOver='javascript:highlight("cell_5_1",true);return false;' onMouseOut='javascript:highlight("cell_5_1",false);return false;'><a onClick='javascript:save(213);' class='character'>&#213;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_2' id='cell_5_2' height='20' onMouseOver='javascript:highlight("cell_5_2",true);return false;' onMouseOut='javascript:highlight("cell_5_2",false);return false;'><a onClick='javascript:save(214);' class='character'>&#214;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_3' id='cell_5_3' height='20' onMouseOver='javascript:highlight("cell_5_3",true);return false;' onMouseOut='javascript:highlight("cell_5_3",false);return false;'><a onClick='javascript:save(215);' class='character'>&#215;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_4' id='cell_5_4' height='20' onMouseOver='javascript:highlight("cell_5_4",true);return false;' onMouseOut='javascript:highlight("cell_5_4",false);return false;'><a onClick='javascript:save(216);' class='character'>&#216;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_5' id='cell_5_5' height='20' onMouseOver='javascript:highlight("cell_5_5",true);return false;' onMouseOut='javascript:highlight("cell_5_5",false);return false;'><a onClick='javascript:save(217);' class='character'>&#217;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_6' id='cell_5_6' height='20' onMouseOver='javascript:highlight("cell_5_6",true);return false;' onMouseOut='javascript:highlight("cell_5_6",false);return false;'><a onClick='javascript:save(218);' class='character'>&#218;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_7' id='cell_5_7' height='20' onMouseOver='javascript:highlight("cell_5_7",true);return false;' onMouseOut='javascript:highlight("cell_5_7",false);return false;'><a onClick='javascript:save(219);' class='character'>&#219;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_8' id='cell_5_8' height='20' onMouseOver='javascript:highlight("cell_5_8",true);return false;' onMouseOut='javascript:highlight("cell_5_8",false);return false;'><a onClick='javascript:save(220);' class='character'>&#220;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_9' id='cell_5_9' height='20' onMouseOver='javascript:highlight("cell_5_9",true);return false;' onMouseOut='javascript:highlight("cell_5_9",false);return false;'><a onClick='javascript:save(221);' class='character'>&#221;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_10' id='cell_5_10' height='20' onMouseOver='javascript:highlight("cell_5_10",true);return false;' onMouseOut='javascript:highlight("cell_5_10",false);return false;'><a onClick='javascript:save(222);' class='character'>&#222;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_11' id='cell_5_11' height='20' onMouseOver='javascript:highlight("cell_5_11",true);return false;' onMouseOut='javascript:highlight("cell_5_11",false);return false;'><a onClick='javascript:save(223);' class='character'>&#223;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_12' id='cell_5_12' height='20' onMouseOver='javascript:highlight("cell_5_12",true);return false;' onMouseOut='javascript:highlight("cell_5_12",false);return false;'><a onClick='javascript:save(224);' class='character'>&#224;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_13' id='cell_5_13' height='20' onMouseOver='javascript:highlight("cell_5_13",true);return false;' onMouseOut='javascript:highlight("cell_5_13",false);return false;'><a onClick='javascript:save(225);' class='character'>&#225;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_5_14' id='cell_5_14' height='20' onMouseOver='javascript:highlight("cell_5_14",true);return false;' onMouseOut='javascript:highlight("cell_5_14",false);return false;'><a onClick='javascript:save(226);' class='character'>&#226;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_6_0' id='cell_6_0' height='20' onMouseOver='javascript:highlight("cell_6_0",true);return false;' onMouseOut='javascript:highlight("cell_6_0",false);return false;'><a onClick='javascript:save(227);' class='character'>&#227;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_1' id='cell_6_1' height='20' onMouseOver='javascript:highlight("cell_6_1",true);return false;' onMouseOut='javascript:highlight("cell_6_1",false);return false;'><a onClick='javascript:save(228);' class='character'>&#228;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_2' id='cell_6_2' height='20' onMouseOver='javascript:highlight("cell_6_2",true);return false;' onMouseOut='javascript:highlight("cell_6_2",false);return false;'><a onClick='javascript:save(229);' class='character'>&#229;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_3' id='cell_6_3' height='20' onMouseOver='javascript:highlight("cell_6_3",true);return false;' onMouseOut='javascript:highlight("cell_6_3",false);return false;'><a onClick='javascript:save(230);' class='character'>&#230;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_4' id='cell_6_4' height='20' onMouseOver='javascript:highlight("cell_6_4",true);return false;' onMouseOut='javascript:highlight("cell_6_4",false);return false;'><a onClick='javascript:save(231);' class='character'>&#231;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_5' id='cell_6_5' height='20' onMouseOver='javascript:highlight("cell_6_5",true);return false;' onMouseOut='javascript:highlight("cell_6_5",false);return false;'><a onClick='javascript:save(232);' class='character'>&#232;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_6' id='cell_6_6' height='20' onMouseOver='javascript:highlight("cell_6_6",true);return false;' onMouseOut='javascript:highlight("cell_6_6",false);return false;'><a onClick='javascript:save(233);' class='character'>&#233;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_7' id='cell_6_7' height='20' onMouseOver='javascript:highlight("cell_6_7",true);return false;' onMouseOut='javascript:highlight("cell_6_7",false);return false;'><a onClick='javascript:save(234);' class='character'>&#234;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_8' id='cell_6_8' height='20' onMouseOver='javascript:highlight("cell_6_8",true);return false;' onMouseOut='javascript:highlight("cell_6_8",false);return false;'><a onClick='javascript:save(235);' class='character'>&#235;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_9' id='cell_6_9' height='20' onMouseOver='javascript:highlight("cell_6_9",true);return false;' onMouseOut='javascript:highlight("cell_6_9",false);return false;'><a onClick='javascript:save(236);' class='character'>&#236;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_10' id='cell_6_10' height='20' onMouseOver='javascript:highlight("cell_6_10",true);return false;' onMouseOut='javascript:highlight("cell_6_10",false);return false;'><a onClick='javascript:save(237);' class='character'>&#237;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_11' id='cell_6_11' height='20' onMouseOver='javascript:highlight("cell_6_11",true);return false;' onMouseOut='javascript:highlight("cell_6_11",false);return false;'><a onClick='javascript:save(238);' class='character'>&#238;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_12' id='cell_6_12' height='20' onMouseOver='javascript:highlight("cell_6_12",true);return false;' onMouseOut='javascript:highlight("cell_6_12",false);return false;'><a onClick='javascript:save(239);' class='character'>&#239;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_13' id='cell_6_13' height='20' onMouseOver='javascript:highlight("cell_6_13",true);return false;' onMouseOut='javascript:highlight("cell_6_13",false);return false;'><a onClick='javascript:save(240);' class='character'>&#240;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_6_14' id='cell_6_14' height='20' onMouseOver='javascript:highlight("cell_6_14",true);return false;' onMouseOut='javascript:highlight("cell_6_14",false);return false;'><a onClick='javascript:save(241);' class='character'>&#241;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_7_0' id='cell_7_0' height='20' onMouseOver='javascript:highlight("cell_7_0",true);return false;' onMouseOut='javascript:highlight("cell_7_0",false);return false;'><a onClick='javascript:save(242);' class='character'>&#242;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_1' id='cell_7_1' height='20' onMouseOver='javascript:highlight("cell_7_1",true);return false;' onMouseOut='javascript:highlight("cell_7_1",false);return false;'><a onClick='javascript:save(243);' class='character'>&#243;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_2' id='cell_7_2' height='20' onMouseOver='javascript:highlight("cell_7_2",true);return false;' onMouseOut='javascript:highlight("cell_7_2",false);return false;'><a onClick='javascript:save(244);' class='character'>&#244;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_3' id='cell_7_3' height='20' onMouseOver='javascript:highlight("cell_7_3",true);return false;' onMouseOut='javascript:highlight("cell_7_3",false);return false;'><a onClick='javascript:save(245);' class='character'>&#245;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_4' id='cell_7_4' height='20' onMouseOver='javascript:highlight("cell_7_4",true);return false;' onMouseOut='javascript:highlight("cell_7_4",false);return false;'><a onClick='javascript:save(246);' class='character'>&#246;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_5' id='cell_7_5' height='20' onMouseOver='javascript:highlight("cell_7_5",true);return false;' onMouseOut='javascript:highlight("cell_7_5",false);return false;'><a onClick='javascript:save(247);' class='character'>&#247;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_6' id='cell_7_6' height='20' onMouseOver='javascript:highlight("cell_7_6",true);return false;' onMouseOut='javascript:highlight("cell_7_6",false);return false;'><a onClick='javascript:save(248);' class='character'>&#248;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_7' id='cell_7_7' height='20' onMouseOver='javascript:highlight("cell_7_7",true);return false;' onMouseOut='javascript:highlight("cell_7_7",false);return false;'><a onClick='javascript:save(249);' class='character'>&#249;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_8' id='cell_7_8' height='20' onMouseOver='javascript:highlight("cell_7_8",true);return false;' onMouseOut='javascript:highlight("cell_7_8",false);return false;'><a onClick='javascript:save(250);' class='character'>&#250;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_9' id='cell_7_9' height='20' onMouseOver='javascript:highlight("cell_7_9",true);return false;' onMouseOut='javascript:highlight("cell_7_9",false);return false;'><a onClick='javascript:save(251);' class='character'>&#251;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_10' id='cell_7_10' height='20' onMouseOver='javascript:highlight("cell_7_10",true);return false;' onMouseOut='javascript:highlight("cell_7_10",false);return false;'><a onClick='javascript:save(252);' class='character'>&#252;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_11' id='cell_7_11' height='20' onMouseOver='javascript:highlight("cell_7_11",true);return false;' onMouseOut='javascript:highlight("cell_7_11",false);return false;'><a onClick='javascript:save(253);' class='character'>&#253;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_12' id='cell_7_12' height='20' onMouseOver='javascript:highlight("cell_7_12",true);return false;' onMouseOut='javascript:highlight("cell_7_12",false);return false;'><a onClick='javascript:save(254);' class='character'>&#254;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_13' id='cell_7_13' height='20' onMouseOver='javascript:highlight("cell_7_13",true);return false;' onMouseOut='javascript:highlight("cell_7_13",false);return false;'><a onClick='javascript:save(255);' class='character'>&#255;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_7_14' id='cell_7_14' height='20' onMouseOver='javascript:highlight("cell_7_14",true);return false;' onMouseOut='javascript:highlight("cell_7_14",false);return false;'><a onClick='javascript:save(402);' class='character'>&#402;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_8_0' id='cell_8_0' height='20' onMouseOver='javascript:highlight("cell_8_0",true);return false;' onMouseOut='javascript:highlight("cell_8_0",false);return false;'><a onClick='javascript:save(913);' class='character'>&#913;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_1' id='cell_8_1' height='20' onMouseOver='javascript:highlight("cell_8_1",true);return false;' onMouseOut='javascript:highlight("cell_8_1",false);return false;'><a onClick='javascript:save(914);' class='character'>&#914;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_2' id='cell_8_2' height='20' onMouseOver='javascript:highlight("cell_8_2",true);return false;' onMouseOut='javascript:highlight("cell_8_2",false);return false;'><a onClick='javascript:save(915);' class='character'>&#915;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_3' id='cell_8_3' height='20' onMouseOver='javascript:highlight("cell_8_3",true);return false;' onMouseOut='javascript:highlight("cell_8_3",false);return false;'><a onClick='javascript:save(916);' class='character'>&#916;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_4' id='cell_8_4' height='20' onMouseOver='javascript:highlight("cell_8_4",true);return false;' onMouseOut='javascript:highlight("cell_8_4",false);return false;'><a onClick='javascript:save(917);' class='character'>&#917;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_5' id='cell_8_5' height='20' onMouseOver='javascript:highlight("cell_8_5",true);return false;' onMouseOut='javascript:highlight("cell_8_5",false);return false;'><a onClick='javascript:save(918);' class='character'>&#918;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_6' id='cell_8_6' height='20' onMouseOver='javascript:highlight("cell_8_6",true);return false;' onMouseOut='javascript:highlight("cell_8_6",false);return false;'><a onClick='javascript:save(919);' class='character'>&#919;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_7' id='cell_8_7' height='20' onMouseOver='javascript:highlight("cell_8_7",true);return false;' onMouseOut='javascript:highlight("cell_8_7",false);return false;'><a onClick='javascript:save(920);' class='character'>&#920;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_8' id='cell_8_8' height='20' onMouseOver='javascript:highlight("cell_8_8",true);return false;' onMouseOut='javascript:highlight("cell_8_8",false);return false;'><a onClick='javascript:save(921);' class='character'>&#921;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_9' id='cell_8_9' height='20' onMouseOver='javascript:highlight("cell_8_9",true);return false;' onMouseOut='javascript:highlight("cell_8_9",false);return false;'><a onClick='javascript:save(922);' class='character'>&#922;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_10' id='cell_8_10' height='20' onMouseOver='javascript:highlight("cell_8_10",true);return false;' onMouseOut='javascript:highlight("cell_8_10",false);return false;'><a onClick='javascript:save(923);' class='character'>&#923;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_11' id='cell_8_11' height='20' onMouseOver='javascript:highlight("cell_8_11",true);return false;' onMouseOut='javascript:highlight("cell_8_11",false);return false;'><a onClick='javascript:save(924);' class='character'>&#924;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_12' id='cell_8_12' height='20' onMouseOver='javascript:highlight("cell_8_12",true);return false;' onMouseOut='javascript:highlight("cell_8_12",false);return false;'><a onClick='javascript:save(925);' class='character'>&#925;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_13' id='cell_8_13' height='20' onMouseOver='javascript:highlight("cell_8_13",true);return false;' onMouseOut='javascript:highlight("cell_8_13",false);return false;'><a onClick='javascript:save(926);' class='character'>&#926;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_8_14' id='cell_8_14' height='20' onMouseOver='javascript:highlight("cell_8_14",true);return false;' onMouseOut='javascript:highlight("cell_8_14",false);return false;'><a onClick='javascript:save(927);' class='character'>&#927;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_9_0' id='cell_9_0' height='20' onMouseOver='javascript:highlight("cell_9_0",true);return false;' onMouseOut='javascript:highlight("cell_9_0",false);return false;'><a onClick='javascript:save(928);' class='character'>&#928;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_1' id='cell_9_1' height='20' onMouseOver='javascript:highlight("cell_9_1",true);return false;' onMouseOut='javascript:highlight("cell_9_1",false);return false;'><a onClick='javascript:save(929);' class='character'>&#929;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_2' id='cell_9_2' height='20' onMouseOver='javascript:highlight("cell_9_2",true);return false;' onMouseOut='javascript:highlight("cell_9_2",false);return false;'><a onClick='javascript:save(931);' class='character'>&#931;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_3' id='cell_9_3' height='20' onMouseOver='javascript:highlight("cell_9_3",true);return false;' onMouseOut='javascript:highlight("cell_9_3",false);return false;'><a onClick='javascript:save(932);' class='character'>&#932;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_4' id='cell_9_4' height='20' onMouseOver='javascript:highlight("cell_9_4",true);return false;' onMouseOut='javascript:highlight("cell_9_4",false);return false;'><a onClick='javascript:save(933);' class='character'>&#933;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_5' id='cell_9_5' height='20' onMouseOver='javascript:highlight("cell_9_5",true);return false;' onMouseOut='javascript:highlight("cell_9_5",false);return false;'><a onClick='javascript:save(934);' class='character'>&#934;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_6' id='cell_9_6' height='20' onMouseOver='javascript:highlight("cell_9_6",true);return false;' onMouseOut='javascript:highlight("cell_9_6",false);return false;'><a onClick='javascript:save(935);' class='character'>&#935;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_7' id='cell_9_7' height='20' onMouseOver='javascript:highlight("cell_9_7",true);return false;' onMouseOut='javascript:highlight("cell_9_7",false);return false;'><a onClick='javascript:save(936);' class='character'>&#936;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_8' id='cell_9_8' height='20' onMouseOver='javascript:highlight("cell_9_8",true);return false;' onMouseOut='javascript:highlight("cell_9_8",false);return false;'><a onClick='javascript:save(937);' class='character'>&#937;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_9' id='cell_9_9' height='20' onMouseOver='javascript:highlight("cell_9_9",true);return false;' onMouseOut='javascript:highlight("cell_9_9",false);return false;'><a onClick='javascript:save(945);' class='character'>&#945;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_10' id='cell_9_10' height='20' onMouseOver='javascript:highlight("cell_9_10",true);return false;' onMouseOut='javascript:highlight("cell_9_10",false);return false;'><a onClick='javascript:save(946);' class='character'>&#946;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_11' id='cell_9_11' height='20' onMouseOver='javascript:highlight("cell_9_11",true);return false;' onMouseOut='javascript:highlight("cell_9_11",false);return false;'><a onClick='javascript:save(947);' class='character'>&#947;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_12' id='cell_9_12' height='20' onMouseOver='javascript:highlight("cell_9_12",true);return false;' onMouseOut='javascript:highlight("cell_9_12",false);return false;'><a onClick='javascript:save(948);' class='character'>&#948;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_13' id='cell_9_13' height='20' onMouseOver='javascript:highlight("cell_9_13",true);return false;' onMouseOut='javascript:highlight("cell_9_13",false);return false;'><a onClick='javascript:save(949);' class='character'>&#949;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_9_14' id='cell_9_14' height='20' onMouseOver='javascript:highlight("cell_9_14",true);return false;' onMouseOut='javascript:highlight("cell_9_14",false);return false;'><a onClick='javascript:save(950);' class='character'>&#950;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_10_0' id='cell_10_0' height='20' onMouseOver='javascript:highlight("cell_10_0",true);return false;' onMouseOut='javascript:highlight("cell_10_0",false);return false;'><a onClick='javascript:save(951);' class='character'>&#951;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_1' id='cell_10_1' height='20' onMouseOver='javascript:highlight("cell_10_1",true);return false;' onMouseOut='javascript:highlight("cell_10_1",false);return false;'><a onClick='javascript:save(952);' class='character'>&#952;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_2' id='cell_10_2' height='20' onMouseOver='javascript:highlight("cell_10_2",true);return false;' onMouseOut='javascript:highlight("cell_10_2",false);return false;'><a onClick='javascript:save(953);' class='character'>&#953;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_3' id='cell_10_3' height='20' onMouseOver='javascript:highlight("cell_10_3",true);return false;' onMouseOut='javascript:highlight("cell_10_3",false);return false;'><a onClick='javascript:save(954);' class='character'>&#954;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_4' id='cell_10_4' height='20' onMouseOver='javascript:highlight("cell_10_4",true);return false;' onMouseOut='javascript:highlight("cell_10_4",false);return false;'><a onClick='javascript:save(955);' class='character'>&#955;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_5' id='cell_10_5' height='20' onMouseOver='javascript:highlight("cell_10_5",true);return false;' onMouseOut='javascript:highlight("cell_10_5",false);return false;'><a onClick='javascript:save(956);' class='character'>&#956;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_6' id='cell_10_6' height='20' onMouseOver='javascript:highlight("cell_10_6",true);return false;' onMouseOut='javascript:highlight("cell_10_6",false);return false;'><a onClick='javascript:save(957);' class='character'>&#957;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_7' id='cell_10_7' height='20' onMouseOver='javascript:highlight("cell_10_7",true);return false;' onMouseOut='javascript:highlight("cell_10_7",false);return false;'><a onClick='javascript:save(958);' class='character'>&#958;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_8' id='cell_10_8' height='20' onMouseOver='javascript:highlight("cell_10_8",true);return false;' onMouseOut='javascript:highlight("cell_10_8",false);return false;'><a onClick='javascript:save(959);' class='character'>&#959;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_9' id='cell_10_9' height='20' onMouseOver='javascript:highlight("cell_10_9",true);return false;' onMouseOut='javascript:highlight("cell_10_9",false);return false;'><a onClick='javascript:save(960);' class='character'>&#960;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_10' id='cell_10_10' height='20' onMouseOver='javascript:highlight("cell_10_10",true);return false;' onMouseOut='javascript:highlight("cell_10_10",false);return false;'><a onClick='javascript:save(961);' class='character'>&#961;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_11' id='cell_10_11' height='20' onMouseOver='javascript:highlight("cell_10_11",true);return false;' onMouseOut='javascript:highlight("cell_10_11",false);return false;'><a onClick='javascript:save(962);' class='character'>&#962;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_12' id='cell_10_12' height='20' onMouseOver='javascript:highlight("cell_10_12",true);return false;' onMouseOut='javascript:highlight("cell_10_12",false);return false;'><a onClick='javascript:save(963);' class='character'>&#963;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_13' id='cell_10_13' height='20' onMouseOver='javascript:highlight("cell_10_13",true);return false;' onMouseOut='javascript:highlight("cell_10_13",false);return false;'><a onClick='javascript:save(964);' class='character'>&#964;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_10_14' id='cell_10_14' height='20' onMouseOver='javascript:highlight("cell_10_14",true);return false;' onMouseOut='javascript:highlight("cell_10_14",false);return false;'><a onClick='javascript:save(965);' class='character'>&#965;</a></td>
					</tr>
					<tr>
						<td class="speciacharacter" width='20' align='center' name='cell_11_0' id='cell_11_0' height='20' onMouseOver='javascript:highlight("cell_11_0",true);return false;' onMouseOut='javascript:highlight("cell_11_0",false);return false;'><a onClick='javascript:save(966);' class='character'>&#966;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_11_1' id='cell_11_1' height='20' onMouseOver='javascript:highlight("cell_11_1",true);return false;' onMouseOut='javascript:highlight("cell_11_1",false);return false;'><a onClick='javascript:save(967);' class='character'>&#967;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_11_2' id='cell_11_2' height='20' onMouseOver='javascript:highlight("cell_11_2",true);return false;' onMouseOut='javascript:highlight("cell_11_2",false);return false;'><a onClick='javascript:save(968);' class='character'>&#968;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_11_3' id='cell_11_3' height='20' onMouseOver='javascript:highlight("cell_11_3",true);return false;' onMouseOut='javascript:highlight("cell_11_3",false);return false;'><a onClick='javascript:save(969);' class='character'>&#969;</a></td>
						<td class="speciacharacter" width='20' align='center' name='cell_11_4' id='cell_11_4' height='20' onMouseOver='javascript:highlight("cell_11_4",true);return false;' onMouseOut='javascript:highlight("cell_11_4",false);return false;'><a onClick='javascript:save(977);' class='character'>&#977;</a></td>
						<td width="20" height="20">&#160;</td>
						<td width="20" height="20">&#160;</td>
						<td width="20" height="20">&#160;</td>
						<td width="20" height="20">&#160;</td>
						<td width="20" height="20">&#160;</td>
						<td width="20" height="20">&#160;</td>
						<td width="20" height="20">&#160;</td>
						<td width="20" height="20">&#160;</td>
						<td width="20" height="20">&#160;</td>
						<td width="20" height="20">&#160;</td>
					</tr>
					<tr>
						<td align="center" colspan="15">					
						<input class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="Cancel"></td>
					</tr></table>
					</div>
				</p></span>
			</div>
		</div>
	</form>
</body>
</html>
