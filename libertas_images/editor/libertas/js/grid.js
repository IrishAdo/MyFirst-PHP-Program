/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   G R I D . J S 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- The Grid is used in colour picker and Special Character drop down
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	author : Adrian Sweeney
	-	Modified $Date: 2004/11/15 20:23:52 $
	-	$Revision: 1.3 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
var __gridcontainer;
function grid_execute_event(integer_ptr){
//	alert(this.grid.items[integer_ptr].action);
	eval(this.grid.items[integer_ptr].action);
}

function hidegrid (){
    if (__gridcontainer != null && !__gridcontainer.inuse) {
        __gridcontainer.style.visibility = 'hidden';
		__gridcontainer.style.display = "none";
    }
}

function gridItem(label, action, available, icon, item){
	if (item+""=="undefined"){
		item="ITEM";
	}
    this.itemtype = item;
    this.label = label;
    this.action = action;
	this.available = available;
	this.icon = icon;
	this.children = null;
	
    this.onClick = function(){
	}
}

function Grid(label){
    this.itemtype 	= "grid";
    this.items 		= new Array();
    this.label 		= label;
    this.addItem 	= function (item){
        this.items[this.items.length]= item;
    }
    this.overwrite 	= function (item,index){
        this.items[index] = item;
    }
    
    this.addSeparator = function(){this.addItem(new gridItem("-", null));}
	
	this.hide = hidegrid;
    this.draw = function (width){
		var spacerSize = 6;
		if (width){
		} else {
			width=6;
		}
		var newtable= document.createElement('TABLE');
		var newrow 	= null;
		var newcell = null;

		newtable.summary 		= 'Grid';
		newtable.style.width	= '100%';
		newtable.style.height	= '100%';
		newtable.border 		= 0;
		newtable.cellPadding	= 1;
		newtable.cellSpacing	= 1;
	   	container = "<style>\n"
			  + ".gridbar{background:grey}\n"
			  + ".grid_item_highlighted{background:yellow}\n"
			  + ".grid_item{background:white}\n"
			  + "</style>\n";
		//container += "<table cellspacing='1' cellpadding='1' width='100%' style='height:100%'>";
		height = (this.items.length *12)+30;
		z=0;
		if (this.items[-1]+'' !="undefined"){
			var newrow 	= document.createElement('TR');
			var newcell = document.createElement('TD');
				newcell.setAttribute("width"		,"100%");
				newcell.setAttribute("id"			,"btn");
				newcell.setAttribute("name"			,"btn");
				if(version>5){
					newcell.setAttribute("onclick"		,"javascript:parent.grid_execute_event(-1);parent.oPopup.hide();event.cancelBubble=true;");
				} else {
					newcell.setAttribute("onclick"		,"javascript:parent.cancelTable();parent.grid_execute_event(-1);event.cancelBubble=true;");
				}
				newcell.setAttribute("height"		,"20");
				newcell.setAttribute("align"		,"center"); 
				newcell.style.border = "1px solid #ebebeb";
				newcell.setAttribute("onmouseover"	,"btn.style.background='#9999cc';btn.style.borderTop='1px solid #993399';btn.style.borderRight='1px solid #993399';btn.style.borderBottom='1px solid #993399';btn.style.borderLeft='1px solid #993399';");
				newcell.setAttribute("onmouseout"	,"btn.style.background='#ebebeb';btn.style.borderTop='1px solid #ebebeb';btn.style.borderRight='1px solid #ebebeb';btn.style.borderBottom='1px solid #ebebeb';btn.style.borderLeft='1px solid #ebebeb';");
				newcell.innerHTML = "None";
			newrow.appendChild(newcell);
			newtable.appendChild(newrow);
		}
	    if (this.items[0].itemtype == "color"){
			var newrow 	= document.createElement('TR');
			var newcell = document.createElement('TD');
			var secondtable = document.createElement('TABLE');
				secondtable.setAttribute("width"		,"100%");
				secondtable.setAttribute("cellpadding"	,2);
				secondtable.setAttribute("cellspacing"	,0);
		}
	    if (this.items[0].itemtype == "character"){
			var newrow 	= document.createElement('TR');
			var newcell = document.createElement('TD');
			var secondtable = document.createElement('TABLE');
				secondtable.setAttribute("width"		,"100%");
				secondtable.setAttribute("cellpadding"	,2);
				secondtable.setAttribute("cellspacing"	,1);
		}
		for (var i = 0; i < this.items.length; i ++) {
		    if (this.items[i].itemtype == "color"){
				if (z==0){
					//alert(this.items.length);
					var secondnewrow	= document.createElement('TR');
					var secondnewcell	= document.createElement('TD');
						secondnewcell.setAttribute("bgcolor"	,"#ebebeb");
						var spacer	= document.createElement('IMG');
							spacer.setAttribute("border", 0);
							spacer.setAttribute("unselectable", "on");
							spacer.setAttribute("src", "/libertas_images/themes/1x1.gif");
							spacer.setAttribute("width", spacerSize);
							spacer.setAttribute("height", spacerSize);
						secondnewcell.appendChild(spacer);
						secondnewrow.appendChild(secondnewcell);
//						secondtable.appendChild(secondnewrow);
				}
				var secondnewcell	= document.createElement('TD');
//					alert(this.items[i].label);
					secondnewcell.setAttribute("bgcolor"	,this.items[i].label.substring(1,7));
					secondnewcell.setAttribute("onmouseover"	,"btn"+i+".style.borderTop='1px solid #993399';btn"+i+".style.borderRight='1px solid #993399';btn"+i+".style.borderBottom='1px solid #993399';btn"+i+".style.borderLeft='1px solid #993399';");
					secondnewcell.setAttribute("onmouseout"		,"btn"+i+".style.borderTop='1px solid #000000';btn"+i+".style.borderRight='1px solid #000000';btn"+i+".style.borderBottom='1px solid #000000';btn"+i+".style.borderLeft='1px solid #000000';");
					secondnewcell.setAttribute("id"				,"btn"+i); 
					secondnewcell.setAttribute("name"			,"btn"+i);
					secondnewcell.style.border	= "1px solid #000000";
					var spacer	= document.createElement('IMG');
						spacer.setAttribute("border"		,0);
						spacer.setAttribute("unselectable"	,"on");
						spacer.setAttribute("src"			,"/libertas_images/themes/1x1.gif");
						if(version>5){
							spacer.setAttribute("onclick"		,"javascript:parent.grid_execute_event("+i+");parent.oPopup.hide();event.cancelBubble=true;");
						} else {
							spacer.setAttribute("onclick"		,"javascript:parent.cancelTable();parent.grid_execute_event("+i+");event.cancelBubble=true;");
						}
						spacer.setAttribute("width", spacerSize);
						spacer.setAttribute("height", spacerSize);
					secondnewcell.appendChild(spacer);
					secondnewrow.appendChild(secondnewcell);
				//spacer
				var secondnewcell	= document.createElement('TD');
					secondnewcell.setAttribute("bgcolor"	,"#EBEBEB");
					var spacer					= document.createElement('IMG');
						spacer.setAttribute("border", 0);
						spacer.setAttribute("unselectable", "on");
						spacer.setAttribute("src", "/libertas_images/themes/1x1.gif");
						spacer.setAttribute("width", spacerSize);
						spacer.setAttribute("height", spacerSize);
					secondnewcell.appendChild(spacer);
					secondnewrow.appendChild(secondnewcell);
				
				z++;
				if (z>=width){
					secondtable.appendChild(secondnewrow);
//					container += "<tr><td colspan='13' bgcolor=#ebebeb ><img src='/libertas_images/themes/1x1.gif' width=6 height=6 unselectable='on'></td></tr>";
					var secondnewrow	= document.createElement('TR');
					var secondnewcell	= document.createElement('TD');
						secondnewcell.setAttribute("colspan"	,"13");
						secondnewcell.setAttribute("bgcolor"	,"#ebebeb");
						var spacer	= document.createElement('IMG');
							spacer.setAttribute("border",		0);
							spacer.setAttribute("unselectable", "on");
							spacer.setAttribute("src", 			"/libertas_images/themes/1x1.gif");
							spacer.setAttribute("width",		spacerSize);
							spacer.setAttribute("height",		spacerSize);
						secondnewcell.appendChild(spacer);
						secondnewrow.appendChild(secondnewcell);
						secondtable.appendChild(secondnewrow);
					z=0;
				}
			}
		    if (this.items[i].itemtype == "character"){
				if (z==0){
					var secondnewrow	= document.createElement('TR');
				}
				var secondnewcell	= document.createElement('TD');
				secondnewcell.setAttribute("id"		,"btn"+i);
				secondnewcell.setAttribute("name"	,"btn"+i);
				secondnewcell.style.border	="1px solid #cccccc";
				secondnewcell.style.width	="20px";
				secondnewcell.style.textAlign="center";
				secondnewcell.setAttribute("onmouseover",	"btn"+i+".style.borderTop='1px solid #993399';btn"+i+".style.borderRight='1px solid #993399';btn"+i+".style.borderBottom='1px solid #993399';btn"+i+".style.borderLeft='1px solid #993399';btn"+i+".style.backgroundColor='#9999cc';");
				secondnewcell.setAttribute("onmouseout",	"btn"+i+".style.borderTop='1px solid #cccccc';btn"+i+".style.borderRight='1px solid #cccccc';btn"+i+".style.borderBottom='1px solid #cccccc';btn"+i+".style.borderLeft='1px solid #cccccc';btn"+i+".style.backgroundColor='#ebebeb';");
				if(version>5){
					secondnewcell.setAttribute("onclick"		,"javascript:parent.grid_execute_event("+i+");parent.oPopup.hide();event.cancelBubble=true;");
				} else {
					secondnewcell.setAttribute("onclick"		,"javascript:parent.cancelTable();parent.grid_execute_event("+i+");event.cancelBubble=true;");
				}
				secondnewcell.innerHTML ="&#" + this.items[i].label + ";";
				secondnewrow.appendChild(secondnewcell)
				z++;
				if (z>=width){
					z=0;
					secondtable.appendChild(secondnewrow);
				}
			}			
		}
		newcell.appendChild(secondtable);
		newrow.appendChild(newcell);
		newtable.appendChild(newrow);
		container += newtable.outerHTML;
		return 	container
    }
}


