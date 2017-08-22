/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Test
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function check_links(){
}

function save_to_doc(l_o_g){
	myelement = document.group_selection.elements['group_list[]'];
	length_of_array=myelement.length;

	str="";
	description="";
	for (index=0;index<length_of_array;index++){
		if (myelement[index].checked){
			str += " "+myelement[index].value+",";
			description += "<li>"+l_o_g[index][1]+"</li>";
		}
	}
	window.opener.document.user_form.trans_groups.value = str;
	window.opener.document.all.trans_group_information.innerHTML = description
	window.close();
}
