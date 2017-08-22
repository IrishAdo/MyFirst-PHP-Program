/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- contact javascript functions
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function contact_associate(destination){
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- open popup window
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	if (destination=="metadata_creator"){
		PATH = 'file_associate.php?command=CONTACT_LIST_SELECTION&amp;'+session_url+'&amp;destination='+destination+'&amp;associated_list='+document.user_form.metadata_creator_associations.value;
	} else {
		PATH = 'file_associate.php?command=CONTACT_LIST_SELECTION&amp;'+session_url+'&amp;destination='+destination+'&amp;associated_list='+document.user_form.metadata_contributor_associations.value;
	}
	my_preview_window = window.open(PATH,'PREVIEW_WINDOW','scrollbars=yes,resizable=yes,width=750,height=550');
	my_preview_window.focus();
}

function contact_check_links(){
	s=new String(document.associated_files_form.associated_list.value).split(" ").join("");
	AS = new String(s).split(",");
	AS.length--;
	myelement = document.display_list.elements['file_list[]'];
	length_of_array=myelement.length;
	if (length_of_array+""=="undefined"){
		length_of_array=1;
	}
	for (index=0;index<length_of_array;index++){
		found=false;
		for (check_index=0;check_index<AS.length;check_index++){
			if (length_of_array==1){
				alert(AS[check_index])
				if (AS[check_index]+"" == myelement.value+""){
					found=true;
				}	
			}else{
				if (AS[check_index]+"" == myelement[index].value+""){
					found=true;
				}	
			}
		}
		if (length_of_array==1){
			myelement.checked=found;
		}else{
			myelement[index].checked=found;
		}
	}
	if (document.associated_files_form.save_now.value=='1'){
		save_to_doc();
	}
}

function return_to_doc(){
	document.associated_files_form.save_now.value='1';
	document.associated_files_form.submit();
}
function save_to_doc(){
	destination = document.associated_files_form.destination.value;

	if (destination=="metadata_creator"){
		window.opener.document.user_form.metadata_creator_associations.value = document.associated_files_form.associated_list.value;
		window.opener.document.all.metadata_creator.innerHTML = document.associated_files_form.description.value;
	} else {
		window.opener.document.user_form.metadata_contributor_associations.value = document.associated_files_form.associated_list.value;
		window.opener.document.all.metadata_contributor.innerHTML = document.associated_files_form.description.value;
	}
	
	window.close();
}
function submit_filter(gotopage){
    document.associated_files_form.page.value=gotopage;
	document.associated_files_form.submit();
}
function manage(id){
	AS = new String(document.associated_files_form.associated_list.value);
	if (AS.indexOf(" "+id+",")>-1){
		// remove from list
		AS= AS.split(" "+id+",").join("");
	} else {
   		// add to the list
   		AS+=" "+id+",";
	}
	document.associated_files_form.associated_list.value=AS;

}