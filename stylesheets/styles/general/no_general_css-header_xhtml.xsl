<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.13 $
- Modified $Date: 2005/03/18 14:57:26 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="display_companyname">
<xsl:choose>
	<xsl:when test="//modules/module[@name='client']/client/homepagetitle!='' and //setting[@name='real_script']='index.php'"><xsl:value-of select="//modules/module[@name='client']/client/homepagetitle"/></xsl:when>
	<xsl:when test="//modules/module[@name='client']/client/internalpagetitle!=''"><xsl:value-of select="//modules/module[@name='client']/client/internalpagetitle"/></xsl:when>
	<xsl:when test="//modules/module[@name='client']/client/module[@name='contact']/table/row[@label=$company_name]=''"><xsl:choose>
		<xsl:when test="contains(//setting[@name='domain'],'www.')"><xsl:value-of select="translate(substring-before(substring-after(//setting[@name='domain'],'www.'),'.'),'-',' ')"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="translate(substring-before(//setting[@name='domain'],'.'),'-',' ')"/></xsl:otherwise>
	</xsl:choose></xsl:when>
	<xsl:otherwise><xsl:value-of select="//modules/module[@name='client']/client/module[@name='contact']/table/row[@label=$company_name]" disable-output-escaping="yes"/></xsl:otherwise>
</xsl:choose>

</xsl:template>
		 <xsl:variable name="page_title_string">
		 <!--
		 [<xsl:value-of select="count(//content/info/results/entry)"/>]
		 [<xsl:value-of select="substring-after(//setting[@name='real_script'], substring-before(//setting[@name='script'],'index.php'))"/>]
		 [<xsl:value-of select="//content/info/results/entry/field[@name='uri']/value"/>]
		 [<xsl:value-of select="//content/info/results/entry[field[@name='uri' and contains(substring-after(//setting[@name='real_script'], substring-before(//setting[@name='script'],'index.php')),//content/info/results/entry/field[@name='uri']/value)]]/field[@name='ie_title']"/> ]
		 [<xsl:value-of select="//content/info/results/entry/field[@name='ie_title']"/>]
		-->
		 <xsl:choose>
		 <!-- -1 -->
			 <xsl:when test="//webobject/fake_title!=''">
			 <xsl:value-of select="//webobject/fake_title"/> - <xsl:call-template name="display_companyname"/> 
			 </xsl:when>
		 	<xsl:when test="//setting[@name='fake_category']!=''">
				<xsl:choose>
					<xsl:when test ="count(//content/info/results/entry)=1"><xsl:value-of select="//content/info/results/entry/field[@name='ie_title']"/> - <xsl:call-template name="display_companyname"/> </xsl:when>
