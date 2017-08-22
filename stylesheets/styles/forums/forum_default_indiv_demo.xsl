<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.11 $
- Modified $Date: 2008/09/15 10:06:00 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template match="forum_list">
	<h1 class='entrylocation'><span class='icon'><span class='text'><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></span></span></h1>
	<br/>
	<div class='forum'>
	<xsl:for-each select="category">
		<xsl:if test="label!=''">
	
			<div class='forum-category'>
				<span class='label'><xsl:value-of select="label"/></span>
				
			</div>
		
		</xsl:if>
		<xsl:for-each select="forum">	
			<div id='forum_block'>
				<div id='title_and_description'>
				<div id='forum_home'><a>
					<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="uri"/>/index.php</xsl:attribute>
					<xsl:attribute name="title"><xsl:value-of select="summary"/></xsl:attribute>
					
					<xsl:value-of select="title"/></a></div>
					<xsl:if test="description!=''">
    			<div id="forum_description"><xsl:value-of select="description" disable-output-escaping="yes"/></div>
			</xsl:if>
			</div>
				<div id='total_threads'><xsl:value-of select="threads/@total_threads"/> themes</div>
				<div id='total_posts'><xsl:value-of select="threads/@total_posts"/> posts</div>
			</div>
			
  	 	</xsl:for-each>
	</xsl:for-each>
	</div>
</xsl:template>


<xsl:template name="display_forum_results">
	<xsl:if test="./filter"><xsl:call-template name="display_filter"/></xsl:if>
	
	<div class='forum'>
	<h1 class='forum_title'><span id='forum_loc'>Forum > </span><span class='icon'><span class='text'><xsl:choose>
		<xsl:when test="label!=''"><xsl:value-of select="label"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="../fake_title"/></xsl:otherwise>
	</xsl:choose></span></span></h1>
	<xsl:if test="./data_list">
	<div class="contentpos">
		<xsl:if test="./data_list/@number_of_records='0'">
			<p>Sorry there are currently no threads in this forum</p>
		</xsl:if>
		<xsl:if test="(./data_list/@number_of_records>'1' or ./table_list/@number_of_records>'1') and (./data_list/@number_of_pages>'1' or ./table_list/@number_of_pages>'1')">
		<p>Displaying results <xsl:value-of select="./data_list/@start"/> to <xsl:value-of select="./data_list/@finish"/> of <xsl:value-of select="./data_list/@number_of_records"/> results</p>
		</xsl:if>
		</div>
		<xsl:if test="./data_list/@number_of_records>='1'">
			<script type="text/javascript" src="/libertas_images/javascripts/sortabletable.js"><xsl:comment> load sortable table</xsl:comment>
			</script>
			<link type="text/css" rel="StyleSheet" href="/libertas_images/themes/sortabletable.css" />
			<table class="sortable_forum" style="width:100%" cellspacing="0" cellpadding="2" summary="list of threads for this forum"><xsl:call-template name="forum_data_list"/></table>
		   	<div align="center"><xsl:call-template name="function_page_spanning"/></div>
		</xsl:if>
	</xsl:if>
	<xsl:if test="page_options/button">
	<div class="contentpos2">
	
	<xsl:for-each select="page_options/button">
		<div id='forumoption'><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/_new-topic.php</xsl:attribute><img alt='Add a new topic' border='0'><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute></img></a></div>
	</xsl:for-each>
	</div>
	</xsl:if>
	</div>	
</xsl:template>

<xsl:template name="forum_data_list">
	<xsl:for-each select="data_list/entry">
		<xsl:variable name='identifier'><xsl:value-of select="@identifier"/></xsl:variable>
		<xsl:if test="position()=1">
		<tr> 
		   	<xsl:for-each select="attribute[@show!='No']">
	   		<th class="tableHeader"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template></th>
			</xsl:for-each>
		</tr>
		</xsl:if>
		<tr> 
		   	<xsl:for-each select="attribute[@show!='No']">
		   	<xsl:choose>
			   	<xsl:when test="position()=1"><td>
				<xsl:variable name="name"><xsl:value-of select="@link"/></xsl:variable>
				<xsl:variable name="alt"><xsl:value-of select="@alt"/></xsl:variable>
				<xsl:choose>
					<xsl:when test="../attribute[@name=$name]='[[nbsp]]'"><xsl:value-of select="."/></xsl:when>
					<xsl:otherwise><a>
						<xsl:attribute name='title'><xsl:value-of select="../attribute[@name=$alt]"/></xsl:attribute>
						<xsl:attribute name="href"><xsl:value-of select="substring-before(//setting[@name='real_script'],'index.php')"/><xsl:value-of select="../attribute[@name=$name]"/>?thread_identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:value-of select="."/></a>
					</xsl:otherwise>
				</xsl:choose></td></xsl:when>
			   	<xsl:otherwise><td><xsl:value-of select="."/></td></xsl:otherwise>
		   	</xsl:choose>
			</xsl:for-each>
  		</tr>
	</xsl:for-each>
