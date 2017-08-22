/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 	Information Directory Feature List field selection Javascript file.
-	$Date: 2005/01/21 00:03:24 $
-	$Revision: 1.1 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var field_list_definition = new __LS_field_list();
/*************************************************************************************************************************
* 
*************************************************************************************************************************/
function module_info_featured_lists_start(){
	field_list_definition.list = feature_fields;
	field_list_definition.selectedlist = new Array();
	field_list_definition.selectedlistdetails = selected_fields;
	
	for(i=0;i<field_list_definition.selectedlistdetails.length;i++){
		if(selected_fields[i].value == "__new_row__"){
			field_list_definition.selectedlist[field_list_definition.selectedlist.length] = "__new_row__";
		} else if(selected_fields[i].value == "__new_column__"){
			field_list_definition.selectedlist[field_list_definition.selectedlist.length] = "__new_column__";
		} else {
			val = selected_fields[i].value;
			for(z=0; z<field_list_definition.list.length; z++){
				if (field_list_definition.list[z].value == val){
					field_list_definition.selectedlist[field_list_definition.selectedlist.length] = z;
					field_list_definition.list[z].labelsetting = selected_fields[i].labelsetting;
				}
			}
		}
	}
	field_list_definition.draw();
}
/*************************************************************************************************************************
* 
*************************************************************************************************************************/
function __LS_field_list(){
	// Properties
	this.name				= "field_list_definition";	// variable name of this object  
	this.rank				= 0;						// max rankings
	this.list				= new Array();				// Directory list Identifier 
	this.selectedlist		= new Array();				// 
	this.selectedlistdetails= new Array();				// 
	this.outputDiv			= "feature_fields";			// location to publish to
	// methods
	this.draw				= __LSFL_draw;				// draw the content to the screen
	this.add				= __LSFL_add;				// add an entry on load to the list property
	this.remove				= __LSFL_remove;			// remove
	this.up					= __LSFL_up;				// up
	this.down				= __LSFL_down;				// down
	this.edit				= __LSFL_edit;				// edit
	this.setfieldlabel		= __LSFL_setfieldlabel;
} 

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__LSFL_draw
-	usage	:	object.draw(void);
-	returns	:	true on success
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	Draws the outputlist to the screen to the display position this.outputDiv
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __LSFL_draw(){
	HTMLContent = "";
	if(this.rendered==0){
		HTMLContent = "<input type='button' id='addbtn' onclick='javascript:myobjectlist.retrieve();return false;' value='Add new entry to list' class='bt'/><br />";
	}
	try{
		if (this.outputDiv==""){
			ok=false
		} else {
			HTMLContent += "<div style='display:inline;width:50%'><label for='selectfield'>Select field</label> <select id='selectfield' name='selectfield'>";
			HTMLContent += "<option value='__new_row__'>New Row</option>";
			HTMLContent += "<option value='__new_column__'>New Column</option>";
			for (i=0;i<this.list.length;i++){
				ok=true;
				for(z=0;z<this.selectedlist.length;z++){
					if(this.selectedlist[z]==i){
						ok=false;
					}
				}
				if(ok){
					HTMLContent += "<option value='"+i+"'>"+this.list[i].label+"</option>";
				}
			}
			HTMLContent += "</select><input type='button' onclick='"+this.name+".add("+i+");' value='Add field' class='bt'></div><div id='propertyField' style='display:inline;width:50%'></div>";
			HTMLContent += "<table class='width100percent'><tr><td valign='top'>";
			r=0;
			for (i=0;i<this.selectedlist.length;i++){
				inputdata = "";
				if(this.selectedlist[i] == "__new_row__"){
					inputdata = "<input type='hidden' name='fields[]' value='__new_row__'/>";
					inputdata += "<input type='hidden' name='iffr_label_display[]' value='0'/>"
					inputdata += "<input type='hidden' name='label[]' value=''/>"
					HTMLContent += "</td>";
					HTMLContent += "	</tr>";
					HTMLContent += "		</table>";
					HTMLContent += "Row"+inputdata;
					if (i != 0){
						HTMLContent += "	<input type='button' onclick='"+this.name+".up("+i+");' class='bt' value='Up'></input>";
					} else {
						HTMLContent += "	<img src='/libertas_images/themes/1x1.gif' width='30' height='25'>";
					}
					if (r != this.selectedlist.length-1){
						HTMLContent += "	<input type='button' onclick='"+this.name+".down("+i+");' class='bt' value='Down'></input>";
					} else {
						HTMLContent += "	<img src='/libertas_images/themes/1x1.gif' width='55' height='25'>";
					}
					HTMLContent += "		<input type='button' onclick='"+this.name+".remove("+i+");' class='bt' value='Remove'></input>";
					HTMLContent += "	<img src='/libertas_images/themes/1x1.gif' width='60' height='25'>";
					HTMLContent += "<table class='width100percent'>";
					HTMLContent += "	<tr>";
					HTMLContent += "		<td valign='top'>";
				} else if(this.selectedlist[i] == "__new_column__"){
					inputdata = "<input type='hidden' name='fields[]' value='__new_column__'/>";
					inputdata += "<input type='hidden' name='iffr_label_display[]' value='0'/>"
					inputdata += "<input type='hidden' name='label[]' value=''/>"
					HTMLContent += "<p><div style='display:inline;width:49%;'>Column"+inputdata+"</div><div style='display:inline;width:50%;'>";
					if (i != 0){
						HTMLContent += "	<input type='button' onclick='"+this.name+".up("+i+");' class='bt' value='Up'></input>";
					} else {
						HTMLContent += "	<img src='/libertas_images/themes/1x1.gif' width='30' height='25'>";
					}
					if (r != this.selectedlist.length-1){
						HTMLContent += "	<input type='button' onclick='"+this.name+".down("+i+");' class='bt' value='Down'></input>";
					} else {
						HTMLContent += "	<img src='/libertas_images/themes/1x1.gif' width='55' height='25'>";
					}
					HTMLContent += "		<input type='button' onclick='"+this.name+".remove("+i+");' class='bt' value='Remove'></input>";
					HTMLContent += "	<img src='/libertas_images/themes/1x1.gif' width='60' height='25'>";
					HTMLContent += "</div></p></td>";
					HTMLContent += "		<td valign='top'>";
				} else {
					HTMLContent += "<p><div style='display:inline;width:49%;'><input type='hidden' name='fields[]' value='"+this.list[this.selectedlist[i]].value+"' />";
					HTMLContent += "<input type='hidden' name='iffr_label_display[]' value='"+this.list[this.selectedlist[i]].labelsetting+"'/>";
					HTMLContent += "<input type='hidden' name='label[]' value='"+this.list[this.selectedlist[i]].label+"'/>";
					HTMLContent += ""+this.list[this.selectedlist[i]].label+"";
					HTMLContent += "</div><div style='display:inline;width:49%;'>";
					if (i != 0){
						HTMLContent += "	<input type='button' onclick='"+this.name+".up("+i+");' class='bt' value='Up'></input>";
					} else {
						HTMLContent += "	<img src='/libertas_images/themes/1x1.gif' width='30' height='25'>";
					}
					
					if (r != this.selectedlist.length - 1){
						HTMLContent += "	<input type='button' onclick='"+this.name+".down("+i+");' class='bt' value='Down'></input>";
					} else {
						HTMLContent += "	<img src='/libertas_images/themes/1x1.gif' width='55' height='25'>";
					}
					HTMLContent += "		<input type='button' onclick='"+this.name+".remove("+i+");' class='bt' value='Remove'></input>";
					HTMLContent += "		<input type='button' onclick='"+this.name+".edit("+i+");' class='bt' value='Edit'></input>";
					HTMLContent += "		</div>		</p>";
				}
				r++;
			}
			HTMLContent += "		</td></tr></table>";
			printToId(this.outputDiv,HTMLContent);
			ok=true;
		}
	} catch (e){
		ok = false;
	}
	
	return ok;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__LSFL_add
-	usage	:	object.add(index [int]);
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	allows the addition of a field into the define list of fields for this featured list
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __LSFL_add(index){
	var el = document.getElementById('selectfield');
	selected_index = el.options[el.selectedIndex].value;
	this.selectedlist[this.selectedlist.length] = selected_index;
	this.draw();
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__LSFL_remove
-	usage	:	object.remove(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	removes entry from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __LSFL_remove(index){
	this.selectedlist.splice(index,1);
	this.draw();
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__LSFL_up
-	usage	:	object.up(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	changes the rank order of selected entries from selected list and redisplays content
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __LSFL_up(index){
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -	store the top entry in a tmp location
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	tmp_value								= this.selectedlist[index-1];
	this.selectedlist[index-1]				= this.selectedlist[index];
	this.selectedlist[index]				= tmp_value;
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -	file the content with the temp data
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	this.draw();
}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- function	:	__LSFL_down
-	usage	:	object.down(index [int]);
-	returns	:	boolean (true on successfully retrieving cache)
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Description :
-	move the item below up one;
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function __LSFL_down(index){
	this.up(index+1);
}

function __LSFL_edit(index){
	if ((this.selectedlist[index]!='__new_row__') && (this.selectedlist[index]!='__new_column__')){
		label_setting = this.list[this.selectedlist[index]].labelsetting;
		var docPropertyField = document.getElementById("propertyField");
		sz = "<p><strong>Choose label display position for '"+this.list[this.selectedlist[index]].label+"'</strong><br>"
		sz += "How to display the label <select name='how_to_display_label' id ='how_to_display_label'>";
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
		sz += "</select> <input type=button value='Store Change' class=bt onclick=\"javascript:"+this.name+".setfieldlabel("+index+");\"><input type=button value='Cancel' class=bt onclick=\"javascript:"+this.name+".setfieldlabel(-1);\"></p>";
		docPropertyField.innerHTML = sz;
	}
	
}

function __LSFL_setfieldlabel(index){
	if(index!=-1){
		var e = document.getElementById("how_to_display_label");
		this.list[this.selectedlist[index]].labelsetting = e.options[e.options.selectedIndex].value;
	}
	var docPropertyField = document.getElementById("propertyField");
	docPropertyField.innerHTML = "";
	this.draw();
}