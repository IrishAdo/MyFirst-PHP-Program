<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.rss.php
* @date 15 April 2004
*/
/**
*
*/

class rss extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_name_label			= "RSS Management Module (Presentation)";
	var $module_name				= "rss";
	var $module_admin				= "0";
	var $module_command				= "RSS_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_RSS";
	var $module_modify	 			= '$Date: 2005/03/03 15:59:16 $';
	var $module_version 			= '$Revision: 1.25 $';
	var $module_updated 			= '$Date: 2005/03/03 15:59:16 $';
	var $module_creation 			= "26/02/2004";
	
	/**
	*  Frequency list
	*/
	var $freq_list = array();
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
		if (strpos($user_command, $this->module_command)===0){
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
				return $this->module_version;
			}
			/**
			* Module Specific Function calls
			*/
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->display($parameter_list);
			}
			if ($user_command==$this->module_command."GET"){
				return $this->getRSS($parameter_list);
			}
			if ($user_command==$this->module_command."CACHE"){
				$parameter_list["store"] = 1;
				$this->cache($parameter_list);
			}
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                     S E T U P   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
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
		$this->load_locale("rss_admin");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		$this->page_size	= $this->check_prefs(Array("sp_page_size"));
//		$this->rss_external = $this->check_prefs(Array("sp_open_rss_external"	,"LOCALE_SP_OPEN_RSS_EXTERNAL"	,"default" => 'No'	, "options" => 'Yes:No'				, "module" => "RSSADMIN_",	"ECMS"));
		$this->freq_list = array(
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_1_VAL,LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_1_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_2_VAL,LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_2_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_3_VAL,LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_3_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_4_VAL,LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_4_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_5_VAL,LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_5_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_6_VAL,LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_6_TXT),
			array(LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_7_VAL,LOCALE_RSS_DOWNLOAD_FREQUENCY_OPTION_7_TXT)
		);
		return 1;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                   R S S   F E E D   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	/*************************************************************************************************************************
    * cache an RSS Feed
    *************************************************************************************************************************/
	function cache($parameters){
		$data_files		= $this->parent->site_directories["DATA_FILES_DIR"];
		$root			= $this->parent->site_directories["ROOT"];
		$identifier		= $this->check_parameters($parameters,"identifier");
		$extractable	= $this->check_parameters($parameters,"extractable");
		$store			= $this->check_parameters($parameters,"store",1);
		$sql 			= "select * from rss_feed where rss_client=$this->client_identifier and rss_status=1 and rss_identifier =$identifier";
		$rss_label		= "";
		$result  		= $this->parent->db_pointer->database_query($sql);
		$out 			= "";
		$xfilecontent	= "";
		//print "<li>".$sql."</li>";
    	while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$identifier			= $r["rss_identifier"];
			$filename 			= $r["rss_url"];
			$rss_label 			= $r["rss_label"];
			$rss_description	= $r["rss_description"];
			$last_dowloaded		= $r["rss_downloaded"];
			$refresh			= $r["rss_frequency"];
			$bulletlist			= $r["rss_bulletlist"];
			$rss_type			= $r["rss_type"];
			$rss_channel_image	= $r["rss_channel_image"];
			$extract_all		= $r["rss_extract_all_locations"];
			$external			= $r["rss_external"];
			$extractable		= $r["rss_extractable"];
			$number_of_items	= $r["rss_number_of_items"];
			$download_now 		= 0;
			/*************************************************************************************************************************
            * 
            *************************************************************************************************************************/
			$sql = "select * from menu_to_object 
						inner join menu_data on menu_identifier = mto_menu and  menu_client= mto_client
					where mto_object = $identifier and mto_client=$this->client_identifier and mto_module='$this->webContainer' and mto_publish=0";
			$menu_result  = $this->parent->db_pointer->database_query($sql);
			$mlist = Array();
			$mcount=0;
            while($r = $this->parent->db_pointer->database_fetch_array($menu_result)){
            	$mlist[count($mlist)] = Array($r["mto_menu"],$r["menu_url"]);
				$mcount++;
            }
            $this->parent->db_pointer->database_free_result($menu_result);
			/*
				if not cached or inneed of refresh update table for Recache
			*/
			
			$filecontent		= "";
			$sql = "select * from rss_feed_fields where rff_feed = $identifier and rff_client = $this->client_identifier";
			$field_result  = $this->parent->db_pointer->database_query($sql);
			$xfields  = Array();
			$xfields["bulletlist"] = $bulletlist;
			
			$fields="<fields>
						<field name='bulletlist'><![CDATA[$bulletlist]]></field>
						<field name='label'><![CDATA[$rss_label]]></field>
					";
			if ($rss_type!=2){
				$fields.="<field name='number_of_items'><![CDATA[$number_of_items]]></field>";
				$xfields["number_of_items"] = $number_of_items;
			} else {
				$fields.="<field name='number_of_items'><![CDATA[0]]></field>";
				$xfields["number_of_items"] = 0;
			}
            while($r = $this->parent->db_pointer->database_fetch_array($field_result)){
      	       		$fields .= "<field name='show'><![CDATA[".$r["rff_field"]."]]></field>";
					$xfields[$r["rff_field"]] = 1;
			}
			$fields 	.= "</fields>";
			$this->parent->db_pointer->database_free_result($field_result);
			$filecontent 		 = "";
			$xfilecontent 		 = "";

			$sql = "select * from file_info where file_client=$this->client_identifier and file_identifier=$rss_channel_image";
			$file_result  = $this->parent->db_pointer->database_query($sql);
			$channelimg="";
	        while($r = $this->parent->db_pointer->database_fetch_array($file_result)){
	        	$src_w = $r["file_width"];
	        	$src_h = $r["file_height"];
				$dir_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($r["file_directory"]));
	        	$src = $this->parent->base.$dir_path.$r["file_md5_tag"].$this->file_extension($r["file_name"]);
				$channelimg = "\n<image>
								<title>".$rss_label."</title>
								<url>http://".$this->parent->domain."".$src."</url>
								<link>http://".$this->parent->domain."</link>
								<width>$src_w</width>
								<height>$src_h</height>
								<description>".$r["file_label"]."</description>
							</image>";
				$xchannelimg = "<div class='image'><a href='http://".$this->parent->domain."'>
								<img src='http://".$this->parent->domain.$src."' alt='".$rss_label."' style='width:".$src_w.";height:".$src_h.";'/></a></div>";
	        }
			
	//		print $src_w." ".$src_h." ".$src;
	        $this->parent->db_pointer->database_free_result($file_result);

			$channel	 = "<title>".$rss_label."</title>\n";
			$channel	.= "<link>http://".$this->parent->domain."</link>\n";
			$channel	.= "<description>".$rss_description."</description>\n";
			$channel	.= "<pubDate>".$this->libertasGetDate("r")."</pubDate>\n";
			$channel	.= $channelimg;
			$xchannel	 = "";
