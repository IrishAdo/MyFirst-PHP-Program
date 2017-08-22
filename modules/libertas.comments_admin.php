<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.comments.php
* @date 06 Feb 2004
*/
/**
* This module is the module for displaying any comments for a page
*/

class comments_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_name_label			= "Comments Module (Administration)";
	var $module_name				= "comments_admin";
	var $module_admin				= "1";
	var $module_command				= "COMMENTSADMIN_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_COMMENTS";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.11 $';
	var $module_creation 			= "06/02/2004";
	var $searched					= 0;

	/**
	*  Management Menu entries
	*/
	
	var $module_admin_options 		= array(
		array("COMMENTSADMIN_LIST", "MANAGE_COMMENTS","")
	);
	/*
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    - Module Preferences
    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
    */
	var $preferences= Array(
			Array('sp_comment_require_approval'	,"LOCALE_SP_COMMENT_REQUIRE_APPROVAL"				,'Yes'	, 'Yes:No'					, "COMMENTSADMIN_",	"ECMS"),
			Array('sp_comments_open'			,"LOCALE_SP_COMMENTS_OPEN"							,'Yes'	, 'Yes:No'					, "COMMENTSADMIN_",	"ECMS")
	);
	
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access = array(
		array("COMMENTSADMIN_ALL","COMPLETE_ACCESS"),
		array("COMMENTSADMIN_APPROVER","ACCESS_LEVEL_APPROVER")
	);

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
		array(2,"Display the Page Comments","WEBOBJECTS_SHOW_PAGE_COMMENTS",0,0)
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
				
		if (strpos($user_command,$this->module_command)===0){
			if ($user_command==$this->module_command."GET_PREFS"){
				return $this->preferences;
			}
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
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Administration Module commands
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($this->admin_access==1){
				if ($user_command==$this->module_command."WEB_COMMENTS_TOGGLE"){
					return $this->toggle_comment($parameter_list);
				}
				if ($user_command==$this->module_command."CACHE"){
					return $this->cache_comment($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE"){
					return $this->comment_remove($parameter_list);
				}
				if ($user_command==$this->module_command."REMOVE_CONFIRM"){
					$this->comment_remove_confirm($parameter_list);
					$type		= $this->check_parameters($parameter_list,"entry_type");
					$identifier	= $this->check_parameters($parameter_list,"trans_identifier");
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=COMMENTSADMIN_VIEW&entry_type=$type&identifier=$identifier"));
				}
				if  ($user_command==$this->module_command."LIST"){
					return $this->mycomment_list($parameter_list);
				}
				if  ($user_command==$this->module_command."VIEW_LIST"){
					return $this->view_notes($parameter_list);
				}
				// single html page returned;
				if  ($user_command==$this->module_command."VIEW"){
					return $this->view_comment($parameter_list);
				}
				if ($this->approve_comments_access ==1){
					if ($user_command==$this->module_command."APPROVE"){
						return $this->comment_approve($parameter_list);
					}
				} else {
					if ($user_command==$this->module_command."APPROVE"){
						return $this->sorry_no_access_to_this_functionality($parameter_list);
					}
				}
				if ($user_command==$this->module_command."APPROVE_CONFIRM"){
					return $this->comment_approve_confirm($parameter_list);
				}
				if ($user_command == $this->module_command."MY_WORKSPACE"){
					return $this->retrieve_my_docs($parameter_list);
				}
				if (
					($user_command==$this->module_command."ADD") || 
					($user_command==$this->module_command."EDIT") || 
					($user_command==$this->module_command."RESPOND")
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
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		$this->admin_access=1;
		$this->approve_comments_access	= 0;
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		$this->discussion_admin_access=0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (
					("ALL"==$access[$index]) ||
					("COMMENTSADMIN_ALL"==$access[$index]) ||
					("COMMENTSADMIN_APPROVER"==$access[$index])
				){
					$this->approve_comments_access =1;
					$this->discussion_admin_access=1;
				}
				if (
					("ALL"==$access[$index]) ||
					("PAGE_ALL"==$access[$index]) ||
					("PAGE_DISCUSSION"==$access[$index])
				){
					$this->discussion_admin_access = 1;	
				}
	}
		}
		
		return 1;
	}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* FN:: preview_comment()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* this function is used preview the comment on the screen.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function preview_comment($parameters){
		$entry_type	= $this->check_parameters($parameters,"entry_type");
		$trans_id 	= $this->check_parameters($parameters,"trans_id");
		$edit_id 	= $this->check_parameters($parameters,"edit_id");
		$reply_id 	= $this->check_parameters($parameters,"reply_id");
		$page_id 	= $this->check_parameters($parameters,"page_id");

		$sql = "select * from page_comments where comment_identifier=$edit_id and comment_client=$this->client_identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$comment_title	= "";
		$comment_msg	= "";
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$comment_title	= $r["comment_title"];
			$comment_msg	= $r["comment_message"];
		}
		$label = LOCALE_COMMENTS_PREVIEW;
		$out   = "<module name=\"page\" display=\"form\">";
		$out  .= "<form method='post' name=\"process_form\" label=\"".$label."\">";
		$out  .= "	<input type=\"hidden\" name=\"trans_id\"><![CDATA[$trans_id]]></input>";
		$out  .= "	<input type=\"hidden\" name=\"reply_id\"><![CDATA[$reply_id]]></input>";
		$out  .= "	<input type=\"hidden\" name=\"page_id\"><![CDATA[$page_id]]></input>";
		$out  .= "	<input type='hidden' name='edit_id' ><![CDATA[$edit_id]]></input>";
		$out  .= "	<input type='hidden' name='command' ><![CDATA[COMMENTSADMIN_SAVE_CONFIRMED]]></input>";
		$out  .= "	<input type='hidden' name='entry_type' ><![CDATA[$entry_type]]></input>";
		$out  .= "	<text ><![CDATA[".LOCALE_COMMENTS_PREVIEW_MSG."]]></text>";
		$out  .= "	<text label=\"Title\"><![CDATA[<strong>$comment_title</strong>]]></text>";
		$out  .= "	<text label=\"Message\"><![CDATA[$comment_msg]]></text>";
		$out  .= "	<input type='button' command='COMMENTSADMIN_EDIT&amp;entry_type=$entry_type&amp;identifier=$edit_id&amp;page_id=$page_id' iconify='CANCEL' value='".LOCALE_CANCEL."'/>";
		$out  .= "	<input type='submit' iconify='SAVE' value='".SAVE_DATA."'/>";
		$out  .= "</form>";
		$out  .= "</module>";
		return $out;	
	}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* FN:: modify_comment()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* this function is used 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function modify_comment($parameters){
		$comment_message			= "";
		$comment_title				= "";
		$reply_id					= "-1";
		$edit_id					= "-1";
		$page_id  					= "-1";
		$trans_id					= "-1";
		$label						= LOCALE_ADD;
		$id							= $this->check_parameters($parameters,"identifier");
		if ($this->check_parameters($parameters,"command")=="COMMENTSADMIN_ADD"){
			$page_id 				= $this->check_parameters($parameters,"identifier","-1");
			$sql 					= "select trans_identifier from page_trans_data where trans_page = $page_id and trans_client= $this->client_identifier and trans_published_version =1";
			$result 				= $this->call_command("DB_QUERY",array($sql));
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r 					= $this->call_command("DB_FETCH_ARRAY",array($result));
				$trans_id 			= $r["trans_identifier"];
			}
			$label = LOCALE_ADD_COMMENT_FORM;
		} else if ($this->check_parameters($parameters,"command")=="COMMENTSADMIN_RESPOND"){
			$reply_id 				= $this->check_parameters($parameters,"identifier","-1");
			$sql 					= "select * from page_comments inner join page_trans_data on trans_identifier = comment_translation where comment_identifier=$reply_id and comment_client=$this->client_identifier";
			$result					= $this->call_command("DB_QUERY",array($sql));
			$comment_translation 	= -1;
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r 					= $this->call_command("DB_FETCH_ARRAY",array($result));
				$comment_title 		= "RE: ".$r["comment_title"];
				$trans_id 			= $r["trans_identifier"];
			}
			$label = LOCALE_RESPOND_COMMENT_FORM;
		} else if ($this->check_parameters($parameters,"command")=="COMMENTSADMIN_EDIT"){
			$edit_id			 	= $this->check_parameters($parameters,"identifier","-1");
			$page_id			 	= $this->check_parameters($parameters,"page_id","-1");
			$label 					= LOCALE_EDIT_COMMENT_FORM;
			$sql 					= "select * from page_comments inner join page_trans_data on trans_identifier = comment_translation where comment_identifier=$edit_id and comment_client=$this->client_identifier";
			$result				 	= $this->call_command("DB_QUERY",array($sql));
			$comment_translation 	= -1;
			$comment_title			= "";
			$comment_message		="";
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
				$comment_title 			= $r["comment_title"];
				$comment_message 		= $this->html_2_txt($this->check_parameters($r,"comment_message"));
				if($page_id==-1){
					$page_id 			= $r["trans_page"];
				}
			}
		} 
		
		$sp_comments_open = strtoupper($this->check_prefs(Array("sp_comments_open","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No")));
		if($sp_comments_open=="YES" || $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER","0")>0){
			$comment_type 	= strtoupper($this->check_parameters($parameters,"entry_type","WEB"));
			$out  = "<module name=\"page\" display=\"form\">";
			$out .= "<form method='post' name=\"process_form\" label=\"".$label."\">";
			$out .= "	<input type=\"hidden\" name=\"trans_id\"><![CDATA[$trans_id]]></input>";
			$out .= "	<input type=\"hidden\" name=\"edit_id\"><![CDATA[$edit_id]]></input>";
			$out .= "	<input type=\"hidden\" name=\"reply_id\"><![CDATA[$reply_id]]></input>";
			$out .= "	<input type=\"hidden\" name=\"page_id\"><![CDATA[$page_id]]></input>";
			$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[COMMENTSADMIN_PREVIEW]]></input>";
			$out .= "	<input type=\"hidden\" name=\"entry_type\" value=\"$comment_type\"/>";
			if ($this->check_parameters($parameters,"command")=="COMMENTSADMIN_ADD"){
				$out .= "	<input type=\"text\" name=\"comment_title\" size=\"60\" label=\"".LOCALE_SUBJECT."\"><![CDATA[$comment_title]]></input>";
			} else {
				$out .= "		<input type=\"hidden\" name=\"comment_title\" ><![CDATA[$comment_title]]></input>";
				$out .= "		<text label=\"".LOCALE_SUBJECT."\"><![CDATA[$comment_title]]></text>";
			}
			$out .= "	<textarea name=\"page_comment\" label=\"".LOCALE_COMMENT."\" size=\"60\" height=\"10\" type=\"PLAIN-TEXT\"><![CDATA[$comment_message]]></textarea>";
			$out .= "	<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"COMMENTSADMIN_VIEW_LIST&amp;entry_type=$comment_type&amp;identifier=$page_id\"/>";
			$out .= "	<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
			$out .= "</form>";
			$out .= "</module>";
			return $out;	
		}else{
			$out = $this->call_command("USERS_SHOW_LOGIN",Array("redirect"=>"command=PAGE_ADD_COMMENT&identifier=$page_id"));
			return $out;	
		}
	}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* FN:: save_comment()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* this function is used 
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function save_comment($parameters){
		$out="";
		$sp_comments_open = strtoupper($this->check_prefs(Array("sp_comments_open","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No")));
		$user		= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
		$command	= $this->check_parameters($parameters,"command");
		$entry_type	= $this->check_parameters($parameters,"entry_type","WEB");
		if($sp_comments_open=="YES" || $user>0){
			if ($command == "COMMENTSADMIN_PREVIEW"){
				$trans_id 	= $this->check_parameters($parameters,"trans_id");
				$edit_id 	= $this->check_parameters($parameters,"edit_id");
				$reply_id 	= $this->check_parameters($parameters,"reply_id");
				$page_id 	= $this->check_parameters($parameters,"page_id");
				if ($user==0){
					$user=-1;
				}
				if ($entry_type=="WEB"){
					$insert_type 	= 0;
				}else{
					$insert_type 	= 1;
				}
				$now 		= $this->libertasGetDate("Y/m/d H:i:s");
				$msg		= $this->validate($this->tidy($this->txt2html(strip_tags($this->check_parameters($parameters,"page_comment")))));
				$title		= $this->strip_tidy($this->validate(strip_tags($this->check_parameters($parameters,"comment_title"))));
				if ($edit_id==-1){
					if ($reply_id==-1){
						$reply_id=0;
					}
					$sql = "insert into page_comments (
								comment_translation, 
								comment_client, 
								comment_title, 
								comment_message, 
								comment_date, 
								comment_user, 
								comment_response_to, 
								comment_type,
								comment_status,
								comment_approved_by
							) values (
								'$trans_id',
								'$this->client_identifier',
								'".$title."',
								'".$msg."',
								'$now',
								$user,
								'$reply_id',
								'$insert_type',
								0,
								0
							)";
					$sql_retrieve = "select * from page_comments where comment_client=$this->client_identifier and comment_user = $user and comment_date = '$now'";
				} else {
					$sql_retrieve = "";
//									comment_title = '".$title."',

					$sql = "update page_comments set
								comment_message = '".$msg."'
							where 
								comment_identifier = $edit_id and 
								comment_client=$this->client_identifier
						";
				}
				$this->call_command("DB_QUERY",array($sql));
				if ($sql_retrieve!=""){
					$result 	= $this->call_command("DB_QUERY",array($sql_retrieve));
					$r = $this->call_command("DB_FETCH_ARRAY",array($result));
					$edit_id	= $r["comment_identifier"];
				}

				$out = $this->call_command("COMMENTSADMIN_DISPLAY_PREVIEW",Array("entry_type"=>$entry_type, "trans_id" => $trans_id, "edit_id" => $edit_id, "reply_id" 	=> $reply_id, "page_id" 	=> $page_id));
			}
			if ($command == "PAGE_SAVE_COMMENT"){
				
			}
		}else{
			$out="";
		}
		return $out;
	}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* FN:: comment_remove_confirm()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* this function is used delete the requested comment
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function comment_remove_confirm($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$sql = "delete from page_comments where comment_identifier=$identifier and comment_type = 0 and comment_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
	}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* FN:: comment_remove()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* this function is used to remove a comment from the system
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function comment_remove($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$sql = "select * from page_comments where comment_identifier=$identifier and comment_client=$this->client_identifier";
		$result 	= $this->call_command("DB_QUERY",array($sql));
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$comment_translation 	= $r["comment_translation"];
			$comment_type 			= $r["comment_type"];
		}
		$out ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .="<form name=\"process_form\" label=\"".LOCALE_REMOVE_COMMENT_FORM."\">";
			$out .="	<input type=\"hidden\" name=\"trans_identifier\" value=\"$comment_translation\"/>";
			$out .="	<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
			$out .="	<input type=\"hidden\" name=\"command\" value=\"COMMENTSADMIN_REMOVE_CONFIRM\"/>";
			$out .="	<text><![CDATA[".LOCALE_REMOVE_COMMENT."]]></text>";
			$out .="	<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"COMMENTSADMIN_LIST\"/>";
			$out .="	<input type=\"submit\" iconify=\"YES\" value=\"".LOCALE_NO."\" />";
			$out .="</form>";
		$out .="</module>";
		return $out;
	}
