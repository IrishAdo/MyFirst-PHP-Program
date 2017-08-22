/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Information Directory Feature List Javascript file.
-	$Date: 2004/12/23 17:20:12 $
-	$Revision: 1.4 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIB_ObjectList(list, outputDiv, selectorDiv){
	// Properties
	this.name				= "";	 			// variable name of this object  
	this.list				= list; 			// Directory list Identifier 
	this.category			= -1;
	this.rendered			= 0;
	this.cachedArray		= new Array();		// List of cached Information
	this.selectedArray		= new Array();		// List of selected infromation entries holds ID and Title
	this.outputDiv			= outputDiv;		// location to publish to
	this.selectorDiv		= selectorDiv;		// location to display selector
	// methods
	this.draw				= __OL_draw;				// draw the content to the screen
	this.add				= __OL_add;					// add an entry on load to the selectedArray property
	this.loadCache			= __OL_load_cache;			// load the cache with the list of entries available;
	this.retrieve			= __OL_retrieve;			// retrieve cache info
	this.remove				= __OL_remove;				// remove
	this.up					= __OL_up;					// up
	this.down				= __OL_down;				// down
	this.exec_info			= __OL_extract_information;	// Extract from cache
	this.get_info			= __OL_get_info;			// Get Cached Info
	this.updateCatSelector	= __OL_enableExtractList;	// Update the Selector extract button
	this.extractEntries		= __OL_extractEntries;		// retrieve a list of entries that are assigned to this category
	this.get_entries		= __OL_get_entries;
	this.activateAddButton	= __OL_activateAddButton;
	this.AddEntries			= __OL_addEntry;
} 

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_draw
-	usage	:	object.draw(void);
-	returns	:	true on success
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	Draws the outputlist to the screen to the display position this.outputDiv
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __OL_draw(){
	HTMLContent = "";
	if(this.rendered==0){
		HTMLContent = "<input type='button' id='addbtn' onclick='javascript:myobjectlist.retrieve();return false;' value='Add new entry to list' class='bt'/><br />";
	}
	try{
		if (this.outputDiv==""){
			ok=false
		} else {
			HTMLContent += "<div class='row'>";
//			HTMLContent += "	<div style='display:inline;width:75px;' class='bt'>ID</div>";
			HTMLContent += "	<div style='display:inline;width:300px;' class='bt'>Entry</div>";
			HTMLContent += "	<div style='display:inline;width:190px;' class='bt'>Options</div>";
			HTMLContent += "</div>";
			for (i=0;i<this.selectedArray.length;i++){
				HTMLContent += "<div class='row'>";
				//alert(this.selectedArray[i].id + " " + this.selectedArray[i].category + " " + this.selectedArray[i].title);
				HTMLContent += "<input type=hidden name='ManualEntryId[]' value='"+this.selectedArray[i].id+"'>";
				HTMLContent += "<input type=hidden name='ManualEntryCat[]' value='"+this.selectedArray[i].category+"'>";
//				HTMLContent += "<div style='display:inline;width:75px;'>"+this.selectedArray[i].id+"</div>";
				HTMLContent += "<div style='display:inline;width:300px;'>"+this.selectedArray[i].title+"</div>";
				HTMLContent += "<div style='display:inline;width:190px;'>";
				HTMLContent += "<input type='button' class='bt' style='width:60px;display:inline;text-align:center;' onclick='javascript:"+this.name+".remove("+i+");' value='Remove'>";
				HTMLContent += "<input type='button' class='bt' style='width:60px;display:inline;text-align:center;' onclick='javascript:"+this.name+".up("+i+");' value='Up'";
				if(i==0){
					HTMLContent += " disabled=true";
				}
				HTMLContent += ">";
				HTMLContent += "<input type='button' class='bt' style='width:60px;display:inline;text-align:center;' onclick='javascript:"+this.name+".down("+i+");' value='Down'";
				if(i == this.selectedArray.length - 1){
					HTMLContent += " disabled=true";
				}
				HTMLContent += ">";
				HTMLContent += "</div>";
				HTMLContent += "</div>";
			}
			myDiv = document.getElementById(this.outputDiv);
			myDiv.innerHTML = HTMLContent;
			ok=true;
		}
	} catch (e){
		ok = false;
	}
	
	return ok;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_retrieve
-	usage	:	object.retrieve(void);
-	returns	:	false (called from link);
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	Draws the selection form on the screen to the display position this.selectorDiv and extracts the category list
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __OL_retrieve(){
	HTMLContent = "";
	this.rendered=1;
	document.getElementById("addbtn").disabled=true;
	try{
		if (this.selectorDiv==""){
			ok=false;
		} else {
			catoptionlist	= "";
			entryoptionlist	= "";
			HTMLContent += "<div class='row'><div class='bt' style='width:250px'>Category</div></div>";
			// Category list
			HTMLContent += "<div class='row' >";
			HTMLContent += "<select name='categorylistselector' disabled='true' style='width:250px' onchange='"+this.name+".updateCatSelector();'><option>Retrieving List</option></select>";
			HTMLContent += "</div>";
			// extract button
			HTMLContent += "<div class='row'><div style='width:250px;text-align:right'><input disabled='true' id='entryExtractButton' type='button' class='bt' value='Extract this Category' onclick='"+this.name+".extractEntries();'></div></div>";
			// label
			HTMLContent += "<div class='row'><div class='bt' style='width:250px'>Entries</div></div>";
			// Entries in specific Category
			HTMLContent += "<div class='row'>";
			HTMLContent += "<select name='categorylistentries' disabled='true' id='categorylistentries' style='width:250px;height:150' multiple='true' onchange='"+this.name+".activateAddButton()'>"+entryoptionlist+"</select>";
			HTMLContent += "</div>";
			// Add to list
			HTMLContent += "<div class='row'><div style='width:250px;text-align:right'><input disabled='true' id='addSelectedButton' type='button' class='bt' value='Add Selected' onclick='"+this.name+".AddEntries()'></div></div>";
			myDiv = document.getElementById(this.selectorDiv);
			myDiv.innerHTML = HTMLContent;
			this.exec_info('category', "identifier="+this.category);

			ok=true;
		}
	} catch (e){
		ok = false;
	}
	return false;
}
 

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_add
-	usage	:	object.add(id [int], title [string], category [int]);
-	returns	:	boolean (true on successfull addition)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	Draws the selection form on the screen to the display position this.selectorDiv
-	Draws the outputlist to the screen to the display position this.outputDiv
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __OL_add(id, title,category){
	ok = false;
	try{
		ok=true;
		for(var i=0;i<this.selectedArray.length;i++){
			if(this.selectedArray[i]["id"]==id){ // id is unique number if we have it then we have this entry.
				ok = false;
			}
		}
		if(ok){
			this.selectedArray[this.selectedArray.length] = {"id":id,"title":title,"category":category};
		}
		
	} catch (e){
		ok = false;
	}
	return ok;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_add
-	usage	:	object.addEntry();
-	returns	:	boolean (true on successfull addition)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	Draws the selection form on the screen to the display position this.selectorDiv
-	Draws the outputlist to the screen to the display position this.outputDiv
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __OL_addEntry(){
	ok = false;
	try{
		var frm = get_form();
		var len = frm.categorylistentries.options.length;
		var cat = frm.categorylistselector.options[frm.categorylistselector.selectedIndex].value; // category id
		for (var i=0; i<len ; i++){
			if (frm.categorylistentries.options[i].selected){
				id = frm.categorylistentries.options[i].value;	 // Entry Id
				title = frm.categorylistentries.options[i].text; // Entry title
				success = this.add(id,title,cat);
			}
		}
		ok=true;
	} catch (e){
		ok = false;
	}
	if (ok){
		this.draw();
	}
	return ok;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_load_cache
-	usage	:	object.loadCache();
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	Caches the information directory content into javascript
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __OL_load_cache(){

}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_remove
-	usage	:	object.remove(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	removes entry from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __OL_remove(index){
	this.selectedArray.splice(index,1);
	this.draw();
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_up
-	usage	:	object.up(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	changes the rank order of selected entries from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __OL_up(index){
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -	store the top entry in a tmp location
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	tmp_id									= this.selectedArray[index-1].id;
	tmp_category							= this.selectedArray[index-1].category;
	tmp_title								= this.selectedArray[index-1].title;
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -	overwrite the top entry with the content
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	this.selectedArray[index-1].id			= this.selectedArray[index].id;
	this.selectedArray[index-1].category	= this.selectedArray[index].category;
	this.selectedArray[index-1].title		= this.selectedArray[index].title;
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -	file the content with the temp data
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	this.selectedArray[index].id			= tmp_id;
	this.selectedArray[index].category		= tmp_category;
	this.selectedArray[index].title			= tmp_title;
	
	this.draw();
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_down
-	usage	:	object.down(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	removes entry from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __OL_down(index){
//	alert(index+" "+this.selectedArray.length)
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -	store the top entry in a tmp location
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	tmp_id									= this.selectedArray[index].id;
	tmp_category							= this.selectedArray[index].category;
	tmp_title								= this.selectedArray[index].title;
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -	overwrite the top entry with the content
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	this.selectedArray[index].id			= this.selectedArray[index+1].id;
	this.selectedArray[index].category		= this.selectedArray[index+1].category;
	this.selectedArray[index].title			= this.selectedArray[index+1].title;
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -	file the content with the temp data
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	this.selectedArray[index+1].id			= tmp_id;
	this.selectedArray[index+1].category	= tmp_category;
	this.selectedArray[index+1].title		= tmp_title;
	
	this.draw();
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_get_info
-	usage	:	object.get_info(typeOfInfo [string]);
-	returns	:	nothing
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __OL_get_info(typeOfInfo){
//alert(typeOfInfo);
	if(typeOfInfo=='category'){
		if(cache_data.document.frmDoc.category_data.value==""){
			setTimeout(this.name+".get_info('"+typeOfInfo+"')",2000);
		} else {
			data 			= new String(cache_data.document.frmDoc.category_data.value).split("|1234567890|");
			var mylist = new Array();
			var frm = get_form();
			frm.categorylistselector.options.length=0;
			frm.categorylistselector.options[0] = new Option("Select a category to filter on ...",-1)
			for (var i =0; i<data.length;i++){
				mylist[i] = data[i].split("::");
			}
			for (var i =0; i<mylist.length;i++){
				frm.categorylistselector.options[i+1] = new Option(LIBERTAS_GENERAL_unjtidy(mylist[i][2]), mylist[i][1]);
			}
			frm.categorylistselector.disabled=false;
		}
	}
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_get_info
-	usage	:	object.get_info(typeOfInfo [string]);
-	returns	:	nothing
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __OL_extract_information(szType, parameter){
	/*
		check to see if the cache is available for information yet? 
	*/
	if (this.category!=-1){
		if (cache_data.document.readyState != 'complete'){
			setTimeout(this.name+".exec_info('"+szType+"', '"+parameter+"');",1000);
			return;
		} else {
			if (szType=='category' && cache_data.frmDoc.category_data.value==''){
				cache_data.frmDoc.category_data.value="";
				cache_data.__extract_info('category',parameter);
				setTimeout(this.name+".get_info('"+szType+"');",1000);
			}
		}
	} else {
		alert("No category available");
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_get_info
-	usage	:	object.get_info(typeOfInfo [string]);
-	returns	:	nothing
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __OL_extractEntries(){
	/*
		get the category with which to filter with
	*/
	var frm = get_form();
	var selectedCategory = frm.categorylistselector.options[frm.categorylistselector.options.selectedIndex].value;
	if (cache_data.document.readyState != 'complete'){
		setTimeout(this.name+".extractEntries();",1000);
		return;
	} else {
//		if (cache_data.frmDoc.infodir_data.value==''){
			frm.addSelectedButton.disabled=true;
			frm.categorylistentries.disabled=true;
			cache_data.frmDoc.infodir_data.value="";
			cache_data.__extract_info('infodir',"cat="+selectedCategory+"&list="+this.list+"&catlist="+this.category);
			setTimeout(this.name+".get_entries();",1000);
//		}
	}
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_get_entries
-	usage	:	object.get_entries();
-	returns	:	nothing
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __OL_get_entries(){
	if(cache_data.document.frmDoc.infodir_data.value==""){
		setTimeout(this.name+".get_entries();",2000);
	} else {
		data 			= new String(cache_data.document.frmDoc.infodir_data.value).split("|1234567890|");
		var mylist = new Array();
		var frm = get_form();
		frm.categorylistentries.options.length=0;
		for (var i =0; i<data.length;i++){
			mylist = data[i].split("::");
			frm.categorylistentries.options[i] = new Option(LIBERTAS_GENERAL_unjtidy(mylist[1]), mylist[0]);
		}
		frm.categorylistentries.disabled=false;
	}
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_enableExtractList
-	usage	:	object.updateCatSelector();
-	returns	:	nothing
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	Enables and disables the extract entries button
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __OL_enableExtractList(){
	var frm = get_form();
	si = frm.categorylistselector.options.selectedIndex;
	if (si==0){
		frm.entryExtractButton.disabled=true;
	} else {
		frm.entryExtractButton.disabled=false;
	}
	frm.categorylistentries.options.length=0;
	frm.categorylistentries.disabled=true;
}


/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__OL_activateAddButton
-	usage	:	object.activateAddButton();
-	returns	:	nothing
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	Enables and disables the Add entries button
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __OL_activateAddButton(){
	var frm = get_form();
	var selected=0;
	var len = frm.categorylistentries.options.length
	for (var i=0; i<len ; i++){
		if (frm.categorylistentries.options[i].selected){
			selected++;
		}
	}
	if (selected==0){
		frm.addSelectedButton.disabled=true;
	} else {
		frm.addSelectedButton.disabled=false;
	}
}











/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-
-								C L A S S   -   C a c h e D a t a O b j e c t
-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- This function is designed to allow the system to cache the list of directory entries into an array that will allow 
- us to decide if the system needs to request the list of entries from the database.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- FN:: CacheDataObject()
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




