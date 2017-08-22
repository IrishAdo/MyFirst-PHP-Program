<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.9 $
- Modified $Date: 2005/01/11 16:37:09 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
 
	<xsl:template match="node()|*">
		<xsl:if test="//setting[@name='sp_meta_dublin_core']='Yes'">
		<xsl:if test="name()!='setting' and name()!='' and name()!='myfield' and name()!='footer' and .!='' and .!='0000-00-00 00:00:00' and //xml_document/modules/module[@name='client']/licence/product/@type='ECMS'">
				<meta>
					<xsl:attribute name="name">DC.<xsl:value-of select="name()"/><xsl:if test="@refinement">.<xsl:value-of select="@refinement"/></xsl:if></xsl:attribute>
					<xsl:attribute name="content"><xsl:choose>
						<xsl:when test="local-name()='keywords'">
						<xsl:for-each select="keyword"><xsl:value-of select="."/><xsl:if test="position()!=last()">, </xsl:if></xsl:for-each>
						</xsl:when>
						<xsl:otherwise><xsl:variable name="content"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:variable><xsl:value-of select="substring($content,0,1023)"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
				</meta>
		</xsl:if>
	</xsl:if>
	</xsl:template>
	
	<xsl:template name="display_metadata">
		<script type="text/javascript" src="/libertas_images/javascripts/open_in_external.js"><xsl:comment>open in external window</xsl:comment></script>			
		
		<xsl:choose>
			<xsl:when test="substring(//setting[@name='real_script'],1,2)='-/'">
				<META NAME="ROBOTS" CONTENT="NOARCHIVE"/>
				<META NAME="ROBOTS" CONTENT="NOINDEX"/>
				<META NAME="ROBOTS" CONTENT="NOFOLLOW"/>
			</xsl:when>
			<xsl:otherwise>
		<xsl:if test="not(//menu[url=//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']]/groups/option)">
			
			<xsl:choose>
				<!-- Metadata to be pulled from pages -->
				<xsl:when test="//modules/container/webobject/module/page[position()=1]/metadata">
					<xsl:for-each select="//modules/container/webobject/module/page[position()=1]/metadata/description">
						<meta><xsl:attribute name="name">description</xsl:attribute><xsl:attribute name="content"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:attribute></meta>
					</xsl:for-each>
					<xsl:for-each select="//modules/container/webobject/module/page[position()=1]/metadata">
						<xsl:variable name="pagecount"><xsl:value-of select="count(//modules/container/webobject/module/page)"/></xsl:variable>
						<xsl:choose>
							<xsl:when test="$pagecount=0"></xsl:when>
							<xsl:otherwise>
								<xsl:choose>								
									<xsl:when test="keywords/keyword">
										<xsl:variable name="keys"><xsl:for-each select="keywords/keyword"><xsl:value-of select="."/><xsl:if test="position()!=last()">, </xsl:if></xsl:for-each><xsl:if test="subject[@refinement='keywords']!=''">, <xsl:value-of select="subject[@refinement='keywords']"/></xsl:if></xsl:variable>
										<meta name="keywords"><xsl:attribute name="content"><xsl:value-of select="$keys"/></xsl:attribute></meta>
									</xsl:when>
									<xsl:when test="subject[@refinement='keywords']!=''">
										<xsl:variable name="keys"><xsl:value-of select="subject[@refinement='keywords']"/></xsl:variable>
										<meta name="keywords"><xsl:attribute name="content"><xsl:value-of select="$keys"/></xsl:attribute></meta>
									</xsl:when>
									
									<xsl:otherwise>
										<xsl:variable name="keys">		
											<xsl:for-each select="//page">
												<xsl:value-of select="./metadata/keywords" disable-output-escaping="yes"/><xsl:if test="position()!=last()">, </xsl:if>
											</xsl:for-each>			
										</xsl:variable>
										<meta name="keywords">
											<xsl:attribute name="content">
												<xsl:value-of select="$keys" />
											</xsl:attribute>											
										</meta>														
									</xsl:otherwise>	
								</xsl:choose>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
					<xsl:for-each select="//modules/container/webobject/module/page[position()=1]/metadata/creator">
						<meta name="author"><xsl:attribute name="content"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:attribute></meta>
					</xsl:for-each>
					<meta name="Last-Modified">
						<xsl:attribute name="content">
							<xsl:call-template name="get_newest_date">
								<xsl:with-param name="current_date"><xsl:value-of select="//page[position()=1]/metadata/date[@refinement='publish']/@seconds"/></xsl:with-param>
							</xsl:call-template>
						</xsl:attribute>
					</meta>
					<xsl:for-each select="//modules/container/webobject/module/page[position()=1]/metadata">
						<xsl:apply-templates />
					</xsl:for-each>
				</xsl:when>
				<xsl:when test="count(//modules/container/webobject/module/headline[position()=1]/page) >=1 ">								
					<xsl:variable name="keys">		
						<xsl:for-each select="//page">
							<xsl:value-of select="./metadata/keywords" disable-output-escaping="yes"/><xsl:if test="position()!=last()">, </xsl:if>
						</xsl:for-each>			
					</xsl:variable>
					<meta name="keywords">
						<xsl:attribute name="content">
							<xsl:value-of select="$keys" />
						</xsl:attribute>											
					</meta>														
					<xsl:for-each select="//modules/container/webobject/module/headline[position()=1]/page">	
						<meta><xsl:attribute name="name">description</xsl:attribute><xsl:attribute name="content"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="./metadata/description" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:attribute></meta>					
					</xsl:for-each>									
				</xsl:when>				
				<xsl:otherwise>
					<xsl:if test="count(//content/info/results/entry)=1">
					<xsl:for-each select="//content/info/results/entry">
						<xsl:variable name="eid"><xsl:value-of select="@real_id"/></xsl:variable>
						<xsl:for-each select="../metadata[@linkto=$eid]">
							<META name="description"><xsl:attribute name="content"><xsl:value-of select="description"/></xsl:attribute></META>
							<META name="DC.format"><xsl:attribute name="content"><xsl:value-of select="format"/></xsl:attribute></META>
							<META name="DC.language"><xsl:attribute name="content"><xsl:value-of select="language"/></xsl:attribute></META>
						<!--
