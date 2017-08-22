<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2004/10/08 16:36:47 $
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
	<table border="0" width="100%" cellspacing="0" cellpadding="0" summary="Each row will hol ad table that will contain ta deeper link than the previous.">
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
<tr><td colspan="2"><table class="width100percent" cellspacing="0" cellpadding="0" summary="the first level menu is displayed as a row of links to the first level of menu on the site."><tr><xsl:for-each select="menu[@parent=$parent_identifier]">
		<xsl:variable name="class"><xsl:choose>
			<xsl:when test="url=//setting[@name='script'] and @hidden='0'">menuon</xsl:when>
			<xsl:when test=".//children/menu[url=//setting[@name='script'] and @hidden='0']">menuon</xsl:when>
			<xsl:otherwise>menuoff</xsl:otherwise>
		</xsl:choose></xsl:variable>
		<xsl:variable name="found">
			<xsl:if test="url='admin/index.php' and //xml_document/modules/session/groups/@type=2 and //setting[@name='real_script'] != 'admin/preview.php'">2</xsl:if>
			<xsl:choose>
				<xsl:when test="//setting[@name='real_script'] = 'admin/preview.php'"></xsl:when>
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
		<xsl:if test="(($found!='') or count(groups/option)=0)">
		<td valign="top"><xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute><img width="9" height="9" border="0" alt=""><xsl:attribute name="src">/libertas_images/themes/turkington/left_<xsl:value-of select="$class"/>.gif</xsl:attribute></img><br/><img width="9" height="30" border="0" alt=""><xsl:attribute name="src">/libertas_images/themes/1x1.gif</xsl:attribute></img></td>
		<td valign="middle">
		<xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
		<a><xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
		<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
        <xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
		<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
		<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td>
		<td valign="top"><xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute><img border="0" width="9" height="9" alt=""><xsl:attribute name="src">/libertas_images/themes/turkington/right_<xsl:value-of select="$class"/>.gif</xsl:attribute></img><br/><img width="9" height="30" border="0" alt=""><xsl:attribute name="src">/libertas_images/themes/1x1.gif</xsl:attribute></img></td>
		<xsl:if test="position() != last()"><td class="spacer"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="25" alt=""/></td></xsl:if>
		</xsl:if></xsl:for-each>
		</tr></table></td></tr>
		<tr>
			<td class="secondlevelimg"><img src="/libertas_images/themes/1x1.gif" border="0" height="20" width="1"/></td>
			<td class="secondlevel">
	<xsl:for-each select="menu[@parent=-1 and @hidden='0']">
<!--
		<xsl:value-of select="url"/>=<xsl:value-of select="//setting[@name='script']"/>=
		<xsl:value-of select="url=//setting[@name='script']"/>=<xsl:value-of select="@identifier"/><br/>
-->
		<xsl:if test=".//children/menu[url=//setting[@name='script'] and @hidden='0'] or url=//setting[@name='script']">
		<table class="width100percent" cellspacing="0" cellpadding="0" summary="the Second level menu">
		<xsl:call-template name="display_row_children">
			<xsl:with-param name="level">2</xsl:with-param>
			<xsl:with-param name="parent_identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			<xsl:with-param name="current_url"><xsl:value-of select="$current_url"/></xsl:with-param>
		</xsl:call-template></table>
		</xsl:if>
	</xsl:for-each></td>
		</tr>
</xsl:template>

<xsl:template name="display_menu_parent_children">
</xsl:template>


<xsl:template name="display_cell_children">
	<xsl:param name="level">2</xsl:param>
	<xsl:param name="parent_identifier">-1</xsl:param>
	<xsl:param name="current_url"/>
<!--
	Col Params::[<xsl:value-of select="$level"/>, <xsl:value-of select="$parent_identifier"/>, <xsl:value-of select="$current_url"/>]
