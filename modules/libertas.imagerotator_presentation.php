<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.image_rotator.php
* @date 12 Feb 2004
*/
/**
* This module is the administration module for Categories it will allow the user to 
* generate Category LISTS which will contain lists of categories that could be used
* by one or more modules.
*/

class imagerotator_presentation extends module{
	/**
	*  Class Variables
	*/
	var $module_load_type			= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_grouping			= "";
	var $module_name_label			= "Image Rotator Manager Module (Presentation)";
	var $module_name				= "imagerotator_presentation";
	var $module_admin				= "0";
	var $module_command				= "IMAGEROTATOR_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "IMAGEROTATOR_"; 		// the web Container if the key for the menu_to_object infromation
	var $module_label				= "";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:11 $';
	var $module_version 			= '$Revision: 1.9 $';
	var $module_creation 			= "02/03/2004";
	var $searched					= 0;
	var $previewed					= Array();
	
	/**
	*  filter options
	*/
	var $display_options			= array();
	
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
			if  ($user_command==$this->module_command."DISPLAY"){
				return $this->module_display($parameter_list);
			}
			if  ($user_command==$this->module_command."DISPLAY_PREVIEW"){
				return $this->module_display_preview($parameter_list);
			}
			if ($user_command==$this->module_command."INHERIT"){
				$this->call_command("IMAGEROTATORADMIN_INHERIT",$parameter_list);
			}
		}
		return "";
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                                D I R E C T O R Y   S E T U P   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	/**
	* Initialise function
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
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		return 1;
	}
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	-                       D I R E C T O R Y   P R E S E N T A T I O N   F U N C T I O N S
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	
	/*
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	- fn :: module_display($parameters)
	-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	*/
	function module_display($parameters){
		$wo_owner_id= $this->check_parameters($parameters,"wo_owner_id",-1);
		$cmd = $this->check_parameters($parameters,"command");
		$pos = $this->check_parameters($parameters,"__layout_position");
		if (($pos==2 || $pos==3) && $cmd!=""){
			return "";
		}
		
		$image_list	= "";
		$out 		= "";
		$where="";
		if ($wo_owner_id !=-1){
			$where = " irl_identifier=$wo_owner_id and ";
		}
		$irl_number = -1;
		$sql 		= "select * from imagerotate_list
							left outer join menu_to_object on 
								(mto_client = irl_client and mto_object=irl_identifier and mto_module='$this->webContainer' and 
									(mto_menu=".$parameters["current_menu_location"]." or 
										(mto_menu is null and irl_all_locations=1)
									) and mto_publish=1
								) 
					   where 
					   $where 
			   			(mto_menu=".$parameters["current_menu_location"]." or (mto_menu is null and irl_all_locations=1)) and 
					   irl_client= $this->client_identifier";
//		print $sql;
		$result  = $this->call_command("DB_QUERY",Array($sql));
		$max = count($this->previewed);
		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$ok = 1;
			$irl_identifier				= $r["irl_identifier"];
			for ($index=0;$index<$max;$index++){
				if ($this->previewed[$index] == $irl_identifier){
					$ok = 0;
				}
			}
			if ($ok==1){
	//	     	print " Label :: 			".$r["irl_label"];
	//       	print " Identifier :: 		".$r["irl_identifier"];
	//        	print " Number to show ::	".$r["irl_number"];
				$irl_number					= $r["irl_number"];
				$irl_type					= $r["irl_type"];
				$irl_direction				= $r["irl_direction"];
				$irl_width					= $this->check_parameters($r,"irl_width",0);
				$irl_height					= $this->check_parameters($r,"irl_height",0);
				$irl_vspace					= $this->check_parameters($r,"irl_vspace",0);
				$irl_hspace					= $this->check_parameters($r,"irl_hspace",0);
				$irl_align					= $this->check_parameters($r,"irl_align",0);
				if ($irl_align==0){
					$irl_align = "alignleft";
				} else if($irl_align==1){
					$irl_align = "aligncenter";
				} else {
					$irl_align = "alignright";
				}
				$sql = "select * from imagerotate_filelist
							inner join file_info on (file_identifier = irlf_file  and irlf_client = file_client)
						 where irlf_list = $irl_identifier and irlf_client = $this->client_identifier 
						 order by irlf_rank";
				$fileresult = $this->call_command("DB_QUERY",Array($sql));
				$numfiles	= $this->call_command("DB_NUM_ROWS",Array($fileresult));
				$c=0;
				$image_list = Array();
	   	        while ($filerecord = $this->call_command("DB_FETCH_ARRAY",Array($fileresult))){
					$dir_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($filerecord["file_directory"]));
					$destination_filename = $dir_path.$filerecord["file_md5_tag"].$this->file_extension($filerecord["file_name"]);
	           		$image_list[$c]=Array("<object type='img' width='".$filerecord["file_width"]."' height='".$filerecord["file_height"]."'>
												<alt><![CDATA[".$filerecord["file_label"]."]]></alt>
												<src><![CDATA[".$destination_filename."]]></src>
											</object>",0);
					$c++;
					// (".$filerecord["file_size"].")
	   	        }
				$this->call_command("DB_FREE",Array($fileresult));
				$images = "";
				if ($irl_type==0){
					$max = ($numfiles>$irl_number)?$irl_number:$numfiles;
					for($i = 0; $i<$max ; $i ++){
						$found=0;
						while ($found==0){
							$index = rand(0,$numfiles-1);
							if ($image_list[$index][1]==0){
								$found=1;
								$images .= $image_list[$index][0];
								$image_list[$index][1]=1;
							}
						}
					}
				} else if ($irl_type==1){
					$listOfImages = $this->check_parameters($_SESSION,"IR_".$irl_identifier,Array());
					if (count($listOfImages)==0){
						$max = ($numfiles>$irl_number) ? $irl_number : $numfiles ;
						for($i = 0; $i<$max ; $i ++){
							$found=0;
							while ($found==0){
								$index = rand(0,$numfiles-1);
								if ($image_list[$index][1]==0){
									$found=1;
									$images .= $image_list[$index][0];
									$image_list[$index][1]=1;
									$listOfImages[$i] = $index;
								}
							}
						}
						$_SESSION["IR_".$irl_identifier] = $listOfImages;
					} else {
						$max = count($listOfImages);
						for($i = 0; $i<$max ; $i ++){
							$images .= $image_list[$listOfImages[$i]][0];
						}
					}
				} else {
					$index = $this->check_parameters($_SESSION,"IRI_".$irl_identifier,0);
					$max = ($numfiles>$irl_number) ? $irl_number : $numfiles;
					if ($this->check_parameters($_SESSION,"IRI_".$irl_identifier,0)>=$numfiles){
						$_SESSION["IRI_".$irl_identifier] = 0;
					} else {
						$_SESSION["IRI_".$irl_identifier] = $index + 1;
					}
					if ($index >= $numfiles){
						$index=0;
						$_SESSION["IRI_".$irl_identifier] = 0;
					}
					for($i = 0; $i<$max ; $i ++){
						$images .= $image_list[$index][0];
						$index++;
						if ($index >= $numfiles){
							$index=0;
						}
					}
				}
				$dir = ($irl_direction==1)?"vertical":"horizontal";
				$out .= "<module name=\"".$this->module_name."\" display=\"IMAGELIST\">";
				$out .= "	<type identifier=\"$irl_identifier\" direction=\"$dir\" resize_width=\"$irl_width\" resize_height=\"$irl_height\" vspace=\"$irl_vspace\" hspace=\"$irl_hspace\" align='$irl_align'/>";
				$out .= "	$images";
				$out .= "</module>";
			}
        }
        $this->call_command("DB_FREE",Array($result));
		return $out;
	}
	function module_display_preview($parameters){
		$debug 		= $this->debugit(false,$parameters);
		$wo_owner_id= $this->check_parameters($parameters,"wo_owner_id",-1);
		if($debug){
			foreach ($parameters as $key => $value) { 
			   echo "$key : $value<br>\n"; 
			} 
		}
		$irl_number							= $this->check_parameters($parameters,"irl_number");
		$irl_type							= $this->check_parameters($parameters,"irl_type");
		$irl_direction						= $this->check_parameters($parameters,"irl_direction");
		$irl_identifier						= $this->check_parameters($parameters,"unset_identifier");
		$irl_width							= $this->check_parameters($parameters,"irl_width",0);
		$irl_height							= $this->check_parameters($parameters,"irl_height",0);
		$irl_vspace							= $this->check_parameters($parameters,"irl_vspace",0);
		$irl_hspace							= $this->check_parameters($parameters,"irl_hspace",0);
		$irl_align							= $this->check_parameters($parameters,"irl_align",0);
		if ($irl_align==0){
			$irl_align = "alignleft";
		} else if($irl_align==1){
			$irl_align = "aligncenter";
		} else {
			$irl_align = "alignright";
		}
		$file_associations					= $this->check_parameters($parameters,"file_associations",0);
		$irl_all_locations					= $this->check_parameters($parameters,"irl_all_locations",0);

		$totalnumberofchecks_irl_containers	= $this->check_parameters($parameters,"totalnumberofchecks_irl_containers",0);
		$irl_container_list = Array();
		for ($index=0; $index<$totalnumberofchecks_irl_containers;$index++){
			$list = $this->check_parameters($parameters,"irl_containers_$index",Array());
			for ($i=0; $i<count($list);$i++){
				$irl_container_list[count($irl_container_list)] = $list[$i];
			}
		}
		$image_list	= "";
		$out 		= "";
		$where="";
		$sql = "select * from file_info 
					 where file_identifier in ($file_associations -1 ) and file_client = $this->client_identifier 
				 ";
		if ($debug) print "<p><strong>:: ".__FILE__." @ ".__LINE__." ::</strong><br/>$sql</p>\n";
			$fileresult = $this->call_command("DB_QUERY",Array($sql));
			$numfiles	= $this->call_command("DB_NUM_ROWS",Array($fileresult));
			$c=0;
			$image_list = Array();
   	        while ($filerecord = $this->call_command("DB_FETCH_ARRAY",Array($fileresult))){
				$dir_path = $this->call_command("LAYOUT_GET_DIRECTORY_PATH",Array($filerecord["file_directory"]));
				$destination_filename = $dir_path.$filerecord["file_md5_tag"].$this->file_extension($filerecord["file_name"]);
           		$image_list[$c]=Array("<object type='img' width='".$filerecord["file_width"]."' height='".$filerecord["file_height"]."'>
											<alt><![CDATA[".$filerecord["file_label"]." (".$filerecord["file_size"].")]]></alt>
											<src><![CDATA[".$destination_filename."]]></src>
										</object>",0);
				$c++;
   	        }
			$this->call_command("DB_FREE",Array($fileresult));
			$images = "";
			if ($irl_type==0){
				$max = ($numfiles>$irl_number)?$irl_number:$numfiles;
				for($i = 0; $i<$max ; $i ++){
					$found=0;
					while ($found==0){
						$index = rand(0,$numfiles-1);
						if ($image_list[$index][1]==0){
							$found=1;
							$images .= $image_list[$index][0];
							$image_list[$index][1]=1;
						}
					}
				}
			} else if ($irl_type==1){
				$listOfImages = $this->check_parameters($_SESSION,"IR_".$irl_identifier,Array());
				if (count($listOfImages)==0){
					$max = ($numfiles>$irl_number) ? $irl_number : $numfiles ;
					for($i = 0; $i<$max ; $i ++){
						$found=0;
						while ($found==0){
							$index = rand(0,$numfiles-1);
							if ($image_list[$index][1]==0){
								$found=1;
								$images .= $image_list[$index][0];
								$image_list[$index][1]=1;
								$listOfImages[$i] = $index;
							}
						}
					}
					$_SESSION["IR_".$irl_identifier] = $listOfImages;
				} else {
					$max = count($listOfImages);
					for($i = 0; $i<$max ; $i ++){
						$images .= $image_list[$listOfImages[$i]][0];
					}
				}
			} else {
				$index = $this->check_parameters($_SESSION,"IRI_".$irl_identifier,0);
				$max = ($numfiles>$irl_number) ? $irl_number : $numfiles;
				if ($this->check_parameters($_SESSION,"IRI_".$irl_identifier,0)>=$numfiles){
					$_SESSION["IRI_".$irl_identifier] = 0;
				} else {
					$_SESSION["IRI_".$irl_identifier] = $index + 1;
				}
				if ($index >= $numfiles){
					$index=0;
					$_SESSION["IRI_".$irl_identifier] = 0;
				}
				for($i = 0; $i<$max ; $i ++){
					$images .= $image_list[$index][0];
					$index++;
					if ($index >= $numfiles){
						$index=0;
					}
				}
			}
			if ($irl_all_locations==1){
				$menu = "menu_url ='index.php'";
			} else {
				if (is_array($irl_all_locations)){
					$menu_id = $irl_all_locations[0];
				} else {
					$menu_id = $irl_all_locations;
				}
				$menu = "menu_identifier ='$menu_id'";
			}
			$sql = "Select 
					web_containers.*, 
					menu_data.menu_url 
				from web_containers 
					inner join web_layouts on wc_layout_identifier = wol_identifier and wc_client = wol_client
					left outer join web_layout_to_menu on (wol_identifier = wor_layout) and (wor_client = wol_client)
					left outer join menu_data on (menu_identifier = wor_menu) and (wor_client = menu_client)
				where 
					wc_identifier in (". join(",", $irl_container_list) .") and 
					wc_client=5 and 
					($menu or (wol_all_locations =1 and menu_url is null))
				order by 
					wol_all_locations asc, 
					wc_layout_identifier";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			if ($debug) print "<p><strong>:: \n".__FILE__." @ ".__LINE__." ::</strong><br/>\n$sql</p>\n";
			$c=0;
		    while(($c==0) && ($r = $this->call_command("DB_FETCH_ARRAY",Array($result)))){
		    	$c++;
				$cid		= $r["wc_identifier"];
				$pos		= $r["wc_position"];
				$wlt		= $r["wc_layout_type"];
				$cols		= $r["wc_layout_columns"];
				$width		= $r["wc_width"];
				$rank 		= $r["wc_rank"];
		    }
		    $this->call_command("DB_FREE",Array($result));
			$dir = ($irl_direction==1)?"vertical":"horizontal";
//			$out .= '<container i="1" identifier="'.$cid.'" rank="'.$rank.'" pos="'.$pos.'" layouttype="'.$wlt.'" columns="'.$cols.'" width="'.$width.'">'."\n";
//			$out .= '<webobject i="preview" identifier="-1" display_label="0" container="'.$cid.'" pos="1" rank="1" type="1">'."\n";
//			$out .= '<property id="-1"><option><name><![CDATA[text-align]]></name><value><![CDATA[left]]></value>'."\n";
//			$out .= '</option>'."\n";
//			$out .= '</property>'."\n";

			$this->previewed[count($this->previewed)] = $irl_identifier;
			$max = count($this->previewed);
			$out .= "<module name=\"".$this->module_name."\" display=\"IMAGELIST\">"."\n";
			$out .= "	<type preview=\"1\" identifier=\"$irl_identifier\" direction=\"$dir\" resize_width=\"$irl_width\" resize_height=\"$irl_height\" vspace=\"$irl_vspace\" hspace=\"$irl_hspace\" align='$irl_align'/>"."\n";
			$out .= "	$images"."\n";
			$out .= "</module>"."\n";
//			$out .= '</webobject>'."\n";
//			$out .= '</container>'."\n";
//			print str_replace(Array("<",">"), Array("_","_"),$out);
		return $out;
	}
}
?>
