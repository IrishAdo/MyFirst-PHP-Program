/*
	Some Definitions a placeHolder is a Container while a widget is a Web Object
	wording changed through the development of this functionality
	Adrian Sweeney
	
	NOTE ::
	Make sure you are always using local variables in functions by defining all varialbles 
	that are inside a function to be local ie (var x=0;)  espically on loops 
*/
var webObjectGroupLayout = Array;

function getGroupLayout(){
	var wogl = Array('1','1','1','1');
	for(i=0;i<document.webobjects_form.wol_layout.length;i++){
		if(document.webobjects_form.wol_layout[i].checked){
			wogl = document.webobjects_form.wol_layout[i].value.split('')
		}
	}
	return wogl;
}
function setGroupLayout(wogl){
	if (wogl+''==''){
		wogl="1111";
	}
	for(i=0;i<document.webobjects_form.wol_layout.length;i++){
		if (document.webobjects_form.wol_layout[i].value == wogl){
			document.webobjects_form.wol_layout[i].checked = true;
		} else {
			document.webobjects_form.wol_layout[i].checked = false;
		}
	}
	return wogl;
}

function display_columns(){
	webObjectGroupLayout = getGroupLayout()
	wogl = webObjectGroupLayout;
	var sz ='<table border="0" cellspacing="1" cellpadding="0" width="100%" bgcolor="#ebebeb">';
	sz	+='<tr>';
	buttons = '<a href="javascript:add_container(\'header\',\''+1+'\')"><img src="/libertas_images/themes/site_administration/container_add.gif" width="20" height="20" alt="Add new Container" border="0"/></a>';
	sz	+='<td valign="top" class="bt" colspan="4"><table width="100%"><tr><td>Header</td><td align="right">'+buttons+'</td></tr></table></td>';
	sz	+='<tr>';
	sz	+='<td valign="top" class="tablecell" colspan="4">';
	sz  +=display_placeholders('header',placeholder_id_list);
	sz  +='</td>';
	sz	+='</tr>';
	sz	+='<tr>';
	var c=1;	
	for(var i=0; i<wogl.length;i++){
		buttons = '';
		p= c;
		c+=(wogl[i]*1);
		if (i>0){
			buttons += '<a href="javascript:move_container('+i+','+p+',\'left\')"><img src="/libertas_images/themes/site_administration/container_left.gif" width="20" height="20" alt="Move Left" border="0"/></a>';
		}
		buttons += '<a href="javascript:add_container('+p+')"><img src="/libertas_images/themes/site_administration/container_add.gif" width="20" height="20" alt="Add new Container" border="0"/></a>';
		if (c<=4){
			buttons += '<a href="javascript:move_container('+i+','+p+',\'right\')"><img src="/libertas_images/themes/site_administration/container_right.gif" width="20" height="20" alt="Move Right" border="0"/></a>';
		}
		sz	+='<td valign="top" class="bt" colspan="'+wogl[i]+'" width="'+(wogl[i]*25)+'%"><table width="100%"><tr><td>Column '+(i+1)+'</td><td align="right">'+buttons+'</td></tr></table></td>';
	}
	sz	+='</tr>';
	sz	+='<tr>';
	c=1;
	for(var i=0; i<wogl.length;i++){
		sz +='<td valign="top" class="tablecell" colspan="'+wogl[i]+'" width="'+(wogl[i]*25)+'%">'
		sz +=display_placeholders(c+i,placeholder_id_list);
		if (wogl[i]>1){
			c++;
			sz +=display_placeholders(c+i,placeholder_id_list);
		}
		if (wogl[i]>2){
			c++;
			sz +=display_placeholders(c+i,placeholder_id_list);
		}
		if (wogl[i]>3){
			c++;
			sz +=display_placeholders(c+i,placeholder_id_list);
		}
		sz +='</td>';
	}
	sz	+='</tr>';
	sz	+='<tr>';
	buttons = '<a href="javascript:add_container(\'footer\',\''+1+'\')"><img src="/libertas_images/themes/site_administration/container_add.gif" width="20" height="20" alt="Add new Container" border="0"/></a>';
	sz	+='<td valign="top" class="bt" colspan="4"><table width="100%"><tr><td>Footer</td><td align="right">'+buttons+'</td></tr></table></td>';

	sz	+='</tr>';
	sz	+='<tr>';
	sz	+='<td valign="top" class="tablecell" colspan="4">';
	sz  +=display_placeholders('footer',placeholder_id_list);
	sz  +='</td>';
	sz	+='</tr>';
	sz	+='</table>';
	document.getElementById("placeholdersdisplay").innerHTML=sz;
}

