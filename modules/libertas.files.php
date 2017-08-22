<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.files.php
* @date 09 Oct 2002
*/
/**
* this module should allow the engine to authenticate files when they
* log into the system
*/
class files extends module{
	/**
	*  Class Variables 
	*/
	var $module_name	 = "files";
	var $module_label	 = "MANAGEMENT_FILES";
	var $module_name_label		= "File Management Module";
	var $module_grouping = "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_admin	 = "1";
	var $module_debug	 = false;
	var $module_creation = "10/12/2002";
	var $module_modify	 = '$Date: 2005/02/15 12:03:28 $';
	var $module_version	 = '$Revision: 1.40 $';
	var $module_command	 = "FILES_";	// all commands specifically for this module will start with this token
	var $webContainer	 = "FILES_"; // this is used incase this module splits into admin and presentation modules
	var $display_options=array(
		array (0,FILTER_ORDER_DATE_NEWEST		, "file_info.file_creation_date desc"),
		array (1,FILTER_ORDER_DATE_OLDEST		, "file_info.file_creation_date asc"),
		array (2,FILTER_ORDER_FILE_NAME_A_Z		, "file_info.file_name asc"),
		array (3,FILTER_ORDER_FILE_NAME_Z_A		, "file_info.file_name desc")
	);
	
	var $module_admin_options = array();
	
	var $module_admin_user_access = array(
		array("FILES_ALL",				"COMPLETE_ACCESS"),
		array("FILES_UPLOADER",			"ADD_NEW"),
		array("FILES_ADMIN",			"FILE_EDIT_REMOVE"),
		array("FILES_IMPORTER",			"FILE_FTP_IMPORTER"),
		array("FILES_UPLOAD_MULTIPLE",	"FILE_ADD_MULTIPLE"),
		array("FILES_LIST_GENERATOR",	"FILES_LIST_GENERATOR")
	);
	
	var $module_admin_access = 0;

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- File Module Preferences
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	var	$preferences = Array(
			Array("sp_force_download"		,"LOCALE_SP_FORCE_DOWNLOAD"			,"No"		, "Yes:No"					, "FILES_",	"ALL"),
			Array("sp_force_download_login"	,"LOCALE_SP_FORCE_DOWNLOAD_LOGIN" 	,"No"		, "Yes:No"					, "FILES_",	"ALL"),
			Array("sp_file_list_format"		,"LOCALE_SP_FILE_LIST_FORMAT"		,"Title Only"	, "Table:List:Title and Summary:Title Only", "FILES_", "ALL")
		);

	/**
	*  mime_images
	*/
	var $mime_images = Array(
    	Array("pdf",":apf::api::dpdf::fdf::not::pdf::pdp::pdx::rmi::sequ::udc::xfdf:","pdf"),
   		Array("ppt",":mpp::mpt::pot::ppa::pps::ppt::pwz:","powerpoint"),
    	Array("rm",":ra::ram::rjs::rm::rmm::rmp::rms::rmx::rnx::rsml::rv::smi::smil::ssm:","audio"),
    	Array("avi",":aif::rmi::aifc::aiff::asf::asx::au::audiocd::avi::cda::dvd::it::itz::ivf::kar::m1v::m3u::mid::midi::miz::mmm::mod::mp1::mp2::mp2v::mp3::mpa::mpv2::mswmm::mtm::pls::s3m::s3z::snd::stm::stz::ult::voc::wal::wav::wax::wm::wma::wmd::wmp::wms::wmv::wmx::wmz::wvx::xm::xmz:","audio"),
    	Array("avi",":avi::dvd::mpe::mpeg::mpg::mov:","movie"),
    	Array("tif",":ai::cdr::cdt::clk::cmx::cpt::csf::csh::ecs::emf::eps::fh10::fhx::ft10::gif::image2::img::jfif::jpe::jpeg::jpg::out.txt::pat::pcd::pct::pcx::pic::pict::png::psd::psf::pxr::raw::rmf::shc::tba::tif::tiff::tpl::vdx::vsd::vsl::vss::vsu::vsw::vsx::wbmp::wmf::wsp::xmp:","image"),
    	Array("htm",":htm::html::dwr::dwt::its::mht::mhtml::pothtml::ppthtml::pptmhtml:","html"),
    	Array("xls",":csv::dif::mpd::slk::xla::xlb::xlc::xld::xlk::xll::xlm::xls::xlshtml::xlsmhtml::xlt::tazhtml::xlv::xlw:","excel"),
    	Array("swf",":fla::spa::spl::swd::swf::swt:","flash"),
    	Array("db",":mas::mda::mdb::mdbhtml::mde::mdn::mdt::mdw::mdz::obd::obt::obz::wizhtml::db:","database"),
    	Array("zip",":zip::ace::arj::BHX::bz2::bz::cab::gz::iso::jar::lha::lzh::r00::rar::tar::taz::tbz2::tgz::tz::uu::uue::xxe::zip:","compression"),
    	Array("doc",":doc::dochtml::docmhtml:dot::dothtml::rtf::wbk::wiz::wll:","word"),
   	 	Array("txt",":txt::sql::nfo:","text"),
   	 	Array("css",":css:","css"),
   	 	Array("aim",":aim:","aim"),
   	 	Array("chm",":chm::chf:","help"),
  	 	Array("class",":class::java:","java"),
    	Array("exe",":exe::msi:","executable"),
   	 	Array("xml",":xml::xsl::xslt:","xml")
	);
	/**
	*  ROLES 
	*/
	
	var $list_generator			= 0;
	var $add_access				= 0;
	var $manage_access			= 0;
	var $add_category			= 0;
	var $upload_multiple_access	= 0;
	var $ftp_upload_access		= 0;
	
