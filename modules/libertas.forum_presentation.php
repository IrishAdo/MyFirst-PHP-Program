<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.forum.php
* @date 09 Oct 2002
*/
/**
* Original Module split into two (admin and presentation)
*/
class forum extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name				= "forum";
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_label				= "MANAGEMENT_FORUM";
	var $module_name_label			= "Forum Manager Presentation Module";
	var $module_admin				= "0";
	var $can_log_search				= true;
	var $module_channels			= array("FORUM_DISPLAY");
	var $module_debug				= false;
	var $module_creation			= "13/09/2002";
	var $module_modify	 			= '$Date: 2005/03/21 15:00:28 $';
	var $module_version 			= '$Revision: 1.7 $';
	var $module_command				= "FORUM_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "FORUM_"; 		// key for Memo_info etc ...
	var $has_module_contact			= 0;
	var $has_module_group			= 0;
	var $display_options			= null;
	
		
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
//		print "<li>$user_command</li>";
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
			if ($user_command==$this->module_command."GET_CREATION"){
				return $this->get_module_creation();
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			if ($user_command==$this->module_command."THREAD_SAVE"){
				$forum = $this->forum_thread_save($parameter_list);
				$parameter_list["forum_identifier"] = $forum;
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>"index.php"));
			}
			if ($user_command==$this->module_command."VIEW_THREADS"){
				return $this->forum_view_thread($parameter_list);
			}
			if ($user_command==$this->module_command."THREAD_VIEW_ENTRY"){
				return $this->thread_display($parameter_list);
			}
			if ($user_command==$this->module_command."THREAD_GENERATE"){
				return $this->forum_thread_generation($parameter_list);
			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->forum_display($parameter_list);
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	
	/**
	* Initialise function
	*
	* This function will initialise some variables for this modules functions to use.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise",__LINE__,""));
		}
		$this->load_locale("forum");
		$this->page_size	= $this->check_prefs(Array("sp_page_size"));
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		
		/**
		* define the filtering information that is available
		*/
		$this->display_options		= array(
			array (0,"Order by Date Created (oldest first)"	,"FORUM_creation_date Asc"),
			array (1,"Order by Date Created (newest first)"	,"FORUM_creation_date desc"),
			array (2,"Order by Title A -> Z"				,"FORUM_title asc"),
			array (3,"Order by Title Z -> A"				,"FORUM_title desc")
		);
		
		$group_access = $this->check_parameters($_SESSION,"SESSION_GROUP_ACCESS");
		
		$this->module_admin_access		= 0;
		$access_list = $group_access;
		if (!is_array($access_list)){
			$access = Array();
			$access[0] = $access_list;
		}else{
			$access = $access_list;
		}
		$this->module_approver_access=0;
		
		for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
			if (
					("FORUM_ALL" == $access[$index]) || 
					("ALL"==$access[$index]) || 
					("FORUM_APPROVER"==$access[$index])
				){
				$this->module_approver_access=1;
			}
		}
	}
	/*************************************************************************************************************************
    * display the list of forums in this section
	*
	* if only one forum then skip to this screen and redirect to that forums threads as there is no need to display one entry
    *************************************************************************************************************************/
	function forum_display($parameters){
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_display",__LINE__,"".print_r($parameters,true).""));
		}
		if($this->check_parameters($parameters,"command","")==""){
			$out="";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"FORUM_display",__LINE__,""));
			}
			$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", 
				Array(
					"table_as"			=> "forum_data",
					"field_as"			=> "forum_description",
					"identifier_field"	=> "forum.forum_identifier",
					"module_command"	=> $this->webContainer,
					"client_field"		=> "forum_client",
					"mi_field"			=> "forum_description"
				)
			);
			/*ZIA
			$sql = "Select forum_category.fc_label, ".$body_parts["return_field"].", metadata_details.md_title as forum_label, metadata_details.md_description, forum_identifier ,  count(forum_thread.forum_thread_identifier) as total_threads, menu_data.menu_label, sum(forum_thread_counter.ftc_posts) as total_posts from forum 
					inner join metadata_details on md_link_id=forum_identifier and md_client=$this->client_identifier and md_module='FORUM_'
				".$body_parts["join"]."
				inner join menu_data on forum_location=menu_identifier  and menu_client = forum_client
				left outer join forum_thread on forum_identifier = forum_thread_forum and forum_thread_parent = 0 and forum_thread_status=1 and forum_thread_client = forum_client
				left outer join forum_category on fc_identifier = forum_category and forum_client = fc_client
				left outer join forum_thread_counter on ftc_identifier = forum_thread_identifier and forum_client = ftc_client
			where 
				forum_client = $this->client_identifier and 
				forum_status=1 and 
				forum_location=".$parameters["current_menu_location"]." 
				".$body_parts["where"]."
			group by forum_identifier 
			order by fc_label asc, forum_identifier desc";
			*/
			$sql = "Select forum_category.fc_label, ".$body_parts["return_field"].", metadata_details.md_title as forum_label, metadata_details.md_description, forum_identifier ,  count(forum_thread.forum_thread_identifier) as total_threads, menu_data.menu_label, sum(forum_thread_counter.ftc_posts) as total_posts from forum 
					inner join metadata_details on md_link_id=forum_identifier and md_client=$this->client_identifier and md_module='FORUM_'
				".$body_parts["join"]."
				inner join menu_data on forum_location=menu_identifier  and menu_client = forum_client
				left outer join forum_thread on forum_identifier = forum_thread_forum and forum_thread_parent = 0 and forum_thread_status=1 and forum_thread_client = forum_client
				left outer join forum_category on fc_identifier = forum_category and forum_client = fc_client
				left outer join forum_thread_counter on ftc_identifier = forum_thread_identifier and forum_client = ftc_client
			where 
				forum_client = $this->client_identifier and 
				forum_status=1 and 
				forum_location=".$parameters["current_menu_location"]." 
				".$body_parts["where"]."
			group by forum_identifier 
			order by fc_label asc, forum_identifier desc";
			//print "<li>".__FILE__."@".__LINE__."<pre>$sql</pre></li>";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
			}
			$result 	= $this->parent->db_pointer->database_query($sql);
			$num_rows	= $this->call_command("DB_NUM_ROWS",array($result));
			
			/** CHECK IF THIS MENU LOCATION HAS PAGE CONTENTS PUBLISHED TO IT */
			$has_page_data = 0;
			/** Get last transaction identifier of page */
			$sql = "select max(trans_identifier) as trans_page from menu_access_to_page where menu_identifier = ".$parameters["current_menu_location"]." and client_identifier = ".$this->client_identifier;
			$result_page 	= $this->parent->db_pointer->database_query($sql);
			$num_rows_page	= $this->call_command("DB_NUM_ROWS",array($result_page));			
			if ($num_rows_page > 0){
				$trans_page_row = $this->parent->db_pointer->database_fetch_array($result_page);
				$sql = "SELECT mi_identifier FROM `memo_information` WHERE mi_link_id =". $trans_page_row['trans_page'] ." and mi_client = ".$this->client_identifier;
				$result_page 	= $this->parent->db_pointer->database_query($sql);								
				$num_rows_page	= $this->call_command("DB_NUM_ROWS",array($result_page));							
				if ($num_rows_page > 0){
					/** This location has page data */
					$has_page_data = 1;
				}
			}	
			$this->parent->db_pointer->database_free_result($result_page);
			//print 'num'.$num_rows;
			if($num_rows>1 || $has_page_data == 1) {
				$locations="";
				$out .="<module name=\"".$this->module_name."\" display=\"forum_list\">";
				$out .="<forum_list command=\"\">";
				$threads="";
				$c=0;
				$category="__UNKNOWN__";
				$cat_count=0;
				while($r = $this->parent->db_pointer->database_fetch_array($result)){
					$total_posts=0;
					$fc_label = $this->check_parameters($r,"fc_label","");
					if($category!=$fc_label){
						if($cat_count>0){
							$out .= "</category>";
						}
						$out .= "<category><label><![CDATA[$fc_label]]></label>";
						$category=$fc_label;
						$cat_count++;
					}
					$identifier		= $r["forum_identifier"];
					$total_threads	= $this->check_parameters($r,"total_threads",0);
					$total_posts	= $this->check_parameters($r,"total_posts",0);
					$access_prev = $this->check_access_parameters($identifier);
					/* ZIA - No need */
					//if($access_prev["can_read"]!=0){
					$out .= "<forum identifier=\"$identifier\">
								<title><![CDATA[".$r["forum_label"]."]]></title>
								<uri><![CDATA[-".$this->make_uri($r["forum_label"])."]]></uri>
								<description><![CDATA[".$r["forum_description"]."]]></description>
								<summary><![CDATA[".$r["md_description"]."]]></summary>
								<threads total_threads=\"$total_threads\" total_posts=\"$total_posts\">$threads</threads>
							</forum>";
					//}
				}
				if($cat_count>0){
					$out .= "</category>";
				}
				$out .="</forum_list>";
				$out .="</module>";
			} else {								
				$sql = "Select forum.forum_identifier,metadata_details.md_title as forum_label from forum,metadata_details where forum.forum_client = $this->client_identifier and forum.forum_status=1 and forum.forum_location=".$parameters["current_menu_location"]." AND metadata_details.md_link_id=forum.forum_identifier" ; 
				//print $sql;
				$result 	= $this->parent->db_pointer->database_query($sql);
				$num_rows	= $this->call_command("DB_NUM_ROWS",array($result));
				if($num_rows>0) {
					$r = $this->parent->db_pointer->database_fetch_array($result);
					$url = str_replace('index.php',"-".$this->make_uri($r["forum_label"]).'/index.php', $_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]);
					$s = "s";
					if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS']!="on") {
						$s = "";
					}
					$url = "http".$s."://".$url;
					//print $url;
					$parameters["forum_identifier"]=$r["forum_identifier"];	
					header ("Location: $url");				
					exit();
					//$out = $this->forum_view_thread($parameters);
				}	
			}
		} else {
			$out ="";
		}
		return $out;
	}
	/**
	* This function will generate the list of threads for a specific forum.
	*/
	function forum_view_thread($parameters){
//		print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameters,true)."</p></li>";
		$forum_identifier = $this->check_parameters($parameters, "forum_identifier",-1);
		$access_prev = $this->check_access_parameters($forum_identifier);
		/*ZIA -- EVERY ONE CAN READ
		if($access_prev["can_read"]==0){
			return "";
			$this->exitprogram();
		} 
		*/
		$out="";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_view_thread",__LINE__,"".print_r($parameters,true).""));
		}
		if (empty($parameters["thread_parent"])){
			$parameters["thread_parent"]=0;
		}
		$forum_label="";
		$sql = "Select * from forum inner join menu_data on forum_location=menu_identifier 
					inner join metadata_details on md_link_id=forum_identifier and md_client=$this->client_identifier and md_module='FORUM_'
				where 
					forum_client = $this->client_identifier and 
					forum_status = 1 and 
					forum_identifier = ".$forum_identifier." and 
					menu_url = '".$this->parent->script."'";
		$result  = $this->parent->db_pointer->database_query($sql);
		$forum_workflow=0;
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$forum_workflow = $r["forum_workflow"];
			$forum_label	= $r["md_title"];
        }
		$this->parent->db_pointer->database_free_result($result);
		$sql = "Select forum.*, menu_label, forum_thread_counter.*, ft1_metadata_details.*, forum_metadata_details.md_title as forum_label,
					ft1.*, ft1.forum_thread_date as created_date, ft1.forum_thread_author as created_by,
					ft2.forum_thread_date as reply_date, ft2.forum_thread_author as reply_by,ft2.forum_thread_author_name AS reply_author_name
				from forum 
					inner join menu_data on forum_location=menu_identifier 
					inner join forum_thread_counter on ftc_forum=forum_identifier
					inner join forum_thread as ft1 on ftc_identifier = ft1.forum_thread_identifier
					inner join forum_thread as ft2 on ftc_lastthread = ft2.forum_thread_identifier
					inner join metadata_details as ft1_metadata_details on ft1_metadata_details.md_link_id=ft1.forum_thread_identifier and ft1_metadata_details.md_client=$this->client_identifier and ft1_metadata_details.md_module='FORUMTHREAD_'
					inner join metadata_details as forum_metadata_details on forum_metadata_details.md_link_id=forum_identifier and forum_metadata_details.md_client=$this->client_identifier and forum_metadata_details.md_module='FORUM_'
				where 
					forum_client = $this->client_identifier and 
					forum_status = 1 and 
					forum_identifier = ".$parameters["forum_identifier"]." and 
					menu_url = '".$this->parent->script."'
				 order by ftc_sticky desc, reply_date desc";
		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}

		$result = $this->parent->db_pointer->database_query($sql);
		$variables["GROUPING_IDENTIFIER"] = $parameters["forum_identifier"];
		
		/*ZIA -- WORK FLOW IS NOT REQUIRED AND EVERYONE CAN POST
		if ($this->check_parameters($_SESSION, "SESSION_USER_IDENTIFIER",0)>0 || $forum_workflow==1){
			
			if($access_prev["can_post"]!=0){
				$variables["PAGE_BUTTONS"] = Array(
					Array("NEWTOPIC",$this->module_command."THREAD_GENERATE","Add a new thread")
				);
			}
		}
		*/
		//if ($this->check_parameters($_SESSION, "SESSION_USER_IDENTIFIER",0)>0){
				$variables["PAGE_BUTTONS"] = Array(
					Array("NEWTOPIC",$this->module_command."THREAD_GENERATE","Add a new thread")
				);
		//}
		
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			$number_of_records	= 0;
			$goto				= 0;
			$finish				= 0;
			$page				= 0;
			$num_pages			= 0;
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page =$this->check_parameters($parameters,"page",1);
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
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			$variables["START_PAGE"]		= $start_page;
			if (($start_page+$this->page_size)>$num_pages)
				$end_page=$num_pages;
			else
				$end_page+=$this->page_size;
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= "";//$this->user_filter($grouplevel,$orderby);
			$variables["RESULT_ENTRIES"] =Array();
			define("FORUM_ENTRY_TOTAL"									,"Total");
			define("ENTRY_DATE_UPDATED"									,"Last Reply");
			$dis_thread	= $this->check_prefs(Array("sp_forum_thread_list_display_format", "LOCALE_SP_FORUM_THREAD_LIST_DISPLAY_FORMAT"	,"default" => 'Reply Details'	, "options" => 'Reply Details:Creation Details'				, "module" => "FORUM_",""));
			while (($r = $this->parent->db_pointer->database_fetch_array($result))&&($counter<$this->page_size)){
				if ($r["created_by"] != 0) 
					$thread_created_by = str_replace(",","",$this->call_command("CONTACT_GET_NAME",array("contact_user" => $r["created_by"])));
				else
					$thread_created_by = (strlen($r["forum_thread_author_name"])>0)?$r["forum_thread_author_name"]:"Anonymous";
				if ($r["reply_by"] != 0) 
					$thread_last_reply_by = str_replace(",","",$this->call_command("CONTACT_GET_NAME",array("contact_user" => $r["reply_by"])));
				else
					$thread_last_reply_by = (strlen($r["reply_author_name"])>0)?$r["reply_author_name"]:"Anonymous";

					
				if ($this->check_parameters($r,"forum_thread_identifier","__NOT_FOUND__")!="__NOT_FOUND__"){
					$counter++;
					
					if (!empty($r["ftc_posts"])){
						$total_posts = $r["ftc_posts"];
						if($total_posts>0)
							$total_posts--;
					}else{
						$total_posts = 0;
					}
					if (!empty($r["ftc_views"])){
						$total_views = $r["ftc_views"];
					}else{
						$total_views = 0;
					}
					if($dis_thread=="Reply Details"){
						$date = ($r["reply_date"]!=""?$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["reply_date"])):$r["created_date"]);
						$date .= " ".$thread_last_reply_by;
					} else {
						$date = ($r["created_date"]!=""?$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["created_date"])):"No reply");
						$date .= " ".$thread_created_by;
					}
					/*ZIA FORUM REQUIRES THREAD TO BE APPROVED 
					if ($r["forum_workflow"]!=0 || $r["forum_thread_status"]==1) {
						$re_index = count($variables["RESULT_ENTRIES"]);
						$variables["RESULT_ENTRIES"][$re_index] = Array(
							"identifier"	=> $r["forum_thread_identifier"],
							"ENTRY_BUTTONS" => Array(),
							"attributes"	=> Array(
								Array(FORUM_ENTRY_TOPIC, $r["md_title"], "TITLE", "VIEW_DOCUMENT","ALT_TEXT"),
								Array("VIEW_DOCUMENT", $r["md_uri"],"No","No"),
								Array("ALT_TEXT", $r["md_description"],"No","NO")
							)
						);
					}	
					*/
					if ($r["forum_workflow"]!=0 || $r["forum_thread_status"]==1) {
						$re_index = count($variables["RESULT_ENTRIES"]);
						$variables["RESULT_ENTRIES"][$re_index] = Array(
							"identifier"	=> $r["forum_thread_identifier"],
							"ENTRY_BUTTONS" => Array(),
							"attributes"	=> Array(
								Array(FORUM_ENTRY_TOPIC, $r["md_title"], "TITLE", "VIEW_DOCUMENT","ALT_TEXT"),
								Array("VIEW_DOCUMENT", "_read-topic.php","No","No"),
								Array("ALT_TEXT", $r["md_description"],"No","NO")
							)
						);
					}	

					$show_author		= $this->check_prefs(Array("sp_show_author", "LOCALE_SP_SHOW_AUTHOR"	,"default" => 'Yes'	, "options" => 'Yes:No'				, "module" => "FORUM_",""));
					$show_total_posts	= $this->check_prefs(Array("sp_show_total_posts", "LOCALE_SP_SHOW_TOTAL_POSTS"	,"default" => 'Yes'	, "options" => 'Yes:No'				, "module" => "FORUM_",""));
					$show_total_views	= $this->check_prefs(Array("sp_show_total_views", "LOCALE_SP_SHOW_TOTAL_VIEWS"	,"default" => 'Yes'	, "options" => 'Yes:No'				, "module" => "FORUM_",""));
					$show_date_updated	= $this->check_prefs(Array("sp_show_date_updated", "LOCALE_SP_SHOW_DATE_UPDATED"	,"default" => 'Yes'	, "options" => 'Yes:No'				, "module" => "FORUM_",""));
					if($show_author=="Yes"){
						$variables["RESULT_ENTRIES"][$re_index]["attributes"][count($variables["RESULT_ENTRIES"][$re_index]["attributes"])] = Array(ENTRY_AUTHOR, $thread_created_by, "SUMMARY","");
					}
					if($show_total_posts=="Yes"){
					$variables["RESULT_ENTRIES"][$re_index]["attributes"][count($variables["RESULT_ENTRIES"][$re_index]["attributes"])] = Array(FORUM_ENTRY_TOTAL_POSTS, "$total_posts", "SUMMARY","");
					}
					if($show_total_views=="Yes"){
					$variables["RESULT_ENTRIES"][$re_index]["attributes"][count($variables["RESULT_ENTRIES"][$re_index]["attributes"])] = Array(FORUM_ENTRY_TOTAL_VIEWS, "$total_views", "SUMMARY","");
					}
					if($show_date_updated=="Yes"){
					$variables["RESULT_ENTRIES"][$re_index]["attributes"][count($variables["RESULT_ENTRIES"][$re_index]["attributes"])] = Array(ENTRY_DATE_UPDATED, $date,"SUMMARY","");
					}
					
				} else {
					$number_of_records --;
				}
				$forum_label = $r["forum_label"];
			}
		}
		$variables["PAGE_COMMAND"]		= "FORUM_VIEW_ENTRY";
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$variables["LABEL"]				= "$forum_label";
		if($number_of_records==0){
			$sql = "select * from forum
						inner join metadata_details on md_link_id=forum_identifier and md_client=$this->client_identifier and md_module='FORUM_'
					where 
						forum_client = $this->client_identifier and 
						forum_status = 1 and 
						forum_identifier = ".$parameters["forum_identifier"];
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result  = $this->parent->db_pointer->database_query($sql);
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$forum_label = $r["md_title"];
            }
            $this->parent->db_pointer->database_free_result($result);
		}
		$out = $this->generate_list($variables)."<bread_crumb>
			<crumb>
				<url>".$this->parent->base.dirname($this->parent->real_script)."/index.php</url>
				<label><![CDATA[$forum_label]]></label>
			</crumb>
		</bread_crumb>
		<fake_title><![CDATA[$forum_label]]></fake_title>";
		return $out;
	}

	/*************************************************************************************************************************
    * display a thread 
    *************************************************************************************************************************/
	function thread_display($parameters){
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		$out="";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"thread_display",__LINE__,""));
		}
		$starter=0;
		$the_thread_label="";
		$dis_form			= $this->check_prefs(Array("sp_forum_list_display_format", "LOCALE_SP_FORUM_LIST_DISPLAY_FORMAT"	,"default" => 'Flat'	, "options" => 'Flat:Threaded'				, "module" => "FORUM_",	""));
		$thread 			= $this->check_parameters($parameters,"thread_identifier",0);
		$th		 			= $this->check_parameters($parameters,"th",0);
		$forum 				= $this->check_parameters($parameters,"forum_identifier",0);
		$access_prev 		= $this->check_access_parameters($forum);
		/* ZIA -NO NEED
		if($access_prev["can_read"]==0){
			return "";
			$this->exitprogram();
		} 
		*/
		$msg="";
		$threadlist="";
		$sql = "SELECT forum_workflow from forum where forum_identifier = ".$forum." and forum_client=".$this->client_identifier;
		$result				= $this->parent->db_pointer->database_query($sql);
		$r = $this->parent->db_pointer->database_fetch_array($result);
		if ( $r["forum_workflow"] == 0) {
			$sql_forum_thread_status = " forum_thread_status=1 and ";
		}	
		$body_parts 		= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", 
			Array(
				"table_as"			=> "forum_thread_data",
				"field_as"			=> "forum_thread_description",
				"identifier_field"	=> "forum_thread.forum_thread_identifier",
				"module_command"	=> $this->webContainer,
				"client_field"		=> "forum_thread_client",
				"mi_field"			=> "forum_thread_description"
			)
		);
		$sql = "Select 
					".$body_parts["return_field"].", 
					forum_thread.*, 
					forum.*, 
					ftmd.md_title as thread_label, 
					ftmd.md_description as thread_des, 
					ftmd.md_link_group_id as thread_author,
					fmd.md_title as forum_label
				from forum_thread 
					".$body_parts["join"]." 
					inner join forum on forum_identifier = forum_thread_forum 
					inner join metadata_details as ftmd on ftmd.md_link_id=forum_thread_identifier and ftmd.md_client=$this->client_identifier and ftmd.md_module='FORUMTHREAD_'
					inner join metadata_details as fmd on fmd.md_link_id=forum_identifier and fmd.md_client=$this->client_identifier and fmd.md_module='FORUM_'
		where 
			forum_thread_client = $this->client_identifier and 
			forum_status=1 and 
			". $sql_forum_thread_status ."
			forum_thread_starter = ".$thread." 
			".$body_parts["where"]." and 
			forum_identifier = ".$forum;
			if($dis_form=="Flat"){
				$sql.="	order by forum_thread_identifier";
			} else {
				$sql.="	order by forum_thread_parent, forum_thread_date ";
			}
		//print "<li>".__FILE__."@".__LINE__."<pre>$sql</pre></li>";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$found_counter=0;
		$this->thread_size	= $this->check_prefs(Array("sp_thread_size", "default"=>20,"module"=>"FORUM_", "options"=>"10:20:30:50"));
		$result				= $this->parent->db_pointer->database_query($sql);
		$num_rows			= $this->call_command("DB_NUM_ROWS",Array($result));
		$start_page			= 1;
		$end_page = $start_page;

		if($num_rows > 0) {
			$locations = "";
			$out .= "<module name=\"".$this->module_name."\" display=\"display\" grouping=\"".$parameters["forum_identifier"]."\">";
			$out .= "	<thread_entry command=\"".$parameters["command"]."\">";
			if($dis_form=="Flat"){
				if($num_rows>$this->thread_size){
					$page = $this->check_parameters($parameters,"page",1) - 1;
					$this->parent->db_pointer->database_data_seek($result, $page*$this->thread_size);
					$num_pages = ceil($num_rows / $this->thread_size);
					//$page_count = intval($page/$this->page_size);
					if($page>5)
						$start_page	= $page-5;
					$end_page	=$start_page+5;
					if (($start_page+$this->page_size)>$num_pages)
						$end_page=$num_pages;
					else
						$end_page+=$this->page_size;
				}
				$counter=0;
				$forum_workflow=0;
	            while(($r = $this->parent->db_pointer->database_fetch_array($result)) && ($counter<$this->thread_size)){
					$forum_workflow	= $r["forum_workflow"];
					$identifier 	= $r["forum_thread_identifier"];
					$parent			= $r["forum_thread_parent"];
					$starter		= $r["forum_thread_starter"];
					if ($r["forum_thread_author"] != 0) 
						$thread_author_name = str_replace(",","",$this->call_command("CONTACT_GET_NAME",array("contact_user" => $r["forum_thread_author"])));
					else
						$thread_author_name = (strlen($r["forum_thread_author_name"])>0)?$r["forum_thread_author_name"]:"Anonymous";
					if ($parent>0){
						$sql = "select * from forum_thread 
									inner join metadata_details on md_link_id=forum_thread_identifier and md_client=$this->client_identifier and md_module='FORUMTHREAD_'
								where forum_thread_client = $this->client_identifier and forum_thread_identifier=$parent";
						$parent_result = $this->parent->db_pointer->database_query($sql);
						$parent_row = $this->call_command("DB_FETCH_ARRAY",array($parent_result));
						if($parent_row["forum_thread_blocked"]==0){
							$parent_title	= $parent_row["md_title"];
						} else {
							$parent_title	= "Blocked by Administrator";
						}
						$this->call_command("DB_FREE",array($parent_result));
					}else{
						$parent_title="";
					}
					$out .="<thread identifier=\"$identifier\" starter=\"$starter\" blocked='".$r["forum_thread_blocked"]."'>";
					if ($parent!=0){
	//					$out .="<parent identifier=\"$parent\"><title><![CDATA[$parent_title]]></title></parent>";
					}
					if($r["forum_thread_blocked"]==0){
						if($found_counter==0){
							$the_thread_label = $r["thread_label"];
							$found_counter=1;
						}
						//print "<li>link".$thread_author_name ."</li>";						
						$out .="<label><![CDATA[".$r["thread_label"]."]]></label>";
						$out .="<description><![CDATA[".$r["forum_thread_description"]."]]></description>";
						$out .="<date>".$r["forum_thread_date"]."</date>";
						$out .="<author><![CDATA[".$thread_author_name."]]></author>";
					} else {
						if($found_counter==0){
							$the_thread_label = $r["Blocked by Administrator"];
							$found_counter=1;
						}
						$out .="<label><![CDATA[Blocked by Administrator]]></label>";
						$out .="<description><![CDATA[Blocked by Administrator]]></description>";
						$out .="<date>".$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["forum_thread_date"]))."</date>";
						$out .="<author><![CDATA[".$thread_author_name."]]></author>";
					}
					$out .="</thread>";
					$forum_label = $r["forum_label"];
					$counter++;
	            }
				
			} else {
				$page = $this->check_parameters($parameters,"page",1) - 1;
				$counter=0;
				$msg="";
				$list=Array();
	            while(($r = $this->parent->db_pointer->database_fetch_array($result))){
					$forum_workflow	= $r["forum_workflow"];
					$identifier 	= $r["forum_thread_identifier"];
					$parent			= $r["forum_thread_parent"];
					$starter		= $r["forum_thread_starter"];
					if ($r["forum_thread_author"] != 0) 
						$thread_author_name = str_replace(",","",$this->call_command("CONTACT_GET_NAME",array("contact_user" => $r["forum_thread_author"])));
					else
						$thread_author_name = (strlen($r["forum_thread_author_name"])>0)?$r["forum_thread_author_name"]:"Anonymous";
					$ok = 0;
					if($page==$counter && $th == 0){
						$ok = 1;
					}
					if($th == $identifier && $th!=0){
						$ok = 1;
					}
					if("__NOT_FOUND__" == $this->check_parameters($list,$r["forum_thread_parent"],"__NOT_FOUND__")){
						$list[$r["forum_thread_parent"]] = Array();
					}
					if ($ok==1){
						if ($parent>0){
							$sql = "select * from forum_thread 
										inner join metadata_details on md_link_id=forum_thread_identifier and md_client=$this->client_identifier and md_module='FORUMTHREAD_'
									where forum_thread_client = $this->client_identifier and forum_thread_identifier=$parent";
							$parent_result = $this->parent->db_pointer->database_query($sql);
							$parent_row = $this->call_command("DB_FETCH_ARRAY",array($parent_result));
							$parent_title	= $parent_row["md_title"];
							$forum_thread_status = $parent_row["forum_thread_status"];
							$this->call_command("DB_FREE",array($parent_result));
						}else{
							$parent_title="";
						}
						$msg ="<thread identifier=\"$identifier\" starter=\"$starter\" blocked='".$r["forum_thread_blocked"]."'>";
						if($r["forum_thread_blocked"]==0){
							if($found_counter==0){
								$the_thread_label = $r["thread_label"];
								$found_counter=1;
							}
							$msg .="<label><![CDATA[".$r["thread_label"]."]]></label>";
							if ($parent!=0){
							/* Get exact page number for 'In reply to' portion starts (Added By Muhammad Imran Mirza) */
								if($parent != $thread){
									$msg .="<parent identifier=\"$thread\" page=\"$counter\"><title><![CDATA[$parent_title]]></title></parent>";
								}else{
									$msg .="<parent identifier=\"$thread\"><title><![CDATA[$parent_title]]></title></parent>";
								}
							/* Get exact page number for 'In reply to' portion ends (Added By Muhammad Imran Mirza) */
							}
							$msg .="<description><![CDATA[".$r["forum_thread_description"]."]]></description>
									<date>".$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["forum_thread_date"]))."</date>
									<author><![CDATA[".$thread_author_name."]]></author>		
								</thread>
								<metadata identifier='$identifier'>
									<summary><![CDATA[".$r["thread_des"]."]]></summary>
									<date><![CDATA[".$this->libertasGetDate("r",strtotime($r["forum_thread_date"]))."]]></date>
								</metadata>";
							$counter++;

							if($counter!=1){
								$list[$r["forum_thread_parent"]][count($list[$r["forum_thread_parent"]])] = Array("identifier"=>$identifier, "date"=>$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["forum_thread_date"])),"title"=>$r["thread_label"], "author"=>$thread_author_name, "url" => "page=$counter", "summary"=>$r["thread_des"]);
								//print_r($list);
							} else {
								$list[$r["forum_thread_parent"]][count($list[$r["forum_thread_parent"]])] = Array("identifier"=>$identifier, "date"=>$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["forum_thread_date"])),"title"=>$r["thread_label"], "author"=>$thread_author_name, "url" => "", "summary"=>$r["thread_des"]);
							}
						} else {
							if($found_counter==0){
								$the_thread_label = $r["Blocked by Administrator"];
								$found_counter = 1;
							}
							$msg .="<label><![CDATA[Blocked by Administrator]]></label>";
							$msg .="<description><![CDATA[Blocked by Administrator]]></description>";
							$msg .="<date>".$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["forum_thread_date"]))."</date>";
							$msg .="<author><![CDATA[".$thread_author_name."]]></author>";
							$counter++;
							if($counter!=1){
								$list[$r["forum_thread_parent"]][count($list[$r["forum_thread_parent"]])] = Array("identifier"=>$identifier, "date"=>$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["forum_thread_date"])), "title"=>"Blocked by Administrator", "author"=>$thread_author_name, "url" => "page=$counter", "summary"=>"Blocked By Administrator");
							} else {
								$list[$r["forum_thread_parent"]][count($list[$r["forum_thread_parent"]])] = Array("identifier"=>$identifier, "date"=>$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["forum_thread_date"])), "title"=>"Blocked by Administrator", "author"=>$thread_author_name, "url" => "", "summary"=>"Blocked By Administrator");
							}
						}

					}else {
						$counter++;
						//print '<li>b- '.$counter.'  auth-'.$thread_author_name.'</li>.';
						if($counter!=1){
							$list[$r["forum_thread_parent"]][count($list[$r["forum_thread_parent"]])] = Array("identifier"=>$identifier, "date"=>$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["forum_thread_date"])),"title"=>$r["thread_label"], "author"=>$thread_author_name, "url" => "page=$counter", "summary"=>$r["thread_des"]);
						} else {
							$list[$r["forum_thread_parent"]][count($list[$r["forum_thread_parent"]])] = Array("identifier"=>$identifier, "date"=>$this->libertasGetDate("d-m-Y H:i:s",strtotime($r["forum_thread_date"])),"title"=>$r["thread_label"], "author"=>$thread_author_name, "url" => "", "summary"=>$r["thread_des"]);
						}
					}
					$forum_label = $r["forum_label"];
		        }
		        
				$end_page=$counter;
				$threadlist = "";
				if(count($list)>1){
					$threadlist = "<text><![CDATA[".$this->generate_thread_list($list,0,"",$thread,$forum)."]]></text>";
				} 
			}
