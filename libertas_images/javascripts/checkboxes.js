/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- C H E C K B O X   F U N C T I O N S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-				$Revision: 1.2 $, 
-				$Date: 2004/07/31 13:34:12 $
-				$Author: aldur $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Variable:
-	hlist is a list of Menus containing (0 => identifiers, 1 => parent, 2 => selected);
- 
- Functions:
- 	FN menu_location_toggle()
-		this function will toggle the checkboxes and is the main function called on click of checkbox
- 	FN toggle_setting()
-		Recurse through menu and change settings as needed
- 	FN toggle_inheritance_group()
- 		on change of inheritance grouping status we need to make changes to the html enabling checkboxes etc.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function menu_checkbox(){
	// properties
	this.hlist 						= new Array();
	this.code						= "cml_";
	
	// methods
	this.menu_location_toggle		= __mcb_menu_location_toggle;
	this.toggle_setting				= __mcb_toggle_setting;
	this.menu_location_update		= __mcb_menu_location_update;
	this.enable_all					= __mcb_enable_all;
	this.shownumber					= __mcb_show_number;
}

function __mcb_menu_location_toggle(identifier,t){
	f = get_form();
	fieldvalue	="";
	if (this.code=="ecml_"){
		fieldvalue	=	"extract";
	} 
	action="disable";
	for (var i =0; i < this.hlist.length ; i++){
		if (this.hlist[i][0]==identifier){
			if (this.hlist[i][2]==1){
				action = "enable";
				this.hlist[i][2]=0;
			} else {
				action = "disable";
				this.hlist[i][2]=1;
			}
		}
	}
	if (f.elements[fieldvalue+'set_inheritance'].type=="hidden"){
	
	}else{
		check = f.elements[fieldvalue+'set_inheritance'][0].checked;
		this.toggle_setting(identifier,action,check);
	}
}

function __mcb_toggle_setting(parent_identifier,action,inherit){
	if (inherit){
		for (var i =0; i < this.hlist.length ; i++){
			if (this.hlist[i][1]==parent_identifier){
				cml = document.getElementById(this.code+this.hlist[i][0]);
				if (action == "enable"){
					this.hlist[i][2] = 0;
					try{
						cml.disabled=false;
						cml.checked=false;
					} catch (e){
					}
				} else {
					this.hlist[i][2] = 1;
					try{
						cml.checked=true;
						cml.disabled=true;
					} catch (e){
					}
				}
				this.toggle_setting(this.hlist[i][0],action,inherit);
			}
		}
	} else {
		for (var i =0; i < this.hlist.length ; i++){
			cml = document.getElementById(this.code+this.hlist[i][0]);
			if (cml+"" !="undefined"){
				try{
					this.hlist[i][2] = cml.checked;
					cml.disabled=false;
				} catch(e){
				}
			}
		}
	}
}

function toggle_inheritance_group(t,s){
	var val = t.name.split("set_inheritance");
	if (val[0]==""){
		fieldvalue	="";
	}else{
		fieldvalue	=	"extract";
	} 
	try{
		eval("var workinglist = hlist"+fieldvalue+"menu_locations"); 
		var f = get_form();
		check = f.elements[fieldvalue+'set_inheritance'][0].checked;
		if (check){
			for (var i =0; i < workinglist.hlist.length ; i++){
				if(workinglist.hlist[i][2]){
					action = "disable";
					workinglist.toggle_setting(workinglist.hlist[i][0],action,check);
				}
			}							
		} else {
			for (var i =0; i < workinglist.hlist.length ; i++){
				cml = document.getElementById(workinglist.code+workinglist.hlist[i][0]);
				cml.disabled=false;
			}
		}
	} catch(e){
	}
}
// if editing object then 
function __mcb_menu_location_update(){
	fieldvalue	="";
	if (this.code=="ecml_"){
		fieldvalue	=	"extract";
	} 
	var f = get_form();
	try{
		if (f.elements[fieldvalue+'set_inheritance'].type=="hidden"){
		
		}else{
			check = f.elements[fieldvalue+'set_inheritance'][0].checked;
			for (var i =0; i < this.hlist.length ; i++){
				if(this.hlist[i][2]){
					action = "disable";
					this.toggle_setting(this.hlist[i][0],action,check);
				}
			}
		}
	} catch(e){
	}		
}

function menu_locations_group(t,s){
	val = t.name.split("all_locations");
	fieldvalue="";
	if (val.length>0){
		fieldvalue = val[0];
	}
	el1 = document.getElementById('hidden_'+fieldvalue+'menu_locations_label');
	el2 = document.getElementById('hidden_'+fieldvalue+'menu_locations');
	el3 = document.getElementById('hidden_'+fieldvalue+'set_inheritance_label');
	el4 = document.getElementById('hidden_'+fieldvalue+'set_inheritance');
		
	if(t.value==1){
		el1.style.display='none';
		el2.style.display='none';
	}else{
		el1.style.display='';
		el2.style.display='';
	}
	if (el3+""!='undefined'){
		if(t.value==1){
			el3.style.display='none';
			el4.style.display='none';
		}else{
			el3.style.display='';
			el4.style.display='';
		}
	}
}
function __mcb_enable_all(){
	for (var i =0; i < this.hlist.length ; i++){
		cml = document.getElementById(this.code+this.hlist[i][0]);
		try{
			cml.disabled=false;
		} catch(e){
		
		}
	}
}

function __mcb_show_number(bool){
	for (var i =0; i < this.hlist.length ; i++){
		var myitem = document.getElementById(this.code+this.hlist[i][0]+"numbercell");
		if(bool){
			myitem.style.display='';
		} else {
			myitem.style.display='none';
		}
	}

}

function manageMirrorTypes(t,s){
	myvalue = t.options[t.selectedIndex].value;
	fieldvalue = "extract";
	if (myvalue==0){
		btn = document.getElementById("section_button_extractlocations_btn");
		btn.style.display='none';
		toggle_hidden("extractlocations",0);
	} else {
		toggle_hidden("extractlocations",1);
		btn = document.getElementById("section_button_extractlocations_btn");
		btn.style.display='';
		if (myvalue==2){
			hlistextractmenu_locations.shownumber(true);
			btn = document.getElementById("hidden_rss_number_of_items_label");
			btn.style.display='none';
			btn = document.getElementById("hidden_rss_number_of_items");
			btn.style.display='none';
		} else {
			hlistextractmenu_locations.shownumber(false);
			btn = document.getElementById("hidden_rss_number_of_items_label");
			btn.style.display='';
			btn = document.getElementById("hidden_rss_number_of_items");
			btn.style.display='';
		}
	}
}
