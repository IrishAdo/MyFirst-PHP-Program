<?PHP
/*************************************************************************************************************************
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.payment_gateway_worldpay.php
* @date 08 Nov 2004
*************************************************************************************************************************/

/*************************************************************************************************************************
* This module is the administration module for the Payment Gateway.
*************************************************************************************************************************/
require_once dirname(__FILE__)."/libertas.payment_gateway.php";
class paymentgateway_worldpay extends paymentgateway{
	/*************************************************************************************************************************
	*  Class Variables (generic
	*************************************************************************************************************************/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_name_label			= "Payment Gateway - WorldPay (Presentation)";
	var $module_name				= "paymentgateway";
	var $module_admin				= "1";
	var $module_command				= "PAYGATE_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "PAYGATE_";
	var $module_label				= "MANAGEMENT_PAYMENTGATEWAY";
	var $module_modify		 		= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.6 $';
	var $module_creation 			= "26/02/2004";
	/*************************************************************************************************************************
    * Lists to be used by payment systems 
    *************************************************************************************************************************/
	var $testModes = Array(
		Array("101", "LOCALE_TEST_MODE_FAIL_ALL"),
		Array("100", "LOCALE_TEST_MODE_PASS_ALL")
	);
	/**
    * field, label and type 
	*
	* this array is used in the account setup functions to define the fields that are to be sent with the order 
	* these fields are defined once for the client, and used in all orders
    */
	var $setupProperties			= Array(
		Array("instId",		"Your Unique WorldPay Installation ID Number","text"),
		Array("currency",	"Select the currency your prices are in","__CURRENCY__"),
		Array("testMode",	"Enable Test Mode","__TEST__")
	);
	/**
    * field and type 
	*
	* this array is used in the account setup functions to define the fields that are to be sent with the order 
	* these fields are defined per order
    */
	var $generatedProperties		= Array(
		Array("cartId",	"__SYS_REFERENCE__"),
		Array("desc",	"__USR_REFERENCE__"),
		Array("amount",	"__TOTAL__")
	); // to be defined per instance
	
