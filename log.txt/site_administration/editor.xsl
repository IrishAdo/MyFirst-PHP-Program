<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2005/01/11 16:27:35 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:template match="textarea">
	<xsl:param name="display_label">0</xsl:param>
	<xsl:param name="span_name"></xsl:param>
	<xsl:if test="$display_label=1"><td valign="top" class="TableCell"><label><xsl:value-of select="@label"/></label></td></xsl:if>
	<td class="TableCell"><xsl:if test="not(@label)"><xsl:attribute name="colspan">2</xsl:attribute></xsl:if>
	 	<xsl:choose>	
	   	   	<xsl:when test="@type='FIELD-TEXT'">
				<table border="0" class="width100percent"><tr><td valign="top">
				<script>
					<xsl:comment>
						var user_form = get_form();
					<xsl:if test="//textarea[@required='YES']">
						check_editors[check_editors.length] = Array('<xsl:value-of select="@name"/>', '<xsl:value-of select="@label"/>', '<xsl:value-of select="$span_name"/>');
					</xsl:if>
					//</xsl:comment>
				</script>
				<xsl:variable name="configuration_type"><xsl:value-of select="@config_type"/></xsl:variable>
				<xsl:variable name="configuration_locked_to"><xsl:if test="@config_type!='unlocked'"><xsl:value-of select="@locked_to"/></xsl:if></xsl:variable>
				<input type="hidden" value="off" >
					<xsl:attribute name="name"><xsl:value-of select="@name"/>_switch_editor</xsl:attribute>
					<xsl:attribute name="id"><xsl:value-of select="@name"/>_textarea_PLAIN</xsl:attribute>
				</input>
				<table class="width600px" border="0" cellspacing="0" cellpadding="0">
				<tr>
					<td class="LIBERTAS_default_toolbar_top" colspan="2" >
						<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_toolbar_top_html</xsl:attribute>
						<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_toolbar_top_html</xsl:attribute>
						<xsl:if test="//module[@name='editor']/setting[@name=$configuration_locked_to or $configuration_type='unlocked']/btn[@id='cut_copy_paste' or @id='undo_redo' ]">
						<span><xsl:attribute name="id"><xsl:value-of select="@name"/>_html_buttons</xsl:attribute><xsl:attribute name="name"><xsl:value-of select="@name"/>_html_buttons</xsl:attribute>
<img class='LIBERTAS_default_tb_out' unselectable='on' alt='Cut' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_cut.gif' width='24' height='24' onMouseOver='LIBERTAS_default_bt_over(this);' onMouseOut='LIBERTAS_default_bt_out(this);' onMouseDown='LIBERTAS_default_bt_down(this);' onMouseUp='LIBERTAS_default_bt_up(this);'>
	<xsl:attribute name="onclick">LIBERTAS_on_click('<xsl:value-of select="@name"/>',this ,'cut')</xsl:attribute>
	<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_tb_cut</xsl:attribute>
	<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_tb_cut</xsl:attribute>
</img>
<img class='LIBERTAS_default_tb_out' unselectable='on' alt='Copy' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_copy.gif' width='24' height='24' onMouseOver='LIBERTAS_default_bt_over(this);' onMouseOut='LIBERTAS_default_bt_out(this);' onMouseDown='LIBERTAS_default_bt_down(this);' onMouseUp='LIBERTAS_default_bt_up(this);'>
	<xsl:attribute name="onclick">LIBERTAS_on_click('<xsl:value-of select="@name"/>',this ,'copy')</xsl:attribute>
	<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_tb_copy</xsl:attribute>
	<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_tb_copy</xsl:attribute>
</img>
<img class='LIBERTAS_default_tb_out' unselectable='on' alt='Paste Normal' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_paste.gif' width='24' height='24' onMouseOver='LIBERTAS_default_bt_over(this);' onMouseOut='LIBERTAS_default_bt_out(this);' onMouseDown='LIBERTAS_default_bt_down(this);' onMouseUp='LIBERTAS_default_bt_up(this);'>
	<xsl:attribute name="onclick">LIBERTAS_on_click('<xsl:value-of select="@name"/>',this ,'paste')</xsl:attribute>
	<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_tb_paste</xsl:attribute>
	<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_tb_paste</xsl:attribute>
