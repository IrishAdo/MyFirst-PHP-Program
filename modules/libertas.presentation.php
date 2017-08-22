<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.presentation.php
* @date 20 Oct 2003
*/
/**
* this module will display pages that are published to a specific menu location it will handle 
* Archiving, Headlining, Go live dates, Remove Dates
*/
class presentation extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name_label			= "Page Manager Module (Presentation)";
	var $module_name				= "presentation";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:13 $';
	var $module_version 			= '$Revision: 1.27 $';
	var $modules 					= array(); 				// A list of all the modules in the system.
	var $module_creation 			= "20/10/2003";
	var $module_command				= "PRESENTATION_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_PRESENTATION";
	var $searched					= 0;
	var $module_admin				= "0";

	/**
	*  Management Menu entries
	*/
	var $module_admin_options 		= array();

	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	var $module_admin_user_access	= array();
	
	/**
	*  Channel options
	*/
	var $module_display_options 	= array(
		array("PRESENTATION_SEARCH",LOCALE_PAGE_DISPLAY_SEARCH_CHANNEL),
		array("PRESENTATION_DISPLAY",LOCALE_PAGE_DISPLAY_AUTO_CHANNEL),
		array("PRESENTATION_LATEST",LOCALE_PAGE_DISPLAY_NEWEST_CHANNEL)
	);
	
	/**
	*  filter options
	*/
	var $display_options			= array(
		array (0,FILTER_ORDER_DATE_NEWEST	,"trans_date_modified Desc"),
		array (1,FILTER_ORDER_DATE_OLDEST	,"trans_date_modified Asc"),
		array (2,FILTER_ORDER_TITLE_A_Z		,"trans_title Asc"),
		array (3,FILTER_ORDER_TITLE_Z_A		,"trans_title Desc")
	);

	var $WebObjects				 	= array(
//		array(2,"Display List of pages in this location","WEBOBJECTS_SHOW_LOCATION_PAGES",0,0)
	);
	
	var $author_access				= 0;
	var $approver_access			= 0;
	var $publisher_access			= 0;
	var $list_access				= 0;
	var $archiver_access			= 0;
	var $discussion_admin_access	= 0;
	var $force_unlock_access		= 0;
	var $approve_comments_access	= 0;
	/**
	*  Class Methods
	*/
	
	function command($user_command, $parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command, $parameter_list,__LINE__,"command"));
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
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->display("ENTRY",$parameter_list);
			}
			if ($user_command==$this->module_command."PERSISTANT"){
				return $this->display_persistant("ENTRY",$parameter_list);
			}
			if ($user_command==$this->module_command."SEARCH"){
				if ($this->searched==0){
					$this->searched=1;
					return $this->presentation_search($parameter_list);
				}
			}
			if ($user_command==$this->module_command."LATEST"){
				return $this->latest_pages($parameter_list);
			}
			if ($user_command==$this->module_command."ATOZ"){
				return $this->AtoZ($parameter_list);
			}
			if ($user_command==$this->module_command."GET_A2Z"){
				return $this->widget_atoz($parameter_list);
			}			
			if ($user_command==$this->module_command."ATOZ_ALL"){
				return $this->AtoZ_all($parameter_list);
			}
			if (($user_command==$this->module_command."SLIDESHOW") || ($user_command==$this->module_command."SLIDESHOW_TOPBOTTOM")){
				$parameter_list["cmd"] = $user_command;
				return $this->slideshow($parameter_list);
			}
			if ($user_command==$this->module_command."ATOZ_ADD"){
				return $this->call_command("PAGE_ADD_A2Z_ENTRIES",$parameter_list);
			}
			if ($user_command==$this->module_command."ATOZ_REMOVE"){
				return $this->call_command("PAGE_REMOVE_A2Z_ENTRIES",$parameter_list);
			}
			if ($user_command==$this->module_command."ATOZ_ALL_ADD"){
				return $this->call_command("PAGE_ADD_A2Z_ENTRIES",$parameter_list);
			}
			if ($user_command==$this->module_command."ATOZ_ALL_REMOVE"){
				return $this->call_command("PAGE_REMOVE_A2Z_ENTRIES",$parameter_list);
			}
			if ($user_command==$this->module_command."RESTORE"){
				return $this->call_command("PAGE_RESTORE",$parameter_list);
			}
			if ($user_command==$this->module_command."ARCHIVE"){
				return $this->show_archive($parameter_list);
			}
		}else{
			// wrong command sent to system
			return "";
		}
	}
	/**
	* This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	* module::initialise() function allowing you to define any extra constructor
	* functionality.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* define some variables 
		*/
		$this->presentation_size=$this->check_prefs(Array("sp_page_size"));
		$this->can_log_search = true;
		$this->module_constants["__REMOVE_ALL_HISTORY__"]=-2;
		$this->module_constants["__REMOVE__"]=-1;
		$this->module_constants["__REMOVE_REVERT__"]=-2;
		$sql = "select page_status_constant, page_status_identifier from page_status";

		/**
		* Added by zia to see sql 1
		*/
		if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}	
		
		
		$result = $this->call_command("DB_QUERY",Array($sql));
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$this->module_constants[$r["page_status_constant"]]=$r["page_status_identifier"];
			}
		}
//		$group_access = $this->check_parameters($_SESSION,"SESSION_GROUP_ACCESS");
		
		$rnotes = $this->check_prefs(Array("sp_can_save_notes","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No"));
		if ($rnotes=="YES"){
			$this->record_notes			= 1;
		} else {
			$this->record_notes			= 0;
		}
		return 1;
	}
	
	function display($type,$parameters){
		$debug = $this->debugit(false,$parameters);
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* get the current date and time 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$display_fields			 = $this->check_parameters($parameters, "display_fields", Array());
		$return_number			 = $this->check_parameters($display_fields, "return_number", -1);
		$current_menu_location	 = $this->check_parameters($parameters, "override_script", $this->parent->script);
		$display_fields_counted = count($display_fields);
		$headline_menus = Array();
		$now   			= $this->libertasGetDate("Y/m/d H:i:s");
		$nYear 			= $this->libertasGetDate("Y");
		$lang  			= "en";
		$join  			= "";
		$list_order_by	= "";
		$where 			= "";
		$headline_where = "";
		$return_the_first=-1;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[".print_r($parameters,true)."]"));
		}		
		if($return_number==-1){
			$headline 			=0; // 0 = no headline 1= headlines
			$headline_all		=0; // 0= defined list of menu locations, 1 = all children
			$headline_content	=0; // 0 = title only, 1 = title and summary
			$headline_count		=3; // number of items per menu location
			$headline_label		=0; // show ide labels
			$headline_titles	=0;
			$sql = "select menu_headline, menu_headline_all, menu_headline_content, menu_headline_counter, menu_headline_label, menu_headline_title_pages, menu_identifier, menu_archiving, menu_archive_on, menu_archive_display, menu_archive_access, menu_archive_label from menu_data where menu_client = $this->client_identifier and menu_url = '$current_menu_location'";

			/**
			* Added by zia to see sql 2
			*/
			if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}	
				
	        $result  = $this->call_command("DB_QUERY",Array($sql));
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$headline 			= $r["menu_headline"];
				$headline_all		= $r["menu_headline_all"];
				$headline_content	= $r["menu_headline_content"];
				$headline_count		= $r["menu_headline_counter"];
				$headline_label		= $r["menu_headline_label"];
				$headline_titles	= $r["menu_headline_title_pages"];
				$menu_identifier	= $r["menu_identifier"];
				$archive			= $r["menu_archiving"];
				if ($archive==1){
					$menu_archive_on			= $r["menu_archive_on"];
					$menu_archive_display		= $r["menu_archive_display"];
					$menu_archive_access		= $r["menu_archive_access"];
					$menu_archive_label			= $r["menu_archive_label"];
				} else {
					$menu_archive_on			= 0;
					$menu_archive_display		= 0;
					$menu_archive_access		= 0;
					$menu_archive_label			="";
				}
	        }
			if (strpos($this->parent->real_script,"index.php")==false){
				$headline 			= 0;
			}
		    $this->call_command("DB_FREE",Array($result));
		} else {
			$headline 			= 0;
			$archive 			= 0;
		}
    	if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp)){
				$grp_list ="";
				for($i=0,$m=count($grp);$i<$m;$i++){
					$grp_list .= $grp[$i]["IDENTIFIER"].", ";
				}
				$where .= "
					(
						(group_access_to_page.group_identifier is null or group_access_to_page.group_identifier in ($grp_list -1)) or 
						(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1))
					) and ";
				$headline_where .= "
					(
						(group_access_to_page.group_identifier is null or group_access_to_page.group_identifier in ($grp_list -1)) or 
						(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1))
					) and ";
			} else {
				$where .= " (group_access_to_page.group_identifier is null) and ";
				$headline_where .= " (group_access_to_page.group_identifier is null) and ";
			}
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier
					 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier ";
		}
	
		
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$load_notes = 0;
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if (isset($parameters["override_script"])){
			$where .= " menu_data.menu_url = '".$parameters["override_script"]."' and ";
			$where2 = " menu_data.menu_url = '".$parameters["override_script"]."' and ";
		}else{
			$where .= " menu_data.menu_url = '".$this->parent->script."' and ";
			$where2 = " menu_data.menu_url = '".$this->parent->script."' and ";
		}
		if (!empty($parameters["identifier"])){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- select the single document from the database.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$extra_fields ="";
			/*
			foreach($display_fields as $key=>$val){
				if ($key=='date'){
					$extra_fields .=", trans_date_available";
				}
				if ($key=='title'){
					$extra_fields .=", trans_title";
				}
				if ($key=='summary'){
					$extra_fields .=", trans_summary1";
				}
				if ($key=='url'){
					$extra_fields .=", menu_url";
				}
			}
			*/
			$sql = "
			select 
				distinct page_data.page_identifier, 
				page_data.page_web_discussion, 
				menu_access_to_page.page_rank, 
				menu_access_to_page.trans_identifier, 
				menu_access_to_page.title_page, 
				menu_data.*, 
				menu_access_to_page.menu_identifier,
				theme_type_label,
				theme_type_field_list
				$extra_fields, trans_date_publish, trans_date_available
			from page_data
				inner join page_trans_data on page_trans_data.trans_page = page_data.page_identifier
				inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
				inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier 
				inner join theme_types on theme_types.theme_type_identifier = menu_data.menu_stylesheet
				$join
			where 
				page_data.page_client=$this->client_identifier and 
				page_trans_data.trans_published_version=1 and 
				page_trans_data.trans_language ='en' and 
				$where 
				(page_trans_data.trans_date_available < '$now' or page_trans_data.trans_date_available = '0000/00/00 00:00:00') and 
				(page_trans_data.trans_date_remove > '$now' or page_trans_data.trans_date_remove = '0000/00/00 00:00:00') and 
				page_trans_data.trans_doc_status=".$this->module_constants["__STATUS_PUBLISHED__"]." and 
				page_trans_data.trans_page=".$parameters["identifier"]
				;
			$load_notes = 1;
			$display_one = 1;
//			print "1[ $sql ]";
		}else{
			$display_one = 0;
			
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- we have got to order these some how
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql = "
			select
				menu_sort_tag_value
			from menu_data 
				left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
			where
				$where2 menu_client = $this->client_identifier";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"$type :: [$sql]"));
			}
			if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}	
						
			$result = $this->call_command("DB_QUERY",Array($sql));
			if ($result){
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$list_order_by = "order by 
						 menu_access_to_page.title_page desc ,".$r["menu_sort_tag_value"].", page_data.page_web_discussion, menu_access_to_page.trans_identifier , page_data.page_identifier";
				}
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- select all the documents from the database for this location
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$display_fields_counted = count($display_fields);
			$extra_fields ="";
			/*
			foreach($display_fields as $key=>$val){
				if ($key=='date'){
					$extra_fields .=", trans_date_available";
				}
				if ($key=='title'){
					$extra_fields .=", trans_title";
				}
				if ($key=='summary'){
					$extra_fields .=", trans_summary";
				}
				if ($key=='url'){
					$extra_fields .=", menu_url";
				}
			}
			*/
			$sql = "
			select 
				distinct 
					page_data.page_identifier, 
					page_data.page_web_discussion,  
					menu_access_to_page.page_rank, 
					menu_access_to_page.trans_identifier, 
					menu_data.*, 
					menu_access_to_page.title_page,
					theme_type_label,
					theme_type_field_list,
					page_trans_data.trans_date_publish, trans_date_available
					$extra_fields
			from page_data
				inner join page_trans_data on page_trans_data.trans_page = page_identifier
				inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
				inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier 
				inner join theme_types on theme_types.theme_type_identifier = menu_stylesheet
				$join
			where 
				page_data.page_client=$this->client_identifier and 
				page_trans_data.trans_published_version=1 and 
				page_trans_data.trans_language ='en' and 
				page_trans_data.trans_doc_status=".$this->module_constants["__STATUS_PUBLISHED__"]." and 
				$where 
				(page_trans_data.trans_date_available < '$now' or page_trans_data.trans_date_available = '0000/00/00 00:00:00') and 
				(page_trans_data.trans_date_remove > '$now' or page_trans_data.trans_date_remove = '0000/00/00 00:00:00') 
				$list_order_by";
		}
		//if ($_SERVER['REMOTE_ADDR'] == "61.5.139.12")		
		//	print "<p>[".__LINE__."][ $sql ]</p>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"$type :: [$sql]"));
		}
		if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}	
		
		$result = $this->call_command("DB_QUERY",Array($sql));
		$page_documents	= Array();
		$page_ids		= Array();
		$page_comments	= Array();
		$years			= Array();
		$cy=-1; // current year
		$cm=-1; // current month
		$page_rank=Array();
		$found =-1;
		$page_com="";
		$page_doc="";
		$records_returned=$this->call_command("DB_NUM_ROWS",Array($result));

		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: ".dirname(__FILE__)));
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: ".print_r($this->parent->site_directories,true)));
		}
		$sz="";
		if ($records_returned==1){
			$load_notes=1;
		}
		if ($records_returned>0){
			if($archive==1){
				if ($menu_archive_display==1){
					$newerthan = mktime (0,0,0,date("m"),date("d"),  date("Y")-1); 
				} else if ($menu_archive_display==2){
					$newerthan = mktime (0,0,0,date("m")-1,date("d"),  date("Y")); 
				} else {
					$return_the_first = $menu_archive_display; // return x number of pages
				}
			}
			if ($return_the_first==-1){
				$return_the_first =$records_returned; // set to return all (defualt) archive setting overrides
			}
			$count=0;
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$count++;
//				print "<p>$count</p>";
				$y = date("Y",strtotime($r["trans_date_available"]));
				$m = date("M",strtotime($r["trans_date_available"]));
				if($cy==-1){
					$cy=$y;
					$cm=$m;
				}
			if($return_number==-1){
				if($r["title_page"]!=1){
					$showok=1;
					if($archive==1){
						if (($menu_archive_display==1)){
							
//							print strtotime($r["trans_date_available"])."<=$newerthan";
							if (strtotime($r["trans_date_available"])<=$newerthan){
								if(empty($years[$y])){
									$years[$y] = Array(
										"Jan"=>0,
										"Feb"=>0,
										"Mar"=>0,
										"Apr"=>0,
										"May"=>0,
										"Jun"=>0,
										"Jul"=>0,
										"Aug"=>0,
										"Sep"=>0,
										"Oct"=>0,
										"Nov"=>0,
										"Dec"=>0
									);									
								}
								$years[$y][$m]++;
								$showok=0;
							}
						} else {
							if(empty($years[$y])){
								$years[$y] = Array(
									"Jan"=>0,
									"Feb"=>0,
									"Mar"=>0,
									"Apr"=>0,
									"May"=>0,
									"Jun"=>0,
									"Jul"=>0,
									"Aug"=>0,
									"Sep"=>0,
									"Oct"=>0,
									"Nov"=>0,
									"Dec"=>0
								);
							}
							$years[$y][$m]++;							
							if($count >= $return_the_first){ 								
								$showok=0;
							}
						}
					}
				}
				if($r["title_page"]==1){
					$showok=1;
				}
				if ($showok==0){
					if ($menu_archive_display==1){
						if ($y==$cy){
							$showok=1;
						}
					} else if($menu_archive_display==2){
						if (($y<=$cy) && ($m=($cm-1))){
							$showok=1;
						}
					}
				}
			} else {
				$showok =0;
				if($count<=$return_number){
					$showok=1;
				}
			}
				//print "<li>count ".$showok ."  ".$r["title_page"]." [".count($page_documents)."]</li>";											
				if($archive==1){
					/* Comment and modify by Muhammad Imran Mirza*/
					//$max_num_of_pages_to_show = 10;
					$max_num_of_pages_to_show = 3000;
				}
				else{
					$max_num_of_pages_to_show = 3000;
				}
				if($showok==1){													
					if ($debug) print "records returned [$records_returned]";

					if ($this->check_parameters($r,"title_page",0) == 1 || $records_returned==1){						
						$i = count($page_documents);
						$fname=$data_files."/presentation_all_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: $fname"));
						}
						if ($debug) print "<p>$fname</p>";
						/**Display max of 10 */
						
						if (file_exists($fname) && $i<$max_num_of_pages_to_show){
							$t = strtotime($r["trans_date_publish"]);
							if ($t > $this->parent->updated_date){
								$this->parent->updated_date = $t;
							}
							if ($debug) print "<p>found</p>";
							$found=$i;

							$page_ids[count($page_ids)]	= $r["trans_identifier"];
							$page_documents[$i] = $fname;							
							$cfname = $data_files."/comments_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
							//print $cfname;
							//print "[$load_notes]";							
							if (($load_notes==1) && ($r["page_web_discussion"]==1 && file_exists($cfname))){
								$page_comments[$i] = $cfname;
							} else {
								$page_comments[$i] = "";
							}
						}
						$page_rank[$i] = $r["page_rank"];
					} else {
						if ($display_fields_counted==0){
							$theme_type_field_list = $r["theme_type_field_list"];
						} else {
							$theme_type_field_list = "summary";
						}
						$i = count($page_documents);
						$fname = $data_files."/presentation_".$theme_type_field_list."_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: $fname"));
						}
						/**Display max of 10 */
						if (file_exists($fname) && $i<$max_num_of_pages_to_show){
							$t = strtotime($r["trans_date_publish"]);
							if ($t > $this->parent->updated_date){
								$this->parent->updated_date = $t;
							}
							$page_ids[count($page_ids)]	= $r["trans_identifier"];
							$page_documents[$i] = $fname;
							$page_comments[$i] = "";
							$page_rank[$i] = $r["page_rank"];
						} else {
							$page_comments[$i] = "";
						}
					}
				}
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
/*
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            - get headline locations 
            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
            */
