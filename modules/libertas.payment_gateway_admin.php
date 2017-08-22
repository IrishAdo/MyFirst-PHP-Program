<?PHP
/*************************************************************************************************************************
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.payment_gateway_admin.php
* @date 08 Nov 2004
*************************************************************************************************************************/
/*************************************************************************************************************************
* This module is the administration module for the Payment Gateway.
*************************************************************************************************************************/
class paymentgateway_admin extends module{
	/*************************************************************************************************************************
	*  Class Variables (generic
	*************************************************************************************************************************/
	var $module_grouping			= "LOCALE_MANAGEMENT_GROUP_INTERACTIVE";
	var $module_name_label			= "Payment Gateway (Administration)";
	var $module_name				= "paymentgateway_admin";
	var $module_admin				= "1";
	var $module_command				= "PAYGATEADMIN_"; 		// all commands specifically for this module will start with this token
	var $webContainer				= "PAYGATEADMIN_";
	var $module_label				= "MANAGEMENT_PAYMENTGATEWAY";
	var $module_modify		 		= '$Date: 2005/02/08 17:01:12 $';
	var $module_version 			= '$Revision: 1.5 $';
	var $module_creation 			= "26/02/2004";
	/*************************************************************************************************************************
    * Lists to be used by payment systems 
    *************************************************************************************************************************/
	var $testModes 					= Array();
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
	*  filter options
	*************************************************************************************************************************/
	var $display_options			= array();
	
