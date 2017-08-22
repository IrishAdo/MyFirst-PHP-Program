<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.events.php
* @created 10 Dec 2004
*/
/**
* This module is the module for managing Editor functionality access for users. it allows you to
* manage a selection of editor configurations and to define that a group has access to a specific editor
*
* it also allows you to specify the default editor for a modules form.
*/
require_once ("libertas.information_admin.php");
class events_admin extends information_admin {
	/**#@+
	* Class Variables
    * @var string
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name				= "events_admin";
	var $module_name_label			= "Event Management Module (Administration)";
	var $module_admin				= "1";
	var $module_channels			= "";
	var $module_modify	 			= '$Date: 2005/02/16 17:04:06 $';
	var $module_version 			= '$Revision: 1.13 $';
	var $module_command				= "EVENTADMIN_"; 		// all commands specifically for this module will start with this token
	var $module_presentation		= "EVENT_"; 		// all commands specifically for this module will start with this token
	var $module_presentation_name	= "events";
	var $webContainer				= "EVENT_";
	var $module_label				= "MANAGEMENT_EVENTS";
	var $module_setup				= "Events Builder";
	/*************************************************************************************************************************
    * define database tab one fields status
    *************************************************************************************************************************/
	var $setup_screen = Array(
		"ADDTOBASKET"	=> Array("visible"=>"No", "default"=>"LOCALE_DIRECTORY_ECOMMERCE_ADD_LABEL_DEFAULT", "LOCALE"=>1),
		"FULLYBOOKED"	=> Array("visible"=>"No", "default"=>"LOCALE_DIRECTORY_ECOMMERCE_NOSTOCK_LABEL_DEFAULT", "LOCALE"=>1),
		"NOSTOCK"		=> Array("visible"=>"No", "default"=>1, "LOCALE"=>0),
		"INMENU"		=> Array("visible"=>"No", "default"=>0, "LOCALE"=>0)
	);

	/*************************************************************************************************************************
    * define list of display options
    *************************************************************************************************************************/
	var $info_summary_layout_options_values = Array(0, 1, 3, 2 ,4, 5, 6);
	var $info_summary_layout_options_labels = Array("Display in defined structure", "Display in a table (sortable)", "Display in a table (non-sortable)", "Display an A to Z formatted directory","Available this year","Available this month","Available today");
    var $searched							= 0;
	/*************************************************************************************************************************
    * extra commands defined by
    *************************************************************************************************************************/
	var $extra_commands = Array(
		Array("EVENTADMIN_MANAGE_CALENDAR", "manage_calendar", 1, "Mini Calendar", "CALENDAR"),
		Array("EVENTADMIN_SAVE_CALENDAR", "manage_calendar_save", 0 ),
		Array("SHOP_ITEM_PURCHASE_HISTORY", "", 2, "Purchase History", "HISTORY")
	);

	/**#@+
	*  Management Menu entries
	*/
	var $module_admin_options 		= array(
	);
	var $admin_access				= 0;
	var $approver_access			= 0;
	var $author_access				= 0;
	var $event_admin_access			= 0;
	/**
	*  Group access Restrictions, restrict a group to these command sets
	*/
	var $module_admin_user_access	= array();

    /**
	*  Channel options
	*/
	var $module_display_options 	= array();

    /**
	*  Class Methods
	*/

