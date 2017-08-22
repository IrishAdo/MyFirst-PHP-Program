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
			new Array("iexcl", "#161", "�"),
			new Array("cent", "#162", "�"),
			new Array("pound", "#163", "�"),
			new Array("curren", "#164", "�"),
			new Array("yen", "#165", "�"),
			new Array("brvbar", "#166", "�"),
			new Array("sect", "#167", "�"),
			new Array("uml", "#168", "�"),
			new Array("copy", "#169", "�"),
			new Array("ordf", "#170", "�"),
			new Array("laquo", "#171", "�"),
			new Array("not", "#172", "�"),
			new Array("shy", "#173", "�"),
			new Array("reg", "#174", "�"),
			new Array("macr", "#175", "�"),
			new Array("deg", "#176", "�"),
			new Array("plusmn", "#177", "�"),
			new Array("sup2", "#178", "�"),
			new Array("sup3", "#179", "�"),
			new Array("acute", "#180", "�"),
			new Array("micro", "#181", "�"),
			new Array("para", "#182", "�"),
			new Array("middot", "#183", "�"),
			new Array("cedil", "#184", "�"),
			new Array("sup1", "#185", "�"),
			new Array("ordm", "#186", "�"),
			new Array("raquo", "#187", "�"),
			new Array("frac14", "#188", "�"),
			new Array("frac12", "#189", "�"),
			new Array("frac34", "#190", "�"),
			new Array("iquest", "#191", "�"),
			new Array("Agrave", "#192", "�"),
			new Array("Aacute", "#193", "�"),
			new Array("Acirc", "#194", "�"),
			new Array("Atilde", "#195", "�"),
			new Array("Auml", "#196", "�"),
			new Array("Aring", "#197", "�"),
			new Array("AElig", "#198", "�"),
			new Array("Ccedil", "#199", "�"),
			new Array("Egrave", "#200", "�"),
			new Array("Eacute", "#201", "�"),
			new Array("Ecirc", "#202", "�"),
			new Array("Euml", "#203", "�"),
			new Array("Igrave", "#204", "�"),
			new Array("Iacute", "#205", "�"),
			new Array("Icirc", "#206", "�"),
			new Array("Iuml", "#207", "�"),
			new Array("ETH", "#208", "�"),
			new Array("Ntilde", "#209", "�"),
			new Array("Ograve", "#210", "�"),
			new Array("Oacute", "#211", "�"),
			new Array("Ocirc", "#212", "�"),
			new Array("Otilde", "#213", "�"),
			new Array("Ouml", "#214", "�"),
			new Array("times", "#215", "�"),
			new Array("Oslash", "#216", "�"),
			new Array("Ugrave", "#217", "�"),
			new Array("Uacute", "#218", "�"),
			new Array("Ucirc", "#219", "�"),
			new Array("Uuml", "#220", "�"),
			new Array("Yacute", "#221", "�"),
			new Array("THORN", "#222", "�"),
			new Array("szlig", "#223", "�"),
			new Array("agrave", "#224", "�"),
			new Array("aacute", "#225", "�"),
			new Array("acirc", "#226", "�"),
			new Array("atilde", "#227", "�"),
			new Array("auml", "#228", "�"),
			new Array("aring", "#229", "�"),
			new Array("aelig", "#230", "�"),
			new Array("ccedil", "#231", "�"),
			new Array("egrave", "#232", "�"),
			new Array("eacute", "#233", "�"),
			new Array("ecirc", "#234", "�"),
			new Array("euml", "#235", "�"),
			new Array("igrave", "#236", "�"),
			new Array("iacute", "#237", "�"),
			new Array("icirc", "#238", "�"),
			new Array("iuml", "#239", "�"),
			new Array("eth", "#240", "�"),
			new Array("ntilde", "#241", "�"),
			new Array("ograve", "#242", "�"),
			new Array("oacute", "#243", "�"),
			new Array("ocirc", "#244", "�"),
			new Array("otilde", "#245", "�"),
			new Array("ouml", "#246", "�"),
			new Array("divide", "#247", "�"),
			new Array("oslash", "#248", "�"),
			new Array("ugrave", "#249", "�"),
			new Array("uacute", "#250", "�"),
			new Array("ucirc", "#251", "�"),
			new Array("uuml", "#252", "�"),
			new Array("yacute", "#253", "�"),
			new Array("thorn", "#254", "�"),
			new Array("yuml", "#255", "�")

		);
		var fakeCharacterlist = Array(
			new Array("[[amp]]","&"),
			new Array("[[apos]]","'"),
			new Array("[[pos]]","'"),
			new Array("[[pound]]","�"),
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
