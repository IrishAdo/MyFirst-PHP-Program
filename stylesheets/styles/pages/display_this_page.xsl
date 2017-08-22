<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.15 $
- Modified $Date: 2005/02/09 12:08:56 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
load comment formatting
-->
<xsl:include href="display_this_page_in_table.xsl"/>
<xsl:include href="display_this_page_textonly.xsl"/>

<!--
generic Page formatting
-->
<xsl:template name="display_this_page">
	<xsl:param name="summary">0</xsl:param>
	<xsl:param name="content">0</xsl:param>
	<xsl:param name="author">0</xsl:param>
	<xsl:param name="page_title">0</xsl:param>
	<xsl:param name="alt_read_more"></xsl:param>
	<xsl:param name="title">1</xsl:param>
	<xsl:param name="alt_title">0</xsl:param>
	<xsl:param name="source">0</xsl:param>
	<xsl:param name="contributors">0</xsl:param>
	<xsl:param name="date_publish">0</xsl:param>
	<xsl:param name="date_modified">0</xsl:param>
	<xsl:param name="subject_category">0</xsl:param>
	<xsl:param name="audience">0</xsl:param>
	<xsl:param name="top_of_doc">0</xsl:param>
	<xsl:param name="back">0</xsl:param>
	<xsl:param name="show_btn">1</xsl:param>
	<xsl:param name="more">1</xsl:param>
	<xsl:param name="enable_discussion">0</xsl:param>
	<xsl:param name="style">ENTRY</xsl:param>
	<xsl:param name="identifier">-1</xsl:param>
	<xsl:param name="save_to_bookmark"></xsl:param>
	<xsl:param name="file_location">bottom</xsl:param>
	<xsl:param name="title_bullet"><xsl:value-of select="$title_bullet"/></xsl:param>
	<xsl:param name="title_starter">[[rightarrow]]</xsl:param>
	<xsl:param name="start_on_title_only">1</xsl:param>
	<xsl:param name="title_is_link">0</xsl:param>
	<xsl:param name="showinpage">0</xsl:param>
	<xsl:param name="increment_page_id">0</xsl:param>

<xsl:choose>
<xsl:when test="$image_path='/libertas_images/themes/textonly'">
	<xsl:call-template name="display_this_page_textonly">
		<xsl:with-param name="summary"><xsl:value-of select="$summary"/></xsl:with-param>
		<xsl:with-param name="content"><xsl:value-of select="$content"/></xsl:with-param>
		<xsl:with-param name="author"><xsl:value-of select="$author"/></xsl:with-param>
		<xsl:with-param name="page_title"><xsl:value-of select="$page_title"/></xsl:with-param>
		<xsl:with-param name="alt_read_more"><xsl:value-of select="$alt_read_more"/></xsl:with-param>
		<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
		<xsl:with-param name="alt_title"><xsl:value-of select="$alt_title"/></xsl:with-param>
		<xsl:with-param name="source"><xsl:value-of select="$source"/></xsl:with-param>
		<xsl:with-param name="contributors"><xsl:value-of select="$contributors"/></xsl:with-param>
		<xsl:with-param name="date_publish"><xsl:value-of select="$date_publish"/></xsl:with-param>
		<xsl:with-param name="date_modified"><xsl:value-of select="$date_modified"/></xsl:with-param>
		<xsl:with-param name="subject_category"><xsl:value-of select="$subject_category"/></xsl:with-param>
		<xsl:with-param name="audience"><xsl:value-of select="$audience"/></xsl:with-param>
		<xsl:with-param name="top_of_doc"><xsl:value-of select="$top_of_doc"/></xsl:with-param>
		<xsl:with-param name="back"><xsl:value-of select="$back"/></xsl:with-param>
		<xsl:with-param name="show_btn"><xsl:value-of select="$show_btn"/></xsl:with-param>
		<xsl:with-param name="more"><xsl:value-of select="$more"/></xsl:with-param>
		<xsl:with-param name="enable_discussion"><xsl:value-of select="$enable_discussion"/></xsl:with-param>
		<xsl:with-param name="style"><xsl:value-of select="$style"/></xsl:with-param>
		<xsl:with-param name="identifier"><xsl:value-of select="$identifier"/></xsl:with-param>
		<xsl:with-param name="save_to_bookmark"><xsl:value-of select="$save_to_bookmark"/></xsl:with-param>
		<xsl:with-param name="file_location"><xsl:value-of select="$file_location"/></xsl:with-param>
		<xsl:with-param name="title_bullet"><xsl:value-of select="$title_bullet"/></xsl:with-param>
		<xsl:with-param name="title_starter"><xsl:value-of select="$title_starter"/></xsl:with-param>
		<xsl:with-param name="start_on_title_only"><xsl:value-of select="$start_on_title_only"/></xsl:with-param>
		<xsl:with-param name="title_is_link"><xsl:value-of select="$title_is_link"/></xsl:with-param>
		<xsl:with-param name="showinpage"><xsl:value-of select="$showinpage"/></xsl:with-param>
		<xsl:with-param name="increment_page_id"><xsl:value-of select="$increment_page_id"/></xsl:with-param>
	</xsl:call-template>
