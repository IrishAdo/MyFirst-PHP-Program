<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.metadata_administration.php
* @date 31 July 2004
* $Revision: 1.17 $, $Date: 2005/02/08 17:01:12 $
*/
/**
* Notes from discussion with neil
* 
* Metadata to be collected
*	* represents store multiple records
*	EX. Type {A: Automatic, M: Manual}
* 
*	Field				|Ex.Type|	Field Type
*------------------------------------------------------------------------------------
* 	Title				|	A	|	String
* 	Keywords*			|	M	|	Relationship table
* 	Description			|	A	|	Memo
* 	Date				|		|	
* 		.published		|	A	|	Date stamp
* 		.available		|	AM	|	Date stamp
* 		.review			|	AM	|	Date stamp
* 		.remove			|	AM	|	Date stamp
* 		.update			|	A	|	Date stamp
* 		.created		|	A	|	Date stamp
*	Creator * 			|	M	|	(come back to)
*	Contributer * 		|	M	|	(come back to)
*	Audience *			|	M	|	Lookup table with other option 
*	Author				|	A	|	User Identifier
*	Alternative Title	|	M	|	String
*	Category *			|	AM	|	(Use existing tech)
*	Coverage *			|	M	|	
*		.place			|	M	|	Lookup table with other option 
*		.postcode		|	M	|	String
*		.time			|	M	|	Date Start and Finish
*	Format				|	A	|	Automatically defined (HTML, PDF, ....)
*	Publisher			|	M	|	Lookup table with other option 
*	Rights				|	M	|	Selection of check boxes
*	CopyRight			|	M	|	Lookup table with other option 
*	Source				|	M	|	Memo
*	Identifier			|	A	|	Metadata record Identifier its completely unique
*=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
*	Site Wide Settings
*		Availble		|	Checkbox
*		Required		|	Checkbox
*		Hidden			|	Checkbox
*		Default Value	|	String	
*	---------------------------------
*	Dates
*		Review			|	Dropdown (1 month, 2months ....)
*		Remove			|	Dropdown
*	---------------------------------
*	Number of keywords
*	Microsoft SmartTags enable / disable
*	---------------------------------
*	
*	Multiple records are stored in a relationship table and a lookup table
*	+----------------+     +--------------+     +--------+
*	| MetaData Table | <-> | Relationship | <-> | Lookup |
*	+----------------+     +--------------+     +--------+
* 
*
*	To do 
*		Add site wide settings to Installer

*/
define ("MANAGEMENT_METADATA","Manage Metadata");
class metadata_admin extends module{
	/*************************************************************************************************************************
	*  Class Variables (generic
	*************************************************************************************************************************/
	var $module_grouping			= "";
	var $module_name_label			= "MetaData (Administration)";
	var $module_name				= "metadata_admin";
	var $module_admin				= "1";
	var $module_command				= "METADATAADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "METADATAADMIN_";
	var $module_label				= "MANAGEMENT_METADATA";
	var $module_modify		 		= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.17 $';
	var $module_creation 			= "26/02/2004";
	
	/*************************************************************************************************************************
	*                                M E T A D A T A   S E T U P   F U N C T I O N S
	*************************************************************************************************************************/
	
	var $metadata_fields = Array(
		Array("value"=> "", "key" => "md_title",			"label" => "Title",								"type" => "standard", "default" => "__NOT_FOUND__"), 
		Array("value"=> "", "key" => "md_description",		"label" => "Description",						"type" => "standard", "default" => "__NOT_FOUND__"), 
		Array("value"=> "", "key" => "md_date_available",	"label" => "Date available from",				"type" => "standard", "default" => "__NOT_FOUND__"), 
		Array("value"=> "", "key" => "md_date_remove",		"label" => "Date available to",					"type" => "standard", "default" => "__NOT_FOUND__"), 
		Array("value"=> "", "key" => "md_date_review",		"label" => "Date to be reviewed",				"type" => "standard", "default" => "__NOT_FOUND__"),

		Array("value"=> "", "key" => "md_price",			"label" => "Price of item",						"type" => "ecommerce", "default" => "0"), 
		Array("value"=> "", "key" => "md_vat",				"label" => "Charge Vat",						"type" => "ecommerce", "default" => "0"), 
		Array("value"=> "", "key" => "md_discount",			"label" => "Discount Available",				"type" => "ecommerce", "default" => "0"), 
		Array("value"=> "", "key" => "md_weight",			"label" => "Weight of item",					"type" => "ecommerce", "default" => "0"), 
		Array("value"=> "", "key" => "md_quantity",			"label" => "Quantity in stock",					"type" => "ecommerce", "default" => "-1"),
		Array("value"=> "", "key" => "md_canbuy",			"label" => "Can this item be added to a basket","type" => "ecommerce", "default" => "0") 
	);
	
