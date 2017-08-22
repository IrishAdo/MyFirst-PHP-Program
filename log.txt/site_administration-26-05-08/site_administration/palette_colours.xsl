<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:50:05 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template match="colours">
		<input type='hidden'><xsl:attribute name='name'>colour_count</xsl:attribute>
			<xsl:attribute name='value'><xsl:value-of select="count(colour)"/></xsl:attribute>
		</input>
	<xsl:for-each select="colour">
		<input type='hidden'>
			<xsl:attribute name='id'>colour_<xsl:value-of select="position()"/></xsl:attribute>
			<xsl:attribute name='name'>colour_<xsl:value-of select="position()"/></xsl:attribute>
			<xsl:attribute name='value'><xsl:value-of select="@value"/></xsl:attribute>
		</input>
	</xsl:for-each>
	<table><tr><td valign="top">
	Your Palette::
	<table border="1" cellspacing="3" bgcolor="#cccccc">
	<tr><xsl:for-each select="colour">
	<xsl:if test="not (position() > 6)">
		<td><xsl:attribute name='onclick'>javascript:set_dest(<xsl:value-of select="position()"/>)</xsl:attribute><xsl:attribute name='id'>dest_<xsl:value-of select="position()"/></xsl:attribute><xsl:attribute name='bgcolor'><xsl:value-of select="@value"/></xsl:attribute><img src='/libertas_images/themes/1x1.gif' width='20' height='20' class='img_pick'/></td>
	</xsl:if>
	</xsl:for-each>
	</tr>
	<tr ><xsl:for-each select="colour">
	<xsl:if test="position() > 6">
		<td><xsl:attribute name='onclick'>javascript:set_dest(<xsl:value-of select="position()"/>)</xsl:attribute><xsl:attribute name='id'>dest_<xsl:value-of select="position()"/></xsl:attribute><xsl:attribute name='bgcolor'><xsl:value-of select="@value"/></xsl:attribute><img src='/libertas_images/themes/1x1.gif' width='20' height='20' class='img_pick'/></td>
	</xsl:if>
	</xsl:for-each>
	</tr>
	</table><br/>
	Sample::
	<table border="1" bgcolor="#cccccc"><tr><td name="sample" id="sample" bgcolor="#ffffff"><img src="/libertas_images/themes/1x1.gif" width="190" height="140" border="0"/></td></tr></table> 
	
	</td><td valign="top">
	Options to choose from ::
	<script>
		var destinition =0;
		set_dest(1);
		function set_dest(val){
			destinition = val;
			for(i=1; i != 13; i++){
				eval("document.all['dest_"+i+"'].className = 'img_pick_black';");
			}
			eval("document.all['dest_"+destinition+"'].className = 'img_pick_over';");
		}
		function imgOn(imgid){
			c = new String(imgid.id).split("img");
	    	imgid.className = 'img_pick_over';
			eval("document.all['sample'].style.background='#"+c[1]+"';");
			eval("document.all['sample'].alt='#"+c[1]+"';");
	  	}
  		function imgOff(imgid){
	    	imgid.className = 'img_pick';
	  	}
		function selColor(setcolour){
			eval("document.all['dest_"+destinition+"'].style.background='#"+setcolour+"';");
			eval("document.<xsl:value-of select="../@name"/>['colour_"+destinition+"'].value='#"+setcolour+"';");
		}
  	</script>
	<style>
		.img_pick_black{
			border : 2px solid #666666;
		}
		.img_pick {
			border : 2px solid #ffffff;
		}
		.img_pick_over {
			border : 2px outset #ff0000;
		}
	</style>
	<table border="1" cellspacing="0" cellpadding="0" id='web_palette'  bgcolor="#cccccc">
