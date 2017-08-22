<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:49:48 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:variable name="setup_type"><xsl:value-of select="//xml_document/modules/module[@name='client']/licence/product/@type" /></xsl:variable>

<xsl:template name="display_header_data">
<base><xsl:attribute name="href">http://<xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='domain']"/><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/></xsl:attribute></base>
<title>Libertas-Solutions :: Administration :: 	<xsl:if test="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='expires']"> - Demo expires on <xsl:value-of select="/xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='expires']"/></xsl:if></title>
<link href="libertas_images/themes/site_administration/favicon.ico" rel="shortcut icon"/>
<link rel='stylesheet' type='text/css'><xsl:attribute name="href"><xsl:value-of select="$image_path"/>/style.css</xsl:attribute></link>
</xsl:template>

<xsl:template name="display_form_data">
	<xsl:param name="ignore">--nothing to ignore--</xsl:param>
	<table border="0" cellpadding="0" cellspacing="0" summary="This table holds a form">	
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
		<xsl:if test="//@required">
		<tr> <xsl:comment>required</xsl:comment>
		   	<td valign="top" colspan="2"><span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template></td>
  		</tr>
		</xsl:if>
		<xsl:if test="local-name(..)='filter'">
		<tr>
			<xsl:for-each select="child::*">
				<xsl:choose>
				<xsl:when test="@type!='hidden'">
				   	<td valign="top"><label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if></td>			
				</xsl:when>
				<xsl:when test="name()!='input' and name()!='url'">
				   	<td valign="top"><label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if></td>			
				</xsl:when>
				</xsl:choose>
			</xsl:for-each>
			<xsl:if test="child::*[@type='submit']">
		   	<td rowspan="2">
	  	 	<xsl:for-each select="input">
  				<xsl:if test="@type='submit'"><input type='image' border="0">
				   	<xsl:attribute name="src">/libertas_images/themes/site_administration/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
				   	<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:attribute>
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
								   	<td class="TableLabel" align="right" valign="top"><input type="image" border="0">
									<xsl:attribute name="src">/libertas_images/themes/site_administration/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
									<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:attribute>
									</input></td>			
								</tr></xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<xsl:if test="local-name()!=$ignore">
									<tr>
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
									<xsl:if test="local-name()='form_field'">
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
								<tr><xsl:apply-templates select="self::*"/></tr>
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
<xsl:if test="//config or @name!='USERS_SHOW_LOGIN'">
	<script>
		<xsl:choose>
		<xsl:when test="//setting[@name='remote_addr']='192.168.0.116'">
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
			var browser = '<xsl:value-of select="$browser_type"/>';
			var session_url = '<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>';
			var domain		= "<xsl:value-of select="//xml_document/modules/module[@name='system_prefs']/setting[@name='domain']"/>";
			var base_href 	= "<xsl:value-of select="//xml_document/modules/module[@name='system_prefs']/setting[@name='base']"/>";
			var xcheck_required = Array(<xsl:for-each select="//input[@required='YES']">Array('<xsl:value-of select="@name"/>','tab')<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);

			var check_required = Array();
			var check_contains_selected = Array();
			var check_compair = Array();
			var check_editors = Array();


			var check_build_dates = Array(<xsl:for-each select="//form/input[@type='date_time']">'<xsl:value-of select="@name"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);

			var check_not_allowed = Array(<xsl:for-each select="//form/select/option[@disabled='true']">'<xsl:value-of select="@value"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);

			<xsl:value-of select="../javascript" disable-output-escaping="yes" />

			/*
				if the form has any required fields then execute this function
			*/
			function show_tabular_screen(tab){
				for(i=0;i &lt; screen_tabs.length;i++){
					if (tab==screen_tabs[i]){
						document.getElementById(screen_tabs[i]).style.visibility='visible';
						document.getElementById(screen_tabs[i]).style.display='';
						document.getElementById('btn_'+screen_tabs[i]).className='buttonOn';
					} else {
						document.getElementById(screen_tabs[i]).style.visibility='hidden';
						document.getElementById(screen_tabs[i]).style.display='none';
						document.getElementById("btn_"+screen_tabs[i]).className='buttonOff';
					}
				}
			}

			<xsl:choose>
			<xsl:when test="@name">
			function get_form(){
				return document.<xsl:value-of select="@name"/>;
			}
			function check_required_fields(rt){
				var return_type=1;
				var f = document.<xsl:value-of select="@name"/>;
				if ((rt+"" == "undefined") || (rt+"" == "0")){
					return_type = 0;
				} else {
					return_type = rt;
				}
				ok = 1;

				<xsl:if test="//select/option[@disabled='true']">
				/*
					if the form has any required fields then execute this function
				*/
				for(index=0;index &lt; check_not_allowed.length ; index++){
					if (f.<xsl:value-of select="//select[option[@disabled='true']]/@name"/>.options[f.<xsl:value-of select="//select[option[@disabled='true']]/@name"/>.selectedIndex].value == check_not_allowed[index]){
						ok = 0;
						alert("You do not have publishing access to this location");
					}
				}
				</xsl:if>
				/*
					if the form has any required fields then execute this function
				*/
				if (ok==1){
					for(index=0;index &lt; check_required.length ; index++){
						eval("len = f."+check_required[index][0]+".value.length;");
						if (len==0){
							ok=0;
							alert("You have not filled in the '"+check_required[index][1]+"' field");
							eval("cando = f."+check_required[index][0]+".type;");
							if (cando!='hidden'){
								show_tabular_screen(check_required[index][2]);
								eval("f."+check_required[index][0]+".focus();");
							}
							break;
						}
					}		
				}
				/*
					if the form has any editors then execute this function
				*/

				if (ok==1){
					for(index=0;index &lt; check_editors.length ; index++){
						eval("len = this['"+check_editors[index][0]+"_rEdit'].document.body.innerHTML.length;");
						if (len==0){
							ok=0;
							alert("You have not filled in the '"+check_editors[index][1]+"' field");
							show_tabular_screen(check_editors[index][2]);
							eval("this['"+check_editors[index][0]+"_rEdit'].focus();");
							break;
						}
					}		
				}

				/*
					if the form has any fields that are checkboxes then execute this function
				*/
				if (ok==1){
					for(index=0;index &lt; check_contains_selected.length ; index++){
						eval("myelement =f.elements['"+check_contains_selected[index][0]+"[]'];");
						if (myelement+''!='undefined'){
							len = myelement.length;
							found =0
							for (i = 0 ;i &lt; len; i++){
								if (myelement[i].checked == 1){
									found=1;
								}
							}
							if (found==0){
								alert("You have not filled in the '"+check_contains_selected[index][1]+"' field");
								show_tabular_screen(check_contains_selected[index][2]);
								myelement[0].focus();
								ok=0;
								break;
							}
						}
					}
				}		
				/*
					if there are no errors yet then check to see if there are any comparisons to be made
				*/
				if ((ok==1)&amp;&amp;(check_compair.length &gt; 0)){
					for(index=0;index &lt; check_compair.length ; index++){
						eval("value_to_be   = f."+check_compair[index][0][0]+".value;");
						eval("value_current = f."+check_compair[index][0][1]+".value;");
						if (value_to_be!=value_current){
							ok=0;
							alert("The Fields '"+check_compair[index][1][0]+"' and '"+check_compair[index][1][1]+"' do not match");
							show_tabular_screen(check_compair[index][2]);
							eval("f."+check_compair[index][0][0]+".focus();");
							break;
						}
					}		
					
				}
				/*
					Build dates if required
				*/
				for(index=0;index &lt; check_build_dates.length ; index++){
						eval("year_select	= f."+check_build_dates[index]+"_date_year;");
						eval("month_select	= f."+check_build_dates[index]+"_date_month;");
						eval("day_select	= f."+check_build_dates[index]+"_date_day;");
						eval("hour_select	= f."+check_build_dates[index]+"_date_hour;");

						my_year		= year_select.options[year_select.selectedIndex].value;
						my_month	= month_select.options[month_select.selectedIndex].value;
						my_day		= day_select.options[day_select.selectedIndex].value;
						my_hour		= hour_select.options[hour_select.selectedIndex].value;
						if (my_year.length &gt; 0){
							str = "f."+check_build_dates[index]+".value='"+my_year+"/"+my_month+"/"+my_day+" "+my_hour+":00:00';"
							eval(str);
						}
				}
				/*
					decide if the user has filled in the form correctly and submit.
				*/
				debug ("return type [" + return_type + "][" + ok + "]");
				if (return_type == 0){
					
					if (ok==1){
						if (tw_debug_on){
						
							if (confirm("transmit form information???")){
								f.submit();
							}
						} else {
							f.submit();
						}
					}
				} else {
					debug("return_type :: "+ return_type);
					if (return_type == 2){
						LIBERTAS_UpdateFields();
						if (ok==1){
							if (tw_debug_on){
								if (confirm("transmit form information???")){
									f.submit();
								}
							} else {
								f.submit();
							}
						}
					} else {
						return ok;
					}
				}
				
			}
			</xsl:when>
			<xsl:otherwise>function check_required_fields(rt){return true}</xsl:otherwise></xsl:choose>
			
		//</xsl:comment>
	</script>
	<script src='/libertas_images/javascripts/module_pages.js'></script>
	<script src='/libertas_images/javascripts/module.js'></script>
</xsl:if>
	<xsl:if test="//config">
		<script>
		var editor_configurations = Array(<xsl:for-each select="//xml_document/modules/module/form/editors/editor">Array(<xsl:value-of select="@identifier"/>,"<xsl:value-of select="." disable-output-escaping="yes"/>")<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
		</script>
	</xsl:if>
	<xsl:if test="//textarea[@type='RICH-TEXT']">
		<div id="divTemp" style="VISIBILITY: hidden; OVERFLOW: hidden; POSITION: absolute; WIDTH: 1px; HEIGHT: 1px"></div>
		<IFRAME id="tableWizard" style="Z-INDEX: 2; VISIBILITY: hidden; WIDTH: 10px; POSITION: absolute; HEIGHT: 10px" marginWidth="0" marginHeight="0" src="about:blank" scrolling="no"></IFRAME>
		<script>
		var wai_compliance = ('<xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='sp_wai_forms']"/>'=='Yes')?1:0;
		</script>
		<script language="JavaScript" src="/libertas_images/editor/libertas/js/editorfunctions.js"></script>
		<script language="JavaScript" src="/libertas_images/editor/libertas/js/toolbarfunctions.js"></script>
		<script language="JavaScript" src="/libertas_images/editor/libertas/js/menu.js"></script>
		<script language="JavaScript" src="/libertas_images/editor/libertas/js/simplebuttons.js"></script>
		<script language="JavaScript" src="/libertas_images/editor/libertas/js/grid.js"></script>
		<xsl:if test="//module[@name='editor']/setting/btn[@id='tables_basic'] or //module[@name='editor']/setting/btn[@id='tables_cell']">
			<script language="JavaScript" src="/libertas_images/editor/libertas/js/tablebasicfunctions.js"></script>
		</xsl:if>
		<xsl:if test="//module[@name='editor']/setting/btn[@id='tables_row_column']">
			<script language="JavaScript" src="/libertas_images/editor/libertas/js/rowfunctions.js"></script>
		</xsl:if>
		<xsl:if test="//module[@name='editor']/setting/btn[@id='tables_split_merge']">
			<script language="JavaScript" src="/libertas_images/editor/libertas/js/cellfunctions.js"></script>
		</xsl:if>
		<xsl:if test="//module[@name='editor']/setting/btn[@id='images']">
			<script language="JavaScript" src="/libertas_images/editor/libertas/js/imagefunctions.js"></script>
		</xsl:if>
		<xsl:if test="//module[@name='editor']/setting/btn[@id='headings'] or //module[@name='editor']/setting/btn[@id='font_face'] or //module[@name='editor']/setting/btn[@id='font_size']">
			<script language="JavaScript" src="/libertas_images/editor/libertas/js/fontfunctions.js"></script>
		</xsl:if>
		<xsl:if test="//module[@name='editor']/setting/btn[@id='tidy']">
			<script language="JavaScript" src="/libertas_images/editor/libertas/js/tidyfunctions.js"></script>
		</xsl:if>
		<xsl:choose>
			<xsl:when test="//module[@name='editor']/setting/btn[@id='context_sensitive']">
				<script src="/libertas_images/editor/libertas/js/contextsensitivebuttonfunctions.js"></script>
			</xsl:when>
			<xsl:otherwise>
				<script language="JavaScript" src="/libertas_images/editor/libertas/js/noncontextsensitivebuttonfunctions.js"></script>
			</xsl:otherwise>
		</xsl:choose>
		<xsl:if test="//module[@name='editor']/setting/btn[@id='embed_movie'] or //module[@name='editor']/setting/btn[@id='embed_flash'] or //module[@name='editor']/setting/btn[@id='embed_audio']">
			<script language="JavaScript" src="/libertas_images/editor/libertas/js/embedflashaudiomoviesfunctions.js"></script>
		</xsl:if>
		<xsl:if test="//module[@name='editor']/setting/btn[@id='paste_special']">
			<script language="JavaScript" src="/libertas_images/editor/libertas/js/pastespecialfunctions.js"></script>
		</xsl:if>
		<xsl:if test="//module[@name='editor']/setting/btn[@id='embed_form']">
			<script language="JavaScript" src="/libertas_images/editor/libertas/js/formbuilderfunctions.js"></script>
		</xsl:if>
		<xsl:if test="//module[@name='editor']/setting/btn[@id='internal_links'] or //module[@name='editor']/setting/btn[@id='file_links'] or //module[@name='editor']/setting/btn[@id='external_links'] or //module[@name='editor']/setting/btn[@id='email_links']">
			<script language="JavaScript" src="/libertas_images/editor/libertas/js/hyperlinkfunctions.js"></script>
		</xsl:if>
		<script language="JavaScript" src="/libertas_images/editor/libertas/js/extrafunctions.js"></script>
		<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/toolbar.css"/>		
		<iframe src="/libertas_images/editor/libertas/cache.php" width='100%' height='0' style='visibility:hidden' id='cache_data' name='cache_data'></iframe>
	</xsl:if>
	<form ><xsl:attribute name="action"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/></xsl:attribute>
	<xsl:if test="@name">
		<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
	</xsl:if>
	<xsl:if test="input[@type='file']">
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
	<xsl:for-each select="//input[@type='hidden']">
			<input type='hidden'>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose></xsl:attribute>
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
					<td valign="middle" align="center" class="buttonOff" style="height:100%"><xsl:attribute name="onclick"><xsl:choose>
						<xsl:when test='@onclick'>javascript:show_tabular_screen('tab_<xsl:value-of select="position()"/>');<xsl:value-of select="@onclick" disable-output-escaping="yes"/>(<xsl:for-each select="parameters/field">'<xsl:value-of select="."/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);</xsl:when>
						<xsl:otherwise>javascript:show_tabular_screen('tab_<xsl:value-of select="position()"/>');</xsl:otherwise>
					</xsl:choose></xsl:attribute><xsl:attribute name="id">btn_tab_<xsl:value-of select="position()"/></xsl:attribute>
						<xsl:attribute name="onMouseOver">javascript:this.style.cursor='hand';<xsl:value-of select="position()"/></xsl:attribute>
						<xsl:attribute name="name">tab_btn_<xsl:value-of select="position()"/></xsl:attribute>[[nbsp]]<xsl:value-of select="@label"/>[[nbsp]]</td>
					<td style="height:100%;width:1px;background:#333333" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="36" alt=""/></td>
					</tr></table></td>
					<xsl:if test="position()!=last()">
						<td style="height:100%;width:1px;border-bottom:1px solid #333333;background:#ffffff"><xsl:attribute name="id">section_button_<xsl:value-of select="@name"/>_spacer</xsl:attribute><img src="/libertas_images/themes/1x1.gif" border="0" width="2" height="36" alt=""/></td>
					</xsl:if>
				</xsl:for-each>
					<td width="100%" style="height:100%;width:100%;border-bottom:1px solid #333333;background:#ffffff"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="36" alt=""/></td>
				</tr>
			</table>
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
				<tr><td style="border-left:1px solid #999999;border-top:0px solid #ffffff;border-right:1px solid #333333;border-bottom:1px solid #333333;"><xsl:attribute name="colspan"><xsl:value-of select="(count(page_sections/section) * 2 ) -1 "/></xsl:attribute><xsl:for-each select="page_sections/section">
					<xsl:call-template name="displaySection"><xsl:with-param name="section_name">tab</xsl:with-param></xsl:call-template>
				</xsl:for-each></td></tr>
			</table>
			<script>
				var screen_tabs = Array(<xsl:for-each select="page_sections/section">'tab_<xsl:value-of select="position()"/>'<xsl:if test="section">,<xsl:variable name="pos"><xsl:value-of select="position()"/></xsl:variable><xsl:for-each select="section">'tab_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each></xsl:if><xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
				
				<xsl:variable name="tab">
				<xsl:for-each select="page_sections/section">
					<xsl:if test="@selected='true'">tab_<xsl:value-of select="position()"/></xsl:if>	
				</xsl:for-each>
				</xsl:variable>
				show_tabular_screen('<xsl:choose><xsl:when test="$tab!=''"><xsl:value-of select="$tab"/></xsl:when><xsl:otherwise>tab_1</xsl:otherwise></xsl:choose>');
			</script>
		</xsl:when>
	<xsl:otherwise>
		<xsl:call-template name="display_form_data"/>
	</xsl:otherwise>
	</xsl:choose>
	</form>
	<script src="/libertas_images/javascripts/module_retrieve.js"></script>
</xsl:template>
<xsl:template match="select">
		   	<td><select>
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
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:if test="option">
		   	<xsl:for-each select="option">
			   	<option><xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute><xsl:if test="@disabled='true'"><xsl:attribute name="style">color:#666666;</xsl:attribute></xsl:if><xsl:if test="@selected"><xsl:attribute name="selected"><xsl:value-of select="@selected"/></xsl:attribute></xsl:if><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="."/></xsl:with-param>
						</xsl:call-template></xsl:with-param></xsl:call-template></option>
		   	</xsl:for-each>
			</xsl:if>
		   	</select><xsl:if test="@other='true'">[[nbsp]]<input style="visibility:hidden" type='text' ><xsl:attribute name="name"><xsl:value-of select="@name"/>_extra</xsl:attribute></input></xsl:if></td>  		
</xsl:template>

<xsl:template match="checkboxes">
	<xsl:variable name="sort"><xsl:choose><xsl:when test="@sort=0">0</xsl:when><xsl:otherwise>1</xsl:otherwise></xsl:choose></xsl:variable>
	<xsl:if test=".//option">
		   	<td valign="top" colspan="2">
			<xsl:if test="@type='vertical' or not(@type)">
			<table width="100%" border="0" cellpadding="3" cellspacing="0">
			<xsl:choose>
			<xsl:when test="options">
			<xsl:for-each select="options">
				<xsl:sort select="@module" order="ascending"/>
				<tr>
					<td><xsl:if test ="@module"><b><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@module"/></xsl:call-template></b><br /></xsl:if>
					<xsl:choose>
						<xsl:when test="$sort=1"><xsl:for-each select="option">
							<xsl:sort select="@value"/>
						<input type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
							<xsl:attribute name="id"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
							<xsl:if test="@selected='true'">
								<xsl:attribute name="checked">true</xsl:attribute>
							</xsl:if>
				   			<xsl:if test="@disabled='true'">
								<xsl:attribute name="disabled">true</xsl:attribute>
							</xsl:if>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
				   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:call-template name="get_translation">
							<xsl:with-param name="check"><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:copy-of select="."/></xsl:with-param>
						</xsl:call-template></xsl:with-param></xsl:call-template><br /></label></xsl:for-each></xsl:when>
						<xsl:otherwise><xsl:for-each select="option">
						<input type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
							<xsl:attribute name="id"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
							<xsl:if test="@selected='true'">
								<xsl:attribute name="checked">true</xsl:attribute>
							</xsl:if>
				   			<xsl:if test="@disabled='true'">
								<xsl:attribute name="disabled">true</xsl:attribute>
							</xsl:if>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
				   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:call-template name="get_translation">
							<xsl:with-param name="check"><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:copy-of select="."/></xsl:with-param>
						</xsl:call-template></xsl:with-param>
						</xsl:call-template><br /></label></xsl:for-each></xsl:otherwise>
					</xsl:choose>
		   			</td>
		   		</tr>
		   	</xsl:for-each>
			</xsl:when><xsl:otherwise>
				<tr>
					<td><xsl:for-each select="option">
				<input type="checkbox">
				   	<xsl:attribute name="name"><xsl:value-of select="../@name"/>[]</xsl:attribute>
					<xsl:attribute name="id"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
					<xsl:if test="@selected='true'">
						<xsl:attribute name="checked">true</xsl:attribute>
					</xsl:if>
		   			<xsl:if test="@disabled='true'">
						<xsl:attribute name="disabled">true</xsl:attribute>
					</xsl:if>
		   			<xsl:if test="../../@onclick">
						<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
					</xsl:if>
		   		</input><label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:call-template name="get_translation">
							<xsl:with-param name="check"><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:copy-of select="."/></xsl:with-param>
						</xsl:call-template></xsl:with-param></xsl:call-template><br /></label>
			</xsl:for-each>
			</td></tr>
			</xsl:otherwise></xsl:choose>
			</table>
		   	</xsl:if>
		   	<xsl:if test="@type='horizontal' and options=true() ">
				<table width="100%" border="0" cellpadding="15" cellspacing="0">
		   		   	<tr>
						<td valign="top"><xsl:call-template name="display_checkbox_table">
						<xsl:with-param name="column" select="1"/>
						</xsl:call-template></td>
						<td valign="top"><xsl:call-template name="display_checkbox_table">
						<xsl:with-param name="column" select="2"/>
						</xsl:call-template></td>
						<td valign="top"><xsl:call-template name="display_checkbox_table">
							<xsl:with-param name="column" select="0"/>
						</xsl:call-template></td>
					</tr>
				</table>
				
			</xsl:if>
		   	<xsl:if test="@type='horizontal' and options=false() ">
				<xsl:call-template name="display_checkbox_table">
					<xsl:with-param name="total" select="3"/>
				</xsl:call-template>
				
			</xsl:if>
		   	</td>
	</xsl:if>
	
</xsl:template>

<xsl:template match="input">
	<xsl:choose>
  		<xsl:when test="@type='text'">
		   	<td><input type='text'><xsl:attribute name="value"><xsl:choose>
					<xsl:when test="value"><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="value"/></xsl:with-param>
						</xsl:call-template></xsl:when>
					<xsl:otherwise><xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="." /></xsl:with-param>
						</xsl:call-template></xsl:otherwise>
				</xsl:choose></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:if test="@size">
				<xsl:choose>
    				<xsl:when test="@size>80">
		   				<xsl:attribute name="style">width:550px</xsl:attribute>
		   			</xsl:when>
		   			<xsl:otherwise>
		   				<xsl:attribute name="style">width:<xsl:value-of select="@size*8"/>px</xsl:attribute>
			   			</xsl:otherwise>
		   		</xsl:choose>
		   		<xsl:attribute name="maxlength"><xsl:value-of select="@size"/></xsl:attribute>
		   	</xsl:if>
			</input>
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
				   	<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
		   			<xsl:attribute name="name"><xsl:value-of select="../@name"/>_<xsl:value-of select="@name"/></xsl:attribute>
			   		<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
			   		<xsl:if test="@checked='true'">
			   			<xsl:attribute name="checked"><xsl:value-of select="@checked"/></xsl:attribute>
		   			</xsl:if>
		   			<xsl:attribute name="onclick">javascript:document.all.file_upload_span_<xsl:value-of select="../@name"/>.style.visibility='<xsl:value-of select="@visibility"/>'</xsl:attribute>
			   		</input>
			   		<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template>
			   		</td><xsl:if test="position()=1 and  not(../@preview)"><td rowspan="2" align="right"><a target="_external_file"><xsl:attribute name="href">?command=FILES_DOWNLOAD&amp;download=<xsl:value-of select="../@tag"/><xsl:value-of select="../@value"/></xsl:attribute><img src="/libertas_images/themes/site_administration/button_VIEW.gif" border="0"><xsl:attribute name="alt">
					<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'VIEW_FILE'"/></xsl:call-template>
					</xsl:attribute></img></a></td></xsl:if></tr>
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
		   	</input></td>
  		
  		</xsl:when>
  		<xsl:when test="@type='date_time'">
		   	<td>
			<input type='hidden'>
				<xsl:attribute name='name'><xsl:value-of select='@name'/></xsl:attribute>
				<xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose></xsl:attribute>
			</input>
			<xsl:variable name="year"><xsl:value-of select="substring-before(@value,'-')"/></xsl:variable>
			<xsl:variable name="month"><xsl:value-of select="substring-before(substring-after(@value,'-'),'-')"/></xsl:variable>
			<xsl:variable name="day"><xsl:value-of select="substring-before(substring-after(substring-after(@value,'-'),'-'),' ')"/></xsl:variable>
			<xsl:variable name="hour"><xsl:value-of select="substring-before(substring-after(@value,' '),':')"/></xsl:variable>
			<xsl:variable name="year_start"><xsl:choose><xsl:when test="@year_start"><xsl:value-of select="@year_start"/></xsl:when><xsl:otherwise><xsl:value-of select="//setting[@name='year'] - 1"/></xsl:otherwise></xsl:choose></xsl:variable>
			<xsl:variable name="year_end"><xsl:choose><xsl:when test="@year_start"><xsl:value-of select="@year_end"/></xsl:when><xsl:otherwise><xsl:value-of select="//setting[@name='year'] + 1"/></xsl:otherwise></xsl:choose></xsl:variable>
			<select><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_year</xsl:attribute>
				<option value=''></option>
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$year"/>
			   		<xsl:with-param name="start" select="$year_start"/>
			   		<xsl:with-param name="end" select="$year_end"/>
			   		<xsl:with-param name="type" select="year"/>
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
			<select><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_day</xsl:attribute>
				<option value=''></option>
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$day"/>
			   		<xsl:with-param name="start" select="1"/>
			   		<xsl:with-param name="end" select="31"/>
			   		<xsl:with-param name="type" select="day"/>
			 	</xsl:call-template>
			</select>
			<select><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_hour</xsl:attribute>
				<option value=''></option>
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$hour"/>
			   		<xsl:with-param name="start" select="0"/>
			   		<xsl:with-param name="end" select="24"/>
			   		<xsl:with-param name="type" >hour</xsl:with-param>
			 	</xsl:call-template>
			</select>
			</td>
  		</xsl:when>
	</xsl:choose>
</xsl:template>

<xsl:template name="display_date">
	<xsl:param name="current"/>
	<xsl:param name="start"/>
	<xsl:param name="end"/>
	<xsl:param name="type"/>
	<option ><xsl:if test="number($current) = ($start)"><xsl:attribute name="selected">true</xsl:attribute></xsl:if><xsl:attribute name="value"><xsl:value-of select="$start"/></xsl:attribute><xsl:value-of select="$start"/><xsl:if test="$type='hour'">:00</xsl:if></option>
	<xsl:if test="$start != $end">
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$current"/>
			   		<xsl:with-param name="start" select="$start + 1"/>
			   		<xsl:with-param name="end" select="$end"/>
			   		<xsl:with-param name="type" select="$type"/>
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
			<xsl:when test="@iconify='PREVIEW'"><xsl:choose><xsl:when test="../../@display='form'">javascript:preview_from_form(<xsl:choose>
					<xsl:when test="../../form/input/@name[.='page_identifier']/@value=''">-1,'page',document.<xsl:value-of select="../../form/@name" />,'PAGE_PREVIEW_FORM'</xsl:when>
					<xsl:otherwise><xsl:value-of select="../../form/input[@name='page_identifier']" />,'page',document.<xsl:value-of select="../../form/@name" />,'PAGE_PREVIEW_FORM'</xsl:otherwise>
					</xsl:choose>);</xsl:when>
				<xsl:when test="../../@display='results'">javascript:preview_from_list(<xsl:value-of select="../../form/input/@name[.='page_identifier']/@value"/>,'page');</xsl:when>
					<xsl:otherwise>admin/preview.php?command=PAGE_PREVIEW&amp;<xsl:value-of select="@parameters" /></xsl:otherwise>
				</xsl:choose></xsl:when>
				<xsl:when test="@command='GENERAL_BACK'">javascript:history.back();</xsl:when>
				<xsl:otherwise>admin/index.php?command=<xsl:value-of select="@command"/>&amp;<xsl:if test="@parameters"><xsl:value-of select="@parameters"/></xsl:if></xsl:otherwise>
			</xsl:choose></xsl:attribute><xsl:call-template name="display_icon"/></a>
		</xsl:for-each>
	</xsl:if>
</xsl:template>

<xsl:template match="text">
	<td valign="top"><xsl:choose><xsl:when test="@label"></xsl:when><xsl:otherwise><xsl:attribute name="colspan">2</xsl:attribute></xsl:otherwise></xsl:choose>
	<xsl:if test="@type"><xsl:attribute name="class">error</xsl:attribute></xsl:if>
	<xsl:choose>
		<xsl:when test="@name='trans_menu_location'">
		</xsl:when>
		<xsl:when test="@name='trans_group_information'">
			<span><xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute><ul>
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
			<span><xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:call-template name="get_translation">
				<xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param>
			</xsl:call-template>
			</span>
		</xsl:otherwise>
	</xsl:choose>
	</td>
</xsl:template>


</xsl:stylesheet>