//			print "[<strong>$headline,$headline_all</strong>]";
			if($headline==1){
				$menu_list = Array();
				if($headline_all==1){
					// extract from defined list of children
					$sql = "select * from menu_to_object 
						inner join menu_data on mto_menu = menu_identifier and mto_client=menu_client
						left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
					where mto_client = $this->client_identifier and mto_object = $menu_identifier and mto_module='LAYOUT_' order by menu_order";
					/**
					* Added by zia to see sql 5
					*/
					if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
					}	
					
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$c=0;
                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	                   	$menu_list[count($menu_list)] = Array($r["mto_menu"], $r["menu_sort_tag_value"]);
	                }
                    $this->call_command("DB_FREE",Array($result));
				}else{
					// extract from all children
					$sql = "select * from menu_data 
								left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
							where menu_client = $this->client_identifier and (menu_parent = $menu_identifier or menu_identifier = $menu_identifier) order by menu_order";

					/**
					* Added by zia to see sql 6
					*/
					if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
					}	
										
					$result  = $this->call_command("DB_QUERY",Array($sql));
					$c=0;
					$menu_url ="";
                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
						if($r["menu_identifier"] !=$menu_identifier ){
                    		$menu_list[count($menu_list)] = Array($r["menu_identifier"], $r["menu_sort_tag_value"]);
							$c++;
						} else {
							$menu_url = $r["menu_url"];
						}
                    }
                    $this->call_command("DB_FREE",Array($result));
					if ($c==0 && $menu_url=="index.php"){
						$sql = "Select * from menu_data 
									left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
								where menu_client=$this->client_identifier and menu_parent=-1 and menu_url!='index.php' and menu_url!='admin/index.php' order by menu_order";
						/**
						* Added by zia to see sql 7
						*/
						if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
						}	
						
						$result  = $this->call_command("DB_QUERY",Array($sql));
						$c=0;
						$menu_url ="";
	                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	                    		$menu_list[count($menu_list)] = Array($r["menu_identifier"], $r["menu_sort_tag_value"]);
	                    }
	                    $this->call_command("DB_FREE",Array($result));
					}
				}
				$m = count($menu_list);
//				print "<li>$sql</li>";
//				print_r($menu_list);
				for($i=0;$i<$m;$i++){
					$list_order_by = $menu_list[$i][1];
					$sql = "
					select 
						distinct 
							page_data.page_identifier, 
							page_data.page_web_discussion,  
							menu_access_to_page.page_rank, 
							menu_access_to_page.trans_identifier, 
							menu_data.*, 
							menu_access_to_page.title_page,
							theme_type_label,
							theme_type_field_list,
							page_trans_data.trans_date_publish,
							title_page
							$extra_fields
					from page_data
						inner join page_trans_data on trans_page = page_identifier
						inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
						inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier 
						inner join theme_types on theme_type_identifier = menu_stylesheet
						$join
					where 
						$headline_where 
						menu_data.menu_identifier = ".$menu_list[$i][0]." and 
						menu_data.menu_url not in ('index.php','admin/index.php') and
						(trans_date_available < '$now' or trans_date_available = '0000/00/00 00:00:00') and 
						(trans_date_remove > '$now' or trans_date_remove = '0000/00/00 00:00:00') and 
						page_trans_data.trans_doc_status=".$this->module_constants["__STATUS_PUBLISHED__"]." and 
						page_data.page_client=$this->client_identifier and 
						page_trans_data.trans_language ='en' and 
						page_trans_data.trans_published_version=1 ";
					if($headline_titles==0){
						$sql .=	"and title_page=0 ";
					}
					$sql .=	"order by $list_order_by";
					//print "<li>$sql</li>";
					/**
					* Added by zia to see sql 8
					*/
					if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
					}	
					
		            $result  = $this->call_command("DB_QUERY",Array($sql));
					$pos = count($headline_menus);
					$c=0;
					while(($r = $this->call_command("DB_FETCH_ARRAY",Array($result))) && ($c<$headline_count)){
						if ($c==0){
							$headline_menus[$pos] = Array("label"=> $r["menu_label"],"list"=>Array(),"pages"=>Array(),"files"=>Array(),"uri"=>$r["menu_url"], "title_page" => Array());
						}
						$c++;
						if (($headline_content==1) || ($headline_content==4) || ($headline_content==5)){
							$fname=$data_files."/presentation_summary_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
						} else {
							$fname=$data_files."/presentation_title_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
						}
						if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: $fname"));}
//						print "<li>$fname</li>";
						if (file_exists($fname)){
							$t = strtotime($r["trans_date_publish"]);
							if ($t > $this->parent->updated_date){
								$this->parent->updated_date = $t;
							}
							$headline_menus[$pos]["list"][count($headline_menus[$pos]["list"])] = $r["trans_identifier"];
							$headline_menus[$pos]["pages"][count($headline_menus[$pos]["pages"])] = $r["page_identifier"];
							$headline_menus[$pos]["files"][count($headline_menus[$pos]["files"])] = $fname;
							$headline_menus[$pos]["title_page"][count($headline_menus[$pos]["title_page"])] = $r["title_page"];
						}
                    }
                    $this->call_command("DB_FREE",Array($result));
				}
			}
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"variables :: display_one [$display_one] Found[$found] docs:: ".print_r($page_documents,true)));}
			if ($found!=-1){
				$page_doc=$page_documents[$found];
				$page_com=$page_comments[$found];
			}else{
				$page_doc="";
			}
		$page = $this->check_parameters($parameters,"display_page",-1);
		$out = "<uid>".md5(uniqid(rand(), true))."</uid>";
		$out .= "<module name=\"".$this->module_name."\" display=\"$type\"><time><![CDATA[".$this->libertasGetTime()."]]></time>";
		
		if($archive==1){
			$out .="<page_archive>";
			$root = $this->parent->site_directories["ROOT"];
			$path = $root."/".dirname($this->parent->script);
			//if ($_SERVER['REMOTE_ADDR'] == "61.5.139.12")
			//	print "<li>".$menu_archive_display."</li>";
			krsort($years);
			/** If single page to be displayed then get the archive years */
			if (!empty($parameters["identifier"])){
				$d = dir($path); 
				while (false !== ($entry = $d->read())) { 
					if ((substr($entry,-4)==".php") && (intval(substr($entry,1,4))>(intval(date("Y"))-10))){
						$y=intval(substr($entry,1,4));
						$years[$y]++;
					}
				} 
				$d->close();				
			}
			if ($menu_archive_display==1){
				//$out.="<year id='$nYear' link='index.php'/>";
				foreach($years as $myYear => $val){
					$out.="<year id='$myYear'/>";
					if(!file_exists($path."/-$myYear.php")){
						$this->makeSpecial("PRESENTATION_ARCHIVE", $this->parent->script, $path."/-$myYear.php",Array("archive_filter" 	=> "Year","archive_value"		=> "$myYear"),"Archive content for $myYear");
					}
				}
			} else if ($menu_archive_display==2 || $menu_archive_display==6){
				foreach($years as $myYear => $val){
					$out.="<year id='$myYear'>";
					if(!file_exists($path."/-$myYear.php")){
						$this->makeSpecial("PRESENTATION_ARCHIVE", $this->parent->script, $path."/-$myYear.php",Array("archive_filter" 	=> "Year|Month","archive_value"		=> "$myYear"),"Archive content for $myYear");
					}
					if (is_array($years[$myYear])) {
						foreach($years[$myYear] as $month => $val){
							if($val>0){
								$out.="<month id='$month'/>";
								if(!file_exists($path."/-$myYear-$month.php")){
									$this->makeSpecial("PRESENTATION_ARCHIVE", $this->parent->script, $path."/-$myYear-$month.php",Array("archive_filter" 	=> "Year|Month","archive_value"	=> "$myYear:$month"),"Archive content for $myYear / $month");
								}
							}
						}
					}	
					$out.="</year>";
				}
			} else {
				$out.="<link url='-archive.php'><![CDATA[$menu_archive_label]]></link>";
				if(!file_exists($path."/-archive.php")){
					$this->makeSpecial("PRESENTATION_ARCHIVE", $this->parent->script, $path."/-archive.php",Array("archive_filter" 	=> "Page","archive_value"	=> "1"),"Archive");
				}
			}
			$out .="</page_archive>";
		}
		$c=0;
		//print "<p>length_of_entities [".count($page_documents)."]</p>";
		if ($page==-1){
			if ($debug) print "$page_doc";
			if ($page_doc!=""){
				$c++;
				$fp		 = fopen($page_doc, "rb");
				if ($fp){
					while (!feof($fp)){
						$out .= fread($fp, 4096);
					}
				}
				fclose($fp);
				if ($page_com!=""){
					$fp		 = fopen($page_com, "rb");
					if ($fp){
						while (!feof($fp)){
							$out .= fread($fp, 4096);
						}
					}
					fclose($fp);
				}
			}
			$length_of_entities = count($page_documents);
			if ($debug) print "<p>length_of_entities [$length_of_entities]</p>";
			for ($index	 = 0; $index < $length_of_entities; $index++){
				 if ($index!=$found){
					$c++;
					$fp		 = fopen($page_documents[$index], "rb");
					if ($fp){
						while (!feof($fp)){
							$out .= fread($fp, 4096);
						}
					}
					fclose($fp);
					if (!empty($page_comments[$index])){
						$fp		 = fopen($page_comments[$index], "rb");
						if ($fp){
							while (!feof($fp)){
								$out .= fread($fp, 4096);
							}
						}
						fclose($fp);
					}
				}
			}
		}else {
			$counter=0;
			if ($page==0){
				$c++;
				$fp	 = fopen($page_documents[$found], "rb");
				if ($fp){
					while (!feof($fp)){
						$out .= fread($fp, 4096);
					}
				}
				fclose($fp);
				if (!empty($page_comments[$found])){
					$fp		 = fopen($page_comments[$found], "rb");
					if ($fp){
						while (!feof($fp)){
							$out .= fread($fp, 4096);
						}
					}
					fclose($fp);
				}
				for ($index	 = 0, $length_of_entities = count($page_documents); $index < ($length_of_entities -1); $index++){
					$out .= "<page/>";
				}
			} else {
				$count=-1;
				for ($index	 = 0, $length_of_entities = count($page_documents); $index < $length_of_entities; $index++){
					 if ($index==$page-1) {
						$c++;
						$count++;
						$fp	 = fopen($page_documents[$count], "rb");
						if ($fp){
							while (!feof($fp)){
								$out .= fread($fp, 4096);
							}
						}
						fclose($fp);
						if (!empty($page_comments[$count])){
							$fp		 = fopen($page_comments[$count], "rb");
							if ($fp){
								while (!feof($fp)){
									$out .= fread($fp, 4096);
								}
							}
							fclose($fp);
						}
					} else {
						$out .= "<page/>";
						$count++;
						$c++;
					}
				}
			}
		}
		/**
		* if there are any headlines then display them
	*/ 
