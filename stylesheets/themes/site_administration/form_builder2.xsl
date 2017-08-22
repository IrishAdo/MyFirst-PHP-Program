<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:49:54 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 


<xsl:template match="data">
</xsl:template>

<xsl:template match="form_builder">
<xsl:if test="@name!='USERS_SHOW_LOGIN'">
	<script>
		<xsl:comment>
			var session_url = '<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>';
			var domain		= "<xsl:value-of select="//xml_document/modules/module[@name='system_prefs']/setting[@name='domain']"/>";
			var base_href 	= "<xsl:value-of select="//xml_document/modules/module[@name='system_prefs']/setting[@name='base']"/>";
			var xcheck_required = Array(<xsl:for-each select="//input[@required='YES']">Array('<xsl:value-of select="@name"/>','tab')<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);

			var check_required = Array();
			var check_contains_selected = Array();
			var check_compair = Array();
			var check_editors = Array();


			var check_build_dates = Array(<xsl:for-each select="//input[@type='date_time']">'<xsl:value-of select="@name"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);

			var check_not_allowed = Array(<xsl:for-each select="//select/option[@disabled='true']">'<xsl:value-of select="@value"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);

			<xsl:value-of select="../javascript" disable-output-escaping="yes" />

			function get_form(){
				return document.<xsl:value-of select="@name"/>;
			}
			/*
				if the form has any required fields then execute this function
			*/
			function show_tabular_screen(tab){
				for(i=0;i &lt; screen_tabs.length;i++){
					if (tab==screen_tabs[i]){
						eval("document.all." + screen_tabs[i] + ".style.visibility='visible';");
						eval("document.all." + screen_tabs[i] + ".style.display='';");
						eval("document.all.btn_" + screen_tabs[i] + ".className='buttonOn';");
					} else {
						eval("document.all." + screen_tabs[i] + ".style.visibility='hidden';");
						eval("document.all." + screen_tabs[i] + ".style.display='NONE';");
						eval("document.all.btn_" + screen_tabs[i] + ".className='buttonOff';");
					}
				}
			}

			function check_required_fields(rt){
				var return_type=1;
				var f = document.<xsl:value-of select="@name"/>;
				if ((rt+"" == "undefined") || (rt+"" == "0")){
					return_type = 0;
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
				if (return_type==0){
					if (ok==1){
						f.submit();
					}
				} else {
					return ok;
				}
				
			}
		//</xsl:comment>
	</script>
	<script src='/libertas_images/javascripts/module_pages.js'></script>
	<script src='/libertas_images/javascripts/module.js'></script>
</xsl:if>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td><table width="100%" cellspacing="0" cellpadding="0">
				<tr>
				<td style="width:1px;height:1px;background:#ffffff"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
				<td valign="middle" align="center" style="height:1px;background:#999999"><img src="/libertas_images/themes/1x1.gif" border="0" width="150" height="1" alt=""/></td>
				<td style="width:1px;height:1px;background:#ffffff" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
				</tr>
				<tr>
				<td style="width:1px;background:#999999"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="25" alt=""/></td>
				<td valign="middle" align="center" class="buttonOff" onclick="javascript:show_tabular_screen('tab_1');" id="btn_tab_1" name="btn_tab_1">Form Manager</td>
				<td style="width:1px;background:#333333" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="25" alt=""/></td>
				</tr></table></td>
				<td style="width:1px;border-bottom:1px solid #333333;background:#ffffff"><img src="/libertas_images/themes/1x1.gif" border="0" width="2" height="25" alt=""/></td>
				<td><table width="100%" cellspacing="0" cellpadding="0">
				<tr>
				<td style="width:1px;height:1px;background:#ffffff"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
				<td valign="middle" align="center" style="height:1px;background:#999999"><img src="/libertas_images/themes/1x1.gif" border="0" width="150" height="1" alt=""/></td>
				<td style="width:1px;height:1px;background:#ffffff" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
				</tr>
				<tr>
				<td style="width:1px;background:#999999"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="25" alt=""/></td>
				<td valign="middle" align="center" class="buttonOff" onclick="javascript:show_tabular_screen('tab_2');" id="btn_tab_2" name="btn_tab_2">Field Manager</td>
				<td style="width:1px;background:#333333" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="25" alt=""/></td>
				</tr></table></td>
				<td style="width:1px;border-bottom:1px solid #333333;background:#ffffff"><img src="/libertas_images/themes/1x1.gif" border="0" width="2" height="25" alt=""/></td>
				<td><table width="100%" cellspacing="0" cellpadding="0">
				<tr>
				<td style="width:1px;height:1px;background:#ffffff"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
				<td valign="middle" align="center" style="height:1px;background:#999999"><img src="/libertas_images/themes/1x1.gif" border="0" width="150" height="1" alt=""/></td>
				<td style="width:1px;height:1px;background:#ffffff" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
				</tr>
				<tr>
				<td style="width:1px;background:#999999"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="25" alt=""/></td>
				<td valign="middle" align="center" class="buttonOff" onclick="javascript:show_tabular_screen('tab_3');" id="btn_tab_3" name="btn_tab_3">Preview</td>
				<td style="width:1px;background:#333333" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="25" alt=""/></td>
				</tr></table></td>
				<td style="width:1px;border-bottom:1px solid #333333;background:#ffffff"><img src="/libertas_images/themes/1x1.gif" border="0" width="2" height="25" alt=""/></td>
				<td><table width="100%" cellspacing="0" cellpadding="0">
				<tr>
				<td style="width:1px;height:1px;background:#ffffff"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
				<td valign="middle" align="center" style="height:1px;background:#999999"><img src="/libertas_images/themes/1x1.gif" border="0" width="150" height="1" alt=""/></td>
				<td style="width:1px;height:1px;background:#ffffff" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
				</tr>
				<tr>
				<td style="width:1px;background:#999999"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="25" alt=""/></td>
				<td valign="middle" align="center" class="buttonOff" onclick="javascript:show_tabular_screen('tab_4');" id="btn_tab_4" name="btn_tab_4">Confirm Screen</td>
				<td style="width:1px;background:#333333" ><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="25" alt=""/></td>
				</tr></table></td>
				<td width="100%" style="border-bottom:1px solid #000000; background:#ffffff"><img src="/libertas_images/themes/1x1.gif" border="0" width="1" height="1" alt=""/></td>
			</tr>
			<tr><td colspan="8" style="border-left:1px solid #999999;border-top:0px solid #ffffff;border-right:1px solid #333333;border-bottom:1px solid #333333;">
			<table><tr><td>
			<table cellspacing="5"><tr><td>
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
<form method="post"><xsl:attribute name="name"><xsl:value-of select="form_data/@name"/></xsl:attribute>
<span id="tab_1" name="tab_1">
<input type='hidden' name='identifier'><xsl:attribute name='value'><xsl:value-of select="@identifier"/></xsl:attribute></input>
<input type='hidden' name='command' value='SFORM_FORM_BUILDER_SUBMIT' />
<input type='hidden' name='xml_representation' />
<input type='hidden' name='group_fields' />
<input type='hidden' name='required_fields' />
<input type='hidden' name='email_list' />
<input type='hidden' name='destination_url' />
	<xsl:for-each select="form_data/input[@type='hidden']">
			<input type='hidden'>
			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:choose>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
			</xsl:choose></xsl:attribute>
		   	</input>
	</xsl:for-each>
	<xsl:for-each select="form_data">
		<table><xsl:call-template name="display_form_data"><xsl:with-param name="ignore">textarea</xsl:with-param></xsl:call-template></table>
	</xsl:for-each>
<table>
<tr><td valign="top"><span id="list_of_emails"></span></td></tr>
</table>
			</span>
			<span id="tab_2" name="tab_2"><span id='wizard'></span></span>
			<span id="tab_3" name="tab_3"><span id='myform'></span></span>
			<span id="tab_4" name="tab_4"><span id='confirmscreen'><xsl:apply-templates select="form_data/textarea"/></span></span>
</form>
			</td></tr>
		</table>
			</td></tr>
		</table>
			</td></tr>
		</table>
		
<script>
			var session_url = '<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>';
			var screen_tabs = Array('tab_1', 'tab_2', 'tab_3', 'tab_4');
			
			function show_tabular_screen(tab){
				for(i=0 ; i &lt; screen_tabs.length ; i++){
					if (tab==screen_tabs[i]){
						eval("document.all." + screen_tabs[i] + ".style.visibility='visible';");
						eval("document.all." + screen_tabs[i] + ".style.display='';");
						eval("document.all.btn_" + screen_tabs[i] + ".className='buttonOn';");
						
					} else {
						eval("document.all." + screen_tabs[i] + ".style.visibility='hidden';");
						eval("document.all." + screen_tabs[i] + ".style.display='NONE';");
						eval("document.all.btn_" + screen_tabs[i] + ".className='buttonOff';");
					}
				}
				if (tab=="tab_1"){
					document.all.list_of_emails.style.display='';
					document.all.list_of_emails.style.visibility='visible';
				} else {
					document.all.list_of_emails.style.display='none';
					document.all.list_of_emails.style.visibility='hidden';
				}

			}
			
			<xsl:variable name="tab">
				<xsl:for-each select="page_sections/section">
					<xsl:if test="@selected='true'">tab_<xsl:value-of select="position()"/></xsl:if>	
				</xsl:for-each>
			</xsl:variable>
			show_tabular_screen('tab_1');
		</script>

<!--<hr/>
<img src="/libertas_images/themes/site_administration/button_SAVE.gif" onclick=""/>
-->
<script src="/libertas_images/editor/formbuilder/locale_en.js"></script>
<script src="/libertas_images/editor/formbuilder/formbuilder.js"></script>
<script>
<xsl:comment>
var buildform  = new form_data();
	buildform.form_identifier = "<xsl:value-of select="@identifier"/>";
	buildform.form_emails	=Array(<xsl:for-each select="form_data/data/email">
		new Array("<xsl:value-of select="subject" disable-output-escaping="yes"/>","<xsl:value-of select="address" disable-output-escaping="yes"/>",<xsl:choose>
			<xsl:when test="@selected='true'">true</xsl:when>
			<xsl:otherwise>false</xsl:otherwise>
		</xsl:choose>)
		<xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>);
	buildform.form_url				= "<xsl:value-of select="form_data/url" disable-output-escaping="yes"/>";
	buildform.form_method			= "<xsl:value-of select="form_data/method" disable-output-escaping="yes"/>";
	buildform.form_confirm_screen	= "<xsl:value-of select="form_data/confirm_screen" disable-output-escaping="yes"/>";
	buildform.form_action	= "<xsl:value-of select="form_data/radio/option[@selected='true']/@value"/>";
	<xsl:for-each select="fields">
		<xsl:for-each select="seperator">
			<xsl:for-each select="child::*">
				<xsl:choose>
				<xsl:when test="@type='hidden' and @name='number_of_fields' ">
				</xsl:when>
				<xsl:when test="@type='hidden' and @name!='number_of_fields' ">
					property = new Array();
					property["width"]="<xsl:value-of select="@size"/>";
					buildform.add("<xsl:value-of select="@name"/>","hidden","hidden", property, false, null, null, false,false,'<xsl:choose><xsl:when test="@value"><xsl:value-of select="@value" disable-output-escaping="yes"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose>');
				</xsl:when>
				<xsl:when test="local-name()='text'">
					property = new Array();
					buildform.add("","cdata",jtidy("<xsl:value-of select="."/>"), property, false, null, null, false, false, '');
				</xsl:when>
				<xsl:when test="@type='text'">
					property = new Array();
					property["width"]="<xsl:value-of select="@size"/>";
					buildform.add("<xsl:value-of select="@name"/>","<xsl:value-of select="@type"/>",jtidy("<xsl:value-of select="@label"/>"), property, false, null, null, 
					<xsl:choose>
						<xsl:when test="@required='true'">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>, 
					<xsl:choose>
						<xsl:when test="@session_hide='true'">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>,'');
				</xsl:when>
				<xsl:when test="local-name()='textarea'">
					property = new Array();
					property["width"]=<xsl:value-of select="@size"/>;
					property["height"]=<xsl:value-of select="@height"/>;
					buildform.add("<xsl:value-of select="@name"/>","textarea",jtidy("<xsl:value-of select="@label"/>"), property, false, null, null, 
					<xsl:choose>
						<xsl:when test="@required='true'">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>, 
					<xsl:choose>
						<xsl:when test="@session_hide='true'">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>,'');
	
				</xsl:when>
				<xsl:when test="local-name()='radio'">
					property = new Array();
					<xsl:for-each select="option">
						property[property.length] = new Array(
							"<xsl:value-of select="." disable-output-escaping="yes"/>",
							jtidy("<xsl:value-of select="@value" disable-output-escaping="yes"/>"),
							<xsl:choose>
								<xsl:when test="@checked='true'">true</xsl:when>
								<xsl:otherwise>false</xsl:otherwise>
							</xsl:choose>
						);
					</xsl:for-each>
					buildform.add("<xsl:value-of select="@name"/>","radio",jtidy("<xsl:value-of select="@label"/>"), property, true, <xsl:choose>
					<xsl:when test="@type='vertical'">"vertical"</xsl:when>
					<xsl:otherwise>"horizontal"</xsl:otherwise>
					</xsl:choose>, null, 
					<xsl:choose>
						<xsl:when test="@required='true'">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>, 
					<xsl:choose>
						<xsl:when test="@session_hide='true'">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>,'');
	
				</xsl:when>
				<xsl:when test="local-name()='checkboxes'">
					property = new Array();
					<xsl:for-each select="option">
						property[property.length] = new Array(
							"<xsl:value-of select="." disable-output-escaping="yes"/>",
							jtidy("<xsl:value-of select="@value" disable-output-escaping="yes"/>"),
							<xsl:choose>
								<xsl:when test="@checked='true'">true</xsl:when>
								<xsl:otherwise>false</xsl:otherwise>
							</xsl:choose>
						);
					</xsl:for-each>
					buildform.add("<xsl:value-of select="@name"/>","checkbox",jtidy("<xsl:value-of select="@label"/>"), property, true, <xsl:choose>
					<xsl:when test="@type='vertical'">"vertical"</xsl:when>
					<xsl:otherwise>"horizontal"</xsl:otherwise>
					</xsl:choose>, null, 
					<xsl:choose>
						<xsl:when test="@required='true'">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>, 
					<xsl:choose>
						<xsl:when test="@session_hide='true'">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>,'');
	
				</xsl:when>
				<xsl:when test="local-name()='select'">
					property = new Array();
					<xsl:for-each select="option">
						property[property.length] = new Array(
							"<xsl:value-of select="." disable-output-escaping="yes"/>",
							jtidy("<xsl:value-of select="@value" disable-output-escaping="yes"/>"),
							<xsl:choose>
								<xsl:when test="@checked='true'">true</xsl:when>
								<xsl:otherwise>false</xsl:otherwise>
							</xsl:choose>
						);
					</xsl:for-each>
					buildform.add("<xsl:value-of select="@name"/>","select",jtidy("<xsl:value-of select="@label"/>"), property, true, <xsl:choose>
					<xsl:when test="@type='vertical'">"vertical"</xsl:when>
					<xsl:otherwise>"horizontal"</xsl:otherwise>
					</xsl:choose>, null, 
					<xsl:choose>
						<xsl:when test="@required='true'">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>, 
					<xsl:choose>
						<xsl:when test="@session_hide='true'">true</xsl:when>
						<xsl:otherwise>false</xsl:otherwise>
					</xsl:choose>,'');
				</xsl:when>
				<xsl:otherwise>
					alert('Missing <xsl:value-of select="local-name()"/>');
				</xsl:otherwise>
				</xsl:choose>
			</xsl:for-each>
			<xsl:if test="position()!=last()">
					property = new Array();
					buildform.add("<xsl:value-of select="@name"/>","splitter","", property, false, "null", "null", false,false,'');
			</xsl:if>
		</xsl:for-each>
	</xsl:for-each>
	buildform.build();
	if (buildform.form_emails.length>0 || buildform.form_action ==0 || buildform.form_action==1){
		buildform.show_email();
	}
	if (buildform.form_url.length>0 || buildform.form_action ==3){
		buildform.show_url();
	}
	xml_destination = user_form.xml_representation;
var page =0;
var field =null
next_page(page);
</xsl:comment>
</script>

</xsl:template>
</xsl:stylesheet>
