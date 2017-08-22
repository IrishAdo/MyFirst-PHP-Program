/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 DHTML Editor
 
 This editor is designed to work only in IE modifications required to work on mozilla.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

var spellRange						= new Object();
var spellIgnore						= new Array();
var libertas_editors				= new Array();
var libertas_editors_type			= new Array();
var libertas_context_html 			= "";
var image_window					= new Object();	
var myPopup							= new Object();
var myForegroundColor				= "__REMOVE__";
var myBackgroundColor				= "__REMOVE__";
var previous_design_mode			= "design";
var myEditor						= "";
var state_EditingSource				= false; //true if current control is in html mode
var readyState						= false;
var popup_blocker_disabled 			= "";

function LIBERTAS_editorInit(editor, css_stylesheet, direction,textonly){
	alert('ssss');
	// prevent from executing twice on the same editor
	if (!LIBERTAS_editor_registered(editor)){
		// check if the editor completely loaded and schedule to try again if not
		if (this[editor+'_rEdit'].document.readyState != 'complete'){
			setTimeout("LIBERTAS_editorInit('"+editor+"', '"+css_stylesheet+"', '"+direction+"', '"+textonly+"');",200);
			return;
		}
		
//		this[editor+'_rEdit'].textonly 					= (textonly+''=='undefined')?'':textonly;
		this[editor+'_rEdit'].document.designMode 		= 'On';
		this[editor+'_rEdit'].ShowDetails 				= 'On';
		this[editor+'_rEdit'].currentDesignMode 		= 'On';
		this[editor+'_rEdit'].document.oncontextmenu 	= function(){setContextMenu(editor);return false;}
		this[editor+'_rEdit'].document.ondblclick		= function(){setdoubleclick(editor);return false;}
		this[editor+'_rEdit'].menu						= new Menu();
		this[editor+'_rEdit'].grid 						= new Grid();
	// register the editor 
		libertas_editors[libertas_editors.length] 			= editor;
		libertas_editors_type[libertas_editors_type.length]	= (textonly+''=='undefined') ? '' : textonly ;
	// add on submit handler
		LIBERTAS_addOnSubmitHandler(editor);
		document.all[editor].value 						+= "";
		document.all[editor].value = document.all[editor].value.replace(/&quot;/g,'"');
		setTimeout("LIBERTAS_editorswitch('"+editor+"', '"+css_stylesheet+"', '"+direction+"');",100);
	}
}	


// returns true if editor is already registered
function LIBERTAS_editor_registered(editor){
	var found = false;
	for(i=0;i<libertas_editors.length;i++){
		if (libertas_editors[i] == editor){
			found = true;
			break;
		}
	}
	return(found);
}
	
// onsubmit
function LIBERTAS_UpdateFields(){
	for (i=0; i<libertas_editors.length; i++){
		LIBERTAS_updateField(libertas_editors[i], null);
	}
}
	
// adds event handler for the form to update hidden fields
function LIBERTAS_addOnSubmitHandler(editor){
	thefield = LIBERTAS_getFieldByEditor(editor, null);
	
	var sTemp = "";
	oForm = document.all[thefield].form;
	if(oForm.onsubmit != null) {
		sTemp = oForm.onsubmit.toString();
		iStart = sTemp.indexOf("{") + 2;
		sTemp = sTemp.substr(iStart,sTemp.length-iStart-2);
	}
	if (sTemp.indexOf("LIBERTAS_UpdateFields();") == -1){
		oForm.onsubmit = new Function("LIBERTAS_UpdateFields();" + sTemp);
	}
}

	// editor initialization
function LIBERTAS_editorswitch(editor, css_stylesheet, direction){
	if (this[editor+'_rEdit'].document.readyState == 'complete'){
		this[editor+'_rEdit'].document.body.className = 'tableCell';
		this[editor+'_rEdit'].document.createStyleSheet(css_stylesheet);
		this[editor+'_rEdit'].document.body.dir = direction;
//		alert(document.all[editor].value);
		this[editor+'_rEdit'].document.body.innerHTML = document.all[editor].value;
		LIBERTAS_toggle_borders(editor, this[editor+'_rEdit'].document.body, null);
		// hookup active toolbar related events
		this[editor+'_rEdit'].document.onkeyup = function() { LIBERTAS_onkeyup(editor); }
		this[editor+'_rEdit'].document.ondrop = function() { LIBERTAS_onDrop(editor); }
		this[editor+'_rEdit'].document.onmouseup = function() { LIBERTAS_update_toolbar(editor, true); }
		// initialize toolbar
		libertas_context_html = "";
		LIBERTAS_update_toolbar(editor, true);
	}
}

