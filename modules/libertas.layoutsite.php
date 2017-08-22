<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.layout.php
* @date 09 Oct 2002
*/
/**
* This module is for managing the site structure presentation.
*/

class layoutsite extends module {
	/**
	*  Class Variables	
	*/
	var $module_name_label			= "Site Structure Presentation Module";
	var $module_name				= "layoutsite";
	var $module_admin				= "0";
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_debug				= false;
	var $module_modify	 			= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.28 $';
	var $module_creation			= "26/10/2002";
	var $module_command				= "LAYOUTSITE_"; 		// all commands specifically for this module will start with this token
	
	var $menu_structure 			= array();
	var $directories 				= array();
	var $parentIDlist 				= "";
	var $displayed 					= "";
	var $debug_count				= 0;
	var $max_depth					= 0;
	var $module_label				= "MANAGEMENT_LAYOUT";
	
	var $module_admin_options		= array();
	var $module_admin_user_access	= array();
	var $module_display_options 	= array();
	
	var $admin_access				= 0;
	var $menu_access				= 0;
	var $directory_access			= 0;
	var $module_type_admin_access	= 0;
	
	var $title_pages				= Array();
	/**
	* command()
	- want to do anything with this module go through me simply create a condition for
	- the user command that you want to execute and hey presto I'll return the output of
	- that module
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
			if ($user_command == $this->module_command."DEBUG_ON"){
				$this->module_debug=true;
			}
			if ($user_command == $this->module_command."JUMP_TO"){
				print_r($parameters);
				$this->module_jumpto($parameter_list);
			}
			if ($user_command == $this->module_command."DEBUG_OFF"){
				$this->module_debug=false;
			}
			if ($user_command == $this->module_command."GET_MODULE"){
				return $this->get_module_name();
			}
			if ($user_command == $this->module_command."GET_VERSION"){
				return $this->get_module_version();
			}
			if ($user_command == $this->module_command."DISPLAY_IDS"){
				return $this->display_id($this->check_parameters($parameter_list,0,-1));
			}
			if ($user_command == $this->module_command."DISPLAY_CHILD_IDS"){
				return $this->display_child_id($parameter_list);
			}
			if ($user_command==$this->module_command."GET_PAGE"){
				return $this->get_page($parameter_list);
			}
			if ($user_command==$this->module_command."GET_DIRECTORY_PATH"){
				return $this->retrieve_directory_path($parameter_list[0]);
			}
			if ($user_command==$this->module_command."GET_THEME_ID"){
				return $this->layout_retrieve_theme();
			}
			if ($user_command==$this->module_command."MENU_HAS_ACCESS"){
				return $this->have_access($parameter_list);
			}
			if ($user_command==$this->module_command."WEB_MENU"){
				return $this->web_generate_menu();
			}
			if ($user_command==$this->module_command."GET_LOCATION_ID"){
				return $this->layout_retrieve_location_id($parameter_list);
			}
			if ($user_command==$this->module_command."GET_LOCATION_URL"){
				return $this->layout_retrieve_location_url($parameter_list);
			}
			if ($user_command==$this->module_command."SHOW_IMAGE"){
				return $this->get_menu_images($parameter_list);
			}
		}else{
			// wrong command sent to system
			return "";
		}
	}
	function retrieve_directory_path($id=-2){
		$out ="";
		$id *=1;
		if ($id>-1){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if there are no specified directories then check to see if the array has been filled.
			- causes the array being used to be built only once in this recursive function call.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$num_occurs = count($this->directories);
			if ($num_occurs==0){
				$this->get_directories();
				$num_occurs = count($this->directories);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=1-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- get the path.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			for ($index=0;$index<$num_occurs;$index++){
				if ($this->directories[$index]["IDENTIFIER"] == $id){
					$out .= $this->retrieve_directory_path($this->directories[$index]["PARENT"]).$this->directories[$index]["NAME"]."/";
				}
			}
		} else {
			$out="";
		}
		return $out;
	}
	
	function layout_retrieve_theme(){
		$url = $this->parent->script;
		$len = count($this->menu_structure);
		if ($len==0){
			$this->load_menu();
			$len = count($this->menu_structure);
		}
		$found_theme=-1;
		for ($index=0;$index<$len;$index++){
			if ($this->menu_structure[$index]["URL"]==$url){
				if ($this->menu_structure[$index]["THEME"]>0){
					$found_theme = $this->menu_structure[$index]["THEME"];
					break;
				} else {
					if ($this->menu_structure[$index]["PARENT"]>-1){
						$found_theme = $this->get_parent_theme($this->menu_structure[$index]["PARENT"]);
					}
				}
			}
		}
		return $found_theme;
	}

	function layout_retrieve_location_id($parameters){
		$url = $this->check_parameters($parameters,"url",$this->parent->script);
		$len = count($this->menu_structure);
		if ($len==0){
			$this->load_menu();
			$len = count($this->menu_structure);
		}
		$found=-1;
		for ($index=0;$index<$len;$index++){
			if ($this->menu_structure[$index]["URL"]==$url){
				$found  = $this->menu_structure[$index]["IDENTIFIER"];
			}
		}
		return $found;
	}

	function layout_retrieve_location_url($parameters){
		$id = $this->check_parameters($parameters,"id",-1);
		if (is_array($id)){
			$id = $this->check_parameters($id,0);
		} else {
			if (strpos($id,",")===false){
			} else {
				$id=split(",", str_replace(Array(" "), Array(""), $id));
			}
		}
		if (is_array($id)){
			$id = $this->check_parameters($id,0);
		}
		$len = count($this->menu_structure);
		if ($len==0){
			$this->load_menu();
			$len = count($this->menu_structure);
		}
		$found="";
		if ($id > -1){
			for ($index=0;$index<$len;$index++){
				if ($this->menu_structure[$index]["IDENTIFIER"]==$id){
					$found  = $this->menu_structure[$index]["URL"];
				}
			}
		}
		return $found;
	}

	function get_parent_theme($parent){
		$found_theme=-1;
		$len = count($this->menu_structure);
		if ($len==0){
			$this->load_menu();
			$len = count($this->menu_structure);
		}
		for ($index=0;$index<$len;$index++){
			if ($this->menu_structure[$index]["IDENTIFIER"]==$parent){
				if ($this->menu_structure[$index]["THEME"]>0){
					$found_theme = $this->menu_structure[$index]["THEME"];
					break;
				} else {
					if ($this->menu_structure[$index]["PARENT"]>-1){
						$found_theme = $this->get_parent_theme($this->menu_structure[$index]["PARENT"]);
					}
				}
			}
		}
		return $found_theme;
	}

	function initialise(){
		$this->client_identifier = $this->parent->client_identifier;
		if ($this->client_identifier == -1){
			$this->client_identifier=$this->check_parameters($_SESSION,"client_identifier",-1);
		}
		$this->load_locale("layout");
		$this->menu_access	= 0;
		$this->directory_access	= 0;
		return 1;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Generate the menu used in the web site.
	- this is based on the menu sturcture that has been produced by the system
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function web_generate_menu(){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* if the user is not logged in then try to load the anonymous access cached menu.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN","0")=="0"){
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- load the unrestricted menu structure
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
		
			$file_to_use=$data_files."/layout_".$this->client_identifier."_anonymous.xml";
		}else{
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- load the restricted menu structure
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$file_to_use=$data_files."/layout_".$this->client_identifier."_restricted.xml";
		}
		$cached_file = "";
		$sql = "select menu_identifier from menu_data where menu_client = $this->client_identifier and menu_plus = 1";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if(file_exists($data_files."/cat_menu_".$this->client_identifier."_".$r["menu_identifier"].".xml")){
				$cached_file .= join("",file($data_files."/cat_menu_".$this->client_identifier."_".$r["menu_identifier"].".xml"));
			}
        }
        $this->parent->db_pointer->database_free_result($result);
		if (!file_exists($file_to_use)){
			return $this->cache_menu_structure();
		} else {
			$cached_file .= join("",file($file_to_use));
			return "<module name=\"layout\" display=\"menu\">".$cached_file."</module>";
		}
	}

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Fn:: get_page
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- This function will call the modules that have been defined to contain 
	- information for this location that is to be produced.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function get_page($parameters){
		$debug = $this->debugit(false, $parameters);
		$start = substr($this->check_parameters($parameters,"SCRIPT_NAME",$this->parent->real_script),0,strlen($this->parent->base)+1);
		$merge3to2 =0;
		if (($start==$this->parent->base."-") || ($start==$this->parent->base."_")){
			$merge3to2 =1;
		}
		$parameters["current_menu_location"] = $this->call_command("LAYOUTSITE_GET_LOCATION_ID");
		$igCmd								 = $this->check_parameters($parameters, "igCmd");
		// here override this parameter;
		$ml  = $this->check_parameters($parameters,"menu_locations");
		if ($ml!=""){
			if (is_array($ml)){
				$parameters["current_menu_location"] = $ml[0];
			} else {
				$parameters["current_menu_location"] = $ml;
			}
		}
		$pscript= $this->parent->script;
		$this->parent->script = $this->layout_retrieve_location_url(Array("id"=>$parameters["current_menu_location"]));
		$ignoreCommands  = $this->check_parameters($parameters,"ignore_commands",Array());
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_page",__LINE__,"[PARAMETERS} ".print_r($parameters,true)."]"));
		}
		if ($this->check_parameters($parameters,"LIBERTAS_XML")=="OPEN_AND_DISPLAY"){
			$debug = false;
		}
		$parameters["unset_identifier"] =	$this->check_parameters($parameters,"identifier");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_page",__LINE__,$parameters));
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_page",__LINE__,$parameters));
		}
		$remove_condition="";
		if ($this->check_parameters($parameters,"command")!=""){
			$remove_condition = " and wo_command!='PRESENTATION_DISPLAY' and wo_command!='PRESENTATION_SLIDESHOW' and wo_command!='PRESENTATION_ATOZ' and wo_command!='PRESENTATION_ATOZ_ALL'  and wo_command!='PRESENTATION_SLIDESHOW_TOPBOTTOM'";
		}
		$rank_unspecified =900;
		$command = $this->check_parameters($parameters,"command");
		$p = Array();
//		$available_commands = "'".join("','",$this->check_parameters($parameters,"available_commands",Array()))."'";
		$condition ="";
		if ($this->check_parameters($_SESSION,"CHOOSEN_THEME",0)!=0){
			$this->parent->choosen_theme = $this->check_parameters($_SESSION,"CHOOSEN_THEME",0);
		}
		$condition="";
		if ($this->parent->choosen_theme!=0){
			if ($this->parent->choosen_theme==-2 || $this->parent->choosen_theme==-3){
				$condition ="wol_theme = ".$this->parent->previous_theme." and ";
			} else {
				if($this->parent->choosen_theme==-1){
					$condition ="wol_theme = ".$this->parent->real_choosen_theme." and ";
				}else {
					$condition ="wol_theme = ".$this->parent->choosen_theme." and ";
				}
			}
		}
		if ($command!=""){
			if($command == "INFORMATION_SEARCH"){
				$condition .= "wo_command!='INFORMATION_DISPLAY' and ";
			}
			if($igCmd!=""){
				$condition .=" wo_command !='$igCmd' and ";
			}
			$condition .=" wo_command !='PRESENTATION_DISPLAY_PAGE' and  ".$condition;
		}
		for ($index=0,$max=count($ignoreCommands);$index<$max;$index++){
			$condition .=" wo_command != '".$ignoreCommands[$index]."' and ";
		}
		$choosen_layout=-1;
		$extra_condition = "";
		if ($this->check_parameters($_SESSION,"displaymode")=="printerfriendly"){
			$sql ="select distinct
					wol_identifier, wctl_position, wol_layout_design
				from web_layouts
					left outer join menu_to_object on mto_object = wol_identifier and mto_client = wol_client and mto_module ='WEBOBJECTS_'
					left outer join menu_data on menu_identifier = mto_menu and mto_client = menu_client
					left outer join web_container_to_layout on wctl_layout = wol_identifier and wctl_client = wol_client
					left outer join web_containers on wc_identifier = wctl_container and wc_client = wctl_client
					inner join web_objects_in_container on woic_container = wc_identifier and woic_client = wc_client
					inner join web_objects on wo_identifier = woic_object and wo_client = woic_client
				where
					wo_command = 'PRESENTATION_DISPLAY_PAGE' and
					wol_client=$this->client_identifier $extra_condition and
					(wol_all_locations=1 or
						(wol_all_locations=0 and
							(mto_menu is not null and menu_url is not null)
						)
					) and (
						menu_url is null or menu_url = '".$this->parent->script."'
					)
			";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$column_result  = $this->parent->db_pointer->database_query($sql);
			$wctl_position ="";
			while ($r = $this->parent->db_pointer->database_fetch_array($column_result)) {
				if ($choosen_layout==-1){
					$choosen_layout = $r["wol_identifier"];
				}
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"choosen_layout :: $choosen_layout = ".$r["wol_identifier"]." with position of ".$r["wctl_position"]));
				}
				if ($choosen_layout==$r["wol_identifier"]){
/*					if($wctl_position !=""){
						$wctl_position .= ", ";
					}
					$wctl_position .= $r["wctl_position"];
					*/
					if (($r["wol_layout_design"] == "112") || ($r["wol_layout_design"] == "13")){
						$wctl_position .= "2,3,4";
					}
					if (($r["wol_layout_design"] == "31") || ($r["wol_layout_design"] == "4")){
						$wctl_position .= "1";
					}
					if(($r["wol_layout_design"] == "121") || $r["wol_layout_design"] == "1111"){
						$wctl_position .= "2,3";
					}
					if($r["wol_layout_design"] == "211"){
						if($r["wctl_position"]=1)
							$wctl_position .= "1";
						if($r["wctl_position"]=3)
							$wctl_position .= "3";
						if($r["wctl_position"]=3)
							$wctl_position .= "4";
					}
					if($r["wol_layout_design"] == "22"){
						if($r["wctl_position"]=1)
							$wctl_position .= "1";
						if($r["wctl_position"]=3)
							$wctl_position .= "3";
					}
				}
			}
			$this->parent->db_pointer->database_free_result($column_result);
			if ($wctl_position!=""){
				$extra_condition = " and wctl_position in ($wctl_position) ";// and wo_command not like 'WEBOBJECTS_%') ";
			}
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"printer Friendlt",__LINE__,$extra_condition));
			}
		}
		if (substr($this->parent->real_script,0,1)=="-"){
		//	 $extra_condition .= " and wctl_position!=4 ";
		}
		
		/* Starts Apply Default Layout to Basket or all System Generated Root level Pages (Added By Muhammad Imran)*/
		$sql_web_layouts = "select * from web_layouts where wol_client = $this->client_identifier and wol_default=1";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql_web_layouts\n\n\n".$this->check_parameters($_SESSION,"displaymode")));
		}
		$web_layouts_results = $this->parent->db_pointer->database_query($sql_web_layouts);
        $number_of_rows_returned = $this->call_command("DB_NUM_ROWS",Array($web_layouts_results));
