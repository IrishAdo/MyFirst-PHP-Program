<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="get_translation">
	<xsl:param name="check"/>
	<xsl:param name="maxlen">-1</xsl:param>
	<xsl:if test="$check!='LOCALE_DEFAULT_STRING' or ($check='LOCALE_DEFAULT_STRING' and //setting[@name='sp_wai_forms']!='NO')">
		<xsl:variable name="display_codex">0</xsl:variable>
		<xsl:variable name="confirm"><xsl:value-of select="//xml_document/locale/localisation[@code=$check]"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="$confirm=''">
				<xsl:choose>
					<xsl:when test="$maxlen!=-1">
						<xsl:choose>
							<xsl:when test="string-length($check) > $maxlen"><xsl:value-of select="substring($check,0,$maxlen)" disable-output-escaping="yes" /> ...</xsl:when>
							<xsl:otherwise><xsl:value-of select="$check" disable-output-escaping="yes" /></xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise>
						<xsl:value-of select="$check" disable-output-escaping="yes" />
					</xsl:otherwise>
				</xsl:choose>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="$display_codex=1"><xsl:value-of select="//xml_document/locale/@codex"/> :: </xsl:if>
				<xsl:choose>
					<xsl:when test="$maxlen!=-1"><xsl:value-of select="substring($confirm,0,$maxlen)" disable-output-escaping="yes" /></xsl:when>
					<xsl:otherwise><xsl:value-of select="$confirm" disable-output-escaping="yes" /></xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</xsl:if>
</xsl:template>


</xsl:stylesheet>
