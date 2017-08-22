var title_defined =0;
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Javascript Object to manage the ranking of content
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function webobjects_submit(){
	f = get_form();
	f.submit();
}
function WebObjectList(order){
	this.list		= new Array();
	this.sortorder	= order;
	this.filter		= "";
	this.type		= new Array();
	
	
	this.set				= __WOC_ranking_set_attribute;
	this.draw				= __WOC_ranking_display;
	this.add				= __WOC_ranking_add;
	this.insertdata 		= __WOC_insertdata;
	this.removedata			= __WOC_file_remover;
	this.gen_hidden 		= __WOC_generate_hidden;
	this.move				= __WOC_move;
	this.show				= __WOC_show;
	this.cancel_form		= __WOC_cancel;
	this.property			= __WOC_property;
	this.updateProperties	= __WOC_propertyUpdate;
	this.tidy				= __WOC_tidy; 
}

function __WOC_RankItem(){
	this.title				= "";
	this.identifier 		= -1;
	this.rank 				= -1;
	this.update				= 0;
	this.properties			= {"text-align":"left", "width":"100%", "label":""};
	this.propertiesOptions	= {"text-align":new Array("Left","Center","Right"), "width":"", "label":""};
	this.propertiesToString = __WOCI_PropToString
}

function __WOC_ranking_add(title, identifier, rank, props){
	i = this.list.length;
	this.list[i] = new __WOC_RankItem();
	this.set(i, "title", title);
	this.set(i, "identifier", identifier);
	if (rank+''!='undefined'){
		this.set(i, "rank", rank);
	} else {
		this.set(i, "rank", i);
	}
	for(var index=0; index < props.length; index++){
		this.list[i].properties[props[index][0]] = this.tidy(props[index][1]);
	}
}

function __WOC_ranking_set_attribute(index, attribute_name, attribute_value){
	try{
		if (this.list[index]+'' != 'undefined'){
			switch(attribute_name){
				case 'title':
					this.list[index].title		= attribute_value;
					break;
				case 'identifier':
					this.list[index].identifier = attribute_value;
					break;
				case 'rank':
					this.list[index].rank		= attribute_value;
					break;
				case 'update':
					this.list[index].update		= attribute_value;
					break;
				default:
					alert('Unknown Attribute "'+attribute_name+'" supplied.');
			}
		} else {
			alert('Unknown Index "'+index+'" supplied');
		}
	} catch(e){
		alert(e.message);
	}
}

function __WOC_ranking_display(field){
	c=0;
	str = "<tr>";
	str += "<td align='right'><a href='javascript:webobjectlist.show();' accesskey='a' title='Add a new webobject to the list [a]'>Add to list</a></td></tr>";
	if (this.list.length!=0){
		str += "<tr>";
		str += "<td><strong>Title</strong></td></tr>";
		previous = "";
		for (i = 0;i<this.list.length;i++){
			if (c % 2 ==1 ){
				bgcolor="#ffffff";
			} else {
				bgcolor="#ebebeb";
			}
			if(c==0 && this.list.length>1){
				options = "<td></td><td><a href='javascript:webobjectlist.move("+i+", "+(i+1)+")'>Down</a></td>";
			} else {
				if (this.list.length==1){
					options = "<td></td><td></td>";
				} else if (c==this.list.length-1){
					options = "<td><a href='javascript:webobjectlist.move("+i+", "+(i-1)+")'>Up</a></td><td></td>";
				} else {
					options = "<td><a href='javascript:webobjectlist.move("+i+", "+(i-1)+")'>Up</a></td><td><a href='javascript:webobjectlist.move("+i+", "+(i+1)+")'>Down</a></td>";
				}
			}
			options += "<td><a href='javascript:webobjectlist.removedata("+i+")'>Remove</a></td>";
			options += "<td><a href='javascript:webobjectlist.property("+i+")'>Properties</a></td>";
			column = "<td>";
			column += "<input type='hidden' name='id[]' value='"+ this.list[i].identifier +"'/>";
			column += "<input type='hidden' name='rank[]' value='"+ this.list[i].rank +"'/>";
			column += "</td>";
			str += "<tr bgcolor='"+bgcolor+"'>" + column + "<td>" + this.list[i].title + "</td>" + options + "</tr>";
			c++;
		}
	}
	document.getElementById("display_list_of_webobjects").innerHTML ="<input type='hidden' name='number_of' value='"+c+"'/><table cellpadding='3' cellspacing='0' border='0'>"+str+"</table>";
	this.gen_hidden();
}

