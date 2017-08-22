/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- G E N E R A L   F U N C T I O N S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-


-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- General Variables
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var LIBERTAS_GENERAL_POPUP_function_properites = {};


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- General Methods
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function LIBERTAS_GENERAL_jtidy(str);
- function LIBERTAS_GENERAL_unjtidy(str);
- function LIBERTAS_GENERAL_cdata_plain_to_rich(str);
- function LIBERTAS_GENERAL_cdata_rich_to_plain(str);
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-
-
-
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
		} catch (e){
			debug_alert("problem while removing [[quote]] fn:: LIBERTAS_GENERAL_jtidy(str)")
		}
		try{
			str = encodeURI(new String(str));
		} catch (e){
			debug_alert("unable to encodeURI(str) fn:: LIBERTAS_GENERAL_jtidy(str)")
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
	var str = decodeURI(str);
	var find = new Array(
		new Array("&#60;","<"),
		new Array("&#62;",">"),
		new Array("&#34;","&quot;"),
		new Array("\r",""),
		new Array("\n","")
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
		myFrame = window.frames[id];
		return myFrame;
/*		if (browser=="IE"){
			return this[id];
		} else {
			 alert("Attempting w.l "+id);
			return window.document.iframe[id];
		}*/
	} catch (e){
		try{
			myFrame = document.getElementById(id);
			return myFrame;
		} catch (ee){
			debug_alert("unable to comply");
		}
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
			debug_alert("Unable to write to 'document.all[\""+id+"\"].innerHTML'");
		} else {
			debug_alert("Unable to write to 'document.layers[\""+id+"\"].innerHTML'");
		}
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
		debug_alert("Can not set focus on element '"+id+"'");
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
//	debug_alert(decodeURI(str));
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
