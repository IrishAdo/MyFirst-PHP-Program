/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- 													J U M P T O 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- $Date: 2004/10/04 07:56:22 $
- $Revision: 1.3 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var currentTimeout;
function jumpUrl(t){
	//alert(t.selectedIndex);
	if (t.selectedIndex>0){
		currentTimeout = setTimeout("window.location = '"+t.options[t.selectedIndex].value+"';",2000);
	} else {
		try{
			window.clearTimeout(currentTimeout);
		} catch(e){}
	}
}

function jumpToHideButton(id){
		jumpToHideButtonNow(id);
}
function jumpToHideButtonNow(id){
	el = document.getElementById(id);
	el.submitbutton.style.display='none';
} 