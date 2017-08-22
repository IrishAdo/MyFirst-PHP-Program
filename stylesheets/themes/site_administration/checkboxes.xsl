<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.5 $
- Modified $Date: 2004/09/06 16:49:46 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template match="checkboxes">
	<xsl:variable name="sort"><xsl:choose><xsl:when test="@sort=0">0</xsl:when><xsl:otherwise>1</xsl:otherwise></xsl:choose></xsl:variable>
	<xsl:choose>
		<xsl:when test="menu">
			<td valign="top" colspan="2">
			<script src="/libertas_images/javascripts/checkboxes.js"></script>
			<script>
			<xsl:comment>
				var hlist<xsl:value-of select="@name"/> = new menu_checkbox();
				<xsl:if test="@name='extractmenu_locations'">
				hlist<xsl:value-of select="@name"/>.code	= "ecml_";
				</xsl:if>
				hlist<xsl:value-of select="@name"/>.hlist	= new Array(
					<xsl:call-template name="checkboxArray"></xsl:call-template>
				);	
				setTimeout("hlist<xsl:value-of select="@name"/>.menu_location_update()",3000);
				objects_to_check[objects_to_check.length] = hlist<xsl:value-of select="@name"/>;
			</xsl:comment>
			</script>
			<table width="100%" border="0" cellpadding="3" cellspacing="0">
					<xsl:call-template name="checkboxmenu"></xsl:call-template>
			</table></td>
		</xsl:when>
		<xsl:otherwise>
			<xsl:if test=".//option">
			   	<td valign="top" colspan="2">
				<xsl:if test="@type='vertical' or not(@type)">
				<xsl:choose>
				<xsl:when test="options">
				<table width="100%" border="0" cellpadding="3" cellspacing="0">
				<xsl:for-each select="options">
					<xsl:sort select="@module" order="ascending"/>
					<tr>
						<td><xsl:if test ="@module"><b><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@module"/></xsl:call-template></b><br /></xsl:if>
						<xsl:choose>
							<xsl:when test="$sort=1"><xsl:for-each select="option">
								<xsl:sort select="@value"/>
							<input type="checkbox">
							   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
								<xsl:attribute name="id"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
								<xsl:attribute name="value"><xsl:choose>
									<xsl:when test="@value"><xsl:value-of  disable-output-escaping="yes" select="@value"/></xsl:when>
									<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
								</xsl:choose></xsl:attribute>
								<xsl:if test="@selected='true'"><xsl:attribute name="checked">true</xsl:attribute></xsl:if>
					   			<xsl:if test="@disabled='true'"><xsl:attribute name="disabled">true</xsl:attribute></xsl:if>
					   			<xsl:if test="../../@onclick"><xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>'<xsl:if test="../../parameters">, <xsl:value-of select="$cposition"/></xsl:if>);</xsl:attribute></xsl:if>
					   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:call-template name="get_translation">
								<xsl:with-param name="check"><xsl:call-template name="print">
								<xsl:with-param name="str_value"><xsl:copy-of select="."/></xsl:with-param>
							</xsl:call-template></xsl:with-param></xsl:call-template><br /></label></xsl:for-each></xsl:when>
							<xsl:otherwise><xsl:for-each select="option">
							<input type="checkbox">
							   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/>[]</xsl:attribute>
								<xsl:attribute name="id"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
								<xsl:attribute name="value"><xsl:choose>
									<xsl:when test="@value"><xsl:value-of  disable-output-escaping="yes" select="@value"/></xsl:when>
									<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
								</xsl:choose></xsl:attribute>
								<xsl:if test="@selected='true'">
									<xsl:attribute name="checked">true</xsl:attribute>
								</xsl:if>
					   			<xsl:if test="@disabled='true'">
									<xsl:attribute name="disabled">true</xsl:attribute>
								</xsl:if>
					   			<xsl:if test="../../@onclick">
									<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
								</xsl:if>
					   		</input><label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:call-template name="get_translation">
								<xsl:with-param name="check"><xsl:call-template name="print">
								<xsl:with-param name="str_value"><xsl:copy-of select="."/></xsl:with-param>
							</xsl:call-template></xsl:with-param>
							</xsl:call-template><br /></label></xsl:for-each></xsl:otherwise>
						</xsl:choose>
			   			</td>
			   		</tr>
			   	</xsl:for-each>
				</table>
				</xsl:when><xsl:otherwise>
					<xsl:for-each select="option">
					<input type="checkbox">
					   	<xsl:attribute name="name"><xsl:value-of select="../@name"/>[]</xsl:attribute>
						<xsl:attribute name="id"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:choose>
							<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:attribute>
						<xsl:if test="@selected='true'">
							<xsl:attribute name="checked">true</xsl:attribute>
						</xsl:if>
			   			<xsl:if test="@disabled='true'">
							<xsl:attribute name="disabled">true</xsl:attribute>
						</xsl:if>
			   			<xsl:if test="../../@onclick">
							<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
						</xsl:if>
			   		</input><label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:call-template name="get_translation">
								<xsl:with-param name="check"><xsl:call-template name="print">
								<xsl:with-param name="str_value"><xsl:copy-of select="."/></xsl:with-param>
							</xsl:call-template></xsl:with-param></xsl:call-template></label><br />
				</xsl:for-each>
				</xsl:otherwise></xsl:choose>
			   	</xsl:if>
			   	<xsl:if test="@type='horizontal' and options=true() ">
					<input type="hidden">
						<xsl:attribute name="name">totalnumberofchecks_<xsl:value-of select="@name"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="count(options)"/></xsl:attribute>
					</input>
					<table width="100%" border="0" cellpadding="15" cellspacing="0">
			   		   	<tr>
							<td valign="top"><xsl:call-template name="display_checkbox_table">
							<xsl:with-param name="column" select="1"/>
							</xsl:call-template></td>
							<td valign="top"><xsl:call-template name="display_checkbox_table">
							<xsl:with-param name="column" select="2"/>
							</xsl:call-template></td>
							<td valign="top"><xsl:call-template name="display_checkbox_table">
								<xsl:with-param name="column" select="0"/>
							</xsl:call-template></td>
						</tr>
					</table>
					
				</xsl:if>
			   	<xsl:if test="@type='horizontal' and options=false() ">
					<xsl:call-template name="display_checkbox_table">
						<xsl:with-param name="total" select="3"/>
					</xsl:call-template>
					
				</xsl:if>
			   	</td>
			</xsl:if>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="checkboxmenu">
	<xsl:param name="depth">0</xsl:param>
	<xsl:param name="fieldname"><xsl:value-of select="@name"/></xsl:param>
	<xsl:param name="jscriptobject">hlist<xsl:value-of select="@name"/>.</xsl:param>
	<xsl:param name="shownumber"><xsl:if test="@shownumber='YES'">YES</xsl:if></xsl:param>
	<xsl:variable name="idtag"><xsl:if test="$fieldname!='menu_locations'">e</xsl:if></xsl:variable>
	<xsl:for-each select="menu">
				<xsl:variable name="menu_id"><xsl:value-of select="@id"/></xsl:variable>
		<tr><xsl:attribute name="id">ml_<xsl:value-of select="@id"/></xsl:attribute>
			<td width="20px"><xsl:attribute name="id"><xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/>numbercell</xsl:attribute>
			<xsl:if test="$shownumber!='YES'">
				<xsl:attribute name="style">display:none</xsl:attribute>
			</xsl:if>
			<input type="text" onchange="javascript:check_format(this,'number')" size="3" maxlength="2" style="width:20px">
				<xsl:attribute name="id"><xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/>number</xsl:attribute>
				<xsl:attribute name="value"><xsl:choose><xsl:when test="//counters/counter[@menu=$menu_id]=0 or not(//counters/counter[@menu=$menu_id])">2</xsl:when><xsl:otherwise><xsl:value-of select="//counters/counter[@menu=$menu_id]"/></xsl:otherwise></xsl:choose></xsl:attribute>
				<xsl:attribute name="name"><xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/>number</xsl:attribute>
			</input></td>
			<td style="width:10px"><input type="checkbox" >
			<xsl:attribute name="name"><xsl:value-of select="$fieldname"/>[]</xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:attribute name="id"><xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/></xsl:attribute>
			<xsl:if test="@selected"><xsl:attribute name="checked">true</xsl:attribute></xsl:if>
			<xsl:attribute name="onclick"><xsl:value-of select="$jscriptobject"/>menu_location_toggle(<xsl:value-of select="@id"/>,'<xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/>')</xsl:attribute>
			</input></td>
			<td><xsl:attribute name="style">padding-left:<xsl:value-of select="$depth * 8"/>px;</xsl:attribute><label><xsl:attribute name="for">cml_<xsl:value-of select="@id"/></xsl:attribute><xsl:value-of select="label"/></label></td>
		</tr>
		<xsl:call-template name="checkboxmenu">
			<xsl:with-param name="depth"><xsl:value-of select="$depth + 1"/></xsl:with-param>
			<xsl:with-param name="fieldname"><xsl:value-of select="$fieldname"/></xsl:with-param>
			<xsl:with-param name="jscriptobject"><xsl:value-of select="$jscriptobject"/></xsl:with-param>
			<xsl:with-param name="shownumber"><xsl:value-of select="$shownumber"/></xsl:with-param>
		</xsl:call-template>
	</xsl:for-each>
