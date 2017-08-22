<?PHP
/*************************************************************************************************************************
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.payment_gateway.php
* @date 08 Nov 2004
*************************************************************************************************************************/
/*************************************************************************************************************************
* This module is the administration module for the Payment Gateway.
*************************************************************************************************************************/
class paymentgateway extends module{
	/*************************************************************************************************************************
	*  Class Variables (generic
	*************************************************************************************************************************/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_name_label			= "Payment Gateway (Presentation)";
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
	var $testModes 					= Array();
	var $vat_rate 					= 0;
	var $currency					= Array(
		Array("AED", "United Arab Emirates, Dirhams"),
		Array("AFA", "Afghanistan, Afghanis"),
		Array("ALL", "Albania, Leke"),
		Array("AMD", "Armenia, Drams"),
		Array("ANG", "Netherlands Antilles, Guilders (also called Florins)"),
		Array("AOA", "Angola, Kwanza"),
		Array("ARS", "Argentina, Pesos"),
		Array("AUD", "Australia, Dollars"),
		Array("AWG", "Aruba, Guilders (also called Florins)"),
		Array("AZM", "Azerbaijan, Manats"),
		Array("BAM", "Bosnia and Herzegovina, Convertible Marka"),
		Array("BBD", "Barbados, Dollars"),
		Array("BDT", "Bangladesh, Taka"),
		Array("BGN", "Bulgaria, Leva"),
		Array("BHD", "Bahrain, Dinars"),
		Array("BIF", "Burundi, Francs"),
		Array("BMD", "Bermuda, Dollars"),
		Array("BND", "Brunei Darussalam, Dollars"),
		Array("BOB", "Bolivia, Bolivianos"),
		Array("BRL", "Brazil, Brazil Real"),
		Array("BSD", "Bahamas, Dollars"),
		Array("BTN", "Bhutan, Ngultrum"),
		Array("BWP", "Botswana, Pulas"),
		Array("BYR", "Belarus, Rubles"),
		Array("BZD", "Belize, Dollars"),
		Array("CAD", "Canada, Dollars"),
		Array("CDF", "Congo/Kinshasa, Congolese Francs"),
		Array("CHF", "Switzerland, Francs"),
		Array("CLP", "Chile, Pesos"),
		Array("CNY", "China, Yuan Renminbi"),
		Array("COP", "Colombia, Pesos"),
		Array("CRC", "Costa Rica, Colones"),
		Array("CSD", "Serbia, Dinars"),
		Array("CUP", "Cuba, Pesos"),
		Array("CVE", "Cape Verde, Escudos"),
		Array("CYP", "Cyprus, Pounds"),
		Array("CZK", "Czech Republic, Koruny"),
		Array("DJF", "Djibouti, Francs"),
		Array("DKK", "Denmark, Kroner"),
		Array("DOP", "Dominican Republic, Pesos"),
		Array("DZD", "Algeria, Algeria Dinars"),
		Array("EEK", "Estonia, Krooni"),
		Array("EGP", "Egypt, Pounds"),
		Array("ERN", "Eritrea, Nakfa"),
		Array("ETB", "Ethiopia, Birr"),
		Array("EUR", "Euro Member Countries, Euro"),
		Array("FJD", "Fiji, Dollars"),
		Array("FKP", "Falkland Islands (Malvinas), Pounds"),
		Array("GBP", "United Kingdom, Pounds"),
		Array("GEL", "Georgia, Lari"),
		Array("GGP", "Guernsey, Pounds"),
		Array("GHC", "Ghana, Cedis"),
		Array("GIP", "Gibraltar, Pounds"),
		Array("GMD", "Gambia, Dalasi"),
		Array("GNF", "Guinea, Francs"),
		Array("GTQ", "Guatemala, Quetzales"),
		Array("GYD", "Guyana, Dollars"),
		Array("HKD", "Hong Kong, Dollars"),
		Array("HNL", "Honduras, Lempiras"),
		Array("HRK", "Croatia, Kuna"),
		Array("HTG", "Haiti, Gourdes"),
		Array("HUF", "Hungary, Forint"),
		Array("IDR", "Indonesia, Rupiahs"),
		Array("ILS", "Israel, New Shekels"),
		Array("IMP", "Isle of Man, Pounds"),
		Array("INR", "India, Rupees"),
		Array("IQD", "Iraq, Dinars"),
		Array("IRR", "Iran, Rials"),
		Array("ISK", "Iceland, Kronur"),
		Array("JEP", "Jersey, Pounds"),
		Array("JMD", "Jamaica, Dollars"),
		Array("JOD", "Jordan, Dinars"),
		Array("JPY", "Japan, Yen"),
		Array("KES", "Kenya, Shillings"),
		Array("KGS", "Kyrgyzstan, Soms"),
		Array("KHR", "Cambodia, Riels"),
		Array("KMF", "Comoros, Francs"),
		Array("KPW", "Korea (North), Won"),
		Array("KRW", "Korea (South), Won"),
		Array("KWD", "Kuwait, Dinars"),
		Array("KYD", "Cayman Islands, Dollars"),
		Array("KZT", "Kazakstan, Tenge"),
		Array("LAK", "Laos, Kips"),
		Array("LBP", "Lebanon, Pounds"),
		Array("LKR", "Sri Lanka, Rupees"),
		Array("LRD", "Liberia, Dollars"),
		Array("LSL", "Lesotho, Maloti"),
		Array("LTL", "Lithuania, Litai"),
		Array("LVL", "Latvia, Lati"),
		Array("LYD", "Libya, Dinars"),
		Array("MAD", "Morocco, Dirhams"),
		Array("MDL", "Moldova, Lei"),
		Array("MGA", "Madagascar, Ariary"),
		Array("MKD", "Macedonia, Denars"),
		Array("MMK", "Myanmar (Burma), Kyats"),
		Array("MNT", "Mongolia, Tugriks"),
		Array("MOP", "Macau, Patacas"),
		Array("MRO", "Mauritania, Ouguiyas"),
		Array("MTL", "Malta, Liri"),
		Array("MUR", "Mauritius, Rupees"),
		Array("MVR", "Maldives (Maldive Islands), Rufiyaa"),
		Array("MWK", "Malawi, Kwachas"),
		Array("MXN", "Mexico, Pesos"),
		Array("MYR", "Malaysia, Ringgits"),
		Array("MZM", "Mozambique, Meticais"),
		Array("NAD", "Namibia, Dollars"),
		Array("NGN", "Nigeria, Nairas"),
		Array("NIO", "Nicaragua, Gold Cordobas"),
		Array("NOK", "Norway, Krone"),
		Array("NPR", "Nepal, Nepal Rupees"),
		Array("NZD", "New Zealand, Dollars"),
		Array("OMR", "Oman, Rials"),
		Array("PAB", "Panama, Balboa"),
		Array("PEN", "Peru, Nuevos Soles"),
		Array("PGK", "Papua New Guinea, Kina"),
		Array("PHP", "Philippines, Pesos"),
		Array("PKR", "Pakistan, Rupees"),
		Array("PLN", "Poland, Zlotych"),
		Array("PYG", "Paraguay, Guarani"),
		Array("QAR", "Qatar, Rials"),
		Array("ROL", "Romania, Lei"),
		Array("RUR", "Russia, Rubles"),
		Array("RWF", "Rwanda, Rwanda Francs"),
		Array("SAR", "Saudi Arabia, Riyals"),
		Array("SBD", "Solomon Islands, Dollars"),
		Array("SCR", "Seychelles, Rupees"),
		Array("SDD", "Sudan, Dinars"),
		Array("SEK", "Sweden, Kronor"),
		Array("SGD", "Singapore, Dollars"),
		Array("SHP", "Saint Helena, Pounds"),
		Array("SIT", "Slovenia, Tolars"),
		Array("SKK", "Slovakia, Koruny"),
		Array("SLL", "Sierra Leone, Leones"),
		Array("SOS", "Somalia, Shillings"),
		Array("SPL", "Seborga, Luigini"),
		Array("SRD", "Suriname, Dollars"),
		Array("STD", "São Tome and Principe, Dobras"),
		Array("SVC", "El Salvador, Colones"),
		Array("SYP", "Syria, Pounds"),
		Array("SZL", "Swaziland, Emalangeni"),
		Array("THB", "Thailand, Baht"),
		Array("TJS", "Tajikistan, Somoni"),
		Array("TMM", "Turkmenistan, Manats"),
		Array("TND", "Tunisia, Dinars"),
		Array("TOP", "Tonga, Pa'anga"),
		Array("TRL", "Turkey, Liras"),
		Array("TTD", "Trinidad and Tobago, Dollars"),
		Array("TVD", "Tuvalu, Tuvalu Dollars"),
		Array("TWD", "Taiwan, New Dollars"),
		Array("TZS", "Tanzania, Shillings"),
		Array("UAH", "Ukraine, Hryvnia"),
		Array("UGX", "Uganda, Shillings"),
		Array("USD", "United States of America, Dollars"),
		Array("UYU", "Uruguay, Pesos"),
		Array("UZS", "Uzbekistan, Sums"),
		Array("VEB", "Venezuela, Bolivares"),
		Array("VND", "Viet Nam, Dong"),
		Array("VUV", "Vanuatu, Vatu"),
		Array("WST", "Samoa, Tala"),
		Array("XAF", "Communauté Financière Africaine BEAC, Francs"),
		Array("XAG", "Silver, Ounces"),
		Array("XAU", "Gold, Ounces"),
		Array("XCD", "East Caribbean Dollars"),
		Array("XDR", "International Monetary Fund (IMF) Special Drawing Rights"),
		Array("XOF", "Communauté Financière Africaine BCEAO, Francs"),
		Array("XPD", "Palladium Ounces"),
		Array("XPF", "Comptoirs Français du Pacifique Francs"),
		Array("XPT", "Platinum, Ounces"),
		Array("YER", "Yemen, Rials"),
		Array("ZAR", "South Africa, Rand"),
		Array("ZMK", "Zambia, Kwacha"),
		Array("ZWD", "Zimbabwe, Zimbabwe Dollars")
	);
	var $setupProperties			= Array();
	var $generatedProperties		= Array();
	/*************************************************************************************************************************
	* Management Menu entries
	*************************************************************************************************************************/
	var $module_admin_options 		= array(
	);
	
