<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.contact.php
* @date 09 Oct 2002
*/
class contact extends module{
	/**
	*  Class Variables
	*/
	var $module_type		= "__SYSTEM__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name_label	= "Contact Module (System)";
	var $module_name 		= "contact";
	var $module_admin 		= "1";
	var $admin_access 		= 0;
	var $module_debug 		= false;
	var $module_label 		= "MANAGEMENT_CONTACT";
	var $module_modify	 	= '$Date: 2005/02/18 18:43:49 $';
	var $module_version 	= '$Revision: 1.26 $';
	var $module_creation 	= "06/11/2002";
	var $module_command		= "CONTACT_"; 		// all commands specifically for this module will start with this token
	var $display_options	= array(
		array (0, "FILTER_ORDER_SURNAME_A_Z"		,"contact_last_name asc, contact_first_name asc"),
		array (1, "FILTER_ORDER_SURNAME_Z_A"		,"contact_last_name Desc, contact_first_name Desc")
	);
	var $available_forms = Array("LOCALE_CONTACT_US");
	var $module_admin_options		= array();
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
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			if ($user_command==$this->module_command."CLONE"){
				return $this->clone_this_contact($parameter_list);
			}
			if ($this->admin_access==1){
				if ($user_command==$this->module_command."VIEW_USER"){
					return $this->get_user_detail($parameter_list);
				}
			}
			if ($user_command==$this->module_command."GET_DETAILS"){
				return $this->get_details($parameter_list);
			}
			if ($user_command==$this->module_command."GET_COMPANY"){
				return $this->get_company($parameter_list);
			}
			if ($user_command==$this->module_command."SAVE"){
				$next_command = $this->check_parameters($parameter_list,"next_command");
				$next_command_module = $this->check_parameters($parameter_list,"next_command_module", "CONTACT_LIST_SELECTION");
				$next_command_identifier = $this->check_parameters($parameter_list,"next_command_identifier", "-1");
				if($next_command_identifier!=-1){
					$ok = $this->save($parameter_list);
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=$next_command_module&amp;identifier=$next_command_identifier"));
				} else {
					if ($next_command!="CONTACT_LIST_SELECTION"){
						$ok = $this->save($parameter_list);
						return $ok;
					} else{
						$error=0;
						$first_name 		= $this->check_parameters($parameter_list,"contact_first_name");
						if ($first_name==""){
							$error=1;
						}
						$last_name			= $this->check_parameters($parameter_list,"contact_last_name");
						if ($last_name==""){
							$error=1;
						}
						$initials			= $this->check_parameters($parameter_list,"contact_initials");
						$email				= $this->check_parameters($parameter_list,"contact_email");
						$email_confirm		= $this->check_parameters($parameter_list,"contact_confirm_email");
						if (($email!=$email_confirm) ||($email=="")){
							$email="";
							$email_confirm="";
							$error = 1;
						}
						if ($error==0){
							$ok = $this->save($parameter_list);
							$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$next_command_module));
						} else {
							$parameter_list["command"]="USERS_REGISTRATION_SAVE";
							$user_command=$this->module_command."ADD";
						}
					}
				}
			}
			
			if ($user_command==$this->module_command."ADD"){
				$next_command = $this->check_parameters($parameter_list,"next_command");
				$out  = "<module name=\"contact\" display=\"form\">\n";
				$out .=	"<form name=\"contact_form\" label=\"Define the Contact Details\">\n";
				$out .=	"<input type=\"hidden\" name=\"contact_user\" value=\"".$this->check_parameters($parameter_list,"user_identifier","0")."\"/>";
				$out .=	"<input type=\"hidden\" name=\"command\" value=\"CONTACT_SAVE\"/>\n";
				$out .=	"<input type=\"hidden\" name=\"next_command\" value=\"$next_command\"/>\n";
				$out .=	$this->form($parameter_list);
				$out .=	"\n<input type=\"submit\" value=\"".ADD_NEW."\" iconify=\"ADD\"/>\n</form>\n</module>\n";
				return $out;
			}
			if ($user_command==$this->module_command."FORM"){
				return $this->form($parameter_list);
			}
			if ($user_command==$this->module_command."REGISTER_FORM"){
				return $this->register_form($parameter_list);
			}
			
			if ($user_command==$this->module_command."GET_NAME"){
				return $this->get_name($parameter_list);
			}
			if ($user_command==$this->module_command."LIST_SELECTION"){
				return $this->result_list($parameter_list);
			}
			if ($user_command==$this->module_command."LIST_SELECTION_DETAILS"){
				return $this->result_list_details($parameter_list);
			}
			if ($user_command==$this->module_command."GET_METADATA_AUTHOR_DETAILS"){
				return $this->get_metadata_author_details($parameter_list);
			}
			if ($user_command==$this->module_command."GET_DESCRIPTIONS"){
				return $this->get_descriptions($parameter_list);
			}
			if ($user_command==$this->module_command."LIST_FORM_FIELD_ACCESS"){
				return $this->list_available_fields();
			}
			if (($user_command==$this->module_command."CACHE_AVAILABLE_FORM")){
				return $this->cache_available_forms($parameter_list);
			}
			if ($user_command==$this->module_command."LIST_FORMS"){
				return $this->available_forms;
			}
            /*************************************************************************************************************************
            *                        A D V A N C E D   F O R M   B U I L D E R   F O R M   F U N C T I O N S
            *************************************************************************************************************************/
			if ($user_command == $this->module_command."GET_FIELD_LIST"){
				return $this->get_field_list($parameter_list);
			}
			if ($user_command == $this->module_command."GET_FIELD_OPTIONS"){
				return $this->get_field_options($parameter_list);
			}
            if($user_command==$this->module_command."SAVE_FBA"){
                return $this->save_fba($parameter_list);
            }
            if($user_command==$this->module_command."LOAD_FBA"){
                return $this->load_fba($parameter_list);
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
		/**
		* request the client identifier once we use this variable often
		*/
		$this->editor_configurations = Array(
			"LOCALE_PROFILE" => Array(
				"status"=>"unlocked", 
				"locked_to" => "", 
				"identifier"=>0
			),
		);
		$this->client_identifier = $this->parent->client_identifier;
		if ($this->parent->module_type=="web"){
			$this->admin_access = 0;
		}else{
			$this->admin_access = 1;
		}
	}

	function list_available_fields(){
		$data = array();
		
		$data[count($data)] = Array("LOCALE_CLIENT_DETAILS",
			Array(
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_job_title', 'LOCALE_CONTACT_JOB_TITLE', '0', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_company', 'LOCALE_CONTACT_COMPANY', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_first_name', 'LOCALE_CONTACT_FIRST_NAME', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_initials', 'LOCALE_CONTACT_INITIALS', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_last_name', 'LOCALE_CONTACT_SURNAME', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_email', 'LOCALE_CONTACT_EMAIL_ADDRESS', '1', '1');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_telephone', 'LOCALE_CONTACT_PHONE', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_fax', 'LOCALE_CONTACT_FAX', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_address1', 'LOCALE_ADDRESS1', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_address2', 'LOCALE_ADDRESS2', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_address3', 'LOCALE_ADDRESS3', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_city', 'LOCALE_ADDRESS_CITY', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_county', 'LOCALE_ADDRESS_COUNTY', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_country', 'LOCALE_ADDRESS_COUNTRY', '1', '0');\";",
				"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_postcode', 'LOCALE_ADDRESS_POSTCODE', '1', '0');\";"
			)
		);
		$data[count($data)] = Array("LOCALE_CONTACT_US",
			Array(
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_job_title', 'LOCALE_CONTACT_JOB_TITLE', '0', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_company', 'LOCALE_CONTACT_COMPANY', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_web_site', 'LOCALE_CONTACT_WEB_URL', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_first_name', 'LOCALE_CONTACT_FIRST_NAME', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_initials', 'LOCALE_CONTACT_INITIALS', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_last_name', 'LOCALE_CONTACT_SURNAME', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_email', 'LOCALE_CONTACT_EMAIL_ADDRESS', '1', '1');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_telephone', 'LOCALE_CONTACT_PHONE', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_fax', 'LOCALE_CONTACT_FAX', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_address1', 'LOCALE_ADDRESS1', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_address2', 'LOCALE_ADDRESS2', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_address3', 'LOCALE_ADDRESS3', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_city', 'LOCALE_ADDRESS_CITY', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_county', 'LOCALE_ADDRESS_COUNTY', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_country', 'LOCALE_ADDRESS_COUNTRY', '1', '0');\";",
					"\$sql = \"INSERT INTO available_fields (af_client, af_form, af_name, af_locale, af_available, af_required) VALUES (\$client_identifier,\$id,'contact_postcode', 'LOCALE_ADDRESS_POSTCODE', '1', '0');\";"
			)
		);
		
		return $data;
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
	$tables = Array();
		/**
		* Table structure for table 'contact_data'
		*/
		$fields = array(
			array("contact_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("contact_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("contact_user"		,"integer"			,"NOT NULL"	,"default '0'"),
			array("contact_first_name"	,"varchar(20)"		,"NULL"		,"default ''"),
			array("contact_last_name"	,"varchar(20)"		,"NULL"		,"default ''"),
			array("contact_initials"	,"varchar(3)"		,"NULL"		,"default ''"),
			array("contact_job_title"	,"varchar(40)"		,"NULL"		,"default ''"),
			array("contact_telephone"	,"varchar(20)"		,"NULL"		,"default ''"),
			array("contact_fax"			,"varchar(20)"		,"NULL"		,"default ''"),
			array("contact_profile"		,"text"				,"NULL"		,"default ''"),
			array("contact_date_created","datetime"			,"NULL"		,"default ''"),
			array("contact_address"		,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary="contact_identifier";
		$tables[count($tables)] = array("contact_data",$fields,$primary);

		$fields = array(
			array("company_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("company_name"		,"varchar(255)"		,"NULL"		,"default ''"),
			array("company_address"		,"unsigned integer"	,"NOT NULL"	,""),
			array("company_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("company_web_site"	,"varchar(255)"		,"NULL"		,"default ''")
		);
		$primary="company_identifier";
		$tables[count($tables)] = array("contact_company",$fields,$primary);
		
		$fields = array(
			array("address_identifier"	,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("address_1"			,"varchar(255)"		,"NULL"		,"default ''"),
			array("address_2"			,"varchar(255)"		,"NULL"		,"default ''"),
			array("address_3"			,"varchar(255)"		,"NULL"		,"default ''"),
			array("address_city"		,"varchar(50)"		,"NULL"		,"default ''"),
			array("address_county"		,"varchar(50)"		,"NULL"		,"default ''"),
			array("address_country"		,"unsigned integer"	,"NULL"		,"default ''"),
			array("address_postcode"	,"varchar(10)"		,"NULL"		,"default ''"),
			array("address_created",	"datetime"			,"NULL"		,"default ''"),
			array("address_client"		,"unsigned integer"	,"NOT NULL"	,"default '0'")
		);
		$primary = "address_identifier";
		$tables[count($tables)] = array("contact_address",$fields,$primary);

		/**
		* Table structure for table 'domains'
		*/
		return $tables;
	}
	
	function save($parameters){
		$fba				= $this->check_parameters($parameters,"FBA",0);
		$return_results		= $this->check_parameters($parameters,"results","normal");
		$contact_identifier	= $this->check_parameters($parameters,"contact_identifier",-1);
		$company_identifier	= $this->check_parameters($parameters,"company_identifier",-1);
		$address_identifier	= $this->check_parameters($parameters,"address_identifier",-1); // address_identifier
		$user_identifier	= $this->check_parameters($parameters,"contact_user",0);
		$email_identifier	= $this->check_parameters($parameters,"email_identifier",-1);
		$form_restrict		= $this->check_parameters($parameters,"form_restrict","__NOT_FOUND__");
		$name				= $this->check_parameters($parameters,"name","USERS_SHOW_REGISTER");
		$company			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_company"))));
		$contact_web_site	= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_web_site"))));
		$job_title			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_job_title"))));
		$first_name			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_first_name"))));
		$last_name			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_last_name"))));
		$initials 			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_initials"))));
		$email				= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_email"))));
		$telephone			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_telephone"))));
		$fax				= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_fax"))));
		$address1			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_address1"))));
		$address2 			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_address2"))));
		$address3			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_address3"))));
		$city				= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_city"))));
		$county 			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_county"))));
		$country 			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_country",169))));
		$times_through		= $this->check_parameters($parameters,"times_through",0);
		$postcode			= trim($this->strip_tidy($this->check_locale_starter($this->check_parameters($parameters,"contact_postcode"))));
		$rtf				= $this->check_parameters($parameters,"email_rtf",0);
		$email_codex		= $this->check_parameters($parameters,"email_codex");
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		if ($this->parent->module_type=="admin"){
			$profile			= $this->check_editor($parameters, "contact_profile");
		} else {
			$profile			= $this->check_parameters($parameters, "contact_profile");
		}
		$now = $this->libertasGetDate("Y/m/d H:i:s");				
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"save",__LINE__,"[$contact_identifier, $user_identifier, $company, $job_title, $first_name, $last_name, $initials, $email, $fax, $telephone, $address1, $address2, $address3, $city, $county, $country, $postcode]"));
		}
		if($form_restrict=="__NOT_FOUND__"){
			$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>$name));
		} else {
			$form_restriction_list = $this->call_command($form_restrict."FORM_RESTRICTIONS",Array("name"=>$name));
		}
//		print_r($form_restriction_list);
		$error_array= Array(
			"error_email"		=> 0,
			"error_firstname"	=> 0,
			"error_last_name"	=> 0,
			"error_company"		=> 0,
			"error_job_title" 	=> 0,
			"error_telephone" 	=> 0,
			"error_fax" 		=> 0,
			"error_address" 	=> 0,
			"error_city" 		=> 0,
			"error_county" 		=> 0,
			"error_country" 	=> 0,
			"error_postcode" 	=> 0,
			"error_initials"	=> 0,
			"error_web_site" 	=> 0,
			"error_profile"		=> 0
		);
		$total_error_count = 0;
		if($fba==0){
			$mark_required=true;
			$contact_identifier = $this->check_parameters($parameters,"contact_identifier");

			if ($this->check_parameters($form_restriction_list,"contact_first_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				$first_name 		= $this->check_parameters($parameters,"contact_first_name");
				if ($this->check_parameters($form_restriction_list["contact_first_name"],"required","0")!="0"){
					if ($first_name==""){
						$error_array["error_firstname"]=1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_initials","__NOT_FOUND__")!="__NOT_FOUND__"){
				$initials			= $this->check_parameters($parameters,"contact_initials");
				if ($this->check_parameters($form_restriction_list["contact_initials"],"required","0")!="0"){
					if ($initials==""){
						$error_array["error_initials"]=1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_last_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				$last_name			= $this->check_parameters($parameters,"contact_last_name");
				if ($this->check_parameters($form_restriction_list["contact_last_name"],"required","0")!="0"){
					if ($last_name==""){
						$error_array["error_last_name"]=1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_email","__NOT_FOUND__")!="__NOT_FOUND__"){
				$email				= $this->check_parameters($parameters,"contact_email");
				$email_confirm		= $this->check_parameters($parameters,"contact_confirm_email");
//				print "<li>Email [".$this->check_parameters($form_restriction_list["contact_email"],"required","0")."]</li>";
				if ($this->check_parameters($form_restriction_list["contact_email"],"required","0")!="0"){
//				print "<li>[$email]</li>";
					if (($email!=$email_confirm) || ($email=="")){
						$email="";
						$email_confirm="";
						$error_array["error_email"] = 1;
						$total_error_count++;
//						print "<li><font color='red'>Error</font> [$email]</li>";
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_company","__NOT_FOUND__")!="__NOT_FOUND__"){
				$company 			= $this->check_parameters($parameters,"contact_company");
				if ($this->check_parameters($form_restriction_list["contact_company"],"required","0")!="0"){
					if ($company==""){
						$error_array["error_company"] = 1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_web_site","__NOT_FOUND__")!="__NOT_FOUND__"){
				$web_site 			= $this->check_parameters($parameters,"contact_web_site");
				if ($this->check_parameters($form_restriction_list["contact_web_site"],"required","0")!="0"){
					if ($company==""){
						$error_array["error_web_site"] = 1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_job_title","__NOT_FOUND__")!="__NOT_FOUND__"){
				$job_title			= $this->check_parameters($parameters,"contact_job_title");
				if ($this->check_parameters($form_restriction_list["contact_job_title"],"required","0")!="0"){
					if ($job_title==""){
						$error_array["error_job_title"] = 1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_telephone","__NOT_FOUND__")!="__NOT_FOUND__"){
				$telephone			= $this->check_parameters($parameters,"contact_telephone");
				if ($this->check_parameters($form_restriction_list["contact_telephone"],"required","0")!="0"){
					if ($telephone==""){
						$error_array["error_telephone"] = 1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_fax","__NOT_FOUND__")!="__NOT_FOUND__"){
				$fax				= $this->check_parameters($parameters,"contact_fax");
				if ($this->check_parameters($form_restriction_list["contact_fax"],"required","0")!="0"){
					if ($fax==""){
						$error_array["error_fax"] = 1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_address","__NOT_FOUND__")!="__NOT_FOUND__"){
				$address1			= $this->check_parameters($parameters,"contact_address1");
				$address2			= $this->check_parameters($parameters,"contact_address2");
				$address3			= $this->check_parameters($parameters,"contact_address3");
				if ($this->check_parameters($form_restriction_list["contact_address"],"required","0")!="0"){
					if ($address1==""){
						$error_array["error_address"] = 1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_city","__NOT_FOUND__")!="__NOT_FOUND__"){
				$city				= $this->check_parameters($parameters,"contact_city");
				if ($this->check_parameters($form_restriction_list["contact_city"],"required","0")!="0"){
					if ($city==""){
						$error_array["error_city"] = 1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_county","__NOT_FOUND__")!="__NOT_FOUND__"){
				$county				= $this->check_parameters($parameters,"contact_county");
				if ($this->check_parameters($form_restriction_list["contact_county"],"required","0")!="0"){
					if ($county==""){
						$error_array["error_county"] = 1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_country","__NOT_FOUND__")!="__NOT_FOUND__"){
				$country			= $this->check_parameters($parameters,"contact_country");
				if ($this->check_parameters($form_restriction_list["contact_country"],"required","0")!="0"){
					if ($country==""){
						$error_array["error_country"] = 1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_postcode","__NOT_FOUND__")!="__NOT_FOUND__"){
				$postcode			= $this->check_parameters($parameters,"contact_postcode");
				if ($this->check_parameters($form_restriction_list["contact_postcode"],"required","0")!="0"){
					if ($postcode==""){
						$error_array["error_postcode"] = 1;
						$total_error_count++;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_profile","__NOT_FOUND__")!="__NOT_FOUND__"){
				$profile			= $this->check_parameters($parameters,"contact_profile");
				if ($this->check_parameters($form_restriction_list["contact_profile"],"required","0")!="0"){
					if ($profile==""){
						$error_array["error_profile"] = 1;
						$total_error_count++;
					}
				}
			}
		}
		if ($total_error_count>0){
			return Array("id"=>$contact_identifier, "errors" => $error_array, "errorCount" =>$total_error_count);
		} else {
			$address_fields= array(
				"address_identifier"	=> $this->getUid(),
				"address_1"				=> "'$address1'",
				"address_2"				=> "'$address2'",
				"address_3"				=> "'$address3'",
				"address_city"			=> "'$city'",
				"address_county"		=> "'$county'",
				"address_country"		=> "'$country'",
				"address_postcode"		=> "'$postcode'",
				"address_client"		=> "'$this->client_identifier'",
				"address_created"		=> "'$now'"
			);
			$contact_fields = array(
				"contact_identifier"	=> $this->getUid(),
				"contact_user"			=> "'$user_identifier'",
				"contact_job_title"		=> "'$job_title'",
				"contact_first_name"	=> "'$first_name'",
				"contact_last_name"		=> "'$last_name'",
				"contact_initials"		=> "'$initials'",
				"contact_fax"			=> "'$fax'",
				"contact_telephone"		=> "'$telephone'",
				"contact_client"		=> "'$this->client_identifier'",
				"contact_address"		=> "'$address_identifier'",
				"contact_profile"		=> "'$profile'"
			);
			$company_fields= array(
				"company_identifier"	=> $this->getUid(),
				"company_name"			=> "'$company'",
				"company_client"		=> "'$this->client_identifier'",
				"company_address"		=> "'$address_identifier'",
				"company_web_site"		=> "'$contact_web_site'"
			);
			$field_list = "";
			$value_list = "";
			if ($contact_identifier<1 || $company_identifier<1){
				$count=0;
				$field_list = "";
				$value_list = "";
				foreach ($address_fields as $key => $val) {
					if ($count>0){
						$field_list .= ", ";
						$value_list .= ", ";
					}
					$field_list .= $key;
					$value_list .= $val;
					$count++;
				}
				$sql="insert into contact_address ($field_list) values ($value_list)";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				$this->call_command("DB_QUERY",array($sql));
//				$sql = "Select * from contact_address where address_created='$now' and address_client='$this->client_identifier'";
//				$result = $this->call_command("DB_QUERY",array($sql));
//				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
//				$address_identifier = $this->check_parameters($r,"address_identifier",-1);
				$address_identifier = $address_fields["address_identifier"];
				$contact_fields["contact_address"]	= $address_identifier;
				$contact_fields["contact_date_created"]	= "'$now'";
				$company_fields["company_address"]  = $address_identifier;
				$field_list = "";
				$value_list = "";
				$count=0;
				$contact_identifier  = $contact_fields["contact_identifier"];
				foreach ($contact_fields as $key => $val) {
					if ($count>0){
						$field_list .= ", ";
						$value_list .= ", ";
					}
					$field_list .= $key;
					$value_list .= $val;
					$count++;
				}
				$sql="insert into contact_data ($field_list) values ($value_list)";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				$this->call_command("DB_QUERY",array($sql));
				
//				$sql= "select * from contact_data where contact_user =".$contact_fields["contact_user"]." and contact_date_created='$now'";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
//				$result = $this->call_command("DB_QUERY",array($sql));
//				if ($result){
//					$r = $this->call_command("DB_FETCH_ARRAY",array($result));
//					 = $r["contact_identifier"];
					$email_id = $this->call_command("EMAIL_INSERT_ADDRESS",array("email_address" => $email,"email_rtf" => $rtf, "email_codex" => $email_codex, "email_contact" => $contact_identifier));
//				}
				$field_list = "";
				$value_list = "";
				$count=0;
				foreach ($company_fields as $key => $val) {
					if ($count>0){
						$field_list .= ", ";
						$value_list .= ", ";
					}
					$field_list .= $key;
					$value_list .= $val;
					$count++;
				}
				$sql="insert into contact_company ($field_list) values ($value_list)";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				$this->call_command("DB_QUERY",array($sql));
			} else {
				$count=0;
				$field_list="";
				unset($contact_fields["contact_email"]);
				$this->call_command("EMAIL_UPDATE_ADDRESS",array("email_address" => $email, "email_identifier" => $email_identifier));
				unset($contact_fields["contact_identifier"]);
				unset($address_fields["address_identifier"]);
				unset($company_fields["company_identifier"]);
				foreach ($contact_fields as $key => $val) {
					if ($count>0){
						$field_list .= ", ";
					}
					$field_list .= $key."=".$val;
					$count++;
				}
				if ($user_identifier==0){
					$sql="update contact_data set $field_list where contact_identifier=$contact_identifier and contact_client=$this->client_identifier";
				}else{
					$sql="update contact_data set $field_list where contact_user=$user_identifier and contact_identifier=$contact_identifier and contact_client=$this->client_identifier";
				}
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				$this->call_command("DB_QUERY",array($sql));
				$count=0;
				$field_list="";
				foreach ($company_fields as $key => $val) {
					if ($count>0){
						$field_list .= ", ";
					}
					$field_list .= $key."=".$val;
					$count++;
				}
				$sql="update contact_company set $field_list where company_identifier=$company_identifier and company_client=$this->client_identifier";
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				
				$this->call_command("DB_QUERY",array($sql));
				$count=0;
				$field_list="";
				foreach ($address_fields as $key => $val) {
					if ($count>0){
						$field_list .= ", ";
					}
					$field_list .= $key."=".$val;
					$count++;
				}
				$sql="update contact_address set $field_list where address_identifier=$address_identifier and address_client=$this->client_identifier";
				
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
				}
				$this->call_command("DB_QUERY",array($sql));
			}
		}
		if($return_results=="normal"){
			return $contact_identifier;
		} else {
			return Array(
				"id"=>$contact_identifier, 
				"errors" => Array(), 
				"errorCount" =>0
			);
		}
	}
	
	function form($parameters =Array()){
		$contact_times_through = $this->check_parameters($parameters,"contact_times_through",0);
		$times_through 		= $this->check_parameters($parameters,"times_through",0);
		$form_restrict 		= $this->check_parameters($parameters,"form_restrict","__NOT_FOUND__");
		$override_required	= $this->check_parameters($parameters,"override_required",Array());
		$name				= $this->check_parameters($parameters,"name","USERS_SHOW_REGISTER");
		$embed				= $this->check_parameters($parameters,"embed",1);
		$contact_identifier	= $this->check_parameters($parameters,"contact_identifier",0);
		$user_identifier	= $this->check_parameters($parameters,"user_identifier",0);
		$command			= $this->check_parameters($parameters,"command");
		$hide				= $this->check_parameters($parameters,"hide",0);
		$restrict_country	= $this->check_parameters($parameters,"restrict_country",0);
		$errors				= $this->check_parameters($parameters,"errors", Array());
		$mark_required		= false;
		$company_identifier	= $this->check_parameters($parameters,"company_identifier",-1);
		$address_identifier	= $this->check_parameters($parameters,"address_identifier",-1);
		$company		 	= "";
		$web_site			= "";
		$first_name	 		= "";
		$last_name			= "";
		$initials			= "";
		$email_identifier	= "";
		$email				= "";
		$email_confirm		= "";
		$telephone			= "";
		$fax				= "";
		$address1			= "";
		$address2			= "";
		$address3			= "";
		$city				= "";
		$county				= "";
		$country			= "";
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		if ($country==""){
			$ip								= $this->check_parameters($_SERVER,"REMOTE_ADDR");
			$sql = "select * from user_access_ip_lookup
						inner join user_access_countries on TLD = access_country 
						inner join country_lookup on cl_abbr = FIPS104
					 where access_ip ='".$ip."'";
//			 print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
//			 $this->exitprogram();
			$result  = $this->parent->db_pointer->database_query($sql);
			$ip_country = -1;
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$ip_country = $r["cl_identifier"];
            }
            $this->parent->db_pointer->database_free_result($result);
			if($ip_country==-1){
				$country=169; // uk 
			} else {
				$country=$ip_country; // from ip address
			}
		}

		$postcode			= "";
		$job_title			= "";
		$profile 			= "";
		
		$sql="";
		if ($contact_times_through!=0){
			$times_through=1;
		}
		if ($times_through==0){
			if ($contact_identifier>0){
				$sql = "
				select
					* 
				from contact_data 
					left outer join contact_address on contact_data.contact_address = contact_address.address_identifier
					left outer join contact_company on contact_company.company_address = contact_address.address_identifier
				where 
					contact_identifier = $contact_identifier and 
					contact_client=$this->client_identifier";
			}
			if (($contact_identifier==0) && ($user_identifier!=0)){
				$sql = "select * from  contact_data 
					left outer join contact_address on contact_data.contact_address = contact_address.address_identifier
					left outer join contact_company on contact_company.company_address = contact_address.address_identifier
				where contact_user = $user_identifier and contact_client=$this->client_identifier";
			}
//			print "<li>".__FILE__." ".__LINE__."$sql </li>";
			if ($sql!=""){
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if there is contact information then display it
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if($user_result = $this->call_command("DB_QUERY",array($sql))) {
					$r = $this->call_command("DB_FETCH_ARRAY",array($user_result));
					if (!empty($r["contact_identifier"])){
						$contact_identifier = $r["contact_identifier"];
						$company_identifier = $r["company_identifier"];
						$address_identifier = $this->check_parameters($r,"address_identifier");
						
						$company 			= $this->check_parameters($r,"company_name");
						$web_site			= $this->check_parameters($r,"company_web_site");
						$job_title			= $this->check_parameters($r,"contact_job_title");
						$first_name 		= $this->check_parameters($r,"contact_first_name");
						$last_name			= $this->check_parameters($r,"contact_last_name");
						$initials			= $this->check_parameters($r,"contact_initials");
						$telephone			= $this->check_parameters($r,"contact_telephone");
						$fax				= $this->check_parameters($r,"contact_fax");
						$address1			= $this->check_parameters($r,"address_1");
						$address2			= $this->check_parameters($r,"address_2");
						$address3			= $this->check_parameters($r,"address_3");
						$city				= $this->check_parameters($r,"address_city");
						$county				= $this->check_parameters($r,"address_county");
						$country			= $this->check_parameters($r,"address_country");
						$postcode			= $this->check_parameters($r,"address_postcode");
						$profile			= $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$this->check_parameters($r,"contact_profile")));
						$struct 			= $this->call_command("EMAIL_EXTRACT_STRUCTURE",Array("email_contact"=>$contact_identifier));
						$email_identifier	= $this->check_parameters($struct,"identifier",-1);
						$email				= $this->check_parameters($struct,"address");
						$email_confirm		= $this->check_parameters($struct,"address");
					}
				}
			}
		}
		if($form_restrict=="__NOT_FOUND__"){
			$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>$name, "override_required"=>$override_required));
		} else {
			$form_restriction_list = $this->call_command($form_restrict."FORM_RESTRICTIONS",Array("name"=>$name, "override_required"=>$override_required));
		}
		$error_email		= 0;
		$error_firstname	= 0;
		$error_last_name	= 0;
		$error_company 		= 0;
		$error_job_title 	= 0;
		$error_telephone 	= 0;
		$error_fax 			= 0;
		$error_address 		= 0;
		$error_city 		= 0;
		$error_county 		= 0;
		$error_country 		= 0;
		$error_postcode 	= 0;
		$error_initials		= 0;
		$error_web_site 	= 0;
		$error_profile		= 0;

		if (($times_through>0) && ($command!="USERS_SAVE")){
			$mark_required=true;
			$contact_identifier = $this->check_parameters($parameters,"contact_identifier");

			if ($this->check_parameters($form_restriction_list,"contact_first_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				$first_name 		= $this->check_locale_starter($this->check_parameters($parameters,"contact_first_name"));
				if ($this->check_parameters($form_restriction_list["contact_first_name"],"required","0")!="0"){
					if ($first_name==""){
						$error_firstname=1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_initials","__NOT_FOUND__")!="__NOT_FOUND__"){
				$initials			= $this->check_locale_starter($this->check_parameters($parameters,"contact_initials"));
				if ($this->check_parameters($form_restriction_list["contact_initials"],"required","0")!="0"){
					if ($initials==""){
						$error_initials=1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_last_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				$last_name			= $this->check_locale_starter($this->check_parameters($parameters,"contact_last_name"));
				if ($this->check_parameters($form_restriction_list["contact_last_name"],"required","0")!="0"){
					if ($last_name==""){
						$error_last_name=1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_email","__NOT_FOUND__")!="__NOT_FOUND__"){
				$email				= $this->check_locale_starter($this->check_parameters($parameters,"contact_email"));
				$email_confirm		= $this->check_locale_starter($this->check_parameters($parameters,"contact_confirm_email"));
				if ($this->check_parameters($form_restriction_list["contact_email"],"required","0")!="0"){
					if (($email!=$email_confirm) || ($email=="") || $this->check_email_address($email)){
						$email_confirm="";
						$error_email = 1;
					} else {
						$email_confirm = $email;
					}
				} else {
					$email_confirm = $email;
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_company","__NOT_FOUND__")!="__NOT_FOUND__"){
				$company 			= $this->check_locale_starter($this->check_parameters($parameters,"contact_company"));
				if ($this->check_parameters($form_restriction_list["contact_company"],"required","0")!="0"){
					if ($company==""){
						$error_company = 1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_web_site","__NOT_FOUND__")!="__NOT_FOUND__"){
				$web_site 			= $this->check_locale_starter($this->check_parameters($parameters,"contact_web_site"));
				if ($this->check_parameters($form_restriction_list["contact_web_site"],"required","0")!="0"){
					if ($company==""){
						$error_web_site = 1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_job_title","__NOT_FOUND__")!="__NOT_FOUND__"){
				$job_title			= $this->check_locale_starter($this->check_parameters($parameters,"contact_job_title"));
				if ($this->check_parameters($form_restriction_list["contact_job_title"],"required","0")!="0"){
					if ($job_title==""){
						$error_job_title = 1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_telephone","__NOT_FOUND__")!="__NOT_FOUND__"){
				$telephone			= $this->check_locale_starter($this->check_parameters($parameters,"contact_telephone"));
				if ($this->check_parameters($form_restriction_list["contact_telephone"],"required","0")!="0"){
					if ($telephone==""){
						$error_telephone = 1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_fax","__NOT_FOUND__")!="__NOT_FOUND__"){
				$fax				= $this->check_locale_starter($this->check_parameters($parameters,"contact_fax"));
				if ($this->check_parameters($form_restriction_list["contact_fax"],"required","0")!="0"){
					if ($fax==""){
						$error_fax = 1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_address","__NOT_FOUND__")!="__NOT_FOUND__"){
				$address1			= $this->check_locale_starter($this->check_parameters($parameters,"contact_address1"));
				$address2			= $this->check_locale_starter($this->check_parameters($parameters,"contact_address2"));
				$address3			= $this->check_locale_starter($this->check_parameters($parameters,"contact_address3"));
				if ($this->check_parameters($form_restriction_list["contact_address"],"required","0")!="0"){
					if ($address1==""){
						$error_address = 1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_city","__NOT_FOUND__")!="__NOT_FOUND__"){
				$city				= $this->check_locale_starter($this->check_parameters($parameters,"contact_city"));
				if ($this->check_parameters($form_restriction_list["contact_city"],"required","0")!="0"){
					if ($city==""){
						$error_city = 1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_county","__NOT_FOUND__")!="__NOT_FOUND__"){
				$county				= $this->check_locale_starter($this->check_parameters($parameters,"contact_county"));
				if ($this->check_parameters($form_restriction_list["contact_county"],"required","0")!="0"){
					if ($county==""){
						$error_county = 1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_country","__NOT_FOUND__")!="__NOT_FOUND__"){
				$country			= $this->check_locale_starter($this->check_parameters($parameters,"contact_country"));
				if ($this->check_parameters($form_restriction_list["contact_country"],"required","0")!="0"){
					if ($country==""){
						$error_country = 1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_postcode","__NOT_FOUND__")!="__NOT_FOUND__"){
				$postcode			= $this->check_locale_starter($this->check_parameters($parameters,"contact_postcode"));
				if ($this->check_parameters($form_restriction_list["contact_postcode"],"required","0")!="0"){
					if ($postcode==""){
						$error_postcode = 1;
					}
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_profile","__NOT_FOUND__")!="__NOT_FOUND__"){
				$profile			= $this->check_locale_starter($this->check_parameters($parameters,"contact_profile"));
				if ($this->check_parameters($form_restriction_list["contact_profile"],"required","0")!="0"){
					if ($profile==""){
						$error_profile = 1;
					}
				}
			}
			
			$total_error_count = ($error_email + $error_firstname + $error_last_name + $error_company + $error_job_title + $error_telephone + $error_fax + $error_address + $error_city + $error_county + $error_country + $error_postcode + $error_profile);
			if ($hide==1){
				if ($total_error_count==0){
					$total_error_count=-1;
				}
			}
		}else{
			$total_error_count = -1;
		}
		if (($times_through==0) || ($total_error_count>=0)){
			$out ="<input type=\"hidden\" name=\"contact_identifier\" value=\"$contact_identifier\"/>";
			$out .="<input type=\"hidden\" name=\"company_identifier\" value=\"$company_identifier\"/>";
			$out .="<input type=\"hidden\" name=\"address_identifier\" value=\"$address_identifier\"/>";
			$out .="<input type=\"hidden\" name=\"contact_times_through\" value=\"1\"/>";
			if ($this->check_parameters($form_restriction_list,"contact_company","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_company==1 || $this->check_parameters($errors, "error_company", 0) )){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_COMPANY."]]></text>";
				}
				$out .="<input type=\"text\" size=\"255\" label=\"".LOCALE_CONTACT_COMPANY."\" name=\"contact_company\"";
				if ($this->check_parameters($form_restriction_list["contact_company"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$company]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_web_site","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_web_site==1 || $this->check_parameters($errors, "error_web_site", 0))){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_WEB_ADDRESS."]]></text>";
				}
				$out .="<input type=\"text\" size=\"40\" label=\"".LOCALE_CONTACT_WEB_URL."\" name=\"contact_web_site\" ";
				if ($this->check_parameters($form_restriction_list["contact_web_site"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$web_site]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_job_title","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_job_title==1 || $this->check_parameters($errors, "error_job_title", 0))){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_JOB."]]></text>";
				}
				$out .="<input type=\"text\" size=\"40\" label=\"".LOCALE_CONTACT_JOB_TITLE."\" name=\"contact_job_title\" ";
				if ($this->check_parameters($form_restriction_list["contact_job_title"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$job_title]]></input>\n";
			}

			if ($this->check_parameters($form_restriction_list,"contact_first_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_firstname==1 || $this->check_parameters($errors, "error_firstname", 0))){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_FIRSTNAME."]]></text>";
				}
				$out .="<input type=\"text\" size=\"20\" label=\"".LOCALE_CONTACT_FIRST_NAME."\" name=\"contact_first_name\"";
				if ($this->check_parameters($form_restriction_list["contact_first_name"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$first_name]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_initials","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_initials==1 || $this->check_parameters($errors, "error_", 0))){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_INITIALS."]]></text>";
				}
				$out .="<input type=\"text\" size=\"3\" label=\"".LOCALE_CONTACT_INITIALS."\" name=\"contact_initials\" ";
				if ($this->check_parameters($form_restriction_list["contact_initials"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$initials]]></input>\n";

			}
			if ($this->check_parameters($form_restriction_list,"contact_last_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_last_name==1 || $this->check_parameters($errors, "error_last_name", 0))){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_LASTNAME."]]></text>";
				}
				$out .="<input type=\"text\" size=\"20\" label=\"".LOCALE_CONTACT_SURNAME."\" name=\"contact_last_name\"";
				if ($this->check_parameters($form_restriction_list["contact_last_name"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$last_name]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_email","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_email==1 || $this->check_parameters($errors, "error_email", 0))){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_EMAIL."]]></text>";
				}
				$out .="<input type=\"hidden\" name=\"email_identifier\" value=\"$email_identifier\"/>";
				$out .="<input type=\"text\" size=\"255\" label=\"".LOCALE_CONTACT_EMAIL_ADDRESS."\" name=\"contact_email\"";
				if ($this->check_parameters($form_restriction_list["contact_email"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$email]]></input>\n";
				$out .="<input type=\"text\" size=\"255\" label=\"".LOCALE_CONTACT_CONFIRM_EMAIL_ADDRESS."\" name=\"contact_confirm_email\" ";
				if ($this->check_parameters($form_restriction_list["contact_email"],"required","0")!="0"){
					$out .= " required=\"contact_email\"";
				}
				$out .= "><![CDATA[$email_confirm]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_address1","__NOT_FOUND__")!="__NOT_FOUND__"
			|| $this->check_parameters($form_restriction_list,"contact_address2","__NOT_FOUND__")!="__NOT_FOUND__" 
			|| $this->check_parameters($form_restriction_list,"contact_address3","__NOT_FOUND__")!="__NOT_FOUND__" 
			){
				if ($mark_required &&($error_address==1 || $this->check_parameters($errors, "error_address", 0))){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_ADDRESS."]]></text>";
				}
				$out .="<input type=\"text\" size=\"255\" label=\"".LOCALE_ADDRESS1."\" name=\"contact_address1\" value=\"$address1\"";
				if ($this->check_parameters($form_restriction_list["contact_address1"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$address1]]></input>\n";
				$out .="<input type=\"text\" size=\"255\" label=\"".LOCALE_ADDRESS2."\" name=\"contact_address2\"><![CDATA[$address2]]></input>\n";
				$out .="<input type=\"text\" size=\"255\" label=\"".LOCALE_ADDRESS3."\" name=\"contact_address3\"><![CDATA[$address3]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_city","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_city==1 || $this->check_parameters($errors, "error_city", 0))){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_CITY."]]></text>";
				}
				$out .="<input type=\"text\" size=\"50\" label=\"".LOCALE_ADDRESS_CITY."\" name=\"contact_city\"";
				if ($this->check_parameters($form_restriction_list["contact_city"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$city]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_county","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_county==1 || $this->check_parameters($errors, "error_county", 0))){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_COUNTY."]]></text>";
				}
				$out .="<input type=\"text\" size=\"50\" label=\"".LOCALE_ADDRESS_COUNTY."\" name=\"contact_county\"";
				if ($this->check_parameters($form_restriction_list["contact_county"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$county]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_country","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_country==1 || $this->check_parameters($errors, "error_country", 0))){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_COUNTRY."]]></text>";
				}
				/*
				$out .="<input type=\"text\" size=\"50\" label=\"".LOCALE_ADDRESS_COUNTRY."\" name=\"contact_country\"";
				if ($this->check_parameters($form_restriction_list["contact_country"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$country]]></input>\n";
				
				*/
				$data = $this->call_command("LANGUAGE_GET_COUNTRIES",Array("selected"=>$country, "restrict_country" => $restrict_country));
				if(is_array($data)){
					$out .= "<input type='hidden' name='contact_country' value='".$data[1]."'/>";
				} else {
					$out .="<select label=\"".LOCALE_ADDRESS_COUNTRY."\" name=\"contact_country\">";
					$out .= $data;
					$out .= "</select>\n";
				}
			}
			if ($this->check_parameters($form_restriction_list,"contact_postcode","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_postcode==1)){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_POSTCODE."]]></text>";
				}
				$out .="<input type=\"text\" size=\"10\" label=\"".LOCALE_ADDRESS_POSTCODE."\" name=\"contact_postcode\" ";
				if ($this->check_parameters($form_restriction_list["contact_postcode"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$postcode]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_telephone","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_telephone==1)){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_TELEPHONE."]]></text>";
				}
				$out .="<input type=\"text\" size=\"20\" label=\"".LOCALE_CONTACT_PHONE."\" name=\"contact_telephone\" ";
				if ($this->check_parameters($form_restriction_list["contact_telephone"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$telephone]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_fax","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_fax==1)){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_FAX>"]]></text>";
				}
				$out .="<input type=\"text\" size=\"20\" label=\"".LOCALE_CONTACT_FAX."\" name=\"contact_fax\"";
				if ($this->check_parameters($form_restriction_list["contact_fax"],"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$fax]]></input>\n";
			}
			if ($this->check_parameters($form_restriction_list,"contact_profile","__NOT_FOUND__")!="__NOT_FOUND__"){
				if ($mark_required &&($error_profile==1)){
					$out .="<text type=\"error\"><![CDATA[".LOCALE_FORM_SUPPLY_VALID_PROFILE."]]></text>";
				}
				$this_editor = $this->check_parameters($this->editor_configurations,"LOCALE_PROFILE",Array());
				$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
				$locked_to  = $this->check_parameters($this_editor,"locked_to","");
				$out .="<textarea  size=\"40\" height=\"15\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to' label=\"".LOCALE_PROFILE."\" name=\"contact_profile\" ";
				if ($this->check_parameters($this->check_parameters($form_restriction_list,"contact_profile",Array()),"required","0")!="0"){
					$out .= " required=\"YES\"";
				}
				$out .= "><![CDATA[$profile]]></textarea>\n";
			}
		} else {
			$out="";
		}
		if($embed==1){
			return $out;
		} else {
			$ncm = $this->check_parameters($parameters,"ncm");
			$nci = $this->check_parameters($parameters,"nci",-1);
			if($nci!=-1){
				$cancel = $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","SHOP_PROCESS_ORDER&amp;identifier=$nci",LOCALE_CANCEL));
			} else {
				$cancel = $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","SHOP_PROCESS_ORDER",LOCALE_CANCEL));
			}
			return "<module name='contact' display='form'>
						<page_options>
							<header>Modify contact details</header>
							" .$cancel."
						</page_options>
						<form name='contact_update_form' label='Modify form details' method='post'>
							<input type='hidden' name='command' value='CONTACT_SAVE'></input>
							<input type='hidden' name='next_command_module' value='$ncm'></input>
							<input type='hidden' name='next_command_identifier' value='$nci'></input>
							<page_sections>
								<section label='Details'>
									$out
								</section>
							</page_sections>
							<input type='submit' iconify='SAVE' value='".SAVE_DATA."'></input>
						</form>
					</module>";
		}
	}

	function get_name($parameters){
		$format = $this->check_parameters($parameters,"format","indexing");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,join($parameters,", ")));
		}
		$out = LOCALE_ANONYMOUS;
		$cuser = $this->check_parameters($parameters,"contact_user",-1);
		if ($cuser>0){
			$sql = "select * from contact_data where contact_user = ".$cuser;
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				if ($format=='indexing'){
					$out = $this->check_parameters($r,"contact_last_name").", ".$this->check_parameters($r,"contact_first_name");
				}else{
					$out = $this->check_parameters($r,"contact_first_name")." ".$this->check_parameters($r,"contact_last_name");
				}
			}
		}
		return $out;
	}

	function get_metadata_author_details($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,join($parameters,", ")));
		}
		$sql = "select * from contact_data where contact_user = ".$parameters["contact_user"];
		if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$out = "[Not available]";
		while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$out = $r["contact_first_name"]." ".$r["contact_last_name"].", ".$r["contact_job_title"];//.", ".$r["contact_company"];	
		}
		return $out;
	}
	
	function register_form($parameters){
		$parameters["command"] = $this->check_parameters($parameters,"access_command");
		return $this->form($parameters);
	}
	
	function get_user_detail($parameters){
		$plain_text 			= $this->check_parameters($parameters,"plain_text",0);
		$has_module				= $this->check_parameters($parameters,"has_module",1);
		$label				= $this->check_parameters($parameters,"label",LOCALE_CONTACT_DETAILS);
		if ($plain_text==0){
			$user_identifier 	= $this->check_parameters($parameters,"identifier",$this->check_parameters($parameters,"unset_identifier"));
			$uid_identifier 	= $this->check_parameters($parameters,"uid_identifier");
			if ($uid_identifier!=""){
				$sql = "select * from contact_data 
					inner join contact_address on contact_data.contact_address = contact_address.address_identifier
					inner join contact_company on contact_company.company_address = contact_address.address_identifier
					inner join country_lookup on contact_address.address_country = country_lookup.cl_identifier
					where contact_data.contact_identifier = $uid_identifier and contact_data.contact_client=$this->client_identifier";
			}else{
				$sql = "select * from contact_data 
					inner join contact_address on contact_data.contact_address = contact_address.address_identifier
					inner join contact_company on contact_company.company_address = contact_address.address_identifier
					inner join country_lookup on contact_address.address_country = country_lookup.cl_identifier
					where contact_data.contact_user = $user_identifier and contact_data.contact_client=$this->client_identifier";
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- if there is contact information then display it
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$contact_identifier = "";
			$company 			= "";
			$web	 			= "";
			$job_title			= "";
			$first_name 		= "";
			$last_name			= "";
			$initials			= "";
			$email				= "";
			$telephone			= "";
			$fax				= "";
			$address1			= "";
			$address2			= "";
			$address3			= "";
			$city				= "";
			$county				= "";
			$country			= "";
			$postcode			= "";
			
			if($user_result = $this->call_command("DB_QUERY",array($sql))) {
				$r = $this->call_command("DB_FETCH_ARRAY",array($user_result));
				if (!empty($r["contact_identifier"])){
					$contact_identifier = $r["contact_identifier"];
					$company 			= $this->check_parameters($r,"company_name");
					$web 				= $this->check_parameters($r,"company_web_site");
					$job_title			= $this->check_parameters($r,"contact_job_title");
					$first_name 		= $this->check_parameters($r,"contact_first_name");
					$last_name			= $this->check_parameters($r,"contact_last_name");
					$initials			= $this->check_parameters($r,"contact_initials");
					
					$email				= $this->call_command("EMAIL_EXTRACT_ADDRESS",Array("contact_identifier" => $contact_identifier));
					
					$telephone			= $this->check_parameters($r,"contact_telephone");
					$fax				= $this->check_parameters($r,"contact_fax");
					$address1			= $this->check_parameters($r,"address_1");
					$address2			= $this->check_parameters($r,"address_2");
					$address3			= $this->check_parameters($r,"address_3");
					$city				= $this->check_parameters($r,"address_city");
					$county				= $this->check_parameters($r,"address_county");
					$country			= $this->check_parameters($r,"cl_country");
					$postcode			= $this->check_parameters($r,"address_postcode");
				}
			}
			$out  = "";
			if ($has_module==1){
			$out  = "<module name=\"contact\" display=\"table\">";
			}
			$out .=	"<table label=\"".$label."\">";
			$out .= "<row label=\"".LOCALE_CONTACT_COMPANY."\"><![CDATA[$company]]></row>";
			$out .= "<row label=\"".LOCALE_CONTACT_WEB_URL."\"><![CDATA[$web]]></row>";
			$out .= "<row label=\"".LOCALE_CONTACT_JOB_TITLE."\"><![CDATA[$job_title]]></row>";
			$out .= "<row label=\"".LOCALE_CONTACT_FIRST_NAME."\"><![CDATA[$first_name]]></row>";
			$out .= "<row label=\"".LOCALE_CONTACT_INITIALS."\"><![CDATA[$initials]]></row>";
			$out .= "<row label=\"".LOCALE_CONTACT_SURNAME."\"><![CDATA[$last_name]]></row>";
			$out .= "<row label=\"".LOCALE_ADDRESS1."\"><![CDATA[$address1]]></row>";
			if(strlen($address2)>0){
			$out .= "<row label=\"".LOCALE_ADDRESS2."\"><![CDATA[$address2]]></row>";
			}
			if(strlen($address3)>0){
			$out .= "<row label=\"".LOCALE_ADDRESS3."\"><![CDATA[$address3]]></row>";
			}
			$out .= "<row label=\"".LOCALE_ADDRESS_CITY."\"><![CDATA[$city]]></row>";
			$out .= "<row label=\"".LOCALE_ADDRESS_COUNTY."\"><![CDATA[$county]]></row>";
			$out .= "<row label=\"".LOCALE_ADDRESS_COUNTRY."\"><![CDATA[$country]]></row>";
			$out .= "<row label=\"".LOCALE_ADDRESS_POSTCODE."\"><![CDATA[$postcode]]></row>";
			$out .= "<row label=\"".LOCALE_CONTACT_EMAIL_ADDRESS."\"><![CDATA[$email]]></row>";
			$out .= "<row label=\"".LOCALE_CONTACT_PHONE."\"><![CDATA[$telephone]]></row>";
			$out .= "<row label=\"".LOCALE_CONTACT_FAX."\"><![CDATA[$fax]]></row>";
			$out .=	"</table>";
			if ($has_module==1){
				$out .="</module>";
			}
		} else {
			if ($this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER")>0){
				$user_identifier 	= $_SESSION["SESSION_USER_IDENTIFIER"];
				$sql = "select * from  contact_data 
					inner join contact_address on contact_data.contact_address = contact_address.address_identifier
					inner join contact_company on contact_company.company_address = contact_address.address_identifier
					inner join email_addresses on email_addresses.email_contact = contact_data.contact_identifier
					inner join country_lookup on contact_address.address_country = country_lookup.cl_identifier
					where contact_data.contact_user = $user_identifier and contact_data.contact_client=$this->client_identifier";
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- if there is contact information then display it
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$contact_identifier = "";
				$company 			= "";
				$web 				= "";
				$job_title			= "";
				$first_name 		= "";
				$last_name			= "";
				$initials			= "";
				$email				= "";
				$telephone			= "";
				$fax				= "";
				$address1			= "";
				$address2			= "";
				$address3			= "";
				$city				= "";
				$county				= "";
				$country			= "";
				$postcode			= "";
				
				if($user_result = $this->call_command("DB_QUERY",array($sql))) {
					$r = $this->call_command("DB_FETCH_ARRAY",array($user_result));
					if (!empty($r["contact_identifier"])){
						$contact_identifier = $this->check_parameters($r,"contact_identifier");
						$company 			= $this->check_parameters($r,"company_name");
						$web	 			= $this->check_parameters($r,"company_web_site");
						$job_title			= $this->check_parameters($r,"contact_job_title");
						$first_name 		= $this->check_parameters($r,"contact_first_name");
						$last_name			= $this->check_parameters($r,"contact_last_name");
						$initials			= $this->check_parameters($r,"contact_initials");
						$email				= $this->check_parameters($r,"email_address");
						$telephone			= $this->check_parameters($r,"contact_telephone");
						$fax				= $this->check_parameters($r,"contact_fax");
						$address1			= $this->check_parameters($r,"address_1");
						$address2			= $this->check_parameters($r,"address_2");
						$address3			= $this->check_parameters($r,"address_3");
						$city				= $this->check_parameters($r,"address_city");
						$county				= $this->check_parameters($r,"address_county");
						$country			= $this->check_parameters($r,"address_country");
						$postcode			= $this->check_parameters($r,"address_postcode");
					}
				}
			}else{
				$company 				= $this->check_locale_starter($this->check_parameters($parameters,"contact_company"));
				$web	 				= $this->check_locale_starter($this->check_parameters($parameters,"contact_web_site"));
				$job_title				= $this->check_locale_starter($this->check_parameters($parameters,"contact_job_title"));
				$first_name 			= $this->check_locale_starter($this->check_parameters($parameters,"contact_first_name"));
				$last_name				= $this->check_locale_starter($this->check_parameters($parameters,"contact_last_name"));
				$initials				= $this->check_locale_starter($this->check_parameters($parameters,"contact_initials"));
				$email					= $this->check_locale_starter($this->check_parameters($parameters,"contact_email"));
				$telephone				= $this->check_locale_starter($this->check_parameters($parameters,"contact_telephone"));
				$fax					= $this->check_locale_starter($this->check_parameters($parameters,"contact_fax"));
				$address1				= $this->check_locale_starter($this->check_parameters($parameters,"contact_address1"));
				$address2				= $this->check_locale_starter($this->check_parameters($parameters,"contact_address2"));
				$address3				= $this->check_locale_starter($this->check_parameters($parameters,"contact_address3"));
				$city					= $this->check_locale_starter($this->check_parameters($parameters,"contact_city"));
				$county					= $this->check_locale_starter($this->check_parameters($parameters,"contact_county"));
				$country				= $this->check_locale_starter($this->check_parameters($parameters,"contact_country"));
				$postcode				= $this->check_locale_starter($this->check_parameters($parameters,"contact_postcode"));
			}
			$name 					= $this->check_parameters($parameters,"name","LOCALE_CONTACT_US");
			$form_restriction_list	= $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>$name));
			$ok=0;
			$out="";
			if ($this->check_parameters($form_restriction_list,"contact_company","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out = "Company :: $company\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_web_site","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "Web Address :: $web\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_job_title","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "Job_title :: $job_title\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_first_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "First_name :: $first_name\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_initials","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "Initials :: $initials\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_last_name","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "Surname :: $last_name\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_address1","__NOT_FOUND__")!="__NOT_FOUND__"
			|| $this->check_parameters($form_restriction_list,"contact_address2","__NOT_FOUND__")!="__NOT_FOUND__" 
			|| $this->check_parameters($form_restriction_list,"contact_address3","__NOT_FOUND__")!="__NOT_FOUND__" 
			){
				$out .= "Address :: $address1\n";
				if(strlen($address2)!=0){
					$out .= "        :: $address2\n";
				}
				if(strlen($address3)!=0){
				$out .= "        :: $address3\n";
				}
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_city","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "City :: $city\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_county","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "County :: $county\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_country","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "Country :: $country\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_email","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "Email Address :: $email\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_postcode","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "Postcode :: $postcode\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_telephone","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "Phone :: $telephone\n";
				$ok=1;
			}
			if ($this->check_parameters($form_restriction_list,"contact_fax","__NOT_FOUND__")!="__NOT_FOUND__"){
				$out .= "Fax :: $fax\n";
				$ok=1;
			}
			if ($ok==1){
				$out =	"\n\n:: Supplied Contact Details ::\n\n$out";
			}
		}
		return $out;
	}
	
	function result_list($parameters){

		$where = "";
		$join="";
		$order_by="";
		$status =array();
		
		$group_filter 			= $this->check_parameters($parameters,"group_filter",-1);
		$join 					= "";

		$search=0;
		$contact_job_title 	= "";
		$contact_first_name = "";
		$contact_last_name	= "";
		$contact_company	= "";
		$_SESSION["SESSION_USER_LANGUAGE"]="en";
		$page_boolean= "or";
		if (isset($parameters["search_str"])){
			if (strlen($parameters["search_str"])>0){
				$search=1;
				$words = split(" ",$parameters["search_str"]);
				for($index=0,$len=count($words);$index<$len;$index++){
					if ($index>0){
						$contact_job_title 	.= " $page_boolean";
						$contact_first_name .= " $page_boolean";
						$contact_last_name 	.= " $page_boolean";
						$contact_company	.= " $page_boolean";
					}
					$contact_job_title .= " contact_job_title like '%".$words[$index]."%'";
					$contact_company .= " company_name like '%".$words[$index]."%'";
					$contact_first_name .= " contact_first_name like '%".$words[$index]."%'";
					$contact_last_name .= " contact_last_name like '%".$words[$index]."%'";
				}
				$where .= " and ($contact_job_title or $contact_first_name or $contact_last_name or $contact_company)";
				$join .= " ";
			}
		}
		if ($group_filter>0){
			$search=1;
			$where .= " and group_identifier = ".$group_filter."";
			$join .= " inner join group_access_to_page on group_access_to_page.trans_identifier = page_trans_data.trans_identifier ";
		}
		if (empty($parameters["order_filter"])){
			$parameters["order_filter"]=0;
		}
		$order_by .= "order by ".$this->display_options[$parameters["order_filter"]][2];
		$lang_of_choice = "en";
		if (empty($filter_translation)){
			$translation = $lang_of_choice;
		} else {
			$translation = $filter_translation;
		}
		$description="";
		$des = $this->get_descriptions($this->check_parameters($parameters,"associated_list",""));
		$max = count($des);
		for($index=0;$index<$max;$index++){
			$description .="<li>".$des[$index]."</li>";
		}
		$sql = "select 
					*
				from contact_data 
					inner join contact_address on contact_data.contact_address = contact_address.address_identifier
					inner join contact_company on contact_company.company_address = contact_address.address_identifier
					left outer join user_info on user_identifier = contact_user 
				where 
					contact_client=$this->client_identifier 
					$where
				$order_by";
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$variables = Array();
		$parameters["description"]		= $description;
		$variables["FILTER"]			= $this->filter($parameters,"CONTACT_LIST_SELECTION");
		$variables["MENU_LINKS"]		= "?command=PAGE_LIST&page=1&search=1&menu_location=";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		
		if (true){//($this->module_admin_access==1 || $search==1){
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
				$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
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
				
				$counter=0;
				if($this->admin_access==1){
					$variables["PAGE_BUTTONS"] = Array(Array("ADD","CONTACT_ADD&amp;next_command=CONTACT_LIST_SELECTION","ADD_NEW",""));
				}
				
				$variables["NUMBER_OF_ROWS"]	= $number_of_records;
				$variables["START"]				= $goto;
				$variables["FINISH"]			= $finish;
				$variables["CURRENT_PAGE"]		= $page;
				$variables["NUMBER_OF_PAGES"]	= $num_pages;
				
				$start_page=intval($page / $this->page_size);
				$remainder = $page % $this->page_size;
				if ($remainder>0){
					$start_page++;
				}
				
				$variables["START_PAGE"]		= $start_page;
				
				if (($start_page+$this->page_size)>$num_pages){
					$end_page	=	$num_pages;
				}else{
					$end_page	=	$this->page_size;
				}
				$user_identifier 				= $_SESSION["SESSION_USER_IDENTIFIER"];
				$variables["END_PAGE"]			= $end_page;
				$variables["ENTRY_BUTTONS"] 	= Array();
				$variables["CONDITION"]			= Array();
				$variables["RESULT_ENTRIES"] 	= Array();
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
					$counter++;
					$i = count($variables["RESULT_ENTRIES"]);
					$entry = split(" ",$r["contact_date_created"]);
					if ($r["contact_initials"]!=""){
						$full_name = $r["contact_first_name"].", ".$r["contact_initials"].", ".$r["contact_last_name"];
					}else{
						$full_name = $r["contact_first_name"].", ".$r["contact_last_name"];
					}
					
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=> $r["contact_identifier"],
						"attributes"	=> Array(
							Array(LOCALE_CONTACT_NAME, $full_name,"TITLE","NO"),
							Array(LOCALE_CONTACT_JOB_TITLE, $this->check_parameters($r,"contact_job_title"),"SUMMARY","NO"),
							Array(LOCALE_CONTACT_COMPANY, $this->check_parameters($r,"company_name"),"YES","NO"),
							Array(LOCALE_CONTACT_WEB_URL, $this->check_parameters($r,"company_web_site"),"YES",LOCALE_CONTACT_WEB_URL),
							Array(LOCALE_ADDRESS1, $this->check_parameters($r,"address_1")."<br/>".$this->check_parameters($r,"address_2")."<br/>".$this->check_parameters($r,"address_3"),"YES","NO"),
							Array(LOCALE_ADDRESS_CITY, $this->check_parameters($r,"address_city"),"YES","NO"),
							Array(LOCALE_ADDRESS_COUNTY, $this->check_parameters($r,"address_county"),"YES","NO"),
							Array(LOCALE_ADDRESS_COUNTRY, $this->check_parameters($r,"address_country"),"YES","NO"),
							Array(LOCALE_ADDRESS_POSTCODE, $this->check_parameters($r,"address_postcode"),"YES","NO"),
							Array(LOCALE_CONTACT_FAX, $this->check_parameters($r,"address_fax"),"YES","NO"),
							Array(LOCALE_CONTACT_PHONE, $this->check_parameters($r,"address_telephone"),"YES","NO")
						)
					);
					$checkin=false;
				}
			}
		}
		$out = $this->generate_list($variables);
		return $out;
	}

	function filter($parameters,$type){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"user_filter",__LINE__,"[]"));
		}
		$search 		= $this->check_parameters($parameters,"search",0);
		$search_str 	= $this->check_parameters($parameters,"search_str");
		$group_filter 	= $this->check_parameters($parameters,"group_filter",-1);
		$order_filter 	= $this->check_parameters($parameters,"order_filter",-1);
		$associated_list= $this->check_parameters($parameters,"associated_list");
		$return_note	= $this->check_parameters($parameters,"return_note",-1);
		$return_hidden	= $this->check_parameters($parameters,"return_hidden",-1);
		$destination 	= $this->check_parameters($parameters,"destination",-1);
		$description 	= $this->check_parameters($parameters,"description","");
		$save_now		= $this->check_parameters($parameters,"save_now","0");
		$command 		= $this->check_parameters($parameters,"command");
		$search++;
		$group_list 	= $this->call_command("GROUP_RETRIEVE",array(@$parameters["group_filter"]));
		
		$out = "\t\t\t\t<form name=\"associated_form\" label=\"".FILTER_RESULTS."\" method=\"get\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\" value=\"$command\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"save_now\" value=\"$save_now\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"return_hidden\" value=\"".$this->check_parameters($parameters,"return_hidden")."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"return_note\" value=\"".$this->check_parameters($parameters,"return_note")."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"return_command\" value=\"".$this->check_parameters($parameters,"return_command")."\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"associated_list\" value=\"$associated_list\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"destination\" value=\"$destination\"/>\n";

		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"description\"><![CDATA[$description]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" value=\"1\"/>\n";
		$str = join("&#39;",split("\\\'",htmlspecialchars($search_str)));
		$search_str = join("&#34;",split("\\\&quot;",$str));
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"search\" value=\"$search\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"text\" name=\"search_str\" label=\"SEARCH_KEYWORDS\"><![CDATA[$search_str]]></input>\n";
		
		if ($this->admin_access==1){
			$out .= "\t\t\t\t\t<select name=\"order_filter\" label=\"".ENTRY_ORDER_FILTER."\">\n";
			for ($index=0,$max=count($this->display_options);$index<$max;$index++){
				$out .="\t\t\t\t\t\t<option value=\"".$this->display_options[$index][0]."\"";
				if ($order_filter==$this->display_options[$index][0]){
					$out .=" selected=\"true\"";
				}
				$out .=">".$this->display_options[$index][1]."</option>\n";
			}
			$out .= "\t\t\t\t\t</select>\n";
		}
		$out .= "\t\t\t\t\t<input type=\"submit\" iconify=\"SEARCH\" name=\"\" value=\"".FILTER_RESULTS."\"/>\n";

		$out .= "\t\t\t\t</form>";
		return $out;
	}

	function get_descriptions($parameters){
		$mylist = split(",",trim($this->check_parameters($parameters,0)));
		$field = $this->check_parameters($parameters,"field","contact_user");
		$list="";
		for ($i=0,$m=count($mylist);$i<$m;$i++){
			if ((strlen($list)>0) && (strlen($mylist[$i])>0)){
				$list .= ",".$mylist[$i];
			} else {
				$list .= $mylist[$i];
			}
		}
		$out = Array();
		$count=0;
	 	if (strlen($list)>0){
			$sql = "select * from contact_data 
					inner join contact_address on contact_data.contact_address = contact_address.address_identifier
					inner join contact_company on contact_company.company_address = contact_address.address_identifier
					left outer join email_addresses on email_addresses.email_contact = contact_data.contact_identifier
					where $field in (".$list.", -2) and contact_client = $this->client_identifier";
			$c_name="";
			$result = $this->call_command("DB_QUERY",array($sql));
			$fullname="";
			if ($result){
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$c_name="";
					if ($this->check_parameters($r,"contact_initials")!=""){
						$full_name = $this->check_parameters($r,"contact_first_name")." ".$this->check_parameters($r,"contact_initials")." ".$this->check_parameters($r,"contact_last_name");
					}else{
						$full_name = $this->check_parameters($r,"contact_first_name")." ".$this->check_parameters($r,"contact_last_name");
					}
					if ($this->check_parameters($r,"company_name")!=""){
						if (strlen($full_name)>0){
							$c_name = ", ";
						}
						$c_name .= $this->check_parameters($r,"company_name");
					}
//					$email = $this->check_parameters($r,"email_address");
					$email =""; // remove email address from the author details 
					$web = ", ".$this->check_parameters($r,"company_web_site");
					if (strlen($email)>0){
						$email =", $email";
					}
					$out[$count++] =$full_name.$c_name.$email.$web;
				}
			}
		} 
		
		return $out;
	}
	function get_company($parameters){
		$user= $this->check_parameters($parameters,"user_identifier",-1);
		$sql = "
		select company_name from contact_company 
			inner join contact_data on contact_data.contact_address = contact_company.company_address
		where contact_company.company_client=$this->client_identifier and contact_data.contact_client=$this->client_identifier and contact_data.contact_user = $user";
//	print $sql;
	$company = "";
		$result = $this->call_command("DB_QUERY",array($sql));
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$company = $r["company_name"];
			}
		}
		return $company;
	}
	function cache_available_forms($parameters){
		$frms = $this->available_forms;
		$lang="en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		for ($index=0,$max=count($frms);$index<$max;$index++){
			$out = $this->call_command($frms[$index],Array("show_module"=>0));
			$fp = fopen($data_files."/form_".$this->client_identifier."_".$lang."_".$frms[$index].".xml", 'w');
			fwrite($fp, $out);
			fclose($fp);
					$um = umask(0);
					@chmod($data_files."/form_".$this->client_identifier."_".$lang."_".$frms[$index].".xml", LS__FILE_PERMISSION);
					umask($um);
		}
	}

	function result_list_details($parameters){
		$return_note		= $this->check_parameters($parameters,"return_note",-1);
		$return_hidden		= $this->check_parameters($parameters,"return_hidden",-1);
		$list	= $this->check_parameters($parameters,"associated_list");
		$out ="<module name=\"".$this->module_name."\" display=\"data\">";
		$out.="<list><![CDATA[$list]]></list>";
		$out.="<hidden><![CDATA[$return_hidden]]></hidden>";
		$out.="<note><![CDATA[$return_note]]></note><people>";
		$result_list = $this->get_descriptions(Array($list,"field"=>"contact_identifier"));
		for($index=0,$max=count($result_list);$index<$max;$index++){
			$out .="<person><![CDATA[".$result_list[$index]."]]></person>";
		}
		$out .="</people></module>";
		return $out;
	}
	
	function get_details($parameters){
//	print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameters,true)."</p></li>";
		$label				= $this->check_parameters($parameters,"label",LOCALE_CONTACT_DETAILS);
		$required			= $this->check_parameters($parameters,"required",Array());
		$user_identifier 	= $this->check_parameters($parameters,"identifier");
		$form_restrict		= $this->check_parameters($parameters,"form_restrict","__NOT_FOUND__");
		$sql = "select * from  contact_data 
					inner join contact_address on contact_data.contact_address = contact_address.address_identifier
					inner join contact_company on contact_company.company_address = contact_address.address_identifier
					inner join email_addresses on email_addresses.email_contact = contact_data.contact_identifier
					left outer join country_lookup on contact_address.address_country = country_lookup.cl_identifier
					where contact_identifier = $user_identifier and contact_client=$this->client_identifier";
//		print "<"."!-- ".__FILE__."@".__LINE__." \n\n\n $sql ---".">";
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		- if there is contact information then display it
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$contact_identifier = "";
		$company 			= "";
		$web 				= "";
		$job_title			= "";
		$first_name 		= "";
		$last_name			= "";
		$initials			= "";
		$email				= "";
		$telephone			= "";
		$fax				= "";
		$address1			= "";
		$address2			= "";
		$address3			= "";
		$city				= "";
		$county				= "";
		$country			= "";
		$postcode			= "";
		$country_label		= "";
		if($user_result = $this->call_command("DB_QUERY",array($sql))) {
			$r = $this->call_command("DB_FETCH_ARRAY",array($user_result));
			if (!empty($r["contact_identifier"])){
				$contact_identifier = $this->check_parameters($r,"contact_identifier");
				$company 			= $this->check_parameters($r,"company_name");
				$web	 			= $this->check_parameters($r,"company_web_site");
				$job_title			= $this->check_parameters($r,"contact_job_title");
				$first_name 		= $this->check_parameters($r,"contact_first_name");
				$last_name			= $this->check_parameters($r,"contact_last_name");
				$initials			= $this->check_parameters($r,"contact_initials");
				$email				= $this->check_parameters($r,"email_address");
				$telephone			= $this->check_parameters($r,"contact_telephone");
				$fax				= $this->check_parameters($r,"contact_fax");
				$address1			= $this->check_parameters($r,"address_1");
				$address2			= $this->check_parameters($r,"address_2");
				$address3			= $this->check_parameters($r,"address_3");
				$city				= $this->check_parameters($r,"address_city");
				$county				= $this->check_parameters($r,"address_county");
				$country			= $this->check_parameters($r,"address_country");
				$country_label		= $this->check_parameters($r,"cl_country");
				$postcode			= $this->check_parameters($r,"address_postcode");
			}
		}
		if($form_restrict=="__NOT_FOUND__"){
			$form_restriction_list = $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>$this->check_parameters($parameters,"name","LOCALE_CONTACT_US")));
		} else {
			$form_restriction_list = $this->call_command($form_restrict."FORM_RESTRICTIONS",Array("name"=>$this->check_parameters($parameters,"name","LOCALE_CONTACT_US")));
		}
		$ok=0;
		$out="";
		$entry=Array();
		if ($this->check_parameters($form_restriction_list,"contact_company","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_company",$required)){
			$out = "<div class='row'><div class='cell'>Company</div><div class='cell'>$company</div></div>";
			$entry["contact_company"] = $company;
			$ok=1;
		}
		if ($this->check_parameters($form_restriction_list,"contact_web_site","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_web_site",$required)){
			$out .= "<div class='row'><div class='cell'>Web Address</div><div class='cell'>$web</div></div>";
			$entry["contact_web"] = $web;
			$ok=1;
		}
		if ($this->check_parameters($form_restriction_list,"contact_job_title","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_job_title",$required)){
			$out .= "<div class='row'><div class='cell'>Job_title</div><div class='cell'>$job_title</div></div>";
			$ok=1;
			$entry["contact_job"] = $job_title;
		}
		if ($this->check_parameters($form_restriction_list,"contact_first_name","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_name",$required)){
			$out .= "<div class='row'><div class='cell'>First name</div><div class='cell'>$first_name</div></div>";
			$ok=1;
			$entry["contact_name"] = $first_name;
		}
		if ($this->check_parameters($form_restriction_list,"contact_initials","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_initials",$required)){
			$out .= "<div class='row'><div class='cell'>Initials</div><div class='cell'>$initials</div></div>";
			$ok=1;
			$entry["contact_initials"] = $initials;
		}
		if ($this->check_parameters($form_restriction_list,"contact_last_name","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_name",$required)){
			$out .= "<div class='row'><div class='cell'>Surname</div><div class='cell'>$last_name</div></div>";
			$ok=1;
			$entry["contact_name"] .= " ".$last_name;
		}
		if ($this->check_parameters($form_restriction_list,"contact_address1","__NOT_FOUND__")!="__NOT_FOUND__" 
			|| $this->check_parameters($form_restriction_list,"contact_address2","__NOT_FOUND__")!="__NOT_FOUND__" 
			|| $this->check_parameters($form_restriction_list,"contact_address3","__NOT_FOUND__")!="__NOT_FOUND__" 
			|| in_array("contact_address",$required)){
			$out .= "<div class='row' style='vertical-align:top'><div style='vertical-align:top' class='cell'>Address</div><div class='cell'>$address1<br/>";
			if(strlen($address2)>0){
				$out .= "$address2<br/>";
			}
			if(strlen($address3)>0){
				$out .= "$address3";
			}
			$out .= "</div></div>";
			$entry["contact_address"] = $address1.",".$address2.",".$address3;
			$entry["contact_address_array"] = Array($address1,$address2,$address3);
			$ok=1;
		}
		if ($this->check_parameters($form_restriction_list,"contact_city","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_city",$required)){
			$out .= "<div class='row'><div class='cell'>City</div><div class='cell'>$city</div></div>";
			$ok=1;
			$entry["contact_city"] = $city;
		}
		if ($this->check_parameters($form_restriction_list,"contact_county","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_county",$required)){
			$out .= "<div class='row'><div class='cell'>County</div><div class='cell'>$county</div></div>";
			$ok=1;
			$entry["contact_county"] = $county;
		}
		if ($this->check_parameters($form_restriction_list,"contact_country","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_country",$required)){
			$out .= "<div class='row'><div class='cell'>Country</div><div class='cell'>$country_label</div></div>";
			$ok=1;
			$entry["contact_country"] = $country;
			$entry["contact_country_label"] = $country_label;
		}
		if ($this->check_parameters($form_restriction_list,"contact_postcode","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_postcode",$required)){
			$out .= "<div class='row'><div class='cell'>Postcode</div><div class='cell'>$postcode</div></div>";
			$ok=1;
			$entry["contact_postcode"] = $postcode;
		}
		if ($this->check_parameters($form_restriction_list,"contact_email","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_email",$required)){
			$out .= "<div class='row'><div class='cell'>Email Address</div><div class='cell'><a href='mailto:$email'>$email</a></div></div>";
			$ok=1;
			$entry["contact_email"] = $email;
		}
		if ($this->check_parameters($form_restriction_list,"contact_telephone","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_telephone",$required)){
			$out .= "<div class='row'><div class='cell'>Phone</div><div class='cell'>$telephone</div></div>";
			$entry["contact_phone"] = $telephone;
			$ok=1;
		}
		if ($this->check_parameters($form_restriction_list,"contact_fax","__NOT_FOUND__")!="__NOT_FOUND__" || in_array("contact_fax",$required)){
			$out .= "<div class='row'><div class='cell'>Fax</div><div class='cell'>$fax</div></div>";
			$entry["contact_fax"] = $fax;
			$ok=1;
		}
		return Array("text"=>$out, "array"=> $entry);	
	}
	/*************************************************************************************************************************
    * retrieve the list of fields available
    *************************************************************************************************************************/
	function get_field_list($parameters){
		$as = $this->check_parameters($parameters,"as","XML");
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$form_restriction_list	= $this->call_command("SFORM_FORM_RESTRICTIONS",Array("name"=>$this->check_parameters($parameters,"name","LOCALE_CONTACT_US")));
		$out = Array();
		$out[count($out)] = Array("name"=>"contact_identifier",	"label"=>"", 			"type"=>"hidden", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"company_identifier",	"label"=>"", 			"type"=>"hidden", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"address_identifier",	"label"=>"", 			"type"=>"hidden", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"email_identifier",	"label"=>"", 			"type"=>"hidden", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_user",		"label"=>"", 			"type"=>"hidden", 	"map"=>"", "auto"=>"user_identifier", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_company",	"label"=>"Company", 	"type"=>"text",		"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_web_site",	"label"=>"Web Site", 	"type"=>"text",		"map"=>"", "auto"=>"ie_uri", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_job_title",	"label"=>"Job Title", 	"type"=>"text",		"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_first_name",	"label"=>"First name", 	"type"=>"text",		"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_last_name",	"label"=>"Surname", 	"type"=>"text",		"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_initials",	"label"=>"Initials",	"type"=>"text", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_email",		"label"=>"Email", 		"type"=>"email",	"map"=>"", "auto"=>"ie_email", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_telephone",	"label"=>"Telephone", 	"type"=>"text", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_fax",		"label"=>"Fax", 		"type"=>"email",	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_address1",	"label"=>"Address 1", 	"type"=>"text", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_address2",	"label"=>"Address 2", 	"type"=>"text", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_address3",	"label"=>"Address 3", 	"type"=>"text", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_city",		"label"=>"City", 		"type"=>"text", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_county",		"label"=>"County", 		"type"=>"text", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_country",	"label"=>"Country", 	"type"=>"select", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_postcode",	"label"=>"PostCode", 	"type"=>"text", 	"map"=>"", "auto"=>"", "required"=>"no");
		$out[count($out)] = Array("name"=>"contact_profile",	"label"=>"Profile", 	"type"=>"memo", 	"map"=>"", "auto"=>"ie_description", "required"=>"no");
		for($i=0;$i<count($out);$i++){
			if ($this->check_parameters($form_restriction_list,$out[$i]["name"],"__NOT_FOUND__")!="__NOT_FOUND__"){
				$out[$i]["required"]="yes";
			}
		}
		if($as == "Array"){
			return $out;
		} else {
			$outd   = "<module name=\"".$this->module_name."\" display=\"fields\">\n";
			$i = count($out);
			for ($index=0; $index<$i;$index++){
				$outd .= "<field>";	
				foreach($out[$index] as $key => $value){
					$outd .= "<$key><![CDATA[$value]]></$key>";	
				}
				$outd .= "</field>\n";	
			}
			$outd .= "</module>";	
			return $outd;
		}
	}
	/*************************************************************************************************************************
	* get a list of the options for a field
	*************************************************************************************************************************/
	function get_field_options($parameters){
		$as 		= $this->check_parameters($parameters,"as","XML");
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$field		= $this->check_parameters($parameters,"field","");
		$country	= $this->check_parameters($parameters,"selected",-1);
		if($field=="contact_country"){
			$restrict_country=0;
			$out = $this->call_command("LANGUAGE_GET_COUNTRIES",Array("selected"=>$country, "restrict_country" => $restrict_country,"as"=>$as));
		}
		if($as == "Array"){
			return $out;
		} else {
			$outd   = "<module name=\"".$this->module_name."\" display=\"fieldoptions\">";
			for ($index=0; $index<$i;$index++){
				$outd .= "<option>";
				foreach($out[$index] as $key => $value){
					$outd .= "<$key><![CDATA[$value]]></$key>";	
				}
				$outd .= "</option>";	
			}
			$outd .= "</module>";	
			return $outd;
		}
	}
	/*************************************************************************************************************************
    * a gateway function to save an advanced form builder form to this module
    *************************************************************************************************************************/
	function save_fba($parameters){
		$list 		= $this->check_parameters($parameters, "module_identifier");
		$parameters["identifier"] = $this->check_parameters($parameters, "contact_identifier",-1);
		if ($parameters["identifier"]==""){
			$parameters["identifier"]=-1;
		}
		if (!$this->check_parameters($parameters,"user_identifier") == ""){
			if ($this->check_parameters($parameters,"contact_user") == ""){
				$parameters["contact_user"] = $parameters["user_identifier"];
			}
		}
		$parameters["FBA"] = 1;
		return $this->save($parameters);
	}
	/**
    * a gateway function to retrieve an advanced form builder form entry
    */
	function load_fba($parameters){
//		print_r($parameters);
		$mod_fields	= $this->check_parameters($parameters, "mod_fields", Array());
		$field_list = "";
		for($i=0;$i<count($mod_fields);$i++){
			if($mod_fields[$i][5]=="CONTACT_::-1"){
				if($field_list!=""){
					$field_list .=", ";
				}
				$field_list .="'".$mod_fields[$i][0]."'";
			}
		}
		$db_fields=Array(
			"contact_identifier"	=> "contact_identifier",
			"address_identifier"	=> "contact_address",
			"company_identifier"	=> "company_identifier",
			"email_identifier"		=> "email_identifier",
			"contact_user"			=> "contact_user",
			"contact_company"		=> "company_name",
			"contact_web_site"		=> "company_web_site",
			"contact_job_title"		=> "contact_job_title",
			"contact_first_name"	=> "contact_first_name",
			"contact_last_name"		=> "contact_last_name",
			"contact_initials"		=> "contact_initials",
			"contact_email"			=> "email_address",
			"contact_telephone"		=> "contact_telephone",
			"contact_fax"			=> "contact_fax",
			"contact_address1"		=> "address_1",
			"contact_address2"		=> "address_2",
			"contact_address3"		=> "address_3",
			"contact_city"			=> "address_city",
			"contact_county"		=> "address_county",
			"contact_country"		=> "address_country",
			"contact_postcode"		=> "address_postcode",
			"contact_profile"		=> "contact_profile"
		);
		$user		= $this->check_parameters($parameters, "user", -1);
		if($user==-1){
		$sql = "select * from contact_data 
					inner join contact_address on contact_data.contact_address = contact_address.address_identifier and contact_client = address_client
					inner join contact_company on contact_company.company_address = contact_address.address_identifier and company_client = address_client
					inner join email_addresses on email_addresses.email_contact = contact_data.contact_identifier and email_client = contact_client
				where contact_client=$this->client_identifier and contact_user = ".$_SESSION["SESSION_USER_IDENTIFIER"]."";
		} else {
		$sql = "select * from contact_data 
					inner join contact_address on contact_data.contact_address = contact_address.address_identifier and contact_client = address_client
					inner join contact_company on contact_company.company_address = contact_address.address_identifier and company_client = address_client
					inner join email_addresses on email_addresses.email_contact = contact_data.contact_identifier and email_client = contact_client
				where contact_client=$this->client_identifier and contact_user = ".$user."";
		}
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$values = Array();
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			for($i=0;$i<count($mod_fields);$i++){
				if($mod_fields[$i][5]=="CONTACT_::-1"){
//					print "<li>".$mod_fields[$i][0]."</li>";
					$mod_fields[$i]["value"] = $this->check_parameters($r, $db_fields[$mod_fields[$i][0]]);
				}
			}
        }
        $this->call_command("DB_FREE",Array($result));
//		print_r($mod_fields);
		return $mod_fields;
	}
	/*************************************************************************************************************************
    * clone the contact details of a contact
    *************************************************************************************************************************/
	function clone_this_contact($parameters){
		$identifier = $this->check_parameters($parameters,"contact_identifier",-1);
		if($identifier==-1){
			return -1;
		}
		$sql = "select * from contact_data 
					inner join contact_address on contact_address = address_identifier and contact_client=address_client
					inner join contact_company on company_address = address_identifier and company_client=address_client
					inner join email_addresses on email_contact = contact_identifier and email_client=contact_client
			where contact_identifier=$identifier and contact_client=$this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$first_name = $r["contact_first_name"];
        	$user 		= $r["contact_user"];
        	$last_name 	= $r["contact_last_name"];
        	$initials 	= $r["contact_initials"];
        	$job_title 	= $r["contact_job_title"];
			$tel 		= $r["contact_telephone"];
			$fax 		= $r["contact_fax"];
			$address_1	= $r["address_1"];
			$address_2	= $r["address_2"];
			$address_3	= $r["address_3"];
			$city		= $r["address_city"];
			$postcode	= $r["address_postcode"];
			$county		= $r["address_county"];
			$country	= $r["address_country"];
			$email		= $r["email_address"];
			$company	= $r["company_name"];
			$web		= $r["company_web_site"];
        }
        $this->call_command("DB_FREE",Array($result));
		$new_contact_id = $this->getUid();
		$new_add_id 	= $this->getUid();
		$new_email_id 	= $this->getUid();
		$new_company_id	= $this->getUid();
		$sql = "insert into contact_data 
				(contact_identifier, contact_client, contact_first_name, contact_last_name, contact_initials, contact_job_title, contact_telephone, contact_fax, contact_profile, contact_address) values
				($new_contact_id, $this->client_identifier, '$first_name', '$last_name', '$initials', '$job_title', '$tel', '$fax', '', '$new_add_id')";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "insert into contact_address 
				(address_identifier, address_client, address_1, address_2, address_3, address_city, address_county, address_country, address_postcode) values
				($new_add_id, $this->client_identifier, '$address_1', '$address_2', '$address_3', '$city', '$county', '$country', '$postcode')";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "insert into email_addresses 
				(email_identifier, email_client, email_address, email_rtf, email_date, email_codex, email_verified, email_contact) values
				($new_email_id, $this->client_identifier, '$email', 0, '".Date("Y-m-d H:i:s")."', '', 0, '$new_contact_id')";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "insert into contact_company 
				(company_identifier, company_client, company_name, company_address, company_web_site) values
				($new_company_id, $this->client_identifier, '$company', '$new_add_id', '$web')";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$this->call_command("DB_QUERY",Array($sql));
//		$this->exitprogram();
		return $new_contact_id;
	}
}
?>