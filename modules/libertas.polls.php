<?php
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.polls.php
* @date 09 Oct 2002
*/
/**
* Enterprise poll presentations module
*/
class polls extends module{

	/**#@+
	* Class Variables
    * @var String
	*/
	var $module_load_type		= "__PRESENTATION__"; // options are __SYSTEM__, __ADMIN__ or __PRESENTATION__
	var $module_name			= "polls";
	var $module_name_label		= "Poll Manager Module (Presentation - Advanced Version)";
	var $module_label			= "MANAGEMENT_POLL";
	var $module_admin			= "0";
	var $module_debug			= false;
	var $module_creation		= "13/09/2002";
	var $module_version 		= '$Revision: 1.11 $';//"3.0";
	var $module_command			= "POLLS_"; 		// all commands specifically for this module will start with this token
	var $webContainer			= "POLLS_"; 		// all commands specifically for this module will start with this token
	
	/**#@+
	* Class Variables
    * @var Integer
	*/
	var $has_module_contact		= 0;
	var $has_module_group		= 0;
	
	/**#@+
	* Class Variables
    * @var Array
	*/
	var $display_options		= null;
	var $list_num_votes 		= Array(0, 3, 5, 10, 25, 50, 100);
	var $module_display_options = array(array("POLLS_DISPLAY","LOCALE_POLL_CHANNEL_OPEN","POLL_MANAGER"));
	var $module_admin_options = array(array("POLL_MANAGER","MANAGEMENT_POLL","POLLS_ALL"));
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
			if ($user_command==$this->module_command."VOTE"){
				$this->polls_vote($parameter_list);
			}
			if ($user_command==$this->module_command."DISPLAY"){
				return $this->polls_display($parameter_list);
			}
			if ($user_command==$this->module_command."INHERIT"){
				$this->call_command("POLLSADMIN_INHERIT",$parameter_list);
			}
		}else{
			return "";// wrong command sent to system
		}
	}
	
	/**
	* Initialise function
	*
	* This function will initialise some variables for this modules functions to use.
	*/
	function initialise(){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise",__LINE__,""));
		}
		$this->load_locale("poll");
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;

	}
	/**
	* display a poll
	* 
	* this functionality is based on the need to be able to display a group of polls and to display the appropraite poll 
	* per page
	* 
	* @param Array keys => "wo_owner_id","sr","poll_id","poll_group","displaymode"
	* @return String a string containing the XML representation of the poll form
	*/
	function polls_display($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"<pre>".print_r($parameters,true)."</pre>"));}
		$owner 		= $this->check_parameters($parameters,"wo_owner_id");
		$show_result_instead	= $this->check_parameters($parameters,"sr",0);
		$poll_id 				= $this->check_parameters($parameters,"poll_id",0);
		$poll_group				= $this->check_parameters($parameters,"poll_group",0);
		$displaymode				= $this->check_parameters($parameters,"displaymode",0);
		$poll_label				="";
		$out="";
		$show_poll=0;
		$get_poll_at_index=1;
		$sql = "
		select poll_group.*, count(pgl_poll) as total  from poll_group 
			left outer join menu_to_object on 
				(mto_client = pg_client and mto_object=pg_identifier and mto_module='$this->webContainer' and 
					(mto_menu=".$parameters["current_menu_location"]." or 
						(mto_menu is null and pg_all_locations=1)
					) and mto_publish=1
				) 
			inner join poll_group_list on pg_identifier = pgl_group and pg_client = pgl_client 
			inner join poll_list on pgl_poll = poll_list_identifier and poll_list_client = pgl_client 
		where 
			pg_client = $this->client_identifier and 
			(mto_menu=".$parameters["current_menu_location"]." or (mto_menu is null and pg_all_locations=1)) and 
			poll_list.poll_list_status=1 and 
			poll_group.pg_status=1 and 
			pg_identifier =$owner
		group by pg_identifier";

		$displayedlist = "";
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
		$result  = $this->call_command("DB_QUERY",Array($sql));
	    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$display_option				 = $r["pg_display_option"];
			$pg_identifier				 = $r["pg_identifier"];
			$security_options			 = $r["pg_security_option"];
			$show_results				 = $r["pg_results_available"];
			$poll_list_results_settings	 = $r["pg_results_settings"];
			$pg_results_on_same_page	 = $r["pg_results_on_same_page"];
			$results_after_num			 = $r["pg_number_of_votes"];
			$poll_list_msg_thankyou		 = $r["pg_msg_thankyou"];
			$poll_list_msg_already_voted = $r["pg_msg_already_voted"];
			$total 						 = $r["total"];
			$poll_label					 = $r["pg_label"];