//		print "hkj";
//		print_r($headline_menus);
		$m = count($headline_menus);
		for($i = 0; $i<$m ;$i++){
			$sz.="<headline content='$headline_content' > ";
				$sz.="<uri><![CDATA[".$headline_menus[$i]["uri"]."]]></uri>";
			if (($headline_content==2) || ($headline_content==4)){
				$sz .= "<cols>2</cols>";
			} else if (($headline_content==3) || ($headline_content==5)){
				$sz .= "<cols>3</cols>";
			} else {
				$sz .= "<cols>1</cols>";
			}
			if($headline_label==1){
				$sz.="<label><![CDATA[".$headline_menus[$i]["label"]."]]></label>";
			}
			$list_of_headlines = count($headline_menus[$i]["files"]);
			for ($index	 = 0; $index < $list_of_headlines; $index++){
				$c++;				
				$fp		 = fopen($headline_menus[$i]["files"][$index], "rb");
				if ($fp){
					while (!feof($fp)){
						if ($headline_menus[$i]["title_page"][$index]==1){
							$sz.="<title_page identifier='".$headline_menus[$i]["list"][$index]."'/>";
						}
						$sz .= fread($fp, 4096);
					}
				}
				fclose($fp);
			}
			$sz.="</headline>";
		}
		$out .= "$sz</module>";
			$out.= $this->call_command("SFORM_LOAD_CACHE",Array("list_of_trans"=>$page_ids));
			return $out;
	}

	function presentation_search($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Page search",__LINE__,print_r($parameters,true)));
		}
		$has_file_module=1;
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* get the current date and time 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd1",
				"field_as"			=> "trans_body1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "body",
				"join_type"			=> "inner"
			)
		);
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary",
				"join_type"			=> "inner"
			)
		);

		$page_boolean	= $this->check_parameters($parameters,"page_boolean","exact");
		$page_author	= $this->check_locale_starter($this->check_parameters($parameters,"page_author",""));
		$menu_location = $this->check_parameters($parameters,"menu_location","");
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		$where = "";
		$join="";
		$order_by="";
		$status =array();
		$words =array();
		$variables=array();
		$search=0;
		$where_title 	= "";
		$where_body 	= "";
		$where_summary 	= "";
		$where_author 	= "";
		$where_file		= "";
		$where_file_name= "";
		if ($page_author!=""){
			$where_author = " and trans_dc_creator like '%$page_author%' ";
			
		}
		if (isset($parameters["page_search"])){
			if (strlen($parameters["page_search"])>0){
				$search=1;
				$words = split(" ",$parameters["page_search"]);
				if ($page_boolean=='exact'){
						$where_title 		.= " page_trans_data.trans_title like '%".$parameters["page_search"]."%'";
						$where_body 		.= " ".$body_parts["where_field"]." like '%".$parameters["page_search"]."%'";
						$where_summary 		.= " ".$summary_parts["where_field"]." like '%".$parameters["page_search"]."%'";
						if ($has_file_module){
							$where_file		.= "file_label like '%".$parameters["page_search"]."%'";
							$where_file_name.= "file_name like '%".$parameters["page_search"]."%'";
						}
		
				}else{
					for($index=0,$len=count($words);$index<$len;$index++){
						if ($index>0){
							$where_title .= " $page_boolean";
							$where_body .= " $page_boolean";
							$where_summary .= " $page_boolean";
							if ($has_file_module){
								$where_file .= " $page_boolean";
								$where_file_name .= " $page_boolean";
							}
						}
						$where_title 		.= " page_trans_data.trans_title like '%".$words[$index]."%'";
						$where_body 		.= " ".$body_parts["where_field"]." like '%".$words[$index]."%'";
						$where_summary 		.= " ".$summary_parts["where_field"]." like '%".$words[$index]."%'";
						if ($has_file_module){
							$where_file	.= " file_label like '%".$parameters["page_search"]."%'";
							$where_file_name	.= " file_name like '%".$parameters["page_search"]."%'";
						}
					}
				}
				$where .= " and (($where_title) or ($where_body) or ($where_summary) or($where_file) or ($where_file_name)) $where_author";
				$this->update_search_keys($parameters["page_search"]);
			}
		}
		$where .= " and
					(page_trans_data.trans_date_available < '$now' or page_trans_data.trans_date_available = '0000/00/00 00:00:00') and 
				(page_trans_data.trans_date_remove > '$now' or page_trans_data.trans_date_remove = '0000/00/00 00:00:00') 
		";
		
		if ((isset($parameters["menu_location"])) && ($parameters["menu_location"]!='-1')){
			$search=1;
			$menu_list = $parameters["menu_location"] . $this->call_command("LAYOUT_DISPLAY_IDS", Array($parameters["menu_location"]));
//			$where .= " and menu_access_to_page.menu_identifier = '".$parameters["menu_location"]."'";
		} else{
			$menu_list = $this->call_command("LAYOUT_DISPLAY_IDS");
		}
		if (strlen($menu_list)>0){
			$where .= " and menu_access_to_page.menu_identifier in ($menu_list)";
		}
		if ($this->call_command("ENGINE_HAS_MODULE",array("LAYOUT_"))==1){
			$join.=" inner join menu_access_to_page on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier";
		}
		if ($has_file_module){
			$join.=" left outer join file_access_to_page on page_trans_data.trans_identifier = file_access_to_page.trans_identifier";
			$join.=" left outer join file_info on file_info.file_identifier = file_access_to_page.file_identifier";
		}
		if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp)){
				$grp_list ="";
				for($i=0,$m=count($grp);$i<$m;$i++){
					$grp_list .= $grp[$i]["IDENTIFIER"].", ";
				}
				$where .= " and (group_identifier is null or group_identifier in ($grp_list -1))";
			} else {
				$where .= " and (group_identifier is null)";
			}
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier";
			
		}
		
		if (empty($parameters["order_filter"])){
			$parameters["order_filter"]=0;
		}
		$order_by .= "order by ".$this->display_options[$parameters["order_filter"]][2];
		$sql = "select DISTINCT 
					trans_page, page_rank
				from page_trans_data 
					inner join page_status on page_status.page_status_identifier = page_trans_data.trans_doc_status 
					$join
					".$body_parts["join"]."
					".$summary_parts["join"]."
				where 
					page_trans_data.trans_client=$this->client_identifier and 
					page_trans_data.trans_doc_status = 4 and 
					page_trans_data.trans_published_version = 1 
					$where 
					".$body_parts["where"]."
					".$summary_parts["where"]."
				group by trans_page, page_rank";

			$counter_sql = "select 
					count(*) as total
				from page_trans_data 
					inner join page_status on page_status.page_status_identifier = page_trans_data.trans_doc_status 
					$join
					".$body_parts["join"]."
					".$summary_parts["join"]."
				where 
					page_trans_data.trans_client=$this->client_identifier and 
					page_trans_data.trans_doc_status = 4 and 
					page_trans_data.trans_published_version = 1 
					$where 
					".$body_parts["where"]."
					".$summary_parts["where"]."
					order by page_trans_data.trans_date_modified Desc";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"$counter_sql"));
		}
		$variables["FILTER"]			= $this->filter($parameters,"PRESENTATION_SEARCH");
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		$variables["FILE"]				= "summary";
		if (isset($parameters["page_search"])){
			$variables["PRESENTATION_SEARCH"]	= $parameters["page_search"];
		}else {
			$variables["PRESENTATION_SEARCH"]	= "";
		}
		
		if ($this->module_admin_access==1 || $search==1){
			/**
			* Added by zia to see sql 9
			*/
			if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}	
			
			$counter_result = $this->call_command("DB_QUERY",array($counter_sql));
			
			if (!$counter_result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
                while($r = $this->call_command("DB_FETCH_ARRAY",Array($counter_result))){
					$number_of_records = $r["total"];
                }
                $this->call_command("DB_FREE",Array($counter_result));
				//$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
				$page = $this->check_parameters($parameters,"page",1);
				$goto = ((--$page)*$this->page_size);
				if ($goto<=0){
				$goto=0;
				}
				if (($goto!=0)&&($number_of_records>$goto)){
//					$pointer = $this->call_command("DB_SEEK",array($result,$goto));
//					$sql .= " limit $goto,$this->page_size ";
					$limit = Array("action"=>"LIMIT","seek"=>$goto,"num_results"=>$this->page_size); 
				} else {
//					$sql .= " limit $this->page_size ";
					$limit = Array("action"=>"LIMIT","seek"=>0,"num_results"=>$this->page_size); 
				}
//				if (($goto!=0)&&($number_of_records>$goto)){
//					$pointer = $this->call_command("DB_SEEK",array($result,$goto));
//				}
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"$sql"));
				}
				/**
				* Added by zia to see sql 10
				*/
				if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
				}	
				
				$result = $this->call_command("DB_QUERY",array($sql,$limit));
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
				
				$variables["ENTRY_BUTTONS"] =Array();
				$variables["RESULT_ENTRIES"] =Array();
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<10)){
					$counter++;
					$i = count($variables["RESULT_ENTRIES"]);
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=> $r["trans_page"],
						"rank"			=> $r["page_rank"]
					);
