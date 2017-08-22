<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.10 $
- Modified $Date: 2005/03/10 12:07:24 $
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
<xsl:comment>display information in two columns.</xsl:comment>
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
			<div class="page" id="page1">test<h1 class='entrytitle' id="notitlepage"><span><xsl:value-of select="$page_title_string"/></span></h1></div>
	</xsl:otherwise>
</xsl:choose>	
<xsl:for-each select="//xml_document/modules/container/webobject/module/page[position()!=$title_page][(position() mod 2)=1]">
	<div class='multicolumn'>
		<div class='columncount2'>
			[[rightarrow]] <a align='left'><xsl:attribute name="title"><xsl:choose>
				<xsl:when test="metadata/description!=''"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="translate(metadata/description,'&amp;','')" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>...</xsl:when>
				<xsl:otherwise><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:otherwise>
			</xsl:choose></xsl:attribute>
			<xsl:attribute name="href"><xsl:value-of select="locations/location[@url=//xml_document/modules/module/setting[@name='script']]"/></xsl:attribute>
			<xsl:value-of select="title" disable-output-escaping="yes"/></a> 
			<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param><xsl:with-param name="identifier"><xsl:choose>
				<xsl:when test="//modules/module/setting/licence/product/@type='ECMS'"><xsl:value-of select="following-sibling::page[(position() mod 3) = 1]/@identifier"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="following-sibling::page[(position() mod 3) = 1]/@translation_identifier"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template>
		</div>
			<xsl:if test="following-sibling::page[(position() mod 2)=1]">
		<div class='columncount2'>
			[[rightarrow]] <a align='left'><xsl:attribute name="title"><xsl:choose>
				<xsl:when test="following-sibling::page[(position() mod 3) = 1]/metadata/description!=''"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="translate(following-sibling::page[(position() mod 3) = 1]/metadata/description,'&amp;','')" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>...</xsl:when>
				<xsl:otherwise><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="following-sibling::page[(position() mod 3) = 1]/title" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:otherwise>
			</xsl:choose></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="following-sibling::page[(position() mod 2) = 1]/locations/location[@url=//xml_document/modules/module/setting[@name='script']]"/></xsl:attribute><xsl:value-of select="following-sibling::page[(position() mod 2) = 1]/title" disable-output-escaping="yes"/></a> <xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param><xsl:with-param name="identifier"><xsl:choose>
				<xsl:when test="//modules/module/setting/licence/product/@type='ECMS'"><xsl:value-of select="following-sibling::page[(position() mod 3) = 1]/@identifier"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="following-sibling::page[(position() mod 3) = 1]/@translation_identifier"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template>
		</div>
		</xsl:if>
	</div>
</xsl:for-each>
	<xsl:if test="//xml_document/modules/container/webobject/module/headline">
		<xsl:call-template name='show_headlines'>
			<xsl:with-param name='cols'>2</xsl:with-param>
		</xsl:call-template>
	</xsl:if>


<xsl:call-template name="display_modules"><!-- supply the ignore tags LATEST is a display attribute and polls is a name attribute--><xsl:with-param name="ignore" select="'[page]'"/></xsl:call-template>

	<xsl:if test="xml_document/debugging">
		<xsl:comment> debug Data </xsl:comment>
		<xsl:apply-templates select="xml_document/debugging"/>
	</xsl:if></xsl:template>

</xsl:stylesheet>


