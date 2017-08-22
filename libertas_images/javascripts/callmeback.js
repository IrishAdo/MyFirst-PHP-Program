<!--BEGIN Call Me Back Web Site Code------------------------------------------>
<script language="JavaScript">
<!--
var popWin = null    // use this when referring to pop-up window
var winCount = 0
var winName = "popWin"
function openPopWin(winURL, winWidth, winHeight, winFeatures, winLeft, winTop){
  var d_winLeft = 20  // default, pixels from screen left to window left
  var d_winTop = 20   // default, pixels from screen top to window top
  winName = "popWin" + winCount++ //unique name for each pop-up window
  closePopWin()           // close any previously opened pop-up window
  if (openPopWin.arguments.length >= 4)  // any additional features? 
	winFeatures = "," + winFeatures
  else 
	winFeatures = ""
  if (openPopWin.arguments.length == 6)  // location specified
	winFeatures += getLocation(winWidth, winHeight, winLeft, winTop)
  else
	winFeatures += getLocation(winWidth, winHeight, d_winLeft, d_winTop)
  popWin = window.open(winURL, winName, "width=" + winWidth 
		   + ",height=" + winHeight + winFeatures)
  }
function closePopWin(){    // close pop-up window if it is open
  if (navigator.appName != "Microsoft Internet Explorer" 
	  || parseInt(navigator.appVersion) >=4) //do not close if early IE
	if(popWin != null) if(!popWin.closed) popWin.close()
  }
function getLocation(winWidth, winHeight, winLeft, winTop){
  return ""
  }
function getLocation(winWidth, winHeight, winLeft, winTop){
  var winLocation = ""
  if (winLeft < 0)
	winLeft = screen.width - winWidth + winLeft
  if (winTop < 0)
	winTop = screen.height - winHeight + winTop
  if (winTop == "cen")
	winTop = (screen.height - winHeight)/2 - 20
  if (winLeft == "cen")
	winLeft = (screen.width - winWidth)/2
  if (winLeft>0 & winTop>0)
	winLocation =  ",screenX=" + winLeft + ",left=" + winLeft
				+ ",screenY=" + winTop + ",top=" + winTop
  else
	winLocation = ""
  return winLocation
  }
//-->
</script>
<!--END Call Me Back Web Site Code------------------------------------------->
					  