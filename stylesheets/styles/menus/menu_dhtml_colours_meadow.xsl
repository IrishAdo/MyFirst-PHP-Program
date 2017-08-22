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

<xsl:variable name="colour_spacer_off">#CCCCFF</xsl:variable>
<xsl:variable name="colour_spacer_on">#CCFFCC</xsl:variable>
<xsl:variable name="colour_text_spacer_on">#000000</xsl:variable>
<xsl:variable name="colour_text_spacer_off">#000000</xsl:variable>
<xsl:variable name="colour_button_left_spacer_off">#EBEBEB</xsl:variable>
<xsl:variable name="colour_button_left_spacer_on">#99FF99</xsl:variable>
<xsl:variable name="colour_button_right_spacer_off">#808080</xsl:variable>
<xsl:variable name="colour_button_right_spacer_on">#339900</xsl:variable>
<xsl:variable name="colour_button_top_spacer_off">#EBEBEB</xsl:variable>
<xsl:variable name="colour_button_top_spacer_on">#99FF99</xsl:variable>
<xsl:variable name="colour_button_bottom_spacer_off">#808080</xsl:variable>
<xsl:variable name="colour_button_bottom_spacer_on">#339900</xsl:variable>

<xsl:variable name="colour_button_first_level_off">#CCCCFF</xsl:variable>
<xsl:variable name="colour_button_first_level_on">#CCFFCC</xsl:variable>
<xsl:variable name="colour_text_first_level_on">#000000</xsl:variable>
<xsl:variable name="colour_text_first_level_off">#000000</xsl:variable>
<xsl:variable name="colour_button_left_first_level_off">#EBEBEB</xsl:variable>
<xsl:variable name="colour_button_left_first_level_on">#99FF99</xsl:variable>
<xsl:variable name="colour_button_right_first_level_off">#808080</xsl:variable>
<xsl:variable name="colour_button_right_first_level_on">#339900</xsl:variable>
<xsl:variable name="colour_button_top_first_level_off">#EBEBEB</xsl:variable>
<xsl:variable name="colour_button_top_first_level_on">#99FF99</xsl:variable>
<xsl:variable name="colour_button_bottom_first_level_off">#808080</xsl:variable>
<xsl:variable name="colour_button_bottom_first_level_on">#339900</xsl:variable>

<xsl:variable name="cellspacing">0</xsl:variable>
<xsl:variable name="padding">1</xsl:variable>
<xsl:variable name="shadow_colour">#000000</xsl:variable>
<xsl:variable name="shadow_depth">5</xsl:variable>
<xsl:variable name="top_colour">#979779</xsl:variable>
<xsl:variable name="bottom_colour">#339900</xsl:variable>
<xsl:variable name="right_colour">#339900</xsl:variable>
<xsl:variable name="left_colour">#979779</xsl:variable>
<xsl:variable name="level_one_top_colour">#666666</xsl:variable>
<xsl:variable name="level_one_bottom_colour">#339900</xsl:variable>
<xsl:variable name="level_one_right_colour">#666666</xsl:variable>
<xsl:variable name="level_one_left_colour">#666666</xsl:variable>
<xsl:variable name="outline_depth">1</xsl:variable>
<!-- DHTML menu Variables -->
<xsl:variable name="menu_dhtml_direction">vertically</xsl:variable>
<xsl:variable name="spacer"> | </xsl:variable>
</xsl:stylesheet>