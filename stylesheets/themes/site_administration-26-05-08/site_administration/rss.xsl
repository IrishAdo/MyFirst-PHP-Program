<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2004/11/15 15:33:39 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
	exclude-result-prefixes="rdf rss l dc admin content xsl"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:rss="http://purl.org/rss/1.0/"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:l="http://purl.org/rss/1.0/modules/link/"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" 
	xmlns:syn="http://purl.org/rss/1.0/modules/syndication/" 
	xmlns:admin="http://webns.net/mvcb/"
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"
 > 

<xsl:template name="rssChannel">
	<xsl:choose>
		<xsl:when test="rss">
			<xsl:if test="feed/fields/field[@name='show']='Channel_Title'">
				<div class="formheader"><xsl:value-of select="rss/channel/title"/></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Image'">
				<xsl:if test="rss/channel/image">
				<div class="contentpos"><a>
					<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
						<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
					</xsl:if>
					<xsl:attribute name="href"><xsl:value-of select="rss/channel/image/link"/></xsl:attribute><img border="0">
					<xsl:attribute name="src"><xsl:value-of select="rss/channel/image/url"/></xsl:attribute>
					<xsl:attribute name="alt"><xsl:value-of select="rss/channel/image/title"/></xsl:attribute>
					<xsl:if test="rss/channel/image/width">
						<xsl:attribute name="width"><xsl:value-of select="rss/channel/image/width"/></xsl:attribute>
					</xsl:if>
					<xsl:if test="rss/channel/image/height">
						<xsl:attribute name="height"><xsl:value-of select="rss/channel/image/height"/></xsl:attribute>
					</xsl:if>
					</img></a></div>
				</xsl:if>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Description' and rss/channel/description"><div class="contentpos"><xsl:value-of select="rss/channel/description"/></div></xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Copyright' and rss/channel/copyright">
				<div class="contentpos">Copyright :: <xsl:value-of select="rss/channel/copyright"/></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Last Build Date' and rss/channel/lastBuildDate">
			<div class="contentpos">Last Build :: <xsl:value-of select="rss/channel/lastBuildDate"/></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Categories' and rss/channel/category">
				<div class="contentpos">Categories :: <xsl:for-each select="rss/channel/category">
					<xsl:value-of select="."/><xsl:if test="position()!=last()">, </xsl:if>
				</xsl:for-each></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Managing Editor' and rss/channel/managingEditor">
				<div class="contentpos">Editor :: <xsl:value-of select="rss/channel/managingEditor"/></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Web Master' and rss/channel/webMaster">
			<div class="contentpos">WebMaster :: <a><xsl:attribute name="href">mailto:<xsl:value-of select="rss/channel/webMaster"/></xsl:attribute><xsl:value-of select="rss/channel/webMaster"/></a></div>
			</xsl:if>			
			<xsl:if test="feed/fields/field[@name='show']='Channel_Publish Date' and (rdf:RDF/rss:channel/dc:date or rss/channel/pubDate)">
			<div class="contentpos">Published :: <xsl:choose>
						<xsl:when test="rdf:RDF/rss:channel/dc:date"><xsl:variable name="date"><xsl:value-of select="rdf:RDF/rss:channel/dc:date"/></xsl:variable><xsl:value-of select="substring-before($date,'T')"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="rss/channel/pubDate"/></xsl:otherwise>
					</xsl:choose></div>
			</xsl:if>
		<!-- <hr/> -->
		</xsl:when>
		<!--
		RDF 
		-->
		<xsl:when test="rdf:RDF">
			<xsl:if test="feed/fields/field[@name='show']='Channel_Title'">
				<div class="formheader"><a><xsl:attribute name="id">jump_to_<xsl:value-of select="feed/@identifier"/></xsl:attribute></a><xsl:value-of select="rdf:RDF/rss:channel/rss:title"/></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Image'">
				<xsl:if test="rdf:RDF/rss:image">
				<div class="contentpos"><a>
					<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
						<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
					</xsl:if>
					<xsl:attribute name="href"><xsl:value-of select="rdf:RDF/rss:image/rss:link"/></xsl:attribute><img border="0">
					<xsl:attribute name="src"><xsl:value-of select="rdf:RDF/rss:image/rss:url"/></xsl:attribute>
					<xsl:attribute name="alt"><xsl:value-of select="rdf:RDF/rss:image/rss:title"/></xsl:attribute>
					<xsl:if test="rdf:RDF/rss:image/rss:width">
						<xsl:attribute name="width"><xsl:value-of select="rdf:RDF/rss:image/rss:width"/></xsl:attribute>
					</xsl:if>
					<xsl:if test="rdf:RDF/rss:image/rss:height">
						<xsl:attribute name="height"><xsl:value-of select="rdf:RDF/rss:image/rss:height"/></xsl:attribute>
					</xsl:if>
					</img></a></div>
				</xsl:if>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Description'">
				<xsl:if test="rdf:RDF/rss:channel/rss:description"><div class="contentpos"><xsl:value-of select="rdf:RDF/rss:channel/rss:description"/></div></xsl:if>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Copyright' and rdf:RDF/rss:channel/rss:copyright">
				<div class="contentpos">Copyright :: <xsl:value-of select="rdf:RDF/rss:channel/rss:copyright"/></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Last Build Date' and rdf:RDF/rss:channel/rss:lastBuildDate">
				<div class="contentpos">Last Build :: <xsl:value-of select="rdf:RDF/rss:channel/rss:lastBuildDate"/></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Categories' and rdf:RDF/rss:channel/rss:category">
				<div class="contentpos">Categories :: <xsl:for-each select="rdf:RDF/rss:channel/rss:category">
					<xsl:value-of select="."/><xsl:if test="position()!=last()">, </xsl:if>
				</xsl:for-each></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Managing Editor' and rdf:RDF/rss:channel/rss:managingEditor">
				<div class="contentpos">Editor :: <a><xsl:attribute name="href">mailto:<xsl:value-of select="rdf:RDF/rss:channel/rss:managingEditor"/></xsl:attribute><xsl:value-of select="rdf:RDF/rss:channel/rss:managingEditor"/></a></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Web Master' and rdf:RDF/rss:channel/rss:webMaster">
			<div class="contentpos">WebMaster :: <a><xsl:attribute name="href">mailto:<xsl:value-of select="rdf:RDF/rss:channel/rss:webMaster"/></xsl:attribute><xsl:value-of select="rdf:RDF/rss:channel/rss:webMaster"/></a></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Publish Date' and (rdf:RDF/rss:channel/dc:date or rdf:RDF/rss:channel/rss:pubDate)">
			<div class="contentpos">Published :: <xsl:choose>
						<xsl:when test="rdf:RDF/rss:channel/dc:date"><xsl:variable name="date"><xsl:value-of select="rdf:RDF/rss:channel/dc:date"/></xsl:variable><xsl:value-of select="substring-before($date,'T')"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="rdf:RDF/rss:channel/rss:pubDate"/></xsl:otherwise>
					</xsl:choose></div>
			</xsl:if>
			</xsl:when>
	</xsl:choose>
