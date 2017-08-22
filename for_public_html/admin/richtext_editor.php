
<HTML>
<base href="<?PHP
print "http://".$_SERVER["HTTP_HOST"];	
if ($_SERVER["SERVER_PORT"]!=80){
	print ":".$_SERVER["SERVER_PORT"];
}

$base="";
		if (strpos($_SERVER["PHP_SELF"],"~")===0){
			$base ="/";
		}else{
			$start= strpos($_SERVER["PHP_SELF"], "~");
			$end  = strpos($_SERVER["PHP_SELF"], "/",$start);
			$base = substr($_SERVER["PHP_SELF"], 0,$end+1);
		}
print $base;
?>">
<HEAD>
<SCRIPT>

aLocation = new Array()

function fillLocationsOnLoad()
{


// for example
// addLItem("-1","Government%20Legislation%20Programme%20","eng")
}

function addLItem(ID,optionText, lang, INFO_EXIST)
{
	aLocation[aLocation.length] = new aLocationItem(ID,  unescape(optionText), lang, INFO_EXIST )
}
// synch changes
<?PHP 


	if (strlen(session_id())>0){
		print "session_url = '".session_name()."=".session_id()."';\n";
	}else{
		print "session_url = '".session_name()."=".$_GET["PHPSESSID"]."';\n";
	}
?>

