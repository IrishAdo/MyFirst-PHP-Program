<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:50:08 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
 
<xsl:template name="display_stats">
<xsl:if test="table">
	<xsl:apply-templates select="table"/>
</xsl:if>
	<tr><td align="left" valign="top"><table>
	<xsl:choose>
	<xsl:when test="count(stat_results/stat_entry)>0">
	<xsl:if test="count(stat_results)>1">
	<tr><td>
	<h1><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_STATS_LIST_OF_REPORTS'"/></xsl:call-template></h1>
	<ul>
	<xsl:for-each select="stat_results">
		<LI><a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?<xsl:value-of select="//xml_document/qstring"/>#<xsl:call-template name="get_translation"><xsl:with-param name="check" select="translate(@label,' ','_')"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></a></LI>
	</xsl:for-each>
	</ul>
	</td></tr>
	</xsl:if>
	<xsl:for-each select="stat_results">
		<xsl:if test="@total_pages>1">
			<tr><td align="left" valign="top">Page <xsl:value-of select="@page"/> of <xsl:value-of select="@total_pages"/></td></tr>
		</xsl:if>
		<tr><td align="left" valign="top"><xsl:apply-templates select="."/></td></tr>
		<xsl:if test="@total_pages>1">
			<tr><td align="center" valign="top">
				Page <xsl:value-of select="@page"/> of <xsl:value-of select="@total_pages"/><br/>|
				<xsl:for-each select="pages/page">
				<a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=<xsl:value-of select="../../@report"/>&amp;page=<xsl:value-of select="."/></xsl:attribute><xsl:value-of select="."/></a> |
				</xsl:for-each>
			</td></tr>
		</xsl:if>
	</xsl:for-each>
	<xsl:for-each select="previous/stat_results">
	<tr><td align="left" valign="top"><xsl:apply-templates select="."/></td></tr>
	</xsl:for-each>
	<xsl:if test="graphs/graph">
	<tr><td align="left" valign="top"><xsl:call-template name="display_graph"></xsl:call-template></td></tr>
	</xsl:if></xsl:when>
	<xsl:otherwise>
		<tr> 
			<td valign="top" class="formheader"><b><xsl:call-template name="get_translation"><xsl:with-param name="check" select="stat_results/@label"/></xsl:call-template></b></td>
  		</tr>
		<tr><td>
			<h2><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'SORRY_NO_RESULTS'"/></xsl:call-template></h2>
		</td></tr>
	</xsl:otherwise>
	</xsl:choose>
</table></td></tr>
<script>
function display(str,p){
	eval('loc = document.all.content_'+p+';');
	loc.innerHTML=str;
}
</script>
</xsl:template>


<xsl:template match="stat_results">
	<xsl:variable name="total"></xsl:variable>
