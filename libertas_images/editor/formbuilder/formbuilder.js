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
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- $Revision: 1.2 $, $Date: 2004/04/14 08:21:44 $
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
	Configuration options
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	var SHOW_SESSION_HIDE 	= 0;
	var SHOW_REQUIRED_FIELD = 1;

	var optionLabel ='';
	var current_page=0;
	var advanced_form_builder=false;
/*
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
	this.show_url				= __show_url;
	this.add_email				= __add_email;
	this.remove_email			= __remove_email;
	this.move_email				= __move_email;
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
function __form_add_field(name, type, mylabel, field_property, isPropertyArray, layout_direction, options, isRequired, session_hide, val){
	pos = this.form_fields.length;
	this.form_fields[pos] = new form_field(type);
	this.form_fields[pos].name				= name;
	this.form_fields[pos].fieldName			= "field"+pos;
	this.form_fields[pos].fieldValue		= val;
	this.form_fields[pos].label 			= mylabel;
	this.form_fields[pos].field_property	= field_property;
	this.form_fields[pos].isPropertyArray	= isPropertyArray;
	this.form_fields[pos].layout_direction	= layout_direction;
	this.form_fields[pos].options			= options;
	this.form_fields[pos].isRequired 		= isRequired;
	this.form_fields[pos].session_hide		= session_hide;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __form_edit_field(searchFor)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- this function will extract the required field and call the proper screen of the wizard;
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __form_edit_field(searchFor){
	for(var i=0;i<buildform.form_fields.length;i++){
//		alert(buildform.form_fields[i].fieldName +'=='+ searchFor);
		if (buildform.form_fields[i].fieldName == searchFor){
			index=i;
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
		printToId("list_of_emails","");
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
	str  = '<table>';
	str += '	<tr><td class="TableHeader">'+LOCALE_SFORM_URL+'</td><td><input type="text" name="url" onchange="javascript:buildform.form_url=this.value" value="'+this.form_url+'"></td></tr>';
	str += '</table>';
//	document.all.list_of_emails.innerHTML=str;
	printToId("list_of_emails",str);
	wizard_frm.url.focus();
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

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
	str += '	<tr><td><input type="text" name="email_option_label"></td><td><input type="text" name="email_option_value"></td><td><input class="bt" type="button" onclick="javascript:buildform.add_email();" value="'+LOCALE_ADD_EMAIL+'"></td></tr>';
	str += '</table>';
	this.form_url="";
	printToId("list_of_emails",str);
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
	for (var index=0; index<keys.length;index++){
		if (wizard_frm.email_option_label.value.indexOf(keys[index])!=-1){
			ok = false;
			fail = 1;
		}
	}
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
		this.build();
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
	this.build();
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

function __form_build_rank(){
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
		if (this.form_fields[field_name].type=="splitter" || this.form_fields[field_name].type=="cdata"){
			str +="<tr ><td valign='top' class='display_element' colspan='2'>";
		} else {
			str +="<tr ><td valign='top' class='display_element'><label class='display_element' >"+unjtidy(this.form_fields[field_name].label)+"</label>"+required+"</td><td valign='top' class='display_element'>";
		}
		if (this.form_fields[field_name].type=="hidden"){
			str +=this.form_fields[field_name].fieldValue;
		}
		if (this.form_fields[field_name].type=="text"){
			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
			str +="<input class='display_element' type='text' size='"+width+"' maxlength='255' border='0' class='form_element'>";
		}
		if (this.form_fields[field_name].type=="textarea"){
			str +="<textarea class='display_element' cols='"+this.form_fields[field_name].field_property["width"]+"' rows='"+this.form_fields[field_name].field_property["height"]+"'></textarea>";
		}
		if (this.form_fields[field_name].type=="cdata"){
			str += unjtidy(this.form_fields[field_name].label);
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
				if (info+'' != ''){
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
		if (this.form_fields[field_name].type=="splitter"){
			str += '<hr />';
		}
		if (this.form_fields[field_name].type=="splitter"){
			str +="</td><td valign='top' width='10'>&#160;</td>";
		} else {
			str +="</td><td valign='top' width='10'><input class='bt' type=button value='"+LOCALE_EDIT+"' onclick='javscript:__form_edit_field(\""+this.form_fields[field_name].name+"\")'></td>";
		}
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
	str += "<tr><td colspan='2'></td><td colspan='4'><input type=button class=bt value='Add new field' onclick='gen_new()'></td></tr>";
	printToId("myform","<table width='100%' border='0'>"+str+"</table>");
	this.preview();
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
		if (this.form_fields[field_name].type=="splitter" || this.form_fields[field_name].type=="cdata"){
			str +="<tr ><td valign='top' class='display_element' colspan='2'>";
		} else {
			str +="<tr ><td valign='top' class='display_element'><label class='display_element' >"+unjtidy(this.form_fields[field_name].label)+"</label>"+required+"</td><td valign='top' class='display_element'>";
		}
		if (this.form_fields[field_name].type=="hidden"){
			str +=this.form_fields[field_name].fieldValue;
		}
		if (this.form_fields[field_name].type=="text"){
			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
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
			str += unjtidy(this.form_fields[field_name].label);
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
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if the type is a "select" then display pulldown combo 
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
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
		if (this.form_fields[field_name].type=="splitter"){
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if the type is a "splitter" then start a new column
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			str += "</table></td><td valign='top'><table width='100%' border='0'>";
			column_counter++;
		}

		str +="</tr>";
		i++;
	}
	required_str="";
	if (count_required>0){
		required_str= "<tr><td colspan='"+ ( column_counter * 2 ) +"'>"+LOCALE_REQUIRED_MSG+"</td></tr>";
	}
	printToId("preview","<table width='100%' border='0'>"+required_str+"<tr><td valign='top'><table width='100%' border='0'>"+str+"</td></tr></table><p align='right'><input type=button class=bt value='Add new field' onclick='gen_new()'></p>");
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- F O R M  F I E L D   O B J E C T 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
- this is the object for the fields of the form builder
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function form_field(field_type){
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Properties
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	this.name					= "";
	this.value					= "";
	this.fieldName				= "";
	this.fieldValue				= "";
	this.type 					= field_type;
	this.label 					= "";
	this.field_property			= new Array();
	this.isPropertyArray		= false;
	this.layout_direction		= "vertical";
	this.options				= new Array();
	this.isRequired 			= false;
	this.session_hide			= false;
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Methods
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	this.clone 					= __field_clone;
	this.clone_field_property	= __clone_field_property;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __field_clone(data)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will clone a field supplied as parameter "data"
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __field_clone(data){
	this.name				= data.name;
	this.fieldName			= data.fieldName;
	this.fieldValue			= data.fieldValue;
	this.type 				= data.type;
	this.label 				= data.label;
	this.options			= data.options;
	this.isPropertyArray	= data.isPropertyArray;
	this.layout_direction	= data.layout_direction;
	this.isRequired 		= data.isRequired;
	this.session_hide		= data.session_hide;
	this.clone_field_property(data.field_property, this.isPropertyArray);
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __clone_field_property(info, bool)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- this function will create a duplicate of a fields property list
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
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
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- F O R M   B U I L D E R   D I S P L A Y   F U N C T I O N S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
- Functions to display the form builder wizard, ranking and preview.
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __return_field(data)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will either add a new field to the end of the field list or overwrite a field with modified 
- values
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __return_field(data){
	if (data.fieldName==""){
		pos = buildform.form_fields.length;
	} else {
		pos = data.fieldName.toString().substr(5);
	}
	buildform.form_fields[pos] = new form_field();
	buildform.form_fields[pos].clone(data);
//	alert("field"+pos);
	buildform.form_fields[pos].fieldName="field"+pos;
	buildform.form_fields[pos].name="field"+pos;
/*	if (buildform.form_url!=""){
	} else {
		buildform.form_fields[pos].name="";
	}*/
	buildform.build();
}	

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __recieve_field(index)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- currently not called
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __recieve_field(index){
	return buildform.form_fields[index];
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: gen_new()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will call the wizard page 0 and display the second tab
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function gen_new(){
	show_tabular_screen('tab_2')
	next_page(0);
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: next_page(page)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- page == 0 - if the value == zero then we are adding a new field to the system.
-
-             otherwise
-
- page < 0  - if the page value is a minus number then we are loading an existing field into the editor wizard
- page > 0  - if the page value is a positive number then we are loading a specific page of the wizard
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function next_page(page){
	str="";
	ignore_previous_form = false;
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- load field form form list then go to screen 2
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	if (page<0){
		field = new form_field("");
		field.clone(buildform.form_fields[(page * -1)-1]);
		if (field.fieldName==""){
			field.fieldName = "field"+((page * -1)-1)
		}
//		alert(field.fieldName);
		page=2;
		ignore_previous_form = true;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Add a new field and go to screen 1
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	if (page==0){
		current_page=page;
		field = new form_field("")
		page=1;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- if page equals one then display list of fields that are available to the user
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	if (page==1){
		current_page=page;
		myOptions = Array(
			Array("hidden.gif","hidden","typeHidden",LOCALE_MSG_ADD_HIDDEN_LABEL),
			Array("text.gif","text","typeText",LOCALE_MSG_ADD_TEXT_LABEL),
			Array("textarea.gif", "textarea", "typeTextArea", LOCALE_MSG_ADD_TEXTAREA_LABEL),
			Array("radio.gif", "radio", "typeRadio", LOCALE_MSG_ADD_RADIO_LABEL),
			Array("checkbox.gif", "checkbox", "typeCheckBox", LOCALE_MSG_ADD_CHECK_LABEL),
			Array("select.gif", "select", "typeSelect", LOCALE_MSG_ADD_SELECT_LABEL),
			Array("splitter.gif", "splitter", "typeSplitter", LOCALE_MSG_ADD_SPLITTER_LABEL),
			Array("msg.gif", "cdata", "typeCdata", LOCALE_MSG_ADD_TXT_MSG),
			Array("datetime.gif", "date_time", "typeDateTime", LOCALE_MSG_ADD_DATETIME)
		);
		if (maximumAccess=='ECMS'){
			myOptions[myOptions.length]=Array("fileupload.gif", "fileupload", "typeFileUpload", LOCALE_MSG_ADD_FILE_UPLOAD);
		}
		str  = '<h1>'+LOCALE_WIZARD_PAGE+' 1</h1>';
		str += LOCALE_MSG_ADD_FIELD;
		str += '<input type="hidden" name="page" value="1">';
		str += '<p>'
		for(var i =0; i <myOptions.length;i++){
			str += '<img src="/libertas_images/editor/formbuilder/images/'+myOptions[i][0]+'" width="21" height="21" border="0"/> <input type="radio" id="'+myOptions[i][2]+'" name="typeSelected" value="'+myOptions[i][1]+'"';
			if (field.type==myOptions[i][1] || (i==0 && field.type=='')){
				str += ' checked';
			}
			str += '/> <label for="'+myOptions[i][2]+'">'+myOptions[i][3]+'</label><br />\n';
		}
		str += '<input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
		printToId("wizard",str);
	}	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- if page equals two then display the form for that type of entry if splitter then jump directly to page three
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	if(page==2){
		current_page=page;
		if (document.wizard_frm.typeSelected+'' != 'undefined'){
			l = document.wizard_frm.typeSelected.length;
			for(i=0;i<l;i++){
				if (document.wizard_frm.typeSelected[i].checked && document.wizard_frm.typeSelected[i].value=="splitter"){
					field.type = "splitter";
					page=3;
				} 
			}
		}
		if (page==2){
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
//			alert(field.type);
			switch (field.type){
				case "fileupload":
					field.field_property=Array();
					str += LOCALE_MSG_ADD_TXT_MSG;
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}else{
						fname = (field.name==''? field.fieldName :field.name);
						str += '<input type="hidden" id="field_name" name="field_name" maxlength="255" size="50" value="'+fname+'"/>';
					}
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+unjtidy(field.label)+'"/></td></tr>';
					str += '</table>';
					str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
					break;
				case "date_time":
					if (field.field_property["dateType"]+"" == 'undefined'){
						field.field_property["dateType"] = LOCALE_DATETYPE_1_VALUE;
					}
					str += LOCALE_MSG_ADD_TXT_MSG;
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}else{
						fname = (field.name==''? field.fieldName :field.name);
						str += '<input type="hidden" id="field_name" name="field_name" maxlength="255" size="50" value="'+fname+'"/>';
					}
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+unjtidy(field.label)+'"/></td></tr>';
					str += '<tr><td valign="top"><label for="dateType"><strong>'+LOCALE_DATETYPE+'</strong></label></td><td><select name="dateType" id="dateType">';
					str += '<option ';
//					alert("check :: "+field.field_property["dateType"]+" "+LOCALE_DATETYPE_2_VALUE);
					if (field.field_property["dateType"]==LOCALE_DATETYPE_1_VALUE){
						str += "selected='true' ";
					}
					str += 'value="'+LOCALE_DATETYPE_1_VALUE+'">'+LOCALE_DATETYPE_1_LABEL+'</option>';
					str += '<option ';
					if (field.field_property["dateType"]==LOCALE_DATETYPE_2_VALUE){
						str += "selected='true' ";
					}
					str += 'value="'+LOCALE_DATETYPE_2_VALUE+'">'+LOCALE_DATETYPE_2_LABEL+'</option>';
					str += '<option ';
					if (field.field_property["dateType"]==LOCALE_DATETYPE_3_VALUE){
						str += "selected='true' ";
					}
					str += 'value="'+LOCALE_DATETYPE_3_VALUE+'">'+LOCALE_DATETYPE_3_LABEL+'</option>';
					str += '</select></td></tr>';
					if (SHOW_REQUIRED_FIELD==1){
						str += '<tr><td valign="top"><strong><label for="required_yes">'+LOCALE_INPUT_REQUIRED+'</label></strong></td><td>';
						str += '<p><input type="checkbox" id="required_yes" name="isRequired" value="YES" ';
						if (field.isRequired){
							str += ' checked';
						}
						str += '/>';
						str += '</td></tr>';
					}
					str += '</table>';
					str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
					break;
				case "cdata":
					field.field_property=Array();
					str += LOCALE_MSG_ADD_TXT_MSG;
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}else{
						fname = (field.name==''? field.fieldName :field.name);
						str += '<input type="hidden" id="field_name" name="field_name" maxlength="255" size="50" value="'+fname+'"/>';
					}
					str += '<tr><td valign="top" colspan=2><label for="field_label"><strong>'+LOCALE_MSG+'</strong></label> </td></tr>';
					str += '<tr><td valign="top" colspan=2><textarea type="text" id="field_label" name="field_label" cols="55" rows="15">'+cdata_rich_to_plain(field.label)+'</textarea></td></tr>';
					str += '</table>';
					str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
					break;
				case "splitter":
					break;
				case "hidden":
					str += LOCALE_MSG_ADD_TEXT_BOX;
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<input type="hidden" id="field_label" name="field_label" value=""/>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
					str += '<tr><td valign="top"><label for="fieldValue"><strong>'+LOCALE_INPUT_VALUE+'</strong></label> </td><td valign="top"><input type="text" name=fieldValue size=25 maxlength=255 value="'+field.fieldValue+'"></td></tr>';
					str += '</table>';
					str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
					break;
				case "text":
					if (field.field_property["width"]+"" == 'undefined'){
						field.field_property["width"]="20";
					}
					str += LOCALE_MSG_ADD_TEXT_BOX;
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+unjtidy(field.label)+'"/></td></tr>';
					str += '<tr><td valign="top"><label for="field_field_property"><strong>'+LOCALE_INPUT_SPECIAL+'</strong></label> </td><td valign="top">';
					str += LOCALE_INPUT_WIDTH+': <input type="text" name=field_width size=3 maxlength=3 value="'+field.field_property['width']+'">';
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
					break;
				case "textarea":
					if (field.field_property["width"]+"" == 'undefined'){
						field.field_property["width"]="";
					}
					if (field.field_property["height"]+"" == 'undefined'){
						field.field_property["height"]="";
					}
					str += LOCALE_INPUT_TEXTAREA_BOX;
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+unjtidy(field.label)+'"/></td></tr>';
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
					//str += '</form>';
					break;
				case "radio":
					field.field_property["width"]=null;
					field.field_property["height"]=null;
					str += LOCALE_INPUT_RADIO_BOX;
//					str += '<form name="wizard_frm">';
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
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
					str += '<tr><td colspan="2" valign="top">'+show_options(1)+'</td></tr>';
					str += '</table>';
					str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
					//str += '</form>';
					break;
				case "checkbox":
					field.field_property["width"]=null;
					field.field_property["height"]=null;
					str += LOCALE_INPUT_CHECK_BOX;
//					str += '<form name="wizard_frm">';
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+unjtidy(field.label)+'"/></td></tr>';
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
					str += '<tr><td colspan="2" valign="top"><span id ="display_options">'+show_options(1)+'</span></td></tr>';
					str += '</table>';
					str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
					//str += '</form>';
					break;
				case "select":
					field.field_property["width"]=null;
					field.field_property["height"]=null;
					str += LOCALE_INPUT_SELECT_BOX;
//					str += '<form name="wizard_frm">';
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+unjtidy(field.label)+'"/></td></tr>';
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
					str += '<tr><td colspan="2" valign="top"><span id ="display_options">'+show_options(1)+'</span></td></tr>';
					str += '</table>';
					str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
					
					break;
				default:
			}
			printToId("wizard",str);
		}
		
	}
	if(page==3){
		switch (field.type){
			case "splitter":
				break;
			case "cdata":
				if (buildform.form_url != ''){
					field.fieldName			 				= jtidy(document.wizard_frm.field_name.value);
				}
				field.label = jtidy(cdata_plain_to_rich(document.wizard_frm.field_label.value));
				break;
			case "hidden":
				if (buildform.form_url != ''){
					field.fieldName			 				= jtidy(document.wizard_frm.field_name.value);
				}
				field.isPropertyArray 				= false;
				field.label							= "hidden";
				field.fieldValue					= jtidy(document.wizard_frm.fieldValue.value);
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
			case "date_time":
				if (buildform.form_url != ''){
					field.fieldName			 				= jtidy(document.wizard_frm.field_name.value);
				}
				field.label = jtidy(document.wizard_frm.field_label.value);
				field.isPropertyArray 				= false;
				field.fieldValue					= "";
//					alert(field.field_property["dateType"]);
//				alert(document.wizard_frm.dateType.options[document.wizard_frm.dateType.selectedIndex].value)
				field.field_property["dateType"]	= document.wizard_frm.dateType.options[document.wizard_frm.dateType.selectedIndex].value;
				if (document.wizard_frm.isRequired+"" !="undefined")
					field.isRequired 				= document.wizard_frm.isRequired.checked;
				else 
					field.isRequired 				= false;
				if (document.wizard_frm.isSession+"" !="undefined")
					field.session_hide				= document.wizard_frm.isSession.checked;
				else 
					field.session_hide 				= false;
				break;
			case "fileupload":
				if (buildform.form_url != ''){
					field.fieldName			 		= jtidy(document.wizard_frm.field_name.value);
				}
				field.label 						= jtidy(document.wizard_frm.field_label.value);
				field.isPropertyArray 				= false;
				field.fieldValue					= "";
				if (document.wizard_frm.isRequired+"" !="undefined")
					field.isRequired 				= document.wizard_frm.isRequired.checked;
				else 
					field.isRequired 				= false;
				if (document.wizard_frm.isSession+"" !="undefined")
					field.session_hide				= document.wizard_frm.isSession.checked;
				else 
					field.session_hide 				= false;
				break;
			case "text":
				if (buildform.form_url != ''){
					field.fieldName			 				= jtidy(document.wizard_frm.field_name.value);
				}
				field.label = jtidy(document.wizard_frm.field_label.value);
				field.isPropertyArray 				= false;
				field.field_property["width"]		= jtidy(document.wizard_frm.field_width.value);
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
				field.label = jtidy(document.wizard_frm.field_label.value);
				if (buildform.form_url != ''){
					field.fieldName			 				= jtidy(document.wizard_frm.field_name.value);
				}
				field.isPropertyArray 				= false;
				field.field_property["width"]		= jtidy(document.wizard_frm.field_width.value);
				field.field_property["height"]		= jtidy(document.wizard_frm.field_height.value);
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
				field.label = jtidy(document.wizard_frm.field_label.value);
				if (buildform.form_url != ''){
					field.fieldName			 				= jtidy(document.wizard_frm.field_name.value);
				}
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
				field.label = jtidy(document.wizard_frm.field_label.value);
				if (buildform.form_url != ''){
					field.fieldName			 				= jtidy(document.wizard_frm.field_name.value);
				}
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
				field.label = jtidy(document.wizard_frm.field_label.value);
				if (buildform.form_url != ''){
					field.fieldName			 				= jtidy(document.wizard_frm.field_name.value);
				}
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
//		str += '<form name="wizard_frm">';
		str += '<table>';
		str += '<input type="hidden" name="page" value="3">';
		str += '<p>'+LOCALE_FIELD_THANKS+'</p>';
		str += '<p><input class="bt" type="button" value="'+LOCALE_BACK+'" onclick="javascript:next_page(0);"/><input class="bt" type="button" value="Finish" onclick="javascript:show_tabular_screen(\'tab_3\');"/></p>';
		//str += '</form>';
		printToId("wizard",str);
		__return_field(field);
	}
	if(page==4){
		next_page(0);
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: show_options(returnType);
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- When a user is adding options to a form element (select, radio, checkbox) these functions will allow the 
- user to manage the data.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function show_options(returnType){
	
	str = '<table>';
	str += '	<tr><td>'+LOCALE_INPUT_LABEL+'</td><td>'+LOCALE_INPUT_VALUE+'</td>';
	if ((field.type=="radio") && (field.type=="checkbox")){
		str += '<td>'+LOCALE_DEFAULT+'</td></tr>';
	}
	for (i=0; i<field.field_property.length;i++){
		str += '<tr><td>'+unjtidy(field.field_property[i][0])+'</td>';
		if (advanced_form_builder){
			str += '<td>'+unjtidy(field.field_property[i][1])+'</td>';
		}
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
	str += '	<tr><td><input type="text" maxlength="255" size="15" name="option_label" id="option_label"></td>';
		if (advanced_form_builder){
			str += '<td><input maxlength="255" size="15" type="text" name="option_value" id="option_value" onclick="javascript:copy_label(this);" onfocus="javascript:copy_label(this);" onblur="javascript:copy_label(this);">';
		} else {
			str += '<input type="hidden" name="option_value" id="option_value">';
		}
		str += '</td><td><input class="bt" type=button onclick="javascript:add_option();" value="'+LOCALE_INPUT_ADD_OPTION+'"></td></tr>';
	str += '</table>';
	return str;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function define_label(t){
	optionLabel = t
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: copy_label()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Copy a label into the value field of an option.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function copy_label(){
	myLabel ="";
	for (var x=0;x<document.wizard_frm.elements.length;x++){
		if (document.wizard_frm.elements[x].name=="option_label"){
			myLabel=document.wizard_frm.elements[x].value
		} else if (document.wizard_frm.elements[x].name=="option_value"){
			if (document.wizard_frm.elements[x].value.length==0){
				document.wizard_frm.elements[x].value = myLabel;
			}
		}
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: add_option()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function is designed to add a new option to a fields option list (Select, Checkbox and Radio)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function add_option(){
	if(document.wizard_frm.option_label.value.length==0){
		alert(LOCALE_SUPPLY_A_LABEL);
		document.wizard_frm.option_label.focus();
	} else {
		pos = field.field_property.length
		if (pos==0){
			field.field_property = new Array();
		}
		if (pos==0 && field.type=="radio"){
			bool 	= true;
		}else{
			bool	= false;
		}
		if (document.wizard_frm.option_value.value.length==0){
			document.wizard_frm.option_value.value = jtidy(document.wizard_frm.option_label.value);
		}

		field.field_property[pos] = new Array(
			jtidy(document.wizard_frm.option_label.value),
			jtidy(document.wizard_frm.option_value.value),
			bool
		);
		field.label = jtidy(document.wizard_frm.field_label.value);
		next_page(current_page);
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function remove_option(remove_index){
	pos = field.field_property.length
	field.label = jtidy(document.wizard_frm.field_label.value);
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
	next_page(current_page);
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
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

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- radio, checkboxes buttons horizontal and vertical 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function submission_group(x,y){
	f = document.wizard_frm.submission;
	for (var index=0; index<f.length; index++){
		if (f[index].checked){
			if (f[index].value==0 || f[index].value==1){
				buildform.show_email();
			} else if (f[index].value==3){
				buildform.show_url();
			} else {
				buildform.hide_email();
			}
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
function set_default_email(index){
	if (index+'' == 'undefined'){
		for (i=0; i< wizard_frm.field_default_option.length; i++){
			if ((wizard_frm.field_default_option[i].checked+''!='') && (wizard_frm.field_default_option[i].checked+'' !='undefined') && (wizard_frm.field_default_option[i].checked+'' =='true')){
				buildform.form_emails[i][2]=true;
			} else {
				buildform.form_emails[i][2]=false;
			}
		}
	} else {
		eval('form_element = wizard_frm.field_default_option'+index+';');
		if (form_element[0].checked){
			buildform.form_emails[index][2]=true;
		} else {
			buildform.form_emails[index][2]=false;
		}
	}
	buildform.build();
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
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
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
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
	for (index = start; index< finish ; index++){
		field.field_property[index] = new Array(
			field.field_property[index+pos][0],
			field.field_property[index+pos][1],
			field.field_property[index+pos][2]
		);
	}
	field.field_property[finish] = new Array(
		temp[0],
		temp[1],
		temp[2]
	);
	next_page(current_page);
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function layout_associate(){
	
	f = user_form;
	PATH = 'file_associate.php?command=LAYOUT_RETRIEVE_LIST_MENU_OPTIONS&amp;page_menu_locations='+f.trans_menu_locations.value+"&amp;"+session_url;
	my_preview_window = window.open(PATH,'PREVIEW_WINDOW','scrollbars=yes,resizable=yes,width=750,height=550');
	my_preview_window.focus();
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function layout_save_to_doc(list, description, g_list, g_descriptions){
	window.opener.document.wizard_frm.trans_menu_locations.value		= list;
	window.opener.document.all.trans_menu_location.innerHTML			= description;
	window.close();
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function check(v,label){
	if(v){
		str = ":=========:"+label+"::1";
	} else {
		str = ":=========:"+label+"::0";
	}
	return str;
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __form_build_XML(){
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
			str +=this.form_fields[field_name].label;
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
			str += check(this.form_fields[field_name].isRequired,"required");
			str += check(this.form_fields[field_name].session_hide,"session_hide");
			str +=":===:";
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="textarea"){
			str +="textarea:=========:width::"+this.form_fields[field_name].field_property["width"]+":=========:height::"+this.form_fields[field_name].field_property["height"];
			str +=":=========:label::"+this.form_fields[field_name].label;
			str +=":=========:name::"+defined_field_name;
			str += check(this.form_fields[field_name].isRequired,"required");
			str += check(this.form_fields[field_name].session_hide,"session_hide");
			str += ':===:';
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="radio"){
			str += 'radio';
			str += ":=========:label::"+this.form_fields[field_name].label;
			str += ":=========:type::"+this.form_fields[field_name].layout_direction; 
			str += check(this.form_fields[field_name].isRequired,"required");
			str += check(this.form_fields[field_name].session_hide,"session_hide");
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
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="checkbox"){
			str +='checkboxes:=========:type::'+this.form_fields[field_name].layout_direction+':=========:label::'+this.form_fields[field_name].label;
			str +=":=========:name::"+defined_field_name;
			str += check(this.form_fields[field_name].isRequired,"required");
			str += check(this.form_fields[field_name].session_hide,"session_hide");
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
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="select"){
			str += 'select';
			str += ":=========:label::"+this.form_fields[field_name].label;
			str += check(this.form_fields[field_name].session_hide,"session_hide");
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
			str +="~~select:===:";
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="splitter"){
			str+="seperator:===:";
		}
		str +="";
	}
	xml_destination.value = unjtidy(str);
		if (this.form_fields[field_name].type=="text"){
			str +="<input type=\"text\" ";
			str +=" label=\""+this.form_fields[field_name].label+"\"";
			str +=" name=\""+defined_field_name+"\"";
			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
			str +=" size=\""+width+"\"";
			str += check(this.form_fields[field_name].isRequired,  "required");
			str += check(this.form_fields[field_name].session_hide,"session_hide");
			str +="/>:===:";
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
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
//	onSubmitCompose(2,'');
	document.wizard_frm.submit();
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- G E N E R A L   F U N C T I O N S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
- function jtidy(str);
- function unjtidy(str);
- function cdata_plain_to_rich(str);
- function cdata_rich_to_plain(str);
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/



/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: jtidy
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function is a simple tidy function to be used to strip some bad characters from the content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function jtidy(str){
	if (str+''!='' && str+''!='undefined' && str+''!='null'){
		str = encodeURI(new String(str));
		pos  = str.indexOf("&");
		splitter ="&amp;";
		while (pos !=-1){
			str = str.substring(0,pos) + splitter + str.substring(pos+1);
			pos  = str.indexOf("&", pos + splitter.length);
		}
		
		find = new Array(
				new Array("\?","&#63;"),
				new Array("'","&#39;"),
				new Array("<","&#60;"),
				new Array(">","&#62;"),
				new Array('"',"&#34;"),
				new Array('€',"&#8364;")
			);
		for(index=0;index<find.length;index++){
			while (str.indexOf(find[index][0])!=-1){
				str = str.replace(find[index][0],find[index][1]);
			}
		}
	}else{
		str = '';
	}
	return str;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: unjtidy
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function is a simple untidy function to be used to fix tags back togethor also decodes content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function unjtidy(str){
	str = decodeURI(str);
	find = new Array(
			new Array("&#60;","<"),
			new Array("&#62;",">"),
			new Array("\r",""),
			new Array("\n","")
	);
	for(index=0;index<find.length;index++){
		while (str.indexOf(find[index][0])!=-1){
			str = str.replace(find[index][0],find[index][1]);
		}
	}
	return str;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function cdata_plain_to_rich(str){
	str = decodeURI(str);
	find = new Array(
			new Array("\n","<br />")
	);
	for(index=0;index<find.length;index++){
		while (str.indexOf(find[index][0])!=-1){
			str = str.replace(find[index][0],find[index][1]);
		}
	}
	return str;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function cdata_rich_to_plain(str){
	str = decodeURI(str);
	find = new Array(
			new Array("<br />","\n"),
			new Array("<br/>","\n"),
			new Array("<br>","\n"),
			new Array("&#60;br /&#62;","\n"),
			new Array("&#60;br/&#62;","\n"),
			new Array("&#60;br&#62;","\n")
	);
	for(index=0;index<find.length;index++){
		while (str.indexOf(find[index][0])!=-1){
			str = str.replace(find[index][0],find[index][1]);
		}
	}
	return str;
}
