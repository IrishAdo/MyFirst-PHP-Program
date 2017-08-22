var mc=titlea.length;
var inoout=false;

var spage;
var cvar=0,say=0,tpos=0,enson=0,hidsay=0,hidson=0;
var tmpv;
tmpv=180-8-8-(2*1);


divtextb ="<div id=d";
divtev1=" onmouseover=\"mdivmo(";
divtev2=")\" onmouseout =\"restime(";
divtev3=")\" onclick=\"butclick(";
divtev4=")\"";
divtexts = " style=\"position:absolute;visibility:hidden;width:"+tmpv+"; background:#FFFFFF; COLOR: 000000; FONT-FAMILY: MS Sans Serif,arial,helvetica; FONT-SIZE: 8pt; FONT-STYLE: normal; FONT-WEIGHT: normal; TEXT-DECORATION: none; margin:0px; overflow-x:hidden; LINE-HEIGHT: 12pt; text-align:left;padding:0px; cursor:'default';\">";
ns6span= " style=\"position:relative;background: #FFFFFF; COLOR: 414A76; width:"+tmpv+"; FONT-FAMILY: verdana,arial,helvetica; FONT-SIZE: 9pt; FONT-STYLE: normal; FONT-WEIGHT: bold; TEXT-DECORATION: none; LINE-HEIGHT: 14pt; text-align:left;padding:0px;\"";

uzun="<div id=\"enuzun\" style=\"position:absolute;background: #FFFFFF;left:0;top:0;\">";
var uzunobj=null;
var uzuntop=0;
var toplay=0;

function mdivmo(gnum)
{
	inoout=true;

	if((linka[gnum].length)>2)
	{
		objd=document.getElementById('d'+gnum);
		objd2=document.getElementById('hgd'+gnum);

		objd.style.color="#8E0606";
		objd2.style.color="#B90000";

		objd.style.cursor='pointer';
		objd2.style.cursor='pointer';
		window.status=""+linka[gnum];
	}
}

function restime(gnum2)
{
	inoout=false;
	
	objd=document.getElementById('d'+gnum2);
	objd2=document.getElementById('hgd'+gnum2);

	objd.style.color="#000000";
	objd2.style.color="#414A76";

	window.status="";
}

function butclick(gnum3)
{
if(linka[gnum3].substring(0,11)=="javascript:"){eval(""+linka[gnum3]);}else{if((linka[gnum3].length)>3){
if((trgfrma[gnum3].indexOf("_parent")>-1)){eval("parent.window.location='"+linka[gnum3]+"'");}else if((trgfrma[gnum3].indexOf("_top")>-1)){eval("top.window.location='"+linka[gnum3]+"'");}else{window.open(''+linka[gnum3],''+trgfrma[gnum3]);}}}

}

function dotrans()
{
	if(inoout==false){

	uzuntop--;
	if(uzuntop<(-1*toplay))
	{
		uzuntop=15;
	}

	uzunobj.style.top=uzuntop+"px";
}
	setTimeout('dotrans()',35);
}


function initte2()
{
	i=0;
	for(i=0;i<mc;i++)
	{
		objd=document.getElementById('d'+i);
		heightarr[i]=objd.offsetHeight;
	}
	toplay=4;
	for(i=0;i<mc;i++)
	{
		objd=document.getElementById('d'+i);
		objd.style.visibility="visible";
		objd.style.top=""+toplay+"px";
		toplay=toplay+heightarr[i]+10;

	}

	uzunobj=document.getElementById('enuzun');
	uzunobj.style.left=8+"px";
	uzunobj.style.height=toplay+"px";
	uzunobj.style.width=tmpv+"px";
	uzuntop=15;
	dotrans();

}

function initte()
{
	i=0;
	innertxt=""+uzun;

	for(i=0;i<mc;i++)
	{
		innertxt=innertxt+""+divtextb+""+i+""+divtev1+i+divtev2+i+divtev3+i+divtev4+divtexts+"<div id=\"hgd"+i+"\""+ns6span+">"+titlea[i]+"<br></div>"+texta[i]+"</div>";
	}
	innertxt=innertxt+"</div>";
	spage=document.getElementById('spagens');
	spage.innerHTML=""+innertxt;
	setTimeout('initte2()',500);

}


window.onload=initte;