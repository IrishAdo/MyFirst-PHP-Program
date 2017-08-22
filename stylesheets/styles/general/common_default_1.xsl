<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.7 $
- Modified $Date: 2005/01/21 19:16:21 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0">

<xsl:include href="../../styles/general/display_shopping.xsl"/>
<xsl:template match="form">
	<xsl:param name="formname"></xsl:param>
	<xsl:param name="uid"></xsl:param>
	<xsl:if test="$formname=@name or $formname=''">
	<form >
		<xsl:attribute name="action"><xsl:choose>
	<xsl:when test="@action"><xsl:value-of select="@action" disable-output-escaping="yes"/></xsl:when>
	<xsl:when test="action"><xsl:value-of select="action" disable-output-escaping="yes"/></xsl:when>
	<xsl:otherwise><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='real_script']"/></xsl:otherwise>
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
	<xsl:if test="//setting[@name='displaymode']!=''">
		<input type="hidden" name="displaymode"><xsl:attribute name="value"><xsl:value-of select="//setting[@name='displaymode']"/></xsl:attribute></input>
	</xsl:if>
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

	<table border="0" cellpadding="0" cellspacing="0" summary="This table holds a form">
	<xsl:if test="@width">
		<xsl:attribute name="width"><xsl:value-of select="@width"/></xsl:attribute>
	</xsl:if>

		<tr>
		   	<td valign="top" class="TableBackground"><table border="0" cellpadding="3" cellspacing="1" width="100%" summary="This table holds the row information for the forms">
		<tr>
		   	<td valign="top" colspan="2" class="TableHeader"><span><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
				<xsl:when test="@label"><xsl:value-of select="@label" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template></span></td>
  		</tr>
		<xsl:if test=".//@required">
		<tr>
		   	<td valign="top" colspan="2" class="TableHeader"><span class="required">*</span><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_REQUIRED_FIELDS'"/></xsl:call-template></td>
  		</tr>
		</xsl:if>
		<xsl:choose>
			<xsl:when test="seperator"><tr><td><table><tr><xsl:apply-templates/></tr></table></td></tr></xsl:when>

			<xsl:otherwise><xsl:apply-templates/></xsl:otherwise>
		</xsl:choose>
		<tr>
		   	<td valign="top" colspan="2" class="TableCell" align="right">
	  	 	<xsl:for-each select="input">
  				<xsl:if test="@type='submit'"><input type='image'>
				   	<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
				   	<xsl:attribute name="alt">
					<xsl:call-template name="get_translation">
						<xsl:with-param name="check" select="@value"/>
					</xsl:call-template>
					</xsl:attribute>
				   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	</input><img src="/libertas_images/themes/1x1.gif" width="5" height="5"/></xsl:if>
  				<xsl:if test="@type='button'">
				<a><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='real_script']"/>?command=<xsl:value-of select="@command"/></xsl:attribute><img border="0">
				   	<xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_<xsl:value-of select="@iconify"/>.gif</xsl:attribute>
				   	<xsl:attribute name="alt">
					<xsl:call-template name="get_translation">
						<xsl:with-param name="check" select="@value"/>
					</xsl:call-template>
					</xsl:attribute>
				   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	</img></a><img src="/libertas_images/themes/1x1.gif" width="5" height="5"/></xsl:if>
		   	</xsl:for-each></td>
  		</tr>
	</table></td></tr></table>
	</form>
	<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
		<script type="text/javascript">
			__FRM_add('<xsl:value-of select="@name"/>');
		</script>
	</xsl:if>
</xsl:if>
</xsl:template>


