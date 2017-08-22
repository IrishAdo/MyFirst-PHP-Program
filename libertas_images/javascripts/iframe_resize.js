/**********************************************************************************************
* IFrame SSI script II 
***********************************************************************************************
* © Dynamic Drive DHTML code library (http://www.dynamicdrive.com)
* Visit DynamicDrive.com for hundreds of original DHTML scripts
* This notice must stay intact for legal use
***********************************************************************************************
*
* Input the IDs of the IFRAMES you wish to dynamically resize to match its content height:
* Separate each ID with a comma. Examples: ["myframe1", "myframe2"] or ["myframe"] or [] for none:
* 
***********************************************************************************************
* NOTE:: by Adrian Sweeney
* This will not work with frames sources that are not on the same domain as the parent script.
*	ie http:localhost frames in http://www.google.com
*	Not allowed to do this in a browser for security reasons.
**********************************************************************************************/
var iframeids=Array();


//Should script hide iframe from browsers that don't support this script (non IE5+/NS6+ browsers. Recommended):
var iframehide="no"

function resizeCaller() {
	var dyniframe=new Array();
	for (i=0; i<iframeids.length; i++){
		if (document.getElementById)
			resizeIframe(iframeids[i]);
		//reveal iframe for lower end browsers? (see var above):
		if ((document.all || document.getElementById) && iframehide=="no"){
			var tempobj=document.all? document.all[iframeids[i]] : document.getElementById(iframeids[i]);
			tempobj.style.display="block";
		}
	}
}

function resizeIframe(frameid){
	var currentfr=document.getElementById(frameid);
	if (currentfr && !window.opera){
		currentfr.style.display = "block";
		try{
			if (currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight) {
				//ns6 syntax
				val = currentfr.contentDocument.body.offsetHeight+20; 
				if(val<150){
					val=150;
				}
				currentfr.height = val;
			} else if (currentfr.Document && currentfr.Document.body.scrollHeight){
				 //ie5+ syntax
				val = currentfr.Document.body.scrollHeight+20
				if(val<150){
					val=150;
				}
				currentfr.height = val;
			}	
			if (currentfr.addEventListener){
				currentfr.addEventListener("load", readjustIframe, false);
			} else if (currentfr.attachEvent){
				currentfr.detachEvent("onload", readjustIframe); // Bug fix line
				currentfr.attachEvent("onload", readjustIframe);
			}
		}catch(e){}
	}
}

function readjustIframe(loadevt) {
	var crossevt=(window.event)? event : loadevt;
	var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement;
	if (iframeroot)
		resizeIframe(iframeroot.id);
}

function loadintoIframe(iframeid, url){
	if (document.getElementById);
		document.getElementById(iframeid).src=url;
}

if (window.addEventListener){
	window.addEventListener("load", resizeCaller, false);
	window.addEventListener("onreadystatechange", resizeCaller, false);
}else if (window.attachEvent){
	window.attachEvent("onload", resizeCaller);
	try{
		window.addEventListener("onreadystatechange", resizeCaller, false);
	} catch (e) {
		window.onreadystatechange=resizeCaller;
	}
}else{
	window.onload=resizeCaller;
	window.onreadystatechange=resizeCaller;
}