	/*************************************************************************************************************************
	*  Access options php 5 will allow these to become private variables.
	*************************************************************************************************************************/
	var $admin_access						= 0; // can access admin
	var $admin_access_setup_account			= 0; // can setup account
	var $admin_access_order_management		= 0; // can view order status
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
			if ($user_command==$this->module_command."ACCESS_OPTIONS"){
				return $this->module_admin_options(0);
			}
			if ($user_command==$this->module_command."ACCESS_DISPLAY_OPTIONS"){
				return $this->module_admin_access_options(0);
			}
			if ($user_command==$this->module_command."MENU_DISPLAY_OPTIONS"){
				return $this->display_channels($parameter_list);
			}
			/*************************************************************************************************************************
			* Create table function allow access if in install mode
			*************************************************************************************************************************/
			if ($user_command==$this->module_command."CREATE_TABLE"){
				return $this->create_table();
			}
			/*************************************************************************************************************************
			* Administration Module commands
			*************************************************************************************************************************/
			if ($this->admin_access==1){
				if($this->admin_access_setup_account==1){
					if ($user_command==$this->module_command."SETUP"){
						return $this->payment_account_screen_setup($parameter_list);
					}
					if ($user_command==$this->module_command."SETUP_SAVE"){
						$this->payment_account_screen_setup_save($parameter_list);
						$redirect = $this->check_parameters($parameter_list,"onsaveredirect","ENGINE_SPLASH");
						$this->call_command("ENGINE_REFRESH_BUFFER", Array("url"=>$this->parent->base."admin/index.php?command=$redirect"));
					}
				}
				if($this->admin_access_order_management==1){
					if ($user_command==$this->module_command."LIST"){
						return $this->order_list($parameter_list);
					}
				}
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
		* lock down the access
		*************************************************************************************************************************/
		$this->admin_access						= 0; // access to the admin functions
		/*************************************************************************************************************************
		* load the required locale
		*************************************************************************************************************************/
		$this->load_locale("payment_gateway_admin");
		/*************************************************************************************************************************
		* define the list of Editors in this module and define them as empty
		*************************************************************************************************************************/
		$this->editor_configurations = Array();
		/*************************************************************************************************************************
        * set up defaults
        *************************************************************************************************************************/
		$this->module_admin_options = array(
//			array("PAYGATEADMIN_SETUP", "MANAGE_PAYMENT_SETUP","PAYGATEADMIN_ALL"),
//			array("PAYGATEADMIN_LIST", "MANAGE_PAYMENT_ORDERS","PAYGATEADMIN_ALL|PAYGATEADMIN_ORDER_MANAGER")
		);
		/*************************************************************************************************************************
		* request the page size 
		*************************************************************************************************************************/
		$this->page_size=$this->check_prefs(Array("sp_page_size"));
		/*************************************************************************************************************************
		* define the admin access that this user has.
		*************************************************************************************************************************/
		$grp_info = $this->check_parameters($_SESSION,"SESSION_GROUP",Array());
		$max_grps = count($grp_info);
		if ($this->parent->module_type=="admin"){
			for($i=0;$i < $max_grps; $i++){
				$access = $grp_info[$i]["ACCESS"];
				$length_of_array=count($access);
				for ($index=0;$index<$length_of_array;$index++){
					if (("PAYGATEADMIN_ALL"==$access[$index]) || ("ALL"==$access[$index])){
						$this->admin_access					= 1; // can access the admin commands
						$this->admin_access_setup_account	= 1; // 
						$this->admin_access_order_management= 1;
					}
					if("PAYGATEADMIN_ACCOUNT_ADMIN"==$access[$index]){
						$this->admin_access_setup_account	= 1;
					}
					if("PAYGATEADMIN_ORDER_MANAGER"==$access[$index]){
						$this->admin_access_order_management= 1;
					}
				}
			}
		}
		return 1;
	}
	/*************************************************************************************************************************
	* function produces abstract structure of modules db structure
	*
    * used to generate the table structure required abstract function that passes an abstract
	* representation of the desired table structure to the proper database module which will
	* interpet the abstract and convert it into a valid SQL Create table structure.
	*
	* @return Array list of abstract table definitions for this module
	*************************************************************************************************************************/
	function create_table(){
		$tables = array();
		/*************************************************************************************************************************
		* Table structure for table 'payment_account_details'
		*
		* Payment details for different paysysttems differ greatly this table holds the key pairs of variable/value
		*************************************************************************************************************************/
		$fields = array(
			array("pad_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("pad_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("pad_uri"				,"varchar (255)"			,"NOT NULL"	,"default '0'","key"),
			array("pad_confirm_msg"		,"text"						,"NOT NULL"	,"default ''"),
			array("pad_deny_msg"		,"text"						,"NOT NULL"	,"default ''")
		);
		$primary ="pad_identifier";
		$tables[count($tables)] = array("payment_account_details", $fields, $primary);
		/*************************************************************************************************************************
		* Table structure for table 'payment_account_properties'
		*
		* Payment details for different paysysttems differ greatly this table holds the key pairs of variable/value
		* the pap_identifier is mapped to the pad_identifier field
		*************************************************************************************************************************/
		$fields = array(
			array("pap_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'"), // link to PAD owner
			array("pap_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("pap_property"		,"varchar (255)"			,"NOT NULL"	,"default '0'","key"),
			array("pap_value"			,"varchar (255)"			,"NOT NULL"	,"default '0'","key")
		);
		$primary ="";
		$tables[count($tables)] = array("payment_account_properties", $fields, $primary);
		/*************************************************************************************************************************
		* Table structure for table 'payment_account_orders'
		*
		* Record that links to the shop basket and holds status of the order process fromthe payment system
		*************************************************************************************************************************/
		$fields = array(
			array("pao_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'","key"), // 
			array("pao_pad"				,"unsigned integer"			,"NOT NULL"	,"default '0'","key"), // maps to the payment_account_details table pad_identifier
			array("pao_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("pao_user"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("pao_basket"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("pao_status"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("pao_sent"			,"datetime"					,"NOT NULL"	,"default '0'"), // date time order request sent
			array("pao_approved"		,"datetime"					,"NOT NULL"	,"default '0'")  // date time order request approved
		);
		$primary ="pao_identifier";
		$tables[count($tables)] = array("payment_account_orders", $fields, $primary);
		/*************************************************************************************************************************
		* Table structure for table 'payment_order_properties'
		*
		* Extra payment properties for the basket specific for this Payment Module this table holds the key pairs of variable/value
		* the pop_identifier is mapped to the pao_identifier field
		*************************************************************************************************************************/
		$fields = array(
			array("pop_identifier"		,"unsigned integer"			,"NOT NULL"	,"default '0'"), // link to PAO owner
			array("pop_client"			,"unsigned integer"			,"NOT NULL"	,"default '0'","key"),
			array("pop_property"		,"varchar (255)"			,"NOT NULL"	,"default '0'","key"),
			array("pop_value"			,"varchar (255)"			,"NOT NULL"	,"default '0'","key")
		);
		$primary ="";
		$tables[count($tables)] = array("payment_order_properties", $fields, $primary);
		return $tables;
	}
	/*************************************************************************************************************************
	*                               P A Y M E N T   S E T U P  M A N A G E R   F U N C T I O N S
	*************************************************************************************************************************/
	
	/*************************************************************************************************************************
	* Setup screen for the payment account
	*
	* @return String XML representationof the form
	*************************************************************************************************************************/
	function payment_account_screen_setup($parameters){
		$identifier  = $this->check_parameters($parameters,"identifier",-1);
		/**
        * get Account setup details
        */
		$sql = "select * ";
		$sql .=	"from payment_account_details ";
		$sql .= " where pad_client = $this->client_identifier";
			
		$propertyValues = Array();
		$uri ="";
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
			$identifier 		= $r["pad_identifier"];
			$uri		 		= $r["pad_uri"];
        }
        $this->call_command("DB_FREE",Array($result));
		/**
        *
        */
		$sql = "select * from payment_account_properties where pap_client = $this->client_identifier and pap_identifier = $identifier";
		$propertyValues = Array();
        $result  = $this->call_command("DB_QUERY",Array($sql));
        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
        	$propertyValues[count($propertyValues)] = Array(
				"key" => $r["pap_property"], 
				"value" => $r["pap_value"]
			);
        }
        $this->call_command("DB_FREE",Array($result));
		/**
        * display this form
        */
		$out ="<module name=\"".$this->module_name."\" display=\"form\">";
		$out .="<form name=\"".$this->module_name."_form\" label=\"".LOCALE_PAYMENT_SETUP."\">";
		$out .="	<input type=\"hidden\" name=\"command\" value=\"".$this->module_command."SETUP_SAVE\"/>";
		$out .="	<input type=\"hidden\" name=\"identifier\" value=\"$identifier\"/>";
		$out .="		<page_sections>";
		$out .= "		<section label='Gateways' redirect='SHOP_CHOOSEPAY'>";
		$out .= "		</section>";
		$out .= "		<section label='Merchant Account' selected='true'>";
//		$out .="				<input type=\"text\" name=\"pad_uri\" label=\"".LOCALE_PAYMENT_URI."\" required='YES'><![CDATA[$uri]]></input>";
		$out .="				<text><![CDATA[<p>Special properties for this Account</p>]]></text>";
		$max_setup_values	= count($this->setupProperties);
		$max_prop_values	= count($propertyValues);
		for($i=0; $i<$max_setup_values; $i++){
			$value="";
			
			for($v=0; $v<$max_prop_values; $v++){
				if ($propertyValues[$v]["key"]==$this->setupProperties[$i][0]){
					$value = $propertyValues[$v]["value"];
				}
			}
			if($this->setupProperties[$i][2]=="text"){
				$out .="	<input type=\"text\" name=\"prop_".$this->setupProperties[$i][0]."\" label=\"".$this->setupProperties[$i][1]."\"><![CDATA[$value]]></input>";
			}
			if($this->setupProperties[$i][2]=="__CALLBACK__"){
				if($value==""){
					$value="http://".$this->parent->domain.$this->parent->base."_process.php";
				}
				$out .="	<input type=\"text\" name=\"prop_".$this->setupProperties[$i][0]."\" label=\"".$this->setupProperties[$i][1]."\"><![CDATA[$value]]></input>";
			}
			if($this->setupProperties[$i][2]=="boolean"){
				
				$out .="	<radio name=\"prop_".$this->setupProperties[$i][0]."\" label=\"".$this->setupProperties[$i][1]."\">
					<option value='No'";
					if ($value=='No' || $value==''){
						$out .= " selected='true'";
					}	
					$out.=">No</option>
					<option value='Yes'";
					if ($value=='Yes'){
						$out .= " selected='true'";
					}	
					$out.=">Yes</option>
				</radio>";
			}
			if($this->setupProperties[$i][2]=="__CURRENCY__"){
				if($value==""){
					$value ="GBP";
				}
				$out .="<select name=\"prop_".$this->setupProperties[$i][0]."\" label=\"".$this->setupProperties[$i][1]."\" >";
				for($curr = 0, $curr_max=count($this->currency); $curr<$curr_max; $curr++){
					$out .="	<option value=\"".$this->currency[$curr][0]."\"";
					if ($value==$this->currency[$curr][0]){
						$out.=" selected='true'";
					}
					$out .="><![CDATA[".$this->currency[$curr][1]."]]></option>";
				}
				$out .="</select>";
			}
			if($this->setupProperties[$i][2]=="__TEST__"){
				$out .="<select name=\"prop_".$this->setupProperties[$i][0]."\" label=\"".$this->setupProperties[$i][1]."\" >";
				$out .="	<option value=\"\"><![CDATA[".LOCALE_NO_TEST_MODE."]]></option>";					
				$mz= count($this->testModes);
				for($z=0;$z<$mz;$z++){
					$out .="	<option value=\"".$this->testModes[$z][0]."\"";
					if ($value==$this->testModes[$z][0]){
						$out.=" selected='true'";
					}
					$out .="><![CDATA[".constant($this->testModes[$z][1])."]]></option>";
				}
				$out .="</select>";
			}
		}
		$out .="		</section>";

		$out .="	</page_sections>";
		$out .="			<input type=\"submit\" iconify=\"SAVE\" value=\"".SAVE_DATA."\"/>";
		$out .="</form>";
		$this->call_command("DB_FREE",array($result));
		$out .="</module>";
		return $out;
	}
	/*************************************************************************************************************************
	* Save setup screen information for the payment account
	*
	*************************************************************************************************************************/
	function payment_account_screen_setup_save($parameters){
		$identifier 	= $this->check_parameters($parameters,"identifier",-1);
//		$pad_uri 		= $this->validate($this->check_parameters($parameters,"pad_uri"));
		$confirm_msg	= $this->check_editor($parameters, "pad_confirm_msg");
		$deny_msg		= $this->check_editor($parameters, "pad_deny_msg");
	/**
        * update account setup details
        */
		if ($identifier==-1){
			$identifier = $this->getUid();
			$sql = "insert into payment_account_details (pad_identifier, pad_client, pad_uri) values ('$identifier', $this->client_identifier, '')";
		} else {
			$sql = "update payment_account_details set pad_uri = '' where pad_identifier = $identifier and pad_client = $this->client_identifier";
		}
		$this->call_command("DB_QUERY",Array($sql));
		/**
        * save the confirm message
        */
		$loei_confirm	= $this->call_command("EMBED_EXTRACT_INFO" , Array("str" => $confirm_msg));
		$this->call_command("EMBED_SAVE_INFO",Array("list_of_results" => $loei_confirm, "id" => $identifier, "editor"=>"confirm", 	"module" => $this->module_command, "previous_title" => ""));
		$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->module_command,"mi_memo"=>$confirm_msg,	"mi_link_id" => $identifier, "mi_field" => "confirm"));
		/**
        * save the deny message
        */
		$loei_deny		= $this->call_command("EMBED_EXTRACT_INFO" , Array("str" => $deny_msg));
		$this->call_command("EMBED_SAVE_INFO",Array("list_of_results" => $loei_deny, 	"id" => $identifier, "editor"=>"deny",		"module" => $this->module_command, "previous_title" => ""));
		$this->call_command("MEMOINFO_UPDATE",array("mi_type"=>$this->module_command,"mi_memo"=>$deny_msg,		"mi_link_id" => $identifier, "mi_field" => "deny"));
		/**
        * update properties
        */
		$max_setup_values	= count($this->setupProperties);
		$sql = "delete from payment_account_properties where pap_identifier= $identifier and  pap_client=$this->client_identifier";
		$this->call_command("DB_QUERY",Array($sql));
		for($i=0; $i<$max_setup_values; $i++){
			$sql = "insert into payment_account_properties (pap_identifier, pap_client, pap_property, pap_value) values ($identifier, $this->client_identifier, '".$this->setupProperties[$i][0]."','".$this->validate($this->check_parameters($parameters,"prop_".$this->setupProperties[$i][0]))."')";
			$this->call_command("DB_QUERY",Array($sql));
		}
    	return "";
	}
	/*************************************************************************************************************************
	*                                    O R D E R   M A N A G E R   F U N C T I O N S
	*************************************************************************************************************************/
	function order_list($parameters){
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"file_list",__LINE__,print_r($parameters,true)));
		}
		$this->page_size=100;
		$sql = "Select 
					*  
				from payment_account_orders
					inner join contact_data on pao_user = contact_user and pao_client=contact_client 
					inner join user_info on pao_user = user_identifier and pao_client=user_client 
				where 
					pao_client=$this->client_identifier
				order by pao_identifier desc";
		if ($this->module_debug){
			$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[$sql]"));
		}
		$result = $this->call_command("DB_QUERY",array($sql));
		$variables = Array();
		$variables["FILTER"]			= $this->filter($parameters);
		$variables["NUMBER_OF_ROWS"]	= 0;
		$variables["START"]				= 0;
		$variables["FINISH"]			= 0;
		$variables["CURRENT_PAGE"]		= 0;
		$variables["NUMBER_OF_PAGES"]	= 0;
		$variables["ONLY_ONE"]  		= $this->check_parameters($parameters,"onlyone");
		$variables["PAGE_BUTTONS"] = Array(
			Array("CANCEL","",LOCALE_CANCEL,"","","","")
		);
		if ($this->call_command("DB_NUM_ROWS",Array($result))==0){
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records not returned]"));
			}
		}else{
			if ($this->module_debug){
				$this->call_command("UTILS_DEBUG_ENTRY",array($this->module_name,"SQL Statement",__LINE__,"[Records returned]"));
			}
			
			$page = $this->check_parameters($parameters,"page","1");
			$number_of_records = $this->call_command("DB_NUM_ROWS",array($result));
			$goto = ((--$page)*$this->page_size);
			
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
			$variables["PAGE_COMMAND"] = "FILES_LIST";
			$variables["NUMBER_OF_ROWS"]	= $number_of_records;
			$variables["START"]				= $goto;
			$variables["FINISH"]			= $finish;
			$variables["CURRENT_PAGE"]		= $page;
			$variables["NUMBER_OF_PAGES"]	= $num_pages;
			$start_page=intval($page/$this->page_size);
			$remainder = $page % $this->page_size;
			if ($remainder>0){
				$start_page++;
			}
			
			$variables["START_PAGE"]		= $start_page;
			
			if (($start_page+$this->page_size)>$num_pages)
			$end_page=$num_pages;
			else
			$end_page=$this->page_size;
			
			$variables["END_PAGE"]			= $end_page;
			
			$variables["RESULT_ENTRIES"] =Array();
			$list ="";
			$entry = Array();
            $result  = $this->call_command("DB_QUERY",Array($sql));
            while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$name = $this->check_parameters($r,"contact_first_name")." ".$this->check_parameters($r,"contact_last_name");
				if($name == " "){
					$name = $this->check_parameters($r,"user_login_name");
				}
            	$entry[$r["pao_identifier"]] = Array(
					"props"			=> Array(),
					"Owner"			=> $name,
					"Date Ordered"	=> $r["pao_sent"],
					"Date Approved"	=> $r["pao_approved"],
					"Total"			=> $this->check_parameters($r,"total")
				);
				if($list!=""){
					$list .=",";
				}
				$list .= $r["pao_identifier"];
            }
            $this->call_command("DB_FREE",Array($result));
			$sql = "select * from payment_order_properties where pop_identifier in ($list) pop_client = $this->client_identifier";
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$entry[$r["pop_identifier"]]["props"][count($entry[$r["pop_identifier"]]["props"])] = Array("property"=>$r["pop_property"],"value"=>$r["pop_value"]);
			}
			$counter=0;
			foreach ($entry as $key => $value){
				$variables["RESULT_ENTRIES"][$counter]=Array(
					"identifier"		=> $key,
					"ENTRY_BUTTONS" 	=> Array(),
					"attributes"		=> Array(
						Array(LOCALE_LABEL, $entry["props"]["desc"], "TITLE", "NO"),
						Array(LOCAL_USER, 	$entry["Owner"]),
						Array("Date Ordered", 	$entry["Date Ordered"]),
						Array("Date Approved", 	$entry["Date Approved"]),
						Array(LOCAL_TOTAL, 	$entry["Total"])
					)
				);
				$variables["RESULT_ENTRIES"][$counter]["ENTRY_BUTTONS"][count($variables["RESULT_ENTRIES"][$i]["ENTRY_BUTTONS"])]	=	Array("PREVIEW",$this->module_command."PREVIEW",LOCALE_PREVIEW);
				$counter++;
			}
		}
		$variables["as"] = "table";
		$out = $this->generate_list($variables);
		return $out;
	}
	
}

?>