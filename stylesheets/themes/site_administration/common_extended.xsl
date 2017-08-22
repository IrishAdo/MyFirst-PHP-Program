<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.15 $
- Modified $Date: 2005/01/11 16:27:06 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template match="javascript"></xsl:template>


<xsl:template match="radio">
	<td valign="top">
	<xsl:variable name="pos"><xsl:value-of select="@name"/></xsl:variable>
	<table border="0" cellspacing="0">
	<xsl:choose>
	<xsl:when test="options/option">
	   	<xsl:for-each select="options">
	   	<tr class="TableCell">
	   	<td valign="top ">	
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr><td valign="top" class="formbackground"><table border="0" cellspacing="1" cellpadding="0" width="100%">
				<tr><td class="formheader"><b><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@module"/></xsl:call-template></b></td></tr>
	   		   	<tr><td class="TableCell"><table width="100%" border="0" cellpadding="3" cellspacing="0">
			<xsl:for-each select="option">
					<tr>
						<td class="TableCell" width="10"><input type="radio">
					   	<xsl:attribute name="name"><xsl:value-of select="../../@name"/></xsl:attribute>
						<xsl:attribute name="id"><xsl:value-of select="../../@name"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:choose>
						<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
						<xsl:if test="@selected='true'">
							<xsl:attribute name="checked">true</xsl:attribute>
						</xsl:if>
						<xsl:if test="../../@onclick">
							<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
						</xsl:if>
				   		</input></td><td valign="top" class="TableCell">
						<label><xsl:attribute name="for"><xsl:value-of select="../../@name"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></label></td>
					</tr>
				</xsl:for-each>
				</table></td>
				</tr></table></td>
			</tr>
		</table>
				</td></tr>
</xsl:for-each>
	</xsl:when>
	<xsl:when test="@type='horizontal'">
	   	<tr class="TableCell">
		<xsl:for-each select="option">
	   	
	   	<td valign="top"><input type="radio">
		   	<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
			<xsl:attribute name="id"><xsl:value-of select="../@name"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:choose>
						<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
			<xsl:if test="@selected='true'">
				<xsl:attribute name="checked">true</xsl:attribute>
			</xsl:if>
			<xsl:if test="../@onclick">
				<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
			</xsl:if>
	   		</input>
			<label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
				<xsl:when test=".!=''"><xsl:value-of select="."/></xsl:when>
				<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template></label>

   		</td>
		</xsl:for-each>
		
   	</tr>
	</xsl:when>
	<xsl:otherwise>
	   	<xsl:for-each select="option">
	   	<tr class="TableCell">
	   	<td valign="top"><input type="radio">
		   	<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
			<xsl:attribute name="id"><xsl:value-of select="../@name"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/></xsl:attribute>
			<xsl:attribute name="value"><xsl:choose>
						<xsl:when test="@value"><xsl:value-of disable-output-escaping="yes" select="@value"/></xsl:when>
						<xsl:otherwise><xsl:value-of select="." disable-output-escaping="yes"/></xsl:otherwise>
					</xsl:choose></xsl:attribute>
			<xsl:if test="@selected='true'">
				<xsl:attribute name="checked">true</xsl:attribute>
			</xsl:if>
			<xsl:if test="../@onclick">
				<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
			</xsl:if>
	   		</input></td><td valign="top" width="100%">
			<label><xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
				<xsl:when test=".!=''"><xsl:value-of select="."/></xsl:when>
				<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template></label>

   		</td>
   	</tr></xsl:for-each>
	</xsl:otherwise>
	</xsl:choose>
	</table>
	<xsl:if test="@span">
		<span><xsl:attribute name="id"><xsl:value-of select="@span"/></xsl:attribute><xsl:attribute name="name"><xsl:value-of select="@span"/></xsl:attribute></span>
	</xsl:if>

	</td>

</xsl:template>

<xsl:template match="key">
</xsl:template>

<xsl:template match="files">
	<input type="hidden"><xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute><xsl:attribute name="value">
	<xsl:for-each select="file"><xsl:value-of select="@identifier"/>,</xsl:for-each></xsl:attribute></input>
		   	<td><span>
			<xsl:attribute name="name">editable_span</xsl:attribute>
			<xsl:attribute name="id">editable_span</xsl:attribute>
			<ul>
			<xsl:choose><xsl:when test="file">
			<xsl:for-each select="file">
				<li><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></li>
			</xsl:for-each></xsl:when><xsl:otherwise>
				<li><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_NO_FILE_ASSOCIATIONS'"/></xsl:call-template></li>
			</xsl:otherwise></xsl:choose></ul></span></td>
</xsl:template>



<xsl:template match="page_sections"></xsl:template>
<xsl:template match="li"></xsl:template>
<xsl:template match="ul"></xsl:template>

