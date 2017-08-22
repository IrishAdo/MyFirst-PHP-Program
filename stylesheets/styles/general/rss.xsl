<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.7 $
- Modified $Date: 2005/03/03 15:59:06 $
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

<xsl:template name="replace_string">
	<xsl:param name="str_value"></xsl:param>
	<xsl:param name="find">&amp;#39;</xsl:param>
	<xsl:param name="replace_with">'</xsl:param>
	<xsl:choose>
		<xsl:when test="contains($str_value,$find)"><xsl:call-template name="replace_string">
			<xsl:with-param name="str_value"><xsl:value-of select="substring-before($str_value,$find)"/><xsl:value-of select="$replace_with"/><xsl:value-of select="substring-after($str_value,$find)"/></xsl:with-param>
			<xsl:with-param name="find"><xsl:value-of select="$find"/></xsl:with-param>
			<xsl:with-param name="replace_with"><xsl:value-of select="$replace_with"/></xsl:with-param>
		</xsl:call-template></xsl:when>
		<xsl:otherwise><xsl:value-of select="$str_value"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="print">
	<xsl:param name="str_value"></xsl:param>
	<xsl:call-template name="replace_string">
		<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
		<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
			<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
				<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
					<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
						<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
							<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
								<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
									<xsl:with-param name="str_value"><xsl:call-template name="replace_string">
										<xsl:with-param name="str_value"><xsl:value-of select="$str_value" disable-output-escaping="yes"/></xsl:with-param>
									<xsl:with-param name="find">&amp;amp;</xsl:with-param><xsl:with-param name="replace_with">&amp;</xsl:with-param></xsl:call-template></xsl:with-param>
								<xsl:with-param name="find">&amp;#8230;</xsl:with-param><xsl:with-param name="replace_with">&#8230;</xsl:with-param></xsl:call-template></xsl:with-param>
							<xsl:with-param name="find">&amp;#163;</xsl:with-param><xsl:with-param name="replace_with">&#163;</xsl:with-param></xsl:call-template></xsl:with-param>
						<xsl:with-param name="find">&amp;#153;</xsl:with-param><xsl:with-param name="replace_with">&#8482;</xsl:with-param></xsl:call-template></xsl:with-param>
					<xsl:with-param name="find">&amp;#8482;</xsl:with-param><xsl:with-param name="replace_with">&#8482;</xsl:with-param></xsl:call-template></xsl:with-param>
				<xsl:with-param name="find">&amp;#169;</xsl:with-param><xsl:with-param name="replace_with">&#169;</xsl:with-param></xsl:call-template></xsl:with-param>
			<xsl:with-param name="find">&amp;#174;</xsl:with-param><xsl:with-param name="replace_with">&#174;</xsl:with-param></xsl:call-template></xsl:with-param>
		<xsl:with-param name="find">&amp;quot;</xsl:with-param><xsl:with-param name="replace_with">'</xsl:with-param></xsl:call-template></xsl:with-param>
	<xsl:with-param name="find">"</xsl:with-param><xsl:with-param name="replace_with">'</xsl:with-param></xsl:call-template></xsl:with-param>
	</xsl:call-template>