//			print "[pgi:$pg_identifier, so:$security_options, $show_results, $poll_list_results_settings, $pg_results_on_same_page, $results_after_num]";

			$get_poll_at_index =1;
           	if($r["pg_display_settings"]==0){
				if ($total>1){
					srand(time());
					$get_poll_at_index=rand(1,$total);
				}
			}
           	if($r["pg_display_settings"]==1){
				if ($this->check_parameters($_SESSION,"POLL_PER_SESSION_".$pg_identifier,-1)==-1){
					if ($total>1){
						$get_poll_at_index=rand(1,$total);
						$_SESSION["POLL_PER_SESSION_".$pg_identifier] = $get_poll_at_index;
					}
				} else {
					$get_poll_at_index = $_SESSION["POLL_PER_SESSION_".$pg_identifier];
				}
			}
            if($r["pg_display_settings"]==2){
				if ($this->check_parameters($_SESSION,"POLL_CYCLE_".$pg_identifier,-1)==-1){
					if ($total>1){
						$_SESSION["POLL_CYCLE_".$pg_identifier] = 1;
						$get_poll_at_index = 1;
					}
				} else {
					$get_poll_at_index = $_SESSION["POLL_CYCLE_".$pg_identifier]+1;
					$_SESSION["POLL_CYCLE_".$pg_identifier] = $get_poll_at_index;
					if ($get_poll_at_index>$total){
						$get_poll_at_index = 1;
						$_SESSION["POLL_CYCLE_".$pg_identifier] = 1;
					}
				}
			}
			$show_poll=1;
			$sql = "select *
					from poll_group_list
						inner join poll_list on pgl_poll = poll_list_identifier and poll_list_client = pgl_client 
					where 
						pgl_client = $this->client_identifier and pgl_group = $pg_identifier and 
						poll_list.poll_list_status=1 and pgl_poll not in ($displayedlist -1)";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
			$resultpoll = $this->call_command("DB_QUERY",Array($sql));
			$numpolls 	= $this->call_command("DB_NUM_ROWS",Array($resultpoll));
			$found = 0;
			$c=0;
			$poll_identifier=-1;
            while($rec = $this->call_command("DB_FETCH_ARRAY",Array($resultpoll))){
				if ($poll_id == $rec["pgl_poll"]){
					$found = 1;
				}
				if ($c==0 && $get_poll_at_index>$numpolls ){
					$poll_identifier = $rec["pgl_poll"];
				} else if (($c == $get_poll_at_index-1) || (($get_poll_at_index-1)==-1)){
					$poll_identifier = $rec["pgl_poll"];
				}
				$c++;
			}
			if ($found == 1 && $pg_identifier == $poll_group){
				$poll_identifier = $poll_id;
			}
