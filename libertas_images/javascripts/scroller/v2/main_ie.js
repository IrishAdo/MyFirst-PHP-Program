var mc=titlea.length;
var inoout=false;
var tmpv;
	tmpv=180-8-8-2*parseInt(1);
var cvar=0,say=0,tpos=0,enson=0,hidsay=0,hidson=0;

divtextb ="<div id=d";
divtev1=" onmouseover=\"mdivmo(";
divtev2=")\" onmouseout =\"restime(";
divtev3=")\" ";
divtev4="";
divtexts = " style=\"position:absolute;visibility:hidden;width:"+tmpv+"; background:#FFFFFF; COLOR: #000000; left:0; top:0; FONT-FAMILY: MS Sans Serif; FONT-SIZE: 8pt; FONT-STYLE: normal; FONT-WEIGHT: normal; TEXT-DECORATION: none; margin:0px; overflow-x:hidden; LINE-HEIGHT: 12pt; text-align:left;padding:0px; cursor:'default';\">";
ie6span= " style=\"position:relative;background: #FFFFFF; COLOR: #414A76; width:"+tmpv+"; FONT-FAMILY: Book Antiqua; FONT-SIZE: 9pt; FONT-STYLE: normal; FONT-WEIGHT: bold; TEXT-DECORATION: none; LINE-HEIGHT: 14pt; text-align:left;padding:0px;\"";

uzun="<div id=\"enuzun\" style=\"position:absolute;background: #FFFFFF;left:0;top:0;\">";
var uzunobj=null;
var uzuntop=0;
var toplay=0;


function mdivmo(gnum)
{
	inoout=true;

	if((linka[gnum].length)>2)
	{
	objd=eval("d"+gnum);
	objd2=eval("hgd"+gnum);

	objd.style.color="#8E0606";
	objd2.style.color="#B90000";

	objd.style.cursor='hand';
	objd2.style.cursor='hand';

}
	window.status=" Unregistered version, visit: www.news-scroller.com/  ";

}

function restime(gnum2)
{
	inoout=false;
	objd=eval("d"+gnum2);
	objd2=eval("hgd"+gnum2);

	objd.style.color="#000000";
	objd2.style.color="#414A76";

	window.status="";

}

function butclick(gnum3)
{
//buildergenlink


}

function dotrans()
{
	if(inoout==false){
	uzuntop--;
	if(uzuntop<(-1*toplay))
	{
		uzuntop=15;
	}

	enuzun.style.pixelTop=uzuntop;
}
setTimeout('dotrans()',35);


}

function initte2()
{
	i=0;
	for(i=0;i<mc;i++)
	{
		objd=eval("d"+i);
		heightarr[i]=objd.offsetHeight;
	}

	toplay=4;
	for(i=0;i<mc;i++)
	{
		objd=eval("d"+i);
		objd.style.visibility="visible";
		objd.style.pixelTop=toplay;
		toplay=toplay+heightarr[i]+10;

	}


	enuzun.style.left=8+"px";
	enuzun.style.height=toplay+"px";
	enuzun.style.width=tmpv+"px";
	uzuntop=15;



	dotrans();

}

function initte()
{
	i=0;
	innertxt=""+uzun;
	for(i=0;i<mc;i++)
	{
		innertxt=innertxt+""+divtextb+""+i+""+divtev1+i+divtev2+i+divtev3+i+divtev4+divtexts+"<span id=\"hgd"+i+"\""+ie6span+">"+titlea[i]+"</span><br>"+texta[i]+"</div>";
	}
	innertxt=innertxt+"</div>";
	spageie.innerHTML=""+innertxt;
	setTimeout('initte2()',500);

}




window.onload=initte;