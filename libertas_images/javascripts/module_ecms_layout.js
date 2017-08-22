/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Javascript to manage the menu navigation in page add/edit
	- written by Adrian Sweeney
	- Date crreated : 1st June 2004
	- Current $Revision: 1.2 $
	- Last updated $Date: 2004/07/31 13:34:17 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function ecms_menu(){
	this.list 			=	Array();
	this.disabledlist	=	Array();
	this.can_add_menu	=	'0';
	
	this.display		= 	__ecms_menu_display;
	this.add			= 	__ecms_menu_add;
	this.remove			= 	__ecms_menu_remove;
	this.get_parent		=	__ecms_menu_parent;
	this.tidy			=	__ecms_menu_tidy;
	this.check			=	__ecms_menu_check;
}

function __ecms_menu_display(){
	var f = get_form();
	var doc = document.getElementById("selection_data");
	var sz		= "";
	var num_sz	= "";
	
	for(var i=0; i < this.list.length ; i++){
		if((i % 2) == 0){
			color ="#ebebeb";
		} else {
			color="#ffffff";
		}
		sz 		+= "<li style='margin:0px;padding:3px;list-style-type:none;width:100%;display:block;background:"+color+"'><div style='display:inline;width:500px;'>"+this.list[i][1]+"</div><div style='width:200px;display:inline;text-align:right;'><a href='javascript:menu_data.remove("+i+");'>Remove</a></div></li>";
		num_sz	+= " "+this.list[i][0]+",";
	}
	tml = f.trans_menu_location;
	for (var i=0; i<tml.options.length;i++){
		for (var z=0; z<this.disabledlist.length;z++){
			if (tml.options[i].value==this.disabledlist[z][0]+","+this.disabledlist[z][1]){
				tml.options[i].style.color="#cccccc";
			}
		}
	}
	doc.innerHTML = sz;
	if(this.can_add_menu=='1'){
		menudoc = document.getElementById("add_new_menu");
		if (f.show_add_menu.value!=0){
			if(menudoc.style.display!=''){
				szfrm ='<ul><div style="vertical-align:top;display:block"><div style="display:inline;width:200px;">Parent for new location<br/><select style="width:250px" name="new_menu_parent">';
				for (var i =0;i<f.trans_menu_location.options.length;i++){
					val = f.trans_menu_location.options[i].value.split(",");
					txt = f.trans_menu_location.options[i].text;
					if (f.trans_menu_location.options[i].style.color=='#cccccc') {
						style='style="color:#cccccc"';
					} else {
						if (this.disabledlist.length!=0 && val+""=="-1,-1"){
							style='style="color:#cccccc"';
						} else {
							style='';
						}
					}
					if (val+""=="-1,-1"){
						txt = "Root of Site";
					} else {
						txt = "&#160;&#160;&#160;"+txt;
					}
					szfrm += '<option value="'+val+'" '+style+'>'+txt+'</option>';
				}
				szfrm+='</select></div><div style="display:inline;width:300px">Label for new location <br><input type="text" name="new_menu_label" size=50 maxlength=255></div>';
				szfrm+='<div style="display:inline;width:100px;padding-left:10px"><a href="javascript:menu_data.remove(\'-1\');">Remove</a></div></div></ul>';
				menudoc.innerHTML 		= szfrm;
				menudoc.style.display	= '';
			} 
		} else {
			menudoc.style.display='none';
		}
	}
	f.trans_menu_locations.value = num_sz;
}

function __ecms_menu_add(){
	var f= get_form();
	var tml = f.trans_menu_location;
	if (tml.selectedIndex!=0){
		if (tml.options[tml.selectedIndex].value=="-1,-1"){
			if (f.show_add_menu.value==0){
				f.show_add_menu.value=1;
				this.display();
			} else {
				alert("Sorry you can only add one new menu location per page");
			}
		} else {
			ok=1;
			for (var z=0; z<this.disabledlist.length;z++){
				if (tml.options[tml.selectedIndex].value==this.disabledlist[z][0]+","+this.disabledlist[z][1]){
					ok = 0;
					alert("Sorry you do not have permission to publish to this location");
				}
			}
			if (ok==1){
				var menu_option = tml.options[tml.selectedIndex].value.split(",");
				var menu_id = menu_option[0];
				var menu_parent = menu_option[1];
				var found=false;
				for(var i=0; i < this.list.length ; i++){
					if(this.list[i][0]+"" == menu_id+""){
						found=true;
					}
				}
				if(!found){
					if (menu_parent==-1){
						bc_label = "&#187; "+this.tidy(tml.options[tml.selectedIndex].text);
					} else {
						bc_label = "&#187; "+this.get_parent(menu_parent)+" &#187; "+this.tidy(tml.options[tml.selectedIndex].text);
					}
					this.list[this.list.length] = new Array(menu_id, bc_label);
					this.display();
				} else {
					alert("You have already selected that location");
				}
				tml.selectedIndex=0;
			}
		}
	}
}