	/* meta data fields for email admin portion starts (Added by Muhammad Imran Mirza)*/
	var $metadata_fields_email_admin = Array(
		Array("value"=> "", "key" => "md_surname",			"label" => "Surname",								"type" => "standard", "default" => "__NOT_FOUND__"), 
		Array("value"=> "", "key" => "md_first_name",		"label" => "First Name",						"type" => "standard", "default" => "__NOT_FOUND__"), 
		Array("value"=> "", "key" => "md_email",		"label" => "Email",						"type" => "standard", "default" => "__NOT_FOUND__"), 
		Array("value"=> "", "key" => "md_region",		"label" => "Region",						"type" => "standard", "default" => "__NOT_FOUND__"), 
	);
	/* meta data fields for email admin portion ends (Added By Muhammad Imran Mirza)*/
	
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
		if (strpos($user_command, $this->module_command)===0){
			/*************************************************************************************************************************
			* Generic module functions
			*************************************************************************************************************************/
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
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/*************************************************************************************************************************
			* Create table function allow access if in install mode
			*************************************************************************************************************************/
			if ($this->parent->module_type=="install"){
				if ($user_command==$this->module_command."CREATE_TABLE"){
					return $this->create_table();
				}
			}
			/*************************************************************************************************************************
			* Administration Module commands
			*************************************************************************************************************************/
			if ($this->admin_access==1){
				/*************************************************************************************************************************
				* modif (add/edit) a metadata record
				*************************************************************************************************************************/
				if ($user_command==$this->module_command."CLONE"){
					return $this->metadata_clone($parameter_list);
				}
				if ($user_command==$this->module_command."TIDY_KEYWORDS"){
					return $this->metadata_tidy_keywords($parameter_list);
				}
				if ($user_command==$this->module_command."GENERATE_XML"){
					return $this->metadata_generate_xml($parameter_list);
				}
				
			}
			/*************************************************************************************************************************
            * functions available to both presentation and admin
            *************************************************************************************************************************/
			if ($user_command==$this->module_command."MODIFY"){
				return $this->metadata_modify($parameter_list);
			}
			if ($user_command==$this->module_command."CACHE"){
				return $this->metadata_cache($parameter_list);
			}
			if ($user_command==$this->module_command."GET_SQL_DATE_PARTS"){
				return $this->get_sql_date_statement($parameter_list);
			}
			if ($user_command==$this->module_command."GET_FIELDLIST"){
				return $this->get_fieldlist($parameter_list);
			}
			if ($user_command==$this->module_command."GET_FIELDLIST_EMAIL_ADMIN"){
				return $this->get_fieldlist_email_admin($parameter_list);
			}
			
		}else{
			return "";// wrong command sent to system
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
		/*************************************************************************************************************************
		* request the client identifier once we use this variable often
		*************************************************************************************************************************/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* define the list of Editors in this module and define them as empty
		*/
		$this->editor_configurations = Array();
		/*************************************************************************************************************************
		* lock down the access
		*************************************************************************************************************************/
		$this->admin_access						= 0; // access to the admin functions
		/*************************************************************************************************************************
		* load the required locale
		*************************************************************************************************************************/
		$this->load_locale("metadata_admin");
		/*************************************************************************************************************************
		* define the list of Editors in this module and define them as empty
		*************************************************************************************************************************/
		$this->editor_configurations = Array();
		/*************************************************************************************************************************
        * set up defaults
        *************************************************************************************************************************/
		$this->module_admin_options = array();
		/*************************************************************************************************************************
		* request the page size 
		*************************************************************************************************************************/
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		/*************************************************************************************************************************
		* define the admin access that this user has.
		*************************************************************************************************************************/
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		if ($this->parent->module_type=="admin"){
			/* if you are logged into the admin of the site then you have access to these functions */
			$this->admin_access					= 1; 
		}
		return 1;
	}
	/*************************************************************************************************************************
	*                         			M E T A D A T A   A D M I N   F U N C T I O N S
	*************************************************************************************************************************/

