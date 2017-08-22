<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2005/02/09 12:10:16 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:variable name="current_site_depth"><xsl:value-of select="//menu[url=//setting[@name='script']]/@depth"/></xsl:variable>
<xsl:variable name="menu_splits_at_depth">-1</xsl:variable>

<xsl:template name="display_menu">
	<form method="get">
		<div class='formfields'>
		<input type='hidden' name='command' value='LAYOUTSITE_JUMP_TO'></input>
		<select name='url'>
			<xsl:for-each select="//menu[@parent=-1 and @hidden='0']">
				<option>
					<xsl:attribute name='value'><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="url"/></xsl:attribute>
					<xsl:if test="url=//setting[@name='script']">
						<xsl:attribute name='selected'>selected</xsl:attribute>
					</xsl:if>
					<xsl:value-of select="label"/>
				</option>
				<xsl:if test="@children!=0 and ./children/menu[@hidden=0]">
					<xsl:call-template name="show_children">
						<xsl:with-param name="depthstr">[[nbsp]]-[[nbsp]]</xsl:with-param>
						<xsl:with-param name="parent_id"><xsl:value-of select="@identifier"/></xsl:with-param>
					</xsl:call-template>
				</xsl:if>
			</xsl:for-each>
		</select>
		<input type='submit' value='Go'></input>
		</div>
	</form>
</xsl:template>

<xsl:template name="show_children">
	<xsl:param name="depthstr">[[nbsp]]-[[nbsp]]</xsl:param>
	<xsl:param name="parent_id">-1</xsl:param>
	
	<xsl:for-each select="./children/menu[@hidden='0']">
		<option>
			<xsl:attribute name='value'><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="url"/></xsl:attribute>
			<xsl:if test="url=//setting[@name='script']">
				<xsl:attribute name='selected'>selected</xsl:attribute>
			</xsl:if>
			<xsl:value-of select="$depthstr"/><xsl:value-of select="label"/>
		</option>
		<xsl:if test="@children!=0 and ./children/menu[@hidden=0]">
			<xsl:call-template name="show_children">
				<xsl:with-param name="depthstr">[[nbsp]][[nbsp]][[nbsp]]<xsl:value-of select="$depthstr"/></xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_submenu">
</xsl:template>
</xsl:stylesheet>