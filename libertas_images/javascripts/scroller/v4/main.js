
/*
Cross browser Marquee II- © Dynamic Drive (www.dynamicdrive.com)
For full source code, 100's more DHTML scripts, and TOS, visit http://www.dynamicdrive.com
Credit MUST stay intact
*/

//Specify the marquee's width (in pixels)
var marqueewidth="230px"
//Specify the marquee's height
var marqueeheight="15px"
//Specify the marquee's marquee speed (larger is faster 1-10)
var marqueespeed=1
//Pause marquee onMousever (0=no. 1=yes)?
var pauseit=1

//Specify the marquee's content
//Keep all content on ONE line, and backslash any single quotations (ie: that\'s great):

//var marqueecontent='<font face="Verdana">Thank you for visiting <a href="http://www.dynamicdrive.com">Dynamic Drive.</a><p>If you find this script useful, please consider linking to us by <a href="../link.htm">clicking here.</a><p>Visit our partner <a href="http://javascriptkit.com">JavaScript Kit</a> for JavaScript tutorials.</font>'


////NO NEED TO EDIT BELOW THIS LINE////////////

marqueespeed=(document.all)? marqueespeed : Math.max(1, marqueespeed-1) //slow speed down by 1 for NS
var copyspeed=marqueespeed
var pausespeed=(pauseit==0)? copyspeed: 0
var iedom=document.all||document.getElementById
var actualheight=''
var cross_marquee, ns_marquee

function populate(){
	if (iedom){
		cross_marquee=document.getElementById? document.getElementById("iemarquee") : document.all.iemarquee
		cross_marquee.style.top=parseInt(marqueeheight)+8+"px"
		cross_marquee.innerHTML=marqueecontent
		actualheight=cross_marquee.offsetHeight
	}else if (document.layers){
		ns_marquee=document.ns_marquee.document.ns_marquee2
		ns_marquee.top=parseInt(marqueeheight)+8
		ns_marquee.document.write(marqueecontent)
		ns_marquee.document.close()
		actualheight=ns_marquee.document.height
	}
	lefttime=setInterval("scrollmarquee()",50)
}
window.onload=populate

function scrollmarquee(){

if (iedom){
if (parseInt(cross_marquee.style.top)>(actualheight*(-1)+8))
cross_marquee.style.top=parseInt(cross_marquee.style.top)-copyspeed+"px"
else
cross_marquee.style.top=parseInt(marqueeheight)+8+"px"
}
else if (document.layers){
if (ns_marquee.top>(actualheight*(-1)+8))
ns_marquee.top-=copyspeed
else
ns_marquee.top=parseInt(marqueeheight)+8
}
}

if (iedom||document.layers){
with (document){
if (iedom){
write('<div style="position:relative;width:'+marqueewidth+';height:'+marqueeheight+';overflow:hidden" onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=marqueespeed">')
write('<div id="iemarquee" style="position:absolute;left:0px;top:0px;width:100%;">')
write('</div></div>')
}
else if (document.layers){
write('<ilayer width='+marqueewidth+' height='+marqueeheight+' name="ns_marquee">')
write('<layer name="ns_marquee2" width='+marqueewidth+' height='+marqueeheight+' left=0 top=0 onMouseover="copyspeed=pausespeed" onMouseout="copyspeed=marqueespeed"></layer>')
write('</ilayer>')
}
}
}
