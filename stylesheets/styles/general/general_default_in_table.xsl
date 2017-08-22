<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/09/15 10:54:25 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!--
This function will take an ignore parameter in the format of '[IGNORE_TXT1][IGNORE_TXT2][...]'

we are using the substring-after function which will return empty if the ignore command is not found.
-->
<xsl:variable name="company_name"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_CONTACT_COMPANY'"/></xsl:call-template></xsl:variable>
<xsl:include href="../../styles/general/header.xsl"/>
<xsl:include href="../../styles/general/format_date.xsl"/>
<xsl:include href="../../styles/general/footer_data.xsl"/>
<xsl:include href="../../styles/general/wai_compliance.xsl"/>

<xsl:template name="display_modules">
	<xsl:param name="ignore"/>
	<xsl:param name='display_more_as_text'/>
	<xsl:variable name="other_ignore"><xsl:choose>
		<xsl:when test="contains($ignore,'[page]')">[presentation]</xsl:when>
		<xsl:otherwise></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:for-each select="//modules/module">
		   	<xsl:choose>
    			<xsl:when test="(substring-after($ignore,@name)!='' or substring-after($other_ignore,@name)!='') and (@display!='form' and @display!='search_results')"></xsl:when>
    			<xsl:when test="substring-after($ignore,@display)!=''"></xsl:when>
				<xsl:when test="@display='table'"><xsl:call-template name="display_table"/></xsl:when>
    			<xsl:when test="@name='layout'"></xsl:when>
				<xsl:when test="@display='form'"><xsl:apply-templates select="form"/></xsl:when>
		    	<xsl:when test="@display='reference'"></xsl:when>
		    	<xsl:when test="@display='search_results'"><xsl:call-template name="display_search_results"/></xsl:when>
				<xsl:when test="@name='forum' and @display='results'"><xsl:call-template name="display_forum_results"/></xsl:when>
				<xsl:when test="@display='results'"><xsl:call-template name="display_list_results"/></xsl:when>
		    	<xsl:when test="data_list"><xsl:call-template name="display_list_results"/></xsl:when>
				<xsl:when test="@display='filter'"><xsl:apply-templates select="filter/form"/></xsl:when>
		    	<xsl:when test="@display='LATEST'"><xsl:call-template name="display_latest"/></xsl:when>
		    	<xsl:when test="@display='LOCATION'"><xsl:if test="page">
			    	<ul>
   					<xsl:for-each select="page">
		    			<li><xsl:value-of select="substring-before(publishdate,' ')"/> - 
						<a><xsl:attribute name="href"><xsl:value-of select="locations/location[@url=//setting[@name='script']]/@url"/>?command=PRESENTATION_DISPLAY&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute><xsl:value-of select="title"/></a></li>
		    		</xsl:for-each>
					</ul>
				</xsl:if></xsl:when>
    			<xsl:when test="@display='ENTRY'">
					<xsl:choose>
						<xsl:when test="count(page)=1">
							<xsl:call-template name="display_this_page">
								<xsl:with-param name="title">0</xsl:with-param>
								<xsl:with-param name="alt_title">0</xsl:with-param>
								<xsl:with-param name="content">1</xsl:with-param>
								<xsl:with-param name="enable_discussion">1</xsl:with-param>
								<xsl:with-param name="style">LOCATION</xsl:with-param>
								<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
								<xsl:with-param name="identifier"><xsl:value-of select="//modules/module[@display='ENTRY']/page[position()=1]/@identifier"/></xsl:with-param>
							</xsl:call-template>
						</xsl:when>
						<xsl:otherwise>
		   					<xsl:for-each select="page">
								<xsl:choose>
								<xsl:when test="position()=1">
								<xsl:call-template name="display_this_page">
									<xsl:with-param name="title">1</xsl:with-param>
									<xsl:with-param name="alt_title">1</xsl:with-param>
									<xsl:with-param name="content">1</xsl:with-param>
									<xsl:with-param name="back">1</xsl:with-param>
									<xsl:with-param name="date_publish">0</xsl:with-param>
									<xsl:with-param name="style">LOCATION</xsl:with-param>
									<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
									<xsl:with-param name="enable_discussion">1</xsl:with-param>
									<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
								</xsl:call-template>
								</xsl:when>
								<xsl:otherwise>
								<xsl:call-template name="display_this_page">
									<xsl:with-param name="title">1</xsl:with-param>
									<xsl:with-param name="alt_title">1</xsl:with-param>
									<xsl:with-param name="content">1</xsl:with-param>
									<xsl:with-param name="back">1</xsl:with-param>
									<xsl:with-param name="date_publish">0</xsl:with-param>
									<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
									<xsl:with-param name="enable_discussion">1</xsl:with-param>
									<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
								</xsl:call-template>
								</xsl:otherwise>
								</xsl:choose>
				    		</xsl:for-each>
						</xsl:otherwise>
					</xsl:choose>