function display_placeholders(column,placelist,rank){
	var sz 		= '';
	var counter	= 0;
	for (var i = 0; i < placelist.length;i++){
		if (placelist[i][2] == 'column'+column){
//		alert('found'+placelist[i][0]);
			maxRank = countContainers(column, placelist[i][4]);
			sz += '<table width="100%"><tr bgcolor="#c0c0c0"><td><strong>'
			if (placelist[i][4]!=''){
				sz += placelist[i][4]+') ';
			}
			sz += placelist[i][0]+'</strong></td>';
			sz +='<td width="10"><a href="javascript:obj_edit(\'container\', \''+column+'\', \''+placelist[i][4]+'\',\'\');"><img src="/libertas_images/themes/site_administration/obj_properties.gif" alt="Up" width="10" height="10" border="0"></a></td>';
			if (counter>0){
				sz +='<td width="10px"><a href="javascript:rank_up(\'container\',\''+column+'\',\''+placelist[i][4]+'\');"><img src="/libertas_images/themes/site_administration/rank_up.gif" alt="Up" width="10" height="10" border="0"></a></td>';
			} else {
				sz +='<td width="10px"></td>';
			}
			if (counter < countContainers(column, placelist[i][4])-1){
				sz +='<td width="10px"><a href="javascript:rank_down(\'container\',\''+column+'\',\''+placelist[i][4]+'\');"><img src="/libertas_images/themes/site_administration/rank_down.gif" alt="Down" width="10" height="10" border="0"></a></td>';
			} else {
				sz +='<td width="10px"></td>';
			}
			sz +='</tr>'+displayWidgets(placelist[i][3], placelist[i][2], placelist[i][4])+'<tr><td><a href="javascript:add_webObject(\''+placelist[i][2]+'\', \''+placelist[i][4]+'\', \''+placelist[i][1]+'\')">Add new WebObject</a></td></tr></table>';
			counter++;
		}
	}
	return sz;
}

function displayWidgets(hlist,cname,pos){
	var sz ="";
	for(var i =0;i<hlist.length;i++){
		sz +='<tr><td width="100%">'+hlist[i][1]+'</td>';
		sz +='<td width="10"><a href="javascript:obj_edit(\'widget\', \''+cname+'\', \''+hlist[i][2]+'\', \''+pos+'\');"><img src="/libertas_images/themes/site_administration/obj_properties.gif" alt="Up" width="10" height="10" border="0"></a></td>';
		if (i>0){
			sz +='<td width="10"><a href="javascript:rank_up(\'widget\', \''+cname+'\', \''+hlist[i][2]+'\', \''+pos+'\');"><img src="/libertas_images/themes/site_administration/rank_up.gif" alt="Up" width="10" height="10" border="0"></a></td>';
		} else {
			sz +='<td width="10"><img src="/libertas_images/themes/1x1.gif" border="0" width="10" height="10" /></td>';
		}
		if (i<hlist.length-1){
			sz +='<td width="10"><a href="javascript:rank_down(\'widget\', \''+cname+'\', \''+hlist[i][2]+'\', \''+pos+'\');"><img src="/libertas_images/themes/site_administration/rank_down.gif" alt="Down" width="10" height="10" border="0"></a></td>';
		} else {
			sz +='<td width="10"><img src="/libertas_images/themes/1x1.gif" border="0" width="10" height="10" /></td>';
		}
		sz +='</tr>';
	}
	return sz;
}

function move_container(index, column, direction){
	wogl = getGroupLayout();
	if (direction=='left'){
		if (index >0 ){
			num1 	= (wogl[index-1] * 1)
			num2 	= (wogl[index] * 1)
			swap1 	= column;
			swap1to = column-num1;
			swap2 	= swap1to;
			swap2to = swap2+num2;
			swap(swap1,'tmp1');
			swap(swap2,'tmp2');
			swap('tmp1',swap1to);
			swap('tmp2',swap2to);
			tmp = wogl[index-1];
			wogl[index-1] = wogl[index];
			wogl[index] = tmp;
		}
	}
	if (direction=='right'){
		if (index < (wogl.length-1)){
			num1 	= (wogl[index+1] * 1)
			num2 	= (wogl[index] * 1)
			swap1 	= column;
			swap1to = column+num1;
			swap2 	= column+num2;
			swap2to = column;
			swap(swap1,'tmp1');
			swap(swap2,'tmp2');
			swap('tmp1',swap1to);
			swap('tmp2',swap2to);
			tmp = wogl[index+1];
			wogl[index+1] = wogl[index];
			wogl[index] = tmp;
		}
	}
	setGroupLayout(wogl.join(''));
	display_columns();
}