<a><xsl:attribute name="name"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="translate(@label,' ','_')"/></xsl:call-template></xsl:attribute></a>
	<table border="0" cellpadding="0" cellspacing="0" summary="This table holds a form">	
		<xsl:variable name="num_of_columns"><xsl:value-of select="count(stat_entry/attribute[@show!='NO'])"/></xsl:variable>
		<tr> 
				<td valign="top" class="formheader"><xsl:attribute name="colspan">
			<xsl:choose>
				<xsl:when test ="stat_entry/attribute[@show='BAR']">
					<xsl:value-of select="$num_of_columns + 2"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$num_of_columns + 1"/>
				</xsl:otherwise>
			</xsl:choose>
			</xsl:attribute><b><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></b></td>
  		</tr>
		<xsl:for-each select="../text">
		<tr> 
				<td valign="top" class="TableCell"><xsl:attribute name="colspan">
			<xsl:choose>
				<xsl:when test ="../stat_entry/attribute[@show='BAR']">
					<xsl:value-of select="$num_of_columns + 2"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$num_of_columns + 1"/>
				</xsl:otherwise>
			</xsl:choose>
			</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of disable-output-escaping="yes" select="."/></xsl:with-param></xsl:call-template></td>
  		</tr>
		</xsl:for-each>
		<xsl:for-each select="text">
		<tr> 
				<td valign="top" class="TableCell"><xsl:attribute name="colspan">
			<xsl:choose>
				<xsl:when test ="../stat_entry/attribute[@show='BAR']">
					<xsl:value-of select="$num_of_columns + 2"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:value-of select="$num_of_columns + 1"/>
				</xsl:otherwise>
			</xsl:choose>
			</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of disable-output-escaping="yes" select="."/></xsl:with-param></xsl:call-template></td>
  		</tr>
		</xsl:for-each>

		<tr> 
		   	<td valign="top" class="formbackground"><table border="0" cellpadding="0" cellspacing="0" summary="This table holds stats">	
		<tr> 
		   	<td valign="top" class="formbackground">
				<table class="sortable" id="stats"  cellpadding="0" cellspacing="0" width="100%" summary="This table holds the row information for stats">
					<xsl:attribute name="id">stats-<xsl:value-of select="count(stat_entry)"/></xsl:attribute>
					<xsl:apply-templates select="stat_entry"/>

				<xsl:apply-templates select="stat_total"/>
				</table>				
			</td>
		</tr>
	</table>
			</td>
			<xsl:if test="../graphs">
			<td valign="top">
			<xsl:call-template name="display_legend"></xsl:call-template>
			</td>
			</xsl:if>
		</tr>
	</table>
</xsl:template>

