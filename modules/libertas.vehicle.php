<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.vehicle.php
* @date 09 Oct 2002
*/
/**
* vehicle module
*/
class vehicle extends module{
	/**
	*  Class Variables
	*/
	var $module_name			= "vehicle";
	var $module_grouping		= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_label			= "MANAGEMENT_VEHICLE";
	var $module_admin			= "1";
	var $module_debug			= false;
	var $module_creation		= "13/09/2002";
	var $module_modify	 		= '$Date: 2005/02/08 17:01:14 $';
	var $module_version 			= '$Revision: 1.8 $';
	var $module_command			= "VEHICLE_"; 		// all commands specifically for this module will start with this token
	var $has_module_contact		= 0;
	var $has_module_group		= 0;
	var $display_options		= null;
	var $searched				= 0;
	
	var $module_display_options = array(
		Array("VEHICLE_LOCATION","Display the vehicles straight away (Vehicle Channel)"),
		Array("VEHICLE_LATEST","Display the latest vehicles in this location."),
		Array("VEHICLE_SEARCH","Display the vehicle search engine.")
	);
	
	var $module_admin_options = array(
	array("VEHICLE_MODIFY_LOOKUP","MANAGE_VEHICLE_LOOKUP"),
	array("VEHICLE_LIST","MANAGEMENT_VEHICLE")
	);
	
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
			if ($user_command==$this->module_command."GET_AUTHOR"){
				return $this->get_module_author();
			}
			if ($user_command==$this->module_command."GET_CREATION"){
				return $this->get_module_creation();
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}

			if ($user_command==$this->module_command."ITEM_DISPLAY"){
				return $this->item_display($parameter_list);
			}
			if ($user_command==$this->module_command."LATEST"){
				return $this->vehicle_location("LATEST",$parameter_list);
			}
			if ($user_command==$this->module_command."LOCATION"){
				return $this->vehicle_location("LOCATION",$parameter_list);
			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->vehicle_location("ENTRY",$parameter_list);
			}
			if ($user_command==$this->module_command."SEARCH"){
				if ($this->searched==0){
					$this->searched=1;
					return $this->vehicle_search($parameter_list);
				}
			}

