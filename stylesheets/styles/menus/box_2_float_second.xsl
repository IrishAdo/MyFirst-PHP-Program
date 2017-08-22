<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/08/24 13:21:11 $
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
<xsl:variable name="menu_splits_at_depth">3</xsl:variable>

<xsl:template name="display_menu">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
	<table border="0" width="100%" cellspacing="0" cellpadding="0" summary="the first level menu is displayed as a row of links to the first level of menu on the site.">
	<xsl:call-template name="display_menu_parent">
		<xsl:with-param name="parent_identifier" select="-1"/>       
		<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
  	</xsl:call-template>
	</table>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_menu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
<tr><td width="1"><img border="0" width="1" alt="" height="25"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/menu_spacer.gif</xsl:attribute></img></td>
	<td><xsl:attribute name="background"><xsl:value-of select="$image_path"/>/menu_bg_off.gif</xsl:attribute>
		<xsl:attribute name="class">menuoff</xsl:attribute><span class="date"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='date']"/>[[nbsp]]</span></td>
<td width="1"><img border="0" alt="" width="1" height="25"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/menu_spacer.gif</xsl:attribute></img></td>
<xsl:for-each select="menu[@parent=$parent_identifier and @hidden=0]">
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
		<xsl:if test="($found!='') or count(groups/option)=0"><td><xsl:attribute name="background"><xsl:choose>
			<xsl:when test="url=//setting[@name='script']"><xsl:value-of select="$image_path"/>/menu_bg_on.gif</xsl:when>
			<xsl:when test=".//children/menu[url=//setting[@name='script']]"><xsl:value-of select="$image_path"/>/menu_bg_on.gif</xsl:when>
			<xsl:otherwise><xsl:value-of select="$image_path"/>/menu_bg_off.gif</xsl:otherwise>
		</xsl:choose></xsl:attribute><xsl:attribute name="class"><xsl:choose>
			<xsl:when test="url=//setting[@name='script']">menuon</xsl:when>
			<xsl:when test=".//children/menu[url=//setting[@name='script']]">menuon</xsl:when>
			<xsl:otherwise>menuoff</xsl:otherwise>
		</xsl:choose></xsl:attribute><a><xsl:attribute name="class"><xsl:choose>
			<xsl:when test="url=//setting[@name='script']">menuon</xsl:when>
			<xsl:when test=".//children/menu[url=//setting[@name='script']]">menuon</xsl:when>
			<xsl:otherwise>menuoff</xsl:otherwise>
		</xsl:choose></xsl:attribute>
		<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
        <xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
		<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
		<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td>
		<td width="1"><img border="0" width="1" alt=""  height="25"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/menu_spacer.gif</xsl:attribute></img></td></xsl:if></xsl:for-each>
		<td align="center"><xsl:attribute name="background"><xsl:value-of select="$image_path"/>/menu_bg_off.gif</xsl:attribute>[[nbsp]][[nbsp]]</td>
		<td width="0.000001%"><img border="0" width="1" alt=""  height="25"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/menu_spacer.gif</xsl:attribute></img></td>
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
	<table border="0">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
		<xsl:for-each select="menu">
			<xsl:choose>
				<xsl:when test="url=//setting[@name='script']">
					<xsl:if test="children/menu[@hidden=0]">
						<xsl:for-each select="children/menu[@hidden=0]">
								<tr><td><img border="0" alt="" ><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img></td></tr>
								<tr><td><xsl:attribute name='style'>padding-left:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute>
									<xsl:if test="url=//setting[@name='script']"><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow.gif</xsl:attribute></img></xsl:if>
									<a><xsl:attribute name="class">menulevel2</xsl:attribute>
										<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
										<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
										<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
											<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
											<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
										</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
									<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
								</td></tr>
								<tr><td><img alt="" border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img></td></tr>
								<xsl:call-template name="display_submenu_parent"><xsl:with-param name="parent_identifier"><xsl:value-of select="@identifier"/></xsl:with-param></xsl:call-template>
						</xsl:for-each>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test=".//menu[@hidden=0 and url=//setting[@name='script']]">
						<xsl:for-each select="children/menu[@hidden=0]">
							<tr><td><img border="0" alt="" ><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img></td></tr>
							<tr><td>
								<xsl:if test="url=//setting[@name='script']"><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow.gif</xsl:attribute></img></xsl:if>
								<a><xsl:attribute name="class">menulevel2</xsl:attribute>
									<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
	                                <xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
									<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
										<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
										<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
									</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
								<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
							</td></tr>
							<tr><td><img border="0" alt="" ><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img></td></tr>
							<xsl:call-template name="display_submenu_parent"><xsl:with-param name="parent_identifier"><xsl:value-of select="@identifier"/></xsl:with-param></xsl:call-template>
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
	<xsl:for-each select="//children/menu[@parent=$parent_identifier and @hidden=0]">
		<xsl:variable name="parent"><xsl:value-of select="@identifier"/></xsl:variable>
			<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
				
				<tr><td>
				<xsl:attribute name='style'>padding-left:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute>
				<xsl:if test="url=//setting[@name='script']"><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow.gif</xsl:attribute></img></xsl:if>
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