//					print $r["trans_page"]."<br>";
				}
			}
		}
		$out = $this->generate_search($variables);
		
		return $out;
	}
	
	function filter($parameters,$cmd){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_filter",__LINE__,"[]"));
		}
		$advanced 		= $this->XSSbasicClean($this->check_parameters($parameters,"advanced",0));
		$page_author	= $this->XSSbasicClean($this->check_parameters($parameters,"page_author",""));
		$page_boolean	= $this->XSSbasicClean($this->check_parameters($parameters,"page_boolean","exact"));
		$search 		= $this->XSSbasicClean($this->check_parameters($parameters,"search",-1));
		$page_search 	= $this->XSSbasicClean($this->check_parameters($parameters,"page_search"));
		$group_filter 	= $this->check_parameters($parameters,"group_filter",-1);
		$menu_location 	= $this->check_parameters($parameters,"menu_location",-1);
		$order_filter 	= $this->check_parameters($parameters,"order_filter",-1);
		$status_filter 	= $this->check_parameters($parameters,"status_filter",-1);
//		$page_author	= $this->check_parameters($parameters,"page_author","");
		$group_list 	= $this->call_command("GROUP_RETRIEVE",array($this->check_parameters($parameters,"group_filter")));
		$label 			= $this->check_parameters($parameters,"label",FILTER_RESULTS);
		$search++;
		if ($advanced==1){
			$name = "filter_form_advanced";
		} else {
			$name = "filter_form_basic";
		}
		$out = "\t\t\t\t<form name=\"$name\" label=\"".$label."\" method=\"get\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\"><![CDATA[$cmd]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"advanced\"><![CDATA[$advanced]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"associated_list\" value=\"".$this->XSSbasicClean($this->check_parameters($parameters,"associated_list"))."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" ><![CDATA[1]]></input>\n";
		$str = join("&#39;",split("\\\'",htmlspecialchars($page_search)));
		$page_search = join("&#34;",split("\\\&quot;",$str));
