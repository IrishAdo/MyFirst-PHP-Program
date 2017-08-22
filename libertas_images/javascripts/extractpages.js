/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- L I B E R T A S   S O L U T I O N S   -   M E N U L I N K S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function menuLinks(){
	this.dhtml			= new DhtmlScript();					// include html generation code builder 
	this.links 			= new Array();
	this.name			= "menulinks";
	this.idIndex		= 0;
	this.firstTime		= 1;
	
	this.draw					= __lib_menulink_draw;							
	this.get_buttons			= __lib_menulink_get_buttons;					// get buttons for display
	this.add_new				= __lib_menulink_add_new;						
	this.set_menu				= __lib_menulink_set_menu;						
	this.set_retrieve_button	= __lib_menulink_set_retrievebutton;			
	this.retrievePages			= __lib_menulink_retrievePages;					
	this.exec_info				= __lib_menulink_extract_information;			// 
	this.getPages				= __lib_menulink_getPages;						
	this.updateChoosenMenu		= __lib_menulink_updateChoosenMenu;				
	this.setUrl					= __lib_menulink_setUrl;				
	this.remove					= __lib_menulink_remove;				// remove
	this.edit					= __lib_menulink_edit;				// add an entry on load to the selectedArray property
	this.up						= __lib_menulink_up;				// up
	this.down					= __lib_menulink_down;				// down
	this.cancel					= __lib_menulink_cancel;			// cancel
	this.update					= __lib_menulink_update;			// update
	this.displayblock			= __lib_menulink_displayblock;		// block of info representing links;
	this.trim					= __lib_menulink_trim;
	this.getMenu				= __lib_menulink_getMenu;
	
//	this.getMenu();
}
function __lib_menulink_setUrl(){
	cPage = document.getElementById("choosenPage");
	var f = get_form();
	if((cPage+""!="undefined") && (cPage+""!="null")){
		// page set
		f.choosenLabel.value 		= this.trim(cPage.options[cPage.selectedIndex].text);
		f.choosenTitleLabel.value	= this.trim(f.choosenLabel.value);
		f.choosenUrl.value			= this.trim(cPage.options[cPage.selectedIndex].value);
	} else {
		// menu set
		cMenu = document.getElementById("choosenMenu");
		f.choosenLabel.value		= this.trim(cMenu.options[cMenu.selectedIndex].text);
		f.choosenTitleLabel.value 	= this.trim(f.choosenLabel.value);
		var sections = cMenu.options[cMenu.selectedIndex].value.split("::");
		f.choosenUrl.value			= this.trim(sections[2]);
	}
}

function __lib_menulink_updateChoosenMenu(){
	this.set_retrieve_button();
}
function __lib_menulink_getPages(){
	var f = get_form();
	tmp = cache_data.document.frmDoc.page_data.value;
	if("data:"+tmp=="data:"){
		setTimeout(this.name+".getPages();",2000);
	} else {
		listing = tmp.split("|1234567890|");
		doc = document.getElementById("retrievePages");
		HTMLContent   = "<label for='choosenPage' style='width:125px;'>Choose page</label><select name='choosenPage' style='width:250px'>";
		for(var i=0;i<listing.length;i+=2){
			HTMLContent	  += "<option value='" + listing[i+1]  + "'>" + listing[i] + "</option>\n";
			// "+ listing[i+1]  +"
		}
		HTMLContent  += "</select><br/>";
		HTMLContent  += "<input type='button' value='Cancel' class='bt' onclick='menulinks.set_retrieve_button();'/>";
		HTMLContent  += "<input type='button' value='Select Page' class='bt' onclick='menulinks.setUrl();'/>";
		doc.innerHTML = HTMLContent;
		f.choosenMenu.disabled= false;
	}
}

function __lib_menulink_retrievePages(){
	var f = get_form();
	f.choosenMenu.disabled= true;
	doc = document.getElementById("retrievePages");
	HTMLContent   = "<label for='choosenPage' style='width:125px;'>Choose page</label><select name='choosenPage' style='width:250px'>";
	HTMLContent	 += "<option>Downloading list of pages for choosen location.</option>";
	HTMLContent  += "</select>";
	doc.innerHTML = HTMLContent;
//	this.exec_info("menu", parameter+'owner='+this.owner+'&module='+this.module+'&block='+document.getElementById("filter_builder_blockinfo").value.split("'").join("\'").split("\r\n").join(":-:"));
	this.exec_info('page',"menu_identifier="+f.choosenMenu.options[f.choosenMenu.selectedIndex].value);
	setTimeout(this.name+".getPages();",2000);
}

