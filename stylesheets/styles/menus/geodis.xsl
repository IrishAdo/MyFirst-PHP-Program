<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.7 $
- Modified $Date: 2004/10/05 11:32:54 $
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
	<xsl:call-template name="display_menu_parent">
		<xsl:with-param name="parent_identifier" select="-1"/>       
		<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
  	</xsl:call-template>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_menu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
<table id='mainmenu' class="width100percent" cellspacing="0" cellpadding="0" summary="the first level menu is displayed as a row of links to the first level of menu on the site.">
	<tr class="firstrow">
		<xsl:for-each select="menu[@parent=$parent_identifier and @hidden=0]">
			<xsl:variable name="class"><xsl:choose>
				<xsl:when test="//setting[@name='script']='index.php'">menu<xsl:value-of select="position() mod 6"/></xsl:when>
				<xsl:when test="//menu[url='index.php']//children/menu[//setting[@name='script']=url]">menu<xsl:value-of select="position() mod 6"/></xsl:when>
				<xsl:when test="url = //setting[@name='script']">child<xsl:value-of select="$generalclass"/></xsl:when>
				<xsl:when test=".//children/menu/url = //setting[@name='script']">child<xsl:value-of select="$generalclass"/></xsl:when>
				<xsl:otherwise>menuoff</xsl:otherwise>
			</xsl:choose></xsl:variable>
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
		<td>
			<xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
			<a><xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
				<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
		        <xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
					<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
				</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
				<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template>
			</a>
		</td>
			</xsl:if></xsl:for-each>
	</tr>