	/*************************************************************************************************************************
	*                                D I R E C T O R Y   S E T U P   F U N C T I O N S
	*************************************************************************************************************************/
	
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
		* load the required locale
		*************************************************************************************************************************/
		$this->load_locale("payment_gateway_worldpay");
		parent::initialise();
		return 1;
	}
	/*************************************************************************************************************************
	*                         P A Y M E N T   M A N A G E R   F U N C T I O N S   F O R   W O R L D P A Y
	*************************************************************************************************************************/
	
	/*************************************************************************************************************************
	* request payment of an order
	*
	* if the request has already been made then do not process again, ie user refreshes the screen
	*
	* @param Integer the id of the basket
	* @param Double the amount total of the basket
	* @param String The description of the order
	* @return String XML representationof the form
	*************************************************************************************************************************/
	function payment_request($parameters){
		$basket_identifier	= $this->check_parameters($parameters,"__SYS_REFERENCE__",-1);
		$description 		= $this->validate($this->check_parameters($parameters,"__USR_REFERENCE__","Order from [http://".$this->parent->domain."]"));
		/**
        * if the amount is zero or less then exit
        */
		if($basket_identifier==-1){
			return "";
		}
		/**
        * get Account setup details
        */
		$pao_identifier = -1;
		$pad_identifier = -1;
		$pao_pad 		= -1;
		$basket_total	= 0;
		$items			= Array();
		$sql = "select * from shop_basket_container
					inner join shop_basket_items on shop_basket_identifier = shop_item_basket and shop_basket_client=shop_item_client
				where shop_basket_identifier = $basket_identifier and shop_basket_client = $this->client_identifier";
		//print $sql;
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$items[count($items)] = Array(
				"label"		=> $r["shop_item_title"],
				"price"		=> $r["shop_item_pickup_price"],
				"discount"	=> $r["shop_item_pickup_discount"],
				"quantity"	=> $r["shop_item_quantity"],
				"weight"	=> $r["shop_item_weight"]
			);
        	$basket_total += $r["shop_item_pickup_price"] * $r["shop_item_quantity"];
			$bstatus= $r["shop_basket_status"];
			$bill	= $r["shop_basket_bill_contact"];
			$del	= $r["shop_basket_delivery_contact"];
        }
        $this->call_command("DB_FREE",Array($result));
		if($bstatus<4){
			$required = Array("contact_name", "contact_address", "contact_country", "contact_postcode", "contact_telephone", "contect_fax", "contact_email");
			$bill_details		= $this->call_command("CONTACT_GET_DETAILS", Array("identifier"=>$bill, "required" => $required, "restrict_country"=>1));
			$delivery_details	= $this->call_command("CONTACT_GET_DETAILS", Array("identifier"=>$del,	"required" => $required, "restrict_country"=>1));
			$propertyValues = Array();
			$sql = "select * from payment_account_details 
						left outer join payment_account_orders on  pao_client = pad_client and pao_basket = $basket_identifier
					where  pad_client = $this->client_identifier";
	        $result  = $this->call_command("DB_QUERY",Array($sql));
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$pao_identifier = $this->check_parameters($r,"pao_identifier",-1);
				$pad_identifier	= $r["pad_identifier"];
				$pad_uri		= $r["pad_uri"];
	        }
	        $this->call_command("DB_FREE",Array($result));
			$sql = "select * from payment_account_properties where pap_client = $this->client_identifier and pap_identifier = $pad_identifier";
	        $result  = $this->call_command("DB_QUERY",Array($sql));
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
	        	$propertyValues[count($propertyValues)] = Array("key"=>$r["pap_property"], "value"=>$r["pap_value"]);
	        }
	        $this->call_command("DB_FREE",Array($result));
			/**
	        * add new entries if required
	        */
			if ($pao_identifier==-1){
				$user_id  = $this->check_parameters($_SESSION,"SESION_USER_IDENTIFIER",-1);
				$pao_identifier = $this->getUid();
				$sql = "insert into payment_account_orders (pao_identifier, pao_user, pao_client, pao_basket, pao_status, pao_pad) 
							values 
						($pao_identifier, $user_id, $this->client_identifier, $basket_identifier, 0, $pad_identifier)";
				$this->call_command("DB_QUERY",Array($sql));
				/*
					Array("cartId",	"__SYS_REFERENCE__"),
					Array("desc",	"__USR_REFERENCE__")
				*/
				for($i=0;$i<count($this->generatedProperties);$i++){
					$value = $this->check_parameters($parameters,"__SYS_REFERENCE__","__NOT_FOUND__");
					if ($value != "__NOT_FOUND__"){
						$sql= "insert into payment_order_properties (pop_identifier, pop_client, pop_property, pop_value)
									values
								($pao_identifier, $this->client_identifier, '".$this->generatedProperties[$i][0]."', '$value')";
						$this->call_command("DB_QUERY",Array($sql));
						$this->generatedProperties[$i][2] = $value;
					}
				}
			}
			/**
	        * display this form
	        */
			$call_back = "http://".$this->parent->domain."".$this->parent->base."_process.php";
//			www.myserver.com/mycallbackscript.cgi
			
			$out ="<module name=\"".$this->module_name."\" display=\"form\">";
//			$out .="<form name=\"".$this->module_name."_form\" action='$pad_uri' label=\"Confirm Details\">";
			$out .="<form name=\"".$this->module_name."_form\" action='https://select.worldpay.com/wcc/purchase' label=\"Confirm Details\">";
				$out .= "<text><![CDATA[Choose to pay your account via our Worldpay secure payment form]]></text>
						<input type='hidden' name='callback' ><![CDATA[$call_back]]></input>
						<input type='hidden' name='MC_callback' ><![CDATA[$call_back]]></input>
						<input type='hidden' name='callbackPW' ><![CDATA[$call_back]]></input>
						<input type='hidden' name='M_callback' ><![CDATA[$call_back]]></input>
						<input type='hidden' name='CM_callback' ><![CDATA[$call_back]]></input>
						<input type='hidden' name='C_callback' ><![CDATA[$call_back]]></input>