function swap(s1,s2){
	ok = false;
	for (var i=0; i<placeholder_id_list.length;i++){
		if(placeholder_id_list[i][2]=='column'+s1){
			placeholder_id_list[i][2]='column'+s2;
			ok = true;
		}
	}
	return ok;
}

function countContainers(column, rank){
	var countc=0;
	for(var i=0; i<placeholder_id_list.length; i++){
		if (placeholder_id_list[i][2] == 'column'+column){
			countc++;
		}
	}
	return countc;
}

function rank_up(type, cname, rank, pos){
	rank = rank*1;
	if (type=='container'){
		for(var i=0; i<placeholder_id_list.length; i++){
			if (placeholder_id_list[i][2] == 'column'+cname){
				if (placeholder_id_list[i][4] == rank){
					placeholder_id_list[i][4] = rank-1;
					placeholder_id_list[i][7] = 1;
				} else if (placeholder_id_list[i][4] == rank-1){
					placeholder_id_list[i][4] = rank;
					placeholder_id_list[i][7] = 1;
				}
			}
		}
		placeholder_id_list.sort(rank_container_sort);
	}
	if (type=='widget'){
		for(var i=0; i<placeholder_id_list.length; i++){
			if (placeholder_id_list[i][2] == cname){
				for(var z=0; z<placeholder_id_list[i][3].length; z++){
					if (placeholder_id_list[i][3][z][2] == rank-1){
						placeholder_id_list[i][3][z][2] = rank;
						placeholder_id_list[i][7] = 1;
						placeholder_id_list[i][3][z][8] = 1;
					} else if (placeholder_id_list[i][3][z][2] == rank){
						placeholder_id_list[i][3][z][2] = rank-1;
						placeholder_id_list[i][7] = 1;
						placeholder_id_list[i][3][z][8] = 1;
					}
				}
				placeholder_id_list[i][3].sort(rank_widget_sort);
			}
		}
	}
	display_columns();
}
function rank_down(type, cname, rank, pos){
	rank = rank*1;
	if (type=='container'){
		for(var i=0; i<placeholder_id_list.length; i++){
			if (placeholder_id_list[i][2] == 'column'+cname){
				if (placeholder_id_list[i][4] == rank){
					placeholder_id_list[i][4] = rank+1;
					placeholder_id_list[i][7] = 1;
				} else if (placeholder_id_list[i][4] == rank+1){
					placeholder_id_list[i][4] = rank;
					placeholder_id_list[i][7] = 1;
				}
			}
		}
		placeholder_id_list.sort(rank_container_sort);
	}
	if (type=='widget'){
		for(var i=0; i<placeholder_id_list.length; i++){
			if (placeholder_id_list[i][2] == cname){
				if(placeholder_id_list[i][3].length!=0){
					for(var z=0; z<placeholder_id_list[i][3].length; z++){
						if (placeholder_id_list[i][3][z][2] == rank){
							placeholder_id_list[i][3][z][2] = rank+1;
							placeholder_id_list[i][3][z][8] = 1;
							placeholder_id_list[i][7] = 1;
						} else if (placeholder_id_list[i][3][z][2] == rank+1){
							placeholder_id_list[i][3][z][8] = 1;
							placeholder_id_list[i][7] = 1;
							placeholder_id_list[i][3][z][2] = rank;
						}
					}
					placeholder_id_list[i][3].sort(rank_widget_sort);
				}
			}
		}
	}
	display_columns();
}

function rank_container_sort(a,b){
	if (a[2]==b[2]){
		return a[4] - b[4];
	} else {
		if (a[2]<b[2])
			return -1;
		if (a[2]>b[2])
			return 1;
	}
}

function rank_widget_sort(a,b){
	return a[2] - b[2];
}

