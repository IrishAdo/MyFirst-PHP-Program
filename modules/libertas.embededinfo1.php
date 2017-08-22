<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.embededinfo.php
* @date 03 Dec 2002
*/
/**
* 
*/
class embededinfo extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_REPORTS";
	var $module_name				= "embededinfo";
	var $module_name_label			= "Embedded Information Manager Module (Administration)";
	var $module_admin				= "1";
	var $module_channels			= Array();
	var $searched					= 0;
	var $module_modify	 		= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.6 $';
	var $module_command				= "EMBED_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_EMBEDED_INFO";
	/**
	*  Management Menu entries
	*/
	var $module_admin_options 		= array(
		array("EMBED_LIST_BROKEN", "MANAGE_BROKEN_LINKS","")
	);
	
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	var $module_admin_user_access = array(
		array("EMBED_ALL",			"COMPLETE_ACCESS")
	);
	/**
	*  Channel options
	*/
	var $module_display_options 	= array();
	
	/**
	*  filter options
	*/
	var $display_options			= array();
	/**
	*  Class Methods
	*/
	
	function command($user_command, $parameter_list=array()){
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
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($this->module_admin_access==1){
				if ($user_command==$this->module_command."MANAGE_LINKS"){
					return $this->manage_links($parameter_list);
				}
				if ($user_command==$this->module_command."UPDATE_INFO"){
					return $this->update_embed($parameter_list);
				}
				if ($user_command==$this->module_command."CHECK_LOCATION"){
					return $this->check_location($parameter_list);
				}
				if ($user_command==$this->module_command."EXTRACT_INFO"){
					return $this->extract_embedded_information($parameter_list);
				}
				if ($user_command==$this->module_command."SAVE_INFO"){
					return $this->save_extracted_info($parameter_list);
				}
				if ($user_command==$this->module_command."COPY_INFO"){
					return $this->copy_embedded($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE_INFO"){
					return $this->remove_embeded_info($parameter_list);
				}
				if ($user_command==$this->module_command."REQUEST_UPDATE"){
					return $this->request_page_update($parameter_list);
				}
				if ($user_command==$this->module_command."SAVE_UPDATE_REQUEST"){
					return $this->submit_page_update_request($parameter_list);
				}
				if ($user_command==$this->module_command."FIX"){
					return $this->find_and_fix($parameter_list);
				}
				if ($user_command==$this->module_command."FIX_MENU"){
					return $this->fix_menu($parameter_list);
				}
				if ($user_command==$this->module_command."FIX_PAGES"){
					return $this->fix_pages($parameter_list);
				}
				if ($user_command==$this->module_command."LIST_BROKEN"){
					return $this->list_broken($parameter_list);
				}
				if ($user_command==$this->module_command."DELETE_ALL_CLIENT_MODULE"){
					$this->delete_module($parameter_list);
				}
				if ($user_command==$this->module_command."DEBUGTEST"){
					$this->debugtest();
				}
			}
		}else{
			// wrong command sent to system
			return "";
		}
	}
	function list_available_fields(){}
	/**
	* Initialise function
	-----------------------
	- This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	- module::initialise() function allowing you to define any extra constructor
	- functionality.
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
		* define if access is allowed 
		*/
		if (($this->parent->module_type=="admin") || ($this->parent->module_type=="view_comments")||($this->parent->module_type=="preview")||($this->parent->module_type=="files")){
			$this->module_admin_access=1;
		}
		return 1;
	}
	
	function create_table(){
		$tables = array();

		/**
		* Table structure for table 'embed_libertas_form'
		*/
		
		$fields = array(
			array("embed_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
			array("trans_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("client_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("form_int_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("form_str_identifier"		,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("module_starter"			,"varchar(50)"		,"NOT NULL"	,"default ''"),
			array("editor"					,"varchar(255)"		,"NOT NULL"	,"default ''")
		);
		
		$primary ="embed_identifier";
		$tables[count($tables)] = array("embed_libertas_form", $fields, $primary);
		/**
		* Table structure for table 'embed_libertas_image'
		*/
		
		$fields = array(
			array("embed_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
			array("client_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("trans_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("image_tag"				,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("module_starter"			,"varchar(50)"		,"NOT NULL"	,"default ''")
		);
		
		$primary ="embed_identifier";
		$tables[count($tables)] = array("embed_libertas_image", $fields, $primary);
		/**
		* Table structure for table 'embed_libertas_file'
		*/
		
		$fields = array(
			array("embed_identifier"		,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
			array("client_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("trans_identifier"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("file_tag"				,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("module_starter"			,"varchar(50)"		,"NOT NULL"	,"default ''")
		);
		$primary ="embed_identifier";
		$tables[count($tables)] = array("embed_libertas_file", $fields, $primary);
		/**
		* Table structure for table 'embed_libertas_link'
		*/
		$fields = array(
			array("embed_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("client_identifier"	,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("menu_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("src_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("dst_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("module_starter"		,"varchar(50)"				,"NOT NULL"	,"default ''"),
			array("editor"				,"varchar(50)"				,"NOT NULL"	,"default ''"),
			array("destination_url"		,"text"						,"NOT NULL"	,"default ''"),
			array("broken" 				,"unsigned small integer"	,""			,"default '0'")
		);
		$primary ="embed_identifier";
		$tables[count($tables)] = array("embed_libertas_link", $fields, $primary);
		


		return $tables;
	}
	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* this function extracts information from the page content that information comes in 
		* several flavours the following are the types of information returned by the system
		* ie these are the keys tot he array's of information found.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* libertas_form
		* libertas_form_int
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* libertas_files
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* libertas_image
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* libertas_links_trans
		* libertas_links_menu
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function extract_embedded_information($parameters){
		$str = html_entity_decode(html_entity_decode(html_entity_decode($this->check_parameters($parameters,"str"))));
		$str = html_entity_decode(str_replace(
		Array(
			"src = \"",
			"href = \"",
			"src= \"",
			"href= \"",
			"src =\n\"",
			"href =\n\"",
			"src=\n\"",
			"href=\n\"",
			"src\n=\n\"",
			"href\n=\n\""
		), 
		Array(
			"src=\"",
			"href=\"",
			"src=\"",
			"href=\"",
			"src=\"",
			"href=\"",
			"src=\"",
			"href=\"",
			"src=\"",
			"href=\""
		),
		$this->validate($str)));
		$check = $str;
//		print $check;
		$results = array();
		$found=true;
		while($found){
			$pos = strpos(strtolower($check), '<img id="libertas_form"');
			if ($pos===false){
				$found=false;
			} else {
				$end = strpos($check,'>', $pos)+1;
				$rest= substr($check, $end);
				$tag = substr($check, $pos, $end-$pos);
				$tag_start = strpos(strtolower($tag), 'frm_identifier="');
				$tag_end  = strpos($tag, '"', $tag_start+16);
				$identifier = substr($tag, $tag_start+16, $tag_end -($tag_start+16));
				if (strpos($identifier,"libertas_form_")===false){
					$id = "";
				} else {
					$id = substr($identifier,strlen("libertas_form_"));
				}
				if ($this->check_parameters($results,"libertas_form","__EMPTY__")=="__EMPTY__"){
					$results["libertas_form"] = array();
					$results["libertas_form_int"] = array();
					$results["libertas_form"][0] =$identifier;
					$results["libertas_form_int"][0] =$id;
				} else {
					$results["libertas_form"][count($results["libertas_form"])] = $identifier;
					$results["libertas_form_int"][count($results["libertas_form_int"])] =$id;
				}
				$check = $rest;
			} 
		}
		$found=true;
		$check=$str;
		while($found){
			$pos = strpos(strtolower($check),'<img');
			if ($pos===false){
				$found=false;
			} else {
				$end = strpos($check,'>', $pos)+1;
				$rest= substr($check, $end);
				if (strtolower(substr($check,$pos,strlen('<img id="libertas_form"')))!='<img id="libertas_form"'){
					$tag		= substr($check, $pos, $end-$pos);
					$tag_start	= strpos(strtolower($tag), 'src="');
					$s = 5;
					$tag_end	= strpos($tag, '"', $tag_start+$s);
					$src		= substr($tag, $tag_start+$s, $tag_end -($tag_start+$s));
					$file_src	= split("/",$src);
					$identifier = split("\.", $file_src[count($file_src)-1]);
					if ($this->check_parameters($results,"libertas_image","__EMPTY__")=="__EMPTY__"){
						$results["libertas_image"] = array();
						$results["libertas_image"][0] = $identifier[0];
					} else {
						$results["libertas_image"][count($results["libertas_image"])] = $identifier[0];
					}
				}
				$check = $rest;
			} 
		}
		$found=true;
		$check=$str;
		while($found){
			$pos = strpos(strtolower($check),'<a ');
			if ($pos===false){
				$found=false;
			} else {
				$end = strpos(strtolower($check),'>', $pos)+1;
				$rest= substr($check, $end);
				$tag		= substr($check, $pos, $end-$pos);
				$tag_start	= strpos(strtolower($tag), 'href="');
				$tag_end	= strpos($tag, '"', $tag_start+6);
				$src		= join("?",split('\?&amp;',join("&amp;",split('&amp;&amp;',substr($tag, $tag_start+6, $tag_end - ($tag_start+6))))));
				if (strpos($src,"command=FILES_DOWNLOAD&amp;download=")){
					$identifier = split("command=FILES_DOWNLOAD&amp;download=", $src);
					$id = split("&amp;",$identifier[1]);
					if ($this->check_parameters($results,"libertas_files","__EMPTY__")=="__EMPTY__"){
						$results["libertas_files"] = array();
						$results["libertas_files"][0] = $id[0];
					} else {
						$results["libertas_files"][count($results["libertas_files"])] = $id[0];
					}
				}
				$check = $rest;
			}
		}
		$found=true;
		$check=$str;
		while($found){
			$pos = strpos(strtolower($check),'<a ');
			if ($pos===false){
				$found=false;
			} else {
				$end = strpos(strtolower($check),'>', $pos)+1;
				$rest= substr($check, $end);
				$tag		= substr($check, $pos, $end-$pos);
				$tag_start	= strpos(strtolower($tag), 'href="');
				if ($tag_start){
					$tag_end	= strpos(strtolower($tag), '"', $tag_start+6);
					$src		= substr($tag, $tag_start+6, $tag_end - ($tag_start+6));
					$lsrc = strtolower($src);
//					print "[$lsrc]<br>";
					if (strpos($lsrc,"http://")===false && strpos($lsrc,"?command=files_download&amp;download=")===false && strpos($lsrc,"mailto:")===false){
						if (strpos($lsrc,"index.php")===false){
							$list 					= split("/",$src);
							$old 					= split("\?",$list[count($list)-1]);
							$old = $old[0];
							$list[count($list)-1]	= "index.php";
							$identifier 			= join("/", $list);
							$sql 					= "
								select page_trans_data.trans_identifier, page_trans_data.trans_title, menu_data.menu_identifier from menu_data 
									inner join menu_access_to_page on menu_access_to_page.menu_identifier = menu_data.menu_identifier 
									inner join page_trans_data on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier 
								where 
									menu_client = $this->client_identifier and 
									menu_url = '$identifier'";
							$tid=0;
							$mid=0;
							$result			=	$this->call_command("DB_QUERY",Array($sql));
							while ($r		=	$this->call_command("DB_FETCH_ARRAY", Array($result))){
								$title		=	$r["trans_title"];
								$filename 	=	$this->make_uri($title).".php";
								if ($old 	==	$filename){
									$tid 	=	$r["trans_identifier"];
									$mid 	=	$r["menu_identifier"];
								}
							}
							if ($this->check_parameters($results,"libertas_links_trans","__EMPTY__")=="__EMPTY__"){
								$results["libertas_links_trans"] = array();
								$results["libertas_links_trans"][0] = Array("trans" =>$tid, "menu"=>$mid, "src" => $src);
							} else {
								$results["libertas_links_trans"][count($results["libertas_links_trans"])] = Array("trans" =>$tid, "menu"=>$mid, "src" => $src);
							}
						}else{
							$list 					= split("/",$src);
							$list[count($list)-1]	= "index.php";
							$identifier 			= join("/",$list);
							$sql 					= "select menu_identifier from menu_data where menu_client=$this->client_identifier and menu_url = '$identifier'";
//							print $sql;
							$result 				= $this->call_command("DB_QUERY",Array($sql));
							$old_id 				= $identifier;
							$mid					= 0;
//							print "[0,$mid,$src]";
							while ($r = $this->call_command("DB_FETCH_ARRAY", Array($result))){
								$mid = $r["menu_identifier"];
							}
							if ($this->check_parameters($results,"libertas_links_menu","__EMPTY__")=="__EMPTY__"){
								$results["libertas_links_menu"] = array();
								$results["libertas_links_menu"][0] = Array("trans" =>0, "menu"=>$mid, "src"=>$src);
							} else {
								$results["libertas_links_menu"][count($results["libertas_links_menu"])] = Array("trans" =>0, "menu"=>$mid, "src"=>$src);
							}
						}
					}
				}
				$check = $rest;
			}
		}
		return $results;
	}
	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* FN:: save_extracted_info
		* this function will attempt to save the information extracted from a memo field
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function save_extracted_info($parameters){
		$list_of_results	= $this->check_parameters($parameters,"list_of_results");
		$id					= $this->check_parameters($parameters,"id");
		$editor				= $this->check_parameters($parameters,"editor");
		$module_starter		= $this->check_parameters($parameters,"module");
		$previous_page_title= $this->check_parameters($parameters,"previous_title");
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		save the embedded form information
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$sql= "delete from embed_libertas_form where trans_identifier=$id and client_identifier=$this->client_identifier and editor='$editor'";
		$this->call_command("DB_QUERY",Array($sql));
		$list		= $this->check_parameters($list_of_results,"libertas_form","__NOT_FOUND__");
		$list_int	= $this->check_parameters($list_of_results,"libertas_form_int","__NOT_FOUND__");
		if ($list != "__NOT_FOUND__"){
			$len = count($list);
			for($index=0;$index<$len;$index++){
				if (is_int($list[$index])){
					$sql = "insert into embed_libertas_form (trans_identifier, client_identifier, form_int_identifier, editor, module_starter) values ('$id', '$this->client_identifier', '".$list[$index]."', '$editor', '$module_starter');";
				} else {
					$sql = "insert into embed_libertas_form (trans_identifier, client_identifier, form_int_identifier, form_str_identifier, editor, module_starter) values ('$id', '$this->client_identifier', '".$list_int[$index]."', '".$list[$index]."', '$editor', '$module_starter');";
				}
				$this->call_command("DB_QUERY",Array($sql));
			}
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		save the embedded image information
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$sql= "delete from embed_libertas_image where trans_identifier=$id and client_identifier=$this->client_identifier and editor='$editor'";
		$this->call_command("DB_QUERY",Array($sql));
		$list = $this->check_parameters($list_of_results,"libertas_image","__NOT_FOUND__");
		if ($list != "__NOT_FOUND__"){
			$len = count($list);
			for($index=0;$index<$len;$index++){
				$sql = "insert into embed_libertas_image (trans_identifier, client_identifier, image_tag, editor, module_starter) values ('$id', '$this->client_identifier', '".$list[$index]."', '$editor', '$module_starter');";
				$this->call_command("DB_QUERY",Array($sql));
			}
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		save the embedded image information
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$sql= "delete from embed_libertas_file where trans_identifier=$id and client_identifier=$this->client_identifier and editor='$editor'";
		$this->call_command("DB_QUERY",Array($sql));
		$list = $this->check_parameters($list_of_results,"libertas_files","__NOT_FOUND__");
		if ($list != "__NOT_FOUND__"){
			$len = count($list);
			for($index=0;$index<$len;$index++){
				$sql = "insert into embed_libertas_file (trans_identifier, client_identifier, file_tag, editor, module_starter) values ('$id', '$this->client_identifier', '".$list[$index]."', '$editor', '$module_starter');";
				$this->call_command("DB_QUERY",Array($sql));
			}
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* save the embedded link information
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$source = $id;
		$sql = "delete from embed_libertas_link where src_identifier=$source and client_identifier=$this->client_identifier and editor='$editor'";
		$this->call_command("DB_QUERY",Array($sql));
		$list = $this->check_parameters($list_of_results,"libertas_links_menu","__NOT_FOUND__");
		if ($list != "__NOT_FOUND__"){
			$len = count($list);
			for($index=0;$index<$len;$index++){
				$src = $this->check_parameters($list[$index],"src","");
				if (strpos($src,"\?")){
					$split_info = split("\?",$src);
					$src = $split_info[0];
				}
				$sql = "insert into embed_libertas_link (src_identifier, client_identifier, menu_identifier, dst_identifier, destination_url , editor, module_starter) values ('$source', '$this->client_identifier', '".$this->check_parameters($list[$index],"menu","0")."', 0, '".$src."', '$editor', '$module_starter');";
//				print $sql;
				$this->call_command("DB_QUERY",Array($sql));
			}
		}
		$list = $this->check_parameters($list_of_results,"libertas_links_trans","__NOT_FOUND__");
		if ($list != "__NOT_FOUND__"){
			$len = count($list);
			for($index=0;$index<$len;$index++){
				$src = $this->check_parameters($list[$index],"src","");
				if (strpos($src,"?")){
					$split_info = split("\?",$src);
					$src = $split_info[0];
				}
				$sql = "insert into embed_libertas_link (src_identifier, client_identifier, dst_identifier, menu_identifier, destination_url , editor, module_starter) values ('$source', '$this->client_identifier', '".$this->check_parameters($list[$index],"trans","0")."', '".$this->check_parameters($list[$index],"menu","0")."', '".$src."', '$editor', '$module_starter');";
//				print $sql;
				$this->call_command("DB_QUERY",Array($sql));
			}
		}
	}
	
	function copy_embedded($parameters){
		$from_translation_identifier 	= $this->check_parameters($parameters,"from_trans_id",-1);
		$to_translation_identifier		= $this->check_parameters($parameters,"to_trans_id",-1);
		$user 							= $_SESSION["SESSION_USER_IDENTIFIER"];	
		
		$sql = "select * from embed_libertas_form where trans_identifier  = $from_translation_identifier and client_identifier = $this->client_identifier;";
		$copy_result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_access_to_page form",__LINE__,"[$copy_result :: $sql]"));
		}	
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($copy_result))){
			$sql ="insert into embed_libertas_form (trans_identifier, client_identifier, form_int_identifier, form_str_identifier, module_starter, editor ) values ($to_translation_identifier, $this->client_identifier, ".$this->check_parameters($r,"form_int_identifier",0).", '".$this->check_parameters($r,"form_str_identifier")."', '".$this->check_parameters($r,"module_starter")."', '".$this->check_parameters($r,"editor")."')";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_embedded form",__LINE__,"[$copy_result :: $sql]"));
			}
			$this->call_command("DB_QUERY",array($sql));
		}

		$sql = "select * from embed_libertas_image where trans_identifier = $from_translation_identifier and client_identifier = $this->client_identifier;";
		$copy_result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_access_to_page image",__LINE__,"[$copy_result :: $sql]"));
		}	
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($copy_result))){
			$sql ="insert into embed_libertas_image (trans_identifier, client_identifier, image_tag ) values ($to_translation_identifier, $this->client_identifier, '".$this->check_parameters($r,"image_tag")."')";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_embedded image",__LINE__,"[$copy_result :: $sql]"));
			}
			$this->call_command("DB_QUERY",array($sql));
		}

		$sql = "select * from embed_libertas_file where trans_identifier = $from_translation_identifier and client_identifier = $this->client_identifier;";
		$copy_result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_access_to_page image",__LINE__,"[$copy_result :: $sql]"));
		}	
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($copy_result))){
			$sql ="insert into embed_libertas_file (trans_identifier, client_identifier, file_tag ) values ($to_translation_identifier, $this->client_identifier, '".$this->check_parameters($r,"file_tag")."')";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_embedded image",__LINE__,"[$copy_result :: $sql]"));
			}
			$this->call_command("DB_QUERY",array($sql));
		}
		$sql = "
		select 
			*
		from embed_libertas_link 
		where 
			((src_identifier = $from_translation_identifier or dst_identifier = $from_translation_identifier )) and 
			client_identifier = $this->client_identifier
		";
		$copy_result = $this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_access_to_page link",__LINE__,"[$copy_result :: $sql]"));
		}	
		while ($r = $this->call_command("DB_FETCH_ARRAY",Array($copy_result))){
				$destination_url	= $this->check_parameters($r, "destination_url");
				$editor				= $this->check_parameters($r, "editor");
				$module_starter		= $this->check_parameters($r, "module_starter");
				if ($r["src_identifier"]==$from_translation_identifier){
					$sql ="insert into embed_libertas_link (destination_url, src_identifier, client_identifier, dst_identifier, menu_identifier, editor, module_starter) 
								values 
						   ('$destination_url', $to_translation_identifier, $this->client_identifier, '".$this->check_parameters($r,"dst_identifier")."', '".$this->check_parameters($r,"menu_identifier")."', '$editor', '$module_starter')";
				} else {
					$sql ="insert into embed_libertas_link (destination_url, src_identifier, client_identifier, dst_identifier, menu_identifier, editor, module_starter) 
								values 
						   ('$destination_url', '".$this->check_parameters($r,"src_identifier")."', $this->client_identifier, '".$to_translation_identifier."', '".$this->check_parameters($r,"menu_identifier")."', '$editor', '$module_starter')";
				}
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"copy_embedded link",__LINE__,"[$copy_result :: $sql]"));
				}
				$this->call_command("DB_QUERY",array($sql));
		}
	}
	
	
	
	function request_page_update($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$cancel = $this->check_parameters($parameters,"cancel","PAGE_LIST");
		$out = "";
		$sql = "
			select distinct user_info.user_identifier from user_info 
				inner join groups_belonging_to_user on groups_belonging_to_user.user_identifier = user_info.user_identifier
				inner join group_access on group_access.access_group = groups_belonging_to_user.group_identifier
				left outer join relate_user_menu on relate_user_menu.user_identifier = user_info.user_identifier
				left outer join menu_access_to_page on menu_access_to_page.menu_identifier = relate_user_menu.menu_identifier
			where
				(
					access_code = 'ALL' or 
					access_code = 'PAGE_ALL' or 
					access_code = 'PAGE_AUTHOR' 
				) and 
				(trans_identifier = $identifier or trans_identifier is null) and
				user_client = $this->client_identifier
			";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$authors ="";
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$user_identifier = $r["user_identifier"];
				$contact_full_name	= $this->call_command("CONTACT_GET_NAME",Array("contact_user" => $user_identifier));
				$authors .= "<option value='".$user_identifier  ."'>". $contact_full_name ."</option>";
			}
		}

		$out .="<form name=\"process_form\" label=\"Remove this entry completely\">";
		$out .= "<input type=\"hidden\" name=\"identifier\"><![CDATA[$identifier]]></input>";
		$out .= "<input type=\"hidden\" name=\"cancel\"><![CDATA[$cancel]]></input>";
		$out .= "<input type=\"hidden\" name=\"command\"><![CDATA[EMBED_SAVE_UPDATE_REQUEST]]></input>";
		$out .= "<select required='yes' label='Select an author to request the page update' name='author'><option value=''>-- Please select an Author to update this page -- </option>$authors</select>";
		$out .= "<textarea required='yes' height='12' label='Message to send to author, please describe request in detail' name='request_message'><![CDATA[]]></textarea>";
		$out .= "<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"$cancel\"/>";
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"SAVE_DATA\"/>";
		$out .="</form>";
		$out = "<module name=\"".$this->module_name."\" display=\"form\">".$out."</module>";

		return $out;
	}

	function submit_page_update_request($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$cancel 	= $this->check_parameters($parameters,"cancel","PAGE_LIST");
		$your_msg 	= $this->check_parameters($parameters,"request_message","");
		$author		= $this->check_parameters($parameters,"author",-1);
		$sql 		= "
			select page_trans_data.trans_title, menu_access_to_page.menu_identifier from menu_access_to_page 
				inner join page_trans_data on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier
				where page_trans_data.trans_identifier = $identifier and page_trans_data.trans_client=$this->client_identifier
		";
		
		$location	= "";
		$title		= "";
		$result 	= $this->call_command("DB_QUERY",Array($sql));
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$location .= $this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL",Array("id"=>$r["menu_identifier"],"splitter"=>" / ")) . "\n";
				$title = $r["trans_title"];
			}
		}
		$loaction = join("/",split("'\&\#187\;'",$location));
		$msg 		= "
