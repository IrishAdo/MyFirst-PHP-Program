/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- F O R M   B U I L D E R   J A V A S C R I P T   F I L E 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-
- Author: Adrian Sweeney
- Company: Libertas Solutions
- Copyright: 2003
- Created: 12th August 2003
-
- Description:-
-
- This is the javascript for the form builder module it will allow you do to the following 
-
- 1. Build up an XML representation of form,
- 2. Preview is form as you work,
- 3. Edit the form Quickly and easily
- 4. Move the field up and down the list
- 5. Allow the form to have dirrent destinations
- 6. Use (Text, Textarea, Radio, Check Box and Select box) elements
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- No copying, modifing, reproducing without the consent of Libertas Solutions
- Libertas Solutions does not supply any warranty with the use of this software.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	Modified $Date: 2004/08/25 07:34:36 $
-	$Revision: 1.2 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	Configuration options
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	var SHOW_SESSION_HIDE 	= 0;
	var SHOW_REQUIRED_FIELD = 1;

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	form object
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function form_data(){
	// define data variables for object
	this.form_fields	= new Array();
	this.form_label		= "";
	this.form_emails	= new Array();
	this.form_action	= "";
	this.form_identifier= "";
	// define method for object
	this.add			= __form_add_field;
	this.remove_field	= __remove_field;
	this.build			= __form_build;
	this.build_XML		= __form_build_XML;
	this.hide_email		= __hide_email;
	this.show_email		= __show_email;
	this.add_email		= __add_email;
	this.remove_email	= __remove_email;
	this.move_email		= __move_email;
}
/*
	form methods
*/
function __form_add_field(){
//	myWin = window.open("formbuilder_fieldwizard.html","myPopup");
//	myWin.focus();
next_page(0);
}

function __remove_field(remove_index){
	pos = this.form_fields.length
	if (pos>1){
		for (index = remove_index; index< pos-1 ; index++){
			this.form_fields[index].clone(this.form_fields[index+1]);
		}
		this.form_fields.length--;
	} else {
		this.form_fields.length=0;
	}
	this.build();
}

function __form_add_field(name, type, label, field_property, isPropertyArray, layout_direction, options, isRequired, session_hide){
	pos = this.form_fields.length;
	this.form_fields[pos] = new form_field(type);
	this.form_fields[pos].name				= name;
	this.form_fields[pos].label 			= label;
	this.form_fields[pos].field_property	= field_property;
	this.form_fields[pos].isPropertyArray	= isPropertyArray;
	this.form_fields[pos].layout_direction	= layout_direction;
	this.form_fields[pos].options			= options;
	this.form_fields[pos].isRequired 		= isRequired;
	this.form_fields[pos].session_hide		= session_hide;
}
function __form_edit_field(index){
//	myWin = window.open("formbuilder_fieldwizard.html?edit="+index,"myPopup");
	//myWin.__recieve_field(buildform.form_fields[index]);
//	myWin.focus();
	
	next_page((index+1) * -1);
	show_tabular_screen('tab_2');
}
function __hide_email(){
	ok=true;
	if (this.form_emails.length!=0){
		ok = confirm(LOCALE_DESTROY_EMAILS);
	}
	if (ok){
		this.form_emails = new Array();
		document.all.list_of_emails.innerHTML="";
	}
}
function __show_email(){
	str  = '<table>';
	str += '	<tr><td class="tableheader">'+LOCALE_SUBJECT_LINE+'</td><td class="tableheader">'+LOCALE_EMAIL_ADDRESS+'</td><td class="tableheader">'+LOCALE_DEFAULT+'</td></tr>';
	for (var i=0; i<this.form_emails.length;i++){
		str += '	<tr><td>'+this.form_emails[i][0]+'</td><td>'+this.form_emails[i][1]+'</td>';
		str += '<td><input type=radio name="field_default_option" onclick="javascript:set_default_email();"';
		if (this.form_emails[i][2]){
			str += ' checked';
		}
		str += '></td>';
		str += '<td><input class="bt" type="button" value="'+LOCALE_REMOVE+'" onclick="javascript:buildform.remove_email('+i+')"/></td>';
		if (i!=0){
			str += '<td><input class="bt" type="button" value="'+LOCALE_UP+'" onclick="javascript:buildform.move_email('+i+','+(i-1)+')"/></td>';
		} else {
			str += '<td>&#160;</td>';
		}
		if (i!=this.form_emails.length-1){
			str += '<td><input class="bt" type="button" value="'+LOCALE_DOWN+'" onclick="javascript:buildform.move_email('+i+','+(i+1)+')"/></td>';
		} else {
			str += '<td>&#160;</td>';
		}
		str += '</tr>';
	}
	str += '	<tr><td><input type="text" name="option_label"></td><td><input type="text" name="option_value"></td><td><input class="bt" type="button" onclick="javascript:buildform.add_email();" value="'+LOCALE_ADD_EMAIL+'"></td></tr>';
	str += '</table>';
	document.all.list_of_emails.innerHTML=str;
}
function __add_email(){
	var pos = this.form_emails.length
	var keys = new Array("\\","{","}","[","]",";","'","#",":","~",",","/","<",">","?","!","\"","£","$","%","^","&","*","(",")");
	var ok = true;
	if (pos==0){
		bool 	= true;
	}else{
		bool	= false;
	}
	for (var index=0; index<keys.length;index++){
		if (document.user_form.option_label.value.indexOf(keys[index])!=-1){
			ok = false;
			fail = 1;
		}
	}
	for (var index=0; index<keys.length;index++){
		if (document.user_form.option_value.value.indexOf(keys[index])!=-1){
			ok = false;
			fail = 1;
		}
	}
	if (ok){
		at_symbol_position = document.user_form.option_value.value.indexOf("@");
		dot_symbol_position = document.user_form.option_value.value.indexOf(".",at_symbol_position);
		if (at_symbol_position==-1 || dot_symbol_position==-1){
			ok = false;
			fail = 2;
		}
	}
	if (ok){
		this.form_emails[pos] = new Array(
			document.user_form.option_label.value,
			document.user_form.option_value.value,
			bool
		);
		this.show_email();
		this.build();
	} else {
		if (fail==1){
			alert(LOCALE_SORRY_CHARACTERS+keys.join(""));
		} else {
			alert(LOCALE_SORRY_EMAIL_INVALID);
		}
	}
}
function __remove_email(remove_index){
	pos = this.form_emails.length
	for (index = remove_index; index< pos-1 ; index++){
		if (this.form_emails[index][2]){
			this.form_emails[index] = new Array(
				this.form_emails[index+1][0],
				this.form_emails[index+1][1],
				true
			);
		} else {
			field.field_property[index] = new Array(
				this.form_emails[index+1][0],
				this.form_emails[index+1][1],
				this.form_emails[index+1][2]
			);
		}
	}
	this.form_emails.length--;
	this.show_email();
	this.build();
}
function __move_email(from, to){
	if (from<to){
		start	= from;
		finish	= to;
		pos = +1;
	} else {
		start	= to;
		finish	= from;
		pos = +1;
	}
	temp = new Array(this.form_emails[start][0], this.form_emails[start][1], this.form_emails[start][2]);
//	alert(start+" "+finish+" "+temp[0] +" "+pos);
	for (index = start; index< finish ; index++){
		this.form_emails[index] = new Array(
			this.form_emails[index+pos][0],
			this.form_emails[index+pos][1],
			this.form_emails[index+pos][2]
		);
	}
//	alert(finish);
	this.form_emails[finish] = new Array(
		temp[0],
		temp[1],
		temp[2]
	);
	this.show_email();
}

