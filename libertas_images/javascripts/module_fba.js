/*************************************************************************************************************************
* module_fba.js 
* $Date: 2005/01/25 20:03:16 $			
* $Revision: 1.7 $
* Form Builder Advanced
* for the  FORMBUILDER_ module
*************************************************************************************************************************/
myFrm = new Frm();

function Frm(){
	this.loaded_modules			= new Array();
	this.imported_fields		= new Array();
	this.used_fields			= new Array();
	this.imported_modules		= new Array();
	this.merged_fields			= new Array();
	this.mappingchecked			= 0;
	this.importtag				= "";
	this.import_selection		= new Array();
	
	// extract functions
	this.get_fields				= __Frm_get_fields;
	this.retrieveFields			= __Frm_retrieveFields;
	this.exec_info				= __Frm_extract_information;			
	// display functions
	this.draw					= __Frm_draw;
	this.draw_merge				= __Frm_draw_merge;
	this.render					= __Frm_render;
	this.updateImport			= __Frm_updateImport;
	this.addSection				= __Frm_addSection;
	this.addField				= __Frm_addField;
	this.remove_from			= __Frm_remove_from;
	this.move_from				= __Frm_move_from;
	this.edit_from				= __Frm_edit_from;
	this.edit_from_save			= __Frm_edit_from_save;
	this.get_module_map 		= __Frm_get_module_map;
	this.select_mods			= __Frm_select_mods;
	this.update_merge			= __Frm_update_merge;
	this.get_field				= __Frm_get_field;
	// ecommerce functions
	this.checkChargetype		= __Frm_checkChargetype;
	this.displayFieldPrice		= __Frm_displayFieldPrice;
	this.retrievePriceFields	= __Frm_retrievePriceFields;
	this.updatePrice			= __Frm_updatePrice;
	// merge
	this.select_merge			= __Frm_select_merge;
}
/*************************************************************************************************************************
* blank the form structure
*************************************************************************************************************************/
function blank_fba(type){
	myFrm.imported_fields		= new Array();
	myFrm.used_fields			= new Array();
	myFrm.imported_modules		= new Array();
	myFrm.merged_fields			= new Array();
	myFrm.mappingchecked		= 0;
	myFrm.importtag				= "";
	myFrm.checkChargetype('')
	myFrm.updatePrice('fieldprice', prices, prices[0][0]);
	el = document.getElementById("import_module");
	el.options.length=1;
	for(i=0 ; i < myFrm.import_selection.length ; i++){
		val = myFrm.import_selection[i][1].split("::");
//		alert(this.import_selection[i]);
		if(type==1 && val[1]!="-1"){
			el.options[el.options.length] = new Option(myFrm.import_selection[i][0], myFrm.import_selection[i][1]);
		}
		if(type==0){
			el.options[el.options.length] = new Option(myFrm.import_selection[i][0], myFrm.import_selection[i][1]);
		}
	}
	doc = document.getElementById("importoption");
	doc.style.display='';
	myFrm.draw();
}
/*************************************************************************************************************************
*  generate a new instance
*************************************************************************************************************************/
function initialise(el){
	prev="";
	var f= get_form();
	importmodule = document.getElementById(el);
	myFrm.import_selection = new Array();
	for(i=1 ; i < importmodule.options.length ; i++){
		myFrm.import_selection[myFrm.import_selection.length] = new Array(importmodule.options[i].text, importmodule.options[i].value);
	}
	for(i=0;i<imported_tags.length;i++){
		pos = myFrm.imported_fields.length;
		myFrm.imported_fields[pos] = imported_tags[i][1].split("::");
		myFrm.imported_fields[pos][myFrm.imported_fields[pos].length] = imported_tags[i][0];
		if (prev!=imported_tags[i][0]){
			myFrm.imported_modules[myFrm.imported_modules.length] = imported_tags[i][0];
			prev=imported_tags[i][0];
		}
	}
	for(i=0;i<usedfieldlist.length;i++){
		var list = new String(usedfieldlist[i]).split("::");
		pos = myFrm.used_fields.length;
		myFrm.used_fields[pos] = Array()
		myFrm.used_fields[pos]["name"]		= list[0];
		myFrm.used_fields[pos]["belongs"]	= list[5]+"::"+list[6];
		myFrm.used_fields[pos]["label"]		= list[1];
		myFrm.used_fields[pos]["labelpos"]	= list[7];
		myFrm.used_fields[pos]["type"] 		= list[2];
		myFrm.used_fields[pos]["rank"] 		= list[8];
		myFrm.used_fields[pos]["required"]	= (list[9]==0?"no":"yes");
		ipos = myFrm.loaded_modules.length;
		ok=1;
		for(y=0; y<ipos; y++){
			if ((myFrm.loaded_modules[y]["value"]=="FBA::-1") || (myFrm.loaded_modules[y]["value"]==list[5]+"::"+list[6])){
				ok=0;
			}
		}
		if(ok==1){
			myFrm.loaded_modules[ipos]				= new Array();
			myFrm.loaded_modules[ipos]["value"]		= list[5]+"::"+list[6];
			for(a=1 ; a < importmodule.options.length ; a++){
				if (importmodule.options[a].value == list[5]+"::"+list[6]){
					if (importmodule.options[a].text.indexOf(" :: ")>0){
						info = importmodule.options[a].text.split(" :: ");
						myFrm.loaded_modules[ipos]["module"]	= info[0];
						myFrm.loaded_modules[ipos]["section"]	= info[1];
					} else {
						myFrm.loaded_modules[ipos]["module"]	= importmodule.options[a].text;
						myFrm.loaded_modules[ipos]["section"]	= "";
					}
				}
			}
		}
	}
	myFrm.used_fields.sort(mysort);
	for(i=0;i<merged_tags.length;i++){
		pos = myFrm.merged_fields.length;
		list = merged_tags[i].split("::");
		myFrm.merged_fields[pos] = Array();
		myFrm.merged_fields[pos][0] = list[0];
		myFrm.merged_fields[pos][1] = list[1];
		myFrm.merged_fields[pos][2] = list[2];
		myFrm.merged_fields[pos][3] = list[3];
		myFrm.merged_fields[pos][4] = list[4];
		myFrm.merged_fields[pos][5] = list[5];
		myFrm.merged_fields[pos][6] = list[6];
		myFrm.merged_fields[pos][7] = list[7]+"::"+list[8];
		myFrm.merged_fields[pos][8] = list[9]+"::"+list[10];
	}
	myFrm.mappingchecked=1;
	for(i=0; i < prices.length; i++){
		prices[i] = prices[i].join("::");
	}
	myFrm.checkChargetype('')
	myFrm.updatePrice('fieldprice', prices, prices[0][0]);
	myFrm.updateImport(el);
	myFrm.draw();
}

