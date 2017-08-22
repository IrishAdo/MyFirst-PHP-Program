<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/09/06 16:49:53 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 


<xsl:template match="data">
</xsl:template>

<xsl:template match="form_builder">
<script src="/libertas_images/editor/formbuilder/locale_en.js"></script>
<script src="/libertas_images/editor/formbuilder/fb_field.js"></script>
<script src="/libertas_images/editor/formbuilder/fb_form.js"></script>
<script src="/libertas_images/editor/formbuilder/fb_main.js"></script>
<script>
<xsl:comment>
var page =0;
var field =null
var maximumAccess ='<xsl:value-of select="//xml_document/modules/module/licence/product/@type"/>';
var buildform  = new form_data();
	buildform.form_identifier = "<xsl:value-of select="//xml_document/modules/module/form/input[@name='identifier']/@value"/>";
	buildform.form_emails	=Array(<xsl:for-each select="email">
		new Array("<xsl:value-of select="subject" disable-output-escaping="yes"/>","<xsl:value-of select="address" disable-output-escaping="yes"/>",<xsl:choose>
			<xsl:when test="@selected='true'">true</xsl:when>
			<xsl:otherwise>false</xsl:otherwise>
		</xsl:choose>)
		<xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>);
	buildform.form_url				= "<xsl:value-of select="//xml_document/modules/module/form/page_sections/section[@name='Advanced']/form_builder/url" disable-output-escaping="yes"/>";
	buildform.form_method			= "<xsl:value-of select="form_data/method" disable-output-escaping="yes"/>";
	buildform.form_confirm_screen	= "<xsl:value-of select="form_data/confirm_screen" disable-output-escaping="yes"/>";
	buildform.form_action	= "<xsl:value-of select="form_data/radio/option[@selected='true']/@value"/>";
	<xsl:for-each select="fields">
		<xsl:for-each select="seperator_row">
			<xsl:for-each select="seperator">
				<xsl:for-each select="child::*">
					<xsl:choose>
					<xsl:when test="@type='hidden' and @name='number_of_fields' ">
					</xsl:when>
					<xsl:when test="@type='hidden' and @name!='number_of_fields' ">
						<xsl:variable name="valueofhiddenfield">
						<xsl:choose>						
							<xsl:when test="@value">
								<xsl:value-of select="@value" disable-output-escaping="yes"/>
							</xsl:when>
							<xsl:otherwise>
								<xsl:value-of select="." disable-output-escaping="yes"/>
							</xsl:otherwise>
						</xsl:choose>					
						</xsl:variable>
						property = new Array();
						property["width"]="<xsl:value-of select="@size"/>";
						buildform.add("<xsl:value-of select="@name"/>","hidden","hidden", property, false, null, null, false,false,'<xsl:value-of select='$valueofhiddenfield' />', null);
					</xsl:when>
					<xsl:when test="@type='file' ">
						property = new Array();
						property["width"]="<xsl:value-of select="@size"/>";
						buildform.add("<xsl:value-of select="@name"/>","fileupload",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="@label"/></xsl:with-param></xsl:call-template>"), property, false, null, null, false,false,'', null);
					</xsl:when>
					<xsl:when test="local-name()='text'">
						property = new Array();
						buildform.add("<xsl:value-of select="@name"/>","cdata",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="."/></xsl:with-param></xsl:call-template>"), property, false, null, null, false, false, '', null);
					</xsl:when>
					<xsl:when test="@type='text'">
						property = new Array();
						property["width"]="<xsl:value-of select="@size"/>";
						buildform.add("<xsl:value-of select="@name"/>","<xsl:value-of select="@type"/>",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="@label"/></xsl:with-param></xsl:call-template>"), property, false, null, null, <xsl:choose>
							<xsl:when test="@required='true'">true</xsl:when>
							<xsl:otherwise>false</xsl:otherwise>
						</xsl:choose>, <xsl:choose>
							<xsl:when test="@session_hide='true'">true</xsl:when>
							<xsl:otherwise>false</xsl:otherwise>
						</xsl:choose>,'', null);
					</xsl:when>
					<xsl:when test="@type='date_time'">
						property = new Array();
						property["dateType"]="<xsl:value-of select="@dateType"/>";
						buildform.add("<xsl:value-of select="@name"/>","<xsl:value-of select="@type"/>",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="@label"/></xsl:with-param></xsl:call-template>"), property, false, null, null, false,false,'', null);
					</xsl:when>
					<xsl:when test="local-name()='textarea'">
						property = new Array();
						property["width"]=<xsl:value-of select="@size"/>;
						property["height"]=<xsl:value-of select="@height"/>;
						buildform.add("<xsl:value-of select="@name"/>","textarea",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="@label"/></xsl:with-param></xsl:call-template>"), property, false, null, null, <xsl:choose>
							<xsl:when test="@required='true'">true</xsl:when>
							<xsl:otherwise>false</xsl:otherwise>
						</xsl:choose>, <xsl:choose>
							<xsl:when test="@session_hide='true'">true</xsl:when>
							<xsl:otherwise>false</xsl:otherwise>
						</xsl:choose>,'', null);
		
					</xsl:when>
					<xsl:when test="local-name()='radio'">
						Attributes = new Array();
						Attributes[Attributes.length]= Array("other",<xsl:choose><xsl:when test="@other">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>);
						Attributes[Attributes.length]= Array("other_label",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:choose><xsl:when test="other_label"><xsl:value-of select="other_label"/></xsl:when><xsl:otherwise>Other please specify</xsl:otherwise></xsl:choose></xsl:with-param></xsl:call-template>"));
						property = new Array();
						<xsl:for-each select="option">
						property[property.length] = new Array("<xsl:value-of select="." disable-output-escaping="yes"/>", LIBERTAS_GENERAL_jtidy("<xsl:value-of select="@value" disable-output-escaping="yes"/>"),<xsl:choose>
									<xsl:when test="@checked='true'">true</xsl:when>
									<xsl:otherwise>false</xsl:otherwise>
								</xsl:choose>);
						</xsl:for-each>
						buildform.add("<xsl:value-of select="@name"/>","radio",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="@label"/></xsl:with-param></xsl:call-template>"), property, true, <xsl:choose>
						<xsl:when test="@type='vertical'">"vertical"</xsl:when>
						<xsl:otherwise>"horizontal"</xsl:otherwise>
						</xsl:choose>, null, <xsl:choose>
							<xsl:when test="@required='true'">true</xsl:when>
							<xsl:otherwise>false</xsl:otherwise>
						</xsl:choose>, <xsl:choose>
							<xsl:when test="@session_hide='true'">true</xsl:when>
							<xsl:otherwise>false</xsl:otherwise>
						</xsl:choose>,'', Attributes);
		
					</xsl:when>
					<xsl:when test="local-name()='checkboxes'">
						Attributes = new Array();
						Attributes[Attributes.length]= Array("other",<xsl:choose><xsl:when test="@other">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>);
						Attributes[Attributes.length]= Array("other_label",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:choose><xsl:when test="other_label"><xsl:value-of select="other_label"/></xsl:when><xsl:otherwise>Other please specify</xsl:otherwise></xsl:choose></xsl:with-param></xsl:call-template>"));
						property = new Array();
						<xsl:for-each select="option">
						property[property.length] = new Array("<xsl:value-of select="." disable-output-escaping="yes"/>", LIBERTAS_GENERAL_jtidy("<xsl:value-of select="@value" disable-output-escaping="yes"/>"), <xsl:choose>
									<xsl:when test="@checked='true'">true</xsl:when>
									<xsl:otherwise>false</xsl:otherwise>
								</xsl:choose>);</xsl:for-each>
						buildform.add("<xsl:value-of select="@name"/>","checkbox",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="@label"/></xsl:with-param></xsl:call-template>"), property, true, <xsl:choose>
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
						</xsl:choose>,'', Attributes);
		
					</xsl:when>
					<xsl:when test="local-name()='select'">
						property = new Array();
						<xsl:for-each select="option">
							property[property.length] = new Array(
								"<xsl:value-of select="." disable-output-escaping="yes"/>",
								LIBERTAS_GENERAL_jtidy("<xsl:value-of select="@value" disable-output-escaping="yes"/>"),
								<xsl:choose>
									<xsl:when test="@checked='true'">true</xsl:when>
									<xsl:otherwise>false</xsl:otherwise>
								</xsl:choose>
							);
						</xsl:for-each>
						Attributes = new Array();
						Attributes[Attributes.length]= Array("multiple",<xsl:choose><xsl:when test="@multiple">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>);
						Attributes[Attributes.length]= Array("size","<xsl:choose><xsl:when test="@size"><xsl:value-of select="@size"/></xsl:when><xsl:otherwise>1</xsl:otherwise></xsl:choose>");
						Attributes[Attributes.length]= Array("other",<xsl:choose><xsl:when test="@other">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>);
						Attributes[Attributes.length]= Array("other_label",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:choose><xsl:when test="other_label"><xsl:value-of select="other_label"/></xsl:when><xsl:otherwise>Other please specify</xsl:otherwise></xsl:choose></xsl:with-param></xsl:call-template>"));
						buildform.add("<xsl:value-of select="@name"/>","select",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="@label"/></xsl:with-param></xsl:call-template>"), property, true, <xsl:choose>
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
						</xsl:choose>,'',Attributes);
					</xsl:when>
					<xsl:when test="local-name()='form_subject'">
						property = new Array();
						property["field_format"] = '<xsl:value-of select="@type"/>';
						buildform.add("<xsl:value-of select="@name"/>","subject",LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="@label"/></xsl:with-param></xsl:call-template>"), property, false, null , null, false, false, '', null);
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
			<xsl:if test="position()!=last()">
				property = new Array();
				buildform.add("<xsl:value-of select="@name"/>","row_splitter","", property, false, "null", "null", false,false,'');
			</xsl:if>
		</xsl:for-each>
	</xsl:for-each>
//	buildform.build(-1,-1);
//	if (buildform.form_emails.length>0 || buildform.form_action ==0 || buildform.form_action==1){
//		buildform.show_email();
//	}
	if (buildform.form_url.length>0 || buildform.form_action ==3){
		buildform.show_url();
	}
	xml_destination = document.wizard_frm.xml_representation;
	next_page(page);
</xsl:comment>
</script>

</xsl:template>
<xsl:template name="escapequotes">
	<xsl:param name="str"></xsl:param>
	<xsl:param name="debugon">0</xsl:param>
	<xsl:variable name="replace">&amp;amp;quot;</xsl:variable>
	<xsl:variable name="replace1">"</xsl:variable>
	<xsl:variable name="replace2">&amp;quot;</xsl:variable>
	<xsl:variable name="replace3">&quot;</xsl:variable>
	<xsl:variable name="replace4">&#34;</xsl:variable>
	<xsl:variable name="replace5">&amp;#34;</xsl:variable>
	<xsl:variable name="replace6">&amp;amp;#34;</xsl:variable>
	<xsl:variable name="replace7">&amp;amp;amp;quot;</xsl:variable>
	<xsl:if test="$debugon=1">
		[
		<xsl:if test="contains($str,$replace)">1</xsl:if>,
		<xsl:if test="contains($str,$replace1)">2</xsl:if>,
		<xsl:if test="contains($str,$replace2)">3</xsl:if>,
		<xsl:if test="contains($str,$replace3)">4</xsl:if>,
		<xsl:if test="contains($str,$replace4)">5</xsl:if>,
		<xsl:if test="contains($str,$replace5)">6</xsl:if>,
		<xsl:if test="contains($str,$replace6)">7</xsl:if>,
		<xsl:if test="contains($str,$replace7)">8</xsl:if>
		]
	</xsl:if>
	<xsl:choose>
		<xsl:when test="contains($str,$replace)"><xsl:value-of select="substring-before($str,$replace)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace1)"><xsl:value-of select="substring-before($str,$replace1)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace1)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace2)"><xsl:value-of select="substring-before($str,$replace2)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace2)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace3)"><xsl:value-of select="substring-before($str,$replace3)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace3)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace4)"><xsl:value-of select="substring-before($str,$replace4)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace4)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace5)"><xsl:value-of select="substring-before($str,$replace5)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace5)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace6)"><xsl:value-of select="substring-before($str,$replace6)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace6)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:when test="contains($str,$replace7)"><xsl:value-of select="substring-before($str,$replace7)"/>[[jsquote]]<xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="substring-after($str,$replace7)"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:otherwise><xsl:value-of select="$str"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>
</xsl:stylesheet>