</xsl:template>

 
<xsl:template name="rssChannel">
	<xsl:choose>
		<xsl:when test="rss">
			<xsl:if test="feed/fields/field[@name='show']='Channel_Title'">
				<xsl:choose>
					<xsl:when test="feed/fields/field[@name='override_channel_title']=1">
						<div class="rsslabel"><a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
							<xsl:attribute name="href"><xsl:value-of select="rss/channel/link"/></xsl:attribute>
							<span class='icon'><span class='text'><xsl:value-of select="feed/fields/field[@name='label']"/></span></span></a></div>
					</xsl:when>
					<xsl:otherwise>
						<div class="rsslabel"><a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="href"><xsl:value-of select="rss/channel/link"/></xsl:attribute>
								<span class='icon'><span class='text'><xsl:value-of select="rss/channel/title"/></span></span></a></div>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
			
			<xsl:if test="feed/fields/field[@name='show']='Channel_Image'">
				<xsl:if test="rss/channel/image">
					<div class="image"><a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
					<xsl:attribute name="href"><xsl:value-of select="rss/channel/image/link"/></xsl:attribute><img>
						<xsl:attribute name="src"><xsl:value-of select="rss/channel/image/url"/></xsl:attribute>
						<xsl:attribute name="alt"><xsl:value-of select="rss/channel/image/title"/></xsl:attribute>
						<xsl:attribute name="style">
							<xsl:if test="rss/channel/image/width">width:<xsl:value-of select="rss/channel/image/width"/>;</xsl:if>
							<xsl:if test="rss/channel/image/height">height:<xsl:value-of select="rss/channel/image/height"/>;</xsl:if>
						</xsl:attribute>
					</img></a></div>
				</xsl:if>
			</xsl:if>
			<xsl:if test="(feed/fields/field[@name='show']='Channel_Description' and rss/channel/description ) or (feed/fields/field[@name='show']='Channel_Copyright' and rss/channel/copyright) or (feed/fields/field[@name='show']='Channel_Managing Editor' and rss/channel/managingEditor) or (feed/fields/field[@name='show']='Channel_Last Build Date' and rss/channel/lastBuildDate ) or (feed/fields/field[@name='show']='Channel_Publish Date' and (rdf:RDF/rss:channel/dc:date or rss/channel/pubDate) ) or (feed/fields/field[@name='show']='Channel_Web Master' and rss/channel/webMaster)">
				<ul class='meta'>
					<xsl:if test="feed/fields/field[@name='show']='Channel_Description' and rss/channel/description">
						<li class='meta'><span><xsl:value-of select="rss/channel/description"/></span></li>
					</xsl:if>
					<xsl:if test="feed/fields/field[@name='show']='Channel_Copyright' and rss/channel/copyright">
						<li class='meta'><span><xsl:value-of select="rss/channel/copyright"/></span></li>
					</xsl:if>
					<xsl:if test="feed/fields/field[@name='show']='Channel_Managing Editor' and rss/channel/managingEditor">
						<li class='meta'><span><xsl:value-of select="rss/channel/managingEditor"/></span></li>
					</xsl:if>
					<xsl:if test="feed/fields/field[@name='show']='Channel_Categories' and rss/channel/category">
						<li class='meta'><span><xsl:for-each select="rss/channel/category">
							<xsl:value-of select="."/><xsl:if test="position()!=last()">, </xsl:if>
						</xsl:for-each></span></li>
					</xsl:if>
					<xsl:if test="feed/fields/field[@name='show']='Channel_Last Build Date' and rss/channel/lastBuildDate">
						<li class='meta'><span><xsl:call-template name="format_date">
								<xsl:with-param name="current_date"><xsl:value-of select="rss/channel/lastBuildDate"/></xsl:with-param>
								<xsl:with-param name="output_format"><xsl:value-of select="//settings[@name='sp_default_time_format']"/></xsl:with-param>
						</xsl:call-template></span></li>
					</xsl:if>
					<xsl:if test="feed/fields/field[@name='show']='Channel_Publish Date' and (rdf:RDF/rss:channel/dc:date or rss/channel/pubDate)">
						<li class='meta'><span><xsl:choose>
							<xsl:when test="rdf:RDF/rss:channel/dc:date"><xsl:variable name="date"><xsl:value-of select="rdf:RDF/rss:channel/dc:date"/></xsl:variable><xsl:value-of select="substring-before($date,'T')"/></xsl:when>
							<xsl:otherwise><xsl:call-template name="format_date">
								<xsl:with-param name="current_date"><xsl:value-of select="rss/channel/pubDate"/></xsl:with-param>
								<xsl:with-param name="output_format"><xsl:value-of select="//settings[@name='sp_default_time_format']"/></xsl:with-param>
						</xsl:call-template></xsl:otherwise>
						</xsl:choose></span></li>
					</xsl:if>
					<xsl:if test="feed/fields/field[@name='show']='Channel_Web Master' and rss/channel/webMaster">
						<li class='meta'><span><a><xsl:attribute name="href">mailto:<xsl:value-of select="rss/channel/webMaster"/></xsl:attribute><xsl:value-of select="rss/channel/webMaster"/></a></span></li>
					</xsl:if>			
				</ul>
			</xsl:if>			
			<ul class="rss">
				<xsl:apply-templates select="rss"/>
			</ul>
		</xsl:when>
		<!--
		RDF 
		-->
		<xsl:when test="rdf:RDF">
			<xsl:if test="feed/fields/field[@name='show']='Channel_Title'">
				<xsl:choose>
					<xsl:when test="feed/fields/field[@name='override_channel_title']=1">
						<div class="rsslabel"><span class='icon'><span class='text'><xsl:value-of select="feed/fields/field[@name='label']"/></span></span></div>
					</xsl:when>
					<xsl:otherwise>
						<div class="rsslabel"><span class='icon'><span class='text'><xsl:value-of select="rdf:RDF/rss:channel/rss:title"/></span></span></div>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Image'">
				<xsl:if test="rdf:RDF/rss:image">
					<div class="channel"><a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
					<xsl:attribute name="href"><xsl:value-of select="rdf:RDF/rss:image/rss:link"/></xsl:attribute><img>
						<xsl:attribute name="src"><xsl:value-of select="rdf:RDF/rss:image/rss:url"/></xsl:attribute>
						<xsl:attribute name="alt"><xsl:value-of select="rdf:RDF/rss:image/rss:title"/></xsl:attribute>
						<xsl:attribute name="style">
							<xsl:if test="rdf:RDF/rss:image/rss:width">width:<xsl:value-of select="rdf:RDF/rss:image/rss:width"/>;</xsl:if>
							<xsl:if test="rdf:RDF/rss:image/rss:height">height:<xsl:value-of select="rdf:RDF/rss:image/rss:height"/>;</xsl:if>
						</xsl:attribute>
					</img></a></div>
				</xsl:if>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Description' and rdf:RDF/rss:channel/rss:description">
				<div class="contentpos"><span><xsl:value-of select="rdf:RDF/rss:channel/rss:description"/></span></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Copyright' and rdf:RDF/rss:channel/rss:copyright">
				<div class="contentpos"><span>Copyright :: <xsl:value-of select="rdf:RDF/rss:channel/rss:copyright"/></span></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Managing Editor' and rdf:RDF/rss:channel/rss:managingEditor">
				<div class="contentpos"><span>Editor :: <a><xsl:attribute name="href">mailto:<xsl:value-of select="rdf:RDF/rss:channel/rss:managingEditor"/></xsl:attribute><xsl:value-of select="rdf:RDF/rss:channel/rss:managingEditor"/></a></span></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Categories' and rdf:RDF/rss:channel/rss:category">
				<div class="contentpos"><span>Categories :: <xsl:for-each select="rdf:RDF/rss:channel/rss:category">
					<xsl:value-of select="."/><xsl:if test="position()!=last()">, </xsl:if>
				</xsl:for-each></span></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Last Build Date' and rdf:RDF/rss:channel/rss:lastBuildDate">
				<div class="contentpos"><span>Last Build :: <xsl:call-template name="format_date">
								<xsl:with-param name="current_date"><xsl:value-of select="rdf:RDF/rss:channel/rss:lastBuildDate"/></xsl:with-param>
								<xsl:with-param name="output_format"><xsl:value-of select="//settings[@name='sp_default_time_format']"/></xsl:with-param>
						</xsl:call-template></span></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Publish Date' and (rdf:RDF/rss:channel/dc:date or rdf:RDF/rss:channel/rss:pubDate)">
				<div class="contentpos"><span>Published :: <xsl:choose>
					<xsl:when test="rdf:RDF/rss:channel/dc:date"><xsl:variable name="date"><xsl:value-of select="rdf:RDF/rss:channel/dc:date"/></xsl:variable><xsl:value-of select="substring-before($date,'T')"/></xsl:when>
					<xsl:otherwise><xsl:call-template name="format_date">
								<xsl:with-param name="current_date"><xsl:value-of select="rdf:RDF/rss:channel/rss:pubDate"/></xsl:with-param>
								<xsl:with-param name="output_format"><xsl:value-of select="//settings[@name='sp_default_time_format']"/></xsl:with-param>
						</xsl:call-template></xsl:otherwise>
				</xsl:choose></span></div>
			</xsl:if>
			<xsl:if test="feed/fields/field[@name='show']='Channel_Web Master' and rdf:RDF/rss:channel/rss:webMaster">
				<div class="contentpos"><span>WebMaster :: <a><xsl:attribute name="href">mailto:<xsl:value-of select="rdf:RDF/rss:channel/rss:webMaster"/></xsl:attribute><xsl:value-of select="rdf:RDF/rss:channel/rss:webMaster"/></a></span></div>
			</xsl:if>
			<ul class="rss">
				<xsl:apply-templates select="*"/>
			</ul>
		</xsl:when>
	</xsl:choose>
