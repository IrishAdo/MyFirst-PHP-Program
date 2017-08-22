<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:53 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template match="forum_list">
<p class="tableheader"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Forum Name'"/></xsl:call-template> (<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_NUMBER_THREADS'"/></xsl:call-template>)
		</p>
    	<xsl:for-each select="forum">	
		<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_VIEW_THREADS&amp;forum_identifier=<xsl:value-of select="@identifier"/>&amp;page=1</xsl:attribute><xsl:value-of select="title"/></a> (<xsl:value-of select="threads/@total_threads"/> threads)<br/>
		<xsl:if test=".!=''">
	    	<xsl:value-of select="description" disable-output-escaping="yes"/>
		</xsl:if>
  	 	</xsl:for-each>
</xsl:template>


<xsl:template name="display_forum_results">
<hr/>
	<xsl:if test="./filter"><xsl:call-template name="display_filter"/></xsl:if>
	<xsl:if test="page_options/button">
	<p class="contentpos"> |
	<xsl:for-each select="page_options/button">
		<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=<xsl:value-of select="@command"/>&amp;forum_identifier=<xsl:value-of select="../../@grouping"/></xsl:attribute><xsl:value-of select="@alt"/></a> |
	</xsl:for-each>
	</p>
	</xsl:if>
	<xsl:if test="./data_list">
		<xsl:if test="./data_list/@number_of_records='0'">
			<p>Sorry there are currently no threads in this forum</p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records='1'">
		<p>Displaying 1 result</p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records>'1'">
		<p>Displaying results <xsl:value-of select="./data_list/@start"/> to <xsl:value-of select="./data_list/@finish"/> of <xsl:value-of select="./data_list/@number_of_records"/> results</p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records>='1'">
			<xsl:call-template name="forum_data_list"/>
		   	<p align="center"><xsl:call-template name="function_page_spanning"/></p>
		</xsl:if>
	</xsl:if>
	
</xsl:template>

<xsl:template name="forum_data_list">
	<xsl:for-each select="data_list/entry">
	   	<xsl:for-each select="attribute">
	   		<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template> :: 
		   	<xsl:choose>
			   	<xsl:when test="position()=1">[<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_THREAD_VIEW_ENTRY&amp;forum_identifier=<xsl:value-of select="../../../@grouping"/>&amp;thread_identifier=<xsl:value-of select="../@identifier"/></xsl:attribute><xsl:value-of select="."/></a>]</xsl:when>
			   	<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
		   	</xsl:choose><br/>
		</xsl:for-each>
		<hr/>
	</xsl:for-each>
</xsl:template>


<xsl:template match="thread_entry">
	<hr/>
	<xsl:if test="//session/@user_identifier>0">
		<p>| <a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_THREAD_GENERATE&amp;identifier=<xsl:value-of select="thread/@identifier"/>&amp;forum_identifier=<xsl:value-of select="../@grouping"/></xsl:attribute>Reply to this Thread</a> | 
		<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_VIEW_THREADS&amp;forum_identifier=<xsl:value-of select="../@grouping"/></xsl:attribute>Back to forum list </a> | 
		<xsl:if test="//xml_document/modules/module[@name='client']/licence/product/@type='ECMS' and //session/@logged_in!=0">
		<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=ELERT_SIGNUP&amp;thread=<xsl:value-of select="thread/@starter"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_ELERT_SIGNUP'"/></xsl:call-template></a> |
		</xsl:if>
		</p>
	</xsl:if>
	<p class="tableHeader"><xsl:value-of select="thread/title"/></p>
	<p class="contentpos">Posted :: <xsl:value-of select="thread/date"/> by <xsl:value-of select="thread/author"/>
	<xsl:if test="thread/parent/@identifier>0"><br />In reply to "<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_THREAD_VIEW_ENTRY&amp;forum_identifier=<xsl:value-of select="../@grouping"/>&amp;thread_identifier=<xsl:value-of select="thread/parent/@identifier"/></xsl:attribute><xsl:value-of select="thread/parent/title"/></a>"</xsl:if></p>
	<p class="contentpos"><p><xsl:value-of select="thread/description" disable-output-escaping="yes"/></p></p>
	<p class="contentpos"><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_VIEW_THREADS&amp;forum_identifier=<xsl:value-of select="../@grouping"/></xsl:attribute>Back</a></p>
</xsl:template>

</xsl:stylesheet>