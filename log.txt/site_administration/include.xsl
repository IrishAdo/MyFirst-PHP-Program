<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:45:43 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:variable name="browser_type">
	<xsl:choose>
	<xsl:when test="contains(//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='browser'],'MSIE')">IE</xsl:when>
	<xsl:otherwise>NET</xsl:otherwise>
	</xsl:choose>
</xsl:variable>

<xsl:include href="../../themes/site_administration/bc_default.xsl"/>
<xsl:include href="../../themes/site_administration/list_details.xsl"/>
<xsl:include href="../../themes/site_administration/list_menu.xsl"/>

<xsl:include href="../../themes/site_administration/common.xsl"/>
<xsl:include href="../../themes/site_administration/common_extended.xsl"/>
<xsl:include href="../../themes/site_administration/print.xsl"/>
<xsl:include href="../../themes/site_administration/menu.xsl"/>
<xsl:include href="../../themes/site_administration/debug.xsl"/>
<xsl:include href="../../themes/site_administration/functions.xsl"/>

<xsl:include href="../../themes/site_administration/page_structure.xsl"/>
<xsl:include href="../../themes/site_administration/my_workspace.xsl"/>
<xsl:include href="../../themes/site_administration/stats.xsl"/>
<xsl:include href="../../themes/site_administration/display_tables.xsl"/>
<xsl:include href="../../themes/site_administration/form_builder.xsl"/>

<xsl:include href="../../themes/site_administration/editor.xsl"/>
<xsl:include href="../../themes/site_administration/sections.xsl"/>
<xsl:include href="../../themes/site_administration/lookups.xsl"/>
<xsl:include href="../../themes/site_administration/sitelayoutmanager.xsl"/>
<xsl:include href="../../themes/site_administration/palette_colours.xsl"/>

<xsl:include href="rss.xsl"/>

<xsl:include href="../../styles/general/powered_by.xsl"/>
<xsl:include href="../../localisation.xsl"/>


</xsl:stylesheet>