/*		list = new String(document.group_administration_form.prev_group_menus.value).split(", ");
		myelement = document.group_administration_form.elements['menu_options[]'];
		
		for (index=0;index < myelement.length;index++){
			found=false;
			for (list_index=0;list_index < list.length;list_index++){
				if(list[list_index]==myelement[index].value){
					found=true;
				}
			}
			if (found){
				myelement[index].checked = true;
			} else {
				myelement[index].checked = false;
			}
		}
	*/	
	try{
		l = document.group_administration_form.totalnumberofchecks_group_access.value
		list = new String(document.group_administration_form.prev_group_admin_access.value).split("|");
		for (i=1; i<=l ;i++){
			myelement = document.group_administration_form.elements['group_access_'+i+'[]'];
			if (myelement+''!='undefined'){
				if (myelement.length != undefined){
					for (index=0;index < myelement.length;index++){
						found=false;
						for (list_index=0;list_index < list.length;list_index++){
							if (list[list_index]!=""){
								split_list = list[list_index].split("_");
								checker="";
								if(myelement[index].value.indexOf(list[list_index])>-1){
									found=true;
								}
							}
						}
						if (found){
							myelement[index].checked = true;
						} else {
							myelement[index].checked = false;
						}
					}
				} else {
					found=false
					for (list_index=0;list_index < list.length;list_index++){
						if (list[list_index]!=""){
							split_list = list[list_index].split("_");
							checker="";
							if(myelement.value.indexOf(list[list_index])>-1){
								found=true;
							}
						}
					}
					if (found){
						myelement.checked = true;
					} else {
						myelement.checked = false;
					}
					
				}
				document.group_administration_form.elements['group_type'].onchange=toggle_group_access;
			}
		}
	}catch (e){
	// non enterprise account
	}
		function toggle_group_access(){
			try{
				gt = document.group_administration_form.group_type;
				if (gt.options[gt.selectedIndex].value=="2"){
					set_value=false;
					toggle_hidden('group_access',1);
				} else {
					set_value=true;
					toggle_hidden('group_access',0);
				}
				l = document.group_administration_form.totalnumberofchecks_group_access.value
				for (i=1; i<=l ;i++){
					myelement = document.group_administration_form.elements['group_access_'+i+'[]'];
					testmyelement = document.group_administration_form.elements['group_access_'+i+''];
					if (myelement+''!='undefined'){
						if (myelement.length != undefined){
							for (index=0;index < myelement.length;index++){
								myelement[index].disabled=set_value;
							}
						} else {
							myelement.disabled=set_value;
						}
					}
				}
			} catch(e){
			
			}
		}
		
		function check_group(t,tag){
			myelement = document.group_administration_form.elements[t.name];
			tag_index = -1;
			for (index=0;index < myelement.length;index++){
				if (myelement[index].value.substring(0,tag.length)==tag){
					if (myelement[index].value==tag+"ALL"){
						tag_index = index;
					}
					if ((tag+"ALL"==t.value) && (myelement[index].value!=tag+"ALL")){
						myelement[index].checked=false;
					}
					if ((tag+"ALL"!=t.value)&&(tag_index!=-1)){
						myelement[tag_index].checked=false;
					}
				}
			}
		}
		
		toggle_group_access();
		
