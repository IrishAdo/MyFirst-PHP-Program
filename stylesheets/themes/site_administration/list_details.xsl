<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.7 $
- Modified $Date: 2005/02/28 17:27:47 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template match="data_list">
	<xsl:for-each select="entry">
		<tr>
		<xsl:attribute name="class"><xsl:choose>
		<xsl:when test="(position() mod 2) = 1">TableCell_alt</xsl:when>
		<xsl:otherwise>TableCell</xsl:otherwise></xsl:choose></xsl:attribute>
		<xsl:if test="attribute[@show='FILE']"><td><img border="0"><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="attribute[@show='ICON']"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:value-of select="attribute[@show='FILE_TYPE']"/></xsl:attribute></img></td></xsl:if>
		<xsl:if test="attribute[@show='IMAGE']"><td><img border="0" width="80"><xsl:if test="attribute[@show='IMAGE']='images/themes/1x1.gif'"><xsl:attribute name="height">100</xsl:attribute></xsl:if><xsl:attribute name="src"><xsl:value-of select="attribute[@show='IMAGE']"/></xsl:attribute></img></td></xsl:if>					 

		<td valign="top" width="100%"><table summary="A table to hold some attribute information of the document in question" width="100%" border="0">
				<xsl:if test="attribute[@show='TITLE']">
					<tr>
						<td align="right" width="150" valign="top"><span class="field_txt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="attribute[@show='TITLE']/@name"/></xsl:call-template> :: </span></td>
						<td><strong><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="attribute[@show='TITLE']" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></strong></td>
					</tr>
				</xsl:if>
				<xsl:if test="attribute[@show='REPLY_TO']">
					<xsl:variable name="rec"><xsl:value-of select="attribute[@show='REPLY_TO']"/></xsl:variable>
					<tr><td align="right" width="150" valign="top"><span class="field_txt"><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="attribute[@show='REPLY_TO']/@name" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template> :: </span></td><td>
					<xsl:for-each select="../entry">
						<xsl:if test="@identifier=$rec">
							<xsl:value-of select="position()"/>
						</xsl:if>
					</xsl:for-each></td></tr>
				</xsl:if>
				<xsl:if test="attribute[@show='SUMMARY']">
				<xsl:for-each select="attribute[@show='SUMMARY']">
				<xsl:choose>
					<xsl:when test="string-length(.)!=0">
						<tr><td width="150" align="right" valign="top"><span class="field_txt">
							<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="@name" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template> :: 
						</span>
						</td><td>
						<xsl:call-template name="get_translation">
							<xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param>
						</xsl:call-template>
						</td></tr>
					</xsl:when>
					<xsl:otherwise><tr><td valign="top"><span class="field_txt"><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="@name" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template> :: </span></td><td><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_NA'"/></xsl:call-template></td></tr></xsl:otherwise>
				</xsl:choose>
				</xsl:for-each>
				</xsl:if>
				</table>
			</td><td valign="top"><table summary="A table to hold some attribute information of the document in question" width="300"><xsl:for-each select="attribute[@show='YES']">
				<tr><td class="field_txt" width="120" align="right" valign="top">
				<xsl:call-template name="get_translation">
								<xsl:with-param name="check" select="@name"/>
							</xsl:call-template> :: </td><td class="field_value" valign="top">
			   	<xsl:choose>
					<xsl:when test="@name='ENTRY_LOCKED'">
						<xsl:choose>
							<xsl:when test=".='0'"><xsl:call-template name="get_translation">
								<xsl:with-param name="check" select="'ENTRY_UNLOCKED'"/>
							</xsl:call-template>&#32;</xsl:when>
							<xsl:otherwise><xsl:choose>
								<xsl:when test="@link!='NO'">
								<xsl:variable name="link"><xsl:value-of select="@link"/></xsl:variable>
								<a><xsl:attribute name="href">admin/index.php<xsl:value-of select="../attribute[@name=$link]"/></xsl:attribute><xsl:call-template name="get_translation">
								<xsl:with-param name="check" select="'ENTRY_LOCKED'"/>
							</xsl:call-template></a></xsl:when>
								<xsl:otherwise><xsl:call-template name="get_translation">
								<xsl:with-param name="check" select="'ENTRY_LOCKED'"/>
							</xsl:call-template></xsl:otherwise>
							</xsl:choose>&#32;</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="@link!='NO'">
							<xsl:variable name="link"><xsl:value-of select="@link"/></xsl:variable>
							<a><xsl:attribute name="href"><xsl:choose>
					<xsl:when test="contains(../attribute[@name=$link],'http://') and substring-before(../attribute[@name=$link],'http://')=''"><xsl:value-of select="../attribute[@name=$link]" disable-output-escaping="yes"/></xsl:when>
					<xsl:when test="contains(../attribute[@name=$link],'?')"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/><xsl:value-of select="../attribute[@name=$link]" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?<xsl:if test="../../@link">command=<xsl:value-of select="../../@link"/>&amp;</xsl:if>
						<xsl:value-of select="../attribute[@name=$link]"/></xsl:otherwise>
					</xsl:choose></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a></xsl:when>
						<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:otherwise>
					</xsl:choose>
					</xsl:otherwise>
				</xsl:choose>
