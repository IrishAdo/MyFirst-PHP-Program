/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Information Directory Feature List Javascript file.
-	author : Adrian Sweeney
-	Creation Date: 13 August 2004
-	Modified $Date: 2005/03/01 19:26:15 $
-	$Revision: 1.7 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function Libertas_filter_builder(){
	// Properties
	this.list				= new Array(); 							// Filter defintion
	this.idIndex			= 0;									// 
	this.dhtml		 		= new DhtmlScript();					// include html generation code builder 
																	// requires file DHTMLSCRIPT.JS 
	this.module				= "";
	this.order_field_dir	= 0;
	this.order_field		= "";
	this.name				= "builder";
	this.owner				= -1;
	this.editIndex			= -1;
	this.outputDiv			= null;									// location to publish filter builder to
	this.resultDiv			= null;									// location to publish defined filter results to 
	this.queryDiv			= null;									// 
	this.filteroptions		= new Array();							// filter option lists
	this.fieldlist			= new Array();							// field lists
	this.matchlist			= new Array();							// match condition lists
	this.extratags			= new Array();

	// methods
	this.draw				= __lib_filterBuild_draw;				// draw the content to the screen
	this.add				= __lib_filterBuild_add;				// add an entry on load to the selectedArray property
	this.add_edit			= __lib_filterBuild_edit_save;			// after an update submit the save back to the system
	this.cancel				= __lib_filterBuild_cancel;				// cancel and edit back to the add screen;
	this.add_new			= __lib_filterBuild_add_new;			// add new entry from the website tool
	this.updateCondition	= __lib_filterBuild_updateCondition;	// add new entry from the website tool
	this.remove				= __lib_filterBuild_remove;				// remove
	this.edit				= __lib_filterBuild_edit;				// add an entry on load to the selectedArray property
	this.up					= __lib_filterBuild_up;					// up
	this.down				= __lib_filterBuild_down;				// down
	this.filterUpdate		= __lib_filterBuild_update;				// update the filter builder enable the Add button and display the 
	this.get_buttons		= __lib_get_buttons;					// get buttons for display
	this.build_blockInfo	= __lib_build_blockInfo;
	this.fillinQuery		= __lib_fillinQuery;					// call this function and pass the desired parameters will produce the query section of the page
	this.test				= __lib_test;							// call the Engine and get the number of results and the top 10 results.
	this.exec_info			= __extract_information;				// 
	this.getQuery			= __lib_getQuery;
	this.change_order		= __update_order_field_dir;				// change the order of the ascending / descending 
} 

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_filterBuild_draw
-	usage	:	object.draw(void);
-	returns	:	true on success
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	Draws the outputlist to the screen to the display position this.outputDiv
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_filterBuild_draw(){
	var HTMLContent = "";
	try{
		if (this.outputDiv==""){
			var ok=false
		} else {
			HTMLContent += "<input type=hidden value='' name='filter_builder_blockinfo' id='filter_builder_blockinfo'/>";
			HTMLContent += "<div class='row'><div>Order results by what field</div><div><select name='choosen_order_field'><option value=''>No ordering on results</option>";
			for(var i = 0 ; i< this.fieldlist.length ; i++){
				if(this.fieldlist[i][3]!=""){
					HTMLContent += "<option value='"+this.fieldlist[i][3]+"'";
					if(this.fieldlist[i][3]==this.order_field){
						HTMLContent += " selected='true'";
					}
					HTMLContent += ">"+this.fieldlist[i][1]+"</option>";
				}
			}
			HTMLContent += "</select></div></div>";
			HTMLContent += "<div class='row'><div>Rank results in </div><div><input type='radio' name='rank_order' value='0'";
			if(0==this.order_field_dir){
				HTMLContent += " checked='true'";
			}
			HTMLContent += " id='order1' onchange='javascript:builder.change_order(this);'><label for='order1'>Ascending</label><br><input type='radio' name='rank_order' value='1'";
			if(1==this.order_field_dir){
				HTMLContent += " checked='true'";
			}
			HTMLContent += " id='order2' onchange='javascript:builder.change_order(this);'><label for='order2'>Descending</label></div></div>";
			HTMLContent += "<div class='row'><div style='display:inline;width:150px;' class='bt'>Field</div><div style='display:inline;width:150px;' class='bt'>Condition</div><div style='display:inline;width:190px;' class='bt'>Value</div><div style='display:inline;width:240px;' class='bt'></div></div>";
			HTMLContent += "<div class='row'>";
			HTMLContent += "<div style='display:inline;width:150px;border:1px solid white;'><select name='f_field' id='f_field' style='width:150px' onchange='javascript:builder.filterUpdate(\"\");'>";
			for (var i = 0 ; i< this.fieldlist.length ; i++){
				HTMLContent += "<option value='"+this.fieldlist[i][0]+"'>"+this.fieldlist[i][1]+"</option>";
			}
			HTMLContent += "</select></div>";
			HTMLContent += "<div style='display:inline;width:150px;border:1px solid white;'><select name='f_match' id='f_match' style='width:150px'>";
			for (var i = 0 ; i< this.matchlist.length ; i++){
				HTMLContent += "<option value='"+this.matchlist[i][0]+"'>"+this.matchlist[i][1]+"</option>";
			}
			HTMLContent += "</select></div>";
			HTMLContent += "<div style='display:inline;width:150px;border:1px solid white;' id='selectiontypediv'></div>";
			HTMLContent += "<div style='display:inline;width:180px;border:1px solid white' >";
			HTMLContent += "<input style='width:60px;' class='bt' type=button value='Add' id='addnew' onclick='javascript:builder.add_new();return false;'/>";
			HTMLContent += "<input style='display:none;width:70px;' class='bt' type=button value='Update' id='editexisting' onclick='javascript:builder.add_edit();return false;'/>";
			HTMLContent += "<input style='display:none;width:60px;' class='bt' type=button value='Cancel' id='cancel' onclick='javascript:builder.cancel();return false;'/>";
			HTMLContent += "</div>";
			HTMLContent += "</div>";
			myDiv = document.getElementById(this.outputDiv);
			myDiv.innerHTML = HTMLContent;
			
			HTMLContent="<table id='resultTable' border='0'>"
			for(var i =0; i < this.list.length ;i++){
				myidentifier =this.list[i].id;
				HTMLContent += "<tr id='"+myidentifier+"'>";
				HTMLContent += "<td id='content"+myidentifier+"' style='width:430px;'>";
				HTMLContent += this.list[i].title+" "+this.list[i].matchstringText+" "+this.list[i].value+" ";
				HTMLContent += "</td>";
				HTMLContent += "<td id='condition"+myidentifier+"' style='width:60px;'><select name='condition_"+i+"' onchange='javascript:builder.updateCondition(\""+myidentifier+"\",this);return false;'>";
				HTMLContent += "<option value='and'";
				if (this.list[i].conditionaljoin=="0"){
					HTMLContent += " selected='true'";
				}
				HTMLContent += ">And</option>";
				HTMLContent += "<option value='or'";
				if (this.list[i].conditionaljoin=="1"){
					HTMLContent += " selected='true'";
				}
				HTMLContent += ">Or</option>";
				HTMLContent += "</select>";
//				HTMLContent += this.list[i].title+" "+this.list[i].matchstring+" ";
				HTMLContent += "</td>";
				HTMLContent += "<td id='options_"+myidentifier+"' style='width:240px;'>";
				if (i==0){
					if (this.list.length ==1){
						HTMLContent += this.get_buttons(i,0,0, myidentifier);
					} else {
						HTMLContent += this.get_buttons(i,0,1, myidentifier);
					}
				} else {
					if (this.list.length-1 == i){
						HTMLContent += this.get_buttons(i,1,0, myidentifier);
					} else {
						HTMLContent += this.get_buttons(i,1,1, myidentifier);
					}
				}
				HTMLContent += "</td>";
				HTMLContent += "</tr>";
			}
			HTMLContent += "</table>";
			
			myDiv = document.getElementById(this.resultDiv);
			myDiv.innerHTML = HTMLContent;
			
			
			myDiv = document.getElementById(this.queryDiv);
			if (myDiv.innerHTML ==""){
				this.fillinQuery();
			}
			this.filterUpdate();
			this.build_blockInfo();
			var ok=true;
		}
	} catch (e){
		var ok = false;
	}
	if(!ok){
//		alert("There was a problem with the displaying of this query");
	}
	return false;
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_filterBuild_add
-	usage	:	object.add(id [int], title [string]);
-	returns	:	boolean (true on successfull addition)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	functionality for system to supply through a list of conditional statements that will make up the parsing sql 
-	statement.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_filterBuild_add(field_name, matchstring, joincondition, value){
	try{
		i = this.list.length;
		this.list[i]									= {};
		this.list[i].id 								= "entry"+(this.idIndex++);
		this.list[i].field 								= field_name;
		title="";
		for (var index = 0 ; index< this.fieldlist.length ; index++){
			if(this.fieldlist[index][0]==field_name){
				title = this.fieldlist[index][1];
			}
		}
		this.list[i].title 								= title;
		this.list[i].matchstring						= matchstring;
		this.list[i].matchstringText					= this.matchlist[matchstring][1];
		this.list[i].conditionaljoin					= joincondition;
		this.list[i].value								= value;
		ok = true;
	} catch (e){
		ok = false;
	}
	return ok;
}
 

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_filterBuild_add_new
-	usage	:	object.add_new(void);
-	returns	:	boolean (true on successfull addition)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	Draws the selection form on the screen to the display position this.selectorDiv
-	Draws the outputlist to the screen to the display position this.outputDiv
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_filterBuild_add_new(){
	try{
		var element1 									= document.getElementById("f_field");
		var element2 									= document.getElementById("f_match");
		var element3 									= document.getElementById("f_text_name");
		var element4 									= document.getElementById("f_text_select");
		var i 											= this.list.length;
		this.list[i]									= {};
		this.list[i].id 								= "entry"+(this.idIndex++);
		this.list[i].field 								= element1.options[element1.selectedIndex].value;
		this.list[i].title 								= element1.options[element1.selectedIndex].text;
		this.list[i].matchstring						= element2.options[element2.selectedIndex].value;
		this.list[i].matchstringText					= element2.options[element2.selectedIndex].text;
		this.list[i].conditionaljoin					= 0;
		if (element3+""!="undefined" && element3+""!="null"){
			this.list[i].value							= element3.value;
			element3.value								= '';
		} else {
			this.list[i].value							= element4.options[element4.selectedIndex].value;
			element4.options[element4.selectedIndex].selected	= false;
			element4.selectedIndex						= 0;
		}
		element1.selectedIndex=0;
		element2.selectedIndex=0;
		var tab											= document.getElementById("resultTable");
		var newRow										= tab.insertRow();
		var newCell										= this.dhtml.createTag({'tag':'td',"id":"content"+this.list[i].id, "width":"430px","innerHTML":""+this.list[i].title+" "+this.list[i].matchstringText+" "+this.list[i].value+" "});
		newRow.appendChild(newCell);
//		var newCell = 
		myoptions 						=	new Array();
		myoptions[0] 					=	new Array();
		myoptions[0]["label"]			=	"And"
		myoptions[0]["value"]			=	"and";
		if (this.list[i].conditionaljoin==	"0"){
			myoptions[0]["selected"]	=	"true";
		}
		myoptions[1] 					=	new Array();
		myoptions[1]["label"]			=	"Or";
		myoptions[1]["value"]			=	"or";
		if (this.list[i].conditionaljoin==	"1"){
			myoptions[1]["selected"]	=	"true";
		}
		var newcell = null;
		var newcell = this.dhtml.createTag({'tag':'td',"id":"condition"+this.list[i].id,"width":"60px"});
		newcell.appendChild(this.dhtml.createTag(
			{
				'tag':'select',
				"name":"condition_"+this.list[i].id, 
				"onchange":"javascript:builder.updateCondition(\""+this.list[i].id+"\",this);return false;", 
				"myoptions":myoptions
			}
		));
		newRow.appendChild(newcell);
		var newcell = null;
		//set the last row of buttons 
		HTMLContent = this.get_buttons(i,1,0, this.list[i].id)
		
		var newcell = this.dhtml.createTag({"id":"options_"+this.list[i].id,'tag':'td',"width":"240px","innerHTML":HTMLContent});
		newRow.appendChild(newcell);
		//if not on row 0 then modify second last row of buttons
		if (i!=0){
			mytableCell = document.getElementById("options_"+this.list[i-1].id);
			mytableCell.innerHTML = this.get_buttons(i-1, 1, 1, this.list[i-1].id);
		}
		ok=true;
	} catch (e){
		ok = false;
	}
	this.build_blockInfo();
	return ok;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_get_buttons(
-	usage	:	object.getbuttons(index [int], up [boolean], down [boolean]);
-	returns	:	String (html for buttons)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	display s four buttons remove, edit up and down
-	sets index;
-	
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_get_buttons(index, up, down, myid){
var HTMLContent = "<input type='button' class='bt' style='width:60px;text-align:center' onclick='javascript:builder.remove("+index+",\""+myid+"\");\' value='Remove'>";
	HTMLContent += "<input type='button' class='bt' style='width:55px;text-align:center' onclick='javascript:builder.edit(\""+myid+"\");' value='Edit'>";
	if(index==0){
		HTMLContent += "<input type='button' disabled='true' class='bt' style='display:inline;width:55px;text-align:center;color:#999999' value=\"Up\">";
	} else {
		HTMLContent += "<input type='button' class='bt' style='width:55px;text-align:center' onclick='javascript:builder.up("+index+");' value=\"Up\">";
	}
	if(down==0){
		HTMLContent += "<input type='button' disabled='true' class='bt' style='display:inline;width:55px;text-align:center;color:#999999' value=\"Down\">";
	} else {
		HTMLContent += "<input type='button' class='bt' style='width:55px;text-align:center;' onclick='javascript:builder.down("+index+");' value=\"Down\">";
	}
	return HTMLContent;
	
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_filterBuild_remove
-	usage	:	object.remove(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	removes entry from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __lib_filterBuild_remove(index,id){
	var tab		= document.getElementById("resultTable");
	tab.deleteRow(index);
	sindex = 0;
	for (i=0;i<this.list.length;i++){
		if (this.list[i].id == id ){
			sindex = i;
		}
	}
	this.list.splice(sindex,1);
	for (i=0;i<this.list.length;i++){
		tableCell = document.getElementById("options_"+this.list[i].id);
		tableCell.innerHTML = this.get_buttons(i,1,1, this.list[i].id);
	}
	this.build_blockInfo();
	//this.draw();
	return false;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_filterBuild_up
-	usage	:	object.up(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	changes the rank order of selected entries from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __lib_filterBuild_up(index){
	var tmp	= {};
	if (index>0){
		first	= this.list[index].id+"";
		second	= this.list[index-1].id+"";
		// store info temporarly
		tmp.id											= this.list[index].id
		tmp.field	 									= this.list[index].field;
		tmp.title 										= this.list[index].title;
		tmp.matchstring									= this.list[index].matchstring;
		tmp.matchstringText								= this.list[index].matchstringText;
		tmp.conditionaljoin								= this.list[index].conditionaljoin;
		tmp.value										= this.list[index].value;
		//overwrite first 
		this.list[index].id 							= this.list[index-1].id;
		this.list[index].field 							= this.list[index-1].field;
		this.list[index].title 							= this.list[index-1].title;
		this.list[index].matchstring					= this.list[index-1].matchstring;
		this.list[index].matchstringText				= this.list[index-1].matchstringText;
		this.list[index].conditionaljoin				= this.list[index-1].conditionaljoin;
		this.list[index].value							= this.list[index-1].value;
		// replace second
		this.list[index-1].id 							= tmp.id;
		this.list[index-1].field 						= tmp.field;
		this.list[index-1].title 						= tmp.title;
		this.list[index-1].matchstring					= tmp.matchstring;
		this.list[index-1].matchstringText				= tmp.matchstringText;
		this.list[index-1].conditionaljoin				= tmp.conditionaljoin;
		this.list[index-1].value						= tmp.value;
		this.draw();
		
	}
	return false;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_filterBuild_down
-	usage	:	object.down(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	removes entry from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __lib_filterBuild_down(index){
	var tmp	= {};
	if (index < this.list.length){
		// store info temporarly
		tmp.id											= this.list[index].id
		tmp.field	 									= this.list[index].field;
		tmp.title 										= this.list[index].title;
		tmp.matchstring									= this.list[index].matchstring;
		tmp.matchstringText								= this.list[index].matchstringText;
		tmp.conditionaljoin								= this.list[index].conditionaljoin;
		tmp.value										= this.list[index].value;
		//overwrite first 
		this.list[index].id 							= this.list[index+1].id;
		this.list[index].field 							= this.list[index+1].field;
		this.list[index].title 							= this.list[index+1].title;
		this.list[index].matchstring					= this.list[index+1].matchstring;
		this.list[index].matchstringText				= this.list[index+1].matchstringText;
		this.list[index].conditionaljoin				= this.list[index+1].conditionaljoin;
		this.list[index].value							= this.list[index+1].value;
		// replace second
		this.list[index+1].id 							= tmp.id;
		this.list[index+1].field 						= tmp.field;
		this.list[index+1].title 						= tmp.title;
		this.list[index+1].matchstring					= tmp.matchstring;
		this.list[index+1].matchstringText				= tmp.matchstringText;
		this.list[index+1].conditionaljoin				= tmp.conditionaljoin;
		this.list[index+1].value						= tmp.value;
		this.draw();
	}
	return false;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_filterBuild_update
-	usage	:	object.filterUpdate();
-	returns	:	void
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	check to see what the field type is and display appropraite value box.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __lib_filterBuild_update(set_field_value){
	var output = document.getElementById("selectiontypediv");
	var out = "";
	if (set_field_value+""=="undefined" || set_field_value+""=="null"){
		set_field_value ="";
	}
	f = document.getElementById("f_field");
	fieldname = f.options[f.selectedIndex].value;
	for (var z=0; z<this.fieldlist.length;z++){
		if (this.fieldlist[z][0] == fieldname){
			if (this.fieldlist[z][2] == "text"){
				out += "<input type=text name='f_text_name' value='"+new String(set_field_value).split("%20").join(" ")+"' style='width:190px;'/>";
			} else {
				out += "<select name='f_text_select' style='width:190px;'>";
				if (this.filteroptions[fieldname]){
					for (var j=0; j < this.filteroptions[fieldname].length; j++){
						out += "<option value='"+new String(this.filteroptions[fieldname][j]).split("%20").join(" ")+"'";
						if (set_field_value == new String(this.filteroptions[fieldname][j]).split("%20").join(" ")){
							out += " selected='true' ";
						}
						out += ">"+new String(this.filteroptions[fieldname][j]).split("%20").join(" ")+"</option>";
					}
				}
				out += "</select>";
			}
		}
	}
	output.innerHTML = out;
	
	
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
function __lib_filterBuild_updateCondition(id, sender){
	sindex=-1;
	for (i=0;i<this.list.length;i++){
		if (this.list[i].id == id ){
			sindex = i;
		}
	}
	this.list[sindex].conditionaljoin = sender.selectedIndex;//sender.options[].value;
	this.build_blockInfo();
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
function __lib_filterBuild_edit(identifier){
	sindex=0;
	element = document.getElementById("editexisting");
	element.style.display="inline";
	element = document.getElementById("cancel");
	element.style.display="inline";
	element = document.getElementById("addnew");
	element.style.display="none";
	for (i=0;i<this.list.length;i++){
		if (this.list[i].id == identifier ){
			sindex = i;
		}
	}
	this.editIndex = sindex;
	var d_field =	document.getElementById("f_field");
	for(var i=0; i<d_field.options.length; i++){
		if (d_field.options[i].value == this.list[sindex].field){
			d_field.options[i].selected=true;
		}
	}
	var d_match =	document.getElementById("f_match");
	for(var i=0; i<d_match.options.length; i++){
		if (d_match.options[i].value == this.list[sindex].matchstring){
			d_match.options[i].selected=true;
		}
	}
	this.filterUpdate(this.list[sindex].value);
}
/*
		this.list[i].field 								= element1.options[element1.selectedIndex].value;
		this.list[i].title 								= element1.options[element1.selectedIndex].text;
		this.list[i].matchstring						= element2.options[element2.selectedIndex].value;
		this.list[i].matchstringText					= element2.options[element2.selectedIndex].text;
		this.list[i].conditionaljoin					= 0;
		if (element3+""!="undefined" && element3+""!="null"){
			this.list[i].value							= element3.value;
		} else {
			this.list[i].value							= element4.options[element4.selectedIndex].value;
*/

function __lib_filterBuild_edit_save(){
	element = document.getElementById("editexisting");
	element.style.display="none";
	element = document.getElementById("cancel");
	element.style.display="none";
	element = document.getElementById("addnew");
	element.style.display="";
	try{
		var element1 									= document.getElementById("f_field");
		var element2 									= document.getElementById("f_match");
		var element3 									= document.getElementById("f_text_name");
		var element4 									= document.getElementById("f_text_select");
		var i 											= this.editIndex;
		this.list[i]									= {};
		this.list[i].id 								= "entry"+(this.idIndex++);
		this.list[i].field 								= element1.options[element1.selectedIndex].value;
		this.list[i].title 								= element1.options[element1.selectedIndex].text;
		this.list[i].matchstring						= element2.options[element2.selectedIndex].value;
		this.list[i].matchstringText					= element2.options[element2.selectedIndex].text;
		this.list[i].conditionaljoin					= 0;
		if (element3+""!="undefined" && element3+""!="null"){
			this.list[i].value							= element3.value;
		} else {
			this.list[i].value							= element4.options[element4.selectedIndex].value;
		}
	} catch(e){
		alert("Sorry there was an Error saving your data");
	}
	this.editIndex			= -1;
	this.draw();
}
function __lib_filterBuild_cancel(){
	element = document.getElementById("editexisting");
	element.style.display="none";
	element = document.getElementById("cancel");
	element.style.display="none";
	element = document.getElementById("addnew");
	element.style.display="";
	var element1 									= document.getElementById("f_field");
	var element2 									= document.getElementById("f_match");
	var element3 									= document.getElementById("f_text_name");
	var element4 									= document.getElementById("f_text_select");
	if (element3+""!="undefined" && element3+""!="null"){
		element3.value								= '';
	} else {
		element4.options[element4.selectedIndex].selected	= false;
		element4.selectedIndex						= 0;
	}
	element1.selectedIndex=0;
	element2.selectedIndex=0;
	this.editIndex			= -1;
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_filterBuild_updateCondition
-	usage	:	object.updateCondition();
-	returns	:	void
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	build the block information that will be used by the system to add user defined filters to sections of the
-	screen.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_build_blockInfo(){
	// 
	var sz = "";
	for (var i=0;i<this.list.length;i++){
		sz+=this.list[i].id+":::"+this.list[i].field+":::"+this.list[i].matchstring+":::"+this.list[i].conditionaljoin+":::"+this.list[i].value+"\n";
	}
	document.getElementById("filter_builder_blockinfo").value = sz;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_fillinQuery
-	usage	:	object.fillinQuery();
-	returns	:	void
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	calling this function to produce the query section of the page
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_fillinQuery(str, numberRows){
	if((str+""=="undefined") || (str+""=="null")){
		str="";
	}
	if((numberRows+""=="undefined") || (numberRows+""=="null")){
		numberRows=-1;
	}
	var myDiv = document.getElementById(this.queryDiv);
	var sz  = "<div style='width:650px;text-align:right;'><input type='button' onclick='builder.test();' value='Test your query' class='bt' style='width:150px;'/></div>";
		sz += "<div id='printResults'><strong>";
		if (numberRows>-1){
			if (numberRows>0){
				sz += "Your query returned "+numberRows+" row(s) and here is a small sample of the records returned by your query";
			}else{
				sz += "There were no records found with your query";
			}
		}
		sz += "</strong><br/><ul>"+str+"</ul></div>";
	myDiv.innerHTML = sz;
	cache_data = document.getElementById("cache_data");
} 

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__lib_fillinQuery
-	usage	:	object.fillinQuery();
-	returns	:	void
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	calling this function to produce the query section of the page
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __lib_test(){
	if(cache_data+""!="null"){
		var parameter="";
		for(key in this.extratags){
			parameter += key+"="+escape(this.extratags[key])+"&";
		}
		//alert(parameter+'owner='+this.owner+'&amp;module='+this.module+'&amp;block='+document.getElementById("filter_builder_blockinfo").value.split("'").join("\'").split("\n").join(":-:"));
		this.build_blockInfo();
		cache_data.document.frmDoc.query_data.value="";
		this.exec_info('query', parameter+'owner='+this.owner+'&module='+this.module+'&block='+document.getElementById("filter_builder_blockinfo").value.split("'").join("\'").split("\r\n").join(":-:")+"&order_dir="+this.order_field_dir+"&order="+this.order_field);
		setTimeout("builder.getQuery()",2000);
	} else {
		alert("Sorry I was unable to find the Cache please try refreshing your browser.");
	}
} 
function __lib_getQuery(){
	if(cache_data.document.frmDoc.query_data.value==""){
		setTimeout("builder.getQuery()",2000);
	} else {
		data 			= new String(cache_data.document.frmDoc.query_data.value).split(":1234567890:");
		query_number	= data[0];
		myArray 		= new String(data[1]).split("[[pos]]").join("'").split("[[quot]]").join("\"").split("|1234567890|");
		//McKee[[pos]]s|1234567890|[[quot]]Evans, T[[quot]]|1234567890|Stanley Racing|1234567890|The Bethel Bible & Book Shop|1234567890|McKee Butchers|1234567890|Hutton Meats|1234567890|[[quot]]Fulton, J. & Co.[[quot]]|1234567890|Magill Meats|1234567890|Meadow Lane Meats|1234567890|Meadow Lane Meats
		sz="";
		for(i in myArray){
			sz += "<li  class='redbullet'>" + myArray[i] + "</li>";
		} 
		this.fillinQuery(sz, query_number);
	}
}

function __extract_information(szType, parameter){
	/*
		check to see if the cache is available for information yet? 
	*/
	if (cache_data.document.readyState != 'complete'){
		setTimeout(this.name+".exec_info('"+szType+"', '"+parameter+"');",1000);
		return;
	} else {
		if (szType=='query' && cache_data.frmDoc.query_data.value==''){
			cache_data.frmDoc.query_data.value="";
			cache_data.__extract_info('query',parameter);
		}
	}
}

function __update_order_field_dir(t){
	this.order_field_dir = t.value;
}
toggle_tab();