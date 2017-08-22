<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.10 $
- Modified $Date: 2005/02/03 08:05:24 $
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
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="uid"></xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="show_label">0</xsl:param>
	<xsl:param name="show_label_bullet">0</xsl:param>
	<xsl:if test="$formname=@name or $formname=''"><hr/>
	<form>
	<xsl:attribute name="action"><xsl:choose>
	<xsl:when test="@action"><xsl:value-of select="@action" disable-output-escaping="yes"/></xsl:when>
	<xsl:when test="action"><xsl:value-of select="action" disable-output-escaping="yes"/></xsl:when>
	<xsl:otherwise><xsl:value-of select="//setting[@name='real_script']"/></xsl:otherwise>
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
	<div>
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
		<xsl:when test="$intable=0">
			<xsl:choose>
				<xsl:when test="$show_label=1"></xsl:when>
				<xsl:otherwise>
					<p class="TableHeader">
						<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
							<xsl:when test="@label"><xsl:value-of select="@label" disable-output-escaping="yes"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:with-param></xsl:call-template>
					</p>
				</xsl:otherwise>
			</xsl:choose>
			<xsl:choose>
				<xsl:when test="seperator_row/seperator">
					<xsl:if test=".//@required">
						<span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template><br/>
					</xsl:if>
					<xsl:apply-templates>
							<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
					</xsl:apply-templates><br/>
					<xsl:call-template name="display_submit_buttons"/>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test=".//@required">
						<span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template><br/>
					</xsl:if>
					<xsl:apply-templates/>
					<xsl:call-template name="display_submit_buttons">
						<xsl:with-param name="submit_align"><xsl:value-of select="$submit_align"/></xsl:with-param>
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:when>
		<!-- // $intable == 1 -->
		<xsl:otherwise>
			<xsl:choose>
				<xsl:when test="seperator_row/seperator">
			 		<xsl:if test=".//@required">
						<p><span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template></p>
					</xsl:if>
					<xsl:apply-templates/>
					<xsl:call-template name="display_submit_buttons"/>
				</xsl:when>
				<xsl:otherwise>
			 		<xsl:if test=".//@required">
						<p><span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template></p>
					</xsl:if>
					<xsl:for-each select="./child::*">
						<xsl:variable name="current"><xsl:value-of select="position()"/></xsl:variable>
						<xsl:choose>
				    		<xsl:when test="$labelinnewrow=1 and (@type!='hidden' or @type!='submit' or @type!='button')">
		    					<p><label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute>
									<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if></p>
								<p><xsl:apply-templates select=".">
												<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
											</xsl:apply-templates></p>
						</xsl:when>
						<xsl:otherwise>
							<xsl:apply-templates select="."><xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param></xsl:apply-templates>
						</xsl:otherwise>
					</xsl:choose>
					</xsl:for-each>
					<xsl:call-template name="display_submit_buttons">
						<xsl:with-param name="submit_align"><xsl:value-of select="$submit_align"/></xsl:with-param>
					</xsl:call-template>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
	</form>

	<xsl:if test="../text">
	<xsl:for-each select="../text"><xsl:value-of select="."/></xsl:for-each>
	</xsl:if>
</xsl:if>
</xsl:template>

<xsl:template name="display_submit_buttons">
	<xsl:param name="submit_align">center</xsl:param>
		<p>
	  	 	<xsl:for-each select="input">
  				<xsl:if test="@type='submit'">
					<input class="button" type='submit'>
						<xsl:attribute name="value"><xsl:call-template name="get_translation">
							<xsl:with-param name="check"><xsl:choose>
								<xsl:when test="@alt!=''"><xsl:value-of select="@alt"/></xsl:when>
								<xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
							</xsl:choose></xsl:with-param>
						</xsl:call-template></xsl:attribute>
					   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
				   	</input>
				</xsl:if>
  				<xsl:if test="@type='button'">
				<xsl:choose>
				<xsl:when test="$image_path = '/libertas_images/themes/pda'"><br/>[[rightarrow]][[nbsp]]<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=<xsl:value-of select="@command"/></xsl:attribute>
					<xsl:attribute name="class">button</xsl:attribute><xsl:call-template name="get_translation">
						<xsl:with-param name="check"><xsl:choose>
							<xsl:when test="@alt!=''"><xsl:value-of select="@alt"/></xsl:when>
							<xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
						</xsl:choose></xsl:with-param>
					</xsl:call-template></a>
					</xsl:when>
				<xsl:otherwise>
				<a><xsl:attribute name="href"><xsl:value-of select="//setting[@name='real_script']"/>?command=<xsl:value-of select="@command"/></xsl:attribute>
					<xsl:attribute name="class">button</xsl:attribute><xsl:call-template name="get_translation">
						<xsl:with-param name="check"><xsl:choose>
							<xsl:when test="@alt!=''"><xsl:value-of select="@alt"/></xsl:when>
							<xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
						</xsl:choose></xsl:with-param>
					</xsl:call-template></a>
					</xsl:otherwise>
					</xsl:choose>
				</xsl:if>

		   	</xsl:for-each></p>
