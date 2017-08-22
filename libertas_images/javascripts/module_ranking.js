var title_defined =0;
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Javascript Object to manage the ranking of content
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function Ranking(order){
	this.list		= new Array();
	this.sortorder	= order;
	this.set		= ranking_set_attribute;
	this.draw		= ranking_display;
	this.add		= ranking_add;
}

function Rank(){
	this.title		= "";
	this.identifier = "";
	this.menu 		= -1;
	this.rank 		= -1;
	this.titlePage	= 0;
}

function ranking_add(title, identifier, menu, rank, myDate, titlePage){
	i = this.list.length;
	this.list[i] = new Rank();
	this.set(i, "title", LIBERTAS_GENERAL_unjtidy(title));
	this.set(i, "identifier", identifier);
	this.set(i, "menu", menu);
	this.set(i, "myDate", myDate);
	if (titlePage==1){
		this.set(i, "rank", 0);
	} else {
		this.set(i, "rank", i+1);
	}
	this.set(i, "titlePage", titlePage);
	if (titlePage!=0){
		title_defined =1;
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
				case 'myDate':
					this.list[index].myDate		= attribute_value;
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
/*	if (title_defined==0){
		this.list[0].titlePage=1;
		title_defined=1;
	}
	*/
//	alert(field +" line 77");
	if (field+''!="undefined"){
		bubble(field);
		this.sortOrder = field.selectedIndex;
	}
	str = "<tr><td colspan='5'>Does this location have a title Page?</td></tr><tr><td colspan='5'>";
	str += "<select name='has_title' onchange='javascript:ranking_redraw(this)'><option value=''>No Title Page</option>"
	for (i = 0;i<this.list.length;i++){
		str += "<option value='" + this.list[i].identifier + "'";
		if (this.list[i].titlePage==1)
			str += " selected";
		str += ">" + this.list[i].title + "</option>";
	}
	str += "</select>";
	str += "</td></tr>";
	str += "<tr>";
	if (this.sortOrder == 0){
		str += "<td><strong>Rank</strong></td>";
	}
	str += "<td><strong>Title</strong></td><td colspan='3'>Option</td></tr>";
	var c=0;
	previous = "";
	has_title=0
	for (i = 0;i<this.list.length;i++){
		if (this.list[i].titlePage==0){
			if (this.sortOrder == 0){
				if (c % 2 ==1 ){
					bgcolor="#ffffff";
				} else {
					bgcolor="#ebebeb";
				}
				if(c==0){
					options = "<td></td><td><a href='javascript:move("+i+", "+(i+1)+")'>Down</a></td>";
				} else {
					if((c==this.list.length-1 && title_defined==0) || (c==this.list.length-2 && title_defined==1)){
						options = "<td><a href='javascript:move("+i+", "+(i-1)+")'>Up</a></td><td></td>";
					} else {
						options = "<td><a href='javascript:move("+i+", "+(i-1)+")'>Up</a></td><td><a href='javascript:move("+i+", "+(i+1)+")'>Down</a></td>";
					}
				}
				column = "<td>"+this.list[i].rank+"";
				column += "<input type='hidden' name='id[]' value='"+ this.list[i].identifier +"'/>";
				column += "<input type='hidden' name='rank[]' value='"+ this.list[i].rank +"'/>";
				column += "</td>";
			} else if (this.sortOrder == 1 || this.sortOrder == 4){
				bgcolor="#ffffff";
				options=""
				column="<input type='hidden' name='id[]' value='"+ this.list[i].identifier +"'/>"
//				alert(this.list[i].myDate+" ["+this.list[i].myDate.substring(8,12)+"]");
				current = (this.list[i].myDate.substring(8,10) * 1 )+", "+getMonthName(this.list[i].myDate.substring(5,7))+", "+this.list[i].myDate.substring(0,4);
				if (previous!=current){
					str += "<tr bgcolor='#ffffff'><td><hr />"+ current +"<hr /></td></tr>";
				}
				previous = current;
			} else {
				bgcolor="#ffffff";
				column="<input type='hidden' name='id[]' value='"+ this.list[i].identifier +"'/>"
				options="";
				current = this.list[i].title.charAt(0);
				if (previous!=current){
					str += "<tr bgcolor='#ffffff'><td><hr />"+ current +"<hr /></td></tr>";
				}
				previous = current;
			}
			str += "<tr bgcolor='"+bgcolor+"'>"+column;
			str += "<td>"+this.list[i].title + "</td>"+ options +"</tr>";
			c++;
		} else {
			str += "<input type='hidden' name='id[]' value='"+ this.list[i].identifier +"'/>";
			str += "<input type='hidden' name='rank[]' value='0'/>";
			has_title=1
		}
	}	
	str="<input type='hidden' name='number_of' value='" + (c + has_title) + "'/><table cellpadding='3' cellspacing='0' border='0'>"+str+"</table>";
	LIBERTAS_GENERAL_printToId("noteArea", str);
}

function move(src, dst, move_rank){
	tmp_title 					= ranks.list[src].title;
	tmp_identifier 				= ranks.list[src].identifier;
	tmp_menu 					= ranks.list[src].menu;
	tmp_titlePage 				= ranks.list[src].titlePage;
	if (move_rank+'' != 'undefined'){
		tmp_rank				= ranks.list[src].rank;
	}
	tmp_myDate					= ranks.list[src].myDate;
	tmp_titlePage				= ranks.list[src].titlePage;

	ranks.list[src].title		= ranks.list[dst].title;
	ranks.list[src].identifier	= ranks.list[dst].identifier;
	ranks.list[src].menu		= ranks.list[dst].menu;
	ranks.list[src].titlePage	= ranks.list[dst].titlePage;
	if (move_rank+'' != 'undefined'){
		ranks.list[src].rank	= ranks.list[dst].rank;
	}
	ranks.list[src].myDate		= ranks.list[dst].myDate;
	ranks.list[src].titlepage	= ranks.list[dst].titlePage;

	ranks.list[dst].title 		= tmp_title;
	ranks.list[dst].identifier	= tmp_identifier;
	ranks.list[dst].menu		= tmp_menu;
	ranks.list[dst].titlePage 	= tmp_titlePage;
	if (move_rank+'' != 'undefined'){
		ranks.list[dst].rank	= tmp_rank;
	}
	ranks.list[dst].myDate		= tmp_myDate;
	ranks.list[dst].titlepage	= tmp_titlePage;
	ranks.draw();
}

function setTitle(index){
	for (i=0; i<ranks.list.length;i++){
		ranks.list[i].titlePage = 0;
	}
	ranks.list[index].titlePage = 1;
	ranks.draw();
}


function bubble(by){
	ranks.sortOrder = by.selectedIndex
	if (by.selectedIndex==0){
		ranks.list.sort(bubblesort_rank);
	}
	if (by.selectedIndex==1){
		ranks.list.sort(bubblesort_date_oldest);
	}
	if (by.selectedIndex==2){
		ranks.list.sort(bubblesort_a2z);
	}
	if (by.selectedIndex==3){
		ranks.list.sort(bubblesort_z2a);
	}
	if (by.selectedIndex==4){
		ranks.list.sort(bubblesort_date_newest);
	}
	ranks.draw();
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
	if (item1.myDate < item2.myDate){
		return -1;
	} else if (item1.myDate > item2.myDate){
		return 1;
	} else {
		return 0;
	}
}
function bubblesort_date_newest(item1, item2){
	if (item1.myDate > item2.myDate){
		return -1;
	} else if (item1.myDate < item2.myDate){
		return 1;
	} else {
		return 0;
	}
}

function ranking_redraw(t){
	for (i=0;i<ranks.list.length;i++){
		ranks.list[i].titlePage = 0;
	}
	if (t.selectedIndex!=0)
		ranks.list[t.selectedIndex-1].titlePage = 1;
	ranks.draw();
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
