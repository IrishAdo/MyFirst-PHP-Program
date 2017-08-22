<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.13 $
- Modified $Date: 2005/02/09 12:13:11 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:include href="checkboxes.xsl"/>

<xsl:variable name="setup_type"><xsl:value-of select="//xml_document/modules/module[@name='client']/licence/product/@type" /></xsl:variable>

<xsl:template name="display_header_data">
<base><xsl:attribute name="href">http<xsl:if test="//setting[@name='SSL']='yes'">s</xsl:if>://<xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='domain']"/><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/></xsl:attribute></base>
<title><xsl:choose>
	<xsl:when test="//module/page_options/header"><xsl:value-of select="//module/page_options/header"/></xsl:when>
	<xsl:otherwise>Libertas-Solutions :: Administration
	</xsl:otherwise>
</xsl:choose><xsl:if test="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='expires']"> - Demo expires on <xsl:value-of select="/xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='expires']"/></xsl:if>
</title>
<link href="libertas_images/themes/site_administration/favicon.ico" rel="shortcut icon"/>
<link rel='stylesheet' type='text/css'><xsl:attribute name="href"><xsl:value-of select="$image_path"/>/style.css</xsl:attribute></link>
	<xsl:if test ="//file_associate">
		<script src="/libertas_images/javascripts/module_info_dir_files.js"></script>
	</xsl:if>
</xsl:template>

<xsl:template name="display_form_data">
	<xsl:param name="ignore">--nothing to ignore--</xsl:param>
	<xsl:param name="showrequired">0</xsl:param>
	<table cellpadding="0" cellspacing="0" summary="This table holds a form">	
		<xsl:if test="@width">
			<xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute>
		</xsl:if>
		<xsl:variable name="column_counter"><xsl:for-each select="child::*"><xsl:if test="@type!='hidden'">1</xsl:if><xsl:if test="name()!='input'">1</xsl:if></xsl:for-each></xsl:variable>
		<tr><td valign="top" class="formbackground"><table border="0" width="100%" cellpadding="3" cellspacing="1" summary="This table holds the row information for the forms">
		<xsl:if test="local-name(..)!='filter'">
		<tr><xsl:comment>no filter</xsl:comment>
		   	<td valign="top" class="formheader"><xsl:attribute name="colspan"><xsl:value-of select="string-length($column_counter) + 1"/></xsl:attribute><b><xsl:call-template name="get_translation">
				<xsl:with-param name="check" select="@label"/>
			</xsl:call-template></b></td>
  		</tr>
		</xsl:if>
		<xsl:if test="//@error=1">
		<tr><td>
		<h1>Error with form</h1>
		<p>The following fields have been found to be duplicates</p>
		<ul class="error">
			<xsl:for-each select="descendant-or-self::node()[./@error=1]">
				<li><label><xsl:attribute name='for'><xsl:value-of select="@name"/></xsl:attribute><xsl:value-of select="@label"/></label></li>
			</xsl:for-each>
		</ul></td></tr></xsl:if>

		<xsl:if test="//@required and $showrequired=1">
		<tr>
			<xsl:comment>required</xsl:comment>
		   	<td valign="top" colspan="2"><span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template></td>
  		</tr>
		</xsl:if>
		<xsl:if test="local-name(..)='filter'">
		<tr>
			<xsl:for-each select="child::*">
				<xsl:choose>
				<xsl:when test="@type!='hidden' and name()!='selection'">
				   	<td valign="top"><label class="filterlabel"><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
						<xsl:when test="@label"><xsl:value-of select="@label"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="label"/></xsl:otherwise>
					</xsl:choose></xsl:with-param></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if></td>			
				</xsl:when>
				<xsl:when test="name()!='input' and name()!='url'">
				   	<td valign="top"><label class="filterlabel"><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if></td>			
				</xsl:when>
				<xsl:when test="name()='choose_categories'">
				   	<td valign="top"><label class="filterlabel"><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if></td>			
				</xsl:when>
				</xsl:choose>
			</xsl:for-each>
			<xsl:if test="child::*[@type='submit']">
		   	<td rowspan="2" valign="bottom">
	  	 	<xsl:for-each select="input">
  				<xsl:if test="@type='submit'"><input type='submit' border="0" class="filterbutton">
				   	<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:attribute>
				   	<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:attribute>
				   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
				   	</input>&#32;</xsl:if>
				<xsl:if test="@type='button'">
				<a><xsl:attribute name="href"><xsl:choose>
						<xsl:when test="@command='BACK'">javascript:history.back();</xsl:when>
						<xsl:when test="@command='VEHICLE_LOOKUP_REMOVE'">javascript:lookup_remove(document.<xsl:value-of select="../@name"/>);</xsl:when>
						<xsl:otherwise>admin/index.php?command=<xsl:value-of select="@command"/></xsl:otherwise>
					</xsl:choose>
				</xsl:attribute>
				<img border="0">
				   	<xsl:attribute name="src">/libertas_images/themes/site_administration/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
				   	<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:attribute>
				   	<xsl:attribute name="id"><xsl:value-of select="@command"/></xsl:attribute>
			   	</img></a>&#32;</xsl:if>
		   	</xsl:for-each></td>
			</xsl:if>
		</tr>
		<tr>
			<xsl:for-each select="child::*">
				<xsl:if test="local-name()!=$ignore">
					<xsl:choose>
						<xsl:when test="@type!='hidden'"><xsl:if test="@type!='submit'"><xsl:apply-templates select="."/></xsl:if></xsl:when>
						<xsl:otherwise><xsl:apply-templates select="."/></xsl:otherwise>
					</xsl:choose>
				</xsl:if>
			</xsl:for-each>
		</tr>
		</xsl:if>
		<xsl:if test="local-name(..)!='filter'">
			<xsl:for-each select="child::*">
				<xsl:choose>
					<xsl:when test="@type!='hidden'">
						<xsl:choose>
							<xsl:when test="@type='button'"></xsl:when>
							<xsl:when test="@type='submit'"><xsl:if test="../@name='USERS_SHOW_LOGIN'">
								<tr>
								   	<td class="TableLabel" align="right" valign="top"><input type="submit" class="filterbutton" border="0">
									<xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:attribute>
									</input></td>			
								</tr></xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<xsl:if test="local-name()!=$ignore">
									<tr>
										<xsl:if test="local-name()='field_form'">
											<xsl:attribute name="class"><xsl:choose><xsl:when test="position() mod 2 = 0">tablecell</xsl:when><xsl:otherwise>tablecell_alt</xsl:otherwise></xsl:choose></xsl:attribute>
											<td><xsl:call-template name="display_form_field"/></td>
										</xsl:if>
									   	<td class="TableLabel" valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></td>			
									</tr>
									<tr><xsl:apply-templates select="self::*"/></tr>
								</xsl:if>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:when>
					<xsl:otherwise>
					<xsl:if test="local-name()!=$ignore">
							<xsl:if test="@label!=''">
								<tr>
									<xsl:if test="local-name()='field_form'">
										<xsl:attribute name="class"><xsl:choose><xsl:when test="position() mod 2 = 0">tablecell</xsl:when><xsl:otherwise>tablecell_alt</xsl:otherwise></xsl:choose></xsl:attribute>
										<td><xsl:call-template name="display_form_field"/></td>
									</xsl:if>
						   			<td valign="top" class="TableLabel"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></td>			
								</tr>
							</xsl:if>
							<xsl:if test="@type!='hidden'">
								<tr><xsl:apply-templates select="self::*"/></tr>
							</xsl:if>
							<xsl:if test="local-name()!='input'">
								<tr><xsl:if test="local-name()='checkboxes'">1</xsl:if><xsl:apply-templates select="self::*"/></tr>
							</xsl:if>
						</xsl:if>
					</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
		</xsl:if>
	</table>
