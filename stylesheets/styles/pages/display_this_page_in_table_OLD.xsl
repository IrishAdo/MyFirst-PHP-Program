<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/09/11 10:03:11 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:include href="display_comments.xsl"/>

<!--
generic Page formatting
-->
<xsl:template name="display_this_page_in_table">
	<xsl:param name="summary">0</xsl:param>
	<xsl:param name="content">0</xsl:param>
	<xsl:param name="author">0</xsl:param>
	<xsl:param name="page_title">0</xsl:param>
	<xsl:param name="alt_read_more"></xsl:param>
	<xsl:param name="title">1</xsl:param>
	<xsl:param name="alt_title">0</xsl:param>
	<xsl:param name="source">0</xsl:param>
	<xsl:param name="contributors">0</xsl:param>
	<xsl:param name="date_publish">0</xsl:param>
	<xsl:param name="date_modified">0</xsl:param>
	<xsl:param name="subject_category">0</xsl:param>
	<xsl:param name="audience">0</xsl:param>
	<xsl:param name="top_of_doc">0</xsl:param>
	<xsl:param name="back">0</xsl:param>
	<xsl:param name="show_btn">1</xsl:param>
	<xsl:param name="more">1</xsl:param>
	<xsl:param name="enable_discussion">0</xsl:param>
	<xsl:param name="style">ENTRY</xsl:param>
	<xsl:param name="identifier">-1</xsl:param>
	<xsl:param name="save_to_bookmark"></xsl:param>
	<xsl:param name="file_location">bottom</xsl:param>
	<xsl:param name="title_bullet"><xsl:value-of select="$title_bullet"/></xsl:param>
	<xsl:param name="title_starter">[[rightarrow]]</xsl:param>
	<xsl:param name="start_on_title_only">1</xsl:param>
	
	<!-- Page Anchor -->
	<xsl:for-each select="//modules/container/webobject/module[@display!='LATEST']/page[@identifier=$identifier]">
		<a><xsl:attribute name="name">page_<xsl:value-of select="@identifier" disable-output-escaping="yes"/></xsl:attribute></a>
		<div id='table'>
		<xsl:choose>
		<xsl:when test="contains(../../../@width,'px')">
			<xsl:attribute name="class">width<xsl:value-of select="../../../@width"/></xsl:attribute>
		</xsl:when>
		<xsl:otherwise>
			<xsl:attribute name="class">width<xsl:value-of select="substring-before(../../../@width,'%')"/>percent</xsl:attribute>
		</xsl:otherwise>
		</xsl:choose>
		<xsl:attribute name="summary">This table holds the content of the article '<xsl:value-of select="title" disable-output-escaping="yes"/>'</xsl:attribute>
			<xsl:if test="$title=0 and $show_btn=1"><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template></xsl:if>
			<xsl:if test="$title=1"><div id='row'><div id='cell'><h1><xsl:attribute name="class"><xsl:choose>
					<xsl:when test="$style='LOCATION'">entrylocation</xsl:when>
					<xsl:when test="$style!='ENTRY'"><xsl:value-of select="$style"/></xsl:when>
					<xsl:otherwise>entrytitle</xsl:otherwise>
				</xsl:choose></xsl:attribute>
				<xsl:if test="$title_bullet!=0">
					<img border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/title_bullet.gif</xsl:attribute></img>[[nbsp]]
				</xsl:if>
				<xsl:if test="$start_on_title_only=1 and ($title=1 and $date_publish=0 and $summary=0 and $content=0 and $title_bullet=0)">
					<xsl:value-of select="$title_starter" disable-output-escaping="yes"/>
				</xsl:if>
				<xsl:value-of select="title" disable-output-escaping="yes"/>
				<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
				<xsl:if test="$date_publish=1 and metadata/date[@refinement='available']!=''">
					<xsl:call-template name="format_date">
						<xsl:with-param name="current_date"><xsl:value-of select="substring-before(metadata/date[@refinement='creation'],' ')"/></xsl:with-param>
					</xsl:call-template>
				</xsl:if></h1><xsl:if test="$date_modified!=0">
				<br/><div class="datemodified">Page last modified - <xsl:call-template name="format_date">
						<xsl:with-param name="current_date"><xsl:value-of select="substring-before(metadata/date[@refinement='modified'],' ')"/></xsl:with-param>
					</xsl:call-template></div>
				</xsl:if>
			</div></div>