function import_fields(element){
	el = document.getElementById(element);
	val = el.options[el.options.selectedIndex].value;
	myFrm.retrieveFields(val, element);
	var f = get_form();
	if(f.fbs_type.options.selectedIndex==1){
		doc = document.getElementById("importoption");
		doc.style.display='none';
	}
}

function __Frm_get_fields(fieldname, el){
	var f = get_form();
	if(el == 'import_module'){
		tmp = cache_data.document.frmDoc.field_data.value;
		if("data:"+tmp=="data:"){
			setTimeout("myFrm.get_fields('"+fieldname+"','"+el+"');",2000);
		} else {
			listing = tmp.split("|1234567890|");
			index = this.imported_fields.length
			for(i=0;i<listing.length;i++){
				this.imported_fields[index+i] = listing[i].split("::");
				this.imported_fields[index+i][this.imported_fields[index+i].length] = fieldname;
			}
			this.mappingchecked = 0;
			dInput = document.getElementById("addfieldsbtn");
			dInput.disabled=false;
			dInput = document.getElementById(el);
			dInput.disabled=false;
			this.imported_modules[this.imported_modules.length] = dInput.options[dInput.options.selectedIndex].value;
			this.updateImport(el);
		}
	}
	if(el == 'fieldoptions'){
		tmp = cache_data.document.frmDoc.field_options.value;
		if("data:"+tmp=="data:"){
			setTimeout("myFrm.get_fields('"+fieldname+"','"+el+"');",2000);
		} else {
			listing = tmp.split("|1234567890|");
			var f 	= document.getElementById('priceField');
			this.updatePrice(el,listing,f.options[f.options.selectedIndex].value);
		}
	}
	this.draw();
}

function __Frm_retrieveFields(fieldname, el){
	var f = get_form();
//	f.choosenMenu.disabled= true;
	dInput = document.getElementById(el);
	if(dInput.selectedIndex!=0){
		dInput.disabled=true;
		dInput = document.getElementById("addfieldsbtn");
		dInput.disabled=true;
		this.importtag = fieldname;
		ipos =myFrm.loaded_modules.length;
		fielddata = fieldname.split("::");
		myFrm.loaded_modules[ipos] = new Array();
		myFrm.loaded_modules[ipos]["value"] = f.import_module.options[f.import_module.selectedIndex].value;
		label = f.import_module.options[f.import_module.selectedIndex].text;
		if (label.indexOf(" :: ")!=-1){
			l = label.split(" :: ");
			myFrm.loaded_modules[ipos]["module"] = l[0];
			myFrm.loaded_modules[ipos]["section"] = l[1];
		} else {
			myFrm.loaded_modules[ipos]["module"] = label;
			myFrm.loaded_modules[ipos]["section"] = "";
		}
		cmd = fielddata[0]+"GET_FIELD_LIST";
		if (fielddata[1]!="-1"){
			cmd += "&identifier="+fielddata[1];
		}
		this.exec_info('field',cmd);
		setTimeout("myFrm.get_fields('"+fieldname+"','"+el+"');",2000);
	}
/*
	this.loaded_modules[index]["value"]
			
			myFrm.loaded_modules[ipos]["value"]		= list[5]+"::"+list[6];
			for(a=1 ; a < importmodule.options.length ; a++){
				if (importmodule.options[a].value == list[5]+"::"+list[6]){
					if (importmodule.options[a].text.indexOf(" :: ")>0){
						info = importmodule.options[a].text.split(" :: ");
						myFrm.loaded_modules[ipos]["module"]	= info[0];
						myFrm.loaded_modules[ipos]["section"]	= info[1];
*/

}

function __Frm_extract_information(szType, parameter){
	/*
		check to see if the cache is available for information yet? 
	*/
	if (cache_data.document.readyState != 'complete'){
		setTimeout(this.name+".exec_info('"+szType+"', '"+parameter+"');",1000);
		return;
	} else {
		if (szType=='field'){
			cache_data.frmDoc.field_data.value='';
			cache_data.__extract_info(szType,parameter);
		}
		if (szType=='field_options'){
			cache_data.frmDoc.field_options.value='';
			cache_data.__extract_info(szType,parameter);
		}
	}
}