<META name="keywords"><xsl:attribute name="content"></xsl:attribute></META>
-->
<xsl:for-each select="date">
	<xsl:if test=".!='0000-00-00 00:00:00'"><META><xsl:attribute name="name">Dc.date.<xsl:value-of select="@refinement"/></xsl:attribute><xsl:attribute name="content"><xsl:value-of select="."/></xsl:attribute></META></xsl:if>
</xsl:for-each>
						</xsl:for-each>
					</xsl:for-each>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:choose>
			<xsl:when test="//setting[@name='fake_title']!='' and contains(//setting[@name='real_script'],'-/-')  or (//setting[@name='isbot']=1 and //setting[@name='domain']='apollo.rsdns.com')"> 
				<META NAME="ROBOTS" CONTENT="NOARCHIVE"/>
				<META NAME="ROBOTS" CONTENT="NOINDEX"/>
				<META NAME="ROBOTS" CONTENT="NOFOLLOW"/>
			</xsl:when>
			<xsl:when test="//modules/module[@name='client']/client/robots!=''">
				<xsl:choose>
					<xsl:when test="//modules/module[@name='client']/client/robots='LOCALE_SP_INDEX_FOLLOW'"><meta name="robots" ><xsl:attribute name="content">index,follow</xsl:attribute></meta></xsl:when>
					<xsl:when test="//modules/module[@name='client']/client/robots='LOCALE_SP_INDEX_NOFOLLOW'"><meta name="robots" ><xsl:attribute name="content">index,nofollow</xsl:attribute></meta></xsl:when>
					<xsl:when test="//modules/module[@name='client']/client/robots='LOCALE_SP_NOINDEX_FOLLOW'"><meta name="robots" ><xsl:attribute name="content">noindex,follow</xsl:attribute></meta></xsl:when>
					<xsl:when test="//modules/module[@name='client']/client/robots='LOCALE_SP_NOINDEX_NOFOLLOW'"><meta name="robots" ><xsl:attribute name="content">noindex,nofollow</xsl:attribute></meta></xsl:when>
					<xsl:otherwise>
						<meta name="robots" ><xsl:attribute name="content"><xsl:value-of select="//modules/module[@name='client']/client/robots" disable-output-escaping="yes"/></xsl:attribute></meta>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
			<xsl:if test ="//modules/module[@name='client']/client/revisit!=''">
				<meta name="revisit-after"><xsl:attribute name="content"><xsl:value-of select="//modules/module[@name='client']/client/revisit" disable-output-escaping="yes"/> days</xsl:attribute></meta>
			</xsl:if>
					<meta name="MSSmartTagsPreventParsing" content="TRUE"/>
					<meta name="generator"><xsl:attribute name="content"><xsl:value-of select="//modules/module[@name='client']/licence/product" disable-output-escaping="yes"/></xsl:attribute></meta>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:template>
	<xsl:template name="get_keywords"><xsl:call-template name="display_all_keywords"><xsl:with-param name="total"><xsl:value-of select="count(//modules/container/webobject/module[@name='presentation' and @display='ENTRY']/page)"/></xsl:with-param></xsl:call-template></xsl:template>
	
	<xsl:template name="display_all_keywords">
		<xsl:param name="current_list"></xsl:param>
		<xsl:param name="current_position">1</xsl:param>
		<xsl:param name="total">0</xsl:param>
		<xsl:variable name="return">
			
			<!--<xsl:for-each select="//xml_document/modules/container/webobject/module/*/page[position()=$current_position]/metadata/keywords/keyword[not(contains($current_list,.))]"> -->
			<xsl:for-each select="//page[position()=$current_position]">
				<xsl:if test="not(contains($current_list,./metadata/keywords))">
					<xsl:value-of select="./metadata/keywords" disable-output-escaping="yes"/> ,
				</xsl:if>					
			</xsl:for-each>			
		</xsl:variable>
		
		<xsl:choose>
			<xsl:when test="$total!=$current_position">
				<xsl:variable name="test">
					<xsl:call-template name="display_all_keywords">
						<xsl:with-param name="total"><xsl:value-of select="$total"/></xsl:with-param>
						<xsl:with-param name="current_list"><xsl:value-of select="$return"/></xsl:with-param>
						<xsl:with-param name="current_position"><xsl:value-of select="$current_position + 1"/></xsl:with-param>
					</xsl:call-template>
				</xsl:variable>
				<xsl:value-of select="$test"/>
			</xsl:when>
			<xsl:otherwise>
				<xsl:value-of select="$return"/>
			</xsl:otherwise>
		</xsl:choose>		
		<xsl:value-of select="$return"/>		
	</xsl:template>
	
	<xsl:template name="get_newest_date">
		<xsl:param name="current_date"></xsl:param>
		<xsl:param name="current_position">1</xsl:param>
		<xsl:variable name="return">
			<xsl:choose>
				<xsl:when test="//page[position()>$current_position]/metadata/date[@refinement='publish']/@seconds > $current_date">
					<xsl:call-template name="get_newest_date">
						<xsl:with-param name="current_date"><xsl:value-of select="//page[position()>$current_position]/metadata/date[@refinement='publish'][@seconds > $current_date]/@seconds"/></xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise><xsl:value-of select="//page/metadata/date[@refinement='publish'][@seconds=$current_date]"/></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:value-of select="$return"/>
	</xsl:template>
</xsl:stylesheet>