<!--
			<xsl:if test="count(//modules/module[@display='ENTRY']/page)=1"><tr><td align="right"><xsl:attribute name="class"><xsl:choose>
					<xsl:when test="$style='LOCATION'">entrylocation</xsl:when>
					<xsl:otherwise>entrytitle</xsl:otherwise>
				</xsl:choose></xsl:attribute>
			</div></div></xsl:if>
			-->
			</xsl:if>
			
		<!-- summary -->
		<xsl:if test="$summary=1"><div id='row'><div id='cell' class='contentpos'><xsl:value-of select="summary" disable-output-escaping="yes"/></div></div></xsl:if>
		<!-- content -->
		<xsl:if test="$content=1">
			<div id='row'><div id='cell' class='contentpos'><xsl:choose>
				<xsl:when test="files/file and $file_location='right'">
					<div id='table' align="right">
						<div id='row'><div id='cell'><xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="content" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></div>
						<div id='cell'><xsl:call-template name="display_files"/></div></div>
					</div>
				</xsl:when>
				<xsl:otherwise><xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="content" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:otherwise>
			</xsl:choose></div></div>
			<xsl:if test="files/file and $file_location='bottom'">
				<div id='row'><div id='cell' class='contentpos'><xsl:call-template name="display_files"><xsl:with-param name="file_download_style"><xsl:value-of select="$file_download_style"/></xsl:with-param></xsl:call-template></div></div>
			</xsl:if>
		</xsl:if>
		<xsl:if test="($alt_title=-1 and metadata/alternative!='')
						or ($author='1' and count(metadata/creator)>0)
					  	or ($contributors='1' and count(metadata/contributor)>0)
						or ($source=1 and metadata/source!='')
						or ($audience=1 and metadata/audience!='')
						or ($subject_category=1 and metadata/subject[@refinement='category']!='')
		">
		<xsl:if test="$alt_title=1 and metadata/alternative!=''"><div id='row'><div id='cell' class="metadetails"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_ALT_TITLE'"/></xsl:call-template><br/><xsl:value-of select="metadata/alternative" disable-output-escaping="yes" /></div></div></xsl:if>
		<!-- authors -->
		<xsl:if test="$author='1' and count(metadata/creator)>0"><div id='row'><div id='cell'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_AUTHORS'"/></xsl:call-template><br/><xsl:for-each select="metadata/creator">
			<xsl:value-of select="." disable-output-escaping="yes" /><br/>
		</xsl:for-each>
		</div></div></xsl:if>
		<!-- contributors -->
		<xsl:if test="$contributors='1' and count(metadata/contributor)>0"><div id='row'><div id='cell' class="metadetails"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CONTRIBUTORS'"/></xsl:call-template><br/><xsl:for-each select="metadata/contributor">
			<xsl:value-of select="." disable-output-escaping="yes" /><br/>
		</xsl:for-each>
		</div></div></xsl:if>
		<!-- source -->
		<xsl:if test="$source=1 and metadata/source!=''"><div id='row'><div id='cell' class="metadetails"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SOURCE'"/></xsl:call-template><br/><xsl:value-of select="metadata/source" disable-output-escaping="yes" /></div></div></xsl:if>
		<!-- audience -->
		<xsl:if test="$audience=1 and metadata/audience!=''"><div id='row'><div id='cell' class="metadetails"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_AUDIENCE'"/></xsl:call-template> <br/><xsl:value-of select="metadata/audience" disable-output-escaping="yes" /></div></div></xsl:if>
		<!-- subject_category -->
		<xsl:if test="$subject_category=1 and metadata/subject[@refinement='category']!=''"><div id='row'><div id='cell' class="metadetails"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CATEGORY'"/></xsl:call-template> <br/><xsl:value-of select="metadata/subject[@refinement='category']" disable-output-escaping="yes" /></div></div></xsl:if>
		</xsl:if>
	</div>
		<xsl:call-template name="display_comments">
			<xsl:with-param name="enable_discussion"><xsl:value-of select="$enable_discussion"/></xsl:with-param>
			<xsl:with-param name="trans_id"><xsl:value-of select="@translation_identifier"/></xsl:with-param>
		</xsl:call-template>
		<xsl:if test="$top_of_doc=1 and $more!=1">
			<p class="topofdoc"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_RETURN_TO'"/></xsl:call-template><a class="topofdoc"><xsl:attribute name="name">page_<xsl:value-of select="@identifier" disable-output-escaping="yes"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_TOP_OF_PAGE'"/></xsl:call-template></a></p>
		</xsl:if>
		<xsl:if test="$content=0 and $summary=1 and $more=1"><br/>
			<xsl:choose>
				<xsl:when test="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']='index.php' and count(locations/location)>1">
				<span class="readmore"><a class="readmore"><xsl:attribute name="href"><xsl:value-of select="./locations/location[@url!='index.php']" disable-output-escaping="yes"/></xsl:attribute><xsl:choose>
					<xsl:when test="$display_more_as_text=1"><xsl:choose>
						<xsl:when test="$alt_read_more!=''"><xsl:value-of select="$alt_read_more"/></xsl:when>
						<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template></xsl:otherwise>
					</xsl:choose></xsl:when>
					<xsl:otherwise><img border="0">
						<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_MORE.gif</xsl:attribute>
						<xsl:attribute name="width"><xsl:value-of select="$image_more_width"/></xsl:attribute>
						<xsl:attribute name="height"><xsl:value-of select="$image_more_height"/></xsl:attribute>
						<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template><xsl:value-of select='" "'/><xsl:call-template name="replace_string">
								<xsl:with-param name="str_value"><xsl:copy-of select="title"/></xsl:with-param>
								<xsl:with-param name="find">&amp;#39;</xsl:with-param>
								<xsl:with-param name="replace_with">'</xsl:with-param>
							</xsl:call-template>.</xsl:attribute></img></xsl:otherwise>
					</xsl:choose></a></span>
				</xsl:when>
				<xsl:otherwise>
					<span class="readmore">
