/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-														n e w C a t e g o r i e s . J S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 		
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-				$Revision: 1.4 $, 
-				$Date: 2004/09/16 10:52:51 $
-				$Author: aldur $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-																	C L A S S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function newCategories(){ 
	// properties of class
	this.outputDisplay			= document.getElementById("CategoryList");
	this.outputWindow			= document.getElementById("newCategoryForm");
	this.list					= new Array()
	this.id						= ''; //Category list Identifier
	this.output					= '';
	this.newlist				= new Array()
	this.pageForm 				= get_form();
	// methods of class
	this.toString				= __NC_toString;
	this.displayForm			= __NC_displayForm;
	this.hidedisplayForm		= __NC_hidedisplayForm;
	this.display				= __NC_display;
	this.showCat				= __NC_showCat;
	this.submitNewCat			= __NC_submitNewCat;	
	this.genDisplay				= __NC_genDisplay;
	this.submitCancelCat		= __NC_Cancel;
	this.display_options		= __NC_display_options;
	this.removeCat				= __NC_removeCat;
	this.edit					= __NC_displayFormEdit;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	FN:: __NC_toString()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __NC_toString(){
	var output_string	 = "";
	for(var i=0; i<this.newlist.length;i++){
		output_string	+= this.newlist[i][0]+"::"+this.newlist[i][1]+"::"+this.newlist[i][2]+"::"+this.newlist[i][3]+"\n";
	}
	this.pageForm.newCategories.value = output_string;
	if (this.output!=""){
		var f = get_form();
		max_imports = f.num_of_imports.value;
		for (i=0;i<max_imports;i++){
			eval("myelement = f.importfolder"+i); 
			elementsSelectedIndex = myelement.options[myelement.selectedIndex].value;
			myelement.options.length=0;
			this.display_options(myelement,this.list[0][0],elementsSelectedIndex,"");
		}
	}
}

function __NC_display_options(myelement,parent,selIndex,depth){
	for(var z=0; z<this.list.length;z++){
//		alert(this.list[z][0]+", "+this.list[z][1]+", "+this.list[z][2]+", "+this.list[z][3]+", ");
		if (parent==this.list[z][0]){
			position = myelement.options.length;
			myelement.options[position] = new Option(depth+this.list[z][2].split("&amp;").join("&"),this.list[z][1]);
			if (selIndex==this.list[z][1]){
				myelement.options[position].selected=true;
			}
			this.display_options(myelement,this.list[z][1],selIndex," - "+depth);
		}
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	FN:: __NC_displayForm()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __NC_displayForm(){
	if (this.outputWindow.rows.length>0){
		this.outputWindow.deleteRow(2);
		this.outputWindow.deleteRow(1);
		this.outputWindow.deleteRow(0);
	}
	alink = document.getElementById("addLink");
	alink.style.display='none';
	var r = this.outputWindow.insertRow(-1);
	var c = r.insertCell(-1);
		c.innerHTML = "Add sub folder to ";
	var c = r.insertCell(-1);
		str = "<select id='categoryLocation' name='categoryLocation' style='width:300px'>";
		startCat = this.id;
		if (this.id==''){
			if (this.list.length>0){
				startCat  = this.list[0][0];
			}
		}
		str += "<option value='"+startCat +"'>new top level entry</option>";
		str += this.showCat(startCat,0);
		str += "</select>";
		c.innerHTML = str;
	var r = this.outputWindow.insertRow(-1);
	var c = r.insertCell(-1);
		c.innerHTML = "Label";
	var c = r.insertCell(-1);
		str = "<input name='categoryLabel' id='categoryLabel' type='text' value='' style='width:300'/>";
		c.innerHTML = str;
	var r = this.outputWindow.insertRow(-1);
	var c = r.insertCell(-1);
		c.colspan="2";
		c.innerHTML = "<input type='button' onclick='newCategory.submitNewCat()' value='Add' class='bt'/> <input type='button' onclick='newCategory.submitCancelCat()' value='Cancel' class='bt'/>";
	this.outputWindow.style.display='';
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	FN:: __NC_displayFormEdit()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __NC_displayFormEdit(){
	if (this.outputWindow.rows.length>0){
		this.outputWindow.deleteRow(2);
		this.outputWindow.deleteRow(1);
		this.outputWindow.deleteRow(0);
	}
	alink = document.getElementById("addLink");
	alink.style.display='none';
	var r = this.outputWindow.insertRow(-1);
	var c = r.insertCell(-1);
		c.innerHTML = "Add sub folder to ";
	var c = r.insertCell(-1);
		str = "<select id='categoryLocation' name='categoryLocation' style='width:300px'>";
		startCat = this.id;
		if (this.id==''){
			if (this.list.length>0){
				startCat  = this.list[0][0];
			}
		}
		str += "<option value='"+startCat +"'>new top level entry</option>";
		str += this.showCat(startCat,0);
		str += "</select>";
		c.innerHTML = str;
	var r = this.outputWindow.insertRow(-1);
	var c = r.insertCell(-1);
		c.innerHTML = "Label";
	var c = r.insertCell(-1);
		str = "<input name='categoryLabel' id='categoryLabel' type='text' value='' style='width:300'/>";
		c.innerHTML = str;
	var r = this.outputWindow.insertRow(-1);
	var c = r.insertCell(-1);
		c.colspan="2";
		c.innerHTML = "<input type='button' onclick='newCategory.submitNewCat()' value='Add' class='bt'/> <input type='button' onclick='newCategory.submitCancelCat()' value='Cancel' class='bt'/>";
	this.outputWindow.style.display='';
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __NC_hidedisplayForm(){
	this.outputWindow.style.display='none';
	alink = document.getElementById("addLink");
	alink.style.display='';
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	FN:: __NC_showcat()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __NC_showCat(index, depthIndex){
	var str="";
	for (var i =0; i< this.list.length ; i++){
		if (this.list[i][0]==index){
			depthText=""
			for (var d=0; d < depthIndex; d++){
				depthText += "&nbsp;-&nbsp;";
			}
			str += "<option value='"+this.list[i][1]+"'>"+depthText+""+this.list[i][2]+"</option>";
			str += this.showCat(this.list[i][1], depthIndex+1);
		}
	}
	return str;
}


function __NC_submitNewCat(){
	if(this.pageForm.categoryLabel.value==""){
		alert("You have not supplied a label for this category");
		this.pageForm.categoryLabel.focus();
	} else {
		pos = this.list.length;
		this.list[pos] = new Array();
		this.list[pos][0] = this.pageForm.categoryLocation.options[this.pageForm.categoryLocation.selectedIndex].value;
		this.list[pos][1] = "new_"+pos;
		this.list[pos][2] = this.pageForm.categoryLabel.value;
		this.list[pos][3] = 1;
		
		npos = this.newlist.length;
		this.newlist[npos] = new Array();
		this.newlist[npos][0] = this.list[pos][0];
		this.newlist[npos][1] = this.list[pos][1];
		this.newlist[npos][2] = this.list[pos][2];
		this.newlist[npos][3] = this.list[pos][3];
	
		this.hidedisplayForm()
		this.display();
		this.toString();
	}
}
function __NC_display(){
	startCat = this.id;
	if (this.id==''){
		if (this.list.length>0){
			startCat  = this.list[0][0];
		}
	}
	str = this.genDisplay(startCat,0)
	this.outputDisplay.innerHTML = str;
}
function __NC_genDisplay(index, depthIndex){
	str="";
	for(var i =0 ; i<this.list.length;i++){
		if (index==this.list[i][0]){
			checkParent 		= this.list[i][0];
			checkIdentifier		= new String(this.list[i][1]);
			checkLabel			= LIBERTAS_GENERAL_unjtidy(this.list[i][2]+""); // fix any changes
			mychecked			= this.list[i][3];  // is this one selected
			
			if (this.output==""){
				str += "<input type='checkbox' name='cat_id_list[]' id='cat_"+checkParent+"_"+checkIdentifier+"' value='"+checkIdentifier+"' ";
				if (mychecked==1){
					str += "checked='true'";
				}
				str += ">";
				depthText ="";
				for (var d=0; d < depthIndex; d++){
						depthText += "&nbsp;-&nbsp;";
				}
				if (checkIdentifier.indexOf("new_")==-1){
					str += "<label style='width:350px' for='cat_"+checkParent+"_"+checkIdentifier+"'>"+depthText+checkLabel+"</label><br />";
				} else {
					str += "<label style='width:350px' for='cat_"+checkParent+"_"+checkIdentifier+"'>"+depthText+checkLabel+"</label> <input type='button' onclick='newCategory.removeCat(\""+checkIdentifier+"\")' value='Remove' class='bt'/><br />";
				}
				str += this.genDisplay(this.list[i][1], depthIndex + 1);
			} else {
				str += "<input type='hidden' name='cat_id_list[]' id='cat_"+checkParent+"_"+checkIdentifier+"' value='"+checkIdentifier+"' >";
				depthText ="";
				for (var d=0; d < depthIndex; d++){
					depthText += "&nbsp;-&nbsp;";
				}
				if(checkIdentifier.indexOf("new_")==-1){
					str += "<label style='width:350px' for='cat_"+checkParent+"_"+checkIdentifier+"'>"+depthText+checkLabel+"</label><br />";
				} else {
					str += "<label style='width:350px' for='cat_"+checkParent+"_"+checkIdentifier+"'>"+depthText+checkLabel+"</label> <input type='button' onclick='newCategory.removeCat(\""+checkIdentifier+"\")' value='Remove' class='bt'/><br />";
				}
				str += this.genDisplay(this.list[i][1], depthIndex + 1);
			}
		}
	}
	return str;
}

function __NC_Cancel(){
	this.hidedisplayForm()

}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	remove new category
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __NC_removeCat(index){
	pos = this.list.length;
	found = -1;
	for (i=0;i<pos;i++){
		if(this.list[i][1] == index){
			found = i;
		}
	}
	if(found!=-1){
		this.list.splice(found,1);
	}
	this.display();
	this.toString();
}
