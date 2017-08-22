<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.contenttable_.php
*/
/**
* This module is for producing micro menus for displaying ont he site.
*/
class contenttable extends module{
	/**
	*  Class Variables
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name				= "contenttable";
	var $module_name_label			= "Table of Contents (Presentation)";
	var $module_admin				= "0";
	var $module_command				= "CONTENTTABLE_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "CONTENTTABLE_";
	var $module_label				= "MANAGEMENT_CONTENTTABLE";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.8 $';
	var $module_creation 			= "13/08/2004";
	var $searched					= 0;
	
	var $admin_access				= 0;	
	var $admin_function_access		= 0;	
	
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
			/*
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			- Secure Administrative functions requires mode ADMIN and Role Access
			-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
			*/
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->module_display($parameter_list);
			}
			if ($user_command==$this->module_command."JUMPTO"){
				return $this->module_jumpto($parameter_list);
			}
			
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                			S E T U P   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- 
	- 
	*
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
		$this->load_locale($this->module_name);
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		$this->page_size				= $this->check_prefs(Array("sp_page_size"));
		return 1;
	}
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                         			P R E S E N T A T I O N   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/

	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: module_form(Arary())
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- used to displayt he managment for forthis item ( add and remove)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_display($parameters){
		//print_r($parameters);
		/**
		* Extract parameters
		*/ 
		$wo_owner_id  			= $this->check_parameters($parameters,"wo_owner_id",-1);
		$current_menu_location  = $this->check_parameters($parameters,"current_menu_location",-1);
		$cmd					= $this->check_parameters($parameters,"comamnd");
		$letter					= $this->check_parameters($parameters,"letter");
		$unset_identifier		= $this->check_parameters($parameters,"unset_identifier");
		$list 					= Array();
		$folders 				= Array();
		$outputlabel 			= "";
		$title_page				= "";
		$show_folders			= 0;
		$show_home				= 1;
		$menu_label				= "";
		/**
		* we have got to order these some how
		*/
		$sql = "
		select
			menu_sort_tag_value
		from menu_data 
			left outer join menu_sort on menu_sort.menu_sort_identifier = menu_data.menu_sort
		where
			menu_identifier = $current_menu_location and menu_client = $this->client_identifier";
		//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		$slideshow = $this->call_command("THEME_GET_STYLESHEET", Array());
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"display",__LINE__,"$type :: [$sql]"));
		}
		$list_order_by="";
		$result = $this->call_command("DB_QUERY",Array($sql));
		if ($result){
			while ($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$list_order_by = "order by 
					 menu_access_to_page.title_page desc ,".$r["menu_sort_tag_value"].", menu_access_to_page.trans_identifier , page_trans_data.trans_page";
			}
		}
		$slideshow_ok=0;
		if ($slideshow[2]=="LOCALE_THEME_012_TYPE_SLIDESHOW"){
			$slideshow_ok=1;
		}
		/**
		* Extract List of pages and menu locations 
		*/ 
		$sql = "select * from content_table where ct_identifier= $wo_owner_id and ct_status=1 and ct_client = $this->client_identifier";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$c=0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$show_folders		= $r["ct_show_folders"];
        	$show_label			= $r["ct_show_label"];
        	$label				= $r["ct_label"];
        	$display_type		= $r["ct_display_type"];
        	$show_menu			= $r["ct_show_menu"];
        	$show_home			= $r["ct_show_home"];
			$c++;
        }
        $this->call_command("DB_FREE",Array($result));
		/**
		* check if we should be displaying this at all (homepage)
		*/ 
		$ok =1;
		if($show_home==0 && $this->parent->real_script=="index.php"){
			$ok = 0;
		}
		if($ok){
			/*
	        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	        -
	        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	        */
			$sql = "select * from menu_data
				inner join display_data on menu_identifier = display_menu and menu_client=display_client
			where display_menu = $current_menu_location and display_client = $this->client_identifier and display_command like 'PRESENTATION_%'";
			//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
	        $result  = $this->call_command("DB_QUERY",Array($sql));
			$a2z=0;
	       	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				if($r["display_command"]=="PRESENTATION_ATOZ"){
					$a2z =1;
				}
				$menu_label = $r["menu_label"];
			}
	//		print "[$menu_label]";
			$this->call_command("DB_FREE",Array($result));
			/*
	        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	        -
	        -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	        */
			$counter=0;
			$out ="";
			if($c>0){
				if (false && $show_menu==0 && strpos($this->parent->real_script, "index.php") ){
				}else{
					$whereP	="";
					$whereM	="";
					$joinP	="";
					$joinM	="";
					if($show_menu==1 || !strpos($this->parent->real_script, "index.php")){
	 					/*
						-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						- define group conditions for both sql statements
						-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
						*/
						
						if ($this->call_command("ENGINE_HAS_MODULE",array("GROUP_"))==1){
							$grp = $this->check_parameters($_SESSION,"SESSION_GROUP");
							if (is_array($grp)){
								$grp_list ="";
								for($i=0,$m=count($grp);$i<$m;$i++){
									$grp_list .= $grp[$i]["IDENTIFIER"].", ";
								}
								$whereP .= "
									(
										(group_access_to_page.group_identifier is null or group_access_to_page.group_identifier in ($grp_list -1)) or 
										(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1))
									) and ";
								$whereM .= "(relate_menu_groups.group_identifier is null or relate_menu_groups.group_identifier in ($grp_list -1)) and ";
							} else {
								$whereM .= " (relate_menu_groups.group_identifier is null) and ";
								$whereP .= " (group_access_to_page.group_identifier is null) and ";
							}
							$joinM.=" left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier ";
							$joinP.=" left outer join group_access_to_page on page_trans_data.trans_identifier = group_access_to_page.trans_identifier
									 left outer join relate_menu_groups on relate_menu_groups.menu_identifier = menu_data.menu_identifier ";
						}
					}
					$out  = "";
					if($show_folders!=0){
						$sql = "select distinct menu_data.* from menu_data 
							$joinM
						where $whereM
						menu_parent = $current_menu_location and menu_client = $this->client_identifier order by menu_order";
//						print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
				        $result  = $this->call_command("DB_QUERY",Array($sql));
			        	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
							$pos = count($folders);
			        		$folders[$pos] = Array("url"=>$r["menu_url"], "label"=>$r["menu_label"], "title"=>$r["menu_alt_text"]);
			    	    }
						$this->call_command("DB_FREE",Array($result));
					}
					/*
	                -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	                - pages
	                -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	                */
					 if($show_menu==1 || !strpos($this->parent->real_script, "index.php") || $slideshow_ok){
						$dir_list = split("/", $this->parent->real_script);
						if (($a2z == 1 && $dir_list[count($dir_list)-1] != "index.php") || ($a2z==0)){
							$summary_parts 	= $this->call_command("MEMOINFO_GET_SQL_COMPONENTS", 
								Array(
									"table_as"			=> "ptd2",
									"field_as"			=> "summary",
									"identifier_field"	=> "page_trans_data.trans_identifier",
									"module_command"	=> "PAGE_",
									"client_field"		=> "trans_client",
									"mi_field"			=> "summary",
									"join_type"			=> "inner"
								)
							);
							$bypass=0;
							if (($a2z==1 && $letter!="") || (strpos($this->parent->real_script, "index.php") && $slideshow_ok==0)){
								$bypass=1;
		//						$whereP .=" (page_trans_data.trans_title like '$letter%' or title_page=1) and ";
							}
							if ($a2z==1 && $letter=="" && $unset_identifier!=""){
								$letter= substr($dir_list[count($dir_list)-1],0,1);
								$whereP .=" (page_trans_data.trans_title like '$letter%' or title_page=1) and ";
							}
//							$bypass=0;
							if($bypass==0){
								$sql = "select *, ".$summary_parts["return_field"]." from page_trans_data 
									inner join menu_access_to_page on page_trans_data.trans_identifier = menu_access_to_page.trans_identifier and menu_access_to_page.client_identifier=trans_client
									inner join menu_data on menu_access_to_page.menu_identifier = menu_data.menu_identifier and menu_access_to_page.client_identifier=menu_client
									$joinP
									".$summary_parts["join"]."
								where 
									$whereP
									menu_access_to_page.menu_identifier = $current_menu_location and 
									trans_client = $this->client_identifier and 
									trans_published_version=1 and 
									trans_doc_status=4
									$list_order_by
									";
	//							print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
						        $result  = $this->call_command("DB_QUERY",Array($sql));
								
						       	while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
									$pos = count($list);
									$url = dirname($r["menu_url"]);
									if($url=="."){
										$url="";
									}else {
										$url.="/";
									}
									$url.=$this->make_uri($r["trans_title"]).".php";
									$des = substr(strip_tags(html_entity_decode($r["summary"])),0,255);
									if($r["title_page"]==1){
										$title_page =$r["trans_title"];
									}
						       		$list[$pos] = Array("url"=>$url, "label"=>$r["trans_title"], "rank"=>$r["page_rank"], "title_page"=>$r["title_page"], "title"=>$des);
						   	    }
						        $this->call_command("DB_FREE",Array($result));
							}
						}
					}