			/*
			-- admin commands --
			*/
			if ($user_command==$this->module_command."SAVE"){
				$this->item_save($parameter_list);
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=VEHICLE_LIST"));
			}
			if (($user_command==$this->module_command."EDIT") || ($user_command==$this->module_command."ADD")){
				return $this->item_form($parameter_list);
			}
			if ($user_command==$this->module_command."REMOVE"){
				return $this->item_remove($parameter_list);
			}
			if ($user_command==$this->module_command."REMOVE_CONFIRM"){
				$this->item_remove_confirm($parameter_list);
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=VEHICLE_LIST"));
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."LIST"){
				return $this->display_list($parameter_list);
			}
			if (($user_command==$this->module_command."LOOKUP_REMOVE")||($user_command==$this->module_command."UPDATE_LOOKUP")){
				return $this->update_lookup($parameter_list);
			}
			if ($user_command==$this->module_command."MODIFY_LOOKUP"){
				return $this->modify_lookup();
			}
			if ($user_command == $this->module_command."REGENERATE_CACHE"){
				$this->regenerate_cache($parameter_list);
			}
			
		}else{
			// wrong command sent to system
		}
	}
	
	/**
	* Initialise function
	-----------------------
	- This function will initialise some variables for this modules functions to use.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise",__LINE__,""));
		}
		$this->editor_configurations = Array(
			"ENTRY_DESCRIPTION" => Array(
				"status"=>"unlocked", 
				"locked_to" => "", 
				"identifier"=>0
			)
		);
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* define the filtering information that is available
		*/
		$this->display_options		= array(
		array (0,"Order by Date Created (oldest first)"	,"vehicle_creation_date Asc"),
		array (1,"Order by Date Created (newest first)"	,"vehicle_creation_date desc"),
		array (2,"Order by Title A -> Z"				,"vehicle_title asc"),
		array (3,"Order by Title Z -> A"				,"vehicle_title desc")
		);
		$this->module_admin_user_access	= array(
			array($this->module_command."ALL","Complete Access")
		);
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
		* Table structure for table 'group_data'
		*/
		$fields = array(
			array("vehicle_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("vehicle_client"				,"unsigned integer"			,""			,"default '0'"),
			array("vehicle_manufacturer"		,"unsigned integer"			,""			,"default '0'"),
			array("vehicle_model"				,"unsigned integer"			,""			,"default '0'"),
			array("vehicle_year"				,"unsigned integer"			,"" 		,"default '0'"),
			array("vehicle_mileage"				,"unsigned integer"			,""			,"default '0'"),
			array("vehicle_price"				,"double"					,""			,"default '0'"),
			array("vehicle_engine_size"			,"varchar(20)"				,""	,"default ''"),
			array("vehicle_test"				,"unsigned small integer"	,""	,"default '0'"),
			array("vehicle_tax_year"			,"varchar(4)"				,""	,"default ''"),
			array("vehicle_tax_month"			,"varchar(2)"				,""	,"default ''"),
			array("vehicle_gearbox"				,"unsigned integer"			,""	,"default '0'"),
			array("vehicle_cab_type"			,"unsigned integer"			,""	,"default '0'"),
			array("vehicle_body_type"			,"unsigned integer"			,""	,"default '0'"),
			array("vehicle_extra_information"	,"text"						,""	,"default ''"),
			array("vehicle_licence_plate"		,"varchar(10)"				,""	,"default ''"),
			array("vehicle_date_created"		,"datetime"					,""	,"default NULL"),
			array("vehicle_status"				,"unsigned small integer"	,""	,"default '1'"),
			array("vehicle_chassis_number"		,"varchar(50)"				,""	,"default ''"),
			array("vehicle_customer"			,"varchar(50)"				,""	,"default ''"),
			array("vehicle_image_thumbnail"		,"unsigned small integer"	,""	,"default '0'"),
			array("vehicle_image_main"			,"unsigned small integer"	,""	,"default '0'"),
			array("vehicle_live"				,"unsigned small integer"	,""	,"default '0'"),
			array("vehicle_supplier"			,"varchar(50)"				,""	,"default ''")
		);
		
		
		$primary ="vehicle_identifier";
		$tables[count($tables)] = array("vehicle",$fields,$primary);
		/**
		* Table structure for table 'combo lookups'
		*/
		$insert_array = Array("body","cab","gears","manufacturer","model");
		for($index=0;$index<5;$index++){
			$val = $insert_array[$index];
			$fields = array(
			array("vehicle_".$val."_identifier"		,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("vehicle_".$val."_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'"),
			array("vehicle_".$val."_description"	,"text"						,""			,"default ''")
			);
			if ($insert_array[$index]=="model"){
				$fields[count($fields)] = array("vehicle_model_manufacturer"	,"unsigned integer" ,"NOT NULL"		,"default '0'");
			}
			$primary ="vehicle_".$val."_identifier";
			$tables[count($tables)] = array("vehicle_".$val,$fields,$primary);
		}
		/**
		* Table structure for table 'combo lookups'
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		
		
		*/
		$fields = array(
		array("vehicle_image_identifier"	,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
		array("vehicle_image_client"		,"unsigned integer"			,"NOT NULL"	,"default '0'"),
		array("vehicle_image_type"			,"unsigned small integer"	,"NOT NULL"	,"default '0'"),
		array("vehicle_image_file"			,"varchar(255)"				,"NOT NULL"	,"default ''"),
		array("vehicle_image_vehicle"		,"unsigned integer"			,""			,"default ''"),
		array("vehicle_image_mime"			,"varchar(50)"				,""			,"default ''"),
		array("vehicle_image_size"			,"unsigned integer"			,""			,"default ''")
		);
		$primary ="vehicle_image_identifier";
		$tables[count($tables)] = array("vehicle_image",$fields,$primary);
		
		return $tables;
	}
	
	/**
	* display_list function
	-----------------------
	- This function returns the list of vehicle that exists for this client.
	*/
	function display_list($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display_list",__LINE__,"[]"));
		}
		$where ="";
		$manufacturer_model = $this->check_parameters($parameters,"manufacturer_model","_none");
		if ($manufacturer_model!=""){
			$list = split("_",$manufacturer_model);
			if ($list[0] != ""){
				$where  .= " vehicle_manufacturer_identifier = ".$list[0]." and";
			}
			if ($list[1] != "none"){
				$where .= " vehicle_model_identifier = ".$list[1]." and";
			}
		}
		
		$sql =  " Select ";
		$sql .= "   vehicle.vehicle_identifier, ";
		$sql .= "   vehicle_manufacturer.vehicle_manufacturer_description, ";
		$sql .= "   vehicle_model.vehicle_model_description,";
		$sql .= "   vehicle.vehicle_licence_plate,";
		$sql .= "   vehicle.vehicle_year,";
		$sql .= "   vehicle.vehicle_mileage,";
		$sql .= "   vehicle.vehicle_price,";
		$sql .= "   vehicle.vehicle_engine_size,";
		$sql .= "   vehicle.vehicle_test,";
		$sql .= "   vehicle.vehicle_tax_year,";
		$sql .= "   vehicle.vehicle_tax_month,";
		$sql .= "   vehicle_gears.vehicle_gears_description,";
		$sql .= "   vehicle_cab.vehicle_cab_description,";
		$sql .= "   vehicle_body.vehicle_body_description,";
		$sql .= "   vehicle.vehicle_live,";
		$sql .= "   vehicle.vehicle_date_created,";
		$sql .= "   vehicle_image.vehicle_image_file";
		$sql .= " from vehicle ";
		$sql .= " left outer join vehicle_manufacturer on vehicle.vehicle_manufacturer = vehicle_manufacturer.vehicle_manufacturer_identifier";
		$sql .= " left outer join vehicle_model on vehicle_model.vehicle_model_identifier = vehicle.vehicle_model";
		$sql .= " left outer join vehicle_cab on vehicle_cab.vehicle_cab_identifier = vehicle.vehicle_cab_type";
		$sql .= " left outer join vehicle_body on vehicle_body.vehicle_body_identifier = vehicle.vehicle_body_type";
		$sql .= " left outer join vehicle_gears on vehicle_gears.vehicle_gears_identifier = vehicle.vehicle_gearbox";
		$sql .= " left outer join vehicle_image on vehicle_image.vehicle_image_vehicle = vehicle.vehicle_identifier";
		$sql .= " where $where (vehicle_image.vehicle_image_type is null or vehicle_image.vehicle_image_type=1)";
		
		$sql .= " order by vehicle.vehicle_identifier desc";
		
//		print $sql;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables["PAGE_BUTTONS"] = Array(
			Array("ADD",$this->module_command."ADD","Add a new vehicle"),
		);
		
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			$number_of_records=0;
			$goto=0;
			$finish=0;
			$page=0;
			$num_pages=0;
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = intval($this->check_parameters($parameters,"page",1));
			$goto = ((--$page) * PAGE_SIZE);
			
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
			}
			if ($goto+PAGE_SIZE>$number_of_records){
				$finish = $number_of_records;
			}else{
				$finish = $goto+PAGE_SIZE;
			}
			$goto++;
			$page++;
			
			$num_pages=floor($number_of_records / PAGE_SIZE);
			$remainder = $number_of_records % PAGE_SIZE;
			if ($remainder>0){
				$num_pages++;
			}
			
			$counter=0;
			
			
			
			$start_page=intval($page/PAGE_SIZE);
			$remainder = $page % PAGE_SIZE;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+PAGE_SIZE)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page+=PAGE_SIZE;
			
			$variables["END_PAGE"]			= $end_page;
			$variables["FILTER"]			= $this->filter($parameters);
			
			$variables["ENTRY_BUTTONS"] =Array(
			);
			
			$variables["RESULT_ENTRIES"] =Array();
			
			while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<10)){
				$counter++;
				$variables["RESULT_ENTRIES"][count($variables["RESULT_ENTRIES"])]=Array(
					"identifier"	=> $r["vehicle_identifier"],
					"ENTRY_BUTTONS"	=>	Array(
						Array("EDIT",$this->module_command."EDIT",EDIT_EXISTING),
						Array("REMOVE",$this->module_command."REMOVE_CONFIRM",REMOVE_EXISTING)
					),
					"attributes"	=> Array(
						array("Licence Plate #",$this->check_parameters($r,"vehicle_licence_plate"),"TITLE"),
						array("Manufacturer",$this->check_parameters($r,"vehicle_manufacturer_description"),"SUMMARY"),
						array("Model",$this->check_parameters($r,"vehicle_model_description"),"SUMMARY"),
						array("Year",$this->check_parameters($r,"vehicle_year")),
						array("Cab",$this->check_parameters($r,"vehicle_cab_description")),
						array("Body",$this->check_parameters($r,"vehicle_body_description")),
						array("Gears",$this->check_parameters($r,"vehicle_gears_description")),
						array("Created",$this->check_parameters($r,"vehicle_date_created")),
						array("Status",$this->check_status($this->check_parameters($r,"vehicle_live"))),
						array("Image",$this->check_parameters($r,"vehicle_image_file","images/themes/1x1.gif"),"IMAGE")
					)
				);
			}
		}
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$out = $this->generate_list($variables);
		return $out;
		
	}
	/**
	* filter
	-----------
	- The user filter will allow the user to filter the way that information is filtered
	- on the screen.
	*/
	function filter($parameters,$cmd="VEHICLE_LIST"){
		$manufacturer_model		= $this->check_parameters($parameters,"manufacturer_model","__NOT_SPECIFIED__");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"filter",__LINE__,join($parameters,", ")));
		}
		$out = "\t\t\t\t<form name=\"user_filter_form\" label=\"Vehicle Search Filter\" method=\"GET\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\" ><![CDATA[$cmd]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\"  ><![CDATA[1]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"search\"  ><![CDATA[1]]></input>\n";
		/**
		* retrieve the list of groups and display for selection
		*/
		$manufacturer_list = $this->manufacturer_and_model($manufacturer_model);
		$out .= "\t\t\t\t\t<select name=\"manufacturer_model\" label=\"Manufacturer\">\n";
		$out .= "\t\t\t\t\t\t<option value=\"\">Display All Manufacturers</option>\n";
		$out .= "$manufacturer_list";
		$out .= "\t\t\t\t\t</select>\n";
		/**
		* display the order by filter option
		*/
		$out .= "\t\t\t\t\t<input type=\"submit\" iconify=\"SEARCH\" name=\"\" value=\"Filter\"/>\n";
		$out .= "\t\t\t\t</form>";
		/**
		* return the filter XML document
		*/
		return $out;
	}
	
	/**
	* VEHICLE_form function
	-----------------------
	- This function will generate the proper table structure in the choosen database
	- format.
	*/
	function item_form($parameters){
		$this->load_editors();
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"VEHICLE_form",__LINE__,"[".join($parameters,", ")."]"));
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* define the default values for the filed variables
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$identifier = $this->check_parameters($parameters,"identifier","-1");
		$vehicle_licence_plate			= "";
		$vehicle_description			= "";
		$vehicle_gears					= "";
		$vehicle_cab					= "";
		$vehicle_body					= "";
		$vehicle_year 					= "";
		$vehicle_price 					= "";
		$vehicle_mileage 				= "";
		$vehicle_engine_size 			= "";
		$vehicle_test 					= "";
		$vehicle_tax_year 				= 0;
		$vehicle_tax_month				= 0;
		$vehicle_manufacturer			= 0;
		$vehicle_model					= 0;
		$vehicle_extra_information      = "";
		$vehicle_status					= 1;
		$vehicle_chassis_number 		= "";
		$vehicle_customer 				= "";
		$vehicle_supplier 				= "";
		$vehicle_image_thumbnail		= "";
		$vehicle_image_thumbnail_size	= 0;
		$vehicle_image_thumbnail_file	= "";
		$vehicle_image_main 			= "";
		$vehicle_image_main_size		= 0;
		$vehicle_image_main_file		= "";
		$vehicle_live					= 0;
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* if the identifier is not -1 then we can fill the default values with real data
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$label ="Add new Vehicle";
		
		if ($identifier!=-1){
			$label ="Edit existing Vehicle";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"vehicle_form",__LINE__,"[]"));
			}
			$sql =  "select    
						vehicle.*,
						vehicle_manufacturer.*,
						vehicle_model.*,
						vehicle_gears.*,
						vehicle_cab.*,
						vehicle_body.*
					from vehicle  
						left outer join vehicle_manufacturer on vehicle.vehicle_manufacturer 				= vehicle_manufacturer.vehicle_manufacturer_identifier 
						left outer join vehicle_model on vehicle_model.vehicle_model_identifier 			= vehicle.vehicle_model 
						left outer join vehicle_cab on vehicle_cab.vehicle_cab_identifier 					= vehicle.vehicle_cab_type 
						left outer join vehicle_body on vehicle_body.vehicle_body_identifier 				= vehicle.vehicle_body_type 
						left outer join vehicle_gears on vehicle_gears.vehicle_gears_identifier 			= vehicle.vehicle_gearbox 
					where vehicle.vehicle_identifier=$identifier and vehicle.vehicle_client=$this->client_identifier order by vehicle.vehicle_identifier desc;";
			$result = $this->call_command("DB_QUERY",Array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"vehicle_form",__LINE__,"[$sql]"));
			}
			if ($result){
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
				$vehicle_licence_plate			= $r["vehicle_licence_plate"];
				$vehicle_manufacturer			= $r["vehicle_manufacturer"];
				$vehicle_model					= $r["vehicle_model"];
				$vehicle_extra_information		= $r["vehicle_extra_information"];
				$vehicle_gears					= $r["vehicle_gearbox"];
				$vehicle_cab					= $r["vehicle_cab_type"];
				$vehicle_body					= $r["vehicle_body_type"];
				$vehicle_tax_year 				= $r["vehicle_tax_year"];
				$vehicle_tax_month 				= $r["vehicle_tax_month"];
				$vehicle_year 					= $r["vehicle_year"];
				$vehicle_mileage 				= $r["vehicle_mileage"];
				$vehicle_price 					= $r["vehicle_price"];
				$vehicle_engine_size 			= $r["vehicle_engine_size"];
				$vehicle_test 					= $r["vehicle_test"];
				$vehicle_status 				= $r["vehicle_status"];
				$vehicle_image_thumbnail 		= $r["vehicle_image_thumbnail"];
//				print $vehicle_image_thumbnail;
				$vehicle_image_main 			= $r["vehicle_image_main"];
//				print $vehicle_image_main;
				$vehicle_chassis_number 		= $r["vehicle_chassis_number"];
				$vehicle_customer 				= $r["vehicle_customer"];
				$vehicle_supplier 				= $r["vehicle_supplier"];
				$vehicle_live					= $r["vehicle_live"];
				$this->call_command("DB_FREE",array($result));
			}
			$sql = "select * from vehicle_image where vehicle_image_client=$this->client_identifier and  vehicle_image_vehicle = $identifier";
			$result = $this->call_command("DB_QUERY",Array($sql));
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"get Images SQL",__LINE__,"[$sql]"));
			}
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				if ($r["vehicle_image_type"]==1){
					$vehicle_image_thumbnail_size	= $r["vehicle_image_size"];
					$vehicle_image_thumbnail_file	= $r["vehicle_image_file"];
				} else {
					$vehicle_image_main_size		= $r["vehicle_image_size"];
					$vehicle_image_main_file		= $r["vehicle_image_file"];
				}
			}
			
		}
		
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* define the select combo boxes data
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		
		$months="";
		$month_list = Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
		for($index=0;$index<12;$index++){
			if ($vehicle_tax_month==$index){
				$months.="<option value='$index' selected='true'>".$month_list[$index]."</option>";
			}else{
				$months.="<option value='$index'>".$month_list[$index]."</option>";
			}
		}
		$years="";
		for($index=2001;$index<=(intval(date("Y"))+1);$index++){
			if ($vehicle_tax_year==$index){
				$years.="<option value='$index' selected='true' >$index</option>";
			}else{
				$years.="<option value='$index'>$index</option>";
			}
		}
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* retrieve the lookup tables for cab, gears and body
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		
		$gears 	= "";
		$cab="";
		$body="";
		$tables = array("gears","cab","body");
		for ($index=0;$index<3;$index++){
			$sql = "select * from vehicle_".$tables[$index]." order by vehicle_".$tables[$index]."_description";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"vehicle_form",__LINE__,"[$sql]"));
			}
			$lookup_result = $this->call_command("DB_QUERY",Array($sql));
			eval ("\$".$tables[$index].".=\"<option value='-1'>Select a ".$tables[$index]."</option>\";");
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($lookup_result))) {
				eval ("\$".$tables[$index].".=\"<option value='".$r["vehicle_".$tables[$index]."_identifier"]."'\";");
				eval("\$test = \$vehicle_".$tables[$index]."==".$r["vehicle_".$tables[$index]."_identifier"].";");
				if ($test){
					eval ("\$".$tables[$index].".=\" selected='true'\";");
				}
				eval ("\$".$tables[$index].".=\">".$r["vehicle_".$tables[$index]."_description"]."</option>\n\";");
			}
			eval ("\$".$tables[$index].".=\"<option value='-2'>Other</option>\n\";");
			$this->call_command("DB_FREE",array($lookup_result));
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"vehicle_form",__LINE__,"[]"));
		}
		
		
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* continue to generate the html form output
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$status  = "<option value='1'";
		if ($vehicle_status==1){
			$status .= " selected='true'";
		}
		$status .= ">Available</option>";
		$status .= "<option value='2'";
		if ($vehicle_status==2){
			$status .= " selected='true'";
		}
		$status .= ">Under Agreement</option>";
		$status .= "<option value='3'";
		if ($vehicle_status==3){
			$status .= " selected='true'";
		}
		$status .= ">Sold</option>";
		if ($vehicle_test==0){
			$psv = "selected=\"true\"";
			$mot = "";
		}else{
			$mot = "selected=\"true\"";
			$psv = "";
		}
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$pub_status  = "<option value='0'";
		if ($vehicle_live==0){
			$pub_status .= " selected='true'";
		}
		$pub_status .= ">Not live</option>";
		$pub_status .= "<option value='1'";
		if ($vehicle_live==1){
			$pub_status .= " selected='true'";
		}
		$pub_status .= ">Live</option>";
		$out.= "<page_options>
					<button command=\"VEHICLE_LIST\" alt=\"Return to the list of vehicle\" iconify=\"CANCEL\" /> 
  				</page_options>
			<form action ='vehicle.php' name='vehicle_form' columns=\"2\" label=\"$label\" method='post' enctype='multipart/form-data'>
			<input type='hidden' name='command' ><![CDATA[VEHICLE_SAVE]]></input>\n
			<input type='hidden' name='identifier' ><![CDATA[$identifier]]></input>\n
			<input type='hidden' name='MAX_FILE_SIZE' ><![CDATA[100000]]></input>\n
			<select label='Publication Status' name='vehicle_live'>$pub_status</select>
			<select label='Manufacturer' name='vehicle_manufacturer' other='true'></select>
			<select label='Model' name='vehicle_model' other='true'></select>
			<select label='Gear Box' name='vehicle_gears' other='true'>$gears</select>
			<select label='Cab Type' name='vehicle_cab' other='true'>$cab</select>
			<select label='Body Type' name='vehicle_body' other='true'>$body</select>
			<input label='Licence Plate' type='text' name='vehicle_licence_plate' ><![CDATA[$vehicle_licence_plate]]></input>
			<input label='Year (eg 1998)' type='text' name='vehicle_year' size='4'><![CDATA[$vehicle_year]]></input>\n
			<input label='Mileage' type='text' name='vehicle_mileage'><![CDATA[$vehicle_mileage]]></input>\n
			<input label='Price' type='text' name='vehicle_price' ><![CDATA[$vehicle_price]]></input>\n
		    <input label='Engine Size' type='text' name='vehicle_engine_size' ><![CDATA[$vehicle_engine_size]]></input>\n
			<radio label='Vehicle Test Type' name='vehicle_test'><option value=\"0\" $mot>MOT</option><option value=\"1\" $psv>PSV</option></radio>
			<select label='Tax Month' name='vehicle_tax_month'>$months</select>
			<select label='Tax Year' name='vehicle_tax_year'>$years</select>\n
			<select label='Sale Status' name='vehicle_status'>$status</select>\n
			<input label='Chassis Number' type='text' name='vehicle_chassis_number' ><![CDATA[$vehicle_chassis_number]]></input>\n
			<input label='Customer' type='text' name='vehicle_customer' ><![CDATA[$vehicle_customer]]></input>\n
			<input label='Supplier' type='text' name='vehicle_supplier' ><![CDATA[$vehicle_supplier]]></input>\n";
		$choices="";
		if($vehicle_image_thumbnail>0){
			$choices  = "<choice name=\"file_upload_radio\" value=\"__KEEP__\" label=\"Keep this file\" checked=\"true\" visibility=\"hidden\"/>";
			$choices .= "<choice name=\"file_upload_radio\" value=\"__REPLACE__\" label=\"Replace this file\" checked=\"\" visibility=\"visible\"/>";
		}