<!--
	    			<xsl:if test="substring(string(//menu/@display-options),'PRESENTATION_DISPLAY')">
		    			<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/></xsl:attribute>Return to section</a>
    				</xsl:if >
-->
					<xsl:if test="./vehicle">
		    		<table summary="Vehicle information" width="100%" border="0"><xsl:attribute name="summary">This table contains the content for a vehicle</xsl:attribute>
		    			<xsl:for-each select="vehicle">
		    			<tr>
							<td valign="top">
								<xsl:if test="licence_plate!=''"><strong>Licence Plate : </strong><xsl:value-of select="licence_plate" disable-output-escaping="yes"/><br /></xsl:if>
								<xsl:if test="year!=''"><strong>Year : </strong><xsl:value-of select="year" disable-output-escaping="yes"/><br /></xsl:if>
								<xsl:if test="manufacturer_description!=''"><strong>Manufacturer : </strong><xsl:value-of select="manufacturer_description" disable-output-escaping="yes"/><br /></xsl:if>
								<xsl:if test="model_description!=''"><strong>Model : </strong><xsl:value-of select="model_description" disable-output-escaping="yes"/><br /></xsl:if>
								<xsl:if test="gears_description!=''"><strong>Gears : </strong><xsl:value-of select="gears_description" disable-output-escaping="yes"/><br /></xsl:if>
								<xsl:if test="cab_description!=''"><strong>Cab Type : </strong><xsl:value-of select="cab_description" disable-output-escaping="yes"/><br /></xsl:if>
								<xsl:if test="body_description!=''"><strong>Body Type : </strong><xsl:value-of select="body_description" disable-output-escaping="yes"/><br /></xsl:if>
								<xsl:if test="price!='' and price!='0'"><strong>Price : </strong><xsl:value-of select="price" disable-output-escaping="yes"/><br /></xsl:if>
								<xsl:if test="tax_month!='' or tax_year!=''"><strong>Taxed : </strong><xsl:value-of select="tax_month" disable-output-escaping="yes"/> / <xsl:value-of select="tax_year" disable-output-escaping="yes"/><br /></xsl:if>
								<xsl:if test="engine_size!=''"><strong>Engine Size : </strong><xsl:value-of select="engine_size" disable-output-escaping="yes"/><br /></xsl:if>
								<xsl:if test="test!=''"><strong>Test Type : </strong><xsl:choose><xsl:when test="test='0'">MOT</xsl:when><xsl:otherwise>PSV</xsl:otherwise></xsl:choose><br /></xsl:if>
								<xsl:if test="description!=''"><p><strong>Description : </strong><br/><xsl:value-of select="description" disable-output-escaping="yes"/></p></xsl:if>
							</td>
							<td width="300" valign="top"><xsl:if test="image_main[@exists=1]"><img border="0" width="300"><xsl:attribute name="src"><xsl:value-of select="image_main"/></xsl:attribute></img></xsl:if></td>
						</tr>
						<xsl:if test="position()!=last()"><tr><td colspan="2"><hr/></td></tr></xsl:if>
		    			</xsl:for-each>
			    	</table>
<!--
					<xsl:if test="//menu[//setting[@name='script']=@url]/display_options/display='VEHICLE_DISPLAY'">
			    		<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='script']"/></xsl:attribute>Return to section</a>
					</xsl:if>
-->
		    		</xsl:if>
		    	</xsl:when>
				<xsl:when test="@name='mirror'"></xsl:when>
				<xsl:when test="@display='embeddedInformation'"><!-- we want to hide these entries --></xsl:when>
		    	<xsl:otherwise><xsl:apply-templates/></xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
</xsl:template>

<xsl:template name="display_filter">
	<xsl:param name="intable">0</xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<div class="filter">
	<xsl:apply-templates select="./filter/*">
		<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
		<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
	</xsl:apply-templates>
	</div>
</xsl:template>
</xsl:stylesheet>