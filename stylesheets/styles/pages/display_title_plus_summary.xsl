<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.7 $
- Modified $Date: 2004/10/05 11:03:51 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="display_list">
	<xsl:param name='display_more_as_text'><xsl:value-of select="$display_more_as_text"/></xsl:param>
	<xsl:param name="more">0</xsl:param>

	<xsl:variable name="title_page"><xsl:choose>
			<xsl:when test="//menu[url=//modules/module/setting[@name='script']]/@title_page=1">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:comment>display title and summary only.</xsl:comment>
	<a name="top_of_content"><xsl:comment>top of content</xsl:comment></a>
	<xsl:choose>
		<xsl:when test="$title_page=1">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
				<xsl:with-param name="alt_title">1</xsl:with-param>
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="date_publish">0</xsl:with-param>
				<xsl:with-param name="more">0</xsl:with-param>
				<xsl:with-param name="style">LOCATION</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="//module[@display='ENTRY']/page[position()=1]/@identifier"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:variable name="page_title_string"><xsl:choose>
				<xsl:when test="count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page) != 1"><xsl:value-of select="//menu[url=//	setting[@name='script']]/label"/></xsl:when>
				<!--
								<xsl:when test="contains(//setting[@name='fake_title'],'content for')">	
				<xsl:value-of select="//menu[url=//	setting[@name='fake_title']]/label" disable-output-escaping="yes" />
				</xsl:when>

				<xsl:when test="contains(//setting[@name='fake_title'],'content for')">	<xsl:value-of select="//menu[url=//	setting[@name='fake_title']]/label"/>				
				</xsl:when>
				-->


				<xsl:when test="//setting[@name='fake_title']!=''"><xsl:value-of select="//setting[@name='fake_title']" disable-output-escaping="yes"/></xsl:when>
				<xsl:when test="//setting[@name='real_script']='index.php'"><xsl:value-of select="//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:when>
				<xsl:when test="not(contains(//setting[@name='real_script'],'index.php')) and boolean(//module[@name='presentation' and @display='ATOZ']/letters) and count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page)!=1"><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></xsl:when>
				<xsl:when test="not(contains(//setting[@name='real_script'],'index.php')) and boolean(//module[@name='presentation' and @display='ATOZ']/letters) "><xsl:call-template name="display_firstpage"/></xsl:when>
				<xsl:when test="not(contains(//setting[@name='script'],//setting[@name='fake_script']))"><xsl:value-of select="//modules/container/webobject/module[@name='information_presentation']/content/entry/seperator_row/seperator/field[@name='ie_title']/value" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:call-template name="display_firstpage"/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			
			<div class="page" id="page1">
			<h1 class='entrytitle' id="notitlepage">
				<span>
				<xsl:choose>
				<xsl:when test="contains(//setting[@name='fake_title'],'content for')">	
					<xsl:value-of select="//setting[@name='fake_title']"/>
					
				</xsl:when>
				<xsl:otherwise>
				<xsl:value-of select="$page_title_string"/>
				</xsl:otherwise>
			
				</xsl:choose>
				<!--<xsl:value-of select="contains(//setting[@name='fake_url'],'-')"/>-->
				
				</span>
				</h1>
				</div>
		
			</xsl:otherwise>
	</xsl:choose>
	<xsl:for-each select="//xml_document/modules/container/webobject/module[@display='ENTRY']/page[position() != $title_page]">
		<xsl:call-template name="display_this_page">
			<xsl:with-param name="title">1</xsl:with-param>
			<xsl:with-param name="alt_title">1</xsl:with-param>
			<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
			<xsl:with-param name="summary">1</xsl:with-param>
			<xsl:with-param name="more"><xsl:value-of select="$more"/></xsl:with-param>
			<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			<xsl:with-param name="title_is_link">1</xsl:with-param>
		</xsl:call-template>
	</xsl:for-each>
	<xsl:if test="//xml_document/modules/container/webobject/module/headline">
		<xsl:call-template name='show_headlines'/>
	</xsl:if>
</xsl:template>



</xsl:stylesheet>