//			print "pi[$poll_identifier][$get_poll_at_index][$numpolls]";
			$displayedlist .= " ".$poll_identifier.",";
            $this->call_command("DB_FREE",Array($resultpoll));
			$sql = "select * from poll_list where poll_list_status = 1 and poll_list_client = $this->client_identifier and poll_list_identifier = $poll_identifier";
			if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL",__LINE__,"<pre>$sql</pre>"));}
			if($resultpoll = $this->call_command("DB_QUERY",array($sql))) {
				while($rec = $this->call_command("DB_FETCH_ARRAY",array($resultpoll))){
					$footer="";
					$locations					= "";
					$answer_counter 			= array();
					$identifier 				= $rec["poll_list_identifier"];
					$poll_list_label 			= $rec["poll_list_label"];
					$poll_question	 			= $rec["poll_list_question"];
					$query_string_data = "select poll_info_answer, count(poll_info_answer) as total from poll_info where poll_info_identifier = $poll_identifier group by poll_info_answer";
					if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$query_string_data"));}
					$total=0;
					if ($data_result = $this->call_command("DB_QUERY",array($query_string_data))){
						while ($row = $this->call_command("DB_FETCH_ARRAY",array($data_result))){
							$answer_counter["answer_".$row["poll_info_answer"]] = $row["total"];
							$total += $row["total"];
						}
						$this->call_command("DB_FREE",Array($data_result));
					}
					$answers  = "";
					$vote		= 0;
					$already	= 0;
					if($this->check_parameters($parameters,"poll_vote")==0 && $this->check_parameters($parameters,"poll_id")==$identifier){
						$already= 1;
					}
					$not_logged_in = 0;
					$show_buttons = 1;
					/*************************************************************************************************************************
                    * Security options 
					* 0 = Allow multiple voting
					* 1 = 1 vote per site visit (show results)
					* 2 = 1 vote per registed user (Requires Login - show results)
					* 3 = 1 vote per site visit (return to poll screen)
					* 4 = 1 vote per registed user (Requires Login - return to poll 
                    *************************************************************************************************************************/
					$list 	= $this->check_parameters($_SESSION,"voted_on",Array());
					if ($security_options==0 ){//|| $security_options==3 || $security_options==4
						$vote = 1; // can vote
					} else if (($security_options==1) || ($security_options==3)){
						/*************************************************************************************************************************
                        * check each polls that this session has voted on
                        *************************************************************************************************************************/
						$vote	= 1; // can vote
						if(in_array($identifier, $list)){
							$vote		=	0; // can not vote
							$already	=	1; // has voted
							if ($security_options==1){
								$show_result_instead=1; // show results
							}
						}
					} else if (($security_options==2) || ($security_options==4)){
						if ( $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",-1) > 0){
							$vote 	= 1; // can vote
						} else {
							$vote	= 0; // can not vote
							$not_logged_in = 1; // not logged in
						}
						if(in_array($identifier, $list)){
							$vote		=	0; // can not vote
							$already	=	1; // has voted
							if ($security_options==2){
								$show_result_instead=1; // show results
							}
						}
					}
//					print "a[$vote, $not_logged_in, $security_options, $show_result_instead]";
					$srn =0;
					if ($show_result_instead==1 && $poll_id == $identifier && $poll_group == $pg_identifier){
						$srn = 1;
					}
//					print "[a:: $already, v::$vote, sr::$show_results, pgrsp:: $pg_results_on_same_page, pi:: $poll_id, i:: $identifier, sri:: $show_result_instead, pg:: $poll_group, pg_i:: $pg_identifier]";
					/*************************************************************************************************************************
                    * for 
                    *************************************************************************************************************************/
					if ($security_options==1 && $already == 1 && (
							($show_result_instead==1 && $pg_results_on_same_page==1)
								||
							($show_result_instead==0 && $pg_results_on_same_page==0)
						)
					){
						$srn=1;
						$show_buttons = 0;
					}
					if ($srn == 1){
						$answers .= "<text><![CDATA[<table border='0' class='width100percent'>";
						for($index=1;$index<=10;$index++){
							if (!empty($rec["poll_list_answer$index"])){
								if (($total>0) && (!empty($answer_counter["answer_$index"]))){
									$value = round(($answer_counter["answer_$index"]/$total)*100,2);
								}else{
									$value=0;
								}
								$a = $this->check_parameters($answer_counter,"answer_$index",0);
								$answers .= "<tr><td>".$rec["poll_list_answer$index"];
								if($poll_list_results_settings==0)
									$preval = $value;//round(($a/$total)*100,2);
									$answers .= "<br/><img src='/libertas_images/general/graphs/bar_left.gif' width='5' height='20' alt='".($value)."%'/><img src='/libertas_images/general/graphs/bar_middle.gif' width='".($value)."' height='20' alt='".($value)."%'/><img src='/libertas_images/general/graphs/bar_right.gif' width='5' height='20' alt='".($value)."%'/>";
								if($poll_list_results_settings==1)
									$answers .= "<br/> ($value%)";
								if($poll_list_results_settings==2)
									$answers .= "<br/> ".$a ." ".LOCALE_VOTES;
								$answers .="</td></tr>";
							}
						}
						$answers .= "</table>]]></text>";
					} else {
						$answers .= "<radio name=\"poll_answer\" type='vertical'>";
						for($index=1;$index<=10;$index++){
							if (!empty($rec["poll_list_answer$index"])){
								if (($total>0) && (!empty($answer_counter["answer_$index"]))){
									$value = round(($answer_counter["answer_$index"]/$total)*100,2);
								}else{
									$value=0;
								}
								if ($show_results==1 && ($total > $this->list_num_votes[$results_after_num]) && $pg_results_on_same_page==1){
								$a = $this->check_parameters($answer_counter,"answer_$index",0);
								$preval =$value;
								$answers .= "	<option value=\"$index\"><![CDATA[".$rec["poll_list_answer$index"];
								if($poll_list_results_settings==0 && $displaymode!="textonly")
									$answers .= "<br/><img src='/libertas_images/general/graphs/bar_left.gif' width='5' height='20' alt='".($preval)."%'/><img src='/libertas_images/general/graphs/bar_middle.gif' width='".($preval)."' height='20' alt='".($preval)."%'/><img src='/libertas_images/general/graphs/bar_right.gif' width='5' height='20' alt='".($preval)."%'/>";
								if($poll_list_results_settings==1 ||($poll_list_results_settings==0 && $displaymode=="textonly"))
									if ($displaymode!="textonly"){
										$answers .= "<br/>";
									}
									$answers .= " ($value%)";
								if($poll_list_results_settings==2)
									$answers .= "<br/> ".$a ." ".LOCALE_VOTES;
								$answers .="]]></option>";
								} else {
									$answers .= "	<option value=\"$index\"><![CDATA[".$rec["poll_list_answer$index"]."]]></option>";
								}
							}
						}
						$answers .= "</radio>";
					}
//					print "b[$vote, $not_logged_in, $security_options, $show_result_instead]";
					$out .="<module name=\"".$this->module_name."\" display=\"form\">";
					$out .= "<form name=\"poll_submission_form_$identifier\" method=\"post\">";
					$out .= "<label><![CDATA[$poll_label]]></label>";
					$out .= "<text class='label'><![CDATA[$poll_question]]></text>";
					$out .= "<input type=\"hidden\" name=\"command\" value=\"POLLS_VOTE\"/>";
					$out .= "<input type=\"hidden\" name=\"poll_group\" value=\"$pg_identifier\"/>";
					$out .= "<input type=\"hidden\" name=\"poll_identifier\" value=\"$identifier\"/>";
					$out .= $answers;
					$list 	= $this->check_parameters($_SESSION,"voted_on",Array());
					$found_voted = 0;
					for($index=0;$index<count($list);$index++){
						if($list[$index] == $identifier && $poll_group == $pg_identifier){
							$found_voted=1;
						}
					}
					if($this->check_parameters($parameters,"poll_vote")==2 && $this->check_parameters($parameters,"poll_id")==$identifier){
						$footer .= "<text><![CDATA[<span class='alignitcenter'><strong>".LOCALE_CHOOSE_ANSWER."</strong></span>]]></text>";
					}
//					print "c[$vote, $not_logged_in, $security_options, $show_result_instead]";
//					print "<!-- [$show_buttons,$vote,$show_results,$pg_results_on_same_page, $poll_id, $identifier, $show_result_instead, $poll_group, $pg_identifier, $vote, $already] -->";
//					print "line::".__LINE__." = [$show_buttons,$vote,$show_results,$pg_results_on_same_page, $poll_id, $identifier, $show_result_instead, $poll_group, $pg_identifier, $vote, $already]";
					//			1			1			1		0							25			25		0						7			7
					if ($show_buttons==1){
						if ($vote == 1){
							if ($show_results==1 && $pg_results_on_same_page==0){
								if ($poll_id==0){
									$out .= "<input type=\"submit\" iconify=\"VOTE\" value=\"".LOCALE_VOTE_NOW."\"/>";
									$footer .= "<text><![CDATA[<span class='alignitcenter'><a href='".$this->parent->script."?poll_id=$identifier&amp;sr=1&amp;poll_group=$pg_identifier'>".LOCALE_VIEW_RESULTS."</a></span>]]></text>";
								} else {
									if ($poll_id == $identifier && $show_result_instead==1 && $poll_group == $pg_identifier){
										$footer .= "<text><![CDATA[<span class='alignitcenter'><a href='".$this->parent->script."?'>".LOCALE_BACK_TO_VOTING."</a></span>]]></text>";
									} else {
										$out .= "<input type=\"submit\" iconify=\"VOTE\" value=\"".LOCALE_VOTE_NOW."\"/>";
										$footer .= "<text><![CDATA[<span class='alignitcenter'><a href='".$this->parent->script."?poll_id=$identifier&amp;sr=1&amp;poll_group=$pg_identifier'>".LOCALE_VIEW_RESULTS."</a></span>]]></text>";
									}
								}
							}  else {
								$out .= "<input type=\"submit\" iconify=\"VOTE\" value=\"".LOCALE_VOTE_NOW."\"/>";
							}
						} else {
							if ($already == 1){// && ($show_result_instead==1 && $pg_results_on_same_page==1) || ($show_result_instead==0 && $pg_results_on_same_page==0)){
								$footer .= "<text><![CDATA[<span class='alignitcenter'>$poll_list_msg_already_voted</span>]]></text>";
							}
							if ($not_logged_in==1){
								$footer .= "<text><![CDATA[<span class='alignitcenter'>". LOCALE_LOGIN_REQUIRED . "</span>]]></text>";
							}
							if ($show_results==1 && $show_result_instead==1 && $pg_results_on_same_page==0 && $poll_group == $pg_identifier){
								$out .= "<text><![CDATA[<span class='alignitcenter'><a href='".$this->parent->script."?'>".LOCALE_BACK_TO_VOTING."</a></span>]]></text>";
							} else if ($show_results==1 && $show_result_instead==0 && $pg_results_on_same_page==0 && ($poll_group == $pg_identifier || $poll_group == 0)){
								$out .= "<text><![CDATA[<span class='alignitcenter'><a href='".$this->parent->script."?poll_id=$identifier&amp;sr=1&amp;poll_group=$pg_identifier'>".LOCALE_VIEW_RESULTS."</a></span>]]></text>";
							}
						}
					}
					$out .= "$footer</form>";
					$out .="</module>";
				}
			}
        }
		return $out;
	}
	/**
	* vote on a poll
	* 
	* @param Array keys => "poll_group","poll_answer"
	*/
	function polls_vote($parameters){
		if ($this->module_debug){ $this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,__FUNCTION__,__LINE__,"<pre>".print_r($parameters,true)."</pre>"));}
		$ok=0;
		$poll_group = $this->check_parameters($parameters,"poll_group",-1);
		if ($this->check_parameters($parameters,"poll_answer","__NOT_FOUND__")!="__NOT_FOUND__"){
			$sql = "Select poll_info.*, 
						poll_group.pg_security_option 
					from poll_info 
						inner join poll_list on poll_list_identifier = poll_info_identifier and poll_list_client= poll_info_client 
						inner join poll_group_list on pgl_poll = poll_info_identifier and pgl_client = poll_info_client
						inner join poll_group on pgl_group = pg_identifier and pgl_client = pg_client
					where poll_info_client=$this->client_identifier and poll_info_session_id='".session_id()."' and poll_info_ip_address='".$_SERVER["REMOTE_ADDR"]."' and  poll_info_identifier=".$parameters["poll_identifier"];
			//print $sql;
			if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));}
			if($result = $this->call_command("DB_QUERY",array($sql))) {
				$num_rows= $this->call_command("DB_NUM_ROWS",array($result));
				$r = $this->call_command("DB_FETCH_ARRAY",array($result));
				if ($num_rows==0 || $r["pg_security_option"]==0){
					/*
					+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					| add the vote to the database
					+=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
					*/
					$poll_info_user 		= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
					$poll_info_date_voted	= $this->libertasGetDate("Y/m/d H:i:s");
					$sql = "insert into poll_info (poll_info_answer, poll_info_client, poll_info_session_id, poll_info_ip_address, poll_info_identifier, poll_info_user, poll_info_date_voted) values (".$parameters["poll_answer"].",$this->client_identifier,'".session_id()."', '".$_SERVER["REMOTE_ADDR"]."', ".$parameters["poll_identifier"].", '$poll_info_user', '$poll_info_date_voted');";
					if ($this->module_debug){$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"sql",__LINE__,"$sql"));}
					$this->call_command("DB_QUERY",array($sql));
					$ok=1;
					if(!is_array($this->check_parameters($_SESSION,"voted_on"))){
						$_SESSION["voted_on"]=Array();
					}
					$_SESSION["voted_on"][count($_SESSION["voted_on"])]=$parameters["poll_identifier"];
				}
			}
		} else {
			$ok =2;
		}
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("&amp;poll_vote=$ok&amp;poll_group=$poll_group&amp;poll_id=".$parameters["poll_identifier"]));
	}

}
?>