</xsl:when>
<xsl:otherwise>
	<xsl:call-template name="display_this_page_in_table">
		<xsl:with-param name="summary"><xsl:value-of select="$summary"/></xsl:with-param>
		<xsl:with-param name="content"><xsl:value-of select="$content"/></xsl:with-param>
		<xsl:with-param name="author"><xsl:value-of select="$author"/></xsl:with-param>
		<xsl:with-param name="page_title"><xsl:value-of select="$page_title"/></xsl:with-param>
		<xsl:with-param name="alt_read_more"><xsl:value-of select="$alt_read_more"/></xsl:with-param>
		<xsl:with-param name="title"><xsl:value-of select="$title"/></xsl:with-param>
		<xsl:with-param name="alt_title"><xsl:value-of select="$alt_title"/></xsl:with-param>
		<xsl:with-param name="source"><xsl:value-of select="$source"/></xsl:with-param>
		<xsl:with-param name="contributors"><xsl:value-of select="$contributors"/></xsl:with-param>
		<xsl:with-param name="date_publish"><xsl:value-of select="$date_publish"/></xsl:with-param>
		<xsl:with-param name="date_modified"><xsl:value-of select="$date_modified"/></xsl:with-param>
		<xsl:with-param name="subject_category"><xsl:value-of select="$subject_category"/></xsl:with-param>
		<xsl:with-param name="audience"><xsl:value-of select="$audience"/></xsl:with-param>
		<xsl:with-param name="top_of_doc"><xsl:value-of select="$top_of_doc"/></xsl:with-param>
		<xsl:with-param name="back"><xsl:value-of select="$back"/></xsl:with-param>
		<xsl:with-param name="show_btn"><xsl:value-of select="$show_btn"/></xsl:with-param>
		<xsl:with-param name="more"><xsl:value-of select="$more"/></xsl:with-param>
		<xsl:with-param name="enable_discussion"><xsl:value-of select="$enable_discussion"/></xsl:with-param>
		<xsl:with-param name="style"><xsl:value-of select="$style"/></xsl:with-param>
		<xsl:with-param name="identifier"><xsl:value-of select="$identifier"/></xsl:with-param>
		<xsl:with-param name="save_to_bookmark"><xsl:value-of select="$save_to_bookmark"/></xsl:with-param>
		<xsl:with-param name="file_location"><xsl:value-of select="$file_location"/></xsl:with-param>
		<xsl:with-param name="title_bullet"><xsl:value-of select="$title_bullet"/></xsl:with-param>
		<xsl:with-param name="title_starter"><xsl:value-of select="$title_starter"/></xsl:with-param>
		<xsl:with-param name="start_on_title_only"><xsl:value-of select="$start_on_title_only"/></xsl:with-param>
		<xsl:with-param name="title_is_link"><xsl:value-of select="$title_is_link"/></xsl:with-param>
		<xsl:with-param name="showinpage"><xsl:value-of select="$showinpage"/></xsl:with-param>
		<xsl:with-param name="increment_page_id"><xsl:value-of select="$increment_page_id"/></xsl:with-param>
	</xsl:call-template>
</xsl:otherwise>
</xsl:choose>
	
</xsl:template>

<xsl:template name="display_page_functions">
	<xsl:param name="display_position">Top</xsl:param>
	<xsl:param name="enable_discussion">0</xsl:param>
	<xsl:param name="show_top_as_image">1</xsl:param>
	<xsl:param name="total_pages"><xsl:value-of select="count(//xml_document/modules/module[@name='presentation']/page)"/></xsl:param>

</xsl:template>