//					print_r($list);
					$nfld = count($folders);
					$nlist= count($list);
					if($display_type==0){
						$out .= "	<cdata><![CDATA[<ul class='contenttable'>";
						/*
	                    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	                    - FOLDERS
	                    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	                    */
						if($show_folders==1){
							for($i=0; $i<$nfld; $i++){
								if($folders[$i]["title"]!=""){
									$title = $folders[$i]["title"];
								} else {
									$title = $folders[$i]["label"];
								}
								$out .= "		<li class='folder'><a href='".$folders[$i]["url"]."' title='$title'>".$folders[$i]["label"]."</a></li>";
								$counter++;
							}
						}
						/*
	                    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	                    - Pages display if more than one
	                    -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	                    */
						if($nlist!=1){
							for($i=0;$i<$nlist;$i++){
								if((trim($list[$i]["title"])!="") && ($list[$i]["title"]!=" ")){
									$title = $list[$i]["title"];
								} else {
									$title = $list[$i]["label"];
								}
								$title = str_replace(Array("\"","'","&quot;","&#39;","&amp;quot;","&amp;#39;"), Array("","","","","",""), $title);
								if($list[$i]["title_page"]==1 && $this->parent->real_script == $list[$i]["url"]){
//									$out .= "		<li class=\"selected\"><a href=\"".$this->parent->script."\" title=\"".$title."\">".$list[$i]["label"]."</a></li>";
									$out .= "		<li class=\"selected\">".$list[$i]["label"]."</li>";
								} else if($list[$i]["title_page"]==0 && $this->parent->real_script == $list[$i]["url"]){
//									$out .= "		<li class=\"selected\"><a href=\"".$list[$i]["url"]."\" title=\"".$title."\">".$list[$i]["label"]."</a></li>";
									$out .= "		<li class=\"selected\">".$list[$i]["label"]."</li>";
								} else if($list[$i]["title_page"]==1 && strpos($this->parent->real_script,"index.php")){
//									$out .= "		<li class=\"selected\"><a href=\"".$this->parent->script."\" title=\"".$title."\">".$list[$i]["label"]."</a></li>";
									$out .= "		<li class=\"selected\">".$list[$i]["label"]."</li>";
								} else {
									if($list[$i]["title_page"]==1){
										$out .= "		<li class=\"file\"><a href=\"".$this->parent->script."\" title=\"".$title."\">".$list[$i]["label"]."</a></li>";
									} else {
										$out .= "		<li class=\"file\"><a href=\"".$list[$i]["url"]."\" title=\"".$title."\">".$list[$i]["label"]."</a></li>";
									}
								}
								$counter++;
							}
						}
						if($show_folders==2){
							for($i=0; $i<$nfld; $i++){
								if($folders[$i]["title"]!=""){
									$title = $folders[$i]["title"];
								} else {
									$title = $folders[$i]["label"];
								}
								$out .= "		<li class='folder'><a href='".$folders[$i]["url"]."' title='$title'>".$folders[$i]["label"]."</a></li>";
								$counter++;
							}
						}
						$out .= "	</ul>]]></cdata>";
					} else {
						$out .= "	<form name='frm' method='post' ><label><![CDATA[$label]]></label>";
						$dt = "";
						if($display_type==2){
							$dt = "dropdownauto";
						}
						$out .= "		<display_format>$dt</display_format>";
						for($i=0; $i<$nfld; $i++){
							$out .= "	<menulink><url><![CDATA[".$folders[$i]["url"]."]]></url><label><![CDATA[".$folders[$i]["label"]."]]></label></menulink>";
							$counter++;
						}
						for($i=0;$i<$nlist;$i++){
							$out .= "	<menulink><url><![CDATA[".$list[$i]["url"]."]]></url><label><![CDATA[".$list[$i]["label"]."]]></label></menulink>";
							$counter++;
						}
						$out .= "		<input type='submit' value='Go'/>";
						$out .= "	</form>";
					}
				}
			}
			if($out !="" && $counter!=0){
				if($show_folders!=0){
					if($a2z==1){
						$out.="<a2z/>";
					}
				}

				if ($show_label==1 && $out!=""){
					if ($label!=""){
						$out .= "<label><![CDATA[$label]]></label>";
					} else {
						$out .= "<label><![CDATA[$menu_label]]></label>";
					}
				}
				$content = "<module name='".$this->module_name."' display='LIST'>$out</module>";
			} else {
				$content="";
			}
		} else {
			$content="";
		}
		return $content;
	}

	function module_jumpto($parameters){
		$url  = $this->check_parameters($parameters,"ctqlink");
		if ($url=="-1"){
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$this->parent->script));
		} else {
			$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$url));
		}
	}
}
?>