</xsl:template>

<xsl:template match="select">
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
		<xsl:if test="$labelinnewrow=0">
		   	<label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if><br/>
		</xsl:if>
		   	<select>
   			<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/><xsl:if test="@multiple='1'">[]</xsl:if></xsl:attribute>
			<xsl:if test="@multiple='1'"><xsl:attribute name="multiple">1</xsl:attribute>
				<xsl:if test="@size">
					<xsl:attribute name="size"><xsl:value-of select="@size"/></xsl:attribute>
				</xsl:if>
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
			<xsl:if test="other_label"><option value="_system_defined_other_"><xsl:value-of select="other_label"/></option></xsl:if>
		   	</select><xsl:if test="other_label">
				<br/>
					<label class="otherspecify"><xsl:attribute name="for">other_entry_<xsl:value-of select="@name"/></xsl:attribute><xsl:value-of select="other_label" disable-output-escaping="yes"/></label>
					<input type="text" size="20" maxlenght="255">
						<xsl:attribute name="name">other_entry_<xsl:value-of select="@name"/></xsl:attribute>
					   	<xsl:attribute name="id">other_entry_<xsl:value-of select="@name"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:choose>
							<xsl:when test="//values/field[@name=./@name]"><xsl:value-of select="//values/field[@name=./@name]"/></xsl:when>
							<xsl:when test="string-length(other_label)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="other_label"/></xsl:call-template>[[gt]]</xsl:when>
							<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="other_label"/></xsl:otherwise>
						</xsl:choose></xsl:attribute>
					</input>
			</xsl:if>
		<br/>
</xsl:template>

<xsl:template match="textarea">
<xsl:param name="labelinnewrow">0</xsl:param>

		<xsl:if test="$labelinnewrow=0 and @label">
<label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute>
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label><br/>
 <xsl:if test="@required"><span class="required">*</span></xsl:if><br/>
			</xsl:if>
			<xsl:if test="not(@label)"><xsl:attribute name="colspan">2</xsl:attribute><xsl:attribute name="align">center</xsl:attribute></xsl:if>
				<textarea>

		   			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   			<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
		   			<xsl:attribute name="cols"><xsl:choose>
						<xsl:when test="@size > 32">32</xsl:when>
						<xsl:otherwise><xsl:value-of select="@size"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
					<xsl:attribute name="rows"><xsl:value-of select="@height"/></xsl:attribute>
					<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
					<xsl:choose>
						<xsl:when test="//values/field[@name=$name]"><xsl:value-of select="//values/field[@name=$name]"/></xsl:when>
						<xsl:when test="string-length(.)=0 and string-length(@value)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template>[[gt]]</xsl:when>
						<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
					</xsl:choose>
				</textarea>
		   	<br/>
</xsl:template>

<xsl:template match="checkboxes">
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
	<xsl:variable name="lengthOfEntries"><xsl:for-each select="//values/field[@name=$name]">x</xsl:for-each></xsl:variable>
	<xsl:variable name="counter"><xsl:value-of select="string-length($lengthOfEntries)"/></xsl:variable>