<tr>
    <td bgcolor="#000000"><img id="img000000" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('000000')" onDblClick="returnColor('000000')"/></td>
    <td bgcolor="#000000"><img id="img000000" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('000000')" onDblClick="returnColor('000000')"/></td>
    <td bgcolor="#000033"><img id="img000033" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('000033')" onDblClick="returnColor('000033')"/></td>
    <td bgcolor="#000066"><img id="img000066" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('000066')" onDblClick="returnColor('000066')"/></td>
    <td bgcolor="#000099"><img id="img000099" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('000099')" onDblClick="returnColor('000099')"/></td>
    <td bgcolor="#0000CC"><img id="img0000CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0000CC')" onDblClick="returnColor('0000CC')"/></td>
    <td bgcolor="#0000FF"><img id="img0000FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0000FF')" onDblClick="returnColor('0000FF')"/></td>
    <td bgcolor="#003300"><img id="img003300" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('003300')" onDblClick="returnColor('003300')"/></td>
    <td bgcolor="#003333"><img id="img003333" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('003333')" onDblClick="returnColor('003333')"/></td>
    <td bgcolor="#003366"><img id="img003366" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('003366')" onDblClick="returnColor('003366')"/></td>
    <td bgcolor="#003399"><img id="img003399" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('003399')" onDblClick="returnColor('003399')"/></td>
    <td bgcolor="#0033CC"><img id="img0033CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0033CC')" onDblClick="returnColor('0033CC')"/></td>
    <td bgcolor="#0033FF"><img id="img0033FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0033FF')" onDblClick="returnColor('0033FF')"/></td>
    <td bgcolor="#006600"><img id="img006600" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('006600')" onDblClick="returnColor('006600')"/></td>
    <td bgcolor="#006633"><img id="img006633" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('006633')" onDblClick="returnColor('006633')"/></td>
    <td bgcolor="#006666"><img id="img006666" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('006666')" onDblClick="returnColor('006666')"/></td>
    <td bgcolor="#006699"><img id="img006699" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('006699')" onDblClick="returnColor('006699')"/></td>
    <td bgcolor="#0066CC"><img id="img0066CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0066CC')" onDblClick="returnColor('0066CC')"/></td>
    <td bgcolor="#0066FF"><img id="img0066FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0066FF')" onDblClick="returnColor('0066FF')"/></td>
</tr><tr>
    <td bgcolor="#333333"><img id="img333333" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('333333')" onDblClick="returnColor('333333')"/></td>
    <td bgcolor="#330000"><img id="img330000" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('330000')" onDblClick="returnColor('330000')"/></td>
    <td bgcolor="#330033"><img id="img330033" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('330033')" onDblClick="returnColor('330033')"/></td>
    <td bgcolor="#330066"><img id="img330066" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('330066')" onDblClick="returnColor('330066')"/></td>
    <td bgcolor="#330099"><img id="img330099" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('330099')" onDblClick="returnColor('330099')"/></td>
    <td bgcolor="#3300CC"><img id="img3300CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('3300CC')" onDblClick="returnColor('3300CC')"/></td>
    <td bgcolor="#3300FF"><img id="img3300FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('3300FF')" onDblClick="returnColor('3300FF')"/></td>
    <td bgcolor="#333300"><img id="img333300" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('333300')" onDblClick="returnColor('333300')"/></td>
    <td bgcolor="#333333"><img id="img333333" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('333333')" onDblClick="returnColor('333333')"/></td>
    <td bgcolor="#333366"><img id="img333366" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('333366')" onDblClick="returnColor('333366')"/></td>
    <td bgcolor="#333399"><img id="img333399" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('333399')" onDblClick="returnColor('333399')"/></td>
    <td bgcolor="#3333CC"><img id="img3333CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('3333CC')" onDblClick="returnColor('3333CC')"/></td>
    <td bgcolor="#3333FF"><img id="img3333FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('3333FF')" onDblClick="returnColor('3333FF')"/></td>
    <td bgcolor="#336600"><img id="img336600" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('336600')" onDblClick="returnColor('336600')"/></td>
    <td bgcolor="#336633"><img id="img336633" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('336633')" onDblClick="returnColor('336633')"/></td>
    <td bgcolor="#336666"><img id="img336666" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('336666')" onDblClick="returnColor('336666')"/></td>
    <td bgcolor="#336699"><img id="img336699" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('336699')" onDblClick="returnColor('336699')"/></td>
    <td bgcolor="#3366CC"><img id="img3366CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('3366CC')" onDblClick="returnColor('3366CC')"/></td>
    <td bgcolor="#3366FF"><img id="img3366FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('3366FF')" onDblClick="returnColor('3366FF')"/></td>
