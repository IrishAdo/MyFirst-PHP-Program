/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- S I T E   L A Y O U T   M A N A G E R
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-
- Author: Adrian Sweeney
- Company: Libertas Solutions
- Copyright: 2003
- Created: 29th Dec 2003 
-
- This code grants the ability to move items around the screens layout zones.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- No copying, modifing, reproducing without the consent of Libertas Solutions
- Libertas Solutions does not supply any warranty with the use of this software.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- $Revision: 1.4 $, $Date: 2004/04/23 15:47:16 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
/*

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	Methods
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function getGroupLayout(){
	var wogl = Array('1','1','1','1');
	myform = get_form();
	for(i=0;i < myform.wol_layout.length;i++){
		if(myform.wol_layout[i].checked){
			wogl = myform.wol_layout[i].value.split('')
		}
	}
	return wogl;
}

function setGroupLayout(wogl){
	if (wogl+''==''){
		wogl="1111";
	}
	myform = get_form();
	for(i=0;i<document.webobjects_form.wol_layout.length;i++){
		if (document.webobjects_form.wol_layout[i].value == wogl){
			document.webobjects_form.wol_layout[i].checked = true;
		} else {
			document.webobjects_form.wol_layout[i].checked = false;
		}
	}
	return wogl;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: display_columns()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will display a column full of containers as they would be called by the XSLT processor
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function display_columns(){
	reIndexRank()
	webObjectGroupLayout = getGroupLayout()
	wogl = webObjectGroupLayout;
	var sz ='<table border="0" cellspacing="1" cellpadding="0" width="100%" bgcolor="#ebebeb">';
	sz	+='<tr>';
	buttons = '<a href="javascript:add_container(\'header\',\''+1+'\')"><img src="/libertas_images/themes/site_administration/container_add.gif" width="20" height="20" alt="Add new Container" border="0"/></a>';
	sz	+='<td valign="top" class="bt"  colspan="4"><table width="100%"><tr><td>Header</td><td align="right">'+buttons+'</td></tr></table></td>';
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
		sz	+='<td valign="top" class="bt"  colspan="'+wogl[i]+'" width="'+(wogl[i]*25)+'%"><table width="100%"><tr><td>Column '+(i+1)+'</td><td align="right">'+buttons+'</td></tr></table></td>';
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
	sz	+='<td valign="top" class="bt"  colspan="4"><table width="100%"><tr><td>Footer</td><td align="right">'+buttons+'</td></tr></table></td>';

	sz	+='</tr>';
	sz	+='<tr>';
	sz	+='<td valign="top" class="tablecell" colspan="4">';
	sz  +=display_placeholders('footer',placeholder_id_list);
	sz  +='</td>';
	sz	+='</tr>';
	sz	+='</table>';
	LIBERTAS_GENERAL_printToId("placeholdersdisplay",sz);
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: display_placeholders(column,placelist,rank)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will display a container for a specific Column
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function display_placeholders(column,placelist,rank){
	var sz 		= '';
	var counter	= 0;
	for (var i = 0; i < placelist.length;i++){
		if (placelist[i][2] == 'column'+column){
			debug('Container "'+placelist[i][0]+'" holds '+placelist[i][3].length+' webObjects');
			maxRank = countContainers(column, placelist[i][4]);
			sz += '<table width="100%"><tr bgcolor="#c0c0c0"><td><strong>'
			if (placelist[i][4]!=''){
				sz += placelist[i][4]+') ';
			}
			sz += placelist[i][0]+' - '+getlabel(placelist[i][9])+'</strong></td>';
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
			sz +='</tr>'+displayWidgets(placelist[i][3], placelist[i][2], placelist[i][4]);
			sz +='<tr><td>';
			sz +='<a href="javascript:add_webObject(\''+placelist[i][2]+'\', \''+placelist[i][4]+'\', \''+placelist[i][1]+'\',\''+placelist[i][9]+'\')">';
				sz +='Add new '+getlabel(placelist[i][9]);
			sz +='</a></td></tr></table>';
			counter++;
		}
	}
	return sz;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: displayWidgets(hlist,cname,pos)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will list the widgets in a container
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function displayWidgets(hlist,cname,pos){
	var sz ="";
	for(var i =0;i<hlist.length;i++){
		debug("WebObject type ::'"+hlist[i][0]+"', '"+hlist[i][1]+"'");
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

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: move_container(index, column, direction)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will allow a user to move a container
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function move_container(index, column, direction){
	debug("move_container('"+index+"', '"+column+"', '"+direction+"')");
	wogl = getGroupLayout();
	debug("Group Layout is :: [wogl]");
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


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: swap(s1, s2)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will swap two columns used by move container function
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
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
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: countContainers(column, rank)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- count the number of containers in a Column so that we can add a new contianer and add it to the end of the 
- rank
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function countContainers(column, rank){
	var countc=0;
	for(var i=0; i<placeholder_id_list.length; i++){
		if (placeholder_id_list[i][2] == 'column'+column){
			countc++;
		}
	}
	return countc;
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: rank_up(type, cname, rank, pos)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- this function is designed to move either webobjects or container
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function rank_up(type, cname, rank, pos){
	rank = rank*1;
	breakpoint();
	debug("rank_up('"+type+"', '"+cname+"', '"+rank+"', '"+pos+"')")
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
		debug(cname);
		for(var i=0; i<placeholder_id_list.length; i++){
			if (placeholder_id_list[i][2] == cname){
				debug("Position " + placeholder_id_list[i][2] + "Contains " + placeholder_id_list[i][3].length);
				for(var z=0; z < placeholder_id_list[i][3].length; z++){
					if (placeholder_id_list[i][4]==pos && placeholder_id_list[i][3][z][2] == rank-1){
						placeholder_id_list[i][3][z][2] = rank;
						placeholder_id_list[i][7] 		= 1;
						placeholder_id_list[i][3][z][8] = 1;
					} else if (placeholder_id_list[i][4]==pos && placeholder_id_list[i][3][z][2] == rank){
						placeholder_id_list[i][3][z][2] = rank-1;
						placeholder_id_list[i][7] 		= 1;
						placeholder_id_list[i][3][z][8] = 1;
					}
				}
				placeholder_id_list[i][3].sort(rank_widget_sort);
			}
		}
	}
	breakpoint();
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
	LIBERTAS_GENERAL_printToId("offline_form","");
}

function delete_element(i,z){
debug("deleteing element ["+i+" "+z+"]\n");
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


function add_webObject(cname, containerrank, placeholder,filter){
	cmd = 'ADD';

//	webobject_list.sort(sort_objects);
	var sz = '<table border="0" cellspacing="1" cellpadding="0" width="100%" bgcolor="#ebebeb">';
	sz += '<tr><td colspan="2" class="bt" ><strong>Choose Web Object</strong></td></tr>';
	if (filter=="__OPEN__"){
		sz += '<tr><td class="tablecell"><select name="web_c_type" id="web_ctype" style="width:200px;" onchange="javascript:cacheData.showcategory(this)">';
			sz+="<option value=''>Please select a category first</option>"
		for(var index =0 ; index<webTypes.length;index++){
			if (webTypes[index][0]!="__OPEN__"){
				sz+="<option value='"+webTypes[index][0]+"'>"+webTypes[index][1]+"</option>"
			}
		}
		sz+='</select></td></tr>';
		filteredOptions	 = "";
		d='display:none';
		sz += '<tr><td class="tablecell"><select name="web_object_list" id="web_object_list" style="width:200px;'+d+'" size="10">'+filteredOptions+'</select></td></tr>';
	} else {
	//	cacheData.getCache(filter);
		filteredOptions = cacheData.show(filter,0);
		if (filteredOptions!=''){
			d='';
		} else {
			d='display:none';
		}
		sz += '<tr><td class="tablecell"><select name="web_object_list" id="web_object_list" style="width:200px;'+d+'" size="10">'+filteredOptions+'</select></td></tr>';
	}
/*	sz += '<tr><td class="tablecell">Web Objects</td></tr><tr><td class="tablecell"><select name="web_object_list" id="web_object_list" style="width:200px">'
	for (index = 0; index < webobject_list.length; index++){
		sz+='<option value="'+webobject_list[index][0]+':==:'+webobject_list[index][1]+':==:'+webobject_list[index][2]+'">'+webobject_list[index][2]+'</option>';
	}
	sz+='</select></td></tr>';*/
	label	="";
	val		="";
