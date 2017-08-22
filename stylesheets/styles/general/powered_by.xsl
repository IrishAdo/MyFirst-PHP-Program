<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.6 $
- Modified $Date: 2005/02/09 12:12:22 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
	 	
<xsl:template name="display_power_by">
	<xsl:param name="type">old1</xsl:param>
	<xsl:choose>
		<xsl:when test="$type='old'">
			<table width="100%" border='0' summary="This table holds the information telling the visitor that the system is powered by Libertas Solutions">
				<tr>
					<td class="poweredbytext" align="right"><a  href="http://www.libertas-solutions.co.uk/solutions/web-accessibility/" title="U DO Accessible Web Content Management Software from Libertas Solutions"><img width="18" height="14" src="/libertas_images/themes/site_administration/title_bullet.gif" alt="Accessible Web Content Management Software from Libertas Solutions CMS"></img></a>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_POWERED_BY'"/></xsl:call-template></td>
					<td class="poweredbylink" ><a class="poweredbylink"><xsl:attribute name="href">http://www.libertas-solutions.com/</xsl:attribute><xsl:attribute name="title">Site managed using Libertas Solutions Web Content Management Software</xsl:attribute>Libertas Solutions - Content Management Software</a></td>
				</tr>
			</table>
		</xsl:when>
		<xsl:when test="$type='pda'">
			<div id='powerby' class="poweredbytext"><a  href="http://www.libertas-solutions.co.uk/solutions/web-accessibility/" title="U DO - Accessible Web Content Management Software from Libertas Solutions"><img width="18" height="14" src="/libertas_images/themes/site_administration/title_bullet.gif" alt="Accessible Web Content Management Software from Libertas Solutions CMS"></img></a>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_POWERED_BY'"/></xsl:call-template>[[nbsp]]<a  href="http://www.libertas-solutions.com/" title="Site managed using Libertas Solutions Web Content Management Software">Libertas Solutions CMS</a></div>
		</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="//licence/product[@type='ECMS']">
					<div id='powerby' class="poweredbytext">
						Content Management with<a href="http://www.go-udo.com"> U DO</a> from Libertas Solutions<a href="http://www.libertas-solutions.com"> Northern Ireland Web Design</a>
						</div>
				</xsl:when>
				<xsl:otherwise>
					<div id='powerby' class="poweredbytext">
						Content Management with<a href="http://www.go-udo.com">U DO</a><a href="http://www.libertas-solutions.com">Web Design Northern Ireland</a><br/><a  href="http://www.libertas-solutions.com/" title="U DO Libertas Solutions  browser based Web Content Management Software"><xsl:if test="//setting[@name='powerby_in_new_window']='Yes'">
							<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
						</xsl:if>Libertas Solutions</a></div>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:if test="$type!='pda'">
		<xsl:call-template name="display_wai_footer_links"/>
	</xsl:if>
	<xsl:if test="/xml_document/debugging">
		<div><xsl:apply-templates select="/xml_document/debugging"/></div>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>