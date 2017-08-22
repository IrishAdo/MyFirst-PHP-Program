<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.2 $
- Modified $Date: 2004/09/06 16:49:59 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template match="lookups"><td>
	<input type='hidden' name='lookup_todo' value=''/>
	<input type='hidden' name='lookup_id' value=''/>
	<span id='lookup_form_screen'></span>
	<br/>
	<span id="updates"></span>
	<script>
<xsl:comment>
	screen1 = "&lt;TABLE>&lt;TR>&lt;TD colspan='2' valign='top'>Don't forget to save your changes when you are finished.&lt;/TD>&lt;/TR>&lt;TR>&lt;TD valign='top'>&lt;LABEL for='lookup_table'>Choose a Lookup table&lt;BR/>to modify&lt;/LABEL>&lt;/TD>&lt;TD>&lt;SELECT id='lookup_table' name='lookup_table' style='width=250px' onChange='javascript:generate_results();'>&lt;/SELECT>&lt;/TD>&lt;/TR>&lt;TR>&lt;TD valign='top'>&lt;LABEL for='lookup_result'>You can modify or remove&lt;BR/>these existing entries&lt;/LABEL>&lt;/TD>&lt;TD>&lt;SELECT name='lookup_result' size='15' style='width=250px' onChange='javascript:select_entry();'>&lt;/SELECT>&lt;/TD>&lt;/TR>&lt;TR>&lt;TD valign='top'>&lt;LABEL for='lookup_editor'>Edit this value&lt;/LABEL>&lt;/TD>&lt;TD>&lt;INPUT type='text' name='lookup_editor' maxlength='255' style='width=250px' onfocus='javascript:set_options();'/>&lt;/TD>&lt;/TR>&lt;TR>&lt;TD valign='top' align='right' colspan='2'>&lt;SPAN id='model_button'>&lt;/SPAN>&lt;INPUT name='btn_sub' id='btn_sub' type='button' onclick='javascript:update_entry();' value='Insert'/>&lt;SPAN id='remove_button'>&lt;/SPAN>[[nbsp]]&lt;INPUT id='btn_add' type='button' onclick='javascript:frm_reset();' value='New'/>&lt;/TD>&lt;/TR>&lt;/TABLE>";
	screen2 = "&lt;TABLE>&lt;TR>&lt;TD colspan='2' valign='top'>Don't forget to save your changes when you are finished.&lt;/TD>&lt;/TR>&lt;TR>&lt;TD valign='top'>&lt;LABEL for='lookup_table'>Choose a Manufacturer&lt;BR/>to modify&lt;/LABEL>&lt;/TD>&lt;TD>&lt;SELECT id='lookup_table' name='lookup_manufacturer' style='width=250px' onChange='javascript:generate_models();'>&lt;/SELECT>&lt;/TD>&lt;/TR>&lt;TR>&lt;TD valign='top'>&lt;LABEL for='lookup_models'>You can modify or remove&lt;BR/>these existing entries&lt;/LABEL>&lt;/TD>&lt;TD>&lt;SELECT name='lookup_result' size='15' style='width=250px' onChange='javascript:select_model();'>&lt;/SELECT>&lt;/TD>&lt;/TR>&lt;TR>&lt;TD valign='top'>&lt;LABEL for='lookup_editor'>Edit this value&lt;/LABEL>&lt;/TD>&lt;TD>&lt;INPUT type='text' name='lookup_editor' maxlength='255' style='width=250px' onfocus='javascript:set_options();'/>&lt;/TD>&lt;/TR>&lt;TR>&lt;TD valign='top' align='right' colspan='2'>&lt;INPUT name='btn_back' id='btn_back' type='button' onclick='javascript:back_to_all();' value='Back'/>[[nbsp]]&lt;INPUT name='btn_sub' id='btn_sub' type='button' onclick='javascript:update_model();' value='Insert'/>&lt;SPAN id='remove_button'>&lt;/SPAN>[[nbsp]]&lt;INPUT id='btn_add' type='button' onclick='javascript:frm_reset();' value='New'/>&lt;/TD>&lt;/TR>&lt;/TABLE>";
	document.all.lookup_form_screen.innerHTML = screen1;
	var mytodolist=Array();
	var item_counter = 0;
	var frm = 	document.<xsl:value-of select="../@name"/>;
	var lookups = Array(<xsl:for-each select="lookup"><xsl:if test="position()!=1">,</xsl:if>
					Array('<xsl:value-of select="@name"/>',
						Array(<xsl:for-each select="entry"><xsl:if test="position()!=1">,</xsl:if>
							Array(<xsl:value-of select="@value"/>,'<xsl:value-of select="."/>'<xsl:if test="@manufacturer!=''">,<xsl:value-of select="@manufacturer"/></xsl:if>)
						</xsl:for-each>)
						)
	</xsl:for-each>)
	len = lookups.length;
	frm.lookup_table.options.length=0;
	for(i=0;i &lt; (len-1) ;i++){
		frm.lookup_table.options[i] = new Option(lookups[i][0],lookups[i][0]);
	}
	frm.lookup_table.selectedindex=0;
	generate_results();
	
	function remove_from_list(){
		if (frm.lookup_id.value!=''){
			mytodolist[mytodolist.length] = frm.lookup_table.options[frm.lookup_table.options.selectedIndex].value+"::"+frm.lookup_id.value+"::"+frm.lookup_editor.value+"::REMOVE";
			for (i=0; i &lt; lookups.length ; i++){
				if (lookups[i][0]==frm.lookup_table.options[frm.lookup_table.options.selectedIndex].value){
					for (index=0; index &lt; lookups[i][1].length ; index++){
						if (lookups[i][1][index][0]==frm.lookup_id.value){
							lookups[i][1][index] = Array('_IGNORE_','_IGNORE_');
						}
					}
				}
			}
			frm_reset();
			generate_results();
		} else {
		}
	}	
	
	function set_options(){
	
	}
	
	function insert_into_list(){
		if (frm.lookup_id.value==''){
			if (frm.lookup_editor.value!=''){
				frm.lookup_id.value = (mytodolist.length+1) *-1;
				index=0;
				len = lookups.length;
				for(i=0;len > i;i++){
					if (lookups[i][0]==frm.lookup_table.options[frm.lookup_table.options.selectedIndex].value){
						index = lookups[i][1].length
						item_counter =(mytodolist.length+1);
						lookups[i][1][index] = Array(((item_counter)*-1),frm.lookup_editor.value);
						mytodolist[mytodolist.length] = frm.lookup_table.options[frm.lookup_table.options.selectedIndex].value+"::"+((item_counter)*-1)+"::"+frm.lookup_editor.value+"::ADD";
					}
				}
				frm_reset();
				generate_results();
			}
		} else {
			if (frm.lookup_editor.value!=''){
				if (confirm("Are you sure you wish to lose your changes?")){
					frm_reset();
				}
			}
		}
	}

	function update_entry(){
		if (frm.lookup_id.value!=''){
			mytodolist[mytodolist.length] = frm.lookup_table.options[frm.lookup_table.options.selectedIndex].value+"::"+frm.lookup_id.value+"::"+frm.lookup_editor.value+"::EDIT";
			index=0;
			len = lookups.length;
			for(i=0;len > i;i++){
				if (lookups[i][0]==frm.lookup_table.options[frm.lookup_table.options.selectedIndex].value){
					index 				= lookups[i][1].length
					for(p=0; index > p ; p++){
						if (lookups[i][1][p][0] == frm.lookup_id.value){
							lookups[i][1][p][1] = frm.lookup_editor.value;
						}
					}
				}
			}
			frm_reset();
		}else{
			insert_into_list();
		}
		generate_results();
	}

	function generate_results(){
		len = lookups.length;
		for(i=0;len > i;i++){
			if (lookups[i][0]==frm.lookup_table.options[frm.lookup_table.options.selectedIndex].value){
				index=i;
			}
		}
		frm.lookup_result.options.length=0
		len = lookups[index][1].length;
		for(i=0;len > i;i++){
		if (lookups[index][1][i][1]!='_IGNORE_'){
			frm.lookup_result.options[frm.lookup_result.options.length] = new Option(lookups[index][1][i][1], lookups[index][1][i][0]);
			}
		}
		len = mytodolist.length;
		str='';
		for(i=0;len > i;i++){
			str += mytodolist[i]+"\n";
		}
		frm.lookup_todo.value=str;
		//document.all.updates.innerHTML = str;
	}

	function select_entry(){
		frm.lookup_id.value = frm.lookup_result.options[frm.lookup_result.options.selectedIndex].value;
		frm.lookup_editor.value = frm.lookup_result.options[frm.lookup_result.options.selectedIndex].text;
		document.all.remove_button.innerHTML = "[[nbsp]]&lt;input id='btn_rem' type='button' onclick='javascript:remove_from_list();' value='Remove'/>";
		frm.btn_sub.value='Update';
		if (frm.lookup_table.options[frm.lookup_table.options.selectedIndex].value=="manufacturer"){
			str= "&lt;input name='btn_sub' id='btn_mod' type='button' disable='true' onclick='javascript:edit_models();' value='Edit Models'/>[[nbsp]]";
			document.all.model_button.innerHTML = str
		} else {
			document.all.model_button.innerHTML = "";
		}
	}
	
	function frm_reset(){
		frm.btn_sub.value='Insert';
		frm.lookup_editor.value='';
		document.all.remove_button.innerHTML = "";
		frm.lookup_id.value='';
	}
	
	function edit_models(){
		index = frm.lookup_result.options.selectedIndex;
		document.all.lookup_form_screen.innerHTML = screen2;
		len = lookups.length;
		frm.lookup_result.options.length=0
		for(i=0;len > i;i++){
			if (lookups[i][0]=="manufacturer"){
				leng =lookups[i][1].length;
				for(z=0;leng > z;z++){
					frm.lookup_manufacturer.options[frm.lookup_manufacturer.options.length]= new Option(lookups[i][1][z][1], lookups[i][1][z][0]);
					if (index==z){
						frm.lookup_manufacturer.options[frm.lookup_manufacturer.options.length-1].selected=true;
					}
				}
			}
		}
		frm_reset();
		generate_models();
	}
	function back_to_all(){
		document.all.lookup_form_screen.innerHTML = screen1;
		len = lookups.length;
		frm.lookup_table.options.length=0;
		for(i=0;i &lt; len;i++){
			if (lookups[i][0]!='model')
			frm.lookup_table.options[i] = new Option(lookups[i][0],lookups[i][0]);
		}
		frm.lookup_table.selectedindex=0;
		generate_results();	
	}

	function insert_into_model(){
		if (frm.lookup_id.value==''){
			if (frm.lookup_editor.value!=''){

				frm.lookup_id.value = (mytodolist.length+1) *-1;
				mytodolist[mytodolist.length] = "model::"+frm.lookup_manufacturer.options[frm.lookup_manufacturer.options.selectedIndex].value+"::"+frm.lookup_id.value+"::"+frm.lookup_editor.value+"::ADD";
				index=0;
				len = lookups.length;
				for(i=0;len > i;i++){
					if (lookups[i][0]=="model"){
						index = lookups[i][1].length
						lookups[i][1][index] = Array((index*-1),frm.lookup_editor.value, frm.lookup_manufacturer.options[frm.lookup_manufacturer.options.selectedIndex].value);

//			((item_counter)*-1)
					}
				}
				frm_reset();
				generate_models();
			}
		} else {
			if (frm.lookup_editor.value!=''){
				if (confirm("Are you sure you wish to lose your changes?")){
					frm_reset();
				}
			}
		}
	}

	function update_model(){
		if (frm.lookup_id.value!=''){
			mytodolist[mytodolist.length] = "model::"+frm.lookup_manufacturer.options[frm.lookup_manufacturer.options.selectedIndex].value+"::"+frm.lookup_id.value+"::"+frm.lookup_editor.value+"::EDIT";
			index=0;
			len = lookups.length;
			for(i=0;len > i;i++){
				if (lookups[i][0]=="model"){
					index 				= lookups[i][1].length
					for(p=0; index > p ; p++){
						if (lookups[i][1][p][0] == frm.lookup_id.value){
							lookups[i][1][p][1] = frm.lookup_editor.value;
						}
					}
				}
			}
			frm_reset();
		}else{
			insert_into_model();
		}
		generate_models();
	}
	function generate_models(){
		len = lookups.length;
		for(i=0;len > i;i++){
			if (lookups[i][0]=="model"){
				index=i;
			}
		}
		frm.lookup_result.options.length=0
		len = lookups[index][1].length;
		for(i=0;len > i;i++){
			if (lookups[index][1][i][2]==frm.lookup_manufacturer.options[frm.lookup_manufacturer.options.selectedIndex].value){
				frm.lookup_result.options[frm.lookup_result.options.length] = new Option(lookups[index][1][i][1], lookups[index][1][i][0]);
			}
		}
		len = mytodolist.length;
		str='';
		for(i=0;len > i;i++){
			str += mytodolist[i]+"\n";
		}
		frm.lookup_todo.value=str;
		//document.all.updates.innerHTML = str;
	}

	function select_model(){
		frm.lookup_id.value = frm.lookup_result.options[frm.lookup_result.options.selectedIndex].value;
		frm.lookup_editor.value = frm.lookup_result.options[frm.lookup_result.options.selectedIndex].text;
		document.all.remove_button.innerHTML = "[[nbsp]]&lt;input id='btn_rem' type='button' onclick='javascript:remove_from_list();' value='Remove'/>";
		frm.btn_sub.value='Update';
	}

</xsl:comment>
</script>
</td></xsl:template>
</xsl:stylesheet>