//		print "[$vehicle_image_thumbnail]";
		$out .= "
				<input type='hidden' name='file_upload_vehicle_image_thumbnail_exists' value='$vehicle_image_thumbnail'/>\n
				<input type=\"file\" file_size=\"$vehicle_image_thumbnail_size\" label=\"File thumbnail\" size=\"20\" name=\"vehicle_image_thumbnail\"  value=\"$vehicle_image_thumbnail_file\">$choices</input>";
		$choices="";
		if($vehicle_image_main>0){
			$choices  = "<choice name=\"file_upload_radio\" value=\"__KEEP__\" label=\"Keep this file\" checked=\"true\" visibility=\"hidden\"/>";
			$choices .= "<choice name=\"file_upload_radio\" value=\"__REPLACE__\" label=\"Replace this file\" checked=\"\" visibility=\"visible\"/>";
		}
		$this_editor = $this->check_parameters($this->editor_configurations,"ENTRY_DESCRIPTION",Array());
		$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
		$locked_to  = $this->check_parameters($this_editor,"locked_to","");
		$out .= "
			<input type='hidden' name='file_upload_vehicle_image_main_exists' value='$vehicle_image_main'/>\n
			<input type=\"file\" file_size=\"$vehicle_image_main_size\" label=\"File main image\" size=\"20\" name=\"vehicle_image_main\"  value=\"$vehicle_image_main_file\">$choices</input>
			<textarea config_type='$config_status_of_editor' locked_to='$locked_to' label=\"".ENTRY_DESCRIPTION."\" size=\"40\" height=\"15\" name=\"vehicle_extra_information\" type=\"RICH-TEXT\"><![CDATA[$vehicle_extra_information]]></textarea>
			<input type=\"submit\" iconify=\"SAVE\" label=\"Save this vehicle\"/>
		</form>\n";
		
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* retrieve the vehicle manufacturer and model data for the combo box, generate javascript to
		* maintain the form hidding, showing and repopulating the fileds as required
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		
		
		$sql = "select * 
					from vehicle_manufacturer 
				left outer join vehicle_model on vehicle_manufacturer.vehicle_manufacturer_identifier = vehicle_model.vehicle_model_manufacturer 
				where 
					vehicle_manufacturer.vehicle_manufacturer_client=$this->client_identifier 
				order by vehicle_manufacturer_description,vehicle_model_description";
