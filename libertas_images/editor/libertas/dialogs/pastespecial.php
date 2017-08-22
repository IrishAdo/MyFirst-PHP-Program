<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:05 $
	-	$Revision: 1.2 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';

$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$base_href = check_parameters($_GET,"base_href","/");
$todo = check_parameters($_GET,"ToDo","find");
function check_parameters($arr,$ind,$def=""){
	if (isset($arr[$ind])){
		return $arr[$ind];
	} else {
		return $def;
	}
}

?>
<HTML>
<HEAD>
 <link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
<TITLE>Find / Replace</TITLE>
<script language="JavaScript">
// init stuff
var rng;
rng = dialogArguments.document.selection.createRange();

// returns a calculated value for matching case and matching whole words
function searchtype(){
    var retval = 0;
    var matchcase = 0;
    var matchword = 0;
	var sDir=0;
	if (document.frmSearch.searchDir[0].checked) sDir = 1;
    if (document.frmSearch.blnMatchCase.checked) matchcase = 4;
    if (document.frmSearch.blnMatchWord.checked) matchword = 2;
    retval = matchcase + matchword + sDir;
    return(retval);
}

function checkInput(){
    if (document.frmSearch.strSearch.value.length < 1) {
        alert("Please enter text in the 'Find what:' field.");
        return false;
    } else {
        return true;
    }
}

// find the text I want
function findtext(){
    if (checkInput()) {
        var searchval = document.frmSearch.strSearch.value;
        rng.collapse(true);
		if (document.frmSearch.searchDir[0].checked)
			rng.setEndPoint("EndToStart", rng);
		else 
			rng.setEndPoint("StartToEnd", rng);
        if (rng.findText(searchval, 10000000000, searchtype())) {
            rng.select();
        } else {
            var startfromtop = confirm("Your word was not found.Would you like to start again from the top?");
            if (startfromtop) {
                rng.expand("textedit");
                rng.collapse();
                rng.select();
                findtext();
            }
        }
    }
}

// replace the selected text
function replacetext(){
    if (checkInput()) {
        if (document.frmSearch.blnMatchCase.checked){
            if (rng.text == document.frmSearch.strSearch.value) 
				rng.text = document.frmSearch.strReplace.value
        } else {
            if (rng.text.toLowerCase() == document.frmSearch.strSearch.value.toLowerCase()) 
				rng.text = document.frmSearch.strReplace.value
        }
        findtext();
    }
}

function replacealltext(){
    if (checkInput()) {
        var searchval = document.frmSearch.strSearch.value;
        var wordcount = 0;
        var msg = "";
        rng.expand("textedit");
        rng.collapse();
        rng.select();
        while (rng.findText(searchval, 1000000000, searchtype())){
            rng.select();
            rng.text = document.frmSearch.strReplace.value;
            wordcount++;
        }
        if (wordcount == 0) msg = "Word was not found. Nothing was replaced."
        else msg = wordcount + " word(s) were replaced.";
        alert(msg);
    }
}
</script>

</HEAD>
<BODY>
<FORM NAME="frmSearch" method="post" action="" onSubmit="return false;">
<TABLE CELLSPACING="0" cellpadding="5" border="0">
<TR>
<TD VALIGN="top" align="left" nowrap style="font-family:Arial; font-size:11px;" colspan="2">
    <label for="strSearch">Find what:</label><br>
    <INPUT TYPE=TEXT SIZE=40 NAME=strSearch id="strSearch" style="width : 280px;"><br>
<?php
if ($todo=='replace'){
?>
    <label for="strReplace">Replace with:</label><br>
    <INPUT TYPE=TEXT SIZE=40 NAME=strReplace id="strReplace" style="width : 280px;"><br>
<?php
}
?>
</td>
</tr>
<TR>
<TD VALIGN="top" align="left" nowrap style="font-family:Arial; font-size:11px;">
    <INPUT TYPE=Checkbox SIZE=40 NAME=blnMatchCase ID="blnMatchCase"><label for="blnMatchCase">Match case</label><br>
    <INPUT TYPE=Checkbox SIZE=40 NAME=blnMatchWord ID="blnMatchWord"><label for="blnMatchWord">Match whole word only</label>
</td>
<td>
Search Direction::<br/><input type=radio id=searchUp name=searchDir value='Up'><label for=searchUp>Up</label><input type=radio id=searchDown checked name=searchDir value='Down'><label for=searchDown>Down</label>

</td>
</tr>
<tr>
<td valign="top" colspan="2">
<?php
if ($todo=='find'){
?>
    <button class="bt" name="btnFind" style="width:75px;" onClick="findtext();">Find Next</button>&nbsp;
<?php
}
if ($todo=='replace'){
?>
    <button class="bt" name="btnReplace" onClick="replacetext();">Replace</button>&nbsp;
    <button class="bt" name="btnReplaceall" onClick="replacealltext();">Replace All</button>&nbsp;
<?php
}
?>
    <button class="bt" name="btnCancel" onClick="window.close();">Close</button>
</td>

</tr></table>
</FORM>
</BODY>
</HTML>
