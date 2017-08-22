<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2004/10/05 11:03:49 $
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
	<xsl:comment>pages\display_main.xsl</xsl:comment>
	
	<xsl:choose>
		<xsl:when test="count(page)=1">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
				<xsl:with-param name="alt_title">1</xsl:with-param>
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="enable_discussion">1</xsl:with-param>
				<xsl:with-param name="style">LOCATION</xsl:with-param>
				<xsl:with-param name="file_location">none</xsl:with-param>
				<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="//modules/container/webobject/module[@display='ENTRY']/page[position()=1]/@identifier"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:for-each select="page">
				<xsl:choose>
				<xsl:when test="position()=1">
				<xsl:call-template name="display_this_page">
					<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
					<xsl:with-param name="alt_title">1</xsl:with-param>
					<xsl:with-param name="file_location">none</xsl:with-param>
					<xsl:with-param name="content">1</xsl:with-param>
					<xsl:with-param name="back">1</xsl:with-param>
					<xsl:with-param name="date_publish">0</xsl:with-param>
					<xsl:with-param name="style">LOCATION</xsl:with-param>
					<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
					<xsl:with-param name="enable_discussion">1</xsl:with-param>
					<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
				</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
				<xsl:call-template name="display_this_page">
					<xsl:with-param name="title">1</xsl:with-param>
					<xsl:with-param name="alt_title">1</xsl:with-param>
					<xsl:with-param name="content">1</xsl:with-param>
					<xsl:with-param name="back">1</xsl:with-param>
					<xsl:with-param name="date_publish">0</xsl:with-param>
					<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
					<xsl:with-param name="enable_discussion">1</xsl:with-param>
					<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
				</xsl:call-template>
				</xsl:otherwise>
				</xsl:choose>
	   		</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:if test="//xml_document/modules/container/webobject/module/headline">
		<xsl:call-template name='show_headlines'>
			<xsl:with-param name='cols'>3</xsl:with-param>
		</xsl:call-template>
	</xsl:if>
<!--
	<xsl:if test="substring(string(//menu/@display-options),'PRESENTATION_DISPLAY')">
	 	<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/></xsl:attribute>Return to '<xsl:value-of select="//menu[url=//setting[@name='script']]/label"/>'</a>
	</xsl:if >
-->
	<xsl:if test="xml_document/debugging">
		<xsl:comment> debug Data </xsl:comment>
		<xsl:apply-templates select="xml_document/debugging"/>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>


