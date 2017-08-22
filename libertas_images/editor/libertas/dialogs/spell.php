<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/08/25 07:35:03 $
	-	$Revision: 1.2 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
?>
<html>
<head>
  <title>Checking Spelling</title>
  <meta http-equiv="Pragma" content="no-cache">
  <link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css"/>
</head>
</head>
  <script language="javascript" src="utils.js"></script>

<body onLoad="processForm();">
<form name="spell_checker" method="post" action="">
<input type="hidden" name="theme" value="default">
<input type="hidden" name="lang" value="en">
<input type="hidden" name="images" value="">
<div style="border: 1 solid Black; padding: 5 5 5 5;">

<DIV ID='internalprop1'>
<P id=tableProps CLASS=tablePropsTitle>Checking Spelling</P>
<table>
	<tr><td align='right' style='font-size:10px'>Not found:</td><td><input type=text disabled='true' style='width:200px;' size=20 value='' name='misspeltword' id='misspeltword'></td><td></td></tr>
	<tr><td align='right' style='font-size:10px'>Change to:</td><td><input type=text style='width:200px;' size=20 value='' name='changeto' id='changeto'></td><td></td></tr>
	<tr><td align='right' style='font-size:10px'>Suggestions:</td><td><select size=5 style='width:200px;height:80px' name='suggestedwords' id='suggestedwords' onclick="javascript:move_to_change(this)"></td><td><table>
	<tr><td><input type='button' class='bt' onClick='closeSpell("ignore")' value='Ignore' style='width:75px;'></td><td>
	<input type='button' class='bt' onClick='closeSpell("ignore_all")' value='Ignore All' style='width:75px;'></td></tr>
	<tr><td><input type='button' class='bt' onClick='closeSpell("change")' value='Change' style='width:75px;'></td><td>
	<input type='button' class='bt' onClick='closeSpell("change_all")' value='Change All' style='width:75px;'></td></tr>
	<tr><td></td><td><input type='button' class='bt' onClick='closeSpell("end")' value='Cancel' style='width:75px;'></td></tr>
	</table></td></tr>
</table>
</DIV>
</div>

</form>

	<script>
		function processForm(){
			var spellProps = window.dialogArguments;
			resizeDialogToContent();
			document.spell_checker.misspeltword.value = spellProps.misspeltword
			
			document.spell_checker.changeto.value = spellProps.misspeltword
			suggestions = spellProps.suggestions
			for (index=0;index<suggestions.length;index++){
				if (index==0){
					document.spell_checker.changeto.value = suggestions[index];
				}
				document.spell_checker.suggestedwords.options[document.spell_checker.suggestedwords.options.length] = new Option(suggestions[index], suggestions[index]);
			}
		}					

		function closeSpell(cmd){
			if (document.spell_checker.changeto.value.length!=0){
			var r = new Object();
				r.command = cmd;
				r.thisWord = document.spell_checker.misspeltword.value;
				r.withThis = document.spell_checker.changeto.value;
				window.returnValue = r;
				window.close();
			} else {
				alert("You have not specified a word to change to");
			}
		}		
		
		function move_to_change(t){
			document.spell_checker.changeto.value = t.options[t.options.selectedIndex].value
		}
	</script>
</body>
</html>
