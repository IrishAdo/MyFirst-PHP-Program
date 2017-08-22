<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.11 $
- Modified $Date: 2005/02/05 12:20:25 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->


<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:include href="../../styles/directory/display_directory.xsl"/>

<xsl:variable name="displayLayout"><xsl:value-of select="//setting[@name='display_layout']"/></xsl:variable>

<xsl:variable name="show_header"><xsl:call-template name="displayLocation"><xsl:with-param name="display_position">header</xsl:with-param></xsl:call-template></xsl:variable>
<xsl:variable name="show_1"><xsl:call-template name="displayLocation"><xsl:with-param name="display_position">1</xsl:with-param></xsl:call-template></xsl:variable>
<xsl:variable name="show_2"><xsl:call-template name="displayLocation"><xsl:with-param name="display_position">2</xsl:with-param></xsl:call-template></xsl:variable>
<xsl:variable name="show_3"><xsl:call-template name="displayLocation"><xsl:with-param name="display_position">3</xsl:with-param></xsl:call-template></xsl:variable>
<xsl:variable name="show_4"><xsl:call-template name="displayLocation"><xsl:with-param name="display_position">4</xsl:with-param></xsl:call-template></xsl:variable>
<xsl:variable name="show_footer"><xsl:call-template name="displayLocation"><xsl:with-param name="display_position">footer</xsl:with-param></xsl:call-template></xsl:variable>

  
<xsl:template name="show_containers">
	<xsl:param name="display_position">2</xsl:param>
	<xsl:param name="ignore_containers">-1</xsl:param>
	<xsl:param name="show_containers">-1</xsl:param>
	<xsl:param name="show_label">0</xsl:param>
	<xsl:param name="show_label_bullet">0</xsl:param>
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="uses_label_class"></xsl:param>
	<xsl:param name="intable">0</xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="alignment">left</xsl:param>
	<xsl:if test="$display_position='2' and $show_containers=-1"><div id="content"><xsl:comment>jump to top links here</xsl:comment></div></xsl:if>


	<xsl:variable name="max_cols"><xsl:choose>
		<xsl:when test="//modules/container[@pos=$display_position]/@columns=5">5</xsl:when>
		<xsl:when test="//modules/container[@pos=$display_position]/@columns=4">4</xsl:when>
		<xsl:when test="//modules/container[@pos=$display_position]/@columns=3">3</xsl:when>
		<xsl:when test="//modules/container[@pos=$display_position]/@columns=2">2</xsl:when>
		<xsl:otherwise>1</xsl:otherwise>
	</xsl:choose></xsl:variable>

	<xsl:for-each select="//modules/container[@pos=$display_position]">
		<xsl:sort select="@rank" order="ascending"/>
		<xsl:variable name="rank_str">[<xsl:value-of select="@rank"/>]</xsl:variable>
		<xsl:variable name="show_this_widget"><xsl:choose>
			<xsl:when test="contains($ignore_containers, $rank_str)">0</xsl:when>
			<xsl:when test="$show_containers!=-1"><xsl:choose>
				<xsl:when test="$show_containers=@rank">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose></xsl:when>
		<xsl:otherwise>1</xsl:otherwise>
		</xsl:choose></xsl:variable>
		<xsl:if test="$show_this_widget=1"><table summary="This table is a container of Elements to be displayed and is defined as structure" cellspacing="0" cellpadding="0">
		<xsl:attribute name="border"><xsl:value-of select="@columns * 5"/></xsl:attribute>
		<xsl:attribute name="class"><xsl:choose>
			<xsl:when test="@width='100%'">width100percent</xsl:when>
			<xsl:when test="contains(@width,'%')">width<xsl:value-of select="substring-before(@width,'%')"/>percent</xsl:when>
			<xsl:when test="@width">width<xsl:value-of select="@width"/></xsl:when>
			<xsl:otherwise>width100percent</xsl:otherwise>
		</xsl:choose></xsl:attribute>
			<xsl:variable name="container"><xsl:value-of select="@identifier"/></xsl:variable>
			<xsl:choose>
				<xsl:when test="((@layouttype='0' or @layouttype='_NA_') and (@columns='0' or @columns='1' or @columns='_NA_'))">
					<xsl:for-each select="webobject[@container=$container and (@pos=$display_position or @pos='_NA_')]">
						<xsl:sort select="@rank" order="ascending"/>
						<xsl:call-template name="show_widgets">
							<xsl:with-param name="cspan"><xsl:value-of select="$max_cols - 1"/></xsl:with-param>
							<xsl:with-param name="display_id"><xsl:value-of select="@identifier"/></xsl:with-param>
							<xsl:with-param name="display_container"><xsl:value-of select="$container"/></xsl:with-param>
							<xsl:with-param name="display_position"><xsl:value-of select="$display_position"/></xsl:with-param>
							<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
							<xsl:with-param name="alignment"><xsl:value-of select="$alignment"/></xsl:with-param>
							<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
							<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
							<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
							<xsl:with-param name="uses_label_class"><xsl:value-of select="$uses_label_class"/></xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>
				</xsl:when>
				<xsl:when test="@layouttype=0 and (@columns=2)">
					<xsl:for-each select="webobject[@container=$container and @pos=$display_position][(position() mod 2) = 1]">
						<xsl:sort select="@rank" order="ascending"/>
						<xsl:comment><xsl:value-of select="@rank"/></xsl:comment>
						<xsl:comment><xsl:value-of select="@container"/></xsl:comment>
						<xsl:comment><xsl:value-of select="@pos"/></xsl:comment>
						<xsl:comment><xsl:value-of select="module/@display"/></xsl:comment>
						<xsl:call-template name="show_widgets">
							<xsl:with-param name="cspan"><xsl:value-of select="$max_cols - 2"/></xsl:with-param>
							<xsl:with-param name="display_id"><xsl:value-of select="@identifier"/></xsl:with-param>
							<xsl:with-param name="display_container"><xsl:value-of select="$container"/></xsl:with-param>
							<xsl:with-param name="display_position"><xsl:value-of select="$display_position"/></xsl:with-param>
							<xsl:with-param name="show_column_2">1</xsl:with-param>
							<xsl:with-param name="alignment"><xsl:value-of select="$alignment"/></xsl:with-param>
							<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
							<xsl:with-param name="width">2</xsl:with-param>
							<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
							<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
							<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
							<xsl:with-param name="uses_label_class"><xsl:value-of select="$uses_label_class"/></xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>
				</xsl:when>
				<xsl:when test="@layouttype=0 and (@columns=3)">
					<xsl:for-each select="//modules/container/webobject[@container=$container and @pos=$display_position][(position() mod 3) = 1]">
						<xsl:sort select="@rank" order="ascending"/>
						<xsl:call-template name="show_widgets">
							<xsl:with-param name="cspan"><xsl:value-of select="$max_cols - 3"/></xsl:with-param>
							<xsl:with-param name="display_id"><xsl:value-of select="@identifier"/></xsl:with-param>
							<xsl:with-param name="display_container"><xsl:value-of select="$container"/></xsl:with-param>
							<xsl:with-param name="display_position"><xsl:value-of select="$display_position"/></xsl:with-param>
							<xsl:with-param name="show_column_2">1</xsl:with-param>
							<xsl:with-param name="show_column_3">1</xsl:with-param>
							<xsl:with-param name="alignment"><xsl:value-of select="$alignment"/></xsl:with-param>
							<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							<xsl:with-param name="width">3</xsl:with-param>
							<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
							<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
							<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
							<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
							<xsl:with-param name="uses_label_class"><xsl:value-of select="$uses_label_class"/></xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>
				</xsl:when>
				<xsl:when test="@layouttype=0 and (@columns=4)">
					<xsl:for-each select="//modules/container/webobject[@container=$container and @pos=$display_position and position() mod 4=1]">
						<xsl:sort select="@rank" order="ascending"/>
						<xsl:call-template name="show_widgets">
							<xsl:with-param name="cspan"><xsl:value-of select="$max_cols - 4"/></xsl:with-param>
							<xsl:with-param name="display_id"><xsl:value-of select="@identifier"/></xsl:with-param>
							<xsl:with-param name="display_container"><xsl:value-of select="$container"/></xsl:with-param>
							<xsl:with-param name="alignment"><xsl:value-of select="$alignment"/></xsl:with-param>
							<xsl:with-param name="display_position"><xsl:value-of select="$display_position"/></xsl:with-param>
							<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							<xsl:with-param name="show_column_2">1</xsl:with-param>
							<xsl:with-param name="show_column_3">1</xsl:with-param>
							<xsl:with-param name="show_column_4">1</xsl:with-param>
							<xsl:with-param name="width">4</xsl:with-param>
							<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
							<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
							<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
							<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
							<xsl:with-param name="uses_label_class"><xsl:value-of select="$uses_label_class"/></xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>
				</xsl:when>			
				<xsl:when test="@layouttype=0 and (@columns=5)">
					<xsl:for-each select="//modules/container/webobject[@container=$container and @pos=$display_position and position() mod 5=1]">
						<xsl:sort select="@rank" order="ascending"/>
						<xsl:call-template name="show_widgets">
							<xsl:with-param name="cspan"><xsl:value-of select="$max_cols - 5"/></xsl:with-param>
							<xsl:with-param name="display_id"><xsl:value-of select="@identifier"/></xsl:with-param>
							<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							<xsl:with-param name="display_container"><xsl:value-of select="$container"/></xsl:with-param>
							<xsl:with-param name="display_position"><xsl:value-of select="$display_position"/></xsl:with-param>
							<xsl:with-param name="show_column_2">1</xsl:with-param>
							<xsl:with-param name="show_column_3">1</xsl:with-param>
							<xsl:with-param name="alignment"><xsl:value-of select="$alignment"/></xsl:with-param>
							<xsl:with-param name="show_column_4">1</xsl:with-param>
							<xsl:with-param name="show_column_5">1</xsl:with-param>
							<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
							<xsl:with-param name="width">5</xsl:with-param>
							<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
							<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
							<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
							<xsl:with-param name="uses_label_class"><xsl:value-of select="$uses_label_class"/></xsl:with-param>
						</xsl:call-template>
					</xsl:for-each>
				</xsl:when>			
				<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
		</table></xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="show_widgets">
	<xsl:param name="cspan">-1</xsl:param>
	<xsl:param name="display_id">-1</xsl:param>
	<xsl:param name="display_position">-1</xsl:param>
	<xsl:param name="display_container">-1</xsl:param>
	<xsl:param name="show_column_2">0</xsl:param>
	<xsl:param name="show_column_3">0</xsl:param>
	<xsl:param name="show_column_4">0</xsl:param>
	<xsl:param name="show_column_5">0</xsl:param>
	<xsl:param name="width">1</xsl:param>
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="uses_label_class"></xsl:param>
	<xsl:param name="intable">0</xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="alignment">left</xsl:param>
	<xsl:param name="show_label">0</xsl:param>
	<xsl:param name="show_label_bullet">0</xsl:param>
	<xsl:param name="uses_label_class"></xsl:param>
	<!--
	[
	sc2	;<xsl:value-of select="$show_column_2"/>;,
	sc3	;<xsl:value-of select="$show_column_3"/>;,
	sc4	;<xsl:value-of select="$show_column_4"/>;,
	sc5	;<xsl:value-of select="$show_column_5"/>;,
	<xsl:value-of select="@identifier"/>
	]
	-->
		<xsl:variable name="showit">
			<xsl:choose>
				<xsl:when test="@type='2'">1</xsl:when>
				<xsl:when test="@type='1' and count(module/*) != 0">1</xsl:when>
				<xsl:when test="@type='1' and count(module/*) = 0">0</xsl:when>
				<xsl:when test="@type='0'">1</xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
	<xsl:if test="$showit=1">
	<tr>
		<td><xsl:attribute name="colspan"><xsl:choose>
				<xsl:when test="$cspan>1"><xsl:value-of select="$cspan"/></xsl:when>
				<xsl:otherwise>1</xsl:otherwise>
			</xsl:choose></xsl:attribute><xsl:choose>
			<xsl:when test="$uses_class!=''"><xsl:attribute name="class"><xsl:value-of select="$uses_class"/></xsl:attribute></xsl:when>
			<xsl:otherwise><!-- <xsl:attribute name="class">align<xsl:value-of select="$alignment"/></xsl:attribute> --></xsl:otherwise>
			</xsl:choose>
			<xsl:variable name="column_1"><xsl:value-of select="@identifier"/></xsl:variable>
			<xsl:for-each select="//modules/container[@identifier = $display_container]/webobject[@identifier=$column_1]">1
				<xsl:call-template name="show_webobject">
					<xsl:with-param name="cspan"><xsl:value-of select="$cspan"/></xsl:with-param>
					<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
					<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
					<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
					<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
					<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
					<xsl:with-param name="uses_label_class"><xsl:value-of select="$uses_label_class"/></xsl:with-param>
			</xsl:call-template>
			</xsl:for-each></td>
		<xsl:if test="$show_column_2='1'">
		<xsl:value-of select="local-name()"/>asd<xsl:value-of select="count(../webobject)"/>
			<xsl:if test="count(../webobject)!=1">
			<td><xsl:attribute name="colspan"><xsl:choose>
				<xsl:when test="$cspan>1"><xsl:value-of select="$cspan"/></xsl:when>
				<xsl:otherwise>1</xsl:otherwise>
				</xsl:choose></xsl:attribute>
				<xsl:choose>
					<xsl:when test="$uses_class!=''"><xsl:attribute name="class"><xsl:value-of select="$uses_class"/></xsl:attribute></xsl:when>
					<xsl:otherwise><!-- <xsl:attribute name="class">align<xsl:value-of select="$alignment"/></xsl:attribute> --></xsl:otherwise>
				</xsl:choose>
				<xsl:variable name="column_2"><xsl:value-of select="following-sibling::webobject[(position() mod $width)=1]/@identifier"/></xsl:variable>
				<xsl:for-each select="//modules/container[@identifier = $display_container]/webobject[@identifier=$column_2]">2
					<xsl:call-template name="show_webobject">
						<xsl:with-param name="cspan"><xsl:value-of select="$cspan"/></xsl:with-param>
						<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
						<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
						<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
						<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
						<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
						<xsl:with-param name="uses_label_class"><xsl:value-of select="$uses_label_class"/></xsl:with-param>
					</xsl:call-template>
				</xsl:for-each></td>
			</xsl:if>
		</xsl:if>
		<xsl:if test="$show_column_3='1' and following-sibling::webobject[(position() mod number($width))=2]">
			<td ><xsl:attribute name="colspan"><xsl:choose>
				<xsl:when test="$cspan>1"><xsl:value-of select="$cspan"/></xsl:when>
				<xsl:otherwise>1</xsl:otherwise>
			</xsl:choose></xsl:attribute>
				<xsl:choose>
					<xsl:when test="$uses_class!=''"><xsl:attribute name="class"><xsl:value-of select="$uses_class"/></xsl:attribute></xsl:when>
					<xsl:otherwise><xsl:attribute name="class">align<xsl:value-of select="$alignment"/></xsl:attribute></xsl:otherwise>
				</xsl:choose>
			<xsl:variable name="column_3"><xsl:value-of select="following-sibling::webobject[(position() mod $width)=2]/@identifier"/></xsl:variable>
			<xsl:for-each select="//modules/container[@identifier = $display_container]/webobject[@identifier=$column_3]">
				<xsl:call-template name="show_webobject">
					<xsl:with-param name="cspan"><xsl:value-of select="$cspan"/></xsl:with-param>
					<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
					<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
					<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
					<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
					<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
					<xsl:with-param name="uses_label_class"><xsl:value-of select="$uses_label_class"/></xsl:with-param>
				</xsl:call-template>
			</xsl:for-each>
		</td>
		</xsl:if>
		<xsl:if test="$show_column_4='1' and following-sibling::webobject[(position() mod number($width))=3]">
			<td ><xsl:attribute name="colspan"><xsl:choose>
				<xsl:when test="$cspan>1"><xsl:value-of select="$cspan"/></xsl:when>
				<xsl:otherwise>1</xsl:otherwise>
			</xsl:choose></xsl:attribute>
				<xsl:choose>
					<xsl:when test="$uses_class!=''"><xsl:attribute name="class"><xsl:value-of select="$uses_class"/></xsl:attribute></xsl:when>
					<xsl:otherwise><xsl:attribute name="class">align<xsl:value-of select="$alignment"/></xsl:attribute></xsl:otherwise>
				</xsl:choose>
			<xsl:variable name="column_4"><xsl:value-of select="following-sibling::webobject[(position() mod $width)=3]/@identifier"/></xsl:variable>
			<xsl:for-each select="//modules/container[@identifier = $display_container]/webobject[@identifier=$column_4]">
				<xsl:call-template name="show_webobject">
					<xsl:with-param name="cspan"><xsl:value-of select="$cspan"/></xsl:with-param>
					<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
					<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
					<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
					<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
					<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
					<xsl:with-param name="uses_label_class"><xsl:value-of select="$uses_label_class"/></xsl:with-param>
				</xsl:call-template>
			</xsl:for-each>
		</td>
		</xsl:if>
		<xsl:if test="$show_column_5='1' and following-sibling::webobject[(position() mod number($width))=4]">
			<td><xsl:attribute name="colspan"><xsl:choose>
				<xsl:when test="$cspan>1"><xsl:value-of select="$cspan"/></xsl:when>
				<xsl:otherwise>1</xsl:otherwise>
			</xsl:choose></xsl:attribute>
				<xsl:choose>
					<xsl:when test="$uses_class!=''"><xsl:attribute name="class"><xsl:value-of select="$uses_class"/></xsl:attribute></xsl:when>
					<xsl:otherwise><xsl:attribute name="class">align<xsl:value-of select="$alignment"/></xsl:attribute></xsl:otherwise>
				</xsl:choose>
			<xsl:variable name="column_5"><xsl:value-of select="following-sibling::webobject[(position() mod $width)=4]/@identifier"/></xsl:variable>
			<xsl:for-each select="//modules/container[@identifier = $display_container]/webobject[@identifier=$column_5]">
				<xsl:call-template name="show_webobject">
					<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
					<xsl:with-param name="cspan"><xsl:value-of select="$cspan"/></xsl:with-param>
					<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
					<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
					<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
					<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
					<xsl:with-param name="uses_label_class"><xsl:value-of select="$uses_label_class"/></xsl:with-param>
				</xsl:call-template>
			</xsl:for-each>
		</td>
		</xsl:if>
	</tr>
	</xsl:if>