function obj_edit(type, cname, rank, pos){
	if (type=='widget'){
		pos *=1;
		for(var i=0; i<placeholder_id_list.length; i++){
			if (placeholder_id_list[i][2] == cname){
				if (placeholder_id_list[i][4] == pos){
					for (r = 0; r < placeholder_id_list[i][3].length;r++){
						if(placeholder_id_list[i][3][r][2]==rank){
							show_widget(placeholder_id_list[i][3][r], cname, r, placeholder_id_list[i][1]);
						}
					}
				}
			}
		}
	}
	if (type=='container'){
		for(var i=0; i<placeholder_id_list.length; i++){
			if (placeholder_id_list[i][2] == 'column'+cname){
				if (placeholder_id_list[i][4] == rank){
					show_container(placeholder_id_list[i],i);
				}
			}
		}
	}
}


function cancel_form(){
	document.getElementById("offline_form").innerHTML = "";
}

function delete_element(i,z){
//	alert(i+" "+z);
	if(confirm("Are you sure you want to remove this Web Object?")){
		z = z*1;
		for (index=0;index<placeholder_id_list.length;index++){
			if (placeholder_id_list[index][1]==i){
				placeholder_id_list[index][3].splice(z,1);
				placeholder_id_list[index][7]=1;
			}
		}
		cancel_form();
		display_columns();
	} else {
		cancel_form();
	}
}
function delete_container(i){
	if(confirm("Are you sure you want to remove this container and any widgets that it contains?")){
		i = i*1;
		column = placeholder_id_list[i][2]
		placeholder_id_list.splice(i,1);
		cancel_form();
		display_columns();
	} else {
		cancel_form();
	}
}

function rerank_placeholders(){
	prev_pos = "";
	prev_rank = 0;
	var Ranking = Array(
		Array("columnheader",0),
		Array("column1",0),
		Array("column2",0),
		Array("column3",0),
		Array("column4",0),
		Array("columnfooter",0)
	);
	
	for(index=0;index < placeholder_id_list.length; index++){
		for (y=0;y<Ranking.length;y++){
			if (Ranking[y][0] == placeholder_id_list[index][2]){
				if (placeholder_id_list[index][4]!=Ranking[y][1]+1){
					placeholder_id_list[index][4]=Ranking[y][1]+1;
					Ranking[y][1]++;
				} else {
					Ranking[y][1]++;
				}
			}
		}
	}
}

function display_date(naming, frmDay, frmMonth, frmYear, frmTime){
	var myYear = new Date().getFullYear();
	var monthlist = Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sept","Oct","Nov","Dec");
	var sz="";
	sz+='<select name='+naming+'Day>';
	sz+='	<option value="">DD</option>';
	for(index=1;index<32;index++){
		sz+='	<option value="'+index+'"';
		if (index==frmDay){
			sz+=' selected="true"';
		}
		sz+='>'+index+'</option>';
	}
	sz+='</select>';
	sz+='<select name='+naming+'Month>';
	sz+='	<option value="">MM</option>';
	for(index=0;index<12;index++){
		sz+='	<option value="'+index+'"';
		if (index==frmMonth){
			sz+=' selected="true"';
		}
		sz+='>'+monthlist[index]+'</option>';
	}
	sz+='</select>';
	sz+='<select name='+naming+'Year>';
	for(index=1990;index<myYear+5;index++){
		if (index==myYear){
			sz+='	<option value=""';
			if (frmYear==""){
				sz+=' selected="true"';
			}
			sz+='>YYYY</option>';
		}
		sz+='	<option value="'+index+'"';
		if (index==frmYear){
			sz+=' selected="true"';
		}
		sz+='>'+index+'</option>';
	}
	sz+='</select>';
	sz+='<select name='+naming+'Time class=small>';
	sz+='	<option value="">HH:MM</option>';
	for(hr=0;hr<24;hr++){
		for(minute=0;minute<60;minute+=15){
			if (hr<10){
				h = "0"+hr
			} else {
				h = hr
			}
			if (minute<10){
				m = "0"+minute
			} else {
				m = minute
			}
			sz+='	<option value="'+h+':'+m+':00"';
			if (h+':'+m+':00'==frmTime){
				sz+=' selected="true"';
			}
			sz+='>'+h+':'+m+'</option>';
		}
	}
	sz+='</select>';
	return sz;
}


