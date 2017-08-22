<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:49:59 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:include href="./stylesheets/themes/site_administration/common.xsl"/>
<xsl:include href="./stylesheets/themes/site_administration/localisation.xsl"/>
<xsl:include href="./stylesheets/themes/site_administration/stats.xsl"/>
<xsl:include href="./stylesheets/themes/site_administration/debug.xsl"/>
<xsl:include href="./stylesheets/themes/site_administration/functions.xsl"/>
<xsl:variable name="counter" >1</xsl:variable>
<xsl:variable name="number_of_pages">0</xsl:variable>
<xsl:variable name="max_depth">0</xsl:variable>

<xsl:key name="module_group" match="mod" use="@grouping"/>

<xsl:template match="/">
<html>
<head>
<title>ado Libertas-Solutions :: Administration :: <xsl:value-of select="/XMLDocument/PageTitle"/></title>

<link rel="stylesheet" href="/stylesheets/site_administration.css" />
<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/web_menu.js"></script>
</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" class="TableCell">
<table border="0" cellpadding="0" cellspacing="0" width="100%" summary="This table is used to layout the information on this page">
  <xsl:apply-templates select="/xml_document/modules"/>
  <xsl:if test="/xml_document/debugging">
  <tr> 
   	<td valign="top" colspan="3" ><xsl:apply-templates select="/xml_document/debugging"/></td>
  </tr>
  </xsl:if>
</table>
</body>
</html>
</xsl:template>

<xsl:template match="modules">
   		<tr><td><img border="0" src="/libertas_images/libertas_logo_top.gif"/></td><td width="100%" bgcolor="#ffffff"></td></tr>
   		<tr><td><img border="0" src="/libertas_images/libertas_logo_middle.gif"/></td><td width="100%" bgcolor="#F5CD20" background="images/libertas_middle_background.gif"></td></tr>
   		<tr><td><img border="0" src="/libertas_images/libertas_logo_bottom.gif"/></td><td height="25" align="left" background="images/background_top_row_2.gif" colspan="2" valign="top" ><xsl:for-each select="./module">
		<xsl:if test="@name='admin_menu'">
			<xsl:call-template name="display_admin_menu"/>
		</xsl:if>
	</xsl:for-each>
	<xsl:if test="module[@name='vehicle']">
   			<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_vehicle.js"></script>
   	</xsl:if>
</td></tr>
	<tr><td colspan="3">
		<table border="0" cellpadding="0" cellspacing="0" summary="This table holds a form" width="100%">	
    	<tr class="PAGE_HEADER_LIGHT"><td><img width="1" height="1" border="0" src="/libertas_images/1x1.gif"/></td></tr>
  	 	<tr><td valign="top" class="PAGE_HEADER" align="right"><img src="/libertas_images/1x1.gif" width="1" height="45"/>
 	 	<xsl:apply-templates select="//page_options"/>
  	 	<xsl:choose>
  	 	<xsl:when test="//filter"></xsl:when>
  	 	<xsl:otherwise>
  	 	<xsl:for-each select="//input">
			<xsl:if test="@type='submit'"><a>
			<xsl:attribute name="href"><xsl:choose>
			<xsl:when test="../textarea[@type='RICH-TEXT']">javascript:ok = onSubmitCompose(1);</xsl:when>
			<xsl:otherwise>javascript:document.<xsl:value-of select="../@name"/>.submit();</xsl:otherwise>
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
					<!--<xsl:otherwise>javascript:button_action('<xsl:value-of select="@command"/>');</xsl:otherwise>-->
				</xsl:choose>
			</xsl:attribute><xsl:call-template name="display_icon"/></a>[[nbsp]]</xsl:if>
		   	</xsl:for-each>
		</xsl:otherwise>
		</xsl:choose>
		</td></tr>
		<tr class="PAGE_HEADER_DARK"><td><img width="1" height="1" border="0" src="/libertas_images/1x1.gif"/></td></tr>
    	<tr class="PAGE_HEADER_LIGHT"><td><img width="1" height="1" border="0" src="/libertas_images/1x1.gif"/></td></tr>
    </table>
	<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<xsl:for-each select="./module">
		<xsl:apply-templates select="."/>
	</xsl:for-each></table>
			<table border="1" cellpadding="0" cellspacing="0" summary="This table holds a form" width="100%">	
    	<tr class="PAGE_HEADER_LIGHT"><td><img width="1" height="1" border="0" src="/libertas_images/1x1.gif"/></td></tr>
  	 	<tr><td valign="top" class="PAGE_HEADER" align="right"><img src="/libertas_images/1x1.gif" width="1" height="45"/>
 	 	<xsl:apply-templates select="//page_options"/>
  	 	<xsl:choose>
  	 	<xsl:when test="//filter"></xsl:when>
  	 	<xsl:otherwise>
  	 	<xsl:for-each select="//input">
			<xsl:if test="@type='submit'"><a>
			<xsl:attribute name="href"><xsl:choose>
			<xsl:when test="../textarea[@type='RICH-TEXT']">javascript:ok = onSubmitCompose(1);</xsl:when>
			<xsl:otherwise>javascript:document.<xsl:value-of select="../@name"/>.submit();</xsl:otherwise>
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
					<!--<xsl:otherwise>javascript:button_action('<xsl:value-of select="@command"/>');</xsl:otherwise>-->
				</xsl:choose>
			</xsl:attribute><xsl:call-template name="display_icon"/></a>[[nbsp]]</xsl:if>
		   	</xsl:for-each>
		</xsl:otherwise>
		</xsl:choose>
		</td></tr>
		<tr class="PAGE_HEADER_DARK"><td><img width="1" height="1" border="0" src="/libertas_images/1x1.gif"/></td></tr>
    	<tr class="PAGE_HEADER_LIGHT"><td><img width="1" height="1" border="0" src="/libertas_images/1x1.gif"/></td></tr>
    </table>

	</td></tr>
