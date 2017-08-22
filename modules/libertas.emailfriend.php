<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.emailfriend.php
* @date 06 Feb 2004
*/
/**
* This module is the module for emailing a url to a friend
*/

class emailfriend extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name_label			= "Email URL link to a Friend Module";
	var $module_name				= "emailfriend";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.8 $';
	var $module_command				= "EMAILFRIEND_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "";
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
		array(2,"Email A Friend Link","WEBOBJECTS_SHOW_EMAILAFRIEND",0,0, "will add the Email a friend button and link")
	);
	/**
	*  Channel options
	*/
	var $module_display_options 	= array();
	/**
	* SPECIAL PAGES
	*
	* Each special page will call a specific function as defined here
	*/
	var $specialPages			 	= array(
		array("-email-a-friend.php"					,"EMAILFRIEND_URL"	,"VISIBLE",	""),
		array("-block-email-from-friend.php"		,"EMAILFRIEND_BETF"	,"HIDDEN",	""),
		array("-block-email-from-friend-confirm.php","EMAILFRIEND_BETFC","HIDDEN",	"")
	);
	
	/**
	*  filter options
	*/
	var $display_options			= array();

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
			if ($user_command==$this->module_command."SPECIAL_PAGES"){
				return $this->specialPages;
			}
			/**
			* Email A friend functions 
			*/
			if ($user_command==$this->module_command."URL"){
				return $this->email_to_a_friend($parameter_list);
			}
			if ($user_command==$this->module_command."URL_CONFIRM"){
				return $this->email_to_a_friend_confirm($parameter_list);
			}
			if ($user_command==$this->module_command."BETF"){
				return $this->block_email_from_friend($parameter_list);
			}
			if ($user_command==$this->module_command."BETFC"){
				return $this->block_email_from_friend_confirm($parameter_list);
			}
			/**
			* Build DB Structure
			*/
			if ($this->running_as_installer){
				if ($user_command==$this->module_command."CREATE_TABLE"){
					return $this->create_table();
				}
			}
			
		}else{
			// wrong command sent to system
			return "";
		}
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
		return 1;
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
		* Table structure for table 'page_blocked_emails'
		*/
		
		$fields = array(
			array("pbe_user_access_id"					,"unsigned integer"	,"NULL"	,""),
			array("pbe_uid"								,"varchar(25)"		,"NULL"	,"default ''"),
			array("pbe_client"							,"unsigned integer"	,"NULL"	,""),
			array("pbe_email_address"					,"varchar(255)"		,"NULL"	,"default ''")
		);
		
		$primary ="";
		$tables[count($tables)] = array("page_blocked_emails", $fields, $primary);

		return $tables;
	}
		
	function email_to_a_friend($parameters){
		$your_name		= strip_tags($this->check_parameters($parameters,"your_name"));
		$friends_name	= strip_tags($this->check_parameters($parameters,"friends_name"));
		$your_email		= strip_tags($this->check_parameters($parameters,"your_email"));
		$friends_email	= strip_tags($this->check_parameters($parameters,"friends_email"));
		$msg			= strip_tags($this->check_parameters($parameters,"email_body"));
		if ($this->check_parameters($_SESSION,"EMAIL_TO_A_FRIEND","")==""){
			$_SESSION["EMAIL_TO_A_FRIEND"] = $this->check_parameters($_SERVER,"HTTP_REFERER","__NOT_FOUND__");
		}
		$out= "	<module name='page' display='form'>";
		$out.="		<form name='PAGE_email_to_a_friend' method='post' label='".LOCALE_EMAIL_TO_A_FRIEND."'>";
		$out.="			<input type='hidden' name='command' value='".$this->module_command."URL_CONFIRM'/>";
		$out.="			<input type='text' label='Your name' name='your_name'><![CDATA[$your_name]]></input>";
		if(strlen($your_email)>0){
			$out.="			<text><![CDATA[".LOCALE_SUPPLY_A_VALID_EMAIL."]]></text>";
		}
		$out.="			<input type='text' label='Your Email' name='your_email'><![CDATA[$your_email]]></input>";
		$out.="			<input type='text' label='Friends name' name='friends_name'><![CDATA[$friends_name]]></input>";
		if(strlen($friends_email)>0){
			$out.="			<text><![CDATA[".LOCALE_SUPPLY_A_VALID_EMAIL."]]></text>";
		}
		$out.="			<input type='text' label='Friends Email' name='friends_email'><![CDATA[$friends_email]]></input>";
		$out.="			<textarea label='Message' height='12' size='60' name='email_body'><![CDATA[$msg]]></textarea>";
		$out.="			<input type='submit' value='".LOCALE_SEND_MAIL."' iconify='SAVE'/>";
		$out.="		</form>";
		$out.="	</module>";
		return $out;		
	}
	function email_to_a_friend_confirm($parameters){
		$your_name		= strip_tags($this->check_parameters($parameters,"your_name"));
		$friends_name	= strip_tags($this->check_parameters($parameters,"friends_name"));
		$your_email		= strip_tags($this->check_parameters($parameters,"your_email"));
		$friends_email	= strip_tags($this->check_parameters($parameters,"friends_email"));
		$email_body		= strip_tags($this->check_parameters($parameters,"email_body"));
		if ($this->check_email_address($your_email) && $this->check_email_address($friends_email)){
			$sql = "select * from page_blocked_emails where pbe_uid = '__BLOCKED__' and pbe_email_address ='$friends_email' and pbe_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",Array($sql));
			$url = $_SESSION["EMAIL_TO_A_FRIEND"];
			if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
				$msg = $email_body."
You have received this email from our web site (http://".$this->parent->domain.") as a friend decided that the content on the following page would be of interest to yourself

".$_SESSION["EMAIL_TO_A_FRIEND"]."
----------------------------------------------------------------------
If the link above is broken then you have the following options
1. You can copy the complete link into notepad and remove the returns that were inserted before copying the new link into your web browser

or 

2. You can visit us at http://".$this->parent->domain.$this->parent->base."-search.php and use our online search functionality
----------------------------------------------------------------------
This is an automated email from http://".$this->parent->domain.$this->parent->base.".
Please note due to our privicy policy none of your contact details have been stored by us and you will not receive any further correspondance from either us or any third party company as a conquence of receiving this email
You have received this email from $your_name with and email address of $your_email 

If you do not want to receive any further emails from this automated service please click on the link below.
http://".$this->parent->domain.$this->parent->base."-block-email-from-friend.php?m=$friends_email
----------------------------------------------------------------------
";
$_SESSION["EMAIL_TO_A_FRIEND"]="";
				$this->call_command("EMAIL_QUICK_SEND",
					Array(
						"from" => $your_email,
						"subject" => "Though you would be interested in this",
						"body" => $msg,
						"to" => $friends_email
					)
				);
			}
			$out= "	<module name='page' display='form'>";
			$out.="		<form name='PAGE_email_confirm' label='".LOCALE_SUPPLY_PAGE_SENT_LABEL."'><text><![CDATA[".LOCALE_SUPPLY_PAGE_SENT."<p><a href='$url'></p>To return to the page you were previously on click here</a></p>]]></text></form>";
			$out.="	</module>";
			$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_PAGE_EMAILED__",$parameters));
			return $out;		
		}else {
			return $this->email_to_a_friend($parameters);
		}
	}
	
	function block_email_from_friend($parameters){
	 	$email 				= $this->check_parameters($parameters,"m");
		$this->call_command("USERACCESSLOG_ACCESS",Array("__LOG_PAGE_EMAIL_BLOCKED__",$parameters));
		if ($email!=""){
			$sql = "select * from page_blocked_emails where pbe_uid = '__BLOCKED__' and pbe_email_address ='$email' and pbe_client=$this->client_identifier";
			$result = $this->call_command("DB_QUERY",Array($sql));
			$user_access_identifier 		= $this->check_parameters($_SESSION,"SESSION_USER_ACCESS_IDENTIFIER",$this->check_parameters($_COOKIE,"SESSION_USER_ACCESS_IDENTIFIER"));
//			$user_identifier	= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",);
			$uid	= substr(uniqid("", true),0,25);
			$body	= "Hello,
You have requested to remove the ability for anyone to send you a link via our automated service for sending pages to a friend on our web site http://".$this->parent->domain."".$this->parent->base."

If this is correct then please click on the link below to complete the final process.
http://".$this->parent->domain."".$this->parent->base."-block-email-from-friend-confirm.php?m=$email&id=$uid
";
			$this->call_command("EMAIL_QUICK_SEND",
				Array(
					"from" => $email,
					"subject" => "Request for blocking of your email address",
					"body" => $body,
					"to" => $email
				)
			);
			if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
				$sql = "update page_blocked_emails set pbe_uid = '$uid' where pbe_email_address ='$email' and pbe_client=$this->client_identifier";
			} else {
				$sql = "insert into page_blocked_emails (pbe_user_access_id, pbe_email_address, pbe_uid, pbe_client) values ($user_access_identifier, '$email','$uid',$this->client_identifier)";
			}
			print "<li>$sql</li>";
			$this->call_command("DB_QUERY",Array($sql));
			$out= "	<module name='page' display='form'>";
			$out.="		<form name='page_email_confirm' label='".LOCALE_SUPPLY_PAGE_EMAIL_BLOCKED."'><text><![CDATA[".LOCALE_SUPPLY_PAGE_EMAIL_BLOCKED_CONFIRM."]]></text></form>";
			$out.="	</module>";
			return $out;		
		} else {
			$out= "	<module name='page' display='form'>";
			$out.="		<form name='page_email_confirm' label='".LOCALE_SUPPLY_PAGE_EMAIL_BLOCKED."'><text><![CDATA[".LOCALE_SUPPLY_PAGE_EMAIL_BLOCKED_NO_EMAIL."]]></text></form>";
			$out.="	</module>";
			return $out;		
		}
	}

	function block_email_from_friend_confirm($parameters){
	 	$email 				= $this->check_parameters($parameters,"m");
	 	$uid 				= $this->check_parameters($parameters,"id");
		if (($email!="") && ($uid!="")){
			$sql = "update page_blocked_emails set pbe_uid ='__BLOCKED__' where pbe_email_address ='$email' and pbe_uid ='$uid'";
			$this->call_command("DB_QUERY",Array($sql));
			$out= "	<module name='page' display='form'>";
			$out.="		<form name='PAGE_email_confirm' label='".LOCALE_SUPPLY_PAGE_EMAIL_BLOCKED."'><text><![CDATA[".LOCALE_SUPPLY_PAGE_EMAIL_BLOCKED_FINAL."]]></text></form>";
			$out.="	</module>";
			return $out;		
		}
	}
}
?>
