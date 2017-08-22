<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.27 $
- Modified $Date: 2005/02/25 09:30:56 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0">

<xsl:include href="../../styles/general/display_shopping.xsl"/>
<xsl:template match="form">
	<xsl:param name="submit_align"><xsl:value-of select="$form_submit_align"/></xsl:param>
	<xsl:param name="formname"></xsl:param>
	<xsl:param name="intable">0</xsl:param>
	<xsl:param name="uid"><xsl:value-of select="../uid"/></xsl:param>
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="extract_pos">2</xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="show_label">0</xsl:param>
	<xsl:param name="show_label_bullet">0</xsl:param>
	<xsl:if test="$formname=@name or $formname=''">
	<form>
	<xsl:attribute name="action"><xsl:choose>
	<xsl:when test="@action"><xsl:value-of select="@action" disable-output-escaping="yes"/></xsl:when>
	<xsl:when test="action"><xsl:value-of select="action" disable-output-escaping="yes"/></xsl:when>
	<xsl:otherwise><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='real_script']"/></xsl:otherwise>
	</xsl:choose></xsl:attribute>
	<xsl:if test="@name">
		<xsl:attribute name="id"><xsl:value-of select="@name"/><xsl:value-of select="$uid"/></xsl:attribute>
	</xsl:if>
	<xsl:if test=".//@type='file'">
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
	<xsl:if test="//@error=1">
		<h1><span>Error with form</span></h1>
		<p>The following fields have been found to be duplicates</p>
		<ul class="error">
			<xsl:for-each select="descendant-or-self::node()[./@error=1]">
				<li><label><xsl:attribute name='for'><xsl:value-of select="@name"/></xsl:attribute><xsl:value-of select="@label"/></label></li>
			</xsl:for-each>
		</ul>
	</xsl:if>
	<xsl:variable name="required"><xsl:if test=".//@required">1</xsl:if></xsl:variable>
	<div class='hiddenfields'>
		<xsl:comment>Hidden fields</xsl:comment>
		<xsl:for-each select="input[@type='hidden']">
			<input type='hidden'>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:attribute name="value"><xsl:choose>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
			</xsl:choose></xsl:attribute>
		   	</input>
		</xsl:for-each>
	</div>

	<xsl:choose>
		<xsl:when test="$show_label=1 "></xsl:when>
		<xsl:otherwise>
			<xsl:variable name="label"><xsl:choose>
					<xsl:when test="@label"><xsl:value-of select="@label" disable-output-escaping="yes"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
				</xsl:choose></xsl:variable>
			<xsl:if test="$label!=''">
			<h1><span class='icon'><span class='text'>
				<xsl:if test="$title_bullet=1"><img width="20" border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/title_bullet.gif</xsl:attribute></img>[[nbsp]]</xsl:if>
				<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="$label"/></xsl:with-param></xsl:call-template>
			</span></span>	</h1>
			</xsl:if>
		</xsl:otherwise>
	</xsl:choose>

	<xsl:choose>
		<xsl:when test="$intable=0">
		<div class='table'>
			<xsl:choose>
				<xsl:when test="seperator_page/seperator_row/seperator">
					<xsl:apply-templates>
						<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
						<xsl:with-param name="uid"><xsl:value-of select="$uid"/></xsl:with-param>
						<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
					</xsl:apply-templates>
					<xsl:if test="$required=1">
						<div class='required'><span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template></div>
					</xsl:if>
					<xsl:call-template name="display_submit_buttons">
						<xsl:with-param name="uid"><xsl:value-of select="$uid"/></xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:when test="seperator_row/seperator">
					<xsl:apply-templates>
						<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
						<xsl:with-param name="uid"><xsl:value-of select="$uid"/></xsl:with-param>
						<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
					</xsl:apply-templates>
					<xsl:if test="$required=1">
						<div class='required'><span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template></div>
					</xsl:if>
					<xsl:call-template name="display_submit_buttons">
						<xsl:with-param name="uid"><xsl:value-of select="$uid"/></xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:apply-templates>
						<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
					</xsl:apply-templates>
					<xsl:if test="$required=1">
						<div class='required'><span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template></div>
					</xsl:if>
					<xsl:choose>
					<xsl:when test=".//@adv='0'">
					</xsl:when>
					<xsl:otherwise>
						<xsl:call-template name="display_submit_buttons">
							<xsl:with-param name="submit_align"><xsl:value-of select="$submit_align"/></xsl:with-param>
							<xsl:with-param name="uid"><xsl:value-of select="$uid"/></xsl:with-param>
						</xsl:call-template>
					</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</div>
		</xsl:when>
		<xsl:otherwise><div class='table'>
			<xsl:if test="@width">
				<xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute>
			</xsl:if>
			<xsl:choose>
				<xsl:when test="seperator_row/seperator">
					<xsl:apply-templates>
						<xsl:with-param name="uid"><xsl:value-of select="$uid"/></xsl:with-param>
						<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
					</xsl:apply-templates>
					<xsl:if test="$required=1">
						<div class='required'><span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template></div>
					</xsl:if>
					<xsl:call-template name="display_submit_buttons">
						<xsl:with-param name="uid"><xsl:value-of select="$uid"/></xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select="./child::*">
						<xsl:variable name="current"><xsl:value-of select="position()"/></xsl:variable>
						<xsl:choose>
				    		<xsl:when test="$labelinnewrow=1 and (@type!='hidden' or @type!='submit' or @type!='button')">
		    					<div class='row'>
					   				<div class='cell'><label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
									<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template><xsl:if test="@required"> <span class="required">*</span></xsl:if></label> </div>
				    			</div>
								<div class='row'>
									<div class="cell">
										<xsl:apply-templates select=".">
											<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
											<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
											<xsl:with-param name="uid"><xsl:value-of select="$uid"/></xsl:with-param>
										</xsl:apply-templates>
									</div>
								</div>
							</xsl:when>
							<xsl:otherwise>
								<xsl:apply-templates select="."><xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
								<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
								<xsl:with-param name="uid"><xsl:value-of select="$uid"/></xsl:with-param>
								</xsl:apply-templates>
							</xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
					<xsl:if test="$required=1">
						<div class='required'><span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template></div>
					</xsl:if>
					<xsl:choose>
					<xsl:when test="//@adv='0'">
					</xsl:when>
					<xsl:otherwise>
					<xsl:call-template name="display_submit_buttons">
						<xsl:with-param name="submit_align"><xsl:value-of select="$submit_align"/></xsl:with-param>
						<xsl:with-param name="uid"><xsl:value-of select="$uid"/></xsl:with-param>
					</xsl:call-template>
					</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>
		</div>
	</xsl:otherwise>
	</xsl:choose>
	</form>
	<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
		<script type="text/javascript">
			__FRM_add('<xsl:value-of select="@name"/><xsl:value-of select="$uid"/>');
		</script>
	</xsl:if>
	<xsl:if test="../text">
	<xsl:for-each select="../text"><xsl:value-of select="."/></xsl:for-each>
	</xsl:if>