/*************************************************************************************************************************
* this.draw()
*************************************************************************************************************************/
function __Frm_draw(){
	/*************************************************************************************************************************
    * Check for merged fields
    *************************************************************************************************************************/
	if(this.merged_fields.length == 0 || this.mappingchecked == 0){
		// look for mapping
		this.mappingchecked =1;
		for(i=0;i<this.imported_fields.length;i++){
			if (this.imported_fields[i][4]!="" && this.imported_fields[i][2]=="hidden"){
				for(j=0;j<this.imported_fields.length;j++){
					if (this.imported_fields[i][4] == this.imported_fields[j][0]){
						ok =1;
						for(z=0;z<this.merged_fields.length;z++){
							if ((this.merged_fields[z][2]!=i && this.merged_fields[z][3]!=i)&&(this.merged_fields[z][2]!=j && this.merged_fields[z][3]!=j)) {
								if(this.merged_fields[z][0] == this.imported_fields[i][4]){
									ok = 0;
									break;
								} 
								if(this.merged_fields[z][1] == this.imported_fields[i][4]){
									ok = 0;
									break;
								}
							} else{
								if (this.merged_fields[z][2]==i || this.merged_fields[z][3]==i || this.merged_fields[z][2]==j || this.merged_fields[z][3]==j) {
								} else{
									ok =0;
									break;
								}
							}
						}
						if(ok==1){
							if(this.imported_fields[j][this.imported_fields[j].length - 1]!=this.imported_fields[i][this.imported_fields[j].length - 1]){
								winner = 0;
								if(this.imported_fields[j][2]=="select"){
									winner = 1;
									this.imported_fields[i]["display"] ="hide";
									for(c=0;c<this.used_fields.length;c++){
										if(this.used_fields[c]["name"] == this.imported_fields[i][0]){
											this.used_fields[c]["name"] = this.imported_fields[j][0]
											this.used_fields[c]["belongs"] = this.imported_fields[j][this.imported_fields[j].length - 1]
										}
									}
								}
								if(this.imported_fields[i][0]=="select"){
									winner = 2;
									this.imported_fields[j]["display"] ="hide";
									for(c=0;c<this.used_fields.length;c++){
										if(this.used_fields[c]["name"] == this.imported_fields[j][0]){
											this.used_fields[c]["name"] = this.imported_fields[i][0]
											this.used_fields[c]["belongs"] = this.imported_fields[j][this.imported_fields[j].length - 1]
										}
									}
								}
								if (winner == 0){
									winner = 2;
									this.imported_fields[j]["display"] ="hide";
									for(c=0;c<this.used_fields.length;c++){
										if(this.used_fields[c]["name"] == this.imported_fields[j][0]){
											this.used_fields[c]["name"] = this.imported_fields[i][0]
											this.used_fields[c]["belongs"] = this.imported_fields[j][this.imported_fields[j].length - 1]
										}
									}
								}
								this.merged_fields[this.merged_fields.length] = Array(
										this.imported_fields[j][0],this.imported_fields[i][0],
										j,i,
										this.imported_fields[j][2],this.imported_fields[i][2], 
										winner, 
										this.imported_fields[j][this.imported_fields[j].length - 1], this.imported_fields[i][this.imported_fields[j].length - 1])
							}
						}
						break;
					}
				}
			}
		}
	}
	/*************************************************************************************************************************
    * merged fields
    *************************************************************************************************************************/
//		 		  this.imported_fields[j]["display"] ="hide";
		for(i=0;i<this.imported_fields.length;i++){
			if (this.imported_fields[i][2]!="hidden"){
//				for(j=0;j<this.imported_fields.length;j++){
					for(c=0;c<this.merged_fields.length;c++){
						if (this.merged_fields[c][6] == 1 && this.merged_fields[c][1] == this.imported_fields[i][0]){
							this.imported_fields[i]["display"] = "hide";
//							this.imported_fields[i]["display"] = "hide";
						}
						if (this.merged_fields[c][6] == 2 && this.merged_fields[c][0] == this.imported_fields[i][0]){
//							this.imported_fields[j]["display"] = "hide";
							this.imported_fields[i]["display"] = "hide";
						}
					}
//				}
			}
		}
	/*************************************************************************************************************************
    * draw the structure Tab
    *************************************************************************************************************************/
	var dOut	 = document.getElementById("imported_field_div");
	var szOut	 = "";
	/*
		0 => Name
		1 => Label
		2 => Type
		3 => Maps
		4 => Auto
	*/
	szFields="";
	f = get_form();
	ok =1;
	for(i=0;i<this.imported_fields.length;i++){
		if(this.imported_fields[i][0]=="__search__"){
			if(f.fbs_type.options.selectedIndex==0){
				this.imported_fields.splice(i,1);
				ok = 0;
			} else {
				ok = 0
			}
		}
	}
	if(f.fbs_type.options.selectedIndex==1 && ok == 1 && this.imported_fields.length>0){
		pos = this.imported_fields.length
		this.imported_fields[pos] = Array("__search__","Search Phrase","__search__","","","");
		this.imported_fields[pos][this.imported_fields[pos].length] = this.imported_modules[0];
//		alert(this.imported_fields[pos]);
	}
	for(i=0;i<this.imported_fields.length;i++){
		if(this.imported_fields[i][2]!="hidden" && this.imported_fields[i]["display"]!="hide"){
			ok = 1;
			for (x=0; x<this.used_fields.length; x++){
				if(this.used_fields[x]["name"]==this.imported_fields[i][0]){
					ok = 0;
				}
			}
			if(ok==1){
				szFields	+= "<option value='" + this.imported_fields[i][0] + "'>"+this.imported_fields[i][1]+"</option>";
			}
		}
	}	
	szOut += "<div style='width:100%;'><div style='width:300px;display:inline'><p><strong>Add elements to this form </strong></p>";
	szOut += "<div id='showfields' name='showfields' ";
	if(szFields==""){
		szOut += " style='display:none'";
	}
	
	szOut += ">";
	szOut += "<label for='flist'>Fields</label> <select name='flist' id='flist'>";
	szOut += szFields;
	szOut += "</select>  <input class='bt' type=button value='Add Field' onclick='myFrm.addField()'></div>";
	szOut += "<label for='flist'>Formatting</label> <select name='formatlist' id='formatlist'>";
	szOut += "	<option value='pagesplitter'>Page Break</option>";
	szOut += "	<option value='colsplitter'>New Column</option>";
	szOut += "	<option value='rowsplitter'>New Row</option>";
	szOut += "	<option value='label'>New Label</option>";
	szOut += "</select> <input type=button class='bt' value='Add section' onclick='myFrm.addSection()'></div>";
	szOut += "<div style='width:650px;display:inline;vertical-align:top' id='questions'></div></div><hr/><div id='displayStructure'></div>";
	dOut.innerHTML = szOut;
	/*************************************************************************************************************************
    * reset the string for merged tab
    *************************************************************************************************************************/
	this.draw_merge();
	this.render();
}

/*************************************************************************************************************************
* update the import module drop down
*************************************************************************************************************************/
function __Frm_updateImport(el){
	dInput = document.getElementById(el);
	dInput.disabled=false;
	list = new Array();
	for (i=0;i<dInput.options.length;i++){
		ok = 1;
		for (z=0;z<this.imported_modules.length;z++){
			if (this.imported_modules[z]==dInput.options[i].value){
				ok=0;
				break;
			}
		}
		if (ok==1){
			list[list.length] = new Array(dInput.options[i].value, dInput.options[i].text);
		}
	}
	dInput.options.length =0;
	for (i=0;i<list.length;i++){
		dInput.options[dInput.options.length] = new Option(list[i][1], list[i][0]);
	}
	var f = get_form();
	if(f.fbs_type.options.selectedIndex==1 && this.imported_modules.length>0){
		doc = document.getElementById("importoption");
		doc.style.display='none';
	}
	
}

/*************************************************************************************************************************
* add a section to the form structure
*************************************************************************************************************************/
function __Frm_addSection(){
	el = document.getElementById("formatlist");
	pos = this.used_fields.length;
	this.used_fields[pos] = new Array();
	this.used_fields[pos]["name"] =	el.options[el.options.selectedIndex].value;
	this.used_fields[pos]["type"] =	el.options[el.options.selectedIndex].value;
	this.used_fields[pos]["belongs"] = "FBA::-1";
	this.used_fields[pos]["required"] = "no";
	if(el.options[el.options.selectedIndex].value=="pagesplitter"){
		label = "Page Splitter";
	} else if(el.options[el.options.selectedIndex].value=="colsplitter"){
		label = "Column Splitter";
	} else if(el.options[el.options.selectedIndex].value=="rowsplitter"){
		label = "Row Splitter";
	} else if(el.options[el.options.selectedIndex].value=="label"){
		label = "Undefined";
		this.edit_from(pos);
	} else {
		label = "Page Splitter";
	}
	this.used_fields[pos]["label"] = label;
	this.render();
}
/*************************************************************************************************************************
* add a field to the form structure
*************************************************************************************************************************/
function __Frm_addField(){
	el = document.getElementById("flist");
	pos = this.used_fields.length;
	this.used_fields[pos] = new Array();
	for(i=0;i<this.imported_fields.length;i++){
		if(this.imported_fields[i][0] == el.options[el.options.selectedIndex].value){
			this.used_fields[pos]["name"] 		=	this.imported_fields[i][0];
			this.used_fields[pos]["type"] 		=	this.imported_fields[i][2];
			this.used_fields[pos]["label"] 		= 	this.imported_fields[i][1];
			this.used_fields[pos]["required"]	=	this.imported_fields[i][5];
			this.used_fields[pos]["belongs"]	=	this.imported_fields[i][this.imported_fields[i].length -1];
		}
	}
	el.options.length =0;
	for(i=0;i<this.imported_fields.length;i++){
		if(this.imported_fields[i][2]!="hidden" && this.imported_fields[i]["display"]!="hide"){
			ok = 1;
			for (x=0; x<this.used_fields.length; x++){
				if(this.used_fields[x]["name"]==this.imported_fields[i][0]){
					ok = 0;
				}
			}
			if(ok==1){
				el.options[el.options.length] = new Option(this.imported_fields[i][1], this.imported_fields[i][0]);
			}
		}
	}	
	if(el.options.length==0){
		var showfields = document.getElementById("showfields");
		showfields.style.display='none';
	}
	this.render();
}


