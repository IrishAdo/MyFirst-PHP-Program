			/*
				+- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
				| check to see if a Other input box is to be visible
				+- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
			*/
			function check(entry){
				str = 'test = document.all.vehicle_'+entry+'.options[document.all.vehicle_'+entry+'.options.selectedIndex].value';
				eval (str);
					if (test==-2){
						eval ("document.vehicle_form.vehicle_"+entry+"_extra.style.visibility='visible'");
					} else {
						eval ("document.vehicle_form.vehicle_"+entry+"_extra.style.visibility='hidden'");
					}
			}
	 		function check_browse(item,browse_button){
	 		}
			/*
				+- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
				| Fill in the Manufacturer combo box
				+- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
			*/
			function Fill_Manufacturer_Combo(Manufacturer,manufacture_and_model,form_entry){
				if (form_entry+"" == "undefined"){
					form_entry = document.vehicle_form.vehicle_manufacturer;
				}
				form_entry.length=0;
				form_entry.options[form_entry.options.length]= new Option('Select a Manufacturer','-1');
				array_length = manufacture_and_model.length;
				for (index=0;index<array_length;index++){
					form_entry.options[index+1]= new Option(manufacture_and_model[index][0],manufacture_and_model[index][1]);
					if (Manufacturer==manufacture_and_model[index][1]){
						form_entry.options[index+1].selected=true;
					}
				}
				form_entry.options[form_entry.options.length]= new Option('Other','-2');
			}
			
			/*
				+- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
				| Fill in the model combo box
				+- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
			*/
			function Fill_Model_Combo(vehicle_manufacturer, vehicle_model, manufacture_and_model, form_entry){
				if (form_entry+"" == "undefined"){
					form_entry = document.vehicle_form.vehicle_model;
				}
				array_length = manufacture_and_model.length;
				form_entry.options.length= 0;
				form_entry.options[form_entry.options.length]= new Option('Select a Model','-1');
				for (index=0;index<array_length;index++){
					if (vehicle_manufacturer==manufacture_and_model[index][1]){
						model_length=manufacture_and_model[index][2].length;
						for(model=0;model<model_length;model++){
							form_entry.options[model+1]= new Option(manufacture_and_model[index][2][model][1],manufacture_and_model[index][2][model][0]);
							if(vehicle_model==manufacture_and_model[index][2][model][0]){
								form_entry.options[model+1].selected=true;
							}
						}
					}
				}
				form_entry.options[form_entry.options.length]= new Option('Other','-2');
				/*
					+- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
					| Fill in the manfacturer combo box is other then set the model to be other
					| also enable both extra fields.
					+- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
				*/
				if(vehicle_manufacturer==-2){
					document.vehicle_form.vehicle_manufacturer_extra.style.visibility='visible';
				} else {
					document.vehicle_form.vehicle_manufacturer_extra.style.visibility='hidden';
				}
			}
			function remove(){
				document.vehicle_form.command.value='VEHICLE_REMOVE';
				document.vehicle_form.submit();
			}
			/*
				+--------------------------------------------------------------------
				| check to see if a Other input box is to be visible
				+--------------------------------------------------------------------
			*/
			function lookup_check(t){
				
				test_value = t.lookup_table.options[t.lookup_table.options.selectedIndex].value;
				test_text = t.lookup_table.options[t.lookup_table.options.selectedIndex].text;
				if (test_value<0){
					t.new_value.value = '';
				} else {
					t.new_value.value = test_text;
				}
			}
			function lookup_remove(t){
				t.command.value='VEHICLE_LOOKUP_REMOVE';
				t.submit();
			}
			try{
				Fill_Manufacturer_Combo(vehicle_manufacturer,manufacture_and_model);
				Fill_Model_Combo(vehicle_manufacturer,vehicle_model,manufacture_and_model);
			} catch(e) {
			}