//		$page_search = $this->convert_amps($page_search);
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"search\" ><![CDATA[$search]]></input>\n";
		
		$out .= "\t\t\t\t\t<input type=\"text\" adv=\"0\" name=\"page_search\" label=\"".SEARCH_KEYWORDS."\" ><![CDATA[$page_search]]></input>\n";
		if ($this->module_admin_access==1){
			$out .= "\t\t\t\t\t<select name=\"order_filter\" label=\"".ENTRY_ORDER_FILTER."\">\n";
			for ($index=0,$max=count($this->display_options);$index<$max;$index++){
				$out .="\t\t\t\t\t\t<option value=\"".$this->display_options[$index][0]."\"";
				if ($order_filter==$this->display_options[$index][0]){
					$out .=" selected=\"true\"";
				}
				$out .=">".$this->display_options[$index][1]."</option>\n";
			}
			$out .= "\t\t\t\t\t</select>\n";
				$out .= "\t\t\t\t\t<select name=\"status_filter\" label=\"".ENTRY_STATUS_FILTER."\"><option value=\"-1\">".OPTION_DISPLAY_ALL."</option>\n";
				$out .= $this->get_status($status_filter);
				$out .= "\t\t\t\t\t</select>\n";
		}
		if ($advanced==1){
			$out .= "\t\t\t\t\t<input type=\"text\" name=\"page_author\" label=\"".LOCALE_AUTHOR."\" ><![CDATA[$page_author]]></input>\n";
			$menu = $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($menu_location));
			$out .="\t\t\t\t\t<select label=\"".ENTRY_MENU_LOCATION."\" name=\"menu_location\"><option value=\"-1\">".LOCALE_OPTION_DISPLAY_ALL_LOCATIONS."</option>$menu</select>";
			$out .= "\t\t\t\t\t<select name=\"page_boolean\" label=\"".LOCALE_SEARCH_BOOLEAN."\">";
			if ($page_boolean=='or'){
				$or_selected ="selected='true'";
				$and_selected ="";
				$exact_selected="";
			}else if ($page_boolean=='and'){
				$or_selected ="";
				$and_selected ="selected='true'";
				$exact_selected="";
			}else{
				$or_selected ="";
				$and_selected ="";
				$exact_selected="selected='true'";
			}
			$out .= "<option value='or' $or_selected>Any keyword</option>";
			$out .= "<option value='and' $and_selected>All keywords</option>";
			$out .= "<option value='exact' $exact_selected>Exact Phrase</option>";
			$out .= "</select>\n";
		
		} else {
			$out .= "\t\t\t\t\t<input type=\"hidden\" name='menu_location' value='$menu_location'/>";
			$out .= "\t\t\t\t\t<input type=\"hidden\" name='page_boolean' value='exact' />";
		}
		if (($this->parent->server[LICENCE_TYPE]==ECMS) && false){
			if ($this->module_admin_access==0){
				if ($advanced==0){
					$out .= "\t\t\t\t\t<input type=\"button\" iconify=\"ADVANCED\" name=\"switch_to_advanced\" command=\"PRESENTATION_SEARCH&amp;advanced=1\"/>\n";
				}else{
					$out .= "\t\t\t\t\t<input type=\"button\" iconify=\"BASIC\" name=\"switch_to_normal\" command=\"PRESENTATION_SEARCH&amp;advanced=0\"/>\n";
				}
			}
		}
		$out .= "\t\t\t\t\t<input type=\"submit\" iconify=\"SEARCH\" name=\"\" value=\"".FILTER_RESULTS."\"/>\n";
		$out .= "\t\t\t\t</form>";
		return $out;
	}
	
	
	/**
	*  latest pages does not order by rank it orders by date modified.
	*/
		
	function latest_pages($parameters){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* get the current date and time 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$menu_loc_id=-1;
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"latest_pages",__LINE__,"[".print_r($parameters,true)."]"));
		}
		$group = "";
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		if ($max_grps==0){
			$group = "((group_access_to_page.group_identifier is null ) and (relate_menu_groups.group_identifier is null))";
		}else if ($max_grps==1){
			$group = "((group_access_to_page.group_identifier is null or group_access_to_page.group_identifier = ".$grp_info[0]["IDENTIFIER"]." ) and (relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier = ".$grp_info[0]["IDENTIFIER"]."))";
		}else{
			$group_in = " in (";
			for($i=0;$i < $max_grps; $i++){
				if ($i>0){
					$group_in .= ",";
				}
				$group_in .= $grp_info[$i]["IDENTIFIER"];
			}
			$group_in .= ")";
			$group = "((group_access_to_page.group_identifier is null or group_access_to_page.group_identifier $group_in ) and (relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier $group_in))";
		}

		$where ="";
		$join ="";
		if ($this->parent->script=="/index.php"){
			$menu_list 	= "";
		} else {
			$menu_loc_id= $this->call_command("LAYOUT_GET_LOCATION_ID");
			$menu_list 	= "$menu_loc_id, ".$this->call_command("LAYOUT_DISPLAY_CHILD_IDS",Array("parent" => $menu_loc_id));
		}
		if (strlen($menu_list)>0){
			$where = " and menu_access_to_page.menu_identifier in ($menu_list , -1)";
//			$join =" inner join menu_access_to_page on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier";
		}

		$sql = "select distinct trans_page, trans_date_available
				from page_trans_data 
					inner join menu_access_to_page on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier 
					$join
					left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_access_to_page.menu_identifier 
					left outer join group_access_to_page on group_access_to_page.trans_identifier = page_trans_data.trans_identifier 
				where 
					page_trans_data.trans_client=$this->client_identifier and 
					page_trans_data.trans_doc_status = 4 and page_trans_data.trans_published_version=1 and 
					(page_trans_data.trans_date_available < cast('$now' as DATETIME) or page_trans_data.trans_date_available = cast('0000/00/00 00:00:00' as DATETIME)) and 
					(page_trans_data.trans_date_remove > cast('$now' as DATETIME) or page_trans_data.trans_date_remove = cast('0000/00/00 00:00:00' as DATETIME)) and 
					$group
					$where
		 		group by trans_page,trans_date_available
 				order by 
					trans_date_available desc, trans_page";
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"latest_pages",__LINE__," :: [$sql]"));
		}
		$out  = "<module name=\"".$this->module_name."\" display=\"LATEST\" command=\"PRESENTATION_DISPLAY\" call=\"PRESENTATION_LIST\"><label><![CDATA[Latest pages]]></label>";
		$lang = "en";
		/**
		* Added by zia to see sql 11
		*/
		if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}	
		
		$result = $this->call_command("DB_QUERY",Array($sql));
		$PAGE_documents= Array();
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($result) {
			$c=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",Array($result))) && ($c<10)){
				$id			= $r["trans_page"];
				$filename = $data_files."/presentation_summary_".$this->client_identifier."_".$lang."_$id.xml";
				if ($this->module_debug ){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"file",__LINE__," :: [$filename]"));
				}
				if (file_exists($filename)){
					$fp = fopen($filename, 'rb');
					$out .= fread($fp, filesize($filename));
					fclose($fp);
					$c++;
				}
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
		$out .= "</module>";
		return $out;
	}
	
	function translate_to_filename($url,$title,$id,$menu_id){
		$root				= $this->parent->site_directories["ROOT"];
		$dir 				= dirname($root."/".$url);
		$filename		 	= $this->make_uri($title).".php";
		$directories 		= split('/',$url);
		$directorycount		= count($directories)-1;
		$directory_to_root	= "";
		
		if ($directorycount>0){
 			for($index=0;$index<$directorycount;$index++){
				$directory_to_root .= "../";
			}
		}
		$um = umask(0);
		@chmod($dir, LS__DIR_PERMISSION);
		umask($um);
		$fp = fopen($dir."/".$filename, 'w');
		$module_directory = $this->check_parameters($this->parent->site_directories,"MODULE_DIR",$directory_to_root);
		fwrite($fp, "<"."?php\r\n\$identifier=$id;\n\$menu_identifier=$menu_id;\r\n\$script=\"$url\";\r\n\$command=\"PRESENATATION_DISPLAY\";\r\nrequire_once \"".$root."/admin/include.php\"; \r\nrequire_once \"".$module_directory."/included_page.php\"; \r\n?".">");
		fclose($fp);
		$um = umask(0);
		@chmod($dir."/".$filename, LS__FILE_PERMISSION);
		umask($um);
		$pos =strlen($root);
		if ($pos+1 < strlen($dir)){
			$pos++;
		}
		$url = substr($dir,$pos);
		if ($url==""){
			return $filename;
		}else{
			return $url."/".$filename;
		}
	}
	

	function get_page($parameters){
		$menu_identifier = $this->check_parameters($parameters,"menu_identifier");
		$sql = "select * from menu_access_to_page where menu_identifier = $menu_identifier and client_identifier = $this->client_identifier";
		/**
		* Added by zia to see sql 12
		*/
		if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}	
		
		$result = $this->call_command("DB_QUERY",Array($sql));
		$identifier=-1;
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$identifier =$r["trans_identifier"];
		}
		return $identifier;
	}
	
	function PAGE_list_detail($parameters){
		$list_of_pages = $this->check_parameters($parameters,"PAGE_associations","__NOT_FOUND__");
		$out="";
		if ($list_of_pages!="__NOT_FOUND__"){
			$sql = "select distinct
						page_trans_data.trans_page, 
						page_trans_data.trans_title, 
						menu_access_to_page.menu_identifier 
					from 
						page_trans_data 
					inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
					where 
						trans_client = $this->client_identifier and 
						page_trans_data.trans_page in ($list_of_pages -1) and 
						page_trans_data.trans_published_version=1";
			/**
			* Added by zia to see sql 13
			*/
			if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
			}	
			
			$result = $this->call_command("DB_QUERY",array($sql));
			$out="<module name='page' display='associated_pages'>";
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$out.="<associate page='".$r["trans_page"]."' location='".$r["menu_identifier"]."'><![CDATA[".$r["trans_title"]."]]></associate>";
			}
			$out.="</module>";
			$out.=$this->call_command("LAYOUT_WEB_MENU");
		}
		return $out;
	}

	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: AtoZ_all()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function simply calls the AtoZ function and sets a parameter that will tell the function to present 
	- all of the pages.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/	
	function AtoZ_all($parameters){	
		$parameters["choosenconfig"] = "ALL";
		return $this->AtoZ($parameters);
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: AtoZ()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- function to give A to Z functionality to the system
	-
	- the AtoZ function requires the PRESENTATION_ATOZ channel this channel is used in replace of
	- PRESENTATION_DISPLAY or PRESENTATION_SLIDESHOW see table theme_types for list of these channels
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/	
	function AtoZ($parameters){
		$debug = $this->debugit(false,$parameters);
		$choosenletter	= strtoupper($this->check_parameters($parameters,"letter"));
		$choosenconfig	= $this->check_parameters($parameters,"choosenconfig");
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		$display_fields = $this->check_parameters($parameters,"display_fields",Array());
		$now   			= $this->libertasGetDate("Y/m/d H:i:s");
		$join  			= "";
		$list_order_by	= "";
		$where 			= "";
		$page_ids		= Array();
		if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp)){
				$grp_list ="";
				for($i=0,$m=count($grp);$i<$m;$i++){
					$grp_list .= $grp[$i]["IDENTIFIER"].", ";
				}
				$where .= "
					(
						(group_access_to_page.group_identifier is null or group_access_to_page.group_identifier in ($grp_list -1)) or 
						(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1))
					) and ";
			} else {
				$where .= " (group_access_to_page.group_identifier is null) and ";
			}
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier
					 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier ";
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if (isset($parameters["override_script"])){
			$where .= " menu_data.menu_url = '".$parameters["override_script"]."' and ";
		}else{
			$where .= " menu_data.menu_url = '".$this->parent->script."' and ";
		}
			
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- we have got to order these some how
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$list_order_by = "order by 
				menu_access_to_page.title_page desc , page_trans_data.trans_title, page_data.page_web_discussion, menu_access_to_page.trans_identifier , page_data.page_identifier";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- select all the documents from the database for this location
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql = "
			select 
				distinct 
					page_data.page_identifier, 
					page_trans_data.trans_identifier,
					page_trans_data.trans_title,
					page_data.page_web_discussion,  
					menu_access_to_page.page_rank, 
					menu_access_to_page.trans_identifier, 
					menu_data.menu_label, 
					menu_access_to_page.menu_identifier, 
					menu_access_to_page.title_page,
					theme_type_label
			from page_data
				inner join page_trans_data on trans_page = page_identifier
				inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
				inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier 
				inner join theme_types on theme_type_identifier = menu_stylesheet
				$join
			where 
				page_data.page_client=$this->client_identifier and 
				page_trans_data.trans_language ='en' and 
				page_trans_data.trans_published_version=1 and 
				$where 
				(trans_date_available < '$now' or trans_date_available = '0000/00/00 00:00:00') and 
				(trans_date_remove > '$now' or trans_date_remove = '0000/00/00 00:00:00') and 
				page_trans_data.trans_doc_status=".$this->module_constants["__STATUS_PUBLISHED__"]."
				$list_order_by";
		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";

		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}

		$result = $this->call_command("DB_QUERY",Array($sql));
        $number_of_rows = $this->call_command("DB_NUM_ROWS",Array($result));
		$out  = "<module name=\"".$this->module_name."\" display=\"ATOZ\" command=\"PRESENTATION_ATOZ\" call=\"PRESENTATION_ATOZ2\"><label><![CDATA[A to Z index]]></label>";
		$letters = array();
		$letters["undefined"] = 0;
		for ($index = 1 ; $index<=26;$index++){
			$letters[chr($index+64)]=0;
		}
		$title_page_string="";
		$lang = "en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($result) {
			$c=0;
			$choosenletterord =72; // make it a letter 
			if ($choosenletter!=""){
				$choosenletterord = ord($choosenletter);
			}
			if($debug) print "<p>[$choosenletter]</p>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"$choosenletter"));}
			while (($r = $this->call_command("DB_FETCH_ARRAY",Array($result)))){
				$id	= $r["page_identifier"];
				$l	= strtoupper(substr($r["trans_title"],0,1));
				$o	= ord($l);
				if($debug) print "[$l, $o, $choosenletter, ".$r["title_page"]."]";
				if ($choosenletter!=""){
					//print "[$l, $o, $choosenletter, ".$r["title_page"]."]";
					if ($r["title_page"]==0){
						if ($o>=65 && $o <65+26){
							$letters[$l]++;
						} else {
							$letters["undefined"] ++;
						}
						if ($choosenletter==$l || $choosenconfig=='ALL'){
							$fname =$data_files."/presentation_all_".$this->client_identifier."_".$lang."_$id.xml";
							if (file_exists($fname)){
								$fp = fopen($fname, 'r');
								$out .= fread($fp, filesize($fname));
								fclose($fp);
								$page_ids[count($page_ids)]	= $r["trans_identifier"];
							}
							if ($choosenconfig == 'ALL' && $r["title_page"]==0){
								if ($o >= 65 && $o < 65+26){
									$letters[$l]++;
								} else {
									$letters["undefined"] ++;
								}
							}
						} else {
							// if not the choosen letter check if the choosen letter is not a a-z character
							if ($o>=65 && $o <65+26){
							} else {
								if ($choosenletterord>=65 && $choosenletterord<=65+26){
								
								} else {
									$fname =$data_files."/presentation_all_".$this->client_identifier."_".$lang."_$id.xml";
									if (file_exists($fname)){
										$fp = fopen($fname, 'r');
										$out .= fread($fp, filesize($fname));
										fclose($fp);
										$page_ids[count($page_ids)]	= $r["trans_identifier"];
									}
								}
							}
						}
					} else {
						$title_page_string=$r["trans_title"];
					}
				} else {
					if($identifier==-1){
						if ($r["title_page"]==1 || $choosenconfig=='ALL'){					
							$fname = $data_files."/presentation_all_".$this->client_identifier."_".$lang."_$id.xml";
							if (file_exists($fname)){
								$fp = fopen($fname, 'r');
								$out .= fread($fp, filesize($fname));
								fclose($fp);
								$page_ids[count($page_ids)]	= $r["trans_identifier"];
							}
							if ($choosenconfig=='ALL' && $r["title_page"]==0){
								if ($o>=65 && $o <65+26){
									$letters[$l]++;
								} else {
									$letters["undefined"] ++;
								}
							}
						} else {
							if ($o>=65 && $o <65+26){
								$letters[$l]++;
							} else {
								$letters["undefined"] ++;
							}
						}
					} else {
						if ($r["page_identifier"]==$identifier){					
							$fname =$data_files."/presentation_all_".$this->client_identifier."_".$lang."_$id.xml";
							if (file_exists($fname)){
								$fp = fopen($fname, 'r');
								$out .= fread($fp, filesize($fname));
								fclose($fp);
								$page_ids[count($page_ids)]	= $r["trans_identifier"];
//								print $r["trans_identifier"];
							}
//							if ($choosenconfig=='ALL' && $r["title_page"]==0){					
								if ($o>=65 && $o <65+26){
									$letters[$l]++;
								} else {
									$letters["undefined"] ++;
								}
//							}
						} else {
							if ($r["title_page"]==0){					
								if ($o>=65 && $o <65+26){
									$letters[$l]++;
								} else {
									$letters["undefined"] ++;
								}
							}
						}
					}
				}
				$c++;
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		if($title_page_string!=""){
			$out .="<title_page_string><![CDATA[$title_page_string]]></title_page_string>";
		}
		$out .= "<letters choosenletter='$choosenletter'>";
			$out .= "<letter count='".$letters["undefined"]."' lcase='undefined'>#</letter>";
		for ($index = 1 ; $index<=13;$index++){
			$out .= "<letter count='".$letters[chr($index+64)]."' lcase='".chr($index+96)."'>".chr($index+64)."</letter>";
		}
		$out .= "</letters>";
		$out .= "<letters>";
		for ($index = 14 ; $index<=26;$index++){
			$out .= "<letter count='".$letters[chr($index+64)]."' lcase='".chr($index+96)."'>".chr($index+64)."</letter>";
		}
		$out .= "</letters>";
		if ($this->parent->script == $this->parent->real_script){
			$parameters["menu_location"] = $this->call_command("LAYOUTSITE_GET_LOCATION_ID");
			$parameters["label"]		 = "Search";
			$out .= "<filter>".$this->filter($parameters,"PRESENTATION_SEARCH")."</filter>";
		}
		$out .= "</module>";
//		print join($page_ids,",");
		$out.= $this->call_command("SFORM_LOAD_CACHE",Array("list_of_trans"=>$page_ids));
		return $out;
	}
	
	function widget_atoz($parameters){
		$debug = $this->debugit(false,$parameters);
		$choosenletter	= strtoupper($this->check_parameters($parameters,"letter"));
		$choosenconfig	= $this->check_parameters($parameters,"choosenconfig");
		$identifier		= $this->check_parameters($parameters,"identifier",-1);
		$display_fields = $this->check_parameters($parameters,"display_fields",Array());
		$atoz_label		= $this->check_parameters($parameters,"atoz_label","(Index)");		
		$menu_url		= $this->check_parameters($parameters,"menu_url","");

		$arratoz_label	= explode("(",$atoz_label,1);
		$arratoz_label	= explode(")",$arratoz_label[1],1);		
		$atoz_label		= str_replace(array("A to Z Widget (",")"),array("",""),$atoz_label);
		$now   			= $this->libertasGetDate("Y/m/d H:i:s");
		$join  			= "";
		$list_order_by	= "";
		$where 			= "";
		$page_ids		= Array();
		/*
		foreach ($parameters as $key => $value) {
			print "<li>$key  =  $value </li>";
		}
		*/
		if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp)){
				$grp_list ="";
				for($i=0,$m=count($grp);$i<$m;$i++){
					$grp_list .= $grp[$i]["IDENTIFIER"].", ";
				}
				$where .= "
					(
						(group_access_to_page.group_identifier is null or group_access_to_page.group_identifier in ($grp_list -1)) or 
						(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1))
					) and ";
			} else {
				$where .= " (group_access_to_page.group_identifier is null) and ";
			}
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier
					 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier ";
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if (isset($parameters["menu_url"])){
			$where .= " menu_data.menu_url = '".$parameters["menu_url"]."' and ";
		}
			
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- we have got to order these some how
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$list_order_by = "order by 
				menu_access_to_page.title_page desc , page_trans_data.trans_title, page_data.page_web_discussion, menu_access_to_page.trans_identifier , page_data.page_identifier";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- select all the documents from the database for this location
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql = "
			select 
				distinct 
					page_data.page_identifier, 
					page_trans_data.trans_identifier,
					page_trans_data.trans_title,
					page_data.page_web_discussion,  
					menu_access_to_page.page_rank, 
					menu_access_to_page.trans_identifier, 
					menu_data.menu_label, 
					menu_access_to_page.menu_identifier, 
					menu_access_to_page.title_page,
					theme_type_label
			from page_data
				inner join page_trans_data on trans_page = page_identifier
				inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
				inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier 
				inner join theme_types on theme_type_identifier = menu_stylesheet
				$join
			where 
				page_data.page_client=$this->client_identifier and 
				page_trans_data.trans_language ='en' and 
				page_trans_data.trans_published_version=1 and 
				$where 
				(trans_date_available < '$now' or trans_date_available = '0000/00/00 00:00:00') and 
				(trans_date_remove > '$now' or trans_date_remove = '0000/00/00 00:00:00') and 
				page_trans_data.trans_doc_status=".$this->module_constants["__STATUS_PUBLISHED__"]."
				$list_order_by";
		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";

		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}

		$result = $this->call_command("DB_QUERY",Array($sql));
        $number_of_rows = $this->call_command("DB_NUM_ROWS",Array($result));
		$out  = "<module name=\"".$this->module_name."\" display=\"ATOZ_WIDGET\" command=\"PRESENTATION_GET_ATOZ\"><label><![CDATA[A to Z ".$this->makeCleanOutputforXSL($atoz_label)."]]></label>";
		$letters = array();
		$letters["undefined"] = 0;
		for ($index = 1 ; $index<=26;$index++){
			$letters[chr($index+64)]=0;
		}
		$title_page_string="";
		$lang = "en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($result) {
			$c=0;
			$choosenletterord =72; // make it a letter 
			if ($choosenletter!=""){
				$choosenletterord = ord($choosenletter);
			}
			if($debug) print "<p>[$choosenletter]</p>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"$choosenletter"));}
			while (($r = $this->call_command("DB_FETCH_ARRAY",Array($result)))){
				$id	= $r["page_identifier"];
				$l	= strtoupper(substr($r["trans_title"],0,1));
				$o	= ord($l);
				if($debug) print "[$o, ".strtoupper(substr($r["trans_title"],0,1)).", ".$r["trans_title"]."]";
				if ($choosenletter!=""){
					if ($r["title_page"]==0){
						if ($o>=65 && $o <65+26){
							$letters[$l]++;
						} else {
							$letters["undefined"] ++;
						}
						if ($choosenletter==$l || $choosenconfig=='ALL'){
							//if ($choosenconfig == 'ALL' && $r["title_page"]==0){
								if ($o >= 65 && $o < 65+26){
									$letters[$l]++;
								} else {
									$letters["undefined"] ++;
								}
							//}
						} 
					} else {
						$title_page_string=$r["trans_title"];
					}
				} else {
					if($identifier==-1){												
						if ($r["title_page"]==1 || $choosenconfig=='ALL'){					
							//if ($choosenconfig=='ALL' && $r["title_page"]==0){
								if ($o>=65 && $o <65+26){
									$letters[$l]++;
								} else {
									$letters["undefined"] ++;
								}
							//}
						} else {
							if ($o>=65 && $o <65+26){
								$letters[$l]++;
							} else {
								$letters["undefined"] ++;
							}
						}
					} else {
						if ($r["page_identifier"]==$identifier){					
							if ($o>=65 && $o <65+26){
								$letters[$l]++;
							} else {
								$letters["undefined"] ++;
							}

						} else {
							if ($r["title_page"]==0){					
								if ($o>=65 && $o <65+26){
									$letters[$l]++;
								} else {
									$letters["undefined"] ++;
								}
							}
						}
					}
				}
				$c++;
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$out .= "<uri><![CDATA[".dirname($menu_url)."/]]></uri>";
		$out .= "<letters choosenletter='$choosenletter'>";
			$out .= "<letter count='".$letters["undefined"]."' lcase='undefined'>#</letter>";
		for ($index = 1 ; $index<=13;$index++){
			$out .= "<letter count='".$letters[chr($index+64)]."' lcase='".chr($index+96)."'>".chr($index+64)."</letter>";
		}
		$out .= "</letters>";
		$out .= "<letters>";
		for ($index = 14 ; $index<=26;$index++){
			$out .= "<letter count='".$letters[chr($index+64)]."' lcase='".chr($index+96)."'>".chr($index+64)."</letter>";
		}
		$out .= "</letters>";
		$out .= "</module>";

		return $out;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: slideshow()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- function to give slideshow functionality to the system
	-
	- the slideshow function requires the PRESENTATION_SLIDESHOW channel this channel is used in replace of
	- PRESENTATION_DISPLAY or PRESENTATION_ATOZ.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/	
	function slideshow($parameters){
		$debug=false;
		$display_fields = $this->check_parameters($parameters,"display_fields",Array());
		$now   			= $this->libertasGetDate("Y/m/d H:i:s");
		$join  			= "";
		$list_order_by	= "";
		$where 			= "";
		$page_ids		= Array();
		$cmd 			= $this->check_parameters($parameters,"cmd");
		
		if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp)){
				$grp_list ="";
				for($i=0,$m=count($grp);$i<$m;$i++){
					$grp_list .= $grp[$i]["IDENTIFIER"].", ";
				}
				$where .= "
					(
						(group_access_to_page.group_identifier is null or group_access_to_page.group_identifier in ($grp_list -1)) or 
						(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1))
					) and ";
			} else {
				$where .= " (group_access_to_page.group_identifier is null) and ";
			}
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier
					 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier ";
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if (isset($parameters["override_script"])){
			$where .= " menu_data.menu_url = '".$parameters["override_script"]."' and ";
			$where2 = " menu_data.menu_url = '".$parameters["override_script"]."' and ";
//			$where .= " menu_data.menu_identifier = ".$parameters["current_menu_location"]." and ";
//			$where2 = " menu_data.menu_identifier = ".$parameters["current_menu_location"]." and ";
		}else{
			$where .= " menu_data.menu_url = '".$this->parent->script."' and ";
			$where2 = " menu_data.menu_url = '".$this->parent->script."' and ";
		}

			
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- we have got to order these some how
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql = "
			select
				menu_sort_tag_value
			from menu_data 
				left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
			where
				$where2 menu_client = $this->client_identifier";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__," :: [$sql]"));
			}
			$result = $this->call_command("DB_QUERY",Array($sql));
			if ($result){
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$list_order_by = "order by 
						menu_access_to_page.title_page desc , ".$r["menu_sort_tag_value"].", page_data.page_web_discussion, menu_access_to_page.trans_identifier , page_data.page_identifier";
				}
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- select all the documents from the database for this location
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$extra_fields =", trans_title";
			foreach($display_fields as $key=>$val){
				if ($key=='date'){
					$extra_fields .=", trans_date_available";
				}
				if ($key=='summary'){
					$extra_fields .=", trans_summary1";
				}
				if ($key=='url'){
					$extra_fields .=", menu_url";
				}
			}
			$sql = "
			select 
				distinct 
					page_data.page_identifier, 
					page_trans_data.trans_identifier,
					page_trans_data.trans_title,
					page_data.page_web_discussion,  
					menu_access_to_page.page_rank, 
					menu_access_to_page.trans_identifier, 
					menu_data.menu_label, 
					menu_access_to_page.menu_identifier, 
					menu_access_to_page.title_page,
					theme_type_label
					$extra_fields
			from page_data
				inner join page_trans_data on trans_page = page_identifier
				inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
				inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier 
				inner join theme_types on theme_type_identifier = menu_stylesheet
				$join
			where 
				page_data.page_client=$this->client_identifier and 
				page_trans_data.trans_language ='en' and 
				page_trans_data.trans_published_version=1 and 
				$where 
				(trans_date_available < '$now' or trans_date_available = '0000/00/00 00:00:00') and 
				(trans_date_remove > '$now' or trans_date_remove = '0000/00/00 00:00:00') and 
				page_trans_data.trans_doc_status=".$this->module_constants["__STATUS_PUBLISHED__"]."
				$list_order_by";
		if ($debug) print "[ $sql ]";
		if ($cmd=="PRESENTATION_SLIDESHOW_TOPBOTTOM"){
			$d = "SLIDESHOW_TOPBOTTOM";
		}else{
			$d = "SLIDESHOW";
		}
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$out  = "<module name=\"".$this->module_name."\" display=\"$d\" command=\"$cmd\" call=\"PRESENTATION_SLIDESHOW\"><label><![CDATA[Slide Show]]></label>";
		$lang = "en";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$pagelists="";
		$found=-1;
		
		$path_info  = split("/",$this->parent->script);
		$url = "";
		for ($i =0;$i<count($path_info)-1;$i++){
			$url .= $path_info[$i]."/";
		}
		if ($result) {
			$c=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY",Array($result)))){
				$c++;
				$id	= $r["page_identifier"];
				if ($debug) print $url . $this->make_uri($r["trans_title"]).".php == ".$this->parent->real_script."<br/>";
//				print "<!-- \n". $url . $this->make_uri($r["trans_title"]).".php\n = \n".$this->parent->real_script."\n \n".substr($this->parent->real_script,strlen($this->parent->real_script)-9)." == index.php\n [$c]\n ".file_exists($fname)."\n file_exists ".$data_files."/presentation_".$this->client_identifier."_".$lang."_$id.xml -->\n";
				if ($url . $this->make_uri($r["trans_title"]).".php" == $this->parent->real_script || (substr($this->parent->real_script,strlen($this->parent->real_script)-9)=="index.php" && $c==1)){
					$fname = $data_files."/presentation_all_".$this->client_identifier."_".$lang."_$id.xml";
					if (file_exists($fname)){
						$fp = fopen($fname, 'r');
						$out .= fread($fp, filesize($fname));
						fclose($fp);
						$page_ids[count($page_ids)]	= $r["trans_identifier"];
						$found = $r["trans_identifier"];
					}
				}
				$pagelists .= "<page index='".$c."' id='".$r["trans_identifier"]."'>
									<label><![CDATA[".$r["trans_title"]."]]></label>
									<url><![CDATA[".substr($this->parent->script,0,strlen($this->parent->script)-9).$this->make_uri($r["trans_title"]).".php]]></url>
								</page>";
			}
			$this->call_command("DB_FREE",Array($result));
		}
		if($pagelists!="" && $c>1){
			$out .= "<pagelists found='$found'>$pagelists</pagelists>";
		}
		$out .= "</module>";
