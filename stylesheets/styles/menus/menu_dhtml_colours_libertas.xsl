<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:17 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:variable name="dhtml_menu_type">static</xsl:variable>
<xsl:variable name="indent">5</xsl:variable>

<xsl:variable name="menulevel_1_border">0</xsl:variable>
<xsl:variable name="menulevel_n_border">1</xsl:variable>

<xsl:variable name="colour_button_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_on">#a2c2ea</xsl:variable>
<xsl:variable name="colour_text_on">#000000</xsl:variable>
<xsl:variable name="colour_text_off">#000000</xsl:variable>
<xsl:variable name="colour_button_left_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_left_on">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_right_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_right_on">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_top_off">#ffffff</xsl:variable>
<xsl:variable name="colour_button_top_on">#ffffff</xsl:variable>
<xsl:variable name="colour_button_bottom_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_bottom_on">#e5eef9</xsl:variable>

<xsl:variable name="colour_spacer_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_spacer_on">#ffcc33</xsl:variable>
<xsl:variable name="colour_text_spacer_on">#484847</xsl:variable>
<xsl:variable name="colour_text_spacer_off">#484847</xsl:variable>
<xsl:variable name="colour_button_left_spacer_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_left_spacer_on">#cccccc</xsl:variable>
<xsl:variable name="colour_button_right_spacer_off">#cccccc</xsl:variable>
<xsl:variable name="colour_button_right_spacer_on">#cccccc</xsl:variable>
<xsl:variable name="colour_button_top_spacer_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_top_spacer_on">#cccccc</xsl:variable>
<xsl:variable name="colour_button_bottom_spacer_off">#cccccc</xsl:variable>
<xsl:variable name="colour_button_bottom_spacer_on">#cccccc</xsl:variable>

<xsl:variable name="colour_button_first_level_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_first_level_on">#a2c2ea</xsl:variable>
<xsl:variable name="colour_text_first_level_on">#000000</xsl:variable>
<xsl:variable name="colour_text_first_level_off">#000000</xsl:variable>
<xsl:variable name="colour_button_left_first_level_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_left_first_level_on">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_right_first_level_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_right_first_level_on">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_top_first_level_off">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_top_first_level_on">#e5eef9</xsl:variable>
<xsl:variable name="colour_button_bottom_first_level_off">#ffffff</xsl:variable>
<xsl:variable name="colour_button_bottom_first_level_on">#ffffff</xsl:variable>

<xsl:variable name="cellspacing">0</xsl:variable>
<xsl:variable name="padding">0</xsl:variable>
<xsl:variable name="shadow_colour">#000000</xsl:variable>
<xsl:variable name="shadow_depth">5</xsl:variable>
<xsl:variable name="top_colour">#D5D3D3</xsl:variable>
<xsl:variable name="bottom_colour">#ffffff</xsl:variable>
<xsl:variable name="right_colour">#cccccc</xsl:variable>
<xsl:variable name="left_colour">#cccccc</xsl:variable>
<xsl:variable name="level_one_top_colour">#D5D3D3</xsl:variable>
<xsl:variable name="level_one_bottom_colour">#ffffff</xsl:variable>
<xsl:variable name="level_one_right_colour">#cccccc</xsl:variable>
<xsl:variable name="level_one_left_colour">#cccccc</xsl:variable>
<xsl:variable name="outline_depth">1</xsl:variable>
<!-- DHTML menu Variables -->
<xsl:variable name="menu_dhtml_direction">vertically</xsl:variable>
<xsl:variable name="spacer"></xsl:variable>
</xsl:stylesheet>