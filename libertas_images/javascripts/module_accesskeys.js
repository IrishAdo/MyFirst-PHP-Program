/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Javascript file to manage the accesskey administration manager
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Written by Adrian Sweneey
	- Libertas Solutions
	- 3rd April 2004
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- This File contains three Classes
	- 1. AccessKeyList
	- 2. AccessKeyEntry
	- 3. URL_Filter
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- $Revision: 1.3 $, $Date: 2004/04/14 12:03:48 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- V A R I A B L E   D E F I N I T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var menu_array		= Array();
var split_list 		= Array();
var blocked_list	= Array(
		"a", "b", "e", "f", "g", "h", "t", 
		"v", "w", "s", '"', "'", "!", "£", 
		"$", "%", "^", "&", "*", "(", ")", 
		"_", "+", "{", "}", ":", "@", "~", 
		"<", ">", "-", "=", "[", "]", ";", 
		"#", ",", ".", "/", "1", "2", "3", 
		"4", "5", "6", "7", "8", "9", "0", 
		"S", '`', '¬', '|', '\\', 'm'
);
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L O C A L E   D E F I N I T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var LOCALE_ACCESSKEYS_RETRIEVE_PAGES 			= 'Retrieve pages';
var LOCALE_ACCESSKEYS_SORRY_UNUSEABLE_LETTER	= 'Sorry that letter can not be assigned please choose another letter';
var LOCALE_ACCESSKEYS_RETRIEVING_PAGES			= 'Retrieving list of requested pages';
var LOCALE_ACCESSKEYS_SORRY_LETTER_START 		= 'Sorry the letter "';
var LOCALE_ACCESSKEYS_SORRY_LETTER_FINISH		= '" is already assigned please choose another letter';
var LOCALE_ACCESSKEYS_EDIT						= 'Edit';
var LOCALE_ACCESSKEYS_REMOVE					= 'Remove';
var LOCALE_ACCESSKEYS_NEW						= 'New';
var LOCALE_ACCESSKEYS_FILTER_NAME 				= 'Choose url for access key';
var LOCALE_ACCESSKEYS_TITLE						= 'Title';
var LOCALE_ACCESSKEYS_LETTER					= 'Letter';
var LOCALE_ACCESSKEYS_LINK						= 'Link';
var LOCALE_ACCESSKEYS_OPTIONS					= 'Options';
var LOCALE_ACCESSKEYS_LINK_MENU					= '--- Link to this menu location ---';
var LOCALE_ACCESSKEYS_CHOOSE_MENU				= "--- Select a menu location ---";
var LOCALE_ACCESSKEYS_CHOOSE_MENU_FIRST			= "Select a menu location first";
var LOCALE_ACCESSKEYS_MENU_LOCATION				= "Menu Location";
var LOCALE_ACCESSKEYS_EDIT_URL					= "Update";
var LOCALE_ACCESSKEYS_ADD_URL					= "Insert";
var LOCALE_ACCESSKEYS_CANCEL					= "Cancel";
var LOCALE_ACCESSKEYS_LOADING_ADD				= "<input type=button value='Select URL' class='bt' onclick='myAccessKeys.urlFilter.getURL()'/>";
var LOCALE_ACCESSKEYS_LOADING_EDIT				= "<input type=button value='Change URL' class='bt' onclick='myAccessKeys.urlFilter.getURL()'/>";
var LOCALE_ACCESSKEYS_WAITING					= "Waiting ...";
var LOCALE_ACCESSKEYS_NO_PAGES					= "No pages available using menu url";
var LOCALE_ACCESSKEYS_PAGE_LIST					= "Page list";
var LOCALE_ACCESSKEYS_TITLE_EXISTS				= "Sorry a accesskey with that Title already exists please chosoe another Title";
var LOCALE_ACCESSKEYS_FILL_IN_FORM				= "You are required to fill in the complete form.";
var LOCALE_ACCESSKEYS_FILL_IN_FORM_LETTER		= "You must specify a letter to use.";
var LOCALE_ACCESSKEYS_FILL_IN_FORM_TITLE		= "You must specify a title to for this.";
var LOCALE_ACCESSKEYS_FILL_IN_FORM_MENU			= "You must specify at least a menu location to link to.";
var LOCALE_NO_KEYS_LEFT 						= "<h1>Sorry there are no more Access Keys available </h1>";

