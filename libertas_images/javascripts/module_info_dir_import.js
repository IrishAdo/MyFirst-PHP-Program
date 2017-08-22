/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Javascript Object to manage the import mapping
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var import_tool;
function information_directory_import_tool(import_field_list,directory_field_list){
	this.importfield		= import_field_list;
	this.directory			= directory_field_list;
	this.outputZone			= "_ifit_mapping";
	this.mappings			= new Array();
	this.firstrun			= 0;
	this.updated			= 0;
	this.duplicate_check	= 'no';
	
	//methods
	this.display			= __ifit_display;
	this.displayDuplicate	= __ifit_displayDuplicate;
	this.displaymap			= __ifit_mappingoutput;
	this.map				= __ifit_map;
	this.remove				= __ifit_remove;
	this.autoassign			= __ifit_auto_assign;
	
	// execute on new entry
	try{
		for (var i=0; i< this.importfield[0].length;i++){
			if (this.importfield[0][i]==""){
				this.importfield[0][i] = "Field "+(i+1);
			}
		}
	} catch(e){
		alert("Sorry there is a problem with your import file please try uploading it again");
	}
} 

function __ifit_display(){
	var doc = document.getElementById(this.outputZone);
	if (doc+""!="undefined"){
		if(this.firstrun==0){
			this.autoassign();
		}
		field_options 		= "";
		directory_options	= "";
		sz  = "<p>Below is the mapping section of the import tool you can select a column from the import file and map it to a field in the directory</p>";
		sz += "Map the <strong>Import</strong> field <select name='importfield'>"+field_options+"</select> to the <strong>directory</strong> field <select name='directoryfield'>"+directory_options+"</select> <input type='button' class='bt' id='mapbutton' name='mapbutton' value='Map it' onclick='import_tool.map()'/><br>";
		sz += "<hr><strong>Current defined mappings</strong><br><div id='mappingoutput' name='mappingoutput'></div>";
		doc.innerHTML = sz;
		this.displaymap();
	}
}
function __ifit_auto_assign(){
	this.firstrun=1;
	mymaps= Array(
				Array("tel","t","telephone","phone"),
				Array("fax","f","faxsmile"),
				Array("categories","category","categorisation","categorization")
			);
	if(this.mappings.length==0){
		for(var i=0; i<this.importfield[0].length; i++){
			for(var j=0; j<this.directory.length; j++){
				str1 = new String(this.importfield[0][i].toLowerCase()).split(' ').join('');
				str2 = new String(this.directory[j][1].toLowerCase()).split(' ').join('');
				ok =0;
				if(
					(this.importfield[0][i].toLowerCase()==this.directory[j][1].toLowerCase()) || 
					(str1==str2)
				){
					ok =1;
				} else {
					for(m=0;m<mymaps.length;m++){
						mapstr1 = "|"+mymaps[m].join("|")+"|";
//						alert(mapstr1+" = "+"|"+str1+"|")
						if(mapstr1.indexOf("|"+str1+"|")!=-1 &&  mapstr1.indexOf("|"+str2+"|")!=-1){
							ok =1;
						}
					}
				}
				if(ok==1){
					new_pos=this.mappings.length;
					this.mappings[new_pos] = new Array();
					this.mappings[new_pos][0] = this.importfield[0][i];
					this.mappings[new_pos][1] = i;
					this.mappings[new_pos][2] = this.directory[j][0];
					this.mappings[new_pos][3] = this.directory[j][1];
					ok=0;
				}
			}
		}
		if(this.mappings.length!=0){
			this.updated = 1;
		}
	}
}
function __ifit_mappingoutput(){
	sz="";
	var f = get_form();
	for(var i=0;i<this.mappings.length;i++){
		sz += "<div>";
		sz += "<div style='display:inline;width:150px;height:15px;'><input type='hidden' name='fieldindex[]' value='"+this.mappings[i][1]+"'/>"+this.mappings[i][0]+"</div>"; // from_id
		sz += "<div style='display:inline;width:150px;height:15px;'> maps to </div>"; // from_label
//		sz += "<div style='display:inline;width:150px;'>"+this.mappings[i][2]+"</div>"; // to_id
		sz += "<div style='display:inline;width:150px;height:15px;'><input type='hidden' name='importindex[]' value='"+this.mappings[i][2]+"'/>"+this.mappings[i][3]+"</div>";
		sz += "<div style='display:inline;width:150px;height:15px;'><a class='bt' style='width:100%;text-align:center;' href='javascript:import_tool.remove("+i+");'>Remove Mapping</a></div>";
		sz += "</div>"; // to_label
	}
	var doc = document.getElementById("mappingoutput");
	doc.innerHTML = sz;
	f.importfield.options.length=0;
	f.directoryfield.options.length=0;
	for(var i=0;i<this.importfield[0].length;i++){
		ok = 1;
		for(var z=0;z < this.mappings.length;z++){
			if(this.mappings[z][0]==this.importfield[0][i]){
				ok = 0;
			}
		}
		if (ok==1){
			f.importfield.options[f.importfield.options.length] = new Option(this.importfield[0][i],this.importfield[0][i]);
		}
	}
	for(var i=0;i<this.directory.length;i++){
		ok = 1;
		for(var z=0;z < this.mappings.length;z++){
			if(this.mappings[z][2]==this.directory[i][0] && (this.directory[i][2]!= "list" && this.directory[i][2]!= "check" && this.directory[i][2]!= "category")){
				ok = 0;
			}
		}
		//alert(ok+" "+this.directory[i][0]);
		if (ok==1){
			f.directoryfield.options[f.directoryfield.options.length] = new Option(this.directory[i][1],this.directory[i][0]);
		}
	}
	
	if (f.importfield.options.length==0 ||	f.directoryfield.options.length==0){
		f.mapbutton.style.display='none';
	} else {
		f.mapbutton.style.display='';
	}
}
function __ifit_map(){
	f = get_form();
	new_pos = this.mappings.length;
	ok =1;
	var typeofDirectoryEntry = "";
	for (var i=0; i < this.directory.length ; i++){
//		alert(this.directory[i][0]+"=="+f.directoryfield.options[f.directoryfield.selectedIndex].value);
		if (this.directory[i][0]==f.directoryfield.options[f.directoryfield.selectedIndex].value){
			typeofDirectoryEntry = this.directory[i][2];
		}
	}
	for(var i=0;i < new_pos;i++){
		if(this.mappings[i][0]==f.importfield.options[f.importfield.selectedIndex].value){
			ok = 0;
		}
	//	alert(f.directoryfield.options[f.directoryfield.selectedIndex].value +" ["+ typeofDirectoryEntry+"]");
		if(this.mappings[i][2]==f.directoryfield.options[f.directoryfield.selectedIndex].value && (typeofDirectoryEntry != "list" && typeofDirectoryEntry != "check" && typeofDirectoryEntry != "category")){
			ok = 0;
		}
	}
	if(ok==1){
		this.mappings[new_pos] = new Array();
		this.mappings[new_pos][0] = f.importfield.options[f.importfield.selectedIndex].value;
		found_index=-1;
		for (var i=0;i<this.importfield[0].length;i++){
			if(this.importfield[0][i]==f.importfield.options[f.importfield.selectedIndex].text){
				found_index=i;
			}
		}
		this.mappings[new_pos][1] = found_index;
		this.mappings[new_pos][2] = f.directoryfield.options[f.directoryfield.selectedIndex].value;
		this.mappings[new_pos][3] = f.directoryfield.options[f.directoryfield.selectedIndex].text;
		this.displaymap();
	} else {
		alert("Sorry you have already mapped (to/from) that field.");
	}
}

