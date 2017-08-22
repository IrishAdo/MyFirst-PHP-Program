<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.4 $
- Modified $Date: 2004/09/11 10:05:49 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"> 
<xsl:template match="forum_list">
		<div class="row">
			<div class="tableheader50"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_TITLE'"/></xsl:call-template></div>
			<div class="tableheader50"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_NUMBER_THREADS'"/></xsl:call-template></div>
		</div>
    	<xsl:for-each select="forum">	
			<div class="row">
				<div class="cell50"><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_VIEW_THREADS&amp;forum_identifier=<xsl:value-of select="@identifier"/>&amp;page=1</xsl:attribute><xsl:value-of select="title"/></a></div>
				<div class="cell50"><xsl:value-of select="threads/@total_threads"/> threads</div>
			</div>
			<xsl:if test=".!=''">
				<div class="row">
	    			<div class="TableCell"><xsl:value-of select="description" disable-output-escaping="yes"/></div>
				</div>
			</xsl:if>
  	 	</xsl:for-each>
</xsl:template>


<xsl:template name="display_forum_results">
	<xsl:if test="label"><h1><span><xsl:value-of select="label"/></span></h1></xsl:if>
	<xsl:if test="./filter"><xsl:call-template name="display_filter"/></xsl:if>
	<xsl:if test="//session/@logged_in='0'">
		<p>You are required to <A><xsl:attribute name='href'><xsl:value-of select="//setting[@name='script']"/>?command=USERS_SHOW_LOGIN</xsl:attribute>login</A> to.</p>
	</xsl:if>
	<xsl:if test="page_options/button">
		<div class="contentpos"><p> | <xsl:for-each select="page_options/button"><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=<xsl:value-of select="@command"/>&amp;forum_identifier=<xsl:value-of select="../../@grouping"/></xsl:attribute><xsl:value-of select="@alt"/></a> | </xsl:for-each></p></div>
	</xsl:if>
	<xsl:if test="./data_list">
	<div class="contentpos">
		<xsl:if test="./data_list/@number_of_records='0'">
			<p>Sorry there are currently no threads in this forum</p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records='1'">
		<p>Displaying 1 result</p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records>'1'">
		<p>Displaying results <xsl:value-of select="./data_list/@start"/> to <xsl:value-of select="./data_list/@finish"/> of <xsl:value-of select="./data_list/@number_of_records"/> results</p>
		</xsl:if>
		</div>
		<xsl:if test="./data_list/@number_of_records>='1'">
			<xsl:call-template name="forum_data_list"/>
		   	<div align="center"><xsl:call-template name="function_page_spanning"/></div>
		</xsl:if>
	</xsl:if>
	
</xsl:template>

<xsl:template name="forum_data_list">
	<xsl:for-each select="data_list/entry">
		<xsl:if test="position()=1">
		<div class='row'> 
		   	<xsl:for-each select="attribute">
	   		<div class="tableHeader33"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template></div>
			</xsl:for-each>
		</div>
		</xsl:if>
		<div class='row'> 
		   	<xsl:for-each select="attribute">
		   	<xsl:choose>
			   	<xsl:when test="position()=1"><div class='cell33'><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_THREAD_VIEW_ENTRY&amp;forum_identifier=<xsl:value-of select="../../../@grouping"/>&amp;thread_identifier=<xsl:value-of select="../@identifier"/></xsl:attribute><xsl:value-of select="."/></a></div></xsl:when>
			   	<xsl:otherwise><div class='cell33' ><xsl:value-of select="."/></div></xsl:otherwise>
		   	</xsl:choose>
			</xsl:for-each>
  		</div>
	</xsl:for-each>
</xsl:template>


<xsl:template match="thread_entry">
	<xsl:if test="//session/@user_identifier>0"><div class='contentpos'>
		<p>| <a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_THREAD_GENERATE&amp;identifier=<xsl:value-of select="thread/@identifier"/>&amp;forum_identifier=<xsl:value-of select="../@grouping"/></xsl:attribute>Reply to this Thread</a> | 
		<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_VIEW_THREADS&amp;forum_identifier=<xsl:value-of select="../@grouping"/></xsl:attribute>Back to forum list </a> | 
		<xsl:if test="//xml_document/modules/module[@name='client']/licence/product/@type='ECMS' and //session/@logged_in!=0">
		<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=ELERT_SIGNUP&amp;thread=<xsl:value-of select="thread/@starter"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_ELERT_SIGNUP'"/></xsl:call-template></a> |
		</xsl:if>
		</p></div></xsl:if>
	<div class="tableHeader"><xsl:value-of select="thread/title"/></div>
	<div class="contentpos"><p>Posted :: <xsl:value-of select="thread/date"/> by <xsl:value-of select="thread/author"/>
	<xsl:if test="thread/parent/@identifier>0"><br />sIn reply to "<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_THREAD_VIEW_ENTRY&amp;forum_identifier=<xsl:value-of select="../@grouping"/>&amp;thread_identifier=<xsl:value-of select="thread/parent/@identifier"/></xsl:attribute><xsl:value-of select="thread/parent/title"/></a>"</xsl:if></p></div>
	<div class="contentpos"><p><xsl:value-of select="thread/description" disable-output-escaping="yes"/></p></div>
	<div class="contentpos"><p><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>?command=FORUM_VIEW_THREADS&amp;forum_identifier=<xsl:value-of select="../@grouping"/></xsl:attribute>Back</a></p></div>
</xsl:template>

</xsl:stylesheet>