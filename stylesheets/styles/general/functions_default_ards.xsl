<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.38 $
- Modified $Date: 2005/02/14 19:23:10 $
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
		<xsl:when test="table_list"><xsl:value-of select="./table_list/@page_size"/></xsl:when>
		<xsl:when test="pagespancommon"><xsl:value-of select="./pagespancommon/@page_size"/></xsl:when>		
		<xsl:otherwise><xsl:value-of select="./data_list/@page_size"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="current_page"><xsl:choose>
		<xsl:when test="table_list"><xsl:value-of select="./table_list/@current_page"/></xsl:when>
		<xsl:when test="pagespancommon"><xsl:value-of select="./pagespancommon/@current_page"/></xsl:when>		
		<xsl:otherwise><xsl:value-of select="./data_list/@current_page"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="number_of_pages"><xsl:choose>
		<xsl:when test="table_list/@number_of_pages"><xsl:value-of select="./table_list/@number_of_pages"/></xsl:when>
		<xsl:when test="table_list/@number_of_records"><xsl:value-of select="./table_list/@number_of_records"/></xsl:when>
		<xsl:when test="pagespancommon/@number_of_pages"><xsl:value-of select="./pagespancommon/@number_of_pages"/></xsl:when>		
		<xsl:when test="data_list/@number_of_pages"><xsl:value-of select="./data_list/@number_of_pages"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="./data_list/@number_of_records"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="searchfilter"><xsl:choose>
		<xsl:when test="table_list"><xsl:value-of select="./table_list/searchfilter"/></xsl:when>
		<xsl:when test="pagespancommon"><xsl:value-of select="./pagespancommon/searchfilter"/></xsl:when>		
		<xsl:otherwise><xsl:value-of select="./data_list/searchfilter"/></xsl:otherwise>
	</xsl:choose></xsl:variable>
	<xsl:variable name="start"><xsl:choose>
		<xsl:when test="table_list"><xsl:value-of select="./table_list/@start"/></xsl:when>
		<xsl:when test="pagespancommon"><xsl:value-of select="./pagespancommon/@start"/></xsl:when>		
		<xsl:when test="data_list"><xsl:value-of select="./data_list/@start"/></xsl:when>
	</xsl:choose></xsl:variable>
	<xsl:variable name="finish"><xsl:choose>
		<xsl:when test="table_list"><xsl:value-of select="./table_list/@finish"/></xsl:when>
		<xsl:when test="pagespancommon"><xsl:value-of select="./pagespancommon/@finish"/></xsl:when>		
		<xsl:when test="data_list"><xsl:value-of select="./data_list/@finish"/></xsl:when>
	</xsl:choose></xsl:variable>	
	<xsl:variable name="number_of_records"><xsl:choose>
		<xsl:when test="pagespancommon"><xsl:value-of select="./pagespancommon/@number_of_records"/></xsl:when>		
		<xsl:when test="data_list"><xsl:value-of select="./data_list/@number_of_records"/></xsl:when>
	</xsl:choose></xsl:variable>
	
<!--
	[
	 l::<xsl:value-of select="local-name()"/>,
	 p::<xsl:value-of select="$page_size"/>,
	 n::<xsl:value-of select="$number_of_pages"/>,
	 c::<xsl:value-of select="$current_page"/>
	]