</xsl:if>
</xsl:template>

<xsl:template name="display_submit_buttons">
	<xsl:param name="submit_align">center</xsl:param>
	<xsl:param name="uid"></xsl:param>
	<xsl:param name="adv"></xsl:param>

	<xsl:choose>
		<xsl:when test=".//adv=0">
		</xsl:when>
		<xsl:otherwise>
		<div class='buttonrow'>
			<div><xsl:attribute name="class">align<xsl:value-of select="$submit_align"/></xsl:attribute>
	  	 	<xsl:for-each select="input[@type='submit']">
				<xsl:choose>
					<xsl:when test="$form_button_type='IMAGE'">
						<input type='image' class="button">
						   	<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
						   	<xsl:attribute name="alt">
							<xsl:call-template name="get_translation">
								<xsl:with-param name="check" select="@value"/>
							</xsl:call-template>
							</xsl:attribute>
						   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
						</input>
					</xsl:when>
					<xsl:when test="$form_button_type='HTML_WITH_ARROWS'">
						<input class="button" type='submit'>
						   	<xsl:attribute name="value">> <xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
									<xsl:when test="@alt!=''"><xsl:value-of select="@alt"/></xsl:when>
									<xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when>
									<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
									</xsl:choose>
								</xsl:with-param>
							</xsl:call-template></xsl:attribute>
						   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
					   	</input>
					</xsl:when>
						<xsl:when test="$form_button_type='HTML'">
						<input class="button" type='submit'><xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check">
										<xsl:choose>
										<xsl:when test="@alt!=''"><xsl:value-of select="@alt"/></xsl:when>
										<xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when>
										<xsl:when test=".!=''"><xsl:value-of select="@value"/></xsl:when>
										<xsl:otherwise>Submit</xsl:otherwise>
										</xsl:choose>
									</xsl:with-param>
								</xsl:call-template></xsl:attribute>
							   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
						   	</input>
						</xsl:when>
				</xsl:choose><xsl:if test="position()!=last()"><img src="/libertas_images/themes/1x1.gif" width="5" height="5" alt=""/></xsl:if>
			</xsl:for-each>
			<xsl:if test="boolean(input[@type='button'])">
			<ul class='form_options'>
		  	 	<xsl:for-each select="input[@type='button']">
					<li><xsl:attribute name="class"><xsl:value-of select="@iconify"/></xsl:attribute>
						<a><xsl:attribute name="href"><xsl:choose>
							<xsl:when test="contains(@command,'.')"><xsl:value-of select="@command"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='real_script']"/>?command=<xsl:value-of select="@command"/></xsl:otherwise>
						</xsl:choose></xsl:attribute>
						<span class='icon'><span class='text'><xsl:call-template name="get_translation">
							<xsl:with-param name="check"><xsl:choose>
								<xsl:when test="@alt!=''"><xsl:value-of select="@alt"/></xsl:when>
								<xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
							</xsl:choose></xsl:with-param>
						</xsl:call-template></span></span></a></li>
				   	</xsl:for-each>
				</ul>
			</xsl:if>
			</div>
  		</div></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="select">
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="uid"></xsl:param>
	<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
		<div class='row'>
		<xsl:if test="$labelinnewrow=0">
		   	<div class='cell'><label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
				<xsl:when test="boolean(label)"><xsl:value-of select="label"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
			</xsl:choose></xsl:with-param> </xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></label></div>
		</xsl:if>
		   	<div class='cell'><select>
   			<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/><xsl:if test="@multiple='1'">[]</xsl:if></xsl:attribute>
			<xsl:if test="@multiple='1'"><xsl:attribute name="multiple">1</xsl:attribute>
			<xsl:attribute name="style">width:200px</xsl:attribute>
				<xsl:if test="@size">
					<xsl:attribute name="size"><xsl:value-of select="@size"/></xsl:attribute>
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
			<xsl:if test="boolean(optiondata)">
				<xsl:value-of select="optiondata"/>
			</xsl:if>
			<xsl:if test="other_label"><option value="_system_defined_other_"><xsl:value-of select="other_label"/></option></xsl:if>
		   	</select><xsl:if test="other_label">
				<br/>
					<label class="otherspecify"><xsl:attribute name="for">other_entry_<xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute><xsl:value-of select="other_label" disable-output-escaping="yes"/></label>
					<input type="text" size="20" maxlenght="255">
						<xsl:variable name="value"><xsl:choose>
							<xsl:when test="//values/field[@name=./@name]"><xsl:value-of select="//values/field[@name=./@name]"/></xsl:when>
							<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="other_label"/></xsl:otherwise>
						</xsl:choose></xsl:variable>

					<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No' and string-length($value)=0">
						<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
					</xsl:if>
						<xsl:attribute name="name">other_entry_<xsl:value-of select="@name"/></xsl:attribute>
					   	<xsl:attribute name="id">other_entry_<xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:choose>
								<xsl:when test="string-length($value)!=0"><xsl:value-of select="$value"/></xsl:when>
								<xsl:when test="string-length(other_label)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="other_label"/></xsl:call-template>[[gt]]</xsl:when>
						</xsl:choose></xsl:attribute>
					</input>
			</xsl:if>
		</div>
	</div>
</xsl:template>