/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* FN:: cache_comment()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* this function is used cahce the comments out into an XML file
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function cache_comment($parameters){
		$comment_id = $this->check_parameters($parameters,"comment_id");
		$trans_page = $this->check_parameters($parameters,"trans_page");
		$trans_identifier = $this->check_parameters($parameters,"trans_identifier");
		if (($comment_id!="") && ($trans_page=="") && ($trans_identifier=="")){
			$sql = "select trans_page from page_comments 
						inner join page_trans_data on page_trans_data.trans_identifier = comment_translation
			where comment_identifier=$comment_id and comment_client=$this->client_identifier;";
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$trans_page=$r["trans_page"];
			}
		}
		if (($trans_page=="") && ($trans_identifier!="")){
			$sql = "select * from page_trans_data where page_trans_data.trans_identifier=$trans_identifier;";
			$result = $this->call_command("DB_QUERY",Array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$trans_page=$r["trans_page"];
			}
		}
		
			$sql = "select 
						page_comments.*, page_trans_data.trans_page 
					from page_comments 
						inner join page_trans_data on page_trans_data.trans_identifier = page_comments.comment_translation 
					where 
						page_trans_data.trans_page = $trans_page and 
						page_trans_data.trans_client = $this->client_identifier and 
						page_comments.comment_type != 1 and 
						page_comments.comment_status = 2
					";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$company ="";
		$out="";
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				if ($r["comment_user"]!=-1){
					$company = $this->call_command("CONTACT_GET_COMPANY",Array("user_identifier" => $r["comment_user"]));
				}
				$out .= "<comment identifier=\"".$r["comment_identifier"]."\" page=\"".$r["trans_page"]."\" translation=\"".$r["comment_translation"]."\" response_to=\"".$r["comment_response_to"]."\">\n";
				$out .= "	<date><![CDATA[".$this->libertasGetDate("r",strtotime($r["comment_date"]))."]]></date>\n";
				$out .= "	<title><![CDATA[".$r["comment_title"]."]]></title>\n";
				$out .= "	<body><![CDATA[".$r["comment_message"]."]]></body>\n";
				$out .= "	<user identifier=\"".$r["comment_user"]."\"><![CDATA[".$this->call_command("CONTACT_GET_NAME",Array("contact_user" => $r["comment_user"],"format"=>"not_indexing"))."]]></user>\n";
				$out .= "	<company><![CDATA[".$company."]]></company>\n";
				$out .= "</comment>\n";
			}
			if (strlen($out)>0){
				$out ="<comments>\n".$out."</comments>";
			}
		}
		if (strlen($out)>0){
			$root = $this->parent->site_directories["ROOT"];
			$lang = "en";
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
			$fp = fopen($data_files."/comments_".$this->client_identifier."_".$lang."_".$trans_page.".xml", 'w');
			fwrite($fp, $out);
			fclose($fp);
			$um = umask(0);
			@chmod($data_files."/comments_".$this->client_identifier."_".$lang."_".$trans_page.".xml", LS__FILE_PERMISSION);
			umask($um);
		}
	}

	function view_comment($parameters){
		$identifier 		= strtoupper($this->check_parameters($parameters,"identifier","-1"));
		if ($identifier==-1){
			return "";
		} else {
			/**
			* Produce the list of notes for this page, no options
			*/
			$has_contact = $this->call_command("ENGINE_HAS_MODULE",Array("CONTACT_"));
			$join = "";
			if ($has_contact==1){
				$join .= " inner join contact_data on comment_user=contact_user";
			}
			$has_email = $this->call_command("ENGINE_HAS_MODULE",Array("EMAIL_"));
			if ($has_email==1){
				$join .= " inner join email_addresses on email_contact=contact_identifier";
			}
			$sql = "select page_comments.*, contact_data.* from page_comments
							$join
						where comment_identifier = $identifier and 
							comment_client=$this->client_identifier";
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
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$title	= strip_tags(html_entity_decode($this->check_parameters($r,"comment_title","No Title Supplied")));
					$content= $this->check_parameters($r,"comment_message");
					$author	= $this->check_parameters($r,"contact_first_name","unknown").", ".$this->check_parameters($r,"contact_last_name","unknown");
					$email	= $this->check_parameters($r,"email_address","unknown");
					$date	= $r["comment_date"];
				}		
				return "
<html>
	<head>
		<title>$title</title>
		<meta http-equiv='Pragma' content='no-cache'>
 		<link rel='stylesheet' type='text/css' href='/libertas_images/editor/libertas/lib/themes/default/css/dialog.css'>
	</head>
	<body>
		<script language='javascript'>
		<!--
			window.name = 'key_generator';
		//-->
		</script>
		<div style='border:1 solid Black; padding: 5 5 5 5; height:100%'>
			<P id=tableProps CLASS=tablePropsTitle>$title</P>
			<center><input type='button' class='bt' value='Close Window' onclick='javascript:window.close()'/></center>
			<table width='390px' border=0>
				<tr><td><table width='100%'>
				<tr><td><p>Author :: $author</p>
					$content
				</td></tr>
				</table></td></tr>
			</table>
			<center><input type='button' class='bt' value='Close Window' style='vertical-align:bottom;' onclick='javascript:window.close()'/></center>
		</div>
	</body>
</html>";
			}
		}
	}

	function view_notes($parameters){
		$type 		= strtoupper($this->check_parameters($parameters,"entry_type","web"));
		$lock 		= strtoupper($this->check_parameters($parameters,"lock","0"));
		$identifier = $this->check_parameters($parameters,"identifier","-1");

		/**
		* Produce the list of notes for this page, no options
		*/
		$has_contact = $this->call_command("ENGINE_HAS_MODULE",Array("CONTACT_"));
		if ($has_contact==1){
//			$join = "LEFT OUTER join contact_data on comment_user=contact_user and comment_client= contact_client";
			$join = "left outer join contact_data on comment_user=contact_user and comment_client= contact_client and comment_user !=0";
		}
		$where =" and comment_type='0'";
		if ($lock=="1"){
			$where =" and comment_status='1'";
		}
		$page_identifier = $identifier;
		$sql="select * from page_comments 
				inner join page_trans_data on page_comments.comment_translation=page_trans_data.trans_identifier and comment_client = trans_client
				inner join page_data on page_data.page_identifier = page_trans_data.trans_page and page_client = trans_client
				$join 
				where (page_comments.comment_status=1 or page_comments.comment_status=2 or page_comments.comment_status is null) and 
				page_trans_data.trans_page=$identifier and 
				comment_client=$this->client_identifier 
				$where order 
				by comment_status asc, comment_identifier desc";
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
			$prev = $this->page_size;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
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
			
			if ($this->discussion_admin_access==1){
				$variables["PAGE_BUTTONS"] = Array(
					Array("TOGGLE","COMMENTSADMIN_WEB_COMMENTS_TOGGLE&amp;identifier=$identifier","Toggle Web Comments for this page On/Off"),
					Array("CANCEL","COMMENTSADMIN_LIST","Back to List")
				);
			} else {
				$variables["PAGE_BUTTONS"] = Array(
					Array("CANCEL","COMMENTSADMIN_LIST","Back to List")
				);
			
			}
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
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
				if ($type=="ADMIN"){
					$page_discussion=$r["page_admin_discussion"];
				}else{
					$page_discussion=$r["page_web_discussion"];
				}
				$trans_page=$r["page_identifier"];
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["comment_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		 $this->check_parameters($r,"comment_title",$r["comment_date"]),"SUMMARY"),
						Array(LOCALE_DATE,		 $r["comment_date"]),
						Array(LOCALE_COMMENT_ID, $counter,"TITLE")
					)
				);
				if ($has_contact==1){
					$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array("Contact" ,$this->check_parameters($r,"contact_last_name").", ".$this->check_parameters($r,"contact_first_name"));
				}
				if($r["comment_response_to"]!=0){
					$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array(LOCALE_REPLYING_TO ,$r["comment_response_to"],"REPLY_TO");
				}
				$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array(LOCALE_MESSAGE	,"".$r["comment_message"]."","SUMMARY");

				if($page_discussion==1){
					if ($this->discussion_admin_access == 1){
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE","COMMENTSADMIN_REMOVE",REMOVE_EXISTING);
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT","COMMENTSADMIN_EDIT",EDIT_EXISTING);
					}
					if ($r["comment_status"]==1){
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("RESPOND","COMMENTSADMIN_APPROVE&amp;page=$page_identifier",LOCALE_APPROVE);
					} else {
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("RESPOND","COMMENTSADMIN_RESPOND",LOCALE_RESPOND);
					}
				}
			}
			if ($trans_page==0){
				$sql = "select page_data.page_identifier, page_data.page_".strtolower($type)."_discussion as discussion from page_data where page_identifier =$identifier";
				$result = $this->call_command("DB_QUERY",array($sql));
				if ($result){
					while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
						$trans_page = $r["page_identifier"];
						$page_discussion=$r["discussion"];
					}
				}
			}
			if($page_discussion==1){
				$variables["PAGE_BUTTONS"][count($variables["PAGE_BUTTONS"])]=Array("ADD","COMMENTSADMIN_ADD",ADD_NEW,"entry_type=$type&amp;identifier=$identifier");
			}