function LIBERTAS_getParentTag(editor){
	var trange = this[editor+'_rEdit'].document.selection.createRange();
	if (window.frames[editor+'_rEdit'].document.selection.type != "Control"){
		return (trange.parentElement());
	} else {
		return (trange(0));		
	}
}

	// trim functions	
function LIBERTAS_ltrim(txt){
	var spacers = " \t\r\n";
	while (spacers.indexOf(txt.charAt(0)) != -1){
		txt = txt.substr(1);
	}
	return(txt);
}

function LIBERTAS_rtrim(txt){
	var spacers = " \t\r\n";
	while (spacers.indexOf(txt.charAt(txt.length-1)) != -1){
		txt = txt.substr(0,txt.length-1);
	}
	return(txt);
}

function LIBERTAS_trim(txt){
	return(LIBERTAS_ltrim(LIBERTAS_rtrim(txt)));
}


	
	// is selected text a full tags inner html?
function LIBERTAS_isFoolTag(editor, el){
	var trange = this[editor+'_rEdit'].document.selection.createRange();
	var ttext;
	if (trange != null) 
		ttext = LIBERTAS_trim(trange.htmlText);
	if (ttext != LIBERTAS_trim(el.innerHtml))
		return false;
	else
		return true;
}
	
		
	

	// switch to wysiwyg mode
function LIBERTAS_design_tab_click(editor, sender){
	if (this[editor+'_rEdit'].currentDesignMode!="On"){
		this[editor+'_rEdit'].currentDesignMode='On';
		//iText = this[editor+'_rEdit'].document.body.innerText;
		iText = document.all[editor].value;
		this[editor+'_rEdit'].document.body.innerHTML = iText;
		document.all['LIBERTAS_'+editor+'_editor_mode'].value = 'design';

		// turn off html mode toolbars
		document.all['LIBERTAS_'+editor+'_toolbar_top_html'].style.display = 'none';
		document.all['LIBERTAS_'+editor+'_TableButtons'].style.visibile = 'show';
		document.all['LIBERTAS_'+editor+'_TableButtons'].style.display = '';
		// turn on design mode toolbars
		document.all['LIBERTAS_'+editor+'_toolbar_top_design'].style.display = 'inline';
		// switch editors		
		document.all[editor].style.display = "none";
		document.all[editor+"_rEdit"].style.display = "inline";
		document.all[editor+"_rEdit"].document.body.focus();
	}
	// turn on invisible borders if needed
	LIBERTAS_toggle_borders(editor,this[editor+'_rEdit'].document.body, null);
		
	this[editor+'_rEdit'].focus();
	LIBERTAS_update_toolbar(editor, true);		
}
	
	// switch to html mode
function LIBERTAS_html_tab_click(editor, sender){
	window.resizeTo(screen.availWidth,screen.availHeight);
	if (this[editor+'_rEdit'].currentDesignMode=='On'){
		this[editor+'_rEdit'].currentDesignMode='Off';
		this[editor+'_rEdit'].width=screen.availWidth-50;
		iHTML = this[editor+'_rEdit'].document.body.innerHTML;
		document.all[editor].value = iHTML;
		document.all['LIBERTAS_'+editor+'_editor_mode'].value = 'html';

		// turn off design mode toolbars
		document.all['LIBERTAS_'+editor+'_toolbar_top_design'].style.display	= 'none';
		document.all['LIBERTAS_'+editor+'_TableButtons'].style.visibile = 'hide';
		document.all['LIBERTAS_'+editor+'_TableButtons'].style.display = 'none';
		// turn on html mode toolbars
		document.all['LIBERTAS_'+editor+'_toolbar_top_html'].style.display		= 'inline';
		// switch editors		
		document.all[editor+"_rEdit"].style.display = "none";
		document.all[editor].style.display = "inline";
	}
	document.all[editor].focus();
	this[editor+'_rEdit'].focus();
	LIBERTAS_update_toolbar(editor, true);		
}
function LIBERTAS_html_editor_setup(editor, sender){
//	this[editor+'_rEdit'].currentDesignMode='Off';
//	this[editor+'_rEdit'].width=screen.availWidth-50;
//	iHTML = this[editor+'_rEdit'].document.body.innerHTML;
//	document.all[editor].value = iHTML;
	document.all['LIBERTAS_'+editor+'_editor_mode'].value = 'html';

// turn on html mode toolbars
	document.all['LIBERTAS_'+editor+'_toolbar_top_html'].style.display		= 'inline';
// switch editors		
//	document.all[editor+"_rEdit"].style.display = "none";
	document.all[editor].style.display = "inline";
	document.all[editor].focus();
	this[editor+'_rEdit'].focus();
	LIBERTAS_update_toolbar(editor, true);		
}
	