<xsl:template match="section">
<tr><td><span><xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
	<xsl:choose>
	<xsl:when test="child::*">
		<table border="0">
		<xsl:for-each select="child::*">
			<tr><td><a><xsl:attribute name="href"><xsl:value-of select="@url" disable-output-escaping="yes"/></xsl:attribute><xsl:value-of select="." disable-output-escaping="yes"/></a></td></tr>
		</xsl:for-each>
		</table></xsl:when>
	<xsl:otherwise><xsl:call-template name="get_translation">
			<xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param>
		</xsl:call-template></xsl:otherwise>
	</xsl:choose></span>
		<xsl:if test="@command">
		<ul><li><a><xsl:attribute name="href"><xsl:choose>
			<xsl:when test="@command='PAGE_LIST'">javascript:page_associate();</xsl:when>
			<xsl:when test="@command='LAYOUT_MENU_SELECT'">javascript:layout_associate();</xsl:when>
			<xsl:when test="@command='GROUP_SELECT'">javascript:group_associate();</xsl:when>
			<xsl:when test="@command='FILES_SELECT'">javascript:file_associate();</xsl:when>
			<xsl:when test="@command='CONTACT_LIST_SELECTION'">javascript:contact_associate('<xsl:value-of select="@name"/>');</xsl:when>
			<xsl:when test="@command='LANGUAGES_SELECT'">javascript:alert('Not currently available');</xsl:when>
			<xsl:otherwise>admin/index.php?command=<xsl:value-of select="@command"/></xsl:otherwise>
		</xsl:choose></xsl:attribute><xsl:call-template name="get_translation">
			<xsl:with-param name="check"><xsl:value-of select="'LOCALE_ADD_NEW_ENTRY'"/></xsl:with-param>
		</xsl:call-template></a></li></ul>
		</xsl:if>
</td></tr>
</xsl:template>

<xsl:template name="display_checkbox_table">
	<xsl:param name="column" select="0"/>
	<xsl:param name="total" select="3"/>
	<xsl:choose>
	<xsl:when test="options">
	<xsl:for-each select="options">
		<xsl:sort select="@module" order="ascending"/>
		<xsl:variable name="pos"><xsl:value-of select="translate(@module,' ','_')"/></xsl:variable>
		<xsl:if test="(position() mod $total) = $column">
		<xsl:variable name="FieldName"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:variable>
		<xsl:variable name="FieldPos"><xsl:value-of select="position()"/></xsl:variable>
		<table border="0" cellspacing="1" cellpadding="0" width="100%">
			<tr><td class="formheader"><b><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
				<xsl:when test="@module"><xsl:value-of select="@module"/></xsl:when>
				<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
			</xsl:choose></xsl:with-param></xsl:call-template></b></td></tr>
			<tr><td class="TableCell">
				<input type="hidden">
					<xsl:attribute name="name"><xsl:value-of select="../@name"/>_<xsl:value-of select="$pos"/></xsl:attribute>
					<xsl:attribute name="value"><xsl:value-of select="count(option)"/></xsl:attribute>
				</input>
				<xsl:variable name="sort"><xsl:value-of select="@sort"/></xsl:variable>
				<table width="100%" border="0" cellpadding="3" cellspacing="0">
				<xsl:choose>
					<xsl:when test="$sort='yes'">
					<xsl:for-each select="option">
							<xsl:sort select="." order="ascending"/>
						<tr>
						<td width="20%"><input type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="$FieldName"/>[]</xsl:attribute>
							<xsl:attribute name="id"><xsl:value-of select="$FieldName"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
							<xsl:if test="@selected='true'">
								<xsl:attribute name="checked">true</xsl:attribute>
							</xsl:if>
				   			<xsl:if test="@disabled='true'">
								<xsl:attribute name="disabled">true</xsl:attribute>
							</xsl:if>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>','<xsl:value-of select="$FieldPos"/>');</xsl:attribute>
							</xsl:if>
				   		</input>
						<label>
							<xsl:attribute name="for"><xsl:value-of select="$FieldName"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/></xsl:attribute>									
							<xsl:attribute name="id"><xsl:value-of select="$FieldName"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/>_label</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></label>
						</td>
						</tr>
					</xsl:for-each>
					</xsl:when>
					<xsl:otherwise>
						<xsl:for-each select="option">
						<tr>
						<td width="20%"><input type="checkbox">
						   	<xsl:attribute name="name"><xsl:value-of select="$FieldName"/>[]</xsl:attribute>
							<xsl:attribute name="id"><xsl:value-of select="$FieldName"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/></xsl:attribute>
							<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
							<xsl:if test="@selected='true'">
								<xsl:attribute name="checked">true</xsl:attribute>
							</xsl:if>
				   			<xsl:if test="@disabled='true'">
								<xsl:attribute name="disabled">true</xsl:attribute>
							</xsl:if>
				   			<xsl:if test="../../@onclick">
								<xsl:attribute name="onclick">javascript:<xsl:value-of select="../../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>','<xsl:value-of select="$FieldPos"/>');</xsl:attribute>
							</xsl:if>
				   		</input>
						<label><xsl:attribute name="for"><xsl:value-of select="$FieldName"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/></xsl:attribute>									
							<xsl:attribute name="id"><xsl:value-of select="$FieldName"/>_<xsl:value-of select="$pos"/>_<xsl:value-of select="position()"/>_label</xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></label>
						</td>
						</tr>
					</xsl:for-each>
					</xsl:otherwise>
					</xsl:choose>
				</table></td>
			</tr></table></xsl:if>
	</xsl:for-each>
	</xsl:when>
	<xsl:otherwise>
		<table width="100%" border="0" cellpadding="3" cellspacing="0">
			<xsl:for-each select="option[(position() mod 3) =1]">
				<tr>
					<td width="20%"><input type="checkbox">
					   	<xsl:attribute name="name"><xsl:value-of select="../@name"/>[]</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
						<xsl:if test="@selected='true'">
							<xsl:attribute name="checked">true</xsl:attribute>
						</xsl:if>
			   			<xsl:if test="@disabled='true'">
							<xsl:attribute name="disabled">true</xsl:attribute>
						</xsl:if>
			   			<xsl:if test="../@onclick">
							<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
						</xsl:if>
			   		</input><xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:value-of select="."/></xsl:with-param></xsl:call-template>
					</td>
					<xsl:if test="following-sibling::option[(position() mod 3) = 1]">
					<xsl:variable name="locale"><xsl:value-of select="following-sibling::option[(position() mod 3) = 1]"/></xsl:variable>
					<td width="20%"><input type="checkbox">
					   	<xsl:attribute name="name"><xsl:value-of select="../@name"/>[]</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="following-sibling::option[. = $locale]/@value"/></xsl:attribute>
						<xsl:if test="following-sibling::option[. = $locale]/@selected='true'">
							<xsl:attribute name="checked">true</xsl:attribute>
						</xsl:if>
			   			<xsl:if test="following-sibling::option[. = $locale]/@disabled='true'">
							<xsl:attribute name="disabled">true</xsl:attribute>
						</xsl:if>
			   			<xsl:if test="../@onclick">
							<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
						</xsl:if>
			   		</input><xsl:variable name="locale1"><xsl:value-of select="following-sibling::option[(position() mod 3) = 1]"/></xsl:variable> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="$locale1"/></xsl:call-template>
					</td>
					</xsl:if>
					<xsl:if test="following-sibling::option[(position() mod 3) = 2]">
					<xsl:variable name="locale2"><xsl:value-of select="following-sibling::option[(position() mod 3) = 2]"/></xsl:variable>
					<td width="20%"><input type="checkbox">
					   	<xsl:attribute name="name"><xsl:value-of select="../@name"/>[]</xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="following-sibling::option[. = $locale2]/@value"/></xsl:attribute>
						<xsl:if test="following-sibling::option[. = $locale2]/@selected='true'">
							<xsl:attribute name="checked">true</xsl:attribute>
						</xsl:if>
			   			<xsl:if test="following-sibling::option[. = $locale2]/@disabled='true'">
							<xsl:attribute name="disabled">true</xsl:attribute>
						</xsl:if>
			   			<xsl:if test="../@onclick">
							<xsl:attribute name="onclick">javascript:<xsl:value-of select="../@onclick"/>_group(this,'<xsl:value-of select="../@tag"/>');</xsl:attribute>
						</xsl:if>
			   		</input><xsl:variable name="locale3"><xsl:value-of select="following-sibling::option[(position() mod 3) = 2]"/></xsl:variable> <xsl:call-template name="get_translation"><xsl:with-param name="check" select="$locale3"/></xsl:call-template>
					
					</td>
					</xsl:if>
				</tr>
			</xsl:for-each>
		</table>
	</xsl:otherwise>
	</xsl:choose>
