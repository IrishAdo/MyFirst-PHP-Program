<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:48:52 $
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
<xsl:include href="../../styles/lists/list_default_title_linking.xsl"/>
	 	
<!--
This is the first rule in our style sheet and it will get executed straight away
we will tell it to load the look and feel of the theme in.
-->
<xsl:template match="/">
	<xsl:comment> style sheet default_main.xsl </xsl:comment>
	<xsl:call-template name="display_layout_structure"/>
</xsl:template>
<!--
The standard layout for this theme will call the display_content_data function which will display 
the data from the xml on the screen.
-->
<xsl:template name="display_content_data">
	<xsl:comment> start Content Data </xsl:comment>
	<!-- 
		display comment in html output this is the main page formatting
		NOTE :: we are displaying the home page in a specific format
		
		Single page,
		Latest documents and Poll	
	-->
	<table cellpadding="5" cellspacing="5" border="0" width="100%" summary="">
		<tr><td><xsl:call-template name="display_modules"/></td></tr>
	</table>

	<xsl:if test="xml_document/debugging">
		<xsl:comment> debug Data </xsl:comment>
		<xsl:apply-templates select="xml_document/debugging"/>
	</xsl:if>
	
	<xsl:comment> end Content Data </xsl:comment>
</xsl:template>
</xsl:stylesheet>