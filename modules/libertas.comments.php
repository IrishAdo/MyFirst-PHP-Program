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

class comments extends module{
	/**
	*  Class Variables
	*/
	var $module_name_label			= "Comments Module (Presentation)";
	var $module_name				= "comments";
	var $module_command				= "COMMENTS_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.8 $';
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
		array(2,"Page Comments Link","WEBOBJECTS_SHOW_PAGECOMMENTS",0,0,"will display the icon and link text for add comments")
	);
	
	/**
	*  filter options
	*/
	var $display_options			= array();

	var $admin_access				= 0;
	/*
	var $author_access				= 0;
	var $approver_access			= 0;
	var $publisher_access			= 0;
	var $list_access				= 0;
	var $archiver_access			= 0;
	var $force_unlock_access		= 0;
	*/
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
			* COMMENT Specific Functions
			*/
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
					("COMMENTSADMIN_ALL"==$access[$index]) ||
					("COMMENTSADMIN_APPROVER"==$access[$index])
				){
					$this->approve_comments_access =1;
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
		$out .= "	<input type=\"hidden\" name=\"trans_id\"><![CDATA[$trans_id]]></input>";
		$out .= "	<input type=\"hidden\" name=\"reply_id\"><![CDATA[$reply_id]]></input>";
		$out .= "	<input type=\"hidden\" name=\"page_id\"><![CDATA[$page_id]]></input>";
		$out  .= "	<input type='hidden' name='edit_id' ><![CDATA[$edit_id]]></input>";
		$out  .= "	<input type='hidden' name='command' ><![CDATA[COMMENTS_SAVE_CONFIRMED]]></input>";
		$out  .= "	<input type='hidden' name='entry_type' ><![CDATA[$entry_type]]></input>";
		$out  .= "	<text ><![CDATA[".LOCALE_COMMENTS_PREVIEW_MSG."]]></text>";
		$out  .= "	<text label=\"Title\"><![CDATA[<strong>$comment_title</strong>]]></text>";
		$out  .= "	<text label=\"Message\"><![CDATA[$comment_msg]]></text>";
		$out  .= "	<input type='button' command='COMMENTS_EDIT&amp;entry_type=$entry_type&amp;identifier=$edit_id&amp;page_id=$page_id' iconify='CANCEL' value='".LOCALE_CANCEL."'/>";
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
		$id							= $this->check_parameters($parameters,"identifier");
		if ($this->check_parameters($parameters,"command")=="COMMENTS_ADD"){
			$page_id 				= $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"unset_identifier","-1"));
			$sql 					= "select trans_identifier from page_trans_data where trans_page = $page_id and trans_client= $this->client_identifier and trans_published_version =1";
			$result 				= $this->call_command("DB_QUERY",array($sql));
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r 					= $this->call_command("DB_FETCH_ARRAY",array($result));
				$trans_id 			= $r["trans_identifier"];
			}
			$label = LOCALE_ADD_COMMENT_FORM;
		} else if ($this->check_parameters($parameters,"command")=="COMMENTS_RESPOND"){
			$reply_id 				= $this->check_parameters($parameters,"reply_to","-1");
			$page_id 				= $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"unset_identifier","-1"));
			$sql 					= "select * from page_comments inner join page_trans_data on trans_identifier = comment_translation where comment_identifier=$reply_id and comment_client=$this->client_identifier and trans_page=$page_id";
			$result					= $this->call_command("DB_QUERY",array($sql));
			$comment_translation 	= -1;
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r 					= $this->call_command("DB_FETCH_ARRAY",array($result));
				$comment_title 		= "RE: ".$r["comment_title"];
				$trans_id 			= $r["trans_identifier"];
			}
			$label = LOCALE_RESPOND_COMMENT_FORM;
		} else if ($this->check_parameters($parameters,"command")=="COMMENTS_EDIT"){
			$edit_id			 	= $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"unset_identifier","-1"));
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
			$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[COMMENTS_PREVIEW]]></input>";
			$out .="	<input type=\"hidden\" name=\"entry_type\" value=\"$comment_type\"/>";
			if ($this->check_parameters($parameters,"command")=="COMMENTS_ADD"){
				$out .= "	<input type=\"text\" name=\"comment_title\" size=\"60\" label=\"".LOCALE_SUBJECT."\"><![CDATA[$comment_title]]></input>";
			} else {
				$out .="		<input type=\"hidden\" name=\"comment_title\" ><![CDATA[$comment_title]]></input>";
				$out .="		<text label=\"".LOCALE_SUBJECT."\"><![CDATA[$comment_title]]></text>";
			}
			$out .= "	<textarea name=\"page_comment\" label=\"".LOCALE_COMMENT."\" size=\"60\" height=\"10\" type=\"PLAIN-TEXT\"><![CDATA[$comment_message]]></textarea>";
			$out .= "	<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
			$out .= "</form>";
			$out .= "</module>";
			return $out;	
		}else{
			$out = $this->call_command("USERS_SHOW_LOGIN",Array("redirect"=>"command=COMMENTS_ADD&identifier=$page_id"));
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
		$sp_comments_open = strtoupper($this->check_prefs(Array("sp_comments_open","default"=>"No","module"=>"SYSPREFS_", "options"=>"Yes:No")));
		$user		= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
		$command	= $this->check_parameters($parameters,"command");
		$entry_type	= $this->check_parameters($parameters,"entry_type","WEB");
		if($sp_comments_open=="YES" || $user>0){
			if ($command == "COMMENTS_PREVIEW"){
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

				$out = $this->call_command("COMMENTS_DISPLAY_PREVIEW",Array("entry_type"=>$entry_type, "trans_id" => $trans_id, "edit_id" => $edit_id, "reply_id" 	=> $reply_id, "page_id" 	=> $page_id));
			}
			if ($command == "COMMENTS_SAVE"){
				
			}
		}else{
			$out="";
		}
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
//			$out .="		<input type=\"hidden\" name=\"command\" value=\"\" />";
			$out .="		<text><![CDATA[".LOCALE_COMMENT_SAVED_CONFIRM."]]></text>";
			$out .="		<input type=\"submit\" iconify=\"CANCEL\" value=\"".LOCALE_BACK."\" />";
			$out .="	</form>";
			$out .="</module>";
			$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_WEB_USER_COMMENTS__"], "identifier" => $edit_id, "url"=> $this->parent->script));
		} else {
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .="	<form name=\"process_form\" label=\"".LOCALE_COMMENT_SAVED."\">";
/*
			$out .="		<input type=\"hidden\" name=\"command\" value=\"\" />";
			$out .="		<input type=\"hidden\" name=\"entry_type\" value=\"$entry_type\" />";
			$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$page_id\" />";
*/
			$out .="		<text><![CDATA[".LOCALE_COMMENT_SAVED_CONFIRM."]]></text>";
			$out .="		<input type=\"submit\" iconify=\"CANCEL\" value=\"".LOCALE_BACK."\" />";
			$out .="	</form>";
			$out .="</module>";
			$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_COMMENTS_APPROVER__"], "identifier" => $edit_id, "url"=> "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=COMMENTSADMIN_VIEW_LIST&amp;lock=1&amp;identifier=$page_id"));
		}
		return $out;
	}

}
?>
