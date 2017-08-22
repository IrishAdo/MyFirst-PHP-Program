<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2004/10/03 12:27:21 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:variable name="image_path">/libertas_images/themes/site_administration</xsl:variable>	 	

<xsl:template name="display_layout_structure">

<html ><xsl:attribute name="lang"><xsl:value-of select="//locale/@codex"/></xsl:attribute>
<head>
<script type="text/javascript" src="/libertas_images/javascripts/sortabletable.js">
<xsl:comment> load sortable table</xsl:comment>
</script>
<link type="text/css" rel="StyleSheet" href="/libertas_images/themes/sortabletable.css"/>
<xsl:call-template name="display_header_data"/>
<xsl:if test="//modules/module[@name='vehicles']">
	<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_vehicle.js"></script>
</xsl:if>
<xsl:if test="//session/@logged_in='1'">
	<script>
	function check_confirm(type,command){
		ok = false;
		if (type=="REMOVE"){
			ok = confirm("Are you sure you wish to Remove this entry?");
		}
		if (type=="PUBLISH"){
			ok = confirm("Are you sure you wish to Publish this entry?");
		}
		if (type=="UNPUBLISH"){
			ok = confirm("Are you sure you wish to Unpublish this entry?");
		}
		if (type=="NEXT_STAGE"){
			ok = confirm("Are you sure you wish to send this entry for approval?");
		}
		if (type=="REWORK"){
			ok = confirm("Are you sure you wish to send this entry to be reworked?");
		}
		if (type=="VALIDATE"){
			ok = confirm("Are you sure you wish to approve this entry?");
		}
		// Modification By Ali Imran Ahmad
		if (type=="SUBSCRIBE"){
			ok = confirm("Are you sure you wish to subscribe this entry?");
		}
		if (type=="UNSUBSCRIBE"){
			ok = confirm("Are you sure you wish to unsubscribe this entry?");
		}
		//End Modification of Ali Imran Ahmad
		// if ok then call command
		if (ok){
			window.location = "http:\/\/<xsl:value-of select="//modules/module/setting[@name='domain']" disable-output-escaping="yes"/><xsl:value-of select="//modules/module/setting[@name='base']" disable-output-escaping="yes"/>"+command+"&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>";
		}
	}
	function irl_all_locations_group(t,s){
		el1 = document.getElementById('hidden_irl_menu_locations_label');
		el2 = document.getElementById('hidden_irl_menu_locations');
		if(t.value==1){
			el1.style.display='none';
			el2.style.display='none';
		}else{
			el1.style.display='';
			el2.style.display='';
		}
	}
	

	
	function mirror_locations_group(t,s){
		el1 = document.getElementById('hidden_mirror_locations_label');
		el2 = document.getElementById('hidden_mirror_locations');
		if(t.value==1){
			el1.style.display='none';
			el2.style.display='none';
		}else{
			el1.style.display='';
			el2.style.display='';
		}
	}
	function show_hidden_group(t,s){
		el1 = document.getElementById('hidden_menu_display_label');
		el2 = document.getElementById('hidden_menu_display');
		if(t.value==1){
			el1.style.display='none';
			el2.style.display='none';
		}else{
			el1.style.display='';
			el2.style.display='';
		}
	}

	/* Added By Muhammad Imran Mirza Starts */

	function show_hidden_label_group(t,s){
		el1 = document.getElementById('hidden_region_type_label');
		el2 = document.getElementById('hidden_region_type');
		if(t.value==1){
			el1.style.display='none';
			el2.style.display='none';
		}else{
			el1.style.display='';
			el2.style.display='';
		}
	}
	
	/* Added By Muhammad Imran Mirza Ends */
	
	var browser 				= "<xsl:value-of select="$browser_type"/>";
	var session_url 			= "<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>";
	var domain					= "<xsl:value-of select="//xml_document/modules/module[@name='system_prefs']/setting[@name='domain']"/>";
	var base_href 				= "<xsl:value-of select="//xml_document/modules/module[@name='system_prefs']/setting[@name='base']"/>";
	
	</script>
</xsl:if>
</head>