<xsl:choose>
	<xsl:when test="options">
	<xsl:if test="$labelinnewrow=0"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if></xsl:if><br />
		<xsl:if test="@type='vertical'">
			<xsl:for-each select="options">
				<xsl:sort select="@module"/>
				<b><xsl:value-of select="@module"/></b><br />
				<xsl:for-each select="option">
					<input type="checkbox" class="radiocheckbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:choose>
								<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
								<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
							</xsl:choose></xsl:attribute>
							<xsl:choose>
								<xsl:when test="(@checked='true' or @selected='true') and $counter='0'">
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
					<label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label><br />
	   			</xsl:for-each>

	   		</xsl:for-each>
	   	</xsl:if>
		<xsl:if test="@type='horizontal'">
			<xsl:for-each select="options">
				<h2 class="TableHeader"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@module"/></xsl:call-template></h2>
				<xsl:for-each select="option">
					<input class="checkbox" type="checkbox">
					   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
						<xsl:attribute name="value"><xsl:choose>
							<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:attribute>
				   		<xsl:attribute name="id"><xsl:value-of select="../../@name"/></xsl:attribute>
						<xsl:choose>
							<xsl:when test="(@checked='true' or @selected='true') and $counter='0'">
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
			   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label><br/>
				</xsl:for-each>
	  		</xsl:for-each>
		</xsl:if>
	</xsl:when>
	<xsl:otherwise>
			<xsl:if test="$labelinnewrow=0 and @label">
		   	<h2 class="TableHeader"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template>
				 <xsl:if test="@required"><span class="required">*</span></xsl:if>
			</h2>
			</xsl:if>
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
								<xsl:when test="(@checked='true' or @selected='true') and $counter='0'">
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
								<xsl:when test="(@checked='true' or @selected='true') and $counter='0'">
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
								<xsl:when test="(@checked='true' or @selected='true') and $counter='0'">
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
							<xsl:when test="(@checked='true' or @selected='true') and $counter='0'">
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
	</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template match="input">
	<xsl:param name="labelinnewrow">0</xsl:param>

	<xsl:choose>
  		<xsl:when test="@type='text'">

    		<xsl:if test="$labelinnewrow=0">
		   	<label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if><br/>
			</xsl:if>
			<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
			<input type='text'>

			<xsl:attribute name="value"><xsl:choose>
				<xsl:when test="../../../values/field[@name=$name]"><xsl:value-of select="../../../values/field[@name=$name]"/></xsl:when>
				<xsl:when test="string-length(.)=0 and string-length(@value)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/><xsl:with-param name="maxlen" select="50"/></xsl:call-template>[[gt]]</xsl:when>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
				</xsl:choose></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:if test="@size">
				<xsl:choose>
    				<xsl:when test="@size * 8 > 160">
		   				<xsl:attribute name="style">width:160px</xsl:attribute>
		   			</xsl:when>
		   			<xsl:otherwise>
		   				<xsl:attribute name="style">width:<xsl:value-of select="@size*8"/>px</xsl:attribute>
			   			</xsl:otherwise>
		   		</xsl:choose>
		   		<xsl:attribute name="maxlength"><xsl:value-of select="@size"/></xsl:attribute>
		   	</xsl:if>
		   	</input><br/>
  		</xsl:when>
  		<xsl:when test="@type='password'">

    		<xsl:if test="$labelinnewrow=0">
		   	<label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if><br/>
			</xsl:if>
		   	<input type='password'>

			<xsl:attribute name="value"><xsl:choose>
				<xsl:when test="../../../values/field[@name=$name]"><xsl:value-of select="../../../values/field[@name=$name]"/></xsl:when>
				<xsl:when test="string-length(.)=0 and string-length(@value)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/><xsl:with-param name="maxlen" select="50"/></xsl:call-template>[[gt]]</xsl:when>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
				</xsl:choose></xsl:attribute>
		   	<xsl:attribute name='id'><xsl:value-of select='@name'/></xsl:attribute>
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
		   	</input><br/>
  		</xsl:when>
  		<xsl:when test="@type='date_time'">
    		<label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label>  <xsl:if test="@required"><span class="required">*</span></xsl:if><br/>
			<input type='hidden'>
				<xsl:attribute name='name'><xsl:value-of select='@name'/></xsl:attribute>
				<xsl:attribute name='id'><xsl:value-of select='@name'/></xsl:attribute>
				<xsl:attribute name='value'><xsl:value-of select='@value'/></xsl:attribute>
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
				<option value='01'><xsl:if test="$month='01'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Janurary</option>
				<option value='02'><xsl:if test="$month='02'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>Feburary</option>
				<option value='03'><xsl:if test="$month='03'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>March</option>
				<option value='04'><xsl:if test="$month='04'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>April</option>
				<option value='05'><xsl:if test="$month='05'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>May</option>
				<option value='06'><xsl:if test="$month='06'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>June</option>
				<option value='07'><xsl:if test="$month='07'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>July</option>
				<option value='08'><xsl:if test="$month='08'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>August</option>
				<option value='09'><xsl:if test="$month='09'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>September</option>
				<option value='10'><xsl:if test="$month='10'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>October</option>
				<option value='11'><xsl:if test="$month='11'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>November</option>
				<option value='12'><xsl:if test="$month='12'"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>December</option>
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
			   		<xsl:with-param name="start" select="1"/>
			   		<xsl:with-param name="end" select="24"/>
			   		<xsl:with-param name="type" >hour</xsl:with-param>
			 	</xsl:call-template>
			</select><br/>
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
    		<xsl:if test="$labelinnewrow=0">
		   	<label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if>
			</xsl:if>
		   	<input type='file'>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	</input><br/>
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
		<xsl:when test="local-name(..)='module'">
			<p><xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></p>
		</xsl:when>
		<xsl:otherwise><p>
			<xsl:choose>
				<xsl:when test="@type"><xsl:attribute name="class">error</xsl:attribute></xsl:when>
				<xsl:otherwise><xsl:attribute name="class">TableCell</xsl:attribute></xsl:otherwise>
			</xsl:choose>
			<xsl:call-template name="extract_form_data"><xsl:with-param name="cdata"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>
		</p></xsl:otherwise>
	</xsl:choose>
