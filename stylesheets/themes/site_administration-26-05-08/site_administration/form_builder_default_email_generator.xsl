<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2005/01/11 16:28:00 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="escapequotes"><xsl:param name="str"></xsl:param><xsl:variable name="replace">&amp;amp;quot;</xsl:variable><xsl:variable name="replace1">"</xsl:variable><xsl:variable name="replace2">&amp;quot;</xsl:variable><xsl:variable name="replace3">&quot;</xsl:variable><xsl:choose>
		<xsl:when test="contains($str,$replace)"><xsl:value-of select="substring-before($str,$replace)"/>[[quote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace1)"><xsl:value-of select="substring-before($str,$replace1)"/>[[quote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace1)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace2)"><xsl:value-of select="substring-before($str,$replace2)"/>[[quote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace2)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace3)"><xsl:value-of select="substring-before($str,$replace3)"/>[[quote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace3)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:otherwise><xsl:value-of select="$str"/></xsl:otherwise>
</xsl:choose></xsl:template>


<xsl:template match="/"><xsl:for-each select="//seperator_row/seperator/*"><xsl:variable name="field"><xsl:value-of select="@name"/></xsl:variable><xsl:if test="not(local-name()='input' and @type='hidden')"><xsl:value-of select="@label"/>::[[returns]]<xsl:for-each select="//form_submission/form_data/field[@name=$field]"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose>[[returns]]</xsl:for-each>[[returns]]</xsl:if></xsl:for-each></xsl:template>

</xsl:stylesheet>