//			print "<li>".__FILE__."@".__LINE__."<pre>".print_r($list, true)."</pre></li>";
    		$out .="$msg</thread_entry>";

			$out.= $threadlist;
			if($start_page!=$end_page && $dis_form=="Flat"){
				$out .= "<text><![CDATA[<ul class='pagespan'>";
				for($i=$start_page;$i<=$end_page;$i++){
					if(($page+1)==$i){
						$out .= "<li>$i</li>";
					} else {
						$out .= "&lt;li&gt;&lt;a href=''&gt;$i&lt;/a&gt;&lt;/li&gt;";
					}
				}
				$out .= "&lt;/ul>]]></text>";
			}
           	$this->parent->db_pointer->database_free_result($result);
			$out .="<commands>";			
			//if ($this->check_parameters($_SESSION, "SESSION_USER_IDENTIFIER",0)>0 || $forum_workflow==1){				
				if($access_prev["can_post"]!=0){					
					$out .= "<command per_thread='1' type='reply'></command>";
				}
				if($dis_form=="Threaded"){
					if($page>0){
						if($page==0){
							$out .= "<command per_thread='1' type='previous'><![CDATA[]]></command>";
						} else {
							$out .= "<command per_thread='1' type='previous'><![CDATA[page=".($page)."]]></command>";
						}
					}
					if($page<$end_page-1){
						$out .= "<command per_thread='1' type='next'><![CDATA[page=".($page+2)."]]></command>";
					}
				}
			//}
			$out .="</commands>";
			$out .="</module>";
			
			$out .="<bread_crumb>
						<crumb>
							<url>".$this->parent->base.dirname($this->parent->real_script)."/index.php</url>
							<label><![CDATA[$forum_label]]></label>
						</crumb>";
			$out .="	<crumb>
							<label><![CDATA[$the_thread_label]]></label>
						</crumb>";
			$out .="</bread_crumb>"; 
			$out .="<fake_title>
						<label><![CDATA[$the_thread_label]]></label>
					</fake_title>";
			/*************************************************************************************************************************
            * update number of views
            *************************************************************************************************************************/
			$sql = "update forum_thread_counter set ftc_views = ftc_views + 1 where ftc_identifier = $starter and ftc_client=$this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
		}