function add_webObject(cname, containerrank, placeholder){
	cmd = 'ADD';
	webobject_list.sort(sort_objects);
	var sz = '<table border="0" cellspacing="1" cellpadding="0" width="100%" bgcolor="#ebebeb">';
	sz += '<tr><td colspan="2" class="bt"><strong>Choose Web Object</strong></td></tr>';
	sz += '<tr><td class="tablecell">Web Objects</td></tr><tr><td class="tablecell"><select name="web_object_list" id="web_object_list" style="width:200px">'
	for (index = 0; index < webobject_list.length; index++){
		sz+='<option value="'+webobject_list[index][0]+':==:'+webobject_list[index][1]+':==:'+webobject_list[index][2]+'">'+webobject_list[index][2]+'</option>';
	}
	sz+='</select></td></tr>';
	sz+='<tr><td colspan="2" class="tablecell" align="center">';
	sz+='<input type="button" value="Ok" class="bt" onclick="javascript:get_widget(\''+cname+'\', \''+containerrank+'\', \''+placeholder+'\')"> ';
	sz+='<input type="button" value="Cancel" class="bt" onclick="javascript:cancel_form()"> ';
	sz+='</td></tr>';
	sz+='</table>';
	printToId("offline_form", sz)
}
function get_widget(c,r,p){
	w = document.webobjects_form.web_object_list.options[document.webobjects_form.web_object_list.selectedIndex].value.split(":==:");
	widget = Array(w[0], w[2],-1   , ''  , '', Array(), ''  ,w[1], 0, w[1]);
	show_widget(widget,c,r,p);
}

function save_widget(i,z,container){
	i 		= document.webobjects_form.change_containers.options[document.webobjects_form.change_containers.selectedIndex].value;
	prev	= document.webobjects_form.previous_container.value;
	z = z*1;
	if (document.webobjects_form.cmd.value=='ADD'){
		for (index = 0; index < placeholder_id_list.length; index++){
			if ((placeholder_id_list[index][1]==i) && (placeholder_id_list[index][1]==container)){
				if (document.webobjects_form.wobj_rank.value==-1){
					rank = placeholder_id_list[index][3].length+1
				} else {
					rank = document.webobjects_form.wobj_rank.value;
				}
//				alert(document.webobjects_form.wobj_uid.value);
				placeholder_id_list[index][3][placeholder_id_list[index][3].length] = Array(
					document.webobjects_form.wobj_type.value,
			 		document.webobjects_form.wobj_label.value, 
					rank, 
					'', 
					'', 
					document.webobjects_form.wobj_group.value.split(","), 
					'',
					document.webobjects_form.wobj_command.value,
					1,
					document.webobjects_form.wobj_uid.value
				);
				placeholder_id_list[index][7] = 1;
			}
		}
	} else {
		for (index = 0; index < placeholder_id_list.length; index++){
			if (placeholder_id_list[index][1] == i){
				if (prev == i){
					placeholder_id_list[index][3][z] = Array(
						document.webobjects_form.wobj_type.value,
				 		document.webobjects_form.wobj_label.value, 
						document.webobjects_form.wobj_rank.value, 
						'', 
						'', 
						document.webobjects_form.wobj_group.value.split(","), 
						'',
						document.webobjects_form.wobj_command.value,
						1,
						document.webobjects_form.wobj_uid.value
					);
					placeholder_id_list[index][7] = 1;
				} else {
					for (extract_index = 0;extract_index < placeholder_id_list.length; extract_index++){
						if (placeholder_id_list[extract_index][1] == container){
							Returned_list = placeholder_id_list[extract_index][3][z];
							placeholder_id_list[extract_index][3].splice(z,1);
							Returned_list[2] = placeholder_id_list[index][3].length+1;
							Returned_list[8] = 1;
							placeholder_id_list[index][3][placeholder_id_list[index][3].length] = Returned_list;
							placeholder_id_list[index][7] = 1;
						}
					}
				}
			}
		}
	}
	cancel_form();
	display_columns();
}

