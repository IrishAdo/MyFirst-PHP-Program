var hiddenframeProgressDescriptionPtr	= document.getElementById("hiddenframeProgressDescription");
var hiddenframeProgressBarPtr 			= document.getElementById("hiddenframeProgressBar");
hiddenframeProgressDescriptionPtr.innerHTML = "Current Progress 0%";

function statusBarUpdate(p){
	var width = (3 * p)+"";
/*	if (width.indexOf(".")!=-1){
		percentage = width.substr(0,width.indexOf("."));
	} else {
		percentage = width;
	}
	*/
	if (p>100){
		p=100;
	}
	if (width>300){
		width=300;
	}
	hiddenframeProgressBarPtr.innerHTML = "<div style='width:"+width+"px;border:1px solid black;background-color:#0000ff;height:15px;'>&nbsp;</div>" ;
	hiddenframeProgressDescriptionPtr.innerHTML = "Current Progress " + p +"%";
}
