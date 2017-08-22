<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:55 $
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
	<xsl:param name="author">1</xsl:param>
	<xsl:param name="alt_title">1</xsl:param>
	<xsl:param name="source">1</xsl:param>
	<xsl:param name="contributors">1</xsl:param>
	<xsl:param name="date_publish">1</xsl:param>

	<xsl:comment> start list Data </xsl:comment>
	<a name="top_of_content"/>
	<xsl:choose><xsl:when test="count(//modules/container/webobject/module/page)>1">
		<xsl:call-template name="display_this_page">
			<xsl:with-param name="content">1</xsl:with-param>
			<xsl:with-param name="identifier"><xsl:value-of select="//modules/container/webobject/module/page[position()=1]/@identifier"/></xsl:with-param>
		</xsl:call-template>
	<xsl:for-each select="//modules/container/webobject/module/page">
		<xsl:if test="position()!=1">
			<a><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:value-of select="substring-before(metadata/date[@refinement='publish'],' ')"/> - <xsl:value-of disable-output-escaping="yes" select="title"/></a><br/>
		</xsl:if>
	</xsl:for-each>
	</xsl:when><xsl:otherwise>
		<xsl:call-template name="display_this_page">
			<xsl:with-param name="content">1</xsl:with-param>
			<xsl:with-param name="author"><xsl:value-of select="$author"/></xsl:with-param>
			<xsl:with-param name="alt_title"><xsl:value-of select="$alt_title"/></xsl:with-param>
			<xsl:with-param name="source"><xsl:value-of select="$source"/></xsl:with-param>
			<xsl:with-param name="contributors"><xsl:value-of select="$contributors"/></xsl:with-param>
			<xsl:with-param name="date_publish"><xsl:value-of select="$date_publish"/></xsl:with-param>
			<xsl:with-param name="identifier"><xsl:value-of select="//modules/container/webobject/module/page[position()=1]/@identifier"/></xsl:with-param>
			<xsl:with-param name="back">1</xsl:with-param>
		</xsl:call-template>
	</xsl:otherwise></xsl:choose>
		<xsl:comment> end list Data </xsl:comment>
</xsl:template>

</xsl:stylesheet>