function show_widget(widget,i,z,container){
	widgetFromDay ="";
	widgetFromMonth ="";
	widgetFromYear ="";
	widgetFromTime ="";
	widgetToDay ="";
	widgetToMonth ="";
	widgetToYear ="";
	widgetToTime ="";
	cmd = 'EDIT';
	if (widget[2]==-1){
		cmd = 'ADD';
		for(var index=0; index<placeholder_id_list.length; index++){
			if (placeholder_id_list[index][2] == i && placeholder_id_list[index][4]==z){
				widget[2]=placeholder_id_list[index][3].length+1;
			}
		}
	}
	placeholder ="";
	for(var index=0; index<placeholder_id_list.length; index++){
		if ((placeholder_id_list[index][2]==i) && (placeholder_id_list[index][4]==z) ){
			placeholder=placeholder_id_list[index][1];
		}
	}
//alert(widget[9]);
	var sz ='<input type="hidden" name="wobj_group" value="'+widget[5].join(",")+'"/>';
	sz+='<input type="hidden" name="cmd" value="'+cmd+'"/>';
	sz+='<input type="hidden" name="wobj_uid" value="'+widget[9]+'"/>';
	sz+='<input type="hidden" name="wobj_type" value="'+widget[0]+'"/>';
	sz+='<input type="hidden" name="wobj_command" value="'+widget[7]+'"/>';
	sz+='<table border="0" cellspacing="1" cellpadding="0" width="100%" bgcolor="#ebebeb">';
	sz+='<tr><td colspan="2" class="bt"><strong>Web Objects Properties</strong></td></tr>';
	sz+='<tr><td class="tablecell">Label</td><td class="tablecell"><input type="hidden" id="wobj_label" name="wobj_label" value="'+widget[1]+'"/>'+widget[1]+'</td></tr>';
	sz+='<tr><td class="tablecell">Rank</td><td class="tablecell"><input type="hidden" id="wobj_rank" name="wobj_rank" value="'+widget[2]+'"/>'+widget[2]+'</td></tr>';
	sz+='<tr><td class="tablecell">Belongs to</td><td class="tablecell"><input type="hidden" name="previous_container" value="'+placeholder+'"><select name="change_containers" id="change_containers">'
	for (index = 0; index < placeholder_id_list.length; index++){
		sz+='<option value="'+placeholder_id_list[index][1]+'"';
		if ((placeholder_id_list[index][2]==i) && (placeholder_id_list[index][1]==container) ){
			sz+=' selected="true"';
		}
		sz+='>'+placeholder_id_list[index][0]+'</option>'
	}
	sz+='</select></td></tr>';
/*
	sz+='<tr><td class="tablecell" colspan="2">Available From</td></tr>';
	sz+='<tr><td colspan="2" class="tablecell">';
	sz+=display_date('AvailFrom', widgetFromDay, widgetFromMonth, widgetFromYear, widgetFromTime);
	sz+='</td></tr>';
	sz+='<tr><td class="tablecell" colspan="2">Available Until</td></tr>';
	sz+='<tr><td colspan="2" class="tablecell">';
	sz+=display_date('AvailFrom', widgetToDay, widgetToMonth, widgetToYear, widgetToTime);
	sz+='</td></tr>';
	sz+='<tr><td class="tablecell">Language</td><td class="tablecell"><select name="language"><option value="">All</option><option value="en">English</option></select></td></tr>';
	sz+='<tr><td class="tablecell" colspan="2">Group Premissions</td></tr><tr><td colspan="2" class="tablecell"><a href="#	">Add More</a></td></tr>';
*/
	sz+='<tr><td colspan="2" class="tablecell" align="center">';
	sz+='<input type="button" value="Ok" class="bt" onclick="javascript:save_widget(\''+i+'\',\''+z+'\',\''+container+'\')"> ';
	sz+='<input type="button" value="Cancel" class="bt" onclick="javascript:cancel_form()"> ';
	sz+='<input type="button" value="Remove" class="bt" onclick="javascript:delete_element(\''+container+'\',\''+z+'\')">';
	sz+='</td></tr>';
	sz+='</table>';
	printToId("offline_form", sz)
	if (cmd == 'ADD'){
		save_widget(i,z,container);
	}
}


function __get_web_objects(){
	webobject_list.sort(sort_objects)
	return webobject_list;
}
function webobjects_submit(){
	if (check_required_fields(1)){
		document.webobjects_form.layout_structure.value = save_changes();
		document.webobjects_form.submit()
	}
}


