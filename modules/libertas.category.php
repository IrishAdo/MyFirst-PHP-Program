<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.category_admin.php
* @date 12 Feb 2004
*/
/**
* This module is the presentation module for Categories it will allow the user to
* load Category LISTS and can be used by one or more modules.
*/

class category extends module{
	/**#@+
	*  Class Variables
    * @access private
    * @var string
	*/
	var $module_grouping			= "";
	var $module_name_label			= "Categorization Module (Presentation)";
	var $module_name				= "category";
	var $module_admin				= "0";
	var $module_command				= "CATEGORY_"; 		// all commands specifically for this module will start with this token
	var $module_label				= "";
	var $module_modify	 		    = '$Date: 2005/02/08 17:01:09 $';
	var $module_version 			= '$Revision: 1.23 $';
	var $module_creation 			= "26/02/2004";
    /**#@+
	* @access private
    * @var integer
	*/
	var $searched					= 0;
    /**#@+
	* @access private
    * @var Array
	*/
	var $loadedCatList              = Array();
	/**
	*  filter options
	*/
	var $display_options			= array();

	/**
	*  loaded category list
	*/
	var $clist 						= Array();
	var $clist_id					= 0;
	var $clist_locked				= 0;
	/**
	*  command function is the public interface for calling any of the private functions of this module
    *
    * @param $user_command String
    * @param $parameter_list array
    * @return mixed returns the data from the desired function
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
			if ($user_command==$this->module_command."LOAD"){
				return $this->category_load($parameter_list);
			}
			if ($user_command==$this->module_command."GET_OPTION_LIST"){
				return $this->return_option_categories($parameter_list["id"], $parameter_list["selected"]);
			}
			if ($user_command==$this->module_command."GET_CHILDREN"){
				return $this->return_child_categories($parameter_list);
			}
			if ($user_command==$this->module_command."GET_BREADCRUMBTRAILS"){
				return $this->get_bctrails($parameter_list);
			}
			if ($user_command == $this->module_command."LIST_LOAD"){
				return $this->category_list_load($parameter_list);
			}
		}
		return "";
	}
	/*
	*                                C A T E G O R Y   S E T U P   F U N C T I O N S
	*/