</xsl:template>


<xsl:template match="settings">
</xsl:template>
<xsl:template match="feed">
</xsl:template>

<xsl:template match="rss">
	<xsl:variable name="id"><xsl:value-of select="../feed/@identifier"/></xsl:variable>
	<!--
	<a><xsl:attribute name="name">#jump_to_<xsl:value-of select="$id"/></xsl:attribute></a>
	-->
	<xsl:variable name="num_items"><xsl:choose>
		<xsl:when test="../feed/fields/field[@name='number_of_items']=0"><xsl:value-of select="count(channel/item) + 1"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="../feed/fields/field[@name='number_of_items'] + 1"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="has_content">
		<xsl:if test="(../feed/fields/field[@name='show']='Story_Description'	or (title='' and link=''))	and boolean(channel/item/description) and (channel/item/description != channel/item/title or title='')">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Description'	and boolean(channel/item/description) and (channel/item/description != channel/item/title)">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Description'	and boolean(channel/item/content:encoded)">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Title'	 		and boolean(channel/item/title)">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Ticker' 		and boolean(channel/item/ticker)">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Publish Date'	and boolean(channel/item/pubDate)">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Author' 		and boolean(channel/item/author)">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Categories'		and boolean(channel/item/category)">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Attachments'	and boolean(channel/item/enclosure)">1</xsl:if>
	</xsl:variable>
	<xsl:if test="$has_content!=''">
		<xsl:for-each select="channel/item">
			<xsl:if test="position() &lt; $num_items">
				<li>
					<xsl:attribute name="class">withsummary</xsl:attribute>
					<xsl:if test="../../../feed/fields/field[@name='show']='Story_Title'"><span>
						<a >
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="title"><xsl:choose>
									<xsl:when test="not(../../../feed/fields/field[@name='show']='Story_Description')">
									<xsl:call-template name="print">
										<xsl:with-param name="str_value"><xsl:value-of select="description"/></xsl:with-param>
									</xsl:call-template></xsl:when>
									<xsl:otherwise>Read more about <xsl:value-of select="title"/></xsl:otherwise>
								</xsl:choose></xsl:attribute>
								<xsl:attribute name="href"><xsl:value-of select="link"/></xsl:attribute><xsl:choose>
								<xsl:when test="title=''"><xsl:value-of select="link"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="title"/></xsl:otherwise>
						</xsl:choose></a></span>
					</xsl:if>
					<xsl:if test="../feed/fields/field[@name='show']='Story_Attachments' and enclosure">
						<ul class="enclosure">
							<xsl:for-each select="enclosure">
								<xsl:choose>
									<xsl:when test="substring-before(@type,'/')='audio'">
										<li class='audio'><a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
										<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
											<span>Listen to <xsl:choose>
												<xsl:when test="title!=''">'<xsl:value-of select="title"/>'</xsl:when>
												<xsl:otherwise>this file</xsl:otherwise>
											</xsl:choose> (<xsl:value-of select="@length"/> bytes)</span></a></li>
									</xsl:when>
									<xsl:otherwise>
										<li class='file'><a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
										<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute>
										<span>Download <xsl:choose>
											<xsl:when test="title!=''">'<xsl:value-of select="title"/>'</xsl:when>
											<xsl:otherwise>this file</xsl:otherwise>
										</xsl:choose> (<xsl:value-of select="@length"/> bytes)</span></a></li>
									</xsl:otherwise>
								</xsl:choose>
							</xsl:for-each>
						</ul>
					</xsl:if>
					<xsl:if test="../../../feed/fields/field[@name='show']='Story_Publish Date' and pubDate">
						<div class='contentpos'><span class='date'><xsl:call-template name="format_date">
								<xsl:with-param name="current_date"><xsl:value-of select="pubDate"/></xsl:with-param>
								<xsl:with-param name="output_format"><xsl:value-of select="//settings[@name='sp_default_time_format']"/></xsl:with-param>
						</xsl:call-template></span></div>
					</xsl:if>
					<xsl:if test="../../../feed/fields/field[@name='show']='Story_Author' and author">
						<div class='contentpos'><span class='author'>Author :: <xsl:value-of select="author"/></span></div>
					</xsl:if>
					<xsl:if test="../../../feed/fields/field[@name='show']='Story_Categories' and category">
						<div class='contentpos'><span>Categories :: <xsl:for-each select="category">
							<xsl:value-of select="."/><xsl:if test="position()!=last()">, </xsl:if>
						</xsl:for-each></span></div>
					</xsl:if>
					<xsl:if test="(../../../feed/fields/field[@name='show']='Story_Description' or (title='' and link='')) and description and (description != title or title='')"><div class='contentpos'><span><xsl:value-of select="description"/></span></div></xsl:if>
					<xsl:if test="../../../feed/fields/field[@name='show']='Story_Description' and content:encoded"><div class='contentpos'><span><xsl:value-of select="content:encoded"/></span></div></xsl:if>
					<xsl:if test="../../../feed/fields/field[@name='show']='Story_Ticker' and  ticker">
						<ul class='ticker'><xsl:for-each select="ticker">
							<li><a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
								<xsl:attribute name="title"><xsl:value-of select="@exchange"/> Quotes</xsl:attribute>
								<xsl:attribute name="href">http://quote.fool.com/uberdata.asp?symbols=<xsl:value-of select="@symbol"/></xsl:attribute><span><xsl:value-of select="@symbol"/></span></a></li>
							</xsl:for-each>
						</ul>
					</xsl:if>
					<xsl:if test="../../../feed/fields/field[@name='show']='Story_Comments Url' and comments">
						<div class='contentpos'><span>
						Comments :: 
						<a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
						<xsl:attribute name="href"><xsl:value-of select="comments"/></xsl:attribute><xsl:value-of select="comments"/></a>
						</span></div>
					</xsl:if>
				</li>
			</xsl:if>
		</xsl:for-each>
	</xsl:if>