function __WOC_move(src, dst, move_rank){
	tmp_title 					= this.list[src].title;
	tmp_identifier 				= this.list[src].identifier;
	tmp_menu 					= this.list[src].menu;
	tmp_titlePage 				= this.list[src].titlePage;
	if (move_rank+'' != 'undefined'){
		tmp_rank				= this.list[src].rank;
	}
	tmp_icon					= this.list[src].icon;
	tmp_titlePage				= this.list[src].titlePage;

	this.list[src].title		= this.list[dst].title;
	this.list[src].identifier	= this.list[dst].identifier;
	this.list[src].menu			= this.list[dst].menu;
	this.list[src].titlePage	= this.list[dst].titlePage;
	if (move_rank+'' != 'undefined'){
		this.list[src].rank		= this.list[dst].rank;
	}
	this.list[src].icon			= this.list[dst].icon;
	this.list[src].titlepage	= this.list[dst].titlePage;

	this.list[dst].title 		= tmp_title;
	this.list[dst].identifier	= tmp_identifier;
	this.list[dst].menu			= tmp_menu;
	this.list[dst].titlePage 	= tmp_titlePage;
	if (move_rank+'' != 'undefined'){
		this.list[dst].rank	= tmp_rank;
	}
	this.list[dst].icon		= tmp_icon;
	this.list[dst].titlepage	= tmp_titlePage;
	this.draw();
}

function __WOC_setTitle(index){
	for (i=0; i<this.list.length;i++){
		this.list[i].titlePage = 0;
	}
	this.list[index].titlePage = 1;
	this.draw();
}

//[business-woman-small.jpg::tif::122:1234567890:Design 1::tif::66:1234567890:Design 1b::tif::67:1234567890:top_left.gif::tif::120]

function __WOC_ranking_files_insertdata(str){
	item_array 		= str.split(":1234567890:");
	for(i = 0 ; i < item_array.length ; i++){
		ok_add = true
		file_info	= item_array[i].split("::");
		for (z=0 ; z < this.list.length ; z++){
			if (this.list[z].identifier+'' == ''+file_info[2]){
				ok_add = false
				this.list[z].update = 1;
			}
		}
		if (ok_add){
			this.insertdata(file_info[0], file_info[2]);
		}
	}
	file_tidyup();
	this.draw();
}

function __WOC_insertdata(t){
	//alert("[title, "+title+"][icon, "+icon+"][identifier, "+identifier+"]");
	f = get_form();
	field_result = f.web_object_list.options[f.web_object_list.selectedIndex].value
	mylist = field_result.split(":==:");
	insert_index = this.list.length;
	this.list[insert_index] = new __WOC_RankItem();
	this.set(insert_index, "title", mylist[2]);
	this.set(insert_index, "rank", this.list.length);
	this.set(insert_index, "identifier",  mylist[1]);
	this.set(insert_index, "update",1);
	LIBERTAS_GENERAL_printToId("CacheScriptDiv", "");
	this.draw();
}
function __WOC_cancel(){
	LIBERTAS_GENERAL_printToId("CacheScriptDiv", "");
}
function __WOC_file_remover(index){
	this.list.splice(index,1);
	this.draw();
}

function __WOC_file_tidyup(){
	for (i=0; i < this.list.length; i++){
		if (this.list[i].update == 0) {
			file_remover(i);
		} else {
			this.list[i].update = 0;
		}
	}
	this.draw();
}

