/*************************************************************************************************************************
* Protect fields in the information directory (database)
*
*
*************************************************************************************************************************/
function GenerateProtection(_groups, _fields, _output){
	//
	this.output 				= _output;
	this.groups 				= _groups;
	this.fields 				= _fields;
	//
	this.display				= __gp_display_screen;
	this.print					= __gp_print;
	this.update_select_a_field	= __gp_update_select_a_field;
	this.protectField			= __gp_protectField;
	this.saveResults			= __gp_saveresults;
}

/**
* display the field protection form on the screen
*/
function __gp_display_screen(){
	var szScreen ="";
	szScreen += "<p>Select a field to set the protection on<br>";
	szScreen += "<select name='select_a_field' onchange='javascript:gp.update_select_a_field()'>";
	szScreen += "<option value='-1'>Select a Field</option>";
	for(var i=0; i<this.fields.length;i++){
		szScreen += "<option value='"+this.fields[i][0]+"'>"+LIBERTAS_GENERAL_unjtidy(this.fields[i][1])+"</option>";
	}
	szScreen += "</select>";
	szScreen += "</p><p id='fieldchoosen' style='display:none'>Select the groups to secure this field too (leave blank for anonymous access)<br>You have choose to update the <strong><span id=fieldname></span>&nbsp;</strong> field<br>";
	for(var i=0; i<this.groups.length;i++){
		szScreen += "<input type='checkbox' id='grp_"+this.groups[i][0]+"' value='"+this.groups[i][0]+"' onclick='javascript:gp.protectField("+this.groups[i][0]+")'><label for='grp_"+this.groups[i][0]+"'>"+LIBERTAS_GENERAL_unjtidy(this.groups[i][1])+"</label><br>";
	}
	szScreen += "</p><div id='saveresults'></div>";
	this.print(szScreen)
}

/**
* print the parameter to the output location
*/
function __gp_print(content_string){
	document.getElementById(this.output).innerHTML = new String(content_string);
}

/**
* update the screen 
*/
function __gp_update_select_a_field(){
	var f= get_form();
	index = f.select_a_field.selectedIndex - 1;
	el = document.getElementById("fieldchoosen");
	if(f.select_a_field.selectedIndex==0){
		el.style.display='none';
	} else {
		el.style.display='';
		el = document.getElementById("fieldname");
		el.innerHTML = f.select_a_field[f.select_a_field.selectedIndex].text;
		for(var g=0; g< this.groups.length;g++){
			el = document.getElementById("grp_"+this.groups[g][0]);
			el.checked=false;
			for(var findex=0; findex<this.fields[index][2].length; findex++){
				if (this.fields[index][2][findex] == this.groups[g][0]){
					el.checked=true;
				}
			}
		}
	}	
}

/**
* toggle the protection on a field for a specific group
*/
function __gp_protectField(group_identifier){
	var f= get_form();
	index = f.select_a_field.selectedIndex - 1;
	el = document.getElementById("grp_"+group_identifier);
	if(el.checked==true){
		this.fields[index][2][this.fields[index][2].length] = group_identifier;
	} else {
		found_index=-1;
		for(var findex=0; findex<this.fields[index][2].length; findex++){
			if (this.fields[index][2][findex]==group_identifier){
				found_index=findex;
			}
		}
		if(found_index!=-1){
			this.fields[index][2].splice(found_index,1);
		}
	}
	this.saveResults();
}
function __gp_saveresults(){
	var out ="";
	el = document.getElementById("saveresults");
	for(var index=0; index< this.fields.length;index++){
		for(var findex=0; findex<this.fields[index][2].length; findex++){
			out += "<input type=hidden name='"+this.fields[index][0]+"[]' value='"+this.fields[index][2][findex]+"'>";
		}
	}
	el.innerHTML= out;
}
