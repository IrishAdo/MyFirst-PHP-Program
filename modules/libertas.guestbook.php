<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.guestbook.php
* @date 06 Feb 2004
*/
/**
* This module is the module for displaying a guestbook
*/

class guestbook extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type				= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name_label			= "GuestBook Module (Presentation)";
	var $module_name				= "guestbook";
	var $module_command				= "GUESTBOOK_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "";
	var $module_modify	 		= '$Date: 2005/02/26 10:49:44 $';
	var $module_version 			= '$Revision: 1.12 $';
	var $module_creation 			= "06/02/2004";
	var $searched					= 0;
	
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
	var $module_display_options 	= array();
	
	/**
	* WebObject entries
	*
	* Each Array has (Type, Label, Command, All locations, Has label)
	-
	- Type: - 0 = User defined, 1 = Channel type Web object, 2 = XSL defined WEbObject)
	-
	- Channels extract information from the system wile XSl defined are functions in the
	- XSL display.
	*/
	var $WebObjects				 	= array(
	//		array(2,"Display the Guestbooks","GUESTBOOK_DISPLAY",0,0)
	);
	
	/**
	*  filter options
	*/
	var $display_options			= array();
	
	var $admin_access				= 0;
	var $discussion_admin_access	= 0;
	var $approve_comments_access	= 0;
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
		//print "<li>".__FILE__."@".__LINE__."<p>$user_command</p></li>";
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
				return $this->module_version;
			}
			
			/**
			* Specific Functions
			*/
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->display_guestbooks($parameter_list);
			}
			if ($user_command==$this->module_command."VIEW_LIST"){
				return $this->list_comments($parameter_list);
			}
			if (
				($user_command==$this->module_command."ADD") ||
				($user_command==$this->module_command."EDIT")
				){
				return $this->modify_comment($parameter_list);
			}
			if ($user_command==$this->module_command."DISPLAY_PREVIEW"){
				return $this->preview_comment($parameter_list);
			}
			if ($user_command==$this->module_command."SAVE_CONFIRMED"){
				return $this->commit_comment($parameter_list);
			}
			if (
			($user_command==$this->module_command."PREVIEW") ||
			($user_command==$this->module_command."SAVE")
			){
				return $this->save_comment($parameter_list);
			}
		}
		return "";
	}
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
		$this->load_locale("guestbook");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* check to see if the user is able to post directly to he web site
		*/
		$this->approve_comments_access	= 0;
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (
				("ALL"==$access[$index]) ||
				("GUESTBOOKADMIN_ALL"==$access[$index]) ||
				("GUESTBOOKADMIN_APPROVER"==$access[$index])
				){
					$this->approve_comments_access =1;
				}
			}
		}
		return 1;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: preview_comment()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is used preview the comment on the screen.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function preview_comment($parameters){
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
//		print "<li>".__FILE__."@".__LINE__."<pre>".print_r($_SESSION, true)."</pre></li>";
//		$this->exitprogram();
//		$book_id 	= $this->check_parameters($parameters,"book_id");
//		$edit_id 	= $this->check_parameters($parameters,"edit_id");
		$ep 					= split("::", $this->check_parameters($_SESSION,"PREVIEW_GUESTBOOK_EDIT","-1::-1"));
		$edit_id 	= $ep[1];
		$book_id	= $ep[0];
		if($book_id==-1){
			return "";
		}
		
		$sql = "
		select gbe_label, mi_memo from guestbooks_entry
				left outer join memo_information on mi_link_id = gbe_identifier
				where gbe_identifier = $edit_id and gbe_book = $book_id and gbe_client= $this->client_identifier and (mi_client=$this->client_identifier or mi_client is null) and (mi_type='GUESTBOOK_' or mi_type is null) and (mi_field='gbe_message' or mi_field is null)";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$comment_title	= "";
		$comment_msg	= "";
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
//			$comment_title	= $r["gbe_label"];
			$comment_msg	= $r["mi_memo"];
		}
		$label = LOCALE_COMMENTS_PREVIEW;
		$_SESSION["PREVIEW_GUESTBOOK_EDIT"] = $book_id."::".$edit_id;
		$out   = "<module name=\"page\" display=\"form\">";
		$out  .= "<form method='post' name=\"process_form\" label=\"".$label."\">";