<xsl:template match="stat_entry">
	<xsl:variable name="num_of_columns"><xsl:value-of select="count(attribute[@show!='NO'])"/></xsl:variable>
	<xsl:variable name="current_line"><xsl:value-of select="position()"/></xsl:variable>
	<xsl:if test="position()=1">
	<!--<tr> -->
		<xsl:for-each select="attribute[@show!='NO']">
			<xsl:variable name="current"><xsl:choose>
				<xsl:when test="../attribute/@name='LOCALE_STATS_FLAG'"><xsl:value-of select="position() + 1"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="position()"/></xsl:otherwise>
			</xsl:choose></xsl:variable>
  			<th valign="top"><xsl:attribute name="class"><xsl:choose>
					<xsl:when test="../../../graphs/graph[.=$current]">1
					<xsl:variable name="graph_number">
					<xsl:for-each select="../../../graphs/graph">
					<xsl:if test=".=$current"><xsl:value-of select="position()"/></xsl:if>
					</xsl:for-each></xsl:variable>graph<xsl:value-of select="$graph_number"/></xsl:when>
					<xsl:otherwise>TableCell</xsl:otherwise>
				</xsl:choose></xsl:attribute>
			<xsl:attribute name="colspan"><xsl:choose>
				<xsl:when test="position()=last()"><xsl:choose>
				<xsl:when test="@show='BAR'"><xsl:value-of select="$num_of_columns"/></xsl:when>
				<xsl:otherwise>1</xsl:otherwise>
				</xsl:choose></xsl:when>
				<xsl:when test="position()=1"><xsl:choose>
					<xsl:when test="../../@show_counter=0">1</xsl:when>
					<xsl:otherwise>1</xsl:otherwise>
				</xsl:choose></xsl:when>
			</xsl:choose></xsl:attribute>
			<strong><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template>[[nbsp]]</strong>
			</th>
		</xsl:for-each>
 		<!--</tr>-->
	</xsl:if>
	<tr> 
	<xsl:variable name="position"><xsl:value-of select="position()"/></xsl:variable>
		<xsl:for-each select="attribute[@show!='NO']">
			<xsl:variable name="current"><xsl:choose>
				<xsl:when test="../attribute/@name='LOCALE_STATS_FLAG'"><xsl:value-of select="position() + 1"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="position()"/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			<td valign="top"><xsl:attribute name="class"><xsl:choose>
					<xsl:when test="../../../graphs/graph[.=$current]">1
					<xsl:variable name="graph_number">
					<xsl:for-each select="../../../graphs/graph">
					<xsl:if test=".=$current"><xsl:value-of select="position()"/></xsl:if>
					</xsl:for-each></xsl:variable>graph<xsl:value-of select="$graph_number"/></xsl:when>
					<xsl:otherwise>TableCell</xsl:otherwise>
				</xsl:choose></xsl:attribute>
			<xsl:choose>
				<xsl:when test="@show='FLAG' or @show='OS' or @show='BROWSER'"><xsl:attribute name="width">14</xsl:attribute></xsl:when>
				<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
	
			
			<xsl:if test="position()=last()"><xsl:if test="attribute[@show='BAR']"><xsl:attribute name="colspan"><xsl:value-of select="$num_of_columns - 1"/></xsl:attribute></xsl:if></xsl:if>
			<xsl:choose>
				<xsl:when test="@link='NO'">
					<xsl:choose>
					<xsl:when test="@show='FLAG'"><img width="14" height="14" border="1"><xsl:attribute name="src">/libertas_images/icons/flags/<xsl:value-of select="."/>.png</xsl:attribute><xsl:attribute name="alt"><xsl:value-of select="@alt"/></xsl:attribute></img></xsl:when>
					<xsl:when test="@show='OS'"><img width="14" height="14" border="0"><xsl:attribute name="alt"><xsl:value-of select="@alt"/></xsl:attribute><xsl:attribute name="src">/libertas_images/icons/os/<xsl:value-of select="."/>.png</xsl:attribute></img></xsl:when>
					<xsl:when test="@show='BROWSER'"><img width="14" height="14" border="0"><xsl:attribute name="alt"><xsl:value-of select="@alt"/></xsl:attribute><xsl:attribute name="src">/libertas_images/icons/browser/<xsl:value-of select="."/>.png</xsl:attribute></img></xsl:when>
					<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></xsl:otherwise>
					</xsl:choose>
				</xsl:when>
				<xsl:otherwise>
					<xsl:variable name="link"><xsl:value-of select="@link"/></xsl:variable>
					<a><xsl:attribute name="href"><xsl:choose>
					<xsl:when test="contains(../attribute[@name=$link],'http://') and substring-before(../attribute[@name=$link],'http://')=''"><xsl:value-of select="../attribute[@name=$link]" disable-output-escaping="yes"/></xsl:when>
					<xsl:when test="contains(../attribute[@name=$link],'?')"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/><xsl:value-of select="../attribute[@name=$link]" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?<xsl:if test="../../@link">command=<xsl:value-of select="../../@link"/>&amp;</xsl:if>
						<xsl:value-of select="../attribute[@name=$link]"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
					<xsl:choose>
					<xsl:when test="@show='FLAG'"><img width="14" height="14" border="1"><xsl:attribute name="src">/libertas_images/icons/flags/<xsl:value-of select="."/>.png</xsl:attribute></img><xsl:attribute name="alt"><xsl:value-of select="@alt"/></xsl:attribute></xsl:when>
					<xsl:when test="@show='OS'"><img width="14" height="14" border="0"><xsl:attribute name="src">/libertas_images/icons/os/<xsl:value-of select="."/>.png</xsl:attribute><xsl:attribute name="alt"><xsl:value-of select="@alt"/></xsl:attribute></img></xsl:when>
					<xsl:when test="@show='BROWSER'"><img width="14" height="14" border="0"><xsl:attribute name="alt"><xsl:value-of select="@alt"/></xsl:attribute><xsl:attribute name="src">/libertas_images/icons/browser/<xsl:value-of select="."/>.png</xsl:attribute></img></xsl:when>
					<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></xsl:otherwise>
					</xsl:choose></a>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:variable name="id"><xsl:value-of select="@name"/></xsl:variable>
			<xsl:if test="position()=1 and ../attribute[@show='HIDDEN' and @link=$id]">
			<span><xsl:attribute name="id">content_<xsl:value-of select="$current_line"/></xsl:attribute>
			
			<br/><a><xsl:attribute name='href'>javascript:display('<xsl:value-of disable-output-escaping="yes" select="../attribute[@show='HIDDEN' and @link=$id]"/>',<xsl:value-of select="$current_line"/>);</xsl:attribute><img src='/libertas_images/themes/site_administration/button_MORE.gif' border='0'/></a>
			</span>
			</xsl:if>
			</td>
		</xsl:for-each>
		<xsl:if test="attribute[@show='BAR']">
		<xsl:variable name="percentage">
		<xsl:choose>
			<xsl:when test="attribute[@show='BAR']='0'">0</xsl:when>
			<xsl:otherwise><xsl:value-of select="round((attribute[@show='BAR'] div ../@total)*10000)div 100"/></xsl:otherwise>
		</xsl:choose>
		</xsl:variable>
		<td class="TableCell" valign="top" ><img border="0" src="/libertas_images/themes/site_administration/bar_middle1.png" height="13"><xsl:attribute name="width"><xsl:value-of select="round($percentage)"/></xsl:attribute><xsl:attribute name="alt"><xsl:value-of select="round($percentage)"/> %</xsl:attribute></img>[[nbsp]] <xsl:value-of select="$percentage"/> %</td>
		</xsl:if>
 		</tr>
		<xsl:if test="position()=last()">
			<xsl:if test="../@total>0">
			<tr class="ignore"> 
				<xsl:variable name="colspan"><xsl:for-each select="attribute"><xsl:if test="@show='BAR'"><xsl:value-of select="position() - 1"/></xsl:if></xsl:for-each></xsl:variable>
 				<td valign="top" class="TableCell" align="right"><xsl:attribute name="colspan"><xsl:value-of select="$colspan"/></xsl:attribute><strong>Total</strong></td>
	  			<td valign="top" colspan="2" class="TableCell"><strong><xsl:value-of select="../@total"/></strong></td>
  			</tr>
			</xsl:if>
		</xsl:if>
