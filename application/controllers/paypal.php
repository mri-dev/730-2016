<?	
use PayPal\IPNReceiver;

class paypal extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'PayPal';

			if( $this->view->gets[1] == '' ){
				Helper::reload( '/user' );
			}

				/*
			error_reporting(E_ALL|E_STRICT);
			ini_set('display_errors', '1');
			*/
			

		}

		/**************************************** 
		* PayPal - IPN
		****************************************/
		function ipn(){
			$this->shop = $this->model->open('Shop');			
			
			$paypal = new IPNReceiver( 'live' );
			$data = $paypal->receiving();
			
			$msg = json_encode( $data['msg'], JSON_UNESCAPED_UNICODE );
			$status = $data['status'];
			$type = $data['msg']['payment_status'];
			
			
			$order = $this->shop->getOrderData( $data['msg']['invoice'] );
			
			// Save status
			// Folyamatbanlévőnek jelez
			if( $type == 'Pending' ) {
				$this->model->db->query("UPDATE ".TAGS::DB_TABLE_ORDERS." SET payu_state = 'IN_PROGRESS', status = 'Folyamatban' WHERE orderKey = '{$data['msg']['invoice']}';");
			}
			
			// Befejezettnek jelez
			if( $type == 'Completed' ) {
				$this->model->db->query("UPDATE ".TAGS::DB_TABLE_ORDERS." SET payu_state = 'COMPLETE', status = 'Fizetve - Postázás alatt' WHERE orderKey = '{$data['msg']['invoice']}';");
				
				// Üzenet a fizetésről
				if( $order[alerted_user_about_pay] == 0 ){
					$email 	= $order[user_data][email];

					$emsg 	= ''; 

					if( strtoupper($order[priceCode]) != 'HUF' ){
						$tema =  $data['msg']['invoice'] . ' order: successful payment';
						$emsg .= '<h2> Dear '.$order[user_data][szam_firstname].' '.$order[user_data][szam_lastname].'!</h2><br/>';
						$emsg .= 'Notify you that your order ID '.$data['msg']['invoice'].' has been successfull paid.<br/><br/>';
						$emsg .= '<strong>Transaction details:</strong><br>';
						$emsg .= 'PayPal transaction ID: '.$data[msg]['txn_id'] . '<br>';
						$emsg .= 'Date: '.$data[msg]['payment_date'];
					}else{
						$tema =  $data['msg']['invoice'] . ' megrendelés: fizetését fogadtuk ';
						$emsg .= '<h2> Tisztelt '.$order[user_data][szam_firstname].' '.$order[user_data][szam_lastname].'!</h2><br/>';
						$emsg .= 'Értesítjük, hogy a(z) '.$data['msg']['invoice'].' azonosítójú megrendelését sikeresen kifizette.<br/><br/>';
						$emsg .= '<strong>Tranzakció adatok:</strong><br>';
						$emsg .= 'PayPal tranzakció azonosító: '.$data[msg]['txn_id'].'<br>';
						$emsg .= 'Időpont: '.$data[msg]['payment_date'].'<br>';
					}

					Helper::sendMail(array(
						'recepiens' => array($email),
						'msg' 	=> $emsg,
						'tema' 	=> $tema,
						'from' 	=> NOREPLY_EMAIL,
						'sub' 	=> $tema
					));

					$this->model->db->update(
						'orders',
						array(
							'alerted_user_about_pay' => 1
						),
						"ID = ".$order[ID]
					);
				}
			}
			
			// Log IPN transaction
			$this->model->db->query("INSERT INTO ".TAGS::DB_TABLE_PAYPAL_IPN_TRANSACTIONS."( trans_type, msg, status) VALUES( '$type', '$msg', '$status' )");
		}
		
		function __destruct(){}
	}
?>