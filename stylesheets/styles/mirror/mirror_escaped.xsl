<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:57 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:template name="display_mirror">
	<xsl:param name="header"><xsl:value-of select="//menu[url=//xml_document/modules/module[@name='mirror']/menulocation]/label"/></xsl:param>
	<xsl:param name="display_date">1</xsl:param>
	<xsl:variable name="content">
		&lt;table width="100%" border="0" cellspacing="0" cellpadding="3" summary="This table contains documents that have been mirrored from another location."&gt;
			<xsl:for-each select="//xml_document/modules/module[@name='mirror']/module/page[position()!=1]">
		  		&lt;tr&gt;&lt;td class="TableCell"&gt;
				<xsl:choose>
				<xsl:when test="$display_date=1">
					<xsl:value-of select="substring-before(metadata/date[@refinement='publish'],' ')"/>
					&lt;br/&gt;
					&lt;a class="news" href="<xsl:value-of select="locations/location[position()=1]"/>"><xsl:value-of select="title"/>&lt;/a&gt;
					<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
					&lt;br/&gt;<xsl:value-of select="summary"/>&lt;hr/&gt;
				</xsl:when>
				<xsl:otherwise>
				&lt;p&gt;
				&lt;strong&gt;<xsl:value-of select="title"/>&lt;/strong&gt;&lt;br/&gt;
				<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
				<xsl:value-of select="summary"/>
				&lt;a class="news" href="<xsl:value-of select="locations/location[position()=1]"/>"&gt;more ...&lt;/a&gt;
				&lt;/p&gt;
				</xsl:otherwise>
				</xsl:choose>
				&lt;/td&gt;&lt;/tr&gt;
			</xsl:for-each>
		&lt;/table&gt;
	</xsl:variable>
	<xsl:call-template name="display_a_table">
		<xsl:with-param name="header"><xsl:value-of select="$header" disable-output-escaping="yes"/></xsl:with-param>
		<xsl:with-param name="content"><xsl:value-of select="$content" disable-output-escaping="yes"/></xsl:with-param>
	</xsl:call-template>
	
</xsl:template>

</xsl:stylesheet>