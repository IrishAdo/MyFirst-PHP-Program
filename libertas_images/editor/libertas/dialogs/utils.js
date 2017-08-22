/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/10/02 12:12:03 $
	-	$Revision: 1.5 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	

function resizeDialogToContent(w,h){
  	if ((w+""!="undefined")&&(h+""!="undefined")){
		window.dialogWidth =  w + 'px';
		window.dialogHeight = h + 'px';
		
	}else {
	  	if (w+""=="undefined"){
		    var dw = window.dialogWidth;
		}else {
		    var dw = w;
		}
  		if (h+""=="undefined"){
	    	var dh = window.dialogHeight;
		}else {
		    var dh = h;
		}
    	// resize window so there are no scrollbars visible
		if (window.dialogWidth + '' != 'undefined'){
		    while (isNaN(dw)){
	    	  dw = dw.substr(0,dw.length-1);
		    }	
		    difw = dw - this.document.body.clientWidth;
	    	window.dialogWidth = this.document.body.scrollWidth+difw+'px';
		}
	    if (window.dialogHeight + '' != 'undefined'){
		    while (isNaN(dh)){
		      dh = dh.substr(0,dh.length-1);
	    	}
	   		difh = dh - this.document.body.clientHeight;
		    window.dialogHeight = this.document.body.scrollHeight+difh+'px';
		}
	}
//	alert(window.dialogWidth + " " + window.dialogHeight);
  }
  
  function ShowImage(t){
	src = t.all.imagesrc.options[t.all.imagesrc.selectedIndex].value.split("::");
	t.all.imagepreview.src = base_href+src[0];
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
	t.all.imagesize.innerHTML = ""+str;
	tmp =src[3];
  	t.all.imagealt.value=""+t.all.imagesrc.options[t.all.imagesrc.selectedIndex].text;
}

function save_image(){
	source = document.all.imagesrc.options[document.all.imagesrc.selectedIndex].value.split("::");
	md_arr=source[0].split("/");
	f = md_arr[md_arr.length-1];
	t = f.split(".");
	md5 = t[0];
	var item = {};
	if (document.all.imagealign.value!=""){
		item.styleDefinitionfloat= document.all.imagealign.value;
	}
	item.styleDefinitionmarginleft=document.all.imagespacing.value+"px";
	item.styleDefinitionmarginright=document.all.imagespacing.value+"px";
	item.styleDefinitionmargintop=document.all.imagespacing.value+"px";
	item.styleDefinitionmarginbottom=document.all.imagespacing.value+"px";
	item.source 			 = base_href+source[0];
	item.alt				 = new String(escape(document.all.imagealt.value)).split("%20").join(" ").split('%22').join('&#34;').split('%27').join('&#39;');
	item.longdesc			 = "?command=FILES_INFO&identifier="+md5;
	item.width="";
	item.height="";
	if (source[1]>0 && source[2]>0 ){
		item.width=source[1];
		item.height=source[2];
	}	
	return item	;
}

function convert_special_characters(str, keepEntities){
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
			new Array("Iuml", "#207", "Ï")/*,
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
*/
		);
		var fakeCharacterlist = Array(
			new Array("[[amp]]","&"),
			new Array("[[apos]]","'"),
			new Array("[[pos]]","'"),
			new Array("[[pound]]","£"),
			new Array("[[quot]]",'"'),
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
