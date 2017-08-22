<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.database.php
* @date 09 Oct 2002
*/
/**
* This INTERFACE module does not connect to any database it sets up the base functionality for
* other modules to define.
*
* This moudle should not ever be loaded directly instead please load the database modules that
* extend this module which will include this module as default
*
* NOTE:: all functions return null as this is an interface module
*/
class database extends module{
	/**#@+
	* Class Variables
    * @var string
	*/
	var $module_load_type		= "__SYSTEM__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name			="database";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 		= '$Revision: 1.11 $';
	var $module_command			="DB_"; 		// all commands specifically for this module will start with this token
	/**#@+
	* Class Variables
    * @var Pointer
	*/
	var $connection				= null;
	var $database				= null;
	/**#@+
	* Class Variables
    * @var Array
	*/
	var $modules 				= array(); 				// A list of all the modules in the system.
	var $result_list			= Array();
//	var $module_debug=true;
	
	/**
	*  Class Methods
	*/

	/**
	* command()
	* want to do anything with this module go through me simply create a condition for
	* the user command that you want to execute and hey presto I'll return the output of
	* that module
	*/
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			if (is_array($parameter_list))
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,$user_command, __LINE__, join(", ",$parameter_list)));
		}
		
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*/
		if (strpos($user_command,$this->module_command)===0){
			if ($user_command=="DB_DEBUG_ON"){
				$this->module_debug = true;
			}
			if ($user_command=="DB_DEBUG_OFF"){
				$this->module_debug = false;
			}
			if ($user_command=="DB_GET_MODULE"){
				return $this->get_module_name();
			}
			if ($user_command=="DB_GET_VERSION"){
				return $this->get_module_version();
			}
			if ($user_command=="DB_QUERY"){
				return $this->database_query($parameter_list[0], $this->check_parameters($parameter_list,1,Array()));
			}
			if ($user_command=="DB_SELECT"){
				return $this->database_select_db($parameter_list[0]);
			}
			if ($user_command=="DB_CONNECT"){
				return $this->database_connect($parameter_list[0],$parameter_list[1],$parameter_list[2]);
			}
			if ($user_command=="DB_ERROR"){
				return $this->database_error();
			}
			if ($user_command=="DB_NUM_ROWS"){
				return $this->database_num_rows($parameter_list[0]);
			}
			if ($user_command=="DB_FETCH_ARRAY"){
				return $this->database_fetch_array($parameter_list[0]);
			}
			if ($user_command=="DB_SEEK"){
				return $this->database_data_seek($parameter_list[0],$parameter_list[1]);
			}
			if ($user_command=="DB_FETCH_ROW"){
				return $this->database_fetch_row($parameter_list[0]);
			}
			if ($user_command=="DB_FREE"){
				return $this->database_free_result($parameter_list[0]);
			}
			if ($user_command=="DB_CLOSE"){
				return $this->database_close($parameter_list[0]);
			}
			if ($this->parent->module_type=="install"){
				if ($user_command=="DB_CREATE_TABLE"){
					return $this->create_table($parameter_list[0],$parameter_list[1],$parameter_list[2]);
				}
			}
			if ($user_command=="DB_PREPARE_SQL_PARAMETER"){
				return $this->database_prepare_sql_parameter($parameter_list[0]);
			}
			if ($user_command=="DB_TABLE_EXISTS"){
				return $this->table_exists($parameter_list[0]);
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	
    /**
	* connect to a database server
    *
    * establishes a connection to a SQL server. The following defaults are assumed for missing optional parameters: 
	* host = 'localhost:3306', username = name of the user that owns the server process and password = empty password
    *
    * @param String the location of the host file
    * @param String the username of the user able to connect to the database
    * @param String the password of the user able to connect to the database
    *
    * @return Connection
	*/
	function database_connect($host,$USERNAME, $password){
		return null;
	}
	
	
    /**
	* select the database to connect to on database connection
    *
	* Sets the current active database on the server that's associated with the specified connection. 
	* If no connection is specified, the last opened connection is assumed. If no connection is open, 
	* the function will try to establish a link as if database::database_connect was called without 
	* parameters
	*
    * @param String the name of the database
    * @param Connection the database connection to use
    * @param String the password of the user able to connect to the database
    *
    * @return DatabaseConnection
	*/
	function database_select_db($db){
		return null;
	}

	/**
	* Check for database error
    *
	* Returns the error text from the last SQL function, or '' (the empty string) if no error occurred.
	* the last successful open link will be used to retrieve the error message from the SQL server.
	*
    * @return String information
	*/
	function database_error(){
		return null;
	}
	/**
	* return the number of rows in the record set
	*/
	function database_num_rows($link){
		return null;
	}
	/**
	* execute a database query
	*/
	function database_query($sql){
		return null;
	}
	/**
	* fetch the next recordset as an array
	*
	* @param Resource pointer to the recordset
	* @return Array Associative & Indexed array of results
	*/
	function database_fetch_array($link){
		return null;
	}

	/**
	* seek a certain record
	*/
	function database_data_seek($link,$row){
		return null;
	}

	/**
	* Get a result row as an enumerated array
	*
    * fetches one row of data from the result associated with the specified result identifier. The row is returned as an array. Each result column is stored in an array offset, starting at offset 0.
    * Subsequent call to mysql_fetch_row() would return the next row in the result set, or FALSE if there are no more rows.
    *
    * <b>NOT</b> recommended , use {@see database::database_fetch_array() database_fetch_array } instead
    * @uses $this->call_command("DB_FETCH_ROW", Array($result))
    *
    * @param Resource pointer to the recordset
	* @return Array indexed array of results
	*/
	function database_fetch_row($result){
		return null;
	}
	
	/**
	* free up a record set
	*/
	function database_free_result($result){
		return null;
	}
	
	/**
	*  close the database connection
	*/
	function database_close($connection){
		return null;
	}
	
	/**
	* prepare an sql Statement
	*/
	function database_prepare_sql_parameter($str){
	    return $str;
	}
	
	/**
    * check if a table exists already
	*
	* @param String the name of the table you are searching for
	* @return Bool table found
    */
	function table_exists($str){
		return 0;
	}
	/**
    * Make a series of changes to the SQL before executing
	*
	* <strong>NOTE::</strong> most <strong>SQL</strong> statements will have no changes to them 
	* once in a while a change will be required Date formats are one of these changes
	*
	* @access private
	*
	* @param String the sql statement to test
	* @return String the updated string
    */
	function convert_to_SQL($sql){
		return $sql;
	}
}
?>