<body>
	<table border="0" width="100%" cellspacing="0" cellpadding="0" summary="This table contains the company logo, search box and login,join now and logout links" class="headerbar">
	<tr>
		<td valign="middle">
		<xsl:choose>
		<xsl:when test="//module/licence/product/@type='SITE'"><img width="250" height="80" alt="Libertas Site Wizard"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/admin_header.gif</xsl:attribute></img></xsl:when>
		<xsl:when test="//module/licence/product/@type='MECM'"><img width="250" height="80" alt="Libertas Web Content Manager"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/admin_header.gif</xsl:attribute></img></xsl:when>
		<xsl:when test="//module/licence/product/@type='ECMS'"><img width="250" height="80" alt="Libertas Enterprise Content Manager"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/admin_header.gif</xsl:attribute></img></xsl:when>
		<xsl:otherwise><img width="250" height="80" alt="Libertas Solutions"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/admin_header.gif</xsl:attribute></img></xsl:otherwise>
		</xsl:choose>
		</td>
		<td align="right">
			<xsl:apply-templates select="//filter/form"/>
		</td>
	</tr></table>

	<table width="100%" border="0" cellspacing="0" cellpadding="0" summary="This table contains the first level menu for the site.">
	<tr>
	<td class="MenuNavigationCell">
	<xsl:if test="//modules/module[@name='admin_menu']">
	<xsl:for-each select="//modules/module">
		<xsl:if test="@name='admin_menu'">
			<xsl:call-template name="display_admin_menu"/>
		</xsl:if>
	</xsl:for-each>
	</xsl:if>
	</td>
	<xsl:choose>
	<xsl:when test="//modules/module[@name='admin_menu'] and (//page_options/button or //page_options/buttons or //module/form/input[@type='submit'])">
		<td class="MenuNavigationCell" align="right" width="20%"><xsl:call-template name="display_page_menu"/></td>
	</xsl:when>
	<xsl:otherwise>
		<td class="MenuNavigationCell" align="right" width="20%"><img alt="spacer gif" src="/libertas_images/themes/1x1.gif" height="20" width="1"/></td>
	</xsl:otherwise>
	</xsl:choose>
	</tr></table>
<table width="100%" border="0" cellspacing="0" cellpadding="0" height="100%" class="contentTable">
  <tr><!-- secondard nav-->
	<xsl:if test="((//menulinks) and (//module[@name='layout']/@display='menu')) or (//links)">
	<xsl:if test ="//links">
    <td valign="top" style="border-right:1px solid #999999;">
	<xsl:for-each select="//links">
	<strong><xsl:value-of select="@label"/></strong>
		<UL><xsl:for-each select="link"><li><a><xsl:attribute name="href">admin/index.php?command=<xsl:value-of select="@command"/></xsl:attribute><xsl:value-of select="."/></a></li></xsl:for-each></UL>
	</xsl:for-each>
	</td>
	</xsl:if>
	<xsl:if test="//menulinks">
	<xsl:if test ="//module[@name='layout']/@display='menu'">
    <td valign="top">
	 <table cellspacing="0" cellpadding="0" summary="This is a series of quick links that will filter the documents per location." width="100%" height="100%" border="0">
	   <tr><td valign="top" style="height:100%">
				<script>
				<xsl:comment>
				var filterparameter = "<xsl:value-of select="//menulinks" disable-output-escaping="yes" />";
				specialLinks  = Array(Array(
					Array("-1","All Pages (With Filter)","<xsl:value-of select="//menulinks" disable-output-escaping="yes" />-1","1",Array(),"-1"),
					Array("-3","All Pages (Without Filter)","?command=PAGE_LIST","1",Array(),"-1"),
					Array("-2","Orphan pages","<xsl:value-of select="//menulinks" disable-output-escaping="yes" />-3","1",Array(),"-1")
				),
				Array(
				<xsl:call-template name="display_menu_tree"/>
				));
				// </xsl:comment>
				</script>
				<iframe frameborder="no" id="menu_tree" name="menu_tree" style="width:200px;height:100%;" src="/libertas_images/javascripts/menu_nav.html"></iframe>
		</td></tr>
	 </table>
	</td>
	 </xsl:if>
	</xsl:if>
    <td class="menulinkspacer"><img alt="spacer gif" src="/libertas_images/themes/1x1.gif" height="350" width="2"/></td>
	</xsl:if>
	<!-- Main body-->
		<td class="MainContentLocation">
			<xsl:call-template name="generate_content"/>
		</td>
  </tr>
  <tr>
	<xsl:if test="((//menulinks) and (//module[@name='layout']/@display='menu')) or (//links)">
    <td><img alt="spacer gif" src="/libertas_images/themes/1x1.gif" height="1" width="200"/></td>
    <td><img alt="spacer gif" src="/libertas_images/themes/1x1.gif" height="1" width="1"/></td>
	 </xsl:if>
    <td width="100%"><img alt="spacer gif" src="/libertas_images/themes/1x1.gif" height="1"/></td>
  </tr>
  </table>
  
  <xsl:if test="/xml_document/debugging">
		<xsl:apply-templates select="/xml_document/debugging"/>
  </xsl:if>
  
</body>
</html>
</xsl:template>

<xsl:template name="display_menu_tree">
	<xsl:call-template name="display_menu_children"></xsl:call-template>
</xsl:template>

<xsl:template name="display_menu_children">
	<xsl:param name="parent">-1</xsl:param>
	<xsl:for-each select="//menu[@parent=$parent]">
	Array("<xsl:value-of select="@identifier"/>","<xsl:value-of select="label"/>","","<xsl:value-of select="@depth"/>",Array(<xsl:call-template name="display_menu_children"><xsl:with-param name="parent"><xsl:value-of select="@identifier"/></xsl:with-param></xsl:call-template>),"<xsl:value-of select="@parent"/>")<xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>