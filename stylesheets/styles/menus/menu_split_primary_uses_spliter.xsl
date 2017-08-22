<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/08/24 13:21:21 $
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
	<table cellspacing="0" cellpadding="0" summary="Main Menu Navigation">
	<tr>
		<td>Home</td>
		<td></td>
		<td>Company</td>
		<td><img width="5" height="5" src="/libertas_images/themes/1x1.gif"/><img src="/libertas_images/themes/theme001/button_type_1_splitter.gif"/><img width="5" height="5" src="/libertas_images/themes/1x1.gif"/></td>
		<td>Search</td>
		<td><img width="5" height="5" src="/libertas_images/themes/1x1.gif"/><img src="/libertas_images/themes/theme001/button_type_1_splitter.gif"/><img width="5" height="5" src="/libertas_images/themes/1x1.gif"/></td>
		<td>Contact Us</td>
		<td><img width="5" height="5" src="/libertas_images/themes/1x1.gif"/><img src="/libertas_images/themes/theme001/button_type_1_splitter.gif"/><img width="5" height="5" src="/libertas_images/themes/1x1.gif"/></td>
	</tr>
	</table>
-->
<xsl:variable name="current_site_depth"><xsl:value-of select="//menu[url=//setting[@name='script']]/@depth"/></xsl:variable>
<xsl:variable name="menu_splits_at_depth">-1</xsl:variable>

<xsl:template name="display_menu">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
	<table cellspacing="0" cellpadding="0" summary="the first level menu is displayed as a row of links to the first level of menu on the site.">
	<xsl:call-template name="display_menu_parent">
			<xsl:with-param name="parent_identifier" select="-1"/>       
			<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
       	</xsl:call-template></table>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_menu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
<tr>
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
		</xsl:variable><xsl:if test="($found!='') or count(groups/option)=0"><td>
		<xsl:attribute name="class"><xsl:choose>
			<xsl:when test="url=//setting[@name='script']">menuon</xsl:when>
			<xsl:when test=".//children/menu[url=//setting[@name='script']]">menuon</xsl:when>
			<xsl:otherwise>menuoff</xsl:otherwise>
		</xsl:choose></xsl:attribute><a><xsl:attribute name="class"><xsl:choose>
			<xsl:when test="url=//setting[@name='script']">menuon</xsl:when>
			<xsl:when test=".//children/menu[url=//setting[@name='script']]">menuon</xsl:when>
			<xsl:otherwise>menuoff</xsl:otherwise>
		</xsl:choose></xsl:attribute>
		<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
		<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
		<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td>
		<td><img width="5" alt="" height="5" src="/libertas_images/themes/1x1.gif"/><img alt="splitter between menu options"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_type_1_splitter.gif</xsl:attribute></img><img width="5" height="5" alt="" src="/libertas_images/themes/theme001/1x1.gif"/></td></xsl:if></xsl:for-each>
	</tr>
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
<table border="0" class="width90percent" summary="submenu for this menu location">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
		<xsl:for-each select="menu">
			<xsl:choose>
				<xsl:when test="url=//setting[@name='script'] and @hidden='0'">
					<xsl:if test="children/menu[@hidden='0']">
						<xsl:for-each select="children/menu[@hidden='0']">
<tr><td align="right"><xsl:attribute name='style'>padding-right:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute>
								<xsl:if test="url=//setting[@name='script']"><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow.gif</xsl:attribute></img></xsl:if>
									<a><xsl:attribute name="class">menulevel2</xsl:attribute>
										<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
                                        <xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
										<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
											<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
											<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
										</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
									<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
								<br/>
</td></tr>
							<xsl:call-template name="display_submenu_parent"><xsl:with-param name="parent_identifier"><xsl:value-of select="@identifier"/></xsl:with-param></xsl:call-template>
<tr><td><hr width="90%"/></td></tr>
						</xsl:for-each>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test=".//menu[url=//setting[@name='script'] and @hidden='0']">
						<xsl:for-each select="children/menu">
<tr><td align="right"><xsl:attribute name='style'>padding-right:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute>
								<xsl:if test="url=//setting[@name='script']"><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow.gif</xsl:attribute></img></xsl:if>
								<a><xsl:attribute name="class">menulevel2</xsl:attribute>
									<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
									<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
									<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
										<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
										<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
									</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
								<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
							<br/>
</td></tr>
							<xsl:call-template name="display_submenu_parent"><xsl:with-param name="parent_identifier"><xsl:value-of select="@identifier"/></xsl:with-param></xsl:call-template>
<tr><td><hr width="90%"/></td></tr>
						</xsl:for-each>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:for-each>
</table>
</xsl:template>

<xsl:template name="display_submenu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:variable name="url"><xsl:value-of select="//setting[@name='script']"/></xsl:variable>
	<xsl:for-each select="//children/menu[@parent=$parent_identifier]">
		<xsl:variable name="parent"><xsl:value-of select="@identifier"/></xsl:variable>
			<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
<tr><td align="right"><xsl:attribute name='style'>padding-right:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute>
				<xsl:if test="url=//setting[@name='script']"><img border="0" alt="Arrow"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow.gif</xsl:attribute></img></xsl:if>
				<a><xsl:attribute name="class">menulevel2</xsl:attribute>
					<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
					<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
					<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
						<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
				<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td></tr>
			</xsl:if> 
		<xsl:if test="children/menu">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>