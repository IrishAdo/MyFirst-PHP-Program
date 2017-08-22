<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.3 $
- Modified $Date: 2004/10/04 13:59:50 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
<xsl:template name="display_list">
	<xsl:param name="display_more_as_text"/>
	<xsl:param name="displaydigits">1</xsl:param>
	<xsl:param name="uses_class"/>
	<xsl:param name="pos">TOP</xsl:param>
	<xsl:comment>fasdf</xsl:comment>
<xsl:choose>
	<xsl:when test="$title_page=1">
		<xsl:call-template name="display_this_page">
			<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
			<xsl:with-param name="alt_title">1</xsl:with-param>
			<xsl:with-param name="content">1</xsl:with-param>
			<xsl:with-param name="date_publish">0</xsl:with-param>
			<xsl:with-param name="more">0</xsl:with-param>
			<xsl:with-param name="style">LOCATION</xsl:with-param>
			<xsl:with-param name="identifier"><xsl:value-of select="//modules/container/webobject/module/page[position()=1]/@identifier"/></xsl:with-param>
		</xsl:call-template>
	</xsl:when>
	<xsl:otherwise>
		<xsl:variable name="page_title_string"><xsl:choose>
			<xsl:when test="count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page) != 1"><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></xsl:when>
			<xsl:when test="//setting[@name='fake_title']!=''"><xsl:value-of select="//setting[@name='fake_title']" disable-output-escaping="yes"/></xsl:when>
			<xsl:when test="//setting[@name='real_script']='index.php'"><xsl:value-of select="//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page[position()=1]/title" disable-output-escaping="yes"/></xsl:when>
			<xsl:when test="not(contains(//setting[@name='real_script'],'index.php')) and boolean(//module[@name='presentation' and @display='ATOZ']/letters) and count(//modules/container/webobject/module[@name='presentation' and @display!='LATEST']/page)!=1"><xsl:value-of select="//menu[url=//setting[@name='script']]/label"/></xsl:when>
			<xsl:when test="not(contains(//setting[@name='real_script'],'index.php')) and boolean(//module[@name='presentation' and @display='ATOZ']/letters) "><xsl:call-template name="display_firstpage"/></xsl:when>
			<xsl:when test="not(contains(//setting[@name='script'],//setting[@name='fake_script']))"><xsl:value-of select="//modules/container/webobject/module[@name='information_presentation']/content/entry/seperator_row/seperator/field[@name='ie_title']/value" disable-output-escaping="yes"/></xsl:when>
				<xsl:otherwise><xsl:call-template name="display_firstpage"/></xsl:otherwise>
			</xsl:choose></xsl:variable>
			<div class="page" id="page1"><h1 class='entrytitle' id="notitlepage"><span><xsl:value-of select="$page_title_string"/></span></h1></div>
	</xsl:otherwise>
</xsl:choose>	
<xsl:comment>
count : <xsl:value-of select="count(//xml_document/modules/container/webobject/module[@name='presentation' and @display='PERSISTANT']/link)"/>
cols : <xsl:value-of select="cols"/>