<xsl:for-each select="children/menu">
		<xsl:variable name="menu_id"><xsl:value-of select="@id"/></xsl:variable>
		<tr><xsl:attribute name="id">ml_<xsl:value-of select="@id"/></xsl:attribute>
			<td width="20px"><xsl:attribute name="id"><xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/>numbercell</xsl:attribute>
			<xsl:if test="$shownumber!='YES'">
				<xsl:attribute name="style">display:none</xsl:attribute>
			</xsl:if>
			<input type="text" onchange="javascript:check_format(this,'number')" size="3" maxlength="2" style="width:20px">
				<xsl:attribute name="id"><xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/>number</xsl:attribute>
				<xsl:attribute name="value"><xsl:choose><xsl:when test="//counters/counter[@menu=$menu_id]=0 or not(//counters/counter[@menu=$menu_id])">2</xsl:when><xsl:otherwise><xsl:value-of select="//counters/counter[@menu=$menu_id]"/></xsl:otherwise></xsl:choose></xsl:attribute>
				<xsl:attribute name="name"><xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/>number</xsl:attribute>
			</input></td>
			<td style="width:10px"><input type="checkbox">
			<xsl:attribute name="name"><xsl:value-of select="$fieldname"/>[]</xsl:attribute>
			<xsl:attribute name="value"><xsl:value-of select="@id"/></xsl:attribute>
			<xsl:attribute name="id"><xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/></xsl:attribute>
			<xsl:if test="@selected"><xsl:attribute name="checked">true</xsl:attribute></xsl:if>
			<xsl:attribute name="onclick"><xsl:value-of select="$jscriptobject"/>menu_location_toggle(<xsl:value-of select="@id"/>,'<xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/>')</xsl:attribute>
		</input></td>
			<td><xsl:attribute name="style">padding-left:<xsl:value-of select="$depth * 8"/>px</xsl:attribute><label><xsl:attribute name="for"><xsl:value-of select="$idtag"/>cml_<xsl:value-of select="@id"/></xsl:attribute><xsl:value-of select="label"/></label></td>
		</tr>
		<xsl:call-template name="checkboxmenu">
			<xsl:with-param name="depth"><xsl:value-of select="$depth + 1"/></xsl:with-param>
			<xsl:with-param name="fieldname"><xsl:value-of select="$fieldname"/></xsl:with-param>
			<xsl:with-param name="jscriptobject"><xsl:value-of select="$jscriptobject"/></xsl:with-param>
			<xsl:with-param name="shownumber"><xsl:value-of select="$shownumber"/></xsl:with-param>
		</xsl:call-template>
	</xsl:for-each>
</xsl:template>

<xsl:template name="checkboxArray">
	<xsl:for-each select=".//menu">
		new Array(<xsl:value-of select="@id"/>,<xsl:value-of select="@parent"/>,<xsl:choose>
			<xsl:when test="@selected">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose>)<xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>

</xsl:stylesheet>

