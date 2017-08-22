<?php
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- L I B E R T A S   S O L U T I O N S   E D I T O R   -   D I A L O G   
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Modified $Date: 2004/11/15 16:53:26 $
	-	$Revision: 1.5 $
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/	
include '../config/libertas_control.config.php';
include $libertas_locale.'en/locale.php';
include $libertas_locale.'en/locale_general.php';

$session_url= "LEI=".check_parameters($_GET,"LEI","NA");
$domain 	= $_SERVER["HTTP_HOST"];
$base_href 	= "http://$domain".check_parameters($_GET,"base_href","/");

function check_parameters($arr,$ind,$def=""){
	if (isset($arr[$ind])){
		return $arr[$ind];
	} else {
		return $def;
	}
}

$product = check_parameters($_GET,"product","");
/*
	less than 
*/
?>
<html>
<head>
<title>Insert Image</title>
<meta http-equiv="Pragma" content="no-cache">
<link rel="stylesheet" type="text/css" href="/libertas_images/editor/libertas/lib/themes/default/css/dialog.css">
</head>
<body onLoad="Init()">
	<iframe src='about:blank' width='0' height='0' style='visibility:hidden' id='extract_cache2' name='extract_cache2'></iframe>
	<form name="image_browser" method="post" action="">		
		<input type="hidden" name="theme" value="default">		
		<input type="hidden" name="lang" value="en">		
		<input type="hidden" name="images" value="">
		<div style="border: 1 solid Black; padding: 5 5 5 5;">
<p id="tableProps" class="tablePropsTitle"><img src='/libertas_images/editor/libertas/lib/themes/default/img/tb_image_insert.gif'/>Image Insertion Manager</p><table width='450px' border="0">
			<tr>
				<td><strong>Filter Type</strong></td>
				<td>
				<select name='filterType' style='width:300px' onChange='parent.ShowFilter();'>
					<option>Filter by Date</option>
					<option>Filter by Category</option>
				</select></td>
			</tr>
			<tr>
				<td><strong>Filter Option</strong></td>
				<td>
				<select id='filterOption' name='filterOption' style='width:300px' onChange='parent.ShowImageList(0);'>
					<option>Loading filters.....</option>
				</select></td>
			</tr>
			<tr id="ImageList">
				<td><strong>Select Image</strong></td>
				<td>
				<select id='imagesrc' name='imagesrc' style='width:300px' onChange='parent.ShowImage(this.document);'>
					<option>Select filter option first .....</option>
				</select></td>
			</tr>
	
			<tr id="ImageData">
				<td valign="top">
<p>Preview</p><img border="1" src='/libertas_images/themes/1x1.gif' width="130" height="130" id='imagepreview'></td>
				<td valign='top'><table>
			<tr>
				<td colspan="2"><strong>Alt Tag information</strong></td>
			</tr>
			<tr>
				<td colspan="2">				
				<input type=text id='imagealt' size=255 style='width:230px'>				
				<input type=hidden id='imageexisting' value=''></td>
			</tr>
				<tr>
					<td><strong>Alignment </strong></td>
					<td>
					<select id='imagealign' style='width:150'>
						<option value=''>None</option>
						<option value='left'>Left</option>
						<option value='right'>Right</option>
					</select></td>
				</tr>
				<tr>
					<td><strong>Spacing </strong></td>
					<td>
					<select id='imagespacing' style='width:150'>
						<option value='0'>None</option>
						<option value='1'>1px</option>
						<option value='2'>2px</option>
						<option value='3' selected='true'>3px</option>
						<option value='4'>4px</option>
						<option value='5'>5px</option>
						<option value='6'>6px</option>
						<option value='7'>7px</option>
						<option value='8'>8px</option>
						<option value='9'>9px</option>
						<option value='10'>10px</option>
					</select></td>
				</tr>
				<tr><td id='imagesize' colspan="2"><strong>Width:</strong> <em>X px</em>&#160;<strong>Height:</strong> <em>Y px</em><br>
						<strong>Size:</strong><br>
						<strong>Approximate Download Speeds:</strong><br>
						<em>56k:</em> X secs &#160;<em>ISDN:</em> X sec</td></tr>
				</table></td>
			</tr>
			<tr>
				<td>				
				<input ONCLICK="selectClick()" TYPE=button class="bt" ID=idSave  VALUE="<?php echo LOCALE_SELECT; ?>">				
				<input class="bt" ONCLICK="window.close()" TYPE=reset ID=idCancel VALUE="<?php echo LOCALE_CANCEL;?>"></td>
			</tr></table>
		</div>
	</form>
