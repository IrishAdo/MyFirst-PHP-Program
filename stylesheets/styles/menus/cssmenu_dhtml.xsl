<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2005/01/11 16:35:37 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:variable name="current_site_depth"><xsl:value-of select="//menu[url=//setting[@name='script']]/@depth"/></xsl:variable>
<xsl:variable name="menu_splits_at_depth">-1</xsl:variable>
<xsl:template name="display_menu">
	<div id='mainmenu'>
		<xsl:for-each select="//xml_document/modules/module[@name='layout']">
			<ul class='level1'>
				<xsl:call-template name="display_menu_parent">
					<xsl:with-param name="parent_identifier" select="-1"/>       
					<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
    	   		</xsl:call-template>
			</ul>
		</xsl:for-each>
	</div>
</xsl:template>

<xsl:template name="display_submenu">
</xsl:template>

<xsl:template name="display_menu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
	<xsl:param name="depth">2</xsl:param>
	<xsl:for-each select="menu[@parent=$parent_identifier and @hidden='0']">
		<xsl:variable name="found">
			<xsl:if test="url='admin/index.php' and //xml_document/modules/session/groups/@type=2">2</xsl:if>
			<xsl:choose>
				<xsl:when test="boolean(groups)">
					<xsl:for-each select="groups/option">
						<xsl:variable name="val"><xsl:value-of select="@value"/></xsl:variable>
						<xsl:for-each select="//xml_document/modules/session/groups/group">
							<xsl:if test="$val=@identifier">1</xsl:if>
						</xsl:for-each>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>1</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:if test="($found!='') or count(groups/option)=0">
		<li>
			<xsl:variable name="path"><xsl:value-of select="substring-before(url,'/index.php')"/></xsl:variable>
			<xsl:if test="//fake_menu[@url = $path] or ./children/menu[@hidden=0]"><xsl:attribute name='class'>folder</xsl:attribute></xsl:if>
		<a>
			<xsl:if test="//fake_menu[@url = $path] or ./children/menu[@hidden=0]"><xsl:attribute name='class'>submenu</xsl:attribute></xsl:if>
		
			<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
			<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
			<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
			<xsl:if test="//fake_menu[@url = $path]">
				<xsl:value-of select="//fake_menu[@url = $path]"/>
			</xsl:if>
			<xsl:if test="./children/menu">
				<ul><xsl:attribute name='class'>level<xsl:value-of select="@depth + 1"/></xsl:attribute>
					<xsl:for-each select="./children">
	    				<xsl:call-template name="display_menu_parent">
							<xsl:with-param name="parent_identifier" select="../@identifier"/>       
							<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
		    				<xsl:with-param name="depth" select="$depth + 2"/>       
		       			</xsl:call-template>
		       		</xsl:for-each>
				</ul>
	    	</xsl:if>
			</li>
    	</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_indent">
	<xsl:param name="depth"/>
	<xsl:if test="$depth>1">
		[[nbsp]]-[[nbsp]]
		<xsl:variable name="new_depth"><xsl:value-of select="$depth - 1"/></xsl:variable>
		<xsl:call-template name="display_indent">
			<xsl:with-param name="depth" select="$new_depth"/>       
       	</xsl:call-template>
	</xsl:if>
</xsl:template>


</xsl:stylesheet>