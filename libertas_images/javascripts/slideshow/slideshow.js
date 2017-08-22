/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   -   S L I D E S H O W . J S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Display an embedded image slide show on this page
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	author : Adrian Sweeney
	-	Modified $Date: 2004/09/07 07:24:45 $
	-	$Revision: 1.4 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	

var locationArray 	= new String(window.location).split("/");
var slides 			= new Array();
find_slideshows();

function slideshow_update(index){
	slides[index].refresh()
}

function find_slideshows(){
	for (i=0;i<document.images.length;i++){
		if (document.images[i].id.substring(0,9)=="slideshow"){
			pos = slides.length;
			slides[pos] = new SlideShow(document.images[i].id,document.images[i], pos, i );
		}
	}
	if (slides.length){
		for (i=0;i<slides.length;i++){
			slides[i].start();
		}
	}
}
/*
Class for slideshow
*/
function SlideShow(myid, src, identifier, DOMimageIndex){
	/*
		Properties
	*/
	this.imageCache 			= new Array();
	this.identifier				= identifier;
	this.DOMimageIndex			= DOMimageIndex;
	this.slideshow_id			= myid;
	this.time_delay				= 3000;
	this.cached					= 0;
	this.status 				= "stopped";
	this.currentImageIndex 		= 0;
	this.SetDefault				= 0;
	this.debugCode 					= false;
	try{
		
		if (src.time_delay+""!="undefined" || src.time_delay+""!="null"){
			this.time_delay				= src.getAttribute("time_delay") * 1000;
		}
		if (src.parameters+""!="undefined" || src.parameters+""!="null"){
			this.imageList 				= src.getAttribute("parameters").split("::");
		}
		if (src.paramtitle+""!="undefined" || src.paramtitle+""!="null"){
			this.imageListTitle			= src.getAttribute("paramtitle").split("::");
		}
	} catch(e){
		this.SetDefault = 1;
	}
	if (this.SetDefault==1){
		try{
			this.SetDefault=0;
			spanobj 				= document.getElementById("js"+myid);
			//this.imageList 			= src.getAttribute("parameters").split("::");
			aList = new String(spanobj.innerHTML).split("\"");
			for(index = 0; index < aList.length; index++){
				if (aList[index].indexOf("time_delay")!=-1){
					this.time_delay			= aList[index+1] * 1000;
				}
				if (aList[index].indexOf("parameters")!=-1){
					this.imageList 			= new String(aList[index+1]).split("::");
				}
				
				if (aList[index].indexOf("paramtitle")!=-1){
					this.imageListTitle 			= new String(aList[index+1]).split("::");
				}
			}
		} catch(e) {
			this.SetDefault = 1;
		}
	}
	if (this.SetDefault == 1){
		if (src!='null'){
			this.imageList 				= new Array(src.src);
			this.imageListTitle 		= new Array(src.alt);
		} else {
			this.imageList 				= new Array();
			this.imageListTitle 		= new Array();
		}
	}
//	alert(this.imageList[0]);
//	alert(this.imageListTitle[0]);
	
	this.currentPic 			= new Image();
	this.currentPic.src 		= this.imageList[0];



	/*
		Methods
	*/
	this.start 					= __slideshow_start;
	this.stop 					= __slideshow_stop;
	this.refresh 				= __slideshow_refresh;
	this.cache_images			= __cache_images;
	if (this.cached==0){
		this.cache_images();
		this.cached=1;
	}
}
function __slideshow_start(){
	this.status = "started";
	setInterval("slideshow_update("+this.identifier+")", this.time_delay)
}
function __slideshow_stop(){
	this.status = "stopped";
}
function __slideshow_refresh(){
	if (this.status == "started"){
//		alert('str:'+this.imageCache[this.currentImageIndex].src);
//		alert('al:'+this.imageCache[this.currentImageIndex].alt);
		
		document.images[this.DOMimageIndex].src = this.imageCache[this.currentImageIndex].src;
		document.images[this.DOMimageIndex].alt = this.imageCache[this.currentImageIndex].alt;
//		alert(document.getElementById('ttl_layer').innerHTML);
//		document.write("<div id=ee>sdfd</div>");
/*
		var dot = document.createElement("div");
		dot.id = "1324";
		dot.style = "position: relative; background-color: #000; height: 10px; width: 10px;";
		document.body.appendChild(dot);
*/
/*
		// this code was written for posible sublevel menus not used
		var newdiv = document.createElement('DIV');
		//newdiv.innerHTML = this.imageCache[this.currentImageIndex].alt;
		newdiv.innerHTML = this.imageCache[this.currentImageIndex].alt;
		document.body.appendChild(newdiv);
*/
//		document.getElementById('1324').innerHTML = this.imageCache[this.currentImageIndex].alt;
		document.getElementById('ttl_layer').innerHTML = this.imageCache[this.currentImageIndex].alt;

		this.currentImageIndex++;
		if (this.currentImageIndex>=this.imageList.length){
			this.currentImageIndex=0;
		}
	}
}

function __cache_images(){
	this.debugCode = false;
	for (var locationIndex=0; locationIndex < locationArray.length; locationIndex++){
		if (locationArray[locationIndex].indexOf("debug=javascript")!=-1){
			this.debugCode = true;
		}
	}
	
	for (position_of_new_image=0; position_of_new_image < this.imageList.length ; position_of_new_image++){
		this.imageCache[position_of_new_image] = new Image();
		
		src="/"+this.imageList[position_of_new_image];
		if (src.substring(0,5)=="/http"){
			src = src.substring(1);
		}
		if (locationArray[3].indexOf("~")!=-1){
			src = "/"+locationArray[3]+src;
		}
		if(this.debugCode){
			alert(src);
		}

		if (this.imageListTitle[position_of_new_image])
			this.imageCache[position_of_new_image].alt = this.imageListTitle[position_of_new_image];
		else
			this.imageCache[position_of_new_image].alt = "sld";
			
		this.imageCache[position_of_new_image].src = src;
	}
}