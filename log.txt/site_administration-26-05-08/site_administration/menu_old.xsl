<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/09/06 16:50:01 $
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
	<xsl:element name="script">
		<xsl:attribute name="type">text/javascript</xsl:attribute>
		<xsl:attribute name="language">JavaScript1.2</xsl:attribute>
		http = "http<xsl:if test="//setting[@name='SSL']='yes'">s</xsl:if>://";
var access_list =Array(
	<xsl:for-each select="//xml_document/modules/session/groups[@type=2]/group[@type=2]/access">
		"<xsl:value-of select="."/>" <xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
);

var menu_list = Array(<xsl:for-each select="grouping">
	<xsl:sort select="@name" order="ascending"/>
	<xsl:sort select="mod/@label" order="ascending"/>
	Array ("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template>","#",Array(<xsl:for-each select="mod">
		Array("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template>","<xsl:value-of select="options/@tag"/>","<xsl:choose>
				<xsl:when test="count(options/option)=1">?command=<xsl:value-of select="options/option[position()=1]/@value"/><xsl:if test="//setting[@name='cookieset']=''">&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/></xsl:if></xsl:when>
					<xsl:otherwise></xsl:otherwise>
					</xsl:choose>",<xsl:choose>
					<xsl:when test="count(options/option)>0">
						Array(
							<xsl:for-each select="options/option[@value!=../../@ignore]">
								<xsl:sort select="@value" order="ascending"/>
								<xsl:variable name="me"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></xsl:variable>
								<xsl:variable name="value"><xsl:value-of select="@value"/></xsl:variable>
								Array("<xsl:value-of select="$me"/>","<xsl:value-of select="$value"/>","<xsl:value-of select="@grouping"/>","?command=<xsl:value-of select="$value"/><xsl:if test="//setting[@name='cookieset']=''">&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/></xsl:if>")<xsl:if test="position()!=last()">, </xsl:if>
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
var total_visibility= 100;
var direction		= 4; // horizontal
var showdelay		= 200;
var hidedelay 		= 1000;
var padding			= 4;
var spacing			= 0;
var backgroundimage = "";

stm_bm(["Libertas_Solutions",400,"","/libertas_images/themes/1x1.gif",0,"","",0,1,showdelay,showdelay,hidedelay,1,0,0,""],this);
	begin(0,0,"Horizontally",0,"p0",1);
		add_entry1("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_HOME'"/></xsl:call-template>","","p0i0");
		begin(0,-1,"vertically",0,"p1",0,4);
			href="http://<xsl:value-of select="//setting[@name='domain']"/><xsl:value-of select="//setting[@name='base']"/>admin/index.php";
			add_entry("Digital Desktop",href,"p0i1","p0i0");
			href="http://<xsl:value-of select="//setting[@name='domain']"/><xsl:value-of select="//setting[@name='base']"/>index.php";
			add_entry("Return to Site",href,"p0i2","p0i0");
			add_entry("<hr/>","","p0i2","p0i0");
			href="admin/index.php?command=ENGINE_LOGOUT";
			add_entry("Exit (logout)",href,"p0i2","p0i0");
		stm_ep();

		libertas_dhtml_menu = add_admin_options(menu_list,access_list);
		men = 1
		for (key in libertas_dhtml_menu){
			pos=0;
			for (libertas_dhtml_menu_entry in libertas_dhtml_menu[key].children){
			
				if (libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry].length!=0){
				show=0;
					for (option=0;option != libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry].length;option++){
						if (libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry][option][1]+'' !=''){
							show=1;
						}
					}
					if(show==1){
						if(libertas_dhtml_menu[key].displayed==0){
							add_entry1(key,"","p0i"+men);
			 				begin(0,-1,"undefined",0,"p0"+men,0, 4, 0);
							libertas_dhtml_menu[key].displayed=1;
						}
						if (libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry].length != 1){
							add_entry(libertas_dhtml_menu_entry,"","p"+men+"i"+pos, "p0"+men, 1);
		 					begin(0,-1,"undefined",0,"p0"+men, 1, 2, 1);
						}
						for (option=0;option != libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry].length;option++){
							if (libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry][option][1]+'' !=''){
								href = "http://"+domain + base_href + "admin/index.php"+libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry][option][1];
								add_entry(libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry][option][0], href,"p"+men+"i"+pos, "p0"+men);
							}
						}
	
						if (libertas_dhtml_menu[key].children[libertas_dhtml_menu_entry].length != 1){
							stm_ep();
						}
						pos++;
					}
				}
			}
			men++;
			if(libertas_dhtml_menu[key].displayed==1){
				if (key!=prev){
				stm_ep();
				}
			}
			prev=key;
		}
		add_entry1("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_HELP'"/></xsl:call-template>","","p0i99");
			begin(0,0,"undefined",0,"p99",0,4);
				add_entry("About Us","http://www.libertas-solutions.com", "p99i1", "p0i99");
				add_entry("<hr/>","", "p99i1", "p0i99");
				href="http://"+domain + base_href + "admin/index.php?command=ENGINE_VERSIONS&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>"
				add_entry("Module Versions",href, "p99i1", "p0i99",0);
				add_entry("PHP Info","http://"+domain + base_href + "admin/phpinfo.php", "p99i1", "p0i99");
				<xsl:if test="//setting[@name='domain'] = 'caplo' or //setting[@name='domain'] = 'professor'">
				add_entry("Development","", "p199i1", "p0i99",1);
				begin(0,-1,"undefined",0,"p19901", 1, 2, 1);
					add_entry("System Debug","http://"+domain + base_href + "admin/index.php?command=SYSPREFS_DEBUG_ADMIN", "p19901i1", "p19901");
					add_entry("Regenerate Menu","http://"+domain + base_href + "admin/index.php?command=ENGINE_REGEN_MENUS", "p19901i2", "p19901");
				stm_ep();
				</xsl:if>
			stm_ep();
	stm_ep();
stm_em();
function add_entry1(label_str,url_str,current_id){
	stm_ai(current_id,
		[0,label_str,"","",-1,-1,0,url_str,"_self","","","","",0,0,0,
		"",
		"",
		0 ,0 ,0 ,0 ,0 ,"",1,"#B6BDD2",0,
		"","",3,3,1,1,"#fcfcfc #ffffff #cccccc #999999","#0A246A","#333333","#333333","0.9em Verdana","0.9em Verdana"]);
}
function add_entry(label_str,url_str,parent_id,current_id, has_children){
	if (has_children+"" == "undefined"){
		has_children = 0;
	}
	if (has_children == 1){
		image_width   = 10;
		image_height  = 10;
		image_source1 = "/libertas_images/themes/site_administration/arrow_r.gif";
		image_source2 = "/libertas_images/themes/site_administration/arrow_r.gif";
	} else {
		image_width   = 0;
		image_height  = 0;
		image_source1 = "";
		image_source2 = "";
	}
	
	if (label_str=="<hr/>"){
		stm_aix(parent_id,current_id,
			[0,label_str,"","",-1,-1,0,url_str,"_self","","","","",0,0,0,
			"","",
			0,0,0,0,1,"<xsl:value-of select="$colour_button_off"/>",1,"<xsl:value-of select="$colour_button_off"/>",0,
			"","",3,3,1,1,"#ffffff","#ebebeb","#ebebeb","#ebebeb","0.9em Verdana","0.9em Verdana"]);
	} else {
		stm_aix(parent_id,current_id,
		[0,label_str,"","",-1,-1,0,url_str,"_self","","","","",0,0,0,
		image_source1, image_source1, image_width, image_height ,0 ,0 ,1 ,"<xsl:value-of select="$colour_button_off"/>",0,"#B6BDD2",0,
		"","",3,3,1,1,"#ffffff","#0A246A","#333333","#333333","0.9em Verdana","0.9em Verdana"]);
/*
		stm_aix(parent_id,current_id,
			[0,label_str,"","",10,10,1,url_str,"_self","","","","",0,0,0,
			"/libertas_images/themes/site_administration/arrow_r.gif",
			"/libertas_images/themes/site_administration/arrow_r.gif",
			10 ,10 ,0 ,0 ,1 ,"<xsl:value-of select="$colour_button_off"/>",0,"#dfdfdf",0,
			"","",3,3,1,1,"#ffffff","#666666","#333333","#333333","0.9em Verdana","0.9em Verdana"]);
*/
	}
}
function begin(x,y, mydirection,img,nme, trans, pos){
	arrow_image="";
	if (pos+"" == "undefined"){
		pos=2;
	}
	if (trans+"" == "undefined"){
		trans = 0  
	}
	if (trans==0) {
		buttoncolor ="<xsl:value-of select="$colour_button_off"/>";
	} else {
		buttoncolor ="transparent";
	}
	iconwidth =10;
	imagewidth=10;
	
	if (mydirection+""=="undefined" || mydirection+""=="vertically"){
		stm_bp(nme,[1,pos,0,0,spacing,padding,iconwidth,imagewidth,total_visibility, "",-2,"",-2,90,3,3,"#000000",
		buttoncolor, backgroundimage, 0, 1, 1, "#000000"]);
	}else{
		stm_bp(nme,[0,pos,0,0,spacing,padding, iconwidth, imagewidth, total_visibility , "",-2,"",-2,90,0,0,"#000000",
		buttoncolor, backgroundimage, 0, 1, 1,"#ffffff #999999 #cccccc #999999"]);
	}
	
}
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