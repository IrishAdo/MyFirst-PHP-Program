<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.8 $
- Modified $Date: 2005/02/09 12:14:34 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:variable name="image_path">/libertas_images/themes/site_administration</xsl:variable>	 	
<xsl:variable name="counter" >1</xsl:variable>
<xsl:variable name="number_of_pages">0</xsl:variable>
<xsl:variable name="max_depth">0</xsl:variable>

<xsl:include href="../../themes/site_administration/file_common.xsl"/>
<xsl:include href="../../themes/site_administration/functions.xsl"/>
<xsl:include href="../../themes/site_administration/print.xsl"/>
<xsl:include href="../../localisation.xsl"/>
<xsl:include href="../../themes/site_administration/bc_default.xsl"/>
<xsl:include href="../../themes/site_administration/debug.xsl"/>
<xsl:include href="../../themes/site_administration/sections.xsl"/>
	 	
<xsl:template match="/">

<html>
<base><xsl:attribute name="href">http://<xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='domain']"/><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/></xsl:attribute></base>
<head>
<title>Libertas-Solutions :: Administration :: </title>
<link rel="stylesheet" href="/libertas_images/themes/site_administration/style.css" />
<script src="/libertas_images/javascripts/module_retrieve.js"></script>
<script src="/libertas_images/javascripts/generic_functions.js"></script>
</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0"><xsl:attribute name="onload">javascript:check_association()</xsl:attribute>
<!--
<xsl:choose>
	<xsl:when test="//modules/module/file"></xsl:when>
	<xsl:when test="//modules/module/bread_crumbs"></xsl:when>
	<xsl:when test="//modules/module[@name='page']"><xsl:attribute name="onload">javascript:page_check_links()</xsl:attribute></xsl:when>
	<xsl:when test="//modules/module[@name='contact']"><xsl:attribute name="onload">javascript:contact_check_links()</xsl:attribute></xsl:when>
	<xsl:when test="//modules/module[@name='layout']"><xsl:attribute name="onload">javascript:layout_check_links()</xsl:attribute></xsl:when>
	<xsl:when test="//modules/module[@name='files']"><xsl:attribute name="onload">javascript:check_links()</xsl:attribute></xsl:when>
	<xsl:otherwise></xsl:otherwise>
</xsl:choose>
-->

<table border="0" cellpadding="10" cellspacing="0" width="100%" summary="This table is used to layout the information on this page">
	<xsl:apply-templates select="//xml_document/modules/module[@name!='client' and @name!='system_prefs']"/>
	<xsl:if test="/xml_document/debugging">
	<tr> 
   		<td valign="top"><xsl:apply-templates select="/xml_document/debugging"/></td>
	</tr>
	</xsl:if>
</table>
</body>
<script>
<xsl:comment>
	var entry_list = Array();
	<xsl:if test="//module[@name='group']">
		list_of_groups = Array(
			<xsl:for-each select="//option">Array('<xsl:value-of select="@value"/>','<xsl:value-of select="."/>')<xsl:if test="position()!=last()">, 
			</xsl:if></xsl:for-each>
		)
		check_links();
	</xsl:if>
	<xsl:if test="//module/@name='files'">
		check_links();
	</xsl:if>
	//</xsl:comment>
</script>
</html>
</xsl:template>
<xsl:template match="modules">
	<xsl:apply-templates select="module"/>
</xsl:template>

<xsl:template match="module">
<xsl:choose>
<xsl:when test="@display='associated_pages'">
<script>
	list = '<xsl:for-each select="associate"><xsl:value-of select="@page"/><xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>';
	description  = "<table>";<xsl:for-each select="associate">
	<xsl:variable name="location"><xsl:value-of select="@location"/></xsl:variable>
	<xsl:variable name="url"><xsl:value-of select="//menu[@identifier=$location]/url"/></xsl:variable>
	description += '<tr><td><xsl:call-template name="display_breadcrumb_trail"><xsl:with-param name="url" select="$url"/><xsl:with-param name="linking" select="2"/></xsl:call-template> [[rightarrow]] <xsl:value-of select="."/></td></tr>';</xsl:for-each>
	description += "</table>";

	save_to_doc(list, description, '<xsl:value-of select="note"/>', '<xsl:value-of select="hidden"/>');
