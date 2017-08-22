/*
/libertas_images/javascripts/special_page_add_new_menu.js
*/

function menucheck(){
	var f = get_form();
	var sz= "";
	tml = f.trans_menu_locations;
	if (tml.options[tml.selectedIndex].value=="-1,-1"){
		d = document.getElementById("special_page_add_new_menu");
		szfrm ='<div style="padding-left:20px;vertical-align:top;display:block"><div style="display:inline;width:200px;">Parent for new location<br/><select style="width:250px" name="new_menu_parent">';
		for (var i =1;i<tml.options.length;i++){
			if (tml.options[i].value!="-1,-1"){
				val = tml.options[i].value;
				txt = tml.options[i].text;
//					if (tml.options[i].style.color=='#cccccc') {
//						style='style="color:#cccccc"';
//					} else {
				style='';
//					}
				szfrm += '<option value="'+val+'" '+style+'>&nbsp;&nbsp;&nbsp;'+txt+'</option>';
			} else {
				szfrm += '<option value="-1,-1">Root of Site</option>';
			}
		}
		szfrm+='</select></div><div style="display:inline;width:300px;">Label for new location <br><input type="text" name="new_menu_label" size=50 maxlength=255></div>';
		szfrm+='</div>';
		d.innerHTML 		= szfrm;
		d.style.display		= '';
	} else {
		d = document.getElementById("special_page_add_new_menu");
		d.innerHTML 		= "";
		d.style.display		= 'none';
	}
	
}