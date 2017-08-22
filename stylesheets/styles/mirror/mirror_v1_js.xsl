<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:59 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"> 
  
<xsl:template name="display_mirror">
	<xsl:param name="header"><xsl:value-of select="//module[@name='mirror']/label" disable-output-escaping="yes"/></xsl:param>
	<xsl:param name="display_option"><xsl:value-of select="//module[@name='mirror']/display"/></xsl:param>
	<xsl:param name="max_list_count"><xsl:value-of select="//module[@name='mirror']/size"/></xsl:param>
	<xsl:param name="hr_return">1</xsl:param>
	<xsl:param name="hr">1</xsl:param>
	<xsl:param name="width">100%</xsl:param>
	<xsl:param name="class">nojustify</xsl:param>
	<xsl:param name="type"></xsl:param>
	<xsl:param name="display_more_as_text"><xsl:value-of select="$more_text"/></xsl:param>
	<xsl:param name="mirror_starter"></xsl:param>
	<xsl:param name="title_starter">[[rightarrow]]</xsl:param>
	<xsl:param name="bullet_width">16</xsl:param>
	<xsl:param name="bullet_height">16</xsl:param>
	<xsl:param name="title_bullet">0</xsl:param>
	<xsl:param name="label_bullet">0</xsl:param>
	<xsl:param name="inTable">0</xsl:param>
	<!--
		Define variables from xml data to define the look and feel of the mirror
	-->
<xsl:if test="//xml_document/modules/container/webobject/module[@name='mirror']">
<div id='mirror'>
<div id="tempholder"></div>
<br/><a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/container/webobject/module[@name='mirror']/menulocation"/></xsl:attribute><img src="/libertas_images/themes/turkington/news.gif" alt="latest news" id="news_image" border="0" style="position:absolute;left:50%;margin-left:-120px;top:53px;"/></a>
<script language="JavaScript" src="/libertas_images/javascripts/scroller/v1/dhtmllib.js"></script>
<script language="JavaScript" src="/libertas_images/javascripts/scroller/v1/main.js"></script>
<script language="JavaScript">

/*
Mike's DHTML scroller (By Mike Hall)
Last updated July 21st, 02' by Dynamic Drive for NS6 functionality
For this and 100's more DHTML scripts, visit http://www.dynamicdrive.com
*/
var myScroller1 = new Scroller((window.screen.availWidth / 2) - 55, 60, 250, 25, 1, 5); //(xpos, ypos, width, height, border, padding)
//myScroller1.setColors("#006600", "#ccffcc", "#009900"); //(fgcolor, bgcolor, bdcolor)
//myScroller1.setFont("Verdana,Arial,Helvetica", 2);
<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='mirror']/module/page[$max_list_count >= position()]">
myScroller1.addItem('<xsl:value-of select="locations/location[position()=1]/@url"/>', '<xsl:value-of select="title"/>');
</xsl:for-each>

//SET SCROLLER PAUSE
myScroller1.setPause(2500); //set pause beteen msgs, in milliseconds
function runmikescroll(){
  var layer;
  var mikex, mikey;
  // Locate placeholder layer so we can use it to position the scrollers.
  layer = getLayer("placeholder");
  mikex = getPageLeft(layer);
  mikey = getPageTop(layer);
  // Create the first scroller and position it.
  myScroller1.create();
  myScroller1.hide();
  myScroller1.moveTo(mikex, mikey);
  myScroller1.setzIndex(100);
  myScroller1.show();
}
window.onload=runmikescroll
</script>
<div id="placeholder" style="position:relative; width:340px; height:0px;"> </div>
</div>
</xsl:if>
		
</xsl:template>

</xsl:stylesheet>
