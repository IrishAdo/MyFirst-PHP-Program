<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2004/09/19 10:27:45 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="display_list">
	<xsl:param name="display_more_as_text"/>
	<xsl:variable name="title_page"><xsl:choose>
			<xsl:when test="//menu[url=//modules/module/setting[@name='script']]/@title_page=1">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:comment>display title and summary only (turkington).</xsl:comment>
	<table cellpadding="5" cellspacing="5" border="0" width="100%" summary="This table contains a list of articles for this location">
		<tr>
		<td>
		<xsl:if test="$title_page=1">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
				<xsl:with-param name="alt_title">1</xsl:with-param>
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="date_publish">0</xsl:with-param>
				<xsl:with-param name="more">0</xsl:with-param>
				<xsl:with-param name="style">LOCATION</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="//page[position()=1]/@identifier"/></xsl:with-param>
			</xsl:call-template>
		</xsl:if>
		<div class="aligncenter">
		<table cellspacing="0" cellpadding="0" class="width650px" border="0" summary="A three column list of pages.">
			<xsl:for-each select="//xml_document/modules/container/webobject/module/page[position()!=$title_page]">
				<tr>
					<td class="indentpage">
						<xsl:call-template name="display_this_page">
							<xsl:with-param name="title">1</xsl:with-param>
							<xsl:with-param name="alt_title">1</xsl:with-param>
							<xsl:with-param name="display_more_as_text" select="1"/>
							<xsl:with-param name="alt_read_more">Click here to view story in full </xsl:with-param>
							<xsl:with-param name="show_hr" select="1"/>
							<xsl:with-param name="summary">1</xsl:with-param>
							<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
						</xsl:call-template>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<hr/>
					</td>
				</tr>
			</xsl:for-each>
		</table>
		</div>
		</td></tr>
	</table>

	<xsl:if test="xml_document/debugging">
		<xsl:comment> debug Data </xsl:comment>
		<xsl:apply-templates select="xml_document/debugging"/>
	</xsl:if>
	
</xsl:template>

</xsl:stylesheet>