</xsl:template>

<xsl:template name="show_webobject">
	<xsl:param name="cspan">-1</xsl:param>
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="intable">0</xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="show_label">0</xsl:param>
	<xsl:param name="show_label_bullet">0</xsl:param>
	<xsl:param name="uses_label_class"></xsl:param>
		<xsl:variable name="showit">
			<xsl:choose>
				<xsl:when test="@type='2'">0</xsl:when>
				<xsl:when test="count(module/*) != 0">1</xsl:when>
				<xsl:otherwise></xsl:otherwise>
			</xsl:choose>
		</xsl:variable>
	<xsl:choose>
	<xsl:when test="$show_label=1 and $showit=1">
			<table class="width100percent" cellspacing="0" cellpadding="0" summary="" border="0">
				<tr>
					<td><xsl:attribute name="class"><xsl:value-of select="$uses_label_class"/></xsl:attribute><table width="width100percent" cellspacing="0" cellpadding="0" summary="" border="0">
					<tr>
					<xsl:if test="$showbulletimagebeforelabel=1">
						<td><img alt=""><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/title_bullet.gif</xsl:attribute></img></td>
					</xsl:if>
					<td>
						<xsl:attribute name="class"><xsl:value-of select="$uses_label_class"/>text</xsl:attribute>[[nbsp]][[nbsp]]<xsl:call-template name="get_translation"><xsl:with-param name="check"><xsl:choose>
							<xsl:when test="@type=2"><xsl:value-of select="property/option[name='label']/value"/></xsl:when>
							<xsl:when test="module[@display='form']/form/@label"><xsl:value-of select="module[@display='form']/form/@label"/></xsl:when>
							<xsl:when test="module[@display='form']/form/label"><xsl:value-of select="module[@display='form']/form/label"/></xsl:when>
							<xsl:when test="module/label"><xsl:value-of select="module/label"/></xsl:when>
							<xsl:when test="@label"><xsl:value-of select="@label" disable-output-escaping="yes"/></xsl:when>
							<xsl:otherwise><xsl:value-of select="label" disable-output-escaping="yes"/></xsl:otherwise>
						</xsl:choose></xsl:with-param>
					</xsl:call-template></td>
					</tr></table></td>
				</tr>
				<tr>
					<td colspan="2" class="tablecell">
					<xsl:call-template name="show_webobject_item">
						<xsl:with-param name="cspan"><xsl:value-of select="$cspan"/></xsl:with-param>
						<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
						<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
						<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>	
						<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
						<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
					</xsl:call-template></td>
				</tr>
			</table>
	</xsl:when>
	<xsl:otherwise>
		<xsl:call-template name="show_webobject_item">
			<xsl:with-param name="cspan"><xsl:value-of select="$cspan"/></xsl:with-param>
			<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
			<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
			<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>	
			<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
			<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
		</xsl:call-template>
	</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="show_webobject_item">
	<xsl:param name="cspan">-1</xsl:param>
	<xsl:param name="uses_class"></xsl:param>
	<xsl:param name="intable">0</xsl:param>
	<xsl:param name="labelinnewrow">0</xsl:param>
	<xsl:param name="show_label">0</xsl:param>
	<xsl:param name="show_label_bullet">0</xsl:param>
	<xsl:comment> <xsl:value-of select="local-name()"/> </xsl:comment>
	<xsl:choose>
		<xsl:when test="@type='0'">
			<xsl:choose>
				<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
					<xsl:with-param name="header"><xsl:if test="label and @display_label='1'">&#60;label class='webobject'&#62;<xsl:value-of select="label" disable-output-escaping="yes"/>&#60;/label&#62;</xsl:if></xsl:with-param>
					<xsl:with-param name="content"><xsl:value-of select="data" disable-output-escaping="yes"/></xsl:with-param>
				</xsl:call-template></xsl:when>
				<xsl:otherwise><xsl:value-of select="data" disable-output-escaping="yes"/></xsl:otherwise>
			</xsl:choose></xsl:when>
			<xsl:when test="@type=2">
					<xsl:if test="property/option[name='label']/value and $show_label='1'"><div class='webobjectheader'>
						<label><xsl:call-template name="get_translation">
							<xsl:with-param name="check"><xsl:value-of select="property/option[name='label']/value" disable-output-escaping="yes"/></xsl:with-param>
						</xsl:call-template></label>
					</div></xsl:if>
					<div><xsl:attribute name='class'><xsl:choose>
						<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
						<xsl:when test="command='WEBOBJECTS_SHOW_MAIN_MENU'">webobjectnoindent</xsl:when>
						<xsl:otherwise>webobject</xsl:otherwise>
					</xsl:choose></xsl:attribute>
					<xsl:attribute name='style'><xsl:for-each select="property/option"><xsl:value-of select="name"/>:<xsl:value-of select="value"/>;</xsl:for-each></xsl:attribute>
					   	<xsl:choose>
							<xsl:when test="command='WEBOBJECTS_SHOW_SITEMAP_EXTRACT'"><xsl:choose>
								<xsl:when test="$sitemap_type='columns'"><xsl:call-template name="sitemap_columns"/></xsl:when>
								<xsl:otherwise><xsl:call-template name="sitemap_default"/></xsl:otherwise>
							</xsl:choose></xsl:when>
							<!--
								Page options include the following icon only links if available
								Printer Friendly Button
								Page Comment Button
								Email a Friend Button
								Bookmark Page Button
							-->
							<xsl:when test="command='WEBOBJECTS_SHOW_PAGE_OPTIONS' or command='WEBOBJECTS_SHOW_PRINTER_FRIENDLY'"><xsl:call-template name="display_page_functions_as_widgets">
								<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							</xsl:call-template></xsl:when>
							<!--
								Printer friendly icon and text link
							-->
							<xsl:when test="command='WEBOBJECTS_SHOW_PRINTERFRIENDLY'"><xsl:call-template name="display_page_functions_as_widgets">
								<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							</xsl:call-template></xsl:when>
							<!--
								Page Comment icon and text link
							-->
							<xsl:when test="command='WEBOBJECTS_SHOW_PAGECOMMENTS'"><xsl:call-template name="display_page_functions_as_widgets">
								<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							</xsl:call-template></xsl:when>
							<!--
								Email A Friend icon and text link
							-->
							<xsl:when test="command='WEBOBJECTS_SHOW_EMAILAFRIEND'"><xsl:call-template name="display_page_functions_as_widgets">
								<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							</xsl:call-template></xsl:when>
							<!--
								Bookmark page icon and text link
							<xsl:when test="command='WEBOBJECTS_SHOW_BOOKMARKPAGE'"><xsl:call-template name="display_page_functions_as_widgets">
								<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							</xsl:call-template></xsl:when>
							-->
							<!--
								Show Archive function
							-->
							<xsl:when test="command='WEBOBJECTS_SHOW_ARCHIVE_OPTIONS'"><xsl:call-template name="webobject_archive"></xsl:call-template></xsl:when>
							
							<xsl:when test="command='WEBOBJECTS_SHOW_SITE_UPDATED_DATE'"><xsl:choose>
								<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
										<xsl:with-param name="header">Site Updated</xsl:with-param>
										<xsl:with-param name="content"><div><xsl:attribute name='class'><xsl:choose>
									<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
									<xsl:otherwise>date</xsl:otherwise>
								</xsl:choose></xsl:attribute><xsl:value-of select="//setting[@name='site_updated']"/></div></xsl:with-param>
								</xsl:call-template></xsl:when>
								<xsl:otherwise><div><xsl:attribute name='class'><xsl:choose>
									<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
									<xsl:otherwise>date</xsl:otherwise>
								</xsl:choose></xsl:attribute><xsl:value-of select="//setting[@name='site_updated']"/></div></xsl:otherwise>
							</xsl:choose></xsl:when>
							<xsl:when test="command='WEBOBJECTS_SHOW_PAGE_UPDATED_DATE'"><!-- not defined yet --></xsl:when>

							<xsl:when test="command='WEBOBJECTS_SHOW_DATE'"><xsl:choose>
								<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
										<xsl:with-param name="header">Todays Date</xsl:with-param>
										<xsl:with-param name="content"><div><xsl:attribute name='class'><xsl:choose>
									<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
									<xsl:otherwise>date</xsl:otherwise>
								</xsl:choose></xsl:attribute><xsl:value-of select="//setting[@name='date']"/></div></xsl:with-param>
								</xsl:call-template></xsl:when>
								<xsl:otherwise><div><xsl:attribute name='class'><xsl:choose>
									<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
									<xsl:otherwise>date</xsl:otherwise>
								</xsl:choose></xsl:attribute><xsl:value-of select="//setting[@name='date']"/></div></xsl:otherwise>
							</xsl:choose></xsl:when>
							
							
							<xsl:when test="command='WEBOBJECTS_SHOW_LOGIN'"><xsl:call-template name="display_login">
								<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							</xsl:call-template></xsl:when>
							<xsl:when test="command='WEBOBJECTS_SHOW_BREADCRUMB'"><div><xsl:attribute name='class'><xsl:choose>
									<xsl:when test="$uses_class!=''"><xsl:value-of select="$uses_class"/></xsl:when>
									<xsl:otherwise>breadcrumb</xsl:otherwise>
								</xsl:choose></xsl:attribute><xsl:call-template name="display_breadcrumb_trail"><xsl:with-param name="linking" select="0"/>
								<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							</xsl:call-template></div></xsl:when>
							
							<xsl:when test="command='WEBOBJECTS_SHOW_MAIN_MENU'"><a name="menu"/><xsl:call-template name="display_menu"/></xsl:when>
							<xsl:when test="command='WEBOBJECTS_SHOW_SUB_MENU'"><a name="submenu"/><xsl:call-template name="display_submenu"/></xsl:when>
							<xsl:when test="command='WEBOBJECTS_SHOW_WAI_COMPLIANCE'"><xsl:call-template name="display_bobby_rating"/></xsl:when>
							<xsl:when test="command='WEBOBJECTS_SHOW_CLIENT_FOOTER'"><xsl:choose>
								<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
										<xsl:with-param name="header"> </xsl:with-param>
										<xsl:with-param name="content"><xsl:call-template name="display_footer_data"></xsl:call-template></xsl:with-param></xsl:call-template></xsl:when>
								<xsl:otherwise><xsl:call-template name="display_footer_data"></xsl:call-template></xsl:otherwise>
							</xsl:choose></xsl:when>
							<xsl:when test="command='WEBOBJECTS_SHOW_SEARCH_BOX_COLUMN'"><xsl:choose>
								<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
										<xsl:with-param name="valign">middle</xsl:with-param>
										<xsl:with-param name="header"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SEARCH_LABEL'"/></xsl:call-template></xsl:with-param>
										<xsl:with-param name="content"><xsl:call-template name="display_page_search">
										<xsl:with-param name="searchType">COLUMN</xsl:with-param>
										<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
									</xsl:call-template></xsl:with-param>
								</xsl:call-template></xsl:when>
								<xsl:otherwise><xsl:call-template name="display_page_search">
								<xsl:with-param name="searchType">COLUMN</xsl:with-param>
								<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							</xsl:call-template></xsl:otherwise>
							</xsl:choose></xsl:when>
							<xsl:when test="command='WEBOBJECTS_SHOW_SEARCH_BOX_ROW'"><xsl:choose>
								<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
										<xsl:with-param name="header"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_SEARCH_LABEL'"/></xsl:call-template></xsl:with-param>
										<xsl:with-param name="content"><xsl:call-template name="display_page_search">
										<xsl:with-param name="searchType">ROW</xsl:with-param>
										<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
									</xsl:call-template></xsl:with-param>
								</xsl:call-template></xsl:when>
								<xsl:otherwise><xsl:call-template name="display_page_search">
								<xsl:with-param name="searchType">ROW</xsl:with-param>
								<xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param>
							</xsl:call-template></xsl:otherwise>
							</xsl:choose></xsl:when>
							<xsl:when test="command='WEBOBJECTS_SHOW_SEARCH_BOX_FANCY'"><xsl:call-template name="display_fancy_search"/></xsl:when>
							<xsl:when test="command='WEBOBJECTS_SHOW_TEXT_BASED_LEVEL_ONE_MENU'"><xsl:call-template name="display_footer"/></xsl:when>
							<xsl:when test="command='WEBOBJECTS_SHOW_OPTIONS'"><a href="index.php" class="menulevel0"><xsl:value-of select="//menu[url='index.php']/label"/></a><xsl:choose>
									<xsl:when test="//module/menu[./display_options/display[.='SFORM_DISPLAY_CONTACT_US']]"> | <a class="menulevel0"><xsl:attribute name="href"><xsl:value-of select="//module/menu[./display_options/display[.='SFORM_DISPLAY_CONTACT_US']]/url"/></xsl:attribute><xsl:value-of select="//module/menu[./display_options/display[.='SFORM_DISPLAY_CONTACT_US']]/label"/></a></xsl:when>
									<xsl:when test="//display[.='SFORM_DISPLAY_CONTACT_US']"> | <a class="menulevel0"><xsl:attribute name="href"><xsl:value-of select="//menu[./display_options/display[.='SFORM_DISPLAY_CONTACT_US']]/url"/></xsl:attribute><xsl:value-of select="//menu[./display_options/display[.='SFORM_DISPLAY_CONTACT_US']]/label"/></a></xsl:when>
								</xsl:choose><xsl:if test="//session/groups[@type='2'] and //menu[url='admin/index.php']"> | <a class="menulevel0" href="admin/index.php"><xsl:value-of select="//menu[url='admin/index.php']/label"/></a></xsl:if>
							</xsl:when>
						</xsl:choose>
					</div>
				</xsl:when>
				<xsl:otherwise>
					<xsl:for-each select="module">
					   	<xsl:choose>
							<xsl:when test="@name='mirror'"><xsl:choose>
								<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
										<xsl:with-param name="header"><xsl:choose>
												<xsl:when test="//xml_document/modules/container/webobject/module[@name='mirror']/label=''">&#60;a class="mirrorlabel" href="<xsl:value-of select="//xml_document/modules/container/webobject/module[@name='mirror']/menulocation"/>"&#62;<xsl:value-of select="//menu[url=//xml_document/modules/container/webobject/module[@name='mirror']/menulocation]/label" disable-output-escaping="yes"/>&#60;/a&#62;
												<xsl:call-template name="show_edit_button">
													<xsl:with-param name="cmd_starter">MIRROR_</xsl:with-param>
													<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
												</xsl:call-template>
												</xsl:when>
												<xsl:otherwise>
												<xsl:value-of select="//xml_document/modules/container/webobject/module[@name='mirror']/label"/> <xsl:call-template name="show_edit_button"><xsl:with-param name="cmd_starter">MIRROR_</xsl:with-param></xsl:call-template></xsl:otherwise>
											</xsl:choose></xsl:with-param>
										<xsl:with-param name="content"><xsl:call-template name="display_mirror">
											<xsl:with-param name="show_label">0</xsl:with-param>
											<xsl:with-param name="header"></xsl:with-param>
										</xsl:call-template></xsl:with-param>
								</xsl:call-template></xsl:when>
								<xsl:otherwise><xsl:comment>mirror</xsl:comment><xsl:call-template name="display_mirror">
									<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
									<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
								</xsl:call-template></xsl:otherwise>
							</xsl:choose></xsl:when>
			    			
			    			<xsl:when test="@name='layout'"></xsl:when>
							<xsl:when test="@display='form'">
							<xsl:choose>
								<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
										<xsl:with-param name="header"><xsl:choose>
											<xsl:when test="form/label"><xsl:value-of select="form/label"/></xsl:when>
											<xsl:otherwise><xsl:value-of select="form/@label"/></xsl:otherwise>
										</xsl:choose></xsl:with-param>
										<xsl:with-param name="content">
										<xsl:call-template name="display_form">
											<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
											<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
											<xsl:with-param name="module"><xsl:value-of select="@name"/></xsl:with-param>
											<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
											<xsl:with-param name="id"><xsl:value-of select="form/@name"/></xsl:with-param>
											<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
										</xsl:call-template></xsl:with-param>
								</xsl:call-template></xsl:when>
								<xsl:otherwise><xsl:call-template name="display_form">
									<xsl:with-param name="show_label"><xsl:value-of select="$show_label"/></xsl:with-param>
									<xsl:with-param name="show_label_bullet"><xsl:value-of select="$show_label_bullet"/></xsl:with-param>
									<xsl:with-param name="id"><xsl:value-of select="form/@name"/></xsl:with-param>
									<xsl:with-param name="module"><xsl:value-of select="@name"/></xsl:with-param>
									<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
									<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
								</xsl:call-template></xsl:otherwise>
							</xsl:choose></xsl:when>
					    	<xsl:when test="@display='reference'"></xsl:when>
					    	<xsl:when test="@display='search_results'"><xsl:comment>Search Results</xsl:comment><xsl:choose>
								<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
										<xsl:with-param name="header">Search</xsl:with-param>
										<xsl:with-param name="content"><xsl:call-template name="display_search_results">
											<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
											<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
										</xsl:call-template></xsl:with-param>
								</xsl:call-template></xsl:when>
								<xsl:otherwise><xsl:call-template name="display_search_results">
											<xsl:with-param name="labelinnewrow"><xsl:value-of select="$labelinnewrow"/></xsl:with-param>
											<xsl:with-param name="intable"><xsl:value-of select="$intable"/></xsl:with-param>
										</xsl:call-template></xsl:otherwise>
							</xsl:choose></xsl:when>
							<xsl:when test="@name='forum' and @display='results'"><xsl:call-template name="display_forum_results"/></xsl:when>
							<xsl:when test="@display='results'"><xsl:call-template name="display_list_results"/></xsl:when>
							<xsl:when test="@display='confirm'"><xsl:apply-templates/></xsl:when>
					    	<xsl:when test="data_list"><xsl:call-template name="display_list_results"/></xsl:when>
							<xsl:when test="@display='filter'"><xsl:apply-templates select="filter/form"/></xsl:when>
					    	<xsl:when test="@display='SLIDESHOW'"><xsl:choose>
								<xsl:when test="$intable = 1"><xsl:call-template name="display_a_table">
									<xsl:with-param name="header"><xsl:choose><xsl:when test="label"><xsl:value-of select="label"/></xsl:when><xsl:otherwise>Latest Information</xsl:otherwise></xsl:choose></xsl:with-param>
									<xsl:with-param name="content"><xsl:call-template name="display_slideshow"><xsl:with-param name="show_label">0</xsl:with-param><xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param></xsl:call-template></xsl:with-param>
								</xsl:call-template></xsl:when>
								<xsl:otherwise><xsl:call-template name="display_slideshow"><xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param></xsl:call-template></xsl:otherwise>
							</xsl:choose></xsl:when>
					    	<xsl:when test="@display='ATOZ'"><xsl:choose>
								<xsl:when test="$intable = 1"><xsl:call-template name="display_a_table">
									<xsl:with-param name="header"><xsl:choose><xsl:when test="label"><xsl:value-of select="label"/></xsl:when><xsl:otherwise>Latest Information</xsl:otherwise></xsl:choose></xsl:with-param>
									<xsl:with-param name="content"><xsl:call-template name="display_atoz"><xsl:with-param name="show_label">0</xsl:with-param><xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param></xsl:call-template></xsl:with-param>
								</xsl:call-template></xsl:when>
								<xsl:otherwise><xsl:call-template name="display_atoz"><xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param></xsl:call-template></xsl:otherwise>
							</xsl:choose></xsl:when>
					    	<xsl:when test="@display='LATEST'"><xsl:choose>
								<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
									<xsl:with-param name="header"><xsl:choose><xsl:when test="label"><xsl:value-of select="label"/></xsl:when><xsl:otherwise>Latest Information</xsl:otherwise></xsl:choose></xsl:with-param>
									<xsl:with-param name="content"><xsl:call-template name="display_latest"><xsl:with-param name="show_label">0</xsl:with-param><xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param></xsl:call-template></xsl:with-param>
								</xsl:call-template></xsl:when>
								<xsl:otherwise><xsl:call-template name="display_latest"><xsl:with-param name="uses_class"><xsl:value-of select="$uses_class"/></xsl:with-param></xsl:call-template></xsl:otherwise>
							</xsl:choose></xsl:when>
					    	<xsl:when test="@display='LOCATION'">
								<xsl:if test="page">
							    	<ul>
			    					<xsl:for-each select="page">
						    			<li><xsl:value-of select="substring-before(publishdate,' ')"/> - 
										<a><xsl:attribute name="href"><xsl:value-of select="locations/location[@url=//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']]/@url"/>?command=PRESENTATION_DISPLAY&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute><xsl:value-of select="title"/></a></li>
						    		</xsl:for-each>
									</ul>
								</xsl:if>
							</xsl:when>
				   			<xsl:when test="@display='ENTRY'">
								<xsl:if test="page">
									<xsl:choose>
										<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
											<xsl:with-param name="header"><xsl:value-of select="page[position()=1]/title"/></xsl:with-param>
											<xsl:with-param name="content"><xsl:choose>
												<xsl:when test="count(page)=1">
													<xsl:call-template name="display_this_page">
														<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
														<xsl:with-param name="alt_title">0</xsl:with-param>
														<xsl:with-param name="content">1</xsl:with-param>
														<xsl:with-param name="summary">0</xsl:with-param>
														<xsl:with-param name="enable_discussion">1</xsl:with-param>
														<xsl:with-param name="style">LOCATION</xsl:with-param>
														<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
														<xsl:with-param name="identifier"><xsl:value-of select="page[position()=1]/@identifier"/></xsl:with-param>
													</xsl:call-template>
												</xsl:when>
												<xsl:otherwise>
													<xsl:call-template name="display_list"></xsl:call-template>
												</xsl:otherwise>
											</xsl:choose>
						    				<xsl:if test="//menu[url=//setting[@name='script']]/display_options/display = 'PRESENTATION_DISPLAY' and count(//xml_document/modules/container/webobject/module/page)=1 and contains(//setting[@name='real_script'],'index.php')=false()">
								    			<a class="returntolink"><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/></xsl:attribute>Return to '<xsl:value-of select="//menu[url=//setting[@name='script']]/label"/>'</a>
						    				</xsl:if></xsl:with-param>
										</xsl:call-template></xsl:when>
									<xsl:otherwise><xsl:choose>
										<xsl:when test="count(page)=1">
											<xsl:call-template name="display_this_page">
												<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
												<xsl:with-param name="alt_title">0</xsl:with-param>
												<xsl:with-param name="content">1</xsl:with-param>
												<xsl:with-param name="summary">0</xsl:with-param>
												<xsl:with-param name="enable_discussion">1</xsl:with-param>
												<xsl:with-param name="style">LOCATION</xsl:with-param>
												<xsl:with-param name="display_more_as_text"><xsl:value-of select="$display_more_as_text"/></xsl:with-param>
												<xsl:with-param name="identifier"><xsl:value-of select="page[position()=1]/@identifier"/></xsl:with-param>
											</xsl:call-template>
										</xsl:when>
										<xsl:otherwise>
													<xsl:call-template name="display_list"></xsl:call-template>
										</xsl:otherwise>
									</xsl:choose>
					    			<xsl:if test="//menu[url=//setting[@name='script']]/display_options/display = 'PRESENTATION_DISPLAY' and count(//xml_document/modules/container/webobject/module/page)=1 and contains(//setting[@name='real_script'],'index.php')=false()">
							    		<a class="returntolink"><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/></xsl:attribute>Return to '<xsl:value-of select="//menu[url=//setting[@name='script']]/label"/>'</a>
					    			</xsl:if></xsl:otherwise>