	/**
	*  Class Methods
	*/
	
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*/
		if (strpos($user_command,$this->module_command)===0){
			if ($user_command==$this->module_command."DEBUG_ON"){
				$this->module_debug=true;
			}
			if ($user_command==$this->module_command."DEBUG_OFF"){
				$this->module_debug=false;
			}
			if ($user_command==$this->module_command."GET_MODULE"){
				return $this->get_module_name();
			}
			if ($user_command==$this->module_command."GET_VERSION"){
				return $this->get_module_version();
			}
			if ($user_command==$this->module_command."GET_AUTHOR"){
				return $this->get_module_author();
			}
			if ($user_command==$this->module_command."GET_PREFS"){
				return $this->preferences;
			}
			if ($user_command==$this->module_command."GET_CREATION"){
				return $this->get_module_creation();
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if($this->module_admin_access==1){
				/*************************************************************************************************************************
                * basic admin only functions
                *************************************************************************************************************************/
				if ($user_command==$this->module_command."IMPORT_IMAGE_FROM_DIRECTORY_IMPORT"){
					return $this->file_import_image($parameter_list);
				}
				if ($user_command==$this->module_command."MANAGE_MODULE"){
					return $this->manage_module_relationship($parameter_list);
				}
				if ($user_command==$this->module_command."TO_OBJECT_COPY"){
					return $this->to_object_copy($parameter_list);
				}
				if ($user_command==$this->module_command."LIST_IMAGES"){
					$parameter_list["file_mime"]="image";
					$parameter_list["lock_mime"]=1;
					$parameter_list["onlyone"]=1;
					return $this->file_list($parameter_list);
				}
				if ($user_command==$this->module_command."LIST_REMOVE_NO_REFRESH"){
					$this->remove_confirm($parameter_list);
				}					
				
				/*************************************************************************************************************************
                * role based functions
                *************************************************************************************************************************/
				if ($this->list_generator==1){
					/**
                    * management of downloadable files  locked to role
                    */
					if ($user_command==$this->module_command."LIST_GENERATOR"){
						return $this->file_list_generator($parameter_list);
					}
					if (($user_command==$this->module_command."LIST_ADD") || ($user_command==$this->module_command."LIST_EDIT")){
						return $this->file_list_modify($parameter_list);
					}
					if ($user_command==$this->module_command."LIST_REMOVE_CONFIRM"){
						$this->file_list_remove($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=FILES_LIST_GENERATOR"));
					}
					if ($user_command==$this->module_command."LIST_SAVE"){
						$this->file_list_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=FILES_LIST_GENERATOR"));
					}
				}
				if ($user_command==$this->module_command."LIST_ITEMS"){
					return $this->files_list_items($parameter_list);
				}
				if($this->add_access==1 || $this->manage_access==1){
					if ($user_command==$this->module_command."LIST"){
						return $this->file_list($parameter_list);
					}
					if ($user_command==$this->module_command."FILTER"){
						return $this->file_get_filter($parameter_list);
					}
					if ($user_command==$this->module_command."DRAW_FILTER"){
						return $this->file_filter($parameter_list);
					}
					if($this->add_access==1){
						if (($user_command==$this->module_command."ADD")){
							return $this->file_form($parameter_list);
						}
					}
					if($this->manage_access==1){
						if (($user_command==$this->module_command."EDIT")){
							return $this->file_form($parameter_list);
						}
					}
					if ($user_command==$this->module_command."SAVE"){
						$this->save($parameter_list);
						$onlyone		= $this->check_parameters($parameter_list,"onlyone");
						if ($onlyone!=""){
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=FILES_LIST&amp;onlyone=$onlyone"));
						} else {
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=FILES_LIST"));
						}
					}
					if ($user_command==$this->module_command."REMOVE"){
						return  $this->remove_file_screen($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE_CONFIRM"){
						$this->remove_confirm($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=FILES_LIST"));
					}
				}
				if ($user_command==$this->module_command."VIEW_DOWNLOAD_REPORT"){
					return $this->file_download_report($parameter_list);
				}
				
				/* Tmp Function just to fix number of file downloads (added By Muhammd Imran) */	
				if ($user_command==$this->module_command."TMP_IMPORT_CONTACT_ID_FOR_FILE_DOWNLOADS"){
					return $this->tmp_import_contact_id_for_file_downloads();
				}
				
				if (($user_command==$this->module_command."VIEW_FILE_DOWNLOADS") || ($user_command==$this->module_command."VIEW_USERS_DOWNLOADS")){
					return $this->file_download_report_downloaded($parameter_list);
				}
				if ($user_command==$this->module_command."MOVE_TO_MENU"){
					return $this->file_move_to_menu_location($parameter_list);
				}
				if($this->ftp_upload_access==1){
					if ($user_command==$this->module_command."IMPORT"){
						return $this->import_list($parameter_list);
					}
					if ($user_command==$this->module_command."ENTRY_IMPORT"){
						return $this->save_import_list($parameter_list);
					}
				}
			}
			
			if ($user_command==$this->module_command."DOWNLOAD_DISPLAY"){
				return $this->files_download_display($parameter_list);
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."LIST_FILE_DETAIL"){
				 return $this->module_list_file_details($parameter_list);
			}
			if ($user_command==$this->module_command."LIST_SUMMARY_FILE_DETAIL"){
				 return $this->module_list_summary_file_details($parameter_list);
			}
			if ($user_command==$this->module_command."DOWNLOAD"){
				return $this->file_download($parameter_list);
			}
			if ($user_command==$this->module_command."STREAM"){
				return $this->file_stream($parameter_list);
			}
			if ($user_command==$this->module_command."INFO"){
				return $this->file_info($parameter_list);
			}
			if ($user_command==$this->module_command."GET_MIME_IMAGE"){
				return $this->get_mime_image($this->check_parameters($parameter_list,0));
			}
			if ($user_command==$this->module_command."RETRIEVE_FROM_OBJECT"){
				return $this->retrieve_module_relationship($parameter_list);
			}
			/*************************************************************************************************************************
            * file to object functions
            *************************************************************************************************************************/
			if ($user_command==$this->module_command."GET_OBJECT"){
				return $this->get_module_relationship($parameter_list);
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function initialise(){
//	print "FILE MODULE loaded";
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		$this->load_locale("file_admin");
		$this->list_generator=0;
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$this->module_admin_options = array(
				array("FILES_ADD","FILES_ADDNEW","FILES_UPLOADER|FILES_ADMIN"),
				array("FILES_LIST","FILES_LIST","FILES_UPLOADER|FILES_ADMIN"),
				array("FILES_IMPORT","FILE_IMPORT","FILES_UPLOADER|FILES_ADMIN|FILES_FTP_IMPORTER"),
				array("FILES_LIST_GENERATOR","FILES_LIST_GENERATOR","FILES_LIST_GENERATOR")
			);
		} else {
			$this->module_admin_options = array(
				array("FILES_LIST","MANAGEMENT_FILES","FILES_UPLOADER|FILES_ADMIN")
			);
			if ($this->parent->server[LICENCE_TYPE]==MECM){
				$this->module_admin_options[count($this->module_admin_options)] = array("FILES_LIST_GENERATOR","FILES_LIST_GENERATOR","FILES_LIST_GENERATOR");
			
			}
		}
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			if (in_array("ALL",$access)){
				$this->add_access						= 1;
				$this->add_category 					= 1;
				$this->manage_access					= 1;
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					$this->upload_multiple_access		= 1;
					$this->ftp_upload_access 			= 1;
				}
				if ($this->parent->server[LICENCE_TYPE]==MECM){
					$this->list_generator 				= 1;
				}
			}
			if (in_array("FILES_ALL",$access)){
				$this->list_generator 					= 1;
				$this->add_access						= 1;
				$this->manage_access					= 1;
				$this->add_category 					= 1;
				if ($this->parent->server[LICENCE_TYPE]==MECM){
					$this->list_generator 				= 1;
				}
				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					$this->upload_multiple_access		= 1;
					$this->ftp_upload_access 			= 1;
					$this->list_generator 				= 1;
				}
			}
			if (in_array("FILES_UPLOADER",$access)){
				$this->add_access						= 1;
			}
			if (in_array("FILES_ADMIN",$access)){
				$this->manage_access					= 1;
			}
			if ($this->parent->server[LICENCE_TYPE]==ECMS){
				if (in_array("FILES_UPLOAD_MULTIPLE",$access)){
					$this->upload_multiple_access		= 1;
				}
				if (in_array("FILES_IMPORTER",$access)){
					$this->ftp_upload_access 				= 1;
				}
				if (in_array("FILES_LIST_GENERATOR",$access)){
					$this->list_generator 				= 1;
				}
			}
			if (in_array("CATEGORYADMIN_ALL",$access)){
				$this->add_category						= 1;
			}
		}
		//CATEGORYADMIN_ALL
		if ((($this->list_generator == 1) || ($this->add_access == 1) || ($this->add_category == 1)) && (($this->parent->module_type=="admin")||($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files"))){
			$this->module_admin_access=1;
		}
//		print "<li>[$this->list_generator, $this->add_access, $this->manage_access, $this->add_category, $this->list_generator, $this->upload_multiple_access, $this->ftp_upload_access, $this->list_generator]</li>";

	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function file_list($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"file_list",__LINE__,print_r($parameters,true)));
		}
		$orderby=0;
		$page_boolean = "or";
		$where="";
		$join = "";
		if (!empty($parameters["filter_category"])){
			if ($parameters["filter_category"]!="ALL"){
				$where .=" cto_clist ='".$parameters["filter_category"]."' and ";
				$join = " inner join category_to_object on (cto_module='FILES_' and cto_client=file_info.file_client and cto_object=file_info.file_identifier)";
			}
		}
		$file_mime  = $this->check_parameters($parameters,"file_mime");
		if ($file_mime!=""){
			$where .=" file_mime like '%".$parameters["file_mime"]."%' and ";
		}
		if (!empty($parameters["search_phrase"])){
			if (strlen($parameters["search_phrase"])>0){
				$search=1;
				$where_title = "";
				$where_body = "";
				$where_summary = "";
				$words = split(" ",$parameters["search_phrase"]);
				for($index=0,$len=count($words);$index<$len;$index++){
					if ($index>0){
						$where_title .= " $page_boolean";
						$where_body .= " $page_boolean";
						$where_summary .= " $page_boolean";
					}
					$where_title .= " file_label like '%".$words[$index]."%'";
					$where_body .= " file_name like '%".$words[$index]."%'";
					$where_summary .= " file_description like '%".$words[$index]."%'";
				}
				$where .= "(($where_title) or ($where_body) or ($where_summary)) and ";
			}
		}
		if (empty($parameters["order_filter"])){
			$parameters["order_filter"]=0;
		}
		$order_by = "order by ".$this->display_options[$parameters["order_filter"]][2];

		$sql = "Select 
					file_info.file_identifier, count(file_access_to_page.file_identifier) as links 
				from file_info 
					left outer join file_access_to_page on file_info.file_identifier=file_access_to_page.file_identifier 
					$join
				where 
					$where file_info.file_client=$this->client_identifier
				GROUP BY 
					file_info.file_identifier, file_info.file_creation_date
				$order_by";
				
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables = Array();
		$variables["FILTER"]			= $this->filter($parameters);
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		$variables["ONLY_ONE"]  		= $this->check_parameters($parameters,"onlyone");
		if($this->add_access==1){
			$variables["PAGE_BUTTONS"] = Array(
				Array("ADD",$this->module_command."ADD",ADD_NEW,"","","","")
			);
		} else {
			$variables["PAGE_BUTTONS"] = Array();
		}
		if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$page = $this->check_parameters($parameters,"page","1");
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$goto = ((--$page)*$this->page_size);
			
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
			}
			if ($goto+$this->page_size>$number_of_records){
				$finish = $number_of_records;
			}else{
				$finish = $goto+$this->page_size;
			}
			$goto++;
			$page++;
			
			$num_pages=floor($number_of_records / $this->page_size);
			$remainder = $number_of_records % $this->page_size;
			if ($remainder>0){
				$num_pages++;
			}
			
			$counter=0;
			$variables["PAGE_COMMAND"] = "FILES_LIST";
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			
			$variables["RESULT_ENTRIES"] =Array();
			$list ="";
			$links = Array();
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
				if (strlen($list)>0){
					$list .=", ";
				}
				$list .=  $r["file_identifier"];
				$links[$r["file_identifier"]] = $r["links"];
			}
			$sql = "select * from file_info where file_identifier in ($list) $order_by";
			$result = $this->call_command("DB_QUERY",array($sql));
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) && ($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				
				$variables["RESULT_ENTRIES"][$i]=Array(
				"identifier"		=> $r["file_identifier"],
				"ENTRY_BUTTONS" 	=> Array(),
				"attributes"		=> Array(
						Array(FILE_TYPE,strtolower($this->get_mime_image($r["file_name"])),"ICON","NO"),
						Array(LOCALE_FILE_LABEL,$this->convert_amps($r["file_label"]),"TITLE","NO"),
						Array("", $this->convert128($r["file_name"]), "FILE", "NO"),
						Array("", $r["file_mime"], "FILE_TYPE", "NO"),
						Array(LOCALE_FILE_DESCRIPTION, $this->show_255($r["file_description"]),"SUMMARY"),
						Array(FILE_NAME_AND_LOCATION, $this->convert_amps($this->check_parameters($r,"file_name")),"SUMMARY"),
						Array(LOCALE_FILE_SIZE, $r["file_size"]),
						Array(LOCALE_FILE_DOWNLOAD_TIMES,$this->check_parameters($r,"file_dl_sec",0)." download"),
						Array(LOCALE_FILE_ASSOCIATION,$links[$r["file_identifier"]]." page(s)"),
						Array(LOCALE_FILE_EMBEDDED,$this->get_embed_information(Array("type"=>"count","tag"=>$r["file_md5_tag"]))." page(s)"),
						Array(LOCALE_FILE_DOWNLOADS,$this->file_download_counter(Array("file_identifier"=>$r["file_identifier"],"type"=>"count"))." time(s)")
					)
				);
				$dir_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"]));
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("PREVIEW",$this->module_command."DOWNLOAD&amp;download=".$r["file_md5_tag"],LOCALE_PREVIEW);
				if($this->manage_access==1){
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT",$this->module_command."EDIT",EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE",$this->module_command."REMOVE_CONFIRM",REMOVE_EXISTING);
				}
			
			}
		}

		$out = $this->generate_list($variables);
		return $out;
	}
	
	function get_embed_information($parameters){
		$type	= $this->check_parameters($parameters, "type", "ALL");
		$tag	= $this->check_parameters($parameters, "tag", -1);
		$sql 	= "select count(*) as Total from embed_libertas_file where client_identifier=$this->client_identifier and file_tag='$tag'";
		$total=0;
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$total += $r["Total"];
		}
		$sql 	= "select count(*) as Total from embed_libertas_image where client_identifier=$this->client_identifier and image_tag='$tag'";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$total += $r["Total"];
		}
		return $total;
	}
	
	/**
	* function produces abstract structure of modules db structure
	*
    * used to generate the table structure required abstract function that passes an abstract
	* representation of the desired table structure to the proper database module which will
	* interpet the abstract and convert it into a valid SQL Create table structure.
	*
	* @return Array list of abstract table definitions for this module
	*/
	function create_table(){
		$tables = array();
		/**
		* Table structure for table 'File_info'
		*/
		
		$fields = array(
			array("file_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("file_label"			,"varchar(255)"		,"NULL"		,"default ''"),
			array("file_name"			,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("file_directory"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("file_mime"			,"varchar(50)"		,"NULL"		,"default ''"),
			array("file_creation_date"	,"datetime"			,"" 		,"default NULL"),
			array("file_client"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("file_size"			,"varchar(50)"		,"NOT NULL"	,"default ''"),
			array("file_width"			,"varchar(5)"		,"NOT NULL"	,"default ''"),
			array("file_height"			,"varchar(5)"		,"NOT NULL"	,"default ''"),
			array("file_user"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("file_description"	,"text"				,""			,"default ''"),
			array("file_md5_tag"		,"varchar(32)"		,""			,"default ''"),
			array("file_dl_sec"			,"varchar(20)"		,""			,"default ''"),
			array("file_data"			,"blob"				,""			,"default ''")
//			array("file_user"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
		);
		$primary ="file_identifier";
		$tables[count($tables)] = array("file_info", $fields, $primary);
		
		$fields = array(
			array("file_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("trans_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("client_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("file_rank"			,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);

		$primary ="";
		$tables[count($tables)] = array("file_access_to_page", $fields, $primary);
		/*************************************************************************************************************************
        * allow linking of files to other module objects
        *************************************************************************************************************************/
		$fields = array(
			array("fto_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("fto_object"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("fto_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("fto_file"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("fto_rank"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("fto_module"		,"varchar(255)"	,""	,"default ''"),
			array("fto_title"		,"varchar(255)"	,""	,"default ''")
		);
		$primary ="fto_identifier";
		$tables[count($tables)] = array("file_to_object", $fields, $primary);
		/*************************************************************************************************************************
        * store file download data
        *************************************************************************************************************************/
		$fields = array(
			array("file_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("user_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("client_identifier"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("download_time"		,"datetime"			,"NULL"		,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("file_downloads", $fields, $primary);

		$fields = array(
			array("fi_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("fi_menu_only"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),	// 0 = available on pages (leaf nodes)
			array("fi_client"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("fi_display"			,"varchar(255)"		,"NOT NULL"	,"default 'List'"),	// display option (overrides syspref)
			array("fi_set_inheritance"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("fi_all_locations"	,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("fi_status"			,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("fi_label"			,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("fi_date_created"		,"datetime"			,"NOT NULL"	,"default ''"),
			array("fi_grouping"			,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);

		$primary ="fi_identifier";
		$tables[count($tables)] = array("file_list", $fields, $primary);

		return $tables;
		//file_associations
	}
	
	function file_form($parameters){
		$command		= $this->check_parameters($parameters,"command");
		$identifier 	= $this->check_parameters($parameters,"identifier",-1);
		$file_directory	= $this->check_parameters($parameters,"file_directory");
		$file_name		= $this->check_parameters($parameters,"file_name");
		$file_label		= $this->check_parameters($parameters,"file_label");
		$file_size		= $this->check_parameters($parameters,"file_size");
		$choices		= $this->check_parameters($parameters,"choices");
		$file_tag		= $this->check_parameters($parameters,"file_tag");
		$display_tab	= $this->check_parameters($parameters,"display_tab");
		$onlyone		= $this->check_parameters($parameters,"onlyone");
		$file_description	= "";
		$associated_list= $this->check_parameters($parameters,"associated_list");
		if ($this->parent->script =="admin/file_associate.php"){
			$_SESSION["associated_list"]	= $associated_list;
		} else {
			unset($_SESSION["return_hidden"]);
			unset($_SESSION["return_note"]);
			unset($_SESSION["return_command"]);
			unset($_SESSION["associated_list"]);
		}
		$label=ADD_NEW;
		$category_list = $this->call_command("CATEGORYADMIN_RETRIEVE_LIST", 
			Array(
				"module"=>"FILES_", 
				"label"=>"Choose the Virtual folders this file is available in",
				"add"=>"Add new Virtual Folder"
			)
		);
		if ($identifier!=-1){
			$sql = "Select * from file_info where file_identifier=".$identifier." and file_client=$this->client_identifier";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$file_name			= $this->file_extension($r["file_name"]);
					$file_directory		= $this->convert_amps($r["file_directory"]);
					$file_mime			= $r["file_mime"];
					$file_size			= $r["file_size"];
					$file_label			= $this->convert_amps($r["file_label"]);
					$file_description	= $this->convert_amps($r["file_description"]);
					$file_tag			= $r["file_md5_tag"];
				}
			}
			$label=LOCALE_FILE_EDIT_LABEL;
		}
		
		$required = "required=\"yes\"";
		$required = "";
		
		$directories = $this->call_command("LAYOUT_DISPLAY_UPLOAD_DIRECTORY",array(-1,$file_directory));
		$out  = "<module name=\"files\" display=\"form\">";
		$out .= "<page_options>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","FILES_LIST",LOCALE_CANCEL));
		$out .= "</page_options>";
		if (strlen($directories)>0){
			$out .= "<form name=\"file_form\" method=\"post\" label=\"$label\">";
			$out .= "<input type=\"hidden\" name=\"file_identifier\" value=\"".$identifier."\"/>";
			$out .= "<input type=\"hidden\" name=\"prev_command\" value=\"$command\"/>";
			$out .= "<input type=\"hidden\" name=\"command\" value=\"FILES_SAVE\"/>";
			$out .= "<input type=\"hidden\" name=\"previous_file\" value=\"$file_name\"/>";
			$out .= "<input type=\"hidden\" name=\"previous_dir\" value=\"$file_directory\"/>";
			$out .= "<input type=\"hidden\" name=\"tag\" value=\"$file_tag\"/>";
			$out .= "<input type=\"hidden\" name=\"onlyone\" value=\"$onlyone\"/>";
			$out .= "<page_sections>";
			$out .= "	<section label=\"Primary File\"";
			if ($display_tab==""){
				$out .= " selected='true'";
			}
			$out .= ">";
			$out .= "<input type=\"text\" label=\"".WHAT_LABEL."\" name=\"file_label0\" $required><![CDATA[$file_label]]></input>";
			if($identifier!=-1){
				$choices  = "<choice name=\"file_upload_radio\" value=\"__KEEP__\" label=\"".NO_KEEP."\" checked=\"true\" visibility=\"hidden\"/>";
				$choices .= "<choice name=\"file_upload_radio\" value=\"__REMOVE__\" label=\"".YES_REMOVE."\" checked=\"\" visibility=\"hidden\"/>";
				$choices .= "<choice name=\"file_upload_radio\" value=\"__REPLACE__\" label=\"".YES_REPLACE."\" checked=\"\" visibility=\"visible\"/>";
				$required="";
			} else {
//				$required="required=\"YES\"";
			}
			$out .= "<input type=\"file\" file_size=\"$file_size\" label=\"".LOCALE_FILE_NAME."\" size=\"20\" name=\"file_name0\" value=\"$file_tag\" $required>$choices</input>";
			$out .= "<textarea type=\"plain-text\" $required label=\"".SHORT_DESCRIPTION."\" size=\"40\" height=\"5\" name=\"file_description0\"><![CDATA[$file_description]]></textarea>";
			if (is_numeric($directories)){
				$out .="<input type=\"hidden\" name=\"file_directory0\"><![CDATA[$directories]]></input>\n";
			}else{
				$out .="<select label=\"".FILE_DIRECTORY."\" name=\"file_directory0\">$directories</select>\n";
			}
			if ($category_list!=""){
				$out .= "	</section><section label=\"".LOCALE_FILES_CATEGORY_TAB."\"";
				if ($display_tab == "categories"){
					$out .= " selected='true'";
				}
				$out .= "><text><![CDATA[".LOCALE_FILES_CATEGORY_MSG."]]></text>";
				$out .= "$category_list";
				if($identifier!=-1){
					$out .= $this->call_command("CATEGORYADMIN_TO_OBJECT_LIST", 
						Array(
							"module"		=>	"FILES_",
							"identifier"	=>	$identifier,
							"returntype"	=>	1
						)
					);
				}
			}
			if ($this->upload_multiple_access==1 && $identifier==-1){
				$out .= "</section><section label=\"Upload Multiple\">";
				$out .= "<text><![CDATA[You have been given the <strong>permission</strong> to upload multiple files.  You can upload up to 5 additionial files.<br/>You <strong>must</strong> also define the Primary File Information or these files will not be uploaded. You will find the primary information located on the first tab]]></text>";
				for($i=1;$i<=5 ;$i++){
					$out .= "<text><![CDATA[]]></text>";
					$out .= "<input type=\"text\" label=\" #".$i.") ".WHAT_LABEL."\" name=\"file_label".$i."\"><![CDATA[]]></input>";
					$out .= "<input type=\"file\" file_size=\"$file_size\" label=\"".LOCALE_FILE_NAME."\" size=\"20\" name=\"file_name".$i."\" value=\"\" ></input>";
					$out .= "<textarea type=\"plain-text\" label=\"".SHORT_DESCRIPTION."\" size=\"40\" height=\"5\" name=\"file_description".$i."\"><![CDATA[]]></textarea>";
					if (is_numeric($directories)){
						$out .="<input type=\"hidden\" name=\"file_directory".$i."\"><![CDATA[$directories]]></input>\n";
					}else{
						$out .="<select label=\"".FILE_DIRECTORY."\" name=\"file_directory".$i."\">$directories</select>\n";
					}
				}
			}
			$out .= "	</section>";
			$out .= "</page_sections>";
			$out .= "<input iconify=\"SAVE\" type=\"submit\" value=\"".SAVE_DATA."\"/>";
			$out .= "</form>";
		} else {
			$out .= "<form name=\"file_form\" method=\"post\" label=\"".LOCALE_SORRY."\">";
			$out .= "<text><![CDATA[LOCALE_SORRY_FILE_MSG]]></text>";
			$out .= "</form>";
		}
		$out .= "</module>";
		return $out;
		
	}
	
	function file_get_filter($parameter){
		$debug		= false;
		$sql		= "";
		$found		= "";
		$cond		= "";
		$join 		= "";
		$where 		= "";
		$filter 	= $this->check_parameters($parameter,"filter");
		$date		= $this->check_parameters($parameter,"date", "");
		$maxwidth	= $this->check_parameters($parameter,"maxwidth", -1);
		$maxheight	= $this->check_parameters($parameter,"maxheight", -1);
		if ($maxwidth>-1){
			$where .= " file_width<=$maxwidth and ";
		}
		if ($maxheight>-1){
			$where .= " file_height<=$maxheight and ";
		}
//		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			// the Enterprise module has categorisation as standard 
			$category	= $this->check_parameters($parameter,"cat", -1);
			if ($category!=-1){
				if ($category == "undefined"){
					$join = " left outer join category_to_object on cto_module='FILES_' and cto_object = file_identifier and cto_client=file_client";
					$where .=" cto_clist is null and ";
				} else {
					$join = " inner join category_to_object on cto_module='FILES_' and cto_object = file_identifier and cto_client=file_client";
					$where .=" cto_clist = $category and ";
				}
			}
	//	}
		if ($date!=""){
			if($date=="1day"){
				$now = $this->libertasGetDate("Y/m/d H:i:s",time()-86400);
				$where .= " file_creation_date > '$now' and ";
			}
			if($date=="1week"){
				$now = $this->libertasGetDate("Y/m/d H:i:s",time()-604800);
				$where .= " file_creation_date > '$now' and ";
			}
			if($date=="4weeks"){
				$now = $this->libertasGetDate("Y/m/d H:i:s",time()-2419200);
				$where .= " file_creation_date > '$now' and ";
			}
		}
		if ($filter!=""){
				$sql ="Select * from file_info $join where $where file_mime like '%".$filter."%' and file_client = $this->client_identifier order by file_label asc";
		} else {
			$type = $this->check_parameters($parameter,"type");
			if ($type!="all"){
				$found=-1;
				for($index=0;$index<count($this->mime_images);$index++){
					if ($this->mime_images[$index][2]==$type){
						$list = split("::",substr($this->mime_images[$index][1],1,strlen($this->mime_images[$index][1])-2));
						for($c_index=0;$c_index<count($list);$c_index++){
							if ($cond!=""){
								$cond .= " or ";
							}
							$cond .= "file_name like '%.".$list[$c_index]."'";
						}
					}
				}
				$sql ="Select * from file_info $join where $where ($cond) and  file_client = $this->client_identifier order by file_label asc";
			} else {
				$sql ="Select * from file_info $join where $where file_client = $this->client_identifier order by file_label asc";
			}
		}
		if ($debug) print "<p>".__FILE__." ".__LINE__."$sql</p>";
		$out="";
//		print "[<p>$sql</p>]";
		$result = $this->call_command("DB_QUERY",array($sql));
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
//			print $sql;
			if ($filter=="" && $type==""){
				$out  = "<module name=\"files\" display=\"completelist\"><files>";
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$file_label		 = $this->convert_amps($r["file_label"]);
					$file_md5		 = $r["file_md5_tag"];
					$file_identifier = $r["file_identifier"];
//					join("",split("\&quot;",))
					$out			.= "<file>
											<label><![CDATA[".$this->convert_amps($r["file_label"])."]]></label>
											<md5><![CDATA[$file_md5]]></md5>
											<id><![CDATA[$file_identifier]]></id>
										</file>\n";
				}
				$out  .= "</files></module>";
			}else{
				$out  = "<module name=\"files\" display=\"filteredlist\"><files>";
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$file_id		 = $r["file_identifier"];
					$file_label		 = $this->convert_amps($r["file_label"]);
					$file_name		 = $this->convert_amps($r["file_name"]);
					$file_mime		 = $r["file_mime"];
					$file_size		 = $r["file_size"];
					$file_directory	 = $r["file_directory"];
					$file_width		 = $this->check_parameters($r,"file_width");
					$file_height	 = $this->check_parameters($r,"file_height");
					$file_date		 = $this->check_parameters($r,"file_creation_date");
					$file_description= $this->split_me($this->split_me($this->split_me($r["file_description"],'"',""),"\r",""),"\n","");
					$file_md5		 = $r["file_md5_tag"];
					$file_dl_sec	 = $this->check_parameters($r,"file_dl_sec");
					//". join("", split("&quot;",$file_description) ) ."
					$out		.= "<file identifier=\"$file_id\">
										<url><![CDATA[".$this->convert_amps($r["file_name"])."]]></url>
										<label><![CDATA[".join("",split("\&quot;",$this->split_me($this->convert_amps($r["file_label"]),"\"", "\\\"")))."]]></label>
										<md5><![CDATA[$file_md5]]></md5>
										<name><![CDATA[$file_name]]></name>
										<mime><![CDATA[$file_mime]]></mime>
										<description><![CDATA[". join("", split("&quot;",$file_description) ) ."]]></description>
										<directory><![CDATA[$file_directory]]></directory>
										<size><![CDATA[$file_size]]></size>
										<width><![CDATA[$file_width]]></width>
										<height><![CDATA[$file_height]]></height>
										<icon><![CDATA[".$this->get_mime_image($r["file_name"])."]]></icon>
										<download_time><![CDATA[$file_dl_sec]]></download_time>
										<date><![CDATA[$file_date]]></date>
										<ext><![CDATA[".$this->file_extension($r["file_name"])."]]></ext>
									</file>\n";
				}
				$data = $this->call_command("LAYOUT_DISPLAY_DIRECTORY");
				$out  .= "</files><directories><directory name=\"/\" identifier=\"-1\" parent=\"-2\">$data</directory></directories></module>";
			}
			
		}
		return $out;
	}
	function save($parameters){
		$debug 			=	$this->debugit(false,$parameters);
		$file_user		=	$_SESSION["SESSION_USER_IDENTIFIER"];
		$root			=	$this->check_parameters($this->parent->site_directories,"ROOT");
		$file_identifier=	$this->check_parameters($parameters,"file_identifier",-1);
		$previous_file	=	$this->check_parameters($parameters,"previous_file");
		$previous_dir	=	$this->check_parameters($parameters,"previous_dir");
		$newCategories	=	$this->check_parameters($parameters,"newCategories");
		$tag 			=	$this->check_parameters($parameters,"tag");
		$now 			=	$this->libertasGetDate("Y/m/d H:i:s");
		$cat_created	=	0;
		
		$out = "";
		$upload_errors 	=	array(
			LOCALE_UPLOAD_ERR_OK,
			LOCALE_UPLOAD_ERR_INI_SIZE,
			LOCALE_UPLOAD_ERR_FORM_SIZE,
			LOCALE_UPLOAD_ERR_PARTIAL,
			LOCALE_UPLOAD_ERR_NO_FILE
		);
		$imgwidth	= 0;
		$imgheight	= 0;
		$out ="";
		if ($file_identifier==-1){
			if ($this->upload_multiple_access==1){
				$endIndex = 5;
			} else {
				$endIndex = 0;
			}
			for($i=0;$i<=$endIndex;$i++){
				$f = $this->check_parameters($_FILES, "file_name$i", "__NOT_FOUND__");
				if ($f!="__NOT_FOUND__"){
					$t_name = $this->check_parameters($f,"tmp_name");
					if (file_exists($t_name)){
						if (is_array($f)){
							$fsize = $this->check_parameters($f,"size",0);
							if (substr($this->check_parameters($f,"type"),0,5)=="image"){
								$imgsize = GetImageSize($this->check_parameters($f,"tmp_name"));
								if (is_array($imgsize)){
									$imgwidth = $imgsize[0];
									$imgheight = $imgsize[1];
								}
							}
						}else{
							$fsize = 0;
						}
						$size_des		= $this->get_size_string(intval($fsize));
						$file_dl_size	= $this->get_download_string(intval($fsize));
						$type			= $this->check_parameters($f,"type");
						$name			= $this->validate($this->check_parameters($f,"name"));
						$name_tag		= md5($name.uniqid(rand(),1));
						$f_ext			= $this->file_extension($name);
						if ($upload_errors[$this->check_parameters($f,"error",0)] == LOCALE_UPLOAD_ERR_OK){
							if (strlen($name)>0){
								$des 					= trim($this->strip_tidy($this->check_parameters($parameters,"file_description$i")));
								$label				 	= trim($this->strip_tidy($this->check_parameters($parameters,"file_label$i")));
								$dir_path				= $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($parameters["file_directory$i"]));
								$destination_filename	= str_replace("//","/",$root."/".$dir_path.$name_tag.$f_ext);
								move_uploaded_file($t_name,$destination_filename);
								$um = umask(0);
								@chmod($destination_filename, LS__FILE_PERMISSION);
								umask($um);
								
								$sql = "insert into file_info 
											(file_client, file_directory, file_name, file_mime, file_user, file_label, file_size, file_creation_date, file_description, file_md5_tag, file_width, file_height, file_dl_sec) 
										values 
											('".$this->client_identifier."', '".$parameters["file_directory$i"]."', '".$name."', '".$type."', '".$file_user."', '".$label."', '$size_des', '$now', '".$des."', '$name_tag', $imgwidth, $imgheight, '$file_dl_size');";
								if ($this->module_debug){
									$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
								}
								$result = $this->call_command("DB_QUERY",array($sql));
								/*
								-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
								- get new id
								-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
								*/
								$identifier=-1;
								$sql = "select file_identifier from file_info where file_client = $this->client_identifier and file_directory = '".$parameters["file_directory$i"]."' and file_name = '".$name."' and file_user = '$file_user' and file_label = '".$label."' and file_creation_date = '$now' and file_md5_tag = '$name_tag' ";
								$result  = $this->call_command("DB_QUERY",Array($sql));
		                        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	                    	$identifier = $r["file_identifier"];
       	    	                }
								$this->call_command("DB_FREE",Array($result));
								/*
								save cat
								*/
//								if ($this->parent->server[LICENCE_TYPE]==ECMS){
								if ($identifier!=-1){
									if($cat_created==0){
										$cat_created=1;
										$this->call_command("CATEGORYADMIN_TO_OBJECT_UPDATE", 
											Array(
												"new_categories"=>$newCategories,
												"data_list"		=> $this->check_parameters($parameters,"cat_id_list",Array()),
												"module"		=> "FILES_",
												"identifier"	=> $identifier
											)
										);
									}
								}
//								}
								/*
								add to list
								*/
								if ($this->parent->script =="admin/file_associate.php"){
									$alist = $this->check_parameters($_SESSION,"associated_list");
									if ($alist==""){
										$_SESSION["associated_list"]	= $identifier.",";
									}else{
										$_SESSION["associated_list"]	= $alist.",".$identifier.",";
									}
								} 
							}
						}
					} else {
						// Temporary file not found
						$out .= "<text><![CDATA[".$upload_errors[$this->check_parameters($_FILES["file_name$i"],"error")]."]]></text>";
					}
				} else {
					$out .= "<text><![CDATA[".LOCALE_UPLOAD_ERR_NO_FILE."]]></text>";
				}
			}
		} else {
			$exec			= 1;
			$file_identifier= $this->check_parameters($parameters,"file_identifier",-1);
			$action 		= $this->check_parameters($parameters,"file_name0_file_upload_radio");
			$directory		= $this->check_parameters($parameters,"file_directory0");
			$prev_dir		= $this->check_parameters($parameters,"previous_dir");
			$f 				= $this->check_parameters($_FILES, "file_name0", "__NOT_FOUND__");
			$des 			= trim($this->strip_tidy($this->check_parameters($parameters,"file_description0")));
			$label			= trim($this->strip_tidy($this->check_parameters($parameters,"file_label0")));
			$dir_path 		= $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($directory));
			$prev_dir_path	= $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($prev_dir));
			$previous_file	= $this->check_parameters($parameters,"previous_file");
			$sql 		= "
					update file_info set 
						file_description = '".$des."', 
						file_client = '".$this->client_identifier."', 
						file_label = '".$label."', 
						file_directory = '".$directory."'";
			if (($action=="__KEEP__") && ($prev_dir_path!=$dir_path)){
				$str_length = strlen(dirname($root.$prev_dir_path.$tag.$previous_file));
				rename($root."/".$prev_dir_path.$tag.$previous_file, $root."/".$dir_path.$tag.$previous_file);
			}
			if ($action=="__REMOVE__"){
				$dir_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($previous_dir));
				if ($previous_file!=""){
					@unlink ($root."/".$dir_path.$tag.$previous_file);
				}
			}
			if ($f != "__NOT_FOUND__"){
				if ($action=="__REPLACE__"){
					if ($upload_errors[$this->check_parameters($f,"error",0)]==LOCALE_UPLOAD_ERR_OK){
						$t_name = $this->check_parameters($f,"tmp_name");
						if (file_exists($t_name)){
							$fsize = $_FILES["file_name0"]["size"];
							$size_des		= $this->get_size_string(intval($fsize));
							$file_dl_size	= $this->get_download_string(intval($fsize));
							if (substr($_FILES["file_name0"]["type"],0,5)=="image"){
								$imgsize = GetImageSize($this->check_parameters($f,"tmp_name"));
								if (is_array($imgsize)){
									$imgwidth = $imgsize[0];
									$imgheight = $imgsize[1];
								}
							}
							$f_ext = $this->file_extension($f["name"]);
							$sql .= ",
									file_name = '".$this->validate($f["name"])."', 
									file_mime = '".$f["type"]."', 
									file_user = '".$file_user."',
									file_size = '".$size_des."',
									file_width = '".$imgwidth."',
									file_height = '".$imgheight."',
									file_dl_sec = '".$file_dl_size."',
									file_creation_date = '$now'";
							$dir = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",array($prev_dir));
							$dir_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($directory));
							$destination_filename = str_replace("//","/",$root."/".$dir_path.$tag.$f_ext);
							@unlink ($root."/".$dir.$tag.$previous_file);
							move_uploaded_file($f["tmp_name"],$destination_filename);
						}
						$exec = 1;
					} else {
						$out .= "<text><![CDATA[(".$upload_errors[$this->check_parameters($_FILES["file_name0"],"error")].")]]></text>";
						$exec = 0;
					}
				} else {
					$exec = 1;
				}
				if ($newCategories!=""){
					$exec==1;
				}
				if ($exec == 1){
					$sql .= " where file_identifier=".$file_identifier;
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
					}
					$result = $this->call_command("DB_QUERY",array($sql));
//					if ($this->parent->server[LICENCE_TYPE]==ECMS){
						$this->call_command("CATEGORYADMIN_TO_OBJECT_UPDATE", 
							Array(
								"new_categories"=>$newCategories,
								"data_list"		=> $this->check_parameters($parameters,"cat_id_list",Array()),
								"module"		=> "FILES_",
								"identifier"	=> $parameters["file_identifier"]
							)
						);
//					}
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=FILES_LIST"));
				}
			} else {
				if ($parameters["file_name0_file_upload_radio"]=="__KEEP__"){
					if ($exec == 1){
						$sql .= " where file_identifier=".$parameters["file_identifier"];
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
						}
						$result = $this->call_command("DB_QUERY",array($sql));
						if ($newCategories!=""){
							$this->call_command("CATEGORYADMIN_TO_OBJECT_UPDATE", 
								Array(
									"new_categories"=>$newCategories,
									"data_list"		=> $this->check_parameters($parameters,"cat_id_list",Array()),
									"module"		=> "FILES_",
									"identifier"	=> $parameters["file_identifier"]
								)
							);
						}
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=FILES_LIST"));
					}
				} else if ($parameters["file_name0_file_upload_radio"]=="__REPLACE__"){
					$out .= "<text><![CDATA[Unable to find File upload infomration]]></text>";
				}
			}
		}
		$output  = "<module name=\"files\" display=\"form\">";
		$output .= "<page_options>";
		$output .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","FILES_LIST",LOCALE_CANCEL));
		$output .= "</page_options>";
		$output .= $out;
		$output .= "<form name=\"file_form\" method=\"post\" label=\"".LOCALE_SORRY."\">";
		$output .= "</form>";
		$output .= "</module>";
		return $output;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Return the Size of a file as a text string 
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function get_size_string($size_bytes){
		$one_k 	= 1024;
		$one_mb = ($one_k * $one_k);
		if (($size_bytes / $one_mb)>=1){
			$size_value = ($size_bytes / $one_mb);
			$size_des = "".round($size_value,1)." MB";
		}else if (($size_bytes / $one_k)>=1){
			$size_value = $size_bytes / $one_k;
			$size_des = "".round($size_value)." kb";
		}else{
			$size_des = "".$size_bytes." bytes";
		}
		return $size_des;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-	Return the download time on a 56k modem for this file size wD xH yM zS (Days, Hours, Minutes and Seconds)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function get_download_string($size_bytes){
		$file_dl_total	= ceil($size_bytes/5000);
		$file_dl_days	= floor($file_dl_total / (3600*24));
		$file_dl_hour	= floor(($file_dl_total - ($file_dl_days * (3600*24)))  / 3600);
		$file_dl_min	= floor(($file_dl_total - (($file_dl_days * (3600*24)) + ($file_dl_hour *3600))) / 60);
		$file_dl_sec	= ($file_dl_total % 60);
		$file_dl_size	= "";
		
		if ($file_dl_days>0)
			$file_dl_size = $file_dl_days."d ";
		
		if ($file_dl_hour>0)
			$file_dl_size .= $file_dl_hour."h ";
		
		if ($file_dl_min>0)
			$file_dl_size .= $file_dl_min."m ";
		
		if ($file_dl_sec>0)
			$file_dl_size .= $file_dl_sec."s";
		return $file_dl_size;
	}
	function filter($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_filter",__LINE__,"[]"));
		}
		$group_list			= $this->call_command("GROUP_RETRIEVE",array($this->check_parameters($parameters,"group_filter")));
		$filter_category	= $this->check_parameters($parameters,"filter_category");
		$onlyone 			= $this->check_parameters($parameters,"onlyone",0);
		$order_filter		= $this->check_parameters($parameters,"order_filter",0);		
		//$menu 			= $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($this->check_parameters($parameters,"menu_location")));
		$file_mime 			= $this->check_parameters($parameters,"file_mime","");		
		if ($this->check_parameters($parameters,"lock_mime",0)==0){
			$file_mime_types = $this->get_mime_types_options($this->check_parameters($parameters,"file_mime"));
		}
		$category_list = $this->call_command("CATEGORYADMIN_RETRIEVE_LIST",Array("module"=>"FILES_","label"=>"Virtual folders","returnType"=>"select"));
		$cat_list = "<specified_categorys identifier='".$filter_category."'/>";
		$out = "\t\t\t\t<form name=\"associated_form\" label=\"\" method='get'>\n";
		$cmd  = $this->check_parameters($parameters,"command","FILES_LIST");
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\" value=\"$cmd\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" value=\"1\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"onlyone\" value=\"$onlyone\"/>\n";
		
		$ret_hide 		= $this->check_parameters($parameters,"return_hidden"	,$this->check_parameters($_SESSION, "return_hidden"		));
		$return_note	= $this->check_parameters($parameters,"return_note"		,$this->check_parameters($_SESSION, "return_note"		));
		$return_command = $this->check_parameters($parameters,"return_command"	,$this->check_parameters($_SESSION, "return_command"	));
		$associated_list= $this->check_parameters($parameters,"associated_list"	,$this->check_parameters($_SESSION, "associated_list"	));
		if ($this->parent->script =="admin/file_associate.php"){
			$_SESSION["return_hidden"]	= $ret_hide;
			$_SESSION["return_note"]	= $return_note;
			$_SESSION["return_command"]	= $return_command;
			$_SESSION["associated_list"]	= $associated_list;
		} else {
			unset($_SESSION["return_hidden"]);
			unset($_SESSION["return_note"]);
			unset($_SESSION["return_command"]);
			unset($_SESSION["associated_list"]);
		}
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"associated_list\" value=\"".$associated_list."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"return_hidden\" value=\"".$ret_hide."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"return_note\" value=\"".$return_note."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"return_command\" value=\"".$return_command."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"text\" size=\"12\" length='255' label=\"".SEARCH_KEYWORDS."\" name=\"search_phrase\"><![CDATA[".$this->check_parameters($parameters,"search_phrase")."]]></input>\n";
		
		$out .= "\t\t\t\t\t<select name=\"order_filter\" label=\"".ENTRY_ORDER_FILTER."\">\n";
			for ($index=0,$max=count($this->display_options);$index<$max;$index++){
				$out .="\t\t\t\t\t\t<option value=\"".$this->display_options[$index][0]."\"";
				if ($order_filter==$this->display_options[$index][0]){
					$out .=" selected=\"true\"";
				}
				$out .=">".$this->display_options[$index][1]."</option>\n";
			}
			$out .= "\t\t\t\t\t</select>\n";
		if ($this->check_parameters($parameters,"lock_mime",0)==0){
			$out .= "\t\t\t\t\t<select name=\"file_mime\" label=\"".FILE_TYPE."\">
									<option value=\"\">".LOCALE_FILES_FILTER_ALL."</option>
									$file_mime_types
								</select>\n";
		$out .= $category_list.$cat_list;
			$out .= "\t\t\t\t\t<input type='hidden' name=\"lock_mime\" value=\"0\"/>\n";
		} else {
			$out .= "\t\t\t\t\t<input type='hidden' name=\"file_mime\" value=\"$file_mime\"/>\n";
			$out .= "\t\t\t\t\t<input type='hidden' name=\"lock_mime\" value=\"1\"/>\n";
		}
		$out .= "\t\t\t\t\t<input type=\"submit\" value=\"".SEARCH_NOW."\" name=\"filter\" iconify=\"SEARCH\"/>\n";
		$out .= "\t\t\t\t</form>";
		return $out;
	}
	
	function get_mime_types_options($selected=""){
		$sql = "select file_mime from file_info where file_client=$this->client_identifier group by file_mime";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"$sql"));
		}
		$out="";
		$mime_result = $this->call_command("DB_QUERY",array($sql));
		if (!$mime_result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($mime_result))){
				$out .= "<option value=\"".$r["file_mime"]."\"";
				if ($selected==$r["file_mime"]){
					$out .= " selected=\"true\"";
				}
				$out .= " >".$r["file_mime"]."</option>";
			}
			//$this->call_command("DB_FREE",array());
		}
		return $out;
	}

	/**
	* remove confirm
	----------------
	- This module will allow an administrator to remove a user from the system.
	*/
	function remove_confirm($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		/**
		* delete this user it has been confirmed that the user is to be deleted
		*/
		$sql = "select * from file_info where file_client = $this->client_identifier and file_identifier=$identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		$root=$this->check_parameters($this->parent->site_directories,"ROOT");
		while($r=$this->call_command("DB_FETCH_ARRAY",array($result))){
			$url = $root."/".$this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"])).$r["file_md5_tag"].$this->file_extension($r["file_name"]);
		}
		@unlink ($url);
		$this->call_command("CATEGORYADMIN_TO_OBJECT_REMOVE", Array("identifier"=>$identifier,"module"=>$this->webContainer));
		$sql = "delete from file_info where file_client = $this->client_identifier and file_identifier=$identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		/**
		* delete this files associations with published documents
		*/
		$sql = "delete from file_access_to_page where file_client=$this->client_identifier and file_identifier = $identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		
		$sql = "delete from file_downloads where client_identifier = $this->client_identifier and file_identifier = $identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		if (file_exists($url)){
			$this->call_command("TASK_SUBMIT", Array(
				"from" => "libertas_system@".$this->parent->domain,
				"to" => "support@libertas-solutions.com",
				"subject" => "LS00000013 - Unable to delete the following file",
				"msg" => "File: ".$url)
			);
		}
	}
	
	/**
	* remove user screen
	-----------------------
	- This function will display the form for the user to select if they truly want to
	- remove the selected user.
	*/
	function remove_file_screen($parameters){
		
		/**
		* query if the user wishes to actually remove this users details as this might be a
		* mistake.
		*/
		$identifier = $parameters["identifier"];
		$sql = "select * from user_info where user_client = $this->client_identifier and user_identifier=$identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$out ="<module name=\"files\" display=\"form\">";
		if ($result){
			$out .="<form name=\"_remove_form\" label=\"".LOCALE_CONFIRM_DELETE."\">";
			$out .="	<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
			$out .="	<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."REMOVE_CONFIRM\"/>";
			$out .="	<text><![CDATA[".LOCALE_FILE_REMOVE_CONFIRMATION_LABEL."]]></text>";
			$out .="	<input type=\"button\" command=\"".$this->module_command."LIST\" iconify=\"NO\" value=\"".NO_KEEP."\"/>";
			$out .="	<input type=\"submit\" iconify=\"YES\" value=\"".YES_REMOVE."\"/>";
			$out .="</form>";
			$this->call_command("DB_FREE",array($result));
		} else {
			$out .= "Sorry that is an invalid file.";
		}
		$out .="</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function module_list_file_details($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$return_note		= $this->check_parameters($parameters,"return_note",-1);
		$return_hidden		= $this->check_parameters($parameters,"return_hidden",-1);
		$file_associations	= $this->check_parameters($parameters,"associated_list",$this->check_parameters($parameters,"file_associations",-1));
		$embed				= $this->check_parameters($parameters,"embed",true);
		if ($file_associations != -1){
			$file_associations = str_replace(",,",",",$file_associations);
			
			/* Modified By Ali Imran */
			$file_associations_checked = str_replace(",,",",",$_SESSION['associated_list']);
			
			$last_entered_str = rtrim($file_associations,",");
			$ids_list_arr = explode(",",$last_entered_str);
			
			$last_entered_str_checked = rtrim($file_associations_checked,",");
			$ids_list_arr_checked = explode(",",$last_entered_str_checked);
			
			$j= sizeof($ids_list_arr)-1;
			$new_list = array();
			if((sizeof($ids_list_arr)-sizeof($ids_list_arr_checked)) > 0){
				for($i=0; $i < (sizeof($ids_list_arr)-sizeof($ids_list_arr_checked)); $i++){
					$new_list[$i] = $ids_list_arr[$j];
					$j--;
				}
				foreach($ids_list_arr_checked as $value){
					$new_list[$i] = $value;
					$i ++;
				}
			}else{
				$i = 0;
				/*$i = 0;
				$new_list = array();
				if(sizeof($ids_list_arr_checked) > 0 && sizeof($ids_list_arr_checked) > 0){
					$new_list = array_diff($ids_list_arr_checked,$ids_list_arr);
					$ids_list_arr_checked = array_diff($ids_list_arr_checked,$new_list);
					$new_list = array_diff($new_list,$ids_list_arr);
					$new_list = array_merge($new_list,$ids_list_arr_checked);
				}*/
				if(sizeof($ids_list_arr) > 0 ){
					foreach($ids_list_arr as $value){
						$new_list[$i] = $value;
						$i ++;
					}
				}
				/*
				foreach($ids_list_arr as $value){
					if(array_search($value, $ids_list_arr_checked) = "")
											
					}
					$i ++;*/
				
			}
			
			
			$last_entered_str = "";
			if(sizeof($new_list) > 0){
				$last_entered_str = " order by ";
				$new_list = array_reverse($new_list);
				foreach($new_list as $value){
					if($value!='')
						$last_entered_str .= "file_info.file_identifier=".$value." ,";
				}
				$last_entered_str = rtrim($last_entered_str,",");
			}			
			$sql = "select file_info.* from file_info where file_info.file_identifier in ($file_associations -1) and file_info.file_client = $this->client_identifier $last_entered_str";
			/* End Modifications By Ali Imran */
			
			/*$sql = "select file_info.* from file_info where file_info.file_identifier in ($file_associations -1) and file_info.file_client = $this->client_identifier order by file_label ";*/
		}else{
			$sql = "select file_info.* from file_info inner join file_access_to_page on file_info.file_identifier=file_access_to_page.file_identifier where file_access_to_page.trans_identifier=$identifier and file_access_to_page.client_identifier=$this->client_identifier order by file_rank, file_label";
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[$sql]"));
		}

		
		$result = $this->call_command("DB_QUERY",Array($sql));
		$page_documents= Array();
		//print $sql; die();
		$file_list="";
		if ($file_associations != -1){
			if ($embed){
				$out ="<module name=\"files\" display=\"data\">";
				$out.="<hidden><![CDATA[$return_hidden]]></hidden>";
				$out.="<note><![CDATA[$return_note]]></note>";
				$out.="<files>";
			}else{
				$out="<hidden><![CDATA[$return_hidden]]></hidden>";
				$out.="<note><![CDATA[$return_note]]></note>";
			}
		}else{
			$out="<hidden><![CDATA[$return_hidden]]></hidden>";
			$out.="<note><![CDATA[$return_note]]></note>";
			$out.="<files>";
		}
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$file_id		 = $r["file_identifier"];
				$file_label		 = $this->convert_amps($r["file_label"]);
				$file_name		 = $this->convert_amps($r["file_name"]);
				$file_mime		 = $r["file_mime"];
				$file_size		 = $r["file_size"];
				$file_directory	 = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"]));
				$file_width		 = $this->check_parameters($r,"file_width");
				$file_date		 = $this->check_parameters($r,"file_creation_date");
				$file_height	 = $this->check_parameters($r,"file_height");
				$file_description= $r["file_description"];
				$file_md5		 = $r["file_md5_tag"];
				$file_dl_sec	 = $this->check_parameters($r,"file_dl_sec");
				$out		.= "<file identifier=\"$file_id\">
									<url><![CDATA[".$this->convert_amps($r["file_name"])."]]></url>
									<label><![CDATA[".$this->convert_amps($r["file_label"])."]]></label>
									<md5><![CDATA[$file_md5]]></md5>
									<name><![CDATA[$file_name]]></name>
									<mime><![CDATA[$file_mime]]></mime>
									<description><![CDATA[$file_description]]></description>
									<directory><![CDATA[$file_directory]]></directory>
									<size><![CDATA[$file_size]]></size>
									<width><![CDATA[$file_width]]></width>
									<height><![CDATA[$file_height]]></height>
									<icon><![CDATA[".$this->get_mime_image($r["file_name"])."]]></icon>
									<download_time><![CDATA[$file_dl_sec]]></download_time>
									<date><![CDATA[$file_date]]></date>
									<ext><![CDATA[".$this->file_extension($r["file_name"])."]]></ext>
								</file>\n";
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
		if ($file_associations != -1){
			if ($embed){
				$out .="</files></module>";
			}else{
				$out .="";
			}
		}else{
			$out .="</files>";
		}
		
		return $out;
	}

	/*****
		Tmp Function just to solve 'the numbers for the file downloads showing incorrectly' 
		Added new field 'contact_identifier' in a table 'file_downloads',so this function is just for importing old data
		( added By Muhammd Imran )
	******/	
	function tmp_import_contact_id_for_file_downloads(){
		$sql = "select distinct user_identifier as u_id from file_downloads where client_identifier = $this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$user_id = $r['u_id'];

			$sql_contact = "select contact_identifier from contact_data where contact_client = $this->client_identifier and contact_user = ".$user_id;
			$result_contact = $this->call_command("DB_QUERY",array($sql_contact));
			$r_contact = $this->call_command("DB_FETCH_ARRAY",array($result_contact));
			$contact_identifier = $r_contact["contact_identifier"];

			$sql_file ="update file_downloads set contact_identifier = $contact_identifier where user_identifier=$user_id and client_identifier=$this->client_identifier";
			$result_file = $this->call_command("DB_QUERY",array($sql_file));

		}
	}
	
	function file_download($parameters){
//	print "download";
//	exit();
		$sp_force_download = strtoupper($this->check_prefs(Array("sp_force_download","default"=>"No","module"=>"FILES_", "options"=>"Yes:No")));
		$sp_force_download_login = strtoupper($this->check_prefs(Array("sp_force_download_login","default"=>"No","module"=>"FILES_", "options"=>"Yes:No")));
		$can_download_ok=0;
		if (!isset($_SESSION["SESSION_LOGGED_IN"])){
			$_SESSION["SESSION_LOGGED_IN"]=0;
			$_SESSION["SESSION_USER_IDENTIFIER"]=-1;
		}
		if (($sp_force_download_login=="YES") && ($_SESSION["SESSION_LOGGED_IN"]==1)){
			$can_download_ok=1;
		}
		if ($sp_force_download_login!="YES"){
			$can_download_ok=1;
		}
		if($can_download_ok==1){
			$id 	= $this->check_parameters($parameters,"download");
			$sql 	= "select * from file_info where file_client=$this->client_identifier and file_md5_tag='$id'";
			$result = $this->call_command("DB_QUERY",array($sql));
			$ok=0;
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$ok=1;
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$f_id = $r["file_identifier"];
					$f_url = $r["file_md5_tag"];
					$f_name = $r["file_name"];
					$f_dir = $r["file_directory"];
					$f_mime = $r["file_mime"];
				}
			}
			if ($result){
				$this->call_command("DB_FREE",array($result));
			}
			if($ok){
				$user = $_SESSION["SESSION_USER_IDENTIFIER"];
				$f_ext = $this->file_extension($f_name);
				$path	= $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($f_dir)).$f_url.$f_ext;
				$now = $this->libertasGetDate("Y/m/d H:i:s");
				
				/* Get Contact identifier (added and comment By Muhammad Imran) */
				$sql = "select * from contact_data where contact_client = $this->client_identifier and contact_user = ".$user;
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				$result = $this->call_command("DB_QUERY",array($sql));
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
				$contact_identifier = $r["contact_identifier"];

				if (!isset($contact_identifier) || $contact_identifier == ""){
					$contact_identifier=-1;
				}
				/*
				$sql ="insert into file_downloads (file_identifier, client_identifier, user_identifier, download_time) values 
				($f_id, $this->client_identifier, $user, '$now')";
				*/

				/* Get Contact identifier (added and comment By Muhammad Imran) */

				$sql ="insert into file_downloads (file_identifier, client_identifier, user_identifier, download_time, contact_identifier) values 
				($f_id, $this->client_identifier, $user, '$now', $contact_identifier)";		

				$this->call_command("DB_QUERY",array($sql));
				$filename  = $f_name;
				$root=$this->check_parameters($this->parent->site_directories,"ROOT");
				$filename  =$root."/".$path;
				if (file_exists($filename)){
					/*******************Uncommit & Commit by Ali to Remove User Access Logs****************************/
					$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_FILE_DOWNLOAD__",$parameters));
				    /**************************************************************/
					@ob_end_clean();
					header("Pragma: public");
					header("Expires: 0"); // set expiration time
					header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
					// browser must download file from server instead of cache
	
					// force download dialog
					header("Content-Type: application/download");
					header("Content-Type: application/force-download");

					// use the Content-Disposition header to supply a recommended filename and 
					// force the browser to display the save dialog. 
					$user_agent = strtolower ($_SERVER["HTTP_USER_AGENT"]);


					// this if block is commented out because of the bug with opening in Acrobat Reader v6
					if ((is_integer (strpos($user_agent, "msie"))) && (is_integer (strpos($user_agent, "win")))){
						if($sp_force_download=="YES"){
							header("Content-Disposition: attachment; filename=".basename($f_name).";");
						} else {
							header("Content-Disposition: filename=".basename($f_name).";" );
						}
					} else {
						header("Content-Disposition: attachment; filename=".basename($f_name).";" );
					}
//					header("Content-Disposition: attachment; filename=".basename($f_name).";");
//					header("Content-Disposition: filename=".basename($f_name).";" );
					/*
					The Content-transfer-encoding header should be binary, since the file will be read 
					directly from the disk and the raw bytes passed to the downloading computer.
					The Content-length header is useful to set for downloads. The browser will be able to 
					show a progress meter as a file downloads. The content-lenght can be determines by 
					filesize function returns the size of a file. 
					*/
					header("Content-Transfer-Encoding: auto");
					header("Content-Length: ".filesize($filename));
					$fp = fopen("$filename","rb");
					header("Content-Type: $f_mime");
					fpassthru($fp); 
					$this->exitprogram();
				} else {
					/*******************Uncommit & Commit by Ali to Remove User Access Logs********************/
					$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_FILE_DOWNLOAD_NO_FILE__",$parameters));
					/***************************************************************/
					$out ="<module name=\"files\" display=\"confirmation\">";
					$out .= "<text><![CDATA[".LOCALE_SORRY_NO_FILE."]]></text>";
					$out .="</module>";
					return $out;
				}
			}else{
					/*******************Uncommit & Commit by Ali to Remove User Access Logs********************/
					$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_FILE_DOWNLOAD_NO_FILE__",$parameters));
					/***************************************************************/
					$out ="<module name=\"files\" display=\"confirmation\">";
					$out .= "<text><![CDATA[".LOCALE_SORRY_NO_FILE."]]></text>";
					$out .="</module>";
				return $out;
			}
		}else{
			/*******************Uncommit & Commit by Ali to Remove User Access Logs********************/
			$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_FILE_LOGIN_REQUIRED__",$parameters));
			/***************************************************************/
			$out ="<module name=\"files\" display=\"confirmation\">";
			$out .= "<text><![CDATA[".LOCALE_SORRY_LOGIN_REQUIRED."]]></text>";
			$out .="</module>";
			$out .= $this->call_command("USERS_SHOW_LOGIN",Array(0,1));
			return $out;
		}
	}
	function file_download_counter($parameters){
		$type = $this->check_parameters($parameters,"type");
		if ($type=="count"){
			$file  = $this->check_parameters($parameters,"file_identifier",-1);
			$sql ="select count(file_identifier) as total from file_downloads where file_identifier = $file and client_identifier = $this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$total = $r["total"];
				}
			}
			return $total;
		} else {
			
		}
	}
	

	function get_mime_image($file){
		$max = count($this->mime_images);
		$ext = strtolower(substr($file,strrpos($file,".")+1));
		$mime_image ="";
		for ($index=0;$index<$max;$index++){
			$pos = strpos($this->mime_images[$index][1], ":$ext:");
			if ($pos === false){
			}else{
				$mime_image = $this->mime_images[$index][0];
			}
		}
		if ($mime_image==""){
			$mime_image="lsl";
		}
		return $mime_image;
	}
	
	function file_download_report($parameters){
		$show				=$this->check_parameters($parameters, "show");
		$_filter_year		=$this->check_parameters($parameters, "_filter_year");
		$_filter_month		=$this->check_parameters($parameters, "_filter_month");
		$_filter_day		=$this->check_parameters($parameters, "_filter_day");
		$form = $this->call_command("USERACCESS_DISPLAY_FILTER",$parameters);
		$date_conditions = $this->call_command("USERACCESS_EXTRACT_DATE_CONDITION",Array($parameters));
		if ($show=="file"){
			$index ="file_identifier";
			$join = "";
			$link = $this->module_command."VIEW_FILE_DOWNLOADS";
			$sql = "
				SELECT count(file_downloads.$index) as total, max(download_time) as max_date, file_info.file_label, file_info.file_identifier
				FROM file_downloads 
					inner join file_info on file_info.file_identifier = file_downloads.file_identifier 
				WHERE 
					client_identifier = $this->client_identifier 
				group by file_downloads.file_identifier, file_info.file_label, 
			    	   file_info.file_identifier
				order by max_date desc
			";
		}else{
			$index ="user_identifier";
			$link = $this->module_command."VIEW_USERS_DOWNLOADS";
			
			/* comment and added By Muhammad Imran */
			
/*			$sql = "
					SELECT     COUNT(file_downloads.user_identifier) AS total, MAX(file_downloads.download_time) AS max_date, contact_data.contact_last_name, 
					                      contact_data.contact_first_name, file_downloads.user_identifier
					FROM         file_downloads INNER JOIN
					                      contact_data ON contact_data.contact_user = file_downloads.user_identifier
					WHERE     (file_downloads.client_identifier = $this->client_identifier)
					GROUP BY file_downloads.user_identifier, contact_data.contact_last_name, contact_data.contact_first_name
					ORDER BY total
			";
*/
			$sql = "
					SELECT     COUNT(file_downloads.user_identifier) AS total, MAX(file_downloads.download_time) AS max_date, contact_data.contact_last_name, 
					                      contact_data.contact_first_name, file_downloads.user_identifier
					FROM         file_downloads INNER JOIN
					                      contact_data ON contact_data.contact_user = file_downloads.user_identifier
										  AND contact_data.contact_identifier=file_downloads.contact_identifier
					WHERE     (file_downloads.client_identifier = $this->client_identifier)
					GROUP BY file_downloads.user_identifier, contact_data.contact_last_name, contact_data.contact_first_name
					ORDER BY max_date desc
			";
			/* comment and added By Muhammad Imran */
		}

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}

		
		$result = $this->call_command("DB_QUERY", Array($sql));
		