</tr><tr>
    <td bgcolor="#666666"><img id="img666666" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('666666')" onDblClick="returnColor('666666')"/></td>
    <td bgcolor="#660000"><img id="img660000" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('660000')" onDblClick="returnColor('660000')"/></td>
    <td bgcolor="#660033"><img id="img660033" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('660033')" onDblClick="returnColor('660033')"/></td>
    <td bgcolor="#660066"><img id="img660066" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('660066')" onDblClick="returnColor('660066')"/></td>
    <td bgcolor="#660099"><img id="img660099" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('660099')" onDblClick="returnColor('660099')"/></td>
    <td bgcolor="#6600CC"><img id="img6600CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('6600CC')" onDblClick="returnColor('6600CC')"/></td>
    <td bgcolor="#6600FF"><img id="img6600FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('6600FF')" onDblClick="returnColor('6600FF')"/></td>
    <td bgcolor="#663300"><img id="img663300" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('663300')" onDblClick="returnColor('663300')"/></td>
    <td bgcolor="#663333"><img id="img663333" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('663333')" onDblClick="returnColor('663333')"/></td>
    <td bgcolor="#663366"><img id="img663366" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('663366')" onDblClick="returnColor('663366')"/></td>
    <td bgcolor="#663399"><img id="img663399" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('663399')" onDblClick="returnColor('663399')"/></td>
    <td bgcolor="#6633CC"><img id="img6633CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('6633CC')" onDblClick="returnColor('6633CC')"/></td>
    <td bgcolor="#6633FF"><img id="img6633FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('6633FF')" onDblClick="returnColor('6633FF')"/></td>
    <td bgcolor="#666600"><img id="img666600" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('666600')" onDblClick="returnColor('666600')"/></td>
    <td bgcolor="#666633"><img id="img666633" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('666633')" onDblClick="returnColor('666633')"/></td>
    <td bgcolor="#666666"><img id="img666666" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('666666')" onDblClick="returnColor('666666')"/></td>
    <td bgcolor="#666699"><img id="img666699" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('666699')" onDblClick="returnColor('666699')"/></td>
    <td bgcolor="#6666CC"><img id="img6666CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('6666CC')" onDblClick="returnColor('6666CC')"/></td>
    <td bgcolor="#6666FF"><img id="img6666FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('6666FF')" onDblClick="returnColor('6666FF')"/></td>
</tr><tr>
    <td bgcolor="#999999"><img id="img999999" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('999999')" onDblClick="returnColor('999999')"/></td>
    <td bgcolor="#990000"><img id="img990000" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('990000')" onDblClick="returnColor('990000')"/></td>
    <td bgcolor="#990033"><img id="img990033" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('990033')" onDblClick="returnColor('990033')"/></td>
    <td bgcolor="#990066"><img id="img990066" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('990066')" onDblClick="returnColor('990066')"/></td>
    <td bgcolor="#990099"><img id="img990099" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('990099')" onDblClick="returnColor('990099')"/></td>
    <td bgcolor="#9900CC"><img id="img9900CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('9900CC')" onDblClick="returnColor('9900CC')"/></td>
    <td bgcolor="#9900FF"><img id="img9900FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('9900FF')" onDblClick="returnColor('9900FF')"/></td>
    <td bgcolor="#993300"><img id="img993300" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('993300')" onDblClick="returnColor('993300')"/></td>
    <td bgcolor="#993333"><img id="img993333" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('993333')" onDblClick="returnColor('993333')"/></td>
    <td bgcolor="#993366"><img id="img993366" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('993366')" onDblClick="returnColor('993366')"/></td>
    <td bgcolor="#993399"><img id="img993399" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('993399')" onDblClick="returnColor('993399')"/></td>
    <td bgcolor="#9933CC"><img id="img9933CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('9933CC')" onDblClick="returnColor('9933CC')"/></td>
    <td bgcolor="#9933FF"><img id="img9933FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('9933FF')" onDblClick="returnColor('9933FF')"/></td>
    <td bgcolor="#996600"><img id="img996600" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('996600')" onDblClick="returnColor('996600')"/></td>
    <td bgcolor="#996633"><img id="img996633" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('996633')" onDblClick="returnColor('996633')"/></td>
    <td bgcolor="#996666"><img id="img996666" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('996666')" onDblClick="returnColor('996666')"/></td>
    <td bgcolor="#996699"><img id="img996699" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('996699')" onDblClick="returnColor('996699')"/></td>
    <td bgcolor="#9966CC"><img id="img9966CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('9966CC')" onDblClick="returnColor('9966CC')"/></td>
    <td bgcolor="#9966FF"><img id="img9966FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('9966FF')" onDblClick="returnColor('9966FF')"/></td>
