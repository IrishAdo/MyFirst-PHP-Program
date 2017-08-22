<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2004/11/25 18:19:43 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="display_menu">
	<xsl:param name="folder" select="-1"/>
	
	<xsl:if test="boolean(//session/admin_restriction/locations/location)">
	<p>You have access restrictions in place, you will only be able to manage the specific locations that the administrator has defined for you</p>
	</xsl:if>
	<table cellspacing="1" cellpadding="5" border="0" width="100%">
	<xsl:variable name="choosenfolder"><xsl:choose>
		<xsl:when test="($folder != -1) and (count(//menu[@parent=$folder])=0)"><xsl:value-of select="//menu[@identifier=$folder]/@parent"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="$folder"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:if test="($choosenfolder != -1) and (count(//menu[@parent=$choosenfolder])=0)">
		<tr><td class="TableCell" width="45"><a><xsl:attribute name="href">admin/index.php?command=LAYOUT_LIST_MENU&amp;folder=<xsl:value-of select="../../@parent"/></xsl:attribute><img src="/libertas_images/themes/site_administration/folder.gif" border="0" alt="Return to the parent folder"/></a></td><td><a><xsl:attribute name="href">admin/index.php?command=LAYOUT_LIST_MENU&amp;folder=-1</xsl:attribute>Sorry there are not locations return to the root of the site </a></td></tr>
	</xsl:if>
	
	<xsl:for-each select ="//menu[@parent=$choosenfolder]">
		<xsl:variable name="p"><xsl:value-of select="@parent"/></xsl:variable>
		<xsl:if test="position()=1">
		<tr>
			<td colspan="2" class="tableCell"><xsl:call-template name="display_breadcrumb_trail">
				<xsl:with-param name="url"><xsl:value-of select="//menu[@identifier=$p]/url"/></xsl:with-param>
					<xsl:with-param name="linking">0</xsl:with-param>
			</xsl:call-template></td>
		</tr>
		</xsl:if>
		<tr>
		<xsl:attribute name="class"><xsl:choose>
		<xsl:when test="(position() mod 2) = 1">TableCell_alt</xsl:when>
		<xsl:otherwise>TableCell</xsl:otherwise></xsl:choose></xsl:attribute>
		<td width="45">
		<xsl:choose>
			<xsl:when test="children/menu"><a><xsl:attribute name="href">admin/index.php?command=LAYOUT_LIST_MENU&amp;folder=<xsl:value-of select="@identifier"/></xsl:attribute><img src="/libertas_images/themes/site_administration/folder.gif" border="0" alt="Open this folder"/></a></xsl:when>
			<xsl:otherwise><img src="/libertas_images/themes/site_administration/file.gif" border="0"/></xsl:otherwise>
		</xsl:choose></td>
		<td width="100%">
		<xsl:choose>
			<xsl:when test="children/menu"><a><xsl:attribute name="href">admin/index.php?command=LAYOUT_LIST_MENU&amp;folder=<xsl:value-of select="@identifier"/></xsl:attribute><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:copy-of select="label"/></xsl:with-param>
						</xsl:call-template></a></xsl:when>
			<xsl:otherwise><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:copy-of select="label"/></xsl:with-param>
						</xsl:call-template></xsl:otherwise>
		</xsl:choose> <xsl:if test="@hidden=1"> <span class="highlight">(Hidden)</span></xsl:if>
		</td>
		<td align="right">
		<xsl:if test="//session/admin_restriction/locations/location = @parent or not(boolean(//session/admin_restriction/locations/location))">
		
		<table cellspacing="0" cellpadding="0" border="0"><tr>
		<td><a><xsl:attribute name="href">admin/index.php?command=PAGE_ADD&amp;folder=<xsl:value-of select="@identifier"/></xsl:attribute><img src="/libertas_images/themes/site_administration/button_PAGE.gif" border="0"/></a></td>
		<td><xsl:choose>
		<xsl:when test="position()=1"><img width="40" height="40" src="/libertas_images/themes/1x1.gif" border="0"/></xsl:when>
		<xsl:otherwise><a><xsl:attribute name="href">admin/index.php?command=LAYOUT_CHANGE_ORDER&amp;menu_identifier=<xsl:value-of select="@identifier"/>&amp;menu_parent=<xsl:value-of select="@parent"/>&amp;menu_pos=<xsl:value-of select="position() - 1"/></xsl:attribute><img src="/libertas_images/themes/site_administration/button_UP.gif" border="0"/></a></xsl:otherwise>
		</xsl:choose></td>
		<td><xsl:choose>
		<xsl:when test="position()=last()"><img width="40" height="40" src="/libertas_images/themes/1x1.gif" border="0"/></xsl:when>
		<xsl:otherwise><a><xsl:attribute name="href">admin/index.php?command=LAYOUT_CHANGE_ORDER&amp;menu_identifier=<xsl:value-of select="@identifier"/>&amp;menu_parent=<xsl:value-of select="@parent"/>&amp;menu_pos=<xsl:value-of select="position() + 1"/></xsl:attribute><img src="/libertas_images/themes/site_administration/button_DOWN.gif" border="0"/></a></xsl:otherwise>
		</xsl:choose></td>
		</tr>
		</table></xsl:if></td>
		</tr>
		<xsl:if test="//session/admin_restriction/locations/location = @identifier or not(boolean(//session/admin_restriction/locations/location))">
		<tr><xsl:attribute name="class"><xsl:choose>
		<xsl:when test="(position() mod 2) = 1">TableCell_alt</xsl:when>
		<xsl:otherwise>TableCell</xsl:otherwise></xsl:choose></xsl:attribute>
		<td colspan="3"> | 
		<xsl:if test="url!='/admin/index.php'">
		<xsl:choose>
		<xsl:when test="//infolder/@type!=''">
			<a><xsl:attribute name="href">admin/index.php?command=LAYOUT_EDIT_MENU&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute>Edit</a> 
		</xsl:when>
		<xsl:otherwise>
			<a><xsl:attribute name="href">admin/index.php?command=PAGE_EDIT_MENU&amp;menu_identifier=<xsl:value-of select="@identifier"/></xsl:attribute>Edit</a>
		</xsl:otherwise>
		</xsl:choose>
		&#32;|&#32;
		</xsl:if>
		<!--
		<xsl:if test="//infolder/@type!=''">
			<xsl:if test="//product/@type!='SITE'">
				<xsl:if test="url!='admin/index.php'">
					<a><xsl:attribute name="href">admin/index.php?command=LAYOUT_EDIT_MENU&amp;menu_identifier=<xsl:value-of select="@identifier"/>&amp;menu_parent=<xsl:value-of select="@parent"/>&amp;menu_pos=<xsl:value-of select="position() - 1"/>&amp;btn=channel_manager</xsl:attribute>Modify Channels</a> |
				</xsl:if>
				<xsl:if test="url!='index.php' ">
					<a><xsl:attribute name="href">admin/index.php?command=LAYOUT_EDIT_MENU&amp;menu_identifier=<xsl:value-of select="@identifier"/>&amp;menu_parent=<xsl:value-of select="@parent"/>&amp;menu_pos=<xsl:value-of select="position() - 1"/>&amp;btn=access_restrictions</xsl:attribute>Modify Group Access</a> |
				</xsl:if>
			</xsl:if>
			<xsl:if test="url!='admin/index.php'">
				<a><xsl:attribute name="href">admin/index.php?command=LAYOUT_EDIT_MENU&amp;menu_parent=<xsl:value-of select="@parent"/>&amp;menu_identifier=<xsl:value-of select="@identifier"/>&amp;menu_pos=<xsl:value-of select="position() - 1"/>&amp;btn=rank_pages</xsl:attribute>Order Pages</a> |
				<xsl:if test="//product/@type!='SITE'">
					<a><xsl:attribute name="href">admin/index.php?command=LAYOUT_EDIT_MENU&amp;menu_parent=<xsl:value-of select="@parent"/>&amp;menu_identifier=<xsl:value-of select="@identifier"/>&amp;menu_pos=<xsl:value-of select="position() - 1"/>&amp;btn=access_advanced</xsl:attribute>Advanced Options</a> |
					<xsl:if test="url!='index.php'">
						<a><xsl:attribute name="href">admin/index.php?command=LAYOUT_EDIT_MENU&amp;menu_parent=<xsl:value-of select="@parent"/>&amp;menu_identifier=<xsl:value-of select="@identifier"/>&amp;menu_pos=<xsl:value-of select="position() - 1"/>&amp;btn=access_restrictions</xsl:attribute>Admin Restrictions</a> |
					</xsl:if>
				</xsl:if>
			</xsl:if>
		</xsl:if>
		-->
		<xsl:if test="url!='admin/index.php' and url!='index.php' and not(boolean(display_options/display='PRESENTATION_SEARCH')) and not(boolean(children/menu))">
			&lt;a href="javascript:check_confirm('REMOVE','admin/index.php?command=LAYOUT_REMOVE_MENU&amp;identifier=<xsl:value-of select="@identifier"/>');"&gt;Delete&lt;/a&gt; | 
		</xsl:if>
		</td></tr>
		</xsl:if>
	</xsl:for-each>
	</table>
</xsl:template>

</xsl:stylesheet>