function externalLinks() { 
	if (!document.getElementsByTagName) return; 
	var anchors = document.getElementsByTagName("a"); 
	for (var i=0; i<anchors.length; i++) { 
		var anchor = anchors[i]; 
		// comment
		try{
	 		if (
		 		anchor.getAttribute("href") && 
				anchor.getAttribute("rel") == "_libertasExternalWindow"
			){ 
				anchor.target = "_libertasExternalWindow"; 
				if (anchor.title.indexOf("external")==-1){
					anchor.title += " - (opens in external window)";
				}
			}
		} catch(e){
			//ignore errors
		}
	} 
	if(window.setTall != null){
		setTall();
	}
	setTallRSS();

} 
function setTallRSS() {
	if (document.getElementById) {
		// the divs array contains references to each column's div element.  
		// Replace 'center' 'right' and 'left' with your own.  
		// Or remove the last one entirely if you've got 2 columns.  Or add another if you've got 4!
		var uls = document.getElementsByTagName("ul"); 
		var divs = new Array();		
		var cnt = 0;
		for (var i=0; i<uls.length; i++) { 
			
			vAttrValue = uls[i].getAttribute("id");			
			if(vAttrValue+'' != 'undefined' && vAttrValue+'' !='' && vAttrValue != null)			
			{
				/*
				if(vAttrValue.substring(0,4)=="rss-"){
					divs[cnt] = eval("document.getElementById('rss19')");
					cnt++;
				}
				*/
			}
			
		}

		//var divs = new Array(document.getElementById(elementid1), document.getElementById(elementid2));
		// Let's determine the maximum height out of all columns specified
		var maxHeight = 0;
		for (var i = 0; i < divs.length; i++) {
			if (divs[i].offsetHeight > maxHeight) maxHeight = divs[i].offsetHeight;
		}
		
		// Let's set all columns to that maximum height
		for (var i = 0; i < divs.length; i++) {
			divs[i].style.height = maxHeight + 'px';

			// Now, if the browser's in standards-compliant mode, the height property
			// sets the height excluding padding, so we figure the padding out by subtracting the
			// old maxHeight from the new offsetHeight, and compensate!  So it works in Safari AND in IE 5.x
			if (divs[i].offsetHeight > maxHeight) {
				divs[i].style.height = (maxHeight - (divs[i].offsetHeight - maxHeight)) + 'px';
			}
		}
	}
}
window.onload = function() { 
externalLinks(); 
}
window.onresize = function() {
	if(window.setTall != null){
		setTall();
	}
}