</script>
</xsl:when>
<xsl:when test="groups">
<script>
	list = '<xsl:value-of select="list"/>';
	<xsl:variable name='list'><xsl:value-of select="list"/></xsl:variable>
	description  = "<ul>";
	<xsl:for-each select="groups/option">
		<xsl:variable name='val'> <xsl:value-of select="@value"/>,</xsl:variable>
		<xsl:if test="contains($list,$val)">
		description += '<li><xsl:value-of select="." disable-output-escaping="yes"/></li>';
		</xsl:if>
	</xsl:for-each>
	description += "</ul>";
	save_to_doc(list, description, '<xsl:value-of select="note"/>', '<xsl:value-of select="hidden"/>');
</script></xsl:when>
<xsl:when test="people">
<script>
	list = '<xsl:value-of select="list"/>';
	description  = "<ul>";<xsl:for-each select="people/person">
	description += '<li><xsl:value-of select="."/></li>';</xsl:for-each>
	description += "</ul>";
	save_to_doc(list, description, '<xsl:value-of select="note"/>', '<xsl:value-of select="hidden"/>');
</script>
</xsl:when>
<xsl:when test="files">
<script>
	file_list = '<xsl:for-each select="files/file"><xsl:value-of select="@identifier"/>,</xsl:for-each>';
	file_des ='';
	<xsl:choose>
		<xsl:when test="files/file">
			<xsl:for-each select="files/file">
			file_des += "<xsl:value-of select="label"/>::<xsl:value-of select="icon"/>::<xsl:value-of select="@identifier"/>::<xsl:value-of select="width"/>::<xsl:value-of select="height"/>::<xsl:value-of select="size"/>::<xsl:value-of select="md5"/>::<xsl:value-of select="ext"/><xsl:if test="position()!=last()">:1234567890:</xsl:if>";
			</xsl:for-each>
		</xsl:when>
		<xsl:otherwise>
			file_des  = "";
		</xsl:otherwise>
	</xsl:choose>
	//alert (file_list+'\n\n\n 4'+file_des);
	save_to_doc(file_list,file_des, '<xsl:value-of select="note"/>', '<xsl:value-of select="hidden"/>');
</script></xsl:when>
<xsl:when test="bread_crumbs">
<script>

	menu_list	= ' <xsl:for-each select="bread_crumbs/menu"><xsl:value-of select="@identifier"/>, </xsl:for-each>';
	menu_des	= "<ul>";
	<xsl:for-each select="bread_crumbs/menu">
	menu_des   += '<li><xsl:call-template name="display_breadcrumb_trail"><xsl:with-param name="url" select="url"/><xsl:with-param name="linking" select="2"/></xsl:call-template></li>';
	</xsl:for-each>
	menu_des   += "</ul>";
/*
	add ability to store group identifiers in form
*/
save_to_doc(menu_list,menu_des, '<xsl:value-of select="note"/>', '<xsl:value-of select="hidden"/>');
//alert(menu_list+", "+menu_des);

</script>
</xsl:when>
<xsl:otherwise>
<xsl:if test="page_options">
  	 	<tr><td align='right' valign="top" class="PAGE_HEADER"><xsl:apply-templates select="page_options"/></td></tr>
</xsl:if>
		<tr><td><table width="100%">
   	<xsl:choose>
    	<xsl:when test="@display='results'"><tr><td valign="top"><xsl:call-template name="display_results"/></td></tr></xsl:when>
		<xsl:when test="@display='form'"><tr><td valign="top">
		<xsl:apply-templates select="form"/>
		</td></tr></xsl:when>
	</xsl:choose></table></td></tr>
	</xsl:otherwise></xsl:choose>
