<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.standard_forms.php
* @date 09 Oct 2002
*/
/**
* 
*/
class formbuilder extends module{
	/**
	*  Class Variables
	*/
	var $module_name_label			= "Form Builder (Presentation) Module";
	var $module_name				= "formbuilder";		// name of module is used in configuration
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";// what group does this module belong to
	var $module_label				= "MANAGEMENT_FORM_BUILDER";			// label describing the module 
	var $module_creation			= "26/11/2004";							// date module was created
	var $module_modify	 			= '$Date: 2005/02/25 17:51:46 $';
	var $module_version 			= '$Revision: 1.25 $';					// Actual version of this module
	var $module_admin				= "0";									// does this system have an administrative section
	var $module_command				= "FORMBUILDER_";						// what does this commad start with ie TEMP_ (use caps)
	var $webContainer				= "FORMBUILDER_";						// what does this commad start with ie TEMP_ (use caps)
	var $displayed_form				= 0;
	var $currency					="[[pound]]";
	/**
	*  Class Variables
	*/
	var $preferences = Array();
	var $display	 = 0;
	var $shown_ids	 = Array();
	/**
	*  Class Methods
	*/
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug ){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command"));
		}
		/**
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*/
		if (strpos($user_command,$this->module_command)===0){
			/**
			* basic commands
			*/
			if ($user_command==$this->module_command."DEBUG_ON"){
				$this->module_debug=true;
			}
			if ($user_command==$this->module_command."DEBUG_OFF"){
				$this->module_debug=false;
			}

			if ($user_command==$this->module_command."GET_PREFS"){
				return $this->preferences;
			}
			if ($user_command==$this->module_command."GET_MODULE"){
				return $this->get_module_name();
			}
			if ($user_command==$this->module_command."GET_VERSION"){
				return $this->get_module_version();
			}
			if ($user_command==$this->module_command."LOAD_PREFS"){
				return $this->load_prefs();
			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->module_display($parameter_list);
			}
			if ($user_command==$this->module_command."EDIT"){
				return $this->module_display($parameter_list);
			}
			if ($user_command==$this->module_command."SAVE"){
				return $this->module_save($parameter_list);
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	/**
	* call the initialisation function only when this module is created
	*/
	
	function initialise(){
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* load the locale
		*/
		$this->load_locale("formbuilder");
		/**
		* define some access functionality
		*/
		$this->module_display_options 		= array();
		$this->available_forms				= array();//"SFORM_DISPLAY_CONTACT_US"
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function module_display($parameters){
		$lang="en";
		$identifier = $this->check_parameters($parameters,"wo_owner_id",-1);		
		
		if ($identifier == -1){
			$identifier = $this->check_parameters($parameters,"identifier",-1);
		}
		
		if (!in_array($identifier, $this->shown_ids)){
			$this->shown_ids[count($this->shown_ids)] = $identifier;
		} else {						
			return  "";
		}
		/*
		foreach ($parameters as $key => $val) {
			print '<li>'.$key.'  '.$val.'</li>';
		}
		*/
//		print "<li>".$identifier."</li>";
//		print "<pre>";
//		print_r($this->shown_ids);
//		print "</pre>";
		$previous_cost = 0;
		$cmd 		= $this->check_parameters($parameters,"command",$this->module_command."DISPLAY");
		$user 		= $this->check_parameters($_SESSION, "SESSION_USER_IDENTIFIER", -1);
		$formname 	= $this->check_parameters($parameters, "formname", "form_$identifier");
		$show_module= $this->check_parameters($parameters,"show_module",1);
		$show_anyway= $this->check_parameters($parameters,"show_anyway",0);		
		$page 		= $this->check_parameters($parameters,"page",1);
		$lpos 		= $this->check_parameters($parameters,"__layout_position","__NO_DISPLAY__");		

		if (($lpos==2 && $cmd!=$this->module_command."DISPLAY")){// || ($this->display==1 && $show_anyway==0)){
			return "";
		}
		
		$cml 		= $this->check_parameters($parameters,"current_menu_location"); 
		$module_fields			= Array();
		$data					= Array("errorCount"=>0);
		if($show_anyway==1){
			$sql = "select * from formbuilder_settings 
						inner join metadata_details on md_module = '$this->webContainer' and md_client=fbs_client and fbs_identifier = md_link_id
					where fbs_identifier = $identifier and fbs_client=$this->client_identifier";
		} else {
			$sql = "select * from formbuilder_settings 
						inner join menu_to_object on mto_module ='$this->webContainer' and mto_object = fbs_identifier and mto_client=fbs_client and mto_menu = '$cml'
						inner join metadata_details on md_module = '$this->webContainer' and md_client=fbs_client and fbs_identifier = md_link_id
					where fbs_identifier = $identifier and fbs_client=$this->client_identifier";
		}

	
		$result  = $this->parent->db_pointer->database_query($sql);
		$id=-1;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
           	$fbs_label				= $r["md_title"];
           	$fbs_ecommerce			= $r["fbs_ecommerce"];
           	$fbs_pricingstructure	= $r["fbs_pricingstructure"];
           	$fbs_fixedprice			= $r["md_price"];
           	$fbs_price_link			= $r["fbs_price_link"];
           	$fbs_fieldcount			= $r["fbs_fieldcount"];
			$all_locations			= $r["fbs_all_locations"];
			$set_inheritance		= $r["fbs_set_inheritance"];
			$id 					= $r["fbs_identifier"];
			$fbs_type				= $r["fbs_type"];
        }
		if($id == -1){
			return "";
		}
		$this->parent->db_pointer->database_free_result($result);
		if($fbs_ecommerce==1){
			$settings = $this->call_command("SHOP_GET_SETTINGS");
		}
		$this->display=1;
		$error="";
		$seperator_page=0;
		$sp_counter=1; // start at page 2
		$sql = "select * from formbuilder_field_map where fbfm_client = $this->client_identifier and fbfm_setting=$identifier order by fbfm_rank";
		$result  = $this->parent->db_pointer->database_query($sql);
		$out ="";
		$total_pages=1;
		$previous_cost_value = $this->check_parameters($parameters,"previous_cost_value");
		$errorCount=0;
		$error=Array();
		$module_list = Array();
		$useraccount_details = 0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$pos = count($module_fields);
			if(!in_array($r["fbfm_belongs"],$module_list)){
				$module_list[count($module_list)] = $r["fbfm_belongs"];
			}
			if($r["fbfm_type"]=="pagesplitter"){
				$seperator_page=1;
				$total_pages++;
			}
			if($user !=-1 && $r["fbfm_belongs"] == 'USERS_::-1'){
			} else {
	    	   	$module_fields[$pos] = Array($r["fbfm_fieldname"], $r["fbfm_label"], $r["fbfm_type"], $r["fbfm_map"], $r["fbfm_auto"], $r["fbfm_belongs"], $r["fbfm_labelpos"], "value"=>$this->check_locale_starter($this->check_parameters($parameters,$r["fbfm_fieldname"])), "required"=>$r["fbfm_required"]);
    			if(($module_fields[$pos]["required"]=="yes" || $module_fields[$pos]["required"]=="1") && ($page-1)==$total_pages && $page!=1){
					if($module_fields[$pos]["value"]==""){
						$errorCount++;
						$error[count($error)] = Array($module_fields[$pos][0], "<li>You did not fill in this field (".$module_fields[$pos][1].")</li>");
					}
					if($module_fields[$pos][2]=="password"){
						if($module_fields[$pos]["value"]!=$this->check_locale_starter($this->check_parameters($parameters,$r["fbfm_fieldname"]."_confirm"))){
							$errorCount++;
							$error[count($error)] = Array($module_fields[$pos][0], "<li>Your passwords do not match</li>");
						}
						if(strlen($module_fields[$pos]["value"])<6){
							$errorCount++;
							$error[count($error)] = Array($module_fields[$pos][0], "<li>Your passwords must be longer than 6 characters</li>");
						}
					}
				}
			}
        }
        $this->parent->db_pointer->database_free_result($result);
		$hidden = "";
		if ($fbs_type==0){
			if($user !=-1){
				$hidden = "<input type='hidden' name='frm_action' value='EDIT'></input>";
				$values = Array();
				for($i=0;$i<count($module_list);$i++){
					$data = split("::",$module_list[$i]);
					$command = str_replace("ADMIN","",$data[0]);
					$new_fields = $this->call_command($command."LOAD_FBA", Array("module_identifier"=>$data[1], "mod_fields" =>$module_fields, "parameters" => $parameters)); //, 
					if($new_fields!=""){
						$module_fields = $new_fields;
					}
				}
			}
		}
		if($errorCount>0){
			$page--;
		}

		$oFile = "<uid>".md5(uniqid(rand(), true))."</uid>";
		if ($fbs_type==0){
			if (($page==$total_pages)||($seperator_page==0)){
				if (
					($this->parent->sp_ssl_available=="Yes") && ($this->parent->domain != $this->parent->DEV_SERVER)
				){
					$oFile  .= "<form label='$fbs_label' method='post' name='$formname' action='https://".$this->parent->domain."".$this->parent->base."".$this->parent->real_script."'>\n";
				} else {
					$oFile  .= "<form label='$fbs_label' method='post' name='$formname' action='http://".$this->parent->domain."".$this->parent->base."".$this->parent->real_script."'>\n";
				}
			} else {
				if (
					($this->parent->sp_ssl_available=="Yes") &&	($this->parent->domain != $this->parent->DEV_SERVER)
				){
					$oFile  .= "<form label='$fbs_label' method='post' name='$formname' action='https://".$this->parent->domain."".$this->parent->base."".$this->parent->real_script."'>\n";
				} else {
					$oFile  .= "<form label='$fbs_label' method='post' name='$formname' action='http://".$this->parent->domain."".$this->parent->base."".$this->parent->real_script."'>\n";
				}
			}
			if ($seperator_page==0){
				$oFile  .= "<input type='hidden' name='command' value='FORMBUILDER_SAVE'/>\n";
				$oFile  .= "<input type='hidden' name='page' value='".($page + 1)."'/>\n";
			} else {
				if ($page==$total_pages){
					$oFile  .= "<input type='hidden' name='command' value='FORMBUILDER_SAVE'/>\n";
					/*	OLD TECH. WE DONT NEED INCREMENT IN PAGE IF ON LAST PAGE
						$oFile  .= "<input type='hidden' name='page' value='".($page + 1)."'/>\n";
					*/					
					$oFile  .= "<input type='hidden' name='page' value='".($page + 1)."'/>\n";					
					//$oFile  .= "<input type='hidden' name='page' value='".$page."'/>\n";
				} else {
					$oFile  .= "<input type='hidden' name='command' value='FORMBUILDER_DISPLAY'/>\n";
					$oFile  .= "<input type='hidden' name='page' value='".($page + 1)."'/>\n";
				}
			}
		} else {
			$sql = "select * from formbuilder_field_map where fbfm_setting = '$identifier' and fbfm_client = $this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$fbfm_belongs = split("::",$r["fbfm_belongs"]);
				$identifier = $fbfm_belongs[1];
				$mod = join("",split("ADMIN",$fbfm_belongs[0]));
            }
            $this->parent->db_pointer->database_free_result($result);
			$sql = "select * from information_list 
						inner join menu_data on menu_identifier = info_menu_location and info_client = menu_client
					where info_client = $this->client_identifier and info_identifier = $identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
            while($r = $this->parent->db_pointer->database_fetch_array($result)){
            	$menu_url = dirname($r["menu_url"])."/_search.php";
            }
            $this->parent->db_pointer->database_free_result($result);
			$oFile  = "<form label='$fbs_label' method='get' name='$formname' action='$menu_url'>\n";
//			$oFile  .= "<input type='hidden' name='command' value='".$mod."ADVANCED_SEARCH'/>\n";
			$oFile  .= "<input type='hidden' name='search' value='1'/>";
		}
		$oFile .= $hidden;
		$oFile .= "<input type='hidden' name='identifier' value='$identifier'/>\n";
		$page_label = $fbs_label;
		if ($fbs_type==0){
			if($page==1){
				$out .="<text class='pagelabel'><![CDATA[";
				$out .= $page_label;
				$out .= "]]></text>\n";
			}
		}
		for ($i=0;$i<count($module_fields);$i++){
			$key = $module_fields[$i][0];
//			print "<li>$key = ".$module_fields[$i][2]."   value = " "</li>";
			$val = $this->check_locale_starter($this->check_parameters($parameters,"$key","__NOT_FOUND__"));
			if($fbs_price_link == $module_fields[$i][0]){
				$previous_cost_value = strip_tags(str_replace(Array("\r","\n"), Array("","[[altreturn]]"), $this->html_2_txt(html_entity_decode($module_fields[$i]["value"]))));
			}
			if($val != "__NOT_FOUND__"){
				if (($module_fields[$i][2] == "memo") || ($module_fields[$i][2] == "smallmemo")) {
					$module_fields[$i]["value"] = strip_tags(str_replace(Array("\r","\n","\""), Array("","[[altreturn]]","[[doublequote]]"), $val));
				} else {
					$module_fields[$i]["value"] =$val;
				}
			} else {
				if(!is_array($module_fields[$i]["value"]))
					$module_fields[$i]["value"] = strip_tags(str_replace(Array("\r","\n"), Array("","[[altreturn]]"), $this->html_2_txt(html_entity_decode($module_fields[$i]["value"]))));
			}
		}
		for ($i=0;$i<count($module_fields);$i++){
			$key 	= $module_fields[$i][0];
			$label	= $module_fields[$i][1];
			//print "<li>$key -> $label  -> ".$module_fields[$i][2]." value ->".$module_fields[$i]["value"] ." sep_page->".$seperator_page." </li>";													
			if($seperator_page==0){
				if($module_fields[$i][2]=="__search__"){
					$out .="<input type=\"text\" name=\"search_phrase\" label=\"".$label."\" size=\"255\"";
					if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
						$out .= ' required="YES"';
					}
					$out.="><![CDATA[]]></input>\n";
				}
				if($module_fields[$i][2]=="hidden"){
					$oFile  .= "<input type='hidden' name='$key' value='".$module_fields[$i]["value"]."'/>\n";
				}
				if($module_fields[$i][2]=="label"){
					$val = $this->html_2_txt($module_fields[$i][1]);
					if($val!=""){
						$out .="<text class='textlabel'><![CDATA[".$val."]]></text>\n";
					}
				}
				if($module_fields[$i][2]=="colsplitter"){
					$out .="</seperator><seperator>\n";
				}
				if($module_fields[$i][2]=="rowsplitter"){
					$out .="</seperator></seperator_row><seperator_row><seperator>\n";
				}
				if ($fbs_type==0){
					if($module_fields[$i][2]=="pagesplitter"){
						$sp_counter++;
						$out .="</seperator></seperator_row></seperator_page><seperator_page id='$sp_counter'>";
						$val = $this->html_2_txt($module_fields[$i][1]);
						if($val!=""){
							$out .="<text class='pagelabel'><![CDATA[";
							$out .= $val;
							$out .= "]]></text>\n";
						}
						$out .="<seperator_row><seperator>\n";
					}
				}
				if ($key == "ie_quantity"){
					$out .="<input type=\"quantity\" name=\"".$key."\" label=\"".$label."\" size=\"255\"";
					if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
						$out .= ' required="YES"';
					}
					$val = strip_tags(html_entity_decode($module_fields[$i]["value"]));
					if ($val==""){
						$val=-1;
					}
					$out.="><![CDATA[".$val."]]></input>\n";
				}
				if($module_fields[$i][2]=="text" || $module_fields[$i][2]=="URL" || $module_fields[$i][2]=="email" || $module_fields[$i][2]=="double"){
					$out .="<input type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"255\"";
					if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
						$out .= ' required="YES"';
					}
					$out.="><![CDATA[".$this->makeCleanOutputforXSL(html_entity_decode($module_fields[$i]["value"]))."]]></input>\n";
				}
				if($module_fields[$i][2]=="password"){
					$out .="<input type=\"password\" name=\"".$key."\" label=\"".$label."\" size=\"255\"><![CDATA[]]></input>\n";
					$out .="<input type=\"password\" name=\"".$key."_confirm\" label=\"Please Confirm\" size=\"255\"><![CDATA[]]></input>\n";
				}
				if($module_fields[$i][2]=="smallmemo"){
					if ($fbs_type==0){
						$out .="<textarea type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"40\" height=\"6\"";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$out .= "><![CDATA[";
						$out .= $this->html_2_txt($module_fields[$i]["value"]);
						$out .= "]]></textarea>\n";
					} else {
						$out .="<input type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"255\"><![CDATA[".$this->makeCleanOutputforXSL(html_entity_decode($module_fields[$i]["value"]))."]]></input>\n";
					}
				}
				if($module_fields[$i][2]=="memo"){
					if ($fbs_type==0){
						$out .="<textarea ";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$out .= " type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"40\" height=\"12\"><![CDATA[";
						$out .= $this->html_2_txt($module_fields[$i]["value"]);
						$out .= "]]></textarea>\n";
					} else {
						$out .="<input type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"255\"><![CDATA[".$this->makeCleanOutputforXSL(html_entity_decode($module_fields[$i]["value"]))."]]></input>\n";
					}
				}
				
				if($module_fields[$i][2]=="datetime" || $module_fields[$i][2]=="date_time" || $module_fields[$i][2]=="date" || $module_fields[$i][2]=="time" ){
				$year_start = Date("Y");
				$year_finish =$year_start+1;
				$d = strip_tags(html_entity_decode($module_fields[$i]["value"]));
				if($d==""){
					$d = Date("Y-m-d ");
				}
//				print "[$year_start, $year_finish]";
						$out.= "<input type=\"date_time\" name=\"".$key."\" label=\"".$label."\" size=\"255\" year_start=\"$year_start\" year_end=\"$year_finish\" value='".$d."'/>\n";
//						$out .="<input type=\"".$module_fields[$i][2]."\" name=\"".$key."\" label=\"".$label."\" size=\"255\"><![CDATA[".strip_tags(html_entity_decode($module_fields[$i]["value"]))."]]></input>\n";
				}
				
				if (($module_fields[$i][2]=="radio") || ($module_fields[$i][2]=="select") || ($module_fields[$i][2]=="check") || ($module_fields[$i][2]=="list")){
					$fb_price = Array();
					$price=0;
					if($fbs_price_link == $module_fields[$i][0]){
						$price=1;
						$sql = "select * from formbuilder_price where fbp_client = $this->client_identifier and fbp_setting=$identifier";
						$price_result  = $this->parent->db_pointer->database_query($sql);
                        while($price_r = $this->call_command("DB_FETCH_ARRAY",Array($price_result))){
                           	$fb_price[$price_r["fbp_value"]] = $price_r["fbp_price"];
                        }
                        $this->call_command("DB_FREE",Array($price_result));
					}
					$details  = split("::",$module_fields[$i][5]);
					$fdata = $this->call_command($details[0]."GET_FIELD_OPTIONS", Array("identifier" => $details[1], "field"=>$module_fields[$i][0] ,"as" =>"Array", "selected"=>$module_fields[$i]["value"]));
					$val = $module_fields[$i]["value"];
					$m = count($fdata);
					if ($m>0){
						$prevSection = "";
						for($z=0;$z<$m;$z++){
							if ($fdata[$z]["section"] != $prevSection){
								if ($module_fields[$i]["value"] != ""){
									$module_fields[$i]["value"] .= "</optgroup>";
								}
								$prevSection = $fdata[$z]["section"];
								$module_fields[$i]["value"] .= "<optgroup label=\"".$prevSection."\">";
							}
							$module_fields[$i]["value"] .= "<option value=\"".$fdata[$z]["value"]."\"";
							if ($fdata[$z]["selected"] == "true"){
								$module_fields[$i]["value"] .= " selected='true'";
							}
							$module_fields[$i]["value"] .= "><![CDATA[".$fdata[$z]["label"];
							if($price==1){
								$module_fields[$i]["value"] .= " - [[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]] ".$fb_price[$fdata[$z]["label"]];
							}
							$module_fields[$i]["value"] .= "]]></option>";
						}
						if ($fdata[0]["section"]!=""){
							$module_fields[$i]["value"] .= "</optgroup>";
						}
					}
				}
				if ($fbs_type==0){
					if($module_fields[$i][2]=="radio"){
						$out .= "<radio type='vertical' ";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$out.=" name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</radio>\n";
					}
					if($module_fields[$i][2]=="select"){
						$module_fields[$i]["value"] = "<option value=\"\">Select one</option>".$module_fields[$i]["value"];
						$out .="<select ";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$out.=" name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
					}
					if($module_fields[$i][2]=="check"){
						$out .="<checkboxes ";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$out.=" type='vertical' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</checkboxes>\n";
					}
					if($module_fields[$i][2]=="list"){
						$out .="<select ";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$out.="multiple='1' size='10' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
					}
				} else {
					if (($module_fields[$i][2]=="radio") || ($module_fields[$i][2]=="select") || ($module_fields[$i][2]=="check") || ($module_fields[$i][2]=="list")){
						$module_fields[$i]["value"] = "<option value=\"\">Select one</option>".$module_fields[$i]["value"];
						$out .="<select";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$out.=" name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
					}						
				}
				if($module_fields[$i][2]=="__category__"){
					$details  = split("::",$module_fields[$i][5]);
					$v = $this->call_command($details[0]."GET_FIELD_OPTIONS", Array("identifier" => $details[1], "field"=>$module_fields[$i][0] ,"as" =>"Array", "selected" => $module_fields[$i]["value"], "limit"=>"first"));
					$module_fields[$i]["value"] = $v[0]["value"];
					$out .="<select name=\"".$key."\" label=\"".$label."\" required='YES'><option value=\"\">Select one</option>".$module_fields[$i]["value"]."</select>\n";
				}
				for($z=0;$z<$errorCount;$z++){
					if($error[$z][0] == "$key"){
						$out .="<text type=\"error\"><![CDATA[".$error[$z][1]."]]></text>";
					}
				}
			} else {
				if ($module_fields[$i][2]=="hidden"){
					$oFile  .= "<input type='hidden' name='$key' value='".$module_fields[$i]["value"]."'/>\n";
				}
				if($module_fields[$i][2]=="pagesplitter"){
					$sp_counter++;
					if($page == $sp_counter){
						$val = $this->html_2_txt($module_fields[$i][1]);
						if($val!=""){
							$out .="<text class='pagelabel'><![CDATA[";
							$out .= $val;
							$page_label = $val;
							$out .= "]]></text>\n";
						}
					}
				}
				if ($sp_counter==$page){
					if($module_fields[$i][2]=="label"){
						$out .="<text class='textlabel'><![CDATA[";
						$out .= $this->html_2_txt($module_fields[$i][1]);
						$out .= "]]></text>\n";
					}
					if($module_fields[$i][2]=="colsplitter"){
						$out .="</seperator><seperator>\n";
					}
					if($module_fields[$i][2]=="rowsplitter"){
						$out .="</seperator></seperator_row><seperator_row><seperator>\n";
					}
		
					if($module_fields[$i][2]=="text" || $module_fields[$i][2]=="URL" || $module_fields[$i][2]=="email" || $module_fields[$i][2]=="double" || $key == "ie_quantity"){
						if($key=='user_login_name'){
							$out .="<text><![CDATA[User names must be at least 6 characters long]]></text>";
						}
						$out .="<input type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"255\"";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$out.="><![CDATA[".$this->makeCleanOutputforXSL($module_fields[$i]["value"])."]]></input>\n";
					}
					if($module_fields[$i][2]=="password"){
						$out .="<text><![CDATA[Passwords must be at least 6 characters long]]></text>";
						$out .="<input type=\"password\" name=\"".$key."\" label=\"".$label."\" size=\"255\"";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$out.="><![CDATA[]]></input>\n";
						$out .="<input type=\"password\" name=\"".$key."_confirm\" label=\"Please Confirm\" size=\"255\"";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= " required=\"$key\"";
						}
						$out.="><![CDATA[]]></input>\n";
					}
					if($module_fields[$i][2]=="smallmemo"){
						$out .="<textarea";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$val = $this->html_2_txt(str_replace(Array("[[less]]","[[greater]]"), Array("<",">"), $module_fields[$i]["value"]));
						$out.=" type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"40\" height=\"6\"><![CDATA[".strip_tags($val)."]]></textarea>\n";
					}
					if($module_fields[$i][2]=="memo"){	
						$out .="<textarea";
						if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
							$out .= ' required="YES"';
						}
						$val = $this->html_2_txt(str_replace(Array("[[less]]","[[greater]]"), Array("<",">"), $module_fields[$i]["value"]));
						$out.=" type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"40\" height=\"12\"><![CDATA[".strip_tags($val)."]]></textarea>\n";
					}
					if($module_fields[$i][2]=="datetime" || $module_fields[$i][2]=="date_time" || $module_fields[$i][2]=="date" || $module_fields[$i][2]=="time" ){
						$year_start = Date("Y");
						$year_finish =$year_start+1;
						$d = strip_tags(html_entity_decode($module_fields[$i]["value"]));
						if($d==""){
							$d = Date("Y-m-d ");
						}
						$out.= "<input type=\"date_time\" name=\"".$key."\" label=\"".$label."\" size=\"255\" year_start=\"$year_start\" year_end=\"$year_finish\" value='".$d."'/>\n";
					}
					if (($module_fields[$i][2]=="radio") || ($module_fields[$i][2]=="select") || ($module_fields[$i][2]=="check") || ($module_fields[$i][2]=="list")){
						$fb_price = Array();
						$price=0;
						if($fbs_price_link == $module_fields[$i][0]){
							$price=1;
							$sql = "select * from formbuilder_price where fbp_client = $this->client_identifier and fbp_setting=$identifier";
							$price_result  = $this->parent->db_pointer->database_query($sql);
                            while($price_r = $this->call_command("DB_FETCH_ARRAY",Array($price_result))){
                            	$fb_price[$price_r["fbp_value"]] = $price_r["fbp_price"];
                            }
                            $this->call_command("DB_FREE",Array($price_result));
						}
						$details  = split("::",$module_fields[$i][5]);
						$fdata = $this->call_command($details[0]."GET_FIELD_OPTIONS", Array("identifier" => $details[1], "field"=>$module_fields[$i][0] ,"as" =>"Array", "selected"=>$module_fields[$i]["value"]));
						$val = $module_fields[$i]["value"];
						$module_fields[$i]["value"] = "";
						$m = count($fdata);
						if ($m>0){
							$prevSection = "";
							for($z=0;$z<$m;$z++){
								if ($fdata[$z]["section"] != $prevSection){
									if ($module_fields[$i]["value"] != ""){
										$module_fields[$i]["value"] .= "</optgroup>";
									}
									$prevSection = $fdata[$z]["section"];
									$module_fields[$i]["value"] .= "<optgroup label=\"".$prevSection."\">";
								}
								$module_fields[$i]["value"] .= "<option value=\"".$fdata[$z]["value"]."\"";
								if ($fdata[$z]["selected"] == "true"){
									$module_fields[$i]["value"] .= " selected='true'";
									if($price==1){
										$previous_cost = $fb_price[$fdata[$z]["label"]];
									}
								}
								$module_fields[$i]["value"] .= ">".$fdata[$z]["label"];
								if($price==1){
									$module_fields[$i]["value"] .= " - [[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]] ".$fb_price[$fdata[$z]["label"]];
								}
								$module_fields[$i]["value"] .= "</option>";
							}
							if ($fdata[0]["section"]!=""){
								$module_fields[$i]["value"] .= "</optgroup>";
							}
						}
					}
					if ($fbs_type==0){
						if($module_fields[$i][2]=="radio"){
							$out .="<radio";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.=" type='vertical' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</radio>\n";
						}
						if($module_fields[$i][2]=="select"){
							$out .="<select";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.=" name=\"".$key."\" label=\"".$label."\"><option value=''>Select One</option>".$module_fields[$i]["value"]."</select>\n";
						}
						if($module_fields[$i][2]=="check"){
							$out .="<checkboxes ";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.=" type='vertical' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</checkboxes>\n";
						}
						if($module_fields[$i][2]=="list"){
							$out .="<select ";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.="multiple='1' size='10' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
						}
					} else {
						if (($module_fields[$i][2]=="radio") || ($module_fields[$i][2]=="select") || ($module_fields[$i][2]=="check") || ($module_fields[$i][2]=="list")){
							$out .="<select name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
						}
					}
					if($module_fields[$i][2]=="__category__"){
						$details  = split("::",$module_fields[$i][5]);
						$v = $this->call_command($details[0]."GET_FIELD_OPTIONS", Array("identifier" => $details[1], "field"=>$module_fields[$i][0] ,"as" =>"Array", "selected"=>$module_fields[$i]["value"]));
						$module_fields[$i]["value"] = $v[0]["value"];
						$out .="<select name=\"".$key."\" label=\"".$label."\" required='YES'><option value=''>Select one</option>".$module_fields[$i]["value"]."</select>\n";
					}
					for($z=0;$z<$errorCount;$z++){
						if($error[$z][0] == "$key"){
							$out .="<text type=\"error\"><![CDATA[".$error[$z][1]."]]></text>";
						}
					}

				} else {
					if($module_fields[$i][2]=="pagesplitter"){
				//		$sp_counter++;
					}
					if($sp_counter<$page){
						if (is_array($module_fields[$i]["value"])){
							$m = count($module_fields[$i]["value"]);
							for($z=0;$z<$m;$z++){
								$hidden .="<input type='hidden' name=\"".$key."[]\"><![CDATA[".$this->makeCleanOutputforXSL($module_fields[$i]["value"][$z])."]]></input>\n";
							}
						} else {
							if($module_fields[$i][2]!="pagesplitter" && $module_fields[$i][2]!="colsplitter" && $module_fields[$i][2]!="rowsplitter"){
								$hidden .="<input type='hidden' name='".$key."' value='".$this->makeCleanOutputforXSL($module_fields[$i]["value"])."'/>\n";
							}
						}
					}
				}
			}
		}

		$oFile .= "$hidden<seperator_row><seperator>$out</seperator></seperator_row>";
		if ($fbs_type==0){
			if($seperator_page==0){
				$oFile .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
			} else {
				if($page==$total_pages){
					$oFile .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
				}else {
					$oFile .="		<input type=\"submit\" iconify=\"NEXT\" value=\"".LOCALE_NEXT."\" />";
				}
			}
		} else {
			$oFile .="		<input type=\"submit\" iconify=\"SEARCH\" value=\"".LOCALE_GO."\" />";
		}

		
		$oFile .="<input type='hidden' name='previous_cost_value' value='".str_replace(Array("'",'"'), Array("&#39;","&quot;"), $previous_cost_value)."'/>\n";
		$oFile .= "</form>";
		if($show_module==1){
			$out ="";
			$out .=" <module name=\"".$this->module_name."\" display=\"form\">$oFile</module>";
		} else {
			$out ="$oFile";
		}
		return $out;
	

	}
	
	/*************************************************************************************************************************
    * save the content of this form to multiple modules based on the fields used
    *************************************************************************************************************************/
	function module_save($parameters){
		/*************************************************************************************************************************
        * always charge unless user is confirmed and editing profile and then only when they don't change thier value of the price option
        *************************************************************************************************************************/
		$charge_for_update	= 1;
		$fbs_ecommerce		= 0;
		$email_body			= "";
		$frm_action 		= $this->check_parameters($parameters,"frm_action");
		$user 				= $this->check_parameters($_SESSION, "SESSION_USER_IDENTIFIER", -1);
		$msg				= "";
		$identifier 		= $this->check_parameters($parameters,"identifier",-1);
		$throughpage		= $this->check_parameters($parameters,"throughpage",0);
		$cml 				= $this->check_parameters($parameters,"current_menu_location"); 
		$previous_cost_value= $this->check_parameters($parameters,"previous_cost_value",0);
		/*
		foreach ($parameters as $key => $val) {
			print '<li>'.$key.' = '.$val.'</li>';
		}
		*/
		foreach ($parameters as $key => $value){
			if(!is_array($value)){
				$parameters[$key] = str_replace(Array("[[doublequote]]"), Array("\""),$value);
			}
		}

		$out ="";
//					inner join menu_to_object on mto_module ='$this->webContainer' and mto_object = fbs_identifier and mto_client=fbs_client and mto_menu = '$cml'
		/*************************************************************************************************************************
        * get settings for this form
        *************************************************************************************************************************/
		$sql = "select * from formbuilder_settings 
					inner join memo_information on mi_type ='$this->webContainer' and mi_link_id = fbs_identifier and mi_client=fbs_client and mi_field = 'fba_confirm'
					inner join metadata_details on md_module='$this->webContainer' and md_link_id = fbs_identifier and md_client=fbs_client
				where fbs_identifier = $identifier and fbs_client=$this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
		$module_fields			= Array();
		$id=-1;
		$msg = "Thank You,<br/><br/>Your entry has been submitted.";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$id 			= $r["fbs_identifier"];
			$stock_title	= $r["md_title"];
			$fbs_ecommerce 	= $r["fbs_ecommerce"];
			$fbs_price_link	= $r["fbs_price_link"];
			$fbs_fixedprice	= $r["md_price"];
			$msg			= $r["mi_memo"];
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * ok do we have a real id number (confirmed)
        *************************************************************************************************************************/
		if($id == -1){
			return "";
		}
		/*************************************************************************************************************************
        * extract mapped information 
        *************************************************************************************************************************/
		$map_results=Array();
		$sql = "select * from formbuilder_merge_map where fbmm_setting = $id and fbmm_client = $this->client_identifier";
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
		$result  = $this->parent->db_pointer->database_query($sql);
		$param_results = Array();
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if($r["fbmm_mapping"]!=""){
	        	$map = split("::",$r["fbmm_mapping"]);
				$map_results[count($map_results)] = $map;
				if($map[6]==1){
					$parameters[$map[1]] = $this->check_parameters($parameters,$map[0]);
					if(empty($param_results[$map[9]."::".$map[10]])){
						$param_results[$map[9]."::".$map[10]] = Array();
						$param_results[$map[9]."::".$map[10]]["module_command"] = $map[9];
						$param_results[$map[9]."::".$map[10]]["parameters"] = Array("module_identifier" => $map[10]);
						$param_results[$map[9]."::".$map[10]]["parameters"][$map[1]] = $this->check_parameters($parameters,$map[0]);
						$param_results[$map[9]."::".$map[10]]["map"] = Array();
					}
					$param_results[$map[9]."::".$map[10]]["map"][count($param_results[$map[9]."::".$map[10]]["map"])] = Array("from"=>$map[0],"to"=>$map[1], "attempt"=>0);
				} else {
					$parameters[$map[0]] = $this->check_parameters($parameters,$map[1]);
					if(empty($param_results[$map[7]."::".$map[8]])){
						$param_results[$map[7]."::".$map[8]] = Array();
						$param_results[$map[7]."::".$map[8]]["module_command"] = $map[7];
						$param_results[$map[7]."::".$map[8]]["parameters"] = Array("module_identifier" => $map[8]);
						$param_results[$map[7]."::".$map[8]]["parameters"][$map[0]] = $this->check_parameters($parameters,$map[1]);
						$param_results[$map[7]."::".$map[8]]["map"] = Array();
					}
					$param_results[$map[7]."::".$map[8]]["map"][count($param_results[$map[7]."::".$map[8]]["map"])] = Array("from"=>$map[1],"to"=>$map[0], "attempt"=>0);
	    		}
			}
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * extract the fields from the parameters passed to the function and the updated parameters as defined by mapping
        *************************************************************************************************************************/
		$sql = "select * from formbuilder_field_map where fbfm_setting = $id and fbfm_client = $this->client_identifier";
//		print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
		$result  = $this->parent->db_pointer->database_query($sql);
	    while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$field = $r["fbfm_fieldname"];
        	$key = split("::",$r["fbfm_belongs"]);
			$key[0] = str_replace("ADMIN","",$key[0]);
			if(empty($param_results[$r["fbfm_belongs"]])){
				$param_results[$r["fbfm_belongs"]] = Array();
				$param_results[$r["fbfm_belongs"]]["module_command"] = $key[0];
				$param_results[$r["fbfm_belongs"]]["parameters"] = Array(
					"module_identifier" => $key[1], 
					"fbs_identifier"	=> $id
				);
			}
			/*************************************************************************************************************************
            * if the user is logged in then reset the user details if not set to the users login session id
            *************************************************************************************************************************/
			if (($field=="contact_user") || ($field=="user_identifier")){
				$sval = $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
				$fval = $this->check_parameters($parameters,$field);
				if($fval=""){
					$parameters[$field] = $sval;
				}
			}
			if($r["fbfm_type"]=="date" || $r["fbfm_type"]=="datetime" || $r["fbfm_type"]=="time" || $r["fbfm_type"]=="date_time"){
				$param_results[$r["fbfm_belongs"]]["parameters"]["$field"] = $this->check_date($parameters,$field);
			} else if($r["fbfm_fieldname"]=="ie_quantity"){
				$value_of_entry = $this->check_parameters($parameters,"ie_quantity",-1);
				if ($value_of_entry==-2){
					$value_of_entry = $this->check_parameters($parameters,"ie_quantity_other","");
				}
				$param_results[$r["fbfm_belongs"]]["parameters"]["$field"] = $value_of_entry;
			} else {
				$param_results[$r["fbfm_belongs"]]["parameters"]["$field"] = $this->check_parameters($parameters,$field);
			}
        }
        $this->parent->db_pointer->database_free_result($result);