<xsl:template match="select">
<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
		<tr class="TableCell">
		   	<td valign="top"><label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute>
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if></td>
		   	<td><select>
   			<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:if test="@multiple='1'"><xsl:attribute name="multiple">1</xsl:attribute>
				<xsl:if test="@size">
					<xsl:attribute name="size"><xsl:value-of select="@size"/></xsl:attribute>
				</xsl:if>
			</xsl:if>
		   	<xsl:for-each select="option">
			   	<option><xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute><xsl:choose>
					<xsl:when test="(@checked='true' or @selected='true') and not(../../values/fields[@name=$name])">
						<xsl:attribute name="selected">selected</xsl:attribute>
					</xsl:when>
					<xsl:otherwise>
						<xsl:if test="../../values/field[@name=$name]=@value"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
					</xsl:otherwise>
				</xsl:choose><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></option>
		   	</xsl:for-each>
		   	</select></td>
  		</tr>
</xsl:template>
<xsl:template match="textarea">
		<tr class="TableCell">
			<xsl:if test="@label">
		   	<td valign="top">
<label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute>
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label>
 <xsl:if test="@required"><span class="required">*</span></xsl:if></td>
			</xsl:if>
			<td><xsl:if test="not(@label)"><xsl:attribute name="colspan">2</xsl:attribute><xsl:attribute name="align">center</xsl:attribute></xsl:if>
					<textarea>
					<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
						<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
					</xsl:if>
		   			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   			<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
		   			<xsl:attribute name="cols"><xsl:value-of select="@size"/></xsl:attribute>
					<xsl:attribute name="rows"><xsl:value-of select="@height"/></xsl:attribute>
					<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
					<xsl:choose>
				<xsl:when test="../values/field[@name=$name]"><xsl:value-of select="../values/field[@name=$name]"/></xsl:when>
				<xsl:when test="string-length(.)=0 and string-length(@value)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/><xsl:with-param name="maxlen" select="50"/></xsl:call-template>[[gt]]</xsl:when>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
				</xsl:choose></textarea>
		   	</td>
  		</tr>
</xsl:template>

<xsl:template match="checkboxes">
	<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
<xsl:choose>
	<xsl:when test="options">
	<tr class="TableCell">
	   	<td valign="top" colspan="2"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template> <xsl:if test="@required"><span class="required">*</span></xsl:if><br />
		   	<xsl:if test="@type='vertical'">
			<table width="100%" border="0" cellpadding="3" cellspacing="0" summary="a selection of checkboxes you can check the ones you wish">
			<xsl:for-each select="options">
				<xsl:sort select="@module"/>
				<tr>
					<td><b><xsl:value-of select="@module"/></b><br />
					<xsl:for-each select="option">
					<input type="checkbox" class="radiocheckbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
			<xsl:choose>
			<xsl:when test="(@checked='true' or @selected='true') and not(../../../values/fields[@name=$name])">
				<xsl:attribute name="checked">checked</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="../../../values/field[@name=$name]=@value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
			</xsl:otherwise>
			</xsl:choose>