//		print join($page_ids,",");
		$out.= $this->call_command("SFORM_LOAD_CACHE",Array("list_of_trans"=>$page_ids));
		return $out;
	}
	
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    - Show the archive based on filter options
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
function show_archive($parameters){
		//print_r($parameters);

		$filteron	= $this->check_parameters($parameters,"archive_filter");
		$arc_value	= $this->check_parameters($parameters,"archive_value");
		$nYear = $this->libertasGetDate("Y");
		$where 		= "";
		$join="";
		$lang="en";
		$page_documents	= Array();
		$page_ids		= Array();
		$years			= Array();
		
		$page_rank=Array();
		$found =-1;
		$page_com="";
		$page_doc="";
		//$menu_archive_display=6; //IT IS A DEFAULT VALUE IN CASE IF THE VALUE IS NOT COMMING FROM DATABASE
		
		if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
		//	print_r ("1");
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp)){
				$grp_list ="";
				for($i=0,$m=count($grp);$i<$m;$i++){
					$grp_list .= $grp[$i]["IDENTIFIER"].", ";
				}
				$where .= "
					(
						(group_access_to_page.group_identifier is null or group_access_to_page.group_identifier in ($grp_list -1)) or 
						(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1))
					) and ";
			} else {
				$where .= " (group_access_to_page.group_identifier is null) and ";
			}
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier
					 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier ";
		}
		
		if (isset($parameters["override_script"])){
			$where .= " menu_data.menu_url = '".$parameters["override_script"]."' and ";
			$where2 = " menu_data.menu_url = '".$parameters["override_script"]."' and ";
		}else{
			$where .= " menu_data.menu_url = '".$this->parent->script."' and ";
			$where2 = " menu_data.menu_url = '".$this->parent->script."' and ";
		}
		$display_one = 0;
		
		
	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* we have got to order these some how
		*/
		$sql = "
		select
			menu_archive_page_label, menu_archive_display,  menu_sort_tag_value
		from menu_data 
			left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
		where
			$where2 
			menu_client = $this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"$type :: [$sql]"));
		
		}
		$result = $this->call_command("DB_QUERY",Array($sql));
		//print_r ($sql);
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
//				$list_order_by = "order by menu_access_to_page.title_page desc ,".$r["menu_sort_tag_value"].", page_data.page_web_discussion, menu_access_to_page.trans_identifier , page_data.page_identifier";
				$list_order_by = "order by trans_date_available desc, menu_access_to_page.trans_identifier , page_data.page_identifier";
				
				$menu_archive_display = $r["menu_archive_display"];
				
				$menu_archive_page_label = $r["menu_archive_page_label"];
			}
			
		}
		/**
		* select all the documents from the database for this location
		*/
		
		$now = $this->libertasGetDate();
		$sql = "
		select 
			distinct 
				page_data.page_identifier, 
				page_data.page_web_discussion,  
				menu_access_to_page.page_rank, 
				menu_access_to_page.trans_identifier, 
				menu_data.*, 
				menu_access_to_page.title_page,
				theme_type_label,
				theme_type_field_list,
				page_trans_data.trans_date_publish, trans_date_available
		from page_data
			inner join page_trans_data on trans_page = page_identifier
			inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier and title_page=0
			inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier 
			inner join theme_types on theme_type_identifier = menu_stylesheet
			$join
		where 
			page_data.page_client=$this->client_identifier and 
			page_trans_data.trans_language ='en' and 
			page_trans_data.trans_published_version=1 and 
			$where 
			(trans_date_available < '$now' or trans_date_available = '0000/00/00 00:00:00') and 
			(trans_date_remove > '$now' or trans_date_remove = '0000/00/00 00:00:00') and 
			page_trans_data.trans_doc_status=".$this->module_constants["__STATUS_PUBLISHED__"]."
			$list_order_by
		";


		//print_r($sql);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"$type :: [$sql]"));
		}
		$result = $this->call_command("DB_QUERY",Array($sql));
		$records_returned=$this->call_command("DB_NUM_ROWS",Array($result));
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: ".dirname(__FILE__)));
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: ".print_r($this->parent->site_directories,true)));
		
		}
		$sz="";
		if ($records_returned==1){
			$load_notes=1;
		}
		if ($records_returned>0){
		
			/*
			if($archive==1){
				if ($menu_archive_display==1){
					$newerthan = mktime (0,0,0,date("m"),date("d"),  date("Y")-1); 
				} else if ($menu_archive_display==2){
					$newerthan = mktime (0,0,0,date("m")-1,date("d"),  date("Y")); 
				} else {
					$return_the_first = $menu_archive_display; // return x number of pages
				}
			}
			if ($return_the_first==-1){
				$return_the_first =$records_returned; // set to return all (defualt) archive setting overrides
			}
			*/
			$count=0;
			$firstm=-1;
			$max_num_of_pages_to_show = 8; // Max number of stories to show when year filter is on.
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			
				$count++;
				//print "<p>$count</p>";
				$y = date("Y",strtotime($r["trans_date_available"]));
				$m = date("M",strtotime($r["trans_date_available"]));
				
				//print_r ($years);
				if($firstm==-1){
					if($arc_value==$y){
						$firstm=$m;
					}
				}

				//				print "<li>($y)($m)($filteron)($arc_value)($nYear)</li>";
				$showok=1;
				//print "<li> $i <li>";
				if($r["title_page"]!=1){
					if($filteron=="Year"){
						$iYear = Date("Y",strtotime($r["trans_date_available"]));

						if($menu_archive_display==6 || ($iYear != $nYear)){
							if(empty($years[$y])){
								$years[$y] = Array(
									"Jan"=>0,
									"Feb"=>0,
									"Mar"=>0,
									"Apr"=>0,
									"May"=>0,
									"Jun"=>0,
									"Jul"=>0,
									"Aug"=>0,
									"Sep"=>0,
									"Oct"=>0,
									"Nov"=>0,
									"Dec"=>0
								);
							}
							$years[$y][$m]++;
						}
						if(($arc_value != $iYear) || ($i > $max_num_of_pages_to_show)){
							$showok=0;
						}
					} else if ($filteron=="Year|Month"){
						if(empty($years[$y])){
							$years[$y] = Array(
								"Jan"=>0,
								"Feb"=>0,
								"Mar"=>0,
								"Apr"=>0,
								"May"=>0,
								"Jun"=>0,
								"Jul"=>0,
								"Aug"=>0,
								"Sep"=>0,
								"Oct"=>0,
								"Nov"=>0,
								"Dec"=>0
							);
						}
						$years[$y][$m]++;
						if(strlen($arc_value) == 4){
							if (($arc_value != Date("Y",strtotime($r["trans_date_available"]))) || ($i > $max_num_of_pages_to_show))
								$showok=0;
						}
						else {		
							if($arc_value != Date("Y:M",strtotime($r["trans_date_available"])))
							$showok=0;
						}
					}
				}
				
				if($menu_archive_display==6 && $filteron=="Year"){
					if($m!=$firstm){
						$showok=0;
					}
				}
//				print "<li>$showok</li>";
				if($showok==1){
					if ($this->check_parameters($r,"title_page",0) == 1 || $records_returned==1){
						$i = count($page_documents);
						$fname=$data_files."/presentation_all_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
						
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: $fname"));
						}
						if (file_exists($fname)){
							$t = strtotime($r["trans_date_publish"]);
							if ($t > $this->parent->updated_date){
								$this->parent->updated_date = $t;
							}
							$found=$i;
							$page_ids[count($page_ids)]	= $r["trans_identifier"];
							$page_documents[$i] = $fname;
						}
						$page_rank[$i] = $r["page_rank"];
					} else {
						$theme_type_field_list = $r["theme_type_field_list"];
						$i = count($page_documents);
						$fname = $data_files."/presentation_".$theme_type_field_list."_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
						
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: $fname"));
						}
						if (file_exists($fname)){
							$t = strtotime($r["trans_date_publish"]);
							if ($t > $this->parent->updated_date){
								$this->parent->updated_date = $t;
							}
							$page_ids[count($page_ids)]	= $r["trans_identifier"];
							$page_documents[$i] = $fname;
							$page_rank[$i] = $r["page_rank"];
						}
					}
				}
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"variables :: display_one [$display_one] Found[$found] docs:: ".print_r($page_documents,true)));}
		if ($found!=-1){
			$page_doc=$page_documents[$found];
		}else{
			$page_doc="";
		}
