<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/08/24 13:22:00 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:include href="../../styles/general/display_list_results.xsl"/>

<xsl:template name="display_search_results">
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="intable">0</xsl:param>
	<xsl:param name="alternative_is_image"><xsl:value-of select="$alternative_is_image"/></xsl:param>
	<xsl:if test="./filter"><xsl:call-template name="display_filter">
		<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
		<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
	</xsl:call-template></xsl:if>
	<xsl:if test="@name='forum'">
	<xsl:for-each select="page_options/button">
		<a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=<xsl:value-of select="@command"/>&amp;forum_identifier=<xsl:value-of select="../../@grouping"/></xsl:attribute><xsl:value-of select="@alt"/></a>
	</xsl:for-each>
	</xsl:if>
	<xsl:if test="./data_list and ( filter/form/input[@name='search']!=0)">
		<xsl:if test="./data_list/@number_of_records='0'">
		<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'SORRY_NO_RESULTS'"/></xsl:call-template></p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records='1'">
		<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ONE_RESULT'"/></xsl:call-template></p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records>'1'">
		<p>
			<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAYING_RESULTS'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@start"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAY_TO'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@finish"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAY_OF'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@number_of_records"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'RESULT'"/></xsl:call-template>
		</p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records>='1'">
		<table border="0" width="100%" cellpadding="0" cellspacing="0" summary="This table holds the results of the serach you submitted">
			<tr> 
			   	<td valign="top" class="formbackground"><table border="0" cellpadding="3" cellspacing="1" width="100%" summary="">
				<xsl:call-template name="search_data_list"><xsl:with-param name="alternative_is_image"><xsl:value-of select="$alternative_is_image"/></xsl:with-param></xsl:call-template>
				</table></td>
			</tr>
			<tr> 
			   	<td valign="top" class="center"><xsl:call-template name="function_page_spanning"/></td>
			</tr>
		</table>
		</xsl:if>
	</xsl:if>
	
</xsl:template>