//				print $sql;
		$mm_result = $this->call_command("DB_QUERY",Array($sql));
		$man = "Array(\n";
		$prev="";
		$man_count=0;
		$mod_count=0;
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* build javascript 4D array that will be used to store the vehicle information.
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$found=	0;
		if($mm_result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($mm_result))) {
				$found=1;
				if ($r["vehicle_manufacturer_description"]!=$prev){
					$prev=$r["vehicle_manufacturer_description"];
					$mod_count=0;
					if ($man_count>0){
						$man.="\n\t\t\t\t\t\t)\n\t\t\t\t\t),\n";
					}
					$man_count++;
					$man.="\t\t\t\t\tArray('".$this->convert_amps($this->check_parameters($r,"vehicle_manufacturer_description"))."','".$this->check_parameters($r,"vehicle_manufacturer_identifier")."',\n";
					$man.="\t\t\t\t\t\tArray(\n";
				}
				if ($mod_count>0){
					$man.=",";
				}
				if ($this->check_parameters($r,"vehicle_model_identifier")!=""){
					$man.="\n\t\t\t\t\t\t\tArray('".$this->check_parameters($r,"vehicle_model_identifier")."','".$this->convert_amps($this->check_parameters($r,"vehicle_model_description"))."')";
					$mod_count++;
				}
			}
		}
		if($found==1){
			$man .= "\n\t\t\t\t\t\t)\n\t\t\t\t\t)\n";
		}
		$man .= "\t\t\t\t)";
		$man = str_replace("&amp;","&",$man);
		$out.="\n<javascript>
		<![CDATA[
			vehicle_manufacturer=$vehicle_manufacturer;
			vehicle_model=$vehicle_model;
			var manufacture_and_model = $man;
			/*
				+- - - - - - - - - - - - - - - - 
				| Fill the default Combo boxes
				+- - - - - - - - - - - - - - - - 
			*/
//			setTimeout(\"Fill_Manufacturer_Combo(vehicle_manufacturer,manufacture_and_model);\",5000);
//			setTimeout(\"Fill_Model_Combo(vehicle_manufacturer,vehicle_model,manufacture_and_model);\",5000);
		]]>
	</javascript>";
		$out .="</module>";
		return $out;
	}
	
	/**
	* VEHICLE_save function
	-----------------------
	- This function will generate the proper table structure in the choosen database
	- format.
	*/
	function item_save($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"vehicle_save",__LINE__,join($parameters,", ")));
		}
		
		$ok=0;
		$vehicle_extra_information 		= $this->split_me($this->tidy($this->validate($this->check_parameters($parameters,"vehicle_extra_information"))),"'","&#39;");
		$vehicle_live					= $this->check_parameters($parameters,"vehicle_live");
		$vehicle_identifier 			= $this->check_parameters($parameters,"identifier","-1");
		$vehicle_gears_extra			= $this->strip_tidy($this->check_parameters($parameters,"vehicle_gears_extra"));
		$vehicle_gears					= $this->check_parameters($parameters,"vehicle_gears");
		$vehicle_cab_extra				= $this->strip_tidy($this->check_parameters($parameters,"vehicle_cab_extra"));
		$vehicle_cab					= $this->check_parameters($parameters,"vehicle_cab");
		$vehicle_body_extra				= $this->strip_tidy($this->check_parameters($parameters,"vehicle_body_extra"));
		$vehicle_body					= $this->check_parameters($parameters,"vehicle_body");
		$vehicle_manufacturer_extra		= $this->strip_tidy($this->check_parameters($parameters,"vehicle_manufacturer_extra"));
		$vehicle_manufacturer			= $this->check_parameters($parameters,"vehicle_manufacturer");
		$vehicle_model_extra			= $this->strip_tidy($this->check_parameters($parameters,"vehicle_model_extra"));
		$vehicle_model					= $this->check_parameters($parameters,"vehicle_model");
		$vehicle_licence_plate			= $this->strip_tidy($this->check_parameters($parameters,"vehicle_licence_plate"));
		$vehicle_year 					= $this->strip_tidy($this->check_parameters($parameters,"vehicle_year"));
		$vehicle_price 					= $this->strip_tidy($this->check_parameters($parameters,"vehicle_price"));
		$vehicle_mileage			 	= $this->strip_tidy($this->check_parameters($parameters,"vehicle_mileage"));
		$vehicle_engine_size 			= $this->strip_tidy($this->check_parameters($parameters,"vehicle_engine_size"));
		$vehicle_test					= $this->strip_tidy($this->check_parameters($parameters,"vehicle_test"));
		$vehicle_tax_year				= $this->strip_tidy($this->check_parameters($parameters,"vehicle_tax_year"));
		$vehicle_tax_month				= $this->strip_tidy($this->check_parameters($parameters,"vehicle_tax_month"));
		$vehicle_status					= $this->strip_tidy($this->check_parameters($parameters,"vehicle_status"));
		$vehicle_chassis_number			= $this->strip_tidy($this->check_parameters($parameters,"vehicle_chassis_number"));
		$vehicle_customer				= $this->strip_tidy($this->check_parameters($parameters,"vehicle_customer"));
		$vehicle_supplier				= $this->strip_tidy($this->check_parameters($parameters,"vehicle_supplier"));
		$file_upload_thumbnail_exists	= $this->check_parameters($parameters,"file_upload_thumbnail_exists");
		$file_upload_vehicle_image_thumbnail_exists = $this->check_parameters($parameters,"file_upload_vehicle_image_thumbnail_exists",0);
		$file_upload_vehicle_image_main_exists = $this->check_parameters($parameters,"file_upload_vehicle_image_main_exists",0);
//		print $file_upload_vehicle_image_main_exists;
		$file_upload_main_exists		= $this->check_parameters($parameters,"file_upload_main_exists");
		$user_identifier 				= $this->call_command("SESSION_GET",Array("SESSION_USER_IDENTIFIER"));
		
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* if other was specified then insert the new information into a lookup table and get the indexof it
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		if($vehicle_gears==-2){
			$vehicle_gears=$this->add_new_lookup("vehicle_gears","vehicle_gears_description",$vehicle_gears_extra,"vehicle_gears_identifier");
		}
		if($vehicle_cab==-2){
			$vehicle_cab=$this->add_new_lookup("vehicle_cab","vehicle_cab_description",$vehicle_cab_extra,"vehicle_cab_identifier");
		}
		if($vehicle_body==-2){
			$vehicle_body=$this->add_new_lookup("vehicle_body","vehicle_body_description",$vehicle_body_extra,"vehicle_body_identifier");
		}
		if($vehicle_manufacturer==-2){
			$vehicle_manufacturer=$this->add_new_lookup("vehicle_manufacturer","vehicle_manufacturer_description",$vehicle_manufacturer_extra,"vehicle_manufacturer_identifier");
		}
		if($vehicle_model==-2){
			$vehicle_model=$this->add_new_lookup("vehicle_model","vehicle_model_description",$vehicle_model_extra,"vehicle_model_identifier","vehicle_model_manufacturer",$vehicle_manufacturer);
		}
		
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* Store the values in an array for easy management
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$fields = array(
		"vehicle_client"					=> $this->client_identifier,
		"vehicle_licence_plate" 			=> $vehicle_licence_plate,
		"vehicle_manufacturer" 				=> $vehicle_manufacturer,
		"vehicle_model"						=> $vehicle_model,
		"vehicle_live"						=> $vehicle_live,
		"vehicle_year" 						=> $vehicle_year,
		"vehicle_mileage" 					=> $vehicle_mileage,
		"vehicle_price" 					=> $vehicle_price,
		"vehicle_engine_size" 				=> $vehicle_engine_size,
		"vehicle_test" 						=> $vehicle_test,
		"vehicle_tax_month" 				=> $vehicle_tax_month,
		"vehicle_tax_year" 					=> $vehicle_tax_year,
		"vehicle_gearbox" 					=> $vehicle_gears,
		"vehicle_cab_type" 					=> $vehicle_cab,
		"vehicle_body_type"					=> $vehicle_body,
		"vehicle_extra_information"			=> $vehicle_extra_information,
		"vehicle_status"					=> $vehicle_status,
		"vehicle_chassis_number"			=> $vehicle_chassis_number,
		"vehicle_customer"					=> $vehicle_customer,
		"vehicle_supplier"					=> $vehicle_supplier
		);
		/*
		"vehicle_image_main"				=> $vehicle_image_main_exists,
		"vehicle_image_thumbnail"			=> $vehicle_image_thumbnail_exists,
		*/
		
		if (($file_upload_main_exists==0) && $_FILES['vehicle_image_main']['size']>0){
			$fields["vehicle_image_main"]="1";
		}
		if (($file_upload_main_exists==1) && ($vehicle_image_main_choice=='ERASE')){
			$fields["vehicle_image_main"]="0";
			$this->remove_image("main",$vehicle_identifier);
		}
		
		if (($file_upload_thumbnail_exists==0) && ($_FILES['vehicle_image_thumbnail']['size']>0)){
			$fields["vehicle_image_thumbnail"]="1";
		}
		if (($file_upload_thumbnail_exists==1) && ($vehicle_image_thumbnail_choice=='ERASE')){
			$fields["vehicle_image_thumbnail"]="0";
			$this->remove_image("main",$vehicle_identifier);
		}
		$insertFields= "";
		$insertValues= "";
		$updateFields= "";
		$counter=0;
		foreach ($fields as $key=>$val){
			if ($counter>0){
				$insertFields.= ", ";
				$insertValues.= ", ";
				$updateFields.= ", ";
			}
			$counter++;
			$insertFields.= "$key";
			$insertValues.= "'$val'";
			$updateFields.="$key='$val'";
		}
		
		$identy=-1;
		if ($vehicle_identifier==-1){
			/**
			* Add a new vehicle to the system
			*/
			$now	= date("Y-m-d H:i:s");
			$sql	= "insert into vehicle ($insertFields, vehicle_date_created) values ($insertValues, '$now');";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			$sql	= "select * from vehicle where vehicle_client=$this->client_identifier and vehicle_date_created='$now'";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			if($result){
				$r 		= $this->call_command("DB_FETCH_ARRAY",array($result));
				$identy = $r["vehicle_identifier"];
			}
		} else {
			$sql = "update vehicle set $updateFields where vehicle_identifier=$vehicle_identifier;";
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$identy=$vehicle_identifier;
			
			/**
			* update an existing vehicle in the system
			*/
			$this->call_command("DB_QUERY",array($sql));
		}
		
		$thumb_extension			= substr($_FILES["vehicle_image_thumbnail"]["name"],strrpos($_FILES["vehicle_image_thumbnail"]["name"],"."));
		$thumb_destination_filename = "images/commercial_vehicles/vehicle_thumb_".$identy.$thumb_extension;
		$main_extension 			= substr($_FILES["vehicle_image_main"]["name"],strrpos($_FILES["vehicle_image_main"]["name"],"."));
		$main_destination_filename	= "images/commercial_vehicles/vehicle_main_".$identy.$main_extension;
		$root=$this->check_parameters($this->parent->site_directories,"ROOT");

