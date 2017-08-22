<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.7 $
- Modified $Date: 2005/02/09 12:08:44 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
 
<xsl:template name="get_image_tag">
	<xsl:param name="url"></xsl:param>
	<xsl:choose>
		<xsl:when test="contains($url,'/')"><xsl:call-template name="get_image_tag"><xsl:with-param name="url"><xsl:value-of select="substring-after($url,'/')"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:otherwise><xsl:value-of select="substring-before($url,'.')"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_imagelist">
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="show_label">1</xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:choose>
		<xsl:when test="//setting[@name='displaymode']='pda'">		
		</xsl:when>
		<xsl:when test="$image_path = '/libertas_images/themes/textonly'">
			<xsl:if test="object">
				<hr/>
				<xsl:for-each select="object">
					<xsl:choose>
						<xsl:when test="@type='img'">
							[image: <a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=FILES_INFO&amp;identifier=<xsl:call-template name="get_image_tag"><xsl:with-param name="url"><xsl:value-of select="src"/></xsl:with-param></xsl:call-template></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="alt"/></xsl:attribute><xsl:value-of select="alt"/></a>]
						</xsl:when>
						<xsl:otherwise></xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</xsl:if>
		</xsl:when>
		<xsl:otherwise>
	<xsl:if test="$show_label=1 and label!=''">
		<h1 id='label'><xsl:value-of select="label"/></h1>
	</xsl:if>
	<div><xsl:attribute name="class"><xsl:value-of select="type/@align"/></xsl:attribute>
	<xsl:choose>
		<xsl:when test="type/@direction='horizontal'">
			<xsl:for-each select="object">
				<xsl:choose>
					<xsl:when test="@type='img'">
						<img>
							<xsl:attribute name="style">margin:<xsl:value-of select="../type/@vspace"/>px <xsl:value-of select="../type/@hspace"/>px</xsl:attribute>
							<xsl:attribute name="longdesc"><xsl:value-of select="//setting[@name='real_script']"/>?command=FILES_INFO&amp;identifier=<xsl:call-template name="get_image_tag"><xsl:with-param name="url"><xsl:value-of select="src"/></xsl:with-param></xsl:call-template></xsl:attribute>
							<xsl:attribute name="src"><xsl:value-of select="src"/></xsl:attribute>
							<xsl:attribute name="alt"><xsl:value-of select="alt"/></xsl:attribute>
							<xsl:attribute name="height"><xsl:choose>
								<xsl:when test="../type/@resize_height!=0"><xsl:value-of select="../type/@resize_height"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="@height"/></xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:attribute name="width"><xsl:choose>
								<xsl:when test="../type/@resize_width!=0"><xsl:value-of select="../type/@resize_width"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="@width"/></xsl:otherwise>
							</xsl:choose></xsl:attribute>
						</img><div class="desclink"><ul><li><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=FILES_INFO&amp;identifier=<xsl:call-template name="get_image_tag"><xsl:with-param name="url"><xsl:value-of select="src"/></xsl:with-param></xsl:call-template></xsl:attribute>D</a></li></ul></div>
					</xsl:when>
					<xsl:otherwise></xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		
		</xsl:when>
		<xsl:otherwise>
			<xsl:for-each select="object">
				<xsl:choose>
					<xsl:when test="@type='img'">
						<!-- Paragraph  remove <p> --> <img>
							<xsl:attribute name="style">margin:<xsl:value-of select="../type/@vspace"/>px <xsl:value-of select="../type/@hspace"/>px</xsl:attribute>
							<xsl:attribute name="longdesc"><xsl:value-of select="//setting[@name='real_script']"/>?command=FILES_INFO&amp;identifier=<xsl:call-template name="get_image_tag"><xsl:with-param name="url"><xsl:value-of select="src"/></xsl:with-param></xsl:call-template></xsl:attribute>
							<xsl:attribute name="src"><xsl:value-of select="src"/></xsl:attribute>
							<xsl:attribute name="alt"><xsl:value-of select="alt"/></xsl:attribute>
							<xsl:attribute name="height"><xsl:choose>
								<xsl:when test="../type/@resize_height!=0"><xsl:value-of select="../type/@resize_height"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="@height"/></xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:attribute name="width"><xsl:choose>
								<xsl:when test="../type/@resize_width!=0"><xsl:value-of select="../type/@resize_width"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="@width"/></xsl:otherwise>
							</xsl:choose></xsl:attribute>
						</img><div class="desclink"><ul><li><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=FILES_INFO&amp;identifier=<xsl:call-template name="get_image_tag"><xsl:with-param name="url"><xsl:value-of select="src"/></xsl:with-param></xsl:call-template></xsl:attribute>D</a></li></ul></div><!-- Paragraph  remove </p> -->
					</xsl:when>
					<xsl:otherwise></xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
	</div>
	</xsl:otherwise></xsl:choose>
</xsl:template>


</xsl:stylesheet>
