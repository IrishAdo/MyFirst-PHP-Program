/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Javascript Object to manage the ranking abd generation of categories
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/


function CategoryList(order, output_identifier, rootIdentifier, name, rootCount){
	this.name = name;
	this.form		= get_form();
	this.list		= new Array();
	this.sortorder	= order;
	this.root		= rootIdentifier;
	this.root_count	= rootCount;
	this.output		= document.getElementById(output_identifier);
//	this.filter		= "";
//	this.type		= new Array();
	
	
	this.set				= __CAT_ranking_set_attribute;
	this.display			= __CAT_ranking_display;
	this.add				= __CAT_ranking_add;
	this.insertdata 		= __CAT_insertdata;
	this.removedata			= __CAT_file_remover;
	this.gen_hidden 		= __CAT_generate_hidden;
	this.move				= __CAT_move;
	this.show				= __CAT_show;
	this.cancel_form		= __CAT_cancel;
	this.property			= __CAT_property;
	this.updateProperties	= __CAT_propertyUpdate;
	this.tidy				= __CAT_tidy; 
	this.execute			= __CAT_execute;
	this.set_sort			= __CAT_alphabetic;
	this.sort_entries		= __CAT_sort;
	this.compair			= __CAT_sort_a2z;
}

function __CAT_ranking_set_attribute(){}

function __CAT_ranking_display(parent_identifier, numchildren){
	var output_html = "";
	if (parent_identifier==-1 || parent_identifier+""=="undefined"){
		parent_identifier	= this.root;
	}
	if (numchildren+""=="undefined"){
		numchildren = this.root_count
	}
	var c=0;
	if(parent_identifier == this.root){
		output_html += "<tr>";
		output_html += "<td colspan='3'><a href='javascript:"+this.name+".execute(\"CATEGORYADMIN_ADD\",\"-1\");' class='bt' style='width:180px;text-align:center'>Add New Sub-Category</a></td>";
		output_html += "</tr>";
//		output_html += "<tr>";
//		output_html += "<td colspan='3'><a href='javascript:"+this.name+".set_sort();'>Alphabetic Sort</a></td>";
//		output_html += "</tr>";
	}
	for(var i=0; i<this.list.length; i++){
		id  = this.list[i]["item"];
		if(parent_identifier == this.list[i]["parent"]){
			c++;
			output_html += "<tr class='lineit'>";
			output_html += "<td width='20px' valign='top' class='tablecell'";
			if (this.list[i]["has_child"]>0){
				output_html += " rowspan='2'";
			}
			output_html += " ><input type='hidden' name='ranking[]' value='"+this.list[i]["parent"]+","+id+"'/><img src='/libertas_images/general/iconification/";
			if (this.list[i]["has_child"]>0){
				output_html += "folderopen";
			} else {
				output_html += "item";
			}
			output_html += ".gif'></td>";
			output_html += "<td class='lineit'>"+this.list[i]["label"]+"</td>";
			output_html += "<td align='right' class='lineit' style='margin-right:0px;padding-right:0px;' >";
			if(this.sortorder==1){
				if(c==1){
					output_html += "<span class='ghost' style='width:60px;text-align:center;text-decoration:none;border:1px solid white'>Up</span>&nbsp;";
				} else {
					output_html += "<a class='bt' style='width:60px;text-align:center;text-decoration:none;' href='javascript:"+this.name+".move(\"Up\",\""+id+"\");'>Up</a>&nbsp;";
				}
				if(c==numchildren){
					output_html += "<span class='ghost' style='width:60px;text-align:center;text-decoration:none;border:1px solid white;'>Down</span>&nbsp;";
				} else {
					output_html += "<a class='bt' style='width:60px;text-align:center;text-decoration:none;' href='javascript:"+this.name+".move(\"Down\",\""+id+"\");'>Down</a>&nbsp;";
				}
			}
			output_html += "<a class='bt' style='width:120px;text-align:center;text-decoration:none;' href='javascript:"+this.name+".execute(\"CATEGORYADMIN_ADD\",\""+id+"\");'>Add Sub-Category</a>&nbsp;";
			output_html += "<a class='bt' style='width:120px;text-align:center;text-decoration:none;' href='javascript:"+this.name+".execute(\"CATEGORYADMIN_EDIT\",\""+id+"\");'>Edit Category</a>&nbsp;";
			output_html += "";
			if (this.list[i]["has_child"]>0){
				output_html += "<span style='width:120px;text-align:center;text-decoration:none;' class='ghost'>Remove Category</span>";
			} else {
				output_html += "<a class='bt' style='width:120px;text-align:center;text-decoration:none;' href='javascript:"+this.name+".execute(\"CATEGORYADMIN_REMOVE\",\""+id+"\");'>Remove Category</a>";
			}
			output_html += "</td></tr>";
			if(this.list[i]["has_child"]>0){
				output_html += this.display(id,this.list[i]["has_child"]);
			}
		}
	}
	if (parent_identifier==this.root){
		this.output.innerHTML = "<table width='100%' border='0' cellspacing='0' cellpadding='0'>"+output_html+"</table>";
	} else {
		return "<tr><td colspan='2' style='padding:0px;'><table width='100%' border='0' cellspacing='0' cellpadding='3'>"+output_html+"</table></td></tr>";
	}
}