	/*************************************************************************************************************************
	* function produces abstract structure of modules db structure
	*
    * used to generate the table structure required abstract function that passes an abstract
	* representation of the desired table structure to the proper database module which will
	* interpet the abstract and convert it into a valid SQL Create table structure.
	*
	* @return Array list of abstract table definitions for this module
	*************************************************************************************************************************/
	function create_table(){
		$tables = array();
		/*************************************************************************************************************************
		* Table structure for table 'metadata_details'
		*
		* Payment details for different paysysttems differ greatly this table holds the key pairs of variable/value
		*************************************************************************************************************************/
		$fields = array(
			array("md_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("md_link_id"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("md_link_group_id"	,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("md_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("md_uri"				,"varchar (255)"			,"NOT NULL"	,"default ''"),
			array("md_title"			,"varchar (255)"			,"NOT NULL"	,"default ''"),
			array("md_module"			,"varchar (255)"			,"NOT NULL"	,"default ''"),
			array("md_description"		,"text"						,"NOT NULL"	,"default ''"),
			/*************************************************************************************************************************
			* shopping cart information
			*************************************************************************************************************************/
			array("md_price"			,"double"					,"NOT NULL"	,"default '0'"),
			array("md_discount"			,"double"					,"NOT NULL"	,"default '0'"),
			array("md_weight"			,"double"					,"NOT NULL"	,"default '0'"), // in kg
			array("md_vat"				,"small integer"			,"NOT NULL"	,"default '0'"), // charge vat (1,0) true/false
			array("md_quantity"			,"signed integer"			,"NOT NULL"	,"default '-1'"),
			array("md_canbuy"			,"small integer"			,"NOT NULL"	,"default '0'"), // can buy item (1,0) true/false
			/*************************************************************************************************************************
			* metadata Date information
			* 
			* creation date  :: is the creation date of the content
			* modified date  :: is the date the content was last modified.
			* review date    :: is the date that this content has to be reviewed.
			* publish date	 :: is the date that this content was published to the site.
			* remove date    :: is the date that this content should be automatically removed from the site.
			* available date :: is the date that this content will be avaliable on the site can be equal to or greater than the publish date 
			*************************************************************************************************************************/
			array("md_date_creation"		,"datetime"			,""			,"default ''"),
			array("md_date_modified"		,"datetime"			,""			,"default ''"),
			array("md_date_review"			,"datetime"			,""			,"default ''"),
			array("md_date_publish"			,"datetime"			,""			,"default ''"),
			array("md_date_remove"			,"datetime"			,""			,"default ''"),
			array("md_date_available"		,"datetime"			,""			,"default ''")
		);
		$primary ="md_identifier";
		$tables[count($tables)] = array("metadata_details", $fields, $primary);
		/*************************************************************************************************************************
		* Table structure for table 'metadata_properties'
		*
		* Payment details for different paysysttems differ greatly this table holds the key pairs of variable/value
		* the pap_identifier is mapped to the pad_identifier field
		*************************************************************************************************************************/
		$fields = array(
			array("mdp_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'"), // link to Metadata record
			array("mdp_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("mdp_property"		,"varchar (255)"			,"NOT NULL"	,"default ''","key"),
			array("mdp_value"			,"varchar (255)"			,"NOT NULL"	,"default ''","key")
		);
		$primary ="";
		$tables[count($tables)] = array("metadata_properties", $fields, $primary);
		/*************************************************************************************************************************
		* Table structure for table 'metadata_keywords'
		*
		* keywords are stored once for all clients
		*************************************************************************************************************************/
		$fields = array(
			array("mdk_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'"), // primary key
			array("mdk_value"			,"varchar (255)"			,"NOT NULL"	,"default ''","key")
		);
		$primary ="";
		$tables[count($tables)] = array("metadata_keyword", $fields, $primary);
		/*************************************************************************************************************************
		* Table structure for table 'metadata_keywords_relationship'
		*
		* keywords are stored once for all clients
		*************************************************************************************************************************/
		$fields = array(
			array("mdkr_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),	// metadata record
			array("mdkr_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"), 	// client owner
			array("mdkr_keyword"		,"unsigned integer"			,"NOT NULL"	,"default '0'"), 	// client owner
			array("mdkr_count"			,"unsigned integer"			,"NOT NULL"	,"default '0'")		// keyword identifier
		);
		$primary ="";
		$tables[count($tables)] = array("metadata_keyword_relationship", $fields, $primary);
		
		return $tables;
	}
	
	/*************************************************************************************************************************
	* a function that will store metadata information on an object
	* 
    * this function will require as a mimimum the following
	* 
	* 1. Identifier of parent object
	* 2. Module name parent object belongs to
	* 3. Title/Label of parent object
	* 
	* it will do the following actions
	* 
	* 1. it will strip any and all html from the description
	* 2. if no keywords are supplied it will add the title and the description together and generate keywords from that data
	* 3. it will crop the description to be no longer than 255 characters
	* 
	* 
	* @param Array list of keys required to maintain metadata info
	*				"identifier"	=> identifier of object
	*				"module"		=> name of module associated with object
	*				"title"			=> label of object 
	*				"command"		=> Action to take
	*				"description"	=> description to be used for generating keywords(if not supplied) cropped to 250 characters
	*				"keywords"		=> Array of keyword/phrases
	*				"extra_..."		=> extra field data to save 
	*				"date_..."		=> list of dates for usage
	* @return Integer the identifier of the metadata object
	*************************************************************************************************************************/
	function metadata_modify($parameters){
		/**
		* get the primary fields for metadata
		*/
		$md_uri ="";
		$md_link_id			= $this->check_parameters($parameters,"identifier",-1);
		$md_link_group_id	= $this->check_parameters($parameters,"md_link_group_id",$md_link_id);
		$md_module			= $this->check_parameters($parameters,"module");		
		if (($md_link_id==-1) || ($md_module==""))
			return ""; // fail we need an module for all metadata (it has to link to something)
		$md_identifier		= -1;
		$md_keywords		= "__NOT_FOUND__";
		$command			= $this->check_parameters($parameters,"command","ADD");
		$md_fields			= $this->check_parameters($parameters,"fields","__NOT_FOUND__");
		$len = count($this->metadata_fields);
		$mdt = -1;
		$mdd = -1;
		if ($md_fields != "__NOT_FOUND__"){ // import tool uses this import method
			for($i=0;$i<$len;$i++){
				$this->metadata_fields[$i]["value"] = $this->check_parameters($md_fields, $this->metadata_fields[$i]["key"], $this->metadata_fields[$i]["default"]);
				if($this->metadata_fields[$i]["key"]=="md_title"){
					$mdt = $i;
				}
				if($this->metadata_fields[$i]["key"]=="md_description"){
					$mdd = $i;
				}
			}
			if($mdt!=-1 && $this->metadata_fields[$mdt]["value"]==""){
				$this->metadata_fields[$mdt]["value"]	= $this->check_parameters($parameters,"title");
			}
			if($mdd!=-1 && $this->metadata_fields[$mdt]["value"]==""){
				$this->metadata_fields[$mdd]["value"]	= $this->check_parameters($parameters,"description");
			}
		} else {
			for($i=0;$i<$len;$i++){
				$this->metadata_fields[$i]["value"] = $this->check_parameters($parameters, $this->metadata_fields[$i]["key"], $this->metadata_fields[$i]["default"]);
				if($this->metadata_fields[$i]["key"]=="md_title"){
					$mdt = $i;
				}
				if($this->metadata_fields[$i]["key"]=="md_description"){
					$mdd = $i;
				}
			}
			if($mdt!=-1 && $this->metadata_fields[$mdt]["value"]==""){
				$this->metadata_fields[$mdt]["value"]	= htmlentities(strip_tags(html_entity_decode($this->check_parameters($parameters,"title"))));
			}
			if($mdd!=-1 && $this->metadata_fields[$mdt]["value"]==""){
				$this->metadata_fields[$mdd]["value"]	= $this->check_parameters($parameters,"description");
			}
		}
		$md_title			= substr(htmlentities(strip_tags(html_entity_decode($this->metadata_fields[$mdt]["value"]))),0,255);
		$md_description		= substr(htmlentities(strip_tags(html_entity_decode($this->metadata_fields[$mdd]["value"]))),0,255);
		
		$longDescription		= $this->check_parameters($parameters,"longDescription"		,"$md_title $md_description");
		$md_date_publish		= $this->check_parameters($parameters,"md_date_publish"		,"__NOT_FOUND__");
		$md_date_available		= $this->check_parameters($parameters,"md_date_available"	,"__NOT_FOUND__");
		$extra_list		= Array();
		$date_list 		= Array();
		$find_keys		= "";
		$md_uri			= "";
		if($md_keywords=="__NOT_FOUND__"){
			$md_keywords = $this->call_command("UTILS_GENERATE_KEYWORDS", Array($longDescription, 10, "__ARRAY__", ""));
		}
		$md_description = substr($md_description,0,255);
		foreach ($parameters as $key => $value){
			if(substr($key,0,6)=="extra_"){
				if ($find_keys!=""){
					$find_keys .=", ";
				}
				$find_keys .= $key;
				$extra_list[$key] = $value;
			}
			if(substr($key,0,5)=="date_"){
				if ($find_keys!=""){
					$find_keys .=", ";
				}
				$find_keys .= $key;
				$date_list[$key] = $value;
			}
		}
		/**
        * metadata must allways have keywords
        */
		$now = $this->libertasGetDate();
		if ($command=="EDIT"){
			$sql ="select * from metadata_details 
						left outer join metadata_properties on mdp_identifier = md_identifier and mdp_client=md_client
				   where 
					md_link_id		= $md_link_id and
					md_client		= $this->client_identifier and 
					md_module		= '$md_module'";
	        $result  = $this->call_command("DB_QUERY",Array($sql));
			$found = Array(); // get a list of the properties that are defined
			$fc=0; // fc = found counter
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$md_identifier  	= $r["md_identifier"];
				if($md_date_available=="__NOT_FOUND__"){
					$md_date_available  = $r["md_date_available"];
				}
				$mdp_name  = $this->check_parameters($r,"mdp_name");
				if($mdp_name!=""){
					$found[$mdp_name] = $r["mdp_value"];
				}
        		$fc++;
		    }
            $this->call_command("DB_FREE",Array($result));
			/*	To update event md_date_available field portion starts (Modified by Muhammad Imran Mirza ) */
			$md_date_available_already_exist = 0;
			if ($fc > 0){
				$sql = "update metadata_details 
					set 
						 md_uri			= '$md_uri', ";
				for($i=0;$i<$len;$i++){
					$sql .= $this->metadata_fields[$i]["key"]." = '".$this->metadata_fields[$i]["value"]."',";
					if ($this->metadata_fields[$i]["key"] == "md_date_available")
						$md_date_available_already_exist = 1;
					
				}
				$sql .= " md_date_modified = '$now', md_link_group_id = '$md_link_group_id'";
				if($md_date_publish		!= "__NOT_FOUND__"){
					$sql .= ", md_date_publish = '$md_date_publish'";
					if($md_date_available=="0000-00-00 00:00:00" || $md_date_available=="__NOT_FOUND__" && $md_date_available_already_exist == 0){
						$sql .= ", md_date_available = '$md_date_publish'";
					}
				}
				if($md_date_available!="0000-00-00 00:00:00" && $md_date_available!="__NOT_FOUND__" && $md_date_available_already_exist == 0){
					$sql .= ", md_date_available = '$md_date_available'";
				}
			/*	To update event md_date_available field portion ends (Modified by Muhammad Imran Mirza ) */
				$sql .= "
					where 
						md_identifier	= $md_identifier and 
						md_link_id		= $md_link_id and
						md_client		= $this->client_identifier and 
						md_module		= '$md_module'";
				$this->call_command("DB_QUERY",Array($sql));
				foreach ($date_list as $key => $value){
					if ($this->check_parameters($found, $key, "__NOT_FOUND__")=="__NOT_FOUND__"){
						$sql = "insert into metadata_properties (mdp_identifier, mdp_client, mdp_property, mdp_value) values 
									($md_identifier, $this->client_identifier, '$key', '$value')";
					} else {
						$sql = "update metadata_properties set mdp_value = '$value' where 
									mdp_identifier = $md_identifier and
									mdp_client = $this->client_identifier and
									mdp_property = '$key'";
					}
					$this->call_command("DB_QUERY",Array($sql));
				}
				foreach ($extra_list as $key => $value){
					if ($this->check_parameters($found, $key, "__NOT_FOUND__")=="__NOT_FOUND__"){
						$sql = "insert into metadata_properties (mdp_identifier, mdp_client, mdp_property, mdp_value) values 
									($md_identifier, $this->client_identifier, '$key', '$value')";
					} else {
						$sql = "update metadata_properties set mdp_value = '$value' where 
									mdp_identifier = $md_identifier and
									mdp_client = $this->client_identifier and
									mdp_property = '$key'";
					}
					$this->call_command("DB_QUERY",Array($sql));
				}
			} else {
				$command="ADD";
			}
		}
		if ($command=="ADD"){
			$md_identifier = $md_link_id; 
			$sql = "insert into metadata_details (";
			for($i=0;$i<$len;$i++){
				if ($this->metadata_fields[$i]["value"]!="__NOT_FOUND__"){
					$sql .= $this->metadata_fields[$i]["key"].", ";
				}
			}
			/*
			if($md_date_publish		!= "__NOT_FOUND__"){
				$sql .= "md_date_publish ,";
				if($md_date_available=="0000-00-00 00:00:00" || $md_date_available=="__NOT_FOUND__"){
					$sql .= "md_date_available, ";
				}
			}
			if($md_date_available!="0000-00-00 00:00:00" && $md_date_available!="__NOT_FOUND__"){
				$sql .= "md_date_available, ";
			}
			*/
			$sql .= "md_date_creation, md_date_modified, md_identifier, md_link_id, md_client, md_uri, md_module, md_link_group_id) values 
					(";
			for($i=0;$i<$len;$i++){
				if ($this->metadata_fields[$i]["value"]!="__NOT_FOUND__"){
					$sql .= "'".$this->metadata_fields[$i]["value"]."', ";
				}
			}
			/*
			if($md_date_publish		!= "__NOT_FOUND__"){
				$sql .= "'$md_date_publish', ";
				if($md_date_available=="0000-00-00 00:00:00" || $md_date_available=="__NOT_FOUND__"){
					$sql .= "'$md_date_publish', ";
				}
			}
			if($md_date_available!="0000-00-00 00:00:00" && $md_date_available!="__NOT_FOUND__"){
				$sql .= "'$md_date_available', ";
			}
			*/
			$sql .= "'$now', '$now', $md_identifier, $md_link_id, $this->client_identifier, '$md_uri', '$md_module', '$md_link_group_id')";
			$this->call_command("DB_QUERY",Array($sql));
			foreach ($date_list as $key => $value){
				$sql = "insert into metadata_properties (mdp_identifier, mdp_client, md_property, md_value) values 
							($md_identifier, $this->client_identifier, '$key', '$value')";
				$this->call_command("DB_QUERY",Array($sql));
			}
			foreach ($extra_list as $key => $value){
				$sql = "insert into metadata_properties (mdp_identifier, mdp_client, md_property, md_value) values 
							($md_identifier, $this->client_identifier, '$key', '$value')";
				$this->call_command("DB_QUERY",Array($sql));
			}
		} 
		if ($command=="REMOVE"){
			/* 
			* get the metadata record for this information
			*/
			$sql ="select * from metadata_details 
						left outer join metadata_properties on mdp_identifier = md_identifier and mdp_client=md_client
				   where 
					md_link_id		= $md_link_id and
					md_client		= $this->client_identifier and 
					md_module		= '$md_module'";
            $result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$md_identifier  = $r["md_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			$sql = "delete from metadata_details where md_identifier = $md_identifier and md_link_id = $md_link_id and md_client = $this->client_identifier";
			$this->call_command("DB_QUERY",Array($sql));
			$sql = "delete from metadata_properties where mdp_identifier = $md_identifier, mdp_client = $this->client_identifier";
			$this->call_command("DB_QUERY",Array($sql));
			$md_keywords= Array();
		} 
		$this->metadata_modify_keywords(Array("identifier" => $md_identifier, "keywords" => $md_keywords));
		
		return $md_identifier;
	}
	/*************************************************************************************************************************
	* a function that will store and manage the metadata keywords
	* 
    * this function will require as a mimimum the following
	* 
	* 1. Identifier of metadata to attach to
	* 2. array of keywords/counter pairs Array("key" => ... , "count" => ...)
	* 
	* @param Array list of keys required to maintain metadata info
	*				"identifier"	=> identifier of metadata object
	*				"keywords"		=> Array of keyword/counter pairs Array("key" => ... , "count" => ...)
	* @return Integer the identifier of the metadata object keywords are associated with
	*************************************************************************************************************************/
	function metadata_modify_keywords($parameters){
		/**
		* get the primary fields for metadata
		*/
		$md_link_id		= $this->check_parameters($parameters,"identifier",-1);
		$md_keywords	= $this->check_parameters($parameters,"keywords",Array());
//		print_r($md_keywords);
		/**
        *extract ids of existing keywords
        */
		$md_key_list  	= "";
		$max_number_of_keys = count($md_keywords);
		
		for ($index = 0; $index < $max_number_of_keys ; $index++){
			$key = $md_keywords[$index]["key"];
			$md_keywords[$index]["identifier"] = -1;
			if ($md_key_list!=""){
				$md_key_list .= ", ";
			}
			$md_key_list .= "'$key'";
		}
		$sql ="select * from metadata_keyword
			   where 
				mdk_value in ($md_key_list)";
        $result  = $this->call_command("DB_QUERY",Array($sql));
		$found = Array(); // get a list of the properties that are defined
		$fc=0; // fc = found counter
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
          	$mdk_identifier  = $r["mdk_identifier"];
			$mdk_value  	 = $r["mdk_value"];
			for ($index = 0; $index < $max_number_of_keys ; $index++){
				if ($mdk_value == $md_keywords[$index]["key"]){
					$md_keywords[$index]["identifier"] = $mdk_identifier;
					break;
				}
			}
		}
		$this->call_command("DB_FREE",Array($result));
		/**
        * Insert data into relationship table and create new keyword lookup if needed
        */
		$sql = "delete from metadata_keyword_relationship where mdkr_identifier = $md_link_id and mdkr_client = $this->client_identifier)";
        $this->call_command("DB_QUERY",Array($sql));
		for ($index = 0; $index < $max_number_of_keys ; $index++){
			if ($md_keywords[$index]["identifier"] == -1){
				$md_keywords_identifier = $this->getUID();
			} else {
				$md_keywords_identifier = $md_keywords[$index]["identifier"];
			}
			$sql = "insert into metadata_keyword_relationship (mdkr_identifier, mdkr_client, mdkr_keyword, mdkr_count) values ($md_link_id, $this->client_identifier, ".$md_keywords_identifier.", ".$md_keywords[$index]["count"].")";
            $this->call_command("DB_QUERY",Array($sql));
			if ($md_keywords[$index]["identifier"] == -1){
				$md_keywords[$index]["identifier"] = $md_keywords_identifier;
				$sql = "insert into metadata_keyword (mdk_identifier, mdk_value) values (".$md_keywords[$index]["identifier"].", '".$md_keywords[$index]["key"]."')";
                $this->call_command("DB_QUERY",Array($sql));
            }
		}
		/**
        * get rid on unused keywords
        */
		$sql ="select * from metadata_keyword
					left outer join metadata_keyword_relationship on mdkr_keyword = mdk_identifier
				   where 
					mdk_value in ($md_key_list) and mdkr_keyword is null";
        $result  = $this->call_command("DB_QUERY",Array($sql));
		$remove_list="";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if ($remove_list!=""){
				$remove_list .=", ";
			}
			$remove_list	.= $r["mdk_identifier"];
        }
		$this->call_command("DB_FREE",Array($result));
		if($remove_list!=""){
			$sql ="delete from metadata_keyword
				   where 
					mdk_identifier in ($remove_list)";
	        $this->call_command("DB_QUERY",Array($sql));
		}
		
		return $md_link_id;
	}
	/*************************************************************************************************************************
	* a function that will clone one metadata record into another metadata record
	* 
    * this function will require two identifiers Source and Destination and the module it belongs to
	* 
	* @param Array list of keys required to maintain metadata info
	*				"source"		=> identifier of <strong>old</strong> metadata object
	*				"destination"	=> identifier of <strong>new</strong> metadata object
	*				"module"		=> module that metadata object belongs to
	* @return Integer the destination identifier for the new metadata object
	*************************************************************************************************************************/
	function metadata_clone($parameters){
		$source			= $this->check_parameters($parameters,"source",-1);
		$module			= $this->check_parameters($parameters,"module",-1);
		$destination	= $this->check_parameters($parameters,"destination",-1);
		$list_of_metadata_fields = Array(
			"md_identifier"		=> $destination,
			"md_link_id"		=> "",
			"md_uri"			=> "",
			"md_module"			=> "",
			"md_description"	=> "",
			"md_price"			=> "",
			"md_discount"		=> "",
			"md_canbuy"			=> "",
			"md_vat"			=> "",
			"md_title"			=> "",
			"md_quantity"		=> "",
			"md_date_creation"	=> "",
			"md_date_modified"	=> "",
			"md_date_review"	=> "",
			"md_date_publish"	=> "",
			"md_date_remove"	=> "",
			"md_date_available"	=> ""
		);
		$sql 			= "select * from metadata_details where md_link_id = $source and md_module = '$module' and md_client=$this->client_identifier";
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	foreach($list_of_metadata_fields as $key => $value){
				if($list_of_metadata_fields[$key] == ""){
					$list_of_metadata_fields[$key] = $r[$key];
				}
			}
        }
        $this->call_command("DB_FREE",Array($result));
		$list_of_metadata_fields["md_identifier"]	= $this->getUID();
		if($destination==-1){
			$list_of_metadata_fields["md_link_id"]		= $list_of_metadata_fields["md_identifier"];
		} else {
			$list_of_metadata_fields["md_link_id"]		= $destination;
		}
		$fields = "";
		$values = "";
		foreach($list_of_metadata_fields as $key => $value){
			if ($fields!=""){
				$fields .= ", ";
				$values .= ", ";
			}
			$fields .= " $key";
			$values .= " '$value'";
		}
		$sql = "insert into metadata_details 
					(md_client, $fields) 
				values 
					($this->client_identifier, $values)";
        $this->call_command("DB_QUERY",Array($sql));
//		print_r($sql);
//		$this->exitprogram();
		/**
        * get properties 
        */
		$sql = "select * from metadata_properties where mdp_identifier = $source and mdp_client = $this->client_identifier";
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$sql = "insert into metadata_properties (mdp_identifier, mdp_client, md_property, md_value) values 
						(".$list_of_metadata_fields["md_identifier"].", $this->client_identifier, '".$r["md_property"]."', ".$r["md_value"]."')";
			$this->call_command("DB_QUERY",Array($sql));
        }
        $this->call_command("DB_FREE",Array($result));
		/**
        * get keywords (nice bit about this is just copy relationship table)
        */
		$sql = "select * from metadata_keyword_relationship where mdkr_identifier = $source and mdkr_client = $this->client_identifier";
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$sql = "insert into metadata_keyword_relationship (mdkr_identifier, mdkr_client, mdkr_keyword, mdkr_count) values 
						(".$list_of_metadata_fields["md_identifier"].", $this->client_identifier, '".$r["mdkr_keyword"]."', '".$r["mdkr_count"]."')";
			$this->call_command("DB_QUERY",Array($sql));
        }
        $this->call_command("DB_FREE",Array($result));
		
		return $list_of_metadata_fields["md_link_id"];
	}
	
