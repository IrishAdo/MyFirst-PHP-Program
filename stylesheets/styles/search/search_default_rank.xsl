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

<xsl:template name="display_search_results">
	<xsl:if test="./filter"><xsl:call-template name="display_filter"/></xsl:if>
	<xsl:if test="@name='forum'">
	<xsl:for-each select="page_options/button">
		<a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=<xsl:value-of select="@command"/>&amp;forum_identifier=<xsl:value-of select="../../@grouping"/></xsl:attribute><xsl:value-of select="@alt"/></a>
	</xsl:for-each>
	</xsl:if>
	<xsl:if test="./data_list">
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
		<table border="0" width="100%" cellpadding="0" cellspacing="0" summary="This table holds the menu information from the modules">
			<tr> 
			   	<td valign="top" class="formbackground"><table border="0" cellpadding="3" cellspacing="1" width="100%" summary="This table holds the user information">
				<xsl:call-template name="search_data_list"/>
				</table></td>
			</tr>
			<tr> 
			   	<td valign="top" align="center"><xsl:call-template name="function_page_spanning"/></td>
			</tr>
		</table>
		</xsl:if>
	</xsl:if>
	
</xsl:template>

<xsl:template name="search_data_list">
	<xsl:for-each select="data_list/results/page" >
		<xsl:sort select="sum(metadata/keywords/keyword[.=../../../../../search_keywords/search_keyword]/@count)" data-type="number" order="descending"/>
		<xsl:if test="position()=1">
		<tr class="formheader"> 
			<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_TITLE'"/></xsl:call-template></td>
			<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SCORING'"/></xsl:call-template></td>
		</tr>
		</xsl:if>
		<tr><xsl:choose>
			<xsl:when test="(position() mod 2) = 0"><xsl:attribute name="class">result_light</xsl:attribute></xsl:when>
			<xsl:when test="(position() mod 2) = 1"><xsl:attribute name="class">result_dark</xsl:attribute></xsl:when>
		</xsl:choose> 
		   	<td valign="top">
		   	<a><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute>
		   	<xsl:value-of select="title"/></a>
			<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
			<br />
		   	<xsl:if test="string-length(metadata/description)>0"><xsl:value-of select="metadata/description"/><br /></xsl:if>
						<xsl:variable name="len"><xsl:value-of select="count(locations/location)"/></xsl:variable>
						<span class="breadcrumb_text">Located in</span> ::
						<xsl:choose>
						<xsl:when test="$len>1"> <span class="breadcrumb_text">(<xsl:value-of select="$len"/>) <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'NUMBER_OF_LOCATIONS'"/></xsl:call-template></span>
						<xsl:for-each select="locations/location">
						<br />[[rightarrow]] <a class="search_locations"><xsl:attribute name="href"><xsl:value-of select="."/></xsl:attribute><xsl:call-template name="display_breadcrumb_trail">
							<xsl:with-param name="url" select="concat(substring-before(@url,'.php'),'.php')"/>       
							<xsl:with-param name="linking" select="2"/>
					   	</xsl:call-template></a></xsl:for-each>
						</xsl:when>
						<xsl:otherwise><a class="search_locations"><xsl:attribute name="href"><xsl:value-of select="locations/location"/></xsl:attribute><xsl:call-template name="display_breadcrumb_trail">
							<xsl:with-param name="url" select="concat(substring-before(locations/location/@url,'.php'),'.php')"/>       
							<xsl:with-param name="linking" select="2"/>       
					   	</xsl:call-template></a><br /></xsl:otherwise></xsl:choose>
					</td>
		   	<td align="right" valign="top">
		   	<xsl:variable name="search_phrase"><xsl:value-of select="../../../filter/form/input[@name='page_search']/@value"/></xsl:variable>
		   	<xsl:variable name="search_score"><xsl:value-of select="sum(//keyword[.=../../../../../search_keywords/search_keyword]/@count)"/></xsl:variable>
		   	<xsl:variable name="search_contains_title">
				<xsl:call-template name="sub_string_count">
					<xsl:with-param name="search_phrase" 		select="$search_phrase"/>       
					<xsl:with-param name="search_text" 			select="title"/>
					<xsl:with-param name="search_occurances" 	select="0"/>
			   	</xsl:call-template>
			</xsl:variable>
			<xsl:variable name="search_contains_summary">
				<xsl:call-template name="sub_string_count">
					<xsl:with-param name="search_phrase" 		select="$search_phrase"/>       
					<xsl:with-param name="search_text" 			select="summary"/>
					<xsl:with-param name="search_occurances" 	select="0"/>
			   	</xsl:call-template>
			</xsl:variable>
			<xsl:variable name="search_contains_body">
				<xsl:call-template name="sub_string_count">
					<xsl:with-param name="search_phrase" 		select="$search_phrase"/>       
					<xsl:with-param name="search_text" 			select="content"/>
					<xsl:with-param name="search_occurances" 	select="0"/>
			   	</xsl:call-template>
			</xsl:variable>
		   	<xsl:value-of select="$search_score + number($search_contains_body * 50) + number($search_contains_summary * 50) + number($search_contains_title * 50) + 1 "/>
			pts</td>
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