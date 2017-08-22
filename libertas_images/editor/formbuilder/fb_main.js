/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- F O R M   B U I L D E R   J A V A S C R I P T   F I L E 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-
- Author: Adrian Sweeney
- Company: Libertas Solutions
- Copyright: 2004
- Created: 23th Jan 2004 
-
- Main functionality for the form builder requires that the fb_form.js and fb_field.js are loaded as well
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- No copying, modifing, reproducing without the consent of Libertas Solutions
- Libertas Solutions does not supply any warranty with the use of this software.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	Modified $Date: 2004/08/25 07:34:36 $
-	$Revision: 1.2 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	Configuration options
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	var SHOW_SESSION_HIDE 		= 0;
	var SHOW_REQUIRED_FIELD 	= 1;
	var formRequiresReDraw 		= true;
	var previewRequiresReDraw 	= true;
	var optionLabel 			= '';
	var current_page			= 0;
	var advanced_form_builder	= false;
	var field;
	debug_var("field");
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- F O R M   B U I L D E R   D I S P L A Y   F U N C T I O N S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
- most of these functions will actually reference the form and field classes rather than use methods
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-


-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: __return_field(data)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will either add a new field to the end of the field list or overwrite a field with modified 
- values
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __return_field(data){
	/*
	if (data.fieldName==""){
		pos = buildform.form_fields.length;
	} else {
		pos = data.fieldName.toString().substr(5);
	}
	*/
	len = buildform.form_fields.length;
	ok = -1;
	for (i=0; i<len ; i++){
//		alert("compare "+buildform.form_fields[i].fieldName+" and "+data.fieldName);
		if (buildform.form_fields[i].fieldName == data.fieldName){
			ok = i;
		}
	}
//	alert("ok = "+ok)
	if (ok == -1){
		if((this.form_edit_field_index >= 0) && (buildform.form_url != '')) {
			pos = this.form_edit_field_index;
			this.form_edit_field_index = -1;
		}
		else
			pos = len;
	} else {
		pos = ok;
	}
	buildform.form_fields[pos] = new form_field();
	buildform.form_fields[pos].clone(data);
	if (ok==-1){
		if (data.fieldName != '') {
			buildform.form_fields[pos].fieldName = data.fieldName;
			buildform.form_fields[pos].name = data.fieldName;			
		}
		else {
			n = getFieldName(pos)
			buildform.form_fields[pos].fieldName=n;
			buildform.form_fields[pos].name=n;
		}
	}
/*	if (buildform.form_url!=""){
	} else {
		buildform.form_fields[pos].name="";
	}*/
	//buildform.build();
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
	breakpoint("calling FN:: next_page("+page+")");
	if (page<0){
		field = new form_field("");
		field.clone(buildform.form_fields[(page * -1)-1]);
		//alert(field.fieldName);
		if (field.fieldName==""){
			field.fieldName = getFieldName(((page * -1)-1))
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
			Array("text.gif","text","typeText",LOCALE_MSG_ADD_TEXT_LABEL),
			Array("textarea.gif", "textarea", "typeTextArea", LOCALE_MSG_ADD_TEXTAREA_LABEL),
			Array("radio.gif", "radio", "typeRadio", LOCALE_MSG_ADD_RADIO_LABEL),
			Array("checkbox.gif", "checkbox", "typeCheckBox", LOCALE_MSG_ADD_CHECK_LABEL),
			Array("select.gif", "select", "typeSelect", LOCALE_MSG_ADD_SELECT_LABEL),
			Array("splitter.gif", "splitter", "typeSplitter", LOCALE_MSG_ADD_SPLITTER_LABEL),
			Array("row_splitter.gif", "row_splitter", "typeRowSplitter", LOCALE_MSG_ADD_ROW_SPLITTER_LABEL),
			Array("msg.gif", "cdata", "typeCdata", LOCALE_MSG_ADD_TXT_MSG)/*,
			Array("datetime.gif", "date_time", "typeDateTime", LOCALE_MSG_ADD_DATETIME)*/
		);
		f = document.wizard_frm.submission;
		if (f+''=='undefined'){
			show_subject=true;
		}else{
			if (f.length+'' == 'undefined'){
				show_subject=true;
			} else {
				for (var index=0; index<f.length; index++){
					if (f[index].checked){
						if (f[index].value==0 || f[index].value==1){
							show_subject=true;
						} else {
							show_subject=false;
						}
					}
				}
			}
		}
		if (show_subject){
				myOptions[myOptions.length] = Array("subject.gif","subject","typeSubject",LOCALE_SUBJECT_LINE)
		}
		if (maximumAccess=='ECMS'){
			myOptions[myOptions.length]=Array("hidden.gif","hidden","typeHidden",LOCALE_MSG_ADD_HIDDEN_LABEL);
//			myOptions[myOptions.length]=Array("fileupload.gif", "fileupload", "typeFileUpload", LOCALE_MSG_ADD_FILE_UPLOAD);
		}
		str  = '<h1>'+LOCALE_WIZARD_PAGE+' 1</h1>';
		str += LOCALE_MSG_ADD_FIELD;
		str += '<input type="hidden" name="page" value="1">';
		str += '<p>'
		for(var i =0; i <myOptions.length;i++){
			str += '<img src="/libertas_images/editor/formbuilder/images/'+myOptions[i][0]+'" width="24" height="24" border="0"/> <input type="radio" id="'+myOptions[i][2]+'" name="typeSelected" value="'+myOptions[i][1]+'"';
			if (field.type==myOptions[i][1] || (i==0 && field.type=='')){
				str += ' checked';
			}
			str += '/> <label for="'+myOptions[i][2]+'">'+myOptions[i][3]+'</label><br />\n';
		}
		str += '<input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
		LIBERTAS_GENERAL_printToId("wizard",str);
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
				if (document.wizard_frm.typeSelected[i].checked && document.wizard_frm.typeSelected[i].value=="row_splitter"){
					field.type = "row_splitter";
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
			str  = '<h1>'+LOCALE_WIZARD_PAGE+' 2</h1><input type="hidden" name="previous_label" value="'+field.label+'">';
			
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
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+LIBERTAS_GENERAL_unjtidy(field.label)+'"/></td></tr>';
					str += '</table>';
					str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
					break;
				case "subject":
					if (field.field_property["field_format"]+"" == 'undefined'){
						field.field_property["field_format"] = "select";
					}
					str += '<input type="hidden" name="page" value="2">';
					fname = (field.name==''? field.fieldName :field.name);
					str += '<input type="hidden" id="field_name" name="field_name" maxlength="255" size="50" value="'+fname+'"/>';
					str += LOCALE_SUBJECT_INTRO;
					str += '<table>';
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+LIBERTAS_GENERAL_unjtidy(field.label)+'"/></td></tr>';
					str += '<tr><td valign="top"><label for="field_format"><strong>'+LOCALE_SUBJECT_FORMAT+'</strong></label> </td><td valign="top"><select id="field_format" name="field_subject_format" onchange="javascript:toggleemail(this)">';
					str += '<option value="select"';
					if (field.field_property["field_format"] == "select"){
						str += ' selected';
					}
					str += '>'+LOCALE_MSG_ADD_SELECT_LABEL+'</option>';
					str += '<option value="radio"';
					if (field.field_property["field_format"] == "radio"){
						str += ' selected';
					}
					str += '>'+LOCALE_MSG_ADD_RADIO_LABEL+'</option>';
					str += '<option value="checkbox"';
					if (field.field_property["field_format"] == "checkbox"){
						str += ' selected';
					}
					str += '>'+LOCALE_MSG_ADD_CHECK_LABEL+'</option>';
					str += '</select></td></tr>';
					str += '<tr><td colspan="2" id="field_list_of_emails"></td></tr>';
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
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+LIBERTAS_GENERAL_unjtidy(field.label)+'"/></td></tr>';
					str += '<tr><td valign="top"><label for="dateType"><strong>'+LOCALE_DATETYPE+'</strong></label></td><td><select name="dateType" id="dateType">';
					str += '<option ';
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
					str += '<tr><td valign="top" colspan=2><textarea type="text" id="field_label" name="field_label" cols="55" rows="15">'+LIBERTAS_GENERAL_cdata_rich_to_plain(field.label)+'</textarea></td></tr>';
					str += '</table>';
					str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
					break;
				case "splitter":
					break;
				case "row_splitter":
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
						field.field_property["width"]="255";
					}
					str += LOCALE_MSG_ADD_TEXT_BOX;
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+LIBERTAS_GENERAL_unjtidy(field.label)+'"/></td></tr>';
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
						field.field_property["width"]="20";
					}
					if (field.field_property["height"]+"" == 'undefined'){
						field.field_property["height"]="6";
					}
					str += LOCALE_INPUT_TEXTAREA_BOX;
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+LIBERTAS_GENERAL_unjtidy(field.label)+'"/></td></tr>';
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
					break;
				case "radio":
					field.field_property["width"]=null;
					field.field_property["height"]=null;
					str += LOCALE_INPUT_RADIO_BOX;
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
					str += '<tr><td valign="top"><label for="field_label_id"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label_id" name="field_label" maxlength="255" size="50" value="'+LIBERTAS_GENERAL_unjtidy(field.label)+'"/></td></tr>';
					str += '<tr><td valign="top"><strong><label for="required_yes">'+LOCALE_HAS_OTHER+'</label></strong></td><td>';
					str += '<input type="checkbox" id="other" name="other" value="1"';
					if (get_attribute(field.attributes,"other")+""=="1"){
						str += ' checked';
					}
					str += '/> <label for="other_label"> Label :: </label><input id="other_label" type="text" name="other_label" value="'+LIBERTAS_GENERAL_unjtidy(get_attribute(field.attributes,"other_label"))+'"/>';
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
					str += '<tr><td colspan="2" valign="top"><span id ="display_options">'+show_options(1)+'</span></td></tr>';
					str += '</table>';
					str += '<p><input class="bt" type="button" value="'+LOCALE_PREVIOUS+'" onclick="javascript:next_page('+(page-1)+');"/><input class="bt" type="button" value="'+LOCALE_NEXT+'" onclick="javascript:next_page('+(page+1)+');"/></p>';
					break;
				case "checkbox":
					field.field_property["width"]=null;
					field.field_property["height"]=null;
					str += LOCALE_INPUT_CHECK_BOX;
					str += '<table>';
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+LIBERTAS_GENERAL_unjtidy(field.label)+'"/></td></tr>';
					str += '<tr><td valign="top"><strong><label for="required_yes">'+LOCALE_HAS_OTHER+'</label></strong></td><td>';
					str += '<input type="checkbox" id="other" name="other" value="1" ';
					if (get_attribute(field.attributes,"other")+""=="1"){
						str += ' checked';
					}
					str += '/> <label for="other_label"> Label :: </label><input id="other_label" type="text" name="other_label" value="'+LIBERTAS_GENERAL_unjtidy(get_attribute(field.attributes,"other_label"))+'"/>';
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
					break;
				case "select":
					str += LOCALE_INPUT_SELECT_BOX;
					str += '<input type="hidden" name="page" value="2">';
					str += '<table>';
					if (buildform.form_url != ''){
						str += '<tr><td valign="top"><label for="field_name"><strong>'+LOCALE_ELEMENT_NAME+'</strong></label> </td><td valign="top"><input type="text" id="field_name" name="field_name" maxlength="255" size="50" value="'+field.name+'"/></td></tr>';
					}
					str += '<tr><td valign="top"><label for="field_label"><strong>'+LOCALE_ELEMENT_LABEL+'</strong></label> </td><td valign="top"><input type="text" id="field_label" name="field_label" maxlength="255" size="50" value="'+LIBERTAS_GENERAL_unjtidy(field.label)+'"/></td></tr>';
					str += '<tr><td valign="top"><strong><label for="required_yes">'+LOCALE_HAS_OTHER+'</label></strong></td><td>';
					str += '<input type="checkbox" id="other" name="other" value="1" ';
					if (get_attribute(field.attributes,"other")+""=="1"){
						str += ' checked';
					}
					str += '/> <label for="other_label"> Label :: </label><input id="other_label" type="text" name="other_label" value="'+LIBERTAS_GENERAL_unjtidy(get_attribute(field.attributes,"other_label"))+'"/>';
					str += '</td></tr>';
					str += '<tr><td valign="top"><strong><label for="required_yes">'+LOCALE_SELECT_MULTIPLE+'</label></strong></td><td>';
					str += '<p><input type="checkbox" id="multiple_yes" name="isMultiple" value="1" ';
					if (get_attribute(field.attributes,"multiple")=="1"){
						str += ' checked';
					}
					str += '/>';
					str += '</td></tr>';
					str += '<tr><td>'+LOCALE_INPUT_HEIGHT+':</td><td><input type="text" name=field_height size=3 maxlength=3 value="'+get_attribute(field.attributes,"size")+'"></td><tr>';
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
			LIBERTAS_GENERAL_printToId("wizard",str);
			if(field.type=="subject"){
				buildform.show_email();
			}
		}
		
	}
	if(page==3){
		breakpoint();
		switch (field.type){
			case "subject":
				if (buildform.form_url != ''){
					field.fieldName			 		 = LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_name.value);
				}
				p_label 							= field.label;
				field.label 						= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
				check_email(p_label,field.label);
				field.isPropertyArray 				 = false;
				field.fieldValue					 = "";
				field.field_property["field_format"] = document.wizard_frm.field_subject_format.options[document.wizard_frm.field_subject_format.selectedIndex].value;
				break;
			case "splitter":
				break;
			case "row_splitter":
				break;
			case "cdata":
				if (buildform.form_url != ''){
					field.fieldName	= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_name.value);
				}
				p_label 			= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
				try{
					field.label 	= LIBERTAS_GENERAL_jtidy(LIBERTAS_GENERAL_cdata_plain_to_rich(document.wizard_frm.field_label.value));
				} catch (e){
					debug_alert("error::"+document.wizard_frm.field_label.value)
				}
				break;
			case "hidden":
				if (buildform.form_url != ''){
					field.fieldName			 				= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_name.value);
				}
				field.isPropertyArray 				= false;
				field.label							= "hidden";
				field.fieldValue					= LIBERTAS_GENERAL_jtidy(document.wizard_frm.fieldValue.value);
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
					field.fieldName			 				= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_name.value);
				}
				p_label 							= field.label;
				field.label 						= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
				check_email(p_label,field.label);
				field.isPropertyArray 				= false;
				field.fieldValue					= "";
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
					field.fieldName			 		= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_name.value);
				}
				p_label 							= field.label;
				field.label 						= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
				check_email(p_label,field.label);
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
					field.fieldName			 				= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_name.value);
				}
				p_label = field.label;
				field.label = LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
				check_email(p_label,field.label);
				field.isPropertyArray 				= false;
				field.field_property["width"]		= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_width.value);
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
				p_label 							= field.label;
				field.label 						= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
				check_email(p_label,field.label);
				if (buildform.form_url != ''){
					field.fieldName			 				= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_name.value);
				}
				field.isPropertyArray 				= false;
				field.field_property["width"]		= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_width.value);
				field.field_property["height"]		= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_height.value);
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
				p_label 							= field.label;
				field.label 						= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
				check_email(p_label,field.label);
				if (buildform.form_url != ''){
					field.fieldName			 				= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_name.value);
				}
				field.attributes = new Array();
				field.attributes[0]=Array("other",(document.wizard_frm.other.checked)?1:0);
				field.attributes[1]=Array("other_label",LIBERTAS_GENERAL_jtidy(document.wizard_frm.other_label.value));
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
				p_label 							= field.label;
				field.label 						= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
				check_email(p_label,field.label);
				if (buildform.form_url != ''){
					field.fieldName			 				= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_name.value);
				}
				field.attributes = new Array();
				field.attributes[0]=Array("other",(document.wizard_frm.other.checked)?1:0);
				field.attributes[1]=Array("other_label",LIBERTAS_GENERAL_jtidy(document.wizard_frm.other_label.value));
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
				debug("here");
				p_label 							= field.label;
				field.label 						= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
				check_email(p_label,field.label);
				if (buildform.form_url != ''){
					field.fieldName			 				= LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_name.value);
				}
				field.isPropertyArray 				= false;
				debug("Attributes");
				debug("Attributes ["+document.wizard_frm.other.checked+"]");
				field.attributes = new Array();
				field.attributes[0]=Array("multiple",(document.wizard_frm.isMultiple.checked)?1:0);
				field.attributes[1]=Array("size",LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_height.value));
				field.attributes[2]=Array("other",(document.wizard_frm.other.checked)?1:0);
				field.attributes[3]=Array("other_label",LIBERTAS_GENERAL_jtidy(document.wizard_frm.other_label.value));
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
		str  = '<h1>Form builder field wizard page 3</h1>';
		str += '<table>';
		str += '<input type="hidden" name="page" value="3">';
		str += '<p>'+LOCALE_FIELD_THANKS+'</p>';
		str += '<p><input class="bt" type="button" value="'+LOCALE_BACK+'" onclick="javascript:next_page(0);"/><input class="bt" type="button" value="'+LOCALE_JUMP_TO_RANKING+'" onclick="javascript:buildform.build();show_tabular_screen(\'tab_3\');"/><input class="bt" type="button" value="'+LOCALE_JUMP_TO_PREVIEW+'" onclick="javascript:buildform.preview();show_tabular_screen(\'tab_4\');"/></p>';
		LIBERTAS_GENERAL_printToId("wizard",str);
		__return_field(field);
		formRequiresReDraw = true;
		previewRequiresReDraw = true;
		field=null;
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

