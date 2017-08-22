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
require_once ("libertas.information_presentation.php");
class events extends information_presentation{
	/**#@+
	* Class Variables
    * @var string
	*/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_name				= "events";
	var $module_presentation_name	= "events";
	var $module_name_label			= "Event Management Module (Presentation)";
	var $module_admin				= "0";
	var $module_channels			= "";
	var $module_modify	 			= '$Date: 2005/02/08 17:01:10 $';
	var $module_version 			= '$Revision: 1.13 $';
	var $module_command				= "EVENT_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "EVENT_";
	var $webAdmin					= "EVENTADMIN_";
	var $module_label				= "MANAGEMENT_EVENT";
	var $shop_type					= "EVENT";
	/*************************************************************************************************************************
    * extra commands defined by
    *************************************************************************************************************************/
	var $extra_commands = Array(
		Array("EVENT_SHOW_CALENDAR", "show_calendar")
	);
	/*************************************************************************************************************************
	* @function Initialise function
	*
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
		/**
		*request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		parent::initialise();
		return 1;
	}
	
	/*************************************************************************************************************************
    * show_calendar
    *************************************************************************************************************************/
	function show_calendar($parameters){
//	print_r($parameters);
		$today	= split("-",$this->libertasGetDate("Y-m-d"));
		$list	= $this->check_parameters($parameters,"wo_owner_id",$this->check_parameters($parameters,"identifier",-1));
		$y		= $this->check_parameters($parameters,"year", $this->check_parameters($parameters,"y", date("Y")));
		$m		= $this->check_parameters($parameters,"month", $this->check_parameters($parameters,"m", date("m")));
		$d		= $this->check_parameters($parameters,"d", -1);
		$ym		= $this->check_parameters($parameters,"ym", "");
		if($ym!=""){
			$l = split("::",$ym);
			$y = $l[0];
			$m = $l[1];
		}
		$params = split("::",date("l::t::D",  mktime (0,0,0,$m,1,$y)));
		$maxendyear = "";
		$maxendmonth = "";
		$minstartyear = "";
		$minstartmonth = "";
		$sql = "
		select ie_parent,md_identifier, year(md_date_remove) as yr, month(md_date_remove) as mth, max(year(md_date_remove)) as maxendyear, max(month(md_date_remove)) as maxendmonth, min(year(md_date_available)) as minstartyear, min(month(md_date_available)) as minstartmonth, menu_url, wo_show_label , wo_label  from information_entry 
			inner join metadata_details on md_link_id = ie_identifier and md_module='EVENT_' and md_client= ie_client 
			inner join information_list on info_identifier = ie_list and ie_client = info_client
			inner join menu_data on info_menu_location = menu_identifier and menu_client = info_client
			inner join web_objects on wo_owner_id = $list and wo_client=ie_client and wo_owner_module='EVENT_SHOW_CALENDAR'
		where ie_published=1 and ie_status = 1 and ie_client=$this->client_identifier and ie_list = $list
			group by md_date_remove 
			order by ie_uri ";
			//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::show_calendar",__LINE__,"<li>$sql</li>"));}
		$script="";
		$date_list =Array();
		
		
		$result	= $this->call_command("DB_QUERY",Array($sql));
		$ok		= 0;
		$mths = Array("Janurary", "Feburary", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
		$count_event = 0;
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$script= $r["menu_url"];
			$label = $r["wo_label"];
			$show_label = $r["wo_show_label"];
			
			$listOfEntries[count($listOfEntries)]= Array(
				"parent"	=> $r["ie_parent"],
				"md_id"		=> $r["md_identifier"]
			);
			
			if (empty($date_list[$r["yr"]])){
        		$date_list[$r["yr"]] = Array(
					1	=>0,
					2	=>0,
					3	=>0,
					4	=>0,
					5	=>0,
					6	=>0,
					7	=>0,
					8	=>0,
					9	=>0,
					10	=>0,
					11	=>0,
					12	=>0
				);
			}
			
			
			if($maxendyear == '' || $maxendyear < $r["maxendyear"])
				$maxendyear = $r["maxendyear"];
			if($maxendmonth == '' || $maxendmonth < $r["maxendmonth"])
				$maxendmonth = $r["maxendmonth"];
			if($minstartyear == '' || $minstartyear > $r["minstartyear"])
				$minstartyear = $r["minstartyear"];
			if($minstartmonth == '' || $minstartmonth > $r["minstartmonth"])
				$minstartmonth = $r["minstartmonth"];
			$date_list[$r["styr"]][$r["stmth"]] = 1;
			if($y == $r["yr"] && $m == $r["mth"]){
				//$ok = 1;
				
			}
        }
		//print_r($listOfEntries);echo '<br>';
        $this->call_command("DB_FREE",Array($result));
		
		/* Starts Added By Muhammad Imran */
		////categories
		$sql = "select * from category_to_object 
						inner join category on cat_identifier = cto_clist and cat_client=cto_client
					where cto_client = $this->client_identifier";
//				print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$result  = $this->parent->db_pointer->database_query($sql);
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$cat[$r["cat_identifier"]] = Array("cat_label"=>$r["cat_label"],"cat_parent"=>$r["cat_parent"],"cat_list"=>$r["cat_list_id"],"path"=>"");
			}
			
			
			$cat_path = "";
			$ctr_var = 0;
			foreach($cat as $key => $catEntry){
				if($cat[$key]["path"]==""){
					$path = $this->get_path($cat[$key], $catlist);
					$cat[$key]["path"] = $path;
				} else {
					$path = $cat[$key]["path"];
				}
				$cat_path .= "<cat_path id='$key'><![CDATA[".dirname($this->parent->script)."/$path]]></cat_path>";
				
				$cat[$key]["category_identifier"] = $key;
//				echo $key.'<br>';
				
//				$cat_path_arr[$ctr_var]['cat_name'] = dirname($this->parent->script)."/$path";
//				$cat_path_arr[$ctr_var]['cat_identifier'] = $cat_parent;
				$ctr_var++;
			}
//			print_r($cat);
//				echo $cat_path;
				