</xsl:template>
<xsl:template name="display_form_field">
Required <input type="checkbox" name="required[]"><xsl:if test="@requires!='0'"><xsl:attribute name="checked">true</xsl:attribute></xsl:if><xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute></input>
Available <input type="checkbox" name="available[]"><xsl:if test="@available!='0'"><xsl:attribute name="checked">true</xsl:attribute></xsl:if><xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute></input>
</xsl:template>

<xsl:template match="extract">
	<xsl:if test="@type='select'">
	   	<td><select>
			<xsl:if test="@onchange!=''">
				<xsl:attribute name="onchange">javascript:<xsl:value-of select="@name"/>_change('<xsl:value-of select="@name"/>');</xsl:attribute>
			</xsl:if>
	   		<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
	   		<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
		   	<xsl:for-each select="option">
			   	<option><xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute><xsl:if test="@selected"><xsl:attribute name="selected"><xsl:value-of select="@selected"/></xsl:attribute></xsl:if><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></option>
		   	</xsl:for-each>
	   	</select><br/>
		<iframe width="0px" height="0px" style="visibility:hidden">
			<xsl:if test="@url!=''">
				<xsl:attribute name="src"><xsl:value-of select="@url"/>&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/>&amp;dest=<xsl:value-of select="@name"/></xsl:attribute>
			</xsl:if>
		</iframe>
		<span><xsl:attribute name="id">html_output_<xsl:value-of select="@name"/></xsl:attribute></span>
		</td>  		
	</xsl:if>
</xsl:template>

<xsl:template match="category_location">
<xsl:choose>
<xsl:when test ="count(category)!=0">
<xsl:if test="label">
	<tr>
		<td><label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:value-of select="label"/></label></td>
	</tr>
</xsl:if>
	<tr>
		<td>
		<select>
			<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:call-template name="optionize_categories">
				<xsl:with-param name="depth">1</xsl:with-param>
				<xsl:with-param name="find"><xsl:value-of select="@parent"/></xsl:with-param>
				<xsl:with-param name="id"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template>
		</select></td>
	</tr>