<!--							<xsl:if test="@selected='true'">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:if>
							-->
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
			   		</input>
					<label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label>
						<br />
		   			</xsl:for-each>
		   			</td>
		   		</tr>

		   		</xsl:for-each></table>
		   	</xsl:if>
		   	<xsl:if test="@type='horizontal'">
	   		   	<table width="100%" border="0" cellpadding="15" cellspacing="0" summary="you have been given a selection of check boxes which have been split into groups">
	   		   	<xsl:for-each select="options">
	   		   	<tr>
					<xsl:if test="(position() mod 3) = 1">
					<td valign="top"><table border="0" cellspacing="0" cellpadding="0" width="100%" summary="">
					<tr><td valign="top" class="TableBackground"><table border="0" cellspacing="1" cellpadding="0" width="100%" summary="a selection of checkboxes you can check the ones you wish">
					<tr><td class="TableHeader"><b><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@module"/></xsl:call-template></b></td></tr>
		   		   	<tr><td class="TableCell">
					<table width="100%" border="0" cellpadding="3" cellspacing="0" summary="">
					<xsl:for-each select="option">
					<tr>
						<td width="20%"><input class="checkbox" type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../../@name"/></xsl:attribute>
			<xsl:choose>
			<xsl:when test="(@checked='true' or @selected='true') and not(../../../values/fields[@name=$name])">
				<xsl:attribute name="checked">checked</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="../../../values/field[@name=$name]=@value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
			</xsl:otherwise>
			</xsl:choose>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
				   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label></td>
					</tr>
					</xsl:for-each>
				   	</table></td></tr></table></td></tr></table></td>
					<xsl:if test="following-sibling::options[position()=1]">
					<td valign="top"><table border="0" cellspacing="0" cellpadding="0" width="100%" summary="">
					<tr><td valign="top" class="TableBackground"><table border="0" cellspacing="1" cellpadding="0" width="100%" summary="a selection of checkboxes you can check the ones you wish">
					<tr><td class="TableHeader"><b><xsl:value-of select="following-sibling::options[position()=1]/@module"/></b></td></tr>
		   		   	<tr><td class="TableCell">
					<table width="100%" border="0" cellpadding="3" cellspacing="0" summary="">
					<xsl:for-each select="following-sibling::options[position()=1]/option">
					<tr>
						<td width="20%"><input class="checkbox" type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../../@name"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
			<xsl:choose>
				<xsl:when test="(@checked='true' or @selected='true') and not(../../../values/fields[@name=$name])">
					<xsl:attribute name="checked">checked</xsl:attribute>
				</xsl:when>
				<xsl:otherwise>
					<xsl:if test="../../../values/field[@name=$name]=@value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
				</xsl:otherwise>
			</xsl:choose>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
				   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label></td>
					</tr>
					</xsl:for-each>
				   	</table></td></tr></table></td></tr></table></td>
					</xsl:if>
					<xsl:if test="following-sibling::options[position()=2]">
					<td valign="top"><table border="0" cellspacing="0" cellpadding="0" width="100%" summary="">
					<tr><td valign="top" class="TableBackground"><table border="0" cellspacing="1" cellpadding="0" width="100%">
					<tr><td class="TableHeader"><b><xsl:value-of select="following-sibling::options[position()=2]/@module"/></b></td></tr>
		   		   	<tr><td class="TableCell">
					<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<xsl:for-each select="following-sibling::options[position()=2]/option">
					<tr>
						<td width="20%"><input class="checkbox" type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../../@name"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
			<xsl:choose>
			<xsl:when test="(@checked='true' or @selected='true') and not(../../../values/fields[@name=$name])">
				<xsl:attribute name="checked">checked</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="../../../values/field[@name=$name]=@value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
			</xsl:otherwise>
			</xsl:choose>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
							</xsl:if>
				   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label></td>
					</tr>
					</xsl:for-each>
				   	</table></td></tr></table></td></tr></table></td>
					</xsl:if>
					</xsl:if>
					</tr>
	  		</xsl:for-each>
	</table>
			</xsl:if>
		   	</td>
  		</tr>
	</xsl:when>
	<xsl:otherwise>
		<tr class="TableCell">
			<xsl:if test="@label">
		   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template>
				 <xsl:if test="@required"><span class="required">*</span></xsl:if>
			</td>
			</xsl:if>
			<td>
			   	<xsl:if test="@type='vertical'">
					<xsl:for-each select="option">
						<input type="checkbox" class="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="../@name"/>[]</xsl:attribute>
						   	<xsl:attribute name="id"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
							<xsl:choose>
								<xsl:when test="(@checked='true' or @selected='true') and not(../../values/fields[@name=$name])">
									<xsl:attribute name="checked">checked</xsl:attribute>
								</xsl:when>
								<xsl:otherwise>
									<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
									<xsl:for-each select="../../values/field[@name=$name]">
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
			   	</xsl:if>
			   	<xsl:if test="@type='horizontal'">
	   		   	<xsl:for-each select="option">
					<input class="checkbox" type="checkbox">
						<xsl:attribute name="name"><xsl:value-of select="../@name"/>[]</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
					   	<xsl:attribute name="id"><xsl:value-of select="../@name"/></xsl:attribute>
						<xsl:choose>
							<xsl:when test="(@checked='true' or @selected='true') and not(../../values/fields[@name=$name])">
								<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
								<xsl:for-each select="../../values/field[@name=$name]">
									<xsl:if test=".=$value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
								</xsl:for-each>
							</xsl:otherwise>
						</xsl:choose>
						<xsl:if test="../@onclick">
							<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
						</xsl:if>
						</input><label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></label>
		  		</xsl:for-each>
					</xsl:if>
			</td>
  		</tr>
			</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template match="input">
	<xsl:choose>
  		<xsl:when test="@type='text'">
    	<tr class="TableCell">
		   	<td valign="top"><label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute>
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> <xsl:if test="@required"><span class="required">*</span></xsl:if></td>
					<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
		   	<td><input type='text'>
					<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
						<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
					</xsl:if>
			<xsl:attribute name="value"><xsl:choose>
				<xsl:when test="../values/field[@name=$name]"><xsl:value-of select="../values/field[@name=$name]"/></xsl:when>
				<xsl:when test="string-length(.)=0 and string-length(@value)=0 and //setting[@name='sp_wai_forms']!='No'">[[lt]]<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/><xsl:with-param name="maxlen" select="50"/></xsl:call-template>[[gt]]</xsl:when>
				<xsl:when test="@value"><xsl:value-of select="@value"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
				</xsl:choose></xsl:attribute>
		   	<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
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
		   	</input>
		   	</td>
  		</tr>
  		</xsl:when>
  		<xsl:when test="@type='password'">
    	<tr class="TableCell">
		   	<td valign="top"><label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute>
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label>  <xsl:if test="@required"><span class="required">*</span></xsl:if></td>
		   	<td><input type='password'>
					<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
						<xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
					</xsl:if>
			<xsl:attribute name="value"><xsl:choose>
				<xsl:when test="../values/field[@name=$name]"><xsl:value-of select="../values/field[@name=$name]"/></xsl:when>
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
		   	</input></td>
  		</tr>

  		</xsl:when>
  		<xsl:when test="@type='date_time'">
    	<tr class="TableCell">
		   	<td valign="top"><label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute>