</xsl:template>

<xsl:template name="display_results">
	<xsl:if test="./filter"><xsl:call-template name="display_filter"/></xsl:if>
	<xsl:if test="./data_list">
		<xsl:if test="./data_list/@number_of_records='0'">
		<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'SORRY_NO_RESULTS'"/></xsl:call-template></p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records='1'">
		<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ONE_RESULT'"/></xsl:call-template></p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records>'1'">
		<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAYING_RESULTS'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@start"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAY_TO'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@finish"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAY_OF'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="./data_list/@number_of_records"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'RESULT'"/></xsl:call-template></p>
		</xsl:if>
		<xsl:if test="./data_list/@number_of_records>='1'">
		<table border="0" width="100%" cellpadding="0" cellspacing="0" summary="This table holds the menu information from the modules">
			<tr> 
			   	<td valign="top" class="formbackground"><form name="display_list"><table border="0" cellpadding="3" cellspacing="1" width="100%" summary="This table holds the user information">
				<xsl:apply-templates select="data_list"/>
				</table></form></td>
			</tr>
			<tr> 
			   	<td valign="top" align="center"><xsl:call-template name="function_page_spanning_javascript"/></td>
			</tr>
		</table>
		</xsl:if>
	</xsl:if>
</xsl:template>

<xsl:template match="data_list">
	<xsl:for-each select="entry">
		<tr>
		<xsl:attribute name="class"><xsl:choose>
		<xsl:when test="(position() mod 2) = 1">TableCell_alt</xsl:when>
		<xsl:otherwise>TableCell</xsl:otherwise></xsl:choose></xsl:attribute>
	   	 	<td>
			<xsl:choose>
				<xsl:when test="../../onlyone='1'"><input type="radio" name="file_list[]">
					<xsl:choose>
						<xsl:when test="attribute/@name='PAGE_IDENTIFIER'">
						   	<xsl:attribute name="onclick">javascript:manage(<xsl:value-of select="attribute[@name='PAGE_IDENTIFIER']"/>,1);</xsl:attribute>
						   	<xsl:attribute name="value"><xsl:value-of select="attribute[@name='PAGE_IDENTIFIER']"/></xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
						   	<xsl:attribute name="onclick">javascript:manage(<xsl:value-of select="@identifier"/>,1);</xsl:attribute>
						   	<xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
			   	</input>
				</xsl:when>
				<xsl:otherwise>
		   	 	<input type="checkbox" name="file_list[]">
					<xsl:choose>
						<xsl:when test="attribute/@name='PAGE_IDENTIFIER'">
						   	<xsl:attribute name="onclick">javascript:manage(<xsl:value-of select="attribute[@name='PAGE_IDENTIFIER']"/>,0);</xsl:attribute>
						   	<xsl:attribute name="value"><xsl:value-of select="attribute[@name='PAGE_IDENTIFIER']"/></xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
						   	<xsl:attribute name="onclick">javascript:manage(<xsl:value-of select="@identifier"/>,0);</xsl:attribute>
						   	<xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute>
						</xsl:otherwise>
					</xsl:choose>
			   	</input>
				</xsl:otherwise>
			</xsl:choose>
			</td>
			<xsl:if test="attribute[@show='ICON']">
			<td><img><xsl:attribute name="src">/libertas_images/icons/mime-images/<xsl:value-of select="attribute[@show='ICON']"/>.gif</xsl:attribute></img></td>
			</xsl:if>
			<td valign="top" width="100%">
				<p><span class="field_txt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="attribute[@show='TITLE']/@name"/></xsl:call-template> :: </span><strong>
				<xsl:value-of select="attribute[@show='TITLE']" disable-output-escaping="yes"/></strong><br/>
				<xsl:if test="attribute[@show='REPLY_TO']">
					<xsl:variable name="rec"><xsl:value-of select="attribute[@show='REPLY_TO']"/></xsl:variable>

					<span class="field_txt"><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="attribute[@show='REPLY_TO']/@name" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template> :: </span>
					<xsl:for-each select="../entry">
						<xsl:if test="@identifier=$rec">
							<xsl:value-of select="position()"/>
						</xsl:if>
					</xsl:for-each>
					<br/>
				</xsl:if>
				<xsl:if test="attribute[@show='SUMMARY']">
				<xsl:for-each select="attribute[@show='SUMMARY']">
				<xsl:choose>
					<xsl:when test="string-length(.)!=0"><span class="field_txt"><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="@name" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template> :: </span><xsl:value-of select="." disable-output-escaping="yes" /></xsl:when>
					<xsl:otherwise><span class="field_txt"><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="@name" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template> :: </span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_NA'"/></xsl:call-template></xsl:otherwise>
				</xsl:choose><br/>
				</xsl:for-each>
				</xsl:if></p>
			</td><td valign="top"><table summary="A table to hold some attribute information of the document in question" width="350">
			<xsl:for-each select="attribute[@show='YES' and .!='']">
			<tr>
				<td class="field_txt" width="150" align="right" valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template> :: </td>
				<td class="field_value" valign="top"><xsl:choose>
					<xsl:when test="@link!='NO'">
					<xsl:variable name="link"><xsl:value-of select="@link"/></xsl:variable>
					<a><xsl:attribute name="href"><xsl:value-of select="../attribute[@name=$link]"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></a></xsl:when>
					<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:otherwise>
				</xsl:choose></td>
			</tr>
			</xsl:for-each></table></td>
  		</tr>
	</xsl:for-each>
	