/*	for (index = 0; index < webobject_list.length; index++){
		sz+='<tr><td class="tablecell">' + label + '</td><td class="tablecell">' + val + '</td></tr>';
	}*/
	sz+='<tr><td colspan="2" class="tablecell" align="center">';
	sz+='<input type="button" value="Ok" class="bt" id="add_web_object_ok_button" onclick="javascript:get_widget(\''+cname+'\', \''+containerrank+'\', \''+placeholder+'\')" style="'+d+'"> ';
	sz+='<input type="button" value="Cancel" class="bt"  onclick="javascript:cancel_form()"> ';
	sz+='</td></tr>';
	sz+='</table>';
	LIBERTAS_GENERAL_printToId("offline_form", sz)
}

function get_widget(c,r,p){
	w = document.webobjects_form.web_object_list.options[document.webobjects_form.web_object_list.selectedIndex].value.split(":==:");
	widget = Array(w[0], w[2],-1   , ''  , '', Array(), ''  ,w[1], 0, w[1], Array());
	show_widget(widget,c,r,p);
}

function save_widget(i,z,container){
	breakpoint();
	var current_container 	= document.webobjects_form.change_containers.options[document.webobjects_form.change_containers.selectedIndex].value;
	var prev_container		= document.webobjects_form.previous_container.value;
	z = z*1;
	if (document.webobjects_form.cmd.value=='ADD'){
		for (index = 0; index < placeholder_id_list.length; index++){
			if ((placeholder_id_list[index][1]==current_container) && (placeholder_id_list[index][1]==container)){
				if (document.webobjects_form.wobj_rank.value==-1){
					rank = placeholder_id_list[index][3].length+1
				} else {
					rank = document.webobjects_form.wobj_rank.value;
				}
				debug(document.webobjects_form.wobj_uid.value);
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
					document.webobjects_form.wobj_uid.value,
					Array(Array("text-align","left"))//, Array("width","100px")
				);
				placeholder_id_list[index][7] = 1;
			}
		}
	} else {
		for (index = 0; index < placeholder_id_list.length; index++){
			if (placeholder_id_list[index][1] == current_container){
				if (prev_container == current_container){
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
						document.webobjects_form.wobj_uid.value,
						Array(Array("text-align", document.webobjects_form.wobj_alignment.value))//Array("width", document.webobjects_form.wobj_Width.value + document.webobjects_form.wobj_WidthType.value)
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
							placeholder_id_list[index][7]	= 1;
							placeholder_id_list[index][10]	= Array(Array("text-align", document.webobjects_form.wobj_alignment.value)); //,  Array("width", document.webobjects_form.wobj_Width.value + document.webobjects_form.wobj_WidthType.value)
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
	placeholder =container;
/*
	for(var index=0; index<placeholder_id_list.length; index++){
		if ((placeholder_id_list[index][2]==i) && (placeholder_id_list[index][4]==z) ){
			placeholder=placeholder_id_list[index][1];
		}
	}
*/
	wobj_WidthType	= "";
	val 			= "";
	var sz ='<input type="hidden" name="wobj_group" value="'+widget[5].join(",")+'"/>';
	sz+='<input type="hidden" name="cmd" value="'+cmd+'"/>';
	sz+='<input type="hidden" name="wobj_uid" value="'+widget[9]+'"/>';
	sz+='<input type="hidden" name="wobj_type" value="'+widget[0]+'"/>';
	sz+='<input type="hidden" name="wobj_command" value="'+widget[7]+'"/>';
	sz+='<table border="0" cellspacing="1" cellpadding="0" width="100%" bgcolor="#ebebeb">';
	sz+='<tr><td colspan="2" class="bt" ><strong>Web Objects Properties</strong></td></tr>';
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
	my_alignment = retrieve_property(widget[10],'text-align');
	sz+='<tr><td class="tablecell">Alignment</td><td class="tablecell"><select name="wobj_alignment"><option value="left"';
	if (my_alignment=="left"){
		sz+=' selected';
	}
	sz+='>Left</option><option value="center"';
	if (my_alignment=="center"){
		sz+=' selected';
	}
	sz+='>Center</option><option value="right"';
	if (my_alignment=="right"){
		sz+=' selected';
	}
	sz+='>Right</option></select></td></tr>';
	sz+='<tr><td colspan="2" class="tablecell" align="center">';
	sz+='<input type="button" value="Ok" class="bt"  onclick="javascript:save_widget(\''+i+'\',\''+z+'\',\''+container+'\')"> ';
	sz+='<input type="button" value="Cancel" class="bt"  onclick="javascript:cancel_form()"> ';
	sz+='<input type="button" value="Remove" class="bt"  onclick="javascript:delete_element(\''+container+'\',\''+z+'\')">';
	sz+='</td></tr>';
	sz+='</table>';
	LIBERTAS_GENERAL_printToId("offline_form", sz)
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
		sz += placeholder_id_list[index][9] + field_splitter;
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
	return sz;
}


/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Create Tag
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function createHTMLTAG(parameters){
		var myTag="";
		if (parameters['tag']){
			myTag = document.createElement(parameters['tag']);
			for(var i in parameters){
				if (i!="tag"){
					myTag.setAttribute(i	,parameters[i]);
				}
				if (i=="myoptions"){
					for(var index in parameters[i]){
						myTag.options[myTag.options.length] = new Option(parameters[i][index]["label"], parameters[i][index]["value"]);
						if (parameters[i][index]["selected"]){
							myTag.options[myTag.options.length-1].selected = true;
						}
					}
				}
			}
		} else {
			myTag = null;
		}
		return myTag;
	}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Containers
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function show_container(container,i){
	debug("show_container({" + container + "}, '" + i + "')")
	webObjectGroupLayout = getGroupLayout();
	num_cols = container[6];
	var containertypelist = "";
	var containerisnew = 0;
	//containertypelist =
	var sz ='';
	containerIndex = createHTMLTAG({tag:'INPUT', name:"container_index", id:"container_index", type:"hidden", value:i});
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Table
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	containerTable = createHTMLTAG({tag:'TABLE',"border":"0","cellspacing":"1","cellpadding":"1","width":"100%","bgcolor":"#ebebeb"});
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- New Row
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	containerRow	= createHTMLTAG({tag:'TR'});
		containerCell	= createHTMLTAG({tag:'TD',"colspan":"2","class":"bt"});
			containerStrong = createHTMLTAG({tag:'STRONG'});
				containerStrong.innerHTML = "Container Properties";
			containerCell.appendChild(containerStrong);
			containerStrong= null;
		containerRow.appendChild(containerCell);
	containerTable.appendChild(containerRow);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- New Row
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	containerRow				= createHTMLTAG({tag:'TR'});
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
		containerCell.innerHTML = "Label";
		containerRow.appendChild(containerCell);
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
			containerInput		= createHTMLTAG({tag:'INPUT', size:"10", type:"text", maxlength:255, value:container[0], id:"containerLabel", name:"containerLabel"});
			containerCell.appendChild(containerInput);
		containerRow.appendChild(containerCell);
	containerTable.appendChild(containerRow);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- New Row
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	if (container[9]==-1){
		containerisnew = 1;
		containerRow				= createHTMLTAG({tag:'TR'});
			containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
			containerCell.innerHTML = "Type";
			containerRow.appendChild(containerCell);
			var ctypeList = new Array();
			for(var index =0 ; index < webTypes.length;index++){
				if (webTypes[index][0]!="__UD__"){
					ctypeList[index] = new Array();
					ctypeList[index]["value"] = webTypes[index][0];
					ctypeList[index]["label"] = webTypes[index][1];
					if (container[9]==webTypes[index][0]){
						ctypeList[index]["selected"] = "true";
					}
				}
			}
			containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
				containerInput		= createHTMLTAG({tag:'SELECT', id:"containerType", name:"containerType", "myoptions":ctypeList});
				containerCell.appendChild(containerInput);
			containerRow.appendChild(containerCell);
		containerTable.appendChild(containerRow);
		hiddenContainerType		= "";
	}else {
		containerisnew=0;
		hiddenContainerType		= createHTMLTAG({tag:'INPUT', id:"containerType", name:"containerType", type:"hidden", value:container[9]});
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- New Row
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	containerRow				= createHTMLTAG({tag:'TR'});
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
		containerCell.innerHTML = "Position";
		containerRow.appendChild(containerCell);
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
			containerInput		= createHTMLTAG({tag:'INPUT', size:"10", type:"hidden", value:container[2], id:"previous_container", name:"previous_container"});
			
			containerCell.appendChild(containerInput);
			
			var containerList = new Array();
			insertPosition = containerList.length;
			containerList[insertPosition]=new Array();
			containerList[insertPosition]['value'] = "columnheader";
			containerList[insertPosition]['label'] = "Header";
			if (container[2]=='columnheader'){
				containerList[insertPosition]['selected'] = "true";
			}
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
				insertPosition = containerList.length;
				containerList[insertPosition] = new Array();
				containerList[insertPosition]['value'] = 'column'+p;
				containerList[insertPosition]['label'] = 'Column '+(1+index);
				if (selected){
					containerList[insertPosition]['selected'] = "true";
				}
			}
			insertPosition = containerList.length;
			containerList[insertPosition]=new Array();
			containerList[insertPosition]['value'] = "columnfooter";
			containerList[insertPosition]['label'] = "Footer";
			if (container[2]=='columnfooter'){
				containerList[insertPosition]['selected'] = "true";
			}
			containerInput		= createHTMLTAG({tag:'SELECT', 
													id:"change_containers", 
													name:"change_containers",
													"myoptions":containerList
												}
											);
			containerCell.appendChild(containerInput);
		containerRow.appendChild(containerCell);
	containerTable.appendChild(containerRow);	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- New Row
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	if (container[4]==-1){
		column = container[2].split("column");
		rank = countContainers(column[1], '')
	} else {
		rank = container[4];
	}
	containerRow				= createHTMLTAG({tag:'TR'});
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
		containerCell.innerHTML = "Rank";
		containerRow.appendChild(containerCell);
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
		containerCell.innerHTML = rank;
		containerRow.appendChild(containerCell);
	containerTable.appendChild(containerRow);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- New Row
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	containerRow				= createHTMLTAG({tag:'TR'});
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
		containerCell.innerHTML = "Contains";
		containerRow.appendChild(containerCell);
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
		containerCell.innerHTML = container[3].length;
		containerRow.appendChild(containerCell);
	containerTable.appendChild(containerRow);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- New Row
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	containerRow				= createHTMLTAG({tag:'TR'});
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
		containerCell.innerHTML = "Columns";
		containerRow.appendChild(containerCell);
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
			containerInput		= createHTMLTAG({tag:'INPUT', type:"hidden", value:0, id:"containerLayout", name:"containerLayout"});
			containerCell.appendChild(containerInput);
			var containerList = new Array();
			for(index=1; index<5; index++){
				containerList[index] = new Array();
				containerList[index]['value'] = index;
				containerList[index]['label'] = index;
				if (index == num_cols){
					containerList[index]['selected'] = "true";
				}
			}
			containerInput		= createHTMLTAG({tag:'SELECT', 
													id:"number_of_columns", 
													name:"number_of_columns",
													"myoptions":containerList
												}
											);
			containerCell.appendChild(containerInput);
		containerRow.appendChild(containerCell);
	containerTable.appendChild(containerRow);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- New Row
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	containerRow				= createHTMLTAG({tag:'TR'});
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
		containerCell.innerHTML = "Columns";
		containerRow.appendChild(containerCell);
		containerCell			= createHTMLTAG({tag:'TD',"class":"tablecell"});
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
			containerInput		= createHTMLTAG({tag:'INPUT', size:3, maxlength:3, type:"text", value:val, id:"containerWidth", name:"containerWidth"});
			containerCell.appendChild(containerInput);
			var containerList = new Array();
			containerList[0] = new Array();
			containerList[0]['value'] = "%";
			containerList[0]['label'] = "%";
			containerList[1] = new Array();
			containerList[1]['value'] = "px";
			containerList[1]['label'] = "px";
			if (containerWidthType=="%"){
				containerList[0]['selected'] = "true";
			}
			if (containerWidthType=="px"){
				containerList[1]['selected'] = "true";
			}
			containerInput		= createHTMLTAG({tag:'SELECT', 
													id:"containerWidthType", 
													name:"containerWidthType",
													"myoptions":containerList
												}
											);
			containerCell.appendChild(containerInput);
		containerRow.appendChild(containerCell);
	containerTable.appendChild(containerRow);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- New Row
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	containerRow				= createHTMLTAG({tag:'TR'});
		containerCell			= createHTMLTAG({tag:'TD', "class":"tablecell", "colspan":"2","align":"center"});
			containerButton		= createHTMLTAG({tag:'INPUT', "class":"bt", "value":"Ok","type":"button","onclick":"javascript:save_container('"+i+"');"});
			containerCell.appendChild(containerButton);
			containerButton		= createHTMLTAG({tag:'INPUT', "class":"bt", "value":"Cancel","type":"button","onclick":"javascript:cancel_form();"});
			containerCell.appendChild(containerButton);
			if (i!=-1){ // new container no remove button just ok and cancel
				containerButton		= createHTMLTAG({tag:'INPUT', "class":"bt", "value":"Remove","type":"button","onclick":"javascript:delete_container('"+i+"');"});
				containerCell.appendChild(containerButton);
			}
		containerRow.appendChild(containerCell);
	containerTable.appendChild(containerRow);
	if (containerisnew==0){
		LIBERTAS_GENERAL_printToId("offline_form", hiddenContainerType.outerHTML+ containerIndex.outerHTML + containerTable.outerHTML);
	} else {
		LIBERTAS_GENERAL_printToId("offline_form", containerIndex.outerHTML + containerTable.outerHTML);
	}
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: add_container(cname)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function add_container(cname){
	pos = (placeholder_id_list.length+1);
	mycontainer = Array('New Container', 'newplaceholder'+pos, 'column'+cname, Array(),'-1','rows',1,0,'100%',-1);
	show_container(mycontainer,-1);
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: saveContainer(i)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function save_container(i){
	debug("save_container("+i+");")
	i = i*1;
	container_index  = document.webobjects_form.container_index.value;
	if (document.webobjects_form.containerType.tagName == "select"){
		val = document.webobjects_form.containerType.options[document.webobjects_form.containerType.selectedIndex].value
	} else {
		val  = document.webobjects_form.containerType.value;
	}
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
			document.webobjects_form.containerWidth.value+document.webobjects_form.containerWidthType.options[document.webobjects_form.containerWidthType.selectedIndex].value,
			val
		);
		rerank_placeholders()
	} else {
		placeholder_id_list[container_index][0] = document.webobjects_form.containerLabel.value;
		placeholder_id_list[container_index][5] = 0;
		placeholder_id_list[container_index][7] = 1;
		placeholder_id_list[container_index][8] = document.webobjects_form.containerWidth.value+document.webobjects_form.containerWidthType.options[document.webobjects_form.containerWidthType.selectedIndex].value;
		placeholder_id_list[container_index][9] = val;
		placeholder_id_list[container_index][6] = document.webobjects_form.number_of_columns.options[document.webobjects_form.number_of_columns.selectedIndex].value;
		if (document.webobjects_form.previous_container.value != document.webobjects_form.change_containers.options[document.webobjects_form.change_containers.selectedIndex].value){
			placeholder_id_list[container_index][2] = document.webobjects_form.change_containers.options[document.webobjects_form.change_containers.selectedIndex].value;
			column = document.webobjects_form.change_containers.options[document.webobjects_form.change_containers.selectedIndex].value.split('column');
			placeholder_id_list[container_index][4] = countContainers(column[1], '');
			placeholder_id_list.sort(rank_container_sort);
			rerank_placeholders()
		}
	}
	cancel_form();
	reIndexRank();
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

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: reIndexRank()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will reindex rank that will remove the problem of having to containers with the same rank in 
- the same column.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function reIndexRank(){
	positionial_columns = Array("1","2","3","4");
	webObjectGroupLayout = getGroupLayout()
	wogl = webObjectGroupLayout;
	debug ("WOGL::"+wogl);
	var rank =1;
	var previous_column="";
	debug ("len :: "+wogl.length);
	c=1;
	rank=1;
	for (var i = 0; i < placeholder_id_list.length;i++){
		if (placeholder_id_list[i][2] == 'columnheader'){
			debug(placeholder_id_list[i][2] +" becomes "+rank+" from "+placeholder_id_list[i][4]);
			placeholder_id_list[i][4]=rank;
			reIndexObjects(placeholder_id_list[i][3]);
			rank++;
		}
	}	
	rank=1;
	for (var i = 0; i < placeholder_id_list.length;i++){
		if (placeholder_id_list[i][2] == 'columnfooter'){
			debug(placeholder_id_list[i][2] +" becomes "+rank+" from "+placeholder_id_list[i][4]);
			placeholder_id_list[i][4]=rank;
			reIndexObjects(placeholder_id_list[i][3]);
			rank++;
		}
	}	
	for(var x=0; x<wogl.length;x++){
		debug("C :: "+c);
		rank=1;
		for (var i = 0; i < placeholder_id_list.length;i++){
			debug("checking "+c);
			if (placeholder_id_list[i][2] == 'column'+c){
				debug(placeholder_id_list[i][2] +" becomes "+rank+" from "+placeholder_id_list[i][4]);
				placeholder_id_list[i][4]=rank;
				reIndexObjects(placeholder_id_list[i][3]);
				rank++;
			}
		}	
		if (wogl[x]>1){
			c++;
			debug("checking "+c);
			for (var i = 0; i < placeholder_id_list.length;i++){
				if (placeholder_id_list[i][2] == 'column'+c){
					debug(placeholder_id_list[i][2] +" becomes "+rank+" from "+placeholder_id_list[i][4]);
					placeholder_id_list[i][4] = rank;
					placeholder_id_list[i][2] = 'column'+(c-1);
					reIndexObjects(placeholder_id_list[i][3]);
					rank++;
				}
			}	
		}
		if (wogl[x]>2){
			c++;
			debug("checking "+c);
			for (var i = 0; i < placeholder_id_list.length;i++){
				if (placeholder_id_list[i][2] == 'column'+c){
					debug(placeholder_id_list[i][2] +" becomes "+rank+" from "+placeholder_id_list[i][4]);
					placeholder_id_list[i][4]=rank;
					placeholder_id_list[i][2] = 'column'+(c-2);
					reIndexObjects(placeholder_id_list[i][3]);
					rank++;
				}
			}	
		}
		if (wogl[x]>3){
			c++;
			debug("checking "+c);
			for (var i = 0; i < placeholder_id_list.length;i++){
				if (placeholder_id_list[i][2] == 'column'+c){
					debug(placeholder_id_list[i][2] +" becomes "+rank+" from "+placeholder_id_list[i][4]);
					placeholder_id_list[i][4]=rank;
					placeholder_id_list[i][2] = 'column'+(c-3);
					reIndexObjects(placeholder_id_list[i][3]);
					rank++;
				}
			}	
		}
		c++;
	}
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: reIndexObjects(list)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will reindex the list of web objects in a container.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function reIndexObjects(list){
	var current_rank=1;
	for (var i = 0; i < list.length;i++){
		list[i][2]=current_rank;
		current_rank++;
	}
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: retrieve_property(list, key)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will retrieve a property of a widget
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function retrieve_property(list, key){
	if (list instanceof Array){
		for (var i=0 ; i<list.length ; i++){
			if (list[i][0]==key){
				return list[i][1];
			}
		}
	}
	return "";
}
function set_property(list, key, value){
	try{
		if (list instanceof Array){
			for (var i=0 ; i<list.length ; i++){
				if (list[i][0]==key){
					list[i][1]=value;
					return 1;
				}
			}
		}
		list[list.length] = Array(key, value);
	} catch(e){
		debug_alert("Unable to set property (key=>["+key+"] value=>["+value+"])");
	}
	return 1;
}

function getlabel(index){
	for (i=0;i<webTypes.length;i++){
		if (webTypes[i][0]==index){
			return webTypes[i][1];
		}
	}
	return "Web Object";
}



/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	General variables
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var webObjectGroupLayout	= new Array();
var cacheData 				= new CacheDataObject();

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-
-								C L A S S   -   C a c h e D a t a O b j e c t
-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function is designed to allow the system to cache the list of webobjects into an array that will allow 
- us to decide if the system needs to request the list of web objects from the database.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: CacheDataObject()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function will retrieve a property of a widget
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function CacheDataObject(){
	//properties
	this.list					= new Array();
	this.downloading			= "";
	// methods
	this.getCache				= __CDO_getCache;
	this.setCache				= __CDO_setCache;
	this.show					= __CDO_show;
	this.extract_information	= __CDO_extract_information;
	this.retrieve_info			= __CDO_retrieve_info;
	this.showcategory			= __CDO_showcategory;
}
function __CDO_showcategory(t){
	if (t.selectedIndex!=0){
		this.show(t.options[t.selectedIndex].value,1);
		
	} else {
		document.getElementById("web_object_list").style.display='none';
		document.getElementById("add_web_object_ok_button").style.display='none';
	}
}

function __CDO_show(retrieveType, formelement){
	if (formelement+""=="undefined"){
		formelement = 0;
	}
	this.getCache(retrieveType);
	var output ="";
	if(formelement==1){
		f = get_form();
		f.web_object_list.options.length=0;
	}
	for (var i=0; i<this.list[retrieveType].length ; i++){
		val_str = this.list[retrieveType][i][0]+':==:'+this.list[retrieveType][i][1]+':==:'+this.list[retrieveType][i][2];
		if (formelement==0){
			output += "<option value='"+val_str+"'";
			if(i==0) output+=" selected='true' ";
			output +=">"+this.list[retrieveType][i][2]+"</option>";
		} else {
			f.web_object_list.options[i] = new Option(this.list[retrieveType][i][2], val_str);
			if(i==0) f.web_object_list.options[i].selected=true;
		}
	}
	if (formelement==1){
		document.getElementById("web_object_list").style.display='';
//		document.getElementById("add_web_object_ok_button").style.display='';
	}
	return output;
}
function __CDO_setCache(retrieveType,data){
	this.list[retrieveType] = data;
}
function __CDO_getCache(retrieveType){
	var ok=0;
	if (this.list[retrieveType]+"" == "undefined"){
			ok = 1;
	} else {
		if (this.list[retrieveType].length==0){
			ok = 1;
		}
	}
	if(ok==1){
		this.list[retrieveType] = new Array();
		if (retrieveType=="__UD__"){
			p = "filter=WEBOBJECTS_"
		} else {
			p="filter="+retrieveType;
		}
		this.extract_information('webobjects',p,retrieveType);
	}
}

function __CDO_extract_information(szType, parameter,mylist,timesthrough){
	if (timesthrough+""=="undefined"){
		timesthrough=1;
	}
	/*
		check to see if the cache is available for information yet? 
	*/
	this.downloading = mylist;
//	alert(cache_data.document.readyState +"||"+ timesthrough);
	if (cache_data.document.readyState != 'complete'){
		timesthrough++;
		setTimeout("cacheData.extract_information('"+szType+"', '"+parameter+"','"+mylist+"',"+timesthrough+");",1000);
		return;
	} else {
		if (cache_data.frmDoc.webobjects_data.value==''){
			cache_data.__extract_info(szType, parameter);
		}
		setTimeout("cacheData.retrieve_info('"+szType+"', '"+parameter+"', '"+mylist+"');",1000);
//		alert(mylist);
		return;
	}
}

function __CDO_retrieve_info(szType, parameter, mylist){
alert("Retrieving Information ");
//	if (this.downloading==mylist){
//		alert(mylist);
		if (mylist+""!="undefined"){
			if (cache_data.frmDoc.webobjects_data.value!=''){
				fieldValue = cache_data.frmDoc.webobjects_data.value;
				cache_data.frmDoc.webobjects_data.value = "";
				var prev_style="",identifier=-1 ,parent_identifier=-1;
				var mystyle ="";
 				var output="";
				found			= 0;
				tmp 			= new String(fieldValue);
				if (tmp.indexOf("|1234567890|")>0){
					myArray 	= tmp.split("|1234567890|");
				} else {
					myArray 	= new Array();
					myArray[0]	= tmp;
				}
				
				len				= myArray.length;
				var f = get_form();
				f.web_object_list.options.length=0;
				for (var i = 0; i<len; i++){
					split_list = myArray[i].split("::");
					label_str = fix(split_list[2].split("&#39;").join("'"));
	//				output += "<option value='"+split_list[1]+"'>"+label_str+"</option>";
					val_str = split_list[0]+':==:'+split_list[1]+':==:'+split_list[2];
					f.web_object_list.options[i] = new Option(label_str, val_str);
//					alert(mylist+" "+i+" = "+val_str);
					this.list[mylist][i] = new Array(split_list[0],split_list[1],split_list[2]);
				}
				f.web_object_list.options[0].selected 	= true;
				f.web_object_list.options.selectedindex = 0;
				document.getElementById("web_object_list").style.display='';
				document.getElementById("add_web_object_ok_button").style.display='';
			} else {
				setTimeout("cacheData.retrieve_info('"+szType+"', '"+parameter+"', '"+mylist+"');",1000);
			}
		}
//	}
}

function fix(str){
	return str;
}
setTimeout("display_columns();",1000);
