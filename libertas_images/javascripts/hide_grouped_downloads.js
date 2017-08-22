/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- GROUPED display enhancement
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
var UNIQUE_ID_FOR_DOWNLOAD_TABLE = new Date().getTime();
var ListOfDownloads = Array();
function downloadlist_init(){
	found=0;
	foundsecond=0;
    if (!document.getElementsByTagName) return;
    links = document.links;
//	foundsecond=0;
    for (i=0;i<links.length;i++) {
        if (((' '+links[i].href+' ').indexOf("#dl_") != -1)) {
			found++;
			id = links[i].href.substring((' '+links[i].href+' ').indexOf("#dl_"));
			myli	= document.getElementById("li"+id.substring(2));
			try{
			myli.className='downloadlinkon'
			} catch( e ){
				myli	= document.getElementById("dl"+id.substring(2));
				myli.className='downloadlinkon'
			}
//				alert(myli.tagName+" "+myli.id);
			//links[i].className= 'downloadlinkon';
			if (found!=1){
				myli.className='downloadlinkoff'
				myli.style.display='none';
//				mytab	= document.getElementById(id);
//				mytab.style.display='none';
			}
			links[i].href = "javascript:show_downloads('li"+(id.substring(2))+"', this);";
			links[i].id = "download_"+id;
        }
        if (((' '+links[i].href+' ').indexOf("#dl2_") != -1)) {
			foundsecond++;
			id = links[i].href.substring((' '+links[i].href+' ').indexOf("#dl2_"));
//			alert(id);
			myli	= document.getElementById("li"+id.substring(2));
			myli.className='downloadlinkon'
			//links[i].className= 'downloadlinkon';
			if (foundsecond!=1){
				myli.className='downloadlinkoff'
				mytab = document.getElementById(id);
				mytab.style.display='none';
				//links[i].className= 'downloadlinkoff';
			}
			links[i].href = "javascript:show_downloads('"+id+"', this);";
			links[i].id = "download_"+id;
        }
    }
}


function show_downloads(id,t){
	idlist = new String(id).split("_");
	start= idlist[0]+"_"+idlist[1];
//	alert(id);
	element = document.getElementById(id);
	if((element+""=="undefined") || (element+""=="null")){
		idlist[0]="dl"+idlist[0].substring(2);
		id ="dl"+id.substring(2);
		start= idlist[0]+"_"+idlist[1];
		element = document.getElementById(id);
	}
	if(element.tagName.toLowerCase()=="table"){
	    tbls = document.getElementsByTagName("table");
	//	alert(tbls.length);
    	for (ti=0;ti<tbls.length;ti++) {
			thisTbl = tbls[ti];
		//	alert(thisTbl.id+" "+idlist[0]+"_");
			if(((' '+thisTbl.id+' ').indexOf(idlist[0]+"_") != -1)) {
				thisTbl.style.display='none';
			}
			if (((' '+thisTbl.id+' ').indexOf(start) != -1)) {
				thisTbl.style.display='none'
				if(thisTbl.id == id){
					thisTbl.style.display='';
				}
			}
	    }
	} else {
    	tbls = document.getElementsByTagName("TD");
    	for (ti=0;ti<tbls.length;ti++) {
			thisTbl = tbls[ti];
			if(((' '+thisTbl.id+' ').indexOf("dl_") != -1)) {
				firstbutton = document.getElementById("dl"+thisTbl.id.substring(2));
				firstbutton.className='downloadlinkoff'
			}
		}
    	tbls = document.getElementsByTagName("TR");
    	for (ti=0;ti<tbls.length;ti++) {
			thisTbl = tbls[ti];
			if(((' '+thisTbl.id+' ').indexOf(idlist[0]+"_") != -1)) {
				thisTbl.style.display='none';
			}
			if (((' '+thisTbl.id+' ').indexOf(start) != -1)) {
				if(thisTbl.id == id){
					thisTbl.style.display='';
					firstbutton = document.getElementById("dl"+id.substring(2));
					firstbutton.className='downloadlinkon'
				}
			}
	    }
	}
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    - change links now
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	links = document.links;
	try{
    	for (i=0;i<links.length;i++) {
	        if ((links[i].id+"").indexOf("download_"+idlist[0]+"_") != -1) {
				checkid = links[i].id;
				if("download_"+id==checkid){
					myli = "li"+id.substring(2);
					mylitag = document.getElementById(myli);
					mylitag.className= 'downloadlinkon';
				} else {
					myli = "li"+checkid.substring(11);
					mylitag = document.getElementById(myli);
					mylitag.className= 'downloadlinkoff';
				}
			}
		}
	}catch(e){
	
	}
	
}