<xsl:template match="textarea">
<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="uid"></xsl:param>
		<div class='row'>
			<xsl:variable name="label"><xsl:choose>
				<xsl:when test="boolean(label)"><xsl:value-of select="label"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			<xsl:if test="$labelinnewrow=0 and @label">
			   	<div class='cell'>
					<label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
					<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label>
					 <xsl:if test="@required"><span class="required">*</span></xsl:if>
				</div>
			</xsl:if>
			<div class='cell'><xsl:if test="not(@label)"><xsl:attribute name="class">aligncenter</xsl:attribute></xsl:if>
					<textarea>
		   			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   			<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
		   			<xsl:choose>
						<xsl:when test="@size > 32"><xsl:attribute name="style">width:99%</xsl:attribute></xsl:when>
						<xsl:otherwise><xsl:attribute name="cols"><xsl:value-of select="@size"/></xsl:attribute></xsl:otherwise>
					</xsl:choose>
					<xsl:attribute name="rows"><xsl:value-of select="@height"/></xsl:attribute>
					<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
					<xsl:variable name="value"><xsl:choose>
						<xsl:when test="//values/field[@name=$name]"><xsl:value-of select="//values/field[@name=$name]"/></xsl:when>
						<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
					</xsl:choose></xsl:variable>
					<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No' and string-length($value)=0">
						<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
					</xsl:if>
					<xsl:choose>
						<xsl:when test="string-length(.)=0 and string-length(@value)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation">
							<xsl:with-param name="check" select="$label"/>
							<xsl:with-param name="maxlen" select="50"/>
						</xsl:call-template>[[gt]]</xsl:when>
						<xsl:when test="$value!=''"><xsl:value-of select="$value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="$value"/></xsl:otherwise>
					</xsl:choose></textarea>
		   	</div>
  		</div>
</xsl:template>

<xsl:template match="checkboxes">
	<xsl:param name="uid"></xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
	<xsl:variable name="lengthOfEntries"><xsl:for-each select="//values/field[@name=$name]">x</xsl:for-each></xsl:variable>
	<xsl:variable name="mycounter"><xsl:value-of select="string-length($lengthOfEntries)"/></xsl:variable>
