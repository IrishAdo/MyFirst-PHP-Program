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

<xsl:output method="xml" indent="yes" 
    doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"
    doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" />
	  
<xsl:template name="display_layout_structure">
<html ><xsl:attribute name="lang"><xsl:value-of select="//locale/@codex"/></xsl:attribute>
<head>
<xsl:call-template name="display_header_data"/>
<xsl:comment><![CDATA[[if IE 6]><link href="/libertas_images/themes/balmoral/ie_style.css" rel="stylesheet" type="text/css"><![endif]]]></xsl:comment>
</head>
<body>

<xsl:attribute name='id'>L<xsl:choose>
	<xsl:when test="//setting[@name='display_layout'] = '1111'">
		<xsl:choose>
			<xsl:when test="not(boolean(//container[@pos='3']))">121</xsl:when>
			<xsl:otherwise>1111</xsl:otherwise>
		</xsl:choose>
	</xsl:when>
	<xsl:otherwise><xsl:value-of select="//setting[@name='display_layout']"/></xsl:otherwise>
</xsl:choose></xsl:attribute>
<div id="pagestructure">
<div id='header' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">header</xsl:with-param></xsl:call-template></div>
<xsl:if test="boolean(//container[@pos='1'])">
<div id='position1' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></div>
</xsl:if>
<xsl:if test="boolean(//container[@pos='2'])">
<div id='position2' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></div>
</xsl:if>
<xsl:if test="boolean(//container[@pos='3'])">
<div id='position3' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></div>
</xsl:if>
<xsl:if test="boolean(//container[@pos='4'])">
<div id='position4' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></div>
</xsl:if>
<div id='footer' class='smallcell'><xsl:call-template name="show_containers"><xsl:with-param name="display_position">footer</xsl:with-param></xsl:call-template></div>

</div>

</body>
</html>

</xsl:template>

</xsl:stylesheet>
