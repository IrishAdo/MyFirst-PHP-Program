<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.(ELERT) - Email Alerts - 
* @date 03 Dec 2002
*/

class elert extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_grouping			= "";
	var $module_name_label			= "Elert Manager Module (Presentation)";
	var $module_name				= "elert";
	var $module_admin				= "0";
	var $module_channels			= "";
	var $searched					= 0;
	var $module_modify	 			= '$Date: 2005/02/15 15:15:39 $';
	var $module_version 			= '$Revision: 1.6 $';
	var $module_command				= "ELERT_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "MANAGEMENT_ELERTS";
	
	
	/**
	*  Management Reports that are available
	*/
	var $module_reports				= Array(
									  );
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
	*  filter options
	*/
	var $display_options			= array();
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
	*  access permissions
	*/
	var $admin_access				= 0;
	var $signup_access				= 0;
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
			if ($this->check_parameters($_SESSION,"SESSION_LOGGED_IN")==1){
				if ($user_command==$this->module_command."DISPLAY"){
					return $this->display();
				}
				if ($user_command==$this->module_command."SIGNUP"){
					return $this->signup($parameter_list);
				}
				if ($user_command==$this->module_command."SIGNUP_SAVE"){
					return $this->signup_save($parameter_list);
				}
				if ($user_command==$this->module_command."SAVE"){
					return $this->save($parameter_list);
				}
			}
		}else{
			// wrong command sent to system
			return "";
		}
	}
	/*************************************************************************************************************************
	* Initialise function
	* This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	* module::initialise() function allowing you to define any extra constructor
	* functionality.
	*************************************************************************************************************************/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise()",__LINE__,""));
		}
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* load the elert locale
		*/
		$this->load_locale("elert");
		return 1;
	}
	
	function display(){
		$uid= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1);
		if ($uid!=-1){
			$sql = "select * from elert_sections left outer join elert_signup on esu_client= es_client and esu_section=es_identifier and (esu_user is null or esu_user = $uid) where es_client = $this->client_identifier order by es_label";
//			print $sql;
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$opt = "";
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$opt .= "<option value='".$r["es_identifier"]."'";
				if ($this->check_parameters($r,"esu_user",-99)==$uid){
					$opt .= " selected='true'";
				}
				$opt .= "><![CDATA[".$r["es_label"]."]]></option>";
	        }
			$sql = "select * from elert_signup_urls where esurl_client = $this->client_identifier and esurl_user=$uid order by esurl_label";
