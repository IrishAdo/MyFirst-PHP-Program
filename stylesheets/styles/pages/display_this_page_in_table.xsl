<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.20 $
- Modified $Date: 2005/03/10 17:59:10 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:include href="display_comments.xsl"/>

<!--
generic Page formatting

DO NOT FORGET to add to call parameter in "display_this_page.xsl"
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
	<xsl:param name="showinpage">0</xsl:param>
	<xsl:param name="title_bullet"><xsl:value-of select="$title_bullet"/></xsl:param>
	<xsl:param name="title_starter">[[rightarrow]]</xsl:param>
	<xsl:param name="start_on_title_only">1</xsl:param>
	<xsl:param name="title_is_link">0</xsl:param>
	<xsl:param name="increment_page_id">0</xsl:param>
	<!-- Page Anchor -->
	<xsl:variable name="position"><xsl:for-each select="//modules/container/webobject/module[@display!='LATEST']/page"><xsl:if test="@identifier=$identifier"><xsl:value-of select="position()"/></xsl:if></xsl:for-each></xsl:variable>
	<xsl:for-each select="//modules/container/webobject/module[@display!='LATEST']/page[@identifier=$identifier]">
	<div class='page'><xsl:attribute name="id">page<xsl:choose>
		<xsl:when test="//menu[url=//setting[@name='script']]/@title_page = 1"><xsl:value-of select="$position + $increment_page_id"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="$position + 1 + $increment_page_id"/></xsl:otherwise>
		</xsl:choose></xsl:attribute>
	<xsl:if test="$summary=1">
		<xsl:if test="boolean(summary_files)">
			<xsl:for-each select="summary_files/file">
				<xsl:choose>
					<xsl:when test="//setting[@name='displaymode']!='textonly'">
						<div ><xsl:attribute name="class">summaryimage<xsl:choose>
							<xsl:when test="//menu[url=//setting[@name='script']]/@summaryImgDisplay='0'">left</xsl:when>
							<xsl:when test="//menu[url=//setting[@name='script']]/@summaryImgDisplay='2' and $position mod 2 = 0">left</xsl:when>
							<xsl:when test="//menu[url=//setting[@name='script']]/@summaryImgDisplay='2' and $position mod 2 = 1">right</xsl:when>
							<xsl:otherwise>right</xsl:otherwise>
						</xsl:choose></xsl:attribute><img>
							<xsl:attribute name="src"><xsl:value-of select="directory"/><xsl:value-of select="md5"/><xsl:call-template name="getextension">
									<xsl:with-param name="url"><xsl:value-of select="url"/></xsl:with-param>
								</xsl:call-template></xsl:attribute>
							<xsl:attribute name="alt"><xsl:value-of select="label"/></xsl:attribute>
							<xsl:attribute name="longdesc"><xsl:value-of select="//setting[@name='real_script']"/>?command=FILES_INFO&amp;identifier=<xsl:value-of select="md5"/></xsl:attribute>
						</img></div>
					</xsl:when>
					<xsl:otherwise>
						[ image: <a>
							<xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=FILES_INFO&amp;identifier=<xsl:value-of select="md5"/></xsl:attribute>
							<xsl:attribute name="title"><xsl:value-of select="label"/></xsl:attribute>
							<xsl:value-of select="label"/>
						</a>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</xsl:if>
	</xsl:if>
	<div class="pagecontent">
		<xsl:if test="$title=0 and $show_btn=1"><xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template></xsl:if>
		<xsl:if test="$title=1">
	<!--
		[<xsl:value-of select="$identifier"/>]
		[<xsl:value-of select="not(count(//modules/container/webobject/module[@display!='LATEST']/page[@identifier!=$identifier])=0)"/>] and 
		[<xsl:value-of select="count(//modules/container/webobject/module[@display!='LATEST']/page[@identifier!=$identifier])!=1"/>] and 
		([<xsl:value-of select="boolean(//modules/container/webobject/module[@display!='LATEST']/page[position()=1 and @identifier = $identifier ])"/>] or
		([<xsl:value-of select="(//menu[url=//setting[@name='script']]/@title_page = 0 and $position=1)"/>]))
		= (<xsl:value-of select="not(count(//modules/container/webobject/module[@display!='LATEST']/page[@identifier!=$identifier])=0) and 
				count(//modules/container/webobject/module[@display!='LATEST']/page[@identifier!=$identifier])!=1 and 
				(boolean(//modules/container/webobject/module[@display!='LATEST']/page[position()=1 and @identifier = $identifier ]) or 
				(//menu[url=//setting[@name='script']]/@title_page = 0 and $position!=1) or 
				(//menu[url=//setting[@name='script']]/@title_page = 1 and $position!=1)
				)
			"/>)
	-->
		<xsl:choose>
			<xsl:when test="
				not(count(//modules/container/webobject/module[@display!='LATEST']/page[@identifier!=$identifier])=0) and 
				count(//modules/container/webobject/module[@display!='LATEST']/page[@identifier!=$identifier])!=1 and 
				(boolean(//modules/container/webobject/module[@display!='LATEST']/page[position()=1 and @identifier = $identifier ]) or 
				(//menu[url=//setting[@name='script']]/@title_page = 0 and $position!=1) or 
				(//menu[url=//setting[@name='script']]/@title_page = 1 and $position!=1))
			"><h2>
			<xsl:attribute name="class"><xsl:choose>
				<xsl:when test="$style='LOCATION'">entrylocation</xsl:when>
				<xsl:when test="$style!='ENTRY'"><xsl:value-of select="$style"/></xsl:when>
				<xsl:otherwise>entrytitle</xsl:otherwise>
			</xsl:choose></xsl:attribute>
			<xsl:attribute name="id">pageheader<xsl:value-of select="$position + $increment_page_id"/></xsl:attribute>
			<span>
				<xsl:if test="$date_publish=1 and metadata/date[@refinement='available']!=''">
					<xsl:call-template name="format_date">
						<xsl:with-param name="current_date"><xsl:value-of select="metadata/date[@refinement='available']"/></xsl:with-param>
						<xsl:with-param name="output_format">DD/MM/YYYY</xsl:with-param>
					</xsl:call-template> - 
				</xsl:if>
				<xsl:if test="$title_bullet!=0">
					<img alt="title bullet icon"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/title_bullet.gif</xsl:attribute></img>[[nbsp]]
				</xsl:if>
				<xsl:if test="$start_on_title_only=1 and ($title=1 and $date_publish=0 and $summary=0 and $content=0 and $title_bullet=0)">
					<xsl:value-of select="$title_starter" disable-output-escaping="yes"/>
				</xsl:if>
				<xsl:choose>
					<xsl:when test="$title_is_link=1"><a>
					<xsl:attribute name='href'><xsl:value-of select="locations/location[@url=//setting[@name='script']]"/></xsl:attribute>
					<xsl:attribute name='title'><xsl:value-of select="metadata/description"/></xsl:attribute>
					<xsl:choose>
						<xsl:when test="//setting[@name='sp_page_title_is_caps']='Yes' and $position=0"><xsl:value-of select="translate(title, 'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" disable-output-escaping="yes"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></a></xsl:when>
					<xsl:otherwise><xsl:choose>
						<xsl:when test="//setting[@name='sp_page_title_is_caps']='Yes' and $position=1"><xsl:value-of select="translate(title, 'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" disable-output-escaping="yes"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:otherwise>
				</xsl:choose>
				
				<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
			</span>
			</h2></xsl:when>
			<xsl:otherwise><h1>
			<xsl:attribute name="class"><xsl:choose>
				<xsl:when test="$style='LOCATION'">entrylocation</xsl:when>
				<xsl:when test="$style!='ENTRY'"><xsl:value-of select="$style"/></xsl:when>
				<xsl:otherwise>entrytitle</xsl:otherwise>
			</xsl:choose></xsl:attribute>
			<xsl:attribute name="id">pageheader<xsl:value-of select="$position + $increment_page_id"/></xsl:attribute>
			<span>
				<xsl:if test="$date_publish=1 and metadata/date[@refinement='available']!=''">
					<xsl:call-template name="format_date">
						<xsl:with-param name="current_date"><xsl:value-of select="metadata/date[@refinement='available']"/></xsl:with-param>
						<xsl:with-param name="output_format">DD/MM/YYYY</xsl:with-param>
					</xsl:call-template> - 
				</xsl:if>
				<xsl:if test="$title_bullet!=0">
					<img alt="title bullet icon"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/title_bullet.gif</xsl:attribute></img>[[nbsp]]
				</xsl:if>
				<xsl:if test="$start_on_title_only=1 and ($title=1 and $date_publish=0 and $summary=0 and $content=0 and $title_bullet=0)">
					<xsl:value-of select="$title_starter" disable-output-escaping="yes"/>
				</xsl:if>
				<xsl:choose>
					<xsl:when test="$title_is_link=1"><a>
					<xsl:attribute name='href'><xsl:value-of select="locations/location[@url=//setting[@name='script']]"/></xsl:attribute>
					<xsl:attribute name='title'><xsl:value-of select="metadata/description"/></xsl:attribute>
					<xsl:choose>
						<xsl:when test="//setting[@name='sp_page_title_is_caps']='Yes' and $position=0"><xsl:value-of select="translate(title, 'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" disable-output-escaping="yes"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></a></xsl:when>
					<xsl:otherwise><xsl:choose>
						<xsl:when test="//setting[@name='sp_page_title_is_caps']='Yes' and $position=1"><xsl:value-of select="translate(title, 'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" disable-output-escaping="yes"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:otherwise>
				</xsl:choose>
				
				<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
			</span>
		</h1></xsl:otherwise>
		</xsl:choose>
			
				<xsl:if test="$date_modified!=0">
				<br/><div class="datemodified">Page last modified - <xsl:call-template name="format_date">
						<xsl:with-param name="current_date"><xsl:value-of select="substring-before(metadata/date[@refinement='modified'],' ')"/></xsl:with-param>
					</xsl:call-template></div>
				</xsl:if>
			<!--
			<xsl:if test="count(//modules/module[@display='ENTRY']/page)=1"><tr><td align="right"><xsl:attribute name="class"><xsl:choose>
					<xsl:when test="$style='LOCATION'">entrylocation</xsl:when>
					<xsl:otherwise>entrytitle</xsl:otherwise>
				</xsl:choose></xsl:attribute>
			</div></div></xsl:if>
			-->
			</xsl:if>
			<xsl:if test="$showinpage='1'">
				<xsl:if test="../letters">
					<xsl:call-template name="display_atoz_links">
						<xsl:with-param name="module">presentation</xsl:with-param>
					</xsl:call-template>
				</xsl:if>
			</xsl:if>
		<!-- summary -->
		<xsl:if test="$summary=1">
			<div class='contentpos'>
				<xsl:call-template name="extract_form_data">
					<xsl:with-param name="cdata"><xsl:value-of select="summary" disable-output-escaping="yes"/></xsl:with-param>
				</xsl:call-template>
			</div>
		</xsl:if>
		<!-- content -->
		<xsl:if test="$content=1">
			<div class="contentpos"><xsl:choose>
				<xsl:when test="files/file and $file_location='right'">
					<div>
						<xsl:variable name="cond">&lt;p</xsl:variable>
						<div class="contentpos"><!--<xsl:choose>
							<xsl:when test="not(contains(content,$cond))"><p>
								<xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="content" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
							</p></xsl:when>
							<xsl:otherwise>-->
								<p><xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="content" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></p>
							<!--</xsl:otherwise>
						</xsl:choose>-->
						</div>
						<div class="contentpos"><xsl:call-template name="display_files"/></div>
					</div>
				</xsl:when>
				<xsl:otherwise>
				<xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="content" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
					<!--
					<xsl:choose>
						<xsl:when test="contains(content,'libertas_form')"><p><xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="content" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></p></xsl:when>
						<xsl:otherwise></xsl:otherwise>
					</xsl:choose>
					-->
				</xsl:otherwise>
			</xsl:choose></div>
			<xsl:if test="files/file and $file_location='bottom'">
				<div class='contentpos'><xsl:call-template name="display_files"></xsl:call-template></div>
			</xsl:if>
		</xsl:if>
		<xsl:if test="($alt_title=-1 and metadata/alternative!='')
						or ($author='1' and count(metadata/creator)>0)
					  	or ($contributors='1' and count(metadata/contributor)>0)
						or ($source=1 and metadata/source!='')
						or ($audience=1 and metadata/audience!='')
						or ($subject_category=1 and metadata/subject[@refinement='category']!='')
		">
		<xsl:if test="$alt_title=1 and metadata/alternative!=''"><div class="metadetails"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_ALT_TITLE'"/></xsl:call-template><br/><xsl:value-of select="metadata/alternative" disable-output-escaping="yes" /></div></xsl:if>
		<!-- authors -->
		<xsl:if test="$author='1' and count(metadata/creator)>0"><div class="contentpos"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_AUTHORS'"/></xsl:call-template><br/><xsl:for-each select="metadata/creator">
			<xsl:value-of select="." disable-output-escaping="yes" /><br/>
		</xsl:for-each>
		</div></xsl:if>
		<!-- contributors -->
		<xsl:if test="$contributors='1' and count(metadata/contributor)>0"><div class="metadetails"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CONTRIBUTORS'"/></xsl:call-template><br/><xsl:for-each select="metadata/contributor">
			<xsl:value-of select="." disable-output-escaping="yes" /><br/>
		</xsl:for-each>
		</div></xsl:if>
		<!-- source -->
		<xsl:if test="$source=1 and metadata/source!=''"><div class="metadetails"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SOURCE'"/></xsl:call-template><br/><xsl:value-of select="metadata/source" disable-output-escaping="yes" /></div></xsl:if>
		<!-- audience -->
		<xsl:if test="$audience=1 and metadata/audience!=''"><div class="metadetails"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_AUDIENCE'"/></xsl:call-template> <br/><xsl:value-of select="metadata/audience" disable-output-escaping="yes" /></div></xsl:if>
		<!-- subject_category -->
		<xsl:if test="$subject_category=1 and metadata/subject[@refinement='category']!=''"><div class="metadetails"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CATEGORY'"/></xsl:call-template> <br/><xsl:value-of select="metadata/subject[@refinement='category']" disable-output-escaping="yes" /></div></xsl:if>
		</xsl:if>
		<xsl:call-template name="display_comments">
			<xsl:with-param name="enable_discussion"><xsl:value-of select="$enable_discussion"/></xsl:with-param>
			<xsl:with-param name="trans_id"><xsl:value-of select="@translation_identifier"/></xsl:with-param>
		</xsl:call-template>
		<xsl:if test="$top_of_doc=1 and $more!=1">
			<p class="topofdoc">:: <a class="topofdoc"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/>#page1</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_TOP_OF_PAGE'"/></xsl:call-template></a> ::</p>
		</xsl:if>
		<xsl:if test="$content=0 and $summary=1 and $more=1">
			<xsl:choose>
				<xsl:when test="//setting[@name='script']='index.php' and count(locations/location)>1">
				<div class="readmore"><a class="readmore">
					<xsl:attribute name="href"><xsl:value-of select="./locations/location[@url!='index.php']" disable-output-escaping="yes"/></xsl:attribute>
					<xsl:attribute name="title">More on <xsl:value-of select="title"/></xsl:attribute>
					<xsl:choose>
						<xsl:when test="string-length(title)>20">More on <xsl:value-of select="translate(concat(substring(title,1,20), substring-before(substring(title,20),' ')) ,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')"/>...</xsl:when>
						<xsl:otherwise>More on <xsl:value-of select="translate(title,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')"/></xsl:otherwise>
					</xsl:choose>
					</a></div>
				</xsl:when>
				<xsl:otherwise>
					<div class="readmore">
					<a class="readmore"><xsl:attribute name="href"><xsl:value-of select="locations/location[@url=//setting[@name='script']]" disable-output-escaping="yes"/></xsl:attribute>
					<xsl:attribute name="title">More on <xsl:value-of select="title"/></xsl:attribute>
					<xsl:choose>
						<xsl:when test="string-length(title)>20">More on <xsl:value-of select="translate(concat(substring(title,1,20), substring-before(substring(title,20),' ')),'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')"/>...</xsl:when>
						<xsl:otherwise>More on <xsl:value-of select="translate(title,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','abcdefghijklmnopqrstuvwxyz')"/></xsl:otherwise>
					</xsl:choose>
					</a></div>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
		</div>
	</div>
	<xsl:if test="contains(content,'id=&#34;slideshow') or contains(content,'id=&amp;quot;slideshow') or contains(content,'id=&amp;amp;quot;slideshow') or contains(content,'id=&quot;slideshow') or contains(content,'id=slideshow')">
		<script src="/libertas_images/javascripts/slideshow/slideshow.js" type="text/javascript">
		<xsl:comment>Javascript for slide show</xsl:comment>
		</script>
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

<xsl:template name="getextension">
	<xsl:param name="url"></xsl:param>
	<xsl:choose>
		<xsl:when test="contains($url,'.')"><xsl:call-template name="getextension">
			<xsl:with-param name="url"><xsl:value-of select="substring-after($url,'.')" /></xsl:with-param>
		</xsl:call-template></xsl:when>
		<xsl:otherwise>.<xsl:value-of select="$url"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>



</xsl:stylesheet>