</xsl:template>

<xsl:template match="module">
<tr><td class="TableCell">
	<xsl:choose>
    	<xsl:when test="@display='stats'"><xsl:call-template name="display_stats"/></xsl:when>
    	<xsl:when test="@display='results'"><xsl:call-template name="display_results"/></xsl:when>
		<xsl:when test="@display='filter'"><xsl:apply-templates select="filter/form"/></xsl:when>
		<xsl:when test="@display='form'"><xsl:apply-templates select="form"/></xsl:when>
    	<xsl:when test="@display='remove_form'"><xsl:apply-templates select="form"/></xsl:when>
    	<xsl:when test="@name='contact'"><xsl:call-template name="display_users"/></xsl:when>
    	<xsl:when test="@name='versions'"><xsl:call-template name="display_module_versions"/></xsl:when>
    	<xsl:when test="@name='splash'"><xsl:apply-templates select="text"/></xsl:when>
    	<xsl:when test="@name='layout_menu_manager'"><xsl:call-template name="layout_menu_manager"/></xsl:when>
    	<xsl:when test="@name='layout_directory_manager'"><xsl:call-template name="layout_directory_manager"/></xsl:when>
	</xsl:choose>
	[<xsl:value-of select="@name"/>]
	<xsl:choose>
    	<xsl:when test="@name='page'">
	    	<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_files.js"></script>
	    	<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_layout.js"></script>
    		<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/module_pages.js"></script>
    	</xsl:when>
    	<xsl:when test="@name='groups'">
    		<script type="text/javascript" language="JavaScript1.2"><xsl:attribute name="src">/libertas_images/javascripts/module_groups.js</xsl:attribute></script>
    	</xsl:when>
    	<xsl:when test="@name='vehicle'">
    		<script type="text/javascript" language="JavaScript1.2"><xsl:attribute name="src">/libertas_images/javascripts/module_vehicle.js</xsl:attribute></script>
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
		Array(<xsl:value-of select="@identifier"/>,	"<xsl:value-of select="@parent"/>",	"<xsl:value-of select="@name"/>",	<xsl:choose><xsl:when test="directory">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>,	<xsl:value-of select="@depth"/>,<xsl:value-of select="@can_upload"/>)<xsl:if test="directory">,<xsl:apply-templates select="directory"/></xsl:if>
</xsl:template>


