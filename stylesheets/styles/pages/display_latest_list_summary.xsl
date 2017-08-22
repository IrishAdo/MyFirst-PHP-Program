<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/09/01 10:55:51 $
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
	<xsl:param name="show_label">1</xsl:param>
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="display_more_as_text"/>
	<div  class="latestpages">
	<xsl:if test="$show_label"><h1 class="entrytitle"><span><xsl:value-of select="label"/></span></h1></xsl:if>
	<xsl:choose>
		<xsl:when test="@name='presentation'">
			<ul>
			<xsl:for-each select="page">
				<xsl:sort select="@identifier" order="descending"/>
				<xsl:variable name="url_to_find"><xsl:value-of select="locations/location[position()=1]/@url"/></xsl:variable>
				<li><a>
				<xsl:attribute name="class"><xsl:value-of select="$uses_class"/></xsl:attribute>
				<xsl:attribute name="href"><xsl:choose>
				<xsl:when test="//menu[@url=$url_to_find]"><xsl:value-of select="locations/location[position()=1]/@url" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="locations/location[position()=1]" disable-output-escaping="yes"/></xsl:otherwise>
				</xsl:choose></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a>
				<xsl:if test="summary">
					<div class='latestsummary'><xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="summary" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></div>
				</xsl:if></li>
			</xsl:for-each>
			</ul>
		</xsl:when>
		<xsl:when test="@name='vehicle'">
		<table cellspacing="0" cellpadding="0" width="100%" border="0" summary="A list of the latest content published to the site located in this location and any child locations.">
			<xsl:for-each select="vehicle">
		    	<tr><td colspan="2"><strong>Latest Vehicle</strong></td></tr>		
						<tr>
				<td width="80" valign="top"><xsl:if test="image_main[@exists=1]"><img border="0" width="80"><xsl:attribute name="src"><xsl:value-of select="image_thumbnail"/></xsl:attribute></img></xsl:if></td>
							<td valign="top">
								<xsl:if test="licence_plate!=''"><strong>Licence Plate : </strong><xsl:value-of select="licence_plate"/><br /></xsl:if>
								<xsl:if test="year!=''"><strong>Year : </strong><xsl:value-of select="year"/><br /></xsl:if>
								<xsl:if test="manufacturer_description!=''"><strong>Manufacturer : </strong><xsl:value-of select="manufacturer_description"/><br /></xsl:if>
								<xsl:if test="model_description!=''"><strong>Model : </strong><xsl:value-of select="model_description"/><br /></xsl:if>
								<a><xsl:attribute name="href">?command=VEHICLE_DISPLAY&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute>View more details</a>								
							</td>
			</tr>
		    </xsl:for-each>
		</table>
		</xsl:when>
		</xsl:choose>
	</div>
</xsl:template>

</xsl:stylesheet>