//		print 	$root."/".$main_destination_filename." ".$root."/".$thumb_destination_filename." ".$file_upload_vehicle_image_thumbnail_exists." ".$file_upload_vehicle_image_main_exists."<br/>";
		if ($_FILES["vehicle_image_thumbnail"]["name"]!=""){
			if ($file_upload_vehicle_image_thumbnail_exists==0){
			$sql = "
			insert into vehicle_image 
				(vehicle_image_client, vehicle_image_file, vehicle_image_mime, vehicle_image_size, vehicle_image_vehicle, vehicle_image_type)
			values 
				('".$this->call_command("CLIENT_GET_IDENTIFIER")."','$thumb_destination_filename', '".$_FILES["vehicle_image_thumbnail"]["type"]."', '".$_FILES["vehicle_image_thumbnail"]["size"]."', '".$identy."', 1);";
			}else{
				$sql = "
				update vehicle_image set 
					 vehicle_image_file='$thumb_destination_filename', vehicle_image_mime='".$_FILES["vehicle_image_thumbnail"]["type"]."', vehicle_image_size='".$_FILES["vehicle_image_thumbnail"]["size"]."'
				where vehicle_image_vehicle='".$identy."' and vehicle_image_type = 1 and vehicle_image_client='".$this->call_command("CLIENT_GET_IDENTIFIER")."';";	
			}
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"destination location",__LINE__,$_FILES["vehicle_image_thumbnail"]["tmp_name"]."=>[$root$thumb_destination_filename]"));
			}
			move_uploaded_file($_FILES["vehicle_image_thumbnail"]["tmp_name"],$root."/".$thumb_destination_filename);
			@chmod($root.$thumb_destination_filename, LS__FILE_PERMISSION);
		}
		if (!empty($_FILES["vehicle_image_main"]["name"])){
			if ($file_upload_vehicle_image_main_exists==0){
				$sql = "
				insert into vehicle_image 
					(vehicle_image_client, vehicle_image_file, vehicle_image_mime, vehicle_image_size, vehicle_image_vehicle, vehicle_image_type)
				values 
					('".$this->call_command("CLIENT_GET_IDENTIFIER")."','$main_destination_filename', '".$_FILES["vehicle_image_main"]["type"]."', '".$_FILES["vehicle_image_main"]["size"]."', '".$identy."', 2);";
			}else{
				$sql = "
				update vehicle_image set 
					 vehicle_image_file='$main_destination_filename', vehicle_image_mime='".$_FILES["vehicle_image_main"]["type"]."', vehicle_image_size='".$_FILES["vehicle_image_main"]["size"]."' 
				where vehicle_image_vehicle='".$identy."' and vehicle_image_type = 2 and vehicle_image_client='".$this->call_command("CLIENT_GET_IDENTIFIER")."';";	
			}
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"destination location",__LINE__,$_FILES["vehicle_image_main"]["tmp_name"]."=>[$root$main_destination_filename]"));
			}
			move_uploaded_file($_FILES["vehicle_image_main"]["tmp_name"],$root."/".$main_destination_filename);
			@chmod($root.$main_destination_filename, LS__FILE_PERMISSION);
		}
		if($vehicle_live==1){
			$this->cache($identy);
		}
	}
	/**
	* VEHICLE_remove function                                                            
	-------------------------------------------------------------------------------------
	- This function will generate the proper table structure in the choosen database     
	- format.                                                                            
	*/
	function item_remove($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"item_remove",__LINE__,""));
		}
		
		$out  = "<module name=\"".$this->module_name."\" display=\"results\">";
		$out .= "<form label=\"vehicle information\" method=\"post\">";
		$out .= "<input type=\"hidden\" name=\"command\" value=\"VEHICLE_REMOVE_CONFIRM\"/>";
		$out .= "<input type=\"hidden\" name=\"vehicle_identifier\" value=\"".$parameters["identifier"]."\"/>";
		$out .= "<text><![CDATA[You have choosen to remove this vehicle from the system.<br/> By selecting yes the vehicle  will be removed permentaly]]></text>";
		$out .= "<input type=\"button\" iconify=\"NO\" label=\"No, I want to keep this ".$this->module_name."\" name=\"action\"  command=\"VEHICLE_LIST\"/>";
		$out .= "<input type=\"submit\" iconify=\"YES\" label=\"Yes, I want to remove this ".$this->module_name."\" name=\"action\" value=\"Remove\"/>";
		$out .= "</form>";
		$out .="</module>";
		
		return $out;
	}
	/*
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	| VEHICLE_remove_confirm function |                                                  
	+---------------------------------+                                                  
	| This function will generate the proper table structure in the choosen database     
	| format.                                                                            
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function item_remove_confirm($parameters){
		$id = $this->check_parameters($parameters,"identifier",-1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"vehicle_remove_confirm",__LINE__,""));
		}
		$sql = "Select vehicle_image_file from vehicle_image where vehicle_image_vehicle=".$id." and vehicle_image_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$list = Array();
		while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$list[count($list)] = $r["vehicle_image_file"];
		}
		
		$sql = "delete from vehicle where vehicle_identifier=".$id." and vehicle_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$this->call_command("DB_QUERY",array($sql));
		$sql = "delete from vehicle_image where vehicle_image_vehicle=".$id." and vehicle_image_client=$this->client_identifier";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$root=$this->check_parameters($this->parent->site_directories,"ROOT");
		for ($index=0,$max=count($list);$index<$max;$index++){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"unlink file",__LINE__,$root.$link[$index]));
			}
			@unlink($root.$link[$index]);
		}
		
		return true;
	}
	function manufacturer_and_model($manufacturer_model){
//	print $manufacturer_model;
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"manufacturer_and_model",__LINE__,"$manufacturer_model"));
		}
		$where = "";
		$out="";
		$list = array("","");
		if (isset($manufacturer_model)){
			$list = split("_",$manufacturer_model);
		}
		if (empty($list[1])){
			$list[1]="none";
		}
		$sql = "select * from vehicle_manufacturer inner join vehicle_model on vehicle_model_manufacturer = vehicle_manufacturer_identifier
		order by vehicle_manufacturer_description asc, vehicle_model_description asc";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$prev="";
		while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
			$identifier = $r["vehicle_manufacturer_identifier"]."_".$r["vehicle_model_identifier"];
			$selected = " ";
			if ($list[0]==$r["vehicle_manufacturer_identifier"]){
				if ($list[1]==$r["vehicle_model_identifier"]){
					$selected = " selected='true'";
				}
			}
			if ($prev!=$r["vehicle_manufacturer_identifier"]){
				$prev=$r["vehicle_manufacturer_identifier"]	;
				if ($list[0]==$r["vehicle_manufacturer_identifier"]){
					if ($list[1]=="none"){
						$selected = " selected='true'";
					}
				}
				
				$out .="<option value=\"".$r["vehicle_manufacturer_identifier"]."_none\" $selected>".$r["vehicle_manufacturer_description"]."</option>";
				if ($list[1] == "none"){
					$selected = " ";
				}
			}
			$out .="<option value=\"$identifier\" $selected>&#160;&#160;&#160;".$r["vehicle_model_description"]."</option>";
		}
		return $out;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- Add new lookup entry
	- This is used by the save functionality to add a new entry to one of the lookup tables
	- if the value specified in other is empty then dont add, just return -1
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function add_new_lookup($table,$field1,$value1,$id,$field2="",$value2=""){
		$value=-1;
		if (strlen($value1)>0){
			if ($field2==""){
				$sql ="insert into $table (".$table."_client,$field1) values ($this->client_identifier,'$value1')";
			} else {
				$sql ="insert into $table (".$table."_client,$field1,$field2) values ($this->client_identifier,'$value1','$value2')";
			}
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"add_new_lookup",__LINE__,"$sql"));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			$sql = "select * from $table where ".$table."_client=$this->client_identifier and $field1 = '$value1'";
			$result = $this->call_command("DB_QUERY",array($sql));
			$r = $this->call_command("DB_FETCH_ARRAY",array($result));
			$value=$r[$id];
			$this->call_command("DB_FREE",array($result));
		}
		return $value;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- list lookup table
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function modify_lookup(){
		$table = Array("cab","gears","body","manufacturer","model");
		$max = count($table);
		$outText  = "<module name=\"".$this->module_name."\" display=\"form\">";
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* define screen options for this screen
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
		$outText .= "<page_options>
						<button command=\"VEHICLE_LIST\" alt='".LOCALE_VEHICLE_RETURN_TO_LIST."' iconify='CANCEL'/> 
		  			</page_options>";
		$outText .= "<form name='vehicle_lookup' label=\"".LOCALE_VEHICLE_LOOKUP_FORM."\" method=\"POST\">";
		$outText .= "<input type=\"hidden\" name='command' value='VEHICLE_UPDATE_LOOKUP'/>";
		$outText .= "<input type=\"hidden\" name='todolist' value=''/>";
		$outText .= "<lookups>";
		for($index=0;$index<$max;$index++){
			$outText .= "<lookup name='".$table[$index]."'>";
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- start output generation
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			$sql ="select * from vehicle_".$table[$index]." order by vehicle_".$table[$index]."_description";
			//print $sql;
			$result = $this->call_command("DB_QUERY",array($sql));
			$option_list  = "";
		
			if($result){
				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
					$val = $r["vehicle_".$table[$index]."_identifier"];
					$text = $r["vehicle_".$table[$index]."_description"];
					if ($table[$index]=="model"){
						$man = $r["vehicle_model_manufacturer"];
						$option_list .="<entry value='$val' manufacturer='$man'>$text</entry>";
					}else{
						$option_list .="<entry value='$val'>$text</entry>";
					}
				}
			}
		
			$table_des = substr("vehicle_".$table[$index],8);
			$outText  .= "$option_list";
			$outText .= "</lookup>";
		}
		$outText .= "</lookups>";
		$outText .= "
			<input type=\"submit\" iconify=\"SAVE\" alt=\"Save Changes\"/>";
		$outText .= "</form></module>
		";
		return $outText;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- update lookup table
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function update_lookup($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"update_lookup",__LINE__,join(", ",$parameters)));
		}
		$command	= $this->check_parameters($parameters,"command","");
		$lookup_todo= $this->check_parameters($parameters,"lookup_todo");
		/*
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		* The TO DO LIST.
		* 
		* The to do list is a smart way of updating the lookup tables so that a user can modify all of the
		* information held in all of the look up tables.
		-
		* The information is recieved in a block of text seperated in two ways each line is a different 
		* action,  Each action is seperated by two colons :: that will contain all of the infomration that 
		* is required by this function to update any records, Actions available are ADD, EDIT and REMOVE
		-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		*/
