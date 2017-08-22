<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.6 $
- Modified $Date: 2004/09/27 11:17:28 $
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
	<xsl:comment> start FAQ Data </xsl:comment>
	<xsl:if test="$title_page=1">
	<div id='page1'>
	<xsl:for-each select="//modules/container/webobject/module[@name='presentation']/page[position()=1]">
		<h1 class='entrylocation'><span>
			<xsl:choose>
				<xsl:when test="//setting[@name='sp_page_title_is_caps']='Yes' "><xsl:value-of select="translate(title, 'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose>
			<xsl:call-template name="show_edit_button">
				<xsl:with-param name="cmd_starter">PAGE_</xsl:with-param>
			</xsl:call-template></span></h1>
		<div class="contentpos">
		<xsl:value-of select="content" disable-output-escaping="yes"/>
		</div>
		<xsl:if test="files/file">
			<div class='contentpos'><p><xsl:call-template name="display_files"></xsl:call-template></p></div>
		</xsl:if>
		
	</xsl:for-each>
	</div>
	</xsl:if>
	<div class="contentpos">
	<ul>
	<xsl:for-each select="//modules/container/webobject/module[@name='presentation']/page">
		<xsl:if test="position()!=$title_page">
			<li class="faqbullets"><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>#page<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of disable-output-escaping="yes" select="title"/></a> <xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template></li>
		</xsl:if>
	</xsl:for-each>
	</ul>
	</div>
	<xsl:for-each select="//modules/container/webobject/module[@name='presentation']/page">
		<xsl:if test="position()!=$title_page">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
				<xsl:with-param name="alt_title">0</xsl:with-param>
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="date_publish">0</xsl:with-param>
				<xsl:with-param name="top_of_doc">1</xsl:with-param>
				<xsl:with-param name="more">0</xsl:with-param>
				<xsl:with-param name="style">LOCATION</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>
	<xsl:comment> end FAQ Data </xsl:comment>
</xsl:template>

</xsl:stylesheet>