</tr><tr>
    <td bgcolor="#CCCCCC"><img id="imgCCCCCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCCCCC')" onDblClick="returnColor('CCCCCC')"/></td>
    <td bgcolor="#CC0000"><img id="imgCC0000" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC0000')" onDblClick="returnColor('CC0000')"/></td>
    <td bgcolor="#CC0033"><img id="imgCC0033" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC0033')" onDblClick="returnColor('CC0033')"/></td>
    <td bgcolor="#CC0066"><img id="imgCC0066" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC0066')" onDblClick="returnColor('CC0066')"/></td>
    <td bgcolor="#CC0099"><img id="imgCC0099" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC0099')" onDblClick="returnColor('CC0099')"/></td>
    <td bgcolor="#CC00CC"><img id="imgCC00CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC00CC')" onDblClick="returnColor('CC00CC')"/></td>
    <td bgcolor="#CC00FF"><img id="imgCC00FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC00FF')" onDblClick="returnColor('CC00FF')"/></td>
    <td bgcolor="#CC3300"><img id="imgCC3300" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC3300')" onDblClick="returnColor('CC3300')"/></td>
    <td bgcolor="#CC3333"><img id="imgCC3333" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC3333')" onDblClick="returnColor('CC3333')"/></td>
    <td bgcolor="#CC3366"><img id="imgCC3366" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC3366')" onDblClick="returnColor('CC3366')"/></td>
    <td bgcolor="#CC3399"><img id="imgCC3399" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC3399')" onDblClick="returnColor('CC3399')"/></td>
    <td bgcolor="#CC33CC"><img id="imgCC33CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC33CC')" onDblClick="returnColor('CC33CC')"/></td>
    <td bgcolor="#CC33FF"><img id="imgCC33FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC33FF')" onDblClick="returnColor('CC33FF')"/></td>
    <td bgcolor="#CC6600"><img id="imgCC6600" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC6600')" onDblClick="returnColor('CC6600')"/></td>
    <td bgcolor="#CC6633"><img id="imgCC6633" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC6633')" onDblClick="returnColor('CC6633')"/></td>
    <td bgcolor="#CC6666"><img id="imgCC6666" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC6666')" onDblClick="returnColor('CC6666')"/></td>
    <td bgcolor="#CC6699"><img id="imgCC6699" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC6699')" onDblClick="returnColor('CC6699')"/></td>
    <td bgcolor="#CC66CC"><img id="imgCC66CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC66CC')" onDblClick="returnColor('CC66CC')"/></td>
    <td bgcolor="#CC66FF"><img id="imgCC66FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC66FF')" onDblClick="returnColor('CC66FF')"/></td>