//		print $lookup_todo;
		$lines = split("\r\n",$lookup_todo);
		$list = Array();
		$counter=0;
		$m = count($lines)-1;
//		print $m;
		for ($index=0;$index < $m;$index++){
			$found=-2;
			$entry = split("::",$lines[$index]);
			for ($z=0;$z<$counter;$z++){
				if ($entry[0]=="model"){
					if ($list[$z]["IDENTIFIER"] == $entry[2]){
						$found = $z;
					}
				} else {
					if ($list[$z]["IDENTIFIER"] == $entry[1]){
						$found = $z;
					}
				}
			}
//			print "($counter)[$found]";
			if ($found==-2){
				if ($entry[0]=="model"){
					$list[count($list)] = Array(
						"TABLE" => $entry[0],
						"IDENTIFIER" => $entry[2],
						"VALUE" => $this->strip_tidy($entry[3]),
						"ACTION" => $entry[4],
						"MANUFACTURER" => $entry[1]
					);
				} else {
					$list[count($list)] = Array(
						"TABLE" => $entry[0],
						"IDENTIFIER" => $entry[1],
						"VALUE" => $this->strip_tidy($entry[2]),
						"ACTION" => $entry[3],
						"MANUFACTURER" => ""
					);
				}
				$counter++;
			} else {
				if ($entry[0]=="model"){
					if ($entry[4]!="REMOVE"){
						$list[$found]["VALUE"] = $entry[3];
					} else {
						$list[count($list)] = Array(
							"TABLE" => $entry[0],
							"IDENTIFIER" => $entry[2],
							"VALUE" => $this->strip_tidy($entry[3]),
							"ACTION" => $entry[4],
							"MANUFACTURER" => $entry[1]
						);
					}
				} else {
					if ($entry[3]!="REMOVE"){
						$list[$found]["VALUE"] = $entry[2];
					} else {
						$list[count($list)] = Array(
							"TABLE" => $entry[0],
							"IDENTIFIER" => $entry[1],
							"VALUE" => $this->strip_tidy($entry[2]),
							"ACTION" => $entry[3],
							"MANUFACTURER" => ""
						);
					}
				}
			}
		}
		$sql= Array();
		$counter=0;
		for ($index=0;$index < count($list);$index++){
			if ($list[$index]["TABLE"]!="model"){
				if ($list[$index]["ACTION"]=="ADD"){
					$value = $list[$index]["VALUE"];
					$sql[$counter] = "insert into vehicle_".$list[$index]["TABLE"]." (vehicle_".$list[$index]["TABLE"]."_client, vehicle_".$list[$index]["TABLE"]."_description) values ($this->client_identifier, '$value')";
					$counter++;
				}
				if ($list[$index]["ACTION"]=="EDIT"){
					$value = $list[$index]["VALUE"];
					$identifier = $list[$index]["IDENTIFIER"];
					$sql[$counter] = "update vehicle_".$list[$index]["TABLE"]." set vehicle_".$list[$index]["TABLE"]."_description='$value' where vehicle_".$list[$index]["TABLE"]."_identifier='$identifier' and vehicle_".$list[$index]["TABLE"]."_client=$this->client_identifier";
					$counter++;
				}
				if ($list[$index]["ACTION"]=="REMOVE"){
					$value = $list[$index]["VALUE"];
					$identifier = $list[$index]["IDENTIFIER"];
					if ($identifier>0){
						$sql[$counter] = "delete from vehicle_".$list[$index]["TABLE"]." where vehicle_".$list[$index]["TABLE"]."_identifier='$identifier' and vehicle_".$list[$index]["TABLE"]."_client=$this->client_identifier ";
						$counter++;
					}
				}
			}
		}
		for ($index=0;$index < count($sql);$index++){
//			print "<p>".$sql[$index]."</p>";
			$result = $this->call_command("DB_QUERY",array($sql[$index]));
		}
		$list = $this->retrieve_manfacturers($list);
		for ($index=0;$index < count($list);$index++){
			if ($list[$index]["TABLE"]=="model"){
				if ($list[$index]["ACTION"]=="ADD"){
					$value = $list[$index]["VALUE"];
					$man = $list[$index]["MANUFACTURER"];
					$sql[$counter] = "insert into vehicle_model (vehicle_".$list[$index]["TABLE"]."_client,vehicle_".$list[$index]["TABLE"]."_description, vehicle_model_manufacturer) values ($this->client_identifier,'$value','$man')";
					$counter++;
				}
				if ($list[$index]["ACTION"]=="EDIT"){
					$value = $list[$index]["VALUE"];
					$identifier = $list[$index]["IDENTIFIER"];
					$man = $list[$index]["MANUFACTURER"];
					$sql[$counter] = "update vehicle_".$list[$index]["TABLE"]." set vehicle_".$list[$index]["TABLE"]."_description='$value' where vehicle_model_manufacturer = '' and vehicle_".$list[$index]["TABLE"]."_identifier='$identifier' and vehicle_".$list[$index]["TABLE"]."_client=$this->client_identifier";
					$counter++;
				}
				if ($list[$index]["ACTION"]=="REMOVE"){
					$value = $list[$index]["VALUE"];
					$identifier = $list[$index]["IDENTIFIER"];
					if ($identifier>0){
						$sql[$counter] = "delete from vehicle_".$list[$index]["TABLE"]." where vehicle_".$list[$index]["TABLE"]."_identifier='$identifier' and vehicle_".$list[$index]["TABLE"]."_client=$this->client_identifier ";
						$counter++;
					}
				}
			$result = $this->call_command("DB_QUERY",array($sql[$index]));
			}
		}
		$outText  = "<module name=\"".$this->module_name."\" display=\"form\">";
		/*-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
		  - define screen options for this screen
		  -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-*/
		$outText .= "
					<page_options>
						<button command=\"VEHICLE_LIST\" alt='".LOCALE_VEHICLE_RETURN_TO_LIST."' iconify='CANCEL'/> 
					</page_options>
					<form name='vehicle_lookup_confirm' label=\"".LOCALE_VEHICLE_LOOKUP_FORM_CONFIRM."\" method=\"POST\">
						<text><![CDATA[".LOCALE_VEHICLE_CONFIRM_LOOKUP_UPDATE."]]></text>
			 		</form></module>
		";
		return $outText;
	}

	function retrieve_manfacturers($l){
		$len = count($l);
		for ($index=0;$index<$len;$index++){
			if ($l[$index]["TABLE"]=="model"){
				if ($l[$index]["MANUFACTURER"]<=0){
					for ($z=0;$z<$len;$z++){
						if ($l[$z]["TABLE"]=="manufacturer"){
							if ($l[$z]["IDENTIFIER"]<1){
								$prev_id = $l[$z]["IDENTIFIER"];
								$value=$l[$z]["VALUE"];
								$sql = "Select * from vehicle_manufacturer where vehicle_manufacturer_client = $this->client_identifier and vehicle_manufacturer_description='$value'";
//print "<p>$sql</p>";
								$result = $this->call_command("DB_QUERY",array($sql));
								while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
									$man = $r["vehicle_manufacturer_identifier"];
									for ($x=0;$x<$len;$x++){
										if (($l[$x]["TABLE"]=="model") && ($l[$x]["MANUFACTURER"]==$prev_id)){
											$l[$x]["MANUFACTURER"]=$man;
											$l[$z]["IDENTIFIER"]=$man;
										}
									}
									break;
								}
							}
						}
					}
				}
			}
		}
		return $l;
	}
	function vehicle_latest(){
		$where ="";
		
		$sql =  " Select ";
		$sql .= "   vehicle.vehicle_identifier ";
		$sql .= " from vehicle ";
		$sql .= " where vehicle_live=1 and vehicle_client=$this->client_identifier";
		$sql .= " order by vehicle.vehicle_identifier desc limit 0,1";

		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"latest_pages",__LINE__," :: [$sql]"));
		}
		$out = "<module name=\"".$this->module_name."\" display=\"LATEST\" call=\"vehicle_list\">";
		
		$result = $this->call_command("DB_QUERY",Array($sql));
		$page_documents= Array();
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$id	  = $r["vehicle_identifier"];
				if (file_exists(dirname(__FILE__)."/data_files/vehicle_$id.xml")){
					$fp   = fopen(dirname(__FILE__)."/data_files/vehicle_$id.xml", 'r');
					$out .= fread($fp, filesize(dirname(__FILE__)."/data_files/vehicle_$id.xml"));
					fclose($fp);
				}
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
		$out .= "</module>";
		return $out;
	}
	
	function remove_image($s,$t){
	}

	function check_status($s){
		if ($s==0){
			return "Not Live";
		}
		if ($s==1){
			return "Live";
		}
	}
	
	function vehicle_location($type,$parameters){
		$lang="en";
		$out="";
		$page = $this->check_parameters($parameters,"page",1);
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[".join($parameters,", ")."]"));
		}
		if ($type=="LATEST"){
			$sql = "select * from vehicle where vehicle_live=1 and vehicle_client=$this->client_identifier order by vehicle_identifier desc limit 0,1";
		} else if ($type=="LOCATION"){
			$sql = "select * from vehicle where vehicle_live=1 and vehicle_client=$this->client_identifier order by vehicle_identifier desc";
		} else {
			if (!empty($parameters["identifier"])){
				$sql = "select * from vehicle where vehicle_live=1 and vehicle_client=$this->client_identifier and vehicle_identifier=".$parameters["identifier"];
			}else{
				$sql = "select * from vehicle where vehicle_live=1 and vehicle_client=$this->client_identifier  order by vehicle_identifier desc";
			}
		}
