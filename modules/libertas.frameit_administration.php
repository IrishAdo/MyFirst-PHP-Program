<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.frameit_presentation.php
* @date 12 Feb 2004
*/
/**
* This module is the administration module for Categories it will allow the user to 
* generate Category LISTS which will contain lists of categories that could be used
* by one or more modules.
*/
class frameit_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name				= "frameit_admin";
	var $module_name_label			= "Frame Module (Adminsitration)";
	var $module_admin				= "1";
	var $module_command				= "FRAMEITADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "FRAMEIT_";
	var $module_label				= "MANAGEMENT_FRAMEIT";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:11 $';
	var $module_version 			= '$Revision: 1.12 $';
	var $module_creation 			= "23/07/2004";
	var $searched					= 0;
	var $loadedcat					= Array();
	
	var $admin_access				= 0;	
	var $admin_function_access		= 0;	
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
	
	/**
	*  Administrative Menu Commands
	*
	*  format :
	- 	0 => Command
	-	1 => Label
	-	2 => Roles empty for all roles have access
	-	3 => Menu Path 
				ie LOCALE1/LOCALE2/LOCALE3/LOCALE4 will create a tree structure 4 levels
				deep
	*/
	var $module_admin_options 		= array(
		array("FRAMEITADMIN_LIST", "Frame in Site","","Content Manage/External Content")
	);
	
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*
	*  format :
	- 	0 => Role
	-	1 => Label
	*
	*  basic setting is module_command + "ALL" with COMPLETE ACCESS
	*/
	
	var $module_admin_user_access = array(
		array("FRAMEITADMIN_ALL",			"COMPLETE_ACCESS")
	);
		
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
			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
				return $this->webContainer;
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Create table function allow access if in install mode
			- non secure as all this will attempt to do is return an array that contains the definition of the table 
			- structures does not execute them.
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Secure Administrative functions requires mode ADMIN
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($this->admin_function_access==1){
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- Secure Administrative functions requires mode ADMIN and Role Access
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if ($this->admin_access==1){	
					if ($user_command==$this->module_command."LIST"){
						return $this->module_list($parameter_list);
					}
					if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
						return $this->module_modify($parameter_list);
					}
					if ($user_command==$this->module_command."REMOVE"){
						$this->module_removal($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
					if ($user_command==$this->module_command."SAVE"){
						$this->module_save($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST"));
					}
					if ($user_command==$this->module_command."PREVIEW_FORM"){
						return $this->module_preview_form($parameter_list);
					}
					
				}
			}
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                			S E T U P   F U N C T I O N S
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
		$this->load_locale($this->module_name);
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		$this->page_size				= $this->check_prefs(Array("sp_page_size"));
		/**
		* define the admin access that this user has.
		*/
		$this->admin_function_access	= 0;
		$this->admin_access				= 0;
		/**
		* define the admin access that this user has.
		*/
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_array = array();
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (($this->module_command."ALL"==$access[$index]) || ("ALL"==$access[$index])){
					$this->admin_access=1;
				}
			}
		}
		if (($this->admin_access) && ($this->parent->module_type=="admin" || $this->parent->module_type=="preview")){
			$this->admin_function_access=1;
		}
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
		* Table structure for table 'frameit'
		*/
		$fields = array(
			array("fi_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("fi_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("fi_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fi_creation_date"	,"datetime"					,"NOT NULL"	,"default '0000-00-00 00:00:00'"),
			array("fi_status"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fi_uri"				,"text"						,"NOT NULL"	,"default ''"),
			array("fi_menu"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("fi_parameterfields"	,"text"						,"NOT NULL"	,"default ''"),
			array("fi_width"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fi_height"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fi_width_type"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fi_height_type"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("fi_longdes"			,"text"						,"NOT NULL" ,"default ''"),
			array("fi_show_label"		,"unsigned small integer"	,"NOT NULL"	,"default '0'")
		);
		$primary ="fi_identifier";
		$tables[count($tables)] = array("frameit", $fields, $primary);
		return $tables;
	}
	
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                         					A D M I N   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/

	function module_list($parameters){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*  List all of the entries
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($this->page_size==0){
			$this->page_size=10;
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*  Sql to return Records
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$sql = "select * from frameit where fi_client=$this->client_identifier order by fi_identifier Desc";
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*  Sql to know how many records there are
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/

		$counter_sql = "select count(*) as total from frameit where fi_client=$this->client_identifier order by fi_identifier Desc";

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement, Counter Sql",__LINE__,"[$counter_sql]"));
		}
		$variables = Array();
		$variables["FILTER"]			= "";// $this->filter($parameters,$this->module_command."LIST");
		$variables["MENU_LINKS"]		= "";
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		if($this->admin_access==1 && $this->parent->module_type=="admin"){
			$variables["PAGE_BUTTONS"] = Array(Array("ADD",$this->module_command."ADD",ADD_NEW));
		}
		$extra = Array();
		if ($this->admin_access==1){
			$counter_result = $this->call_command("DB_QUERY",array($counter_sql));
			if (!$counter_result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned] problem with SQL statement"));
				}
				return "";
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
                while($r = $this->call_command("DB_FETCH_ARRAY",Array($counter_result))){
					$number_of_records = $r["total"];
                }
                $this->call_command("DB_FREE",Array($counter_result));
				$page = $this->check_parameters($parameters,"page",1);
				$goto = ((--$page)*$this->page_size);
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- If the value of goto is greater than zero then we need to tell the DB_QUERY function to limit the results
				- to a certain number of results.  ( the DB implementation will deal with changing the sql statement to allow 
				- this as MySQL and MSSQL use different sql statements and positioning to do this.
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				if (($goto!=0)&&($number_of_records>$goto)){
					$extra["action"]="LIMIT";
					$extra["seek"]=$goto;
					$extra["num_results"]=$this->page_size;
				}
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql, ".print_r($extra,true)."]"));
				}
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- Execute the sql to retrieve the actual records
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$result = $this->call_command("DB_QUERY",array($sql,$extra));
				if ($goto+$this->page_size>$number_of_records){
					$finish = $number_of_records;
				}else{
					$finish = $goto+$this->page_size;
				}
				$goto++;
				$page++;
				
				if ($number_of_records>0){
					$num_pages=floor($number_of_records / $this->page_size);
					$remainder = $number_of_records % $this->page_size;
					if ($remainder>0){
						$num_pages++;
					}
				}else{
					$num_pages=0;
					$remainder=0;
				}
				$counter=0;
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- Start defining some of the variables needed
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				$variables["HEADER"] = "Frame Manager List";
				$variables["SEARCHFILTER"] 		= "";
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
					$end_page	 =	$num_pages;
				}else{
					$end_page	=	$this->page_size;
				}
				$user_identifier 		= $_SESSION["SESSION_USER_IDENTIFIER"];
				$variables["END_PAGE"]			= $end_page;
				$variables["ENTRY_BUTTONS"] =Array();
				$variables["CONDITION"]= array();
				$variables["RESULT_ENTRIES"] =Array();
				/*
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				- Add entries to the $variables["RESULT_ENTRIES"] Array
				-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
				*/
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<$this->page_size)){
					$counter++;
					$i = count($variables["RESULT_ENTRIES"]);
					if ($r["fi_menu"]!=0){
						$menu_location = $this->call_command("LAYOUT_GET_BREAD_CRUMB_TRAIL",Array("id"=>$r["fi_menu"]));					
					} else {
						$menu_location = LOCALE_ORPHANED;
					}
					if($r["fi_status"] == "0"){
						$status = "Not Live";
					} else {
						$status = "Live";
					}
					$variables["RESULT_ENTRIES"][$i]=Array(
						"identifier"	=>	$r["fi_identifier"],
						"ENTRY_BUTTONS" =>	Array(
							Array("EDIT",$this->module_command."EDIT",EDIT_EXISTING),
							Array("REMOVE",$this->module_command."REMOVE",REMOVE_EXISTING)
						),
						"attributes"	=>	Array(
							Array(ENTRY_TITLE, $r["fi_label"], "TITLE", "EDIT_DOCUMENT"),
							Array(ENTRY_STATUS, $status, "SUMMARY", ""),
							Array("Location", $menu_location, "SUMMARY", "")
						)
					);
				}
			}
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Take the Array defined above and convert into default XML listing representation
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$out = $this->generate_list($variables);
		return $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Modify/ Create information
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_modify($parameters){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Set default defintion (ADD)
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$form_label 		= LOCALE_ADD;
		$fi_label			= "";
		$fi_status			= "1";
		$fi_width			= "100";
		$fi_width_type		= "%";
		$fi_height			= "300";
		$fi_menu 			= 0;
		$fi_parameterfields	= "";
		$fi_uri				= "";
		$fi_longdes			= "";
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* if Identifier is supplied then load information from database
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($identifier!=-1){
			$form_label 	= LOCALE_EDIT;
			$sql= "select * from frameit where fi_identifier = $identifier and fi_client= $this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$fi_label			= $r["fi_label"];
				$fi_status			= $r["fi_status"];
				$fi_menu			= $r["fi_menu"];
				$fi_width			= $r["fi_width"];
				$fi_width_type		= $r["fi_width_type"];
				$fi_height			= $r["fi_height"];
				$fi_parameterfields	= $r["fi_parameterfields"];
				$fi_uri				= $r["fi_uri"];
				$fi_longdes			= $r["fi_longdes"];
			}
			$this->call_command("DB_FREE",array($result));
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Output XML form structure
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[Frame Manager]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."SAVE\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<page_sections>";
		$out .="		<section label='".LOCALE_FRAMEIT_SETUP."'>
		<seperator_row>
			<seperator>";
		$out .="		<input required=\"YES\" type=\"text\" name=\"fi_label\" label=\"".LOCALE_LABEL."\" size=\"255\"><![CDATA[$fi_label]]></input>";
		$out .="		<input required=\"YES\" type=\"text\" name=\"fi_uri\" label=\"".LOCALE_URL."\" size=\"255\"><![CDATA[$fi_uri]]></input>";
		$out .="		<select name=\"fi_show_label\" label=\"".LOCALE_SHOW_LABEL."\">
							<option value='0'";
		if ($fi_width_type=="0"){
			$out .= " selected='true'";
		}
		$out .=">".LOCALE_NO."</option>
							<option value='1'";
		if ($fi_width_type=="1"){
			$out .= " selected='true'";
		}
		$out .=">".LOCALE_YES."</option>
						</select>";
		$out .="		<select required=\"YES\" name=\"fi_status\" label=\"".LOCALE_STATUS."\">
							<option value='0'";
		if ($fi_status=="0"){
			$out .= " selected='true'";
		}
		$out .=">".STATUS_NOT_LIVE."</option>
							<option value='1'";
		if ($fi_status=="1"){
			$out .= " selected='true'";
		}
		$out .=">".STATUS_LIVE."</option>
						</select>";
		$out .="		<input type=\"hidden\" name=\"fi_width\" value=\"100\"/>";
		$out .="		<input type=\"hidden\" name=\"fi_width_type\" value=\"0\"/>";
		$out .="		<input required=\"YES\" type=\"text\" name=\"fi_height\" label=\"".LOCALE_HEIGHT." (pixels)\" size=\"5\"><![CDATA[$fi_height]]></input>";
		$out .="		<textarea name=\"fi_parameterfields\" label=\"".LOCALE_FRAME_PARAMETER_LIST."\" size='20' width=\"24\" height=\"7\"><![CDATA[$fi_parameterfields]]></textarea>";
		$out .="</seperator><seperator>";
		$out .="<textarea name=\"fi_longdes\" label=\"".LOCALE_DESCRIPTION."\" size='30' width=\"30\" height=\"25\"><![CDATA[$fi_longdes]]></textarea>";
		$out .="</seperator></seperator_row>";
		$out .="</section>";
		$out .="		<section label='".LOCALE_LOCATION_TAB."'>";
		if ($this->parent->server[LICENCE_TYPE]==MECM){
			$data =  $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($fi_menu,"use_useraccess_restrictions"=>"YES"));
		} else {
			$data =  $this->call_command("LAYOUT_LIST_MENU_OPTIONS",Array($fi_menu));
		}
		$out .= "	<select label=\"".LOCALE_DEFAULT_MENU_MSG."\" name=\"fi_menu\" >$data</select>";

		$web_containers = split("~----~",$this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",Array("module"=>$this->webContainer, "identifier"=>$identifier)));
		if ($web_containers[0]!=""){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}
		$out .="		</section>";
		$out .= 		$this->preview_section($this->module_command.'PREVIEW_FORM');
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	function module_removal($parameters){
		$identifier					=	$this->check_parameters($parameters,"identifier",-1);
		if ($identifier!=-1){
			$sql ="delete from frameit where fi_identifier = $identifier and fi_client = $this->client_identifier";
			$result = $this->call_command("DB_QUERY",array($sql));
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"DB_Values",__LINE__,"[$sql]"));}
			$this->call_command("WEBOBJECTS_MANAGE_MODULE", 
				Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"cmd"			=> "REMOVE"
				)
			);	
		}
	}
	function module_save($parameters){
		$identifier					=	$this->check_parameters($parameters,"identifier");
		$currentlyhave				=	$this->check_parameters($parameters	,"currentlyhave");
		$replacelist				=	$this->check_parameters($parameters	,"web_containers",Array());
		$DB_values = Array(
			"fi_label"				=>	$this->check_parameters($parameters,"fi_label",""),
			"fi_status"				=>	$this->check_parameters($parameters,"fi_status","0"),
			"fi_uri"				=>	$this->check_parameters($parameters,"fi_uri",""),
			"fi_menu"				=>	$this->check_parameters($parameters,"fi_menu","0"),
			"fi_parameterfields" 	=>	$this->check_parameters($parameters,"fi_parameterfields",""),
			"fi_show_label"			=>	$this->check_parameters($parameters,"fi_show_label","0"),
			"fi_width"				=>	$this->check_parameters($parameters,"fi_width","100"),
			"fi_height"				=>	$this->check_parameters($parameters,"fi_height","300"),
			"fi_width_type"			=>	$this->check_parameters($parameters,"fi_width_type","0"),
			"fi_longdes"			=>	$this->check_parameters($parameters,"fi_longdes","")
		);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"DB_Values",__LINE__,"[".print_r($DB_values,true)."]"));
		}
		$now = $this->libertasGetDate();
		$field_list="";
		$value_list="";
		$update_list="";
		$extract_list="";
		$c=0;
		foreach($DB_values as $key => $value){
			if ($c!=0){
				$field_list		.=	", ";
				$value_list		.=	", ";
				$update_list	.=	", ";
				$extract_list	.=	" and ";
			}
			$c++;
			$update_list	.=	$key."='$value'";
			$extract_list	.=	$key."='$value'";
			$field_list		.=	$key;
			$value_list		.=	"'$value'";
		}
		if ($identifier==-1){ // add new entry
			$sql ="insert into frameit (fi_creation_date, fi_client, $field_list) values ('$now', $this->client_identifier, $value_list)";
			$this->call_command("DB_QUERY",Array($sql));
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
			$sql ="select * from frameit where $extract_list and fi_creation_date='$now' and fi_client=$this->client_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$identifier = $r["fi_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE", Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $DB_values["fi_label"],
					"wo_command"	=> $this->webContainer."DISPLAY",
					"cmd"			=> "ADD",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);
		} else {
			$sql ="update frameit set $update_list where fi_identifier = $identifier and fi_client=$this->client_identifier";
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
			$this->call_command("DB_QUERY",Array($sql));
			$this->call_command("WEBOBJECTS_MANAGE_MODULE", Array(
					"owner_module" 	=> $this->webContainer,
					"owner_id" 		=> $identifier,
					"label" 		=> $DB_values["fi_label"],
					"wo_command"	=> $this->webContainer."DISPLAY",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist
				)
			);	
		}
		$sql = "select distinct fi_menu from frameit 
				where fi_client=$this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
       	$keep_list_data  = Array();
		$found=0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$keep_list_data[count($keep_list_data)]  = $r["fi_menu"];
			if($r["fi_menu"]==$DB_values["fi_menu"]){
				$found=1;
			}
        }
        $this->call_command("DB_FREE",Array($result));
		$sql = "delete from display_data where display_command='".$this->webContainer."DISPLAY' and display_client=$this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"DB_Values",__LINE__,"[$found] [".$DB_values["fi_menu"]."]"));}
		foreach($keep_list_data as $k => $v){
			$sql= "insert into display_data (display_menu, display_command, display_client) values (" . $v . ", '".$this->webContainer."DISPLAY', $this->client_identifier)";
			$this->call_command("DB_QUERY",Array($sql));
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
		}
	}
	
	function module_preview_form($parameters){
		$this->parent->print_first .="<script>
		try{
			if(parent.document){
				if (parent.document.all.preview_loaded.value==0){
					parent.document.all.preview.style.display='';
					parent.document.all.preview_loading.style.display='none';
				}
			}
		} catch (e){
			alert('Sorry currently unable to show preview screen');
		}
		</script>";
		$uri  			= $this->check_parameters($parameters,"fi_uri");
		$width  		= $this->check_parameters($parameters,"fi_width",100);
		$height  		= $this->check_parameters($parameters,"fi_height");
		$width_type		= $this->check_parameters($parameters,"fi_width_type","0");
		if ($width_type==0){
			$width_type ="%";
		} else {
			$width_type ="px";
		}
		$label			= $this->check_parameters($parameters,"fi_label");
		$show_label			= $this->check_parameters($parameters,"fi_show_label");
		$uri  = $this->check_parameters($parameters,"fi_uri");
		$out = "<module name=\"".$this->module_name."\" display=\"display\">
					<frame identifier='0' width='".$width."$width_type' height='".$height."px' show_label='$show_label'><uri><![CDATA[$uri]]></uri><label><![CDATA[$label]]></label></frame>
				</module>";
		return $out;
	}
}

?>
