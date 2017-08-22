<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/09/06 16:49:55 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template match="/"><xsl:for-each select="//form_submission/form_data/field">Array("<xsl:call-template name="get_label">
			<xsl:with-param name="field"><xsl:value-of select="@name"/></xsl:with-param>
		</xsl:call-template>","<xsl:value-of select="."/>")<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each></xsl:template>

<xsl:template name="get_label">
	<xsl:param name="field"></xsl:param>
	<xsl:for-each select="//form_submission/form_structure/seperator_row/seperator">
			<xsl:for-each select="child::*">
				<xsl:if test="@name=$field"><xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="@label"/></xsl:with-param></xsl:call-template></xsl:if>
			</xsl:for-each>
	</xsl:for-each>
</xsl:template>

<xsl:template name="escapequotes">
	<xsl:param name="str"></xsl:param>
	<xsl:variable name="replace">&amp;amp;quot;</xsl:variable>
	<xsl:variable name="replace1">"</xsl:variable>
	<xsl:variable name="replace2">&amp;quot;</xsl:variable>
	<xsl:variable name="replace3">&quot;</xsl:variable>
	<xsl:variable name="replace4">&#34;</xsl:variable>
	<xsl:variable name="replace5">&amp;#34;</xsl:variable>
	<xsl:variable name="replace6">&amp;amp;#34;</xsl:variable>
	<xsl:choose>
		<xsl:when test="contains($str,$replace)"><xsl:value-of select="substring-before($str,$replace)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace1)"><xsl:value-of select="substring-before($str,$replace1)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace1)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace2)"><xsl:value-of select="substring-before($str,$replace2)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace2)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace3)"><xsl:value-of select="substring-before($str,$replace3)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace3)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace4)"><xsl:value-of select="substring-before($str,$replace4)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace4)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace5)"><xsl:value-of select="substring-before($str,$replace5)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace5)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace6)"><xsl:value-of select="substring-before($str,$replace6)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace6)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:otherwise><xsl:value-of select="$str"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>