<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label>  <xsl:if test="@required"><span class="required">*</span></xsl:if></td>
		   	<td>
			<input type='hidden'>
				<xsl:attribute name='name'><xsl:value-of select='@name'/></xsl:attribute>
				<xsl:attribute name='id'><xsl:value-of select='@name'/></xsl:attribute>
				<xsl:attribute name='value'><xsl:value-of select='@value'/></xsl:attribute>
			</input>
			<xsl:variable name="year"><xsl:value-of select="substring-before(@value,'-')"/></xsl:variable>
			<xsl:variable name="month"><xsl:value-of select="substring-before(substring-after(@value,'-'),'-')"/></xsl:variable>
			<xsl:variable name="day"><xsl:value-of select="substring-before(substring-after(substring-after(@value,'-'),'-'),' ')"/></xsl:variable>
			<xsl:variable name="hour"><xsl:value-of select="substring-before(substring-after(@value,' '),':')"/></xsl:variable>
			<xsl:variable name="year_start"><xsl:value-of select="@year_start"/></xsl:variable>
			<xsl:variable name="year_end"><xsl:value-of select="@year_end"/></xsl:variable>
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
			</select>
			</td>
  		</tr>
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
	<tr><td valign="top" colspan="2">
	<xsl:choose>
		<xsl:when test="@type"><xsl:attribute name="class">error</xsl:attribute></xsl:when>
		<xsl:otherwise><xsl:attribute name="class">TableCell</xsl:attribute></xsl:otherwise>
	</xsl:choose>
	<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template>
	</td></tr>
</xsl:template>