</tr>
<tr>
    <td bgcolor="#FFFFFF"><img id="imgFFFFFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFFFFF')" onDblClick="returnColor('FFFFFF')"/></td>
    <td bgcolor="#FF0000"><img id="imgFF0000" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF0000')" onDblClick="returnColor('FF0000')"/></td>
    <td bgcolor="#FF0033"><img id="imgFF0033" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF0033')" onDblClick="returnColor('FF0033')"/></td>
    <td bgcolor="#FF0066"><img id="imgFF0066" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF0066')" onDblClick="returnColor('FF0066')"/></td>
    <td bgcolor="#FF0099"><img id="imgFF0099" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF0099')" onDblClick="returnColor('FF0099')"/></td>
    <td bgcolor="#FF00CC"><img id="imgFF00CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF00CC')" onDblClick="returnColor('FF00CC')"/></td>
    <td bgcolor="#FF00FF"><img id="imgFF00FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF00FF')" onDblClick="returnColor('FF00FF')"/></td>
    <td bgcolor="#FF3300"><img id="imgFF3300" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF3300')" onDblClick="returnColor('FF3300')"/></td>
    <td bgcolor="#FF3333"><img id="imgFF3333" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF3333')" onDblClick="returnColor('FF3333')"/></td>
    <td bgcolor="#FF3366"><img id="imgFF3366" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF3366')" onDblClick="returnColor('FF3366')"/></td>
    <td bgcolor="#FF3399"><img id="imgFF3399" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF3399')" onDblClick="returnColor('FF3399')"/></td>
    <td bgcolor="#FF33CC"><img id="imgFF33CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF33CC')" onDblClick="returnColor('FF33CC')"/></td>
    <td bgcolor="#FF33FF"><img id="imgFF33FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF33FF')" onDblClick="returnColor('FF33FF')"/></td>
    <td bgcolor="#FF6600"><img id="imgFF6600" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF6600')" onDblClick="returnColor('FF6600')"/></td>
    <td bgcolor="#FF6633"><img id="imgFF6633" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF6633')" onDblClick="returnColor('FF6633')"/></td>
    <td bgcolor="#FF6666"><img id="imgFF6666" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF6666')" onDblClick="returnColor('FF6666')"/></td>
    <td bgcolor="#FF6699"><img id="imgFF6699" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF6699')" onDblClick="returnColor('FF6699')"/></td>
    <td bgcolor="#FF66CC"><img id="imgFF66CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF66CC')" onDblClick="returnColor('FF66CC')"/></td>
    <td bgcolor="#FF66FF"><img id="imgFF66FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF66FF')" onDblClick="returnColor('FF66FF')"/></td>
</tr>


<tr>
    <td bgcolor="#cccccc" rowspan="6"><img id="" src="/libertas_images/themes/1x1.gif" width="15" height="15" /></td>
    <td bgcolor="#009900"><img id="img009900" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('009900')" onDblClick="returnColor('009900')"/></td>
    <td bgcolor="#009933"><img id="img009933" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('009933')" onDblClick="returnColor('009933')"/></td>
    <td bgcolor="#009966"><img id="img009966" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('009966')" onDblClick="returnColor('009966')"/></td>
    <td bgcolor="#009999"><img id="img009999" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('009999')" onDblClick="returnColor('009999')"/></td>
    <td bgcolor="#0099CC"><img id="img0099CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0099CC')" onDblClick="returnColor('0099CC')"/></td>
    <td bgcolor="#0099FF"><img id="img0099FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('0099FF')" onDblClick="returnColor('0099FF')"/></td>
    <td bgcolor="#00CC00"><img id="img00CC00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00CC00')" onDblClick="returnColor('00CC00')"/></td>
    <td bgcolor="#00CC33"><img id="img00CC33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00CC33')" onDblClick="returnColor('00CC33')"/></td>
    <td bgcolor="#00CC66"><img id="img00CC66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00CC66')" onDblClick="returnColor('00CC66')"/></td>
    <td bgcolor="#00CC99"><img id="img00CC99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00CC99')" onDblClick="returnColor('00CC99')"/></td>
    <td bgcolor="#00CCCC"><img id="img00CCCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00CCCC')" onDblClick="returnColor('00CCCC')"/></td>
    <td bgcolor="#00CCFF"><img id="img00CCFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00CCFF')" onDblClick="returnColor('00CCFF')"/></td>
    <td bgcolor="#00FF00"><img id="img00FF00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00FF00')" onDblClick="returnColor('00FF00')"/></td>
    <td bgcolor="#00FF33"><img id="img00FF33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00FF33')" onDblClick="returnColor('00FF33')"/></td>
    <td bgcolor="#00FF66"><img id="img00FF66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00FF66')" onDblClick="returnColor('00FF66')"/></td>
    <td bgcolor="#00FF99"><img id="img00FF99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00FF99')" onDblClick="returnColor('00FF99')"/></td>
    <td bgcolor="#00FFCC"><img id="img00FFCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00FFCC')" onDblClick="returnColor('00FFCC')"/></td>
    <td bgcolor="#00FFFF"><img id="img00FFFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('00FFFF')" onDblClick="returnColor('00FFFF')"/></td>