<xsl:choose>
	<xsl:when test="options">
	<div class='row'>
	   	<div class='celllabel'><xsl:if test="$labelinnewrow=0"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></xsl:if><br />
		   	<xsl:if test="@type='vertical'">
			<xsl:for-each select="options">
				<xsl:sort select="@module"/>
				<div class='row'>
					<div class='cell'><b><xsl:value-of select="@module"/></b><br />
					<xsl:for-each select="option">
					<input type="checkbox" class="radiocheckbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:choose>
								<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:choose>
								<xsl:when test="(@checked='true' or @selected='true') and $mycounter='0'">
									<xsl:if test="(@checked='true' or @selected='true')">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
									<xsl:for-each select="//values/field[@name=$name]">
										<xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
			   		</input>
					<label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label>
						<br />
		   			</xsl:for-each>
		   			</div>
		   		</div>
		   		</xsl:for-each>
		   	</xsl:if>
		   	<xsl:if test="@type='horizontal'">
	   		   	<div class='table'>
	   		   	<xsl:for-each select="options">
	   		   	<div class='row'>
					<xsl:if test="(position() mod 3) = 1">
					<div class="TableHeader"><b><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@module"/></xsl:call-template></b></div>
					<xsl:for-each select="option">
					<div class='radiooption'>
						<input class="checkbox" type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
							<xsl:attribute name="value"><xsl:choose>
						<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../../@name"/></xsl:attribute>
							<xsl:choose>
								<xsl:when test="(@checked='true' or @selected='true') and $mycounter='0'">
									<xsl:if test="(@checked='true' or @selected='true')">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
									<xsl:for-each select="//values/field[@name=$name]">
										<xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
				   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label>
					</div>
					</xsl:for-each>
					<xsl:if test="following-sibling::options[position()=1]">
					<div class="TableHeader"><b><xsl:value-of select="following-sibling::options[position()=1]/@module"/></b></div>
					<xsl:for-each select="following-sibling::options[position()=1]/option">
					<div class='radiooption'><input class="checkbox" type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../../@name"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:choose>
						<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
							<xsl:choose>
								<xsl:when test="(@checked='true' or @selected='true') and $mycounter='0'">
									<xsl:if test="(@checked='true' or @selected='true')">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
									<xsl:for-each select="//values/field[@name=$name]">
										<xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
				   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label>
					</div>
					</xsl:for-each>
					</xsl:if>
					<xsl:if test="following-sibling::options[position()=2]">
					<div class='cell'><b><xsl:value-of select="following-sibling::options[position()=2]/@module"/></b></div>
		   		   	<xsl:for-each select="following-sibling::options[position()=2]/option">
					<div class='radiooption'><input class="checkbox" type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../../@name"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:choose>
						<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
							<xsl:choose>
								<xsl:when test="(@checked='true' or @selected='true') and $mycounter='0'">
									<xsl:if test="(@checked='true' or @selected='true')">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
									<xsl:for-each select="//values/field[@name=$name]">
										<xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
									   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label>
										</div>
									</xsl:for-each>
								</xsl:if>
							</xsl:if>
						</div>
			  		</xsl:for-each>
				</div>
			</xsl:if>
		   	</div>
  		</div>
	</xsl:when>
	<xsl:otherwise>
		<div class='row'>
			<xsl:if test="$labelinnewrow=0 and @label">
		   	<div class="celllabel"><xsl:call-template name="get_translation">
					<xsl:with-param name="check" select="@label"/>
				</xsl:call-template>
				<xsl:if test="@required"><span class="required">*</span></xsl:if>
			</div>
			</xsl:if>
			<div class='cell'>
			   	<xsl:if test="@type='vertical'">
					<xsl:for-each select="option">
						<input type="checkbox" class="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:choose>
						<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
							<xsl:choose>
								<xsl:when test="(@checked='true' or @selected='true') and $mycounter='0'">
									<xsl:if test="(@checked='true' or @selected='true')">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
									<xsl:for-each select="//values/field[@name=$name]">
										<xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
			   				<xsl:if test="../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
			   			</input>
						<label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label>
						<br />
		   			</xsl:for-each>
				   	<xsl:if test="@other='true'">
						<input type="checkbox" class="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 1"/></xsl:attribute>
							<xsl:attribute name="value">_system_defined_other_</xsl:attribute>
							<xsl:choose>
								<xsl:when test="(@checked='true' or @selected='true') and $mycounter='0'">
									<xsl:if test="(@checked='true' or @selected='true')">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
									<xsl:for-each select="//values/field[@name=$name]">
										<xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
			   				<xsl:if test="../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
			   			</input>
						<label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 1"/></xsl:attribute><xsl:value-of select="other_label" disable-output-escaping="yes"/></label><br/>
						<label class="otherspecify"><xsl:attribute name="for">other_entry_<xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 2"/></xsl:attribute><xsl:value-of select="other_label" disable-output-escaping="yes"/></label>
						<input type="text" size="20" maxlenght="255">
					<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
						<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
					</xsl:if>
							<xsl:attribute name="name">other_entry_<xsl:value-of select="@name"/></xsl:attribute>
						   	<xsl:attribute name="id">other_entry_<xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 2"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:choose>
								<xsl:when test="//values/field[@name=./@name]"><xsl:value-of select="//values/field[@name=./@name]"/></xsl:when>
								<xsl:when test="string-length(other_label)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="other_label"/></xsl:call-template>[[gt]]</xsl:when>
								<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="other_label"/></xsl:otherwise>
							</xsl:choose></xsl:attribute>
						</input>
				   	</xsl:if>
			   	</xsl:if>
			   	<xsl:if test="@type='horizontal'">
	   		   	<xsl:for-each select="option">
					<input class="checkbox" type="checkbox">
						<xsl:attribute name="name"><xsl:value-of select="../@name"/>[]</xsl:attribute>
						<xsl:attribute name="value"><xsl:choose>
						<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
					   	<xsl:attribute name="id"><xsl:value-of select="../@name"/></xsl:attribute>
							<xsl:choose>
								<xsl:when test="(@checked='true' or @selected='true') and $mycounter='0'">
									<xsl:if test="(@checked='true' or @selected='true')">
										<xsl:attribute name="checked">checked</xsl:attribute>
									</xsl:if>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
									<xsl:for-each select="//values/field[@name=$name]">
										<xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
						<xsl:if test="../@onclick">
							<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
						</xsl:if>
						</input><label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label>
		  		</xsl:for-each>
				<xsl:if test="@other='true'">
					<input type="checkbox" class="checkbox">
					   	<xsl:attribute name="name"><xsl:value-of select="@name"/>[]</xsl:attribute>
					   	<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 1"/></xsl:attribute>
						<xsl:attribute name="value">_system_defined_other_</xsl:attribute>
						<xsl:choose>
							<xsl:when test="(@checked='true' or @selected='true') and $mycounter='0'">
								<xsl:if test="(@checked='true' or @selected='true')">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:if>
							</xsl:when>
							<xsl:otherwise>
								<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
								<xsl:for-each select="//values/field[@name=$name]">
									<xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
								</xsl:for-each>
							</xsl:otherwise>
						</xsl:choose>
			   			<xsl:if test="../@onclick">
							<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
						</xsl:if>
			   		</input>
					<label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 1"/></xsl:attribute><xsl:value-of select="other_label" disable-output-escaping="yes"/></label><br/>
					<label class="otherspecify"><xsl:attribute name="for">other_entry_<xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 2"/></xsl:attribute><xsl:value-of select="other_label" disable-output-escaping="yes"/></label>
					<input type="text" size="20" maxlenght="255">
					<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
						<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
					</xsl:if>
						<xsl:attribute name="name"><xsl:value-of select="@name"/>[]</xsl:attribute>
					   	<xsl:attribute name="id">other_entry_<xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 2"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:choose>
							<xsl:when test="//values/field[@name=./@name]"><xsl:value-of select="//values/field[@name=./@name]"/></xsl:when>
							<xsl:when test="string-length(other_label)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="other_label"/></xsl:call-template>[[gt]]</xsl:when>
							<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="other_label"/></xsl:otherwise>
						</xsl:choose></xsl:attribute>
					</input>
			   	</xsl:if>
			</xsl:if>
			</div>
  		</div>
	</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template match="input">
	<xsl:param name="uid"></xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="extract_pos">2</xsl:param>
	<xsl:choose>
  		<xsl:when test="@type='quantity'">
	   		<xsl:if test="$labelinnewrow=1">
			   	<div class='row'><label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
					<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
					<xsl:when test="boolean(label)"><xsl:value-of select="label"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
				</xsl:choose></xsl:with-param> </xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></label></div>
			</xsl:if>
    	<div class='row'>
			<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
			<xsl:variable name="label"><xsl:choose>
					<xsl:when test="boolean(label)"><xsl:value-of select="label"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
				</xsl:choose></xsl:variable>
    		<xsl:if test="$labelinnewrow=0">
				<div class='cell'>
				<label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
				<xsl:attribute name="class">advlabel</xsl:attribute>
				<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="$label"/></xsl:with-param></xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></label></div>
			</xsl:if>

		   	<div class='cell'>
			<xsl:choose>
    		<xsl:when test="$labelinnewrow=0 and boolean(@adv=0)"><xsl:attribute name="class">advcell</xsl:attribute></xsl:when>
			<xsl:otherwise><xsl:attribute name="class">cell</xsl:attribute></xsl:otherwise>
			</xsl:choose>
			<xsl:variable name="value"><xsl:choose>
				<xsl:when test="../../../values/field[@name=$name]"><xsl:value-of select="../../../values/field[@name=$name]"/></xsl:when>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			<input type='radio' value='-1'>
				<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
				<xsl:attribute name="id"><xsl:value-of select="@name"/>_1</xsl:attribute>
				<xsl:if test="$value='-1'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
			</input>
			<label><xsl:attribute name="for"><xsl:value-of select="@name"/>_1</xsl:attribute>Unlimited</label><br/>
			<input type='radio' value='-2'>
				<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
				<xsl:attribute name="id"><xsl:value-of select="@name"/>_2</xsl:attribute>
				<xsl:if test="$value!='-1'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
			</input>
			<label><xsl:attribute name="for"><xsl:value-of select="@name"/>_2</xsl:attribute>Other</label>
			<input type='text' class='quantity_other'>
			<xsl:attribute name="name"><xsl:value-of select="@name"/>_other</xsl:attribute>
			<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No' and string-length($value)=0">
				<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
			</xsl:if>
			<xsl:attribute name="value"><xsl:choose>
				<xsl:when test="-1">[[lt]]<xsl:call-template name="get_translation">
						<xsl:with-param name="check" select="$label"/>
						<xsl:with-param name="maxlen" select="50"/>
					</xsl:call-template>[[gt]]</xsl:when>
				<xsl:when test="../../../values/field[@name=$name]"><xsl:value-of select="../../../values/field[@name=$name]"/></xsl:when>
				<xsl:when test="(string-length(value)=0 and string-length(.)=0 and string-length(label)!=0 and //setting[@name='sp_wai_forms']!='No') or (string-length(value)=0 and string-length(.)=0 and string-length(@label)!=0 and //setting[@name='sp_wai_forms']!='No') or (string-length(.)=0 and string-length(@value)=0 and //setting[@name='sp_wai_forms']!='No')">[[lt]]<xsl:call-template name="get_translation">
						<xsl:with-param name="check" select="$label"/>
						<xsl:with-param name="maxlen" select="50"/>
					</xsl:call-template>[[gt]]</xsl:when>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
				</xsl:choose></xsl:attribute>
			   	<xsl:if test="@size">
			   		<xsl:attribute name="maxlength"><xsl:value-of select="@size"/></xsl:attribute>
			   	</xsl:if>
		   	</input>
		   	</div>
  		</div>
  		</xsl:when>
  		<xsl:when test="@type='text'">
	   		<xsl:if test="$labelinnewrow=1">
			   	<div class='row'><label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
					<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
					<xsl:when test="boolean(label)"><xsl:value-of select="label"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
				</xsl:choose></xsl:with-param> </xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></label></div>
			</xsl:if>
    	<div class='row'>
			<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
			<xsl:variable name="label"><xsl:choose>
					<xsl:when test="boolean(label)"><xsl:value-of select="label"/></xsl:when>
					<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
				</xsl:choose></xsl:variable>
    		<xsl:if test="$labelinnewrow=0">
				<div class='cell'>
				<label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
				<xsl:attribute name="class">advlabel</xsl:attribute>
				<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="$label"/></xsl:with-param></xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></label></div>
			</xsl:if>

		   	<div class='cell'>
			<xsl:choose>
    		<xsl:when test="$labelinnewrow=0 and boolean(@adv=0)"><xsl:attribute name="class">advcell</xsl:attribute></xsl:when>
			<xsl:otherwise><xsl:attribute name="class">cell</xsl:attribute></xsl:otherwise>
			</xsl:choose>
			<input type='text'>
			<xsl:variable name="value"><xsl:choose>
				<xsl:when test="../../../values/field[@name=$name]"><xsl:value-of select="../../../values/field[@name=$name]"/></xsl:when>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No' and string-length($value)=0">
				<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
			</xsl:if>
			<xsl:attribute name="value"><xsl:choose>
				<xsl:when test="../../../values/field[@name=$name]"><xsl:value-of select="../../../values/field[@name=$name]"/></xsl:when>
				<xsl:when test="(string-length(value)=0 and string-length(.)=0 and string-length(label)!=0 and //setting[@name='sp_wai_forms']!='No') or (string-length(value)=0 and string-length(.)=0 and string-length(@label)!=0 and //setting[@name='sp_wai_forms']!='No') or (string-length(.)=0 and string-length(@value)=0 and //setting[@name='sp_wai_forms']!='No')">[[lt]]<xsl:call-template name="get_translation">
						<xsl:with-param name="check" select="$label"/>
						<xsl:with-param name="maxlen" select="50"/>
					</xsl:call-template>[[gt]]</xsl:when>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
				</xsl:choose></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
			<xsl:if test="@adv='0'">
			<xsl:attribute name="class">advinput</xsl:attribute>
			</xsl:if>
		   	<xsl:if test="@size">
				<xsl:choose>
					<xsl:when test="$extract_pos=1 or $extract_pos=4"></xsl:when>
    				<xsl:when test="@size >20">
		   				<xsl:attribute name="style">width:99%</xsl:attribute>
		   			</xsl:when>
		   			<xsl:otherwise>
		   				<xsl:attribute name="style">width:<xsl:value-of select="@size*10"/>px</xsl:attribute>
			   			</xsl:otherwise>
		   		</xsl:choose>
		   		<xsl:attribute name="maxlength"><xsl:value-of select="@maxlength"/></xsl:attribute>
		   	</xsl:if>
		   	</input>
			<xsl:if test="@adv='0'">
				<input class="advbutton" type='submit'>
					<xsl:attribute name="value">Go</xsl:attribute>
				</input>
			</xsl:if>

		   	</div>
  		</div>
  		</xsl:when>
  		<xsl:when test="@type='password'">
    	<div class='row'>
    		<xsl:if test="$labelinnewrow=0">
		   	<div class='cell'><label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></label></div>
			</xsl:if>
			<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
		   	<div class='cell'><input type='password'>
					<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
						<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
					</xsl:if>
			<xsl:attribute name="value"><xsl:choose>
				<xsl:when test="../../../values/field[@name=$name]"><xsl:value-of select="../../../values/field[@name=$name]"/></xsl:when>
				<xsl:when test="string-length(.)=0 and string-length(@value)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/><xsl:with-param name="maxlen" select="50"/></xsl:call-template>[[gt]]</xsl:when>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
				</xsl:choose></xsl:attribute>
		   	<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:if test="@size">
				<xsl:choose>
    				<xsl:when test="@size>40">
		   				<xsl:attribute name="style">width:99%</xsl:attribute>
		   			</xsl:when>
		   			<xsl:otherwise>
		   				<xsl:attribute name="style">width:<xsl:value-of select="@size*8"/>px</xsl:attribute>
		   			</xsl:otherwise>
		   		</xsl:choose>
		   		<xsl:attribute name="maxlength"><xsl:value-of select="@size"/></xsl:attribute>
		   	</xsl:if>
		   	</input>
			</div>
  		</div>

  		</xsl:when>
  		<xsl:when test="@type='date_time' or @type='datetime' or @type='date' or @type='time'">
    	<div class='row'>
		   	<div class='cell'><label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label>  <xsl:if test="@required"><span class="required">*</span></xsl:if></div>