</xsl:when>	
<xsl:otherwise>
<input type="hidden" name="cat_parent"><xsl:attribute name="value"><xsl:value-of select="@parent"/></xsl:attribute></input>
</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template name="optionize_categories">
	<xsl:param name="depth">1</xsl:param>
	<xsl:param name="find">0</xsl:param>
	<xsl:param name="id">0</xsl:param>
	<xsl:if test="$depth=1">
		<xsl:choose>
			<xsl:when test="$find=-2">
				<option value="">Select location to merge into</option></xsl:when>
			<xsl:otherwise>
				<option><xsl:attribute name="value"><xsl:value-of select="list/@identifier"/></xsl:attribute><xsl:if test="$find=list/@identifier"><xsl:attribute name="selected">true</xsl:attribute></xsl:if><xsl:value-of select="list"/></option>
			</xsl:otherwise>
		</xsl:choose>
		
		
	</xsl:if>
	<xsl:for-each select="category[@identifier!=$id]">
		
		<option><xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute><xsl:if test="$find=@identifier"><xsl:attribute name="selected">true</xsl:attribute></xsl:if><xsl:call-template name="show_depth">
			<xsl:with-param name="depth"><xsl:value-of select="$depth - 1"/></xsl:with-param>
		</xsl:call-template> - <xsl:value-of select="label"/></option>
			<xsl:call-template name="optionize_categories">
				<xsl:with-param name="depth"><xsl:value-of select="$depth + 1"/></xsl:with-param>
				<xsl:with-param name="find"><xsl:value-of select="$find"/></xsl:with-param>
				<xsl:with-param name="id"><xsl:value-of select="$id"/></xsl:with-param>
			</xsl:call-template>
	</xsl:for-each>
	<xsl:for-each select="children/category">
		<xsl:if test="$id!=@identifier">
		<option><xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute><xsl:if test="$find=@identifier"><xsl:attribute name="selected">true</xsl:attribute></xsl:if><xsl:call-template name="show_depth">
			<xsl:with-param name="depth"><xsl:value-of select="$depth - 1"/></xsl:with-param>
		</xsl:call-template> - <xsl:value-of select="label"/></option>
		<xsl:if test="$find!=@identifier or $id=@identifier">
			<xsl:call-template name="optionize_categories">
				<xsl:with-param name="depth"><xsl:value-of select="$depth + 1"/></xsl:with-param>
				<xsl:with-param name="find"><xsl:value-of select="$find"/></xsl:with-param>
				<xsl:with-param name="id"><xsl:value-of select="$id"/></xsl:with-param>
			</xsl:call-template>
		</xsl:if>
		</xsl:if>		
	</xsl:for-each>
</xsl:template>



<xsl:template name="show_depth">
	<xsl:param name="depth">0</xsl:param>
	<xsl:if test="$depth>0">[[nbsp]][[nbsp]]<xsl:call-template name="show_depth"><xsl:with-param name="depth"><xsl:value-of select="$depth - 1"/></xsl:with-param></xsl:call-template></xsl:if>
</xsl:template>



<xsl:template match="categories">
<xsl:if test="label">
	<tr>
		<td><xsl:value-of select="label"/></td>
	</tr>
</xsl:if>
<tr><td>
<div id='category_output'></div>
<script src="/libertas_images/javascripts/module_category.js"></script>
<script>
<xsl:comment>
	var mycatlist = new CategoryList('<xsl:value-of select="list/@rank"/>','category_output','<xsl:value-of select="list/@identifier"/>','mycatlist','<xsl:value-of select="count(category)"/>');
	<xsl:for-each select="category">
	mycatlist.add('<xsl:value-of select="@parent"/>','<xsl:value-of select="@identifier"/>','<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>','<xsl:value-of select="count(children/category)"/>');<xsl:if test="children/category"><xsl:call-template name="display_child_categories">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template></xsl:if></xsl:for-each>
	mycatlist.display();
</xsl:comment>
</script>
</td></tr>
<!--<tr>
	<td>
	<table summary="" border="0" cellspacing="0" cellpadding="0" width="100%">
	<xsl:for-each select="category">
		<tr><xsl:attribute name="class">lineit</xsl:attribute> 
			<td width="20px" valign="top" class="tablecell"><xsl:if test="children/category"><xsl:attribute name="rowspan">2</xsl:attribute></xsl:if><img><xsl:attribute name="src">/libertas_images/general/iconification/<xsl:choose><xsl:when test="children/category">folderopen</xsl:when><xsl:otherwise>item</xsl:otherwise></xsl:choose>.gif</xsl:attribute></img></td>
			<td ><xsl:attribute name="class">lineit</xsl:attribute><xsl:value-of select="label"/></td>
			<td align="right"><xsl:attribute name="class">lineit</xsl:attribute>
			[<a><xsl:attribute name="href">javascript:category_fn('CATEGORYADMIN_ADD','<xsl:value-of select="@identifier"/>');</xsl:attribute>Add Sub-Category</a>]
			[<a><xsl:attribute name="href">javascript:category_fn('CATEGORYADMIN_EDIT','<xsl:value-of select="@identifier"/>');</xsl:attribute>Edit Category</a>] 
			[<xsl:choose>
			<xsl:when test="children/category"><span class="ghost">Remove Category</span></xsl:when>
			<xsl:otherwise><a><xsl:attribute name="href">javascript:category_fn('CATEGORYADMIN_REMOVE','<xsl:value-of select="@identifier"/>');</xsl:attribute>Remove Category</a></xsl:otherwise>
			</xsl:choose>]
			</td>
		</tr>
		<xsl:if test="children/category">
		<tr>
			<td colspan="2"><xsl:call-template name="display_child_categories">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template></td>
		</tr>
		</xsl:if>
	</xsl:for-each>	
	</table></td>
</tr>-->
</xsl:template>

<xsl:template name="display_child_categories">
	<xsl:param name="identifier">-2</xsl:param>
	<xsl:for-each select="children/category[@parent = $identifier]">
	mycatlist.add('<xsl:value-of select="@parent"/>','<xsl:value-of select="@identifier"/>','<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>','<xsl:value-of select="count(children/category)"/>');<xsl:if test="children/category"><xsl:call-template name="display_child_categories">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
	</xsl:call-template></xsl:if></xsl:for-each>
</xsl:template>
<xsl:template name="display_child_categories1">
	<xsl:param name="identifier">-2</xsl:param>
