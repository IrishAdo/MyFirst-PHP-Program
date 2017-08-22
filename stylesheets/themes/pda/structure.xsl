<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2005/02/09 12:16:11 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:template name="display_layout_structure">
<html ><xsl:attribute name="lang"><xsl:value-of select="//locale/@codex"/></xsl:attribute>
<head>
<!--
<xsl:call-template name="display_header_data"/>
-->
</head>
<body>
<!--
<a name="top_of_content"/>
<xsl:call-template name="display_wai_header_links"/>
-->
<h1><xsl:value-of select="//module[@name='contact']/table/row[@label='Company']"/></h1>
<div><xsl:call-template name="display_breadcrumb_trail"><xsl:with-param name="linking" select="0"/></xsl:call-template></div>
<div><xsl:call-template name="display_menu"/></div>
<div>Last Update :: <xsl:value-of select="//setting[@name='site_updated']"/></div>
<xsl:if test="//command='WEBOBJECTS_SHOW_LOGIN'"><xsl:call-template name="display_login"></xsl:call-template></xsl:if>
<xsl:if test="//command='WEBOBJECTS_SHOW_SEARCH_BOX_FANCY' or //command='WEBOBJECTS_SHOW_SEARCH_BOX_COLUMN' or //command='WEBOBJECTS_SHOW_SEARCH_BOX_ROW'"><xsl:call-template name="display_page_search"><xsl:with-param name="searchType">ROW</xsl:with-param></xsl:call-template></xsl:if>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">header</xsl:with-param></xsl:call-template>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param><xsl:with-param name="show_label">1</xsl:with-param></xsl:call-template>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param><xsl:with-param name="show_label">1</xsl:with-param></xsl:call-template>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param><xsl:with-param name="show_label">1</xsl:with-param></xsl:call-template>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param><xsl:with-param name="show_label">1</xsl:with-param></xsl:call-template>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">footer</xsl:with-param></xsl:call-template>
<br/>
<div align="center">:: <a ><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>#top_of_content</xsl:attribute>Back to top of page</a> ::</div>
<hr/>
<xsl:call-template name="display_power_by">
	<xsl:with-param name="type">pda</xsl:with-param>
</xsl:call-template>
</body>
</html>

</xsl:template>

</xsl:stylesheet>