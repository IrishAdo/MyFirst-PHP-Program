<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.6 $
- Modified $Date: 2004/10/05 11:03:47 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
default FAQ look and feel
-->
<xsl:template name="display_list">
	<xsl:comment>display three columns title and summary.</xsl:comment>
	<table cellpadding="5" cellspacing="5" border="0" width="100%" summary="This table contains a list of articles for this location">
		<tr>
		<td>
<xsl:choose>
		<xsl:when test="$title_page=1">
			<xsl:call-template name="display_this_page">
				<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
				<xsl:with-param name="alt_title">1</xsl:with-param>
				<xsl:with-param name="content">1</xsl:with-param>
				<xsl:with-param name="date_publish">0</xsl:with-param>
				<xsl:with-param name="more">0</xsl:with-param>
				<xsl:with-param name="style">LOCATION</xsl:with-param>
				<xsl:with-param name="identifier"><xsl:value-of select="//modules/container/webobject/module/page[position()=1]/@identifier"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
			<xsl:variable name="page_title_string"><xsl:choose>
				<xsl:when test="count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page) != 1"><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></xsl:when>

				<xsl:when test="//setting[@name='fake_title']!=''"><xsl:value-of select="//setting[@name='fake_title']" disable-output-escaping="yes"/></xsl:when>
				
				<xsl:when test="//setting[@name='real_script']='index.php'"><xsl:value-of select="//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:when>

				<xsl:when test="not(contains(//setting[@name='real_script'],'index.php')) and boolean(//module[@name='presentation' and @display='ATOZ']/letters) and count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page)!=1"><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></xsl:when>

				<xsl:when test="not(contains(//setting[@name='real_script'],'index.php')) and boolean(//module[@name='presentation' and @display='ATOZ']/letters) "><xsl:call-template name="display_firstpage"/></xsl:when>

				<xsl:when test="not(contains(//setting[@name='script'],//setting[@name='fake_script']))"><xsl:value-of select="//modules/container/webobject/module[@name='information_presentation']/content/entry/seperator_row/seperator/field[@name='ie_title']/value" disable-output-escaping="yes"/></xsl:when>

					<xsl:otherwise><xsl:call-template name="display_firstpage"/></xsl:otherwise>
				</xsl:choose></xsl:variable>
			
			<div class="page" id="page1"><h1 class='entrytitle' id="notitlepage"><span><xsl:value-of select="$page_title_string"/></span></h1></div>
		</xsl:otherwise>
	</xsl:choose>	

		<table cellspacing="0" cellpadding="0" width="100%" border="0" summary="A three column list of pages.">
			<xsl:for-each select="//xml_document/modules/container/webobject/module/page[position()!=$title_page][(position() mod 3)=1]">
				<tr>
				<td  align="left" valign="top"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute>
					<img border="0" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/corner_top_left.gif</xsl:attribute></img></td>
					<td width="33%" valign="top"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><table cellspacing="0" cellpadding="5" width="100%" summary="">
					<tr><td width="100%"><xsl:call-template name="display_this_page">
						<xsl:with-param name="summary">1</xsl:with-param>
						<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
						<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
					</xsl:call-template></td></tr>
					</table></td>
<td align="right" valign="bottom"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><img border="0" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/corner_bottom_right.gif</xsl:attribute></img></td>
<td ><img src="/images/themes/1x1.gif" width="5" alt="" border="0"/></td>
					<xsl:if test="following-sibling::page[(position() mod 3)=1]">
				<td align="left" valign="top"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><img border="0" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/corner_top_left.gif</xsl:attribute></img></td>
					<td width="33%" valign="top"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><table cellspacing="0" cellpadding="5" width="100%" summary=""><tr><td>
					<xsl:call-template name="display_this_page">
						<xsl:with-param name="summary">1</xsl:with-param>
						<xsl:with-param name="identifier"><xsl:value-of select="following-sibling::page[(position() mod 3) = 1]/@identifier"/></xsl:with-param>
						<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
					</xsl:call-template>
</td></tr></table></td>
<td align="right" valign="bottom"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><img border="0" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/corner_bottom_right.gif</xsl:attribute></img></td>
<td ><img src="/images/themes/1x1.gif" width="5" alt=""  border="0"/></td>
					</xsl:if>
					<xsl:if test="following-sibling::page[(position() mod 3)=2]">
				<td align="left" valign="top"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><img border="0" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/corner_top_left.gif</xsl:attribute></img></td>
					<td width="33%" valign="top"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><table cellspacing="0" cellpadding="5" width="100%" summary=""><tr><td>
					<xsl:call-template name="display_this_page">
						<xsl:with-param name="summary">1</xsl:with-param>
						<xsl:with-param name="identifier"><xsl:value-of select="following-sibling::page[(position() mod 3) = 2]/@identifier"/></xsl:with-param>
						<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
					</xsl:call-template>
</td></tr></table></td>
<td align="right" valign="bottom"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><img border="0" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/corner_bottom_right.gif</xsl:attribute></img></td>
<td ><img src="/images/themes/1x1.gif" width="5" alt=""  border="0"/></td>
					</xsl:if>
				</tr>
				<tr><td ><img src="/images/themes/1x1.gif" alt="" width="5" border="0"/></td></tr>
			</xsl:for-each>
		</table>
	<xsl:if test="//xml_document/modules/container/webobject/module/headline">
		<xsl:call-template name='show_headlines'>
			<xsl:with-param name='cols'>3</xsl:with-param>
		</xsl:call-template>
	</xsl:if>
				<xsl:call-template name="display_modules"><!-- supply the ignore tags LATEST is a display attribute and polls is a name attribute--><xsl:with-param name="ignore" select="'[page]'"/></xsl:call-template>
			</td>
		</tr>
	</table>

	<xsl:if test="xml_document/debugging">
		<xsl:comment> debug Data </xsl:comment>
		<xsl:apply-templates select="xml_document/debugging"/>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>