function __form_build(){
	max_fields = this.form_fields.length;
	str = "";
	count_required=0;
	counter_email = this.form_emails.length;
	if (counter_email>1){
		str +="<tr ><td valign='top' class='display_element'><label class='display_element' >"+LOCALE_SUBJECT+"</label></td><td valign='top' class='display_element'>";
		str +="<select name='form_subject'>";
		for (index in this.form_emails){
		str += "<option";
		if (this.form_emails[index][2]){
			str += " selected";
		}
		str += ">"+this.form_emails[index][0]+"</option>";
		}
		str +="</select>";
		str +="</td></tr>";
	}
	var i = 0;
	for (field_name in this.form_fields){
		required ="";
		
		if (this.form_fields[field_name].isRequired){
			required = "<span class='required'>*</span>";
			count_required ++;
		}
		str +="<tr ><td valign='top' class='display_element'><label class='display_element' >"+this.form_fields[field_name].label+"</label>"+required+"</td><td valign='top' class='display_element'>";
		if (this.form_fields[field_name].type=="text"){
			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
			str +="<input class='display_element' type='text' size='"+width+"' maxlength='255' border='0' class='form_element'>";
		}
		if (this.form_fields[field_name].type=="textarea"){
			str +="<textarea class='display_element' cols='"+this.form_fields[field_name].field_property["width"]+"' rows='"+this.form_fields[field_name].field_property["height"]+"'></textarea>";
		}
		if (this.form_fields[field_name].type=="radio"){
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					str += '	<input type=radio class="display_element" id="field'+field_name+'_'+index+'" name="field'+field_name+'" value='+info[1];
					if (info[2]==true){
						str += ' checked';
					}
					str += '>&#160;<label class="display_element" for="field'+field_name+'_'+index+'">'+info[0]+'</label>';
					if (this.form_fields[field_name].layout_direction=="vertical"){
						str += '<br/>';
					} else {
						str += '&#160;';
					}
				}
			}
			str +="";
		}
		if (this.form_fields[field_name].type=="checkbox"){
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					str += '	<input class="display_element" type=checkbox name="field'+field_name+'" id="field'+field_name+'_'+index+'" value='+info[1];
					if (info[2]==true){
						str += ' checked';
					}
					str += '>&#160;<label class="display_element" for="field'+field_name+'_'+index+'">'+info[0]+'</label>';
					if (this.form_fields[field_name].layout_direction=="vertical"){
						str += '<br/>';
					} else {
						str += '&#160;';
					}
				}
			}
			str +="";
		}
		if (this.form_fields[field_name].type=="select"){
			str += '	<select class="display_element" name=field'+field_name+'>';
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					 str += '<option value='+info[1];
					if (info[2]==true){
						str += ' selected';
					}
					str += '>'+info[0]+'</option>';
				}
			}
			str +="</select>";
		}
		str +="</td><td valign='top' width='10'><input class='bt' type=button value='"+LOCALE_EDIT+"' onclick='javscript:__form_edit_field("+field_name+")'></td>";
		str +="<td valign='top' width='10'><input class='bt' type=button value='"+LOCALE_REMOVE+"' onclick='javscript:buildform.remove_field("+field_name+")'></td>";
		if (i!=0){
			str += '<td valign="top" width="10"><input class="bt" type="button" value="'+LOCALE_UP+'" onclick="javascript:__form_move_option('+i+','+(i-1)+')"/></td>';
		} else {
			str += '<td width="10">&#160;</td>';
		}
		if (i!=this.form_fields.length-1){
			str += '<td valign="top" width="10"><input class="bt" type="button" value="'+LOCALE_DOWN+'" onclick="javascript:__form_move_option('+i+','+(i+1)+')"/></td>';
		} else {
			str += '<td width="10">&#160;</td>';
		}

		str +="</tr>";
		i++;
	}
	if (count_required>0){
		str= "<tr><td colspan='2'>"+LOCALE_REQUIRED_MSG+"</td></tr>"+str;
	}
	document.all.myform.innerHTML = "<table width='100%'>"+str+"</table>";

}
function __form_build_XML(){
	max_fields 		= this.form_fields.length;
	str 			= "";
	group_list		= new String();
	required_list	= new String();
	
	str +="<input type='hidden' ";
	str +=" name='number_of_fields'";
	str +=" value='"+this.form_fields.length+"'";
	str +="/>\n";
	for (field_name in this.form_fields){
		if (this.form_fields[field_name].type=="text"){
			str +="<xforms:input ref='"+this.form_fields[field_name].name+"' model='form_builder_"+this.form_identifier+"'>\n";
			str +="\t<xforms:label>"+this.form_fields[field_name].label+"</xform:label>\n";
/*			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
			str +=" size='"+width+"'";
			if (this.form_fields[field_name].isRequired){
				str +=" required='"+this.form_fields[field_name].isRequired+"'";
			}
			if (this.form_fields[field_name].session_hide){
				str +=" session_hide='"+this.form_fields[field_name].session_hide+"'";
			}
			str +="/>\n";
			*/
			str +="</xforms:input>\n";
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="textarea"){
			str +="<textarea size='"+this.form_fields[field_name].field_property["width"]+"' height='"+this.form_fields[field_name].field_property["height"]+"'";
			str +=" label='"+this.form_fields[field_name].label+"'";
			str +=" name='"+this.form_fields[field_name].name+"'";
			if (this.form_fields[field_name].isRequired){
				str +=" required='"+this.form_fields[field_name].isRequired+"'";
			}
			if (this.form_fields[field_name].session_hide){
				str +=" session_hide='"+this.form_fields[field_name].session_hide+"'";
			}
			str += '></textarea>\n';
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="radio"){
			str +="<xforms:select1 ref='"+this.form_fields[field_name].name+"' model='form_builder_"+this.form_identifier+"'>\n";
			str +="\t<xforms:label>"+this.form_fields[field_name].label+"</xform:label>\n";
/*			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
			str +=" size='"+width+"'";
			if (this.form_fields[field_name].isRequired){
				str +=" required='"+this.form_fields[field_name].isRequired+"'";
			}
			if (this.form_fields[field_name].session_hide){
				str +=" session_hide='"+this.form_fields[field_name].session_hide+"'";
			}
			str +="/>\n";
			*/
			str +="</xforms:select1>\n";
			str += '<radio';
			str += " label='"+this.form_fields[field_name].label+"'";
			str += " type='"+this.form_fields[field_name].layout_direction+"'"; 
			if (this.form_fields[field_name].isRequired){
				str +=" required='"+this.form_fields[field_name].isRequired+"'";
			}
			if (this.form_fields[field_name].session_hide){
				str +=" session_hide='"+this.form_fields[field_name].session_hide+"'";
			}
			str += " name='"+this.form_fields[field_name].name+"'>\n";
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					str +="<option value='"+info[1]+"'";
					if (info[2]==true){
						str += " checked='true'";
					}
					str+= '><![CDATA['+info[0]+']]></option>\n';
				}
			}
			str +="</radio>\n";
			if (group_list.length!=0){
				group_list+=",";
			}
			group_list +=""+field_name;
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="checkbox"){
			str += '<checkboxes type="'+this.form_fields[field_name].layout_direction+'" label="'+this.form_fields[field_name].label+'" name="'+this.form_fields[field_name].name+'"';
			if (this.form_fields[field_name].isRequired){
				str +=" required='"+this.form_fields[field_name].isRequired+"'";
			}
			if (this.form_fields[field_name].session_hide){
				str +=" session_hide='"+this.form_fields[field_name].session_hide+"'";
			}
			str += '>\n';
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					str +="<option value='"+info[1]+"'";
					if (info[2]==true){
						str += " checked='true'";
					}
					str+= '><![CDATA['+info[0]+']]></option>\n';
				}
			}
			str +="</checkboxes>\n";
			if (group_list.length!=0){
				group_list+=",";
			}
			group_list +=""+field_name;
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="select"){
			str += '	<select ';
			str += " label='"+this.form_fields[field_name].label+"'";
			if (this.form_fields[field_name].session_hide){
				str +=" session_hide='"+this.form_fields[field_name].session_hide+"'";
			}
			str += ' name="field'+field_name+'">\n';
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					str +="<option value='"+info[1]+"'";
					if (info[2]==true){
						str += " checked='true'";
					}
					str+= '><![CDATA['+info[0]+']]></option>\n';
				}
			}
			str +="</select>\n";
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
			}
		}
		str +="";
	}
	alert(str);
	xml_destination.value = str;
	str="";
	if (this.form_emails.length!=0 && this.form_action!=2){
		c=0;
		for (index in this.form_emails){
			if (c!=0){
				str += "@@";
			}
			c++;
			str += this.form_emails[index][0]+":"+this.form_emails[index][1]+":"+this.form_emails[index][2];
		}
	} else {
		str="";
	}
	
	document.user_form.email_list.value = str;
	document.user_form.group_fields.value= group_list;
	document.user_form.required_fields.value= required_list;
