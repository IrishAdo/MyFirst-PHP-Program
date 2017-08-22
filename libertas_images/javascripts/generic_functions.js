/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- G E N E R A L   F U N C T I O N S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- General Variables
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var LIBERTAS_GENERAL_POPUP_function_properites = {};


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- General Methods
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function LIBERTAS_GENERAL_jtidy(str);
- function LIBERTAS_GENERAL_unjtidy(str);
- function LIBERTAS_GENERAL_cdata_plain_to_rich(str);
- function LIBERTAS_GENERAL_cdata_rich_to_plain(str);
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: jtidy
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function is a simple tidy function to be used to strip some bad characters from the content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_GENERAL_jtidy(str){
	var pos = -1;
	var splitter = "";
	if (str+''!='' && str+''!='undefined' && str+''!='null'){
		try{
			pos  = str.indexOf("[[quote]]");
			while (pos !=-1){
				str = str.substring(0,pos) + "&amp;#34;" + str.substring(pos+9);
				pos  = str.indexOf("[[quote]]", pos + 5);
			}
			pos  = str.indexOf("[[jsquote]]");
			while (pos !=-1){
				str = str.substring(0,pos) + "&amp;#34;" + str.substring(pos+11);
				pos  = str.indexOf("[[jsquote]]", pos + 5);
			}
		} catch (e){
			debug_alert("problem while removing [[quote]] fn:: LIBERTAS_GENERAL_jtidy(str)")
		}
		try{
			str = encodeURI(new String(str));
		} catch (e){
			debug_alert("unable to encodeURI(str) fn:: LIBERTAS_GENERAL_jtidy(str)")
			str = escape(str.split(String.fromCharCode(160)).join(" "));
		}
		try{
			pos  = str.indexOf("&");
			splitter ="&amp;";
			while (pos !=-1){
				str = str.substring(0,pos) + splitter + str.substring(pos+1);
				pos  = str.indexOf("&", pos + splitter.length);
			}
			var find = new Array(
				new Array("\%22","&amp;#34;"),
				new Array("&amp;amp;#","&#"),
				new Array("&amp;#","&#"),
				new Array("\?","&#63;"),
				new Array("\?","&#63;"),
				new Array("'","&#39;"),
				new Array("<","&#60;"),
				new Array(">","&#62;"),
				new Array('"',"&#34;"),
				new Array('€',"&#8364;")
			);
			for(var index=0;index<find.length;index++){
				while (str.indexOf(find[index][0])!=-1){
					str = str.replace(find[index][0],find[index][1]);
				}
			}
		} catch (e){
			debug_alert("Problem with transforming content",1,str);
		}

	}else{
		str = '';
	}
	return str;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_unjtidy
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function is a simple untidy function to be used to fix tags back togethor also decodes content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_GENERAL_unjtidy(str){
	var str = unescape(str+"");
	var find = new Array(
		new Array("&#63;","?"),
		new Array("&#60;","<"),
		new Array("&#62;",">"),
		new Array("&#34;","&quot;"),
		new Array("\r",""),
		new Array("\n",""),
		new Array("[[javareturn]]","\n"),
		new Array("[[js_quot]]","\""),
		new Array("[[quot]]","\"")
	);
	for(var index=0;index<find.length;index++){
		while (str.indexOf(find[index][0])!=-1){
			str = str.replace(find[index][0],find[index][1]);
		}
	}
	return str;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_cdata_plain_to_rich(str)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function is good if you are wanting to convert plaing text into HTML
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_GENERAL_cdata_plain_to_rich(str){
	try{
		var str = encodeURI(str);
	} catch (e){
		debug_alert("unable to decodeURI(str) fn:: LIBERTAS_GENERAL_cdata_plain_to_rich(str)")
	}
	try{
		var find = new Array(
			new Array("%0D%0A","<br />")
		);
		for(index=0;index<find.length;index++){
			while (str.indexOf(find[index][0])!=-1){
				str = str.replace(find[index][0],find[index][1]);
			}
		}
	} catch (e){
		debug_alert("Problem with transforming content",1,str);
	}
	return decodeURI(str);
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_cdata_rich_to_plain(str)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Small html stripping function replaces BR tags with single returns
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_GENERAL_cdata_rich_to_plain(str){
	try{
		var str = decodeURI(str);
	} catch (e){
		debug_alert("unable to decodeURI(str) fn:: LIBERTAS_GENERAL_cdata_rich_to_plain(str)")
	}
	var find = new Array(
		new Array("<br />","\n"),
		new Array("<br/>","\n"),
		new Array("<br>","\n"),
		new Array("&#60;br /&#62;","\n"),
		new Array("&#60;br/&#62;","\n"),
		new Array("&#60;br&#62;","\n")
	);
	for(index=0;index<find.length;index++){
		while (str.indexOf(find[index][0])!=-1){
			str = str.replace(find[index][0],find[index][1]);
		}
	}
	return str;
}



/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_open_popup_window(url, properties)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function to open a popup window 
- THIS FUNCTION IS NOT USED EVER NOT WORKING
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function LIBERTAS_GENERAL_open_popup_window(url, properties){
	if (browser=="IE"){
		return showModalDialog(url, LIBERTAS_GENERAL_POPUP_function_properites, 'dialogHeight:200px; dialogWidth:366px; resizable:no; status:no');	
	} else {
		
	}
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_getFrame(id)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will return a reference to the editor frame
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_GENERAL_getFrame(id){
	try{
		if (browser=="IE"){
			return window.frames[id];
		} else {
			return window.layers[id];
		}
	} catch (e){
	
	}
}/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_focus(id)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will print the string held in the function parameter "data" to the tag with the id named in the 
- function parameter "id"
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_GENERAL_getFormElement(id){
	try{
		if (browser=="IE"){
			return document.all[id];
		} else {
			return document.getElementsByName(id);
		}
	} catch (e){
	
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_printToId(id, data)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will print the string held in the function parameter "data" to the tag with the id named in the 
- function parameter "id"
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function printToId(id, data){
	LIBERTAS_GENERAL_printToId(id, data);
}
function LIBERTAS_GENERAL_printToId(id, data){
	try{
		var d = document.getElementById(id);
		d.innerHTML = data;
	} catch (e){
		if (browser=="IE"){
			alert("Unable to open 'document.all[\""+id+"\"].innerHTML' for writting\n unable to write "+data);
		} else {
			debug_alert("Unable to write to 'document.layers[\""+id+"\"].innerHTML'");
		}
	}
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_addToTagId(id, childrenNodes)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will print the string held in the function parameter "data" to the tag with the id named in the 
- function parameter "id"
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_GENERAL_addToTagId(id, myChildNode){
	try{
		var d = document.getElementById(id);
		d.appendChild(myChildNode);
	} catch (e){
		alert("Unable to append to node '"+id+"'");
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_printToId(id, data)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will print the string held in the function parameter "data" to the tag with the id named in the 
- function parameter "id"
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function findid(id){
	try{
		var d = document.getElementById(id);
		return d;
//		d.focus();
	} catch (e) {
		alert("Can not set focus on element '"+id+"'");
		return null;
	}
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_enable_tab(tab)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will enable a hidden tab on a form
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_GENERAL_enable_tab(tab){
	//debug("tab :: "+tab+"\n");
	debug ("GetElementByID :: section_button_"+tab+"_btn\n");
	button_btn		= document.getElementById("section_button_"+tab+"_btn");
	button_btn.style.display="";
	try{
		debug ("GetElementByID :: section_button_"+tab+"_spacer\n");
		button_spacer	= document.getElementById("section_button_"+tab+"_spacer");
		button_spacer.style.display="";
	} catch(e){
		//the last entry does not have a spacer
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: LIBERTAS_GENERAL_disable_tab(tab)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will enable a hidden tab on a form
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_GENERAL_disable_tab(tab){
	//debug("tab :: "+tab+"\n");
	debug ("GetElementByID :: section_button_"+tab+"_btn\n");
	debug ("GetElementByID :: section_button_"+tab+"_spacer\n");
	button_btn		= document.getElementById("section_button_"+tab+"_btn");
	button_btn.style.display="none";
	try{
		button_spacer	= document.getElementById("section_button_"+tab+"_spacer");
		button_spacer.style.display="none";
	} catch(e){
		//the last entry does not have a spacer
	}
}

function untidy_quotes(str){
	find = new Array(
		new Array("&#34;",'\"'),
		new Array("&quot;",'\"'),
		new Array("&amp;quot;",'\"'),
		new Array("[[quote]]",'\"')
	);
	for(var index=0;index<find.length;index++){
		if (str.indexOf(find[index][0])!=-1){
			while (str.indexOf(find[index][0])!=-1){
				str = str.replace(find[index][0],find[index][1]);
			}
		}
	}
	return decodeURI(str);
}


function tidy_quotes(str){
	str = encodeURI(str);
	find = new Array(
		new Array("\%22","[[quote]]"),
		new Array("&amp;amp;#34;","[[quote]]"),
		new Array("&amp;#34;","[[quote]]"),
		new Array("&amp;amp;quot;","[[quote]]"),
		new Array("&amp;quot;","[[quote]]"),
		new Array("&#34;","[[quote]]"),
		new Array("&quot;","[[quote]]"),
		new Array("\"","[[quote]]")
	);
	for(var index=0;index<find.length;index++){
		if (str.indexOf(find[index][0])!=-1){
			while (str.indexOf(find[index][0])!=-1){
				str = str.replace(find[index][0],find[index][1]);
			}
		}
	}
	return str;
}

function check_format(thisObject,formatType){
	v = thisObject.value;
	if (formatType.substring(0,6)=='string'){
		var types = formatType.split("::");
		if(v.length < types[1]){
			alert('Sorry you must supply at least '+types[1]+' characters');
			thisObject.focus();
		}
	} else if (formatType=='password'){
		if(v.length<6){
			alert('Sorry you must supply at least 6 characters');
			thisObject.focus();
		} else {
			for(i=0;i<v.length;i++){
				if(
					(v.charCodeAt(i)>=48 && v.charCodeAt(i)<=57) || 
					(v.charCodeAt(i)>=65 && v.charCodeAt(i)<=90) || 
					(v.charCodeAt(i)>=97 && v.charCodeAt(i)<=122)
				){
					// do nothing
				} else {
					alert('Sorry you must supply at least 6 characters of numbers or letters (0-9 and a-z and A-Z)');
					thisObject.focus();
					break;
				}
			}
		}
	} else if (formatType=='number'){
		if (v*1 != v+""){
			alert('Sorry you must supply a number');
			thisObject.focus();
		}
	} else if (formatType=='notzero'){
		test = v*1;
		if ((test != v+"") || (test<=0)){
			alert('Sorry you must supply a number greater than zero');
			thisObject.focus();
		}
	} else if (formatType=='email'){
		at = thisObject.indexOf("@");
		dot = thisObject.substring(at+1).indexOf('.');
		if (at==-1 || dot==-1){
			alert('Sorry that is not a valid formatted email address');
			thisObject.focus();
		}
	}

}

function previewFrame(){
	// frame name
	fname			= "preview";
	document.all.preview.style.display='none';
	document.all.preview_loading.style.display='';
	f 				= get_form();
	prev_action 	= f.action;
	prev_command 	= f.command.value
	if(objects_to_check+""!="undefined"){
		for (var i=0; i<objects_to_check.length;i++){
			objects_to_check[i].enable_all();
		}
	}
	f.action 		= base_href+"admin/preview.php";
	f.command.value = f.preview_command.value;
	f.target 		= fname;
	if (check_editors+""!="undefined"){
		if (check_editors.length > 0){
			LIBERTAS_UpdateFields();
		}
	}
	f.submit();
	f.target 		= "";
	f.action 		= prev_action;
	f.command.value = prev_command;

	//	comment out for live data
	loc = new String(window.location);
	if(loc.indexOf("LIBERTAS_EDITOR=SHOW_PREVIEW")!=-1){
		document.all.preview.style.display='';
		document.all.preview_loading.style.display='none';
	}
}

function LIBERTAS_view_comments_click(identifier){
	var str = ""; // current cell
	path = base_href + 'admin/view_comments.php?identifier='+identifier+'&'+ session_url;
	var keys = showModalDialog(path, null , 'dialogHeight:400px; dialogWidth:480px; resizable:no; status:no');	
}

function group_toggle_access_group(t,s){
var gal = document.getElementById("hidden_group_access_label");
var ga = document.getElementById("hidden_group_access");
if (t.value==0){
	gal.style.display='';
	ga.style.display='';
} else {
	gal.style.display='none';
	ga.style.display='none';

}
}

function menu_check_parent(){
	var f = get_form();
	var mp = f.menu_parent;
	if (mp.options[mp.selectedIndex].style.color=="#666666"){
		alert("You do not have permission for this location");
		return false;
	} else {
		return true;
	}
	
	
}

function check_extraction(){
	var f= get_form();
	
	autosel = document.getElementById("automatic_selection");
	manusel = document.getElementById("manual_selection");
	if (f.mm_extract_type_format.selectedIndex==0){
		autosel.style.display='';
		manusel.style.display='none';
	} else {
		autosel.style.display='none';
		manusel.style.display='';
	}
}

function subsection_display_group(t,tag){
	if (t.value==0){
		document.getElementById(tag).style.display='none';
		document.getElementById(tag).style.visibility='hidden';
	} else {
		document.getElementById(tag).style.display='';
		document.getElementById(tag).style.visibility='visible';
	}
}

function get_dbfields_group(t,tag){
/*	var doc = document.getElementById("detail5");
//	doc.innerHTML = t.value;
	var sz="";
	sz += '<input type="text" name="identifier" id="identifier" value="' + t.value + '" />';
	doc.innerHTML = sz;
*/

	if (t.value != '-1'){
		document.getElementById('identifier').value=t.value;
		document.getElementById('command').value="EMAILADMIN_PRINT_LABELS";
		document.getElementById('user_form').submit();
	}
}

function chkMyfunc(t){
	alert('asdf');
}


function setquantity(n){
	var f = get_form();
	var el = document.getElementById("id_"+n);
	var quantity = document.getElementById("quantity_"+n);
	if(quantity.options[quantity.selectedIndex].value==-1){
		el.value = '-1';
		el.style.display = 'none';
	} else {
		el.style.display = '';
		el.value = '';
	}
}

function banner_type_edit(n){
	var el = document.getElementById("banner_edit_"+n);
	el.style.display='';
}
function banner_type_hide(n){
	var el = document.getElementById("banner_edit_"+n);
	var label_el = document.getElementById("l_"+n);
	var width_el = document.getElementById("w_"+n);
	var height_el = document.getElementById("h_"+n);
	var l_el = document.getElementById("bt_label_"+n);
	var w_el = document.getElementById("id_bt_width_"+n);
	var h_el = document.getElementById("id_bt_height_"+n);
	label_el.innerHTML = l_el.value;
	width_el.innerHTML = (w_el.value<1?"NA":w_el.value);
	height_el.innerHTML = (h_el.value<1?"NA":h_el.value);
	el.style.display='none';

}