</xsl:template>
<xsl:template match="rdf:RDF">
	<xsl:variable name="id"><xsl:value-of select="../feed/@identifier"/></xsl:variable>
	<xsl:variable name="has_content">
		<xsl:if test="../feed/fields/field[@name='show']='Story_Description'	and rss:item/rss:description and rss:item/rss:description != rss:item/rss:title">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Description'	and rss:item/content:encoded">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Ticker' 		and rss:item/rss:ticker">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Publish Date'	and rss:item/rss:pubDate">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Author' 		and rss:item/rss:author">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Categories' 	and rss:item/rss:category">1</xsl:if>
		<xsl:if test="../feed/fields/field[@name='show']='Story_Title'	 		and rss:item/rss:title">1</xsl:if>
	</xsl:variable>
	<xsl:variable name="num_items"><xsl:choose>
		<xsl:when test="../feed/fields/field[@name='number_of_items']=0"><xsl:value-of select="count(rss:item)"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="../feed/fields/field[@name='number_of_items'] + 1"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:choose>
		<xsl:when test="$has_content=''">
			    <xsl:for-each select="rss:item">    
					<xsl:if test="position() &lt; $num_items">
						<li>
							<xsl:attribute name="class">storyitem</xsl:attribute>
							<a target="_open_in_external_window">
								<xsl:attribute name="title"><xsl:choose>
									<xsl:when test="not(../../feed/fields/field[@name='show']='Story_Description')"><xsl:call-template name="print">
	<xsl:with-param name="str_value"><xsl:value-of select="rss:description"/></xsl:with-param></xsl:call-template></xsl:when>
									<xsl:otherwise>Read more about <xsl:value-of select="rss:title"/></xsl:otherwise>
								</xsl:choose></xsl:attribute>
								<xsl:attribute name="href"><xsl:value-of select="rss:link"/></xsl:attribute><xsl:choose>
									<xsl:when test="rss:title=''"><xsl:value-of select="rss:link"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="rss:title"/></xsl:otherwise>
						</xsl:choose></a></li>
					</xsl:if>
				</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
			<xsl:for-each select="rss:item">    
				<xsl:if test="position() &lt; $num_items">
					<li>
						<xsl:attribute name="class">withsummary</xsl:attribute>
						<a >
						<xsl:attribute name="title"><xsl:choose>
									<xsl:when test="not(../../feed/fields/field[@name='show']='Story_Description')"><xsl:call-template name="print">
	<xsl:with-param name="str_value"><xsl:value-of select="rss:description"/></xsl:with-param></xsl:call-template></xsl:when>
									<xsl:otherwise>Read more about <xsl:value-of select="rss:title"/></xsl:otherwise>
								</xsl:choose></xsl:attribute>
						<xsl:attribute name="href"><xsl:value-of select="rss:link"/></xsl:attribute><span><xsl:value-of select="rss:title"/></span></a>
						<xsl:if test="enclosure"><ul class="enclosure">
							<xsl:for-each select="rss:enclosure">
								<xsl:choose>
									<xsl:when test="substring-before(@type,'/')='audio'">
										<li class="audio"><a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
										<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute><span>
											Listen to <xsl:choose>
											<xsl:when test="rss:title!=''">'<xsl:value-of select="rss:title"/>'</xsl:when>
											<xsl:otherwise>this file</xsl:otherwise>
											</xsl:choose> (<xsl:value-of select="@length"/> bytes)</span></a></li>
									</xsl:when>
									<xsl:otherwise>
										<li class="file"><a>
								<xsl:if test="//setting[@name='sp_open_rss_external']='Yes'">
									<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
								</xsl:if>
										<xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute><span>
										Download <xsl:choose>
											<xsl:when test="rss:title!=''">'<xsl:value-of select="rss:title"/>'</xsl:when>
											<xsl:otherwise>this file</xsl:otherwise>
											</xsl:choose> (<xsl:value-of select="@length"/> bytes)</span></a></li>
									</xsl:otherwise>
								</xsl:choose></xsl:for-each>
							</ul>
						</xsl:if>
		    			<xsl:if test="../../feed/fields/field[@name='show']='Story_Publish Date'">
		                <div class='contentpos'><span><xsl:choose>
								<xsl:when test="dc:date"><xsl:variable name="date"><xsl:value-of select="dc:date"/></xsl:variable><xsl:value-of select="substring-before($date,'T')"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="pubDate"/></xsl:otherwise>
							</xsl:choose></span></div>
		    			</xsl:if>
		    			<xsl:if test="../../feed/fields/field[@name='show']='Story_Author'">
			                <div class='contentpos'><span>Author :: <xsl:choose>
								<xsl:when test="dc:creator"><xsl:value-of select="dc:creator"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="author"/></xsl:otherwise>
							</xsl:choose></span></div>
		    			</xsl:if>
		   				<xsl:if test="../../feed/fields/field[@name='show']='Story_Description'">
			        	    <div class='contentpos'><span><xsl:value-of disable-output-escaping="yes" select="rss:description"/></span></div>    
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
					</li>
				</xsl:if>
			</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

</xsl:stylesheet>


