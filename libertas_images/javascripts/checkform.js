/*
	if the form has any required fields then execute this function
*/
function show_tabular_screen(tab, hidelist){
	for(i=0;i < screen_tabs.length;i++){
		if (tab==screen_tabs[i]){
			document.getElementById(screen_tabs[i]).style.visibility='visible';
			document.getElementById(screen_tabs[i]).style.display='';
			document.getElementById('btn_'+screen_tabs[i]).className='buttonOn';
		} else {
			document.getElementById(screen_tabs[i]).style.visibility='hidden';
			document.getElementById(screen_tabs[i]).style.display='none';
			document.getElementById("btn_"+screen_tabs[i]).className='buttonOff';
		}
	}
	if (hidelist instanceof Array ){
		for (h=0;h < hidelist.length; h++){
			document.getElementById(hidelist[h]+"_btn").style.visibility='hidden';
			document.getElementById(hidelist[h]+"_btn").style.display='none';
//			document.getElementById(hidelist[h]+"_spacer").style.visibility='hidden';
//			document.getElementById(hidelist[h]+"_spacer").style.display='none';
		}
	} else {
		if (hidelist+''!='undefined'){
			document.getElementById(hidelist+"_btn").style.visibility='hidden';
			document.getElementById(hidelist+"_btn").style.display='none';
//			document.getElementById(hidelist+"_spacer").style.visibility='hidden';
//			document.getElementById(hidelist+"_spacer").style.display='none';
		}
	}
}

