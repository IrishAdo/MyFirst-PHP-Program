<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:39 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
default FAQ look and feel
-->
<xsl:template name="display_latest">
	<xsl:param name="display_more_as_text"/>
		
	<xsl:for-each select="//xml_document/modules/container/webobject/module[@display='LATEST']">
		<table cellspacing="0" cellpadding="0" width="100%" border="0" summary="This table contains a three column list of pages.">
		<tr><td colspan="3"><strong>Latest Information</strong><p></p></td></tr>		
				<xsl:for-each select="page[(position() mod 3)=1]">
				<tr>
				<td  align="left" valign="top"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute>
					<img src="/images/themes/libertas/corner_top_left.gif" border="0" alt=""/></td>
					<td><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><table cellspacing="0" cellpadding="5" width="100%" summary=""><tr><td width="100%"><a><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:value-of select="title"/></a></td></tr></table></td>
<td align="right" valign="bottom"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><img src="/images/themes/libertas/corner_bottom_right.gif" border="0" alt=""/></td>
<td ><img src="/images/themes/1x1.gif" width="5" border="0"/></td>
					<xsl:if test="following-sibling::page[(position() mod 3)=1]">
				<td align="left" valign="top"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><img src="/images/themes/libertas/corner_top_left.gif" border="0" alt=""/></td>
					<td ><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><table cellspacing="0" cellpadding="5" width="100%" summary=""><tr><td><a><xsl:attribute name="href"><xsl:value-of select="following-sibling::page[(position() mod 3) = 1]/locations/location[position()=1]"/></xsl:attribute><xsl:value-of select="following-sibling::page[(position() mod 3) = 1]/title"/></a></td></tr></table></td>
<td align="right" valign="bottom"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 67">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><img src="/images/themes/libertas/corner_bottom_right.gif" border="0" alt=""/></td>
<td ><img src="/images/themes/1x1.gif" width="5" border="0"/></td>
					</xsl:if>
					<xsl:if test="following-sibling::page[(position() mod 3)=2]">
				<td align="left" valign="top"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><img src="/images/themes/libertas/corner_top_left.gif" border="0" alt=""/></td>
					<td ><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><table cellspacing="0" cellpadding="5" width="100%" summary=""><tr><td><a><xsl:attribute name="href"><xsl:value-of select="following-sibling::page[(position() mod 3) = 2]/locations/location[position()=1]"/></xsl:attribute><xsl:value-of select="following-sibling::page[(position() mod 3) = 2]/title"/></a></td></tr></table></td>
<td align="right" valign="bottom"><xsl:attribute name="class">latest<xsl:choose>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 0">2</xsl:when>
					<xsl:when test="(round((((position() div 3) mod 3) * 100)) mod 100) = 33">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
					</xsl:choose></xsl:attribute><img src="/images/themes/libertas/corner_bottom_right.gif" border="0" alt=""/></td>
<td ><img src="/images/themes/1x1.gif" width="5" border="0"/></td>
					</xsl:if>
				</tr>
				<tr><td ><img src="/images/themes/1x1.gif" width="5" border="0"/></td></tr>
			</xsl:for-each>
		</table>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>

