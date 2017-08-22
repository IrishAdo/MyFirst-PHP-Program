<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
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
	<xsl:param name="class_to_use" select="'menulevel1'"/>
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
       <table width="100%" border="0" cellspacing="0" cellpadding="0" summary="This is the first level of the site menu. It is placed horizontally accross the page layout"><xsl:call-template name="display_menu_parent">
			<xsl:with-param name="class_to_use" select="$class_to_use"/>
			<xsl:with-param name="parent_identifier" select="-1"/>       
			<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
       	</xsl:call-template></table>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_menu_parent">
	<xsl:param name="class_to_use" select="'menulevel1'"/>
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
	
<tr><td ><xsl:attribute name="class"><xsl:value-of select="$class_to_use"/>bg</xsl:attribute>| 
	<xsl:for-each select="menu[@parent=$parent_identifier]">
		<xsl:variable name="found">
		<xsl:if test="url='admin/index.php' and //xml_document/modules/session/groups/@type=2">2</xsl:if>
			<xsl:for-each select="groups/option">
				<xsl:variable name="val"><xsl:value-of select="@value"/></xsl:variable>
				<xsl:for-each select="//xml_document/modules/session/groups/group">
					<xsl:if test="$val=@identifier">1</xsl:if>
				</xsl:for-each>
			</xsl:for-each>
		</xsl:variable>
		<xsl:if test="($found!='') or count(groups/option)=0">
			<a><xsl:attribute name="class"><xsl:value-of select="$class_to_use"/></xsl:attribute>
				<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
                <xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
					<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
				</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
			<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>&#32;|&#32;
		</xsl:if>
	</xsl:for-each>
	</td></tr>
</xsl:template>

<xsl:template name="display_menu_parent_children">
</xsl:template>