<!--	
	[<xsl:value-of select="//setting[@name='script']!='index.php'"/>
	 and (
	 <xsl:value-of select="boolean(//menu[@parent=$parent_identifier]//children/menu[url=//setting[@name='script'] and @hidden='0'])"/>
	  or ((
	  <xsl:value-of select="boolean(menu[@parent=$parent_identifier and url=//setting[@name='script']])"/> and 
	  <xsl:value-of select="count(menu[@parent=$parent_identifier and url=//setting[@name='script']]/children/menu)"/> !=0)))"/>]
-->
	<xsl:choose>
		<xsl:when test="//setting[@name='script']='index.php'"></xsl:when>
		<xsl:when test="//setting[@name='script']!='index.php' and (boolean(//menu[@parent=$parent_identifier]//children/menu[url=//setting[@name='script'] and @hidden='0']) or ((boolean(menu[@parent=$parent_identifier and url=//setting[@name='script']]) and count(menu[@parent=$parent_identifier and url=//setting[@name='script']]/children/menu) !=0)))">
		<tr class="secondrow">
			<td><xsl:attribute name="colspan"><xsl:value-of select="count(//menu[@parent=-1 and @hidden='0'])"/></xsl:attribute><xsl:attribute name="class">child<xsl:value-of select="$generalclass"/></xsl:attribute>
			
				<table border="0" id="secondlevelmenu" cellspacing="0" cellpadding="0" summary="the Second level menu">
					<xsl:call-template name="display_row_children">
						<xsl:with-param name="level">2</xsl:with-param>
						<xsl:with-param name="parent_identifier"><xsl:value-of select="//menu[@parent=$parent_identifier and (./url=//setting[@name='script'] or .//children/menu/url=//setting[@name='script'])]/@identifier"/></xsl:with-param>
						<xsl:with-param name="current_url"><xsl:value-of select="$current_url"/></xsl:with-param>
					</xsl:call-template>
				</table>
			</td>
		</tr>
		</xsl:when>
		<xsl:otherwise>
			<xsl:if test="//setting[@name!='index.php'] and not(//menu[@parent=-1 and url='index.php']//children/menu[url=//setting[@name='script']])">
				<tr class="secondrow">
					<td><xsl:attribute name="colspan"><xsl:value-of select="count(//menu[@parent=-1 and @hidden='0'])"/></xsl:attribute><xsl:attribute name="class">child<xsl:value-of select="$generalclass"/></xsl:attribute>[[nbsp]]</td>
				</tr>
			</xsl:if>
		</xsl:otherwise>
	</xsl:choose>		
</table>
<!--
</td></tr>
		<tr>
			<td class="secondlevelimg"><img src="/libertas_images/themes/1x1.gif" border="0" height="20" width="1"/></td>
			<td class="secondlevel">
	<xsl:for-each select="menu[@parent=-1 and @hidden='0']">
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
-->
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
				<xsl:when test="@parent=$parent_identifier"><td><xsl:attribute name="class">child<xsl:value-of select="$generalclass"/></xsl:attribute>
					<a><xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
						<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
						<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
							<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
						<xsl:choose>
						<xsl:when test="url=//setting[@name='script']"><strong><xsl:value-of select="label"/></strong></xsl:when>
						<xsl:when test=".//children/menu/url=//setting[@name='script']"><strong><xsl:value-of select="label"/></strong></xsl:when>
						<xsl:otherwise><xsl:value-of select="label"/></xsl:otherwise>
					</xsl:choose></a></td>
				</xsl:when>
				<xsl:when test="//menu[@parent=$parent_identifier and @hidden='0']/url=//setting[@name='script']"><td><xsl:attribute name="class">child<xsl:value-of select="$generalclass"/></xsl:attribute>
					<a>
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
					</xsl:choose></a></td>
				</xsl:when>
			</xsl:choose>
		</xsl:if>
	</xsl:for-each>
	
</xsl:template>


<xsl:template name="display_row_children">
	<xsl:param name="level">2</xsl:param>
	<xsl:param name="parent_identifier">-1</xsl:param>
	<xsl:param name="current_url"/>
	<xsl:choose>
		<xsl:when test="//menu[@identifier=$parent_identifier and @children!='0' and @hidden='0']">
		<tr>
			<xsl:call-template name="display_cell_children">
				<xsl:with-param name="level"><xsl:value-of select="$level"/></xsl:with-param>
				<xsl:with-param name="parent_identifier"><xsl:value-of select="$parent_identifier"/></xsl:with-param>
				<xsl:with-param name="current_url"><xsl:value-of select="$current_url"/></xsl:with-param>
			</xsl:call-template>
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
		<xsl:for-each select="menu[@hidden=0]">
			<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
				<xsl:choose>
					<xsl:when test=".//children/menu[url=//setting[@name='script'] and @depth>1 and @hidden='0' and boolean(children/menu)]">
							<div class="label"><span><a><xsl:attribute name="href"><xsl:value-of select="children/menu[.//url = //setting[@name='script']]/url"/></xsl:attribute><xsl:value-of select="children/menu[.//url = //setting[@name='script']]/label"/></a></span></div>
							<ul class='subnav'>
							<xsl:for-each select="children/menu[.//url = //setting[@name='script']]/children/menu">
								<li><a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:value-of select="label"/></a>
									<xsl:if test="url=//setting[@name='script'] or .//url=//setting[@name='script']">
										<xsl:call-template name="display_submenu_parent">
											<xsl:with-param name="parent_identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
										</xsl:call-template>
									</xsl:if>
								</li>
							</xsl:for-each>
						</ul>
					</xsl:when>
					<xsl:otherwise>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
		</xsl:for-each>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_submenu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:variable name="url"><xsl:value-of select="//setting[@name='script']"/></xsl:variable>
	<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
	<ul>
	<xsl:for-each select="//children/menu[@parent=$parent_identifier]">
		<xsl:variable name="parent"><xsl:value-of select="@identifier"/></xsl:variable>
		<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
			<li><a>
				<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
				<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
					<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
				</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
				<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
				<xsl:if test="url=//setting[@name='script'] or children/menu/url=//setting[@name='script'] or .//children/menu/url=//setting[@name='script']">
					<xsl:call-template name="display_submenu_parent">
						<xsl:with-param name="parent_identifier" select="@identifier"/>       
					</xsl:call-template>
				</xsl:if>
			</li>
		</xsl:if> 
	</xsl:for-each>
	</ul>
	</xsl:if>
</xsl:template>


</xsl:stylesheet>