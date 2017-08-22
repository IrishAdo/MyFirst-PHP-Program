<html>
<?php
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
-	Modified $Date: 2004/12/08 19:27:56 $
-	$Revision: 1.6 $
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
?>
<head>
</head>
<body>
	<iframe src='about:blank' width='450' height='200' style='visibility:visible' id='extract_cache' name='extract_cache'></iframe>
	<form name=frmDoc>
	  <input type=hidden name="frmType" value="menu">
	  <input type=hidden name="image_data" value="">
	  <input type=hidden name="flash_data" value="">
	  <input type=hidden name="menu_data" value="">
	  <input type=hidden name="page_data" value="">
	  <input type=hidden name="file_data" value="">
	  <input type=hidden name="audio_data" value="">
	  <input type=hidden name="movie_data" value="">
	  <input type=hidden name="form_data" value="">
	  <input type=hidden name="webobjects_data" value="">
	  <input type=hidden name="category_data" value="">
	  <input type=hidden name="infodir_data" value="">
	  <input type=hidden name="query_data" value=''>
	  <input type=hidden name="field_data" value=''>
	  <input type=hidden name="field_options" value=''>
	</form>

</body>
<script>
	var base_href;
	var base_path;
	try{
		list = new String(window.parent.location+"").split("/");
		if (list[3].indexOf("~")!=-1){
			base_href = "http://"+list[2]+"/"+list[3]+"/"
			base_path = "/"+list[3]+"/"
			mystart=4
		} else {
			base_href = "http://"+list[2]+"/"
			base_path = "/"
			mystart=3
		}
	}catch(e){
		mystart=-1;
	}
	var session_url='<?php print session_name("LEI")."=".session_id(); ?>'
	if (mystart==-1){
	}else{
		for (i=mystart;i<list.length;i++){
			if (list[i].indexOf('<?php print session_name(); ?>')!=-1){
				l = list[i].split("?");
				file_name = l[0];
				qstring =l[1].split("&");
				for (z=0;z<qstring.length;z++){
					if (qstring[z].indexOf('<?php print session_name(); ?>')!=-1){

						session_url =  qstring[z]
					}
				}
			}
		}
	}
//	alert(session_url)

	function __extract_info(libType,param){
		//alert("["+libType+"]["+param+"]");

		if (extract_cache.document.readyState != 'complete'){
			//setTimeout("__extract_info('"+libType+"', '"+param+"');",1000);
			return;
		}	
		else
		{	
			loc = "#";
			if (libType=='image' && document.frmDoc.image_data.value==''){
				if (param+""=="undefined"){
					loc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&"+session_url;
				} else {
					loc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&"+param+"&"+session_url;
				}
			}
			
			if (libType=='field_options'){
				document.frmDoc.field_options.value="";
				loc = base_href+"admin/load_cache.php?command="+param+"&"+session_url;
			}
			if (libType=='field'){
				document.frmDoc.field_data.value="";
				loc = base_href+"admin/load_cache.php?command="+param+"&type=field&"+session_url;
			}
			if (libType=='flash' && document.frmDoc.flash_data.value==''){
				loc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=flash&"+session_url;
			}
			if (libType=='menu' && document.frmDoc.menu_data.value==''){
				loc = base_href+"admin/load_cache.php?command=LAYOUT_RETRIEVE_LIST_MENU_OPTIONS_DETAIL&"+session_url;
			}
			if (libType=='page' && document.frmDoc.page_data.value==''){
				 loc = base_href+"admin/load_cache.php?command=PAGE_LIST_ALL&"+param+"&"+session_url;
			}
			if (libType=='file' && document.frmDoc.file_data.value==''){
				 loc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=all&"+session_url;
			}
			if (libType=='audio' && document.frmDoc.audio_data.value==''){
				 loc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=audio&"+session_url;
			}
			if (libType=='movie' && document.frmDoc.movie_data.value==''){
				 loc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=movie&"+session_url;
			}
			if (libType=='forms' && document.frmDoc.form_data.value==''){
				 loc = base_href+"admin/load_cache.php?command=SFORM_FORM_EMBED&"+session_url;
			}
			if (libType=='webobjects' && document.frmDoc.webobjects_data.value==''){
				 loc = base_href+"admin/load_cache.php?command=WEBOBJECTS_EXTRACT_OBJECTS&"+param+"&"+session_url;
			}
			if (libType=='category' && document.frmDoc.category_data.value==''){
				 loc = base_href+"admin/load_cache.php?command=CATEGORYADMIN_EXTRACT_LIST&"+param+"&"+session_url;
			}
			if (libType=='infodir' && document.frmDoc.infodir_data.value==''){
				 loc = base_href+"admin/load_cache.php?command=INFORMATIONADMIN_EXTRACT_LIST&"+param+"&"+session_url;
			}
			
			if (libType=='query' && document.frmDoc.query_data.value==''){
				 loc = base_href+"admin/load_cache.php?command=FILTERADMIN_TEST_QUERY&"+param+"&"+session_url;
			}
		//alert(loc);
		//extract_cache.location = loc
		//extract_cache.src = loc
		extract_cache.location.href= loc
		}
	}

	function _exec_function(s,field){
//		alert("_exec_function('"+field+"') = "+s)
		if (field=='menu'){
			document.frmDoc.menu_data.value = s;
		}
		if (field=='page'){
			document.frmDoc.page_data.value = s;
		}
		if (field=='file'){
			document.frmDoc.file_data.value = s;
		}
		if (field=='image'){
			document.frmDoc.image_data.value = s;

		}
		if (field=='flash'){
			document.frmDoc.flash_data.value = s;
		}
		if (field=='audio'){
			document.frmDoc.audio_data.value = s;
		}
		if (field=='movie'){
			document.frmDoc.movie_data.value = s;
		}
		if (field=='forms'){
			document.frmDoc.form_data.value = s;
		}
		if (field=='webobjects'){
			document.frmDoc.webobjects_data.value = s;
		}
		if (field=='category'){
			document.frmDoc.category_data.value = s;
		}
		if (field=='infodir'){
			document.frmDoc.infodir_data.value = s;
		}
		if (field=='query'){
			document.frmDoc.query_data.value = s;
		}
		if (field=='fields'){
			document.frmDoc.field_data.value = s;
		}
		if (field=='field_options'){
			document.frmDoc.field_options.value = s;
		}
//			alert(s);
	}
	function assign_data(type){
	//	alert('assign')
		if (type=='image'){
			if (document.frmDoc.image_data.value!=''){
				tmp 			= new String(document.frmDoc.image_data.value);
				myArray 		 = tmp.split("|1234567890|")
				l				 = myArray.length-1;
				list="";
	//			alert(parent.image_window.all.image_browser.imagesrc.name);
				parent.image_window.all.image_browser.imagesrc.options.length=0
				for (i = 0;i<l;i+=2){
					parent.image_window.all.image_browser.imagesrc.options[document.image_browser.imagesrc.options.length] = new Option(myArray[i], myArray[i+1]);
				}
			} else {
//				alert('not found');
			}
		}
	}
</script>

</html>