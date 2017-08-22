<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.9 $
- Modified $Date: 2005/01/24 08:57:25 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
	 	
<xsl:template name="display_files">
	<xsl:param name="show_label">1</xsl:param>
	<xsl:param name="file_download_style"><xsl:value-of select="//setting[@name='file_list_format']"/></xsl:param>
   	<xsl:if test="contains(files/file/mime,'image/')">
<SCRIPT LANGUAGE="JavaScript">
<xsl:comment> Begin
var timeDelay = 5; // change delay time in seconds
var Pix = new Array(<xsl:for-each select="files/file[contains(mime,'image')]">"<xsl:value-of select="directory"/><xsl:value-of select="md5"/>.<xsl:call-template name="get_file_extension"><xsl:with-param name="file"><xsl:value-of select="url"/></xsl:with-param></xsl:call-template>"<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
var howMany = Pix.length;
timeDelay *= 1000;
var PicCurrentNum = 0;
var PicCurrent = new Image();
	PicCurrent.src = Pix[PicCurrentNum];

function startPix() {
	setInterval("slideshow()",timeDelay);
}

function slideshow() {
	PicCurrentNum++;
	if (PicCurrentNum == howMany) {
		PicCurrentNum = 0;
	}
	PicCurrent.src = Pix[PicCurrentNum];
	document["ChangingPix"].src = PicCurrent.src;
}
//  End </xsl:comment>
</SCRIPT>
<xsl:for-each select="files/file[contains(mime,'image')][position()=1]">
<img name="ChangingPix" width="275" height="188" alt="" border="0" align="left">
<xsl:attribute name="src"><xsl:value-of select="directory"/><xsl:value-of select="md5"/>.<xsl:call-template name="get_file_extension"><xsl:with-param name="file"><xsl:value-of select="url"/></xsl:with-param></xsl:call-template></xsl:attribute>
</img></xsl:for-each>
</xsl:if>	
    <xsl:comment>display_files</xsl:comment>
	<xsl:choose>
		<xsl:when test="$file_download_style='LIST'">
			<xsl:if test="count(files/file)!=1">
				<div class="filetitle"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LIST_OF_FILES'"/></xsl:call-template></div>
			</xsl:if>
	    	<xsl:for-each select="files/file">
			<div>
    			<div class='fileicon'><img style="width:32px;height:32px"><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></div>
				<div class='filedes'><a>
				<xsl:attribute name="title"><xsl:value-of select="url"/></xsl:attribute>
				<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute><xsl:value-of select="label"/></a>
					<br/>File size : <span class="filedescription"><xsl:value-of select="size"/></span><br/>Download time on (56k) : <span class="filedescription"><xsl:value-of select="download_time"/></span><br/>Date uploaded : <span class="filedescription"><xsl:value-of select="substring-before(date,' ')"/></span></div>
				</div>
    		</xsl:for-each>
		</xsl:when>
		<xsl:when test="$file_download_style='TABLE'">
			<xsl:if test="count(files/file)!=1">
			<div class="filetitle"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LIST_OF_FILES'"/></xsl:call-template></div>
			</xsl:if>
			<table summary="This table holds the title,summary,size of file and download time" class="filetable">
			<tr><th scope="col" class='alignleft' colspan="2">Filename</th><th class='alignleft' scope="col" style='width:60'>Size</th><th class='alignright' scope="col" style='width:100'>Time</th></tr>
	    	<xsl:for-each select="files/file">
    		<tr>
				<td valign="top" style="width:20px"><img  style="width:16px;height:16px"><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute></img></td>
				<td><a>
				<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
				<xsl:attribute name="title"><xsl:value-of select="url"/></xsl:attribute>
				<xsl:value-of select="label"/></a></td>
				<td valign="top"><span class="filedescription"><xsl:value-of select="size"/></span></td>
				<td valign="top"><span class="filedescription"><xsl:value-of select="download_time"/></span></td>
			</tr>
    		</xsl:for-each>
			</table>
		</xsl:when>
		<xsl:when test="$file_download_style='TITLE AND SUMMARY'">
			<xsl:if test="count(files/file)!=1">
				<div class="filetitle"><xsl:choose><xsl:when test="position()=1"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LIST_OF_FILES'"/></xsl:call-template></xsl:when><xsl:otherwise>[[nbsp]]</xsl:otherwise></xsl:choose></div>
			</xsl:if>
			<table summary="this table holds the title,size of file and download time" class="filetable">
				<tr>
					<th colspan="2">Filename</th>
					<th>Download time</th>
				</tr>
		    	<xsl:for-each select="files/file">
				<tr class="filecells">
					<td valign="top" rowspan="2" class="imgicon"><img style="width:32px;height:32px"><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></td>
					<td><a>
						<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
						<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> '<xsl:value-of select="label"/>'</xsl:attribute>
						<xsl:value-of select="label"/>
						</a></td>
					<td valign="top"  class='alignright'><span class="filedescription"><xsl:value-of select="size"/> / <xsl:value-of select="download_time"/> to download</span></td>
				</tr>
				<tr class="filecells">
					<td valign="top" colspan="2"><span class="filedescription"><xsl:value-of select="description"/></span></td>
				</tr>
    		</xsl:for-each>
		    </table>
		</xsl:when>
		<xsl:otherwise>
			<table summary="this table holds the title,summary,size of file and download time" class='filetable'>
	    	<xsl:for-each select="files/file">
    		<tr>
				<td valign="top" style="width:20px"><img  style="width:16px;height:16px"><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/></xsl:attribute></img></td>
				<td><a>
					<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
					<xsl:attribute name="title"><xsl:value-of select="url"/> (<xsl:value-of select="size"/>/<xsl:value-of select="download_time"/>)</xsl:attribute>
				<xsl:value-of select="label"/></a></td>
			</tr>
    		</xsl:for-each>
			</table>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_files_comma">
    	<xsl:comment>display_files_comma</xsl:comment>
    		<xsl:for-each select="files/file[contains(mime,'image')=false()]">
    		<xsl:if test="position()>1">,</xsl:if>
    		<a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="md5"/></xsl:attribute>
			<img border="0" style="width:32px;height:32px" ><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="icon"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CLICK_TO_DOWNLOAD'"/></xsl:call-template> <xsl:value-of select="label"/>.</xsl:attribute></img>
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