//		$out .= "	<input type=\"hidden\" name=\"book_id\"><![CDATA[$book_id]]></input>";
//		$out  .= "	<input type='hidden' name='edit_id' ><![CDATA[$edit_id]]></input>";
		$out  .= "	<input type='hidden' name='command' ><![CDATA[GUESTBOOK_SAVE_CONFIRMED]]></input>";
		$out  .= "	<text ><![CDATA[".LOCALE_COMMENTS_PREVIEW_MSG."]]></text>";
//		$out  .= "	<text label=\"Title\"><![CDATA[<strong>$comment_title</strong>]]></text>";
		$out  .= "	<text label=\"Message\" class='gbpreview'><![CDATA[$comment_msg]]></text>";
		$out  .= "	<input type='button' command='GUESTBOOK_EDIT' iconify='CANCEL' value='Edit'/>";
		if ($comment_msg == ""){
			$out .= "<text><![CDATA[".LOCALE_GUESTBOOK_NO_CONTENT."]]></text>";
		} else {
			$out  .= "	<input type='submit' iconify='SAVE' value='".SAVE_DATA."'/>";
		}
		$out  .= "</form>";
		$out  .= "</module>";
		return $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: modify_comment()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is used
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function modify_comment($parameters){
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		$comment_message			= "";
		$comment_title				= "";
		$reply_id					= "-1";
		$edit_id					= "-1";
		$page_id  					= "-1";
		$gbe_name					= "";
		$trans_id					= "-1";
		$id							= $this->check_parameters($parameters,"identifier");
		if ($id==""){
			$id = $this->check_parameters($parameters,"unset_identifier");
		}
		if ($this->check_parameters($parameters,"command")==$this->module_command."ADD"){
			$book_id 				= $id;
			$label = LOCALE_ADD_COMMENT_FORM;
			$_SESSION["PREVIEW_GUESTBOOK_EDIT"] = $id."::-1";

		} else if ($this->check_parameters($parameters,"command")==$this->module_command."EDIT"){
//			$book_id			 	= $this->check_parameters($parameters,"book_id","-1");
//			$edit_id			 	= $this->check_parameters($parameters,"edit_id","-1");
			$ep 					= split("::", $this->check_parameters($_SESSION,"PREVIEW_GUESTBOOK_EDIT","-1::-1"));
			$edit_id 	= $ep[1];
			$book_id	= $ep[0];
			if($edit_id==-1 || $book_id==-1){
				return "";
			}
			$label 					= LOCALE_EDIT_COMMENT_FORM;
			$sql 					= "select * from guestbooks_entry 
											left outer join memo_information on mi_link_id = gbe_identifier
										where gbe_identifier = $edit_id and gbe_book = $book_id and gbe_client= $this->client_identifier and (mi_client=$this->client_identifier or mi_client is null) and (mi_type='GUESTBOOK_' or mi_type is null) and (mi_field='gbe_message' or mi_field is null)";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				//						where gbe_book = $book_id and gbe_identifier = $edit_id and gbe_client=$this->client_identifier";
			$result				 	= $this->call_command("DB_QUERY",array($sql));
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
//				$comment_title 			= $r["gbe_label"];
				$comment_message 		= str_replace("\n","[[jsreturn]]",strip_tags($this->html_2_txt(html_entity_decode($this->check_parameters($r,"mi_memo")))));
				$gbe_name				= $this->check_parameters($r,"gbe_name");
			}
		}
		
		$sp_comments_open = strtoupper($this->check_prefs(Array("sp_comments_open","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No")));
		//if($sp_comments_open=="YES" || $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER","0")>0){
		$comment_type 	= strtoupper($this->check_parameters($parameters,"entry_type","WEB"));
		$out  = "<module name=\"page\" display=\"form\">";
		$out .= "<form method='post' name=\"process_form\" label=\"".$label."\">";
