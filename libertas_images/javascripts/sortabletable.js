addEvent(window, "load", sortables_init);
var SORT_COLUMN_INDEX = "";
var UNIQUE_ID_FOR_TABLE = new Date().getTime();
var total_number_of_sortable_tables_on_this_page =0;

function sortables_init() {
    // Find all tables with class sortable and make them sortable
	if (!document.getElementsByTagName) return;
    tbls = document.getElementsByTagName("table");
    for (ti=0;ti<tbls.length;ti++) {
        thisTbl = tbls[ti];
        if (''+thisTbl.className+''=="sortable") {
			if (thisTbl.id+"" == "undefined"){
				total_number_of_sortable_tables_on_this_page++;
				tbls[ti].id = "table-"+UNIQUE_ID_FOR_TABLE+"-"+total_number_of_sortable_tables_on_this_page;
				thisTbl.id  = "table-"+UNIQUE_ID_FOR_TABLE+"-"+total_number_of_sortable_tables_on_this_page;
				thisTbl.width  = "100%";
			}
            //initTable(thisTbl.id);
            ts_makeSortable(thisTbl);
        }
    }
}

function ts_makeSortable(table) {
    if (table.rows && table.rows.length > 0) {
        var firstRow = table.rows[0];
    }
    if (!firstRow) return;
    
    // We have a first row: assume it's the header, and make its contents clickable links
    for (var i=0;i<firstRow.cells.length;i++) {
        var cell = firstRow.cells[i];
        var txt = ts_getInnerText(cell);
		if(txt=="#"){
			cell.width='30px'
		} 
//		<img style="float:right" src="/libertas_images/themes/1x1.gif" alt="" width="10" height="11"/>
        cell.innerHTML = '<a href="#" class="sortheader" onclick="ts_resortTable(this);return false;"><span class="sortarrow"><span class="text">'+txt+'</span></span></a>';
    }
	//colorTable
	ts_colorTable(table);
}

function ts_getInnerText(el) {
	if (typeof el == "string") return el;
	if (typeof el == "undefined") { return el };
	if (el.innerText) return el.innerText;	//Not needed but it is faster
	var str = "";
	
	var cs = el.childNodes;
	var l = cs.length;
	for (var i = 0; i < l; i++) {
		switch (cs[i].nodeType) {
			case 1: //ELEMENT_NODE
				str += ts_getInnerText(cs[i]);
				break;
			case 3:	//TEXT_NODE
				str += cs[i].nodeValue;
				break;
		}
	}
	return str;
}