/*
if ($this->parent->domain == 'www.2and4wheels.com'){
	echo '<br>Real:'.$this->parent->real_script;
	echo '<br>Scr:'.$this->parent->script;
}
*/
	$real_str_arr = explode("/",$this->parent->real_script);

		if (($this->parent->script != $this->parent->real_script) && (substr($this->parent->real_script,0,1) == '_' || $real_str_arr[0] == 'events') && ($number_of_rows_returned > 0)){
		/* starts if want to apply default layout enable this and disable next query */
		/*
			$sql = "select distinct 
						wol_all_locations, wol_identifier ,wol_layout_design, 
						wc_identifier, wc_width, wc_label, wctl_position, wctl_rank, wc_layout_type, wc_layout_columns, 
						woic_rank, woic_container, woic_identifier, 
						wo_identifier, wo_type, wo_label,
						wo_command, wo_all_locations, wo_show_label, wo_owner_id, wo_owner_module
					from web_layouts 
						left outer join menu_to_object on mto_object = wol_identifier and mto_client = wol_client and mto_module ='WEBOBJECTS_'
						left outer join menu_data on menu_identifier = mto_menu and mto_client = menu_client
						left outer join web_container_to_layout on wctl_layout = wol_identifier and wctl_client = wol_client
						left outer join web_containers on wc_identifier = wctl_container and wc_client = wctl_client
						inner join web_objects_in_container on woic_container = wc_identifier and woic_client = wc_client
						inner join web_objects on wo_identifier = woic_object and wo_client = woic_client
					where 
						$condition
						wol_client=$this->client_identifier $extra_condition and 
						wol_all_locations=1
						
						$remove_condition 
					order by 
						wol_all_locations, wol_identifier, wctl_position, wctl_rank, woic_rank, wol_layout_design, wc_identifier, wc_label, wc_layout_type, 
						wc_layout_columns, woic_container, wo_identifier, wo_type, wo_label, wo_command, wo_all_locations, wo_show_label";
		*/
		/* ends if want to apply default layout enable this and disable next query */
			$sql = "select distinct 
						wol_all_locations, wol_identifier ,wol_layout_design, 
						wc_identifier, wc_width, wc_label, wctl_position, wctl_rank, wc_layout_type, wc_layout_columns, 
						woic_rank, woic_container, woic_identifier, 
						wo_identifier, wo_type, wo_label,
						wo_command, wo_all_locations, wo_show_label, wo_owner_id, wo_owner_module
					from web_layouts 
						left outer join menu_to_object on mto_object = wol_identifier and mto_client = wol_client and mto_module ='WEBOBJECTS_'
						left outer join menu_data on menu_identifier = mto_menu and mto_client = menu_client
						left outer join web_container_to_layout on wctl_layout = wol_identifier and wctl_client = wol_client
						left outer join web_containers on wc_identifier = wctl_container and wc_client = wctl_client
						inner join web_objects_in_container on woic_container = wc_identifier and woic_client = wc_client
						inner join web_objects on wo_identifier = woic_object and wo_client = woic_client
					where 
						$condition
						wol_client=$this->client_identifier $extra_condition and 
						wol_default=1
						
						$remove_condition 
					order by 
						wol_all_locations, wol_identifier, wctl_position, wctl_rank, woic_rank, wol_layout_design, wc_identifier, wc_label, wc_layout_type, 
						wc_layout_columns, woic_container, wo_identifier, wo_type, wo_label, wo_command, wo_all_locations, wo_show_label";
		}else{
		/* Ends Apply Default Layout to Basket or all System Generated Root level Pages (Added By Muhammad Imran)*/
			$sql = "select distinct 
						wol_all_locations, wol_identifier ,wol_layout_design, 
						wc_identifier, wc_width, wc_label, wctl_position, wctl_rank, wc_layout_type, wc_layout_columns, 
						woic_rank, woic_container, woic_identifier, 
						wo_identifier, wo_type, wo_label,
						wo_command, wo_all_locations, wo_show_label, wo_owner_id, wo_owner_module
					from web_layouts 
						left outer join menu_to_object on mto_object = wol_identifier and mto_client = wol_client and mto_module ='WEBOBJECTS_'
						left outer join menu_data on menu_identifier = mto_menu and mto_client = menu_client
						left outer join web_container_to_layout on wctl_layout = wol_identifier and wctl_client = wol_client
						left outer join web_containers on wc_identifier = wctl_container and wc_client = wctl_client
						inner join web_objects_in_container on woic_container = wc_identifier and woic_client = wc_client
						inner join web_objects on wo_identifier = woic_object and wo_client = woic_client
					where 
						$condition
						wol_client=$this->client_identifier $extra_condition and 
						(wol_all_locations=1 or 
							(wol_all_locations=0 and 
								(mto_menu is not null and menu_url is not null)
							)
						) and (
							menu_url is null or menu_url = '".$this->parent->script."'
						)
						$remove_condition 
					order by 
						wol_all_locations, wol_identifier, wctl_position, wctl_rank, woic_rank, wol_layout_design, wc_identifier, wc_label, wc_layout_type, 
						wc_layout_columns, woic_container, wo_identifier, wo_type, wo_label, wo_command, wo_all_locations, wo_show_label";
		}
		
		//if ($_SERVER['REMOTE_ADDR'] == "202.154.241.147")		
		//	print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql\n\n\n".$this->check_parameters($_SESSION,"displaymode")));
		}
		$page_result = $this->parent->db_pointer->database_query($sql);
		$display_commands = Array();

		$sql ="select display_command from display_data where display_menu = ".$parameters["current_menu_location"]." and display_client=$this->client_identifier";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$displayresult  = $this->parent->db_pointer->database_query($sql);
		$display_commands[count($display_commands)] = "LAYOUTSITE_SHOW_IMAGE";
		$display_commands[count($display_commands)] = "BANNER_DISPLAY";
		while($display_r = $this->parent->db_pointer->database_fetch_array($displayresult)){
			$display_commands[count($display_commands)] = $display_r["display_command"];
		}
		$this->parent->db_pointer->database_free_result($displayresult);
		//print_r($display_commands);
		$out = "";
		$available_commands = "";
		$containers = Array();
		$choosen_layout = -1;

		/*
			Accept only the first layout as the site default and a site location layout will be returned 
			results are ordered by not default first then layout identifier the system should never allow 
			multiple non default to be returned so accespt the first layouts definition and ignore rest.
		*/
		$position_frm = "";
		if ($page_result){
			$count=0;
			while ($r = $this->parent->db_pointer->database_fetch_array($page_result)) {
				$parameters["__layout_position"] = $r["wctl_position"];
				if ($choosen_layout==-1){
					$choosen_layout = $r["wol_identifier"];
					$layout_design_var = $r["wol_layout_design"];
					
				    if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"choosen_layout :: $choosen_layout = ".$r["wol_identifier"]." with layout of ".$r["wol_layout_design"]));}
				}
				//print "<li> layout ".$choosen_layout."  ". $r["wo_command"]. "   wol ".$r["wol_identifier"]."</li> ";				

				/* By Imran for FORM 'Thank you' msg position handling */
				if($layout_design_var == 4)
					$pos_var = "1";
				elseif($layout_design_var == 1111)
					$pos_var = "1,2,3,4";
				elseif($layout_design_var == 31)
					$pos_var = "1,4";
				elseif($layout_design_var == 13)
					$pos_var = "1,2";
				elseif($layout_design_var == 112)
					$pos_var = "1,2,3";
				elseif($layout_design_var == 121)
					$pos_var = "1,2,4";
				elseif($layout_design_var == 211)
					$pos_var = "1,3,4";
				elseif($layout_design_var == 22)
					$pos_var = "1,3";
					
				$pos_var_arr = split(",",$pos_var);