/*************************************************************************************************************************
* 
*************************************************************************************************************************/
function __Frm_render(){
	dOut = document.getElementById("displayStructure");
	myArray = this.used_fields;
	/*
		0 => Name
		1 => Label
		2 => Type
		3 => Maps
		4 => Auto
		5 => Sum Label Position
		6 => Con Label Position
	*/
	var sz = '<input type="hidden" name="form_num_of_fields" id="form_num_of_fields" value="' + myArray.length + '" />';
		sz+= "<table width='100%' border='0' cellpadding=3 cellspacing=1 bgcolor=#cccccc><tr bgcolor=#ffffff><td valign=top>";
	var closing = "";
	var f = get_form();
	field="form";
	
	len = this.merged_fields.length;
//	alert(len);
	szmerged="";
	for(f_index = 0 ; f_index < len; f_index++){
		szmerged += "<input type='hidden' name='merge[]' value='"+this.merged_fields[f_index].join("::")+"'/>";
	}
	sz += szmerged;
//	alert(szmerged);
	
	len = this.imported_fields.length;
	info_fieldlist = this.imported_fields;
	
	
	formfield = "choose_form_field";
	for(info_index = 0 ; info_index < len; info_index++){
		if(info_fieldlist[info_index][2]=="hidden"){
			sz += "<input type='hidden' name='hidden[]' value='"+info_fieldlist[info_index][0]+"'/>";
			sz += "<input type='hidden' name='belongs[]' value='"+info_fieldlist[info_index][info_fieldlist[info_index].length - 1]+"'/>";
		}
	}
	shown=0;
	for (var i =0; i<myArray.length;i++){
		if(myArray[i]["type"] != "hidden" ){
			extra ="";
			extra += '<input type="hidden" name="'+field+'_name_' + i + '" id="'+field+'_name_' + i + '" value="' + myArray[i]["name"] + '" />';
			for(info_index = 0 ; info_index < len; info_index++){
				if(myArray[i]["name"] == info_fieldlist[info_index][0]){
//					myArray[i]["label"] 	= info_fieldlist[info_index][1];
					myArray[i]["belongs"]	= info_fieldlist[info_index][info_fieldlist[info_index].length - 1];
//					myArray[i]["required"]	= info_fieldlist[info_index][5];
					myArray[i]["labelpos"]	= 1;
				}
			}
			extra += '<input type="hidden" name="'+field+'_label_' + i + '" id="'+field+'_label_' + i + '" value="' + myArray[i]["label"] + '" />';
			extra += '<input type="hidden" name="'+field+'_type_' + i + '" id="'+field+'_type_' + i + '" value="' + myArray[i]["type"] + '" />';
			extra += '<input type="hidden" name="'+field+'_rank_' + i + '" id="'+field+'_rank_' + i + '" value="' + (i) + '" />';
			extra += '<input type="hidden" name="'+field+'_belongs_' + i + '" id="'+field+'_belongs_' + i + '" value="' + myArray[i]["belongs"] + '" />';
			extra += '<input type="hidden" name="'+field+'_labelpos_' + i + '" id="'+field+'_labelpos_' + i + '" value="' + myArray[i]["labelpos"] + '" />';
			extra += '<input type="hidden" name="'+field+'_required_' + i + '" id="'+field+'_required_' + i + '" value="' + myArray[i]["required"] + '" />';
			p0 = "";
			p1 = "";
			p2 = "";
			p3 = "";
			options		 = "<div style='width:auto;display:inline;text-align:right'>";
			options 	+= "<a href='javascript:myFrm.remove_from("+i+");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Remove</a> ";
			if(i==0 || shown==0){
				options 	+= "<a style='width:60px;background-color:#ebebeb;color:#999999;border:1px solid #cccccc;text-align:center;text-decoration:none'>Up</a> ";
			} else {
				options 	+= "<a href='javascript:myFrm.move_from("+i+",\"Up\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Up</a> ";
			}
			if(i==myArray.length-1){
				options 	+= "<a style='width:60px;background-color:#ebebeb;color:#999999;border:1px solid #cccccc;text-align:center;text-decoration:none'>Down</a> "
			} else {
				options 	+= "<a href='javascript:myFrm.move_from("+i+",\"Down\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Down</a> ";
			}
			//if (myArray[i]["type"]=="label"){
			if ((myArray[i]["type"]!="colsplitter") && (myArray[i]["type"]!="rowsplitter")){
				options 	+= "<a href='javascript:myFrm.edit_from("+i+");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Edit</a> ";
			}
			options 	+= "</div>";
			myArraylabel="";
			if(myArray[i]["required"]==1 || myArray[i]["required"]=="yes"){
				myArraylabel = " <span style=\"color:#ff9900\">*</span>";
			}
			if (myArray[i]["type"]=="label"){
				shown=1;
//				alert(myArray[i]["label"]);
				sz += "<div style='width:auto;'><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"</div>";
			} else if (myArray[i]["type"]=="colsplitter"){
				shown=1;
				sz += "<br>"+extra+"<a href='javascript:myFrm.remove_from("+i+");' class='bt' style='width:120px;text-align:center;text-decoration:none'>Remove Column</a> ";
				sz += "<a href='javascript:myFrm.move_from("+i+",\"Up\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Up</a> ";
				sz += "<a href='javascript:myFrm.move_from("+i+",\"Down\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Down</a></td><td valign=top width='50%'>";
			} else if (myArray[i]["type"]=="rowsplitter"){
				shown=1;
				sz += "</td></tr></table>"+extra+"<a href='javascript:myFrm.remove_from("+i+");' class='bt' style='width:120px;text-align:center;text-decoration:none'>Remove Row</a> ";
				sz += "<a href='javascript:myFrm.move_from("+i+",\"Up\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Up</a> ";
				sz += "<a href='javascript:myFrm.move_from("+i+",\"Down\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Down</a><table width='100%' border='0' cellpadding=3 cellspacing=1 bgcolor=#cccccc><tr bgcolor=#ffffff><td valign=top width='50%'>";
			} else if (myArray[i]["type"]=="pagesplitter"){
				shown=1;
				sz += "</td></tr></table>"+extra+"<div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+" "+myArraylabel+"</strong></div><a href='javascript:myFrm.remove_from("+i+");' class='bt' style='width:160px;text-align:center;text-decoration:none'>Remove Page Splitter</a> ";
				sz += "<a href='javascript:myFrm.move_from("+i+",\"Up\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Up</a> ";
				sz += "<a href='javascript:myFrm.move_from("+i+",\"Down\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Down</a>";
				sz += "<a href='javascript:myFrm.edit_from("+i+");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Edit</a> ";
				sz += "<table width='100%' border='0' cellpadding=3 cellspacing=1 bgcolor=#cccccc><tr bgcolor=#ffffff><td valign=top width='50%'>";
			} else if (myArray[i]["type"]=="select" || myArray[i]["type"]=="radio"){
				shown=1;
				sz += "<div><div style='display:inline;width:50%;border-bottom:1px dashed #cccccc;'>"+extra+"<strong>"+myArray[i]["label"]+" "+myArraylabel+"</strong></div>"+options+"</div>";
			} else if ((myArray[i]["type"]=="check") || (myArray[i]["type"]=="associated_entries")){
				shown=1;
				sz += "<div><div style='display:inline;width:50%;border-bottom:1px dashed #cccccc;'>"+extra+"<strong>"+myArray[i]["label"]+" "+myArraylabel+"</strong></div>"+options+"</div>";
			} else if (myArray[i]["type"]=="smallmemo"){
				shown=1;
				sz += "<div><div style='display:inline;width:50%;border-bottom:1px dashed #cccccc;'>"+extra+"<strong>"+myArray[i]["label"]+" "+myArraylabel+"</strong></div>"+options+"</div>";
			} else if (myArray[i]["type"]=="memo"){
				shown=1;
				sz += "<div><div style='display:inline;width:50%;border-bottom:1px dashed #cccccc;'>"+extra+"<strong>"+myArray[i]["label"]+" "+myArraylabel+"</strong></div>"+options+"</div>";
			} else if (myArray[i]["type"]=="URL"){
				shown=1;
				sz += "<div><div style='display:inline;width:50%;border-bottom:1px dashed #cccccc;'>"+extra+"<strong>"+myArray[i]["label"]+" "+myArraylabel+"</strong></div>"+options+"</div>";
			} else if (myArray[i]["type"]=="hidden"){
				sz += ""+extra+"";
			} else {
				shown=1;
				sz += "<div style='width:auto;'><div style='display:inline;width:50%;border-bottom:1px dashed cccccc;'>"+extra+"<strong>"+myArray[i]["label"]+" "+myArraylabel+"</strong></div>"+options+"</div>";
			}
		}
	}
	sz+= "</td></tr></table>"+closing;
	dOut.innerHTML = sz;
}

