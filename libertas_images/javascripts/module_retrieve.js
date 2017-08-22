/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- RETRIEVE ASSOCIATION FUNCTIONS.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function retrieve_data(cmd,associated,note,return_command){
	f = get_form();
	try{
		PATH = "http://"+domain+base_href+'admin/file_associate.php?command='+cmd+'&amp;'+session_url+'&amp;return_hidden='+associated+'&amp;return_note='+note+'&amp;associated_list='+f[associated].value+'&amp;return_command='+return_command;
		retrieval_window = window.open(PATH,'PREVIEW_WINDOW','scrollbars=yes,resizable=yes,width=750,height=550');
		retrieval_window.focus();
	} catch(e) {
		alert("Error in opening new window");
	}
}


function submit_filter(gotopage){
    document.associated_form.page.value=gotopage;
	document.associated_form.submit();
}

function manage(id, lockToOne){
	if(lockToOne+""=="undefined"){
		lockToOne=0;
	}
	AS ="";
	if (lockToOne==0){
		AS = new String(document.associated_form.associated_list.value);
		if (AS.indexOf(""+id+",")>-1){
			// remove from list
			AS  = AS.split(""+id+",").join("");
		} else {
   			// add to the list
   			AS += ""+id+",";
		}
	} else {
   			AS += ""+id+",";
	}
	document.associated_form.associated_list.value=AS;
}
function check_links(){
	return check_association()
}
function check_association(){
	var f = document.associated_form;
	if (f+'' !='undefined'){
		AS = new String(document.associated_form.associated_list.value).split(" ").join("").split(",");
		AS.length--;
		if (document.display_list+''!='undefined'){
			myelement = document.display_list.elements['file_list[]'];
		} else {
			myelement = document.associated_form.elements['file_list[]'];
		}
		if (myelement+"" == "undefined"){
			length_of_array = 0;
		} else {
			if (myelement.length+""=="undefined"){
				length_of_array = 0;
			}else {
				length_of_array = myelement.length;
			}
		}
		
		for (index=0;index<length_of_array;index++){
			found=false;
			for (check_index=0;check_index<AS.length;check_index++){
				if (length_of_array==0){
					if (AS[check_index]+"" == myelement.value+""){
						found=true;
					}	
				}else{
					if (AS[check_index]+"" == myelement[index].value+""){
							found=true;	
					}	
				}
			}
			if (length_of_array==0){
				myelement.checked=found;
			}else{
				myelement[index].checked=found;
			}
		}
	}
}

function return_to_doc(php_session){
	f = get_form();
	if (f.return_command+""!="undefined"){
		f.command.value=f.return_command.value;
	}
	f.submit();
}
function save_to_doc(list, description, note, hidden){
	var f = window.opener.get_form();
	f[hidden].value	 			= list;
//	window.opener.document.user_form[hidden].value	 	= list;
	hidden = " "+hidden;
	if (hidden.indexOf('file_associations')==1){
		window.opener.ranking_files_insertdata(description);
	} else {
		window.opener.document.all[note].innerHTML	= description;
	}
	window.close();
}

/*
function open_associate_docs(){
	var file_associations_DOC = window.open("file_associations.php?associated_list="+document.composeform.file_associations.value+'&amp;'+session_url,"file_associations","location=no,scrollbars=yes,status=no,width=750,height=590");
	file_associations_DOC.focus();
}

*/

function file_add(){
	f = get_form();
	f.command.value="FILES_ADD";
	f.submit();
}

function a_group(id){
	/*AS = new String(document.associated_form.associated_list.value);
	if (AS.indexOf(""+id+",")>-1){
		// remove from list
		AS= AS.split(""+id+",").join("");
	} else {
   		// add to the list
   		AS+=""+id+",";
	}
	document.associated_form.associated_list.value=AS;
	*/
}