/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   M A I N . J S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- The Editor will be split into two files Main.js and Extended.js
	-
	- The file main.js will contain only the code required by the system to generate the editor
	- Once the editor is loaded and initialised the main.js will load the extended.js file.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- DHTML Editor
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- This editor is designed to work only in IE modifications required to work on mozilla.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	author : Adrian Sweeney
	-	Modified $Date: 2004/11/23 12:25:44 $
	-	$Revision: 1.19 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
var spellRange						= new Object();
var spellIgnore						= new Array();
var libertas_editors				= new Array();
var libertas_editors_type			= new Array();
var libertas_context_html 			= "";
var image_window					= new Object();	
var myPopup							= new Object();
var myForegroundColor				= "__undefined__";
var myBackgroundColor				= "__undefined__";
var previous_design_mode			= "design";
var myEditor						= "";
var state_EditingSource				= false; //true if current control is in html mode
var readyState						= false;
var popup_blocker_disabled 			= "";
var mybkmrk 						= null;

/*
function XXXX_onload(editor, css_stylesheet, direction,textonly)
{
	// Insert your code here to initialize your program
	// Then, this little bit calls the old onload function

	if (XXXX_old_onload != null)
	{
		alert("Old On loading ");
		setTimeout("LIBERTAS_editorInit('"+editor+"', '"+css_stylesheet+"', '"+direction+"', '"+textonly+"');",200);
		return;
	}
		alert("On loading ");
		setTimeout("LIBERTAS_editorInit('"+editor+"', '"+css_stylesheet+"', '"+direction+"', '"+textonly+"');",200);
		return;
}
*/	