function __Frm_remove_from(i){
	this.used_fields.splice(i,1);
	el = document.getElementById("flist");
	el.options.length =0;
	for(i=0;i<this.imported_fields.length;i++){
		if(this.imported_fields[i][2]!="hidden" && this.imported_fields[i]["display"]!="hide"){
			ok = 1;
			for (x=0; x<this.used_fields.length; x++){
				if(this.used_fields[x]["name"]==this.imported_fields[i][0]){
					ok = 0;
				}
			}
			if(ok==1){
				el.options[el.options.length] = new Option(this.imported_fields[i][1], this.imported_fields[i][0]);
			}
		}
	}	
	var showfields = document.getElementById("showfields");
	if(el.options.length==0){
		showfields.style.display='none';
	} else {
		showfields.style.display='';
	}
	this.render();
	var doc = document.getElementById("questions");
	doc.innerHTML= "";
}
function __Frm_move_from(i,direction){
	var doc = document.getElementById("questions");
	doc.innerHTML= "";
	if (direction=="Up"){
		tmp_name	= this.used_fields[i-1]["name"];
		tmp_type	= this.used_fields[i-1]["type"];
		tmp_label	= this.used_fields[i-1]["label"];
		tmp_belongs = this.used_fields[i-1][this.used_fields[i-1].length - 1];
		tmp_belongs2 = this.used_fields[i-1]["belongs"];
		tmp_required = this.used_fields[i-1]["required"];
		this.used_fields[i-1] = Array();
		this.used_fields[i-1]["name"]	= this.used_fields[i]["name"];
		this.used_fields[i-1]["type"]	= this.used_fields[i]["type"];
		this.used_fields[i-1]["label"]	= this.used_fields[i]["label"];
		this.used_fields[i-1]["belongs"]	= this.used_fields[i]["belongs"];
		this.used_fields[i-1]["required"]	= this.used_fields[i]["required"];
		this.used_fields[i-1][this.used_fields[i-1].length - 1]= this.used_fields[i][this.used_fields[i].length - 1];
		this.used_fields[i] = Array();
		this.used_fields[i]["name"]		= tmp_name;
		this.used_fields[i]["type"] 	= tmp_type;
		this.used_fields[i]["label"]	= tmp_label;
		this.used_fields[i]["belongs"]	= tmp_belongs2;
		this.used_fields[i][this.used_fields[i].length - 1]	= tmp_belongs;
		this.used_fields[i]["required"]	= tmp_required;
	} else {
		this.move_from(i+1,"Up");
/*
		tmp_name 												= this.used_fields[i]["name"];
		tmp_type 												= this.used_fields[i]["type"];
		tmp_label 												= this.used_fields[i]["label"];
		tmp_belongs												= this.used_fields[i][this.used_fields[i].length - 1];
		tmp_belongs2 											= this.used_fields[i]["belongs"];
		this.used_fields[i] 									= Array();
		this.used_fields[i]["name"] 							= this.used_fields[i+1]["name"];
		this.used_fields[i]["type"] 							= this.used_fields[i+1]["type"];
		this.used_fields[i]["label"]							= this.used_fields[i+1]["label"];
		this.used_fields[i]["belongs"]							= this.used_fields[i+1]["belongs"];
		this.used_fields[i][this.used_fields[i].length - 1]		= this.used_fields[i+1][this.used_fields[i+1].length - 1];
		this.used_fields[i+1] 									= Array();
		this.used_fields[i+1]["name"]							= tmp_name;
		this.used_fields[i+1]["type"]							= tmp_type;
		this.used_fields[i+1]["label"]							= tmp_label;
		this.used_fields[i+1]["belongs"]						= tmp_belongs2;
		this.used_fields[i+1][this.used_fields[i+1].length - 1]	= tmp_belongs;
*/
	}
	this.render();
}

