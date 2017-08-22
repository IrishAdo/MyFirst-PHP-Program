<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/10/13 09:47:20 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
 
<xsl:template name="debug_data">
	<h3>Debug Information</h3>
	<xsl:if test="//debug">
		<xsl:apply-templates select="//debug"/>
	</xsl:if>
</xsl:template>

<xsl:template match="debugging">
	<xsl:call-template name="debug_data"/>
</xsl:template>

<xsl:template match="Errors">
	<h3>Errors</h3>
	<xsl:if test="./error">
		<xsl:apply-templates/>
	</xsl:if>
</xsl:template>

<xsl:template match="debug">
				<div align='left' style='padding-left:10px;'>
					<hr width="90%"/>
					<strong>Module :: </strong> <xsl:value-of select="@module"/><br/>
					<strong>timestamp :: </strong> <xsl:value-of select="@time"/><br/>
					<strong>Fn() :: </strong> <xsl:value-of select="@fn"/><br/>
					<strong>Line ::</strong> <xsl:value-of select="@line"/><br/>
					<strong>Command :: </strong> <xsl:value-of select="@cmd"/><br/>
					<strong># Parameters</strong> <xsl:value-of select="@parameters"/><br/>
					<xsl:value-of select="."/>
				</div>
</xsl:template>
<!--
<xsl:template name="debug_data">
	<h3>Debug Information</h3>
	<xsl:if test="//debug">
	<table width="100%" border="0" cellspacing="1" cellpadding="0" summary="debug information"><tr><td class="formbackground">
		<table width="100%" border="0" cellspacing="1" cellpadding="3" sumamry="debug info">
			<tr class="formheader">
				<td><strong>Module</strong></td>
				<td><strong>Fn()</strong></td>
				<td><strong>Line</strong></td>
				<td><strong>Command</strong></td>
				<td><strong>#</strong></td>
				<td><strong>Parameters</strong></td>
			</tr>
		<xsl:apply-templates select="//debug"/>
		</table></td></tr></table>
	</xsl:if>
</xsl:template>

<xsl:template match="Errors">
	<h3>Errors</h3>
	<xsl:if test="./error">
		<xsl:apply-templates/>
	</xsl:if>
</xsl:template>

<xsl:template match="debugging">
	<xsl:call-template name="debug_data"/>
</xsl:template>

<xsl:template match="debug">
			<tr>
				<td class="TableCell"><xsl:value-of select="@module"/></td>
				<td class="TableCell"><xsl:value-of select="@fn"/></td>
				<td class="TableCell"><xsl:value-of select="@line"/></td>
				<td class="TableCell"><xsl:value-of select="@cmd"/></td>
				<td class="TableCell"><xsl:value-of select="@parameters"/></td>
				<td class="TableCell"><xsl:value-of select="."/></td>
			</tr>
</xsl:template>

-->
</xsl:stylesheet>