<xsl:template name="search_data_list">
	<xsl:param name="alternative_is_image">_NOT_SET_</xsl:param>
	<xsl:for-each select="data_list/results/page" >
		<xsl:sort select="sum(metadata/keywords/keyword[.=../../../../../search_keywords/search_keyword]/@count)" data-type="number" order="descending"/>
		<xsl:if test="position()=-1">
		<tr > 
			<td valign="top" class="TableHeader"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_TITLE'"/></xsl:call-template></td>
		</tr>
		</xsl:if>
		<tr><xsl:if test="$alternative_is_image!='_NOT_SET_'"><xsl:choose>
		<xsl:when test="(position() mod 2) = 0"><xsl:attribute name="background"><xsl:value-of select="$image_path"/>/search_bg.gif</xsl:attribute></xsl:when>
		<xsl:when test="(position() mod 2) = 1"><xsl:attribute name="background"><xsl:value-of select="$image_path"/>/search_bg_alt.gif</xsl:attribute></xsl:when>
		</xsl:choose></xsl:if><xsl:choose>
		<xsl:when test="(position() mod 2) = 0"><xsl:attribute name="class">TableCell</xsl:attribute></xsl:when>
		<xsl:when test="(position() mod 2) = 1"><xsl:attribute name="class">TableCellAlt</xsl:attribute></xsl:when>
		</xsl:choose> 
		   	<td valign="top"><xsl:if test="$alternative_is_image!='_NOT_SET_'"><xsl:choose>
		<xsl:when test="(position() mod 2) = 0"><xsl:attribute name="background"><xsl:value-of select="$image_path"/>/search_bg.gif</xsl:attribute></xsl:when>
		<xsl:when test="(position() mod 2) = 1"><xsl:attribute name="background"><xsl:value-of select="$image_path"/>/search_bg_alt.gif</xsl:attribute></xsl:when>
		</xsl:choose></xsl:if>
			<xsl:variable name="id"><xsl:value-of select="@identifier"/></xsl:variable>
		   	<a><xsl:attribute name="href"><xsl:choose>
				<xsl:when test="../ranking[@identifier=$id]/@rank!=1"><xsl:value-of select="locations/location[position()=1]"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="locations/location[position()=1]/@url"/></xsl:otherwise>
			</xsl:choose></xsl:attribute><xsl:value-of select="title" disable-output-escaping="yes"/></a>
			<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
			<br />
		   	<xsl:if test="string-length(metadata/description)>0"><xsl:value-of select="metadata/description" disable-output-escaping="yes"/>...<br /></xsl:if>
						<xsl:variable name="len"><xsl:value-of select="count(locations/location)"/></xsl:variable>
						<span class="breadcrumb_text">Located in</span> ::
						<xsl:choose>
						<xsl:when test="$len>1"> 
						<xsl:for-each select="locations/location">
						<br />[[rightarrow]] <a><xsl:attribute name="href"><xsl:value-of select="."/></xsl:attribute><xsl:call-template name="display_breadcrumb_trail">
							<xsl:with-param name="url" select="concat(substring-before(@url,'.php'),'.php')"/>       
							<xsl:with-param name="youarehere" select="0"/>
							<xsl:with-param name="linking" select="2"/>
					   	</xsl:call-template></a></xsl:for-each>
						</xsl:when>
						<xsl:otherwise><a><xsl:attribute name="href"><xsl:value-of select="locations/location/@url"/></xsl:attribute><xsl:call-template name="display_breadcrumb_trail">
							<xsl:with-param name="url" select="concat(substring-before(locations/location/@url,'.php'),'.php')"/>       
							<xsl:with-param name="youarehere" select="0"/>
							<xsl:with-param name="linking" select="2"/>       
					   	</xsl:call-template></a><br /></xsl:otherwise></xsl:choose>
					</td>
  		</tr>
		<tr>
			<td valign="top" colspan="2"><img alt="" border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/hr.gif</xsl:attribute></img></td>
		</tr>
	</xsl:for-each>
	<xsl:for-each select="data_list/results/vehicle" >
						<tr><xsl:choose>
		<xsl:when test="(position() mod 2) = 0"><xsl:attribute name="class">TableCell</xsl:attribute></xsl:when>
		<xsl:when test="(position() mod 2) = 1"><xsl:attribute name="class">TableCellAlt</xsl:attribute></xsl:when>
		</xsl:choose> 
							<td width="80" ><xsl:if test="image_thumbnail[@exists=1]"><img border="0" width="80"><xsl:attribute name="src"><xsl:value-of select="image_thumbnail"/></xsl:attribute></img></xsl:if></td>
							<td valign="top">
								<xsl:if test="licence_plate!=''"><strong>Licence Plate : </strong><xsl:value-of select="licence_plate"/><br /></xsl:if>
								<xsl:if test="year!=''"><strong>Year : </strong><xsl:value-of select="year"/><br /></xsl:if>
								<xsl:if test="manufacturer_description!=''"><strong>Manufacturer : </strong><xsl:value-of select="manufacturer_description"/><br /></xsl:if>
								<xsl:if test="model_description!=''"><strong>Model : </strong><xsl:value-of select="model_description"/><br /></xsl:if>
								<xsl:if test="price!=''"><strong>Price : </strong><xsl:value-of select="price"/><br /></xsl:if>
							</td>
						</tr>
						<tr><xsl:choose>
		<xsl:when test="(position() mod 2) = 0"><xsl:attribute name="class">TableCell</xsl:attribute></xsl:when>
		<xsl:when test="(position() mod 2) = 1"><xsl:attribute name="class">TableCellAlt</xsl:attribute></xsl:when>
		</xsl:choose> 
							<td valign="top" colspan="2"><a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=VEHICLE_DISPLAY&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute>View more details</a></td>
						</tr>
	</xsl:for-each>
	<xsl:for-each select="data_list/results/entry" >
						<tr><xsl:choose>
		<xsl:when test="(position() mod 2) = 0"><xsl:attribute name="class">TableCell</xsl:attribute></xsl:when>
		<xsl:when test="(position() mod 2) = 1"><xsl:attribute name="class">TableCellAlt</xsl:attribute></xsl:when>
		</xsl:choose> 
							<td valign="top">
								<xsl:for-each select="field[position()=1]">
									<xsl:value-of select="label"/> :: <xsl:value-of select="value"/><br/>
								</xsl:for-each>
							</td>
						</tr>
						<tr><xsl:choose>
		<xsl:when test="(position() mod 2) = 0"><xsl:attribute name="class">TableCell</xsl:attribute></xsl:when>
		<xsl:when test="(position() mod 2) = 1"><xsl:attribute name="class">TableCellAlt</xsl:attribute></xsl:when>
		</xsl:choose> 
							<td valign="top" colspan="2"><a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=VEHICLE_DISPLAY&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute>View more details</a></td>
						</tr>
	</xsl:for-each>

</xsl:template>

<xsl:template name="sub_string_count">
	<xsl:param name="search_phrase"/>
	<xsl:param name="search_text"/>
	<xsl:param name="search_occurances" select="0"/>
	<xsl:variable name="condition_true">
		<xsl:choose>
			<xsl:when test="contains($search_text,$search_phrase)">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>

	<xsl:variable name="found"><xsl:value-of select="$search_occurances + $condition_true"/></xsl:variable>
	<xsl:if test="$condition_true=1">
		<xsl:call-template name="sub_string_count">
			<xsl:with-param name="search_phrase" 		select="$search_phrase"/>       
			<xsl:with-param name="search_text" 			select="substring-after($search_text,$search_phrase)"/>
			<xsl:with-param name="search_occurances" 	select="$found"/>
	   	</xsl:call-template>
	</xsl:if>	
	<xsl:if test="$condition_true=0"><xsl:value-of select="$found"/></xsl:if>	
	
</xsl:template>



</xsl:stylesheet>