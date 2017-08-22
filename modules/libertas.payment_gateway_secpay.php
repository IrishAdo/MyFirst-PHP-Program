<?PHP
/*************************************************************************************************************************
* @@company Libertas Solutions Ltd
* @@package com.solutions.libertas.cms
* @@author Adrian Sweeney
* @@file libertas.payment_gateway_secpay.php
* @@date 08 Nov 2004
*************************************************************************************************************************/

/*************************************************************************************************************************
* This module is the administration module for the Payment Gateway.
*************************************************************************************************************************/
require_once dirname(__FILE__)."/libertas.payment_gateway.php";
class paymentgateway_secpay extends paymentgateway{
	/*************************************************************************************************************************
	*  Class Variables (generic
	*************************************************************************************************************************/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_name_label			= "Payment Gateway - secPay (Presentation)";
	var $module_name				= "paymentgateway";
	var $module_admin				= "1";
	var $module_command				= "PAYGATE_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "PAYGATE_";
	var $module_label				= "MANAGEMENT_PAYMENTGATEWAY";
	var $module_modify		 		= '$Date: 2005/02/21 16:36:14 $';
	var $module_version 			= '$Revision: 1.17 $';
	var $module_creation 			= "26/02/2004";
	var	$endpoint					= "https://www.secpay.com/java-bin/soap";
	var	$wsdl						= "/temp/SECCardService.xml";//"https://www.secpay.com/java-bin/services/SECCardService?wsdl";//"https://www.secpay.com/java-bin/services/SECCardService?wsdl";  

	/*************************************************************************************************************************
    * Lists to be used by payment systems 
    *************************************************************************************************************************/
	var $testModes = Array(
		Array("false",  "LOCALE_TEST_MODE_FAIL_ALL"),
		Array("true", 	"LOCALE_TEST_MODE_PASS_ALL")
	);
	/**
    * field, label and type 
	*
	* this array is used in the account setup functions to define the fields that are to be sent with the order 
	* these fields are defined once for the client, and used in all orders
    */
	var $setupProperties			= Array(
		Array("merchant",		"Your Unique secpay Installation ID Number","text"),
		Array("test_status",	"Enable Test Mode","__TEST__"),
		Array("callback",		"Call Back function complete URI","__CALLBACK__"),
		Array("endpoint",		"End Point Uri","__ENDPOINT__"),
		Array("wsdl",			"WSDL Uri","__WSDL__"),
		Array("remotepassword",	"What is your Remote password","text"),
		Array("soap",	"Use Soap communication model (requires an SSL Certificate [https])","boolean")
	);
	/**
    * field and type 
	*
	* this array is used in the account setup functions to define the fields that are to be sent with the order 
	* these fields are defined per order
    */
	var $generatedProperties		= Array(
		Array("trans_id",	"__SYS_REFERENCE__"),
		Array("currency",	"Select the currency your prices are in","__CURRENCY__"),
		Array("desc",		"__USR_REFERENCE__"),
		Array("amount",		"__AMOUNT__")
	); // to be defined per instance
	var $charge_vat				= "Yes";
	var	$always_charge			= "No";
	var $vat_rate 				= "0";		
	
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
		$this->load_locale("payment_gateway_secpay");
		parent::initialise();
		return 1;
	}
	/*************************************************************************************************************************
	*                         P A Y M E N T   M A N A G E R   F U N C T I O N S   F O R   W O R L D P A Y
	*************************************************************************************************************************/
	
	/*************************************************************************************************************************
	* request payment of an order
	*
	* Supplied the basket identifier 
	*
	* @@param Integer the id of the basket
	* @@param Double the amount total of the basket
	* @@param String The description of the order
	* @@return String XML representationof the form
	*************************************************************************************************************************/
	function payment_request($parameters){
		$basket_identifier	= $this->check_parameters($parameters,"__SYS_REFERENCE__",0);
		$description 		= $this->validate($this->check_parameters($parameters,"__USR_REFERENCE__",0));
		$errors		 		= $this->check_parameters($parameters,"error",Array());
		$card_name			= $this->check_parameters($parameters,"card_name");
		$card_type			= $this->check_parameters($parameters,"card_type");
		$card_number		= $this->check_parameters($parameters,"card_number");
		$card_security		= $this->check_parameters($parameters,"card_security");
		$card_expires_y		= $this->check_parameters($parameters,"card_expires_y");
		$card_expires_m		= $this->check_parameters($parameters,"card_expires_m");		
		$card_start_date_y	= $this->check_parameters($parameters,"card_start_date_y");		
		$card_start_date_m	= $this->check_parameters($parameters,"card_start_date_m");		
		$card_issue			= $this->check_parameters($parameters,"card_issue");		
		/**
        * if the amount is zero or less then exit
        */
		if($basket_identifier==-1){
			return "";
		}
		$s="s";
		if($this->parent->domain=="dev"){
			$s="";
		}
		/**
        * get Account setup details
        */
		$pao_identifier = -1;
		$pad_identifier = -1;
		$mid			= "";
		$pao_pad 		= -1;
		$details="";
		$basket_total	= 0;
		$call_back		= "";
		$basket = $this->call_command("SHOP_GET_ORDER", Array("basket_identifier" => $basket_identifier, "return_type" => Array())); 
		$basket_total = $basket["basket_total"];
		if($basket["bstatus"]<4){
			$propertyValues = Array();
			$sql = "select * from payment_account_details 
						left outer join payment_account_orders on  pao_client = pad_client and pao_basket = $basket_identifier
					where  pad_client = $this->client_identifier";
	        $result  = $this->parent->db_pointer->database_query($sql);
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$pao_identifier = $this->check_parameters($r,"pao_identifier",-1);
				$pad_identifier	= $r["pad_identifier"];
				$pad_uri		= $r["pad_uri"];
	        }
	        $this->parent->db_pointer->database_free_result($result);
			$sql = "select * from payment_account_properties where pap_client = $this->client_identifier and pap_identifier = $pad_identifier";
	        $result  = $this->parent->db_pointer->database_query($sql);
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
	        	$propertyValues[count($propertyValues)] = Array("key"=>$r["pap_property"], "value"=>$r["pap_value"]);
	        }
	        $this->parent->db_pointer->database_free_result($result);
			/**
	        * add new entries if required
        	*/
