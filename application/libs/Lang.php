<?
  class Lang{
	private static $lang_root 			= 'lang';
	private static $language_content 	= false;
	private static $avaiable_currency 	= array('HUF','USD','EUR');

	const COUNTRY_CURRENCY_COOKIE = '__countryCurrency';

	public static function content($string){
		$langfile = self::$lang_root .'/'. self::getLang() . '.txt';

		if(file_exists($langfile)){

			if(!self::$language_content){
				$ctx 	= @file_get_contents($langfile);
				self::$language_content = $ctx;
			}else{
				$ctx = self::$language_content;
			}

			$src 	= self::formatToArray($ctx);

			$string = (array_key_exists($string,$src))?$src[$string]:$string;

			return $string;
		}else{
			return $string;
		}
	}

	public static function getAvaiableCurrencies(){
		$currency = array();

		foreach( self::$avaiable_currency as $c ){
			$currency[] = array(
				'text' => $c,
				'code' => strtolower($c),
				'active' => ( (self::getPriceCode() == strtoupper($c)) ? true : false )
			);
		}

		return $currency;
	}

	public static function getActiveCurrency(){

		$currency = false;

		foreach( self::$avaiable_currency as $c ){
			if(self::getPriceCode() == strtoupper($c)){
				$currency = array(
					'text' => $c,
					'code' => strtolower($c)
				);
			}
		}

		return $currency;
	}

	private static function formatToArray($str){
		$arr = array();
		$a_str = explode(';;',rtrim($str,';;'));
		foreach($a_str as $as){
			$b_str = explode('::',$as);
			$arr[trim($b_str[0])] = trim($b_str[1]);
		}

		return $arr;
	}

	public static function setLang($langKey){
		setcookie('lang', $langKey, time() + 60*60*6, '/');

		switch( $langKey ){
			case 'hu':
				setcookie(self::COUNTRY_CURRENCY_COOKIE, 'huf', time() + 60*60*24*7,'/');
			break;
			case 'en':
				setcookie(self::COUNTRY_CURRENCY_COOKIE, 'eur', time() + 60*60*24*7,'/');
			break;
			case 'ger':
				setcookie(self::COUNTRY_CURRENCY_COOKIE, 'eur', time() + 60*60*24*7,'/');
			break;
			case 'rus':
				setcookie(self::COUNTRY_CURRENCY_COOKIE, 'eur', time() + 60*60*24*7,'/');
			break;
		}

	}

	public static function setCurrency($code){

		if( empty( $code ) ) return false;

		if( !in_array( $code, self::$avaiable_currency ) ) return false;

		setcookie(self::COUNTRY_CURRENCY_COOKIE, strtolower($code), time() + 60*60*24*7,'/');
	}

	public static function getLang(){
		$lang = DLANG;

		$pricecode = $_COOKIE[self::COUNTRY_CURRENCY_COOKIE];

		if($pricecode != ''){
			switch($c){
				default:
					$lang = 'hu';
				break;
				case 'huf':
					$lang = 'hu';
				break;
				case 'eur':
					$lang = 'en';
				break;
				case 'usd':
					$lang = 'en';
				break;
			}
		}

		if(strpos($_SERVER[REQUEST_URI], C_ADMROOT) === 0) return DLANG;

		if($_COOKIE[lang] != ''){
			$lang = $_COOKIE[lang];
		}

		return $lang;
	}

	public static function getPriceCode(){
		$c 		= false;
		$code 	= 'HUF';

		if( $_COOKIE[self::COUNTRY_CURRENCY_COOKIE] == '' ){
			//$getCode = self::ip_info(NULL, 'currencycode');
			if( !$getCode ){
				$getCode = $code;
			}

			//setcookie( self::COUNTRY_CURRENCY_COOKIE, $getCode, time() + 60*60*24*7, '/' );

			$c = $getCode;
		}else{
			$c = $_COOKIE[self::COUNTRY_CURRENCY_COOKIE];
		}

		$code = strtoupper( $c );
		/*
		switch($c){
			default:
				$code = strtoupper('USD');
			break;
			case 'HUF':
				$code = strtoupper('HUF');
			break;
			case 'EUR':
				$code = strtoupper('EUR');
			break;
			case 'USD':
				$code = strtoupper('USD');
			break;
		}*/

		return $code;
	}

	public static function ip_info($ip = NULL, $purpose = "countrycode", $deep_detect = TRUE) {
		$output = NULL;
		if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
			$ip = $_SERVER["REMOTE_ADDR"];
			if ($deep_detect) {
				if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
				if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
					$ip = $_SERVER['HTTP_CLIENT_IP'];
			}
		}
		$purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
		$support    = array("country", "countrycode", "currencycode", "state", "region", "city", "location", "address");
		$continents = array(
			"AF" => "Africa",
			"AN" => "Antarctica",
			"AS" => "Asia",
			"EU" => "Europe",
			"OC" => "Australia (Oceania)",
			"NA" => "North America",
			"SA" => "South America"
		);
		if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
			$ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));

			if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
				switch ($purpose) {
					case "location":
						$output = array(
							"city"           => @$ipdat->geoplugin_city,
							"state"          => @$ipdat->geoplugin_regionName,
							"country"        => @$ipdat->geoplugin_countryName,
							"country_code"   => @$ipdat->geoplugin_countryCode,
							"continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
							"continent_code" => @$ipdat->geoplugin_continentCode,
							"currency_code"  => @$ipdat->geoplugin_currencyCode,
						);
						break;
					case "address":
						$address = array($ipdat->geoplugin_countryName);
						if (@strlen($ipdat->geoplugin_regionName) >= 1)
							$address[] = $ipdat->geoplugin_regionName;
						if (@strlen($ipdat->geoplugin_city) >= 1)
							$address[] = $ipdat->geoplugin_city;
						$output = implode(", ", array_reverse($address));
						break;
					case "city":
						$output = @$ipdat->geoplugin_city;
						break;
					case "state":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "region":
						$output = @$ipdat->geoplugin_regionName;
						break;
					case "country":
						$output = @$ipdat->geoplugin_countryName;
						break;
					case "countrycode":
						$output = @$ipdat->geoplugin_countryCode;
						break;
					case "currencycode":
						$output = @$ipdat->geoplugin_currencyCode;
						break;
				}
			}
		}
		return $output;
	}
  }
?>
