<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.14 $
- Modified $Date: 2005/03/16 11:16:23 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="display_list_results">
	<xsl:param name="showfilter">1</xsl:param>
	<xsl:param name="showtext">1</xsl:param>
	<xsl:param name="showa2z">0</xsl:param>
<!--
	[<xsl:value-of select="local-name()"/>]
	[<xsl:value-of select="@name"/>]
	[<xsl:value-of select="@module"/>]
-->
	<xsl:choose>
		<xsl:when test="label!=''">
			<h1><xsl:if test="count(//modules/container/webobject/module[@display!='LATEST']/page)=0"><xsl:attribute name='class'>entrylocation</xsl:attribute></xsl:if>
			<span class='icon'><span class='text'><xsl:value-of select="label"/></span></span></h1>
		</xsl:when>
		<xsl:when test="./data_list/@number_of_records >= '1' and count(./../module[@name='guestbook'])=0">
			<h1><xsl:if test="count(//modules/container/webobject/module[@display!='LATEST']/page)=0"><xsl:attribute name='class'>entrylocation</xsl:attribute></xsl:if>
			<span class='icon'><span class='text'><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></span></span></h1>
		</xsl:when>
	</xsl:choose>
	<xsl:if test="$showa2z=1"><div class='a2z'><xsl:call-template name="display_atoz_links"/></div></xsl:if>
	<xsl:if test="./filter and $showfilter=1"><xsl:call-template name="display_filter"/></xsl:if>
	<xsl:if test="./text and $showtext=1">1<xsl:apply-templates select="text" /></xsl:if>
	<xsl:if test="page_options/button">
		<ul class="moduleoptions">
			<xsl:for-each select="page_options/button">
			<li><xsl:if test="@iconify!=''"><xsl:attribute name='class'><xsl:value-of select="@command"/></xsl:attribute></xsl:if><a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="../../@grouping"/></xsl:attribute><span class='icon'><span class='text'><xsl:value-of select="@alt"/></span></span></a></li>
			</xsl:for-each>
		</ul> 
	</xsl:if>
	<div class="resultlist">
	<xsl:if test="./data_list or ./table_list">
		<!--
		<xsl:if test="./data_list/@number_of_records='0' or ./table_list/@number_of_records='0'">
			<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'SORRY_NO_RESULTS'"/></xsl:call-template></p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records='1' or ./table_list/@number_of_records='1'">
			<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ONE_RESULT'"/></xsl:call-template></p>
		</xsl:if>
		-->
		<xsl:if test="(./data_list/@number_of_records>'1' or ./table_list/@number_of_records>'1') and (./data_list/@number_of_pages>'1' or ./table_list/@number_of_pages>'1')">
			<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAYING_RESULTS'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@start"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAY_TO'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@finish"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAY_OF'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@number_of_records"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'RESULT'"/></xsl:call-template></p>
		</xsl:if>
	</xsl:if>
	</div>
	<xsl:choose>
		<xsl:when test="../display_format=2">
			<div class="results">
				<xsl:if test="./data_list/@number_of_records>='1'">
					<div class="contentpos">
						<xsl:for-each select="data_list/entry">
							<div class="entrytitle"><strong><xsl:value-of select="attribute[@name='Author']"/></strong></div>
							<div class="contentpos"><xsl:value-of select="attribute[@name='Date']"/></div>
							<div class="contentpos"><xsl:value-of select="attribute[@name='Msg']"/></div>
						</xsl:for-each>
					</div>
				</xsl:if>
			</div>
		</xsl:when>
		<xsl:otherwise>
			<xsl:if test="./data_list/@number_of_records>='1' or ./table_list/@number_of_records>='1'">
				<xsl:choose>
					<xsl:when test="data_list/text"><div class="contentpos"><xsl:value-of select="data_list/text"/></div>
					<div class="aligncenter"><xsl:call-template name="function_page_spanning"/></div>
					</xsl:when>
					<xsl:when test="table_list/text"><div class="contentpos"><xsl:value-of select="table_list/text"/></div>
					<div class="aligncenter"><xsl:call-template name="function_page_spanning"/></div>
					</xsl:when>
					<xsl:otherwise>
						<div class="contentpos"><xsl:call-template name="display_data_list"/></div>
						<div align="center"><xsl:call-template name="function_page_spanning"/></div>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:if>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_data_list">
	<!--
		display pages
	-->
	<xsl:variable name="fake_uri"><xsl:value-of select="fake_uri"/></xsl:variable>
	<xsl:if test="boolean(data_list/content/info/results/entry)">
	<!--changed searchresults to panelcontainer below SB-->
	<div class="slider-wrap">
	<div id="slider1" class="csw">
		<div class="panelContainer">
			<xsl:for-each select="data_list/content/info/results/entry" >			
				<xsl:call-template name="display_entry">
					<xsl:with-param name="fake_path"><xsl:value-of select="$fake_uri"/></xsl:with-param>
					<xsl:with-param name="entry_identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
					<xsl:with-param name="directory_identifier"><xsl:value-of select="../../@list"/></xsl:with-param>
				</xsl:call-template>			
			</xsl:for-each>
		</div>
		</div>
		</div>
	</xsl:if>

	<xsl:if test="boolean(data_list/content/info/results/entry)">
		<div class="searchresults"> 
			<xsl:for-each select="data_list/results/page" >
				<xsl:if test="position()=1">
					<div class="TableHeader"> 
						<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_TITLE'"/></xsl:call-template>
					</div>
				</xsl:if>
				<div>
					<xsl:choose>
						<xsl:when test="(position() mod 2) = 0"><xsl:attribute name="class">TableCell</xsl:attribute></xsl:when>
						<xsl:when test="(position() mod 2) = 1"><xsl:attribute name="class">TableCellAlt</xsl:attribute></xsl:when>
					</xsl:choose> 
				   	<a><xsl:attribute name="href"><xsl:value-of select="locations/location[position()=1]"/></xsl:attribute><xsl:value-of select="title" disable-output-escaping="yes"/></a><br />
					   	<xsl:if test="string-length(summary)>0"><xsl:value-of select="summary" disable-output-escaping="yes"/>...<br /></xsl:if>
						<xsl:variable name="len"><xsl:value-of select="count(locations/location)"/></xsl:variable>
						<span class="breadcrumb_text">Located in</span> :: 
						<xsl:choose>
							<xsl:when test="$len>1"> 
								<xsl:for-each select="locations/location">
									<br />[[rightarrow]] <xsl:call-template name="display_breadcrumb_trail">
										<xsl:with-param name="url" select="concat(substring-before(@url,'.php'),'.php')"/>       
										<xsl:with-param name="youarehere" select="0"/>
										<xsl:with-param name="linking" select="2"/>
						   			</xsl:call-template>
								</xsl:for-each>
							</xsl:when>
						<xsl:otherwise><xsl:call-template name="display_breadcrumb_trail">
								<xsl:with-param name="url" select="concat(substring-before(locations/location/@url,'.php'),'.php')"/>       
								<xsl:with-param name="youarehere" select="0"/>
								<xsl:with-param name="linking" select="2"/>       
			   				</xsl:call-template><br />
						</xsl:otherwise>
					</xsl:choose>
	  			</div>
			</xsl:for-each>
		</div>
	</xsl:if>
	<!--
		display vechiles
	-->
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
	<!--
		display entries
	-->
	<xsl:if test="boolean(data_list/entry)">
		<div class='results' style=''>
			<xsl:for-each select="data_list/entry" >
				<div class='row' style='vertical-align:top;'>
					<div style='display:inline;vertical-align:top;width=100%;padding:2px' id='info'>
						<xsl:for-each select="attribute[@show='Column1']">
							<xsl:choose>
								<xsl:when test="position()=1"><strong><xsl:value-of select = "."/></strong><br/></xsl:when>
								<xsl:otherwise><xsl:value-of select = "."/><br/></xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
					</div>
					<div style='display:inline;width:100%;padding:2px;vertical-align:top;' id='contents'>
						<xsl:for-each select="attribute[@show='Column2']"><xsl:value-of select = "."/></xsl:for-each>
					</div>
				</div>
				<xsl:if test="entry_options/button">
					<div class='row'>
						<ul>
						<xsl:for-each select="entry_options/button">
							<li><a><xsl:attribute name='href'><xsl:value-of select = "//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=<xsl:value-of select = "@command"/>&amp;identifier=<xsl:value-of select = "../../@identifier"/></xsl:attribute><span class='icon'><span class='text'><xsl:value-of select = "@alt"/></span></span></a></li>
						</xsl:for-each>
						</ul>
					</div>
				</xsl:if>
			</xsl:for-each>
		</div>
	</xsl:if>
	<xsl:if test="table_list">
		<table class="sortable" cellspacing="0" width="100%" cellpadding="3" sumamry = "items available to buy">
		<xsl:for-each select="table_list/entry" >
			<xsl:if test="position()=1">
				<tr>
				<xsl:for-each select="attribute[@show='YES']">
				   	<th valign="top"><xsl:value-of select = "@name"/></th>
				</xsl:for-each>
				<xsl:if test="entry_options/button">
			 	 	<th valign="top">Options</th>
				</xsl:if>
		  		</tr>
			</xsl:if>
			<tr>
				<xsl:for-each select="attribute[@show='YES']">
			   	<td valign="top"><xsl:value-of select = "."/></td>
				</xsl:for-each>
				<xsl:variable name="countbuttons"><xsl:value-of select="count(entry_options/button)"/></xsl:variable>
				<xsl:if test="entry_options/button">
			   	<td valign="top"><xsl:if test="$countbuttons!=1">| </xsl:if>
				<xsl:for-each select="entry_options/button">
					<a><xsl:attribute name='href'><xsl:value-of select = "//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/>?command=<xsl:value-of select = "@command"/>&amp;identifier=<xsl:value-of select = "../../@identifier"/></xsl:attribute><xsl:value-of select = "@alt"/></a> <xsl:if test="$countbuttons!=1">| </xsl:if>
				</xsl:for-each>
				</td>
				</xsl:if>
	  		</tr>
		</xsl:for-each>
		</table>
	</xsl:if>

</xsl:template>


</xsl:stylesheet>