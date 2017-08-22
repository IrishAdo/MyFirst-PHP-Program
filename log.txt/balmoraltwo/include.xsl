<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.1 $
- Modified $Date: 2005/02/28 17:16:02 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
Load the required xsl style sheets that will be used to produce the finished page
-->
<xsl:include href="./structure.xsl"/>
<xsl:include href="./variables.xsl"/>
<xsl:include href="../../localisation.xsl"/>
 
<!--
	Display Styles
-->	 	
<xsl:include href="../../styles/login/login_form_div.xsl"/>
<xsl:include href="../../styles/mirror/mirror_bullet.xsl"/>
<xsl:include href="../../styles/pages/display_latest_list.xsl"/>
<xsl:include href="../../styles/files/file_indiv.xsl"/>
<xsl:include href="../../styles/search/search_default_xhtml.xsl"/>
<xsl:include href="../../styles/search/search_page_form_indiv.xsl"/>
<xsl:include href="../../styles/forums/forum_default_indiv.xsl"/>
<xsl:include href="../../styles/breadcrumbs/bc_default.xsl"/>
<xsl:include href="../../styles/menus/menu_lbs_split.xsl"/> 
<!-- <xsl:include href="../../styles/menus/menu_split_show_siblings_and_children.xsl"/> -->

 
<xsl:include href="../../styles/table/table.xsl"/>
<xsl:include href="../../styles/metadata/metadata_default.xsl"/>
<xsl:include href="../../styles/sitemaps/sitemap_xhtml_standard.xsl"/>

<!--
	General Display Styles
-->	 	
<xsl:include href="../../styles/general/show_widgets_libertas.xsl"/>
<xsl:include href="../../styles/general/powered_by.xsl"/>
<xsl:include href="../../styles/general/debug_default.xsl"/>
<xsl:include href="../../styles/general/functions_default.xsl"/>
<xsl:include href="../../styles/general/general_default_xhtml.xsl"/>
<xsl:include href="../../styles/general/common_indiv.xsl"/>
<xsl:include href="../../styles/pages/display_this_page_xhtml.xsl"/>
<xsl:include href="../../styles/general/general_layout_xhtml.xsl"/>

</xsl:stylesheet>
