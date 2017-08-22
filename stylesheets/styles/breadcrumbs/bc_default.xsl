<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.12 $
- Modified $Date: 2005/02/12 15:23:27 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
 

<xsl:template name="display_breadcrumb_trail">
	<xsl:param name="url" select="//setting[@name='script']"/>
	<xsl:param name="youarehere">1</xsl:param>
	<xsl:param name="show_fake">0</xsl:param>
	<xsl:param name="displayhome">1</xsl:param>
	<xsl:param name="linking">0</xsl:param>
	<xsl:param name="splitter">[[nbsp]][[rightarrow]][[nbsp]]</xsl:param>
	<xsl:param name="displaylast"><xsl:choose>
		<xsl:when test="boolean(//current_category)">0</xsl:when>
		<xsl:otherwise>1</xsl:otherwise>
	</xsl:choose></xsl:param>
	<!-- display breadcrumbs -->
	<xsl:if test="$youarehere=1"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_YOU_ARE_HERE'"/></xsl:call-template><xsl:value-of select="$splitter" disable-output-escaping="no"/></xsl:if>
	
	<xsl:variable name="test">index.php</xsl:variable>

	<xsl:if test="$url!=$test and not(boolean(//menu[url='index.php']//children/menu[url=$url]))">
		<xsl:if test="$displayhome=1">
			<xsl:choose>
				<xsl:when test="$linking=0"><a title="Home" class="breadcrumb"><xsl:attribute name="href">index.php</xsl:attribute>Home</a><xsl:value-of select="$splitter" disable-output-escaping="no"/></xsl:when>
				<xsl:when test="$linking=2">Home<xsl:copy-of select="$splitter"/></xsl:when>
				<xsl:otherwise>Home<xsl:copy-of select="$splitter"/></xsl:otherwise>
			</xsl:choose>
		</xsl:if>
	</xsl:if>
		<xsl:for-each select="//xml_document/modules/module[@name='layout']">
			<xsl:call-template name="display_breadcrumb_parent">
				<xsl:with-param name="parent_identifier" select="-1"/>       
				<xsl:with-param name="link" select="$linking"/>
				<xsl:with-param name="current_url" select="$url"/>      
				<xsl:with-param name="splitter" select="$splitter"/>
				<xsl:with-param name="displaylast" select="$displaylast"/>
			</xsl:call-template>
		</xsl:for-each>
<!--
	[bct:50]
	[<xsl:value-of select="//setting[@name='fake_script']"/>]
-->
	<xsl:if test="//setting[@name='fake_script']!='-'">
		<xsl:if test="$displaylast=0 and substring(//setting[@name='real_script'],1,1)!='_' and substring(substring-after(//setting[@name='real_script'],//setting[@name='fake_script']),2,1)!='_'">
			<a class='breadcrumb'>
				<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/></xsl:attribute>
				<xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></a>
				<xsl:variable name="uri"><xsl:value-of select="substring-before(//module/fake_uri,'index.php')"/></xsl:variable>
				<xsl:variable name="cat"><xsl:value-of select="//module/current_category"/></xsl:variable>
				<xsl:if test="$cat!=''">
					<xsl:call-template name="build_directory_breadcrumb">
						<xsl:with-param name="identifier"><xsl:value-of select="$cat"/></xsl:with-param>
						<xsl:with-param name="uri"><xsl:value-of select="$uri"/></xsl:with-param>
					</xsl:call-template>
				</xsl:if>
		</xsl:if>
		<xsl:if test="//setting[@name='fake_title']!='' and (substring(//setting[@name='real_script'],1,1)!='-' or $show_fake='1')">
			<xsl:value-of select="$splitter" disable-output-escaping="no"/> <a class="breadcrumb"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="//setting[@name='fake_title']"/></xsl:with-param></xsl:call-template></xsl:attribute>
			<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="//setting[@name='fake_title']"/></xsl:with-param></xsl:call-template>
			</a>
		</xsl:if>
	</xsl:if>
<!--
	[>>]
-->
	<xsl:if test="//setting[@name='fake_title']!='' and //setting[@name='script']='index.php' and not(//setting[@name='fake_title']!='' and (substring(//setting[@name='real_script'],1,1)!='-' or $show_fake='1'))">
	[[nbsp]][[rightarrow]][[nbsp]][[nbsp]]<xsl:value-of select="//setting[@name='fake_title']"/>
	</xsl:if>
<!--
	[<xsl:value-of select="//setting[@name='fake_title']"/>]
	<xsl:if test="//container[contains(@identifier,'unique_')] and //setting[@name='script']='index.php'">
		[[nbsp]][[rightarrow]][[nbsp]]<xsl:value-of select="//container[contains(@identifier,'unique_')]/module[@name='information_presentation' and @display='INFORMATION']/content/info/results/entry[position()=1]/field[@name='ie_title']" />
	</xsl:if>
-->
</xsl:template>

<xsl:template name="display_breadcrumb_parent">
	<xsl:param name="current_url"/>
	<xsl:param name="link"/>
	<xsl:param name="splitter"/>
	<xsl:param name="parent_identifier"/>       
	<xsl:param name="displaylast">1</xsl:param>
	<xsl:for-each select="//menu[@parent=$parent_identifier]">
		<xsl:choose>
			<xsl:when test="url=$current_url">
				<xsl:choose>
					<xsl:when test="$link=0">
						<xsl:if test="$current_url='/index.php'"> <xsl:value-of select="$splitter"/> </xsl:if>
						<xsl:choose>
							<xsl:when test="//page/title[../locations/location[@url=$current_url and (//setting[@name='real_script'] != $current_url)]] or //setting[@name='fake_title']!=''">
								<a class="breadcrumb"><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a>
							</xsl:when>
							<xsl:otherwise><xsl:if test="$displaylast=1">
									<a class="breadcrumb">
									<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
									<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:attribute>
									<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a>
								</xsl:if></xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="$link=2"><xsl:if test="$displaylast=1"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:if></xsl:when>
					<xsl:otherwise><xsl:if test="$displaylast=1"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:if></xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test=".//children/menu[url=$current_url]">
					<xsl:choose>
						<xsl:when test="$link='0'"><a class="breadcrumb"><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a> <xsl:value-of select="$splitter"/> </xsl:when>
						<xsl:when test="$link='2'"><xsl:if test="$displaylast=1"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template> <xsl:value-of select="$splitter"/></xsl:if></xsl:when>
						<xsl:otherwise><xsl:if test="$displaylast=1"> <xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template> <xsl:value-of select="$splitter"/> </xsl:if></xsl:otherwise>
					</xsl:choose>
					<xsl:if test="$displaylast=1">
					<xsl:call-template name="display_breadcrumb_parent">
						<xsl:with-param name="parent_identifier" select="@identifier"/>     
						<xsl:with-param name="splitter" select="$splitter"/>      
						<xsl:with-param name="link" select="$link"/>
						<xsl:with-param name="current_url" select="$current_url"/> 
						<xsl:with-param name="displaylast" select="$displaylast"/>     
    			   	</xsl:call-template> 
				</xsl:if>
				</xsl:if>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>