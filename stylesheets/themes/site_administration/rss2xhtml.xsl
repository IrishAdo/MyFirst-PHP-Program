<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2005/01/11 16:24:53 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:include href="../../styles/general/rss.xsl"/>
<xsl:include href="../../styles/general/format_date.xsl"/>
<xsl:template match="/">
<xsl:for-each select="module">
<xsl:call-template name="rssChannel"/>
</xsl:for-each>
</xsl:template>


</xsl:stylesheet>