<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:46 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
default FAQ look and feel
-->
<xsl:template name="display_atoz">
	<xsl:param name="display_more_as_text"/>
	<xsl:param name="uses_class"/>
	<table summary="link of links for A to z" class="width100percent">
	<tr><td><a><xsl:attribute name="class"><xsl:choose>
				<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
				<xsl:otherwise>atozlinks</xsl:otherwise>
			</xsl:choose></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='script']"/>?letter=</xsl:attribute>Back to Section</a></td>
	<td class="alignmentright">	
	<xsl:for-each select="letters">
		<div>[[nbsp]]|[[nbsp]]<xsl:for-each select="letter">
		<xsl:choose>
			<xsl:when test="@count!=0"><a><xsl:attribute name="class"><xsl:choose>
				<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
				<xsl:otherwise>atozlinks</xsl:otherwise>
			</xsl:choose></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='script']"/>?letter=<xsl:value-of select="."/></xsl:attribute><xsl:value-of select="."/></a></xsl:when>
			<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
		</xsl:choose>[[nbsp]]|[[nbsp]]
		</xsl:for-each></div></xsl:for-each></td></tr></table>
	<xsl:for-each select="page">
		<xsl:call-template name="display_this_page">
			<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
			<xsl:with-param name="alt_title">1</xsl:with-param>
			<xsl:with-param name="content">1</xsl:with-param>
			<xsl:with-param name="date_publish">0</xsl:with-param>
			<xsl:with-param name="more">0</xsl:with-param>
			<xsl:with-param name="style">LOCATION</xsl:with-param>
			<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
		</xsl:call-template>
	</xsl:for-each>
</xsl:template>
</xsl:stylesheet>