";
						
			$max_setup_values	= count($this->setupProperties);
			$max_prop_values	= count($propertyValues);
			for($i=0; $i<$max_setup_values; $i++){
				for($v=0; $v<$max_prop_values; $v++){
					if ($propertyValues[$v]["key"]==$this->setupProperties[$i][0]){
						if($propertyValues[$v]["value"]!=""){
							$out .="	<input type=\"hidden\" name=\"".$this->setupProperties[$i][0]."\"><![CDATA[".$propertyValues[$v]["value"]."]]></input>";
						}
					}
				}
			}
			for($i=0;$i<count($this->generatedProperties);$i++){
				if($this->generatedProperties[$i][1]=="__SYS_REFERENCE__"){
					$value = $basket_identifier;
				} else if($this->generatedProperties[$i][1]=="__USR_REFERENCE__"){
					$value = $description;
				} else if($this->generatedProperties[$i][1]=="__TOTAL__"){
					$value = $basket_total;
				} else {
					$value = "";
				}
				$out .="		<input type=\"hidden\" name=\"".$this->generatedProperties[$i][0]."\"><![CDATA[".$value."]]></input>";
			}
			
			$m = count($items);
			if($m>0){
				/*
				$table = "<table style='width:100%' cellspacing='0' cellpadding='3' border='1' summary='contents of basket'>";
					$table .="<tr>";
					$table .="<th>Label</th>";
					$table .="<th class='classright'>Price</th>";
					$table .="<th class='classright'>Discount</th>";
					$table .="<th class='classright'>Quantity</th>";
					$table .="<th class='classright'>Weigth (kg)</th>";
					$table .="<th class='classright'>Cost</th>";
					$table .="</tr>";
				$totalw =0;
				for($i=0;$i<$m;$i++){
					$table .="<tr>";
					$table .="<td>".$items[$i]["label"]."</td>";
					$table .="<td align='right'>".round($items[$i]["price"],2)."</td>";
					$table .="<td align='right'>".round($items[$i]["discount"],2)."</td>";
					$table .="<td align='right'>".$items[$i]["quantity"]."</td>";
					$table .="<td align='right'>".round($items[$i]["weight"] * $items[$i]["quantity"],2)."</td>";
					$totalw += ($items[$i]["weight"] * $items[$i]["quantity"]);
					$table .="<td align='right'>".round(($items[$i]["price"]-$items[$i]["discount"]) * $items[$i]["quantity"],2)."</td>";
					$table .="</tr>";
				}
				$sql = "select * from contact_data 
					inner join contact_address on contact_data.contact_address = address_identifier
				where contact_identifier = $del and contact_client = $this->client_identifier";
        		$result  = $this->call_command("DB_QUERY",Array($sql));
				$country = -1;
		        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        			$country = $r["address_country"];
		        }
		        $this->call_command("DB_FREE",Array($result));
				if($country == -1){
					$shipping_cost = -1;
				}else{
					$shipping_cost = $this->call_command("SHOP_GET_COST",Array("country"=>$country, "weight"=>$totalw));
				}
				if($shipping_cost>-1){
					$table .="	<tr>
									<td align='right' colspan='4'><strong>Shipping Weigth/Cost</strong></td><td align='right'>$totalw</td><td align='right'>".number_format(round($shipping_cost,2),2,'.','')."</td>
								</tr>";
				} else {
					$shipping_cost=0;
				}
				$basket_total = $basket_total+$shipping_cost;
				if($this->vat_rate!=0 && $this->vat_rate!=""){
					$table .="	<tr>
									<td align='right' colspan='4'><strong>Vat</strong></td><td align='right'>".number_format(round($this->vat_rate,2),2,'.','')."%</td><td align='right'>".number_format(round(((($basket_total/100) * $this->vat_rate)),2),2,'.','')."</td>
								</tr>";
					$basket_total += round((($basket_total/100) * $this->vat_rate),2);
				}

				$table .="<tr>";
				$table .="<td colspan='4' class='alignright'><strong>Total</strong></td><td></td><td align='right'>".number_format($basket_total,2,'.','')."</td>";				$table .="</tr>";
				$table.="</table>";
				
				$out .= "<text><![CDATA[<p><strong>You ordered the following items</strong></p>$table]]></text>";
				$out .= "<text><![CDATA[<p><strong>Billing details</strong></p>".$bill_details["text"]."]]></text>";
				$out .= "<text><![CDATA[<p><strong>Delivery details</strong></p>".$delivery_details["text"]."]]></text>";
				*/
				$fields = Array(
					Array("name",		"contact_name"),
					Array("address",	"contact_address"),
					Array("country",	"contact_country"),
					Array("postcode",	"contact_postcode"),
					Array("tel",		"contact_telephone"),
					Array("fax",		"contect_fax"),
					Array("email",		"email")
				);
	//			print_r($bill_details["array"]);
	//			print_r($delivery_details["array"]);
				$m=count($fields);
				foreach ($bill_details["array"] as $key => $value){
					for($i=0;$i<$m;$i++){
						if($key == $fields[$i][1]){
							if($value!=""){
								$out .="	<input type=\"hidden\" name=\"".$fields[$i][0]."\"><![CDATA[".$value."]]></input>";
							}
						}
					}
				}
				foreach ($delivery_details["array"] as $key => $value){
					for($i=0;$i<$m;$i++){
						if($key == $fields[$i][1]){
							if($value!=""){
								$out .="	<input type=\"hidden\" name=\"M_".$fields[$i][0]."\"><![CDATA[".$value."]]></input>";
							}
						}
					}
				}
				//$bill_details["array"];
				//$delivery_details["array"];
			}
			$iev_basket_var = $_SESSION["IEV_BASKET_SESSION"];
			unset($_SESSION["IEV_BASKET_SESSION"]);
			
			$out .="			<input type=\"hidden\" name=\"MC_session\" ><![CDATA[".session_id()."]]></input> 
			<input type=\"hidden\" name=\"MC_iev_basket_var\" ><![CDATA[".$iev_basket_var."]]></input> 
								
								<input type=\"submit\" iconify=\"CONFIRM\" value=\"".LOCALE_CONFIRM."\"/>";
			$out .="</form>";
			$out .="</module>";
		} else {
			$out ="<module name=\"".$this->module_name."\" display=\"entry\">";
//			$out .="	<form name=\"".$this->module_name."_form\" action='$pad_uri' label=\"Confirm Details\">";
			$out .="		<text><![CDATA[Sorry this basket has already been purchased]]></text>";
//			$out .="		<input type=\"submit\" iconify=\"CONFIRM\" value=\"".LOCALE_CONFIRM."\"/>";
//			$out .="	</form>";
			$out .="</module>";	
		}
		return $out;
	}
	/*************************************************************************************************************************
	* callback process for an order
	*
	* this is the script that a callback function will call
	*
	* @param Array Array of elements that are to be processed
	*************************************************************************************************************************/
	function payment_process($parameters){
		$basket_identifier	= $this->check_parameters($parameters, "cartId");
		$basket_session		= $this->check_parameters($parameters, "MC_session");
		$transStatus		= $this->check_parameters($parameters, "transStatus");
		$iev_basket_var		= $this->check_parameters($parameters, "MC_iev_basket_var");
		
		if($transStatus=="Y"){
			$transStatus=1;
		} else {
			$transStatus=0;
		}
		$now = $this->libertasGetDate();
		$sql = "update 
					shop_basket_container 
				set 
					shop_basket_status = 4,
					shop_basket_payment = $transStatus
				where 
					shop_basket_status = 3 and 
					shop_basket_identifier = $basket_identifier and
					shop_basket_session = '$basket_session' and
					shop_basket_client = $this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$sql = "update 
					payment_account_orders 
				set 
					pao_status = ".($transStatus+1).",
					pao_approved = '$now'
				where 
					pao_basket = $basket_identifier and
					pao_client = $this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		$_SESSION["SHOP_BASKET_IDENTIFIER"]=-1;
		$sql = "SELECT *
			FROM `payment_account_orders`
				inner join memo_information on mi_link_id = pao_pad and mi_type = 'PAYGATEADMIN_' and mi_field in ('deny','confirm') and mi_client=pao_client
			where pao_client=$this->client_identifier and pao_basket = $basket_identifier
		";
		$screens = Array();
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$screens[$r["mi_field"]] = $r["mi_memo"];
        }
        $this->call_command("DB_FREE",Array($result));
		$_SESSION["SHOP_BASKET_IDENTIFIER"]=-1;
		if($transStatus==1){
			$msg = $screens["confirm"];
		} else {
			$msg = $screens["deny"];
		}