<xsl:template name="display_indent">
	<xsl:param name="depth"/>
	<xsl:if test="$depth>2">
		[[nbsp]]
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
			<xsl:when test="url=//setting[@name='script'] and @hidden='0'">
				<xsl:variable name="rowspan">
					<xsl:choose>
						<xsl:when test="g"></xsl:when>
						<xsl:otherwise></xsl:otherwise>
					</xsl:choose>
				</xsl:variable>
				<xsl:if test="children/menu[@hidden='0']">
				<table border="" cellspacing="0" cellpadding="0" width="100%" summary="This table contains all sub menu levels of the root location you choose. The First row contains the Root location label and the second row contains the children of that location if any">
					<tr>
						<td rowspan="3" width="1" class="menutitlesplitterdark"><img src="/images/themes/1x1.gif" border="0" height="30" width="1"/></td>
						<td class="menutitle"><table border="0" cellspacing="0" cellpadding="5" width="100%" summary="This table holds one cell that holds the label of the root location you choose to visit"><tr><td class="menutitle"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></td></tr></table></td>
						<td rowspan="3" width="1" class="menutitlesplitterdark"><img src="/images/themes/1x1.gif" border="0" height="30" width="1"/></td>
					</tr>
					<tr>
						<td><table border="0" cellspacing="0" cellpadding="0" width="100%" summary="This contains the children of the root location that you chose if any exist."><xsl:call-template name="display_submenu_parent">
								<xsl:with-param name="parent_identifier" select="@identifier"/>       
								<xsl:with-param name="destination_identifier" select="@identifier"/>
			    	   		</xsl:call-template></table></td>
					</tr>
					<tr height="1"><td class="leftmenuspliterdark"><img src="/images/themes/1x1.gif" border="0" height="1" width="1"/></td></tr>
				</table></xsl:if>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test=".//menu[url=//setting[@name='script'] and @hidden='0']">
						<table border="0" cellspacing="0" cellpadding="0" width="100%" summary="">
						<tr>
							<td rowspan="3" width="1" class="menutitlesplitterdark"><img src="/images/themes/1x1.gif" border="0" height="30" width="1"/></td>
							<td class="menutitle"><table border="0" cellspacing="0" cellpadding="5" width="100%" summary=""><tr><td class="menutitle"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template> [[rightarrow]]</td></tr></table></td>
							<td rowspan="3" width="1" class="menutitlesplitterdark"><img src="/images/themes/1x1.gif" border="0" height="30" width="1"/></td>
						</tr>
						<tr>
							<td><table border="0" cellspacing="0" cellpadding="0" width="100%" summary="">
							<xsl:call-template name="display_submenu_parent">
								<xsl:with-param name="parent_identifier" select="@identifier"/>       
								<xsl:with-param name="destination_identifier" select=".//menu[url=//setting[@name='script']]/@identifier"/>
					       	</xsl:call-template>
							</table></td>
						</tr>
						<tr height="1"><td class="leftmenuspliterdark"><img src="/images/themes/1x1.gif" border="0" height="1" width="1"/></td></tr>
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
	<xsl:variable name="url"><xsl:value-of select="//setting[@name='script']"/></xsl:variable>
	<xsl:for-each select="//children/menu[@parent=$parent_identifier and @hidden='0']">
		<xsl:variable name="parent"><xsl:value-of select="@identifier"/></xsl:variable>
		<xsl:variable name="open_folder">
			<xsl:if test=".//children/menu[@identifier=$destination_identifier]">1</xsl:if>
			<xsl:if test="following-sibling::menu[url=$url]">1</xsl:if>
			<xsl:if test="preceding-sibling::menu[url=$url]">1</xsl:if>
			<xsl:if test="@parent=$destination_identifier">2</xsl:if>
			<xsl:if test="@identifier=$destination_identifier">3</xsl:if>
		</xsl:variable>
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
		<xsl:if test="$open_folder > 0">
	<xsl:if test="($found != '') or not(groups/option)">
		<tr height="1">
		<td ><xsl:attribute name="background"><xsl:value-of select="$image_path"/>/menu_option_spacer.gif</xsl:attribute><img border="0" height="1" width="1"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/menu_option_spacer.gif</xsl:attribute></img></td></tr>
		<tr>
			<td><xsl:if test="@url!=$url"><xsl:attribute name="class">leftmenu</xsl:attribute></xsl:if>
			<xsl:if test="url=$url">
				<xsl:attribute name="height">30px</xsl:attribute>
				<xsl:attribute name="background"><xsl:value-of select="$image_path"/>/menu_option_current.gif</xsl:attribute>
			</xsl:if>
			<table width="100%" border="0" cellpadding="3" cellspacing="0" height="20" summary="">
			<tr>
				<td><xsl:attribute name='style'>padding-left:<xsl:value-of select="((@depth - 2)* 8)+3 "/>px</xsl:attribute>
			<a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
				<xsl:if test="@accesskey!=''"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
				<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
					<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
				</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
			<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
			</td>
			<xsl:if test=".//children/menu"><td width="10">
			<xsl:choose>
				<xsl:when test=".//menu[url=$url]"></xsl:when>
				<xsl:when test="url=$url"><img border="0">
					<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow_d.gif</xsl:attribute>
				</img></xsl:when>
				<xsl:otherwise><img border="0">
					<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow_r.gif</xsl:attribute>
				</img></xsl:otherwise>
			</xsl:choose>&#32;</td></xsl:if>
			</tr></table></td>
		</tr>
		</xsl:if> 
		</xsl:if> 
		<xsl:if test=".//menu[url=$url and @hidden='0']">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
				<xsl:with-param name="destination_identifier" select="$destination_identifier"/>
   			</xsl:call-template>
		</xsl:if>
		<xsl:if test="url=$url and @hidden='0'">
			<xsl:call-template name="display_submenu_parent">
				<xsl:with-param name="parent_identifier" select="@identifier"/>       
				<xsl:with-param name="destination_identifier" select="$destination_identifier"/>
   			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>

</xsl:template>

</xsl:stylesheet>