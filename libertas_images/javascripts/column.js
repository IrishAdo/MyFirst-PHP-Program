function setTall() {
	if (document.getElementById) {
		var divs = new Array(document.getElementById('position2'), document.getElementById('position4'), document.getElementById('position1'));
		var maxHeight = 0;				
		for (var i = 0; i < divs.length; i++) {
			if(divs[i] != null && divs[i].tagName == "DIV"){
				if (divs[i].offsetHeight > maxHeight) maxHeight = divs[i].offsetHeight;
			}	
		}
		for (var i = 0; i < divs.length; i++) {
			if(divs[i] != null && divs[i].tagName == "DIV"){
				divs[i].style.height = maxHeight + 'px';
	if (divs[i].offsetHeight > maxHeight) {
					divs[i].style.height = (maxHeight - (divs[i].offsetHeight - maxHeight)) + 'px';
				}
			}	
		}
	}
}