//			print "[$pao_identifier]";
			/*
			
			if ($pao_identifier==-1){
				$user_id  = $this->check_parameters($_SESSION,"SESION_USER_IDENTIFIER",-1);
				$pao_identifier = $this->getUid();
				$sql = "insert into payment_account_orders (pao_identifier, pao_user, pao_client, pao_basket, pao_status, pao_pad) 
							values 
						($pao_identifier, $user_id, $this->client_identifier, $basket_identifier, 0, $pad_identifier)";
				$this->parent->db_pointer->database_query($sql);
				
				//	Array("cartId",	"__SYS_REFERENCE__"),
				//	Array("desc",	"__USR_REFERENCE__")
				
				for($i=0;$i<count($this->generatedProperties);$i++){
					$value = $this->check_parameters($parameters,"__SYS_REFERENCE__","__NOT_FOUND__");
					if ($value != "__NOT_FOUND__"){
						$sql= "insert into payment_order_properties (pop_identifier, pop_client, pop_property, pop_value)
									values
								($pao_identifier, $this->client_identifier, '".$this->generatedProperties[$i][0]."', '$value')";
						$this->parent->db_pointer->database_query($sql);
						$this->generatedProperties[$i][2] = $value;
					}
				}
			}
			*/
			/**
	        * display this form
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
				$m=count($fields);
				$soap_boolean ="No";
				$pv = count($propertyValues);
				$options=Array();
				for($i=0;$i<count($propertyValues);$i++){
					if($propertyValues[$i]["key"] == "remotepassword"){
//						print "<"."!-- [$basket_identifier][$basket_total][".$propertyValues[$i]["value"]."] -->";
						$options["digest"] = md5($basket_identifier."".$basket_total."".$propertyValues[$i]["value"]);
					}
					if($propertyValues[$i]["key"] == "test_status"){
						if ($propertyValues[$i]["value"]!=""){
							$options["test_status"] = $propertyValues[$i]["value"];
						}
					}
					if($propertyValues[$i]["key"]=="soap"){
						$soap_boolean = $propertyValues[$i]["value"];
					}
					if($propertyValues[$i]["key"]=="merchant"){
						$merchant = $propertyValues[$i]["value"];
					}
					if($propertyValues[$i]["key"]=="callback"){
						if($propertyValues[$i]["value"]==""){
							$call_back = "http://".$this->parent->domain."".$this->parent->base."_process.php";
						} else {
							$call_back = $propertyValues[$i]["value"];
						}
					}
					
				}
			$out ="<module name=\"".$this->module_name."\" display=\"form\">";
			
			if ($soap_boolean!="Yes"){
				$out .="<form name=\"".$this->module_name."_form\" label=\"Confirm Details\" action=\"https://www.secpay.com/java-bin/ValCard\">";
				$out .= "<text><![CDATA[Choose to pay your account via our Secpay secure payment form]]></text>";
				$out .="
						<input type='hidden' name='merchant' ><![CDATA[$merchant]]></input>
						<input type='hidden' name='trans_id' ><![CDATA[$basket_identifier]]></input>
						<input type='hidden' name='amount' ><![CDATA[$basket_total]]></input>
						<input type='hidden' name='callback' ><![CDATA[$call_back]]></input>
				";
				foreach($options as $key => $value){
					$out .= "<input type='hidden' name='$key' ><![CDATA[$value]]></input>";
				}
			} else {
				/**
				* if using soap then request the credit card details
				*			<option value='American Express'>American Express</option>
				*			<option value='Diners'>Diners</option>
				*			<option value='JCB'>JCB</option>
				*/
				
				$out .="<form name=\"".$this->module_name."_form\" label=\"Credit Card Details\" action=\"http$s://".$this->parent->domain."".$this->parent->base."_process.php\">";