</xsl:template>

<xsl:template match="stat_total">
		<tr class="ignore"> 
		<xsl:for-each select="attribute[@show!='NO']">
			<td valign="top">
			<strong><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></strong></td>
		</xsl:for-each>
		</tr>
</xsl:template>

<xsl:template name="display_legend">
	<table>
		<tr>
			<td  class="formheader" colspan="2"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_KEY'"/></xsl:call-template></td>
		</tr>
		<xsl:for-each select="../graphs/graph">
		<tr>
			<td>
				<img width="30" height="6" border="0"><xsl:attribute name="src">/libertas_images/themes/site_administration/bar_middle<xsl:value-of select="position()"/>.png</xsl:attribute></img>
			</td>
			<td>
				<xsl:variable name="graph_index"><xsl:value-of select="."/></xsl:variable>
				<xsl:value-of select="../../stat_results/stat_entry/attribute[position()=$graph_index]/@name"/>
			</td>
		</tr>
		</xsl:for-each>
	</table>
</xsl:template>

<xsl:template name="display_graph">
	<xsl:param name="header" select="1"/>
	<xsl:param name="column" select="1"/>
<table border="0" cellpadding="0" cellspacing="0" summary="This table holds a form" width="100%">	
	<tr><td valign="top" class="formbackground">
	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr>
			<td class="formheader"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="../../stat_results/stat_entry/attribute[position()=$column]/@name"/></xsl:call-template></td>
		</tr>
		<tr><td class='TableCell'><table border="0" cellspacing="1" cellpadding="0">
		<xsl:for-each select="stat_results/stat_entry[(position() mod 15)=1]">
		<xsl:call-template name="display_graph_row">
			<xsl:with-param name="header"><xsl:value-of select="$header"/></xsl:with-param>
			<xsl:with-param name="modual"><xsl:choose><xsl:when test="../../stat_results/@split_on"><xsl:value-of select="../../stat_results/@split_on"/></xsl:when><xsl:otherwise>15</xsl:otherwise></xsl:choose></xsl:with-param>
			<xsl:with-param name="check"><xsl:value-of select="position()"/></xsl:with-param>
		</xsl:call-template>
		</xsl:for-each>
		<xsl:for-each select="previous/stat_results/stat_entry[(position() mod 15)=1]">
		<xsl:call-template name="display_graph_row">
			<xsl:with-param name="header"><xsl:value-of select="$header"/></xsl:with-param>
			<xsl:with-param name="modual"><xsl:choose><xsl:when test="../../stat_results/split_on"><xsl:value-of select="../../stat_results/@split_on"/></xsl:when><xsl:otherwise>15</xsl:otherwise></xsl:choose></xsl:with-param>
			<xsl:with-param name="check"><xsl:value-of select="position()"/></xsl:with-param>
		</xsl:call-template>
		</xsl:for-each>
		</table></td></tr>
	</table></td></tr>