<!--					<xsl:when test ="count(//content/info/results/entry)=1"><xsl:value-of select="//content/info/results/entry[field[@name='uri' and contains(substring-after(//setting[@name='real_script'], substring-before(//setting[@name='script'],'index.php')),value)]]/field[@name='ie_title']"/> - <xsl:call-template name="display_companyname"/> </xsl:when>-->
					<xsl:otherwise>
					<xsl:variable name="my_title_cat_path">/<xsl:value-of select="//setting[@name='fake_category']"/>/index.php</xsl:variable>
					<xsl:variable name="my_title_cat_path1"><xsl:value-of select="//setting[@name='fake_category']"/>/index.php</xsl:variable>
					<xsl:choose>
						<xsl:when test="//category[uri = $my_title_cat_path]"><xsl:value-of select="//category[uri = $my_title_cat_path]/label "/></xsl:when>
						<xsl:otherwise><xsl:value-of select="//category[uri = $my_title_cat_path1]/label "/></xsl:otherwise>
					</xsl:choose>
					 - <xsl:call-template name="display_companyname"/> </xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		 <!-- 0 -->
		 	<xsl:when test="//setting[@name='real_script']='index.php'"><xsl:choose>
					<xsl:when test="//modules/module[@name='client']/client/homepagetitle!=''"><xsl:value-of select="//modules/module[@name='client']/client/homepagetitle"/></xsl:when>
					<xsl:otherwise><xsl:choose>
						<xsl:when test="contains(//setting[@name='domain'],'www.')"><xsl:value-of select="translate(substring-before(substring-after(//setting[@name='domain'],'www.'),'.'),'-',' ')"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="translate(substring-before(//setting[@name='domain'],'.'),'-',' ')"/></xsl:otherwise>
					</xsl:choose></xsl:otherwise>
				</xsl:choose></xsl:when>
		 <!-- 1 -->
			<xsl:when test="//setting[@name='fake_title']!=''"><xsl:choose>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_ONLY'"><xsl:value-of select="//setting[@name='fake_title']" disable-output-escaping="yes"/></xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_AND_DEFAULT_TITLE'"><xsl:value-of select="//setting[@name='fake_title']" disable-output-escaping="yes"/> - <xsl:call-template name="display_companyname"/> </xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_DEFAULT_TITLE_AND_PAGE_TITLE'"><xsl:call-template name="display_companyname"/> - <xsl:value-of select="//setting[@name='fake_title']" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="//setting[@name='fake_title']" disable-output-escaping="yes"/> - <xsl:call-template name="display_companyname"/> </xsl:otherwise>
			</xsl:choose></xsl:when>
		 <!-- 2 -->
			<xsl:when test="//setting[@name='real_script']='index.php'"><xsl:choose>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_ONLY'"><xsl:value-of select="//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_AND_DEFAULT_TITLE'"><xsl:value-of select="//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page[position()=1]/title" disable-output-escaping="yes"/> - <xsl:call-template name="display_companyname"/> </xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_DEFAULT_TITLE_AND_PAGE_TITLE'"><xsl:call-template name="display_companyname"/> - <xsl:value-of select="//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:call-template name="display_companyname"/> - <xsl:value-of select="//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:otherwise>
				</xsl:choose>
			 </xsl:when>
		 <!-- 3 -->
			<xsl:when test="not(contains(//setting[@name='real_script'],'index.php')) and boolean(//module[@name='presentation' and @display='ATOZ']/letters) and count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page)!=1">
				<xsl:choose>
					<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_ONLY'"><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/> - <xsl:if test="//module[@name='presentation' and @display='ATOZ']/letters[@choosenletter!='']"><xsl:value-of select="//module[@name='presentation' and @display='ATOZ']/letters/@choosenletter"/></xsl:if></xsl:when>
					<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_AND_DEFAULT_TITLE'"><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/> - <xsl:if test="//module[@name='presentation' and @display='ATOZ']/letters[@choosenletter!='']"><xsl:value-of select="//module[@name='presentation' and @display='ATOZ']/letters/@choosenletter"/> - </xsl:if><xsl:call-template name="display_companyname"/> </xsl:when>
					<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_DEFAULT_TITLE_AND_PAGE_TITLE'"><xsl:call-template name="display_companyname"/> - <xsl:value-of select="//menu[url=//setting[@name='script']]/label"/> - <xsl:if test="//module[@name='presentation' and @display='ATOZ']/letters[@choosenletter!='']"><xsl:value-of select="//module[@name='presentation' and @display='ATOZ']/letters/@choosenletter"/></xsl:if></xsl:when>
					<xsl:otherwise><xsl:call-template name="display_companyname"/> - <xsl:value-of select="//menu[url=//setting[@name='script']]/label"/> - <xsl:if test="//module[@name='presentation' and @display='ATOZ']/letters[@choosenletter!='']"><xsl:value-of select="//module[@name='presentation' and @display='ATOZ']/letters/@choosenletter"/></xsl:if></xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		 <!-- 4 -->
			<xsl:when test="not(contains(//setting[@name='real_script'],'index.php')) and boolean(//module[@name='presentation' and @display='ATOZ']/letters) ">
			<xsl:choose>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_ONLY'"><xsl:call-template name="display_firstpage"/></xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_AND_DEFAULT_TITLE'"><xsl:call-template name="display_firstpage"/> - <xsl:call-template name="display_companyname"/> </xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_DEFAULT_TITLE_AND_PAGE_TITLE'"><xsl:call-template name="display_companyname"/> - <xsl:call-template name="display_firstpage"/></xsl:when>
				<xsl:otherwise><xsl:call-template name="display_companyname"/> - <xsl:call-template name="display_firstpage"/></xsl:otherwise>
				</xsl:choose>
			 </xsl:when>
		 <!-- 5 -->
			<xsl:when test="not(contains(//setting[@name='script'],//setting[@name='fake_script']))">
			<xsl:choose>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_ONLY'"><xsl:value-of select="//modules/container/webobject/module[@name='information_presentation']/content/entry/seperator_row/seperator/field[@name='ie_title']/value" disable-output-escaping="yes"/></xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_AND_DEFAULT_TITLE'"><xsl:value-of select="//modules/container/webobject/module[@name='information_presentation']/content/entry/seperator_row/seperator/field[@name='ie_title']/value" disable-output-escaping="yes"/> - <xsl:call-template name="display_companyname"/> </xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_DEFAULT_TITLE_AND_PAGE_TITLE'"><xsl:call-template name="display_companyname"/> - <xsl:value-of select="//modules/container/webobject/module[@name='information_presentation']/content/entry/seperator_row/seperator/field[@name='ie_title']/value" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:call-template name="display_companyname"/> - <xsl:value-of select="//modules/container/webobject/module[@name='information_presentation']/content/entry/seperator_row/seperator/field[@name='ie_title']/value" disable-output-escaping="yes"/></xsl:otherwise>
				</xsl:choose>
			 </xsl:when>
		 <!-- 6 -->
			<xsl:when test="//menu[url=//setting[@name='script']]/@title_page = 0">
			<xsl:choose>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_ONLY'"><xsl:choose>
					<xsl:when test="count(//modules/container/webobject/module/content/info/results/entry)=1"><xsl:value-of select="//modules/container/webobject/module/content/info/results/entry/field[@name='ie_title']"/></xsl:when>
					<xsl:when test="count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page) != 1"><xsl:value-of select="//menu[url=//	setting[@name='script']]/label"/></xsl:when>
					<xsl:otherwise><xsl:call-template name="display_firstpage"/></xsl:otherwise>
				</xsl:choose></xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_AND_DEFAULT_TITLE'"><xsl:choose>
					<xsl:when test="count(//modules/container/webobject/module/content/info/results/entry)=1"><xsl:value-of select="//modules/container/webobject/module/content/info/results/entry/field[@name='ie_title']"/> - </xsl:when>
					<xsl:when test="count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page) != 1"><xsl:value-of select="//menu[url=//	setting[@name='script']]/label"/> - </xsl:when>
					<xsl:otherwise><xsl:call-template name="display_firstpage"/> - </xsl:otherwise>
				</xsl:choose> <xsl:call-template name="display_companyname"/> </xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_DEFAULT_TITLE_AND_PAGE_TITLE'"><xsl:call-template name="display_companyname"/> - <xsl:choose>
					<xsl:when test="count(//modules/container/webobject/module/content/info/results/entry)=1"><xsl:value-of select="//modules/container/webobject/module/content/info/results/entry/field[@name='ie_title']"/></xsl:when>
					<xsl:when test="count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page) != 1"><xsl:value-of select="//menu[url=//	setting[@name='script']]/label"/></xsl:when>
					<xsl:otherwise><xsl:call-template name="display_firstpage"/></xsl:otherwise>
				</xsl:choose></xsl:when>
				<xsl:otherwise><xsl:call-template name="display_companyname"/><xsl:choose>
					<xsl:when test="count(//modules/container/webobject/module/content/info/results/entry)=1"><xsl:value-of select="//modules/container/webobject/module/content/info/results/entry/field[@name='ie_title']"/></xsl:when>
					<xsl:when test="count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page) != 1"><xsl:value-of select="//menu[url=//	setting[@name='script']]/label"/></xsl:when>
					<xsl:otherwise><xsl:call-template name="display_firstpage"/></xsl:otherwise>
				</xsl:choose></xsl:otherwise>
				</xsl:choose>
			</xsl:when>
		 <!-- 7 -->
			<xsl:otherwise><xsl:choose>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_ONLY'"><xsl:value-of select="//modules/container/webobject/module[@name='presentation']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_PAGE_TITLE_AND_DEFAULT_TITLE'"><xsl:value-of select="//modules/container/webobject/module[@name='presentation']/page[position()=1]/title" disable-output-escaping="yes"/> - <xsl:call-template name="display_companyname"/> </xsl:when>
				<xsl:when test="//client/homepagedisplayformat='LOCALE_SP_DEFAULT_TITLE_AND_PAGE_TITLE'"><xsl:call-template name="display_companyname"/> - <xsl:value-of select="//modules/container/webobject/module[@name='presentation']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:call-template name="display_companyname"/> - <xsl:value-of select="//modules/container/webobject/module[@name='presentation']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:otherwise>
		</xsl:choose></xsl:variable>	 	
