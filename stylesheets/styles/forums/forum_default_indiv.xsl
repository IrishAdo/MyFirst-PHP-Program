<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.10 $
- Modified $Date: 2005/03/21 15:02:47 $
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
		<div>
			<div class='forum-category'>
				<span class='label'><xsl:value-of select="label"/></span>
				<span class='info'>Topics</span>
				<span class='info'>Posts</span>
			</div>
		</div>
		</xsl:if>
		<xsl:for-each select="forum">	
			<div>
				<div style='width:72%;display:inline'><a>
					<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="uri"/>/index.php</xsl:attribute>
					<xsl:attribute name="title"><xsl:value-of select="summary"/></xsl:attribute>
					
					<xsl:value-of select="title"/></a></div>
				<div style='width:14%;display:inline'><xsl:value-of select="threads/@total_threads"/></div>
				<div style='width:10%;display:inline'><xsl:value-of select="threads/@total_posts"/></div>
			</div>
			<xsl:if test="description!=''">
    			<div><xsl:value-of select="description" disable-output-escaping="yes"/></div>
			</xsl:if>
  	 	</xsl:for-each>
	</xsl:for-each>
	</div>
</xsl:template>


<xsl:template name="display_forum_results">
	<xsl:if test="./filter"><xsl:call-template name="display_filter"/></xsl:if>
	<h1 class='entrylocation'><span class='icon'><span class='text'><xsl:choose>
		<xsl:when test="label!=''"><xsl:value-of select="label"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="../fake_title"/></xsl:otherwise>
	</xsl:choose></span></span></h1>
	<div class='forum'>
	
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
			<script type="text/javascript" src="/libertas_images/javascripts/sortabletable.js"><xsl:comment> load sortable table //</xsl:comment>
			</script>
			<link type="text/css" rel="StyleSheet" href="/libertas_images/themes/sortabletable.css" />
			<table class="sortable" style="width:100%" cellspacing="0" cellpadding="2" summary="list of threads for this forum"><xsl:call-template name="forum_data_list"/></table>
		   	<div align="center"><xsl:call-template name="function_page_spanning"/></div>
		</xsl:if>
	</xsl:if>
	<xsl:if test="page_options/button">
	<div class="contentpos">
	<ul class="pageoptions">
	<xsl:for-each select="page_options/button">
		<li><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/_new-topic.php</xsl:attribute><img alt='Add a new topic' border='0'><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute></img></a></li>
	</xsl:for-each></ul>
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
	<h1 class='threadlabel'><span class='icon'><span class='text'><xsl:value-of select="thread[position()=1]/label"/></span></span></h1>
	<xsl:for-each select="thread">
		<xsl:variable name='blocked'><xsl:value-of select="@blocked"/></xsl:variable>
		<xsl:variable name='starter'><xsl:value-of select="@starter"/></xsl:variable>
		<xsl:variable name='identifier'><xsl:value-of select="@identifier"/></xsl:variable>
		<xsl:variable name='grouping'><xsl:value-of select="../../@grouping"/></xsl:variable>
		<div class='threadentry'><xsl:attribute name='id'>te<xsl:value-of select="position()"/></xsl:attribute>
			<div class="threadtitle">
			<xsl:if test="../../commands/command[@per_thread='1']">
			<xsl:if test="../../commands/command[@type!='reply']">
				<ul class='forumoptions'>
					<xsl:for-each select="../../commands/command[@per_thread='1']">
						<xsl:if test="@type='next'">
							<li class='next'><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?thread_identifier=<xsl:value-of select="$starter"/><xsl:if test=".!=''">&amp;<xsl:value-of select="."/></xsl:if></xsl:attribute><img alt='Next response' border='0'><xsl:attribute name="src" border='0'><xsl:value-of select="$image_path"/>/button_NEXT.gif</xsl:attribute></img></a></li>
						</xsl:if>
						<xsl:if test="@type='previous'">
							<li class='previous'><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?thread_identifier=<xsl:value-of select="$starter"/><xsl:if test=".!=''">&amp;<xsl:value-of select="."/></xsl:if></xsl:attribute><img alt='Previous response' border='0'><xsl:attribute name="src" border='0'><xsl:value-of select="$image_path"/>/button_PREVIOUS.gif</xsl:attribute></img></a></li>
						</xsl:if>
					</xsl:for-each>
				</ul> 
			</xsl:if>
			</xsl:if>
			<span><xsl:value-of select="label"/></span>
			</div>
			<div class="contentpos">
				<div class="postinfo"><xsl:value-of select="date"/> by <xsl:value-of select="author"/> 
				<xsl:if test="parent/@identifier>0"><br />In reply to "<a><xsl:attribute name='href'><xsl:value-of select="//setting[@name='real_script']"/>?thread_identifier=<xsl:value-of select="parent/@identifier"/><xsl:if test="parent/@page">&amp;page=<xsl:value-of select="parent/@page"/></xsl:if></xsl:attribute><xsl:value-of select="parent/title"/></a>"</xsl:if></div>
				<div class="content"><p><xsl:value-of select="description" disable-output-escaping="yes"/></p></div>
			</div>
			<xsl:if test="../../commands/command[@per_thread='1']">
				<ul class='forumoptions'>
					<xsl:for-each select="../../commands/command[@per_thread='1']">
						<xsl:if test="@type='reply' and $blocked=0">
							<li class='reply'><a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/_new-topic.php?identifier=<xsl:value-of select="$identifier"/></xsl:attribute><img alt='Reply' border='0'><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_REPLY.gif</xsl:attribute></img></a></li>
						</xsl:if>
					</xsl:for-each>
				</ul> 
			</xsl:if>
		</div>
	</xsl:for-each>
	<div class='threadoption'><a><xsl:attribute name='href'><xsl:value-of select="//setting[@name='fake_script']"/>/index.php</xsl:attribute><span class='icon'><span class='text'>Back to forum index</span></span></a></div>
	
</xsl:template>

</xsl:stylesheet>