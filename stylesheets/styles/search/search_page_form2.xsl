<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.6 $
- Modified $Date: 2004/09/11 09:58:45 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="display_page_search">
	<xsl:param name="buttonType"><xsl:value-of select="$form_button_type"/></xsl:param>
	<xsl:param name="searchType">ROW</xsl:param>
	<xsl:param name="uses_class"></xsl:param>
	<form method="get"><xsl:attribute name="action"><xsl:choose>
		<xsl:when test="//display[.='PAGE_SEARCH']"><xsl:value-of select="//menu[display_options/display[.='PAGE_SEARCH']]/url"/></xsl:when>
		<xsl:otherwise>-search.php</xsl:otherwise>
	</xsl:choose></xsl:attribute>
		<xsl:if test="uid!=''">
		<xsl:attribute name="id">search_box_<xsl:value-of select="uid"/></xsl:attribute>
	</xsl:if>

	<div>
		<!-- <input type="hidden" name="command" value="PRESENTATION_SEARCH"/> -->
		<input type="hidden" name="advanced" value="0"/>				
		<input type="hidden" name="associated_list" value=""/>				
		<input type="hidden" name="page" value="1"/>				
		<input type="hidden" name="search" value="0"/>
		<xsl:choose>
			<xsl:when test="$searchType='COLUMN'">
					<div class="aligncenter">
					<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
					<label><xsl:attribute name="for">search_<xsl:value-of select="uid"/></xsl:attribute><xsl:attribute name='class'><xsl:choose>
									<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
									<xsl:otherwise>searchlabel</xsl:otherwise>
								</xsl:choose></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SEARCH_LABEL'"/></xsl:call-template></label><br/>
					</xsl:if>
					<input type="text" name="page_search" class="headersearch" maxlength="255" size="12">
					<xsl:attribute name="id">search_<xsl:value-of select="uid"/></xsl:attribute>
<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
  <xsl:attribute name="onfocus">javascript:__FRM_reset(this);</xsl:attribute>
</xsl:if>
					<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
					<xsl:attribute name="value"><xsl:if test="//setting[@name='sp_wai_forms']!='No'"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SEARCH_BOX_DEFAULT_TXT'"/></xsl:call-template></xsl:if></xsl:attribute>
					</xsl:if>
					</input>
					<br/>
					<xsl:call-template name="display_page_search_button"><xsl:with-param name="buttonType"><xsl:value-of select="$buttonType"/></xsl:with-param></xsl:call-template>
				</div>
			</xsl:when>
			<xsl:otherwise>
<!--				<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
				<label><xsl:attribute name="for">search_<xsl:value-of select="uid"/></xsl:attribute><xsl:attribute name='class'><xsl:choose>
									<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
									<xsl:otherwise>searchlabel</xsl:otherwise>
								</xsl:choose></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SEARCH_LABEL'"/></xsl:call-template></label>
				</xsl:if>
				<input type="text" name="page_search" class="headersearch" id="search" maxlength="255" size="4">
				<xsl:if test="//setting[@name='sp_wai_forms']!='No'">
				<xsl:attribute name="value"></xsl:attribute>
				</xsl:if>
				</input>
				<input type="submit" value="Go" class="bt"/>-->
			</xsl:otherwise>
		</xsl:choose></div>
	</form>
<xsl:if test="//setting[@name='sp_blank_field_on_click']='Yes' and //setting[@name='sp_wai_forms']!='No'">
 	<script type="text/javascript">
 		__FRM_add('search_box_<xsl:value-of select="uid"/>');
 	</script>
</xsl:if>
</xsl:template>

<xsl:template name="display_page_search_button">
<xsl:param name="buttonType"><xsl:value-of select="$form_button_type"/></xsl:param>
		<xsl:choose>
			<xsl:when test="$buttonType='IMAGE'">
				<input  type="image"  class="searchbutton"><xsl:attribute name="alt"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SEARCH_BUTTON_ALT'"/></xsl:call-template></xsl:attribute><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_SEARCH.gif</xsl:attribute></input>
			</xsl:when>
			<xsl:when test="$buttonType='HTML_WITH_ARROWS'">
				<input  type="submit" class="searchbutton"><xsl:attribute name="value">&gt; <xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SEARCH_BUTTON_ALT'"/></xsl:call-template> &lt;</xsl:attribute></input>
			</xsl:when>
			<xsl:when test="$buttonType='HTML'">
				<input  type="submit" class="searchbutton"><xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SEARCH_BUTTON_ALT'"/></xsl:call-template></xsl:attribute></input>
			</xsl:when>
			<xsl:otherwise>
				<input  type="submit" class="searchbutton"><xsl:attribute name="value"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SEARCH_BUTTON_ALT'"/></xsl:call-template></xsl:attribute></input>
			</xsl:otherwise>
		</xsl:choose>
</xsl:template>


</xsl:stylesheet>