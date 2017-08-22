var title_defined =0;
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Javascript Object to manage the ranking of content
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function RankingFile(order){
	this.list		= new Array();
	this.optionArray= new Array();
	this.report		= "normal";
	this.sortorder	= order;
	this.set		= ranking_set_attribute;
	this.draw		= ranking_display;
	this.newOption	= ranking_newOption;
	this.add		= ranking_add;
	this.insertdata = ranking_file_add_new;
	this.gen_hidden = generate_hidden;
}

function RankFile(){
	this.title		= "";
	this.icon		= "";
	this.identifier = -1;
	this.menu 		= -1;
	this.rank 		= -1;
	this.update		= 0;
}

function ranking_newOption(command, label, linkinfo, return_command){
	index = this.optionArray.length
	this.optionArray[index] 				= {};
	this.optionArray[index].command 		= command;
	this.optionArray[index].label 			= label;
	this.optionArray[index].linkinfo 		= linkinfo;
	this.optionArray[index].return_command	= return_command;
}
/*
"Zenith Learning", 
1915, 
'1', 
"/libertas_images/icons/mime-images/tif.gif",
"100",
"100",
"16 kb",
"f37e5da682cc1c2cd7afc20220ae30b7",
".gif"
*/
function ranking_add(title, identifier, menu, icon, width, height, size, md5, extension, rank){
	i = this.list.length;
	this.list[i] = new RankFile();
	this.set(i, "title", 		title);
	this.set(i, "identifier", 	identifier);
	this.set(i, "menu", 		menu);
	this.set(i, "icon",			icon);
	this.set(i, "width", 		width);
	this.set(i, "height",	 	height);
	this.set(i, "size",			size);
	this.set(i, "md5",			md5);
	this.set(i, "extension", 	extension);
	if (rank+''!='undefined'){
		this.set(i, "rank", rank);
	} else {
		this.set(i, "rank", i);
	}
}

