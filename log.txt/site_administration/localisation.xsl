<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:49:58 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="get_translation">
	<xsl:param name="check"/>
		<xsl:variable name="confirm"><xsl:value-of select="//xml_document/locale/localisation[@code=$check]"/></xsl:variable>
		<xsl:choose>
		<xsl:when test="$confirm=''"><xsl:value-of select="$check" disable-output-escaping="yes" /></xsl:when>
		<xsl:otherwise><xsl:value-of select="$confirm" disable-output-escaping="yes" /></xsl:otherwise>
		</xsl:choose>
</xsl:template>


</xsl:stylesheet>