<!--
This function will take an ignore parameter in the format of '[IGNORE_TXT1</li>
<li>IGNORE_TXT2</li>
<li>...]'

we are using the substring-after function which will return empty if the ignore command is not found.
-->

<xsl:template name="display_logo">
	<xsl:choose>
		<xsl:when test="//module/client/@logo='1'"><h1 class="company_logo"><xsl:value-of select="//module/client/module/table/row[@label=$company_name]"/></h1></xsl:when>
		<xsl:when test="//module/client/@logo='2'"><img src="images/company_logo.gif"><xsl:attribute name="alt"><xsl:value-of select="//module/client/module/table/row[@label=$company_name]"/> - <xsl:value-of select="//module/client/strapline" disable-output-escaping="yes"/></xsl:attribute></img></xsl:when>
		<xsl:when test="//module/client/@logo='4'"><table cellspacing="0" cell_padding="0"><tr><td><img src="images/company_logo.gif"><xsl:attribute name="alt"><xsl:value-of select="//module/client/module/table/row[@label=$company_name]"/> - <xsl:value-of select="//module/client/strapline" disable-output-escaping="yes"/></xsl:attribute></img></td><td><h1 class="company_logo"><xsl:value-of select="//module/client/module/table/row[@label=$company_name]" disable-output-escaping="yes"/></h1></td></tr></table>
	</xsl:when>
	<xsl:otherwise></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_wai_footer_links">