----------------------------------------------------------------------
A request has been made for you to update the following page
\"$title\"
it is located in the following location(s)
$location
----------------------- Message from requester -----------------------
".$your_msg."
----------------------------------------------------------------------
";
		$this->call_command("TASK_SUBMIT",Array(
			"author" => $author,
			"subject" => "Request for update to page content",
			"msg" => $msg)
		);
	}
	
	function update_embed($parameters){
		$seek_index = $this->check_parameters($parameters,"seek_index",0);
		$id = $this->check_parameters($parameters,"id",0);
		
		if($id==0)
			$sql 	= "select * from memo_information where mi_client=$this->client_identifier order by mi_identifier";
		else 
			$sql 	= "select * from memo_information where mi_client=$this->client_identifier and mi_link_id=$id order by mi_link_id";
		$result 	= $this->call_command("DB_QUERY", Array($sql));
		$total 		= $this->call_command("DB_NUM_ROWS",Array($result));
		if ($total > $seek_index){
			$pointer 	= $this->call_command("DB_SEEK", Array($result, $seek_index));
			$i=0;
			while (($r = $this->call_command("DB_FETCH_ARRAY", Array($result))) && ($i<10)){
				$i++;
				$mi_memo 						= $this->validate(html_entity_decode($r["mi_memo"]));
				$identifier 					= $r["mi_link_id"];
				$list_of_embedded_information	= $this->extract_embedded_information(Array("str" => $mi_memo));
				$this->save_extracted_info(Array(
						"list_of_results"	=> $list_of_embedded_information, 
						"id"				=> $identifier, 
						"editor"			=> $r["mi_field"], 
						"module"			=> $r["mi_type"]
					)
				);
			}
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=EMBED_UPDATE_INFO&seek_index=".($seek_index+10)));
		}
		
	}

	function manage_links($parameters){
		$debug = $this->debugit(false,$parameters);
		$processtype			= $this->check_parameters($parameters	,"processtype","noaction");
		$action					= $this->check_parameters($parameters	,"action","");
		$source					= $this->check_parameters($parameters	,"source","-1");
		$editor					= $this->check_parameters($parameters	,"editor","");
		$direction	 			= $this->check_parameters($parameters	,"direction","inout");
		$restricted_access		= $this->check_parameters($_SESSION		,"SESSION_MANAGEMENT_ACCESS",	Array());
		$user_identifier		= $this->check_parameters($_SESSION		,"SESSION_USER_IDENTIFIER",	Array());
		$restricted_access_CSV	= " ".join(", ",$restricted_access).",";
		if ($source==-1){
			$trans		 = $this->check_parameters($parameters,"identifier","-1");
			$source = $trans;
		}
		$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd1",
				"field_as"			=> "trans_body1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "body"
			)
		);
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary"
			)
		);

		$sql = "
			select embed_libertas_link.*, ptd1.trans_title as src_title, ptd2.trans_title as dst_title,
					ptd1.trans_doc_lock_to_user as src_lock, 
					ptd2.trans_doc_lock_to_user as dst_lock,
					ptd1.trans_doc_status as src_status,
					ptd2.trans_doc_status as dst_status,
					menu_data.menu_label 
				from embed_libertas_link 
				left outer join page_trans_data as ptd1 on 
					ptd1.trans_identifier = embed_libertas_link.src_identifier and 
					embed_libertas_link.dst_identifier >0 and 
					module_starter ='PAGE_' 
				left outer join page_trans_data as ptd2 on 
					ptd2.trans_identifier = embed_libertas_link.dst_identifier 
				left outer join menu_data on 
					menu_data.menu_identifier = embed_libertas_link.menu_identifier 
			where 
				(src_identifier=$source or dst_identifier=$source) and 
				client_identifier=$this->client_identifier and
				(
					(ptd2.trans_current_working_version = 1 or ptd2.trans_current_working_version is null )
						 or 
					(embed_libertas_link.dst_identifier =0 and ptd1.trans_current_working_version = 1 or ptd1.trans_current_working_version is null )
				)";
				/*
			 editor ='$editor' and
				(
					(embed_libertas_link.dst_identifier >0 and ptd1.trans_current_working_version = 1 and ptd2.trans_current_working_version = 1)
						 or 
					(embed_libertas_link.dst_identifier =0 and ptd1.trans_current_working_version = 1)
				)
		";*/

		if ($debug)	print "<h1>Manage Links</h1>".$sql."";
		$result = $this->call_command("DB_QUERY",array($sql));
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			$external_to_me = "";
			$me_to_external = "";
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$embed_id				= $this->check_parameters($r,"embed_identifier","0");
				$dst					= $this->check_parameters($r,"dst_identifier","0");
				$src					= $this->check_parameters($r,"src_identifier","0");
				$menu					= $this->check_parameters($r,"menu_identifier","0");
				$menu_label				= $this->check_parameters($r,"menu_label","");
				$dst_title				= $this->check_parameters($r,"dst_title","");
				$dst_lock 				= $this->check_parameters($r,"dst_lock",0);
				$dst_status				= $this->check_parameters($r,"dst_status","");
				$src_status				= $this->check_parameters($r,"src_status","");
				$src_lock 				= $this->check_parameters($r,"src_lock",0);
				$src_title				= $this->check_parameters($r,"src_title","");
				$destination_url		= $this->check_parameters($r,"destination_url","");
				$editor					= $this->check_parameters($r,"editor","");
				$module_starter			= $this->check_parameters($r,"module_starter");