<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
		   	<div class='cell'>
			<input type='hidden'>
				<xsl:attribute name='name'><xsl:value-of select='@name'/></xsl:attribute>
				<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
				<xsl:attribute name='value'><xsl:value-of select='@value'/></xsl:attribute>
			</input>
			<xsl:variable name="year"><xsl:value-of select="substring-before(@value,'-')"/></xsl:variable>
			<xsl:variable name="month"><xsl:value-of select="substring-before(substring-after(@value,'-'),'-')"/></xsl:variable>
			<xsl:variable name="day"><xsl:value-of select="substring-before(substring-after(substring-after(@value,'-'),'-'),' ')"/></xsl:variable>
			<xsl:variable name="hour"><xsl:value-of select="substring-before(substring-after(@value,' '),':')"/></xsl:variable>
			<xsl:variable name="year_start"><xsl:choose><xsl:when test="@year_start"><xsl:value-of select="@year_start"/></xsl:when><xsl:otherwise><xsl:value-of select="//setting[@name='year'] - 1"/></xsl:otherwise></xsl:choose></xsl:variable>
			<xsl:variable name="year_end"><xsl:choose><xsl:when test="@year_start"><xsl:value-of select="@year_end"/></xsl:when><xsl:otherwise><xsl:value-of select="//setting[@name='year'] + 1"/></xsl:otherwise></xsl:choose></xsl:variable>
			<div class='dateformat'>
			<xsl:if test="contains(@type,'date')">
			<select class="day"><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_day</xsl:attribute>
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$day"/>
			   		<xsl:with-param name="start" select="1"/>
			   		<xsl:with-param name="end" select="31"/>
			   		<xsl:with-param name="type" select="day"/>
			 	</xsl:call-template>
			</select>
			<select class="month"><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_month</xsl:attribute>
				<option value='01'><xsl:if test="$month='01'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Jan</option>
				<option value='02'><xsl:if test="$month='02'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Feb</option>
				<option value='03'><xsl:if test="$month='03'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Mar</option>
				<option value='04'><xsl:if test="$month='04'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Apr</option>
				<option value='05'><xsl:if test="$month='05'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>May</option>
				<option value='06'><xsl:if test="$month='06'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Jun</option>
				<option value='07'><xsl:if test="$month='07'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Jul</option>
				<option value='08'><xsl:if test="$month='08'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Aug</option>
				<option value='09'><xsl:if test="$month='09'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Sept</option>
				<option value='10'><xsl:if test="$month='10'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Oct</option>
				<option value='11'><xsl:if test="$month='11'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Nov</option>
				<option value='12'><xsl:if test="$month='12'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Dec</option>
			</select>
			<select class='year'><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_year</xsl:attribute>
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$year"/>
			   		<xsl:with-param name="start" select="$year_start"/>
			   		<xsl:with-param name="end" select="$year_end"/>
			   		<xsl:with-param name="type" select="year"/>
			 	</xsl:call-template>
			</select>
				</xsl:if>
				<xsl:if test="contains(@type,'time')">
			<select class="time"><xsl:attribute name='name'><xsl:value-of select='@name'/>_date_hour</xsl:attribute>
				<option value=''>Time</option>
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$hour"/>
			   		<xsl:with-param name="start" select="1"/>
			   		<xsl:with-param name="end" select="24"/>
			   		<xsl:with-param name="type" >hour</xsl:with-param>
			 	</xsl:call-template>
			</select>
				</xsl:if>
				</div>
			</div>
  		</div>
  		</xsl:when>
  		<xsl:when test="@type='ccdate'">
    	<div class='row'>
		   	<div class='cell'><label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label>  <xsl:if test="@required"><span class="required">*</span></xsl:if></div>