<div id="botkeys">
	<ul class="accesskeysbottom">
		<li><a accesskey="m" title="Toggle low graphics version [m]"><xsl:attribute name="href"><xsl:if test="//setting[@name='isbot']='0'">-/-toggle-text-only-mode.php</xsl:if></xsl:attribute><span class="icon"><span class="text">Text only version</span></span></a></li>
		<xsl:choose>
			<xsl:when test="//setting[@name='fontsize']='smallest'">
				<li><a accesskey="=" title="Increase font size [=]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute><span class="icon"><span class="text">Increase font size</span></span></a></li>
				<li><a accesskey="+" title="Increase font size [+]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute><span class="icon"><span class="text">Increase font size</span></span></a></li>
			</xsl:when>
			<xsl:when test="//setting[@name='fontsize']='smaller'">
				<li><a accesskey="-" title="Reduce font size [-]"><xsl:attribute name="href">-/-reduce-font.php</xsl:attribute><span class="icon"><span class="text">Reduce font size</span></span></a></li>
				<li><a accesskey="=" title="Increase font size [=]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute><span class="icon"><span class="text">Increase font size</span></span></a></li>
				<li><a accesskey="+" title="Increase font size [+]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute><span class="icon"><span class="text">Increase font size</span></span></a></li>
			</xsl:when>
			<xsl:when test="//setting[@name='fontsize']=''"><xsl:if test="//setting[@name='isbot']=0">
				<li><a accesskey="-" title="Reduce font size [-]"><xsl:attribute name="href">-/-reduce-font.php</xsl:attribute><span class="icon"><span class="text">Reduce font size</span></span></a></li>
				<li><a accesskey="=" title="Increase font size [=]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute><span class="icon"><span class="text">Increase font size</span></span></a></li>
				<li><a accesskey="+" title="Increase font size [+]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute><span class="icon"><span class="text">Increase font size</span></span></a></li>
				</xsl:if>
			</xsl:when>
			<xsl:when test="//setting[@name='fontsize']='larger'">
				<li><a accesskey="-" title="Reduce font size [-]"><xsl:attribute name="href">-/-reduce-font.php</xsl:attribute><span class="icon"><span class="text">Reduce font size</span></span></a></li>
				<li><a accesskey="=" title="Increase font size [=]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute><span class="icon"><span class="text">Increase font size</span></span></a></li>
				<li><a accesskey="+" title="Increase font size [+]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute><span class="icon"><span class="text">Increase font size</span></span></a></li>
			</xsl:when>
			<xsl:when test="//setting[@name='fontsize']='largest'">
				<li><a accesskey="-" title="Reduce font size [-]"><xsl:attribute name="href">-/-reduce-font.php</xsl:attribute><span class="icon"><span class="text">Reduce font size</span></span></a></li>
			</xsl:when>
		</xsl:choose>
		<xsl:choose>
			<xsl:when test="//module[@name='client']/licence/product/@type='ECMS'"><xsl:value-of select="//setting[@name='accesskeys']"/></xsl:when>
			<xsl:otherwise>
				<li><a accesskey="0" title="Accesskey detail [0]"><xsl:attribute name="href">-access-key-defintion.php</xsl:attribute><span class="icon"><span class="text">Accesskey detail</span></span></a></li>
				<li><a accesskey="1" title="Home Page [1]"><xsl:attribute name="href">index.php</xsl:attribute><span class="icon"><span class="text">Home Page</span></span></a></li>
				<li><a accesskey="2" title="Whats new [2]"><xsl:attribute name="href">-whats-new.php</xsl:attribute><span class="icon"><span class="text">Whats new on the site</span></span></a></li>
				<li><a accesskey="3" title="Sitemap [3]"><xsl:attribute name="href"><xsl:choose>
					<xsl:when test="//menu/display_options/display='SITEMAP_DISPLAY'"><xsl:value-of select="//menu[display_options/display='SITEMAP_DISPLAY']/url"/></xsl:when>
					<xsl:otherwise>-site-map.php</xsl:otherwise>
				</xsl:choose></xsl:attribute><span class="icon"><span class="text">View the Site Map</span></span></a></li>
				<li><a accesskey="4" title="Search [4]"><xsl:attribute name="href">-search.php</xsl:attribute><span class="icon"><span class="text">Search the site</span></span></a></li>
				<li><a accesskey="9" title="Feed back form [9]"><xsl:attribute name="href">-/-feedback-form.php</xsl:attribute><span class="icon"><span class="text">Feedback Form</span></span></a></li>
			</xsl:otherwise>
		</xsl:choose>
		<li><a accesskey="p" title="Show this page in the printer friendly mode [p] - opens in new window"><xsl:attribute name="href">-/-toggle-printer-friendly-mode.php</xsl:attribute><span class="icon"><span class="text"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_PRINTER_FRENDLY'"/></xsl:call-template></span></span></a></li>
	</ul>
