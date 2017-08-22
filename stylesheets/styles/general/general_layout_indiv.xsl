<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2004/08/25 11:59:45 $
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

	<div id='contentcolumns'>
		<xsl:choose>
			<xsl:when test="$displayLayout = '1111'">
				<xsl:if test="$show_1=1"><div id='position1' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></div></xsl:if>
				<div id='position2' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></div>
				<div id='position3' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></div>
				<div id='position4' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:when test="$displayLayout = '112'">
				<xsl:if test="$show_1=1"><div id='position1' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></div></xsl:if>
				<div id='position2' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></div>
				<div id='position4' class='normalcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:when test="$displayLayout = '121'">
				<div id='position2' class='normalcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></div>
				<xsl:if test="$show_1=1"><div id='position1' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></div></xsl:if>
				<div id='position4' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:when test="$displayLayout = '211'">
				<div id='position1' class='normalcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></div>
				<xsl:if test="$show_3=1" class='smallcell'><div id='position3'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></div></xsl:if>
				<xsl:if test="$show_4=1" class='smallcell'><div id='position4'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div></xsl:if>
			</xsl:when>
			<xsl:when test="$displayLayout = '22'">
				<div id='position1' class='normalcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></div>
				<div id='position3' class='normalcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:when test="$displayLayout = '13'">
				<xsl:if test="$show_1=1"><div id='position1' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></div></xsl:if>
				<div id='position2' class='bigcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:when test="$displayLayout = '31'">
				<div id='position1' class='bigcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></div>
				<div id='position4' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:otherwise>
			<div id='position1' class='largecell'>
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
			</div></xsl:otherwise>
		</xsl:choose>
	</div>
</xsl:template>

</xsl:stylesheet>