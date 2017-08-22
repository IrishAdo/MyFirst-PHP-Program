<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2004/09/11 10:07:06 $
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
				<div id='position1'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></div>
				<div id='position2'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></div>
				<div id='position3'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></div>
				<div id='position4'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:when test="$displayLayout = '112'">
				<div id='position3'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
				<div id='position1'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></div>
				<div id='position2'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:when test="$displayLayout = '121'">
				<div id='position1'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></div>
				<div id='position2'>
				<xsl:if test="$show_4='1'">
				<table cellspacing="0" cellpadding="0" id="position4" summary="Content floating on right hand side of the screen">
				<tr><td><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></td></tr></table>
				</xsl:if>
				<xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template>
				</div>
			</xsl:when>
			<xsl:when test="$displayLayout = '211'">
				<div id='position1'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></div>
				<div id='position3'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></div>
				<div id='position4'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:when test="$displayLayout = '22'">
				<div id='position1'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></div>
				<div id='position3'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:when test="$displayLayout = '13'">
				<div id='position1'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></div>
				<div id='position2'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:when test="$displayLayout = '31'">
				<div id='position1'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></div>
				<div id='position4'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
			</xsl:when>
			<xsl:otherwise><div id='position1'>
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
		<div id='header'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">header</xsl:with-param></xsl:call-template></div>
		<div id='footer'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">footer</xsl:with-param></xsl:call-template></div>
		<xsl:call-template name="display_power_by"><xsl:with-param name="type">new</xsl:with-param></xsl:call-template>
	</div>
</xsl:template>

</xsl:stylesheet>