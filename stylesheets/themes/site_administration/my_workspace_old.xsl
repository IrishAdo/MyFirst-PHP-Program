<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/10/03 12:26:00 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
	version="1.0" 
	exclude-result-prefixes="rdf rss l dc admin content xsl"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rss="http://purl.org/rss/1.0/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:l="http://purl.org/rss/1.0/modules/link/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" 
	xmlns:syn="http://purl.org/rss/1.0/modules/syndication/" 
	xmlns:admin="http://webns.net/mvcb/"
> 

<xsl:template name="display_my_workspace">
	<xsl:param name="number" select="1"/>
	<table border="0" cellpadding="5" cellspacing="0" summary="This table holds some information for the digital desktop" width="100%">	
		<tr><td valign="top" class="formbackground"><table border="0" cellpadding="0" cellspacing="0" width="100%" summary="This table holds the row information for the forms">
		<xsl:if test="@label">
			<xsl:choose>
				<xsl:when test="rss or rdf:RDF">
				<tr><td><xsl:call-template name="rssChannel"/></td></tr>
				</xsl:when>
				<xsl:otherwise>
					<tr> 
				   		<td valign="top" class="formheader" id="steve"><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="@label"/></xsl:with-param></xsl:call-template></td>
						<xsl:if test="cmd">
							<td valign="top" align="right" class="TableCell">[[nbsp]]|[[nbsp]]<xsl:for-each select="cmd">
							<a>
								<xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/>admin/index.php?command=<xsl:value-of select="."/></xsl:attribute> 
								<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template>
							</a>[[nbsp]]|[[nbsp]]
							</xsl:for-each>
							</td>
						</xsl:if>
					</tr>
					<xsl:if test="label">
					<tr> 
				   		<td valign="top" class="contentpos" colspan="2"><xsl:value-of select="label"/></td>
					</tr>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		<xsl:if test="text">
		<tr>
			<td colspan="2"><ul>
			<xsl:for-each select="text">
				<li class="redbullet"><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></li>
			</xsl:for-each>
			
			</ul></td>
				</tr>
		</xsl:if>
		<xsl:if test="rss">
		<tr><td colspan="2"><xsl:apply-templates select="rss"/></td></tr>
		</xsl:if>
		<xsl:if test="rdf:RDF">
		<tr><td colspan="2"><xsl:apply-templates select="rdf:RDF"/></td></tr>
		</xsl:if>
		<xsl:for-each select="grouped">
			<tr> 
			   	<td valign="top" align="left" class="TableCell"><xsl:choose><xsl:when test="cmd"></xsl:when><xsl:otherwise><xsl:attribute name="colspan">2</xsl:attribute></xsl:otherwise></xsl:choose><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></td>
				<xsl:if test="cmd">
				<td valign="top" align="right" class="TableCell"><xsl:for-each select="cmd"><input type="button" class='bt'>
					<xsl:attribute name="onclick">javascript:window.location='<xsl:value-of select="//setting[@name='base']"/>admin/index.php?command=<xsl:value-of select="."/>'</xsl:attribute> 
					<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></xsl:attribute>
				</input></xsl:for-each></td>
				</xsl:if>
	  		</tr>
			<tr><td colspan="2" valign="top"><table cellspacing="0" cellpadding="3" border="0" width="100%">
			<xsl:for-each select="child::*">
				<xsl:if test="local-name()='title'">
					<tr><xsl:attribute name="class"><xsl:if test="(position() mod 2) = 1">TableCell_alt</xsl:if><xsl:if test="(position() mod 2) = 0">TableCell</xsl:if></xsl:attribute>
						<td><xsl:value-of select="."/></td>
						<xsl:if test="@language">
						<td><xsl:value-of select="@language"/></td>
						</xsl:if>
						<xsl:if test="@result">
						<td><xsl:value-of select="@result"/></td>
						</xsl:if>
						<xsl:choose>
						<xsl:when test="@identifier">
						<xsl:variable name="id"><xsl:value-of select="@identifier"/></xsl:variable>
							<td valign="top" align="right"><xsl:for-each select="../commands/cmd"><input type="button" class='bt'>
								<xsl:attribute name="onclick">javascript:window.location='<xsl:value-of select="//setting[@name='base']"/>admin/index.php?command=<xsl:value-of select="."/>&amp;identifier=<xsl:value-of select="$id"/>'</xsl:attribute> 
								<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></xsl:attribute>
								</input></xsl:for-each></td>
						</xsl:when>
						<xsl:otherwise><td>[[nbsp]]</td></xsl:otherwise>
						</xsl:choose>
					</tr>
					</xsl:if>
					<xsl:if test="local-name()='text'">
						<tr>
							<td><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></td>
						</tr>
					</xsl:if>
				</xsl:for-each>
			</table></td></tr>
		</xsl:for-each>
	</table></td></tr></table>
</xsl:template>



</xsl:stylesheet>