//		print_r($years);
		$page = $this->check_parameters($parameters,"display_page",-1);
		
		//!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! THE GENERATION OF THE XML STARTS FROM HERE !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
		$out = "<uid>".md5(uniqid(rand(), true))."</uid>";
		
		
		$out .= "<module name=\"".$this->module_name."\" display=\"ENTRY\" ><time><![CDATA[".$this->libertasGetTime()."]]></time>";
		
		
		//print 	"[$filteron, $menu_archive_display]";
		//krsort($years);
		if(strpos($arc_value,":")){
			$arc_value_list = split(":",$arc_value);
			$arc_value		= $arc_value_list[0];
		}
		
			$out .="<page_archive filter='$arc_value'>";			
			$root = $this->parent->site_directories["ROOT"];
			$path = $root."/".dirname($this->parent->script);
			/** If years array is empty then get the archive years */
			if (sizeof($years[$y])==0){
				$d = dir($path); 
				while (false !== ($entry = $d->read())) { 
					if ((substr($entry,-4)==".php") && (intval(substr($entry,1,4))>(intval(date("Y"))-10))){
						$y=intval(substr($entry,1,4));
						$years[$y]++;
					}
				} 
				$d->close();				
			}
		
			if ($filteron=="Year" && ($menu_archive_display==1 || $menu_archive_display==2 || $menu_archive_display==6)){
				//$out.="<year id='$nYear' link='index.php'/>";
				foreach($years as $myYear => $val){
					$out.="<year id='$myYear'/>";
					if(!file_exists($path."/-$myYear.php")){
						$this->makeSpecial("PRESENTATION_ARCHIVE", $this->parent->script, $path."/-$myYear.php",Array("archive_filteron" 	=> "Year","archive_value"		=> "$myYear"),"Archive content for $myYear");

						}
			
				}
		
			} else if ($filteron=="Year|Month" || $menu_archive_display==6){			
				foreach($years as $myYear => $val){				
					$out.="<year id='$myYear'>";					
					if(!file_exists($path."/-$myYear.php")){
						$this->makeSpecial("PRESENTATION_ARCHIVE", $this->parent->script, $path."/-$myYear.php",Array("archive_filteron" 	=> "Year","archive_value"		=> "$myYear"),"Archive content for $myYear");
					}
					
					foreach($years[$myYear] as $month => $val){
						//SHOW ONLY THE MONTH WITH NO LINK WHERE THERE IS NO ARCHIVE DATA AVAILABLE
						//TO CHANGE THE WHOLE SEQUENCE FOLLOWING TECHNIQUE IS USED
						//IN 'functions_default.xsl' ON LINE 1075-1095 THE IF CONDITION IS CHANGED TO MORE
						//BROADER ASPECT		
										
						if(file_exists($path."/-$myYear-$month.php")){
							$out.="<month id='$month' link='1' />";								
						}
						else{				
							$out.="<month id='$month' link='0' />";
						}													
					}
					$out.="</year>";
				
				}
			} else {
				$out.="<![CDATA[Leave the archive]]>";
				$out.= "<filter>".$this->filter($parameters, 'PRESENTATION_ARCHIVE')."</filter>";
			}

			/*
				if($menu_archive_display==2 && $filteron=="Year"){
					 $ac = split(":",$arc_value);
					 foreach($years as $yr => $mths){
					 	if($yr==$ac[0]){
						 	foreach($years[$yr] as $mths => $v){
								if($v>0){
								 	$sz .= "<li><a href='".dirname($this->parent->script)."/-$yr-$mths.php'>$mths</a></li>\n";
								}
							}
						}
					 }
				}
*/
			if($menu_archive_page_label==""){
				$menu_archive_page_label = $this->check_parameters($parameters,"fake_title");
			} 
			
			//~~~~~~~~~~ CHANGES DONE BY SHAHZAD TO HIDE THE 'ARCHIVE FOR YYYY LINKED 

			$out.="<dynamic_title title='$menu_archive_page_label' show_title='1' ></dynamic_title>";
			
		$out .="</page_archive><page identifier='-1'>
				<content><![CDATA[<ul>$sz</ul>]]></content>
			</page>";
			
			/*$out .="</page_archive><page identifier='-1'>
				<title><![CDATA[".$menu_archive_page_label."]]></title>
				<content><![CDATA[<ul>$sz</ul>]]></content>
			</page>";
			*/
			
		$c=0;
		if ($page==-1){
			if ($page_doc!=""){
				
				$c++;
				$fp		 = fopen($page_doc, "rb");
				if ($fp){
					while (!feof($fp)){
						$out .= fread($fp, 4096);
					}
				}
				fclose($fp);
				if ($page_com!=""){
					$fp		 = fopen($page_com, "rb");
					if ($fp){
						while (!feof($fp)){
							$out .= fread($fp, 4096);
						}
					}
					fclose($fp);
				}
			}
			$length_of_entities = count($page_documents);
			for ($index	 = 0; $index < $length_of_entities; $index++){
				 if ($index!=$found){
					$c++;
					$fp		 = fopen($page_documents[$index], "rb");
					if ($fp){
						while (!feof($fp)){
							$out .= fread($fp, 4096);
						}
					}
					fclose($fp);
				}
			}
		}else {
			
			$counter=0;
			if ($page==0){
				$c++;
				$fp	 = fopen($page_documents[$found], "rb");
				if ($fp){
					while (!feof($fp)){
						$out .= fread($fp, 4096);
					}
				}
				fclose($fp);
				for ($index	 = 0, $length_of_entities = count($page_documents); $index < ($length_of_entities -1); $index++){
					$out .= "<page/>";
				}
			} else {
				$count=-1;
				for ($index	 = 0, $length_of_entities = count($page_documents); $index < $length_of_entities; $index++){
					 if ($index==$page-1) {
						$c++;
						$count++;
						$fp	 = fopen($page_documents[$count], "rb");
						if ($fp){
							while (!feof($fp)){
								$out .= fread($fp, 4096);
							}
						}
						fclose($fp);
					} else {
						$out .= "<page/>";
						$count++;	
						$c++;
					}
				}
			}
		}
		$out .= "</module>";
		//print_r($out);
