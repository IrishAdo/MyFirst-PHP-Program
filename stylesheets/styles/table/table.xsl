<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/08/24 13:22:05 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:template name="display_a_table">
	<xsl:param name="align"></xsl:param>
	<xsl:param name="header"></xsl:param>
	<xsl:param name="content"></xsl:param>
	<table width="100%" cellpadding="0" cellspacing="0" border="0" summary="A display table that defines a representation of a table with the title in the first row and the main content on the second">
		<xsl:if test="align=''"><xsl:attribute name="align"><xsl:choose>
			<xsl:when test="$align='alignleft'">left</xsl:when>
			<xsl:when test="$align='aligncenter'">center</xsl:when>
			<xsl:when test="$align='alignright'">right</xsl:when>
			<xsl:otherwise><xsl:value-of select="$align"/></xsl:otherwise>
		</xsl:choose></xsl:attribute></xsl:if>
		<tr>
			<td class="TableBackground"><table width="100%" cellpadding="0" cellspacing="1" border="0" summary="">
				<tr>
					<td><table width="100%" cellpadding="5" cellspacing="0" border="0" summary="">
					<tr><td class="TableHeader"><xsl:value-of select="$header" disable-output-escaping="yes"/></td></tr>
					</table></td>
				</tr>
				<tr>
					<td class="TableBackground"><table width="100%" border="0" cellspacing="0" cellpadding="0" summary="">
				  		<tr><td class="TableCell"><xsl:copy-of select="$content" /></td></tr>
					</table></td>
				</tr>
			</table></td>
		</tr>
	</table>
</xsl:template>

</xsl:stylesheet>