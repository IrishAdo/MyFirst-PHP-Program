<?PHP
/**
* @company Libertas Solutions Ltd
* @package com.solutions.libertas.cms
* @author Adrian Sweeney
* @file libertas.mirror.php
* @date 09 Oct 2002
*/
/**
* Language module 
*/
class languages extends module{
	/**
	*  Class Variables
	*/
	var $module_name="languages";
	var $module_name_label="Language Management";
	var $module_label="Language Management";
	var $module_grouping="";
	var $module_admin="0";
	var $module_debug=false;
	var $module_creation="05/02/2003";
	var $module_modify	 		= '$Date: 2005/02/18 18:44:52 $';
	var $module_version 			= '$Revision: 1.14 $';
	var $module_command="LANGUAGE_"; 		// all commands specifically for this module will start with this token
	var $module_display_options=Array();
	var $module_admin_options = array();
	var $module_admin_user_access = array();
	
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
			/*
				module specific commands
			*/
			if ($user_command==$this->module_command."UPDATE_DEBUG"){
				$this->debug_update($parameter_list);
				$user_command=$this->module_command."DEBUG_ADMIN";
			}
			if ($user_command==$this->module_command."DEBUG_ADMIN"){
				return $this->debug_admin($parameter_list);
			}
			if ($user_command==$this->module_command."UPDATE_SYSTEM_PREFS"){
				$this->system_admin_save($parameter_list);
				$user_command=$this->module_command."SYSTEM_ADMIN";
			}
			if ($user_command==$this->module_command."SYSTEM_ADMIN"){
				return $this->system_admin($parameter_list);
			}
			if ($user_command==$this->module_command."SPLASH"){
				return $this->splash($parameter_list);
			}
			if ($user_command==$this->module_command."EXTRACT_SYSTEM_PREFERENCE"){
				return $this->extract_system_preference($parameter_list);
			}
			if ($user_command==$this->module_command."GET_COUNTRIES"){
				return $this->get_countries($parameter_list);
			}
		}else{
			return ""; // wrong command sent to system
		}
	}
	/**
	* call the initialisation function only when this module is created
	*/
	
	function initialise(){
		/**
		* request the client identifier once we use this variable often
		*/
		$this->client_identifier = $this->parent->client_identifier;
		/**
		* define some access functionality
		*/
		$this->module_admin_options			= array();
		$this->module_admin_user_access		= array();
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
		* Table structure for table 'available_languages' table
		*/
		$fields = array(
		array("language_code"				,"varchar(10)"		,"NOT NULL"	,"default ''"),
		array("language_label"				,"varchar(255)"		,"NOT NULL"	,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("available_languages", $fields, $primary, $this->system_default_data(1));
		/**
		* Table structure for table 'country_area_lookup' table
		*/
		$fields = array(
		array("cal_code"				,"varchar(2)"		,"NOT NULL"	,"default ''"),
		array("cal_label"				,"varchar(255)"		,"NOT NULL"	,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("country_area_lookup", $fields, $primary, $this->system_default_data(2));
		/**
		* Table structure for table 'avaialble country_lookup' table
		*/
		$fields = array(
			array("cl_identifier"			,"unsigned integer"	,"NOT NULL"	,"auto_increment","key"),
			array("cl_country"				,"varchar(255)"		,"NOT NULL"	,"default ''"),
			array("cl_abbr"					,"varchar(2)"		,"NOT NULL"	,"default ''"),
			array("cl_number"				,"unsigned integer"	,"NOT NULL"	,"default '0'"),
			array("cl_area"					,"varchar(2)"		,"NOT NULL"	,"default ''")
		);
		$primary ="";
		$tables[count($tables)] = array("country_lookup", $fields, $primary, $this->system_default_data(3));
		
		
		return $tables;
	}
	
	
	function system_default_data($type){
		$i=0;
		$sql = Array();
		if($type==1){
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-us', 'English (United States)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('af', 'Afrikaans');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sq', 'Albanian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar', 'Arabic');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-sa', 'Arabic (Saudi Arabia)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-iq', 'Arabic (Iraq)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-eg', 'Arabic (Egypt)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-ly', 'Arabic (Libya)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-dz', 'Arabic (Algeria)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-ma', 'Arabic (Morocco)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-tn', 'Arabic (Tunisia)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-om', 'Arabic (Oman)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-ye', 'Arabic (Yemen)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-sy', 'Arabic (Syria)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-jo', 'Arabic (Jordan)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-lb', 'Arabic (Lebanon)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-kw', 'Arabic (Kuwait)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-ae', 'Arabic (U.A.E.)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-bh', 'Arabic (Bahrain)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ar-qa', 'Arabic (Qatar)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('hy', 'Armenian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('as', 'Assamese');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('az', 'Azeri');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('eu', 'Basque');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('bn', 'Bengali');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('bg', 'Bulgarian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('be', 'Belarusian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ca', 'Catalan');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('zh', 'Chinese');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('zh', 'Chinese (Macau)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('zh-tw', 'Chinese (Taiwan)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('zh-cn', 'Chinese (PRC)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('zh-hk', 'Chinese (Hong Kong)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('zh-sg', 'Chinese (Singapore)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('hr', 'Croatian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('cs', 'Czech');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('da', 'Danish');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('nl', 'Dutch (Netherlands)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('nl-be', 'Dutch (Belgium)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en', 'English');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-gb', 'English (United Kingdom)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-au', 'English (Australia)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-ca', 'English (Canada)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-nz', 'English (New Zealand)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-ie', 'English (Ireland)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-za', 'English (South Africa)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-zw', 'English (Zimbabwe)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-jm', 'English (Jamaica)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-bz', 'English (Belize)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-tt', 'English (Trinidad)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('en-ph', 'English (Philippines)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('et', 'Estonian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('fo', 'Faeroese');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('fa', 'Farsi');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('fi', 'Finnish');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('fr', 'French (France)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('fr-be', 'French (Belgium)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('fr-ca', 'French (Canada)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('fr-ch', 'French (Switzerland)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('fr-lu', 'French (Luxembourg)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('fr-mc', 'French (Monaco)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('gd', 'Gaelic');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ka', 'Georgian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('de', 'German (Germany)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('de-ch', 'German (Switzerland)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('de-at', 'German (Austria)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('de-lu', 'German (Luxembourg)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('de-li', 'German (Liechtenstein)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('el', 'Greek');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('gu', 'Gujarati');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('he', 'Hebrew');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('hi', 'Hindi');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('hu', 'Hungarian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('is', 'Icelandic');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('id', 'Indonesian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('it', 'Italian (Italy)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('it-ch', 'Italian (Switzerland)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ja', 'Japanese');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('kn', 'Kannada');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('kk', 'Kazakh');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('x-kok', 'Konkani');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ko', 'Korean');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('lv', 'Latvian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('lt', 'Lithuanian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('mk', 'Macedonian (FYROM)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ms', 'Malay (Malaysia)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('mt', 'Maltese');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('mr', 'Marathi');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ms', 'Malay (Brunei Darussalam)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ml', 'Malayalam');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ne', 'Nepali (India)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('no', 'Norwegian (Bokmal)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('no', 'Norwegian (Nynorsk)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('or', 'Oriya');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('pl', 'Polish');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('pt-br', 'Portuguese (Brazil)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('pt', 'Portuguese (Portugal)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('pa', 'Punjabi');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('rm', 'Rhaeto-Romanic');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ro', 'Romanian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ro-mo', 'Romanian (Moldova)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ru', 'Russian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ru-mo', 'Russian (Moldova)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sa', 'Sanskrit');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sr', 'Serbian (Cyrillic)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sr', 'Serbian (Latin)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sk', 'Slovak');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sl', 'Slovenian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sb', 'Sorbian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-mx', 'Spanish (Mexico)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es', 'Spanish');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-gt', 'Spanish (Guatemala)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-cr', 'Spanish (Costa Rica)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-pa', 'Spanish (Panama)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-do', 'Spanish (Dominican Republic)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-ve', 'Spanish (Venezuela)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-co', 'Spanish (Colombia)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-pe', 'Spanish (Peru)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-ar', 'Spanish (Argentina)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-ec', 'Spanish (Ecuador)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-cl', 'Spanish (Chile)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-uy', 'Spanish (Uruguay)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-py', 'Spanish (Paraguay)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-bo', 'Spanish (Bolivia)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-sv', 'Spanish (El Salvador)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-hn', 'Spanish (Honduras)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-ni', 'Spanish (Nicaragua)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('es-pr', 'Spanish (Puerto Rico)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sx', 'Sutu');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sw', 'Swahili');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sv', 'Swedish');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('sv-fi', 'Swedish (Finland)');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ta', 'Tamil');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('tt', 'Tatar');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('te', 'Telugu');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('th', 'Thai');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ts', 'Tsonga');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('tn', 'Tswana');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('tr', 'Turkish');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('uk', 'Ukrainian');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('ur', 'Urdu');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('uz', 'Uzbek');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('vi', 'Vietnamese');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('xh', 'Xhosa');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('yi', 'Yiddish');";
		$sql[$i++] ="insert into available_languages (language_code, language_label) values ('zu', 'Zulu');";
		}
		if($type==2){
		$sql[$i++]="insert into country_area_lookup (cal_code,cal_label) values
						('AF','Africa'),
						('AR','Antarctic'),
						('AS','Asia'),
						('CA','Caribbean'),
						('CE','Central Americas'),
						('EU','Europe'),
						('IO','Indian Ocean'),
						('ME','Middle East'),
						('NA','North America'),
						('OC','Oceania'),
						('SA','South America');";
		}
		if($type==3){
		$sql[$i++] = "insert into country_lookup (cl_country, cl_abbr, cl_number, cl_area) values
						('Algeria','DZ','012','AF'),
						('Angola','AO','024','AF'),
						('Benin','BJ','204','AF'),
						('Botswana','BW','072','AF'),
						('Burkina Faso','BF','854','AF'),
						('Burundi','BI','108','AF'),
						('Cameroon','CM','120','AF'),
						('Cape Verde','CV','132','AF'),
						('Central African Republic','CF','140','AF'),
						('Chad','TD','148','AF'),
						('Congo Democratic Republic of','CG','178','AF'),
						('Congo Republic of (Zaire)','CD','180','AF'),
						('Cote D’Ivoire','CI','384','AF'),
						('Djibouti','DJ','262','AF'),
						('Egypt','EG','818','AF'),
						('Equatorial Guinea','GQ','226','AF'),
						('Eritrea','ER','232','AF'),
						('Ethiopia','ET','231','AF'),
						('Gabon','GA','266','AF'),
						('Gambia','GM','270','AF'),
						('Ghana','GH','288','AF'),
						('Guinea','GN','324','AF'),
						('Guinea-Bissau','GW','624','AF'),
						('Kenya','KE','404','AF'),
						('Lesotho','LS','426','AF'),
						('Liberia','LR','430','AF'),
						('Libya','LY','434','AF'),
						('Malawi','MW','454','AF'),
						('Mali','ML','466','AF'),
						('Mauritania','MR','478','AF'),
						('Morocco','MA','504','AF'),
						('Mozambique','MZ','508','AF'),
						('Namibia','NA','516','AF'),
						('Niger','NE','562','AF'),
						('Nigeria','NG','566','AF'),
						('Rwanda','RW','646','AF'),
						('Sao Tome and Principe','ST','678','AF'),
						('Senegal','SN','686','AF'),
						('Sierra Leone','SL','694','AF'),
						('Somalia','SO','706','AF'),
						('South Africa','ZA','710','AF'),
						('St. Helena','SH','654','AF'),
						('Sudan','SD','736','AF'),
						('Swaziland','SZ','748','AF'),
						('Tanzania','TZ','834','AF'),
						('Togo','TG','768','AF'),
						('Tunisia','TN','788','AF'),
						('Uganda','UG','800','AF'),
						('Western Sahara','EH','732','AF'),
						('Zambia','ZM','894','AF'),
						('Zimbabwe','ZW','716','AF'),
						('Antarctica','AQ','010','AR'),
						('Bouvet Island','BV','074','AR'),
						('French Southern Territories','TF','260','AR'),
						('Heard Island and McDonald Islands','HM','334','AR'),
						('South Georgia and the South Sandwich Islands','GS','239','AR'),
						('Afghanistan','AF','004','AS'),
						('Bangladesh','BD','050','AS'),
						('Bhutan','BT','064','AS'),
						('Brunei','BN','096','AS'),
						('Cambodia','KH','116','AS'),
						('China','CN','156','AS'),
						('East Timor','TP','626','AS'),
						('Hong Kong','HK','344','AS'),
						('India','IN','356','AS'),
						('Indonesia','ID','360','AS'),
						('Japan','JP','392','AS'),
						('Kazakhstan','KZ','398','AS'),
						('Kyrgyzstan','KG','417','AS'),
						('Laos','LA','418','AS'),
						('Macau','MO','446','AS'),
						('Malaysia','MY','458','AS'),
						('Mongolia','MN','496','AS'),
						('Myanmar','MM','104','AS'),
						('Nepal','NP','524','AS'),
						('North Korea','KP','408','AS'),
						('Pakistan','PK','586','AS'),
						('Philippines','PH','608','AS'),
						('Russia','RU','643','AS'),
						('Singapore','SG','702','AS'),
						('South Korea','KR','410','AS'),
						('Taiwan','TW','158','AS'),
						('Tajikistan','TJ','762','AS'),
						('Thailand','TH','764','AS'),
						('Turkmenistan','TM','795','AS'),
						('Uzbekistan','UZ','860','AS'),
						('Vietnam','VN','704','AS'),
						('Belize','BZ','084','CE'),
						('Costa Rica','CR','188','CE'),
						('El Salvador','SV','222','CE'),
						('Guatemala','GT','320','CE'),
						('Honduras','HN','340','CE'),
						('Nicaragua','NI','558','CE'),
						('Panama','PA','591','CE'),
						('Anguilla','AI','660','CA'),
						('Antigua and Barbuda','AG','028','CA'),
						('Aruba','AW','533','CA'),
						('Bahamas','BS','044','CA'),
						('Barbados','BB','052','CA'),
						('Bermuda','BM','060','CA'),
						('British Virgin Islands','VG','092','CA'),
						('Cayman Islands','KY','136','CA'),
						('Cuba','CU','192','CA'),
						('Dominica','DM','212','CA'),
						('Dominican Republic','DO','214','CA'),
						('Grenada','GD','308','CA'),
						('Guadeloupe','GP','312','CA'),
						('Haiti','HT','332','CA'),
						('Jamaica','JM','388','CA'),
						('Martinique','MQ','474','CA'),
						('Montserrat','MS','500','CA'),
						('Netherlands Antilles','AN','530','CA'),
						('Puerto Rico','PR','630','CA'),
						('St. Kitts and Nevis','KN','659','CA'),
						('St. Lucia','LC','662','CA'),
						('St. Vincent and the Grenadines','VC','670','CA'),
						('Trinidad and Tobago','TT','780','CA'),
						('Turks and Caicos Islands','TC','796','CA'),
						('U.S. Virgin Islands','VI','850','CA'),
						('Albania','AL','008','EU'),
						('Andorra','AD','020','EU'),
						('Armenia','AM','051','EU'),
						('Austria','AT','040','EU'),
						('Azerbaijan','AZ','031','EU'),
						('Belarus','BY','112','EU'),
						('Belgium','BE','056','EU'),
						('Bosnia and Herzegovina','BA','070','EU'),
						('Bulgaria','BG','100','EU'),
						('Croatia','HR','191','EU'),
						('Cyprus','CY','196','EU'),
						('Czech Republic','CZ','203','EU'),
						('Denmark','DK','208','EU'),
						('Estonia','EE','233','EU'),
						('Faroe Islands','FO','234','EU'),
						('Finland','FI','246','EU'),
						('France','FR','250','EU'),
						('Georgia','GE','268','EU'),
						('Germany','DE','276','EU'),
						('Gibraltar','GI','292','EU'),
						('Greece','GR','300','EU'),
						('Greenland','GL','304','EU'),
						('Hungary','HU','348','EU'),
						('Iceland','IS','352','EU'),
						('Ireland','IE','372','EU'),
						('Italy','IT','380','EU'),
						('Latvia','LV','428','EU'),
						('Liechtenstein','LI','438','EU'),
						('Lithuania','LT','440','EU'),
						('Luxembourg','LU','442','EU'),
						('Macedonia','MK','807','EU'),
						('Malta','MT','470','EU'),
						('Metropolitan France','FX','249','EU'),
						('Moldova','MD','498','EU'),
						('Monaco','MC','492','EU'),
						('Netherlands','NL','528','EU'),
						('Norway','NO','578','EU'),
						('Poland','PL','616','EU'),
						('Portugal','PT','620','EU'),
						('Romania','RO','642','EU'),
						('San Marino','SM','674','EU'),
						('Slovakia','SK','703','EU'),
						('Slovenia','SI','705','EU'),
						('Spain','ES','724','EU'),
						('Svalbard and Jan Mayen Islands','SJ','744','EU'),
						('Sweden','SE','752','EU'),
						('Switzerland','CH','756','EU'),
						('Turkey','TR','792','EU'),
						('Ukraine','UA','804','EU'),
						('United Kingdom (GB)','UK','826','EU'),
						('Northern Ireland','UK','826','EU'),
						('Vatican City','VA','336','EU'),
						('Yugoslavia','YU','891','EU'),
						('British Indian Ocean Territory','IO','086','IO'),
						('Christmas Island','CX','162','IO'),
						('Cocos (Keeling) Islands','CC','166','IO'),
						('Comoros','KM','174','IO'),
						('Madagascar','MG','450','IO'),
						('Maldives','MV','462','IO'),
						('Mauritius','MU','480','IO'),
						('Mayotte','YT','175','IO'),
						('Reunion','RE','638','IO'),
						('Seychelles','SC','690','IO'),
						('Sri Lanka','LK','144','IO'),
						('Bahrain','BH','048','ME'),
						('Iran','IR','364','ME'),
						('Iraq','IQ','368','ME'),
						('Israel','IL','376','ME'),
						('Jordan','JO','400','ME'),
						('Kuwait','KW','414','ME'),
						('Lebanon','LB','422','ME'),
						('Oman','OM','512','ME'),
						('Qatar','QA','634','ME'),
						('Saudi Arabia','SA','682','ME'),
						('Syria','SY','760','ME'),
						('United Arab Emirates','AE','784','ME'),
						('Yemen','YE','887','ME'),
						('Canada','CA','124','NA'),
						('Mexico','MX','484','NA'),
						('St. Pierre and Miquelon','PM','666','NA'),
						('United States','US','840','NA'),
						('American Samoa','AS','016','OC'),
						('Australia','AU','036','OC'),
						('Cook Islands','CK','184','OC'),
						('Federated States of Micronesia','FM','583','OC'),
						('Fiji','FJ','242','OC'),
						('French Polynesia','PF','258','OC'),
						('Guam','GU','316','OC'),
						('Kiribati','KI','296','OC'),
						('Marshall Islands','MH','584','OC'),
						('Nauru','NR','520','OC'),
						('New Caledonia','NC','540','OC'),
						('New Zealand','NZ','554','OC'),
						('Niue','NU','570','OC'),
						('Norfolk Island','NF','574','OC'),
						('Northern Mariana Islands','MP','580','OC'),
						('Palau','PW','585','OC'),
						('Papua New Guinea','PG','598','OC'),
						('Pitcairn','PN','612','OC'),
						('Samoa','WS','882','OC'),
						('Solomon Islands','SB','090','OC'),
						('Tokelau','TK','772','OC'),
						('Tonga','TO','776','OC'),
						('Tuvalu','TV','798','OC'),
						('United States Minor Outlying Islands','UM','581','OC'),
						('Vanuatu','VU','548','OC'),
						('Wallis and Futuna Islands','WF','876','OC'),
						('Argentina','AR','032','SA'),
						('Bolivia','BO','068','SA'),
						('Brazil','BR','076','SA'),
						('Chile','CL','152','SA'),
						('Colombia','CO','170','SA'),
						('Ecuador','EC','218','SA'),
						('Falkland Islands','FK','238','SA'),
						('French Guyana','GF','254','SA'),
						('Guyana','GY','328','SA'),
						('Paraguay','PY','600','SA'),
						('Peru','PE','604','SA'),
						('Suriname','SR','740','SA'),
						('Uruguay','UY','858','SA'),
						('Venezuela','VE','862','SA');";
		}
		return $sql;
	}
	
	function get_countries($parameters){
		$selected  			= $this->check_parameters($parameters,"selected");
		$restrict_country	= $this->check_parameters($parameters,"restrict_country",0);
		$as					= $this->check_parameters($parameters,"as","__XML__");
		if($restrict_country==0){
			$sql = "select cl_country, cl_identifier, cal_label from country_lookup 
						inner join country_area_lookup on cl_area =cal_code
					order by cal_label, cl_country";
		} else {
			$sql		= "select * from shop_weight_matrix where swm_client=$this->client_identifier";
			//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
			$regions	= "";
			$countries	= "";
			$result 	= $this->parent->db_pointer->database_query($sql);
            while($r 	= $this->parent->db_pointer->database_fetch_array($result)){
				if($r["swm_country"]==-1){
					if($regions != ""){
						$regions .= ", ";
					}
					$regions .= "'".$r["swm_region"]."'";
				}
				if($r["swm_country"]!=-1){
					if($countries != ""){
						$countries .= ", ";
					}
					$countries .= $r["swm_country"];
				}
            }
			$where = "";
			if($regions!=""){
				$where .= " cl_area in ($regions)";
			}
			if($countries!=""){
				if($where != ""){
					$where .= " or ";
				}
				$where .= " cl_identifier in ($countries)";
			}
			if ($where !=""){
				$where = "where ($where)";
			}
            $this->parent->db_pointer->database_free_result($result);
			$sql = "select distinct cl_country, cl_identifier, cal_label from country_lookup 
						inner join country_area_lookup on cl_area =cal_code
					$where 
					order by cal_label, cl_country";
//						inner join shop_weight_matrix on swm_country = cl_identifier and swm_client= $this->client_identifier
			//print "<li>".__FILE__."@".__LINE__."<p>$sql</p></li>";
		}
        $result  = $this->call_command("DB_QUERY",Array($sql));
		$section ="";
		if($as=="__XML__"){
			$out="";
			$out .= "<optgroup label='Select One'>";
			$out .= "<option value=''>Select One</option>";
			$out .= "</optgroup>";
			$c=0;
	        while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$c++;
				$this_section = $r["cal_label"];
	        	if ($this_section != $section){
					if($section!=""){
						$out .= "</optgroup>";
					}
					$section = $this_section;
					$out .= "<optgroup label='".$r["cal_label"]."'>";
				}
				$out .= "<option value='".$r["cl_identifier"]."'";
				$cval = $r["cl_identifier"];
				$ctxt = $r["cl_country"];
				if($selected==$r["cl_identifier"]){
					$out .= " selected='true'";
				}
				$out .= "><![CDATA[".$r["cl_country"]."]]></option>";
	        }
			if($out !=""){
				$out .= "</optgroup>";
			}
	        $this->call_command("DB_FREE",Array($result));
			if($c==1){
				return Array("hide",$cval,$ctxt);
			}
		} else {
			$out=Array();
			$result  = $this->call_command("DB_QUERY",Array($sql));
			$i=0;
		    while($r = $this->call_command("DB_FETCH_ARRAY",Array($result))){
				$selectedValue="false";
				if($selected==$r["cl_identifier"]){
					$selectedValue="true";
				}
	        	$out[$i] = Array("label"=>$r["cl_country"], "value"=>$r["cl_identifier"],"section"=>$r["cal_label"],"selected"=>$selectedValue);
				$i++;
	        }
	        $this->call_command("DB_FREE",Array($result));
		}
		return $out;
	}
	function get_country($parameters){
		$selected  = $this->check_parameters($parameters,"selected");
	}
}
?>