//				print $r["module_starter"];
				if (strlen($dst_title)==0){
					$dst_title	 = $menu_label;
				}
				if (strlen($dst_title)==0){
					$dst_title	 = $destination_url;
				}
				//$restricted_access_CSV ="  393, ";
				
				if (($direction  == "inout") || ($direction == "out")){
					if ($src==$source){
						if ($dst==0 && $menu!=0){
							$well=" <span style='color:#0000ff'>Link to Menu Location</span>";
							$valid = $this->check_location(
								Array(
									"type"			=> "MENU", 
									"embed_id"		=> $embed_id, 
									"page"			=> $src, 
									"menu"			=> $menu, 
									"dst"			=> $dst, 
									"url"			=> $destination_url,
									"src_title"		=> $src_title,
									"dst_title"		=> $dst_title,
									"module_starter"=> $module_starter
								)
							);
						} else {
							if ($dst == 0 ){
								$well=" <span style='color:#ff0000'>Unknown Link</span>";
								$valid = $this->check_location(
									Array(
										"type"		=> "MENU", 
										"embed_id"	=> $embed_id, 
										"page"		=> $src, 
										"menu"		=> $menu, 
										"dst"		=> $dst, 
										"url"		=> $destination_url,
										"src_title"	=> $src_title,
										"dst_title"	=> $dst_title,
										"module_starter"=> $module_starter
									)
								);
							} else {
								$well=" <span style='color:#0000ff'>Links to Page</span>";
								$valid = $this->check_location(
									Array(
										"type"		=> "PAGE", 
										"embed_id"	=> $embed_id, 
										"page"		=> $src, 
										"menu"		=> $menu, 
										"dst"		=> $dst, 
										"url"		=> $destination_url,
										"src_title"	=> $src_title,
										"dst_title"	=> $dst_title,
										"module_starter"=> $module_starter
									)
								);
							}
						}
						if ($action!=""){
							if(strlen($restricted_access_CSV)==2){
								if (($dst_lock != 0) && ($dst_lock != $user_identifier)){
									$list_of_options="<a href='admin/index.php?command=EMBED_REQUEST_UPDATE&identifier=$src'>".LOCALE_EMAIL_THIS_PAGE."</a>";
								} else {
									if ($src_status!=4){
										$list_of_options="<a href='admin/index.php?command=PAGE_EDIT&identifier=$src'>".EDIT_EXISTING."</a>";
									} else {
										$list_of_options="<a href='admin/index.php?command=PAGE_COPY_VERSION&identifier=$src'>".LOCALE_NEW_COPY."</a>";
									}
								}
							} else {
								if (strpos($restricted_access_CSV," $source,")===false){
									$list_of_options="<a href='admin/index.php?command=EMBED_REQUEST_UPDATE&identifier=$src'>".LOCALE_EMAIL_THIS_PAGE."</a>";
								} else {
									if (($dst_lock != 0) && ($dst_lock != $user_identifier)){
										$list_of_options="<a href='admin/index.php?command=EMBED_REQUEST_UPDATE&identifier=$src'>".LOCALE_EMAIL_THIS_PAGE."</a>";
									} else {
										if ($dst_status!=4){
											$list_of_options="<a href='admin/index.php?command=PAGE_EDIT&identifier=$src'>".EDIT_EXISTING."</a>";
										} else {
											$list_of_options="<a href='admin/index.php?command=PAGE_COPY_VERSION&identifier=$src'>".LOCALE_NEW_COPY."</a>";
										}
									}
								}
							}
							$me_to_external .= "<tr bgcolor='#ffffff'><td>linking to (".$dst_title.")</td><td>$well</td><td>$valid</td><td>$list_of_options</td></tr>";
						} else {
							$me_to_external .= "<tr bgcolor='#ffffff'><td>linking to (".$dst_title.",".$src_title.")</td><td>$well</td><td>$valid</td></tr>";
						}
					} 
				}
				if (($direction == "inout") || ($direction == "in")){
					if ($dst == $source) {
						if ($dst==0 && $menu!=0){
							$well=" <span style='color:#0000ff'>Link to Menu Location</span>";
							$valid = $this->check_location(
								Array(
									"type"=>"MENU", 
									"embed_id"=>$embed_id, 
									"page"=>$src, 
									"menu"=>$menu, 
									"dst"=>$dst, 
									"url" => $destination_url,
									"src_title"	=> $src_title,
									"dst_title"	=> $dst_title,
									"module_starter"=> $module_starter
								)
							);
						} else {
							if ($dst == 0 ){
								$well=" <span style='color:#ff0000'>Unknown Link</span>";
								$valid = $this->check_location(
									Array(
										"type"=>"MENU", 
										"embed_id"=>$embed_id, 
										"page"=>$src, 
										"menu"=>$menu, 
										"dst"=>$dst, 
										"url" => $destination_url,
										"src_title"	=> $src_title,
										"dst_title"	=> $dst_title,
										"module_starter"=> $module_starter
									)
								);
							} else {
								$well=" <span style='color:#0000ff'>Links to Page</span>";
								$valid = $this->check_location(
									Array(
										"type"=>"PAGE", 
										"embed_id"=>$embed_id, 
										"page"=>$src, 
										"menu"=>$menu, 
										"dst"=>$dst, 
										"url" => $destination_url,
										"src_title"	=> $src_title,
										"dst_title"	=> $dst_title,
										"module_starter"=> $module_starter
									)
								);
							}
						}
						if ($action!=""){
							if(strlen($restricted_access_CSV)==2){
								if (($dst_lock != 0) && ($dst_lock != $user_identifier)){
									$list_of_options="<a href='admin/index.php?command=EMBED_REQUEST_UPDATE&identifier=$src'>".LOCALE_EMAIL_THIS_PAGE."</a>";
								} else {
									if ($src_status!=4){
										$list_of_options="<a href='admin/index.php?command=PAGE_EDIT&identifier=$src'>".EDIT_EXISTING."</a>";
									} else {
										$list_of_options="<a href='admin/index.php?command=PAGE_COPY_VERSION&identifier=$src'>".LOCALE_NEW_COPY."</a>";
									}
								}
							} else {
								if (strpos($restricted_access_CSV," $source,")===false){
									$list_of_options="<a href='admin/index.php?command=EMBED_REQUEST_UPDATE&identifier=$src'>".LOCALE_EMAIL_THIS_PAGE."</a>";
								} else {
									if (($dst_lock != 0) && ($dst_lock != $user_identifier)){
										$list_of_options="<a href='admin/index.php?command=EMBED_REQUEST_UPDATE&identifier=$src'>".LOCALE_EMAIL_THIS_PAGE."</a>";
									} else {
										if ($dst_status!=4){
											$list_of_options="<a href='admin/index.php?command=PAGE_EDIT&identifier=$src'>".EDIT_EXISTING."</a>";
										} else {
											$list_of_options="<a href='admin/index.php?command=PAGE_COPY_VERSION&identifier=$src'>".LOCALE_NEW_COPY."</a>";
										}
									}
								}
							}
							$external_to_me .= "<tr bgcolor='#ffffff'><td>links from ($src_title)</td><td>$well</td><td>$valid</td><td>$list_of_options</td></tr>";
						} else {
							$external_to_me .= "<tr bgcolor='#ffffff'><td>links from ($src_title)</td><td>$well</td><td>$valid</td></tr>";
						}
					}
				}
			}
			if (($direction == "inout") || ($direction == "in")){
				if (strlen($external_to_me)==0){
					$external_to_me = "<tr><td><h2>Documents containing links to this page</h2></td></tr>
									<tr><td><table cellspacing='1' cellpadding='3' width='100%'><tr bgcolor='#ffffff'><td colspan='3'><p><strong>Sorry there area no pages linking to this page.</strong></p></td></tr></table></td></tr>";
				} else {
					$ex_to_me = "<tr><td><h2>Documents containing links to this page</h2></td></tr>
								<tr><td><table cellspacing='1' cellpadding='3' bgcolor='#666666' width='100%'><tr bgcolor='#ffffff'><td class='btlabel'><strong>Link</strong></td><td width='15%' class='btlabel'><strong>Type</strong></td><td width='15%' class='btlabel'><strong>Status</strong></td>";
					if ($action!=""){
						$ex_to_me .= "<td width='15%' class='btlabel'><strong>Action</strong></td>";
					}
					$external_to_me = $ex_to_me."</tr>".$external_to_me."</table></td></tr>";
				}	
			}
			if (($direction == "inout") || ($direction == "out")){
				if (strlen($me_to_external)==0){
					$me_to_external = "<tr><td><h2>Links contained in this document</h2></td></tr>
					<tr><td><table cellspacing='1' cellpadding='3' width='100%'><tr bgcolor='#ffffff'><td colspan='3'><p><strong>Sorry there area no links in this page.</strong></p></td></tr></table></td></tr>";
				} else {
					$me_to_ex = "<tr><td><h2>Links contained in this document</h2></td></tr>
					<tr><td><table cellspacing='1' cellpadding='3' bgcolor='#666666' width='100%'><tr bgcolor='#ffffff'><td class='btlabel'><strong>Link</strong></td><td width='15%' class='btlabel'><strong>Type</strong></td><td width='15%' class='btlabel'><strong>Status</strong></td>";
					if ($action!=""){
						$me_to_ex .= "<td width='15%' class='btlabel'><strong>Action</strong></td>";
					}
					$me_to_external = $me_to_ex."</tr>".$me_to_external."</table></td></tr>";
				}
			}
		}
		$sz = "<table width='100%'>
					$me_to_external
					$external_to_me
				</table>";
