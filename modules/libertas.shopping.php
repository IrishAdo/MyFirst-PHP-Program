<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.shopping.php
* @date 09 Oct 2002
*/
/**
* this module should allow the engine to authenticate vehicle when they
* log into the system
*/

class shopping extends module{
	/**
	*  Class Variables
	*/
	var $module_name			= "shopping";
	var $module_grouping		= "LOCALE_MANAGEMENT_GROUP_CONTENT";
	var $module_label			= "MANAGEMENT_SHOP";
	var $module_admin			= "1";
	var $module_debug			= false;
	var $module_creation		= "13/09/2002";
	var $module_modify	 		= '$Date: 2005/02/22 19:56:55 $';
	var $module_version 		= '$Revision: 1.36 $';
	var $module_command			= "SHOP_"; 		// all commands specifically for this module will start with this token
	var $display_options		= null;
	var $searched				= 0;
	
	var $module_display_options = array(
//		Array("SHOP_DISPLAY_STOCK","Display the main shop stock list"),
//		Array("SHOP_HOTSPOTS","Display the latest Hot items in the shop."),
//		Array("SHOP_SEARCH","Display the Shop search engine.")
	);
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	var $module_admin_options	= array();
	var $shop_setup				= 0;
	var $shop_order_approver	= 0;
	var $can_request_invoice	= 0;
	var $shop_setting_id		= -1;
	var $ss_autoreduce			= 0;
	/*************************************************************************************************************************
    * Currencys shop works in
    *************************************************************************************************************************/
		var $currency					= Array(/*
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
		Array("BAM", "Bosnia and Herzegovina, Convertible M7arka"),
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
		Array("ETB", "Ethiopia, Birr"),*/
		Array("EUR", "Euro Member Countries, Euro"),/*
		Array("FJD", "Fiji, Dollars"),
		Array("FKP", "Falkland Islands (Malvinas), Pounds"),*/
		Array("GBP", "United Kingdom, Pounds"),/*
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
		Array("UGX", "Uganda, Shillings"),*/
		Array("USD", "United States of America, Dollars")/*,
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
		Array("ZWD", "Zimbabwe, Zimbabwe Dollars")*/
	);

	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	var	$status_list = Array(
			"Credit Card - Payment denied",  					// 0
			"Credit Card - Payment approved",					// 1
			"Cheque - Payment requested",						// 2
			"Cheque - Payment received",						// 3
			"Credit Card - Payment awaiting refund",			// 4
			"Credit Card - Payment refunded",					// 5
			"Cheque - Payment awaiting refund",					// 6
			"Cheque - Payment refunded",						// 7
			"Cheque - No Payment Required"						// 8
		);

	/*************************************************************************************************************************
    * roles in the module 
    *************************************************************************************************************************/
	var $module_admin_user_access	= array(
		array("SHOP_ALL", 				"COMPLETE_ACCESS",""),
		array("SHOP_ORDER_PROCESSOR", 	"Can process orders",""),
		array("SHOP_SETUP", 			"Can set shop parameters","")
	);
	
	var $vat_rate = "0";
	var $bill_and_del_same 		= "Yes"; // force delivery to bill payer more secure

	var $can_pay_by_invoice		= "No";
	var $charge_vat				= "Yes";
	var	$always_charge			= "No";


	var $special_webobjects		= Array(
		"ADD" => Array(
			"owner_module" 	=> "",
			"label" 		=> "Add to cart",
			"wo_command"	=> "SHOP_BASKET_BUY_NOW",
			"file"			=> "_add-to-cart.php",
			"available"		=> 1
		),
		"VIEW" => Array(
			"owner_module" 	=> "",
			"label" 		=> "View basket",
			"wo_command"	=> "SHOP_BASKET_VIEW",
			"file"			=> "_view-cart.php",
			"available"		=> 1
		),
		"PURCHASE" => Array(
			"owner_module" 	=> "",
			"label" 		=> "Purchase basket",
			"wo_command"	=> "SHOP_PURCHASE_BASKET",
			"file"			=> "_purchase-cart.php",
			"available"		=> 1
		)
	);
	/*************************************************************************************************************************
    * editor configurations
    *************************************************************************************************************************/
	var $editors = Array(
		"credit_confirm"	=> Array("memo" => "", "ed"=> "ENTRY_CREDITCARD_CONFIRM",	"label" => "LOCALE_CONFIRM_MSG_TAB",		"lval"=>""),
		"credit_deny"		=> Array("memo" => "", "ed"=> "ENTRY_CREDITCARD_DENY",		"label" => "LOCALE_DENY_MSG_TAB",			"lval"=>""),
		"invoice_confirm"	=> Array("memo" => "", "ed"=> "ENTRY_INVOICE_CONFIRM",		"label" => "LOCALE_INVOICE_MSG_TAB", 		"lval"=>""),
		"invoice_email"		=> Array("memo" => "", "ed"=> "ENTRY_INVOICE_EMAIL",		"label" => "LOCALE_INVOICE_EMAIL_MSG_TAB",	"lval"=>"")
	);
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	var $admin_access = 0;
	/**
	*  Class Methods
	*/
	
	function command($user_command,$parameter_list=array()){
		/**
		* If debug is turned on then output the command sent and the parameter list too.
		*/
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_COMMAND_PARAMETERS",array($this->module_name,$user_command,$parameter_list,__LINE__,"command::".print_r($parameter_list,true)));
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
			if ($user_command==$this->module_command."GET_WEB_CONTAINER"){
				return $this->webContainer;
			}
			if ($user_command==$this->module_command."GET_AUTHOR"){
				return $this->get_module_author();
			}
			if ($user_command==$this->module_command."GET_CREATION"){
				return $this->get_module_creation();
			}
			if ($user_command==$this->module_command."GET_SETTINGS"){
				return $this->get_shop_settings();
			}
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			if ($user_command==$this->module_command."CREATE_NEW_CLIENT_DETAILS"){
				return $this->create_client_details($parameter_list);
			}
			if ($user_command==$this->module_command."CREATE_SPECIALS"){
				return $this->make_special();
			}
			if ($user_command==$this->module_command."FORM_RESTRICTIONS"){
				return $this->form_restrictions($parameter_list);
			}
			if ($user_command==$this->module_command."PROCESS_TRIGGER"){
				return $this->process_trigger($parameter_list);
			}
			if($this->admin_access==1){
				/**
                * display the digital desktop
                */
				if ($user_command == $this->module_command."MY_WORKSPACE"){
					return $this->retrieve_my_docs($parameter_list);
				}
				if ($user_command == $this->module_command."MESSAGES"){
					return $this->shop_messages($parameter_list);
				}
				if ($user_command == $this->module_command."MESSAGE_SAVE"){
					return $this->shop_save_messages($parameter_list);
				}
				/**
				* Order management functions
				*/
				if($this->shop_order_approver==1){
					if (($user_command==$this->module_command."LIST_ORDERS") || ($user_command==$this->module_command."ORDER_VIEW_TYPE")) {
						return $this->display_list_orders($parameter_list);
					}
					if ($user_command==$this->module_command."PROCESS_ORDER"){
						return $this->display_process_order($parameter_list);
					}
					if ($user_command==$this->module_command."MARK_ORDER_PROCESSED_AS"){
						$this->display_order_processed_as($parameter_list);
					}
					if ($user_command==$this->module_command."DISPLAY_DELETE_ORDER"){
						return $this->display_delete_order($parameter_list);
					}										
					if ($user_command==$this->module_command."DELETE_ORDER"){
						$this->delete_order($parameter_list);
					}					
				}
				/*************************************************************************************************************************
                * 
                *************************************************************************************************************************/
				if($this->shop_setup==1){
					/**
					* manage the sales taxsettings
					*/
					if ($user_command == $this->module_command."SALES_TAX"){
						return $this->shop_sales_tax($parameter_list);
					}
					if ($user_command == $this->module_command."SALES_TAX_SAVE"){
						return $this->shop_sales_tax_save($parameter_list);
					}
					/**
        	        * remove lost /discarded baskets
    	            */
					if ($user_command==$this->module_command."BASKET_CLEAN_UP"){
						$this->shop_basket_clean_up($parameter_list);
						$this->call_command("ENGINE_REFRESH_BUFFER",Array(""));
					}
					/**
					* weight management functions
					*/
					if ($user_command==$this->module_command."WEIGHT_SETUP") {
						return $this->display_weight_setup($parameter_list);
					}
					if ($user_command==$this->module_command."WEIGHT_SAVE") {
						return $this->save_weight_setup($parameter_list);
					}
					/*************************************************************************************************************************
        	        *  chose payment gateway and settings
    	            *************************************************************************************************************************/
					if ($user_command==$this->module_command."CHOOSEPAY"){
						return $this->choose_pay($parameter_list);
					}
					if ($user_command==$this->module_command."CHOOSEPAY_SAVE"){
						return $this->choose_pay_save($parameter_list);
					}
				}
			}
			/*************************************************************************************************************************
            * basket on site 
            *************************************************************************************************************************/
			if ($user_command == $this->module_command."GET_ORDER"){
				return $this->get_order($parameter_list);
			}
			if ($user_command == $this->module_command."MANAGE_QUANTITY"){
				return $this->manage_quantity($parameter_list);
			}
			
			if ($user_command == $this->module_command."INVOICE_PROCESS"){
				return $this->invoice_process($parameter_list);
			}
			if ($user_command == $this->module_command."CREATE_BASKET"){
				$this->create_basket($parameter_list);
			}
			if ($user_command==$this->module_command."ADD_TO_BASKET"){
				return $this->shop_basket_add_module($parameter_list);
			}
			if ($user_command==$this->module_command."BASKET_BUY_NOW"){
				$this->shop_basket_add_to_basket($parameter_list);
				$this->call_command("ENGINE_REFRESH_BUFFER",Array("url" => "_view-cart.php"));
			}
			if ($user_command==$this->module_command."BASKET_VIEW"){
				return $this->shop_basket_view($parameter_list);
			}
			if ($user_command==$this->module_command."REMOVE_FROM_BASKET"){
				return $this->shop_remove_item_from_basket($parameter_list);
			}
			if ($user_command==$this->module_command."PURCHASE_BASKET"){
				return $this->shop_purchase_basket($parameter_list);
			}
			if ($user_command==$this->module_command."UPDATE_BASKET"){
				$this->shop_update_basket($parameter_list);
				/*************************************************************************************************************************
                * if purchase button pressed the redirect to _purchase-cart.php otherwise view the cart
                *************************************************************************************************************************/
				if($this->check_parameters($parameter_list,"button","")=="Purchase"){
					if($this->parent->domain != $this->parent->DEV_SERVER){
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("url" => "http://".$this->parent->domain."".$this->parent->base."_purchase-cart.php"));
					} else {
						$this->call_command("ENGINE_REFRESH_BUFFER",Array("url" => "http://".$this->parent->domain."".$this->parent->base."_purchase-cart.php"));
					}
				} else {
					$this->call_command("ENGINE_REFRESH_BUFFER",Array("url" => "_view-cart.php"));
				}
			}
			if ($user_command==$this->module_command."BLANK_CART"){
				return $this->blank_cart();
			}
			if ($user_command==$this->module_command."GET_COST"){
				return $this->get_cost($parameter_list);
			}
			if ($user_command==$this->module_command."GET_EDITORS"){
				return $this->shop_get_editors();
			}
			if ($user_command==$this->module_command."ITEM_PURCHASE_HISTORY"){
				return $this->display_item_purchase_history($parameter_list);
			}
			if ($user_command==$this->module_command."CHECK_STATUS_LEVELS"){
				return $this->check_status_levels($parameter_list);
			}
		}else{
			// wrong command sent to system
		}
	}
	
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
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"initialise",__LINE__,""));
		}
       	$this->load_locale("shop");
