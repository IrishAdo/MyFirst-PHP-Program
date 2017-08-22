<?php
/**
* @company libertas-Solutions
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.database_mysql.php
* @date 09 Oct 2002
*/
/**
* This is the module that will allow the connection to a database
* server running MySql.
*/
require_once dirname(__FILE__)."/libertas.database.php";
class database_mysql extends database{
	/**#@+
	* Class Variables
    * @var string
	*/
	var $module_load_type		= "__SYSTEM__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name_label		= "Database Connector (MySQL)";
	var $module_name			= "database_mysql";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 		= '$Revision: 1.14 $';

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
	function database_connect($host,$username, $password){
		$this->connection = @mysql_connect("$host", "$username", "$password");
		return $this->connection;
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
    function database_select_db($db,$connection=null){
		if ($connection==null){
			$connection=$this->connection;
		}
		$this->database=$db;
		$this->result_list = array();
		return @mysql_select_db("$db",$connection);
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
		return @mysql_error();
	}
    /**
	* return the number of rows in the record set
    *
    * @param Resource the ptr to the recordSet returned by a DB_QUERY call
    * @return Integer number of rows in the database recordset contained in the Link to
	*/
	function database_num_rows($link){
		return @mysql_num_rows($link);
	}
    /**
	* execute a database query
    *
	* Executes a prescribed sql query, if the $extra parameter is supplied the following
    * updates will happen to the sql
    *
    * 1. if action == "LIMIT" then get the "seek" position and the "num_results" value
    *
    * @param String the sql statement for this query
    * @param Array extra setting for sql statement see description
    * @param Connection
	*
    * @return Resource pointer to the database recordset
	*/
	function database_query($sql,$extra = Array(),$connection=null){
		if ($connection==null){
			$connection=$this->connection;
		}
//		print $sql;
//		print "<p>".$this->convert_to_SQL($sql)."</p>";
		$action			= $this->check_parameters($extra,"action");
		$seek			= $this->check_parameters($extra,"seek",0);
		$num_results	= $this->check_parameters($extra,"num_results",10);
		if ($action=="LIMIT"){
			if($seek==0){
				$sql .=" LIMIT $seek, $num_results";
			} else {
				$sql .=" LIMIT $num_results";
			}
		}
//		print "[$sql]\n\n\n";
		$i = count($this->result_list);
		//print "<li>".__FILE__."@".__LINE__."<p>".time()." <pre>".$this->convert_to_SQL($sql)."</pre></p></li>";
		$this->result_list[$i] = mysql_query($this->convert_to_SQL($sql),$connection);
		return $this->result_list[$i];
	}
	/**
	* fetch the next recordset as an array
	*
	* @param Resource pointer to the recordset
	* @return Array Associative & Indexed array of results
	*/
	function database_fetch_array($link){
		if ($link)
			return @mysql_fetch_array($link);
		else
			return null;
	}

	/**
	* seek a certain record in the recordset
	* 
	* Jump to a record position via this command allows you to jump to record 15000 with outh haveing to read records 1 to 14999
	* 
	* @param Resource which sql statements results are we working with
	* @param Integer which row inthe recordset we are to jump to
	* @return ???
	*/
	function database_data_seek($link,$row){
		return @mysql_data_seek($link,$row);
	}

	function database_fetch_row($result){
		return @mysql_fetch_row($result);
	}

	/**
	* free up a record set
	*
	* @param Resource
	*/
	function database_free_result($result){
		return @mysql_free_result($result);
	}
	
	/**
	* close the database connection
	*
	* takes a connection string and attemptes to make sure that any queries opened on this connection are closed eventually
	* @param Connection the connection returned by the open database command
	*/
	function database_close($connection=null){
		if ($connection==null){
			$connection=$this->connection;
		}
		for($i=0;$i<count($this->result_list);$i++){
			@mysql_free_result($this->result_list[$i]);
		}
		return mysql_close($connection);
	}
	
	/**
	* create a database table in a My SQL Server
	*
	* @param String the table name
	* @param Array the field definitions for this table
	* @param String the primary field name
	* @return Integer return 1 when finished
	*/
	function create_table($table,$fields,$primary){
		$sql="CREATE TABLE $table (\n";
		$indexes ="";
		for($index=0,$length=count($fields);$index<$length;$index++){
			if ($index>0){
				$sql.=",\n";
			}
			$sql.=$fields[$index][0];
			$int = false;
			$isblob= false;
			switch (strtolower($fields[$index][1])){
				case "signed integer":
					$sql.=" bigint(20) ";
					$int = true;
					break;
				case "unsigned integer":
					$int = true;
					$sql.=" bigint(20) unsigned ";
					if($fields[$index][3]=="default ''"){
						$fields[$index][3]="";
					}
					break;
				case "unsigned small integer":
					$int = true;
					$sql.=" tinyint(3) unsigned ";
					if($fields[$index][3]=="default ''"){
						$fields[$index][3]="";
					}
					break;
				case "integer":
					$sql.=" bigint(20) ";
					$int = true;
					if($fields[$index][3]=="default ''"){
						$fields[$index][3]="";
					}
					break;
				case "small integer":
					$sql.=" tinyint(3) ";
					$int = true;
					if($fields[$index][3]=="default ''"){
						$fields[$index][3]="";
					}
					break;
				case "text":
					$sql.=" LONGTEXT ";
					$isblob = true;
					if($fields[$index][3]=="default ''"){
						$fields[$index][3]="";
					}
					break;
				case "blob":
					$sql.=" LONGBLOB ";
					$isblob = true;
					if($fields[$index][3]=="default ''"){
						$fields[$index][3]="";
					}
					break;
				case "datetime":
					$sql.=" DATETIME ";
					if($fields[$index][3]=="default ''"){
						$fields[$index][3]="";
					}
					break;
				case "double":
					$sql.=" DOUBLE PRECISION ";
					break;
				default:
					$sql.=" ".$fields[$index][1]." ";
			}
			$sql.=$fields[$index][2]." ";
			// if not a blob && not a int field then ok
			// if blob or int then add index 3 if != "default ''"
			//print "<li>".print_R($fields[$index],true)."((!".print_R($int,true)." && !$isblob ) || ((($int||$isblob) && ".$fields[$index][3]."!=\"default ''\")))";
			$sql.=$fields[$index][3]."";
			$key = $this->check_parameters($fields[$index],4,"__NOT_FOUND__");
			if ($key!="__NOT_FOUND__" && $key != ""){
				$indexes .=", ";
				$indexes .=" KEY ".$fields[$index][0]." (".$fields[$index][0].")";
			}
		}
		if ($primary!=""){
			$sql.=",\nPRIMARY KEY  ($primary)";
		}
		
		$sql.="$indexes );";
		//print "<li>$sql";
		$this->call_command("DB_QUERY",array($sql));
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Create Table",__LINE__,"$sql"));
		}
		return 1;
	}
	
	/**
    * check if a table exists already
	*
	* @param String the name of the table you are searching for
	* @return Bool table found
    */
	function table_exists($tableName){
		$tables = array();
		$tablesResult = mysql_list_tables($this->database, $this->connection);
		while ($row = mysql_fetch_row($tablesResult)) 
			$tables[] = $row[0];
		return(in_array($tableName, $tables));
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
		$sql = str_replace(
			Array("%_%"),
			Array("%\\_%"),
			$sql);
		return $sql;
	}

}
?>