<!--					[
					<xsl:value-of select="locations/location/@url" disable-output-escaping="yes"/>,
					<xsl:value-of select="//setting[@name='script']" disable-output-escaping="yes"/>,
					<xsl:value-of select="locations/location[@url=//setting[@name='script']]" disable-output-escaping="yes"/>
					]-->
					<a class="readmore"><xsl:attribute name="href"><xsl:value-of select="locations/location[@url=//setting[@name='script']]" disable-output-escaping="yes"/></xsl:attribute><xsl:choose>
					<xsl:when test="$display_more_as_text=1"><xsl:choose>
						<xsl:when test="$alt_read_more!=''"><xsl:value-of select="$alt_read_more"/></xsl:when>
						<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template></xsl:otherwise>
					</xsl:choose></xsl:when>
					<xsl:otherwise><img border="0">
						<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_MORE.gif</xsl:attribute>
						<xsl:attribute name="width"><xsl:value-of select="$image_more_width"/></xsl:attribute>
						<xsl:attribute name="height"><xsl:value-of select="$image_more_height"/></xsl:attribute>
						<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template><xsl:value-of select='" "'/><xsl:call-template name="replace_string">
								<xsl:with-param name="str_value"><xsl:copy-of select="title"/></xsl:with-param>
								<xsl:with-param name="find">&amp;#39;</xsl:with-param>
								<xsl:with-param name="replace_with">'</xsl:with-param>
							</xsl:call-template>.</xsl:attribute></img></xsl:otherwise>
					</xsl:choose></a></span>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
<!--		<xsl:if test="not(contains(//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='real_script'],'index.php')) and count(//modules/module[@display='ENTRY']/page)=1">
			<a class="back"><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']" disable-output-escaping="yes"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_BACK'"/></xsl:call-template></a>
		</xsl:if>
		-->
	<xsl:if test="contains(content,'id=&#34;slideshow') or contains(content,'id=&amp;quot;slideshow') or contains(content,'id=&amp;amp;quot;slideshow') or contains(content,'id=&quot;slideshow') or contains(content,'id=slideshow')">
		<script src="/libertas_images/javascripts/slideshow/slideshow.js" type="text/javascript"></script>
	</xsl:if>
	<xsl:if test="contains(content,'id=&#34;mouseover&#34;')">
	<SCRIPT>
		function m_over(img, t){
			img.src=t;
		}
	</SCRIPT>
	</xsl:if>
	</xsl:for-each>
</xsl:template>



</xsl:stylesheet>