function ts_resortTable(lnk) {
    // get the span
    var span;
    for (var ci=0;ci<lnk.childNodes.length;ci++) {
        if (lnk.childNodes[ci].tagName && lnk.childNodes[ci].tagName.toLowerCase() == 'span') span = lnk.childNodes[ci];
    }
    var spantext = ts_getInnerText(span);
    var td = lnk.parentNode;
    var column = td.cellIndex;
    var table = getParent(td,'TABLE');
    
    // Work out a type for the column
    if (table.rows.length <= 1) return;
    var itm = ts_getInnerText(table.rows[1].cells[column]);
/*    
    if (itm.match(/^(sun,|mon,|tue,|wed,|thu,|fri,|sat,) \d{2}\s{1}(Jan|Feb|Mar|Apr|May|Jun|Jul|Apr|Sep|Oct|Nov|Dec)\s{1}\d{4}$/i)) alert('eassdf');//Mon, 03 Oct 2007

    if (itm.match(/^((31(?!\\ (Feb(ruary)?|Apr(il)?|June?|(Sep(?=\\b|t)t?|Nov)(ember)?)))|((30|29)(?!\\ Feb(ruary)?))|(29(?=\\ Feb(ruary)?\\ (((1[6-9]|[2-9]\\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))|(0?[1-9])|1\\d|2[0-8])\\ (Jan(uary)?|Feb(ruary)?|Ma(r(ch)?|y)|Apr(il)?|Ju((ly?)|(ne?))|Aug(ust)?|Oct(ober)?|(Sep(?=\\b|t)t?|Nov|Dec)(ember)?)\\ ((1[6-9]|[2-9]\\d)\\d{2})$/)) alert('String Date');//D,d M Y

    if (itm.match(/^(3[0-1]|2[0-9]|1[0-9]|0[1-9])[\s{1}|\/|-](Jan|JAN|Feb|FEB|Mar|MAR|Apr|APR|May|MAY|Jun|JUN|Jul|JUL|Aug|AUG|Sep|SEP|Oct|OCT|Nov|NOV|Dec|DEC)[\s{1}|\/|-]\d{4}$/)) alert('String Date2');//01 JAN 2003|||31/Dec/2002|||20-Apr-2003

*/
    sortfn = ts_sort_caseinsensitive;
    if (itm.match(/^\d\d[\/-]\d\d[\/-]\d\d\d\d$/)) sortfn = ts_sort_date;
    if (itm.match(/^\d\d[\/-]\d\d[\/-]\d\d$/)) sortfn = ts_sort_date;
    if (itm.match(/^[£$€]/)) sortfn = ts_sort_currency;
    if (itm.match(/^[\d\.]+$/)) sortfn = ts_sort_numeric;
	
	/* RegExp (pattern Thu, 20 Dec 2007) for string date to sort (Added By Muhammad Imran) */
    if (itm.match(/^(Sun,|Mon,|Tue,|Wed,|Thu,|Fri,|Sat,) \d{2}\s{1}(Jan|Feb|Mar|Apr|May|Jun|Jul|Apr|Sep|Oct|Nov|Dec)\s{1}\d{4}$/)) 		
		sortfn = ts_sort_str_date;//Thu, 20 Dec 2007

    SORT_COLUMN_INDEX = column;
    var firstRow = new Array();
    var newRows = new Array();
    for (i=0;i<table.rows[0].length;i++) { 
		firstRow[i] = table.rows[0][i]; 
	}
    for (j=1;j<table.rows.length;j++) { 
		//if(table.rows[j].className!="ignore"){
			newRows[j-1] = table.rows[j]; 
		//	alert(table.rows[j].className + "    " + j);
		//}	
	}

    newRows.sort(sortfn);

    if (span.getAttribute("sortdir") == 'down') {
        ARROW = '/libertas_images/general/bullets/sort-arrow-up.gif';
        newRows.reverse();
        span.setAttribute('sortdir','up');
    } else {
        ARROW = '/libertas_images/general/bullets/sort-arrow-down.gif';
        span.setAttribute('sortdir','down');
    }
    
    // We appendChild rows that already exist to the tbody, so it moves them rather than creating new ones
    // don't do sortbottom rows
    for (i=0;i<newRows.length;i++) { 
		//if (!newRows[i].className || (newRows[i].className && (newRows[i].className.indexOf('sortbottom') == -1))) {
		if (!newRows[i].className || (newRows[i].className && (newRows[i].className.indexOf('ignore') == -1))) {		
			table.tBodies[0].appendChild(newRows[i]);
		}
	}
    // do sortbottom rows only
    for (i=0;i<newRows.length;i++) { 	
		//if (newRows[i].className && (newRows[i].className.indexOf('sortbottom') != -1)) {	
		if (newRows[i].className && (newRows[i].className.indexOf('ignore') != -1)) {	
			table.tBodies[0].appendChild(newRows[i]);
		}
	}
    
    // Delete any other arrows there may be showing
    var allspans = document.getElementsByTagName("span");
    for (var ci=0;ci<allspans.length;ci++) {
        if (allspans[ci].className == 'sortarrow') {
            if (getParent(allspans[ci],"table") == getParent(lnk,"table")) { // in the same table as us?
                //allspans[ci].innerHTML = '&nbsp;&nbsp;&nbsp;';
				allspans[ci].style.backgroundImage="none";
            }
        }
    }
        
    span.style.backgroundImage = "url("+ARROW+")";
    span.style.backgroundPosition = "right";
    span.style.backgroundRepeat = "no-repeat";
	ts_colorTable(table);
}

function getParent(el, pTagName) {
	if (el == null) return null;
	else if (el.nodeType == 1 && el.tagName.toLowerCase() == pTagName.toLowerCase())	// Gecko bug, supposed to be uppercase
		return el;
	else
		return getParent(el.parentNode, pTagName);
}
function ts_sort_date(a,b) {
    // y2k notes: two digit years less than 50 are treated as 20XX, greater than 50 are treated as 19XX
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
	//alert('aa:'+bb+'b:'+bb);
    if (aa.length == 10) {
        dt1 = aa.substr(6,4)+aa.substr(3,2)+aa.substr(0,2);
    } else {
        yr = aa.substr(6,2);
        if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
        dt1 = yr+aa.substr(3,2)+aa.substr(0,2);
    }
    if (bb.length == 10) {
        dt2 = bb.substr(6,4)+bb.substr(3,2)+bb.substr(0,2);
    } else {
        yr = bb.substr(6,2);
        if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
        dt2 = yr+bb.substr(3,2)+bb.substr(0,2);
    }
    if (dt1==dt2) return 0;
    if (dt1<dt2) return -1;
    return 1;
}