function __CAT_ranking_add(parent_id, item_id, item_label, has_child){
	var myItem = new Array();
		myItem["parent"]		= parent_id;
		myItem["item"]			= item_id;
		myItem["label"]			= item_label;
		myItem["has_child"]		= has_child;
	this.list[this.list.length] = myItem;
}

function __CAT_insertdata(){}
function __CAT_file_remover(){}
function __CAT_generate_hidden(){}
function __CAT_show(){}
function __CAT_cancel(){}
function __CAT_property(){}
function __CAT_propertyUpdate(){}
function __CAT_tidy(){}
function __CAT_alphabetic(){
	if (this.form.cat_ranking[0].checked){
		if(confirm("You have requested to change the ranking of this category list to alphabetic continuing will lose any previous ranking you have defined\n\nAre you sure?")){
			this.sortorder = 0;
			this.sort_entries(this.root);		
		}
	} else {
			this.sortorder = 1;
			this.display();
	}
}
function __CAT_move(cmd, id){
	var myItem = new Array();
		myItem["parent"]		= "";
		myItem["item"]			= "";
		myItem["label"]			= "";
		myItem["has_child"]		= "";
	var src_item	= -1;
	var prev_item				= -1;
	var next_item				= -1;
	for(var i=0; i<this.list.length; i++){
		if (this.list[i]["item"]==id){
			src_item				=  i;
			c						=  0;
			for(var z=0; z<this.list.length; z++){
//				alert(this.list[z]["parent"] +"=="+ this.list[i]["parent"]);
				if (this.list[z]["parent"] == this.list[i]["parent"]){	
					if (this.list[z]["item"] != id && c == 0){
						prev_item = z;
					}
					if (this.list[z]["item"] == id){
						c=1;
					}
					if (this.list[z]["item"] != id && c==1){
						next_item = z;
						c=2;
					}
				}
			}
		}
	}
	myItem["parent"]		= this.list[src_item]["parent"];
	myItem["item"]			= this.list[src_item]["item"];
	myItem["label"]			= this.list[src_item]["label"];
	myItem["has_child"]		= this.list[src_item]["has_child"]
	if(cmd=="Up"){
		this.list[src_item]["parent"] 		= this.list[prev_item]["parent"]
		this.list[src_item]["item"]			= this.list[prev_item]["item"];
		this.list[src_item]["label"]		= this.list[prev_item]["label"];
		this.list[src_item]["has_child"]	= this.list[prev_item]["has_child"];
		this.list[prev_item]["parent"]		= myItem["parent"];
		this.list[prev_item]["item"]		= myItem["item"];
		this.list[prev_item]["label"]		= myItem["label"];
		this.list[prev_item]["has_child"]	= myItem["has_child"];
	} else {
		this.list[src_item]["parent"] 		= this.list[next_item]["parent"]
		this.list[src_item]["item"]			= this.list[next_item]["item"];
		this.list[src_item]["label"]		= this.list[next_item]["label"];
		this.list[src_item]["has_child"]	= this.list[next_item]["has_child"];
		this.list[next_item]["parent"]		= myItem["parent"];
		this.list[next_item]["item"]		= myItem["item"];
		this.list[next_item]["label"]		= myItem["label"];
		this.list[next_item]["has_child"]	= myItem["has_child"];
	}
	this.display(this.root,this.root_count);
}


function __CAT_execute(next,id){
	var f 			= get_form();
	f.next.value	= next;
	f.cat.value		= id;
	f.submit();
}

function __CAT_sort(parent){
	var ok = 1;
	var previous = -1;
	var myItem= new Array();
	for(i=0;i<this.list.length;i++){
		if (parent == this.list[i]["parent"]){
			if(previous!=-1){
				cmp = this.compair(this.list[i]["label"],this.list[previous]["label"]);
				if(cmp==-1){
					// only move if comes before
					myItem["parent"]					= this.list[i]["parent"];
					myItem["item"]						= this.list[i]["item"];
					myItem["label"]						= this.list[i]["label"];
					myItem["has_child"]					= this.list[i]["has_child"];
					this.list[i]["parent"] 				= this.list[previous]["parent"]
					this.list[i]["item"]				= this.list[previous]["item"];
					this.list[i]["label"]				= this.list[previous]["label"];
					this.list[i]["has_child"]			= this.list[previous]["has_child"];
					this.list[previous]["parent"]		= myItem["parent"];
					this.list[previous]["item"]		= myItem["item"];
					this.list[previous]["label"]		= myItem["label"];
					this.list[previous]["has_child"]	= myItem["has_child"];
					ok =0;
				} else {
				}
			} 
			previous = i;
			if (this.list[i]["has_child"]>0){
				this.sort_entries(this.list[i]["item"]);
			}
		}
	}
	if(ok==0){
		this.sort_entries(parent);
	}
	if (parent == this.root){
		this.display();
	}
}


function __CAT_sort_a2z(item1, item2){
	item1 = item1.toLowerCase(); 
	item2 = item2.toLowerCase();
	if (item1 < item2){
		return -1;
	} else if (item1 > item2){
		return 1;
	} else {
		return 0;
	}
}

function update_category_sorting_group(t,d){
	mycatlist.set_sort();
}