//		print $sql;
		
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",Array($sql));
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
		$page_documents= Array();
		if ($result){
			$number_of_records = $this->call_command("DB_NUM_ROWS",Array($result));
			$counter=0;
			$start=0;

			if ($type=='LOCATION'){
					$goto = ((--$page)*PAGE_SIZE);
					if (($goto!=0)&&($number_of_records>$goto)){
						$pointer = $this->call_command("DB_SEEK",array($result,$goto));
					}
					if ($goto+PAGE_SIZE>$number_of_records){
						$finish = $number_of_records;
					}else{
						$finish = $goto+PAGE_SIZE;
					}
					$goto++;
					$page++;
					
					$num_pages=floor($number_of_records / PAGE_SIZE);
					$remainder = $number_of_records % PAGE_SIZE;
					if ($remainder>0){
						$num_pages++;
					}
					
					$counter=0;
	/*				$variables["START"]				= $goto;
					$variables["FINISH"]			= $finish;
					$variables["CURRENT_PAGE"]		= $page;
					$variables["NUMBER_OF_PAGES"]	= $num_pages;
		*/			
					$start_page						= intval($page/PAGE_SIZE);
					$remainder						= $page % PAGE_SIZE;
					if ($remainder>0){
						$start_page++;
					}
					
					if (($start_page+PAGE_SIZE)>$num_pages)
						$end_page					= $num_pages;
					else
						$end_page					+=PAGE_SIZE;
			}
			while (($r = $this->call_command("DB_FETCH_ARRAY",Array($result))) && ($counter<PAGE_SIZE)){
				$identifier = $r["vehicle_identifier"];
				if (file_exists($data_files."/vehicle_".$this->client_identifier."_".$lang."_".$identifier.".xml")){
					$page_documents[count($page_documents)]=$data_files."/vehicle_".$this->client_identifier."_".$lang."_".$identifier.".xml";
				}
				$counter++;
			}
			$result = $this->call_command("DB_FREE",Array($result));
		}
		$out = "<module name=\"".$this->module_name."\" display=\"$type\">";
		if ($type=='LOCATION'){
			$out .= "\t\t\t<data_list command=\"";
			
			$out .= "\" page_size=\"".$this->page_size."\" number_of_records=\"$number_of_records\" start=\"$goto\" finish=\"$finish\" current_page=\"$page\" number_of_pages=\"$num_pages\">\n";
			$out .= "\t\t\t\t<pages>\n";
			for($index=$start_page;$index<=$end_page;$index++){
				$out .= "<page>$index</page>\n";
			}
			$out .= "\t\t\t\t</pages>\n";
			$out .= "<results>";		
		}
		for ($index	 = 0, $length_of_entities = count($page_documents); $index < $length_of_entities; $index++){
			$fp		 = fopen($page_documents[$index], "rb");
			if ($fp){
				while (!feof($fp)){
					$out .= fread($fp, 4096);
				}
			}
			fclose($fp);
		}
		if ($type=='LOCATION'){
			$out .= "</results>\t\t\t</data_list>\n";
		}
		$out .= "</module>";
		return $out;
	}
	function vehicle_search($parameters){
		$where = "";
		$join="";
		$order_by="";
		$status =array();
		$variables=array();
		$search = $this->check_parameters($parameters,"search",0);
		$where ="";
		$manufacturer_model = $this->check_parameters($parameters,"manufacturer_model");
		if ($manufacturer_model!=""){
			$list = split("_",$manufacturer_model);
			if ($list[0] != ""){
				$where  = " vehicle_manufacturer_identifier = ".$list[0]." and ";
			}
			if ($list[1] != "none"){
				$where .= "vehicle_model_identifier = ".$list[1]." and ";
			}
		}
			
		$sql =  " Select ";
		$sql .= "   vehicle.vehicle_identifier, ";
		$sql .= "   vehicle_manufacturer.vehicle_manufacturer_description, ";
		$sql .= "   vehicle_model.vehicle_model_description,";
		$sql .= "   vehicle.vehicle_licence_plate,";
		$sql .= "   vehicle.vehicle_year,";
		$sql .= "   vehicle.vehicle_mileage,";
		$sql .= "   vehicle.vehicle_price,";
		$sql .= "   vehicle.vehicle_engine_size,";
		$sql .= "   vehicle.vehicle_test,";
		$sql .= "   vehicle.vehicle_tax_year,";
		$sql .= "   vehicle.vehicle_tax_month,";
		$sql .= "   vehicle_gears.vehicle_gears_description,";
		$sql .= "   vehicle_cab.vehicle_cab_description,";
		$sql .= "   vehicle_body.vehicle_body_description,";
		$sql .= "   vehicle.vehicle_live,";
		$sql .= "   vehicle.vehicle_date_created";
		$sql .= " from vehicle ";
		$sql .= " left outer join vehicle_manufacturer on vehicle.vehicle_manufacturer = vehicle_manufacturer.vehicle_manufacturer_identifier";
		$sql .= " left outer join vehicle_model on vehicle_model.vehicle_model_identifier = vehicle.vehicle_model";
		$sql .= " left outer join vehicle_cab on vehicle_cab.vehicle_cab_identifier = vehicle.vehicle_cab_type";
		$sql .= " left outer join vehicle_body on vehicle_body.vehicle_body_identifier = vehicle.vehicle_body_type";
		$sql .= " left outer join vehicle_gears on vehicle_gears.vehicle_gears_identifier = vehicle.vehicle_gearbox";
		$sql .= " where $where vehicle_client=$this->client_identifier and vehicle_live=1";
		$sql .= " order by vehicle.vehicle_identifier desc";
		
		$variables["FILTER"]			= $this->filter($parameters,"VEHICLE_SEARCH");
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		if (isset($parameters["page_search"])){
			$variables["PAGE_SEARCH"]	= $parameters["page_search"];
		}else {
			$variables["PAGE_SEARCH"]	= "";
		}
		if ($search==1){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,$sql));
			}
			$result = $this->call_command("DB_QUERY",array($sql));
			
			if (!$result){
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
				}
				return "";
			}else{
				if ($this->module_debug){
					$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
				}
				$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
				$page = $parameters["page"];
				$goto = ((--$page)*PAGE_SIZE);
				if (($goto!=0)&&($number_of_records>$goto)){
					$pointer = $this->call_command("DB_SEEK",array($result,$goto));
				}
				if ($goto+PAGE_SIZE>$number_of_records){
					$finish = $number_of_records;
				}else{
					$finish = $goto+PAGE_SIZE;
				}
				$goto++;
				$page++;
				
				$num_pages=floor($number_of_records / PAGE_SIZE);
				$remainder = $number_of_records % PAGE_SIZE;
				if ($remainder>0){
					$num_pages++;
				}
				
				$counter=0;
				$variables["NUMBER_OF_ROWS"]	= $number_of_records;
				$variables["START"]				= $goto;
				$variables["FINISH"]			= $finish;
				$variables["CURRENT_PAGE"]		= $page;
				$variables["NUMBER_OF_PAGES"]	= $num_pages;
				
				$start_page						= intval($page/PAGE_SIZE);
				$remainder						= $page % PAGE_SIZE;
				if ($remainder>0){
					$start_page++;
				}
				
				$variables["START_PAGE"]		= $start_page;
				
				if (($start_page+PAGE_SIZE)>$num_pages)
					$end_page					= $num_pages;
				else
					$end_page					+=PAGE_SIZE;
				
				$variables["END_PAGE"]			= $end_page;
				$variables["ENTRY_BUTTONS"] 	= Array();
				$variables["RESULT_ENTRIES"] 	= Array();
				while (($r = $this->call_command("DB_FETCH_ARRAY",array($result)))&&($counter<PAGE_SIZE)){
					$counter++;
					$variables["RESULT_ENTRIES"][count($variables["RESULT_ENTRIES"])]=Array(
						"identifier"	=> $r["vehicle_identifier"],
						"attributes"	=> Array(
								array("Year",			$r["vehicle_year"]),
								array("Manufacturer",	$r["vehicle_manufacturer_description"]),
								array("Model",			$r["vehicle_model_description"])/*,
								array("Price",			$r["vehicle_price"])*/
							)
						);
				}
			}
		}
		$out = $this->generate_search($variables);
		
		return $out;
	}

	function regenerate_cache($parameters=Array()){
		$page 		= intval($this->check_parameters($parameters,"page","1"));
		$sql  		= "select vehicle.vehicle_identifier from vehicle where vehicle.vehicle_live = 1 and vehicle.vehicle_client=$this->client_identifier order by vehicle.vehicle_identifier asc";
		$size 		= 50;
		$result 	= $this->call_command("DB_QUERY", array($sql));
		$num_rows 	= $this->call_command("DB_NUM_ROWS", array($result));
		$goto = (($page-1)*$size);
		if (($goto!=0)&&($num_rows>$goto)){
			$pointer = $this->call_command("DB_SEEK",array($result,$goto));
			
		}
		$c=0;
		while (($r=$this->call_command("DB_FETCH_ARRAY", array($result))) && ($c<$size)){
			$this->cache(intval($r["vehicle_identifier"]),0);
			$c++;
		}
		if ($goto<$num_rows){
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=VEHICLE_REGENERATE_CACHE&".SID."&page=".($page+1)));
		}
	}

		function cache($identifier){
		$lang ="en";
		if ($identifier>0){
			$sql =  " Select ";
			$sql .= "   vehicle.*, ";
			$sql .= "   vehicle_manufacturer.vehicle_manufacturer_description, ";
			$sql .= "   vehicle_model.vehicle_model_description,";
			$sql .= "   vehicle_gears.vehicle_gears_description,";
			$sql .= "   vehicle_cab.vehicle_cab_description,";
			$sql .= "   vehicle_body.vehicle_body_description";
			$sql .= " from vehicle ";
			$sql .= " left outer join vehicle_manufacturer on vehicle.vehicle_manufacturer = vehicle_manufacturer.vehicle_manufacturer_identifier";
			$sql .= " left outer join vehicle_model on vehicle_model.vehicle_model_identifier = vehicle.vehicle_model";
			$sql .= " left outer join vehicle_cab on vehicle_cab.vehicle_cab_identifier = vehicle.vehicle_cab_type";
			$sql .= " left outer join vehicle_body on vehicle_body.vehicle_body_identifier = vehicle.vehicle_body_type";
			$sql .= " left outer join vehicle_gears on vehicle_gears.vehicle_gears_identifier = vehicle.vehicle_gearbox";
			$sql .= " where vehicle_client=$this->client_identifier and vehicle_identifier=$identifier";
			
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,":: [$sql]"));
			}
			$month_list = Array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
			
			$result = $this->call_command("DB_QUERY",Array($sql));
			$page_documents= Array();
			if ($result){
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					$id						= $this->check_parameters($r,"vehicle_identifier");
					$licence_plate			= $this->check_parameters($r,"vehicle_licence_plate");
					$manufacturer			= $this->check_parameters($r,"vehicle_manufacturer");
					$model					= $this->check_parameters($r,"vehicle_model");
					$gears					= $this->check_parameters($r,"vehicle_gearbox");
					$cab					= $this->check_parameters($r,"vehicle_cab_type");
					$body					= $this->check_parameters($r,"vehicle_body_type");
					$gears_description		= $this->check_parameters($r,"vehicle_gears_description");
					$cab_description		= $this->check_parameters($r,"vehicle_cab_description");
					$body_description		= $this->check_parameters($r,"vehicle_body_description");
					$manufacturer_description= $this->check_parameters($r,"vehicle_manufacturer_description");
					$model_description		= $this->check_parameters($r,"vehicle_model_description");
					$tax_year 				= $this->check_parameters($r,"vehicle_tax_year");
					$tax_month 				= $month_list[$this->check_parameters($r,"vehicle_tax_month")];
					$year 					= $this->check_parameters($r,"vehicle_year");
					$mileage 				= $this->check_parameters($r,"vehicle_mileage");
					//$price 					= $this->check_parameters($r,"vehicle_price");
					$engine_size 			= $this->check_parameters($r,"vehicle_engine_size");
					$test 					= $this->check_parameters($r,"vehicle_test");
					$status 				= $this->check_parameters($r,"vehicle_status");
					$image_thumbnail 		= $this->check_parameters($r,"vehicle_image_thumbnail");
					$image_main 			= $this->check_parameters($r,"vehicle_image_main");
					$chassis_number 		= $this->check_parameters($r,"vehicle_chassis_number");
					$customer 				= $this->check_parameters($r,"vehicle_customer");
					$supplier 				= $this->check_parameters($r,"vehicle_supplier");
					$description			= $this->check_parameters($r,"vehicle_extra_information");
				}
				$result = $this->call_command("DB_FREE",Array($result));
			}
			$sql = "select * from vehicle_image where vehicle_image_vehicle = $identifier and vehicle_image_client = $this->client_identifier";
			$result = $this->call_command("DB_QUERY",Array($sql));
			$root=$this->check_parameters($this->parent->site_directories,"ROOT");
			$thumb_destination_filename = "";
			$image_thumbnail =0;
			$main_destination_filename	= "";
			$image_main =0;
			if ($result){
				while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
					if ($r["vehicle_image_type"]==1){
						$thumb_destination_filename = $r["vehicle_image_file"];
						if (file_exists($root."/".$thumb_destination_filename)){
							$image_thumbnail =1;
						}else{
							$image_thumbnail =0;
						}
					}
					if ($r["vehicle_image_type"]==2){
						$main_destination_filename	= $r["vehicle_image_file"];
						if (file_exists($root."/".$main_destination_filename)){
							$image_main =1;
						}else{
							$image_main =0;
						}
					}
				}
			}
			$out = "<vehicle identifier=\"$id\">
					<licence_plate><![CDATA[$licence_plate]]></licence_plate>
					<manufacturer><![CDATA[$manufacturer]]></manufacturer>
					<model><![CDATA[$model]]></model>
					<gears><![CDATA[$gears]]></gears>
					<cab><![CDATA[$cab]]></cab>
					<body><![CDATA[$body]]></body>
					<gears_description><![CDATA[$gears_description]]></gears_description>
					<cab_description><![CDATA[$cab_description]]></cab_description>
					<body_description><![CDATA[$body_description]]></body_description>
					<manufacturer_description><![CDATA[$manufacturer_description]]></manufacturer_description>
					<model_description><![CDATA[$model_description]]></model_description>
					<tax_year><![CDATA[$tax_year]]></tax_year>
					<tax_month><![CDATA[$tax_month]]></tax_month>
					<year><![CDATA[$year]]></year>
					<mileage><![CDATA[$mileage]]></mileage>
					";
