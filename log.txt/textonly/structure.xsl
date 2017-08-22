<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2004/10/04 12:07:56 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:template name="display_layout_structure">
<html ><xsl:attribute name="lang"><xsl:value-of select="//locale/@codex"/></xsl:attribute>
<head>
<xsl:call-template name="display_header_data_no_images"/>
</head>
<body>
<xsl:call-template name="display_wai_header_links_no_images"/>
<p><xsl:call-template name="display_breadcrumb_trail">
	<xsl:with-param name="displayhome">0</xsl:with-param>
</xsl:call-template></p>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">header</xsl:with-param></xsl:call-template>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template>
<xsl:call-template name="show_containers"><xsl:with-param name="display_position">footer</xsl:with-param></xsl:call-template>
<hr/>
<a name="menu"/>
<h1>Navigation</h1>
<xsl:call-template name="display_menu"/>
<a name="access_keys"/>
<xsl:choose>
	<xsl:when test="//setting[@name='accesskeys']"><xsl:value-of select="//setting[@name='accesskeys']"/></xsl:when>
	<xsl:otherwise>
	<h1>Access Keys</h1>
	<xsl:call-template name="display_wai_footer_links"/></xsl:otherwise>
</xsl:choose>
<xsl:if test="//xml_document/debugging"><xsl:apply-templates select="//xml_document/debugging"/></xsl:if>
</body>
</html>

</xsl:template>

</xsl:stylesheet>