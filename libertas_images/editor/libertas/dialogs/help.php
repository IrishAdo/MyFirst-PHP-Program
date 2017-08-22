<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/11/15 17:24:12 $
	-	$Revision: 1.5 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';

$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$domain 	= $_SERVER["HTTP_HOST"];
$base_href 	= "http://$domain".check_parameters($_GET,"base_href","/");
$editor		= check_parameters($_GET,"editor","");

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
<title>Libertas Editor Online Help</title>
<meta http-equiv="Pragma" content="no-cache">
<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/help.css">
</head>
<body>
<a name='#top'/>
<div style="border: 1 solid Black; padding: 5 5 5 5;">
<p id="tableProps" class="tablePropsTitle"><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_help.gif'/>Help</p>
	<table width='380px' border="0">
			<tr>
				<td id='HelpInformation'></td>
			</tr>
		</table>
		</div>
	</form>
<script language="javascript" src="utils.js"></script>
<script>
<!--
	var winOpener = window.dialogArguments.document.parentWindow;
	var base_href ='<?php print $base_href; ?>';
	var session_url ='<?php print $session_url; ?>';
	var editor ='<?php print $editor; ?>';
	
	var help_list = Array(
Array("copy",			 "cut_copy_paste", "Copy", "Ctrl + c", "Copies the selected content into the clipboard"),
Array("cut",			 "cut_copy_paste", "Cut", "Ctrl + x", "Removes the selected content and places it in the clipboard for later use"),
Array("paste",			 "cut_copy_paste", "Paste", "Ctrl + v", "Paste contents of clipboard into page keeping all formating etc",Array(
Array("paste_plain",	 "paste_special", "Paste (plain text)", "", "Paste contents of clipboard into page as plain text, removes all formating including tables"),
Array("paste_special",	 "paste_special", "Paste (from word)", "", "Paste contents of clipboard into page as cleaned text.  Removes all Microsoft word formating including fonts and styles.  Tables and headings remain intact with this option.")
)),
Array("cleanup",		 "tidy", "Tidy", "", "Runs the clean tool to remove all formating from the selected text or the entire page if no text is selected."),

Array("undo",			 "undo_redo", "Undo", "Ctrl + y", "Undoes the last action in the editor"),
Array("redo",			 "undo_redo", "Redo", "Ctrl + z", "Redoes the last action in the editor"),

Array("find",			 "find_replace", "Find", "", "Allows all content in the editor to be searched"),
Array("replace",		 "find_replace", "Replace", "", "Searches through all content in the editor replacing items that match the search term with those that match the replace term."),

Array("hyperlink",		 "internal_links", "Internal Link", "", "Insert a link to a page within this website."),
Array("externallink",	 "external_links", "External Link", "", "Insert hyperlink to external website"),
Array("emaillink",		 "email_links", "Email", "", "Inserts email link into document.  If text is selected that text will become an email link, if no text is selected the email address will also be inserted into the body of the text."),
Array("filelink",		 "file_links", "Insert file", "", "Insert link file contained within the website that has been uploaded using Libertas Solutions CMS."),
Array("unlink",			 "unlink", "Remove Link", "", "Removes any links from the selected text.  Removes internal, external, email and file links."),

Array("flash",			 "embed_flash", "Flash", "", "Insert flash file that has been uploaded using Libertas Solutions CMS.  The use of flash is not recommended due to the conflicts between this tecnology and the Web Accessability Initive (WAI) guidelines."),
Array("movie",			 "embed_movie", "Insert Movie", "", "Embed windows media player movies into page (avi, mpeg, "),
Array("audio", 			 "embed_audio", "Insert Audio", "", "Embed an audion file in page"),

Array("image_insert",	 "images", "Image", "", "Insert an image that has been uploaded to the site with Libertas Solutions CMS",Array(
Array("image_prop",		 "images", "Image Properties", "", "View and change the properties of an image, including its alignment and alternative (alt) text for WAI.")
)),

Array("subscript",		 "sub_super_strike", "Subscript", "", "Changes the selected text into subscript."),
Array("superscript",	 "sub_super_strike", "Superscript", "", "Changes the selected text into supperscript."),
Array("strikethrough",	 "sub_super_strike", "Strikethrough", "", "Strikes a line through the slected text."),

Array("hr",				 "hr", "Line", "", "Inserts a horizontal rule / line at the selected position in the editor"),
Array("special_character","special_character", "Special Characters", "", "Opens the special character window to allow a special character to be inserted into the editor at the selected position."),

Array("form",			 "embed_form", "Insert form", "", "Embed a form in document"),
Array("spell",			 "spell_checker", "Spell Check", "", "Check spelling within page."),
Array("acronym",		 "abbr_acronym", "Acronyms", "", "Define an Acronym on the page to help others understand what <ACRONYM title='World Wide Web'>www</ACRONYM> means for example. place your mouse cursor over the letters <ACRONYM title='World Wide Web'>www</ACRONYM>. In the Editor Acronyms will be displayed with a red dashed line around them for editorial access and eas of use, this will not be the case in the preview mode or on the site."),
Array("stats",			 "page_properties", "Page Properties", "", "Details useful information such as a word count, page size and estimated download time for page on a 56k modem."),

Array("set_zoom",		 "set_zoom", "Set Magification", "", "Change the magification level of the content.  Only modifies display size does not save content at magification."),
Array("font_header",	 "headings", "Header", "Ctrl + Alt 1-3", "Change the selected text into a heading 1-7.  <br></br> Heading 1 = Ctrl + Alt 1<br></br>Heading 2 = Ctrl + Alt 2 <br></br>Heading 3 = Ctrl + Alt 3"),
Array("font_family",	 "font_face", "Font", "", "Change the font of selected text"),
Array("font_size",		 "font_size", "Font Size", "", "Change the font size for the selected text."),

Array("bold", 			 "bold_italic_underline", "Bold", "Ctrl + b", "Bolds the selected text"),
Array("italic",			 "bold_italic_underline", "Italic", "Ctrl + i", "Italitises the selected text."),
Array("underline",		 "bold_italic_underline", "Underline", "Ctrl + u", "Underlines the selected text."),
	
Array("left", 			 "justification", "Left Justify", "", "Changes the justifation of selected text to be left justified."),
Array("center",			 "justification", "Center justify", "", "Centers the selected content"),
Array("right",			 "justification", "Right Justify", "", "Changes the justifation of selected text to be right justified."),
Array("justify",		 "justification", "Justify", "", "Changes the justifation of selected text to justified."),

Array("indent",			 "indent_unindent", "Indent", "", "Intents the selected text."),
Array("unindent",		 "indent_unindent", "Unindent", "", "Unindents the selected text if indented."),

Array("ordered_list",	 "bullet", "Numbered List", "", "Inserts a numbered list, sometimes refered to as an ordered list."),
Array("bulleted_list",	 "bullet", "Bulleted list", "", "Inserts a bulletpoint list, sometimes refered to as an unordered list."),


Array("fore_color",		 "fore_colour", "Foreground colour", "", "Changes the background colour of the selected text"),
Array("bg_color", 		 "background_colour", "Background colour", "", "Changes the background colour of the selected text"),


Array("table_create",	 "tables_basic", "Create Table", "", "Inserts a table into the editor at the selected position.",Array(
Array("table_prop",		 "tables_basic", "Table Properties", "", "Change the properties of the selected table including editing the table summary for WAI compliance."),
Array("toggle_borders",	 "tables_basic", "Show Borders", "", "Displays the borders of all tables as a dashed line to make working with them easier."),
Array("table_cell_prop", "table_cell", "Cell properties", "", "View and change the properties of the selected cell."),

Array("table_cell_merge_down", "tables_split_merge", "Merge down", "", "Merges the selected table cell downwards."),
Array("table_cell_merge_right", "tables_split_merge", "Merge right", "", "Merges the selected table cell to the right."),
Array("table_cell_split_horizontal", "tables_split_merge", "Horizontal cell split", "", "Splits selected cell in two horizontally."),
Array("table_cell_split_vertical", "tables_split_merge", "Vertical cell split", "", "Splits selected cell in two vertically."),

Array("table_column_delete", "tables_row_column", "Delete column", "", "Deletes selected column"),
Array("table_column_insert", "tables_row_column", "Insert column", "", "Inserts new column to the right of the selected column."),
Array("table_row_delete","tables_row_column", "Delete Row", "", "Deletes the selected row."),
Array("table_row_insert","tables_row_column", "Insert Row", "", "Inserts new row below the selected row."),

Array("table_row_prop",	 "tables_row_props", "Row Properties", "", "Change the properties of an individual row."),

Array("table_set_background", "background", "Table Background Colour", "", "Set the background colour for the selected table."),
Array("table_set_cell_backgroun", "background", "Cell Background Colour", "", "Set the background colour for the selected cell.")
)),

Array("emoc",			 "emocs_icons", "Emotions", "", "Insert emotions icons such as smiles")
)
sz="";
/*	
	sz ="<ul>";
	for (i=0; i < help_list.length ; i++){
		if (winOpener.has_function(editor,'libertas_configuration_'+help_list[i][1])){
			sz+="<li><a href='#num"+i+"'>"+help_list[i][2]+"</a></li>";
		}
	}
	sz +="</ul>";
*/
	for (i=0; i < help_list.length ; i++){
		if (winOpener.has_function(editor,help_list[i][0])){
			if (help_list[i].length==5){
				sz+="<table width='100%'><tr><td colspan='2' width='100%'><strong>"+help_list[i][2]+"</strong>";
				if (help_list[i][3]!='')
					sz+=" ("+help_list[i][3]+")";
				sz+="</td></tr>";
				sz+="<tr><td width='30' valign='top'><a name='num"+i+"'></a><img align='left' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_"+help_list[i][0]+".gif'></td><td width='100%'>";
				sz += ""+help_list[i][4]+"</td></tr>";
				sz+="<tr><td colspan='2' width='100%'><hr></td></tr></table>";
			} else {
				sz+="<table width='100%'><tr><td colspan='2' width='100%'><strong>"+help_list[i][2]+"</strong>";
				if (help_list[i][3]!='')
					sz+=" ("+help_list[i][3]+")";
				sz+="</td></tr>";
				sz+="<tr><td valign='top' rowspan=2><a name='num"+i+"'></a><img align='left' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_"+help_list[i][0]+".gif'><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_dropdown.gif'></td><td width='100%' valign='top' >";
				sz += ""+help_list[i][4]+"<br>";
				sz+="</td></tr>";
				sz+="<tr><td width='100%'>";
				for (j=0; j < help_list[i][5].length ; j++){
					if (winOpener.has_function(editor,help_list[i][5][j][0])){
						sz+="<p><a name='num"+i+"'></a><img align='left' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_"+help_list[i][5][j][0]+".gif'><strong>"+help_list[i][5][j][2]+"</strong>";
						sz += "<br>"+help_list[i][5][j][4]+"<br>";
					}
				}
				sz+="</td></tr>";
				sz+="<tr><td colspan=2><hr></td></tr>";
				sz+="</table>";
			}
			//sz+="<br>"+help_list[i][4]+"<br><a href='#top'>Back to Top</a></p>";
		}
	}
	document.all['HelpInformation'].innerHTML = sz;
//-->
</script>
</body>
</html>
