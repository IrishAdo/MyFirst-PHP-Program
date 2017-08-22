<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.7 $
- Modified $Date: 2005/01/11 16:36:48 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:variable name="current_site_depth"><xsl:value-of select="//menu[url=//setting[@name='script']]/@depth"/></xsl:variable>
<xsl:variable name="menu_splits_at_depth">-1</xsl:variable>
<xsl:template name="display_menu">
	<xsl:param name="spacer"></xsl:param>
	
<noscript>
<xsl:call-template name="display_noscript"></xsl:call-template>
</noscript>
<script type="text/javascript"  src="/libertas_images/javascripts/web_menu.js"></script>
<script type="text/javascript" >
level_one_image_file = "";
level_one_image_width = "0";
level_one_image_height = "0";
level_x_image_file = "<xsl:value-of select="$image_path"/>/arrow_r.gif";
level_x_image_width = "10";
level_x_image_height = "10";

beginSTM("Libertas_Solutions","<xsl:value-of select="$dhtml_menu_type"/>","0","0","none","false","true","310","0","0","100","","/libertas_images/themes/1x1.gif");
	begin(0,0,"<xsl:value-of select="$menu_dhtml_direction"/>",1,level_one_image_file, level_one_image_width, level_one_image_height,"<xsl:value-of select="$level_one_top_colour"/>", "<xsl:value-of select="$level_one_bottom_colour"/>", "<xsl:value-of select="$level_one_right_colour"/>", "<xsl:value-of select="$level_one_left_colour"/>");
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
		<xsl:for-each select="menu[@hidden=0]">
			<xsl:if test="$spacer!=''">
			add_spacer("<xsl:value-of select="$spacer"/>","#");
			</xsl:if>
			mylink="<xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/><xsl:value-of select="url"/>";
			label="<xsl:value-of select="label"/>";
			alt_label="<xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose>";
			add_entry_first_level(label,mylink,alt_label);
			<xsl:if test="./children/menu[@hidden=0]">
				begin(<xsl:value-of select="$indent"/>,0,"undefined",0,level_x_image_file, level_x_image_width, level_x_image_height, "<xsl:value-of select="$top_colour"/>", "<xsl:value-of select="$bottom_colour"/>", "<xsl:value-of select="$right_colour"/>", "<xsl:value-of select="$left_colour"/>");
				<xsl:for-each select="./children">
	   	 			<xsl:call-template name="display_menu_parent">
						<xsl:with-param name="parent_identifier" select="../@identifier"/>       
						<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
   	    			</xsl:call-template>
   	    		</xsl:for-each>
				endSTMB();
			</xsl:if>
			<xsl:if test="position()=last() and $spacer!=''">add_spacer("<xsl:value-of select="$spacer"/>","#");</xsl:if>
		</xsl:for-each>
	</xsl:for-each>
	endSTMB();
endSTM();