	/*************************************************************************************************************************
	* a function that will remove any non associated keywords
	*************************************************************************************************************************/
	function metadata_tidy_keywords($parameters){
		$md_key_list = "";
		$sql ="select * from metadata_keyword
					left outer join metadata_keyword_relationship on mdkr_keyword = mdk_identifier
				   where 
					mdkr_keyword is null";
        $result  = $this->call_command("DB_QUERY",Array($sql));
		$remove_list="";
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			if ($remove_list!=""){
				$remove_list .=", ";
			}
			$remove_list	.= $r["mdk_identifier"];
        }
		$this->call_command("DB_FREE",Array($result));
		if($remove_list!=""){
			$sql ="delete from metadata_keyword
				   where 
					mdk_identifier in ($remove_list)";
	        $this->call_command("DB_QUERY",Array($sql));
		}
		return "";
	}
	/*************************************************************************************************************************
	* a function that will generate the metadatas xml structure for the record
	*************************************************************************************************************************/
	function metadata_generate_xml($parameters){
		$md_key_list 			= "";
		$lang		 			= "en";
		$md_link_id	 			= $this->check_parameters($parameters,"identifier",-1);
		$md_module	 			= $this->check_parameters($parameters,"module");
		$metadata_keyword_block	= "";
		if (($md_link_id==-1) || ($md_module == "")){
			return 0; // failed
		}
		
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql ="select * from metadata_details where md_client = $this->client_identifier and md_link_id= $md_link_id and md_module='$md_module'";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$identifier				= $r["md_identifier"];
			$md_title				= $r["md_title"];
			//$md_creator			= $r[""];
			//$md_audience			= $r[""];
			//$md_alt_title			= $r[""];
			//$md_subject_category	= $r[""];
			//$md_subject_keywords	= $r[""];
			//$md_subject_project	= $r[""];
			//$md_subject_programme	= $r[""];
			$md_description			= $r["md_description"];
			//$md_publisher			= $r[""];
			//$md_source			= $r[""];
			$md_date_pub_seconds	= strtotime($r["md_date_publish"]);
			$md_date_pub			= $r["md_date_publish"];
			$md_date_create			= $r["md_date_creation"];
			$md_date_review			= $r["md_date_review"];
			$md_date_modify			= $r["md_date_modified"];
			$md_date_remove			= $r["md_date_remove"];
			$md_date_available		= $r["md_date_available"];
			//$md_type				= $r[""];
			$md_unique_identifier	= $r["md_identifier"];
			//$md_relation			= $r[""];
			//$md_coverage			= $r[""];
			//$md_coverage_postcode	= $r[""];
			//$md_time				= $r[""];
			//$md_rights				= $r[""];
			//$md_copyright			= $r[""];
			$lang					= "en";//$r[""];
			/*************************************************************************************************************************
            * ecommerce
            *************************************************************************************************************************/
			$md_price				= $r["md_price"];
			$md_discount			= $r["md_discount"];
			$md_quantity			= $r["md_quantity"];
			$md_wieght				= $r["md_weight"];
			$md_vat					= $r["md_vat"];
			$md_canbuy				= $r["md_canbuy"];
		}
		$this->call_command("DB_FREE",Array($result));
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql = "select * from metadata_keyword inner join metadata_keyword_relationship on mdkr_keyword=mdk_identifier where mdkr_client=$this->client_identifier and mdkr_identifier = $identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$metadata_keyword_block .= '<keyword count="'.$r["mdkr_count"].'"><![CDATA['.$r["mdk_value"].']]></keyword>';
		}
		$this->call_command("DB_FREE",Array($result));
		/*************************************************************************************************************************
        * build XML block
        *************************************************************************************************************************/
		$metadata_block = '
			<metadata>
				<title><![CDATA['.$md_title.']]></title>
				<keywords>'.$metadata_keyword_block .'</keywords>
				<description><![CDATA['.$md_description.']]></description>
				<date refinement="publish" seconds="'.$md_date_pub_seconds.'"><![CDATA['.$md_date_pub.']]></date>
				<date refinement="creation"><![CDATA['.$md_date_create.']]></date>
				<date refinement="review"><![CDATA['.$md_date_review.']]></date>
				<date refinement="modified"><![CDATA['.$md_date_modify.']]></date>
				<date refinement="remove"><![CDATA['.$md_date_remove.']]></date>
				<date refinement="available"><![CDATA['.$md_date_available.']]></date>
				<identifier><![CDATA['.$md_unique_identifier.']]></identifier>
				<format><![CDATA[text/html]]></format>
				<language><![CDATA['.$lang.']]></language>
				<price><![CDATA['.$md_price.']]></price>
				<discount><![CDATA['.$md_discount.']]></discount>
				<quantity><![CDATA['.$md_quantity.']]></quantity>
				<weight><![CDATA['.$md_wieght.']]></weight>
				<vat><![CDATA['.$md_vat.']]></vat>
				<canbuy><![CDATA['.$md_canbuy.']]></canbuy>
			</metadata>';