</div>
</xsl:template>


<xsl:template name="display_wai_header_links">

<div><ul class="accesskeys" id="accesskeystop">
	<li class='text'><span class="icon"><span class="text">Accessibility features:</span></span></li>
	
			<li> <a accesskey="s" title="Skip start of page content [s]"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_url']"/>#content</xsl:attribute><span class="icon"><span class="text">Skip to content</span></span></a></li>
			<li>| <a title="Skip to main navigation for site"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_url']"/>#menu</xsl:attribute><span class="icon"><span class="text">Skip to menu</span></span></a></li>
			<li>| <a accesskey="m" title="Toggle between text only / graphical versions of site [m]" href="-/-toggle-text-only-mode.php"><span class="icon"><span class="text">Text only</span></span></a></li>
			<li>| <a accesskey="0" title="Access keys to aid site navigation [0]" href="-access-key-defintion.php"><span class="icon"><span class="text">Access keys</span></span></a></li>
</ul>
</div>
	<xsl:variable name="frames"><xsl:for-each select="//frame"><xsl:if test="contains(uri,//setting[@name='domain'])">1</xsl:if></xsl:for-each></xsl:variable>
	<xsl:if test="$frames!=''"><script type="text/javascript" src="/libertas_images/javascripts/iframe_resize.js"><xsl:comment>load the iframe resize code</xsl:comment></script></xsl:if>
	<!-- <xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'"><script type="text/javascript" src="/libertas_images/javascripts/formdefintion.js"><xsl:comment>load the form blank code</xsl:comment></script></xsl:if> -->
</xsl:template>

