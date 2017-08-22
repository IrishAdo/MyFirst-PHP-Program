/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FILE ASSOCIATION FUNCTIONS.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function file_associate(){
	PATH = 'file_associate.php?command=FILES_LIST&amp;'+session_url+'&amp;associated_list='+document.forms[0].file_associations.value;
	my_preview_window = window.open(PATH,'PREVIEW_WINDOW','scrollbars=yes,resizable=yes,width=750,height=550');
	my_preview_window.focus();
}

function submit_filter(gotopage){
    document.associated_files_form.page.value=gotopage;
	document.associated_files_form.submit();
}

function manage(id){
	AS = new String(document.associated_files_form.associated_list.value);
	if (AS.indexOf(""+id+",")>-1){
		// remove from list
		AS= AS.split(""+id+",").join("");
	} else {
   		// add to the list
   		AS+=""+id+",";
	}
	document.associated_files_form.associated_list.value=AS;
}

function return_to_doc(php_session){
	window.location="?command=FILES_LIST_FILE_DETAIL&amp;file_associations="+document.associated_files_form.associated_list.value+"&amp;"+php_session;
}

function save_to_doc(list,description){
	window.opener.document.user_form.file_associations.value	 = list;
	window.opener.ranking_files_insertdata(description);
	window.close();
}

function open_associate_docs(){
	var file_associations_DOC = window.open("file_associations.php?associated_list="+document.composeform.file_associations.value+'&amp;'+session_url,"file_associations","location=no,scrollbars=yes,status=no,width=750,height=590");
	file_associations_DOC.focus();
}

function check_links(){
f = document.associated_files_form;
	if (f+'' !='undefined'){
		AS = new String(document.associated_files_form.associated_list.value).split(",");
		AS.length--;
		if (document.display_list+''!='undefined'){
			myelement = document.display_list.elements['file_list[]'];
			if (myelement+""=="undefined"){
				length_of_array=1;
			} else {
				length_of_array=myelement.length;
			}
			for (index=0;index<length_of_array;index++){
				found=false;
				for (check_index=0;check_index<AS.length;check_index++){
					if (length_of_array==1){
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
		}
	}
}

