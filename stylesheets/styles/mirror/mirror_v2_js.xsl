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
	<xsl:variable name="summary"><xsl:choose>
		<xsl:when test="contains($display_option,'SUMMARY')">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:variable>
	
<xsl:if test="//xml_document/modules/container/webobject/module[@name='mirror']">
<xsl:value-of select="label" disable-output-escaping="yes"/>
<div id="spageie" style="background:#FFFFFF;width:180;height:15;border-style:solid; border-width:1px; border-color:#5C5C5C;overflow:hidden;"></div>
<div id="spagens" style="background:#FFFFFF;width:180;height:15;border-style:solid; border-width:1px; border-color:#5C5C5C;overflow:hidden;"></div>
<script language="JavaScript">
	var OPB=false;

	uagent = window.navigator.userAgent.toLowerCase();

	OPB=(uagent.indexOf('opera') != -1)?true:false;
	
	var titlea = new Array();
	var texta = new Array();
	var linka = new Array();
	var trgfrma = new Array();
	var heightarr = new Array();
	var cyposarr = new Array();
	
	<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='mirror']/module/page[$max_list_count >= position()]">
		cyposarr[<xsl:value-of select="position() - 1"/>]=0;
		titlea[<xsl:value-of select="position() - 1"/>] = "<xsl:value-of select="title" disable-output-escaping="yes"/>";
		<xsl:choose>
		<xsl:when test="$summary=1">texta[<xsl:value-of select="position() - 1"/>] = "<xsl:value-of select="summary" disable-output-escaping="yes"/>";</xsl:when>
		<xsl:otherwise>texta[<xsl:value-of select="position() - 1"/>] = "";</xsl:otherwise>
		</xsl:choose>
		linka[<xsl:value-of select="position() - 1"/>] = "<xsl:value-of select="locations/location[position()=1]/@url"/>";
		trgfrma[<xsl:value-of select="position() - 1"/>] = "_self";
	</xsl:for-each>
	if((document.all)&amp;&amp;(OPB==false)){
		document.write("&#60;scr"+"ipt language=\"javascript\" sr"+"c=\"/libertas_images/javascripts/scroller/v2/main_ie.js\">&#60;/scr"+"ipt>");
	}else{
		if(OPB==true){
			document.write("&#60;div id=\"spagens\" style=\"background:#FFFFFF;width:180;height:15;border-style:solid; border-width:1px; border-color:#5C5C5C;overflow:hidden;\">&#60;/div>");
		}else{
//			document.write("&#60;div id=\"spagens\" style=\"background:#FFFFFF; width:180; height:15; left:0; top:0; border-style:solid; border-width:1px; border-color:#5C5C5C;overflow:hidden;\">&#60;/div>");
		}
		document.write("&#60;scr"+"ipt language=\"javascript\" sr"+"c=\"/libertas_images/javascripts/scroller/v2/main_ns.js\">&#60;/scr"+"ipt>");
	}
</script>

</xsl:if>
		
</xsl:template>


</xsl:stylesheet>