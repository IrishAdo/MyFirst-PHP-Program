<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2004/08/24 13:21:58 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"> 
  
<xsl:template name="display_mirror">
	<xsl:param name="header"><xsl:value-of select="//module[@name='mirror']/label" disable-output-escaping="yes"/></xsl:param>
	<xsl:param name="display_option"><xsl:value-of select="//module[@name='mirror']/display"/></xsl:param>
	<xsl:param name="max_list_count"><xsl:value-of select="//module[@name='mirror']/size"/></xsl:param>
	<xsl:param name="hr_return">1</xsl:param>
	<xsl:param name="hr">1</xsl:param>
	<xsl:param name="width">100%</xsl:param>
	<xsl:param name="class">nojustify</xsl:param>
	<xsl:param name="type"></xsl:param>
	<xsl:param name="display_more_as_text"><xsl:value-of select="$more_text"/></xsl:param>
	<xsl:param name="mirror_starter"></xsl:param>
	<xsl:param name="title_starter">[[rightarrow]]</xsl:param>
	<xsl:param name="bullet_width">16</xsl:param>
	<xsl:param name="bullet_height">16</xsl:param>
	<xsl:param name="title_bullet">0</xsl:param>
	<xsl:param name="label_bullet">0</xsl:param>
	<xsl:param name="inTable">0</xsl:param>
	<xsl:param name="show_label">1</xsl:param>

	<!--
		Define variables from xml data to define the look and feel of the mirror
	-->
