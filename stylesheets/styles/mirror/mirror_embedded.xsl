<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:57 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"> 
  
<xsl:template name="display_mirror">
	<xsl:param name="header"><xsl:value-of select="//module[@name='mirror']/label"/></xsl:param>
	<xsl:param name="display_option"><xsl:value-of select="//module[@name='mirror']/display"/></xsl:param>
	<xsl:param name="max_list_count"><xsl:value-of select="//module[@name='mirror']/size"/></xsl:param>
	<xsl:param name="hr_return">1</xsl:param>
	<xsl:param name="hr">1</xsl:param>
	<xsl:param name="width">100%</xsl:param>
	<xsl:param name="class">nojustify</xsl:param>
	<xsl:param name="type"></xsl:param>
	<xsl:param name="mirror_starter"></xsl:param>
	<xsl:param name="title_starter"></xsl:param>
	<!--
		Define variables from xml data to define the look and feel of the mirror
	-->
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
		<xsl:if test="$title_bullet=1">&lt;img border="0" src="<xsl:value-of select="$image_path"/>/title_bullet.gif"&gt;[[nbsp]]</xsl:if>
		<xsl:choose>
			<xsl:when test="$header=''"><xsl:value-of select="//menu[url=//xml_document/modules/container/webobject/module[@name='mirror']/menulocation]/label"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="$header"/></xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<table class="mirrortable" width="100%" summary="" cellspacing="1" border="0">
		<tr><td class="mirrorheader"><span class="mirrorlabel"><xsl:value-of select="$mirror_starter"/><a class="mirrorlabel"><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/container/webobject/module[@name='mirror']/menulocation"/></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="$new_header" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">MIRROR_</xsl:with-param></xsl:call-template></span></td></tr>
		<tr><td class="mirrordata"><table border="0" cellspacing="0" cellpadding="3" summary="This table contains documents that have been mirrored from another location.">
		<xsl:attribute name="background"><xsl:value-of select="$image_path"/>/mirror_background.gif</xsl:attribute>
		<xsl:attribute name="width"><xsl:value-of select="$width"/></xsl:attribute>
			<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='mirror']/module/page[position()!=//menu[url=//xml_document/modules/container/webobject/module[@name='mirror']/menulocation]/@title_page][$max_list_count >= position()]">
		  		<tr><xsl:if test="$title_starter!=''"><td valign="top"><xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
				<xsl:value-of select="$title_starter"/>
				[[nbsp]]</td></xsl:if>
				<td valign="top"><xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
				<xsl:variable name="page_date"><xsl:value-of select="metadata/date[@refinement='available']"/></xsl:variable>
				<xsl:choose>
					<xsl:when test="$display_date=1 and contains($display_option,'DATE')">
						<xsl:choose>
							<xsl:when test="$title_link=1 or $summary=0">
								<a class="news"><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:value-of select="$page_date"/> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title"/></xsl:with-param></xsl:call-template></a>
								<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
							</xsl:when>
							<xsl:otherwise><xsl:value-of select="$page_date"/> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:otherwise>
						</xsl:choose>
						<xsl:if test="$summary=1">
						<br/><span class="summary"><xsl:value-of select="summary" disable-output-escaping="yes"/></span>
						</xsl:if>
						<xsl:if test="$more=1">
						<p align="right">
						<a><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><img align="right" border="0">
						<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_MORE.gif</xsl:attribute>
						<xsl:attribute name="width"><xsl:value-of select="$image_more_width"/></xsl:attribute>
						<xsl:attribute name="height"><xsl:value-of select="$image_more_height"/></xsl:attribute>
						<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template> <xsl:value-of select="title"/></xsl:attribute></img></a>
						</p>
						</xsl:if>
						<xsl:if test="$hr=1">
						<xsl:if test="$hr_return=1"><br/><br/></xsl:if>
						<img alt="" border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="$title_link=1 or $summary=0">
								<a class="news"><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a>
								<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
							</xsl:when>
							<xsl:otherwise><span class="news"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></span></xsl:otherwise>
						</xsl:choose><br/>
						<xsl:if test="$summary=1 and contains($display_option,'SUMMARY')">
						<xsl:value-of select="summary" disable-output-escaping="yes"/><br/>
						</xsl:if>
						<xsl:if test="$more=1 and contains($display_option,'READMORE')">
						<a align="right"><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><img align="right" border="0">
						<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_MORE.gif</xsl:attribute>
						<xsl:attribute name="width"><xsl:value-of select="$image_more_width"/></xsl:attribute>
						<xsl:attribute name="height"><xsl:value-of select="$image_more_height"/></xsl:attribute>
						<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template> <xsl:value-of select="title"/></xsl:attribute></img></a>
						</xsl:if>
						<xsl:if test="$hr=1">
						<xsl:if test="$hr_return=1"><br/><br/></xsl:if>
						<img alt="" border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
				</td></tr>
			</xsl:for-each>
		</table></td></tr></table>
	</xsl:template>
</xsl:stylesheet>