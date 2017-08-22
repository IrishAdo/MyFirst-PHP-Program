<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.8 $
- Modified $Date: 2005/01/11 16:25:17 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
	
<xsl:include href="../../themes/site_administration/include.xsl" />

<xsl:template match="/">
<xsl:call-template name="display_layout_structure"/>
</xsl:template>

<xsl:template name="generate_content">
		<xsl:if test="boolean(//page_options/alert)">
		<script>
		<xsl:comment>
			alert('<xsl:value-of select="//page_options/alert"/>');
		</xsl:comment>
		</script>
		</xsl:if>
		<xsl:if test="boolean(//page_options/text)">
			<h2 style='color:red'><xsl:value-of select="//page_options/text"/></h2>
		</xsl:if>
    	<div class='row'>
		<div class='cell' style='float:right;display:inline;text-align:right'>
	 	 <xsl:apply-templates select="//page_options"/>
  	 	<xsl:choose>
  	 	<xsl:when test="//filter"></xsl:when>
  	 	<xsl:otherwise>
  	 	<xsl:for-each select="//input">
			<xsl:if test="@type='submit' and ../@name!='USERS_SHOW_LOGIN'"><a>
				<xsl:attribute name="href"><xsl:choose>
					<xsl:when test="@command='BACK'">javascript:history.back();</xsl:when>
					<xsl:when test="@command='LAYOUT_REMOVE_MENU'">javascript:button_action('LAYOUT_REMOVE_MENU');</xsl:when>
					<xsl:when test="@command='LAYOUT_REMOVE_DIRECTORY'">javascript:button_action('LAYOUT_REMOVE_DIRECTORY');</xsl:when>
					<xsl:when test="@command='LAYOUT_SAVE_DIRECTORY'">javascript:button_action('LAYOUT_SAVE_DIRECTORY');</xsl:when>
					<xsl:when test="@command='VEHICLE_LOOKUP_REMOVE'">javascript:lookup_remove(document.<xsl:value-of select="../@name"/>);</xsl:when>
					<xsl:when test="//input/@name='command' and //input/@value='WEBOBJECTS_LAYOUT_SAVE'">javascript:webobjects_submit();</xsl:when>
					<xsl:when test="//textarea[@type='RICH-TEXT']">javascript:ok = onSubmitCompose(2,'<xsl:value-of select="@command"/>');</xsl:when>
					<xsl:otherwise>
						<xsl:choose>
							<xsl:when test="..//@required">javascript:check_required_fields();</xsl:when>
							<xsl:otherwise>javascript:document.<xsl:value-of select="../@name"/>.submit();</xsl:otherwise>
						</xsl:choose>
					</xsl:otherwise>
				</xsl:choose></xsl:attribute>
			<xsl:call-template name="display_icon"/></a>[[nbsp]]</xsl:if>
			<xsl:if test="@type='button'">
			<a><xsl:attribute name="href"><xsl:choose>
					<xsl:when test="@command='BACK'">javascript:history.back();</xsl:when>
					<xsl:when test="@command='LAYOUT_REMOVE_MENU'">javascript:button_action('LAYOUT_REMOVE_MENU');</xsl:when>
					<xsl:when test="@command='LAYOUT_REMOVE_DIRECTORY'">javascript:button_action('LAYOUT_REMOVE_DIRECTORY');</xsl:when>
					<xsl:when test="@command='LAYOUT_SAVE_DIRECTORY'">javascript:button_action('LAYOUT_SAVE_DIRECTORY');</xsl:when>
					<xsl:when test="@command='VEHICLE_LOOKUP_REMOVE'">javascript:lookup_remove(document.<xsl:value-of select="../@name"/>);</xsl:when>
					<xsl:otherwise>admin/index.php?command=<xsl:value-of select="@command"/><xsl:value-of select="@parameters"/></xsl:otherwise>
				</xsl:choose>
			</xsl:attribute><xsl:call-template name="display_icon"/></a>[[nbsp]]</xsl:if>
		   	</xsl:for-each>
		</xsl:otherwise>
		</xsl:choose>
		</div>
		<div class='cell' style='width:auto;display:inline;'><h1><xsl:choose>
			<xsl:when test="//module[@name='users' and @display='form']/form[@name='USERS_SHOW_LOGIN']"></xsl:when>
			<xsl:when test="//page_options/header"><xsl:value-of select="//page_options/header" disable-output-escaping="yes"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="//module/form/@label" disable-output-escaping="yes"/></xsl:otherwise>
		</xsl:choose></h1></div>
		</div>
		<xsl:choose>
			<xsl:when test="/xml_document/modules/module[@name='splash']">
				<xsl:apply-templates select="/xml_document/modules/module"/>
			</xsl:when>
			<xsl:otherwise>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" summary="">
		<xsl:apply-templates select="/xml_document/modules/module"/>
		</table></xsl:otherwise>
		</xsl:choose>