//		print "[".__line__."]";
//		print $out;
//		$this->exitprogram();
		return $out;
	}
	/*************************************************************************************************************************
    * This function will allow the Adding or replying to an existing message
    *************************************************************************************************************************/
	function forum_thread_generation($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_view_thread",__LINE__,"".print_r($parameters,true).""));
		}
		$title = "";
		$label = "Add a new topic";
		$forum_identifier = $parameters["forum_identifier"];
		$access_prev 		= $this->check_access_parameters($forum_identifier);
		/*ZIA --NO NEED TO
		if($access_prev["can_post"]==0){
			return "";
			$this->exitprogram();
		} 
		*/
		$parent = $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"unset_identifier",0));
		$starter =0;
		$forum_label="";
		$sql = "select * from metadata_details where md_link_id=$forum_identifier and md_client=$this->client_identifier and md_module='FORUM_'";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$forum_label = $r["md_title"];
        }
        $this->parent->db_pointer->database_free_result($result);
		
		if (empty($parent)){
			$parent 	= 0;
			$starter	= 0;
		} else {
			$label="Reply to topic";
			$sql = "select * from forum_thread 
				inner join metadata_details on md_link_id=forum_thread_identifier and md_client=$this->client_identifier and md_module='FORUMTHREAD_'
			where forum_thread_client=$this->client_identifier and forum_thread_identifier=$parent";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$starter = $r["forum_thread_starter"];
				if(substr($r["md_title"],0,3)=="re:"){
					$title = str_replace(Array("&quot;","&#39;","&amp;#39;"),Array("[[quot]]","[[pos]]","[[pos]]"),$r["md_title"]);
				} else {
            		$title = "re: ".str_replace(Array("&quot;","&#39;","&amp;#39;"),Array("[[quot]]","[[pos]]","[[pos]]"),$r["md_title"]);
				}
            }
            $this->parent->db_pointer->database_free_result($result);
			
		}
		
		$out = "<module name=\"$this->module_name\" display=\"form\">";
		$out .= "<form name=\"thread_form\" method=\"post\" label=\"$label\">";
		$out .= "<input type=\"hidden\" name=\"thread_parent\" value=\"$parent\"/>";
		$out .= "<input type=\"hidden\" name=\"thread_starter\" value=\"$starter\"/>";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"FORUM_THREAD_SAVE\"/>";
		$out .= "<input type=\"hidden\" name=\"forum_identifier\" value=\"$forum_identifier\"/>";
		$out .= "<input type=\"text\" size=\"255\" name=\"thread_title\" label=\"Subject\"><![CDATA[$title]]></input>"; 
		$out .= "<textarea label=\"Message\" size=\"60\" height=\"10\" name=\"thread_body\" type=\"PLAIN-TEXT\"><![CDATA[]]></textarea>";
		/** IF USER IS NOT LOGGED IN AND FORUM HAS ANONYMOUS REPLY PERMISSIONS */
		if (($this->check_parameters($_SESSION, "SESSION_USER_IDENTIFIER",0) == 0) && ($access_prev["can_post"]!=0)){						
			$out .= "<input type=\"text\" size=\"50\" name=\"thread_author\" label=\"Author\"><![CDATA[]]></input>"; 		
		}
		$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"Submit this post\"/>";
		$out .= "</form>";
		$out .= "</module>";
		if($title==""){
			$title="Add New Topic";
		} else {
			$title = "Reply to";
		}
		$out .="<bread_crumb>
					<crumb>
						<url>".$this->parent->base.dirname($this->parent->real_script)."/index.php</url>
						<label><![CDATA[$forum_label]]></label>
					</crumb>";
		$out .="	<crumb>
						<label><![CDATA[$title]]></label>
					</crumb>";
		$out .="</bread_crumb>";
		$out .="<fake_title>
					<label><![CDATA[$title]]></label>
				</fake_title>";
		return $out;
	}
	/*************************************************************************************************************************
    * This function will allow the saving of a new thread
    *************************************************************************************************************************/
	function forum_thread_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_thread_save",__LINE__, "".print_r($parameters,true).""));
		}
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		
		$thread_forum 	= $parameters["forum_identifier"];
		$thread_parent 	= $parameters["thread_parent"];
		$thread_title 	= htmlentities($this->validate($this->call_command("UTILS_STRIP_SWEARWORDS",Array("source_string" => strip_tags($parameters["thread_title"])))));
		$thread_body	= $this->moduletidy($this->split_me($this->validate($this->call_command("UTILS_STRIP_SWEARWORDS",Array("source_string" => strip_tags($parameters["thread_body"])))),"\n","<br/>"));
		$thread_author  = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
		$thread_author_name = trim(strip_tags($this->check_parameters($parameters,"thread_author",null)));
		$thread_author_name = ($thread_author_name != "")? "'$thread_author_name'":"NULL";

		$thread_starter = 0;
		$access_prev 	= $this->check_access_parameters($thread_forum);
		/*ZIA -NO NEED
		if($access_prev["can_post"]==0){
			return "";
			$this->exitprogram();
		} 
		*/
		$ok 			= 0;
		if ($thread_parent==0){
			$thread_starter	=	0;
		}else{
			$sql = "select forum_thread_identifier, forum_thread_starter from forum_thread where forum_thread_identifier=$thread_parent and forum_thread_forum=$thread_forum and forum_thread_client=$this->client_identifier";
			$result = $this->parent->db_pointer->database_query($sql);
			if ($result){
				$r 	= $this->parent->db_pointer->database_fetch_array($result);
				if ($r["forum_thread_starter"]>0){
					$thread_starter = $r["forum_thread_starter"];
				}else{
					$thread_starter = $r["forum_thread_identifier"];
				}
			}
		}
	//	print "[$thread_parent, $thread_starter]";
		$workflow = -1;
		$sql ="select * from forum where forum_identifier = $thread_forum and forum_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$workflow = $r["forum_workflow"];
        }
        /* ZIA - CHANGED
		if ($workflow==1){
			if($this->module_approver_access==1){
				$forum_thread_status = 1;
			} else {
				$forum_thread_status = 0;
			}
		} else {
			$forum_thread_status = 1;
		}
		*/
        $forum_thread_status = $workflow;
		/*ZIA -NO NEED 
		if($access_prev["approved"]!=0){
			$forum_thread_status = 1;
		}
		*/
        $this->parent->db_pointer->database_free_result($result);
		$now = $this->libertasGetDate("Y/m/d H:i:s");
		$forum_thread_identifier = $this->getUID();
		if ($thread_starter==0){
			$thread_starter = $forum_thread_identifier;
		}
		$sql = "insert into forum_thread
		(forum_thread_identifier, forum_thread_forum, forum_thread_status, forum_thread_starter, forum_thread_parent, forum_thread_date, forum_thread_client, forum_thread_author, forum_thread_author_name)
		values
		($forum_thread_identifier, $thread_forum, $forum_thread_status, $thread_starter, $thread_parent, '$now', $this->client_identifier, $thread_author, $thread_author_name)
		";
		//print '<li>'.$sql.'<li>';
		$this->parent->db_pointer->database_query($sql);
		$originalTitle = "";
		if ($thread_parent==0){
			$sql = "insert into forum_thread_counter 
				(ftc_identifier, ftc_lastthread, ftc_client, ftc_forum, ftc_posts, ftc_views)
				values
				($forum_thread_identifier, $forum_thread_identifier, $this->client_identifier, $thread_forum, 1, 0)
			";
			$originalTitle = $thread_title;
		} else {
			$sql = "select * from forum_thread 
						inner join metadata_details on md_link_id = forum_thread_identifier and md_client  = $this->client_identifier and md_module  = 'FORUMTHREAD_'
					where forum_thread_client = $this->client_identifier and forum_thread_parent=0 and forum_thread_identifier = $thread_starter";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$originalTitle = $r["md_title"];
            }
            $this->parent->db_pointer->database_free_result($result);
			
			$sql = "update forum_thread_counter 
						set ftc_lastthread=$forum_thread_identifier, ftc_posts=ftc_posts+1 
					where ftc_client=$this->client_identifier and  ftc_forum=$thread_forum and ftc_identifier=$thread_starter
			";
			
		}
		$this->parent->db_pointer->database_query($sql);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"forum_thread_save",__LINE__,"$sql"));
		}
		$this->call_command("MEMOINFO_INSERT",array("mi_type"=>$this->webContainer, "mi_memo"=>$thread_body,	"mi_link_id" => $forum_thread_identifier, "mi_field" => "forum_thread_description"));
		if ($workflow==1){
			if($this->module_approver_access==1){
//				$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_FORUM__"], "identifier" => $forum_thread_identifier, "url"=> $this->parent->script));
			} else {
//				$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_FORUM_APPROVER__"], "identifier" => $forum_thread_identifier, "url"=> "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=FORUM_MANAGE_THREADS&identifier=$thread_forum"));
			}
		} else {
//			$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_FORUM__"], "identifier" => $forum_thread_identifier, "url"=> $this->parent->script));
		}
		if($forum_thread_status==1 && $thread_parent==0){
			$sql = "select * from forum_thread
						inner join metadata_details on md_link_id = forum_thread_identifier and md_client  = $this->client_identifier and md_module  = 'FORUMTHREAD_'
						inner join forum on forum_thread_forum =forum_identifier  and forum_client = $this->client_identifier
						inner join menu_data on forum_location = menu_identifier and menu_client = $this->client_identifier
					where 
						forum_thread_parent=0 and 
						forum_thread_client = $this->client_identifier and 
						md_title='$originalTitle' and 
						forum_thread_forum = $thread_forum and 
						forum_thread_identifier != $thread_starter
					";
			$result  = $this->parent->db_pointer->database_query($sql);
			$num_rows  = $this->parent->db_pointer->database_num_rows($result);
			$this->parent->db_pointer->database_free_result($result);
			$sql = "select * from forum 
						inner join metadata_details on md_link_id = forum_identifier and md_client  = $this->client_identifier and md_module  = 'FORUM_'
					where 
						forum_client = $this->client_identifier and 
						forum_identifier = $thread_forum
					";
			$result  = $this->parent->db_pointer->database_query($sql);
			$forum_label = "";
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
              	$ml_id = $r["forum_location"];
				$forum_label = $r["md_title"];
			}
            $this->parent->db_pointer->database_free_result($result);
			if($num_rows==0){
				$fname = $this->make_uri($originalTitle).".php";
			} else {
				$fname = $this->make_uri($originalTitle." ".$num_rows).".php";
			}
			$this->make_special($ml_id, $thread_starter, "-".$this->make_uri($forum_label)."/".$fname, $thread_forum);
		}
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$this->call_command("METADATAADMIN_MODIFY", 
			Array(
				"md_title" 			=> $thread_title,
				"md_uri" 			=> "-".$this->make_uri($forum_label)."/".$fname,
				"md_link_group_id"	=> $thread_author, 
				"md_date_publish"	=> $this->libertasGetDate(), 
				"module"			=> "FORUMTHREAD_", 
				"identifier"		=> $forum_thread_identifier, 
				"command"			=> "ADD", 
				"longDescription"	=> $thread_body
			)
		);
		return $thread_forum;
	}
	/*************************************************************************************************************************
    * generate threaded list based ont he parentage of the threads
    *************************************************************************************************************************/
	function generate_thread_list($list, $start=0, $url="",$thread, $forum){
		$str = "";
		$found=0;
		$result = $this->check_parameters($list,$start,Array());
		for($i=0;$i<count($result);$i++){
			$found++;
			if($result[$i]["url"]==""){
				$str .="<li><a title='".$result[$i]["summary"]."' href='".$this->parent->real_script."?thread_identifier=".$thread."'>".$result[$i]["title"]."</a> by ".$result[$i]["author"]." at ".$result[$i]["date"];
			} else {
				$str .="<li><a title='".$result[$i]["summary"]."' href='".$this->parent->real_script."?".$result[$i]["url"]."&amp;thread_identifier=".$thread."'>".$result[$i]["title"]."</a> by ".$result[$i]["author"]." at ".$result[$i]["date"];
				/* ZIA No need to pass command parameter
				$str .="<li><a title='".$result[$i]["summary"]."' href='".$this->parent->real_script."?".$result[$i]["url"]."&amp;command=".$this->module_command."THREAD_VIEW_ENTRY&amp;forum_identifier=".$forum."&amp;thread_identifier=".$thread."'>".$result[$i]["title"]."</a> by ".$result[$i]["author"]." at ".$result[$i]["date"];				
				*/
			}
			$str .= $this->generate_thread_list($list, $result[$i]["identifier"],"",$thread,$forum);
			$str.="</li>";
		}
		if($found>0){
			return "<ul class='threadlist'>$str</ul>";
		} else {
			return "";
		}
	}
	/*************************************************************************************************************************
    * builds special pages for the information directory
	*
	* <strong>Note::</strong> only creates a2z pages when display layout is = 2
    *
    * @param string path on site to the file
    * @param integer id of information directory this will use
    * @param string path on site to the file
	* @param Integer $summary_layout
    *************************************************************************************************************************/
	function make_special($ml_id, $id, $filename, $forum_identifier){
		$root 				= $this->parent->site_directories["ROOT"];
		$module_directory	= $this->parent->site_directories["MODULE_DIR"];
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql = "select * from menu_data where menu_identifier = '$ml_id' and menu_client='$this->client_identifier' ";
		$result  = $this->parent->db_pointer->database_query($sql);
		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			$ml_url	= $r["menu_url"];
		}
		$this->parent->db_pointer->database_free_result($result);
		$dir = $root."/".dirname($ml_url);
		
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$this->special_webobjects			= Array(
			"READ_THREAD" => Array(
				"owner_module" 	=> "",
				"label" 		=> "",
				"wo_command"	=> "FORUM_THREAD_VIEW_ENTRY",
				"file"			=> $filename
			)
		);
		$max 				= count($this->special_webobjects);
		$forum_label_uri = dirname($filename);
		foreach($this->special_webobjects as $index => $value){
			$out ="<"."?php
\$script_file	= dirname(\$_SERVER[\"SCRIPT_FILENAME\"]);
\$root 			= '$root';
\$site_root		= \$_SERVER[\"DOCUMENT_ROOT\"];
\$script		= get(\$script_file, \$root, \$site_root);
\$mode			= \"EXECUTE\";
\$command	 	= \"".$value["wo_command"]."\";
\$extra = Array(
	\"forum_identifier\" =>\"$forum_identifier\",
	\"thread_identifier\"=>\"$id\"
);
require_once \"".$root."/admin/include.php\";
require_once \"\$module_directory/included_page.php\";

function get(\$sfile, \$rt, \$sroot){
	\$cat = \"$forum_label_uri\";
	if (strpos(\$sfile,\$rt)===false){
		return substr(\$sfile, strpos(\$sfile,\$sroot)+strlen(\$sroot)+1, - strlen(\$cat)).\"index.php\";
	} else {
		\$l = split(\$rt.\"/\",\$sfile);
		if(strlen(\$cat)==0){
			return \$l[1].\"/index.php\";
		} else {
			return substr(\$sfile, strpos(\$sfile,\$rt)+strlen(\$rt)+1, - strlen(\$cat)).\"index.php\";
		}
	}
}
					?".">";

			$file_to_use = $dir."/".$value["file"];
			$fp = fopen($file_to_use,"w");
			fwrite($fp, $out);
			fclose($fp);
			$old_umask = umask(0);
			@chmod($file_to_use,LS__FILE_PERMISSION);
			umask($old_umask);
		}
	}
	/*************************************************************************************************************************
	* extract and retrieve the access restrictions for this user on this forum
	*************************************************************************************************************************/
	function check_access_parameters($forum_identifier){
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$glist="-1";
		for($i=0;$i<$max_grps;$i++){
			$glist .= ", ".$grp_info[$i]["IDENTIFIER"];
		}
		$sql = "select sum(fgr_can_view) as view,sum(fgr_can_post) as reply,sum(fgr_auto_publish) as approve from forum_group_restrictions 
					where 
						fgr_client = $this->client_identifier and
						fgr_forum = $forum_identifier and 
						fgr_group in ($glist)
						";
		$result_access  = $this->parent->db_pointer->database_query($sql);
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$can_read = 0;
		$can_post = 0;
		$approved = 0;
		while($r = $this->parent->db_pointer->database_fetch_array($result_access)){
			$can_read = $r["view"];
			$can_post = $r["reply"];
			$approved = $r["approve"];
	    }
		$this->parent->db_pointer->database_free_result($result_access);
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r(Array("can_post" => $can_post, "can_read" => $can_read, "approved" => $approved), true)."</pre></li>";
		return Array("can_post" => $can_post, "can_read" => $can_read, "approved" => $approved);
	}	
}
?>