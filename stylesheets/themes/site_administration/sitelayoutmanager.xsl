<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:50:08 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="drawDefination">
	<table width="100%" height="100%">
		<tr>
			<td valign='top' id='placeholdersdisplay'><p>[[nbsp]]</p><h1>Please wait while the system loads this layout manager interface</h1></td>
	<td width="200" valign="top" id='offline_form' name='offline_form' style="visibility:hidden"><div class="bt" style="width:250px">Add a new container to the layout</div>
		<select name="containers" style="width:250px;">
			<option value="-1">Select a container to add</option>
		<xsl:for-each select="containers/optgroup">
			<optgroup><xsl:attribute name='label'><xsl:value-of select='label'/></xsl:attribute>
		<xsl:for-each select="option">
			<option><xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute><xsl:value-of select="."/></option></xsl:for-each>
			</optgroup>
		</xsl:for-each>
		</select><br/>
		<input type='hidden' name='save_container' value=''/>
		<input type='hidden' name='used_containers' value=''/>
		<input type="button" class="bt" value="Add" onclick="javascript:save_the_container();"/>
		<input type="button" class="bt" value="Cancel" onclick="javascript:cancel_form()"/>

		</td>
	</tr></table>
<script src="/libertas_images/editor/SiteLayoutManager/main.js"></script>
<script>
	var number_of_placeholders	= <xsl:value-of select="count(placeholders/row/placeholder)"/>;
	debug_var("placeholder_id_list");
	var placeholder_id_list		= new Array(<xsl:for-each select="//placeholder">
		new Array('<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>', 'placeholder<xsl:value-of select="@id"/>', 'column<xsl:value-of select="../@id"/>' <xsl:call-template name='listWidgets'></xsl:call-template>, '<xsl:value-of select="@rank"/>', '<xsl:value-of select="layout"/>', '<xsl:value-of select="layout/@numCols"/>', 0, '<xsl:choose>
					<xsl:when test="@width"><xsl:value-of select="@width"/></xsl:when>
					<xsl:otherwise><xsl:choose>
						<xsl:when test="@width"><xsl:value-of select="@width"/></xsl:when>
						<xsl:otherwise>100%</xsl:otherwise>
					</xsl:choose></xsl:otherwise>
				</xsl:choose>', '<xsl:value-of select="@type"/>')<xsl:if test="position()!=last()">,</xsl:if>
			</xsl:for-each>);
	var webTypes = new Array(<xsl:for-each select="//webTypes/webType">
						new Array("<xsl:value-of select="@module"/>","<xsl:value-of select="."/>")<xsl:if test="position()!=last()">,</xsl:if>
					</xsl:for-each>);
<!--
	var webobject_list			= new Array()
	<xsl:for-each select="//web_objects/web_object">
		webobject_list[webobject_list.length] =	new Array(<xsl:value-of select="@type"/>, "<xsl:value-of select="@id"/>", "<xsl:if test="@type='0'">UD - </xsl:if><xsl:value-of select="label"/>");
	</xsl:for-each>
-->		
	var webObjectGroupLayout	= ""
	setTimeout("setGroupLayout('<xsl:value-of select="@layout"/>');",3000);
	setTimeout("reIndexRank();",3000);
	setTimeout("display_columns();",3000);
</script>	
</xsl:template>
<xsl:template name="listWidgets">, new Array(<xsl:for-each select="web_object">new Array(<xsl:value-of select="@type"/>, '<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:with-param></xsl:call-template>', '<xsl:value-of select="@rank"/>', '<xsl:value-of select="from"/>', '<xsl:value-of select="to"/>', Array(), '<xsl:value-of select="lang"/>','<xsl:value-of select="@id"/>',0,'<xsl:value-of select="@unique_id"/>',<xsl:call-template name='listProperties'></xsl:call-template>)<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>)</xsl:template>
<xsl:template name="listProperties">new Array(<xsl:for-each select="properties/option">new Array('<xsl:value-of select="name"/>','<xsl:value-of select="value"/>')<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>)</xsl:template>
<xsl:template name="webTypes"></xsl:template>

</xsl:stylesheet>

