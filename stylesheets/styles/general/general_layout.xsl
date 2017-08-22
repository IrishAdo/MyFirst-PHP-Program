<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/08/24 13:21:30 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="display_general_layout">
	<xsl:param name="show_1"><xsl:value-of select="$show_1"/></xsl:param>
	<xsl:param name="show_2"><xsl:value-of select="$show_2"/></xsl:param>
	<xsl:param name="show_3"><xsl:value-of select="$show_3"/></xsl:param>
	<xsl:param name="show_4"><xsl:value-of select="$show_4"/></xsl:param>
	<table class="bodylayouttable" border="0" cellpadding="0" cellspacing="0" summary="This table holds the possibility of holding between 1 and four columns of information">
		<xsl:choose>
			<xsl:when test="$displayLayout = '1111'">
			<tr>
				<xsl:if test="$show_1=1"><td id="position1" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td></xsl:if>
				<td id="position2" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td>
				<td id="position3" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td>
				<td id="position4" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></td>
			</tr>
			</xsl:when>
			<xsl:when test="$displayLayout = '112'">
			<tr>
				<xsl:if test="$show_1=1"><td id="position1" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td></xsl:if>
				<td id="position2" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td>
				<td id="position4" class="normalcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></td>
			</tr>
			</xsl:when>
			<xsl:when test="$displayLayout = '121'">
			<tr>
				<xsl:if test="$show_1=1"><td id="position1" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td></xsl:if>
				<td id="position2" class="normalcell" colspan="2"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td>
				<td id="position4" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></td>
			</tr>
			</xsl:when>
			<xsl:when test="$displayLayout = '211'">
			<tr>
				<td id="position1" class="normalcell" colspan="2"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td>
				<xsl:if test="$show_3=1"><td id="position3" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td></xsl:if>
				<xsl:if test="$show_4=1"><td id="position4" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></td></xsl:if>
			</tr>
			</xsl:when>
			<xsl:when test="$displayLayout = '22'">
			<tr>
				<td id="position1" class="normalcell" colspan="2"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td>
				<td id="position4" class="normalcell" colspan="2"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></td>
			</tr>
			</xsl:when>
			<xsl:when test="$displayLayout = '13'">
			<tr>
				<xsl:if test="$show_1=1"><td id="position1" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td></xsl:if>
				<td id="position4" class="bigcell" colspan="3"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></td>
			</tr>
			</xsl:when>
			<xsl:when test="$displayLayout = '31'">
			<tr>
				<td id="position1" class="bigcell" colspan="3"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></td><td class="cellsplitter"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="400" alt=""/></td>
				<xsl:if test="$show_4=1"><td id="position4" class="smallcell"><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></td></xsl:if>
			</tr>
			</xsl:when>
			<xsl:otherwise>
			<tr><td class="largecell" colspan="4" id="position1">
				<xsl:call-template name="show_containers">
					<xsl:with-param name="display_position">1</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="show_containers">
					<xsl:with-param name="display_position">2</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="show_containers">
					<xsl:with-param name="display_position">3</xsl:with-param>
				</xsl:call-template>
				<xsl:call-template name="show_containers">
					<xsl:with-param name="display_position">4</xsl:with-param>
				</xsl:call-template>
			</td></tr></xsl:otherwise>
		</xsl:choose>
	</table>
</xsl:template>

</xsl:stylesheet>