//			print_r($xfields);
			if ($this->check_parameters($xfields,"Channel_Title","__NOT_FOUND__")!="__NOT_FOUND__"){
				if($mcount>0){
					$xchannel .= "<div class='rsslabel'><a href='http://".$this->parent->domain.$this->parent->base.$mlist[0][1]."'><span class='icon'><span class='text'>".$rss_label."</span></span></a></div>\n";
				} else {
					$xchannel .= "<div class='rsslabel'><a href='http://".$this->parent->domain.$this->parent->base."'><span class='icon'><span class='text'>".$rss_label."</span></span></a></div>\n";
				}
			}
			//print "<li>".__FILE__."@".__LINE__."<p>$xchannel</p></li>";
//			$this->exitprogram();
			$c=0;
			if ($this->check_parameters($xfields,"Channel_Description","__NOT_FOUND__")!="__NOT_FOUND__"){
				if($c==0){
					$c++;
					$xchannel	.= "<ul class='meta'>";
				}
				$xchannel	.= "<li class='meta'><span>".$rss_description."</span></li>";
			}
			if ($this->check_parameters($xfields,"Channel_Date Publish","__NOT_FOUND__")!="__NOT_FOUND__"){
				if($c==0){
					$c++;
					$xchannel	.= "<ul class='meta'>";
				}
				$xchannel	.= "<li class='contentpos'><span>".$this->libertasGetDate("d/m/Y")."</span></li>";
			}		
			if ($xchannelimg != ""){
				$xchannel	.= $xchannelimg;				
			}
			//if ($_SERVER['REMOTE_ADDR'] == "61.5.139.12")			
			//print "channel  { ".$channel." }";
			
			switch($rss_type){
				case 1: // default
					$val= $this->load_latest($identifier, $channel, $xchannel, $xfields, $number_of_items,$extract_all, $store, 0, $mlist);
					$filecontent	.= $val["RSS"];
					$xfilecontent	.= $val["XHTML"];
					break;
				case 2: // accumulative
					$val 			= $this->load_accumulitive($identifier, $channel, $xchannel, $xfields, $extract_all, $store);
					$filecontent	.= $val["RSS"];
					$xfilecontent	.= $val["XHTML"];
					break;
				case 3: /// random
					$val 			= $this->load_random($identifier, $channel, $xchannel, $xfields, $number_of_items,$extract_all, $store);
//					print_r($val);
					$filecontent	.= $val["RSS"];
					$xfilecontent	.= $val["XHTML"];
					break;
			}
			$filecontent.="";			
			$xfilecontent="<module name=\"RSS\" display=\"TEXT\"><label><![CDATA[$rss_label]]></label>\n<text><![CDATA[$xfilecontent]]></text></module>";
			//if ($_SERVER['REMOTE_ADDR'] == "61.5.139.12")			
			//print "[".$store ." && ".strlen($filecontent)."]  { ".$xfilecontent." }";
			if (($store != 0) && (strlen($filecontent)>0)){
				$fname = $data_files."/rss_feed_".$this->client_identifier."_".$identifier;
				$fp = fopen($fname.".xml", 'w');
				fwrite($fp, $filecontent);
				fclose($fp);
				$um = umask(0);
				@chmod($fname.".xml", LS__FILE_PERMISSION);
				umask($um);
				$fp2 = fopen($fname.".xhtml", 'w');
				fwrite($fp2, $xfilecontent);
				fclose($fp2);
				$um = umask(0);
				@chmod($fname.".xhtml", LS__FILE_PERMISSION);
				umask($um);
				if ($extractable == 1){
					if(!file_exists($root."/-rss/index.php")){
						$um =umask(0);
						@mkdir($root."/-rss",LS__DIR_PERMISSION);
						umask($um);
						$fname = $root."/-rss/index.php";
						$fp = fopen($fname, 'w');
						fwrite($fp, "<h1>Sorry no access to this directory</h1>");
						fclose($fp);
						$um = umask(0);
						@chmod($fname, LS__FILE_PERMISSION);
						umask($um);
					}
					$fname = $root."/-rss/".$this->make_uri($rss_label).".xml";
					$fp = fopen($fname, 'w');
					fwrite($fp, $filecontent);
					fclose($fp);
					$um = umask(0);
					@chmod($fname, LS__FILE_PERMISSION);
					umask($um);
				}
			}	
			$out =$filecontent;
   	    }
       	$this->parent->db_pointer->database_free_result($result);
		$time = $this->libertasGetTime();
		$sql 			= "update rss_feed set rss_downloaded='$time' where rss_client=$this->client_identifier and rss_status=1 and rss_identifier =$identifier";
		$this->parent->db_pointer->database_query($sql);
