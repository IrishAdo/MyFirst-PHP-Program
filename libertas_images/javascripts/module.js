/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- GENERAL FUNCTIONS.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
function applyfilter(page,form_name){
	eval("document."+form_name+".page.value="+page+";");
	eval("document."+form_name+".submit();");
}

function shop_stock_image_change(t){
	eval("my_element = document.all."+t);
//	alert(my_element.options[my_element.selectedIndex].value);
	list = new String(my_element.options[my_element.selectedIndex].value).split("::");
	out ="<table >";
	out+="	<tr>";
	out+="		<td valign=top><img src='"+list[0]+"' width='160' height='120'/></td>";
	out+="		<td valign=top><strong>Width</strong> : "+list[1]+"<br/>";
	out+="			<strong>Height</strong> : "+list[2]+"<br/>";
	out+="			<strong>File Size</strong> : "+list[4]+"<br/>";
	out+="			<strong>Description</strong> :<br/> "+list[3]+"<br/>";
	out+="		</td>";
	out+="	</tr>";
	out+="</table>";
//	html_output_<xsl:value-of select="@name"/>.innerHTML = out;
	eval("html_output_"+t+".innerHTML = out");
	
}



