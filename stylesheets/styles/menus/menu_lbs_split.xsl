<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:23 $
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
<xsl:variable name="menu_splits_at_depth">-1</xsl:variable>
<xsl:variable name="current_site_depth"><xsl:value-of select="//menu[url=//setting[@script]]/@depth"/></xsl:variable>

<xsl:template name="display_menu">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
       <div id="mainmenu"><ul class="topmenu"><xsl:call-template name="display_menu_parent">
			<xsl:with-param name="parent_identifier" select="-1"/>       
			<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
       	</xsl:call-template></ul></div>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_menu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>

	<xsl:for-each select="menu[@parent=$parent_identifier and @hidden='0']">
	<li class="topmenu_level1">
		<a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
		<xsl:if test="url=//setting[@name='script']">
				<xsl:attribute name="class">selected</xsl:attribute>
			</xsl:if>
			<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
	        <xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
		<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
	</li>
	</xsl:for-each>
	
</xsl:template>

<xsl:template name="display_menu_parent_children">
</xsl:template>

<xsl:template name="display_indent">
	<xsl:param name="depth"/>
	<xsl:if test="$depth>1">
		
		<xsl:variable name="new_depth"><xsl:value-of select="$depth - 1"/></xsl:variable>
		<xsl:call-template name="display_indent">
			<xsl:with-param name="depth" select="$new_depth"/>       
       	</xsl:call-template>
	</xsl:if>
</xsl:template>

<xsl:template name="display_submenu">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
		<xsl:for-each select="menu[@hidden='0']">
			<xsl:choose>
				<xsl:when test="url=//setting[@name='script']">
				<div id="active_menu">
				<ul class="main_submenu">
				<div id="menu_this_ident">
				<xsl:call-template name="print">
				<xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></div>
						<xsl:call-template name="display_submenu_parent">
							<xsl:with-param name="parent_identifier" select="@identifier"/>       
							<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
		    		   	</xsl:call-template>
				</ul></div>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test=".//menu[url=//setting[@name='script'] and @hidden='0']">
						
						
						<!--<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template>-->
								
								<ul id="leftmenu"><xsl:call-template name="display_submenu_parent">
								<xsl:with-param name="parent_identifier" select="@identifier"/></xsl:call-template></ul>
						
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_submenu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:for-each select="children/menu[@parent=$parent_identifier and @hidden='0']">
		<li><xsl:attribute name="class">leftmenu<xsl:value-of select="@depth"/><xsl:if test="url=//setting[@name='script']">on</xsl:if></xsl:attribute>
		
		<!--  <xsl:if test="url=//setting[@name='script']">[[rightarrow]]</xsl:if>-->
		<a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
			<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
			<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
		<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
		<xsl:if test="url=//setting[@name='script']"></xsl:if>
		 </li>
		
		<xsl:if test=".//menu[url=//setting[@name='script'] and @hidden='0']">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
   			</xsl:call-template>
		</xsl:if>
		<xsl:if test="url=//setting[@name='script'] and @hidden='0'">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
   			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>