<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.6 $
- Modified $Date: 2005/01/31 09:47:38 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<!-- 
	function to display page spanning on results
-->
<xsl:template name="function_page_spanning">

<!--
	define some variables to make the code easier to read and reduce the amount of tree navigation in the processing of the XSL 
-->	
	
	<xsl:variable name="page_size"><xsl:choose>
		<xsl:when test="boolean(table_list)"><xsl:value-of select="table_list/@page_size"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="data_list/@page_size"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
		
	<xsl:variable name="current_page"><xsl:choose>
		<xsl:when test="boolean(table_list)"><xsl:value-of select="table_list/@current_page"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="data_list/@current_page"/></xsl:otherwise>
	</xsl:choose></xsl:variable>

	<xsl:variable name="number_of_pages"><xsl:choose>
		<xsl:when test="boolean(table_list)"><xsl:value-of select="table_list/@number_of_pages"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="data_list/@number_of_pages"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
		
	
<!--
	[p::<xsl:value-of select="$page_size"/>,n::<xsl:value-of select="$number_of_pages"/>,c::<xsl:value-of select="$current_page"/>]
-->
	<xsl:if test="$number_of_pages>1">
			   	<xsl:if test="$current_page>'1'">
				
					[[leftarrow]][[nbsp]]<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get"><xsl:with-param name="page" select="$current_page + -1"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation">
						<xsl:with-param name="check" select="'LOCALE_PREVIOUS'"/>
					</xsl:call-template>[[nbsp]]<xsl:value-of select="$page_size"/></a>[[nbsp]]|
			   	</xsl:if>
				[[nbsp]]
				<xsl:choose>
					<xsl:when test="boolean(table_list)"><xsl:value-of select="table_list/@start"/> - <xsl:value-of select="table_list/@finish"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="data_list/@start"/> - <xsl:value-of select="data_list/@finish"/></xsl:otherwise>
				</xsl:choose>
				[[nbsp]]
				<xsl:call-template name="get_translation">
					<xsl:with-param name="check" select="'DISPLAY_OF'"/>
				</xsl:call-template>
				[[nbsp]]
				<xsl:choose>
					<xsl:when test="boolean(table_list)"><xsl:value-of select="table_list/@number_of_records"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="data_list/@number_of_records"/></xsl:otherwise>
				</xsl:choose>
				[[nbsp]]
			   	<xsl:if test="number($number_of_pages) > number($current_page)">
					 |[[nbsp]]<a><xsl:attribute name="href"><xsl:choose>
					 	<xsl:when test="./table_list"><xsl:call-template name="display_form_as_get"><xsl:with-param name="page" select="./table_list/@current_page + 1"/></xsl:call-template></xsl:when>
					 	<xsl:otherwise><xsl:call-template name="display_form_as_get"><xsl:with-param name="page" select="./data_list/@current_page + 1"/></xsl:call-template></xsl:otherwise>
					 </xsl:choose></xsl:attribute><xsl:call-template name="get_translation">
						<xsl:with-param name="check" select="'LOCALE_NEXT'"/>
					</xsl:call-template>[[nbsp]]<xsl:value-of select="$page_size"/></a>[[nbsp]][[rightarrow]]
			   	</xsl:if>
				<br /> 
				
				[[nbsp]]<xsl:call-template name="get_translation">
				<xsl:with-param name="check" select="'LOCALE_PAGE'"/>
			</xsl:call-template>[[nbsp]] 
			<xsl:if test="number($current_page)>number($page_size)">[[leftarrow]]</xsl:if>[[nbsp]]
				<xsl:choose>
					<xsl:when test="boolean(table_list)">
						<xsl:for-each select="table_list/pages/page">
					   		<xsl:choose>
						   		<xsl:when test=".=$current_page">
				   					[<xsl:value-of select="."/>][[nbsp]]
				   				</xsl:when>
						   		<xsl:otherwise>
									<xsl:if test="floor((. - 1) div $page_size) = floor(($current_page - 1 ) div $page_size) ">
										<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get"><xsl:with-param name="page" select="."/></xsl:call-template></xsl:attribute><xsl:value-of select="."/></a>[[nbsp]]
									</xsl:if>
						   		</xsl:otherwise>
					   		</xsl:choose>
					   	</xsl:for-each>
			   	</xsl:when>
					<xsl:otherwise>
						<xsl:for-each select="data_list/pages/page">
					   		<xsl:choose>
						   		<xsl:when test=".=$current_page">
				   					[<xsl:value-of select="."/>][[nbsp]]
				   				</xsl:when>
						   		<xsl:otherwise>
									<xsl:if test="floor((. - 1) div $page_size) = floor(($current_page - 1 ) div $page_size) ">
										<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get"><xsl:with-param name="page" select="."/></xsl:call-template></xsl:attribute><xsl:value-of select="."/></a>[[nbsp]]
									</xsl:if>
						   		</xsl:otherwise>
					   		</xsl:choose>
					   	</xsl:for-each>
					</xsl:otherwise>
				</xsl:choose>
				<xsl:if test="number($number_of_pages) > number($current_page)">
				<xsl:choose>
					<xsl:when test="number($number_of_pages - $page_size)>$current_page">[[nbsp]][[rightarrow]]</xsl:when>
					<xsl:when test="$current_page>number($number_of_pages - $page_size)"></xsl:when>
					<xsl:otherwise><xsl:choose>
					<xsl:when test="number($number_of_pages - ($number_of_pages mod $page_size)) >  $current_page">[[nbsp]][[rightarrow]]</xsl:when>
					</xsl:choose></xsl:otherwise>
				</xsl:choose>
				</xsl:if>

		   	</xsl:if>