</xsl:template>


<xsl:template match="radio">
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:if test="$labelinnewrow=0 and @label">
		<p><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template><xsl:if test="@required"><span class="required">*</span></xsl:if></p>
	</xsl:if>
	<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="@type='vertical'">
	   		<xsl:for-each select="option">
				<input class="checkbox" type="radio">
				   	<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
					<xsl:attribute name='id'><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute>
					<xsl:attribute name="value"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
					<xsl:choose>
						<xsl:when test="(@checked='true' or @selected='true') and not(//values/fields[@name=$name])"><xsl:attribute name="checked">checked</xsl:attribute></xsl:when>
						<xsl:when test=". = //values/field[@name=$name]"><xsl:attribute name="checked">checked</xsl:attribute></xsl:when>
						<xsl:otherwise><xsl:if test="not(../option/@checked) and position()=1"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></xsl:otherwise>
					</xsl:choose>
					<xsl:if test="../../@onclick">
						<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
					</xsl:if>
	   				</input>
					<label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template></label><br/>
			</xsl:for-each>
			<xsl:if test="@other='true'">
					<input type="radio" class="checkbox">
				   		<xsl:attribute name="name">other_entry_<xsl:value-of select="@name"/></xsl:attribute>
					   	<xsl:attribute name="id"><xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 1"/></xsl:attribute>
						<xsl:attribute name="value">_system_defined_other_</xsl:attribute>
						<xsl:choose>
							<xsl:when test="(@checked='true' or @selected='true') and $counter='0'"><xsl:if test="(@checked='true' or @selected='true')"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></xsl:when>
							<xsl:otherwise><xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable><xsl:for-each select="//values/field[@name=$name]"><xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></xsl:for-each></xsl:otherwise>
						</xsl:choose>
		   				<xsl:if test="../@onclick"><xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute></xsl:if>
		   			</input>
					<label><xsl:attribute name="for"><xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 1"/></xsl:attribute><xsl:value-of select="other_label" disable-output-escaping="yes"/></label>
			<label class="otherspecify"><xsl:attribute name="for">other_entry_<xsl:value-of select="@name"/>_<xsl:value-of select="count(option) + 2"/></xsl:attribute><xsl:value-of select="other_label" disable-output-escaping="yes"/></label>
					<input type="text" size="20" maxlenght="255">


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
		</xsl:when>
		<xsl:otherwise>
		   	<xsl:for-each select="option">
				<input class="checkbox" type="radio">
		   			<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
					<xsl:attribute name='id'><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute>
					<xsl:attribute name="value"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
					<xsl:choose>
						<xsl:when test="(@checked='true' or @selected='true') and not(../../../../values/field[@name=$name])"><xsl:attribute name="checked">checked</xsl:attribute></xsl:when>
						<xsl:otherwise><xsl:if test="../../../../values/field[@name=$name]=@value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></xsl:otherwise>
					</xsl:choose>
					<xsl:if test="../../@onclick">
						<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
					</xsl:if>
	   			</input>[[nbsp]]
				<label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template></label>
	   		</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template match="form_subject">
	<xsl:param name="labelinnewrow">0</xsl:param>
   		<xsl:if test="$labelinnewrow=0 and @label">
	   	<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template><xsl:if test="@required"><span class="required">*</span></xsl:if>
	</xsl:if>
	<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="@type='radio'">
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
		</xsl:when>
		<xsl:when test="@type='checkbox'">
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
		</xsl:when>
		<xsl:otherwise>
				<select>
					<xsl:attribute name="name">form_subject</xsl:attribute>
					<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
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
	</xsl:choose>
</xsl:template>
<xsl:template match="seperator_row">
	<xsl:apply-templates/>
</xsl:template>
<xsl:template match="seperator">
	<xsl:apply-templates />
</xsl:template>
<xsl:template match="link">
:: <a><xsl:attribute name="title"><xsl:value-of select ="@title" disable-output-escaping="yes"/></xsl:attribute><xsl:attribute name="href">?command=<xsl:value-of select ="@command" disable-output-escaping="yes"/></xsl:attribute><xsl:value-of select ="@title" disable-output-escaping="yes"/></a>
</xsl:template>

</xsl:stylesheet>