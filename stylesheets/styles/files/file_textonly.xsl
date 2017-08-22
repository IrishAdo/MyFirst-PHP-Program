<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2004/10/01 08:39:55 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
	 	
<xsl:template name="display_files">
	<xsl:param name="file_download_style"><xsl:value-of select="//setting[@name='file_list_format']"/></xsl:param>
	<p><strong>File Downloads</strong><br/>
	    	<xsl:for-each select="files/file">
    		[[nbsp]][[rightarrow]][[nbsp]]<a>
				<xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:value-of select="size"/>, approximate download time on 56k modem of <xsl:value-of select="download_time"/></xsl:attribute>
				<xsl:value-of select="label"/>.<xsl:call-template name="get_file_extension">
					<xsl:with-param name="file" select="name"/>
				</xsl:call-template>
			</a><br/>
			</xsl:for-each></p>
</xsl:template>

<xsl:template name="display_files_comma">
    	<xsl:comment>display_files_comma</xsl:comment>
    		<xsl:for-each select="files/file">
    		<xsl:if test="position()>1">,</xsl:if>
    		<a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
			<img width="32" height="32" ><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> "<xsl:value-of select="label"/>".</xsl:attribute></img>
			&#32;<xsl:value-of select="label"/></a>
    	</xsl:for-each>
</xsl:template>



<xsl:template name="get_file_extension">
	<xsl:param name="file"/>
	<xsl:param name="has"><xsl:choose>
	<xsl:when test="contains($file,'.')">1</xsl:when>
	<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:param>
	
	<xsl:variable name="values">
	<xsl:choose>
		<xsl:when test="contains($file,'.')">
				<xsl:call-template name="get_file_extension">
					<xsl:with-param name="file" select="substring-after($file,'.')"/>
				<xsl:with-param name="has" select="$has"/>
			 </xsl:call-template>
	   	</xsl:when>
		<xsl:otherwise>
			<xsl:if test="$has=1">
				<xsl:value-of select="$file"/>
			</xsl:if>
		</xsl:otherwise>
 	</xsl:choose>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="$values!=''"><xsl:value-of select="$values"/></xsl:when>
		<xsl:otherwise>lsl</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>