//				print_r($pos_var_arr);
				
				if ($r["wo_command"] == "FORMBUILDER_DISPLAY" && in_array($r["wctl_position"],$pos_var_arr)){
					if ($r["wctl_position"] != 4)
						$position_frm = $r["wctl_position"];
				}


				if ($choosen_layout==$r["wol_identifier"]){
					if (strlen($available_commands)!=0){
						$available_commands .=", ";
					}
					$available_commands .="'".$r["wo_command"]."'";
					$display_command = $r["wo_command"];
					if ($r["wo_type"]==1){
						$display_command = "";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"".$r["wo_command"]."\n".print_r($display_commands,true).""));}
						if ($r["wo_command"]=="PRESENTATION_DISPLAY_PAGE"){
							if (in_Array('PRESENTATION_DISPLAY',$display_commands)){
								$display_command = 'PRESENTATION_DISPLAY';
							} else if (in_Array('PRESENTATION_ATOZ',$display_commands)){
								$display_command = 'PRESENTATION_ATOZ';
							} else if (in_Array('PRESENTATION_ATOZ_ALL',$display_commands)){
								$display_command = 'PRESENTATION_ATOZ_ALL';
							} else if (in_Array('PRESENTATION_SLIDESHOW',$display_commands)){
								$display_command = 'PRESENTATION_SLIDESHOW';
							} else if (in_Array('PRESENTATION_SLIDESHOW_TOPBOTTOM',$display_commands)){
								$display_command = 'PRESENTATION_SLIDESHOW_TOPBOTTOM';
							} else if (in_Array('PRESENTATION_PERSISTANT',$display_commands)){
								$display_command = 'PRESENTATION_PERSISTANT';
							}
						} else if ($r["wo_command"]=="PRESENTATION_LATEST" && in_Array('VEHICLE_LOCATION',$display_commands) && in_Array('INFORMATION_DISPLAY',$display_commands) == false){
								$display_command = 'VEHICLE_LOCATION';								
						}else if ($r["wo_command"]=="INFORMATION_DISPLAY" || $r["wo_command"]=="INFORMATION_ATOZ"){
							if (in_Array('INFORMATION_A2Z',$display_commands)){
								$display_command = 'INFORMATION_A2Z';
							} else {
								if (in_Array('INFORMATION_DISPLAY',$display_commands)){
									$display_command = 'INFORMATION_DISPLAY';
								} else {
									//print $command;
								}
							}
						} else {
							if (in_array($r["wo_command"],$display_commands)){
								$display_command=$r["wo_command"];
							}
							else{
								//print '<li>disp comd '.$r["wo_command"].' </li>';
							}
						}
					}
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_page",__LINE__," execute [".$r["wo_type"]."::".$r["wo_command"]."::".$r["wo_identifier"]."] [$display_command][".print_r($display_commands,true)."]"));
					}
					
					//	print "<li>".__FILE__."@".__LINE__." :: $display_command :: [".$r["wo_type"]."]::[".$r["wc_label"]."][".$r["wo_command"]."]::</li>";
					if ($r["wo_type"]==1){
						if ($display_command!=""){
							if ($this->check_parameters($p,$r["wo_command"],"__NOT_FOUND__")=="__NOT_FOUND__"){
								$p[$r["wo_command"]]	= Array();
								if ($this->module_debug){
									$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"<li> ".$r["wo_command"] . " command = <em>" . $this->check_parameters($parameters,"command") . "</em></li> "));
								}
								if ($r["wo_command"] == "PRESENTATION_DISPLAY_PAGE" && $this->check_parameters($parameters,"command")=="PAGE_PREVIEW_FORM"){
									if ($this->module_debug){
										$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"<li>".__LINE__." <strong>1.0</strong> getting ".$r["wo_command"]."</li> "));
									}
									$p["PAGE_PREVIEW_FORM"] = Array($r["wo_type"], $r["woic_container"], $r["woic_rank"], $r["wctl_position"], $r["wo_identifier"], $r["wc_width"],$r["wo_show_label"], $r["woic_identifier"]);
								} else if ($r["wo_command"] == "PRESENTATION_DISPLAY_PAGE" && $this->check_parameters($parameters,"command")=="PAGE_PREVIEW"){
									if ($this->module_debug){
										$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"<li>".__LINE__." <strong>1.1</strong> getting ".$r["wo_command"]."</li> "));
									}
									$p["PAGE_PREVIEW"] = Array($r["wo_type"], $r["woic_container"], $r["woic_rank"], $r["wctl_position"], $r["wo_identifier"], $r["wc_width"],$r["wo_show_label"], $r["woic_identifier"]);
								} else if ($r["wo_command"] == "PRESENTATION_DISPLAY_PAGE" && $this->check_parameters($parameters,"command")=="PAGE_PREVIEW"){
									if ($this->module_debug){
										$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"<li>".__LINE__." <strong>1.1</strong> getting ".$r["wo_command"]."</li> "));
									}
									$p["PAGE_PREVIEW"] = Array($r["wo_type"], $r["woic_container"], $r["woic_rank"], $r["wctl_position"], $r["wo_identifier"], $r["wc_width"],$r["wo_show_label"], $r["woic_identifier"]);
								} else if ($r["wo_command"] == "PRESENTATION_ATOZ" || $r["wo_command"] == "PRESENTATION_ATOZ_ALL" ){
									if ($this->module_debug){
										$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"<li>".__LINE__." <strong>1.1</strong> getting ".$r["wo_command"]."</li> "));
									}
									$p["PRESENTATION_DISPLAY"] = Array($r["wo_type"], $r["woic_container"], $r["woic_rank"], $r["wctl_position"], $r["wo_identifier"], $r["wc_width"],$r["wo_show_label"], $r["woic_identifier"]);
								} else if ($r["wo_command"] == "IMAGEROTATOR_DISPLAY" && $this->check_parameters($parameters,"command")=="IMAGEROTATOR_DISPLAY_PREVIEW"){
									if ($this->module_debug){
										$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"<li>".__LINE__." <strong>2</strong> getting ".$r["wo_command"]."</li> "));
									}
									$p["IMAGEROTATOR_DISPLAY_PREVIEW"] = Array($r["wo_type"], $r["woic_container"], $r["woic_rank"], $r["wctl_position"], $r["wo_identifier"], $r["wc_width"],$r["wo_show_label"], $r["woic_identifier"]);
								} else {
									if ($this->module_debug){
										$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"<li>".__LINE__." <strong>3.</strong> get ".$r["wo_command"]."</li> "));
									}
									$p[$r["wo_command"]][count($p[$r["wo_command"]])]	= Array($r["wo_type"], $r["woic_container"], $r["woic_rank"], $r["wctl_position"], $r["wo_identifier"], $r["wc_width"],$r["wo_show_label"], $r["woic_identifier"], $this->check_parameters($r,"wo_owner_id",-1));
								}
							} else {
								$p[$r["wo_command"]][count($p[$r["wo_command"]])]	= Array($r["wo_type"], $r["woic_container"], $r["woic_rank"], $r["wctl_position"], $r["wo_identifier"], $r["wc_width"],$r["wo_show_label"], $r["woic_identifier"], $this->check_parameters($r,"wo_owner_id",-1));
							}
						}
					}
					if($this->check_parameters($containers,$r["wc_identifier"],"__NOT_FOUND__") == "__NOT_FOUND__"){
						$containers[$r["wc_identifier"]] = Array();
						if($merge3to2==1 && $r["wctl_position"]==3){
							$containers[$r["wc_identifier"]]["start"] = "<container identifier='".$r["wc_identifier"]."' rank='".$r["wctl_rank"]."' pos='2' layouttype='".$r["wc_layout_type"]."' columns='".$r["wc_layout_columns"]."' width='".$this->check_parameters($r,"wc_width","100%")."'>";
						} else {
							$containers[$r["wc_identifier"]]["start"] = "<container identifier='".$r["wc_identifier"]."' rank='".$r["wctl_rank"]."' pos='".$r["wctl_position"]."' layouttype='".$r["wc_layout_type"]."' columns='".$r["wc_layout_columns"]."' width='".$this->check_parameters($r,"wc_width","100%")."'>";
						}
						$containers[$r["wc_identifier"]]["content"] = Array();
						$containers[$r["wc_identifier"]]["end"] = "</container>";
					}
					$webobject_properties = $this->call_command("WEBOBJECTS_GET_PROPERTIES",Array("identifier"=>$r["woic_identifier"]));
					if ($r["wo_type"]==0){
						if($merge3to2==1 && $r["wctl_position"]==3){
							$val = $this->call_command("WEBOBJECTS_EXTRACT",Array("identifier" => $r["wo_identifier"], "wo_owner_id" => $r["wo_owner_id"], "web_container" => $r["woic_container"], "position"=>2 ,"command"=> $this->check_parameters($parameters,"command")));
						} else {
							$val = $this->call_command("WEBOBJECTS_EXTRACT",Array("identifier" => $r["wo_identifier"], "wo_owner_id" => $r["wo_owner_id"], "web_container" => $r["woic_container"], "position"=>$r["wctl_position"] ,"command"=> $this->check_parameters($parameters,"command")));
						}
						if($val != ""){
							$containers[$r["woic_container"]]["content"][count($containers[$r["woic_container"]]["content"])] = Array($r["woic_rank"] , "<webobject identifier=\"".$r["wo_identifier"]."\" display_label=\"".$r["wo_show_label"]."\" container=\"". $r["woic_container"] ."\" pos=\"".$r["wctl_position"]."\" rank=\"".$r["woic_rank"]."\" type=\"".$r["wo_type"]."\">$webobject_properties".$val."</webobject>");
						}
					}
					if ($r["wo_type"]==2){
							if($r["wo_owner_id"]==0){
								$val = "<uid>".md5(uniqid(rand(), true))."</uid><label>".$r["wo_label"]."</label><command><![CDATA[".$r["wo_command"]."]]></command>";
								$containers[$r["woic_container"]]["content"][count($containers[$r["woic_container"]]["content"])] = Array($r["woic_rank"] , "<webobject identifier=\"".$r["wo_identifier"]."\" display_label=\"".$r["wo_show_label"]."\" container=\"". $r["woic_container"] ."\" pos=\"".$r["wctl_position"]."\" rank=\"".$r["woic_rank"]."\" type=\"".$r["wo_type"]."\">$webobject_properties".$val."</webobject>");
							} else {
								$parameters["wo_owner_id"]		= $r["wo_owner_id"];
								$parameters["web_container"]	= $r["woic_container"];
								$parameters["menu_url"]			= $r["wo_owner_module"];
								$parameters["atoz_label"]		= $r["wo_label"];
								
								/* Get Newsletter at client side ( Added By Muhammad Imran Mirza ) */
								if ($r["wo_command"] == "DISPLAY_SUBSCRIPTION_FORM")
									$val = $this->call_command("NEWSLETTER_".$r["wo_command"], $parameters);
								else
									$val = $this->call_command($r["wo_command"], $parameters);
								/* Get Newsletter at client side ( Added By Muhammad Imran Mirza ) */

								if ($val!="")
									$containers[$r["woic_container"]]["content"][count($containers[$r["woic_container"]]["content"])] = Array($r["woic_rank"] , "<webobject identifier=\"".$r["wo_identifier"]."\" display_label=\"".$r["wo_show_label"]."\" container=\"". $r["woic_container"] ."\" pos=\"".$r["wctl_position"]."\" rank=\"".$r["woic_rank"]."\" type=\"".$r["wo_type"]."\">$webobject_properties".$val."</webobject>");
							}
					}
					
					if ($r["wo_type"]==1){
						//print "<li>".$r["woic_container"]."  ".$display_command ."</li>";																																														
						//print "<li>$display_command</li>";						
						if ($display_command != ""){
							$parameters["wo_owner_id"] = $r["wo_owner_id"];
							$parameters["web_container"] = $r["woic_container"];							
							$val = $this->call_command($display_command, $parameters);							
							if ($this->module_debug){
								$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_page",__LINE__," execute [".$r["wo_type"]."::$display_command::".strlen($val)."]"));
							}
							if ($val!="")
								$containers[$r["woic_container"]]["content"][count($containers[$r["woic_container"]]["content"])] = Array($r["woic_rank"] , "<webobject identifier=\"".$r["wo_identifier"]."\" display_label=\"".$r["wo_show_label"]."\" container=\"". $r["woic_container"] ."\" pos=\"".$r["wctl_position"]."\" rank=\"".$r["woic_rank"]."\" type=\"".$r["wo_type"]."\">$webobject_properties".$val."</webobject>");
						}
					}
				}
			}
		}
		$this->parent->db_pointer->database_free_result($page_result);
		$t 					= $this->check_parameters($parameters,"override_script",$this->check_parameters($parameters,"fake_uri"));
		if ($t==""){
			$t = $this->parent->script;
		}
		$client = $this->client_identifier;
		$ok_first_check = 1;
		$ignore_cmds = $this->check_parameters($parameters,"ignore_commands",Array());
		for($index=0,$max=count($ignore_cmds);$index<$max;$index++){
			if ($this->check_parameters($ignore_cmds,$index)=="MIRROR_PAGE_HAS"){
				$ok_first_check=0;
			}
		}
		$ok_second_check =0; // was zero
		foreach($p as $key => $val){
			if ($key=="MIRROR_RETRIEVE"){
				$ok_second_check=0;
			}
		}
		if (($ok_first_check==1) && ($ok_second_check==1)){
			$index = $this->check_parameters($p,"MIRROR_RETRIEVE",Array());
			$wo_identifier	= $this->check_parameters($index,4,"_NA_");
			$wo_container	= $this->check_parameters($index,1,"_NA_");
			$wo_pos 		= $this->check_parameters($index,3,"_NA_");
			$wo_rank		= $this->check_parameters($index,2,"_NA_");
			$wo_type 		= $this->check_parameters($index,0,"_NA_");
			$woic_identifier= $this->check_parameters($index,7,"_NA_");
			if($this->check_parameters($containers,$wo_container,"__NOT_FOUND__") == "__NOT_FOUND__"){
				$containers[$wo_container] = Array();
				$containers[$wo_container]["start"] ="<container identifier='".$r["wc_identifier"]."' rank='".$r["wctl_rank"]."' pos='".$r["wctl_position"]."' layouttype='".$r["wc_layout_type"]."' columns='".$r["wc_layout_columns"]."'>";
				$containers[$wo_container]["content"] =Array();
				$containers[$wo_container]["end"] ="</container>";
			}
			$webobject_properties = $this->call_command("WEBOBJECTS_GET_PROPERTIES",Array("identifier"=>$woic_identifier));
			$val = $this->call_command("MIRROR_PAGE_HAS",$parameters);
			if($val!=""){
				$containers[$wo_container]["content"][count($containers[$wo_container]["content"])] = Array($wo_rank , "<webobject  identifier='".$wo_identifier."' display_label=\"0\" container=\"". $wo_container."\" pos=\"$wo_pos\" rank=\"$wo_rank\" type=\"$wo_type\">$webobject_properties".$val."</webobject>");
			}
		}
		if ($this->check_parameters($parameters,"command")!="LAYOUTSITE_GET_PAGE"){
		
			/* Get Default Position of Container Portion Starts (Added by Muhammad Imran Mirza) */
				/************
				 Set the position from Preferences > General Settings > System Settings 
				 ************/
			$sql_pos = "select system_preference_value
					from system_preferences 
					where system_preference_client=$this->client_identifier 
					and system_preference_name='sp_default_position' 
					and system_preference_module='SYSPREFS_'";
			//	print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
	
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql_pos\n\n\n".$this->check_parameters($_SESSION,"displaymode")));
			}
			$result_pos = $this->parent->db_pointer->database_query($sql_pos);
			$r_pos = $this->parent->db_pointer->database_fetch_array($result_pos);
			$default_pos_str = $r_pos["system_preference_value"];
			if ($default_pos_str == 'Position1')
				$default_pos = 1;
			elseif ($default_pos_str == 'Position2')
				$default_pos = 2;
			elseif ($default_pos_str == 'Position3')
				$default_pos = 3;
			elseif ($default_pos_str == 'Position4')
				$default_pos = 4;
			else
				$default_pos = 2;#if position is not set the default is 2
			/* Get Default Position of Container Portion Ends (Added by Muhammad Imran Mirza) */

			$index	= $this->check_parameters($p, $this->check_parameters($parameters,"command"), Array());
			/*
				Adrian you need this line 
			*/
			//if (!in_array($this->check_parameters($parameters,"command"),$display_commands)){
				/** IF $index is mutidimensional and command is not FORMBUILDER_DISPLAY COMMAND then step into otherwise FORMBUILDER_DISPLAY needs its separate container.  */
				//if(is_array($this->check_parameters($index,0,"")) && ($this->check_parameters($parameters,"command") != "FORMBUILDER_DISPLAY")){
				if(is_array($this->check_parameters($index,0,""))){				
					for($z=0; $z<count($index);$z++){
						$wo_type 		= $this->check_parameters($index[$z],0,1);
						$wo_container	= $this->check_parameters($index[$z],1,"unique_".uniqid(time()));
						$wo_rank		= $this->check_parameters($index[$z],2,0);
						
						//$wo_pos 		= $this->check_parameters($index[$z],3,2);
						if ($position_frm == "")
							$wo_pos 		= $this->check_parameters($index[$z],3,$default_pos);
						else
							$wo_pos 		= $this->check_parameters($index[$z],3,$position_frm);
						
						$wo_identifier	= $this->check_parameters($index[$z],4,"_NA_");
						$wo_width		= $this->check_parameters($index[$z],5,"_NA_");
						$wo_label		= $this->check_parameters($index[$z],6,"_NA_");
						$woic_identifier= $this->check_parameters($index[$z],7,"_NA_");
						$wo_owner_id	= $this->check_parameters($index[$z],8,-1);
						/** CHECK IF COMMAND IS FOR FORMBUILDER MODULE THEN wo_owner_id should be removed to get value of $identifier */
						if ($this->check_parameters($parameters,"command") == "FORMBUILDER_DISPLAY"){
							
							$wo_owner_id = -1;
							$wo_container="unique_".uniqid(time());

						}
						$parameters["wo_owner_id"] = $wo_owner_id;
						if($this->check_parameters($containers, $wo_container, "__NOT_FOUND__") == "__NOT_FOUND__"){
							$containers[$wo_container] = Array();
							$rank_unspecified++;
//							$containers[$wo_container]["start"] ="<container identifier='".$wo_container."' rank='$rank_unspecified' pos='2' layouttype='0' columns='1'>";
/** To hide position 2 and show content on database system position changed pos=2 into pos=$wo_pos (Modified by M.Imran Mirza) **/
							$containers[$wo_container]["start"] ="<container identifier='".$wo_container."' rank='$rank_unspecified' pos='$wo_pos' layouttype='0' columns='1'>";
							$containers[$wo_container]["content"] =Array();
							$containers[$wo_container]["end"] ="</container>";
						}
						$webobject_properties = $this->call_command("WEBOBJECTS_GET_PROPERTIES",Array("identifier"=>$woic_identifier));
						
						$val = $this->call_command($this->check_parameters($parameters,"command"),$parameters);
						if($val!=""){
							$containers[$wo_container]["content"][count($containers[$wo_container]["content"])] = Array($wo_rank , "<webobject identifier=\"$wo_identifier\" display_label=\"0\" container=\"". $wo_container."\" pos=\"$wo_pos\" rank=\"".$wo_rank."\" type=\"1\">$webobject_properties".$val."</webobject>");
						}
					}					
				} else {
					$wo_type 		= $this->check_parameters($index,0,1);
					$wo_container	= $this->check_parameters($index,1,"unique_".uniqid(time()));
					$wo_rank		= $this->check_parameters($index,2,0);
					
					//$wo_pos 		= $this->check_parameters($index,3,2);
					if ($position_frm == "")
						$wo_pos 		= $this->check_parameters($index,3,$default_pos);
					else
						$wo_pos 		= $this->check_parameters($index,3,$position_frm);

					$wo_identifier	= $this->check_parameters($index,4,"_NA_");
					$wo_width		= $this->check_parameters($index,5,"_NA_");
					$wo_label		= $this->check_parameters($index,6,"_NA_");
					$woic_identifier= $this->check_parameters($index,7,"_NA_");
					$wo_owner_id	= $this->check_parameters($index,8,-1);
					$parameters["wo_owner_id"] = $wo_owner_id;
					if($this->check_parameters($containers, $wo_container, "__NOT_FOUND__") == "__NOT_FOUND__"){
						$containers[$wo_container] = Array();
						$rank_unspecified++;
//						$containers[$wo_container]["start"] ="<container identifier='".$wo_container."' rank='$rank_unspecified' pos='2' layouttype='0' columns='1'>";
/** To hide position 2 and show content on database system position changed pos=2 into pos=$wo_pos (Modified by M.Imran Mirza) **/
						$containers[$wo_container]["start"] ="<container identifier='".$wo_container."' rank='$rank_unspecified' pos='$wo_pos' layouttype='0' columns='1'>";
						$containers[$wo_container]["content"] =Array();
						$containers[$wo_container]["end"] ="</container>";
					}
					$webobject_properties = $this->call_command("WEBOBJECTS_GET_PROPERTIES",Array("identifier"=>$woic_identifier));
					$val = $this->call_command($this->check_parameters($parameters,"command"),$parameters);
					if($val!=""){
						$containers[$wo_container]["content"][count($containers[$wo_container]["content"])] = Array($wo_rank , "<webobject identifier=\"$wo_identifier\" display_label=\"0\" container=\"". $wo_container."\" pos=\"$wo_pos\" rank=\"".$wo_rank."\" type=\"1\">$webobject_properties".$val."</webobject>");
					}
				}
				
		//	}
		}
		foreach ($containers as $key=>$val){
			$l = count($containers[$key]["content"]);
			if ($l>0){
				$out .= $containers[$key]["start"];
				usort($containers[$key]["content"],"mywebObjSort");
				for($i=0;$i<$l;$i++){
					$out .= $containers[$key]["content"][$i][1];
				}
				$out .= $containers[$key]["end"];
			}
		}
		if ($debug) $out;
		if ($debug) $this->exitprogram();
		$this->parent->script = $pscript;
		return $out;
	}
	
	function cache_menu_structure(){
		return $this->call_command("LAYOUT_WEB_MENU");
	}

	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Load the menu structure into the array only if the array is empty ie do not recreate the 
	- menu structure. bar making the code faster this will reduce the possibilities of load twice
	- the information into the array.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function load_menu(){
		if (count($this->menu_structure)==0){

			$this->menu_structure = array();
			$sql_menus = "select 
				menu_data.*, menu_sort.*, relate_menu_groups.group_identifier, display_data.display_command
			from menu_data 
				inner join menu_sort on menu_data.menu_sort = menu_sort.menu_sort_identifier 
				left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier
				left outer join display_data on display_data.display_menu = menu_data.menu_identifier
			where 
				menu_client=$this->client_identifier
			order by 
				menu_data.menu_parent, 
				menu_data.menu_order, 
				menu_data.menu_identifier, 
				relate_menu_groups.group_identifier, 
				display_data.display_command";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_menu",__LINE__,"$sql_menus"));
			}
			$menu_result = $this->parent->db_pointer->database_query($sql_menus);
			$prev_menu=-1;
			$pos=-1;
			while ($r = $this->parent->db_pointer->database_fetch_array($menu_result)) {
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_menu",__LINE__,"Adding entries to arrays. index=".count($this->menu_structure)));
				}
				if ($prev_menu!=$r["menu_identifier"]){
					$prev_menu=$r["menu_identifier"];
					$pos =count($this->menu_structure);
					$this->menu_structure[$pos]=array(
						"LABEL" 			=> htmlentities($this->convert_amps(join("[[copy]]",split("&amp;#169;",$r["menu_label"])))),
						"ORDER" 			=> $r["menu_order"],
						"URL" 				=> $this->convert_amps($r["menu_url"]),
						"IDENTIFIER"		=> $r["menu_identifier"],
						"PARENT" 			=> $r["menu_parent"],
						"SORT" 				=> $r["menu_sort_tag_value"],
						"CHILDREN" 			=> 0,
						"SIBLINGS" 			=> 0,
						"THEME" 			=> $r["menu_theme"],
						"STYLESHEET"		=> $r["menu_stylesheet"],
						"GROUPS" 			=> Array(),
						"DISPLAY_OPTIONS"	=> Array(),
						"DIRECTORY"			=> $r["menu_directory"],
						"HIDDEN"			=> $this->check_parameters($r,"menu_hidden",0),
						"ALT_TEXT"			=> $this->check_parameters($r,"menu_alt_text",""),
						"IMAGES"			=> $this->check_parameters($r,"menu_images",""),
						"IMAGE_INHERIT"		=> $this->check_parameters($r,"menu_image_inherit","")
					);
					$prev_group=-1;
					$prev_cmd=-1;
				}
				$g_id = $this->check_parameters($r,"group_identifier");
				if ($prev_group!=$g_id){
					$this->menu_structure[$pos]["GROUPS"][count($this->menu_structure[$pos]["GROUPS"])]=$g_id;
					$prev_group=$g_id;
				}
				$dis_cmd = $this->check_parameters($r,"display_command");
				if ($prev_cmd!=$dis_cmd){
					$this->menu_structure[$pos]["DISPLAY_OPTIONS"][count($this->menu_structure[$pos]["DISPLAY_OPTIONS"])]=$dis_cmd;
					$prev_cmd=$dis_cmd;
				}
				
			}
			$this->parent->db_pointer->database_free_result($menu_result);
			$length_of_array = count($this->menu_structure);
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_menu",__LINE__,"number of entries =".$length_of_array));
			}
			for($index=0;$index<$length_of_array;$index++){
				$this->menu_structure[$index]["CHILDREN"]=0;
				$this->menu_structure[$index]["SIBLINGS"]=0;
				for($second_index=0;$second_index<$length_of_array;$second_index++){
					if ($this->menu_structure[$second_index]["PARENT"]==$this->menu_structure[$index]["IDENTIFIER"]){
						$this->menu_structure[$index]["CHILDREN"]++;
					}
					if (($this->menu_structure[$second_index]["PARENT"]==$this->menu_structure[$index]["PARENT"])&&($this->menu_structure[$second_index]["IDENTIFIER"]!=$this->menu_structure[$index]["IDENTIFIER"])){
						$this->menu_structure[$index]["SIBLINGS"]++;
					}
				}
			}		
		}
		return $this->menu_structure;
	}
	
	function have_access($parameters){
//		print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameters,true)."</p></li>";
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$script = $this->check_parameters($parameters,"fake_uri");
		if($script==""){
			$script = $this->parent->script;
		}
		if($script=="index.php"){
			return 1;
		}
		$max_grps = count($grp_info);
		$gps = Array();
		$check = "DEFINED";
		if ($max_grps==0){
			$group_identifier_list = " is null";
			$check = "NULL";
		} else if ($max_grps==1){
			$group_identifier_list = " = ".$grp_info[0]["IDENTIFIER"]." or relate_menu_groups.group_identifier is null";
			$gps = Array($grp_info[0]["IDENTIFIER"],"__NOT_FOUND__");
		} else {
			$group_identifier_list = " in (";
			$gps[0] = "__NOT_FOUND__"; // set null for check 
			for($i=0;$i < $max_grps; $i++){
				if ($i>0){
					$group_identifier_list .= ",";
				}
				$group_identifier_list .= $grp_info[$i]["IDENTIFIER"];
				$gps[count($gps)] = $grp_info[$i]["IDENTIFIER"];
			}
			$group_identifier_list .= ") or relate_menu_groups.group_identifier is null";
		}
		$sdir = dirname($script);
		$adir = Array();
		if(strpos($sdir,"/")===false){
			$menu = "menu_url='".$script."' and ";
		} else {
			$list = split("/",$sdir);
			$menu ="(";
			$l = count($list);
			for($i=0;$i<$l;$i++){
				$adir[$i]="";
				if($i!=0){
					$menu .=" or ";
				}
				$menu .=" menu_url = '";
				for($z=0;$z<($l-$i);$z++){
					$menu .=$list[$z]."/";
					$adir[$i].=$list[$z]."/";
				}
				$menu .="index.php'";
				$adir[$i].="index.php'";
			}
			$menu .=") and ";
		}
		
		$sql ="select menu_data.*,  group_identifier  from menu_data 
					left outer join relate_menu_groups on menu_data.menu_identifier = relate_menu_groups.menu_identifier
				where 
					$menu
					menu_client=$this->client_identifier
					order by menu_parent";
		$result = $this->parent->db_pointer->database_query($sql);
		if ($check=="NULL"){
			$ok = 1;	// true inles not null
		} else {
			$ok = 0;	// prove true (member of group or is null
		}
		$aMenu=Array();
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if($check=="NULL" && $this->check_parameters($r,"group_identifier","__NOT_FOUND__") != "__NOT_FOUND__"){
				$ok = 0;
			}
			if($check=="DEFINED"){
				if(empty($aMenu[$r["menu_parent"]])){
					$aMenu[$r["menu_parent"]] = Array(
						"identifier"=>$r["menu_identifier"],
						"url"=>$r["menu_url"],
						"group"=>Array()
					);
				} 
				$aMenu[$r["menu_parent"]]["group"][count($aMenu[$r["menu_parent"]]["group"])] = $this->check_parameters($r,"group_identifier","__NOT_FOUND__");
			}
        }
		if($check=="DEFINED"){
			$ok = 1; // currently logged in and user is member of group
			$parent = -1;
			$ok = $this->cgrp($gps, $aMenu, -1, $script);
		}
        $this->parent->db_pointer->database_free_result($result);
		return $ok;
	}
	function cgrp($g, $m, $p=-1, $url="index.php"){
		$r_ok = 0;
		if(!empty($m[$p])){
			for($x=0; $x<count($g); $x++){
				if(in_array($g[$x], $m[$p]["group"])){
					$r_ok= 1;
					$id = $m[$p]["identifier"];
					if(!empty($m[$id])){
						$r_ok = $this->cgrp($g,$m,$id,$url);
					}
				}
			}
		}
		return $r_ok;
	}
	function get_menu_images($parameters){
		$out="";
		$wc			= $this->check_parameters($parameters,"web_container");
		$mid		= $this->check_parameters($parameters,"current_menu_location");
		$terminateOn= $this->check_parameters($parameters,"terminateOn",0);
		$sql		= "select * from file_to_object 
							inner join file_info on fto_file = file_identifier and fto_client = file_client 
						where fto_client = $this->client_identifier and fto_module='LAYOUT_$wc' and fto_object = $mid";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
	    $result		= $this->parent->db_pointer->database_query($sql);
        $number_of_rows_returned = $this->call_command("DB_NUM_ROWS",Array($result));
		if($number_of_rows_returned>0){
			while($r	= $this->parent->db_pointer->database_fetch_array($result)){
				$file_id		 = $r["file_identifier"];
				$lab = $r["fto_title"];
				if ($lab==""){
					$lab = $r["file_label"];
				}
				$file_label		 = $this->convert_amps($lab);
				$file_name		 = $this->convert_amps($r["file_name"]);
				$file_mime		 = $r["file_mime"];
				$file_size		 = $r["file_size"];
				$file_directory	 = $this->retrieve_directory_path($r["file_directory"]);
	//$file_directory = $r["file_directory"];
				$file_width		 = $this->check_parameters($r,"file_width");
				$file_height	 = $this->check_parameters($r,"file_height");
				$file_date		 = $this->check_parameters($r,"file_creation_date");
				$file_description= $this->split_me($this->split_me($this->split_me($r["file_description"],'"',""),"\r",""),"\n","");
				$file_md5		 = $r["file_md5_tag"];
				$file_dl_sec	 = $this->check_parameters($r,"file_dl_sec");
				//". join("", split("&quot;",$file_description) ) ."
				$out		.= "<file identifier=\"$file_id\">
					<url><![CDATA[".$r["file_name"]."]]></url>
					<label><![CDATA[".join("",split("\&quot;",$this->split_me($file_label,"\"", "\\\"")))."]]></label>
					<md5><![CDATA[$file_md5]]></md5>
					<name><![CDATA[$file_name]]></name>
					<mime><![CDATA[$file_mime]]></mime>
					<description><![CDATA[". join("", split("&quot;",$file_description) ) ."]]></description>
					<directory><![CDATA[$file_directory]]></directory>
					<size><![CDATA[$file_size]]></size>
					<width><![CDATA[$file_width]]></width>
					<height><![CDATA[$file_height]]></height>
					<date><![CDATA[$file_date]]></date>
				</file>\n";
	    	}
		    $this->parent->db_pointer->database_free_result($result);
		} else {
			if(count($this->menu_structure)>0){
				$this->load_menu();
			}
			$max_count= count($this->menu_structure);
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"max_count",__LINE__,"$max_count"));}
			$search = $mid;