<script language="javascript" src="utils.js"></script>
<script language="javascript"><!--
    window.name = 'imglibrary';
	var myCategoryList = Array();
	var winOpener = window.dialogArguments.document.parentWindow;
	var base_href ='<?php print $base_href; ?>';
	var session_url ='<?php print $session_url; ?>';
	ShowFilter();
Init();
function get_images(){
	if (window.dialogArguments.document.cache_data.document.readyState=='complete'){
		if (window.dialogArguments.document.cache_data.frmDoc.image_data.value!=''){
			if (window.dialogArguments.document.cache_data.frmDoc.image_data.value!='__NOT_FOUND__'){
				tmp 			= new String(window.dialogArguments.document.cache_data.frmDoc.image_data.value);
				window.dialogArguments.document.cache_data.frmDoc.image_data.value="";
				myArray 		= tmp.split("|1234567890|")
				var l				= myArray.length-1;
				list="";
				document.image_browser.imagesrc.options.length=0
				for (var i = 0 ; i < l ; i += 2){
					document.image_browser.imagesrc.options[document.image_browser.imagesrc.options.length] = new Option(
						convert_special_characters(myArray[i],false), 
						convert_special_characters(myArray[i+1],false)
					);
				}
			} else {
				alert("Sorry there are no images available");
				window.dialogArguments.document.cache_data.frmDoc.image_data.value="";
			}
		} else {
			setTimeout("get_images()",1000);
		}
	} else {
		setTimeout("get_images()",1000);
	}
}
    function selectClick()
    {
      if (document.image_browser.imagesrc.selectedIndex>0){
        window.returnValue = save_image();
        window.close();
      } else {
        alert('Please select an image to insert');
      }
    }
    
    function Init()
    {
         resizeDialogToContent();
    }
	
	function get_categories(){
		if (window.dialogArguments.document.cache_data.document.readyState=='complete'){
			if (window.dialogArguments.document.cache_data.frmDoc.category_data.value!=''){
				if (window.dialogArguments.document.cache_data.frmDoc.category_data.value!='__NOT_FOUND__'){
					tmp 			= new String(window.dialogArguments.document.cache_data.frmDoc.category_data.value);
					myArray 		= tmp.split("|1234567890|")
					var l			= myArray.length;
					list="";
					document.image_browser.filterOption.options.length=0
					/* example of categorization feed
						format (parent id::identifier::label)
						'13::14::Images',
						'14::16::Layout Images',
						'14::18::Page Content',
						'14::15::RSS Channels',	
						'13::17::Other'
					*/
					for (var i = 0 ; i < l ; i ++){
						myArray[i] = myArray[i].split("::")
					}
					myCategoryList = myArray;
					rootparent	= myArray[0][0];
					depth 		= 0;
					prev_parent = 0;
					document.image_browser.filterOption.options[document.image_browser.filterOption.options.length] = new Option("Select filter option", 0);
					document.image_browser.filterOption.options[document.image_browser.filterOption.options.length] = new Option("-------------------- Special Filters --------------------", -1);
					document.image_browser.filterOption.options[document.image_browser.filterOption.options.length] = new Option("Show all Images", -1);
					document.image_browser.filterOption.options[document.image_browser.filterOption.options.length] = new Option("Show Uncategorised Images", -1);
					document.image_browser.filterOption.options[document.image_browser.filterOption.options.length] = new Option("-------------------- By Category --------------------", -1);
					define_pulldown(rootparent, depth);
				} else {
					alert("Sorry there are no Categories available");
					ShowImageList(1);
				}
				resizeDialogToContent();
			} else {
				setTimeout("get_categories()", 1000);
			}
		} else {
			setTimeout("get_categories()", 1000);
		}
	}
	function ShowImageList(actiontype){
		if (actiontype==0){
			cat = document.image_browser.filterOption;
			switch (document.image_browser.filterType.selectedIndex){
				case 0:
					if (cat.selectedIndex==0){
					}else{
						imgtag 		= document.getElementById("imagepreview");
						imgtag.src	= '/libertas_images/themes/1x1.gif';
						opt = document.image_browser.imagesrc
						opt.options.length=0;
						opt.options[opt.options.length] = new Option("Please wait downloading list of images.")
						if (cat.selectedIndex==1){
							/* Inorder to cope with IE security restrictions this patch is added */
							iframeloc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&date=1day&"+session_url;	
							extract_cache2.location.href= iframeloc;
						
							 /*IE7 security restrictions would not allow */
							//winOpener.__extract_information('image','date=1day');
							setTimeout("get_images()",1000);
						} else if (cat.selectedIndex==2){
							/* Inorder to cope with IE security restrictions this patch is added */
							iframeloc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&date=1week&"+session_url;	
							extract_cache2.location.href= iframeloc;
						
							 /*IE7 security restrictions would not allow */
							//winOpener.__extract_information('image','date=1week');
							setTimeout("get_images()",1000);
						} else if (cat.selectedIndex==3){
							/* Inorder to cope with IE security restrictions this patch is added */
							iframeloc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&date=4weeks&"+session_url;	
							extract_cache2.location.href= iframeloc;
						
							 /*IE7 security restrictions would not allow */
							//winOpener.__extract_information('image','date=4weeks');
							setTimeout("get_images()",1000);
						} else {
							/* Inorder to cope with IE security restrictions this patch is added */
							iframeloc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&"+session_url;	
							extract_cache2.location.href= iframeloc;
						
							 /*IE7 security restrictions would not allow */
							//winOpener.__extract_information('image');
							setTimeout("get_images()",1000);
						}		
					}
					break
				case 1:
					if ((cat.selectedIndex==0)||(cat.selectedIndex==1)||(cat.selectedIndex==4)){
					}else{
						imgtag 		= document.getElementById("imagepreview");
						imgtag.src	= '/libertas_images/themes/1x1.gif';
						opt = document.image_browser.imagesrc
						opt.options.length=0;
						opt.options[opt.options.length] = new Option("Please wait downloading list of images.")
						if (cat.selectedIndex==2){
							/* Inorder to cope with IE security restrictions this patch is added */
							iframeloc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&"+session_url;	
							extract_cache2.location.href= iframeloc;
						
							 /*IE7 security restrictions would not allow */
							//winOpener.__extract_information('image');
							setTimeout("get_images()",1000);
						} else if (cat.selectedIndex==3){
							/* Inorder to cope with IE security restrictions this patch is added */
							iframeloc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&cat=undefined&"+session_url;	
							extract_cache2.location.href= iframeloc;
						
							 /*IE7 security restrictions would not allow */
							//winOpener.__extract_information('image','cat=undefined');
							setTimeout("get_images()",1000);
						} else {
							/* Inorder to cope with IE security restrictions this patch is added */
							iframeloc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&cat=cat.options[cat.selectedIndex].value&"+session_url;	
							extract_cache2.location.href= iframeloc;
						
							 /*IE7 security restrictions would not allow */
							//winOpener.__extract_information('image','cat='+cat.options[cat.selectedIndex].value);
							setTimeout("get_images()",1000);
						}		
					}
					break
			}
		} else {
			/* Inorder to cope with IE security restrictions this patch is added */
			iframeloc = base_href+"admin/load_cache.php?command=FILES_FILTER&type=image&"+session_url;	
			extract_cache2.location.href= iframeloc;
		
			 /*IE7 security restrictions would not allow */
			//winOpener.__extract_information('image');
			setTimeout("get_images()",1000);
		}
	}

	function define_pulldown(elementparent, mydepth){
		for (var i=0 ; i < myCategoryList.length; i++){
			if (myCategoryList[i][0] == elementparent){
				mylabel = myCategoryList[i][2];
				var str="";
				for(var myx=0; myx< mydepth; myx++){
					str+= "&nbsp;-&nbsp;" 
				}
				mylabel = str + mylabel;
				document.image_browser.filterOption.options[document.image_browser.filterOption.options.length] = new Option(
					convert_special_characters(mylabel,false), 
					convert_special_characters(myCategoryList[i][1],false)
				);
				myparentelement = myCategoryList[i][1];
				define_pulldown(myparentelement , mydepth+1);
			}
		}
	}

	function ShowFilter(){
		switch (document.image_browser.filterType.selectedIndex){
			case 0:
				opt = document.image_browser.filterOption
				opt.options.length=0;
				opt.options[opt.options.length] = new Option("Select filter option");
				opt.options[opt.options.length] = new Option("Show images less than 1 day old");
				opt.options[opt.options.length] = new Option("Show images less than 1 week old");
				opt.options[opt.options.length] = new Option("Show images less than 4 weeks old");
				opt.options[opt.options.length] = new Option("Show all images");
				
				opt = document.image_browser.imagesrc
				opt.options.length=0;
				opt.options[opt.options.length] = new Option("Select filter option first .....");//Please wait downloading list of images.")
				
				imgtag 		= document.getElementById("imagepreview");
				imgtag.src	= '/libertas_images/themes/1x1.gif';
				break
			case 1:
				opt = document.image_browser.imagesrc
				opt.options.length=0;
				opt.options[opt.options.length] = new Option("Select filter option first .....");//Please wait downloading list of images.")
				opt = document.image_browser.filterOption
				opt.options.length=0;
				opt.options[opt.options.length] = new Option("Select filter option");

				/* Inorder to cope with IE security restrictions this patch is added */
				//iframeloc = base_href+"admin/load_cache.php?command=CATEGORYADMIN_EXTRACT_LIST&type=category&module=FILES_&"+session_url;	
				iframeloc = base_href+"admin/load_cache.php?command=CATEGORYADMIN_EXTRACT_LIST&module=FILES_&"+session_url;	
				extract_cache2.location.href= iframeloc;
			
				 /*IE7 security restrictions would not allow */
				//winOpener.__extract_information('category','module=FILES_');
				setTimeout("get_categories()",1000);
				imgtag 		= document.getElementById("imagepreview");
				imgtag.src	= '/libertas_images/themes/1x1.gif';
				break
		}
	}
	//	extract_cache.location = base_href+"admin/load_cache.php?command=FILES_FILTER&filter=image&"+session_url;


	function _exec_function(s,field){
//		alert("_exec_function('"+field+"') = "+s)
		if (field=='menu'){
			window.dialogArguments.document.cache_data.frmDoc.menu_data.value = s;
		}
		if (field=='page'){
			window.dialogArguments.document.cache_data.frmDoc.page_data.value = s;
		}
		if (field=='file'){
			window.dialogArguments.document.cache_data.frmDoc.file_data.value = s;
		}
		if (field=='image'){
			window.dialogArguments.document.cache_data.frmDoc.image_data.value = s;

		}
		if (field=='flash'){
			window.dialogArguments.document.cache_data.frmDoc.flash_data.value = s;
		}
		if (field=='audio'){
			window.dialogArguments.document.cache_data.frmDoc.audio_data.value = s;
		}
		if (field=='movie'){
			window.dialogArguments.document.cache_data.frmDoc.movie_data.value = s;
		}
		if (field=='forms'){
			window.dialogArguments.document.cache_data.frmDoc.form_data.value = s;
		}
		if (field=='webobjects'){
			window.dialogArguments.document.cache_data.frmDoc.webobjects_data.value = s;
		}
		if (field=='category'){
			window.dialogArguments.document.cache_data.frmDoc.category_data.value = s;
		}
		if (field=='infodir'){
			window.dialogArguments.document.cache_data.frmDoc.infodir_data.value = s;
		}
		if (field=='query'){
			window.dialogArguments.document.cache_data.frmDoc.query_data.value = s;
		}
		if (field=='fields'){
			window.dialogArguments.document.cache_data.frmDoc.field_data.value = s;
		}
		if (field=='field_options'){
			window.dialogArguments.document.cache_data.frmDoc.field_options.value = s;
		}
//			alert(s);
	}

//-->
</script>
</body>
</html>
