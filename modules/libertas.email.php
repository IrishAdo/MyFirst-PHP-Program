<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.email.php
*/
/**
* A module for sending an email via the system
*/
class email extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__SYSTEM__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name				= "email";		// name of module is used in configuration
	var $module_name_label			= "Email Module (DUAL)";	// label describing the module 
	var $module_grouping			= "";									// what group does this module belong to
	var $module_label				= "MANAGEMENT_EMAIL";					// label describing the module 
	var $module_creation			= "04/03/2003";							// date module was created
	var $module_modified_date		= '$Date: 2005/02/20 17:10:55 $';
	var $module_modify	 			= '$Date: 2005/02/20 17:10:55 $';
	var $module_version 			= '$Revision: 1.13 $';									// Actual version of this module
	var $module_admin				= "0";									// does this system have an administrative section
	var $module_command				= "EMAIL_";								// what does this commad start with ie TEMP_ (use caps)
	var $module_display_options		= array();								// what output channels does this module have
	var $module_admin_options 		= array();								// what options are available in the admin menu
	var $module_admin_user_access 	= array();								// specify types of access for groups

	var $email_from					="";
	var $email_reply_to				="";
	var $email_format				="PLAIN";
	var $email_to					="";
	var $email_body					="";
	var $email_subject				="";
	var $email_cc_list				=Array();
	var $email_attachments			=Array();
	var $headers					="";
	var $XMailer 					="";
	var $email_bcc					="1";
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
		if (strpos($user_command,$this->module_command)===0){
			/**
			* basic commands
			*/
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
			/**
			* needed for administrative access
			*/
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/**
			* specific functions for this module
			*/
			if ($user_command==$this->module_command."SEND"){
				return $this->email_send();
			}
			if ($user_command==$this->module_command."NEW"){
				return $this->intiialise();
			}
			if ($user_command==$this->module_command."TEST"){
				return $this->test();
			}
			if ($user_command==$this->module_command."QUICK_SEND"){
				return $this->quick_send($parameter_list);
			}
			if ($user_command==$this->module_command."BULK_SEND"){
				return $this->bulk_send($parameter_list);
			}
			if ($user_command==$this->module_command."INSERT_ADDRESS"){
				return $this->insert_new_email($parameter_list);
			}
			if ($user_command==$this->module_command."UPDATE_ADDRESS"){
				return $this->update_address($parameter_list);
			}
			if ($user_command==$this->module_command."EXTRACT_ADDRESS"){
				return $this->extract_address($parameter_list);
			}
			if ($user_command==$this->module_command."EXTRACT_STRUCTURE"){
				return $this->extract_structure($parameter_list);
			}
			if ($user_command==$this->module_command."EXTRACT_IDENTIFIER"){
				return $this->retrieve_id($parameter_list);
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	/**
	* call the initialisation function only when this module is created
	*/
	
	function initialise(){
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* define some access functionality
		*/
		$this->email_from  		= $this->check_prefs(Array("sp_from_email"));
		$this->email_reply_to 	= $this->email_from;
		$this->email_format		= "PLAIN";
		$this->email_to			= "";
		$this->email_body		= "";
		$this->email_subject	= "";
		$this->email_cc_list	= Array();
		$this->email_attachments= Array();
		$this->headers			= "";
//		$this->$email_bcc		= "1";		
//		die();
		$this->XMailer 			= "Libertas-Solutions Email Manager - version ".$this->get_module_version()." - http://www.libertas-solutions.com";

	}
	
	/**
	* send a defined email
	*
	* takes no parameters requires details to be set by other functions
	*/
	function email_send(){

		$sent=false;
		
		if ($this->email_to == "")
			$this->email_to = "info@libertas-solutions.com";
//			$this->email_to = "imranmirza@nxvt.com";

		if ($this->email_to!=""){
			$this->headers  = "MIME-Version: 1.0\r\n";
			if ($this->email_format=="HTML"){
				$this->headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
			}else{
				$this->headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
			}
			$this->headers .= "Return-path: ".$this->filterSpam($this->email_reply_to)." \r\n";
			$this->headers .= "From: ".$this->filterSpam($this->email_from)." \r\n";
			$this->headers .= "Reply-To: ".$this->filterSpam($this->email_reply_to)." \r\n";
			$this->headers .= "X-Priority: 3\r\n";
			$this->headers .= "X-Mailer: $this->XMailer\r\n";

			if (($this->parent->domain!="caplo") && ($this->parent->domain!="professor") && ($this->parent->domain!="newdawn") && ($this->email_bcc==1)){
				$this->headers .= "Bcc: webform@libertas-solutions.com\r\n";
//				$this->headers .= "Bcc: imranmirza@nxb.com.pk\r\n";
			}
			if ($this->email_format=="HTML"){
				$body=$this->email_body;
			}else{
				$body=strip_tags($this->email_body);
			}
			if (count($this->email_attachments)>0){

				/* For attachment Added and comment by Muhammad Imran */
				/*
				$fileatt = $attachment; // Path to the file                  
				$fileatt_type = "application/octet-stream"; // File Type 
				$start=	strrpos($attachment, '/') == -1 ? strrpos($attachment, '//') : strrpos($attachment, '/')+1;
				$fileatt_name = substr($attachment, $start, strlen($attachment)); // Filename that will be used for the file as the 	attachment 
				*/
/*
				$semi_rand = md5(time()); 
				$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 

				$headers = "From: imranmirza82@hotmail.com";
				$headers .= "\nMIME-Version: 1.0\n" . 
					"Content-Type: multipart/mixed;\n" . 
					" boundary=\"{$mime_boundary}\""; 

				$msg_txt="\n\nMail created using free code from 4word systems : http://4wordsystems.com";

				$email_message .= "This is a multi-part message in MIME format.\n\n" . 
							"--{$mime_boundary}\n" . 
							"Content-Type:text/html; charset=\"iso-8859-1\"\n" . 
						   "Content-Transfer-Encoding: 7bit\n\n" . 
				$email_txt . "\n\n"; 
*/				
				//$this->headers = 'From: imranmirza@nxvt.com \r\n';
				// boundary="==Multipart_Boundary_xc713b1f0dd0867d73e76f11a68836a19x"';
			
				$attachments = $this->attachment($body);
				//print_r($attachments);
//				echo '<br>Body:<br>'.$attachments[0];
//				echo '<br>Header:<br>'.$this->headers.$attachments[1];
//				echo '<br><br>sd';
//				print_r($this->headers);
//				die();
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- Email the content of the email depending on the requested email format
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$sent = @mail(
					$this->email_to,
					$this->email_subject,
					$attachments[0],
					$this->headers.$attachments[1]
				);
				/* For attachment Added and comment by Muhammad Imran */
				if (count($this->email_cc_list)>0){
					foreach($this->email_cc_list as $index => $val){
						$this->email_to ="$val";
						$sent = @mail(
							$this->email_to,
							$this->email_subject,
							$attachments[0],
							$this->headers.$attachments[1]
						);
					}
				}
			} else {
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,$this->email_to));}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,$this->email_subject));}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,$body));}
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,$this->headers));}
				$sent = @mail(
					$this->email_to,
					$this->email_subject,
					$body,
					$this->headers
				);
				if (count($this->email_cc_list)>0){
					foreach($this->email_cc_list as $index => $val){
						$this->email_to ="$val";
						$sent = @mail(
							$this->email_to,
							$this->email_subject,
							$body,
							$this->headers
						);
					}
				}
			}
		}
		return $sent;
	}	
	
	function set_email_body($body){
		$this->email_body = $body;
	}
	
	function set_email_destination($email_address){
		$this->email_to = $email_address;
	}
    /*
    * set the email format
    *
    * @param String the format of the email ("DEFAULT", "HTML")
    */
	function set_email_format($email_format){
		$this->email_format = $email_format;
	}

    /*
    * set the email from details also sets the replay to details
    *
    * @param String email address to say this is from
    */
	function set_email_from($email_from){
		$this->email_from = $email_from;
		$this->email_reply_to = $this->email_from;
	}

    /*
    * set the email from details also sets the replay to details
    *
    * from          : String email address
    * to            : String email address
    * format        : String default "PLAIN"
    * subject       : String the subject of the email
    * attachements  : Array list of attachments
    * body          : String content of email message
    * cc            : Array of email addresses
    * @param Array block of parameters for sending a single quick email
    */
	function quick_send($parameters){
		$this->initialise();
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,print_r($parameters,true)));}
		$val = $this->check_parameters($parameters,"from","");
		if ($val != ""){
			$this->email_from 	= $val;
		}
		$this->email_reply_to	= $this->check_parameters($parameters,"to",$this->email_from);
		$this->email_format 	= $this->filterSpam($this->check_parameters($parameters,"format","PLAIN"));
		$this->email_subject	= $this->filterSpam($this->check_parameters($parameters,"subject"));
		$this->email_body		= html_entity_decode(html_entity_decode(html_entity_decode(str_replace(Array("&amp;amp;#39;","&amp;amp;#163;","[[gbp]]","[[eur]]","[[usd]]"),Array("'","£","£","€","\$"),$this->check_parameters($parameters,"body")))));
		$this->email_attachments= $this->check_parameters($parameters,"attachments",Array());
		$this->email_to			= $this->check_parameters($parameters,"to",$this->email_from);
		$this->email_cc_list	= $this->check_parameters($parameters,"cc",Array());
		return $this->email_send();
	}
	function bulk_send($parameters){
		$val =  $this->filterSpam($this->check_parameters($parameters,"from",""));
		$this->initialise();
		if ($val != ""){
			$this->email_from 	= $val;
		} else {
			$this->email_from 	= $this->check_prefs(Array("sp_from_email"));
		}
		$_SESSION["PARAMETERS"] 		= $parameters;
		$_SESSION["PARAMETER_INDEX"]	= $this->check_parameters($_SESSION,"PARAMETER_INDEX",0);
		$this->email_format 	=  $this->filterSpam($this->check_parameters($parameters,"format","PLAIN"));
		$this->email_subject	=  $this->filterSpam($this->check_parameters($parameters,"subject"));
		$original				=  $this->check_parameters($parameters,"body");
		$this->email_attachments= $this->check_parameters($parameters,"attachments",Array());
		$this->email_cc_list	= Array();
		//$this->email_bcc		= $this->check_parameters($parameters,"to",$this->email_from);
		$elist 					= $this->check_parameters($parameters,"EMAIL_LIST",Array());
		$max					= count($elist);
		$bcc_counter = $max;
		if ($max>0){
			for($index = $_SESSION["PARAMETER_INDEX"]; $index < $max; $index++){
//		print_r($this->email_attachments);
//				$this->email_attachments= Array();
//				$this->email_subject	= "ado@bloodmoongames.com";//str_replace(Array("[[contact_first_name]]"),Array($elist[$index]["NAME"]),$original);
//				$this->email_from		= "ado@bloodmoongames.com";//str_replace(Array("[[contact_first_name]]"),Array($elist[$index]["NAME"]),$original);
				$this->email_body		= html_entity_decode(html_entity_decode(html_entity_decode(str_replace(Array("[[contact_first_name]]","[[contact_email]]","[[title]]","[[url]]","&amp;amp;#39;","&amp;amp;#163;"),Array($this->check_parameters($elist[$index],"NAME"),$this->check_parameters($elist[$index],"EMAIL"),$this->check_parameters($elist[$index],"TITLE"), $this->check_parameters($elist[$index],"URL"),"'","£"),$original))));
				$this->email_to			= $elist[$index]["EMAIL"];
				$this->email_reply_to	= $this->email_from;
				$this->email_bcc		= $bcc_counter;
//				print "<li>sending to ".$elist[$index]["NAME"]." At ".$this->email_to." from ".$this->email_from." in ".$this->email_format." with a subject of ".$this->email_subject."\n\n$this->email_body</li>";
				/*
				if ($this->parent->db_pointer->database == 'system_cruise_new'){
					if ($bcc_counter == $max - 2)
						die();
				}
				*/
				
				$succeed = $this->email_send();
//				echo $this->email_body;
//				if(!$succeed){
//					print "<font color='red'>failed to send</font>";
//				} else {
//					print "<font color='green'>sent</font>";
//				}
//				$this->print_report();
				$bcc_counter--;
			}
		}
//		$this->exitprogram();
	}
	
	function print_report(){
		print "[" . __FILE__ . ", " . __FUNCTION__ . ", " . __LINE__ . "]";
//		print $this->email_body;
		print "[from:" . $this->email_from."]";
		print "[to:" . $this->email_to."]";
		print "[subject:" . $this->email_subject."]";
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
		* Table structure for table 'email_addresses' a list of people that have subscribed
		* to a particular mailing list
		*/
		$fields = array(
			array("email_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"	),
			array("email_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"		),
			array("email_address"		,"varchar(255)"		,"NOT NULL"	,"default ''"		),
			array("email_rtf"			,"small integer"	,"NOT NULL"	,"default '0'"		),
			array("email_codex"			,"varchar(6)"		,"NOT NULL"	,"default ''"		),
			array("email_date"			,"datetime"			,"NOT NULL"	,"default ''"		),
			array("email_verified"		,"small integer"	,"NOT NULL"	,"default '0'"		),
			array("email_contact"		,"unsigned integer"	,"NOT NULL"	,"default '0'"		)
		);
		$primary = "email_identifier";
		$tables[count($tables)] = array("email_addresses",$fields,$primary);

		return $tables;
	}

	function insert_new_email($parameters){
		$now			= $this->libertasGetDate("Y/m/d H:i:s");
		$codex			= $this->check_parameters($parameters,"email_codex",$this->generate_random_text(6));
		$rtf 			= $this->check_parameters($parameters,"email_rtf",0);
		$email 			= $this->check_parameters($parameters,"email_address");
		$verified		= $this->check_parameters($parameters,"verified",0);
		$email_contact 	= $this->check_parameters($parameters,"email_contact",0);
		$email_client 	= $this->check_parameters($parameters,"email_client" ,$this->client_identifier);
		$sql 			="insert into email_addresses (email_client, email_address, email_rtf, email_codex, email_verified, email_date, email_contact) values ($email_client, '$email', $rtf, '$codex', $verified, '$now', '$email_contact')";
		$result 		= $this->call_command("DB_QUERY",array($sql));
		$sql 			="select * from email_addresses where email_client=$email_client and email_address = '$email' and email_codex='$codex'";
		$result 		= $this->call_command("DB_QUERY",array($sql));
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$email_identifier = $r["email_identifier"];
		}
		return $email_identifier;
	}
	function update_address($parameters){
		$email 		= $this->check_parameters($parameters,"email_address",'');
		$email_id	= $this->check_parameters($parameters,"email_identifier",'');
		$sql 	="update email_addresses set email_address='$email' where email_client = $this->client_identifier and email_identifier='$email_id'";
		$result = $this->call_command("DB_QUERY",array($sql));
	}
	function extract_address($parameters){
		$email 		= $this->check_parameters($parameters,"contact_identifier",0);
		$sql 		= "select * from email_addresses where email_client=$this->client_identifier and email_contact = $email";
		$result 	= $this->call_command("DB_QUERY",array($sql));
		$email_address = "";
		while ($r 	= $this->call_command("DB_FETCH_ARRAY",array($result))){
			$email_address = $this->check_parameters($r,"email_address");
		}
		return $email_address;
	}
	function extract_structure($parameters){
		$email 		= $this->check_parameters($parameters,"email_identifier",0);
		$contact	= $this->check_parameters($parameters,"email_contact",0);
		if ($email!=0){
			$sql 	= "select * from email_addresses where email_client=$this->client_identifier and email_identifier = $email";
		} else{
			$sql 	= "select * from email_addresses where email_client=$this->client_identifier and email_contact = $contact";
		}
		$result 	= $this->call_command("DB_QUERY",array($sql));
		$struct=array();
		while ($r 	= $this->call_command("DB_FETCH_ARRAY",array($result))){
			$struct["identifier"]	= $r["email_identifier"];
			$struct["address"]		= $r["email_address"];
			$struct["rtf"]			= $r["email_rtf"];
			$struct["codex"]		= $r["email_codex"];
			$struct["date"]			= $r["email_date"];
			$struct["verified"]		= $r["email_verified"];
		}
		return $struct;
	}
	
	function retrieve_id($parameters){
		$email 		= $this->check_parameters($parameters,"email");
		$sql 	= "select * from email_addresses where email_client=$this->client_identifier and email_address = '$email'";
		$result = $this->call_command("DB_QUERY",array($sql));
		$email_identifier = -1;
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$email_identifier = $r["email_identifier"];
		}
		return $email_identifier;
	}
	
	function read_attachment($filename){
		$buf = '';
		if (file_exists($filename)){
		$fd = fopen($filename, 'r');
		if ($fd){
			while(!feof($fd)){
				$buf .= fread($fd, 256);
			}
			fclose($fd);
		}
		if (strlen($buf))
			return $buf;
		} else {
			return "";
		}
	}
	
	function attachment($body_txt){
		$boundary = $this->new_boundary();
		$buf = "";
		
		$buf .= '--' . $boundary. "\n";
		$buf .= "Content-ID:".$this->attachment_id()."\n";
		$buf .= "Content-Type: text/html;\ncharset=\"iso-8859-1\"";
		$buf .= "Content-Transfer-Encoding: quoted-printable\n\n";
		$buf .= $body_txt;
//		print "<p>Attachments [";
		for($x=0;$x < count($this->email_attachments) ; $x++){
//			print $this->email_attachments[$x]["actual_filename"].", ";
//			print $this->email_attachments[$x]["original_filename"].", ";
//			print $this->email_attachments[$x]["file_size"].", ";
//			print $this->email_attachments[$x]["mime_type"]."<br>";
			$myheaders = Array();
			$myheaders['Content-ID'] = $this->attachment_id();
			$myheaders['Content-Transfer-Encoding'] = 'BASE64';
			if (strlen($this->email_attachments[$x]["original_filename"])){
				$myheaders['Content-Type'] = $this->email_attachments[$x]["mime_type"].'; name="'.$this->email_attachments[$x]["original_filename"].'"';
				$myheaders['Content-Description'] = '';
				$myheaders['Content-Disposition'] = 'attachment; filename="'.$this->email_attachments[$x]["original_filename"].'"';
			}  else {
				$myheaders['Content-Type'] = $this->email_attachments[$x]["mime_type"];
			}
			$fileatt_name = $this->email_attachments[$x]["original_filename"];

			$f = $this->read_attachment( $this->email_attachments[$x]["actual_filename"] );
//			$data = chunk_split(base64_encode($f),60,"\n");
			$data = chunk_split(base64_encode($f)); 
/*
			$this->headers .= '--' . $boundary. "\n";
			$this->headers .= "\n";
			$this->headers .= $data;
*/
//			print "]</p>";
			$buf .= '--' . $boundary. "\n";
		    while(list($key, $val) = each($myheaders)){
				$buf .= $key.': '.$val."\n";
			}
   			$buf .= "\n";
	    	$buf .= $data;
		}
		$buf .= '--' . $boundary . '--' ;
		/* For attachment Added and comment by Muhammad Imran */

//		echo 'buuf:';
//		print $buf;
//		echo '<br><br>email_msg:';
/*
echo '--data:<br><br>'.$data;
echo '<br><br>--data:';
die();
*/		
		$semi_rand = md5(time()); 
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x"; 
		$email_message .= "This is a multi-part message in MIME format.\n\n" . 
					"--{$mime_boundary}\n" . 
					"Content-Type:text/html; charset=\"iso-8859-1\"\n" . 
				   "Content-Transfer-Encoding: 7bit\n\n" . 
		$body_txt . "\n\n"; 

	$fileatt_type = "application/octet-stream"; // File Type 
	$email_message .= "--{$mime_boundary}\n" . 
                  "Content-Type: {$fileatt_type};\n" . 
                  " name=\"{$fileatt_name}\"\n" . 
                  //"Content-Disposition: attachment;\n" . 
                  //" filename=\"{$fileatt_name}\"\n" . 
                  "Content-Transfer-Encoding: base64\n\n" . 
                 $data . "\n\n" . 
                  "--{$mime_boundary}--\n"; 
//		echo $email_message;
//		die();
		return array(
			0 => $email_message,
			1 =>'MIME-Version: 1.0'."\n".'Content-Type: MULTIPART/MIXED;'."\n".'  BOUNDARY="'.$mime_boundary.'"',
			2 => array('MIME-Version: 1.0', 'Content-Type: MULTIPART/MIXED;'."\n".'  BOUNDARY="'.$boundary.'"','X-Generated-By: '.$this->XMailer)
		);
		/* For attachment Added and comment by Muhammad Imran */
	}

    /**
    * create a new boundary attachment identifier
    *
    * @access private
    * @return String boundary identifier
    */
	function attachment_id(){
		return '<'.'lib_multipart-'.str_replace(' ','.',microtime()).'@'.$this->parent->domain.'>';
	}

    /**
    * create a new boundary for attachments
    *
    * @access private
    * @return String boundary identifier
    */
	function new_boundary(){
		return '-'.'lib_multipart-'.str_replace(' ','.',microtime());
	}
	/**
    * test the send email function on this server
    *
    * sends a plain email to debug@libertas-solutions.com
    */
	function test(){
		$parameters["from"]		= "debug@libertas-solutions.com";
		$parameters["to"]		= "debug@libertas-solutions.com";
		$parameters["format"]	= "PLAIN";
		$parameters["subject"] 	= "this is the subject";
		$parameters["body"]		= "this is the body";
		$sent = $this->quick_send($parameters);
		if ($sent){
		print "<font color=green><strong>sent</strong></font>";
		} else {
		print "<font color=red><strong>not sent</strong></font>";
		}
	}
	function filterSpam($str){
		if(strpos($str,'bcc') || strpos($str,'charset') || strpos($str,'content-type') ){
	  		die('');exit;
		}
		return str_replace("\n","",$str);	
	 }

}
?>