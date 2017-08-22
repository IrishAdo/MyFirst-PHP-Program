<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2004/10/05 11:03:50 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
default FAQ look and feel
-->
<xsl:template name="display_slideshow">
	<xsl:param name="display_more_as_text"/>
	<xsl:param name="displaydigits">1</xsl:param>
	<xsl:param name="uses_class"/>
	<xsl:param name="pos">TOP</xsl:param>
	<xsl:if test="$image_path!='/libertas_images/themes/textonly'">
	<xsl:if test="contains($pos,'TOP')">
		<xsl:call-template name="slideshowspan">
			<xsl:with-param name="display_more_as_text"><xsl:value-of select="display_more_as_text"/></xsl:with-param>
			<xsl:with-param name="displaydigits"><xsl:value-of select="displaydigits"/></xsl:with-param>
		</xsl:call-template>
	</xsl:if>
	</xsl:if>
	<xsl:for-each select="page">
		<xsl:call-template name="display_this_page">
			<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
			<xsl:with-param name="alt_title">1</xsl:with-param>
			<xsl:with-param name="content">1</xsl:with-param>
			<xsl:with-param name="date_publish">0</xsl:with-param>
			<xsl:with-param name="more">0</xsl:with-param>
			<xsl:with-param name="style">LOCATION</xsl:with-param>
			<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
		</xsl:call-template>
	</xsl:for-each>
	<xsl:if test="contains($pos,'BOTTOM')">
	<xsl:call-template name="slideshowspan">
		<xsl:with-param name="display_more_as_text"><xsl:value-of select="display_more_as_text"/></xsl:with-param>
		<xsl:with-param name="displaydigits"><xsl:value-of select="displaydigits"/></xsl:with-param>
	</xsl:call-template>
	</xsl:if>
	<xsl:if test="//xml_document/modules/container/webobject/module/headline">
		<xsl:call-template name='show_headlines'>
			<xsl:with-param name='cols'>3</xsl:with-param>
		</xsl:call-template>
	</xsl:if>
</xsl:template>

<xsl:template name="slideshowspan">
	<xsl:param name="display_more_as_text"/>
	<xsl:param name="displaydigits">0</xsl:param>
	<div><xsl:attribute name="class">slideshowlinklocation</xsl:attribute>	
	<xsl:choose>
		<xsl:when test="$displaydigits=0">
			<ul class="slideshow">
			<xsl:for-each select="pagelists/page">
				<xsl:if test="../@found=@id">
					<xsl:if test="position()!=1">
						<li><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='script']"/>?page=<xsl:value-of select="@index - 1"/></xsl:attribute>[[leftarrow]][[nbsp]]Previous</a></li>
					</xsl:if>
						<li><a><xsl:attribute name="title"><xsl:value-of select="label"/></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='script']"/>?page=1</xsl:attribute>back to start</a></li>
					<xsl:if test="position()!=last()">
						<li><a><xsl:attribute name="title"><xsl:value-of select="following-sibling::page/title"/></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='script']"/>?page=<xsl:value-of select="@index + 1"/></xsl:attribute>next[[nbsp]][[rightarrow]]</a></li>
					</xsl:if>
				 </xsl:if>
			 </xsl:for-each>
			 </ul>
		</xsl:when>
		<xsl:otherwise>	
			<ul class="slideshow">
			<xsl:for-each select="pagelists/page">
				<xsl:if test="../@found=@id">
					<xsl:if test="position()!=1">
						<xsl:variable name="index"><xsl:value-of select="@index"/></xsl:variable>
						<xsl:variable name="pos"><xsl:value-of select="position() - 1"/></xsl:variable>
						<li><a><xsl:attribute name="title"><xsl:value-of select="../page[position()=$pos]/label"/></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="../page[@index = ($index - 1)]/url"/></xsl:attribute>[[leftarrow]][[nbsp]]Previous</a></li>
					</xsl:if>
				</xsl:if>
			</xsl:for-each>
			<xsl:for-each select="pagelists/page">
				<xsl:variable name="id"><xsl:value-of select="@id"/></xsl:variable>
				<xsl:choose>
					<xsl:when test="../@found=@id"><li class='active'><xsl:value-of select="@index"/></li></xsl:when>
					<xsl:otherwise><li><a><xsl:attribute name="title"><xsl:value-of select="label"/></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="url"/></xsl:attribute><xsl:value-of select="@index"/></a></li></xsl:otherwise>
				</xsl:choose></xsl:for-each>
			<xsl:for-each select="pagelists/page">
				<xsl:if test="../@found=@id">
					<xsl:if test="position()!=last()">
						<xsl:variable name="index"><xsl:value-of select="@index"/></xsl:variable>
						<xsl:variable name="pos"><xsl:value-of select="position() + 1"/></xsl:variable>
						<li><a><xsl:attribute name="title"><xsl:value-of select="../page[position()=$pos]/label"/></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="../page[@index = ($index + 1)]/url"/></xsl:attribute>next[[nbsp]][[rightarrow]]</a></li>
					</xsl:if>
				</xsl:if>
			</xsl:for-each>	
			</ul>
		</xsl:otherwise>
	</xsl:choose>	
	 </div>
</xsl:template>

</xsl:stylesheet>

