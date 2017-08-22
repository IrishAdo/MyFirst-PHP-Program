<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.filter_administration.php
*/
/**
*
* This module is the administration module for Categories it will allow the user to 
* generate Category LISTS which will contain lists of categories that could be used
* by one or more modules.
-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*/
define ("LOCALE_FILTER_SETUP"				, "Filter Defintion");
define ("FILTERADMIN_CONTAINER"				, "Filter Container");
class filter_admin extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name				= "filter_admin";
	var $module_name_label			= "Filter Module (Adminsitration)";
	var $module_admin				= "1";
	var $module_command				= "FILTERADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "FILTER_";
	var $module_label				= "MANAGEMENT_FILTER";
	var $module_modify	 			= '$Date: 2005/03/02 09:53:37 $';
	var $module_version 			= '$Revision: 1.18 $';
	var $module_creation 			= "13/08/2004";
	var $searched					= 0;
	
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
	*  filter options
	*/
	
	var $match_list = Array(
			Array("Equals "						, "= '[[value]]'"),
			Array("Does Not Equal "				, "!= '[[value]]'"),
			Array("Less than "					, "< '[[value]]'"),
			Array("Less than or equal to "		, "<= '[[value]]'"),
			Array("Greater than "				, "> '[[value]]'"),
			Array("Greater than or equal to "	, ">= '[[value]]'"),
			Array("Contains"					, "like '%[[value]]%'"),
			Array("Does not contains"			, "not like '%[[value]]%'"),
			Array("Begins with"					, "like '[[value]]%'"),
			Array("Ends with"					, "like '%[[value]]'"),
			Array("Does not begin with"			, "not like '[[value]]%'"),
			Array("Does not end with"			, "not like '%[[value]]'")
		);
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
	);
	
	// no role access
	var $module_admin_user_access = array();
		
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
			/**
			* Create table function allow access if in install mode
			* non secure as all this will attempt to do is return an array that contains the definition of the table 
			* structures does not execute them.
			*/
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."GET_SQL"){
				return $this->manage_filter_object($parameter_list);
			}
			if ($user_command==$this->module_command."GET_MATCHLIST"){
				return $this->match_list;
			}
			
			/**
			* Secure Administrative functions requires mode ADMIN 
			*/
			if ($this->admin_function_access==1){
				if ($user_command==$this->module_command."EMBED"){
					return $this->embed($parameter_list);
				}
				if ($user_command==$this->module_command."TEST_QUERY"){
					return $this->test_query($parameter_list);
				}
				if ($user_command==$this->module_command."MANAGE_OBJECT"){
					return $this->manage_filter_object($parameter_list);
				}
			}
		}
		return "";
	}
	/**
	*                                			S E T U P   F U N C T I O N S
	*/
	
	/** 
	*
	* Initialise function
	*
	* This function will initialise some variables for this modules functions to use.
	*
	* this function is called by the constructor it over writes the basic
	* module::initialise() function allowing you to define any extra constructor
	* functionality.
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
		if ($this->parent->module_type=="admin" || $this->parent->module_type=="preview" || $this->parent->module_type=="files"){
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
		* Table structure for table 'filter_list'
		*/
		$fields = array(
			array("filter_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","auto_increment"),
			array("filter_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("filter_label"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("filter_creation_date"	,"datetime"					,"NOT NULL"	,"default '0000-00-00 00:00:00'"),
			array("filter_status"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("filter_module"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("filter_owner"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("filter_cache"			,"text"						,"NOT NULL" ,"default ''")
		);
		$primary ="filter_identifier";
		$tables[count($tables)] = array("filter_list", $fields, $primary);
		/**
		* Table structure for table 'filter_definition'
		*/
		$fields = array(
			array("fdef_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","auto_increment"),
			array("fdef_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("fdef_filter"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("fdef_field"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fdef_condition"		,"unsigned integer"			,"NOT NULL"	,"default '0'"), // 0= or , 1= and
			array("fdef_value"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("fdef_join"			,"varchar(255)"				,"NOT NULL"	,"default ''")
		);
		$primary ="fdef_identifier";
		$tables[count($tables)] = array("filter_definition", $fields, $primary);
		/**
		* Table structure for table 'filter_definition'
		*/
		$fields = array(
			array("ford_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","auto_increment"),
			array("ford_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ford_filter"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ford_field"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("ford_direction"		,"unsigned small integer"	,"NOT NULL"	,"default '0'") // 0= Ascending,  1 = descending 
		);
		$primary ="ford_identifier";
		$tables[count($tables)] = array("filter_order", $fields, $primary);
		return $tables;
	}
	
	
	/**
	*                         					A D M I N   F U N C T I O N S
	*/
	
	/**
	* embed this tab in another modules administrative screen
	*
	* @return String Section tag for embedding into a form
	*/	
	function embed($parameters){
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Set default defintion (ADD)
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$hide		 		= $this->check_parameters($parameters,"hide",-1);
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$module		 		= $this->check_parameters($parameters,"module","");
		$field_list			= $this->check_parameters($parameters,"field_list",-1);
		$extratags			= $this->check_parameters($parameters,"extratags",Array());
		$filter_label		= "";
		$filter_status		= 0;
		$filter_identifier	= -1;

		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* if Identifier is supplied then load information from database
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if ($identifier!=-1){
			$form_label 	= LOCALE_EDIT;
			$sql= "select * from filter_list where filter_owner = $identifier and filter_module='$module' and filter_client=$this->client_identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$filter_identifier		= $r["filter_identifier"];
			}
			$this->parent->db_pointer->database_free_result($result);
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Output XML form structure
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$out ="		<section label='".LOCALE_FILTER_SETUP."' name='embedfilter'";
		if($hide==1){
			$out .= " hidden='true'";
		}
		$out .=">";
		$out .="<filterbuilder module='$module' owner='$identifier'>";
		$out .="<extratags>";
		foreach($extratags as $key => $value){
			$out .="<$key><![CDATA[$value]]></$key>";
		}
		$out .="</extratags>";
		$out .="		<filterselect name=\"f_field\" label=\"Select Field\">";
		$max= count($field_list);
		if ($max>=1){
			for ($index=0;$index<$max;$index++){
				$out .="<option value='".$field_list[$index]["field"]."' type='".$field_list[$index]["type"]."' order='".$field_list[$index]["orderable"]."'>".$field_list[$index]["label"]."</option>";
			}
		}
		$out .="</filterselect>";

		$out .="<filterselect name=\"f_match\" label=\"".LOCALE_STATUS."\">";
		
		$max= count($this->match_list);
		for ($index=0;$index<$max;$index++){
			$out .= "<option value='".$index."'";
			$out .=">".$this->match_list[$index][0]."</option>";
		}
		$out .="</filterselect>";

		$out .="<filterselect name=\"f_value\" label=\"Value\">";
		
		$max = count($field_list);
		for ($index=0; $index < $max; $index++){
			if ($field_list[$index]["type"]=="check" || $field_list[$index]["type"]=="list" || $field_list[$index]["type"]=="select" || $field_list[$index]["type"]=="radio"){
				$out .= "<options field='".$field_list[$index]["field"]."'>";
				$m = count($field_list[$index]["options"]);
				if ($m>0){
					for ($i=0;$i<$m;$i++){
						$out .= "<option value='".htmlentities($field_list[$index]["options"][$i], ENT_QUOTES)."'>".htmlentities($field_list[$index]["options"][$i], ENT_QUOTES)."</option>";
					}
				}
				$out .= "</options>";
			}
		}
		$out .="</filterselect>";
		
		$out .="<filterdef>";
		$sql = "select * from filter_definition where fdef_client = $this->client_identifier and fdef_filter = $filter_identifier order by fdef_identifier ";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$out .="<definition field='".$r["fdef_field"]."' condition='".$r["fdef_condition"]."' join='".$r["fdef_join"]."'><![CDATA[".trim($r["fdef_value"])."]]></definition>";
        }
        $this->parent->db_pointer->database_free_result($result);
		$out .="</filterdef>";
		/*************************************************************************************************************************
        * this next part could be updated at a later date to allow the specification of multiple order fields
        *************************************************************************************************************************/
		$out .="<filterorder>";
		$sql = "select * from filter_order where ford_client = $this->client_identifier and ford_filter = $filter_identifier order by ford_identifier ";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
		$result  = $this->parent->db_pointer->database_query($sql);
		$c=0;
        while(($r = $this->parent->db_pointer->database_fetch_array($result)) && ($c==0)){
			$c=1;
			$out .="<direction>".$r["ford_direction"]."</direction>";
			$out .="<field>".$r["ford_field"]."</field>";
        }
        $this->parent->db_pointer->database_free_result($result);
		$out .="</filterorder>";
		$out .="</filterbuilder>";
		$out .="</section>";
		return $out;
	}
	
	/**
	* call the test query from the module in question
	* 
	*	"filter_builder_blockinfo"	=> Block of information represents the block list
	*	"module"					=> module that this links to 
	*	"owner"						=> identifier, of record in some table 
	*	"cmd"						=> Command to execute (GET, INSERT, UPDATE and REMOVE)
	*	"filter"					=> 
	* each row in block equates to the following information
	*		id:::field:::matchstring:::conditionaljoin:::value
	* 
	* @param Array associative Array ("cmd", "filter", "module", "filter_builder_blockinfo");
	* @return results from module
	*/
	
	function manage_filter_object($parameters){
		$cmd 	= $this->check_parameters($parameters,"cmd");
		$filter = $this->check_parameters($parameters,"filter",-1);
		$module	= $this->check_parameters($parameters,"module");
		$maps	= $this->check_parameters($parameters,"maps", Array());
		$owner	= $this->check_parameters($parameters,"owner",$this->check_parameters($parameters,"identifier"));
		$block	= $this->check_parameters($parameters,"filter_builder_blockinfo");
		$label='';
		$now	= $this->LibertasGetDate();
		$ok = 0;
//		print "<li>".__FILE__."@".__LINE__."<p>".print_r($parameters,true)."</p></li>";
		if ($this->module_debug){
			print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
		}
		
		if ($cmd == "GET"){
			$ok = 1;
			$filter = -1;
			$sql ="select filter_identifier, filter_cache from filter_list where filter_client='$this->client_identifier' and filter_module='$module' and filter_owner = $owner";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$cache_sql="";
//			print "<li>$sql</li>";
			$order="";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$cache_sql = split("::", $r["filter_cache"]);
				$filter_identifier = $r["filter_identifier"];
            }
            $this->parent->db_pointer->database_free_result($result);
			$sql = "select * from filter_order where ford_client='$this->client_identifier' and ford_filter = $filter_identifier";
//			print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result  = $this->parent->db_pointer->database_query($sql);
           	$ford_field 	= "";
			$ford_direction	= "";
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$ford_field 	= $r["ford_field"];
				$ford_direction	= $r["ford_direction"];
            }
            $this->parent->db_pointer->database_free_result($result);
			if ($this->module_debug){
				print "<li>".__FILE__."@".__LINE__."<pre>".print_r($cache_sql, true)."</pre></li>";
			}
			
			if(is_array($cache_sql)){
				return Array("join" => $this->check_parameters($cache_sql,0), "where" => $this->check_parameters($cache_sql,1), "order"=>Array("field"=>$ford_field,"dir"=>$ford_direction));
			} else {
				return Array("join" => "", "where" => "", "order"=>Array("field"=>"","dir"=>0));
			}
		} else {
/*
			array("ford_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ford_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ford_filter"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("ford_field"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
			array("ford_direction"		,"unsigned small integer"	,"NOT NULL"	,"default '0'") // 0= Ascending,  1 = descending 
*/
			$a = $this->call_command($module."GEN_SQL_CACHE",Array("block" => $block, "match_list" => $this->match_list, "identifier"=>$owner, "maps" => $maps));
			if ($this->module_debug){ 
				print "<li>".__FILE__."@".__LINE__."<pre>".print_r(Array("block" => $block, "match_list" => $this->match_list, "identifier"=>$owner, "maps" => $maps), true)."</pre></li>";
				print "<li>".__FILE__."@".__LINE__."<pre>".print_r($a, true)."</pre></li>";
			}
			$d = $a["join"]."::".$a["where"];
			$cache_SQL = str_replace(
				Array("'"), 
				Array("\\'"), 
				$d
			);
//			print $module."GEN_SQL_CACHE::$cache_SQL";
//			$this->exitprogram();
			if ($this->module_debug){ 
				print "<li>".__FILE__."@".__LINE__."<pre>".$cache_SQL."</pre></li>";
			}
			if($cache_SQL==""){
				$cache_SQL = str_replace(Array("'"), Array("\\'"), $this->gen_sql_cache(Array("block" => $block, "match_list" => $this->match_list)));
			}
			if ($this->module_debug){ 
				print "<li>".__FILE__."@".__LINE__."<pre>".$cache_SQL."</pre></li>";
			}
			$blocklist = split("\n",$block);
	
			if ($cmd == "UPDATE"){
				$ok = 1;
				$filter = -1;
				$sql ="select * from filter_list where filter_client='$this->client_identifier' and filter_module='$module' and filter_owner = $owner";
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
				$result  = $this->parent->db_pointer->database_query($sql);
	            while($r = $this->parent->db_pointer->database_fetch_array($result)){
	            	$filter = $r["filter_identifier"];
	            }
	            $this->parent->db_pointer->database_free_result($result);
				if($filter == -1){
					$cmd= "INSERT";
				}else{
					$sql = "delete from filter_order where ford_client=$this->client_identifier and ford_filter = $filter";
//					print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
					$this->parent->db_pointer->database_query($sql);
					$sql = "update filter_list
						set filter_label = '$label', filter_cache='$cache_SQL' 
					where 
					filter_client = $this->client_identifier and filter_module = '$module' and filter_owner =$owner 
					";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					$this->parent->db_pointer->database_query($sql);
					$sql = "delete from filter_definition where fdef_client = $this->client_identifier and fdef_filter=$filter ";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					$this->parent->db_pointer->database_query($sql);
				}
			}
			if ($cmd == "INSERT"){
				$ok = 1;
				$filter = $this->getUid();
				$sql = "insert into filter_list
				(filter_identifier, filter_client, filter_module, filter_owner, filter_label, filter_cache, filter_creation_date) 
					values
				($filter, '$this->client_identifier', '$module', $owner,  '$label', '$cache_SQL', '$now')";
				$this->parent->db_pointer->database_query($sql);
			} 
			$filter_field = $this->check_parameters($parameters,"choosen_order_field");
//			print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
			if($filter_field!=""){
				$filter_field_order = $this->check_parameters($parameters,"rank_order",0);
				$sql = "insert into filter_order (ford_client, ford_filter, ford_field, ford_direction) values ($this->client_identifier, $filter, '$filter_field', $filter_field_order)";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				$this->parent->db_pointer->database_query($sql);
			}
//			
			if ($this->module_debug){ 
//				print "<li>".__FILE__."@".__LINE__."<pre>".print_r($parameters, true)."</pre></li>";
				$this->exitprogram();
			}
		}
		if ($cmd == "REMOVE"){
			$ok = 0;
			$sql = "select * from filter_list  where filter_client='$this->client_identifier' and filter_module='$module' and filter_owner = $owner";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result  = $this->parent->db_pointer->database_query($sql);
			$filter=-1;
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$filter=$r["filter_identifier"];
            }
            $this->parent->db_pointer->database_free_result($result);
			$sql ="delete from filter_list where filter_client='$this->client_identifier' and filter_module='$module' and filter_owner = $owner";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->parent->db_pointer->database_query($sql);
			$sql = "delete from filter_definition where fdef_client = $this->client_identifier and fdef_filter=$filter";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$this->parent->db_pointer->database_query($sql);
		}
		
		
		if ($ok == 1){
			foreach($blocklist as $index => $row){
				if($row!=""){
					$blocklist[$index] = split(":::",$row);
					$fdef_identifier = $this->getUid();
					$sql = "insert into filter_definition (
							fdef_identifier, 
							fdef_client, 
							fdef_filter, 
							fdef_field, 
							fdef_condition, 
							fdef_value, 
							fdef_join
						) values (
							$fdef_identifier, 
							$this->client_identifier, 
							$filter, 
							'".$blocklist[$index][1]."', 
							'".$blocklist[$index][2]."', 
							'".$blocklist[$index][4]."', 
							'".$blocklist[$index][3]."' 
						)";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					$this->parent->db_pointer->database_query($sql);
				}
			}
		}
	}
	/**
	* call the test query from the module in question
	*
	* @param Array associative Array ("block", "module", "owner");
	* @return results from module
	*/
	function test_query($parameters){
		$block  = $this->check_parameters($parameters,"block");
		$module = $this->check_parameters($parameters,"module");
		$owner = $this->check_parameters($parameters,"owner");
		$parameters["match_list"] = $this->match_list;
		return $this->call_command($module."TEST_QUERY", $parameters);
	}
}

?>
