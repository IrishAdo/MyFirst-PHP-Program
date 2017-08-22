<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:47 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->


<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!-- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- variables for the default settings for pages 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- you can overwrite these variables in your themes variable.xsl file 
- if you want to overwrite one variable you must overwrite all.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Acceptable variable values are 
- "Yes" or "No"
- "Top" or "Bottom" or "Top,Bottom"
- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-->

<xsl:variable name="show_printer_friendly">Yes</xsl:variable>
<xsl:variable name="show_email_friend"><xsl:choose>
	<xsl:when test="//xml_document/modules/module[@name='client']/licence/product/@type='SITE'">No</xsl:when>
	<xsl:otherwise>Yes</xsl:otherwise>
</xsl:choose></xsl:variable>
<xsl:variable name="show_add_bookmark">No</xsl:variable>

<xsl:variable name="more_text">0</xsl:variable>

<xsl:variable name="query_starter"><xsl:choose>
			<xsl:when test="string-length(//xml_document/qstring)!=0">?<xsl:value-of select="//xml_document/qstring"/>&amp;</xsl:when>
			<xsl:otherwise>?</xsl:otherwise>
		</xsl:choose></xsl:variable>


<xsl:variable name="location_of_functions">Top,Bottom</xsl:variable>
</xsl:stylesheet>