//		echo $this->call_command("DB_NUM_ROWS",array($result));

		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result)))){
				$total				= $this->check_parameters($r,"total");
				$max_date 			= $this->check_parameters($r,"max_date");
				if ($show=="file"){
					$fid				= $this->check_parameters($r,"file_identifier");
					$file_label 		= $this->check_parameters($r,"file_label");
					$out .= "<stat_entry>
						<attribute name=\"File label\" show=\"YES\" link=\"NO\"><![CDATA[$file_label]]></attribute>
						<attribute name=\"Downloaded\" show=\"YES\" link=\"owner\"><![CDATA[".$r["total"]."]]></attribute>
						<attribute name=\"Last Download\" show=\"YES\" link=\"NO\"><![CDATA[$max_date]]></attribute>
						<attribute name=\"owner\" show=\"NO\" link=\"NO\"><![CDATA[identifier=$fid]]></attribute>
					</stat_entry>";
				}else{
					$contact = $this->check_parameters($r,"contact_last_name").", ".$this->check_parameters($r,"contact_first_name");
					$uid 		= $this->check_parameters($r,"user_identifier");
					$out .= "<stat_entry>
						<attribute name=\"User\" show=\"YES\" link=\"NO\"><![CDATA[$contact]]></attribute>
						<attribute name=\"Downloaded\" show=\"YES\" link=\"owner\"><![CDATA[".$r["total"]."]]></attribute>
						<attribute name=\"Last Download\" show=\"YES\" link=\"NO\"><![CDATA[$max_date]]></attribute>
						<attribute name=\"owner\" show=\"NO\" link=\"NO\"><![CDATA[identifier=$uid]]></attribute>
					</stat_entry>";
				}
			}
		}

		$page_options ="".$this->call_command("USERACCESS_GENERATE_LINKS");
