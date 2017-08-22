<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:49:45 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 



<xsl:template name="display_breadcrumb_trail">
	<xsl:param name="url" select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>
	<xsl:param name="linking"/>
	<!-- display breadcrumbs -->
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
	   <xsl:call-template name="display_breadcrumb_parent">
			<xsl:with-param name="parent_identifier" select="-1"/>       
			<xsl:with-param name="link" select="$linking"/>
			<xsl:with-param name="current_url" select="$url"/>       
       	</xsl:call-template>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_breadcrumb_parent">
	<xsl:param name="current_url"/>
	<xsl:param name="link"/>
	<xsl:param name="parent_identifier"/>       

	<xsl:for-each select="//menu[@parent=$parent_identifier]">
		<xsl:choose>
			<xsl:when test="url=$current_url">
					<span class="breadcrumb_text"><xsl:value-of select="label"/></span>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test=".//children/menu[url=$current_url]">
					<xsl:choose>
						<xsl:when test="$link='0'"><a class="breadcrumb_link"><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:value-of select="label"/></a> [[rightarrow]] </xsl:when>
						<xsl:otherwise><span class="breadcrumb_text"><xsl:value-of select="label"/></span>&#32;[[rightarrow]]&#32;</xsl:otherwise>
					</xsl:choose>
					<xsl:call-template name="display_breadcrumb_parent">
						<xsl:with-param name="parent_identifier" select="@identifier"/>       
						<xsl:with-param name="link" select="$link"/>
						<xsl:with-param name="current_url" select="$current_url"/>       
    			   	</xsl:call-template>
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>