//			print "<li>$search</li>";
				for($i=$max_count-1;$i>=0;$i--){
					if($this->menu_structure[$i]["IDENTIFIER"]==$search){
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"found menu",__LINE__,"$search (".print_r($this->menu_structure[$i],true).")"));}
						if ($this->menu_structure[$i]["IMAGES"]==1){
							if ($this->menu_structure[$i]["IMAGE_INHERIT"]==1){
								$search = $this->menu_structure[$i]["PARENT"];
							} else {
//								return "";
								$search=-1;
							}
						} else {
//							return "";
							$search=-1;
						}
					}
				}
				if($search == $mid){
					$exitLoop = true;
				}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"found menu",__LINE__,"$search ()"));}
//			print_r($this->menu_structure);
//			$this->exitprogram();
//return "";
			if($search!=-1){
				return $this->get_menu_images(Array("web_container"=>$wc, "current_menu_location"=>$search, "terminateOn"=>1));
			} else {
				return "";
			}
		}
		return "<module name='layoutimage' display='image'>$out</module>";
	}
	
	function get_directories($can_upload=-1){
		$where="";
		$sql = "select * from directory_data where directory_client = $this->client_identifier $where order by directory_parent, directory_name";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get_directories",__LINE__,"$sql"));
		}
		$dir_result = $this->parent->db_pointer->database_query($sql);
		if ($dir_result){
			while ($r = $this->parent->db_pointer->database_fetch_array($dir_result)) {
				$this->directories[count($this->directories)]= array(
				"IDENTIFIER" 	=> $r["directory_identifier"],
				"CLIENT" 		=> $r["directory_client"],
				"PARENT" 		=> $r["directory_parent"],
				"NAME" 			=> $r["directory_name"],
				"CAN_UPLOAD"	=> $r["directory_can_upload"],
				"CAN_SPIDER"	=> $r["directory_can_spider"]
				);
			}
			$this->parent->db_pointer->database_free_result($dir_result);
			return 1;
		} else {
			return 0;
		}
	}

}
	function mywebObjSort($a,$b){
		if ($a[0]==$b[0]){
			return 0;
		} else {
			return ($a[0] < $b[0]) ? -1 : 1; 
		}
	}

?>