<!--
	<table summary="" border="0" cellspacing="0" cellpadding="0" width="100%">
	<xsl:for-each select="children/category[@parent = $identifier]">
		<tr>
			<td width="20px" valign="top" class="tablecell"><xsl:if test="children/category"><xsl:attribute name="rowspan">2</xsl:attribute></xsl:if><img><xsl:attribute name="src">/libertas_images/general/iconification/<xsl:choose><xsl:when test="children/category">folderopen</xsl:when><xsl:otherwise>item</xsl:otherwise></xsl:choose>.gif</xsl:attribute></img></td>
			<td ><xsl:attribute name="class">lineit</xsl:attribute><xsl:value-of select="label"/></td>
			<td align="right"><xsl:attribute name="class">lineit</xsl:attribute>
			[<a><xsl:attribute name="href">javascript:category_fn('CATEGORYADMIN_ADD','<xsl:value-of select="@identifier"/>');</xsl:attribute>Add Sub-Category</a>]
			[<a><xsl:attribute name="href">javascript:category_fn('CATEGORYADMIN_EDIT','<xsl:value-of select="@identifier"/>');</xsl:attribute>Edit Category</a>] 
			[<xsl:choose>
			<xsl:when test="children/category"><span class="ghost">Remove Category</span></xsl:when>
			<xsl:otherwise><a><xsl:attribute name="href">javascript:category_fn('CATEGORYADMIN_REMOVE','<xsl:value-of select="@identifier"/>');</xsl:attribute>Remove Category</a></xsl:otherwise>
			</xsl:choose>]
			</td>
		</tr>
		<xsl:if test="children/category">
		<tr>
			<td colspan="3"><xsl:call-template name="display_child_categories">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template></td>
		</tr>
		</xsl:if>
	</xsl:for-each>	
	</table>
	-->
	<!--
	<xsl:for-each select="//category[@parent = $identifier]">
		<li><xsl:attribute name="class"><xsl:choose><xsl:when test="children/category">folder</xsl:when><xsl:otherwise>category<xsl:choose>
			<xsl:when test="position() mod 2 = 0">0</xsl:when> 
			<xsl:otherwise>1</xsl:otherwise>
		</xsl:choose></xsl:otherwise></xsl:choose></xsl:attribute><xsl:value-of select="label"/>
		 [[nbsp]] <a><xsl:attribute name="href">admin/index.php?command=CATEGORYADMIN_EDIT&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute>Edit Category</a>
		 [[nbsp]] <a><xsl:attribute name="href">admin/index.php?command=CATEGORYADMIN_ADD&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute>Add Sub-Category</a>
		</li> 
		<xsl:if test="children/category"><ul class="category"><xsl:call-template name="display_child_categories">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template></ul></xsl:if>
	</xsl:for-each> -->
</xsl:template>