<xsl:if test="//xml_document/modules/container/webobject/module[@name='mirror']">
	<xsl:variable name="title_link"><xsl:choose>
		<xsl:when test="$display_option='TITLE'">1</xsl:when>
		<xsl:when test="contains($display_option,'TITLE') and not(contains($display_option,'READMORE'))">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="summary"><xsl:choose>
		<xsl:when test="contains($display_option,'SUMMARY')">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="more"><xsl:choose>
		<xsl:when test="contains($display_option,'READMORE')">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="display_date"><xsl:choose>
		<xsl:when test="contains($display_option,'DATE')">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="new_header">
		<xsl:if test="$label_bullet=1">&lt;img border="0" src="<xsl:value-of select="$image_path"/>/title_bullet.gif"&gt;[[nbsp]]</xsl:if>
		<xsl:choose>
			<xsl:when test="$header=''"><xsl:value-of select="//menu[url=//xml_document/modules/container/webobject/module[@name='mirror']/menulocation]/label"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="$header"/></xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:choose>
	<xsl:when test="$inTable=1">
		<xsl:variable name="content">
		<table border="0" cellspacing="0" cellpadding="3" summary="This table contains documents that have been mirrored from another location.">
		<xsl:attribute name="background"><xsl:value-of select="$image_path"/>/mirror_background.gif</xsl:attribute>
		<xsl:attribute name="width"><xsl:value-of select="$width"/></xsl:attribute>
	  		<tr><td colspan="2"><img width="150" alt="" height="1" src="/libertas_images/themes/1x1.gif" border="0"/></td></tr>
			<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='mirror']/module/page[position()!=//menu[url=//xml_document/modules/container/webobject/module[@name='mirror']/menulocation]/@title_page][$max_list_count >= position()]">
		  		<tr><xsl:if test="$title_starter!=''"><td valign="top" class="title_starter"><xsl:value-of select="$title_starter"/>[[nbsp]]</td></xsl:if>
				<td><xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
				<xsl:variable name="page_date"><xsl:value-of select="metadata/date[@refinement='available']"/></xsl:variable>
				<xsl:choose>
					<xsl:when test="$display_date=1 and contains($display_option,'DATE')">
					<xsl:if test="$title_bullet=1">
					<img border="0">
						<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/title_bullet.gif</xsl:attribute>
						<xsl:attribute name="width"><xsl:value-of select="$bullet_width"/></xsl:attribute>
						<xsl:attribute name="height"><xsl:value-of select="$bullet_height"/></xsl:attribute>
						<xsl:attribute name="alt"></xsl:attribute></img>[[nbsp]]</xsl:if>
						<span class="newsdate"><xsl:call-template name="format_date">
						<xsl:with-param name="current_date"><xsl:value-of select="$page_date"/></xsl:with-param>
						</xsl:call-template></span>
						<br/>
						<xsl:choose>
							<xsl:when test="$title_link=1 or $summary=0">
								<a class="news"><xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:attribute><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of disable-output-escaping="yes" select="title"/></xsl:with-param>
						</xsl:call-template></a>
						<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
							</xsl:when>
							<xsl:otherwise><span class="mirrortitle"><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of disable-output-escaping="yes" select="title"/></xsl:with-param>
						</xsl:call-template></span><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template></xsl:otherwise>
						</xsl:choose>
						<xsl:if test="$summary=1">
						<br/><span class="summary"><xsl:value-of select="summary" disable-output-escaping="yes"/></span>
						</xsl:if>
						<xsl:if test="$more=1">
						<p align="right">
						<a><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:choose>
								<xsl:when test="$display_more_as_text=1"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template></xsl:when>
								<xsl:otherwise><img border="0">
									<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_MORE.gif</xsl:attribute>
									<xsl:attribute name="width"><xsl:value-of select="$image_more_width"/></xsl:attribute>
									<xsl:attribute name="height"><xsl:value-of select="$image_more_height"/></xsl:attribute>
									<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template> <xsl:value-of select="title" disable-output-escaping="yes"/></xsl:attribute></img></xsl:otherwise>
								</xsl:choose></a>
						</p>
						</xsl:if>
						<xsl:if test="$hr=1">
						<xsl:if test="$hr_return=1"><br/><br/></xsl:if>
						<img alt="" border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="$title_link=1 or $summary=0">
								<a class="news"><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of disable-output-escaping="yes" select="title"/></xsl:with-param>
						</xsl:call-template></a>
						<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
							</xsl:when>
							<xsl:otherwise><span class="news"><xsl:value-of disable-output-escaping="yes" select="title"/><xsl:call-template name="replace_string">
								<xsl:with-param name="str_value"><xsl:value-of select="title"/></xsl:with-param>
								<xsl:with-param name="find">&amp;#39;</xsl:with-param>
								<xsl:with-param name="replace_with">'</xsl:with-param>
							</xsl:call-template><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template></span></xsl:otherwise>
						</xsl:choose><br/>
						<xsl:if test="$summary=1 and contains($display_option,'SUMMARY')">
						<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:copy-of select="summary"/></xsl:with-param>
						</xsl:call-template>
						<br/>
						</xsl:if>
						<xsl:if test="$more=1 and contains($display_option,'READMORE')">
								<a class="readmore"><xsl:attribute name="href"><xsl:value-of select="./locations/location[@url!='index.php']" disable-output-escaping="yes"/></xsl:attribute><xsl:choose>
								<xsl:when test="$display_more_as_text=1"> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template></xsl:when>
								<xsl:otherwise><img border="0">
										<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_MORE.gif</xsl:attribute>
										<xsl:attribute name="width"><xsl:value-of select="$image_more_width"/></xsl:attribute>
										<xsl:attribute name="height"><xsl:value-of select="$image_more_height"/></xsl:attribute>
										<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template> :- <xsl:value-of select="title" disable-output-escaping="yes"/></xsl:attribute></img></xsl:otherwise>
								</xsl:choose></a>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
				</td></tr>
			</xsl:for-each>
		</table>
	</xsl:variable>
<xsl:call-template name="display_a_table">
	<xsl:with-param name="header">&lt;span class="mirrorlabel"&gt;<xsl:value-of select="$mirror_starter"/>&lt;a class="mirrorlabel" href="<xsl:value-of select="//xml_document/modules/container/webobject/module[@name='mirror']/menulocation"/>"&gt;<xsl:copy-of select="$new_header"/>&lt;/a&gt;&lt;/span&gt;</xsl:with-param>
	<xsl:with-param name="content"><xsl:copy-of select="$content"/></xsl:with-param>
</xsl:call-template>
</xsl:when>
<xsl:otherwise>
		<table border="1" cellspacing="1" cellpadding="3" summary="This table contains documents that have been mirrored from another location.." class="mirrortable">
			<xsl:if test="$show_label=1"><tr class="mirrorHeader"><td colspan="2" height="0.001%"><img width="150" alt="" height="1" src="/libertas_images/themes/1x1.gif" border="0"/><br/><span class="mirrorlabel" ><xsl:value-of select="$mirror_starter"/><a class="mirrorlabel"><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/container/webobject/module[@name='mirror']/menulocation"/></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="$new_header" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a> <xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">MIRROR_</xsl:with-param></xsl:call-template></span></td></tr></xsl:if>
			<tr><td><xsl:attribute name="background"><xsl:value-of select="$image_path"/>/mirror_background.gif</xsl:attribute><table summary="">
			<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='mirror']/module/page[position() != //menu[url=//xml_document/modules/container/webobject/module[@name='mirror']/menulocation]/@title_page][$max_list_count >= position()]">
				<xsl:comment><xsl:value-of disable-output-escaping="yes" select="title"/> </xsl:comment>
		  		<tr id="mirror">
				<xsl:if test="$title_starter!=''"><td valign="top" width="0.001%" class="title_starter"><xsl:value-of select="$title_starter"/>[[nbsp]]</td></xsl:if>
				<td><xsl:attribute name="class"><xsl:value-of select="$class"/></xsl:attribute>
				<xsl:variable name="page_date"><xsl:value-of select="metadata/date[@refinement='available']"/></xsl:variable>
				<xsl:choose>
					<xsl:when test="$display_date=1 and contains($display_option,'DATE')">
					<xsl:if test="$title_bullet=1">
					<img border="0">
						<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/title_bullet.gif</xsl:attribute>
						<xsl:attribute name="width"><xsl:value-of select="$bullet_width"/></xsl:attribute>
						<xsl:attribute name="height"><xsl:value-of select="$bullet_height"/></xsl:attribute>
						<xsl:attribute name="alt"></xsl:attribute></img>[[nbsp]]</xsl:if>
						<span class="newsdate"><xsl:call-template name="format_date">
							<xsl:with-param name="current_date"><xsl:value-of select="$page_date"/></xsl:with-param>
						</xsl:call-template></span>
						<br/>
						<xsl:choose>
							<xsl:when test="$title_link=1 or $summary=0">
								<a class="news"><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of disable-output-escaping="yes" select="title"/></xsl:with-param>
						</xsl:call-template></a>
								<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
							</xsl:when>
							<xsl:otherwise><span class="mirrortitle"><xsl:value-of disable-output-escaping="yes" select="title"/></span><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template></xsl:otherwise>
						</xsl:choose>
						<xsl:if test="$summary=1">
						<br/><span class="summary"><xsl:value-of select="summary" disable-output-escaping="yes"/></span>
						</xsl:if>
							<xsl:if test="$more=1">
						<p align="right">
						<a><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:choose>
								<xsl:when test="$display_more_as_text=1"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template></xsl:when>
								<xsl:otherwise><img border="0">
									<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_MORE.gif</xsl:attribute>
									<xsl:attribute name="width"><xsl:value-of select="$image_more_width"/></xsl:attribute>
									<xsl:attribute name="height"><xsl:value-of select="$image_more_height"/></xsl:attribute>
									<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template> <xsl:value-of select="title" disable-output-escaping="yes"/></xsl:attribute></img></xsl:otherwise>
								</xsl:choose></a>
						</p>
						</xsl:if>
						<xsl:if test="$hr=1">
						<xsl:if test="$hr_return=1"><br/><br/></xsl:if>
						<img alt="" border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="$title_link=1 or $summary=0">
								<!--<a class="news"><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of disable-output-escaping="yes" select="title"/></xsl:with-param>
						</xsl:call-template></a>-->
								<a class="news"><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:value-of disable-output-escaping="yes" select="title"/></a>
						<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
							</xsl:when>
							<xsl:otherwise><span class="news"><xsl:value-of disable-output-escaping="yes" select="title"/><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template></span></xsl:otherwise>
						</xsl:choose><br/>
						<xsl:if test="$summary=1 and contains($display_option,'SUMMARY')">
						<xsl:value-of disable-output-escaping="yes" select="summary"/>
						<br/>
						</xsl:if>
						<xsl:if test="$more=1 and contains($display_option,'READMORE')">
								<a class="readmore"><xsl:attribute name="href"><xsl:value-of select="./locations/location[@url!='index.php']" disable-output-escaping="yes"/></xsl:attribute><xsl:choose>
								<xsl:when test="$display_more_as_text=1"> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template></xsl:when>
								<xsl:otherwise><img border="0">
										<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_MORE.gif</xsl:attribute>
										<xsl:attribute name="width"><xsl:value-of select="$image_more_width"/></xsl:attribute>
										<xsl:attribute name="height"><xsl:value-of select="$image_more_height"/></xsl:attribute>
										<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template> :- <xsl:value-of select="title" disable-output-escaping="yes"/></xsl:attribute></img></xsl:otherwise>
								</xsl:choose></a>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
				</td></tr>
			</xsl:for-each>
			</table></td></tr>
		</table>
		</xsl:otherwise>
		</xsl:choose>
		</xsl:if>
</xsl:template>


</xsl:stylesheet>