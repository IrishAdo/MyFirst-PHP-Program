<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.6 $
- Modified $Date: 2005/03/03 09:24:14 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:include href="display_comments_textonly.xsl"/>
<!--
generic Page formatting
-->
<xsl:template name="display_this_page_textonly">
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
	<xsl:param name="title_is_link">0</xsl:param>
	<!-- Page Anchor -->
	<hr/>
	<xsl:for-each select="//modules/container/webobject/module[@display!='LATEST']/page[@identifier=$identifier]">
		<a><xsl:attribute name="name">page_<xsl:value-of select="@identifier" disable-output-escaping="yes"/></xsl:attribute></a>
<!--			[Edit : <a>'<xsl:value-of select="title" disable-output-escaping="yes"/>'</a>] -->
			<xsl:if test="$title=0 and $show_btn=1"><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template></xsl:if>
			<xsl:if test="$title=1"><h1 class='tableheader'>
				<xsl:if test="$start_on_title_only=1 and ($title=1 and $date_publish=0 and $summary=0 and $content=0 and $title_bullet=0)">
					<xsl:value-of select="$title_starter" disable-output-escaping="yes"/>
				</xsl:if>
				<xsl:choose>
					<xsl:when test="$title_is_link=1"><a><xsl:attribute name='href'><xsl:value-of select="//setting[@name='script']"/></xsl:attribute><xsl:value-of select="title" disable-output-escaping="yes"/></a></xsl:when>
					<xsl:otherwise><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:otherwise>
				</xsl:choose>
				<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
				<xsl:if test="$date_publish=1 and metadata/date[@refinement='available']!=''">
					<xsl:call-template name="format_date">
						<xsl:with-param name="current_date"><xsl:value-of select="substring-before(metadata/date[@refinement='creation'],' ')"/></xsl:with-param>
					</xsl:call-template>
				</xsl:if></h1><xsl:if test="$date_modified!=0">
				<br/>Page last modified - <xsl:call-template name="format_date">
						<xsl:with-param name="current_date"><xsl:value-of select="substring-before(metadata/date[@refinement='modified'],' ')"/></xsl:with-param>
					</xsl:call-template>
				</xsl:if>
			</xsl:if>
			
		<!-- summary -->
		<xsl:if test="$summary=1">
			<xsl:call-template name="fix_it"><xsl:with-param name="cdata"><xsl:value-of select="summary" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:if>
		<!-- content -->
		<xsl:if test="$content=1">
			<xsl:call-template name="fix_it"><xsl:with-param name="cdata"><xsl:value-of select="content" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
			<xsl:if test="files/file and $file_location='bottom'">
				<xsl:call-template name="display_files"></xsl:call-template>
			</xsl:if>
		</xsl:if>
		<xsl:if test="($alt_title=-1 and metadata/alternative!='')
						or ($author='1' and count(metadata/creator)>0)
					  	or ($contributors='1' and count(metadata/contributor)>0)
						or ($source=1 and metadata/source!='')
						or ($audience=1 and metadata/audience!='')
						or ($subject_category=1 and metadata/subject[@refinement='category']!='')
		">
		<xsl:if test="$alt_title=1 and metadata/alternative!=''"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_ALT_TITLE'"/></xsl:call-template><br/><xsl:value-of select="metadata/alternative" disable-output-escaping="yes" /><BR/></xsl:if>
		<!-- authors -->
		<xsl:if test="$author='1' and count(metadata/creator)>0"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_AUTHORS'"/></xsl:call-template><br/><xsl:for-each select="metadata/creator">
			<xsl:value-of select="." disable-output-escaping="yes" /><br/>
		</xsl:for-each>
		</xsl:if>
		<!-- contributors -->
		<xsl:if test="$contributors='1' and count(metadata/contributor)>0"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CONTRIBUTORS'"/></xsl:call-template><br/><xsl:for-each select="metadata/contributor">
			<xsl:value-of select="." disable-output-escaping="yes" /><br/>
		</xsl:for-each>
		</xsl:if>
		<!-- source -->
		<xsl:if test="$source=1 and metadata/source!=''"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SOURCE'"/></xsl:call-template><br/><xsl:value-of select="metadata/source" disable-output-escaping="yes" /></xsl:if>
		<!-- audience -->
		<xsl:if test="$audience=1 and metadata/audience!=''"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_AUDIENCE'"/></xsl:call-template> <br/><xsl:value-of select="metadata/audience" disable-output-escaping="yes" /></xsl:if>
		<!-- subject_category -->
		<xsl:if test="$subject_category=1 and metadata/subject[@refinement='category']!=''"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CATEGORY'"/></xsl:call-template> <br/><xsl:value-of select="metadata/subject[@refinement='category']" disable-output-escaping="yes" /></xsl:if>
		</xsl:if>
		<xsl:call-template name="display_comments_textonly">
			<xsl:with-param name="enable_discussion"><xsl:value-of select="$enable_discussion"/></xsl:with-param>
			<xsl:with-param name="trans_id"><xsl:value-of select="@translation_identifier"/></xsl:with-param>
		</xsl:call-template>
		<xsl:if test="$top_of_doc=1 and $more!=1">
			<p class="topofdoc"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_RETURN_TO'"/></xsl:call-template>
			[<a class="topofdoc">
			<xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>#top</xsl:attribute>
			<xsl:attribute name="name">page_<xsl:value-of select="@identifier" disable-output-escaping="yes"/></xsl:attribute>
			<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_TOP_OF_PAGE'"/></xsl:call-template></a>]</p>
		</xsl:if>
		<xsl:if test="$content=0 and $summary=1 and $more=1"><br/>
			<xsl:choose>
				<xsl:when test="//setting[@name='script']='index.php' and count(locations/location)>1">
				<a class="readmore"><xsl:attribute name="href"><xsl:value-of select="./locations/location[@url!='index.php']" disable-output-escaping="yes"/></xsl:attribute>
					<xsl:choose>
						<xsl:when test="$alt_read_more!=''"><xsl:value-of select="$alt_read_more"/></xsl:when>
						<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template> '<xsl:value-of select="title"/>'</xsl:otherwise>
					</xsl:choose></a>
				</xsl:when>
				<xsl:otherwise>
					<a class="readmore"><xsl:attribute name="href"><xsl:value-of select="locations/location[@url=//setting[@name='script']]" disable-output-escaping="yes"/></xsl:attribute><xsl:choose>
						<xsl:when test="$alt_read_more!=''"><xsl:value-of select="$alt_read_more"/></xsl:when>
						<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_LINK_MORE'"/></xsl:call-template> '<xsl:value-of select="title"/>'</xsl:otherwise>
					</xsl:choose></a>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="extract_image_data">
	<xsl:param name="cdata"></xsl:param>
	<xsl:variable name="checkme"></xsl:variable>
	<xsl:variable name="condition">
	<xsl:if test="contains($cdata,'&amp;lt;img id=&amp;quot;libertas_form&amp;quot;')">0</xsl:if>
	<xsl:if test="contains($cdata,'&lt;form')">6</xsl:if>
	<xsl:if test="contains($cdata,'&amp;amp;amp;lt;img')">1</xsl:if>
	<xsl:if test="contains($cdata,'&lt;img')">2</xsl:if>
	<xsl:if test="contains($cdata,'&amp;lt;img')">3</xsl:if>
	<xsl:if test="contains($cdata,'&amp;amp;lt;img')">4</xsl:if>
	<xsl:if test="contains($cdata,'&#60;img')">5</xsl:if>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="contains($condition,0)">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;lt;img id=&amp;quot;libertas_form&amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring-after($cdata,'&amp;lt;img id=&amp;quot;libertas_form&amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:call-template name="extract_image_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_start" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
			
			<xsl:if test="string-length($string_start)!=0">&lt;/p&gt;</xsl:if>
			<xsl:variable name="get_frm"><xsl:value-of select="substring-before(substring-after($cdata,'frm_identifier=&amp;quot;'),'&amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="frm_identifier"><xsl:value-of select="$get_frm"/></xsl:variable>
			<xsl:call-template name="display_form"><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param></xsl:call-template>
			<xsl:if test="string-length($string_start)!=0">&lt;p&gt;</xsl:if>
			<xsl:call-template name="extract_image_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="contains($condition,2)">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&lt;img')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start))" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="image"><xsl:value-of select="substring-before($string_rest,'&gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:variable name="get_longdesc"><xsl:value-of select="substring-before(substring-after($image,'longdesc=&quot;'),'&quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="get_alt"><xsl:value-of select="substring-before(substring-after($image,'alt=&quot;'),'&quot;')" disable-output-escaping="yes"/></xsl:variable>
			[image: &lt;a href="<xsl:value-of select="$get_longdesc"/>" title="<xsl:value-of select="$get_alt"/>"&gt;<xsl:value-of select="$get_alt"/>&lt;/a&gt;]
			<xsl:call-template name="extract_image_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="$condition=3">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;lt;img')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start))" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="image"><xsl:value-of select="substring-before($string_rest,'&amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:variable name="get_longdesc"><xsl:choose>
				<xsl:when test="contains($image,'longDesc')"><xsl:choose>
					<xsl:when test="contains(substring-after($image,'longDesc='),'&amp;quot;')"><xsl:value-of select="substring-before(substring-after($image,'longDesc=&amp;quot;'),'&amp;quot;')" disable-output-escaping="yes"/></xsl:when>
					<xsl:when test="contains(substring-after($image,'longDesc='),' ')"><xsl:value-of select="substring-before(substring-after($image,'longDesc='),' ')" disable-output-escaping="yes"/></xsl:when>
					<xsl:when test="contains(substring-after($image,'longDesc='),'&amp;gt;')"><xsl:value-of select="substring-before(substring-after($image,'longDesc='),'&amp;gt;')" disable-output-escaping="yes"/></xsl:when>
					<xsl:when test="contains(substring-after($image,'longDesc='),'&amp;amp;gt;')"><xsl:value-of select="substring-before(substring-after($image,'longDesc='),'&amp;amp;gt;')" disable-output-escaping="yes"/></xsl:when>
					<xsl:when test="contains(substring-after($image,'longDesc='),'&gt;')"><xsl:value-of select="substring-before(substring-after($image,'longDesc='),'&gt;')" disable-output-escaping="yes"/></xsl:when>
					<xsl:when test="contains(substring-after($image,'longDesc='),'&amp;#62;')"><xsl:value-of select="substring-before(substring-after($image,'longDesc='),'&amp;#62;')" disable-output-escaping="yes"/></xsl:when>
					<xsl:when test="contains(substring-after($image,'longDesc='),'&#62;')"><xsl:value-of select="substring-before(substring-after($image,'longDesc='),'&#62;')" disable-output-escaping="yes"/></xsl:when>
					<xsl:when test="contains(substring-after($image,'longDesc='),'&amp;amp;#62;')"><xsl:value-of select="substring-before(substring-after($image,'longDesc='),'&amp;amp;#62;')" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise>woo woo</xsl:otherwise>
				</xsl:choose></xsl:when>
				<xsl:otherwise><xsl:value-of select="substring-before(substring-after($image,'longdesc=&amp;quot;'),'&amp;quot;')" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			<xsl:variable name="get_alt"><xsl:value-of select="substring-before(substring-after($image,'alt=&amp;quot;'),'&amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			[image: &lt;a href="<xsl:value-of select="$get_longdesc"/>" title="<xsl:value-of select="$get_alt"/>"&gt;<xsl:value-of select="$get_alt"/>&lt;/a&gt;]
			<xsl:call-template name="extract_image_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="$condition=4">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;amp;lt;img')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start))" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="image"><xsl:value-of select="substring-before($string_rest,'&amp;amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:variable name="get_longdesc"><xsl:value-of select="substring-before(substring-after($image,'longdesc=&amp;amp;quot;'),'&amp;amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="get_alt"><xsl:value-of select="substring-before(substring-after($image,'alt=&amp;amp;quot;'),'&amp;amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			[image: &lt;a href="<xsl:value-of select="$get_longdesc"/>" title="<xsl:value-of select="$get_alt"/>"&gt;<xsl:value-of select="$get_alt"/>&lt;/a&gt;]
			<xsl:call-template name="extract_image_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="$condition=5">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&#60;img')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start))" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&#62;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="image"><xsl:value-of select="substring-before($string_rest,'&#62;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:variable name="get_longdesc"><xsl:value-of select="substring-before(substring-after($image,'longdesc=&amp;quot;'),'&quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="get_alt"><xsl:value-of select="substring-before(substring-after($image,'alt=&quot;'),'&quot;')" disable-output-escaping="yes"/></xsl:variable>
			[image: &lt;a href="<xsl:value-of select="$get_longdesc"/>" title="<xsl:value-of select="$get_alt"/>"&gt;<xsl:value-of select="$get_alt"/>&lt;/a&gt;]
			<xsl:call-template name="extract_image_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:otherwise><xsl:copy-of select="$cdata" /></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="extract_link_data">
	<xsl:param name="cdata"></xsl:param>
	<xsl:variable name="checkme">&amp;#60;a </xsl:variable>
	<xsl:variable name="condition">
	<xsl:if test="contains($cdata,'&amp;amp;amp;lt;a ')">1</xsl:if>
	<xsl:if test="contains($cdata,'&lt;a ')">2</xsl:if>
	<xsl:if test="contains($cdata,'&amp;lt;a ')">3</xsl:if>
	<xsl:if test="contains($cdata,'&amp;amp;lt;a ')">4</xsl:if>
	<xsl:if test="contains($cdata,'&#60;a ')">5</xsl:if>
	<xsl:if test="contains($cdata,'&amp;#60;a ')">6</xsl:if>
	<xsl:if test="contains($cdata,'&amp;amp;#60;a ')">7</xsl:if>
	<xsl:if test="contains($cdata,'&amp;amp;amp;#60;a ')">8</xsl:if>
	<xsl:if test="contains($cdata,$checkme)">9</xsl:if>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="contains($condition,2)">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&lt;a ')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start)+1)" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&lt;/a&gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="linkinfo"><xsl:value-of select="substring($string_rest,0,(string-length($string_rest)-string-length($string_end))+1)"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			[<xsl:copy-of select="$linkinfo" />]
			<xsl:call-template name="extract_link_data"><xsl:with-param name="cdata"><xsl:copy-of select="$string_end" /></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="contains($condition,3)">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;lt;a ')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start)+1)" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;lt;/a&amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="linkinfo"><xsl:value-of select="substring($string_rest,0,(string-length($string_rest)-string-length($string_end))+1)"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			[<xsl:value-of select="$linkinfo" disable-output-escaping="yes"/>]
			<xsl:call-template name="extract_link_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="contains($condition,4)">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;amp;lt;a ')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start)+1)" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;amp;lt;/a&amp;amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="linkinfo"><xsl:value-of select="substring($string_rest, 0, (string-length($string_rest) - string-length($string_end) ) + 1 )"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			[<xsl:value-of select="$linkinfo" disable-output-escaping="yes"/>]
			<xsl:call-template name="extract_link_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="contains($condition,5)">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&#60;a ')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start))" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&#60;/a&#62;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="linkinfo"><xsl:value-of select="substring($string_rest,0,(string-length($string_rest)-string-length($string_end))+1)"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			::[<xsl:value-of select="$linkinfo" disable-output-escaping="yes"/>]::
			<xsl:call-template name="extract_link_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="contains($condition,9)">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;#60;a ')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start))" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;#60;/a&amp;#62;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="linkinfo"><xsl:value-of select="substring($string_rest,0,(string-length($string_rest)-string-length($string_end))+1)"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			::[<xsl:value-of select="$linkinfo" disable-output-escaping="yes"/>]::
			<xsl:call-template name="extract_link_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:otherwise><xsl:value-of select="$cdata" disable-output-escaping="yes"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

			
			
<xsl:template name="extract_styles">
	<xsl:param name="cdata"></xsl:param>
	<xsl:variable name="condition"><xsl:if test="contains($cdata,' style=')">1</xsl:if></xsl:variable>
	<xsl:choose>
		<xsl:when test="$condition=1">
			<xsl:value-of select="substring-before($cdata,' style=')" disable-output-escaping="yes"/> xstyle=
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($cdata,' style=')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:call-template name="extract_styles">
				<xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise><xsl:value-of select="$cdata" disable-output-escaping="yes"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="fix_tables">
	<xsl:param name="cdata"></xsl:param>
	<xsl:variable name="condition">
		<xsl:if test="contains($cdata,'&amp;amp;lt;table')">1</xsl:if>
		<xsl:if test="contains($cdata,'&amp;lt;table')">2</xsl:if>
		<xsl:if test="contains($cdata,'&lt;table')">3</xsl:if>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="$condition=1">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;amp;lt;table')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start))" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="tabletag"><xsl:value-of select="substring-before($string_rest,'&amp;amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:choose>
				<xsl:when test="contains($tabletag ,' align=')"><xsl:value-of select="substring-before($tabletag,' align=')" disable-output-escaping="yes"/> xalign=<xsl:value-of select="substring-after($tabletag,' align=')" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="$tabletag" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose>
			<xsl:call-template name="fix_tables">
				<xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:when test="$condition=2">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;lt;table')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring($cdata,string-length($string_start))" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="tabletag"><xsl:value-of select="substring-before($string_rest,'&amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:choose>
				<xsl:when test="contains($tabletag ,' align=')"><xsl:value-of select="substring-before($tabletag,' align=')" disable-output-escaping="yes"/> xalign=<xsl:value-of select="substring-after($tabletag,' align=')" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="$tabletag" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose>
			<xsl:call-template name="fix_tables">
				<xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise><xsl:value-of select="$cdata" disable-output-escaping="yes"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="fix_it">
	<xsl:param name="cdata"></xsl:param>

	<xsl:call-template name="extract_image_data">
		<xsl:with-param name="cdata"><xsl:call-template name="extract_styles">
			<xsl:with-param name="cdata"><xsl:call-template name="fix_tables">
				<xsl:with-param name="cdata"><xsl:call-template name="extract_link_data">
					<xsl:with-param name="cdata"><xsl:value-of select="$cdata" disable-output-escaping="yes"/></xsl:with-param>
				</xsl:call-template></xsl:with-param>
			</xsl:call-template></xsl:with-param>
		</xsl:call-template></xsl:with-param>
	</xsl:call-template>

	<!--
	
		
		</xsl:with-param>
				</xsl:call-template></xsl:with-param>
			</xsl:call-template></xsl:with-param>
		</xsl:call-template></xsl:with-param>
	</xsl:call-template>
	-->
</xsl:template>


</xsl:stylesheet>