//					<price><![CDATA[$price]]></price>
			$out.="	<engine_size><![CDATA[$engine_size]]></engine_size>
					<test><![CDATA[$test]]></test>
					<status><![CDATA[$status]]></status>
					<image_thumbnail exists=\"$image_thumbnail\"><![CDATA[$thumb_destination_filename]]></image_thumbnail>
					<image_main exists=\"$image_main\"><![CDATA[$main_destination_filename]]></image_main>
					<chassis_number><![CDATA[$chassis_number]]></chassis_number>
					<customer><![CDATA[$customer]]></customer>
					<supplier><![CDATA[$supplier]]></supplier>
					<description><![CDATA[$description]]></description>
			</vehicle>\n";
			$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
//			print $data_files."/vehicle_".$this->client_identifier."_".$lang."_".$identifier.".xml<br/>";
			$fp = fopen($data_files."/vehicle_".$this->client_identifier."_".$lang."_".$identifier.".xml", 'w');
			fwrite($fp, $out);
			fclose($fp);
				return 1;
		}else {
			return 0;
		}
		

	}


}

	/***
	| VEHICLE_display function	    |
	+-------------------------------+
	| This function will generate the proper table structure in the choosen database
	| format.
	+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
/*	function item_display($parameters){
		$out="";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"VEHICLE_display",__LINE__,""));
		}
		
		$sql = "Select vehicle.*, count(vehicle_thread.vehicle_thread_identifier) as total_threads, menu_data.menu_label from vehicle inner join menu_data on vehicle_location=menu_identifier left outer join vehicle_thread on vehicle_identifier = vehicle_thread_identifier where vehicle_client = $this->client_identifier and vehicle_status=1 and vehicle_location=".$parameters["current_menu_location"]." group by vehicle_identifier order by vehicle_identifier desc";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));
		}
		
		$result = $this->call_command("DB_QUERY",array($sql));
		if($this->call_command("DB_NUM_ROWS",array($result))>0) {
			$locations="";
			$out .="<module name=\"".$this->module_name."\" display=\"vehicle_list\">";
			
			$out .="<vehicle_list command=\"\">";
			while($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$identifier = $r["vehicle_identifier"];
				$threads="";
				$total_threads = $r["total_threads"];
				$out .="<vehicle indentifier=\"$identifier\" title=\"".$r["vehicle_label"]."\"><description><![CDATA[".$r["vehicle_description"]."]]></description><threads total_threads=\"$total_threads\">$threads</threads></vehicle>";
			}
			$out .="</vehicle_list>";
			$out .="</module>";
		}
		
		
		return $out;
	}
*/

?>