function show_options(returnType , indexToEdit){
	if (indexToEdit+''=='undefined'){
		var indexToEdit = -1;
	} else {
	 	var indexToEdit = indexToEdit;
	}
	var str = '<table border="0">';
	str += '	<tr class="bt"><td class="bt">'+LOCALE_INPUT_LABEL+'</td>'
	if (advanced_form_builder){
		str+='<td class="bt">'+LOCALE_INPUT_VALUE+'</td>';
	}
	if ((field.type=="radio") || (field.type=="checkbox") || (field.type=="select")){
		str += '<td class="bt">'+LOCALE_DEFAULT+'</td>';
	}
	str += '<td class="bt" colspan="4">'+LOCALE_OPTIONS+'</td></tr>';
	for (var i=0; i<field.field_property.length;i++){
		if (indexToEdit == i){
			str += '<tr><td><input type="text" id="update_option_label" name="update_option_label" value="'+LIBERTAS_GENERAL_unjtidy(field.field_property[i][0])+'" size="15" maxlength="255"/></td>';
			if (advanced_form_builder){
				str += '<td><input type="text" id="update_option_value" name="update_option_value" value="'+LIBERTAS_GENERAL_unjtidy(field.field_property[i][1])+'" size="15" maxlength="255"/></td>';
			}
			if(field.type=="radio" || field.type=="select"){
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
			str += '<td colspan="4"><input class="bt" type="button" value="'+LOCALE_UPDATE+'" onclick="javascript:update_options(this,'+i+')"/></td>';
			str += '</tr>';
		} else {
			str += '<tr><td>'+LIBERTAS_GENERAL_unjtidy(field.field_property[i][0])+'</td>';
			if (advanced_form_builder){
				str += '<td>'+LIBERTAS_GENERAL_unjtidy(field.field_property[i][1])+'</td>';
			}
			if(field.type=="radio" || field.type=="select"){
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
			str += '<td><input class="bt" type="button" value="'+LOCALE_EDIT+'" onclick="javascript:show_options(1,'+i+')"/></td>';
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
	}
	cspan=5;
		if (advanced_form_builder){
			cspan++;	
		}
		if(field.type=="radio" || field.type=="select"){
			cspan++;	
		}
		if(field.type=="checkbox"){
			cspan+=2;	
		}
	
	str += '<tr><td colspan="'+cspan+'"><hr></td></tr>';
	
	str += '<tr><td><input type="text" maxlength="255" size="15" name="option_label" id="option_label"></td>';
	if (advanced_form_builder){
		str += '<td><input maxlength="255" size="15" type="text" name="option_value" id="option_value" onclick="javascript:copy_label(this);" onfocus="javascript:copy_label(this);" onblur="javascript:copy_label(this);">';
	} else {
		str += '<input type="hidden" name="option_value" id="option_value">';
	}
	str += '</td><td><input class="bt" type=button onclick="javascript:add_option();" value="'+LOCALE_INPUT_ADD_OPTION+'"></td></tr>';
	str += '</table>';
	if (indexToEdit!=-1){
		LIBERTAS_GENERAL_printToId("display_options",str);
	} else {
		if (returnType==1){
			return str;
		} else {
			LIBERTAS_GENERAL_printToId("display_options",str);
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
- FN:: update_options(index)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function is designed to add a new option to a fields option list (Select, Checkbox and Radio)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function update_options(t, index){
	for (var x=0;x<document.wizard_frm.elements.length;x++){
		if (document.wizard_frm.elements[x].name=="update_option_label"){
			field.field_property[index][0]=document.wizard_frm.elements[x].value
		} else if (document.wizard_frm.elements[x].name=="update_option_value"){
			document.wizard_frm.elements[x].value = field.field_property[index][0];
		}
	}
//	alert(t.form.elements["update_option_value"].name);
//	alert(document.getElementById("update_option_value").value);
//	 = LIBERTAS_GENERAL_jtidy(document.wizard_frm.update_option_value.value);
//	field.field_property[index][1] = LIBERTAS_GENERAL_jtidy(document.wizard_frm.update_option_label.value);
	show_options(1, -2);
}/*
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
		copy_label();
		var pos = field.field_property.length
		if (pos==0){
			field.field_property = new Array();
		}
		if (pos==0 && (	field.type=="radio" || field.type=="select")){
			var bool 	= true;
		}else{
			var bool	= false;
		}
		if (document.wizard_frm.option_value.value.length==0){
			document.wizard_frm.option_value.value = LIBERTAS_GENERAL_jtidy(document.wizard_frm.option_label.value);
		}

		field.field_property[pos] = new Array(
			LIBERTAS_GENERAL_jtidy(document.wizard_frm.option_label.value),
			LIBERTAS_GENERAL_jtidy(document.wizard_frm.option_value.value),
			bool
		);
		field.label = LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
		if (document.wizard_frm.isRequired+"" !="undefined")
			field.isRequired 				= document.wizard_frm.isRequired.checked;
		else 
			field.isRequired 				= false;
		show_options(2 , -1);
//		next_page(current_page);
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function remove_option(choosen_index_to_remove){
	var choosen_index_to_remove = choosen_index_to_remove * 1;
	var pos = field.field_property.length
	try{
		field.label = LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value);
		if (choosen_index_to_remove < pos){
			field.field_property.splice(choosen_index_to_remove,1)
		}
	}catch(e){
		debug_alert("FN remove_option(remove_index)",1,"remove_index :: "+choosen_index_to_remove+"\nCurrent lenght of elements ::"+pos+"\nLabel :: "+LIBERTAS_GENERAL_jtidy(document.wizard_frm.field_label.value));
	}
	try{
		next_page(current_page);
	} catch(e){
		debug_alert("FN Calling next_page from remove_option");
	}
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
		for (var i=0; i< document.wizard_frm.field_default_option.length; i++){
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
				buildform.show_email_option();
				next_page(0);
			} else if (f[index].value==3){
				buildform.show_url();
				next_page(0);
			} else if (f[index].value==4){
				buildform.show_url();
				next_page(0);
			} else {
				buildform.hide_email();
				next_page(0);
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
		formRequiresReDraw = true;
		previewRequiresReDraw = true;
	//buildform.build();
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __form_move_option(from, to, ignoreParameter){
	if (from<to){
		var start	= from;
		var finish	= to;
		var pos = +1;
	} else {
		var start	= to;
		var finish	= from;
		var pos = +1;
	}
	var temp = new form_field();
	temp.clone(buildform.form_fields[start]);
	for (var index = start; index < finish ; index ++){
		buildform.form_fields[index] = new form_field();
		buildform.form_fields[index].clone(buildform.form_fields[index+1]);
	}
	buildform.form_fields[finish] = new form_field();
	buildform.form_fields[finish].clone(temp);
	previewRequiresReDraw = true;
	// only rebuild the moved items. :) much faster as only two will have changed
	buildform.build(start, finish);
	formRequiresReDraw = false;
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
- FN:: fb_generate_field_list()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- build the list of links that can insert special tags into content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function fb_generate_field_list(){
	var str = "";
	total_length = buildform.form_fields.length;
	for(var index=0;index<total_length;index++){
		if ((buildform.form_fields[index].type!="splitter") && (buildform.form_fields[index].type!="subject") && (buildform.form_fields[index].type!="row_splitter") && (buildform.form_fields[index].type!="cdata"))
		str += "<div class='fieldlist' onmouseover='javascript:this.className=\"sfieldlist\"' onmouseout='javascript:this.className=\"fieldlist\"'><a class='btbtn' href='javascript:LIBERTAS_paste_Text(\"emailscreen\",this,\"" + tidy_quotes(buildform.form_fields[index].label) + "\");'>"+decodeURI(buildform.form_fields[index].label)+"</a></div>";
	}
	LIBERTAS_GENERAL_printToId("email_msg_list_of_fields", str);
}

function FIELD_TEXT_add_to_msg(msg){
	document.wizard_frm.emailscreen.focus();		
	var selection = document.wizard_frm.emailscreen.selection.createRange();
	selection.pasteHTML('asdf');
}

function check_email(previous_label, new_label){
	debug_alert("check_email :: "+previous_label+", "+new_label);
	previous_label = untidy_quotes(previous_label);
	new_label = untidy_quotes(new_label);
	debug("replace [["+decodeURI(LIBERTAS_GENERAL_unjtidy(previous_label))+"]] with [["+decodeURI(LIBERTAS_GENERAL_unjtidy(new_label))+"]]");
	debug("before \n\n\n"+document.all['emailscreen'].value);
	document.all['emailscreen'].value = untidy_quotes(document.all['emailscreen'].value).split("[["+decodeURI(LIBERTAS_GENERAL_unjtidy(previous_label))+"]]").join("[["+decodeURI(LIBERTAS_GENERAL_unjtidy(new_label))+"]]");
	debug("\n\n\nafter \n\n\n"+document.all['emailscreen'].value);
}

function get_attribute(arr,key){
	for (zindex=0;zindex <arr.length;zindex++){
		if (arr[zindex][0]+"" == key){
			return arr[zindex][1]
		}
	}
	return "";
}

function getFieldName(fname){
	len = buildform.form_fields.length;
	ok = true;
	for(var i =0; i<len;i++){
		if (buildform.form_fields[i].fieldName == "field"+fname){
			ok = false
		}
	}
	if (ok) 
		return "field"+fname;
	else 
		return getFieldName((fname+1));
}
