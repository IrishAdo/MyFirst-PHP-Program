/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- F O R M   B U I L D E R   J A V A S C R I P T   F I L E 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-
- Author: Adrian Sweeney
- Company: Libertas Solutions
- Copyright: 2003
- Created: 12th August 2003
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- $Revision: 1.3 $, $Date: 2004/07/31 13:34:41 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Modifications: 1st Jan 2004 - 23rd Jan 2004
-	Added extra tabs and functionality (preview, Advanced, Confirm) and tidied up the layout of the form 
-	builder.
- Modified: 23rd Jan 2004
- 	Adding better property definitions to the form builder includes ability to specify that the field's 
-	values are to result in email target addresses.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
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

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- F O R M   O B J E C T 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
- this is the general object for the form builder
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function form_data(){
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Properties
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	this.form_fields			= new Array();
	this.form_label				= "";
	this.form_emails			= new Array();
	this.form_action			= "";
	this.form_url				= "";
	this.form_identifier		= "";
	this.form_edit_field_index	= -1;	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Methods
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	this.add					= __form_add_field;
	this.remove_field			= __remove_field;
	this.build					= __form_build_rank;
	this.preview				= __form_build_preview;
	this.build_XML				= __form_build_XML;
	this.hide_email				= __hide_email;
	this.show_email				= __show_email;
	this.show_email_option		= __show_email_option;
	this.show_url				= __show_url;
	this.add_email				= __add_email;
	this.remove_email			= __remove_email;
	this.move_email				= __move_email;
	this.editemail				= __edit_email;
	this.update_email			= __update_email;

}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- F O R M   O B J E C T   M E T H O D S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __form_add_field()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- this function is used by the system to reset the form wizard to the first screen.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __form_add_field(){
	next_page(0);
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __remove_field(remove_index)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- this function is to remove a field from the list of fields
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __remove_field(remove_index){
	pos = this.form_fields.length
	this.form_fields.splice(remove_index,1);
	this.build();
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __form_add_field(name, type, mylabel, field_property, isPropertyArray, layout_direction, options, 
-      isRequired, session_hide, val)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- this function is used to build the intial form structure 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __form_add_field(name, type, mylabel, field_property, isPropertyArray, layout_direction, options, isRequired, session_hide, val, attributes){
	pos = this.form_fields.length;
//	alert(type+" "+layout_direction);
	this.form_fields[pos] = new form_field(type);
	this.form_fields[pos].name				= name;
	this.form_fields[pos].fieldName			= name;	
	//this.form_fields[pos].fieldName			= "field"+pos;
	this.form_fields[pos].fieldValue		= val;
	this.form_fields[pos].label 			= mylabel;
	this.form_fields[pos].field_property	= field_property;
	this.form_fields[pos].isPropertyArray	= isPropertyArray;
	this.form_fields[pos].layout_direction	= layout_direction;
	this.form_fields[pos].options			= options;
	this.form_fields[pos].isRequired 		= isRequired;
	this.form_fields[pos].session_hide		= session_hide;
	this.form_fields[pos].attributes		= attributes;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __form_edit_field(searchFor)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- this function will extract the required field and call the proper screen of the wizard;
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __form_edit_field(searchFor){
	var index;
	//debug("SearchFor :: " + searchFor);
	for(var i=0;i<buildform.form_fields.length;i++){
//		alert(buildform.form_fields[i].fieldName +'=='+ searchFor);
		if (buildform.form_fields[i].name == searchFor){
			index=i;
			this.form_edit_field_index = index;
		}
	}
//	alert(index);
	next_page((index + 1 ) * -1);
	show_tabular_screen('tab_2');
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __hide_email()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- hide the email address options fromthe form builder as they have choosen not to send data via email
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __hide_email(){
	ok=true;
	if (this.form_emails.length!=0){
		ok = confirm(LOCALE_DESTROY_EMAILS);
	}
	if (ok){
		this.form_emails = new Array();
		this.form_url="";
//		document.all.list_of_emails.innerHTML="";
		LIBERTAS_GENERAL_printToId("list_of_emails","");
		LIBERTAS_GENERAL_disable_tab("emailmsgscreen");
		next_page(1);
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __show_url()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This form will be submitting to an exteranl (from the system) url allow the user to specify the url
-
- NOTE::
-
- Once this option is enabled extra fields appear in the form builder "Wizard page 2" allowing you to specify 
- the name of the field so that the recieving script will be able to determine which field is which.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __show_url(){
	
	if (this.form_emails.length!=0){
		ok = confirm(LOCALE_DESTROY_EMAILS);
	}
	else{
		ok = true;
	}
	if (ok){
		str  = '<table>';
		str += '	<tr><td class="TableHeader">'+LOCALE_SFORM_URL+'</td><td><input type="text" name="url" onchange="javascript:buildform.form_url=this.value" value="'+this.form_url+'"></td></tr>';
		str += '</table>';
//		document.all.list_of_emails.innerHTML=str;
		LIBERTAS_GENERAL_printToId("list_of_emails",str);
		if (this.form_identifier == "")
			wizard_frm.url.focus();
		//LIBERTAS_GENERAL_disable_tab("emailmsgscreen");
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __show_email_option(){
	next_page(1);
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __show_email()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Display the email form content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __show_email(index_to_edit){
	if (index_to_edit+''=='undefined'){
		index_to_edit=-1;
	}
	str  = '<table>';
	str += '	<tr><td class="bt">'+LOCALE_SUBJECT_LINE+'</td><td class="bt">'+LOCALE_EMAIL_ADDRESS+'</td><td class="bt" >'+LOCALE_DEFAULT+'</td><td class="bt" colspan="4">'+LOCALE_OPTIONS+'</td></tr>';
	for (var i=0; i<this.form_emails.length;i++){
		if (index_to_edit==i){
			str += '	<tr><td><input type="text" name="new_subject_line" value="'+this.form_emails[i][0]+'"/></td><td><input type="text" name="new_email_address" value="'+this.form_emails[i][1]+'"/></td>';
			field_format = document.wizard_frm.field_subject_format.options[document.wizard_frm.field_subject_format.selectedIndex].value
			if(field_format=="checkbox"){
				str += '<td><input type=radio name="field_default_option'+i+'" onclick="javascript:set_default_email('+i+');"';
				if (this.form_emails[i][2]){
					str += ' checked';
				}
				str += '> Yes <input type=radio name="field_default_option'+i+'" onclick="javascript:set_default_email('+i+');"';
				if (!this.form_emails[i][2]){
					str += ' checked';
				}
				str += '> No ';
				str += '</td>';
			} else {
				str += '<td><input type=radio name="field_default_option" onclick="javascript:set_default_email();"';
				if (this.form_emails[i][2]){
					str += ' checked';
				}
				str += '></td>';
			}
			str += '<td colspan="4"><input class="bt" type="button" value="'+LOCALE_UPDATE+'" onclick="javascript:buildform.update_email('+i+')"/></td>';
			str += '</tr>';		
		} else{
			str += '	<tr><td>'+this.form_emails[i][0]+'</td><td>'+this.form_emails[i][1]+'</td>';
			field_format = document.wizard_frm.field_subject_format.options[document.wizard_frm.field_subject_format.selectedIndex].value
			if(field_format=="checkbox"){
				str += '<td><input type=radio name="field_default_option'+i+'" onclick="javascript:set_default_email('+i+');"';
				if (this.form_emails[i][2]){
					str += ' checked';
				}
				str += '> Yes <input type=radio name="field_default_option'+i+'" onclick="javascript:set_default_email('+i+');"';
				if (!this.form_emails[i][2]){
					str += ' checked';
				}
				str += '> No ';
				str += '</td>';
			} else {
				str += '<td><input type=radio name="field_default_option" onclick="javascript:set_default_email();"';
				if (this.form_emails[i][2]){
					str += ' checked';
				}
				str += '></td>';
			}
			str += '<td><input class="bt" type="button" value="'+LOCALE_EDIT+'" onclick="javascript:buildform.editemail('+i+')"/></td>';
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
	}
	
	str += '	<tr><td colspan="7"><hr/></td></tr>';
	str += '	<tr><td><input type="text" name="email_option_label"></td><td><input type="text" name="email_option_value"></td><td><input class="bt" type="button" onclick="javascript:buildform.add_email();" value="'+LOCALE_ADD_EMAIL+'"></td></tr>';
	str += '</table>';
	this.form_url="";
	LIBERTAS_GENERAL_printToId("field_list_of_emails",str);
	LIBERTAS_GENERAL_enable_tab("emailmsgscreen");
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __add_email(){
	var pos = this.form_emails.length
	var keys = new Array("\\","{","}","[","]",";","'","#",":","~",",","/","<",">","?","!","\"","£","$","%","^","&","*","(",")");
	var ok = true;
	if (pos==0){
		bool 	= true;
	}else{
		bool	= false;
	}
	subject_line  = LIBERTAS_GENERAL_jtidy(wizard_frm.email_option_label.value);
	for (var index=0; index<keys.length;index++){
		if (wizard_frm.email_option_value.value.indexOf(keys[index])!=-1){
			ok = false;
			fail = 1;
		}
	}
	if (ok){
		at_symbol_position = wizard_frm.email_option_value.value.indexOf("@");
		dot_symbol_position = wizard_frm.email_option_value.value.indexOf(".",at_symbol_position);
		if (at_symbol_position==-1 || dot_symbol_position==-1){
			ok = false;
			fail = 2;
		}
	}
	if (ok){
		this.form_emails[pos] = new Array(
			wizard_frm.email_option_label.value,
			wizard_frm.email_option_value.value,
			bool
		);
		this.show_email();
		formRequiresReDraw = true;
		previewRequiresReDraw = true;
//		this.build();
	} else {
		if (fail==1){
			alert(LOCALE_SORRY_CHARACTERS+keys.join(""));
		} else {
			alert(LOCALE_SORRY_EMAIL_INVALID);
		}
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __remove_email(remove_index){
	pos = this.form_emails.length
	setting = this.form_emails[remove_index][2];
	if (remove_index < pos-1){
		this.form_emails.splice(remove_index,1);
		if (setting)
			this.form_emails[remove_index][2] = setting;
	} else {
		this.form_emails.splice(remove_index,1);
		if (setting && remove_index>0)
			this.form_emails[remove_index][2] = setting;
	}

	this.show_email();
		formRequiresReDraw = true;
		previewRequiresReDraw = true;
//	this.build();
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
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
	for (index = start; index< finish ; index++){
		this.form_emails[index] = new Array(
			this.form_emails[index+pos][0],
			this.form_emails[index+pos][1],
			this.form_emails[index+pos][2]
		);
	}
	this.form_emails[finish] = new Array(
		temp[0],
		temp[1],
		temp[2]
	);
	this.show_email();
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __form_build_rank()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will build the page rank display allowing an administrator to see the order that the fields 
- are in and a general look of the form.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __form_build_rank(f, t){

	if (f+'' == 'undefined'){
		f=-1;
	}
	if (t+'' == 'undefined'){
		t=-1;
	}
	max_fields = this.form_fields.length;
	str = "";
	count_required=0;
	var i = 0;
	var positioncell = 0;
	for (field_name in this.form_fields){
		if ((f==-1 || f == positioncell) || (t==-1 || t==positioncell)){
			required ="";
			if (this.form_fields[field_name].isRequired){
				required = "<div class='required'>*</div>";
				count_required ++;
			}
			if (f==-1 || t==-1){
				str+= "<div id='rankcell"+positioncell+"' class='width100percent'>";
			}
			if ((this.form_fields[field_name].type=="row_splitter") || (this.form_fields[field_name].type=="splitter") || (this.form_fields[field_name].type=="cdata")){
				str +="<div class='display_element_wide'>";
			} else {
				label = this.form_fields[field_name].label;			
				str +="<div class='display_element_label'><label class='display_element' >"+LIBERTAS_GENERAL_unjtidy(label)+"</label>"+required+"</div><div class='display_element_entry'>";
			}
			if (this.form_fields[field_name].type=="hidden"){
				str +=this.form_fields[field_name].fieldValue;
			}
			if (this.form_fields[field_name].type=="text"){
				width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : (this.form_fields[field_name].field_property["width"]+"">"20")?"20": this.form_fields[field_name].field_property["width"]+""; 
				str +="<input class='display_element' type='text' size='"+width+"' maxlength='255' border='0' class='form_element'>";
			}
			if (this.form_fields[field_name].type=="textarea"){
				str +="<textarea class='display_element' cols='"+this.form_fields[field_name].field_property["width"]+"' rows='"+this.form_fields[field_name].field_property["height"]+"'></textarea>";
			}
			if (this.form_fields[field_name].type=="cdata"){
				str += LIBERTAS_GENERAL_unjtidy(this.form_fields[field_name].label);
			}
			if (this.form_fields[field_name].type=="date_time"){
	//		alert(this.form_fields[field_name].field_property["dateType"]);
				if (this.form_fields[field_name].field_property["dateType"].indexOf("Day")!=-1){
					str += "<select><option>DD</option></select>";
				}
				if (this.form_fields[field_name].field_property["dateType"].indexOf("Month")!=-1){
					str += "<select><option>MM</option></select>";
				}
				if (this.form_fields[field_name].field_property["dateType"].indexOf("Year")!=-1){
					str += "<select><option>YYYY</option></select>";
				}
				if (this.form_fields[field_name].field_property["dateType"].indexOf("Hour,Minute")!=-1){
					str += "<select><option>HH:MM</option></select>";
				}
			}
			if (this.form_fields[field_name].type=="fileupload"){
				str += "<input type='text' size='20'><input type='button' class='bt' value='upload'>";
			}
			if (this.form_fields[field_name].type=="radio"){
				for (index in this.form_fields[field_name].field_property){
					info = this.form_fields[field_name].field_property[index];
					if (info+''!=''){
						str += '	<input type=radio class="display_element" id="field'+field_name+'_'+index+'" name="field'+field_name+'" value='+LIBERTAS_GENERAL_unjtidy(info[1]);
						if (info[2]==true){
							str += ' checked';
						}
						str += '>&#160;<label class="display_element" for="field'+field_name+'_'+index+'">'+LIBERTAS_GENERAL_unjtidy(info[0])+'</label>';
						if (this.form_fields[field_name].layout_direction=="vertical"){
							str += '<br/>';
						} else {
							str += '&#160;';
						}
					}
				}
				if(get_attribute(this.form_fields[field_name].attributes,"other")==1){
					str += '	<input type=radio class="display_element" id="field'+field_name+'_'+(index+1)+'" name="field'+field_name+'" value="Other">&#160;<label class="display_element" for="field'+field_name+'_'+(index+1)+'">'+LIBERTAS_GENERAL_unjtidy(get_attribute(this.form_fields[field_name].attributes,"other_label"))+'</label> <br>&nbsp;&nbsp;&nbsp;<input type="text size="20" maxlength="255" name="h"/>';
					if (this.form_fields[field_name].layout_direction=="vertical"){
						str += '<br/>';
					} else {
						str += '&#160;';
					}

				}
				str +="";
			}
			if (this.form_fields[field_name].type=="checkbox"){
				for (index in this.form_fields[field_name].field_property){
					info = this.form_fields[field_name].field_property[index];
					if (info+''!=''){
						str += '	<input class="display_element" type=checkbox name="field'+field_name+'" id="field'+field_name+'_'+index+'" value='+LIBERTAS_GENERAL_unjtidy(info[1]);
						if (info[2]==true){
							str += ' checked';
						}
						str += '>&#160;<label class="display_element" for="field'+field_name+'_'+index+'">'+LIBERTAS_GENERAL_unjtidy(info[0])+'</label>';
						if (this.form_fields[field_name].layout_direction=="vertical"){
							str += '<br/>';
						} else {
							str += '&#160;';
						}
					}
				}
				if(get_attribute(this.form_fields[field_name].attributes,"other")==1){
					str += '	<input type=checkbox class="display_element" id="field'+field_name+'_'+(index+1)+'" name="field'+field_name+'" value="Other">&#160;<label class="display_element" for="field'+field_name+'_'+(index+1)+'">'+LIBERTAS_GENERAL_unjtidy(get_attribute(this.form_fields[field_name].attributes,"other_label"))+'</label> <br>&nbsp;&nbsp;&nbsp;<input type="text size="20" maxlength="255" name="h"/>';
					if (this.form_fields[field_name].layout_direction=="vertical"){
						str += '<br/>';
					} else {
						str += '&#160;';
					}

				}
				str +="";
			}
			if (this.form_fields[field_name].type=="select"){
				str += '<select class="display_element" name=field'+field_name;
				mymultiple	= get_attribute(this.form_fields[field_name].attributes,"multiple");
				mysize 		= get_attribute(this.form_fields[field_name].attributes,"size");
				myother 	= get_attribute(this.form_fields[field_name].attributes,"other");
				if (mymultiple+""=="1"){
					str += ' multiple="'+mymultiple+'"';
				}
				if (mysize+"">"1"){
					str += ' size="'+mysize+'"';
				}
				str += '>';
				for (index in this.form_fields[field_name].field_property){
					info = this.form_fields[field_name].field_property[index];
					if (info+''!=''){
						 str += '<option value='+LIBERTAS_GENERAL_unjtidy(info[1]);
						if (info[2]==true){
							str += ' selected';
						}
						str += '>'+LIBERTAS_GENERAL_unjtidy(info[0])+'</option>';
					}
				}
				if(myother==1){
					str += '	<option value="Other">'+LIBERTAS_GENERAL_unjtidy(get_attribute(this.form_fields[field_name].attributes,"other_label"))+'</option>';
					str += '</select><br><label class="display_element" for="field'+field_name+'_'+(index+1)+'">'+LIBERTAS_GENERAL_unjtidy(get_attribute(this.form_fields[field_name].attributes,"other_label"))+'</label> <input type="text size="20" maxlength="255" name="h"/>';

				} else {
					str +="</select>";
				}
			}
			if (this.form_fields[field_name].type=="subject"){
				counter_email = this.form_emails.length;
				if (counter_email>1){
					if (this.form_fields[field_name].field_property["field_format"]=="select"){
						str +="<select class='display_element' name='form_subject'>";
						for (index in this.form_emails){
							str += "<option";
							if (this.form_emails[index][2]){
								str += " selected";
							}
							str += ">"+LIBERTAS_GENERAL_unjtidy(this.form_emails[index][0])+"</option>";
						}
						str +="</select>";
					}
					if (this.form_fields[field_name].field_property["field_format"]=="radio"){
						for (index in this.form_emails){
							element_label = this.form_emails[index][0];
							element_selected = this.form_emails[index][2];
							str +="<input type='radio' class='display_element' name='form_subject'";
							str +=" value='"+LIBERTAS_GENERAL_unjtidy(element_label)+"'";
							if (element_selected+""=="true"){
								str += " checked";
							}
							str += "> <label>"+LIBERTAS_GENERAL_unjtidy(element_label)+"</label>";
							str +="<br/>";
						}
					}
					if (this.form_fields[field_name].field_property["field_format"]=="checkbox"){
						for (index in this.form_emails){
							element_label = this.form_emails[index][0];
							element_selected = this.form_emails[index][2];
							str +="<input type='checkbox' class='display_element' name='form_subject[]'";
							str +=" value='"+LIBERTAS_GENERAL_unjtidy(element_label)+"'";
							if (element_selected+""=="true"){
								str += " checked";
							}
							str += ">";
							str += " <label>"+LIBERTAS_GENERAL_unjtidy(element_label)+"</label>";
							str +="<br/>";
						}
					}
				}
			}
			if (this.form_fields[field_name].type=="row_splitter"){
//				str += '<table width="66%"><tr><td><hr width="200px"/></td><td width="100px" align="center" style="font-weight:bold;color:#ff0000;">New Row</td><td><hr  width="200px"/></td></tr></table>';
				str += '<div class="centered">';
				str += '<div class="divhrleft"><hr/></div>';
				str += '<div class="divhrlabel">New Row</div>';
				str += '<div class="divhrright"><hr/></div>';
				str += '</div>';
				column_counter=0;
			}
			if (this.form_fields[field_name].type=="splitter"){
//				str += '<table width="66%"><tr><td><hr width="200px/></td><td width="100px" align="center" style="font-weight:bold;color:#ff0000;">New Column</td><td><hr  width="200px/></td></tr></table>';
				str += '<div class="centered">';
				str += '<div class="divhrleft"><hr/></div>';
				str += '<div class="divhrlabel">New Column</div>';
				str += '<div class="divhrright"><hr/></div>';
				str += '</div>';
			}
			if (this.form_fields[field_name].type=="splitter" || this.form_fields[field_name].type=="row_splitter"){
				str +="</div><div class='display_element_button_list'><div valign='top' width='10' class='display_element_button'>&#160;</div>";
			} else {				
				str +="</div><div class='display_element_button_list'><div valign='top' width='10' class='display_element_button'><input class='bt' type=button value='"+LOCALE_EDIT+"' onclick='javscript:__form_edit_field(\""+this.form_fields[field_name].name+"\")'></div>";				
			}
			str +="<div width='10' class='display_element_button'><input class='bt' type=button value='"+LOCALE_REMOVE+"' onclick='javscript:buildform.remove_field("+field_name+")'></div>";
			if (i!=0){
				str += '<div valign="top" width="10" class="display_element_button"><input class="bt" type="button" value="'+LOCALE_UP+'" onclick="javascript:__form_move_option('+i+','+(i-1)+')"/></div>';
			} else {
				str += '<div width="10" class="display_element_button">&#160;</div>';
			}
			if (i!=this.form_fields.length-1){
				str += '<div valign="top" width="10" class="display_element_button"><input class="bt" type="button" value="'+LOCALE_DOWN+'" onclick="javascript:__form_move_option('+i+','+(i+1)+')"/></div>';
			} else {
				str += '<div width="10" class="display_element_button">&#160;</div>';
			}
			str +="</div></div>";
			if (f==-1 || t==-1){
				str += "</div>";
			}
			if (f!=-1 && t!=-1){
				LIBERTAS_GENERAL_printToId("rankcell"+positioncell,str);
				str="";
//				document.getElementById("tableRanking").refresh();
			}
		}
		i++;
		positioncell++;
	}
	if (f==-1 || t==-1){
		if (count_required>0){
			str= "<div>"+LOCALE_REQUIRED_MSG+"</div>"+str;
		}
		str += "<div align='right'><input type=button class=bt value='Add new field' onclick='gen_new()'></div>";
		LIBERTAS_GENERAL_printToId("myform",str);
	}
//	this.preview(); // this now moved to button action
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: checkMyForm()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will be required to be able to define if the form requires to be redrawn or if the form only
- needs to be made visible
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function checkMyForm(){
	if (formRequiresReDraw){
		buildform.build();
		formRequiresReDraw = false;
	}
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: checkMyPreview()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will be required to be able to define if the preview requires to be redrawn or if it only
- needs to be made visible.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function checkMyPreview(){
	if (previewRequiresReDraw){
		buildform.preview();
		previewRequiresReDraw = false;
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __form_build_preview()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will build the page preview display allowing an administrator to see the desired layout that 
- the fields should appear.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __form_build_preview(){
	max_fields = this.form_fields.length;
	str = "";
	count_required=0;
	column_counter=1;
	counter_email = this.form_emails.length;
	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- if there is more than one (subject line) email address then display it as a select combo box
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	/*
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
	*/
	var i = 0;
	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- build the preview of the fields
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	for (field_name in this.form_fields){
		required ="";
		if (this.form_fields[field_name].isRequired){
			required = "<span class='required'>*</span>";
			count_required ++;
		}
		if (this.form_fields[field_name].type=="row_splitter"){
		//	str +="</table><table><tr ><td valign='top' class='display_element' colspan='2'>";
		} else if (this.form_fields[field_name].type=="splitter"){
		} else if (this.form_fields[field_name].type=="cdata"){
			str +="<tr ><td valign='top' class='display_element' colspan='2'>";
		} else {
			str +="<tr ><td valign='top' class='display_element'><label class='display_element' >"+LIBERTAS_GENERAL_unjtidy(this.form_fields[field_name].label)+"</label>"+required+"</td><td valign='top' class='display_element'>";
		}
		if (this.form_fields[field_name].type=="hidden"){
			str +=this.form_fields[field_name].fieldValue;
		}
		if (this.form_fields[field_name].type=="text"){
//			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : (this.form_fields[field_name].field_property["width"]+"">"20")?"20": this.form_fields[field_name].field_property["width"]+""; 
			str +="<input class='display_element' type='text' size='"+width+"' maxlength='255' border='0' class='form_element'>";
		}
		if (this.form_fields[field_name].type=="textarea"){
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if the type is a "textarea" then display the textarea field 
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			str +="<textarea class='display_element' cols='"+this.form_fields[field_name].field_property["width"]+"' rows='"+this.form_fields[field_name].field_property["height"]+"'></textarea>";
		}
		if (this.form_fields[field_name].type=="cdata"){
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if the type is a "cdata" then display as text
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			str += LIBERTAS_GENERAL_unjtidy(this.form_fields[field_name].label);
		}
		if (this.form_fields[field_name].type=="date_time"){
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if the type is a "date_time" then display pulldown combo boxes for each type
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if (this.form_fields[field_name].field_property["dateType"].indexOf("Day")!=-1){
				str += "<select><option>DD</option></select>";
			}
			if (this.form_fields[field_name].field_property["dateType"].indexOf("Month")!=-1){
				str += "<select><option>MM</option></select>";
			}
			if (this.form_fields[field_name].field_property["dateType"].indexOf("Year")!=-1){
				str += "<select><option>YYYY</option></select>";
			}
			if (this.form_fields[field_name].field_property["dateType"].indexOf("Hour,Minute")!=-1){
				str += "<select><option>HH:MM</option></select>";
			}
		}
		if (this.form_fields[field_name].type=="fileupload"){
			str += "<input type='text' size='20'><input class='bt' type='button' value='upload'>";
		}
		
		if (this.form_fields[field_name].type=="radio"){
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					str += '	<input type=radio class="display_element" id="field'+field_name+'_'+index+'" name="field'+field_name+'" value='+LIBERTAS_GENERAL_unjtidy(info[1]);
					if (info[2]==true){
						str += ' checked';
					}
					str += '>&#160;<label class="display_element" for="field'+field_name+'_'+index+'">'+LIBERTAS_GENERAL_unjtidy(info[0])+'</label>';
					if (this.form_fields[field_name].layout_direction=="vertical"){
						str += '<br/>';
					} else {
						str += '&#160;';
					}
				}
			}
			if(get_attribute(this.form_fields[field_name].attributes,"other")==1){
					str += '	<input type=radio class="display_element" id="field'+field_name+'_'+(index+1)+'" name="field'+field_name+'" value="Other"';
					if (info[2]==true){
						str += ' checked';
					}
					str += '>&#160;<label class="display_element" for="field'+field_name+'_'+(index+1)+'">'+LIBERTAS_GENERAL_unjtidy(get_attribute(this.form_fields[field_name].attributes,"other_label"))+'</label> <br>&nbsp;&nbsp;&nbsp;<input type="text size="20" maxlength="255" name="h"/>';
					if (this.form_fields[field_name].layout_direction=="vertical"){
						str += '<br/>';
					} else {
						str += '&#160;';
					}

				}
			str +="";
		}
		if (this.form_fields[field_name].type=="checkbox"){
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				
				if (info+''!=''){
					str += '	<input class="display_element" type=checkbox name="field'+field_name+'" id="field'+field_name+'_'+index+'" value='+LIBERTAS_GENERAL_unjtidy(info[1]);
					if (info[2]==true){
						str += ' checked';
					}
					str += '>&#160;<label class="display_element" for="field'+field_name+'_'+index+'">'+LIBERTAS_GENERAL_unjtidy(info[0])+'</label>';
					if (this.form_fields[field_name].layout_direction=="vertical"){
						str += '<br/>';
					} else {
						str += '&#160;';
					}
				}
			}
				if(get_attribute(this.form_fields[field_name].attributes,"other")==1){
					str += '	<input type=checkbox class="display_element" id="field'+field_name+'_'+(index+1)+'" name="field'+field_name+'" value="Other"';
					if (info[2]==true){
						str += ' checked';
					}
					str += '>&#160;<label class="display_element" for="field'+field_name+'_'+(index+1)+'">'+LIBERTAS_GENERAL_unjtidy(get_attribute(this.form_fields[field_name].attributes,"other_label"))+'</label> <br>&nbsp;&nbsp;&nbsp;<input type="text size="20" maxlength="255" name="h"/>';
					if (this.form_fields[field_name].layout_direction=="vertical"){
						str += '<br/>';
					} else {
						str += '&#160;';
					}

				}
			str +="";
		}
		if (this.form_fields[field_name].type=="select"){
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if the type is a "select" then display pulldown combo 
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			str += '	<select class="display_element" name=field'+field_name;
			mymultiple = get_attribute(this.form_fields[field_name].attributes,"multiple");
			mysize = get_attribute(this.form_fields[field_name].attributes,"size");
			if (mymultiple+""=="1"){
				str += ' multiple="'+mymultiple+'"';
			}
			if (mysize+"">"1"){
				str += ' size="'+mysize+'"';
			}
			str += '>';
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					 str += '<option value='+LIBERTAS_GENERAL_unjtidy(info[1]);
					if (info[2]==true){
						str += ' selected';
					}
					str += '>'+LIBERTAS_GENERAL_unjtidy(info[0])+'</option>';
				}
			}
			if(get_attribute(this.form_fields[field_name].attributes,"other")==1){
				str += '	<option value="Other"';
				if (info[2]==true){
					str += ' selected';
				}
				str += '>'+LIBERTAS_GENERAL_unjtidy(get_attribute(this.form_fields[field_name].attributes,"other_label"))+'</option>';
				str += '</select><br><label class="display_element" for="field'+field_name+'_'+(index+1)+'">'+LIBERTAS_GENERAL_unjtidy(get_attribute(this.form_fields[field_name].attributes,"other_label"))+'</label> <input type="text size="20" maxlength="255" name="h"/>';
			} else {
				str +="</select>";
			}
		}
		if (this.form_fields[field_name].type=="row_splitter"){
			str += "</table></td></tr></table><table width='100%'><tr><td><table width='100%' border='0'><tr><td valign='top'><table width='100%' border='0'>";
			column_counter=0;
		}
		if (this.form_fields[field_name].type=="splitter"){
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if the type is a "splitter" then start a new column
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			str += "</table></td><td valign='top'><table width='100%' border='0'>";
//			str +="<tr ><td valign='top' class='display_element' colspan='2'>";
			column_counter++;
		}
		if (this.form_fields[field_name].type=="subject"){
			counter_email = this.form_emails.length;
			if (counter_email>1){
				if (this.form_fields[field_name].field_property["field_format"]=="select"){
					str +="<select class='display_element' name='form_subject'>";
					for (index in this.form_emails){
						str += "<option";
						if (this.form_emails[index][2]){
							str += " selected";
						}
						str += ">"+LIBERTAS_GENERAL_unjtidy(this.form_emails[index][0])+"</option>";
					}
					str +="</select>";
				}
				if (this.form_fields[field_name].field_property["field_format"]=="radio"){
					for (index in this.form_emails){
						element_label = this.form_emails[index][0];
						element_selected = this.form_emails[index][2];
						str +="<input type='radio' class='display_element' name='form_subject'";
						str +=" value='"+LIBERTAS_GENERAL_unjtidy(element_label)+"'";
						if (element_selected){
							str += " checked";
						}
						str += "> <label>"+LIBERTAS_GENERAL_unjtidy(element_label)+"</label>";
						str +="<br/>";
					}
				}
				if (this.form_fields[field_name].field_property["field_format"]=="checkbox"){
					for (index in this.form_emails){
						element_label = this.form_emails[index][0];
						element_selected = this.form_emails[index][2];
						str +="<input type='checkbox' class='display_element' name='form_subject[]'";
						str +=" value='"+LIBERTAS_GENERAL_unjtidy(element_label)+"'";
						if (element_selected){
							str += " checked";
						}
						str += "> <label>"+LIBERTAS_GENERAL_unjtidy(element_label)+"</label>";
						str +="<br/>";
					}
				}
			}
		}

		if ((this.form_fields[field_name].type=="row_splitter") || (this.form_fields[field_name].type=="splitter")){
		}else {
			str +="</tr>";
			i++;
		}
		
	}
	required_str="";
	if (count_required>0){
		required_str= "<tr><td colspan='"+ ( column_counter * 2 ) +"'>"+LOCALE_REQUIRED_MSG+"</td></tr>";
	}
	breakpoint();
	debug("<table width='100%' border='0'>"+required_str+"<tr><td valign='top'><table width='100%' border='0'>"+str+"</table></td></tr></table><p align='right'><input type=button class=bt value='Add new field' onclick='gen_new()'></p>");
	LIBERTAS_GENERAL_printToId("preview","<table width='100%' border='0'>"+required_str+"<tr><td valign='top'><table width='100%' border='0'>"+str+"</td></tr></table><p align='right'><input type=button class=bt value='Add new field' onclick='gen_new()'></p>");
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __form_build_XML()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Build the data block that will be passed to the php script to define the complete data structure 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __form_build_XML(){
//debug('__form_build_XML()');
	if(this.form_fields.length==0){
		alert("Sorry you have not defined any fields for this form");
	} else {
		if ((field==null) || (field+''=='undefined')){
			debug_alert("no field defined");
		} else {
			if (field.fieldName!=''){
				debug_alert("field has details");
				__return_field(field);
			}
		}
		max_fields 		= this.form_fields.length;
		str 			= "";
		group_list		= new String();
		required_list	= new String();
		
		str +="number_of_fields::"+this.form_fields.length+":===:";
		defined_field_name="";
		for (field_name in this.form_fields){
			if (this.form_fields[field_name].fieldName!=''){
				defined_field_name = this.form_fields[field_name].fieldName;
			} else {
				defined_field_name = this.form_fields[field_name].name;
			}
			if (this.form_fields[field_name].type=="hidden"){
				str +="hidden";
				str +=":=========:name::"+defined_field_name;
				str +=":=========:value::"+this.form_fields[field_name].fieldValue;
				str +=":===:";
			}
			if (this.form_fields[field_name].type=="cdata"){
				str +="CDATA:===:";
				
				/* Starts label Msg portion Comment and added By Muhammad Imran */

				//str +=this.form_fields[field_name].label;
				str +=":=========:name::"+defined_field_name
				str +=":=========:label::"+this.form_fields[field_name].label;
				
				/* Ends label Msg portion Comment and added By Muhammad Imran */
				
				str +=":===:~~CDATA:===:";
			}
			if (this.form_fields[field_name].type=="fileupload"){
				str += "fileupload";
				str +=":=========:name::"+defined_field_name
				str +=":=========:label::"+this.form_fields[field_name].label;
				str +=":===:";
			}
			if (this.form_fields[field_name].type=="date_time"){
				str += "date_time";
				str +=":=========:name::"+defined_field_name
				str +=":=========:label::"+this.form_fields[field_name].label;
				str +=":=========:dateType::"+this.form_fields[field_name].field_property["dateType"];
				str +=":===:";
			}
			
			if (this.form_fields[field_name].type=="text"){
				str +="text";
				str +=":=========:label::"+this.form_fields[field_name].label;
				str +=":=========:name::"+defined_field_name;
				width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
				str +=":=========:width::"+width;
				str += formbuilder_check(this.form_fields[field_name].isRequired,"required");
				str += formbuilder_check(this.form_fields[field_name].session_hide,"session_hide");
				str +=":===:";
				if (this.form_fields[field_name].isRequired){
					if (required_list.length!=0){
						required_list+=",";
					}
					required_list +=""+defined_field_name; //Zia what if use defined_field_name instead of field_name 
				}
			}
			if (this.form_fields[field_name].type=="textarea"){
				str +="textarea:=========:width::"+this.form_fields[field_name].field_property["width"]+":=========:height::"+this.form_fields[field_name].field_property["height"];
				str +=":=========:label::"+this.form_fields[field_name].label;
				str +=":=========:name::"+defined_field_name;
				str += formbuilder_check(this.form_fields[field_name].isRequired,"required");
				str += formbuilder_check(this.form_fields[field_name].session_hide,"session_hide");
				str += ':===:';
				if (this.form_fields[field_name].isRequired){
					if (required_list.length!=0){
						required_list+=",";
					}
					required_list +=""+defined_field_name;
				}
			}
			if (this.form_fields[field_name].type=="radio"){
				str += 'radio';
				str += ":=========:label::"+this.form_fields[field_name].label;
				str += ":=========:type::"+this.form_fields[field_name].layout_direction; 
				str += formbuilder_check(this.form_fields[field_name].isRequired,"required");
				str += formbuilder_check(this.form_fields[field_name].session_hide,"session_hide");
				other = get_attribute(this.form_fields[field_name].attributes,"other");
				str +=":=========:other::"+other
				other_label = get_attribute(this.form_fields[field_name].attributes,"other_label");
				str +=":=========:other_label::"+other_label
				str +=":=========:name::"+defined_field_name+":===:";
				for (index in this.form_fields[field_name].field_property){
					info = this.form_fields[field_name].field_property[index];
					if (info+''!=''){
						str +="label::"+info[1]+":=========:";
						if (info[2]==true){
							str += "checked::true";
						} else {
							str += "checked::false";
						}
						str+= ':=========:value::'+info[0]+':===:';
					}
				}
				str +="~~radio:===:";
				if (group_list.length!=0){
					group_list+=",";
				}
				group_list +=""+field_name;
				if (this.form_fields[field_name].isRequired){
					if (required_list.length!=0){
						required_list+=",";
					}
					required_list +=""+defined_field_name;
				}
			}
			if (this.form_fields[field_name].type=="checkbox"){
				str +='checkboxes:=========:type::'+this.form_fields[field_name].layout_direction+':=========:label::'+this.form_fields[field_name].label;
				str +=":=========:name::"+defined_field_name;
				str += formbuilder_check(this.form_fields[field_name].isRequired,"required");
				str += formbuilder_check(this.form_fields[field_name].session_hide,"session_hide");
				other = get_attribute(this.form_fields[field_name].attributes,"other");
				str +=":=========:other::"+other
				other_label = get_attribute(this.form_fields[field_name].attributes,"other_label");
				str +=":=========:other_label::"+other_label
				str += ':===:';
				for (index in this.form_fields[field_name].field_property){
					info = this.form_fields[field_name].field_property[index];
					if (info+''!=''){
						str +="label::"+info[1]+":=========:";
						if (info[2]==true){
							str += "checked::true";
						} else {
							str += "checked::false";
						}
						str+= ':=========:value::'+info[0]+':===:';
					}
				}
				str +="~~checkboxes:===:";
				if (group_list.length!=0){
					group_list+=",";
				}
				group_list +=""+field_name;
				if (this.form_fields[field_name].isRequired){
					if (required_list.length!=0){
						required_list+=",";
					}
					required_list +=""+defined_field_name;
				}
			}
			if (this.form_fields[field_name].type=="select"){
				str += 'select';
				str += ":=========:label::"+this.form_fields[field_name].label;
				str += formbuilder_check(this.form_fields[field_name].session_hide,"session_hide");
				str += ":=========:name::"+defined_field_name
				mymultiple = get_attribute(this.form_fields[field_name].attributes,"multiple");
				str +=":=========:multiple::"+mymultiple
				mysize = get_attribute(this.form_fields[field_name].attributes,"size");
				str +=":=========:size::"+mysize
				other = get_attribute(this.form_fields[field_name].attributes,"other");
				str +=":=========:other::"+other
				other_label = get_attribute(this.form_fields[field_name].attributes,"other_label");
				str +=":=========:other_label::"+other_label
				str += ":===:";
				for (index in this.form_fields[field_name].field_property){
					info = this.form_fields[field_name].field_property[index];
					if (info+''!=''){
						str +="label::"+info[1]+":=========:";
						if (info[2]==true){
							str += "checked::true";
						} else {
							str += "checked::false";
						}
						str+= ':=========:value::'+info[0]+':===:';
					}
				}
				str +="~~select:===:";
				if (this.form_fields[field_name].isRequired){
					if (required_list.length!=0){
						required_list+=",";
					}
					required_list +=""+defined_field_name;
				}
			}
			if (this.form_fields[field_name].type=="subject"){
				str += 'subject';
				str += ":=========:label::"+this.form_fields[field_name].label;
				str += ":=========:type::"+this.form_fields[field_name].field_property["field_format"]; 
				str += ":=========:name::"+defined_field_name+":===:";
				str +="~~subject:===:";
				if (this.form_fields[field_name].isRequired){
					if (required_list.length!=0){
						required_list+=",";
					}
					required_list +=""+defined_field_name;
				}
			}
			if (this.form_fields[field_name].type=="splitter"){
				str+="seperator:===:";
			}
			if (this.form_fields[field_name].type=="row_splitter"){
				str+="seperator_row:===:";
			}
			str +="";
		}
		breakpoint();
		
		debug(LIBERTAS_GENERAL_unjtidy(str))
		xml_destination.value = LIBERTAS_GENERAL_unjtidy(str);
		if (this.form_fields[field_name].type=="text"){
			str +="<input type=\"text\" ";
			str +=" label=\""+this.form_fields[field_name].label+"\"";
			str +=" name=\""+defined_field_name+"\"";
			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
			str +=" size=\""+width+"\"";
			str += formbuilder_check(this.form_fields[field_name].isRequired,  "required");
			str += formbuilder_check(this.form_fields[field_name].session_hide,"session_hide");
			str +="/>:===:";
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+defined_field_name;
			}
		}
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
			
		wizard_frm.destination_url.value = this.form_url;
		wizard_frm.email_list.value = str;
		wizard_frm.group_fields.value= group_list;
		wizard_frm.required_fields.value= required_list;
		debug_alert("has_editor ["+has_editor+"]")
		if (has_editor == 1){
			check_required_fields(2);
		} else {
			check_required_fields(0);
		}
		//	document.wizard_frm.submit();
	}
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: formbuilder_check( field , label)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- this function is used by the BUILD_XML function.
- if the field is true then return the label and a 1 otherwise label follwoed by a zero
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function formbuilder_check(v,label){
	if(v+""=="undefined"){
		str = ":=========:"+label+"::0";
	} else {
		if(v){
			str = ":=========:"+label+"::1";
		} else {
			str = ":=========:"+label+"::0";
		}
	}
	return str;
}

function toggleemail(){
	buildform.show_email();
}
function __edit_email(i){
	buildform.show_email(i);
}

function __update_email(index){
	for (var x=0;x<document.wizard_frm.elements.length;x++){
		if (document.wizard_frm.elements[x].name=="new_subject_line"){
			buildform.form_emails[index][0] = document.wizard_frm.elements[x].value
		} else if (document.wizard_frm.elements[x].name=="new_email_address"){
			buildform.form_emails[index][1] = document.wizard_frm.elements[x].value;
		}
	}
	toggleemail();
}
