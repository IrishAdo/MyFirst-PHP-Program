<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2004/10/05 07:52:19 $
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
	<xsl:param name="title_starter"></xsl:param>
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
		<div id='mirrordata'>
			<div class='label'><span><xsl:value-of disable-output-escaping="yes" select="label"/></span></div>
			<xsl:variable name="murl"><xsl:value-of select="menulocation"/></xsl:variable>
			<xsl:variable name="ptitle"><xsl:value-of select="//menu[url=$murl]/@title_page"/></xsl:variable>
			<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='mirror']/module/page[position() != $ptitle][$max_list_count >= position()]">
				<div class='mirroritem'>
				<xsl:variable name="page_date"><xsl:value-of select="metadata/date[@refinement='available']"/></xsl:variable>
				<xsl:choose>
					<xsl:when test="$display_date=1 and contains($display_option,'DATE')">
						<xsl:choose>
							<xsl:when test="$title_link=1 or $summary=0">
								<div class="title">
								<xsl:call-template name="format_date">
							<xsl:with-param name="current_date"><xsl:value-of select="$page_date"/></xsl:with-param>
							<xsl:with-param name="output_format">DD:MM:YYYY</xsl:with-param>
						</xsl:call-template>[[nbsp]]
								<xsl:if test="$title_starter!=''"><xsl:value-of select="$title_starter"/>[[nbsp]]</xsl:if>
								<a class="news"><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute>
								<xsl:attribute name="title"><xsl:call-template name="print">
									<xsl:with-param name="str_value"><xsl:value-of disable-output-escaping="yes" select="title"/></xsl:with-param>
								</xsl:call-template></xsl:attribute>
								<xsl:call-template name="print">
									<xsl:with-param name="str_value"><xsl:value-of disable-output-escaping="yes" select="title"/></xsl:with-param>
								</xsl:call-template></a>
								<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
								</div>
							</xsl:when>
							<xsl:otherwise><div class="title"><xsl:value-of disable-output-escaping="yes" select="title"/><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template></div></xsl:otherwise>
						</xsl:choose>
						<xsl:if test="$summary=1 or $more=1">
						<div class='contentpos'>
							<xsl:if test="$summary=1">
								<br/><span class="summary"><xsl:value-of select="summary" disable-output-escaping="yes"/></span>
							</xsl:if>
							<xsl:if test="$more=1">
								<div class="readmore"><a><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:choose>
									<xsl:when test="$display_more_as_text=1"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template></xsl:when>
									<xsl:otherwise><img border="0">
										<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_MORE.gif</xsl:attribute>
										<xsl:attribute name="width"><xsl:value-of select="$image_more_width"/></xsl:attribute>
										<xsl:attribute name="height"><xsl:value-of select="$image_more_height"/></xsl:attribute>
										<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template> <xsl:value-of select="title" disable-output-escaping="yes"/></xsl:attribute></img></xsl:otherwise>
								</xsl:choose></a></div>
							</xsl:if>
						</div>
						</xsl:if>
					</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="$title_link=1 or $summary=0">
								<div class="title">
								<xsl:if test="$title_starter!=''"><xsl:value-of select="$title_starter"/>[[nbsp]]</xsl:if>
								<a class="news"><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute>
								<xsl:attribute name="title"><xsl:call-template name="print">
									<xsl:with-param name="str_value"><xsl:value-of disable-output-escaping="yes" select="title"/></xsl:with-param>
								</xsl:call-template></xsl:attribute>
								<xsl:value-of disable-output-escaping="yes" select="title"/></a>
								<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
								</div>
							</xsl:when>
							<xsl:otherwise><div class="title"><xsl:value-of disable-output-escaping="yes" select="title"/><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template></div></xsl:otherwise>
						</xsl:choose>
						<xsl:if test="($more=1 and contains($display_option,'READMORE')) or ($summary=1 and contains($display_option,'SUMMARY'))">
						<div class='contentpos'>
						<xsl:if test="$summary=1 and contains($display_option,'SUMMARY')">
							<xsl:value-of disable-output-escaping="yes" select="summary"/>
							<br/>
							</xsl:if>
							<xsl:if test="$more=1 and contains($display_option,'READMORE')">
								<div class="readmore"><a class="readmore"><xsl:attribute name="href"><xsl:value-of select="./locations/location[@url!='index.php']" disable-output-escaping="yes"/></xsl:attribute><xsl:choose>
									<xsl:when test="$display_more_as_text=1"> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template></xsl:when>
									<xsl:otherwise><img border="0">
										<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_MORE.gif</xsl:attribute>
										<xsl:attribute name="width"><xsl:value-of select="$image_more_width"/></xsl:attribute>
										<xsl:attribute name="height"><xsl:value-of select="$image_more_height"/></xsl:attribute>
										<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template> :- <xsl:value-of select="title" disable-output-escaping="yes"/></xsl:attribute></img></xsl:otherwise>
								</xsl:choose></a></div>
							</xsl:if>
							</div>
							</xsl:if>
						</xsl:otherwise>
					</xsl:choose>
					</div>
				</xsl:for-each>
		</div>
	
	</xsl:if>
</xsl:template>


</xsl:stylesheet>