</td></tr></table>
</xsl:template>

<xsl:template match="form">
	<xsl:if test="boolean(page_sections/section/@redirect)">
		<script>
			function mysubmit(redirect){
				var f= get_form();
				f.onsaveredirect.value = redirect;
				f.submit();
			}
		</script>
	</xsl:if>
	<xsl:if test="//config or @name!='USERS_SHOW_LOGIN'">
	<script>
		<xsl:choose>
		<xsl:when test="contains(//setting[@name='qstring'],'LIBERTAS_DEBUG_JAVASCRIPT')">
		if (confirm("Do you want to disable javascript debugging???")){
			var tw_debug_on = false;
		} else {
			var tw_debug_on = true;
		}
		</xsl:when>
		<xsl:otherwise>
		var tw_debug_on = false;
		</xsl:otherwise>
		</xsl:choose>
	</script>
    <script src="/libertas_images/javascripts/debug/tw_debug.js" type="text/javascript"></script>
	<script src='/libertas_images/javascripts/generic_functions.js'></script>
</xsl:if>
<xsl:if test="@name!='USERS_SHOW_LOGIN'">
	<script>
		<xsl:comment>
			debug("Defining generic variables");
			var check_required 			= new Array(<xsl:for-each select="//input[@required='YES']"> new Array('<xsl:value-of select="@name"/>','tab')<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
			var has_editor				= <xsl:choose><xsl:when test="//textarea[@type='RICH-TEXT']">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>;
			var check_required			= new Array();
			var fld_addtotitle			= new Array();
			var check_contains_selected = new Array();
			var check_compair 			= new Array();
			var check_editors 			= new Array();
			var check_selection			= new Array();
			var hidden_list 			= new Array();
			var objects_to_check		= new Array();
			var product_version = '<xsl:value-of select="//xml_document/modules/module/licence/product/@type"/>';
			var browserstring = '<xsl:value-of select="//xml_document/modules/module[@name='system_prefs']/setting[@name='browser']"/>';
			var info = browserstring.substring(browserstring.indexOf("MSIE")).split(';');
			var version="mozilla";
			if (info[0].indexOf(" ")!=-1){
				version = info[0].split(" ")[1];
			} else {
				version = info[0].split("MSIE")[1];
			}

			var check_build_dates = new Array(<xsl:for-each select="//form/input[@type='date_time']">'<xsl:value-of select="@name"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);

			var check_not_allowed = new Array(<xsl:for-each select="//form/select/option[@disabled='true']">'<xsl:value-of select="@value"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);

			<xsl:value-of select="../javascript" disable-output-escaping="yes" />
			<xsl:choose>
			<xsl:when test="@name">
			function get_form(){
				return document.<xsl:value-of select="@name"/>;
			}
			</xsl:when>
			<xsl:otherwise>
			function get_form(){
				return undefined;
			}
			</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="//select/option[@disabled='true']">
					var selectdisabled = true;
					var selectfield = '<xsl:value-of select="//select[option[@disabled='true']]/@name"/>'
				</xsl:when>
				<xsl:otherwise>
					var selectdisabled = true;
					var selectfield = ''
				</xsl:otherwise>
			</xsl:choose>


			
			debug("Defining generic variables  - DONE");
		//</xsl:comment>
	</script>
	<xsl:choose>
		<xsl:when test="@name">
			<script src='/libertas_images/javascripts/checkform.js'></script>
		</xsl:when>
		<xsl:otherwise>
			<SCRIPT>
				debug("Over ride check form functions  - DONE");
				function check_required_fields(rt){return true;}
			</SCRIPT>
		</xsl:otherwise>
	</xsl:choose>
	<script src='/libertas_images/javascripts/module_pages.js'></script>
	<script src='/libertas_images/javascripts/module.js'></script>
	</xsl:if>
	<xsl:if test="//config">
		<script>
		var editor_configurations = new Array(<xsl:for-each select="//xml_document/modules/module/form/editors/editor"> new Array(<xsl:value-of select="@identifier"/>,"<xsl:value-of select="." disable-output-escaping="yes"/>")<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
		debug("Defining Editor Configurations - DONE");
		</script>
	</xsl:if>
	<xsl:if test="//textarea[@type='RICH-TEXT']">
		<div id="divTemp" style="VISIBILITY: hidden; OVERFLOW: hidden; POSITION: absolute; WIDTH: 1px; HEIGHT: 1px"></div>
		<IFRAME id="tableWizard" style="Z-INDEX: 2; VISIBILITY: hidden; WIDTH: 10px; POSITION: absolute; HEIGHT: 10px" marginWidth="0" marginHeight="0" src="about:blank" scrolling="no"></IFRAME>
		<script>
		var wai_compliance = ('<xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='sp_wai_forms']"/>'=='Yes')?1:0;
		debug("Defining wai_compliance - DONE");
		</script>
		<xsl:choose>		
			<xsl:when test="$browser_type='IE'">
				<script language="JavaScript" src="/libertas_images/editor/libertas/js/main.js"></script>
				<script language="JavaScript" src="/libertas_images/editor/libertas/js/menu.js"></script>
				<script language="JavaScript" src="/libertas_images/editor/libertas/js/grid.js"></script>
				<script language="JavaScript" src="/libertas_images/editor/libertas/js/extended.js"></script>
			</xsl:when>	
			<xsl:otherwise>				
				<script language="JavaScript" src="/libertas_images/editor/libertas/js/extended_gecko.js"></script>			
				<script language="JavaScript" src="/libertas_images/editor/libertas/js/main_gecko.js"></script>
			</xsl:otherwise>				  		
		</xsl:choose>		  		
		<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/toolbar.css"/>		
	</xsl:if>
	<xsl:if test="//textarea[@type='RICH-TEXT'] or //showframe='1'">
		<xsl:choose>
			<xsl:when test="contains(//setting[@name='qstring'],'LIBERTAS_EDITOR=SHOW_CACHE')"><iframe src="/libertas_images/editor/libertas/cache.php" width='100%' height='300' id='cache_data' name='cache_data'></iframe></xsl:when>
			<xsl:otherwise><iframe src="/libertas_images/editor/libertas/cache.php" width='100%' height='0' style='display:none;visibility:hidden' id='cache_data' name='cache_data'></iframe></xsl:otherwise>
		</xsl:choose>
	</xsl:if>
	<form >
		<xsl:attribute name="action"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/></xsl:attribute>
	<xsl:if test="@name">
		<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
	</xsl:if>
	<xsl:if test="//input[@type='file']">
		<xsl:attribute name="enctype">multipart/form-data</xsl:attribute>
	</xsl:if>
	<xsl:choose>
		<xsl:when test="@method">
			<xsl:attribute name="method"><xsl:value-of select="@method"/></xsl:attribute>
		</xsl:when>
		<xsl:otherwise>
			<xsl:attribute name="method">post</xsl:attribute>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:if test="//ranks">
	</xsl:if>
	<xsl:if test="boolean(page_sections/section/@redirect)">
		<input type='hidden'>
		   	<xsl:attribute name="name">onsaveredirect</xsl:attribute>
			<xsl:attribute name="value"></xsl:attribute>
	   	</input>
	</xsl:if>
	<xsl:for-each select="//input[@type='hidden']">
		<input type='hidden'>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:choose>
					<xsl:when test="@value"><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="@value"/></xsl:with-param>
						</xsl:call-template></xsl:when>
					<xsl:otherwise><xsl:call-template name="print">
						<xsl:with-param name="str_value"><xsl:value-of select="." /></xsl:with-param>
					</xsl:call-template></xsl:otherwise>
			</xsl:choose></xsl:attribute>
	   	</input>
	</xsl:for-each>
	<xsl:choose>
		<xsl:when test="page_sections">
			<table border="0" cellspacing="0" cellpadding="0" width="100%">
				<tr>
				<xsl:for-each select="page_sections/section">
					<td valign="bottom"><xsl:attribute name="id">section_button_<xsl:value-of select="@name"/>_btn</xsl:attribute><table width="100%" cellspacing="0" cellpadding="0" style="height:100%">
					<tr>
					<td style="width:1px;height:1px;background:#ffffff"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
					<td valign="middle" align="center" style="height:1px;background:#999999"><img src="/libertas_images/themes/1x1.gif" border="0" height="1" alt=""><xsl:attribute name="width">108</xsl:attribute></img></td>
					<td style="width:1px;height:1px;background:#ffffff" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
					</tr>
					<tr>
					<td style="height:100%;width:1px;background:#999999"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="36" alt=""/></td>
					<td valign="middle" align="center" class="buttonOff" style="height:100%">
					<xsl:if test="@accesskey"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:if>
					<xsl:attribute name="onclick"><xsl:choose>
						<xsl:when test='boolean(@redirect)'>javascript:mysubmit('<xsl:value-of select="@redirect"/>');</xsl:when>
						<xsl:when test='boolean(@onclick)'>javascript:show_tabular_screen('tab_<xsl:value-of select="position()"/>');<xsl:value-of select="@onclick" disable-output-escaping="yes"/>(<xsl:for-each select="parameters/field">'<xsl:value-of select="."/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);</xsl:when>
						<xsl:otherwise>javascript:show_tabular_screen('tab_<xsl:value-of select="position()"/>');</xsl:otherwise>
					</xsl:choose></xsl:attribute>
						<xsl:attribute name="id">btn_tab_<xsl:value-of select="position()"/></xsl:attribute>
						<!--<xsl:attribute name="onMouseOver">javascript:this.style.cursor='hand';</xsl:attribute>-->
						<xsl:attribute name="name">tab_btn_<xsl:value-of select="position()"/></xsl:attribute>[[nbsp]]<xsl:value-of select="@label"/>[[nbsp]]</td>
					<td style="height:100%;width:1px;background:#333333" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="36" alt=""/></td>
					<xsl:if test="position()!=last()">
						<td style="height:100%;width:1px;border-bottom:1px solid #333333;"><xsl:attribute name="id">section_button_<xsl:value-of select="@name"/>_spacer</xsl:attribute><img src="/libertas_images/themes/1x1.gif" border="0" width="2" height="36" alt=""/></td>
					</xsl:if>
					</tr></table></td>
				</xsl:for-each>
					<td width="100%" class="tabempty"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="36" alt=""/></td>
				</tr>
			</table>
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr><td style="border-left:1px solid #999999;border-top:0px solid #ffffff;border-right:1px solid #333333;border-bottom:1px solid #333333;"><xsl:attribute name="colspan"><xsl:value-of select="(count(page_sections/section) * 2 ) -1 "/></xsl:attribute><xsl:for-each select="page_sections/section">
					<xsl:call-template name="displaySection"><xsl:with-param name="section_name">tab</xsl:with-param></xsl:call-template>
				</xsl:for-each></td></tr>
			</table>
			<script>
				debug ("Drawing Screen tabs")
				var screen_tabs = new Array(<xsl:for-each select="page_sections/section">'tab_<xsl:value-of select="position()"/>'<xsl:if test="section">,<xsl:variable name="pos"><xsl:value-of select="position()"/></xsl:variable><xsl:for-each select="section">'tab_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each></xsl:if><xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
				<xsl:variable name="hidetabs"><xsl:for-each select="page_sections/section[@hidden='true']">"section_button_<xsl:value-of select="@name"/>"<xsl:if test="position()!=last()">, </xsl:if></xsl:for-each></xsl:variable>
				<xsl:variable name="tab">
				<xsl:for-each select="page_sections/section">
					<xsl:if test="@selected='true'">tab_<xsl:value-of select="position()"/></xsl:if>	
				</xsl:for-each>
				</xsl:variable>
				debug ("Drawing Screen tabs - DONE [drew <xsl:value-of select="count(page_sections/section)"/>]")
				hiddenlist = new Array(<xsl:value-of select="$hidetabs" />)
				show_tabular_screen('<xsl:choose><xsl:when test="$tab!=''"><xsl:value-of select="$tab"/></xsl:when><xsl:otherwise>tab_1</xsl:otherwise></xsl:choose>', hiddenlist);
			</script>
		</xsl:when>
	<xsl:otherwise>
		<xsl:call-template name="display_form_data"/>
	</xsl:otherwise>
	</xsl:choose>
	</form>
	<script src="/libertas_images/javascripts/module_retrieve.js"></script>
	<xsl:if test="//section/@onclick='preview_poll'">
	<script src="/libertas_images/javascripts/module_poll.js"></script>
 	</xsl:if>
</xsl:template>
<xsl:template match="div">
	<div><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
	<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute></div>
</xsl:template>
<xsl:template match="btn">
	<input type='button'>
		<xsl:if test="@hidden='YES'">
		<xsl:attribute name="style">display:none</xsl:attribute>
		</xsl:if>
		<xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute>
		<xsl:attribute name="onclick"><xsl:value-of select="@onclick"/>()</xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
		<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
		<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
	</input>
</xsl:template>

<xsl:template match="select">
		   	<td>
				<xsl:variable name="length"><xsl:call-template name="setMaxLength"><xsl:with-param name="length"><xsl:call-template name="getMaxLength"/></xsl:with-param></xsl:call-template></xsl:variable>
			<select>
			<xsl:if test="@multiple='1'"><xsl:attribute name="multiple">1</xsl:attribute>
				<xsl:if test="@size">
					<xsl:attribute name="size"><xsl:value-of select="@size"/></xsl:attribute>
				</xsl:if>
			</xsl:if>
			<xsl:if test="number($length) > 90"><xsl:attribute name="style">width:600px</xsl:attribute></xsl:if>
		   	<xsl:if test="../@name='vehicle_form'">
			   	<xsl:choose>
				   	<xsl:when test="@name='vehicle_manufacturer'"><xsl:attribute name="onchange">javascript:Fill_Model_Combo(this.options[this.options.selectedIndex].value,-1,manufacture_and_model);</xsl:attribute></xsl:when>
		   			<xsl:when test="@name='vehicle_model'"><xsl:attribute name="onchange">javascript:check(&#34;model&#34;);</xsl:attribute></xsl:when>
		   			<xsl:when test="@name='vehicle_cab'"><xsl:attribute name="onchange">javascript:check(&#34;cab&#34;);</xsl:attribute></xsl:when>
		   			<xsl:when test="@name='vehicle_gears'"><xsl:attribute name="onchange">javascript:check(&#34;gears&#34;);</xsl:attribute></xsl:when>
		   			<xsl:when test="@name='vehicle_body'"><xsl:attribute name="onchange">javascript:check(&#34;body&#34;);</xsl:attribute></xsl:when>
		   		</xsl:choose>
		   	</xsl:if>
			<xsl:if test="@onchange"><xsl:attribute name="onchange"><xsl:value-of select="@onchange"/></xsl:attribute></xsl:if>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/><xsl:if test="@multiple='1'">[]</xsl:if></xsl:attribute>
			<xsl:if test="@special">
				<xsl:if test="//session/groups/group/access='ALL' or //session/groups/group/access='LAYOUT_ALL' or //session/groups/group/access='LAYOUT_AUTHOR_CAN_MANAGE_MENU'">
					<xsl:attribute name="onchange">javascript:menucheck();</xsl:attribute>
				</xsl:if>
			</xsl:if>
			<xsl:if test="optgroup">
		   	<xsl:for-each select="optgroup">
				<optgroup><xsl:attribute name="label"><xsl:value-of select="@label"/></xsl:attribute>
				   	<xsl:for-each select="option">
					   	<option><xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value" disable-output-escaping="yes"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
						<xsl:choose>
							<xsl:when test="(@checked='true' or @selected='true') and not(//values/field[@name=$name])">
								<xsl:if test="(@checked='true' or @selected='true')">
									<xsl:attribute name="selected">selected</xsl:attribute>
								</xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<xsl:variable name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value" disable-output-escaping="yes"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:variable>
								<xsl:for-each select="//values/field[@name=$name]">
									<xsl:if test=".=$value"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
								</xsl:for-each>
							</xsl:otherwise>
						</xsl:choose><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></option>
				   	</xsl:for-each>
				</optgroup>
		   	</xsl:for-each>
			</xsl:if>
			
			<xsl:if test="option">
				<xsl:if test="@special and (//session/groups/group/access='ALL' or //session/groups/group/access='LAYOUT_ALL' or //session/groups/group/access='LAYOUT_AUTHOR_CAN_MANAGE_MENU')">
					<option value="">Choose one</option>
					<option value="-1,-1"><xsl:attribute name="style">color:#ff0000</xsl:attribute><xsl:choose>
						<xsl:when test="@special='page_add_new_menu'">New menu Location</xsl:when>
						<xsl:otherwise>Special Function </xsl:otherwise>
					</xsl:choose></option>
				</xsl:if>
				
			   	<xsl:for-each select="option">
		   			<option><xsl:attribute name="value"><xsl:choose>
						<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:attribute>
						<xsl:if test="boolean(//session/admin_restriction/locations/location)">
							<xsl:variable name="val"><xsl:value-of select="@value"/></xsl:variable>
							<xsl:if test="@disabled='true' or (../@name='menu_parent' and boolean(../@onsave) and not(//session/admin_restriction/locations/location[. = $val]))">
								<xsl:attribute name="style">color:#666666;</xsl:attribute>
							</xsl:if>
						</xsl:if>
						<xsl:if test="@selected"><xsl:attribute name="selected"><xsl:value-of select="@selected"/></xsl:attribute></xsl:if><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:call-template name="print">
						<xsl:with-param name="str_value"><xsl:value-of select="."/></xsl:with-param>
						</xsl:call-template></xsl:with-param></xsl:call-template></option>
			   	</xsl:for-each>
			</xsl:if>
		   	</select><xsl:if test="@other='true'">[[nbsp]]<input style="visibility:hidden" type='text' ><xsl:attribute name="name"><xsl:value-of select="@name"/>_extra</xsl:attribute></input></xsl:if>
			<xsl:if test="@special">
			<div ><xsl:attribute name="id">special_<xsl:value-of select="@special"/></xsl:attribute></div>
				<script><xsl:attribute name="src">/libertas_images/javascripts/special_<xsl:value-of select="@special"/>.js</xsl:attribute></script>
			</xsl:if>
			<xsl:if test="@onsave">
			<script>
				check_selection[check_selection.length] = Array('<xsl:value-of select="@onsave"/>', '<xsl:value-of select="@label"/>', 'tab_1');
			</script>
			</xsl:if>
			<xsl:if test="./@addtotitle='1'">
				<script>
					fld_addtotitle[fld_addtotitle.length] = '<xsl:value-of select="@name"/>';
				</script>					
			</xsl:if>			
			</td>  		
</xsl:template>

<xsl:template name="getMaxLength">
	<xsl:param name="length">0</xsl:param>
	<xsl:choose>
		<xsl:when test="option[string-length(.) > number($length)]"><xsl:variable name="new_length"><xsl:for-each select="option[string-length(.) > number($length)][position()=1]"><xsl:value-of select="string-length(.)"/></xsl:for-each></xsl:variable>
		<xsl:call-template name="getMaxLength"><xsl:with-param name="length"><xsl:value-of select="$new_length"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:otherwise>
		<xsl:value-of select="$length"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="setMaxLength">
	<xsl:param name="length">0</xsl:param>
	<xsl:choose>
		<xsl:when test="$length=0"></xsl:when>
		<xsl:otherwise>
		<xsl:variable name="new_str"><xsl:for-each select="option[string-length(.) = number($length)][position()=1]"><xsl:value-of select="."/></xsl:for-each></xsl:variable>
		<xsl:variable name="rep_str"><xsl:call-template name="remove_string">
				<xsl:with-param name="str"><xsl:value-of select="$new_str"/></xsl:with-param>
			</xsl:call-template></xsl:variable>
		<xsl:value-of select="string-length($rep_str)"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="remove_string">
	<xsl:param name="str"></xsl:param>
	<xsl:param name="find">[[nbsp]]</xsl:param>
	<xsl:param name="replace"><xsl:value-of select="' '"/></xsl:param>
	
	<xsl:choose>
		<xsl:when test="contains($str,$find)">
		<xsl:value-of select="substring-before($str, $find)"/><xsl:value-of select="$replace"/><xsl:call-template name="remove_string">
				<xsl:with-param name="str"><xsl:value-of select="substring-after($str, $find)"/></xsl:with-param>
			</xsl:call-template>
		</xsl:when>
		<xsl:otherwise>[<xsl:value-of select="$str"/>]</xsl:otherwise>
	</xsl:choose>
</xsl:template>



<xsl:template match="input">
	<xsl:choose>
  		<xsl:when test="@type='quantity' or @format='unlimited'">
			<td>
			<select>
				<xsl:attribute name="name">quantity_<xsl:value-of select="@name"/></xsl:attribute>
				<xsl:attribute name="onchange">javascript:setquantity('<xsl:value-of select="@name"/>');</xsl:attribute>
				<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
				<option value='-1'><xsl:if test=".=-1"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Unlimited</option>
				<option value='-2'><xsl:if test=".!=-1"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Defined</option>
			</select>
			<input type='text'>
				<xsl:if test=".=-1">
					<xsl:attribute name='style'>display:none</xsl:attribute>
				</xsl:if>
				<xsl:attribute name='name'><xsl:value-of select="@name"/></xsl:attribute>
				<xsl:attribute name='id'>id_<xsl:value-of select="@name"/></xsl:attribute>
				<xsl:attribute name='value'><xsl:value-of select="."/></xsl:attribute>
				<xsl:attribute name='onchange'>javascript:check_format(this,'number')</xsl:attribute>
			</input>
			</td>
		</xsl:when>
  		<xsl:when test="@type='text'">
		   	<td>
			<input type='text'>
				<xsl:attribute name="value"><xsl:choose>
					<xsl:when test="value"><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="value"/></xsl:with-param>
						</xsl:call-template></xsl:when>
					<xsl:otherwise><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="." /></xsl:with-param>
						</xsl:call-template></xsl:otherwise>
				</xsl:choose></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:choose>
    			<xsl:when test="@size>40 or not(@size)">
	  				<xsl:attribute name="style">width:320px</xsl:attribute>
	   				<xsl:attribute name="maxlength"><xsl:choose>
						<xsl:when test="@length"><xsl:value-of select="@length"/></xsl:when>
						<xsl:when test="@size"><xsl:value-of select="@size"/></xsl:when>
						<xsl:otherwise>255</xsl:otherwise>
					</xsl:choose></xsl:attribute>
				</xsl:when>
   				<xsl:otherwise>
   					<xsl:attribute name="style">width:<xsl:value-of select="@size*8"/>px</xsl:attribute>
	   					</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="@format"><xsl:attribute name='onchange'>javascript:check_format(this,'<xsl:value-of select="@format"/>')</xsl:attribute></xsl:if>
			</input>
			<script>
				<xsl:if test="local-name(..)='seperator' and ./@required='YES'">
					<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
					<xsl:variable name="span_name"><xsl:value-of select="//page_sections/section[.//input[@name=$name]]/@name"/></xsl:variable>
					check_required[check_required.length] = new Array('<xsl:value-of select="$name"/>', '<xsl:value-of select="@label"/>', '<xsl:value-of select="$span_name"/>');
				</xsl:if>
			</script>
			<xsl:if test="./@addtotitle='1'">
				<script>
					fld_addtotitle[fld_addtotitle.length] = '<xsl:value-of select="@name"/>';
				</script>					
			</xsl:if>			
			</td>
  		</xsl:when>

  		<xsl:when test="@type='file'">
		   	<td>
		   	<xsl:if test="@value">
			   	<xsl:if test="@file_size>'0'">
				   	<b><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'FILE_SIZE'"/></xsl:call-template> ::</b> (<xsl:value-of select="@file_size"/>)<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'FILE_MEASURE_BYTES'"/></xsl:call-template>
			   	</xsl:if>
			   	<xsl:if test="./choice">
			   	<table width="250">
			   		<xsl:for-each select="choice">
		   			<tr><td><input type="radio">
				   	<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
		   			<xsl:attribute name="name"><xsl:value-of select="../@name"/>_<xsl:value-of select="@name"/></xsl:attribute>
			   		<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
			   		<xsl:if test="@checked='true'">
			   			<xsl:attribute name="checked"><xsl:value-of select="@checked"/></xsl:attribute>
		   			</xsl:if>
		   			<xsl:attribute name="onclick">javascript:document.all.file_upload_span_<xsl:value-of select="../@name"/>.style.visibility='<xsl:value-of select="@visibility"/>'</xsl:attribute>
			   		</input>
			   		<label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label>
			   		</td><xsl:if test="position()=1 and  not(../@preview)">
					<td rowspan="2" align="right"><a target="_external_file"><xsl:attribute name="href">?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="../@tag"/><xsl:value-of select="../@value"/></xsl:attribute><img src="/libertas_images/themes/site_administration/button_DOWNLOAD.gif" border="0"><xsl:attribute name="alt">Download this file</xsl:attribute></img></a></td>
					</xsl:if></tr>
		   			</xsl:for-each>
			   	</table>
			   	</xsl:if>
			</xsl:if>
		   	<input type='hidden'>
		   	<xsl:attribute name="name">file_upload_<xsl:value-of select="@name"/>_exists</xsl:attribute>
		   	<xsl:attribute name="value">
		   		<xsl:choose>
		   			<xsl:when test="@file_size>0">1</xsl:when>
		   			<xsl:otherwise>0</xsl:otherwise>
		   		</xsl:choose>
		   	</xsl:attribute>
		   	</input>
		   	<span><xsl:attribute name="id">file_upload_span_<xsl:value-of select="@name"/></xsl:attribute><xsl:if test="@value">
		   	<xsl:if test="./choice">
		   	<xsl:attribute name="style">visibility:hidden</xsl:attribute>
		   	</xsl:if>
		   	</xsl:if>
		   	<input type='file'>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	</input></span>
		   	</td>
  		
  		</xsl:when>
  		<xsl:when test="@type='password'">
		   	<td><input type='password'>
		   	<xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:if test="@size">
				<xsl:choose>
    				<xsl:when test="@size>40">
		   				<xsl:attribute name="style">width:320px</xsl:attribute>
		   			</xsl:when>
		   			<xsl:otherwise>
		   				<xsl:attribute name="style">width:<xsl:value-of select="@size*8"/>px</xsl:attribute>
		   			</xsl:otherwise>
		   		</xsl:choose>
		   		<xsl:attribute name="maxlength"><xsl:value-of select="@size"/></xsl:attribute>
		   	</xsl:if>
			<xsl:if test="@format"><xsl:attribute name='onchange'>javascript:check_format(this,'<xsl:value-of select="@format"/>')</xsl:attribute></xsl:if>
		   	</input></td>
  		
  		</xsl:when>
  		<xsl:when test="@type='date_time' or @type='date' or @type='time'">
		   	<td>
			<input type='hidden'>
				<xsl:attribute name='name'><xsl:value-of select='@name'/></xsl:attribute>
				<xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose></xsl:attribute>
			</input>
			<xsl:variable name="year"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="substring-before(@value,'-')"/></xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose></xsl:variable>
			<xsl:variable name="month"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="substring-before(substring-after(@value,'-'),'-')"/></xsl:when><xsl:otherwise></xsl:otherwise></xsl:choose></xsl:variable>
			<xsl:variable name="day"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="substring-before(substring-after(substring-after(@value,'-'),'-'),' ')"/></xsl:when><xsl:otherwise></xsl:otherwise></xsl:choose></xsl:variable>
			<xsl:variable name="hour"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="substring-before(substring-after(@value,' '),':')"/></xsl:when><xsl:otherwise></xsl:otherwise></xsl:choose></xsl:variable>
			<xsl:variable name="minutes"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="substring-before(substring-after(substring-after(@value,' '),':'),':')"/></xsl:when><xsl:otherwise></xsl:otherwise></xsl:choose></xsl:variable>
				
			<xsl:variable name="year_start"><xsl:choose><xsl:when test="@year_start"><xsl:value-of select="@year_start"/></xsl:when><xsl:otherwise><xsl:value-of select="//setting[@name='year'] - 1"/></xsl:otherwise></xsl:choose></xsl:variable>
			<xsl:variable name="year_end"><xsl:choose><xsl:when test="@year_start"><xsl:value-of select="@year_end"/></xsl:when><xsl:otherwise><xsl:value-of select="//setting[@name='year'] + 1"/></xsl:otherwise></xsl:choose></xsl:variable>
			<xsl:if test="contains(@type,'date')">
			<select><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_day</xsl:attribute>
				<option value=''></option>
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$day"/>
			   		<xsl:with-param name="start" select="1"/>
			   		<xsl:with-param name="end" select="31"/>
			   		<xsl:with-param name="type" select="day"/>
			 	</xsl:call-template>
			</select>
			<select><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_month</xsl:attribute>
				<option value=''></option>
				<option value='01'><xsl:if test="$month='01'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>Janurary</option>
				<option value='02'><xsl:if test="$month='02'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>Feburary</option>
				<option value='03'><xsl:if test="$month='03'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>March</option>
				<option value='04'><xsl:if test="$month='04'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>April</option>
				<option value='05'><xsl:if test="$month='05'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>May</option>
				<option value='06'><xsl:if test="$month='06'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>June</option>
				<option value='07'><xsl:if test="$month='07'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>July</option>
				<option value='08'><xsl:if test="$month='08'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>August</option>
				<option value='09'><xsl:if test="$month='09'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>September</option>
				<option value='10'><xsl:if test="$month='10'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>October</option>
				<option value='11'><xsl:if test="$month='11'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>November</option>
				<option value='12'><xsl:if test="$month='12'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>December</option>
			</select>
<!--
			[700:: <xsl:value-of select="$year"/>, <xsl:value-of select="$year_start"/>, <xsl:value-of select="$year_end"/>]
-->
			<select><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_year</xsl:attribute>
				<option value=''></option>
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$year"/>
			   		<xsl:with-param name="start" select="$year_start"/>
			   		<xsl:with-param name="end" select="$year_end"/>
			   		<xsl:with-param name="type" select="year"/>
			 	</xsl:call-template>
			</select>
				</xsl:if>
				<xsl:if test="contains(@type,'time')">
			<select><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_hour</xsl:attribute>
				<option value=''></option>
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$hour"/>
			   		<xsl:with-param name="start" select="0"/>
			   		<xsl:with-param name="end" select="24"/>
			   		<xsl:with-param name="type" >hour</xsl:with-param>
					<xsl:with-param name="expand">1</xsl:with-param>
			 	</xsl:call-template>
			</select>
			<select><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_minute</xsl:attribute>
				<option value=''></option>
				<option value='00'><xsl:if test="$minutes = '00'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>00</option>
				<option value='15'><xsl:if test="$minutes = '15'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>15</option>
				<option value='30'><xsl:if test="$minutes = '30'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>30</option>
				<option value='45'><xsl:if test="$minutes = '45'"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>45</option>
			</select>
				</xsl:if>
			</td>
  		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_date">
	<xsl:param name="current"/>
	<xsl:param name="start"/>
	<xsl:param name="end"/>
	<xsl:param name="type"/>
	<xsl:param name="expand">0</xsl:param>
	<option ><xsl:if test="number($current) = ($start)"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>
	<xsl:attribute name="value"><xsl:if test="$start = '1' or $start = '2' or $start = 3 or $start = 4 or $start = 5 or $start = 6 or $start = 7 or $start = 8 or $start = 9 or $start = 0 ">0</xsl:if><xsl:value-of select="$start"/></xsl:attribute>
	<xsl:if test="$expand=1 and ($start = '1' or $start = '2' or $start = 3 or $start = 4 or $start = 5 or $start = 6 or $start = 7 or $start = 8 or $start = 9 or $start = 0 )">0</xsl:if><xsl:value-of select="$start"/></option>
	<xsl:if test="$start != $end">
		<xsl:call-template name="display_date">
	   		<xsl:with-param name="current" select="$current"/>
	   		<xsl:with-param name="start" select="$start + 1"/>
	   		<xsl:with-param name="end" select="$end"/>
	   		<xsl:with-param name="type" select="$type"/>
			<xsl:with-param name="expand"><xsl:value-of select="$expand"/></xsl:with-param>
	 	</xsl:call-template>
	</xsl:if>
</xsl:template>


<xsl:template match="page_options">
	<xsl:if test="button">
	<xsl:for-each select="button">
		<a><xsl:attribute name="href"><xsl:choose>
			<xsl:when test="@command='FILES_ASSOCIATE_FILES'">javascript:file_associate();</xsl:when>
			<xsl:when test="@command='MENU_LIST'">javascript:layout_associate();</xsl:when>
			<xsl:when test="@command='SFORM_BUILD_XML'">javascript:buildform.build_XML();</xsl:when>
			<xsl:when test="@command='LAYOUT_PAGE_RANKING'">javascript:button_action('RANK');</xsl:when>
			<xsl:when test="@iconify='PREVIEW'"><xsl:choose>
					<xsl:when test="../../@display='form' and ../../@name='information_admin'">javascript:preview_infodir('<xsl:value-of select="@command"/>');</xsl:when>
					<xsl:when test="../../@display='form'">javascript:preview_from_form(<xsl:choose>
					<xsl:when test="../../form/input[@name='page_identifier']"><xsl:choose>
						<xsl:when test="../../form/input[@name='page_identifier']/@value!=''"><xsl:value-of select="../../form/input[@name='page_identifier']/@value"/></xsl:when>
						<xsl:when test="../../form/input[@name='page_identifier']!=''"><xsl:value-of select="../../form/input[@name='page_identifier']"/></xsl:when>
						<xsl:otherwise>-1</xsl:otherwise>
						</xsl:choose>,'page',document.<xsl:value-of select="../../form/@name" />,'PAGE_PREVIEW_FORM'</xsl:when>
					<xsl:otherwise><xsl:choose>
						<xsl:when test="../../form/input[@name='identifier']/@value"><xsl:value-of select="../../form/input[@name='identifier']/@value"/></xsl:when>
						<xsl:when test="../../form/input[@name='identifier']!=''"><xsl:value-of select="../../form/input[@name='identifier']"/></xsl:when>
						<xsl:otherwise>-1</xsl:otherwise>
						</xsl:choose>,'<xsl:value-of select="../../@name" />',document.<xsl:value-of select="../../form/@name" />,'<xsl:value-of select="@command"/>'</xsl:otherwise>
					</xsl:choose>);</xsl:when>
				<xsl:when test="../../@display='results'">javascript:preview_from_list(<xsl:value-of select="../../form/input/@name[.='page_identifier']/@value"/>,'page');</xsl:when>
					<xsl:otherwise>admin/preview.php?command=PAGE_PREVIEW&amp;<xsl:value-of select="@parameters" /></xsl:otherwise>
				</xsl:choose></xsl:when>
			<xsl:when test="@command='GENERAL_BACK'">javascript:history.back();</xsl:when>
			
			<xsl:otherwise>admin/index.php?command=<xsl:value-of select="@command"/>&amp;<xsl:if test="@parameters"><xsl:value-of select="@parameters"/></xsl:if></xsl:otherwise>
			</xsl:choose></xsl:attribute>
			
			<xsl:choose>
				<xsl:when test="@access_key"><xsl:attribute name="accesskey"><xsl:value-of select="@accesskey"/></xsl:attribute></xsl:when>
				<xsl:when test="@iconify='ADD'"><xsl:attribute name="accesskey">n</xsl:attribute><xsl:attribute name="title">New [n]</xsl:attribute></xsl:when>
				<xsl:when test="@iconify='PREVIEW'"><xsl:attribute name="accesskey">p</xsl:attribute><xsl:attribute name="title">preview [p]</xsl:attribute></xsl:when>
				<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
			<xsl:call-template name="display_icon"/></a>
		</xsl:for-each>
	</xsl:if>
</xsl:template>

<xsl:template match="text">
	<td valign="top"><xsl:choose><xsl:when test="@label"></xsl:when><xsl:otherwise><xsl:attribute name="colspan">2</xsl:attribute></xsl:otherwise></xsl:choose>
	<xsl:if test="@id"><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute></xsl:if>
	<xsl:if test="@type or @class"><xsl:attribute name="class"><xsl:choose><xsl:when test="@class"><xsl:value-of select="@class"/></xsl:when><xsl:otherwise>error</xsl:otherwise></xsl:choose></xsl:attribute></xsl:if>
	<xsl:choose>
		<xsl:when test="@name='trans_menu_location'">
		</xsl:when>
		<xsl:when test="@name='trans_group_information'">
			<span><xsl:if test="@name!=''"><xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute></xsl:if><ul>
			<xsl:call-template name="get_translation">
				<xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param>
			</xsl:call-template>
			</ul>
			</span>
			<ul><li><a href="javascript:layout_associate();"><xsl:call-template name="get_translation">
				<xsl:with-param name="check"><xsl:value-of select="'LOCALE_ADD_NEW_ENTRY'"/></xsl:with-param>
			</xsl:call-template></a></li></ul>
		</xsl:when>
		<xsl:otherwise>
			<span><xsl:if test="@name!=''"><xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute></xsl:if>
			<xsl:call-template name="get_translation">
				<xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param>
			</xsl:call-template>
			</span>
		</xsl:otherwise>
	</xsl:choose>
	</td>
</xsl:template>

<xsl:template match="list">
	<td valign="top">
		<ul>
		<xsl:for-each select="option"><li><xsl:value-of select="."/></li></xsl:for-each>
		</ul>
	</td>
</xsl:template>

<xsl:template match="file_associate">
	<tr><td class="TableLabel"><xsl:value-of select="label" disable-output-escaping="yes"/></td></tr>
	<tr><td><div><xsl:attribute name="id">display_list_of_files_<xsl:value-of select="@name"/></xsl:attribute><xsl:attribute name="name">display_list_of_files_<xsl:value-of select="@name"/></xsl:attribute></div>
<!--
			<xsl:if test="@name='ie_image'">
				<div class="TableLabel">Main Image</div>
				<div><xsl:attribute name="id">display_list_of_files_<xsl:value-of select="@name"/>_main</xsl:attribute><xsl:attribute name="name">display_list_of_files_<xsl:value-of select="@name"/>_main</xsl:attribute></div>
			</xsl:if>
-->		
		<SCRIPT>
			if (rf+""=="undefined"){
				var rf = new Array();
			}
			pos 						 = rf.length;
			rf[pos] 					 = new RankingFile();
			rf[pos].key 				 = "<xsl:value-of select="@name"/>";
			rf[pos].file_output			 = "file_associations_"+rf[pos].key;
			rf[pos].position 			 = pos;
			rf[pos].list				 = new Array();
			rf[pos].displaylistoutput 	 = "display_list_of_files_<xsl:value-of select="@name"/>";
			<xsl:for-each select="file_list/file_info">
				rf[pos].add("<xsl:value-of select="." disable-output-escaping="yes"/>", <xsl:value-of select="@identifier"/>, '<xsl:choose><xsl:when test="@rank=''">1</xsl:when><xsl:otherwise><xsl:value-of select="@rank"/></xsl:otherwise></xsl:choose>', "<xsl:value-of select="@logo"/>");</xsl:for-each>
			rf[pos].draw();
		</SCRIPT>
					</td></tr>

</xsl:template>

</xsl:stylesheet>

