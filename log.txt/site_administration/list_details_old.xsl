<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:49:57 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template match="data_list">
	<xsl:for-each select="entry">
		<xsl:if test="position()=1">
			<tr class="formheader"> 
<!--			   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_IDENTY'"/></xsl:call-template></td>-->
			   	<xsl:for-each select="attribute[@show='YES']">
					<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template></td>
				</xsl:for-each>
		  		<xsl:if test="entry_options/button">
			   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_OPTIONS'"/></xsl:call-template></td>
		   		</xsl:if>
  			</tr>
		</xsl:if>
		<tr>
		<xsl:attribute name="class"><xsl:choose>
		<xsl:when test="(position() mod 2) = 1">TableCell_alt</xsl:when>
		<xsl:otherwise>TableCell</xsl:otherwise></xsl:choose></xsl:attribute>
<!--		   	<td valign="top"><xsl:value-of select="@identifier"/></td>-->
		   	<xsl:for-each select="attribute[@show='YES']">
			   	<td valign="top"><xsl:choose>
					<xsl:when test="@name='ENTRY_LOCKED'">
						<xsl:choose>
							<xsl:when test=".='0'"><xsl:call-template name="get_translation">
								<xsl:with-param name="check" select="'ENTRY_UNLOCKED'"/>
							</xsl:call-template>&#32;</xsl:when>
							<xsl:otherwise><xsl:choose>
								<xsl:when test="@link!='NO'">
								<xsl:variable name="link"><xsl:value-of select="@link"/></xsl:variable>
								<a><xsl:attribute name="href"><xsl:value-of select="../attribute[@name=$link]"/></xsl:attribute><xsl:call-template name="get_translation">
								<xsl:with-param name="check" select="'ENTRY_LOCKED'"/>
							</xsl:call-template></a></xsl:when>
								<xsl:otherwise><xsl:call-template name="get_translation">
								<xsl:with-param name="check" select="'ENTRY_LOCKED'"/>
							</xsl:call-template></xsl:otherwise>
							</xsl:choose>&#32;</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="@name='FILE_TYPE'">
					<img border="0"><xsl:attribute name="src">/mime-images/<xsl:call-template name="get_file_extension">
							<xsl:with-param name="file" select="../attribute[@name='FILE_NAME_AND_LOCATION']/."/>
					 </xsl:call-template>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:value-of select="."/></xsl:attribute></img>
					</xsl:when>
					<xsl:when test="@name='Message'"><xsl:value-of select="."/></xsl:when>
					<xsl:otherwise>
					<xsl:choose>
								<xsl:when test="@link!='NO'">
								<xsl:variable name="link"><xsl:value-of select="@link"/></xsl:variable>
								<a><xsl:attribute name="href"><xsl:value-of select="../attribute[@name=$link]"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></a></xsl:when>
								<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></xsl:otherwise>
							</xsl:choose>
							
					</xsl:otherwise>
				</xsl:choose></td>
			</xsl:for-each>
		   	<xsl:variable name="page_status">
			   	<xsl:value-of select="attribute[@name='ENTRY_STATUS']"/>
			</xsl:variable>
		   	<xsl:variable name="locked">
			   	<xsl:value-of select="attribute[@name='ENTRY_LOCKED']"/>
			</xsl:variable>
			<xsl:if test="entry_options/button">
		   	<td valign="top" width="10"><table cellpadding="0" cellspacing="0" border="0"><tr>
			<xsl:for-each select="entry_options/button">
				<xsl:variable name="command"><xsl:value-of select="@command"/></xsl:variable>
				<td><a><xsl:attribute name="href"><xsl:value-of select="@parameters"/>?command=<xsl:value-of select="$command"/>&amp;identifier=<xsl:value-of select="../../@identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a></td>
			</xsl:for-each>
			</tr></table></td></xsl:if>
  		</tr>
	</xsl:for-each>
</xsl:template>

<xsl:template name="check_lock">
	<xsl:param name="pad_lock"/>
	<xsl:param name="record"/>
	<xsl:param name="cmd"/>
	<xsl:variable name="user_identifier"><xsl:value-of select="//session/@user_identifier"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="$pad_lock='0'">
   		  	<a><xsl:attribute name="href">?command=<xsl:value-of select="$cmd"/>&amp;identifier=<xsl:value-of select="$record"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
	   	</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="$pad_lock=$user_identifier">
		   		  	<a><xsl:attribute name="href">?command=<xsl:value-of select="$cmd"/>&amp;identifier=<xsl:value-of select="$record"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
			   	</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
		 	</xsl:choose>
		</xsl:otherwise>
 	</xsl:choose>
</xsl:template>

</xsl:stylesheet>