</xsl:template>


<xsl:template match="module">
<!--
<tr><td class="TableCell">
-->
<div>
	<xsl:choose>
		<xsl:when test="@display='my_workspace'"></xsl:when>
		<xsl:when test="@display='table'"><xsl:call-template name="display_table"/></xsl:when>
    	<xsl:when test="@display='stats'"><xsl:call-template name="display_stats"/></xsl:when>
    	<xsl:when test="@display='results'"><xsl:call-template name="display_results"/></xsl:when>
		<xsl:when test="@display='filter'"></xsl:when>
		<xsl:when test="@display='form'"><xsl:apply-templates select="form"/></xsl:when>
		<xsl:when test="@display='form_builder'"><xsl:apply-templates select="form_builder"/></xsl:when>
    	<xsl:when test="@display='remove_form'"><xsl:apply-templates select="form"/></xsl:when>
    	<xsl:when test="@name='contact'"><xsl:call-template name="display_users"/></xsl:when>
    	<xsl:when test="@name='versions'"><xsl:call-template name="display_module_versions"/></xsl:when>
    	<xsl:when test="@display='text'"><xsl:value-of select="text" disable-output-escaping="yes"/></xsl:when>
    	<xsl:when test="@display='entries'">
			<xsl:for-each select="entry"><div>
				<h1><xsl:value-of select="label"/></h1>
				<p><xsl:value-of select="text"/></p>
			</div></xsl:for-each>
		</xsl:when>
    	<xsl:when test="@name='splash'"><xsl:value-of select="text" disable-output-escaping="yes"/>
			<xsl:if test="boolean(module)">
				<div class="workspace"><xsl:for-each select="module[(position() mod 2)=1]">
					<xsl:call-template name="display_my_workspace">
						<xsl:with-param name="number">1</xsl:with-param>
					</xsl:call-template>
				</xsl:for-each></div>
				<div class="workspace"><xsl:for-each select="module[(position() mod 2)=0]">
					<xsl:call-template name="display_my_workspace">
						<xsl:with-param name="number">0</xsl:with-param>
					</xsl:call-template>
				</xsl:for-each></div><div id="messagebox">
				<div id="system_message" class="sys_message"><h1>System messages</h1><p>Welcome to  Libertas Content Manager. We are currently making some improvements and adding features, and welcome any comments or suggestions that you may have. </p></div><div id="suggest"><a href="http://www.libertas-solutions.com/support/comments-and-suggestions/index.php"><img src="/libertas_images/themes/letusknow.gif" border="0" width="200" height="148"/></a></div></div>
			</xsl:if></xsl:when>
    	<xsl:when test="@display='list_menu'"><xsl:call-template name="display_menu"><xsl:with-param name="folder" select="infolder"/></xsl:call-template></xsl:when>
    	<xsl:when test="@name='layout_directory_manager'"><xsl:call-template name="layout_directory_manager"/></xsl:when>
	</xsl:choose>
	<xsl:choose>
    	<xsl:when test="@name='page'">
	    	<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_files.js"></script>
	    	<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_layout.js"></script>
    		<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_pages.js"></script>
			<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_contact.js"></script>
    	</xsl:when>
		<xsl:when test="@name='groups'">
    		<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_groups.js"></script>
    	</xsl:when>
    	<xsl:when test="@name='vehicle'">
    		<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_vehicle.js"></script>
    	</xsl:when>
	</xsl:choose>
</div>
<!--
</td></tr>
-->
</xsl:template>


