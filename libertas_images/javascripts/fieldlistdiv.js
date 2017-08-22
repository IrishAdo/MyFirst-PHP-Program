/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- INFORMATION DIRECTORY MANAGER - FORM FIELD MANAGER
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Global Variables
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var optionListMode 			= "import"; // on selection of the toggle button which screen should next be 
var w						= 60;							   // displayed
var editting_option_index	= -1;
var content_display 		= new Array();
var summary_display 		= new Array();
var advancedsearch 			= new Array();
var searchlabelposition		= true; // true above false side by side
var mapping 				= new Array(); // metadata mappings
	/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Functions
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function draw_fieldlistdiv(){
	// print the bloody thing
	setCategories();
//	LIBERTAS_GENERAL_printToId("fieldlistdiv", sz);
	RankScreen(); 
	EditScreen(0);
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- sort the field options into groups
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function _fieldrank(currentRank, dir){
	/*
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// 0,	'key',
	// 1,	'rank',
	// 2,	'selected',
	// 3,	'label',
	// 4,	'description'
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	if (dir<0){
		start = currentRank + dir;
		finish= currentRank
	} else {
		start = currentRank;
		finish= currentRank + dir
	}
	tmp = info_fieldlist[start][1];
	info_fieldlist[start][1] = info_fieldlist[finish][1];
	info_fieldlist[finish][1] = tmp;
	viewmetadatamaping();
	refresh_mapping_indexes();
	update_modified("modified_entry_screen");
	RankScreen(start, finish); 
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- sort the field by rank
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function sortByRank(a,b){
	/*
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// 0,	'key',
	// 1,	'rank',
	// 2,	'selected',
	// 3,	'label',
	// 4,	'description'
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	return ((a[1]*1)-(b[1]*1));
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- sort the field options into groups
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __sortByType(a,b){
	/*
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// info_list_of_options
	// index 4 holds the group type info
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	if (a[4] < b[4])
		return (-1);
	if (a[4] == b[4])
		return (0);
	if (a[4] > b[4])
		return (1);
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- remove field option
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function directoryManagerRemove(pos,t){
	/*
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// 0,	'key',
	// 1,	'rank',
	// 2,	'selected',
	// 3,	'label',
	// 4,	'description'
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	if( confirm("Do you want to remove this field all information currently stored in the Information Directory entries will be lost") ){
		var len 	= info_fieldlist.length;
		var rowlist = document.getElementById("rows");
		for (var i = 0; i < rowlist.childNodes.length; i++){
			if (rowlist.childNodes(i).id == "row"+pos){
				use_pos = i;
			}
		}
		__fn_removeElement(use_pos);
		info_fieldlist.splice(use_pos,1);
		
		if (rowlist.childNodes.length>=use_pos){
			if (rowlist.childNodes.length >= use_pos+1){ 
				for (var i = (use_pos+1); i < rowlist.childNodes.length; i++){
					p = rowlist.childNodes(i).id;
					rowlist.childNodes(i).id = "row"+(i-1);
					rowlist.childNodes(i).childNodes(1).id = "optionButtons"+(i-1);
					rowlist.childNodes(i).childNodes(1).innerHTML = getRank(i-1);
					special = document.getElementById("special_"+i).value;
					if(special+""=="undefined"){
						special=0;
					}
					sz  = '';
					sz += '<input type="hidden" name="type_' + (i-1) + '" id="type_' + (i-1) + '" value="' + document.getElementById("type_"+i).value + '" />';
					sz += '<input type="hidden" name="special_' + (i-1) + '" id="special_' + (i-1) + '" value="' + special + '" />';
					sz += '<input type="hidden" name="rank_' + (i-1) + '" id="rank_' + (i-1) + '" value="' + (i-1) + '" />';
					sz += '<input type="hidden" name="hfield_' + (i-1) + '" id="hfield_' + (i-1) + '" value="' + document.getElementById("hfield_"+i).value + '" />';
					sz += '<input type="hidden" name="options_' + (i-1) + '" id="options_' + (i-1) + '" value="' + document.getElementById("options_"+i).value + '" />';
					sz += '<input type="hidden" name="visiblefields_'+(i-1)+'" id="visiblefields_'+(i-1)+'" value="1" />';
					sz += '<input type="hidden" name="vfield_'+(i-1)+'" id="vfield_'+(i-1)+'" value="' + document.getElementById("vfield_"+i).value + '" />';
					document.getElementById("fieldlist"+i).outerHTML="<span id='fieldlist"+(i-1)+"'>"+sz+"</span>";
				}
			}
			rowlist.childNodes(use_pos).removeNode(true);
		}
		update_modified("modified_entry_screen");
		EditScreen(0);
	}
}

function storefield(pos,t){
	/*
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// 0,	'key',
	// 1,	'rank',
	// 2,	'selected',
	// 3,	'label',
	// 4,	'description'
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	info_fieldlist[pos][3] = t.value;
	update_modified("modified_entry_screen");
}

/*
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// Field Options
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function info_show_field_options(){
	sz = "";
	currentScreen = 0;
	DirectoryFormAddEntry(currentScreen);
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Rank Screen
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function RankScreen(s,f){

	/*
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// 0,	'key',
	// 1,	'rank',
	// 2,	'selected',
	// 3,	'label',
	// 4,	'description'
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	info_fieldlist.sort(sortByRank);

	if (s+""!="undefined" && f+""!="undefined"){
		for(i=s;i<=f;i++){
			LIBERTAS_GENERAL_printToId("row"+i, rankRow(i));
		}
//		LIBERTAS_GENERAL_printToId("row"+f, rankRow(f));
	} else {
		sz	  = '<div id="table">';
		sz	 += '	<div id="thead">';
		sz	 += '		<div class="headercheck"></div>';
		sz	 += '		<div class="headerlabel">Label</div>';
		sz	 += '		<div class="headerrank2">Options</div>';
		sz	 += '	</div><div id="rows">';
		c 	  = "alt";

		for(var position = 0; position < info_fieldlist.length; position ++){
			sz 	 += '	<div id="row'+position+'" class="tablecell'+c+'">';
			sz 	 += 	rankRow(position);
			sz 	 += '	</div>';
			if (c == ''){
				c = 'alt';
			} else {
				c = '';
			}
		}
		sz 		 += '</div></div>';
		document.all['max_number_of_fields'].value = info_fieldlist.length;
		LIBERTAS_GENERAL_printToId("fieldRank", sz);
	}
}	

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Edit Screen
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function showCategoryFields(t){
	if (t.selectedIndex>0){
		default_entry = t.options[t.selectedIndex].value;
		document.all.info_fieldType.style.visibility='visible';
		document.all.info_fieldType.style.display='';
		document.all.addbtn.style.visibility='visible';
		document.all.addbtn.style.display='';
		document.all.info_fieldType.options.length = 0;
		for (index=0;index < info_list_of_options.length; index++){
			show=0;
			if (info_list_of_options[index][4]==default_entry && default_entry!="NA"){
				show=1;
				if (info_list_of_options[index][2]!="open"){
					for(z =0; z< info_fieldlist.length ; z++){
						if (info_list_of_options[index][0] == info_fieldlist[z][0]){
							show=0;
						}
					}
				}
			}
			if(show){
				document.all.info_fieldType.options[document.all.info_fieldType.options.length] = new Option(
					LIBERTAS_GENERAL_unjtidy(info_list_of_options[index][1]), 
					LIBERTAS_GENERAL_unjtidy(info_list_of_options[index][0]+"::"+info_list_of_options[index][3])
				);
			}
		}
	} else {
		document.all.info_fieldType.style.visibility='hidden';
		document.all.info_fieldType.style.display='none';
		document.all.addbtn.style.visibility='hidden';
		document.all.addbtn.style.display='none';
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Edit Screen
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function EditScreen(page, id , t){
	var sz				= "";
	var show_options	= -1;
	var cat 			= "";
	var default_entry	= 0;
	infosumlabel	= 2;
	infoconlabel	= 2;
	if (page==0){
		sz += "<h1>Field Definition</h1>";
		sz += "<p>Choose the type of field that you wish to add</p>";
		sz += "<p>";
		sz += "<select style='width:300px;' name='info_fieldType' id='info_fieldType'>";
		for (index=0;index < info_list_of_options.length; index++){
			show=0;
			if ((info_list_of_options[index][4]=="Ecommerce Fields" && document.all['info_shop_enabled'].selectedIndex==1) || (info_list_of_options[index][4]!="Ecommerce Fields" && info_list_of_options[index][4]!="Formatting List")){
				show=1;
				if (info_list_of_options[index][2]=="defined"){
					for(z = 0; z< info_fieldlist.length ; z++){
						if (info_list_of_options[index][0] == info_fieldlist[z][0]){
							show=0;
						}
					}
				}
			}
			if(show){
				sz +=  "<option value='"+LIBERTAS_GENERAL_unjtidy(info_list_of_options[index][0])+"::"+info_list_of_options[index][3]+"'>"+LIBERTAS_GENERAL_unjtidy(info_list_of_options[index][1])+"</option>";
				
			}
		}
		sz += "</select>";
		sz += '<input name="addbtn" id="addbtn" type="button"  value="Add new field" onclick="javascript:EditScreen(1,\'\', this);" class="bt"></p>';
		LIBERTAS_GENERAL_printToId("fieldForm",sz);
	} else if (page==1){
		if (id+""== "" || id+""=="undefined"){
			info_field_mapping	= "";
			sz += "<h1>Define the label of the new entry</h1>";
			e = document.all['info_fieldType'];
			values = e.options[e.selectedIndex].value.split("::");
			for (index=0;index < info_list_of_options.length; index++){
				if(values[0] == info_list_of_options[index][0]){
					info_field_mapping	= info_list_of_options[index][7];
					cat 				= info_list_of_options[index][4];
				}
			}
			vtype = values[1];
			vname = values[0];
			if(cat!="Extra Fields List"){
				infoFieldLabel = e.options[e.selectedIndex].text;
			} else {
				infoFieldLabel = "Please define your label here";
			}
			infoFieldSearch			= 0;
			vfilter					= 0;
			infodata				= Array();
			infodup					= "";
			info_remove_image_ondel	= "";		
			info_add_to_title		= 0;					
			info_add_url_field		= 0;					
			special					= 0;
		} else {
			sz += "";
			if (id >= info_fieldlist.length){
			} else {
				vname		 			= info_fieldlist[id][0];
				infoFieldLabel			= info_fieldlist[id][3];
				vtype		 			= info_fieldlist[id][5];
				cat 					= info_fieldlist[id][7];
				infoFieldSearch			= info_fieldlist[id][8];
				infodata 				= info_fieldlist[id][6];
				infodup					= info_fieldlist[id][9];
				vfilter 				= info_fieldlist[id][10];
				infosumlabel			= info_fieldlist[id][11];
				infoconlabel			= info_fieldlist[id][12];
				info_field_mapping		= info_fieldlist[id][13];
				special					= info_fieldlist[id][14];
				info_remove_image_ondel = info_fieldlist[id][9];
				info_add_to_title		= info_fieldlist[id][15];
				info_add_url_field		= info_fieldlist[id][16];
			}
		}

		if (cat=="Formatting List"){
			page=2;
			var add_index = false;
			var p_index = 0;
			var found=false;
			c=0;
			if (id==""){
				for (var index=0; index < info_list_of_options.length; index++){
					if (info_list_of_options[index][0] == vname){
						infoFieldLabel = info_list_of_options[index][1];
						for(position = 0; position < info_fieldlist.length; position ++){
							if ((info_list_of_options[index][0]==vname && vname==info_fieldlist[position][0]) || (info_list_of_options[index][0].substr(info_fieldlist[position][0].length)==vname && vname==info_fieldlist[position][0])){
								infoFieldLabel = info_fieldlist[position][3];
							}
						}
					}
				}		
				for(position = 0; position < info_list_of_options.length; position ++){
					if (!found){
						if	(
								(info_list_of_options[position][0]==vname) 
									|| 
								(
									(vname.indexOf(info_list_of_options[position][0]) != -1)
								)
							){
								fieldtype = info_list_of_options[position][0];
								if (fieldtype == vname){
									add_index = true;
									/*
									// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
									// fields indexing
									// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
									// 0,	'key',
									// 1,	'rank',
									// 2,	'selected', ignore backwards compatabliity
									// 3,	'label',
									// 4,	'description',
									// 5,	'type',
									// 6,	'options',
									// 7,	'category'
									// 8,	'available on search form'
									// 9, 'duplicate'
									// 10, filterable
									// 11,	info summary label position
									// 12,	info content label position
									// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
									*/
									myinfo="0";
									p_index=0;
									for (var index=0; index < info_fieldlist.length; index++){
										if (info_fieldlist[index][0].indexOf(fieldtype)!=-1){
											myinfo = parseInt(info_fieldlist[index][0].substr(fieldtype.length)) * 1;
											if (myinfo > p_index){
												p_index = myinfo * 1;
											}
											found=true;
											c++;
										}
									}
								} else {
									// editting should never be here
								}
						}
					}
				}
			} else {
				infoFieldLabel = info_list_of_options[id][1];
			}
			/*
			// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			// if adding new entry then add index
			// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
				if (add_index){
					p_index++;
					if (id+""==""){
						vname = vname + p_index;
					} else {
					}
				}

			sz+="<input type='hidden' name=info_field_type value='"+vtype+"'/>";
			sz+="<input type='hidden' name=info_field_name value='"+vname+"'/>["+vname+"]";
			sz+="<input type='hidden' name=info_cat value='"+cat+"'/>";
			sz+="<input type='hidden' name=entry_identifier value='"+id+"'/>";
			sz+="<input type='hidden' name=special value='0'/>";
			sz+="<input type='hidden' name='infoFieldDefinedLabel' id='infoFieldDefinedLabel' value='"+infoFieldLabel+"' />";
			LIBERTAS_GENERAL_printToId("fieldForm",sz);
		} else {
			/*
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            - define entry format add new element screen 2 (before add to list)
			- just selected type 
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            */
			var add_index = false;
			var p_index = 0;
			if (cat == "Extra Fields List"){
				/*
                -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
                - just added a extra entry (not a defined one but a reusable type) then get the index for it
                -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
                */
				var found=false;
				c=0;
				if (id+""==""){
					for(position = 0; position < info_list_of_options.length; position ++){
						if (!found){
							if	(
									(info_list_of_options[position][0]==vname) 
										|| 
									(
										(vname.indexOf(info_list_of_options[position][0]) != -1)
									)
								){
									fieldtype = info_list_of_options[position][0];
									if (fieldtype == vname){
										add_index = true;
										/*
										// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
										// fields indexing
										// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
										// 0,	'key',
										// 1,	'rank',
										// 2,	'selected', ignore backwards compatabliity
										// 3,	'label',
										// 4,	'description',
										// 5,	'type',
										// 6,	'options',
										// 7,	'category'
										// 8,	'available on search form'
										// 9, 	'duplicate'
										// 10,  filterable
										// 11,	info summary label position
										// 12,	info content label position
										// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
										*/
										myinfo=""
										p_index=0;
										for (var index=0; index < info_fieldlist.length; index++){
											if (info_fieldlist[index][0].indexOf(fieldtype)!=-1){
												myinfo = parseInt(info_fieldlist[index][0].substr(fieldtype.length)) *1;
												if (myinfo > p_index){
													p_index = myinfo * 1;
												}
												found=true
												c++
											}
										}
									} else {
										// editting should never be here
									}
							}
						}
					}
				}
				/*
				// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				// if adding new entry then add index
				// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if (add_index){
					infoconlabel=1;
					infosumlabel=1;
					p_index++;
					if (id+""==""){
						vname =vname+""+p_index;
					} else {
					}

				}
			}
			sz += "<input type='hidden' name=info_field_type value='"+vtype+"'/>";
			sz += "<input type='hidden' name=info_cat value='"+cat+"'/>";
			sz += "<input type='hidden' name=info_field_mapping value='"+info_field_mapping+"'/>";
			sz += "<input type='hidden' name=info_field_name value='"+vname+"'/>";
			sz += "<input type='hidden' name=entry_identifier value='"+id+"'/>";
			var Type_of_field="";
			for(var i in info_list_of_options){
				if(vname.indexOf(info_list_of_options[i][0])!=-1){
					Type_of_field	= LIBERTAS_GENERAL_unjtidy(info_list_of_options[i][1]);
					description		= LIBERTAS_GENERAL_unjtidy(info_list_of_options[i][5]);
//					searchable		= info_list_of_options[i][6];

				}
			}
			sz += "<strong>Type of Field</strong> : "+Type_of_field+"<br>";
			sz += "<p>Label<br><input type='text' value='"+infoFieldLabel+"' size='40' maxlength='255' name='infoFieldDefinedLabel' id='infoFieldDefinedLabel'/></p>";
			sz += "<p style='display:none'><strong>Choose Label display option</strong><br>"
			/**
            *	summary label display option
       	    */
			sz += "On the summary screen <select name='info_field_summary_label' id ='info_field_summary_label'>";
			sz +=	"<option value='0'>Do not display the label</option>";
			sz +=	"<option value='1'";
			if (infosumlabel=='1'){
				sz+=" selected='true'";
			}
			sz +=	">Display to the left of the content</option>";
			sz +=	"<option value='2'";
			if (infosumlabel=='2'){
				sz+=" selected='true'";
			}
			sz +=	">Display above the content</option>";
			sz += "</select><br>";
			/**
            *	content label display option
       	    */
			sz += "On the content screen <select name='info_field_content_label' id ='info_field_content_label'>";
			sz +=	"<option value='0'>Do not display the label</option>";
			sz +=	"<option value='1'";
			if (infoconlabel=='1'){
				sz+=" selected='true'";
			}
			sz +=	">Display to the left of the content</option>";
			sz +=	"<option value='2'";
			if (infoconlabel=='2'){
				sz+=" selected='true'";
			}
				sz +=	">Display above the content</option>";
			sz += "</select>";
			sz+="</p>";
			if(vtype=='memo' || vtype=='smallmemo'){
				if(special=="undefined"){
					special=0;
				}
				sz+="Enable editor <select name=special>";
					sz+="<option value='0'";
					if(special==0){
						sz+=" selected='true'";
					}
					sz+=">No</option>";
					sz+="<option value='1'";
					if(special==1){
						sz+=" selected='true'";
					}
					sz+=">Yes</option>";
				sz+="</select>";
				sz+="<br/>";
			} else {
				sz+="<input type='hidden' name=special value='0'/>";
			}
			if(vtype=="image"){
				sz += "<br />Remove image when entry deleted<select name='info_field_remove_image_ondel' id ='info_field_remove_image_ondel'>";
				sz +=	"<option value='No'";
				if (info_remove_image_ondel=='No'){
					sz+=" selected='true'";
				}
				sz +=	">No</option>";
				sz +=	"<option value='Yes'";
				if (info_remove_image_ondel=='Yes'){
					sz+=" selected='true'";
				}
				sz +=	">Yes</option>";
				sz += "</select>";		
			}			
			else {
				sz += "Duplicate check<br/><select name='info_field_duplicate' id ='info_field_duplicate'>";
				sz +=	"<option value=''>Do not check</option>";
				sz +=	"<option value='exact'";
				if (infodup=='exact'){
					sz+=" selected='true'";
				}
				sz +=	">Exact match</option>";
				sz +=	"<option value='contains'";
				if (infodup=='contains'){
					sz+=" selected='true'";
				}
				sz +=	">Contains</option>";
				sz +=	"<option value='startswith'";
				if (infodup=='startswith'){
					sz+=" selected='true'";
				}
				sz +=	">Starts with</option>";
				sz += "</select>";
			}
			if (vtype == 'URL'){
				sz += "<p>Add additional field for link <input type='checkbox' name='info_field_urlfield' value='1'";
				if (info_add_url_field == 1){
					sz+=" checked='checked'";
				}				
				sz += " /></p>";
				
			}
			if ((vtype == 'text' || vtype =='select' || vtype =='list') && vname != 'ie_title'){
				sz += "<p>Merge with title <input type='checkbox' name='info_field_addtotitle' value='1'";
				if (info_add_to_title==1){
					sz+=" checked='checked'";
				}				
				sz += " /></p>";
			}
			else {
				sz += "<input type='hidden' name=info_field_addtotitle value='0'/>";				
			}
			if (vtype!='select' && vtype!='radio' && vtype!='check' && vtype!='list'){
				sz += "<input type='hidden' name=info_field_filter value='0'/>";
			} else {
//				alert("vfilter :: ["+vfilter+"]");
				sz += "<br/>Is this field filterable <select name='info_field_filter'><option value='0'";
				if (vfilter==0){
					sz+=" selected='true' ";
				}
				sz +=	">No</option><option value='1'";
				if (vfilter==1){
					sz+=" selected='true'";
				}
				sz +=	">Yes</option></select>";
			}
			if(vtype=="boolean"){
				intrue	= "Yes";
				infalse = "No";
				if(infodata+""!=""){
					intrue = infodata[0];
					infalse = infodata[1];
				}
				sz += "<p>Label for True <input type='text' name='boolean_true' value='"+intrue+"'/></p>";
				sz += "<p>Label for False <input type='text' name='boolean_false' value='"+infalse+"'/></p>";
			}
			if(vtype=="links"){
				sz += "<p><label for='link_counter'>How many links should be definable</label> <select name='storecounter' id='link_counter'>";
				for(var i =1; i<=10;i++){
					sz += "<option value='"+i+"'";
					if (infodata[0]==i){
						sz+=" selected='true'";
					}
					sz += ">"+i+"</option>";
				}
				sz += "</select></p>";
			}
			show_options = -1;
			for (index=0;index < info_list_of_options.length; index++){
				if(vtype=="radio" || vtype=="select" || vtype=="check" || vtype=="list"){
					show_options = index;
				}
			}
			if (show_options>-1){
				sz += "<div id='optionManager'></div>";
			}
			sz+="<p id='backnextbtns' align='right'><input type='button' value='&#171; Back ' class='bt' onclick='javascript:EditScreen(0);'/><input type='button' value=' Next &#187;' class='bt' onclick='javascript:EditScreen(2);'/></p>";
			LIBERTAS_GENERAL_printToId("fieldForm",sz);
			if (show_options > -1){
				optionManagerDisplay("list", id, show_options);
			}
		}
	} 
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -  A D D   -   I t e m   t o   l i s t 
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	if (page==2){
	//	sz += "field type ["+ document.all['info_field_type'].value +"]\n"
	//	sz += "entry_identifier ["+ document.all['entry_identifier'].value +"]\n"
	//	sz += "label ["+ document.all['infoFieldDefinedLabel'].value +"]\n"
		var id = document.all['entry_identifier'].value;
		/*
		// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		// fields indexing
		// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		// 0,	'key',
		// 1,	'rank',
		// 2,	'selected', ignore backwards compatabliity
		// 3,	'label',
		// 4,	'description',
		// 5,	'type',
		// 6,	'options',
		// 7,	'category'
		// 8,	'available on search form'
		// 9,	'duplicate check'
		// 10,	'filterable'
		// 11,	info summary label position
		// 12,	info content label position
		// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
//		alert(document.all['info_field_type'].value+" ["+id+"]");
		if (id!=""){
			
			info_fieldlist[id][8] = 0;
/*			makeUnAvailableToSearch(id);
			if (document.all['infoFieldDefinedSearch']){
				if (document.all['infoFieldDefinedSearch'].checked){
					info_fieldlist[id][8] = 1;
					makeAvailableToSearch(id);
				}
			}
*/
			info_fieldlist[id][7] = document.all['info_cat'].value;
			info_fieldlist[id][3] = document.all['infoFieldDefinedLabel'].value;
			info_fieldlist[id][5] = document.all['info_field_type'].value;
			info_fieldlist[id][0] = document.all['info_field_name'].value;
			info_fieldlist[id][15] = 0;			
			info_fieldlist[id][16] = 0;			
			if (document.all['info_field_name'].value=='ie_title'){
				info_fieldlist[id][9] = document.all['info_field_duplicate'].value;
			}	
			else if (document.all['info_field_type'].value=='image'){
				info_fieldlist[id][9] = document.all['info_field_remove_image_ondel'].options[document.all['info_field_remove_image_ondel'].selectedIndex].value;
			} else {
				info_fieldlist[id][9] = document.all['info_field_duplicate'].options[document.all['info_field_duplicate'].selectedIndex].value;
			}
			if (
				document.all['info_field_type'].value=='select' || 
				document.all['info_field_type'].value=='radio' || 
				document.all['info_field_type'].value=='check' || 
				document.all['info_field_type'].value=='list'
				){
				info_fieldlist[id][10] = document.all['info_field_filter'].options[document.all['info_field_filter'].selectedIndex].value;
			} else {
				info_fieldlist[id][10] = document.all['info_field_filter'].value;
			}
			info_fieldlist[id][11] = document.all['info_field_summary_label'].options[document.all['info_field_summary_label'].selectedIndex].value;
			info_fieldlist[id][12] = document.all['info_field_content_label'].options[document.all['info_field_content_label'].selectedIndex].value;
			info_fieldlist[id][13] = document.all['info_field_mapping'].value;
			if(document.all['info_field_type'].value=="text" || document.all['info_field_type'].value=="select" || document.all['info_field_type'].value=="list"){		
				if(document.all['info_field_addtotitle'].checked)
					info_fieldlist[id][15] = 1;
			}	
			if(document.all['info_field_type'].value=="URL"){		
				if(document.all['info_field_urlfield'].checked)
					info_fieldlist[id][16] = 1;
			}			
			try{
				info_fieldlist[id][14] = document.all['special'].options[document.all['special'].selectedIndex].value;
			} catch(e){
				info_fieldlist[id][14] = document.all['special'].value;	
			}
		} else {
			var len = info_fieldlist.length;
			m=0;
			// get rank as new id
			/*
			for (var i=0 ; i<len ; i++){
				v = (info_fieldlist[i][1]*1);
				if (m < v){
					m = v;
				}
			}*/
			rankpos=1;
			for (var i=0 ; i<len ; i++){
				for (var z=0 ; z<info_list_of_options.length ; z++){ 
					if (info_list_of_options[z][0]== info_fieldlist[i][0]){
						 // ignore
						  break;
					} else {
						// check 
						if (info_fieldlist[i][0].substring(0,info_list_of_options[z][0].length) == info_list_of_options[z][0]){
							v = info_fieldlist[i][0].substring(info_list_of_options[z][0].length);
							if (m<v){
								m=v;
							}
						}
					}
				}
				/**
                * get the biggest rank
                */
				if (info_fieldlist[i][1]>rankpos){
					rankpos = info_fieldlist[i][1];
				}
			}
			
			id = info_fieldlist.length;
			info_fieldlist[id] = new Array(
				document.all['info_field_name'].value+"",  (rankpos*1)+1, 1, document.all['infoFieldDefinedLabel'].value, '', document.all['info_field_type'].value, Array(),document.all['info_cat'].value
			);
			
			info_fieldlist[id][15] = 0;			
			info_fieldlist[id][16] = 0;			
			if (document.all['info_field_type'].value=='colsplitter' || document.all['info_field_type'].value=='rowsplitter'){
				info_fieldlist[id][9] = "";
			} else if (document.all['info_field_type'].value=='ie_title'){
				info_fieldlist[id][9] = document.all['info_field_duplicate'].value;			
			}else if (document.all['info_field_type'].value =='image'){
				info_fieldlist[id][9] = document.all['info_field_remove_image_ondel'].options[document.all['info_field_remove_image_ondel'].selectedIndex].value;
			} else {
				info_fieldlist[id][9] = document.all['info_field_duplicate'].options[document.all['info_field_duplicate'].selectedIndex].value;
			}
			if (
				document.all['info_field_type'].value=='select' || 
				document.all['info_field_type'].value=='radio' || 
				document.all['info_field_type'].value=='check' || 
				document.all['info_field_type'].value=='list'
				){
				info_fieldlist[id][10] = document.all['info_field_filter'].options[document.all['info_field_filter'].selectedIndex].value;
			} else {
				info_fieldlist[id][10] = document.all['info_field_filter'].value;
			}
			info_fieldlist[id][11] = document.all['info_field_summary_label'].options[document.all['info_field_summary_label'].selectedIndex].value;
			info_fieldlist[id][12] = document.all['info_field_content_label'].options[document.all['info_field_content_label'].selectedIndex].value;
			info_fieldlist[id][13] = document.all['info_field_mapping'].value;
			try{
				info_fieldlist[id][14] = document.all['special'].options[document.all['special'].selectedIndex].value;
			} catch(e){
				info_fieldlist[id][14] = document.all['special'].value;	
			}
			if(document.all['info_field_type'].value=="text" || document.all['info_field_type'].value=="select" || document.all['info_field_type'].value=="list"){		
				if(document.all['info_field_addtotitle'].checked)
					info_fieldlist[id][15] = 1;
			}	
			if(document.all['info_field_type'].value=="URL"){		
				if(document.all['info_field_urlfield'].checked)
					info_fieldlist[id][16] = 1;
			}	
			
			
		}
		if(document.all['info_field_type'].value=="radio" || document.all['info_field_type'].value=="select" || document.all['info_field_type'].value=="check" || document.all['info_field_type'].value=="list"){
			info_fieldlist[id][6] = new Array();
			for (var index=0; index < document.all['optionManagerList'].options.length; index++){
				info_fieldlist[id][6][info_fieldlist[id][6].length] = document.all['optionManagerList'].options[index].value
			}
		}
		if(document.all['info_field_type'].value == "boolean"){
			info_fieldlist[id][6] = new Array();
			info_fieldlist[id][6][info_fieldlist[id][6].length] = document.all['boolean_true'].value;
			info_fieldlist[id][6][info_fieldlist[id][6].length] = document.all['boolean_false'].value;
		}
		if(document.all['info_field_type'].value=="links"){
			info_fieldlist[id][6] = new Array();
			info_fieldlist[id][6][0] = document.all['storecounter'].options[document.all['storecounter'].selectedIndex].value
		}

		update_modified("modified_entry_screen");
		update_fields();