</xsl:template>


<xsl:template match="thread_entry">
<div class='forum'>
<div class='threadoption'><a><xsl:attribute name='href'><xsl:value-of select="//setting[@name='fake_script']"/>/index.php</xsl:attribute><span class='icon'><span class='text'>Forum Home</span></span></a></div>
	
	<h1 class='threadlabel'><span class='icon'><span class='text'><xsl:value-of select="thread[position()=1]/label"/></span></span></h1>
	
	<xsl:for-each select="thread">
		<xsl:variable name='blocked'><xsl:value-of select="@blocked"/></xsl:variable>
		<xsl:variable name='starter'><xsl:value-of select="@starter"/></xsl:variable>
		<xsl:variable name='identifier'><xsl:value-of select="@identifier"/></xsl:variable>
		<xsl:variable name='grouping'><xsl:value-of select="../../@grouping"/></xsl:variable>
		<div class='threadentry'><xsl:attribute name='id'><xsl:value-of select="position()"/></xsl:attribute>
			<div class="threadtitle">
			<xsl:if test="../../commands/command[@per_thread='1']">
			<xsl:if test="../../commands/command[@type!='reply']">
				
					<xsl:for-each select="../../commands/command[@per_thread='1']">
						<xsl:if test="@type='next'">
							<div id='next'><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?thread_identifier=<xsl:value-of select="$starter"/><xsl:if test=".!=''">&amp;<xsl:value-of select="."/></xsl:if></xsl:attribute><img alt='Next response' border='0'><xsl:attribute name="src" border='0'><xsl:value-of select="$image_path"/>/button_NEXT.gif</xsl:attribute></img></a></div>
						</xsl:if>
						<xsl:if test="@type='previous'">
							<div id='prev'><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?thread_identifier=<xsl:value-of select="$starter"/><xsl:if test=".!=''">&amp;<xsl:value-of select="."/></xsl:if></xsl:attribute><img alt='Previous response' border='0'><xsl:attribute name="src" border='0'><xsl:value-of select="$image_path"/>/button_PREVIOUS.gif</xsl:attribute></img></a></div>
						</xsl:if>
					</xsl:for-each>
				 
			</xsl:if>
			</xsl:if>
			
			</div></div>
			<div id="postinfo">
				<div id="postauthor"><xsl:value-of select="author"/></div>
				<div id="postdate"><xsl:value-of select="date"/></div> 
			</div>
				<div id="response"><xsl:if test="parent/@identifier>0">In reply to "<a><xsl:attribute name='href'><xsl:value-of select="//setting[@name='real_script']"/>?thread_identifier=<xsl:value-of select="parent/@identifier"/><xsl:if test="parent/@page">&amp;page=<xsl:value-of select="parent/@page"/></xsl:if></xsl:attribute><xsl:value-of select="parent/title"/></a>"</xsl:if></div>
				<div id="content"><p><xsl:value-of select="description" disable-output-escaping="yes"/></p></div>
			
			<xsl:if test="../../commands/command[@per_thread='1']">
				
					<xsl:for-each select="../../commands/command[@per_thread='1']">
						<xsl:if test="@type='reply' and $blocked=0">
							<div id='forumoption'><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/_new-topic.php?identifier=<xsl:value-of select="$identifier"/></xsl:attribute><img alt='Reply' border='0'><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_REPLY.gif</xsl:attribute></img></a></div>
						</xsl:if>
					</xsl:for-each>
				 
			</xsl:if>
		
	</xsl:for-each>
	
	</div>
</xsl:template>

</xsl:stylesheet>