//		<graphs><graph>4</graph><graph>5</graph><graph>6</graph></graphs>

		return "<module name=\"files\" display=\"stats\">$form$page_options
		<stat_results label=\"".$this->get_constant("LOCALE_STATS_".strtoupper($show)."_DOWNLOAD")."\"  link=\"$link&amp;show=$show\" split_on='15' show_counter='1'>".$out."</stat_results></module>";
	
	}
	
	function file_download_report_downloaded($parameters){
		$identifier			= $this->check_parameters($parameters, "identifier");
		$show				= $this->check_parameters($parameters, "show");
		$_filter_year		= $this->check_parameters($parameters, "_filter_year");
		$_filter_month		= $this->check_parameters($parameters, "_filter_month");
		$_filter_day		= $this->check_parameters($parameters, "_filter_day");
		$form 				= $this->call_command("USERACCESS_DISPLAY_FILTER",$parameters);
		$date_conditions 	= $this->call_command("USERACCESS_EXTRACT_DATE_CONDITION",Array($parameters));
		if ($show=="user"){
			$index ="file_identifier";
			$join = "	inner join file_info on file_info.file_identifier = file_downloads.file_identifier ";
			$link = $this->module_command."VIEW_FILE_DOWNLOADS&amp;show=file";
			$where ="	and file_downloads.user_identifier=$identifier";
			$output=$this->call_command("CONTACT_VIEW_USER",Array("uid_identifier"=>$identifier));
		}else{
			$index ="user_identifier";
			$join = "	inner join file_info on file_info.file_identifier = file_downloads.file_identifier ";
			$join .= "	inner join contact_data on contact_data.contact_user = file_downloads.user_identifier ";
			/* To fix number of download Added By Muhammad Imran*/
			$join .= "	and contact_data.contact_identifier=file_downloads.contact_identifier ";

			$link = $this->module_command."VIEW_USERS_DOWNLOADS&amp;show=user";

			//$where ="	and file_downloads.file_identifier=$identifier";
			$where ="";
			/* Added By Muhammad Imran */
			
			
			$output="";
		}
		$sql = "
SELECT *, count(file_downloads.$index) as total, max(download_time) as max_date
FROM file_downloads 
$join
WHERE 
	client_identifier = $this->client_identifier $where
group by file_downloads.$index
order by total
";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_last_number_of_days",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY", Array($sql));
		$out	= "";
		$total 	= 0;
		if ($result){
			$total	= 0;
			$c=0;
			while (($r= $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($c<200)){
				$total				= $this->check_parameters($r,"total");
				$max_date 			= $this->check_parameters($r,"max_date");
				if ($show=="user"){
					$file_label 		= $this->check_parameters($r,"file_label");
					$file = $this->check_parameters($r,"file_identifier");
					$out .= "<stat_entry>
						<attribute name=\"File label\" show=\"YES\" link=\"NO\"><![CDATA[$file_label]]></attribute>
						<attribute name=\"Downloaded\" show=\"YES\" link=\"owner\"><![CDATA[".$r["total"]."]]></attribute>
						<attribute name=\"Last Download\" show=\"YES\" link=\"NO\"><![CDATA[$max_date]]></attribute>
						<attribute name=\"owner\" show=\"NO\" link=\"NO\"><![CDATA[identifier=$file]]></attribute>
					</stat_entry>";
				}else{
					$contact = $this->check_parameters($r,"contact_last_name").", ".$this->check_parameters($r,"contact_first_name");
					$user = $this->check_parameters($r,"user_identifier");
					$out .= "<stat_entry>
						<attribute name=\"User\" show=\"YES\" link=\"NO\"><![CDATA[$contact]]></attribute>
						<attribute name=\"Downloaded\" show=\"YES\" link=\"owner\"><![CDATA[".$r["total"]."]]></attribute>
						<attribute name=\"Last Download\" show=\"YES\" link=\"NO\"><![CDATA[$max_date]]></attribute>
						<attribute name=\"owner\" show=\"NO\" link=\"NO\"><![CDATA[identifier=$user]]></attribute>
					</stat_entry>";
				}
			}
		}

		$page_options ="<page_options><button iconify=\"CANCEL\" command=\"USERACCESS_VIEW_SUMMARY\"/></page_options>".$this->call_command("USERACCESS_GENERATE_LINKS");
//		<graphs><graph>4</graph><graph>5</graph><graph>6</graph></graphs>
		return $output."<module name=\"files\" display=\"stats\">$form$page_options
		<stat_results label=\"".$this->get_constant("LOCALE_STATS_".strtoupper($show)."_DOWNLOADED")."\"  link=\"$link\" split_on='15' show_counter='1'>".$out."</stat_results></module>";
	}
	
	function file_move_to_menu_location($parameters){
		$menu_locations = $this->check_parameters($parameters,"menu_locations");
		$old_path 		= $this->check_parameters($parameters,"old_path");
		$new_path		= $this->check_parameters($parameters,"new_path");
		$root			= $this->check_parameters($this->parent->site_directories,"ROOT");
		if (strpos("/"," ".$old_path)>0){
			$l 		= split("/",$old_path);
			$pth = $l[count($l)-1];
		} else {
			$pth=$old_path;
		}
		if (strlen($menu_locations)>0){
			// move any files that have been uploaded into these menu locations.
			$sql = "select file_info.*, directory_parent from file_info 
						inner join menu_data on menu_directory = file_directory 
						inner join directory_data on directory_identifier = file_directory 
					where menu_identifier in ($menu_locations -2)
					and menu_client = $this->client_identifier
				    and directory_client = $this->client_identifier
				    and file_client = $this->client_identifier";
			$result = $this->call_command("DB_QUERY", Array($sql));
			while ($r= $this->call_command("DB_FETCH_ARRAY", Array($result))){
				$dir_old = str_replace("/".$new_path,"/".$old_path,"/".$this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"])));
				$uri_old = $root.$dir_old.$r["file_md5_tag"].$this->file_extension($r["file_name"]);
				$uri_new = $root."/".$this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"])).$r["file_md5_tag"].$this->file_extension($r["file_name"]);
				rename($uri_old,$uri_new);
			}
		}
	}
	
	function file_info($parameters){
		$id 	= $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"unset_identifier"));
		$sql 	= "select * from file_info where file_client=$this->client_identifier and file_md5_tag='$id'";
		$result = $this->call_command("DB_QUERY",array($sql));
		$ok=0;
		$f_label= "";
		$f_name = "";
		$f_size 	= "";
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			$ok=1;
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$f_label = $r["file_label"];
				$f_name	 = $r["file_name"];
				$f_size	 = $this->check_parameters($r,"file_size");
				if ($f_size=="0 bytes"){
					$f_size ="unknown";
				}
				$f_download_time = $this->check_parameters($r,"file_dl_sec","unknown");
				$f_date	 = $this->check_parameters($r,"file_creation_date");
				$f_des	 = $this->check_parameters($r,"file_description");
			}
		}
		
		if ($f_name!=""){
			header ("Content-type: text/plain");
			print "$f_des\n";
		} else {
			print "Sorry there is no information available for the specified file.";
		}
		$this->exitprogram();
	}

	function file_stream($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		
		$f_name		= "";
		$stream_ext = "";
		$f_dir		= "";
		$md5 		= "";
		$sql 		= "select * from file_info where file_client=$this->client_identifier and file_md5_tag='$identifier'";

//		print $sql;
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$f_name = $r["file_name"];
				$f_dir	= $r["file_directory"];
				$md5	= $r["file_md5_tag"];
			}
		}
		$ext = strtolower(substr($this->file_extension($f_name),1));
		$dir = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($f_dir));
		$content_type = Array(
				Array (":rm::ra:", "ram")
		);
		for ($index=0;$index<count($content_type);$index++){
			if (strpos($content_type[$index][0],":".$ext.":")===false){
			}else{
				$stream_ext = $content_type[$index][1];
			}
		}
		$url	 = $dir.$md5;
		$root=$this->check_parameters($this->parent->site_directories,"ROOT");
		if (file_exists($root."/".$url.$stream_ext)){
			$url	 .= ".".$stream_ext;
			header("Content-Disposition: filename=download.".$stream_ext.";");
//			print "Location: http://".$this->parent->domain.$this->parent->base.$url;
			header("Location: http://".$this->parent->domain.$this->parent->base.$url);
			exit();
		}else{
			$out = "http://".$this->parent->domain.$this->parent->base.$url.".".$ext;
			$fp = fopen($root."/".$url.".".$stream_ext, 'w');
			fwrite($fp, $out);
			fclose($fp);
			header("Content-Disposition: filename=download.".$stream_ext.";");
			header("Location: http://".$this->parent->domain.$this->parent->base.$url.".".$stream_ext);
			exit();
		}
	/*	
		
		header("Content-Type: application/octet-stream");
		header("Content-Type: audio/x-pn-realaudio");
		header("Content-Disposition: filename=download.ram;");
		print $url;
		*/
		
	}

	/* Starts To remove CDATA tags (Function added by Muhammad Imran Mirza )*/
	function uncdata($xml)
    {
        // States:
        //
        //     'out'
        //     '<'
        //     '<!'
        //     '<!['
        //     '<![C'
        //     '<![CD'
        //     '<![CDAT'
        //     '<![CDATA'
        //     'in'
        //     ']'
        //     ']]'
        //
        // (Yes, the states a represented by strings.) 
        //

        $state = 'out';

        $a = str_split($xml);

        $new_xml = '';

        foreach ($a AS $k => $v) {

            // Deal with "state".
            switch ( $state ) {
                case 'out':
                    if ( '<' == $v ) {
                        $state = $v;
                    } else {
                        $new_xml .= $v;
                    }
                break;

                case '<':
                    if ( '!' == $v  ) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                break;

                 case '<!':
                    if ( '[' == $v  ) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                break;

                case '<![':
                    if ( 'C' == $v  ) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                break;

                case '<![C':
                    if ( 'D' == $v  ) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                break;

                case '<![CD':
                    if ( 'A' == $v  ) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                break;

                case '<![CDA':
                    if ( 'T' == $v  ) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                break;

                case '<![CDAT':
                    if ( 'A' == $v  ) {
                        $state = $state . $v;
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                break;

                case '![CDATA[':
                    if ( '[' == $v  ) {


                        $cdata = '';
                        $state = 'in';
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                break;

                case '<![CDATA':
                    if ( '[' == $v  ) {


                        $cdata = '';
                        $state = 'in';
                    } else {
                        $new_xml .= $state . $v;
                        $state = 'out';
                    }
                break;

                case 'in':
                    if ( ']' == $v ) {
                        $state = $v;
                    } else {
                        $cdata .= $v;
                    }
                break;

                case ']':
                    if (  ']' == $v  ) {
                        $state = $state . $v;
                    } else {
                        $cdata .= $state . $v;
                        $state = 'in';
                    }
                break;

                case ']]':
                    if (  '>' == $v  ) {
                        $new_xml .= str_replace('>','&gt;',
                                    str_replace('<','&lt;',
                                    str_replace('"','&quot;',
                                    str_replace('&','&amp;',
                                    $cdata))));
                        $state = 'out';
                    } else {
                        $cdata .= $state . $v;
                        $state = 'in';
                    }
                break;
            } // switch

        }

        //
        // Return.
        //
            return $new_xml;

    }
	/* Ends To remove CDATA tags (Function added by Muhammad Imran Mirza )*/
	
	function import_list($parameters){
	$display_tab="";
		$tmp_dir = $this->check_parameters($this->parent->site_directories,"TMP_UPLOAD_DIR","__NOT_DEFINED__");
		$hidden="";

		if ($tmp_dir == "__NOT_DEFINED__"){
			$out ="
			<module name=\"files\" display=\"form\">
				<form name=\"import_files\" label=\"".LOCALE_ADD_FILES_FORM."\" method=\"post\" width=\"100%\">
					<text><![CDATA[".LOCALE_NO_TEMP_UPLOAD_DIRECTORY_DEFINED."]]></text>
				</form>
			</module>";
		}else{
			$category_list = $this->call_command("CATEGORYADMIN_RETRIEVE_OPTION_LIST", 
				Array(
					"module"=>"FILES_", 
					"label"=>"Choose the Virtual folders this file is available in"
				)
			);
			$category_list = $this->uncdata($category_list);
			
			$directories = $this->call_command("LAYOUT_DISPLAY_UPLOAD_DIRECTORY",array(-1,""));
			$d = dir($tmp_dir."/");
			$file_list = Array();
			while (false !== ($entry = $d->read())) {
				if (($entry != "." ) && ($entry != "..") && ($entry != "Thumbs.db")){
					$size_bytes 	= intval(filesize($tmp_dir."/".$entry));
					$file_dl_total	= ceil($size_bytes/5000);
					$file_dl_days	= floor($file_dl_total / (3600*24));
					$file_dl_hour	= floor(($file_dl_total - ($file_dl_days * (3600*24)))  / 3600);
					$file_dl_min	= floor(($file_dl_total - (($file_dl_days * (3600*24)) + ($file_dl_hour *3600))) / 60);
					$file_dl_sec	= ($file_dl_total % 60);
					$file_dl_size	= "";
					
					if ($file_dl_days>0)
						$file_dl_size = $file_dl_days."d ";
					
					if ($file_dl_hour>0)
						$file_dl_size .= $file_dl_hour."h ";
					
					if ($file_dl_min>0)
						$file_dl_size .= $file_dl_min."m ";
					
					if ($file_dl_sec>0)
						$file_dl_size .= $file_dl_sec."s";
					
					$one_k 	= 1024;
					$one_mb = ($one_k * $one_k);
					if (($size_bytes / $one_mb)>=1){
						$size_value = ($size_bytes / $one_mb);
						$size_des = "".round($size_value,1)." MB";
					}else if (($size_bytes / $one_k)>=1){
						$size_value = $size_bytes / $one_k;
						$size_des = "".round($size_value)." kb";
					}else{
						$size_des = "".$size_bytes." bytes";
					}
					$name = $entry;
					$name_tag = md5($name.uniqid(rand(),1));
					$file_list[count($file_list)]= Array("LABEL"=> "","NAME"=>$entry, "SIZE"=>$size_des, "TAG"=>$name_tag, "DTIME"=>$file_dl_size);
				}
			}
			$d->close(); 
			$max = count($file_list);
			$out ="
			<module name=\"files\" display=\"form\">
				<form name=\"import_file\" label=\"".LOCALE_ADD_FILES_FORM."\" method=\"post\" width=\"100%\">
					<input type=\"hidden\" name=\"command\" value=\"FILES_ENTRY_IMPORT\"/>
					<input type=\"hidden\" name=\"num_of_imports\" value=\"$max\"/>
					<page_sections>
					<section name='importer' label='File Importer'>
					<text><![CDATA[Below are the files that have been FTP'd to your account you may import them into the system now.]]></text>
					<text><![CDATA[<table cellspacing='0' width='100%'>
						<tr>
							<td>".LOCALE_IMPORT."</td>
							<td>".LOCALE_LABEL."</td>
							<td>".LOCALE_NAME."</td>";
					if (!is_numeric($directories)){
					$out .="<td>".LOCALE_DIR."</td>";
					}
					$out .="<td>Download Tag</td>
							<td>Size</td>
							<td>Image Dims</td>
						</tr>";
				$num=0;
				for ($index=0;$index<$max;$index++){
					$num++;
					if (($num % 2) == 0){
						$bgcolor = "#ebebeb";
					} else {
						$bgcolor = "#cccccc";
					}
					$out.="
	<tr bgcolor='$bgcolor'>
		<td><input type='checkbox' style='background:$bgcolor' name='file_import[]' size='25' value='$num'/></td>
		<td><input type='text' name='file_label[]' size='25' value='".$this->check_parameters($file_list[$index],"NAME")."'/></td>
		<td>
	<input type='hidden' name='file_dl_time[]' size='25' value='".$this->check_parameters($file_list[$index],"DTIME")."'/>
	<input type='hidden' name='file_name[]' size='25' value='".$this->check_parameters($file_list[$index],"NAME")."'/>".$this->check_parameters($file_list[$index],"NAME")."</td>
		";
					if (is_numeric($directories)){
						$hidden .="<input type='hidden' name='file_dir[]' value='$directories' />\n";
					}else{
						$out .="<td><select label='".FILE_DIRECTORY."' name='file_dir[]'>$directories</select>\n</td>";
					}
					$out .= "<td><input type='hidden' name='file_tag[]' size='25' value = '".$this->check_parameters($file_list[$index],"TAG")."' />".$this->check_parameters($file_list[$index],"TAG")."</td>
		<td><input type='hidden' name='file_size[]' size='25' value='".$this->check_parameters($file_list[$index],"SIZE")."' />".$this->check_parameters($file_list[$index],"SIZE")."</td>";
					$ext = strtolower(substr($this->file_extension($this->check_parameters($file_list[$index],"NAME")),1));
					$found_image = false;
					for($i=0;$i<count($this->mime_images);$i++){
						$pos = strpos($this->mime_images[$i][1], ":".$ext.":");
		//				print $this->mime_images[$i][1]." [$pos] [$ext]<br>";
						if ($pos===false ){
						} else {
							if ($this->mime_images[$i][0]=="tif")
								$found_image=true;
						}
					}
					if ($found_image){
						$imgsize = GetImageSize($tmp_dir."/".$this->check_parameters($file_list[$index],"NAME"));
						if (is_array($imgsize)){
							$imgwidth = $imgsize[0];
							$imgheight = $imgsize[1];
						} else {
							$imgwidth = "";
							$imgheight = "";
						}
						$out.="<td>
						<input type='hidden' name='image_width[]' value='$imgwidth'/>
						<input type='hidden' name='image_height[]' value='$imgheight'/>
						$imgwidth x $imgheight</td>";
					} else {
						$out.="<td>
						<input type='hidden' name='image_width[]' value='0'/>
						<input type='hidden' name='image_height[]' value='0'/>
						NA</td>";
					}
				$out.="	</tr>
						<tr bgcolor='$bgcolor'>
							<td valign='top'>Description</td><td colspan='5'><textarea rows='6' name='file_description[]' cols='50'></textarea></td>
						</tr>
						<tr bgcolor='$bgcolor'>
							<td valign='top'>Folder to Upload to </td><td colspan='5'><select id='importfolder".$index."' name='vfolder[]'>$category_list</select></td>
						</tr>
						";
						
				}
				$out.="</table><input type='hidden' name='number_of' value='$num'/>]]></text>";
//				if ($this->parent->server[LICENCE_TYPE]==ECMS){
					$import_category_list = $this->call_command("CATEGORYADMIN_RETRIEVE_LIST", 
						Array(
							"module"=>"FILES_", 
							"label"=>"You may add new virtual folders for your imports",
							"output"=>"LIST"
							
						)
					);
					$out .= "	</section><section label=\"".LOCALE_FILES_CATEGORY_TAB."\"";
					if ($display_tab == "categories"){
						$out .= " selected='true'";
					}
					$out .= ">";
					$out .= "$import_category_list";
//				}

				$out .= "</section></page_sections>
					<input type=\"submit\" name=\"SAVE\" value=\"".SAVE_DATA."\" iconify=\"SAVE\"/>
					$hidden
				</form>
			</module>";
		}
		return $out;
	}
	function save_import_list($parameters){
		$tmp_dir = $this->check_parameters($this->parent->site_directories,"TMP_UPLOAD_DIR","__NOT_DEFINED__");
		if ($tmp_dir == "__NOT_DEFINED__"){
			$out ="
			<module name=\"files\" display=\"form\">
				<form name=\"file_importer\" label=\"".LOCALE_ADD_FILES_FORM."\" method=\"post\" width=\"100%\">
					<text><![CDATA[".LOCALE_NO_TEMP_UPLOAD_DIRECTORY_DEFINED."]]></text>
				</form>
			</module>";
		}else{
			$data			= "";
			$file_import	= $this->check_parameters($parameters,"file_import");
			$file_label		= $this->check_parameters($parameters,"file_label");
			$file_name		= $this->check_parameters($parameters,"file_name");
			$file_dir		= $this->check_parameters($parameters,"file_dir");
			$file_tag		= $this->check_parameters($parameters,"file_tag");
			$file_size		= $this->check_parameters($parameters,"file_size");
			$image_width	= $this->check_parameters($parameters,"image_width");
			$image_height	= $this->check_parameters($parameters,"image_height");
			$description	= $this->check_parameters($parameters,"file_description");
			$number_of		= $this->check_parameters($parameters,"number_of");
			$file_dl_time	= $this->check_parameters($parameters,"file_dl_time");
			$vfolder		= $this->check_parameters($parameters,"vfolder");
			$new_categories	= $this->check_parameters($parameters,"newCategories");
			$root=$this->check_parameters($this->parent->site_directories,"ROOT");
			$m =count($file_import);
			if($m>0){
				for ($z=0 ; $z<$m ; $z++){
					$index = $file_import[$z]-1;
					$uri_new  = $root."/".$this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($file_dir[$index])).$file_tag[$index].$this->file_extension($file_name[$index]);
					if (file_exists($tmp_dir."/".str_replace(Array(" "),Array("%20"),$file_name[$index]))){
						$um = umask(0);
						chmod($tmp_dir."/".$file_name[$index],LS__FILE_PERMISSION);
						umask($um);
						rename($tmp_dir."/".$file_name[$index],$uri_new);
						if (file_exists($uri_new)){
							$now = $this->libertasGetDate("Y/m/d H:i:s");
							$sql = "insert into file_info (
								file_name, 
								file_label, 
								file_directory,
								file_dl_sec,
								file_client,
								file_size,
								file_creation_date,
								file_user,
								file_md5_tag,
								file_description,
								file_width,
								file_height
							) values (
								'".$this->validate($file_name[$index])."',
								'".$this->validate($file_label[$index])."',
								'".$file_dir[$index]."',
								'".$file_dl_time[$index]."',
								'".$this->client_identifier."',
								'".$file_size[$index]."',
								'".$now."',
								'".$_SESSION["SESSION_USER_IDENTIFIER"]."',
								'".$file_tag[$index]."',
								'".$description[$index]."',
								'".$image_width[$index]."',
								'".$image_height[$index]."'
							);";
							$this->call_command("DB_QUERY",array($sql));
								$sql = "select file_identifier from file_info where file_client = $this->client_identifier and file_directory = '".$file_dir[$index]."' and file_name = '".$this->validate($file_name[$index])."' and file_user = '".$_SESSION["SESSION_USER_IDENTIFIER"]."' and file_label = '".$this->validate($file_label[$index])."' and file_creation_date = '$now' and file_md5_tag = '".$file_tag[$index]."' ";
								$result  = $this->call_command("DB_QUERY",Array($sql));
								$identifier=-1;
								while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	                            	$identifier = $r["file_identifier"];
	       	                    }
								if ($identifier!=-1){
	  								$this->call_command("CATEGORYADMIN_TO_OBJECT_UPDATE", 
										Array(
											"new_categories"=> $new_categories,
											"data_list"		=> Array($vfolder[$index]),
											"module"		=> "FILES_",
											"identifier"	=> $identifier
										)
									);
								}
							$data .= "<li>" . $this->validate($file_label[$index]) . " (Successfully Imported)</li>";
						} else {
							$data .= "<li>" . $this->validate($file_label[$index]) . " (<font color='red'>Failed to move</font>)</li>";
						}
					} else {
						$data .= "<li>" . $this->validate($file_label[$index]) . " (<font color='red'>Source Not found</font>)</li>";
					}
				}
			}
			$out ="
			<module name=\"files\" display=\"form\">
		<page_options>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","FILES_LIST",LOCALE_CANCEL));
		$out .= "</page_options><form label='File import complete'>
			<text><![CDATA[File import report]]></text>
			<text><![CDATA[$data]]></text>
			</form>
			</module>";
		}
		return $out;
	}
	
	function files_list_items($parameters){
		$list  = $this->check_parameters($parameters,"list","");
		if(is_array($list)){
			$list = join(",",$list	);
		}
		$type  = $this->check_parameters($parameters,"type");
		if($list!=""){
			$sql = "select * from file_info where file_identifier in (".$list.") and file_client=$this->client_identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$out ="";
			$file_associations_identifiers="";
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$file_id		 = $r["file_identifier"];
				$file_label		 = $this->convert_amps($r["file_label"]);
				$file_name		 = $this->convert_amps($r["file_name"]);
				$file_mime		 = $r["file_mime"];
				$file_size		 = $r["file_size"];
				$file_directory	 = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"]));
				$file_width		 = $this->check_parameters($r,"file_width");
				$file_date		 = $this->check_parameters($r,"file_creation_date");
				$file_height	 = $this->check_parameters($r,"file_height");
				$file_description= $r["file_description"];
				$file_md5		 = $r["file_md5_tag"];
				$file_dl_sec	 = $this->check_parameters($r,"file_dl_sec");
				if($type==""){
					$out	.= "<file identifier=\"$file_id\">
									<url><![CDATA[".$this->convert_amps($r["file_name"])."]]></url>
									<label><![CDATA[".$this->convert_amps($r["file_label"])."]]></label>
									<ext><![CDATA[".$this->file_extension($r["file_name"])."]]></ext>
									<md5><![CDATA[$file_md5]]></md5>
									<name><![CDATA[$file_name]]></name>
									<mime><![CDATA[$file_mime]]></mime>
									<description><![CDATA[$file_description]]></description>
									<directory><![CDATA[$file_directory]]></directory>
									<size><![CDATA[$file_size]]></size>
									<width><![CDATA[$file_width]]></width>
									<height><![CDATA[$file_height]]></height>
									<icon><![CDATA[".$this->get_mime_image($r["file_name"])."]]></icon>
									<download_time><![CDATA[$file_dl_sec]]></download_time>
									<date><![CDATA[$file_date]]></date>
								</file>\n";
					$file_associations_identifiers .="".$r["file_identifier"].",";
				} else {
					$out  .="<file_info logo='/libertas_images/icons/mime-images/".$this->call_command("FILES_GET_MIME_IMAGE",Array($r["file_name"])).".gif' identifier='".$r["file_identifier"]."' rank='".$this->check_parameters($r,"file_rank")."'><![CDATA[".$r["file_label"]."]]></file_info>";
					$file_associations_identifiers .="".$r["file_identifier"].",";
				}
        	}
    	    $this->call_command("DB_FREE",Array($result));
			if($type==""){
				return "<files>$out</files>";
			} else {
				return Array("<file_list>$out</file_list>",$file_associations_identifiers);
			}
		} else {
			return Array("","");
		}
	}
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    - relate a file to an module object
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	function manage_module_relationship($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"manage_module_webobjects",__LINE__,print_r($parameters,true)));
		}