/* Starts Function to sort string date (RegExp pattern: Thu, 20 Dec 2007) (Function Added By Muhammad Imran) */
function ts_sort_str_date(a,b) {
    // y2k notes: two digit years less than 50 are treated as 20XX, greater than 50 are treated as 19XX
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
	
	dd = aa.substr(5,2);
	mm = aa.substr(8,3);
	yyyy = aa.substr(12,4);
	
	if (mm == 'Jan') mm = '01';
	else if (mm == 'Feb') mm = '02';
	else if (mm == 'Mar') mm = '03';
	else if (mm == 'Apr') mm = '04';
	else if (mm == 'May') mm = '05';
	else if (mm == 'Jun') mm = '06';
	else if (mm == 'Jul') mm = '07';
	else if (mm == 'Aug') mm = '08';
	else if (mm == 'Sep') mm = '09';
	else if (mm == 'Oct') mm = '10';
	else if (mm == 'Nov') mm = '11';
	else if (mm == 'Dec') mm = '12';
	
	aa = dd+'-'+mm+'-'+yyyy;

	dd = bb.substr(5,2);
	mm = bb.substr(8,3);
	yyyy = bb.substr(12,4);
	
	if (mm == 'Jan') mm = '01';
	else if (mm == 'Feb') mm = '02';
	else if (mm == 'Mar') mm = '03';
	else if (mm == 'Apr') mm = '04';
	else if (mm == 'May') mm = '05';
	else if (mm == 'Jun') mm = '06';
	else if (mm == 'Jul') mm = '07';
	else if (mm == 'Aug') mm = '08';
	else if (mm == 'Sep') mm = '09';
	else if (mm == 'Oct') mm = '10';
	else if (mm == 'Nov') mm = '11';
	else if (mm == 'Dec') mm = '12';
	
	bb = dd+'-'+mm+'-'+yyyy;

//	alert('straa:'+aa+'b:'+bb);
	
    if (aa.length == 10) {
        dt1 = aa.substr(6,4)+aa.substr(3,2)+aa.substr(0,2);
    } else {
        yr = aa.substr(6,2);
        if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
        dt1 = yr+aa.substr(3,2)+aa.substr(0,2);
    }
    if (bb.length == 10) {
        dt2 = bb.substr(6,4)+bb.substr(3,2)+bb.substr(0,2);
    } else {
        yr = bb.substr(6,2);
        if (parseInt(yr) < 50) { yr = '20'+yr; } else { yr = '19'+yr; }
        dt2 = yr+bb.substr(3,2)+bb.substr(0,2);
    }
    if (dt1==dt2) return 0;
    if (dt1<dt2) return -1;
    return 1;
}
/* Ends Function to sort string date (RegExp pattern: Thu, 20 Dec 2007) (Function Added By Muhammad Imran) */

function ts_sort_currency(a,b) { 
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).replace(/[^0-9.]/g,'');
    return parseFloat(aa) - parseFloat(bb);
}

function ts_sort_numeric(a,b) { 
    aa = parseFloat(ts_getInnerText(a.cells[SORT_COLUMN_INDEX]));
    if (isNaN(aa)) aa = 0;
    bb = parseFloat(ts_getInnerText(b.cells[SORT_COLUMN_INDEX])); 
    if (isNaN(bb)) bb = 0;
    return aa-bb;
}

function ts_sort_caseinsensitive(a,b) {
	if (typeof a.cells[SORT_COLUMN_INDEX] != "undefined")		
	    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]).toLowerCase();
	else
		aa = null;

	if (typeof b.cells[SORT_COLUMN_INDEX] != "undefined")		
	    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]).toLowerCase();
	else
		bb = null;	
    if (aa==bb) return 0;
    if (aa<bb) return -1;
    return 1;
}

function ts_sort_default(a,b) {
    aa = ts_getInnerText(a.cells[SORT_COLUMN_INDEX]);
    bb = ts_getInnerText(b.cells[SORT_COLUMN_INDEX]);
    if (aa==bb) return 0;
    if (aa<bb) return -1;
    return 1;
}


function addEvent(elm, evType, fn, useCapture)
// addEvent and removeEvent
// cross-browser event handling for IE5+,  NS6 and Mozilla
// By Scott Andrew
{
  if (elm.addEventListener){
    elm.addEventListener(evType, fn, useCapture);
    return true;
  } else if (elm.attachEvent){
    var r = elm.attachEvent("on"+evType, fn);
    return r;
  } else {
    alert("Handler could not be removed");
  }
} 

function ts_colorTable(table){
    if (table.rows && table.rows.length > 0) {
		for(index = 1; index<table.rows.length;index++){
			if(table.rows[index].className != "ignore") {
				if(index % 2 == 1){
					table.rows[index].className ='sortTableRow';
				} else {
					table.rows[index].className ='sortTableRowAlt';
				}
			}	
		}
    }

} 