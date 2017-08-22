<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:33 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
	 	
<xsl:template name="display_bobby_rating">
<xsl:if test="//xml_document/modules/module[@name='system_prefs']/setting[@name='sp_bobby_level']!='None'">
<p class="bobby"><img width="88" height="31" border="0" alt="Bobby Compliant Site">
	<xsl:attribute name="src">/libertas_images/icons/bobby/<xsl:choose>
		<xsl:when test="//xml_document/modules/module[@name='system_prefs']/setting[@name='sp_bobby_level']='Bobby Compliant'">bobby_approved.gif</xsl:when>
		<xsl:when test="//xml_document/modules/module[@name='system_prefs']/setting[@name='sp_bobby_level']='Section 508'">bobby_approved_508.gif</xsl:when>
		<xsl:when test="//xml_document/modules/module[@name='system_prefs']/setting[@name='sp_bobby_level']='Bobby A Rating'">bobby_approved_a.gif</xsl:when>
		<xsl:when test="//xml_document/modules/module[@name='system_prefs']/setting[@name='sp_bobby_level']='Bobby AA Rating'">bobby_approved_aa.gif</xsl:when>
		<xsl:when test="//xml_document/modules/module[@name='system_prefs']/setting[@name='sp_bobby_level']='Bobby AAA Rating'">bobby_approved_aaa.gif</xsl:when>
	</xsl:choose></xsl:attribute>
</img></p>
</xsl:if>
</xsl:template>

</xsl:stylesheet>