	/**
	* @function Initialise function
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
		/**
		*request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		parent::initialise();
		$this->special_webobjects["CALENDAR"] = Array(
			"owner_module" 	=> $this->module_presentation."SHOW_CALENDAR",
			"label" 		=> "Calendar",
			"wo_command"	=> $this->module_presentation."SHOW_CALENDAR",
			"file"			=> "_calendar.php",
			"available"		=> 1
		);
		return 1;
	}
	/*************************************************************************************************************************
    * manage calendar locations
    *************************************************************************************************************************/
	function manage_calendar($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		if($identifier==-1){
			return "";
		}
		$all_locations		= 0;
		$set_inheritance	= 0;
		$out 				= "";
		$event_object = $this->call_command("WEBOBJECTS_RETRIEVE_OBJECT",
				Array(
					"owner_module" 	=> $this->webContainer."SHOW_CALENDAR",
					"identifier" 	=> $identifier,
					"wo_command"	=> $this->webContainer."SHOW_CALENDAR",
					"default_label" =>"Show Calendar Web Object"
				)
			);
		$event_label		= $event_object["label"];
		$event_label_show	= $event_object["show_label"];
		$mls = $this->call_command("LAYOUT_MENU_LOCATION_SETTINGS_GET", Array("identifier"=>$identifier, "module"=>$this->webContainer."SHOW_CALENDAR"));
		$all_locations		= $mls["all_locations"];
		$set_inheritance	= $mls["set_inheritance"];

/*		$sql = "select * from menu_location_settings where mls_client=$this->client_identifier and mls_link_id =$identifier and mls_module='$this->webContainer'";
//		print $sql;
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$all_locations		= $r["mls_all_locations"];
			$set_inheritance	= $r["mls_set_inheritance"];
        }
        $this->call_command("DB_FREE",Array($result));
*/		
		$menu_locations		= $this->call_command("LAYOUT_MENU_TO_OBJECT_LIST",
			Array(
				"module"		=> $this->webContainer."SHOW_CALENDAR",
				"identifier"	=> $identifier
			)
		);
		/**
		* Retrieve the list of Web Containers that this item can be put into
		*/ 
		$WebContainerList  = $this->call_command("WEBOBJECTS_EXTRACT_TYPE_2_CONTAINERS",
			Array(
				"module"		=> $this->webContainer."SHOW_CALENDAR", 
				"identifier"	=>$identifier
			)
		);
		/*************************************************************************************************************************
        * return this form
        *************************************************************************************************************************/
		$form_label="Manage Calendar";
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[".LOCALE_INFO_DIRECTORY_HEADER." - $form_label]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."SAVE_CALENDAR\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<page_sections>";
		$out .="			<section label='Setup'>";
		$out .="				<input type=\"text\" name=\"event_label\" label=\"".LOCALE_LABEL."\"><![CDATA[$event_label]]></input>";
		$out .="				<select name=\"event_label_show\" label=\"".LOCALE_LABEL_SHOW."\">".$this->gen_options(Array(0,1), Array(LOCALE_NO, LOCALE_YES),$event_label_show)."</select>";
		$web_containers = split("~----~",$WebContainerList);
		if ( $web_containers[0] != "" ){
			$out .=				"<input type=\"hidden\" name=\"currentlyhave\" value=\"".$web_containers[1]."\" />";
			$out .= 			"<checkboxes type='vertical' name='web_containers' label='What containers should this appear in'>".$web_containers[0]."</checkboxes>";
		}

		$out .="			</section>";
		$out .= $this->location_tab($all_locations, $set_inheritance,$menu_locations, "");
		$out .="		</page_sections>";
		$out .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * save calendar locations
    *************************************************************************************************************************/
	function manage_calendar_save($parameters){
//	print_r($parameters);
		$identifier 			= $this->check_parameters($parameters,"identifier",-1);
		$all_locations			= $this->check_parameters($parameters, "all_locations");
		$menu_locations			= $this->check_parameters($parameters, "menu_locations", Array());
		$pmenu_locations		= $this->check_parameters($parameters, "pmenu_locations",-1);
		$set_inheritance		= $this->check_parameters($parameters, "set_inheritance");
		$replacelist			= $this->check_parameters($parameters, "web_containers",Array());
		$currentlyhave			= $this->check_parameters($parameters, "currentlyhave");
		$event_label 			= $this->check_parameters($parameters, "event_label");
		$event_label_show		= $this->check_parameters($parameters, "event_label_show");
		$mls = $this->call_command("LAYOUT_MENU_LOCATION_SETTINGS_GET", Array("identifier"=>$identifier, "module"=>$this->webContainer."SHOW_CALENDAR"));
		$id  = $mls["mls_id"];
/*
		$sql = "select * from menu_location_settings where mls_client=$this->client_identifier and mls_link_id =$identifier and mls_module='".$this->webContainer."SHOW_CALENDAR'";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$result  = $this->parent->db_pointer->database_query($sql);
		$id=-1;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$id = $r["mls_identifier"];
        }
        $this->call_command("DB_FREE",Array($result));
//		print $sql;
*/
		if($id==-1){
			$this->call_command("LAYOUT_MENU_LOCATION_SETTINGS_SAVE", Array(
					"module"			=> $this->webContainer."SHOW_CALENDAR",
					"identifier"		=> $identifier,
					"set_inheritance"	=> $set_inheritance,
					"all_locations"		=> $all_locations,
					"cmd"				=> "ADD"
				)
			);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",
				Array(
					"owner_module" 	=> $this->webContainer."SHOW_CALENDAR",
					"owner_id" 		=> $identifier,
					"label" 		=> $event_label,
					"wo_command"	=> $this->webContainer."SHOW_CALENDAR",
					"cmd"			=> "INSERT",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist,
					"property"		=> Array("show_label"=>$event_label_show)
				)
			);
		} else {
			$this->call_command("LAYOUT_MENU_LOCATION_SETTINGS_SAVE", Array(
					"module"			=> $this->webContainer."SHOW_CALENDAR",
					"identifier"		=> $identifier,
					"set_inheritance"	=> $set_inheritance,
					"all_locations"		=> $all_locations,
					"cmd"				=> "EDIT"
				)
			);
			$this->call_command("WEBOBJECTS_MANAGE_MODULE",
				Array(
					"owner_module" 	=> $this->webContainer."SHOW_CALENDAR",
					"owner_id" 		=> $identifier,
					"label" 		=> $event_label,
					"wo_command"	=> $this->webContainer."SHOW_CALENDAR",
					"cmd"			=> "UPDATE",
					"previous_list" => $currentlyhave,
					"new_list"		=> $replacelist,
					"property"		=> Array("show_label"=>$event_label_show)
				)
			);
			$cmd			= "UPDATE";
		}
		$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
			Array(
				"menu_locations"=> $menu_locations,
				"module"		=> $this->webContainer."SHOW_CALENDAR",
				"identifier"	=> $identifier,
				"all_locations"	=> $all_locations
			)
		);
		/**
		* Save inheritance
		*/
		if ($set_inheritance==1){
			$child_locations = $this->add_inheritance($this->webContainer."DISPLAY",$menu_locations);
			$this->call_command("LAYOUT_MENU_TO_OBJECT_UPDATE", 
				Array(
					"menu_locations"=>$child_locations,
					"module"		=> $this->webContainer."SHOW_CALENDAR",
					"identifier"	=> $identifier,
					"all_locations"	=> $all_locations,
					"delete"		=> 0
				)
			);
			$this->set_inheritance(
				$this->webContainer."SHOW_CALENDAR",
				$this->call_command("LAYOUT_MENU_TO_OBJECT_EXTRACT",Array(
					"module"			=> $this->webContainer."SHOW_CALENDAR",
					"condition"			=> "mls_set_inheritance =1 and ",
					"client_field"		=> "mls_client",
					"table"				=> "menu_location_settings",
					"primary"			=> "mls_identifier"
					)
				).""
			);
		}
		$this->tidyup_display_commands(Array("all_locations" => $set_inheritance, "tidy_table" => "menu_location_settings", "tidy_field_starter" => "mls_", "tidy_webobj" => $this->webContainer."SHOW_CALENDAR", "tidy_module" => $this->webContainer."SHOW_CALENDAR"));

