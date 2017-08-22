var title_defined =0;
var target_position = -1;
var rEntries = new RankingEntries(0);
function GenerateRankingEntries(list_of_associated_entries, field){
	rEntries.file_output = field;
	for(var i = 0 ; i < list_of_associated_entries.length ; i++ ){
		rEntries.add(
			list_of_associated_entries[i][0], 
			list_of_associated_entries[i][1],
			list_of_associated_entries[i][2],
			list_of_associated_entries[i][3],
			list_of_associated_entries[i][4],
			list_of_associated_entries[i][5],// title
			i);
	}
	rEntries.draw();
}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Javascript Object to manage the ranking of content
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function RankingEntries(order){
	this.list					= new Array();
	this.file_output			= "entry_associate_values";
	this.displaylistoutput		= "div_associated_entries";
	this.key					= "";
	this.position				= -1;
	this.sortorder				= order;
	
	this.set					= __ider_ranking_set_attribute;
	this.draw					= __ider_ranking_display;
	this.add					= __ider_ranking_add;
	this.insertdata 			= __ider_ranking_file_add_new;
	this.gen_hidden 			= __ider_generate_hidden;
	this.retrieve 				= __ider_retrieve_info;
	this.moveitem				= __ider_move
	this.tidyup					= __ider_myfile_tidyup;
	this.remover				= __ider_file_remover;
}

function RankEntries(){
	this.identifier = -1;
	this.title		= "";
	this.linktype 	= 0;
	this.rank 		= -1;
	this.update		= 0;
	this.src_id		= 0;
	this.dst_id 	= 0;
	this.src_cat	= 0;
	this.dst_cat 	= 0;
}

function __ider_ranking_add(identifier, sid, scat, did, dcat, title, rank){
	i = this.list.length;
	this.list[i] = new RankEntries();
	this.set(i, "title", title);
	this.set(i, "identifier", identifier);
	if (rank+''!='undefined'){
		this.set(i, "rank", rank);
	} else {
		this.set(i, "rank", i);
	}
//	this.set(i,"linktype",linktype)
	this.set(i,"src_id",	sid);
	this.set(i,"dst_id",	scat);
	this.set(i,"src_cat",	did);
	this.set(i,"dst_cat",	dcat);
}