function __ecms_menu_remove(index){
	if (index==-1){
		var f = get_form();
		f.show_add_menu.value=0;
	} else {
		this.list.splice(index,1);
	}
	this.display();
}

function __ecms_menu_parent(m_parent){
	var f = get_form();
	var tml = f.trans_menu_location;
	for(var i=0; i < tml.options.length ; i++){
		test = tml.options[i].value.split(",");
		if (test[0] == m_parent){
			if (test[1]==-1){
				return this.tidy(tml.options[i].text);
			} else {
				return this.get_parent(test[1]) +" &#187; "+this.tidy(tml.options[i].text);
			}
		}
	}
}

function __ecms_menu_tidy(str){
	var start	= 0;
	var out		= "";
	for (var i=0; i < str.length ; i++){
		if (start == 0){
			if(str.charCodeAt(i) < 127 && str.charCodeAt(i) > 47){
				out += str.charAt(i);
				start=1;
			}
		} else {
			out += str.charAt(i);
		}
	}
	return out;
}

function __ecms_menu_check(){
	var f = get_form();
	var tml = f.trans_menu_location;
	var p_menu_element = f.new_menu_parent;
	if (tml.options[tml.selectedIndex].style.color=="#cccccc"){
		if (f.trans_menu_locations.value.length==0){
			alert('Sorry you have selected a location to publish too, that you do not have access to, Please select a different location');
		}
		tml.selectedIndex=0;
		return false;
	}
	if(this.can_add_menu=='1'){
		if (f.show_add_menu.value!=0){
			if (p_menu_element+"" !="undefined"){
				if (p_menu_element.selectedIndex==0){
					alert('Sorry you have not selected a menu location the new menu location should be a child location of');
					return false;
				}
				if (p_menu_element.options[p_menu_element.selectedIndex].style.color=='#cccccc') {
					alert("Sorry you can only add a new menu location to a location you have Administrative access to.")
					return false;
				} 	
				if (f.new_menu_label.value.length==0){
					alert("Sorry you have not supplied a menu label for the new location")
					return false;
				}
			}
		}
	}
	if (f.trans_menu_locations.value.length==0){
		if (tml.selectedIndex==0){
			var does_p_menu_element_exist = f.new_menu_parent;
			if(p_menu_element+""=="undefined"){
				return false;
			} else {
				if (p_menu_element.selectedIndex==0){
					alert('Sorry you have not selected a menu location the new menu location should be a child location of');
					return false;
				}
				if (p_menu_element.options[does_p_menu_element_exist.selectedIndex].style.color=='#cccccc') {
					alert("Sorry you can only add a new menu location to a location you have Administrative access to.")
					return false;
				} 	
				if (f.new_menu_label.value.length==0){
					alert("Sorry you have not supplied a menu label for the new location")
					return false;
				}
				return true;
			}
		} else {
			if(this.can_add_menu == '1'){
				if (p_menu_element.selectedIndex==0){
					alert('Sorry you have not selected a menu location the new menu location should be a child location of');
					return false;
				}
				if (p_menu_element.options[does_p_menu_element_exist.selectedIndex].style.color=='#cccccc') {
					alert("Sorry you can only add a new menu location to a location you have Administrative access to.")
					return false;
				} 	
				if (f.new_menu_label.value.length==0){
					alert("Sorry you have not supplied a menu label for the new location")
					return false;
				}
				return true;
			} else {
				return true;
			}
		}
	} else {
		return true;
	}
}

setTimeout("defineEcmMenu();",2000);
