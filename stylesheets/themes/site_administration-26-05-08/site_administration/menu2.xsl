<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:50:03 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 


<xsl:template name="display_admin_menu">
<script>
var asTabs = new Array();
	asTabs[0] = Array("HOME",Array());
<xsl:for-each select="grouping">
	<xsl:sort select="@name" order="ascending"/>
	<xsl:sort select="mod/@label" order="ascending"/>
	p = asTabs.length
	asTabs[p]=Array();
	asTabs[p][0] = "<xsl:value-of select="@name"/>";
	asTabs[p][1] = Array(<xsl:for-each select="mod/options/option">"<xsl:value-of select="../../../@name"/>-<xsl:value-of select="../../@name"/>",</xsl:for-each>'#end#')
</xsl:for-each>

var intNumTabs = asTabs.length;

</script>
<ul id="mainmenu">
<li class="mainmenuitem inactive"><xsl:attribute name="id">menuitem-HOME</xsl:attribute>
		<a href="admin/index.php#" onBlur="doMenuOff();" onMouseOut="doMenuOff();">
		<xsl:attribute name="onMouseOver">doMenuOn('HOME');</xsl:attribute>
		<xsl:attribute name="onFocus">doMenuOn('HOME');</xsl:attribute>
		Home</a>
		<br/>
		<ul class="submenu" onMouseOver="doStopTime();" onMouseOut="doStartTime();" id="submenu-HOME">
			<li class="subsubmenuitem"><a onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">
				<xsl:attribute name="href">admin/index.php?<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/></xsl:attribute> 
				Digital Desktop</a></li>
			<li class="subsubmenuitem"><a onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">
				<xsl:attribute name="href">admin/index.php</xsl:attribute> 
				Digital Desktop</a></li>
			<li class="subsubmenuitem"><hr/></li>
			<li class="subsubmenuitem"><a onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">
				<xsl:attribute name="href">admin/index.php?command=ENGINE_LOGOUT</xsl:attribute> 
				Exit (Logout)</a></li>
		</ul>
	</li>
<xsl:for-each select="grouping">
	<xsl:sort select="@name" order="ascending"/>
	<xsl:sort select="mod/@label" order="ascending"/>
	<li class="mainmenuitem inactive"><xsl:attribute name="id">menuitem-<xsl:value-of select="@name"/></xsl:attribute>
		<a href="admin/index.php#" onBlur="doMenuOff();" onMouseOut="doMenuOff();">
		<xsl:attribute name="onMouseOver">doMenuOn('<xsl:value-of select="@name"/>');</xsl:attribute>
		<xsl:attribute name="onFocus">doMenuOn('<xsl:value-of select="@name"/>');</xsl:attribute>
		<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@name"/></xsl:call-template></a>
		<br/>
		<xsl:if test="mod">
			<ul class="submenu" onMouseOver="doStopTime();" onMouseOut="doStartTime();"><xsl:attribute name="id">submenu-<xsl:value-of select="@name"/></xsl:attribute>
				<xsl:for-each select="mod">
					<xsl:choose>
						<xsl:when test="count(options/option)=1">
							<li class="subsubmenuitem"><a onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">
							<xsl:attribute name="href">admin/index.php?command=<xsl:value-of select="options/option[position()=1]/@value"/>&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/></xsl:attribute>
							<xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></a></li>
						</xsl:when>
						<xsl:otherwise>
							<li ><a>
<!--
								<xsl:attribute name="onMouseOver">doMenuOn('<xsl:value-of select="../@name"/>-<xsl:value-of select="@name"/>')</xsl:attribute>
								<xsl:attribute name="onFocus">doMenuOn('<xsl:value-of select="../@name"/>-<xsl:value-of select="@name"/>')</xsl:attribute>
-->
								<xsl:attribute name="href">?command=<xsl:value-of select="options/option[position()=1]/@value"/>&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/></xsl:attribute>
								<xsl:attribute name="id">menuitem-<xsl:value-of select="../@name"/>-<xsl:value-of select="@name"/></xsl:attribute>
								<xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></a><br/>
							<ul  onMouseOver="doStopTime();" onMouseOut="doStartTime();"><xsl:attribute name="id">submenu-<xsl:value-of select="../@name"/>-<xsl:value-of select="@name"/></xsl:attribute>
							<xsl:for-each select="options/option">
								<li><a onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">
									<xsl:attribute name="href">admin/index.php?command=<xsl:value-of select="@value"/>&amp;<xsl:value-of select="//session/@name"/>=<xsl:value-of select="//session/@session_identifier"/></xsl:attribute>
									<xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></a></li>
							</xsl:for-each>
							</ul></li>
						</xsl:otherwise>
					</xsl:choose>

				</xsl:for-each>
			</ul>
		</xsl:if>
	</li>
</xsl:for-each>
<script type="text/javascript" language="JavaScript1.2" src="/libertas_images/javascripts/div_menu_common.js"></script>
<!--
	<li class="mainmenuitem inactive" id="menuitem-solutions"><a href="http://www.watchfire.com/solutions/default.aspx" onFocus="doMenuOn('solutions');" onMouseOver="doMenuOn('solutions');" onBlur="doMenuOff();" onMouseOut="doMenuOff();">Solutions</a><br/>
	<li class="submenuitem"><a href="http://www.watchfire.com/solutions/default.aspx" onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">Online Business Management</a>
		<ul class="subsubmenu">
			<li class="subsubmenuitem"><a href="http://www.watchfire.com/solutions/online-brand.aspx" onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">Online Brand Management</a></li>
			<li class="subsubmenuitem last"><a href="http://www.watchfire.com/solutions/online-risk.aspx" onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">Online Risk Management</a></li>
		</ul></li>
		<li class="submenuitem"><a href="http://www.watchfire.com/solutions/roles.aspx" onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">By Role:</a>
		<ul class="subsubmenu">
			<li class="subsubmenuitem"><a href="http://www.watchfire.com/solutions/online-brand.aspx" onFocus="doSubMenuOn();" onBlur="doSubMenuOff();"><em>e</em>Business / Marketing</a></li>
			<li class="subsubmenuitem last"><a href="http://www.watchfire.com/solutions/online-risk.aspx" onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">Compliance / Legal</a></li>
		</ul></li>
		<li class="submenuitem"><span class="menunolink">By Vertical:</span>
		<ul class="subsubmenu">
			<li class="subsubmenuitem"><a href="http://www.watchfire.com/solutions/gov/default.aspx" onFocus="doSubMenuOn();" onBlur="doSubMenuOff();">Government</a></li>
		</ul></li>
	</ul>
-->
</ul>
</xsl:template>


</xsl:stylesheet>