//		print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameters,true)."</p></li>";
		$owner_mod	= $this->check_parameters($parameters,"owner_module");
		$label		= $this->check_parameters($parameters,"label");
		$owner_id	= $this->check_parameters($parameters,"owner_id");
		$file_id	= $this->check_parameters($parameters,"file_identifier",-1);
		/**
		* storing file to object relationship 
		*/
		$index	= 1;
		$sql	= Array();
		$sql[count($sql)] = "delete from file_to_object
					where 
					fto_client	=	$this->client_identifier and 
					fto_object	=	$owner_id and
					fto_module	=	'$owner_mod'";
		if($file_id!=-1 && $file_id!=""){
			if (!is_array($file_id)){
				$sql[count($sql)] = "insert into file_to_object
						(fto_client, fto_object, fto_title, fto_module, fto_file, fto_rank) 
					values 
						($this->client_identifier, $owner_id, '$label', '$owner_mod', $file_id, $index);";
			} else {
				$max_files= count($file_id);
				for ($i=0; $i<$max_files;$i++){
					if($file_id[$i]."" != ""){
						$sql[count($sql)] = "insert into file_to_object
							(fto_client, fto_object, fto_title, fto_module, fto_file, fto_rank) 
						values 
							($this->client_identifier, $owner_id, '".$this->check_parameters($label,$i)."', '$owner_mod', ".$file_id[$i].", $index);";
					}
				}
			}
		}
		$max_files= count($sql);
		for ($i=0; $i<$max_files;$i++){
//			print "<li>".__FILE__."@".__LINE__."<p>$sql[$i]</p></li>";
			$this->call_command("DB_QUERY",Array($sql[$i]));
		}
