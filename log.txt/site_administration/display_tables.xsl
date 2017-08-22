<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:49:49 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="display_table">
	<table border="0" cellpadding="0" cellspacing="0" summary="This table holds a form">	
		<tr><td valign="top" class="formbackground"><table border="0" cellpadding="3" cellspacing="0" summary="This table holds the row information for the forms">
		<tr> 
		   	<td valign="top" colspan="2" class="formheader"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="table/@label"/></xsl:call-template></td>
  		</tr>
		<xsl:for-each select="table/child::*">
			<xsl:choose><xsl:when test="local-name()='row'">
				<xsl:if test=".!=''">
				<tr><xsl:attribute name="class">TableCell</xsl:attribute> 
					<xsl:if test="@label">
				   	<td valign="top" align="right"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template> :: </td>
					</xsl:if>
					<td valign="top" align="left"><xsl:if test="not(@label)"><xsl:attribute name="colspan">2</xsl:attribute></xsl:if><xsl:choose>
						<xsl:when test=".!=''"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></xsl:when>
						<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_UNDEFINED'"/></xsl:call-template></xsl:otherwise>
					</xsl:choose></td>
	  			</tr>
				</xsl:if>
			</xsl:when><xsl:otherwise>
				<tr><xsl:attribute name="class">TableHeader</xsl:attribute> 
					<td valign="top" colspan="2" align="left"><xsl:choose>
						<xsl:when test=".!=''"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></xsl:when>
						<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_UNDEFINED'"/></xsl:call-template></xsl:otherwise>
					</xsl:choose></td>
	  			</tr>
			</xsl:otherwise></xsl:choose>
		</xsl:for-each>
		</table></td></tr></table>
</xsl:template>


</xsl:stylesheet>