/*
				<publisher><![CDATA['.$md_publisher.']]></publisher>
				<creator><![CDATA['.$md_creator.']]></creator>
				<audience><![CDATA['.$md_audience.']]></audience>
				<alternative><![CDATA['.$md_alt_title.']]></alternative>
				<subject refinement="category"><![CDATA['.$md_subject_category.']]></subject>
				<subject refinement="keywords"><![CDATA['.$md_subject_keywords.']]></subject>
				<subject refinement="project"><![CDATA['.$md_subject_project.']]></subject>
				<subject refinement="programme"><![CDATA['.$md_subject_programme.']]></subject>
				<source><![CDATA['.$md_source.']]></source>
				<type><![CDATA['.$md_type.']]></type>
				<relation><![CDATA['.$md_relation.']]></relation>
				<coverage><![CDATA['.$md_coverage.']]></coverage>
				<coverage refinement="postcode"><![CDATA['.$md_coverage_postcode.']]></coverage>
				<coverage refinement="time"><![CDATA['.$md_time.']]></coverage>
				<rights><![CDATA['.$md_rights.']]></rights>
				<rights refinement="copyright"><![CDATA['.$md_copyright.']]></rights>
			</metadata>';
*/
		return $metadata_block;
	}
	
	/*************************************************************************************************************************
	* a function that will return an array of sql parts
	*
	* @param Array of parameters keys are "module" eg 'INFORMATION_' and "field" eg 'table.field
	*************************************************************************************************************************/
	function get_sql_date_statement($parameters){
		$module = $this->check_parameters($parameters,"module");
		$field	= $this->check_parameters($parameters,"field"); // table.field 
		$now	= $this->check_parameters($parameters,"now",$this->libertasGetDate()); // date to check
		$sql =Array();
		$sql["join"] = "inner join metadata_details on md_module='$module' and md_link_id = $field";
		$sql["where"] = "(md_date_available < '$now' or md_date_available = '0000/00/00 00:00:00') and (md_date_remove > '$now' or md_date_remove = '0000/00/00 00:00:00') and ";
		return $sql;
	}
	
	/*************************************************************************************************************************
	* get list of metadata fields
	*************************************************************************************************************************/
	function get_fieldlist($parameters){
		return $this->metadata_fields;
	}

	/*************************************************************************************************************************
	* get list of metadata fields for email admin			( Added By Muhammad Imran Mirza )
	*************************************************************************************************************************/
	function get_fieldlist_email_admin($parameters){
		return $this->metadata_fields_email_admin;
	}
	
	
	/*************************************************************************************************************************
    * cache the metadata details 
    *************************************************************************************************************************/
	function metadata_cache($parameters){
		$md_identifier 			= $this->check_parameters($parameters,"md_identifier",-1);
		$identifier 			= $this->check_parameters($parameters,"identifier",-1);
		$module 				= $this->check_parameters($parameters,"module");
		$lang		 			= "en";
		$metadata_keyword_block	= "";
		if ($md_identifier==-1){
			if($module=="" || $identifier ==-1){
				return 0; // failed
			}
		}
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		if($md_identifier !=-1){
			$sql ="select * from metadata_details where md_client = $this->client_identifier and md_identifier=$md_identifier";
		} else {
			$sql ="select * from metadata_details where md_client = $this->client_identifier and md_link_id=$identifier and md_module='$module'";
		}
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$identifier				= "";
		$md_title				= "";
		$md_description			= "";
		$md_date_pub_seconds	= "";
		$md_date_pub			= "";
		$md_date_create			= "";
		$md_date_review			= "";
		$md_date_modify			= "";
		$md_date_remove			= "";
		$md_date_available		= "";
		$md_unique_identifier	= "";
		$md_price				= "";
		$md_discount			= "";
		$md_quantity			= "";
		$md_wieght				= "";
		$md_vat					= "";
		$md_link_id				= "";
		$md_canbuy				= 0;
		$found =0;
        while(($r = $this->call_command("DB_FETCH_ARRAY",Array($result))) && $found==0){
			$found =1;
			$identifier				= $r["md_identifier"];
			$md_link_id				= $r["md_link_id"];
			$md_title				= $r["md_title"];
			$md_description			= $r["md_description"];
			$md_date_pub_seconds	= strtotime($r["md_date_publish"]);
			$md_date_pub			= $r["md_date_publish"];
			$md_date_create			= $r["md_date_creation"];
			$md_date_review			= $r["md_date_review"];
			$md_date_modify			= $r["md_date_modified"];
			$md_date_remove			= $r["md_date_remove"];
			$md_date_available		= $r["md_date_available"];
			$md_unique_identifier	= $r["md_identifier"];
			$md_price				= $r["md_price"];
			$md_discount			= $r["md_discount"];
			$md_quantity			= $r["md_quantity"];
			$md_wieght				= $r["md_weight"];
			$md_vat					= $r["md_vat"];
			$md_canbuy				= $r["md_canbuy"];
		}
		$this->call_command("DB_FREE",Array($result));
		
		if($found==1){
			/*************************************************************************************************************************
	        * 
	        *************************************************************************************************************************/
			$metadata_keyword_block="";
			$sql = "select * from metadata_keyword inner join metadata_keyword_relationship on mdkr_keyword=mdk_identifier where mdkr_client=$this->client_identifier and mdkr_identifier = $identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$metadata_keyword_block .= '<keyword count="'.$r["mdkr_count"].'"><![CDATA['.$r["mdk_value"].']]></keyword>';
			}
			$this->call_command("DB_FREE",Array($result));
			/*************************************************************************************************************************
	        * build XML block
	        *************************************************************************************************************************/
			$metadata_block = '
				<metadata identifier="'.$identifier.'" linkto="'.$md_link_id.'">
					<title><![CDATA['.$md_title.']]></title>
					<keywords>'.$metadata_keyword_block .'</keywords>
					<description><![CDATA['.strip_tags(html_entity_decode($md_description)).']]></description>
					<date refinement="publish" seconds="'.$md_date_pub_seconds.'"><![CDATA['.$md_date_pub.']]></date>
					<date refinement="creation"><![CDATA['.$md_date_create.']]></date>
					<date refinement="review"><![CDATA['.$md_date_review.']]></date>
					<date refinement="modified"><![CDATA['.$md_date_modify.']]></date>
					<date refinement="remove"><![CDATA['.$md_date_remove.']]></date>
					<date refinement="available"><![CDATA['.$md_date_available.']]></date>
					<identifier><![CDATA['.$md_unique_identifier.']]></identifier>
					<format><![CDATA[text/html]]></format>
					<language><![CDATA['.$lang.']]></language>
					<price><![CDATA['.$md_price.']]></price>
					<discount><![CDATA['.$md_discount.']]></discount>
					<quantity><![CDATA['.$md_quantity.']]></quantity>
					<weight><![CDATA['.$md_wieght.']]></weight>
					<vat><![CDATA['.$md_vat.']]></vat>
					<canbuy><![CDATA['.$md_canbuy.']]></canbuy>
				</metadata>';
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
			$fname = $data_files."/metadata_".$this->client_identifier."_".$lang."_".$identifier.".xml";
			$fp = @fopen($fname,"w");
			if($fp){
				fputs($fp,$metadata_block);
				fclose($fp);
			}
			$um = umask(0);
			@chmod($fname, LS__FILE_PERMISSION);
			umask($um);
		}
//		print "<li>Updated [$fname] [ $metadata_block ]</li>";
//		$this->exitprogram();
		return "";
	}
}

?>
