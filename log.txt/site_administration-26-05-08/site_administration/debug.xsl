<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2004/10/12 10:35:13 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
 

<xsl:template match="debug_data">
<xsl:comment> TERMINATE CLEAN </xsl:comment>
	<h3>Debug Information</h3>
	<xsl:if test="./debug">
		<table width="100%" border="0" cellspacing="1" cellpadding="3">
			<tr class="formheader">
				<td valign="top"><strong>Module</strong></td>
				<td valign="top"><strong>Fn()</strong></td>
				<td valign="top"><strong>Line</strong></td>
				<td valign="top"><strong>Command</strong></td>
				<td valign="top"><strong>#</strong></td>
			</tr>
			<tr>
				<td colspan="5" valign="top"><strong>Parameters</strong></td>
			</tr>
		<xsl:apply-templates/>
		</table>
	</xsl:if>
</xsl:template>

<xsl:template match="Errors">
	<h3>Errors</h3>
	<xsl:if test="./error">
		<xsl:apply-templates/>
	</xsl:if>
</xsl:template>

<xsl:template match="debug">
			<tr>
				<td class="TableCell" valign="top"><xsl:value-of select="@module"/></td>
				<td class="TableCell" valign="top"><xsl:value-of select="@fn"/></td>
				<td class="TableCell" valign="top"><xsl:value-of select="@line"/></td>
				<td class="TableCell" valign="top"><xsl:value-of select="@cmd"/></td>
				<td class="TableCell" valign="top"><xsl:value-of select="@parameters"/></td>
				<td width="100%" valign="top"></td>
			</tr>
			<tr>
				<td colspan="6" class="TableCell" valign="top"><pre><xsl:value-of select="."/></pre></td>
			</tr>
</xsl:template>


</xsl:stylesheet>