		/////categories

		$m_list_entries = count($listOfEntries);
//print_r($listOfEntries['parent']);
//echo '<br>';
		$lang="en";
		$data_files = $this->parent->site_directories["DATA_FILES_DIR"];

		$mths_abr = Array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
		$mths_cspell = str_replace("Janurary","January",$mths);
		$mths_cspell = str_replace("Feburary","February",$mths_cspell);
		/* Ends Added By Muhammad Imran */
		
		//print_r($date_end_days_list);
		$start_year	=0;
		$start_month=0;
		$end_year	=0;
		$end_month	=1;
		foreach($date_list as $cyear =>$cmonths){
			
			if($start_year==0){
				$start_year = $cyear;
			}
			$end_year = $cyear;
			$end_month=0;
			foreach($cmonths as $cur_month => $val){
				if($start_year==$cyear){
					if (($start_month==0) && ($val!=0)){
						$start_month=$cur_month;
					}
				}
				if ($val!=0){
					$end_month=$cur_month;
				}
			}
		//print "[$start_year, $start_month, $end_year, $end_month]";
		}
		
		$days = Array();
		$m1 = '';
		if($m <10)
			$m1 = substr($m,1,2);
		else
			$m1= $m;
		$m2 = $m1;		
		if($ok == 1){
			$sql ="select md_date_remove as dy, md_date_remove from information_entry 
					inner join metadata_details on md_link_id = ie_identifier and md_module='EVENT_' and md_client= ie_client 
					left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list 
				where year(md_date_remove) = '$y' and month(md_date_remove) = '$m' and ie_published=1 and ie_status = 1 and ie_client=$this->client_identifier and ie_list = $list
				order by ie_uri ";
			//print $sql;
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::show_calendar",__LINE__,"<li>$sql</li>"));}
			$result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
            	$days[date("d",strtotime($r["dy"]))] = 1;
            }
			
