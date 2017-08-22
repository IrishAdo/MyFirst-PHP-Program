var ignore_list = Array();
var temp_list	= Array();

function preview_from_list(id,module){
	my_preview_window = window.open('preview.php?command='+module.toUpperCase()+'_PREVIEW&amp;'+session_url+'&amp;retrieve=database&amp;identifier='+id,'PREVIEW_WINDOW','toolbar=no,location=no,directories=no,menu_bar=no,scrollbars=yes,resizable=yes');
	my_preview_window.focus();
}

function preview_from_form(id,module,f,cmd){
	f.target		= 'external_preview';
	f.action		= 'admin/preview.php';
	prev_cmd 		= f.command.value; 
	f.command.value	= cmd;//'PAGE_PREVIEW_FORM';
	if (typeof onSubmitCompose =="function") {
		ok 			= onSubmitCompose(2);
	} else {
		f.submit();
	}
	f.target		= '_self';
	f.action		= '';
	f.command.value = prev_cmd;
}

function page_associate(){
	PATH = 'file_associate.php?command=PAGE_LIST&amp;'+session_url+'&amp;associated_list='+document.shop_stock_form.page_associations.value+',';
	my_preview_window = window.open(PATH,'PREVIEW_WINDOW','scrollbars=yes,resizable=yes,width=750,height=550');
	my_preview_window.focus();
}

function page_check_links(){
	f = document.filter_form;
	if (f+'' !='undefined'){
		if (f.associated_list.value==","){
			f.associated_list.value="";
		}
		AS = new String(f.associated_list.value).split(",");
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
							break;
						}	
					}else{
						if (AS[check_index]+"" == myelement[index].value+""){
							found=true;	
							break;
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


function manage(id){
	f = document.filter_form;
	AS = new String(f.associated_list.value);
	if (AS.indexOf(""+id+",")>-1){
		// remove from list
		AS= AS.split(""+id+",").join("");
	} else {
   		// add to the list
   		AS+=""+id+",";
	}
	f.associated_list.value=AS;
}

function save_to_doc(list,description){
	window.opener.document.shop_stock_form.page_associations.value	 = list;
	window.opener.document.all.shop_stock_pages.innerHTML = description;
	window.close();
}
function return_to_doc(php_session){
	f = document.filter_form;
	window.location="?command=PAGE_LIST_DETAIL&amp;page_associations="+f.associated_list.value+"&amp;"+php_session;
}

	
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FILE ASSOCIATION FUNCTIONS.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	function file_associate(){
		PATH = 'file_associate.php?command=FILES_LIST&amp;'+session_url+'&amp;associated_list='+document.forms[0].file_associations.value;
		my_preview_window = window.open(PATH,'PREVIEW_WINDOW','scrollbars=yes,resizable=yes,width=750,height=550');
		my_preview_window.focus();
	}
	function submit_filter(gotopage){
	    f.page.value=gotopage;
		f.submit();
	}
*/
function image_list(text,value){
	alert(text,value);
}

function ignore_keyword(t){
	if (t.name+''!='undefined'){
		var val = t.value.split(', ');
		key=val[1];
	} else {
		key=t;
	}
	if (!t.checked){
		ignore_key = confirm("You have choosen to reject the word '"+key+"',\nIf this is a word that should never appear in the suggested list of keywords then \nselect 'Ok' to never have this word appear again.\n Other wise select 'Cancel' to just ignore it this time.");
		if (ignore_key){
			ignore_list[ignore_list.length] = key;
		}
		temp_list[temp_list.length] = key;
	} else {
		pos =-1;
		for(i=0;i<ignore_list.length;i++){
			if (ignore_list[i] == key){
				pos =i;
			}
			if (pos!=-1){
				if (i !=ignore_list.length){
					ignore_list[i] = ignore_list[i+1];
				}
			}
		}
		if (pos!=-1){
			ignore_list.length--;
		}
		pos =-1;
		for(i=0;i<temp_list.length;i++){
			if (temp_list[i] == key){
				pos =i;
			}
			if (pos!=-1){
				if (i !=temp_list.length){
					temp_list[i] = temp_list[i+1];
				}
			}
		}
		if (pos!=-1){
			temp_list.length--;
		}
	}
	document.user_form.keyword_ignore_list.value = ignore_list.join(",")
	document.user_form.temp_ignore_list.value = temp_list.join(",")
//	alert(document.keyword_frm.keyword_ignore_list.value);
}