/*
		$this->preferences = Array(
			Array('sp_currency_symbol'			,'LOCALE_SP_CURRENCY_SYMBOL'						,'Pounds Sterling'	, 'Pounds Sterling:Euros:US Dollars'	, "SYSPREFS_",	"ALL")
		);
*/
		$this->shop_setup=0;
		$this->shop_order_approver=0;
		/*************************************************************************************************************************
        * load editor definition
        *************************************************************************************************************************/
		$this->editor_configurations = Array(
			"ENTRY_INVOICE_CONFIRM" 	=> $this->generate_default_editor(),
			"ENTRY_INVOICE_EMAIL"		=> $this->generate_default_editor(),
			"ENTRY_CREDITCARD_CONFIRM"	=> $this->generate_default_editor(),
			"ENTRY_CREDITCARD_DENY"		=> $this->generate_default_editor()
		);

		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier	= $this->parent->client_identifier;
		/*************************************************************************************************************************
        * get shop settings
        *************************************************************************************************************************/
		$sql = "select * from shop_settings where ss_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$this->shop_setting_id	= -1;
		$this->ss_autoreduce	= 0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$this->shop_setting_id 	= $r["ss_identifier"];
			$this->vat_rate			= $r["ss_vat"];
			if($this->vat_rate<0){
				$this->vat_rate=0;
			}
			$this->ss_autoreduce	= $r["ss_autoreduce"];
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
			if($r["ss_delivery_always_same"]==1){
				$this->bill_and_del_same	= "Yes";
			} else {
				$this->bill_and_del_same	= "No";
			}
			if($r["ss_can_pay_invoice"]==1){
				$this->can_pay_by_invoice	= "Yes";
			} else {
				$this->can_pay_by_invoice	= "No";
			}
			if($r["ss_can_request_invoice"]==1){
				$this->can_request_invoice	= "Yes";
			} else {
				$this->can_request_invoice	= "No";
			}
        }
		/**
		* define the filtering information that is available
		*/
		$this->display_options		= array(
			array (0,"Order by Date Created (oldest first)"	,"shop_creation_date Asc"),
			array (1,"Order by Date Created (newest first)"	,"shop_creation_date desc"),
			array (2,"Order by Title A -> Z"				,"shop_title asc"),
			array (3,"Order by Title Z -> A"				,"shop_title desc")
		);

		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		$access_list = "";
		$access_array = array();
		$ALL=0;
		for($i=0;$i < $max_grps; $i++){
			$access = $grp_info[$i]["ACCESS"];
			$access_length = count($access);
			$out = "";
			for ($index=0,$length_of_array=count($access);$index<$length_of_array;$index++){
				if (
					("SHOP_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("SHOP_ORDER_PROCESSOR"==$access[$index])
				){
					$this->shop_order_approver=1;
				}
				if (
					("SHOP_ALL"==$access[$index]) ||
					("ALL"==$access[$index]) || 
					("SHOP_SETUP"==$access[$index])
				){
					$this->shop_setup=1;
				}
			}
		}
		
		$this->module_admin_options[count($this->module_admin_options)] = array("SHOP_LIST_ORDERS", MANAGE_PAYMENT_ORDERS, "SHOP_ORDER_PROCESSOR","Content Manage/Ecommerce Orders");
		$this->module_admin_options[count($this->module_admin_options)] = array("SHOP_MESSAGES", MANAGE_SHOP_MESSAGES,"SHOP_SETUP","Preferences/Ecommerce");
//		$this->module_admin_options[count($this->module_admin_options)] = array("PAYGATEADMIN_SETUP", MANAGE_PAYGATE,"SHOP_SETUP","Preferences/Ecommerce");
		$this->module_admin_options[count($this->module_admin_options)] = array("SHOP_SALES_TAX", LOCALE_SALES_TAX,"SHOP_SETUP","Preferences/Ecommerce");
		$this->module_admin_options[count($this->module_admin_options)] = array("SHOP_CHOOSEPAY", LOCALE_CHOOSE_PAYMENT,"SHOP_SETUP","Preferences/Ecommerce");
		$this->module_admin_options[count($this->module_admin_options)] = array("SHOP_WEIGHT_SETUP", MANAGE_SHOP_DELIVERY_SETTINGS,"SHOP_SETUP","Preferences/Ecommerce");
		
		$this->admin_access = 0;
		if (($this->shop_setup==1) || ($this->shop_order_approver==1) || ($this->parent->module_type=="admin") || ($this->parent->module_type=="view_comments") || ($this->parent->module_type=="preview") || ($this->parent->module_type=="files")){
			$this->admin_access = 1;
		}
	}
	
	/**
	* function produces abstract structure of modules db structure
	*
    * used to generate the table structure required abstract function that passes an abstract
	* representation of the desired table structure to the proper database module which will
	* interpet the abstract and convert it into a valid SQL Create table structure.
	*
	* @return Array list of abstract table definitions for this module
	*/
	function create_table(){
		$tables = array();
		/**
		* Table structure for table 'shop_settings'
		*/
		$fields = array(
			array("ss_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("ss_client"				,"unsigned integer"			,""			,"default '0'"),
			array("ss_charge_vat"			,"unsigned small integer"	,""			,"default '0'"),
			array("ss_vat"					,"double"					,""			,"default '0'"),
			array("ss_always_charge"		,"unsigned small integer"	,""			,"default '0'"),
			array("ss_delivery_always_same"	,"unsigned small integer"	,""			,"default '0'"),
			array("ss_can_pay_invoice"		,"unsigned small integer"	,""			,"default '0'"),
			array("ss_can_request_invoice"	,"unsigned small integer"	,""			,"default '0'"),
			array("ss_cc_accept_label"		,"varchar(255)"				,""			,"default ''"),
			array("ss_cc_deny_label"		,"varchar(255)"				,""			,"default ''"),
			array("ss_invoice_msg_label"	,"varchar(255)"				,""			,"default ''"),
			array("ss_invoice_email_label"	,"varchar(255)"				,""			,"default ''"),
			array("ss_currency"				,"varchar(3)"				,""			,"default 'GBP'"),
			array("ss_autoreduce"			,"unsigned small integer"	,""			,"default '0'")
		);
		$primary ="ss_identifier";
		$tables[count($tables)] = array("shop_settings",$fields,$primary);

		/**
		* Table structure for table 'shop_weight'
		*/
		$fields = array(
			array("swm_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("swm_client"				,"unsigned integer"			,""			,"default '0'"),
			array("swm_country"				,"integer"					,""			,"default '0'"),
			array("swm_region"				,"varchar(2)"					,""			,"default '0'"),
			array("swm_price"				,"double"					,""			,"default '0'"),
			array("swm_kg"					,"double"					,""			,"default '0'") // -1 represents above highest
		);
		$primary ="swm_identifier";
		$tables[count($tables)] = array("shop_weight_matrix",$fields,$primary);

		/**
		* Table structure for table 'shop_weight_matrix_list'
		*/
		$fields = array(
			array("sw_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("sw_client"				,"unsigned integer"			,""			,"default '0'"),
			array("sw_max_kg"				,"double"					,""			,"default '0'")
		);
		$primary ="sw_identifier";
		$tables[count($tables)] = array("shop_weight_matrix_list",$fields,$primary);
		/**
		* Table structure for table 'shop_basket_container'
		*/
		$fields = array(
			array("shop_basket_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("shop_basket_client"				,"unsigned integer"			,""			,"default '0'"),
			array("shop_basket_shop"				,"unsigned integer"			,""			,"default '0'"),
			array("shop_basket_session"				,"varchar(32)"				,""			,"default ''"),
			array("shop_basket_user"				,"unsigned integer"			,""			,"default '0'"),
			array("shop_basket_bill_contact"		,"unsigned integer"			,""			,"default '0'"),
			array("shop_basket_delivery_contact"	,"unsigned integer"			,""			,"default '0'"),
			array("shop_basket_status"				,"unsigned small integer"	,""			,"default '0'"),
			array("shop_basket_processed_by"		,"unsigned integer"			,""			,"default '0'"),
			array("shop_basket_date"				,"datetime"					,""			,"default ''"),
			array("shop_basket_payment"				,"small integer"			,""			,"default '0'"),
			array("shop_basket_requires_invoice"	,"small integer"			,""			,"default '1'"),
			array("shop_basket_invoice_sent_date"	,"datetime"					,""			,"default ''")
		);
		$primary ="shop_basket_identifier";
		$tables[count($tables)] = array("shop_basket_container",$fields,$primary);
		/**
		* Table structure for table 'shop_basket_items'
		*/
		$fields = array(
			array("shop_item_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("shop_item_basket"				,"unsigned integer"			,""			,"default '0'"),
			array("shop_item_client"				,"unsigned integer"			,""			,"default '0'"),
			array("shop_item_shop"					,"unsigned integer"			,""			,"default '0'"),
			array("shop_item_stock_id"				,"unsigned integer"			,""			,"default '0'"),
			array("shop_item_stock_group"			,"unsigned integer"			,""			,"default '0'"),
			array("shop_item_pickup_price"			,"double"					,""			,"default '0'"),
			array("shop_item_pickup_discount"		,"double"					,""			,"default '0'"),
			array("shop_item_quantity"				,"unsigned integer"			,""			,"default '0'"),
			array("shop_item_weight"				,"double"					,""			,"default '0'"),
			array("shop_item_title"					,"varchar(255)"				,""			,"default ''"),
			array("shop_item_description"			,"varchar(255)"				,""			,"default ''"),
			array("shop_item_vat"					,"unsigned small integer"	,""			,"default '0'")
		);
		$primary ="shop_item_identifier";
		$tables[count($tables)] = array("shop_basket_items",$fields,$primary);
		/**
		* Table structure for table 'shop_basket_status'
		*/
		$fields = array(
			array("shop_basket_status_identifier"			,"unsigned integer"	,"NOT NULL"	,"auto_increment"),
			array("shop_basket_status_label"				,"varchar(255)"		,""			,"default ''")
		);
		$primary ="shop_basket_status_identifier";
		$tables[count($tables)] = array("shop_basket_account_status",$fields,$primary);
		/**
		* Table structure for table 'shop_trigger'
		*/
		$fields = array(
			array("st_identifier"			,"unsigned integer"			,"NOT NULL"	,"auto_increment"),
			array("st_basket_item"			,"unsigned integer"			,""			,"default ''"),
			array("st_client"				,"unsigned integer"			,""			,"default '0'"),
			array("st_executed"				,"unsigned small integer"	,""			,"default '0'"),
			array("st_cmd"					,"varchar(255)"				,""			,"default ''")
		);
		$primary ="st_identifier";
		$tables[count($tables)] = array("shop_trigger",$fields,$primary);
		/**
		* Table structure for table 'shop_trigger_parameters'
		*/
		$fields = array(
			array("stp_trigger"			,"unsigned integer"			,""	,"default '0'"),
			array("stp_client"			,"unsigned integer"			,""	,"default '0'"),
			array("stp_rank"			,"unsigned integer"			,""	,"default '0'"),
			array("stp_key"				,"varchar(255)"				,""	,"default ''"),
			array("stp_value"			,"varchar(255)"				,""	,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("shop_trigger_parameters",$fields,$primary);

		return $tables;
	}

	/**
	* This function is used to return the list of orders to be dealt with
	*/
	function retrieve_my_docs($parameters){
		$sql    = "SELECT shop_basket_status, count(shop_basket_client) as total_orders FROM shop_basket_container where shop_basket_status>=4  and shop_basket_client = $this->client_identifier group by shop_basket_status";
		$result = $this->parent->db_pointer->database_query($sql);
//		$out="<grouped label=\"".LOCALE_SHOP_ORDERS."\">";
$out="";
		$total_orders=0;
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
				$out .= "<title identifier='".$r["shop_basket_status"]."' result='".$r["total_orders"]."'><![CDATA[".$this->shop_status($r["shop_basket_status"])."]]></title>";
				$total_orders+=$r["total_orders"];
			}
				$out .= "<title result='".$total_orders."'><![CDATA[".LOCALE_SHOP_TOTAL_ORDERS."]]></title>";
			$out .= "<commands><cmd label='".LOCALE_VIEW."'>SHOP_ORDER_VIEW_TYPE</cmd></commands>";
		}else{
			$out .= "<text><![CDATA[".LOCALE_SORRY_NO_ORDERS."]]></text>";
		}
//		$out .= "</grouped>";
		$date = date("Y/m/d H:i:s",mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"))-(24*3600));
		$sql    = "SELECT count(shop_basket_client) as total_lost_baskets FROM shop_basket_container where shop_basket_status<4 and shop_basket_container.shop_basket_date  < '".$date."' and shop_basket_client = $this->client_identifier";
		$result = $this->parent->db_pointer->database_query($sql);
//		$out.="<grouped label=\"".LOCALE_SHOP_LOST_BASKETS."\">";
		if ($this->call_command("DB_NUM_ROWS",Array($result))>0){
			while ($r=$this->call_command("DB_FETCH_ARRAY", array($result))){
				$name=$r["total_lost_baskets"];
				if ($name==0){
					$out .= "<text><![CDATA[".LOCALE_SORRY_NO_LOST_BASKETS."]]></text>";
				}else{
					$out .= "<title identifier='-1'><![CDATA[".$name."]]></title>";
				}
			}
			$out .= "<commands><cmd label='".REMOVE_EXISTING."'>SHOP_BASKET_CLEAN_UP</cmd></commands>";
		}else{
			$out .= "<text><![CDATA[".LOCALE_SORRY_NO_LOST_BASKETS."]]></text>";
		}
//		$out .= "</grouped>";
		
		$out = "<module name=\"shop\" label=\"".MANAGEMENT_SHOP."\" display=\"my_workspace\">".$out."</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * display a list of orders
    *************************************************************************************************************************/
	function display_list_orders($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$reference = $this->check_parameters($parameters,"reference","");
		$cond ="";
		if ($identifier!=-1){
			$cond = " shop_basket_status = '$identifier' and ";
		} else {
			$cond = " shop_basket_status >= 4 and ";
		}
		if ($reference==""){
			$sql    = "
					SELECT 
						country_lookup.*, email_addresses.*,contact_address.*, shop_basket_container.*, bill_payer.*, count(shop_basket_items.shop_item_basket) as total 
					FROM shop_basket_container 
					inner join 
						shop_basket_items on 
							shop_basket_items.shop_item_basket = shop_basket_container.shop_basket_identifier
					inner join 
						contact_data as bill_payer on 
							bill_payer.contact_identifier = shop_basket_bill_contact
					inner join 
						contact_address on 
							contact_address.address_identifier = bill_payer.contact_address
					left outer join 
						country_lookup on 
							contact_address.address_country = country_lookup.cl_identifier
					inner join 
						email_addresses on 
							email_addresses.email_contact = bill_payer.contact_identifier
					where 
						$cond
						shop_basket_client = $this->client_identifier 
					group by 
						shop_basket_items.shop_item_basket 
					order by 
						shop_basket_identifier desc";
		} else {
			$sql    = "
					SELECT 
						country_lookup.*, email_addresses.*,contact_address.*, shop_basket_container.*, bill_payer.*, count(shop_basket_items.shop_item_basket) as total 
					FROM shop_basket_container 
					inner join 
						shop_basket_items on 
							shop_basket_items.shop_item_basket = shop_basket_container.shop_basket_identifier
					inner join 
						contact_data as bill_payer on 
							bill_payer.contact_identifier = shop_basket_bill_contact
					inner join 
						contact_address on 
							contact_address.address_identifier = bill_payer.contact_address
					left outer join 
						country_lookup on 
							contact_address.address_country = country_lookup.cl_identifier
					inner join 
						email_addresses on 
							email_addresses.email_contact = bill_payer.contact_identifier
					where 
						$cond
						shop_basket_identifier = $reference and 
						shop_basket_client = $this->client_identifier
					group by 
						shop_basket_items.shop_item_basket 
					order by 
						shop_basket_identifier desc";
		}
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->parent->db_pointer->database_query($sql);
		$variables["PAGE_BUTTONS"] = Array();
		$variables["FILTER"]			= $this->order_list_filter($parameters);
		
		if (!$result){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
			$number_of_records=0;
			$goto=0;
			$finish=0;
			$page=0;
			$num_pages=0;
		}else{
			$this->page_size=50;
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$page = intval($this->check_parameters($parameters,"page",1));
			$goto = ((--$page) * $this->page_size);
			
			if (($goto!=0)&&($number_of_records>$goto)){
				$pointer = $this->call_command("DB_SEEK",array($result,$goto));
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
			
			$counter=0;
			
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page+=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			
			$variables["ENTRY_BUTTONS"] =Array(
			);
			
			$variables["RESULT_ENTRIES"] =Array();
			
			while (($r = $this->parent->db_pointer->database_fetch_array($result))&&($counter<$this->page_size)){
				$counter++;
				$limit = $this->check_parameters($r,"shop_acc_limit",LOCALE_NO_LIMIT);
				if ($limit==0){
					$limit =LOCALE_NO_LIMIT;
				}
				$status="";
				if ($r["shop_basket_status"]>=4){
					$status = $this->status_list[$r["shop_basket_payment"]];
				}
				/*
				if($r["shop_basket_payment"]==0){
					if ($r["shop_basket_status"]>=4){
						$status = "payment rejected (Credit Card)";
					}
				}
				if($r["shop_basket_payment"]==1){
					if ($r["shop_basket_status"]>=4){
						$status = "payment received (Credit Card)";
					}
				}
				if($r["shop_basket_payment"]==2){
					if ($r["shop_basket_status"]>=4){
						$status = "payment request (Invoice)";
					}
				}
				if($r["shop_basket_payment"]==3){
					if ($r["shop_basket_status"]>=4){
						$status = "payment received (Invoice)";
					}
				}
				*/
				$variables["RESULT_ENTRIES"][count($variables["RESULT_ENTRIES"])]=Array(
					"identifier"	=> $r["shop_basket_identifier"],
					"ENTRY_BUTTONS"	=>	Array(
						Array("PROCESS",$this->module_command."PROCESS_ORDER",LOCALE_SHOP_PROCESS_ORDER)
					,Array("REMOVE",$this->module_command."DELETE_ORDER",LOCALE_SHOP_DELETE_ORDER)),
					
					"attributes"	=> Array(
						array("Reference #",$this->check_parameters($r,"shop_basket_identifier"),"TITLE"),
						array(LOCALE_CONTACT_NAME,$this->check_parameters($r,"contact_first_name").", ".$this->check_parameters($r,"contact_last_name"),"SUMMARY"),
						array(LOCALE_SHOP_NUM_OF_ITEMS,$this->check_parameters($r,"total")),
						array(LOCALE_SHOP_STATUS,$this->shop_status($this->check_parameters($r,"shop_basket_status","0"))." - ".$status),
						array(LOCALE_DATE,$this->check_parameters($r,"shop_basket_date"))
					)
				);


			}
		}
		$variables["NUMBER_OF_ROWS"]	= $number_of_records;
		$variables["START"]				= $goto;
		$variables["FINISH"]			= $finish;
		$variables["CURRENT_PAGE"]		= $page;
		$variables["NUMBER_OF_PAGES"]	= $num_pages;
		$variables["as"]	= "table";
		$out = $this->generate_list($variables);
		return $out;
	}


	function display_process_order($parameters){
		$basket_identifier	= $this->check_parameters($parameters,"identifier",-1);
		$order_type			= $this->check_parameters($parameters,"order_type",-1);
		$entrylist = $this->get_order(Array("basket_identifier"=>$basket_identifier));
		$status_msg ="";
		$invoice_sent_date="";
		$paymethod="";
		if($entrylist["bstatus"]>=4){
			if ($entrylist["payment"]==0){
				$status_msg ="Payment was denied for this basket";
			} else if ($entrylist["payment"]==2){
				$status_msg ="Request to pay this order by cheque";
			} else if ($entrylist["payment"]==1){
				$status_msg ="Payment Recieved";
			}
		} else {
			$status_msg ="Order not completed yet";
		}
		$invoice_sent_date = $entrylist["invoice_sent_date"];
		$pay_method =$status_msg;
		$content = "<module name='shop' display='form'>
						<page_options>
							<header><![CDATA[$status_msg]]></header>
						</page_options>
						<form width='100%' name='shop_processing' method='POST' label='".LOCALE_SHOP_PROCESS_ORDER."'>
							<input type='hidden' name='command' value='SHOP_MARK_ORDER_PROCESSED_AS'/>
							<input type='hidden' name='identifier' value='$basket_identifier'/>
							<input type='hidden' name='type' value='$order_type'/>
							<text><![CDATA[<table width='100%'><tr><td><strong>Order Reference ::</strong> #$basket_identifier</td>";
		$payoptions = $this->gen_options(
			Array(0,1,4,5,2,3,6,7,8), 
			Array("Credit Card - Payment denied",
			"Credit Card - Payment approved",
			"Credit Card - Payment awaiting refund",
			"Credit Card - Payment refunded",
			"Cheque - Payment requested",
			"Cheque - Payment received",
			"Cheque - Payment awaiting refund",
			"Cheque - Payment refunded",
			"Cheque - No Payment required",
			),$entrylist["payment"]);
		$content .= "	<td><strong><label for='prev_paymethod'>Payment Status</label> </strong> <input type='hidden' name='prev_paymethod' id='prev_paymethod' value='".$entrylist["payment"]."'/></input><select name='paymethod'>$payoptions</select></td></tr><tr><td>";
		$content .= "<strong><label for='processaction'>Process Order</label></strong>[[nbsp]]<select name='process_action' id='processaction'>
								<option value='-1'>Do not change status</option>
							";
		if($entrylist["bstatus"]>=4){
			if($entrylist["payment"]==0){
				$content .= "	<option value='9'>Reject this order</option>";
			}
			if($entrylist["payment"]!=0){
				$content .= "	<option value='9'>Mark order as rejected</option>
								<option value='10'>Mark order as out of stock</option>
								<option value='6'>Mark order as ready for delivery</option>
								<option value='7'>Mark order as shipping</option>
								<option value='8'>Mark order as completed</option>";
			}
		}
		$content .= "</select>";
		$content .= "<input type='hidden' name='previous_action' value='".$entrylist["bstatus"]."'/></td><td>";
		if($entrylist["requireinvoice"]==1){
			$content .= "Required invoice sent <input type='checkbox' name='required_invoice' value='sent'/><input type='hidden' name='requires_invoice' value='1'/>";
		} else if($entrylist["requireinvoice"]==2){
			$content .= "Order invoice was sent on $invoice_sent_date <input type='hidden' name='requires_invoice' value='2'/>";
		} else {
			$content .= "<input type='hidden' name='requires_invoice' value='0'/>";
		}
		$content .= "</td></tr></table>]]></text>";
		$content .= $entrylist["output"]."
				<text><![CDATA[<strong>Information about this user</strong><br/>
				<ul>";
//admin/index.php?command=USERACCESS_TRACE_SESSION&identifier=95061
//admin/index.php?command=USERACCESS_TRACE_SESSION&identifier=95061
//		$content .= "<li><a href='admin/index.php?command=USERACCESS_TRACE_SESSION&identifier=".$entrylist["user_access"]["uai"]."'>Trace</a></li>";
		if ($this->check_parameters($entrylist["user_access"],"country_flag","n.a.")!="n.a."){
			$content .= "<li>IP address ".$this->check_parameters($entrylist["user_access"],"ip")."</li>
						 <li>IP based in <img src='/libertas_images/icons/flags/".$this->check_parameters($entrylist["user_access"],"country_flag").".png'/> ".$this->check_parameters($entrylist["user_access"],"country_label")."</li>
						";
		} else {
			$content .= "<li>IP address ".$this->check_parameters($entrylist["user_access"],"ip")."</li>
						 <li>IP not currently listed in IP to Country Database</li>";
		}
		
		if ($this->check_parameters($entrylist["user_access"],"language_flag","n.a.")!="n.a."){
			$content .= "<li>The language of their computer was set to <img src='/libertas_images/icons/flags/".$this->check_parameters($entrylist["user_access"],"language_flag").".png'/> ".$this->check_parameters($entrylist["user_access"],"language_label")."</li>";
		} else {
			$content .= "<li>The language of their computer was not detected</li>";
		}
		$content .= "
				</ul>]]></text>
							<input type='button' iconify='CANCEL' value='back' command='SHOP_LIST_ORDERS'/>
							<input type='submit' iconify='SAVE' value='Process'/>
						</form>
					</module>";
		return $content;
	}


	function display_order_processed_as($parameters){
		$identifier			= $this->check_parameters($parameters, "identifier", -1);
		$type 				= $this->check_parameters($parameters, "type",-1);
		$process_action 	= $this->check_parameters($parameters, "process_action", -1);
		$previous_action	= $this->check_parameters($parameters, "previous_action",-1);
		$requires_invoice	= $this->check_parameters($parameters, "requires_invoice", 0);
		$required_invoice	= $this->check_parameters($parameters, "required_invoice", "");
		$paymethod			= $this->check_parameters($parameters, "paymethod",-1);
		$prev_paymethod		= $this->check_parameters($parameters, "prev_paymethod",-1);
		$set ="";
		if($process_action!=-1){
			$set .= "shop_basket_status = $process_action";
			if($process_action==4 || $process_action==6 || $process_action==7 || $process_action==8 || $process_action==9){
				$this->manage_quantity(Array("basket_identifier"=>$identifier, "process_action"=>$process_action, "previous_action"=>$previous_action));
			}
			/*************************************************************************************************************************
            * call the automatic triggers
            *************************************************************************************************************************/
			if(($process_action==6 || $process_action==7 || $process_action==8) && ($paymethod==1 || $paymethod==3 || $paymethod==8)){
				$list_of_items = Array();
				$sql = "select * from shop_basket_items where shop_item_basket = $identifier and shop_item_client = $this->client_identifier";
				$result  = $this->parent->db_pointer->database_query($sql);
                while($r = $this->parent->db_pointer->database_fetch_array($result)){
                	$list_of_items[count($list_of_items)] = $r["shop_item_identifier"];
                }
                $this->parent->db_pointer->database_free_result($result);
				$this->process_trigger(Array("basket_items" => $list_of_items));
			}
		}
		if ($requires_invoice==1){
			if($required_invoice=="sent"){
				if($set!=""){
					$set .=", ";
				}
				$now = $this->libertasGetDate();
				$set .= "shop_basket_requires_invoice = 2, ";
				$set .= "shop_basket_invoice_sent_date = '$now'";
			}
		}
		if($prev_paymethod!=$paymethod){
			if($set!=""){
				$set .=", ";
			}
			$set .= "shop_basket_payment = $paymethod";
		}
		if($set!=""){
			$sql = "update shop_basket_container 
						set
							$set
					where 
						shop_basket_identifier = $identifier and 
						shop_basket_client = $this->client_identifier";
			$this->parent->db_pointer->database_query($sql);
		}
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("url"=>$this->parent->base."admin/index.php?command=SHOP_LIST_ORDERS"));
	}
	

	function display_delete_order($parameters){
		$basket_identifier	= $this->check_parameters($parameters,"identifier",-1);
		$order_type			= $this->check_parameters($parameters,"order_type",-1);
		$entrylist = $this->get_order(Array("basket_identifier"=>$basket_identifier));
		$status_msg ="";
		$invoice_sent_date="";
		$paymethod="";
		if($entrylist["bstatus"]>=4){
			if ($entrylist["payment"]==0){
				$status_msg ="Payment was denied for this basket";
			} else if ($entrylist["payment"]==2){
				$status_msg ="Request to pay this order by cheque";
			} else if ($entrylist["payment"]==1){
				$status_msg ="Payment Recieved";
			}
		} else {
			$status_msg ="Order not completed yet";
		}
		$invoice_sent_date = $entrylist["invoice_sent_date"];
		$pay_method =$status_msg;
		$status_msg = "Confirm Order Removal";
		$content = "<module name='shop' display='form'>
						<page_options>
							<header><![CDATA[$status_msg]]></header>
						</page_options>
						<form width='100%' name='shop_delete_order' method='POST' label='".LOCALE_SHOP_DELETE_ORDER."'>
							<input type='hidden' name='command' value='SHOP_DELETE_ORDER'/>
							<input type='hidden' name='identifier' value='$basket_identifier'/>
							<input type='hidden' name='type' value='$order_type'/>
							<text><![CDATA[<table width='100%'><tr><td><strong>Order Reference ::</strong> #$basket_identifier</td>";
		$content .= "<input type='hidden' name='previous_action' value='".$entrylist["bstatus"]."'/></td><td>";
		$content .= "				
							<input type='button' iconify='CANCEL' value='back' command='SHOP_LIST_ORDERS'/>
							<input type='submit' iconify='SAVE' value='Delete'/>
						</form>
					</module>";
		return $content;
	}


	function order_list_filter($parameters){
		$cmd		= $this->check_parameters($parameters,"command");
		$ref 		= $this->check_parameters($parameters,"reference");
		$type		= $this->check_parameters($parameters,"identifier");
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"filter",__LINE__,join($parameters,", ")));
		}
		$out = "\t\t\t\t<form name=\"order_filter_form\" label=\"Shopping Order filter\" method=\"GET\">\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"command\" value=\"$cmd\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"page\" value=\"1\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"hidden\" name=\"search\" value=\"1\"/>\n";
		$out .= "\t\t\t\t\t<input type=\"text\" label='".LOCALE_SHOP_ORDER_REFERENCE."' name=\"reference\"><![CDATA[$ref]]></input>\n";
		$out .= "\t\t\t\t\t<input type=\"submit\" iconify=\"SEARCH\" name=\"\" value=\"Filter\"/>\n";
		$out .= "\t\t\t\t</form>";
		/**
		* return the filter XML document
		*/
		return $out;
	}
	/*************************************************************************************************************************
    * used by my_workspace
    *************************************************************************************************************************/
	function shop_status($int){
		if ($int==1 || $int==0)
			return LOCALE_SHOP_BASKET_IN_USE;
		if ($int==2)
			return LOCALE_SHOP_BASKET_AT_CHECKOUT;
		if ($int==3)
			return LOCALE_SHOP_BASKET_REQUESTING_PAYMENT;
		if ($int==4)
			return LOCALE_SHOP_BASKET_PURCHASE_COMPLETE;
		if ($int==5)
			return LOCALE_SHOP_BASKET_PURCHASE_BEING_PROCESSED;
		if ($int==6)
			return LOCALE_SHOP_BASKET_READY_DELIVERY;
		if ($int==7)
			return LOCALE_SHOP_BASKET_PURCHASE_BEING_DELIVERED;
		if ($int==8)
			return LOCALE_SHOP_BASKET_PURCHASE_DELIVERED;
		if ($int==9)
			return LOCALE_SHOP_BASKET_PURCHASE_REJECTED;
		if ($int==10)
			return LOCALE_SHOP_BASKET_PURCHASE_OUT_OF_STOCK;
	}
	/*************************************************************************************************************************
    *                                  P R E S E N T A T I O N   F U N C T I O N S
    *************************************************************************************************************************/

	/*************************************************************************************************************************
    * create a new basket and store the identifier in the session array
    *************************************************************************************************************************/
	function create_basket($parameters){
		$shop_basket_identifier = $this->check_parameters($_SESSION,"SHOP_BASKET_IDENTIFIER","__NOT_FOUND__");
		$override = $this->check_parameters($parameters,"override",0);
		if (($override==1) || ($shop_basket_identifier == "__NOT_FOUND__") || ($shop_basket_identifier == "-1")){
			$shop_basket_client				= $this->client_identifier;
			$shop_basket_session			= session_id();
			$shop_basket_user				= $this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0);
			$shop_basket_bill_contact		= $this->check_parameters($parameters,"ucid",0);
			$shop_basket_delivery_contact	= $this->check_parameters($parameters,"ucid",0);
			$shop_basket_status				= $this->check_parameters($parameters,"basket_status",0);
			$shop_basket_processed_by		= 0;
			$shop_basket_date				= $this->libertasGetDate("Y/m/d H:i:s");
			$shop_basket_identifier			= $this->getUID();
			$sql ="
			insert into shop_basket_container (
				shop_basket_identifier,
				shop_basket_client,
				shop_basket_session,
				shop_basket_user,
				shop_basket_bill_contact,
				shop_basket_delivery_contact,
				shop_basket_status,
				shop_basket_processed_by,
				shop_basket_date
			) values(
				$shop_basket_identifier,
				$shop_basket_client,
				'$shop_basket_session',
				$shop_basket_user,
				$shop_basket_bill_contact,
				$shop_basket_delivery_contact,
				$shop_basket_status,
				$shop_basket_processed_by,
				'$shop_basket_date'
			);";
			$this->parent->db_pointer->database_query($sql);
			$_SESSION["SHOP_BASKET_IDENTIFIER"] = $shop_basket_identifier;
		}
		return $shop_basket_identifier;
	}

	/**
    * add an item to the basket
	*
	* the key type is not the web_container it is used to extract the web container it should be the module starter minus
	* the last underscore
	* @param Array (keys are "identifier" and "type")
	*/
	function shop_basket_add_to_basket($parameters){
		$stock_identifier = $this->check_parameters($parameters,"identifier");
		$type			  = $this->check_parameters($parameters,"type");
		$module = $this->call_command($type."_GET_WEB_CONTAINER");
		$basket_identifier = $this->create_basket($parameters);
		$sql = "select * from metadata_details where md_client=$this->client_identifier and md_link_group_id=$stock_identifier and md_module = '$module' order by md_identifier desc limit 1";
		$result = $this->parent->db_pointer->database_query($sql);
		$stock_price = -1;
		$shop_item_vat=0;
		$amount_left=-1;
		if ($r = $this->parent->db_pointer->database_fetch_array($result)){
			$shop_item_pickup_price		= $r["md_price"];
			$shop_item_pickup_discount 	= $r["md_discount"];
			$shop_item_title			= $r["md_title"];
			$shop_item_description		= $r["md_description"];
			$shop_item_weight			= $r["md_weight"];
			$shop_item_vat				= $r["md_vat"];
			$amount_left 				= $r["md_quantity"]; // how many available
			$shop_item_stock_group		= $r["md_link_group_id"];
		}
		$shop_item_quantity=1;
		$this->parent->db_pointer->database_free_result($result);
		$sql ="select * from shop_basket_items 
					inner join metadata_details on md_identifier = shop_item_stock_id and md_client= shop_item_client
				where shop_item_basket='$basket_identifier' and shop_item_client = $this->client_identifier and shop_item_stock_id='$stock_identifier'";
		$found=0;
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$found=1;
			$prev_amount = $r["shop_item_quantity"]; // how many currently in basket
        }
        $this->parent->db_pointer->database_free_result($result);
		$sql = "";
		$quantity_left=-1;
		if($found==0){
			$sql = "insert into shop_basket_items (
						shop_item_basket,
						shop_item_client,
						shop_item_stock_id,
						shop_item_title,
						shop_item_description,
						shop_item_pickup_price,
						shop_item_pickup_discount,
						shop_item_quantity,
						shop_item_weight,
						shop_item_vat,
						shop_item_stock_group
					) values (
						'$basket_identifier',
						'$this->client_identifier',
						'$stock_identifier',
						'$shop_item_title',
						'$shop_item_description',
						'$shop_item_pickup_price',
						'$shop_item_pickup_discount',
						'1',
						'$shop_item_weight',
						'$shop_item_vat',
						'$shop_item_stock_group'
					)";
		} else {
			$update=0;
			if($amount_left!=-1){
				if ((($prev_amount - $shop_item_quantity)+$amount_left)<0){
					$shop_item_quantity = $prev_amount+$amount_left;
					$quantity_left = 0;
					$update=1;
				} else {
					$quantity_left = (($prev_amount - $shop_item_quantity)+$amount_left);
					$update=1;
				}
			} else {
					$update=1;
			}
			if ($update == 1){
				$sql = "update shop_basket_items set 
							shop_item_title				= '$shop_item_title',
							shop_item_description		= '$shop_item_description',
							shop_item_pickup_price		= '$shop_item_pickup_price',
							shop_item_pickup_discount	= '$shop_item_pickup_discount',
							shop_item_quantity 			= '$shop_item_quantity',
							shop_item_weight 			= '$shop_item_weight',
							shop_item_vat 				= '$shop_item_vat',
							shop_item_stock_group 		= '$shop_item_stock_group'
						where 
							shop_item_stock_id			= '$stock_identifier' and 
							shop_item_basket 			= '$basket_identifier' and 
							shop_item_client 			= '$this->client_identifier'";
			}
		}
		/*************************************************************************************************************************
        *  execute the appropraite command
        *************************************************************************************************************************/
		if($sql !=""){
			$this->parent->db_pointer->database_query($sql);
			if($quantity_left>=0){
//				$sql = "update metadata_details set md_quantity = $quantity_left where md_identifier = '$stock_identifier' and md_client = $this->client_identifier";
//				$this->parent->db_pointer->database_query($sql);
//				$this->call_command("METADATAADMIN_CACHE",Array("md_identifier"=>$stock_identifier));
			}
			return 1; // pass
		} else {
			return 0; // fail
		}
	}
	
	/*************************************************************************************************************************
    *
    *************************************************************************************************************************/
	function shop_basket_view($parameters){
		$basket_identifier = $this->check_parameters($_SESSION,"SHOP_BASKET_IDENTIFIER",-1);
		$settings = $this->call_command("SHOP_GET_SETTINGS");
		
		$sql = "select * from shop_basket_items 
					inner join shop_basket_container on shop_basket_container.shop_basket_identifier = shop_basket_items.shop_item_basket and shop_basket_client=shop_item_client
				where shop_item_client= $this->client_identifier and shop_basket_container.shop_basket_identifier = $basket_identifier";
		$result = $this->parent->db_pointer->database_query($sql);
		$basket_info = "";
		$total=0;
		$totalvat=0;
		$c=0;
		$bstatus=0;
		while ($r = $this->parent->db_pointer->database_fetch_array($result)){
			if ($r["shop_basket_status"]<4){
				$item_total = ($r["shop_item_pickup_price"] - $r["shop_item_pickup_discount"]) * $r["shop_item_quantity"];
				$total +=$item_total;
				$item_vat = 0;
				if ($this->charge_vat=="Yes"){
					if ($this->always_charge=="Yes"){
						$item_vat += number_format(($item_total/100) * $this->vat_rate,2) ;
					} else {
						if ($r["shop_item_vat"]==1){
							$item_vat += number_format(($item_total/100) * $this->vat_rate,2) ;
						}
					}
				}
				$totalvat += $item_vat;
				$basket_info .= "<stock identifier='".$r["shop_item_identifier"]."' price='".$r["shop_item_pickup_price"]."' quantity='".$r["shop_item_quantity"]."' discount='".$r["shop_item_pickup_discount"]."' item_total='".number_format($item_total,2)."' item_total_double='".round($item_total,2)."' item_vat_double='".$item_vat."' item_vat='".number_format($item_vat,2)."'><![CDATA[".$r["shop_item_title"]."]]></stock>";
				$c++;
				$bstatus=1;
			} else {
				$bstatus = 4;
			}
		}
		if($bstatus==4){
			$_SESSION["SHOP_BASKET_IDENTIFIER"]=-1;
			$this->create_basket(Array("override"=>0));
		} 
		if($basket_info!=""){
			$out  = "<module name=\"".$this->module_name."\" display=\"form\">";
			if($this->charge_vat=="Yes"){
				if($totalvat==0){
					$out .= "<form name='confirm' label='".LOCALE_SHOP_BASKET_CONTENTS."' method='post'>
					<stock_list subtotal='".number_format($total,2)."' total='".number_format($total,2)."' subtotal_double='".round($total,2)."' total_double='".round($total,2)."' number_of_items='$c'>
					<currency><![CDATA[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]]></currency>";
				} else {
					$out .= "<form name='confirm' label='".LOCALE_SHOP_BASKET_CONTENTS."' method='post'>
					<stock_list subtotal='".number_format($total,2)."' total='".number_format(($total + $totalvat),2)."' subtotal_double='".round($total,2)."' total_double='".round(($total + $totalvat),2)."' vat='".$this->vat_rate."' number_of_items='$c'>
					<currency><![CDATA[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]]></currency>";
				}
			} else {
				$out .= "<form name='confirm' label='".LOCALE_SHOP_BASKET_CONTENTS."' method='post'><stock_list subtotal='".number_format($total,2)."' total='".number_format($total,2)."' number_of_items='$c'>
				<currency><![CDATA[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]]></currency>";
			}
			$out .= $basket_info;
			$out .= "</stock_list></form>";
		} else {
			$out  = "<module name=\"".$this->module_name."\" display=\"TEXT\">";
			$out .= "<label show='1'><![CDATA[View Basket]]></label>";
			$out .= "<text class='contentpos'><![CDATA[
				Sorry you have not added any items to your shopping basket]]></text>";
		}
		$out .="</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * remove an item from the basket
    *************************************************************************************************************************/
	
	function shop_remove_item_from_basket($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",-1);
		$basket_identifier = $this->check_parameters($_SESSION,"SHOP_BASKET_IDENTIFIER",-1);
		$sql = "delete from shop_basket_items 
				where shop_basket_items.shop_item_client= $this->client_identifier and 
				shop_basket_items.shop_item_identifier = $identifier and
				shop_basket_items.shop_item_basket = $basket_identifier";
		$this->parent->db_pointer->database_query($sql);
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("url" => "_view-cart.php"));
		$this->exitprogram();
	}
	function shop_update_basket($parameters){
		$number_of_items = $this->check_parameters($parameters,"number_of_items");
		$basket_identifier = $this->check_parameters($_SESSION,"SHOP_BASKET_IDENTIFIER",-1);
		if ($number_of_items>0){
			for($index=1;$index<=$number_of_items;$index++){
				$old_qty = $this->check_parameters($parameters,"old_quantity".$index,0);
				$new_qty = $this->check_parameters($parameters,"new_quantity".$index,0);
				if ($old_qty != $new_qty){
					$item = $this->check_parameters($parameters,"item".$index,0);
					$sql = "select myitems.*, sum(purchased.shop_item_quantity) as purchased_quantity 
							from shop_basket_items as myitems
							left outer join shop_basket_items as purchased on purchased.shop_item_shop=1 and purchased.shop_item_stock_group = myitems.shop_item_stock_group and purchased.shop_item_client= myitems.shop_item_client and purchased.shop_item_identifier != myitems.shop_item_identifier
							where myitems.shop_item_basket='$basket_identifier' and myitems.shop_item_client = $this->client_identifier and myitems.shop_item_identifier='$item'
							group by purchased.shop_item_quantity";
					$found=0;
					$result		= $this->parent->db_pointer->database_query($sql);
					$amount_left= 0;
			        while($r = $this->parent->db_pointer->database_fetch_array($result)){
		        		$found				= 1;
						$purchased_quantity = $this->check_parameters($r,"purchased_quantity",0);
						$stock_id			= $r["shop_item_stock_id"];
		    	    }
//						$md_group			= $r["md_link_group_id"];
			        $this->parent->db_pointer->database_free_result($result);
					$sql = "select * from metadata_details where md_link_group_id = $stock_id and md_client= $this->client_identifier order by md_identifier desc";
					$result  = $this->parent->db_pointer->database_query($sql);
					$c=0;
                    while(($r = $this->parent->db_pointer->database_fetch_array($result)) && ($c==0)){
						$c=1;
						$amount_left		= $r["md_quantity"];
						$original_quantity	= $r["md_quantity"];
						$md_group			= $r["md_link_group_id"];
                    }
                    $this->parent->db_pointer->database_free_result($result);
					$update=0;
					$quantity_left =-1;
					if($original_quantity!=-1){
						if (($original_quantity - $purchased_quantity)<$new_qty){
							$new_qty = ($original_quantity - $purchased_quantity);
						}
					}
					$sql= "update shop_basket_items set shop_item_quantity='$new_qty' where shop_item_stock_id=$stock_id and shop_item_basket = $basket_identifier and shop_item_client = $this->client_identifier";
					$this->parent->db_pointer->database_query($sql);
				}
			}
		}
	}
	/*************************************************************************************************************************
    * move to the purchase screens for this basket
    *************************************************************************************************************************/
	function shop_purchase_basket($parameters){
		$page = $this->check_parameters($parameters,"page",1);
		$basket_identifier 		= $this->check_parameters($_SESSION,"SHOP_BASKET_IDENTIFIER",-1);
		$same_delivery_details	= $this->check_parameters($parameters, "same_delivery_details", "YES");
		$prev_page				= $this->check_parameters($parameters, "prev_page",0);
		$settings 				= $this->call_command("SHOP_GET_SETTINGS");
		/** Check if basket is valid and basket has items in it. If basket is empty then return a message. */
		/********************************************************/
		if ($basket_identifier != -1) {
			$sql = "select * from shop_basket_items 
						inner join shop_basket_container on shop_basket_container.shop_basket_identifier = shop_basket_items.shop_item_basket and shop_basket_client=shop_item_client
					where shop_item_client= $this->client_identifier and shop_basket_container.shop_basket_identifier = $basket_identifier";
			$result = $this->parent->db_pointer->database_query($sql);
			if ($this->call_command("DB_NUM_ROWS",array($result)) == 0){			
				$basket_is_empty = 1 ;				
			}
		}	
		else {
			$basket_is_empty = 1 ;
		}
		//print '</li>Basket id'.$basket_identifier.'  is empty'.$basket_is_empty.'</li>';
		if ($basket_is_empty == 1){
			
			$out  = "<module name=\"".$this->module_name."\" display=\"TEXT\">";
			$out .= "<label show='1'><![CDATA[View Basket]]></label>";
			$out .= "<text class='contentpos'><![CDATA[
				Sorry you have not added any items to your shopping basket]]></text>";			
			$out .="</module>";		
			return $out;
		}
		/********************************************************/
		$del_contact_details	= Array();
		$bill_contact_details	= Array();
		$content				= "";
		$errors					= Array();
		$label					= "";
		$sql= "select * from shop_basket_container where 
					shop_basket_identifier = $basket_identifier and 
					shop_basket_status < 4 and 
					shop_basket_client = $this->client_identifier
				";
        $result  				= $this->parent->db_pointer->database_query($sql);
		$bstatus				= -1;
		$alt					= "";
       	$bbill			 		= 0;
       	$bdel			 		= 0;
		$times_through			= 0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$bstatus = $r["shop_basket_status"];
        	$bbill	 = $r["shop_basket_bill_contact"];
        	$bdel	 = $r["shop_basket_delivery_contact"];
        }
		$this->parent->db_pointer->database_free_result($result);
		if($bbill==0 && $bdel==0){
			if($this->check_parameters($_SESSION,"SESSION_USER_IDENTIFIER",0) !=0){
				$bbill	= $this->check_parameters($_SESSION, "SESSION_CONTACT_IDENTIFIER", 0);
				$bdel	= $this->check_parameters($_SESSION, "SESSION_CONTACT_IDENTIFIER", 0);
			}
		}
		if ($bstatus==4) {
			$out ="<module name=\"".$this->module_name."\" display=\"entry\">";
			$out .="	<text><![CDATA[Sorry this basket has already been purchased]]></text>";
			$out .="</module>";	
			return $out;
		}
		if ($bstatus<4 && $prev_page==1){
			$parameters["next_command"]		= "RETURN_IDENTIFIER";
			$parameters["restrict_form"]	= "SHOP_";
			$parameters["results"] 			= "Array";
			$bill_contact_details 			= $this->call_command("CONTACT_SAVE",$parameters);
			if($bill_contact_details["errorCount"]!=0){
				$page=1;
				$bbill			= $bill_contact_details["id"];
				$bill_id		= $bill_contact_details["id"];
				$errors 		= $bill_contact_details["errors"];
				$times_through	= 1;
			} else {
				$bbill			= $bill_contact_details["id"];
				$bill_id		= $bill_contact_details["id"];
			}
		}
		if ($bstatus<4 && $prev_page==2){
			$parameters["next_command"]		= "RETURN_IDENTIFIER";
			$parameters["restrict_form"]	= "SHOP_";
			$parameters["results"] 			= "Array";
			$del_contact_details 			= $this->call_command("CONTACT_SAVE",$parameters);
			if($del_contact_details["errorCount"]!=0){
				$page=2;
				$bdel			= $del_contact_details["id"];
				$errors 		= $del_contact_details["errors"];
				$times_through	= 2;
			} else {
				$bdel			= $del_contact_details["id"];
				$delivery_id 	= $del_contact_details["id"];
			}
		}
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		if($basket_details["basket_total"]==0){
			$this->bill_and_del_same="Yes";
		}
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		if ($page==1 && $bstatus<4){
			$sql = "update 
						shop_basket_container 
					set shop_basket_status = 2 
					where 
						shop_basket_identifier = $basket_identifier and 
						shop_basket_client = $this->client_identifier
				";
			$result = $this->parent->db_pointer->database_query($sql);
			if($this->bill_and_del_same=="Yes"){
				$label="Shipping and Billing Address";
			} else {
				$label=LOCALE_SHOP_PURCHASE_BILLING_DETAILS;
			}
			if ($bbill > 0){
				$parameters["restrict_country"]=1;
				$parameters["form_restrict"]="SHOP_";
				$parameters["override_required"]=Array("contact_first_name", "contact_last_name", "contact_address", "contact_city", "contact_county", "contact_postcode");
				$parameters["contact_identifier"]=$bbill;
				$parameters["errors"]=$errors;
				$parameters["times_through"]=$times_through;
				// a parameter can be added to select different contact details than the registration form
			} else {
				$parameters["restrict_country"]=1;
				$parameters["form_restrict"]="SHOP_";
				$parameters["override_required"]=Array("contact_first_name", "contact_last_name", "contact_address", "contact_city", "contact_county", "contact_postcode");
				$parameters["errors"]=$errors;
				$parameters["times_through"]=$times_through;
			}
			$content = $this->call_command("CONTACT_FORM", $parameters); 
			if($this->bill_and_del_same=="Yes"){
				$content .= "<input type='hidden' name='same_delivery_details' value='YES'/>";
			}else{
				$content .= "<radio name='same_delivery_details' label='".LOCALE_SHOP_SAME_FOR_DELIVERY."'>".$this->gen_options(Array("YES","NO"), Array(LOCALE_YES, LOCALE_NO), $same_delivery_details)."</radio>";
			}
			$alt = 'Next';
		} else if ($page==2 && $bstatus<4){
			$same_delivery_details	= $this->check_parameters($parameters,"same_delivery_details",	'NO');
//			$identifier			 	= $this->check_parameters($parameters,"same_delivery_details",	'NO');
			if ($same_delivery_details == 'NO'){
				$sql = "
					update 
						shop_basket_container 
					set shop_basket_bill_contact = ".$bill_id."
					where 
						shop_basket_status = 2  and 
						shop_basket_identifier = $basket_identifier and 
						shop_basket_client = $this->client_identifier
				";
				$result = $this->parent->db_pointer->database_query($sql);
				$label=LOCALE_SHOP_PURCHASE_DELIVERY_DETAILS;
				if ($bdel>0){
					if ($bdel==$bill_id){
						$bdel=-1;
					}
					$parameters["restrict_country"]=1;
					$parameters["form_restrict"]="SHOP_";
					$parameters["override_required"]=Array("contact_first_name", "contact_last_name", "contact_address", "contact_city", "contact_county", "contact_postcode");
					$parameters["contact_identifier"]=$bdel;
					$parameters["errors"]=$errors;
					$parameters["times_through"]=$times_through;
					// a parameter can be added to select different contact details than the registration form
					$content = $this->call_command("CONTACT_FORM", $parameters); 
				} else {
					$content = $this->call_command("CONTACT_FORM", Array(
						"restrict_country"	=> 1,
						"form_restrict"		=> "SHOP_",
						"override_required"	=> Array("contact_first_name", "contact_last_name", "contact_address", "contact_city", "contact_county", "contact_postcode"),
						"errors"			=> Array(),
						"times_through" 	=> 0
					)); 
				}
				if($this->can_pay_by_invoice=="Yes"){
//					$content .= "<input type='hidden' name='payby' value='$payby'/>";
				}
				$alt = 'Next';
			} else {
				$sql = "
					update 
						shop_basket_container 
					set 
						shop_basket_status = 3,
						shop_basket_bill_contact = $bill_id, 
						shop_basket_delivery_contact = $bill_id 
					where 
						shop_basket_status < 4  and 
						shop_basket_identifier = $basket_identifier and 
						shop_basket_client = $this->client_identifier
				";
				$delivery_id =$bill_id;
				$result = $this->parent->db_pointer->database_query($sql);
				$page=3;
			}
		}
		if ($page==3 && $bstatus<4){
			$actual_page = $this->check_parameters($parameters,"page");
			$payby= $this->check_parameters($parameters,"payby","cc");
			if ($actual_page==3){
				// delivery details are different
				$delivery_override = $this->check_parameters($_SESSION,"SHOP_CONTACTS",-1);
				if ($delivery_override==-1){
					$_SESSION["SHOP_CONTACTS"] = -1;
//					$delivery_id = $this->call_command("CONTACT_SAVE",$parameters);
					$sql = "
							update 
								shop_basket_container 
							set 
								shop_basket_delivery_contact = $delivery_id 
							where 
								shop_basket_status < 4  and 
								shop_basket_identifier = $basket_identifier and 
								shop_basket_client = $this->client_identifier
						";
					$result = $this->parent->db_pointer->database_query($sql);
				} 
			}
			$label=LOCALE_SHOP_PURCHASE_PAYMENT_DETAILS;
			$basket = $this->get_order(Array("basket_identifier" => $basket_identifier, "return_type" => Array())); 
			/*************************************************************************************************************************
			* 
			*************************************************************************************************************************/
//				$requestinvoice			= $this->check_parameters($parameters,"requestinvoice",			'0');
//				$payby					= $this->check_parameters($parameters,"payby",					'');
			/*************************************************************************************************************************
			* 
			*************************************************************************************************************************/
			$content = "";
			$content ="<module name=\"".$this->module_name."\" display=\"form\">";
			$content .="<form name=\"".$this->module_name."_form\" label=\"Confirm Details\">";
			$content .=$basket["output"];
			$content .="</form>";
			$content .="</module>";
			if ($basket["basket_total"]==0) {
				$content .="<module name=\"".$this->module_name."\" display=\"form\"><form name=\"".$this->module_name."_form_order\" label=\"Order this item now\">";
				$content .="<input type=\"hidden\" name=\"basket_identifier\" value=\"$basket_identifier\"/>";
				$content .="<input type=\"hidden\" name=\"command\" value=\"SHOP_INVOICE_PROCESS\"/>";
				if ($this->check_parameters($settings, "ss_can_request_invoice", 0)==1  && $basket_details["basket_total"]>0 ){
					$content .="<checkboxes type='vertical' name=\"requestinvoice\" label='Would you like to recieve a paper invoice for this purchase'><option value=\"1\">Yes</option></checkboxes>";
				}
				$content .="<input type=\"submit\" iconify=\"CONFIRM\" value=\"".LOCALE_CONFIRM."\"/></form>";
				$content .="</module>";
			} else {
				if ($this->can_pay_by_invoice=="Yes"){
					$content .="<module name=\"".$this->module_name."\" display=\"form\"><form name=\"".$this->module_name."_form_order\" label=\"Pay by Cheque\">";
					$content .="<input type=\"hidden\" name=\"basket_identifier\" value=\"$basket_identifier\"/>";
					$content .="<input type=\"hidden\" name=\"command\" value=\"SHOP_INVOICE_PROCESS\"/>";
//				$content .="<text><![CDATA[You have been given the option to like to pay by cheque, would you like to be invoiced for this purchase.]]></text>";
					if ($this->check_parameters($settings, "ss_can_request_invoice", 0)==1 && $basket_details["basket_total"]>0 ){
						$content .="<checkboxes type='vertical' name=\"requestinvoice\" label='Would you like to recieve a paper invoice for this purchase'><option value=\"1\">Yes</option></checkboxes>";
					}
					$content .="<input type=\"submit\" iconify=\"CONFIRM\" value=\"Purchase by Cheque\"/></form>";
					$content .="</module>";
				}
				$content .= $this->call_command("PAYGATE_REQUEST_PAYMENT",Array("__SYS_REFERENCE__"=>$basket_identifier));
			}
			return $content;
		}
		if ($page==4 && $bstatus==2){
			$payment_option = $this->check_parameters($parameters,"payment_option",1);
			$sql = "
				update 
					shop_basket_container 
				set 
					shop_basket_status = 3,
					shop_basket_payment = $payment_option
				where 
					shop_basket_status < 4 and 
					shop_basket_identifier = $basket_identifier and 
					shop_basket_client = $this->client_identifier
			";
			$result = $this->parent->db_pointer->database_query($sql);
			$label=LOCALE_SHOP_PURCHASE_PAYMENT_DETAILS_RECIEVED;
			$sql = "Select 
						* 
					from 
						shop_basket_container 
					where 
						shop_basket_status = 3  and 
						shop_basket_identifier = $basket_identifier and 
						shop_basket_client = $this->client_identifier
				";
			$result = $this->parent->db_pointer->database_query($sql);
			while ($r = $this->parent->db_pointer->database_fetch_array($result)){
				$basket_date = $this->check_parameters($r,"shop_basket_date",-1);
				$basket_id = $this->check_parameters($r,"shop_basket_identifier",-1);
			}			
			$ref = strtotime($basket_date)."_".$this->client_identifier."_".$basket_id;
			$content = "<module name='shopping' display='confirm'>
							<form label='".LOCALE_SHOP_PURCHASE_PAYMENT_DETAILS_RECIEVED."'>
								<text><![CDATA[".LOCALE_SHOP_PURCHASE_COMPLETE."]]></text>
								<text><![CDATA[Your order reference number is #$ref]]></text>
								<text><![CDATA[<a href='".$this->parent->script."'>".LOCALE_CANCEL."</a>]]></text>
							</form>
						</module>";
		} 
		if ($page==1 || $page==2 || $page==3){
			$out  = "<module name=\"".$this->module_name."\" display=\"form\">";
			$out .= "<form name='confirm' label='".$label."' method='post' action='_purchase-cart.php'>";
			$out .= "<input type='hidden' name='command' value='SHOP_PURCHASE_BASKET'/>";
			$out .= "<input type='hidden' name='page' value='".($page+1)."'/>";
			$out .= "<input type='hidden' name='prev_page' value='".($page)."'/>";
			
			$out .= $content;
			$out .= "<input type='submit' iconify='SAVE' value='$alt'/>";
			$out .= "</form>";
//			$out .= "<form name='confirm' label='' method='get' action='_view-cart.php'><input type='submit' value='View basket' iconify='VIEW'/></form>";
			$out .="</module>";
		} else {
			$out = $content;
		}
		return $out;
	}
	
	/*************************************************************************************************************************
    * clean up the baskets that are older than 24hrs and not purchased
    *************************************************************************************************************************/
	function shop_basket_clean_up(){
		$now = date("Y/m/d H:i:s",mktime(date("H"),date("i"),date("s"),date("m"),date("d"),date("Y"))-(24*3600));
		$sql ="select * FROM shop_basket_container where shop_basket_status<4 and shop_basket_container.shop_basket_date  < '".$now."' and shop_basket_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$bids = array();
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$bids[count($bids)] = $r["shop_basket_identifier"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$sql ="delete FROM shop_basket_container where shop_basket_status<4 and shop_basket_container.shop_basket_date  < '".$now."' and shop_basket_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		$sql ="delete FROM shop_basket_items where shop_item_basket in (".join(", ",$bids).")  and shop_item_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
	}
	
	/*************************************************************************************************************************
    *
    *************************************************************************************************************************/
	function create_client_details($parameters){
		$client_identifier = $this->check_parameters($parameters,"client_identifier",-1);
		/**
		* generate default inserts as required by the client
		*/
		$identifier = $this->getUID();
		$this->call_command("DB_QUERY",array("insert into shop_settings (ss_identifier, ss_charge_vat, ss_vat, ss_always_charge, ss_client) values ('$identifier', '1', '17.5', '0', $client_identifier)"));
	}

	function blank_cart(){
		$_SESSION["SHOP_BASKET_IDENTIFIER"]="__NOT_FOUND__";
	}
	/*************************************************************************************************************************
    *										S P E C I A L   P A G E S
    *************************************************************************************************************************/

	/*************************************************************************************************************************
    * builds special pages for the information directory
	*
	* <strong>Note::</strong> only creates a2z pages when display layout is = 2
    *
    * @param string path on site to the file
    * @param integer id of information directory this will use
    * @param string path on site to the file
	* @param Integer $summary_layout
    *************************************************************************************************************************/
	function make_special(){
		$root 				= $this->parent->site_directories["ROOT"];
		$module_directory	= $this->parent->site_directories["MODULE_DIR"];
		$max 				= count($this->special_webobjects);

		foreach($this->special_webobjects as $index => $value){
			if($value["available"]==1){
				$out ="<"."?php
					\$script_file	= dirname(\$_SERVER[\"SCRIPT_FILENAME\"]);
					\$site_root		= \"$root\";
					\$script		= \"/index.php\";
\$mode		 = \"EXECUTE\";
\$command	 = \"".$value["wo_command"]."\";
\$fake_title = \"".$value["label"]."\";
require_once \"".$root."/admin/include.php\";
require_once \"\$module_directory/included_page.php\";
?".">";
				$file_to_use = $root."/".$value["file"];
				$fp = fopen($file_to_use,"w");
				fwrite($fp, $out);
				fclose($fp);
				$old_umask = umask(0);
				@chmod($file_to_use,LS__FILE_PERMISSION);
				umask($old_umask);
			}
		}
	}
	/*************************************************************************************************************************
    * remove special pages form the specified menu url
	*
	* <strong>Note::</strong> only creates a2z pages when display layout is = 2
    *
    * @param string path on site to the special pages to remove
    *************************************************************************************************************************/
	function remove_special($ml_url, $info_dir){
		$root 				= $this->parent->site_directories["ROOT"];
		$module_directory	= $this->parent->site_directories["MODULE_DIR"];
		$max 				= count($this->special_webobjects);
		foreach($this->special_webobjects as $index => $value){
			$file_to_use = $root."/".$value["file"];
			@unlink($file_to_use);
		}
	}
	
	
	/*************************************************************************************************************************
    *make sure its numeric
    *************************************************************************************************************************/
	function make_numeric($number_string){
		$numeric = preg_replace(
					Array("'\D.?\D'","'\D'"),
					Array('',''),
					$number_string);
		return $number_string;//$numeric;
	}
	
	/*************************************************************************************************************************
    * display the list of countries to add to shipping matrix
    *************************************************************************************************************************/
	function display_weight_setup($parameters){
		$array_of_weights	= Array();
		$array_of_countries	= Array();
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql = "SELECT * FROM country_area_lookup";
		$result  = $this->parent->db_pointer->database_query($sql);
		$regions = "<regions>";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$regions .= "<region code='".$r["cal_code"]."'><![CDATA[".$r["cal_label"]."]]></region>";
        }
		$regions .= "</regions>";
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql="select * from shop_settings where ss_client = $this->client_identifier and ss_identifier = $this->shop_setting_id";
		$result  = $this->parent->db_pointer->database_query($sql);
		$identifier =-1;
		$same_del	= 1;
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$same_del = $r["ss_delivery_always_same"];
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$weight_list = "";
				$sql = "select * from shop_weight_matrix_list where sw_client = $this->client_identifier order by sw_max_kg";
		$result  = $this->parent->db_pointer->database_query($sql);
		$end = "";
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if($r["sw_max_kg"]==-1){
				$end = "<weight_list><![CDATA[".$r["sw_max_kg"]."]]></weight_list>";
			}else {
				$weight_list .= "<weight_list><![CDATA[".$r["sw_max_kg"]."]]></weight_list>";
			}
		}
		$weight_list .= $end;
		$this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * 
        *************************************************************************************************************************/
		$sql = "select * from shop_weight_matrix 
					left outer join country_lookup on cl_identifier = swm_country 
					inner join country_area_lookup on cal_code = swm_region
				where swm_client = $this->client_identifier order by swm_region, swm_country, swm_kg";
        $result  = $this->parent->db_pointer->database_query($sql);
		$prev_country	= "";
		$weights		= "$weight_list";
		$end = "";
		$region="";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$c = $r["swm_country"];
			if("$c"=='18446744073709551615'){
				$c=-1;
			}
			$c = $r["swm_region"]."::".$c;
			if ($prev_country != $c){
				if ($weights != "$weight_list"){
					$weights .= "$end</weights>";
				}
				$prev_country	= $c;
				$region			= $this->check_parameters($r,"swm_region");
				$country	  = $this->check_parameters($r,"cl_country",$this->check_parameters($r,"cal_label"));
				if (!in_array($prev_country,$array_of_countries)) {
					$array_of_countries[count($array_of_countries)] = $prev_country;
				}
				$areacode	 = $r["swm_region"];
				$weights .= "<weights><country areacode='$areacode' identifier='$prev_country'><![CDATA[$country]]></country>";
			}
			if($r["swm_kg"]==-1){
				$end = "<weight kg='".$r["swm_kg"]."' price='".$r["swm_price"]."' />";
			}else {
				$weights .= "<weight kg='".$r["swm_kg"]."' price='".$r["swm_price"]."' />";
			}
        }
		if ($weights!="$weight_list"){
			$weights .="$end</weights>";
		}
        $this->parent->db_pointer->database_free_result($result);
		$weights .= $regions;
		/*************************************************************************************************************************
        * get list of countries
        *************************************************************************************************************************/
		$sql = "SELECT *
					FROM country_lookup
				left outer join country_area_lookup on cal_code = cl_area
				order by cal_label, cl_country";
        $result		= $this->parent->db_pointer->database_query($sql);
		$prev		= "";
		$country	= "";
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	if($prev != $r["cal_label"]){
				if($prev!=""){
					$country .= "</options>";
				}
				$country .= "<options module='".$r["cal_label"]."' tag='".$r["cal_label"]."'>";
				$country .= "<option value ='".$r["cal_code"]."::-1'";
			
			if (in_array($r["cl_area"]."::-1", $array_of_countries)) {
				$country.=" selected='true'";
			}
			$country .= ">All ".$r["cal_label"]."</option>";
				$prev = $r["cal_label"];
			}
			$country .= "<option value ='".$r["cal_code"]."::".$r["cl_identifier"]."'";
			
			if (in_array($r["cl_area"]."::".$r["cl_identifier"],$array_of_countries)) {
				$country.=" selected='true'";
			}
			$country .= "><![CDATA[".$r["cl_country"]."]]></option>";
        }
		if ($country != ""){
			$country .= "</options>";
		}
        $this->call_command("DB_FREE", Array($result));
		$out  = "<module name=\"".$this->module_name."\" display=\"form\"><page_options>
					".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","ENGINE_SPLASH",LOCALE_CANCEL))."
					<header><![CDATA[".MANAGE_SHOP_DELIVERY_SETTINGS."]]></header></page_options>\n";
		$out .= "	<form name='confirm' label='Shipping weight to country matrix' method='post' action='_purchase-cart.php'>";
		$out .= "		<input type='hidden' name='command' value='SHOP_WEIGHT_SAVE'/>";
		$out .= "		<input type='hidden' name='identifier' value='$identifier'/>";
		$out .= "		<page_sections>";
		$out .="			<section name='weightSetup1' label='".MANAGE_DELIVER_TO."'>";
		$out .="				<radio name='ss_delivery_always_same' label='".LOCALE_SHOP_SAME_DELIVERY."'><option value='0'";
		if($same_del==0){
			$out .= " selected='true'";
		}
		$out .= ">No</option><option value='1'";
		if($same_del==1){
			$out .= " selected='true'";
		}
		$out .= ">Yes</option></radio>";
		$out .= "				<checkboxes name='country_selection' label='Select the countries you will deliver too' type='horizontal' onclick='toggle_country'>$country</checkboxes>"; 
		$out .= "			</section>";
		$out .= "			<section name='weightSetup2' label='".MANAGE_CARRIAGE_COSTS."'><weight_details>". $weights ."</weight_details>";
		$out .= "			</section>";
		$out .= "		</page_sections>";
		$out .= "		<input type='submit' iconify='SAVE' value='".SAVE_DATA."'/>";
		$out .= "	</form>";
		$out .="</module>";
		return $out;
	}	
	/*************************************************************************************************************************
    * save the shipping matrix
	*
	* save the matrix to the database
    *************************************************************************************************************************/
	function save_weight_setup($parameters){
		$cid_list					= $this->check_parameters($parameters,"country_identifier"		, Array());
		$identifier					= $this->check_parameters($parameters,"identifier"				, -1);
		$weight_list				= $this->check_parameters($parameters,"weight"					, Array());
		$ss_delivery_always_same	= $this->check_parameters($parameters,"ss_delivery_always_same"	, 1);
		$cAreaCountries 			= count($cid_list);
		$cweights					= count($weight_list);
		
		if($this->shop_setting_id==-1){
			$identifier = $this->getUID();
			$this->shop_setting_id=$identifier;
			$sql = "insert into shop_settings (ss_identifier, ss_charge_vat, ss_vat, ss_always_charge, ss_client, ss_delivery_always_same) values ('$identifier', '1', '17.5', '0', $this->client_identifier, '$ss_delivery_always_same')";
			$this->parent->db_pointer->database_query($sql);
		} else {
			$sql = "update shop_settings set ss_delivery_always_same='$ss_delivery_always_same' where ss_identifier = '$this->shop_setting_id' and ss_client=$this->client_identifier)";
			$this->parent->db_pointer->database_query($sql);
		}
		$sql = "delete from shop_weight_matrix where swm_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		$kg = Array();
		for ($index = 0; $index < $cAreaCountries ; $index++){
			for ($wIndex = 0; $wIndex < $cweights ; $wIndex++){
				$swm_price		= $this->check_parameters($parameters,"weight_".$cid_list[$index]."_".$weight_list[$wIndex]);
				$swm_identifier = $this->getUid();
				$swm_kg			= $weight_list[$wIndex];
				if(!in_array($swm_kg,$kg)){
					$kg[count($kg)] = $swm_kg;
				}
				$l = split("::",$cid_list[$index]);
				$swm_country	= $l[1];
				$swm_region		= $l[0];
				
				$sql = "insert into shop_weight_matrix 
							(swm_identifier, swm_client, swm_price, swm_kg, swm_country, swm_region) 
						values
							($swm_identifier, $this->client_identifier, $swm_price, $swm_kg, $swm_country, '$swm_region')";
				$this->parent->db_pointer->database_query($sql);
			}
		}
		$sql = "delete from shop_weight_matrix_list where sw_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		$sql = "select distinct swm_kg from shop_weight_matrix where swm_client = $this->client_identifier order by swm_kg";
        $result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
    		$sql = "insert into shop_weight_matrix_list (sw_client , sw_max_kg) values ($this->client_identifier, ".$r["swm_kg"]." )";
			$this->parent->db_pointer->database_query($sql);
        }
        $this->parent->db_pointer->database_free_result($result);
		
		
		$out  = "<module name=\"".$this->module_name."\" display=\"form\"><page_options>
					".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","ENGINE_SPLASH",LOCALE_CANCEL))."
					<header><![CDATA[".MANAGE_SHOP_DELIVERY_SETTINGS." - Confirm]]></header></page_options>\n";
		$out .= "	<form name='confirm' label='".MANAGE_SHOP_DELIVERY_SETTINGS_CONFIRM_LABEL."' method='post' action='_purchase-cart.php'>";
		$out .= "	<text><![CDATA[".MANAGE_SHOP_DELIVERY_SETTINGS_CONFIRM_MSG."]]></text>";
		$out .= "	</form>";
		$out .="</module>";
		return $out;
	}	
	/*************************************************************************************************************************
    * get the cost to transport basket to country
	* 
	* country is the cl_identifier field in the country_lookup table and is an integer
	* weigth is the weight in kg 0.231 is 231 grams and 1 = 1000 grams
	* @param Array (keys are "country" and "weight"
	* @uses Array("country" => 5 , "weight"=>5.56)
	* @return Double the cost of shipping weight against country (-1 is failed to return costs
    *************************************************************************************************************************/
	function get_cost($parameters){
		$cid		= $this->check_parameters($parameters,"country",-1);
		$weight		= $this->check_parameters($parameters,"weight",-1);
		if($cid==-1 || $weight==-1 || $weight==0){
			return 0;
		} 
//		$sql = "select * from shop_weight_matrix where swm_client = $this->client_identifier and swm_country = $cid order by swm_kg";
		/*************************************************************************************************************************
        * get the shipping matrix
        *************************************************************************************************************************/
		$sql = "select * from shop_weight_matrix where swm_client = $this->client_identifier order by swm_region, swm_country, swm_kg";
	    $result  = $this->parent->db_pointer->database_query($sql);
		$prices= Array();
		$previous_country=-2;
		$previous_region = "";
		$index=0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$country	= $r["swm_country"];
			$region		= $r["swm_region"];
			if($country==-1 || $country=="18446744073709551615"){
				$country=-1;
			}
			if ($previous_country != $country || $previous_region!=$region){
				$previous_country	= $country;
				$previous_region	= $region;
				$index = count($prices);
				$prices[$index] = Array("region"=>$r["swm_region"], "country"=>$country, "matrix" => Array());
			}
			$prices[$index]["matrix"][count($prices[$index]["matrix"])] = Array("kg" => $r["swm_kg"], "price" => $r["swm_price"]);
	    }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * get country of delivery's region incase we need to retrieve the "All region" cost
        *************************************************************************************************************************/
		$sql = "SELECT * FROM `country_lookup` where cl_identifier = $cid";
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$region = $r["cl_area"];// retrieve the area code (AF = Africa , EU = Europe, ...
        }
        $this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * go through the prices and look for region and country id and mark found variable with index in price matrix if found
        *************************************************************************************************************************/
		$found = -1; // indexrange is (0 .. N-1)
		for($i=0, $m=count($prices); $i < $m; $i++){
			if($prices[$i]["region"] == $region && $prices[$i]["country"] == $cid){
				$found = $i;
			}
		}
		if($found==-1){ // check for region being set as specific country not found
			for($i=0, $m=count($prices); $i < $m; $i++){
				if($prices[$i]["region"] == $region && $prices[$i]["country"] == -1){
					$found = $i;
				}
			}
		}
		if($found==-1){
			return 0; // no weight cost found
		}
		/*************************************************************************************************************************
        * examine the price matrix at index $found and retrieve the weight cost start at index 1 of the matrix to N-1 
		* if not found then return index 0
        *************************************************************************************************************************/
		$number_of_matrix_indexes = count($prices[$index]["matrix"]);  // number of entries in the matrix
		if($number_of_matrix_indexes==0){
			return 0; // not weight matric found
		}
		for($i=1;$i<$number_of_matrix_indexes;$i++){
			if ($prices[$found]["matrix"][$i]["kg"]>$weight){
				return $prices[$found]["matrix"][$i]["price"];
			}
		}
		return $prices[$found]["matrix"][0]["price"];
	}
	
	/*************************************************************************************************************************
    * retrieve order details for the "basket_identifier" key
	*
	* @return Array keys are ("bstatus" => Integer, "bill" => Integer, "del" => Integer, "basket_total" => Integer, "items" => Array, "output" => String, "payment" => Integer);
    *************************************************************************************************************************/
	function get_order($parameters){
		$basket_identifier = $this->check_parameters($parameters,"basket_identifier",-1);
		
		$payment=0;
		if ($basket_identifier==-1){
			return Array("bstatus" => 0, "invoice_sent_date"=>"0000-00-00 00:00:00", "user_access"=>Array(), "requireinvoice" => 0, "bill" => -1, "del" => -1, "basket_total" => 0, "items"=>Array(), "output"=>"", "payment"=>0);
		}
		$settings = $this->call_command("SHOP_GET_SETTINGS");

		$items					= Array();
		$basket_vat 			= 0;
       	$basket_weight			= 0;
		$shop_basket_session	= -1;
       	$basket_total	 		= 0;
		$bstatus				= 0;
		$bill					= -1;
		$del					= -1;
		$requireinvoice			= 0;
		$output					= "";
		$invoice_sent_date		= "";
		$sql = "select * from shop_basket_container
					left outer join shop_basket_items on shop_basket_identifier = shop_item_basket and shop_basket_client=shop_item_client
				where shop_basket_identifier = $basket_identifier and shop_basket_client = $this->client_identifier";
        $result  = $this->parent->db_pointer->database_query($sql);
		$basket_discount=0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$pos = count($items);
			$items[$pos] = Array(
				"label"			=> $r["shop_item_title"],
				"price"			=> $r["shop_item_pickup_price"],
				"discount"		=> $r["shop_item_pickup_discount"],
				"quantity"		=> $r["shop_item_quantity"],
				"weight"		=> $r["shop_item_weight"],
				"vat"			=> $r["shop_item_vat"],
				"item_vat"		=> 0,
				"item_total"	=> 0,
				"item_weight"	=> 0
			);
			$items[$pos]["item_total"]	= (($r["shop_item_pickup_price"] - $r["shop_item_pickup_discount"]) * $r["shop_item_quantity"]);
			$items[$pos]["item_weight"] = ($items[$pos]["weight"] * $items[$pos]["quantity"]);
			$basket_discount += $r["shop_item_pickup_discount"];
			if ($this->charge_vat=="Yes"){
				if ($this->always_charge=="Yes"){
					$items[$pos]["item_vat"] = number_format(($items[$pos]["item_total"]/100) * $this->vat_rate,2) ;
				} else {
					if ($items[$pos]["vat"] == 1){
						$items[$pos]["item_vat"] = number_format(($items[$pos]["item_total"]/100) * $this->vat_rate,2) ;
					}
				}
			}
			$basket_vat 	+= $items[$pos]["item_vat"];
        	$basket_total	+= $items[$pos]["item_total"];
        	$basket_weight	+= $items[$pos]["item_weight"];
			
			$bstatus				= $r["shop_basket_status"];
			$bill					= $r["shop_basket_bill_contact"];
			$del					= $r["shop_basket_delivery_contact"];
			$payment				= $r["shop_basket_payment"];
			$requireinvoice			= $r["shop_basket_requires_invoice"];
			$shop_basket_session	= $r["shop_basket_session"];
			$invoice_sent_date		= $r["shop_basket_invoice_sent_date"];
		}
		if($invoice_sent_date!=""){
			$invoice_sent_date = $this->libertasGetDate("l, dS M Y H:i:s", strtotime($invoice_sent_date));
		}
        $this->parent->db_pointer->database_free_result($result);
		$sql ="SELECT *
				FROM user_access
				inner join shop_basket_container on shop_basket_session = user_access_session_identifier  and shop_basket_client = user_access_client
				inner join available_languages on user_access_accept_language = language_code
				left outer join user_access_ip_lookup on access_ip = user_access_ip_address
				where user_access_client= $this->client_identifier and user_access_session_identifier = '$shop_basket_session'";
		$result  = $this->parent->db_pointer->database_query($sql);
		$UA = Array();
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$UA["uai"]				= $r["user_access_identifier"];
        	$UA["ip"] 				= $r["user_access_ip_address"];
        	$UA["language_label"]	= $r["language_label"];
			$UA["language_flag"]	= strtolower($this->check_parameters($r,"language_code","N.A."));
			$UA["country_flag"]		= strtolower($this->check_parameters($r,"access_country","N.A."));
			if (strlen($UA["language_flag"])==5){
				$UA["language_flag"] = substr($UA["language_flag"],3,2);
			}
			$UA["country_label"] 	= $this->check_parameters($r,"cl_country","N.A.");
				$UA["flag"] = "/libertas_images/icons/flags/".substr($r["language_code"],3,2).".png";
        }
        $this->parent->db_pointer->database_free_result($result);
		$required = Array("contact_name", "contact_address", "contact_country", "contact_postcode"	, "contact_telephone", "contect_fax", "contact_email");
		$bill_details		= $this->call_command("CONTACT_GET_DETAILS", Array("identifier"=>$bill	, "required" => $required, "restrict_country"=>1, "form_restrict"=>"SHOP_"));
		if($this->bill_and_del_same!="Yes"){
			$delivery_details	= $this->call_command("CONTACT_GET_DETAILS", Array("identifier"=>$del	, "required" => $required, "restrict_country"=>1, "form_restrict"=>"SHOP_"));
		} else {
			$delivery_details	= $bill_details;
		}
		$table="";
		$totalw =0;
		$m=count($items);
		for($i=0;$i<$m;$i++){
			$table .="<tr>";
			$table .="<td>".$items[$i]["label"]."</td>";
			if($items[$i]["price"]==0){
				$table .="<td align='right'>Free</td>";
			} else {
				$table .="<td align='right'>[[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]] ".number_format($items[$i]["price"],2)."</td>";
			}
			if($basket_discount>0){
			$table .="<td align='right'>".number_format($items[$i]["discount"],2)."</td>";
			}
			$table .="<td align='right'>".$items[$i]["quantity"]."</td>";
			if($basket_vat>0){
//				$table .="<td align='right'>".number_format($items[$i]["item_vat"],2)."</td>";
			}
			if($basket_weight>0){
//			$table .="<td align='right'>".number_format($items[$i]["item_weight"],2)."</td>";
			}
			if($items[$i]["item_total"]==0){
				$table .="<td align='right'>Free</td>";
			} else {
				$table .="<td align='right'>[[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]] ".number_format($items[$i]["item_total"],2)."</td>";
			}
			$table .="</tr>";
		}
		$stable = "<table style='width:100%' cellspacing='0' cellpadding='3' summary='contents of basket'>";
		$stable .="<tr>";
		$stable .="<th align='left'>Item</th>";
		$stable .="<th class='classright' align='right'>Price [[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]]</th>";
		if($basket_discount>0){
			$stable .="<th class='classright' align='right'>Discount</th>";
		}
		$stable .="<th class='classright' align='right'>Quantity</th>";
		if($basket_vat>0){
//			$stable .="<th class='classright'>VAT</th>";
		}
		if($basket_weight>0){
	//		$stable .="<th class='classright'>Weigth (kg)</th>";
		}
		$stable .="<th class='classright' align='right'>Cost</th>";
		$stable .="</tr>";
		$table = $stable.$table;
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
			$shipping_cost = $this->get_cost(Array("country"=>$country, "weight"=>$basket_weight));
		}
		if($basket_discount>0){
			$cspan = 4;
		} else {
			$cspan = 3;
		}
		if($shipping_cost>0){
			$table .="	<tr>
							<td align='right' colspan='$cspan'><strong>Delivery Charge</strong></td>";
			if($basket_vat>0){
//				$table .="<td align='right'></td>";
			}
			//<td align='right'>".number_format($basket_weight,2)."</td>
			$table .="<td align='right'>[[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]] ".number_format(round($shipping_cost,2),2)."</td>
						</tr>";
		} else {
			$shipping_cost=0;
		}
		$basket_total = $basket_total+$shipping_cost;
		if($basket_total!=0){
			$table .="	<tr>
							<td align='right' colspan='$cspan'><strong>Sub Total</strong></td>";
			$table .="<td align='right'>[[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]] ".number_format($basket_total,2,'.','')."</td></tr>";
		}
		if($this->vat_rate!=0 && $this->vat_rate!="" && $basket_vat!=0){
			$table .="	<tr>
							<td align='right' colspan='$cspan'><strong>Vat</strong> ".number_format($this->vat_rate,2)."%</td>";
			$table .="<td align='right'>[[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]] ".number_format($basket_vat,2,'.','')."</td></tr>";
//			$basket_total += number_format($basket_total+$basket_vat,2);
		}
		if($basket_total+$basket_vat!=0){
		$table .="<tr>";
		$table .="<td colspan='$cspan' align='right'><strong>Total</strong></td>";
		$table .="<td align='right'>[[".strtolower($this->check_parameters($settings,"ss_currency","GBP"))."]] ".number_format(round($basket_total+$basket_vat,2),2)."</td>";
		$table .="</tr>";
		}
		$table.="</table>";
		$details = "<text><![CDATA[<strong>You ordered the following items</strong><br/>$table]]></text>";

