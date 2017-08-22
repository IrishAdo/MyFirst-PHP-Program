<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2004/10/05 20:02:20 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="sitemap">
		<xsl:for-each select="//xml_document/modules/module[@name='presentation']/page[position()=1]">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="title">1</xsl:with-param>
				<xsl:with-param name="alt_title">1</xsl:with-param>
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="style">LOCATION</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template>
		</xsl:for-each>
		<xsl:for-each select="//xml_document/modules/module[@name='presentation']/page[position()!=1]">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="alt_title">1</xsl:with-param>
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template>		
		</xsl:for-each>
		<xsl:call-template name="display_modules">
			<xsl:with-param name="ignore" select="'[page]'"/>
		</xsl:call-template>

	<xsl:if test="xml_document/debugging">
		<xsl:comment> debug Data </xsl:comment>
		<xsl:apply-templates select="xml_document/debugging"/>
	</xsl:if>
</xsl:template>

<!--
	function sitemap_default()
	
	simple UL and li tags used
-->
<xsl:template name="sitemap_default">

<xsl:variable name="showhidden"><xsl:value-of select="//module[@name='sitemap']/show"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="$image_path = '/libertas_images/themes/textonly'"><xsl:call-template name="display_menu"/></xsl:when>
		<xsl:otherwise>
			<div id='sitemap'>
			<h1 class='label'><span><xsl:choose>
						<xsl:when test="//setting[@name='sp_page_title_is_caps']='Yes'">SITEMAP</xsl:when>
						<xsl:otherwise>Sitemap</xsl:otherwise>
					</xsl:choose></span></h1>
			<div class="contentpos">
				<ul class="level0">
				<xsl:for-each select="//xml_document/modules/module[@name='layout']/menu[((@hidden=1 and $showhidden=1) or (@hidden=0))]">
					<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
					<li class="sitemap0"><a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
							<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
								<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
							</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
							<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a>
						<xsl:if test="children/menu[((@hidden=1 and $showhidden=1) or (@hidden=0))]">
						<ul>
							<xsl:call-template name="display_sitemap_children"><xsl:with-param name="id"><xsl:value-of select="@identifier"/></xsl:with-param><xsl:with-param name="bc_trail"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JUMP_TO'"/></xsl:call-template> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:with-param></xsl:call-template>
						</ul>
						</xsl:if>
					</li>
					</xsl:if>
				</xsl:for-each>
				</ul>
			</div>
			</div>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_sitemap_children">
	<xsl:param name="bc_trail"></xsl:param>			
	<xsl:param name="li">1</xsl:param>			
	<xsl:param name="id">-1</xsl:param>			
	<xsl:variable name="showhidden"><xsl:value-of select="//module[@name='sitemap']/show"/></xsl:variable>
	
	<xsl:for-each select="//children/menu[((@hidden=1 and $showhidden=1) or (@hidden=0)) and @parent=$id]">
		<xsl:choose>
			<xsl:when test="$li=1">
				<li class="sitemap"><a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="$bc_trail"/> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:attribute>
						<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
							<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
						<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a>
				<xsl:if test="children/menu[((@hidden=1 and $showhidden=1) or (@hidden=0))]">
					<ul>
					<xsl:call-template name="display_sitemap_children">
						<xsl:with-param name="li">1</xsl:with-param>
						<xsl:with-param name="id"><xsl:value-of select="@identifier"/></xsl:with-param>
						<xsl:with-param name="bc_trail"><xsl:value-of select="$bc_trail"/> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:with-param>
					</xsl:call-template>
					</ul>
				</xsl:if>
				</li>
			</xsl:when>
			<xsl:otherwise>
				:: <a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="$bc_trail"/> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:attribute>
						<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
							<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
						<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a>
				<xsl:if test="children/menu[((@hidden=1 and $showhidden=1) or (@hidden=0))]">
					<ul>
					<xsl:call-template name="display_sitemap_children">
						<xsl:with-param name="li">0</xsl:with-param>
						<xsl:with-param name="id"><xsl:value-of select="@identifier"/></xsl:with-param>
						<xsl:with-param name="bc_trail"><xsl:value-of select="$bc_trail"/> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:with-param>
					</xsl:call-template>
					</ul>
				</xsl:if>
				<br/>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:for-each>
</xsl:template>
<!--
	function sitemap_columns()

-->

<xsl:template name="sitemap_columns">
	<xsl:variable name="showhidden"><xsl:value-of select="//module[@name='sitemap']/show"/></xsl:variable>
		<xsl:for-each select="//modules/module/menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3) = 1)]">
			<div class='columncount3'>
				<div class="tableheader">[[rightarrow]] <a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a> [[leftarrow]]</div>
				<xsl:if test="children/menu"><div>
					<ul class="level0">
					<xsl:call-template name="display_sitemap_children">
						<xsl:with-param name="li">0</xsl:with-param>
						<xsl:with-param name="id"><xsl:value-of select="@identifier"/></xsl:with-param>
						<xsl:with-param name="bc_trail"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JUMP_TO'"/></xsl:call-template> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:with-param>
					</xsl:call-template>
					</ul></div>
				</xsl:if>
			</div>
			<xsl:if test="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=1)]">
			<div class='columncount3'>
				<div class="tableheader">[[rightarrow]] <a><xsl:attribute name="href"><xsl:value-of select="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=1)]/url"/></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=1)]/label"/></xsl:with-param></xsl:call-template></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=1)]/label"/></xsl:with-param></xsl:call-template></a> [[leftarrow]]</div>
				<xsl:if test="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=1)]/children/menu"><div>
					<ul class="level0">
						<xsl:call-template name="display_sitemap_children">
						<xsl:with-param name="li">0</xsl:with-param>
						<xsl:with-param name="id"><xsl:value-of select="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=1)]/@identifier"/></xsl:with-param>
						<xsl:with-param name="bc_trail"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JUMP_TO'"/></xsl:call-template> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=1)]/label"/></xsl:with-param></xsl:call-template></xsl:with-param>
						</xsl:call-template>
					</ul></div>
				</xsl:if>
			</div>
			</xsl:if>
			<xsl:if test="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0)) ][ ((position() mod 3)=2)]">
			<div class='columncount3'>
				<div class="tableheader">[[rightarrow]] <a><xsl:attribute name="href"><xsl:value-of select="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=2)]/url"/></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=2)]/label"/></xsl:with-param></xsl:call-template></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=2)]/label"/></xsl:with-param></xsl:call-template></a> [[leftarrow]]</div>
				<xsl:if test="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=2)]/children/menu"><div>
					<ul class="level0">
					<xsl:call-template name="display_sitemap_children">
						<xsl:with-param name="li">0</xsl:with-param>
						<xsl:with-param name="id"><xsl:value-of select="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=2)]/@identifier"/></xsl:with-param>
						<xsl:with-param name="bc_trail"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_JUMP_TO'"/></xsl:call-template> - <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="following-sibling::menu[((@hidden=1 and $showhidden=1) or (@hidden=0))][((position() mod 3)=2)]/label"/></xsl:with-param></xsl:call-template></xsl:with-param>
					</xsl:call-template>
					</ul></div>
				</xsl:if>
			</div>
			</xsl:if>
	</xsl:for-each>
</xsl:template>
</xsl:stylesheet>