//	document.user_form.submit();
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- field object
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function form_field(field_type){
	// define data variables for object
	this.name					= "";
	this.type 					= field_type;
	this.label 					= "";
	this.field_property			= new Array();
	this.isPropertyArray		= false;
	this.layout_direction		= "vertical";
	this.options				= new Array();
	this.isRequired 			= false;
	this.session_hide			= false;
	// define methods for object
	this.clone 					= __field_clone;
	this.clone_field_property	= __clone_field_property;
}
/*
	field methods
*/
function __field_clone(data){
	this.name				= data.name;
	this.type 				= data.type;
	this.label 				= data.label;
	this.options			= data.options;
	this.isPropertyArray	= data.isPropertyArray;
	this.layout_direction	= data.layout_direction;
	this.isRequired 		= data.isRequired;
	this.session_hide		= data.session_hide;
	this.clone_field_property(data.field_property, this.isPropertyArray);
}

function __clone_field_property(info, bool){
	for (property in info){
		if (bool){
			this.field_property[property] = new Array();
			for (index in info[property]){
				this.field_property[property][index] = info[property][index];
			}
		}else{
			this.field_property[property] = info[property];
		}
	}
}
/*
	call this function from the wizard popup to add a defined field into the list of fields
*/
function __return_field(data){
	if (data.name==""){
		pos = buildform.form_fields.length;
	} else {
		pos = data.name.toString().substr(5);
	}
	buildform.form_fields[pos] = new form_field();
	buildform.form_fields[pos].clone(data);
	buildform.form_fields[pos].name="field"+pos;
	buildform.build();
}	

