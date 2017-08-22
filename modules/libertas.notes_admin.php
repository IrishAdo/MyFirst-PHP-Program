<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.notes_admin.php
* @date 06 Feb 2004
*/
/**
* This module is the module for displaying any administrative notes for pages.
*/

class notes_admin extends module{
	/**
	*  Class Variables
	*/
	var $admin_menu					= null;
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_PREFS";
	var $module_name				= "notes_admin";
	var $module_name_label			= "Page notes Module (Administration)";
	var $module_admin				= "1";
	var $module_command				= "NOTESADMIN_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_PAGENOTES";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.10 $';
	var $module_creation 			= "12/02/2004";
	var $searched					= 0;

	/**
	*  Management Menu entries
	*/
	
	var $module_admin_options 		= array(
	);
	
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	
	var $module_admin_user_access = array(
		array("NOTESADMIN_ALL","COMPLETE_ACCESS"),
		array("NOTESADMIN_APPROVER","ACCESS_LEVEL_APPROVER")
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
	var $WebObjects				 	= array();
	
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
		if($this->parent->module_type=="view_comments" || $this->parent->module_type=="admin"){
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
//				if ($user_command==$this->module_command."CREATE_TABLE"){
//					return $this->create_table();
//				}
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
					if ($user_command==$this->module_command."REMOVE"){
						return $this->comment_remove($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE_CONFIRM"){
						$this->comment_remove_confirm($parameter_list);
						$type		= $this->check_parameters($parameter_list,"entry_type");
						$identifier	= $this->check_parameters($parameter_list,"trans_identifier");
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=COMMENTS_VIEW&entry_type=$type&identifier=$identifier"));
					}
					if  ($user_command==$this->module_command."LIST"){
						return $this->comment_list($parameter_list);
					}
					if  ($user_command==$this->module_command."VIEW_LIST"){
						return $this->view_notes($parameter_list);
					}
					// single html page returned;
					if  ($user_command==$this->module_command."VIEW"){
						return $this->view_comment($parameter_list);
					}
					if ($user_command==$this->module_command."APPROVE"){
						return $this->comment_approve($parameter_list);
					}
					if ($user_command==$this->module_command."APPROVE_CONFIRM"){
						return $this->comment_approve_confirm($parameter_list);
					}
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
		if ($this->parent->server[LICENCE_TYPE]==ECMS){
			$this->module_admin_options = array(
				array("NOTESADMIN_LIST", "MANAGE_NOTES","")
			);
		}
		$this->page_size = 50;
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
		$out  .= "	<input type='hidden' name='command' ><![CDATA[NOTESADMIN_SAVE_CONFIRMED]]></input>";
		$out  .= "	<text ><![CDATA[".LOCALE_COMMENTS_PREVIEW_MSG."]]></text>";
		$out  .= "	<text label=\"Title\"><![CDATA[<strong>$comment_title</strong>]]></text>";
		$out  .= "	<text label=\"Message\"><![CDATA[$comment_msg]]></text>";
		$out  .= "	<input type='button' command='NOTESADMIN_EDIT&amp;identifier=$edit_id&amp;page_id=$page_id' iconify='CANCEL' value='".LOCALE_CANCEL."'/>";
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
		if ($this->check_parameters($parameters,"command")=="NOTESADMIN_ADD"){
			$page_id 				= $this->check_parameters($parameters,"identifier","-1");
			$sql 					= "select trans_identifier from page_trans_data where trans_page = $page_id and trans_client= $this->client_identifier and trans_current_working_version = 1";
			$result 				= $this->call_command("DB_QUERY",array($sql));
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r 					= $this->call_command("DB_FETCH_ARRAY",array($result));
				$trans_id 			= $r["trans_identifier"];
			}
			$label = LOCALE_ADD_COMMENT_FORM;
		} else if ($this->check_parameters($parameters,"command")=="NOTESADMIN_RESPOND"){
			$reply_id 				= $this->check_parameters($parameters,"identifier","-1");
			$comment_id 			= $this->check_parameters($parameters,"identifier","-1");
			$sql 					= "select * from page_comments inner join page_trans_data on trans_identifier = comment_translation where comment_identifier=$comment_id and comment_client=$this->client_identifier";
			$result					= $this->call_command("DB_QUERY",array($sql));
			$comment_translation 	= -1;
			if ($this->call_command("DB_NUM_ROWS",array($result))>0){
				$r 					= $this->call_command("DB_FETCH_ARRAY",array($result));
				$comment_title 		= "RE: ".$r["comment_title"];
				$trans_id 			= $r["trans_identifier"];
				$page_id 			= $r["trans_page"];
			}
			$label = LOCALE_RESPOND_COMMENT_FORM;
		} else if ($this->check_parameters($parameters,"command")=="NOTESADMIN_EDIT"){
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
			$out .= "	<input type=\"hidden\" name=\"command\"><![CDATA[NOTESADMIN_PREVIEW]]></input>";
			$out .="	<input type=\"hidden\" name=\"entry_type\" value=\"$comment_type\"/>";
			if ($this->check_parameters($parameters,"command")=="NOTESADMIN_ADD"){
				$out .= "	<input type=\"text\" name=\"comment_title\" size=\"60\" label=\"".LOCALE_SUBJECT."\"><![CDATA[$comment_title]]></input>";
			} else {
				$out .="		<input type=\"hidden\" name=\"comment_title\" ><![CDATA[$comment_title]]></input>";
				$out .="		<text label=\"".LOCALE_SUBJECT."\"><![CDATA[$comment_title]]></text>";
			}
			$out .= "	<textarea name=\"page_comment\" label=\"".LOCALE_COMMENT."\" size=\"60\" height=\"10\" type=\"PLAIN-TEXT\"><![CDATA[$comment_message]]></textarea>";
			if ($comment_type!="WEB"){
				$out .="	<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"NOTESADMIN_VIEW_LIST&amp;entry_type=$comment_type\"/>";
			}
			$out .= "	<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
			$out .= "</form>";
			$out .= "</module>";
			return $out;	
		}else{
			$out = $this->call_command("USERS_SHOW_LOGIN",Array("redirect"=>"command=NOTESADMIN_ADD&identifier=$page_id"));
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
			if ($command == "NOTESADMIN_PREVIEW"){
				$trans_id 	= $this->check_parameters($parameters,"trans_id");
				$edit_id 	= $this->check_parameters($parameters,"edit_id");
				$reply_id 	= $this->check_parameters($parameters,"reply_id");
				$page_id 	= $this->check_parameters($parameters,"page_id");
				if ($user==0){
					$user=-1;
				}
				$insert_type 	= 1;
				$now 		= $this->libertasGetDate("Y/m/d H:i:s");
				$msg		= $this->validate($this->tidy($this->txt2html(strip_tags($this->check_parameters($parameters,"page_comment")))));
				$title		= $this->strip_tidy($this->check_parameters($parameters,"comment_title"));
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

				$out = $this->call_command("NOTESADMIN_DISPLAY_PREVIEW",Array("entry_type"=>$entry_type, "trans_id" => $trans_id, "edit_id" => $edit_id, "reply_id" 	=> $reply_id, "page_id" 	=> $page_id));
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
		$sql = "delete from page_comments where comment_identifier=$identifier and comment_type = 1 and comment_client=$this->client_identifier";
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
			$out .="	<input type=\"hidden\" name=\"command\" value=\"PAGE_REMOVE_COMMENT_CONFIRM\"/>";
			$out .="	<text><![CDATA[".LOCALE_REMOVE_COMMENT."]]></text>";
			$out .="	<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"PAGE_LIST\"/>";
			$out .="	<input type=\"submit\" iconify=\"YES\" value=\"".LOCALE_NO."\" />";
			$out .="</form>";
		$out .="</module>";
		return $out;
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
					$content= html_entity_decode($this->check_parameters($r,"comment_message"));
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
		$identifier = $this->check_parameters($parameters,"identifier","-1");

		/**
		* Produce the list of notes for this page, no options
		*/
		$has_contact = $this->call_command("ENGINE_HAS_MODULE",Array("CONTACT_"));
		if ($has_contact==1){
			$join = "LEFT OUTER join contact_data on comment_user=contact_user";
		}
		$where =" and comment_type='1'";
		$page_identifier = $identifier;
		$sql="select * from page_comments 
				inner join page_trans_data on page_comments.comment_translation=page_trans_data.trans_identifier 
				inner join page_data on page_data.page_identifier = page_trans_data.trans_page 
				$join 
				where page_trans_data.trans_page=$identifier and 
				comment_client=$this->client_identifier 
				$where order 
				by comment_identifier desc";
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
			Array("CANCEL","PAGE_LIST","List Pages"),
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
					"identifier"	=> $r["comment_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array(LOCALE_TITLE		, $this->check_parameters($r,"comment_title",$r["comment_date"]),"SUMMARY"),
						Array(LOCALE_DATE		, $r["comment_date"]),
						Array(LOCALE_COMMENT_ID	, $counter,"TITLE")
					)
				);
				if ($has_contact==1){
					$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array("Contact" ,$this->check_parameters($r,"contact_last_name").", ".$this->check_parameters($r,"contact_first_name"));
				}
				if($r["comment_response_to"]!=0){
					$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array(LOCALE_REPLYING_TO ,$r["comment_response_to"],"REPLY_TO");
				}
				$variables["RESULT_ENTRIES"][$index]["attributes"][count($variables["RESULT_ENTRIES"][$index]["attributes"])] = Array(LOCALE_MESSAGE	,"".substr(strip_tags($this->check_parameters($r,"comment_message")),0,255)."","SUMMARY");
				if($page_discussion==1){
					if ($this->discussion_admin_access == 1){
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE","NOTESADMIN_REMOVE",REMOVE_EXISTING);
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT","NOTESADMIN_EDIT",EDIT_EXISTING);
					}
					if ($r["comment_status"]==1){
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("RESPOND","NOTESADMIN_APPROVE&amp;page=$page_identifier",LOCALE_APPROVE);
					} else {
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("RESPOND","NOTESADMIN_RESPOND",LOCALE_RESPOND);
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
				$variables["PAGE_BUTTONS"][count($variables["PAGE_BUTTONS"])]=Array("ADD","NOTESADMIN_ADD",ADD_NEW,"identifier=$identifier");
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
		$sql = "select page_comments.*, page_trans_data.trans_page from page_comments 
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
		}

		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<form name=\"process_form\" label=\"".LOCALE_APPROVE_COMMENT_FORM."\">";
		$out .="		<input type=\"hidden\" name=\"trans_identifier\" value=\"$comment_translation\"/>";
		$out .="		<input type=\"hidden\" name=\"trans_page\" value=\"$trans_page\"/>";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
		$out .="		<input type=\"hidden\" name=\"entry_type\" value=\"$comment_type\"/>";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"NOTESADMIN_APPROVE_CONFIRM\"/>";
		$out .="		<text><![CDATA[".LOCALE_APPROVE_COMMENT."]]></text>";
		$out .="		<input type=\"button\" iconify=\"CANCEL\" value=\"".LOCALE_CANCEL."\" command=\"NOTESADMIN_VIEW_LIST\"/>";
		$out .="		<input type=\"submit\" iconify=\"YES\" value=\"".LOCALE_NO."\" />";
		$out .="	</form>";
		$out .="</module>";
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
		$this->cache_comments(Array("trans_page" => $trans_page, "trans_identifier" => $trans_identifier));

		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="	<form name=\"process_form\" label=\"".LOCALE_APPROVE_COMMENT_FORM_CONFIRM."\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"NOTESADMIN_VIEW_LIST\" />";
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
	
	function comment_list ($parameters){
		$sql = "select trans_title, trans_page, count(comment_translation) as total from page_trans_data 
					inner join page_comments on trans_identifier = comment_translation 
					where comment_client=$this->client_identifier and comment_type='1'
					group by page_trans_data.trans_page, page_trans_data.trans_title";
//		print $sql;
		$type="ADMIN";
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
			$variables["as"]				= "table";
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=";
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
				$trans_page=$r["trans_page"];
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["trans_page"],
					"ENTRY_BUTTONS"	=> Array(Array("LIST","NOTESADMIN_VIEW_LIST", LOCALE_VIEW)),
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
		$comment_status = 2;
		$comment_approved_by=$user;
		$sql = "update page_comments set comment_status=$comment_status, comment_approved_by = $comment_approved_by where comment_identifier=$edit_id and comment_client=$this->client_identifier";
		$this->call_command("DB_QUERY",array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("command=NOTESADMIN_VIEW_LIST&amp;identifier=$page_id"));
	}
}
?>