function LIBERTAS_editorInit(editor, css_stylesheet, direction,textonly){
	// prevent from executing twice on the same editor
	var ed = document.getElementById(editor+'_rEdit');
	if (!LIBERTAS_editor_registered(editor)){

		// Starts Modifications For firefox Added By Muhammad Imran

		// check if the editor completely loaded and schedule to try again if not
		//Commit for firefox
		/*if (this[editor+'_rEdit'].document.readyState != 'complete'){
			setTimeout("LIBERTAS_editorInit('"+editor+"', '"+css_stylesheet+"', '"+direction+"', '"+textonly+"');",200);
			return;
		}*/
		
//		this[editor+'_rEdit'].textonly 					= (textonly+''=='undefined')?'':textonly;
		
		//document.all[editor+"_rEdit"].style.display = "";
		//document.all[editor+"_rEdit"].style.display = "inline";
		//document.getElementById(editor+'_rEdit').contentDocument.designMode = "on";		
		document.getElementById(editor+'_rEdit').designMode = 'On';
		//this[editor+'_rEdit'].document.designMode 		= 'On';
		document.getElementById(editor+'_rEdit').showDetails = 'On';
		//this[editor+'_rEdit'].document.showDetails 		= 'On';
		document.getElementById(editor+'_rEdit').currentDesignMode = 'On';
		//this[editor+'_rEdit'].currentDesignMode 		= 'On';
		document.getElementById(editor+'_rEdit').oncontextmenu 	= function(){setContextMenu(editor);return false;}
		//this[editor+'_rEdit'].document.oncontextmenu 	= function(){setContextMenu(editor);return false;}
		document.getElementById(editor+'_rEdit').ondblclick		= function(){setdoubleclick(editor);return false;}
		//this[editor+'_rEdit'].document.ondblclick		= function(){setdoubleclick(editor);return false;}
		document.getElementById(editor+'_rEdit').menu		= new Menu();
		//this[editor+'_rEdit'].menu						= new Menu();
		document.getElementById(editor+'_rEdit').grid 		= new Grid();
		//this[editor+'_rEdit'].grid 						= new Grid();
	// register the editor 
		libertas_editors[libertas_editors.length] 			= editor;
		libertas_editors_type[libertas_editors_type.length]	= (textonly+''=='undefined') ? '' : textonly ;
	// add on submit handler
		LIBERTAS_addOnSubmitHandler(editor);
		//for firefox Modified by Ali
		document.getElementById(editor).value            += "";  
		//document.all[editor].value 						+= "";
		//for firefox Modified by Ali
		document.getElementById(editor).value 						 = document.getElementById(editor).value.replace(/&quot;/g,'"');
		//document.all[editor].value 						 = document.all[editor].value.replace(/&quot;/g,'"');

		// Ends Modifications For firefox Added By Muhammad Imran
		
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
	oForm = document.getElementById(thefield).form;
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

//	if (this[editor+'_rEdit'].document.readyState == 'complete'){
		this[editor+'_rEdit'].document.body.className = 'tableCell';
		
		// Starts Create Stylesheet For firefox Added By Muhammad Imran
		if(document.createStyleSheet) {
			this[editor+'_rEdit'].document.createStyleSheet(css_stylesheet);
			this[editor+'_rEdit'].document.createStyleSheet("/libertas_images/editor/libertas/extra.css");
			this[editor+'_rEdit'].document.createStyleSheet("/libertas_images/themes/general.css");
		}else {
			var styles = "@import url('"+css_stylesheet+"');";
			var newSS=this[editor+'_rEdit'].document.createElement('link');
			newSS.rel='stylesheet';
			newSS.href='data:text/css,'+escape(styles);
			this[editor+'_rEdit'].document.getElementsByTagName("head")[0].appendChild(newSS);

			var styles2 = "@import url('/libertas_images/editor/libertas/extra.css');";
			var newSS2=this[editor+'_rEdit'].document.createElement('link');
			newSS2.rel='stylesheet';
			newSS2.href='data:text/css,'+escape(styles2);
			this[editor+'_rEdit'].document.getElementsByTagName("head")[0].appendChild(newSS2);

			var styles3 = "@import url('/libertas_images/themes/general.css');";
			var newSS3=this[editor+'_rEdit'].document.createElement('link');
			newSS3.rel='stylesheet';
			newSS3.href='data:text/css,'+escape(styles3);
			this[editor+'_rEdit'].document.getElementsByTagName("head")[0].appendChild(newSS3);
			//alert("ss rules: "+document.styleSheets[1].cssRules.length);
		}
		// Ends Create Stylesheet For firefox Added By Muhammad Imran


		// Starts Modifications For firefox To assign and show editor in Design mode Added By Muhammad Imran
		this[editor+'_rEdit'].document.body.dir = direction;
		this[editor+'_rEdit'].document.body.innerHTML = document.getElementById(editor).value;
		document.getElementById(editor+'_rEdit').contentDocument.designMode = "On";
		LIBERTAS_toggle_borders(editor, this[editor+'_rEdit'].document.body, null);
		// hookup active toolbar related events
		this[editor+'_rEdit'].document.onkeydown	= function() { LIBERTAS_onkeydown(editor); }
		this[editor+'_rEdit'].document.onkeyup		= function() { LIBERTAS_onkeyup(editor); }
		this[editor+'_rEdit'].document.ondrop		= function() { LIBERTAS_onDrop(editor); }
		this[editor+'_rEdit'].document.onmouseup	= function() {  }
		
		// Ends Modifications For firefox To assign and show editor in Design mode Added By Muhammad Imran
		
		
		// initialize toolbar
		libertas_context_html = "";
		
//		var head = document.getElementsByTagName("head")[0];
//		var script = document.createElement("script");
//			script.src = "/libertas_images/editor/libertas/js/extended.js";
//			head.appendChild(script);
//	}
}

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
//			LIBERTAS_update_toolbar(editor, false);
		}
		if (eobj.event.ctrlKey && eobj.event.keyCode ==86 && ((product_version=='SITE')|| has_function(editor,'auto_tidy'))){
			// if auto clen enabled then on a paste (Ctrl + V) clean the complete page as we can not currently capture the info
			// inserted with (Ctrl+V) if capturable change next function to paste special function instead
			// third parameter is to ignore confirm screen of tidy function
			LIBERTAS_cleanup_click(editor, null, true)
		}
		if (eobj.event.ctrlKey && eobj.event.altKey && (eobj.event.keyCode >= 49 && eobj.event.keyCode<=51)){
			LIBERTAS_change_paragraph_click(editor, null, "H"+(eobj.event.keyCode-48));
		} 
		if (eobj.event.keyCode==13 || eobj.event.keyCode==10){
			remove_empty(editor);
		}
	}
	function LIBERTAS_onkeydown(editor)
	{
		/*
			Arrow Keys
			
			37 - left
			38 - up
			39 - right
			40 - down
		*/
		var eobj = window.frames[editor+'_rEdit']; // editor iframe
		try{
			if (eobj.event.ctrlKey && eobj.event.keyCode ==86 && has_function(editor,'auto_tidy')){
				// if auto clen enabled then on a paste (Ctrl + V) clean the complete page as we can not currently capture the info
				// inserted with (Ctrl+V) if capturable change next function to paste special function instead
				// third parameter is to ignore confirm screen of tidy function
				LIBERTAS_paste_special_click(editor, null);
				eobj.event.cancelBubble = true;
				eobj.event.returnValue = false;
			}
		} catch(e){}
	}
	
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 DHTML Editor
 
 Toolbar Configuration functions
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function display_editor(editor,buttonList, addReturns,justCount){
	// old removed
}

	function LIBERTAS_dropdown_menu_click(editor, sender, extra_parameters){
		this[editor+'_rEdit'].focus();
		try{
			mybkmrk = this[editor+'_rEdit'].document.selection.createRange().getBookmark();
		} catch(e) {}
		if (extra_parameters=="tableWizard"){
			myEditor = editor;
			onTable(editor,sender);			
		} else {
			ptr = LIBERTAS_getParentTag(editor);
			myOptions = Array();
			if (version>5){
				PopupObject = window.createPopup();
				__menucontainer = PopupObject;
				PopupObject.document.oncontextmenu = function(){return false;};
				oPopBody = PopupObject.document.body;
				oPopBody.style.backgroundColor	= "#ebebeb";
				oPopBody.style.border			= "solid #666666 1px";
			}
			if (extra_parameters=="paste"){
				if (product_version=='SITE'){
					myOptions[myOptions.length] = Array('From Word ...',"window.frames['"+editor+"_rEdit'].focus();LIBERTAS_paste_special_click('"+editor+"','"+sender+"');","paste_special",true);
				} else {
					if (!has_function(editor,'auto_tidy')){
						myOptions[myOptions.length] = Array('Normal ...',"window.frames['"+editor+"_rEdit'].focus();LIBERTAS_on_click('"+editor+"','"+sender+"','paste');","paste",true);
					}
					myOptions[myOptions.length] = Array('Plain Text ...',"window.frames['"+editor+"_rEdit'].focus();LIBERTAS_paste_PlainText('"+editor+"','"+sender+"');","paste_plain",true);
					myOptions[myOptions.length] = Array('From Word ...',"window.frames['"+editor+"_rEdit'].focus();LIBERTAS_paste_special_click('"+editor+"','"+sender+"');","paste_special",true);
				}
				width=120;
				type="menu";
			} else if (extra_parameters=="color_fore"){
				gridType= "color";
				for (i=0;i<palette.length;i++){
					myOptions[myOptions.length] = Array("'"+palette[i]+"'","LIBERTAS_fore_color_click(\"" + editor + "\", \"" + sender + "\", \""+palette[i]+"\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				}
				myOptions[myOptions.length] = Array("'#000000'","LIBERTAS_fore_color_click(\"" + editor + "\", \"" + sender + "\", \"#000000\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[myOptions.length] = Array("'#333333'","LIBERTAS_fore_color_click(\"" + editor + "\", \"" + sender + "\", \"#333333\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[myOptions.length] = Array("'#666666'","LIBERTAS_fore_color_click(\"" + editor + "\", \"" + sender + "\", \"#666666\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[myOptions.length] = Array("'#999999'","LIBERTAS_fore_color_click(\"" + editor + "\", \"" + sender + "\", \"#999999\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[myOptions.length] = Array("'#cccccc'","LIBERTAS_fore_color_click(\"" + editor + "\", \"" + sender + "\", \"#cccccc\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[myOptions.length] = Array("'#FFFFFF'","LIBERTAS_fore_color_click(\"" + editor + "\", \"" + sender + "\", \"#FFFFFF\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
	
				myOptions[-1] = Array('__REMOVE__',"LIBERTAS_fore_color_click(\"" + editor + "\", \"" + sender + "\", \"__REMOVE__\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[-2] = Array('__REMOVE__',"LIBERTAS_fore_color_click(\"" + editor + "\", \"" + sender + "\", \"__PICK__\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				width=154;
				type="grid";
				MULTIPLIER =33;
			} else if (extra_parameters=="color_bg"){
				gridType= "color";
				for (i=0;i<palette.length;i++){
					myOptions[myOptions.length] = Array("'"+palette[i]+"'","LIBERTAS_bg_color_click(\"" + editor + "\", \"" + sender + "\", \""+palette[i]+"\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				}
				myOptions[myOptions.length] = Array("'#000000'","LIBERTAS_bg_color_click(\"" + editor + "\", \"" + sender + "\", \"#000000\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[myOptions.length] = Array("'#333333'","LIBERTAS_bg_color_click(\"" + editor + "\", \"" + sender + "\", \"#333333\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[myOptions.length] = Array("'#666666'","LIBERTAS_bg_color_click(\"" + editor + "\", \"" + sender + "\", \"#666666\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[myOptions.length] = Array("'#999999'","LIBERTAS_bg_color_click(\"" + editor + "\", \"" + sender + "\", \"#999999\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[myOptions.length] = Array("'#cccccc'","LIBERTAS_bg_color_click(\"" + editor + "\", \"" + sender + "\", \"#cccccc\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[myOptions.length] = Array("'#FFFFFF'","LIBERTAS_bg_color_click(\"" + editor + "\", \"" + sender + "\", \"#FFFFFF\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[-1] = Array('__REMOVE__',"LIBERTAS_bg_color_click(\"" + editor + "\", \"" + sender + "\", \"__REMOVE__\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[-2] = Array('__REMOVE__',"LIBERTAS_bg_color_click(\"" + editor + "\", \"" + sender + "\", \"__PICK__\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				width=154;
				type="grid";
				MULTIPLIER = 33;
			} else if (extra_parameters=="html_entity"){
				gridType = 'character';
				type="grid";
				characterList = Array(8364,8482,169,174,163,193,201,205,211,218,225,233,237,243,252,192,200,204,210,217,224,232,236,242,249,171,187,188,189,190);
				MULTIPLIER = 24;
				for (x=0; x<characterList.length;x++){
					myOptions[myOptions.length] = Array(characterList[x] , "LIBERTAS_special_char_click(\"" + editor + "\", \"" + sender + "\", \"&#"+characterList[x]+";\")");
				}
//				myOptions[-1] = Array("__REMOVE__"  , "LIBERTAS_special_char_click(\"" + editor + "\", \"" + sender + "\", \"__REMOVE__\")","<img src=/libertas_images/themes/1x1.gif width=20 height=20>");
				myOptions[-2] = Array("__PICK__"  , "LIBERTAS_special_char_click(\"" + editor + "\", this, \"__PICK__\")","<img src=/libertas_images/themes/1x1.gif width='20' height='20'>");
				width=154;
			
			} else 	if (extra_parameters=="image"){
				width=130;
				if (ptr.tagName.toLowerCase()=='img'){
					myOptions[myOptions.length] = Array('Image properties',"LIBERTAS_image_prop_click('"+editor+"','"+sender+"');","image_prop",true);
					type="menu";
				} else {
					myOptions[myOptions.length] = Array('Image properties',"LIBERTAS_image_prop_click('"+editor+"','"+sender+"');","image_prop",false);
					type="menu";
				}	
			} else {
				type="text";
				width=100;
				height=26;
				htmlbox = "<center>Sorry no Menu Available</center>";
			}
			
			if (myOptions.length > 0){
				if (type=="menu"){
					this.menu = new Menu();
					for(index=0;index < myOptions.length;index++){
						this.menu.addItem(new MenuItem(myOptions[index][0], myOptions[index][1], myOptions[index][3], myOptions[index][2]));
					}
					height = (25 * myOptions.length)+10;
					if (version>5){
						oPopBody.innerHTML				= this.menu.draw();// = "Click outside <B>popup</B> to close.";
						
					} else {
						onMenu(editor,sender,this.menu.draw(),-1,-1,height,width);
					}
				} else if (type=="grid"){
					sz  = "<table border='0' cellspacing='3' cellpadding='0' id='popupTable' name='popupTable' bgcolor='#ebebeb'>\n";
					sz += "<tr><td bgcolor='#ebebeb'>";
					this.grid = new Grid();
					for(index=0;index < myOptions.length;index++){
						this.grid.addItem(new gridItem(myOptions[index][0], myOptions[index][1], myOptions[index][3], myOptions[index][2], gridType));
					}
					if (myOptions[-1]){
						this.grid.overwrite(new gridItem("", myOptions[-1][1], "", ""),-1);
					}
					this.grid.overwrite(new gridItem("", myOptions[-2][1], "", ""),-2);
					if (gridType == 'character'){
						gridwidth=5;
					} else {
						gridwidth=6;
					}
					sz += this.grid.draw(gridwidth);// = "Click outside <B>popup</B> to close.";
					l = myOptions.length + 1;
					if ((width+'' == '')||(width+'' == 'undefined')||(width+'' == 'null')){
						width = (25*gridwidth);
					}
					sz += "</td></tr>";
					if (product_version=="ECMS" || gridType == 'character'){
						if(version>5){
							str ="javascript:parent.grid_execute_event(-2);parent.PopupObject.hide();event.cancelBubble=true;";
						} else {
							str ="javascript:parent.grid_execute_event(-2);parent.cancelTable();event.cancelBubble=true;";
						}
						sz += "	<tr><td id='morecolour' name='morecolour' bgcolor='#ebebeb' style='height:20px;font-size:12px;text-align:center' onclick='" + str + "' onmouseover=\""
								+"morecolour.style.background='#9999cc';"
								+"morecolour.style.borderTop='1px solid #993399';"
								+"morecolour.style.borderRight='1px solid #993399';"
								+"morecolour.style.borderBottom='1px solid #993399';"
								+"morecolour.style.borderLeft='1px solid #993399';"
								+"\""
								+" onmouseout=\""
								+"morecolour.style.background='#ebebeb';"
								+"morecolour.style.borderTop='1px solid #ebebeb';"
								+"morecolour.style.borderRight='1px solid #ebebeb';"
								+"morecolour.style.borderBottom='1px solid #ebebeb';"
								+"morecolour.style.borderLeft='1px solid #ebebeb';"
								+"\" "
								+">More ...</td></tr>";
					}
					sz += "</table>";

					if (gridType == 'character'){
						h = 50;
					}else{
						h = 25;
					}
					height = ( Math.round(l / gridwidth) * MULTIPLIER ) + h;
					if (version>5){
						oPopBody.innerHTML	= sz;// = "Click outside <B>popup</B> to close.";
					} else {
						onMenu(editor,sender,sz,-1,-1,height,width);
					}
				}
				if (version>5){
					PopupObject.show(11-width,20,width,height,sender);
				}
			} else {
				oPopBody.innerHTML		 = htmlbox;
				PopupObject.show(11-width, 20, width, height, sender);
			}
		}
	}

// toolbar button effects
function LIBERTAS_default_bt_over(ctrl){
//  ctrl.className = "LIBERTAS_default_tb_over";
	//var imgfile = ctrl.src.substr(0, ctrl.src.length-4) + "_over.gif";
	var imgfile =  LIBERTAS_base_image_name(ctrl)+ "_over.gif";
	ctrl.src = imgfile;
}

// toolbar button effects
function LIBERTAS_default_bt_out(ctrl){
  var imgfile;
  if (ctrl.id+"" !="undefined"){
	  if (ctrl.getAttribute("libertas_state") == true){
	    imgfile = LIBERTAS_base_image_name(ctrl)+"_down.gif";
	  } else {
	    imgfile = LIBERTAS_base_image_name(ctrl)+".gif";
	  }
	  ctrl.src = imgfile;
	  ctrl.disabled = false;
  }
}
function LIBERTAS_default_bt_down(ctrl)
{
  var imgfile = LIBERTAS_base_image_name(ctrl)+"_down.gif";
  ctrl.src = imgfile;
}
function LIBERTAS_default_bt_up(ctrl)
{
  var imgfile = LIBERTAS_base_image_name(ctrl)+".gif";
  ctrl.src = imgfile;
}
function LIBERTAS_default_bt_off(ctrl)
{
  var imgfile = LIBERTAS_base_image_name(ctrl)+"_off.gif";
  ctrl.src = imgfile;
  ctrl.disabled = true;
}

// returns base toolbar image name
function LIBERTAS_base_image_name(ctrl){
	if (ctrl.id+"" != "undefined"){
		var imgname = ctrl.src.substring(0,ctrl.src.lastIndexOf("/"))+"/tb_"+ctrl.id.substr(ctrl.id.lastIndexOf("_tb_")+4, ctrl.id.length);
		return imgname;
	}
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
		var tab  =document.all['LIBERTAS_'+editor+'_editor_mode'].value+"";
		if(tab+"" == 'design'){
			// wysiwyg
			htmlvalue = "";
			try{
				var ed = this[editor+'_rEdit'];
				var inner = ed.document.body.innerHTML+"";
				htmlvalue = fixHR(inner);
			} catch(e){}
		}else{
			// code
			htmlvalue = fixHR(document.all[thefield].value);
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
		var htmlvalue = fixHR(document.all[thefield].value);
	}
	debug('LIBERTAS_updateField("'+editor+'", "'+field+'") returns \n\n '+htmlvalue);
	working_on_editor = document.getElementById(thefield);
	if (working_on_editor.value != htmlvalue){
		working_on_editor.value = htmlvalue;
	}
	this[editor+'_rEdit'].document.body.innerHTML = htmlvalue;
	debug('working_on_editor.value = "'+working_on_editor.value+'"');
}
	function has_function(editor,find){
		if(document.getElementById("LIBERTAS_"+editor+"_tb_"+find)){
			return true;
		}
		return false;
	}
	
	function setContextMenu(editor){
		_event = this[editor+'_rEdit'].event;
		myEditor = editor;
		this[editor+'_rEdit'].focus();
		var selection = _Libertas_GetSelection(editor);
		try{
			mybkmrk = selection.getBookmark();
		} catch (e) {
			mybkmrk = "undefined";
		}
		enable_paste = 1;
		if(selection){
			tag = LIBERTAS_getParentTag(editor);
			while (tag.tagName!="BODY"){
				if (tag.tagName=="TD"){
					enable_paste=0;
					break;
				} 
				tag = tag.parentNode;
			}
		}
		if (
			(has_function(editor,'undo')) || 
			(has_function(editor,'cut')) || 
			(has_function(editor,'paste_special')) || 
			(has_function(editor,'find')) || 
			(has_function(editor,'ordered_list')) || 
			(has_function(editor,'page_prop'))
		){
			this.menu = new Menu();
			if (has_function(editor,'undo')){
				this.menu.addItem(new MenuItem("Undo (Ctrl+Z)", "LIBERTAS_on_click('"+editor+"',this,'undo')",true,"Undo"));
				this.menu.addItem(new MenuItem("Redo (Ctrl+Shift+Z)", "LIBERTAS_on_click('"+editor+"',this,'redo')",true,"Redo"));
				this.menu.addSeparator();
			}
			if (has_function(editor,'cut')){
				if(selection.text != ""){
					this.menu.addItem(new MenuItem("Cut (Ctrl+X)", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_on_click('"+editor+"',this,'cut')",true,"Cut"));
					this.menu.addItem(new MenuItem("Copy (Ctrl+C)", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_on_click('"+editor+"',this,'copy')",true,"Copy"));
				} else {
					this.menu.addItem(new MenuItem("Cut (Ctrl+X)", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_on_click('"+editor+"',this,'cut')",false,"Cut"));
					this.menu.addItem(new MenuItem("Copy (Ctrl+C)", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_on_click('"+editor+"',this,'copy')",false,"Copy"));
				}
				
				if ((product_version!='SITE') && (!has_function(editor,'auto_tidy')) && (enable_paste==1)){
					this.menu.addItem(new MenuItem("Paste (Ctrl+V)", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_on_click('"+editor+"',this,'paste')",true,"Paste"));
				}
			}
			if (product_version=='SITE' && (enable_paste==1)){
					this.menu.addItem(new MenuItem("Paste Special...", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_paste_special_click('"+editor+"',this)",true,"paste_special"));
			} else {
				if (has_function(editor,'paste_special') && (enable_paste==1)){
					this.menu.addItem(new MenuItem("Paste Plain text...", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_paste_PlainText('"+editor+"',this)",true,"paste_plain"));
					this.menu.addItem(new MenuItem("Paste Special...", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_paste_special_click('"+editor+"',this)",true,"paste_special"));
				}
			}
			if (has_function(editor,'cut') || (has_function(editor,'libertas_configuration_paste_special') && (enable_paste==1))){
				this.menu.addSeparator();
			}
			if (has_function(editor,'find')){
				this.menu.addItem(new MenuItem("Find", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_find_click('"+editor+"',this)",true,"find"));
				this.menu.addItem(new MenuItem("Replace", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_replace_click('"+editor+"',this)",true,"replace"));
				this.menu.addSeparator();
			}
			if (has_function(editor,'ordered_list')){
				this.menu.addItem(new MenuItem("Bullet List", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_on_click('"+editor+"',this,'insertunorderedlist')",true,"bulleted_list"));
				this.menu.addItem(new MenuItem("Numbered List", "window.frames['"+editor+"_rEdit'].focus();LIBERTAS_on_click('"+editor+"',this,'insertorderedlist')",true,"ordered_list"));
				this.menu.addSeparator();
			}
			if (has_function(editor,'page_prop')){
				this.menu.addItem(new MenuItem("Page Statistics", "LIBERTAS_stats_click('"+editor+"',this)",true,"stats"));
			}
			X = _event.screenX-10;
			Y = _event.screenY-240;
			Y = _event.screenY;
			if (version>5){
				oPopup.document.oncontextmenu 			= function(){return false;};
				var oPopBody 							= oPopup.document.body;
					oPopBody.style.backgroundColor		= "lightyellow";
					oPopBody.style.border				= "solid black 1px";
					oPopBody.innerHTML					= this.menu.draw();// = "Click outside <B>popup</B> to close.";
				var rightedge 							= screen.width;
				var bottomedge 							= screen.height;
				var lefter 								= X;
				var topper 								= Y;
				oWidth									= 200;
				oHeight 								= ((this.menu.items.length - 1) * 21) + 10;
				if (rightedge < _event.screenX + oWidth){
					lefter = rightedge - oWidth;
				} else {
					lefter = _event.screenX;
				}
				if (bottomedge < _event.screenY + oHeight) {
					topper = bottomedge - oHeight;
				} else {
					topper = _event.screenY;
				}
				oPopup.show(lefter, topper, oWidth, oHeight);
			} else {
				onMenu(editor,null,this.menu.draw(),X,Y);
			}
		}
		myEditor = "";
		return false;
	}
	function LIBERTAS_toggle_borders(editor, root, toggle)
	{
		// get toggle mode (on/off)
		var toggle_mode = toggle;
		if (toggle == null)
		{
			var tgl_borders = document.getElementById("LIBERTAS_"+editor+"_borders");
			if (tgl_borders != null)
			{
				toggle_mode = tgl_borders.value;
			}
			else
			{
				toggle_mode = "on";
			}
		}
		var tbls = new Array();
		if (root.tagName == "TABLE")
		{
			tbls[0] = root;
		}
		else
		{
			// get all tables starting from root
			tbls = root.getElementsByTagName("TABLE");
		}
		
		var tbln = 0;
		if (tbls != null) tbln = tbls.length;
		for (ti = 0; ti<tbln; ti++)
		{
			if ((tbls[ti].style.borderWidth+"" == "" || tbls[ti].style.borderWidth == 0 || tbls[ti].style.borderWidth == "0px") &&
					(tbls[ti].border == 0 || tbls[ti].border == "0px") &&
					(toggle_mode == "on"))
			{
				tbls[ti].runtimeStyle.borderWidth = "1px";
				tbls[ti].runtimeStyle.borderStyle = "dashed";
				tbls[ti].runtimeStyle.borderColor = "#aaaaaa";
			} // no border
			else 
			{
				tbls[ti].runtimeStyle.borderWidth = "";
				tbls[ti].runtimeStyle.borderStyle = "";
				tbls[ti].runtimeStyle.borderColor = "";
			}
				
			var cls = tbls[ti].cells;
			// loop through cells
			for (ci = 0; ci<cls.length; ci++)
			{
				if ((tbls[ti].style.borderWidth == 0 || tbls[ti].style.borderWidth == "0px") &&
						(tbls[ti].border == 0 || tbls[ti].border == "0px") && 
						(cls[ci].style.borderWidth == 0 || cls[ci].style.borderWidth == "0px") && 
						(toggle_mode == "on"))
				{
					cls[ci].runtimeStyle.borderWidth = "1px";
					cls[ci].runtimeStyle.borderStyle = "dashed";
					cls[ci].runtimeStyle.borderColor = "#aaaaaa";
				}
				else 
				{
					cls[ci].runtimeStyle.borderWidth = "";
					cls[ci].runtimeStyle.borderStyle = "";
					cls[ci].runtimeStyle.borderColor = "";
				}
			} // cells loop
		} // tables loop
	} // LIBERTAS_toggle_borders

/**
* get the parent tag of the selection
* @param string the editors name
*/
	
function LIBERTAS_getParentTag(editor){
	var trange = this[editor+'_rEdit'].document.selection.createRange();
	if (window.frames[editor+'_rEdit'].document.selection.type != "Control"){
		return (trange.parentElement());
	} else {
		return (trange(0));		
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 DHTML Editor
 
 Standard buttons that call IE functions
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_justify_click(editor, sender){
	window.frames[editor+'_rEdit'].focus();
	debug('LIBERTAS_justify_click("'+editor+'", "'+sender+'")');
	myEditor = editor;
	element_ptr = new Object();
	page_ptr	= _Libertas_GetSelection();
	if (page_ptr.parentElement){
		element_ptr = _Libertas_GetElement(page_ptr.parentElement(),"P");
	} else {
		element_ptr = _Libertas_GetElement(page_ptr.item(0),"P");
	}
	if (element_ptr==null){	
		this[editor+'_rEdit'].document.body.innerHTML = '<p align="justify">'+this[editor+'_rEdit'].document.body.innerHTML+'</p>';
	} else {
		element_ptr['align'] = 'justify';
	}
	window.frames[editor+'_rEdit'].focus();		 
//		_exec_command(editor+'_rEdit', 'justify', false, null);
}

function _exec_command(editor, param1, param2, param3){
	if ((version*1) < 6){
		//LIBERTAS_GENERAL_getFormElement(editor).document.execCommand(param1, param2, param3);
		window.frames[editor].document.execCommand(param1, param2, param3);
	}else {
		//	LIBERTAS_GENERAL_getFormElement(editor).document.execCommand(param1, param2, param3);
		if (editor.indexOf("_rEdit")!=-1){
			spareEditor = editor.substring(0, editor.indexOf("_rEdit"));
		} else {
			spareEditor = editor;
		}
		check = has_function(spareEditor, 'libertas_configuration_auto_tidy');
		if (param1=='paste' && check == true &&  window.frames[spareEditor+'_rEdit'].currentDesignMode=='On'){
			LIBERTAS_paste_special_click(spareEditor, null);
		}else {
			//for firefox modified by ALi
			//window.frames[editor].document.focus();
			//document.focus();
			if (param1=='pastefromtext'){
				param1='paste';
			}
			//for firefox modified by ALi
			window.frames[editor].document.execCommand(param1, param2, param3);
			//window.frames[editor].document.execCommand('bold', false, null);
			
		}
		debug_alert("Executing Command "+param1+", "+param2+", "+param3);
	}
}
function LIBERTAS_on_click(editor, sender, param){
	debug('file :: simplebuttons line (9) ::fn :: LIBERTAS_on_click("'+editor+'", "'+sender+'", "'+param+'")');
	window.frames[editor+'_rEdit'].focus();
	if (param=='paste'){
		if (has_function(editor,'auto_tidy') && window.frames[editor+'_rEdit'].currentDesignMode=='On'){
			LIBERTAS_paste_special_click(editor, sender);
		} else {
			LIBERTAS_paste_Normal(editor, sender);
		}
	} else {
		_exec_command(editor+'_rEdit', param, false, null);
	}
			
}
	// returns current table	
	function LIBERTAS_getTable(editor)
	{
		if (window.frames[editor+'_rEdit'].document.selection.type == "Control")
		{ 
			var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
			if (tControl(0).tagName == 'TABLE')
				return(tControl(0));
			else
				return(null);
		}
		else
		{
			var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
			tControl = tControl.parentElement();
			while ((tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
			{
				tControl = tControl.parentElement;
			}
			if (tControl.tagName == 'TABLE')
				return(tControl);
			else
				return(null);
		}
	}
function LIBERTAS_getImg(editor) {
    if (window.frames[editor+'_rEdit'].document.selection.type == "Control"){ 
      var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
      if (tControl(0).tagName == 'IMG')
        return(tControl(0));
      else
        return(null);
    } else {
      return(null);
    }
}
	function onSubmitCompose(c, newCommand){
		try {
			ok = 	check_required_fields(1);
		} catch(e){
			ok = true;
		}
		if (ok){
			
			if ((newCommand+'' != '') && (newCommand+'' != 'undefined')){
				if (newCommand.indexOf('PAGE_SAVE_EDIT')!=-1){
					user_form.command.value = 'PAGE_SAVE_EDIT';
					if (user_form.display_tab+''!='undefined'){
						user_form.display_tab.value = newCommand.split('display_tab=')[1];
					}
				} else {
					user_form.command.value = newCommand;
				}
			}
			for (i=0; i<libertas_editors.length; i++){
				if (eval("document.all.LIBERTAS_"+libertas_editors[i]+"_editor_mode.value!='design'")){
						iText = document.all[libertas_editors[i]].value;
						this[libertas_editors[i]+"_rEdit"].document.body.innerHTML = iText;
				}
				eval("document.all."+libertas_editors[i]+".value = this['"+libertas_editors[i]+"_rEdit'].document.body.innerHTML;");
			}
			user_form.submit();
		}
	}
	
function remove_empty(editor){
	myEditor = editor;
	this[editor+'_rEdit'].focus();
	var selection = _Libertas_GetSelection();
	if(selection.tagName+"" == "undefined"){
		var item = selection.parentElement()
		if (item.tagName=="ACRONYM"){
			if ((item.innerHTML=="") || (item.innerHTML==" ") || (item.innerHTML=="&nbsp;")){
				item.removeNode();
			}
		}
	}
}

function fixHR(myHTML){
	myHTML = LIBERTAS_ltrim(myHTML);
	if (myHTML.length != 0 ){
		fake  = myHTML.toLowerCase()
		if (fake.substring(0,2)=="<p"){
			point1 = fake.indexOf("<hr");
			point2 = fake.indexOf("<",2);
			if (point1==point2){
				myHTML = myHTML.substring(point1);
			}
		}
	}
	return myHTML;
}

function tidy_IMG(editor){
	this[editor+'_rEdit'].focus();
	var selection = this[editor+'_rEdit'].document
	numOfImages = selection.images.length;
	for (var i=0; i<numOfImages; i++){
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - blank values for each entry
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */
		imgborder	= '0';
		imghspace	= '0';
		imgvspace	= '0';
		imgalign	= '';
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - get alignment for this image
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */
		if (selection.images[i].hspace+""!="0" && selection.images[i].hspace+""!=""){
			imghspace=selection.images[i].hspace;
		}
		if (selection.images[i].style.marginTop+""!="0" && selection.images[i].style.marginTop+""!=""){
			imghspace=selection.images[i].style.marginTop;
		}
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - get alignment for this image
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */
		if (selection.images[i].vspace+""!="0" && selection.images[i].vspace+""!=""){
			imgvspace=selection.images[i].vspace;
		}
		if (selection.images[i].style.marginLeft+""!="0" && selection.images[i].style.marginLeft+""!=""){
			imgvspace=selection.images[i].style.marginLeft;
		}
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - get alignment for this image
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */
		if(selection.images[i].style.styleFloat!=""){
			imgalign = selection.images[i].style.styleFloat;
		}
		if (selection.images[i].align!=""){
			imgalign = selection.images[i].align;
		}
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - get border for this image
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */

		if (selection.border!=0){
			imgborder = selection.images[i].border;
		}
		if (selection.images[i].style.borderWidth!=0){
			imgborder = selection.images[i].style.borderWidth;
		}
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - remove undesired attributes from the image tag 
		- Alignment,
		- Horizontal Space,
		- Vertical Space,
		- Border,
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */
		selection.images[i].removeAttribute("align");
		selection.images[i].removeAttribute("border");
		selection.images[i].removeAttribute("hspace");
		selection.images[i].removeAttribute("vspace");
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - set the border for this image
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */
		selection.images[i].style.borderWidth	= imgborder;
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - set the alignment for this image
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */
		selection.images[i].style.styleFloat	= imgalign;
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - get Hspace and Vspace for this image
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */
		selection.images[i].style.marginLeft	= imgvspace;
		selection.images[i].style.marginRight	= imgvspace;
		selection.images[i].style.marginTop		= imghspace;
		selection.images[i].style.marginBottom	= imghspace;
	}
	
}