</xsl:template>

<xsl:template match="choose_categories">
<xsl:param name="show_label">0</xsl:param>
<xsl:choose>
	<xsl:when test="local-name(../..)='filter'">
			<xsl:if test="$show_label=1">
			   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="label"/></xsl:call-template></td>
			</xsl:if> 
	<td><select name="filter_category">
	<option value='ALL'>All</option>
	<xsl:for-each select="category">
	<option>
		<xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute>
		<xsl:if test="@identifier = //specified_categorys/@identifier"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>
		<xsl:value-of select="label"/>
	</option>
		<xsl:if test="children/category">
			<xsl:call-template name="display_child_choose_categories_option">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
				<xsl:with-param name="padding">[[nbsp]]-[[nbsp]]</xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>	
	</select></td>
	</xsl:when>
	<xsl:otherwise>
<xsl:if test="label">
	<tr>
		<td><xsl:value-of select="label"/></td>
	</tr>
</xsl:if>
<!--
<tr>
	<td><a><xsl:attribute name="href">javascript:category_fn('CATEGORYADMIN_ADD','-1');</xsl:attribute>Add New Sub-Category</a></td>
</tr>
-->
	<tr>	
		<td width="50%" valign="top"><div id="CategoryList"></div></td>
		<td width="50%" valign="top"><xsl:if test="//choose_categories/@can_add=1">
			<div id='addLink'><input type='button' class='bt' onclick="javascript:newCategory.displayForm();">
				<xsl:attribute name="value"><xsl:value-of select="add"/></xsl:attribute>
			</input></div>
			<table id="newCategoryForm"></table>
		</xsl:if></td>
	</tr>
	<input type='hidden' name='newCategories' value=''/>
	<script src='/libertas_images/javascripts/newCategory.js'></script>
	<script>
	<xsl:comment>
		var newCategory = new newCategories();
		newCategory.id	= '<xsl:value-of select="@identifier"/>';
		newCategory.list	= new Array(
		<xsl:for-each select="category">
			new Array(<xsl:value-of select="@parent"/>,<xsl:value-of select="@identifier"/>,"<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>", <xsl:choose>
				<xsl:when test="@identifier = //specified_categorys/@identifier">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>)<xsl:if test="children/category">, <xsl:call-template name="showcategoriesJS"></xsl:call-template></xsl:if><xsl:if test="position()!=last()">,</xsl:if>
		</xsl:for-each>
		);
		newCategory.display(-1);
	</xsl:comment>
	</script>