</tr>
<tr>
    <td bgcolor="#339900"><img id="img339900" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('339900')" onDblClick="returnColor('339900')"/></td>
    <td bgcolor="#339933"><img id="img339933" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('339933')" onDblClick="returnColor('339933')"/></td>
    <td bgcolor="#339966"><img id="img339966" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('339966')" onDblClick="returnColor('339966')"/></td>
    <td bgcolor="#339999"><img id="img339999" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('339999')" onDblClick="returnColor('339999')"/></td>
    <td bgcolor="#3399CC"><img id="img3399CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('3399CC')" onDblClick="returnColor('3399CC')"/></td>
    <td bgcolor="#3399FF"><img id="img3399FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('3399FF')" onDblClick="returnColor('3399FF')"/></td>
    <td bgcolor="#33CC00"><img id="img33CC00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33CC00')" onDblClick="returnColor('33CC00')"/></td>
    <td bgcolor="#33CC33"><img id="img33CC33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33CC33')" onDblClick="returnColor('33CC33')"/></td>
    <td bgcolor="#33CC66"><img id="img33CC66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33CC66')" onDblClick="returnColor('33CC66')"/></td>
    <td bgcolor="#33CC99"><img id="img33CC99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33CC99')" onDblClick="returnColor('33CC99')"/></td>
    <td bgcolor="#33CCCC"><img id="img33CCCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33CCCC')" onDblClick="returnColor('33CCCC')"/></td>
    <td bgcolor="#33CCFF"><img id="img33CCFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33CCFF')" onDblClick="returnColor('33CCFF')"/></td>
    <td bgcolor="#33FF00"><img id="img33FF00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33FF00')" onDblClick="returnColor('33FF00')"/></td>
    <td bgcolor="#33FF33"><img id="img33FF33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33FF33')" onDblClick="returnColor('33FF33')"/></td>
    <td bgcolor="#33FF66"><img id="img33FF66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33FF66')" onDblClick="returnColor('33FF66')"/></td>
    <td bgcolor="#33FF99"><img id="img33FF99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33FF99')" onDblClick="returnColor('33FF99')"/></td>
    <td bgcolor="#33FFCC"><img id="img33FFCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33FFCC')" onDblClick="returnColor('33FFCC')"/></td>
    <td bgcolor="#33FFFF"><img id="img33FFFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('33FFFF')" onDblClick="returnColor('33FFFF')"/></td>
</tr>
<tr>
    <td bgcolor="#669900"><img id="img669900" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('669900')" onDblClick="returnColor('669900')"/></td>
    <td bgcolor="#669933"><img id="img669933" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('669933')" onDblClick="returnColor('669933')"/></td>
    <td bgcolor="#669966"><img id="img669966" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('669966')" onDblClick="returnColor('669966')"/></td>
    <td bgcolor="#669999"><img id="img669999" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('669999')" onDblClick="returnColor('669999')"/></td>
    <td bgcolor="#6699CC"><img id="img6699CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('6699CC')" onDblClick="returnColor('6699CC')"/></td>
    <td bgcolor="#6699FF"><img id="img6699FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('6699FF')" onDblClick="returnColor('6699FF')"/></td>
    <td bgcolor="#66CC00"><img id="img66CC00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66CC00')" onDblClick="returnColor('66CC00')"/></td>
    <td bgcolor="#66CC33"><img id="img66CC33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66CC33')" onDblClick="returnColor('66CC33')"/></td>
    <td bgcolor="#66CC66"><img id="img66CC66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66CC66')" onDblClick="returnColor('66CC66')"/></td>
    <td bgcolor="#66CC99"><img id="img66CC99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66CC99')" onDblClick="returnColor('66CC99')"/></td>
    <td bgcolor="#66CCCC"><img id="img66CCCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66CCCC')" onDblClick="returnColor('66CCCC')"/></td>
    <td bgcolor="#66CCFF"><img id="img66CCFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66CCFF')" onDblClick="returnColor('66CCFF')"/></td>
    <td bgcolor="#66FF00"><img id="img66FF00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66FF00')" onDblClick="returnColor('66FF00')"/></td>
    <td bgcolor="#66FF33"><img id="img66FF33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66FF33')" onDblClick="returnColor('66FF33')"/></td>
    <td bgcolor="#66FF66"><img id="img66FF66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66FF66')" onDblClick="returnColor('66FF66')"/></td>
    <td bgcolor="#66FF99"><img id="img66FF99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66FF99')" onDblClick="returnColor('66FF99')"/></td>
    <td bgcolor="#66FFCC"><img id="img66FFCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66FFCC')" onDblClick="returnColor('66FFCC')"/></td>
    <td bgcolor="#66FFFF"><img id="img66FFFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('66FFFF')" onDblClick="returnColor('66FFFF')"/></td>