rte_res___0_loaded = false
rte___0_loaded = false
doc_loaded = false
</SCRIPT>
	<SCRIPT SRC="admin/scripts/richtext_config.js"></SCRIPT>
	<SCRIPT SRC="admin/scripts/richtext_functionality.js"></SCRIPT>		
	<STYLE>
    	H1 { FONT-SIZE: 10pt; color:#0053a7}
		p {FONT-SIZE: 10pt;}
		body {margin:0pt;border:none;padding:0pt;Font-family:verdana;FONT-SIZE: 9pt; color:#0053a7; display:block; color:#0066ff}
		#tbDBSelect {display:none;text-align:left;width: 100;margin-right: 1pt;margin-bottom: 0pt;margin-top: 0pt;padding: 0pt; color:#0066ff}
		#DBSelect, #idMode, .userButton {font:8pt arial; color:#0066ff}
		#DBSelect {width:100; color:#0066ff}
		#idMode {margin-top:0pt; color:#0066ff}
		.tbButton {text-align:left;margin:0pt 1pt 0pt 0pt;padding:0pt}
		#EditBox {position: relative; color:#0066ff}
		.center {color:#ff00ff}
	</STYLE>
	<STYLE ID=skin DISABLED>
		body {margin:0pt;border:none;padding:0pt;Font-family:verdana;FONT-SIZE: 9pt; color:#0053a7; display:block;}
		#EditBox {margin: 0px 11px 0px 11px;Font-family:verdana;FONT-SIZE: 7pt; color:#0066ff}
		#tbUpRight, #tbUpLeft {width:20px}	
		#idMode {margin-left:11px;padding:0pt}
		#idMode LABEL {color: navy;text-decoration: underline}
		#tbTopBar {height:19px}
		#tbButtons, #tbContents {background: #FFFFE5;vertical-align: top}
		#tbContents {padding:0px 5px;color:green;}
		#tbBottomBar {height:6px}
	</STYLE>
	<STYLE ID=defPopupSkin>
		#popup BODY {margin:0px;border-top:none;}
		#popup .colorTable {height:91px}
		#popup #header {width:100%}
		#popup #close {cursor:default;font:bold 8pt system;width:16px;text-align: center}
		#popup #content {padding:10pt}
		#popup TABLE {vertical-align:top}
		#popup .tabBody {border:1px black solid;border-top: none; color:#0066ff}
		#popup .tabItem, #popup .tabSpace {border-bottom:1px black solid;border-left:1px black solid}
		#popup .tabItem {border-top:1px black solid;font:10pt arial,geneva,sans-serif;}
		#popup .currentColor {width:20px;height:20px; margin: 0pt;margin-right:15pt;border:1px black solid; color:#0066ff}
		#popup .tabItem DIV {margin:3px;padding:0px;cursor: hand}
		#popup .tabItem DIV.disabled {color: gray;cursor: default}
		#popup .selected {font-weight:bold}
		#popup .loc {color:black}
		#popup .item {color:green}
		#popup .emoticon {cursor:hand}
	</STYLE>
	<STYLE ID=popupSkin>
		#popup BODY {border: 1px #000000 solid; background: #CCCCCC}
		#popup #header {background: #004080; color: white}
		#popup #caption {text-align: left;font: bold 12pt arial , geneva, sans-serif}
		#popup .ColorTable, #popup #idList TD#current {border: 1px black solid}
		#popup #idList TD{cursor: hand;border: 1px #F1F1F1 solid}
		#popup #close {border: 0px #ffffff solid;cursor:hand;color: #ffffff;font-weight: bold;margin-right: 0px;padding:0px 0px 0px 0px}
		#popup #tableProps .tablePropsTitle {color:#006699;text-align:left;margin:0pt;border-bottom: 1px black solid;margin-bottom:5pt}
		#tableButtons, #tableProps {padding:5px}
		#popup #tableContents {height:175px}
		#popup #tableProps .tablePropsTitle, #popup #tableProps, #popup #tableProps TABLE {font:bold 9pt Arial, Geneva, Sans-serif}
		#popup #tableOptions  {font:9pt Arial, Geneva, Sans-serif;padding:15pt 5pt}
		#popup #puDivider {background:black;width:1px}
		#popup #content {margin: 0pt;padding:5pt 5pt 10pt 5pt}
		#popup #ColorPopup {width: 250px}
		#popup .ColorTable TR {height:6px}
		#popup .ColorTable TD {width:6px;cursor:hand}
		#popup .block P,#popup .block H1,#popup .block H2,#popup .block H3,
		#popup .block H4, #popup .block H5,#popup .block H6,#popup .block PRE {margin:0pt;padding:0pt;}
		#popup #customFont {font:12pt Arial;text-decoration:italic}
	</STYLE>
	<SCRIPT>
		var g_state
// synch changes
		window.onload	= initAll
	
function initAll()
{
	if (rte_res___0_loaded && rte___0_loaded && doc_loaded){
		all_loaded = true;
		_initEditor();
	} else {
		setTimeout("initAll()",1000);
		
	}
}
// synch changes end
	</SCRIPT>
</HEAD>													
<BODY ONCONTEXTMENU="return false" TABINDEX  ="-1" SCROLL ="no" ONSELECTSTART ="return false" ONDRAGSTART="return false" ONSCROLL="return false">
	<DIV ID="idEditor" STYLE="VISIBILITY:hidden">
		<TABLE ID=idToolbar WIDTH="100%" CELLSPACING=0 CELLPADDING=0 ONCLICK="_CPopup_Hide()">
			<TR ID=tbTopBar><TD ID=tbUpLeft></TD><TD COLSPAN=2 ID=tbUpMiddle></TD><TD ID=tbUpRight></TD></TR>
			<TR><TD ID=tbMidLeft></TD>
				<TD ID=tbContents><SCRIPT>_drawToolbar()</SCRIPT></TD>
				<TD ID=tbButtons ALIGN=right></TD><TD ID=tbMidRight></TD>
			</TR>
			<TR ID=tbbottomBar><TD ID=tbLowLeft></TD><TD COLSPAN=2 ID=tbLowMiddle></TD><TD ID=tbLowRight></TD></TR>
		</TABLE>
		<IFRAME NAME="idPopup" STYLE="HEIGHT: 200px; LEFT: 25px; MARGIN-TOP: 8px; POSITION: absolute; VISIBILITY: hidden; WIDTH: 200px; Z-INDEX: -1"></IFRAME>
		<IFRAME ID="EditBox" NAME="idEditbox" WIDTH="100%" HEIGHT="100%" ONFOCUS="_CPopup_Hide()"></IFRAME>
		<DIV ID="tbmode"><SCRIPT>_drawModeSelect()</SCRIPT></DIV>
	</DIV>
	<form name=frmDoc>
	  <input type=hidden name="urllist" value="<option>Select existing Approved Document</option>">
	  <input type="hidden" name="imagelist" >
	</form>
	<Script>
	<!--
	 v = document.frmDoc.imagelist.value;
	 v = v.split('<v=').join("<option value=")
	 document.frmDoc.imagelist.value= v;

	 doc_loaded = true
	//-->
	</script>
</BODY>	
</HTML>