</xsl:template>

<xsl:template name="function_page_spanning_javascript">
<!--
	define some variables to make the code easier to read and reduce the amount of tree navigation in the processing of the XSL 
	<xsl:variable name="page_size"><xsl:value-of select="@page_size"/></xsl:variable>
	<xsl:variable name="current_page"><xsl:value-of select="@current_page"/></xsl:variable>
	<xsl:variable name="number_of_pages"><xsl:value-of select="@number_of_pages"/></xsl:variable>
-->	
<xsl:variable name="page_size"><xsl:choose>
		<xsl:when test="boolean(table_list)"><xsl:value-of select="table_list/@page_size"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="data_list/@page_size"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
		
	<xsl:variable name="current_page"><xsl:choose>
		<xsl:when test="boolean(table_list)"><xsl:value-of select="table_list/@current_page"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="data_list/@current_page"/></xsl:otherwise>
	</xsl:choose></xsl:variable>

	<xsl:variable name="number_of_pages"><xsl:choose>
		<xsl:when test="boolean(table_list)"><xsl:value-of select="table_list/@number_of_pages"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="data_list/@number_of_pages"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
<!--
	[p::<xsl:value-of select="$page_size"/>,n::<xsl:value-of select="$number_of_pages"/>,c::<xsl:value-of select="$current_page"/>]
-->
	<xsl:if test="$number_of_pages>1">
			   	<xsl:if test="$current_page>'1'">
			[[leftarrow]][[nbsp]]<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get_javascript"><xsl:with-param name="page" select="$current_page + -1"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation">
				<xsl:with-param name="check" select="'LOCALE_PREVIOUS'"/>
			</xsl:call-template>[[nbsp]]<xsl:value-of select="$page_size"/></a>[[nbsp]]|
		</xsl:if>
		[[nbsp]]<xsl:choose>
			<xsl:when test="table_list"><xsl:value-of select="@start"/> - <xsl:value-of select="@finish"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="./data_list/@start"/> - <xsl:value-of select="./data_list/@finish"/></xsl:otherwise>
			</xsl:choose>[[nbsp]]<xsl:call-template name="get_translation">
				<xsl:with-param name="check" select="'DISPLAY_OF'"/>
			</xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@number_of_records"/>[[nbsp]]
	   	<xsl:if test="./data_list/@number_of_pages > ./data_list/@current_page">
			 |[[nbsp]]<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get_javascript"><xsl:with-param name="page" select="./data_list/@current_page + 1"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation">
				<xsl:with-param name="check" select="'LOCALE_NEXT'"/>
			</xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@page_size"/></a>[[nbsp]][[rightarrow]]
		</xsl:if>
		<br />
		[[nbsp]]<xsl:call-template name="get_translation">
			<xsl:with-param name="check" select="'LOCALE_PAGE'"/>
		</xsl:call-template>[[nbsp]]
		<xsl:if test="$current_page>$page_size">[[leftarrow]]</xsl:if>[[nbsp]]
	   	<xsl:for-each select="./data_list/pages/page">
	   		<xsl:choose>
	   		<xsl:when test=".=$current_page">
	   			[<xsl:value-of select="."/>][[nbsp]]
	   		</xsl:when>
	   		<xsl:otherwise>
				<xsl:if test="floor((. - 1) div ../../@page_size) = floor((../../@current_page - 1 ) div ../../@page_size) ">
				<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get_javascript"><xsl:with-param name="page" select="."/></xsl:call-template></xsl:attribute><xsl:value-of select="."/></a>[[nbsp]]
				</xsl:if>
	   		</xsl:otherwise>
	   		</xsl:choose>
	   	</xsl:for-each>
		<xsl:if test="number($number_of_pages) > number($current_page)">
			<xsl:choose>
				<xsl:when test="number($number_of_pages - $page_size)>$current_page">[[nbsp]][[rightarrow]]</xsl:when>
				<xsl:when test="$current_page>number($number_of_pages - $page_size)"></xsl:when>
				<xsl:otherwise><xsl:choose>
				<xsl:when test="number($number_of_pages - ($number_of_pages mod $page_size)) >  $current_page">[[nbsp]][[rightarrow]]</xsl:when>
				</xsl:choose></xsl:otherwise>
			</xsl:choose>
		</xsl:if>
	</xsl:if>