//		$out .= "	<input type=\"hidden\" name=\"book_id\"><![CDATA[$book_id]]></input>";
		$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[GUESTBOOK_PREVIEW]]></input>";
//		$out .="	<input type=\"hidden\" name=\"edit_id\" value=\"$edit_id\"/>";
		if ($this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0)>0){
		} else {
			$out .= "	<input type=\"text\" name=\"gbe_name\" size=\"60\" label=\"".LOCALE_YOURNAME."\"><![CDATA[$gbe_name]]></input>";
		}
//		if ($this->check_parameters($parameters,"command")=="GUESTBOOK_ADD"){
//			$out .= "	<input type=\"text\" name=\"comment_title\" size=\"60\" label=\"".LOCALE_SUBJECT."\"><![CDATA[$comment_title]]></input>";
//		} else {
//			$out .="		<input type=\"hidden\" name=\"comment_title\" ><![CDATA[$comment_title]]></input>";
//			$out .="		<text label=\"".LOCALE_SUBJECT."\"><![CDATA[$comment_title]]></text>";
//		}
		$out .= "	<textarea name=\"comment\" label=\"".LOCALE_COMMENT."\" size=\"60\" height=\"10\" type=\"PLAIN-TEXT\"><![CDATA[$comment_message]]></textarea>";
		$out .= "	<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .= "</form>";
		$out .= "</module>";
		return $out;
		//		}else{
		//		$out = $this->call_command("USERS_SHOW_LOGIN",Array("redirect"=>"command=GUESTBOOK_ADD&identifier=$page_id"));
		//	return $out;
		//}
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: save_comment()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is used
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function save_comment($parameters){
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		//print "<li>".__FILE__."@".__LINE__."<pre>".print_r($_SESSION, true)."</pre></li>";
		$sp_comments_open = strtoupper($this->check_prefs(Array("sp_comments_open","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No")));
		$user		= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
		$ep 		= split("::", $this->check_parameters($_SESSION,"PREVIEW_GUESTBOOK_EDIT","-1::-1"));
		$book_id	= $ep[0];
		$edit_id 	= $ep[1];
		if($book_id==-1){
			return "";
		}
		$command	= $this->check_parameters($parameters,"command");
		$open_status= 0;
		
		$sql 		= "select * from guestbooks_list where gb_identifier = $book_id and gb_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
		
			$open_status	= $r["gb_status"];
			$workflow_status= $r["gb_workflow_status"];
		}
		$this->call_command("DB_FREE",array($result));
		if ($open_status==2){ // open
//			if($user>0){
				if ($command == $this->module_command."PREVIEW"){
					$now 		= $this->libertasGetDate("Y/m/d H:i:s");
					$msg		= $this->validate($this->tidy($this->txt2html(strip_tags($this->check_locale_starter($this->check_parameters($parameters,"comment"))))));
//					$title		= $this->strip_tidy($this->validate(strip_tags($this->check_locale_starter($this->check_parameters($parameters,"comment_title")))));
					$gbe_name	= htmlentities(strip_tags($this->validate($this->check_locale_starter($this->check_parameters($parameters,"gbe_name")))));
					if ($edit_id==-1){
//								gbe_label, 
//								'$title',
						$sql = "insert into guestbooks_entry (
								gbe_client, 
								gbe_book,
								gbe_creation_date, 
								gbe_user, 
								gbe_log_details,
								gbe_status,
								gbe_approved_by,
								gbe_name
							) values (
								'$this->client_identifier',
								'$book_id',
								'$now',
								$user,
								'".$this->check_parameters($_SESSION,"SESSION_USER_ACCESS_IDENTIFIER",0)."',
								'0',
								0,
								'$gbe_name'
							)";
						$sql_retrieve = "select * from guestbooks_entry where gbe_client=$this->client_identifier and gbe_user = $user and gbe_creation_date = '$now' and gbe_log_details ='".$this->check_parameters($_SESSION,"SESSION_USER_ACCESS_IDENTIFIER",0)."' ";
					} else {
						$sql_retrieve = "";
//								gbe_label	= '$title',
						$sql = "update guestbooks_entry set
								gbe_name	= '$gbe_name'
							where 
								gbe_identifier = $edit_id and 
								gbe_client=$this->client_identifier
						";
					}
					$this->call_command("DB_QUERY",array($sql));
					if ($sql_retrieve!=""){
						$result 	= $this->call_command("DB_QUERY",array($sql_retrieve));
						$r = $this->call_command("DB_FETCH_ARRAY",array($result));
						$edit_id	= $r["gbe_identifier"];
					}
					$this->call_command("MEMOINFO_UPDATE",array(
						"mi_type"		=> $this->module_command,
						"mi_memo"		=> $msg,
						"mi_link_id"	=> $edit_id, 
						"mi_field" 		=> "gbe_message"
					));
					$_SESSION["PREVIEW_GUESTBOOK_EDIT"] = $book_id."::".$edit_id;
					$out = $this->preview_comment(Array());
				}
				if ($command == $this->module_command."SAVE"){
				}
	//		}else{
	//			$out="";
	//		}
		} else {
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .="	<form name=\"process_form\" label=\"".LOCALE_COMMENT_SAVED."\">";
			$out .="		<text><![CDATA[".LOCALE_COMMENT_BOOK_CLOSED."]]></text>";
			$out .="		<input type=\"submit\" iconify=\"CANCEL\" value=\"".LOCALE_BACK."\" />";
			$out .="	</form>";
			$out .="</module>";
		}
		return $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- FN:: commit_comment()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function is used to save the comment
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function commit_comment($parameters){
//		$edit_id 	= $this->check_parameters($parameters,"edit_id");
//		$book_id	= $this->check_parameters($parameters,"book_id");
		$user		= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
		$ep 		= split("::", $this->check_parameters($_SESSION,"PREVIEW_GUESTBOOK_EDIT","-1::-1"));
		unset($_SESSION['PREVIEW_GUESTBOOK_EDIT']);
		$edit_id 	= $ep[1];
		$book_id	= $ep[0];
		if($book_id==-1){
			return "";
		}
		$sql 		= "select * from guestbooks_list where gb_identifier = $book_id and gb_client=$this->client_identifier";
		$open_status = 0;
		$result 	= $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$open_status	= $r["gb_status"];
			$workflow_status= $r["gb_workflow_status"];
		}
		$this->call_command("DB_FREE",array($result));
		
		if ($open_status == 2){ // open
			switch ($workflow_status) {
				case 1:
					//Everyone can publish directly to the site
					$comment_status = 2;
					$comment_approved_by=$user;
					break;
				case 2:
					//Registered users can publish to the site directly, anonymous comments to be approved
					if($user>0){
						$comment_status = 2;
						$comment_approved_by=$user;
					} else {
						$comment_status = 1;
						$comment_approved_by=0;
					}
				break;
				case 3:
					//User must register first no anonymous access to add functions
					$comment_status = 1;
					$comment_approved_by=0;
					break;
				case 4:
					//All content to be approved unless user has approval status set.
					if($this->approve_comments_access){
						$comment_status = 2;
						$comment_approved_by=$user;
					} else {
						$comment_status = 1;
						$comment_approved_by=$user;
					}
				break;
			}
			/*		if (($workflow_status=="YES") && ( ! $this->approve_comments_access)){
			$comment_status = 2;
			$comment_approved_by=$user;
			}else{
			if($this->approve_comments_access){
			$comment_status = 2;
			$comment_approved_by=$user;
			} else {
			$comment_status = 1;
			$comment_approved_by=0;
			}
			}*/
			$sql = "update guestbooks_entry set gbe_status=$comment_status, gbe_approved_by = $comment_approved_by where gbe_identifier=$edit_id and gbe_client=$this->client_identifier and gbe_book=$book_id";
			$this->call_command("DB_QUERY",array($sql));
			if ($comment_status==2){
				$this->cache_comments(Array("comment_id" => $edit_id));
				$out  ="<module name=\"".$this->module_name."\" method=\"get\" display=\"form\">";
				$out .="	<form name=\"process_form\" label=\"".LOCALE_COMMENT_SAVED."\">";
				$out .="		<text><![CDATA[".LOCALE_COMMENT_SAVED_CONFIRM."]]></text>";
				$out .="		<input type=\"submit\" iconify=\"CANCEL\" value=\"".LOCALE_BACK."\" />";
				$out .="	</form>";
				$out .="</module>";
			} else {
				$out  ="<module name=\"".$this->module_name."\" method=\"get\" display=\"form\">";
				$out .="	<form name=\"process_form\" label=\"".LOCALE_COMMENT_SAVED."\">";
				$out .="		<text><![CDATA[".LOCALE_COMMENT_SAVED_CONFIRM."]]></text>";
				$out .="		<input type=\"submit\" iconify=\"CANCEL\" value=\"".LOCALE_BACK."\" />";
				$out .="	</form>";
				$out .="</module>";
			}
			/*************************************************************************************************************************
            * send the approver opf the guest book an email
            *************************************************************************************************************************/
			if($comment_status == 1){
				$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_GUESTBOOK_APPROVER__"], "identifier" => $edit_id, "url"=> "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=GUESTBOOKADMIN_VIEW_LIST&identifier=$book_id"));
			}
//			if($comment_status == 2){
//				$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_GUESTBOOK__"], "identifier" => $edit_id, "url"=> $this->parent->script));
//			}

		} else {
			$out  ="<module name=\"".$this->module_name."\" method=\"get\" display=\"form\">";
			$out .="	<form name=\"process_form\" method=\"get\" label=\"".LOCALE_COMMENT_SAVED."\">";
			$out .="		<text><![CDATA[".LOCALE_COMMENT_BOOK_CLOSED."]]></text>";
			$out .="		<input type=\"submit\" iconify=\"CANCEL\" value=\"".LOCALE_BACK."\" />";
			$out .="	</form>";
			$out .="</module>";
		}
		return $out;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn ::  display_guestbooks
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function display_guestbooks($parameters){
		$sql = "
			select 
				distinct 
					guestbooks_list.*, 
					menu_label, menu_identifier ,
					gbs_label, gbws_label
				from guestbooks_list
					left outer join guestbooks_entry on guestbooks_entry.gbe_book = guestbooks_list.gb_identifier
					left outer join menu_data on guestbooks_list.gb_menu_locations = menu_data.menu_identifier
					left outer join guestbooks_status on guestbooks_status.gbs_identifier = gb_status
					left outer join guestbooks_workflow_status on guestbooks_workflow_status.gbws_identifier = gb_workflow_status
				where 
					guestbooks_list.gb_client=$this->client_identifier and 
					menu_data.menu_url = '".$this->parent->script."'
				group by 
					guestbooks_list.gb_identifier, guestbooks_entry.gbe_book
				order by guestbooks_list.gb_label asc
		";
		$out = "";
		$result = $this->call_command("DB_QUERY",Array($sql));
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			if ($number_of_records>1){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				$prev = $this->page_size;
				$this->page_size = $number_of_records+1;
				$page = $this->check_parameters($parameters,"page",1);
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
				
				$variables["PAGE_BUTTONS"] = Array();
				
				$variables["NUMBER_OF_ROWS"]	= $number_of_records;
				$variables["START"]				= $goto;
				$variables["FINISH"]			= $finish;
				$variables["CURRENT_PAGE"]		= $page;
				$variables["NUMBER_OF_PAGES"]	= $num_pages;
				//			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
				$start_page=intval($page / $this->page_size);
				$remainder = $page % $this->page_size;
				if ($remainder>0){
					$start_page++;
				}
				
				$variables["START_PAGE"]		= $start_page;
				
				if (($start_page+$this->page_size)>$num_pages){
					$end_page	 =	$num_pages;
				}else{
					$end_page	=	$this->page_size;
				}
				
				$variables["END_PAGE"]			= $end_page;
				$variables["FILTER"]			= "";
				$variables["RESULT_ENTRIES"] =Array();
				$counter=0;
				$page_discussion=0;
				$trans_page=0;
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) &&($counter<$this->page_size)){
					$counter++;
					$index=count($variables["RESULT_ENTRIES"]);
					$att = $this->get_message_counter(Array("book"=>$r["gb_identifier"]));
					$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["gb_identifier"],
					"ENTRY_BUTTONS"	=> Array(
						Array("LIST","GUESTBOOK_VIEW_LIST",LOCALE_VIEW_COMMENTS)
					),
					"attributes"	=> Array(
					Array(LOCALE_TITLE,				$this->check_parameters($r,"gb_label")), 
					Array("Message Count",			$this->check_parameters($att,"Total",0)),
					Array("Last Message Posted",	$this->check_parameters($att,"LastUpdated","na"))
					)
					);
					
					
				}
				$this->page_size=$prev ;