</img>
<img alt='' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_vertical_separator.gif' width='3' height='24'/>
<img class='LIBERTAS_default_tb_out' unselectable='on' alt='Undo' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_undo.gif' width='24' height='24' onMouseOver='LIBERTAS_default_bt_over(this);' onMouseOut='LIBERTAS_default_bt_out(this);' onMouseDown='LIBERTAS_default_bt_down(this);' onMouseUp='LIBERTAS_default_bt_up(this);'>
	<xsl:attribute name="onclick">LIBERTAS_on_click('<xsl:value-of select="@name"/>',this ,'undo')</xsl:attribute>
	<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_tb_undo</xsl:attribute>
	<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_tb_undo</xsl:attribute>
</img>
<img class='LIBERTAS_default_tb_out' unselectable='on' alt='Redo' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_redo.gif' width='24' height='24' onMouseOver='LIBERTAS_default_bt_over(this);' onMouseOut='LIBERTAS_default_bt_out(this);' onMouseDown='LIBERTAS_default_bt_down(this);' onMouseUp='LIBERTAS_default_bt_up(this);'>
	<xsl:attribute name="onclick">LIBERTAS_on_click('<xsl:value-of select="@name"/>',this ,'redo')</xsl:attribute>
	<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_tb_redo</xsl:attribute>
	<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_tb_redo</xsl:attribute>
