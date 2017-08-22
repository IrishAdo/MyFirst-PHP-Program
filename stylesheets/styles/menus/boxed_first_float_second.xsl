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
<xsl:variable name="menu_splits_at_depth">1</xsl:variable>


<xsl:template name="display_menu">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">

	<table cellspacing="0" cellpadding="0" summary="the first level menu multiple columns each cell has a link in it"><tr><td class="black"><table border="0" cellspacing="3" cellpadding="6" summary=""><xsl:call-template name="display_menu_parent">
			<xsl:with-param name="parent_identifier" select="-1"/>       
			<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
       	</xsl:call-template></table></td></tr></table>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_menu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
<tr>
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
	
		<xsl:if test="($found!='') or count(groups/option)=0">
		<td><xsl:attribute name="class"><xsl:choose>
				<xsl:when test="url=//setting[@name='script']">green</xsl:when>
				<xsl:when test=".//children/menu[url=//setting[@name='script']]">green</xsl:when>
				<xsl:otherwise>white</xsl:otherwise>
			</xsl:choose></xsl:attribute><a>
				<xsl:attribute name="class">menulevel1</xsl:attribute>
				<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
			<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td>
		</xsl:if>
	</xsl:for-each>
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
	<xsl:variable name="header"><xsl:choose>
		<xsl:when test="//module/menu[url=//setting[@name='script']]"><xsl:value-of select="//module/menu[url=//setting[@name='script']]/label"/></xsl:when>
		<xsl:otherwise><xsl:for-each select="//xml_document/modules/module[@name='layout']/menu">
			<xsl:if test=".//children/menu[url=//setting[@name='script']]"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></xsl:if>
		</xsl:for-each></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="content">
		<table class="width100percent" summary="submenu">
		<xsl:for-each select="//xml_document/modules/module[@name='layout']">
			<xsl:for-each select="menu[@hidden=0]">
				<xsl:choose>
					<xsl:when test="url=//setting[@name='script']">
						<xsl:if test="children/menu[@hidden=0]">
							<xsl:call-template name="display_submenu_parent">
								<xsl:with-param name="parent_identifier" select="@identifier"/>       
								<xsl:with-param name="destination_identifier" select="@identifier"/>
			    	   		</xsl:call-template>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<xsl:if test=".//menu[@hidden=0 and url=//setting[@name='script']]">
							<xsl:call-template name="display_submenu_parent">
								<xsl:with-param name="parent_identifier" select="@identifier"/>       
								<xsl:with-param name="destination_identifier" select=".//menu[url=//setting[@name='script']]/@identifier"/>
					       	</xsl:call-template>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</xsl:for-each>
		</table>
	</xsl:variable>	
	<xsl:if test="string-length($content)!=0">
		<xsl:call-template name="display_a_table">
			<xsl:with-param name="header"><xsl:value-of select="$header" disable-output-escaping="yes"/></xsl:with-param>
			<xsl:with-param name="content"><xsl:copy-of select="$content"/></xsl:with-param>
		</xsl:call-template>
	</xsl:if>	
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
		<xsl:if test="$open_folder > 0">
			<xsl:if test="(//session/groups/group/@identifier = groups/option) or not(groups/option)">
				<tr><td><xsl:attribute name='style'>padding-left:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute><a><xsl:attribute name="class">menulevel2</xsl:attribute>
				<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
				<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></td></tr>
			</xsl:if> 
		</xsl:if> 
		<xsl:if test=".//menu[url=$url and @hidden=0]">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
				<xsl:with-param name="destination_identifier" select="$destination_identifier"/>
   			</xsl:call-template>
		</xsl:if>
		<xsl:if test="url=$url">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
				<xsl:with-param name="destination_identifier" select="$destination_identifier"/>
   			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>