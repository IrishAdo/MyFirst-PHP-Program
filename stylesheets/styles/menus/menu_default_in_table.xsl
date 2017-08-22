<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:14 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- This style sheet is to be used to produce a split menu approach
	-
	- first level menu entries will be displayed in one position in a horizontal format
	- sub level menu options will be displayed in a vertical menu in another location.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-->
<xsl:variable name="current_site_depth"><xsl:value-of select="//menu[url=//setting[@name='script']]/@depth"/></xsl:variable>
<xsl:variable name="menu_splits_at_depth">-1</xsl:variable>
<xsl:template name="display_menu">
	<xsl:param name="class_to_use" select="''"/>
		<xsl:variable name="header"><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></xsl:variable>		
		<xsl:variable name="content">
		<table summary="">
			<xsl:choose>
				<xsl:when test="//menu[url=//setting[@name='script']]"><xsl:call-template name="display_parent">
					<xsl:with-param name="parent"><xsl:value-of select="//menu[url=//setting[@name='script']]/@parent"/></xsl:with-param>
				</xsl:call-template></xsl:when>
				<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
		</table>
		</xsl:variable>
		<xsl:call-template name="display_a_table">
			<xsl:with-param name="header"><xsl:value-of select="$header" disable-output-escaping="yes"/></xsl:with-param>
			<xsl:with-param name="content"><xsl:copy-of select="$content"/></xsl:with-param>
		</xsl:call-template>
</xsl:template>


<xsl:template name="display_parent">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']/menu">
			<xsl:choose>
				<xsl:when test=".//menu[url=//setting[@name='script'] and @hidden=0] or url=//setting[@name='script']">
						<xsl:call-template name="display_submenu"></xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<tr><td><xsl:attribute name='style'>padding-left:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute><a>
					<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
						<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
					<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:attribute name="class">menulevel0</xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td></tr>
				</xsl:otherwise>
			</xsl:choose>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_menu_parent_children">
</xsl:template>

<xsl:template name="display_indent">
	<xsl:param name="depth"/>
	<xsl:if test="$depth>=2">
		[[nbsp]][[nbsp]]
		<xsl:variable name="new_depth"><xsl:value-of select="$depth - 1"/></xsl:variable>
		<xsl:call-template name="display_indent">
			<xsl:with-param name="depth" select="$new_depth"/>       
       	</xsl:call-template>
	</xsl:if>
</xsl:template>

<xsl:template name="display_submenu">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
		<xsl:for-each select="menu">
			<xsl:choose>
			<xsl:when test="url=//setting[@name='script'] and @hidden=0">
					<tr><td><xsl:attribute name='style'>padding-left:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute><a>
			<xsl:if test="@accesskey!=''">
				<xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute>
			</xsl:if>
			<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
			<xsl:attribute name="class">menulevel0</xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td></tr>
				<xsl:if test="children/menu[@hidden=0]">
					<xsl:call-template name="display_submenu_parent">
						<xsl:with-param name="parent_identifier" select="@identifier"/>       
						<xsl:with-param name="destination_identifier" select="@identifier"/>
	    	   		</xsl:call-template>
				</xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test=".//menu[url=//setting[@name='script'] and @hidden=0]">
					<tr><td><xsl:attribute name='style'>padding-left:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute><a>
					<xsl:if test="@accesskey!=''">
						<xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute>
					</xsl:if>
					<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
					<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
						<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
					<xsl:attribute name="class">menulevel0</xsl:attribute>
					<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td></tr>
					<xsl:call-template name="display_submenu_parent">
						<xsl:with-param name="parent_identifier" select="@identifier"/>       
						<xsl:with-param name="destination_identifier" select=".//menu[url=//setting[@name='script']]/@identifier"/>
			       	</xsl:call-template>
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:for-each>
    	
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_submenu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="destination_identifier"/>
	<xsl:variable name="url"><xsl:value-of select="//setting[@name='script']"/></xsl:variable>
	<xsl:for-each select="//children/menu[@parent=$parent_identifier and @hidden=0]">
		<xsl:variable name="parent"><xsl:value-of select="@identifier"/></xsl:variable>
		<xsl:variable name="open_folder">
			<xsl:if test=".//children/menu[@identifier=$destination_identifier]">1</xsl:if>
			<xsl:if test="following-sibling::menu[url=$url]">1</xsl:if>
			<xsl:if test="preceding-sibling::menu[url=$url]">1</xsl:if>
			<xsl:if test="@parent=$destination_identifier">2</xsl:if>
			<xsl:if test="@identifier=$destination_identifier">3</xsl:if>
		</xsl:variable>
		<xsl:if test="$open_folder > 0"><tr><td><xsl:attribute name='style'>padding-left:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute><a><xsl:attribute name="class">menulevel0</xsl:attribute>
			<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
			<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
			<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td></tr>
		</xsl:if> 
		<xsl:if test=".//menu[url=$url and @hidden=0]">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
				<xsl:with-param name="destination_identifier" select="$destination_identifier"/>
   			</xsl:call-template>
		</xsl:if>
		<xsl:if test="url=$url and @hidden=0">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
				<xsl:with-param name="destination_identifier" select="$destination_identifier"/>
   			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>