</xsl:choose>
								</xsl:if>
								<xsl:if test="./vehicle">
					    		<table summary="Vehicle information" width="100%" border="0"><xsl:attribute name="summary">This table contains the content for a vehicle</xsl:attribute>
					    			<xsl:for-each select="vehicle">
					    			<tr>
										<td valign="top">
											<xsl:if test="licence_plate!=''"><strong>Licence Plate : </strong><xsl:value-of select="licence_plate" disable-output-escaping="yes"/><br /></xsl:if>
											<xsl:if test="year!=''"><strong>Year : </strong><xsl:value-of select="year" disable-output-escaping="yes"/><br /></xsl:if>
											<xsl:if test="manufacturer_description!=''"><strong>Manufacturer : </strong><xsl:value-of select="manufacturer_description" disable-output-escaping="yes"/><br /></xsl:if>
											<xsl:if test="model_description!=''"><strong>Model : </strong><xsl:value-of select="model_description" disable-output-escaping="yes"/><br /></xsl:if>
											<xsl:if test="gears_description!=''"><strong>Gears : </strong><xsl:value-of select="gears_description" disable-output-escaping="yes"/><br /></xsl:if>
											<xsl:if test="cab_description!=''"><strong>Cab Type : </strong><xsl:value-of select="cab_description" disable-output-escaping="yes"/><br /></xsl:if>
											<xsl:if test="body_description!=''"><strong>Body Type : </strong><xsl:value-of select="body_description" disable-output-escaping="yes"/><br /></xsl:if>
											<xsl:if test="price!='' and price!='0'"><strong>Price : </strong><xsl:value-of select="price" disable-output-escaping="yes"/><br /></xsl:if>
											<xsl:if test="tax_month!='' or tax_year!=''"><strong>Taxed : </strong><xsl:value-of select="tax_month" disable-output-escaping="yes"/> / <xsl:value-of select="tax_year" disable-output-escaping="yes"/><br /></xsl:if>
											<xsl:if test="engine_size!=''"><strong>Engine Size : </strong><xsl:value-of select="engine_size" disable-output-escaping="yes"/><br /></xsl:if>
											<xsl:if test="test!=''"><strong>Test Type : </strong><xsl:choose><xsl:when test="test='0'">MOT</xsl:when><xsl:otherwise>PSV</xsl:otherwise></xsl:choose><br /></xsl:if>
											<xsl:if test="description!=''"><p><strong>Description : </strong><br/><xsl:value-of select="description" disable-output-escaping="yes"/></p></xsl:if>
										</td>
										<td width="300" valign="top"><xsl:if test="image_main[@exists=1]"><img border="0" width="300"><xsl:attribute name="src"><xsl:value-of select="image_main"/></xsl:attribute></img></xsl:if></td>
									</tr>
									<xsl:if test="position()!=last()"><tr><td colspan="2"><hr/></td></tr></xsl:if>
					    			</xsl:for-each>
						    	</table>
				    			<xsl:if test="//menu[url=//setting[@name='script']]/display_options/display = 'VEHICLE_DISPLAY' and count(//xml_document/modules/container/webobject/module/vehicle)=1 and contains(//setting[@name='real_script'],'index.php')=false()">
						    		<p class="topofdoc"><a class="returntolink"><xsl:attribute name="href"><xsl:value-of select="//xml_document/modules/module[@name='system_prefs' and @display='settings']/setting[@name='script']"/></xsl:attribute>Return to '<xsl:value-of select="//menu[url=//setting[@name='script']]/label"/>'</a></p>
								</xsl:if>
					    		</xsl:if>
	<xsl:if test="//xml_document/modules/container/webobject/module/headline and contains(//setting[@name='script'],'index.php')">
		<xsl:call-template name='show_headlines'/>
	</xsl:if>								
					    	</xsl:when>
							<xsl:when test="@display='PERSISTANT'">
								<xsl:if test="page">
									<xsl:choose>
										<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
											<xsl:with-param name="header"><xsl:choose>
												<xsl:when test="//setting[@name='sp_page_title_is_caps']='Yes' "><xsl:value-of select="translate(page[position()=1]/title, 'abcdefghijklmnopqrstuvwxyz','ABCDEFGHIJKLMNOPQRSTUVWXYZ')" disable-output-escaping="yes"/></xsl:when>
												<xsl:otherwise><xsl:value-of select="page[position()=1]/title"/></xsl:otherwise>
											</xsl:choose></xsl:with-param>
											<xsl:with-param name="content"><xsl:call-template name="display_list"></xsl:call-template></xsl:with-param>
										</xsl:call-template></xsl:when>
										<xsl:otherwise>
											<xsl:call-template name="display_list"></xsl:call-template>
										</xsl:otherwise>
									</xsl:choose>
								</xsl:if>
								<xsl:if test="//xml_document/modules/container/webobject/module[@name='presentation']/headline and contains(//setting[@name='script'],'index.php')">
									<xsl:call-template name='show_headlines'/>
								</xsl:if>								
					    	</xsl:when>
							<xsl:when test="@name='mirror'"></xsl:when>
							<xsl:when test="@display='embeddedInformation'"></xsl:when>
							
					    	<xsl:otherwise><td><xsl:apply-templates/></td></xsl:otherwise>
						</xsl:choose>
					</xsl:for-each>
				</xsl:otherwise>
			</xsl:choose>
