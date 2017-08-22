<?xml version="1.0" encoding="iso-8859-1"?>
<!--
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
- Version $Revision: 1.19 $
- Modified $Date: 2005/02/28 17:28:03 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
-->
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 

<xsl:template name="displaySection">
	<xsl:param name="section_name">tab</xsl:param>
	<xsl:variable name="pos"><xsl:value-of select="position()"/></xsl:variable>
	<xsl:variable name="span_name"><xsl:value-of select="$section_name"/>_<xsl:value-of select="$pos"/></xsl:variable>
	<div><xsl:attribute name="style"><xsl:choose>
	<xsl:when test="$section_name='tab'">width:100%;visibility:hidden</xsl:when>
	<xsl:when test="@hidden='true'">width:100%;visibility:hidden</xsl:when>
	<xsl:otherwise>width:100%;</xsl:otherwise>
	</xsl:choose></xsl:attribute>
	<xsl:attribute name="id"><xsl:value-of select="$span_name"/></xsl:attribute>
	<xsl:attribute name="name"><xsl:value-of select="$section_name"/>_<xsl:value-of select="$pos"/></xsl:attribute>
	<table border="0" cellpadding="0" cellspacing="0" summary="This table holds a form" width="100%">	
		<tr><td valign="top" class="formbackground"><table border="0" cellpadding="3" cellspacing="1" width="100%" summary="This table holds the row information for the forms">
		<tr> 
		   	<td valign="top" class="formheader"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></td>
		</tr>
		<tr class="TableCell">
			<td><div><xsl:if test="@name!=''"><xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
			<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute></xsl:if>
			<xsl:if test="@hidden='1'"><xsl:attribute name="style">display:none</xsl:attribute></xsl:if>
			<table class="width100percent" ><tr><td>
			<xsl:if test="@onclick='importmapping'">
				<div id='_ifit_mapping'></div>
				<script src='/libertas_images/javascripts/module_info_dir_import.js'></script>
			</xsl:if>
			<xsl:for-each select="child::*">
			<xsl:choose>
				<xsl:when test="local-name()=''"></xsl:when>
				<xsl:when test="local-name()='input' and @type='hidden'"></xsl:when>
				<xsl:when test="local-name()='features'">
					<div id="feature_fields">
					</div>
					<script src="/libertas_images/javascripts/module_info_featured_lists.js"></script>
					<script>
						var feature_fields = new Array();
						<xsl:for-each select="feature_fields">
							position = feature_fields.length;
							feature_fields[position] = {};
							feature_fields[position].value			= "<xsl:value-of select="@name"/>";
							feature_fields[position].label			= "<xsl:value-of select="label"/>";
						</xsl:for-each>
						var selected_fields = new Array();
						<xsl:for-each select="selected_field">
							position = selected_fields.length;
							selected_fields[position] = {};
							selected_fields[position].value			= "<xsl:value-of select="@name"/>";
							selected_fields[position].rank			= "<xsl:value-of select="@rank"/>";
							selected_fields[position].label			= "<xsl:value-of select="label"/>";
							selected_fields[position].labelsetting	= "<xsl:value-of select="label_display/@setting"/>";
						</xsl:for-each>
						module_info_featured_lists_start();			
					</script>
				</xsl:when>
				<xsl:when test="local-name()='toggle_type'">
				
					<xsl:for-each select="toggle">
					<tr><td>
						<xsl:choose>
							<xsl:when test="@type='search'">
								<table class='width100percent' id='my_type_search'><tr><td></td></tr></table>
							</xsl:when>
							<xsl:when test="@type='charge'">
								<table class='width100percent' id='my_type_charge'><tr><td>
									<strong><xsl:value-of select="@label"/></strong><br/>
									<xsl:for-each select="option">
										<input type='radio'>
											<xsl:attribute name="name"><xsl:value-of select="../@name"/></xsl:attribute>
											<xsl:attribute name="onclick">myFrm.checkChargetype('<xsl:value-of select="../@name"/>');</xsl:attribute>
											<xsl:attribute name="id"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute>
											<xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute>
											<xsl:if test="@selected">
												<xsl:attribute name="checked">checked</xsl:attribute>
											</xsl:if>
										</input> 
										<label> <xsl:attribute name="for"><xsl:value-of select="../@name"/>_<xsl:value-of select="position()"/></xsl:attribute><xsl:value-of select="."/></label>[[nbsp]]
									</xsl:for-each><br/>
									<div id='getChargetype'></div>
									<div id='charge_data'></div>
									<script type="text/javascript">
										<xsl:comment>
										var pricestructure	= '<xsl:value-of select="pricestructure/@val"/>';
										var charge_vat		= '<xsl:value-of select="@vat"/>';
										var prices = new Array();
										<xsl:choose>
											<xsl:when test="boolean(pricestructure/fields)">
												<xsl:for-each select="pricestructure/fields/field">
													pos = prices.length;
													prices[pos] = Array();
													prices[pos][0] = "<xsl:value-of select="../@link_id"/>";
													prices[pos][1] = "<xsl:value-of select="."/>";
													prices[pos][2] = "<xsl:value-of select="@price"/>";
												</xsl:for-each>
											</xsl:when>
											<xsl:otherwise>
												pos = prices.length;
												prices[pos] = Array();
												prices[pos][0] = "__fixed__";
												prices[pos][1] = "<xsl:value-of select="pricestructure/input[@name='fixedprice']/@label"/>";
												prices[pos][2] = "<xsl:value-of select="pricestructure/input[@name='fixedprice']"/>";
											</xsl:otherwise>
										</xsl:choose>
										// </xsl:comment>
									</script>
									</td></tr></table>
								</xsl:when>
								<xsl:otherwise>
									<xsl:apply-templates select="."/>
								</xsl:otherwise>
							</xsl:choose>
						</td></tr>
					</xsl:for-each>
					<script type="text/javascript">
						function my_toggle_type(){
							ok = confirm("You are about to reset the settings for this form!\n\n Are you sure ?","y","n");
							if(ok){
								var f= get_form();
								si = f.fbs_type.options[f.fbs_type.options.selectedIndex].value;
								var my_togglelist = Array(<xsl:for-each select="toggle">'<xsl:value-of select="@type"/>'<xsl:if test="position()!=last()">, </xsl:if></xsl:for-each>);
								if(si &lt; my_togglelist.length ){
									for(i=0;i &lt; my_togglelist.length; i++){
										d = document.getElementById("my_type_"+my_togglelist[i]);
										if(i==si){
											d.style.display='';
										} else {
											d.style.display='none';
										}
									}
								}
								/*
									blank fields, etc .....
								*/
								blank_fba(si);
							}
						} 
						
						function setup_my_toggle_type(){
							var f= get_form();
							si = f.fbs_type.options[f.fbs_type.options.selectedIndex].value;
							var my_togglelist = Array(<xsl:for-each select="toggle">'<xsl:value-of select="@type"/>'<xsl:if test="position()!=last()">, </xsl:if></xsl:for-each>);
							if(si &lt; my_togglelist.length ){
								for(i=0;i &lt; my_togglelist.length; i++){
									d = document.getElementById("my_type_"+my_togglelist[i]);
									if(i==si){
										d.style.display='';
									} else {
										d.style.display='none';
									}
								}
							}
						} 
						setup_my_toggle_type();
					</script>
				</xsl:when>
				<xsl:when test="local-name()='restrictions'">
					<table>
						<xsl:for-each select="group[@type='0']">
						<tr>
							<th class='bt'>Group</th>	
							<xsl:for-each select="definition">
								<th class='bt'><xsl:value-of select="."/></th>	
							</xsl:for-each>
						</tr>
						<tr>
							<td><xsl:value-of select="label"/></td>	
							<xsl:for-each select="definition">
								<td><input type='checkbox'>
									<xsl:attribute name='value'><xsl:value-of select="../@id"/></xsl:attribute>
									<xsl:attribute name='name'><xsl:value-of select="@name"/>[]</xsl:attribute>
									<xsl:if test="@value='1'">
									<xsl:attribute name='checked'>true</xsl:attribute>
									</xsl:if>
								</input></td>	
							</xsl:for-each>
						</tr>
						</xsl:for-each>
						<tr>
							<th colspan='4' class='bt'>Administrative Groups</th>	
						</tr>
						<xsl:for-each select="group[@type='2']">
						<tr>
							<td><xsl:value-of select="label"/></td>	
							<xsl:for-each select="definition">
								<td><input type='checkbox'>
									<xsl:attribute name='value'><xsl:value-of select="../@id"/></xsl:attribute>
									<xsl:attribute name='name'><xsl:value-of select="@name"/>[]</xsl:attribute>
									<xsl:if test="@value='1'">
									<xsl:attribute name='checked'>true</xsl:attribute>
									</xsl:if>
								</input></td>	
							</xsl:for-each>
						</tr>
						</xsl:for-each>
						<tr>
							<th colspan='4' class='bt'>Web user Groups</th>	
						</tr>
						<xsl:for-each select="group[@type='1']">
						<tr>
							<td><xsl:value-of select="label"/></td>	
							<xsl:for-each select="definition">
								<td><input type='checkbox'>
									<xsl:attribute name='value'><xsl:value-of select="../@id"/></xsl:attribute>
									<xsl:attribute name='name'><xsl:value-of select="@name"/>[]</xsl:attribute>
									<xsl:if test="@value='1'">
									<xsl:attribute name='checked'>true</xsl:attribute>
									</xsl:if>
								</input></td>	
							</xsl:for-each>
						</tr>
						</xsl:for-each>
					</table>
				</xsl:when>
				<xsl:when test="local-name()='merge_fields'">
					<table class='width100percent' ><tr><td><div id='mergeded_field_div'></div>
					<script type="text/javascript">
						var merged_tags = new Array();<xsl:for-each select="option">
							merged_tags[merged_tags.length] = '<xsl:value-of select="."/>';
						</xsl:for-each>
					</script>
					<script type="text/javascript" src='/libertas_images/javascripts/module_fba.js'></script>
				 	<script type="text/javascript" src="/libertas_images/javascripts/dhtmlscript.js"></script>
					<script type="text/javascript" src="/libertas_images/javascripts/extractpages.js"></script>
					<script type="text/javascript">
						initialise('<xsl:value-of select="//import_fields/@name"/>');
					</script></td></tr></table>
				</xsl:when>
				<xsl:when test="local-name()='imported_fields'">
					<table class='width100percent' ><tr><td><div id='imported_field_div'></div>
					<script type="text/javascript">
						var imported_tags = new Array();<xsl:for-each select="option">
							imported_tags[imported_tags.length] = Array('<xsl:value-of select="@value"/>','<xsl:value-of select="."/>','');
						</xsl:for-each>
					</script></td></tr></table>
				</xsl:when>
				<xsl:when test="local-name()='used_fields'">
					<table><tr><td><script type="text/javascript">
						var usedfieldlist = new Array();<xsl:for-each select="option">
							usedfieldlist[usedfieldlist.length] = Array('<xsl:value-of select="."/>');
						</xsl:for-each>
					</script></td></tr></table>
				</xsl:when>
				<xsl:when test="local-name()='import_fields'"><tr><td><div id='importoption'>
					<label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:value-of select="@label"/></label>[[nbsp]]
					<select><xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute><xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
							<xsl:for-each select="option">
								<option><xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute><xsl:value-of select="."/></option>
							</xsl:for-each>
						<xsl:for-each select="optgroup">
							<optgroup><xsl:attribute name="label"><xsl:value-of select="@label"/></xsl:attribute>
							<xsl:for-each select="option">
								<option><xsl:attribute name="value"><xsl:value-of select="@value"/></xsl:attribute><xsl:value-of select="."/></option>
							</xsl:for-each>
							</optgroup>
						</xsl:for-each>
						</select>[[nbsp]]
					<input class='bt' id='addfieldsbtn' type='button' value='Import'><xsl:attribute name="onclick">javascript:import_fields('<xsl:value-of select="@name"/>');</xsl:attribute></input></div>
					</td></tr>
				</xsl:when>
				<xsl:when test="local-name()='weight_details'">
					<input type='hidden' name='weight_list_data' value=''/>
					<table id='weightgrid'>
					</table>
					<script src='/libertas_images/javascripts/dhtmlscript.js'></script>
					<script src='/libertas_images/javascripts/module_shipping_weight_matrix.js'></script>
					<script type="text/javascript">
						var weight_list_kg = new Array(<xsl:for-each select="weight_list"><xsl:value-of select="."/><xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
						var weight_list = new Array();
						var weight_regions = Array();
						
						<xsl:for-each select="weights">
						index = <xsl:value-of select="position() - 1"/>;
						weight_list[index] = Array();
						weight_list[index]["country_identifier"] = '<xsl:value-of select="country/@identifier"/>';
						weight_list[index]["country_code"] = '<xsl:value-of select="country/@areacode"/>';
						weight_list[index]["country"] = '<xsl:value-of select="country"/>';
						weight_list[index]["grid"]= new Array();<xsl:for-each select="weight">
						weight_list[index]["grid"][<xsl:value-of select="position() - 1"/>] = Array();
						weight_list[index]["grid"][<xsl:value-of select="position() - 1"/>]["price"] = '<xsl:value-of select="@price"/>';
						weight_list[index]["grid"][<xsl:value-of select="position() - 1"/>]["kg"] 	= '<xsl:value-of select="@kg"/>';
						</xsl:for-each>
						</xsl:for-each>
						<xsl:for-each select="regions/region">
						weight_regions[weight_regions.length] = {"code":"<xsl:value-of select="@code"/>","label":"<xsl:value-of select="."/>"};
						</xsl:for-each>
						setTimeout("generateGrid();",1000);
					</script>
				</xsl:when>
				<xsl:when test="local-name()='metadatamapfields'">
					<!--
					  information directory, map defined fields to metadata entries
					-->
					<div id='metadata_mapping'></div>
					<script type="text/javascript">
						var metadata_tags = new Array();<xsl:for-each select="metadata_tag">
						metadata_tags[<xsl:value-of select="position() - 1"/>] = Array();
						metadata_tags[<xsl:value-of select="position() - 1"/>]["key"] = '<xsl:value-of select="@name"/>';
						metadata_tags[<xsl:value-of select="position() - 1"/>]["label"] = '<xsl:value-of select="."/>';
						</xsl:for-each>
						setTimeout("viewmetadatamaping();",2000);
					</script>
				</xsl:when>
				<xsl:when test="local-name()='web_objects' or local-name()='webTypes' or local-name()='counters' or local-name()='parameters' or local-name()='file_info'">
				<!-- ignore these tags completly -->
				</xsl:when>
				<xsl:when test="local-name()='attached_files'">
				<strong><xsl:value-of select="@label"/></strong>
				<div id='attached_file_list_output'></div>
				<iframe
						frameborder="no" 
						name="cache_data"
						id="cache_data"
						src='/libertas_images/editor/libertas/cache.php'
						><xsl:attribute name="style">padding:3px 3px 3px 3px;WIDTH: 100%;height:<xsl:value-of select="@height"/>px; DIRECTION: ltr; display:none;visibility:hidden;border:1px solid #000000;</xsl:attribute>
					</iframe>
				<script src='/libertas_images/javascripts/module_attachedImage.js'></script>
				<script type="text/javascript">
					var attached_file_list = Array();
					<xsl:for-each select="attached_file">
						attached_file_list[attached_file_list.length] = Array(
							"<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="file/label"/></xsl:with-param></xsl:call-template>",
							"<xsl:value-of select="file/id"/>",
							<xsl:value-of select="position()"/>,
							"<xsl:value-of select="@label"/>",
							"<xsl:value-of select="@id"/>",
							"<xsl:value-of select="file/path"/><xsl:value-of select="file/md5"/><xsl:value-of select="file/extension"/>"
						);
						//attached_file<xsl:value-of select="position()"/>
						//<xsl:value-of select="@label"/>
						//<xsl:value-of select="@name"/>
						<xsl:if test="file">
//							<xsl:value-of select="id"/>::<xsl:value-of select="label"/>			
						</xsl:if>
					</xsl:for-each>
					var attached_files = new attachedFile(attached_file_list,'attached_file_list_output');
				</script>
				</xsl:when>
				<xsl:when test="local-name()='summary_file'">
				<strong><xsl:value-of select="@label"/></strong>
				<div id='attached_file_list_output'></div>
				<script src='/libertas_images/javascripts/module_attachedImage.js'></script>
				<script type="text/javascript">
					var attached_file_list = Array();
						attached_file_list[attached_file_list.length] = Array('<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="file/label"/></xsl:with-param></xsl:call-template>',"<xsl:value-of select="file/id"/>",<xsl:value-of select="position()"/>,"","","<xsl:value-of select="file/path"/><xsl:value-of select="file/md5"/><xsl:value-of select="file/extension"/>");
					var attached_files = new attachedFile(attached_file_list,'attached_file_list_output');
				</script>
				</xsl:when>
				<xsl:when test="local-name()='selection'">
					<div class='label'><label><xsl:value-of select="label"/></label><xsl:if test="@required='YES'"><span class="required">*</span></xsl:if></div>
					<div class='element'><select style="width:250px"><xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
						<option value='-1'>Select a location</option>
						<xsl:if test="//session/groups/group/access='ALL' or //session/groups/group/access='LAYOUT_ALL' or //session/groups/group/access='LAYOUT_AUTHOR_CAN_MANAGE_MENU'">
							<option style="color:#ff0000"><xsl:attribute name='value'>-1,-1</xsl:attribute>Add a new menu location</option>
						</xsl:if>
						<xsl:for-each select="data/option">
							<option><xsl:attribute name='value'><xsl:value-of select="@value"/></xsl:attribute>
							<xsl:if test="@selected"><xsl:attribute name='selected'>true</xsl:attribute></xsl:if>
							<xsl:if test="@disabled"><xsl:attribute name='disabled'>true</xsl:attribute></xsl:if>
							<xsl:value-of select="."/></option>
						</xsl:for-each>
					</select>[[nbsp]]<a href="javascript:menu_data.add();">Add to location list</a><br/>
					<input type='hidden' name='show_add_menu' value='0'/>
					<div id='add_new_menu'></div>
					<div id='selection_data'><xsl:value-of select="text"/></div>
					</div>
					<script src="/libertas_images/javascripts/module_ecms_layout.js"></script>
					<script>
						var menu_data = new Object();
						function defineEcmMenu(){
							menu_data =new ecms_menu();
							menu_data.can_add_menu = '<xsl:choose>
								<xsl:when test="//session/groups/group/access='ALL'">1</xsl:when>
								<xsl:when test="//session/groups/group/access='LAYOUT_ALL'">1</xsl:when>
								<xsl:when test="//session/groups/group/access='LAYOUT_AUTHOR_CAN_MANAGE_MENU'">1</xsl:when>
								<xsl:otherwise>0</xsl:otherwise>
							</xsl:choose>';
							<xsl:for-each select="data/option[@disabled='true']">
								menu_data.disabledlist[menu_data.disabledlist.length] = new Array(<xsl:value-of select="substring-before(@value,',')"/>,<xsl:value-of select="substring-after(@value,',')"/>);
							</xsl:for-each>

							<xsl:for-each select="clist/option">
								menu_data.list[menu_data.list.length] = new Array('<xsl:value-of select='@value'/>','<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="."/></xsl:with-param>
						</xsl:call-template>');
							</xsl:for-each>
							menu_data.display();
						}
						check_selection[check_selection.length] = Array('menu_data.check()', '<xsl:value-of select='label'/>', 'tab_1');
						
					</script>
					
				</xsl:when>
				<xsl:when test="local-name()='entry'">
					<xsl:if test="position()=1">
					<table class='sortable' width="100%" cellspacing="0" cellpadding="0">
						<tr class="formheader"> 
						   	<th valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Module'"/></xsl:call-template></th>
					  		<th valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Version'"/></xsl:call-template></th>
				  			<th valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Creation Date'"/></xsl:call-template></th>
					  		<th valign="top"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'Last Modified'"/></xsl:call-template></th>
	  					</tr>
						<xsl:for-each select="../entry">
						<tr class="TableCell"> 
		   					<td valign="top"><xsl:value-of select="@name"/></td>
		   					<td valign="top"><xsl:value-of select="@version"/></td>
			   				<td valign="top"><xsl:value-of select="@creation"/></td>
			   				<td valign="top"><xsl:value-of select="@modified"/></td>
	  					</tr>
						</xsl:for-each>
					</table>
					</xsl:if>
				</xsl:when>
				<xsl:when test="local-name()='cache'">
					<table><tr><td width="420" valign="top"><p>
					<xsl:choose>
						<xsl:when test="cache_img/@src='/libertas_images/themes/1x1.gif'">
							<input type="button" onclick="mycache.change()" class="bt" id="changeButton" value="select image"/>  <input type="button" onclick="mycache.remove()" class="bt" id="removeButton" value="Remove" style="display:none"/>
						</xsl:when>
						<xsl:otherwise>
							<input type="button" onclick="mycache.change()" class="bt" id="changeButton" value="change"/>  <input type="button" onclick="mycache.remove()" class="bt" id="removeButton" value="Remove"/>
						</xsl:otherwise>
					</xsl:choose>
					</p>
					<img id='choosenimage'>
					<xsl:attribute name="src"><xsl:value-of select="cache_img/@src" disable-output-escaping="yes"/></xsl:attribute>
					<xsl:attribute name="width"><xsl:value-of select="cache_img/@width"/></xsl:attribute>
					<xsl:attribute name="height"><xsl:value-of select="cache_img/@height"/></xsl:attribute>
					</img>
					</td>
					<td><table id="CacheScriptDiv" style="display:none" width="400"></table></td>
					<td><img src="/libertas_images/themes/1x1.gif" width="1" height="300" /></td>
					</tr></table>
					<script src='/libertas_images/javascripts/dhtmlscript.js'></script>
					<script src='/libertas_images/javascripts/cachescript.js'></script>
					<script>
					<xsl:comment>
						var mycache= new CacheScript();
						mycache.cache_command	= '<xsl:value-of select="@cache_command"/>';
						mycache.cache_type		= '<xsl:value-of select="@cache_type"/>';
						mycache.cache_format	= '<xsl:value-of select="@cache_format"/>';
						mycache.cache_filters	= new Array(<xsl:for-each select="filters/setting">'<xsl:value-of select="."/>'<xsl:if test="position()!=last()">,</xsl:if>
						</xsl:for-each>);
						mycache.display();
					</xsl:comment>
					</script>
					<iframe
						frameborder="no" 
						name="cache_data"
						id="cache_data"
						src='/libertas_images/editor/libertas/cache.php'
						><xsl:attribute name="style">padding:3px 3px 3px 3px;WIDTH: 100%;height:<xsl:value-of select="@height"/>px; DIRECTION: ltr; display:none;visibility:hidden;border:1px solid #000000;</xsl:attribute>
					</iframe>
				</xsl:when>
				<xsl:when test="local-name()='directory_entry'">
					<h1>Directory Entries</h1>
					<tr><td valign="top">
					<div id="display_list_of_directory_entries" name="display_list_of_directory_entries"></div>
					<script src="/libertas_images/javascripts/module_info_dir_features.js"></script>
					<SCRIPT>
						var myobjectlist		= new LIB_ObjectList('<xsl:value-of select="@list"/>','display_list_of_directory_entries','CacheScriptDiv');
						myobjectlist.name 		= "myobjectlist";
						myobjectlist.category	= "<xsl:value-of select="@category"/>";
						var cacheData			= new CacheDataObject(myobjectlist);
						<xsl:for-each select="option">
							myobjectlist.add("<xsl:value-of select="@value" disable-output-escaping="yes"/>", "<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="."/></xsl:with-param></xsl:call-template>", "<xsl:value-of select="@category" disable-output-escaping="yes"/>");
						</xsl:for-each>
						myobjectlist.draw();
					</SCRIPT></td>
						<td valign="top" width="400"><div id='CacheScriptDiv'></div>
						<!--
						<iframe frameborder="no" src="/libertas_images/editor/libertas/cache.php">
								<xsl:choose>
									<xsl:when test="not(contains(//setting[@name='qstring'],'LIBERTAS_EDITOR=SHOW_CACHE'))"><xsl:attribute name="style">border:1px solid #000000;width:800px;height:300px;display:none;</xsl:attribute></xsl:when>
									<xsl:otherwise><xsl:attribute name="style">border:1px solid #000000;width:800px;height:300px;display:;</xsl:attribute></xsl:otherwise>
								</xsl:choose>	
								<xsl:attribute name="id">cache_data</xsl:attribute>
								<xsl:attribute name="name">cache_data</xsl:attribute>
							</iframe>
						-->
						</td>
					</tr>
				</xsl:when>				
				<xsl:when test="local-name()='hiddenframe'">
					<div align='center' style="padding:5px">
						<div id='hiddenframeProgressDescription' style='width:300px;text-align:left;'></div>
						<div id='hiddenframeProgressBar' style='text-align:left;background-color:#cccccc;border:1px solid #999999;width:300px;height:15px;'>[[nbsp]]</div>
						<div id='hiddenframeProgressInfo' style='width:600px;text-align:center;'>
							<strong>Generating the export file.</strong><br/>
							Do not close the browser or navigate away from this screen until the export is completed.						
						</div>
					</div>
					<script src='/libertas_images/javascripts/hiddenframe.js'></script>
					<iframe
						frameborder="yes" 
						>
						<xsl:attribute name="src"><xsl:value-of select="@src"/></xsl:attribute>
						<xsl:attribute name="name"><xsl:value-of select="@id"/></xsl:attribute>
						<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
						<xsl:attribute name="xstyle">display:block;WIDTH:100%;height:500px;border:1px;</xsl:attribute>
					</iframe>
				</xsl:when>
				<xsl:when test="local-name()='set'">
				<tr><td><div>
					<xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
					<xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
					<xsl:if test="@hidden='true'">
						<xsl:attribute name="style">display:none;</xsl:attribute>
					</xsl:if>
					<table><xsl:for-each select="child::*">
				<tr>
				<xsl:if test="@name!=''">
					<xsl:attribute name="id">hidden_<xsl:value-of select="@name"/>_label</xsl:attribute>
					<xsl:attribute name="name">hidden_<xsl:value-of select="@name"/>_label</xsl:attribute>
				</xsl:if>
					<xsl:if test="@hidden='YES'"><xsl:attribute name="style">display:none</xsl:attribute></xsl:if>
				<td><xsl:apply-templates select="@label"/></td></tr>
				<tr>
				<xsl:if test="@name!=''">
					<xsl:attribute name="id">hidden_<xsl:value-of select="@name"/></xsl:attribute>
					<xsl:attribute name="name">hidden_<xsl:value-of select="@name"/></xsl:attribute>
				</xsl:if>
					<xsl:if test="@hidden='YES'"><xsl:attribute name="style">display:none</xsl:attribute></xsl:if>
					<xsl:apply-templates select="."/></tr>
				</xsl:for-each></table></div></td></tr>
				</xsl:when>
				<xsl:when test="local-name()='seperator_row'">
				<tr><xsl:for-each select="seperator"><td valign="top">
				<xsl:if test="boolean(@colspan)">
					<xsl:attribute name="colspan"><xsl:value-of select="@colspan"/></xsl:attribute>
				</xsl:if>
				<xsl:call-template name="display_form_data"><xsl:with-param name="showrequired"><xsl:choose><xsl:when test="position()=1">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose></xsl:with-param></xsl:call-template></td></xsl:for-each></tr>
				</xsl:when>
				<xsl:when test="local-name()='site_defination'">
					<xsl:call-template name='drawDefination'/>
				</xsl:when>
				<xsl:when test="local-name()='frame'">
					<div class="centered"><iframe frameborder="no" >
						<xsl:attribute name="style">padding:3px 3px 3px 3px;WIDTH: 100%;height:<xsl:value-of select="@height"/>px; DIRECTION: ltr; display:;border:1px solid #000000;<xsl:if test="@hidden='YES'">display:none</xsl:if></xsl:attribute>
						 <xsl:attribute name="id"><xsl:value-of select="@name"/></xsl:attribute>
						 <xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
					</iframe></div>
				</xsl:when>
				<xsl:when test="local-name()='key'">
					<xsl:if test="preceding-sibling::key[position() - 1]/@refinement!=''">
						<tr>
						  	<td colspan="2"><label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label></td>
						</tr>
					</xsl:if>
						<tr>
							<xsl:if test="@refinement!=''">
							  	<td align="right"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@refinement"/></xsl:call-template> ::</td>
							</xsl:if>
							<xsl:if test="@refinement=''">
							  	<td align="right"><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template> ::</td>
							</xsl:if>
							<xsl:choose>
								<xsl:when test="@url">
					  				<td><a target="_external"><xsl:attribute name="href"><xsl:value-of select="@url"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></a></td>
								</xsl:when>
								<xsl:otherwise>
									<td><xsl:call-template name="get_translation"><xsl:with-param name="check" select="."/></xsl:call-template></td>
								</xsl:otherwise>
							</xsl:choose>
						</tr>
				</xsl:when>
				<xsl:when test="local-name()='wo_list'">
					<table><tr><td valign="top"><input type='hidden' name='webobject_list' value=''/><input type='hidden' name='webobject_list_properties' value=''/><div id="display_list_of_webobjects" name="display_list_of_webobjects"></div>
					<script src='/libertas_images/javascripts/dhtmlscript.js'></script>
					<script src="/libertas_images/javascripts/module_webobjects_containers.js"></script>
					<SCRIPT>
						var webobjectlist		= new WebObjectList();
						webobjectlist.filter	= '<xsl:value-of select="@type"/>';
						var cacheData			= new CacheDataObject(webobjectlist);
						<xsl:for-each select="item">
							webobjectlist.add("<xsl:value-of select="value" disable-output-escaping="yes"/>", <xsl:value-of select="@identifier"/>, '<xsl:choose><xsl:when test="@rank=''">1</xsl:when><xsl:otherwise><xsl:value-of select="@rank"/></xsl:otherwise></xsl:choose>',new Array(<xsl:for-each select="properties/property/option">new Array("<xsl:value-of select="name"/>","<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="value"/></xsl:with-param>
						</xsl:call-template>")<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>));
						</xsl:for-each>
						<xsl:for-each select="type">
							webobjectlist.type[webobjectlist.type.length] = Array ("<xsl:value-of select="label" disable-output-escaping="yes"/>", '<xsl:value-of select="value"/>');
						</xsl:for-each>
						webobjectlist.draw();
					</SCRIPT></td>
							<td valign="top" width="400"><div id='CacheScriptDiv'></div></td>
						</tr>
					</table>
				</xsl:when>
				<xsl:when test="local-name()='file_list'">
					<tr><td><div id="display_list_of_files" name="display_list_of_files"></div>
					<script src="/libertas_images/javascripts/module_ranking_files.js"></script>
					<SCRIPT>
						var rankfiles = new RankingFile();
							rankfiles.report ="<xsl:choose>
								<xsl:when test="@report!=''"><xsl:value-of select="@report"/></xsl:when>
								<xsl:otherwise>normal</xsl:otherwise>
							</xsl:choose>";
						<xsl:for-each select="option">
							rankfiles.newOption("<xsl:value-of select="cmd" disable-output-escaping="yes"/>","<xsl:value-of select="label" disable-output-escaping="yes"/>","<xsl:value-of select="../../@link"/>","<xsl:value-of select="../../@return_command"/>");
						</xsl:for-each>
						<xsl:for-each select="file_info">
							rankfiles.add(
								"<xsl:value-of select="." disable-output-escaping="yes"/>", 
								<xsl:value-of select="@identifier"/>, 
								'<xsl:choose><xsl:when test="@rank=''">1</xsl:when><xsl:otherwise><xsl:value-of select="@rank"/></xsl:otherwise></xsl:choose>', 
								"<xsl:value-of select="@logo"/>",
								"<xsl:value-of select="@width"/>",
								"<xsl:value-of select="@height"/>",
								"<xsl:value-of select="@size"/>",
								"<xsl:value-of select="@md5"/>",
								"<xsl:value-of select="@ext"/>"
								);</xsl:for-each>
						rankfiles.draw();
					</SCRIPT></td></tr>
				</xsl:when>
				<xsl:when test="local-name()='ranks'">
					<tr><td><div id="noteArea" name="noteArea"></div>
					<input type='hidden'><xsl:attribute name='name'>ranked_pages</xsl:attribute>
					<xsl:attribute name='value'><xsl:for-each select="ranking"><xsl:value-of select="@identifier"/><xsl:if test="position()!=last()">,</xsl:if></xsl:for-each></xsl:attribute></input>
					<script src='/libertas_images/javascripts/module_ranking.js'></script>
					<script>
						var ranks = new Ranking();
						<xsl:for-each select="ranking">
							<xsl:variable name='lab'><xsl:call-template name="escapequotes"><xsl:with-param name="str"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param></xsl:call-template></xsl:variable>
							ranks.add(LIBERTAS_GENERAL_jtidy("<xsl:value-of select="$lab"/>"), <xsl:value-of select="@identifier"/>, <xsl:value-of select="@menu"/>, <xsl:value-of select="@rank"/>, '<xsl:value-of select="@available"/>', <xsl:choose>
							<xsl:when test="@identifier = ../@title">1</xsl:when>
							<xsl:otherwise>0</xsl:otherwise>
							</xsl:choose>);
						</xsl:for-each>
						ranks.draw(document.<xsl:value-of select="../../../@name"/>.menu_sort);
					</script></td></tr>
				</xsl:when>
				<xsl:when test="local-name()='access_list'">
					<table width="100%"><tr><td width="550px" valign='top'><div id="AccessArea" name="AccessArea"></div></td><td valign='top'><div id="AccessFilter" name="AccessFilter"></div><iframe name='cache_data' src="/libertas_images/editor/libertas/cache.php" style='display:none'></iframe></td></tr></table>
					<input type="hidden" name="listOfkeys" value=""/>
					<script src='/libertas_images/javascripts/module_accesskeys.js'></script>
					<script src='/libertas_images/editor/libertas/dialogs/utils.js'></script>
					<script>
						var myAccessKeys = new AccessKeyList();
						<xsl:for-each select="accesskey">
							myAccessKeys.add('<xsl:value-of select="@letter"/>', LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes">
								<xsl:with-param name="str"><xsl:value-of select="label"/></xsl:with-param>
								</xsl:call-template>"), LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes">
								<xsl:with-param name="str"><xsl:value-of select="url"/></xsl:with-param>
								</xsl:call-template>"), '<xsl:value-of select="@type"/>', LIBERTAS_GENERAL_jtidy("<xsl:call-template name="escapequotes">
								<xsl:with-param name="str"><xsl:value-of select="title"/></xsl:with-param>
								</xsl:call-template>"));
						</xsl:for-each>
						myAccessKeys.draw();
					</script>
				</xsl:when>
				<xsl:when test="local-name()='comments'">
				<xsl:for-each select="comment">
					<xsl:if test="position()=1">
						<tr class='tableheader'>
							<td>Title</td>
							<td>Author</td>
							<td>Date</td>
						</tr>
					</xsl:if>
						<tr><xsl:attribute name="class"><xsl:choose><xsl:when test="position() mod 2 = 0 ">tablecell</xsl:when><xsl:otherwise>TableCell_alt</xsl:otherwise></xsl:choose></xsl:attribute>
							<td><a ><xsl:attribute name="href">javascript:LIBERTAS_view_comments_click(<xsl:value-of select="@comment_identifier"/>);</xsl:attribute><xsl:choose><xsl:when test="title=''">No Title Supplied</xsl:when><xsl:otherwise><xsl:value-of select="title" disable-output-escaping="yes"/></xsl:otherwise></xsl:choose></a></td>
							<td><xsl:value-of select="author" disable-output-escaping="yes"/></td>
							<td><xsl:value-of select="date" disable-output-escaping="yes"/></td>
						</tr>
					</xsl:for-each>
				</xsl:when>
				<xsl:when test="local-name()='subsection'">
					<xsl:call-template name="displaySection">
							<xsl:with-param name="section_name"><xsl:choose>
								<xsl:when test="@name!=''"><xsl:value-of select="@name"/></xsl:when>
								<xsl:otherwise>subsection_1</xsl:otherwise>
							</xsl:choose></xsl:with-param>
						</xsl:call-template>
				</xsl:when>
				<xsl:when test="local-name()='column'">
					<div style='width:49%;display:inline;vertical-align:top;'>
						<xsl:call-template name="displaySection">
							<xsl:with-param name="section_name"><xsl:choose>
								<xsl:when test="@name!=''"><xsl:value-of select="@name"/></xsl:when>
								<xsl:otherwise>subsection_1</xsl:otherwise>
							</xsl:choose></xsl:with-param>
						</xsl:call-template>
					</div>
				</xsl:when>
				<xsl:when test="local-name()='menulinks'">
				<script src='/libertas_images/javascripts/dhtmlscript.js'></script>
				<script src="/libertas_images/javascripts/extractpages.js"></script>
				<script>
				function extract_url(t){}
				</script>
				<input type="hidden" name="linkblock"/>
				<div id="menulinksOutput">
					<hr />
					<div id='retrieveMenu'></div>
					<div id='retrievePages'></div>
					<hr />
					<p><label for='choosenLabel' style='width:125px' >Label for Link</label><input type='text' name='choosenLabel' id='choosenLabel'/></p>
					<p><label for='choosenTitleLabel' style='width:125px'>Title for Link</label><input type='text' name='choosenTitleLabel' id='choosenTitleLabel'/></p>
					<p><label for='choosenUrl' style='width:125px'>Url</label><input type='text' name='choosenUrl' id='choosenTitle'/></p>
					<div id='submitbutton'></div>
					<hr />
				</div>
				<table id="resultTable" width="100%">
					<tr><th class='bt'>Title</th><th class='bt'>Label</th><th class='bt'>URL</th><th class='bt'>Options</th></tr>
				</table>
				<script>
					var menulinks	= new menuLinks();
					menulinks.links = new Array();
					<xsl:for-each select="menulink">
					index = menulinks.links.length;
					menulinks.links[index] = new Array();
					menulinks.links[index]["id"]	= "<xsl:value-of select="position()"/>";
					menulinks.links[index]["url"]	= "<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="url"/></xsl:with-param></xsl:call-template>";
					menulinks.links[index]["title"] = "<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title"/></xsl:with-param></xsl:call-template>";
					menulinks.links[index]["label"] = "<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param></xsl:call-template>";
					</xsl:for-each>
					menulinks.draw();
					function extract_url(t){
						menulinks.set_retrieve_button();
					}
				</script>
				</xsl:when>
				<xsl:when test="local-name()='filterbuilder'">				
					<div id="filterBuilderDiv" name="filterBuilderDiv"></div>
							<div id="resultDiv" name="resultDiv" style="width:100%;"></div>
							<div id="queryDiv" name="queryDiv" style="width:100%;"></div>
							<!--
							<iframe id='framecache' name='framecache' width='100%' height='400px' style='width:100%;height:100px;visibility:hidden' frameborder='0' src='/libertas_images/editor/libertas/cache.php'/>
							-->
					<script src='/libertas_images/javascripts/dhtmlscript.js'></script>
					<script src="/libertas_images/editor/filter_builder/main.js"></script>
					<script>
					<xsl:comment>
						var builder 			= new Libertas_filter_builder();
						builder.outputDiv		= "filterBuilderDiv";
						builder.resultDiv		= "resultDiv";
						builder.queryDiv		= "queryDiv";
						builder.module			= "<xsl:value-of select="@module"/>";
						builder.owner			= "<xsl:value-of select="@owner"/>";
						builder.order_field_dir	= <xsl:choose><xsl:when test="boolean(filterorder) and filterorder/direction=1">1</xsl:when><xsl:otherwise>0</xsl:otherwise></xsl:choose>;
						builder.order_field		= "<xsl:value-of select="filterorder/field"/>";
						<xsl:for-each select="extratags/child::*">
							builder.extratags["<xsl:value-of select="local-name()"/>"] = '<xsl:value-of select="."/>';
						</xsl:for-each>
						
						<xsl:for-each select="filterselect">
							<xsl:choose>
								<xsl:when test="@name='f_field'">
								builder.fieldlist = new Array(<xsl:for-each select="option">
									new Array("<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="@value"/></xsl:with-param></xsl:call-template>", "<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="."/></xsl:with-param></xsl:call-template>", "<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="@type"/></xsl:with-param></xsl:call-template>", "<xsl:value-of select="@order"/>")<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>
								);
								</xsl:when>
								<xsl:when test="@name='f_match'">
								builder.matchlist = new Array(<xsl:for-each select="option">new Array("<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="@value"/></xsl:with-param></xsl:call-template>","<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="."/></xsl:with-param></xsl:call-template>")<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
								</xsl:when>
								<xsl:otherwise>
									builder.filteroptions = new Array();
									<xsl:for-each select="options">
										builder.filteroptions["<xsl:value-of select="@field"/>"] = Array(<xsl:for-each select="option">"<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="."/></xsl:with-param></xsl:call-template>"<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
									</xsl:for-each>
								</xsl:otherwise>
							</xsl:choose>
						</xsl:for-each>
						<xsl:for-each select="filterdef/definition">
							builder.add('<xsl:value-of select="@field"/>','<xsl:value-of select="@condition"/>','<xsl:value-of select="@join"/>','<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="."/></xsl:with-param>
						</xsl:call-template>');
						</xsl:for-each>
						builder.draw();
					</xsl:comment>
					</script>
				</xsl:when>
				<xsl:otherwise>
					<tr>
					<xsl:if test="@name!=''">
						<xsl:attribute name="id">hidden_<xsl:value-of select="@name"/>_label</xsl:attribute>
					</xsl:if>
					<xsl:if test="@hidden='YES'"><xsl:attribute name="style">display:none</xsl:attribute></xsl:if>
					<td><xsl:if test="@name!=''"><label><xsl:attribute name="for"><xsl:value-of select="@name"/></xsl:attribute><xsl:call-template name="get_translation"><xsl:with-param name="check" select="@label"/></xsl:call-template></label> </xsl:if><xsl:if test="@required"><span class="required">*</span>
							<xsl:if test="local-name()!='textarea'">
							<script>
							<xsl:if test="//subsection[@required='YES']">
								<xsl:for-each select="//subsection[@required='YES']">
									check_required[check_required.length] = new Array('<xsl:value-of select="@link"/>', '<xsl:value-of select="@label"/>', '<xsl:value-of select="$span_name"/>');
								</xsl:for-each>
							</xsl:if>
							<xsl:choose>
								<xsl:when test="local-name()='input' and @required!='YES'">
								<xsl:variable name="required"><xsl:value-of select="@required"/></xsl:variable>
								check_compair[check_compair.length] = new Array(new Array('<xsl:value-of select="@name"/>','<xsl:value-of select="@required"/>'), new Array('<xsl:value-of select="@label"/>','<xsl:value-of select="//input[@name=$required]/@label"/>'), '<xsl:value-of select="$span_name"/>');</xsl:when>
								<xsl:when test="local-name()='checkboxes'">check_contains_selected[check_contains_selected.length] = new Array('<xsl:value-of select="@name"/>', '<xsl:value-of select="@label"/>', '<xsl:value-of select="$span_name"/>');</xsl:when>
								<xsl:otherwise>check_required[check_required.length] = new Array('<xsl:value-of select="@name"/>', '<xsl:value-of select="@label"/>', '<xsl:value-of select="$span_name"/>');</xsl:otherwise>
							</xsl:choose>

							</script></xsl:if>
						</xsl:if></td></tr>
						<tr>
						<xsl:if test="@name!=''">
						<xsl:attribute name="id">hidden_<xsl:value-of select="@name"/></xsl:attribute>
						</xsl:if>
						<xsl:if test="@hidden='YES'"><xsl:attribute name="style">display:none</xsl:attribute></xsl:if>
							<xsl:apply-templates select=".">
								<xsl:with-param name="span_name"><xsl:value-of select="$span_name"/></xsl:with-param>
							</xsl:apply-templates>
						</tr>
				</xsl:otherwise>
			</xsl:choose>
			</xsl:for-each>
			<xsl:if test="@command">
				<xsl:if test="not(file_list)">
					<div><xsl:attribute name="id">display_note_<xsl:value-of select="@name"/></xsl:attribute>
						<xsl:call-template name="get_translation">
							<xsl:with-param name="check"><xsl:value-of select="." disable-output-escaping="yes"/></xsl:with-param>
						</xsl:call-template>
					</div>
					<ul><li><a><xsl:attribute name="href">javascript:retrieve_data('<xsl:value-of select="@command"/>','<xsl:value-of select="@link"/>','display_note_<xsl:value-of select="@name"/>','<xsl:value-of select="@return_command"/>')</xsl:attribute><xsl:call-template name="get_translation">
						<xsl:with-param name="check"><xsl:value-of select="'LOCALE_ADD_NEW_ENTRY'"/></xsl:with-param>
					</xsl:call-template></a> <xsl:if test="@required='YES'"><span class='required'>*</span></xsl:if></li></ul>
				</xsl:if>
			</xsl:if>
				</td></tr>
				</table>
			</div></td>
			</tr>
		</table></td>
	</tr>
	</table>
</div>
</xsl:template>


<xsl:template match="keywords">
<xsl:choose>
	<xsl:when test=".//keyword">
	   	<td valign="top" colspan="2"><input type="hidden" name="keyword_ignore_list" value=""/><input type="hidden" name="temp_ignore_list" value=""/>
		<span id="displayKeywords">
		<table width="100%" border="0" cellpadding="3" cellspacing="0">
			<xsl:for-each select="keyword[(position() mod 3) =1]">
				<tr>
					<td width="20%">
					<input type="checkbox" checked="true">
					   	<xsl:attribute name="name">keywords[]</xsl:attribute>
						<xsl:attribute name="id">keyword_<xsl:value-of select="((position()-1)*3)+1"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="@count"/>, <xsl:value-of select="."/></xsl:attribute>
						<xsl:attribute name="onclick">javascript:ignore_keyword(this)</xsl:attribute>
			   		</input>&#32;<label><xsl:attribute name="for">keyword_<xsl:value-of select="((position()-1)*3)+1"/></xsl:attribute><xsl:value-of select="."/>, (<xsl:value-of select="@count"/>)</label>
					</td>
					<xsl:if test="following-sibling::keyword[(position() mod 3) = 1]">
					<td width="20%">
					<input type="checkbox" checked="true">
					   	<xsl:attribute name="name">keywords[]</xsl:attribute>
						<xsl:attribute name="id">keyword_<xsl:value-of select="((position()-1)*3)+2"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="following-sibling::keyword[(position() mod 3) = 1]/@count"/>, <xsl:value-of select="following-sibling::keyword[(position() mod 3) = 1]"/></xsl:attribute>
						<xsl:attribute name="onclick">javascript:ignore_keyword(this)</xsl:attribute>
			   		</input>&#32;<label><xsl:attribute name="for">keyword_<xsl:value-of select="((position()-1)*3)+2"/></xsl:attribute><xsl:value-of select="following-sibling::keyword[(position() mod 3) = 1]"/>, (<xsl:value-of select="following-sibling::keyword[(position() mod 3) = 1]/@count"/>)</label>
					</td>
					</xsl:if>
					<xsl:if test="following-sibling::keyword[(position() mod 3) = 2]">
					<td width="20%">
					<input type="checkbox" checked="true">
					   	<xsl:attribute name="name">keywords[]</xsl:attribute>
						<xsl:attribute name="id">keyword_<xsl:value-of select="((position()-1)*3)+3"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="following-sibling::keyword[(position() mod 3) = 2]/@count"/>, <xsl:value-of select="following-sibling::keyword[(position() mod 3) = 2]"/></xsl:attribute>
						<xsl:attribute name="onclick">javascript:ignore_keyword(this)</xsl:attribute>
			   		</input>&#32;<label><xsl:attribute name="for">keyword_<xsl:value-of select="((position()-1)*3)+3"/></xsl:attribute><xsl:value-of select="following-sibling::keyword[(position() mod 3) = 2]"/>, (<xsl:value-of select="following-sibling::keyword[(position() mod 3) = 2]/@count"/>)</label>
					</td>
					</xsl:if>
				</tr>
			</xsl:for-each>
			</table>
			</span>
			<center><input name='btn_regen_keys' id='btn_regen_keys' class="bt" type='button' value='Regenerate Keywords' onclick="LIBERTAS_regenerate_keywords_from_all_editors_click()"/></center>
		   	</td>
	</xsl:when>
	<xsl:otherwise>
		   	<td valign="top" colspan="2"><input type="hidden" name="keyword_ignore_list" value=""/><input type="hidden" name="temp_ignore_list" value=""/>
				<span id="displayKeywords"><strong><xsl:call-template name="get_translation"><xsl:with-param name="check" select="'LOCALE_NO_KEYWORDS'"/></xsl:call-template></strong></span>
				<center><input name='btn_regen_keys' id='btn_regen_keys' class='bt' type='button' value='Regenerate Keywords' onclick="LIBERTAS_regenerate_keywords_from_all_editors_click()"/></center>
		   	</td>
	</xsl:otherwise>
	</xsl:choose>
</xsl:template>
<xsl:template match="phrases">
   	<td valign="top" colspan="2"><input type="hidden" name="phrase_ignore_list" value=""/>
		<table width="100%" border="0" cellpadding="3" cellspacing="0">
			<xsl:for-each select="phrase[(position() mod 3) =1]">
				<tr>
					<td width="20%">
					<input type="text" size="35">
					   	<xsl:attribute name="name">phrase[]</xsl:attribute>
						<xsl:attribute name="id">phrase_<xsl:value-of select="((position()-1)*3)+1"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="."/></xsl:attribute>
			   		</input>
					</td>
					<xsl:if test="following-sibling::phrase[(position() mod 3) = 1]">
					<td width="20%">
					<input type="text" size="35">
					   	<xsl:attribute name="name">phrase[]</xsl:attribute>
						<xsl:attribute name="id">phrase_<xsl:value-of select="((position()-1)*3)+2"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="following-sibling::phrase[(position() mod 3) = 1]"/></xsl:attribute>
			   		</input>
					</td>
					</xsl:if>
					<xsl:if test="following-sibling::phrase[(position() mod 3) = 2]">
					<td width="20%">
					<input type="text" size="35">
					   	<xsl:attribute name="name">phrase[]</xsl:attribute>
						<xsl:attribute name="id">phrase_<xsl:value-of select="((position()-1)*3)+3"/></xsl:attribute>
						<xsl:attribute name="value"><xsl:value-of select="following-sibling::phrase[(position() mod 3) = 2]"/></xsl:attribute>
			   		</input>
					</td>
					</xsl:if>
				</tr>
			</xsl:for-each>
		</table>
   	</td>
</xsl:template>

<xsl:template match="ranks"></xsl:template>
<xsl:template match="subsection"></xsl:template>
<xsl:template match="file_info"></xsl:template>
<xsl:template match="file_list"></xsl:template>
<xsl:template match="clist"></xsl:template>


<xsl:template match="config">
<tr>
	<td>
	<input type='hidden'>
		<xsl:attribute name="name">modules[]</xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@module"/></xsl:attribute>
	</input>
	<input type='hidden'>
		<xsl:attribute name="name">editor_name[]</xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@locale"/></xsl:attribute>
	</input>
	<input type='hidden'>
		<xsl:attribute name="name">list[]</xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="../../@name"/>_<xsl:value-of select="@locale"/></xsl:attribute>
	</input>
	<input type='hidden'>
		<xsl:attribute name="name"><xsl:value-of select="../../@name"/>_<xsl:value-of select="@locale"/>_identifier</xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@identifier"/></xsl:attribute>
	</input>
	<input type='hidden'>
		<xsl:attribute name="name"><xsl:value-of select="../../@name"/>_<xsl:value-of select="@locale"/>_locked_to</xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@locked_to"/></xsl:attribute>
	</input>
	<input type='hidden'>
		<xsl:attribute name="name"><xsl:value-of select="../../@name"/>_<xsl:value-of select="@locale"/>_status</xsl:attribute>
		<xsl:attribute name="value"><xsl:value-of select="@status"/></xsl:attribute>
	</input>
	<xsl:value-of select="@name"/></td><td><select><xsl:attribute name="name"><xsl:value-of select="../../@name"/>_<xsl:value-of select="@locale"/></xsl:attribute><option value="0">Use Groups Access to define Editor Configuration</option></select></td><td></td>
</tr>
</xsl:template>

<xsl:template match="configs">
<tr><td colspan="3">This module utilisies the following editors, You can choose the editor configuration for each editor or leave the editor configuration up to the group that users belong to.</td></tr>
<tr><td><strong>Module Editor</strong></td><td><strong>Setting</strong></td><td></td></tr>
<xsl:apply-templates select="config"/>
	<script src="/libertas_images/javascripts/module_editor.js"></script>
	<script>
		var xeditor_configurations = Array(<xsl:for-each select="../../../editors/editor">
			Array(<xsl:value-of select="@identifier"/>,"<xsl:value-of select="." disable-output-escaping="yes"/>")<xsl:if test="position()!=last()">,</xsl:if></xsl:for-each>);
		load_configs_into_select(document.<xsl:value-of select="../../../@name"/>);
	</script>
</xsl:template>
<xsl:template match="url">
</xsl:template>
<xsl:template match="method">
</xsl:template>
<xsl:template match="confirm_screen">
</xsl:template>
<xsl:template match="frame">
</xsl:template>
<xsl:template match="counters">
</xsl:template>
<xsl:template match="column"></xsl:template>
<!--
<xsl:template match="properties">
</xsl:template>
<xsl:template match="property">
</xsl:template>
<xsl:template match="option">
</xsl:template>
-->
</xsl:stylesheet>