/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-                    END OF GLOBAL VARIABLES                    -=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- A C C E S S   K E Y   L I S T   -   C L A S S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function AccessKeyList(){
	// properties
	this.list		= new Array();
	this.urlFilter	= new URL_Filter();

	// methods
	this.draw		= _display;
	this.add		= _add;
	this.addEntry	= _addEntry;
	this.change		= _change;
	this.getURL		= _getURL;
	this.setURL		= _setURL;
	this.cancelURL	= _cancelURL;
	this.blank		= _blank;
	this.remove		= _remove;
	this.update		= __accessKeys_updateField;
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Add new Access key entry
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function _add(letter, label, url, access_type, title){
	i = this.list.length;
	this.list[i] = new AccessKeyEntry();
	if (label.length>0){
		this.list[i].set("label", LIBERTAS_GENERAL_unjtidy(label));
	} else {
		this.list[i].set("label", "");
	}
	if (title.length>0){
		this.list[i].set("title", LIBERTAS_GENERAL_unjtidy(title));
	} else {
		this.list[i].set("title", "");
	}
	this.list[i].set("url"	, url);
	if (letter.length>0){
		this.list[i].set("letter"	, new String(letter).toLowerCase());
	} else {
		this.list[i].set("letter"	, "");
	}
	if (access_type+"" == "undefined"){
		this.list[i].set("type"		, '0');
	} else {
		this.list[i].set("type"		, access_type+'');
	}
	this.update();
	return i;
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- change Attribute
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function _change(position, field, letter){
	ok = 1;
	fv = field.value.toLowerCase();
	field.value = fv;
	if (letter=="letter"){
		for(var i =0; i< blocked_list.length;i++){
			if (blocked_list[i]==fv)
				ok = 0;
		}
		for(var i =0; i< this.list.length;i++){
			if (this.list[i].access_letter==fv && this.list[i].access_letter!=position)
				ok = -1;
		}
	}
	if (ok == 1){
	// do nothing special
	} else {
		field.value='';
		if (ok == 0)
			alert(LOCALE_ACCESSKEYS_SORRY_UNUSEABLE_LETTER);
		if (ok == -1)
			alert(LOCALE_ACCESSKEYS_SORRY_LETTER_START + fv + LOCALE_ACCESSKEYS_SORRY_LETTER_FINISH);
	}
}


function _blank(position){
	found=-1;
	for(var i =0; i<this.list.length; i++){
		if (this.list[i].access_letter == position){
			found =i;
		}
	}
	fnd=found;
	if (found!=-1)
		this.list[found].blank(position);
	this.update();
}
function _remove(position){
	found=-1;
	for(var i =0; i<this.list.length; i++){
		if (this.list[i].access_letter == position){
			found =i;
		}
	}
	fnd=found;
	if (found!=-1){
		this.list[found].remove(position,found);
		this.list.splice(found,1);
	}
	this.update();
}
function _getURL(position){
	found=-1;
	for(var i =0; i<this.list.length; i++){
		if (this.list[i].access_letter == position){
			found =i;
		}
	}
	fnd=found;
	if (found!=-1)
		this.list[found].getURL(position);
}

function _setURL(position){
	frm = formReady();
	ok=1;
	for(var i =0; i<this.list.length; i++){
		found=0;
//		alert(this.list[i].access_label);
		if (this.list[i].access_letter == document.AccessKeys.setLetter.value){
			found =1;
		}
		if (found==0 && position!=this.list[i].access_letter && document.AccessKeys.setLabel.value.toLowerCase()==this.list[i].access_label.toLowerCase()){
			alert(LOCALE_ACCESSKEYS_TITLE_EXISTS);
			ok=0;
		}
	}
	if(ok==1){
		if (frm==1){
			found=-1;
			for(var i =0; i<this.list.length; i++){
				if (this.list[i].access_letter == position){
					found =i;
				}
			}	
			fnd=found;
			if (found==-1){
				found = this.list.length;
				this.list[found] = new AccessKeyEntry();
				this.list[found].set("type",'2');
				this.list[found].set("letter",position);
				fnd = -1;
			}
			this.list[found].setURL(position,fnd);
			this.update();
		} else {
			if (document.AccessKeys.setLetter.value==''){
				alert(LOCALE_ACCESSKEYS_FILL_IN_FORM_LETTER);	
				document.AccessKeys.setLetter.focus();
			} else if (document.AccessKeys.setLabel.value==''){
				alert(LOCALE_ACCESSKEYS_FILL_IN_FORM_TITLE);	
				document.AccessKeys.setLabel.focus();
			} else if (document.AccessKeys.menu_locations+''=='undefined'){
				alert(LOCALE_ACCESSKEYS_FILL_IN_FORM_MENU);	
			} else if (document.AccessKeys.menu_locations.selectedIndex==0){
				alert(LOCALE_ACCESSKEYS_FILL_IN_FORM_MENU);	
			} else {
				alert(LOCALE_ACCESSKEYS_FILL_IN_FORM);
			}
		}
	}
}
function _cancelURL(){
	this.urlFilter.hide();
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fill in the list of questions to add a new entry into the Access list
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function _addEntry(){
	this.urlFilter.show(-1);
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Add new Access key entry
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function _display(){
	myTable	= document.createElement("TABLE");
	myTable.setAttribute("id","accessList");
	myTable.setAttribute("name","accessList");
	myRow	= document.createElement("TR");
	myCell	=  document.createElement("TD");
	myCell.className = 'bt';
	myCell.setAttribute("align","left");
	myCell.innerHTML = LOCALE_ACCESSKEYS_LETTER;
	myRow.appendChild(myCell);
	myCell	=  document.createElement("TD");
	myCell.setAttribute("align","left");
	myCell.className = 'bt';
	myCell.innerHTML = LOCALE_ACCESSKEYS_TITLE;
	myRow.appendChild(myCell);
	myCell	=  document.createElement("TD");
	myCell.className = 'bt';
	myCell.setAttribute("align","left");
	myCell.setAttribute("width","150px");
	myCell.innerHTML = LOCALE_ACCESSKEYS_LINK;
	myRow.appendChild(myCell);
	myCell	=  document.createElement("TD");
	myCell.className = 'bt';
	myCell.setAttribute("align","left");
	myCell.setAttribute("width","125px");
	myCell.innerHTML = LOCALE_ACCESSKEYS_OPTIONS;
	myRow.appendChild(myCell);
	myTable.appendChild(myRow);
	for (var index=0;index<this.list.length;index++){
		myTable.appendChild(this.list[index].draw(index));
	}
	myRow	= document.createElement("TR");
	myCell	=  document.createElement("TD");
	myCell.setAttribute("colspan","3");
	myCell.setAttribute("align","right");
	myCell.innerHTML = "<input type='button' onclick='javascript:myAccessKeys.addEntry()' class='bt' value='"+LOCALE_ACCESSKEYS_NEW+"'>";
	myRow.appendChild(myCell);
	myTable.appendChild(myRow);
	LIBERTAS_GENERAL_printToId("AccessArea", myTable.outerHTML);
	f = get_form();
	f.numberOfAccessKeys.value = this.list.length;
}

function __accessKeys_updateField(){
	f = get_form();
	f.listOfkeys.value = "";
	for (var i=0;i<this.list.length;i++){
		if (f.listOfkeys.value != ""){
			f.listOfkeys.value += ", ";
		}
		f.listOfkeys.value += this.list[i].access_letter;
	}

}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-                          END OF CLASS                         -=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/


/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- A C C E S S   K E Y   E N T R Y   -   C L A S S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- function definition for this class
	-	this.draw(pos)			= _return_table_row;
	-	this.set(name,value)	= _set_attribute;
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function AccessKeyEntry(){
	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- Properties
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	this.access_label		= "";
	this.access_title		= "";
	this.access_letter		= "";
	this.access_url 		= -1;
	this.access_type		= 0; // 0 = system Defined, 1 = user defined;
	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- Methods
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	this.toString			= _displayString;
	this.draw				= _entry_return_table_row;
	this.set				= _entry_set_attribute;
	this.getURL				= _entry_getURL;
	this.setURL				= _entry_setURL;
	this.blank				= _entry_blank;
	this.remove				= _entry_remove;
}

function _displayString(){
	S  = "access_label : " + this.access_label + "\n";
	S += "access_title : " + this.access_title + "\n";
	S += "access_letter: " + this.access_letter + "\n";
	S += "access_url   : " + this.access_url + "\n";
	S += "access_type  : " + this.access_type + "\n";
	return S;
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Add new Access key entry
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function _entry_set_attribute(attribute_name, attribute_value){
	try{
		if (attribute_name=="letter" && attribute_value==''){
			this.access_letter = '';
		}else {
			eval("this.access_"+attribute_name+" = '"+attribute_value.split("'").join("\\'")+"'");
		}
	} catch(e){
		alert(e.message +"this.access_"+attribute_name+" = '"+attribute_value+"'");
	}
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- add a new row to the table
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function _entry_return_table_row(pos){
	myRow	= document.createElement("TR");
	myCell	=  document.createElement("TD");
	var txt= "<input type='hidden' name='type_"+this.access_letter+"' value='"+this.access_type+"'/>";
		txt += "<input type='hidden' name='letter_"+this.access_letter+"' value='"+this.access_letter+"'/><span id='lettertxt_"+this.access_letter+"'>"+this.access_letter+"</span>";
	myCell.innerHTML =txt;
	myRow.appendChild(myCell);
	myCell	=  document.createElement("TD");
	myCell.setAttribute("valign","top");
	myCell.innerHTML = "<span id='labeltxt_"+this.access_letter+"'>"+this.access_label+"</span>";
	myinput	= document.createElement("<INPUT type='hidden' name='label_"+this.access_letter+"' value='"+this.access_label+"'/>");
	myCell.appendChild(myinput);
				
	myRow.appendChild(myCell);
	myCell	=  document.createElement("TD");
	myCell.setAttribute("valign","top");
	if (this.access_type == 0){
		myCell.innerHTML = "Predefined";
	} else {
		myCell.innerHTML = "";
		myinput	= document.createElement("<div name='txturl_"+this.access_letter+"' id='txturl_"+this.access_letter+"' style='display:inline'></div>");
		myinput.innerHTML = "<a href='"+this.access_url+"' target='_external'>"+this.access_title.split("::").join("")+"</a>";
		myCell.appendChild(myinput);
		myinput	= document.createElement("<INPUT type='hidden' name='url_"+this.access_letter+"' maxlength='255' value='"+this.access_url+"' onchange='javascript:myAccessKeys.change(\""+this.access_letter+"\",this,\"url\")' size=40 />");
		myCell.appendChild(myinput);
		myinput	= document.createElement("<INPUT type='hidden' name='title_"+this.access_letter+"' maxlength='255' value='"+this.access_title+"'/>");
		myCell.appendChild(myinput);
	}
	myRow.appendChild(myCell);
	myCell	=  document.createElement("TD");
	myCell.setAttribute("valign","top");
	if (this.access_type != 0){
		myButton = document.createElement("<input type='button' value='"+LOCALE_ACCESSKEYS_EDIT+"' class='bt' onclick='javascript:myAccessKeys.getURL(\""+this.access_letter+"\")'/>");
		myCell.appendChild(myButton);
		if (this.access_type == 1){
			btnstr ="<input type='button' id='blank_"+this.access_letter+"' name='blank_"+this.access_letter+"' value='"+LOCALE_ACCESSKEYS_REMOVE+"' class='bt' onclick='javascript:myAccessKeys.blank(\""+this.access_letter+"\")' ";
			if (this.access_title==''){
				btnstr+=" style='display:none;'";
			}
			btnstr+="/>";
		} else if (this.access_type == 2){
			btnstr ="<input type='button' id='blank_"+this.access_letter+"' name='blank_"+this.access_letter+"' value='"+LOCALE_ACCESSKEYS_REMOVE+"' class='bt' onclick='javascript:myAccessKeys.remove(\""+this.access_letter+"\")' />";
		}
		myButton = document.createElement(btnstr);
		myCell.appendChild(myButton);
	} else {
		myCell.innerHTML="";
	}
	myRow.appendChild(myCell);
	return myRow;
}

/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Call the Get Filter Functionality
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function _entry_getURL(pos){
//	this.access_letter = "";
	
//	url_field = document.getElementById("url_"+pos);
	myAccessKeys.urlFilter.show(pos);
}

/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- call the set url functionality
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function _entry_setURL(pos,fnd){
	f = document.AccessKeys.pages_in_locations;
	if (f+""=="undefined"){
		// assign the menu url
		f = document.AccessKeys.menu_locations;
		if (f+""=="undefined"){
			//no adding url properly
			this.access_label		= document.AccessKeys.setLabel.value;
			if (typeof document.AccessKeys.setLetter == "select"){
				this.access_letter		= document.AccessKeys.setLetter.options[document.AccessKeys.setLetter.selectedIndex].value;
			} else {
				this.access_letter		= document.AccessKeys.setLetter.value;
			}
		} else {
			if (f.selectedIndex!=0){
				mylist = f.options[f.selectedIndex].value.split("::");	
				this.access_url			= mylist[2];
				this.access_title		= document.AccessKeys.menu_locations.options[document.AccessKeys.menu_locations.selectedIndex].text.split("::").join("");
			} else {
				this.access_url			= document.AccessKeys.setURL.value;
				this.access_title		= document.AccessKeys.setTitle.value;
			}
			this.access_label		= document.AccessKeys.setLabel.value;
			this.access_letter		= document.AccessKeys.setLetter.value;
		}
	} else {
		// assign the page url
		this.access_url			= f.options[f.selectedIndex].value;
		this.access_label		= document.AccessKeys.setLabel.value;
		this.access_letter		= document.AccessKeys.setLetter.value;
		if (f.selectedIndex==0){
			// overriding page url use the menu url
			this.access_title	= document.AccessKeys.menu_locations.options[document.AccessKeys.menu_locations.selectedIndex].text;
		} else {
			var index 				= f.selectedIndex;
			var fieldValue 			= cache_data.frmDoc.page_data.value;
			var tmp 				= new String(fieldValue);
			var myArray 			= tmp.split("|1234567890|");		 
			c						= 0;
			for(i=0;i<myArray.length; i+=2){
				if (c==index){
					label_str = myArray[i].split("&#39;").join("'");
				}
				c++;
			}
			this.access_title		= label_str;
		}
	}
	var myTable		= document.getElementById("accessList");
	var index = myTable.rows.length-1;
	if (fnd==-1){
		i=myAccessKeys.add("","","",'2',"");
		var myNewRow	= myTable.insertRow(index);
		var myCell = myNewRow.insertCell(-1);
		myCell.setAttribute("valign","top");
		var txt= "<input type='hidden' name='type_"+(this.access_letter)+"' value='2'/><input type='hidden' name='letter_"+(this.access_letter)+"' value='"+this.access_letter+"'/><span id='lettertxt_"+this.access_letter+"'>"+this.access_letter+"</span>";
		myCell.innerHTML = txt;
		var myCell = myNewRow.insertCell(-1);
		myCell.setAttribute("valign","top");
		myCell.innerHTML = "<input type='hidden' name='label_"+(this.access_letter)+"' value='"+this.access_label+"'/><span id='labeltxt_"+this.access_letter+"'>"+this.access_label+"</span>";
		var myCell = myNewRow.insertCell(-1);
			myCell.setAttribute("valign","top");
			myinput	= document.createElement("<input type='hidden' name='url_"+(this.access_letter)+"' value='"+this.access_url+"'/>");
			myCell.appendChild(myinput);
			myinput	= document.createElement("<INPUT type='hidden' name='title_"+(this.access_letter)+"' value='"+this.access_title+"'/>");
			myCell.appendChild(myinput);
			myinput	= document.createElement("<div name='txturl_"+(this.access_letter)+"' id='txturl_"+(this.access_letter)+"'></div>");
			myinput.innerHTML = "<a href='"+this.access_url+"' target='_external'>"+this.access_title.split("::").join("")+"</a>";
			myCell.appendChild(myinput);
		var myCell = myNewRow.insertCell(-1);
		mybtn = document.createElement("<input type='button' value='"+LOCALE_ACCESSKEYS_EDIT+"' class='bt' onclick='javascript:myAccessKeys.getURL(\""+this.access_letter+"\")'/>");
		myCell.appendChild(mybtn);
		mybtn = document.createElement("<input type='button' id='blank_"+(this.access_letter)+"' name='blank_"+(this.access_letter)+"' value='"+LOCALE_ACCESSKEYS_REMOVE+"' class='bt' onclick='javascript:myAccessKeys.remove(\""+(this.access_letter)+"\")'/>");
		myCell.appendChild(mybtn);
	} else {
		txturl = document.getElementById("txturl_"+pos);
		txturl.innerHTML = "<a href='"+this.access_url+"' target='_external'>" + this.access_title.split("::").join("") + "</a>";
		var txt = document.getElementById("lettertxt_"+pos);
		txt.innerHTML = this.access_letter;
		txt = document.getElementById("labeltxt_"+pos);
		txt.innerHTML = this.access_label;
		url_field = document.getElementById("url_"+pos);
		url_field.value = this.access_url;
		title_field = document.AccessKeys.all["title_"+pos];
		title_field.value = this.access_title;
		letter_field = document.getElementById("letter_"+pos);
		letter_field.value = this.access_letter;
		label_field = document.AccessKeys.all["label_"+pos];
		label_field.value = this.access_label;
	}
	f = get_form();
	f.numberOfAccessKeys.value = myAccessKeys.list.length;
	document.getElementById("blank_"+this.access_letter).style.display='';
	document.getElementById("AccessFilter").style.display='none';	
}

function _entry_blank(pos){
	this.access_url = "";
	this.access_title = "";
	url_field = document.getElementById("url_"+pos);
	url_field.value = this.access_url;
	url_field = document.getElementById("title_"+pos);
	url_field.value = this.access_title;
	document.all["blank_"+pos].style.display='none';
	txtarea = document.getElementById("txturl_"+pos);
	txtarea.innerHTML="";
	
}
function _entry_remove(pos,fnd){
	this.access_url		 = "";
	this.access_label	 = "";
	this.access_title	 = "";
	this.access_type	 = "";
	this.access_letter		 = "";
	url_field = document.getElementById("url_"+pos);
	url_field.value = "";
	var myTable		= document.getElementById("accessList");
	if (fnd+1<=myTable.rows.length);
		myTable.deleteRow(fnd + 1);
	/*
	myTable.rows[fnd + 1].deleteCell(0);
	myTable.rows[fnd + 1].deleteCell(0);
	myTable.rows[fnd + 1].deleteCell(0);
	myTable.rows[fnd + 1].deleteCell(0);
	myTable.rows[fnd + 1].insertCell();
	myTable.rows[fnd + 1].insertCell();
	myTable.rows[fnd + 1].insertCell();
	myTable.rows[fnd + 1].insertCell();
	*/
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-                          END OF CLASS                         -=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/


/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- A C C E S S   K E Y   U R L   F I L T E R   -   C L A S S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function URL_Filter(){

	this.menu_list		= Array();
	this.filter_loaded	= 0;
	
	this.show 			= __URL_FILTER_SHOW;
	this.hide 			= __URL_FILTER_HIDE;
	this.loadMenu 		= __LOAD_MENU;
	this.getURL			= __URL_GET;
}

function __URL_GET(){
	this.loadMenu();
	document.getElementById("locationpagerow").style.display='';
}

function __URL_FILTER_HIDE(){
	document.all.AccessFilter.style.display='none';
}

function __URL_FILTER_SHOW(pos){
	found = "";
	var myentryObject = null
	if (pos==-1){
		myentryObject = new AccessKeyEntry();
		myentryObject.access_type=2;
	} else {
		for (var i=0; i<myAccessKeys.list.length;i++){
			if (myAccessKeys.list[i].access_letter == pos){
				myentryObject = myAccessKeys.list[i];
			}
		}
	}
	comma_list	= new String(" "+blocked_list.join(", ")+" "+f.listOfkeys.value).toUpperCase();
	option_list	= "";
	for(index = 0; index<26;index++){
		letter = String.fromCharCode(index+65);
		if (comma_list.indexOf(" "+letter.toUpperCase()+",") ==-1 || letter == myentryObject.access_letter){
			option_list += "<option value='"+letter+"' ";
			if (letter == myentryObject.access_letter){
				option_list += " selected='true'";
			}
			option_list += ">"+letter+"</option>";
		}
	}
	if(option_list!=""){
		myTable	= document.createElement("TABLE");
		myTable.setAttribute("width","400");
		myRow	= document.createElement("TR");
		myCell	= document.createElement("TD");
		myCell.setAttribute("colspan","2");
		myCell.className ="bt";
		myCell.innerHTML=LOCALE_ACCESSKEYS_FILTER_NAME;
		myCell.setAttribute("width","100%");
		myRow.appendChild(myCell);
		myTable.appendChild(myRow);
	
			myRow	= document.createElement("TR");
			myCell	= document.createElement("TD");
			myCell.setAttribute("width","150px");
			myCell.innerHTML=LOCALE_ACCESSKEYS_LETTER;
			myRow.appendChild(myCell);
			myCell	= document.createElement("TD");
			if ((myentryObject.access_type == 0)||(myentryObject.access_type == 1)){
				myCell.innerHTML = "<input type='hidden' name='setLetter' id='setLetter' value='"+myentryObject.access_letter+"' maxlength='1' size='1'/>"+myentryObject.access_letter+"";
			}else{
	//			myCell.innerHTML = "<input type='text' name='setLetter' id='setLetter' value='"+myentryObject.access_letter+"' maxlength='1' size='1' onchange='javascript:myAccessKeys.change("+(pos)+",this,\"letter\")'/>";
				out	 = "<select name='setLetter' id='setLetter'";
				if(pos!=-1){
					out	 += " disabled='true'";
				}
				out	 += ">"+option_list;
				out += "</select>";
				
				myCell.innerHTML =out;
			}
			myRow.appendChild(myCell);
			myTable.appendChild(myRow);
	
			myRow	= document.createElement("TR");
			myCell	= document.createElement("TD");
			myCell.setAttribute("width","150px");
			myCell.innerHTML=LOCALE_ACCESSKEYS_TITLE;
			myRow.appendChild(myCell);
			myCell	= document.createElement("TD");
			if ((myentryObject.access_type == 0)||(myentryObject.access_type == 1)){
				myinnerHTML = "<input type='hidden' name='setLabel' value='"+myentryObject.access_label+"'/>"+myentryObject.access_label+"";
				myinnerHTML += "<input type='hidden' name='setURL' value='"+myentryObject.access_url+"' />";
				myinnerHTML += "<input type='hidden' name='setTitle' value='"+myentryObject.access_title+"' />";
				myCell.innerHTML = myinnerHTML;
			} else {
				myinnerHTML = "<input type='text' name='setLabel' value='"+myentryObject.access_label+"' maxlenght='255' size='40'/>";
				myinnerHTML += "<input type='hidden' name='setURL' value='"+myentryObject.access_url+"' />";
				myinnerHTML += "<input type='hidden' name='setTitle' value='"+myentryObject.access_title+"' />";
				myCell.innerHTML = myinnerHTML;
			}
			myRow.appendChild(myCell);
			myTable.appendChild(myRow);
	
			myRow	= document.createElement("TR");
			myCell	= document.createElement("TD");
			myCell.setAttribute("width","150px");
			myCell.innerHTML=LOCALE_ACCESSKEYS_MENU_LOCATION;
			myRow.appendChild(myCell);
			myCell	= document.createElement("TD");
			myCell.setAttribute("ID","menulocationcell");
			if(pos==-1){
				myCell.innerHTML=LOCALE_ACCESSKEYS_LOADING_ADD;
			} else {
				myCell.innerHTML=LOCALE_ACCESSKEYS_LOADING_EDIT;
			}
			myRow.appendChild(myCell);
			myTable.appendChild(myRow);
		
			myRow	= document.createElement("TR");
			myRow.setAttribute("ID","locationpagerow");
			myRow.style.display='none';
			myCell	= document.createElement("TD");
			myCell.setAttribute("width","150px");
			myCell.innerHTML=LOCALE_ACCESSKEYS_PAGE_LIST;
			myRow.appendChild(myCell);
			myCell	=  document.createElement("TD");
			myCell.setAttribute("ID","locationpagecell");
			myCell.innerHTML=LOCALE_ACCESSKEYS_WAITING;
			myRow.appendChild(myCell);
			myTable.appendChild(myRow);
			
			myRow	=  document.createElement("TR");
			myCell	=  document.createElement("TD");
			myCell.setAttribute("colspan","2");
			myCell.setAttribute("id","submitBtnCell");
			myCell.setAttribute("name","submitBtnCell");
			if(pos==-1){
				buttonLabel = LOCALE_ACCESSKEYS_ADD_URL;
			} else {
				buttonLabel = LOCALE_ACCESSKEYS_EDIT_URL;
			}
			out  = "<input type='button' onclick='javascript:myAccessKeys.cancelURL();' value='"+LOCALE_ACCESSKEYS_CANCEL+"' class='bt' />&#160;<input type='button' onclick='javascript:myAccessKeys.setURL(\""+pos+"\");' value='"+buttonLabel+"' class='bt' id='submitBtn' name='submitBtn' />";
			myCell.innerHTML = out;
			myRow.appendChild(myCell);
//		myRow.appendChild(myCell);
		myTable.appendChild(myRow);
		LIBERTAS_GENERAL_printToId("AccessFilter", myTable.outerHTML);
		this.filter_loaded=1;
		document.all.AccessFilter.style.display='';	
	} else {
		LIBERTAS_GENERAL_printToId("AccessFilter", LOCALE_NO_KEYS_LEFT);
	}
}
function __LOAD_MENU(){
	__extract_information('menu',"");
}


function __extract_information(szType, parameter){
	/*
		check to see if the cache is available for information yet? 
	*/
	if (cache_data.document.readyState != 'complete'){
		setTimeout("__extract_information('"+szType+"', '"+parameter+"');",1000);
		return;
	} else {
		if (szType=='menu' && cache_data.frmDoc.menu_data.value==''){
			cache_data.__extract_info('menu');
		}
		if (szType=='page'){
			cache_data.frmDoc.page_data.value='';
			cache_data.__extract_info('page',parameter);
		}
		setTimeout("__retrieve_info('"+szType+"', '"+parameter+"');",1000);
	}
}

function __retrieve_info(szType, parameter){
	if (szType=='menu'){
		if (cache_data.frmDoc.menu_data.value!=''){
			fieldValue = cache_data.frmDoc.menu_data.value;
			var prev_style="",identifier=-1 ,parent_identifier=-1;
			var mystyle ="";
			output = "<select name='menu_locations' id='menu_locations' onchange='javascript:startPage();'>";
			
			if (f!=''){
				if (f!='__NOT_FOUND__'){
					found			= 0;
					tmp 			= new String(fieldValue);
					myArray 		= tmp.split("|1234567890|");
					len				= myArray.length-1;
					list			= "";
					output += "<option value=''>"+LOCALE_ACCESSKEYS_CHOOSE_MENU+"</option>";
					for (var i = 0; i<len; i+=2){
						split_list = myArray[i+1].split("::");
						if (split_list[3]+''=='1'){
							mystyle				= "#ff0000";
							parent_identifier	= split_list[1];
							identifier 			= split_list[0];
						} else {
							if (split_list[1]==identifier){
								
							} else {
								mystyle = "#000000";
								parent_identifier	= -1;
								identifier 			= -1;
							}
						}

						label_str = fix(myArray[i].split("&#39;").join("'").split("&#160;").join("::"));
						output += "<option value='"+fix(myArray[i+1])+"'>"+label_str+"</option>";
					}
				}
			}
			output += "</select>";
			LIBERTAS_GENERAL_printToId("menulocationcell", output);
			LIBERTAS_GENERAL_printToId("locationpagecell", LOCALE_ACCESSKEYS_CHOOSE_MENU_FIRST);
		} else {
			setTimeout("__retrieve_info('"+szType+"', '"+parameter+"');",1000);
		}
	} else if (szType=='page'){
		if (cache_data.frmDoc.page_data.value!=''){
			fieldValue = cache_data.frmDoc.page_data.value;
			var prev_style="",identifier=-1 ,parent_identifier=-1;
			var mystyle ="";
			f = document.AccessKeys.menu_locations;
			split_list = f.options[f.selectedIndex].value.split("::");
			
			if (f!=''){
				if (f!='__NOT_FOUND__'){
					found			= 0;
					tmp 			= new String(fieldValue);
					myArray 		= tmp.split("|1234567890|");
					len				= myArray.length-1;
					list			= "";
					if (len>0){
						var output = "<select name='pages_in_locations' id='pages_in_locations'>";
						output += "<option value='"+split_list[2]+"'>"+LOCALE_ACCESSKEYS_LINK_MENU+"</option>";
						for (var i = 0; i<len; i+=2){
							label_str = myArray[i].split("&#39;").join("'");
							label_str = fix(label_str.split("#39;").join("'"));
								if (label_str.length>30){
								label_str = label_str.substring(0,15)+"..."+label_str.substring(label_str.length-15);
							}
							output += "<option value='"+fix(myArray[i+1])+"'>"+label_str+"</option>";
						}
						output += "</select>";
					} else {
						output = LOCALE_ACCESSKEYS_NO_PAGES;
					}
				}
			}
			LIBERTAS_GENERAL_printToId("locationpagecell", output);
		} else {
			setTimeout("__retrieve_info('"+szType+"', '"+parameter+"');",1000);
		}
	}

}

function fix(str){
	return convert_special_characters(str);
}

function startPage(){
	LIBERTAS_GENERAL_printToId("locationpagecell", "<input class='bt' type='button' value='"+LOCALE_ACCESSKEYS_RETRIEVE_PAGES+"' onclick='javascript:extract_page_url(this.document,\"__RETRIEVE__\")'/>");
}

function extract_page_url(t,cmdtype){
		LIBERTAS_GENERAL_printToId("locationpagecell", "<font color='#ff0000'>"+LOCALE_ACCESSKEYS_RETRIEVING_PAGES+"</font>");
		f = document.AccessKeys.menu_locations;
		split_list = f.options[f.selectedIndex].value.split("::");
		__extract_information('page',"menu_url="+escape(split_list[2]));
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-                          END OF CLASS                         -=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function formReady(){
	var ok = 1;
	f = get_form();
	try{
		if (f.menu_locations.selectedIndex == 0){
			if ((f.setURL.value == '') || (f.setURL.value == '-1')){
				ok=-1;
			}
		} else {
			f.setURL.value = f.menu_locations.options[f.menu_locations.selectedIndex].value.split("::")[2];
		}
	} catch (e){
		// menu Locatiosn select boxes not available 
		if ((f.setURL.value == '') || (f.setURL.value == '-1')){
			ok=-1;
		}
	}
	if (f.setLetter.value == ''){
		ok=-1;
	} 
	if (f.setLabel.value == ''){
		ok=-1;
	} 
	if ((f.setURL.value == '') || (f.setURL.value == '-1')){
		ok=-1;
	} 
//	alert("["+f.setURL.value+"] = "+f.setURL.value.length);
	return ok;
}

function formCheck(){
	if (formReady() == 1){
		document.all.submitBtn.style.display = '';
	} else {
		document.all.submitBtn.style.display = 'none';
	}
}
