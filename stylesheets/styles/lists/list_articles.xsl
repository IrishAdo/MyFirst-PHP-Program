<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:54 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
default FAQ look and feel
-->

<xsl:template name="display_list">
	<xsl:variable name="title_page"><xsl:choose>
		<xsl:when test="//menu[url=//modules/module/setting[@name='script']]/@title_page=1">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:comment> lists\list_articles.xsl </xsl:comment>
	<a name="top_of_content"/>
	<xsl:choose>
		<xsl:when test="count(//modules/container/webobject/module/page)>1">
			<xsl:if test="$title_page=1">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="alt_title">1</xsl:with-param>
				<xsl:with-param name="date_publish">0</xsl:with-param>
				<xsl:with-param name="style">LOCATION</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="//modules/container/webobject/module[@name='presentation' and @display='ENTRY']/page[position()=1]/@identifier"/></xsl:with-param>
			</xsl:call-template>
			</xsl:if>
			<xsl:for-each select="//modules/container/webobject/module[@name='presentation' and @display='ENTRY']/page[position()!=$title_page]">
				<xsl:call-template name="display_this_page">
					<xsl:with-param name="summary">1</xsl:with-param>
					<xsl:with-param name="alt_title">1</xsl:with-param>
					<xsl:with-param name="source">1</xsl:with-param>
					<xsl:with-param name="contributors">1</xsl:with-param>
					<xsl:with-param name="date_publish">0</xsl:with-param>
					<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
				</xsl:call-template>
			</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="back">1</xsl:with-param>
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="alt_title">1</xsl:with-param>
				<xsl:with-param name="source">1</xsl:with-param>
				<xsl:with-param name="contributors">1</xsl:with-param>
				<xsl:with-param name="date_publish">0</xsl:with-param>
				<xsl:with-param name="style">LOCATION</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="//modules/container/webobject/modulemodule[@name='presentation' and @display='ENTRY']/page[position()=1]/@identifier"/></xsl:with-param>
			</xsl:call-template>
		</xsl:otherwise>
	</xsl:choose>

	<xsl:if test="xml_document/debugging">
		<xsl:comment> debug Data </xsl:comment>
		<xsl:apply-templates select="xml_document/debugging"/>
	</xsl:if>
	
</xsl:template>

</xsl:stylesheet>