<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/10/02 12:26:52 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:include href="../../themes/site_administration/editor.xsl"/>
  
<xsl:template match="form">
	<script>
		<xsl:comment>
			var check_required 			= new Array(<xsl:for-each select="//input[@required='YES']"> new Array('<xsl:value-of select="@name"/>','tab')<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
			var has_editor				= <xsl:choose><xsl:when test="//textarea[@type='RICH-TEXT']">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>;
			var check_required			= new Array();
			var check_contains_selected = new Array();
			var check_compair 			= new Array();
			var check_editors 			= new Array();
			var hidden_list 			= new Array();
			var objects_to_check		= new Array();
			var product_version = '<xsl:value-of select="//xml_document/modules/module/licence/product/@type"/>';
			var browserstring = '<xsl:value-of select="//xml_document/modules/module[@name='system_prefs']/setting[@name='browser']"/>';
			var info = browserstring.substring(browserstring.indexOf("MSIE")).split(";");
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
				</xsl:otherwise>
			</xsl:choose>


			
		//</xsl:comment>
	</script>
	<xsl:choose>
		<xsl:when test="@name">
			<script src='/libertas_images/javascripts/checkform.js'></script>
		</xsl:when>
		<xsl:otherwise>
			<SCRIPT>
				function check_required_fields(rt){return true}
			</SCRIPT>
		</xsl:otherwise>
	</xsl:choose>
	<xsl:if test="//textarea[@type='RICH-TEXT']">
	<script>
		var wai_compliance = ('<xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='sp_wai_forms']"/>'=='Yes')?1:0;
	</script>
	<script language="JavaScript" src="/libertas_images/editor/libertas/menu.js"></script>
	<script language="JavaScript" src="/libertas_images/editor/libertas/script.js"></script>
	<script language="JavaScript" src="/libertas_images/editor/libertas/toolbar.js"></script>
	<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/toolbar.css"/>		
	</xsl:if>
	<xsl:if test="//textarea[@type='RICH-TEXT'] or //loadcache">
	<iframe src="/libertas_images/editor/libertas/cache.html" width='100%' height='0' style='visibility:hidden' id='cache_data' name='cache_data'></iframe>
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
	<xsl:for-each select="//input">
		<xsl:if test="@type='hidden'">
			<xsl:comment> <xsl:value-of select="@name"/></xsl:comment>
			<input type='hidden'>
		   	<xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	</input>
		   	
		</xsl:if>
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
					<xsl:attribute name="onclick"><xsl:choose>
						<xsl:when test='@onclick'>javascript:show_tabular_screen('tab_<xsl:value-of select="position()"/>');<xsl:value-of select="@onclick" disable-output-escaping="yes"/>(<xsl:for-each select="parameters/field">'<xsl:value-of select="."/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);</xsl:when>
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
				var screen_tabs = new Array(<xsl:for-each select="page_sections/section">'tab_<xsl:value-of select="position()"/>'<xsl:if test="section">,<xsl:variable name="pos"><xsl:value-of select="position()"/></xsl:variable><xsl:for-each select="section">'tab_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each></xsl:if><xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
				<xsl:variable name="hidetabs"><xsl:for-each select="page_sections/section[@hidden='true']">"section_button_<xsl:value-of select="@name"/>"<xsl:if test="position()!=last()">, </xsl:if></xsl:for-each></xsl:variable>
				<xsl:variable name="tab">
				<xsl:for-each select="page_sections/section">
					<xsl:if test="@selected='true'">tab_<xsl:value-of select="position()"/></xsl:if>	
				</xsl:for-each>
				</xsl:variable>
				hiddenlist = new Array(<xsl:value-of select="$hidetabs" />)
				show_tabular_screen('<xsl:choose><xsl:when test="$tab!=''"><xsl:value-of select="$tab"/></xsl:when><xsl:otherwise>tab_1</xsl:otherwise></xsl:choose>', hiddenlist);
			</script>
		</xsl:when>
	<xsl:otherwise>
	<center><table border="0">
		<tr><td colspan="2" class="formHeader">Filter</td></tr>
		<xsl:apply-templates name="/"><xsl:with-param name="show_label">1</xsl:with-param></xsl:apply-templates>
		<tr><td colspan="2" ><input type="submit" class="bt"><xsl:attribute name="value"><xsl:value-of select="//input[@type='submit']/@value"/></xsl:attribute></input></td></tr>
		</table></center>
	</xsl:otherwise>
	</xsl:choose>
<!--
			$variables["PAGE_BUTTONS"] = Array(
				Array("ADD",$this->module_command."ADD",ADD_NEW,"ds","as","as","as")
			);

	<center><table border="0" cellpadding="0" cellspacing="0" summary="This table holds a form">	
	<xsl:if test="@width">
		<xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute>
	</xsl:if>

		<tr> 
		   	<td valign="top" class="formbackground"><table border="0" cellpadding="3" cellspacing="1" width="100%" summary="This table holds the row information for the forms">
		<tr> 
		   	<td valign="top" colspan="2" class="formheader"><b><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></b></td>
  		</tr>
		<xsl:apply-templates>
			<xsl:with-param name="display_label">1</xsl:with-param>
		</xsl:apply-templates>
	   	<tr class="TableCell"> 
		   	<td align="right" valign="top" colspan="2"><xsl:for-each select="input">
			<xsl:if test="@type='submit'"><input type='image' border="0">
		   	<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
		   	<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	</input>[[nbsp]]</xsl:if>
			<xsl:if test="@type='button'">
			<a><xsl:attribute name="href"><xsl:choose>
					<xsl:when test="@command='BACK'">javascript:history.back();</xsl:when>
					<xsl:when test="../../@name='group'">javascript:save_to_doc(list_of_groups);</xsl:when>
					<xsl:otherwise>javascript:button_action('<xsl:value-of select="@command"/>');</xsl:otherwise>
				</xsl:choose>
			</xsl:attribute>
			<img border="0">
		   	<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
		   	<xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:attribute>
		   	<xsl:attribute name="id"><xsl:value-of select="@command"/></xsl:attribute>
		   	</img></a>[[nbsp]]</xsl:if>
		   	</xsl:for-each></td>
  		</tr>
	</table></td></tr></table></center>
-->	</form>
</xsl:template>


<xsl:template match="select">
<xsl:param name="show_label">0</xsl:param>
		<tr class="TableCell">
			<xsl:if test="$show_label=1">
			   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></td>
			</xsl:if> 
		   	<td><select>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute><xsl:for-each select="option"><option><xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose></xsl:attribute><xsl:if test="@selected"><xsl:attribute name="selected"><xsl:value-of select="@selected"/></xsl:attribute></xsl:if><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></option></xsl:for-each>
		   	</select></td>
  		</tr>
</xsl:template>

<xsl:template match="checkboxes">
		<tr class="TableCell">
<td valign="top" colspan="2">
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template><br />
		   	<xsl:if test="@type='vertical'">
			<table width="100%" border="0" cellpadding="3" cellspacing="0">
			<xsl:for-each select="options">
			   	<tr>
					<td><b><xsl:value-of select="@module"/></b><br />
					<xsl:for-each select="option"><input type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose></xsl:attribute>
							<xsl:if test="@selected='true'">
								<xsl:attribute name="checked">true</xsl:attribute>
							</xsl:if>
				   			<xsl:if test="@disabled='true'">
								<xsl:attribute name="disabled">true</xsl:attribute>
							</xsl:if>

				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
				   		</input>
						<label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
						<xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template>
						</label><br />
		   			</xsl:for-each>
		   			</td>
		   		</tr>
		   		
		   		</xsl:for-each></table>
		   	</xsl:if>
		   	<xsl:if test="@type='horizontal'">
	   		   	<xsl:for-each select="options">
	   		   	<table width="100%" border="0" cellpadding="0" cellspacing="0">
	   		   	<xsl:if test="position()=0">
	   		   	<tr><td colspan="3"><b><xsl:value-of select="@module"/></b></td></tr>
	   		   	</xsl:if>
	   		   	<tr><td>
				<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<xsl:for-each select="option">
					<tr>
						<td><input type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
							<xsl:attribute name="value"><xsl:choose>
								<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:if test="@selected='true'">
								<xsl:attribute name="checked">true</xsl:attribute>
							</xsl:if>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
				   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
						<xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template>
						</label></td>
					</tr>
					</xsl:for-each>
		   	</table>
		   	</td>
  		</tr></table>
		   		</xsl:for-each>
			</xsl:if>
		   	</td>
  		</tr>
</xsl:template>


<xsl:template match="textarea">
	<tr>
		<td colspan="2">
			<textarea class="none">
				<xsl:if test="@nowrap"><xsl:attribute name="nowrap">true</xsl:attribute></xsl:if>
	   			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
	   			<xsl:attribute name="size"><xsl:value-of select="@size"/></xsl:attribute>
				<xsl:attribute name="rows"><xsl:value-of select="@height"/></xsl:attribute>
				<xsl:value-of select="." disable-output-escaping="yes"/>
		   	</textarea>
		</td>
	</tr>
</xsl:template>


<xsl:template match="input">
<xsl:param name="show_label">0</xsl:param>
	<xsl:choose>
  		<xsl:when test="@type='text'">
    	<tr class="TableCell"> 
			<xsl:if test="$show_label=1">
			   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></td>
			</xsl:if> 
		   	<td><input type='text'>
		   	<xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	
		   	</input>
		   	</td>
  		</tr>
  		
  		</xsl:when>
  		<xsl:when test="@type='file'">
    	<tr class="TableCell"> 
<!--		   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></td> -->
		   	<td>
		   	<xsl:if test="@value!=''">
		   	<xsl:value-of select="@value"/> (<xsl:value-of select="@file_size"/>)bytes
		   	<xsl:if test="./choice">
		   	<table>
		   		<xsl:for-each select="choice">
		   		<tr><td><input type="radio">
		   		<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   		<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
		   		<xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose></xsl:attribute>
		   		<xsl:if test="@checked='true'">
		   			<xsl:attribute name="checked"><xsl:value-of select="@checked"/></xsl:attribute>
		   		</xsl:if>
		   		<xsl:attribute name="onclick">javascript:document.all.file_upload_span.style.visibility='<xsl:value-of select="@visibility"/>'</xsl:attribute>
		   		</input>
		   		<label><xsl:attribute name="for"><xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="position()"/></xsl:attribute></xsl:attribute>
				<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label>
		   		</td></tr>
		   		</xsl:for-each>
		   	</table>
		   	</xsl:if>
		   	</xsl:if>
		   	<span id="file_upload_span"><xsl:if test="@value">
		   	<xsl:if test="./choice">
		   	<xsl:attribute name="style">visibility:hidden</xsl:attribute>
		   	</xsl:if>
		   	</xsl:if>
		   	<input type='file'>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	</input></span>
		   	</td>
  		</tr>
  		
  		</xsl:when>
  		<xsl:when test="@type='password'">
    	<tr class="TableCell"> 
<!--		   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></td> -->
		   	<td><input type='password'>
		   	<xsl:attribute name="value"><xsl:choose><xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="."/></xsl:otherwise></xsl:choose></xsl:attribute>
		   	<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
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
  		</tr>
  		
  		</xsl:when>
	</xsl:choose>
</xsl:template>



<xsl:template match="page_options">
	<xsl:for-each select="button">
	<xsl:value-of select="//form/input[@name='page_identifier']/@name"/>
		<a><xsl:attribute name="href">
		<xsl:choose>
		<xsl:when test="@command='FILES_ASSOCIATE_FILES'">javascript:file_associate(<xsl:value-of select="../../form/input[@name='page_identifier']/@value" />);</xsl:when>
		<xsl:when test="@command='PAGE_PREVIEW'">javascript:preview(<xsl:value-of select="../../form/input[@name='page_identifier']/@value" />,'page');</xsl:when>
		<xsl:when test="@command='FILES_ADD'">javascript:file_add();</xsl:when>
		<xsl:when test="@command='GENERAL_BACK'">javascript:history.back();</xsl:when>
		<xsl:otherwise>admin/file_associate.php?command=<xsl:value-of select="@command"/>&amp;<xsl:value-of select="@parameter"/></xsl:otherwise>
		</xsl:choose></xsl:attribute>
			<img border="0">
				<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
				<xsl:attribute name="alt"><xsl:value-of select="@img"/></xsl:attribute>
			</img>
		</a>
	</xsl:for-each>
	<a><xsl:attribute name="href">javascript:return_to_doc('<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>');</xsl:attribute><img border="0" alt="Save Associations"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_SAVE.gif</xsl:attribute></img></a>
</xsl:template>

<xsl:template match="text">
	<tr><td valign="top" colspan="2" class="TableCell"><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template></td></tr>
</xsl:template>



</xsl:stylesheet>