	/*************************************************************************************************************************
	*  Group access Restrictions, restrict a group to these command sets
	*************************************************************************************************************************/
	var $module_admin_user_access = array(
		array("PAYGATEADMIN_ALL",			"COMPLETE_ACCESS"),
		array("PAYGATEADMIN_ACCOUNT_ADMIN",	"ACCESS_LEVEL_SETUP"),  		// this will allow the user to add a new database
		array("PAYGATEADMIN_ORDER_MANAGER",	"ACCESS_LEVEL_ORDER_MANAGER")  // this will allow the user to manage orders
	);

	/*************************************************************************************************************************
	*  Channel options
	*************************************************************************************************************************/
	var $module_display_options 	= array();
	
	/*************************************************************************************************************************
	* SPECIAL PAGES
	*
	* Each special page will call a specific function as defined here
	*************************************************************************************************************************/
	var $specialPages			 	= array(
		array("_process.php"			,"PAYGATE_PROCESS"			,"VISIBLE", "Processing ...."),
		array("_process-confirm.php"	,"PAYGATE_PROCESS_CONFIRM"	,"VISIBLE", "Processing ....")
	);
	/*************************************************************************************************************************
	*  filter options
	*************************************************************************************************************************/
	var $display_options			= array();
	
	/*************************************************************************************************************************
	*  Class Methods
	*************************************************************************************************************************/
	
