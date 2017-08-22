<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/08/24 13:21:16 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
  
<xsl:variable name="dhtml_menu_type">relative</xsl:variable>
<xsl:variable name="indent">-30</xsl:variable>
<xsl:variable name="colour_button_off">#FFFF99</xsl:variable>
<xsl:variable name="colour_button_on">#BDDAF6</xsl:variable>
<xsl:variable name="colour_text_on">#000000</xsl:variable>
<xsl:variable name="colour_text_off">#000000</xsl:variable>
<xsl:variable name="colour_button_left_off">#1472CF</xsl:variable>
<xsl:variable name="colour_button_left_on">#FFFF66</xsl:variable>
<xsl:variable name="colour_button_right_off">#cccc00</xsl:variable>
<xsl:variable name="colour_button_right_on">#4C9DEE</xsl:variable>
<xsl:variable name="colour_button_top_off">#1472CF</xsl:variable>
<xsl:variable name="colour_button_top_on">#4C9DEE</xsl:variable>
<xsl:variable name="colour_button_bottom_off">#cccc00</xsl:variable>
<xsl:variable name="colour_button_bottom_on">#4C9DEE</xsl:variable>

<xsl:variable name="colour_spacer_off">#FFFF99</xsl:variable>
<xsl:variable name="colour_spacer_on">#FFFF99</xsl:variable>

<xsl:variable name="colour_button_left_spacer_off">#FFFF99</xsl:variable>
<xsl:variable name="colour_button_left_spacer_on">#FFFF99</xsl:variable>
<xsl:variable name="colour_button_right_spacer_off">#FFFF99</xsl:variable>
<xsl:variable name="colour_button_right_spacer_on">#FFFF99</xsl:variable>
<xsl:variable name="colour_button_top_spacer_off">#FFFF99</xsl:variable>
<xsl:variable name="colour_button_top_spacer_on">#FFFF99</xsl:variable>
<xsl:variable name="colour_button_bottom_spacer_off">#FFFF99</xsl:variable>
<xsl:variable name="colour_button_bottom_spacer_on">#FFFF99</xsl:variable>

<xsl:variable name="colour_button_first_level_off">#FFFF99</xsl:variable>
<xsl:variable name="colour_button_first_level_on">#BDDAF6</xsl:variable>
<xsl:variable name="colour_text_first_level_on">#000000</xsl:variable>
<xsl:variable name="colour_text_first_level_off">#000000</xsl:variable>

<xsl:variable name="colour_button_left_first_level_off">#99CCFF</xsl:variable>
<xsl:variable name="colour_button_right_first_level_off">#CCCC00</xsl:variable>
<xsl:variable name="colour_button_top_first_level_off">#99CCFF</xsl:variable>
<xsl:variable name="colour_button_bottom_first_level_off">#CCCC00</xsl:variable>

<xsl:variable name="colour_button_left_first_level_on">#CCCC00</xsl:variable>
<xsl:variable name="colour_button_right_first_level_on">#99CCFF</xsl:variable>
<xsl:variable name="colour_button_top_first_level_on">#CCCC00</xsl:variable>
<xsl:variable name="colour_button_bottom_first_level_on">#99CCFF</xsl:variable>

<xsl:variable name="colour_spacer_first_level_off">#FFFF99</xsl:variable>
<xsl:variable name="colour_spacer_first_level_on">#FFFF99</xsl:variable>

<xsl:variable name="cellspacing">0</xsl:variable>
<xsl:variable name="padding">1</xsl:variable>
<xsl:variable name="shadow_colour">#4C9DEE</xsl:variable>
<xsl:variable name="shadow_depth">0</xsl:variable>
<xsl:variable name="top_colour">#979779</xsl:variable>
<xsl:variable name="bottom_colour">#FFFF99</xsl:variable>
<xsl:variable name="right_colour">#FFFF99</xsl:variable>
<xsl:variable name="left_colour">#979779</xsl:variable>
<xsl:variable name="level_one_top_colour">#FFFF99</xsl:variable>
<xsl:variable name="level_one_bottom_colour">#FFFF99</xsl:variable>
<xsl:variable name="level_one_right_colour">#FFFF99</xsl:variable>
<xsl:variable name="level_one_left_colour">#FFFF99</xsl:variable>
<xsl:variable name="outline_depth">1</xsl:variable>

</xsl:stylesheet>