function __Frm_checkChargetype(el){
	var dInput = document.getElementById("fbs_ecommerce");
	if (dInput.checked){
		// No
		document.getElementById('getChargetype').style.display='none'
		document.getElementById('charge_data').style.display='none'
	} else {
		// Yes
		chType = document.getElementById('getChargetype')
		chType.style.display=''
		var szOut= "<p>Should a sales tax (VAT) be charged on this? <br/>";
		szOut += '<INPUT type="radio" name="charge_vat" id="charge_vat_1" value="1"';
		if(charge_vat==1){
			szOut += ' checked';
		}
		szOut += '><LABEL for="charge_vat_1">Yes</LABEL> ';
		szOut += '<INPUT type="radio" name="charge_vat" id="charge_vat_2" value="0"';
		if(charge_vat==0){
			szOut += ' checked';
		}
		szOut += '><LABEL for="charge_vat_2">No</LABEL><BR>';
		szOut += "</p>";									
		szOut += "Choose the type of pricing <select name='pricingStructure' onchange='myFrm.displayFieldPrice(\"\")'>";
		szOut += "	<option value ='1'";
		if (pricestructure==1){
			szOut+=" selected='true'";
		}
		szOut+=">fixed price</option>";
		szOut+="	<option value='0'";
		if (pricestructure==0){
			szOut+=" selected='true'";
		}
		szOut+=">Depends on Field</option>";
		szOut+="</select>";
		chType.innerHTML =szOut;
		document.getElementById('charge_data').style.display='none'
		this.displayFieldPrice("");
	}
}

function __Frm_displayFieldPrice(val){
	var pS = document.getElementById("pricingStructure");
	if (pS+""!="null" && pS+""!="undefined" && pS+""!=""){
		if(pS.options[pS.options.selectedIndex].value==1){
			var szOut = "<p>How much does this form cost <input type='text' name='fixed_price' value='"+val+"'/></p>";
		} else {
			opts="";
			for(i=0;i<this.imported_fields.length;i++){
				if(
					(this.imported_fields[i][2] == "select") ||
					(this.imported_fields[i][2] == "list") ||
					(this.imported_fields[i][2] == "radio")
					){
					opts += "<option value='"+this.imported_fields[i][0]+"'>"+this.imported_fields[i][1]+"</option>";
				}
			}
			if(opts!=""){
				var szOut = "<p>Select a Field <select name=priceField id=priceField>"+opts+"</select> <input type=button class=bt id=retrieveFieldbtn value='Retrieve values' onclick='myFrm.retrievePriceFields()'/></p>";
			} else {
				var szOut = "<p>You have no field that will allow this option <input type=button class=bt id=retrieveFieldbtn value='Refresh' onclick='myFrm.displayFieldPrice()'/></p>";
			}
		}	
		var dOut = document.getElementById('charge_data');
		dOut.style.display	= '';
		dOut.innerHTML 		= szOut;
	}
}

function __Frm_retrievePriceFields(){
	dInput = document.getElementById("retrieveFieldbtn");
	dInput.disabled=true;
	dInput = document.getElementById("priceField");
	field = dInput.options[dInput.options.selectedIndex].value;
	dInput.disabled=true;
	identifier=-1;
	for(i=0; i < this.imported_fields.length; i++){
		if (this.imported_fields[i][0] == field){
			fielddata = this.imported_fields[i][this.imported_fields[i].length - 1];
		}
	}
	var fieldinfo = fielddata.split("::");
	cmd = fieldinfo[0]+"GET_FIELD_OPTIONS&identifier="+fieldinfo[1]+"&field="+field;
	this.exec_info('field_options',cmd);
	setTimeout("myFrm.get_fields('"+fielddata+"', 'fieldoptions');",2000);
}

function __Frm_updatePrice(el, list, fld){
	var dOut= document.getElementById('charge_data');
	dOut.style.display	 = '';
	if(list.length>0){
		var data = new String(list[0]).split("::");
		szOut 				 = "<input type='hidden' name='priceArray' value='"+data[0]+"'></input>";
	}
	szOut 				+= "<table>";
	ok =1;
	if (list.length==1){
		var data = new String(list[0]).split("::");
		if(data[0]=="__fixed__"){
			this.displayFieldPrice(data[2]);
			ok =0;
		}
	}
	if(ok==1){
		szOut 			+=		"<tr><th class='bt'>Option</th><th class='bt'>Price</th></tr>";
		for(i=0; i<list.length; i++){
			var data = new String(list[i]).split("::");
			if(data.length==2){
				data[2]="";
			}
			szOut 			+=		"<tr><td>"+data[1]+"</td><td><input type='text' name='"+data[0]+"[]' value='"+data[2]+"'><input type='hidden' name='label[]' value='"+data[1]+"'></td></tr>";
		}
		szOut 				+= "</table>";
		dOut.innerHTML 		 = szOut;
	}
}

function mysort(a,b){
	return a["rank"] - b["rank"];
}

/*************************************************************************************************************************
* __Frm_edit_from (integer index)
*************************************************************************************************************************/
function __Frm_edit_from(index){
	var doc = document.getElementById("questions");
	tmp_label	= this.used_fields[index]["label"];
	sz = "<div style='margin:10px;padding:10px;border:1px dashed #cccccc;display:inline;width:600px;vertical-align:top'>";
	var belongs = this.used_fields[index]["belongs"];
	
	loaded_index=-1;
	for(i=0;i<this.loaded_modules.length;i++){
		if(belongs+"" == this.loaded_modules[i]["value"]+""){
			loaded_index = i
			break;
		}
	}
	if(loaded_index!=-1){
		sz = "<div style='vertical-align:top'><strong>Field imported from "+this.loaded_modules[loaded_index]["module"]+"";
		if(this.loaded_modules[loaded_index]["section"]!=''){
			sz+= " subsection "+this.loaded_modules[loaded_index]["section"]+"";
		}
		sz+= "</strong></div>";
	}
	sz+= "<div style='width:260px;display:inline;vertical-align:top'><label for='field_label_entry'>Define the label </label><input type='text' value='"+tmp_label+"' name='field_label_entry' id='field_label_entry' style='width:100%'><br />";
	sz+= "<label for='field_required_entry'>Is this field to be required </label><input type='checkbox' value='1' name='field_required_entry' id='field_required_entry'";
	if	((this.used_fields[index]["required"]=="yes") || (this.used_fields[index]["required"]=="1")){
		sz+=" checked";
	}
	sz+="><br/>";
	sz+= "<a style='float:right' href='javascript:myFrm.edit_from_save("+index+");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Update</a></div><div id='mergemapinfo' style='width:200px;display:inline;padding:5px;'>";
	szpause = "Modules that will inherit this data<br /><table style='width:100%'><tr><td class='bt'>Module</td><td class='bt'>Field</td></tr>";
	szpause_list=""
	for(i=0;i<this.merged_fields.length;i++){
		if(this.merged_fields[i][3]+""!=""){
			if(this.imported_fields[this.merged_fields[i][3]][1]!=""){
//				alert("found @ "+i+" :: "+this.merged_fields[i][3])
//				alert(belongs +" => "+this.merged_fields[i][0]+"::"+this.merged_fields[i][1]+"::"+this.merged_fields[i][2]+"::"+this.merged_fields[i][3]+"::"+this.merged_fields[i][4]+"::"+this.merged_fields[i][5]+"::"+this.merged_fields[i][6]+"::"+this.merged_fields[i][7]+"::"+this.merged_fields[i][8])
				if(this.merged_fields[i][7] == belongs){
					if(this.merged_fields[i][0] == this.used_fields[index]["name"]){
						szpause_list = this.get_module_map(i,8,1);
					}
				} else if(this.merged_fields[i][8] == belongs){
					if(this.merged_fields[i][1] == this.used_fields[index]["name"]){
						szpause_list = this.get_module_map(i,7,0);
					}
				}
				//ie_title::contact_company::34::5::text::text::2::INFORMATIONADMIN_::30879719026753181::CONTACT_::-1
			}
		}	
	}
	if(szpause_list!=""){
		sz+=szpause+szpause_list+"</table>";
	}
	sz+="</div></div>";
	doc.innerHTML= sz;
}

