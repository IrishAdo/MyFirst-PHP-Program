<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:22:05 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<!--
	function sitemap_default()
	
	simple UL and li tags used
-->

<xsl:template name="sitemap_columns">
<xsl:call-template name="sitemap_default"/>
</xsl:template>

<xsl:template name="sitemap_default">
<a name="top_of_content"/>
	<xsl:choose>
		<xsl:when test="$image_path = '/libertas_images/themes/textonly'"><xsl:call-template name="display_menu"/></xsl:when>
		<xsl:otherwise>
			<xsl:for-each select="//xml_document/modules/module[@name='layout']/menu">
				<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
					<xsl:call-template name="display_indent">
						<xsl:with-param name="depth"><xsl:value-of select="@depth"/></xsl:with-param>
					</xsl:call-template>
					<a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
							<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
								<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
						<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a><br/>
					<xsl:if test="children/menu">
						<xsl:call-template name="display_sitemap_children"><xsl:with-param name="id"><xsl:value-of select="@identifier"/></xsl:with-param><xsl:with-param name="bc_trail"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JUMP_TO'"/></xsl:call-template> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:with-param></xsl:call-template>
					</xsl:if>
				</xsl:if>
			</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_indent">
	<xsl:param name="depth">0</xsl:param>
	<xsl:choose>
		<xsl:when test="$depth=1">[[rightarrow]][[nbsp]]</xsl:when>
		<xsl:otherwise>[[nbsp]][[nbsp]][[nbsp]]<xsl:call-template name="display_indent">
						<xsl:with-param name="depth"><xsl:value-of select="$depth - 1"/></xsl:with-param>
					</xsl:call-template></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_sitemap_children">
	<xsl:param name="bc_trail"></xsl:param>			
	<xsl:param name="li">1</xsl:param>			
	<xsl:param name="id">-1</xsl:param>			
	
	<xsl:for-each select="//children/menu[@parent=$id]">
				<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
					<xsl:call-template name="display_indent">
						<xsl:with-param name="depth"><xsl:value-of select="@depth"/></xsl:with-param>
					</xsl:call-template>
					<a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="$bc_trail"/> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:attribute>
						<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
							<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
						<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a><br/>
					<xsl:if test="children/menu">
							<xsl:call-template name="display_sitemap_children">
								<xsl:with-param name="li">1</xsl:with-param>
								<xsl:with-param name="id"><xsl:value-of select="@identifier"/></xsl:with-param>
								<xsl:with-param name="bc_trail"><xsl:value-of select="$bc_trail"/> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:with-param>
							</xsl:call-template>
					</xsl:if>
				</xsl:if>
	</xsl:for-each>
</xsl:template>
<!--
	function sitemap_columns()

-->

</xsl:stylesheet>