function save_changes(){
	var sz="";
	var field_splitter =":~:";
	var wobj_splitter =":____:";
	var wobj_field_splitter =":_:";
	var record_splitter =":~~~~:";
	for (index = 0; index < placeholder_id_list.length; index++){
		sz += placeholder_id_list[index][0] + field_splitter;
		sz += placeholder_id_list[index][1] + field_splitter;
		sz += placeholder_id_list[index][2] + field_splitter;
		sz += placeholder_id_list[index][4] + field_splitter;
		sz += placeholder_id_list[index][5] + field_splitter;
		sz += placeholder_id_list[index][6] + field_splitter;
		sz += placeholder_id_list[index][7] + field_splitter;
		sz += placeholder_id_list[index][8] + field_splitter;
		for (obj_index = 0; obj_index < placeholder_id_list[index][3].length; obj_index++){
			for (f_index = 0; f_index < placeholder_id_list[index][3][obj_index].length; f_index++){
				sz+=placeholder_id_list[index][3][obj_index][f_index] 
				if (f_index<placeholder_id_list[index][3][obj_index].length-1){
					sz+=wobj_field_splitter;
				}
			}
			if (obj_index<placeholder_id_list[index][3].length-1){
				sz+=wobj_splitter;
			}
		}
		if (index<placeholder_id_list.length-1){
			sz+=record_splitter;
		}
	}
//	alert(sz);
	return sz;
}


/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Containers
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function show_container(container,i){
	webObjectGroupLayout = getGroupLayout();
	num_cols = container[6];
	var sz ='<input type="hidden" name="container_index" value="'+i+'"><table border="0" cellspacing="1" cellpadding="1" width="100%" bgcolor="#ebebeb">';
	sz+='<tr><td colspan="2" class="bt"><strong>Container Properties</strong></td></tr>';
	sz+='<tr><td class="tablecell">Label</td><td class="tablecell"><input size=10 type="text" maxlength=255 value="'+container[0]+'" id="containerLabel" name="containerLabel"></td></tr>';
	sz+='<tr><td class="tablecell">Position</td><td class="tablecell"><input type="hidden" name="previous_container" value="'+container[2]+'"><select name="change_containers" id="change_containers">'
	sz+='<option value="columnheader"';
	if (container[2]=='columnheader'){
		sz+=' selected="true"';
	}
	sz+='>Header</option>';
	Val=0;
	c=0;
	for(index=0; index<webObjectGroupLayout.length; index++){
		selected=false;
		c++;
		p =c;
		if (container[2]=='column'+c){
			selected=true;
		}
		if (webObjectGroupLayout[index]>1){
			c++;
			if (container[2] == 'column'+(c+index)){
				selected=true;
			}
		}
		if (webObjectGroupLayout[index]>2){
			c++;
			if (container[2] == 'column'+(c+index)){
			selected=true;
			}
		}
		if (webObjectGroupLayout[index]>3){
			c++;
			if (container[2] == 'column'+(c+index)){
			selected=true;
			}
		}
		sz+='<option value="column'+p+'"';
		if (selected){
			sz+=' selected="true"';
		}
		sz+='>Column '+(index+1)+'</option>';
	}
	sz+='<option value="columnfooter"';
	if (container[2]=='columnfooter'){
		sz+=' selected="true"';
	}
	sz+='>Footer</option>';
	sz+='</select></td></tr>';
	if (container[4]==-1){
		column = container[2].split("column");
		rank = countContainers(column[1], '')
	} else {
		rank = container[4];
	}
	sz+='<tr><td class="tablecell">Rank</td><td class="tablecell">'+rank+'</td></tr>';
	sz+='<tr><td class="tablecell">Contains</td><td class="tablecell">'+container[3].length+' widget(s)</td></tr>';
	sz+='<tr><td valign="top" class="tablecell">Columns</td><td class="tablecell"><input type="hidden" name="containerLayout" value="0"/>';
	sz+='<select name="number_of_columns">';
	sz+='<option value="1"';
	if (num_cols==1){
		sz+=' selected="true"';
	}
	sz+='>1</option>';
	sz+='<option value="2"';
	if (num_cols==2){
		sz+=' selected="true"';
	}
	sz+='>2</option>';
	sz+='<option value="3"';
	if (num_cols==3){
		sz+=' selected="true"';
	}
	sz+='>3</option>';
	sz+='<option value="4"';
	if (num_cols==4){
		sz+=' selected="true"';
	}
	sz+='>4</option>';
	sz+='</select></td></tr>';
	if (container[8]+''=='undefined' ||container[8]+''=='' || container[8]+''=='null'){
			containerWidthType="%";
			val=100;
	} else {
		if (container[8].indexOf("px")!=-1) {
			containerWidthType="px"
			val=container[8].substring(0,container[8].indexOf("px"))
		} else {
			containerWidthType="%";
			val=container[8].substring(0,container[8].indexOf("%"));
		}
	}
	sz+='<tr><td class="tablecell">Width</td><td class="tablecell"><input size=3 maxlength=3 type="text" value="'+val+'" id="containerWidth" name="containerWidth">';
	sz+='<select name="containerWidthType">';
		sz+='<option value="%"';
		if (containerWidthType=="%"){
			sz+=' selected="true"';
		}
		sz+='>%</option>';
		sz+='<option value="px"';
		if (containerWidthType=="px"){
			sz+=' selected="true"';
		}
		sz+='>px</option>';
	sz+='</select></td></tr>';
	sz+='<tr><td colspan="2" class="tablecell" align="center">';
	sz+='<input type="button" value="Ok" class="bt" onclick="javascript:save_container(\''+i+'\')"> ';
	sz+='<input type="button" value="Cancel" class="bt" onclick="javascript:cancel_form()"> ';
	sz+='<input type="button" value="Remove" class="bt" onclick="javascript:delete_container(\''+i+'\')">';
	sz+='</td></tr>';
	sz+='</table>';
	document.getElementById("offline_form").innerHTML = sz;
	if (container[5]=='columns'){
		document.webobjects_form.number_of_columns.disabled=true;
	}
}

