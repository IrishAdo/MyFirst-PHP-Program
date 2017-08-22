<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2004/08/24 13:21:13 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:variable name="current_site_depth"><xsl:value-of select="//menu[url=//setting[@name='script']]/@depth"/></xsl:variable>
<xsl:variable name="menu_splits_at_depth">-1</xsl:variable>
<xsl:template name="display_menu">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
		<table border="0" cellspacing="0" cellpadding="0" summary="This table contains the menu" class="width100percent">
			<tr>
				<td class="darkblue"><img src="/libertas_images/themes/1x1.gif" width="25" alt="spacer" height="1"/></td>
				<td class="lightblue"><img src="/libertas_images/themes/1x1.gif" width="135" alt="spacer" height="1"/></td>
			</tr>
			<xsl:call-template name="display_menu_parent">
				<xsl:with-param name="parent_identifier" select="-1"/>       
				<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
	       	</xsl:call-template>
			<tr>
				<td class="darkblue" ><img src="/libertas_images/themes/1x1.gif" width="25" alt="spacer" height="1"/></td>
				<td class="lightblue" ><img src="/libertas_images/themes/1x1.gif" width="135" alt="spacer" height="1"/></td>
			</tr>
		</table>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_menu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
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
		<xsl:variable name="currently_in">
		<xsl:choose>
		<xsl:when test="url=//setting[@name='script']">1</xsl:when>
		<xsl:when test=".//children/menu[url=//setting[@name='script']]">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose>
		</xsl:variable>
	<xsl:if test="(($found!='') or count(groups/option)=0) and url!='admin/index.php'">
	<tr>
		<td><xsl:attribute name="class">darkblue<xsl:if test="$currently_in=1">on</xsl:if></xsl:attribute>
		<img alt="Start of menu level" width="6" height="20"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow_white.gif</xsl:attribute></img></td>
		<td><xsl:attribute name="class">lightblue<xsl:if test="$currently_in=1">on</xsl:if></xsl:attribute>
		
		<a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
		<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
		<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
		</td>
    	</tr>
		<xsl:if test="$currently_in=1">
			<xsl:if test="url=$current_url and @hidden='0'">
				<xsl:for-each select="./children">
	    			<xsl:call-template name="display_menu_children">
						<xsl:with-param name="parent_identifier" select="../@identifier"/>       
						<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
       				</xsl:call-template>
    	   		</xsl:for-each>
	    	</xsl:if>
			<xsl:if test=".//children/menu[url=$current_url and @hidden='0']">
				<xsl:for-each select="./children">
		    		<xsl:call-template name="display_menu_children">
						<xsl:with-param name="parent_identifier" select="../@identifier"/>       
						<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
    			   	</xsl:call-template>
			 	</xsl:for-each>
	    	</xsl:if>
		</xsl:if>
		</xsl:if>
	</xsl:for-each>
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

<xsl:template name="display_footer">
	<xsl:param name="class_to_use" select="'menulevel1'"/>
	<xsl:for-each select="//xml_document/modules/module[@name='layout']/menu">
		<a class="footer"><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a> |
    </xsl:for-each>
</xsl:template>


<xsl:template name="display_menu_children">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
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
		<xsl:variable name="currently_in">
		<xsl:choose>
		<xsl:when test="url=//setting[@name='script']">1</xsl:when>
		<xsl:when test=".//children/menu[url=//setting[@name='script']]">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose>
		</xsl:variable>
	<xsl:if test="($found!='') or count(groups/option)=0">
		<tr>
		<td class="lightblueon">
			<xsl:choose><xsl:when test="url=$current_url"><img alt="" width="16" height="18"><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_YOU_ARE_HERE'"/></xsl:call-template></xsl:attribute><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/arrow_orange.gif</xsl:attribute></img></xsl:when><xsl:otherwise>[[nbsp]]</xsl:otherwise></xsl:choose>
		</td>
		<td><xsl:attribute name="class">lightblueon</xsl:attribute>
		
		<xsl:attribute name='style'>padding-left:<xsl:value-of select="(@depth - 1)* 8 "/>px</xsl:attribute>
		<a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a>
		</td></tr>
		<xsl:if test="$currently_in=1">
			<xsl:if test="url=$current_url">
				<xsl:for-each select="./children">
	    			<xsl:call-template name="display_menu_children">
						<xsl:with-param name="parent_identifier" select="../@identifier"/>       
						<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
       				</xsl:call-template>
    	   		</xsl:for-each>
	    	</xsl:if>
			<xsl:if test=".//children/menu[url=$current_url]">
				<xsl:for-each select="./children">
		    		<xsl:call-template name="display_menu_children">
						<xsl:with-param name="parent_identifier" select="../@identifier"/>       
						<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
    			   	</xsl:call-template>
			 	</xsl:for-each>
	    	</xsl:if>
		</xsl:if>
    	</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_submenu"></xsl:template>
</xsl:stylesheet>