</xsl:template>

<xsl:template name="displayLocation">
	<xsl:param name="display_position">2</xsl:param>
<xsl:choose>
		<xsl:when test="//modules/container[@pos=$display_position]/webobject"><xsl:choose>
			<xsl:when test="//modules/container[@pos=$display_position]/webobject/command!='WEBOBJECTS_SHOW_SUB_MENU'">1</xsl:when>
			<xsl:when test="//modules/container[@pos=$display_position]/webobject/module">1</xsl:when>
			<xsl:when test="//modules/container[@pos=$display_position]/webobject/command='WEBOBJECTS_SHOW_SUB_MENU'"><xsl:choose>
				<xsl:when test="$current_site_depth >= $menu_splits_at_depth"><xsl:choose>
					<xsl:when test="$current_site_depth + 1 >= $menu_splits_at_depth">1</xsl:when>
					<xsl:otherwise>0</xsl:otherwise>
				</xsl:choose></xsl:when>
				<xsl:otherwise>0</xsl:otherwise>
			</xsl:choose></xsl:when>
		<xsl:otherwise>0</xsl:otherwise>
	</xsl:choose></xsl:when>
<xsl:otherwise>0</xsl:otherwise>
</xsl:choose></xsl:template>
<!--
<xsl:choose>
	<xsl:when test="$intable=1"><xsl:call-template name="display_a_table">
			<xsl:with-param name="header"></xsl:with-param>
			<xsl:with-param name="content"></xsl:with-param>
	</xsl:call-template></xsl:when>
	<xsl:otherwise></xsl:otherwise>
</xsl:choose>
-->
</xsl:stylesheet>