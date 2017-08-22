<?php
/**
* @company libertas-Solutions
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.database_mssql.php
* @date 09 Oct 2002
*/
/**
* This module extends the database interface module with the commands written to talk with
* Microsoft SQl Server version 7.0 and 2000
*/
require_once dirname(__FILE__)."/libertas.database.php";
class database_mssql extends database{
	/**#@+
	* Class Variables
    * @var String
    * @acecss private
	*/
	var $module_load_type			= "__SYSTEM__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name	        	= "database_mssql";
	var $module_name_label	        = "Database Connector (MS-SQL)";
	var $module_modify	 		    = '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.11 $';
	var $module_command		        = "DB_"; 		// all commands specifically for this module will start with this token

    /**
	* connect to a database
    *
    * Stores the current connection in the $this->connection variable
    *
    * @param $host string the location of the host file
    * @param $username string the username of the user able to connect to the database
    * @param $password string the password of the user able to connect to the database
    *
    * @return Connection
	*/
	function database_connect($host,$USERNAME, $password){
		$this->connection = @mssql_connect("$host", "$USERNAME", "$password");
		return $this->connection;
	}


	/**
	* select the database to connect to
	*/
	function database_select_db($db,$connection=null){
		if ($connection==null){
			$connection=$this->connection;
		}
		$this->database=$db;
		$this->result_list = array();
		return @mssql_select_db("$db");
	}
	
	/**
	* database Error
	*/
	function database_error(){
		return null;
	}
	/**
	* return the number of rows in the record set
	*/
	function database_num_rows($link){
		return @mssql_num_rows($link);
	}
	/**
	* execute a database query
	*/
	function database_query($sql,$extra,$connection=null){
		if ($connection==null){
			$connection=$this->connection;
		}
//		print "<p>".$this->convert_to_SQL($sql)."</p>";
		$action			= $this->check_parameters($extra,"action");
		if ($action=="LIMIT"){
			$seek			= $this->check_parameters($extra,"seek",0);
			$num_results	= $this->check_parameters($extra,"num_results",10);
			$sql = trim($sql);
			$pos = strpos($sql,"select ");
			if($pos===false){
				// can't find the select at the beginning of the statement then exec original
			}else {
				$sql = "select TOP $num_results ". substr($sql,$pos+7);
			}
			$i = count($this->result_list);
			$this->result_list[$i] = @mssql_query($this->convert_to_SQL($sql));
			if ($seek>0){
				mssql_data_seek($this->result_list[$i],$seek);
			}
			return $this->result_list[$i];
			
		} else {
			$i = count($this->result_list);
			$this->result_list[$i] = @mssql_query($this->convert_to_SQL($sql));
			return $this->result_list[$i];
		}
	}
	/**
	* fetch an array
	*/
	function database_fetch_array($link){
		return @mssql_fetch_array($link);
	}

	/**
	* seek a certain record
	*/
	function database_data_seek($link,$row){
		return @mssql_data_seek($link,$row);
	}

	function database_fetch_row($result_poll_id){
		return @mssql_fetch_row($id);
	}

	/**
	* free up a record set
	*/
	function database_free_result($result){
		return @mssql_free_result($result);
	}

	/**
	*  close the database connection
	*/
	function database_close($connection){
		if ($connection==null){
			$connection=$this->connection;
		}
		for($i=0;$i<count($this->result_list);$i++){
			@mssql_free_result($this->result_list[$i]);
		}
		return mssql_close($connection);
	}
	

	function create_table($table,$fields,$primary){
$sql = "CREATE TABLE dbo.$table (\n";
		for($index=0,$length=count($fields);$index<$length;$index++){
			if ($index>0){
				$sql.=",";
			}
			$sql.=$fields[$index][0];
			switch ($fields[$index][1]){
				case "unsigned integer":
					$sql.=" bigint ";
					break;
				case "double":
					$sql.=" real ";
					break;
				case "unsigned small integer":
					$sql.=" tinyint ";
					break;
				case "integer":
					$sql.=" bigint ";
					break;
				case "small integer":
					$sql.=" tinyint ";
					break;
				case "text":
					$sql.=" text ";
					break;
				case "blob":
					$sql.=" blob ";
					break;
				default:
					$sql.=" ".$fields[$index][1]." ";
			}
			$sql.=$fields[$index][2]." ";
			if ($fields[$index][3]=="default 'null'"){
			}else if ($fields[$index][3]=="auto_increment"){
				$sql .= " IDENTITY PRIMARY KEY";
			}else{
				$sql .= $fields[$index][3];
			}
		}
		$sql.=")";
		$this->call_command("DB_QUERY",array($sql));
		
		return 1;
	}

	function table_exists($tableName){
		$tables = array();
		$sql = "SELECT name FROM sysobjects WHERE (id = OBJECT_ID(N'[dbo].[$tableName]')) AND (OBJECTPROPERTY(id, N'IsUserTable') = 1)";
		$tablesResult = $this->database_query($sql);
		while ($row = $this->database_fetch_array($tablesResult)) 
			$tables[] = $row["name"];
		return(in_array($tableName, $tables));
	}
	function convert_to_SQL($sql){
		$sql = str_replace(
			Array("0000/00/00 00:00:00","dayofmonth("),
			Array("1900/01/01","day("),
			$sql);
//		print "<p>$sql</p>";
		return $sql;
	}
}
?>