//			print $sql;
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$opt1 = "";
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$opt1 .= "<option value='".$r["esurl_identifier"]."'";
				$opt1 .= " selected='true'";
				$opt1 .= "><![CDATA[".$r["esurl_label"]."]]></option>";
	        }
			
	        $this->call_command("DB_FREE",Array($result));
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .= "<form name=\"elert_manager\" method=\"post\" width='200px' label='".LOCALE_ELERT_SIGNUP_LABEL."'>";
			$out .= "<input type='hidden' name='command' value='ELERT_SAVE'/>";
	//		$out .= "<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
			$out .= "<checkboxes type='vertical' label='".LOCALE_ELERT_CHOOSE_SECTION."' name='es_signup_list'>$opt</checkboxes>";
			if ($opt1==""){
				$out .= "<text><![CDATA[".LOCALE_ELERT_NO_WATCHES."]]></text>";
			} else {
				$out .= "<checkboxes type='vertical' label='".LOCALE_ELERT_CHOOSEN_URLS."' name='es_signup_urls'>$opt1</checkboxes>";
			}
			$out .= "<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
			$out .= "</form>";
			$out .="</module>";
		} else {
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .= "<form label='".LOCALE_ELERT_SIGNUP_LABEL."' name='elert' method='post'>";
			$out .= "<text><![CDATA[".LOCALE_ELERT_SORRY_LOGIN_REQUIRED."]]></text>";
			$out .= "</form>";
			$out .="</module>";
		}
		return $out;
	}

	function save($parameters){
		$uid			= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1);
		$es_signup_list = $this->check_parameters($parameters,"es_signup_list",Array());
		$es_signup_urls = join($this->check_parameters($parameters,"es_signup_urls",Array()),", ");
		if ($uid!=-1){
			$m = count($es_signup_list);
			$sql = "delete from  elert_signup where esu_client=$this->client_identifier and esu_user= $uid";
			$this->call_command("DB_QUERY",Array($sql));
			for ($i=0;$i<$m;$i++){
				$sql = "insert into elert_signup (esu_client, esu_section, esu_user) values ($this->client_identifier, ".$es_signup_list[$i].", $uid)";
//				print "<LI>$sql</LI>";
				$this->call_command("DB_QUERY",Array($sql));
			}
			if ($es_signup_urls!=""){
				$sql = "delete from elert_signup_urls where esurl_identifier not in ($es_signup_urls) and esurl_client = $this->client_identifier and esurl_user = $uid";
			} else {
				$sql = "delete from elert_signup_urls where esurl_client = $this->client_identifier and esurl_user = $uid";
			}
			$this->call_command("DB_QUERY",Array($sql));
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .= "<form label='".LOCALE_ELERT_SIGNUP_LABEL."' name='elert' method='post'>";
			$out .= "<text><![CDATA[".LOCALE_ELERT_SIGNUP_CONFIRM."]]></text>";
			$out .= "</form>";
			$out .="</module>";
		} else {
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .= "<form label='".LOCALE_ELERT_SIGNUP_LABEL."' name='elert' method='post'>";
			$out .= "<text><![CDATA[".LOCALE_ELERT_SORRY_LOGIN_REQUIRED."]]></text>";
			$out .= "</form>";
			$out .="</module>";
		}
		return $out;
	}
	
	function signup($parameters){
		$referer = $this->check_parameters($_SERVER,"HTTP_REFERER");
		$ps = Array();
		if ($referer==""){
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .= "<form label='".LOCALE_ELERT_SIGNUP_ERROR."' name='elert' method='post'>";
			$out .= "<text><![CDATA[".LOCALE_ELERT_SIGNUP_ERROR_CONFIRM."]]></text>";
			$out .= "</form>";
			$out .="</module>";
			return $out;
		} else {
	/*		"esurl_identifier"
			"esurl_client"
			"esurl_user"
			"esurl_url"
			"elert_signup_urls"*/
			$base = "http://".$this->parent->domain.$this->parent->base;
			$signup_length = strlen($base);
			$rest = substr($referer,$signup_length);
//			str_replace(Array(session_name()."=".session_id()),Array(""),);
			$pos = strpos($rest, '?');
//			print "[$pos]";
			$extra="";
			if ($this->check_parameters($parameters,"thread")!=""){
				$extra .= "thread=".$this->check_parameters($parameters,"thread");
			}
			if ($this->check_parameters($parameters,"category")!=""){
				$extra .= "category=".$this->check_parameters($parameters,"category");
			}
			if ($pos===false){
				if ($extra==""){
					$url = $rest;
				} else {
					$url = $rest."?".$extra;
				}
			} else {
				$url = substr($rest,0,$pos);
//				print "[".substr($rest,$pos+1)."]";
				if (strpos($rest, '&')==false){
					$ps[0] = substr($rest,$pos+1);
				} else {
					$ps = SPLIT('&',substr($rest,$pos+1));
				} 
				$m = count($ps); 
				for($i=0;$i<$m;$i++){
					if (session_name()."=".session_id() == $ps[$i]){
						unset($ps[$i]);
					} else if ($this->check_parameters($parameters,"thread")!=""){
						if (substr($ps[$i],0, strlen("thread_identifier=")) == "thread_identifier="){
							unset($ps[$i]);
						}
					}
				}
				if (count($ps)>0){
					$url .= "?".join("&amp;",$ps);
					if ($extra!=""){
						$url .="&amp;".$extra;
					}
				} else {
					if ($extra!=""){
						$url .="?".$extra;
					}
				}
			}
			$uid = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1);
			$sql = "select * from elert_signup_urls where esurl_client = $this->client_identifier and esurl_url='$url' and esurl_user=$uid";
			$result  = $this->call_command("DB_QUERY",Array($sql));
	        $num = $this->call_command("DB_NUM_ROWS",Array($result));
			$this->call_command("DB_FREE",Array($result));
			$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
			if ($num==0){
				$out .= "<form label='".LOCALE_ELERT_SIGNUP_LABEL."' name='elert' method='post'>";
				$out .= "	<input type='hidden' value='ELERT_SIGNUP_SAVE' name='command'/>";
				$out .= "	<input type='hidden' value='".$url."' name='esurl_url'/>";
				$out .= "	<input type='text' size='255' value='' name='esurl_label' label='".LOCALE_ELERT_SIGNUP_WATCHING_LABEL."'/>";
				$out .= "	<input type='submit' value='".LOCALE_OK."' iconify='OK'/>";
				$out .= "</form>";
			} else {
				$out .= "<form label='".LOCALE_ELERT_SIGNUP_LABEL."' name='elert' method='post'>";
				$out .= "<text><![CDATA[".LOCALE_ELERT_SIGNUP_ALREADY_WATCHING."]]></text>";
				$out .= "<input type='submit' value='".LOCALE_OK."' iconify='OK'/>";
				$out .= "</form>";
			}
			$out .="</module>";
			return $out;

		}
	}
	function signup_save($parameters){
		$label = htmlentities($this->validate($this->check_parameters($parameters,"esurl_label","")));
		$url = $this->check_parameters($parameters,"esurl_url","");
		$uid = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1);
		$sql = "insert into elert_signup_urls (esurl_client, esurl_label, esurl_url, esurl_user) values ($this->client_identifier, '$label', '$url', $uid);";
		$this->call_command("DB_QUERY",Array($sql));
		$this->call_command("ENGINE_REFRESH_BUFFER",Array(""));
	}
}
?>