/*			
			if ((($this->author_access) || ($this->discussion_admin_access == 1)) && ($trans_page>0)){
				if($page_discussion==0){
					$variables["PAGE_BUTTONS"][count($variables["PAGE_BUTTONS"])]=Array("ENABLE","PAGE_DISCUSSION_ENABLE",LOCALE_ENABLE,"entry_type=$type&amp;identifier=$trans_page");
				}else{
					$variables["PAGE_BUTTONS"][count($variables["PAGE_BUTTONS"])]=Array("DISABLE","PAGE_DISCUSSION_DISABLE",LOCALE_DISABLE,"entry_type=$type&amp;identifier=$trans_page");
				}
			}		
			*/
			$this->page_size=$prev ;
			return $this->generate_list($variables);
		}
	}

/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: comment_approve_confirm($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function comment_approve($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier");
		$page 				= $this->check_parameters($parameters,"page");
		$sql = "select page_comments.*, page_trans_data.trans_page, page_trans_data.trans_title from page_comments 
					inner join page_trans_data on
						page_comments.comment_translation = page_trans_data.trans_identifier
					where 
						trans_page=$page and 
						comment_identifier=$identifier and 
						comment_client=$this->client_identifier";
		$result 	= $this->call_command("DB_QUERY",array($sql));
		if ($this->call_command("DB_NUM_ROWS",array($result))>0){
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$comment_translation 	= $r["comment_translation"];
			$comment_type 			= $r["comment_type"];
			$trans_page				= $r["trans_page"];
			$trans_title			= $r["trans_title"];
		}

		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<form name=\"process_form\" label=\"".LOCALE_APPROVE_COMMENT_FORM."\">";
		$out .="		<input type=\"hidden\" name=\"trans_identifier\" value=\"$comment_translation\"/>";
		$out .="		<input type=\"hidden\" name=\"trans_page\" value=\"$trans_page\"/>";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
		$out .="		<input type=\"hidden\" name=\"entry_type\" value=\"$comment_type\"/>";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"COMMENTSADMIN_APPROVE_CONFIRM\"/>";
		$out .="		<text><![CDATA[".LOCALE_APPROVE_COMMENT."]]></text>";
		$out .="		<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"COMMENTSADMIN_VIEW_LIST\"/>";
		$out .="		<input type=\"submit\" iconify=\"YES\" value=\"".LOCALE_NO."\" />";
		$out .="	</form>";
		$out .="</module>";
		$this->call_command("ELERTADMIN_EMAIL", 
			Array(
				"type" => $this->module_constants["__EMAIL_WEB_USER_COMMENTS__"], 
				"identifier" => $identifier
			)
		);

		return $out;
	}
/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: comment_approve_confirm($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function comment_approve_confirm($parameters){
		$identifier 		= $this->check_parameters($parameters,"identifier");
		$trans_identifier	= $this->check_parameters($parameters,"trans_identifier");
		$trans_page			= $this->check_parameters($parameters,"trans_page");
		$entry_type			= $this->check_parameters($parameters,"entry_type","0");
		$sql 				= "update page_comments set comment_status=2 where comment_identifier=$identifier and comment_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		$this->cache_comment(Array("trans_page" => $trans_page, "trans_identifier" => $trans_identifier));

		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<form name=\"process_form\" label=\"".LOCALE_APPROVE_COMMENT_FORM_CONFIRM."\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"COMMENTSADMIN_VIEW_LIST\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$trans_page\" />";
		if ($entry_type == "1"){
			$out .="		<input type=\"hidden\" name=\"entry_type\" value=\"admin\" />";
		} else {
			$out .="		<input type=\"hidden\" name=\"entry_type\" value=\"web\" />";
		}
		$out .="		<text><![CDATA[".LOCALE_APPROVE_COMMENT_CONFIRM."]]></text>";
		$out .="		<input type=\"submit\" iconify=\"CANCEL\" value=\"".LOCALE_BACK."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	
	function comment_list($parameters){
		$sql = "
		select trans_title, trans_page, count(comment_translation) as total, page_admin_discussion from page_trans_data 
					inner join page_data on page_identifier = trans_page 
					inner join page_comments on trans_identifier = comment_translation 
					where comment_client=$this->client_identifier and comment_type='0'
					group by page_trans_data.trans_page, page_trans_data.trans_title";
//		print $sql;
		$out = "";
		$result = $this->call_command("DB_QUERY",Array($sql));
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$prev = $this->page_size;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
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
				
			$variables["PAGE_BUTTONS"] = Array(
			Array("CANCEL","PAGE_LIST","List Pages"),
			);
			
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
				$page_discussion=$r["page_admin_discussion"];
				$trans_page=$r["trans_page"];
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["trans_page"],
					"ENTRY_BUTTONS"	=> Array(Array("LIST","COMMENTSADMIN_VIEW_LIST", ENTRY_COMMENTS)),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		 $this->check_parameters($r,"trans_title",""),"TITLE"),
						Array("count",	 $r["total"])
					)
				);

				
			}
			$this->page_size=$prev ;
			
			return $this->generate_list($variables);
		}
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
		* Table structure for table 'page_comments'
		* 
		* the comments are grouped by the language of the translation and linked to the page so 
		* that the system will display all comments for the english version and not comments for all 
		* languages
		*/
		$fields = array(
			array("comment_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("comment_translation"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("comment_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("comment_message"			,"text"						,"NOT NULL"	,"default ''"),
			array("comment_date"			,"datetime"					,"NOT NULL"	,"default ''"),
			array("comment_user"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("comment_type"			,"unsigned small integer"	,"NOT NULL"	,"default '1'"),
			array("comment_response_to"		,"integer"					,"NOT NULL"	,"default '-1'","key"),
			array("comment_title"			,"varchar(255)"				,"NOT NULL"	,"default '-1'"),
			array("comment_status"			,"unsigned small integer"	,"NOT NULL"	,"default '1'"),
			array("comment_approved_by"		,"unsigned integer"			,"NOT NULL"	,"default '1'")
		);
		
		$primary ="comment_identifier";
		$tables[count($tables)] = array("page_comments", $fields, $primary);
		
		return $tables;
	}

	function retrieve_my_docs($parameters){
		$debug = $this->debugit(false, $parameters);
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$user   = $_SESSION["SESSION_USER_IDENTIFIER"];
		$access_list = "";
		$access_array = array();
		$ALL=0;
		$out ="";
		$open = 0;
		$userRestricted = 0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$max = count($access);
			for($index=0;$index<$max;$index++){
				if ((substr($access[$index],0,strlen($this->module_command))==$this->module_command)||($access[$index]=="ALL")){
					$open=1;
				}
			}
		}
		$session_management_access	= $this->check_parameters($_SESSION, "SESSION_MANAGEMENT_ACCESS", Array());
		$session_man_access="";
		for($index=0,$max=count($session_management_access);$index<$max;$index++){
			$userRestricted=1;
			if ($index>0){
				$session_man_access .= ",";
			}
			$session_man_access .= " ".$session_management_access[$index];
		}
		$join1 ="";
		$join2 ="";
		$where = "";
		if ($userRestricted){
			$join1  = " inner join menu_access_to_page on (page_trans_data.trans_identifier = menu_access_to_page.trans_identifier and page_trans_data.trans_client = menu_access_to_page.client_identifier) ";
			$join1 .= " left outer join relate_user_menu on (relate_user_menu.menu_identifier = menu_access_to_page.menu_identifier and relate_user_menu.user_identifier = page_trans_data.trans_doc_lock_to_user) ";
			$join2  = " inner join menu_access_to_page on (page_trans_data.trans_identifier = menu_access_to_page.trans_identifier and page_trans_data.trans_client = menu_access_to_page.client_identifier) ";
			$join2 .= " left outer join relate_user_menu on (relate_user_menu.menu_identifier = menu_access_to_page.menu_identifier) ";
			$where = " and relate_user_menu.menu_identifier in ($session_man_access)";
			$link_user = $user;
		} else {
			$link_user = -1;
		}
		if ($open==1){
			if (($this->parent->server[LICENCE_TYPE]==ECMS)||($this->parent->server[LICENCE_TYPE]==MECM)){
//					$out="<cmd label=\"".LOCALE_LIST."\">COMMENTSADMIN_LIST</cmd>";
					$sql    = "select trans_title, trans_page, count(comment_translation) as total from page_trans_data 
								inner join page_comments on page_trans_data.trans_identifier = comment_translation 
								$join2
								where comment_client=$this->client_identifier and comment_status=1 and comment_type='0'
								group by page_trans_data.trans_page, page_trans_data.trans_title
								$where
							";
					if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>";
					$result = $this->call_command("DB_QUERY",Array($sql));
					if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
						while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
							$out .= "<text><![CDATA[";
							$out .= "<a href='admin/index.php?command=COMMENTSADMIN_VIEW_LIST&amp;lock=1&amp;identifier=".$r["trans_page"]."'>";
							$out .= $this->split_me($this->split_me($r["trans_title"],"&#39;","'"),"&quot;",'"')." (".$r["total"].")";
							$out .= "</a>";
							$out .= "]]></text>";
						}
					}else{
						$out .= "<text><![CDATA[".LOCALE_SORRY_NO_WEB_COMMENTS."]]></text>";
					}
			return "<module name=\"$this->module_name\" label=\"".MANAGEMENT_COMMENTS."\" display=\"my_workspace\">".$out."</module>";
			}
		}else{
			return "";
		}
	}
	
	function toggle_comment($parameters){
		$identifier = $this->check_parameters($parameters,"identifier");
		$sql = "update page_data set page_web_discussion = not(page_web_discussion) where page_data.page_client = $this->client_identifier and page_data.page_identifier =$identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$sql = "select * from page_data inner join page_trans_data on page_client = trans_client and trans_page = page_identifier and trans_doc_status=4 and trans_published_version = 1 where page_data.page_client = $this->client_identifier and page_data.page_identifier =$identifier";
		$result = $this->call_command("DB_QUERY",Array($sql));
		$trans=-1;
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
				$toggle = $r["page_web_discussion"];
				$trans = $r["trans_identifier"];
				if ($toggle==1){
					$action ="Enabled";
				} else {
					$action ="Disabled";
				}
			}
		}
		$out  = "<module name=\"$this->module_name\" label=\"".MANAGEMENT_COMMENTS."\" display=\"form\">";
		$out .= "<form label='Web Discussion Status Changed'><text><![CDATA[The status of this pages comment discussion has been set to $action]]></text></form></module>";
		$this->call_command("PAGE_CACHE_PAGE", Array("identifier"=>$trans,"redirect"=>0));
		return $out;
	}

