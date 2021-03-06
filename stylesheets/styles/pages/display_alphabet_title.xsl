<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.8 $
- Modified $Date: 2004/10/05 11:03:48 $
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
		<xsl:call-template name="display_atoz_links"/>
	</xsl:if>

	<xsl:if test="letters/@choosenletter=''">
		<xsl:call-template name="display_this_page">
			<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
			<xsl:with-param name="alt_title">1</xsl:with-param>
			<xsl:with-param name="content">1</xsl:with-param>
			<xsl:with-param name="date_publish">0</xsl:with-param>
			<xsl:with-param name="more">0</xsl:with-param>
			<xsl:with-param name="style">LOCATION</xsl:with-param>
			<xsl:with-param name="showinpage"><xsl:value-of select="$showinpage"/></xsl:with-param>
			<xsl:with-param name="identifier"><xsl:value-of select="page[position()=1]/@identifier"/></xsl:with-param>
		</xsl:call-template>
	</xsl:if>
	<xsl:if test="letters/@choosenletter!=''">
		<h1 class="entrylocation"><xsl:value-of select="letters/@choosenletter"/></h1>
		<xsl:choose>
		<xsl:when test="count(page)=1">
			<xsl:for-each select="page">
				<xsl:call-template name="display_this_page">
					<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
					<xsl:with-param name="alt_title">1</xsl:with-param>
					<xsl:with-param name="content">1</xsl:with-param>
					<xsl:with-param name="date_publish">0</xsl:with-param>
					<xsl:with-param name="more">0</xsl:with-param>
					<xsl:with-param name="style">LOCATION</xsl:with-param>
					<xsl:with-param name="showinpage"><xsl:value-of select="$showinpage"/></xsl:with-param>
					<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
				</xsl:call-template>
			</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
		<div class="contentpos">
			<ul>
				<xsl:for-each select="page">
					<li class="bulletlist"><a><xsl:attribute name="title"><xsl:choose>
						<xsl:when test="metadata/description!=''"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="translate(metadata/description,'&amp;','')" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>...</xsl:when>
						<xsl:otherwise><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:otherwise>
					</xsl:choose></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:value-of select="title" disable-output-escaping="yes"/></a></li>
				</xsl:for-each>
			</ul>
		</div>
		</xsl:otherwise>
		</xsl:choose>
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