//just added
//alert("808");
		viewmetadatamaping();
		EditScreen(0);
		
/*
		show_output("summary_display");
		show_output("content_display");
		show_output("advancedsearch");
*/

		RankScreen();
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function optionManagerDisplay(typeOfDisplay, show_options_index){
	var sz			= "<p>Field Option List Manager<br/>";
	var txt 		= "";
	var optionlist 	= "";
	if (show_options_index+""!="undefined" && show_options_index+""!=""){
		if (info_fieldlist[show_options_index][6]+'' !="undefined"){
			for (var index=0; index < info_fieldlist[show_options_index][6].length; index++){
				optionlist +="<option value='"+info_fieldlist[show_options_index][6][index]+"'>"+info_fieldlist[show_options_index][6][index]+"</option>"
				if (txt!=""){
					txt +="\r\n";
				}
				txt += info_fieldlist[show_options_index][6][index];
			}
		}
	} else{
		show_options_index=="";
	}
	if (typeOfDisplay=="list"){
		action 		= "import";
		listshow	= "";
		importshow	= "visibility:hidden;display:none;";
	} else {
		importshow	= "";
		listshow	= "visibility:hidden;display:none;";
		action 		= "list";
	}
	sz += "<div style='"+listshow+"vertical-align:top;' id='listoptionid'>";
	sz += "<div style='display:inline'><select size='10' name='optionManagerList' style='width:300px;height:200px;' id='optionManagerList'>" + optionlist + "</select></div>";
	sz += "<div style='display:inline;vertical-align:top;width:62px'>";
	sz += "<input type='button' value='Up' style='width:60px' class='bt' onclick='javascript:optionManagerRank(\"up\",\""+show_options_index+"\");'/>";
	sz += "<input type='button' value='Down' style='width:60px' class='bt' onclick='javascript:optionManagerRank(\"down\",\""+show_options_index+"\");'/>";
	sz += "<input type='button' value='Edit' style='width:60px' class='bt' onclick='javascript:optionManagerEdit(\""+show_options_index+"\");'/>";
	sz += "</div>";
	sz += "<br>";
	sz += "<input type='text' style='width:240px' maxlength='255' name='optionEntry' id='optionEntry'/>";
	sz += "<input type='button' id='entryUpdater' name='entryUpdater' value='Add' style='width:55px' class='bt' onclick='javascript:optionManagerAdd(this, \""+show_options_index+"\");'/></div>";
	sz += "<textarea name='optionManagerImport' style='width:300px;height:230px;"+importshow+"'  id='optionManagerImport'>"+txt+"</textarea><br>";
	sz += "<input type='button' value='Toggle (List Manager / Import Tool)' id='togglebtn' name='togglebtn' style='width:300px' class='bt' onclick='javascript:toggleOptionManagerDisplay(\""+show_options_index+"\");'/>";
	sz += "</p>";
	LIBERTAS_GENERAL_printToId("optionManager",sz);
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- rank the order in the list 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function optionManagerRank(dir, show_options_index){
	var currentRank = -1;
	var start		= -1;
	var finsih		= -1;
	if (document.process_form.optionManagerList.options.length>0){
		currentRank = document.process_form.optionManagerList.selectedIndex;
	}
	if (dir=='up'){
		start  = currentRank;
		finish = currentRank - 1;
	}
	if (dir=='down'){
		start  = currentRank + 1;
		finish = currentRank;
	}
	if (start == -1 || finish == -1 || start > document.process_form.optionManagerList.options.length-1 || finish > document.process_form.optionManagerList.options.length-1){
	
	} else {
		tmp = document.process_form.optionManagerList.options[start].value;
		document.process_form.optionManagerList.options[start] = new Option(document.process_form.optionManagerList.options[finish].value,document.process_form.optionManagerList.options[finish].value);
		document.process_form.optionManagerList.options[finish] =  new Option(tmp,tmp);
		if(dir=='up')
			document.process_form.optionManagerList.options[finish].selected =  true;
		if(dir=='down')
			document.process_form.optionManagerList.options[start].selected =  true;
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- edit an entry in the  option list
- relabel the add button 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function optionManagerEdit(){
	editting_option_index = document.all['optionManagerList'].selectedIndex;
	if (editting_option_index == -1){
		alert("You must select an entry to Edit");
	}else{
		document.all['entryUpdater'].value="Update";
		document.all['optionEntry'].value = document.all['optionManagerList'].options[document.all['optionManagerList'].selectedIndex].value;
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- remove an entry in the option list
- relabel the add button 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function optionManagerRemove(){
	if (editting_option_index == -1){
		alert("You must select an entry to Remove");
	}else{
		document.all['optionManagerList'].options.remove(document.all['optionManagerList'].selectedIndex);
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Add an entry to the option list
- if the add button label is update then change exisiting entry
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function optionManagerAdd(t, show_options_index){
	var txt = new String(document.all['optionEntry'].value);
	if(txt==""){
		alert("Sorry you must supply a value");
	} else {
		if (document.all['entryUpdater'].value=="Update"){
			ok=1;
			for(i=0;i<document.all['optionManagerList'].options.length;i++){
				if(document.all['optionManagerList'].options[i].value.toLowerCase()==txt.toLowerCase() && editting_option_index!=i){
					ok=0;
					break;
				}
			}
			if(ok==1){
				document.all['optionManagerList'].options[editting_option_index] = new Option(txt, txt);
				document.all['optionManagerList'].options[editting_option_index].selected = true;
				document.all['optionEntry'].value = "";
				document.all['entryUpdater'].value ="Add";
			} else {
				alert("Sorry you can not Update this entry as the option already exists");
			}
		} else {
			ok=1;
			for(i=0;i<document.all['optionManagerList'].options.length;i++){
				if(document.all['optionManagerList'].options[i].value.toLowerCase()==txt.toLowerCase()){
					ok=0;
					break;
				}
			}
			if(ok==1){
				document.all['optionManagerList'].options[document.all['optionManagerList'].options.length] = new Option(txt, txt);
				document.all['optionEntry'].value = "";
				document.all['entryUpdater'].value ="Add";
			} else {
				alert("Sorry you can not add that entry as it already exists");
			}
		}
	}
	document.all['optionEntry'].focus()
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Toggle the Option list to allow quick importing
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function toggleOptionManagerDisplay(show_options_index){
	if (optionListMode=="list"){
		var myList = document.process_form.optionManagerImport.value.split("\r\n");
		document.all['optionManagerList'].options.length=0;
		for (var index=0; index < myList.length; index++){
			ok=1;
			for(i=0;i<document.all['optionManagerList'].options.length;i++){
				if(document.all['optionManagerList'].options[i].value.toLowerCase()==myList[index].toLowerCase() || myList[index]==""){
					ok=0;
					break;
				}
			}
			if(ok==1){
				document.all['optionManagerList'].options[document.all['optionManagerList'].options.length] = new Option(myList[index], myList[index]);
			}
		}
		document.all['optionManagerImport'].style.visibility	= 'hidden';
		document.all['optionManagerImport'].style.display		= 'none';
		document.all['listoptionid'].style.visibility			= 'visible';
		document.all['listoptionid'].style.display				= '';
		optionListMode											= "import";
		document.all.backnextbtns.style.display					= '';
		document.all.backnextbtns.style.visibility				= 'visible';
	} else {
		optionListMode="list";
		var l ="";
		for (var index=0; index < document.all['optionManagerList'].options.length; index++){
			if (l!=""){
				l += "\r\n";
			}
			l += document.all['optionManagerList'].options[index].value
		}
		document.process_form.optionManagerImport.value 		= l;
		importListChanged 										= true;
		document.all['optionManagerImport'].style.visibility	= 'visible';
		document.all['optionManagerImport'].style.display		= '';
		document.all['listoptionid'].style.visibility			= 'hidden';
		document.all['listoptionid'].style.display				= 'none';
		document.all.backnextbtns.style.display					= 'none';
		document.all.backnextbtns.style.visibility				= 'hidden';
	}
//	optionManagerDisplay(typeOfDisplay, show_options_index)
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- set Categories
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function setCategories(){
	for (var index=0; index < info_list_of_options.length; index++){
		for(position = 0; position < info_fieldlist.length; position ++){
			if	(
					(info_list_of_options[index][0] == info_fieldlist[position][0]) || 
					(info_fieldlist[position][0].substr(0,info_list_of_options[index][0].length) == info_list_of_options[index][0])
				){
				info_fieldlist[position][7] = info_list_of_options[index][4];
			}
		}
	}
}

function getRank(pos){
	rank ='';
	if (info_fieldlist[pos][7]!="Formatting List"){
		rank += '<div class="rank"><input type="button" name="btnEdit'+pos+'" value="Edit" style="width:'+w+'px" onclick="javascript:EditScreen(1, '+pos+',this);" class="bt"></div>';
	} else {
		rank += "<div class='rank'><img src='/libertas_images/themes/1x1.gif' width='"+(w+2)+"' height='20' alt=''/></div>";
	}
	if (info_fieldlist[pos][0]=="ie_title"){
		rank += "<div class='rank'><img src='/libertas_images/themes/1x1.gif' width='"+(w+2)+"' height='20' alt=''/></div>";
	} else {
		rank += '<div class="rank"><input type="button" value="Remove" name="btnRemove'+pos+'" style="width:'+w+'px" onclick="javascript:directoryManagerRemove('+pos+',this);" class="bt"></div>';
	}
	if (pos < info_fieldlist.length-1){
		if(info_fieldlist.length>1){
			rank += '<div class="rank"><input type="button" value="Down" name="btnDown'+pos+'" style="width:'+w+'px" onclick="javascript:_fieldrank('+pos+',1);" class="bt"></div>';
		} else {
			rank += "<div class='rank'><img src='/libertas_images/themes/1x1.gif' width='"+(w+2)+"' height='20' alt=''/></div>";
		}
	} else {
		rank += "<div class = 'rank'><img src = '/libertas_images/themes/1x1.gif' width = '"+(w+2)+"' height='20' alt=''/></div>";
	}
	if (pos>0){
		if(info_fieldlist.length>pos){
			rank += '<div class="rank"><input type="button" value="Up" style="width:'+w+'px" name="btnUp'+pos+'" onclick="javascript:_fieldrank('+pos+',-1);" class="bt"></div>';
		} else {
			rank += "<div class='rank'><img src='/libertas_images/themes/1x1.gif' width='"+(w+2)+"' height='20' alt=''/></div>";
		}
	} else {
		rank += "<div class='rank'><img src='/libertas_images/themes/1x1.gif' width='"+(w+2)+"' height='20' alt=''/></div>";
	}
	if(info_fieldlist[pos][10]!=0){
		rank += "<img src='/libertas_images/themes/site_administration/ftv2filter.gif' width='24' height='22' alt='Filter'/>";
	} else {
		rank += "<img src='/libertas_images/themes/1x1.gif' width='24' height='22' alt=''/>";
	}
	if(info_fieldlist[pos][9]!="" && info_fieldlist[pos][9]!= "Yes" && info_fieldlist[pos][9]!= "No"){
		rank += "<img src='/libertas_images/themes/site_administration/search.gif' width='24' height='22' alt='Import duplicate checking criteria ["+info_fieldlist[pos][9]+"]'/>";
	} else {
		rank += "<img src='/libertas_images/themes/1x1.gif' width='24' height='22' alt=''/>";
	}
	return rank;
}

function rankRow(position){
	sz ='';
	/*
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// fields indexing
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	// 0,	'key',
	// 1,	'rank',
	// 2,	'selected', ignore backwards compatabliity
	// 3,	'label',
	// 4,	'description',
	// 5,	'type',
	// 6,	'options',
	// 7,	'category'
// 8,	'available on search form'
	// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	special = info_fieldlist[position][14];
	if(special+""=="undefined"){
		special=0;
	}

	sz += '<div style="width:140px;display:inline"><span id="fieldlist' + position + '">';
	sz += '	<input type="hidden" name="type_' + position + '" id="type_' + position + '" value="' + info_fieldlist[position][5] + '" />';
	sz += '	<input type="hidden" name="special_' + position + '" id="special_' + position + '" value="' + special + '" />';
	sz += '	<input type="hidden" name="rank_' + position + '" id="rank_' + position + '" value="' + position + '" />';
	sz += '	<input type="hidden" name="hfield_' + position + '" id="hfield_' + position + '" value="' + info_fieldlist[position][0] + '" />';
	sz += '	<input type="hidden" name="options_' + position + '" id="options_' + position + '" value="' + LIBERTAS_GENERAL_jtidy(info_fieldlist[position][6].join("::ls_option::")) + '" />';
	sz += '	<input type="hidden" name="search_' + position + '" id="search_' + position + '" value="' + info_fieldlist[position][8] + '" />';
	sz += '	<input type="hidden" name="duplicate_' + position + '" id="duplicate_' + position + '" value="' + info_fieldlist[position][9] + '" />';
	sz += '	<input type="hidden" name="filter_' + position + '" id="filter_' + position + '" value="' + info_fieldlist[position][10] + '" />';
	sz += '	<input type="hidden" name="sumlabel_' + position + '" id="sumlabel_' + position + '" value="' + info_fieldlist[position][11] + '" />';
	sz += '	<input type="hidden" name="conlabel_' + position + '" id="conlabel_' + position + '" value="' + info_fieldlist[position][12] + '" />';
	sz += '	<input type="hidden" name="addtotitle_' + position + '" id="addtotitle_' + position + '" value="' + info_fieldlist[position][15] + '" />';	
	sz += '	<input type="hidden" name="urlfield_' + position + '" id="urlfield_' + position + '" value="' + info_fieldlist[position][16] + '" />';		
	sz += '	<input name="visiblefields_'+position+'" id="visiblefields_'+position+'" type="hidden"  value="1" />';
	sz += '	<input name="vfield_'+position+'" id="vfield_'+position+'" type="hidden"  value="'+info_fieldlist[position][3]+'" />';
	sz += '</span>';
	sz += LIBERTAS_GENERAL_unjtidy(info_fieldlist[position][3]);
//	sz += LIBERTAS_GENERAL_unjtidy(info_fieldlist[position][0]);
	sz += '</div>';
	sz += '<div id="optionButtons'+position+'" class="headerrank2" style="width:296px" align="right">';
	sz += 	getRank(position);
	sz += "</div>";
	return sz;
}

function preview_infodir(cmd){
	f = get_form();
	f.target		= 'external_preview';
	f.action		= 'admin/preview.php';
	prev_cmd 		= f.command.value; 
	f.command.value	= cmd;
	ok 				= onSubmitCompose(2);
	f.target		= '_self';
	f.action		= '';
	f.command.value = prev_cmd;
}

function show_summary_output(){
	var sz  = "";
	var doc = document.getElementById("summarydata");
		
	sz += "<p>Below is an example of how one of the summary entries will be displayed</p>";
	
	
	doc.innerHTML = sz;
}
function show_output(name){
	var sz  = "";
	var doc = document.getElementById(name);
	var len = info_fieldlist.length;
	sz += "";
	/*
		// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		// fields indexing
		// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		// 0,	'key',
		// 1,	'rank',
		// 2,	'selected', ignore backwards compatabliity
		// 3,	'label',
		// 4,	'description',
		// 5,	'type',
		// 6,	'options',
		// 7,	'category'
		// 8,	'available on search form'
		// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	sz+="<table width=100% border=0><tr><td style='width:400px' valign='top'><label>Choose field to add to this screen</label><br /><select name='choose_"+name+"_field'>";
	sz+="<option value='-1'>Category</option>";
	if (document.all['info_shop_enabled'].selectedIndex==1){
		sz+="<option value='-2'>Add to basket</option>";
	}
	for(i=0;i<len;i++){
		if (info_fieldlist[i][5]!="colsplitter" && info_fieldlist[i][5]!="rowsplitter"){
			sz+="<option value='"+i+"'>"+info_fieldlist[i][3]+"</option>";
		}
	}
	sz += "</select> <input type=button class='bt' onclick=\"javascript:add_to('"+name+"','choose_"+name+"_field');\" value='Add Field'><br>";	
	sz+="<label>Choose field to add to this screen</label><br /><select name='choose_"+name+"_splitter'>";
	sz += "<option value='colsplitter'>New Column</option>";
	sz += "<option value='rowsplitter'>New Row</option>";
	sz += "</select> <input class='bt' type=button onclick=\"javascript:add_to('"+name+"','choose_"+name+"_splitter');\" value='Add Splitter'></td><td id='propertyField_"+name+"' valign='top'></td></tr></table><hr />";
	sz += "<p>Below is an example of how the contents entry will be displayed</p>";
	if (name == "summary_display"){
		sz	+=	"<p>Select the field that is to be used to link to the content screen</p>";
	} else if(name == 'advancedsearch'){
		sz	+=	"<p>Display Labels above fields <input type='checkbox' name='searchformlabelposition' value='above' checked='true' onclick='search_toggle(\""+name+"\")'> uncheck this box to display them side by side.</p>";
	} else {
	}
	sz += "<div id='"+name+"_div'></div>";
	doc.innerHTML = sz;

	eval("myArrayData = "+name+";");
	display_table(myArrayData, name);
}

function show_output_email_admin(name){
	alert(name);
	var sz  = "";
	var doc = document.getElementById(name);
	alert(doc);
	var len = info_fieldlist.length;
	alert(len);
	sz += "";
}

function display_table(myArray, field){
	var sz = '<input type="hidden" name="'+field+'_num_of_fields" id="'+field+'_num_of_fields" value="' + myArray.length + '" />';
		sz+= "<table width='100%' border='0' cellpadding=3 cellspacing=1 bgcolor=#cccccc><tr bgcolor=#ffffff><td valign=top>";
	var closing = "";
	Linkfound=0;
	var f = get_form();
	var screen=2;
	len = info_fieldlist.length;
	formfield = "choose_"+field+"_field";
	lenOfDropdown = f.elements[formfield].options.length;
	for(lodd=0; lodd<lenOfDropdown;lodd++){
		f.elements[formfield].options[lodd].style.color = "";
	}
	if (document.all['info_shop_enabled'].selectedIndex==1){
		shop_enabled = 1;
	} else {
		shop_enabled = 0;
	}
	for (var i =0; i<myArray.length;i++){
		if (field == "summary_display"){
			screen=1;
			if ((myArray[i]["type"]!="colsplitter") && (myArray[i]["type"]!="rowsplitter") && (myArray[i]["type"]!="URL")){
				for(lodd=0; lodd<lenOfDropdown;lodd++){
					val = f.elements[formfield].options[lodd].value;

					if (val==-1){
						if ("__category__" == myArray[i]["name"]){
							f.elements[formfield].options[lodd].style.color="#cccccc";
						}
					} else if (val==-2){
						if ("__add_to_basket__" == myArray[i]["name"]){
							f.elements[formfield].options[lodd].style.color="#cccccc";
						}
					} else {
						if (info_fieldlist[val][0] == myArray[i]["name"]){
							f.elements[formfield].options[lodd].style.color="#cccccc";
						}
					}
				}
				extra	=	"<input type='radio' id='readmorelink"+i+"' name='"+field+"_link_field' value='"+i+"' onclick='javascript:setproperty(\""+field+"\","+i+",\"Link\");'";
				if(myArray[i]["Link"]+""=="1"){
					extra	+=	" checked='true'";
					Linkfound=1;
				}
				extra	+=	">";
			} else {
				extra	=	"";
			}
		} else {
			for(lodd=0; lodd<lenOfDropdown;lodd++){
				val = f.elements[formfield].options[lodd].value;

				if (val==-1){
					if ("__category__" == myArray[i]["name"]){
						f.elements[formfield].options[lodd].style.color="#cccccc";
					}
				} else if (val==-2){
					if ("__add_to_basket__" == myArray[i]["name"]){
						f.elements[formfield].options[lodd].style.color="#cccccc";
					}
				} else {
					if (info_fieldlist[val][0] == myArray[i]["name"]){
						f.elements[formfield].options[lodd].style.color="#cccccc";
					}
				}
			}
			extra	=	"";
			closing	=	"<input type='hidden' name='"+field+"_link_field' value='-1'>";
		}
		extra += '<input type="hidden" name="'+field+'_name_' + i + '" id="'+field+'_name_' + i + '" value="' + myArray[i]["name"] + '" />';
		for(info_index = 0 ; info_index < len; info_index++){
			if(myArray[i]["name"] == info_fieldlist[info_index][0]){
				myArray[i]["label"] = info_fieldlist[info_index][3];
				myArray[i]["filter"] = info_fieldlist[info_index][10];
				myArray[i]["sumlabel"] = info_fieldlist[info_index][11];
				myArray[i]["conlabel"] = info_fieldlist[info_index][12];
				myArray[i]["special"] = info_fieldlist[info_index][14];
				myArray[i]["addtotitle"] = info_fieldlist[info_index][15];				
				myArray[i]["urlfield"] = info_fieldlist[info_index][16];
			}
		}
		extra += '<input type="hidden" name="'+field+'_label_' + i + '" id="'+field+'_label_' + i + '" value="' + myArray[i]["label"] + '" />';
		extra += '<input type="hidden" name="'+field+'_type_' + i + '" id="'+field+'_type_' + i + '" value="' + myArray[i]["type"] + '" />';
		extra += '<input type="hidden" name="'+field+'_special_' + i + '" id="'+field+'_special_' + i + '" value="' + myArray[i]["special"] + '" />';
		extra += '<input type="hidden" name="'+field+'_rank_' + i + '" id="'+field+'_rank_' + i + '" value="' + (i) + '" />';
		extra += '<input type="hidden" name="'+field+'_filter_' + i + '" id="'+field+'_filter_' + i + '" value="' + myArray[i]["filter"] + '" />';
		extra += '<input type="hidden" name="'+field+'_sumlabel_' + i + '" id="'+field+'_sumlabel_' + i + '" value="' + myArray[i]["sumlabel"] + '" />';
		extra += '<input type="hidden" name="'+field+'_conlabel_' + i + '" id="'+field+'_conlabel_' + i + '" value="' + myArray[i]["conlabel"] + '" />';
		extra += '<input type="hidden" name="'+field+'_addtotitle_' + i + '" id="'+field+'_addtotitle_' + i + '" value="' + myArray[i]["addtotitle"] + '" />';		
		extra += '<input type="hidden" name="'+field+'_urlfield_' + i + '" id="'+field+'_urlfield_' + i + '" value="' + myArray[i]["urlfield"] + '" />';

		p0 = "Lorem ipsum dolor sit amet, consectetuer adipiscing elit, "
		p1 = "<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.</p>";
		p2 = "<p>Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi. Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat</p>";
		p3 = "<ul><li>Lorem ipsum dolor sit amet, </li><li>Sed diam nonummy nibh euismod . </li><li>Ut wisi enim ad minim veniam.</li></ul>";
		p0 = "";
		p1 = "";
		p2 = "";
		p3 = "";
		
//		alert(extra);
		options		 = "<div style='width:auto;display:inline;text-align:right'>";
		options 	+= "<a href='javascript:remove_from(\""+field+"\","+i+");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Remove</a> ";
		if(i==0){
			options += "<a style='width:60px;background-color:#ebebeb;color:#999999;border:1px solid #cccccc;text-align:center;text-decoration:none'>Up</a> ";
		} else {
			options += "<a href='javascript:move_from(\""+field+"\","+i+",\"Up\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Up</a> ";
		}
		if(i==myArray.length-1){
			options += "<a style='width:60px;background-color:#ebebeb;color:#999999;border:1px solid #cccccc;text-align:center;text-decoration:none'>Down</a>"
		} else {
			options += "<a href='javascript:move_from(\""+field+"\","+i+",\"Down\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Down</a>"
		}
		if(field=="summary_display" || field=="content_display"){
			options += "<a href='javascript:edit_label_position(\""+field+"\","+i+",\"Edit\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Edit</a>";
		}
		options 	+= "</div>"
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        - display summary and content layout
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        */
		if(field != 'advancedsearch'){
			if (myArray[i]["type"]=="colsplitter"){
				sz += "<br>"+extra+"<a href='javascript:remove_from(\""+field+"\","+i+");' class='bt' style='width:120px;text-align:center;text-decoration:none'>Remove Column</a> ";
				sz += "<a href='javascript:move_from(\""+field+"\","+i+",\"Up\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Up</a> ";
				sz += "<a href='javascript:move_from(\""+field+"\","+i+",\"Down\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Down</a></td><td valign=top width='50%'>";
			} else if (myArray[i]["type"]=="rowsplitter"){
				sz += "</td></tr></table>"+extra+"<a href='javascript:remove_from(\""+field+"\","+i+");' class='bt' style='width:120px;text-align:center;text-decoration:none'>Remove Row</a> ";
				sz += "<a href='javascript:move_from(\""+field+"\","+i+",\"Up\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Up</a> ";
				sz += "<a href='javascript:move_from(\""+field+"\","+i+",\"Down\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Down</a><table width='100%' border='0' cellpadding=3 cellspacing=1 bgcolor=#cccccc><tr bgcolor=#ffffff><td valign=top width='50%'>";
			} else if (myArray[i]["type"]=="select" || myArray[i]["type"]=="radio"){
				sz += "<p><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"<br></p>";
			} else if ((myArray[i]["type"]=="check") || (myArray[i]["type"]=="associated_entries")){
				sz += "<p><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"<br>"+p3+"</p>";
			} else if (myArray[i]["type"]=="smallmemo"){
				sz += "<p><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"<br></p>"+p1;
			} else if (myArray[i]["type"]=="memo"){
				sz += "<p><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"<br></p>"+p1+p2;
			} else if (myArray[i]["type"]=="URL"){
				sz += "<p><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"<br>Choose display option for this url <select  name='"+field+"_url_" + i + "' id='"+field+"_url_" + i + "' onchange='javascript:updatefn(\""+field+"\")'>";
				sz += "<option value=''>Display url here as link</option>";
				sz += "<optgroup label='Clicking on this field will activate this link'>";
				var url_data = get_data(myArray[i]["name"]);
				var ud_len = url_data.length;
				for (var z =0; z < myArray.length; z++){
					if ((myArray[z]["type"]!="colsplitter") && (myArray[z]["type"]!="rowsplitter") && (myArray[z]["type"]!="URL") && (myArray[z]["type"]!="check") && (myArray[z]["type"]!="list")){
						sz+="<option value='"+myArray[z]["name"]+"'";
						for(var ud =0; ud < ud_len;ud++){
							if(url_data[ud][0]==screen && url_data[ud][1]==myArray[z]["name"]){
								sz+=" selected='true'";
							}
						}
						sz+=">"+myArray[z]["label"]+"</option>";
					}
				}
				sz+="</optgroup></select></p>";
			} else {
				sz += "<p style='width:auto;'><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"<br>"+p0+"</p>";
			}
		} else {
			/*
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            - advanced search display
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            */
			if (myArray[i]["type"]=="colsplitter"){
				sz += "<br>"+extra+"<a href='javascript:remove_from(\""+field+"\","+i+");' class='bt' style='width:120px;text-align:center;text-decoration:none'>Remove Column</a> ";
				sz += "<a href='javascript:move_from(\""+field+"\","+i+",\"Up\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Up</a> ";
				sz += "<a href='javascript:move_from(\""+field+"\","+i+",\"Down\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Down</a></td><td valign=top width='50%'>";
			} else if (myArray[i]["type"]=="rowsplitter"){
				sz += "</td></tr></table>"+extra+"<a href='javascript:remove_from(\""+field+"\","+i+");' class='bt' style='width:120px;text-align:center;text-decoration:none'>Remove Row</a> ";
				sz += "<a href='javascript:move_from(\""+field+"\","+i+",\"Up\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Up</a> ";
				sz += "<a href='javascript:move_from(\""+field+"\","+i+",\"Down\");' class='bt' style='width:60px;text-align:center;text-decoration:none'>Down</a><table width='100%' border='0' cellpadding=3 cellspacing=1 bgcolor=#cccccc><tr bgcolor=#ffffff><td valign=top width='50%'>";
			} else if (myArray[i]["type"]=="check" || myArray[i]["type"]=="select" || myArray[i]["type"]=="radio"){
				if(searchlabelposition){
					sz += "<p><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"</p>";
				} else {
					sz += "<p><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"</p>";
				}
			} else {
				if(searchlabelposition){
					sz += "<p><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"</p>";
				} else {
					sz += "<p><div style='display:inline;width:50%;'>"+extra+"<strong>"+myArray[i]["label"]+"</strong></div>"+options+"<br><input type='text' value='Lorem ipsum dolor sit amet' style='width:auto'></p>";
				}
			}
		}
	}
	sz+= "</td></tr></table>"+closing;
	var doc = document.getElementById(field+"_div");
	doc.innerHTML = sz;
	if (field == "summary_display"){
		if(Linkfound==0){
			try{
			var e = document.getElementById("readmorelink0");
			e.checked=true;
			} catch(e){
			}
		}
	}
//	alert(sz);
}

function add_to(array_name,fieldElement){
	var f = get_form();
	val = f.elements[fieldElement].options[f.elements[fieldElement].selectedIndex].value;
	eval("myArray = "+array_name+";");
	mlen = myArray.length
	if (val=="colsplitter" || val=="rowsplitter"){
		myArray[mlen] = Array();
		myArray[mlen]["type"]	= val;
		myArray[mlen]["label"]	= val;
		myArray[mlen]["Link"]	= 0;
		if (val=="colsplitter"){
			myArray[mlen]["name"]	= "ie_splitterCol";
		} else {
			myArray[mlen]["name"]	= "ie_splitterRow";
		}
	}else {
		/*
		// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		// fields indexing
		// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		// 0,	'key',
		// 1,	'rank',
		// 2,	'selected', ignore backwards compatabliity
		// 3,	'label',
		// 4,	'description',
		// 5,	'type',
		// 6,	'options',
		// 7,	'category'
		// 8,	'available on search form'
		// 9,	'duplication'
		// 10,	'filterable'
		// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		ok=1;
		if (val>-1){
			// if not a category field
			for(var i=0;i<mlen;i++){
				if (myArray[i]["name"] == info_fieldlist[val][0]){
					ok=0;
				}	
			}
		} else if (val==-1){
			// if category field
			for(var i=0;i<mlen;i++){
				if (myArray[i]["name"] == "__category__"){
					ok=0;
				}	
			}
		}  else if (val==-2){
			// if add to basket
			for(var i=0;i<mlen;i++){
				if (myArray[i]["name"] == "__add_to_basket__"){
					ok=0;
				}	
			}
		}
		if(ok==1){
			if(val==-1){
				myArray[mlen] = Array();
				myArray[mlen]["type"]	= "__category__";
				myArray[mlen]["name"]	= "__category__";
				myArray[mlen]["label"]	= "Display Category";
				myArray[mlen]["Link"]	= 0;
			} else if(val==-2){
				myArray[mlen] = Array();
				myArray[mlen]["type"]	= "__add_to_basket__";
				myArray[mlen]["name"]	= "__add_to_basket__";
				myArray[mlen]["label"]	= "Add to basket";
				myArray[mlen]["Link"]	= 0;
			} else {
				myArray[mlen] = Array();
				myArray[mlen]["type"]	= info_fieldlist[val][5];
				myArray[mlen]["label"]	= info_fieldlist[val][3];
				if(mlen==0 && array_name=='summary_display'){
					myArray[mlen]["Link"]	= 1;
				} else {
					myArray[mlen]["Link"]	= 0;
				}
				myArray[mlen]["name"]	= info_fieldlist[val][0];
			}
		} else {
			alert("you have already added that element to the output");
		}
	}
	if (array_name == "content_display"){
		update_modified("modified_content_screen");
	} else if (array_name == "summary_display"){
		update_modified("modified_summary_screen");
	}
	display_table(myArray, array_name);
}

function remove_from(array_name,index){
	eval("myArray = "+array_name+";");
	myArray.splice(index,1);
	update_modified("modified_entry_screen");
	if (array_name == "content_display"){
		update_modified("modified_content_screen");
	} else if (array_name == "summary_display"){
		update_modified("modified_summary_screen");
	}
	display_table(myArray, array_name);
}

function move_from(array_name,index,cmd){
	eval("myArray = "+array_name+";");
	tmp 						= new Array();
	tmp["type"] 				= myArray[index]["type"]; 
	tmp["label"] 				= myArray[index]["label"]; 
	tmp["Link"] 				= myArray[index]["Link"]; 
	tmp["name"] 				= myArray[index]["name"]; 
	if (cmd=="Down"){
		myArray[index]["type"]  	= myArray[index + 1]["type"];
		myArray[index]["label"]  	= myArray[index + 1]["label"];
		myArray[index]["Link"]  	= myArray[index + 1]["Link"];
		myArray[index]["name"]  	= myArray[index + 1]["name"];
		myArray[index + 1]["name"] 	= tmp["name"];
		myArray[index + 1]["type"] 	= tmp["type"];
		myArray[index + 1]["label"] = tmp["label"];
		myArray[index + 1]["Link"] = tmp["Link"];
	} else {
		myArray[index]["type"]  	= myArray[index - 1]["type"];
		myArray[index]["label"]  	= myArray[index - 1]["label"];
		myArray[index]["Link"]  	= myArray[index - 1]["Link"];
		myArray[index]["name"]  	= myArray[index - 1]["name"];
		myArray[index - 1]["name"] 	= tmp["name"];
		myArray[index - 1]["Link"] 	= tmp["Link"];
		myArray[index - 1]["type"] 	= tmp["type"];
		myArray[index - 1]["label"] = tmp["label"];
	}
	if (array_name == "content_display"){
		update_modified("modified_content_screen");
	} else if (array_name == "summary_display"){
		update_modified("modified_summary_screen");
	}
	display_table(myArray, array_name);
}

function setproperty(array_name,index,field){
	eval("myArray = "+array_name+";");
	if (field=="Link"){
		for(var i =0; i<myArray.length;i++){
			myArray[i][field] = 0;
		}
	}
	if (index>=0){
		myArray[index][field]  = 1;
	}
	if (array_name == "content_display"){
		update_modified("modified_content_screen");
	} else if (array_name == "summary_display"){
		update_modified("modified_summary_screen");
	}
}

function add_to_array(arr,index,val,pos){
	eval("myArray = "+arr+";");
	if (pos==-1){
		pos = myArray.length
		myArray[pos]		= new Array();
	}
	myArray[pos][index] = val; 
	return pos;
}

function update_modified(fieldname){
	f= get_form();
	f.elements[fieldname].value=1;
}

function updatefn(array_name){
	if (array_name == "content_display"){
		update_modified("modified_content_screen");
	} else if (array_name == "summary_display"){
		update_modified("modified_summary_screen");
	}
}
function get_data(datafield){
	var len = info_fieldlist.length;
	for(var i=0;i<len;i++){
		if (info_fieldlist[i][0] == datafield){
			return info_fieldlist[i][6];
		}
	}
	return Array(Array(0,""));
}
function search_toggle(array_name){
	searchlabelposition = !searchlabelposition;
	display_table(myArray, array_name);
}

/*
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// fields indexing
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// 0,	'key',
// 1,	'rank',
// 2,	'selected', ignore backwards compatabliity
// 3,	'label',
// 4,	'description',
// 5,	'type',
// 6,	'options',
// 7,	'category'
// 8,	'available on search form'
// -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- fn::makeAvailableToSearch(index)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function makeAvailableToSearch(index){
//alert(advancedsearch.length);	
pointerIndex  =  add_to_array('advancedsearch',"label", info_fieldlist[index][3],-1); // -1 = start new entry
pointerIndex  =  add_to_array('advancedsearch',"name",info_fieldlist[index][0],pointerIndex);
pointerIndex  =  add_to_array('advancedsearch',"type",info_fieldlist[index][5],pointerIndex);
}

function makeUnAvailableToSearch(id){
	found=-1;
	for (var i =0; i<advancedsearch.length;i++){
		if (advancedsearch[i]["name"] == info_fieldlist[id][0]){
			found=i;
		}
	}
	if (found!=-1){
		advancedsearch.splice(found,1);
	}
}

function __fn_removeElement(index){
	screenlist= new Array("content_display","summary_display","advancedsearch");
	for(var z=0; z<screenlist.length;z++){
		found=-1;
		eval("myArray = "+screenlist[z]+";");
		for (var i =0; i<myArray.length;i++){
			if (myArray[i]["name"] == info_fieldlist[index][0]){
				found=i;
			}
		}
		if (found!=-1){
			myArray.splice(found,1);
		}
	}
	
}

function  update_fields(){
	screenlist= new Array("content_display","summary_display","advancedsearch");
	len = info_fieldlist.length;
	for(var z=0; z<screenlist.length;z++){
		eval("myArray = "+screenlist[z]+";");
		for(var i=0; i<myArray.length;i++){
			for(info_index = 0 ; info_index < len; info_index++){
				if(myArray[i]["name"] == info_fieldlist[info_index][0]){
					document.getElementById(screenlist[z] + '_filter_'	 + i).value = info_fieldlist[info_index][10];
					document.getElementById(screenlist[z] + '_sumlabel_' + i).value = info_fieldlist[info_index][11];
					document.getElementById(screenlist[z] + '_conlabel_' + i).value = info_fieldlist[info_index][12];
					document.getElementById(screenlist[z] + '_special_' + i).value = info_fieldlist[info_index][14];
					document.getElementById(screenlist[z] + '_addtotitle_' + i).value = info_fieldlist[info_index][15];					
					document.getElementById(screenlist[z] + '_urlfield_' + i).value = info_fieldlist[info_index][16];
				}
			}
		}
	}
}

function toggle_ecommerce(){
	if (document.all['info_shop_enabled'].selectedIndex==1){
		EditScreen(0); // add ability to add shoping cart fields
	} else {
		// remove shopping cart fields from screens
		ok = confirm("You are about to remove fields containing information required for purchasing items.\nAre you sure");
		if(ok){
			// remove all ecommerce fields
			ls = Array("summary_display", "content_display", "advancedsearch");
			for(z=0;z<ls.length;z++){
				eval("myArrayData = "+ls[z]+";");
				for(i=0;i<myArrayData.length;i++){
					if(myArrayData[i]["name"]=="__add_to_basket__"){
						myArrayData.splice(i,1);
						break;
					}
				}
			}
			EditScreen(0); // add ability to add shoping cart fields
//			refresh_mappings();
//			viewmetadatamaping();
		} else {
			document.all['info_shop_enabled'].options[1].selected = true;
			document.all['info_shop_enabled'].options[0].selected = false;
		}
	}
	
}
/**
*
*/
function viewmetadatamaping(){
	refresh_mapping_indexes();
	out="";
	/*
	 for each field defined (screen0)
	 check index 13 != '' means mapped
	 if index 13 == '' then check list of options index 0 and 7
	*/
//	alert("1697) map leng :: "+mapping.length+" iField :: "+info_fieldlist[info_fieldlist.length - 1].join("::"));
	/*************************************************************************************************************************
    * check each defined field and see if it is mapped
    *************************************************************************************************************************/
	for (z=0;z<info_fieldlist.length;z++){
		if (info_fieldlist[z][13] != ""){
			out += info_fieldlist[z][0]+" "+info_fieldlist[z][13]+"\n";
			for (x=0;x<metadata_tags.length; x++){
				if (metadata_tags[x]["key"]==info_fieldlist[z][13]){
					ml = mapping.length
					ok=1;
					for(mli=0;mli<ml;mli++){
						if(mapping[mli][0]==z){
							ok=0;
							break;
						}
					}
					if(ok==1){
						mapping[mapping.length] = new Array(z, info_fieldlist[z][0], x, metadata_tags[x]["label"]);
					}
					break;
				}
			}
		}
	}
	for (z=0;z<info_fieldlist.length;z++){
		if (info_fieldlist[z][13] == ""){
			for (i = 0; i < info_list_of_options.length; i++){
				exit=0;
				if (info_list_of_options[i][7]!=""){
					for (x=0;x<metadata_tags.length; x++){
						if (info_fieldlist[z][0].indexOf(info_list_of_options[i][0])>-1 && metadata_tags[x]["key"]==info_list_of_options[i][7]){
							ok = 1;
							for(y=0;y<mapping.length;y++){
								if(mapping[y][2]==x){
									ok = 0;
								}
							} 
							if(ok==1){
								mapping[mapping.length] = new Array(z, info_fieldlist[z][0], x, metadata_tags[x]["label"]);
								exit=1;
							}
							break;
						}
					}
				}
				if (exit==1){
					break;
				}
			}
		}
	}
//	alert(mapping.length);
	refresh_mapping_indexes();
	refresh_mappings();
}

function metadata_map_add(){
	md = document.all['choosemetadatatomap'].options[document.all['choosemetadatatomap'].selectedIndex].value
	fd = document.all['choosefieldtomap'].options[document.all['choosefieldtomap'].selectedIndex].value
	if (md==-1){
		alert('You need to choose a meatadata field');
	} else if (fd==-1){
		alert('You need to choose a database field');
	} else {
		for (i=0;i<info_fieldlist.length;i++){
			if (info_fieldlist[i][0] == fd){
				z = i;
				break;
			}
		}
		for (i=0;i<metadata_tags.length;i++){
			if (metadata_tags[i]["key"] == md){
				x = i;
				break;
			}
		}
		mapping[mapping.length] = new Array(z, info_fieldlist[z][0], x, metadata_tags[x]["label"]);
		refresh_mapping_indexes();
		refresh_mappings();
	}
}

function metadata_map_remove(index){
	mapping.splice(index,1);
	refresh_mapping_indexes();
	refresh_mappings();
}

function refresh_mappings(){
	refresh_mapping_indexes();
	d = document.getElementById('selectfieldtomap');
	d.innerHTML = "Select a field to map <select name='choosefieldtomap' id='choosefieldtomap'><option value='-1'>Select one</option></select> and map it to the metadata field <select name='choosemetadatatomap' id='choosemetadatatomap'><option value='-1'>Select one</option></select> <input type='button' onclick='javascript:metadata_map_add();' value='Add mapping' class='bt' />";
	document.all['choosemetadatatomap'].options.length=1;
	show=0;
	for (i=0;i<metadata_tags.length;i++){
		ok = 1;
		for (z=0;z<mapping.length;z++){
			if(metadata_tags[mapping[z][2]]["key"] == metadata_tags[i]["key"]){
				ok=0;
			}
		}
		if (ok==1){
			document.all['choosemetadatatomap'].options[document.all['choosemetadatatomap'].options.length] = new Option (metadata_tags[i]["label"], metadata_tags[i]["key"]);
			show=1;
		}
	}
	if(show==1){
		document.all['choosefieldtomap'].options.length=1;
		for (i=0;i<info_fieldlist.length;i++){
			ok = 1;
			for (z=0;z<mapping.length;z++){
				if(mapping[z][0] == i){
					ok=0;
				}
			}
			if (ok==1){
				document.all['choosefieldtomap'].options[document.all['choosefieldtomap'].options.length] = new Option (info_fieldlist[i][3], info_fieldlist[i][0]);
				show=1;
			}
		}
	}
	if(show==1){
		document.all['selectfieldtomap'].style.display='';
	} else {
		document.all['selectfieldtomap'].style.display='none';
	}
}

function draw_metadata_mapping(){
	//alert("draw");
	var maplist ="";
	maplist +="<p id='selectfieldtomap'></p>";
	maplist +="<div><div style='width:39%;display:inline;padding:3px;' class='bt'>Field</div><div style='width:39%;display:inline;padding:3px;' class='bt'>Map to Meta-Data field</div><div style='width:20%;display:inline;padding:3px;' class='bt'>Option</div></div>";
	c=0;
	for(x=0; x<mapping.length;x++){
		mystyle ="";
		if(c==1){
			mystyle = "background-color:#ebebeb;";
			c=-1;
		}
		c++;
		if(info_fieldlist[mapping[x][0]]+""=="undefined"){
			mapping.splice(x,1);
		}
		position = mapping[x][0];
		maplist += "<div style='"+mystyle+"'>";
		maplist	+= '<input type="hidden" name="mdmap[]" id="mdmap_' + position + '" value="' + metadata_tags[mapping[x][2]]["key"] + '::'+mapping[x][1]+'" />';
		maplist += "<div style='width:39%;display:inline;padding:3px;'>"+info_fieldlist[position][3]+"</div>";
		maplist += "<div style='width:39%;display:inline;padding:3px;'>"+mapping[x][3]+"</div>";
		maplist += "<div style='width:20%;display:inline;padding:3px;'><a href='javascript:metadata_map_remove("+x+")' style='width:"+w+"px;text-align:center;padding:3px;text-decoration:none' class='bt'>Remove</a></div>";
		maplist += "</div>";
	}
	maplist += "</div>";
	maplist	+= '<input type="hidden" name="mdcount" value="'+mapping.length+'" />';
	var doc = document.all['metadata_mapping'];
	doc.innerHTML = maplist;
}

function edit_label_position(fieldName, index, Cmd){
	eval("myArray = "+fieldName+";");
//	alert(myArray[index]["name"]+" ["+info_fieldlist.length+"]")
	var label_setting=0;
	for (i=0;i<info_fieldlist.length;i++){
		if(info_fieldlist[i][0]==myArray[index]["name"]){
			if (fieldName=="summary_display"){
				label_setting = info_fieldlist[i][11];
				break;
			}
			if (fieldName=="content_display"){
				label_setting = info_fieldlist[i][12];
				break;
			}
		}
	}
	var docPropertyField = document.getElementById("propertyField_"+fieldName);
	sz = "<p><strong>Choose label display position for '"+myArray[index]["label"]+"'</strong><br>"
	/**
          *	summary label display option
     	    */
	sz += "<input type='hidden' name='screenName' value='"+fieldName+"'/>How to display the label <select name='how_to_display_label' id ='how_to_display_label'>";
	sz +=	"<option value='0'>Do not display the label</option>";
	sz +=	"<option value='1'";
	if (label_setting=='1'){
		sz+=" selected='true'";
	}
	sz +=	">Display to the left of the content</option>";
	sz +=	"<option value='2'";
	if (label_setting=='2'){
		sz+=" selected='true'";
	}
	sz +=	">Display above the content</option>";
	sz += "</select> ";
	sz += "<input type=button value='Store Change' class=bt onclick=\"javascript:setfieldlabel('"+fieldName+"',"+index+");\">";
	sz += "<input type=button value='Cancel' class=bt onclick=\"javascript:setfieldlabel('"+fieldName+"',-1);\"></p>";
	docPropertyField.innerHTML = sz;
}

function setfieldlabel(fieldName, index){
	if(index!=-1){
		if (fieldName=="summary_display"){
			pos = 11;
		}
		if (fieldName=="content_display"){
			pos = 12;
		}
		var e = document.getElementById("how_to_display_label");
		for (i=0;i<info_fieldlist.length;i++){
			if(info_fieldlist[i][0]==myArray[index]["name"]){
				info_fieldlist[i][pos] = e.options[e.options.selectedIndex].value;
				break;
			}
		}
	}
	var docPropertyField = document.getElementById("propertyField_"+fieldName);
	docPropertyField.innerHTML = "";
	eval("myArrayData = "+fieldName+";");
	display_table(myArrayData, fieldName);
	RankScreen();
}

function refresh_mapping_indexes(){
	for (x = 0; x < mapping.length; x++){
		if (info_fieldlist[mapping[x][0]][0]+"" != mapping[x][1]+""){
			for (i = 0; i < info_fieldlist.length; i++){
				if (info_fieldlist[i][0]+"" == mapping[x][1]+""){
					mapping[x][0] = i;
					break;
				}
			}
		}
	}
	draw_metadata_mapping();
}