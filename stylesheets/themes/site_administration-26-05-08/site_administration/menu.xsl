<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2005/01/31 09:01:53 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:variable name="colour_button_off">#ffffff</xsl:variable>
<xsl:variable name="colour_button_on">#000000</xsl:variable>
<xsl:variable name="colour_text_on">#000000</xsl:variable>
<xsl:variable name="colour_text_off">#000000</xsl:variable>
<xsl:variable name="colour_button_left_off">#ebebeb</xsl:variable>
<xsl:variable name="colour_button_left_on">#aaaaaa</xsl:variable>
<xsl:variable name="colour_button_right_off">#ebebeb</xsl:variable>
<xsl:variable name="colour_button_right_on">#aaaaaa</xsl:variable>
<xsl:variable name="colour_button_top_off">#ebebeb</xsl:variable>
<xsl:variable name="colour_button_top_on">#aaaaaa</xsl:variable>
<xsl:variable name="colour_button_bottom_off">#ebebeb</xsl:variable>
<xsl:variable name="colour_button_bottom_on">#aaaaaa</xsl:variable>

<xsl:template name="display_admin_menu">
	<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/admin_menu2.js"></script>
	<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/admin_menu_display.js"></script>
	<xsl:element name="script">
		<xsl:attribute name="type">text/javascript</xsl:attribute>
		<xsl:attribute name="language">JavaScript1.2</xsl:attribute>
		http = "http<xsl:if test="//setting[@name='SSL']='yes'">s</xsl:if>://";