function add_entry_first_level(label_str,url_str, alt_str){
	appendSTMI("false",label_str,"left","middle","","","-1","-1","0","normal",
	"<xsl:value-of select="$colour_button_first_level_off"/>",
	"<xsl:value-of select="$colour_button_first_level_on"/>",
	"","1","-1","-1","/libertas_images/themes/1x1.gif","/libertas_images/themes/1x1.gif","0","0","0",alt_str,url_str,"_self",
	"Arial","0.9em","<xsl:value-of select="$colour_text_first_level_off"/>","bold","normal","none",
	"Arial","0.9em","<xsl:value-of select="$colour_text_first_level_on"/>","bold","normal","none",
	"1","solid",
	"<xsl:value-of select="$colour_button_right_first_level_off"/>",
	"<xsl:value-of select="$colour_button_left_first_level_off"/>",
	"<xsl:value-of select="$colour_button_right_first_level_on"/>",
	"<xsl:value-of select="$colour_button_left_first_level_on"/>",
	"<xsl:value-of select="$colour_button_top_first_level_off"/>",
	"<xsl:value-of select="$colour_button_bottom_first_level_off"/>",
	"<xsl:value-of select="$colour_button_top_first_level_on"/>",
	"<xsl:value-of select="$colour_button_bottom_first_level_on"/>",
	"","","","tiled","tiled");
}
function add_entry(label_str,url_str,alt_str){
	appendSTMI("false",label_str,"left","middle","","","-1","-1","0","normal",
	"<xsl:value-of select="$colour_button_off"/>",
	"<xsl:value-of select="$colour_button_on"/>",
	"","1","-1","-1","/libertas_images/themes/1x1.gif","/libertas_images/themes/1x1.gif","0","0","0",alt_str,url_str,"_self",
	"Arial","0.9em","<xsl:value-of select="$colour_text_off"/>","bold","normal","none",
	"Arial","0.9em,"<xsl:value-of select="$colour_text_on"/>","bold","normal","none",
	"1","solid",
	"<xsl:value-of select="$colour_button_right_off"/>",
	"<xsl:value-of select="$colour_button_left_off"/>",
	"<xsl:value-of select="$colour_button_right_on"/>",
	"<xsl:value-of select="$colour_button_left_on"/>",
	"<xsl:value-of select="$colour_button_top_off"/>",
	"<xsl:value-of select="$colour_button_bottom_off"/>",
	"<xsl:value-of select="$colour_button_top_on"/>",
	"<xsl:value-of select="$colour_button_bottom_on"/>",
	"","","","tiled","tiled");
}
function add_spacer(label_str,url_str){
	appendSTMI("false",label_str,"left","middle","","","-1","-1","0","normal",
	"<xsl:value-of select="$colour_spacer_on"/>",
	"<xsl:value-of select="$colour_spacer_on"/>",
	"","1","-1","-1","/libertas_images/themes/1x1.gif","/libertas_images/themes/1x1.gif","0","0","0","",url_str,"_self",
	"Arial","0.9em","<xsl:value-of select="$colour_text_off"/>","bold","normal","none",
	"Arial","0.9em","<xsl:value-of select="$colour_text_off"/>","bold","normal","none",
	"1","solid",
	"<xsl:value-of select="$colour_button_left_spacer_off"/>",
	"<xsl:value-of select="$colour_button_right_spacer_off"/>",
	"<xsl:value-of select="$colour_button_left_spacer_on"/>",
	"<xsl:value-of select="$colour_button_right_spacer_on"/>",
	"<xsl:value-of select="$colour_button_top_spacer_off"/>",
	"<xsl:value-of select="$colour_button_bottom_spacer_off"/>",
	"<xsl:value-of select="$colour_button_top_spacer_on"/>",
	"<xsl:value-of select="$colour_button_bottom_spacer_on"/>",
	"","","","tiled","tiled");
}
function begin(pos_x, pos_y, direction_required, img, image_file, image_width, image_height, top_colour, bottom_colour, right_colour, left_colour){
spacing 		= <xsl:value-of select="$cellspacing"/>;
padding 		= <xsl:value-of select="$padding"/>;
shadow_colour 	= "<xsl:value-of select="$shadow_colour"/>";
shadow_depth 	= <xsl:value-of select="$shadow_depth"/>;
outline_depth	= <xsl:value-of select="$outline_depth"/>;
	if (direction_required+""=="undefined"){
		beginSTMB("auto",pos_x,pos_y,"vertically",image_file+"",image_width+"",image_height+"",spacing,padding,"transparent","","tiled",
		left_colour,outline_depth,"solid", "0", "Normal","50","8","8","7","7","0","0", shadow_depth, shadow_colour, "false", top_colour, right_colour, 
		bottom_colour, "complex");
	}else{
		beginSTMB("auto",pos_x,pos_y,direction_required,image_file+"",image_width+"",image_height+"",spacing,padding,"transparent","","tiled",
		left_colour,outline_depth,"solid","0","Normal","50","8","8","7","7","0","0", shadow_depth, shadow_colour, "false", top_colour, right_colour, 
		bottom_colour, "complex");
		
	}
}
</script>
</xsl:template>



