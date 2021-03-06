<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2005/02/28 17:19:44 $
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
<!--
This is the first rule in our style sheet and it will get executed straight away
we will tell it to load the look and feel of the theme in.
-->
<xsl:include href="../../styles/pages/display_3_column_summary_graphical.xsl"/>

<xsl:template match="/">
	<xsl:comment> style sheet 3 columns summary graphical.xsl </xsl:comment>
	<xsl:call-template name="display_layout_structure"/>
</xsl:template>
</xsl:stylesheet>