//		print "[exit]";
//		return $xfilecontent;
	}

	function display($parameters){
		$cmd = $this->check_parameters($parameters,"command");
		$pos = $this->check_parameters($parameters,"__layout_position");
		if (($pos==2 || $pos==3) && $cmd!=""){
			return "";
		}
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"fn::display()",__LINE__,"".print_r($parameters,true).""));}
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$owner 		= $this->check_parameters($parameters,"wo_owner_id");
//		print "<li>$owner</li>";
		if($owner>0){
//			$this->rss_external = $this->check_prefs(Array("sp_open_rss_external"	,"LOCALE_SP_OPEN_RSS_EXTERNAL"	,"default" => 'No'	, "options" => 'Yes:No'				, "module" => "RSSADMIN_",	"ECMS"));
			$sql = "select * from rss_feed 
						left outer join menu_to_object on 
							(mto_client = rss_client and mto_object=rss_identifier and mto_module='RSS_' and 
								(mto_menu=".$parameters["current_menu_location"]." or 
									(mto_menu is null and rss_all_locations=1)
								) and mto_publish=1
							) 
						left outer join menu_data on menu_to_object.mto_menu = menu_data.menu_identifier and mto_client = menu_client
					where rss_client=$this->client_identifier and rss_status=1 and rss_identifier =$owner 
						and ( 
							(rss_all_locations=0 and  menu_url = '".$this->parent->script."')
						 or 
							(menu_url is null and  rss_all_locations=1)
						)
					order by rss_label";
			//print "<li>".__FILE__."@".__LINE__."<pre>$sql</pre></li>";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			$out ="";
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$identifier				= $r["rss_identifier"];
				$rss_label 				= html_entity_decode($r["rss_label"]);
				$filename 				= $r["rss_url"];
				$rss_description		= $r["rss_description"];
				$last_dowloaded	 		= $r["rss_downloaded"];
				$refresh				= $r["rss_frequency"];
				$bulletlist				= $r["rss_bulletlist"];
				$rss_type				= $r["rss_type"];
				$extract_all			= $r["rss_extract_all_locations"];
				$external				= $r["rss_external"];
				$rss_new_window			= $r["rss_new_window"];
				$rss_channel_image		= $r["rss_channel_image"];
				$number_of_items		= $r["rss_number_of_items"];
				$failed_downloads		= $r["rss_error_count"];
				$last_download_attempt	= $r["rss_last_download_attempt"];
				$download_now 			= 0;
				//print_r($r);
				/*
					if not cached or in need of refresh update table for Recache
				*/
				$fname = $data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xhtml";
				/*
	            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	            - if it has expired then go get it
	            -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	            */
				$now = $this->libertasGetTime();
				//print "[$now, $last_download_attempt, $last_dowloaded, $refresh, (".($now - $refresh).")]";
				
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"",__LINE__,"[$now - $refresh] > [$last_dowloaded] [".($now - $refresh)."]"));}
				if (
					($last_dowloaded==0) || 
					($now - $refresh > $last_dowloaded) || 
					(
						($last_download_attempt!=0) && 
						($now-1800 > $last_download_attempt)
					)
				){
//					print "<li>Download now [$rss_label]</li>";
					$download_now = 1;
					$sp_rss_downloads = $this->check_prefs(Array("sp_rss_downloads"));
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>Download ($sp_rss_downloads  < $failed_downloads)</pre>"));}
					if ( $sp_rss_downloads  < $failed_downloads){
						$download_now = 0;
					} else {
						$sql = "update rss_feed set rss_needs_cached=1 where rss_client = $this->client_identifier and rss_identifier = $identifier ";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
						$this->parent->db_pointer->database_query($sql);
						$this->call_command("SCHEDULE_ADD",Array("cmd"=>"RSSADMIN_CACHE", "params"=>Array("identifier"=>$identifier)));
					}
				}