/*
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* FN:: commit_comment()
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
* this function is used to save the comment
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
	function commit_comment($parameters){
		$edit_id 		= $this->check_parameters($parameters,"edit_id");
		$trans_identifier	= $this->check_parameters($parameters,"trans_identifier");
		$trans_id			= $this->check_parameters($parameters,"trans_id");
		$page_id			= $this->check_parameters($parameters,"page_id");
		$entry_type			= $this->check_parameters($parameters,"entry_type");
		$user		= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
		$comms_open = strtoupper($this->check_prefs(Array("sp_comments_open","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No")));
		$req_app	= strtoupper($this->check_prefs(Array("sp_comment_require_approval")));
		if (($req_app=="YES") && ( ! $this->approve_comments_access)){
			if ($entry_type=="ADMIN"){
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
			}
		}else{
			if($this->approve_comments_access){
				$comment_status = 2;
				$comment_approved_by=$user;
			} else {
				$comment_status = 1;
				$comment_approved_by=0;
			}
		}
		$sql = "update page_comments set comment_status=$comment_status, comment_approved_by = $comment_approved_by where comment_identifier=$edit_id and comment_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		if ($entry_type=="WEB" && $comment_status==2){
			$this->call_command("COMMENTSADMIN_CACHE",Array("comment_id" => $edit_id));
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .="	<form name=\"process_form\" label=\"".LOCALE_COMMENT_SAVED."\">";
			$out .="		<text><![CDATA[".LOCALE_COMMENT_SAVED_CONFIRM."]]></text>";
			$out .="		<input type=\"submit\" iconify=\"CANCEL\" value=\"".LOCALE_BACK."\" />";
			$out .="	</form>";
			$out .="</module>";
		} else {
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .="	<form name=\"process_form\" label=\"".LOCALE_COMMENT_SAVED."\">";
			$out .="		<text><![CDATA[".LOCALE_COMMENT_SAVED_CONFIRM."]]></text>";
			$out .="		<input type=\"submit\" iconify=\"CANCEL\" value=\"".LOCALE_BACK."\" />";
			$out .="	</form>";
			$out .="</module>";
		}
		return $out;
	}
	
	function mycomment_list($parameters){
		$type 		= strtoupper($this->check_parameters($parameters,"entry_type","web"));
		$identifier = $this->check_parameters($parameters,"identifier","-1");

		/**
		* Produce the list of notes for this page, no options
		*/
		$has_contact = $this->call_command("ENGINE_HAS_MODULE",Array("CONTACT_"));
		if ($has_contact==1){
			$join = "LEFT OUTER join contact_data on comment_user=contact_user";
		}
		$where =" and comment_type='0'";
		$page_identifier = $identifier;
		$sql="select * from page_data	
				inner join page_trans_data on page_data.page_identifier = page_trans_data.trans_page 
				where page_client=$this->client_identifier and page_web_discussion =1 and trans_current_working_version =1 order by page_identifier desc";
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
			$prev = $this->page_size;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
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
				
			$variables["PAGE_BUTTONS"] = Array(
				Array("CANCEL","PAGE_LIST","List Pages")
			);
			
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
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
				if ($type=="ADMIN"){
					$page_discussion=$r["page_admin_discussion"];
				}else{
					$page_discussion=$r["page_web_discussion"];
				}
				$trans_page=$r["page_identifier"];
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["page_identifier"],
					"ENTRY_BUTTONS"	=> Array(Array("LIST","COMMENTSADMIN_VIEW_LIST",LOCALE_VIEW_COMMENTS)),
					"attributes"	=> Array(
						Array(LOCALE_TITLE,		 $this->check_parameters($r,"trans_title"), "TITLE")
					)
				);
			}
			$this->page_size=$prev ;
			return $this->generate_list($variables);
		}
	}
}
?>
