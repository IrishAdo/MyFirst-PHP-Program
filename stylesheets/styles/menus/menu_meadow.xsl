<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:19 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:variable name="current_site_depth"><xsl:value-of select="//menu[url=//setting[@name='script']]/@depth"/></xsl:variable>
<xsl:variable name="menu_splits_at_depth">-1</xsl:variable>
<xsl:template name="display_menu">
<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/web_menu.js"></script>
<script type="text/javascript" language="JavaScript1.2">

beginSTM("Libertas_Solutions","static","0","0","none","false","true","310","0","0","400","","/libertas_images/themes/themes/1x1.gif");
	begin(0,0,"undefined",1);
	<xsl:for-each select="//xml_document/modules/module[@name='layout']">
			<xsl:call-template name="display_menu_parent">
				<xsl:with-param name="parent_identifier" select="-1"/>       
				<xsl:with-param name="current_url" select="//setting[@name='script']"/>       
	       	</xsl:call-template>
	</xsl:for-each>
	endSTMB();
endSTM();

<xsl:variable name="colour_button_off">#CCCCFF</xsl:variable>
<xsl:variable name="colour_button_on">#CCFFCC</xsl:variable>
<xsl:variable name="colour_text_on">#000000</xsl:variable>
<xsl:variable name="colour_text_off">#000000</xsl:variable>
<xsl:variable name="colour_button_left_off">#EBEBEB</xsl:variable>
<xsl:variable name="colour_button_left_on">#99FF99</xsl:variable>
<xsl:variable name="colour_button_right_off">#808080</xsl:variable>
<xsl:variable name="colour_button_right_on">#339900</xsl:variable>
<xsl:variable name="colour_button_top_off">#EBEBEB</xsl:variable>
<xsl:variable name="colour_button_top_on">#99FF99</xsl:variable>
<xsl:variable name="colour_button_bottom_off">#808080</xsl:variable>
<xsl:variable name="colour_button_bottom_on">#339900</xsl:variable>

function add_entry(label_str,url_str, alt_str){

	appendSTMI("false",label_str,"left","middle","","","-1","-1","0","normal",
	"<xsl:value-of select="$colour_button_off"/>",
	"<xsl:value-of select="$colour_button_on"/>",
	"","1","-1","-1","/libertas_images/themes/1x1.gif","/libertas_images/themes/1x1.gif","0","0","", alt_str, url_str, "_self",
	"Arial","9pt","<xsl:value-of select="$colour_text_off"/>","bold","normal","none",
	"Arial","9pt","<xsl:value-of select="$colour_text_on"/>","bold","normal","none",
	"1","solid",
	"<xsl:value-of select="$colour_button_left_off"/>",
	"<xsl:value-of select="$colour_button_right_off"/>",
	"<xsl:value-of select="$colour_button_left_on"/>",
	"<xsl:value-of select="$colour_button_right_on"/>",
	"<xsl:value-of select="$colour_button_top_off"/>",
	"<xsl:value-of select="$colour_button_bottom_off"/>",
	"<xsl:value-of select="$colour_button_top_on"/>",
	"<xsl:value-of select="$colour_button_bottom_on"/>",
	"","","","tiled","tiled");
}
function begin(pos_x,pos_y,direction_required,img){
	image_file="";
	if (direction_required+""=="undefined"){
		if (img==1){
			image_file="/libertas_images/themes/meadow/arrow_r.gif";
		}
		beginSTMB("auto",pos_x,pos_y,"vertically",image_file,"8","8","0","1","transparent","","tiled",
					"#000000","0","solid","10","Normal",
					"50","8","8","7","7","0","0","5","#000000","false","#000000","#000000","#000000","complex");
	}else{
		beginSTMB("auto",pos_x,pos_y,direction_required,"/libertas_images/themes/arrow_d.gif","8","8","0","0","transparent","","tiled",
					"#FFFFFF","0","solid","0","Normal",
					"50","20","10","7","7","0","0","0","#000000","false","#000000","#000000","#000000","complex");
	}
}
</script>
</xsl:template>



<xsl:template name="display_menu_parent">
	<xsl:param name="parent_identifier"/>
	<xsl:param name="current_url"/>

	<xsl:for-each select="menu[@parent=$parent_identifier and @hidden='0']">
		add_entry("<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:with-param></xsl:call-template>","<xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='base']"/><xsl:value-of select="url"/>?<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>","<xsl:choose>
				<xsl:when test="alt_text!=''"><xsl:value-of select="alt_text" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose>");
		<xsl:if test="./children/menu[@hidden='0']">
			begin(0,0,"undefined",1);
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


</xsl:stylesheet>