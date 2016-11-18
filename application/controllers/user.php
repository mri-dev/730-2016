<?	class user extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = __('Fiókom');
			if(!$this->view->user){
				Helper::reload('/login');
			}
			
			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description','');
			$SEO .= $this->view->addMeta('keywords','');
			$SEO .= $this->view->addMeta('revisit-after','3 days');
			
			// FB info
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url',DOMAIN);
			$SEO .= $this->view->addOG('image',DOMAIN.substr(IMG,1).'noimg.jpg');
			$SEO .= $this->view->addOG('site_name',TITLE);
			
			$this->view->SEOSERVICE = $SEO;
		}
		
		function settings(){
			// Jelszó cseréje
			if(Post::on('changePassword')){
				try{
					$c = $this->User->changePassword($this->view->user[data][ID], $_POST);
					$this->view->passwordChangeMsg 	= Helper::makeAlertMsg('pSuccess', $c); 
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->passwordChangeMsg 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
				}
			}
			
			// Felhasználó törlése
			if(Post::on('deleteAccount')){
				try{
					$c = $this->User->deleteAccount( $this->view->user[data], $_POST[password] );
					Helper::reload( '/user/delete_account' );
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->settingsMsg 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
				}
			}			
		}
		
		function purchase(){
			$this->shop = $this->model->open('Shop');
			// Megrendelés leadása
			if(Post::on('purchase')){
				try{
					$c = $this->shop->order($this->view->user[data][ID], $_POST);
					Helper::reload( '/user' );
					//$this->view->order_msg 	= Helper::makeAlertMsg('pSuccess', $c . ' <a href="">Frissítés</a>'); 
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->order_msg 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
				}
			}

		}

		function pay(){
			$this->shop = $this->model->open('Shop');

			switch ( $this->view->gets[2] ) {
				// PayU fizetés
				case 'payu':
					// Megrendelés azonosító
					$order_id = base64_decode( $this->view->gets[3] );
					$discount = 0;

					if( $order_id == '' ){
						Helper::reload( '/user' );
					}

					/*error_reporting(E_ALL|E_STRICT);
					ini_set('display_errors', '1');*/

					require_once( PayUAPI::getConfigFile() );
					require_once( PayUAPI::getPaymentFile() );

					$this->view->order = $this->shop->getOrderData( $order_id );
					$o = $this->view->order; 

					/**
					 * Set merchant account data by currency
					 */	
					$orderCurrency 	= 'HUF';
					
					if( strtolower( $o[priceCode] ) != 'huf' ){
						$orderCurrency = strtoupper($o[priceCode]);
					}

					$modifyConfig 	= new PayUModifyConfig($config);		
					$config 		= $modifyConfig->merchantByCurrency($orderCurrency);
					
					/**
					 * Start LiveUpdate
					 */
					$lu = new PayULiveUpdate($config);
					
				 	/**
					 * Oreder global data (most cases no need to modify)		
					 */	 	
					$lu->setField("PRICES_CURRENCY", $orderCurrency);
					$lu->setField("ORDER_DATE", $config['ORDER_DATE']);
					$lu->setField("BACK_REF", $config['BACK_REF']);
					$lu->setField("TIMEOUT_URL", $config['TIMEOUT_URL']);
					$lu->setField("ORDER_TIMEOUT", $config['ORDER_TIMEOUT']);

					
					/**
					 * Payment method
					 */	 
					//only the given method
					if ($config['METHOD']!='') {
						$lu->setField("PAY_METHOD", $config['METHOD']);
						$lu->setField("AUTOMODE", 1);
					} 
					//select payment method on payment page	
					elseif ($config['METHOD']=='') {
						$lu->setField("PAY_METHOD", '');
						$lu->setField("AUTOMODE", 0);
					} 
					
						
					/**
					 * Only case of uniq contract!
					 * Do not use without it!
					 */	
					/*
						$lu->setField("LU_ENABLE_TOKEN", 1);
						$lu->setField("LU_TOKEN_TYPE", 'PAY_BY_CLICK');
					*/
						
					/**
					* Order ID
					*
					* You have to change this to YOUR ORDER ID!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
					*/
					$testOrderId = $order_id;	

				

					if( $o[payMethod] == 'Utánvétel'){
						// Utánvételes fizetás
						$lu->setField("PAY_METHOD", "CASH");
					}

					$trans_price = ( is_null($o[transportPrice]) || $o[transportPrice] == 0  ) ? 0 : $o[transportPrice];


					/**
					 * Order global data (need to fill by order data)
					 */			
					$lu->setField("ORDER_REF", $testOrderId);
					
					$lu->setField("ORDER_SHIPPING", $trans_price); 
					$lu->setField("LANGUAGE", (Lang::getLang() == 'hu') ? 'hu' : 'en' );
					$lu->setField("ORDER_PRICE_TYPE", "GROSS");			// [ GROSS | NET ]


					/**
					 * Add product with array
					 * Sample products with gross price
					 */	
					/*$lu->addProduct(array(
						'name' => 'Lorem 1',							//product name [ string ]
						'code' => 'sku0001',							//merchant systemwide unique product ID [ string ]
						'info' => 'Lorem ipsum dolor sit amet',			//product description [ string ]
						'price' => 111, 								//product price [ HUF: integer | EUR, USD decimal 0.00 ]
						'vat' => 0,										//product tax rate [ in case of gross price: 0 ] (percent)
						'qty' => 1										//product quantity [ integer ] 
					));
					*/
					$total = 0;
					foreach( $this->view->order[items] as $d ): 
						$price = round( $d[price] / 5 ) * 5;
						$lu->addProduct(array(
							'name' 	=> $d[product_name],						//product name [ string ]
							'code' 	=> $d[ID],									//merchant systemwide unique product ID [ string ]
							'info' 	=> $d[variation_name],						//product description [ string ]
							'price' => $price, 				//product price [ HUF: integer | EUR, USD decimal 0.00 ]
							'vat' 	=> 0,										//product tax rate [ in case of gross price: 0 ] (percent)
							'qty' 	=> $d[pcs]										//product quantity [ integer ] 
						));
						$total += $price * $d[pcs];
					endforeach;

					if( !is_null( $o[couponID] ) ){
						$rate 		= $o[couponRate];
						$discount 	= $total / 100 * $rate; 
					
					}

					$lu->setField("DISCOUNT", $discount); 

					$o[user_data][szall_firstname] = ($o[user_data][szall_firstname] != '') ? $o[user_data][szall_firstname] : $o[user_data][szam_firstname];
					$o[user_data][szall_lastname] 	= ($o[user_data][szall_lastname] != '') ? $o[user_data][szall_lastname] : $o[user_data][szam_lastname];
					$o[user_data][szall_phone] 	= ($o[user_data][szall_phone] != '') ? $o[user_data][szall_phone] : $o[user_data][szam_phone];
					$o[user_data][szall_state] 	= ($o[user_data][szall_state] != '') ? $o[user_data][szall_state] : $o[user_data][szam_state];
					$o[user_data][szall_city] 		= ($o[user_data][szall_city] != '') ? $o[user_data][szall_city] : $o[user_data][szam_city];
					$o[user_data][szall_address] 	= ($o[user_data][szall_address] != '') ? $o[user_data][szall_address] : $o[user_data][szam_address];
					$o[user_data][szall_housenumber] 	= ($o[user_data][szall_housenumber] != '') ? $o[szall_housenumber] : $o[user_data][szam_housenumber];
					$o[user_data][szall_zipcode] 	= ($o[user_data][szall_zipcode] != '') ? $o[user_data][szall_zipcode] : $o[user_data][szam_zipcode];

					/**
					 * Billing data
					 */	
					$lu->setField("BILL_FNAME", $o[user_data][szam_firstname]);
					$lu->setField("BILL_LNAME", $o[user_data][szam_lastname]);
					$lu->setField("BILL_EMAIL", $o[user_data][email]); 
					$lu->setField("BILL_PHONE", $o[user_data][szam_phone]);
					if($o[user_data][szam_company] != ''):
						$lu->setField("BILL_COMPANY", $o[user_data][szam_company]);			// optional
						$lu->setField("BILL_FISCALCODE", $o[user_data][szam_vat]);					// optional
					endif;
				//		
					$lu->setField("BILL_COUNTRYCODE", "HU");
					$lu->setField("BILL_STATE", $o[user_data][szam_state]);
					$lu->setField("BILL_CITY", $o[user_data][szam_city]); 
					$lu->setField("BILL_ADDRESS", $o[user_data][szam_address]." ".$o[user_data][szam_housenumber] ); 
					//$lu->setField("BILL_ADDRESS2", "Second line address");		// optional
					$lu->setField("BILL_ZIPCODE", $o[user_data][szam_zipcode]); 
						
					/**
					 * Delivery data
					 */	
					$lu->setField("DELIVERY_FNAME", $o[user_data][szall_firstname]); 
					$lu->setField("DELIVERY_LNAME", $o[user_data][szall_lastname]); 
					$lu->setField("DELIVERY_EMAIL", $o[user_data][email]); 
					$lu->setField("DELIVERY_PHONE", $o[user_data][szall_phone]); 
					$lu->setField("DELIVERY_COUNTRYCODE", "HU");
					$lu->setField("DELIVERY_STATE", $o[user_data][szall_state]);
					$lu->setField("DELIVERY_CITY", $o[user_data][szall_city]);
					$lu->setField("DELIVERY_ADDRESS",  $o[user_data][szall_address]." ".$o[user_data][szall_housenumber] ); 
					//$lu->setField("DELIVERY_ADDRESS2", "Second line address");	// optional
					$lu->setField("DELIVERY_ZIPCODE", $o[user_data][szall_zipcode]); 


					/**
					 * Log to file
					 */		
					$lu->logger 	= $config['LOGGER'];
					$lu->log_path 	= $config['LOG_PATH'];

					
					/**
					 * Generate fields and print form
					 */	
					$display = $lu->createHtmlForm('PayUForm', 'button', __('Fizetés indítása PayU-val') );	// format: link, button, auto (auto is redirects to payment page immediately )
					//print_r($lu->getMissing());
					$this->view->pay_form = $display;


					break;
				// PayPal fizetés
				case 'paypal':
					$order_id = base64_decode( $this->view->gets[3] );
					$discount = 0;

					if( $order_id == '' ){
						Helper::reload( '/user' );
					}

					$this->view->order = $this->shop->getOrderData( $order_id );
					$o = $this->view->order; 
					
				break;
				default:
					Helper::reload( '/user' );
					break;
			}
		}
		
		function order(){
			switch( $this->view->gets[2] ){
				case 'cancel':
					$this->shop = $this->model->open('Shop');
					
					$id = $this->view->gets[3];
					
					$db = $this->model->db->query("SELECT * FROM ".TAGS::DB_TABLE_ORDERS." WHERE MD5( CONCAT(orderKey,'_',ID)) = '$id';");
					$data = $db->fetch(PDO::FETCH_ASSOC);
					
					$this->view->order_data = $data;
					
					if(Post::on('delorder')){
						try{
							$c = $this->shop->deleteOrder( $this->view->order_data[ID] );
							Helper::reload( '/user' );							
						}catch(Exception $e){
							$this->view->err 		= true;
							$this->view->order_msg 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
						}
					}
					
					// Ha nem létezik a megrendelés, akkor átirányít
					if( !$data ) {
						Helper::reload('/user');
					}
					
					// Ha nem a saját megrendelés, akkor átirányít
					if( $this->view->order_data[userID] != $this->view->user[data][ID] ) {
						Helper::reload('/user');
					}
					
				break;
			}
		}
		
		function logout(){
			$this->User->logout();
			Helper::reload('/login');
		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}
?>