<xsl:template name="display_menu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
		<xsl:for-each select="menu[@parent=$parent_identifier and @hidden=0]">
		mylink="<xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/><xsl:value-of select="url"/>";
		alt_label="<xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose>";

		add_entry("<xsl:call-template name="get_translation"><xsl:with-param name="check" select="label"/></xsl:call-template>",mylink, alt_label);
		<xsl:if test="./children/menu">
			begin(0, 0, "undefined", 1, level_x_image_file, level_x_image_width, level_x_image_height, "<xsl:value-of select="$top_colour"/>", "<xsl:value-of select="$bottom_colour"/>", "<xsl:value-of select="$right_colour"/>", "<xsl:value-of select="$left_colour"/>");
			<xsl:for-each select="./children">
    			<xsl:call-template name="display_menu_parent">
					<xsl:with-param name="parent_identifier" select="../@identifier"/>       
					<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
       			</xsl:call-template>
       		</xsl:for-each>
			endSTMB();
		</xsl:if>
	</xsl:for-each>
</xsl:template>






<xsl:template name="display_noscript">
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
      <table cellspacing="0" border="0" width="100%" summary="This table contains the menu"><xsl:call-template name="display_menu_parent_noscript">
			<xsl:with-param name="parent_identifier" select="-1"/>       
			<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
       	</xsl:call-template></table>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_menu_parent_noscript">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>
	<xsl:for-each select="menu[@parent=$parent_identifier and @hidden=0]">
		<xsl:variable name="found">
			<xsl:if test="url='admin/index.php' and //xml_document/modules/session/groups/@type=2">2</xsl:if>
			<xsl:choose>
				<xsl:when test="boolean(groups)">
					<xsl:for-each select="groups/option">
						<xsl:variable name="val"><xsl:value-of select="@value"/></xsl:variable>
						<xsl:for-each select="//xml_document/modules/session/groups/group">
							<xsl:if test="$val=@identifier">1</xsl:if>
						</xsl:for-each>
					</xsl:for-each>
				</xsl:when>
				<xsl:otherwise>1</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
	
	<xsl:if test="($found!='') or count(groups/option)=0">
		<tr><td><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
		<a><xsl:attribute name="class">menulevel<xsl:value-of select="@depth"/></xsl:attribute>
		<xsl:attribute name="href"><xsl:value-of select="url"/></xsl:attribute>
		<xsl:attribute name="title"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="label"/></xsl:call-template></xsl:attribute>
		<xsl:call-template name="display_indent">
			<xsl:with-param name="depth" select="@depth"/>       
       	</xsl:call-template><xsl:value-of select="label"/></a></td></tr>
		<xsl:if test="url=$current_url">
			<xsl:for-each select="./children">
    			<xsl:call-template name="display_menu_parent_noscript">
					<xsl:with-param name="parent_identifier" select="../@identifier"/>       
					<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
       			</xsl:call-template>
       		</xsl:for-each>
    	</xsl:if>
		<xsl:if test=".//children/menu[url=$current_url and @hidden=0]">
			<xsl:for-each select="./children">
	    		<xsl:call-template name="display_menu_parent_noscript">
					<xsl:with-param name="parent_identifier" select="../@identifier"/>       
					<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
    		   	</xsl:call-template>
		 	</xsl:for-each>
    	</xsl:if>
    	</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_indent">
	<xsl:param name="depth"/>
	<xsl:if test="$depth>1">
		[[nbsp]]-[[nbsp]]
		<xsl:variable name="new_depth"><xsl:value-of select="$depth - 1"/></xsl:variable>
		<xsl:call-template name="display_indent">
			<xsl:with-param name="depth" select="$new_depth"/>       
       	</xsl:call-template>
	</xsl:if>
</xsl:template>

</xsl:stylesheet>