</table>
</xsl:template>

<xsl:template name="display_graph_row">
	<xsl:param name="header" select="1"/>	
	<xsl:param name="modual"><xsl:choose><xsl:when test="../../stat_results/@split_on"><xsl:value-of select="../../stat_results/@split_on"/></xsl:when><xsl:otherwise>15</xsl:otherwise></xsl:choose></xsl:param>
	<xsl:param name="check" select="1"/>
		<xsl:variable name="start"><xsl:choose>
			<xsl:when test="(($modual * $check) - $modual)>=0">
				<xsl:value-of select="(($modual * $check) - $modual)"/>
			</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose></xsl:variable>
		<xsl:variable name="end"><xsl:value-of select="(($modual * $check))"/></xsl:variable>
		<tr>
		<xsl:for-each select="../../stat_results/stat_entry[not(position() >= $end)][position() > $start]">
			<xsl:variable name="stat_entry_index"><xsl:value-of select="position() + $start"/></xsl:variable>
			<xsl:for-each select="attribute">
				<xsl:variable name="pos"><xsl:value-of select="position()"/></xsl:variable>
				<xsl:for-each select="../../../graphs/graph[position()=$pos]">
					<xsl:variable name="index"><xsl:value-of select="."/></xsl:variable>
					<xsl:variable name="index_one"><xsl:value-of select="../../stat_results/stat_entry[position()=$stat_entry_index]/attribute[position()=$index]/@name"/></xsl:variable>
					<xsl:variable name="index_value"><xsl:value-of select="../../stat_results/stat_entry[position()=$stat_entry_index]/attribute[position()=$index]"/></xsl:variable>
					<xsl:variable name="big_number"><xsl:value-of select="../../stat_results/stat_biggest/attribute[position()=$index]" /></xsl:variable>
					<xsl:variable name="total"><xsl:value-of select="100 div $big_number"/></xsl:variable>
					<xsl:variable name="alt"><xsl:value-of select="$index_one"/><xsl:value-of select="' = '"/><xsl:value-of select="$index_value"/></xsl:variable>
					<td width="6" valign='bottom' height='110'><img width="6" border="0">
					<xsl:attribute name="src">/libertas_images/themes/site_administration/bar_center<xsl:value-of select="$pos"/>.png</xsl:attribute>
					<xsl:attribute name="height"><xsl:value-of select="round(($index_value * $total))"/></xsl:attribute>
					<xsl:attribute name="alt"><xsl:value-of select="$alt"/></xsl:attribute>
				</img></td>
				</xsl:for-each>
			</xsl:for-each>
			<td width='6' rowspan="2" valign='bottom' height='110'><img width="10" height="6" border="0"><xsl:attribute name="src">/libertas_images/themes/1x1.gif</xsl:attribute></img></td>
		</xsl:for-each>
		</tr>
		<tr>
			<xsl:for-each select="../../stat_results/stat_entry[not(position() >= $end)][position() > $start]">
				<xsl:variable name="stat_entry_index"><xsl:value-of select="position()"/></xsl:variable>
				<td align="center" class="horizontal">
				<xsl:attribute name="colspan"><xsl:value-of select="count(../../graphs/graph)"/></xsl:attribute>
				<xsl:attribute name="width"><xsl:value-of select="count(../../graphs/graph) * 6"/></xsl:attribute>
					<xsl:value-of select="attribute[position()=$header]"/>
				</td>
			</xsl:for-each>
		</tr>
</xsl:template>


</xsl:stylesheet>