	/**
	* Initialise function
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
		* request the client identifier (this is the most used variable)
		*/
		$this->client_identifier	 	= $this->parent->client_identifier;
		return 1;
	}
	/**
	*                                C A T E G O R Y   F U N C T I O N S
	*/

	/**
    * this function will load a category list from the cache and create the file if it does not
	* exist.
    *
    * @param "category" integer the current category you are on set to -2 to load complet category structure
    * @param "identifier" integer the current category list
    * @param "recache" integer recache default = -1
    * @param "return_array" integer should this function return and Array or a string
    * @return mixed
	*/
	function category_load($parameters){
        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"".print_r($parameters, true).""));}
		$identifier = $this->check_parameters($parameters,"identifier",-1);
        $category = $this->check_parameters($parameters,"category",-2);
		$recache 	= $this->check_parameters($parameters,"recache",-1);
		$returnArray= $this->check_parameters($parameters,"return_array",-1);
		//$this->check_parameters($parameters,"recache",-1);
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];
        $load_cat = Array();
		$parent_cat = Array();
		$sql = "select cat_parent, count(*) as total from category 
			where cat_client = $this->client_identifier and cat_list_id = $identifier
			group by cat_parent";
		$result  = $this->call_command("DB_QUERY",Array($sql));
		while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$parent_cat[$r["cat_parent"]] = $r["total"];
		}
		$this->call_command("DB_FREE",Array($result));
        if($category==-2){
		    $file = $data_files."/category_".$this->client_identifier."_".$identifier.".xml";
            //print "<LI>$file</LI>";
            //print "<li>$file and $recache and  $returnArray</li>";
            if (file_exists($file) && $recache!=1 && $returnArray == -1){
  		        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"category loading complete list",__LINE__,"$file"));}
       		    return join("",file($file));
       		} else {
                if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"category not found",__LINE__,"Not Found"));}
       			if ($identifier!=-1){
                    if(count($this->loadedCatList)==0){
           				$sql			= "select * from category
           								where
           									cat_client	= $this->client_identifier and
           									cat_list_id = $identifier
           								order by cat_parent, cat_label, cat_identifier";
                        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
           				$result = $this->call_command("DB_QUERY",array($sql));
           				$pos = 0; // start with empty array
           				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
							$children = $this->check_parameters($parent_cat,$r["cat_identifier"],0);
           					$this->loadedCatList[$pos]= Array(
           						"cat_label"		=> $r["cat_label"],
           						"cat_parent"	=> $r["cat_parent"],
           						"cat_identifier"=> $r["cat_identifier"],
								"cat_children"	=> $children
           					);
							$pos++;
           				}
            			$this->call_command("DB_FREE",array($result));
            		}
                    if($returnArray==1){
        				return $this->loadedCatList;
        			} else {
                        $out = $this->return_categories($identifier);
                        $fp = fopen($file,"w");
        			    fwrite($fp, $out);
        			    fclose($fp);
        				$um = umask(0);
        				@chmod($file, LS__FILE_PERMISSION);
        				umask($um);
                        return $out;
                    }
        		}
            }
        } else {

            /**
            * load the parent category lists
            */
            $sql = "SELECT distinct cat_parent from category where cat_client= $this->client_identifier and cat_identifier = $category or cat_parent=$category";
            $result = $this->call_command("DB_QUERY",array($sql));
            if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
            //$load_cat = $identifier;
            $found = 0;
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$load_cat[count($load_cat)] = $r["cat_parent"];
                $found++;
			}
		    $this->call_command("DB_FREE",array($result));
            /**
            * if more than one category returned then you've got kids load both
            */
        }
		$out ="";
        for($i=0;$i<count($load_cat);$i++){
            if($load_cat[$i] != -1){
                $file = $data_files."/category_".$this->client_identifier."_".$identifier."_".$load_cat[$i].".xml";
                if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load category file",__LINE__,"$file [$recache,  $returnArray]"));}
    		    if (file_exists($file) && $recache!=1 && $returnArray == -1){
    			    $out .= join("",file($file));
        		} else {
                    if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"category not found",__LINE__,"Not Found"));}
        			if ($identifier!=-1){
                        if(count($this->loadedCatList)==0){
            				$sql			= "select * from category
            								where
            									cat_client	= $this->client_identifier and
            									cat_list_id = $identifier
            								order by cat_parent, cat_label, cat_identifier";
                            if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
            				$result = $this->call_command("DB_QUERY",array($sql));
            				$pos = 0; // start with empty array
            				while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
            					$this->loadedCatList[$pos]= Array(
            						"cat_label"		=> $r["cat_label"],
            						"cat_parent"	=> $r["cat_parent"],
            						"cat_identifier"=> $r["cat_identifier"]
            					);
            					$pos++;
            				}
            				$this->call_command("DB_FREE",array($result));
            			}
                        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"Return Array",__LINE__,"$returnArray"));}
                        if($returnArray==1){
        					return $this->loadedCatList;
        				} else {
                            $this->cache_categories($identifier,$identifier,$data_files."/category_".$this->client_identifier."_".$identifier);
        					$out = $this->return_categories($identifier);
                            $fp = fopen($data_files."/category_".$this->client_identifier."_".$identifier.".xml","w");
        					fwrite($fp, $out);
        					fclose($fp);
        					$um = umask(0);
        					@chmod($file, LS__FILE_PERMISSION);
        					umask($um);
                            $out .= join("",file($data_files."/category_".$this->client_identifier."_".$identifier."_".$identifier.".xml"));
        				}
        			}
                }
            }
		}
		return $out;
	}
	/**
	* Cache the categories structure one section at a time
	*
	* Uses the $this->loadedCatList category list and caches the tree structure into seperate files one for each branch
	*
	* @param $parent integer the parent identifier for the category list
	* @param $list integer the identifier of the category list
	* @param $file string the file name to save under
	* @param $crumb_path string the current category path
	* @return integer number of children in category
	*/
	function cache_categories($parent = -1, $list = -1, $file="", $crumb_path ="", $crumb =""){
        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"cache_categories",__LINE__,"[$parent], [$list], [$file], [$crumb_path], [$crumb]"));}
        $out	 = "<categorylist ";
		$out	.= "parent='$parent' ";
		$out	.= ">";
		$m = count($this->loadedCatList);
		$found =0;
		for($i=0;$i<$m;$i++){
			if ($this->loadedCatList[$i]["cat_identifier"] == $parent && $list != $parent){
				$crumb_path .= $this->make_uri($this->loadedCatList[$i]["cat_label"])."/";
				$crumb	.= "<crumb>";
				$crumb	.= "	<label><![CDATA[".str_replace(Array("'","&amp;amp;#39;","&amp;#39;","&quot;","&amp;#163;"), Array("[[pos]]","[[pos]]","[[amp]]#39;","[[quot]]","[[pound]]"), $this->loadedCatList[$i]["cat_label"])."]]></label>";
				$crumb	.= "	<path><![CDATA[".$crumb_path."index.php]]></path>\n";
				$crumb	.= "</crumb>";
			}
		}
		for($i=0;$i<$m;$i++){
			if ($this->loadedCatList[$i]["cat_parent"] == $parent){
				$found++;
				$out .= "<category ";
				$out .= "identifier='".$this->loadedCatList[$i]["cat_identifier"]."' ";
				$out .= "children='".$this->cache_categories($this->loadedCatList[$i]["cat_identifier"], $list, $file, $crumb_path, $crumb)."'";
				$out .= ">\n";
				$out .= "	<label><![CDATA[".str_replace(Array("'","&amp;amp;#39;","&amp;#39;","&quot;","&amp;#163;"), Array("[[pos]]","[[pos]]","[[amp]]#39;","[[quot]]","[[pound]]"), $this->loadedCatList[$i]["cat_label"])."]]></label>\n";
				$out .= "	<uri><![CDATA[".$crumb_path."/".$this->make_uri($this->loadedCatList[$i]["cat_label"])."/index.php]]></uri>\n";
				$out .= "</category>\n";
			}
		}
		$out	.= "<bread>";
		$out	.= $crumb;
		$out	.= "</bread>";
		$out	 .= "</categorylist>";
		if($found>0){
			$fp = fopen($file."_".$parent.".xml","w");
			fwrite($fp, $out);
			fclose($fp);
			$um = umask(0);
			@chmod($file, LS__FILE_PERMISSION);
			umask($um);
		}
		return $found;
	}
	/**
	* a function to return the complete category block <strong>warning</strong> this can be a large amount of data
	* this function recursivly looks through trying to build up a complete structure
	*
	* @param $id integer parent identifier to find
    * @return String either XML or empty
	*/
	function return_categories($id=-1, $crumb=""){
        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"return_categories",__LINE__,"[$id]"));}
        $out = "";
		$pos = count($this->loadedCatList);
		for($index = 0; $index<$pos; $index++){
			if ($this->loadedCatList[$index]["cat_identifier"] == $id){
				$list_name = $this->loadedCatList[$index]["cat_label"];
				$list_id   = $id;
			}
			if ($this->loadedCatList[$index]["cat_parent"] == $id){
                if ($crumb==""){
                    $crumb_path = $this->make_uri($this->loadedCatList[$index]["cat_label"]);
                } else {
                    $crumb_path = $crumb."/".$this->make_uri($this->loadedCatList[$index]["cat_label"]);
                }
				$out .= "<category parent='".$id."' identifier='".$this->loadedCatList[$index]["cat_identifier"]."'>\n<label><![CDATA[".
				str_replace(
					Array("'","&amp;amp;#39;","&amp;#39;","&quot;","&amp;#163;"),
					Array("[[pos]]","[[pos]]","[[amp]]#39;","[[quot]]","[[pound]]"),
					$this->loadedCatList[$index]["cat_label"]
				)."]]></label>\n<uri><![CDATA[".$crumb_path ."/index.php]]></uri>\n";
				$children = $this->return_categories($this->loadedCatList[$index]["cat_identifier"], $crumb_path);
				if ($children!=""){
					$out .= "<children>\n".$children."</children>\n";
				}
				$out .= "</category>\n";
			}
		}
		if (strlen($out)>0)
			return "<list rank='0' identifier='$list_id'><![CDATA[$list_name]]></list>".$out;
		else
			return "";
	}
	/**
	* a function to return the complete category structure as a option list <strong>warning</strong> this can be a large amount of data
	* this function recursivly looks through trying to build up a complete structure
	*
	* @param integer parent identifier to find
	* @param integer current selected category
	* @param String depth of structure non breaking space per level deep
    * @return String XML block of Option tags indented values as required
	*/
	function return_option_categories($id=-1, $selected = -1, $depth="", $start=0){
        if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"return_categories",__LINE__,"[$id, $selected, $depth]"));}
        $out = "";
		if ($selected==""){
			$selected=-1;
		}
		$pos = count($this->loadedCatList);
		if ($pos==0){
			$this->category_load(
				Array(
					"identifier"	=> $id,
					"return_array"  => 1
				)
			);	
			$pos = count($this->loadedCatList);
		}
		for($index = $start; $index<$pos; $index++){
			if ($this->loadedCatList[$index]["cat_parent"] == $id){
				$cid = $this->loadedCatList[$index]["cat_identifier"];
				$out .= "<option value='".$cid."'";
				if ($cid == $selected){
					$out .= " selected='true'";
				}
				$out .= "><![CDATA[$depth";
				if ($depth!=""){
					$out .= "-[[nbsp]]";
				}
				$out .= str_replace(
							Array("'","&amp;amp;#39;","&amp;#39;","&quot;","&amp;#163;"),
							Array("[[pos]]","[[pos]]","[[amp]]#39;","[[quot]]","[[pound]]"),
							$this->loadedCatList[$index]["cat_label"]
						)."]]></option>";
				if ($this->loadedCatList[$index]["cat_children"]*1 > 0){
					$out .= $this->return_option_categories($cid, $selected, $depth."[[nbsp]]");
				}
			}
		}
		return $out;
	}

	
	/**
	* get list of child categories
	*
	* will return the category supplied and any children in a comma sperated list
	*
	* @param integer parent identifier of children
    * @return String comma seperated list of category identifiers to check
	*/
	function return_child_categories($parameters){
		$rootNode 		= $this->check_parameters($parameters,"rootNode",-1);
		$info_category	= $this->check_parameters($parameters,"info_category",-1);
		$out 			= $rootNode;
		$pos 			= count($this->loadedCatList);
		if ($pos==0){
			$this->category_load(
				Array(
					"identifier"	=> $info_category,
					"return_array"  => 1
				)
			);	
			$pos = count($this->loadedCatList);
		}
		$list = Array();
		$list[0] = $rootNode;
		for($index = 0; $index<$pos; $index++){
			if (in_array($this->loadedCatList[$index]["cat_parent"],$list)){
				$list[count($list)] = $this->loadedCatList[$index]["cat_identifier"];
				$rootNode .= ", ".$this->loadedCatList[$index]["cat_identifier"];
			}
		}
		return $rootNode;
	}
	/**
    * get the bread crumb trail of this category
	*
	* use this function to produce a 
	* @param Array keys are ("object", "list", "category_list", "split_categories")
	* @return Array of paths (entries can belong to more than one category)
    */
	function get_bctrails($parameters){
		$bct = Array(); // bread crumb trails return array
		$object		= $this->check_parameters($parameters,"object");
		$list 		= $this->check_parameters($parameters,"list");
		$cat_list	= $this->check_parameters($parameters,"category_list");
		$split_categories	= $this->check_parameters($parameters,"split_categories");

		if ($this->clist_id == 0 || $this->clist_id != $cat_list){
			if ($this->clist_locked == 0){
				$this->clist_locked = 1;
				$this->clist = $this->category_list_load(
					Array(
						"identifier" => $cat_list,
						"returntype" => 0,
						"list" => $cat_list ,
						"rank" => 0
					)
				);
				$this->clist_id = $cat_list;
			}
		}
		if ($this->clist_id == $cat_list && $this->clist_locked == 0){
			$this->clist_locked = 1;
		}
		if ($this->clist_id == $cat_list && $this->clist_locked == 1){
			$sql = "select * from category_to_object 
					inner join information_entry on ie_client = cto_client and cto_object = ie_identifier and ie_list = $list
				where 
					cto_client=$this->client_identifier and cto_object = $object and cto_module='INFORMATIONADMIN_'";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$paths_to_retrieve = Array();
			$m=0;
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        		$paths_to_retrieve[count($paths_to_retrieve)] = $r["cto_clist"];
				$m++;
    	    }
	        $this->call_command("DB_FREE",Array($result));
			for($i=0; $i<$m ;$i++){
				$bct[$i]= $this->get_path($paths_to_retrieve[$i], $cat_list, $split_categories);
			}
			$this->clist_locked=0;
		}
		return $bct;
	}
	/**
    * extract the path (breadcrumb) for a specific item
	*
	* if the categories parent is equal to the category list identifier then return the label
	* if the categories parent is not equal to the category list identifier then call this function recursivly until it is
	*
	* @param Integer Parent identifier
	* @param Integer category list identifier
	* @param String split the path with this character
	* @return String the label of the categories in the path concationated together with the split categories string
    */
	function get_path($p, $list, $split_categories=""){
		if ($split_categories==""){
			$split_categories=chr(187);	
		}
		/*
			"cat_label"		
			"cat_parent"	
			"cat_identifier"
			"cat_list_id"	
		*/
		$max = count($this->clist);
		for ($iz=0; $iz<$max; $iz++){
			if ($this->clist[$iz]["cat_identifier"] == $p){
				if ($this->clist[$iz]["cat_parent"] == $list){
					return $this->clist[$iz]["cat_label"];
				} else {
					return $this->get_path($this->clist[$iz]["cat_parent"], $list, $split_categories) . $split_categories . $this->clist[$iz]["cat_label"];
				} 
			}
		}
	}
	/**
	* this function will load a category list from the cache and create the file if it does not
	* exist.
    *
	* @param identifier
	* @param returntype
	* @param list
	* @param rank
	* @param recache
	*/
	function category_list_load($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"category_list_load",__LINE__,"".print_r($parameters,true).""));}
		$identifier 	= $this->check_parameters($parameters,"identifier",-1);
		$returntype 	= $this->check_parameters($parameters,"returntype",-1);
		$list			= $this->check_parameters($parameters,"list", -1);
		$rank			= $this->check_parameters($parameters,"rank", 0);
		$recache 		= $this->check_parameters($parameters,"recache",-1);
		$data_files 	= $this->parent->site_directories["DATA_FILES_DIR"];
		$file 			= $data_files."/category_".$this->client_identifier."_".$identifier.".xml";
		$out 			= "";
		$this->loadedCatList= Array(); // reset array and load with they following details
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"category_list_load",__LINE__,"load $recache, $identifier, $returntype, $list"));}
		if ($returntype != -1){
			$parent_cat = Array();
			$sql = "select cat_parent, count(*) as total from category 
				where cat_client = $this->client_identifier and cat_list_id = $identifier
				group by cat_parent";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$parent_cat[$r["cat_parent"]] = $r["total"];
			}
			$this->call_command("DB_FREE",Array($result));
			if ($rank==0){
				$sql		= "select * from category
								where cat_client = $this->client_identifier and cat_list_id = $list
								order by cat_parent, cat_label, cat_identifier";
			} else {
				$sql		= "select category.*, category_ranking.cr_rank from category
									left outer join category_ranking on cr_cat = cat_identifier and cr_client = cat_client
								where cat_client= $this->client_identifier and cat_list_id = $list
								order by cat_parent, cr_rank, cat_label, cat_identifier";
			}
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
			$result = $this->call_command("DB_QUERY",array($sql));
			$pos = 0; // start with empty array
			while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
				$children = $this->check_parameters($parent_cat,$r["cat_identifier"],0);
				$this->loadedCatList[$pos]= Array(
					"cat_label"		=> $r["cat_label"],
					"cat_parent"	=> $r["cat_parent"],
					"cat_identifier"=> $r["cat_identifier"],
					"cat_list_id"	=> $r["cat_list_id"],
					"cat_children"	=> $children
				);
				$pos++;
			}
			$this->call_command("DB_FREE",array($result));
			return $this->loadedCatList;
		} else {
			if (file_exists($file) && $recache==-1){
				$out = join("",file($file));
			} else {
				if ($identifier!=-1){
					if ($rank==0){
						$sql		= "select * from category
										where cat_client= $this->client_identifier and cat_list_id = $identifier
										order by cat_parent, cat_label, cat_identifier";
					} else {
						$sql		= "select category.*, category_ranking.cr_rank from category
											left outer join category_ranking on cr_cat = cat_identifier and cr_client = cat_client
										where cat_client= $this->client_identifier and cat_list_id = $identifier
										order by cat_parent, cr_rank, cat_label, cat_identifier";
					}
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::SQL",__LINE__,"$sql"));}
					$result = $this->call_command("DB_QUERY",array($sql));
					$pos = 0; // start with empty array
					while ($r = $this->call_command("DB_FETCH_ARRAY",array($result))){
						$children = $this->check_parameters($parent_cat,$r["cat_identifier"],0);
						$this->loadedCatList[$pos]= Array(
							"cat_label"		=> $r["cat_label"],
							"cat_parent"	=> $r["cat_parent"],
							"cat_identifier"=> $r["cat_identifier"],
							"cat_url"		=> "",
							"cat_children"	=> $children
						);
						$pos++;
					}
					$this->call_command("DB_FREE",array($result));
					$out = $this->cache_categories($identifier,$identifier,$data_files."/category_".$this->client_identifier."_".$identifier);
//					$out = $this->return_categories($identifier);
					$fp = fopen($data_files."/category_".$this->client_identifier."_".$identifier.".xml","w");
					fwrite($fp, $out);
					fclose($fp);
					$um = umask(0);
					@chmod($file, LS__FILE_PERMISSION);
					umask($um);
                    $out = join("",file($file));
				}
			}
		}
		return $out;
	}
}
?>