<?	class payu extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'PayU - Kártyás fizetés';

			if( $this->view->gets[1] == '' ){
				Helper::reload( '/user' );
			}

				/*
			error_reporting(E_ALL|E_STRICT);
			ini_set('display_errors', '1');
			*/
			

		}

		/****************************************
		* PayU - Timeout
		****************************************/
		function timeout(){

			$message = '<div class="payu-timout">';

			if (@$_REQUEST['redirect']==1) {

				$message .= '<b><font color="red">'.__('Tranzakciót megszakította.').'</font></b><br/>';

			} else {

				$message .= '<b><font color="red">'.__('Tranzakciót időkorlátja lejárt.').'</font></b><br/>';

			}

			$message .= '<strong>'.__('Tranzakció adatok').':</strong><br/><br/>';
			$message .= __('Időpont').': <b>'.date('Y-m-d H:i:s', time()).'</b><br/>';
			$message .= __('Megrendelés azonosító').': <b>'.$_REQUEST['order_ref'].'</b><br/>';

			$message .= '</div>';

			$this->view->pay_msg = $message;
		}

		/**************************************** 
		* PayU - Backref
		****************************************/
		function back(){
			require_once( PayUAPI::getConfigFile() );
			require_once( PayUAPI::getPaymentFile() );

			$this->shop = $this->model->open('Shop');
			/**
			 * Set merchant account data by currency
			 */	
			$modifyConfig 	= new PayUModifyConfig($config);	
			$orderCurrency 	= (isset($_REQUEST['order_currency'])) ? $_REQUEST['order_currency'] : 'N/A';
			$config 		= $modifyConfig->merchantByCurrency($orderCurrency);


			/**
			 * Start backref
			 */		
			$backref = new PayUBackRef($config);		

			
			/**
			 * Add order reference number from merchant system (ORDER_REF)
			 */			
			$backref->order_ref = (isset($_REQUEST['order_ref'])) ? $_REQUEST['order_ref'] : 'N/A';


			/**
			 * Log to file
			 */		
			$backref->logger 	= $config['LOGGER'];
			$backref->log_path 	= $config['LOG_PATH'];


			/**
			 * Check backref
			 */		
			$message = '<div class="payu-back payu-back-'.( ($backref->checkResponse()) ? 'success' : 'error' ).'">';
			if($backref->checkResponse()){
				
				/**
				 * SUCCESSFUL card authorizing
				 * Notify user and wait for IPN
				 * Need to notify user
				 * 
				 */
				$message .= '<div class="head">'.__('Sikeres tranzakció!').'</div>';
				$backStatus = $backref->backStatusArray;
				

				$this->portal->setOrderPayState( $backStatus['REFNOEXT'], $backStatus['ORDER_STATUS'] );
				
				$order = $this->shop->getOrderData( $backStatus['REFNOEXT'] );


				// Üzenet a fizetésről
				if( $order[alerted_user_about_pay] == 0 ){
					$email 	= $order[user_data][email];

					$msg 	= ''; 

					if( strtoupper($order[priceCode]) != 'HUF' ){
						$tema =  $backStatus['REFNOEXT'] . ' order: successful payment';
						$msg .= '<h2> Dear '.$order[user_data][szam_firstname].' '.$order[user_data][szam_lastname].'!</h2><br/>';
						$msg .= 'Notify you that your order ID '.$backStatus['REFNOEXT'].' has been successfull paid.<br/><br/>';
						$msg .= '<strong>Transaction details:</strong><br>';
						$msg .= 'PayU transaction ID: '.$backStatus['PAYREFNO'];
						$msg .= 'Date: '.$backStatus['BACKREF_DATE'];
					}else{
						$tema =  $backStatus['REFNOEXT'] . ' megrendelés: fizetését fogadtuk ';
						$msg .= '<h2> Tisztelt '.$order[user_data][szam_firstname].' '.$order[user_data][szam_lastname].'!</h2><br/>';
						$msg .= 'Értesítjük, hogy a(z) '.$backStatus['REFNOEXT'].' azonosítójú megrendelését sikeresen kifizette.<br/><br/>';
						$msg .= '<strong>Tranzakció adatok:</strong><br>';
						$msg .= 'PayU tranzakció: '.$backStatus['PAYREFNO'].'<br>';
						$msg .= 'Időpont: '.$backStatus['BACKREF_DATE'].'<br>';
					}

					Helper::sendMail(array(
						'recepiens' => array($email),
						'msg' 	=> $msg,
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


				// Notification by payment method
				
				//CCVISAMC
				if ($backStatus['PAYMETHOD']=='Visa/MasterCard/Eurocard') {

					$message .= '<b><font color="green">'.__('Sikeres kártya ellenőrzés.').'</font></b><br/>';

					if ($backStatus['ORDER_STATUS']=='IN_PROGRESS') {

						$message .= '<b><font color="green">'.__('Tranzakció megerősítésre vár.').'</font></b><br/>';

					} elseif ($backStatus['ORDER_STATUS']=='PAYMENT_AUTHORIZED' || $backStatus['ORDER_STATUS']=='COMPLETED') {
						
						$message .= '<b><font color="green">'.__('Sikeres tranzakció!').'</font></b><br/>';

					} 
				}
				//WIRE
				elseif ($backStatus['PAYMETHOD']=='Bank/Wire transfer') {
					$message .= '<b><font color="green">'.__('Átutalás elfogadva.').'</font></b><br/>';
					if ($backStatus['ORDER_STATUS']=='PAYMENT_AUTHORIZED' || $backStatus['ORDER_STATUS']=='COMPLETED') {
						$message .= '<b><font color="green">'.__('Sikeres átutalás.').'</font></b><br/>';
					} 			
				}
				//CASH
				elseif ($backStatus['PAYMETHOD']=='Cash on delivery') {
						$message .= '<b><font color="green">'.__('Megrendelés elfogadva.').'</font></b><br/>';
				}
					
			} else {	

				/**
				 * UNSUCCESSFUL card authorizing
				 * END of transaction
				 * Need to notify user
				 * 
				 */
				$message .= '<div class="head">'.__('Tranzakció sikertelen').'</div>';
				$backStatus = $backref->backStatusArray;	
				
				/**
				 * Your code here
				 */	

				$message .= __('Kérjük, ellenőrizze a tranzakció során megadott adatok helyességét. Amennyiben minden adatot helyesen adott meg, a visszautasítás okának kivizsgálása kapcsán kérjük, szíveskedjen kapcsolatba lépni kártyakibocsátó bankjával.').'<br/><br/>';
			}

			/**
			 * Notification
			 */	
			//$message .= '<b>Kötelező tájékoztatás</b><br/>';  
			$message .= '<div class="ft">'.__('Tranzakció azonosító:').': <b class="d">'.$backStatus['PAYREFNO'].'</b></div>'; 
			$message .= '<div class="ft">'.__('Időpont').': <b class="d">'.$backStatus['BACKREF_DATE'].'</b></div>';
			$message .= '<div class="ft">'.__('Megrendelés azonosító').': <b class="d">'.$backStatus['REFNOEXT'].'</b></div>';
			if( false ):
			$message .= '<b><font color="red">Fejlesztési segítség, éles oldalon ne jelenjen meg!</font></b><br/>'; 
			$message .= 'STATUS: <b class="d">'.$backStatus['ORDER_STATUS'].'</b><br/>';	
			endif;
			$message .= '<a href="/user" class="btn btn-default" style="color:black;"><i class="fa fa-arrow-circle-left"></i> '.__('vissza a megrendelésekhez').'</a>';
			$message .= '</div>';


			/**
			 * Print generated message
			 */			
			//header('Content-Type: text/html; charset=utf-8');
			$this->view->pay_msg = $message;
		}

		/**************************************** 
		* PayU - IRN
		****************************************/
		function irn(){
			require_once( PayUAPI::getConfigFile() );
			require_once( PayUAPI::getPaymentExtraFile() );

			/**
			 * Set merchant account data by currency
			 */		
			$modifyConfig 	= new PayUModifyConfig($config);	
			$orderCurrency 	= (isset($_REQUEST['ORDER_CURRENCY'])) ? $_REQUEST['ORDER_CURRENCY'] : 'HUF';
			$config 		= $modifyConfig->merchantByCurrency($orderCurrency);	


			/*
			 * Start IRN
			 */	
		    $irn = new PayUIrn($config);
			

			/*
			 * Set needed fields
			 */		
			$data['MERCHANT'] 		= $config['MERCHANT'];
			$data['ORDER_REF'] 		= $_REQUEST['ORDER_REF'];
			$data['ORDER_AMOUNT'] 	= $_REQUEST['ORDER_AMOUNT'];	
			$data['ORDER_CURRENCY'] = $orderCurrency;
			$data['IRN_DATE'] 		= date("Y-m-d H:i:s");
			$data['AMOUNT'] 		= $_REQUEST['AMOUNT'];
			$response 				= $irn->requestIrnCurl($data);


			/**
			 * Check response
			 */	
			if (isset($response['RESPONSE_CODE'])) {
				if($irn->checkResponseHash($response)) {
					/*
					* your code here
					*/
							
					print "<pre>";	
					print_r($response);
					print "</pre>";							
				} 
				//print list of missing fields
				//print_r($irn->getMissing()); 
			}
		}

		/**************************************** 
		* PayU - IDN
		****************************************/
		function idn(){
			require_once( PayUAPI::getConfigFile() );
			require_once( PayUAPI::getPaymentExtraFile() );

			/**
			 * Set merchant account data by currency
			 */		
			$modifyConfig 	= new PayUModifyConfig($config);	
			$orderCurrency 	= (isset($_REQUEST['ORDER_CURRENCY'])) ? $_REQUEST['ORDER_CURRENCY'] : 'HUF';
			$config 		= $modifyConfig->merchantByCurrency($orderCurrency);	


			/**
			 * Start IDN
			 */	
			$idn = new PayUIdn($config);

			
			/**
			 * Set needed fields
			 */		
			$data['MERCHANT'] 		= $config['MERCHANT'];
			$data['ORDER_REF'] 		= $_REQUEST['ORDER_REF'];
			$data['ORDER_AMOUNT'] 	= $_REQUEST['ORDER_AMOUNT'];	
			$data['ORDER_CURRENCY'] = $orderCurrency;
			$data['IDN_DATE'] 		= date("Y-m-d H:i:s");
			$response 				= $idn->requestIdnCurl($data);

			
			/**
			 * Check response
			 */		
			if (isset($response['RESPONSE_CODE'])) {	
				if($idn->checkResponseHash($response)){
					/*
					* your code here
					*/
					
					print "<pre>";	
					print_r($response);
					print "</pre>";		
				}
				//print list of missing fields
				//print_r($irn->getMissing()); 
			}    
	  
		}
		/**************************************** 
		* PayU - IPN
		****************************************/
		function ipn(){
			require_once( PayUAPI::getConfigFile() );
			require_once( PayUAPI::getPaymentFile() );

				/**
				 * Set merchant account data by currency
				 */		
				$modifyConfig 	= new PayUModifyConfig($config);	
				$orderCurrency 	= (isset($_REQUEST['CURRENCY'])) ? $_REQUEST['CURRENCY'] : 'N/A';
				$config 		= $modifyConfig->merchantByCurrency($orderCurrency);


				/**
				 * Start IPN
				 */	
				$ipn = new PayUIpn($config);

				
				/**
				 * Log
				 */		
				$ipn->logger 	= $config['LOGGER'];
				$ipn->log_path 	= $config['LOG_PATH'];


				/*
				 * IPN successful
				 * This is the real end of successful payment
				 */			
				if($ipn->validateReceived()){

					//echo <EPAYMENT> (must have)
					echo $ipn->confirmReceived();
					
					/*
					 * End of payment: SUCCESSFUL
					 */

					 
					/*
					 * Your code here
					 */
					$this->portal->setOrderPaySuccess( $_REQUEST['REFNOEXT'] );
					 
					 print "<pre>";
					 print "<br>REQUEST<br>";
					 print_r($_REQUEST);
					 print "<br></pre>";
					 
				}


		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}
?>