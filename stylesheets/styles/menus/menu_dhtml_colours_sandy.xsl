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
<xsl:variable name="indent">-30</xsl:variable>
<xsl:variable name="colour_button_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_on">#F7FAAF</xsl:variable>
<xsl:variable name="colour_text_on">#000000</xsl:variable>
<xsl:variable name="colour_text_off">#000000</xsl:variable>
<xsl:variable name="colour_button_left_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_left_on">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_right_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_right_on">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_top_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_top_on">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_bottom_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_bottom_on">#FAF2D9</xsl:variable>

<xsl:variable name="colour_spacer_off">#ffffff</xsl:variable>
<xsl:variable name="colour_spacer_on">#ffffff</xsl:variable>

<xsl:variable name="colour_button_left_spacer_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_left_spacer_on">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_right_spacer_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_right_spacer_on">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_top_spacer_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_top_spacer_on">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_bottom_spacer_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_bottom_spacer_on">#FAF2D9</xsl:variable>

<xsl:variable name="colour_button_first_level_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_first_level_on">#F7FAAF</xsl:variable>
<xsl:variable name="colour_text_first_level_on">#000000</xsl:variable>
<xsl:variable name="colour_text_first_level_off">#000000</xsl:variable>
<xsl:variable name="colour_button_left_first_level_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_left_first_level_on">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_right_first_level_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_right_first_level_on">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_top_first_level_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_top_first_level_on">#FAF2D9</xsl:variable>
<xsl:variable name="colour_button_bottom_first_level_off">#666666</xsl:variable>
<xsl:variable name="colour_button_bottom_first_level_on">#666666</xsl:variable>
<xsl:variable name="colour_spacer_first_level_off">#FAF2D9</xsl:variable>
<xsl:variable name="colour_spacer_first_level_on">#FAF2D9</xsl:variable>

<xsl:variable name="cellspacing">0</xsl:variable>
<xsl:variable name="padding">1</xsl:variable>
<xsl:variable name="shadow_colour">#000000</xsl:variable>
<xsl:variable name="shadow_depth">5</xsl:variable>
<xsl:variable name="top_colour">#979779</xsl:variable>
<xsl:variable name="bottom_colour">#FAF2D9</xsl:variable>
<xsl:variable name="right_colour">#FAF2D9</xsl:variable>
<xsl:variable name="left_colour">#979779</xsl:variable>
<xsl:variable name="level_one_top_colour">#666666</xsl:variable>
<xsl:variable name="level_one_bottom_colour">#FAF2D9</xsl:variable>
<xsl:variable name="level_one_right_colour">#666666</xsl:variable>
<xsl:variable name="level_one_left_colour">#666666</xsl:variable>
<xsl:variable name="outline_depth">1</xsl:variable>

</xsl:stylesheet>