function add_container(cname){
	pos = (placeholder_id_list.length+1);
	mycontainer = Array('New Container', 'newplaceholder'+pos, 'column'+cname, Array(),'-1','rows',1,0,'100%');
	show_container(mycontainer,-1);
}
function save_container(i){
	i = i*1;
	container_index  = document.webobjects_form.container_index.value;
//	alert(container_index);
	if (container_index==-1){
		column = document.webobjects_form.change_containers.options[document.webobjects_form.change_containers.selectedIndex].value.split('column');
		pos = placeholder_id_list.length;
		placeholder_id_list[pos] = Array(
			document.webobjects_form.containerLabel.value,
			'newplaceholder'+(pos+1),
			document.webobjects_form.change_containers.options[document.webobjects_form.change_containers.selectedIndex].value,
			Array(),
			countContainers(column[1], ''),
			0,
			document.webobjects_form.number_of_columns.options[document.webobjects_form.number_of_columns.selectedIndex].value,
			1,
			document.webobjects_form.containerWidth.value+document.webobjects_form.containerWidthType.options[document.webobjects_form.containerWidthType.selectedIndex].value
		);
		rerank_placeholders()
	} else {
		placeholder_id_list[container_index][0] = document.webobjects_form.containerLabel.value;
		placeholder_id_list[container_index][5] = 0
		placeholder_id_list[container_index][7] = 1;
		placeholder_id_list[container_index][8] = document.webobjects_form.containerWidth.value+document.webobjects_form.containerWidthType.options[document.webobjects_form.containerWidthType.selectedIndex].value;
		placeholder_id_list[container_index][6] = document.webobjects_form.number_of_columns.options[document.webobjects_form.number_of_columns.selectedIndex].value;
//		alert(document.webobjects_form.number_of_columns.options[document.webobjects_form.number_of_columns.selectedIndex].value);
		if (document.webobjects_form.previous_container.value != document.webobjects_form.change_containers.options[document.webobjects_form.change_containers.selectedIndex].value){
			placeholder_id_list[container_index][2] = document.webobjects_form.change_containers.options[document.webobjects_form.change_containers.selectedIndex].value;
			column = document.webobjects_form.change_containers.options[document.webobjects_form.change_containers.selectedIndex].value.split('column');
			placeholder_id_list[container_index][4] = countContainers(column[1], '');
			placeholder_id_list.sort(rank_container_sort);
			rerank_placeholders()
		}
	}
	cancel_form();
	display_columns();
}

	function sort_objects(a,b){
		if (a[2].toLowerCase()==b[2].toLowerCase()){
			return 0;
		}
		if (a[2].toLowerCase()<b[2].toLowerCase()){
			return -1;
		}
		if (a[2].toLowerCase()>b[2].toLowerCase()){
			return 1;
		}
	}