</xsl:template>



<xsl:template match="rss">
	<xsl:variable name="id"><xsl:value-of select="../feed/@identifier"/></xsl:variable>
	<a><xsl:attribute name="id">jump_to_<xsl:value-of select="$id"/></xsl:attribute></a>
	<xsl:variable name="num_items"><xsl:value-of select="../feed/fields/field[@name='number_of_items'] + 1"/></xsl:variable>
	<xsl:variable name="has_content"><xsl:for-each select="channel/item">
		<xsl:if test="(../../../feed/fields/field[@name='show']='Story_Description' or (title='' and link='')) and description and (title='' or description != title)">1</xsl:if>
		<xsl:if test="(../../../feed/fields/field[@name='show']='Story_Description' or (title='' and link='')) and content:encoded">1</xsl:if>
		<xsl:if test="../../../feed/fields/field[@name='show']='Story_Ticker' and ticker">1</xsl:if>
		<xsl:if test="../../../feed/fields/field[@name='show']='Story_Publish Date' and pubDate">1</xsl:if>
		<xsl:if test="../../../feed/fields/field[@name='show']='Story_Author' and author">1</xsl:if>
		<xsl:if test="../../../feed/fields/field[@name='show']='Story_Categories' and category">1</xsl:if>
	</xsl:for-each></xsl:variable>
	<xsl:if test="../feed/fields/field[@name='bulletlist']=1">
		<ul>
			<xsl:for-each select="channel/item">
				<xsl:if test="position() &lt; $num_items">
					<xsl:choose>
					<xsl:when test="$has_content!=''">
						<li class="redbullet"><a ><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>#jump_to_<xsl:value-of select="$id"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:choose>
							<xsl:when test="title=''"><xsl:value-of select="link"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="title"/></xsl:otherwise>
						</xsl:choose></a></li>
					</xsl:when>
					<xsl:otherwise>
						<li class="redbullet"><a>
							<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
								<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute><xsl:choose>
							<xsl:when test="title=''"><xsl:value-of select="link"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="title"/></xsl:otherwise>
						</xsl:choose></a></li>
					</xsl:otherwise>
					</xsl:choose>
				</xsl:if>
			</xsl:for-each>
		</ul>
	</xsl:if>
	
	<xsl:if test="$has_content!=''">
		<xsl:for-each select="channel/item">
			<xsl:if test="position() &lt; $num_items">
				<div class="Storyitem">
				<a><xsl:attribute name="id">jump_to_<xsl:value-of select="$id"/>_<xsl:value-of select="position()"/></xsl:attribute></a>
				<div class="StoryHeader"><xsl:choose>
					<xsl:when test="count(../../../feed/fields/field[@name='show'])=1 and ../../../feed/fields/field[@name='show']='Story_Title'">[[rightarrow]]</xsl:when>
					<xsl:otherwise></xsl:otherwise>
				</xsl:choose>
				<xsl:choose>
				<xsl:when test="title='' and link=''"></xsl:when>
				<xsl:otherwise>
				<a >
					<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
						<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
					</xsl:if>
					<xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute>
					<xsl:choose>
						<xsl:when test="title=''"><xsl:value-of select="link"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="title"/></xsl:otherwise>
					</xsl:choose>
				</a>
				</xsl:otherwise></xsl:choose>
				</div>
				<xsl:if test="enclosure"><div>|
					<xsl:for-each select="enclosure">
						<xsl:choose>
							<xsl:when test="substring-before(@type,'/')='audio'">
								<a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
									<img src="/libertas_images/general/buttons/actions/audio.gif" border="0"><xsl:attribute name='alt'>Listen to <xsl:choose>
									<xsl:when test="title!=''">'<xsl:value-of select="title"/>'</xsl:when>
									<xsl:otherwise>this file</xsl:otherwise>
									</xsl:choose> (<xsl:value-of select="@length"/> bytes)</xsl:attribute></img></a>
							</xsl:when>
							<xsl:otherwise>
								<a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute><img src="/libertas_images/general/buttons/actions/download.gif" border="0"><xsl:attribute name='alt'>Download <xsl:choose>
									<xsl:when test="title!=''">'<xsl:value-of select="title"/>'</xsl:when>
									<xsl:otherwise>this file</xsl:otherwise>
									</xsl:choose> (<xsl:value-of select="@length"/> bytes)</xsl:attribute></img></a>
							</xsl:otherwise>
						</xsl:choose> | </xsl:for-each>
					</div>
				</xsl:if>
				<xsl:if test="../../../feed/fields/field[@name='show']='Story_Publish Date' and pubDate">
					<div class="contentpos">Date :: <xsl:value-of select="pubDate"/></div>
				</xsl:if>
				<xsl:if test="../../../feed/fields/field[@name='show']='Story_Author' and author">
					<div class="contentpos">Author :: <xsl:value-of select="author"/></div>
				</xsl:if>
				<xsl:if test="../../../feed/fields/field[@name='show']='Story_Categories' and category">
					<div class="contentpos">Categories :: <xsl:for-each select="category">
					<xsl:value-of select="."/><xsl:if test="position()!=last()">, </xsl:if>
				</xsl:for-each></div>
				</xsl:if>
				<xsl:if test="(../../../feed/fields/field[@name='show']='Story_Description' or (title='' and link='')) and description and (title='' or description != title)"><div class="contentpos"><xsl:value-of select="description"/></div></xsl:if>
				<xsl:if test="(../../../feed/fields/field[@name='show']='Story_Description' or (title='' and link='')) and content:encoded"><div class="contentpos"><xsl:value-of select="content:encoded"/></div></xsl:if>
				<xsl:if test="../../../feed/fields/field[@name='show']='Story_Ticker' and  ticker">
					<div class="contentpos">[ <xsl:for-each select="ticker">
						<a>
							<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
								<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
							</xsl:if>

						<xsl:attribute name="title"><xsl:value-of select="@exchange"/> Quotes</xsl:attribute>
						<xsl:attribute name="href">http://quote.fool.com/uberdata.asp?symbols=<xsl:value-of select="@symbol"/></xsl:attribute><xsl:value-of select="@symbol"/></a>
						<xsl:if test="position()!=last()">, </xsl:if>
					</xsl:for-each> ]</div>
				</xsl:if>
				<xsl:if test="../../../feed/fields/field[@name='show']='Story_Comments Url' and comments">
						<div class='contentpos'>
						Comments :: 
						<a><xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
								<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="href"><xsl:value-of select="comments"/></xsl:attribute><xsl:value-of select="comments"/></a>
						</div>
				</xsl:if>
				<xsl:if test="(../../../feed/fields/field[@name='show']='Story_Description' or (title='' and link='')) and ((description and (title='' or description != title)) or content:encoded)">
				<div class="readmore"><a class="headlines"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>#jump_to_<xsl:value-of select="$id"/></xsl:attribute>Back to top</a></div>
	            <!-- <hr/> -->
				</xsl:if>
			</div>
			</xsl:if>
		</xsl:for-each>
	</xsl:if>
