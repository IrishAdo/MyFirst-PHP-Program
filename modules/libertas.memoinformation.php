<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.memoinformation.php
* @date 06 Feb 2004
*/
/**
* This module is the module for displaying any comments for a page
*/

class memoinformation extends module{
	/**
	*  Class Variables
	*/
	var $module_name_label			= "Memo Information Management Module";
	var $module_name				= "memoinformation";
	var $module_command				= "MEMOINFO_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.7 $';
	var $module_creation 			= "12/02/2004";
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
	var $WebObjects				 	= array();
	
	/**
	*  filter options
	*/
	var $display_options			= array();

	var $admin_access				= 0;
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
			if ($user_command==$this->module_command."INSERT"){
				return $this->record_insert($parameter_list);
			}
			if ($user_command==$this->module_command."UPDATE"){
				return $this->record_update($parameter_list);
			}
			if ($user_command==$this->module_command."DELETE"){
				return $this->record_delete($parameter_list);
			}
			if ($user_command==$this->module_command."REMOVE_LIST"){
				return $this->record_remove_list($parameter_list);
			}
			if ($user_command==$this->module_command."DELETE_ALL_CLIENT_MODULE"){
				return $this->delete_module($parameter_list);
			}
			
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."GET_SQL_COMPONENTS"){
				return $this->get_sql_objects($parameter_list);
			}
			if ($user_command==$this->module_command."COPY_INFO"){
				return $this->copy_objects($parameter_list);
			}
		}
		return "";
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
		* Table structure for table 'memo_information'
		*/
		$fields = array(
			array("mi_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment","key"),
			array("mi_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("mi_type"			,"varchar(255)"				,"NOT NULL"	,"default '0'"),
			array("mi_memo"			,"text"						,"NOT NULL"	,"default ''"),
			array("mi_link_id"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("mi_field"		,"varchar(255)"				,"NOT NULL" ,"default ''")
		);
		$primary ="mi_identifier";
		$tables[count($tables)] = array("memo_information", $fields, $primary);
		
		return $tables;

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
		if ($this->parent->module_type=="admin" || $this->parent->module_type=="install"){
			$this->admin_access	= 1;
		} else {
			$this->admin_access	= 0;
		}
		return 1;
	}
	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* FN:: record_update()
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* this function is used to save the comment
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function record_update($parameters){
		$debug			= $this->check_parameters($parameters,"debug",0);
		$mi_type		= $this->check_parameters($parameters,"mi_type");
		$mi_link_id		= $this->check_parameters($parameters,"mi_link_id");
		$mi_memo		= $this->check_parameters($parameters,"mi_memo");
		$mi_field 		= $this->check_parameters($parameters,"mi_field");
	/* */
  		if ($mi_field==""){
			$sql 		= "delete from memo_information where mi_client=$this->client_identifier and mi_type='$mi_type' and mi_link_id=$mi_link_id";
		} else {
			$sql 		= "delete from memo_information where mi_client=$this->client_identifier and mi_type='$mi_type' and mi_link_id=$mi_link_id and mi_field='$mi_field'";
		}
		if ($debug==1) print "<p>$sql</p>";
		$this->call_command("DB_QUERY",array($sql));
		$sql = "insert into memo_information (mi_client, mi_type, mi_memo, mi_link_id, mi_field) values ($this->client_identifier,'$mi_type','$mi_memo', $mi_link_id, '$mi_field')";
		//$sql = "update memo_information set mi_memo ='$mi_memo' where mi_field='$mi_field' and mi_type ='$mi_type' and mi_link_id='$mi_link_id' and mi_client = $this->client_identifier";
//		print "<p>$sql</p>";
		$this->call_command("DB_QUERY",array($sql));
//		$this->exitprogram();
	}
	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* FN:: record_insert()
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* this function is used to save the comment
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function record_insert($parameters){
		$mi_type		= $this->check_parameters($parameters,"mi_type");
		$mi_link_id		= $this->check_parameters($parameters,"mi_link_id");
		$description	= $this->check_parameters($parameters,"mi_memo");
		$mi_field 		= $this->check_parameters($parameters,"mi_field");
		$set_client 		= $this->check_parameters($parameters,"set_client",-1);
		if ($set_client==-1){
			$sql 		= "insert into memo_information (mi_client, mi_type, mi_memo, mi_link_id, mi_field) values ($this->client_identifier,'$mi_type','$description', $mi_link_id, '$mi_field')";
		} else {
			$sql 		= "insert into memo_information (mi_client, mi_type, mi_memo, mi_link_id, mi_field) values ($set_client,'$mi_type','$description', $mi_link_id, '$mi_field')";
		}
		$this->call_command("DB_QUERY",array($sql));
	}


	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* FN:: record_delete()
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* this function is used to delete a stored memo field
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function record_delete($parameters){
		$type	= $this->check_parameters($parameters,"mi_type");
		$link	= $this->check_parameters($parameters,"mi_link_id");
		$field 	= $this->check_parameters($parameters,"mi_field");
		$sql 	= "delete from memo_information where mi_client=$this->client_identifier and mi_type='$type' and mi_link_id='$link' and mi_field='$field'";
		$this->call_command("DB_QUERY",array($sql));
	}

	/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* FN:: record_delete()
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* this function is used to delete a stored memo field
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function record_remove_list($parameters){
		$type	= $this->check_parameters($parameters,"mi_type");
		$list	= $this->check_parameters($parameters,"mi_list");
		$field 	= $this->check_parameters($parameters,"mi_field");
		$sql 	= "delete from memo_information where mi_client=$this->client_identifier and mi_type='$type' and mi_link_id in ($list) and mi_field='$field'";
		$this->call_command("DB_QUERY",array($sql));
	}

	function get_sql_objects($parameters){
		$table_as		= $this->check_parameters($parameters,"table_as","m1");
		$field_as		= $this->check_parameters($parameters,"field_as","mi_memo");
		$field			= $this->check_parameters($parameters,"identifier_field");
		$mi_field		= $this->check_parameters($parameters,"mi_field");
		$client_field	= $this->check_parameters($parameters,"client_field");
		$cmd 			= $this->check_parameters($parameters,"module_command");
		$join_type		= $this->check_parameters($parameters,"join_type","left outer");
		$left ="";
		$return_field = "$table_as.mi_memo as ".$field_as." ";
		$left	= " $join_type join memo_information as ".$table_as." on (".$table_as.".mi_link_id = $field and ".$table_as.".mi_type='$cmd' and ".$table_as.".mi_field='".$mi_field."' and ".$table_as.".mi_client = $client_field)";
		//$left	= " inner join memo_information as ".$table_as." on (".$table_as.".mi_link_id = $field and ".$table_as.".mi_type='$cmd' and ".$table_as.".mi_field='".$mi_field."' and ".$table_as.".mi_client = $client_field)";
		$where	= " and (($table_as.mi_client=".$this->client_identifier." or ".$table_as.".mi_client is null) and (".$table_as.".mi_type='$cmd' or ".$table_as.".mi_type is null))";
		return Array(
			"return_field"	=>	$return_field,
			"join"			=>	$left,
			"where"			=>	$where,
			"where_field"			=>	"$table_as.mi_memo"
		);
	}
	
	function copy_objects($parameters){
		$old_link_id	= $this->check_parameters($parameters,"old_link_id");
		$mi_type		= $this->check_parameters($parameters,"mi_type");
		$new_link_id	= $this->check_parameters($parameters,"new_link_id");
		$sql 			= "select * from memo_information where mi_type='$mi_type' and mi_client = $this->client_identifier and mi_link_id = $old_link_id";
		$result  		= $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$sql 		= "insert into memo_information (mi_client, mi_type, mi_memo, mi_link_id, mi_field) values ($this->client_identifier,'".$r["mi_type"]."','".$r["mi_memo"]."', $new_link_id, '".$r["mi_field"]."')";
        	$this->call_command("DB_QUERY",Array($sql));
        }
        $this->call_command("DB_FREE",Array($result));
	}
	
	// a function to remove all the occurances of a moudles data.
	function delete_module($parameters){
		$mi_type= $this->check_parameters($parameters,"mi_type");
		$sql ="delete from memo_information where mi_client = $this->client_identifier and mi_type='$mi_type'";
		$this->call_command("DB_QUERY",Array($sql));
		return "";
	}
}
?>