//				print "<li>".__FILE__."@".__LINE__."<pre>".print_r($variables, true)."</pre></li>";
				return $this->generate_list($variables);
			} else {
				// only one guest book in this location
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$parameters["identifier"] = $r["gb_identifier"];
				}
				return $this->list_comments($parameters);
			}
		}
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn ::  get_message_counter()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function will return an Array of information about the guest book entry total records
	- and Last Updated
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	function get_message_counter($parameters){
		$book = $this->check_parameters($parameters,"book");
		$sql = "select count(gbe_book) as Total, max(gbe_creation_date) as LastUpdated from
					guestbooks_entry 
				where 
					guestbooks_entry.gbe_client=$this->client_identifier and 
					guestbooks_entry.gbe_book=$book and
					guestbooks_entry.gbe_status = 2
				group by 
					guestbooks_entry.gbe_book";
		$result =$this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			return Array("Total"=>$r["Total"], "LastUpdated"=>$r["LastUpdated"]);
		}
		
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: list_comments()
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- this function will display a specific guestbook and its comments in a page spanning format.
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function list_comments($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$found =0;
		$cmd = $this->check_parameters($parameters,"command");
		if (substr($cmd,0,strlen($this->module_command))==$this->module_command){
			$found =1;
		}
		if ($cmd==$this->module_command."DISPLAY"){
			$found=0;
		}
		if ($found==0){
			$sql = "
				select 
					guestbooks_list.*,
					guestbooks_entry.*,
					mi1.mi_memo as gbe_description,
					mi2.mi_memo as gb_description
				from guestbooks_list
					left outer join guestbooks_entry 
						on (guestbooks_list.gb_identifier = guestbooks_entry.gbe_book and 
							gbe_status=2)
					left outer join memo_information as mi1 
						on (mi1.mi_link_id = guestbooks_entry.gbe_identifier and 
							mi1.mi_field = 'gbe_message' and 
							mi1.mi_client = gbe_client)
					left outer join memo_information as mi2 
						on (mi2.mi_link_id = guestbooks_list.gb_identifier and 
							mi2.mi_field = 'gb_description' and 
							mi2.mi_client = gb_client)
				where
					(guestbooks_list.gb_status=2 or guestbooks_list.gb_status=3) and 
					guestbooks_list.gb_client=$this->client_identifier and 
					guestbooks_list.gb_identifier = $identifier
				order by guestbooks_entry.gbe_identifier desc
			";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$out = "";
			$result = $this->call_command("DB_QUERY",Array($sql));
			$variables["GROUPING_IDENTIFIER"] = $identifier;
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
				if ($number_of_records>0){
					if ($this->module_debug){
						$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
					}
					$prev = $this->page_size;
					$this->page_size = 10;
					$page = $this->check_parameters($parameters,"page",1);
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
					
					$variables["START"]				= $goto;
					$variables["FINISH"]			= $finish;
					$variables["CURRENT_PAGE"]		= $page;
					$variables["NUMBER_OF_PAGES"]	= $num_pages;
					$variables["PAGE_COMMAND"]		= "GUESTBOOK_VIEW&amp;identifier=$identifier";
					$start_page=intval($page / $this->page_size);
					$remainder = $page % $this->page_size;
					if ($remainder>0){
						$start_page++;
					}
					
					$variables["START_PAGE"]		= $start_page;
					
					if (($start_page+$this->page_size)>$num_pages){
						$end_page	 =	$num_pages;
					}else{
						$end_page	=	$this->page_size;
					}
					
					$variables["END_PAGE"]			= $end_page;
					$variables["FILTER"]			= "";
					$variables["RESULT_ENTRIES"] =Array();
					$counter=0;
					$page_discussion=0;
					$trans_page=0;
					$status = 1;
					$workflow_status=1;
					$display_format=1;
					while (($r = $this->call_command("DB_FETCH_ARRAY",array($result))) &&($counter<$this->page_size)){
						$display_format=$r["gb_display_format"];
						$index=count($variables["RESULT_ENTRIES"]);
						if ($this->check_parameters($r,"gbe_creation_date","__NOT_VALID__")!="__NOT_VALID__"){
							$counter++;
							$n1 = $this->check_parameters($r,"gbe_name");
							if ($n1 != ""){
								$gbe_name=$n1;
							} else {
								$gbe_name = $this->call_command("CONTACT_GET_NAME",Array("contact_user" => $r["gbe_user"],"format"=>"not_indexing"));
							}
							$variables["RESULT_ENTRIES"][$index]=Array(
								"identifier"	=> $r["gbe_identifier"],
								"ENTRY_BUTTONS"	=> Array(),
								"attributes"	=> Array(
									Array(LOCALE_AUTHOR,		$gbe_name,"Column1",""),
									Array(LOCALE_DATE,			Date("D jS M Y H:i:s",strtotime($this->check_parameters($r,"gbe_creation_date"))),"Column1",""),
									Array(LOCALE_MESSAGE,		$this->check_parameters($r,"gbe_description",""),"Column2","")
								)
							);
						}
						$workflow_status	= $r["gb_workflow_status"];
						$status				= $r["gb_status"];
						$toptext_label		= $r["gb_label"];
						$toptext			= $r["gb_description"];
					}
					$variables["NUMBER_OF_ROWS"]	= $counter;
					//Comment guest book label $variables["LABEL"]				= $toptext_label;
					//Comment guest book label$variables["DESCRIPTION"]		= $toptext;
					//$variables["TOP_TEXT"]			= $toptext;
//					print $status." ".$workflow_status." ".$this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0);
					if($status==2){
						if ($workflow_status!=3 || $this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)!=0){
							/*
							level three must be logged in first
							*/
							$variables["PAGE_BUTTONS"] = Array(
								Array("ADD","GUESTBOOK_ADD", LOCALE_GUESTBOOK_ADD_NEW)
							);
							/*
							if Enterprise and signed in then allow watching to take place
							*/
							/*
							// removed elerts
							if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN",0)!=0 && $this->parent->have_module("ELERT_")){
								$variables["PAGE_BUTTONS"][count($variables["PAGE_BUTTONS"])] = Array("WATCH","ELERT_SIGNUP", LOCALE_ELERT_SIGNUP);
							}
							
							*/
						} else {
							$variables["PAGE_BUTTONS"] = Array(
								Array("LOGIN","USERS_SHOW_LOGIN", LOCALE_LOGIN_TO_USE)
							);
						}
					}
					$this->page_size=$prev ;
					return "<display_format><![CDATA[$display_format]]></display_format>".$this->generate_list($variables);
				} else {
					return "";
				}
			}
		}
		return "";
	}
	
	function cache_comments($parameters){
		$comment_id = $this->check_parameters($parameters,"comment_id");		
	}
}
?>
