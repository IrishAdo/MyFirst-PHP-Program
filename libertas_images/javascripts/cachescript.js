/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-														C A C H E S C R I P T . J S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 		A file to allow the inclusion of cache functionality on a page original source taken from the WYSIWYG Editor and adapted to run in 
- standalone mode.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-				$Revision: 1.2 $, 
-				$Date: 2004/04/23 15:39:05 $
-				$Author: adrian $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	What modules this is used in :- 
-			This file is used in RSS_ADMIN to select the channel Image
-
- Formatting used
-
-	all methods of the class will use the following format two under scores followed by the initials of the class in question followed by a 
-	single underscore followed by the function name 
-	
-	in otherwords the source for the function CacheScript.toString() would be found in the function __CS_toString();
-
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-														C A C H E S C R I P T   C L A S S
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function CacheScript(){ 
	// properties of class
	this.cache_command			= "";
	this.cache_type				= "image";
	this.cache_format			= "RSS";
	this.cache_label			= "";
	this.cache_filters			= new Array();
	this.outputWindow			= document.getElementById("CacheScriptDiv");
	this.myCategoryList 		= Array();
	this.dhtml		 			= new DhtmlScript();
	this.pageForm 				= get_form();
	
	// methods of class
	this.toString				= __CS_toString;
	this.display				= __CS_display;
	this.ShowFilter				= __CS_ShowFilter;
	this.ShowImageList			= __CS_ShowImageList;
	this.ShowImage				= __CS_ShowImage;
	this.extract_information	= __CS_extract_information;
	this.define_pulldown		= __CS_define_pulldown;
	this.get_images				= __CS_get_images;
	this.convertCharacters		= __CS_convert_special_characters;
	this.get_categories			= __CS_get_categories;
	this.selectClick			= __CS_selectClick;
	this.change					= __CS_changeImage;
	this.remove					= __CS_removeImage;
	this.cancel					= __CS_cancelClick;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	FN:: __CS_toString()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __CS_toString(){
	var output_string	 = "";
	output_string		+= "Cache Command : "+this.cache_command+" \n";
	output_string		+= "Filters : {\n";
	for(var i=0; i<this.cache_filters.length;i++){
		output_string		+= i+":: "+this.cache_filters[i]+"\n";
	}
	output_string		+= "}\n";
	return output_string;
}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	FN:: __CS_display()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function __CS_display(){
	switch(this.cache_type){
		case "image":
			if(product_version=="ECMS"){
				var r = this.outputWindow.insertRow(-1);
				var c = r.insertCell(-1);
					c.innerHTML = "<strong>Filter Type</strong>";
				var c = r.insertCell(-1);
					str = "<select name='filterType' style='width:200px' onChange='mycache.ShowFilter();'>";
					str += "	<option>Filter by Date</option>";
					str += "	<option>Filter by Category</option>";
					str += "</select>";
					c.innerHTML = str;
			}
			var r = this.outputWindow.insertRow(-1);
			var c = r.insertCell(-1);
				c.innerHTML = "<strong>Filter Options</strong>";
			var c = r.insertCell(-1);
				str = 	"<select id='filterOption' name='filterOption' style='width:200px' onChange='mycache.ShowImageList(0);'>"+
						"	<option>Loading filters.....</option>"+
						"</select>";
				c.innerHTML = str;
			var r = this.outputWindow.insertRow(-1);
			var c = r.insertCell(-1);
				c.innerHTML = "<strong>Select Image</strong>";
			var c = r.insertCell(-1);
				str =	"<select id='imagesrc' name='imagesrc' style='width:200px' onChange='mycache.ShowImage(this.document);'>"+
						"	<option>Select filter option first .....</option>"+
						"</select>";
				c.innerHTML = str;
			var r = this.outputWindow.insertRow(-1);
			var c = r.insertCell(-1);
				c.innerHTML = "<p>Preview</p><img border='1' src='/libertas_images/themes/1x1.gif' width='130' height='130' id='imagepreview'>";
			var c = r.insertCell(-1);
				str = "<table>"
				if(this.cache_format!="RSS"){
				str +=	"<tr>"+
						"	<td colspan='2'><strong>Alt Tag information</strong></td>"+
						"</tr>"+
						"<tr>"+
						"	<td colspan='2'>"+
						"	<input type=text id='imagealt' size=255 style='width:230px'>"+
						"	<input type=hidden id='imageexisting' value=''></td>"+
						"</tr>"+
						"	<tr>"+
						"		<td><strong>Alignment </strong></td>"+
						"		<td>"+
						"		<select id='imagealign' style='width:150'>"+
						"			<option value=''>None</option>"+
						"			<option value='left'>Left</option>"+
						"			<option value='center'>Center</option>"+
						"			<option value='right'>Right</option>"+
						"		</select></td>"+
						"	</tr>"+
						"	<tr>"+
						"		<td><strong>Spacing </strong></td>"+
						"		<td>"+
						"		<select id='imagespacing' style='width:150'>"+
						"			<option value='0'>None</option>"+
						"			<option value='1'>1px</option>"+
						"			<option value='2'>2px</option>"+
						"			<option value='3' selected='true'>3px</option>"+
						"			<option value='4'>4px</option>"+
						"			<option value='5'>5px</option>"+
						"			<option value='6'>6px</option>"+
						"			<option value='7'>7px</option>"+
						"			<option value='8'>8px</option>"+
						"			<option value='9'>9px</option>"+
						"			<option value='10'>10px</option>"+
						"		</select></td>"+
						"	</tr>"
				}
				str += 	"	<tr><td id='imagesize' colspan='2' valign='top'><strong>Width:</strong> <em>X px</em>&#160;<strong>Height:</strong> <em>Y px</em><br>"+
						"			<strong>Size:</strong><br>"+
						"			<strong>Approximate Download Speeds:</strong><br>"+
						"			<em>56k:</em> X secs &#160;<em>ISDN:</em> X sec</td></tr>"+
						"	</table>";
			c.innerHTML =str
			var r = this.outputWindow.insertRow(-1);
			var c = r.insertCell(-1);
				c.setAttribute("colspan",2);
				c.innerHTML = '<input ONCLICK="mycache.selectClick()" TYPE=button class="bt" ID=idSave  VALUE="Ok"> <input ONCLICK="mycache.cancel()" TYPE=button class="bt" ID=idCancel  VALUE="Cancel">';
			break;
	}
	this.ShowFilter();
}

function __CS_ShowFilter(){
	
	if(product_version=="ECMS"){
		switch (this.pageForm.filterType.selectedIndex){
			case 0:
				opt = this.pageForm.filterOption
				opt.options.length=0;
				opt.options[opt.options.length] = new Option("Select filter option");
				opt.options[opt.options.length] = new Option("Show images less than 1 day old");
				opt.options[opt.options.length] = new Option("Show images less than 1 week old");
				opt.options[opt.options.length] = new Option("Show images less than 4 weeks old");
				opt.options[opt.options.length] = new Option("Show all images");
				
				opt = this.pageForm.imagesrc
				opt.options.length=0;
				opt.options[opt.options.length] = new Option("Select filter option first .....");//Please wait downloading list of images.")
				
				imgtag 		= document.getElementById("imagepreview");
				imgtag.src	= '/libertas_images/themes/1x1.gif';
				break
			case 1:
				opt = this.pageForm.imagesrc
				opt.options.length=0;
				opt.options[opt.options.length] = new Option("Select filter option first .....");//Please wait downloading list of images.")
				opt = this.pageForm.filterOption
				opt.options.length=0;
				opt.options[opt.options.length] = new Option("Select filter option");
				this.extract_information('category','module=FILES_');
				setTimeout("mycache.get_categories()",1000);
				imgtag 		= document.getElementById("imagepreview");
				imgtag.src	= '/libertas_images/themes/1x1.gif';
				break
		}
	} else {
		opt = this.pageForm.filterOption
		opt.options.length=0;
		opt.options[opt.options.length] = new Option("Select filter option");
		opt.options[opt.options.length] = new Option("Show images less than 1 day old");
		opt.options[opt.options.length] = new Option("Show images less than 1 week old");
		opt.options[opt.options.length] = new Option("Show images less than 4 weeks old");
		opt.options[opt.options.length] = new Option("Show all images");
		
		opt = this.pageForm.imagesrc
		opt.options.length=0;
		opt.options[opt.options.length] = new Option("Select filter option first .....");//Please wait downloading list of images.")
		
		imgtag 		= document.getElementById("imagepreview");
		imgtag.src	= '/libertas_images/themes/1x1.gif';
	}
}

function __CS_ShowImageList(actiontype){
	
	if(product_version=="ECMS"){
		if (actiontype==0){
			cat = this.pageForm.filterOption;
			switch (this.pageForm.filterType.selectedIndex){
				case 0:
					if (cat.selectedIndex==0){
					}else{
						imgtag 		= document.getElementById("imagepreview");
						imgtag.src	= '/libertas_images/themes/1x1.gif';
						opt = this.pageForm.imagesrc
						opt.options.length=0;
						opt.options[opt.options.length] = new Option("Please wait downloading list of images.")
						if (cat.selectedIndex==1){
							this.extract_information('image','date=1day');
							setTimeout("mycache.get_images()",1000);
						} else if (cat.selectedIndex==2){
							this.extract_information('image','date=1week');
							setTimeout("mycache.get_images()",1000);
						} else if (cat.selectedIndex==3){
							this.extract_information('image','date=4weeks');
							setTimeout("mycache.get_images()",1000);
						} else {
							this.extract_information('image');
							setTimeout("mycache.get_images()",1000);
						}		
					}
					break
				case 1:
					if ((cat.selectedIndex==0)||(cat.selectedIndex==1)||(cat.selectedIndex==4)){
					}else{
						imgtag 		= document.getElementById("imagepreview");
						imgtag.src	= '/libertas_images/themes/1x1.gif';
						opt = this.pageForm.imagesrc
						opt.options.length=0;
						opt.options[opt.options.length] = new Option("Please wait downloading list of images.")
						if (cat.selectedIndex==2){
							this.extract_information('image');
							setTimeout("mycache.get_images()",1000);
						} else if (cat.selectedIndex==3){
							this.extract_information('image','cat=undefined');
							setTimeout("mycache.get_images()",1000);
						} else {
							this.extract_information('image','cat='+cat.options[cat.selectedIndex].value);
							setTimeout("mycache.get_images()",1000);
						}		
					}
					break
			}
		} else {
			this.extract_information('image');
			setTimeout("mycache.get_images()",1000);
		}
	} else {
		if (actiontype==0){
			cat = this.pageForm.filterOption;
			if (cat.selectedIndex==0){
			}else{
				imgtag 		= document.getElementById("imagepreview");
				imgtag.src	= '/libertas_images/themes/1x1.gif';
				opt = this.pageForm.imagesrc
				opt.options.length=0;
				opt.options[opt.options.length] = new Option("Please wait downloading list of images.")
				if (cat.selectedIndex==1){
					this.extract_information('image','date=1day');
					setTimeout("mycache.get_images()",1000);
				} else if (cat.selectedIndex==2){
					this.extract_information('image','date=1week');
					setTimeout("mycache.get_images()",1000);
				} else if (cat.selectedIndex==3){
					this.extract_information('image','date=4weeks');
					setTimeout("mycache.get_images()",1000);
				} else {
					this.extract_information('image');
					setTimeout("mycache.get_images()",1000);
				}		
			}
		} else {
			this.extract_information('image');
			setTimeout("mycache.get_images()",1000);
		}
	}
}

function __CS_extract_information(szType, parameter){
	/*
		check to see if the cache is available for information yet? 
	*/
	if (cache_data.document.readyState != 'complete'){
		setTimeout("__extract_information('"+szType+"', '"+parameter+"');",1000);
		return;
	} else {
		for (var i =0; i<this.cache_filters.length;i++){
			parameter+="&"+this.cache_filters[i];
		}
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
function __CS_define_pulldown(elementparent, mydepth){
	
	for (var i=0 ; i < this.myCategoryList.length; i++){
		if (this.myCategoryList[i][0] == elementparent){
			mylabel = this.myCategoryList[i][2];
			var str="";
			for(var myx=0; myx< mydepth; myx++){
				str+= "&nbsp;-&nbsp;" 
			}
			mylabel = str + mylabel;
			this.pageForm.filterOption.options[this.pageForm.filterOption.options.length] = new Option(
				this.convertCharacters(mylabel,false), 
				this.convertCharacters(this.myCategoryList[i][1],false)
			);
			myparentelement = this.myCategoryList[i][1];
			this.define_pulldown(myparentelement , mydepth+1);
		}
	}
}

function __CS_get_images(){
	
	if (document.cache_data.document.readyState=='complete'){
		if (document.cache_data.frmDoc.image_data.value!=''){
			if (document.cache_data.frmDoc.image_data.value!='__NOT_FOUND__'){
				tmp 			= new String(document.cache_data.frmDoc.image_data.value);
				document.cache_data.frmDoc.image_data.value="";
				myArray 		= tmp.split("|1234567890|")
				var l				= myArray.length-1;
				list="";
				this.pageForm.imagesrc.options.length=0
				for (var i = 0 ; i < l ; i += 2){
					this.pageForm.imagesrc.options[this.pageForm.imagesrc.options.length] = new Option(
						this.convertCharacters(myArray[i],false), 
						this.convertCharacters(myArray[i+1],false)
					);
				}
			} else {
				alert("Sorry there are no images available");
				document.cache_data.frmDoc.image_data.value="";
			}
		} else {
			setTimeout("mycache.get_images()",1000);
		}
	} else {
		setTimeout("mycache.get_images()",1000);
	}
}
function __CS_ShowImage(t){
	
	src = this.pageForm.imagesrc.options[this.pageForm.imagesrc.selectedIndex].value.split("::");
	var imgpreview = document.getElementById("imagepreview");
	var imgsize = document.getElementById("imagesize");
	imgpreview.src = base_href+src[0];
	size_value =src[4].split(" ");
	total_size=0;
	if (size_value[0]!='0'){
		if (size_value[1]=='MB'){
			total_size=size_value[0]*(1024*1024);
		}
		if (size_value[1]=='kb'){
			total_size=size_value[0]*1024;
		}
		if (size_value[1]=='bytes'){
			total_size=size_value[0];
		}
		speed_isdn= Math.ceil(total_size / 7000)+"";
		speed_56= Math.ceil(total_size / 5500)+"";
	}else{
		speed_isdn="";
		speed_56="";
	}
	str = "<strong>Width:</strong> <em> "+src[1]+" px</em>&#160;";
	str += "<strong>Height:</strong> <em>"+src[2]+" px</em><br>";
	str += "<strong>Size:</strong> <em>"+src[4]+"</em><br>";
	str += "<strong>Approximate Download Speeds:</strong><br>";
	str += "<em>56k:</em> "+speed_56+" <small>sec(s)</small>&#160;";
	str += "<em>ISDN:</em> "+speed_isdn+" <small>sec(s)</small>";
	imgsize.innerHTML = ""+str;
	tmp =src[3];
	if(this.cache_format!="RSS"){
	  	this.pageForm.imagealt.value=""+this.pageForm.imagesrc.options[t.all.imagesrc.selectedIndex].text;
	}
}

function __CS_convert_special_characters(str, keepEntities){
	if (str+"" == "undefined"){
		 return "";
	}else{
		while (str.indexOf("&amp;amp;")!=-1){
			str.split("&amp;amp;").join("&amp;");
		}
		if (keepEntities+""=="undefined"){
			keepEntities = true;
		}
		keepEntities = true;
		var characterlist = new Array(
			new Array("nbsp", "#160", " "),
			new Array("iexcl", "#161", "¡"),
			new Array("cent", "#162", "¢"),
			new Array("pound", "#163", "£"),
			new Array("curren", "#164", "¤"),
			new Array("yen", "#165", "¥"),
			new Array("brvbar", "#166", "¦"),
			new Array("sect", "#167", "§"),
			new Array("uml", "#168", "¨"),
			new Array("copy", "#169", "©"),
			new Array("ordf", "#170", "ª"),
			new Array("laquo", "#171", "«"),
			new Array("not", "#172", "¬"),
			new Array("shy", "#173", "­"),
			new Array("reg", "#174", "®"),
			new Array("macr", "#175", "¯"),
			new Array("deg", "#176", "°"),
			new Array("plusmn", "#177", "±"),
			new Array("sup2", "#178", "²"),
			new Array("sup3", "#179", "³"),
			new Array("acute", "#180", "´"),
			new Array("micro", "#181", "µ"),
			new Array("para", "#182", "¶"),
			new Array("middot", "#183", "·"),
			new Array("cedil", "#184", "¸"),
			new Array("sup1", "#185", "¹"),
			new Array("ordm", "#186", "º"),
			new Array("raquo", "#187", "»"),
			new Array("frac14", "#188", "¼"),
			new Array("frac12", "#189", "½"),
			new Array("frac34", "#190", "¾"),
			new Array("iquest", "#191", "¿"),
			new Array("Agrave", "#192", "À"),
			new Array("Aacute", "#193", "Á"),
			new Array("Acirc", "#194", "Â"),
			new Array("Atilde", "#195", "Ã"),
			new Array("Auml", "#196", "Ä"),
			new Array("Aring", "#197", "Å"),
			new Array("AElig", "#198", "Æ"),
			new Array("Ccedil", "#199", "Ç"),
			new Array("Egrave", "#200", "È"),
			new Array("Eacute", "#201", "É"),
			new Array("Ecirc", "#202", "Ê"),
			new Array("Euml", "#203", "Ë"),
			new Array("Igrave", "#204", "Ì"),
			new Array("Iacute", "#205", "Í"),
			new Array("Icirc", "#206", "Î"),
			new Array("Iuml", "#207", "Ï"),
			new Array("ETH", "#208", "Ð"),
			new Array("Ntilde", "#209", "Ñ"),
			new Array("Ograve", "#210", "Ò"),
			new Array("Oacute", "#211", "Ó"),
			new Array("Ocirc", "#212", "Ô"),
			new Array("Otilde", "#213", "Õ"),
			new Array("Ouml", "#214", "Ö"),
			new Array("times", "#215", "×"),
			new Array("Oslash", "#216", "Ø"),
			new Array("Ugrave", "#217", "Ù"),
			new Array("Uacute", "#218", "Ú"),
			new Array("Ucirc", "#219", "Û"),
			new Array("Uuml", "#220", "Ü"),
			new Array("Yacute", "#221", "Ý"),
			new Array("THORN", "#222", "Þ"),
			new Array("szlig", "#223", "ß"),
			new Array("agrave", "#224", "à"),
			new Array("aacute", "#225", "á"),
			new Array("acirc", "#226", "â"),
			new Array("atilde", "#227", "ã"),
			new Array("auml", "#228", "ä"),
			new Array("aring", "#229", "å"),
			new Array("aelig", "#230", "æ"),
			new Array("ccedil", "#231", "ç"),
			new Array("egrave", "#232", "è"),
			new Array("eacute", "#233", "é"),
			new Array("ecirc", "#234", "ê"),
			new Array("euml", "#235", "ë"),
			new Array("igrave", "#236", "ì"),
			new Array("iacute", "#237", "í"),
			new Array("icirc", "#238", "î"),
			new Array("iuml", "#239", "ï"),
			new Array("eth", "#240", "ð"),
			new Array("ntilde", "#241", "ñ"),
			new Array("ograve", "#242", "ò"),
			new Array("oacute", "#243", "ó"),
			new Array("ocirc", "#244", "ô"),
			new Array("otilde", "#245", "õ"),
			new Array("ouml", "#246", "ö"),
			new Array("divide", "#247", "÷"),
			new Array("oslash", "#248", "ø"),
			new Array("ugrave", "#249", "ù"),
			new Array("uacute", "#250", "ú"),
			new Array("ucirc", "#251", "û"),
			new Array("uuml", "#252", "ü"),
			new Array("yacute", "#253", "ý"),
			new Array("thorn", "#254", "þ"),
			new Array("yuml", "#255", "ÿ")

		);
		var fakeCharacterlist = Array(
			new Array("[[apos]]",""),
			new Array("[[quot]]","")
		);
	
		if (keepEntities){
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- REMOVE LIST OF SPECIAL CHARACTERS ABOVE
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if (str.indexOf("&")!=-1 && str.indexOf(";")!=-1){
				for (x=0; x < 2; x++){
					for (i=0; i < characterlist.length; i++){
						while (str.indexOf("&"+characterlist[i][x]+";")!=-1){
							str = str.replace("&"+characterlist[i][x]+";",characterlist[i][2]);
						}
					}
				}
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- CHECK TO SEE IF ALL ENTITIES HAVE BEEN REMOVED
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if (str.indexOf("&")!=-1 && str.indexOf(";")!=-1){
					/*
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					- IF HERE THEN UNKNOWN ENTITIES STILL EXIST REMOVE THEM
					-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					while (str.indexOf("&")!=-1 && str.indexOf(";")!=-1){
						start	= str.indexOf("&");
						finish	= str.indexOf(";");
						str = str.substring(0,start) + str.substring(finish+1);
					}
				}
			}
		} else {
			if (str.indexOf("&")!=-1 && str.indexOf(";")!=-1){
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- IF HERE THEN ENTITIES STILL EXIST REMOVE THEM
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				while (str.indexOf("&")!=-1 && str.indexOf(";")!=-1){
					start	= str.indexOf("&");
					finish	= str.indexOf(";");
					str = str.substring(0,start) + str.substring(finish+1);
				}
			}
		}
		if (str.indexOf("&#39;")){
			while (str.indexOf("&#39;")!=-1){
				start	= str.indexOf("&#39");
				finish	= str.indexOf(";");
				str = str.substring(0,start) + str.substring(finish+1);
			}
		}
		if (str.indexOf("#39;")){
			while (str.indexOf("#39;")!=-1){
				start	= str.indexOf("#39");
				finish	= str.indexOf(";");
				str = str.substring(0,start) + str.substring(finish+1);
			}
		}
		return str;
	}
}

function __CS_get_categories(){
	
	if (document.cache_data.document.readyState=='complete'){
		if (document.cache_data.frmDoc.category_data.value!=''){
			if (document.cache_data.frmDoc.category_data.value!='__NOT_FOUND__'){
				tmp 			= new String(document.cache_data.frmDoc.category_data.value);
				myArray 		= tmp.split("|1234567890|")
				var l			= myArray.length;
				list="";
				this.pageForm.filterOption.options.length=0
				/* example of categorization feed
					format (parent id::identifier::label)
					'13::14::Images',
					'14::16::Layout Images',
					'14::18::Page Content',
					'14::15::RSS Channels',	
					'13::17::Other'
				*/
				for (var i = 0 ; i < l ; i ++){
					myArray[i] = myArray[i].split("::")
				}
				this.myCategoryList = myArray;
				rootparent	= myArray[0][0];
				depth 		= 0;
				prev_parent = 0;
				this.pageForm.filterOption.options[this.pageForm.filterOption.options.length] = new Option("Select filter option", 0);
				this.pageForm.filterOption.options[this.pageForm.filterOption.options.length] = new Option("-------------------- Special Filters --------------------", -1);
				this.pageForm.filterOption.options[this.pageForm.filterOption.options.length] = new Option("Show all Images", -1);
				this.pageForm.filterOption.options[this.pageForm.filterOption.options.length] = new Option("Show Uncategorised Images", -1);
				this.pageForm.filterOption.options[this.pageForm.filterOption.options.length] = new Option("-------------------- By Category --------------------", -1);
				this.define_pulldown(rootparent, depth);
			} else {
				alert("Sorry there are no Categories available");
				this.ShowImageList(1);
			}
		} else {
			setTimeout("mycache.get_categories()", 1000);
		}
	} else {
		setTimeout("mycache.get_categories()", 1000);
	}
}


function __CS_selectClick(){
	if (this.pageForm.imagesrc.selectedIndex!=0){
		source = this.pageForm.imagesrc.options[this.pageForm.imagesrc.selectedIndex].value.split("::");
		md_arr=source[0].split("/");
		f = md_arr[md_arr.length-1];
		t = f.split(".");
		md5 = t[0];
		this.pageForm.rss_channel_image.value = source[5];
		this.pageForm.choosenimage.src	 = base_href+source[0]
		if (source[1]>0 && source[2]>0 ){
			this.pageForm.choosenimage.width=source[1];
			this.pageForm.choosenimage.height=source[2];
		}		
		this.pageForm.removeButton.style.display='';
		this.outputWindow.style.display='none'
		this.pageForm.changeButton.value='change';
	} else {
		alert("Please select an image");
	}
}
function __CS_cancelClick(){
	this.outputWindow.style.display='none'
}

function __CS_removeImage(){
	this.pageForm.rss_channel_image.value = 0;
	this.pageForm.choosenimage.src	 = "/libertas_images/themes/1x1.gif";
	this.pageForm.choosenimage.width=1;
	this.pageForm.choosenimage.height=1;
	this.outputWindow.style.display='none'
	this.pageForm.removeButton.style.display='none';
	this.pageForm.changeButton.value='select image';
}
function __CS_changeImage(){
	this.outputWindow.style.display=''
}