</tr>

<tr>
    <td bgcolor="#999900"><img id="img999900" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('999900')" onDblClick="returnColor('999900')"/></td>
    <td bgcolor="#999933"><img id="img999933" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('999933')" onDblClick="returnColor('999933')"/></td>
    <td bgcolor="#999966"><img id="img999966" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('999966')" onDblClick="returnColor('999966')"/></td>
    <td bgcolor="#999999"><img id="img999999" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('999999')" onDblClick="returnColor('999999')"/></td>
    <td bgcolor="#9999CC"><img id="img9999CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('9999CC')" onDblClick="returnColor('9999CC')"/></td>
    <td bgcolor="#9999FF"><img id="img9999FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('9999FF')" onDblClick="returnColor('9999FF')"/></td>
    <td bgcolor="#99CC00"><img id="img99CC00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99CC00')" onDblClick="returnColor('99CC00')"/></td>
    <td bgcolor="#99CC33"><img id="img99CC33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99CC33')" onDblClick="returnColor('99CC33')"/></td>
    <td bgcolor="#99CC66"><img id="img99CC66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99CC66')" onDblClick="returnColor('99CC66')"/></td>
    <td bgcolor="#99CC99"><img id="img99CC99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99CC99')" onDblClick="returnColor('99CC99')"/></td>
    <td bgcolor="#99CCCC"><img id="img99CCCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99CCCC')" onDblClick="returnColor('99CCCC')"/></td>
    <td bgcolor="#99CCFF"><img id="img99CCFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99CCFF')" onDblClick="returnColor('99CCFF')"/></td>
    <td bgcolor="#99FF00"><img id="img99FF00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99FF00')" onDblClick="returnColor('99FF00')"/></td>
    <td bgcolor="#99FF33"><img id="img99FF33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99FF33')" onDblClick="returnColor('99FF33')"/></td>
    <td bgcolor="#99FF66"><img id="img99FF66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99FF66')" onDblClick="returnColor('99FF66')"/></td>
    <td bgcolor="#99FF99"><img id="img99FF99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99FF99')" onDblClick="returnColor('99FF99')"/></td>
    <td bgcolor="#99FFCC"><img id="img99FFCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99FFCC')" onDblClick="returnColor('99FFCC')"/></td>
    <td bgcolor="#99FFFF"><img id="img99FFFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('99FFFF')" onDblClick="returnColor('99FFFF')"/></td>
</tr>
<tr>
    <td bgcolor="#CC9900"><img id="imgCC9900" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC9900')" onDblClick="returnColor('CC9900')"/></td>
    <td bgcolor="#CC9933"><img id="imgCC9933" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC9933')" onDblClick="returnColor('CC9933')"/></td>
    <td bgcolor="#CC9966"><img id="imgCC9966" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC9966')" onDblClick="returnColor('CC9966')"/></td>
    <td bgcolor="#CC9999"><img id="imgCC9999" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC9999')" onDblClick="returnColor('CC9999')"/></td>
    <td bgcolor="#CC99CC"><img id="imgCC99CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC99CC')" onDblClick="returnColor('CC99CC')"/></td>
    <td bgcolor="#CC99FF"><img id="imgCC99FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CC99FF')" onDblClick="returnColor('CC99FF')"/></td>
    <td bgcolor="#CCCC00"><img id="imgCCCC00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCCC00')" onDblClick="returnColor('CCCC00')"/></td>
    <td bgcolor="#CCCC33"><img id="imgCCCC33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCCC33')" onDblClick="returnColor('CCCC33')"/></td>
    <td bgcolor="#CCCC66"><img id="imgCCCC66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCCC66')" onDblClick="returnColor('CCCC66')"/></td>
    <td bgcolor="#CCCC99"><img id="imgCCCC99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCCC99')" onDblClick="returnColor('CCCC99')"/></td>
    <td bgcolor="#CCCCCC"><img id="imgCCCCCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCCCCC')" onDblClick="returnColor('CCCCCC')"/></td>
    <td bgcolor="#CCCCFF"><img id="imgCCCCFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCCCFF')" onDblClick="returnColor('CCCCFF')"/></td>
    <td bgcolor="#CCFF00"><img id="imgCCFF00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCFF00')" onDblClick="returnColor('CCFF00')"/></td>
    <td bgcolor="#CCFF33"><img id="imgCCFF33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCFF33')" onDblClick="returnColor('CCFF33')"/></td>
    <td bgcolor="#CCFF66"><img id="imgCCFF66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCFF66')" onDblClick="returnColor('CCFF66')"/></td>
    <td bgcolor="#CCFF99"><img id="imgCCFF99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCFF99')" onDblClick="returnColor('CCFF99')"/></td>
    <td bgcolor="#CCFFCC"><img id="imgCCFFCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCFFCC')" onDblClick="returnColor('CCFFCC')"/></td>
    <td bgcolor="#CCFFFF"><img id="imgCCFFFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('CCFFFF')" onDblClick="returnColor('CCFFFF')"/></td>
