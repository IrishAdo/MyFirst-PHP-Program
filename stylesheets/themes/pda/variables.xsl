<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:48:54 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!-- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- load style defaults variables for this theme 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-->  
  
<!-- 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- variables for this theme 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-->  
<xsl:variable name="image_path">/libertas_images/themes/pda</xsl:variable>	 	

<xsl:variable name="table_direction">
	<xsl:if test="contains(//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='browser'],'MSIE')">LEFT</xsl:if>
	<xsl:if test="not(contains(//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='browser'],'MSIE'))">top</xsl:if>
</xsl:variable>	 	

<xsl:variable name="browser_type">
	<xsl:if test="contains(//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='browser'],'MSIE')">IE</xsl:if>
	<xsl:if test="not(contains(//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='browser'],'MSIE'))">NET</xsl:if>
</xsl:variable>

<xsl:variable name="image_more_height">18</xsl:variable>
<xsl:variable name="image_more_width">57</xsl:variable>

<xsl:variable name="has_submenu"><xsl:choose>
		<xsl:when test="//module/menu[@url=//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']]/children/menu">1</xsl:when>
		<xsl:otherwise><xsl:for-each select="//xml_document/modules/module[@name='layout']/menu">
				<xsl:if test=".//children/menu[@url=//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']]">1</xsl:if>
			</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose></xsl:variable>
<xsl:variable name="form_button_type">HTML</xsl:variable>
<xsl:variable name="title_bullet">0</xsl:variable>
<xsl:variable name="form_submit_align">right</xsl:variable>
<xsl:variable name="file_download_style"></xsl:variable>
<xsl:variable name="alternative_is_image">Set</xsl:variable>


<xsl:variable name="show_printer_friendly">Yes</xsl:variable>
<xsl:variable name="show_email_friend"><xsl:choose>
	<xsl:when test="//xml_document/modules/module[@name='client']/licence/product/@type='SITE'">No</xsl:when>
	<xsl:otherwise>Yes</xsl:otherwise>
</xsl:choose></xsl:variable>
<xsl:variable name="show_add_bookmark">No</xsl:variable>

<xsl:variable name="more_text">0</xsl:variable>

<xsl:variable name="query_starter"><xsl:choose>
			<xsl:when test="string-length(//xml_document/qstring)!=0">?<xsl:value-of select="//xml_document/qstring"/>&amp;</xsl:when>
			<xsl:otherwise>?</xsl:otherwise>
		</xsl:choose></xsl:variable>


<xsl:variable name="location_of_functions">Top,Bottom</xsl:variable><xsl:variable name="sitemap_type">default</xsl:variable>
<xsl:variable name="show_title_page_title">1</xsl:variable>
<xsl:variable name="show_title_page_title_btn">1</xsl:variable>
<xsl:variable name="title_page"><xsl:choose>
	<xsl:when  test="//menu[url=//modules/module/setting[@name='script']]/@title_page = 1 " >1</xsl:when>
	<xsl:otherwise>0</xsl:otherwise>
</xsl:choose></xsl:variable>
<xsl:variable name="display_more_as_text">1</xsl:variable>
<xsl:variable name="showbulletimagebeforelabel">0</xsl:variable>
<xsl:variable name="type_of_form">links</xsl:variable>
</xsl:stylesheet>

