<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:39 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="display_comments_textonly">
	<xsl:param name="enable_discussion">0</xsl:param>
	<xsl:param name="trans_id">-1</xsl:param>
	<!-- Web Notes start here -->
	<!-- is there the ability to have web notes. -->
<xsl:if test="@web_notes=1 and count(../page)=1 and //xml_document/modules/module[@name='client']/licence/product/@type!='SITE'"> 
		<a name='list_comments'></a>
			<!-- Display the Add Comment option (direct or via login)-->
			<xsl:if test="$enable_discussion=1">
				| <a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=COMMENTS_ADD&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_COMMENT_ADD'"/></xsl:call-template></a> |
				<xsl:if test="//xml_document/modules/module[@name='client']/licence/product/@type='ECMS' and //session/@logged_in!=0">
				  <a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=ELERT_SIGNUP</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_ELERT_SIGNUP'"/></xsl:call-template></a> |
				</xsl:if>
			</xsl:if>
			<h1 class="tableheader"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_COMMENTS'"/></xsl:call-template></h1>
			<!-- Comments start here -->
			<xsl:variable name="page_identifier"><xsl:value-of select="@identifier"/></xsl:variable>
			<!-- if no comments then add inviting message-->
			<xsl:if test="not(../comments/comment[@page=$page_identifier])">
				<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_BE_THE_FIRST'"/></xsl:call-template></p>
			</xsl:if>
			<!-- For each comment-->
			<xsl:for-each select="../comments/comment[@page=$page_identifier]">
				<strong><xsl:value-of select="title" disable-output-escaping="yes"/></strong><br/>
				<em><xsl:value-of select="user"/><xsl:if test="company!=''">, <xsl:value-of select="company" disable-output-escaping='yes'/></xsl:if>, 
					<xsl:variable name="page_date"><xsl:value-of select="date"/></xsl:variable>
					<xsl:call-template name="format_date">
						<xsl:with-param name="current_date"><xsl:value-of select="$page_date"/></xsl:with-param>
					</xsl:call-template> </em>
					<xsl:if test="@response_to!=-1">
						<br/>
						<xsl:variable name="response_to"><xsl:value-of select="@response_to"/></xsl:variable>
						<xsl:variable name="str"><xsl:value-of select="../comment[$response_to = @identifier]/title"/></xsl:variable>
						<xsl:if test="string-length($str)>0">
							<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_IN_RESPONSE_TO'"/></xsl:call-template> :: 
							<xsl:value-of select="$str"/>,
							<xsl:call-template name="format_date">
								<xsl:with-param name="current_date"><xsl:value-of select="substring-before(../comment[$response_to = @identifier]/date,' ')"/></xsl:with-param>
							</xsl:call-template> 
							<br/>
						</xsl:if>
					</xsl:if>
				<xsl:if test="$enable_discussion=1">
					<p><xsl:value-of select="body" disable-output-escaping="yes"/></p>| <a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='real_script']"/>?command=COMMENTS_RESPOND&amp;reply_to=<xsl:value-of select="@identifier"/>&amp;identifier=<xsl:value-of select="@page"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REPLY_TO_COMMENT'"/></xsl:call-template></a> |<hr/>
				</xsl:if>
			</xsl:for-each>
				</xsl:if>
</xsl:template>

</xsl:stylesheet>