<xsl:template name="display_wai_header_links_new">
<script language="JavaScript" src="/libertas_images/javascripts/column.js"></script>
<div><ul class="accesskeys" id="accesskeystop">
	<li class='text'><span class="icon"><span class="text">Accessibility:</span></span></li>
	
			<li> <a accesskey="s" title="Skip to the start of page content [s]"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_url']"/>#content</xsl:attribute><span class="icon"><span class="text">Skip to content</span></span></a></li>
			<li>| <a title="Skip to themain navigation for site"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_url']"/>#menu</xsl:attribute><span class="icon"><span class="text">Skip to menu</span></span></a></li>
			<li>| <a accesskey="m" title="Toggle between text only and graphical versions of site [m]" href="-/-toggle-text-only-mode.php"><span class="icon"><span class="text">Text only</span></span></a></li>
			<li>| <a accesskey="=" title="Increase the text size [=]" href="/-/-increase-font.php"><span class="icon"><span class="text">Text +</span></span></a></li>
<li>| <a accesskey="-" title="Reduce the text size [-]" href="/-/-reduce-font.php"><span class="icon"><span class="text">Text -</span></span></a></li>
				
</ul>
</div>
	<xsl:variable name="frames"><xsl:for-each select="//frame"><xsl:if test="contains(uri,//setting[@name='domain'])">1</xsl:if></xsl:for-each></xsl:variable>
	<xsl:if test="$frames!=''"><script type="text/javascript" src="/libertas_images/javascripts/iframe_resize.js"><xsl:comment>load the iframe resize code</xsl:comment></script></xsl:if>
	<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'"><script type="text/javascript" src="/libertas_images/javascripts/formdefintion.js"><xsl:comment>load the form blank code</xsl:comment></script></xsl:if>
</xsl:template>

<xsl:template name="display_wai_header_links_goudo">
<script language="JavaScript" src="/libertas_images/javascripts/column.js"></script>
<div id="header_links">
<div class="keytitle">Accessibility:</div><div id="accessmenu">
<ul class="accesskeys" id="accesskeystop">
	
	
			<li> <a accesskey="s" title="Skip to the start of page content [s]"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_url']"/>#content</xsl:attribute><span class="icon"><span class="text">Skip to content</span></span></a></li>
			<li> <a title="Skip to themain navigation for site"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_url']"/>#menu</xsl:attribute><span class="icon"><span class="text">Skip to menu</span></span></a></li>
			<li> <a accesskey="m" title="Toggle between text only and graphical versions of site [m]" href="-/-toggle-text-only-mode.php"><span class="icon"><span class="text">Text only</span></span></a></li>
			<li> <a accesskey="=" title="Increase the font size [=]" href="/-/-increase-font.php"><span class="icon"><span class="text">Text size +</span></span></a></li>
<li> <a accesskey="-" title="Reduce the font size [-]" href="/-/-reduce-font.php"><span class="icon"><span class="text">Text size -</span></span></a></li>
				
</ul>
</div></div>
	<xsl:variable name="frames"><xsl:for-each select="//frame"><xsl:if test="contains(uri,//setting[@name='domain'])">1</xsl:if></xsl:for-each></xsl:variable>
	<xsl:if test="$frames!=''"><script type="text/javascript" src="/libertas_images/javascripts/iframe_resize.js"><xsl:comment>load the iframe resize code</xsl:comment></script></xsl:if>
	<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'"><script type="text/javascript" src="/libertas_images/javascripts/formdefintion.js"><xsl:comment>load the form blank code</xsl:comment></script></xsl:if>
</xsl:template>


<xsl:template name="display_firstpage">
<xsl:choose>
	<xsl:when test="//modules/container/webobject/module[@name='presentation']/page"><xsl:value-of select="//modules/container/webobject/module[@name='presentation']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:when>
	<xsl:when test="//modules/container/webobject/module/content/info/results/entry"><xsl:value-of select="//modules/container/webobject/module/content/info/results/entry[position()=1]/field[@name='ie_title']" disable-output-escaping="yes"/></xsl:when>
</xsl:choose>
</xsl:template>

<xsl:template name="display_header_data">
	<xsl:param name="style_overwrite">style_<xsl:value-of select="//setting[@name='css']"/>.css</xsl:param>
<xsl:if test="boolean(//setting[@name='showbasehref'])">
<base>
	<xsl:attribute name='href'><xsl:value-of select="//setting[@name='showbasehref']"/></xsl:attribute>