/*
		$out ="<module name=\"".$this->module_name."\" display=\"entry\">";
		$out .="	<text><![CDATA[$msg]]></text>";
		$out .="</module>";	
*/

		$out ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="<form name=\"".$this->module_name."_form\" label=\"Confirm Details\" action=\"index.php\">";
		if ($transStatus==0){
			$out .="	<text><![CDATA[The order failed for the following reason <ul><li>".$soap_result["message"]."</li></ul>]]></text>";
		} else {

			/* Starts to Get Basket form information */
			/*
			$sql = "SELECT *
			FROM `information_entry_values`
			where iev_client=$this->client_identifier and iev_entry = $iev_basket_var
			";
			*/

			/*
			$sql = "SELECT information_entry_values.iev_value,information_fields.if_label
			FROM `information_entry_values`, `information_fields`
			where iev_client=$this->client_identifier and iev_entry = $iev_basket_var
			and iev_list = if_list and iev_client=$this->client_identifier group by if_label order by if_rank
			";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$iev_str = "<br><b>Your details</b><br><br><table border=0 cellspacing=1 cellpadding=1>";
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				if ($r["iev_value"] != "" && $r["if_label"] != "")
					$iev_str .= "<tr><td>".$r["if_label"]."</td><td>".$r["iev_value"]."</td></tr>";
			}
			$iev_str .= "</table>";
			*/
			
			$sql = "SELECT *
			FROM `information_entry_values`
			where iev_client=$this->client_identifier and iev_entry = $iev_basket_var
			";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$i = 0;
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$arr_iev_value[$i]["value"] = $r["iev_value"];
				$arr_iev_list = $r["iev_list"];
				$i++;
			}
			
			$sql = "SELECT *
			FROM `information_fields`
			where if_client=$this->client_identifier and if_list = $arr_iev_list group by if_label order by if_rank
			";
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$j = 0;
			while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$arr_iev_value[$j]["label"] = $r["if_label"];
				$j++;
			}
			
	//		print_r($arr_iev_value);
			$iev_str = "<br><b>Your details</b><br><br><table border=0 cellspacing=1 cellpadding=1 width=80%>";
			foreach ($arr_iev_value as $values){
//				$iev_str .= "<tr><td>Lab: ".$values["label"]."</td><td>Val: ".$values["value"]."</td></tr>";
//				if ($r["iev_value"] != "" && $r["if_label"] != "")
					$iev_str .= "<tr><td>".$values["label"]."</td><td>".$values["value"]."</td></tr>";
			}
			$iev_str .= "</table>";

			
			
			/* Ends to Get Basket form information */
