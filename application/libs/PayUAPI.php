<?

class PayUAPI{
	public static function getConfigFile(){
		return PAYU_PATH."sdk/config.php";
	}
	public static function getPaymentFile(){
		return PAYU_PATH."sdk/PayUPayment.class.php";
	}
	public static function getPaymentExtraFile(){
		return PAYU_PATH."sdk/PayUPaymentExtra.class.php";
	}

}

?>