//		print $sz;
		return $sz;
	}

	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* FN check_loaction
		-
		* One of the parameters of this function is called type this is used in the
		* conditions of this module PAGE and MENU are not modules but rather they are
		* types of links ie this link links to a page rather than a module
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* TO DO LIST
		-	1. make this function work with the memo_information table 
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	function check_location($parameters){
		$debug = $this->debugit(false,$parameters);
		if ($debug) print_r($parameters);
		$embed_id		= $this->check_parameters($parameters,"embed_id");
		$type			= $this->check_parameters($parameters,"type");
		$page			= $this->check_parameters($parameters,"page");
		$menu			= $this->check_parameters($parameters,"menu");
		$url			= $this->check_parameters($parameters,"url");
		$dst			= $this->check_parameters($parameters,"dst");
		$src_title		= $this->check_parameters($parameters,"src_title");
		$dst_title		= $this->check_parameters($parameters,"dst_title");
		$module_starter	= $this->check_parameters($parameters,"module_starter");
		$url_to_check	= "";
		$ok 			= false;

		if ($debug) print 	"[$embed_id, $type, $page, $menu, $url, $dst, $src_title, $dst_title]";

		/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Extract parts for sql
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		/*
				$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
						"table_as"			=> "ptd1",
						"field_as"			=> "trans_body1",
						"identifier_field"	=> "page_trans_data.trans_identifier",
						"module_command"	=> "PAGE_",
						"client_field"		=> "trans_client",
						"mi_field"			=> "body"
					)
				);
				$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
						"table_as"			=> "ptd2",
						"field_as"			=> "trans_summary1",
						"identifier_field"	=> "page_trans_data.trans_identifier",
						"module_command"	=> "PAGE_",
						"client_field"		=> "trans_client",
						"mi_field"			=> "summary"
					)
				);
		*/
		if($debug) print "<hr><h1>Checklocation</h1>";
		if ($type=="MENU"){
			/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- extract only records with the url we are searching for speed increase
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql = "
				select 
					menu_data.menu_directory, 
					menu_data.menu_url, 
					memo_information.mi_type, 
					memo_information.mi_memo, 
					memo_information.mi_link_id, 
					memo_information.mi_field, 
					embed_libertas_link.menu_identifier, 
					embed_libertas_link.destination_url
				from memo_information 
					inner join embed_libertas_link on embed_libertas_link.module_starter = memo_information.mi_type and 
					(mi_link_id = dst_identifier  or (dst_identifier =0 and mi_link_id = src_identifier)) and 
					mi_client = client_identifier and memo_information.mi_field = embed_libertas_link.editor
				left outer join 
					menu_data on 
						embed_libertas_link.menu_identifier = menu_data.menu_identifier and 
						menu_client = client_identifier
				where menu_data.menu_identifier =$menu and 
					mi_client=$this->client_identifier and 
					mi_memo like '%$url%'";
			if($debug) print __LINE__."::[$embed_id, $type, $page, $menu, $url, $dst, $src_title, $dst_title, $module_starter]";
			if($debug) print "<hr>Link to Menu location<p>".$sql."</p><hr>";

			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->call_command("DB_NUM_ROWS", Array($result))==0){
				$ok = false;
			} else {
				$result  = $this->call_command("DB_QUERY",Array($sql));
                while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					if($debug) print "<p>url m_id [".$r["menu_directory"] ."]</p>";
					$url_to_check = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["menu_directory"]))."index.php";
					if (strpos(" ".$url,$url_to_check)!=1){
						if($debug) print "<p>On field [<strong>".$r["mi_field"]."</strong>]replace [".$url."] with [".$url_to_check."]</p>";
						$p_list = Array(
									"type"			=> "MENU", 
									"embed_id"		=> $embed_id, 
									"replace"		=> $url, 
									"with"			=> $url_to_check, 
									"body"			=> $r["mi_memo"],
									"identifier"	=> $r["mi_link_id"],
									"field"			=> $r["mi_field"],
									"mi_type"		=> $r["mi_type"]
								);
						if($debug) print_r($p_list);
						$fixed = $this->fix_page_link($p_list);
						if (!$fixed){
							$url_to_check	= "change to " . $url_to_check;
							$ok				= false;
						} else {
							$url_to_check	= "fixed";
							$ok				= false;
						}
					}else{
						$ok				= true;
					}
                }
                $this->call_command("DB_FREE",Array($result));
			}
		}
		if ($type=="PAGE"){
			$sql = "
				select 
					menu_data.menu_directory, 
					menu_data.menu_url, 
					memo_information.mi_type, 
					memo_information.mi_memo, 
					memo_information.mi_link_id, 
					memo_information.mi_field, 
					embed_libertas_link.menu_identifier, 
					embed_libertas_link.destination_url,
					page_trans_data.trans_title,
					dst_identifier,
					src_identifier
				from memo_information 
					left outer join embed_libertas_link on embed_libertas_link.module_starter = memo_information.mi_type and 
					(mi_link_id = dst_identifier  or mi_link_id = src_identifier) and 
						mi_client = client_identifier and memo_information.mi_field = embed_libertas_link.editor
					left outer join menu_data on 
						embed_libertas_link.menu_identifier = menu_data.menu_identifier and 
						menu_client = client_identifier
					left outer join page_trans_data on 
						embed_libertas_link.dst_identifier = page_trans_data.trans_identifier and 
						memo_information.mi_type = 'PAGE_' and 
						memo_information.mi_client = page_trans_data.trans_client
				where menu_data.menu_identifier =$menu and 
					mi_client=$this->client_identifier  and 
					mi_link_id = $page and 
					dst_identifier =$dst	
				";
			if($debug) print 	"[$embed_id, $type, $page, $menu, $url, $dst, $src_title, $dst_title, $module_starter]";
			if($debug) print "<hr>Link to page url<p>".$sql."</p><hr>";
			// examine 1864
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->call_command("DB_NUM_ROWS", Array($result))==0){
				$ok = false;
			}else{
				$result  = $this->call_command("DB_QUERY",Array($sql));
                while($row = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$transtitle = $this->check_parameters($row,"trans_title",$dst_title);
                	$filename = $this->make_uri($transtitle).".php";
					$locations = split("/",$row["menu_url"]);
					$locations[count($locations)-1] = $filename;
					$url_to_check = join("/",$locations);
//					print "<p>On field [<strong>".$row["mi_field"]."</strong>]replace [".$url."] with [".$url_to_check."]</p>";
//					if ($module_starter=="ACCESSKEYADMIN_"){
//					if ($url == "projects/senior_drugs_amp_development_youth_forum/drugs_in_the_news/drinking/cameron_diaz_says_she_hates_the_damage_binge_drinking_did_to_her_skinssdf.php"){
//						$this->exitprogram();
//					}
					if ($url_to_check == $url){
						$ok = true;
					} else {
						$ok = false;
						$p_list = Array(
							"type"			=> "PAGE", 
							"embed_id"		=> $embed_id, 
							"replace"		=> $url, 
							"with"			=> $url_to_check, 
							"body"			=> $row["mi_memo"],
							"identifier"	=> $row["mi_link_id"],
							"field"			=> $row["mi_field"],
							"mi_type"		=> $row["mi_type"]
						);
						$fixed = $this->fix_page_link($p_list);
						if (!$fixed){
							$url_to_check = "change url to " . $url_to_check;
						} else {
							$url_to_check = "fixed";
						}
					}
				}
			}
		}
		if($debug) print "<hr>";
		return ($ok) ? "<span style='color:#0000ff'>valid</span>" : "<span style='color:#ff0000'>broken $url_to_check</span>";
	}
	
	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* take a mi_memo fields contents and do a string replace on the url included
		* then update the memo_information field and the embeded_libertas_link url to 
		* point to contain the fix.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/

	function fix_page_link($parameters){
		$type				= $this->check_parameters($parameters,"type","MENU");
		$embed_id			= $this->check_parameters($parameters,"embed_id");
		$replace			= $this->check_parameters($parameters,"replace");
		$with				= $this->check_parameters($parameters,"with");
		$body				= $this->check_parameters($parameters,"body");
		$identifier			= $this->check_parameters($parameters,"identifier");
		$field				= $this->check_parameters($parameters,"field");
		$mi_type			= $this->check_parameters($parameters,"mi_type");
		$new_content = str_replace("$replace", "$with", $body);
		$this->call_command("MEMOINFO_UPDATE",
			array(
				"mi_type"		=> $mi_type,
				"mi_memo"		=> $new_content,	
				"mi_link_id" 	=> $identifier, 
				"mi_field" 		=> $field
			)
		);
		
		$sql = "update embed_libertas_link set destination_url='$with' where embed_identifier =$embed_id and client_identifier=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		return false;
	}
	
	function find_and_fix($parameters){
		$with		= $this->check_parameters($parameters,"np").$this->make_uri($this->check_parameters($parameters,"current")).".php";
		$replace	= $this->check_parameters($parameters,"op").$this->make_uri($this->check_parameters($parameters,"old")).".php";
		/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- This function is to be used on the save of a document on the change of the title of a 
			- document the url will change the old url will be search for and all records that are 
			- returned will be fixed.
			*/
		
		$sql	= "
select 		menu_identifier, 
					src_identifier, 
					dst_identifier, 
					destination_url, 
					module_starter, 
					editor,
					ptd1.mi_memo as ptd1body,
					ptd2.mi_memo as ptd2body,
					ptd1.mi_memo as ptd1summary,
					ptd2.mi_memo as ptd2summary,
					ptd1.mi_link_id as ptd1id,
					ptd2.mi_link_id as ptd2id
from embed_libertas_link 
		left outer join memo_information as ptd1 on ptd1.mi_link_id = src_identifier and ptd1.mi_type='PAGE_' and editor = 'body'
		left outer join memo_information as ptd2 on ptd2.mi_link_id = dst_identifier and ptd2.mi_type='PAGE_' and editor = 'body'
		left outer join memo_information as ptd3 on ptd3.mi_link_id = src_identifier and ptd1.mi_type='PAGE_' and editor = 'summary'
		left outer join memo_information as ptd4 on ptd4.mi_link_id = dst_identifier and ptd2.mi_type='PAGE_' and editor = 'summary'
			where 
				destination_url like '$replace%' and 
				client_identifier=$this->client_identifier
			";
/*
					select
				distinct 
					menu_identifier, 
					src_identifier, 
					dst_identifier, 
					destination_url, 
					module_starter, 
					editor,
					ptd1.trans_identifier as ptd1id,
					ptd1.trans_body as ptd1body,
					ptd1.trans_summary as ptd1summary, 
					ptd2.trans_identifier as ptd2id,
					ptd2.trans_body as ptd2body, 
					ptd2.trans_summary as ptd2summary 
			from embed_libertas_link 
				inner join memo_information as ptd1 on ptd1.trans_identifier = src_identifier
				inner join page_trans_data as ptd2 on ptd2.trans_identifier = dst_identifier
			where 
*/
//		print "<p>$sql</p>";
		$result = $this->call_command("DB_QUERY",array($sql));
		while 	($r = $this->call_command("DB_FETCH_ARRAY", array($result) ) ){
//			print $r["src_identifier"]."<br>";
			$ptd1id		 = $r["ptd1id"];
			$ptd2id 	 = $r["ptd2id"];
			$ptd1body	 = str_replace("$replace", "$with", $r["ptd1body"]);
			$ptd2body 	 = str_replace("$replace", "$with", $r["ptd2body"]);
			$ptd1summary = str_replace("$replace", "$with", $r["ptd1summary"]);
			$ptd2summary = str_replace("$replace", "$with", $r["ptd2summary"]);
			$sql = "update page_trans_data set trans_body ='$ptd1body', trans_summary ='$ptd1summary' where trans_identifier =$ptd1id and trans_client=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "update page_trans_data set trans_body ='$ptd2body', trans_summary ='$ptd2summary' where trans_identifier =$ptd2id and trans_client=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
			
			$sql = "update embed_libertas_link set destination_url='$with' where destination_url like '$replace%' and client_identifier=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
		}
	}
	
	function fix_menu($parameters){
		$mIdentifier= $this->check_parameters($parameters,"menu_identifier");
		$mLabel		= $this->check_parameters($parameters,"menu_label");
		$pLabel 	= $this->check_parameters($parameters,"previous_menu_label");
		$mParent	= $this->check_parameters($parameters,"menu_parent",-1);
		$pParent 	= $this->check_parameters($parameters,"prev_menu_parent",-1);
		
		$menus = $this->call_command("LAYOUT_GET_MENUS");
		$out =  "$mIdentifier";
		for ($i=0,$max=count($menus);$i<$max;$i++){
			if ($this->check_parameters($menus[$i],"PARENT")==$mIdentifier){
				$out .= ", ".$this->check_parameters($menus[$i],"IDENTIFIER");
				$out .= $this->list_children($menus,$this->check_parameters($menus[$i],"IDENTIFIER"));
			}
		}
		$sql ="
		select 
			embed_libertas_link.*, 
			menu_data.*, 
			ptd1.trans_identifier as ptd1id,
			ptd1.trans_body as ptd1body,
			ptd1.trans_summary as ptd1summary, 
			ptd2.trans_identifier as ptd2id,
			ptd2.trans_body as ptd2body, 
			ptd2.trans_summary as ptd2summary 
		from embed_libertas_link 
			inner join menu_data on embed_libertas_link.menu_identifier = menu_data.menu_identifier 
			inner join page_trans_data as ptd1 on ptd1.trans_identifier = src_identifier
			inner join page_trans_data as ptd2 on ptd2.trans_identifier = dst_identifier
		where 
			embed_libertas_link.menu_identifier in ($out) and 
			client_identifier=$this->client_identifier
		";
//		print "<p>$sql</p>";
		if ($mLabel!=$pLabel){
		//	print "<p>changing $pLabel to $mLabel</p>";
		}
		if ($mParent!=$pParent){
	//		print "<p>Moving from $pParent to $mParent</p>";
		}
		$result = $this->call_command("DB_QUERY",array($sql));
//		print "<ul>";
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			if (strpos(" ".$r["destination_url"],'?')>0){
				$list 		= split('\?',$r["destination_url"]);
				$path		= $list[0];
			} else {
				$path 		= $r["destination_url"];
			}
			$ptd1id		 	= $r["ptd1id"];
			$ptd2id 		= $r["ptd2id"];
			$list  			= split('/',$path);
			$replace 		= $path;
			
			$with 			= $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["menu_directory"]))."".$list[count($list)-1];
			$ptd1body 		= str_replace("http://www.cewcni.org.uk/","", $r["ptd1body"]);
			$ptd1summary 	= str_replace("http://www.cewcni.org.uk/","", $r["ptd1summary"]);
			$ptd1body 		= str_replace("$replace", "$with", $ptd1body);
			$ptd1summary 	= str_replace("$replace", "$with", $ptd1summary);
			$sql = "update page_trans_data set trans_body ='$ptd1body', trans_summary ='$ptd1summary' where trans_identifier = $ptd1id and trans_client=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