</xsl:template>
<!--
<xsl:template match="RDF">
	<xsl:variable name="id"><xsl:value-of select="../feed/@identifier"/></xsl:variable>
	<a><xsl:attribute name="id">jump_to_<xsl:value-of select="$id"/></xsl:attribute></a>
	<xsl:variable name="num_items"><xsl:value-of select="../feed/fields/field[@name='number_of_items'] + 1"/></xsl:variable>
	<xsl:variable name="has_content"><xsl:for-each select="channel/item">
		<xsl:if test="../../../feed/fields/field[@name='show']='Story_Description' and description and description != title">1</xsl:if>
		<xsl:if test="../../../feed/fields/field[@name='show']='Story_Description' and content:encoded">1</xsl:if>
		<xsl:if test="../../../feed/fields/field[@name='show']='Story_Ticker' and ticker">1</xsl:if>
		<xsl:if test="../../../feed/fields/field[@name='show']='Story_Publish Date' and pubDate">1</xsl:if>
		<xsl:if test="../../../feed/fields/field[@name='show']='Story_Author' and author">1</xsl:if>
		<xsl:if test="../../../feed/fields/field[@name='show']='Story_Categories' and category">1</xsl:if>
	</xsl:for-each></xsl:variable>
<ul>
	<xsl:for-each select="channel/item">
			<xsl:if test="position() &lt; $num_items">
		<li class="redbullet"><a target="_open_in_external_window"><xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute><xsl:choose>
			<xsl:when test="title=''"><xsl:value-of select="link"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="title"/></xsl:otherwise>
		</xsl:choose></a><xsl:if test="description and description != title"><br/><xsl:value-of select="description"/></xsl:if></li>
	</xsl:if>
	</xsl:for-each>