		$form_label="Manage Calendar Confirm";
		$out  ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "<page_options>";
		$out .= "<header><![CDATA[".LOCALE_INFO_DIRECTORY_HEADER." - $form_label]]></header>";
		$out .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$this->module_command."LIST",LOCALE_CANCEL));
		$out .="</page_options>";
		$out .="	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .="		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."SAVE_CALENDAR\" />";
		$out .="		<input type=\"hidden\" name=\"identifier\" value=\"$identifier\" />";
		$out .="		<page_sections>";
		$out .="			<section label='Confirmation'>";
		$out .="				<text><![CDATA[Thanks the Calendar has been positioned]]></text>";
		$out .="			</section>";
		$out .="		</page_sections>";
		$out .="	</form>";
		$out .="</module>";
		return $out;
	}
	/*************************************************************************************************************************
	* list of entries that can be managed
	*
	* @param Array ("identifier", "status_filter", "keywords")
	* @return String XML formated string holding the representation of the list of entries
	*************************************************************************************************************************/
	function list_entries($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier");
		$status_filter	= $this->check_parameters($parameters,"status_filter",-1);
		$keywords		= $this->check_parameters($parameters,"keywords");
		$status_sql = "";
		if ($status_filter!=-1){
			$status_sql .= "ie_status = $status_filter and ";
		} 
		if($keywords!=""){
			$status_sql .= " md_title like '%$keywords%' and ";
		}
		$sql = "select md_date_remove, ie_identifier, ie_version_major, ie_version_minor, ie_parent, ie_status, md_title, md_link_group_id, md_quantity, info_update_access, info_vcontrol from information_entry 
					inner join metadata_details on md_module = '$this->webContainer' and md_client=ie_client and ie_identifier = md_link_id
					inner join information_list on info_identifier = ie_list and info_client = ie_client
				where $status_sql ie_client = $this->client_identifier and ie_list = $identifier and ie_version_wip =1 order by ie_status asc, md_date_remove desc";
		$result = $this->parent->db_pointer->database_query($sql);
		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$variables = Array();
		$variables["FILTER"]			= $this->filter_entries($parameters,$this->module_command."LIST_ENTRIES");
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["HEADER"]			= LOCALE_INFO_DIRECTORY_HEADER." - Entry manager";
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			return "";
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$this->page_size = 20;
			$prev = $this->page_size;
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = $this->check_parameters($parameters,"page",1);
			$goto = ((--$page)*$this->page_size);
			if (($goto!=0)&&($number_of_records>$goto)){
				$this->call_command("DB_SEEK",array($result,$goto));
			}
			if ($goto+$this->page_size>$number_of_records){
				$finish = $number_of_records;
			}else{
				$finish = $goto+$this->page_size;
			}
			$goto++;
			$page++;
			
			$num_pages=floor($number_of_records / $this->page_size);
			$remainder = $number_of_records % $this->page_size;
			if ($remainder>0){
				$num_pages++;
			}
			$variables["PAGE_BUTTONS"] = Array();
			if($this->manage_database_list==1){
				$variables["PAGE_BUTTONS"][0] = Array("CANCEL",$this->module_command."LIST", "Cancel");
			}
			if ($this->author_admin_access == 1){
				$variables["PAGE_BUTTONS"][count($variables["PAGE_BUTTONS"])] = Array("ADD",$this->module_command."ADD&amp;list_id=".$identifier, ADD_NEW);
			}
			
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			//			$variables["PAGE_COMMAND"]		= "PAGE_VIEW_COMMENTS&amp;identifier=$identifier";
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
			
			$variables["END_PAGE"]			= $end_page;
			$variables["RESULT_ENTRIES"] =Array();
			$counter=0;
//			$page_discussion=0;
//			$trans_page=0;
			$lockable=1;
			while (($r = $this->parent->db_pointer->database_fetch_array($result)) &&($counter<$this->page_size)){
				$counter++;
				$index=count($variables["RESULT_ENTRIES"]);
				$lockable = $r["info_update_access"];
				if ($this->check_parameters($r,"ie_status","0")=="0"){
					$ie_status = "Requires Approval";
				} else {
					$ie_status = "Approved";
				}
				$event_status = $this->call_command("SHOP_CHECK_STATUS_LEVELS",Array("item_group"=>$r["md_link_group_id"]));
				$d = split(" ", $this->check_parameters($r,"md_date_remove","NA argh"));
				$quantity = $r["md_quantity"];
				if($quantity==-1){
					$quantity="Unlimited";
				}
				$variables["RESULT_ENTRIES"][$index]=Array(
					"identifier"	=> $r["ie_identifier"],
					"ENTRY_BUTTONS"	=> Array(),
					"attributes"	=> Array(
						Array("Date", $d[0],"Yes","NO"),
						Array(LOCALE_TITLE,	$this->check_parameters($r,"md_title",""),"TITLE","NO"),
						Array("Status",	$ie_status,"YES","NO"),
						Array("Seats", $quantity,"YES","NO"),
						Array("Booked", $event_status["Booked"],"YES","GET_BOOKED"),
						Array("Reserved", $event_status["Reserved"],"YES","GET_RESERVED"),
//						Array("Rejected", $event_status["Rejected"],"",""),
						Array("Invoice Required", $event_status["Invoice"],"YES","GET_INVOICE"),
						Array("GET_BOOKED", "?command=SHOP_ITEM_PURCHASE_HISTORY&amp;group_id=".$r["md_link_group_id"]."&amp;type=booked&amp;return=".$this->module_command."LIST_ENTRIES&amp;list=$identifier","NO","NO"),
						Array("GET_RESERVED", "?command=SHOP_ITEM_PURCHASE_HISTORY&amp;group_id=".$r["md_link_group_id"]."&amp;type=reserved&amp;return=".$this->module_command."LIST_ENTRIES&amp;list=$identifier","NO","NO"),
						Array("GET_INVOICE", "?command=SHOP_ITEM_PURCHASE_HISTORY&amp;group_id=".$r["md_link_group_id"]."&amp;type=invoice&amp;return=".$this->module_command."LIST_ENTRIES&amp;list=$identifier","NO","NO")
					)
				);
				if($r["info_vcontrol"]!=11){
					$ai = count($variables["RESULT_ENTRIES"][$index]["attributes"]);
					$variables["RESULT_ENTRIES"][$index]["attributes"][$ai] = Array("Version", $r["ie_version_major"].".".$r["ie_version_minor"],"YES","GET_VERSION");
					$variables["RESULT_ENTRIES"][$index]["attributes"][$ai+1] = Array("GET_VERSION", "?command=".$this->module_command."ENTRY_HISTORY&amp;list_id=".$identifier."&amp;parent_id=".$r["ie_parent"],"NO","NO");
				}

				if ($this->author_admin_access || $this->approve_admin_access){
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("EDIT",$this->module_command."EDIT&amp;list_id=".$identifier,EDIT_EXISTING);
					$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("REMOVE",$this->module_command."REMOVE&amp;list_id=".$identifier,REMOVE_EXISTING);
					if($lockable==2){
						$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = Array("HISTORY",$this->module_command."ENTRY_USERS&amp;list_id=".$identifier."&amp;parent_id=".$r["ie_parent"],"View User Lock");
					}
				}
//				$variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$index]["ENTRY_BUTTONS"])] = array("PURCHASE",,	"Purchase H");
			}
			$this->parent->db_pointer->database_free_result($result);
			$this->page_size = $prev;
			$variables["as"]="table";
			return $this->generate_list($variables);
		}
	}


}
?>