<xsl:template match="radio">
	   	<tr class="TableCell">
		<xsl:if test="@label">
		   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template><xsl:if test="@required"><span class="required">*</span></xsl:if></td>
		</xsl:if>
		<td>
		<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
		<xsl:choose><xsl:when test="@type='vertical'">
		<table>
	   	<xsl:for-each select="option">
		<tr><td valign="top"><input class="checkbox" type="radio">
		   	<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
			<xsl:attribute name='id'><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute>
			<xsl:attribute name="value"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
			<xsl:choose>
			<xsl:when test="(@checked='true' or @selected='true') and not(../../values/fields[@name=$name])">
				<xsl:attribute name="checked">checked</xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="../../values/field[@name=$name]=@value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
			</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="../../@onclick">
				<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
			</xsl:if>
	   		</input></td><td valign="top">
			<label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template></label>
   		</td>
   	</tr></xsl:for-each>
</table></xsl:when><xsl:otherwise>
	   	<xsl:for-each select="option">
		<input class="checkbox" type="radio">
		   	<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
			<xsl:attribute name='id'><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute>
			<xsl:attribute name="value"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
			<xsl:choose>
			<xsl:when test="(@checked='true' or @selected='true') and not(../../values/field[@name=$name])">
				<xsl:attribute name="checked">true <xsl:value-of select="not(../../values/field[@name=$name])"/><xsl:value-of select="$name"/></xsl:attribute>
			</xsl:when>
			<xsl:otherwise>
				<xsl:if test="../../values/field[@name=$name]=@value"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
			</xsl:otherwise>
			</xsl:choose>
			<xsl:if test="../../@onclick">
				<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
			</xsl:if>
	   		</input>[[nbsp]]
			<label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select='position()'/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template></label>
   		</xsl:for-each>
	</xsl:otherwise>
</xsl:choose></td></tr>
</xsl:template>


<xsl:template match="form_subject">
	<xsl:param name="labelinnewrow">0</xsl:param>
   	<tr class="TableCell">
   		<xsl:if test="$labelinnewrow=0 and @label">
	   	<td valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template><xsl:if test="@required"><span class="required">*</span></xsl:if></td>
	</xsl:if>
	<td>
	<xsl:variable name="name"><xsl:value-of select="@name"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="@type='radio'">
			<table  summary="List of subjects lines please select one">
	   			<tr><td valign="top">
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
				</td></tr>
			</table>
		</xsl:when>
		<xsl:when test="@type='checkbox'">
			<table summary="List of subjects lines please check at least one">
	   			<tr><td valign="top">
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
				</td></tr>
			</table>
		</xsl:when>
		<xsl:otherwise>
				<select>
					<xsl:attribute name="name">form_subject</xsl:attribute>
					<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
					<xsl:for-each select="//xml_document/modules/container/webobject/module/form/emails/option">
					<option>
						<xsl:attribute name="value"><xsl:choose><xsl:when test="@value!=''"><xsl:value-of select="@value"/></xsl:when><xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></xsl:attribute>
						<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes" /></xsl:with-param></xsl:call-template>
						<xsl:choose>
							<xsl:when test="(@checked='true' or @selected='true') and not(../../values/fields[@name=$name])">
							<xsl:attribute name="checked">checked</xsl:attribute>
							</xsl:when>
							<xsl:otherwise>
								<xsl:if test=". = ../../values/field[@name=$name]"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if>
							</xsl:otherwise>
						</xsl:choose>
					</option>
					</xsl:for-each>
				</select>
		</xsl:otherwise>
	</xsl:choose></td></tr>
</xsl:template>



<xsl:template match="seperator_row">
	<tr><td class="tablecell"><table class="width100percent" border="0"><tr><xsl:apply-templates/></tr></table></td></tr>
</xsl:template>

<xsl:template match="seperator">
	<td valign="top"><table class="width100percent"><xsl:apply-templates/></table></td>
</xsl:template>



<xsl:template match="link">
:: <a><xsl:attribute name="title"><xsl:value-of select ="@title" disable-output-escaping="yes"/></xsl:attribute><xsl:attribute name="href">?command=<xsl:value-of select ="@command" disable-output-escaping="yes"/></xsl:attribute><xsl:value-of select ="@title" disable-output-escaping="yes"/></a>
</xsl:template>

</xsl:stylesheet>