//				$download_now = 1;


				if($external==1){
					
					//$download_now = 0;
					$t = $this->libertasGetTime();
					/*
						always pull the cached file if it exists.
					*/
					
					$ok = 0;
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"check existance",__LINE__,"<pre>$fname, $download_now, $identifier</pre>"));}
					if (!file_exists($fname) || $download_now == 1){
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"check existance",__LINE__,"<pre>file not found or download required, loading cache , $identifier</pre>"));}
						$out .= $this->call_command("RSSADMIN_EXTERNAL_CACHE", Array("identifier"=>$identifier));
						$ok = 1;
						if ($out==""){
							$ok = 0;
						}
					} 
					if ($ok == 0){
						if (file_exists($fname)){							
							$content_array = file($fname);
							$out .= implode("", $content_array);
						}
					}
				} else {
					/**
	                * INTERNAL RSS FEED
	                */
					$user_id = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
					/**
	                * logged in users get an uncached version as group restrictions are changed from anonymous up
	                */
					$ok = 0;
					if (!file_exists($fname) || $download_now == 1 || ($user_id != 0)){
						$store=1;
						if($user_id!=0){
							$store = 0;
						}
						$ok = 1;
						$out .= $this->cache(Array("identifier" => $identifier, "store"=>$store));

						if ($out==""){
							$ok = 0;
						}
					} 
					if ($ok == 0){
						if (file_exists($fname)){
							$content_array = file($fname);
							$out .= implode("", $content_array);
						}
					}
				}
	   	    }
	       	$this->parent->db_pointer->database_free_result($result);
			return $out;
		} 
		return "";
	}

	
	function load_latest($identifier,$channel, $xchannel, $xfields, $number_of_items, $extract_all, $store, $preview=0, $menulist=Array()){
//		print "<li>".__FILE__."@".__LINE__."<p>$identifier,$channel, $xchannel, ".print_r($xfields,true).", $number_of_items, $extract_all, $store, $preview</p></li>";
//		$this->exitprogram();
		$data_files	= $this->parent->site_directories["DATA_FILES_DIR"];
		$fname		= $data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml";
		$channel	= '<rss version="2.0"><channel>'.$channel;
		$out 		= "";
		$xout 		= "";
		$xlist 		= "";
		$join		= "";
		$where 		= "";
		//$xchannel	= "";
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary"
			)
		);
		if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp) && $store==0){
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
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier and group_access_to_page.client_identifier = page_trans_data.trans_client
					 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_access_to_page.menu_identifier ";
		}
		if ($extract_all==0){
			if(count($menulist)>0){
				$menu_list="";
				for($mi=0;$mi<count($menulist);$mi++){
					if($menu_list!=""){
						$menu_list.=", ";
					}
					$menu_list .= $menulist[$mi][0];
				}
			} else {
			if ($store>0){
				$menu_list = $this->call_command("LAYOUT_ANONYMOUS_DISPLAY_IDS");
			} else {
				$menu_list = $this->call_command("LAYOUT_DISPLAY_IDS");
			}
			}
			if (strlen($menu_list)>0){
				$where .= " menu_access_to_page.menu_identifier in ($menu_list) and ";
			}
			$sql = "select distinct menu_data.menu_identifier,menu_data.menu_url, page_trans_data.*,".$summary_parts["return_field"]." from menu_to_object 
						inner join menu_access_to_page on menu_access_to_page.menu_identifier = mto_menu and menu_access_to_page.client_identifier = mto_client and menu_access_to_page.title_page=0
						inner join menu_data on menu_data.menu_identifier= mto_menu and menu_data.menu_client = mto_client
						inner join page_trans_data on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier and trans_client = menu_access_to_page.client_identifier and trans_published_version =1 and trans_doc_status =4 
						".$summary_parts["join"]."
						$join
					where $where mto_client=$this->client_identifier and mto_publish=0 and mto_object = $identifier ".$summary_parts["where"]."
					order by trans_date_available desc
			";
		} else {
			 //page_trans_data.trans_title, page_trans_data.trans_date_publish, page_trans_data.trans_page
			$sql = "select distinct menu_data.menu_identifier,menu_data.menu_url, page_trans_data.*
			,".$summary_parts["return_field"]." from page_trans_data 
					inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier and trans_client = menu_access_to_page.client_identifier and trans_published_version =1 and trans_doc_status =4 and menu_access_to_page.title_page=0  
					inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier and menu_data.menu_client = menu_access_to_page.client_identifier
					".$summary_parts["join"]."
					$join
				where $where trans_client=$this->client_identifier ".$summary_parts["where"]."
				order by trans_date_available desc";
		}
			
		$c=0;
		$result 	= $this->parent->db_pointer->database_query($sql);
		$d			= "";
		$url		= "";
		$have 		= Array();
		$blistitem="";
		$x="";
		/** Check if RSS image is selected then no need to set rss identifier. No need to execute setTallRSS javascript */
		if (strpos($xchannel,"<img")>0) {
			$x	.=	"<ul class='rss' id='rss'>";
		}
		else {
			$x	.=	"<ul class='rss' id='rss-".$identifier."'>";
		}
		$d = $this->libertasGetDate("Y/m/d H:i:s");
		while(($r	= $this->parent->db_pointer->database_fetch_array($result)) && count($have)<$number_of_items){
			if($c==0){
				$d = $r["trans_date_publish"];
				$c++;
			}
			
			/** Check to see if date remove is less than today */
			if (($this->makeTimeStamp($r["trans_date_remove"]) > 0) && ($this->makeTimeStamp($r["trans_date_remove"])<time())){
				continue;
			}
			
			$trans_title	= chop($this->check_parameters($r,"trans_title",""));
//			$trans_id	 	= $this->check_parameters($r,"trans_identifier","");
			$trans_page 	= $this->check_parameters($r,"trans_page","");
			$menu_url 		= $this->check_parameters($r,"menu_url");
			$menu_id 		= $this->check_parameters($r,"menu_identifier",-1);
			if (!in_array($trans_page, $have)){

				$url = $this->translate_to_filename($menu_url,$trans_title,$trans_page,$menu_id);
				$have[count($have)] = $trans_page;
//				$url = "";
    	   		$out.= "<item>\n";
	       		$out.= "	<title>".html_entity_decode($r["trans_title"])."</title>\n";
       			$out.= "	<link>http://".$this->parent->domain.$this->parent->base.$url."</link>\n";
				$out.= "	<description>".$r["trans_summary1"]."</description>\n";
				$out.= "	<pubDate>".$this->libertasGetDate("r",strtotime($r["trans_date_publish"]))."</pubDate>\n";
				$out.= "</item>\n";
				if ($this->check_parameters($xfields,"bulletlist",0)!=0){
//					$blistitem	.=	"<li><a href='[[script]]#jump_to_".$identifier."_".$c."'>".html_entity_decode($r["trans_title"])."</a></li>";
				}
				if ($this->check_parameters($xfields,"Story_Description","__NOT_FOUND__")!="__NOT_FOUND__"){
					$x .=	"<li class='withsummary'>";
				} else {
					$x .=	"<li class='storyitem'>";
				}
//				print_r($xfields);
				$x .=   	"<a href='http://".$this->parent->domain.$this->parent->base.$url."'  title=\"";
				if($this->check_parameters($xfields,"Story_Description","__NOT_FOUND__")!="__NOT_FOUND__"){
					$x .=   "Read more about (".str_replace("\"","'", html_entity_decode($r["trans_title"])).")";
				} else {
					$x .=   str_replace("\"","'", strip_tags(html_entity_decode($r["trans_summary1"])));
				}
				$x .=   	"\"><span class='icon'><span class='text'>".html_entity_decode($r["trans_title"])."</span></span></a>";
				if ($this->check_parameters($xfields,"Story_Publish Date","__NOT_FOUND__")!="__NOT_FOUND__"){
					$x .=	"<div class='contentpos'><span>".Date("d/m/Y",strtotime($r["trans_date_publish"]))."</span></div>";
				}
				if ($this->check_parameters($xfields,"Story_Description","__NOT_FOUND__")!="__NOT_FOUND__"){
					$x .=	"<div class='contentpos'><span>".str_replace("\"","'", strip_tags(html_entity_decode($r["trans_summary1"])))."</span></div>";
				}
				$x .=	"</li>";
			}
		}
		$x .=	"</ul>";
		$channel	.= "<lastBuildDate>".$this->libertasGetDate("r",strtotime($d))."</lastBuildDate>\n";
		if ($this->check_parameters($xfields,"Channel_Last Build Date","__NOT_FOUND__")!="__NOT_FOUND__"){
			$xchannel	= "<li class='contentpos'><span>Last Build :: $d</span></li>";
		}
	    $this->parent->db_pointer->database_free_result($result);
		$out .= "</channel>\n</rss>";
		return Array("RSS"=>$channel.$out, "XHTML"=>$xchannel."$blistitem".$x);
	}


	function load_accumulitive($identifier,$channel, $xchannel, $xfields, $extract_all, $store){
		$data_files	= $this->parent->site_directories["DATA_FILES_DIR"];
		$fname		= $data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml";
		$channel	= '<rss version="2.0"><channel>'.$channel;
		$out 		= "";
		$join		= "";
		$where 		= "";
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary"
			)
		);
		if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp) && $store==0){
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
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier and group_access_to_page.client_identifier = page_trans_data.trans_client
					 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_access_to_page.menu_identifier ";
		}
		if ($store>0){
			$menu_list = $this->call_command("LAYOUT_ANONYMOUS_DISPLAY_IDS");
		} else {
			$menu_list = $this->call_command("LAYOUT_DISPLAY_IDS");
		}
		if (strlen($menu_list)>0){
			$where .= " menu_access_to_page.menu_identifier in ($menu_list) and ";
		}
		$sql = "
			SELECT distinct menu_to_object.*, menu_data.menu_url, page_trans_data.*, menu_access_to_page.*, ".$summary_parts["return_field"]." 
				FROM page_trans_data
					inner join menu_access_to_page on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier and menu_access_to_page.title_page=0
					inner join menu_data on menu_data.menu_identifier = menu_access_to_page.menu_identifier
					inner join  menu_to_object on mto_menu = menu_access_to_page.menu_identifier
					".$summary_parts["join"]."
					$join
				where $where mto_publish = 0 and trans_published_version = 1 and trans_doc_status=4 ".$summary_parts["where"]."
				order by  mto_menu, page_trans_data.trans_date_publish desc
			";
		$c=0;
		$result 	= $this->parent->db_pointer->database_query($sql);
		$d			= "";
		$url		= "";
		$have 		= Array();
		$hmenu		= Array();
		$items 		= Array();
		$xitems 	= Array();
		$blist 		= Array();
		while(($r	= $this->parent->db_pointer->database_fetch_array($result))){
			if($c==0){
				$d = $r["trans_date_publish"];
				$c++;
			}
			$mto_extract_num= chop($this->check_parameters($r,"mto_extract_num",1));
			$trans_title	= chop($this->check_parameters($r,"trans_title",""));
			$trans_page 	= $this->check_parameters($r,"trans_page","");
			$menu_url 		= $this->check_parameters($r,"menu_url");
			$menu_id 		= $this->check_parameters($r,"menu_identifier",-1);
			if (!in_array($trans_page, $have) && $this->check_parameters($hmenu,"menu_$menu_id",-1)<$mto_extract_num){
				$url = $this->translate_to_filename($menu_url,$trans_title,$trans_page,$menu_id);
				$have[count($have)] = $trans_page;
				if ($this->check_parameters($hmenu,"menu_$menu_id",-1)==-1){
					$hmenu["menu_$menu_id"] = 0;
				}
				$hmenu["menu_$menu_id"] ++;
    	   		$o= "<item>\n";
	       		$o.= "	<title>".html_entity_decode($r["trans_title"])."</title>\n";
       			$o.= "	<link>http://".$this->parent->domain.$this->parent->base.$url."</link>\n";
				$o.= "	<description>".$r["trans_summary1"]."</description>\n";
				$o.= "	<pubDate>".$this->libertasGetDate("r",strtotime($r["trans_date_publish"]))."</pubDate>\n";
				$o.= "</item>\n";
				$blistitem="";
				if ($this->check_parameters($xfields,"bulletlist",0)!=0){
					$blistitem	=	"<li><a href='[[script]]#jump_to_".$identifier."_".$c."'>".html_entity_decode($r["trans_title"])."</a></li>";
				}
				$ok=0;
				if (
					($this->check_parameters($xfields,"Story_Publish Date","__NOT_FOUND__")!="__NOT_FOUND__") ||
					($this->check_parameters($xfields,"Story_Description","__NOT_FOUND__")!="__NOT_FOUND__")
				   ){
				   $ok=1;
				 }
					if ($ok==1){
						$x	=	"<li class='withsummary'>";
					} else {
						$x	=	"<li class='storyitem'>";
					}
					if ($this->check_parameters($xfields,"Story_Description","__NOT_FOUND__")!="__NOT_FOUND__"){
						$title = strip_tags(html_entity_decode(html_entity_decode($r["trans_title"])));
					} else {
						$title = html_entity_decode(html_entity_decode($r["trans_title"]));
					}
					$x .=	"	<a href='http://".$this->parent->domain.$this->parent->base.$url."' title='$title'><span class='icon'><span class='text'>".html_entity_decode($r["trans_title"])."</span></span></a>";
					if ($this->check_parameters($xfields,"Story_Publish Date","__NOT_FOUND__")!="__NOT_FOUND__"){
						$x .=	"	<div class='contentpos'>".$this->libertasGetDate("d/m/Y",strtotime($r["trans_date_publish"]))."</div>";
					}
					if ($this->check_parameters($xfields,"Story_Description","__NOT_FOUND__")!="__NOT_FOUND__"){
						$x .=	"	<div class='contentpos'>".html_entity_decode($r["trans_summary1"])."</div>";
					}
					$x .=	"</li>";
					if ($this->check_parameters($xitems,$r["trans_date_publish"],"")==""){
						$xitems[$r["trans_date_publish"]]="";
					}
					$xitems[$r["trans_date_publish"]] .= $x;
					if ($this->check_parameters($items,$r["trans_date_publish"],"")==""){
						$items[$r["trans_date_publish"]]="";
					}
					$items[$r["trans_date_publish"]] .= $o;
					if ($this->check_parameters($xfields,"bulletlist",0)!=0){
						if ($this->check_parameters($blist,$r["trans_date_publish"],"")==""){
							$blist[$r["trans_date_publish"]]="";
						}
						$blist[$r["trans_date_publish"]] .= $blistitem;
					}
				}
			}
		krsort($items);
		krsort($xitems);
		$xlist	= "";
		$out	= implode("",$items);
		$xout	= implode("",$xitems);
		if ($this->check_parameters($xfields,"Channel_Last Build Date","__NOT_FOUND__")!="__NOT_FOUND__"){
			$channel	.= "<lastBuildDate>".$this->libertasGetDate("r",strtotime($d))."</lastBuildDate>\n";
			$xchannel	.= "<li class='meta'>".$this->libertasGetDate("r",strtotime($d))."</li>";
		}
	    $this->parent->db_pointer->database_free_result($result);
		$out .= "</channel></rss>";
		if ($xout!=""){
			$xout ="<ul class='rss'>$xout</ul>";
		}
		return Array("RSS"=>$channel.$out, "XHTML"=>$xchannel."$xlist".$xout);
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
	
	function getRSS($parameters){
		$identifier = $this->check_parameters($parameters,"unset_identifier",$this->check_parameters($parameters,"identifier",-1));
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$sql=  "select * from rss_feed where rss_extractable=1 and rss_identifier =$identifier and rss_client=$this->client_identifier";
		$identifier = -1; // if sql not found then don't load
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$identifier = $r["rss_identifier"];
        }
        $this->parent->db_pointer->database_free_result($result);
		if ($identifier!=-1){
			$filename  = $data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml";
			if (file_exists($filename)){
				/*******************Uncommit & Commit by Ali to Remove User Access Logs***********************/
				$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_RSS_DOWNLOAD__",$parameters));
			    /********************************************************/////////////
				@ob_end_clean();
				header("Pragma: public");
				header("Expires: 0"); // set expiration time
				header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	// browser must download file from server instead of cache
	
	// force download dialog
				header("Content-Type: text/xml");
	// use the Content-Disposition header to supply a recommended filename 
				header("Content-Disposition: filename=livefeed.rss;");
	
	/*
	The Content-transfer-encoding header should be binary, since the file will be read 
	directly from the disk and the raw bytes passed to the downloading computer.
	The Content-length header is useful to set for downloads. The browser will be able to 
	show a progress meter as a file downloads. The content-lenght can be determines by 
	filesize function returns the size of a file. 
	*/
				header("Content-Transfer-Encoding: binary");
				header("Content-Length: ".filesize($filename));
				$fp = fopen("$filename","rb");
				fpassthru($fp); 
				$this->exitprogram();
			} else {
				/*******************Uncommit & Commit by Ali to Remove User Access Logs***********************/
				$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_RSS_DOWNLOAD_NO_FILE__",$parameters));
				/*****************************************************/////////////
				$out ="<module name=\"files\" display=\"confirmation\">";
				$out .= "<text><![CDATA[".LOCALE_SORRY_NO_FILE."]]></text>";
				$out .="</module>";
				return $out;
			}
		} else {
			/*******************Uncommit & Commit by Ali to Remove User Access Logs***********************/
			$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_RSS_DOWNLOAD_NO_FILE__",$parameters));
			/*********************************************************/
			$out ="<module name=\"files\" display=\"confirmation\">";
			$out .= "<text><![CDATA[".LOCALE_SORRY_NO_FILE."]]></text>";
			$out .="</module>";
			return $out;
		}
	}
	/*************************************************************************************************************************
    * generate random entries
    *************************************************************************************************************************/
	function load_random($identifier,$channel, $xchannel, $xfields, $number_of_items, $extract_all, $store, $preview=0){
		$data_files	= $this->parent->site_directories["DATA_FILES_DIR"];
		$fname		= $data_files."/rss_feed_".$this->client_identifier."_".$identifier.".xml";
		$channel	= '<rss version="2.0"><channel>'.$channel;
		$out 		= "";
		$xout 		= "";
		$xlist 		= "";
		$join		= "";
		$where 		= "";
		//$xchannel	= "";
		//print "[$xchannel]";
		$summary_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", Array(
				"table_as"			=> "ptd2",
				"field_as"			=> "trans_summary1",
				"identifier_field"	=> "page_trans_data.trans_identifier",
				"module_command"	=> "PAGE_",
				"client_field"		=> "trans_client",
				"mi_field"			=> "summary"
			)
		);
		
		if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
			$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
			if (is_array($grp) && $store==0){
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
			$join.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier and group_access_to_page.client_identifier = page_trans_data.trans_client
					 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_access_to_page.menu_identifier ";
		}
		if ($extract_all==0){
			if ($store>0){
				$menu_list = $this->call_command("LAYOUT_ANONYMOUS_DISPLAY_IDS");
			} else {
				$menu_list = $this->call_command("LAYOUT_DISPLAY_IDS");
			}
			if (strlen($menu_list)>0){
				$where .= " menu_access_to_page.menu_identifier in ($menu_list) and ";
			}
			$sql = "select distinct menu_data.menu_identifier,menu_data.menu_url, page_trans_data.*,".$summary_parts["return_field"]." from menu_to_object 
					inner join menu_access_to_page on menu_access_to_page.menu_identifier = mto_menu and menu_access_to_page.client_identifier = mto_client and menu_access_to_page.title_page=0
					inner join menu_data on menu_data.menu_identifier= mto_menu and menu_data.menu_client = mto_client
					inner join page_trans_data on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier and trans_client = menu_access_to_page.client_identifier and trans_published_version =1 and trans_doc_status =4
					".$summary_parts["join"]."
					$join
				where $where mto_client=$this->client_identifier and mto_publish=0 and mto_object = $identifier ".$summary_parts["where"]."
				order by trans_date_publish desc
			";
		} else {
			$sql = "select distinct menu_data.menu_identifier,menu_data.menu_url, page_trans_data.*,".$summary_parts["return_field"]." from page_trans_data 
					inner join menu_access_to_page on menu_access_to_page.trans_identifier = page_trans_data.trans_identifier and trans_client = menu_access_to_page.client_identifier and trans_published_version =1 and trans_doc_status =4 and menu_access_to_page.title_page=0
					inner join menu_data on menu_data.menu_identifier= menu_access_to_page.menu_identifier and menu_data.menu_client = menu_access_to_page.client_identifier
					".$summary_parts["join"]."
					$join
				where $where trans_client=$this->client_identifier ".$summary_parts["where"]."
				order by trans_date_publish desc";
		}
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$c=0;
		$result 	= $this->parent->db_pointer->database_query($sql);
		$d			= "";
		$url		= "";
		$have 		= Array();
		$blistitem="";
		$x="";
		$x	.=	"<ul class='rss' id='rss-".$identifier."'>";
		$d = $this->libertasGetDate("Y/m/d H:i:s");
		$dataArray= Array();
		$keepArray= Array();
		while($r	= $this->parent->db_pointer->database_fetch_array($result)){
			$dataArray[count($dataArray)] = $r["trans_identifier"];
		}
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r($dataArray, true)."</pre></li>";
		$max = count($dataArray);