	function command($user_command, $parameter_list = array()){
		/*************************************************************************************************************************
		* If debug is turned on then output the command sent and the parameter list too.
		*************************************************************************************************************************/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,print_r($parameter_list,true),__LINE__,"command"));
		}
		
		/*************************************************************************************************************************
		* This is the main function of the Module this function will call what ever function
		* you want to call.
		*************************************************************************************************************************/
		if (strpos($user_command, $this->module_command)===0){
			/*************************************************************************************************************************
			* Generic module functions
			*************************************************************************************************************************/
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
			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
				return $this->webContainer;
			}
			/*************************************************************************************************************************
			* Presentation Module commands
			*************************************************************************************************************************/
			if ($user_command==$this->module_command."REQUEST_PAYMENT"){
				return $this->payment_request($parameter_list);
			}

//			if($this->parent->module_type=="EXECUTE"){
				if ($user_command==$this->module_command."PROCESS"){
					return $this->payment_process($parameter_list);
				}
				if ($user_command==$this->module_command."PROCESS_CONFIRM"){
					return $this->payment_process_confirm($parameter_list);
				}
				if ($user_command==$this->module_command."PROCESS_DETAILS"){
					return $this->payment_process_details($parameter_list);
				}
//			}
			if ($user_command==$this->module_command."SPECIAL_PAGES"){
				return $this->specialPages;
			}
		}
		return "";
	}
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
		* request the client identifier once we use this variable often
		*************************************************************************************************************************/
		$this->client_identifier = $this->parent->client_identifier;
		/*************************************************************************************************************************
		* get the vat rate (default UK)
		*************************************************************************************************************************/
		$this->vat_rate = $this->check_prefs(Array("sp_vat_rate","default"=>"17.5","module"=>"SHOP_", "options"=>"TEXT"));
		/*************************************************************************************************************************
		* load the required locale
		*************************************************************************************************************************/
		$this->load_locale("payment_gateway");
		/*************************************************************************************************************************
		* define the list of Editors in this module and define them as empty
		*************************************************************************************************************************/
		$this->editor_configurations = Array();
		/*************************************************************************************************************************
		* request the page size 
		*************************************************************************************************************************/
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		/*************************************************************************************************************************
		* define the admin access that this user has.
		*************************************************************************************************************************/
		return 1;
	}
	/*************************************************************************************************************************
	*                               P A Y M E N T   M A N A G E R   F U N C T I O N S
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
					inner join shop_basket_items on shop_item_basket = shop_basket_identifier and shop_basket_client=shop_item_client
					inner join shop_stock on shop_stock_client = shop_item_client and shop_stock_identifier = shop_item_stock_id
		where shop_basket_identifier = $basket_identifier and shop_basket_client = $this->client_identifier";
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
						($pao_identifier, $user_id, $this->client_identifier, $basket_identifier, 0, $pao_pad)";
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
			$out ="<module name=\"".$this->module_name."\" display=\"form\">";
			$out .="<form name=\"".$this->module_name."_form\" action='$pad_uri' label=\"Confirm Details\">";
			$max_setup_values	= count($this->setupProperties);
			$max_prop_values	= count($propertyValues);
			for($i=0; $i<$max_setup_values; $i++){
				for($v=0; $v<$max_prop_values; $v++){
					if ($propertyValues[$v]["key"]==$this->setupProperties[$i][0]){
						$out .="	<input type=\"hidden\" name=\"".$this->setupProperties[$i][0]."\"><![CDATA[".$propertyValues[$v]["value"]."]]></input>";
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
				$table = "<table style='width:100%' cellspacing='0' cellpadding='3' summary='contents of basket'>";
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
					$table .="<td align='right'>".number_format($items[$i]["price"],2)."</td>";
					$table .="<td align='right'>".number_format($items[$i]["discount"],2)."</td>";
					$table .="<td align='right'>".$items[$i]["quantity"]."</td>";
					$table .="<td align='right'>".number_format($items[$i]["weight"] * $items[$i]["quantity"],2)."</td>";
					$totalw += ($items[$i]["weight"] * $items[$i]["quantity"]);
					$table .="<td align='right'>".number_format(($items[$i]["price"]-$items[$i]["discount"]) * $items[$i]["quantity"],2)."</td>";
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
									<td align='right' colspan='4'><strong>Shipping Weigth/Cost</strong></td><td align='right'>$totalw</td><td align='right'>".number_format(round($shipping_cost,2),2)."</td>
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
				$table .="<td colspan='4' class='alignright'><strong>Total</strong></td><td></td><td align='right'>".number_format(round($basket_total,2),2)."</td>";				
				$table .="</tr>";
				$table.="</table>";
				$out .= "<text><![CDATA[<p><strong>You ordered the following items</strong></p>$table]]></text>";
				$out .= "<text><![CDATA[<p><strong>Billing details</strong></p>".$bill_details["text"]."]]></text>";
				$out .= "<text><![CDATA[<p><strong>Delivery details</strong></p>".$delivery_details["text"]."]]></text>";
				
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
			$out .="			<input type=\"hidden\" name=\"MC_session\" ><![CDATA[".session_id()."]]></input> 
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
		$basket_identifier	= $this->check_parameters($parameters, "cartid");
		$basket_session		= $this->check_parameters($parameters, "MC_session");
		$transStatus		= $this->check_parameters($parameters, "transStatus");
		/*************************************************************************************************************************
        * store in session
        *************************************************************************************************************************/
		$_SESSION["pay_basket_identifier"] = $basket_identifier;
		$_SESSION["pay_basket_session"] = $basket_session;
		$_SESSION["pay_transStatus"] = $transStatus;
		/*************************************************************************************************************************
        * get msgs
        *************************************************************************************************************************/
		$ed = $this->call_command("SHOP_GET_EDITORS");
		/*************************************************************************************************************************
        * choose message to display 
        *************************************************************************************************************************/
		if($transStatus=="Y"){
			$transStatus=1;
			$msg = $ed["credit_confirm"];
			$pay = "approved";
		} else {
			$transStatus=0;
			$msg = $ed["credit_deny"];
			$pay = "deny";
		}
		$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_SHOP_ORDER_PROCESSOR__"], "identifier" => $basket_identifier, "pay"=> "$pay"));
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
		
		$_SESSION["SHOP_BASKET_IDENTIFIER"] = -1;
		$out ="<module name=\"".$this->module_name."\" display=\"TEXT\">";
		$out .="	<text><![CDATA[$msg]]></text>";
		$out .="</module>";	
		$this->parent->show_base_href = 1;
		return $out;
	}
	/*************************************************************************************************************************
	* callback process for an order confirm screen
	*
	* this function displays the sppropraite scren response
	*************************************************************************************************************************/
	function payment_process_confirm($parameters){
		$basket_identifier	= $this->check_parameters($_SESSION,"pay_basket_identifier");
		$basket_session		= $this->check_parameters($_SESSION,"pay_basket_session");
		$transStatus		= $this->check_parameters($_SESSION,"pay_transStatus");
		/*************************************************************************************************************************
        * choose message to display 
        *************************************************************************************************************************/
		if($transStatus=="Y"){
			$transStatus=1;
			$msg = $ed["credit_confirm"];
			$pay = "approved";
		} else {
			$transStatus=0;
			$msg = $ed["credit_deny"];
			$pay = "deny";
		}
	}
	
}

?>