function __WOC_generate_hidden(){
	f = get_form();
	f.elements["webobject_list"].value='';
	f.elements["webobject_list_properties"].value='';
	for (i=0; i < this.list.length; i++){
		if (i!=0){
			f.elements["webobject_list_properties"].value	+= '~OO~';
			f.elements["webobject_list"].value 				+= ',';
		}
		f.elements["webobject_list"].value += this.list[i].identifier;
		
		f.elements["webobject_list_properties"].value += this.list[i].propertiesToString();
	}
//	alert(f.elements["webobject_list_properties"].value);

}
function __WOC_show(){
	cmd = 'ADD';

//	webobject_list.sort(sort_objects);
	var sz = '<table border="0" cellspacing="1" cellpadding="0" width="100%" bgcolor="#ebebeb">';
	sz += '<tr><td colspan="2" class="bt" ><strong>Choose Web Object</strong></td></tr>';
	if (this.filter=="__OPEN__"){
		sz += '<tr><td class="tablecell"><select name="web_c_type" id="web_ctype" style="width:200px;" onchange="javascript:cacheData.showcategory(this)">';
			sz+="<option value=''>Please select a category first</option>"
		for(var index =0 ; index<this.type.length;index++){
			if (this.type[index][1]!="__OPEN__"){
				sz+="<option value='"+this.type[index][1]+"'>"+this.type[index][0]+"</option>"
			}
		}
		sz+='</select></td></tr>';
		filteredOptions	 = "";
		d='display:none';
		sz += '<tr><td class="tablecell"><select name="web_object_list" id="web_object_list" style="width:400px;'+d+'" size="10">'+filteredOptions+'</select></td></tr>';
	} else {
	//	cacheData.getCache(filter);
		filteredOptions = cacheData.show(this.filter,0);
		if (filteredOptions!=''){
			d='';
		} else {
			d='display:none';
		}
		sz += '<tr><td class="tablecell"><select name="web_object_list" id="web_object_list" style="width:400px;'+d+'" size="10">'+filteredOptions+'</select></td></tr>';
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
	sz+='<input type="button" value="Ok" class="bt" id="add_web_object_ok_button" onclick="javascript:webobjectlist.insertdata(this)" style="'+d+'"> ';
	sz+='<input type="button" value="Cancel" class="bt"  onclick="javascript:webobjectlist.cancel_form()"> ';
	sz+='</td></tr>';
	sz+='</table>';
	LIBERTAS_GENERAL_printToId("CacheScriptDiv", sz)

}

function __WOC_property(index){
	var sz = '';
	sz += '<div class="bt" ><strong>Web Object Properties</strong></div>';
	sz += '<div class="tablecell">';
	sz += this.list[index].title;
	sz += '</div>';
	for(val in this.list[index].properties){
		sz += '<div class="tablecell">';
		sz += val+':<br/>';
		sz += '<input type="text" name="propertyText" id="'+val+'" value="'+this.list[index].properties[val]+'"/>';
		sz += '</div>';
	}
	sz+='<div class="tablecell" align="center">';
	sz+='<input type="button" value="Ok" class="bt" id="add_web_object_ok_button" onclick="javascript:webobjectlist.updateProperties('+index+')"> ';
	sz+='<input type="button" value="Cancel" class="bt"  onclick="javascript:webobjectlist.cancel_form()"> ';
	sz+='</div>';
	LIBERTAS_GENERAL_printToId("CacheScriptDiv", sz)
}

function __WOC_propertyUpdate(index){
	f = get_form();
	for (i=0;i< f.propertyText.length;i++){
		propertyId = f.propertyText[i].id;
		this.list[index].properties[propertyId] = f.propertyText[i].value
//		alert(this.list[index].properties[propertyId]);
	}
	LIBERTAS_GENERAL_printToId("CacheScriptDiv", "")
	this.draw();
}
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
function CacheDataObject(relates){
	//properties
	this.list					= new Array();
	this.relatesTo				= relates;
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

function wcupdatefilter(){
	f = get_form();
	webobjectlist.filter = f.wc_type.options[f.wc_type.selectedIndex].value;
}

function __WOCI_PropToString(){
	var sz = "";
	for (val in this.properties){
		if (sz!=""){
			sz +=",";
		}
		sz+=val+"~~"+this.properties[val];
	}
	return sz;
}

function __WOC_tidy(str){
	if(str.indexOf("[[js_quot]]")!=-1){
		while (str.indexOf("[[js_quot]]")!=-1){
			str.replace("[[js_quot]]",'"');
		}
	}
	return str;
}