</base>
</xsl:if>

			
		<title><xsl:value-of select="substring( $page_title_string, 0, 79)"/><xsl:if test="//setting[@name='expires']"> - Demo expires on <xsl:value-of select="/xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='expires']"/></xsl:if></title>
		<xsl:call-template name="display_metadata"/>
		<xsl:if test="$image_path!='/libertas_images/themes/pda'">
			<xsl:if test="//setting[@name='favicon']=1">
				<link href="images/favicon.ico" rel="shortcut icon"/>
			</xsl:if>
			<xsl:if test="//setting[@name='fontsize']='smallest'">
				<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/>/libertas_images/themes/smallest.css</xsl:attribute></link>
			</xsl:if>
			<xsl:if test="//setting[@name='fontsize']='smaller'">
				<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/>/libertas_images/themes/smaller.css</xsl:attribute></link>
			</xsl:if>
			<xsl:if test="//setting[@name='fontsize']='larger'">
				<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/>/libertas_images/themes/larger.css</xsl:attribute></link>
			</xsl:if>
			<xsl:if test="//setting[@name='fontsize']='largest'">
					<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/>/libertas_images/themes/largest.css</xsl:attribute></link>
			</xsl:if>
			
			
			<xsl:choose>
				<xsl:when test="//setting[@name='overridecss'] != ''"><link rel='stylesheet' type='text/css'><xsl:attribute name="href"><xsl:value-of select="//setting[@name='overridecss']"/></xsl:attribute></link></xsl:when>
				<xsl:otherwise><link rel='stylesheet' type='text/css'><xsl:attribute name="href"><xsl:value-of select="$image_path"/>/<xsl:value-of select="$style_overwrite"/></xsl:attribute></link></xsl:otherwise>
			</xsl:choose>
			
<!--			<link rel='stylesheet' type='text/css'><xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/>container.css</xsl:attribute></link> -->
		</xsl:if>
</xsl:template>

<xsl:template name="display_wai_header_links_no_images">
<div>
<xsl:choose>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'Bot')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'bot')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'spider')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'Spider')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'crawl')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'Crawl')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'scooter')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'Scooter')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'Internet')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'trivial')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'Walker')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'slurp')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'Ask Jeeves')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'scrubby')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'link checker')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'Tooter')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'dmoz_survey')"></xsl:when>
	<xsl:when test="contains(//xml_document/modules/module/setting[@name='browser'],'Web Stripper')"></xsl:when>
	<xsl:otherwise>
	</xsl:otherwise>
</xsl:choose>
| <a accesskey="1" title="Home page [1]"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/>index.php</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_HOME'"/></xsl:call-template></a> |
		<a accesskey="s" title="Jump directly to the start of the content [s]"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>#page1</xsl:attribute>Skip to content</a> |
		<a title="Jump directly to the start of the menu"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>#menu</xsl:attribute>Skip to navigation</a> |
		<a accesskey="2" title="Whats new [2]"><xsl:attribute name="href">-whats-new.php</xsl:attribute>Whats new</a> |
		<a accesskey="3" title="Sitemap [3]"><xsl:attribute name="href"><xsl:choose>
			<xsl:when test="//menu/display_options/display='SITEMAP_DISPLAY'"><xsl:value-of select="//menu[display_options/display='SITEMAP_DISPLAY']/url"/></xsl:when>
			<xsl:otherwise>-site-map.php</xsl:otherwise>
		</xsl:choose></xsl:attribute>Site Map</a> |
		<a accesskey="4" title="Search [4]"><xsl:attribute name="href">-search.php</xsl:attribute>Search the site</a> | 
		<a accesskey="9" title="Feed back form [9]"><xsl:attribute name="href">-/-feedback-form.php</xsl:attribute>Feedback Form</a> | 
		<a accesskey="0" title="Accesskey details [0]"><xsl:attribute name="href"><xsl:choose>
			<xsl:when test="//setting[@name='accesskeys']!=''"><xsl:value-of select="//setting[@name='real_script']"/>#access_keys</xsl:when>
			<xsl:otherwise>-access-key-defintion.php</xsl:otherwise>
		</xsl:choose></xsl:attribute>Access key details</a> |
		<br/>
		| 
			<xsl:choose>
				<xsl:when test="//setting[@name='fontsize']='smallest'">
					<a accesskey="+" title="Increase font size [+]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute>Increase font size</a> |
				</xsl:when>
				<xsl:when test="//setting[@name='fontsize']='smaller'">
					<a accesskey="-" title="Reduce font size [-]"><xsl:attribute name="href">-/-reduce-font.php</xsl:attribute>Reduce font size</a> |
					<a accesskey="+" title="Increase font size [+]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute>Increase font size</a> |
				</xsl:when>
				<xsl:when test="//setting[@name='fontsize']=''"><xsl:if test="//setting[@name='isbot']=0">
					<a accesskey="-" title="Reduce font size [-]"><xsl:attribute name="href">-/-reduce-font.php</xsl:attribute>Reduce font size</a> |
					<a accesskey="+" title="Increase font size [+]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute>Increase font size</a> |
					</xsl:if>
				</xsl:when>
				<xsl:when test="//setting[@name='fontsize']='larger'">
					<a accesskey="-" title="Increase font size [-]"><xsl:attribute name="href">-/-reduce-font.php</xsl:attribute>Reduce font size</a> |
					<a accesskey="+" title="Increase font size [+]"><xsl:attribute name="href">-/-increase-font.php</xsl:attribute>Increase font size</a> |
				</xsl:when>
				<xsl:when test="//setting[@name='fontsize']='largest'">
					<a accesskey="-" title="Reduce font size [-]"><xsl:attribute name="href">-/-reduce-font.php</xsl:attribute>Reduce font size</a> |
				</xsl:when>
			</xsl:choose>
		<a accesskey="m" title="Switch to graphics version [m]"><xsl:attribute name="href">-/-toggle-text-only-mode.php</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_FULL_GRAPHICS'"/></xsl:call-template></a> |<br/>