<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
		   	<div class='cell'>
			<input type='hidden'>
				<xsl:attribute name='name'><xsl:value-of select='@name'/></xsl:attribute>
				<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
				<xsl:attribute name='value'><xsl:value-of select='@value'/></xsl:attribute>
			</input>
			<xsl:variable name="year"><xsl:value-of select="substring-before(@value,'-')"/></xsl:variable>
			<xsl:variable name="month"><xsl:value-of select="substring-before(substring-after(@value,'-'),'-')"/></xsl:variable>
			<xsl:variable name="day"><xsl:value-of select="substring-before(substring-after(substring-after(@value,'-'),'-'),' ')"/></xsl:variable>
			<xsl:variable name="hour"><xsl:value-of select="substring-before(substring-after(@value,' '),':')"/></xsl:variable>
			<xsl:variable name="year_start"><xsl:choose><xsl:when test="@year_start"><xsl:value-of select="@year_start"/></xsl:when><xsl:otherwise><xsl:value-of select="//setting[@name='year'] - 1"/></xsl:otherwise></xsl:choose></xsl:variable>
			<xsl:variable name="year_end"><xsl:choose><xsl:when test="@year_end"><xsl:value-of select="@year_end"/></xsl:when><xsl:otherwise><xsl:value-of select="//setting[@name='year'] + 1"/></xsl:otherwise></xsl:choose></xsl:variable>
			<select><xsl:attribute name='name'><xsl:value-of select='@name'/>_m</xsl:attribute>
				<option value=''></option>
				<option value='01'><xsl:if test="$month='01'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>01</option>
				<option value='02'><xsl:if test="$month='02'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>02</option>
				<option value='03'><xsl:if test="$month='03'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>03</option>
				<option value='04'><xsl:if test="$month='04'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>04</option>
				<option value='05'><xsl:if test="$month='05'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>05</option>
				<option value='06'><xsl:if test="$month='06'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>06</option>
				<option value='07'><xsl:if test="$month='07'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>07</option>
				<option value='08'><xsl:if test="$month='08'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>08</option>
				<option value='09'><xsl:if test="$month='09'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>09</option>
				<option value='10'><xsl:if test="$month='10'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>10</option>
				<option value='11'><xsl:if test="$month='11'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>11</option>
				<option value='12'><xsl:if test="$month='12'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>12</option>
			</select>
			<select><xsl:attribute name='name'><xsl:value-of select='@name'/>_y</xsl:attribute>
				<option value=''></option>
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$year"/>
			   		<xsl:with-param name="start" select="$year_start"/>
			   		<xsl:with-param name="end" select="$year_end"/>
			   		<xsl:with-param name="type" select="year"/>
			 	</xsl:call-template>
			</select>
			</div>
  		</div>
  		</xsl:when>
  		<xsl:when test="@type='file'">
    	<div class='row'>
    		<xsl:if test="$labelinnewrow=0">
		   	<div class='cell'><label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></label></div>
			</xsl:if>
		   	<div class='cell'>
		   	<input type='file'>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	</input>
		   	</div>
  		</div>
		</xsl:when>
  		<xsl:when test="@type='hidden' and string-length(.)=0">
    	<div class='row'>
		   	<input type='hidden'>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
		   	</input>
  		</div>
		</xsl:when>

	</xsl:choose>
</xsl:template>

