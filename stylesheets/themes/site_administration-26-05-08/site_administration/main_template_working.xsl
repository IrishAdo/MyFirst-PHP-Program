<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:50:00 $
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
		<table border="0" cellpadding="0" cellspacing="0" summary="This table holds a form" width="100%">	
    	<tr><td valign="top" align="right">
 	 	<xsl:apply-templates select="//page_options"/>
  	 	<xsl:choose>
  	 	<xsl:when test="//filter"></xsl:when>
  	 	<xsl:otherwise>
  	 	<xsl:for-each select="//input">
			<xsl:if test="@type='submit' and ../@name!='user_login_form'"><a>
				<xsl:attribute name="href"><xsl:choose>
				<xsl:when test="../textarea[@type='RICH-TEXT']">javascript:onSubmitCompose(1,'<xsl:value-of select="@command"/>');</xsl:when>
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
					<xsl:otherwise>?command=<xsl:value-of select="@command"/><xsl:value-of select="@parameters"/></xsl:otherwise>
					
					<!--<xsl:otherwise>javascript:button_action('<xsl:value-of select="@command"/>');</xsl:otherwise>-->
				</xsl:choose>
			</xsl:attribute><xsl:call-template name="display_icon"/></a>[[nbsp]]</xsl:if>
		   	</xsl:for-each>
		</xsl:otherwise>
		</xsl:choose>
		</td></tr>
    </table>
		<table border="0" cellpadding="0" cellspacing="0" width="100%" summary="">
		<xsl:apply-templates select="/xml_document/modules/module"/>
		</table>

  <xsl:if test="/xml_document/debugging">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" summary="">
		  <tr> 
		   	<td valign="top" colspan="3" ><xsl:apply-templates select="/xml_document/debugging"/></td>
		  </tr>
		</table>
  </xsl:if>
</xsl:template>


<xsl:template match="module">
<tr><td class="TableCell">
	<xsl:choose>
		<xsl:when test="@display='my_workspace'"></xsl:when>
		<xsl:when test="@display='table'"><xsl:call-template name="display_table"/></xsl:when>
    	<xsl:when test="@display='stats'"><xsl:call-template name="display_stats"/></xsl:when>
    	<xsl:when test="@display='results'"><xsl:call-template name="display_results"/></xsl:when>
		<xsl:when test="@display='filter'"></xsl:when>
		<xsl:when test="@display='form'"><xsl:apply-templates select="form"/></xsl:when>
    	<xsl:when test="@display='remove_form'"><xsl:apply-templates select="form"/></xsl:when>
    	<xsl:when test="@name='contact'"><xsl:call-template name="display_users"/></xsl:when>
    	<xsl:when test="@name='versions'"><xsl:call-template name="display_module_versions"/></xsl:when>
    	<xsl:when test="@name='splash'">
			<xsl:value-of select="text" disable-output-escaping="yes"/>
			<table border="0" width="100%">
				<tr>
					<td valign="top">
					<xsl:for-each select="module[(position() mod 2)=1]">
						<xsl:call-template name="display_my_workspace">
							<xsl:with-param name="number">1</xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>
					</td>
					<td valign="top">
					<xsl:for-each select="module[(position() mod 2)=0]">
						<xsl:call-template name="display_my_workspace">
							<xsl:with-param name="number">0</xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>
					</td>
				</tr>
			</table>
		</xsl:when>
    	<xsl:when test="@display='list_menu'"><xsl:call-template name="display_menu">
			<xsl:with-param name="folder" select="infolder"/>
		</xsl:call-template></xsl:when>
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
    		<script type="text/javascript" language="JavaScript1.2"><xsl:attribute name="src">./scripts/module_groups.js</xsl:attribute></script>
    	</xsl:when>
	</xsl:choose>
</td></tr>
</xsl:template>


<xsl:template name="layout_menu_manager">
<table width="100%" cellpadding="0" border="0" cellspacing="0">
	<tr class="PAGE_HEADER_LIGHT">
		<td width="240" class="form_cells" valign="top" rowspan="3"><IFRAME id="menu_list_box" height="550" src=""></IFRAME></td>
		<td width="1" rowspan="3" class="PAGE_HEADER_LIGHT" valign="top"><img src="/libertas_images/1x1.gif" border="0" width="1" height="1"/></td>
		<td width="2" rowspan="3" class="PAGE_HEADER" valign="top"><img src="/libertas_images/1x1.gif" border="0" width="2" height="1"/></td>
		<td width="1" rowspan="3" class="PAGE_HEADER_DARK" valign="top"><img src="/libertas_images/1x1.gif" border="0" width="1" height="1"/></td>
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
		<td width="1" rowspan="3" class="PAGE_HEADER_LIGHT" valign="top"><img src="/libertas_images/1x1.gif" border="0" width="1" height="1"/></td>
		<td width="2" rowspan="3" class="PAGE_HEADER" valign="top"><img src="/libertas_images/1x1.gif" border="0" width="2" height="1"/></td>
		<td width="1" rowspan="3" class="PAGE_HEADER_DARK" valign="top"><img src="/libertas_images/1x1.gif" border="0" width="1" height="1"/></td>
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

<xsl:template match="directory"><xsl:if test="position()>1">,</xsl:if>
		Array(<xsl:value-of select="@identifier"/>,	"<xsl:value-of select="@parent"/>",	"<xsl:value-of select="@name"/>",	<xsl:choose><xsl:when test="directory">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>,	<xsl:value-of select="@depth"/>	,<xsl:value-of select="@can_upload"/>	,<xsl:value-of select="@can_spider"/>)<xsl:if test="directory">,<xsl:apply-templates select="directory"/></xsl:if>
</xsl:template>

<xsl:template name="display_results">
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
				<xsl:apply-templates select="data_list"/>
				</table></td>
			</tr>
			<tr> 
			   	<td valign="top" align="center"><xsl:call-template name="function_page_spanning"/></td>
			</tr>
		</table>
		<script src="scripts/module.js"></script>
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
					<xsl:if test="position()=1">
					<tr class="formheader"> 
					   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Module'"/></xsl:call-template></td>
				  		<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Version'"/></xsl:call-template></td>
				  		<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Author'"/></xsl:call-template></td>
				  		<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Creation Date'"/></xsl:call-template></td>
				  		<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Command Starter'"/></xsl:call-template></td>
  					</tr>
					</xsl:if>
					<tr class="TableCell"> 
		   				<td valign="top"><xsl:value-of select="@name"/></td>
		   				<td valign="top"><xsl:value-of select="@version"/></td>
		   				<td valign="top"><xsl:value-of select="@author"/></td>
		   				<td valign="top"><xsl:value-of select="@creation"/></td>
		   				<td valign="top"><xsl:value-of select="@command"/></td>
  					</tr>
				</xsl:for-each>
				</table></td>
			</tr>
		</table>
</xsl:template>




</xsl:stylesheet>