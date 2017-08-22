/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Edit configuration scripts.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
		function load_configs_into_select(f){
			for(i=0;i < f.elements.length;i++){
				if ((pos = f.elements[i].name.indexOf("_identifier"))!=-1){
					find_name = f.elements[i].name.substring(0,pos);
					f.elements[find_name].options.length=1;
					for(z=0;z < editor_configurations.length;z++){
						add_at_index = f.elements[find_name].options.length;
						f.elements[find_name].options[add_at_index] = new Option("Lock to this editor configuration '"+editor_configurations[z][1]+"'",editor_configurations[z][0]);
						if (editor_configurations[z][0]==f.elements[find_name+'_identifier'].value){
							f.elements[find_name].options[add_at_index].selected='true';
						}
					}
				}
			}
		}
		
		function display_row(status, name){
			output += '<input type="hidden" name="old_status[]" value="'+status+'/>';
			output += '<input type="hidden" name="textarea[]" value="'+name+'"/>';
			output += '<select name="status[]">';
			output += '<option value="0">Use Groups Access to define Editor Configuration</option>';
			for(z=0;z < editor_configurations.length;z++){
				output += '<option value="Lock to this editor configuration \''+editor_configurations[z][1]+'\'">,'+editor_configurations[z][0]+'</option>'
			}
			output += '</select>';

		}
