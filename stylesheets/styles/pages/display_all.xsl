<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.9 $
- Modified $Date: 2005/01/24 08:56:34 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
display 2 columns of titles.
-->

<xsl:template name="display_list">
	<xsl:comment>pages\display_all.xsl</xsl:comment>
	<div class='hightlightall'>
		<xsl:for-each select="//modules/container/webobject/module/page">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="title"><xsl:choose><xsl:when test="position()=1"><xsl:value-of select="$show_title_page_title"/></xsl:when><xsl:otherwise>1</xsl:otherwise></xsl:choose></xsl:with-param>
				<xsl:with-param name="alt_title">1</xsl:with-param>
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="date_publish">0</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</div>
	<xsl:if test="//modules/container/webobject/module/headline">
		<xsl:call-template name='show_headlines'/>
	</xsl:if>
		<xsl:call-template name="display_modules">
		<!-- supply the ignore tags LATEST is a display attribute and polls is a name attribute-->
		<xsl:with-param name="ignore" select="'[page]'"/></xsl:call-template>

	<xsl:if test="xml_document/debugging">
		<xsl:comment> debug Data </xsl:comment>
		<xsl:apply-templates select="xml_document/debugging"/>
	</xsl:if></xsl:template>

</xsl:stylesheet>