//		if ($max>$number_of_items){
//			$max=		$number_of_items;
//		}
		$ptr	= $this->call_command("DB_SEEK",Array($result,0));
		for ($i=0;$i<$number_of_items;$i++){
			$randomIndex = rand(0,$max);
			if(!in_array($randomIndex,$keepArray)){
				$keepArray[count($keepArray)] = $randomIndex;
			}
		}
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($keepArray, true)."</pre></li>";
		$chooseIndex = 0;
		while($r	= $this->parent->db_pointer->database_fetch_array($result)){
			if(in_array($chooseIndex, $keepArray)){
				$trans_title	= chop($this->check_parameters($r,"trans_title",""));
//				print "<li>$chooseIndex - $trans_title</li>";
//				$trans_id	 	= $this->check_parameters($r,"trans_identifier","");
				$trans_page 	= $this->check_parameters($r,"trans_page","");
				$menu_url 		= $this->check_parameters($r,"menu_url");
				$menu_id 		= $this->check_parameters($r,"menu_identifier",-1);
//				if (!in_array($trans_page, $have)){
					$url = $this->translate_to_filename($menu_url,$trans_title,$trans_page,$menu_id);
//					$have[count($have)] = $trans_page;
//					$url = "";
   		 	   		$out.= "<item>\n";
		       		$out.= "	<title>".html_entity_decode($trans_title)."</title>\n";
   	    			$out.= "	<link>http://".$this->parent->domain.$this->parent->base.$url."</link>\n";
					$out.= "	<description>".$r["trans_summary1"]."</description>\n";
					$out.= "	<pubDate>".$this->libertasGetDate("r",strtotime($r["trans_date_publish"]))."</pubDate>\n";
					$out.= "</item>\n";
					if ($this->check_parameters($xfields,"bulletlist",0)!=0){
//						$blistitem	.=	"<li><a href='[[script]]#jump_to_".$identifier."_".$c."'>".html_entity_decode($r["trans_title"])."</a></li>";
					}
					if ($this->check_parameters($xfields,"Story_Description","__NOT_FOUND__")!="__NOT_FOUND__"){
						$x .=	"<li class='withsummary'>";
						$title = strip_tags(html_entity_decode(html_entity_decode($r["trans_title"])));
					} else {
						$x .=	"<li class='storyitem'>";
						$title = str_replace("'","",strip_tags(html_entity_decode(html_entity_decode($r["trans_summary1"]))));
					}
//					print_r($xfields);
					$x .=   	"<a href='http://".$this->parent->domain.$this->parent->base.$url."' title='$title'><span class='icon'><span class='text'>".html_entity_decode(html_entity_decode($r["trans_title"]))."</span></span></a>";
					if ($this->check_parameters($xfields,"Story_Publish Date","__NOT_FOUND__")!="__NOT_FOUND__"){
						$x .=	"<div class='contentpos'><span>".Date("d/m/Y",strtotime($r["trans_date_publish"]))."</span></div>";
					}
					if ($this->check_parameters($xfields,"Story_Description","__NOT_FOUND__")!="__NOT_FOUND__"){
						$x .=	"<div class='contentpos'><span>".html_entity_decode($r["trans_summary1"])."</span></div>";
					}
					$x .=	"</li>\n";
//				}
			}
			$chooseIndex++;
		}
		$x .=	"</ul>";
		$channel	.= "<lastBuildDate>".$this->libertasGetDate("r",strtotime($d))."</lastBuildDate>\n";
		if ($this->check_parameters($xfields,"Channel_Last Build Date","__NOT_FOUND__")!="__NOT_FOUND__"){
			$xchannel	= "<li class='contentpos'><span>Last Build :: $d</span></li>";
		}
	    $this->parent->db_pointer->database_free_result($result);
		$out .= "</channel>\n</rss>";
		return Array("RSS"=>$channel.$out, "XHTML"=>$xchannel.$x);
	}

}
?>