</div>
</xsl:template>

<xsl:template name="display_header_data_no_images">
	<xsl:param name="style_overwrite">style.css</xsl:param>
		<title><xsl:value-of select="substring( $page_title_string, 0, 79)"/><xsl:if test="//setting[@name='expires']"> - Demo expires on <xsl:value-of select="/xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='expires']"/></xsl:if></title>
		<META NAME="ROBOTS" CONTENT="NOARCHIVE"/>
		<META NAME="ROBOTS" CONTENT="NOINDEX"/>
		<META NAME="ROBOTS" CONTENT="NOFOLLOW"/>
		
		<xsl:if test="//setting[@name='favicon']=1">
			<link href="images/favicon.ico" rel="shortcut icon"/>
		</xsl:if>
		<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/>/libertas_images/themes/general.css</xsl:attribute></link>
		<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/><xsl:value-of select="$image_path"/>/<xsl:value-of select="$style_overwrite"/></xsl:attribute></link>
<!--		<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/><xsl:value-of select="//setting[@name='base']"/>container.css</xsl:attribute></link> -->
		<xsl:if test="//setting[@name='fontsize']='smallest'">
			<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/>/libertas_images/themes/smallest.css</xsl:attribute></link>
		</xsl:if>
		<xsl:if test="//setting[@name='fontsize']='smaller'">
			<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/>/libertas_images/themes/textsmaller.css</xsl:attribute></link>
		</xsl:if>
		<xsl:if test="//setting[@name='fontsize']='larger'">
			<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/>/libertas_images/themes/larger.css</xsl:attribute></link>
		</xsl:if>
		<xsl:if test="//setting[@name='fontsize']='largest'">
			<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/>/libertas_images/themes/largest.css</xsl:attribute></link>
		</xsl:if>
		<xsl:if test="//setting[@name='fontsize']=''">
			<link rel='stylesheet' type='text/css'><xsl:attribute name="href">http://<xsl:value-of select="//setting[@name='domain']"/>/libertas_images/themes/normal.css</xsl:attribute></link>
		</xsl:if>
</xsl:template>

<xsl:template match="access_list">
<xsl:if test="accesskey[url!='']"><blockquote>
<table summary="Access Keys used in this site">
	<tr><th scope="col" style="width:2em">Key</th><th scope="col" >Definition</th></tr>
	<xsl:for-each select="accesskey[url!='']">
	<tr><td style="width:2em"><xsl:value-of select="@letter"/></td><td ><xsl:value-of select="label"/></td></tr>
	</xsl:for-each>
</table>
</blockquote>
</xsl:if>
</xsl:template>

</xsl:stylesheet>