-->
	<xsl:for-each select="//menu[@parent=$parent_identifier and @hidden='0']">
		<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
			<xsl:choose>
				<xsl:when test="@parent=$parent_identifier">
					<a><xsl:attribute name="class">menulevel<xsl:choose>
							<xsl:when test="url!=//setting[@name='script']"><xsl:value-of select="$level"/></xsl:when>
							<xsl:otherwise>selected</xsl:otherwise>
						</xsl:choose></xsl:attribute>
						<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
						<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
						<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
							<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
						<xsl:choose>
						<xsl:when test="url=//setting[@name='script']"><strong><xsl:value-of select="label"/></strong></xsl:when>
						<xsl:when test=".//children/menu/url=//setting[@name='script']"><strong><xsl:value-of select="label"/></strong></xsl:when>
						<xsl:otherwise><xsl:value-of select="label"/></xsl:otherwise>
					</xsl:choose></a> |
				</xsl:when>
				<xsl:when test="//menu[@parent=$parent_identifier and @hidden='0']/url=//setting[@name='script']">
					<a><xsl:attribute name="class">menulevel<xsl:value-of select="$level"/></xsl:attribute>
						<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
						<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
						<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
							<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
					<xsl:choose>
						<xsl:when test="url=//setting[@name='script'] and @hidden='0'"><strong><xsl:value-of select="label"/></strong></xsl:when>
						<xsl:when test=".//children/menu/url=//setting[@name='script'] and @hidden='0'"><strong><xsl:value-of select="label"/></strong></xsl:when>
						<xsl:otherwise><xsl:value-of select="label"/></xsl:otherwise>
					</xsl:choose></a> |
				</xsl:when>
			</xsl:choose>
		</xsl:if>
	</xsl:for-each>
	
</xsl:template>


<xsl:template name="display_row_children">
	<xsl:param name="level">2</xsl:param>
	<xsl:param name="parent_identifier">-1</xsl:param>
	<xsl:param name="current_url"/>
<!--
	Row Params::[<xsl:value-of select="$level"/>, <xsl:value-of select="$parent_identifier"/>, <xsl:value-of select="$current_url"/>]
-->
	<xsl:choose>
		<xsl:when test="@identifier=$parent_identifier and @children!='0' and @hidden='0'">
		<tr><xsl:attribute name="class">menulevel<xsl:value-of select="$level"/></xsl:attribute>
			<td ><xsl:attribute name="class">menulevel<xsl:value-of select="$level"/></xsl:attribute><img src="/libertas_images/themes/1x1.gif" width="1" height="20" border="0" align="left" alt=""/> |
				<xsl:call-template name="display_cell_children">
					<xsl:with-param name="level"><xsl:value-of select="$level"/></xsl:with-param>
					<xsl:with-param name="parent_identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
					<xsl:with-param name="current_url"><xsl:value-of select="$current_url"/></xsl:with-param>
				</xsl:call-template>
			</td>
		 </tr>
		</xsl:when>
		<xsl:otherwise></xsl:otherwise>
	</xsl:choose>
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
		<xsl:for-each select="menu/children/menu[@hidden='0']">
			<xsl:choose>
				<xsl:when test="url=//setting[@name='script'] and @hidden='0'">
					<xsl:if test="children/menu">
						<xsl:for-each select="children/menu[(position() mod 2)=1]">
						<div><a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
								<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
								<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
									<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
									<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
								</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
							<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></div>
						<xsl:call-template name="display_submenu_parent"><xsl:with-param name="parent_identifier"><xsl:value-of select="@identifier"/></xsl:with-param></xsl:call-template>
						</xsl:for-each>
					</xsl:if>
				</xsl:when>
				<xsl:otherwise>
	
					<xsl:if test=".//menu[url=//setting[@name='script'] and @hidden='0']">
						<xsl:for-each select="children/menu">
							<div><xsl:if test="url=//setting[@name='script']"><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow.gif</xsl:attribute></img></xsl:if>
								<a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
									<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
									<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
										<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
										<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
									</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
								<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></div>
							<xsl:call-template name="display_submenu_parent"><xsl:with-param name="parent_identifier"><xsl:value-of select="@identifier"/></xsl:with-param></xsl:call-template>
						</xsl:for-each>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:for-each>
	
</xsl:template>

<xsl:template name="display_submenu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:variable name="url"><xsl:value-of select="//setting[@name='script']"/></xsl:variable>
	<xsl:for-each select="//children/menu[@parent=$parent_identifier and @hidden='0']">
		<xsl:variable name="parent"><xsl:value-of select="@identifier"/></xsl:variable>
			<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
				<tr><td>
				<xsl:attribute name='style'>padding-left:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute>
				<xsl:if test="url=//setting[@name='script']"><img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow.gif</xsl:attribute></img></xsl:if>
				<a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
					<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
					<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
						<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
					<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td></tr>
			</xsl:if> 
		<xsl:if test="children/menu[@hidden='0']">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>