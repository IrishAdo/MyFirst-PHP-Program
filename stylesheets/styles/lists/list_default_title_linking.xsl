<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.7 $
- Modified $Date: 2004/09/15 10:47:59 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
default FAQ look and feel
-->
<xsl:include href="../../styles/pages/display_alphabet_title_content.xsl"/>
<xsl:template name="display_list">
	<xsl:variable name="title_page"><xsl:choose>
		<xsl:when test="//menu[url=//modules/module/setting[@name='script']]/@title_page=1">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<a id="top_of_content"></a>
	<xsl:choose>
		<xsl:when test="$title_page=1">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
				<xsl:with-param name="alt_title">1</xsl:with-param>
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="date_publish">0</xsl:with-param>
				<xsl:with-param name="more">0</xsl:with-param>
				<xsl:with-param name="style">LOCATION</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="//modules/container/webobject/module/page[position()=1]/@identifier"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:variable name="page_title_string"><xsl:choose>
				<xsl:when test="count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page) != 1"><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></xsl:when>
				<xsl:when test="//setting[@name='fake_title']!=''"><xsl:value-of select="//setting[@name='fake_title']" disable-output-escaping="yes"/></xsl:when>
				<xsl:when test="//setting[@name='real_script']='index.php'"><xsl:value-of select="//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:when>
				<xsl:when test="not(contains(//setting[@name='real_script'],'index.php')) and boolean(//module[@name='presentation' and @display='ATOZ']/letters) and count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page)!=1"><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></xsl:when>
				<xsl:when test="not(contains(//setting[@name='real_script'],'index.php')) and boolean(//module[@name='presentation' and @display='ATOZ']/letters) "><xsl:call-template name="display_firstpage"/></xsl:when>
				<xsl:when test="not(contains(//setting[@name='script'],//setting[@name='fake_script']))"><xsl:value-of select="//modules/container/webobject/module[@name='information_presentation']/content/entry/seperator_row/seperator/field[@name='ie_title']/value" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:call-template name="display_firstpage"/></xsl:otherwise>
				</xsl:choose></xsl:variable>
			
			<div class="page" id="page1"><h1 class='entrytitle' id="notitlepage"><span><xsl:value-of select="$page_title_string"/></span></h1></div>
		</xsl:otherwise>
	</xsl:choose>	
<div class="pagetitles">
	<ul class='pages'>
	<xsl:for-each select="//modules/container/webobject/module[@display='ENTRY']/page">
		<xsl:if test="position()!=$title_page">
			<li><a><xsl:attribute name="title"><xsl:choose>
				<xsl:when test="metadata/description!=''"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="translate(metadata/description,'&amp;','')" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>...</xsl:when>
				<xsl:otherwise><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:otherwise>
			</xsl:choose></xsl:attribute><xsl:attribute name="href"><xsl:choose>
				<xsl:when test="locations/location[@url=//module/setting[@name='script']]"><xsl:value-of select="locations/location[@url=//module/setting[@name='script']]"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="locations/location[position()=1]"/></xsl:otherwise>
			</xsl:choose></xsl:attribute><xsl:value-of disable-output-escaping="yes" select="title"/></a> 
			<xsl:call-template name="show_edit_button">
				<xsl:with-param name="cmd_starter">PAGE_</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:choose>
					<xsl:when test="//modules/module/setting/licence/product/@type='ECMS'"><xsl:value-of select="@identifier"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="@translation_identifier"/></xsl:otherwise>
				</xsl:choose></xsl:with-param>
				</xsl:call-template></li>
		</xsl:if>
	</xsl:for-each>
	</ul>
	</div>
	<xsl:comment> end list Data </xsl:comment>
</xsl:template>

</xsl:stylesheet>