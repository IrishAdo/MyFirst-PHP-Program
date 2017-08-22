/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   M E N U . J S 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- The Menu (right click and drop down menus
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	author : Adrian Sweeney
	-	Modified $Date: 2004/08/24 14:49:50 $
	-	$Revision: 1.2 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
var __menucontainer;
function setForeColor(e,s,c){
	eval("LIBERTAS_fore_color_click('"+e+"','"+s+"','"+c+"');");
}
function execute_event(integer_ptr){
//	alert(this.menu.items[integer_ptr].action);
	eval(this.menu.items[integer_ptr].action);
	try{
		parent.PopupObject.hide();
		//this.hide
//		alert("1");
	} catch(e){
//		alert("0");
	}
}
function hideMenu (){
    if (__menucontainer != null && !__menucontainer.inuse) {
        __menucontainer.style.visibility = 'hidden';
		__menucontainer.style.display = "none";
    }
}
function MenuItem(label, action, available, icon){
    this.itemtype = "ITEM";
    this.label = label;
    this.action = action;
	this.available = available;
	this.icon = new String(icon).toLowerCase();
	this.children = null;
    this.onClick = function(){}
}
function Menu(label){
    this.itemtype = "MENU";
    this.items = new Array();
    this.label = label;
    this.addItem = function (item){
        this.items[this.items.length] = item;
    }
    this.addSeparator = function(){this.addItem(new MenuItem("-", null));}
	this.hide = hideMenu;
	
    this.draw = function (){
//		if (container.tagName == "DIV"){
	var newtable= document.createElement('TABLE');
	var newrow 	= null;
	var newcell = null;

		newtable.summary 		= 'Menu';
		newtable.style.width	= '100%';
		newtable.style.height	= '100%';
		newtable.border 		= 0;
		newtable.cellPadding	= 0;
		newtable.cellSpacing	= 0;
		   	container = "<style>\n"
					  + ".menubar{background:grey}\n"
					  + ".menu_item_highlighted{background:yellow}\n"
					  + ".menu_item{background:white}\n"
					  + "</style>\n";
			height = (this.items.length *12)+30;
			for (var i = 0; i < this.items.length; i ++) {
				if (this.items[i].itemtype=="ITEM"){
					if (this.items[i].label == "-"){
						var newrow 	= document.createElement('TR');
						var newcell = document.createElement('TD');
							newcell.style.filter="progid:dximagetransform.microsoft.gradient(gradienttype=1,startcolorstr=#ffffff,endcolorstr=#cccccc);";
							newcell.setAttribute("width"	,20);						
						newrow.appendChild(newcell);
						var newcell = document.createElement('TD');
							newcell.style.backgroundColor="#ffffff";
							var newhr = document.createElement('HR');
								newhr.setAttribute("width"		,"180px");
							newcell.appendChild(newhr);
						newrow.appendChild(newcell);
					} else {	
						if (this.items[i].available){
							var newrow 	= document.createElement('TR');
							var newcell = document.createElement('TD');

								newcell.style.filter			= "progid:dximagetransform.microsoft.gradient(gradienttype=1,startcolorstr=#ffffff,endcolorstr=#cccccc);";
								newcell.style.backgroundColor	= "#EBEBEB";
								newcell.style.bordertop			= "1px transparent #cccccc";
								newcell.style.borderleft		= "1px transparent #cccccc";
								newcell.style.borderbottom		= "1px transparent #cccccc";
								newcell.setAttribute("width"		,20);						
								newcell.setAttribute("id"			,"btn"+i); 
								newcell.setAttribute("name"			,"btn"+i);
								newcell.setAttribute("unselectable"	,"on");
								newcell.setAttribute("onmouseover"	,"menu"+i+".style.background='#9999cc';menu"+i+".style.borderTop='1px solid #993399';menu"+i+".style.borderRight='1px solid #993399';menu"+i+".style.borderBottom='1px solid #993399';menu"+i+".style.borderLeft='1px solid #993399';btn"+i+".style.background='#9999cc';");
								newcell.setAttribute("onmouseout"	,"menu"+i+".style.background='#ffffff';menu"+i+".style.borderTop='1px solid #ffffff';menu"+i+".style.borderRight='1px solid #ffffff';menu"+i+".style.borderLeft='1px solid #ffffff';menu"+i+".style.borderBottom='1px solid #ffffff';btn"+i+".style.background='';");
								if (this.items[i].icon!=""){
									var iconification = document.createElement('IMG');
									iconification.setAttribute("height"		,20);
									iconification.setAttribute("width"		,20);
									iconification.setAttribute("src"		,'/libertas_images/editor/libertas/lib/themes/classic/img/tb_'+this.items[i].icon+'.gif');
									iconification.setAttribute("border"		,0);
									iconification.setAttribute("alt"		,this.items[i].icon);
									iconification.setAttribute("align"		,"left");
									newcell.appendChild(iconification)
								} else {
									newcell.innerHTML=' ';
								}
							newrow.appendChild(newcell);
							var newcell = document.createElement('TD');
								newcell.style.backgroundColor="#ffffff";
								newcell.setAttribute("id"			,"menu"+i); 
								newcell.setAttribute("name"			,"menu"+i);
								newcell.setAttribute("unselectable"	,"on");
								if(version>5){
									newcell.setAttribute("onclick"		,"parent.oPopup.hide();parent.execute_event("+i+");event.cancelBubble=true;");
								} else {
									newcell.setAttribute("onclick"		,"parent.cancelTable();parent.execute_event("+i+");event.cancelBubble=true;");
								}
								newcell.setAttribute("onmousedown"	,"event.cancelBubble=true;");
							 	newcell.setAttribute("onmouseup"	,"event.cancelBubble=true;");
								newcell.setAttribute("onmouseover"	,"menu"+i+".style.background='#9999cc';menu"+i+".style.borderTop='1px solid #993399';menu"+i+".style.borderRight='1px solid #993399';menu"+i+".style.borderBottom='1px solid #993399';menu"+i+".style.borderLeft='1px solid #993399';btn"+i+".style.background='#9999cc';");
								newcell.setAttribute("onmouseout"	,"menu"+i+".style.background='#ffffff';menu"+i+".style.borderTop='1px solid #ffffff';menu"+i+".style.borderRight='1px solid #ffffff';menu"+i+".style.borderLeft='1px solid #ffffff';menu"+i+".style.borderBottom='1px solid #ffffff';btn"+i+".style.background='';");

								newcell.style.fontSize	=	'12px'
								newcell.style.backgroundColor	= "#ffffff";
								newcell.style.bordertop			= "1px transparent #ffffff";
								newcell.style.borderleft		= "1px transparent #ffffff";
								newcell.style.borderbottom		= "1px transparent #ffffff";
								newcell.innerHTML = this.items[i].label
							newrow.appendChild(newcell);
						} else {
							var newrow 	= document.createElement('TR');
							var newcell = document.createElement('TD');
								newcell.style.filter						= "progid:dximagetransform.microsoft.gradient(gradienttype=1,startcolorstr=#ffffff,endcolorstr=#cccccc);";
								newcell.setAttribute("width"				,20);						
								newcell.setAttribute("unselectable"			,"on");
								if (this.items[i].icon!=""){
									var iconification = document.createElement('IMG');
									iconification.setAttribute("height"		,20);
									iconification.setAttribute("width"		,20);
									iconification.setAttribute("src"		,'/libertas_images/editor/libertas/lib/themes/classic/img/tb_'+this.items[i].icon+'.gif');
									iconification.setAttribute("border"		,0);
									iconification.setAttribute("align"		,"left");
									newcell.appendChild(iconification)
								} else {
									newcell.innerHTML=' ';
								}
							newrow.appendChild(newcell);
							var newcell = document.createElement('TD');
								newcell.setAttribute("unselectable"	,"on");
								newcell.style.fontSize	=	'12px'
								newcell.style.Color="#AAAAAA";
								newcell.style.backgroundColor	= "#ffffff";
								newcell.innerHTML = this.items[i].label
							newrow.appendChild(newcell);
						}
					}
					newtable.appendChild(newrow);
				}
				if (this.items[i].itemtype=="MENU"){
					// this code was written for posible sublevel menus not used
					var newdiv = document.createElement('DIV');
					newdiv.settAttribute("class"		,"menubar");
					newdiv.settAttribute("onclick"		,"this.parentElement.style.visibility='hidden';open_child("+i+");event.cancelBubble=true;");
					newdiv.settAttribute("onmousedown"	,"event.cancelBubble=true;\" onmouseup=\"event.cancelBubble=true;");
					newdiv.settAttribute("onmouseover"	,"this.className='menu_item_highlighted';");
					newdiv.settAttribute("onmouseout"	,"this.className='menu_item'");
					newdiv.settAttribute("unselectable"	,"on");
					newdiv.innerHTML = this.items[i].label + " &#187;";
					newtable.appendChild(newdiv);
				}
			}
		return 	container + newtable.outerHTML;
    }
}