/*Array(
					"body"		=> "<style>.row {width:100%;display:block;}\n.cell {width:49%;display:inline;margin:2px;}</style>".str_replace(Array("<text><![CDATA[", "]]></text>", "<TEXT><![CDATA[", "]]&GT;</TEXT>"), Array("", "", "", ""),$basket["output"]),
					"from"		=> $this->check_prefs(Array("sp_from_email")),
					"to" 		=> $basket["del"]["array"]["contact_email"],
					"format"	=> "HTML",
					"subject"	=> "Order Confirmation ($basket_identifier)"
				)
				
*/		
			$basket = $this->call_command("SHOP_GET_ORDER", Array("basket_identifier"=>$basket_identifier));
			//$out .="	<text><![CDATA[email: ".$basket["del"]["array"]["contact_email"]."Basket:".$basket_identifier."]]</text>";
			$out .="	<text><![CDATA[The order has been validated, a member of staff will be notified soon]]></text>";

//					$to_email = "david.moore@ards-council.gov.uk";
				if(($this->parent->db_pointer->database == 'system_ards' && ($this->parent->domain == 'ards-council.gov.uk') || $this->parent->domain == 'www.ards-council.gov.uk')){
					$this->call_command("EMAIL_QUICK_SEND", 
						Array(
							"body"		=> "<style>.row {width:100%;display:block;}\n.cell {width:49%;display:inline;margin:2px;}</style>".str_replace(Array("<text><![CDATA[", "]]></text>", "<TEXT><![CDATA[", "]]&GT;</TEXT>"), Array("", "", "", ""),$basket["output"].$iev_str),
							"from"		=> $this->check_prefs(Array("sp_from_email")),
							"to" 		=> $basket["del"]["array"]["contact_email"],
							"cc" 		=> Array("david.moore@ards-council.gov.uk"),
							"format"	=> "HTML",
							"subject"	=> "Order Confirmation ($basket_identifier)"
						)
					);
				}else{
					$this->call_command("EMAIL_QUICK_SEND", 
						Array(
							"body"		=> "<style>.row {width:100%;display:block;}\n.cell {width:49%;display:inline;margin:2px;}</style>".str_replace(Array("<text><![CDATA[", "]]></text>", "<TEXT><![CDATA[", "]]&GT;</TEXT>"), Array("", "", "", ""),$basket["output"].$iev_str),
							"from"		=> $this->check_prefs(Array("sp_from_email")),
							"to" 		=> $basket["del"]["array"]["contact_email"],
							"format"	=> "HTML",
							"subject"	=> "Order Confirmation ($basket_identifier)"
						)
					);
				}


		}
		$out .= "		<input type=\"submit\" iconify=\"CONFIRM\" value=\"OK\"/>";
		$out .="</form>";
		$out .="</module>";

		return $out;
	}
}

?>