<xsl:template name="display_admin_menu">
<script type="text/javascript" language="JavaScript1.2">
beginSTM("Libertas_Solutions","static","0","0","none","false","true","310","0","0","400","","images/1x1.gif");
	beginSTMB("auto","0","0","Horizontaly","images/arrow_d.gif","10","10","0","4","transparent","90","tiled","#FFFFFF","0","solid","0","Normal","50","20","10","7","7","0","0","0","#000000","false","#000000","#000000","#000000","complex");
		appendSTMI("false","Home","left","middle","","","1","1","0","normal","#FDE472","#F5CD20","","1","-1","-1","","","0","0","0","","<xsl:value-of select="/xml_document/@script"/>?command=ENGINE_SPLASH","_self","Arial","9pt","#000000","bold","normal","none","Arial","9pt","#000000","bold","normal","none","1","solid","#000000","#FFFFFF","#FFFFFF","#000000","#FFFFFF","#000000","#000000","#FFFFFF","Home","","","tiled","tiled");
			beginSTMB("auto","0","0","vertically","","","","0","5","transparent","10","tiled","#FFFFFF","0","solid","10","Normal","50","8","8","7","7","0","0","5","#000000","false","#000000","#000000","#000000","complex");
				appendSTMI("false","Return to Site","left","middle","","","-1","-1","0","normal","#FDE472","#F5CD20","","1","-1","-1","images/1x1.gif","images/1x1.gif","0","0","0","","/index.php","_self","Arial","9pt","#000000","bold","normal","none","Arial","9pt","#000000","bold","normal","none","1","solid","#000000","#FFFFFF","#FFFFFF","#000000","#FFFFFF","#000000","#000000","#FFFFFF","","","","tiled","tiled");
				appendSTMI("false","Logout","left","middle","","","-1","-1","0","normal","#FDE472","#F5CD20","","1","-1","-1","images/1x1.gif","images/1x1.gif","0","0","0","","?command=ENGINE_LOGOUT","_self","Arial","9pt","#000000","bold","normal","none","Arial","9pt","#000000","bold","normal","none","1","solid","#000000","#FFFFFF","#FFFFFF","#000000","#FFFFFF","#000000","#000000","#FFFFFF","","","","tiled","tiled");
			endSTMB();
		appendSTMI("false","Management Modules","left","middle","","","0","0","0","normal","#FDE472","#F5CD20","","1","-1","-1","images/1x1.gif","images/1x1.gif","0","0","0","","","_self","Arial","9pt","#000000","bold","normal","none","Arial","9pt","#000000","bold","normal","none","1","solid","#000000","#FFFFFF","#FFFFFF","#000000","#FFFFFF","#000000","#000000","#FFFFFF","","","","tiled","tiled");
			beginSTMB("auto","0","0","vertically","images/arrow_r.gif","10","10","0","5","transparent","10","tiled","#FFFFFF","0","solid","10","Normal","50","8","8","7","7","0","0","5","#000000","false","#000000","#000000","#000000","complex");
			<xsl:for-each select="grouping">
				<xsl:sort select="@name" order="ascending"/>
				<xsl:sort select="mod/@label" order="ascending"/>
				appendSTMI("false","<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template>","left","middle","","","-1","-1","0","normal","#FDE472","#F5CD20","","1","-1","-1","images/1x1.gif","images/1x1.gif","0","0","0","","#","_self","Arial","9pt","#000000","bold","normal","none","Arial","9pt","#000000","bold","normal","none","1","solid","#000000","#FFFFFF","#FFFFFF","#000000","#FFFFFF","#000000","#000000","#FFFFFF","","","","tiled","tiled");
					beginSTMB("auto","-10","0","vertically","","0","0","0","5","transparent","90","tiled","#000000","0","solid","10","Normal","50","8","8","7","7","0","0","5","#000000","false","#000000","#000000","#000000","complex");
				<xsl:for-each select="mod">
					<xsl:sort select="options/option" order="ascending"/>
						<xsl:for-each select="./options/option[@value!=../../@ignore]">
							<xsl:variable name="me"><xsl:call-template name="get_translation">
				<xsl:with-param name="check" select="."/>
			</xsl:call-template></xsl:variable>
							<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
						appendSTMI("false","<xsl:value-of select="$me"/>","left","middle","","","-1","-1","0","normal","#FDE472","#F5CD20","","1","-1","-1","images/1x1.gif","images/1x1.gif","0","0","0","","<xsl:value-of select="/xml_document/@script"/>?command=<xsl:value-of select="$value"/>","_self","Arial","9pt","#000000","bold","normal","none","Arial","9pt","#000000","bold","normal","none","1","solid","#000000","#FFFFFF","#FFFFFF","#000000","#FFFFFF","#000000","#000000","#FFFFFF","","","","tiled","tiled");
					</xsl:for-each>
				</xsl:for-each>
					endSTMB();

			</xsl:for-each>
			endSTMB();
		appendSTMI("false","Help"   ,"left","middle","","","-1","-1","0","normal","#FDE472","#F5CD20","","1","-1","-1","images/1x1.gif","images/1x1.gif","0","0","0","","#","_self","Arial","9pt","#000000","bold","normal","none","Arial","9pt","#000000","bold","normal","none","1","solid","#000000","#FFFFFF","#FFFFFF","#000000","#FFFFFF","#000000","#000000","#FFFFFF","","","","tiled","tiled");
			beginSTMB("auto","0","0","vertically","","","","0","5","transparent","10","tiled","#000000","0","solid","10","Normal","50","8","8","7","7","0","0","5","#000000","false","#000000","#000000","#000000","complex");
				appendSTMI("false","About Us","left","middle","","","-1","-1","0","normal","#FDE472","#F5CD20","","1","-1","-1","images/1x1.gif","images/1x1.gif","0","0","0","","?command=ENGINE_ABOUTUS","_self","Arial","9pt","#000000","bold","normal","none","Arial","9pt","#000000","bold","normal","none","1","solid","#000000","#FFFFFF","#FFFFFF","#000000","#FFFFFF","#000000","#000000","#FFFFFF","","","","tiled","tiled");
				appendSTMI("false","Module Versions","left","middle","","","-1","-1","0","normal","#FDE472","#F5CD20","","1","-1","-1","images/1x1.gif","images/1x1.gif","0","0","0","","?command=ENGINE_VERSIONS","_self","Arial","9pt","#000000","bold","normal","none","Arial","9pt","#000000","bold","normal","none","1","solid","#000000","#FFFFFF","#FFFFFF","#000000","#FFFFFF","#000000","#000000","#FFFFFF","","","","tiled","tiled");
				appendSTMI("false","PHP Info","left","middle","","","-1","-1","0","normal","#FDE472","#F5CD20","","1","-1","-1","images/1x1.gif","images/1x1.gif","0","0","0","","phpinfo.php","_self","Arial","9pt","#000000","bold","normal","none","Arial","9pt","#000000","bold","normal","none","1","solid","#000000","#FFFFFF","#FFFFFF","#000000","#FFFFFF","#000000","#000000","#FFFFFF","","","","tiled","tiled");
			endSTMB();
	endSTMB();