<xsl:template name="display_date">
	<xsl:param name="current"/>
	<xsl:param name="start"/>
	<xsl:param name="end"/>
	<xsl:param name="type"/>

	<option ><xsl:if test="$current = $start"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:attribute name="value"><xsl:value-of select="$start"/></xsl:attribute><xsl:value-of select="$start"/><xsl:if test="$type='hour'">:00</xsl:if></option>
	<xsl:if test="$start != $end">
				<xsl:call-template name="display_date">
			   		<xsl:with-param name="current" select="$current"/>
			   		<xsl:with-param name="start" select="$start + 1"/>
			   		<xsl:with-param name="end" select="$end"/>
			   		<xsl:with-param name="type" select="$type"/>
			 	</xsl:call-template>
	</xsl:if>
</xsl:template>




<xsl:template match="text">
<xsl:choose>
	<xsl:when test="local-name(..)='module'"><xsl:choose>
		<xsl:when test="@clear=1"><xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:otherwise><p>
			<xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</p></xsl:otherwise>
	</xsl:choose>
	</xsl:when>
	<xsl:otherwise>
		<div><xsl:choose>
		<xsl:when test="@type"><xsl:attribute name="class">error</xsl:attribute></xsl:when>
		<xsl:when test="@class"><xsl:attribute name="class"><xsl:value-of select="@class"/></xsl:attribute></xsl:when>
		<xsl:otherwise><xsl:attribute name="class">text</xsl:attribute></xsl:otherwise>
	</xsl:choose><span><xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></span></div>
</xsl:otherwise></xsl:choose>
</xsl:template>


<xsl:template match="radio">
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="uid"></xsl:param>
	<xsl:param name="mycounter">3</xsl:param>
	<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="@type='vertical'">
			<xsl:choose>
				<xsl:when test="$labelinnewrow=0 and @label">
				   	<div class='row'>
						<div class="cell"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template><xsl:if test="@required"><span class="required">*</span></xsl:if></div>
						<div class='cell'><xsl:call-template name="radioElements">
					<xsl:with-param name="mycounter"><xsl:value-of select="$mycounter"/></xsl:with-param>
				</xsl:call-template></div>
					</div>
				</xsl:when>
				<xsl:otherwise><xsl:call-template name="radioElements">
					<xsl:with-param name="mycounter"><xsl:value-of select="$mycounter"/></xsl:with-param>
				</xsl:call-template></xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<xsl:otherwise>
		<div class='row'>
			<xsl:if test="$labelinnewrow=0 and @label">
				<div class="cell"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template><xsl:if test="@required"><span class="required">*</span></xsl:if></div>
			</xsl:if>
			<div class="cell">
	   		<xsl:for-each select="option">
				<input class="checkbox" type="radio">
				   	<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
					<xsl:attribute name='id'><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute>
					<xsl:attribute name="value"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
					<xsl:choose>
					<xsl:when test="((@checked='true' or @selected='true') and  not(../../../../values/field[@name=$name])) or (//setting[@name='sp_wai_forms']='Yes' and position()=1 and not(boolean(../options/@checked='true' or ../options/@selected='true')))"><xsl:attribute name="checked">checked</xsl:attribute></xsl:when>
					<xsl:otherwise><xsl:if test="../../../../values/field[@name=$name]=@value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></xsl:otherwise>
				</xsl:choose>
				<xsl:if test="../../@onclick">
					<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
				</xsl:if>
		   		</input>[[nbsp]]<label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template></label>
   		</xsl:for-each>
		</div></div>
	</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template name="radioElements">
	<xsl:param name="mycounter">0</xsl:param>
<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
	   		<xsl:for-each select="option">
				<div class='radiooption'><input class="checkbox" type="radio">
					   	<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
						<xsl:attribute name='id'><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute>
						<xsl:attribute name="value"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
						<xsl:choose>
							<xsl:when test="(@checked='true' or @selected='true') and not(//values/fields[@name=$name])"><xsl:attribute name="checked">checked</xsl:attribute></xsl:when>
							<xsl:when test=". = //values/field[@name=$name]"><xsl:attribute name="checked">checked</xsl:attribute></xsl:when>
							<xsl:otherwise><xsl:if test="//setting[@name='sp_wai_forms']='Yes' and not(../option/@checked) and position()=1"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></xsl:otherwise>
						</xsl:choose>
						<xsl:if test="../../@onclick">
							<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
						</xsl:if>
			   			</input>[[nbsp]]<label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template></label>
		   		</div>
			</xsl:for-each>
		   	<xsl:if test="@other='true'">
				<div class='row'><input type="radio" class="checkbox">
					   	<xsl:attribute name="name">other_entry_<xsl:value-of select="@name"/></xsl:attribute>
					   	<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 1"/></xsl:attribute>
						<xsl:attribute name="value">_system_defined_other_</xsl:attribute>
						<xsl:choose>
							<xsl:when test="(@checked='true' or @selected='true') and $mycounter='0'"><xsl:if test="(@checked='true' or @selected='true')"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></xsl:when>
							<xsl:otherwise>
								<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
								<xsl:for-each select="//values/field[@name=$name]"><xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></xsl:for-each>
							</xsl:otherwise>
						</xsl:choose>
		   				<xsl:if test="../@onclick">
							<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
						</xsl:if>
			   			</input>[[nbsp]]<label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 1"/></xsl:attribute><xsl:value-of select="other_label" disable-output-escaping="yes"/></label>
				</div>
				<div class='row'>
					<div class='cell'><label class="otherspecify"><xsl:attribute name="for">other_entry_<xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 2"/></xsl:attribute><xsl:value-of select="other_label" disable-output-escaping="yes"/></label>
						<input type="text" size="20" maxlenght="255">
					<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
						<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
					</xsl:if>
							<xsl:attribute name="name">other_entry_<xsl:value-of select="@name"/></xsl:attribute>
						   	<xsl:attribute name="id">other_entry_<xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 2"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:choose>
								<xsl:when test="//values/field[@name=./@name]"><xsl:value-of select="//values/field[@name=./@name]"/></xsl:when>
								<xsl:when test="string-length(other_label)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="other_label"/></xsl:call-template>[[gt]]</xsl:when>
								<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="other_label"/></xsl:otherwise>
							</xsl:choose></xsl:attribute>
						</input>
					</div>
				</div>
			</xsl:if>
</xsl:template>