//		$out.= $this->call_command("SFORM_LOAD_CACHE",Array("list_of_trans"=>$page_ids));
		return $out;
	
	}
	function display_persistant($type,$parameters){
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_persistant",__LINE__,"[".print_r($parameters,true)."]"));}		
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* get the current date and time 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$now   			= $this->libertasGetDate("Y/m/d H:i:s");
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* get the data directory path (location of xml cache files)
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* get_first_non_title_page when on the menu location then get the first non title page.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if (strpos($this->parent->real_script,"index.php")===false){
			$get_first_non_title_page	= 0;
		} else {
			$get_first_non_title_page 	= 1; 
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Define function variables
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$sname							= $this->check_parameters($parameters,"SCRIPT_NAME");
		$lang  							= "en";
		$list_pages_by_this_order_by	= "";
		$join  							= "";
		$list_order_by					= "";
		$where 							= "";
		$headline_where 				= "";
		$headline						= 0;
		$return_the_first				= -1;
		$titlelinks 					= Array(); // titlelinks are the links at appear between the title page and the location content
		$page_documents					= "";
		$page_ids						= Array();
		$years							= Array();
		$cy								= -1; // current year
		$cm								= -1; // current month
		$sz								= "";
		$page_rank						= Array();
		$found 							= -1;
		$page_com						= "";
		$page_doc						= "";
		$headline_menus					= Array();
		/**
		* extract informtion from parameters
	*/ 
		$display_fields			 	= $this->check_parameters($parameters, "display_fields", Array());
		$current_menu_location	 	= $this->check_parameters($parameters, "override_script", $this->parent->script);
		$display_fields_counted 	= count($display_fields);
		/**
		* get the current menu locations Identifier
	*/ 
		$headline 			=0; // 0 = no headline 1= headlines
		$headline_all		=0; // 0= defined list of menu locations, 1 = all children
		$headline_content	=0; // 0 = title only, 1 = title and summary
		$headline_count		=3; // number of items per menu location
		$headline_label		=0; // show ide labels
		$headline_titles	=0;
		$sql = "select menu_data.*, menu_sort_tag_value from menu_data 
					left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
				where menu_client = $this->client_identifier and menu_url = '$current_menu_location'";
//		print $sql;
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$headline 			= $r["menu_headline"];
			$headline_all		= $r["menu_headline_all"];
			$headline_content	= $r["menu_headline_content"];
			$headline_count		= $r["menu_headline_counter"];
			$headline_label		= $r["menu_headline_label"];
			$headline_titles	= $r["menu_headline_title_pages"];
			$menu_identifier	= $r["menu_identifier"];
			$archive			= $r["menu_archiving"];
			if ($archive==1){
				$menu_archive_on			= $r["menu_archive_on"];
				$menu_archive_display		= $r["menu_archive_display"];
				$menu_archive_access		= $r["menu_archive_access"];
				$menu_archive_label			= $r["menu_archive_label"];
			} else {
				$menu_archive_on			= 0;
				$menu_archive_display		= 0;
				$menu_archive_access		= 0;
				$menu_archive_label			="";
			}
			$list_pages_by_this_order_by	= "order by 
					 menu_access_to_page.title_page desc ,".$r["menu_sort_tag_value"].", page_data.page_web_discussion, menu_access_to_page.trans_identifier , page_data.page_identifier";
        }
		if (strpos($this->parent->real_script,"index.php")==false){
			$headline 			= 0;
		}
		$this->call_command("DB_FREE",Array($result));
		/**
		* if the group module exists then check for group access to pages (do not include restricted pages
	*/ 
		if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp)){
				$grp_list ="";
				for($i=0,$m=count($grp);$i<$m;$i++){
					$grp_list .= $grp[$i]["IDENTIFIER"].", ";
				}
				$where .= "
					(
						(group_access_to_page.group_identifier is null or group_access_to_page.group_identifier in ($grp_list -1)) or 
						(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1))
					) and ";
				$headline_where .= "
					(
						(group_access_to_page.group_identifier is null or group_access_to_page.group_identifier in ($grp_list -1)) or 
						(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1))
					) and ";
			} else {
				$where .= " (group_access_to_page.group_identifier is null) and ";
				$headline_where .= " (group_access_to_page.group_identifier is null) and ";
			}
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier
					 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier ";
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* and this menu location
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$where .= " menu_data.menu_url = '$current_menu_location' and ";
		/**
		* select all the documents from the database for this location
		*/
		$sql = "
		select 
			distinct 
				page_data.page_identifier, 
				page_data.page_web_discussion,  
				menu_access_to_page.page_rank, 
				menu_access_to_page.trans_identifier, 
				menu_data.*, 
				menu_access_to_page.title_page,
				theme_type_label,
				theme_type_field_list,
				page_trans_data.trans_date_publish,
				page_trans_data.trans_title,
				page_trans_data.trans_dc_url,
				page_trans_data.trans_date_available,
				memo_information.mi_memo
		from page_data
			inner join page_trans_data on trans_page = page_identifier
			inner join memo_information on mi_link_id = page_trans_data.trans_identifier and mi_type='PAGE_' and mi_field='summary' and mi_client=trans_client
			inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
			inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier 
			inner join theme_types on theme_type_identifier = menu_stylesheet
			$join
		where 
			page_data.page_client=$this->client_identifier and 
			page_trans_data.trans_language ='en' and 
			page_trans_data.trans_published_version=1 and 
			$where 
			(trans_date_available < '$now' or trans_date_available = '0000/00/00 00:00:00') and 
			(trans_date_remove > '$now' or trans_date_remove = '0000/00/00 00:00:00') and 
			page_trans_data.trans_doc_status=".$this->module_constants["__STATUS_PUBLISHED__"]."
			$list_pages_by_this_order_by";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"$type :: [$sql]"));
		}
		$result = $this->call_command("DB_QUERY",Array($sql));
		$records_returned=$this->call_command("DB_NUM_ROWS",Array($result));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: ".dirname(__FILE__)));
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: ".print_r($this->parent->site_directories,true)));
		}
		if ($records_returned>0){
			$title_page 	= 0;
			$content_table	= "";
			$found			= -1;
			$count			= 0;
			$directory = dirname($this->parent->real_script);
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				if ($this->check_parameters($r,"title_page",0) == 1){
					$fname=$data_files."/presentation_all_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
					if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: $fname"));}
					if (file_exists($fname)){
						$t = strtotime($r["trans_date_publish"]);
						if ($t > $this->parent->updated_date){
							$this->parent->updated_date = $t;
						}
						$fp		 = fopen($fname, "rb");
						if ($fp){
							while (!feof($fp)){
								$page_documents	.= fread($fp, 4096);
							}
						}
						fclose($fp);
						$page_ids[count($page_ids)]	= $r["trans_identifier"];
					}
				} else {
					if(($get_first_non_title_page==1 && $count==0) || ($this->parent->base.$directory."/".$r["trans_dc_url"] == $sname)){
						$titlelinks[count($titlelinks)] = "<link clickable='0'><url><![CDATA[".$r["trans_dc_url"]."]]></url><title><![CDATA[".$r["trans_title"]."]]></title><description><![CDATA[".str_replace(array("\r\n","\r","\n"),array("","",""),strip_tags(html_entity_decode($r["mi_memo"])))."]]></description></link>";
					} else {
						$titlelinks[count($titlelinks)] = "<link><url><![CDATA[".$r["trans_dc_url"]."]]></url><title><![CDATA[".$r["trans_title"]."]]></title><description><![CDATA[".str_replace(array("\r\n","\r","\n"),array("","",""),strip_tags(html_entity_decode($r["mi_memo"])))."]]></description></link>";
					}
					
					//print $this->parent->base.$directory.$r["trans_dc_url"]." == ".$sname;
					if(($get_first_non_title_page==1 && $count==0) || ($this->parent->base.$directory."/".$r["trans_dc_url"] == $sname)){
						$fname = $data_files."/presentation_all_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
						if ($this->module_debug){
							$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: $fname"));
						}
						if (file_exists($fname)){
							$count++;  // only count it when found
							$t = strtotime($r["trans_date_publish"]);
							if ($t > $this->parent->updated_date){
								$this->parent->updated_date = $t;
							}
							$fp		 = fopen($fname, "rb");
							if ($fp){
								while (!feof($fp)){
									$page_documents	.= fread($fp, 4096);
								}
							}
							fclose($fp);
							$page_ids[count($page_ids)]	= $r["trans_identifier"];
						}
					}
				}
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
			if($headline==1){
				$menu_list = Array();
				if($headline_all==1){
					// extract from defined list of children
					$sql = "select * from menu_to_object 
						inner join menu_data on mto_menu = menu_identifier and mto_client=menu_client
						left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
					where mto_client = $this->client_identifier and mto_object = $menu_identifier and mto_module='LAYOUT_' order by menu_order";
                    $result  = $this->call_command("DB_QUERY",Array($sql));
					$c=0;
                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	                   	$menu_list[count($menu_list)] = Array($r["mto_menu"], $r["menu_sort_tag_value"]);
	                }
                    $this->call_command("DB_FREE",Array($result));
				}else{
					// extract from all children
					$sql = "select * from menu_data 
							left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
							where menu_client = $this->client_identifier and 
							(menu_parent = $menu_identifier or menu_identifier = $menu_identifier) 
							order by menu_order";
                    $result  = $this->call_command("DB_QUERY",Array($sql));
					$c=0;
					$menu_url ="";
                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
						if($r["menu_identifier"] !=$menu_identifier ){
                    		$menu_list[count($menu_list)] = Array($r["menu_identifier"], $r["menu_sort_tag_value"]);
							$c++;
						} else {
							$menu_url = $r["menu_url"];
						}
                    }
                    $this->call_command("DB_FREE",Array($result));
					if ($c==0 && $menu_url=="index.php"){
						$sql = "Select * from menu_data 
									left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
								where menu_client=$this->client_identifier and menu_parent=-1 and menu_url!='index.php' and menu_url!='admin/index.php' order by menu_order";
	                    $result  = $this->call_command("DB_QUERY",Array($sql));
						$c=0;
						$menu_url ="";
	                    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	                    		$menu_list[count($menu_list)] = Array($r["menu_identifier"], $r["menu_sort_tag_value"]);
	                    }
	                    $this->call_command("DB_FREE",Array($result));
					}
				}
				$m = count($menu_list);
//				print "<li>$sql</li>";
//				print_r($menu_list);
				for($i=0;$i<$m;$i++){
					$list_order_by = $menu_list[$i][1];
					$sql = "
					select 
						distinct 
							page_data.page_identifier, 
							page_data.page_web_discussion,  
							menu_access_to_page.page_rank, 
							menu_access_to_page.trans_identifier, 
							menu_data.*, 
							menu_access_to_page.title_page,
							theme_type_label,
							theme_type_field_list,
							page_trans_data.trans_date_publish,
							title_page
					from page_data
						inner join page_trans_data on trans_page = page_identifier
						inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
						inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier 
						inner join theme_types on theme_type_identifier = menu_stylesheet
						$join
					where 
						page_data.page_client=$this->client_identifier and 
						page_trans_data.trans_language ='en' and 
						page_trans_data.trans_published_version=1 and  
						$headline_where 
						menu_data.menu_identifier = ".$menu_list[$i][0]." and 
						menu_data.menu_url not in ('index.php','admin/index.php') and
						(trans_date_available < '$now' or trans_date_available = '0000/00/00 00:00:00') and 
						(trans_date_remove > '$now' or trans_date_remove = '0000/00/00 00:00:00') and 
						page_trans_data.trans_doc_status=".$this->module_constants["__STATUS_PUBLISHED__"]." 
						";
					if($headline_titles==0){
						$sql .=	"and title_page=0 ";
					}
					$sql .=	"order by $list_order_by";
//					print "<li>$sql</li>";
		            $result  = $this->call_command("DB_QUERY",Array($sql));
					$pos = count($headline_menus);
					$c=0;
					while(($r = $this->call_command("DB_FETCH_ARRAY",Array($result))) && ($c<$headline_count)){
						if ($c==0){
							$headline_menus[$pos] = Array("label"=> $r["menu_label"],"list"=>Array(),"pages"=>Array(),"files"=>Array(),"uri"=>$r["menu_url"], "title_page" => Array());
						}
						$c++;
						if (($headline_content==1) || ($headline_content==4) || ($headline_content==5)){
							$fname=$data_files."/presentation_summary_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
						} else {
							$fname=$data_files."/presentation_title_".$this->client_identifier."_".$lang."_".$r["page_identifier"].".xml";
						}
						if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"fname :: $fname"));}
//						print "<li>$fname</li>";
						if (file_exists($fname)){
							$t = strtotime($r["trans_date_publish"]);
							if ($t > $this->parent->updated_date){
								$this->parent->updated_date = $t;
							}
							$headline_menus[$pos]["list"][count($headline_menus[$pos]["list"])] = $r["trans_identifier"];
							$headline_menus[$pos]["pages"][count($headline_menus[$pos]["pages"])] = $r["page_identifier"];
							$headline_menus[$pos]["files"][count($headline_menus[$pos]["files"])] = $fname;
							$headline_menus[$pos]["title_page"][count($headline_menus[$pos]["title_page"])] = $r["title_page"];
						}
                    }
                    $this->call_command("DB_FREE",Array($result));
				}
			}
		/**
		* if there are any headlines then display them
	*/ 
//		print "hkj";
//		print_r($headline_menus);
		$m = count($headline_menus);
		for($i = 0; $i<$m ;$i++){
			$sz.="<headline content='$headline_content' > ";
				$sz.="<uri><![CDATA[".$headline_menus[$i]["uri"]."]]></uri>";
			if (($headline_content==2) || ($headline_content==4)){
				$sz .= "<cols>2</cols>";
			} else if (($headline_content==3) || ($headline_content==5)){
				$sz .= "<cols>3</cols>";
			} else {
				$sz .= "<cols>1</cols>";
			}
			if($headline_label==1){
				$sz.="<label><![CDATA[".$headline_menus[$i]["label"]."]]></label>";
			}
			$list_of_headlines = count($headline_menus[$i]["files"]);
			for ($index	 = 0; $index < $list_of_headlines; $index++){
				$c++;
				$fp		 = fopen($headline_menus[$i]["files"][$index], "rb");
				if ($fp){
					while (!feof($fp)){
						if ($headline_menus[$i]["title_page"][$index]==1){
							$sz.="<title_page identifier='".$headline_menus[$i]["list"][$index]."'/>";
						}
						$sz .= fread($fp, 4096);
					}
				}
				fclose($fp);
			}
			$sz.="</headline>";
		}

		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"variables :: Found[$found] docs:: ".print_r($page_documents,true)));}
		$page	 = $this->check_parameters($parameters,"display_page",-1);
		$out	 = "<uid>".md5(uniqid(rand(), true))."</uid>";
		if($this->parent->theme_type_label=="LOCALE_THEME_013_TYPE_PERSISTANT_3_COLUMN"){
			$cols = 3;
		} else if($this->parent->theme_type_label=="LOCALE_THEME_013_TYPE_PERSISTANT_2_COLUMN"){
			$cols = 2;
		} else {
			$cols = 1;
		}
		$out	.= "<module name=\"".$this->module_name."\" display=\"PERSISTANT\">
						<time><![CDATA[".$this->libertasGetTime()."]]></time>
						<cols>$cols</cols>
					
					";
		$out	.= $page_documents;
		$out	.= join("", $titlelinks);
		$out	.= "$sz</module>";
		if(count($page_ids)>0){
			$out.= $this->call_command("SFORM_LOAD_CACHE",Array("list_of_trans"=>$page_ids));
		}
		return $out;
	}
}
?>
