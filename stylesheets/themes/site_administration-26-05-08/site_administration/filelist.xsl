<?xml version="1.0" encoding="iso-8859-1"?>
<xsl:stylesheet
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  version="1.0"> 
 <!--
 /*
 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 - Version $Revision: 1.7 $
 - Modified $Date: 2004/12/08 18:56:09 $
 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
 */
 -->
<xsl:include href="../../themes/site_administration/print.xsl"/>
<xsl:template match="/">
<html>
<head>
	<script>
		var listing_images	= <xsl:value-of select="contains(//xml_document/modules/module/setting[@name='qstring'],'command=FILES_FILTER&amp;type=image')"/>
		var listing_flash	= <xsl:value-of select="contains(//xml_document/modules/module/setting[@name='qstring'],'command=FILES_FILTER&amp;type=flash')"/>
		var listing_audio	= <xsl:value-of select="contains(//xml_document/modules/module/setting[@name='qstring'],'command=FILES_FILTER&amp;type=audio')"/>
		var listing_movie	= <xsl:value-of select="contains(//xml_document/modules/module/setting[@name='qstring'],'command=FILES_FILTER&amp;type=movie')"/>
		var listing_files	= <xsl:value-of select="contains(//xml_document/modules/module/setting[@name='qstring'],'command=FILES_FILTER&amp;type=all')"/>
		var listing_forms	= <xsl:value-of select="contains(//xml_document/modules/module/setting[@name='qstring'],'command=SFORM_FORM_EMBED')"/>
		var listing_pages	= '<xsl:value-of select="//xml_document/modules/module/pages/@results"/>';
		var file_list		= new Array(<xsl:call-template name="show_files"></xsl:call-template>);
		var filtered_list	= new Array(<xsl:apply-templates select="//xml_document/modules/module[@name='files' and @display='filteredlist']/files" />);
		var directory_list	= new Array(<xsl:apply-templates select="//xml_document/modules/module/directories" />);
		var menu_list		= new Array(<xsl:call-template name="show_menu"></xsl:call-template>);
		var page_list		= new Array(<xsl:call-template name="show_pages"></xsl:call-template>);
		var form_list		= new Array(<xsl:call-template name="show_forms"></xsl:call-template>);
		var webobjects_list = new Array(<xsl:call-template name="show_webobjects"></xsl:call-template>);
		var category_list 	= new Array(<xsl:call-template name="show_categories"></xsl:call-template>);
		var infodir_list 	= new Array(<xsl:call-template name="show_directory_entries"></xsl:call-template>);
		var query_list 		= new Array(<xsl:call-template name="show_query"></xsl:call-template>);
		var query_number	= '<xsl:value-of select="//xml_document/modules/module[@display='TEST_QUERY']/numrows"/>';		
		var field_data		= new Array(<xsl:call-template name="show_fields"></xsl:call-template>);		
		var field_options	= new Array(<xsl:call-template name="show_fieldoptions"></xsl:call-template>);		
		result_list = new Array();

		function generate(){
//			t = window.opener.document.forms[0];
			for (var index=0;index &lt; filtered_list.length;index++){
				filtered_list[index][-1] = get_path(filtered_list[index][2])+filtered_list[index][7]+get_ext(filtered_list[index][9]);
			}
		}
		function get_path(identifier){
			for(dir_index=0;dir_index &lt;  directory_list.length;dir_index++){
				if (directory_list[dir_index][1] == identifier){
					return directory_list[dir_index][2]+directory_list[dir_index][0]+"/";
				}
			}
		}
		
		function get_ext(s){
			s = new String(s);
			p = s.lastIndexOf(".");
			return s.substring(p,s.length);
		}
		if (filtered_list.length!=0){
			generate();
			msg = "";
			for (i=0;i &lt; filtered_list.length; i++){      
				msg += filtered_list[i][1]+"|1234567890|"+filtered_list[i][-1]+"::"+filtered_list[i][3]+"::"+filtered_list[i][4]+"::"+filtered_list[i][5]+"::"+filtered_list[i][6]+"::"+filtered_list[i][8]+"|1234567890|"
			}
			if (listing_flash){
				msg = "Please select a flash movie to insert|1234567890|::0::0::0::|1234567890|"+msg;
				return_data(msg,'flash')
			}
			if (listing_movie){
				msg = "Please select a movie to insert|1234567890|::0::0::0::|1234567890|"+msg;
				return_data(msg,'movie')
			}
			if (listing_audio){
				msg = "Please select an audio file to insert|1234567890|::0::0::0::|1234567890|"+msg;
				return_data(msg,'audio')
			}
			if (listing_images){
				msg = "Please select an image to insert|1234567890|libertas_images/themes/1x1.gif::0::0::0::|1234567890|"+msg;
				return_data(msg,'image')
			}
			if (listing_files){
				msg = "Please select a file to link to|1234567890|::0::0::0::|1234567890|"+msg;
				return_data(msg,'file')
			}
		} else {
			if (listing_flash){
				return_data("",'flash')
			}
			if (listing_movie){
				return_data("",'movie')
			}
			if (listing_audio){
				return_data("",'audio')
			}
			if (listing_images){
				return_data("",'image')
			}
			if (listing_files){
				return_data("",'file')
			}
		}
		
		
		if (field_data.length!=0){
			msg=field_data.join("|1234567890|");
			return_data(msg,'fields')
		}
		if (field_options.length!=0){
			msg=field_options.join("|1234567890|");
			return_data(msg,'field_options')
		}
		if (webobjects_list.length!=0){
			msg=webobjects_list.join("|1234567890|");
			return_data(msg,'webobjects')
		}
		if (category_list.length!=0){
			msg=category_list.join("|1234567890|");
			return_data(msg,'category')
		}
		if (infodir_list.length!=0){
			msg=infodir_list.join("|1234567890|");
			return_data(msg,'infodir')
		}
		if (listing_forms){
			msg  = "Please select a form to embed|1234567890||1234567890|";
			msg += "~~~ System Defined Forms ~~~|1234567890||1234567890|";
			for (i=0;i &lt; form_list.length; i++){      
				msg += form_list[i][0]+"|1234567890|"
			}
			return_data(msg,'forms')
		}
		if (menu_list.length!=0){
			msg="";
			for (var i=0;i &lt; menu_list.length; i++){      
				msg += menu_list[i]+"|1234567890|";
			}
			return_data(msg,'menu')
		}
		if (listing_pages==1){
			if (page_list.length!=0){
				msg="";
				msg = page_list.join("|1234567890|");
				return_data(msg,'page')
			} else {
				return_data('','page')
			}
		}
		if (file_list.length!=0){
			msg="";
			msg = file_list.join("|1234567890|");
			return_data(msg,'file')
		}
		if (query_list.length!=0){
			msg = query_number+":1234567890:";
			msg += query_list.join("|1234567890|");
			return_data(msg,'query')
		}
		function return_data(mydata,key){
			if(mydata==""){
				mydata="__NOT_FOUND__";
			}
			parent._exec_function(mydata,key)
		}
	</script>
