<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:50:02 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:variable name="colour_button_off">#ebebeb</xsl:variable>
<xsl:variable name="colour_button_on">#cccccc</xsl:variable>
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
	<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/admin_menu.js"></script>
	<xsl:element name="script">
		<xsl:attribute name="type">text/javascript</xsl:attribute>
		<xsl:attribute name="language">JavaScript1.2</xsl:attribute>
var access_list =Array(
	<xsl:for-each select="//xml_document/modules/session/groups[@type=2]/group[@type=2]/access">
		"<xsl:value-of select="."/>" <xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
)

var menu_list = Array(<xsl:for-each select="grouping">
	<xsl:sort select="@name" order="ascending"/>
	<xsl:sort select="mod/@label" order="ascending"/>
	Array ("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template>","#",Array(<xsl:for-each select="mod">
		Array("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template>","<xsl:value-of select="options/@tag"/>","<xsl:choose>
				<xsl:when test="count(options/option)=1">?command=<xsl:value-of select="options/option[position()=1]/@value"/>&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/></xsl:when>
					<xsl:otherwise></xsl:otherwise>
					</xsl:choose>",<xsl:choose>
					<xsl:when test="count(options/option)>0">
						Array(
							<xsl:for-each select="options/option[@value!=../../@ignore]">
								<xsl:sort select="@value" order="ascending"/>
								<xsl:variable name="me"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></xsl:variable>
								<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
								Array("<xsl:value-of select="$me"/>","<xsl:value-of select="$value"/>","<xsl:value-of select="@grouping"/>","?command=<xsl:value-of select="$value"/>&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>")<xsl:if test="position()!=last()">, </xsl:if>
							</xsl:for-each>
						)
					</xsl:when>
					<xsl:otherwise>Array(Array("","__NOT_FOUND__",""))</xsl:otherwise>
					</xsl:choose>
					)<xsl:if test="position()!=last()">, </xsl:if> 
				</xsl:for-each>)
)<xsl:if test="position()!=last()">, </xsl:if> 
</xsl:for-each>);
/*
complete_menu = Array();
complete_menu["<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_HELP'"/></xsl:call-template>"] = new admin_menu();

start='	\n';

*/
//alert(finish);
debug_str="";
beginSTM("Libertas_Solutions","static","0","0","none","false","true","310","500","0","100","","/libertas_images/themes/1x1.gif");
	begin(0,0,"Horizontaly",0);
		add_entry1("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_HOME'"/></xsl:call-template>","#");
			begin(0,-1,"undefined",0);
				href="http://<xsl:value-of select="//setting[@name='domain']"/><xsl:value-of select="//setting[@name='base']"/>admin/index.php";
				add_entry("Digitial Desktop",href);
				href="http://<xsl:value-of select="//setting[@name='domain']"/><xsl:value-of select="//setting[@name='base']"/>index.php";
				add_entry("Return to Site",href);
				add_entry("<hr/>","");
				href="admin/index.php?command=ENGINE_LOGOUT";
				add_entry("Exit (Logout)",href);
			endSTMB();
		libertas_dhtml_menu = add_admin_options(menu_list,access_list);
		for (key in libertas_dhtml_menu){
		pos=0;
			for (libertas_dhtml_menu_entry in libertas_dhtml_menu[key].children){
				if (libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry].length!=0){
					if(libertas_dhtml_menu[key].displayed==0){
						add_entry1(key,"#");
		 				begin(0,-1,"undefined",0);
						libertas_dhtml_menu[key].displayed=1;
					}
					if (libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry].length != 1){
						add_entry(libertas_dhtml_menu_entry,"#");
	 					begin(0,-1,"undefined",0);
					}
					for (option=0;option != libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry].length;option++){
						if (libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry][option][1]+'' !=''){
							href=libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry][option][1];
							add_entry(libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry][option][0], href);
						}
					}
					if (libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry].length != 1){
						endSTMB();
					}
					pos++;
				}
			}
			if(libertas_dhtml_menu[key].displayed==1){
				if (key!=prev){
				endSTMB();
				}
			}
			prev=key;
		}
		add_entry1("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_HELP'"/></xsl:call-template>","#");
			begin(0,0,"undefined",0);
				add_entry("About Us","http://www.libertas-solutions.com");
				add_entry("<hr/>","");
				add_entry("Module Versions","index.php?command=ENGINE_VERSIONS&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>");
				add_entry("PHP Info","phpinfo.php");
			endSTMB();
	endSTMB();
endSTM();
	
<xsl:variable name="colour_button_off">#ebebeb</xsl:variable>
<xsl:variable name="colour_button_on">#cccccc</xsl:variable>
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

function add_entry1(label_str,url_str){
	if (url_str!=""){
		colour_on = "<xsl:value-of select="$colour_button_on"/>";
		button_left_on = "<xsl:value-of select="$colour_button_left_on"/>";
		button_right_on = "<xsl:value-of select="$colour_button_right_on"/>";
		button_top_on = "<xsl:value-of select="$colour_button_top_on"/>";
		button_bottom_on = "<xsl:value-of select="$colour_button_bottom_on"/>";
	} else {
		colour_on = "<xsl:value-of select="$colour_button_off"/>";
		button_left_on = "<xsl:value-of select="$colour_button_left_off"/>";
		button_right_on = "<xsl:value-of select="$colour_button_right_off"/>";
		button_top_on = "<xsl:value-of select="$colour_button_top_off"/>";
		button_bottom_on = "<xsl:value-of select="$colour_button_bottom_off"/>";
	}
	if (url_str.substring(0,1)=="?"){
		url_str = "<xsl:value-of select="//xml_document/modules/module/setting[@name='base']"/><xsl:value-of select="//xml_document/modules/module/setting[@name='script']"/>"+url_str;
	}
	url_str = tidy(url_str)
	
	status_bar_text=label_str;
	appendSTMI("false",label_str,"left","middle","","","-1","-1","0","normal",
	"",
	colour_on,
	"","1","-1","-1","/libertas_images/themes/1x1.gif","/libertas_images/themes/1x1.gif","0","0","0","",url_str,"_self",
	"Arial","0.9em","<xsl:value-of select="$colour_text_off"/>","","normal","none",
	"Arial","0.9em","<xsl:value-of select="$colour_text_on"/>","","normal","none",
	"1","solid",
	"#aaaaaa",
	"#ffffff",
	button_left_on,
	button_right_on,
	"<xsl:value-of select="$colour_button_top_off"/>",
	"<xsl:value-of select="$colour_button_bottom_off"/>",
	button_top_on,
	button_bottom_on,
	status_bar_text, "","","tiled","tiled");
}
function add_entry(label_str,url_str){
	if (url_str!=""){
		colour_on = "<xsl:value-of select="$colour_button_on"/>";
		button_left_on = "<xsl:value-of select="$colour_button_left_on"/>";
		button_right_on = "<xsl:value-of select="$colour_button_right_on"/>";
		button_top_on = "<xsl:value-of select="$colour_button_top_on"/>";
		button_bottom_on = "<xsl:value-of select="$colour_button_bottom_on"/>";
	} else {
		colour_on = "<xsl:value-of select="$colour_button_off"/>";
		button_left_on = "<xsl:value-of select="$colour_button_left_off"/>";
		button_right_on = "<xsl:value-of select="$colour_button_right_off"/>";
		button_top_on = "<xsl:value-of select="$colour_button_top_off"/>";
		button_bottom_on = "<xsl:value-of select="$colour_button_bottom_off"/>";
	
	}
	if (url_str.substring(0,1)=="?"){
		url_str = "<xsl:value-of select="//xml_document/modules/module/setting[@name='base']"/><xsl:value-of select="//xml_document/modules/module/setting[@name='script']"/>"+url_str;
	}
	url_str = tidy(url_str)
	
	status_bar_text=label_str;
	appendSTMI("false",label_str,"left","middle","","","-1","-1","0","normal",
	"<xsl:value-of select="$colour_button_off"/>",
	colour_on,
	"","1","-1","-1","/libertas_images/themes/1x1.gif","/libertas_images/themes/1x1.gif","0","0","0","",url_str,"_self",
	"Arial","0.9em","<xsl:value-of select="$colour_text_off"/>","","normal","none",
	"Arial","0.9em","<xsl:value-of select="$colour_text_on"/>","","normal","none",
	"1","solid",
	"<xsl:value-of select="$colour_button_left_off"/>",
	"<xsl:value-of select="$colour_button_right_off"/>",
	button_left_on,
	button_right_on,
	"<xsl:value-of select="$colour_button_top_off"/>",
	"<xsl:value-of select="$colour_button_bottom_off"/>",
	button_top_on,
	button_bottom_on,
	status_bar_text, "","","tiled","tiled");
}
function begin(x,y,direction,img){
	arrow_image="";
	if (direction+""=="undefined"){
		beginSTMB("auto",x,y,"vertically","/libertas_images/themes/site_administration/arrow_r.gif","10","10","0","2","transparent","","tiled","#000000","1","solid","0","Fade","50","8","8","7","7","0","0","0","#000000","false","#000000","#000000","#000000","complex");
	}else{
		beginSTMB("auto",x,y,direction,"/libertas_images/themes/1x1.gif","0","0","0","4","transparent","","tiled","#FFFFFF","0","Fade","0","Wipe right","50","20","10","7","7","0","0","0","#000000","false","#000000","#000000","#000000","complex");
	}
}

	</xsl:element>

</xsl:template>

<xsl:template name="display_page_menu">

	<xsl:element name="script">
		<xsl:attribute name="type">text/javascript</xsl:attribute>
		<xsl:attribute name="language">JavaScript1.2</xsl:attribute>
beginSTM("Libertas_Solutions_page_options","static","0","0","none","false","true","310","500","0","100","","/libertas_images/themes/1x1.gif");
	begin(0,0,"Horizontaly",0);
		add_entry1("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Page Options'"/></xsl:call-template>","#");
			begin(0,-1,"undefined",0);
				<xsl:for-each select="//module/page_options/button">
					href="<xsl:choose>
							<xsl:when test="@command='FILES_ASSOCIATE_FILES'">javascript:file_associate();</xsl:when>
							<xsl:when test="@command='MENU_LIST'">javascript:layout_associate();</xsl:when>
							<xsl:when test="@command='SFORM_BUILD_XML'">javascript:buildform.build_XML();</xsl:when>
							<xsl:when test="@command='LAYOUT_PAGE_RANKING'">javascript:button_action('RANK');</xsl:when>
							<xsl:when test="@iconify='PREVIEW'"><xsl:choose>
									<xsl:when test="../../@display='form' and ../../@name='information_admin'">javascript:preview_infodir('<xsl:value-of select="@command"/>');</xsl:when>
									<xsl:when test="../../@display='form'">javascript:preview_from_form(<xsl:choose>
										<xsl:when test="../../form/input/@name[.='page_identifier']/@value=''">-1,'page',document.<xsl:value-of select="../../form/@name" />,'PAGE_PREVIEW_FORM'</xsl:when>
										<xsl:otherwise><xsl:value-of select="../../form/input[@name='page_identifier']" />,'page',document.<xsl:value-of select="../../form/@name" />,'PAGE_PREVIEW_FORM'</xsl:otherwise>
									</xsl:choose>);</xsl:when>
									<xsl:when test="../../@display='results'">javascript:preview_from_list(<xsl:value-of select="../../form/input/@name[.='page_identifier']/@value"/>,'page');</xsl:when>
									<xsl:otherwise>admin/preview.php?command=PAGE_PREVIEW&amp;<xsl:value-of select="@parameters" /></xsl:otherwise>
						   	</xsl:choose></xsl:when>
							<xsl:when test="@command='GENERAL_BACK'">javascript:history.back();</xsl:when>
							<xsl:otherwise>admin/index.php?command=<xsl:value-of select="@command"/>&amp;<xsl:if test="@parameters"><xsl:value-of select="@parameters"/></xsl:if></xsl:otherwise>
						</xsl:choose>";
			<!-- http://<xsl:value-of select="//setting[@name='domain']"/><xsl:value-of select="//setting[@name='base']"/>admin/index.php -->
					add_entry("<xsl:variable name="possible_alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@alt"/></xsl:call-template></xsl:variable><xsl:variable name="possible_value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:variable><xsl:value-of select="$possible_value"/><xsl:value-of select="$possible_alt"/>",href);
				</xsl:for-each>
  	 	<xsl:choose>
  	 	<xsl:when test="//filter"></xsl:when>
  	 	<xsl:otherwise>
  	 	<xsl:for-each select="//input">
			<xsl:if test="@type='submit' and ../@name!='USERS_SHOW_LOGIN'">
				href="<xsl:choose>
				<xsl:when test="//input/@name='command' and //input/@value='WEBOBJECTS_LAYOUT_SAVE'">javascript:webobjects_submit();</xsl:when>
				<xsl:when test="//textarea[@type='RICH-TEXT']">javascript:ok = onSubmitCompose(2,'<xsl:value-of select="@command"/>');</xsl:when>
				<xsl:otherwise>
					<xsl:choose>
						<xsl:when test="..//@required">javascript:check_required_fields();</xsl:when>
						<xsl:otherwise>javascript:document.<xsl:value-of select="../@name"/>.submit();</xsl:otherwise>
					</xsl:choose>
				</xsl:otherwise>
			</xsl:choose>";
					add_entry("<xsl:variable name="possible_alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@alt"/></xsl:call-template></xsl:variable><xsl:variable name="possible_value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:variable><xsl:value-of select="$possible_value"/><xsl:value-of select="$possible_alt"/>",href);
			</xsl:if>
			<xsl:if test="@type='button'">
			href="<xsl:choose>
					<xsl:when test="@command='BACK'">javascript:history.back();</xsl:when>
					<xsl:when test="@command='LAYOUT_REMOVE_MENU'">javascript:button_action('LAYOUT_REMOVE_MENU');</xsl:when>
					<xsl:when test="@command='LAYOUT_REMOVE_DIRECTORY'">javascript:button_action('LAYOUT_REMOVE_DIRECTORY');</xsl:when>
					<xsl:when test="@command='LAYOUT_SAVE_DIRECTORY'">javascript:button_action('LAYOUT_SAVE_DIRECTORY');</xsl:when>
					<xsl:when test="@command='VEHICLE_LOOKUP_REMOVE'">javascript:lookup_remove(document.<xsl:value-of select="../@name"/>);</xsl:when>
					<xsl:otherwise>admin/index.php?command=<xsl:value-of select="@command"/><xsl:value-of select="@parameters"/></xsl:otherwise>
				</xsl:choose>";
			
					add_entry("<xsl:variable name="possible_alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@alt"/></xsl:call-template></xsl:variable><xsl:variable name="possible_value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@value"/></xsl:call-template></xsl:variable><xsl:value-of select="$possible_value"/><xsl:value-of select="$possible_alt"/>",href);
					</xsl:if>
		   	</xsl:for-each>
		</xsl:otherwise>
		</xsl:choose>
			endSTMB();
	endSTMB();
endSTM();
	


	</xsl:element>

</xsl:template>
<!--
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
-->

</xsl:stylesheet>