var access_list = new Array(
	<xsl:for-each select="//xml_document/modules/session/groups[@type=2]/group[@type=2]/access">
		"<xsl:value-of select="."/>" <xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
);
var all=0;
for(i=0;i &lt; access_list.length;i++){
	if (access_list[i]=="ALL"){
		all=1;
	}
}
var menu_list = new Array();<xsl:for-each select="menu/entry">
	<xsl:variable name="po"><xsl:value-of select="paths/original"/></xsl:variable>
	menu_list[menu_list.length]= new Array("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template>","<xsl:call-template name="get_translation"><xsl:with-param name="check" select="label"/></xsl:call-template>","<xsl:call-template name="get_translation"><xsl:with-param name="check" select="roles"/></xsl:call-template>","<xsl:value-of select="@cmd"/>","<xsl:value-of select="@ignore"/>","<xsl:value-of select="paths/original"/>","<xsl:value-of select="count(//paths[original=$po])"/>",Array(<xsl:for-each select="paths/path">"<xsl:value-of select="."/>"<xsl:if test="position()!=last()">, </xsl:if></xsl:for-each>),Array(<xsl:for-each select="paths/path">0<xsl:if test="position()!=last()">, </xsl:if></xsl:for-each>));
</xsl:for-each>
var total_visibility= 100;
var direction		= 4; // horizontal
var showdelay		= 200;
var hidedelay 		= 1000;
var padding			= 4;
var mysessiondata 	= "&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>";
var spacing			= 0;
var backgroundimage = "";
var path_list = "";
var colour_button_off = "<xsl:value-of select="$colour_button_off"/>";
launch_menu(
	"<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_HOME'"/></xsl:call-template>",
	"<xsl:value-of select="//setting[@name='domain']"/><xsl:value-of select="//setting[@name='base']"/>",
	"<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_HELP'"/></xsl:call-template>",
	<xsl:choose>
		<xsl:when test="//setting[@name='domain'] = 'dev'">1</xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose>
);
	</xsl:element>

</xsl:template>

<xsl:template name="display_page_menu">

	<xsl:element name="script">
		<xsl:attribute name="type">text/javascript</xsl:attribute>
		<xsl:attribute name="language">JavaScript1.2</xsl:attribute>
stm_bm(["Libertas_Solutions_page_options",400,"","/libertas_images/themes/1x1.gif",0,"","",0,1,showdelay,showdelay,hidedelay,1,0,0,""],this);
	begin(0,0,"Horizontaly",0,"p100",1);
		add_entry1("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Page Options'"/></xsl:call-template>","","p100i0");
			begin(0,-1,"undefined",0,"p101",0,4);
			p=0;
				<xsl:for-each select="//module/page_options/button">
					href=<xsl:choose>
							<xsl:when test="@command='FILES_ASSOCIATE_FILES'">"javascript:file_associate();</xsl:when>
							<xsl:when test="@command='MENU_LIST'">"javascript:layout_associate();</xsl:when>
							<xsl:when test="@command='SFORM_BUILD_XML'">"javascript:buildform.build_XML();</xsl:when>
							<xsl:when test="@command='LAYOUT_PAGE_RANKING'">"javascript:button_action('RANK');</xsl:when>
							<xsl:when test="@iconify='PREVIEW'"><xsl:choose>
									<xsl:when test="../../@display='form' and ../../@name='information_admin'">"javascript:preview_infodir('<xsl:value-of select="@command"/>');</xsl:when>
									<xsl:when test="../../@display='form'">"javascript:preview_from_form(<xsl:choose>
										<xsl:when test="../../form/input/@name[.='page_identifier']/@value=''">-1,'page',document.<xsl:value-of select="../../form/@name" />,'PAGE_PREVIEW_FORM'</xsl:when>
										<xsl:otherwise><xsl:value-of select="../../form/input[@name='page_identifier']" />,'page',document.<xsl:value-of select="../../form/@name" />,'PAGE_PREVIEW_FORM'</xsl:otherwise>
									</xsl:choose>);</xsl:when>
									<xsl:when test="../../@display='results'">"javascript:preview_from_list(<xsl:value-of select="../../form/input/@name[.='page_identifier']/@value"/>,'page');</xsl:when>
									<xsl:otherwise>"http://"+domain + base_href + "admin/preview.php?command=PAGE_PREVIEW&amp;<xsl:value-of select="@parameters" /></xsl:otherwise>
						   	</xsl:choose></xsl:when>
							<xsl:when test="@command='GENERAL_BACK'">"javascript:history.back();</xsl:when>
							<xsl:otherwise>http+domain + base_href + "admin/index.php?command=<xsl:value-of select="@command"/>&amp;<xsl:if test="@parameters"><xsl:value-of select="@parameters"/></xsl:if></xsl:otherwise>
						</xsl:choose>";
			<!-- http://<xsl:value-of select="//setting[@name='domain']"/><xsl:value-of select="//setting[@name='base']"/>admin/index.php -->
					add_entry("<xsl:variable name="possible_alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@alt"/></xsl:call-template></xsl:variable><xsl:variable name="possible_value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:variable><xsl:value-of select="$possible_value"/><xsl:value-of select="$possible_alt"/>",href,"p100i"+p,"p100i0");
					p++;
				</xsl:for-each>
  	 	<xsl:choose>
  	 	<xsl:when test="//filter"></xsl:when>
  	 	<xsl:otherwise>
  	 	<xsl:for-each select="//input">
			<xsl:if test="@type='submit' and ../@name!='USERS_SHOW_LOGIN'">
				href="<xsl:choose>
					<xsl:when test="@command='BACK'">javascript:history.back();</xsl:when>
					<xsl:when test="@command='LAYOUT_REMOVE_MENU'">javascript:button_action('LAYOUT_REMOVE_MENU');</xsl:when>
					<xsl:when test="@command='LAYOUT_REMOVE_DIRECTORY'">javascript:button_action('LAYOUT_REMOVE_DIRECTORY');</xsl:when>
					<xsl:when test="@command='LAYOUT_SAVE_DIRECTORY'">javascript:button_action('LAYOUT_SAVE_DIRECTORY');</xsl:when>
					<xsl:when test="@command='VEHICLE_LOOKUP_REMOVE'">javascript:lookup_remove(document.<xsl:value-of select="../@name"/>);</xsl:when>
						<xsl:when test="//input/@name='command' and //input/@value='WEBOBJECTS_LAYOUT_SAVE'">javascript:webobjects_submit();</xsl:when>
						<xsl:when test="//textarea[@type='RICH-TEXT']">javascript:ok = onSubmitCompose(2,'<xsl:value-of select="@command"/>');</xsl:when>
						<xsl:otherwise>
							<xsl:choose>
								<xsl:when test="..//@required">javascript:check_required_fields();</xsl:when>
								<xsl:otherwise>javascript:document.<xsl:value-of select="../@name"/>.submit();</xsl:otherwise>
							</xsl:choose>
						</xsl:otherwise>
					</xsl:choose>";
					<xsl:variable name="possible_alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@alt"/></xsl:call-template></xsl:variable>
					<xsl:variable name="possible_value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:variable>
					<xsl:if test="$possible_alt!='' or $possible_value!=''">
				add_entry("<xsl:value-of select="$possible_value"/><xsl:value-of select="$possible_alt"/>",href,"p100i"+p,"p100i0");
				p++;</xsl:if>
			</xsl:if>
			<xsl:if test="@type='button'">
			href=<xsl:choose>
					<xsl:when test="@command='BACK'">"javascript:history.back();</xsl:when>
					<xsl:when test="@command='LAYOUT_REMOVE_MENU'">"javascript:button_action('LAYOUT_REMOVE_MENU');</xsl:when>
					<xsl:when test="@command='LAYOUT_REMOVE_DIRECTORY'">"javascript:button_action('LAYOUT_REMOVE_DIRECTORY');</xsl:when>
					<xsl:when test="@command='LAYOUT_SAVE_DIRECTORY'">"javascript:button_action('LAYOUT_SAVE_DIRECTORY');</xsl:when>
					<xsl:when test="@command='VEHICLE_LOOKUP_REMOVE'">"javascript:lookup_remove(document.<xsl:value-of select="../@name"/>);</xsl:when>
					<xsl:otherwise>"http://<xsl:value-of select="//setting[@name='domain']"/><xsl:value-of select="//setting[@name='base']"/>admin/index.php?command=<xsl:value-of select="@command"/><xsl:value-of select="@parameters"/></xsl:otherwise>
				</xsl:choose>";
					add_entry("<xsl:variable name="possible_alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@alt"/></xsl:call-template></xsl:variable><xsl:variable name="possible_value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:variable><xsl:value-of select="$possible_value"/><xsl:value-of select="$possible_alt"/>",href,"p100i"+p,"p100i0");
					p++;
					</xsl:if>
		   	</xsl:for-each>
		</xsl:otherwise>
		</xsl:choose>
			stm_ep();
	stm_ep();
stm_em();
	


	</xsl:element>

</xsl:template>
</xsl:stylesheet>