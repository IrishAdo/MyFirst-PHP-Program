<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:28 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
	 	
<xsl:template name="display_footer_data">
	<xsl:if test="//modules/module/footer!='' and //modules/module/footer!='__NOT_FOUND__'">
	<table width="100%" border='0' summary="this table holds the information telling the visitor that the system is powered by Libertas Solutions">
		<tr><td class="footer"><xsl:value-of select="//modules/module/footer" disable-output-escaping="yes"/></td></tr>
		<tr><td style="text-align:right"><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">CLIENT_FOOTER_</xsl:with-param></xsl:call-template></td></tr>
	</table>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>