//$bill
//$del	
		if($this->parent->module_type=="website"){
			if($this->bill_and_del_same=="Yes" || $del==$bill){
				$details .= "<text><![CDATA[<p><strong>Billing/Delivery details</strong></p>".$bill_details["text"]."]]></text>";
			} else {
				$details .= "<text><![CDATA[<table class='width100percent'><tr><td><p><strong>Billing details</strong></p>".$bill_details["text"]."</td><td>";
				$details .= "<p><strong>Delivery details</strong></p>".$delivery_details["text"]."</td></tr></table>]]></text>";
			}
		} else {
			if($this->bill_and_del_same=="Yes" || $del==$bill){
				$details .= "<text><![CDATA[<p><strong>Billing/Delivery details</strong></p>".$bill_details["text"]."";
				if ($this->parent->script=="admin/index.php"){
					$details .= "<p><a href='admin/index.php?command=CONTACT_FORM&amp;contact_identifier=$bill&amp;embed=0&amp;form_restrict=SHOP_&amp;restrict_country=1&amp;ncm=SHOP_PROCESS_ORDER&amp;nci=$basket_identifier'>Edit contact details</a></p>";
				}
				$details .= "]]></text>";
			} else {
				$details .= "<text><![CDATA[<table class='width100percent'><tr><td><p><strong>Billing details</strong></p>".$bill_details["text"];
				if ($this->parent->script=="admin/index.php"){
					$details .= "<p><a href='admin/index.php?command=CONTACT_FORM&amp;contact_identifier=$bill&amp;embed=0&amp;restrict_country=1&amp;form_restrict=SHOP_&amp;ncm=SHOP_PROCESS_ORDER&amp;nci=$basket_identifier'>Edit billing details</a></p>";
				}
				$details .= "</td><td>";
				$details .= "<p><strong>Delivery details</strong></p>".$delivery_details["text"];
				if ($this->parent->script=="admin/index.php"){
					$details .= "<p><a href='admin/index.php?command=CONTACT_FORM&amp;contact_identifier=$del&amp;embed=0&amp;form_restrict=SHOP_&amp;restrict_country=1&amp;ncm=SHOP_PROCESS_ORDER&amp;nci=$basket_identifier'>Edit delivery details</a></p>";
				}
				$details .= "</td></tr></table>]]></text>";
			}
		}
		return Array("invoice_sent_date" => $invoice_sent_date, "user_access"=>$UA, "bstatus" => $bstatus, "requireinvoice"=>$requireinvoice, "bill" => $bill_details, "del" => $delivery_details, "basket_total" => $basket_total+$basket_vat, "items"=>$items, "output"=>$details, "payment"=>$payment);
	}
	/*************************************************************************************************************************
    * set an order to processed with order by invoice
    *************************************************************************************************************************/
	function invoice_process($parameters){
		$bid = $this->check_parameters($parameters,"basket_identifier");
		$requestinvoice	 = $this->check_parameters($parameters,"requestinvoice",0);
		/*************************************************************************************************************************
        *  update basket to be marked as pay by invoice
        *************************************************************************************************************************/
		$sql = "update shop_basket_container set shop_basket_status=4, shop_basket_payment=2, shop_basket_requires_invoice=".($requestinvoice[0]==1?1:0)." where shop_basket_client = $this->client_identifier and shop_basket_identifier=$bid and shop_basket_session='".session_id()."'";
		$basket = $this->get_order(Array("basket_identifier"=>$bid));
		$this->parent->db_pointer->database_query($sql);
		/*************************************************************************************************************************
        * get msgs
        *************************************************************************************************************************/
		$condition="";
		foreach($this->editors as $key => $value){
			if($condition!=""){
				$condition .= ",";
			}
			$condition .= " '$key'";
		}
		$sql = "select * from memo_information where mi_client = $this->client_identifier and mi_field in (".$condition.") and mi_type='".$this->module_command."'";
		$result  = $this->parent->db_pointer->database_query($sql);
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$this->editors[$r["mi_field"]]["memo"] = $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$r["mi_memo"]));
        }
		$this->parent->db_pointer->database_free_result($result);
		/*************************************************************************************************************************
        * get bill payers email address
        *************************************************************************************************************************/
		$sql = "select * from shop_basket_container 
				inner join contact_data on shop_basket_bill_contact = contact_identifier and contact_client = shop_basket_client
				inner join email_addresses on email_contact = contact_identifier and email_client = shop_basket_client
			where shop_basket_client = $this->client_identifier and shop_basket_identifier=$bid and shop_basket_session='".session_id()."'";
		$email_to="";
		$result = $this->parent->db_pointer->database_query($sql);
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$email_to  		= $r["email_address"];
			$firstname  	= $r["contact_first_name"];
			$lastname  		= $r["contact_last_name"];
        }
		$this->parent->db_pointer->database_free_result($result);
		$out  = "<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "	<form name='confirm' label='Order has been received' method='' action='index.php'>";
		$out .= "		<text><![CDATA[".$this->editors["invoice_confirm"]["memo"]."]]></text>";
		$out .= "	</form>";
		$out .="</module>";
		$email_body = "<style>.row {width:100%;display:block;}\n.cell {width:49%;display:inline;margin:2px;}</style>";
		$email_body .= html_entity_decode($this->editors["invoice_email"]["memo"])."<br>";
		$email_body .= str_replace(Array("<text><![CDATA[", "]]></text>", "<TEXT><![CDATA[", "]]&GT;</TEXT>"), Array("", "", "", ""),$basket["output"]);
		
		/* Starts To check info email and send order alret to admin (By Muhammad Imran) */
		if ($this->check_prefs(Array("sp_from_email")) == ""){
			if(($this->parent->db_pointer->database == 'system_libertas' && ($this->parent->domain == 'hairbeautystyle.com') || $this->parent->domain == 'www.hairbeautystyle.com'))
				$email_info = 'reception@'.$this->parseDomain($this->parent->domain);
			else
				$email_info = 'admin@'.$this->parseDomain($this->parent->domain);
		}else{
			$email_info = $this->check_prefs(Array("sp_from_email"));
		}
		
		$this->call_command("EMAIL_QUICK_SEND", 
			Array(
				"body"		=> "A new purchase order has been placed on the website by $firstname $lastname ($email_to)",
				"from"		=> $email_to,
			    "to" 		=> $email_info,
			    "format"	=> "PLAIN",
			    "subject"	=> "New Purchase Order"
	 		)
		);
		
		/* Ends To check info email and send order alret to admin (By Muhammad Imran) */

		
		$this->call_command("EMAIL_QUICK_SEND", 
			Array(
				"body"		=> $email_body,
				"from"		=> $email_info,
			    "to" 		=> $email_to,
			    "format"	=> "HTML",
			    "subject"	=> "Order Processing ($bid)"
	 		)
		);
		$this->call_command("ELERTADMIN_EMAIL", Array("type" => $this->module_constants["__EMAIL_SHOP_ORDER_PROCESSOR__"], "identifier" => $bid, "url"=> "", "emailbody"=>$email_body));
		$_SESSION["SHOP_BASKET_IDENTIFIER"] = -1;
		/*************************************************************************************************************************
        * if total price is zero the automatically reduce quantity 
        *************************************************************************************************************************/
		if($basket["basket_total"]==0){
			$sql = "select shop_item_quantity,shop_item_stock_id from shop_basket_items 
					where shop_item_client = $this->client_identifier and shop_item_basket=$bid";
			$result = $this->parent->db_pointer->database_query($sql);
			while($r = $this->parent->db_pointer->database_fetch_array($result)){
				$this->reduce_quantity(array("stock_identifier"=>$r["shop_item_stock_id"],"basket_qty"=>$r["shop_item_quantity"]));
			}			

			$this->parent->db_pointer->database_free_result($result);
			//			$this->manage_quantity(Array("basket_identifier"=>$bid));
		}
		return $out;
	}
	
	/*************************************************************************************************************************
    * manage message screens for the shop
	*************************************************************************************************************************/
	function shop_messages($parameters){
		$sql="select * from shop_settings where ss_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$this->editors["credit_confirm"]["lval"]	= $r["ss_cc_accept_label"];
			$this->editors["credit_deny"]["lval"]		= $r["ss_cc_deny_label"];
			$this->editors["invoice_confirm"]["lval"]	= $r["ss_invoice_msg_label"];
			$this->editors["invoice_email"]["lval"]		= $r["ss_invoice_email_label"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$condition="";
		foreach($this->editors as $key => $value){
			if($condition!=""){
				$condition .= ",";
			}
			$condition .= " '$key'";
		}
		$sql = "select * from memo_information where mi_client = $this->client_identifier and mi_field in (".$condition.") and mi_type='".$this->module_command."'";
	    $result  = $this->parent->db_pointer->database_query($sql);
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$this->editors[$r["mi_field"]]["memo"] = $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$r["mi_memo"]));
        }
        $this->parent->db_pointer->database_free_result($result);
		$out ="<module name=\"".$this->module_name."\" display=\"form\"><page_options>
					".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","ENGINE_SPLASH",LOCALE_CANCEL))."
					<header><![CDATA[Confirmation messages - editor]]></header></page_options>";
		$out .="<form name=\"".$this->module_name."_form\" label=\"".LOCALE_SHOP_PAYMENT_MESSAGES."\">";
		$out .="	<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."MESSAGE_SAVE\"/>";
