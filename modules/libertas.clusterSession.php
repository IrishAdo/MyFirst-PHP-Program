<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.clusterSession.php
* $Revision: 1.5 $, $Date: 2005/02/08 17:01:10 $
*/
/** 
* this functionality is used to override the basic session management on the server
* these functions will be called instead of the file based session code
*
* NOTE you can access debug information for these functions with 
* <code>
*	print_r($GLOBALS["SESSION_SCRITP"]);
* </code>
*/

$GLOBALS["SESSION_SCRIPT"] = Array();
/**
* session open  set timeout of session from Engine
*/
function sessao_open($aSavaPath, $aSessionName){
	$minutes = $GLOBALS["engine"]->call_command("SYSPREFS_EXTRACT_SYSTEM_PREFERENCE",Array("sp_time_out_minutes"));
	sessao_gc($minutes * 60 );
	$GLOBALS["SESSION_SCRIPT"][count($GLOBALS["SESSION_SCRIPT"])] ="open";
	return true;
}

/**
* close the session 
*/
function sessao_close(){
      return true;
}

/**
* read the session infromation from the database
*/
function sessao_read( $aKey ){
	$ipaddress	= $_SERVER["REMOTE_ADDR"];
	$now 		= $GLOBALS["engine"]->libertasGetDate("Y/m/d H:i:s");
	$sql 		= "SELECT ipaddress, DataValue FROM sessions WHERE SessionID='$aKey' and Client = ".$GLOBALS["engine"]->client_identifier;
	$result 	= $GLOBALS["engine"]->call_command("DB_QUERY",Array($sql));
	$GLOBALS["SESSION_SCRIPT"][count($GLOBALS["SESSION_SCRIPT"])] = "sessao_read :: " . $sql;
    if($GLOBALS["engine"]->call_command("DB_NUM_ROWS",Array($result)) == 1) {
		$r = $GLOBALS["engine"]->call_command("DB_FETCH_ARRAY",Array($result));
	    if($r["ipaddress"] == $ipaddress){
		    return urldecode($r['DataValue']);
		} else {
			$sql ="delete from sessions WHERE SessionID='$aKey' and Client = ".$GLOBALS["engine"]->client_identifier;
			$GLOBALS["SESSION_SCRIPT"][count($GLOBALS["SESSION_SCRIPT"])] = $sql;
			$GLOBALS["engine"]->call_command("DB_QUERY",Array($sql));
			session_id(uniqid(rand()));
			return false;
		}
    } else {
		if (!empty($_SESSION["SESSION_USER_IDENTIFIER"])){
			$user_id	= $_SESSION["SESSION_USER_IDENTIFIER"];
		} else {
			$user_id	= 0;
		}
        $GLOBALS["engine"]->call_command("DB_QUERY",Array("INSERT INTO sessions (SessionID, LastUpdated, DataValue, Client, ipaddress, user_id) VALUES ('$aKey', '$now', '', '".$GLOBALS["engine"]->client_identifier."', '$ipaddress', '$user_id');"));
        return false;
    }
}

/**
* save the session information 
*/
function sessao_write( $aKey, $aVal ){
	$aVal 		= urlencode( $aVal );
	$now 		= $GLOBALS["engine"]->libertasGetDate("Y/m/d H:i:s");
		if (!empty($_SESSION["SESSION_USER_IDENTIFIER"])){
			$user_id	= $_SESSION["SESSION_USER_IDENTIFIER"];
		} else {
			$user_id	= 0;
		}

	$sql 		= "UPDATE sessions SET user_id='$user_id', DataValue = '$aVal', LastUpdated = '$now' WHERE SessionID = '$aKey' and Client = ".$GLOBALS["engine"]->client_identifier;
	$GLOBALS["SESSION_SCRIPT"][count($GLOBALS["SESSION_SCRIPT"])] = $sql;
	$GLOBALS["engine"]->call_command("DB_QUERY",Array($sql));
    return true;
}

/**
* destroy the session information
*/
function sessao_destroy( $aKey ){
	$GLOBALS["engine"]->call_command("DB_QUERY",Array("DELETE FROM sessions WHERE SessionID = '$aKey' and Client = ".$GLOBALS["engine"]->client_identifier));
    return true;
}

/**
* garbage collection
*/
function sessao_gc( $aMaxLifeTime ){
	$now = $GLOBALS["engine"]->libertasGetDate("Y/m/d H:i:s");
	$GLOBALS["engine"]->call_command("DB_QUERY",Array("DELETE FROM sessions WHERE (UNIX_TIMESTAMP('$now') - UNIX_TIMESTAMP(LastUpdated)) > $aMaxLifeTime and Client = ".$GLOBALS["engine"]->client_identifier));
    return true;
}
/**
* setup
*/
$GLOBALS["SESSION_SCRIPT"][count($GLOBALS["SESSION_SCRIPT"])] = "defining....";
session_set_save_handler("sessao_open", "sessao_close", "sessao_read", "sessao_write", "sessao_destroy", "sessao_gc");
$GLOBALS["SESSION_SCRIPT"][count($GLOBALS["SESSION_SCRIPT"])] = "defined";
?>