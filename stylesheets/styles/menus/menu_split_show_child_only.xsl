<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:22 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:variable name="current_site_depth"><xsl:value-of select="//menu[url=//setting[@name='script']]/@depth"/></xsl:variable>
<xsl:variable name="menu_splits_at_depth">-1</xsl:variable>
<!--
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- This style sheet is to be used to produce a split menu approach
	-
	- first level menu entries will be displayed in one position in a horizontal format
	- sub level menu options will be displayed in a vertical menu in another location.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-->
<xsl:template name="display_menu">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
       <table summary="" width="100%"><xsl:call-template name="display_menu_parent">
			<xsl:with-param name="parent_identifier" select="-1"/>       
			<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
       	</xsl:call-template></table>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_menu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
<tr><td>
	<xsl:for-each select="menu[@parent=$parent_identifier and @hidden='0']">
		<a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
			<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
			<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
		<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>[[nbsp]]|[[nbsp]]
	</xsl:for-each>
	</td></tr>
</xsl:template>

<xsl:template name="display_menu_parent_children">
</xsl:template>

<xsl:template name="display_indent">
	<xsl:param name="depth"/>
	<xsl:if test="$depth>1">
		[[nbsp]]
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
				<xsl:when test="url=//setting[@name='script'] and @hidden='0'">
				<table border="0" width="100%" cellspacing="0" cellpadding="0" summary="">
					<tr><td class="menutitle" colspan="2"><table><tr>
						<td><img src="/libertas_images/themes/1x1.gif" border="0" height="25" width="10"/></td>
						<td class="menutitle"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></td>
					</tr></table></td></tr>
						<xsl:call-template name="display_submenu_parent">
							<xsl:with-param name="parent_identifier" select="@identifier"/>       
							<xsl:with-param name="destination_identifier" select="@identifier"/>
		    		   	</xsl:call-template>
				</table>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test=".//menu[url=//setting[@name='script'] and @hidden='0']">
						<table border="0" width="100%" cellspacing="0" cellpadding="0" summary="">
							<tr>
								<td colspan="3" class="menutitlesplitterlight"><img src="/libertas_images/themes/1x1.gif" border="0" height="1" width="1"/></td>
							</tr>
							<tr>
								<td class="menutitlesplitterlight"><img src="/libertas_images/themes/1x1.gif" border="0" height="30" width="1"/></td>
								<td class="menutitle"><table width="100%" summary=""><tr><td class="menutitle"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></td></tr></table></td>
								<td class="menutitlesplitterdark"><img src="/libertas_images/themes/1x1.gif" border="0" height="30" width="1"/></td>
							</tr>
							<tr height="1"><td colspan="2" class="menutitlesplitterdark"><img src="/libertas_images/themes/1x1.gif" border="0" height="1" width="1"/></td></tr>
								<xsl:call-template name="display_submenu_parent">
									<xsl:with-param name="parent_identifier" select="@identifier"/>
									<xsl:with-param name="destination_identifier" select=".//menu[url=//setting[@name='script']]/@identifier"/>
								</xsl:call-template>
						</table>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_submenu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="destination_identifier"/>
	
	<xsl:for-each select="//children/menu[@parent=$parent_identifier and @hidden='0']">
		<xsl:variable name="parent"><xsl:value-of select="@identifier"/></xsl:variable>
		<xsl:variable name="open_folder">
			<xsl:if test=".//menu[@parent=$parent]">1</xsl:if>
			<xsl:if test="@parent=$destination_identifier">1</xsl:if>
			<xsl:if test="@identifier=$destination_identifier">1</xsl:if>
			<xsl:if test="@parent=$parent_identifier">1</xsl:if>
		</xsl:variable>
		<xsl:if test="$open_folder > 0">
			<tr height="1"><td colspan="3" class="leftmenuspliterlight"><img src="/libertas_images/themes/1x1.gif" border="0" height="1" width="1"/></td></tr>
			<tr>
			<td class="leftmenuspliterlight"><img src="/libertas_images/themes/1x1.gif" border="0" height="18" width="1"/></td>
			<td><xsl:attribute name="class">leftmenu<xsl:if test="url=//setting[@name='script']">on</xsl:if></xsl:attribute><table width="100%" summary=""><tr><td>
			<xsl:attribute name='style'>padding-left:<xsl:value-of select="((@depth - 2)* 8)+3 "/>px</xsl:attribute>
			<xsl:if test="url=//setting[@name='script']">[[rightarrow]][[nbsp]]</xsl:if>
			<a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
				<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
				<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
					<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
				</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
			<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
			<xsl:if test="url=//setting[@name='script']">[[nbsp]]&#171;</xsl:if>
			</td></tr></table></td>
			<td class="leftmenuspliterdark"><img src="/libertas_images/themes/1x1.gif" border="0" height="18" width="1"/></td>
			</tr>
			<tr height="1"><td colspan="3" class="leftmenuspliterdark"><img src="/libertas_images/themes/1x1.gif" border="0" height="1" width="1"/></td></tr>
		</xsl:if> 
		<xsl:if test=".//menu[url=//setting[@name='script'] and @hidden='0']">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
				<xsl:with-param name="destination_identifier" select="$destination_identifier"/>
   			</xsl:call-template>
		</xsl:if>
		<xsl:if test="url=//setting[@name='script'] and @hidden='0'">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
				<xsl:with-param name="destination_identifier" select="$destination_identifier"/>
   			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>