/*
	check_required_fields(type)
*/
function check_required_fields(rt){
	var return_type=1;
	var f = get_form();
	if ((rt+"" == "undefined") || (rt+"" == "0")){
		return_type = 0;
	} else {
		return_type = rt;
	}
	ok = 1;

	if (selectdisabled){
		/*
			if the form has any required fields then execute this function
		*/
		for(index=0;index < check_not_allowed.length ; index++){
			if (f.elements[selectfield].options[f.elements[selectfield].selectedIndex].value == check_not_allowed[index]){
				ok = 0;
				alert("You do not have publishing access to this location");
			}
		}
	}
	/*
		if the form has any required fields then execute this function
	*/
	/** Check if any of the fields need to be merged to title. if so then merge them */
	// start of add to title	
	/*
	for(index=0;index < fld_addtotitle.length ; index++){
		eval("el = f."+fld_addtotitle[index]);
		if (el+"" == "undefined") {
			eval("el = f.elements['"+fld_addtotitle[index]+"[]']");			
		}
		if (el.type == 'text'){
			f.ie_title.value += " " + el.value;
		}
		else {
			for (i=0;i < el.options.length;i++) {
				if(el.options[i].selected)
					f.ie_title.value += " " + el.options[i].value;
			}
		}

	}
	*/
	// end of add to title
	if (ok==1){
		for(index=0;index < check_required.length ; index++){
			eval("el = f."+check_required[index][0]);
			if (el.length+""=="undefined"){
				eval("len = f."+check_required[index][0]+".value.length;");
				if (len==0){
					ok=0;
					alert("You have not filled in the '"+check_required[index][1]+"' field");
					eval("cando = f."+check_required[index][0]+".type;");
					if (cando!='hidden'){
						show_tabular_screen(check_required[index][2]);
						eval("f."+check_required[index][0]+".focus();");
					}
					break;
				}
			} else {
				ok=0;
				for(z=0;z<el.length;z++){
					if(el.type=="select-one"){
						if (el[z].selected){
							ok=1;
						}
					} else {
						if (el[z].checked){
							ok=1;
						}
					}
				}
				if(ok==0){
					alert("You have not filled in the '"+check_required[index][1]+"' field");
					show_tabular_screen(check_required[index][2]);
					break;
				}
			}
		}		
	}
	/*
		if the form has any editors then execute this function
	*/

	if (ok==1){
		for(index=0;index < check_editors.length ; index++){
			eval("len = this['"+check_editors[index][0]+"_rEdit'].document.body.innerHTML.length;");
			if (len==0){
				ok=0;
				alert("You have not filled in the '"+check_editors[index][1]+"' field");
				show_tabular_screen(check_editors[index][2]);
				eval("this['"+check_editors[index][0]+"_rEdit'].focus();");
				break;
			}
		}		
	}

	/*
		if the form has any fields that are checkboxes then execute this function
	*/
	if (ok==1){
		for(index=0;index < check_contains_selected.length ; index++){
			eval("myelement =f.elements['"+check_contains_selected[index][0]+"[]'];");
			if (myelement+''!='undefined'){
				len = myelement.length;
				found =0
				for (i = 0 ;i < len; i++){
					if (myelement[i].checked == 1){
						found=1;
					}
				}
				if (found==0){
					alert("You have not filled in the '"+check_contains_selected[index][1]+"' field");
					show_tabular_screen(check_contains_selected[index][2]);
					myelement[0].focus();
					ok=0;
					break;
				}
			}
		}
	}		
	/*
		if there are no errors yet then check to see if there are any comparisons to be made
	*/
	if ((ok==1)&&(check_compair.length > 0)){
		for(index=0;index < check_compair.length ; index++){
			eval("value_to_be   = f."+check_compair[index][0][0]+".value;");
			eval("value_current = f."+check_compair[index][0][1]+".value;");
			if (value_to_be!=value_current){
				ok=0;
				alert("The Fields '"+check_compair[index][1][0]+"' and '"+check_compair[index][1][1]+"' do not match");
				show_tabular_screen(check_compair[index][2]);
				eval("f."+check_compair[index][0][0]+".focus();");
				break;
			}
		}		
		
	}
	/*
		Build dates if required
	*/
	for(index=0;index < check_build_dates.length ; index++){
			eval("year_select	= f."+check_build_dates[index]+"_date_year;");
			eval("month_select	= f."+check_build_dates[index]+"_date_month;");
			eval("day_select	= f."+check_build_dates[index]+"_date_day;");
			eval("hour_select	= f."+check_build_dates[index]+"_date_hour;");

			my_year		= year_select.options[year_select.selectedIndex].value;
			my_month	= month_select.options[month_select.selectedIndex].value;
			my_day		= day_select.options[day_select.selectedIndex].value;
			my_hour		= hour_select.options[hour_select.selectedIndex].value;
			if (my_year.length > 0){
				str = "f."+check_build_dates[index]+".value='"+my_year+"/"+my_month+"/"+my_day+" "+my_hour+":00:00';"
				//alert(str);
				eval(str);
			}
	}
	/*
		undisable any checkboxes used for menu locations if they exist
	*/
	try{
		for (var i=0; i<objects_to_check.length;i++){
			objects_to_check[i].enable_all();
/*		if (f.set_inheritance[0]+"" !="undefined"){
			if (f.set_inheritance[0].checked){
				enable_all();
			}
			}*/
		}
	} catch (e){
	}
	/*
	
	*/
	if (ok==1){
		for (var i = 0 ; i < check_selection.length ; i++){
			eval("test = "+check_selection[i][0]+";");
			if(!test){
				ok=0;
				alert("Please check the field '"+check_selection[i][1]+"'");
				show_tabular_screen(check_selection[i][2]);
			}
		}
	}
	/*
		decide if the user has filled in the form correctly and submit.
	*/
	debug_alert ("return type [" + return_type + "][" + ok + "]");
	if (return_type == 0){
		
		if (ok==1){
			if (tw_debug_on){
				if (confirm("transmit form information???")){
					f.submit();
				}
			} else {
				f.submit();
			}
		}
	} else {
		debug("return_type :: "+ return_type);
		if (return_type == 2){
			if (ok==1){
				LIBERTAS_UpdateFields();
				if (tw_debug_on){
					if (confirm("transmit form information???")){
						f.submit();
					}
				} else {
					f.submit();
				}
			}
		} else {
			return ok;
		}
	}
	
}

function toggle_hidden(tabName, OnOff){
	try{
		if (OnOff==1){
			document.getElementById("section_button_" + tabName + "_btn").style.visibility='visible';
			document.getElementById("section_button_" + tabName + "_btn").style.display='';
		} else {
			document.getElementById("section_button_" + tabName + "_btn").style.visibility='hidden';
			document.getElementById("section_button_" + tabName + "_btn").style.display='none';
		}
	} catch(e){}
}

function check_access_group(t, h, index){
	try{
	found = false;
	for (i = 0 ; i < document.all[t.name].length; i++){
		if (document.all[t.name][i].checked)
			found = true;
	}
	if (index=="1")
		if (!found){
			toggle_hidden("adminlocations", 0);
		} else {
			toggle_hidden("adminlocations", 1);
		}
	} catch (e){}
}

function toggle_tab(){
	var f= get_form();
	if(f.ifeature_list_type.selectedIndex==0){
		toggle_hidden("manualtab", 1);
		toggle_hidden("embedfilter", 0);
	} else if(f.ifeature_list_type.selectedIndex==1){
		toggle_hidden("manualtab", 0);
		toggle_hidden("embedfilter", 1);
	} else if(f.ifeature_list_type.selectedIndex==2){
		toggle_hidden("manualtab", 0);
		toggle_hidden("embedfilter", 1);
	}
	
}