//			print "<p>$sql</p>";
			if ($r["dst_identifier"]!=0){
				$ptd2body 		= str_replace("http://www.cewcni.org.uk/","", $r["ptd2body"]);
				$ptd2summary 	= str_replace("http://www.cewcni.org.uk/","", $r["ptd2summary"]);
				$ptd2body 		= str_replace("$replace", "$with", $ptd2body);
				$ptd2summary 	= str_replace("$replace", "$with", $ptd2summary);
				$sql = "update page_trans_data set trans_body ='$ptd2body', trans_summary ='$ptd2summary' where trans_identifier = $ptd2id and trans_client=$this->client_identifier";
				$this->call_command("DB_QUERY",array($sql));
//				print "<p>$sql</p>";
			}
			$sql = "update embed_libertas_link set destination_url='$with' where destination_url like '$replace%' and client_identifier=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
//			print "<p>$sql</p>";
			
			
//			print "<li>".$r["embed_identifier"]." - ".$r["menu_identifier"]." - ".$r["dst_identifier"]." - <br>path ".$path.", <br>replace $replace, <br>with $with</li>";
		}
//		print "</ul>[$sql]";
		
	}
	
	function list_children($menus,$mIdentifier){
		$out ="";
		for ($i=0,$max=count($menus);$i<$max;$i++){
			if ($this->check_parameters($menus[$i],"PARENT")==$mIdentifier){
				$out .= ", ".$this->check_parameters($menus[$i],"IDENTIFIER");
				$out .= $this->list_children($menus,$this->check_parameters($menus[$i],"IDENTIFIER"));
			}
		}
		return $out;
	}
	
	function fix_pages($parameters){
		$msi		= $this->check_parameters($parameters,"menu_source_identifier");
		$mdi		= $this->check_parameters($parameters,"menu_destination_identifier");
		$replace	= $this->check_parameters($parameters,"replace");
		$with 		= $this->check_parameters($parameters,"with");
		$page 		= $this->check_parameters($parameters,"page");
		
		$sql = "select 
	distinct 
		src_identifier,dst_identifier,
			ptd1.trans_identifier as ptd1id,
			ptd1.trans_body as ptd1body,
			ptd1.trans_summary as ptd1summary, 
			ptd2.trans_identifier as ptd2id,
			ptd2.trans_body as ptd2body, 
			ptd2.trans_summary as ptd2summary 
from embed_libertas_link 
	inner join page_trans_data as ptd1 on ptd1.trans_identifier = src_identifier
	inner join page_trans_data as ptd2 on ptd2.trans_identifier = dst_identifier
where 
	destination_url like '$replace%' and 
	menu_identifier = $msi and 
	dst_identifier = $page and 
	client_identifier = $this->client_identifier
	";
//		print $sql;
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$ptd1id			= $r["ptd1id"];
			$ptd2id			= $r["ptd2id"];
			$ptd1body 		= $r["ptd1body"];
			$ptd1summary 	= $r["ptd1summary"];
			$ptd1body 		= str_replace("$replace", "$with", $ptd1body);
			$ptd1summary 	= str_replace("$replace", "$with", $ptd1summary);
			$sql = "update page_trans_data set trans_body ='$ptd1body', trans_summary ='$ptd1summary' where trans_identifier = $ptd1id and trans_client=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
//			print "<p>$sql</p>";
			if ($r["dst_identifier"]!=0){
				$ptd2body 		= $r["ptd2body"];
				$ptd2summary 	= $r["ptd2summary"];
				$ptd2body 		= str_replace("$replace", "$with", $ptd2body);
				$ptd2summary 	= str_replace("$replace", "$with", $ptd2summary);
				$sql = "update page_trans_data set trans_body ='$ptd2body', trans_summary ='$ptd2summary' where trans_identifier = $ptd2id and trans_client=$this->client_identifier";
				$this->call_command("DB_QUERY",array($sql));
//				print "<p>$sql</p>";
			}
			$sql = "update embed_libertas_link set destination_url='$with', menu_identifier ='$mdi' where destination_url like '$replace%' and client_identifier=$this->client_identifier";
			$this->call_command("DB_QUERY",array($sql));
//			print "<p>$sql</p>";
		}
	}
	
	
	function remove_embeded_info($parameters){
		$trans_list			= $this->check_parameters($parameters,"trans_list");
		$action 			= $this->check_parameters($parameters,"action","remove_item");
		$remove_type		= $this->check_parameters($parameters,"type","PAGE_");
		$trans_identifier	= $this->check_parameters($parameters,"identifier");
		$trans_id_list="";
		if ($action == "remove_item"){
			$sql = "delete from embed_libertas_link where module_starter = '$remove_type' and (src_identifier = $trans_identifier or dst_identifier = $trans_identifier) and client_identifier=$this->client_identifier;";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from embed_libertas_images where module_starter = '$remove_type' and trans_identifier = $trans_identifier and client_identifier=$this->client_identifier;";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from embed_libertas_file where module_starter = '$remove_type' and trans_identifier = $trans_identifier and client_identifier=$this->client_identifier;";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from embed_libertas_form where module_starter = '$remove_type' and trans_identifier = $trans_identifier and client_identifier=$this->client_identifier;";
			$this->call_command("DB_QUERY",array($sql));
		} else {
			$trans_id_list .= "(" . join(",", $trans_list) . ")";
			$sql = "delete from embed_libertas_link where module_starter = '$remove_type' and (src_identifier in $trans_id_list or dst_identifier = $trans_identifier) and client_identifier=$this->client_identifier;";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from embed_libertas_images where module_starter = '$remove_type' and trans_identifier in $trans_id_list and client_identifier=$this->client_identifier;";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from embed_libertas_file where module_starter = '$remove_type' and trans_identifier in $trans_id_list and client_identifier=$this->client_identifier;";
			$this->call_command("DB_QUERY",array($sql));
			$sql = "delete from embed_libertas_form where module_starter = '$remove_type' and trans_identifier in $trans_id_list and client_identifier=$this->client_identifier;";
			$this->call_command("DB_QUERY",array($sql));
		}
	}
	
	function list_broken($parameters){
		$sql = "
select distinct ptd1.trans_title, ptd1.trans_identifier, embed_libertas_link.destination_url from embed_libertas_link 
 left outer join page_trans_data as ptd1 on ptd1.trans_identifier = src_identifier  
 left outer join page_trans_data as ptd2 on ptd2.trans_identifier = dst_identifier 
 where 
 	client_identifier = $this->client_identifier and 
 	menu_identifier = 0 
 order by 
	ptd1.trans_identifier";
	/*
	and 
		ptd2.trans_current_working_version =1 and 
		ptd2.trans_published_version=0 and 
		ptd1.trans_current_working_version =1 and 
		ptd1.trans_published_version=1
		*/
		//print "<p>$sql</p>";
		$restricted_access		= $this->check_parameters($_SESSION,	"SESSION_MANAGEMENT_ACCESS",	Array());
		$user_identifier		= $this->check_parameters($_SESSION,	"SESSION_USER_IDENTIFIER",	Array());
		$restricted_access_CSV	= " ".join(", ",$restricted_access).",";

		$result = $this->call_command("DB_QUERY",array($sql));
		$out  ="<module name='$this->module_name' display='form'><form label='Broken Links' method='post' name='broken_links'><text><![CDATA[<table cellspacing='1' cellpadding='3' bgcolor='#cccccc'>";
		if ($this->call_command("DB_NUM_ROWS",array($result))){
			$out .= "<tr bgcolor='#ffffff'><td class='bt'>Page Title</td><td class='bt'>Options</td></tr>";
			$prev="";
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				if ($prev != $r["trans_identifier"]){
					$prev = $r["trans_identifier"];
					$src_title	= $r["trans_title"];
					$src		= $r["trans_identifier"];
//					print "[$restricted_access_CSV][$src]";
					if (strlen($restricted_access_CSV)==2){
						$list_of_options="<a href='admin/index.php?command=PAGE_EDIT&identifier=$src'>".EDIT_EXISTING."</a>";
					} else {
						if (strpos($restricted_access_CSV," $src,")===false){
							$list_of_options="<a href='admin/index.php?command=EMBED_REQUEST_UPDATE&identifier=$src'>".LOCALE_EMAIL_THIS_PAGE."</a>";
						} else {
							if (($dst_lock != 0) && ($dst_lock != $user_identifier)){
								$list_of_options="<a href='admin/index.php?command=EMBED_REQUEST_UPDATE&identifier=$src'>".LOCALE_EMAIL_THIS_PAGE."</a>";
							} else {
								if ($dst_status!=4){
									$list_of_options="<a href='admin/index.php?command=PAGE_EDIT&identifier=$src'>".EDIT_EXISTING."</a>";
								} else {
									$list_of_options="<a href='admin/index.php?command=PAGE_COPY_VERSION&identifier=$src'>".LOCALE_NEW_COPY."</a>";
								}
							}
						}
					}
					$out .= "<tr bgcolor='#ffffff'><td><strong>$src_title</strong></td><td>$list_of_options</td></tr>";
				}
				$out .= "<tr bgcolor='#ffffff'><td colspan='2'><ul><li>".$r["destination_url"]."</li></ul></td></tr>";
			}
		} else {
			$out .= "<tr bgcolor='#ffffff'><td colspan='2'>Congratulations there are no known broken links.</td></tr>";
		}
		
		$out .= '</table>]]></text></form></module>';
		return $out;
	}
	
	// a function to remove all the occurances of a moudles data.
	function delete_module($parameters){
		$mi_type= $this->check_parameters($parameters,"mi_type");
		$sql ="delete from embed_libertas_image where client_identifier = $this->client_identifier and module_starter='$mi_type'";
		$this->call_command("DB_QUERY",Array($sql));
		$sql ="delete from embed_libertas_file where client_identifier = $this->client_identifier and module_starter='$mi_type'";
		$this->call_command("DB_QUERY",Array($sql));
		$sql ="delete from embed_libertas_form where client_identifier = $this->client_identifier and module_starter='$mi_type'";
		$this->call_command("DB_QUERY",Array($sql));
		$sql ="delete from embed_libertas_link where client_identifier = $this->client_identifier and module_starter='$mi_type'";
		$this->call_command("DB_QUERY",Array($sql));
		return "";
	}

	function debugtest(){
//		print 
		$this->check_location(
			Array(
				"embed_id"		=> 4431,
				"type"			=> "PAGE",
				"page"			=> 1278,
				"menu"			=> 651,
				"url"			=> 'ni_curriculum/ni_personal_development/lesson_8_drugs_and_human_rights_activity_-_last_updated_190204.php',
				"dst"			=> 1268
			)
		);
	}
}
?>