//				$out .= "$details";
				$out .= "
					<input type=\"hidden\" name=\"basket_identifier\" value=\"$basket_identifier\"/> 
					<text><![CDATA[".LOCALE_SUPPLY_PAYMENT_DETAILS."]]></text>";
				if(count($errors)>0){
					$out .="<text class='error'><![CDATA[<strong>There was an error in the information that you supplied some of the details have not been returned to this screen for security reasons</strong>]]></text>";
				}
				if ($this->check_parameters($errors,"card_name",0)==1){
					$out .= "<text class='error'><![CDATA[You did not specify a name for this card]]></text>";
				}
				$out .= "<input type='text' required='YES' name='cname' label='".LOCALE_NAME_OF_CARD_HOLDER."'><![CDATA[$card_name]]></input>";
				$card_type_array = Array("Visa", "Master Card", "Switch", "Solo", "Delta");
				$out .= "
					<select name='ctype' label='".LOCALE_CARD_TYPE."'>".$this->gen_options($card_type_array, $card_type_array, $card_type)."</select>";
				if ($this->check_parameters($errors,"card_number",0)==1){
					$out .= "<text class='error'><![CDATA[There was a problem with the credit card number supplied]]></text>";
				}
				$out .= "
					<input type='text' required='YES' name='cnum' label='".LOCALE_CARD_NUMBER."'><![CDATA[$card_number]]></input>";
				if ($this->check_parameters($errors,"card_security",0)==1){
					$out .= "<text class='error'><![CDATA[You must specify the last three digits on the back of your credit card]]></text>";
				}
				$out .= "
					<input type='text' required='YES' name='csec' size='3' length='3' label='".LOCALE_CARD_SECURITY."'><![CDATA[$card_security]]></input>";
				if ($this->check_parameters($errors,"card_expires",0)==1){
					$out .= "<text class='error'><![CDATA[There was an error with the expiry date you specified]]></text>";
				}
				$out .= "
					<input type='ccdate' required='YES' name='cexpires' year_start='".Date("Y")."' year_end='".(Date("Y")+5)."' label='".LOCALE_CARD_EXPIRES."' value='". $card_expires_y ."-". $card_expires_m ."-' />";
				if ($this->check_parameters($errors,"card_start_date",0)==1){
					$out .= "<text class='error'><![CDATA[There was an error with the start date you specified]]></text>";
				}
				$out .= "
					<input type='ccdate' name='cstart'  year_start='".(Date("Y")-5)."' year_end='".(Date("Y"))."' label='".LOCALE_CARD_START."' value='". $card_start_date_y ."-". $card_start_date_m ."-' />
					<input type='text' name='cissue' label='".LOCALE_CARD_ISSUE."'><![CDATA[$card_issue]]></input>
					<input type=\"hidden\" name=\"command\" value=\"PAYGATE_PROCESS\"/> ";
				$settings 				= $this->call_command("SHOP_GET_SETTINGS");
				if ($this->check_parameters($settings, "ss_can_request_invoice", 0)==1){
					$out .="<checkboxes type='vertical' name=\"requestinvoice\" label='Would you like to recieve a paper invoice for this purchase'><option value=\"1\">Yes</option></checkboxes>";
				}
			}
			$out .="<input type=\"submit\" iconify=\"CONFIRM\" value=\"".LOCALE_CONFIRM."\"/></form>";
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
	* @@param Array Array of elements that are to be processed
	*************************************************************************************************************************/
	function payment_process($parameters){
		/**
        * get Account setup details
        */
		$basket_identifier  = $this->check_parameters($parameters,"basket_identifier",-1);
 		$pao_identifier = -1;
		$pad_identifier = -1;
		$pao_pad 		= -1;
		$basket_total	= 0;
		$shop_basket_payment = 0;
		$items			= Array();
		$ip = $this->check_parameters($_SERVER,"REMOTE_ADDR");
		$mid			 = "secpay";
		$vpn_pwd		 = "secpay";
		$trans_id		 = $basket_identifier;
		$requestinvoice	 = $this->check_parameters($parameters,"requestinvoice",0);
		$card_name		 = $this->check_locale_starter($this->check_parameters($parameters,"cname"));
		$card_type		 = $this->check_parameters($parameters,"ctype");
		$card_number	 = $this->check_locale_starter($this->check_parameters($parameters,"cnum"));
		$card_security	 = $this->check_locale_starter($this->check_parameters($parameters,"csec"));
		$card_expires_m	 = $this->check_parameters($parameters,"cexpires_m");
		$card_expires_y	 = substr($this->check_parameters($parameters,"cexpires_y"),-2);
		$card_expires 	 = $card_expires_m . $card_expires_y;
		$card_start_date_m = $this->check_parameters($parameters,"cstart_m");
		$card_start_date_y = substr($this->check_parameters($parameters,"cstart_y"),-2);
		$card_start_date = $card_start_date_m.$card_start_date_y;
		$card_issue		 = $this->check_parameters($parameters,"cissue");
		$check			 = 1; // every thing ok
		$error			 = Array();
		if($card_name == ""){
			$error["card_name"]=1;
			$check=0;
		}
		if($card_security == ""){
			$error["card_security"] = 1;
			$check=0;
		}
//		print "<li>$card_number</li>";
		if(!$this->check_luhn($card_number,$card_type) || strlen($card_number)<13){
			$error["card_number"] = 1;
			$check=0;
		}
		
		$expirydate = mktime(0,0,1,intval(substr($card_expires,0,2)),30,intval(substr($card_expires,2)));
		if ($expirydate < time() || $card_expires==""){
			$error["card_expires"] = 1;
			$check=0;
		}
//		print "<li>$card_start_date > ".Date("my")."</li>";
		$startdate = mktime(0,0,1,intval(substr($card_start_date,0,2)),30,intval(substr($card_start_date,2)));
		if($startdate > time() || $card_start_date==""){
			$error["card_start_date"] = 1;
			$check=0;
		} 
		
		if($check==0){// every thing still ok ???
//			print_r($error);
//			$this->exitprogram();
			
			return $this->payment_request(
				Array(
					"basket_identifier" => $basket_identifier,
					"error"				=> $error,
					"card_name"			=> $card_name,
					"card_type"			=> $card_type,
					"card_number"		=> $card_number,
					"card_security"		=> $card_security,
					"card_start_date_m"	=> $card_start_date_m,
					"card_start_date_y"	=> $this->check_parameters($parameters,"cstart_y"),
					"card_expires_m"	=> $card_expires_m,
					"card_expires_y"	=> $this->check_parameters($parameters,"cexpires_y"),
					"card_issue"		=> $card_issue																				
					)
			);
		} else {
			/*************************************************************************************************************************
	        * GET PROPERTY VALUES
	        *************************************************************************************************************************/
	
			$propertyValues = Array();
			$sql = "select * from payment_account_details 
						left outer join payment_account_orders on  pao_client = pad_client and pao_basket = $basket_identifier
					where  pad_client = $this->client_identifier";
		    $result  = $this->parent->db_pointer->database_query($sql);
		    while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$pao_identifier = $this->check_parameters($r,"pao_identifier",-1);
				$pad_identifier	= $r["pad_identifier"];
				$pad_uri		= $r["pad_uri"];
		    }
		    $this->parent->db_pointer->database_free_result($result);
			$sql = "select * from payment_account_properties where pap_client = $this->client_identifier and pap_identifier = $pad_identifier";
		    $result  = $this->parent->db_pointer->database_query($sql);
		    while($r = $this->parent->db_pointer->database_fetch_array($result)){
		    	$propertyValues[count($propertyValues)] = Array("key"=>$r["pap_property"], "value"=>$r["pap_value"]);
		    }
		    $this->parent->db_pointer->database_free_result($result);
			/*************************************************************************************************************************
	        * 
	        *************************************************************************************************************************/
			$options		 = "";

			$sql="select ss_charge_vat,ss_always_charge,ss_vat from shop_settings where ss_client = $this->client_identifier";
	        $result  = $this->parent->db_pointer->database_query($sql);
	        $r = $this->parent->db_pointer->database_fetch_array($result);
			$this->vat_rate			= $r["ss_vat"];
	        if($r["ss_charge_vat"]==1){
				$this->charge_vat	= "Yes";
			} else {
				$this->charge_vat	= "No";
			}
			if($r["ss_always_charge"]==1){
				$this->always_charge	= "Yes";
			} else {
				$this->always_charge	= "No";
			}
	        $this->parent->db_pointer->database_free_result($result);
			
			$sql = "select * from shop_basket_container
						inner join shop_basket_items on shop_basket_identifier = shop_item_basket and shop_basket_client=shop_item_client
					where shop_basket_identifier = $basket_identifier and shop_basket_client = $this->client_identifier";
//			print "<li>".__FILE__."@@".__LINE__."<p>$sql</p></li>";
	        $result  = $this->parent->db_pointer->database_query($sql);
	        while($r = $this->parent->db_pointer->database_fetch_array($result)){
	        	$item_vat = 0;
				$items[count($items)] = Array(
					"label" => $r["shop_item_title"],
					"price" => $r["shop_item_pickup_price"],
					"quantity" => $r["shop_item_quantity"]
				);
				$basket_total = ($r["shop_item_pickup_price"] - $r["shop_item_pickup_discount"]) * $r["shop_item_quantity"];
				if ($this->charge_vat=="Yes"){
					if ($this->always_charge=="Yes"){
						$item_vat += number_format(($basket_total/100) * $this->vat_rate,2) ;
					} else {
						if ($r["shop_item_vat"]==1){
							$item_vat += number_format(($basket_total/100) * $this->vat_rate,2) ;
						}
					}
				}
				$basket_total += $item_vat;				
				$bstatus				= $r["shop_basket_status"];
				$bill					= $r["shop_basket_bill_contact"];
				$del					= $r["shop_basket_delivery_contact"];
				$shop_basket_payment	= $r["shop_basket_payment"];
        		$basket_weight			+= $r["shop_item_weight"];		
				$del					= $r["shop_basket_delivery_contact"];        				
	        }

	        /**
	        * Start Get Delivery charges
	        */
			$country = -1;
	        $sql = "select * from contact_data 
				inner join contact_address on contact_data.contact_address = address_identifier
			where contact_identifier = $del and contact_client = $this->client_identifier";
	      	$result  = $this->parent->db_pointer->database_query($sql);
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
	      			$country = $r["address_country"];
	        }
	        $this->parent->db_pointer->database_free_result($result);
			if($country == -1){
				$shipping_cost = -1;
			}else{
				$shipping_cost = $this->call_command("SHOP_GET_COST", Array("country"=>$country, "weight"=>$basket_weight)); 				
			}
	        
	        /**
	        * End delivery charges
	        */
	        $basket_total += $shipping_cost;
	        
	        
	        //$options		 = Array(urlencode("cv2=$card_security"), urlencode("req_cv2=true"), urlencode("card_type=$card_type"));
			$options		 = Array("cv2=$card_security", "req_cv2=true", "card_type=$card_type");
	        for($i=0;$i<count($propertyValues);$i++){
				if($propertyValues[$i]["key"] == "remotepassword"){
					//$options[count($options)] = "digest=".md5($trans_id."".$basket_total."".$propertyValues[$i]["value"]);
					$vpn_pwd	= $propertyValues[$i]["value"];
				}
				if($propertyValues[$i]["key"] == "test_status"){
					if ($propertyValues[$i]["value"]!=""){
						$options[count($options)] = "test_status=" . $propertyValues[$i]["value"];
					}
				}
				if($propertyValues[$i]["key"]=="soap"){
					$soap_boolean = $propertyValues[$i]["value"];
				}
				if($propertyValues[$i]["key"] == "merchant"){
					$mid		= $propertyValues[$i]["value"];
				}
			}
	        $this->parent->db_pointer->database_free_result($result);
	        
	        
			if ($soap_boolean!="Yes"){
				return $this->payment_process_confirm($parameters);
			} else {
				if($bstatus<4 || $shop_basket_payment==0){
					$required = Array("contact_name", "contact_address", "contact_country", "contact_postcode", "contact_telephone", "contect_fax", "contact_email");
					$bill_details		= $this->call_command("CONTACT_GET_DETAILS", Array("identifier"=>$bill, "required" => $required));
					$delivery_details	= $this->call_command("CONTACT_GET_DETAILS", Array("identifier"=>$del,	"required" => $required));
	
					/**
			        * add new entries if required
		        	*/
					if ($pao_identifier==-1){
						$user_id  = $this->check_parameters($_SESSION,"SESION_USER_IDENTIFIER",-1);
						$pao_identifier = $this->getUid();
						$sql = "insert into payment_account_orders (pao_identifier, pao_user, pao_client, pao_basket, pao_status, pao_pad) 
									values 
								($pao_identifier, $user_id, $this->client_identifier, $basket_identifier, 0, $pad_identifier)";
						$this->parent->db_pointer->database_query($sql);
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
								$this->parent->db_pointer->database_query($sql);
								$this->generatedProperties[$i][2] = $value;
							}
						}
					}
					/**
			        * display this form
			        */
					$m = count($items);
					
					if($m>0){
						$order = "<order class='com.secpay.seccard.Order'>";
						$order .="<orderLines class='com.secpay.seccard.OrderLine'>";
						for($i=0;$i<$m;$i++){
							$secpayorderlist .= "prod=". $items[$i]["label"] .",item_amount=". $items[$i]["price"] ."x". $items[$i]["quantity"] .";";
							$order .="<OrderLine>";
							$order .="<prod_code>".$items[$i]["label"]."</prod_code>";
							$order .="<item_amount>".$items[$i]["price"]."</item_amount>";
							$order .="<quantity>".$items[$i]["quantity"]."</quantity>";
							$order .="</OrderLine>";
						}
						$order .="</orderLines>";
						$order .="</order>";
						$fields = Array(
							Array("name",		"contact_name"			),
							Array("company",	"contact_company"		),
							Array("address",	"contact_address_array"	),
							Array("city",		"contact_city"			),
							Array("state",		"contact_county"		),
							Array("country",	"contact_country_label"	),
							Array("postcode",	"contact_postcode"		),
							Array("tel",		"contact_telephone"		),
							Array("email",		"email"					)
						);
	/*
	<name>Fred Bloggs</name>
	<company>Acme Co</company>
	<addr_1>1 The Road</addr_1>
	<addr_2>The Cresent</addr_2>
	<city>Metropolis</city>
	<state>Sunny State</state>
	<country>Atlantis</country>
	<post_code>AA23 1BB</post_code>
	<tel>01732 300200</tel>
	<email>sales\@@secpay.com</email>
	<url>http%3A//www.secpay.com/</url>
	*/					
						$m=count($fields);
	//					print_r($bill_details);
						$bill = "<billing class='com.secpay.seccard.Address'>";
						foreach ($bill_details["array"] as $key => $value){
							for($i=0;$i<$m;$i++){
								if($key == $fields[$i][1]){
									if($value!=""){
										if(is_array($value)){
											if( "address" == $fields[$i][0] ){
												if($this->check_parameters($value,0,"")!="")
													$bill.="<addr_1>".$value[0]."</addr_1>";
												if($this->check_parameters($value,1,"")!="")
													$bill.="<addr_2>".$value[1]."</addr_2>";
											}
										} else {
											$bill.="<".$fields[$i][0].">".$value."</".$fields[$i][0].">";
	///										$alt_bill .= $fields[$i][0]=str_replace(Array(),Array(),$value);
										}
									}
								}
							}
						}
						$bill .= "</billing>";
						$ship = "<shipping class='com.secpay.seccard.Address'>";
						foreach ($delivery_details["array"] as $key => $value){
							for($i=0;$i<$m;$i++){
								if($key == $fields[$i][1]){
									if($value!=""){
										if(is_array($value)){
											if( "address" == $fields[$i][0] ){
												if($this->check_parameters($value,0,"")!="")
													$ship.="<addr_1>".$value[0]."</addr_1>";
												if($this->check_parameters($value,1,"")!="")
													$ship.="<addr_2>".$value[1]."</addr_2>";
											}
										} else {
											$ship.="<".$fields[$i][0].">".$value."</".$fields[$i][0].">";
										}
									}
	//								if($value!=""){
	//									$ship .="	<".$fields[$i][0].">".$value."</".$fields[$i][0].">";
	//								}
								}
							}
						}
						$ship .= "</shipping>";
						//$bill_details["array"];
						//$delivery_details["array"];
					}
					$this->wsdl = "https://www.secpay.com/java-bin/services/SECCardService?wsdl";  
					//$this->wsdl = "http://www.secpay.com/java-bin/services/SECCardService?wsdl";
					if($this->parent->domain == $this->parent->DEV_SERVER){
//						$this->wsdl = "http://dev/SECCardService.wsdl";
					}
					$now = $this->libertasGetDate();
					$soap_ptr = new SoapClient($this->wsdl);
					
					/*
					if ($soap_ptr->__isFault()){
						$soapfault = $soap_ptr->__getFault();
						echo $soapfault->faultstring;
						$this->exitprogram();
					}
					*/
	//				join("&", $options)
					$option_list= join(",",$options);

			
				
					//print $mid. " <br/> ".$vpn_pwd. " <br/> ". $trans_id. " <br/> ". $ip. " <br/> ". $card_name. " <br/> ". $card_number. " <br/> ". $basket_total. " <br/> ". $card_expires. " <br/> ". $card_issue. " <br/> ". $card_start_date. " <br/><br/> ". $secpayorderlist . " <br/><br/> ". $ship. " <br/><br/> ". $bill. " <br/><br/> ". $option_list;
					//$this->exitprogram();
					$qstr = $soap_ptr->validateCardFull($mid, $vpn_pwd, $trans_id, $ip, $card_name, $card_number, $basket_total, $card_expires, $card_issue, $card_start_date, urlencode($secpayorderlist), urlencode($ship), urlencode($bill), $option_list);
					if (substr($qstr,0,1)=="?"){
						$qstr = "http://fake_url/fake_path/index.php".$qstr;
					} else {
						$qstr = "http://fake_url/fake_path/index.php?".$qstr;
					}
	
					//print '<br/><br/>Response='.$qstr;
					//$this->exitprogram();
					$soap_result=Array();
					if (($qstr=="http://fake_url/fake_path/index.php") || ($qstr=="http://fake_url/fake_path/index.php?")){
						$transStatus=0;
						$soap_result["message"] = "failed to connect";
					} else {
						
						$data = parse_url($qstr);
						parse_str($data["query"],$soap_result);
						if($soap_result["valid"]=="true"){
							$transStatus=1;
						} else {
							$transStatus=0;
						}
					}
					$sql = "update 
							shop_basket_container 
						set 
							shop_basket_status = 4,
							shop_basket_payment = $transStatus,
							shop_basket_requires_invoice = ".($requestinvoice[0]==1?1:0)."
						where 
							shop_basket_status = 3 and 
							shop_basket_identifier = $basket_identifier and
							shop_basket_session = '".session_id()."' and
							shop_basket_client = $this->client_identifier";
					$this->parent->db_pointer->database_query($sql);
					$sql = "update 
								payment_account_orders 
							set 
								pao_status = ".($transStatus+1).",
								pao_sent = '$now',
								pao_approved = '$now'
							where 
								pao_basket = $basket_identifier and
								pao_client = $this->client_identifier";
					$this->parent->db_pointer->database_query($sql);
					if($transStatus==1){
						$list_of_items = Array();
						$sql = "select * from shop_basket_items where shop_item_basket = $basket_identifier and shop_item_client = $this->client_identifier";
						$result  = $this->parent->db_pointer->database_query($sql);
		                while($r = $this->parent->db_pointer->database_fetch_array($result)){
		                	$list_of_items[count($list_of_items)] = $r["shop_item_identifier"];
		                }
		                $this->parent->db_pointer->database_free_result($result);
						$this->call_command("SHOP_PROCESS_TRIGGER",Array("basket_items" => $list_of_items));
					}
					$_SESSION["SHOP_BASKET_IDENTIFIER"]=-1;
	//				print "<"."!--";
	//				print_r($soap_result);
	//				print "--".">";
					
					$out ="<module name=\"".$this->module_name."\" display=\"form\">";
					$out .="<form name=\"".$this->module_name."_form\" label=\"Confirm Details\" action=\"index.php\">";
					if ($transStatus==0){
						$out .="	<text><![CDATA[The order failed for the following reason <ul><li>".$soap_result["message"]."</li></ul>]]></text>";
					} else {
						$basket = $this->call_command("SHOP_GET_ORDER", Array("basket_identifier"=>$basket_identifier));
						//$out .="	<text><![CDATA[email: ".$basket["del"]["array"]["contact_email"]."Basket:".$basket_identifier."]]</text>";
						$out .="	<text><![CDATA[The order has been validated, a member of staff will be notified soon]]></text>";
						$this->call_command("EMAIL_QUICK_SEND", 
							Array(
								"body"		=> "<style>.row {width:100%;display:block;}\n.cell {width:49%;display:inline;margin:2px;}</style>".str_replace(Array("<text><![CDATA[", "]]></text>", "<TEXT><![CDATA[", "]]&GT;</TEXT>"), Array("", "", "", ""),$basket["output"]),
								"from"		=> $this->check_prefs(Array("sp_from_email")),
							    "to" 		=> $basket["del"]["array"]["contact_email"],
				    			"format"	=> "HTML",
							    "subject"	=> "Order Confirmation ($basket_identifier)"
				 			)
						);
					}
					$out .= "		<input type=\"submit\" iconify=\"CONFIRM\" value=\"OK\"/>";
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
			}
		}
		return $out;
	}
	function payment_process_confirm($parameters){
/*
		print "<li>File:: ".__FILE__."</li>";
		print "<li>Function:: ".__FUNCTION__."</li>";
		print "<li>Line:: ".__LINE__."</li>";
		print "<li>params:: <pre>".print_r($parameters,true)."</pre></li>";
*/
		$basket_identifier	= $this->check_parameters($parameters, "trans_id");
		$valid				= $this->check_parameters($parameters, "valid");
		$auth_code			= $this->check_parameters($parameters, "code");
		if($valid=="true" && $auth_code=="A"){
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
					shop_basket_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);

		$sql = "update 
					payment_account_orders 
				set 
					pao_status = ".($transStatus+1).",
					pao_approved = '$now'
				where 
					pao_basket = $basket_identifier and
					pao_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);

		$sql = "update 
					shop_basket_items 
				set 
					shop_item_shop = $transStatus
				where 
					shop_item_basket = $basket_identifier and
					shop_item_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		if($transStatus==1){
			$this->call_command("SHOP_MANAGE_QUANTITY",Array("basket_identifier"=>$basket_identifier));
		}
		$sql="select * from memo_information where mi_client = $this->client_identifier and mi_field in ( 'credit_confirm', 'credit_deny', 'invoice_confirm', 'invoice_email') and mi_type='SHOP_'";
		$screens = Array();
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$screens[$r["mi_field"]] = $r["mi_memo"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$labels=Array();
		$sql="select * from shop_settings where ss_client = $this->client_identifier";
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$labels["credit_confirm"]	= $r["ss_cc_accept_label"];
			$labels["credit_deny"]		= $r["ss_cc_deny_label"];
			$labels["invoice_confirm"]	= $r["ss_invoice_msg_label"];
			$labels["invoice_email"]	= $r["ss_invoice_email_label"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$_SESSION["SHOP_BASKET_IDENTIFIER"]="__NOT_FOUND__";
		if($transStatus==1){
			$msg 	= $screens["credit_confirm"];
			$label	= $labels["credit_confirm"];
			
			$basket = $this->call_command("SHOP_GET_ORDER",Array("basket_identifier"=>$basket_identifier));
			//$basket = $this->get_order(Array("basket_identifier"=>$basket_identifier));
			$this->call_command("EMAIL_QUICK_SEND", 
				Array(
					"body"		=> "<style>.row {width:100%;display:block;}\n.cell {width:49%;display:inline;margin:2px;}</style>".str_replace(Array("<text><![CDATA[", "]]></text>", "<TEXT><![CDATA[", "]]&GT;</TEXT>"), Array("", "", "", ""),$basket["output"]),
					"from"		=> $this->check_prefs(Array("sp_from_email")),
				    "to" 		=> $basket["del"]["array"]["contact_email"],
			    	"format"	=> "HTML",
				    "subject"	=> "Order Confirmation ($basket_identifier)"
		 		)
			);
		} else {
			$msg	= $screens["credit_deny"];
			$label	= $labels["credit_deny"];
		}
		$out ="<module name=\"".$this->module_name."\" display=\"entry\">";
		$out .="	<text><![CDATA[<strong>".$label."</strong>]]></text>";
		$out .="	<text><![CDATA[$msg]]></text>";
		$out .="</module>";	
		return $out;
	}
	/*************************************************************************************************************************
    * function to check the validity of a credit card number
	* 
	* Luhn
	* 
	* This will only check if the number is formatted correctly and not if it is a "REAL" credit card number
	* Credit Card numbers are (most times) 13 to 16 digit numbers which are protected by a special numerical check, called Luhn check.
	* Each digit is multiplied by the alternating factors 2 and 1 (last digit is always multiplied by 1). 
	* Of each calculation result, the digits of the result are summed together. Then, these sums are totalized. 
	* Finally, to be a valid Credit Card number, the total must be divisible by 10.
	* 
	* Example (Credit Card number: 1234 5678 7654 3210):
	*	1  	 	2  	 	3  	 	4  	 	5  	 	6  	 	7  	 	8  	 	7  	 	6  	 	5  	 	4  	 	3  	 	2  	 	1  	 	0
	*	* 		* 		* 		* 		* 		* 		* 		*	 	* 		* 		*	 	* 		* 		* 		* 		*
	*	2 		1 		2 		1 		2 		1 		2 		1 		2 		1 		2 		1 		2 		1 		2 		1
	*	= 		= 		= 		= 		= 		= 		= 		= 		= 		= 		= 		= 		= 		= 		= 		=
	*	2 		2	 	6 		4 		10 		6 		14 		8 		14	 	6 		10 		4 		6 		2 		2 		0
	*	2 	+ 	2 	+ 	6 	+ 	4 	+ 	(1+0)+	6 	+ 	(1+4)+ 	8 	+ 	(1+4)+ 	6 	+ 	(1+0)+ 	4 	+ 	6 	+ 	2 	+ 	2 	+ 	0
	*	2 	+ 	2 	+ 	6 	+ 	4 	+ 	1 	+ 	6 	+ 	5 	+ 	8 	+ 	5 	+ 	6 	+ 	1 	+ 	4 	+ 	6 	+ 	2 	+ 	2 	+ 	0 	= 	60 	= 	N*10
    ************************************************************************************************************************/
	function check_luhn($number,$type){
		$str_num = str_replace(" ","",$number."");// remove any spaces that a user might put in
		if($type=="Visa"){
			if(substr($str_num,0,1)!="4"){
				return false;
			}
			if ((strlen($str_num)!=13) && (strlen($str_num)!=16)){
				return false;
			}
		}
		if($type=="Master Card"){
			if (
					(substr($str_num,0,2) != "51") && 
					(substr($str_num,0,2) != "52") && 
					(substr($str_num,0,2) != "53") && 
					(substr($str_num,0,2) != "54") && 
					(substr($str_num,0,2) != "55")
				){
				return false;
			}
		}
		if($type=="American Express"){ // we don't do American Express
			if (
					(substr($str_num,0,2) != "34") && 
					(substr($str_num,0,2) != "37")
				){
				return false;
			}
		}
		$cc_length = strlen($str_num); 
		$multipliers = Array(2,1,2,1, 2,1,2,1, 2,1,2,1, 2,1,2,1);
		$number_list = Array();
		$total=0;
		for($i=0;$i<$cc_length;$i++){
			$number_list[$i] = substr($str_num,$i,1) * $multipliers[$i];
			if(strlen($number_list[$i]."")==1){
				$total+=$number_list[$i];
			} else {
				$nl = (substr($number_list[$i],0,1)*1)+(substr($number_list[$i],1,1)*1);
				$total+=$nl;
			}
		}
		// divisible by ten if the last digit is a zero
		if(substr($total."",-1)=="0"){
			return true;
		} else {
			return false;
		}
	}
}

?>
