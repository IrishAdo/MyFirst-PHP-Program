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

define_toggle(new_menu_link_list);
function define_toggle(arr){
	for (var index=0;index < arr.length;index++){
		toggle[arr[index][0]]=false;
		if (arr[index][4].length>0){
			define_toggle(arr[index][4]);
		}
	}
}

function link_toggle(linkid){
	toggle[linkid] = !toggle[linkid];
	draw_menu();
}

function draw_menu(){
	level = 0;
	prev_level = 0;

	maxDepth = find_depth(new_menu_link_list,0);
	
	output  ='<table width="270" border="1" cellpadding="0" cellspacing="0">\n';
	output += display_children(new_menu_link_list, -1, "", 0, maxDepth);
	output += '</table>';
//	alert(output);
	document.all.menu_tree.innerHTML=output;
}

function find_depth(menu_links, depth){
	var tmp_depth	= 0;
	var tmp = depth;
	for (var index=0; index < menu_links.length; index++){
		if (menu_links[index][4].length > 0){
			var ndepth = depth + 1;
			tmp_depth = find_depth(menu_links[index][4], ndepth);
			if (tmp_depth >= tmp){
				tmp = tmp_depth;
			}
		}
	}
	return tmp;
}
function display_children(menu_links, loc, leftSideFlags, d, depth){
	var output="";
	if (loc==-1){ 
		id = "root";
	} else {
		id =loc
	}
	//alert("d::"+d+" Depth::"+depth);
	for (var index=0;index < menu_links.length;index++){
//		alert(menu_links[index][5]+"=="+loc)
		if (menu_links[index][5]==loc){
			indent="";
			for (i=0;i<leftSideFlags.length;i++){
				if (leftSideFlags.charAt(i)=="0"){
					indent +='<td><img src="/libertas_images/themes/1x1.gif" width="19" height="19" border="0"/></td>';
				}
				if (leftSideFlags.charAt(i)=="1"){
					indent +='<td><img src="/libertas_images/themes/site_administration/folderright.gif" width="16" height="22" border="0"/></td>';
				}
				if (leftSideFlags.charAt(i)=="2"){
					indent +='<td><img src="/libertas_images/themes/site_administration/folderright_plus.gif" width="16" height="22" border="0"/></td>';
				}
				if (leftSideFlags.charAt(i)=="3"){
					indent +='<td><img src="/libertas_images/themes/site_administration/lastnode.gif" width="16" height="22" border="0"/></td>';
				}
			}
			
			if (menu_links[index][4].length>0){
				if (!toggle[menu_links[index][0]]){
					output 		+= '<tr id="menu'+id+'">'+indent+'';
					output 		+= '\n<td width="10" ><a class="layout_menu_folder" href="javascript:link_toggle('+menu_links[index][0]+');"><img src="/libertas_images/themes/site_administration/plus.gif" border="0"/></a></td><td width="100%" colspan="'+((depth+1)-d)+'"><a href="admin/index.php'+menu_links[index][2]+'">'+menu_links[index][1]+'</a></td>\n</tr>\n';
				}else{
					output 		+= '<tr id="menu'+id+'">'+indent+'';
					output		+= '\n<td width="10" ><a class="layout_menu_folder" href="javascript:link_toggle('+menu_links[index][0]+');"><img src="/libertas_images/themes/site_administration/minus.gif" border="0"/></a></td><td width="100%" colspan="'+((depth+1)-d)+'"><a href="admin/index.php'+menu_links[index][2]+'">'+menu_links[index][1]+'</a></td>\n</tr>\n';
					if (menu_links.length-1>index){
						tmpleftSideFlags = leftSideFlags+"1";
					} else {
						tmpleftSideFlags = leftSideFlags+"2";
					}
					output 		+= display_children(menu_links[index][4], menu_links[index][0], tmpleftSideFlags, d+1, depth);
					}
			} else {
				output 		+= '<tr id="menu'+id+'">'+indent+'';
				output += '\n<td ><img src="/libertas_images/themes/site_administration/folder_right.gif" border="0" /></td><td width="100%"  colspan="'+((depth+1)-d)+'"><a href="admin/index.php'+menu_links[index][2]+'">'+menu_links[index][1]+'</a></td>\n</tr>\n';
			}
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
