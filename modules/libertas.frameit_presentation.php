<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.frameit_presentation.php
* @date 12 Feb 2004
*/
/**
* This module is the presentation module for hte farmeit module
*/
class frameit extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name				= "frameit";
	var $module_name_label			= "Frame Module (Presentation)";
	var $module_admin				= "0";
	var $module_command				= "FRAMEIT_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "FRAMEIT_";
	var $module_label				= "MANAGEMENT_FRAMEIT";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:11 $';
	var $module_version 			= '$Revision: 1.11 $';
	var $module_creation 			= "16/07/2004";
	var $searched					= 0;
	var $loadedcat					= Array();
	
	
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
	);
	
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
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,print_r($parameter_list,true),__LINE__,"command"));
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
//			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
//				return $this->webContainer;
//			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->display($parameter_list);
			}
			if ($user_command==$this->module_command."VIEW"){
				return $this->view($parameter_list);
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Create table function allow access if in install mode
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                E M B E D S C R I P T   S E T U P   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- 
	- 
	*
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
		$this->load_locale("embedscript");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		/**
		* define the admin access that this user has.
		*/
		return 1;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                         		E M B E D S C R I P T   S I T E   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/

	function display($parameters){
		$wo_owner_id			= $this->check_parameters($parameters,"wo_owner_id",-1);
		$current_menu_location  = $this->check_parameters($parameters,"current_menu_location",-1);
		$redirect				= $this->check_parameters($parameters,"redirect","");
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"parameters",__LINE__,"".print_r($parameters,true).""));}
		$sql = "select * from frameit 
					inner join display_data on fi_menu = display_menu and fi_client=display_client
				where
					display_command = '".$this->module_command."DISPLAY' and fi_client = $this->client_identifier and display_menu = $current_menu_location and fi_identifier = $wo_owner_id";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result 		=	$this->call_command("DB_QUERY",Array($sql));
		if(	$this->call_command("DB_NUM_ROWS",Array($result)) > 0){
	      	$uri			=	"";
			$label			=	"";
			$width			=	"100%";
			$height			=	"100%";
			$show_label		=	0;
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$identifier	= $r["fi_identifier"];
				if($redirect==""){
	        		$uri		= trim($r["fi_uri"]);
				} else {
					$uri 		= $redirect;
				}
				$label			= $r["fi_label"];
				$width			= $r["fi_width"];
				$fi_width_type	= $r["fi_width_type"];
				$height			= $r["fi_height"];
				$fi_pf			= $r["fi_parameterfields"];
				$show_label		= $r["fi_show_label"];
	        }
			if($fi_width_type==0){
				$widthtype	= "%";
			} else {
				$widthtype	= "px";
			}
			$extra="";
			if ($fi_pf!=""){
				if (strpos($fi_pf,"\r\n")===false){
					$fi_pfa = Array($fi_pf);
				} else {
					$fi_pfa = split("\r\n",$fi_pf);
				}
				if (count($fi_pfa)>0){
					if (strpos("?",$uri)===false){
						$s=0;
					} else {
						$s=1;
					}
					$c=$s;
					foreach($fi_pfa as $k){
						$v  = $this->check_parameters($parameters,trim($k));
						if ($v!=""){
							if($c>0){
								$extra .= "&amp;";
							}
							$extra .= "$k=$v";
							$c++;
						}
					}
				}
			}
			if ($extra!=""){
				if ($s==0){
					$uri .= "?$extra";
				} else {
					$uri .= $extra;
				}
			}
	        $this->call_command("DB_FREE",Array($result));
			$output  = "<module name='".$this->module_name."' display=\"display\">";
			$output .= "<frame identifier='$identifier' width='$width$widthtype' height='".$height."px' show_label='$show_label'><uri><![CDATA[$uri]]></uri><label><![CDATA[$label]]></label></frame>";
			$output .= "</module>";
		} else {
			$output="";
		}
		return $output;
	}
	
	function view($parameters){
		$identifier	= $this->check_parameters($parameters,"identifier",-1);
		if ($identifier!=-1){
			$sql = "select * from frameit where fi_client=$this->client_identifier and fi_identifier=$identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$des = $r["fi_longdes"];
            }
            $this->call_command("DB_FREE",Array($result));
			print $des;
			$this->exitprogram();
		}
	}
}

?>