</td></tr>
			</xsl:for-each></table></td>
  		</tr>
		<xsl:if test="entry_options/button">
<tr>		<xsl:attribute name="class"><xsl:choose>
		<xsl:when test="(position() mod 2) = 1">TableCell_alt</xsl:when>
		<xsl:otherwise>TableCell</xsl:otherwise></xsl:choose></xsl:attribute>
<td><xsl:attribute name="colspan">
<xsl:choose>
		<xsl:when test="attribute[@show='FILE'] or attribute[@show='IMAGE'] ">3</xsl:when>
		<xsl:otherwise>2</xsl:otherwise>
</xsl:choose>
</xsl:attribute>
<xsl:for-each select="entry_options/button">
				<xsl:variable name="command"><xsl:value-of select="@command"/></xsl:variable>
				| <a><xsl:attribute name="href"><xsl:choose>
				<xsl:when test="(@parameters!='IGNORE') and (@iconify='REMOVE' or @iconify='PUBLISH' or @iconify='NEXT_STAGE' or @iconify='VALIDATE')">javascript:check_confirm('<xsl:value-of select="@iconify"/>','admin/index.php?command=<xsl:value-of select="$command"/>&amp;identifier=<xsl:value-of select="../../@identifier"/>');</xsl:when>
				<xsl:when test="(@parameters!='IGNORE') and ((@iconify='REWORK' or @iconify='UNPUBLISH') and //modules/module/licence/product/@type!='ECMS')">javascript:check_confirm('<xsl:value-of select="@iconify"/>','admin/index.php?command=<xsl:value-of select="$command"/>&amp;identifier=<xsl:value-of select="../../@identifier"/>');</xsl:when>
				<xsl:otherwise><xsl:choose><xsl:when test="@parameters='' or @parameters='IGNORE'">admin/index.php</xsl:when>
				<xsl:otherwise><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/><xsl:value-of select="@parameters"/></xsl:otherwise>
				</xsl:choose>?command=<xsl:value-of select="$command"/>&amp;identifier=<xsl:value-of select="../../@identifier"/></xsl:otherwise></xsl:choose></xsl:attribute>
				<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@alt"/></xsl:call-template>
				</a></xsl:for-each> |</td></tr></xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="check_lock">
	<xsl:param name="pad_lock"/>
	<xsl:param name="record"/>
	<xsl:param name="cmd"/>
	<xsl:variable name="user_identifier"><xsl:value-of select="//session/@user_identifier"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="$pad_lock='0'">
   		  	<a><xsl:attribute name="href">admin/index.php?command=<xsl:value-of select="$cmd"/>&amp;identifier=<xsl:value-of select="$record"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
	   	</xsl:when>
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="$pad_lock=$user_identifier">
		   		  	<a><xsl:attribute name="href">admin/index.php?command=<xsl:value-of select="$cmd"/>&amp;identifier=<xsl:value-of select="$record"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
			   	</xsl:when>
				<xsl:otherwise>
				</xsl:otherwise>
		 	</xsl:choose>
		</xsl:otherwise>
 	</xsl:choose>