</ul>
</xsl:template>
-->
<xsl:template match="rdf:RDF">
	<xsl:variable name="id"><xsl:value-of select="../feed/@identifier"/></xsl:variable>
	<xsl:variable name="has_content"><xsl:for-each select="rss:item">
		<xsl:if test="../feed/fields/field[@name='show']='Story_Description' and rss:description and rss:description != title">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Description' and content:encoded">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Ticker' and rss:ticker">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Publish Date' and rss:pubDate">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Author' and rss:author">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Categories' and rss:category">1</xsl:if>
	</xsl:for-each></xsl:variable>
	<xsl:variable name="num_items"><xsl:value-of select="../feed/fields/field[@name='number_of_items'] + 1"/></xsl:variable>
	<a><xsl:attribute name="id">jump_to_<xsl:value-of select="$id"/></xsl:attribute></a>
	<xsl:if test="../feed/fields/field[@name='bulletlist']=1 and $has_content!=''">
		<ul>
			<xsl:for-each select="rss:item">
				<xsl:if test="position() &lt; $num_items">
		   		<li class="redbullet"><a class="headlines">        
	                <xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>#jump_to_<xsl:value-of select="$id"/>_<xsl:value-of select="position()"/></xsl:attribute>        
                    <xsl:value-of select="rss:title"/>        
                </a></li>
				</xsl:if>
			</xsl:for-each>
		</ul>
	</xsl:if>
	<xsl:choose>
		<xsl:when test="$has_content=''">
			<ul>
			    <xsl:for-each select="rss:item">    
					<xsl:if test="position() &lt; $num_items">
						<li class="redbullet"><a>
							<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
								<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="href"><xsl:value-of select="rss:link"/></xsl:attribute><xsl:choose>
								<xsl:when test="rss:title=''"><xsl:value-of select="rss:link"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="rss:title"/></xsl:otherwise>
						</xsl:choose></a></li>
					</xsl:if>
				</xsl:for-each>
			</ul>
		</xsl:when>
		<xsl:otherwise>
			<xsl:for-each select="rss:item">    
				<xsl:if test="position() &lt; $num_items">
					<div class="Storyitem"><xsl:choose>
					<xsl:when test="count(../../../feed/fields/field[@name='show'])=1 and ../../../feed/fields/field[@name='show']='Story_Title'">[[rightarrow]]</xsl:when>
					<xsl:otherwise></xsl:otherwise>
				</xsl:choose>
						<a><xsl:attribute name="id">jump_to_<xsl:value-of select="$id"/>_<xsl:value-of select="position()"/></xsl:attribute>
						<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
						<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
						</xsl:if>
						<xsl:attribute name="href"><xsl:value-of select="rss:link"/></xsl:attribute><div class="StoryHeader"><xsl:value-of select="rss:title"/></div></a>
		<!--		   	More Info on '<xsl:value-of select="rss:title"/>' -->
						<xsl:if test="enclosure"><div>|
							<xsl:for-each select="rss:enclosure">
								<xsl:choose>
									<xsl:when test="substring-before(@type,'/')='audio'">
										<a>
											<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
												<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
											</xsl:if>
											<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
											<img src="/libertas_images/general/buttons/actions/audio.gif" border="0"><xsl:attribute name='alt'>Listen to <xsl:choose>
											<xsl:when test="rss:title!=''">'<xsl:value-of select="rss:title"/>'</xsl:when>
											<xsl:otherwise>this file</xsl:otherwise>
											</xsl:choose> (<xsl:value-of select="@length"/> bytes)</xsl:attribute></img></a>
									</xsl:when>
									<xsl:otherwise>
										<a>
											<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
												<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
											</xsl:if>
											<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute><img src="/libertas_images/general/buttons/actions/download.gif" border="0"><xsl:attribute name='alt'>Download <xsl:choose>
											<xsl:when test="rss:title!=''">'<xsl:value-of select="rss:title"/>'</xsl:when>
											<xsl:otherwise>this file</xsl:otherwise>
											</xsl:choose> (<xsl:value-of select="@length"/> bytes)</xsl:attribute></img></a>
									</xsl:otherwise>
								</xsl:choose> | </xsl:for-each>
							</div>
						</xsl:if>
		    			<xsl:if test="../../feed/fields/field[@name='show']='Story_Publish Date'">
		                <div class='contentpos'>Date :: <xsl:choose>
								<xsl:when test="dc:date"><xsl:variable name="date"><xsl:value-of select="dc:date"/></xsl:variable><xsl:value-of select="substring-before($date,'T')"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="pubDate"/></xsl:otherwise>
							</xsl:choose></div>
		    			</xsl:if>
		    			<xsl:if test="../../feed/fields/field[@name='show']='Story_Author'">
			                <div class='contentpos'>Author :: <xsl:choose>
								<xsl:when test="dc:creator"><xsl:value-of select="dc:creator"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="author"/></xsl:otherwise>
							</xsl:choose></div>
		    			</xsl:if>
		   				<xsl:if test="../../feed/fields/field[@name='show']='Story_Description'">
			        	    <div class='contentpos'>        
		    	        	    <xsl:value-of disable-output-escaping="yes" select="rss:description"/>
			        	    </div>    
						</xsl:if>
						<xsl:if test="feed/fields/field[@name='show']='Story_Comments Url' and rss:comments">
								<div class='contentpos'>
								Comments :: 
								<a>
									<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
										<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
									</xsl:if>
									<xsl:attribute name="href"><xsl:value-of select="rss:comments"/></xsl:attribute><xsl:value-of select="rss:comments"/></a>
								</div>
						</xsl:if>
		   				<xsl:if test="../../feed/fields/field[@name='show']='Story_Description'">
							<div class="readmore"><a class="headlines"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>#jump_to_<xsl:value-of select="$id"/></xsl:attribute>Back to top</a></div>
				            <!-- <hr/> -->
						</xsl:if>
					</div>
				</xsl:if>
			</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>