function __lib_menulink_set_retrievebutton(){
	doc = document.getElementById("retrievePages");
	try{
	if (cache_data.document.readyState != 'complete'){
		cache_data.document.frmDoc.page_data.value="";
	}
	} catch(e){}
	var f = get_form();
	field = f.choosenMenu;
	show_buttons=1;
	if (field+""=="undefined"){
		show_buttons=0;
	}else {
		if (f.choosenMenu.selectedIndex==0){
			show_buttons=0;
		}
	}
	if (show_buttons==1){
		HTMLContent  = "<input type='button' value='Retrieve Pages' class='bt' onclick='menulinks.retrievePages();'/><input type='button' value='Select menu' class='bt' onclick='menulinks.setUrl();'/>";
	} else {
		HTMLContent  = "";
	}
	doc.innerHTML = HTMLContent;
}

function __lib_menulink_getMenu(){
		this.exec_info('menu', '');
		setTimeout(this.name+".set_menu();",1000);
}
function __lib_menulink_set_menu(){
	var f = get_form();
	tmp = cache_data.document.frmDoc.menu_data.value;
	if("data:"+this.trim(tmp)=="data:"){
		setTimeout(this.name+".set_menu();",2000);
	} else {
		HTMLContent  = "<p><label for='choosenMenu' style='width:125px' >Choose menu location</label><select name='choosenMenu' style='width:250px' onchange='javascript:extract_url(this.document)'><option value='-1'>Select a menu location</option></select></p>";
		out = document.getElementById("retrieveMenu");
		out.innerHTML = HTMLContent;
		var prev_style="",identifier=-1 ,parent_identifier=-1;
		var mystyle ="";
		tmp = tmp.split("&amp;").join("&").split("&#39;").join("'");
		myArray 		= tmp.split("|1234567890|");
		len				= myArray.length-1;
		list			= "";
		f.choosenMenu.options.length=0
		f.choosenMenu.options[0] = new Option("--- Select a menu location ---");
		f.choosenMenu.options[0].style.color="#993399";
		for (var i = 0; i<len; i+=2){
			var split_list = myArray[i+1].split("::");
			
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
			label_str = myArray[i].split("&#39;").join("'").split("&#160;").join(" ");
			f.choosenMenu.options[f.choosenMenu.options.length] = new Option(label_str, myArray[i+1], "");
		}
		f.choosenMenu.disabled=false;
	}
	
	doc = document.getElementById("submitbutton");
	HTMLContent  = "<p>";
	HTMLContent  += "<input type='button' id='addnew' value='Add' class='bt' onclick='menulinks.add_new();' />";
	HTMLContent  += "<input type='button' id='Cancel' value='Cancel' class='bt' onclick='menulinks.cancel();' style='display:none'/>";
	HTMLContent  += "<input type='button' id='editexisting' value='Update' class='bt' onclick='menulinks.update();' style='display:none' />";
	HTMLContent  += "</p>";
	doc.innerHTML = HTMLContent;
}
function __lib_menulink_draw(){
	var f = get_form();
	if(this.firstTime==1){
		this.firstTime=0;
		HTMLContent  = "<p>Currently retrieving and processing menu structure please wait..</p>";
		out = document.getElementById("retrieveMenu");
		out.innerHTML = HTMLContent;
		this.getMenu();
		doc = document.getElementById("submitbutton");
		HTMLContent  = "<p>";
		HTMLContent  += "<input type='button' id='addnew' value='Add' class='bt' onclick='menulinks.add_new();' />";
		HTMLContent  += "<input type='button' id='Cancel' value='Cancel' class='bt' onclick='menulinks.cancel();' style='display:none'/>";
		HTMLContent  += "<input type='button' id='editexisting' value='Update' class='bt' onclick='menulinks.update();' style='display:none' />";
		HTMLContent  += "</p>";
		doc.innerHTML = HTMLContent;
	}
	this.set_retrieve_button();
	var tab	= document.getElementById("resultTable");
	var n = tab.rows.length;
	for(var i =1; i < n ;i++){
		tab.deleteRow(1);
	}
	for(var i =0; i < this.links.length ;i++){
		var newRow										= tab.insertRow();
		var newCell										= this.dhtml.createTag({'tag':'td',"id":"title_"+this.links[i]["id"], "width":"25%","innerHTML":this.links[i]["title"]});
		newRow.appendChild(newCell);
		var newCell										= this.dhtml.createTag({'tag':'td',"id":"label_"+this.links[i]["id"], "width":"25%","innerHTML":this.links[i]["label"]});
		newRow.appendChild(newCell);
		var newCell										= this.dhtml.createTag({'tag':'td',"id":"url_"+this.links[i]["id"], "width":"25%","innerHTML":this.links[i]["url"]});
		newRow.appendChild(newCell);
		//set the last row of buttons 
		if (i==0){
			myup=0;
		} else {
			myup=1;
		}
		if (this.links.length==(i+1)){
			mydown=0;
		} else {
			mydown=1;
		}
		HTMLContent = this.get_buttons(i, myup, mydown, this.links[i]["id"]);
		var newcell = this.dhtml.createTag({"id":"options_"+this.links[i].id,'tag':'td',"width":"25%","innerHTML":HTMLContent});
		newRow.appendChild(newcell);
	}		
	this.displayblock();
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_menulink_get_buttons(
-	usage	:	object.getbuttons(index [int], up [boolean], down [boolean]);
-	returns	:	String (html for buttons)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	display s four buttons remove, edit up and down
-	sets index;
-	
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_menulink_get_buttons(index, up, down, myid){
var HTMLContent = "<input type='button' class='bt' style='width:65px;text-align:center' onclick='javascript:"+this.name+".remove("+index+",\""+myid+"\");\' value='Remove'>";
	HTMLContent += "<input type='button' class='bt' style='width:55px;text-align:center' onclick='javascript:"+this.name+".edit(\""+myid+"\");' value='Edit'>";
	if(index==0){
		HTMLContent += "<input type='button' disabled='true' class='bt' style='display:inline;width:55px;text-align:center;color:#999999' value=\"Up\">";
	} else {
		HTMLContent += "<input type='button' class='bt' style='width:55px;text-align:center' onclick='javascript:"+this.name+".up("+index+");' value=\"Up\">";
	}
	if(down==0){
		HTMLContent += "<input type='button' disabled='true' class='bt' style='display:inline;width:55px;text-align:center;color:#999999' value=\"Down\">";
	} else {
		HTMLContent += "<input type='button' class='bt' style='width:55px;text-align:center;' onclick='javascript:"+this.name+".down("+index+");' value=\"Down\">";
	}
	return HTMLContent;
	
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- trim (String)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_menulink_trim(tmp){
	tmp = new String(tmp);
	var ReturnString ="";
	var ok=0;
	for(var index = 0 ; index < tmp.length ; index++){
		c = tmp.charAt(index);
		if(ok==0){
			if(c==" " || c=="-"){
				
			} else {
				ReturnString += c;
				ok=1;
			}
		} else {
			ReturnString += c;
		}
	}
	return ReturnString;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Add_new()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_menulink_add_new(){
	try{
		var f = get_form();
//		var cIndex	= f.choosenIndex.value;
		var cLabel	= "";
		var cLabel = f.choosenLabel.value;
		var cTitle	= f.choosenTitleLabel.value;
		var cUrl	= f.choosenUrl.value;
		if (cLabel=="" || cTitle=="" || cUrl==""){
			alert("You have not filled in all of the fields");
		}else {
			f.choosenLabel.value ="";
			f.choosenTitleLabel.value ="";
			f.choosenUrl.value ="";
			var i 											= this.links.length;
			this.links[i]									= new Array();
			this.links[i]["id"]								= "entry"+(this.idIndex++);
			this.links[i]["title"]							= cTitle;
			this.links[i]["label"]							= cLabel;
			this.links[i]["url"]							= cUrl;
			this.draw();
		}
		ok=true;
	} catch (e){
		ok = false;
	}
//	this.build_blockInfo();
	return ok;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_menulink_remove
-	usage	:	object.remove(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	removes entry from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __lib_menulink_remove(index,id){
	var tab		= document.getElementById("resultTable");
	tab.deleteRow(index+1);
	sindex = -1;
	for (i=0;i<this.links.length;i++){
		if (this.links[i]["id"] == id ){
			sindex = i;
		}
	}
	if(sindex>=0){
		this.links.splice(sindex,1);
		for (i=0;i<this.links.length;i++){
			tableCell = document.getElementById("options_"+this.links[i].id);
			tableCell.innerHTML = this.get_buttons(i,1,1, this.links[i].id);
		}
	//	this.build_blockInfo();
		this.draw();
	}
	return false;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_menulink_up
-	usage	:	object.up(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	changes the rank order of selected entries from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __lib_menulink_up(index){
	var tmp	= {};
	if (index>0){
		// store info temporarly
		tmp.id											= this.links[index]["id"];
		tmp.label	 									= this.links[index]["label"];
		tmp.title 										= this.links[index]["title"];
		tmp.url											= this.links[index]["url"];
		//overwrite first 
		this.links[index]["id"] 						= this.links[index-1].id;
		this.links[index]["label"] 						= this.links[index-1].label;
		this.links[index]["title"] 						= this.links[index-1].title;
		this.links[index]["url"]						= this.links[index-1].url;
		// replace second
		this.links[index-1]["id"] 						= tmp.id;
		this.links[index-1]["label"] 					= tmp.label;
		this.links[index-1]["title"] 					= tmp.title;
		this.links[index-1]["url"]						= tmp.url;
		this.draw();
		
	}
	return false;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_menulink_down
-	usage	:	object.down(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	removes entry from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __lib_menulink_down(index){
	var tmp	= {};
	if (index < this.links.length){
		// store info temporarly
		tmp.id											= this.links[index]["id"];
		tmp.label	 									= this.links[index]["label"];
		tmp.title 										= this.links[index]["title"];
		tmp.url											= this.links[index]["url"];
		//overwrite first 
		this.links[index]["id"] 						= this.links[index+1].id;
		this.links[index]["label"] 						= this.links[index+1].label;
		this.links[index]["title"] 						= this.links[index+1].title;
		this.links[index]["url"]						= this.links[index+1].url;
		// replace second
		this.links[index+1]["id"] 						= tmp.id;
		this.links[index+1]["label"] 					= tmp.label;
		this.links[index+1]["title"] 					= tmp.title;
		this.links[index+1]["url"]						= tmp.url;
		this.draw();
	}
	return false;
}

function __lib_menulink_extract_information(szType, parameter){
	/*
		check to see if the cache is available for information yet? 
	*/
	if (cache_data.document.readyState != 'complete'){
		setTimeout(this.name+".exec_info('"+szType+"', '"+parameter+"');",1000);
		return;
	} else {
		if (szType=='page' && cache_data.frmDoc.page_data.value==''){
			cache_data.frmDoc.page_data.value="";
			cache_data.__extract_info('page',parameter);
		}
		if (szType=='menu' && cache_data.frmDoc.menu_data.value==''){
			cache_data.frmDoc.menu_data.value="";
			cache_data.__extract_info('menu',parameter);
		}
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_filterBuild_updateCondition
-	usage	:	object.updateCondition();
-	returns	:	void
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	update the conditional join setting for this condition joining with the next condition
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_menulink_edit(identifier){
	var f = get_form();
	sindex=0;
	element = document.getElementById("editexisting");
	element.style.display="inline";
	element = document.getElementById("cancel");
	element.style.display="inline";
	element = document.getElementById("addnew");
	element.style.display="none";
	for (i=0;i<this.links.length;i++){
		if (this.links[i]["id"] == identifier ){
			sindex = i;
		}
	}
	this.editIndex = sindex;
	f.choosenLabel.value		= this.links[sindex]["label"];
	f.choosenTitleLabel.value	= this.links[sindex]["title"];
	f.choosenUrl.value	 		= this.links[sindex]["url"];


//	this.filterUpdate(this.links[sindex].value);
}


function __lib_menulink_cancel(){
	var f = get_form();
	element = document.getElementById("editexisting");
	element.style.display="none";
	element = document.getElementById("cancel");
	element.style.display="none";
	element = document.getElementById("addnew");
	element.style.display="";
	f.choosenLabel.value ="";
	f.choosenTitleLabel.value ="";
	f.choosenUrl.value ="";
	this.editIndex			= -1;
}
function __lib_menulink_update(){
	var f = get_form();
	element = document.getElementById("editexisting");
	element.style.display="none";
	element = document.getElementById("cancel");
	element.style.display="none";
	element = document.getElementById("addnew");
	element.style.display="";
	var cLabel	= f.choosenLabel.value;
	var cTitle	= f.choosenTitleLabel.value;
	var cUrl	= f.choosenUrl.value;

		f.choosenLabel.value ="";
		f.choosenTitleLabel.value ="";
		f.choosenUrl.value ="";

	try{
		var i 											= this.editIndex;
		this.links[i]									= {};
		this.links[i]["label"]							= cLabel;
		this.links[i]["title"]							= cTitle;
		this.links[i]["url"]							= cUrl;
	} catch(e){
		alert("Sorry there was an Error saving your data");
	}
	this.editIndex			= -1;
	this.draw();

}

function __lib_menulink_displayblock(){
	var f = get_form();
	var len = this.links.length;
	var standardOutput = "";
	for(var i =0; i<len;i++){
		if(i>0){
			standardOutput += ":0987654321:"; // reversed for new row
		}
		standardOutput += this.links[i]["label"]+":1234567890:"
		standardOutput += this.links[i]["title"]+":1234567890:"
		standardOutput += this.links[i]["url"]
	}
	f.linkblock.value = standardOutput;
}