<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:45:48 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
Load the required xsl style sheets that will be used to produce the finished page
-->
<xsl:include href="structure.xsl"/>
<xsl:include href="variables.xsl"/>
<xsl:include href="../../localisation.xsl"/>
 
<!--
	Display Styles
-->	 	
<xsl:include href="../../styles/login/login_form.xsl"/>
<xsl:include href="../../styles/mirror/mirror.xsl"/>
<xsl:include href="../../styles/pages/display_latest_list.xsl"/>
<xsl:include href="../../styles/files/file_indiv.xsl"/>
<xsl:include href="../../styles/search/search_default.xsl"/>
<xsl:include href="../../styles/search/search_page_form.xsl"/>
<xsl:include href="../../styles/forums/forum_default.xsl"/>
<xsl:include href="../../styles/breadcrumbs/bc_default.xsl"/>
<xsl:include href="../../styles/menus/menu_pda.xsl"/>

<xsl:include href="../../styles/table/table.xsl"/>
<xsl:include href="../../styles/metadata/metadata_default.xsl"/>
<xsl:include href="../../styles/sitemaps/sitemap_textonly.xsl"/>
<!--
	General Display Styles
-->	 	
<xsl:include href="../../styles/general/show_widgets_div.xsl"/>
<xsl:include href="../../styles/general/powered_by.xsl"/>
<xsl:include href="../../styles/general/debug_default.xsl"/>
<xsl:include href="../../styles/general/functions_default.xsl"/>
<xsl:include href="../../styles/general/general_default_xhtml.xsl"/>
<xsl:include href="../../styles/general/common_textonly.xsl"/>
<xsl:include href="../../styles/pages/display_this_page.xsl"/>

</xsl:stylesheet>