            $this->call_command("DB_FREE",Array($result));
		}
		
		$daysOfTheWeek = Array("Sun","Mon","Tue","Wed","Thu","Fri","Sat");
		for($i=0;$i<7;$i++){
			if($params[2]==$daysOfTheWeek[$i]){
				$start = $i;
			}
		}
		$out = "<table summary='calendar of events one month visual' class='calendar'>
		<tr><td colspan='7' class='centeralign'>";
		if (($minstartyear==$y && $m > $minstartmonth) || ($y>$minstartyear)){
			$out.="<a title='Previous month' href='[[script]]?ym=".date("Y::m", mktime (0,0,0,$m-1,1,$y))."'>[[leftarrow]][[leftarrow]]</a>";
		} else {
			$out.="[[leftarrow]][[leftarrow]]";
		}

		$out.=" [[nbsp]] ".$mths[$m-1]." $y [[nbsp]] ";
		if (($maxendyear==$y && $m < $maxendmonth) || ($y<$maxendyear)){
			$out.="<a title='Next month' href='[[script]]?ym=".date("Y::m", mktime (0,0,0,$m+1,1,$y))."'>[[rightarrow]][[rightarrow]]</a>";
		} else {
			$out.="[[rightarrow]][[rightarrow]]";
		}
		
		$out.="</td></tr>
		<tr>";
		for($i=0;$i<7;$i++){
			$out .= "<th scope='col'>".substr($daysOfTheWeek[$i],0,1)."</th>";
		}
		$out .="</tr><tr>";
		$index=1;
		$pmd = date("t", mktime (0,0,0,$m-1,1,$y));
		
		$day_link = '';
		$month = '';	
		$sql = '';
		for($i=0;$i<7;$i++){
			if($i<$start){
				if($m1 == 1){
					$month =12;
					$year = $y-1;
				}else{
					$month = $m1-1;
					$year = $y;
				}
				$date = $year."-".$month."-".(($pmd-($start-1))+$i);
				$sql ="select year(md_date_remove) as yr, month(md_date_remove) as mth, md_date_remove from information_entry 
					inner join metadata_details on md_link_id = ie_identifier and md_module='EVENT_' and md_client= ie_client 
					left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list 
				where ('$date' <=md_date_remove && '$date' >= md_date_available) and ie_published=1 and ie_status = 1 and ie_client=$this->client_identifier and ie_list = $list
				order by ie_uri ";
				
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::show_calendar",__LINE__,"<li>$sql</li>"));}
				$result  = $this->call_command("DB_QUERY",Array($sql));
           		$db_num_rows  = $this->call_command("DB_NUM_ROWS",array($result));
           		if($db_num_rows >= 1){
					$r = $this->call_command("DB_FETCH_ARRAY",Array($result));
					list($e_year, $e_month, $e_day) = explode('-', $r["md_date_remove"]); 
					list($e_day,$e_time) = explode(' ', $e_day);  


				/**** Starts Loop to add file paths for event calendars Next Month offdate event( Added By Muhammad Imran )****/
				for($p_counter=0; $p_counter<$m_list_entries; $p_counter++){
					$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$listOfEntries[$p_counter]["parent"].".xml";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
					if (file_exists($fname)){
//						$out .= join("", file($fname));
						if (!$myxml=simplexml_load_file($fname)){
							echo 'Error reading the XML file';
							}
		//					print_r($myxml);die;
							foreach($myxml as $event_link){
								//echo '<br />FLE: ' . $event_link->value . '<br />';	
		//						echo $file_uri = $event_link->value;die;
								$file_uri = $event_link->value;
		//						echo $file_cat = $event_link->choosencategory;
		//						echo '<br>';
								if ($file_uri != "")
									$file_uri_var = $file_uri;
		//							$file_path = str_replace("index.php","",$script).$file_uri;
							}
							
							$category_identifier = "";
							$xml_file = file_get_contents($fname);
							// load as file
							$sitemap = new SimpleXMLElement($xml_file);
							foreach($sitemap as $values_var) {
							//    echo "Number: {$url['identifier']}: {$url->loc} - {$url->lastmod} - {$url->changefreq} - {$url->priority}\r\n";
								$category_identifier = "{$values_var['identifier']}";
							} 
							
							$category_path = "";
							foreach($cat as $key_cat => $catValue){
								if ($cat[$key_cat]["category_identifier"] == $category_identifier){
//									echo $cat[$key_cat]["category_identifier"].'<br>';
									$category_path = $cat[$key_cat]["path"];
								}
							}
							//$category_identifier;

							////$str_date = strtotime($this->libertasGetDate("d F Y",strtotime($r["yr"]."-".$r["mth"]."-".$index - $params[1])));
							$str_date = strtotime($this->libertasGetDate("d F Y",strtotime($date)));

//					$out .= "<td style='color:#999999'><a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".(($pmd-($start-1))+$i)))." 'href='$file_path?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".(($pmd-($start-1))+$i)."</a></td>";
							
							//echo $str_date.'<br>';die;

							/**Get Start Date**/
							$file_contents = join("", file($fname));

							/* Get URI*/
							$pos_uri = strpos($file_contents, "<field name='uri' link='no' visible='no'><value>");
							$str_pos_uri = substr($file_contents,$pos_uri);
//							$pos_uri2 = strpos($str_pos_uri, "</value>");
							$end_value = strpos($str_pos_uri, "</value></field>");
							$file_uri_var = substr($str_pos_uri,$str_pos_uri+57,$end_value-57-3);
							/* Get URI*/


							$pos = strpos($file_contents, "<field type='datetime' name='ie_odate1' visible='yes'>");
							if ($pos == "")
								$pos = strpos($file_contents, "<field type='date' name='ie_odateonly1' visible='yes'>");

							$str_field = substr($file_contents,$pos);
							$pos = strpos($str_field, ",");
							$date_start = substr($str_field,$pos+2,11);
							$date_start = str_replace($mths_abr,$mths_cspell,$date_start);
							$date_start = strtotime($date_start);
							
							/**Get End Date**/
							$pos2 = strpos($str_field, "<field type='datetime' name='ie_odate2' visible='yes'>");
							if ($pos2 == "")
								$pos2 = strpos($str_field, "<field type='date' name='ie_odateonly2' visible='yes'>");

							$str_field2 = substr($str_field,$pos2);
							$pos2 = strpos($str_field2, ",");
							$date_end = substr($str_field2,$pos+2,11);
							//$month2 = monToMonth(substr($date_end,3,3));
							$date_end = str_replace($mths_abr,$mths_cspell,$date_end);
							$date_end = strtotime($date_end);

/*echo 'sta:'.$date_start.'<br>';
echo 'str:'.$str_date.'<br>';
echo 'end'.$date_end.'<br><br>';
*/
							if ($date_start <= $str_date && $date_end >= $str_date){
//								echo $listOfEntries[$p_counter]["parent"];
							$file_path = dirname($this->parent->script).'/'.$category_path.'/'.$file_uri_var;

//									$out .= "<a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".$index))."'href='$file_path?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".$index."</a>";

					$out .= "<td style='color:#999999'><a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".(($pmd-($start-1))+$i)))." 'href='$file_path?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".(($pmd-($start-1))+$i)."</a></td>";
									
								}
								$str_date = "";
					}
				}
				/**** Ends Loop to add file paths for event calendars Next Month offdate event( Added By Muhammad Imran )****/


				//while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				//	$out .= "<td style='color:#999999'><a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".(($pmd-($start-1))+$i)))." 'href='$script?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".(($pmd-($start-1))+$i)."</a></td>";
					//}
				}else
					$out .= "<td style='color:#999999'>".(($pmd-($start-1))+$i)."</td>";
				
				
			}else{
				
				$month = $m1;
				$year = $y;
				
				$date = $year."-".$month."-".$index;
				$sql ="select year(md_date_remove) as yr, month(md_date_remove) as mth, md_date_remove from information_entry 
					inner join metadata_details on md_link_id = ie_identifier and md_module='EVENT_' and md_client= ie_client 
					left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list 
				where ('$date' <=md_date_remove && '$date' >= md_date_available) and ie_published=1 and ie_status = 1 and ie_client=$this->client_identifier and ie_list = $list
				order by ie_uri ";
				
				if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::show_calendar",__LINE__,"<li>$sql</li>"));}
				$result  = $this->call_command("DB_QUERY",Array($sql));
				$db_num_rows  = $this->call_command("DB_NUM_ROWS",array($result));
           		if($db_num_rows >= 1){
					$r = $this->call_command("DB_FETCH_ARRAY",Array($result));
					list($e_year, $e_month, $e_day) = explode('-', $r["md_date_remove"]);  
					list($e_day,$e_time) = explode(' ', $e_day); 

				/**** Starts Loop to add file paths for event calendars Previous Month offdate event( Added By Muhammad Imran )****/
				for($p_counter=0; $p_counter<$m_list_entries; $p_counter++){
					$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$listOfEntries[$p_counter]["parent"].".xml";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
					if (file_exists($fname)){
//						$out .= join("", file($fname));
						if (!$myxml=simplexml_load_file($fname)){
							echo 'Error reading the XML file';
							}
		//					print_r($myxml);die;
							foreach($myxml as $event_link){
								//echo '<br />FLE: ' . $event_link->value . '<br />';	
		//						echo $file_uri = $event_link->value;die;
								$file_uri = $event_link->value;
		//						echo $file_cat = $event_link->choosencategory;
		//						echo '<br>';
								if ($file_uri != "")
									$file_uri_var = $file_uri;
		//							$file_path = str_replace("index.php","",$script).$file_uri;
							}
							
							$category_identifier = "";
							$xml_file = file_get_contents($fname);
							// load as file
							$sitemap = new SimpleXMLElement($xml_file);
							foreach($sitemap as $values_var) {
							//    echo "Number: {$url['identifier']}: {$url->loc} - {$url->lastmod} - {$url->changefreq} - {$url->priority}\r\n";
								$category_identifier = "{$values_var['identifier']}";
							} 
							
							$category_path = "";
							foreach($cat as $key_cat => $catValue){
								if ($cat[$key_cat]["category_identifier"] == $category_identifier){
//									echo $cat[$key_cat]["category_identifier"].'<br>';
									$category_path = $cat[$key_cat]["path"];
								}
							}
							//$category_identifier;

							////$str_date = $this->libertasGetDate("d F Y",strtotime($r["yr"]."-".$r["mth"]."-".$index - $params[1]));
							$str_date = strtotime($this->libertasGetDate("d F Y",strtotime($date)));
							

							//echo $str_date.'<br>';die;
							/**Get Start Date**/
							$file_contents = join("", file($fname));

							/* Get URI*/
							$pos_uri = strpos($file_contents, "<field name='uri' link='no' visible='no'><value>");
							$str_pos_uri = substr($file_contents,$pos_uri);
//							$pos_uri2 = strpos($str_pos_uri, "</value>");
							$end_value = strpos($str_pos_uri, "</value></field>");
							$file_uri_var = substr($str_pos_uri,$str_pos_uri+57,$end_value-57-3);
							/* Get URI*/


							$pos = strpos($file_contents, "<field type='datetime' name='ie_odate1' visible='yes'>");
							if ($pos == "")
								$pos = strpos($file_contents, "<field type='date' name='ie_odateonly1' visible='yes'>");

							$str_field = substr($file_contents,$pos);
							$pos = strpos($str_field, ",");
							$date_start = substr($str_field,$pos+2,11);
							$date_start = str_replace($mths_abr,$mths_cspell,$date_start);
							$date_start = strtotime($date_start);
							
							/**Get End Date**/
							$pos2 = strpos($str_field, "<field type='datetime' name='ie_odate2' visible='yes'>");
							if ($pos2 == "")
								$pos2 = strpos($str_field, "<field type='date' name='ie_odateonly2' visible='yes'>");

							$str_field2 = substr($str_field,$pos2);
							$pos2 = strpos($str_field2, ",");
							$date_end = substr($str_field2,$pos+2,11);
							//$month2 = monToMonth(substr($date_end,3,3));
							$date_end = str_replace($mths_abr,$mths_cspell,$date_end);
							$date_end = strtotime($date_end);
							

/*echo 'sta:'.$date_start.'<br>';
echo 'str:'.$str_date.'<br>';
echo 'end'.$date_end.'<br><br>';
*/
							if ($date_start <= $str_date && $date_end >= $str_date){
//								echo $listOfEntries[$p_counter]["parent"];
							$file_path = dirname($this->parent->script).'/'.$category_path.'/'.$file_uri_var;

					$out .= "<td style='color:#999999'><a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".$index))."'href='$file_path?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".$index."</a></td>";

//					$out .= "<td style='color:#999999'><a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".$index))."'href='$script?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".$index."</a></td>";
									
								}
								$str_date = "";
					}
				}
				/**** Ends Loop to add file paths for event calendars Previous Month offdate event( Added By Muhammad Imran )****/


				
				}else
					$out .= "<td>".$index."</td>";
				
			
				$index++;
			}
		}
		$zindex=1;
		
		for($z=0;$z<8;$z++){
			if($index <= $params[1]){
				$out.="<tr>";
				for($i=0;$i<7;$i++){
				
					if($index > $params[1]){
						
						if($m1 == 12){
							$month =1;
							$year = $y+1;
						}else{
							$month = $m1+1;
							$year = $y;
						}
						$date = $year."-".$month."-".($index-$params[1]);
						$sql ="select year(md_date_remove) as yr, month(md_date_remove) as mth, md_date_remove from information_entry 
					inner join metadata_details on md_link_id = ie_identifier and md_module='EVENT_' and md_client= ie_client 
					left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list 
				where ('$date' <=md_date_remove && '$date' >= md_date_available) and ie_published=1 and ie_status = 1 and ie_client=$this->client_identifier and ie_list = $list
				order by ie_uri ";
						if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::show_calendar",__LINE__,"<li>$sql</li>"));}
						$result  = $this->call_command("DB_QUERY",Array($sql));
            			$db_num_rows  = $this->call_command("DB_NUM_ROWS",array($result));
           				if($db_num_rows >= 1){
							$r = $this->call_command("DB_FETCH_ARRAY",Array($result));
							list($e_year, $e_month, $e_day) = explode('-', $r["md_date_remove"]);  
							list($e_day,$e_time) = explode(' ', $e_day); 
							

				/**** Starts Loop to add file paths for event calendars Previous Year offdate event( Added By Muhammad Imran )****/
				for($p_counter=0; $p_counter<$m_list_entries; $p_counter++){
					$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$listOfEntries[$p_counter]["parent"].".xml";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
					if (file_exists($fname)){
//						$out .= join("", file($fname));
						if (!$myxml=simplexml_load_file($fname)){
							echo 'Error reading the XML file';
							}
		//					print_r($myxml);die;
							foreach($myxml as $event_link){
								//echo '<br />FLE: ' . $event_link->value . '<br />';	
		//						echo $file_uri = $event_link->value;die;
								$file_uri = $event_link->value;
		//						echo $file_cat = $event_link->choosencategory;
		//						echo '<br>';
								if ($file_uri != "")
									$file_uri_var = $file_uri;
		//							$file_path = str_replace("index.php","",$script).$file_uri;
							}
							
							$category_identifier = "";
							$xml_file = file_get_contents($fname);
							// load as file
							$sitemap = new SimpleXMLElement($xml_file);
							foreach($sitemap as $values_var) {
							//    echo "Number: {$url['identifier']}: {$url->loc} - {$url->lastmod} - {$url->changefreq} - {$url->priority}\r\n";
								$category_identifier = "{$values_var['identifier']}";
							} 
							
							$category_path = "";
							foreach($cat as $key_cat => $catValue){
								if ($cat[$key_cat]["category_identifier"] == $category_identifier){
//									echo $cat[$key_cat]["category_identifier"].'<br>';
									$category_path = $cat[$key_cat]["path"];
								}
							}
							//$category_identifier;

							$str_date = strtotime($this->libertasGetDate("d F Y",strtotime($date)));

							//echo $str_date.'<br>';die;
							/**Get Start Date**/
							$file_contents = join("", file($fname));

							/* Get URI*/
							$pos_uri = strpos($file_contents, "<field name='uri' link='no' visible='no'><value>");
							$str_pos_uri = substr($file_contents,$pos_uri);
//							$pos_uri2 = strpos($str_pos_uri, "</value>");
							$end_value = strpos($str_pos_uri, "</value></field>");
							$file_uri_var = substr($str_pos_uri,$str_pos_uri+57,$end_value-57-3);
							/* Get URI*/


							$pos = strpos($file_contents, "<field type='datetime' name='ie_odate1' visible='yes'>");
							if ($pos == "")
								$pos = strpos($file_contents, "<field type='date' name='ie_odateonly1' visible='yes'>");

							$str_field = substr($file_contents,$pos);
							$pos = strpos($str_field, ",");
							$date_start = substr($str_field,$pos+2,11);
							$date_start = str_replace($mths_abr,$mths_cspell,$date_start);
							$date_start = strtotime($date_start);
							
							/**Get End Date**/
							$pos2 = strpos($str_field, "<field type='datetime' name='ie_odate2' visible='yes'>");
							if ($pos2 == "")
								$pos2 = strpos($str_field, "<field type='date' name='ie_odateonly2' visible='yes'>");

							$str_field2 = substr($str_field,$pos2);
							$pos2 = strpos($str_field2, ",");
							$date_end = substr($str_field2,$pos+2,11);
							//$month2 = monToMonth(substr($date_end,3,3));
							$date_end = str_replace($mths_abr,$mths_cspell,$date_end);
							$date_end = strtotime($date_end);
							

/*echo 'stasss:'.$date_start.'<br>';
echo 'strsss:'.$str_date.'<br>';
echo 'endsss:'.$date_end.'<br><br>';
*/
							if ($date_start <= $str_date && $date_end >= $str_date){
//								echo $listOfEntries[$p_counter]["parent"];
							$file_path = dirname($this->parent->script).'/'.$category_path.'/'.$file_uri_var;

							$out .= "<td style='color:#999999'><a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".($index - $params[1])))."'href='$file_path?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".($index - $params[1])."</a></td>";

//							$out .= "<td style='color:#999999'><a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".($index - $params[1])))."'href='$script?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".($index - $params[1])."</a></td>";
									
								}
								$str_date = "";
					}
				}
				/**** Ends Loop to add file paths for event calendars Previous Year offdate event( Added By Muhammad Imran )****/



						
						}else
							$out .= "<td style='color:#999999'>".($index - $params[1])."</td>";
						$index++;
					}else{	
						$month = $m1;
						$year = $y;
						$date = $year."-".$month."-".$index;
						$sql ="select year(md_date_remove) as yr, month(md_date_remove) as mth, md_date_remove from information_entry 
					inner join metadata_details on md_link_id = ie_identifier and md_module='EVENT_' and md_client= ie_client 
					left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list 
				where ('$date' <=md_date_remove && '$date' >= md_date_available) and ie_published=1 and ie_status = 1 and ie_client=$this->client_identifier and ie_list = $list
				order by ie_uri ";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::show_calendar",__LINE__,"<li>$sql</li>"));}
					$result  = $this->call_command("DB_QUERY",Array($sql));
            		$db_num_rows  = $this->call_command("DB_NUM_ROWS",array($result));
           			if($db_num_rows >= 1){
							$out .= "<td class='";
							if($today[0]==$y && $today[1]==$m && $today[2]==$index){
								$out .= "calendartoday";
							} else {
								$out .= "calendar";
							}
							$out .= "'>";
						
							$sql ="select year(md_date_remove) as yr, month(md_date_remove) as mth, md_date_remove from information_entry 
					inner join metadata_details on md_link_id = ie_identifier and md_module='EVENT_' and md_client= ie_client 
					left outer join information_update_access on iua_entry=ie_parent and ie_client=iua_client and iua_list=ie_list 
				where ('$date' <=md_date_remove && '$date' >= md_date_available) and ie_published=1 and ie_status = 1 and ie_client=$this->client_identifier and ie_list = $list
				order by ie_uri ";
							if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__."::show_calendar",__LINE__,"<li>$sql</li>"));}
							$result  = $this->call_command("DB_QUERY",Array($sql));
            				$db_num_rows  = $this->call_command("DB_NUM_ROWS",array($result));
           					if($db_num_rows >= 1){
								$r = $this->call_command("DB_FETCH_ARRAY",Array($result));
								list($e_year, $e_month, $e_day) = explode('-', $r["md_date_remove"]); 
								list($e_day,$e_time) = explode(' ', $e_day); 			 

				//$str_date = strtotime($this->libertasGetDate("d F Y",strtotime($r["yr"]."-".$r["mth"]."-".$index)));
				/**** Starts Loop to add file paths for event calendars( Added By Muhammad Imran )****/
				for($p_counter=0; $p_counter<$m_list_entries; $p_counter++){
					$fname = $data_files."/".$this->module_presentation_name."_".$this->client_identifier."_".$list."_".$lang."_".$listOfEntries[$p_counter]["parent"].".xml";
					if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"load_XML",__LINE__,"$fname"));}
					if (file_exists($fname)){
//						$out .= join("", file($fname));
						if (!$myxml=simplexml_load_file($fname)){
							echo 'Error reading the XML file';
							}
		//					print_r($myxml);die;
							$file_uri_var = "";
							foreach($myxml as $event_link){
								//echo '<br />FLE: ' . $event_link->value . '<br />';	
		//						echo $file_uri = $event_link->value;die;
//								$file_uri_repeat = $file_uri_var;
								$file_uri = $event_link->value;
		//						echo $file_cat = $event_link->choosencategory;
		//						echo '<br>';
								if ($file_uri != "" && $file_uri_var == "")
									$file_uri_var = $file_uri;
		//							$file_path = str_replace("index.php","",$script).$file_uri;
//								echo $file_uri.'<br>';
							}
	//						die;
							$category_identifier = "";
							$xml_file = file_get_contents($fname);
							// load as file
							$sitemap = new SimpleXMLElement($xml_file);
							foreach($sitemap as $values_var) {
							//    echo "Number: {$url['identifier']}: {$url->loc} - {$url->lastmod} - {$url->changefreq} - {$url->priority}\r\n";
								$category_identifier = "{$values_var['identifier']}";
							} 
							
							$category_path = "";
							foreach($cat as $key_cat => $catValue){
								if ($cat[$key_cat]["category_identifier"] == $category_identifier){
//									echo $cat[$key_cat]["category_identifier"].'<br>';
									$category_path = $cat[$key_cat]["path"];
								}
							}
							//$category_identifier;

							////$str_date = strtotime($this->libertasGetDate("d F Y",strtotime($year."-".$month."-".$index)));
							$str_date = strtotime($this->libertasGetDate("d F Y",strtotime($date)));
							
							//echo $str_date.'<br>';die;
//							echo $listOfEntries[$p_counter]["parent"].'<br>';

							/**Get Start Date**/
							$file_contents = join("", file($fname));


							/* Get URI*/
							$pos_uri = strpos($file_contents, "<field name='uri' link='no' visible='no'><value>");
							$str_pos_uri = substr($file_contents,$pos_uri);
//							$pos_uri2 = strpos($str_pos_uri, "</value>");
							$end_value = strpos($str_pos_uri, "</value></field>");
							$file_uri_var = substr($str_pos_uri,$str_pos_uri+57,$end_value-57-3);
							/* Get URI*/



							$pos = strpos($file_contents, "<field type='datetime' name='ie_odate1' visible='yes'>");
							if ($pos == "")
								$pos = strpos($file_contents, "<field type='date' name='ie_odateonly1' visible='yes'>");

							$str_field = substr($file_contents,$pos);
							$pos = strpos($str_field, ",");
							$date_start = substr($str_field,$pos+2,11);
							$date_start = str_replace($mths_abr,$mths_cspell,$date_start);
							$date_start = strtotime($date_start);
							
							/**Get End Date**/
							$pos2 = strpos($str_field, "<field type='datetime' name='ie_odate2' visible='yes'>");
							if ($pos2 == "")
								$pos2 = strpos($str_field, "<field type='date' name='ie_odateonly2' visible='yes'>");

							$str_field2 = substr($str_field,$pos2);
							$pos2 = strpos($str_field2, ",");
							$date_end = substr($str_field2,$pos+2,11);
							//$month2 = monToMonth(substr($date_end,3,3));
							$date_end = str_replace($mths_abr,$mths_cspell,$date_end);
							$date_end = strtotime($date_end);

/*
echo 'sta:'.$date_start.'<br>';
echo 'str:'.$str_date.'<br>';
echo 'end'.$date_end.'<br><br>';
*/

							if ($date_start <= $str_date && $date_end >= $str_date){
//								echo $listOfEntries[$p_counter]["parent"];
							$file_path = dirname($this->parent->script).'/'.$category_path.'/'.$file_uri_var;

									$out .= "<a class='calendar' title='View the events for ".$this->libertasGetDate("l dS of F Y",strtotime($r["yr"]."-".$r["mth"]."-".$index))."'href='$file_path?y=".$r["yr"]."&amp;m=".$r["mth"]."&amp;d=".$e_day."'>".$index."</a>";
								}
								$str_date = "";
					}
				}
				/**** Ends Loop to add file paths for event calendars( Added By Muhammad Imran )****/
						
							}else
								$out .= "$index";
							
							$out.="</td>";
						}else {
							$out .= "<td class='";
							if($today[0]==$y && $today[1]==$m && $today[2]==$index){
								$out .= "calendartoday";
							} else {
								$out .= "calendar";
							}
							
							$out .= "'>$index</td>";
						}
						$index++;
					}
				}
				$out.="</tr>";
			}
		}
		$out.="</table>";

//echo $out;	
		if($show_label==0){
			return "<module name='$this->module_name' display='TEXT'><text><![CDATA[$out]]></text></module>";
		} else {
			return "<module name='$this->module_name' display='TEXT'><label show='1'><![CDATA[$label]]></label><text><![CDATA[$out]]></text></module>";
		}
	}
}
?>