-->
	<xsl:if test="$number_of_pages>1">
	   	<xsl:if test="$current_page>'1'">
			[[leftarrow]][[nbsp]]<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get">
			<xsl:with-param name="searchfilter"><xsl:value-of select="$searchfilter"/></xsl:with-param>
			<xsl:with-param name="page" select="$current_page + -1"/></xsl:call-template></xsl:attribute><xsl:call-template name="get_translation">
				<xsl:with-param name="check" select="'LOCALE_PREVIOUS'"/>
			</xsl:call-template>[[nbsp]]<xsl:value-of select="$page_size"/></a>[[nbsp]]|
	   	</xsl:if>
		[[nbsp]]<xsl:value-of select="$start"/> - <xsl:value-of select="$finish"/>[[nbsp]]<xsl:call-template name="get_translation">
			<xsl:with-param name="check" select="'DISPLAY_OF'"/>
		</xsl:call-template>[[nbsp]]<xsl:value-of select="$number_of_records"/>[[nbsp]]
	   	<xsl:if test="number($number_of_pages) > number($current_page)">
			 |[[nbsp]]<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get">
				 <xsl:with-param name="searchfilter"><xsl:value-of select="$searchfilter"/></xsl:with-param>
				 <xsl:with-param name="page" select="$current_page+1"/>
			 </xsl:call-template></xsl:attribute><xsl:call-template name="get_translation">
				<xsl:with-param name="check" select="'LOCALE_NEXT'"/>
			</xsl:call-template>[[nbsp]]<xsl:value-of select="$page_size"/></a>[[nbsp]][[rightarrow]]
	   	</xsl:if>
		<br /> 
		[[nbsp]]<xsl:call-template name="get_translation">
			<xsl:with-param name="check" select="'LOCALE_PAGE'"/>
		</xsl:call-template>[[nbsp]] 
		<!--
		[<xsl:value-of select="$current_page"/>, <xsl:value-of select="$page_size"/>, <xsl:value-of select="count(data_list/pages/page)"/>, <xsl:value-of select="count(table_list/pages/page)"/>]
		-->
		<xsl:if test="number($current_page) > number($page_size)">[[leftarrow]]</xsl:if>
		[[nbsp]]
		<xsl:choose>
			<xsl:when test="data_list">
			   	<xsl:for-each select="data_list/pages/page">
					<xsl:variable name='curpage'><xsl:value-of select="."/></xsl:variable>
			   		<xsl:choose>
				   		<xsl:when test="$curpage=$current_page">[<xsl:value-of select="."/>][[nbsp]]</xsl:when>
				   		<xsl:otherwise>[<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get"><xsl:with-param name="searchfilter"><xsl:value-of select="$searchfilter"/></xsl:with-param><xsl:with-param name="page" select="."/></xsl:call-template></xsl:attribute><xsl:value-of select="."/></a>][[nbsp]]</xsl:otherwise>
			   		</xsl:choose>
			   	</xsl:for-each>
			</xsl:when>   	
			<xsl:when test="table_list">			
			   	<xsl:for-each select="./table_list/pages/page">
			   		<xsl:choose>
				   		<xsl:when test=".=$current_page">[<xsl:value-of select="."/>][[nbsp]]</xsl:when>
				   		<xsl:otherwise><xsl:if test="floor((. - 1) div $page_size) = floor(($current_page - 1 ) div $page_size) ">
							<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get"><xsl:with-param name="searchfilter"><xsl:value-of select="$searchfilter"/></xsl:with-param><xsl:with-param name="page" select="."/></xsl:call-template></xsl:attribute><xsl:value-of select="."/></a>[[nbsp]]</xsl:if>
				   		</xsl:otherwise>
			   		</xsl:choose>
			   	</xsl:for-each>			   	
			</xsl:when>   	
			<xsl:otherwise>
			   	<xsl:for-each select="./pagespancommon/pages/page">
			   		<xsl:choose>
				   		<xsl:when test=".=$current_page">[<xsl:value-of select="."/>][[nbsp]]</xsl:when>
				   		<xsl:otherwise><xsl:if test="floor((. - 1) div $page_size) = floor(($current_page - 1 ) div $page_size) ">
							<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get"><xsl:with-param name="searchfilter"><xsl:value-of select="$searchfilter"/></xsl:with-param><xsl:with-param name="page" select="."/></xsl:call-template></xsl:attribute><xsl:value-of select="."/></a>[[nbsp]]</xsl:if>
				   		</xsl:otherwise>
			   		</xsl:choose>
			   	</xsl:for-each>			   				
			</xsl:otherwise>
		</xsl:choose>				   	
		<xsl:if test="number($number_of_pages) > number($current_page)">
			<xsl:choose>
				<xsl:when test="number($number_of_pages - $page_size)>$current_page">[[nbsp]][[rightarrow]]</xsl:when>
				<xsl:when test="$current_page>number($number_of_pages - $page_size)"></xsl:when>
				<xsl:otherwise><xsl:choose><xsl:when test="number($number_of_pages - ($number_of_pages mod $page_size)) >  $current_page">[[nbsp]][[rightarrow]]</xsl:when></xsl:choose></xsl:otherwise>
			</xsl:choose>
		</xsl:if>
   	</xsl:if>
</xsl:template>

<xsl:template name="old_function_page_spanning">

			<xsl:if test="./data_list/@number_of_pages>1">
			   	<xsl:if test="./data_list/@current_page>'1'">
					[[leftarrow]][[nbsp]]<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get"><xsl:with-param name="page" select="./data_list/@current_page + -1"/></xsl:call-template></xsl:attribute>Previous <xsl:value-of select="./data_list/@page_size"/></a>[[nbsp]]|
			   	</xsl:if>
				[[nbsp]]<xsl:value-of select="./data_list/@start"/> - <xsl:value-of select="./data_list/@finish"/> of <xsl:value-of select="./data_list/@number_of_records"/>[[nbsp]]
			   	<xsl:if test="./data_list/@number_of_pages > ./data_list/@current_page">
					 |[[nbsp]]<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get"><xsl:with-param name="page" select="./data_list/@current_page+1"/></xsl:call-template></xsl:attribute>Next <xsl:value-of select="./data_list/@page_size"/></a>[[nbsp]][[rightarrow]]
			   	</xsl:if>
				<br /> Page 
			   	<xsl:for-each select="./data_list/pages/page">
			   		<xsl:choose>
			   		<xsl:when test=".=../../@current_page">
			   			[<xsl:value-of select="."/>][[nbsp]]
			   		</xsl:when>
			   		<xsl:otherwise>
						<xsl:if test="floor((. - 1) div ../../@page_size) = floor((../../@current_page - 1 ) div ../../@page_size) ">
						[<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get"><xsl:with-param name="page" select="."/></xsl:call-template></xsl:attribute><xsl:value-of select="."/></a>][[nbsp]]
						</xsl:if>
			   		</xsl:otherwise>
			   		</xsl:choose>
			   	</xsl:for-each>

		   	</xsl:if>
</xsl:template>

<xsl:template name="function_page_spanning_javascript">
	<xsl:if test="./data_list/@number_of_pages>1">
	   	<xsl:if test="./data_list/@current_page>'1'">
			[[leftarrow]][[nbsp]]<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get_javascript"><xsl:with-param name="page" select="./data_list/@current_page + -1"/></xsl:call-template></xsl:attribute>Previous <xsl:value-of select="./data_list/@page_size"/></a>[[nbsp]]|
	   	</xsl:if>
		[[nbsp]]<xsl:value-of select="./data_list/@start"/> - <xsl:value-of select="./data_list/@finish"/> of <xsl:value-of select="./data_list/@number_of_records"/>[[nbsp]]
	   	<xsl:if test="./data_list/@number_of_pages > ./data_list/@current_page">
			 |[[nbsp]]<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get_javascript"><xsl:with-param name="page" select="./data_list/@current_page+1"/></xsl:call-template></xsl:attribute>Next <xsl:value-of select="./data_list/@page_size"/></a>[[nbsp]][[rightarrow]]
	   	</xsl:if>
		<br /> Page 
	   	<xsl:for-each select="./data_list/pages/page">
	   		<xsl:choose>
	   		<xsl:when test=".=../../@current_page">
	   			[<xsl:value-of select="."/>][[nbsp]]
	   		</xsl:when>
	   		<xsl:otherwise>
				<xsl:if test="floor((. - 1) div ../../@page_size) = floor((../../@current_page - 1 ) div ../../@page_size) ">
				[<a><xsl:attribute name="href"><xsl:call-template name="display_form_as_get_javascript"><xsl:with-param name="page" select="."/></xsl:call-template></xsl:attribute><xsl:value-of select="."/></a>][[nbsp]]
				</xsl:if>
	   		</xsl:otherwise>
	   		</xsl:choose>
	   	</xsl:for-each>
   	</xsl:if>
</xsl:template>

<!-- 
	function to retrieve a filters fields and place in the href of a link
	ie resubmit a form.
-->
<xsl:template name="display_form_as_get">
	<xsl:param name="searchfilter"/>
	<xsl:param name="page"/>
	<xsl:value-of select="//setting[@name='real_script']"/>?<xsl:choose>
		<xsl:when test="$searchfilter!=''"><xsl:value-of select="$searchfilter"/>&amp;page=<xsl:value-of select="$page"/></xsl:when>
		<xsl:otherwise>
		<xsl:choose>
			<xsl:when test="boolean(../../../filter/form)">
		<xsl:for-each select="../../../filter/form/*">
		<xsl:choose>
			<xsl:when test="local-name()='seperator_row'">
				<xsl:for-each select="seperator/*">
					<xsl:choose>
						<xsl:when test="local-name()='text'"></xsl:when>
						<xsl:when test="option_selected"><xsl:value-of select="../@name" />=<xsl:value-of select="option_selected" />&amp;</xsl:when>
						<xsl:when test="@type='submit'"></xsl:when>
						<xsl:when test="@name='page'">&amp;page=<xsl:value-of select="$page"/><xsl:if test="position() != last()">&amp;</xsl:if></xsl:when>
						<xsl:when test="option"><xsl:for-each select="option[@selected='true']"><xsl:value-of select="../@name" />=<xsl:value-of select="@value" />&amp;</xsl:for-each></xsl:when>
						<xsl:otherwise><xsl:value-of select="@name" />=<xsl:choose>
							<xsl:when test="@value"><xsl:value-of select="@value" /></xsl:when>
							<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
						</xsl:choose>&amp;</xsl:otherwise>
					</xsl:choose>
				</xsl:for-each>
			</xsl:when>
			<xsl:when test="@type='submit'"></xsl:when>
			<xsl:when test="@name='page'">&amp;page=<xsl:value-of select="$page"/><xsl:if test="position() != last()">&amp;</xsl:if></xsl:when>
			<xsl:when test="option"><xsl:for-each select="option[@selected='true']"><xsl:value-of select="../@name" />=<xsl:value-of select="@value" />&amp;<xsl:if test="position() != last()"></xsl:if></xsl:for-each></xsl:when>
			<xsl:otherwise><xsl:value-of select="@name" />=<xsl:choose><xsl:when test="@value"><xsl:value-of select="@value" /></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose><xsl:if test="position() != last()">&amp;</xsl:if></xsl:otherwise>
			</xsl:choose>
	   	</xsl:for-each></xsl:when>
		<xsl:otherwise><xsl:for-each select="./filter/form/*">
		<xsl:choose>
			<xsl:when test="@type='submit'"></xsl:when>
			<xsl:when test="@name='page'">page=<xsl:value-of select="$page"/><xsl:if test="position() != last()">&amp;</xsl:if></xsl:when>
			<xsl:when test="option_selected"><xsl:value-of select="../@name" />=<xsl:value-of select="option_selected" />&amp;</xsl:when>
			<xsl:when test="option"><xsl:for-each select="option[@selected='true']"><xsl:value-of select="../@name" />=<xsl:value-of select="@value" />&amp;<xsl:if test="position() != last()"></xsl:if></xsl:for-each></xsl:when>
			<xsl:otherwise><xsl:value-of select="@name" />=<xsl:choose><xsl:when test="@value"><xsl:value-of select="@value" /></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose><xsl:if test="position() != last()">&amp;</xsl:if></xsl:otherwise>
			</xsl:choose>
	   	</xsl:for-each></xsl:otherwise>
		</xsl:choose>
		<xsl:if test="not(../../../filter/form/input[@name='page'])">page=<xsl:value-of select="$page"/></xsl:if></xsl:otherwise>
	</xsl:choose>
</xsl:template>
<!-- 
	function to retrieve a filters fields and place in the href of a link
	ie resubmit a form.
-->
<xsl:template name="display_form_as_get_javascript"><xsl:param name="page"/>javascript:submit_filter(<xsl:value-of select="$page"/>);</xsl:template>


<!-- 
	function to display the right image icon for a button
-->
<xsl:template name="display_icon">
	<img border="0" height="45" width="40">
		<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
		<xsl:attribute name="alt"><xsl:variable name="check"><xsl:value-of select="@alt"/></xsl:variable><xsl:value-of select="//xml_document/translator/translation[@code=$check]/@value"/></xsl:attribute>
		<xsl:attribute name="id"><xsl:value-of select="@command"/></xsl:attribute>
	</img>
</xsl:template>

<xsl:template name="replace_stringA">
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

<xsl:template name="printA">
	<xsl:param name="str_value"></xsl:param><xsl:call-template name="replace_string">
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
		<xsl:with-param name="find">&amp;quot;</xsl:with-param><xsl:with-param name="replace_with">[[quot]]</xsl:with-param></xsl:call-template></xsl:with-param>
	</xsl:call-template>
</xsl:template>

<xsl:template name="display_form">
	<xsl:param name="module"></xsl:param>
	<xsl:param name="id"></xsl:param>
	<xsl:param name="intable"></xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="show_label">0</xsl:param>
	<xsl:param name="show_label_bullet">0</xsl:param>
	<xsl:param name="extract_pos">2</xsl:param>

	<xsl:choose>
		<xsl:when test="$module!=''">
			<xsl:choose>
				<xsl:when test="$module != ''">
					<xsl:for-each select="//module[@display='form'][form/@name=$id][position()=1]">
						<xsl:apply-templates select="form">
							<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
							<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
							<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
							<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
							<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
						</xsl:apply-templates>
					</xsl:for-each>
					<xsl:for-each select="//module[@name=$module][filter/form/@name=$id][position()=1]">
						<xsl:apply-templates select="filter/form">
							<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
							<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
							<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
							<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
							<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
						</xsl:apply-templates>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select="//module[@display='form'][form/@name=$id][position()=1]">
						<xsl:apply-templates select="form">
							<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
							<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
							<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
							<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
							<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
						</xsl:apply-templates>
					</xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
			<xsl:variable name="container"><xsl:value-of select="uid"/></xsl:variable>
			<xsl:for-each select="//module/form[@name=$id]">
				<xsl:if test="position()=1">
				<xsl:apply-templates select=".">
					<xsl:with-param name="formname"><xsl:value-of select="$id"/></xsl:with-param>
					<xsl:with-param name="uid"><xsl:value-of select="$container"/></xsl:with-param>
					<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
					<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
					<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
					<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
					<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
				</xsl:apply-templates>
				</xsl:if>
			</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="show_edit_button">
<xsl:param name="cmd_starter">PAGE_</xsl:param>
<xsl:param name="cmd_finish">LIVE_EDIT</xsl:param>
<xsl:param name="identifier"></xsl:param>
<xsl:param name="intable">0</xsl:param>
<xsl:variable name="canLiveEdit"><xsl:choose>
	<xsl:when test="not(//session/admin_restriction/locations/location)">1</xsl:when>
	<xsl:when test="//menu[url=//xml_document/modules/module/setting[@name='real_script']]/@identifier = //session/admin_restriction/locations/location">2</xsl:when>
	<xsl:otherwise>0</xsl:otherwise>
</xsl:choose></xsl:variable>
	<xsl:choose>
		<xsl:when test="$image_path = '/libertas_images/themes/textonly'">
			<xsl:if test="$canLiveEdit!=0 and not(contains(//xml_document/modules/module/setting[@name='real_script'],'admin/'))">
				<xsl:variable name="id"><xsl:choose>
					<xsl:when test="$identifier!=''"><xsl:value-of select="$identifier"/></xsl:when>
					<xsl:when test="$cmd_starter='PAGE_'"><xsl:choose>
						<xsl:when test="//xml_document/modules/module/licence/product/@type='ECMS'"><xsl:value-of select="@identifier"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="@translation_identifier"/></xsl:otherwise>
					</xsl:choose></xsl:when>
					<xsl:when test="$cmd_starter='MIRRROR_'"></xsl:when>
					<xsl:when test="$cmd_starter='CLIENT_FOOTER_'"></xsl:when>
					<xsl:otherwise><xsl:value-of select="@identifier"/></xsl:otherwise>
				</xsl:choose></xsl:variable>
				<xsl:if test="//session[@logged_in='1']/groups[@type='2']">
					<xsl:if test="//session[@logged_in='1']/editorial='Yes'"><xsl:choose>
					<xsl:when test="$intable=1">[[nbsp]][&#60;a  href="admin/index.php?command=<xsl:value-of select="$cmd_starter"/><xsl:value-of select="$cmd_finish"/>&amp;identifier=<xsl:value-of select="$id"/>&amp;folder=<xsl:value-of select="//menu[url=//setting[@name='real_script']]/@identifier"/>"&#62;<xsl:choose>
						<xsl:when test="$id=''">Edit this document</xsl:when><!-- add-->
						<xsl:otherwise>Edit this document</xsl:otherwise></xsl:choose>&#60;/a&#62;]</xsl:when>
					<xsl:otherwise>[[nbsp]][<a><xsl:attribute name="href">admin/index.php?command=<xsl:value-of select="$cmd_starter"/><xsl:value-of select="$cmd_finish"/>&amp;identifier=<xsl:value-of select="$id"/>&amp;folder=<xsl:value-of select="//menu[url=//setting[@name='real_script']]/@identifier"/></xsl:attribute><xsl:choose>
						<xsl:when test="$id=''">Edit this document</xsl:when><!-- add-->
						<xsl:otherwise>Edit this document</xsl:otherwise></xsl:choose></a>]</xsl:otherwise>
					</xsl:choose>
					</xsl:if>
				</xsl:if>
			</xsl:if>
		</xsl:when>
		<xsl:otherwise>
			<xsl:if test="$canLiveEdit!=0 and not(contains(//xml_document/modules/module/setting[@name='real_script'],'admin/'))">
				<xsl:variable name="id"><xsl:choose>
					<xsl:when test="$identifier!=''"><xsl:value-of select="$identifier"/></xsl:when>
					<xsl:when test="$cmd_starter='PAGE_'"><xsl:choose>
						<xsl:when test="//xml_document/modules/module/licence/product/@type='ECMS'"><xsl:value-of select="@identifier"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="@translation_identifier"/></xsl:otherwise>
					</xsl:choose></xsl:when>
					<xsl:when test="$cmd_starter='MIRRROR_'"></xsl:when>
					<xsl:when test="$cmd_starter='CLIENT_FOOTER_'"></xsl:when>
					<xsl:otherwise><xsl:value-of select="@identifier"/></xsl:otherwise>
				</xsl:choose></xsl:variable>
				<xsl:if test="//session[@logged_in='1']/groups[@type='2'] and $id!=-1">
					<xsl:if test="//session[@logged_in='1']/editorial='Yes'"><xsl:choose>
					<xsl:when test="$intable=1">[[nbsp]]&#60;a  href="admin/index.php?command=<xsl:value-of select="$cmd_starter"/><xsl:value-of select="$cmd_finish"/>&amp;identifier=<xsl:value-of select="$id"/>&amp;folder=<xsl:value-of select="//menu[url=//setting[@name='real_script']]/@identifier"/>"&#62;<xsl:choose>
						<xsl:when test="$id=''">&#60;img src="/libertas_images/editor/libertas/live_edit.gif"  alt='Edit this document'/&#62;</xsl:when><!-- add-->
						<xsl:otherwise>&#60;img src="/libertas_images/editor/libertas/live_edit.gif"  alt='Edit this document'/#62;</xsl:otherwise></xsl:choose>&#60;/a&#62;</xsl:when>
					<xsl:otherwise>[[nbsp]]<a><xsl:attribute name="href">admin/index.php?command=<xsl:value-of select="$cmd_starter"/><xsl:value-of select="$cmd_finish"/>&amp;identifier=<xsl:value-of select="$id"/>&amp;folder=<xsl:value-of select="//menu[url=//setting[@name='real_script']]/@identifier"/></xsl:attribute><xsl:choose>
						<xsl:when test="$id=''"><img src="/libertas_images/editor/libertas/live_edit.gif" alt='Edit this document'/></xsl:when><!-- add-->
						<xsl:otherwise><img src="/libertas_images/editor/libertas/live_edit.gif" alt='Edit this document'/></xsl:otherwise></xsl:choose></a></xsl:otherwise>
					</xsl:choose>
					</xsl:if>
				</xsl:if>
			</xsl:if>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="seperator">
<td valign="top"><table border="0" cellpadding="3" cellspacing="1" class="width100percent" ><xsl:attribute name="summary">Column <xsl:value-of select="position()"/></xsl:attribute>
<xsl:apply-templates/>
</table></td>
</xsl:template>

<xsl:template name="display_footer">
	<xsl:param name="class_to_use" select="'menulevel1'"/>
<!--
	<xsl:variable name="counter"><xsl:value-of select="count(//xml_document/modules/module[@name='layout']/menu)"/></xsl:variable>
<div class="aligncenter"><xsl:for-each select="//xml_document/modules/module[@name='layout']/menu">
		<xsl:if test="position()=1">| </xsl:if><a class="footer">
			<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
		<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a> |
		<xsl:if test="$counter > 6 ">
			<xsl:if test="position() mod 6 =0 ">
				<br/> <xsl:if test="position() != last() ">| </xsl:if>
			</xsl:if>
		</xsl:if>
    </xsl:for-each></div>
-->
</xsl:template>



<xsl:template match="frame">
<xsl:if test="@show_label='1'"><strong><xsl:value-of select="label"/></strong><br/></xsl:if>
<iframe>
<xsl:attribute name='title'><xsl:value-of select="label"/></xsl:attribute>
<xsl:attribute name='width'><xsl:value-of select="@width"/></xsl:attribute>
<xsl:attribute name='height'><xsl:choose><xsl:when test="@height!=''"><xsl:value-of select="@height"/></xsl:when><xsl:otherwise>400px</xsl:otherwise></xsl:choose></xsl:attribute>
<xsl:attribute name='src'><xsl:value-of select="uri"/></xsl:attribute>
<xsl:attribute name='name'>myFrame_<xsl:value-of select="@identifier"/></xsl:attribute>
<xsl:attribute name='id'>myFrame_<xsl:value-of select="@identifier"/></xsl:attribute>
<xsl:attribute name='frameborder'>0</xsl:attribute>
<xsl:attribute name='scrolling'>auto</xsl:attribute>
<xsl:attribute name='marginwidth'>5</xsl:attribute>
<xsl:attribute name='marginheight'>0</xsl:attribute>
<xsl:attribute name='style'>display:block</xsl:attribute>
</iframe>
<xsl:if test="contains(uri,//setting[@name='domain'])">
<script>
<xsl:comment>
iframeids[iframeids.length]="myFrame_<xsl:value-of select="@identifier"/>";
// </xsl:comment>
</script>
<noframes>
<ul>
	<li><a><xsl:attribute name='href'><xsl:value-of select="uri"/></xsl:attribute>No frames version of <xsl:value-of select="label"/></a></li>
	<li><a><xsl:attribute name='href'><xsl:value-of select="//script[@name='script']"/>?command=FRAMEIT_VIEW&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute>more on <xsl:value-of select="label"/></a></li>
</ul>
</noframes>
</xsl:if>
</xsl:template>

<xsl:template name="display_micromenu2">
	<xsl:param name="show_label">0</xsl:param>
	<xsl:param name="header"></xsl:param>
	<xsl:param name="id">0</xsl:param>
	<xsl:for-each select="//module[@name='Standard Forms' and @display='embeddedInformation']/module[@name='micromenu' and @display='LINKS' and concat('libertas_',uid)=$id][position()=1]">
		<xsl:call-template name="display_micromenu">
			<xsl:with-param name="show_label">0</xsl:with-param>
			<xsl:with-param name="header"></xsl:with-param>
		</xsl:call-template>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_micromenu">
	<xsl:param name="show_label">0</xsl:param>
	<xsl:param name="header"></xsl:param>
	<xsl:param name="show_label_bullet">0</xsl:param>
	<xsl:variable name="display_format"><xsl:value-of select="display_format"/></xsl:variable> 
	<xsl:variable name="mm_display_type"><xsl:value-of select="mm_display_type"/></xsl:variable> 
	<xsl:variable name="mm_show_type"><xsl:value-of select="mm_show_type"/></xsl:variable> 
	<div class='micromenu'>
		<xsl:if test="label[@show='1'] and $show_label=0"><div class="label"><span><xsl:value-of select="label"/></span></div></xsl:if>
		<xsl:choose>
			<xsl:when test="display_format='dropdownmanual' or display_format='dropdownauto'"><form method="post">
				<xsl:attribute name="id"><xsl:value-of select="uid"/></xsl:attribute>
				<xsl:attribute name="action"><xsl:value-of select="//setting[@name='real_script']"/></xsl:attribute>
				<div class='micromenuformelements'>
				<xsl:if test="display_format='dropdownauto'">
					<script type="text/javascript">
					<xsl:comment>
					function jumpUrl(t){}
					</xsl:comment>
					</script> 
				</xsl:if>
				<input type="hidden" name="command" value="MICROMENU_JUMPTO"/>
				<div>
				<xsl:if test="label[@show!='1'] or $show_label=1">
					<xsl:attribute name="style">display:none;</xsl:attribute>
				</xsl:if>
				<label ><xsl:attribute name="for">quicklink_<xsl:value-of select="uid"/></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></label></div>
				
				<select name="quicklink"    class="quicklink"><xsl:attribute name="id">quicklink_<xsl:value-of select="uid"/></xsl:attribute>
				<xsl:if test="display_format='dropdownauto'">
					<xsl:attribute name="onchange">javascript:jumpUrl(this)</xsl:attribute>
				</xsl:if>
				<option selected='true'><xsl:attribute name="value">-1</xsl:attribute><xsl:choose>
					<xsl:when test="label[@show='1']">Select One</xsl:when>
					<xsl:otherwise><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></xsl:otherwise>
				</xsl:choose></option>
				<xsl:choose>
					<xsl:when test="menu_parent">
						<xsl:variable name="parent_identifier"><xsl:value-of select="menu_parent"/></xsl:variable>
						<xsl:for-each select="//menu[@parent=$parent_identifier and @hidden='0']">
							<xsl:variable name="found">
								<xsl:if test="url='admin/index.php' and //xml_document/modules/session/groups/@type=2">2</xsl:if>
								<xsl:choose>
									<xsl:when test="boolean(groups)">
										<xsl:for-each select="groups/option">
											<xsl:variable name="val"><xsl:value-of select="@value"/></xsl:variable>
											<xsl:for-each select="//xml_document/modules/session/groups/group">
												<xsl:if test="$val=@identifier">1</xsl:if>
											</xsl:for-each>
										</xsl:for-each>
									</xsl:when>
									<xsl:otherwise>1</xsl:otherwise>
								</xsl:choose>
							</xsl:variable>
							<xsl:if test="($found!='') or count(groups/option)=0">
								<option><xsl:attribute name="value"><xsl:choose>
									<xsl:when test="contains(url,'http:')"><xsl:value-of select="url"/></xsl:when>
									<xsl:otherwise><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="url"/></xsl:otherwise>
								</xsl:choose></xsl:attribute><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></option>
							</xsl:if>
						</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<xsl:for-each select="menulink">
							<option><xsl:attribute name="value"><xsl:value-of select="url"/></xsl:attribute><xsl:value-of select="label"/></option>
						</xsl:for-each>
					</xsl:otherwise>
				</xsl:choose>

				</select>
				<input type="submit" name='submitbutton' class="quicksubmit" value="Go" />
				<xsl:if test="display_format='dropdownauto'">
					<script src="/libertas_images/javascripts/jumpto.js" type="text/javascript">
					<xsl:comment>
					/*
					Load the jumpto script
					*/
					</xsl:comment>
					</script> 
					<script type="text/javascript">
					<xsl:comment>
					/*
					Hide the button
					*/
					jumpToHideButton("<xsl:value-of select="uid"/>");
					</xsl:comment>
					</script> 
				</xsl:if>
				</div>
			</form></xsl:when>
			<xsl:otherwise>
				<xsl:variable name="parent_identifier"><xsl:value-of select="menu_parent"/></xsl:variable>
				<xsl:variable name="current_url" select="//setting[@name='script']"/>       
				<xsl:choose>
					<xsl:when test="boolean(menulink)">
						<ul><xsl:for-each select="menulink">
							<li><a><xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute><xsl:attribute name="title"><xsl:value-of select="title"/></xsl:attribute><xsl:value-of select="label"/></a></li>
						</xsl:for-each></ul>					
					</xsl:when>
					<xsl:otherwise>
				<ul>
				<xsl:for-each select="//menu[@parent=$parent_identifier and (($mm_display_type=1 and @hidden='0') or ($mm_display_type=2 and @hidden='1') or ($mm_display_type=0))]">
					<xsl:variable name="found">
						<xsl:if test="url='admin/index.php' and //xml_document/modules/session/groups/@type=2">2</xsl:if>
						<xsl:choose>
							<xsl:when test="boolean(groups)">
								<xsl:for-each select="groups/option">
									<xsl:variable name="val"><xsl:value-of select="@value"/></xsl:variable>
									<xsl:for-each select="//xml_document/modules/session/groups/group">
										<xsl:if test="$val=@identifier">1</xsl:if>
									</xsl:for-each>
								</xsl:for-each>
							</xsl:when>
							<xsl:otherwise>1</xsl:otherwise>
						</xsl:choose>
					</xsl:variable>
					<xsl:if test="($found!='') or count(groups/option)=0">
						<li><a>
							<xsl:if test="($mm_show_type=2 and ((url=$current_url and @hidden='0' and children/menu) or (.//children/menu[url=$current_url and @hidden='0']))) or url=$current_url">
								<xsl:attribute name="class">menuon</xsl:attribute>
							</xsl:if>
							<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
							<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
								<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
							</xsl:choose></xsl:with-param></xsl:call-template></xsl:attribute>
							<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template>
						</a>
						<xsl:if test="$mm_show_type=2">
							<xsl:if test="url=$current_url and @hidden='0' and children/menu">
								<xsl:for-each select="./children">
					    			<xsl:call-template name="display_micromenu_parent">
										<xsl:with-param name="parent_identifier" select="../@identifier"/>       
										<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
										<xsl:with-param name="mm_show_type" select="$mm_show_type"/>
					       			</xsl:call-template>
					       		</xsl:for-each>
					    	</xsl:if>
							<xsl:if test=".//children/menu[url=$current_url and @hidden='0']">
								<xsl:for-each select="./children">
						    		<xsl:call-template name="display_micromenu_parent">
										<xsl:with-param name="parent_identifier" select="../@identifier"/>       
										<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
										<xsl:with-param name="mm_show_type" select="$mm_show_type"/>
					    		   	</xsl:call-template>
							 	</xsl:for-each>
					    	</xsl:if>
						</xsl:if>
						</li>
					</xsl:if>
				</xsl:for-each></ul>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:otherwise>
		</xsl:choose>
	</div>
</xsl:template>


<xsl:template name="display_content_table">
<xsl:param name="show_label">0</xsl:param>

<div class="content-table">
	<xsl:if test="boolean(label) and $show_label=0">
	<div class="label"><span><xsl:value-of select="label"/></span></div>
	</xsl:if>
	<xsl:if test="boolean(a2z)"><xsl:call-template name="display_atoz_links"/></xsl:if>
	<xsl:choose>
		<xsl:when test="form">
	<div class="contentpos">
		<form method="post">
				<xsl:attribute name="action"><xsl:value-of select="//setting[@name='real_script']"/></xsl:attribute>
			<div class='micromenuformelements'>
				<input type="hidden" name="command" value="CONTENTTABLE_JUMPTO"/>
				<div><label><xsl:attribute name="for">ctqlink_<xsl:value-of select="uid"/></xsl:attribute></label></div>
				<select name="ctqlink" class="quicklink"><xsl:attribute name="id">ctqlink_<xsl:value-of select="uid"/></xsl:attribute>
				<xsl:if test="form/display_format='dropdownauto'">
					<xsl:attribute name="onChange">javascript:jumpUrl(this)</xsl:attribute>
				</xsl:if>
				<option selected='true'><xsl:attribute name="value">-1</xsl:attribute>Quick Links</option>
				<xsl:for-each select="form/menulink">
					<option><xsl:attribute name="value"><xsl:choose>
						<xsl:when test="contains(url,'http:')"><xsl:value-of select="url"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="url"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
					<xsl:variable name="label"><xsl:value-of select="label"/></xsl:variable>
					<xsl:choose>
						<xsl:when test="string-length($label) > 40 "><xsl:value-of select="substring($label, 0, 10)"/>...<xsl:value-of select="substring($label, string-length($label) - 29)"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="$label"/></xsl:otherwise>
					</xsl:choose></option>
				</xsl:for-each>
				</select>
				<input type="submit" class="quicksubmit" value="Go" />
				</div>
				<xsl:if test="form/display_format='dropdownauto'">
					<script src="/libertas_images/javascripts/jumpto.js" type="text/css">
					/*
					Load the jumpto script
					*/
					</script> 
				</xsl:if>
				</form>
				</div>
		</xsl:when>
		<xsl:otherwise><xsl:value-of select="cdata"/></xsl:otherwise>
	</xsl:choose>
</div>
</xsl:template>


<xsl:template name="webobject_top_of_page">
	<ul class="pageoptions">	
		<li class='po-top'><a class="pagelink"><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>#top</xsl:attribute><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_TOP_OF_PAGE'"/></xsl:call-template></xsl:attribute><span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_TOP_OF_PAGE'"/></xsl:call-template></span></span></a></li>
	</ul>
</xsl:template>

<xsl:template name="webobject_home">
	<ul class="pageoptions">	
		<li class='po-home'><a class="pagelink" href="index.php"><xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_HOME'"/></xsl:call-template></xsl:attribute><span class='icon'><span class='text'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_HOME'"/></xsl:call-template></span></span></a></li>
	</ul>
</xsl:template>

<xsl:template name="display_atoz_links">
	<xsl:param name="display_more_as_text"/>
	<xsl:param name="module"></xsl:param>
	<xsl:param name="uses_class"/>
	<xsl:param name="prefix">_</xsl:param>
	<xsl:variable name="uri">
		<xsl:variable name="u"><xsl:value-of select="uri"/></xsl:variable>
		<xsl:choose>
			<xsl:when test="//module[@name='information_admin' and @display='ATOZ_WIDGET']/uri=$u">0</xsl:when>
			<xsl:when test="//module[@name='presentation' and @display='ATOZ_WIDGET'] and contains(//xml_document/modules/module/setting[@name='real_script'],$u)">0</xsl:when>			
			<xsl:otherwise>1</xsl:otherwise>
		</xsl:choose>
	</xsl:variable>
	<xsl:choose>
		<!-- 
			condition added to remove a2z from duplications
		-->
		<xsl:when test="$module='' and $uri=0"></xsl:when>
		<xsl:when test="$module=''">
			<xsl:if test="letters/letter">
				<ul class='atoz-letters'><xsl:for-each select=".//letters/letter">
					<xsl:choose>
						<xsl:when test="@count!=0">
						<li><a>
							<xsl:attribute name="class"><xsl:choose>
								<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
								<xsl:otherwise>atozlinks</xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:attribute name="title"><xsl:value-of select="."/>1</xsl:attribute>
							<xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="substring-before(//setting[@name='script'],'index.php')"/>
							<!--
							<xsl:choose>
								<xsl:when test="//setting[@name='fake_script']=''"></xsl:when>
								<xsl:otherwise><xsl:value-of select="//setting[@name='fake_script']"/>/</xsl:otherwise>
							</xsl:choose>
							-->
							<xsl:value-of select="$prefix"/><xsl:value-of select="@lcase"/>.php</xsl:attribute>
							<xsl:value-of select="."/>
						</a></li></xsl:when>
						<xsl:otherwise><li><xsl:value-of select="."/></li></xsl:otherwise>
					</xsl:choose>
				</xsl:for-each></ul>
			</xsl:if>
		</xsl:when>
		<xsl:otherwise>
			<xsl:for-each select="//module[@name=$module and @display!='ATOZ_WIDGET']">
				<xsl:variable name="u"><xsl:value-of select="uri"/></xsl:variable>
				<xsl:if test="contains(//xml_document/modules/module/setting[@name='real_script'],$u)=false">
				<xsl:if test="letters/letter">
					<ul class='atoz-letters'><xsl:for-each select=".//letters/letter">
						<xsl:choose>
							<xsl:when test="@count!=0">
							<li><a>
							<xsl:attribute name="class"><xsl:choose>
								<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
								<xsl:otherwise>atozlinks</xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:attribute name="title"><xsl:value-of select="."/>2</xsl:attribute>
							<xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:choose>
								<xsl:when test="//setting[@name='fake_script']=''"></xsl:when>
								<xsl:otherwise><xsl:value-of select="//setting[@name='fake_script']"/>/</xsl:otherwise>
							</xsl:choose><xsl:value-of select="$prefix"/><xsl:value-of select="@lcase"/>.php</xsl:attribute>
							<xsl:value-of select="."/>
							</a></li></xsl:when>
							<xsl:otherwise><li><xsl:value-of select="."/></li></xsl:otherwise>
						</xsl:choose>
					</xsl:for-each></ul>
				</xsl:if>	
				</xsl:if> 
			</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="extract_form_data">
	<xsl:param name="cdata"></xsl:param>
	<xsl:param name="extract_pos"><xsl:value-of select="../@pos"/></xsl:param>
	<!--
	[686::<xsl:value-of select="$extract_pos"/>]
	[
	<xsl:if test="contains($cdata,'img id=&amp;amp;amp;quot;libertas_form')">1</xsl:if>,
	<xsl:if test="contains($cdata,'&lt;img id=&quot;libertas_form&quot;')">2</xsl:if>,
	<xsl:if test="contains($cdata,'&amp;lt;img id=&amp;quot;libertas_form&amp;quot;')">3</xsl:if>,
	<xsl:if test="contains($cdata,'&amp;amp;lt;img id=&amp;amp;quot;libertas_form&amp;amp;quot;')">4</xsl:if>,
	<xsl:if test="contains($cdata,'img id=&#34;libertas_form')">5</xsl:if>,
	<xsl:if test="contains($cdata,'img id=')">6</xsl:if>]
	
-->
	<xsl:choose>
		<xsl:when test="contains($cdata,'&lt;img id=&quot;libertas_form&quot;')">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&lt;img id=&quot;libertas_form&quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring-after($cdata,'&lt;img id=&quot;libertas_form&quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:variable name="get_frm"><xsl:value-of select="substring-before(substring-after($cdata,'frm_identifier=&quot;'),'&quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="frm_identifier"><xsl:value-of select="$get_frm"/></xsl:variable>
			<div class='embed'>
				<xsl:call-template name="display_form"><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param><xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param></xsl:call-template>
				<xsl:call-template name="display_micromenu2"><xsl:with-param name="show_label">0</xsl:with-param><xsl:with-param name="header"></xsl:with-param><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param></xsl:call-template>
			</div>
			<xsl:if test="string-length($string_start)!=0">&lt;p&gt;</xsl:if>
			<xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="contains($cdata,'&amp;lt;img id=&amp;quot;libertas_form&amp;quot;')">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;lt;img id=&amp;quot;libertas_form&amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring-after($cdata,'&amp;lt;img id=&amp;quot;libertas_form&amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:if test="string-length($string_start)!=0">&lt;/p&gt;</xsl:if>
			<xsl:variable name="get_frm"><xsl:value-of select="substring-before(substring-after($cdata,'frm_identifier=&amp;quot;'),'&amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="frm_identifier"><xsl:value-of select="$get_frm"/></xsl:variable>
			<div class='embed'>
				<xsl:call-template name="display_form"><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param><xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param></xsl:call-template>
				<xsl:call-template name="display_micromenu2"><xsl:with-param name="show_label">0</xsl:with-param><xsl:with-param name="header"></xsl:with-param><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param></xsl:call-template>
			</div>
			<xsl:if test="string-length($string_start)!=0">&lt;p&gt;</xsl:if>
			<xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="contains($cdata,'&amp;amp;lt;img id=&amp;amp;quot;libertas_form&amp;amp;quot;')">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;amp;lt;img id=&amp;amp;quot;libertas_form&amp;amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring-after($cdata,'&amp;amp;lt;img id=&amp;amp;quot;libertas_form&amp;amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:if test="string-length($string_start)!=0">&lt;/p&gt;</xsl:if>
			<xsl:variable name="get_frm"><xsl:value-of select="substring-before(substring-after($cdata,'frm_identifier=&amp;amp;quot;'),'&amp;amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="frm_identifier"><xsl:value-of select="$get_frm"/></xsl:variable>
			<div class='embed'>
				<xsl:call-template name="display_form"><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param><xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param></xsl:call-template>
				<xsl:call-template name="display_micromenu2"><xsl:with-param name="show_label">0</xsl:with-param><xsl:with-param name="header"></xsl:with-param><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param></xsl:call-template>
			</div>
			<xsl:if test="string-length($string_start)!=0">&lt;p&gt;</xsl:if>
			<xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="contains($cdata,'&amp;amp;amp;lt;img id=&amp;amp;amp;quot;libertas_form&amp;amp;amp;quot;')">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;amp;amp;lt;img id=&amp;amp;amp;quot;libertas_form&amp;amp;amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring-after($cdata,'&amp;amp;amp;lt;img id=&amp;amp;amp;quot;libertas_form&amp;amp;amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;amp;amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:if test="string-length($string_start)!=0">&lt;/p&gt;</xsl:if>
			<xsl:variable name="get_frm"><xsl:value-of select="substring-before(substring-after($cdata,'frm_identifier=&amp;amp;amp;quot;'),'&amp;amp;amp;quot;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="frm_identifier"><xsl:value-of select="$get_frm"/></xsl:variable>
			<div class='embed'>
				<xsl:call-template name="display_form"><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param><xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param></xsl:call-template>
				<xsl:call-template name="display_micromenu2"><xsl:with-param name="show_label">0</xsl:with-param><xsl:with-param name="header"></xsl:with-param><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param></xsl:call-template>
			</div>
			<xsl:if test="string-length($string_start)!=0">&lt;p&gt;</xsl:if>
			<xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="contains($cdata,'&amp;lt;img id=&#34;libertas_form&#34;')">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&amp;lt;img id=&#34;libertas_form&#34;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring-after($cdata,'&amp;lt;img id=&#34;libertas_form&#34;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'&amp;gt;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:if test="string-length($string_start)!=0">&lt;/p&gt;</xsl:if>
			<xsl:variable name="get_frm"><xsl:value-of select="substring-before(substring-after($cdata,'frm_identifier=&#34;'),'&#34;')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="frm_identifier"><xsl:value-of select="$get_frm"/></xsl:variable>
			<div class='embed'>
				<xsl:call-template name="display_form"><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param><xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param></xsl:call-template>
				<xsl:call-template name="display_micromenu2"><xsl:with-param name="show_label">0</xsl:with-param><xsl:with-param name="header"></xsl:with-param><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param></xsl:call-template>
			</div>
			<xsl:if test="string-length($string_start)!=0">&lt;p&gt;</xsl:if>
			<xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:when test="contains($cdata,'&lt;img id=libertas_form')">
			<xsl:variable name="string_start"><xsl:value-of select="substring-before($cdata,'&lt;img id=libertas_form')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_rest"><xsl:value-of select="substring-after($cdata,'&lt;img id=libertas_form')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="string_end"><xsl:value-of select="substring-after($string_rest,'>')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:value-of select="$string_start" disable-output-escaping="yes"/>
			<xsl:if test="string-length($string_start)!=0">&lt;/p&gt;</xsl:if>
			<xsl:variable name="get_frm"><xsl:value-of select="substring-before(substring-after($cdata,'frm_identifier='),' ')" disable-output-escaping="yes"/></xsl:variable>
			<xsl:variable name="frm_identifier"><xsl:value-of select="$get_frm"/></xsl:variable>
			<div class='embed'>
				<xsl:call-template name="display_form"><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param><xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param></xsl:call-template>
				<xsl:call-template name="display_micromenu2"><xsl:with-param name="show_label">0</xsl:with-param><xsl:with-param name="header"></xsl:with-param><xsl:with-param name="id"><xsl:value-of select="$frm_identifier"/></xsl:with-param></xsl:call-template>
			</div>
			<xsl:if test="string-length($string_start)!=0">&lt;p&gt;</xsl:if>
			<xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="$string_end" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:otherwise><xsl:value-of select="$cdata" disable-output-escaping="yes"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name='show_headlines'>
	<xsl:variable name="cols"><xsl:value-of select="headline/cols"/></xsl:variable>
	
	<div class='headlines'>
		<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='presentation']/headline">
			<xsl:variable name="SumImgDis"><xsl:value-of select="//menu[url=//setting[@name='script']]/@summaryImgDisplay"/></xsl:variable>
			<div>
				
				<xsl:variable name='uri'><xsl:value-of select="uri"/></xsl:variable>
				<xsl:if test="label">
					<h3 class="headlines"><span><a>
						<xsl:attribute name="href"><xsl:value-of select="$uri"/></xsl:attribute>
						<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param></xsl:call-template></xsl:attribute>
						<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param></xsl:call-template></a></span></h3>
				</xsl:if>
				<xsl:choose>
				<xsl:when test="not(boolean(page/summary))">
					<ul >
						<xsl:for-each select="page">
						<xsl:variable name="position"><xsl:value-of select="position()"/></xsl:variable>
						<li ><xsl:attribute name="style">display:inline;clear:both;width:<xsl:value-of select="(100 div $cols) - 1"/>%;</xsl:attribute>
							<div class='title'>
								<a>
									<xsl:attribute name='href'><xsl:choose>
										<xsl:when test="../title_page/@identifier = @translation_identifier"><xsl:value-of select="$uri"/></xsl:when>
										<xsl:otherwise><xsl:value-of select="locations/location[position()=1]"/></xsl:otherwise>
									</xsl:choose></xsl:attribute>
									<xsl:attribute name='title'><xsl:value-of select="metadata/description"/></xsl:attribute>
									<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title"/></xsl:with-param></xsl:call-template>
								</a>
								<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
							</div>
						</li>
						</xsl:for-each>
					</ul>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select="page">
						<xsl:variable name="position"><xsl:value-of select="position()"/></xsl:variable>
						<div style="clear:both"><xsl:attribute name="class">columncount<xsl:value-of select="$cols"/></xsl:attribute>
							<xsl:if test="boolean(summary_files)">
								<xsl:for-each select="summary_files/file">
			<xsl:choose>
			<xsl:when test="//setting[@name='displaymode']!='textonly'">

				<img class='summaryimage'>
					<xsl:attribute name="src"><xsl:value-of select="directory"/><xsl:value-of select="md5"/><xsl:call-template name="getextension">
							<xsl:with-param name="url"><xsl:value-of select="url"/></xsl:with-param>
						</xsl:call-template></xsl:attribute>
					<xsl:attribute name="alt"><xsl:value-of select="label"/></xsl:attribute>
					<xsl:attribute name="style"><xsl:choose>
							<xsl:when test="//menu[url=//setting[@name='script']]/@summaryImgDisplay='0'">float:left;</xsl:when>
							<xsl:when test="//menu[url=//setting[@name='script']]/@summaryImgDisplay='2' and $position mod 2 = 0">float:left;</xsl:when>
							<xsl:when test="//menu[url=//setting[@name='script']]/@summaryImgDisplay='2' and $position mod 2 = 1">float:right;</xsl:when>
							<xsl:otherwise>float:right;</xsl:otherwise>
						</xsl:choose>width:<xsl:value-of select="width"/>;height:<xsl:value-of select="height"/>;border:0;</xsl:attribute>
						</img>
				</xsl:when>
				<xsl:otherwise>
				[ image: <a>
					<xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=FILES_INFO&amp;identifier=<xsl:value-of select="md5"/></xsl:attribute>
					<xsl:attribute name="title"><xsl:value-of select="label"/></xsl:attribute>
					<xsl:value-of select="label"/>
				</a>
				
				</xsl:otherwise>
				</xsl:choose>
<!--									<img class='summaryimage'>
										<xsl:attribute name="src"><xsl:value-of select="directory"/><xsl:value-of select="md5"/><xsl:call-template name="getextension">
												<xsl:with-param name="url"><xsl:value-of select="url"/></xsl:with-param>
											</xsl:call-template></xsl:attribute>
										<xsl:attribute name="alt"><xsl:value-of select="label"/></xsl:attribute>
										<xsl:attribute name="style"><xsl:choose>
												<xsl:when test="$SumImgDis='0'">float:left;</xsl:when>
												<xsl:when test="$SumImgDis='2' and $position mod 2 = 0">float:left;</xsl:when>
												<xsl:when test="$SumImgDis='2' and $position mod 2 = 1">float:right;</xsl:when>
												<xsl:otherwise>float:right;</xsl:otherwise>
											</xsl:choose>width:<xsl:value-of select="width"/>;height:<xsl:value-of select="height"/>;border:0;</xsl:attribute>
									</img>
-->
								</xsl:for-each>
							</xsl:if>
							<div class='title'>
								<a>
									<xsl:attribute name='href'><xsl:choose>
										<xsl:when test="../title_page/@identifier = @translation_identifier"><xsl:value-of select="$uri"/></xsl:when>
										<xsl:otherwise><xsl:value-of select="locations/location[position()=1]"/></xsl:otherwise>
									</xsl:choose></xsl:attribute>
									<xsl:attribute name='title'><xsl:value-of select="metadata/description"/></xsl:attribute>
									<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title"/></xsl:with-param></xsl:call-template>
								</a>
								<xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">PAGE_</xsl:with-param></xsl:call-template>
							</div>
							<xsl:if test="boolean(summary)">
								<div class='contentpos'><xsl:value-of select="summary"/></div>
							</xsl:if>
						</div>
					</xsl:for-each>
				
				</xsl:otherwise>
				</xsl:choose>
			</div>
		</xsl:for-each>
	</div>
</xsl:template>

<xsl:template name="display_micromenu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
	<xsl:param name="mm_show_type"/>
<ul>
	<xsl:for-each select="menu[@parent=$parent_identifier and @hidden='0']">
		<xsl:variable name="found">
			<xsl:if test="url='admin/index.php' and //xml_document/modules/session/groups/@type=2">2</xsl:if>
			<xsl:choose>
				<xsl:when test="boolean(groups)">
					<xsl:for-each select="groups/option">
						<xsl:variable name="val"><xsl:value-of select="@value"/></xsl:variable>
						<xsl:for-each select="//xml_document/modules/session/groups/group">
							<xsl:if test="$val=@identifier">1</xsl:if>
						</xsl:for-each>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>1</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
		<xsl:if test="($found!='') or count(groups/option)=0">
			<li>
			<a>
			<xsl:if test="($mm_show_type=2 and ((.//children/menu[url=$current_url]) or url=$current_url))">
				<xsl:attribute name="class">menuon</xsl:attribute>
			</xsl:if>
			
			<xsl:if test="@accesskey!=''">
				<xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute>
			</xsl:if>
			<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
			<xsl:attribute name="title"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template><xsl:if test="@accesskey!=''"> [<xsl:value-of select="@accesskey"/>]</xsl:if></xsl:attribute>
			<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:copy-of select="label" /></xsl:with-param></xsl:call-template></a></li>
			<xsl:if test="url=$current_url and @hidden='0' and children/menu">
				<xsl:for-each select="./children">
	    			<xsl:call-template name="display_micromenu_parent">
						<xsl:with-param name="parent_identifier" select="../@identifier"/>
						<xsl:with-param name="current_url" select="//setting[@name='script']"/>
						<xsl:with-param name="mm_show_type" select="$mm_show_type"/>
	       			</xsl:call-template>
	       		</xsl:for-each>
	    	</xsl:if>
			<xsl:if test=".//children/menu[url=$current_url and @hidden='0']">
				<xsl:for-each select="./children">
		    		<xsl:call-template name="display_micromenu_parent">
						<xsl:with-param name="parent_identifier" select="../@identifier"/>
						<xsl:with-param name="current_url" select="//setting[@name='script']"/>
						<xsl:with-param name="mm_show_type" select="$mm_show_type"/>
	    		   	</xsl:call-template>
			 	</xsl:for-each>
	    	</xsl:if>
    	</xsl:if>
	</xsl:for-each>
</ul></xsl:template>

	<xsl:template name="webobject_archive">

		<xsl:for-each select="//container/webobject/module[@name='presentation']/page_archive">
			<xsl:variable name="filter"><xsl:value-of select="@filter" /></xsl:variable>
			<xsl:choose>
				<xsl:when test="link">
				<ul>
					<xsl:variable name="contentsoflink" select="." />
					<xsl:if test="$contentsoflink!=''">
						<li><a>
							<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="link/@url"/></xsl:attribute>
							<xsl:attribute name="title"><xsl:value-of select="link"/></xsl:attribute>
							<xsl:value-of select="link"/>
						</a></li>
					</xsl:if>	
					<xsl:if test="filter">
						<xsl:apply-templates select="filter/form"></xsl:apply-templates>
					</xsl:if>
				</ul></xsl:when>
				<xsl:otherwise>
					<xsl:if test="year">
						<xsl:choose>
							<xsl:when test="$filter=''">
							<table summary="List of years archiving spans" class='downloadtablelinksg1' cellspacing="0" cellpadding="3">
								<tr>
									<td>
										Archives
									</td>
									<xsl:for-each select="year">
										<td style="white-space: nowrap;">
										<xsl:attribute name="class">downloadlink<xsl:choose>
											<xsl:when test="$filter=@id or ($filter!=@id and @link!='')">on</xsl:when>
											<xsl:otherwise>off</xsl:otherwise>
										</xsl:choose></xsl:attribute>
										 :: 
										<a>
											<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:choose>
												<xsl:when test="@link!='1'"><xsl:value-of select="@link"/></xsl:when>
												<xsl:otherwise>-<xsl:value-of select="@id"/>.php</xsl:otherwise>
											</xsl:choose></xsl:attribute>
											<xsl:attribute name="title">Archive containing articles from <xsl:value-of select="@id"/></xsl:attribute>
											<xsl:value-of select="@id"/></a></td>
									</xsl:for-each>
									<td style="width:100%">[[nbsp]]</td>
								</tr>
								</table>
							</xsl:when>
							<xsl:otherwise>
								<table summary="List of years archiving spans" class='downloadtablelinksg1' cellspacing="0" cellpadding="3">
								<tr>
									<td>
										Archives
									</td>								
									<xsl:for-each select="year">
										<td style="white-space: nowrap;">
										<xsl:attribute name="class">downloadlink<xsl:choose>
											<xsl:when test="$filter=@id">on</xsl:when>
											<xsl:otherwise>off</xsl:otherwise>
										</xsl:choose></xsl:attribute>
										 :: 
										<a>
											<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:choose>
												<xsl:when test="@link!='1'"><xsl:value-of select="@link"/></xsl:when>
												<xsl:otherwise>-<xsl:value-of select="@id"/>.php</xsl:otherwise>
											</xsl:choose></xsl:attribute>
											<xsl:attribute name="title">Archive containing articles from <xsl:value-of select="@id"/></xsl:attribute>
											<xsl:value-of select="@id"/></a></td>
									</xsl:for-each><td style="width:100%">[[nbsp]]</td>
								</tr>
								</table>
								<xsl:if test="year[@id=$filter]/month">
								<table summary="months for the selected year" class="downloadtablelinks" cellspacing="0" cellpadding="3">
									<tr class="downloadrow"><xsl:for-each select="year[@id=$filter]/month">
										<td class="entry" >
										<xsl:choose>
											<xsl:when test="@link='1'">
											<a>
											<xsl:attribute name="href"><xsl:value-of select="//setting[@name='fake_script']"/>/-<xsl:value-of select="../@id"/>-<xsl:value-of select="@id"/>.php</xsl:attribute>
											<xsl:attribute name="title"><xsl:value-of select="@id"/></xsl:attribute>
											<xsl:value-of select="@id"/></a>
											</xsl:when>											
											<xsl:otherwise>
												<xsl:value-of select="@id"/>
											</xsl:otherwise>
										</xsl:choose>											
										</td>
									</xsl:for-each><td style="width:100%">[[nbsp]]</td></tr>
									</table>
								</xsl:if>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:template>

	<xsl:template name="display_banner">
		<xsl:if test="banner">
			<xsl:choose>
				<xsl:when test="//setting[@name='displaymode']='pda'">
					<xsl:for-each select="banner">
							<div align='left'>
								<a><xsl:attribute name="href">-/_clickme.php?ad=<xsl:value-of select="@identifier"/></xsl:attribute>
										<xsl:if test="@open_new_window='1'">
											<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
										</xsl:if>
									<span class='icon'><span class='text'><xsl:value-of select="label"/></span></span>
								</a><br/>
								<xsl:for-each select="text"><xsl:value-of select="."/><br /></xsl:for-each>
								<span class='homepage'><xsl:value-of select="homepage"/></span>
							</div>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test="./banner[@type='txt']">
						<div><xsl:attribute name="class">bannertxt<xsl:if test="count(banner)!=1">-<xsl:choose>
								<xsl:when test="banner/@direction='0'">horizontal</xsl:when>
								<xsl:otherwise>vertical</xsl:otherwise>
							</xsl:choose></xsl:if></xsl:attribute>
							<ul>
								<xsl:for-each select="banner[@type='txt']">
									<li class='bannertxt'><div style='text-align:left;windth:auto'>
										<a><xsl:attribute name="href">-/_clickme.php?ad=<xsl:value-of select="@identifier"/>&amp;f=1</xsl:attribute>
											<xsl:if test="@open_new_window='1'">
												<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
											</xsl:if>
										<span class='icon'><span class='text'><xsl:value-of select="label"/></span></span>
									</a>
									<xsl:for-each select="text"><div><xsl:value-of select="."/></div></xsl:for-each>
									<span class='homepage'><xsl:value-of select="homepage"/></span></div>
								</li>
								</xsl:for-each>
							</ul>
						</div>
					</xsl:if>
					<xsl:if test="./banner[@type='gfx']">
						<div><xsl:attribute name="class">bannergfx<xsl:if test="count(banner)!=1">-<xsl:choose>
								<xsl:when test="banner/@direction='0'">horizontal</xsl:when>
								<xsl:otherwise>vertical</xsl:otherwise>
							</xsl:choose></xsl:if></xsl:attribute>
							<ul>
								<xsl:for-each select="banner[@type='gfx']">
									<li class='banner'>
										<a>
											<xsl:attribute name="href">-/_clickme.php?ad=<xsl:value-of select="@identifier"/>&amp;f=0</xsl:attribute>
										<xsl:if test="@open_new_window='1'">
											<xsl:attribute name="rel">_libertasExternalWindow</xsl:attribute>
										</xsl:if>
										<img class='bannerimage'>
											<xsl:attribute name="src"><xsl:value-of select="src"/></xsl:attribute>
											
											<xsl:attribute name="alt"><xsl:value-of select="label"/>[[altreturn]]<xsl:for-each select="text"><xsl:value-of select="."/>[[altreturn]]</xsl:for-each><xsl:value-of select="homepage"/></xsl:attribute>
											<xsl:attribute name="style">margin:0;padding:0;<xsl:if test="width!=0">width:<xsl:value-of select="width"/>;</xsl:if><xsl:if test="height!=0">height:<xsl:value-of select="height"/>;</xsl:if>border:0;</xsl:attribute>
										</img></a></li>
								</xsl:for-each>
							</ul>
						</div>
					</xsl:if>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:if>
	</xsl:template>
	
</xsl:stylesheet>