//		$this->exitprogram();
	}

	function module_list_summary_file_details($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$module 			= $this->check_parameters($parameters,"module",-1);
		$sql = "select file_info.* from file_info 
			inner join file_to_object on fto_file = file_identifier and fto_client=file_client and fto_object=$identifier and fto_module='$module'
		where 
			file_info.file_client = $this->client_identifier order by fto_rank";
	//	print $sql;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",Array($sql));
		$page_documents= Array();
		
		$file_list="";
		$out="";
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$file_id		 = $r["file_identifier"];
				$file_label		 = $this->convert_amps($r["file_label"]);
				$file_name		 = $this->convert_amps($r["file_name"]);
				$file_mime		 = $r["file_mime"];
				$file_size		 = $r["file_size"];
				$file_directory	 = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"]));
				$file_width		 = $this->check_parameters($r,"file_width");
				$file_date		 = $this->check_parameters($r,"file_creation_date");
				$file_height	 = $this->check_parameters($r,"file_height");
				$file_description= $r["file_description"];
				$file_md5		 = $r["file_md5_tag"];
				$file_dl_sec	 = $this->check_parameters($r,"file_dl_sec");
				$out		.= "<file identifier=\"$file_id\">
									<url><![CDATA[".$this->convert_amps($r["file_name"])."]]></url>
									<label><![CDATA[".$this->convert_amps($r["file_label"])."]]></label>
									<md5><![CDATA[$file_md5]]></md5>
									<name><![CDATA[$file_name]]></name>
									<mime><![CDATA[$file_mime]]></mime>
									<description><![CDATA[$file_description]]></description>
									<directory><![CDATA[$file_directory]]></directory>
									<size><![CDATA[$file_size]]></size>
									<width><![CDATA[$file_width]]></width>
									<height><![CDATA[$file_height]]></height>
									<icon><![CDATA[".$this->get_mime_image($r["file_name"])."]]></icon>
									<download_time><![CDATA[$file_dl_sec]]></download_time>
									<date><![CDATA[$file_date]]></date>
								</file>\n";
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
		return $out;
	}
	
	
	function to_object_copy($parameters){
//	;
		$from_id	= $this->check_parameters($parameters,"from_id");
		$to_id  	= $this->check_parameters($parameters,"to_id");
		$module		= $this->check_parameters($parameters,"module");
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"function to_object_copy(parameters)",__LINE__,print_r($parameters,true)));}
		$sql = "select * from file_to_object where fto_object = $from_id and fto_client = $this->client_identifier and fto_module='$module' order by fto_rank";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$sql ="insert into file_to_object
								(fto_client, fto_object, fto_title, fto_module, fto_file, fto_rank) 
				values 
					($this->client_identifier, $to_id, '".$r["fto_title"]."', '$module', ".$r["fto_file"].", ".$r["fto_rank"].");";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->call_command("DB_QUERY",Array($sql));
        }
        $this->call_command("DB_FREE",Array($result));
	}
	
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    -   						F I L E   L I S T   G E N E R A T O R   F U N C T I O N S
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    - Functions
    -	file_list_generator()	- list generated lists of files
	-   file_list_modify()		- add/edit list
	-	file_list_remove()		- erase list
	-	file_list_save()		- save list details
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	function file_list_generator($parameters){
		$orderby=0;
		$page_boolean = "or";
		$where="";
		$join = "";
		$sql = "Select 
					*
				from file_list
				where 
					file_list.fi_client=$this->client_identifier
				order by
					file_list.fi_identifier desc
				";

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables = Array();
		$variables["FILTER"]			= "";//$this->filter($parameters);
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		$variables["PAGE_BUTTONS"] = Array(
			Array("ADD",$this->module_command."LIST_ADD",ADD_NEW,"","","","")
		);
		if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			
			$page = $this->check_parameters($parameters,"page","1");
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$goto = ((--$page)*$this->page_size);
			
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
			}
			if ($goto+$this->page_size>$number_of_records){
				$finish = $number_of_records;
			}else{
				$finish = $goto+$this->page_size;
			}
			$goto++;
			$page++;
			
			$num_pages=floor($number_of_records / $this->page_size);
			$remainder = $number_of_records % $this->page_size;
			if ($remainder>0){
				$num_pages++;
			}
			
			$counter=0;
			$variables["PAGE_COMMAND"] 		= "FILES_LIST_GENERATOR";
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			
			$variables["RESULT_ENTRIES"] =Array();
			$list ="";
			$links = Array();
			$counter=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) && ($counter<$this->page_size)){
				$counter++;
				$i = count($variables["RESULT_ENTRIES"]);
				
				$variables["RESULT_ENTRIES"][$i]=Array(
				"identifier"		=> $r["fi_identifier"],
				"ENTRY_BUTTONS" 	=> Array(),
				"attributes"		=> Array(
						Array(LOCALE_LABEL,$this->convert_amps($r["fi_label"]),"TITLE","NO")
					)
				);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("EDIT",$this->module_command."LIST_EDIT",EDIT_EXISTING);
				$variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("REMOVE",$this->module_command."LIST_REMOVE_CONFIRM",REMOVE_EXISTING);
			
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}
	function file_list_modify($parameters){
		$fi_identifier 		=	$this->check_parameters($parameters,"identifier",-1);
		$display_tab		=	$this->check_parameters($parameters,"display_tab");
		$label				=	ADD_NEW;
		$fi_label			=	"";
		$fi_status			=	0;
		$fi_set_inheritance	=	0;
		$fi_all_locations	=	0;
		$fi_menu_only		=	0;
		$fi_display			=	0;
		$fi_grouping		=	0;
		$file_associations	=	"";
		$file_associations_identifiers 	= "";
		$menu_locations		=	Array();
		if ($fi_identifier!=-1){
			$sql = "Select * from file_list where fi_identifier=".$fi_identifier." and fi_client=$this->client_identifier";
//			print $sql;
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$fi_label			= $this->convert_amps($r["fi_label"]);
					$fi_all_locations	= $r["fi_all_locations"];
					$fi_set_inheritance	= $r["fi_set_inheritance"];
					$fi_display			= $r["fi_display"];
					$fi_status			= $r["fi_status"];
					$fi_menu_only		= $r["fi_menu_only"];
					$fi_grouping		= $r["fi_grouping"];
				}
				$menu_locations = $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
					Array(
						"module"		=> "FILESLIST_",
						"identifier"	=> $fi_identifier
					)
				);
				$sql = "SELECT * FROM file_to_object inner join file_info on fto_file=file_identifier where fto_object = $fi_identifier and fto_client=$this->client_identifier and fto_module='FILESLIST_' order by fto_rank";
				if($result = $this->call_command("DB_QUERY",array($sql))) {
					while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
						$file_associations_identifiers .="".$r["file_identifier"].",";
						$file_associations  .="<file_info logo='/libertas_images/icons/mime-images/".$this->call_command("FILES_GET_MIME_IMAGE",Array($r["file_name"])).".gif' identifier='".$r["file_identifier"]."' rank='".$this->check_parameters($r,"file_rank")."'><![CDATA[".$r["file_label"]."]]></file_info>";
					}
				}
			}
			$label=LOCALE_FILE_EDIT_LABEL;
		}
		
		$file_associations ="<file_list>$file_associations</file_list>";
		$required = "";
		
		$out  = "<module name=\"files\" display=\"form\">";
		$out .= "<page_options>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","FILES_LIST_GENERATOR",LOCALE_CANCEL));
		$out .= "</page_options>";
		$out .= "<form name=\"file_form\" method=\"post\" label=\"$label\">";
		$out .= "	<input type=\"hidden\" name=\"file_associations\"><![CDATA[$file_associations_identifiers]]></input>";
		$out .= "<input type=\"hidden\" name=\"fi_identifier\" value=\"".$fi_identifier."\"/>";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"FILES_LIST_SAVE\"/>";
		$out .= "<page_sections>";
		$out .= "	<section label='Setup'";
		if ($display_tab==""){
			$out .= " selected='true'";
		}
		$out .= ">";
		$out .= "		<input required='YES' type='text' label='".WHAT_LABEL."' name='fi_label' ><![CDATA[$fi_label]]></input>";
		$out .= "		<select label='".LOCALE_STATUS."' name='fi_status' >";
		$out .= "		<option value='0'";
		if($fi_status==0){
			$out .=" selected='true'";
		}
		$out .=">Not Live</option>";
		$out .= "		<option value='1'";
		if($fi_status==1){
			$out .=" selected='true'";
		}
		$out .=">Live</option>";
		$out .= "		</select>";
		$out .= "		<select label='".LOCALE_GROUP_FILES_BY."' name='fi_grouping' >";
		$fi_grouping_list = Array(
			Array(0, "No grouping"), 
			Array(1, "Group by year"), 
			Array(2, "Group by year/month")
		);
