<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2005/01/11 16:24:12 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="replace_string">
	<xsl:param name="str_value"></xsl:param>
	<xsl:param name="find">&amp;#39;</xsl:param>
	<xsl:param name="replace_with">[[pos]]</xsl:param>
	<xsl:choose>
		<xsl:when test="contains($str_value,$find)"><xsl:call-template name="replace_string">
			<xsl:with-param name="str_value"><xsl:value-of select="substring-before($str_value,$find)"/><xsl:value-of select="$replace_with"/><xsl:value-of select="substring-after($str_value,$find)"/></xsl:with-param>
			<xsl:with-param name="find"><xsl:value-of select="$find"/></xsl:with-param>
			<xsl:with-param name="replace_with"><xsl:value-of select="$replace_with"/></xsl:with-param>
		</xsl:call-template></xsl:when>
		<xsl:otherwise><xsl:value-of select="$str_value"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="print">
	<xsl:param name="str_value"></xsl:param>
	<xsl:call-template name="replace_string">
	<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
	<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
	<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
	<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
	<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
		<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
			<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
				<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
					<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
						<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
							<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
								<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
									<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
										<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
											<xsl:with-param name="str_value"><xsl:value-of select="$str_value" disable-output-escaping="yes"/></xsl:with-param>
											<xsl:with-param name="find">&amp;amp;</xsl:with-param><xsl:with-param name="replace_with">&amp;</xsl:with-param>
										</xsl:call-template></xsl:with-param>
									<xsl:with-param name="find">&amp;#8230;</xsl:with-param><xsl:with-param name="replace_with">&#8230;</xsl:with-param></xsl:call-template></xsl:with-param>
								<xsl:with-param name="find">&amp;#163;</xsl:with-param><xsl:with-param name="replace_with">[[pound]]</xsl:with-param></xsl:call-template></xsl:with-param>
							<xsl:with-param name="find">&amp;#153;</xsl:with-param><xsl:with-param name="replace_with">&#8482;</xsl:with-param></xsl:call-template></xsl:with-param>
						<xsl:with-param name="find">&amp;#8482;</xsl:with-param><xsl:with-param name="replace_with">&#8482;</xsl:with-param></xsl:call-template></xsl:with-param>
					<xsl:with-param name="find">&amp;#169;</xsl:with-param><xsl:with-param name="replace_with">&#169;</xsl:with-param></xsl:call-template></xsl:with-param>
				<xsl:with-param name="find">&amp;#174;</xsl:with-param><xsl:with-param name="replace_with">&#174;</xsl:with-param></xsl:call-template></xsl:with-param>
			<xsl:with-param name="find">&amp;quot;</xsl:with-param><xsl:with-param name="replace_with">[[quot]]</xsl:with-param></xsl:call-template></xsl:with-param>
		<xsl:with-param name="find">&amp;pound;</xsl:with-param><xsl:with-param name="replace_with">[[pound]]</xsl:with-param></xsl:call-template></xsl:with-param>
		<xsl:with-param name="find">£</xsl:with-param><xsl:with-param name="replace_with">[[pound]]</xsl:with-param></xsl:call-template></xsl:with-param>
		<xsl:with-param name="find">€</xsl:with-param><xsl:with-param name="replace_with">[[euro]]</xsl:with-param></xsl:call-template></xsl:with-param>
	<xsl:with-param name="find">\"</xsl:with-param><xsl:with-param name="replace_with">[[quot]]</xsl:with-param></xsl:call-template></xsl:with-param>
	<xsl:with-param name="find">"</xsl:with-param><xsl:with-param name="replace_with">[[quot]]</xsl:with-param></xsl:call-template></xsl:with-param>
	<xsl:with-param name="find">'</xsl:with-param><xsl:with-param name="replace_with">[[apos]]</xsl:with-param></xsl:call-template></xsl:with-param>
	</xsl:call-template>
</xsl:template>

<xsl:template name="print2">
	<xsl:param name="str_value"></xsl:param>
	<xsl:call-template name="replace_string">
	<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
	<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
	<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
	<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
	<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
		<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
			<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
				<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
					<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
						<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
							<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
								<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
									<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
										<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
											<xsl:with-param name="str_value"><xsl:value-of select="$str_value" disable-output-escaping="yes"/></xsl:with-param>
											<xsl:with-param name="find">&amp;amp;</xsl:with-param><xsl:with-param name="replace_with">&amp;</xsl:with-param>
										</xsl:call-template></xsl:with-param>
									<xsl:with-param name="find">&amp;#8230;</xsl:with-param><xsl:with-param name="replace_with">&#8230;</xsl:with-param></xsl:call-template></xsl:with-param>
								<xsl:with-param name="find">&amp;#163;</xsl:with-param><xsl:with-param name="replace_with">[[pound]]</xsl:with-param></xsl:call-template></xsl:with-param>
							<xsl:with-param name="find">&amp;#153;</xsl:with-param><xsl:with-param name="replace_with">&#8482;</xsl:with-param></xsl:call-template></xsl:with-param>
						<xsl:with-param name="find">&amp;#8482;</xsl:with-param><xsl:with-param name="replace_with">&#8482;</xsl:with-param></xsl:call-template></xsl:with-param>
					<xsl:with-param name="find">&amp;#169;</xsl:with-param><xsl:with-param name="replace_with">&#169;</xsl:with-param></xsl:call-template></xsl:with-param>
				<xsl:with-param name="find">&amp;#174;</xsl:with-param><xsl:with-param name="replace_with">&#174;</xsl:with-param></xsl:call-template></xsl:with-param>
			<xsl:with-param name="find">&amp;quot;</xsl:with-param><xsl:with-param name="replace_with">[[quot]]</xsl:with-param></xsl:call-template></xsl:with-param>
		<xsl:with-param name="find">&amp;pound;</xsl:with-param><xsl:with-param name="replace_with">[[pound]]</xsl:with-param></xsl:call-template></xsl:with-param>
		<xsl:with-param name="find">£</xsl:with-param><xsl:with-param name="replace_with">[[pound]]</xsl:with-param></xsl:call-template></xsl:with-param>
		<xsl:with-param name="find">€</xsl:with-param><xsl:with-param name="replace_with">[[euro]]</xsl:with-param></xsl:call-template></xsl:with-param>
	<xsl:with-param name="find">\"</xsl:with-param><xsl:with-param name="replace_with">'</xsl:with-param></xsl:call-template></xsl:with-param>
	<xsl:with-param name="find">"</xsl:with-param><xsl:with-param name="replace_with">'</xsl:with-param></xsl:call-template></xsl:with-param>
	<xsl:with-param name="find">'</xsl:with-param><xsl:with-param name="replace_with">[[apos]]</xsl:with-param></xsl:call-template></xsl:with-param>
	</xsl:call-template>
</xsl:template>
</xsl:stylesheet>