endSTM();</script>
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


<xsl:template match="data_list">
	<xsl:for-each select="entry">
		<xsl:if test="position()=1">
			<tr class="formheader"> 
			   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_IDENTY'"/></xsl:call-template></td>
		   		<xsl:for-each select="attribute">
					<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template></td>
				</xsl:for-each>
		  		<xsl:if test="../entry_options/button">
			   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'ENTRY_OPTIONS'"/></xsl:call-template></td>
		   		</xsl:if>
  			</tr>
			</xsl:if>
		<tr class="TableCell"> 
		   	<td valign="top"><xsl:value-of select="@identifier"/></td>
		   	<xsl:for-each select="attribute">
			   	<td valign="top"><xsl:choose>
					<xsl:when test="@name='ENTRY_LOCKED'">
						<xsl:choose>
							<xsl:when test="@value='0'"><xsl:call-template name="get_translation">
								<xsl:with-param name="check" select="'ENTRY_UNLOCKED'"/>
							</xsl:call-template>[[nbsp]]</xsl:when>
							<xsl:otherwise><xsl:call-template name="get_translation">
								<xsl:with-param name="check" select="'ENTRY_LOCKED'"/>
							</xsl:call-template>[[nbsp]]</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:when test="@name='Message'"><xsl:value-of select="@value"/></xsl:when>
					<xsl:otherwise><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:otherwise>
				</xsl:choose></td>
			</xsl:for-each>
		   	<xsl:variable name="page_status">
			   	<xsl:for-each select="attribute">
					<xsl:if test="@name='ENTRY_STATUS'">
						<xsl:value-of select="@value"/>
					</xsl:if>
				</xsl:for-each>
			</xsl:variable>
		   	<xsl:variable name="locked">
			   	<xsl:for-each select="attribute">
					<xsl:if test="@name='ENTRY_LOCKED'">
						<xsl:value-of select="@value"/>
					</xsl:if>
				</xsl:for-each>
			</xsl:variable>
			<xsl:if test="../entry_options/button">
		   	<td valign="top" width="10"><table cellpadding="0" cellspacing="0" border="0"><tr>
		   	<xsl:variable name="identifier"><xsl:value-of select="@identifier"/></xsl:variable>
		   	<xsl:for-each select="../entry_options/button">
		   	<td width="40">
			   	<xsl:choose>
				   	<xsl:when test="../../../@name='page'">
				   		<xsl:choose>
					   		<xsl:when test="@iconify='PREVIEW'">
			   				  	<a><xsl:attribute name="href">javascript:preview_from_list(<xsl:value-of select="$identifier"/>,'<xsl:value-of select="../../../@name"/>');</xsl:attribute><xsl:call-template name="display_icon"/></a>
					   		</xsl:when>
			   				<xsl:when test="@iconify='FILES'">
		   			  			<a><xsl:attribute name="href">javascript:file(<xsl:value-of select="$identifier"/>,'<xsl:value-of select="../../../@name"/>');</xsl:attribute><xsl:call-template name="display_icon"/></a>
					   		</xsl:when>
	   						<xsl:when test="@iconify='COMMENTS'">
   			  					<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
						   	</xsl:when>
					   		<xsl:when test="@iconify='EDIT'">
				   				<xsl:choose>
									<xsl:when test="$locked='0'">
								   		  	<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
								   	</xsl:when>
									<xsl:when test="$locked!='0'">
										<xsl:if test="$locked=//xml_document/modules/session/@user_identifier">
							   			  	<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
										</xsl:if>
								   	</xsl:when>
					   				<xsl:otherwise>
										<img border="0" height="45" width="40" src="/libertas_images/1x1.gif"/>
									</xsl:otherwise>
							 	</xsl:choose>
							</xsl:when>
			   				<xsl:when test="@iconify='REMOVE'">
				   				<xsl:choose>
									<xsl:when test="$locked='0'">
			   				  			<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
								   	</xsl:when>
									<xsl:when test="$locked!='0'">
										<xsl:if test="$locked=//xml_document/modules/session/@user_identifier">
							   			  	<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
										</xsl:if>
								   	</xsl:when>
					   				<xsl:otherwise>
										<img border="0" height="45" width="40" src="/libertas_images/1x1.gif"/>
									</xsl:otherwise>
							 	</xsl:choose>
						   	</xsl:when>
					   		<xsl:when test="@iconify='NEXT_STAGE'">
				   				<xsl:choose>
									<xsl:when test="$locked='0'">
								   		  	<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
								   	</xsl:when>
									<xsl:when test="$locked!='0'">
										<xsl:if test="$locked=//xml_document/modules/session/@user_identifier">
							   			  	<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
										</xsl:if>
								   	</xsl:when>
					   				<xsl:otherwise>
										<img border="0" height="45" width="40" src="/libertas_images/1x1.gif"/>
									</xsl:otherwise>
							 	</xsl:choose>
							</xsl:when>
			   		   		<xsl:when test="@iconify='REJECT'">
		   					  	<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
					   		</xsl:when>
					   		<xsl:when test="@iconify='APPROVE'">
				   			  	<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
		   					</xsl:when>
			   				<xsl:when test="@iconify='REWORK'">
								<xsl:choose><xsl:when test="$page_status='LOCALE_STATUS_TYPE_3'">
								<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
								</xsl:when><xsl:otherwise><img border="0" height="45" width="40" src="/libertas_images/1x1.gif"/></xsl:otherwise></xsl:choose>
		   			  		</xsl:when>
					   		<xsl:when test="@iconify='PUBLISH'">
								<xsl:choose><xsl:when test="$page_status='LOCALE_STATUS_TYPE_3'">
								<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
								</xsl:when><xsl:otherwise><img border="0" height="45" width="40" src="/libertas_images/1x1.gif"/></xsl:otherwise></xsl:choose>
					   		  						   		
					   		</xsl:when>
					   		<xsl:when test="@iconify='ARCHIVE'">
				   			  	<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
			   			  	</xsl:when>	
					   		<xsl:when test="@iconify='UNPUBLISH'">
								<xsl:choose><xsl:when test="$page_status='LOCALE_STATUS_TYPE_4'">
								<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
								</xsl:when><xsl:otherwise><img border="0" height="45" width="40" src="/libertas_images/1x1.gif"/></xsl:otherwise></xsl:choose>
				   			  	
						   	</xsl:when>
					   		<xsl:when test="@iconify='UNARCHIVE'">
		   					  	<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
				   			</xsl:when>
					   		<xsl:when test="@iconify='LIST_VERSIONS'"><a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a></xsl:when>
					   	</xsl:choose>
			   		</xsl:when>
					<xsl:otherwise>
					  	<a><xsl:attribute name="href">?command=<xsl:value-of select="@command"/>&amp;identifier=<xsl:value-of select="$identifier"/></xsl:attribute><xsl:call-template name="display_icon"/></a>
					</xsl:otherwise>
			   	</xsl:choose>
			   	</td>
		   	</xsl:for-each>
		   	</tr></table>
		   	</td></xsl:if>
  		</tr>
		</xsl:for-each>
</xsl:template>


</xsl:stylesheet>