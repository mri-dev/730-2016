<?

class SimpleAPI{
	public static function getConfigFile(){
		return SIMPLE_PATH."/config.php";
	}
	public static function getPaymentFile(){
		return SIMPLE_PATH."/PayUPayment.class.php";
	}
	public static function getPaymentExtraFile(){
		return SIMPLE_PATH."/PayUPaymentExtra.class.php";
	}
}

?>