//		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$fi_grouping_list[count($fi_grouping_list)] = Array(3, "Group by category");
//		}
		$m=count($fi_grouping_list);
		for($i=0;$i<$m;$i++){
			$out .= "<option value='$i'";
			if($fi_grouping==$i){
				$out .=" selected='true'";
			}
			$out .="><![CDATA[".$fi_grouping_list[$i][1]."]]></option>";
		}
		$out .= "		</select>";
		$out .= "		<select label='".LOCALE_FILES_CHOOSE_DISPLAY_FORMAT."' name='fi_display' >";
		$out .= "		<option value='Table'";
		if($fi_display=="Table"){
			$out .=" selected='true'";
		}
		$out .=">Table</option>";
		$out .= "		<option value='List'";
		if($fi_display=="List"){
			$out .=" selected='true'";
		}
		$out .=">List</option>";
		$out .= "		<option value='Title and Summary'";
		if($fi_display=="Title and Summary"){
			$out .=" selected='true'";
		}
		$out .=">Title and Summary</option>";
		$out .= "		<option value='Title Only'";
		if($fi_display=="Title Only"){
			$out .=" selected='true'";
		}
		$out .=">Title Only</option>";
		$out .= "		<option value='Date, title and size'";
		if($fi_display=="Date, title and size"){
			$out .=" selected='true'";
		}
		$out .=">Date, title and size</option>";
		$out .= "		</select>";
		$out .= "		<radio label='".LOCALE_FILE_DOWNLOAD_ON_MENU_OPTION."' name='fi_menu_only' >";
		$out .= "		<option value='1'";
		if($fi_menu_only==1){
			$out .=" selected='true'";
		}
		$out .=">Menu location only</option>";
		$out .= "		<option value='0'";
		if($fi_menu_only==0){
			$out .=" selected='true'";
		}
		$out .=">Menu location and page content</option>";
		$out .= "		</radio>";

		$web_containers = split("~----~",$this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>"FILESLIST_", "identifier"=>$fi_identifier)));
		if ($web_containers[0]!=""){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}
		$out .= "	</section>";
		$out .= $this->location_tab($fi_all_locations, $fi_set_inheritance, $menu_locations, $display_tab);

			$out .= "		<section label=\"".ENTRY_FILES_ASSOCIATED."\" name=\"file_list_associations\" command=\"FILES_LIST\" link='file_associations' return_command='FILES_LIST_FILE_DETAIL'";
			if ($display_tab=="files"){
				$out .= " selected='true'";
			}
			$out .= ">$file_associations</section>";
		$out .= "</page_sections>";
		$out .= "<input iconify='SAVE' type='submit' value='".SAVE_DATA."' />";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
	}
	
	function file_list_remove($parameters){
		$fi_identifier			= $this->check_parameters($parameters,"identifier",-1);
		$sql = "delete from file_list where fi_identifier = $fi_identifier and fi_client = $this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
				
		$sql = "delete from file_to_object where fto_object = $fi_identifier and fto_client = $this->client_identifier and fto_module='FILESLIST_'";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "delete from menu_to_object where mto_object = $fi_identifier and mto_client = $this->client_identifier and mto_module='FILESLIST_'";
		$this->call_command("DB_QUERY",Array($sql));
		$this->call_command("WEBOBJECTS_MANAGE_MODULE",
			Array(
				"owner_module" 	=> "FILESLIST_",
				"owner_id" 		=> $fi_identifier,
				"wo_command"	=> "FILES_DOWNLOAD_DISPLAY",
				"cmd"			=> "REMOVE"
			)
		);

		$this->tidyup_display_commands(Array("all_locations"=> 0, "tidy_table"=> "file_list", "tidy_field_starter"	=> "fi_","tidy_webobj" => "FILES_DOWNLOAD_DISPLAY","tidy_module" => "FILESLIST_"));
	}
	
	function file_list_save($parameters){
//		print_r($parameters);
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        -	setup tab data
	*/ 
		$fi_identifier			= $this->check_parameters($parameters,"fi_identifier",-1);
		$fi_label				= $this->validate($this->check_parameters($parameters,"fi_label"));
		$fi_status				= $this->check_parameters($parameters,"fi_status",0);
		$fi_display				= $this->check_parameters($parameters,"fi_display","List");
		$fi_menu_only			= $this->check_parameters($parameters,"fi_menu_only",0);
		$fi_grouping			= $this->check_parameters($parameters,"fi_grouping",0);
		$currentlyhave			= $this->check_parameters($parameters,"currentlyhave");
		$web_containers			= $this->check_parameters($parameters,"web_containers",Array());

		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        -	menu locations tab data
	*/ 
		$all_locations			= $this->check_parameters($parameters, "all_locations",0);
		$set_inheritance		= $this->check_parameters($parameters, "set_inheritance",0);
		$menu_locations			= $this->check_parameters($parameters, "menu_locations", Array());
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        -	File assoication tab data
	*/ 
		$file_list_associations	= $this->check_parameters($parameters,"file_list_associations","");
		/*
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
        -	Either add or update this record
	*/ 
		if($fi_identifier==-1){
			$now = $this->LibertasGetDate();
			/*
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            - Add a new record to the table
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            */
			$sql = "insert into file_list (fi_client, fi_label, fi_status, fi_display, fi_set_inheritance, fi_all_locations, fi_date_created, fi_menu_only, fi_grouping)
			values 
			($this->client_identifier, '$fi_label', $fi_status, '$fi_display', $set_inheritance, $all_locations, '$now', $fi_menu_only, $fi_grouping)";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "select * from file_list where fi_client = $this->client_identifier and fi_date_created='$now' and fi_label='$fi_label'";
//			print $sql;
            $result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$fi_identifier= $r["fi_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			$cmd="ADD";
		} else {
			/*
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            - update an existing record
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            */
			$sql = "update file_list set
						fi_label			= '$fi_label',
						fi_status			= '$fi_status',
						fi_display			= '$fi_display',
						fi_set_inheritance	= '$set_inheritance',
						fi_all_locations	= '$all_locations',
						fi_menu_only		= '$fi_menu_only',
						fi_grouping			= '$fi_grouping'
					WHERE 
						fi_identifier		= $fi_identifier and 
						fi_client			= $this->client_identifier
			";
//			print "<li><p>$sql</p></li>";
			$this->call_command("DB_QUERY",Array($sql));
			$cmd="UPDATE";
		}
		/**
		* update/create the webobject as needed
	*/ 
		$this->call_command("WEBOBJECTS_MANAGE_MODULE",
			Array(
				"owner_module" 	=> "FILESLIST_",
				"owner_id" 		=> $fi_identifier,
				"label" 		=> $fi_label,
				"wo_command"	=> "FILES_DOWNLOAD_DISPLAY",
				"cmd"			=> $cmd,
				"previous_list" => $currentlyhave,
				"new_list"		=> $web_containers
			)
		);
		/**
		* link to the menu table any menu locations that are to have this item appearing in 
	*/ 
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> "FILESLIST_",
				"identifier"	=> $fi_identifier,
				"all_locations"	=> $all_locations
			)
		);
		$this->call_command("FILES_MANAGE_MODULE", 
			Array(
				"owner_module" 		=> "FILESLIST_",
				"label" 			=> $fi_label,
				"owner_id"			=> $fi_identifier,
				"file_identifier"	=> $this->check_parameters($parameters,"id",Array())
			)
		);
		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance("FILES_DOWNLOAD_DISPLAY",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=>$child_locations,
					"module"		=> "FILESLIST_",
					"identifier"	=> $fi_identifier,
					"all_locations"	=> $all_locations,
					"delete"		=>0
				)
			);
			$this->set_inheritance(
				"FILES_DOWNLOAD_DISPLAY",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"=> "FILESLIST_",
					"condition"=> "fi_set_inheritance =1 and ",
					"client_field"=> "fi_client",
					"table"	=> "file_list",
					"primary"=> "fi_identifier"
					)
				).""
			);
		}
		$this->tidyup_display_commands(Array("all_locations"=> $all_locations, "tidy_table"=> "file_list", "tidy_field_starter"	=> "fi_","tidy_webobj" => "FILES_DOWNLOAD_DISPLAY","tidy_module" => "FILESLIST_"));
		return "";
		
	}
	function files_download_display($parameters){
		$owner	= $this->check_parameters($parameters,"wo_owner_id",$this->check_parameters($parameters,"identifier"));
		$cml	= $this->check_parameters($parameters,"current_menu_location",-1);
//		print_r($parameters);
		$files=Array();
		$display="";
		$grouping=-1;

		$sql = "select * from file_list 
			inner join menu_to_object on mto_object = fi_identifier and mto_module='FILESLIST_' and mto_client= fi_client 
			inner join file_to_object on fto_object = fi_identifier and fto_module='FILESLIST_' and fto_client= fi_client 
			inner join file_info on file_identifier = fto_file and file_client = fi_client 
			left outer join category_to_object on cto_object = fto_file and cto_module='FILES_' and cto_client= fi_client 
			left outer join category on cat_identifier = cto_clist and cto_module='FILES_' and cto_client= fi_client 
			where mto_menu = $cml and fi_identifier = $owner and fi_client=$this->client_identifier and fi_status=1 order by file_creation_date";
//print "<p>$sql</p>";
        $result  = $this->call_command("DB_QUERY",Array($sql));
		$years	= Array();
		$cat	= Array();
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$cy = date("Y", strtotime($r["file_creation_date"]));
			$cm = date("M", strtotime($r["file_creation_date"]));
			$cn = date("n", strtotime($r["file_creation_date"]));
			$cat[$this->check_parameters($r,"cat_label","undefined")] = 1;
			if(empty($years[$cy])){
				$years[$cy] = Array();
				for($i=1;$i<13;$i++){
					$years[$cy][$i] = 0;
				}
			} else {
				$years[$cy][$cn] ++;
			}
			$pos = count($files);
        	$files[$pos] = Array(
				"label"			=> $r["file_label"],
				"identifier"	=> $r["file_identifier"],
				"name"			=> $r["file_name"],
				"description"	=> $r["file_description"],
				"md5"			=> $r["file_md5_tag"],
				"mime"			=> $r["file_mime"],
				"download"		=> $r["file_dl_sec"],
				"size" 			=> $r["file_size"],
				"year" 			=> $cy,
				"month" 		=> $cm,
				"month_num" 	=> $cn,
				"directory" 	=> $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"])),
				"icon" 			=> $this->file_extension($r["file_name"]),
				"date"			=> $r["file_creation_date"],
				"width"			=> $r["file_width"],
				"height"		=> $r["file_height"],
				"category"		=> $this->check_parameters($r,"cat_label","undefined")
			);
			$grouping	= $r["fi_grouping"];
			$menu_only	= $r["fi_menu_only"];
			$display	= $r["fi_display"];
			$fi_label	= $r["fi_label"];
        }
        $this->call_command("DB_FREE",Array($result));
		if($grouping!=-1){
			$out  = "<module name=\"files\" display=\"download\">";
			$out .="<display><![CDATA[".strToUpper($display)."]]></display>";
			$out .="<label><![CDATA[".strToUpper($fi_label)."]]></label><files>";
			$output =Array();
			if ($grouping==1){ // year only
				foreach ($years as $y => $val){
					for ($i=0;$i<count($files);$i++){
						if ($files[$i]["year"]==$y){
							if(empty($output[$files[$i]["year"]])){
							$output[$files[$i]["year"]]="";
							}
							$output[$files[$i]["year"]] .= "
								<file identifier=\"".$files[$i]["identifier"]."\">
									<url><![CDATA[".$this->convert_amps($files[$i]["name"])."]]></url>
									<label><![CDATA[".join("",split("\&quot;",$this->split_me($this->convert_amps($files[$i]["label"]),"\"", "\\\"")))."]]></label>
									<md5><![CDATA[".$files[$i]["md5"]."]]></md5>
									<name><![CDATA[".$files[$i]["name"]."]]></name>
									<mime><![CDATA[".$files[$i]["mime"]."]]></mime>
									<description><![CDATA[". join("", split("&quot;",$files[$i]["description"]) ) ."]]></description>
									<directory><![CDATA[".$files[$i]["directory"]."]]></directory>
									<size><![CDATA[".$files[$i]["size"]."]]></size>
									<width><![CDATA[".$files[$i]["width"]."]]></width>
									<height><![CDATA[".$files[$i]["height"]."]]></height>
									<icon><![CDATA[".$this->get_mime_image($files[$i]["name"])."]]></icon>
									<download_time><![CDATA[".$files[$i]["download"]."]]></download_time>
									<date><![CDATA[".$this->libertasGetDate("r",strtotime($files[$i]["date"]))."]]></date>
								</file>\n
							";
						}
					}
				}
				foreach($output as $index => $value){
					$out .= "<group label='$index'>$value</group>";
				}
			} else if ($grouping==2){ // year and month
				foreach ($years as $y => $val){
					for ($i=0;$i<count($files);$i++){
						if ($files[$i]["year"]==$y){
							if(empty($output[$files[$i]["year"]])){
								$output[$files[$i]["year"]]=Array();
							}
							if(empty($output[$files[$i]["year"]][$files[$i]["month"]])){
								$output[$files[$i]["year"]][$files[$i]["month"]]="";
							}
							$output[$files[$i]["year"]][$files[$i]["month"]] .= "
								<file identifier=\"".$files[$i]["identifier"]."\">
									<url><![CDATA[".$this->convert_amps($files[$i]["name"])."]]></url>
									<label><![CDATA[".join("",split("\&quot;",$this->split_me($this->convert_amps($files[$i]["label"]),"\"", "\\\"")))."]]></label>
									<md5><![CDATA[".$files[$i]["md5"]."]]></md5>
									<name><![CDATA[".$files[$i]["name"]."]]></name>
									<mime><![CDATA[".$files[$i]["mime"]."]]></mime>
									<description><![CDATA[". join("", split("&quot;",$files[$i]["description"]) ) ."]]></description>
									<directory><![CDATA[".$files[$i]["directory"]."]]></directory>
									<size><![CDATA[".$files[$i]["size"]."]]></size>
									<width><![CDATA[".$files[$i]["width"]."]]></width>
									<height><![CDATA[".$files[$i]["height"]."]]></height>
									<icon><![CDATA[".$this->get_mime_image($files[$i]["name"])."]]></icon>
									<download_time><![CDATA[".$files[$i]["download"]."]]></download_time>
									<date><![CDATA[".$this->libertasGetDate("r",strtotime($files[$i]["date"]))."]]></date>
								</file>\n
							";
						}
					}
				}
				foreach($output as $index => $value){
					$out .= "<group label='$index'>";
					foreach($value as $month => $file_info){
						$out .= "<group label='$month'>";
							$out .= $file_info;
						$out .= "</group>";
					}
					$out .= "</group>";
				}
			} else if ($grouping==3){ // year and month
				foreach ($cat as $cat_label => $val){
					for ($i=0;$i<count($files);$i++){
						if ($files[$i]["category"]==$cat_label){
							if(empty($output[$cat_label])){
								$output[$cat_label]="";
							}
							$output[$cat_label] .= "
								<file identifier=\"".$files[$i]["identifier"]."\">
									<url><![CDATA[".$this->convert_amps($files[$i]["name"])."]]></url>
									<label><![CDATA[".join("",split("\&quot;",$this->split_me($this->convert_amps($files[$i]["label"]),"\"", "\\\"")))."]]></label>
									<md5><![CDATA[".$files[$i]["md5"]."]]></md5>
									<name><![CDATA[".$files[$i]["name"]."]]></name>
									<mime><![CDATA[".$files[$i]["mime"]."]]></mime>
									<description><![CDATA[". join("", split("&quot;",$files[$i]["description"]) ) ."]]></description>
									<directory><![CDATA[".$files[$i]["directory"]."]]></directory>
									<size><![CDATA[".$files[$i]["size"]."]]></size>
									<width><![CDATA[".$files[$i]["width"]."]]></width>
									<height><![CDATA[".$files[$i]["height"]."]]></height>
									<icon><![CDATA[".$this->get_mime_image($files[$i]["name"])."]]></icon>
									<download_time><![CDATA[".$files[$i]["download"]."]]></download_time>
									<date><![CDATA[".$this->libertasGetDate("r",strtotime($files[$i]["date"]))."]]></date>
								</file>\n
							";
						}
					}
				}
				foreach($output as $index => $value){
					$out .= "<group label='$index'>";
					$out .= $value;
					$out .= "</group>";
				}
			} else {
				for ($i=0;$i<count($files);$i++){
					$out .= "
						<file identifier=\"".$files[$i]["identifier"]."\">
							<url><![CDATA[".$this->convert_amps($files[$i]["name"])."]]></url>
							<label><![CDATA[".join("",split("\&quot;",$this->split_me($this->convert_amps($files[$i]["label"]),"\"", "\\\"")))."]]></label>
							<md5><![CDATA[".$files[$i]["md5"]."]]></md5>
							<name><![CDATA[".$files[$i]["name"]."]]></name>
							<mime><![CDATA[".$files[$i]["mime"]."]]></mime>
							<description><![CDATA[". join("", split("&quot;",$files[$i]["description"]) ) ."]]></description>
							<directory><![CDATA[".$files[$i]["directory"]."]]></directory>
							<size><![CDATA[".$files[$i]["size"]."]]></size>
							<width><![CDATA[".$files[$i]["width"]."]]></width>
							<height><![CDATA[".$files[$i]["height"]."]]></height>
							<icon><![CDATA[".$this->get_mime_image($files[$i]["name"])."]]></icon>
							<download_time><![CDATA[".$files[$i]["download"]."]]></download_time>
							<date><![CDATA[".$this->libertasGetDate("r",strtotime($files[$i]["date"]))."]]></date>
						</file>";
				}
			}
			$out .= "</files></module>";
			return $out;
		} else {
			return "";
		}
	}
	
	function update_sites(){
		/**
		* todo
        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		
		1. add files_catagorisation for each client
		
        */
	}
	
	function file_import_image($parameters){
		$path		= $this->check_parameters($parameters,"path");
		$source		= $this->check_parameters($parameters,"source");
		$uploadsPath= $this->check_parameters($parameters,"uppath");
		$uploadsId	= $this->check_parameters($parameters,"upid");
		$description= $this->check_parameters($parameters,"description");
		if($source==""){
			return $source;
		} else {
			$imgsize = GetImageSize($path."/".$source);
			if (is_array($imgsize)){
				$image_width	= $imgsize[0];
				$image_height	= $imgsize[1];
			} else {
				$image_width	= "";
				$image_height	= "";
			}
			$size_bytes 	= intval(filesize($path."/".$source));
			$file_dl_total	= ceil($size_bytes/5000);
			$file_dl_days	= floor($file_dl_total / (3600*24));
			$file_dl_hour	= floor(($file_dl_total - ($file_dl_days * (3600*24)))  / 3600);
			$file_dl_min	= floor(($file_dl_total - (($file_dl_days * (3600*24)) + ($file_dl_hour *3600))) / 60);
			$file_dl_sec	= ($file_dl_total % 60);
			$file_dl_size	= "";
			
			if ($file_dl_days>0)
				$file_dl_size	  = $file_dl_days."d ";
			if ($file_dl_hour>0)
				$file_dl_size	 .= $file_dl_hour."h ";
			if ($file_dl_min>0)
				$file_dl_size	 .= $file_dl_min."m ";
			if ($file_dl_sec>0)
				$file_dl_size	 .= $file_dl_sec."s";
			$one_k 	= 1024;
			$one_mb = ($one_k * $one_k);
			if (($size_bytes / $one_mb)>=1){
				$size_value = ($size_bytes / $one_mb);
				$size_des = "".round($size_value,1)." MB";
			}else if (($size_bytes / $one_k)>=1){
				$size_value = $size_bytes / $one_k;
				$size_des = "".round($size_value)." kb";
			}else{
				$size_des = "".$size_bytes." bytes";
			}
			$name = $source;
			$file_tag = md5($name.uniqid(rand(),1));
			$now = $this->libertasGetDate();
			$ext = $this->file_extension($source);
			$sql = "insert into file_info (
				file_name, 
				file_label, 
				file_directory,
				file_dl_sec,
				file_client,
				file_size,
				file_creation_date,
				file_user,
				file_md5_tag,
				file_description,
				file_width,
				file_height,
				file_mime
			) values (
				'".$this->validate($source)."',
				'".$this->validate($source)."',
				'".$uploadsId."',
				'".$file_dl_size."',
				'".$this->client_identifier."',
				'".$size_des."',
				'".$now."',
				'".$_SESSION["SESSION_USER_IDENTIFIER"]."',
				'".$file_tag."',
				'".$description."',
				'".$image_width."',
				'".$image_height."',
				'image/$ext'
			);";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "select file_identifier from file_info where file_client = $this->client_identifier and 
			file_directory = '".$uploadsId."' and file_name = '".$this->validate($source)."' and file_user = '".$_SESSION["SESSION_USER_IDENTIFIER"]."' and file_label = '".$this->validate($source)."' and file_creation_date = '$now' 
			and file_md5_tag = '".$file_tag."' ";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$identifier=-1;
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$identifier = $r["file_identifier"];
			}
			$root  = $this->check_parameters($this->parent->site_directories,"ROOT")."/";
			rename($path."/".$source, $root.$uploadsPath."/".$file_tag.$ext);
		}
		return $identifier;
	}
	/*************************************************************************************************************************
    * Retrieve list of files for content
    *************************************************************************************************************************/
	function retrieve_module_relationship($parameters){
		$module = $this->check_parameters($parameters,"module");
		$object = $this->check_parameters($parameters,"identifier");
		$sql = "select * from file_info inner join file_to_object on fto_file = file_identifier and fto_module='$module' and fto_client= file_client 
					where fto_object = $object and fto_client=$this->client_identifier order by fto_rank";
//print "<p>$sql</p>";
		$file_associations_identifiers	= "";
		$file_associations				= "";
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$file_associations_identifiers .="".$r["file_identifier"].",";
			$file_associations  .="<file_info logo='/libertas_images/icons/mime-images/".$this->call_command("FILES_GET_MIME_IMAGE",Array($r["file_name"])).".gif' identifier='".$r["file_identifier"]."' rank='".$this->check_parameters($r,"file_rank")."'><![CDATA[".$r["file_label"]."]]></file_info>";
        }
        $this->call_command("DB_FREE",Array($result));
		return Array("file_associations_identifiers"=>$file_associations_identifiers, "file_associations"	=>	$file_associations);
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function get_module_relationship($parameters){
		/*************************************************************************************************************************
        * variable definition
        *************************************************************************************************************************/
		$identifier = $this->check_parameters($parameters,"owner_id",-1);
		$module		= $this->check_parameters($parameters,"module",-1);
//		print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameters,true)."</p></li>";
		$file_list	= "";
		if($identifier==-1){
			return "";  // if -1 then we are adding an object so there are no files currently associated
		}
		/*************************************************************************************************************************
        * extract list of files attached from database associated with this object
        *************************************************************************************************************************/
		$sql = "select * from file_to_object 
					inner join file_info on fto_file = file_identifier and file_client = fto_client
				where fto_object=$identifier and fto_client=$this->client_identifier and fto_module='$module'";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){ 
			$file_list.="
				<file>
					<label><![CDATA[".$r["file_label"]."]]></label>
					<id><![CDATA[".$r["file_identifier"]."]]></id>
					<md5><![CDATA[".$r["file_md5_tag"]."]]></md5>
					<path><![CDATA[".$this->call_command("LAYOUT_GET_DIRECTORY_PATH", array($r["file_directory"]))."]]></path>
					<extension><![CDATA[".$this->file_extension($r["file_name"])."]]></extension>
				</file>";
		}
	    $this->call_command("DB_FREE",Array($result));
		/*************************************************************************************************************************
        * return list of files
        *************************************************************************************************************************/
		return $file_list;
	}
}
?>