function __Frm_get_module_map(index, primary, label){
	var sz="";
	for(z=0;z<this.loaded_modules.length;z++){
		if (this.merged_fields[index][primary] == this.loaded_modules[z]["value"]){
			if(this.loaded_modules[z]["section"]==""){
				sz += "<tr><td>"+this.loaded_modules[z]["module"]+"</td><td>"+this.get_field(this.merged_fields[index][label])+"</td></tr>";
			}else{
				sz += "<tr><td>"+this.loaded_modules[z]["section"]+"</td><td>"+this.get_field(this.merged_fields[index][label])+"</td></tr>";
			}
		}
	}	
	return sz;
}

function __Frm_get_field(fld){
	for (var i=0; i<this.imported_fields.length;i++){
		if (this.imported_fields[i][0] == fld){
			return this.imported_fields[i][1];
		}
	}
}
/*************************************************************************************************************************
* __Frm_edit_from_save (integer index)
*************************************************************************************************************************/
function __Frm_edit_from_save(index){
	var f= get_form();
	this.used_fields[index]["label"] = f.field_label_entry.value;
	if(f.field_required_entry.checked){
		this.used_fields[index]["required"] = "yes";
	} else {
		this.used_fields[index]["required"] = "no";
	}
	var doc = document.getElementById("questions");
	doc.innerHTML= "";
	this.render();
}

