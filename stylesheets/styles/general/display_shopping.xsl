<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.9 $
- Modified $Date: 2005/02/05 12:19:09 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->

<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template match="stock_list">
<table class="sortable" border="0" cellpadding="3" cellspacing="0" width="100%" summary="This table holds the user information">
	<tr class="tableheader">
		<th>Item</th>
		<th>Price</th>
		<xsl:if test="stock[@discount!='0']">
		<th>Discount</th>
		</xsl:if>
		
		<th>Quantity</th>
		<th>Total [[<xsl:value-of select="currency"/>]]</th>
		<xsl:if test="stock[@item_vat!='0']">
		<th>VAT</th>
		</xsl:if>
		<th>[[nbsp]]</th>
	</tr>


	
	<xsl:for-each select="stock">
	<tr>
		<td><xsl:value-of select="."/></td>
		<td><xsl:choose>
			<xsl:when test="@price!=0">[[<xsl:value-of select="../currency"/>]] <xsl:value-of select="@price"/></xsl:when>
			<xsl:otherwise>Free</xsl:otherwise>
		</xsl:choose></td>
		<xsl:if test="stock[@discount!='0']">
		<td><xsl:value-of select="@discount"/></td>
		</xsl:if>
		<td><input type='hidden'>
				<xsl:attribute name='name'>old_quantity<xsl:value-of select="position()"/></xsl:attribute>
				<xsl:attribute name='value'><xsl:value-of select="@quantity"/></xsl:attribute>
			</input><input type='hidden'>
				<xsl:attribute name='name'>item<xsl:value-of select="position()"/></xsl:attribute>
				<xsl:attribute name='value'><xsl:value-of select="@identifier"/></xsl:attribute>
			</input><input type='text' size='5'>
				<xsl:attribute name='name'>new_quantity<xsl:value-of select="position()"/></xsl:attribute>
				<xsl:attribute name='value'><xsl:value-of select="@quantity"/></xsl:attribute>
			</input></td>
		<td><xsl:choose>
			<xsl:when test="@item_total_double!=0">[[<xsl:value-of select="../currency"/>]] <xsl:value-of select="@item_total"/></xsl:when>
			<xsl:otherwise>Free</xsl:otherwise>
		</xsl:choose></td>
		<xsl:if test="../stock[@item_vat!='0']">
		<td><xsl:choose>
			<xsl:when test="@item_vat!=0">[[<xsl:value-of select="../currency"/>]] <xsl:value-of select="@item_vat"/></xsl:when>
			<xsl:otherwise>Free</xsl:otherwise>
		</xsl:choose></td>
		</xsl:if>
		<td>
		<a><xsl:attribute name='href'>_view-cart.php?command=SHOP_REMOVE_FROM_BASKET&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute>Remove</a>
		<!--a><xsl:attribute name='href'>_view-cart.php?command=SHOP_REMOVE_FROM_BASKET&amp;identifier=<xsl:value-of select="@identifier"/></xsl:attribute><img alt="Remove Item" border="0"><xsl:attribute name="src"><xsl:value-of select="$image_path"/>/button_REMOVE.gif</xsl:attribute></img></a-->
	</td>
	</tr>
	</xsl:for-each>

	<xsl:if test="@vat!='0' and @vat!=''">
	<tr class='ignore'>
		<td colspan="4" align="right" class='shoplabels'>Sub-total</td>
		<td>[[<xsl:value-of select="currency"/>]] <xsl:value-of select="@subtotal"/></td>
		<td></td>
	</tr>
	<tr class='ignore'>
		<td colspan="4" align="right" class='shoplabels'>Sales Tax (VAT)[[nbsp]]<xsl:value-of select="@vat"/>%</td>
		<td>[[<xsl:value-of select="currency"/>]] <xsl:value-of select="sum(stock/@item_vat)"/></td>
		<td></td>
	</tr>
	</xsl:if>
	<tr class='ignore'>
		<td colspan="4" align="right" class='shoplabels'>Total</td>
		<td><xsl:choose>
			<xsl:when test="@total_double!=0">[[<xsl:value-of select="currency"/>]] <xsl:value-of select="@total"/></xsl:when>
			<xsl:otherwise>Free</xsl:otherwise>
		</xsl:choose></td>
		<td></td>
	</tr>
	</table>
	<div class="alignright"> 
		<input type='hidden' name='command' value='SHOP_UPDATE_BASKET'/>
		<input id='shopbuttonupdate' class="button" type='submit' name='button' value='Update'></input>
		<input id='shopbuttonpruchase' class="button" type='submit' name='button' value='Purchase'></input>
	</div>
	<input type='hidden' name='number_of_items'><xsl:attribute name='value'><xsl:value-of select="@number_of_items"/></xsl:attribute></input>
</xsl:template>

</xsl:stylesheet>