function __ider_ranking_set_attribute(index, attribute_name, attribute_value){
	try{
		if (this.list[index]+'' != 'undefined'){
			switch(attribute_name){
				case 'title':
					this.list[index].title		= attribute_value;
					break;
				case 'identifier':
					this.list[index].identifier = attribute_value;
					break;
				case 'src_id':
					this.list[index].src_id		= attribute_value;
					break;
				case 'dst_id':
					this.list[index].dst_id 	= attribute_value;
					break;
				case 'src_cat':
					this.list[index].src_cat	= attribute_value;
					break;
				case 'dst_cat':
					this.list[index].dst_cat 	= attribute_value;
					break;
				case 'linktype':
					this.list[index].linktype	= attribute_value;
					break;
				case 'rank':
					this.list[index].rank		= attribute_value;
					break;
				case 'titlePage':
					this.list[index].titlePage	= attribute_value;
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
		alert("["+e.message+"]");
	}
}

function __ider_ranking_display(field){
	if (this.list.length!=0){
		var str = "<tr>";
		str += "<td><strong></strong></td></tr>";
		c=0;
		previous = "";
		for (i = 0;i<this.list.length;i++){
			if (c % 2 ==1 ){
				bgcolor="#ffffff";
			} else {
				bgcolor="#ebebeb";
			}
			if(c==0 && this.list.length>1){
				options = "<td></td><td><a href='javascript:rEntries.moveitem("+i+", "+(i+1)+")'>Down</a></td>";
			} else {
				if (this.list.length==1){
					options = "<td></td><td></td>";
				} else if (c==this.list.length-1){
					options = "<td><a href='javascript:rEntries.moveitem("+i+", "+(i-1)+")'>Up</a></td><td></td>";
				} else {
					options = "<td><a href='javascript:rEntries.moveitem("+i+", "+(i-1)+")'>Up</a></td><td><a href='javascript:rEntries.moveitem("+i+", "+(i+1)+")'>Down</a></td>";
				}
			}
			options += "<td><a href='javascript:rEntries.remover("+i+")'>Remove</a></td>";
			column = "<input type='hidden' name='er_id[]' value='"+ this.list[i].identifier +"'/>";
			column += "<input type='hidden' name='er_rank[]' value='"+ this.list[i].rank +"'/>";
			column += "<input type='hidden' name='er_src_id[]' value='"+ this.list[i].src_id +"'/>";
			column += "<input type='hidden' name='er_src_cat[]' value='"+ this.list[i].src_cat +"'/>";
			column += "<input type='hidden' name='er_dst_id[]' value='"+ this.list[i].dst_id +"'/>";
			column += "<input type='hidden' name='er_dst_cat[]' value='"+ this.list[i].dst_cat +"'/>";
			str += "<tr bgcolor='"+bgcolor+"'><td>" + column +  this.list[i].title + "</td>" + options + "</tr>";
			c++;
		}
		str  = "<input type='hidden' name='er_number_of' value='"+c+"'/><table cellpadding='3' cellspacing='0' border='0' width='100%'>"+str+"</table>";
		str += "<ul>";
		cmd = "INFORMATIONADMIN_DIRECTORY_LIST";
		msg="Select Entries";
		alert("here");
		str += "	<li><a href=\"javascript:rEntries.retrieve('"+cmd+"', 'file_associations_"+this.key+"', 'display_note_trans_file_associations', 'FILES_LIST_FILE_DETAIL')\">"+msg+"</a></li>";
		str += "</ul>";
		str += "";
		document.getElementById(this.displaylistoutput).innerHTML =str;
	} else {
		var str  = "";
		str += "<input type='hidden' name='number_of' value='0'/>";
		str += "<ul>";
		if (this.key=="ie_image"){
			cmd = "FILES_LIST_IMAGES";
		} else {
			cmd = "FILES_LIST";
		}
		if(this.displaylistoutput.indexOf("ie_image")!=-1){
			msg="Select Image";
		} else {
			msg="Add to List";
		}
		str += "	<li><a href=\"javascript:rEntries.retrieve()\">"+msg+"</a></li>";
		str += "</ul>";
		str += "";
		document.getElementById(this.displaylistoutput).innerHTML =str;
	}
	this.gen_hidden();
}

function __ider_move(src, dst, move_rank){
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
		this.list[dst].rank		= tmp_rank;
	}
	this.list[dst].icon			= tmp_icon;
	this.list[dst].titlepage	= tmp_titlePage;
	this.draw();
}

function __ider_setTitle(index){
	for (i=0; i<this.list.length;i++){
		this.list[i].titlePage = 0;
	}
	this.list[index].titlePage = 1;
	this.draw();
}


function bubble(by){
/*	this.sortOrder = by.selectedIndex
	if (by.selectedIndex==0){
		this.list.sort(bubblesort_rank);
	}
	if (by.selectedIndex==1){
		this.list.sort(bubblesort_date_oldest);
	}
	if (by.selectedIndex==2){
		this.list.sort(bubblesort_a2z);
	}
	if (by.selectedIndex==3){
		this.list.sort(bubblesort_z2a);
	}
	if (by.selectedIndex==4){
		this.list.sort(bubblesort_date_newest);
	}
	*/
//	this.draw();
}

function bubblesort_rank(item1, item2){
	if (item1.rank < item2.rank){
		return -1;
	} else if (item1.rank > item2.rank){
		return 1;
	} else {
		return 0;
	}
}
function bubblesort_a2z(item1, item2){
	if (item1.title < item2.title){
		return -1;
	} else if (item1.title > item2.title){
		return 1;
	} else {
		return 0;
	}
}
function bubblesort_z2a(item1, item2){
	if (item1.title > item2.title){
		return -1;
	} else if (item1.title < item2.title){
		return 1;
	} else {
		return 0;
	}
}
function bubblesort_date_oldest(item1, item2){
	if (item1.icon < item2.icon){
		return -1;
	} else if (item1.icon > item2.icon){
		return 1;
	} else {
		return 0;
	}
}
function bubblesort_date_newest(item1, item2){
	if (item1.icon > item2.icon){
		return -1;
	} else if (item1.icon < item2.icon){
		return 1;
	} else {
		return 0;
	}
}

function __ider_ranking_redraw(t){
	for (i=0;i<this.list.length;i++){
		this.list[i].titlePage = 0;
	}
	if (t.selectedIndex!=0)
		this.list[t.selectedIndex-1].titlePage = 1;
	this.draw();
}

function __ider_getMonthName(mth){
	mth = mth*1;
	switch(mth){
		case 1:
			return "Jan"
			break;
		case 2:
			return "Feb"
			break;
		case 3:
			return "Mar"
			break;
		case 4:
			return "Apr"
			break;
		case 5:
			return "May"
			break;
		case 6:
			return "Jun"
			break;
		case 7:
			return "Jul"
			break;
		case 8:
			return "Aug"
			break;
		case 9:
			return "Sep"
			break;
		case 10:
			return "Oct"
			break;
		case 11:
			return "Nov"
			break;
		case 12:
			return "Dec"
			break;
		default:
	}
}

function __ider_emptyall(){
	this.list = new Array();
}
//[business-woman-small.jpg::tif::122:1234567890:Design 1::tif::66:1234567890:Design 1b::tif::67:1234567890:top_left.gif::tif::120]

function ranking_files_insertdata(str){
	if (str!=""){
		file_array 		= str.split(":1234567890:");
		len =file_array.length;
		if(this.key=='ie_image'){
			len=1;	
		}
		for(i = 0 ; i < len ; i++){
			ok_add = true
			file_info	= file_array[i].split("::");
			for (z=0 ; z < rEntries.list.length ; z++){
				if (rEntries.list[z].identifier+'' == ''+file_info[2]){
					ok_add = false
					rEntries.list[z].update = 1;
				}
			}
			if (ok_add){
				icon = '/libertas_images/icons/mime-images/'+file_info[1]+'.gif';
				rEntries.insertdata(file_info[0], icon, file_info[2]);
			}
		}
		rEntries.tidyup();
		rEntries.draw();
	} else {
		rEntries.list.length=0;
		rEntries.tidyup();
		rEntries.draw();
	}
}

function __ider_ranking_file_add_new(title, icon, identifier){
	//alert("[title, "+title+"][icon, "+icon+"][identifier, "+identifier+"]");
	insert_index = this.list.length;
	this.list[insert_index] = new RankEntries();
	this.set(insert_index, "title", title);
	this.set(insert_index, "icon", icon);
	this.set(insert_index, "rank", insert_index);
	this.set(insert_index, "identifier", identifier);
	this.set(insert_index, "update",1);
}

function __ider_file_remover(index){
	for (i=index; i < this.list.length-1; i++){
		this.list[i] = this.list[i+1];
		this.list[i].rank --;
	}
	this.list.length --;
	this.draw();
	
}

function __ider_file_tidyup(){
	for (i=0; i < this.list.length; i++){
		if (this.list[i].update == 0) {
			file_remover(i);
		} else {
			this.list[i].update = 0;
		}
	}
	this.draw();
}

function __ider_myfile_tidyup(){
	for (i=0; i < this.list.length; i++){
		if (this.list[i].update == 0) {
			this.remover(i);
		} else {
			this.list[i].update = 0;
		}
	}
	this.draw();
}

function __ider_generate_hidden(){
	f = get_form();
	f.elements[this.file_output].value = '';
	for (i=0; i < this.list.length; i++){
		f.elements[this.file_output].value += this.list[i].identifier+',';
	}

}

function __ider_retrieve_info(){
	var f = get_form();
	target_position = this.position;
	Alist = f[this.file_output].value;
	if(this.key=='ie_image'){
		Alist="";
	}
	try{
		PATH = "http://"+domain+base_href+'admin/file_associate.php?command='+cmd+'&amp;'+session_url+'&amp;return_hidden='+associated+'&amp;return_note='+note+'&amp;associated_list='+Alist+'&amp;return_command='+return_command;
		retrieval_window = window.open(PATH,'PREVIEW_WINDOW','scrollbars=yes,resizable=yes,width=750,height=550');
		retrieval_window.focus();
	} catch(e) {
		alert("Error in opening new window");
	}
}