function ranking_set_attribute(index, attribute_name, attribute_value){
	try{
		if (this.list[index]+'' != 'undefined'){
			switch(attribute_name){
				case 'title':
					this.list[index].title		= attribute_value;
					break;
				case 'identifier':
					this.list[index].identifier = attribute_value;
					break;
				case 'menu':
					this.list[index].menu		= attribute_value;
					break;
				case 'rank':
					this.list[index].rank		= attribute_value;
					break;
				case 'titlePage':
					this.list[index].titlePage	= attribute_value;
					break;
				case 'icon':
					this.list[index].icon		= attribute_value;
					break;
				case 'update':
					this.list[index].update		= attribute_value;
					break;
				case 'width':
					this.list[index].width		= attribute_value;
					break;
				case 'height':
					this.list[index].height		= attribute_value;
					break;
				case 'md5':
					this.list[index].md5		= attribute_value;
					break;
				case 'size':
					this.list[index].size		= attribute_value;
					break;
				case 'extension':
					this.list[index].extension	= attribute_value;
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

function ranking_display(field){
	var OptionData	= "";
	var str			= "";
	if (this.optionArray.length!=0){
		for( i = 0 ; i < this.optionArray.length ; i++){
			OptionData += "<li><a href=\"javascript:retrieve_data('"+this.optionArray[i].command+"','"+this.optionArray[i].linkinfo+"','display_note_"+this.optionArray[i].linkinfo+"','"+this.optionArray[i].return_command+"')\"><span class='icon'><span class='text'>"+this.optionArray[i].label+"</span></span></a></li>";
		}
	}
	if(OptionData!=""){
		OptionData = "<ul class='button'>"+OptionData+"</ul>";
	}
	if (this.list.length!=0){
		str = "<tr>";
		if (this.sortOrder == 0){
			str += "<td><strong>Icon</strong></td>";
		}
		str += "<td colspan='2'><strong>Title</strong></td>";
		if(this.report=="detailed"){
			str += "<td><strong>Width</strong></td>";
			str += "<td><strong>Height</strong></td>";
			str += "<td><strong>Size</strong></td>";
		}
		str += "<td colspan='3'><strong>Options</strong></td>";
		str += "</tr>";
		c=0;
		previous = "";
		for (i = 0;i<this.list.length;i++){
			if (c % 2 ==1 ){
				bgcolor="#ffffff";
			} else {
				bgcolor="#ebebeb";
			}
			if(c==0 && this.list.length>1){
				options = "<td>&nbsp;</td><td><a href='javascript:move("+i+", "+(i+1)+")'>Down</a></td>";
			} else {
				if (this.list.length==1){
					options = "<td>&nbsp;</td><td>&nbsp;</td>";
				} else if (c==this.list.length-1){
					options = "<td><a href='javascript:move("+i+", "+(i-1)+")'>Up</a></td><td>&nbsp;</td>";
				} else {
					options = "<td><a href='javascript:move("+i+", "+(i-1)+")'>Up</a></td><td><a href='javascript:move("+i+", "+(i+1)+")'>Down</a></td>";
				}
			}
			options += "<td><a href='javascript:file_remover("+i+")'>Remove</a></td>";
			column = "<td><img src='"+this.list[i].icon+"' border='0' />";
			column += "<input type='hidden' name='id[]' value='"+ this.list[i].identifier +"'/>";
			column += "<input type='hidden' name='rank[]' value='"+ this.list[i].rank +"'/>";
			column += "</td>";
			if(this.report=="normal"){
				str += "<tr bgcolor='"+bgcolor+"'>" + column + "<td>" + this.list[i].title + "</td>" + options + "</tr>";
			} else {
				str += "<tr bgcolor='"+bgcolor+"'>" + column ; 
				str += 		"<td>" + this.list[i].title + "</td>";
				str += 		"<td>" + this.list[i].width + " px</td>";
				str += 		"<td>" + this.list[i].height + " px</td>";
				str += 		"<td>" + this.list[i].size + "</td>";
				str += 		options;
				str += "</tr>";
				str += "<tr>"; 
				str += 		"<td colspan='8'><img src='uploads/" + this.list[i].md5 + "" + this.list[i].extension + "'></td>";
				str += "</tr>";
			}
			c++;
		}

		document.getElementById("display_list_of_files").innerHTML =OptionData+"<input type='hidden' name='number_of' value='"+c+"'/><table cellpadding='3' cellspacing='0' border='0' width='400px'>"+str+"</table>";
	} else {
	document.getElementById("display_list_of_files").innerHTML =OptionData+"<input type='hidden' name='number_of' value='0'/>";
	}
	this.gen_hidden();
}

function move(src, dst, move_rank){
	tmp_title 					= rankfiles.list[src].title;
	tmp_identifier 				= rankfiles.list[src].identifier;
	tmp_menu 					= rankfiles.list[src].menu;
	tmp_titlePage 				= rankfiles.list[src].titlePage;
	if (move_rank+'' != 'undefined'){
		tmp_rank				= rankfiles.list[src].rank;
	}
	tmp_icon					= rankfiles.list[src].icon;
	tmp_titlePage				= rankfiles.list[src].titlePage;

	rankfiles.list[src].title		= rankfiles.list[dst].title;
	rankfiles.list[src].identifier	= rankfiles.list[dst].identifier;
	rankfiles.list[src].menu		= rankfiles.list[dst].menu;
	rankfiles.list[src].titlePage	= rankfiles.list[dst].titlePage;
	if (move_rank+'' != 'undefined'){
		rankfiles.list[src].rank	= rankfiles.list[dst].rank;
	}
	rankfiles.list[src].icon		= rankfiles.list[dst].icon;
	rankfiles.list[src].titlepage	= rankfiles.list[dst].titlePage;

	rankfiles.list[dst].title 		= tmp_title;
	rankfiles.list[dst].identifier	= tmp_identifier;
	rankfiles.list[dst].menu		= tmp_menu;
	rankfiles.list[dst].titlePage 	= tmp_titlePage;
	if (move_rank+'' != 'undefined'){
		rankfiles.list[dst].rank	= tmp_rank;
	}
	rankfiles.list[dst].icon		= tmp_icon;
	rankfiles.list[dst].titlepage	= tmp_titlePage;
	rankfiles.draw();
}

function setTitle(index){
	for (i=0; i<rankfiles.list.length;i++){
		rankfiles.list[i].titlePage = 0;
	}
	rankfiles.list[index].titlePage = 1;
	rankfiles.draw();
}


function bubble(by){
/*	rankfiles.sortOrder = by.selectedIndex
	if (by.selectedIndex==0){
		rankfiles.list.sort(bubblesort_rank);
	}
	if (by.selectedIndex==1){
		rankfiles.list.sort(bubblesort_date_oldest);
	}
	if (by.selectedIndex==2){
		rankfiles.list.sort(bubblesort_a2z);
	}
	if (by.selectedIndex==3){
		rankfiles.list.sort(bubblesort_z2a);
	}
	if (by.selectedIndex==4){
		rankfiles.list.sort(bubblesort_date_newest);
	}
	*/
//	rankfiles.draw();
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

function ranking_redraw(t){
	for (i=0;i<rankfiles.list.length;i++){
		rankfiles.list[i].titlePage = 0;
	}
	if (t.selectedIndex!=0)
		rankfiles.list[t.selectedIndex-1].titlePage = 1;
	rankfiles.draw();
}

function getMonthName(mth){
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

function emptyall(){
	this.list = new Array();
}
//[business-woman-small.jpg::tif::122:1234567890:Design 1::tif::66:1234567890:Design 1b::tif::67:1234567890:top_left.gif::tif::120]

function ranking_files_insertdata(str){
	if (str!=""){
		file_array 		= str.split(":1234567890:");
		/* Modified by Ali Imran*/
		rankfiles.list = new Array();
		/*End Modifications by Ali Imran*/
		for(i = 0 ; i < file_array.length ; i++){
			//sdf::lsl::1937::16::16::824 bytes::4d85fd8c5c0efb3d8b8580d67626e5f2::
			ok_add = true
			file_info	= file_array[i].split("::");
			for (z=0 ; z < rankfiles.list.length ; z++){
				if (rankfiles.list[z].identifier+'' == ''+file_info[2]){
					ok_add = false
					rankfiles.list[z].update = 1;
				}
			}
			if (ok_add){
				icon 		= '/libertas_images/icons/mime-images/'+file_info[1]+'.gif';
				rankfiles.insertdata(file_info[0], icon, file_info[2], file_info[3], file_info[4], file_info[5], file_info[6], file_info[7]);
			}
		}
		file_tidyup();
		rankfiles.draw();
	} else {
		rankfiles.list.length=0;
		file_tidyup();
		rankfiles.draw();
	}
}

function ranking_file_add_new(title, icon, identifier, width, height, size, md5, extension){
	//alert("[title, "+title+"][icon, "+icon+"][identifier, "+identifier+"]");
	insert_index = this.list.length;
	this.list[insert_index] = new RankFile();
	this.set(insert_index, "title",			title);
	this.set(insert_index, "icon",			icon);
	this.set(insert_index, "rank",			insert_index);
	this.set(insert_index, "identifier", 	identifier);
	this.set(insert_index, "update",		1);
	this.set(insert_index, "width", 		width);
	this.set(insert_index, "height",	 	height);
	this.set(insert_index, "size", 			size);
	this.set(insert_index, "md5",			md5);
	this.set(insert_index, "extension", 	extension);
}

function file_remover(index){
	for (i=index; i < rankfiles.list.length-1; i++){
		rankfiles.list[i] = rankfiles.list[i+1];
		rankfiles.list[i].rank --;
	}
	rankfiles.list.length --;
	rankfiles.draw();
	
}

function file_tidyup(){
	for (i=0; i < rankfiles.list.length; i++){
		if (rankfiles.list[i].update == 0) {
			file_remover(i);
		} else {
			rankfiles.list[i].update = 0;
		}
	}
	rankfiles.draw();
}

function generate_hidden(){
	f = get_form();
	f.elements["file_associations"].value='';
	for (i=0; i < rankfiles.list.length; i++){
		f.elements["file_associations"].value += rankfiles.list[i].identifier+',';
	}

}