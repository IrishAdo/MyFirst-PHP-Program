/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   E X T E N D E D . J S 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- The Editor will be split into two files Main.js and Extended.js
	-
	- The file main.js will contain only the code required by the system to generate the editor
	- Once the editor is loaded and initialised the main.js will load the extended.js file.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- DHTML Editor
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- This editor is designed to work only in IE modifications required to work on mozilla.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	author : Adrian Sweeney
	-	Modified $Date: 2004/12/29 14:27:48 $
	-	$Revision: 1.27 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
var currentPos=null;
var CloseTableParameter = null;
var labelHTML = "";
	function LIBERTAS_table_cell_merge_right_click(editor, sender)
	{
		var ct = LIBERTAS_getTable(editor); // current table
		var cr = LIBERTAS_getTR(editor); // current row
		var cd = LIBERTAS_getTD(editor); // current row

		if (cd && cr && ct)
		{
			// get "real" cell position and form cell matrix
			var tm = LIBERTAS_formCellMatrix(ct);
			for (j=0; j<tm[cr.rowIndex].length; j++)
			{
				if (tm[cr.rowIndex][j] == cd.cellIndex)
				{
					realIndex=j;
					break;
				}
			}
			if (cd.cellIndex+1<cr.cells.length)
			{
				ccrs = cd.rowSpan?cd.rowSpan:1;
				cccs = cd.colSpan?cd.colSpan:1;
				ncrs = cr.cells(cd.cellIndex+1).rowSpan?cr.cells(cd.cellIndex+1).rowSpan:1;
				nccs = cr.cells(cd.cellIndex+1).colSpan?cr.cells(cd.cellIndex+1).colSpan:1;
				// check if theres nothing between these 2 cells
				j=realIndex;
				while(tm[cr.rowIndex][j] == cd.cellIndex) j++;
				if (tm[cr.rowIndex][j] == cd.cellIndex+1)
				{
					// proceed only if current and next cell rowspans are equal
					if (ccrs == ncrs)
					{
						// increase colspan of current cell and append content of the next cell to current
						cd.colSpan = cccs+nccs;
						cd.innerHTML += cr.cells(cd.cellIndex+1).innerHTML;
						cr.deleteCell(cd.cellIndex+1);
					}
				}
			}
		}
				
	} // mergeRight


	function LIBERTAS_table_cell_merge_down_click(editor, sender)
	{
		var ct = LIBERTAS_getTable(editor); // current table
		var cr = LIBERTAS_getTR(editor); // current row
		var cd = LIBERTAS_getTD(editor); // current row

		if (cd && cr && ct)
		{
			// get "real" cell position and form cell matrix
			var tm = LIBERTAS_formCellMatrix(ct);
			
			for (j=0; j<tm[cr.rowIndex].length; j++)
			{
				if (tm[cr.rowIndex][j] == cd.cellIndex)
				{
					crealIndex=j;
					break;
				}
			}
			ccrs = cd.rowSpan?cd.rowSpan:1;
			cccs = cd.colSpan?cd.colSpan:1;
			
			if (cr.rowIndex+ccrs<ct.rows.length)
			{
				ncellIndex = tm[cr.rowIndex+ccrs][crealIndex];
				if (ncellIndex != -1 && (crealIndex==0 || (crealIndex>0 && (tm[cr.rowIndex+ccrs][crealIndex-1]!=tm[cr.rowIndex+ccrs][crealIndex]))))
				{
		
					ncrs = ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).rowSpan?ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).rowSpan:1;
					nccs = ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).colSpan?ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).colSpan:1;
					// proceed only if current and next cell colspans are equal
					if (cccs == nccs)
					{
						// increase rowspan of current cell and append content of the next cell to current
						cd.innerHTML += ct.rows(cr.rowIndex+ccrs).cells(ncellIndex).innerHTML;
						ct.rows(cr.rowIndex+ccrs).deleteCell(ncellIndex);
						cd.rowSpan = ccrs+ncrs;
					}
				}
			}
		}
				
	} // mergeDown
	
	// split cell horizontally
	function LIBERTAS_table_cell_split_horizontal_click(editor, sender)
	{
		var ct = LIBERTAS_getTable(editor); // current table
		var cr = LIBERTAS_getTR(editor); // current row
		var cd = LIBERTAS_getTD(editor); // current cell

		if (cd && cr && ct)
		{
			// get "real" cell position and form cell matrix
			var tm = LIBERTAS_formCellMatrix(ct);
	
			for (j=0; j<tm[cr.rowIndex].length; j++)
			{
				if (tm[cr.rowIndex][j] == cd.cellIndex)
				{
					realIndex=j;
					break;
				}
			}
			
			if (cd.rowSpan>1) 
			{
				// split only current cell
				// find where to insert a cell in the next row
				i = realIndex;
				while (tm[cr.rowIndex+1][i] == -1) i++;
				if (i == tm[cr.rowIndex+1].length) 
					ni = ct.rows(cr.rowIndex+1).cells.length;
				else
					ni = tm[cr.rowIndex+1][i];
					
				var newc = ct.rows(cr.rowIndex+1).insertCell(ni);
				cd.rowSpan--;
				var nc = cd.cloneNode();
				newc.replaceNode(nc);
	
				cd.rowSpan = 1;
			}
			else
			{
				// add new row and make all other cells to span one row more
				ct.insertRow(cr.rowIndex+1);
				for (i=0; i<cr.cells.length; i++)
				{
					if (i != cd.cellIndex)
					{
						rs = cr.cells(i).rowSpan>1?cr.cells(i).rowSpan:1;
						cr.cells(i).rowSpan = rs+1;
					}
				}
	
				for (i=0; i<cr.rowIndex; i++)
				{
					var tempr = ct.rows(i);
					for (j=0; j<tempr.cells.length; j++)
					{
						if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
							tempr.cells(j).rowSpan++;
					}
				}
				
				// clone current cell to new row
				var newc = ct.rows(cr.rowIndex+1).insertCell(0);
				var nc = cd.cloneNode();
				newc.replaceNode(nc);
			}
		}
				
	} // splitH
	
	function LIBERTAS_table_cell_split_vertical_click(editor, sender)
	{
		var ct = LIBERTAS_getTable(editor); // current table
		var cr = LIBERTAS_getTR(editor); // current row
		var cd = LIBERTAS_getTD(editor); // current cell

		if (cd && cr && ct)
		{
			// get "real" cell position and form cell matrix
			var tm = LIBERTAS_formCellMatrix(ct);
	
			for (j=0; j<tm[cr.rowIndex].length; j++)
			{
				if (tm[cr.rowIndex][j] == cd.cellIndex)
				{
					realIndex=j;
					break;
				}
			}
			
			if (cd.colSpan>1) {
				// split only current cell
				var newc = ct.rows(cr.rowIndex).insertCell(cd.cellIndex+1);
				cd.colSpan--;
				var nc = cd.cloneNode();
				newc.replaceNode(nc);
				cd.colSpan = 1;
			} else {
				// clone current cell
				var newc = ct.rows(cr.rowIndex).insertCell(cd.cellIndex+1);
				var nc = cd.cloneNode();
				newc.replaceNode(nc);
				
				for (i=0; i<tm.length; i++){
					if (i!=cr.rowIndex && tm[i][realIndex] != -1){
						cs = ct.rows(i).cells(tm[i][realIndex]).colSpan>1?ct.rows(i).cells(tm[i][realIndex]).colSpan:1;
						ct.rows(i).cells(tm[i][realIndex]).colSpan = cs+1;
					}
				}
			}
		}
				
	} // splitV

function LIBERTAS_exec(p){
	this[editor+'_rEdit'].document.event.returnValue = false;
}

function LIBERTAS_getParentTag(editor){
	var trange = this[editor+'_rEdit'].document.selection.createRange();
	if (window.frames[editor+'_rEdit'].document.selection.type != "Control"){
		return (trange.parentElement());
	} else {
		return (trange(0));		
	}
}

// trim functions	
function LIBERTAS_ltrim(txt){
	var spacers = " \t\r\n";
	if (txt!=""){
		while (spacers.indexOf(txt.charAt(0)) != -1){
			txt = txt.substr(1);
		}
	}
	return(txt);
}

function LIBERTAS_rtrim(txt){
	var spacers = " \t\r\n";
	while (spacers.indexOf(txt.charAt(txt.length-1)) != -1){
		txt = txt.substr(0,txt.length-1);
	}
	return(txt);
}

function LIBERTAS_trim(txt){
	return(LIBERTAS_ltrim(LIBERTAS_rtrim(txt)));
}


	
	// is selected text a full tags inner html?
function LIBERTAS_isFoolTag(editor, el){
	var trange = this[editor+'_rEdit'].document.selection.createRange();
	var ttext;
	if (trange != null) 
		ttext = LIBERTAS_trim(trange.htmlText);
	if (ttext != LIBERTAS_trim(el.innerHtml))
		return false;
	else
		return true;
}
	
		
	

	// switch to wysiwyg mode
function LIBERTAS_design_tab_click(editor, sender){
	if (this[editor+'_rEdit'].currentDesignMode!="On"){
		this[editor+'_rEdit'].currentDesignMode='On';
		//iText = this[editor+'_rEdit'].document.body.innerText;
		iText = document.all[editor].value;
		this[editor+'_rEdit'].document.body.innerHTML = fixHR(iText);
		document.all['LIBERTAS_'+editor+'_editor_mode'].value = 'design';

		// turn off html mode toolbars
		document.all['LIBERTAS_'+editor+'_toolbar_top_html'].style.display = 'none';
		document.all['LIBERTAS_'+editor+'_TableButtons'].style.visibile = 'show';
		document.all['LIBERTAS_'+editor+'_TableButtons'].style.display = '';
		// turn on design mode toolbars
		document.all['LIBERTAS_'+editor+'_toolbar_top_design'].style.display = 'inline';
		// switch editors		
		document.all[editor].style.display = "none";
		document.all[editor+"_rEdit"].style.display = "inline";
		document.all[editor+"_rEdit"].document.body.focus();
	}
	// turn on invisible borders if needed
	LIBERTAS_toggle_borders(editor,this[editor+'_rEdit'].document.body, null);
		
	this[editor+'_rEdit'].focus();
			
}
	
	// switch to html mode
function LIBERTAS_html_tab_click(editor, sender){
	window.resizeTo(screen.availWidth,screen.availHeight);
	if (this[editor+'_rEdit'].currentDesignMode=='On'){
		this[editor+'_rEdit'].currentDesignMode='Off';
		this[editor+'_rEdit'].width=screen.availWidth-50;
		iHTML = this[editor+'_rEdit'].document.body.innerHTML;
		document.all[editor].value = iHTML;
		document.all['LIBERTAS_'+editor+'_editor_mode'].value = 'html';

		// turn off design mode toolbars
		document.all['LIBERTAS_'+editor+'_toolbar_top_design'].style.display	= 'none';
		document.all['LIBERTAS_'+editor+'_TableButtons'].style.visibile = 'hide';
		document.all['LIBERTAS_'+editor+'_TableButtons'].style.display = 'none';
		// turn on html mode toolbars
		document.all['LIBERTAS_'+editor+'_toolbar_top_html'].style.display		= 'inline';
		// switch editors		
		document.all[editor+"_rEdit"].style.display = "none";
		document.all[editor].style.display = "inline";
	}
	document.all[editor].focus();
	this[editor+'_rEdit'].focus();
			
}
function LIBERTAS_html_editor_setup(editor, sender){
//	this[editor+'_rEdit'].currentDesignMode='Off';
//	this[editor+'_rEdit'].width=screen.availWidth-50;
//	iHTML = this[editor+'_rEdit'].document.body.innerHTML;
//	document.all[editor].value = iHTML;
	document.all['LIBERTAS_'+editor+'_editor_mode'].value = 'html';

// turn on html mode toolbars
	document.all['LIBERTAS_'+editor+'_toolbar_top_html'].style.display		= 'inline';
// switch editors		
//	document.all[editor+"_rEdit"].style.display = "none";
	document.all[editor].style.display = "inline";
	document.all[editor].focus();
	this[editor+'_rEdit'].focus();
			
}
	

	
function remove_name_spaces(doc){
	start_pos			= doc.indexOf("<?");
	while (start_pos != -1){
		start_pos		= doc.indexOf("<?");
		if (start_pos!=-1){
			end_pos		= doc.indexOf(">",start_pos+1);
			doc 		= doc.substring(0, start_pos) + doc.substring(end_pos + 1);
		}
	}
	return doc;
}
	
function launch_progress_bar(){
	try{
		oX								= (screen.width / 2 )-150;
		oY			 					= (screen.height / 2 )-50;
		oWidth							= 488;
		oHeight 						= 199;
		myPopup = window.open("/libertas_images/editor/libertas/dialogs/default_blank.php", "", "scrollbars:no,left="+oX+",top="+oY+",width="+oWidth+",height="+oHeight+"");
//		if (myPopup.document.readyState != 'complete'){
//			setTimeOut("wip(myPopup)",1000);
//		} else {
//			wip(myPopup);
//		}
		myPopup.focus();
		return true;
	} catch (e){
		if (popup_blocker_disabled == false || popup_blocker_disabled == ""){
			alert("Unable to open popup window, if you are using a popup blocker please enable popups for this site.");
		}
		return false;
	}
}

function wip(myPopup){
	if (myPopup.document.readyState == 'complete'){
		myPopup.document.title = 'Libertas Solutions - Processing Dialog';
		var oPopBody = myPopup.document.body;
		oPopBody.style.backgroundColor	= "#ebebeb";
		oPopBody.style.border			= "solid black 1px";
		myPopup.document.title="Work in Progress";
		sz	 = "";
		oPopBody.innerHTML 	= sz;
	} else {
//		alert("not yet");
	}
}	
	function close_progress_bar(){
		if (popup_blocker_disabled == true || popup_blocker_disabled == ""){
			if((myPopup+"" != "null") &&	(myPopup+"" != "") && (myPopup+"" != "undefined")){
				if (myPopup.closed+""!= "undefined"){
					myPopup.close();
				}
			}
		}
	}
	// cleanup html
	
	
	
	

	
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
DHTML Editor
 