<!--
<tr>
	<td>
	<table summary="" border="0" cellspacing="0" cellpadding="0">
	<xsl:for-each select="category">
		<tr>
			<td width="20px" valign="top"><xsl:if test="children/category"><xsl:attribute name="rowspan">2</xsl:attribute></xsl:if>
			<input type="checkbox" name="cat_id_list[]"><xsl:attribute name="id">cat_<xsl:value-of select="@identifier"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute><xsl:if test="@identifier = //specified_categorys/@identifier"><xsl:attribute name="checked">true</xsl:attribute></xsl:if></input>
			</td>
			<td width="100%"><xsl:if test="children/category"><xsl:attribute name="style">background-color:#ebebeb;</xsl:attribute></xsl:if><label><xsl:attribute name="for">cat_<xsl:value-of select="@identifier"/></xsl:attribute><xsl:value-of select="label"/></label></td>
		</tr>
		<xsl:if test="children/category">
		<tr>
			<td width="100%"><xsl:call-template name="display_child_choose_categories">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template></td>
		</tr>
		</xsl:if>
	</xsl:for-each>	
	</table></td>
</tr>
-->
	</xsl:otherwise>
</xsl:choose>
</xsl:template>
<xsl:template name="showcategoriesJS">
	<xsl:for-each select="children/category">
	new Array(<xsl:value-of select="@parent"/>,<xsl:value-of select="@identifier"/>,"<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>", <xsl:choose>
				<xsl:when test="@identifier = //specified_categorys/@identifier">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>)<xsl:if test="children/category">, <xsl:call-template name="showcategoriesJS"></xsl:call-template></xsl:if><xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_child_choose_categories">
	<xsl:param name="identifier">-2</xsl:param>
	<table summary="" border="0" cellspacing="0" cellpadding="0">
	<xsl:for-each select="children/category[@parent = $identifier]">
		<tr>
			<td width="20px" valign="top"><xsl:if test="children/category"><xsl:attribute name="rowspan">2</xsl:attribute></xsl:if>
			<input type="checkbox" name="cat_id_list[]"><xsl:attribute name="id">cat_<xsl:value-of select="@parent"/>_<xsl:value-of select="@identifier"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute><xsl:if test="@identifier = //specified_categorys/@identifier"><xsl:attribute name="checked">true</xsl:attribute></xsl:if></input>
			</td>
			<td width="100%"><xsl:attribute name="class">lineit</xsl:attribute><label><xsl:attribute name="for">cat_<xsl:value-of select="@parent"/>_<xsl:value-of select="@identifier"/></xsl:attribute><xsl:value-of select="label"/></label></td>
		</tr>
		<xsl:if test="children/category">
		<tr>
			<td width="100%"><xsl:call-template name="display_child_choose_categories">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template></td>
		</tr>
		</xsl:if>
	</xsl:for-each>	
	</table>
</xsl:template>
<xsl:template name="display_child_choose_categories_option">
	<xsl:param name="identifier">-2</xsl:param>
	<xsl:param name="padding">[[nbsp]]-[[nbsp]]</xsl:param>
	<xsl:for-each select="children/category[@parent = $identifier]">
			<option >
				<xsl:attribute name="id">cat_<xsl:value-of select="@parent"/>_<xsl:value-of select="@identifier"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute>
				<xsl:value-of select="$padding"/><xsl:value-of select="label"/>
			</option>
		<xsl:if test="children/category">
		<xsl:call-template name="display_child_choose_categories_option">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
				<xsl:with-param name="padding">[[nbsp]][[nbsp]]<xsl:value-of select="$padding"/></xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>	
</xsl:template>



</xsl:stylesheet>