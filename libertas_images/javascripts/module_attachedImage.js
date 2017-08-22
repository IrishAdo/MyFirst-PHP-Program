/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   -   A T T A C H E D F I L E . J S 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this file will allow the selection of images 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	author : Adrian Sweeney
	-	Modified $Date: 2004/10/02 12:10:14 $
	-	$Revision: 1.1 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
function attachedFile(list, output){
	// properties
	this.list	= list;
	this.output = output;
	
	//methods
	this.retrieve	= __af_retrieveimage;
	this.remove		= __af_remove;
	this.draw		= __af_draw;
	this.tidy		= __af_convert_special_characters;
	// after initialisation do this
	this.draw();
}
function __af_remove(t,index){
	f= get_form();
	eval("f.fileattachedid"+index+".value = '';");
	eval("f.fileattached"+index+".value ='';");	
	eval("f.eraser"+index+".disabled=true;");
	eval("f.fileattachedimg"+index+".src='/libertas_images/themes/1x1.gif';");
	eval("f.add"+index+".value='Select Image';");
}
function __af_retrieveimage(t,index){
	f= get_form();
	var retrieveImage = showModalDialog('/libertas_images/editor/libertas/dialogs/associate_image.php?product='+product_version+'&base_href='+escape(base_href)+'&LEI='+session_url, t, 'dialogHeight:200px; dialogWidth:366px; resizable:no; status:no');	
	if (retrieveImage){
		eval("f.fileattachedid"+index+".value = retrieveImage.id;");
		eval("f.fileattachedimg"+index+".src = retrieveImage.source;");
		eval("f.fileattached"+index+".value = retrieveImage.alt.split('&#39;').join(\"'\").split('%28').join(\"(\").split('%29').join(\")\").split('%26').join(\"&\").split('&amp;').join(\"&\");");
		eval("f.eraser"+index+".disabled=false;");
		eval("f.add"+index+".value='Replace Image';");
	}
}

function __af_draw(){
	var sz="";
	for(var i=0; i<this.list.length; i++){
		if(this.list[i][3]+""=="undefined"){
			this.list[i][3] ="";
			this.list[i][4] ="";
		}
		if(this.list[i][5]==""){
			this.list[i][5]="/libertas_images/themes/1x1.gif";
		}
		sz+="<div><br /><p><strong>"+this.list[i][3]+"</strong></p>";
		sz+="<input type='hidden' name='file_attached_container[]' value='"+this.list[i][4]+"'/>";
		sz+="<input type='hidden' name='file_attached_identifier[]' id='fileattachedid"+i+"' value='"+this.list[i][1]+"'/>";
		sz+="<input type='hidden' name='file_attached_label[]' id='fileattached"+i+"' value='";
		sz+=""+this.tidy(this.list[i][0],false)+"'/><img src='"+this.list[i][5]+"' id='fileattachedimg"+i+"'/></div>";
		sz+="<div ><input type='button' style='width:130px;' id='eraser"+i+"' ";
		if(this.list[i][1]+""==""){
			sz+="disabled='true'";
		}
		sz+=" value='Erase Image' class='bt' name='remove' onclick='attached_files.remove(this, \""+i+"\")'/>";
		if (this.list[i][5]=="/libertas_images/themes/1x1.gif"){
			sz+="<input type='button' value='Select Image' class='bt' name='add"+i+"' onclick='attached_files.retrieve(this, \""+i+"\")' style='width:130px;' />";
		} else {
			sz+="<input type='button' value='Replace Image' class='bt' name='add"+i+"' onclick='attached_files.retrieve(this, \""+i+"\")' style='width:130px;' />";
		}
		sz+="</div>";
	}
	var so = document.getElementById(this.output);
	so.innerHTML = sz;
}

function __af_convert_special_characters(str, keepEntities){
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
			new Array("apos", "#39", "'"),
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
			new Array("[[amp]]","&"),
			new Array("[[apos]]","'"),
			new Array("[[pos]]","'"),
			new Array("[[pound]]","£"),
			new Array("[[quot]]",'"')
		);
		var changeCharacterlist = Array(
			new Array("%28","("),
			new Array("%29",")"),
			new Array("%26","&")
		);
//	alert(str);

		if (keepEntities){
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- REMOVE LIST OF SPECIAL CHARACTERS ABOVE
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			
				for (var i=0; i < changeCharacterlist.length; i++){
					if (str.indexOf(changeCharacterlist[i][0])!=-1){
						while (str.indexOf(changeCharacterlist[i][0])!=-1){
							str = str.replace(changeCharacterlist[i][0], changeCharacterlist[i][1]);
						}
					}
				}
			
			if (str.indexOf("[[")!=-1 && str.indexOf("]]")!=-1){
				for (var i=0; i < fakeCharacterlist.length; i++){
					if (str.indexOf(fakeCharacterlist[i][0])!=-1){
						while (str.indexOf(fakeCharacterlist[i][0])!=-1){
							str = str.replace(fakeCharacterlist[i][0], fakeCharacterlist[i][1]);
						}
					}
				}
			}
			if (str.indexOf("&")!=-1 && str.indexOf(";")!=-1){
				for (var x=0; x < 2; x++){
					for (var i=0; i < characterlist.length; i++){
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