</head>
<body topmargin="0" leftmargin="0" marginheight="0" marginwidth="0" onload="javascript:generate()">
</body>

</html>
</xsl:template>

<xsl:template match="files">
	<xsl:for-each select="file">
		new Array("<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>",new String("<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>").split("&amp;amp;").join("&amp;"),"<xsl:value-of select="directory"/>","<xsl:value-of select="width"/>","<xsl:value-of select="height"/>","<xsl:value-of select="icon"/>","<xsl:value-of select="size"/>","<xsl:value-of select="md5"/>","<xsl:value-of select="@identifier"/>","<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="url"/></xsl:with-param>
						</xsl:call-template>")<xsl:if test="position()!=last()">, </xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template match="directories">
	<xsl:call-template name="get_directory"/>
</xsl:template>
<xsl:template name="get_directory">
	<xsl:for-each select="directory">
		new Array("<xsl:value-of select="@name"/>",<xsl:value-of select="@identifier"/>,"<xsl:call-template name="get_path"/>")<xsl:if test="directory">,<xsl:call-template name="get_directory"/></xsl:if><xsl:if test="position()!=last()">, </xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="get_path">
	<xsl:choose>
		<xsl:when test="@parent=-2"></xsl:when>
		<xsl:when test="@parent=-1"></xsl:when>
		<xsl:otherwise>
			<xsl:for-each select="..">
				<xsl:call-template name="get_path"/><xsl:value-of select="@name"/>/</xsl:for-each>
		</xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="show_menu">
	<xsl:for-each select="//xml_document/modules/module/menu_structure/menu">
		new Array('<xsl:call-template name="remove_apost"><xsl:with-param name="str"><xsl:choose>
		<xsl:when test="label"><xsl:value-of select="label"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
		</xsl:choose></xsl:with-param></xsl:call-template>|1234567890|<xsl:value-of select="@identifier"/>::<xsl:value-of select="@parent"/>::<xsl:choose>
		<xsl:when test="url"><xsl:value-of select="url"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="@url"/></xsl:otherwise>
		</xsl:choose>::<xsl:choose>
			<xsl:when test="groups/option">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose><xsl:choose>
			<xsl:when test="children/menu">|1234567890|<xsl:call-template name="show_children"><xsl:with-param name="depth">1</xsl:with-param></xsl:call-template></xsl:when>
			<xsl:otherwise></xsl:otherwise>
		</xsl:choose>')<xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>
<xsl:template name="show_children">
	<xsl:param name="depth">1</xsl:param>
	<xsl:for-each select="children/menu"><xsl:if test="position()!=1">|1234567890|</xsl:if><xsl:call-template name="draw_depth"><xsl:with-param name="depth"><xsl:value-of select="$depth"/></xsl:with-param></xsl:call-template>-<xsl:call-template name="remove_apost"><xsl:with-param name="str"><xsl:choose>
		<xsl:when test="label"><xsl:value-of select="label"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="@label"/></xsl:otherwise>
		</xsl:choose></xsl:with-param></xsl:call-template>|1234567890|<xsl:value-of select="@identifier"/>::<xsl:value-of select="@parent"/>::<xsl:choose>
		<xsl:when test="url"><xsl:value-of select="url"/></xsl:when>
		<xsl:otherwise><xsl:value-of select="@url"/></xsl:otherwise>
		</xsl:choose>::<xsl:choose>
			<xsl:when test="groups/option">1</xsl:when>
			<xsl:otherwise>0</xsl:otherwise>
		</xsl:choose><xsl:choose>
			<xsl:when test="children/menu">|1234567890|<xsl:call-template name="show_children"><xsl:with-param name="depth"><xsl:value-of select="$depth + 1"/></xsl:with-param></xsl:call-template></xsl:when>
			<xsl:otherwise></xsl:otherwise>
		</xsl:choose></xsl:for-each>
</xsl:template>

<xsl:template name="draw_depth">
	<xsl:param name="depth">1</xsl:param>
	<xsl:choose>
		<xsl:when test="$depth > 0">[[nbsp]]<xsl:call-template name="draw_depth"><xsl:with-param name="depth"><xsl:value-of select="$depth - 1"/></xsl:with-param></xsl:call-template></xsl:when>
		<xsl:otherwise></xsl:otherwise>
	</xsl:choose>
</xsl:template>
<xsl:template name="show_pages">
	<xsl:for-each select="//xml_document/modules/module/pages/page">
	new Array('<xsl:value-of select="title" disable-output-escaping="yes"/>|1234567890|<xsl:value-of select="menu_location" disable-output-escaping="yes"/>')<xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="show_files">
<xsl:for-each select="//xml_document/modules/module[@name='files' and @display='completelist']/files/file">	
	new Array('<xsl:call-template name="print">
			<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
		</xsl:call-template>|1234567890|<xsl:value-of select="md5" disable-output-escaping="yes"/>')<xsl:if test="position()!=last()">,</xsl:if>
</xsl:for-each>
</xsl:template>

<xsl:template name="show_forms">
<xsl:for-each select="//xml_document/modules/module[@name='Standard Forms' and @display='embedded_list']/form_builder">	
	new Array('<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>|1234567890|<xsl:value-of select="@identifier" disable-output-escaping="yes"/>::<xsl:value-of select="label" disable-output-escaping="yes"/>')<xsl:if test="position()!=last()">,</xsl:if>
</xsl:for-each>
</xsl:template>

<xsl:template name="show_webobjects">
<xsl:for-each select="//xml_document/modules/module[@name='webobjects' and @display='list']/web_object">	
	'<xsl:value-of select="@type" disable-output-escaping="yes"/>::<xsl:value-of select="@identifier" disable-output-escaping="yes"/>::<xsl:value-of select="." disable-output-escaping="yes"/>'<xsl:if test="position()!=last()">,</xsl:if>
</xsl:for-each>
</xsl:template>

<xsl:template name="show_query">
	<xsl:for-each select="//xml_document/modules/module[@display='TEST_QUERY']/entry">	
		'<xsl:call-template name="print"><xsl:with-param name="str_value"><xsl:value-of select="title"/></xsl:with-param></xsl:call-template>'<xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="show_categories">
	<xsl:for-each select="//xml_document/modules/module[@name='categories' and @display='completelist']/category">	
		'<xsl:value-of select="@parent" disable-output-escaping="yes"/>::<xsl:value-of select="@identifier" disable-output-escaping="yes"/>::<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>'<xsl:if test="children/category">,<xsl:call-template name="show_sub_cats"></xsl:call-template></xsl:if><xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>
<xsl:template name="show_sub_cats">
	<xsl:for-each select="children/category">
		'<xsl:value-of select="@parent" disable-output-escaping="yes"/>::<xsl:value-of select="@identifier" disable-output-escaping="yes"/>::<xsl:call-template name="print">
							<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
						</xsl:call-template>'<xsl:if test="children/category">,<xsl:call-template name="show_sub_cats"></xsl:call-template></xsl:if><xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="remove_apost">
	<xsl:param name="str"></xsl:param>
	<xsl:param name="search">'</xsl:param>
	<xsl:choose>
		<xsl:when test="contains($str,$search)">
			<xsl:value-of select="substring-before($str, $search)" disable-output-escaping="no"/>&amp;#39;<xsl:call-template name="remove_apost"><xsl:with-param name="str"><xsl:value-of select="substring-after($str, $search)" disable-output-escaping="no"/></xsl:with-param></xsl:call-template>
		</xsl:when>
		<xsl:otherwise><xsl:value-of select="$str" disable-output-escaping="no"/></xsl:otherwise>
	</xsl:choose>
</xsl:template>

<xsl:template name="show_directory_entries">
	<xsl:for-each select="//xml_document/modules/module[@name='information_admin' and @display='CACHE']/entry">	
		'<xsl:value-of select="@identifier" disable-output-escaping="yes"/>::<xsl:call-template name="print">
			<xsl:with-param name="str_value"><xsl:value-of select="title"/></xsl:with-param>
		</xsl:call-template>'<xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>

<xsl:template name="show_fields">
	<xsl:for-each select="//module[@display='fields']/field">	
		'<xsl:value-of select="name" disable-output-escaping="yes"/>::<xsl:call-template name="print">
			<xsl:with-param name="str_value"><xsl:value-of select="label"/></xsl:with-param>
		</xsl:call-template>::<xsl:value-of select="type" disable-output-escaping="yes"/>::<xsl:value-of select="map" disable-output-escaping="yes"/>::<xsl:value-of select="auto" disable-output-escaping="yes"/>'<xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>
<xsl:template name="show_fieldoptions">
	<xsl:for-each select="//module[@display='fieldoptions']/option">	
		'<xsl:call-template name="print">
			<xsl:with-param name="str_value"><xsl:value-of select="name"/></xsl:with-param>
		</xsl:call-template>::<xsl:call-template name="print">
			<xsl:with-param name="str_value"><xsl:value-of select="value"/></xsl:with-param>
		</xsl:call-template>'<xsl:if test="position()!=last()">,</xsl:if>
	</xsl:for-each>
</xsl:template>


</xsl:stylesheet>