function __recieve_field(index){
	return buildform.form_fields[index];
}


/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Wizard Functions
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function next_page(page){
	str="";
	ignore_previous_form = false;
	if (page<0){
			field = new form_field("");
			field.clone(buildform.form_fields[(page * -1)-1]);
			page=2;
			ignore_previous_form = true;
	}
	if (page==0){
		field = new form_field("")
		page=1;
	}
	if (page==1){
		str  = '<h1>'+LOCALE_WIZARD_PAGE+' 1</h1>';
		str += LOCALE_MSG_ADD_FIELD;
		str += '<form name="wizard_frm">';
		str += '<input type="hidden" name="page" value="1">';
		str += '<p><img src="/libertas_images/editor/formbuilder/images/text.gif" border="0"/> <input type="radio" id="typeText" name="typeSelected" value="text"';
		if (field.type=='text' || field.type=='' ){
			str += ' checked';
		}
		str += '/> <label for="typeText">'+LOCALE_MSG_ADD_TEXT_LABEL+'</label><br />\n';
		str += '<img src="/libertas_images/editor/formbuilder/images/textarea.gif" border="0"/> <input type="radio" id="typeTextArea" name="typeSelected" value="textarea"';
		if (field.type=='textarea'){
			str += ' checked';
		}
		str += '/> <label for="typeTextArea">'+LOCALE_MSG_ADD_TEXTAREA_LABEL+'</label><br />\n';
		str += '<img src="/libertas_images/editor/formbuilder/images/radio.gif" border="0"/> <input type="radio" id="typeRadio" name="typeSelected" value="radio"';
		if (field.type=='radio'){
			str += ' checked';
		}
		str += '/> <label for="typeRadio">'+LOCALE_MSG_ADD_RADIO_LABEL+'</label><br />\n';
		str += '<img src="/libertas_images/editor/formbuilder/images/checkbox.gif" border="0"/> <input type="radio" id="typeCheckBox" name="typeSelected" value="checkbox"';
		if (field.type=='checkbox'){
			str += ' checked';
		}
		str += '/> <label for="typeCheckBox">'+LOCALE_MSG_ADD_CHECK_LABEL+'</label><br />\n';
		str += '<img src="/libertas_images/editor/formbuilder/images/select.gif" border="0"/> <input type="radio" id="typeSelect" name="typeSelected" value="select"';
		if (field.type=='select'){
			str += ' checked';
		}
		str += '/> <label for="typeSelect">'+LOCALE_MSG_ADD_SELECT_LABEL+'</label><br />\n';
		str += '<input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
		str += '</form>';
		document.all.wizard.innerHTML = str;
	}	
	if(page==2){
		if (ignore_previous_form == false){
			if (document.wizard_frm.typeSelected+'' != 'undefined'){
				l = document.wizard_frm.typeSelected.length;
				for(i=0;i<l;i++){
					if (document.wizard_frm.typeSelected[i].checked){
						fieldType = document.wizard_frm.typeSelected[i].value;
					}		
				}
				field.type = fieldType;
			}
		} else {
			ignore_previous_form = false;
		}
		str  = '<h1>'+LOCALE_WIZARD_PAGE+' 2</h1>';
//		alert(field.type);
		switch (field.type){
			case "text":
				if (field.field_property["width"]+"" == 'undefined'){
					field.field_property["width"]="20";
				}
				str += LOCALE_MSG_ADD_TEXT_BOX;
				str += '<form name="wizard_frm"><table>';
				str += '<input type="hidden" name="page" value="2">';
				str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+field.label+'"/></td></tr>';
				str += '<tr><td valign="top"><label for="field_field_property"><strong>'+LOCALE_INPUT_SPECIAL+'</strong></label> </td><td valign="top">';
				str += LOCALE_INPUT_WIDTH+': <input type="text" name=field_width size=3 maxlength=3 value="'+field.field_property['width']+'">';
				str += '</td></tr>';
				/*
				str += '<tr><td valign="top"><label for="field_field_property"><strong>'+LOCALE_INPUT_SPECIAL+'</strong></label> </td><td valign="top"><select id="field_field_property" name="field_field_property">';
				str += '<option value=""';
				if (field.field_property==""){
					str += ' selected'
				}
				str += '>'+LOCALE_INPUT_SPECIAL_NO+'</option>';
				str += '<option value="EMAIL_ADDRESS"';
				if (field.field_property=="EMAIL_ADDRESS"){
					str += ' selected'
				}
				str += '>'+LOCALE_INPUT_SPECIAL_EMAIL+'</option>';
				str += '<option value="PASSWORD"';
				if (field.field_property=="PASSWORD"){
					str += ' selected'
				}
				str += '>'+LOCALE_INPUT_SPECIAL_PWD+'</option>';
				str += '</select></td></tr>';
				*/
				if (SHOW_REQUIRED_FIELD==1){
					str += '<tr><td valign="top"><strong><label for="required_yes">'+LOCALE_INPUT_REQUIRED+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="required_yes" name="isRequired" value="YES" ';
					if (field.isRequired){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
				}
				if (SHOW_SESSION_HIDE==1){
					str += '<tr><td valign="top"><strong><label for="session_yes">'+LOCALE_INPUT_SESSION_HIDE+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="session_yes" name="isSession" value="YES" ';
					if (field.session_hide){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
				}
				str += '</table>';
				str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
				str += '</form>';
				document.all.wizard.innerHTML = str;
				break;
			case "textarea":
				if (field.field_property["width"]+"" == 'undefined'){
					field.field_property["width"]="";
				}
				if (field.field_property["height"]+"" == 'undefined'){
					field.field_property["height"]="";
				}
				str += LOCALE_INPUT_TEXTAREA_BOX;
				str += '<form name="wizard_frm"><table>';
				str += '<input type="hidden" name="page" value="2">';
				str += '<table>';
				str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+field.label+'"/></td></tr>';
				str += '<tr><td valign="top"><label for="field_field_property"><strong>'+LOCALE_INPUT_SPECIAL+'</strong></label> </td><td valign="top">';
				str += LOCALE_INPUT_WIDTH+': <input type="text" name=field_width size=3 maxlength=3 value="'+field.field_property['width']+'"> <br>';
				str += LOCALE_INPUT_HEIGHT+': <input type="text" name=field_height size=3 maxlength=3 value="'+field.field_property['height']+'"> <br>';
				str += '</td></tr>';
				if (SHOW_REQUIRED_FIELD==1){
					str += '<tr><td valign="top"><strong><label for="required_yes">'+LOCALE_INPUT_REQUIRED+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="required_yes" name="isRequired" value="YES" ';
					if (field.isRequired){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
				}
				if (SHOW_SESSION_HIDE==1){
					str += '<tr><td valign="top"><strong><label for="session_yes">'+LOCALE_INPUT_SESSION_HIDE+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="session_yes" name="isSession" value="YES" ';
					if (field.session_hide){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
				}
				str += '</table>';
				str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
				str += '</form>';
				document.all.wizard.innerHTML = str;
				break;
			case "radio":
				field.field_property["width"]=null;
				field.field_property["height"]=null;
				str += LOCALE_INPUT_RADIO_BOX;
				str += '<form name="wizard_frm"><table>';
				str += '<input type="hidden" name="page" value="2">';
				str += '<table>';
				str += '<tr><td valign="top"><label for="field_label_id"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label_id" name="field_label" maxlength="255" size="50" value="'+field.label+'"/></td></tr>';
				if (SHOW_REQUIRED_FIELD==1){
					str += '<tr><td valign="top"><strong><label for="required_yes">'+LOCALE_INPUT_REQUIRED+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="required_yes" name="isRequired" value="YES" ';
					if (field.isRequired){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
				}
				if (SHOW_SESSION_HIDE==1){
					str += '<tr><td valign="top"><strong><label for="session_yes">'+LOCALE_INPUT_SESSION_HIDE+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="session_yes" name="isSession" value="YES" ';
					if (field.session_hide){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
				}
				str += '<tr><td valign="top"><label for="field_layout"><strong>'+LOCALE_INPUT_LAYOUT_DIR+'</strong></label> </td><td valign="top"><input type="radio" id="field_layout_vertical" name="field_layout_direction"  value="vertical"';
				if (field.layout_direction=="vertical"){
					str += ' checked';
				}
				str += '/> <label for="field_layout_vertical">'+LOCALE_INPUT_VERTICAL+'</label> <input type="radio" id="field_layout_horizontal" name="field_layout_direction"  value="horizontal"';
				if (field.layout_direction=="horizontal"){
					str += ' checked';
				}
				str += '/> <label for="field_layout_horizontal">'+LOCALE_INPUT_HORIZONTAL+'</label> </td></tr>';
				str += '<tr><td colspan="2" valign="top"><label for="field_label"><strong>'+LOCALE_INPUT_OPTIONS+'</strong></label></td></tr>';
				str += '<tr><td colspan="2" valign="top"><span id ="display_options"></span></td></tr>';
				str += '</table>';
				str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
				str += '</form>';
				document.all.wizard.innerHTML = str;
				show_options();
				break;
			case "checkbox":
				field.field_property["width"]=null;
				field.field_property["height"]=null;
				str += LOCALE_INPUT_CHECK_BOX;
				str += '<form name="wizard_frm"><table>';
				str += '<input type="hidden" name="page" value="2">';
				str += '<table>';
				str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+field.label+'"/></td></tr>';
				if (SHOW_REQUIRED_FIELD==1){
					str += '<tr><td valign="top"><strong><label for="required_yes">'+LOCALE_INPUT_REQUIRED+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="required_yes" name="isRequired" value="YES" ';
					if (field.isRequired){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
				}
				if (SHOW_SESSION_HIDE==1){
					str += '<tr><td valign="top"><strong><label for="session_yes">'+LOCALE_INPUT_SESSION_HIDE+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="session_yes" name="isSession" value="YES" ';
					if (field.session_hide){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
				}
				str += '<tr><td valign="top"><label for="field_layout"><strong>'+LOCALE_INPUT_LAYOUT_DIR+'</strong></label> </td><td valign="top"><input type="radio" id="field_layout_vertical" name="field_layout_direction"  value="vertical"';
				if (field.layout_direction == "vertical" || field.layout_direction+'' == "undefined"){
					str += ' checked';
				}
				str += '/> <label for="field_layout_vertical">'+LOCALE_INPUT_VERTICAL+'</label> <input type="radio" id="field_layout_horizonal" name="field_layout_direction"  value="horizontal"';
				if (field.layout_direction == "horizontal"){
					str += ' checked';
				}
				str += '/> <label for="field_layout_horizonal">'+LOCALE_INPUT_HORIZONTAL+'</label> </td></tr>';
				str += '<tr><td colspan="2" valign="top"><label for="field_label"><strong>'+LOCALE_INPUT_OPTIONS+'</strong></label></td></tr>';
				str += '<tr><td colspan="2" valign="top"><span id ="display_options"></span></td></tr>';
				str += '</table>';
				str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
				str += '</form>';
				document.all.wizard.innerHTML = str;
				show_options();
				break;
			case "select":
				field.field_property["width"]=null;
				field.field_property["height"]=null;
				str += LOCALE_INPUT_SELECT_BOX;
				str += '<form name="wizard_frm"><table>';
				str += '<input type="hidden" name="page" value="2">';
				str += '<table>';
				str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+field.label+'"/></td></tr>';
				if (SHOW_REQUIRED_FIELD==1){
					str += '<tr><td valign="top"><strong><label for="required_yes">'+LOCALE_INPUT_REQUIRED+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="required_yes" name="isRequired" value="YES" ';
					if (field.isRequired){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
				}
				if (SHOW_SESSION_HIDE==1){
					str += '<tr><td valign="top"><strong><label for="session_yes">'+LOCALE_INPUT_SESSION_HIDE+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="session_yes" name="isSession" value="YES" ';
					if (field.session_hide){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
				}
				str += '<tr><td colspan="2" valign="top"><label for="field_label"><strong>'+LOCALE_INPUT_OPTIONS+'</strong></label></td></tr>';
				str += '<tr><td colspan="2" valign="top"><span id ="display_options"></span></td></tr>';
				str += '</table>';
				str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
				str += '</form>';
				document.all.wizard.innerHTML = str;
				show_options();
				break;
			default:
//				alert("sorry that is an unknown");
		}
	}
	if(page==3){
		field.label 							= jtidy(document.wizard_frm.field_label.value);
		switch (field.type){
			case "text":
				field.isPropertyArray 			= false;
				field.field_property["width"]	= jtidy(document.wizard_frm.field_width.value);
				if (document.wizard_frm.field_field_property+"" !="undefined")
					field.field_property[0] 		= document.wizard_frm.field_field_property.options[document.wizard_frm.field_field_property.selectedIndex].value;
				if (document.wizard_frm.isRequired+"" !="undefined")
					field.isRequired 				= document.wizard_frm.isRequired.checked;
				else 
					field.isRequired 				= false;
				if (document.wizard_frm.isSession+"" !="undefined")
					field.session_hide				= document.wizard_frm.isSession.checked;
				else 
					field.session_hide 				= false;
				break;
			case "textarea":
				field.isPropertyArray 			= false;
				field.field_property["width"]	= jtidy(document.wizard_frm.field_width.value);
				field.field_property["height"]	= jtidy(document.wizard_frm.field_height.value);
				if (document.wizard_frm.isRequired+"" !="undefined")
					field.isRequired 				= document.wizard_frm.isRequired.checked;
				else 
					field.isRequired 				= false;
				if (document.wizard_frm.isSession+"" !="undefined")
					field.session_hide				= document.wizard_frm.isSession.checked;
				else 
					field.session_hide 				= false;
				break;
			case "radio":
				field.isPropertyArray 				= true;
				if (document.wizard_frm.field_layout_direction[0].checked)
					field.layout_direction			= document.wizard_frm.field_layout_direction[0].value;
				if (document.wizard_frm.field_layout_direction[1].checked)
					field.layout_direction			= document.wizard_frm.field_layout_direction[1].value;
				if (document.wizard_frm.isRequired+"" !="undefined")
					field.isRequired 				= document.wizard_frm.isRequired.checked;
				else 
					field.isRequired 				= false;
				if (document.wizard_frm.isSession+"" !="undefined")
					field.session_hide				= document.wizard_frm.isSession.checked;
				else 
					field.session_hide 				= false;
				break;
			case "checkbox":
				field.isPropertyArray 				= true;
				if (document.wizard_frm.field_layout_direction[0].checked)
					field.layout_direction			= document.wizard_frm.field_layout_direction[0].value;
				if (document.wizard_frm.field_layout_direction[1].checked)
					field.layout_direction			= document.wizard_frm.field_layout_direction[1].value;
				if (document.wizard_frm.isRequired+"" !="undefined")
					field.isRequired 				= document.wizard_frm.isRequired.checked;
				else 
					field.isRequired 				= false;
				if (document.wizard_frm.isSession+"" !="undefined")
					field.session_hide				= document.wizard_frm.isSession.checked;
				else 
					field.session_hide 				= false;
				break;
			case "select":
				field.isPropertyArray 				= true;
				if (document.wizard_frm.isRequired+"" !="undefined")
					field.isRequired 				= document.wizard_frm.isRequired.checked;
				else 
					field.isRequired 				= false;
				if (document.wizard_frm.isSession+"" !="undefined")
					field.session_hide				= document.wizard_frm.isSession.checked;
				else 
					field.session_hide 				= false;
				break;
			default:
		}
//		field.field_property = document.wizard_frm.field_field_property.options[document.wizard_frm.field_field_property.selectedIndex].value;
		str  = '<h1>Form builder field wizard page 3</h1>';
		str += '<form name="wizard_frm"><table>';
		str += '<input type="hidden" name="page" value="3">';
		str += '<p>'+LOCALE_FIELD_THANKS+'</p>';
		str += '<p><input class="bt" type="button" value="'+LOCALE_BACK+'" onclick="javascript:next_page(0);"/><input class="bt" type="button" value="Finish" onclick="javascript:show_tabular_screen(\'tab_3\');"/></p>';
		str += '</form>';
		document.all.wizard.innerHTML = str;
		__return_field(field);
	}
	if(page==4){
//		window.opener.__return_field(field);
		next_page(0);
//		window.close();
	}
}
/*
	When a user is adding options to a form element (select, radio, checkbox) these functions will allow the user to manage the data.
*/

function show_options(){
	str  = '<table>';
	str += '	<tr><td>'+LOCALE_INPUT_LABEL+'</td><td>'+LOCALE_INPUT_VALUE+'</td><td>'+LOCALE_DEFAULT+'</td></tr>';
	for (i=0; i<field.field_property.length;i++){
		str += '	<tr><td>'+field.field_property[i][0]+'</td><td>'+field.field_property[i][1]+'</td>';
		if(field.type=="radio"){
			str += '<td><input type=radio name="field_default_option" onclick="javascript:set_default();"';
			if (field.field_property[i][2]){
				str += ' checked';
			}
			str += '></td>';
		}
		if(field.type=="checkbox"){
			str += '<td><input type=radio name="field_default_option'+i+'" onclick="javascript:set_default('+i+');"';
			if (field.field_property[i][2]){
				str += ' checked';
			}
			str += '> Yes <input type=radio name="field_default_option'+i+'" onclick="javascript:set_default('+i+');"';
			if (!field.field_property[i][2]){
				str += ' checked';
			}
			str += '> No ';
			str += '</td>';
		}
		str += '<td><input class="bt" type="button" value="'+LOCALE_REMOVE+'" onclick="javascript:remove_option('+i+')"/></td>';
		if (i!=0){
			str += '<td><input class="bt" type="button" value="'+LOCALE_UP+'" onclick="javascript:move_option('+i+','+(i-1)+')"/></td>';
		} else {
			str += '<td>&#160;</td>';
		}
		if (i!=field.field_property.length-1){
			str += '<td><input class="bt" type="button" value="'+LOCALE_DOWN+'" onclick="javascript:move_option('+i+','+(i+1)+')"/></td>';
		} else {
			str += '<td>&#160;</td>';
		}
		str += '</tr>';
	}
	str += '	<tr><td><input type="text" maxlength="255" size="15" name="option_label"></td><td><input maxlength="255" size="15" type="text" name="option_value" onclick="javascript:copy_label();" onfocus="javascript:copy_label();" onblur="javascript:copy_label();"></td><td><input class="bt" type=button onclick="javascript:add_option();" value="'+LOCALE_INPUT_ADD_OPTION+'"></td></tr>';
	str += '</table>';
	document.all.display_options.innerHTML = str;
}
function copy_label(){
	if (document.wizard_frm.option_value.value.length==0){
		document.wizard_frm.option_value.value = document.wizard_frm.option_label.value;
	}
}
function add_option(){
	pos = field.field_property.length
	if (pos==0 && field.type=="radio"){
		bool 	= true;
	}else{
		bool	= false;
	}
	if (document.wizard_frm.option_value.value.length==0){
		document.wizard_frm.option_value.value = document.wizard_frm.option_label.value;
	}

	field.field_property[pos] = new Array(
		jtidy(document.wizard_frm.option_label.value),
		jtidy(document.wizard_frm.option_value.value),
		bool
	);
	show_options();
}
function remove_option(remove_index){
	pos = field.field_property.length
	for (index = remove_index; index< pos-1 ; index++){
		if (field.field_property[index][2]){
			field.field_property[index] = new Array(
				field.field_property[index+1][0],
				field.field_property[index+1][1],
				true
			);
		} else {
			field.field_property[index] = new Array(
				field.field_property[index+1][0],
				field.field_property[index+1][1],
				field.field_property[index+1][2]
			);
		}
	}
	field.field_property.length--;
	show_options();
}

function set_default(index){
	if (index+'' == 'undefined'){
		for (i=0; i< document.wizard_frm.field_default_option.length; i++){
			if ((document.wizard_frm.field_default_option[i].checked+''!='') && (document.wizard_frm.field_default_option[i].checked+'' !='undefined') && (document.wizard_frm.field_default_option[i].checked+'' =='true')){
				field.field_property[i][2]=true;
			} else {
				field.field_property[i][2]=false;
			}
		}
	} else {
		eval('form_element = document.wizard_frm.field_default_option'+index+';');
		if (form_element[0].checked){
			field.field_property[index][2]=true;
		} else {
			field.field_property[index][2]=false;
		}
	}
}

function jtidy(str){
	pos  = str.indexOf("&");
	while (pos !=-1){
		str = str.substring(0,pos)+"&amp"+str.substring(pos+1);
		pos  = str.indexOf("&", pos+4);
	}
	find = new Array(
				new Array("'","&#39;"),
				new Array("<","&#60;"),
				new Array(">","&#62;"),
				new Array('"',"&#34;"),
				new Array('€',"&euro;")
			);
	for(index=0;index<find.length;index++){
		while (str.indexOf(find[index][0])!=-1){
			str = str.replace(find[index][0],find[index][1]);
		}
	}
	return str;
}
/*
 radio, checkboxes buttons horizontal and vertical 
*/

function submission_group(x,y){
	f = document.user_form.submission;
	for (var index=0; index<f.length; index++){
		if (f[index].checked){
			if (f[index].value==0 || f[index].value==1){
				buildform.show_email();
			} else {
				buildform.hide_email();
			}
		}
	}
}

function set_default_email(index){
	if (index+'' == 'undefined'){
		for (i=0; i< document.user_form.field_default_option.length; i++){
			if ((document.user_form.field_default_option[i].checked+''!='') && (document.user_form.field_default_option[i].checked+'' !='undefined') && (document.user_form.field_default_option[i].checked+'' =='true')){
				buildform.form_emails[i][2]=true;
			} else {
				buildform.form_emails[i][2]=false;
			}
		}
	} else {
		eval('form_element = document.user_form.field_default_option'+index+';');
		if (form_element[0].checked){
			buildform.form_emails[index][2]=true;
		} else {
			buildform.form_emails[index][2]=false;
		}
	}
	buildform.build();
}

function __form_move_option(from, to){
	if (from<to){
		start	= from;
		finish	= to;
		pos = +1;
	} else {
		start	= to;
		finish	= from;
		pos = +1;
	}
	temp = new form_field();
	temp.clone(buildform.form_fields[start]);
	for (var index = start; index< finish ; index++){
		buildform.form_fields[index] = new form_field();
		buildform.form_fields[index].clone(buildform.form_fields[index+1]);
	}
	buildform.form_fields[finish] = new form_field();
	buildform.form_fields[finish].clone(temp);
	buildform.build();
}
function move_option(from, to){
	if (from<to){
		start	= from;
		finish	= to;
		pos = +1;
	} else {
		start	= to;
		finish	= from;
		pos = +1;
	}
	temp = new Array(field.field_property[start][0], field.field_property[start][1], field.field_property[start][2]);
//	alert(start+" "+finish+" "+temp[0] +" "+pos);
	for (index = start; index< finish ; index++){
		field.field_property[index] = new Array(
			field.field_property[index+pos][0],
			field.field_property[index+pos][1],
			field.field_property[index+pos][2]
		);
	}
//	alert(finish);
	field.field_property[finish] = new Array(
		temp[0],
		temp[1],
		temp[2]
	);
	show_options();
}

function layout_associate(){
	
	f = document.user_form;
	PATH = 'file_associate.php?command=LAYOUT_RETRIEVE_LIST_MENU_OPTIONS&amp;page_menu_locations='+f.trans_menu_locations.value+"&amp;"+session_url;
	my_preview_window = window.open(PATH,'PREVIEW_WINDOW','scrollbars=yes,resizable=yes,width=750,height=550');
	my_preview_window.focus();
}

function layout_save_to_doc(list, description, g_list, g_descriptions){
	window.opener.document.user_form.trans_menu_locations.value		= list;
	window.opener.document.all.trans_menu_location.innerHTML			= description;
	
	window.close();
}