/*************************************************************************************************************************
*
*************************************************************************************************************************/
function __Frm_draw_merge(){	
	field_length = this.loaded_modules.length;
	if(field_length<2){
		szOut ="<p>You must import at least two modules before merging can take place</p>";
	} else {
	szOut	= "<label for='flist'>Define merged fields</label><div id='merged_settings'><p>Select a module work with <select name='import_module_selector'>";
	szOut  += "<option value=''>Select Field to work with</option>";
	for(index=0;index<field_length;index++){
		if(this.loaded_modules[index]["value"]!="FBA::-1"){
			if(this.loaded_modules[index]["section"]==""){
				szOut  += "<option value='"+this.loaded_modules[index]["value"]+"'>"+this.loaded_modules[index]["module"]+"</option>";
			} else {
				szOut  += "<option value='"+this.loaded_modules[index]["value"]+"'>"+this.loaded_modules[index]["module"]+", "+this.loaded_modules[index]["section"]+"</option>";
			}
		}
	}
	szOut  += "</select> <input type='button' onclick='myFrm.select_mods()' class='bt' value='Select Module' ></p>";
	szOut  += "<p id='display_field_list' style='display:none'>Select a field to merge <select name='import_field_selector'>";
	szOut  += "<option value=''>Select Field to work with</option>";
	szOut  += "</select> <input type='button' onclick='myFrm.select_merge()' class='bt' value='Select' ></p><p id='display_field_select' style='display:none'></p></div>";
/*
	for(i=0;i<this.merged_fields.length;i++){
		if(this.imported_fields[this.merged_fields[i][3]][1]!=""){
			szOut	+= "<li><strong>"+this.imported_fields[this.merged_fields[i][2]][1]+"</strong> auto mapping to <strong>"+this.imported_fields[this.merged_fields[i][3]][1]+"</strong></li>";
		}
	}	
*/
	}
	dOut	 	   = document.getElementById("mergeded_field_div");
	dOut.innerHTML = szOut;
}
/*************************************************************************************************************************
* 
		myFrm.used_fields[pos] = Array()
		myFrm.used_fields[pos]["name"]		= list[0];
		myFrm.used_fields[pos]["belongs"]	= list[5]+"::"+list[6];
		myFrm.used_fields[pos]["label"]		= list[1];
		myFrm.used_fields[pos]["labelpos"]	= list[7];
		myFrm.used_fields[pos]["type"] 		= list[2];
		myFrm.used_fields[pos]["rank"] 		= list[8];
		myFrm.used_fields[pos]["required"]	= (list[9]==0?"no":"yes");
 contact_email::Email::email::::ie_email::yes::belongs
 1				2		3	 4		5		6	7	
*************************************************************************************************************************/
function __Frm_select_merge(){
	var f = get_form();
	v = f.import_field_selector.options[f.import_field_selector.selectedIndex].value;
	if(v==""){
		return "";
	}
	d = document.getElementById("display_field_select");
	var module	= this.imported_fields[v][this.imported_fields[v].length-1];
	var field	=  this.imported_fields[v][0];
	var label	=  this.imported_fields[v][1];
	var type	=  this.imported_fields[v][2];

	sz ="what field(s) should inherit this value <br><table><tr><td class='bt'>&nbsp;</td><td class='bt'>Field</td></tr>";
	
	field_length = this.loaded_modules.length;
	prev=-2;
	for(index=0;index<field_length;index++){
		this.loaded_modules[index]["used"] = 0;
	}
	for(i = 0; i < this.merged_fields.length; i++){
		if(this.merged_fields[i][4] != "hidden" && this.merged_fields[i][4] !="password" && this.merged_fields[i][4] !="__category__" ) {
			for(index=0;index < field_length; index++){
				if (this.loaded_modules[index]["value"] == this.merged_fields[i][7]){
					this.loaded_modules[index]["used"]=1;
				}
			}
		}
	}	
	for(i=0;i<this.imported_fields.length;i++){
		if(this.imported_fields[i][6] != module && this.imported_fields[i][2]!="hidden" && this.imported_fields[i][2]!="password" && this.imported_fields[i][2]!="__category__"){
			loaded = -1;
			for(index=0;index<field_length;index++){
				if(this.loaded_modules[index]["value"]==this.imported_fields[i][6]){
					loaded=index;
					if(this.loaded_modules[index]["section"]==""){
						szOut  = "";//this.loaded_modules[index]["module"];
					} else {
						szOut  = this.loaded_modules[index]["section"];
					}
				}	
			}
			used=0;
			for(index=0;index<this.merged_fields.length;index++){
				if(this.merged_fields[index][6] == 1){
					if(this.merged_fields[index][0] == field){
						if(this.merged_fields[index][1]== this.imported_fields[i][0]){
							used=1;
						}
					}
				}
			}
			for(index=0;index<this.merged_fields.length;index++){
				if(this.merged_fields[index][6] == 1){
					if(this.merged_fields[index][7] == module){
						if(this.merged_fields[index][1]== this.imported_fields[i][0]){
							this.loaded_modules[loaded]["used"] = 1;
						}
					}
				}
			}
			if(prev!=loaded){
				if(this.loaded_modules[loaded]["section"]!=""){
					sz += "<tr><td colspan=2><strong>"+ this.loaded_modules[loaded]["module"] +", "+this.loaded_modules[loaded]["section"]+"</strong></td></tr>";
					prev = loaded;
				} else {
					sz += "<tr><td colspan=2><strong>"+this.loaded_modules[loaded]["module"]+"</strong></td></tr>";
					prev = loaded;
				}
				sz += "<tr><td><input type=radio name=field_select_"+loaded+" value='' id=field_select_"+loaded+"_-1";
				if(this.loaded_modules[loaded]["used"] == 0){
					sz += " checked";
				}
				sz += "></td><td><label for=field_select_"+loaded+"_-1>None defined</label></td></tr>";
			}
			if(type == "select"){
				sz += "<tr><td><input type=radio name=field_select_"+loaded+" value="+i+" id=field_select_"+loaded+"_"+i;
				if(used==1){
					sz += " checked";
				}
				sz += "></td><td><label for=field_select_"+loaded+"_"+i+">"+this.imported_fields[i][1]+"</label></td></tr>";
			} else {
				if(this.imported_fields[i][2] == "select"){
					sz += "<tr><td><input type=radio name=field_select_"+loaded+" value="+i+" id=field_select_"+loaded+"_"+i;
					if(used==1){
						sz += " checked";
					}
					sz += "></td><td><label for=field_select_"+loaded+"_"+i+">"+this.imported_fields[i][1]+" (overridden by this field)</label></td></tr>";
				} else {
					sz += "<tr><td><input type=radio name=field_select_"+loaded+" value="+i+" id=field_select_"+loaded+"_"+i;
					if(used==1){
						sz += " checked";
					}
					sz += "></td><td><label for=field_select_"+loaded+"_"+i+">"+this.imported_fields[i][1]+"</label></td></tr>";
				}
			}
		}
	}
	sz+="</table><input type=button class='bt' value=Update onclick='javascript:myFrm.update_merge();'>";
//	alert(sz);
	d.innerHTML = sz
	d.style.display='';
}
/*************************************************************************************************************************
 myFrm.used_fields[pos]["name"]		= list[0];
 myFrm.used_fields[pos]["belongs"]	= list[5]+"::"+list[6];
 myFrm.used_fields[pos]["label"]		= list[1];
 myFrm.used_fields[pos]["labelpos"]	= list[7];
 myFrm.used_fields[pos]["type"] 		= list[2];
 myFrm.used_fields[pos]["rank"] 		= list[8];
 myFrm.used_fields[pos]["required"]	= (list[9]==0?"no":"yes");
*************************************************************************************************************************/
function __Frm_select_mods(){
	var f = get_form();
	v = f.import_module_selector.options[f.import_module_selector.selectedIndex].value;
	if(v != ""){
		var cell = document.getElementById("display_field_list");
		cell.style.display = '';
		field_length = this.imported_fields.length;
		f.import_field_selector.options.length=1;
		for(index=0;index<field_length;index++){
//			if(this.imported_fields[i][6] != module && this.imported_fields[i][2]!="hidden" && this.imported_fields[i][2]!="password" && this.imported_fields[i][2]!="__category__"){
			if(this.imported_fields[index][6] == v && this.imported_fields[index][2]!="hidden" && this.imported_fields[index][2]!="password" && this.imported_fields[index][2]!="__category__"){
				ok=1;
/*				for(oIndex=0;oIndex<this.used_fields.length;oIndex++){
					if(this.imported_fields[index][this.imported_fields[index].length-1] == this.used_fields[oIndex]["belongs"]){
						if(this.imported_fields[index][0] == this.used_fields[oIndex]["name"]){
							ok=0;
							break;
						}
					}
				}
				*/
				if(ok==1){
					f.import_field_selector.options[f.import_field_selector.options.length] = new Option(this.imported_fields[index][1], index);
				}
			}
		}
	}
//	alert("merged fields length "+this.merged_fields.length);
}

/*************************************************************************************************************************
* 
*************************************************************************************************************************/
function __Frm_update_merge(){
	var f = get_form();
	v = f.import_field_selector.options[f.import_field_selector.selectedIndex].value;
	d = document.getElementById("display_field_select");
	var module	= this.imported_fields[v][this.imported_fields[v].length-1];
	var field	=  this.imported_fields[v][0];
	var label	=  this.imported_fields[v][1];
	var type	=  this.imported_fields[v][2];
	
	for(index=0;index<this.loaded_modules.length;index++){
		el = f.elements["field_select_"+index];
		if(el+""!="undefined"){
//			alert("field_select_"+index+" = "+el.length+" "+this.loaded_modules[index]["value"]);
			if(el.length+""=="undefined"){
//				alert("el.name	 = "+el.name);
//				alert("el.length = "+el.length);
//				alert("el.value	 = "+el.value);
//				alert("found :: "+this.imported_fields[el.value][1]);
				if(type == "select"){
					winner=1;
				} else {
					if(this.imported_fields[v][2] == "select"){
						winner=2;
					} else {
						winner=1;
					}
				}
				this.merged_fields[this.merged_fields.length] = Array(
					this.imported_fields[v][0],
					this.imported_fields[el.value][0],
					v,el.value,
					this.imported_fields[v][2],
					this.imported_fields[el.value][2], 
					winner, 
					this.imported_fields[v][this.imported_fields[v].length - 1], 
					this.imported_fields[el.value][this.imported_fields[el.value].length - 1]
				);	
			} else {
				for(i=0;i<el.length;i++){
					if(el[i].value!=''){
						if(el[i].checked){
							if(type == "select"){
								winner = 1;
							} else {
								if(this.imported_fields[v][2] == "select"){
									winner = 2;
								} else {
									winner = 1;
								}
							}
							this.merged_fields[this.merged_fields.length] = Array(
								this.imported_fields[v][0],
								this.imported_fields[el[i].value][0],
								v,el[i].value,
								this.imported_fields[v][2],
								this.imported_fields[el[i].value][2], 
								winner, 
								this.imported_fields[v][this.imported_fields[v].length - 1], 
								this.imported_fields[el[i].value][this.imported_fields[el[i].value].length - 1]
							);	
						}
					}
				}
			}
		}
	}
	this.draw();
	this.draw_merge();
}