//		$out .="	<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
		$out .="		<page_sections>";
		foreach($this->editors as $key => $value){
			$this_editor = $this->check_parameters($this->editor_configurations,$this->editors[$key]["ed"],Array());
			$config_status_of_editor  = $this->check_parameters($this_editor,"status","unlocked");
			$locked_to  = $this->check_parameters($this_editor,"locked_to","");
			$out .= "<section label='".constant($this->editors[$key]["label"])."' name='confirm_msg'>";
			$out .= "	<input type='text' label=\"".constant($this->editors[$key]["label"])." label\" size=\"100\" height=\"18\" name=\"".$key."_label\" required=\"YES\"><![CDATA[".$this->editors[$key]["lval"]."]]></input>";
			$out .= "	<textarea label=\"".constant($this->editors[$key]["label"])." message\" size=\"100\" height=\"18\" name=\"".$key."\" type=\"RICH-TEXT\" config_type='$config_status_of_editor' locked_to='$locked_to'  required=\"YES\"><![CDATA[".$this->editors[$key]["memo"]."]]></textarea>";
			$out .= "</section>";
		}
		$out .="	</page_sections>";
		$out .= "		<input type='submit' iconify='SAVE' value='".SAVE_DATA."'/>";
		$out .="</form>";
		$this->parent->db_pointer->database_free_result($result);
		$out .="</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * save the messages
	*************************************************************************************************************************/
	function shop_save_messages($parameters){
		$sql="select * from shop_settings where ss_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$identifier = $this->shop_setting_id;
		$this->parent->db_pointer->database_free_result($result);
		
		if($this->shop_setting_id==-1){
			$identifier = $this->getUID();
			$this->shop_setting_id=$identifier;
			$sql = "insert into shop_settings (ss_identifier, ss_charge_vat, ss_vat, ss_always_charge, ss_client) values ('$identifier', '1', '17.5', '0', $this->client_identifier)";
			$this->parent->db_pointer->database_query($sql);
		}
		foreach($this->editors as $key => $value){
			$msg	= $this->check_editor($parameters, $key);
			$label	= strip_tags(html_entity_decode($this->check_editor($parameters, $key."_label")));
			if ($key =="credit_confirm"){
				$ss_cc_accept_label = $label;
			}
			if ($key =="credit_deny"){
				$ss_cc_deny_label = $label;
			}	
			if ($key =="invoice_confirm"){
				$ss_invoice_msg_label = $label;
			}
			if ($key =="invoice_email"){
				$ss_invoice_email_label = $label;
			}
			$loei	= $this->call_command("EMBED_EXTRACT_INFO" , Array("str" => $msg));
			$this->call_command("EMBED_SAVE_INFO",Array("list_of_results"	=> $loei,					"id" => 0, 			"editor"=>$key, 	"module" => $this->module_command, "previous_title" => ""));
			$this->call_command("MEMOINFO_UPDATE",array("mi_type"			=> $this->module_command,	"mi_memo"=>$msg,	"mi_link_id" => 0,	"mi_field" => $key));
		}
		$sql = "update shop_settings set	
					ss_cc_accept_label		= '$ss_cc_accept_label',
					ss_cc_deny_label		= '$ss_cc_deny_label',
					ss_invoice_msg_label	= '$ss_invoice_msg_label',
					ss_invoice_email_label	= '$ss_invoice_email_label'
				where ss_identifier=$this->shop_setting_id and ss_client = $this->client_identifier";
		$this->parent->db_pointer->database_query($sql);
		
		$out ="<module name=\"".$this->module_name."\" display=\"form\"><page_options>
					".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL","ENGINE_SPLASH",LOCALE_CANCEL))."
					<header><![CDATA[Confirmation messages saved]]></header></page_options>";
		$out .="<form name=\"".$this->module_name."_form\" label=\"".LOCALE_MESSAGES_UPDATED."\">";
		$out .= "<text><![CDATA[".LOCALE_MESSAGES_UPDATED_TXT."]]></text>";
		$out .="</form>";
		$this->parent->db_pointer->database_free_result($result);
		$out .="</module>";
		return $out;
	}
	
	/*************************************************************************************************************************
    * retrieve the editor screens 
	*************************************************************************************************************************/
	function shop_get_editors(){
		$condition="";
		foreach($this->editors as $key => $value){
			if($condition!=""){
				$condition .= ",";
			}
			$condition .= " '$key'";
		}
		$sql = "select * from memo_information where mi_client = $this->client_identifier and mi_field in (".$condition.") and mi_type='".$this->module_command."'";
		$result  = $this->parent->db_pointer->database_query($sql);
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$this->editors[$r["mi_field"]]["memo"] = $this->call_command("EDITOR_CONVERT_DATA_TO_HTML", Array("string"=>$r["mi_memo"]));
        }
		$this->parent->db_pointer->database_free_result($result);
		return $this->editors;
	}
	
	/*************************************************************************************************************************
    * choose the payment module to use
    *************************************************************************************************************************/
	function choose_pay($parameters){
		$sql = "select * from shop_settings where ss_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$identifier				= -1;
		$ss_can_pay_invoice		= 0;
		$ss_can_request_invoice = 0;
		$ss_currency			= "GBP";
		$c=0;
        while(($r = $this->parent->db_pointer->database_fetch_array($result)) && $c==0){
			$c++;
			$identifier				= $r["ss_identifier"];
			$ss_can_pay_invoice		= $r["ss_can_pay_invoice"];
			$ss_currency			= $r["ss_currency"];
			$ss_can_request_invoice	= $r["ss_can_request_invoice"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$form_label="Choose the payment module to use";
		$system_config_dir	= $this->parent->site_directories["SYSTEM_CONFIG_DIR"];
		$file_to_use		= $system_config_dir."/licence_data.php";
		$file_content		= file($file_to_use);
		$secpay				= 0;
		$worldpay			= 0;
		for($i=0;$i<count($file_content);$i++){
			$file_content[$i] = trim($file_content[$i]);
			if (strpos($file_content[$i],"secpay")===false){
			} else {
				$secpay = 1;
			}
			if (strpos($file_content[$i],"worldpay")===false){
			} else {
				$worldpay = 1;
			}
		}
		$out  = "<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .= "		<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."CHOOSEPAY_SAVE\" />";
		$out .= "		<input type=\"hidden\" name=\"identifier\" value=\"".$identifier."\" />";
		$out .= "		<page_sections>";
		$out .= "		<section label='Gateways'>";
		define("LOCALE_SHOP_CHOOSE_CURRENCY", "Choose currency");
		$out .= "			<select name='ss_currency' label=\"".LOCALE_SHOP_CHOOSE_CURRENCY."\">".$this->gen_options2d($this->currency, $ss_currency)."</select>";
		$out .= "			<radio name=\"paygate\" label=\"".LOCALE_SHOP_GATEWAYS."\">";
		$out .= "				<option value='SecPay' ";
		if($secpay==1){
			$out .= " selected='true'";
		}
		$out .= "><![CDATA[SecPay, payment account]]></option>";
		$out .= "				<option value='WorldPay'";
		if($worldpay==1){
			$out .= " selected='true'";
		}
		$out .= "><![CDATA[WorldPay, payment account]]></option>";
		$out .= "			</radio>";
		$out .= "			<checkboxes name=\"ss_can_pay_invoice\" label=\"".LOCALE_SHOP_CAN_INVOICE."\" type='vertical'>";
		$out .= "				<option value='1' ";
		if($ss_can_pay_invoice==1){
			$out .= " selected='true'";
		}
		$out .= "><![CDATA[".LOCALE_YES."]]></option>";
		$out .= "			</checkboxes>";
		$out .= "			<checkboxes name=\"ss_can_request_invoice\" label=\"".LOCALE_SHOP_CAN_REQUEST_INVOICE."\" type='vertical'>";
		$out .= "				<option value='1' ";
		if($ss_can_pay_invoice==1){
			$out .= " selected='true'";
		}
		$out .= "><![CDATA[".LOCALE_YES."]]></option>";
		$out .= "			</checkboxes>";
		
		$out .= "		</section>";
		$out .= "		<section label='Merchant Account' redirect='PAYGATEADMIN_SETUP'>";
		$out .= "		</section>";
		$out .= "		</page_sections>";
		$out .= "		<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\" />";
		$out .= "	</form>";
		$out .= "</module>";	
		return $out;
	}
	/*************************************************************************************************************************
    * save the payment module to use
    *************************************************************************************************************************/
	function choose_pay_save($parameters){
		$paygate 				= $this->check_parameters($parameters,"paygate");
		$identifier				= $this->check_parameters($parameters,"identifier",-1);
		$ss_can_pay_invoice		= $this->check_parameters($parameters,"ss_can_pay_invoice");
		$ss_can_request_invoice	= $this->check_parameters($parameters,"ss_can_request_invoice");
		
		if($ss_can_request_invoice==""){
			$ss_can_request_invoice= Array();
			$ss_can_request_invoice[0]=0;
		}
		if($ss_can_pay_invoice==""){
			$ss_can_pay_invoice	= Array();
			$ss_can_pay_invoice[0] = 0;
		}
		if($this->shop_setting_id==-1){
			$identifier = $this->getUID();
			$this->shop_setting_id=$identifier;
			$sql = "insert into shop_settings (ss_identifier, ss_charge_vat, ss_vat, ss_always_charge, ss_client, ss_can_pay_invoice, ss_can_request_invoice) values ('$identifier', '1', '17.5', '0', $this->client_identifier, '$ss_can_pay_invoice[0]', '$ss_can_request_invoice[0]')";
		} else {
			$sql = "update shop_settings set
						ss_can_pay_invoice		= '$ss_can_pay_invoice[0]',
						ss_can_request_invoice = '$ss_can_request_invoice[0]'
					 where ss_identifier=$this->shop_setting_id and ss_client = $this->client_identifier";
		}
		$this->parent->db_pointer->database_query($sql);
		if ($paygate=="SecPay"){
			$newAdminDef ="libertas.payment_gateway_secpay_admin.php,paymentgateway_secpay_admin,PAYGATEADMIN_,1,1";
			$newSiteDef ="libertas.payment_gateway_secpay.php,paymentgateway_secpay,PAYGATE_,0,0";
		}
		if ($paygate=="WorldPay"){
			$newAdminDef ="libertas.payment_gateway_worldpay_admin.php,paymentgateway_worldpay_admin,PAYGATEADMIN_,1,1";
			$newSiteDef ="libertas.payment_gateway_worldpay.php,paymentgateway_worldpay,PAYGATE_,0,0";
		}
		$system_config_dir = $this->parent->site_directories["SYSTEM_CONFIG_DIR"];
		$file_to_use = $system_config_dir."/licence_data.php";
		$file_content = file($file_to_use);
		for($i=0;$i<count($file_content);$i++){
			$file_content[$i] = trim($file_content[$i]);
			if(substr($file_content[$i],0,strlen("libertas.payment_gateway"))=="libertas.payment_gateway"){
				if (substr($file_content[$i],-17) == "PAYGATEADMIN_,1,1"){
					$file_content[$i] = $newAdminDef;
				}
				if (substr($file_content[$i],-12) == "PAYGATE_,0,0"){
					$file_content[$i] = $newSiteDef;
				}
			}
		}
		$out = join("\r\n",$file_content);
		$fp = fopen($file_to_use,"w");
		fwrite($fp, $out);
		fclose($fp);
		$old_umask = umask(0);
		@chmod($file_to_use,LS__FILE_PERMISSION);
		umask($old_umask);
		$data_files 	= $this->parent->site_directories["DATA_FILES_DIR"];
		@unlink ($data_files."/layout_".$this->client_identifier."_admin.xml");
		$form_label = "Payment System Updated";
		/*
		$out  = "<module name=\"".$this->module_name."\" display=\"form\">";
		$out .= "	<form name=\"process_form\" label=\"".$form_label."\" width=\"100%\">";
		$out .= "		<text><![CDATA[Thankyou the payment gateway has now been specified]]></text>";
		$out .= "		<text><![CDATA[Don't forget to go to the Payment Definition module and define your payment parameters]]></text>";
		$out .= "	</form>";
		$out .= "</module>";	
		return $out;
		*/
		$redirect	= $this->check_parameters($parameters,"onsaveredirect");
		$url = $this->parent->base."admin/index.php";
		if ($redirect!=""){
			$url = $this->parent->base."admin/index.php?command=$redirect";
		}
		$this->call_command("ENGINE_REFRESH_BUFFER", Array("url"=>$url));
	}
	/*************************************************************************************************************************
    * set sales tax information for the shop on this clients site
    *************************************************************************************************************************/
	function shop_sales_tax($parameters){
		$sql = "select * from shop_settings where ss_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$ss_id		= -1;
		$ss_cvat	= 1;
		$ss_pvat	= 17.5;
		$ss_acharge	= 0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$ss_id			= $r["ss_identifier"];
			$ss_cvat		= $r["ss_charge_vat"];
			$ss_pvat		= $r["ss_vat"];
			$ss_acharge		= $r["ss_always_charge"];
			$ss_autoreduce	= $r["ss_autoreduce"];
        }
        $this->parent->db_pointer->database_free_result($result);
		$out ="<module name=\"".$this->module_name."\" display=\"form\">
					<page_options>
						".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL", "ENGINE_SPLASH", LOCALE_CANCEL))."
						<header><![CDATA[Sales Tax Settings]]></header>
					</page_options>";
		$out .="<form name=\"".$this->module_name."_form\" label=\"Setup sales tax information\">";
		$out .="	<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."SALES_TAX_SAVE\"/>";
		$out .="	<input type=\"hidden\" name=\"identifier\" value=\"$ss_id\"/>";
		$out .="		<page_sections>";
		$out .= "<section label='Setup' name='setup'>
				<radio label='".LOCALE_CHARGE_SALES_TAX."' name='ss_charge_vat'>".$this->gen_options(Array(0,1),Array(LOCALE_NO,LOCALE_YES),$ss_cvat)."</radio>
				<radio label='".LOCALE_ZERO_PRICE_AUTO_REDUCE."' name='ss_autoreduce'>".$this->gen_options(Array(0,1),Array(LOCALE_NO,LOCALE_YES),$ss_autoreduce)."</radio>
				<input type='text' name='ss_pvat' label='".LOCALE_PERCENT_SALES_TAX."'><![CDATA[$ss_pvat]]></input>
				<radio label='".LOCALE_ALWAYS_CHARGE."' name='ss_always_charge'>
					<option value='0' ";
		if ($ss_acharge==0){
			$out .= " selected='true'";
		}
		$out .="><![CDATA[".LOCALE_NO."]]></option>
					<option value='1' ";
		if ($ss_acharge==1){
			$out .= " selected='true'";
		}
		$out .="><![CDATA[".LOCALE_YES."]]></option>
				</radio>
			";
		$out .= "</section>";
		$out .="	</page_sections>";
		$out .= "		<input type='submit' iconify='SAVE' value='".SAVE_DATA."'/>";
		$out .="</form>";
		$out .="</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * save the sales tax information for the shop on this clients site
    *************************************************************************************************************************/
	function shop_sales_tax_save($parameters){
		$identifier			= $this->check_parameters($parameters,"identifier",-1);
		$ss_charge_vat		= $this->check_parameters($parameters,"ss_charge_vat");
		$ss_pvat			= $this->check_parameters($parameters,"ss_pvat");
		$ss_always_charge	= $this->check_parameters($parameters,"ss_always_charge");
		
		if($this->shop_setting_id==-1){
			$identifier = $this->getUID();
			$this->shop_setting_id=$identifier;
			$sql = "insert into shop_settings (ss_identifier, ss_charge_vat, ss_vat, ss_always_charge, ss_client) values ('$identifier', '$ss_charge_vat', '$ss_pvat', '$ss_always_charge', $this->client_identifier)";
		} else {
			$sql = "update shop_settings set
				ss_charge_vat='$ss_charge_vat', ss_vat= '$ss_pvat', ss_always_charge = '$ss_always_charge'
				 where ss_identifier=$this->shop_setting_id and ss_client = $this->client_identifier";
		}
		
		$result  = $this->parent->db_pointer->database_query($sql);
		$out ="<module name=\"".$this->module_name."\" display=\"form\">
					<page_options>
						".$this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL", "ENGINE_SPLASH", LOCALE_CANCEL))."
						<header><![CDATA[".LOCALE_SHOP_SAVE_SALES_HEADER."]]></header>
					</page_options>";
		$out .="<form name=\"".$this->module_name."_form\" label=\"Setup sales tax information\">";
		$out .="	<input type=\"hidden\" name=\"command\" value=\"ENGINE_SPLASH\"/>";
		$out .="		<page_sections>";
		$out .= "			<section label='".LOCALE_SHOP_SAVE_SALES_LABEL."' name='confirm'>";
		$out .= "				<text><![CDATA[".LOCALE_SHOP_SAVE_SALES_MSG."]]></text>";
		$out .= "			</section>";
		$out .="	</page_sections>";
		$out .= "		<input type='submit' iconify='SAVE' value='".SAVE_DATA."'/>";
		$out .="</form>";
		$out .="</module>";
		return $out;
	}
	/*************************************************************************************************************************
    * a function for a 3rd part module to add items to the basket
    *************************************************************************************************************************/
	function shop_basket_add_module($parameters){
		$shop_item_basket			= $_SESSION["SHOP_BASKET_IDENTIFIER"];
		$shop_item_client 			= $this->client_identifier;
		$shop_item_stock_id 		= $this->check_parameters($parameters,"shop_item_stock_id");
		$shop_item_title 			= $this->check_parameters($parameters,"shop_item_title");
		$shop_item_description 		= $this->check_parameters($parameters,"shop_item_description");
		$shop_item_pickup_price 	= $this->check_parameters($parameters,"shop_item_pickup_price");
		$shop_item_pickup_discount	= $this->check_parameters($parameters,"shop_item_pickup_discount");
		$shop_item_quantity			= $this->check_parameters($parameters,"shop_item_quantity");
		$shop_item_weight			= $this->check_parameters($parameters,"shop_item_weight");
		$shop_item_stock_group		= $this->check_parameters($parameters,"shop_item_stock_group");
		$trigger					= $this->check_parameters($parameters,"trigger",Array());
		$quantity_left	= -1;
		$amount_left	= -1;
		
		$sql ="select * from shop_basket_items 
					inner join metadata_details on md_identifier = shop_item_stock_id and md_client= shop_item_client
				where shop_item_basket='$shop_item_basket' and shop_item_client = $shop_item_client and shop_item_stock_id='$shop_item_stock_id'";
		$found=0;
		$result  = $this->parent->db_pointer->database_query($sql);
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
        	$found=1;
			$amount_left = $r["md_quantity"]; // how many available
			$prev_amount = $r["shop_item_quantity"]; // how many currently in basket
        }
        $this->parent->db_pointer->database_free_result($result);
		$sql = "";
		$quantity_left=-1;
		if($found==0){ // Add a new item to the basket
			$insert =0;
			if($amount_left==-1){
				$insert =1;
			}else if (($amount_left>0 && ($amount_left - $shop_item_quantity ) >= 0)){
				$quantity_left = ($amount_left - $shop_item_quantity);
				$insert =1;
			} else if (($amount_left>0 && ($amount_left - $shop_item_quantity ) < 0)){
				$quantity_left = 0;
				$shop_item_quantity = $amount_left;
				$insert =1;
			}
			if($insert == 1){
				$shop_item_identifier = $this->getUID();
				$sql = "insert into shop_basket_items (
					shop_item_identifier,
					shop_item_basket,
					shop_item_client,
					shop_item_stock_id,
					shop_item_title,
					shop_item_description,
					shop_item_pickup_price,
					shop_item_pickup_discount,
					shop_item_quantity,
					shop_item_weight,
					shop_item_stock_group
				) values (
					'$shop_item_identifier',
					'$shop_item_basket',
					'$shop_item_client',
					'$shop_item_stock_id',
					'$shop_item_title',
					'$shop_item_description',
					'$shop_item_pickup_price',
					'$shop_item_pickup_discount',
					'$shop_item_quantity',
					'$shop_item_weight',
					'$shop_item_stock_group'
				)";
				$this->parent->db_pointer->database_query($sql);
				/*************************************************************************************************************************
                * if a triger exists then save it
                *************************************************************************************************************************/
				if (is_array($trigger)){
					for($i=0;$i<count($trigger);$i++){
						$cmd		= $trigger[$i]["cmd"];
						$sp_trigger = $this->getUID();
						$sql		= "insert into shop_trigger (st_identifier, st_executed, st_basket_item, st_client, st_cmd) values (".$sp_trigger.", 0, $shop_item_identifier, $this->client_identifier, '$cmd')";
						$this->parent->db_pointer->database_query($sql);
						foreach($trigger[$i]["params"] as $key => $value){
							$sql	= "insert into shop_trigger_parameters (stp_trigger, stp_client, stp_key, stp_value, stp_rank) values (".$sp_trigger.", $this->client_identifier, '$key', '$value' , $i)";
							$this->parent->db_pointer->database_query($sql);
						}
					}
				}
			}
		} else {
			$update=0;
			if($amount_left!=-1){
				if ((($prev_amount - $shop_item_quantity)+$amount_left)<0){
					$shop_item_quantity = $prev_amount+$amount_left;
					$quantity_left = 0;
					$update=1;
				} else {
					$quantity_left = (($prev_amount - $shop_item_quantity)+$amount_left);
					$update=1;
				}
			} else {
					$update=1;
			}
			if ($update == 1){
				$sql = "update shop_basket_items set 
							shop_item_title = '$shop_item_title',
							shop_item_description = '$shop_item_description',
							shop_item_pickup_price = '$shop_item_pickup_price',
							shop_item_pickup_discount = '$shop_item_pickup_discount',
							shop_item_quantity = '$shop_item_quantity',
							shop_item_weight = '$shop_item_weight',
							shop_item_vat = '$shop_item_vat',
							shop_item_stock_group ='$shop_item_stock_group'
						where 
							shop_item_stock_id = '$shop_item_stock_id' and 
							shop_item_basket = '$shop_item_basket' and 
							shop_item_client = '$this->client_identifier'";
				$this->parent->db_pointer->database_query($sql);
			}
		}
		/*************************************************************************************************************************
        *  execute the appropraite command
        *************************************************************************************************************************/
		if($sql !=""){
			if($quantity_left>=0){
				$sql = "update metadata_details set md_quantity = $quantity_left where md_identifier = '$shop_item_stock_id' and md_client = $this->client_identifier";
				$this->parent->db_pointer->database_query($sql);
				$this->call_command("METADATAADMIN_CACHE",Array("md_identifier"=>$shop_item_stock_id));
			}
			return 1; // pass
		} else {
			return 0; // fail
		}
	}
	
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function display_item_purchase_history($parameters){
		$group_id	= $this->check_parameters($parameters,"group_id");
		$type	 	= $this->check_parameters($parameters,"type","booked");
		$return	 	= $this->check_parameters($parameters,"return","SHOP_LIST");
		$identifier	= $this->check_parameters($parameters,"list","");
		$where ="";
		$typemsg="";
		if($type=="booked"){
			$where		= " and shop_basket_container.shop_basket_payment in (1, 3, 8) ";
			$typemsg	= "Places Booked";
		}
		if($type=="reserved"){
			$where		= " and shop_basket_container.shop_basket_payment=2";
			$typemsg	= "Places Reserved";
		}
		if($type=="invoice"){
			$where		= " and shop_basket_container.shop_basket_requires_invoice=1";
			$typemsg	= "Requires Invoice";
		}
		
		$sql = "SELECT shop_basket_items.*, shop_basket_container.*, company_name, 
					cBill.contact_first_name as cBill_fname, cBill.contact_last_name as cBill_lname, cBill.contact_identifier as cBill_id
				FROM `shop_basket_items` 
					inner join shop_basket_container on shop_item_basket = shop_basket_identifier and shop_basket_client  = shop_item_client 
					inner join contact_data as cBill on shop_basket_bill_contact = cBill.contact_identifier and cBill.contact_client  = shop_basket_client 
					inner join contact_address on cBill.contact_address = address_identifier and address_client=cBill.contact_client
					inner join contact_company on company_address = address_identifier and address_client=company_client
				where shop_item_client=$this->client_identifier and shop_item_stock_group = $group_id and shop_basket_status>=4 $where";
		$out	 = "";
		$result  = $this->parent->db_pointer->database_query($sql);

		$total=0;
		$c=0;
		$invoicable=0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			$paper_invoice="Yes";
			if($c==0){
	        	$out .= "<tr class='tablecellalt'>";
				$c=1;
			} else {
        		$out .= "<tr class='tablecell'>";
				$c=0;
			}
			$payStatus =$this->shop_status($r["shop_basket_status"])." - ". $this->status_list[$r["shop_basket_payment"]];
			if($this->check_parameters($r,"shop_basket_requires_invoice","__NOT_FOUND__")!="__NOT_FOUND__"){
				$invoicable =1;
				if ($r["shop_basket_requires_invoice"]==0){
					$paper_invoice = "No";
				} else if ($r["shop_basket_requires_invoice"]==1){
					$paper_invoice = "Yes";
				} else if ($r["shop_basket_requires_invoice"]==2){
					$paper_invoice = "Invoice Sent";
				}
			}
			if (true){
			$out .= "<td><a href='admin/index.php?command=SHOP_PROCESS_ORDER&amp;identifier=".$r["shop_basket_identifier"]."' alt='Process this order'>".$r["shop_basket_identifier"]."</a></td>";
			}
			
			$out .= "<td>".$r["shop_basket_date"]."</td>";
			$out .= "<td>".$r["company_name"]."</td>
						<td>".$r["shop_item_quantity"]."</td>
						<td>$payStatus</td>";
			if($invoicable==1){
			$out.="		<td>".$paper_invoice."</td>";
			}
			$out .="</tr>";
			$total += $r["shop_item_quantity"];
        }
        $this->parent->db_pointer->database_free_result($result);
       	$out .= "<tr>
					<td colspan='3' align='right'><strong>Total quantity sold</strong></td>
					<td>".$total."</td>
				</tr>";
				
       	$start 	= "<tr>
					<td><strong>Reference</strong></td>
					<td><strong>Date ordered</strong></td>
					<td><strong>Company</strong></td>
					<td><strong>Number purchased</strong></td>
					<td><strong>Payment Status</strong></td>";
		if($invoicable=1){
			$start .= "<td><strong>Requires invoice</strong></td>";
		}
		$start .= "</tr>";
		$out = $start . $out;
		$page_options  = "<page_options><header><![CDATA[$typemsg]]></header>";
		if($identifier!=""){
			$page_options .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$return."&amp;identifier=$identifier",LOCALE_CANCEL));
		} else {
			$page_options .= $this->call_command("XMLTAG_GENERATE_XML_BUTTON",Array("CANCEL",$return,LOCALE_CANCEL));
		}
		$page_options .= "</page_options>";
		return "<module name='Shoppurchasehistory' display='form'>$page_options<form width='100%' label='Order information' name='phistory'><text><![CDATA[<table width=\"100%\">$out</table>]]></text></form></module>";
	}
	/*************************************************************************************************************************
    * 
    *************************************************************************************************************************/
	function check_status_levels($parameters){
		$group = $this->check_parameters($parameters,"item_group",-1);
		$return_data = Array("Booked"=>0, "Reserved"=>0, "Invoice"=>0, "Paid"=>0,"Rejected"=>0 );
/*
		$status= Array();
		$status[5] = "Processing";
		$status[6] = "Valid";
		$status[7] = "Shipping";
		$status[8] = "Delivered";
		$status[9] = "Rejected";
		Mark order as being processed</option>
								<option value='9'>Reject this order</option>
								<option value='6'>Mark order as Valid and Ready for delivery</option>
								<option value='7'>Mark order as currently being shipped</option>
								<option value='8'>Mark order as delivered
*/
		if ($group==-1){
			return $return_data;
		}
		$sql = "SELECT shop_basket_items.*, shop_basket_status, shop_basket_payment, shop_basket_requires_invoice,
					cDel.contact_first_name as cDel_fname, cDel.contact_last_name as cDel_lname, cDel.contact_identifier as cDel_id,
					cBill.contact_first_name as cBill_fname, cBill.contact_last_name as cBill_lname, cBill.contact_identifier as cBill_id
				FROM `shop_basket_items` 
					inner join shop_basket_container on shop_item_basket = shop_basket_identifier and shop_basket_client  = shop_item_client 
					inner join contact_data as cBill on shop_basket_bill_contact = cBill.contact_identifier and cBill.contact_client  = shop_basket_client 
					inner join contact_data as cDel on shop_basket_delivery_contact = cDel.contact_identifier and cDel.contact_client  = shop_basket_client 
				where shop_item_client=$this->client_identifier and shop_item_stock_group = $group and shop_basket_status>=4";
				// and shop_basket_status!=9
		$out	 = "";
		$result  = $this->parent->db_pointer->database_query($sql);
		$total=0;
		$c=0;
        while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if (($r["shop_basket_payment"]==1) || ($r["shop_basket_payment"]==3) || ($r["shop_basket_payment"]==8)){
				$return_data["Booked"]+=$r["shop_item_quantity"];
			}
			/*************************************************************************************************************************
            * if invoice required and not waiting refund or refund given 
            *************************************************************************************************************************/
			if (($r["shop_basket_requires_invoice"]==1) && ($r["shop_basket_payment"]!=4) && ($r["shop_basket_payment"]!=5) && ($r["shop_basket_payment"]!=6) && ($r["shop_basket_payment"]!=7)){
				$return_data["Invoice"]++;
			}
			if ($r["shop_basket_status"]==9){
				$return_data["Rejected"]+=$r["shop_item_quantity"];
			}
			if(($r["shop_basket_payment"]==2) && ($r["shop_basket_status"]!=9)){
				$return_data["Reserved"]+=$r["shop_item_quantity"];
			}
        }
        $this->parent->db_pointer->database_free_result($result);
		return $return_data;
	}
	/*************************************************************************************************************************
    * retrieve the list of contact fields required for shop
    *************************************************************************************************************************/
	function form_restrictions($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"form_restrictions",__LINE__,""));
		}
		$override_required	= $this->check_parameters($parameters,"override_required",Array());
		$entry=Array(
		    "contact_company" 		=> Array("value" => "contact_company" ,"locale" => LOCALE_CONTACT_COMPANY, "available" => 1,"required" => 0),
		    "contact_first_name"	=> Array("value" => "contact_first_name" , "locale" => LOCALE_CONTACT_FIRST_NAME, "available" => 1,"required" => 0),
//		    "contact_initials" 		=> Array("value" => "contact_initials" , "locale" => LOCALE_CONTACT_INITIALS, "available" => 1,"required" => 0),
		    "contact_last_name" 	=> Array("value" => "contact_last_name" , "locale" => LOCALE_CONTACT_SURNAME, "available" => 1,"required" => 0),
		    "contact_email" 		=> Array("value" => "contact_email" , "locale" => LOCALE_CONTACT_EMAIL_ADDRESS, "available" => 1, "required" => 1),
		    "contact_telephone" 	=> Array("value" => "contact_telephone" , "locale" => LOCALE_CONTACT_PHONE, "available" => 1,"required" => 0),
		    "contact_fax" 			=> Array("value" => "contact_fax" , "locale" => LOCALE_CONTACT_FAX, "available" => 1,"required" => 0),
		    "contact_address1" 		=> Array("value" => "contact_address1" , "locale" => LOCALE_ADDRESS1, "available" => 1,"required" => 0),
		    "contact_address2" 		=> Array("value" => "contact_address2" , "locale" => LOCALE_ADDRESS2, "available" => 1,"required" => 0),
		    "contact_address3" 		=> Array("value" => "contact_address3" , "locale" => LOCALE_ADDRESS3, "available" => 1,"required" => 0),
		    "contact_city"			=> Array("value" => "contact_city" , "locale" => LOCALE_ADDRESS_CITY, "available" => 1,"required" => 0),
		    "contact_county" 		=> Array("value" => "contact_county" , "locale" => LOCALE_ADDRESS_COUNTY, "available" => 1,"required" => 0),
		    "contact_country" 		=> Array("value" => "contact_country" , "locale" => LOCALE_ADDRESS_COUNTRY, "available" => 1,"required" => 0),
		    "contact_postcode" 		=> Array("value" => "contact_postcode" , "locale" => LOCALE_ADDRESS_POSTCODE, "available" => 1,"required" => 0)
		);
		for($i=0;$i<count($override_required);$i++){
			$entry[$override_required[$i]]["required"]=1;
			$entry[$override_required[$i]]["available"]=1;
		}
		return $entry;
	}

	/*************************************************************************************************************************
    * get_shop_settings
    *************************************************************************************************************************/
	function get_shop_settings(){
		$list_array = Array();
		$sql="select * from shop_settings where ss_client = $this->client_identifier";
		$result  = $this->parent->db_pointer->database_query($sql);
		$identifier =-1;
		$same_del	= 1;
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			foreach($r as $key => $val){
				if(!is_int($key)){
					$list_array[$key] = $val;
				}
			}
        }
		$this->parent->db_pointer->database_free_result($result);
		$list_array["currency"] = $this->currency;
		return $list_array;
	}

	/*************************************************************************************************************************
    * Reduce meatadata quantities after they are booked/purchased
    *************************************************************************************************************************/
	function reduce_quantity($parameters){
		$stock_id 	= $this->check_parameters($parameters,"stock_identifier");
		$qty	= $this->check_parameters($parameters,"basket_qty");
		$sql 	= "update metadata_details set md_quantity=metadata_details.md_quantity-". $qty ." WHERE md_link_group_id= $stock_id and md_client = $this->client_identifier ";
		$this->parent->db_pointer->database_query($sql);
	}
	/*************************************************************************************************************************
    * mange the meatadata quantities for this basket
    *************************************************************************************************************************/
	function manage_quantity($parameters){
		$bi 			= $this->check_parameters($parameters,"basket_identifier");
		$process_action = $this->check_parameters($parameters,"process_action",4);
		$previous_action= $this->check_parameters($parameters,"previous_action",0);
		$sql 			= "update shop_basket_items SET shop_item_shop = 1 where shop_item_basket= $bi and shop_item_client = $this->client_identifier ";
		$this->parent->db_pointer->database_query($sql);
		$sql 			= "select shop_basket_items.*, metadata_details.* from shop_basket_items 
							inner join metadata_details on md_link_group_id = shop_item_stock_id and md_client = shop_item_client
							where shop_item_basket= $bi and shop_item_client = $this->client_identifier group by md_link_group_id order by md_identifier desc";
		$result  = $this->parent->db_pointer->database_query($sql);
		$prev_process=-1;
		while($r = $this->parent->db_pointer->database_fetch_array($result)){
			if($previous_action!=$process_action){
				if($process_action==9 && $previous_action>4){
					if($r["md_quantity"]!=-1){
		   		   		$sql = "update metadata_details set md_quantity = md_quantity + ".$r["shop_item_quantity"]." where md_client = $this->client_identifier and md_identifier=".$r["md_identifier"];
	    		    	$this->parent->db_pointer->database_query($sql);
						$this->call_command("METADATAADMIN_CACHE",Array("md_identifier"=>$r["md_identifier"]));
					}
				} else {
					if($previous_action==4 || $previous_action==9){
						if($r["md_quantity"]>0){
	    		    		$sql = "update metadata_details set md_quantity = md_quantity - ".$r["shop_item_quantity"]." where md_client = $this->client_identifier and md_identifier=".$r["md_identifier"];
			    	    	$this->parent->db_pointer->database_query($sql);
							$this->call_command("METADATAADMIN_CACHE",Array("md_identifier"=>$r["md_identifier"]));
						}
					}
				}
			}
        }
        $this->parent->db_pointer->database_free_result($result);
	}
	
	/*************************************************************************************************************************
    * process trigger
	*
	* @param Array Parameters 1 index "basket_items" Array of basket item identifiers
    *************************************************************************************************************************/
	function process_trigger($parameters){
		$basket_items = $this->check_parameters($parameters,"basket_items",Array());
		for($i=0;$i<count($basket_items);$i++){
			$sql= "select * from shop_trigger 
					inner join shop_trigger_parameters on stp_client = st_client and stp_trigger = st_identifier
					where st_executed=0 and st_client = $this->client_identifier and st_basket_item = ".$basket_items[$i]."
					order by stp_rank
			";
			$result  = $this->parent->db_pointer->database_query($sql);
			$p = Array();
    	    while($r = $this->parent->db_pointer->database_fetch_array($result)){
	        	$cmd			= $r["st_cmd"];
        		$p[$r["stp_key"]]	= $r["stp_value"];
        	}
    	    $this->parent->db_pointer->database_free_result($result);
			$this->call_command($cmd,$p);
		}

	}
	
	/** Function to delete e-commerce orders */
	function delete_order($parameters){
		$identifier = $this->check_parameters($parameters,"identifier",Array());
		$sql = " DELETE FROM shop_basket_container WHERE shop_basket_identifier = " . $identifier ;
		$result = $this->parent->db_pointer->database_query($sql);
		$sql = " DELETE FROM shop_basket_items WHERE shop_item_basket = " . $identifier ;		
		$result = $this->parent->db_pointer->database_query($sql);						
		$this->call_command("ENGINE_REFRESH_BUFFER",Array("command=".$this->module_command."LIST_ORDERS"));		
	}
}
?>