</xsl:template>

<!-- 
	function to retrieve a filters fields and place in the href of a link
	ie resubmit a form.
-->
<xsl:template name="display_form_as_get"><xsl:param name="page"/>admin/index.php?<xsl:choose>
	<xsl:when test ="../../../filter/form">
		<xsl:for-each select="../../../filter/form/*">
		<xsl:if test="position() != 1">&amp;</xsl:if>
			<xsl:choose>
				<xsl:when test="@type='submit'"></xsl:when>
				<xsl:when test="local-name()='choose_categories'">filter_category=<xsl:value-of select="../specified_categorys/@identifier"/></xsl:when>
				<xsl:when test="@name='page'">page=<xsl:value-of select="$page"/></xsl:when>
				<xsl:when test="option"><xsl:for-each select="option[@selected='true']"><xsl:value-of select="../@name" />=<xsl:value-of select="@value" /></xsl:for-each></xsl:when>
				<xsl:otherwise><xsl:value-of select="@name" />=<xsl:choose><xsl:when test="@value"><xsl:value-of select="@value" /></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose><xsl:if test="position() != last()">&amp;</xsl:if></xsl:otherwise>
			</xsl:choose>
	   	</xsl:for-each>
	</xsl:when>		
	<xsl:when test ="./filter/form">
		<xsl:for-each select="./filter/form/*">
		<xsl:if test="position() != 1">&amp;</xsl:if>
			<xsl:choose>
				<xsl:when test="@type='submit'"></xsl:when>
				<xsl:when test="local-name()='choose_categories'">filter_category=<xsl:value-of select="../specified_categorys/@identifier"/></xsl:when>
				<xsl:when test="@name='page'">page=<xsl:value-of select="$page"/></xsl:when>
				<xsl:when test="option"><xsl:for-each select="option[@selected='true']">
				<xsl:value-of select="../@name" />=<xsl:value-of select="@value" />
				</xsl:for-each></xsl:when>
				<xsl:otherwise><xsl:value-of select="@name" />=<xsl:choose><xsl:when test="@value"><xsl:value-of select="@value" /></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose><xsl:if test="position() != last()">&amp;</xsl:if></xsl:otherwise>
			</xsl:choose>	
	   	</xsl:for-each>
	</xsl:when>		
	<xsl:otherwise>command=<xsl:value-of select="./data_list/@command"/><xsl:value-of select="../../@command"/>&amp;page=<xsl:value-of select="$page"/></xsl:otherwise>
</xsl:choose>
</xsl:template>
<!-- 
	function to retrieve a filters fields and place in the href of a link
	ie resubmit a form.
-->
<xsl:template name="display_form_as_get_javascript"><xsl:param name="page"/>javascript:submit_filter(<xsl:value-of select="$page"/>);</xsl:template>

<!-- 
	function to display a filter if it exists.
-->
<xsl:template name="display_filter">
	<xsl:apply-templates select="//filter"/>
</xsl:template>

<!-- 
	function to display the right image icon for a button
-->
<xsl:template name="display_icon">
	<img border="0" >
		<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
		<xsl:attribute name="alt">
		<xsl:variable name="possible_alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@alt"/></xsl:call-template></xsl:variable>
		<xsl:variable name="possible_value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:variable>
		<xsl:value-of select="$possible_value"/><xsl:value-of select="$possible_alt"/>
		</xsl:attribute>
	   	<xsl:attribute name="id"><xsl:value-of select="@command"/></xsl:attribute>
	   	<xsl:attribute name="name"><xsl:value-of select="@command"/></xsl:attribute>

	</img>
</xsl:template>


<xsl:template name="get_file_extension">
	<xsl:param name="file"/>
	<xsl:param name="has"><xsl:choose>
	<xsl:when test="contains($file,'.')">1</xsl:when>
	<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:param>
	
	<xsl:variable name="values">
	<xsl:choose>
		<xsl:when test="contains($file,'.')">
				<xsl:call-template name="get_file_extension">
					<xsl:with-param name="file" select="substring-after($file,'.')"/>
				<xsl:with-param name="has" select="$has"/>
			 </xsl:call-template>
	   	</xsl:when>
		<xsl:otherwise>
			<xsl:if test="$has=1">
				<xsl:value-of select="$file"/>
			</xsl:if>
		</xsl:otherwise>
 	</xsl:choose>
	</xsl:variable>
	<xsl:choose>
		<xsl:when test="$values!=''"><xsl:value-of select="$values"/></xsl:when>
		<xsl:otherwise>lsl</xsl:otherwise>
	</xsl:choose>
</xsl:template>
</xsl:stylesheet>