<xsl:template match="fieldlist">
	<!--h1>Field List</h1-->
	<span id="fieldlistdiv" name="fieldlistdiv">
	</span>
	<script src="/libertas_images/javascripts/fieldlistdiv.js"></script>
	<script>
		var info_fieldlist = new Array(
		<xsl:for-each select="field">new Array('<xsl:value-of select="@name"/>', '<xsl:value-of select="position()"/>', '<xsl:value-of select="@selected"/>', '<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>', '<xsl:value-of select="description"/>', '<xsl:value-of select="@type"/>', new Array(<xsl:for-each select="values/value"><xsl:choose>
								<xsl:when test="boolean(@screen)">new Array(<xsl:value-of select="@screen"/>, "<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of disable-output-escaping="yes" select="."/></xsl:with-param>
						</xsl:call-template>")</xsl:when>
<xsl:otherwise>"<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of disable-output-escaping="yes" select="."/></xsl:with-param>
						</xsl:call-template>"</xsl:otherwise></xsl:choose><xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>), '<xsl:value-of select="@category"/>', '<xsl:choose>
								<xsl:when test="@search_form=1">1</xsl:when><xsl:otherwise>0</xsl:otherwise>
						</xsl:choose>','<xsl:value-of select="@duplicate"/>', '<xsl:value-of select="@filter"/>','<xsl:value-of select="@sumlabel"/>', '<xsl:value-of select="@conlabel"/>', '<xsl:value-of select="@map"/>', "<xsl:value-of disable-output-escaping="yes" select="@special"/>", "<xsl:value-of disable-output-escaping="yes" select="@add_to_title"/>","<xsl:value-of disable-output-escaping="yes" select="@url_linkfield"/>")<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>
		);
		draw_fieldlistdiv(info_fieldlist);
	</script>
</xsl:template>
<xsl:template match="screen">
	<script>

		var <xsl:value-of select="../@name"/> = new Array();<xsl:for-each select="field">
			pointerIndex = add_to_array('<xsl:value-of select="../../@name"/>',"label",'<xsl:value-of select="label"/>',-1);
			pointerIndex = add_to_array('<xsl:value-of select="../../@name"/>',"name",'<xsl:value-of select="@name"/>',pointerIndex);
			pointerIndex = add_to_array('<xsl:value-of select="../../@name"/>',"type",'<xsl:value-of select="@type"/>',pointerIndex);
			pointerIndex = add_to_array('<xsl:value-of select="../../@name"/>',"Link",'<xsl:choose><xsl:when test="@link='1'">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>',pointerIndex);</xsl:for-each>
		setTimeout("show_output('<xsl:value-of select="../@name"/>');",1000);
	</script>
	
</xsl:template>

<xsl:template match="display_categories">
	<xsl:for-each select="../specified_categorys">
		<xsl:variable name="find"><xsl:value-of select="@identifier"/></xsl:variable>
			<xsl:for-each select="../display_categories/category">
			<xsl:choose>
				<xsl:when test="$find = @identifier"><xsl:value-of select="label"/></xsl:when>
				<xsl:when test=".//children/category[@identifier=$find]"> <xsl:value-of select="label"/> [[rightarrow]]
					<xsl:call-template name="display_categories_path">
						<xsl:with-param name='id'><xsl:value-of select="$find"/></xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
		</xsl:for-each>
	</xsl:for-each>
<!--
-->
</xsl:template>
<xsl:template name="display_categories_path">
	<xsl:param name='id'><xsl:value-of select="$id"/></xsl:param>
	<xsl:for-each select="children/category">
			<xsl:choose>
				<xsl:when test="$id = @identifier"><xsl:value-of select="label"/></xsl:when>
				<xsl:when test=".//children/category[@identifier=$id]"> <xsl:value-of select="label"/> [[rightarrow]]
					<xsl:call-template name="display_categories_path">
						<xsl:with-param name='id'><xsl:value-of select="$id"/></xsl:with-param>
					</xsl:call-template>
				</xsl:when>
				<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
	</xsl:for-each>
<!--
-->
</xsl:template>

<xsl:template match="choose_categories">
<xsl:choose>
	<xsl:when test="local-name(../..)='filter'">
	<td><select name="filter_category">
	<option value='ALL'>All</option>
	<xsl:for-each select="category">
	<option>
		<xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute>
		<xsl:if test="@identifier = //specified_categorys/@identifier"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>
		<xsl:value-of select="label"/>
	</option>
		<xsl:if test="children/category">
			<xsl:call-template name="display_child_choose_categories_option">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
				<xsl:with-param name="padding">[[nbsp]]-[[nbsp]]</xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>	
	</select></td>
	</xsl:when>
	<xsl:otherwise>
<xsl:if test="label">
	<tr>
		<td><xsl:value-of select="label"/></td>
	</tr>
</xsl:if>
<!--
<tr>
	<td><a><xsl:attribute name="href">javascript:category_fn('CATEGORYADMIN_ADD','-1');</xsl:attribute>Add New Sub-Category</a></td>
</tr>
-->
	<tr>	
		<td width="50%" valign="top"><div id="CategoryList"></div></td>
		<td width="50%" valign="top"><xsl:if test="//choose_categories/@can_add=1">
			<div id='addLink'><input type='button' class='bt' onclick="javascript:newCategory.displayForm();">
				<xsl:attribute name="value"><xsl:value-of select="add"/></xsl:attribute>
			</input></div>
			<table id="newCategoryForm"></table>
		</xsl:if></td>
	</tr>
	<input type='hidden' name='newCategories' value=''/>
	<script src='/libertas_images/javascripts/newCategory.js'></script>
	<script>
	<xsl:comment>
		var newCategory = new newCategories();
		newCategory.output	= '<xsl:value-of select="@output"/>';
		newCategory.id		= '<xsl:value-of select="@identifier"/>';
		<xsl:variable name="pid"><xsl:value-of select="@identifier"/></xsl:variable>
		newCategory.list	= new Array(
		<xsl:for-each select="//category[@parent=$pid]">
		<xsl:variable name="nextid"><xsl:value-of select="@identifier"/></xsl:variable>
			new Array('<xsl:value-of select="@parent"/>', '<xsl:value-of select="@identifier"/>', "<xsl:call-template name="print2">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>", <xsl:choose>
				<xsl:when test="@identifier = //specified_categorys/@identifier">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>)<xsl:if test="boolean(//category[@parent=$pid and @identifier=$nextid]/children/category)">, <xsl:call-template name="showcategoriesJS"><xsl:with-param name="pid"><xsl:value-of select="@identifier"/></xsl:with-param></xsl:call-template></xsl:if><xsl:if test="position()!=last()">,</xsl:if>
		</xsl:for-each>
		);
		newCategory.display(-1);
	</xsl:comment>
	</script>
<!--
<tr>
	<td>
	<table summary="" border="0" cellspacing="0" cellpadding="0">
	<xsl:for-each select="category">
		<tr>
			<td width="20px" valign="top"><xsl:if test="children/category"><xsl:attribute name="rowspan">2</xsl:attribute></xsl:if>
			<input type="checkbox" name="cat_id_list[]"><xsl:attribute name="id">cat_<xsl:value-of select="@identifier"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute><xsl:if test="@identifier = //specified_categorys/@identifier"><xsl:attribute name="checked">true</xsl:attribute></xsl:if></input>
			</td>
			<td width="100%"><xsl:if test="children/category"><xsl:attribute name="style">background-color:#ebebeb;</xsl:attribute></xsl:if><label><xsl:attribute name="for">cat_<xsl:value-of select="@identifier"/></xsl:attribute><xsl:value-of select="label"/></label></td>
		</tr>
		<xsl:if test="children/category">
		<tr>
			<td width="100%"><xsl:call-template name="display_child_choose_categories">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template></td>
		</tr>
		</xsl:if>
	</xsl:for-each>	
	</table></td>
</tr>
-->
	</xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template name="showcategoriesJS">
	<xsl:param name="pid"></xsl:param>
	
	<xsl:for-each select="//category[@parent=$pid]">
		<xsl:variable name="nextid"><xsl:value-of select="@identifier"/></xsl:variable>
		new Array('<xsl:value-of select="@parent"/>', '<xsl:value-of select="@identifier"/>', "<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>", <xsl:choose>
				<xsl:when test="@identifier = //specified_categorys/@identifier">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>)
			<xsl:if test="boolean(//category[@parent=$pid and @identifier=$nextid]/children/category[@parent=$nextid])">, <xsl:call-template name="showcategoriesJS">
					<xsl:with-param name="pid"><xsl:value-of select="$nextid"/></xsl:with-param>
					<xsl:with-param name="nid"><xsl:value-of select="@identifier"/></xsl:with-param>
				</xsl:call-template></xsl:if><xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="display_child_choose_categories">
	<xsl:param name="identifier">-2</xsl:param>
	<table summary="" border="0" cellspacing="0" cellpadding="0">
	<xsl:for-each select="children/category[@parent = $identifier]">
		<tr>
			<td width="20px" valign="top"><xsl:if test="children/category"><xsl:attribute name="rowspan">2</xsl:attribute></xsl:if>
			<input type="checkbox" name="cat_id_list[]"><xsl:attribute name="id">cat_<xsl:value-of select="@parent"/>_<xsl:value-of select="@identifier"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute><xsl:if test="@identifier = //specified_categorys/@identifier"><xsl:attribute name="checked">true</xsl:attribute></xsl:if></input>
			</td>
			<td width="100%"><xsl:attribute name="class">lineit</xsl:attribute><label><xsl:attribute name="for">cat_<xsl:value-of select="@parent"/>_<xsl:value-of select="@identifier"/></xsl:attribute><xsl:value-of select="label"/></label></td>
		</tr>
		<xsl:if test="children/category">
		<tr>
			<td width="100%"><xsl:call-template name="display_child_choose_categories">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template></td>
		</tr>
		</xsl:if>
	</xsl:for-each>	
	</table>
</xsl:template>
<xsl:template name="display_child_choose_categories_option">
	<xsl:param name="identifier">-2</xsl:param>
	<xsl:param name="padding">[[nbsp]]-[[nbsp]]</xsl:param>
	<xsl:for-each select="children/category[@parent = $identifier]">
			<option >
				<xsl:attribute name="id">cat_<xsl:value-of select="@parent"/>_<xsl:value-of select="@identifier"/></xsl:attribute>
				<xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute>
				<xsl:value-of select="$padding"/><xsl:value-of select="label"/>
			</option>
		<xsl:if test="children/category">
		<xsl:call-template name="display_child_choose_categories_option">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
				<xsl:with-param name="padding">[[nbsp]][[nbsp]]<xsl:value-of select="$padding"/></xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>	
</xsl:template>


<xsl:template match="multilist">
	<script>
		var info_list_of_options = new Array(
		<xsl:for-each select="multi">
			new Array('<xsl:value-of select="@name"/>', LIBERTAS_GENERAL_jtidy("<xsl:value-of select="label" disable-output-escaping="yes"/>"), "<xsl:value-of select="@accesstype" disable-output-escaping="yes"/>" , "<xsl:value-of select="@type" disable-output-escaping="yes"/>"  , "<xsl:value-of select="@group" disable-output-escaping="yes"/>", LIBERTAS_GENERAL_jtidy("<xsl:value-of select="description" disable-output-escaping="yes"/>") , '<xsl:value-of select="@searchable"/>', '<xsl:value-of select="@map"/>')<xsl:if test="position()!=last()">,</xsl:if>	
		</xsl:for-each>
		);
	</script>
</xsl:template>

<xsl:template match="seperator_row">
<td><table><tr><xsl:for-each select="seperator">
		<td><xsl:if test="boolean(@colspan)">
			<xsl:attribute name="colspan"><xsl:value-of select="@colspan"/></xsl:attribute>
		</xsl:if><table>
			<xsl:for-each select="child::*">
				<tr><td><xsl:value-of select="label"/></td></tr>
				<tr><xsl:apply-templates select="."/></tr>
			</xsl:for-each></table></td>
	</xsl:for-each></tr></table></td>
</xsl:template>

<xsl:template match="entry_associate">
<td><xsl:value-of select="label"/><br/>
<input type='hidden' name='entry_associate_values'><xsl:attribute name="value"><xsl:for-each select="entry"><xsl:value-of select="@identifier"/><xsl:if test="position()!=last()">,</xsl:if></xsl:for-each></xsl:attribute></input>
<div id='div_associated_entries'></div>
<script src='/libertas_images/javascripts/module_info_dir_entries.js'></script>
<script>

var list_of_associated_entries = new Array(<xsl:for-each select="entry">
new Array(<xsl:value-of select="@id"/>, <xsl:value-of select="@src_id"/>, <xsl:value-of select="@src_cat"/>, <xsl:value-of select="@dst_id"/>, <xsl:value-of select="@dst_cat"/>, LIBERTAS_GENERAL_jtidy('<xsl:value-of select="title"/>'))<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
setTimeout("GenerateRankingEntries(list_of_associated_entries,'entry_associate_values');",1000);
</script>
</td>
</xsl:template>

<xsl:template match="list_of_groups">
<div id='fieldProtection'></div>
<script src='/libertas_images/javascripts/protect_fields.js'></script>
<script>
var list_of_groups = new Array(<xsl:for-each select="group">
	new Array(<xsl:value-of select="@value"/>, LIBERTAS_GENERAL_jtidy('<xsl:value-of select="."/>'))<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>
);
var list_of_fields = new Array(<xsl:for-each select="../list_of_fields/secure_field">
	new Array('<xsl:value-of select="@name"/>', LIBERTAS_GENERAL_jtidy('<xsl:value-of select="label"/>'), new Array(<xsl:for-each select="group">'<xsl:value-of select="."/>'<xsl:if test="position()!=last()">, </xsl:if></xsl:for-each>))<xsl:if test="position()!=last()">, 
	</xsl:if></xsl:for-each>
);
var gp = new GenerateProtection(list_of_groups, list_of_fields, 'fieldProtection');
gp.display();
</script>

</xsl:template>

<xsl:template match="list_of_fields">
</xsl:template>


<!-- Choose Category Email Admin Portion Starts
-->
<xsl:template match="choose_email_admin_category">
<xsl:choose>
	<xsl:when test="local-name(../..)='filter'">
	<td><select name="filter_category">
	<option value='ALL'>All</option>
	<xsl:for-each select="category">
	<option>
		<xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute>
		<xsl:if test="@identifier = //specified_categorys/@identifier"><xsl:attribute name="selected">true</xsl:attribute></xsl:if>
		<xsl:value-of select="label"/>
	</option>
		<xsl:if test="children/category">
			<xsl:call-template name="display_child_choose_categories_option">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
				<xsl:with-param name="padding">[[nbsp]]-[[nbsp]]</xsl:with-param>
			</xsl:call-template>
		</xsl:if>
	</xsl:for-each>	
	</select></td>
	</xsl:when>
	<xsl:otherwise>
<xsl:if test="label">
	<tr>
		<td><xsl:value-of select="label"/></td>
	</tr>
</xsl:if>
<!--
<tr>
	<td><a><xsl:attribute name="href">javascript:category_fn('CATEGORYADMIN_ADD','-1');</xsl:attribute>Add New Sub-Category</a></td>
</tr>
-->
	<tr>	
		<td width="50%" valign="top"><div id="CategoryList"></div></td>
		<td width="50%" valign="top"><xsl:if test="//choose_email_admin_category/@can_add=1">
			<div id='addLink'><input type='button' class='bt' onclick="javascript:newCategory.displayForm();">
				<xsl:attribute name="value"><xsl:value-of select="add"/></xsl:attribute>
			</input></div>
			<table id="newCategoryForm"></table>
		</xsl:if></td>
	</tr>
	<input type='hidden' name='newCategories' value=''/>
	<script src='/libertas_images/javascripts/newCategoryEmail.js'></script>
	<script>
	<xsl:comment>
		var newCategory = new newCategories();
		newCategory.output	= '<xsl:value-of select="@output"/>';
		newCategory.id		= '<xsl:value-of select="@identifier"/>';
		<xsl:variable name="pid"><xsl:value-of select="@identifier"/></xsl:variable>
		newCategory.list	= new Array(
		<xsl:for-each select="//category[@parent=$pid]">
		<xsl:variable name="nextid"><xsl:value-of select="@identifier"/></xsl:variable>
			new Array('<xsl:value-of select="@parent"/>', '<xsl:value-of select="@identifier"/>', "<xsl:call-template name="print2">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>", <xsl:choose>
				<xsl:when test="@identifier = //specified_categorys/@identifier">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>)<xsl:if test="boolean(//category[@parent=$pid and @identifier=$nextid]/children/category)">, <xsl:call-template name="showcategoriesJS"><xsl:with-param name="pid"><xsl:value-of select="@identifier"/></xsl:with-param></xsl:call-template></xsl:if><xsl:if test="position()!=last()">,</xsl:if>
		</xsl:for-each>
		);
		newCategory.display(-1);
	</xsl:comment>
	</script>
<!--
<tr>
	<td>
	<table summary="" border="0" cellspacing="0" cellpadding="0">
	<xsl:for-each select="category">
		<tr>
			<td width="20px" valign="top"><xsl:if test="children/category"><xsl:attribute name="rowspan">2</xsl:attribute></xsl:if>
			<input type="checkbox" name="cat_id_list[]"><xsl:attribute name="id">cat_<xsl:value-of select="@identifier"/></xsl:attribute><xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute><xsl:if test="@identifier = //specified_categorys/@identifier"><xsl:attribute name="checked">true</xsl:attribute></xsl:if></input>
			</td>
			<td width="100%"><xsl:if test="children/category"><xsl:attribute name="style">background-color:#ebebeb;</xsl:attribute></xsl:if><label><xsl:attribute name="for">cat_<xsl:value-of select="@identifier"/></xsl:attribute><xsl:value-of select="label"/></label></td>
		</tr>
		<xsl:if test="children/category">
		<tr>
			<td width="100%"><xsl:call-template name="display_child_choose_categories">
				<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
			</xsl:call-template></td>
		</tr>
		</xsl:if>
	</xsl:for-each>	

	</table></td>
</tr>
-->
	</xsl:otherwise>
</xsl:choose>
</xsl:template>
<!-- Choose Category Email Admin Portion Ends
-->


<!-- Choose Email Admin Database Portion Starts
-->
<xsl:template match="choose_email_database">
	<script src="/libertas_images/javascripts/email_admin.js"></script>
	<xsl:element name="select">
			<xsl:attribute name="sel_database">selDatabase</xsl:attribute>
			<xsl:for-each select="databases/database">
				<xsl:element name="option">
					<xsl:attribute name="value">
						<xsl:value-of select="@ID"/>
					</xsl:attribute>
					<xsl:value-of select="@Name"/>
				</xsl:element>
			</xsl:for-each>
		</xsl:element>
			    <input type='button' class='bt' onclick="javascript:getDBFields();" name="get_field" value="Get Fields">
			</input>
		
</xsl:template>
<!-- Choose Email Admin Database Portion Ends
-->

</xsl:stylesheet>