<xsl:template name="layout_menu_manager">
<table width="100%" cellpadding="0" border="0" cellspacing="0">
	<tr class="PAGE_HEADER_LIGHT">
		<td width="240" class="form_cells" valign="top" rowspan="3"><IFRAME id="menu_list_box" height="550" src=""></IFRAME></td>
		<td width="1" rowspan="3" class="PAGE_HEADER_LIGHT" valign="top"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1"/></td>
		<td width="2" rowspan="3" class="PAGE_HEADER" valign="top"><img src="/libertas_images/themes/1x1.gif" border="0" width="2" height="1"/></td>
		<td width="1" rowspan="3" class="PAGE_HEADER_DARK" valign="top"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1"/></td>
		<td width="100%" rowspan="3" class="LAYOUT_FORM" valign="top"><xsl:apply-templates select="form"/></td>
	</tr>
</table>
<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_layout.js"/>		
<script>
	<xsl:comment>
	var menu_list = Array(<xsl:apply-templates select="menu_data/menu"/>);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- initalise the menu navigation
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	initialise_menu_nav();
	//</xsl:comment>
</script>
</xsl:template>

<xsl:template name="layout_directory_manager">
<table width="100%" cellpadding="0" border="0" cellspacing="0">
	<tr class="PAGE_HEADER_LIGHT">
		<td width="240" class="form_cells" valign="top" rowspan="3"><IFRAME id="directory_list_box" height="420" src=""></IFRAME></td>
		<td width="1" rowspan="3" class="PAGE_HEADER_LIGHT" valign="top"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1"/></td>
		<td width="2" rowspan="3" class="PAGE_HEADER" valign="top"><img src="/libertas_images/themes/1x1.gif" border="0" width="2" height="1"/></td>
		<td width="1" rowspan="3" class="PAGE_HEADER_DARK" valign="top"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1"/></td>
		<td width="100%" rowspan="3" class="PAGE_HEADER" valign="top"><xsl:apply-templates select="form"/></td>
	</tr>
</table>
<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_layout.js"></script>
<script>
	<xsl:comment>
	var directory_list = Array(<xsl:apply-templates select="menu_data/directory"/>);
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- initalise the directory navigation
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	initialise_directory_nav();
	//</xsl:comment>
</script>
</xsl:template>

<xsl:template match="menu"><xsl:if test="position()>1">,</xsl:if>
		Array(<xsl:value-of select="@identifier"/>,
			"<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template>",
			"<xsl:value-of select="@url"/>",	
			<xsl:value-of select="@depth"/>,	
			<xsl:value-of select="@children"/>,	
			<xsl:value-of select="@parent"/>,	
			Array(<xsl:for-each select="display_options/display"><xsl:if test="position()>1">, </xsl:if>'<xsl:value-of select="." />'</xsl:for-each>),	
			<xsl:value-of select="@siblings"/>,	
			Array(<xsl:for-each select="groups/option"><xsl:if test="position()>1">, </xsl:if>'<xsl:value-of select="@value" />'</xsl:for-each>),	
			<xsl:value-of select="@order"/>,
			<xsl:choose><xsl:when test="@stylesheet"><xsl:value-of select="@stylesheet"/></xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>,
			<xsl:choose><xsl:when test="@theme"><xsl:value-of select="@theme"/></xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>
			)<xsl:if test="children/menu">,
			<xsl:apply-templates select="children/menu"/></xsl:if>
</xsl:template>

<xsl:template match="directory"><xsl:if test="position()>1">,</xsl:if>Array(<xsl:value-of select="@identifier"/>,	"<xsl:value-of select="@parent"/>",	"<xsl:value-of select="@name"/>",	<xsl:choose><xsl:when test="directory">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>,	<xsl:value-of select="@depth"/>	,<xsl:value-of select="@can_upload"/>	,<xsl:value-of select="@can_spider"/>)<xsl:if test="directory">,<xsl:apply-templates select="directory"/></xsl:if></xsl:template>