</xsl:template>


<xsl:template match="table_list">
	<tr>
		<xsl:for-each select="entry[position()=1]/attribute[@show!='NO']">
		<th><strong><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template></strong></th>
		</xsl:for-each>
		<th><strong>Options</strong></th>
	</tr>
	<xsl:for-each select="entry">
		<tr>
			<xsl:attribute name="class"><xsl:choose>
				<xsl:when test="(position() mod 2) = 1">TableCell_alt</xsl:when>
				<xsl:otherwise>TableCell</xsl:otherwise></xsl:choose>
			</xsl:attribute>
			<xsl:if test="attribute[@show='FILE']"><td><img border="0"><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="attribute[@show='ICON']"/>.gif</xsl:attribute><xsl:attribute name="alt"><xsl:value-of select="attribute[@show='FILE_TYPE']"/></xsl:attribute></img></td></xsl:if>
			<xsl:if test="attribute[@show='IMAGE']"><td><img border="0" width="80"><xsl:if test="attribute[@show='IMAGE']='images/themes/1x1.gif'"><xsl:attribute name="height">100</xsl:attribute></xsl:if><xsl:attribute name="src"><xsl:value-of select="attribute[@show='IMAGE']"/></xsl:attribute></img></td></xsl:if>					 
			<xsl:for-each select="attribute[@show!='NO']">
				<td valign="top">
					<xsl:choose>
						<xsl:when test="@link!='NO'"><xsl:variable name='link'><xsl:value-of select="@link"/></xsl:variable>
						<a>
						<xsl:attribute name="href">admin/index.php<xsl:value-of select="../attribute[@name=$link]"/></xsl:attribute>
						<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a></xsl:when>
						<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:otherwise>
					</xsl:choose>
				</td>
			</xsl:for-each>
			<xsl:if test="entry_options/button">
				<td valign="top">
					<xsl:for-each select="entry_options/button">
						<xsl:variable name="command"><xsl:value-of select="@command"/></xsl:variable>
						| <a><xsl:attribute name="href"><xsl:choose>
							<xsl:when test="(@parameters!='IGNORE') and (@iconify='REMOVE' or @iconify='PUBLISH' or @iconify='NEXT_STAGE' or @iconify='VALIDATE' or @iconify='SUBSCRIBE' or @iconify='UNSUBSCRIBE')">javascript:check_confirm('<xsl:value-of select="@iconify"/>','admin/index.php?command=<xsl:value-of select="$command"/>&amp;identifier=<xsl:value-of select="../../@identifier"/>');</xsl:when>
							<xsl:when test="(@parameters!='IGNORE') and ((@iconify='REWORK' or @iconify='UNPUBLISH') and //modules/module/licence/product/@type!='ECMS')">javascript:check_confirm('<xsl:value-of select="@iconify"/>','admin/index.php?command=<xsl:value-of select="$command"/>&amp;identifier=<xsl:value-of select="../../@identifier"/>');</xsl:when>
							<xsl:otherwise><xsl:choose><xsl:when test="@parameters='' or @parameters='IGNORE'">admin/index.php</xsl:when>
							<xsl:otherwise><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/><xsl:value-of select="@parameters"/></xsl:otherwise>
						</xsl:choose>?command=<xsl:value-of select="$command"/>&amp;identifier=<xsl:value-of select="../../@identifier"/></xsl:otherwise></xsl:choose></xsl:attribute>
							<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@alt"/></xsl:call-template>
						</a></xsl:for-each> |
			</td></xsl:if>
		</tr>
		</xsl:for-each>
</xsl:template>

</xsl:stylesheet>