function LIBERTAS_getFieldByEditor(editor, field){
	var thefield;
	// get field by editor name if no field passed
	if (field == null || field == ""){
		var flds = document.getElementsByName(editor);
		thefield = flds[0].id;
	} else {
		thefield=field;
	}
	return thefield;
}
	
function LIBERTAS_getHtmlValue(editor, thefield){
		var htmlvalue;

		if(document.all['LIBERTAS_'+editor+'_editor_mode'].value == 'design')
		{
			// wysiwyg
			htmlvalue = this[editor+'_rEdit'].document.body.innerHTML;
		}
		else
		{
			// code
			htmlvalue = document.all[thefield].value;
		}
		return htmlvalue;
	}
	
function LIBERTAS_updateField(editor, field){	
	var thefield = LIBERTAS_getFieldByEditor(editor, field);
	debug("editor.textonly = ["+this[editor+'_rEdit'].textonly+"]");
	editorType ="";
	for (var i=0; i < libertas_editors_type.length; i++){
		if (libertas_editors[i]==editor){
			editorType = libertas_editors_type[i];
		}
	}
	
	
	if (editorType != 'textonly'){
		var htmlvalue = LIBERTAS_getHtmlValue(editor, thefield);
	} else {	
	//	var htmlvalue = LIBERTAS_getHtmlValue(editor, thefield);
		var htmlvalue = document.all[thefield].value;
	}
	
	debug('LIBERTAS_updateField("'+editor+'", "'+field+'") returns \n\n '+htmlvalue);
//	if (htmlvalue)
//		document.all[editor].value = htmlvalue;
	working_on_editor = document.getElementById(thefield);
	if (working_on_editor.value != htmlvalue){
		// something changed
		working_on_editor.value = htmlvalue;
	}
	debug('working_on_editor.value = "'+working_on_editor.value+'"');

}

	
function remove_name_spaces(doc){
	start_pos			= doc.indexOf("<?");
	while (start_pos != -1){
		start_pos		= doc.indexOf("<?");
		if (start_pos!=-1){
			end_pos		= doc.indexOf(">",start_pos+1);
			doc 		= doc.substring(0, start_pos) + doc.substring(end_pos + 1);
		}
	}
	return doc;
}
	
function launch_progress_bar(){
	try{
		oX								= (screen.width / 2 )-150
		oY			 					= (screen.height / 2 )-50
		oWidth							= 300
		oHeight 						= 120
		myPopup = window.open("about:blank", "", "scrollbars:no,left="+oX+",top="+oY+",width="+oWidth+",height="+oHeight+"");
		myPopup.document.title = 'Libertas Solutions - Processing Dialog';
		var oPopBody = myPopup.document.body;
		oPopBody.style.backgroundColor	= "#ebebeb";
		oPopBody.style.border			= "solid black 1px";
		sz 								= "<table width='100%'>";
		sz 							   += "<tr><td bgcolor='#cccccc'>Libertas Solutions</td></tr>";
		sz 							   += "<tr><td><center><h3>Work in Progress </h3><img src='/libertas_images/editor/libertas/lib/themes/default/img/working.gif'></center></td></tr>";
		sz 							   += "</table>";
		oPopBody.innerHTML 				= sz;
		myPopup.focus();
		return true;
	} catch (e){
		if (popup_blocker_disabled == false || popup_blocker_disabled == ""){
			alert("Unable to open popup window, if you are using a popup blocker please enable popups for this site.");
		}
		return false;
	}
}
	
	function close_progress_bar(){
		if (popup_blocker_disabled == true || popup_blocker_disabled == ""){
			if((myPopup+"" != "null") &&	(myPopup+"" != "") && (myPopup+"" != "undefined")){
				if (myPopup.closed+""!= "undefined"){
					myPopup.close();
				}
			}
		}
	}
	// cleanup html
	
	
	
	

	// update toolbar if cursor moved or some event happened
	function LIBERTAS_onkeyup(editor)
	{
		/*
			Arrow Keys
			
			37 - left
			38 - up
			39 - right
			40 - down
		*/
		var eobj = window.frames[editor+'_rEdit']; // editor iframe
		if (eobj.event.ctrlKey || (eobj.event.keyCode >= 33 && eobj.event.keyCode<=40))
		{
			LIBERTAS_update_toolbar(editor, false);
		}
		if (eobj.event.ctrlKey && eobj.event.altKey && (eobj.event.keyCode >= 49 && eobj.event.keyCode<=51)){
			LIBERTAS_change_paragraph_click(editor, null, "H"+(eobj.event.keyCode-48));
		} 
	}
	
	
	// update active toolbar state
	
