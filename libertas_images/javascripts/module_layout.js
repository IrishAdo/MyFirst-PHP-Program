var choose_menu_list ="";
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Javascript to manage the menu navigation in layout
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function initialise_menu_nav(){
		document.all.menu_list_box.src="menu_nav.html";
		document.layout_form.menu_order.options.length=0;
		if (menu_list.length > 0){
			for (index=0; index <= menu_list[0][7];index++){
				document.layout_form.menu_order.options[document.layout_form.menu_order.options.length] = new Option(index+1,index+1);
				if (menu_list[0][9]==index+1){
					document.layout_form.menu_order.options[index].selected=true;
				}
			}
		}
	}

	function button_action(cmd){
		if (cmd=='ADD'){
			document.layout_form.menu_identifier.value='';
			document.layout_form.menu_label.value='';
			document.layout_form.menu_url.value='';
			document.layout_form.previous_url.value='';
			document.layout_form.command.value='LAYOUT_SAVE_MENU';
			document.layout_form.menu_order.options[0].selected=true;
			document.layout_form.menu_directory.options[0].selected=true;
			for (index=0;index < document.layout_form.elements['menu_group_access[]'].length;index++){
				document.layout_form.elements['menu_group_access[]'][index].checked=false;
			}
			for (index=0;index < document.layout_form.elements['menu_display[]'].length;index++){
				document.layout_form.elements['menu_display[]'][index].checked=false;
			}
			document.all.LAYOUT_REMOVE_MENU.style.visibility='hidden';
		} else if (cmd=='RANK'){
			document.layout_form.command.value='LAYOUT_PAGE_RANKING';
			if (document.layout_form.menu_identifier.value==''){
				alert('You have not selected a menu location to rank');
			}else{
				document.layout_form.submit();
			}
					
		} else if (cmd=='DIRECTORY_ADD'){
			document.layout_form.command.value='LAYOUT_SAVE_DIRECTORY';
			document.layout_form.directory_name.disabled				= false;
			document.layout_form.directory_parent.disabled				= false;
			document.layout_form.directory_identifier.value				= '';
			document.layout_form.directory_name.value					= '';
			document.layout_form.directory_parent.options[0].selected	= true;
			document.all.LAYOUT_REMOVE_DIRECTORY.style.visibility 		= 'hidden';
			document.all.LAYOUT_SAVE_DIRECTORY.style.visibility			= 'visible';
			
					
		} else if (cmd=='LAYOUT_SAVE_DIRECTORY'){
			document.layout_form.directory_name.disabled				= false;
			document.layout_form.directory_parent.disabled				= false;
			document.layout_form.command.value							= cmd;
			document.layout_form.directory_path.value 					= get_path(document.layout_form.directory_parent.options[document.layout_form.directory_parent.selectedIndex].value);
			document.layout_form.submit();
		} else {
			if(confirm("Are you sure you wish to remove this entry?")){
				document.layout_form.command.value=cmd;
				document.layout_form.submit();
			}
		}
	}

/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Javascript to manage the directory navigation in layout
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function initialise_directory_nav(){
		document.all.directory_list_box.src="admin/directory_nav.html";
		document.layout_form.directory_parent.options[0].selected	= true;
	}		
	function get_path(loc){
		var directory_structure="";
		var sub_directory_structure="";
		for (var index=0;index < directory_list.length;index++){
			if (directory_list[index][0]==loc){
				sub_directory_structure = "/"+directory_list[index][2];
				directory_structure = get_path(directory_list[index][1])+sub_directory_structure;
			}
		}
		return directory_structure;
	}
				
function layout_associate(){
	PATH = 'file_associate.php?command=LAYOUT_RETRIEVE_LIST_MENU_OPTIONS&amp;page_menu_locations='+document.forms[0].trans_menu_locations.value+"&amp;"+session_url+"&amp;page_groups="+document.forms[0].trans_groups.value;
	my_preview_window = window.open(PATH,'PREVIEW_WINDOW','//scrollbars=yes,resizable=yes,width=750,height=550');
	my_preview_window.focus();
}

function group_associate(){
	PATH = 'file_associate.php?command=GROUP_SELECT&amp;'+session_url+'&amp;page_groups='+document.forms[0].trans_groups.value;
	my_preview_window = window.open(PATH,'PREVIEW_WINDOW','scrollbars=yes,resizable=yes,width=750,height=550');
	my_preview_window.focus();
}

function layout_check_links(){
	myelement = document.publish_option_form.elements['publish_option_list[]'];
	length_of_array=myelement.length;
	document.publish_option_form.page_groups.value="";
	for (index=0;index<length_of_array;index++){
		if (myelement[index].checked){
			document.publish_option_form.page_groups.value += " "+myelement[index].value+",";
		}
	}
}

function check_groups(id){
	myelement = document.publish_option_form.elements['publish_option_list[]'];
	AS = new String(document.publish_option_form.page_groups.value);
	length_of_array=myelement.length;
	if (myelement[0].value==-1){
		if (id == 0){
			myelement[0].checked = true;
			document.publish_option_form.page_groups.value=-1;
			for (index=1;index<length_of_array;index++){
				myelement[index].checked = false;
			}
		} else {
			myelement[0].checked = false
			document.publish_option_form.page_groups.value="";
			for (index=1;index<length_of_array;index++){
				if (myelement[index].checked){
					document.publish_option_form.page_groups.value += " "+myelement[index].value+",";
				}
			}
		}
	} else {
		document.publish_option_form.page_groups.value="";
		for (index=0;index<length_of_array;index++){
			if (myelement[index].checked){
				document.publish_option_form.page_groups.value += " "+myelement[index].value+",";
			}
		}
	}
}


function layout_return_to_doc(){
	window.location="?command=LAYOUT_RETRIEVE_LIST_MENU_OPTIONS_DETAIL&amp;page_menu_locations="+document.publish_option_form.page_menu_locations.value;
}
function layout_save_to_doc(list, description, g_list, g_descriptions){
	//layout_check_links();
	window.opener.document.user_form.trans_menu_locations.value	= list;
//	window.opener.document.user_form.trans_groups.value= document.publish_option_form.page_groups.value;
/*
	if (g_descriptions.length==0){
		des = "<ul><li>No access restrictions apply</li></ul>";
	}else{
		len =  g_descriptions.length;
		des = "<ul>";
		for(index=0;index<g_descriptions.length;index++){
			pos = new String(" "+document.publish_option_form.page_groups.value).indexOf(" "+g_descriptions[index][0]+",");
			if (pos>0){
				des += g_descriptions[index][1];
			}
		}
		des += "</ul>";
	}

	str = des;
	window.opener.document.all.trans_group_information.innerHTML		= str;
	*/
	window.opener.document.all.trans_menu_location.innerHTML			= description;
	
	window.close();
}

