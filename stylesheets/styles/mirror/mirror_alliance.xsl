<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/09/15 10:38:23 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"> 
  
<xsl:template name="display_mirror">
	<xsl:param name="header"><xsl:value-of select="//module[@name='mirror']/label" disable-output-escaping="yes"/></xsl:param>
	<xsl:param name="display_option"><xsl:value-of select="//module[@name='mirror']/display"/></xsl:param>
	<xsl:param name="max_list_count"><xsl:value-of select="//module[@name='mirror']/size"/></xsl:param>
	<xsl:param name="hr_return">1</xsl:param>
	<xsl:param name="hr">1</xsl:param>
	<xsl:param name="width">100%</xsl:param>
	<xsl:param name="class">nojustify</xsl:param>
	<xsl:param name="type"></xsl:param>
	<xsl:param name="display_more_as_text"><xsl:value-of select="$more_text"/></xsl:param>
	<xsl:param name="mirror_starter"></xsl:param>
	<xsl:param name="title_starter">[[rightarrow]]</xsl:param>
	<xsl:param name="bullet_width">16</xsl:param>
	<xsl:param name="bullet_height">16</xsl:param>
	<xsl:param name="title_bullet">0</xsl:param>
	<xsl:param name="label_bullet">0</xsl:param>
	<xsl:param name="inTable">0</xsl:param>
	
	<!--
		Define variables from xml data to define the look and feel of the mirror
	-->
	<xsl:if test="//xml_document/modules/container/webobject/module[@name='mirror']">
		<xsl:variable name="title_link"><xsl:choose>
			<xsl:when test="$display_option='TITLE'">1</xsl:when>
			<xsl:when test="contains($display_option,'TITLE') and not(contains($display_option,'READMORE'))">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose></xsl:variable>
		<xsl:variable name="summary"><xsl:choose>
			<xsl:when test="contains($display_option,'SUMMARY')">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose></xsl:variable>
		<xsl:variable name="more"><xsl:choose>
			<xsl:when test="contains($display_option,'READMORE')">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose></xsl:variable>
		<xsl:variable name="display_date"><xsl:choose>
			<xsl:when test="contains($display_option,'DATE')">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose></xsl:variable>
		<xsl:variable name="new_header">
			<xsl:if test="$label_bullet=1">&lt;img border="0" src="<xsl:value-of select="$image_path"/>/title_bullet.gif"&gt;[[nbsp]]</xsl:if>
			<xsl:choose>
				<xsl:when test="$header=''"><xsl:value-of select="//menu[url=//xml_document/modules/container/webobject/module[@name='mirror']/menulocation]/label"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="$header"/></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<table width="95%" border="0" cellspacing="2" cellpadding="1" style="margin-left:3px;" summary="">
			<tr><td class="orange"><span class="mirrorlabel"><xsl:value-of select="$mirror_starter"/><a class="mirrorlabel"><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/container/webobject/module[@name='mirror']/menulocation"/></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="$new_header" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a></span></td></tr>
			<tr><td height="0.001%"><span align="center"><img width="146" height="2" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img></span></td></tr>
			<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='mirror']/module/page[position()!=//menu[url=//xml_document/modules/container/webobject/module[@name='mirror']/menulocation]/@title_page][$max_list_count >= position()]">
				<xsl:variable name="page_date"><xsl:value-of select="metadata/date[@refinement='available']"/></xsl:variable>
	  		<tr><td class="news">
			<a class="news"><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute>
			<xsl:if test="metadata/date[@refinement='available']!=''">
			<xsl:call-template name="format_date">
				<xsl:with-param name="current_date"><xsl:value-of select="$page_date"/></xsl:with-param>
			</xsl:call-template><br/>
			</xsl:if><xsl:call-template name="print">
				<xsl:with-param name="str_value"><xsl:copy-of select="title"/></xsl:with-param>
			</xsl:call-template></a>
			<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
			</td></tr>
			<tr><td height="0.001%"><span align="center"><img width="146" height="2" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img></span></td></tr>
			</xsl:for-each>
		</table>
	</xsl:if>
</xsl:template>


</xsl:stylesheet>