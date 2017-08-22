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
	<xsl:param name="valign">top</xsl:param>
	<xsl:param name="header"></xsl:param>
	<xsl:param name="content"></xsl:param>
	<xsl:choose>
		<xsl:when test="$table_direction='top'">
			<table cellpadding="0" cellspacing="0" border="0" class="width100percent" summary="A display rich table that defines a graphical representation of a round cornered box with the title on the top and the main content on the bottom">
				<tr>
					<td class="width3px"><img src="/libertas_images/themes/1x1.gif" alt="" width="3" border="0" height="1"/></td>
					<td class="width13px"><img src="/libertas_images/themes/1x1.gif" alt="" width="13" border="0" height="1"/></td>
					<td class="width100percent"><img src="/libertas_images/themes/1x1.gif" alt="" width="150" border="0" height="1"/></td>
					<td class="width13px"><img src="/libertas_images/themes/1x1.gif" alt="" width="13" border="0" height="1"/></td>
					<td class="width3px"><img src="/libertas_images/themes/1x1.gif" alt="" width="3" border="0" height="1"/></td>
				</tr>
				<tr>
					<td class="TableHeaderRedCorner" colspan="2" rowspan="2"><img width="16" border="0" height="16" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/top_left.gif</xsl:attribute></img></td>
					<td class="TableBackground"><img src="/libertas_images/themes/1x1.gif" alt="" width="150" border="0" height="3"/></td>
					<td class="TableHeaderRedCorner" colspan="2" rowspan="2"><img width="16" border="0" height="16" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/top_right.gif</xsl:attribute></img></td>
				</tr>
				<tr>
					<td class="TableHeader" rowspan="2"><xsl:value-of select="$header" disable-output-escaping="yes"/></td>
				</tr>
				<tr>
					<td class="TableBackground"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="3"/></td>
					<td class="TableHeader"><img src="/libertas_images/themes/1x1.gif" alt="" width="13" border="0" height="3"/></td>
					<td class="TableHeader"><img src="/libertas_images/themes/1x1.gif" alt="" width="13" border="0" height="3"/></td>
					<td class="TableBackground"><img src="/libertas_images/themes/1x1.gif" alt="" width="3" border="0" height="3"/></td>
				</tr>
				<tr>
					<td colspan="5" class="TableBackground"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="3"/></td>
				</tr>
				<tr>
					<td class="TableBackgroundwidth3px"><img src="/libertas_images/themes/1x1.gif" alt="" width="3" border="0" height="3"/></td>
					<td class="tablecell"><img src="/libertas_images/themes/1x1.gif" alt="" width="13" border="0" height="3"/></td>
					<td class="tablecell" ><xsl:attribute name="valign"><xsl:value-of select="$valign"/></xsl:attribute><div class="width100percent"><xsl:copy-of select="$content" /></div></td>
					<td class="tablecell"><img src="/libertas_images/themes/1x1.gif" alt="" width="13" border="0" height="3"/></td>
					<td class="TableBackgroundwidth3px"><img src="/libertas_images/themes/1x1.gif" alt="" width="3" border="0" height="3"/></td>
				</tr>
				<tr>
					<td class="TableHeaderWhiteCorner" colspan="2" rowspan="2"><img width="16" border="0" height="16" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/bottom_left.gif</xsl:attribute></img></td>
					<td class="TableCell"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="13"/></td>
					<td class="TableHeaderWhiteCorner" colspan="2" rowspan="2"><img width="16" border="0" height="16" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/bottom_right.gif</xsl:attribute></img></td>
				</tr>
				<tr>
					<td  class="TableBackground"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="3"/></td>
				</tr>
			</table>
		</xsl:when>
		<xsl:otherwise>
			<table cellpadding="0" cellspacing="0" border="0" class="width100percent" summary="A display rich table that defines a graphical representation of a round cornered box with the title down the left hand side and the main content on the right hand side">
				<tr>
					<td class="width3px" ><img src="/libertas_images/themes/1x1.gif" alt="" width="3" border="0" height="1"/></td>
					<td class="width3px"><img src="/libertas_images/themes/1x1.gif" alt="" width="13" border="0" height="1"/></td>
					<td><img src="/libertas_images/themes/1x1.gif" alt="" width="10" border="0" height="1"/></td>
					<td class="width3px"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="1"/></td>
					<td ><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="1"/></td>
					<td class="width3px"><img src="/libertas_images/themes/1x1.gif" alt="" width="13" border="0" height="1"/></td>
					<td class="width3px"><img src="/libertas_images/themes/1x1.gif" alt="" width="3" border="0" height="1"/></td>
				</tr>
				<tr>
					<td class="TableHeaderRedCorner" colspan="2" rowspan="2"><img width="16" border="0" height="16" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/top_left.gif</xsl:attribute></img></td>
					<td class="TableBackground"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="3"/></td>
					<td class="TableBackgroundwidth3px" rowspan="2"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="16"/></td>
					<td class="TableBackground"><img src="/libertas_images/themes/1x1.gif" alt="" width="170" border="0" height="3"/></td>
					<td class="TableHeaderWhiteCorner" colspan="2" rowspan="2"><img width="16" border="0" height="16" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/top_right.gif</xsl:attribute></img></td>
				</tr>
				<tr>
					<td class="TableHeaderleft"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="13"/></td>
					<td class="TableCell"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="13"/></td>
				</tr>
				<tr>
					<td class="TableBackgroundwidth3px" ><img src="/libertas_images/themes/1x1.gif" alt="" width="3" border="0" height="3"/></td>
					<td colspan="2" class="TableHeaderleft">
					<xsl:choose>
						<xsl:when test="$browser_type!='IE'"><xsl:call-template name="cut_string"><xsl:with-param name="my_string"><xsl:value-of select="$header" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:when>
						<xsl:otherwise><xsl:value-of select="$header" disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></td>

					<td class="TableBackgroundwidth3px"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="2"/></td>
					<td class="TableCell" valign="top"><div class="width100percent"><xsl:copy-of select="$content" /></div></td>
					<td class="TableCell"><img src="/libertas_images/themes/1x1.gif" alt="" width="13" border="0" height="2"/></td>
					<td class="TableBackgroundwidth3px"><img src="/libertas_images/themes/1x1.gif" alt="" width="3" border="0" height="2"/></td>
				</tr>
				<tr>
					<td class="TableHeaderRedCorner" colspan="2" rowspan="2"><img width="16" border="0" height="16" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/bottom_left.gif</xsl:attribute></img></td>
					<td class="TableHeaderleft"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="13"/></td>
					<td class="TableBackgroundwidth3px" rowspan="2"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="16"/></td>
					<td class="TableCell"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="13"/></td>
					<td class="TableHeaderWhiteCorner" colspan="2" rowspan="2"><img width="16" border="0" height="16" alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/bottom_right.gif</xsl:attribute></img></td>
				</tr>
				<tr>
					<td  class="TableBackground"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="3"/></td>
					<td  class="TableBackground"><img src="/libertas_images/themes/1x1.gif" alt="" width="2" border="0" height="3"/></td>
				</tr>
			</table>
		</xsl:otherwise>
	</xsl:choose>
	<img src="/libertas_images/themes/1x1.gif" width="10" height="10" alt=""/>
</xsl:template>

<xsl:template name="cut_string">
	<xsl:param name="my_string"></xsl:param>
	<xsl:if test="string-length($my_string)!=0">
		<xsl:value-of select="substring($my_string,1,1)"/><br/>
		<xsl:call-template name="cut_string"><xsl:with-param name="my_string"><xsl:value-of select="substring($my_string,2)"/></xsl:with-param></xsl:call-template>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>