<xsl:template name="display_results">
	<xsl:if test="boolean(data_list) or boolean(table_list)">
		<p>
		<xsl:if test="boolean(data_list/@number_of_records='0') or boolean(table_list/@number_of_records='0')">
			<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'SORRY_NO_RESULTS'"/></xsl:call-template>
		</xsl:if>
		<xsl:if test="boolean(data_list/@number_of_records='1') or boolean(table_list/@number_of_records='1')">
			<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ONE_RESULT'"/></xsl:call-template>
		</xsl:if>
		<xsl:if test="boolean(data_list/@number_of_records>'1')">
			<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAYING_RESULTS'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="data_list/@start"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAY_TO'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="data_list/@finish"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAY_OF'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="data_list/@number_of_records"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'RESULT'"/></xsl:call-template>
		</xsl:if>
		<xsl:if test="boolean(table_list/@number_of_records>'1')">
			<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAYING_RESULTS'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="table_list/@start"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAY_TO'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="table_list/@finish"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'DISPLAY_OF'"/></xsl:call-template>[[nbsp]]<xsl:value-of select="table_list/@number_of_records"/>[[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'RESULT'"/></xsl:call-template>
		</xsl:if>
		<xsl:if test="data_list/searchfilter!=''">
			<xsl:value-of select="data_list/searchfilter"/>
		</xsl:if>
		<xsl:if test="table_list/searchfilter!=''">
			<xsl:value-of select="table_list/searchfilter"/>
		</xsl:if>
		</p>
		<xsl:if test="./data_list/@number_of_records>='1' or ./table_list/@number_of_records>='1'">
		<table border="0" width="100%" cellpadding="0" cellspacing="0" summary="This table holds the menu information from the modules">
			<!-- Top Paging Portion By Imran -->
			<tr> 
			   	<td valign="top" align="center"><xsl:call-template name="function_page_spanning"/></td>
			</tr>
			<!-- Top Paging Portion By Imran -->
			<tr> 
			   	<td valign="top" class="formbackground"><table border="0"  width="100%" summary="This table holds the user information">
				<xsl:if test="table_list">
				<xsl:attribute name="class">sortable</xsl:attribute>
				<xsl:attribute name="cellpadding">3</xsl:attribute>
				<xsl:attribute name="cellspacing">0</xsl:attribute>
				</xsl:if>
				<xsl:if test="data_list">
				<xsl:attribute name="cellpadding">3</xsl:attribute>
				<xsl:attribute name="cellspacing">1</xsl:attribute>
				</xsl:if>
				<xsl:apply-templates select="data_list"/>
				<xsl:apply-templates select="table_list"/>
				</table></td>
			</tr>
			<!-- Bottom Paging Portion By Imran -->
			<tr> 
			   	<td valign="top" align="center"><xsl:call-template name="function_page_spanning"/></td>
			</tr>
			<!-- Bottom Paging Portion By Imran -->
		</table>
		<script src="/libertas_images/javascripts/module.js"></script>
		</xsl:if>
	</xsl:if>
	
</xsl:template>

<xsl:template name="display_counter">
<!--
	display a list of options for the page numbers
-->
</xsl:template>


<xsl:template name="display_module_versions">
		<table border="0" width="100%" cellpadding="0" cellspacing="0" summary="This table holds the menu information from the modules">
			<tr> 
			   	<td valign="top" class="formbackground"><table border="0" cellpadding="3" cellspacing="1" width="100%" summary="This table holds the user information">
			   	<xsl:for-each select="entry">
					<xsl:sort select="@name"/>
					<xsl:if test="position()=1">
					<tr class="formheader"> 
					   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Module'"/></xsl:call-template></td>
				  		<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Version'"/></xsl:call-template></td>
				  		<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Author'"/></xsl:call-template></td>
				  		<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Creation Date'"/></xsl:call-template></td>
				  		<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Last Uploaded'"/></xsl:call-template></td>
  					</tr>
					</xsl:if>
					<tr class="TableCell"> 
		   				<td valign="top"><xsl:value-of select="@name"/></td>
		   				<td valign="top"><xsl:value-of select="@version"/></td>
		   				<td valign="top"><xsl:value-of select="@author"/></td>
		   				<td valign="top"><xsl:value-of select="@creation"/></td>
		   				<td valign="top"><xsl:value-of select="@uploaded"/></td>
  					</tr>
				</xsl:for-each>
				</table></td>
			</tr>
		</table>
</xsl:template>



<xsl:template name='alert'></xsl:template>
</xsl:stylesheet>