</tr>

<tr>
    <td bgcolor="#FF9900"><img id="imgFF9900" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF9900')" onDblClick="returnColor('FF9900')"/></td>
    <td bgcolor="#FF9933"><img id="imgFF9933" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF9933')" onDblClick="returnColor('FF9933')"/></td>
    <td bgcolor="#FF9966"><img id="imgFF9966" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF9966')" onDblClick="returnColor('FF9966')"/></td>
    <td bgcolor="#FF9999"><img id="imgFF9999" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF9999')" onDblClick="returnColor('FF9999')"/></td>
    <td bgcolor="#FF99CC"><img id="imgFF99CC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF99CC')" onDblClick="returnColor('FF99CC')"/></td>
    <td bgcolor="#FF99FF"><img id="imgFF99FF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FF99FF')" onDblClick="returnColor('FF99FF')"/></td>
    <td bgcolor="#FFCC00"><img id="imgFFCC00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFCC00')" onDblClick="returnColor('FFCC00')"/></td>
    <td bgcolor="#FFCC33"><img id="imgFFCC33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFCC33')" onDblClick="returnColor('FFCC33')"/></td>
    <td bgcolor="#FFCC66"><img id="imgFFCC66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFCC66')" onDblClick="returnColor('FFCC66')"/></td>
    <td bgcolor="#FFCC99"><img id="imgFFCC99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFCC99')" onDblClick="returnColor('FFCC99')"/></td>
    <td bgcolor="#FFCCCC"><img id="imgFFCCCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFCCCC')" onDblClick="returnColor('FFCCCC')"/></td>
    <td bgcolor="#FFCCFF"><img id="imgFFCCFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFCCFF')" onDblClick="returnColor('FFCCFF')"/></td>
    <td bgcolor="#FFFF00"><img id="imgFFFF00" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFFF00')" onDblClick="returnColor('FFFF00')"/></td>
    <td bgcolor="#FFFF33"><img id="imgFFFF33" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFFF33')" onDblClick="returnColor('FFFF33')"/></td>
    <td bgcolor="#FFFF66"><img id="imgFFFF66" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFFF66')" onDblClick="returnColor('FFFF66')"/></td>
    <td bgcolor="#FFFF99"><img id="imgFFFF99" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFFF99')" onDblClick="returnColor('FFFF99')"/></td>
    <td bgcolor="#FFFFCC"><img id="imgFFFFCC" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFFFCC')" onDblClick="returnColor('FFFFCC')"/></td>
    <td bgcolor="#FFFFFF"><img id="imgFFFFFF" src="/libertas_images/themes/1x1.gif" class="img_pick" width="15" height="15" onMouseOver="imgOn(this)" onMouseOut="imgOff(this)" onClick="selColor('FFFFFF')" onDblClick="returnColor('FFFFFF')"/></td>
</tr>
</table>

	</td></tr></table>
</xsl:template>
</xsl:stylesheet>

