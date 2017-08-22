var timesover=0;
var timerID = null;
//var boolFirstMenuFlag = true;
var boolTimerOn = false;
var intTimecount = 450;
var intRunCount = 0;
var intTabActive = 999;
	
function doInitializeMenu() {
	for (i=0; i<intNumTabs; i++){
		if(document.getElementById("menuitem-" + asTabs[i][0]).className=="mainmenuitem active")
			intTabActive = i;
	}
}
function settimeover(){
timesover=1;
}
function doMenuOn(id) {
	/* initialize the menu to figure out which tab is set to active */
	if (intRunCount < 1)
	{
		doInitializeMenu();
		intRunCount++;
	}

	
	//if (boolFirstMenuFlag == true)
	//{

		/*hide any menus that might be open*/
		if (document.getElementById("menuitem-" + id).className == "mainmenuitem active" && timesover==0)
		{
		//alert("active");
		timesover=1;
		}
		else
		{
		timesover=1;
		doHideAll();
		/* if statement to check if the submenu acutally exists, because the home menu doesn't have one */
		if(document.getElementById("submenu-" + id))
			document.getElementById("submenu-" + id).style.display = "block"; /*turn on the submenu*/
		
		/*change the style of the tab to the hover style*/	
		document.getElementById("menuitem-" + id).className = "mainmenuitem hover"; 
	
		/* stop the timer */
		doStopTime(); 
		}
	//}
}
function doMenuOff(id) {
for (i=0; i<intNumTabs; i++)
	{
			if(document.getElementById("menuitem-" + asTabs[i][0]).className=="mainmenuitem active")
				timesover=1;
	}


	/*start the timer */
	doStartTime(); 
}
function doSubMenuOn(){
	/* stop the timer */
	doStopTime(); 
}
function doSubMenuOff(){
	 /* start the timer */
	 doStartTime();
}
function doHideAll(){
	
	for (i=0; i<intNumTabs; i++)
	{
		/* if statement to check if the submenu exists before we try to hide it */
		if(document.getElementById("submenu-" + asTabs[i][0]))
			document.getElementById("submenu-" + asTabs[i][0]).style.display = "none"; /* hide the submenu */
		/*set all tabs to the inactive style */
		document.getElementById("menuitem-" + asTabs[i][0]).className="mainmenuitem inactive";
	}
	/*set the active tab to the active style*/
	if (intTabActive < 999)
	{
		document.getElementById("menuitem-" + asTabs[intTabActive][0]).className="mainmenuitem active";
	}
}
function doStopTime(){
	if (boolTimerOn){
		clearTimeout(timerID);
        timerID = null;
        boolTimerOn = false;
	}

}
function doStartTime(){
	if (boolTimerOn == false) {
		timerID=setTimeout( "doHideAll()" , intTimecount);
		boolTimerOn = true;
	}
}
