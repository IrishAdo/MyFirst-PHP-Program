<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2004/10/05 11:03:50 $
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
	<xsl:param name="display_more_as_text"/>
		
	<xsl:variable name="page"><xsl:choose>
		<xsl:when test="contains(//xml_document/qstring,'page=')"><xsl:value-of select="substring-before(substring-after(//xml_document/qstring,'page='),'&amp;')"/></xsl:when>	
		<xsl:otherwise>1</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="start"><xsl:value-of select="(floor(($page - 1) div 10)*10)+1"/></xsl:variable>
	<xsl:variable name="end"><xsl:choose>
		<xsl:when test="(ceiling($page div 10)*10) > count(//xml_document/modules/container/webobject/module[@name='presentation']/page)"><xsl:value-of select="count(//xml_document/modules/container/webobject/module[@name='presentation']/page)"/></xsl:when>	
		<xsl:otherwise><xsl:value-of select="(ceiling($page div 10)*10)"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
<!--	<xsl:value-of select="$start"/>-->
	
	<xsl:call-template name="display_content_spanning">
		<xsl:with-param name="start"><xsl:value-of select="$start"/></xsl:with-param>
		<xsl:with-param name="end"><xsl:value-of select="$end"/></xsl:with-param>
		<xsl:with-param name="max"><xsl:value-of select="count(//xml_document/modules/container/webobject/module[@name='presentation']/page)"/></xsl:with-param>
		<xsl:with-param name="page"><xsl:value-of select="$page"/></xsl:with-param>
	</xsl:call-template>
	<xsl:call-template name="display_this_page">
		<xsl:with-param name="title">1</xsl:with-param>
		<xsl:with-param name="alt_title">1</xsl:with-param>
		<xsl:with-param name="content">1</xsl:with-param>
		<xsl:with-param name="style">LOCATION</xsl:with-param>
		<xsl:with-param name="display_more_as_text" select="$display_more_as_text"/>
		<xsl:with-param name="identifier"><xsl:value-of select="//xml_document/modules/container/webobject/module[@name='presentation']/page[position()=$page]/@identifier"/></xsl:with-param>
	</xsl:call-template>
	<xsl:if test="//xml_document/modules/container/webobject/module/headline">
		<xsl:call-template name='show_headlines'>
			<xsl:with-param name='cols'>3</xsl:with-param>
		</xsl:call-template>
	</xsl:if>

</xsl:template>

<xsl:template name="display_content_spanning">
	<xsl:param name="current">1</xsl:param>
	<xsl:param name="start">1</xsl:param>
	<xsl:param name="end">1</xsl:param>
	<xsl:param name="max"></xsl:param>
	<xsl:param name="displayed">0</xsl:param>
	<xsl:param name="page">1</xsl:param>
	<xsl:if test="number($current)=1">
		<xsl:if test="10 > $start"><a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/></xsl:attribute>Index </a></xsl:if>
		<xsl:if test="$start > 10"><a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?page=<xsl:value-of select="$start - 1"/>&amp;</xsl:attribute>... Previous </a></xsl:if>
		| 
	</xsl:if>
	<xsl:if test="number($current) >= number($start) and number($end) >= number($current)">
		<a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?page=<xsl:value-of select="$current"/>&amp;</xsl:attribute><xsl:attribute name="title"><xsl:value-of select="//xml_document/modules/container/webobject/module/page[position()=$current]/title"/></xsl:attribute><xsl:value-of select="$current"/></a> |
	</xsl:if>
	<xsl:if test="number($end) >= number($current) ">
		<xsl:call-template name="display_content_spanning">
			<xsl:with-param name="current"><xsl:value-of select="$current + 1"/></xsl:with-param>
			<xsl:with-param name="max"><xsl:value-of select="$max"/></xsl:with-param>
			<xsl:with-param name="page"><xsl:value-of select="$page"/></xsl:with-param>
			<xsl:with-param name="start"><xsl:value-of select="$start"/></xsl:with-param>
			<xsl:with-param name="end"><xsl:value-of select="$end"/></xsl:with-param>
		</xsl:call-template>
	</xsl:if>
	<xsl:if test="number($current) = number($end) and number($current) != number($max)">
		<a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?page=<xsl:value-of select="$current + 1"/>&amp;</xsl:attribute>Next ...</a>
	</xsl:if>
</xsl:template>
</xsl:stylesheet>

