<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:22:06 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:template name="display_a_table">
	<xsl:param name="header"></xsl:param>
	<xsl:param name="content"></xsl:param>
	<xsl:choose>
		<xsl:when test="$table_direction='top' or $browser_type!='IE'">
			<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr>
			<td class="TableBackground"><table width="100%" cellpadding="0" cellspacing="1" border="0">
				<tr>
					<td><table width="100%" cellpadding="5" cellspacing="0" border="0">
					<tr><td class="TableHeader"><xsl:value-of select="$header" disable-output-escaping="yes"/></td></tr>
					</table></td>
				</tr>
				<tr>
					<td class="TableBackground"><table width="100%" border="0" cellspacing="0" cellpadding="3" summary="This table contains documents that have been mirrored from another location.">
				  		<tr><td class="TableCell"><xsl:copy-of select="$content" /></td></tr>
					</table></td>
				</tr>
			</table></td>
		</tr></table>
		</xsl:when>
		<xsl:otherwise>
			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td width="3" ><img src="/images/themes/1x1.gif" width="3" border="0" height="1"/></td>
					<td width="13"><img src="/images/themes/1x1.gif" width="13" border="0" height="1"/></td>
					<td><img src="/images/themes/1x1.gif" width="2" border="0" height="1"/></td>
					<td width="2"><img src="/images/themes/1x1.gif" width="2" border="0" height="1"/></td>
					<td ></td>
					<td width="13"><img src="/images/themes/1x1.gif" width="13" border="0" height="1"/></td>
					<td width="3"><img src="/images/themes/1x1.gif" width="3" border="0" height="1"/></td>
				</tr>
				<tr>
					<td class="TableHeaderleft" width="16" colspan="2" rowspan="2"><img src="/images/themes/top_left.gif" align="left" width="16" border="0" height="16"/></td>
					<td class="TableBackground"><img src="/images/themes/1x1.gif" width="2" border="0" height="3"/></td>
					<td class="TableBackground" width="16" rowspan="2"><img src="/images/themes/1x1.gif" width="2" border="0" height="16"/></td>
					<td class="TableBackground"><img src="/images/themes/1x1.gif" width="130" border="0" height="3"/></td>
					<td bgcolor="#ffffff" width="16" colspan="2" rowspan="2"><img src="/images/themes/top_right.gif" width="16" border="0" height="16"/></td>
				</tr>
				<tr>
					<td class="TableHeaderleft"><img src="/images/themes/1x1.gif" width="2" border="0" height="13"/></td>
					<td class="TableCell"><img src="/images/themes/1x1.gif" width="2" border="0" height="13"/></td>
				</tr>
				<tr>
					<td width="3"  class="TableBackground"><img src="/images/themes/1x1.gif" width="2" border="0" height="3"/></td>
					<td colspan="2" class="TableHeaderleft"><xsl:value-of select="$header" disable-output-escaping="yes"/></td>
					<td class="TableBackground" width="2"><img src="/images/themes/1x1.gif" width="2" border="0" height="2"/></td>
					<td class="TableCell" width="100%" valign="top"><table border="0">
						<tr><td valign="top"><xsl:copy-of select="$content" /></td></tr>
					</table></td>
					<td class="TableCell" width="13"><img src="/images/themes/1x1.gif" width="13" border="0" height="2"/></td>
					<td class="TableBackground" width="3"><img src="/images/themes/1x1.gif" width="3" border="0" height="2"/></td>
				</tr>
				<tr>
					<td width="16" class="TableHeaderleft" colspan="2" rowspan="2"><img src="/images/themes/bottom_left.gif" align="left" width="16" border="0" height="16"/></td>
					<td class="TableHeaderleft"><img src="/images/themes/1x1.gif" width="2" border="0" height="13"/></td>
					<td  class="TableBackground" width="16" rowspan="2"><img src="/images/themes/1x1.gif" width="2" border="0" height="16"/></td>
					<td class="TableCell"><img src="/images/themes/1x1.gif" width="2" border="0" height="13"/></td>
					<td width="16" colspan="2" rowspan="2"><img src="/images/themes/bottom_right.gif" width="16" border="0" height="16"/></td>
				</tr>
				<tr>
					<td  class="TableBackground"><img src="/images/themes/1x1.gif" width="2" border="0" height="3"/></td>
					<td  class="TableBackground"><img src="/images/themes/1x1.gif" width="2" border="0" height="3"/></td>
				</tr>
			</table>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>