<xsl:template match="form_subject">
	<xsl:param name="uid"></xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
   	<div class='row'>
   		<xsl:if test="$labelinnewrow=0 and @label">
	   	<div class='cell'><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template><xsl:if test="@required"><span class="required">*</span></xsl:if></div>
	</xsl:if>
	<div class='cell'>
	<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="@type='radio'">
			<div class='table'>
	   			<div class='row'><div class='cell'>
				<xsl:variable name="field_name"><xsl:value-of select="@name"/></xsl:variable>
				<xsl:for-each select="//xml_document/modules/container/webobject/module/form/emails/option">
				<input type='radio' class="checkbox">
					<xsl:attribute name="name">form_subject</xsl:attribute>
					<xsl:attribute name="id">fb_<xsl:value-of select="$field_name"/>_<xsl:value-of select="position()"/></xsl:attribute>
					<xsl:attribute name="value"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
					<xsl:choose>
						<xsl:when test="(@checked='true' or @selected='true') and not(../../values/fields[@name=$name])">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:if test=". = ../../values/field[@name=$name]"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
						</xsl:otherwise>
					</xsl:choose>
				</input>
				<label>
					<xsl:attribute name="for">fb_<xsl:value-of select="$field_name"/>_<xsl:value-of select="position()"/></xsl:attribute>
					<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template>
				</label><br/>
					</xsl:for-each>
				</div></div>
			</div>
		</xsl:when>
		<xsl:when test="@type='checkbox'">
			<div class='table'>
	   			<div class='row'><div class='cell'>
				<xsl:variable name="field_name"><xsl:value-of select="@name"/></xsl:variable>
				<xsl:for-each select="//xml_document/modules/container/webobject/module/form/emails/option">
				<input type='checkbox' class="checkbox">
					<xsl:attribute name="name">form_subject[]</xsl:attribute>
					<xsl:attribute name="id">fb_<xsl:value-of select="$field_name"/>_<xsl:value-of select="position()"/></xsl:attribute>
					<xsl:attribute name="value"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
					<xsl:choose>
						<xsl:when test="(@checked='true' or @selected='true') and not(../../values/fields[@name=$name])">
							<xsl:attribute name="checked">checked</xsl:attribute>
						</xsl:when>
						<xsl:otherwise>
							<xsl:if test=". = ../../values/field[@name=$name]"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
						</xsl:otherwise>
					</xsl:choose>
				</input>
				<label>
					<xsl:attribute name="for">fb_<xsl:value-of select="$field_name"/>_<xsl:value-of select="position()"/></xsl:attribute>
					<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template>
				</label><br/>
					</xsl:for-each>
				</div></div>
			</div>
		</xsl:when>
		<xsl:otherwise>
				<select>
					<xsl:attribute name="name">form_subject</xsl:attribute>
					<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="$uid"/></xsl:attribute>
					<xsl:for-each select="//xml_document/modules/container/webobject/module/form/emails/option">
					<option>
						<xsl:attribute name="value"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
						<xsl:choose>
							<xsl:when test="(@checked='true' or @selected='true') and not(../../values/fields[@name=$name])">
							<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:if test=". = ../../values/field[@name=$name]"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template>
					</option>
					</xsl:for-each>
				</select>
		</xsl:otherwise>
	</xsl:choose></div></div>
</xsl:template>

<xsl:template match="seperator_page">
	<xsl:param name="uid"></xsl:param>
	<xsl:param name="entry_url"></xsl:param>
	<xsl:param name="entry_identifier"></xsl:param>
	<xsl:param name="extract_pos">2</xsl:param>
	<div class='form_page'>
		<xsl:attribute name="id">form_<xsl:value-of select="@id"/></xsl:attribute>
		<xsl:apply-templates>
			<xsl:with-param name="count_children"><xsl:value-of select="count(seperator)"/></xsl:with-param>
			<xsl:with-param name='entry_url'><xsl:value-of select="$entry_url"/></xsl:with-param>
			<xsl:with-param name='uid'><xsl:value-of select="$uid"/></xsl:with-param>
			<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
			<xsl:with-param name="entry_identifier"><xsl:value-of select="$entry_identifier"/></xsl:with-param>
		</xsl:apply-templates>
	</div>
</xsl:template>
<xsl:template match="seperator_row">
	<xsl:param name="uid"></xsl:param>
	<xsl:param name="entry_url"></xsl:param>
	<xsl:param name="extract_pos">2</xsl:param>
	<xsl:param name="entry_identifier"></xsl:param>
	<div class='row'><xsl:apply-templates>
		<xsl:with-param name="count_children"><xsl:value-of select="count(seperator)"/></xsl:with-param>
		<xsl:with-param name='entry_url'><xsl:value-of select="$entry_url"/></xsl:with-param>
		<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
		<xsl:with-param name='uid'><xsl:value-of select="$uid"/></xsl:with-param>
		<xsl:with-param name="entry_identifier"><xsl:value-of select="$entry_identifier"/></xsl:with-param>
	</xsl:apply-templates></div>
</xsl:template>
<xsl:template match="seperator">
	<xsl:param name="uid"></xsl:param>
	<xsl:param name="count_children"></xsl:param>
	<xsl:param name="entry_url"></xsl:param>
	<xsl:param name="extract_pos">2</xsl:param>
	<xsl:param name="entry_identifier"></xsl:param>
	<div><xsl:attribute name="class">columncount<xsl:value-of select="$count_children"/></xsl:attribute><xsl:apply-templates>
		<xsl:with-param name='entry_url'><xsl:value-of select="$entry_url"/></xsl:with-param>
		<xsl:with-param name="extract_pos"><xsl:value-of select="$extract_pos"/></xsl:with-param>
		<xsl:with-param name='uid'><xsl:value-of select="$uid"/></xsl:with-param>
		<xsl:with-param name="entry_identifier"><xsl:value-of select="$entry_identifier"/></xsl:with-param>
	</xsl:apply-templates></div>
</xsl:template>
<xsl:template match="link">
	<xsl:param name="uid"></xsl:param>
:: <a><xsl:attribute name="title"><xsl:value-of select ="@title" disable-output-escaping="yes"/></xsl:attribute><xsl:attribute name="href">?command=<xsl:value-of select ="@command" disable-output-escaping="yes"/></xsl:attribute><xsl:value-of select ="@title" disable-output-escaping="yes"/></a>
</xsl:template>
</xsl:stylesheet>