<xsl:template name="display_page_functions_as_widgets">
	<xsl:param name="display_position">Top</xsl:param>
	<xsl:param name="enable_discussion">0</xsl:param>
	<xsl:param name="enable_textonly">0</xsl:param>
	<xsl:param name="show_top_as_image">1</xsl:param>
	<xsl:param name="total_pages"><xsl:value-of select="count(//xml_document/modules/module[@name='presentation']/page)"/></xsl:param>
	<xsl:choose>
		<xsl:when test="$image_path = '/libertas_images/themes/textonly'">
			<xsl:if test="(//modules/container/webobject/module/page[@web_notes=1] and count(//modules/container/webobject/module/page)=1 and //xml_document/modules/module/licence/product/@type='ECMS') or ($show_email_friend!='No' and //xml_document/modules/module/licence/product/@type='ECMS') or $show_add_bookmark!='No'">
			<hr/>
			</xsl:if>
			<xsl:if test="(($show_printer_friendly!='No' or $show_email_friend!='No' or $show_add_bookmark!='No'))">
				<xsl:if test="//modules/container/webobject/module/page[@web_notes=1] and count(//modules/container/webobject/module/page)=1 and //xml_document/modules/module/licence/product/@type='ECMS'">
					[<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>#list_comments</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_COMMENTS'"/></xsl:call-template></a>]
				</xsl:if>
				<xsl:if test="$show_printer_friendly!='No'">
					[<a  title='View in printer friendly mode - opens in new window' ><xsl:attribute name="href">-/-toggle-printer-friendly-mode.php</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_PRINTER_FRENDLY'"/></xsl:call-template></a>]
				</xsl:if>
				<xsl:if test="$show_email_friend!='No' and //xml_document/modules/module/licence/product/@type='ECMS'">
					[<a><xsl:attribute name="href">-email-a-friend.php</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_EMAIL_THIS_PAGE'"/></xsl:call-template></a>]
				</xsl:if>
				<xsl:if test="$show_add_bookmark!='No'">
					[<a><xsl:attribute name="href">-/-bookmark-page.php</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_ADD_TO_FAVOURITES'"/></xsl:call-template></a>]
				</xsl:if>
			</xsl:if>
		</xsl:when>
		<xsl:otherwise>
			<ul class="pageoptions">	
				<xsl:if test="//setting[@name='displaymode']!='pda' and contains(//setting[@name='sp_page_options'] ,'PTR')">
					<li class="po-ptr"><a accesskey="p" title="View in printer friendly mode" class="pagelink">
						<xsl:attribute name="href">-/-toggle-printer-friendly-mode.php</xsl:attribute>
						<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_PRINTER_FRENDLY'"/></xsl:call-template></span></span>
					</a></li>
				</xsl:if>
				<xsl:if test="contains(//setting[@name='sp_page_options'] ,'HOME') and //xml_document/modules/module/licence/product/@type='ECMS'">
					<li class="po-home"><a class="pagelink"><xsl:attribute name="href">index.php</xsl:attribute>
					<xsl:attribute name="title"><xsl:value-of select="//menu[url='index.php']/label"/></xsl:attribute>
					<span class='icon'><span class='text'><xsl:value-of select="//menu[url='index.php']/label"/></span></span></a></li>
				</xsl:if>
				<xsl:if test="//setting[@name='displaymode']!='pda' and contains(//setting[@name='sp_page_options'] ,'TXT')">
					<li class='po-txt'><a class="pagelink" accesskey="m" title="Toggle between text only / graphical versions of site [m]">
					<xsl:attribute name="href">-/-toggle-text-only-mode.php</xsl:attribute>
					<span class='icon'><span class='text'>Text only</span></span></a></li>
				</xsl:if>
				<xsl:if test="contains(//setting[@name='sp_page_options'] ,'EAF') and //xml_document/modules/module/licence/product/@type='ECMS'">
					<li class="po-eaf"><a class="pagelink"><xsl:attribute name="href">-email-a-friend.php</xsl:attribute>
					<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_EMAIL_THIS_PAGE'"/></xsl:call-template></xsl:attribute>
					<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_EMAIL_THIS_PAGE'"/></xsl:call-template></span></span></a></li>
				</xsl:if>
				<xsl:if test="@web_notes='1' and contains(//setting[@name='sp_page_options'] ,'COM') and count(.)=1 and contains($display_position,'Top') and //xml_document/modules/module[@name='client']/licence/product/@type!='SITE'">
					<li class='po-com'><a class="pagelink">
					<xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>#list_comments</xsl:attribute>
					<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_COMMENTS'"/></xsl:call-template></span></span></a></li>
				</xsl:if>
				<xsl:if test="$image_path!='/libertas_images/themes/pda' and contains(//setting[@name='sp_page_options'] ,'CRT') and //setting[@name='shopping']=1">
					<li class="po-crt"><a accesskey="p" title="View your cart" class="pagelink">
						<xsl:attribute name="href">_view-cart.php</xsl:attribute>
						<span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_VIEW_CART'"/></xsl:call-template></span></span>
					</a></li>
				</xsl:if>
			</ul>
		</xsl:otherwise>
	</xsl:choose>


</xsl:template>


	
</xsl:stylesheet>