function __ifit_remove(index){
	this.mappings.splice(index,1);
	this.displaymap();
}
function __ifit_displayDuplicate(){
	var sz = "";
	// if no mapping attempt to auto assign fields
	if (this.mappings.length==0){
		this.autoassign();
	}
	// after auto assign atempt check number mapped
	if (this.mappings.length==0){
		sz = "<h1>Sorry you are required to map fields to import first before you can define duplicat checking</h1>";
	} else {
		sz  = "<p><strong>Do you want to enable duplicate checking</strong><br>";
		sz += "<input type='radio' name='duplicate_check' value='yes' id='dupcheckyes' onclick='javascript:import_tool.duplicate_check=\"yes\";document.getElementById(\"dupfields\").style.display=\"\";' ";
		if (this.duplicate_check=='yes'){
			sz += " checked";
		}
		sz += "> <label for='dupcheckyes'>Yes</label><br />";
		sz += "<input type='radio' name='duplicate_check' value='no' id='dupcheckno' onclick='javascript:import_tool.duplicate_check=\"no\";document.getElementById(\"dupfields\").style.display=\"none\";' ";
		if (this.duplicate_check=='no'){
			sz += " checked";
		}
		sz += "> <label for='dupcheckno'>No</label>";
		if (this.duplicate_check=='yes'){
			defstyle = "";
		} else {
			defstyle = "style='display:none;'";
		}
		sz += "</p><p id='dupfields' "+defstyle+"><strong>NOTE::</strong> <em>about choosing the fields that will be checked for duplicates</em><br><br>";
		sz += "If for exampleyou select more than one field then all of those fields have to map successfully for a duplicate to be found.<br>";
		sz += "<br><strong>Be careful</strong> with any duplication matching that you define <strong>know</strong> your import data for example if you are importing information into a directory and you have values that are to be stored in a lookup table (select combo, radio options or checkboxes) then you might not want to check for duplicates on <em>only those fields</em> as the number of records that will be added successfully would be effected.<br>";
		sz += "<br>Do duplicate checking on multiple fields for example in a business directory filter on <strong>company name</strong> and <strong>town</strong> where company name has a high chance of being unique per town,  while the list of towns is a common field available via a lookup table.<br>";
		sz += "<br><br><strong>Check the fields to add to your duplication condition</strong><br>";
		for (i=0; i<this.mappings.length;i++){
			if (this.mappings[i][3]!="Categorisation"){
				sz += "<input type='checkbox' name='duplicate_check_fields[]' value='"+this.mappings[i][2]+"' id='dupcheck"+i+"'> <label for='dupcheck"+i+"'>"+this.mappings[i][3]+"</label><br/>";
			}
		}
		sz += "</p>";
	}
	var doc = document.getElementById("duptab");
	doc.innerHTML = sz;
}
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
function importmapping(){
	import_tool.display();
}
function duplicatemapping(){
	import_tool.displayDuplicate();
}
setTimeout("start_mapping()",100);