</xsl:comment>
<xsl:if test="//xml_document/modules/container/webobject/module[@name='presentation' and @display='PERSISTANT']/link">
	<xsl:variable name="columns"><xsl:value-of select="cols"/></xsl:variable>
	<xsl:choose>
		<xsl:when test="$columns=1">
			<ul>
				<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='presentation' and @display='PERSISTANT']/link">
					<li><a>
						<xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="url"/></xsl:attribute>
						<xsl:attribute name="title"><xsl:value-of select="description"/></xsl:attribute>
						<xsl:value-of select="title"/>
					</a></li>
				</xsl:for-each>
			</ul>
		</xsl:when>
		<xsl:otherwise>
			<div>
				<xsl:attribute name="class">columncount<xsl:value-of select="$columns"/></xsl:attribute>
			<ul>
			<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='presentation' and @display='PERSISTANT']/link[((position() mod $columns)-1) = 0]">
			<xsl:choose>
				<xsl:when test="@clickable='0'">
					<li><xsl:value-of select="title"/></li>
				</xsl:when>
				<xsl:otherwise>
				<li><a>
					<xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="url"/></xsl:attribute>
					<xsl:attribute name="title"><xsl:value-of select="description"/></xsl:attribute>
					<xsl:value-of select="title"/>
				</a></li>
				</xsl:otherwise>
			</xsl:choose>
			</xsl:for-each>
			</ul>
			</div>
			<xsl:choose>
				<xsl:when test="$columns=2">
			<div>
				<xsl:attribute name="class">columncount<xsl:value-of select="$columns"/></xsl:attribute>
					<ul>
					<xsl:attribute name="href">columncount<xsl:value-of select="$columns"/></xsl:attribute>
						<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='presentation' and @display='PERSISTANT']/link[((position() mod $columns)-1) = -1]">
						<xsl:choose>
							<xsl:when test="@clickable='0'">
								<li><xsl:value-of select="title"/></li>
							</xsl:when>
							<xsl:otherwise>
							<li><a>
								<xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="url"/></xsl:attribute>
								<xsl:attribute name="title"><xsl:value-of select="description"/></xsl:attribute>
								<xsl:value-of select="title"/>
							</a></li>
							</xsl:otherwise>
						</xsl:choose>
						</xsl:for-each>
					</ul>
			</div>
				</xsl:when>
				<xsl:otherwise>
			<div>
				<xsl:attribute name="class">columncount<xsl:value-of select="$columns"/></xsl:attribute>
					<ul>
					<xsl:attribute name="href">columncount<xsl:value-of select="$columns"/></xsl:attribute>
						<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='presentation' and @display='PERSISTANT']/link[((position() mod $columns)-1) = 1]">
						<xsl:choose>
							<xsl:when test="@clickable='0'">
								<li><xsl:value-of select="title"/></li>
							</xsl:when>
							<xsl:otherwise>
							<li><a>
								<xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="url"/></xsl:attribute>
								<xsl:attribute name="title"><xsl:value-of select="description"/></xsl:attribute>
								<xsl:value-of select="title"/>
							</a></li>
							</xsl:otherwise>
						</xsl:choose>
						</xsl:for-each>
					</ul>
			</div>
			<div>
				<xsl:attribute name="class">columncount<xsl:value-of select="$columns"/></xsl:attribute>
					<ul>
						<xsl:for-each select="//xml_document/modules/container/webobject/module[@name='presentation' and @display='PERSISTANT']/link[((position() mod $columns)-1) = -1]">
							<xsl:choose>
								<xsl:when test="@clickable='0'">
									<li><xsl:value-of select="title"/></li>
								</xsl:when>
								<xsl:otherwise>
								<li><a>
									<xsl:attribute name="href"><xsl:value-of select="//setting[@name='base']"/><xsl:value-of select="//setting[@name='fake_script']"/>/<xsl:value-of select="url"/></xsl:attribute>
									<xsl:attribute name="title"><xsl:value-of select="description"/></xsl:attribute>
									<xsl:value-of select="title"/>
								</a></li>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
					</ul>
			</div>
				</xsl:otherwise>
			</xsl:choose>
		</xsl:otherwise>
	</xsl:choose>
</xsl:if>
<xsl:for-each select="//xml_document/modules/container/webobject/module/page[position()!=$title_page][boolean(content)]">
	<div style="clear:both" class='columncount1'>
		<xsl:call-template name="display_this_page">
			<xsl:with-param name="title"><xsl:value-of select="$show_title_page_title"/></xsl:with-param>
			<xsl:with-param name="alt_title">1</xsl:with-param>
			<xsl:with-param name="content">1</xsl:with-param>
			<xsl:with-param name="date_publish">0</xsl:with-param>
			<xsl:with-param name="more">0</xsl:with-param>
			<xsl:with-param name="style">LOCATION</xsl:with-param>
			<xsl:with-param name="identifier"><xsl:value-of select="@identifier"/></xsl:with-param>
		</xsl:call-template>
	</div>
</xsl:for-each>

	<xsl:if test="//xml_document/modules/container/webobject/module/headline">
		<xsl:call-template name='show_headlines'>
			<xsl:with-param name='cols'>3</xsl:with-param>
		</xsl:call-template>
	</xsl:if>
</xsl:template>



</xsl:stylesheet>

