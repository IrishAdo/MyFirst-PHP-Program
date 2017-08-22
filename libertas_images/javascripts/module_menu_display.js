/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- indexes for the menu array are :-
	0	identifier,
	1	label,
	2	url,
	3	depth,	
	4	children,	
	5	parent,	
	6	channels,	
	7	siblings,	
	8	group options Array(),	
	9	order,
	10	stylesheet,
	11	theme
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
indent			= Array();
toggle		 	= Array();
toggle[-1] 		= Array(-1,true);
counter			= Array();
counter[0]=1;
var count = Array();

for (index=0;index < menu_list.length;index++){
	toggle[index]	= Array(menu_list[index][0],false);
}



function link_toggle(linkid){
	for (index=0,len=menu_list.length;index < len;index++){
		if (linkid==menu_list[index][0]){
			toggle[index][1] = !toggle[index][1];
		}
	}
	draw_menu();
}

function draw_menu(){
	//menu_list = ;
	level = 0;
	prev_level = 0;
	depth =0;
	for (index=0,len=menu_list.length;index < len;index++){
		if (depth < menu_list[index][3]){
			depth=menu_list[index][3];
		}
	}
	output  ='<table width="100%" border="0" cellpadding="0" cellspacing="0">\n';
	output += display_children(1,-1);
	output += '</table>';
	document.all.menu_tree.innerHTML=output;
}

function display_children(d,loc){
	var index=0;
	var output="";
	var NEW_DEPTH=d;
	var total = counter[d];
	count[d]=0
	if (loc==-1){
	 id = "root";
	}else {
		id =loc
	}
	for (var index=0;index < menu_list.length;index++){
		if (menu_list[index][5]==loc){
			indent[d] ="";
			
			for (var depth_index=2 ; depth_index < NEW_DEPTH+1 ; depth_index++){
				if (depth_index<d){
					if (count[depth_index]==menu_list[index][7]){
						//alert(depth_index+"  "+count[depth_index]+" "+menu_list[index][7])
						indent[d] +='\n<td width="10"><img src="/libertas_images/themes/1x1.gif" border="0"/></td>';
					} else {
						if (menu_list[index][7] < count[depth_index]){
							if (depth_index<(d)){
								indent[d] +='\n<td width="10"><img src="/libertas_images/themes/1x1.gif" border="0"/></td>'; //folder_down
							}else{
								indent[d] +='\n<td width="10"><img src="/libertas_images/themes/1x1.gif" border="0"/></td>';
							}
						}else{
							//alert(menu_list[index][7]+ " " +depth_index+ " " +d+ " " +count[depth_index]+ " " +count[d])
							indent[d]     +='\n<td width="10"><img src="/libertas_images/themes/1x1.gif" border="0"/></td>';
						}
					}
				}else{
					if (count[depth_index]==menu_list[index][7]){
						indent[d] +='\n<td width="10"><img src="/libertas_images/themes/1x1.gif" border="0" width="19" height="18"/></td>';
					} else if (count[depth_index] < menu_list[index][7]){
						indent[d] +='\n<td width="10"><img src="/libertas_images/themes/1x1.gif" border="0" width="19" height="18"/></td>';
					} else if (count[depth_index] > menu_list[index][7]){
						indent[d] +='\n<td width="10"><img src="/libertas_images/themes/1x1.gif" border="0" width="19" height="18"/></td>';
					} else {
						indent[d] +='\n<td width="10"><img src="/libertas_images/themes/1x1.gif" border="0" width="19" height="18"/></td>';
					}
				}
			}
			if (menu_list[index][4]>0){
				if (!toggle[index][1]){
					output 		+= '<tr>'+indent[d];
					output 		+= '\n<td  id="menu'+id+'" width="10" ><a class="layout_menu_folder" href="javascript:link_toggle('+menu_list[index][0]+');"><img src="/libertas_images/themes/site_administration/plus.gif" border="0"/></a></td><td width="100%" colspan="'+((depth+1)-d)+'"><a href="admin/index.php'+menu_list[index][2]+'">'+menu_list[index][1]+'</a></td>\n</tr>\n';
				}else{
					output 		+= '<tr>'+indent[d];
					output		+= '\n<td  id="menu'+id+'" width="10" ><a class="layout_menu_folder" href="javascript:link_toggle('+menu_list[index][0]+');"><img src="/libertas_images/themes/site_administration/minus.gif" border="0"/></a></td><td width="100%" colspan="'+((depth+1)-d)+'"><a href="admin/index.php'+menu_list[index][2]+'">'+menu_list[index][1]+'</a></td>\n</tr>\n';
					output 		+= display_children(NEW_DEPTH+1,toggle[index][0]);
					}
			} else {
				output 		+= '<tr>'+indent[d];
				output += '\n<td ><img src="/libertas_images/themes/site_administration/folder_right.gif" border="0" /></td><td width="100%" colspan="'+((depth+1)-d)+'"><a href="admin/index.php'+menu_list[index][2]+'">'+menu_list[index][1]+'</a></td>\n</tr>\n';
			}
			count[d]++
		}
	}
	return output;
}

function build_location_combo(parent_id,first) {
	d 	= parent.document.layout_form;
	
	if (1==first){
		d.menu_parent.options.length=1;
	}
	for (var index=0;index < menu_list.length;index++){
		if (menu_list[index][5]==parent_id){
			textinput = "";
			for (var spacer_index=0;spacer_index < menu_list[index][3];spacer_index++){
				if (spacer_index < (menu_list[index][3]-1)){
					textinput += "   ";
				} else {
					if (menu_list[index][4]>0){
						textinput += " - ";
					}else{
						textinput += "   ";
					}
				}
			}
			textinput += menu_list[index][1];
			d.menu_parent.options[d.menu_parent.options.length] = new Option(textinput,menu_list[index][0]);
			build_location_combo(menu_list[index][0],0);
		}
	}
}

//alert("hello");
draw_menu();
//build_location_combo(-1,1);
//d	= parent.document.layout_form;
//parent.document.LAYOUT_REMOVE_MENU.src='images\\1x1.gif';
//				d.LAYOUT_REMOVE_MENU.style.visibility='hidden';