//		print_R($param_results);
		/*************************************************************************************************************************
        * if user form exists then call USERS_SAVE_FBA  which will return the user id for new / existing
        *************************************************************************************************************************/
		$data = Array("user_identifier"=>0,"errorCount"=>0);
		
		if ($frm_action!="EDIT"){
			if ("__NOT_FOUND__" != $this->check_parameters($param_results,"USERS_::-1","__NOT_FOUND__")){
				$param_results["USERS_::-1"]["parameters"]["fbs_identifier"] = $id;
				$param_results["USERS_::-1"]["parameters"]["user_login_pwd_confirm"] = $this->check_parameters($parameters,"user_login_pwd_confirm");
				$data  = $this->call_command($param_results["USERS_::-1"]["module_command"]."SAVE_FBA",$param_results["USERS_::-1"]["parameters"]);
				foreach ($param_results as $key => $module){
					$param_results[$key]["parameters"]["user_identifier"] = $data["user_identifier"];
				}
			} 
		}
		/*************************************************************************************************************************
        * merge data
        *************************************************************************************************************************/
		foreach($param_results as $key => $value){
			if("__NOT_FOUND__" != $this->check_parameters($param_results[$key], "map", "__NOT_FOUND__")){
				$m=count($param_results[$key]["map"]);
				for($i = 0; $i < $m; $i++ ){
					if ($this->check_parameters($parameters, $param_results[$key]["map"][$i]["to"])!=""){
						$param_results[$key]["parameters"][$param_results[$key]["map"][$i]["to"]] = $this->check_parameters($parameters, $param_results[$key]["map"][$i]["to"]);
					}
				}
			}
		}
		/*************************************************************************************************************************
        * check required fields
        *************************************************************************************************************************/
		$sql = "select * from formbuilder_field_map where fbfm_client = $this->client_identifier and fbfm_setting=$id order by fbfm_rank";
		if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
		$result  = $this->parent->db_pointer->database_query($sql);
		$total_pages	=1;
		$errorCount		=0;
		$error=Array();
		$seperator_page=0;
		$page 		= $this->check_parameters($parameters,"page",1); 

        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$pos = count($module_fields);
           	$module_fields[$pos] = Array($r["fbfm_fieldname"], $r["fbfm_label"], $r["fbfm_type"], $r["fbfm_map"], $r["fbfm_auto"], $r["fbfm_belongs"], $r["fbfm_labelpos"], "value"=>$this->check_locale_starter($this->check_parameters($parameters,$r["fbfm_fieldname"])), "required"=>$r["fbfm_required"]);
			if($r["fbfm_type"]=="pagesplitter"){
				$seperator_page=1;
				$total_pages++;
			}
			$throughpage==1;
			if(($module_fields[$pos]["required"]=="yes" || $module_fields[$pos]["required"]==1)){// && ($page)==$total_pages && $throughpage!=0){
			
				if($user==-1 || ($user!=-1 && $module_fields[$pos][5]!="USERS_::-1")){
					if($module_fields[$pos]["value"]==""){
						$errorCount++;
						$error[count($error)] = Array($module_fields[$pos][0], "<li>You did not fill in this field (".$module_fields[$pos][1].")</li>");
					}
					if($module_fields[$pos][2]=="password"){
						if($module_fields[$pos]["value"]!=$this->check_locale_starter($this->check_parameters($parameters,$r["fbfm_fieldname"]."_confirm"))){
							$errorCount++;
							$error[count($error)] = Array($module_fields[$pos][0], "<li>Your passwords do not match</li>");
						}
						if(strlen($module_fields[$pos]["value"])<6){
							$errorCount++;
//							$error[count($error)] = Array($module_fields[$pos][0], "<li>Your passwords must be longer than 6 characters</li>");
						}
					}
				}
			}
			if($module_fields[$pos][2]=="email" && ($module_fields[$pos]["required"]=="1" || strtolower($module_fields[$pos]["required"])=="yes")){
				if(!$this->check_email_address($module_fields[$pos]["value"])){
					$errorCount++;
					$error[count($error)] = Array($module_fields[$pos][0], "<li>You must specify a valid email address</li>");
				}
			}
        }
        /** CHECK IF CURRENT PAGE IS GREATER THAN TOTAL PAGES; */
        if ($page > $total_pages){
        	$page = $total_pages;
        }
        
        $this->parent->db_pointer->database_free_result($result);
		if($this->check_parameters($data,"errorCount",0)==0 && $errorCount==0){
			/*************************************************************************************************************************
    	    * call the save function for each module in turn
        	*************************************************************************************************************************/
			foreach ($param_results as $key => $module){
				if($module["module_command"]!="undefined" && $key !="USERS_::-1"){
					$cmd_to_execute = str_replace("ADMIN_","_",$module["module_command"]);
					$param_results[$key]["uid"] = $this->call_command($cmd_to_execute."SAVE_FBA",$module["parameters"]);
				}
			}
			/*************************************************************************************************************************
    	    * do we charge user
        	*************************************************************************************************************************/
			$previous_price = 0;
			if($fbs_fixedprice!=0 && $fbs_price_link==""){
				$stock_price = $fbs_fixedprice;
				$previous_price = $fbs_fixedprice;
			} else {
				$fbs_price_link_value = $this->check_parameters($parameters,"$fbs_price_link");
				if($previous_cost_value!="" && $previous_cost_value!=$fbs_price_link_value){
					$sql = "select * from formbuilder_price where fbp_setting = $id and fbp_client = $this->client_identifier and fbp_value in ('$fbs_price_link_value','$previous_cost_value')";
				} else {
					$sql = "select * from formbuilder_price where fbp_setting = $id and fbp_client = $this->client_identifier and fbp_value='$fbs_price_link_value'";
				}
				if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"[$sql]"));}
				$result  = $this->parent->db_pointer->database_query($sql);
				
				while($r = $this->parent->db_pointer->database_fetch_array($result)){
					if($previous_cost_value!="" && $previous_cost_value!=$fbs_price_link_value){
						if($r["fbp_value"]==$fbs_price_link_value){
							$stock_price	= $r["fbp_price"];
						} else {
							$previous_price = $r["fbp_price"];
						}
					} else {
						$stock_price	= $r["fbp_price"];
						if($previous_cost_value==$fbs_price_link_value){
							$previous_price = $r["fbp_price"];
						}
					}
				}
                $this->parent->db_pointer->database_free_result($result);
			}
			/*************************************************************************************************************************
    	    * do we charge user
			*
			* Value	Conditions
			*	T	review date has passed
			*	F 	we have already paid and have updated everything but are inside the rental period
        	*************************************************************************************************************************/
			$renewal=0;
			if($user!=-1){
				if ($fbs_ecommerce==1){
					/*************************************************************************************************************************
                    * if previous (paid) price is same then no purchase required
                    *************************************************************************************************************************/
					if($stock_price == $previous_price){
						$charge_for_update = 0;
					} else if ($stock_price < $previous_price){ 
						$charge_for_update = 0;
					}
					/*************************************************************************************************************************
                    * if review date has passed then charge full price for this is required
					* review date is number of seconds
					* now is current time in seconds
                    *************************************************************************************************************************/
					$now=$this->libertasGetTime();
					$review_date	= $this->check_parameters($_SESSION,"SESSION_ACCOUNT_DATE_REVIEW");
					if($review_date<$now){
						$renewal=1;
						$charge_for_update = 1;
					}
				}
			}
			if($fbs_ecommerce==1 && $charge_for_update==1){
				if($this->check_parameters($param_results,"CONTACT_::-1","__NOT_FOUND__")!= "__NOT_FOUND__"){
					$unique_contact_id = $this->check_parameters($param_results["CONTACT_::-1"],"uid",-1);
				}
				$cc_details = $this->call_command("CONTACT_CLONE",Array("contact_identifier"=> $unique_contact_id));
				$this->call_command("SHOP_CREATE_BASKET", Array("ucid"=>$cc_details, "basket_status"=>3));
				if($renewal==1){
					$this->call_command("SHOP_ADD_TO_BASKET",
						Array(
							"shop_item_stock_id"		=> $id,
							"shop_item_title"			=> $stock_title." (Renewal)",
							"shop_item_description"		=> 'Online form',
							"shop_item_pickup_price"	=> $stock_price,
							"shop_item_pickup_discount"	=> 0,
							"shop_item_quantity"		=> 1,
							"shop_item_weight"			=> 0,
							"trigger"					=> Array( // list of triggers to execute
								Array("cmd"=>"USERS_RENEWAL" , "params"=>Array("uid"=>$user))
							)
						)
					);
				} else {
					$this->call_command("SHOP_ADD_TO_BASKET",
						Array(
							"shop_item_stock_id"		=> $id,
							"shop_item_title"			=> $stock_title,
							"shop_item_description"		=> 'Online form',
							"shop_item_pickup_price"	=> $stock_price,
							"shop_item_pickup_discount"	=> 0,
							"shop_item_quantity"		=> 1,
							"shop_item_weight"			=> 0
						)
					);
				}
				$_SESSION["SHOP_CONTACTS"] = $unique_contact_id;
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$this->parent->base."_view-cart.php"));
			} else {
				if($fbs_ecommerce==1){
					/*************************************************************************************************************************
                    * Elert the approver of the information directory that an updated is required to be processed
                    *************************************************************************************************************************/
					$list_id=-1;
					foreach ($param_results as $key =>$val){
						$s = split("::",$key);
						if($s[0]=="INFORMATIONADMIN_"){
							$list_id=$s[1];
						}
					}
					$this->call_command("USERS_RENEWAL",Array("uid"=>$user));
					$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_INFODIR_APPROVER__"], "identifier" => $identifier, "url"=> "http://".$this->parent->domain.$this->parent->base."admin/index.php?command=INFORMATIONADMIN_LIST_ENTRIES&amp;status_filter=0&amp;identifier=$list_id", "email_body" => $email_body));
				}
				$out ="<module name=\"".$this->module_name."\" display=\"form\">
						<form name='user_form' label='Thank you'>
							<text><![CDATA[$msg]]></text>
						</form>
					</module>";
			}
		} else {
			$identifier = $this->check_parameters($parameters,"identifier");
			$sql = "select * from formbuilder_settings where fbs_identifier= $identifier and fbs_client = $this->client_identifier";
			$result  = $this->parent->db_pointer->database_query($sql);
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
	           	$fbs_label				= $r["fbs_label"];
	           	$fbs_ecommerce			= $r["fbs_ecommerce"];
	           	$fbs_pricingstructure	= $r["fbs_pricingstructure"];
	           	$fbs_fixedprice			= $r["fbs_fixedprice"];
	           	$fbs_price_link			= $r["fbs_price_link"];
	           	$fbs_fieldcount			= $r["fbs_fieldcount"];
				$all_locations			= $r["fbs_all_locations"];
				$set_inheritance		= $r["fbs_set_inheritance"];
	        }
			$sp_counter=1; // start at page 2
			$this->parent->db_pointer->database_free_result($result);
			if($errorCount>0){
				$page--;
			}
			$hidden="";
			$oFile  = "<form label='$fbs_label' method='post' name='form_$identifier'>\n";
			if($seperator_page==0){
				$oFile  .= "<input type='hidden' name='command' value='FORMBUILDER_SAVE'/>\n";
			} else {
				if($page==$total_pages){
					$oFile  .= "<input type='hidden' name='command' value='FORMBUILDER_SAVE'/>\n";
				}else {
					$oFile  .= "<input type='hidden' name='command' value='FORMBUILDER_DISPLAY'/>\n";
					$oFile  .= "<input type='hidden' name='page' value='".($page + 1)."'/>\n";
				}
			}
			$oFile  .= "<input type='hidden' name='identifier' value='$identifier'/>\n";
			for ($i=0;$i<count($module_fields);$i++){
				$key = $module_fields[$i][0];
				$label =  $module_fields[$i][1];
				if ($frm_action=="EDIT" && $module_fields[$i][5]=="USERS_::-1"){
				} else {
					if($seperator_page==0){
						if ($module_fields[$i][2]=="label"){
							$out .="<text class='textlabel'><![CDATA[";
							$out .= $this->html_2_txt($module_fields[$i][1]);
							$out .= "]]></text>\n";
						}
						if ($module_fields[$i][2]=="hidden"){
							if($key == "user_identifier" && $module_fields[$i]["value"]==""){
								$module_fields[$i]["value"] = $param_results[$module_fields[$i][5]]["parameters"]["user_identifier"];
							}
							$oFile  .= "<input type='hidden' name='$key' value='".$module_fields[$i]["value"]."'/>\n";
						}
						if ($module_fields[$i][2]=="colsplitter"){
							$out .="</seperator><seperator>\n";
						}
						if ($module_fields[$i][2]=="rowsplitter"){
							$out .="</seperator></seperator_row><seperator_row><seperator>\n";
						}
						if ($module_fields[$i][2]=="pagesplitter"){
							$sp_counter++;
							$out .="</seperator></seperator_row></seperator_page><seperator_page id='$sp_counter'><seperator_row><seperator>\n";
						}
						if ($module_fields[$i][2]=="datetime" || $module_fields[$i][2]=="date_time" || $module_fields[$i][2]=="date" || $module_fields[$i][2]=="time" ){
							$year_start = Date("Y");
							$year_finish =$year_start+1;
							$d = strip_tags(html_entity_decode($module_fields[$i]["value"]));
							if($d==""){
								$d = Date("Y-m-d ");
							}
							$out.= "<input type=\"date_time\" name=\"".$key."\" label=\"".$label."\" size=\"255\" year_start=\"$year_start\" year_end=\"$year_finish\" value='".$d."'/>\n";
						}
						if ($module_fields[$i][2]=="text" || $module_fields[$i][2]=="URL" || $module_fields[$i][2]=="email" || $module_fields[$i][2]=="double"){
							if($key=='user_login_name'){
								$out .="<text><![CDATA[User names must be at least 6 characters long]]></text>";
							}
							$out .="<input type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"255\"";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.="><![CDATA[".$module_fields[$i]["value"]."]]></input>\n";
						}
						if ($module_fields[$i][2]=="password"){
							$out .="<text><![CDATA[Passwords must be at least 6 characters long]]></text>";
							if($module_fields[$i]["value"]==""){
								$out .="<input type=\"password\" name=\"".$key."\" label=\"".$label."\" size=\"255\"><![CDATA[]]></input>\n";
								$out .="<input type=\"password\" name=\"".$key."_confirm\" label=\"Please Confirm\" size=\"255\"><![CDATA[]]></input>\n";
							} else {
								$out .="<input type=\"password\" name=\"".$key."\" label=\"".$label."\" size=\"255\"><![CDATA[__KEEP__]]></input>\n";
								$out .="<input type=\"password\" name=\"".$key."_confirm\" label=\"Please Confirm\" size=\"255\"><![CDATA[__KEEP__]]></input>\n";
							}
						}
						if($module_fields[$i][2]=="smallmemo"){
							$out .="<textarea type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"20\" height=\"6\"";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.="><![CDATA[".$module_fields[$i]["value"]."]]></textarea>\n";
						}
						if($module_fields[$i][2]=="memo"){
							$out .="<textarea ";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.=" type=\"text\" name=\"".$key."\" label=\"".$label." Confirm\" size=\"40\" height=\"12\"><![CDATA[".$module_fields[$i]["value"]."]]></textarea>\n";
						}
						
						if (($module_fields[$i][2]=="radio") || ($module_fields[$i][2]=="select") || ($module_fields[$i][2]=="check") || ($module_fields[$i][2]=="list")){
							$details  = split("::",$module_fields[$i][5]);
							$fdata = $this->call_command($details[0]."GET_FIELD_OPTIONS", Array("identifier" => $details[1], "field"=>$module_fields[$i][0] ,"as" =>"Array", "selected"=>$module_fields[$i]["value"]));
							$val = $module_fields[$i]["value"];
							$module_fields[$i]["value"] = "";
							$m = count($fdata);
							if ($m>0){
								$prevSection = "";
								for($z=0;$z<$m;$z++){
									if ($fdata[$z]["section"] != $prevSection){
										if ($module_fields[$i]["value"] != ""){
											$module_fields[$i]["value"] .= "</optgroup>";
										}
										$prevSection = $fdata[$z]["section"];
										$module_fields[$i]["value"] .= "<optgroup label='".$prevSection."'>";
									}
									$module_fields[$i]["value"] .= "<option value='".$fdata[$z]["value"]."'";
									if ($fdata[$z]["selected"] == "true"){
										$module_fields[$i]["value"] .= " selected='true'";
									}
									$module_fields[$i]["value"] .= ">".$fdata[$z]["label"]."</option>";
								}
								if ($fdata[0]["section"]!=""){
									$module_fields[$i]["value"] .= "</optgroup>";
								}
							}
						}
						if($module_fields[$i][2]=="radio"){
							$out .="<radio type='vertical' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</radio>\n";
						}
						if($module_fields[$i][2]=="select"){
							$out .="<select name=\"".$key."\" label=\"".$label."\"><option value=''>Select One</option>".$module_fields[$i]["value"]."</select>\n";
						}
						if($module_fields[$i][2]=="check"){
							$out .="<checkboxes type='vertical' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</checkboxes>\n";
						}
						if($module_fields[$i][2]=="list"){
							$out .="<select multiple='1' size='10' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
						}
						if($module_fields[$i][2]=="__category__"){
							$details  = split("::",$module_fields[$i][5]);
							$v = $this->call_command($details[0]."GET_FIELD_OPTIONS", Array("identifier" => $details[1], "field"=>$module_fields[$i][0] ,"as" =>"Array", "selected"=>$module_fields[$i]["value"]));
							$module_fields[$i]["value"] = $v[0]["value"];
							$out .="<select name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
						}
						if(is_array($error)){
							for($z=0;$z<$errorCount;$z++){
								if($error[$z][0] == "$key"){
									$out .="<text type=\"error\"><![CDATA[".$error[$z][1]."]]></text>";
								}
							}
						}
						for($z=0; $z<$this->check_parameters($data,"errorCount",0); $z++){
							if($data["error"][$z][0] == "$key"){
								$out .="<text type=\"error\"><![CDATA[".$data["error"][$z][1]."]]></text>";

							}
						}
					} else {
						if($module_fields[$i][2]=="hidden"){
							$oFile  .= "<input type='hidden' name='$key' value='".$module_fields[$i]["value"]."'/>\n";
						}
						//print '<li>sp coutner ='.$sp_counter.'  page= '.$page.'</li>';
						if($sp_counter==$page){
							if($module_fields[$i][2]=="label"){
								$out .="<text class='textlabel'><![CDATA[";
								$out .= $this->html_2_txt($module_fields[$i][1]);
								$out .= "]]></text>\n";
							}
							if($module_fields[$i][2]=="pagesplitter"){
								$sp_counter++;
							}
							if($module_fields[$i][2]=="colsplitter"){
								$out .="</seperator><seperator>\n";
							}
							if($module_fields[$i][2]=="rowsplitter"){
								$out .="</seperator></seperator_row><seperator_row><seperator>\n";
							}
							if($module_fields[$i][2]=="pagesplitter"){
								$sp_counter++;
	//							$out .="</seperator></seperator_row></seperator_page><seperator_page id='$sp_counter'><seperator_row><seperator>\n";
							}
							if ($module_fields[$i][2]=="datetime" || $module_fields[$i][2]=="date_time" || $module_fields[$i][2]=="date" || $module_fields[$i][2]=="time" ){
								$year_start = Date("Y");
								$year_finish =$year_start+1;
								$d = strip_tags(html_entity_decode($module_fields[$i]["value"]));
								if($d==""){
									$d = Date("Y-m-d ");
								}
								$out.= "<input type=\"date_time\" name=\"".$key."\" label=\"".$label."\" size=\"255\" year_start=\"$year_start\" year_end=\"$year_finish\" value='".$d."'/>\n";
							}
							if($module_fields[$i][2]=="text" || $module_fields[$i][2]=="URL" || $module_fields[$i][2]=="email" || $module_fields[$i][2]=="double"){
								if($key=='user_login_name'){
									$out .="<text><![CDATA[User names must be at least 6 characters long]]></text>";
								}
								$out .="<input type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"255\"";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.="><![CDATA[".$module_fields[$i]["value"]."]]></input>\n";
							}
							if($module_fields[$i][2]=="password"){
							$out .="<text><![CDATA[Passwords must be at least 6 characters long]]></text>";
								$out .="<input type=\"password\" name=\"".$key."\" label=\"".$label."\" size=\"255\"";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.="><![CDATA[]]></input>\n";
								$out .="<input type=\"password\" name=\"".$key."_confirm\" label=\"Please Confirm\" size=\"255\"";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= " required=\"$key\"";
							}
							$out.="><![CDATA[]]></input>\n";
							}
							if($module_fields[$i][2]=="smallmemo"){
								$out .="<textarea";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.=" type=\"text\" name=\"".$key."\" label=\"".$label."\" size=\"20\" height=\"6\"><![CDATA[".$module_fields[$i]["value"]."]]></textarea>\n";
							}
							if($module_fields[$i][2]=="memo"){
								$out .="<textarea";
							if ($module_fields[$i]["required"]=="yes" || $module_fields[$i]["required"]==1){
								$out .= ' required="YES"';
							}
							$out.=" type=\"text\" name=\"".$key."\" label=\"".$label." Confirm\" size=\"40\" height=\"12\"><![CDATA[".$module_fields[$i]["value"]."]]></textarea>\n";
							}
							
							if (($module_fields[$i][2]=="radio") || ($module_fields[$i][2]=="select") || ($module_fields[$i][2]=="check") || ($module_fields[$i][2]=="list")){
								$details  = split("::",$module_fields[$i][5]);
								$fdata = $this->call_command($details[0]."GET_FIELD_OPTIONS", Array("identifier" => $details[1], "field"=>$module_fields[$i][0] ,"as" =>"Array", "selected"=>$module_fields[$i]["value"]));
								$val = $module_fields[$i]["value"];
								$module_fields[$i]["value"] = "";
								$m = count($fdata);
								if ($m>0){
									$prevSection = "";
									for($z=0;$z<$m;$z++){
										if ($fdata[$z]["section"] != $prevSection){
											if ($module_fields[$i]["value"] != ""){
												$module_fields[$i]["value"] .= "</optgroup>";
											}
											$prevSection = $fdata[$z]["section"];
											$module_fields[$i]["value"] .= "<optgroup label='".$prevSection."'>";
										}
										$module_fields[$i]["value"] .= "<option value='".$fdata[$z]["value"]."'";
										if ($fdata[$z]["selected"] == "true"){
											$module_fields[$i]["value"] .= " selected='true'";
										}
										$module_fields[$i]["value"] .= ">".$fdata[$z]["label"]."</option>";
									}
									if ($fdata[0]["section"]!=""){
										$module_fields[$i]["value"] .= "</optgroup>";
									}
								}
							}
							if($module_fields[$i][2]=="radio"){
								$out .="<radio type='vertical' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</radio>\n";
							}
							if($module_fields[$i][2]=="select"){
								$out .="<select name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
							}
							if($module_fields[$i][2]=="check"){
								$out .="<checkboxes type='vertical' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</checkboxes>\n";
							}
							if($module_fields[$i][2]=="list"){
								$out .="<select multiple='1' size='10' name=\"".$key."\" label=\"".$label."\">".$module_fields[$i]["value"]."</select>\n";
							}
							if($module_fields[$i][2]=="__category__"){
								$details  = split("::",$module_fields[$i][5]);
								$v = $this->call_command($details[0]."GET_FIELD_OPTIONS", Array("identifier" => $details[1], "field"=>$module_fields[$i][0] ,"as" =>"Array", "selected"=>$module_fields[$i]["value"]));
								$module_fields[$i]["value"] = $v[0]["value"];
								$out .="<select name=\"".$key."\" label=\"".$label."\" required='YES'><option value=''>Select one</option>".$module_fields[$i]["value"]."</select>\n";
							}
							for($z=0;$z<$errorCount;$z++){
								if($error[$z][0] == "$key"){
									$out .="<text type=\"error\"><![CDATA[".$error[$z][1]."]]></text>";
								}
							}
							for($z=0; $z<$this->check_parameters($data,"errorCount",0); $z++){
								if($data["error"][$z][0] == "$key"){
									$out .="<text type=\"error\"><![CDATA[".$data["error"][$z][1]."]]></text>";
								}
							}
						} else {
							if($module_fields[$i][2]=="pagesplitter"){
								$sp_counter++;
							}
							if (is_array($module_fields[$i]["value"])){
								$m = count($module_fields[$i]["value"]);
								for($z=0;$z<$m;$z++){
									$out .="<input type='hidden' name=\"".$key."\">".str_replace(Array("'",'"'),Array("&#39;","&quot;"),$module_fields[$i]["value"][$z])."</input>\n";
								}
							} else {
								if($module_fields[$i][2]!="pagesplitter" && $module_fields[$i][2]!="colsplitter" && $module_fields[$i][2]!="rowsplitter"){
									$hidden .="<input type='hidden' name='".$key."' value='".str_replace(Array("'",'"'),Array("&#39;","&quot;"),$module_fields[$i]["value"])."'/>\n";
								}
							}
						}
					}
				}
			}
			$oFile .= "$hidden<seperator_row><seperator>$out</seperator></seperator_row>";
			if($seperator_page==0){
				$oFile .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
			} else {
				if($page==$total_pages){
					$oFile .="		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
				}else {					
					$oFile .="		<input type=\"submit\" iconify=\"NEXT\" value=\"".LOCALE_NEXT."\" />";
				}
			}
			$oFile .= "<uid>".md5(uniqid(rand(), true))."</uid>";
			$oFile .= "</form>";
			$out =" <module name=\"".$this->module_name."\" display=\"form\">$oFile</module>";
		}
		return $out;
	}
	
}
?>