<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:40 $
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
	<xsl:param name="header">Latest Information</xsl:param>

	
	<xsl:variable name="content">
	<xsl:for-each select="/xml_document/modules/module[@display='LATEST']">
		<table cellspacing="0" cellpadding="5" width="100%" border="0" summary="A list of the latest content published to the site located in this location and any child locations.">
				<tr><td><ul>
			<xsl:for-each select="page">
				<xsl:sort select="@identifier" order="descending"/>
				<li><a align='left'><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:value-of select="title"/></a></li>
			</xsl:for-each>
			</ul></td></tr>		
		</table>
	</xsl:for-each>
	</xsl:variable>
	
	<xsl:call-template name="display_a_table">
		<xsl:with-param name="header"><xsl:value-of select="$header" disable-output-escaping="yes"/></xsl:with-param>
		<xsl:with-param name="content"><xsl:copy-of select="$content"/></xsl:with-param>
	</xsl:call-template>
</xsl:template>

</xsl:stylesheet>