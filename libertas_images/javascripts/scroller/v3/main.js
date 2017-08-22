/***********************************************
* Pausing updown message scroller- © Dynamic Drive DHTML code library (www.dynamicdrive.com)
* This notice MUST stay intact for legal use
* Visit Dynamic Drive at http://www.dynamicdrive.com/ for full source code
***********************************************/

//configure the below five variables to change the style of the scroller
var scrollerdelay='3000' //delay between msg scrolls. 3000=3 seconds.
var scrollerwidth='250px'
var scrollerheight='30px'
var scrollerbgcolor='white'
//set below to '' if you don't wish to use a background image
var scrollerbackground='scrollerback.gif'

//configure the below variable to change the contents of the scroller
var messages=new Array()
messages[0]="<font face='Arial'><a href='http://www.dynamicdrive.com'>Click here to go back to Dynamicdrive.com frontpage</a></font>"
messages[1]="<font face='Arial'><a href='http://javascriptkit.com'>Visit JavaScriptKit for award winning JavaScript tutorials</a></font>"
messages[2]="<font face='Arial'><a href='http://www.codingforums.com'>Get help on scripting and web development. Visit CodingForums.com!</a></font>"
messages[3]="<font face='Arial'><a href='http://www.freewarejava.com'>Looking for Free Java applets? Visit Freewarejava.com!</a></font>"
messages[4]="<font face='Arial'><a href='http://dynamicdrive.com/link.htm'>If you find this script useful, please click here to link back to Dynamic Drive!</a></font>"
messages[5]="<font face='Arial'><a href='http://dynamicdrive.com/link.htm'>If you find this script to Dynamic Drive!</a></font>"
messages[6]="<font face='Arial'><a href='http://dynamicdrive.com/link.htm'>If you find this script to link back to Dynamic Drive!</a></font>"
messages[7]="<font face='Arial'><a href='http://dynamicdrive.com/link.htm'>If you find this script here to link back to Dynamic Drive!</a></font>"
messages[8]="<font face='Arial'><a href='http://dynamicdrive.com/link.htm'>If you find this script Dynamic Drive!</a></font>"

///////Do not edit pass this line///////////////////////

var ie=document.all
var dom=document.getElementById

if (messages.length>2)
i=2
else
i=0

function move1(whichlayer){
	tlayer=eval(whichlayer)
	if (tlayer.top>0&&tlayer.top<=5){
		tlayer.top=0
		setTimeout("move1(tlayer)",scrollerdelay)
		setTimeout("move2(document.main.document.second)",scrollerdelay)
		return
	}
	if (tlayer.top>=tlayer.document.height*-1){
		tlayer.top-=5
		setTimeout("move1(tlayer)",50)
	} else {
		tlayer.top=parseInt(scrollerheight)
		tlayer.document.write(messages[i])
		tlayer.document.close()
		if (i==messages.length-1)
			i=0
		else
			i++
	}
}

function move2(whichlayer){
	tlayer2=eval(whichlayer)
	if (tlayer2.top>0&&tlayer2.top<=5){
		tlayer2.top=0
		setTimeout("move2(tlayer2)",scrollerdelay)
		setTimeout("move1(document.main.document.first)",scrollerdelay)
		return
	}
	if (tlayer2.top>=tlayer2.document.height*-1){
		tlayer2.top-=5
		setTimeout("move2(tlayer2)",50)
	} else {
		tlayer2.top=parseInt(scrollerheight)
		tlayer2.document.write(messages[i])
		tlayer2.document.close()
		if (i==messages.length-1)
			i=0
		else
			i++
	}
}

function move3(whichdiv){
	tdiv=eval(whichdiv)
	if (parseInt(tdiv.style.top)>0&&parseInt(tdiv.style.top)<=5){
		tdiv.style.top=0+"px"
		setTimeout("move3(tdiv)",scrollerdelay)
		setTimeout("move4(second2_obj)",scrollerdelay)
		return
	}
	if (parseInt(tdiv.style.top)>=tdiv.offsetHeight*-1){
		tdiv.style.top=parseInt(tdiv.style.top)-5+"px"
		setTimeout("move3(tdiv)",50)
	} else {
		tdiv.style.top=parseInt(scrollerheight)
		tdiv.innerHTML=messages[i]
		if (i==messages.length-1)
			i=0
		else
			i++
	}
}

function move4(whichdiv){
	tdiv2=eval(whichdiv)
	if (parseInt(tdiv2.style.top)>0&&parseInt(tdiv2.style.top)<=5){
		tdiv2.style.top=0+"px"
		setTimeout("move4(tdiv2)",scrollerdelay)
		setTimeout("move3(first2_obj)",scrollerdelay)
		return
	}
	if (parseInt(tdiv2.style.top)>=tdiv2.offsetHeight*-1){
		tdiv2.style.top=parseInt(tdiv2.style.top)-5+"px"
		setTimeout("move4(second2_obj)",50)
	}else{
		tdiv2.style.top=parseInt(scrollerheight)
		tdiv2.innerHTML=messages[i]
		if (i==messages.length-1)
			i=0
		else
			i++
	}
}

function startscroll(){
	if (ie||dom){
		first2_obj=ie? first2 : document.getElementById("first2")
		second2_obj=ie? second2 : document.getElementById("second2")
		move3(first2_obj)
		second2_obj.style.top=scrollerheight
		second2_obj.style.visibility='visible'
	}else if (document.layers){
		document.main.visibility='show'
		move1(document.main.document.first)
		document.main.document.second.top=parseInt(scrollerheight)+5
		document.main.document.second.visibility='show'
	}
}

window.onload=startscroll


/*print_To_Id("first" ,messages[0]);
print_To_Id("second" ,messages[dyndetermine=(messages.length==1)? 0 : 1])

function print_To_Id(id, data){
	try{
		d = document.getElementById(id);
		d.innerHTML = data;
	} catch (e){
		alert("Unable to write to 'document \""+id+"\"'s innerHTML property'");
	}
}*/
