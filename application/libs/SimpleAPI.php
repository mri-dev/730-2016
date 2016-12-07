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
	public static function docURL()
	{
		$en = 'http://simplepartner.hu/PaymentService/Payment_information.pdf';
		$hu = 'http://simplepartner.hu/PaymentService/Fizetesi_tajekoztato.pdf';
	}

}

?>
