/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	Modified $Date: 2004/08/25 07:34:35 $
-	$Revision: 1.2 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function old__form_build_XML(){
	max_fields 		= this.form_fields.length;
	str 			= "<seperator>";
	group_list		= new String();
	required_list	= new String();
	
	str +="<input type=\"hidden\" ";
	str +=" name=\"number_of_fields\"";
	str +=" value=\""+this.form_fields.length+"\"";
	str +="/>\n";
	defined_field_name="";
	for (field_name in this.form_fields){
		if (this.form_fields[field_name].fieldName!=''){
			defined_field_name = this.form_fields[field_name].fieldName;
		} else {
			defined_field_name = this.form_fields[field_name].name;
		}
		if (this.form_fields[field_name].type=="hidden"){
			str +="<input type=\"hidden\" ";
			str +=" name=\""+defined_field_name+"\"";
			str +=" value=\""+this.form_fields[field_name].fieldValue+"\"";
			str +="/>\n";
		}
		if (this.form_fields[field_name].type=="cdata"){
			str +="<text><![CDATA[";
			str +=this.form_fields[field_name].label;
			str +="]]

></text>";
		}
		if (this.form_fields[field_name].type=="text"){
			str +="<input type=\"text\" ";
			str +=" label=\""+this.form_fields[field_name].label+"\"";
			str +=" name=\""+defined_field_name+"\"";
			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
			str +=" size=\""+width+"\"";
			str += check(this.form_fields[field_name].isRequired,"required")
			str += check(this.form_fields[field_name].session_hide,"session_hide")
			str +="/>\n";
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="textarea"){
			str +="<textarea size=\""+this.form_fields[field_name].field_property["width"]+"\" height=\""+this.form_fields[field_name].field_property["height"]+"\"";
			str +=" label=\""+this.form_fields[field_name].label+"\"";
			str +=" name=\""+defined_field_name+"\"";
			str += check(this.form_fields[field_name].isRequired,"required")
			str += check(this.form_fields[field_name].session_hide,"session_hide")
			str += '></textarea>\n';
			if (this.form_fields[field_name].isRequired){
				if (required_list.length!=0){
					required_list+=",";
				}
				required_list +=""+field_name;
			}
		}
		if (this.form_fields[field_name].type=="radio"){
			str += '<radio';
			str += " label=\""+this.form_fields[field_name].label+"\"";
			str += " type=\""+this.form_fields[field_name].layout_direction+"\""; 
			str += check(this.form_fields[field_name].isRequired,"required")
			str += check(this.form_fields[field_name].session_hide,"session_hide")
			str +=" name=\""+defined_field_name+"\">\n";
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					str +="<option value=\""+info[1]+"\"";
					if (info[2]==true){
						str += " checked=\"true\"";
					}
					str+= '><![CDATA['+info[0]+']]

></option>\n';
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
			str += '<checkboxes type="'+this.form_fields[field_name].layout_direction+'" label="'+this.form_fields[field_name].label+'"';
			str +=" name=\""+defined_field_name+"\"";

			str += check(this.form_fields[field_name].isRequired,"required")
			str += check(this.form_fields[field_name].session_hide,"session_hide")
			str += '>\n';
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					str +="<option value=\""+info[1]+"\"";
					if (info[2]==true){
						str += " checked=\"true\"";
					}
					str+= '><![CDATA['+info[0]+']]

></option>\n';
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
			str += " label=\""+this.form_fields[field_name].label+"\"";
			if (this.form_fields[field_name].session_hide){
				str +=" session_hide=\""+this.form_fields[field_name].session_hide+"\"";
			}
			str +=" name=\""+defined_field_name+"\">\n";
			for (index in this.form_fields[field_name].field_property){
				info = this.form_fields[field_name].field_property[index];
				if (info+''!=''){
					str +="<option value=\""+info[1]+"\"";
					if (info[2]==true){
						str += " checked=\"true\"";
					}
					str+= '><![CDATA['+info[0]+']]

></option>\n';
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
		if (this.form_fields[field_name].type=="splitter"){
			str+="</seperator>";
			str+="<seperator>";
		}
		str +="";
	}
	str+="</seperator>";
	xml_destination.value = unjtidy(str);
		if (this.form_fields[field_name].type=="text"){
			str +="<input type=\"text\" ";
			str +=" label=\""+this.form_fields[field_name].label+"\"";
			str +=" name=\""+defined_field_name+"\"";
			width = (this.form_fields[field_name].field_property["width"]+""=="undefined")? "20" : this.form_fields[field_name].field_property["width"]+""; 
			str +=" size=\""+width+"\"";
			str += check(this.form_fields[field_name].isRequired,"required")
			str += check(this.form_fields[field_name].session_hide,"session_hide")
			str +="/>\n";
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
	onSubmitCompose(2,'');
//		document.wizard_frm.submit();
}
