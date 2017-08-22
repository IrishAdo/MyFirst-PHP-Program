<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.1 $
- Modified $Date: 2005/02/28 17:16:02 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
Load the required xsl style sheets that will be used to produce the finished page
-->
<xsl:include href="include.xsl"/>
<xsl:include href="../../styles/lists/list_default_title_linking.xsl"/>

	 	
<!--
This is the first rule in our style sheet and it will get executed straight away
we will tell it to load the look and feel of the theme in.
-->
<xsl:template match="/">
	<xsl:comment> style sheet default_home.xsl  loading styles/lists/list_default_title_linking.xsl</xsl:comment>
	<xsl:call-template name="display_layout_structure"/>
</xsl:template>
<!--
The standard layout for this theme will call the display_content_data function which will display 
the data from the xml on the screen.
-->
</xsl:stylesheet>