</img>
</span>
							</xsl:if>
							</td>
						</tr>
						<tr>
							<td align="left" valign="top" width="100%">			
						   	<input type="hidden" name="body_type" ><xsl:attribute name="value"><xsl:value-of select="@type"/></xsl:attribute></input>
				   			<zinput type="hidden"><xsl:attribute name="name">rt<xsl:value-of select="@name"/></xsl:attribute></zinput>
							<input type="hidden" name="editor_save_to[]"><xsl:attribute name="value"><xsl:value-of select="@name"/></xsl:attribute></input>
							<textarea>
								<xsl:attribute name="style">width:100%;height:<xsl:value-of select="(@height * 25)"/>px;</xsl:attribute>
								<xsl:attribute name="rows"><xsl:value-of select="@height"/></xsl:attribute>
								<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
								<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
							<xsl:choose><xsl:when test=".!=''"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:when><xsl:otherwise></xsl:otherwise></xsl:choose></textarea>			
							<input type="hidden" value="design">
								<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_editor_mode</xsl:attribute>
								<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_editor_mode</xsl:attribute>
							</input>			
							<input type="hidden" value="en">
								<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_lang</xsl:attribute>
								<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_lang</xsl:attribute>
							</input>
							<input type="hidden" value="default">
								<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_theme</xsl:attribute>
								<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_theme</xsl:attribute>
							</input>
							<input type="hidden" value="on">
								<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_borders</xsl:attribute>
								<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_borders</xsl:attribute>
							</input>
							<xsl:choose>
							<xsl:when test="$browser_type='IE'">
							<iframe class="LIBERTAS_default_editarea" frameborder="no">
								<xsl:attribute name="style">padding:3px 3px 3px 3px;WIDTH: 100%;height:<xsl:value-of select="(@height * 25)"/>px; DIRECTION: ltr; display:none;</xsl:attribute>
								<xsl:attribute name="id"><xsl:value-of select="@name"/>_rEdit</xsl:attribute>
								<xsl:attribute name="name"><xsl:value-of select="@name"/>_rEdit</xsl:attribute>
							</iframe>
							</xsl:when>
							<xsl:otherwise><ilayer
							class="LIBERTAS_default_editarea" frameborder="no" >
								<xsl:attribute name="style">padding:3px 3px 3px 3px;WIDTH: 100%;height:<xsl:value-of select="(@height * 25)"/>px; DIRECTION: ltr; display:none;</xsl:attribute>
								<xsl:attribute name="id"><xsl:value-of select="@name"/>_rEdit</xsl:attribute>
								<xsl:attribute name="name"><xsl:value-of select="@name"/>_rEdit</xsl:attribute>
							</ilayer></xsl:otherwise>
							</xsl:choose>
							<div id='displaymenu' style="position:absolute;display:none;width:150;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;background-Color:#ebebeb; border: outset 1px gray;zindex:-100"></div>
				<script language="javascript">
				<xsl:comment>
				function cache_information(){
					this.menu 		= "";
					this.pages		= Array();
					this.files		= "";
					this.image_list	= "";
					this.flash_list	= "";
					this.form_list	= "";
					this.movie_list	= "";
					this.audio_list	= "";
				}

				var cachedata	= new cache_information();
				var palette 	= new Array(<xsl:for-each select="//module[@name='editor']/colours/colour">'<xsl:value-of select="@value"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
				var function_access_<xsl:value-of select="@name"/> = new Array(<xsl:for-each select="//module[@name='editor']/setting[@name=$configuration_locked_to or $configuration_type='unlocked']/btn">'libertas_configuration_<xsl:value-of select="@id"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
				LIBERTAS_GENERAL_getFormElement('<xsl:value-of select="@name"/>').value = LIBERTAS_GENERAL_getFormElement('<xsl:value-of select="@name"/>').value.split("&lt;br&gt;").join("\r\n");

				<xsl:choose>
					<xsl:when test="$browser_type='IE'">
						if (version>5){
							var oPopup = window.createPopup();
						} else {
							var oPopup = null;
						}
					</xsl:when>
					<xsl:otherwise>
						var oPopup = null;
					</xsl:otherwise>
				</xsl:choose>

				var libertas_active_toolbar = has_function('<xsl:value-of select="@name"/>','libertas_configuration_context_sensitive');
				LIBERTAS_editorInit('<xsl:value-of select="@name"/>','<xsl:choose>
					<xsl:when test="//xml_document/modules/module/setting[@name='theme_directory']!=''">/libertas_images/themes/<xsl:value-of select="//xml_document/modules/module/setting[@name='theme_directory']"/>/style_default.css</xsl:when>
					<xsl:otherwise>/libertas_images/editor/libertas/wysiwyg.css</xsl:otherwise>
				</xsl:choose>','ltr', 'textonly');

				//</xsl:comment>
				</script></td>
						</tr>
						<tr style="height:16px;background:#cccccc;">
							<td colspan="1" align="left" valign="top" width="100%" style="height:16px; border-top:1 solid #333333; border-bottom:1 solid #999999; border-right:1 solid #999999; border-left:1 solid #999999;">(Shift + Enter) for new Line</td>
						</tr>
						</table></td><td id="email_msg_list_of_fields" valign="top"></td></tr></table>
	   	   	</xsl:when>
	   	   	<xsl:when test="@type='RICH-TEXT'">
				<script>
					<xsl:comment>
					
						var user_form = get_form();
					<xsl:if test="//textarea[@required='YES']">
						check_editors[check_editors.length] = Array('<xsl:value-of select="@name"/>', '<xsl:value-of select="@label"/>', '<xsl:value-of select="$span_name"/>');
					</xsl:if>
					//</xsl:comment>
				</script>
				<xsl:variable name="configuration_type"><xsl:value-of select="@config_type"/></xsl:variable>
				<xsl:variable name="configuration_locked_to"><xsl:if test="@config_type!='unlocked'"><xsl:value-of select="@locked_to"/></xsl:if></xsl:variable>
				
				<xsl:if test="//module[@name='editor']/setting[@name=$configuration_locked_to or $configuration_type='unlocked']/btn[@id='toggle_design']">
					<input type="radio" value="on" selected="true" checked="true">
						<xsl:attribute name="name"><xsl:value-of select="@name"/>_switch_editor</xsl:attribute>
						<xsl:attribute name="id"><xsl:value-of select="@name"/>_textarea_WYSIWYG</xsl:attribute>
						<xsl:attribute name="onclick">LIBERTAS_design_tab_click('<xsl:value-of select="@name"/>',this)</xsl:attribute>
					</input>
					<label>
						<xsl:attribute name="for"><xsl:value-of select="@name"/>_textarea_WYSIWYG</xsl:attribute>
						<xsl:attribute name="onclick">LIBERTAS_design_tab_click('<xsl:value-of select="@name"/>',this)</xsl:attribute>
					Richtext</label>		
					<input type="radio" value="off" >
						<xsl:attribute name="name"><xsl:value-of select="@name"/>_switch_editor</xsl:attribute>
						<xsl:attribute name="id"><xsl:value-of select="@name"/>_textarea_PLAIN</xsl:attribute>
						<xsl:attribute name="onclick">LIBERTAS_html_tab_click('<xsl:value-of select="@name"/>',this)</xsl:attribute>
					</input>
					<label>
						<xsl:attribute name="for"><xsl:value-of select="@name"/>_textarea_PLAIN</xsl:attribute>
						<xsl:attribute name="onclick">LIBERTAS_html_tab_click('<xsl:value-of select="@name"/>',this)</xsl:attribute>
					HTML</label>
				</xsl:if>
				<table width="100%" border="0" cellspacing="0" cellpadding="0" style="width:100%">
				<tr>
					<td colspan="3" class="LIBERTAS_default_toolbar_top">
						<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_toolbar_top_design</xsl:attribute>
						<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_toolbar_top_design</xsl:attribute>
						<xsl:call-template name="set_editor">
							<xsl:with-param name="editor"><xsl:value-of select="@name"/></xsl:with-param>
							<xsl:with-param name="str"><xsl:value-of select="//module[@name='editor']/setting[@name=$configuration_locked_to or $configuration_type='unlocked']/top"/></xsl:with-param>
						</xsl:call-template>
					</td>
					<td class="LIBERTAS_default_toolbar_top" colspan="3" style="display : none;" >
						<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_toolbar_top_html</xsl:attribute>
						<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_toolbar_top_html</xsl:attribute>
						<xsl:if test="//module[@name='editor']/setting[@name=$configuration_locked_to or $configuration_type='unlocked']/btn[@id='cut_copy_paste' or @id='undo_redo' ]">
						<span><xsl:attribute name="id"><xsl:value-of select="@name"/>_html_buttons</xsl:attribute><xsl:attribute name="name"><xsl:value-of select="@name"/>_html_buttons</xsl:attribute>
<img class='LIBERTAS_default_tb_out' unselectable='on' alt='Cut' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_cut.gif' width='24' height='24' onMouseOver='LIBERTAS_default_bt_over(this);' onMouseOut='LIBERTAS_default_bt_out(this);' onMouseDown='LIBERTAS_default_bt_down(this);' onMouseUp='LIBERTAS_default_bt_up(this);'>
	<xsl:attribute name="onclick">LIBERTAS_on_click('<xsl:value-of select="@name"/>',this ,'cut')</xsl:attribute>
	<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_tb_cut</xsl:attribute>
	<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_tb_cut</xsl:attribute>
</img>
<img class='LIBERTAS_default_tb_out' unselectable='on' alt='Copy' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_copy.gif' width='24' height='24' onMouseOver='LIBERTAS_default_bt_over(this);' onMouseOut='LIBERTAS_default_bt_out(this);' onMouseDown='LIBERTAS_default_bt_down(this);' onMouseUp='LIBERTAS_default_bt_up(this);'>
	<xsl:attribute name="onclick">LIBERTAS_on_click('<xsl:value-of select="@name"/>',this ,'copy')</xsl:attribute>
	<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_tb_copy</xsl:attribute>
	<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_tb_copy</xsl:attribute>
</img>
<img class='LIBERTAS_default_tb_out' unselectable='on' alt='Paste Normal' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_paste.gif' width='24' height='24' onMouseOver='LIBERTAS_default_bt_over(this);' onMouseOut='LIBERTAS_default_bt_out(this);' onMouseDown='LIBERTAS_default_bt_down(this);' onMouseUp='LIBERTAS_default_bt_up(this);'>
	<xsl:attribute name="onclick">LIBERTAS_on_click('<xsl:value-of select="@name"/>',this ,'paste')</xsl:attribute>
	<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_tb_paste</xsl:attribute>
	<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_tb_paste</xsl:attribute>
</img>
<img alt='' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_vertical_separator.gif' width='3' height='24'/>
<img class='LIBERTAS_default_tb_out' unselectable='on' alt='Undo' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_undo.gif' width='24' height='24' onMouseOver='LIBERTAS_default_bt_over(this);' onMouseOut='LIBERTAS_default_bt_out(this);' onMouseDown='LIBERTAS_default_bt_down(this);' onMouseUp='LIBERTAS_default_bt_up(this);'>
	<xsl:attribute name="onclick">LIBERTAS_on_click('<xsl:value-of select="@name"/>',this ,'undo')</xsl:attribute>
	<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_tb_undo</xsl:attribute>
	<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_tb_undo</xsl:attribute>
</img>
<img class='LIBERTAS_default_tb_out' unselectable='on' alt='Redo' vspace='3' src='/libertas_images/editor/libertas/lib/themes/default/img/tb_redo.gif' width='24' height='24' onMouseOver='LIBERTAS_default_bt_over(this);' onMouseOut='LIBERTAS_default_bt_out(this);' onMouseDown='LIBERTAS_default_bt_down(this);' onMouseUp='LIBERTAS_default_bt_up(this);'>
	<xsl:attribute name="onclick">LIBERTAS_on_click('<xsl:value-of select="@name"/>',this ,'redo')</xsl:attribute>
	<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_tb_redo</xsl:attribute>
	<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_tb_redo</xsl:attribute>
</img>
</span>
							</xsl:if>
							</td>
						</tr>
						<tr>
							<td class="LIBERTAS_default_toolbar_left" valign="top"><xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_TableButtons</xsl:attribute>
							<xsl:call-template name="set_editor">
							<xsl:with-param name="editor"><xsl:value-of select="@name"/></xsl:with-param>
							<xsl:with-param name="str"><xsl:value-of select="//module[@name='editor']/setting[@name=$configuration_locked_to or $configuration_type='unlocked']/left"/></xsl:with-param>
						</xsl:call-template>
							</td>
							<td align="left" valign="top" width="100%">			
						   	<input type="hidden" name="body_type" ><xsl:attribute name="value"><xsl:value-of select="@type"/></xsl:attribute></input>
				   			<zinput type="hidden"><xsl:attribute name="name">rt<xsl:value-of select="@name"/></xsl:attribute></zinput>
							<input type="hidden" name="editor_save_to[]"><xsl:attribute name="value"><xsl:value-of select="@name"/></xsl:attribute></input>
							<textarea>
								<xsl:attribute name="style">width:100%;height:<xsl:value-of select="(@height * 25)"/>px;display:none;</xsl:attribute>
								<xsl:attribute name="rows"><xsl:value-of select="@height"/></xsl:attribute>
								<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
								<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
							<xsl:choose><xsl:when test=".!=''"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:when><xsl:otherwise></xsl:otherwise></xsl:choose></textarea>			
							<input type="hidden" value="design">
								<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_editor_mode</xsl:attribute>
								<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_editor_mode</xsl:attribute>
							</input>			
							<input type="hidden" id="LIBERTAS_libertas1_lang" value="en">
								<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_lang</xsl:attribute>
								<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_lang</xsl:attribute>
							</input>
							<input type="hidden" value="default">
								<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_theme</xsl:attribute>
								<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_theme</xsl:attribute>
							</input>
							<input type="hidden" value="on">
								<xsl:attribute name="id">LIBERTAS_<xsl:value-of select="@name"/>_borders</xsl:attribute>
								<xsl:attribute name="name">LIBERTAS_<xsl:value-of select="@name"/>_borders</xsl:attribute>
							</input>
							<iframe
								class="LIBERTAS_default_editarea" 
								frameborder="no" 
							><xsl:attribute name="style">padding:3px 3px 3px 3px;WIDTH: 100%;height:<xsl:value-of select="(@height * 25)"/>px; DIRECTION: ltr; display:;</xsl:attribute>
								<xsl:attribute name="id"><xsl:value-of select="@name"/>_rEdit</xsl:attribute>
								<xsl:attribute name="name"><xsl:value-of select="@name"/>_rEdit</xsl:attribute>
							</iframe>
							<div id='displaymenu' style="position:absolute;display:none;width:150;padding:0px 0px 0px 0px;margin:0px 0px 0px 0px;background-Color:#ebebeb; border: outset 1px gray;zindex:-100"></div>
				<script language="javascript">
				<xsl:comment>
				var cachedata	= new cache_information();
				
				function cache_information(){
					this.menu 		= "";
					this.pages		= Array();
					this.files		= "";
					this.image_list	= "";
					this.flash_list	= "";
					this.form_list	= "";
					this.movie_list	= "";
					this.audio_list	= "";
				}
				
				var palette = Array(<xsl:for-each select="//module[@name='editor']/colours/colour">'<xsl:value-of select="@value"/>'<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
				var function_access_<xsl:value-of select="@name"/> = Array();
				<xsl:choose>
					<xsl:when test="$browser_type='IE'">
						if (version>5){
							var oPopup = window.createPopup();
						} else {
							var oPopup = null;
						}
					</xsl:when>
					<xsl:otherwise>
						var oPopup = null;
					</xsl:otherwise>
				</xsl:choose>
				var libertas_active_toolbar = has_function('<xsl:value-of select="@name"/>','libertas_configuration_context_sensitive');
				LIBERTAS_editorInit('<xsl:value-of select="@name"/>','<xsl:choose>
				<xsl:when test="//xml_document/modules/module/setting[@name='theme_directory']!=''">/libertas_images/themes/<xsl:value-of select="//xml_document/modules/module/setting[@name='theme_directory']"/>/style_default.css</xsl:when>
				<xsl:otherwise>/libertas_images/editor/libertas/wysiwyg.css</xsl:otherwise>
				</xsl:choose>','ltr', '');
								
				//</xsl:comment>
				</script></td>
						</tr>
						<tr style="height:16px;background:#cccccc;">
							<td colspan="3" align="left" valign="top" width="100%" style="height:16px; border-top:1 solid #333333; border-bottom:1 solid #999999; border-right:1 solid #999999; border-left:1 solid #999999;">(Shift + Enter) for new Line</td>
						</tr>
						</table>
			</xsl:when>
		   	<xsl:otherwise>
				<textarea class="none" >
				<xsl:if test="@nowrap"><xsl:attribute name="nowrap">true</xsl:attribute></xsl:if>
	   			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
	   			<xsl:attribute name="size"><xsl:value-of select="@size"/></xsl:attribute>
				<xsl:attribute name="rows"><xsl:value-of select="@height"/></xsl:attribute>
				<xsl:attribute name="cols"><xsl:value-of select="@width"/></xsl:attribute>
				<xsl:if test="@width"><xsl:attribute name="style">width:<xsl:value-of select="@width"/>em</xsl:attribute></xsl:if>
				<xsl:value-of select="." disable-output-escaping="yes"/>
			   	</textarea>
				<xsl:if test="@required='yes'"><script>check_required[check_required.length] = new Array('<xsl:value-of select="@name"/>', '<xsl:value-of select="@label"/>', 'tab_1');</script></xsl:if>
	   		</xsl:otherwise>
	   	</xsl:choose>
   	</td>
</xsl:template>
<xsl:template name="set_editor">
	<xsl:param name="editor">__NOT_FOUND__</xsl:param>
	<xsl:param name="str"></xsl:param>
	<xsl:choose>
	<xsl:when test="contains($str,'[[editor]]')"><xsl:value-of select="substring-before($str,'[[editor]]')"/><xsl:value-of select="$editor"/><xsl:call-template name="set_editor">
			<xsl:with-param name="editor"><xsl:value-of select="$editor"/></xsl:with-param>
			<xsl:with-param name="str"><xsl:value-of select="substring-after($str,'[[editor]]')"/></xsl:with-param>
	</xsl:call-template></xsl:when>
	<xsl:otherwise><xsl:value-of select="$str"/></xsl:otherwise>
	</xsl:choose></xsl:template>
	
</xsl:stylesheet>
