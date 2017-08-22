<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.7 $
- Modified $Date: 2005/01/11 20:13:18 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
default FAQ look and feel
-->
<xsl:template name="display_atoz">
	<xsl:param name="display_more_as_text"/>
	<xsl:param name="uses_class"/>
	<xsl:if test="$image_path='/libertas_images/themes/textonly'">
		<hr/>
	</xsl:if>
	<xsl:variable name="showinpage"><xsl:choose>
			<xsl:when test="contains(//setting[@name='real_script'], 'index.php')">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose></xsl:variable>
	<xsl:if test="$showinpage = 0">
		<xsl:if test="title_page_string">
		<div class="page" id="page1"><h1 class="entrylocation" id="pageheader1"><span><xsl:value-of select="title_page_string"/></span></h1>
					<xsl:call-template name="display_atoz_links"/>
			<div class="contentpos">
		</div></div>
		</xsl:if>
	</xsl:if>
	<xsl:if test="letters/@choosenletter=''">
		<xsl:call-template name="display_this_page">
			<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
			<xsl:with-param name="alt_title">1</xsl:with-param>
			<xsl:with-param name="content">1</xsl:with-param>
			<xsl:with-param name="date_publish">0</xsl:with-param>
			<xsl:with-param name="more">0</xsl:with-param>
			<xsl:with-param name="showinpage"><xsl:value-of select="$showinpage"/></xsl:with-param>
			<xsl:with-param name="style">LOCATION</xsl:with-param>
			<xsl:with-param name="identifier"><xsl:value-of select="page[position()=1]/@identifier"/></xsl:with-param>
		</xsl:call-template>
	</xsl:if>
	<xsl:if test="letters/@choosenletter!=''">
		<xsl:for-each select="page">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
				<xsl:with-param name="alt_title">0</xsl:with-param>
				<xsl:with-param name="summary">1</xsl:with-param>
				<xsl:with-param name="date_publish">0</xsl:with-param>
				<xsl:with-param name="increment_page_id">1</xsl:with-param>
				<xsl:with-param name="more">0</xsl:with-param>
				<xsl:with-param name="title_is_link">1</xsl:with-param>
				<xsl:with-param name="style">LOCATION</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
	</xsl:if>
	<xsl:if test="filter/form">
		<div class="filter"><xsl:call-template name="display_form">
			<xsl:with-param name="module"><xsl:value-of select="@name"/></xsl:with-param>
			<xsl:with-param name="id"><xsl:value-of select="filter/form/@name"/></xsl:with-param>
		</xsl:call-template></div>
	</xsl:if>
	<xsl:if test="//xml_document/modules/container/webobject/module/headline">
		<xsl:call-template name='show_headlines'>
		</xsl:call-template>
	</xsl:if>

</xsl:template>
</xsl:stylesheet>