Toolbar Context Sensitive Menus switched on use these functions functions other wise load 
 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function LIBERTAS_movie_click(editor, sender){
	myEditor=editor;
	window.frames[editor+'_rEdit'].focus();		 
	movie_window = window.open('/libertas_images/editor/libertas/dialogs/movie_library.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href), "popup", "width=500,height=275,toolbars=no,statusbar=yes;");//
	movie_window.focus();
}

function LIBERTAS_audio_click(editor, sender){
	myEditor=editor;
	window.frames[editor+'_rEdit'].focus();		 
	myWin = window.open('/libertas_images/editor/libertas/dialogs/audio_library.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href), "popup", "width=500,height=275,toolbars=no,statusbar=yes;");//
	myWin.focus();
}

function LIBERTAS_movie_insert(htmlData){
	LIBERTAS_audio_insert(htmlData);
}

function LIBERTAS_audio_insert(htmlData){
	var selection = window.frames[myEditor+'_rEdit'].document.selection.createRange();
		info = htmlData.split("::");
	var l = info[0].split("/")[1].split(".")[0];
	var e = info[0].split(".");
	var ext= e[e.length-1];
		if (ext == 'ra' || ext == 'rm'){
			out ="<a href='?command=FILES_STREAM&identifier="+l+"'>"+selection.htmlText+"</a>";
		} else if (ext == 'au' || ext == 'mp3' || ext == 'mod'){
			out ="<embed src='http://"+domain+base_href+info[0]+"' width='200' height='20' controller=TRUE autoplay=true loop=false>";
			out =' <OBJECT CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" CODEBASE="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0" width="200" height="20"><PARAM NAME="controller" VALUE="'+info[5]+'"><PARAM NAME="type" VALUE="video/quicktime"><PARAM NAME="autoplay" VALUE="'+info[6]+'"><PARAM NAME="target" VALUE="myself"><PARAM NAME="src" VALUE="http://'+domain+base_href+info[0]+'"><PARAM NAME="pluginspage" VALUE="http://www.apple.com/quicktime/download/indext.html"><EMBED CONTROLLER="TRUE" TARGET="myself" SRC="http://'+domain+base_href+info[0]+'" type="video/quicktime" BGCOLOR="#000000" BORDER="1" PLUGINSPAGE="http://www.apple.com/quicktime/download/indext.html"></EMBED></OBJECT>';
		} else if (ext == 'mov'){
		//			out ="<embed src='"+info[0]+"' width='200' height='200' controller=TRUE autoplay=false loop=false>";
			out =' <OBJECT CLASSID="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" CODEBASE="http://www.apple.com/qtactivex/qtplugin.cab#version=6,0,2,0"><PARAM NAME="controller" VALUE="'+info[5]+'"><PARAM NAME="type" VALUE="video/quicktime"><PARAM NAME="autoplay" VALUE="'+info[6]+'"><PARAM NAME="target" VALUE="myself"><PARAM NAME="width" VALUE="200"><PARAM NAME="height" VALUE="200"><PARAM NAME="src" VALUE="http://'+domain+base_href+info[0]+'"><PARAM NAME="pluginspage" VALUE="http://www.apple.com/quicktime/download/indext.html"><EMBED CONTROLLER="TRUE" TARGET="myself" SRC="http://'+domain+base_href+info[0]+'" type="video/quicktime" BGCOLOR="#000000" BORDER="1" PLUGINSPAGE="http://www.apple.com/quicktime/download/indext.html"></EMBED></OBJECT>';
		} else if (ext == 'mv' || ext == 'avi'){
			out = '<OBJECT ID="mediaPlayer" CLASSID="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" STANDBY="Loading Microsoft Windows Media Player components..." TYPE="application/x-oleobject"><PARAM NAME="fileName" VALUE="http://'+domain+base_href+info[0]+'"><PARAM NAME="animationatStart" VALUE="true"><PARAM NAME="transparentatStart" VALUE="true"><PARAM NAME="autoStart" VALUE="'+info[6]+'"><PARAM NAME="showControls" VALUE="'+info[5]+'"></OBJECT>';
		} else {
			out = '<OBJECT ID="mediaPlayer" CLASSID="CLSID:22d6f312-b0f6-11d0-94ab-0080c74c7e95" CODEBASE="http://activex.microsoft.com/activex/controls/mplayer/en/nsmp2inf.cab#Version=5,1,52,701" STANDBY="Loading Microsoft Windows Media Player components..." TYPE="application/x-oleobject"><PARAM NAME="fileName" VALUE="http://'+domain+base_href+info[0]+'"><PARAM NAME="animationatStart" VALUE="true"><PARAM NAME="transparentatStart" VALUE="true"><PARAM NAME="autoStart" VALUE="'+info[6]+'"><PARAM NAME="showControls" VALUE="'+info[5]+'"></OBJECT>';
	}
	selection.pasteHTML(out);			
	myEditor="";
}

function __insert_flash(flash_info,flash_label){ 
	LIBERTAS_flash_insert(flash_info,flash_label);
	myEditor='';
}

function __insert_movie(imgSrc){ 
	LIBERTAS_movie_insert(imgSrc);
	myEditor='';
}

function __insert_audio(imgSrc){ 
	LIBERTAS_audio_insert(imgSrc);
	myEditor='';
}

function LIBERTAS_flash_click(editor, sender){
	myEditor=editor;
	window.frames[editor+'_rEdit'].focus();		 
	image_window = window.open('/libertas_images/editor/libertas/dialogs/flash_library.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href), "popup", "width=500,height=275,toolbars=no,statusbar=yes;");
	image_window.focus();
}

function LIBERTAS_flash_insert(htmlData,flash_label){
	info = htmlData.split("::");
	flashout = '<OBJECT '
					+ 'classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"'
					+ 'codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0"'
					+ 'id="'+info[3]+'" '
					+ 'ALIGN="">'
					+ '<PARAM NAME=movie VALUE="'+base_href+info[0]+'"> '
					+ '<PARAM NAME=quality VALUE=high> '
					+ '<PARAM NAME=bgcolor VALUE=#FFFFFF> '
					+ '<EMBED '
					+ 'src="'+base_href+info[0]+'" '
					+ 'quality=high '
					+ 'bgcolor=#FFFFFF	'
					+ '		NAME="'+info[3]+'" '
					+ '		ALIGN=""'
					+ '	TYPE="application/x-shockwave-flash" '
					+ ' ALT="'+flash_label+'" '
					+ '	PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">'
					+ '</EMBED>'
				+ '</OBJECT>';
	var selection = window.frames[myEditor+'_rEdit'].document.selection.createRange();
	selection.pasteHTML(flashout);			
	myEditor="";
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 DHTML Editor
 
 These are extra functions 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
function LIBERTAS_showColorPicker(editor,curcolor) {
	path_url = '/libertas_images/editor/libertas/dialogs/colorpicker.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url ;
	return showModalDialog(path_url, curcolor, 'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');	
}


function LIBERTAS_stats_click(editor,sender){
	window.frames[editor+'_rEdit'].focus();		 
	var els = this[editor+'_rEdit'].document.body;
	text = els.innerText.split("\n").join(" ").split("\r").join("");
	page_weight	= els.innerHTML.length;
	text_weight	= text.length;
	word_list = text.split(" ");
	img = 0;
	img_file_size =0;
	imgs = this[editor+'_rEdit'].document.images;
	for (i=0;i<imgs.length;i++){
		img ++;
		if (imgs[i].fileSize*1){
			img_file_size += (imgs[i].fileSize*1);
		} else {
			img_file_size += 1;
		}
	}
	if ((page_weight+img_file_size)>5000){
		file_dl_total	= Math.ceil((page_weight+img_file_size)/5000);
		file_dl_days	= Math.floor(file_dl_total / (3600*24));
		file_dl_hour	= Math.floor((file_dl_total - (file_dl_days * (3600*24)))	/ 3600);
		file_dl_min		= Math.floor((file_dl_total - ((file_dl_days * (3600*24)) + (file_dl_hour *3600))) / 60);
		file_dl_sec		= (file_dl_total % 60);
		file_dl_size	= "";
						
		if (file_dl_days>0)
			file_dl_size	= file_dl_days+"d ";
		if (file_dl_hour>0)
			file_dl_size += file_dl_hour+"h ";
		if (file_dl_min>0)
			file_dl_size += file_dl_min+"m ";
		if (file_dl_sec>0)
			file_dl_size += file_dl_sec+"s";
	} else {
		file_dl_size = "< 1s";
	}
	alert("HTML Page size : "+page_weight+" bytes\nTotal images weight: "+img_file_size+" bytes \n"
			+"--------------------------------------\n"
			+"Total page weight : "+(page_weight+img_file_size)+"\n"
			+"--------------------------------------\n"
			+"\n"
			+"Total word count : "+word_list.length +"\n"
			+"Number of embedded images : "+img +"\n\n"
			+"--------------------------------------\n"
			+"Approximate download time of ("+file_dl_size+")");
}



function __extract_information(szType, parameter){
	/*
		check to see if the cache is available for information yet? 
	*/
	if (cache_data.document.readyState != 'complete'){
		setTimeout("__extract_information('"+szType+"', '"+parameter+"');",1000);
		return;
	} else {
		if (szType=='image' && cache_data.frmDoc.image_data.value==''){
			cache_data.__extract_info('image',parameter);
		}
		if (szType=='flash' && cache_data.frmDoc.flash_data.value==''){
			cache_data.__extract_info('flash');
		}
		if (szType=='menu' && cache_data.frmDoc.menu_data.value==''){
			cache_data.__extract_info('menu');
		}
		if (szType=='page' && cache_data.frmDoc.page_data.value==''){
			cache_data.__extract_info('page',parameter);
		}
		if (szType=='file' && cache_data.frmDoc.file_data.value==''){
			cache_data.__extract_info('file',parameter);
		}
		if (szType=='movie' && cache_data.frmDoc.movie_data.value==''){
			cache_data.__extract_info('movie',parameter);
		}
		if (szType=='audio' && cache_data.frmDoc.audio_data.value==''){
			cache_data.__extract_info('audio',parameter);
		}
		if (szType=='forms' && cache_data.frmDoc.form_data.value==''){
			cache_data.__extract_info('forms',parameter);
		}
		if (szType=='category' && cache_data.frmDoc.category_data.value==''){
			cache_data.__extract_info('category',parameter);
		}
	}
}


	function LIBERTAS_emocs_click(editor, sender){
		myEditor=editor;
		window.frames[editor+'_rEdit'].focus();		 
		image_window = showModalDialog('/libertas_images/editor/libertas/dialogs/emocs.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href), sender, "width=500,height=275,toolbars=no,statusbar=no;");
		if (image_window){
			__insert_image(image_window);
		 	}
		myEditor='';
	}


	function LIBERTAS_find_click(editor, sender){
		findStr = showModelessDialog('/libertas_images/editor/libertas/dialogs/findandreplace.php?ToDo=find', this[editor+'_rEdit'], "dialogWidth:330px; dialogHeight:180px; scroll:no; status:no; help:no;" );
	}
	function LIBERTAS_replace_click(editor, sender){
		findStr = showModelessDialog('/libertas_images/editor/libertas/dialogs/findandreplace.php?ToDo=replace', this[editor+'_rEdit'], "dialogWidth:330px; dialogHeight:215px; scroll:no; status:no; help:no;" );
	}

function _Libertas_GetElement(objElement, htmlTag){
	while ((objElement!=null) && (objElement.tagName!=htmlTag)){
		objElement = objElement.parentElement;
	}
	return objElement;
}
function _Libertas_GetBlock(oEl){
	var sBlocks = "|H1|H2|H3|H4|H5|H6|P|PRE|LI|TD|TH|DIV|BLOCKQUOTE|DT|DD|TABLE|HR|IMG|ABBR|ACRONYM|";
	while ((oEl!=null) && (sBlocks.indexOf("|"+oEl.tagName+"|")==-1)){
		oEl = oEl.parentElement;
	}
	return oEl;
}


function range(editor,	v ) {
	myEditor = editor;
	objSel =	_Libertas_GetSelection();
	myEditor = "";
	return objSel;
}


function LIBERTAS_special_char_click(editor, sender, insertcharacter){
	//update 
	if (""+insertcharacter=="undefined" || insertcharacter+''=='' || insertcharacter+''=='__PICK__'){
		myLocation = '/libertas_images/editor/libertas/dialogs/specialcharacter_library.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href);
		insertcharacter = showModalDialog(myLocation, sender, "dialogHeight=430px;dialogWidth=500px;resizable=no;status=no;");	
	}
	window.frames[editor+'_rEdit'].focus();		 
	if (insertcharacter){
		//_exec_command(editor+'_rEdit', 'paste', true, insertcharacter);
		var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
		tmp = selection.htmlText.toLowerCase();
		if (tmp.indexOf("<td")!=-1){
			alert('Sorry, the area that you have selected, contains multiple table cells. Please select information in one cell only.');
		} else {
			selection.select();
			if (mybkmrk+""!="null")
				selection.moveToBookmark(mybkmrk);
			selection.pasteHTML(insertcharacter);			
			if (mybkmrk+""!="null")
				selection.moveToBookmark(mybkmrk);
				selection.select();
			selection.collapse(false);
			mybkmrk = null;
		}
	}
}

function setdoubleclick(editor){
 	//el = document.createEventObject();
	myEditor = editor;
	var oSel = _Libertas_GetSelection();
	if (oSel.item){
		if (oSel.item(0).tagName=="IMG")	{
			if (oSel.item(0).id=='libertas_form'){
				LIBERTAS_embed_form_click(editor,this);
			} else if (oSel.item(0).id.substring(0,9)=='slideshow'){
				LIBERTAS_embed_slideshow_click(editor, this, oSel.item(0));
			} else if (oSel.item(0).id=='mouseover'){	
				LIBERTAS_mouseover_click(editor, this, oSel.item(0));
			} else {
				LIBERTAS_image_prop_click(editor,this);
			}
		}
		if (oSel.item(0).tagName=="TABLE")	{
			LIBERTAS_table_prop_click(editor,this);
		}
	} else {
		try{
			if (oSel.parentElement){
				element_ptr = _Libertas_GetBlock(oSel.parentElement());
			} else {
				element_ptr = _Libertas_GetBlock(oSel.item(0));
			}
			if (element_ptr.tagName=="ABBR"){
				LIBERTAS_abbr(editor, this);
			}
			if (element_ptr.tagName=="ACRONYM"){
				LIBERTAS_acronym(editor, this);
			}
		} catch(e){}
	}	
	
	myEditor = "";
}

function LIBERTAS_change_paragraph_click(editor, sender, override){
	myEditor=editor;
	if (override+""=="undefined"){
		if (sender.options[sender.options.selectedIndex].value!=''){
			val = sender.options[sender.options.selectedIndex].value;
			sender.options[sender.options.selectedIndex].selected=false;
			sender.options[0].selected=true;
			sender.selectedIndex=0;
		}
	} else {
		val = override;
	}
	page_ptr	= _Libertas_GetSelection();
	if (page_ptr.parentElement){
		element_ptr = _Libertas_GetBlock(page_ptr.parentElement());
	} else {
		element_ptr = _Libertas_GetBlock(page_ptr.item(0));
	}
	page_ptr.execCommand("FormatBlock", false, "<"+val+">");
	window.frames[editor+'_rEdit'].focus();		 
}

function spell_replacetext(f,r){
	findtext(f);
		 	if (spellRange.text.toLowerCase() == f.toLowerCase()) 
		spellRange.text = r;
}

function spell_replacealltext(f,r){
				var searchval = f;
				var wordcount = 0;
				var msg = "";
				spellRange.expand("textedit");
				spellRange.collapse();
				spellRange.select();
				while (spellRange.findText(searchval, 1000000000, 6)){
						spellRange.select();
						spellRange.text = r;
						wordcount++;
				}
				if (wordcount == 0) msg = "Word was not found. Nothing was replaced.";
				else msg = wordcount + " word(s) were replaced.";
				alert(msg);
}

function findtext(searchval){
			spellRange.collapse(true);
//		if (document.frmSearch.searchDir[0].checked)
//			rng.setEndPoint("EndToStart", rng);
//		else 
		spellRange.setEndPoint("StartToEnd", spellRange);
		if (spellRange.findText(searchval, 10000000000, 6)) {
			spellRange.select();
		} 
}	
function createActiveXObject(id){
	var error;
	var control = null;
	try{
		if (window.ActiveXObject){
			control = new ActiveXObject(id);
		} else if (window.GeckoActiveXObject){
			control = new GeckoActiveXObject(id);
		}
	} catch (error){
			
	}
	return control;
}
	
function LIBERTAS_generate_summary(editorSrc, editorDest){
	if (this[editorDest+'_rEdit'].document.body.innerHTML.length==0){
		completetext	= this[editorSrc+'_rEdit'].document.body.innerText;
		summary="";
		if ((pos = completetext.indexOf("\n"))!=-1){
			summary = completetext.substring(0,pos);
		} else {
			summary = completetext;
		}
		this[editorDest+'_rEdit'].document.body.innerHTML ="<p>" + summary + "</p>";
		LIBERTAS_regenerate_keywords_from_all_editors_click(editorDest,this);
//		document.all['btn_regen_keys'].style.display="";
	}
}
	


function LIBERTAS_setZoom(editor,sender){
	if (sender.options[sender.selectedIndex].value!=''){
		val = sender.options[sender.selectedIndex].value+"%";
	} else {
		val = "100%";
	}
	this[editor+'_rEdit'].document.body.runtimeStyle.zoom = val;
	this[editor+'_rEdit'].focus();
}

/*
function _exec_command(editor, param1, param2, param3){
	if ((version*1) < 6){
		LIBERTAS_GENERAL_getFormElement(editor).document.execCommand(param1, param2, param3);
		//window.frames[editor].document.execCommand(param1, param2, param3);
	}else {
		//	LIBERTAS_GENERAL_getFormElement(editor).document.execCommand(param1, param2, param3);
		if (editor.indexOf("_rEdit")!=-1){
			spareEditor = editor.substring(0, editor.indexOf("_rEdit"));
		} else {
			spareEditor = editor;
		}
		check = has_function(spareEditor, 'libertas_configuration_auto_tidy');
		if (param1=='paste' && check == true &&  window.frames[spareEditor+'_rEdit'].currentDesignMode=='On'){
			LIBERTAS_paste_special_click(spareEditor, null);
		}else {
			document.focus();
			document.execCommand(param1, param2, param3);
		}
		debug_alert("Executing Command "+param1+", "+param2+", "+param3);
	}
}
function x_exec_command(editor, param1, param2, param3){
	if (version < 6){
		try {
			//LIBERTAS_GENERAL_getFormElement(editor+'_rEdit').document.execCommand(param1, param2, param3);
			window.frames[editor].document.execCommand(param1, param2, param3);
		} catch (e){
			debug("Failed :: window.frames[editor].document.execCommand(param1, param2, param3)");
		}
	}else {
		try {	
			window.frames[editor].focus();
			document.execCommand(param1, param2, param3);
		} catch (e){
			debug("Failed :: document.execCommand(param1, param2, param3);");
		}
	}
}
*/

	function _Libertas_GetSelection(edit_name){
		p= myEditor;
		if (edit_name+"" != "undefined")
			myEditor=edit_name;
		
		var oSel = this.selection;
		if (!oSel) {
			oSel		= this[myEditor+'_rEdit'].document.selection.createRange();
			oSel.type	= this[myEditor+'_rEdit'].document.selection.type;
		}
		myEditor=p;
		return oSel;
	}

	function LIBERTAS_regenerate_keywords_from_all_editors_click(editor, sender){
		var str = ""; // current cell
		var f= get_form();
		try{
			str += f.trans_title.value+" ";
		} catch(e){
			// no field
		}
		for (i=0; i<libertas_editors.length; i++){
			str += this[libertas_editors[i]+"_rEdit"].document.body.innerText+" ";
		}
		myObj = new Object();
		myObj.src	= str;
		
		myObj.extraIgnoreList	= f.temp_ignore_list.value;
		myObj.keywordSpace = document.all['displayKeywords'];
		path = '/libertas_images/editor/libertas/dialogs/regenerate_keywords.php?lang=' + document.all['LIBERTAS_'+libertas_editors[0]+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+libertas_editors[0]+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href);
		var keys = showModalDialog(path, myObj, 'dialogHeight:220px; dialogWidth:500px; resizable:no; status:no');	
		document.all['displayKeywords'].innerHTML = keys;
	}


	
	// toggle borders worker function
	function LIBERTAS_toggle_borders(editor, root, toggle)
	{
		// get toggle mode (on/off)
		var toggle_mode = toggle;
		if (toggle == null)
		{
			var tgl_borders = document.getElementById("LIBERTAS_"+editor+"_borders");
			if (tgl_borders != null)
			{
				toggle_mode = tgl_borders.value;
			}
			else
			{
				toggle_mode = "on";
			}
		}
		var tbls = new Array();
		if (root.tagName == "TABLE")
		{
			tbls[0] = root;
		}
		else
		{
			// get all tables starting from root
			tbls = root.getElementsByTagName("TABLE");
		}
		
		var tbln = 0;
		if (tbls != null) tbln = tbls.length;
		for (ti = 0; ti<tbln; ti++)
		{
			if ((tbls[ti].style.borderWidth+"" == "" || tbls[ti].style.borderWidth == 0 || tbls[ti].style.borderWidth == "0px") &&
					(tbls[ti].border == 0 || tbls[ti].border == "0px") &&
					(toggle_mode == "on"))
			{
				tbls[ti].runtimeStyle.borderWidth = "1px";
				tbls[ti].runtimeStyle.borderStyle = "dashed";
				tbls[ti].runtimeStyle.borderColor = "#aaaaaa";
			} // no border
			else 
			{
				tbls[ti].runtimeStyle.borderWidth = "";
				tbls[ti].runtimeStyle.borderStyle = "";
				tbls[ti].runtimeStyle.borderColor = "";
			}
				
			var cls = tbls[ti].cells;
			// loop through cells
			for (ci = 0; ci<cls.length; ci++)
			{
				if ((tbls[ti].style.borderWidth == 0 || tbls[ti].style.borderWidth == "0px") &&
						(tbls[ti].border == 0 || tbls[ti].border == "0px") && 
						(cls[ci].style.borderWidth == 0 || cls[ci].style.borderWidth == "0px") && 
						(toggle_mode == "on"))
				{
					cls[ci].runtimeStyle.borderWidth = "1px";
					cls[ci].runtimeStyle.borderStyle = "dashed";
					cls[ci].runtimeStyle.borderColor = "#aaaaaa";
				}
				else 
				{
					cls[ci].runtimeStyle.borderWidth = "";
					cls[ci].runtimeStyle.borderStyle = "";
					cls[ci].runtimeStyle.borderColor = "";
				}
			} // cells loop
		} // tables loop
	} // LIBERTAS_toggle_borders
	
	
	function LIBERTAS_help_click(editor, sender){
		access_uri = "/libertas_images/editor/libertas/dialogs/help.php?editor="+editor;
		var nt = showModalDialog(access_uri, sender, 'dialogHeight:600px; dialogWidth:450px; resizable:no; status:no');	
	}

	
	function random_digits(c){
		sz="";
		for(var i=0; i<c;i++){
			sz+= ""+Math.floor(Math.random() * 10)-1;
		}
		return sz;
	}/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 DHTML Editor
 
 Functions for font, size, style and paragraph formatting 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function LIBERTAS_style_change(editor, sender){
	classname = sender.options[sender.selectedIndex].value;
	
	window.frames[editor+'_rEdit'].focus();		 

	var el = LIBERTAS_getParentTag(editor);
	if (el != null && el.tagName.toLowerCase() != 'body')
	{
		if (classname != 'default')
			el.className = classname;
		else
			el.removeAttribute('className');
	}
	else if (el.tagName.toLowerCase() == 'body')
	{
		if (classname != 'default')
			this[editor+'_rEdit'].document.body.innerHTML = '<p class="'+classname+'">'+this[editor+'_rEdit'].document.body.innerHTML+'</p>';
		else
			this[editor+'_rEdit'].document.body.innerHTML = '<p>'+this[editor+'_rEdit'].document.body.innerHTML+'</p>';
	}
	sender.selectedIndex = 0;

			
}

function LIBERTAS_font_change(editor, sender){
	fontname = sender.options[sender.selectedIndex].value;
	
	window.frames[editor+'_rEdit'].focus();		 

	_exec_command(editor+'_rEdit', 'fontname', false, fontname);

	sender.selectedIndex = 0;

			
}

function LIBERTAS_fontsize_change(editor, sender){
	myfontsize = sender.options[sender.selectedIndex].value;
	
	window.frames[editor+'_rEdit'].focus();		 

	_exec_command(editor+'_rEdit', 'fontsize', false, myfontsize);

	sender.selectedIndex = 0;

			
}

function LIBERTAS_paragraph_change(editor, sender){
	format = sender.options[sender.selectedIndex].value;
	
	window.frames[editor+'_rEdit'].focus();		 

	_exec_command(editor+'_rEdit', 'formatBlock', false, format);

	sender.selectedIndex = 0;

			
}

function LIBERTAS_fore_color_click(editor, sender, color){
	window.frames[editor+'_rEdit'].focus();		 
	var selection = range(editor);
	var noHtml 				= (selection.htmlText.indexOf("<")==-1)?1:0;
	var selTypeStartIsTag	= (selection.htmlText.indexOf("<")==2)?1:0;
	var selTypeEndIsTag 	= (selection.htmlText.lastIndexOf(">")==(selection.htmlText.length-1))?1:0;
	if (myForegroundColor=="__undefined__"){
		myForegroundColor=palette[0];
	}
	try{
		if (color == "__REMOVE__"){
			v = "__REMOVE__";
		} else {
			if ((color+""=="undefined") || (color+""=="")){
				v = myForegroundColor; 
			} else if (color=="__PICK__"){
				var v = LIBERTAS_showColorPicker(editor,null);
				myForegroundColor = v;
			} else if ((color+""!="undefined") && (color+""!="")){
				v = color;
				myForegroundColor = v;
			}
		}
		if (v == "__REMOVE__"){
			v = null;
		}
		if ((v!="") && (v+""!="undefined")){
			selection.execCommand("ForeColor",false,v);
			if(selection.tagName+"" == "undefined"){
				var item = selection.parentElement();
				if (item.tagName=="FONT"){
					if (v==null){
						item.style.color = '';
					} else {
						item.style.color = v;
					}
				}
			}
		}
	} catch(e) {
		alert("Sorry you can not set the font across table cells");
	}
	this[editor+'_rEdit'].focus();
}

function LIBERTAS_bg_color_click(editor, sender, color){
	window.frames[editor+'_rEdit'].focus();		 
	var selection = range(editor);
	var noHtml 				= (selection.htmlText.indexOf("<") == -1)?1:0;
	var selTypeStartIsTag 	= (selection.htmlText.indexOf("<P") == 2)?1:0;
	var selTypeEndIsTag 	= (selection.htmlText.lastIndexOf(">") == (selection.htmlText.length-1))?1:0;
	var rng = this[editor+'_rEdit'].document.selection.createRange();
	rng.expand("textedit");
	allText = rng.htmlText;
	var startpoint	= 0;
	var endpoint	=-1;
	if (myBackgroundColor == "__undefined__"){
		myBackgroundColor = palette[1];
	}
	try{
		if (color == "__REMOVE__"){
			v = "__REMOVE__";
		} else {
			if ((color+""=="undefined") || (color+""=="")){
				v = myBackgroundColor;
			} else if (color=="__PICK__"){
				var v = LIBERTAS_showColorPicker(editor,null);
				myBackgroundColor = v;
			} else if ((color+""!="undefined") && (color+""!="")){
				v = color;
				myBackgroundColor = v;
			}
		}
		if (v == "__REMOVE__"){
			v = null;
		}
		if ((v!="") && (v+""!="undefined")){
			selection.execCommand("BackColor",false,v);
		}
	} catch(e) {
		alert("Sorry you can not set the font across table cells");
	}
	this[editor+'_rEdit'].focus();
	}

function LIBERTAS_set_font_size(editor, sender) {
	var selection = range(editor);
	var noHtml 				= (selection.htmlText.indexOf("<")==-1)?1:0;
	var selTypeStartIsTag = (selection.htmlText.indexOf("<")==2)?1:0;
	var selTypeEndIsTag = (selection.htmlText.lastIndexOf(">")==(selection.htmlText.length-1))?1:0;
	try{
		var v = sender.options[sender.selectedIndex].value;
		if (v == ""){
			v = null;
		}
		if ((v!="") && (v+""!="undefined")){
			selection.execCommand("FontSize",false,v);
			sender.options[sender.selectedIndex].selected=false;
			sender.options[0].selected=true;
			sender.selectedIndex=0;
		}
	} catch(e) {
		alert("Sorry you can not set the font across table cells");
	}
	this[editor+'_rEdit'].focus();
}

function LIBERTAS_set_font_face(editor, sender) {
	var selection = range(editor);
	var noHtml 				= (selection.htmlText.indexOf("<")==-1)?1:0;
	var selTypeStartIsTag	= (selection.htmlText.indexOf("<")==2)?1:0;
	var selTypeEndIsTag		= (selection.htmlText.lastIndexOf(">")==(selection.htmlText.length-1))?1:0;
	try{
		var v = sender.options[sender.selectedIndex].value;
		if (v == ""){
			v = null;
		}
		if ((v!="") && (v+""!="undefined")){
			selection.execCommand("FontName",false,v);
			sender.options[sender.selectedIndex].selected=false;
			sender.options[0].selected=true;
			sender.selectedIndex=0;
		}
	} catch(e) {
		alert("Sorry you can not set the font across table cells");
	}
	this[editor+'_rEdit'].focus();
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 DHTML Editor
 
 form builder embed functions

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function LIBERTAS_embed_form_click(editor, sender){
	window.frames[editor+'_rEdit'].focus();		 
	myEditor=editor;
		src = showModalDialog('/libertas_images/editor/libertas/dialogs/form_manager.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value, sender, 'dialogHeight:250px; dialogWidth:500px; resizable:no; status:no');	
		if (src != null){
		list= src.split("::");
		window.frames[editor+'_rEdit'].focus();		 
		myEditor=editor;
		var selection = _Libertas_GetSelection();
		if (selection.item){
			if (selection.item(0).tagName=="IMG" && selection.item(0).id=='libertas_form'){
				selection.item(0).frm_identifier = list[0];
			} else {
				var element = this[editor+'_rEdit'].document.createElement("SPAN");
				element.innerHTML = "<img id='libertas_form' src='/libertas_images/editor/libertas/lib/themes/default/img/embedded_form.gif' alt='"+list[1]+"' frm_identifier='"+list[0]+"' width='300' height='20'/>";
				selection.pasteHTML(element.innerHTML);
			}
		} else {
			var element = this[editor+'_rEdit'].document.createElement("SPAN");
			element.innerHTML = "<img id='libertas_form' src='/libertas_images/editor/libertas/lib/themes/default/img/embedded_form.gif' alt='"+list[1]+"' frm_identifier='"+list[0]+"' width='300' height='20'/>";
			selection.pasteHTML(element.innerHTML);
		}
	}
	myEditor='';
	window.frames[editor+'_rEdit'].focus();		 

}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 DHTML Editor
 
 hypertext link functions

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __insert_link(szURL, szTitle, szType, szExternal){
	var selection = _Libertas_GetSelection();
	try{
		selection.expand("word");
	} catch (e){}
	var oEl, sType = selection.type, bImg = false, sz = command ="";
	var uVal = szURL;
	var emailaddress = "";
	if (szType == "mailto:"){
		emailaddress = uVal.substring(0,uVal.indexOf("?"));
	}
	//alert(szURL+", "+szTitle+", "+szType+", "+szExternal+", "+emailaddress)
	var sType = selection.type;
	if (szTitle==""){
		szTitle=szURL;
	}
	szURL = ((0 == szURL.indexOf("mailto:") || 0 == szURL.indexOf("http://") || 0 == szURL.indexOf("ftp://")) ? "" : szType) + szURL;
	if (szURL!=""){
		var element = this[myEditor+'_rEdit'].document.createElement("SPAN");
//		alert("sel.text = ["+selection.htmlText+"]["+labelHTML+"]");
		if (selection.htmlText+""=="undefined"){
//			alert("1");
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if a control element then htmltext == undefined do this 
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			tag = _Libertas_GetElement(selection.item(0),"IMG");
			if (tag.tagName=='IMG'){
				tag.style.border = 0;
			}
			selection.execCommand("CreateLink",false,szURL);
			selection = _Libertas_GetSelection();
			linktag =	_Libertas_GetElement(selection.item(0),"A");
			//linktag.setAttribute("title",szTitle,0);
			linktag.title= szTitle;
			if (szExternal=='Yes'){
				linktag.rel = '_libertasExternalWindow';
			} else {
				linktag.attributes.removeNamedItem("rel");
			}
		} else if (selection.htmlText=="" || selection.htmlText=="\r\n<P>&nbsp;</P>"){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if no text exists insert link will not work. so this instead
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if (szExternal=='Yes'){
				element.innerHTML = "<a href='"+szURL+"' rel='_libertasExternalWindow' title='"+szTitle+"'>"+szTitle+"</a>";
			} else {
				element.innerHTML = "<a href='"+szURL+"' title='"+szTitle+"'>"+szTitle+"</a>";
			}
			selection = _Libertas_GetSelection();
			selection.pasteHTML(element.innerHTML);
		} else if (labelHTML.indexOf("@")!=-1){
//			alert("3");
			//alert("email iink");
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if no text exists insert link will not work. so this instead
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if (szExternal=='Yes'){
				element.innerHTML = "<a href='"+szURL+"' rel='_libertasExternalWindow' title='"+szTitle+"'>"+emailaddress+"</a>";
			} else {
				element.innerHTML = "<a href='"+szURL+"' title='"+szTitle+"'>"+emailaddress+"</a>";
			}
			selection.pasteHTML(element.innerHTML);
		} else {
//			alert("4");
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- this is for inserting links correctly
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			selection.execCommand("CreateLink", false, szURL);
			selection.moveToElementText(selection.parentElement());
			exists = false;
			var myContent = this[myEditor+'_rEdit'].document.createElement("SPAN");
			myContent.innerHTML = selection.htmlText;
			for(var i=0; i < myContent.all.length; i++){
				if (myContent.all[i].tagName == "A" && (myContent.all[i].editing=="1" || myContent.all.length==1)){
//					alert(i+") ["+myContent.all.length+"] ["+myContent.all[i].editing+"]");
					exists = true;
					myContent.all[i].setAttribute("title",szTitle,0);

					if(labelHTML+""!="" && labelHTML+""!="undefined"){
						myContent.all[i].innerHTML = labelHTML;
					}
					if (szExternal == 'Yes'){
						myContent.all[i].rel = '_libertasExternalWindow';
					} else {
						try{
							myContent.all[i].attributes.removeNamedItem("rel");
						} catch (e) {
							try{
								myContent.all[i].removeAttribute("rel");
							} catch (e) {}
						}
					}
					myContent.all[i].removeAttribute("editing");
				}
				if (selection.parentElement().tagName == "IMG"){
					myContent.all[i].style.border = 0;
				}
			}
			selection.pasteHTML(myContent.innerHTML);
			removeEmptyLinks(myEditor);
		}
	}
	myEditor='';
	
}


function LIBERTAS_hyperlink_click(editor, sender, btntype){
	szURL	= "";
	szTitle = "";
	szExternal = "";
	
	myEditor=editor;
	window.frames[editor+'_rEdit'].focus();		 
	var selection = _Libertas_GetSelection();
	isImage = false;
	page_ptr	= _Libertas_GetSelection(myEditor);
	if (page_ptr.parentElement){
		element_ptr = _Libertas_GetElement(page_ptr.parentElement(),"A");
		if(element_ptr+""!="null" && element_ptr+""!="undefined"){
			element_ptr.editing="1";
		}
	} else {
		element_ptr = _Libertas_GetElement(page_ptr.item(0),"A");
		bImg = (page_ptr.item(0).tagName=="IMG");
		page_ptr.item(0).editing="1";
		element_ptr = page_ptr.item(0);
		isImage=bImg;
	}
	if (element_ptr){
		if(isImage){
			link_element_ptr = _Libertas_GetElement(page_ptr.item(0),"A");
			if(link_element_ptr){
				element_ptr.removeAttribute("editing");
				element_ptr=link_element_ptr;
				element_ptr.editing="1";
				szURL = new String(element_ptr.href);
				szTitle = element_ptr.title;
				szExternal = element_ptr.rel;
				labelHTML = element_ptr.innerHTML;
				if (szExternal!=''){
					szExternal="Yes";
				}
			} else {
				if (btntype == "email"){
	 				command = "email";
					h= 237;
					w = 488;
				} else if (btntype == "file"){
	 				command = "file";
					h= 321;
					w = 488;
				} else if ((btntype == "hyper") || (((szURL.indexOf(base_href)!=-1) && (base_href!='/')) || (szURL.indexOf(domain+base_href)!=-1))){
		 			command = "hyper";
					h= 295;
					w = 488;
				} else {
			 		command = "external";
					h= 287;
					w = 504;
				}
			}
		} else {
			szURL = new String(element_ptr.href);
			szTitle = element_ptr.title;
			szExternal = element_ptr.rel;
			labelHTML = element_ptr.innerHTML;
			if (szExternal!=''){
				szExternal="Yes";
			}
		}
	} else {
	}
	if (szURL.indexOf("mailto:")!=-1){
	 	command = "email";
		if (szURL.indexOf("mailto:")!=-1){
			szURL=szURL.substring(7);
		}
		h= 237;
		w = 488;
 	} else if (szURL.indexOf("FILES_DOWNLOAD")!=-1){
 		command = "file";
		if (szURL.indexOf("?")!=-1){
			szURL=szURL.substring(szURL.indexOf("?"));
		}
		h= 237;
		w = 488;
	} else if (szURL.indexOf("http:")!=-1){
		command = "external";
		h= 287;
		w = 504;
		if (((szURL.indexOf(base_href)!=-1) && (base_href!='/')) || (szURL.indexOf(domain+base_href)!=-1)){
			command="hyper";
			szURL=szURL.substring(base_href.length);
			h= 295;
			w = 488;
		}
	} else {
 		if (btntype == "email"){
 			command = "email";
			h= 237;
			w = 488;
		} else if (btntype == "file"){
 			command = "file";
			h= 321;
			w = 488;
		} else if ((btntype == "hyper") || (((szURL.indexOf(base_href)!=-1) && (base_href!='/')) || (szURL.indexOf(domain+base_href)!=-1))){
 			command = "hyper";
			h= 295;
			w = 488;
		} else {
	 		command = "external";
			h= 287;
			w = 504;
		}
	}
	myLocation = '/libertas_images/editor/libertas/dialogs/'+command+'link_library.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href)+'&url='+escape(szURL)+'&title='+escape(szTitle)+'&external='+escape(szExternal);
	link_window 	= showModalDialog(myLocation, sender, "dialogwidth="+w+"px;dialogheight="+h+"px;toolbars=no;statusbar=no;");
	if (link_window){
		__insert_link(link_window.szURL, link_window.szTitle, link_window.szType, link_window.szExternal);	 
	}
	if(element_ptr+""!="null" && element_ptr+""!="undefined"){
		element_ptr.removeAttribute("editing");
	}
	
	labelHTML="";
}

function LIBERTAS_unlink_click(editor, sender){
	myEditor = editor;
	var oSel = _Libertas_GetSelection();
	var sType = oSel.type;
	if ((oSel.parentElement) && (oSel.text=="")){
		var oStore = oSel.duplicate();
		oSel.expand("word");
		if (oSel.htmlText==""){
			oSel.text = "";
			oSel.setEndPoint("StartToStart",oStore);
		}
		oSel.select();
		sType="Text";
	}
	oSel.execCommand("UnLink",false,"");
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 DHTML Editor
 
 Functions for image maliputation
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function LIBERTAS_image_prop_click(editor, sender){
	var im = LIBERTAS_getImg(editor); // current cell
	if (im.id!=''){
		if (im.id=='libertas_form'){
			LIBERTAS_embed_form_click(editor, this);
		} else if (im.id=='mouseover'){
			LIBERTAS_mouseover_click(editor, this, im);
		} else if (im.id.substring(0,9)=='slideshow'){
			LIBERTAS_embed_slideshow_click(editor, this, im);
		}	
	} else {
		if (im){
			var iProps = {};
			iProps.src = im.src;
			iProps.alt = im.alt;
			iProps.width = (im.style.width)?im.style.width:im.width;
			iProps.height = (im.style.height)?im.style.height:im.height;
			iProps.border = (im.border)?im.border:im.style.borderWidth;
			iProps.align = (im.align)?im.align:im.style.styleFloat;
			iProps.hspace = (im.hspace)?im.hspace:im.style.marginLeft;
			iProps.vspace = (im.vspace)?im.vspace:im.style.marginTop;
//			alert("H " + iProps.hspace);
//			alert("V " + iProps.vspace);
			var niProps = showModalDialog('/libertas_images/editor/libertas/dialogs/img.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value, iProps, 
				'dialogHeight:200px; dialogWidth:366px; resizable:no; status:no');	
			if (niProps){
				im.src = (niProps.src)?niProps.src:'';
				if (niProps.alt){
					im.alt = niProps.alt;
				} else {
					im.removeAttribute("alt");
				}
//				im.align 		= (niProps.align)?niProps.align:'';
				Align  = (niProps.align)?niProps.align:'';
				if (Align=="left" || Align=="right"){
					im.style.float 	= Align;
				} else {
					im.style.float 	= "";
				}
				//im.style.width	= (niProps.width)?niProps.width:'';
				if (niProps.width){
					im.width 		= niProps.width;
					//im.style.width	= niProps.width;
				}
				if (niProps.height){
					//im.style.height	= niProps.height;
					im.height 		= niProps.height;
				}
				//im.style.height = (niProps.height)?niProps.height:'';
				if (niProps.border){
					im.style.borderWidth = niProps.border;
				}
				if (niProps.vspace){
					im.style.marginTop		= niProps.vspace;
					im.style.marginBottom	= niProps.vspace;
				} else {
					im.style.marginTop		= 0;
					im.style.marginBottom	= 0;
				}
				if (niProps.hspace){
					im.style.marginLeft		= niProps.hspace;
					im.style.marginRight	= niProps.hspace;
				} else {
					im.style.marginLeft		= 0;
					im.style.marginRight	= 0;
				}
				im.removeAttribute("border");
				im.removeAttribute("hspace");
				im.removeAttribute("align");
				im.removeAttribute("vspace");
				//im.removeAttribute("width");
				//im.removeAttribute("height");
			}			
			LIBERTAS_updateField(editor,"");
		}
	//	
	}
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	function __insert_image([object])
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __insert_butt(imgSrc){
	if(imgSrc != null){
		this[myEditor+'_rEdit'].document.execCommand('InsertButton', false, 'ttl_layer');
		var e2 = LIBERTAS_getParentTag(myEditor);

		var paramtitle_arr = imgSrc.paramtitle.split("::");

		if (__setAttribute(imgSrc.paramtitle+"")){
			e2.value = paramtitle_arr[0];
		}
		e2.readonly = 1;
		e2.disabled = 1;

		LIBERTAS_updateField(myEditor,"");
	}
	myEditor='';
}


function __insert_image(imgSrc){
	if(imgSrc != null){
/*		
	// this code was written for posible sublevel menus not used
		var newdiv = document.createElement('DIV');
		//newdiv.innerHTML = this.imageCache[this.currentImageIndex].alt;
		newdiv.innerHTML = "sdaf";
		this[myEditor+'_rEdit'].document.body.appendChild(newdiv);
*/
//		this[myEditor+'_rEdit'].document.execCommand('InsertParagraph', false, 'parag');
//		this[myEditor+'_rEdit'].document.execCommand('Delete');


/*		this[myEditor+'_rEdit'].document.execCommand('Delete', 'ttl_layer');
		this[myEditor+'_rEdit'].document.execCommand('Delete', imgSrc.source);
		
		LIBERTAS_updateField(myEditor,"");

		this[myEditor+'_rEdit'].document.execCommand('InsertButton', false, 'ttl_layer');
		var e2 = LIBERTAS_getParentTag(myEditor);

		var paramtitle_arr = imgSrc.paramtitle.split("::");

		if (__setAttribute(imgSrc.paramtitle+"")){
			e2.value = paramtitle_arr[0];
		}
		e2.readonly = 1;
		e2.disabled = 1;
//		e2.style = "color:white";
//		e2.style ="style='COLOR: #ffffff'";
//		e2.Color="#585880";
//		e2.background="#FF0000";

//		e2.classs='btn';

		LIBERTAS_updateField(myEditor,"");
*/
		this[myEditor+'_rEdit'].document.execCommand('insertimage', false, imgSrc.source);
		

		var el = LIBERTAS_getParentTag(myEditor);
		if (__setAttribute(imgSrc.longdesc+"")){
//			el.longdesc			= imgSrc.longdesc;
		}
		if (__setAttribute(imgSrc.alt+"")){
			el.alt				= imgSrc.alt;
		}
		/* Starts To remove stylesheet border from slideshow pics (Comment By Muhammad Imran)*/
		//el.style.border			= 0;
		/* Ends To remove stylesheet border from slideshow pics (Comment By Muhammad Imran)*/
		
		//el.style.width			= imgSrc.width;
		//el.style.height			= imgSrc.height;
		el.width					= imgSrc.width;
		el.height					= imgSrc.height;		
		if (__setAttribute(imgSrc.time_delay+"")){
			el.time_delay = imgSrc.time_delay;
		}
		if (__setAttribute(imgSrc.id+"")){
			el.id = imgSrc.id;
		}
		if (__setAttribute(imgSrc.parameters+"")){
			el.parameters = imgSrc.parameters;
		}
		/* To set paramtitle as image parameter for slideshow Added By Muhammad Imran */
		
		if (__setAttribute(imgSrc.paramtitle+"")){
			el.paramtitle = imgSrc.paramtitle;
		}
		
		/* To set paramtitle as image parameter for slideshow Added By Muhammad Imran */
		
		if (__setAttribute(imgSrc.styleDefinitionfloat+"")){
			el.style.float = imgSrc.styleDefinitionfloat;
		}
//		if (imgSrc.styleDefinitionfloat+""!="undefined"){
//			el.style.float			= imgSrc.styleDefinitionfloat
//		}
		
		/* Starts To remove stylesheet margin from slideshow pics (Comment By Muhammad Imran) */
/*
		el.style.marginLeft		= imgSrc.styleDefinitionmarginleft
		el.style.marginRight	= imgSrc.styleDefinitionmarginright
		el.style.marginTop		= imgSrc.styleDefinitionmargintop
		el.style.marginBottom	= imgSrc.styleDefinitionmarginbottom
*/		
		/* Ends To remove stylesheet margin from slideshow pics (Comment By Muhammad Imran)*/
		
		el.removeAttribute("border");
//		el.removeAttribute("width");
//		el.removeAttribute("height");


		LIBERTAS_updateField(myEditor,"");

//		this[myEditor+'_rEdit'].document.execCommand('InsertParagraph', false, 'ttl_layer');

		

/*
//		this[myEditor+'_rEdit'].document.execCommand('OverWrite', false, imgSrc.source);
		var e2 = LIBERTAS_getParentTag(myEditor);
		if (__setAttribute(imgSrc.paramtitle+"")){
			e2.paramtitle = imgSrc.paramtitle;
		}
*/		
//		LIBERTAS_updateField(myEditor,"");
	
	}
	myEditor='';
}
function __setAttribute(val){
	if ((val+""!="undefined") && (val+""!="null") && (val+""!="")){
		return true
	}
	return false
}
function LIBERTAS_embed_slideshow_click(editor, sender, obj){
	myEditor=editor;
	window.frames[editor+'_rEdit'].focus();		 
	if (obj+''=='undefined'){
		var oSel = _Libertas_GetSelection();
		if (oSel.item){
			if (oSel.item(0).tagName=="IMG"){
				obj = oSel.item(0);
			}
		}
	}
	sender.original = obj;
	access_uri = '/libertas_images/editor/libertas/dialogs/slideshow.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href);
	var image_window = showModalDialog(access_uri, sender, 'dialogHeight:600px; dialogWidth:450px; resizable:no; status:no');	
	if (image_window){
		objsource		 = image_window.source;
		if (objsource+''!='undefined'){
				__insert_image(image_window);
				//__insert_butt(image_window);
		}
	}
	myEditor='';
}

	
	
function LIBERTAS_image_insert_click(editor, sender){
	myEditor=editor;
	window.frames[editor+'_rEdit'].focus();		 
	image_window = showModalDialog('/libertas_images/editor/libertas/dialogs/img_insert.php?product='+product_version+'&lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href), sender, "center=yes;dialogWidth=488px;dialogHeight=399px;toolbars=no;statusbar=no;");//
	if (image_window){
		__insert_image(image_window);
	}
	myEditor='';
}


function LIBERTAS_mouseover_click(editor, sender,obj){
	myEditor=editor;
	window.frames[editor+'_rEdit'].focus();		 
	if (obj+''=='undefined'){
		var oSel = _Libertas_GetSelection();
		if (oSel.item){
			if (oSel.item(0).tagName=="IMG"){
				obj = oSel.item(0);
			}
		}
	}
	sender.original = obj;	
	image_window = showModalDialog('/libertas_images/editor/libertas/dialogs/img_mouseover.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href), sender, "center=yes;dialogWidth=488px;dialogHeight=399px;toolbars=no;statusbar=no;");//
	if (image_window){	
		Source = image_window.source+'" onmouseover="m_over(this,\''+image_window.onmouseover+'\');" onmouseout="m_over(this,\''+image_window.onmouseout+'\');';
		__insert_image(Source);
	}
	myEditor='';
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 DHTML Editor
 
 Special Paste functions

-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/

function GetClipboardHTML(){
	debug('GetClipboardHTML()');
	var oDiv = document.getElementById("divTemp");
	oDiv.innerHTML = "";
	
	var oTextRange = document.body.createTextRange() ;
	oTextRange.moveToElementText(oDiv) ;
	oTextRange.execCommand("Paste") ;
	
	var sData = oDiv.innerHTML ;
	oDiv.innerHTML = "" ;
	return sData ;
}

function LIBERTAS_paste_Normal(editor, sender){
	//alert('LIBERTAS_paste_Normal("'+editor+'", "'+sender+'")');
	//
	window.frames[editor+'_rEdit'].focus();		 
	var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
	if (mybkmrk+""!="undefined" && mybkmrk+""!="" && mybkmrk+""!="null"){
		selection.moveToBookmark(mybkmrk);
	}
	var sText = GetClipboardHTML();
	var f= get_form();
	selection.select();
	window.frames[editor+'_rEdit'].focus();		 
	var el = parent.document.getElementById(editor+"_textarea_PLAIN");
	val="on";
	if (el+"" != "null"){
		if(el.type== "hidden"){
			val = el.value;
		} else {
			if (el.checked){
				val="off";
			}
		}
	}
	if (val=='off'){
		f.elements[editor].focus();
		selection.execCommand("paste", false, null);
	}else{
		try{
			selection.pasteHTML(sText);
			if (mybkmrk+""!="null")
				selection.moveToBookmark(mybkmrk);
			selection.select();
			selection.collapse(false);
		} catch(e){
				
		}
	}
	removeEmptyLinks(editor);
	mybkmrk=null;
	tidy_IMG(editor);
}
function LIBERTAS_paste_PlainText(editor, sender){
	debug('LIBERTAS_paste_PlainText("'+editor+'", "'+sender+'")');
	window.frames[editor+'_rEdit'].focus();		 
	var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
	if (mybkmrk+""!="undefined" && mybkmrk+""!="" && mybkmrk+""!="null"){
		selection.moveToBookmark(mybkmrk);
	}
	var sText = HTMLEncode( clipboardData.getData("Text") ) ;
	selection.select();
	try{
		selection.pasteHTML(sText.replace(/\n/g,'<p>'));
	} catch (e){
		alert("Sorry there was a problem pasting your content please select the location again and try again");
	}
	if (mybkmrk+""!="null")
		selection.moveToBookmark(mybkmrk);
		selection.select();
	selection.collapse(false);
	mybkmrk=null;
}

function HTMLEncode(text){
	debug('HTMLEncode("'+text+'")');
	if ((""+text+""!="") && (""+text+""!="undefined") && (""+text+""!="null")){
		text = text.replace(/&/g, "&amp;");
		text = text.replace(/"/g, "&quot;");
		text = text.replace(/</g, "&lt;");
		text = text.replace(/>/g, "&gt;");
		text = text.replace(/'/g, "&#39;");
	} else {
		text="";
	}
	return text ;
}
function LIBERTAS_paste_special_click(editor, sender){
//	debug('LIBERTAS_paste_special_click("'+editor+'", "'+sender+'")');
	var elements = this[editor+'_rEdit'].document.createElement("SPAN");
	elements.innerHTML = GetClipboardHTML();
	window.frames[editor+'_rEdit'].focus();		 
	var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
	if (mybkmrk+""!="undefined" && mybkmrk+""!="" && mybkmrk+""!="null"){
		selection.moveToBookmark(mybkmrk);
	}
	var found = true;
	while (found){
		found = false;
		var els = elements.all;
		for (i=0; i<els.length; i++){
			if (els[i].tagUrn != null && els[i].tagUrn != ''){
				els[i].removeNode(false);
				found = true;
			} 
			// remove font and span tags
			if (els[i]+'' !='undefined'){
				if (els[i].tagName != null && (els[i].tagName == "FONT" || els[i].tagName == "SPAN" || els[i].tagName == "DIV")){	
					els[i].removeNode(false);
					found = true;
				}
			}
		}			
	}
	// remove styles
	var els = elements.all;
	for (i=0; i<els.length; i++){
		// remove style and class attributes from all tags
		els[i].removeAttribute("className",0);
		els[i].removeAttribute("style",0);
	}
	selection.select();
	selection.pasteHTML(elements.innerHTML);
	if (mybkmrk+""!="null"){
		try{
		selection.moveToBookmark(mybkmrk);
		} catch (e){}
	}
	selection.select();
	selection.collapse(false);
	removeEmptyLinks(editor);
	window.frames[editor+'_rEdit'].focus();		 
	mybkmrk=null;
	tidy_IMG(editor);
}

function LIBERTAS_paste_Text(editor, sender, txt){
	debug('LIBERTAS_paste_Text("'+editor+'", "'+sender+'", "' + txt + '")');
	window.clipboardData.setData("Text","[["+untidy_quotes(txt)+"]]");
	debug("clipboard set [" + txt + "]");
	LIBERTAS_on_click(editor, sender, "paste");
}

function LIBERTAS_table_row_insert_click(editor, sender){
	var ct = LIBERTAS_getTable(editor); // current table
	var cr = LIBERTAS_getTR(editor); // current row

	if (ct && cr){
		var newr = ct.insertRow(cr.rowIndex+1);
		for (i=0; i<cr.cells.length; i++){
			if (cr.cells(i).rowSpan > 1){
				// increase rowspan
				cr.cells(i).rowSpan++;
			}else{
				var newc = cr.cells(i).cloneNode();
				newr.appendChild(newc);
			}
		}
			// increase rowspan for cells that were spanning through current row
			for (i=0; i<cr.rowIndex; i++)
			{
				var tempr = ct.rows(i);
				for (j=0; j<tempr.cells.length; j++)
				{
					if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
						tempr.cells(j).rowSpan++;
				}
			}
		}
				
	} // insertRow
	
	
	function LIBERTAS_table_column_insert_click(editor, sender){
		var ct = LIBERTAS_getTable(editor); // current table
		var cr = LIBERTAS_getTR(editor); // current row
		var cd = LIBERTAS_getTD(editor); // current row

		if (cd && cr && ct)
		{
			// get "real" cell position and form cell matrix
			var tm = LIBERTAS_formCellMatrix(ct);
			
			for (j=0; j<tm[cr.rowIndex].length; j++)
			{
				if (tm[cr.rowIndex][j] == cd.cellIndex)
				{
					realIndex=j;
					break;
				}
			}
			
			// insert column based on real cell matrix
			for (i=0; i<ct.rows.length; i++)
			{
				if (tm[i][realIndex] != -1)
				{
					if (ct.rows(i).cells(tm[i][realIndex]).colSpan > 1)
					{
						ct.rows(i).cells(tm[i][realIndex]).colSpan++;
					}
					else
					{
						var newc = ct.rows(i).insertCell(tm[i][realIndex]+1);
						var nc = ct.rows(i).cells(tm[i][realIndex]).cloneNode();
						newc.replaceNode(nc);
					}
				}
			}
		}
				
	} // insertColumn
	
	
	function LIBERTAS_table_row_delete_click(editor, sender)
	{
		var ct = LIBERTAS_getTable(editor); // current table
		var cr = LIBERTAS_getTR(editor); // current row
		var cd = LIBERTAS_getTD(editor); // current cell

		if (cd && cr && ct)
		{
			// if there's only one row just remove the table
			if (ct.rows.length<=1)
			{
				ct.removeNode(true);
			}
			else
			{
				// get "real" cell position and form cell matrix
				var tm = LIBERTAS_formCellMatrix(ct);
				
				
				// decrease rowspan for cells that were spanning through current row
				for (i=0; i<cr.rowIndex; i++)
				{
					var tempr = ct.rows(i);
					for (j=0; j<tempr.cells.length; j++)
					{
						if (tempr.cells(j).rowSpan > (cr.rowIndex - i))
							tempr.cells(j).rowSpan--;
					}
				}
		
				
				curCI = -1;
				// check for current row cells spanning more than 1 row
				for (i=0; i<tm[cr.rowIndex].length; i++)
				{
					prevCI = curCI;
					curCI = tm[cr.rowIndex][i];
					if (curCI != -1 && curCI != prevCI && cr.cells(curCI).rowSpan>1 && (cr.rowIndex+1)<ct.rows.length)
					{
						ni = i;
						nrCI = tm[cr.rowIndex+1][ni];
						while (nrCI == -1) 
						{
							ni++;
							if (ni<ct.rows(cr.rowIndex+1).cells.length)
								nrCI = tm[cr.rowIndex+1][ni];
							else
								nrCI = ct.rows(cr.rowIndex+1).cells.length;
						}
						
						var newc = ct.rows(cr.rowIndex+1).insertCell(nrCI);
						ct.rows(cr.rowIndex).cells(curCI).rowSpan--;
						var nc = ct.rows(cr.rowIndex).cells(curCI).cloneNode();
						newc.replaceNode(nc);
						// fix the matrix
						cs = (cr.cells(curCI).colSpan>1)?cr.cells(curCI).colSpan:1;
						for (j=i; j<(i+cs);j++)
						{
							tm[cr.rowIndex+1][j] = nrCI;
							nj = j;
						}
						for (j=nj; j<tm[cr.rowIndex+1].length; j++)
						{
							if (tm[cr.rowIndex+1][j] != -1)
								tm[cr.rowIndex+1][j]++;
						}
					}
				}
				// delete row
				ct.deleteRow(cr.rowIndex);
			}
		}
				
	} // deleteRow
	
	function LIBERTAS_table_column_delete_click(editor, sender)
	{
		var ct = LIBERTAS_getTable(editor); // current table
		var cr = LIBERTAS_getTR(editor); // current row
		var cd = LIBERTAS_getTD(editor); // current cell

		if (cd && cr && ct)
		{
			// get "real" cell position and form cell matrix
			var tm = LIBERTAS_formCellMatrix(ct);

			// if there's only one column delete the table
			if (tm[0].length<=1)	
			{
				ct.removeNode(true);
			}
			else
			{
				for (j=0; j<tm[cr.rowIndex].length; j++)
				{
					if (tm[cr.rowIndex][j] == cd.cellIndex)
					{
						realIndex=j;
						break;
					}
				}
				
				for (i=0; i<ct.rows.length; i++)
				{
					if (tm[i][realIndex] != -1)
					{
						if (ct.rows(i).cells(tm[i][realIndex]).colSpan>1)
							ct.rows(i).cells(tm[i][realIndex]).colSpan--;
						else
							ct.rows(i).deleteCell(tm[i][realIndex]);
					}
				}
			}
		}
				
	} // deleteColumn
	
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 DHTML Editor
 
 These are spell checking functions 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	

	function LIBERTAS_spell_click(editor, sender){
		try {
			myObj = new Object();
			spellRange = this[editor+'_rEdit'].document.selection.createRange();
			if(spellRange.text == ""){
				spellRange.expand("textedit");
			}
			spellRange.select();
			myObj.completetext	= this[editor+'_rEdit'].document.body.innerText;
			wordDoc = createActiveXObject("Word.Application");
			if(wordDoc){
				wordDocument = wordDoc.documents.add();
				wordDoc.selection.text = myObj.completetext;
				var objerrors = wordDocument.spellingErrors;
				
				if (objerrors.count>0){
					for (i=1; i<= objerrors.count;i++){
						var misSpeltWord	= objerrors(i);
						myObj.misspeltword	= misSpeltWord.text;
						ok = true;
						for (z = 0; z<spellIgnore.length;z++){
							if (spellIgnore[z]==myObj.misspeltword){
								ok = false;
							}
						}
						if (ok){
							var suggestions		= wordDoc.getspellingsuggestions(misSpeltWord.text);
							myObj.suggestions = new Array();
							for(index=1 ;index<=suggestions.count;index++){
								myObj.suggestions[myObj.suggestions.length]=suggestions(index).Name;
							}
							r = showModalDialog("/libertas_images/editor/libertas/dialogs/spell.php",myObj,"dialogWidth:400px;dialogHeight:300px;center:yes;status:no;scrolling:no");
							if (r){
								if (r.command=="change"){
									spell_replacetext(r.thisWord, r.withThis);
								}
								if (r.command=="change_all"){
									spell_replacealltext(r.thisWord, r.withThis);
									spellIgnore[spellIgnore.length] = r.thisWord;
								}
								if (r.command=="ignore_all"){
									spellIgnore[spellIgnore.length] = r.thisWord;
								}
								if (r.command=="end"){
									i = objerrors.count + 1;
									// ends loop
								}
							}
						}
					}
					alert("Spell Checking is complete");
				} else {
					alert("No Spelling mistakes found.");
				}
				wordDocument.close(0);
				wordDoc.quit();
			} else {
				alert("Sorry there was a problem with loading the Microsoft Word Active X Component.\n"
					+ "This may be caused by not having this web site in your trusted websites or not having Microsoft Office installed.\n"
					+ "To spell check your document and you must select yes if you are requested to run an activeX component.\n"
					+ "--------------------- To Enable this site as a Trusted Site ---------------------\n"
					+ "1. Click on Tools/Internet Options\n"
					+ "2. Click on the Tab labeled as Security\n"
					+ "3. At the next prompt, select Trusted Sites and then click on Sites.\n"
					+ "4. In the next prompt, type in the URL to this Web site. \n"
					+ "	 If your server is not secure, uncheck the box that reads Require server verification\n"
					+ "	 (https://) for all sites in this zone. Click OK.\n"
					+ "5. Now, click on the Custom Level button.\n"
					+ "6. In the next prompt, make sure all ActiveX are enabled. Click Ok. Click Ok again, \n"
					+ "7. Return to the browser. You will see a Trusted Sites icon in the right side of ther status bar.\n"
				);
			}	
		} catch (e) {
			alert("Sorry there was a problem with loading the spell checker.\nThis may be caused by not having MS Office installed.");
		}
	
	}

function createActiveXObject(id){
	var error;
	var control = null;
	try{
		if (window.ActiveXObject){
			control = new ActiveXObject(id);
		} else if (window.GeckoActiveXObject){
			control = new GeckoActiveXObject(id);
		}
	} catch (error){
			
	}
	return control;
}

	function LIBERTAS_table_create_click(editor, sender, rows, cols){
		if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
		{
			// selection is not a control => insert table 
			access_uri ='/libertas_images/editor/libertas/dialogs/table.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href)+'&wai_compliance='+wai_compliance+'&colour_definition='+has_function(editor,'tables_colour');
			var n = new Object();
			if (rows+""!="undefined"){
				n.createRows = rows;
			}
			if (cols+""!="undefined"){
				n.createCols = cols;
			}
			n.palette = palette;
			n.product_version = product_version;
			n.browser_version = version;
			n.browser_type = browser;
			var nt = showModalDialog(access_uri, n, 'dialogHeight:470px; dialogWidth:426px; resizable:no; status:no');	
			if (nt){
				window.frames[editor+'_rEdit'].focus();
				var newtable = document.createElement('TABLE');
				try {
					if(nt.captiontxt!=""){
						oCaption = newtable.createCaption();
						oCaption.innerHTML = nt.captiontxt;
						if (nt.captionalign=="bottom"){
							oCaption.vAlign = nt.captionalign;
						} else {
							oCaption.vAlign='';
						}
					}
					newtable.summary 		= (nt.summary)? nt.summary:'';
					newtable.style.width	= (nt.width)	? nt.width:'';
					newtable.style.height 	= (nt.height) ? nt.height:'';
					newtable.align 			= (nt.align)	? nt.align:'';
					newtable.border 		= (nt.border) ? nt.border:'';
					if (nt.cellPadding) newtable.cellPadding = nt.cellPadding;
					if (nt.cellSpacing) newtable.cellSpacing = nt.cellSpacing;
					if (nt.bgColor) newtable.style.background = (nt.bgColor)?nt.bgColor:'';
					myScope					= (nt.scope) ? nt.scope:'';
					firstRowHeader			= (nt.firstRowHeader) ? 1:0;
					// create rows
					for (i=0;i<parseInt(nt.rows);i++)
					{
						var newrow = document.createElement('TR');
						for (j=0; j<parseInt(nt.cols); j++){
							if ((myScope=='col' || myScope=='row') && i==0 && j==0){
								var newcell = document.createElement('TH');
							} else {
								if (i==0 && myScope=='col'){
									var newcell = document.createElement('TH');
								} else if (j==0 && myScope=='row'){
									var newcell = document.createElement('TH');
								} else {
									var newcell = document.createElement('TD');
								}
							}
							//newcell.setAttribute("align","justify");
							//newcell.setAttribute("valign","top");
							if ((j==0) && (myScope=="row")){
								newcell.setAttribute("scope","row");
							}
							if ((i==0) && (myScope=="col")){
								newcell.setAttribute("scope","col");
							}
							newrow.appendChild(newcell);
						}
						newtable.appendChild(newrow);
					}
					if (currentPos!=null){
						var selection = currentPos;
					} else {
						var selection = window.frames[editor+'_rEdit'].document.selection.createRange();
					}
					selection.pasteHTML(newtable.outerHTML);			
					LIBERTAS_toggle_borders(editor, window.frames[editor+'_rEdit'].document.body, null);
				} catch (excp) {
					alert('error');
				}
			}
		}
	}
	
	function LIBERTAS_table_prop_click(editor, sender){
		window.frames[editor+'_rEdit'].focus();		 

		var tTable;
		// check if table selected
		if (window.frames[editor+'_rEdit'].document.selection.type == "Control"){ 
			var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
			if (tControl(0).tagName == 'TABLE'){
				tTable = tControl(0);
			}
		} else {
			var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
			tControl = tControl.parentElement();
			while ((tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY')){
				tControl = tControl.parentElement;
			}
			if (tControl.tagName == 'TABLE')
				tTable = tControl;
			else
				return false;
		}
		var tProps = {};
		if (tTable.caption){
			tProps.captiontxt			= tTable.caption.innerHTML;
			tProps.captionalign			= tTable.caption.vAlign;
		} else {
			tProps.captiontxt			= "";
			tProps.captionalign			= "";
		}
		tProps.summary 			= tTable.summary;
		tProps.summary 			= tTable.summary;
		tProps.width 			= (tTable.style.width)?tTable.style.width:'';
		tProps.height 			= (tTable.style.height)?tTable.style.height:'';
		tProps.border 			= tTable.border;
		tProps.cellPadding 		= tTable.cellPadding;
		tProps.cellSpacing 		= tTable.cellSpacing;
		tProps.bgColor 			= tTable.style.backgroundColor;
		tProps.align 			= tTable.align;
		tProps.rows				= tTable.rows.length;
		tProps.scope			= '';
		tProps.palette 			= palette;
		tProps.product_version	= product_version;
		tProps.browser_version	= version;
		tProps.browser_type		= browser;
		/*
			try to work out scope from TH tags
		*/
		if (tTable.rows.length>0){
			tProps.columns		= tTable.rows[0].cells.length;
			if (tTable.rows[0].cells[0].tagName.toLowerCase()=="th"){
				if (tTable.rows[0].cells.length>1){
					if (tTable.rows[0].cells[1].tagName.toLowerCase()=="th"){
						tProps.scope='col';
					} else {
						if (tTable.rows.length>1){
							if (tTable.rows[1].cells[0].tagName.toLowerCase()=="th"){
								tProps.scope='row';
							}
						}
					}
				} else {
					if (tTable.rows.length>1){
						if (tTable.rows[1].cells[0].tagName.toLowerCase()=="th"){
							tProps.scope='row';
						} else {
							if (tTable.rows[0].cells[0].tagName.toLowerCase()=="th"){
								tProps.scope='col';
							}
						}
					}
				}
			}
		} else {
			tProps.columns			= 0;
		}
		/*
			confirm scope from scope Attributes tags over write only if scope attribute set.
		*/
		if (tTable.rows.length>1){
			if (tTable.rows[0].cells.length>1){
				if (tTable.rows[0].cells[1].scope=='col'){
					tProps.scope = 'col';
				} else if (tTable.rows[1].cells[0].scope=='row'){
					tProps.scope = 'row';
				}
			} else {
				if ((tTable.rows[1].cells[0].scope=='row') || (tTable.rows[1].cells[0].scope=='col')){
					tProps.scope = tTable.rows[1].cells[0].scope;
				}
			}
		}

		access_uri ='/libertas_images/editor/libertas/dialogs/table.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href)+'&colour_definition='+has_function(editor,'tables_colour');
//		var ntProps = window.open(access_uri)//, tProps, 'dialogHeight:250px; dialogWidth:366px; resizable:no; status:no');	
		var ntProps = showModalDialog(access_uri, tProps, 'dialogHeight:248px; dialogWidth:426px; resizable:no; status:no');	
		
		if (ntProps){
			// set new settings
			if(tTable.caption!=""){
				if (tTable.caption){
					oCaption = tTable.caption;
				} else {
					oCaption = tTable.createCaption();
				}
				oCaption.innerHTML = ntProps.captiontxt;
				
				if (ntProps.captionalign=="bottom"){
					oCaption.vAlign = ntProps.captionalign;
				} else {
					oCaption.vAlign='';
				}
			}
			tTable.style.width = (ntProps.width)?ntProps.width:'';
			tTable.style.height = (ntProps.height)?ntProps.height:'';
			tTable.border = (ntProps.border)?ntProps.border:'';
			tTable.align = (ntProps.align)?ntProps.align:'';
			tTable.summary =(ntProps.summary)?ntProps.summary:'';
			if (ntProps.cellPadding) 
				tTable.cellPadding = ntProps.cellPadding;
			if (ntProps.cellSpacing) 
				tTable.cellSpacing = ntProps.cellSpacing;
			tTable.style.background = (ntProps.bgColor)?ntProps.bgColor:'';
			LIBERTAS_format_table(editor, tTable, ntProps);
			LIBERTAS_toggle_borders(editor, tTable, true);
		}

		//LIBERTAS_updateField(editor,"");
	}
	
	function LIBERTAS_format_table(editor, tTable, newTableProperties){
		/*
			Examine the table structure against changes from the table properties screen 
			noteably the scope of a table we are defining that a scope of a table is based
			on positioning of the Headers of that table. a scope of rows will define the 
		*/
		max_rows = tTable.rows.length;
		max_cols = 0;
		output="";
		for (row_index=0; row_index < max_rows ; row_index++){
			output += LIBERTAS_format_row(tTable, row_index, newTableProperties);
		}
		start = tTable.outerHTML.substr(0,tTable.outerHTML.indexOf(">")+1);
		captionData = tTable.caption.outerHTML;
		if (tTable.caption.innerHTML==""){
		captionData="";
		}
		output = start +captionData + output + "</table>";
		tTable.outerHTML = output;
	}
	
	function LIBERTAS_format_row(t, r, p){
		rowHTML 	= t.rows[r].outerHTML.substr(0,t.rows[r].outerHTML.indexOf(">")+1);
		max_cols	= t.rows[r].cells.length;
		for (col_index=0; col_index < max_cols ; col_index++){
				rowHTML += LIBERTAS_format_cell(t, r, col_index, p);
		}
		rowHTML 	+= "</tr>";
		return rowHTML;
	}
	
	function LIBERTAS_format_cell(t, r, c, p){
		if (p.scope=='row'){
			/*
				Remove scope attributes from table and change any TH tags with a TD tag
			*/
			if (c==0){
				t.rows[r].cells[c].setAttribute("scope","row");
				if (t.rows[r].cells[c].tagName.toLowerCase()=="td"){
					o = t.rows[r].cells[c].outerHTML;
					start_pos = o.indexOf("<");
					return "<th"+(o.substr(start_pos+3,o.length-(start_pos+6)))+"th>"; 
				} 
				if (t.rows[r].cells[c].tagName.toLowerCase()=="th"){
					return t.rows[r].cells[c].outerHTML; 
				} 
			} else {
				t.rows[r].cells[c].removeAttribute("scope");
				if (t.rows[r].cells[c].tagName.toLowerCase()=="th"){
					o = t.rows[r].cells[c].outerHTML;
					start_pos = o.indexOf("<");
					return "<td"+(o.substr(start_pos+3,o.length-(start_pos+6)))+"td>"; 
				} 
				if (t.rows[r].cells[c].tagName.toLowerCase()=="td"){
					return t.rows[r].cells[c].outerHTML; 
				} 
			}
		} else if (p.scope == 'col'){
			if (r==0){
				t.rows[r].cells[c].setAttribute("scope","row");
				if (t.rows[r].cells[c].tagName.toLowerCase()=="td"){
					o = t.rows[r].cells[c].outerHTML;
					start_pos = o.indexOf("<");
					return "<th"+(o.substr(start_pos+3,o.length-(start_pos+6)))+"th>"; 
				} 
				if (t.rows[r].cells[c].tagName.toLowerCase()=="th"){
					return t.rows[r].cells[c].outerHTML; 
				} 
			} else {
				t.rows[r].cells[c].removeAttribute("scope");
				if (t.rows[r].cells[c].tagName.toLowerCase()=="th"){
					o = t.rows[r].cells[c].outerHTML;
					start_pos = o.indexOf("<");
					return "<td"+(o.substr(start_pos+3,o.length-(start_pos+6)))+"td>"; 
				} 
				if (t.rows[r].cells[c].tagName.toLowerCase()=="td"){
					return t.rows[r].cells[c].outerHTML; 
				} 
			}
		} else {
			/*
				Remove scope attributes from table and change any TH tags with a TD tag
			*/
			t.rows[r].cells[c].removeAttribute("scope");
			if (t.rows[r].cells[c].tagName.toLowerCase()=="th"){
				o = t.rows[r].cells[c].outerHTML;
				start_pos = o.indexOf("<");
				return "<td"+(o.substr(start_pos+3,o.length-(start_pos+6)))+"td>"; 
			} 
			if (t.rows[r].cells[c].tagName.toLowerCase()=="td"){
				/*
					On th remove the TH tag and replace with TD tag
				*/
				return t.rows[r].cells[c].outerHTML; 
			} 
		}
	}
	// edits table cell properties
	function LIBERTAS_table_cell_prop_click(editor, sender){
		var cd = LIBERTAS_getTD(editor); // current cell
		
		if (cd){
			var cProps = {};
			bgcolor = cd.style.backgroundColor;
			if (bgcolor.indexOf("#")!=-1){
				cProps.bgColor = cd.style.backgroundColor;
			}
			
			cProps.palette 			= palette;
			cProps.product_version	= product_version;
			cProps.width 			= (cd.style.width)?cd.style.width:cd.width;
			cProps.height 			= (cd.style.height)?cd.style.height:cd.height;
			cProps.align 			= cd.align;
			cProps.vAlign 			= cd.vAlign;
			cProps.className 		= cd.className;
			cProps.noWrap 			= cd.noWrap;
			cProps.styleOptions 	= new Array();
			cProps.product_version	= product_version;
			cProps.browser_version	= version;
			cProps.browser_type		= browser;
			
			if (document.all['LIBERTAS_'+editor+'_tb_style'] != null){
				cProps.styleOptions = document.all['LIBERTAS_'+editor+'_tb_style'].options;
			}
	
			access_uri ='/libertas_images/editor/libertas/dialogs/td.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href)+'&wai_compliance='+wai_compliance+'&colour_definition='+has_function(editor, 'tables_cell_colour');
			var ncProps = showModalDialog(access_uri, cProps, 'dialogHeight:236px; dialogWidth:422px; resizable:no; status:no');	
			
			if (ncProps)	
			{
				cd.align = (ncProps.align)?ncProps.align:'';
				cd.vAlign = (ncProps.vAlign)?ncProps.vAlign:'';
				cd.width = (ncProps.width)?ncProps.width:'';
				cd.style.width = (ncProps.width)?ncProps.width:'';
				cd.height = (ncProps.height)?ncProps.height:'';
				cd.style.height = (ncProps.height)?ncProps.height:'';
				cd.style.background = (ncProps.bgColor)?ncProps.bgColor:'';
				cd.className = (ncProps.className)?ncProps.className:'';
				cd.noWrap = ncProps.noWrap;
			}			
		}
		//LIBERTAS_updateField(editor,"");
	}

	// returns current table cell	
	function LIBERTAS_getTD(editor)
	{
		if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
		{
			var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
			tControl = tControl.parentElement();
			while ((tControl.tagName != 'TD') && (tControl.tagName != 'TH') && (tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
			{
				tControl = tControl.parentElement;
			}
			if ((tControl.tagName == 'TD') || (tControl.tagName == 'TH'))
				return(tControl);
			else
				return(null);
		}
		else
		{
			return(null);
		}
	}

	// returns current table row	
	function LIBERTAS_getTR(editor)
	{
		if (window.frames[editor+'_rEdit'].document.selection.type != "Control")
		{
			var tControl = window.frames[editor+'_rEdit'].document.selection.createRange();
			tControl = tControl.parentElement();
			while ((tControl.tagName != 'TR') && (tControl.tagName != 'TABLE') && (tControl.tagName != 'BODY'))
			{
				tControl = tControl.parentElement;
			}
			if (tControl.tagName == 'TR')
				return(tControl);
			else
				return(null);
		}
		else
		{
			return(null);
		}
	}
	
	
	function LIBERTAS_formCellMatrix(ct)
	{
		var tm = new Array();
		for (i=0; i<ct.rows.length; i++)
			tm[i]=new Array();

		for (i=0; i<ct.rows.length; i++)
		{
			jr=0;
			for (j=0; j<ct.rows(i).cells.length;j++)
			{
				while (tm[i][jr]+"" != "undefined") 
					jr++;
				for (jh=jr; jh<jr+(ct.rows(i).cells(j).colSpan?ct.rows(i).cells(j).colSpan:1);jh++)
				{
					for (jv=i; jv<i+(ct.rows(i).cells(j).rowSpan?ct.rows(i).cells(j).rowSpan:1);jv++)
					{
						if (jv==i)
						{
							tm[jv][jh]=ct.rows(i).cells(j).cellIndex;
						}
						else
						{
							tm[jv][jh]=-1;
						}
					}
				}
			}
		}
		return(tm);
	}
	
	// toggle borders click event 
	function LIBERTAS_toggle_borders_click(editor, sender)
	{
		// get current toggle mode (on/off)
		var toggle_mode;

		var tgl_borders = document.getElementById("LIBERTAS_"+editor+"_borders");
		if (tgl_borders != null)
		{
			toggle_mode = tgl_borders;

			// switch mode		
			if (toggle_mode.value == "on"){
				toggle_mode.value = "off";
			} else {
				toggle_mode.value = "on";
			}

			// call worker function
			LIBERTAS_toggle_borders(editor,this[editor+'_rEdit'].document.body, toggle_mode.value);
		}
				
	} // LIBERTAS_toggle_borders_click

	function onMenu(editor, sender, myData, overRideX, overRideY, overRideHeight, overRideWidth) {
		window.frames[editor+'_rEdit'].focus();		
		myEditor = editor;
		currentPos = _Libertas_GetSelection(); 
		var str = "<div id=\"tblsel\" style=\"background-color:#93d5f4;position:absolute;";
		str = str + "width:0;height:0;z-index:-1;\"></div>";
		str = str + myData
		
		var ifrm = document.frames("tableWizard");
		var obj=sender;//eval("document.all.ae_tbtn"+num);
		var x=0;
		var y=0;
	
		ifrm.document.body.innerHTML	= str;
		ifrm.document.body.onmouseout	= startTimeTable;	
		ifrm.document.body.onmouseover	= endTimeTable;	
		if (sender!=null){
			while(obj.tagName!="BODY") {
				x+=obj.offsetLeft;
				y+=obj.offsetTop;
				obj=obj.offsetParent;
			}	
			document.all.tableWizard.style.pixelTop		= y + 24;
			document.all.tableWizard.style.pixelLeft	= x - 24;
		} else {
			x = overRideX;
			y = overRideY;
			document.all.tableWizard.style.pixelTop		= y;
			document.all.tableWizard.style.pixelLeft	= x;
		}
		document.all.tableWizard.style.pixelWidth	= 0;
		document.all.tableWizard.style.pixelHeight	= 0;
		document.all.tableWizard.style.visibility	= "visible";
	
		document.frames("tableWizard").document.body.style.backgroundColor	= '#ebebeb';	
		document.frames("tableWizard").document.body.style.border			= '1px solid #cccccc';	
		document.frames("tableWizard").document.body.oncontextmenu 			= function(){return false;}
		ifrm.document.body.onselectstart = new Function("return false;");
		if (sender!=null){
			event.cancelBubble = true;
			document.all.tableWizard.style.pixelWidth = overRideWidth;
			document.all.tableWizard.style.pixelHeight = overRideHeight;
		} else {
			document.all.tableWizard.style.pixelWidth = 180;
			document.all.tableWizard.style.pixelHeight = 295;
		}
		
	
	}
		
		
	function onTable(editor,sender) {
		window.frames[editor+'_rEdit'].focus();		
		currentPos = _Libertas_GetSelection(); 
		var str = "<div id=\"tblsel\" style=\"background-color:#93d5f4;position:absolute;";
		str = str + "width:0;height:0;z-index:-1;\"></div>";
		str = str + makeTable(4, 5);
		str = str + "<div style=\"background-color:#ebebeb;text-align:center\" id=\"tblstat\">1 x 1 Table</div>";
		
		var ifrm = document.frames("tableWizard");
		var obj=sender;//eval("document.all.ae_tbtn"+num);
		var x=0;
		var y=0;
	
		ifrm.document.body.innerHTML	= str;
		ifrm.document.body.onmouseout	= startTimeTable;	
		ifrm.document.body.onmouseover	= endTimeTable;	
		while(obj.tagName!="BODY") {
			x+=obj.offsetLeft;
			y+=obj.offsetTop;
			obj=obj.offsetParent;
		}	
		
		document.all.tableWizard.style.pixelTop		= y + 24;
		document.all.tableWizard.style.pixelLeft	= x - 24;
		document.all.tableWizard.style.pixelWidth	= 0;
		document.all.tableWizard.style.pixelHeight	= 0;
		document.all.tableWizard.style.visibility	= "visible";
	
		document.frames("tableWizard").document.body.style.backgroundColor	= '#ebebeb';	
		document.frames("tableWizard").document.body.style.border			= '1px solid #cccccc';	
		document.frames("tableWizard").document.body.onmouseover			= paintTable;	
		document.frames("tableWizard").document.body.onclick				= insertTable;
		document.frames("tableWizard").document.body.oncontextmenu 			= function(){return false;}
	
		event.cancelBubble = true;
	
		ifrm.document.body.onselectstart = new Function("return false;");
		
	
		document.all.tableWizard.style.pixelWidth = ifrm.document.all.oTable.offsetWidth + 3;
		document.all.tableWizard.style.pixelHeight = ifrm.document.all.oTable.offsetHeight + 3 +
		ifrm.document.all.tblstat.offsetHeight;
	
	}
	function makeTable(rows, cols) {
		var a, b, str, n;
		
		str = "<table style=\"table-layout:fixed;border-style:solid; cursor:default;\" "; 
		str = str + "id=\"oTable\" cellpadding=\"0\" ";
		str = str + "cellspacing=\"0\" cols=" + cols;
		str = str + " border=0>\n";
	
		for (a=0;a<rows;a++) {
			str = str + "<tr>\n";
			for(b=0;b<cols;b++) {			
				str = str + "<td width=\"20\" onmouseout=\"javascript:parent.startTimeTable();\" onmouseover=\"javascript:parent.endTimeTable();\" style='border:1px solid #666666'>&nbsp;</td>\n";	// 
			}	
			str = str + "</tr>\n";
		}
		str = str + "</table>";
		return str;
	}
	
	//Closes table selector iframe and replaces document mousedown
	function cancelTable(a) {
	
		document.onmousedown=null;
		document.all.tableWizard.style.visibility = "hidden";
		document.all.tableWizard.style.pixelWidth = 0;
		document.all.tableWizard.style.pixelHeight = 0;
	
		if(a==false) return;
	
		if(typeof(ae_olddocmd)=="function") {
			ae_olddocmd(false);
			document.onmousedown = ae_olddocmd;
		}
		ae_olddocmd = null;
	
		//Set DropDownTable IFrame to small
		document.all.tableWizard.style.pixelWidth = 10;
		document.all.tableWizard.style.pixelHeight = 10;
		
	}
	function paintTable() {
		window.frames[myEditor+'_rEdit'].focus();
		var se = document.frames['tableWizard'].window.event.srcElement;
		var sr, sc, tbl, fAll;
		fAll = document.frames['tableWizard'].document.all;
		if(se.tagName!='TD') {
			sr = 0;
			sc = 0;
			var str="&nbsp;Cancel";
			fAll.tblsel.style.width = 0;
			fAll.tblsel.style.height = 0;
			return;
		}
		
		tbl=fAll.oTable;
		sr=se.parentElement.rowIndex;
		sc=se.cellIndex;
		
		//Expand the table selector if its too small
		if(version>4) {
			if(tbl.rows.length == sr+1) {
				var r = tbl.insertRow(-1);
				var td;
				for(var i=0;i<tbl.rows(1).cells.length;i++) {
					td = r.insertCell(-1);
					td.innerHTML = "&nbsp;";
					td.style.pixelWidth = 20;
					td.style.pixelHeight = 20;
					td.style.border='1px solid #666666';
					td.onMouseOut  = startTimeTable(); 
					td.onMouseOver = endTimeTable();
				}
				var bdy = document.frames['tableWizard'].document.body;			
				var ifrm = document.frames['tableWizard'];
					
				document.all['tableWizard'].style.pixelWidth = ifrm.document.all.oTable.offsetWidth + 3;
				document.all['tableWizard'].style.pixelHeight = ifrm.document.all.oTable.offsetHeight + 3 + ifrm.document.all.tblstat.offsetHeight;
			}
			if(tbl.rows(1).cells.length == sc+1) {
				var td;
				for(var i=0;i<tbl.rows.length;i++) {
					td 					= tbl.rows(i).insertCell(-1);
					td.innerHTML 		= "&nbsp;";
					td.style.pixelWidth = 20;
					td.style.pixelHeight= 20;
					td.style.border		= '1px solid #666666';
					td.onMouseOut  = startTimeTable(); 
					td.onMouseOver = endTimeTable();
				}			
				var bdy = document.frames['tableWizard'].document.body;
				document.all['tableWizard'].style.pixelWidth = bdy.createTextRange().boundingWidth + 5;
				document.all['tableWizard'].style.pixelHeight = bdy.createTextRange().boundingHeight + 5;
			}
		}
		var str	=	(sr+1) + " x " + (sc+1) + " Table";
		fAll.tblsel.style.width		= se.offsetWidth*(sc+1)+5;
		fAll.tblsel.style.height	= se.offsetHeight*(sr+1)+5;
		fAll.tblstat.innerHTML		= str;
	}
	
	function insertTable() {
		//drop down table implementation
		var se = document.frames('tableWizard').window.event.srcElement;
		if(se.tagName!='TD') {
			cancelTable();
			return "";
		}
		NumRows = se.parentElement.rowIndex + 1;
		NumCols = se.cellIndex + 1;
		cancelTable();
		LIBERTAS_table_create_click(myEditor, null, NumRows, NumCols);
	}
	
	
	function startTimeTable(){
		clearTimeout(CloseTableParameter);
		CloseTableParameter = setTimeout("parent.close_the_dropdown()",2000);
	}
	function endTimeTable(){
		clearTimeout(CloseTableParameter);
	}
	function close_the_dropdown(){
		clearTimeout(CloseTableParameter);
		cancelTable();
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	 DHTML Editor
	 
	 Toolbar Configuration functions
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function xLIBERTAS_cleanup_click(editor, sender, proceed){
		if (proceed==undefined){
			proceed = false;
		}
		if (!proceed){
			proceed = confirm('Performing this action will remove all styles, fonts and useless tags from the current content. Some or all your formatting may be lost.');
		}
		if (proceed){
			var params = new Object();
			params.sender 	= sender;
			params.editor 	= editor;
			params.win		= window;
			showModelessDialog('/libertas_images/editor/libertas/dialogs/default_blank.php', params, "dialogWidth:330px; dialogHeight:165px; scroll:no; status:no; help:no;" );
		}
	}
	function LIBERTAS_cleanup_click(editor, sender, proceed){
		if (proceed+""=="undefined"){
			proceed = false;
		}
		if (!proceed){
			proceed = confirm('Performing this action will remove all styles, fonts and useless tags from the current content. Some or all your formatting may be lost.');
		}
		if (proceed){	
			try{
	//			popup_blocker_disabled = launch_progress_bar();
			} catch(e){
			
			}
			//window.frames[editor+'_rEdit'].focus();		 
			myEditor = editor;
			var selection = _Libertas_GetSelection();
			selection.innerHTML  = remove_name_spaces(""+selection.innerHTML);		
			if (selection.text==""){
				var found = true;
				while (found){
					found = false;
					var els = window.frames[editor+'_rEdit'].document.body.all;
					for (i=0; i<els.length; i++){
						// remove tags with urns set
						if ((els[i].tagUrn+'' != "null" && els[i].tagUrn+'' != "undefined" && els[i].tagUrn+'' != '') ||
							(els[i].scopeName+'' != "null" && els[i].scopeName+'' != "undefined" && els[i].scopeName+'' != '' && els[i].scopeName+'' != 'HTML') ||
							 els[i].tagName+"" == "undefined"){
							els[i].removeNode(false);
							found = true;
						} else {
							if (els[i].tagName != null && (els[i].tagName == "FONT" || els[i].tagName == "SPAN" || els[i].tagName == "DIV")){
								els[i].removeNode(false);
					 			found = true;
							}
						}
					}			
		 		}
			
				// remove styles
				var els = window.frames[editor+'_rEdit'].document.body.all;
				for (i=0; i<els.length; i++){
					// remove style and class attributes from all tags
					els[i].removeAttribute("className",0);
					els[i].removeAttribute("style",0);
				}
			} else {
				var elements 	= this[editor+'_rEdit'].document.createElement("span");
				elements.innerHTML = selection.htmlText;
				var found = true;
				while (found){
					found = false;
					var els = elements.all;
					for (i=0; i<els.length; i++){
						if ((els[i].tagUrn+'' != "null" && els[i].tagUrn+'' != "undefined" && els[i].tagUrn+'' != '') || (els[i].scopeName+'' != "null" && els[i].scopeName+'' != "undefined" && els[i].scopeName+'' != '' && els[i].scopeName+'' != 'HTML') || els[i].tagName+"" == "undefined"){
							els[i].removeNode(false);
							found = true;
						} else {
							if (els[i].tagName != null && (els[i].tagName == "FONT" || els[i].tagName == "SPAN" || els[i].tagName == "DIV")){
								els[i].removeNode(false);
								found = true;
				 			}
						}
					}			
				}
				// remove styles
				var els = elements.all;
				if(els+"" !="null"){
					for (i=0; i<els.length; i++){
						els[i].removeAttribute("className",0);
						els[i].removeAttribute("style",0);
					}
				}
				try{
					selection.pasteHTML(elements.innerHTML);
				} catch(e){
					alert("sorry we were unable to process the action, this can be caused by attempting to paste content into multiple cells at once");
				}
			}
			removeEmptyLinks(editor);
					
			close_progress_bar();
		}
	} // LIBERTAS_cleanup_click

	function LIBERTAS_acronym(editor,sender){
		myEditor=editor;
		page_ptr	= _Libertas_GetSelection();
//		page_ptr.collapse(true);
//		page_ptr.expand("word");
		if (page_ptr.parentElement){
			element_ptr = _Libertas_GetElement(page_ptr.parentElement(),"ACRONYM");
		} else {
			element_ptr = _Libertas_GetElement(page_ptr.item(0),"ACRONYM");
		}
		attribute = new Array();
		if(element_ptr){
			attribute.title	= element_ptr.title;
			attribute.txt	= element_ptr.innerHTML;
		} else {
			var selection = _Libertas_GetSelection();
			selection.collapse(true);
			selection.expand("word");
			selection.select();
			if (selection.parentElement){
				element_ptr = _Libertas_GetElement(selection.parentElement(),"ACRONYM");
			} else {
				element_ptr = _Libertas_GetElement(selection.item(0),"ACRONYM");
			}
			if (element_ptr){
				attribute.title	= element_ptr.title;
				attribute.txt	= element_ptr.innerHTML;
			} else {
				attribute.title	= "";
				attribute.txt	= selection.text ;
			}
		}
		window.frames[editor+'_rEdit'].focus();		 
		acronym_data = showModalDialog('/libertas_images/editor/libertas/dialogs/acronym.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href), attribute, "width=500,height=275,toolbars=no,statusbar=no;");
		if (acronym_data){
			window.frames[editor+'_rEdit'].focus();		 
			if(element_ptr){
				if(acronym_data.title==""){
					element_ptr.outerHTML = acronym_data.txt;		
				} else {
					element_ptr.title = acronym_data.title;
					element_ptr.innerHTML = acronym_data.txt;		
				}
			} else {
				var selection = _Libertas_GetSelection();
				selection.collapse(true);
				selection.expand("word");
				if(acronym_data.title!=""){
					if (acronym_data.txt.charAt(acronym_data.txt.length)==" "){
						acronym_data.txt = acronym_data.txt.substring(0,acronym_data.txt.length-1);
					}
					selection.pasteHTML("<ACRONYM title='"+acronym_data.title+"'>"+acronym_data.txt+"</ACRONYM>&nbsp;");		
				} else {
					selection.pasteHTML(acronym_data.txt+"&nbsp;");		
				}
			}
	 	}
		myEditor='';
	}

	function LIBERTAS_abbr(editor,sender){
		myEditor=editor;
		page_ptr	= _Libertas_GetSelection();
		if (page_ptr.parentElement){
			element_ptr = _Libertas_GetElement(page_ptr.parentElement(),"ABBR");
		} else {
			element_ptr = _Libertas_GetElement(page_ptr.item(0),"ABBR");
		}
		attribute = new Array();
		if(element_ptr){
			
			attribute.title	= element_ptr.title;
			attribute.txt	= element_ptr.innerHTML;
		} else {
			attribute.title	= "";
			attribute.txt	= "";
		}
		window.frames[editor+'_rEdit'].focus();		
		abbr_data = showModalDialog('/libertas_images/editor/libertas/dialogs/abbr.php?lang=' + document.all['LIBERTAS_'+editor+'_lang'].value + '&theme=' + document.all['LIBERTAS_'+editor+'_theme'].value +'&'+ session_url+'&base_href='+escape(base_href), attribute, "width=500,height=275,toolbars=no,statusbar=no;");
		if (abbr_data){
			window.frames[editor+'_rEdit'].focus();		 
			var selection = _Libertas_GetSelection();
			if(element_ptr){
				element_ptr.title = abbr_data.title;
				element_ptr.innerHTML = abbr_data.txt;		
			} else {
				myabbr = document.createElement("ABBR");
				myabbr.title = abbr_data.title;
				myabbr.innerHTML =abbr_data.txt;
				selection.pasteHTML("[[ls]]"+myabbr.outerHTML+"[[ls]]");
				window.frames[editor+'_rEdit'].focus();		 
				htmltext  = new String(window.frames[editor+'_rEdit'].document.body.innerHTML);
				if (htmltext.indexOf("<P>")==-1){
					htmltext ="<P>&nbsp;"+htmltext+" </P>";
				}
				while(htmltext.indexOf("[[ls]]<ABBR")!=-1){
					htmltext = htmltext.replace("[[ls]]<ABBR"," <ABBR");
				}
				while(htmltext.indexOf("</ABBR>[[ls]]")!=-1){
					htmltext = htmltext.replace("</ABBR>[[ls]]","</ABBR> ");
				}
				window.frames[editor+'_rEdit'].document.body.innerHTML= htmltext;
			}
	 	}
		myEditor='';
	}
	
	function removeEmptyLinks(editor){
		var found = true;
		while (found){
			found = false;
			var els = window.frames[editor+'_rEdit'].document.body.all;
			for (i=0; i<els.length; i++){
				// remove tags with urns set
//				alert(els[i].href);
				if (els[i].tagName+'' == "A"){
					if (els[i].innerHTML=="" && (els[i].name+""=="" || els[i].name+""=="undefined")){
						els[i].removeNode(false);
						found = true;
					}
				}
			}			
 		}
	}