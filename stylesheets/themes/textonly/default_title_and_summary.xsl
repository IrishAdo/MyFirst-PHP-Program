<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:50:18 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
Load the required xsl style sheets that will be used to produce the finished page
-->
<xsl:include href="include_2_column.xsl"/>
<xsl:include href="../../styles/pages/display_title_plus_summary.xsl"/>
<!--
This is the first rule in our style sheet and it will get executed straight away
we will tell it to load the look and feel of the theme in.
-->
<xsl:template match="/">
	<xsl:comment> style sheet display_title_plus_summary.xsl </xsl:comment>
	<xsl:call-template name="display_layout_structure"/>
</xsl:template>

</xsl:stylesheet>
