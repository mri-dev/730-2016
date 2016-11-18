<?
	class Shop_Model extends Model{
		private $lang = 'hu';
		function __construct(){
			parent::__construct();
			$this->lang = Lang::getLang();
		}

		function deleteOrder( $orderID ) {

			// Termékek törlése
			$this->db->query("DELETE FROM ".TAGS::DB_TABLE_ORDER_ITEMS. " WHERE orderID = $orderID;");

			// Megrendelés törlése
			$this->db->query("DELETE FROM ".TAGS::DB_TABLE_ORDERS. " WHERE ID = $orderID;");

		}

		public function getOrderData( $orderKey = false ){
			if( !$orderKey ) return false;

			$orderData = $this->db->query("
				SELECT 			o.*,
								c.couponKey as couponName,
								c.action_rate as couponRate
				FROM 			".TAGS::DB_TABLE_ORDERS." as o
				LEFT OUTER JOIN ".TAGS::DB_TABLE_COUPONS." as c ON c.ID = o.couponID
				WHERE 			o.orderKey = '$orderKey'")->fetch(PDO::FETCH_ASSOC);

			$orderData[items] 		= $this->getOrderItems( $orderData[ID],  array('priceCode' => $orderData[priceCode]) );

			$total_net = 0;
			$total_gross = 0;
			$discount = 0;

			foreach( $orderData[items] as $items ) {
				$total_net += $items[total_net_price];
				$total_gross += $items[total_price];
			}

			if( $orderData[couponRate] > 0 ) {
				$discount = $total_gross * ( $orderData[couponRate] / 100 );
			}

			$orderData[total_discount] = $discount;
			$orderData[total_net_price] = $total_net;
			$orderData[total_gross_price] = $total_gross;
			$orderData[total_tax] = $total_gross - $total_net;

			$orderData[user_data] 	= $this->db->query( "SELECT * FROM ".TAGS::DB_TABLE_USERS." WHERE ID = ".$orderData[userID] )->fetch(PDO::FETCH_ASSOC);

			return $orderData;
		}

		public function getOrderItems( $ID = false, $arg = array() ){

			$pc = ( $arg[priceCode] ) ? strtolower($arg[priceCode]) : Lang::getPriceCode() ;

			if( !$ID ) return false;

			$orderItems = $this->db->query("
				SELECT 			oi.ID, oi.productID, oi.variationID, oi.pcs, oi.addedAt,
								p.name_".Lang::getLang()." as product_name,
								v.name_".Lang::getLang()." as variation_name,
								p.afa as tax,
								price_".$pc." as net_price,
								(price_".$pc." * oi.pcs) as total_net_price,
								( v.price_".$pc." * ( p.afa / 100 + 1 ) ) as price,
								(v.price_".$pc." * ( p.afa / 100 + 1 ) * oi.pcs ) as total_price
				FROM 			".TAGS::DB_TABLE_ORDER_ITEMS." as oi
				LEFT OUTER JOIN ".TAGS::DB_TABLE_PRODUCTS." as p ON p.ID = oi.productID
				LEFT OUTER JOIN ".TAGS::DB_TABLE_PRODUCT_VARIATIONS." as v ON v.ID = oi.variationID
				WHERE 			oi.orderID = $ID")->fetchAll(PDO::FETCH_ASSOC);

			return $orderItems;

		}

		public function addToCart($mid, $productID, $pcs){
			if($mid == '')
			throw new Exception(__('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!'));

			$c = $this->db->query("SELECT ID FROM ".TAGS::DB_TABLE_SHOP_CART." WHERE productID = $productID and mID = $mid;")->rowCount();

			if($c == 0){
				$this->db->insert(
					TAGS::DB_TABLE_SHOP_CART,
					array('productID', 'mID', 'pcs'),
					array($productID, $mid, $pcs)
				);
			}else{
				$this->db->query("UPDATE ".TAGS::DB_TABLE_SHOP_CART." SET pcs = pcs + $pcs  WHERE productID = $productID and mID = $mid");
			}

		}

		public function addItemToCart($mid, $productID, $variationID){
			if($mid == '')
				throw new Exception(__('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!'));
			if($productID == '')
				throw new Exception(__('Termék azonosító hiányzik!'));
			if($variationID == '')
				throw new Exception(__('Termék variáció azonosító hiányzik!'));

			$c = $this->db->query("SELECT pcs FROM ".TAGS::DB_TABLE_SHOP_CART." WHERE productID = $productID and variationID = $variationID and mID = $mid;")->fetch(PDO::FETCH_ASSOC);

			if($c[pcs] && $c[pcs] > 0){
				$this->db->query("UPDATE ".TAGS::DB_TABLE_SHOP_CART." SET pcs = pcs + 1  WHERE productID = $productID and variationID = $variationID and mID = $mid");
			}else{
				$this->db->insert(
					TAGS::DB_TABLE_SHOP_CART,
					array('mID','productID','variationID','pcs'),
					array($mid, $productID, $variationID, 1)
				);
			}


		}

		public function removeItemFromCart($mid, $productID, $variationID){
			if($mid == '')
				throw new Exception(__('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!'));
			if($productID == '')
				throw new Exception(__('Termék azonosító hiányzik!'));
			if($variationID == '')
				throw new Exception(__('Termék variáció azonosító hiányzik!'));

			$c = $this->db->query("SELECT pcs FROM ".TAGS::DB_TABLE_SHOP_CART." WHERE productID = $productID and variationID = $variationID and mID = $mid;")->fetch(PDO::FETCH_ASSOC);

			$cn = $c[pcs];

			if($cn == 1){
				$this->db->query("DELETE FROM ".TAGS::DB_TABLE_SHOP_CART." WHERE productID = $productID and variationID = $variationID and mID = $mid");
			}else if($cn > 1){
				$this->db->query("UPDATE ".TAGS::DB_TABLE_SHOP_CART." SET pcs = pcs - 1  WHERE productID = $productID and variationID = $variationID and mID = $mid");
			}
		}

		public function removeFromCart($mid, $productID){
			if($mid == '')
				throw new Exception(__('Nem sikerült hozzáadni a terméket a kosárhoz. Frissítse le az oldalt és próbálja újra!'));
			if($productID == '')
				throw new Exception(__('Termék azonosító hiányzik!'));
			$q = "DELETE FROM ".TAGS::DB_TABLE_SHOP_CART." WHERE productID = $productID and mID = $mid";
			$this->db->query($q);
		}
		public function clearCart($mid){
			if($mid == '') return false;
			$this->db->query("DELETE FROM ".TAGS::DB_TABLE_SHOP_CART." WHERE mID = $mid");
		}
		public function cartInfo($mid){
			$re 		= array();
			$itemNum 	= 0;
			$totalPrice = 0;
			$originPrice = 0;

			$q = "SELECT
				c.*,
				c.productID as termekID,
				p.afa,
				CONCAT(coll.name_".$this->lang.",' ',p.name_".$this->lang.") as termekNev,
				pv.price_".Lang::getPriceCode()." as price,
				pv.name_".$this->lang." as variationName
			FROM ".TAGS::DB_TABLE_SHOP_CART." as c
			LEFT OUTER JOIN ".TAGS::DB_TABLE_PRODUCTS." as p ON p.ID = c.productID
			LEFT OUTER JOIN ".TAGS::DB_TABLE_COLLECTIONS." as coll ON coll.ID = p.collectionID
			LEFT OUTER JOIN ".TAGS::DB_TABLE_PRODUCT_VARIATIONS." as pv ON pv.ID = c.variationID
			WHERE c.mID = $mid and pv.price_".Lang::getPriceCode()." > 0 ";
			$arg[multi] = '1';
			extract($this->db->q($q, $arg));

			$dt = array();
			foreach($data as $d){
				$d[isProduct] = 1;
				$d[price] 		= round( ($d[price] * (($d[afa] / 100) + 1)) / 5 ) * 5;

				$d[priceCode] 	= Lang::getPriceCode();
				$d[url] 		= '/webshop/product/'.Helper::makeSafeUrl($d[termekNev],'_-'.$d[termekID]);

				$itemNum 	+= $d[pcs];
				$totalPrice += ($d[price] * $d[pcs]);

				$d[price] 	= Helper::cashFormat($d[price]);

				$dt[] = $d;
			}

			$originPrice = $totalPrice;

			// Coupon
			if ( true ) {
				$dt[] = array(
					'isProduct' => 0,
					'termekID' => 'coupon',
					'variationID' => 'TEST',
					'pcs' => 1,
					'variationName' => __('Kód').': TEST',
					'termekNev' => __('Kuponkedvezmény'),
					'discount' => '10%'
				);

				$totalPrice = $totalPrice - ($totalPrice / 100 * 10);
			}

			//Giftcards
			if ( true ) {
				$dt[] = array(
					'isProduct' => 0,
					'termekID' => 'giftcard',
					'variationID' => '212132343',
					'pcs' => 1,
					'variationName' => '212132343/345',
					'termekNev' => 'Ajándékkártya',
					'price' => -5000,
					'priceCode' => Lang::getPriceCode()
				);
				$totalPrice -= 5000;
				$dt[] = array(
					'isProduct' => 0,
					'termekID' => 'giftcard',
					'variationID' => '1000123345',
					'pcs' => 1,
					'variationName' => '1000123345/164',
					'termekNev' => __('Ajándékkártya'),
					'price' => -2800,
					'priceCode' => Lang::getPriceCode()
				);
				$totalPrice -= 2800;
			}

			$re[itemNum]	= $itemNum;
			$re[originPrice]	= $originPrice;
			$re[originPriceTxt]	= number_format($originPrice,0,""," ");
			$re[totalPrice]	= $totalPrice;
			$re[totalPriceTxt]	= number_format($totalPrice,0,""," ");
			$re[items] 		= $dt;

			return $re;
		}

		protected function getCouponId($key){
			if($key == '') return false;

			$c = $this->db->query("SELECT ID FROM ".TAGS::DB_TABLE_COUPONS." WHERE couponKey = '$key' and (now() >= activeFrom and now() <= activeTo)");

			if($c->rowCount() == 0) return false;

			return $c->fetch(PDO::FETCH_COLUMN);
		}

		public function order($userID, $order){
			extract($order);

			// Felhasználó adatok
			$user = $this->db->query("SELECT * FROM ".TAGS::DB_TABLE_USERS." WHERE ID = $userID")->fetch(PDO::FETCH_ASSOC);

			if(count($order[buyItem]) == 0)
				throw new Exception(__('Az Ön kosarában nincsennek termékek!'));

			// Kupon ellenőrzése
			$validCoupon 	= $this->getCouponId($couponKey);
			$coupon 		= ($validCoupon) ? $validCoupon : 'NULL';

			// Megrendelés jegyzése
				$newOrder = "
					INSERT INTO ".TAGS::DB_TABLE_ORDERS."(email, userID, orderKey, ordererIP, comment, couponID, priceCode, payMethod)
					VALUES('".$user[email]."', '".$user[ID]."', nextOrderId(), '".$_SERVER[REMOTE_ADDR]."', '$comment',	$coupon, '".Lang::getPriceCode()."', '$payMethod' );
				";
				//throw new Exception($newOrder);
				$this->db->query($newOrder);

				$lastOrderId = $this->db->lastInsertId();

			// Kosár tartalmának átmásolása a megrendelt termékek listába
				foreach($order[buyItem] as $productID => $variations){
					foreach($variations as $variationID => $darab){
						$this->db->insert(
							TAGS::DB_TABLE_ORDER_ITEMS,
							array('orderID', 'mID', 'userID', 'email', 'productID', 'variationID', 'pcs'),
							array($lastOrderId, Helper::getMachineID(), $user[ID], $user[email], $productID, $variationID, $darab)
						);
					}
				}

			// Kosár ürítés
				$this->db->query("DELETE FROM ".TAGS::DB_TABLE_SHOP_CART." WHERE mID = ".Helper::getMachineID());

				$orderData = $this->db->query("SELECT * FROM ".TAGS::DB_TABLE_ORDERS." WHERE ID = $lastOrderId")->fetch(PDO::FETCH_ASSOC);

			// E-mail értesítés
				// Alert
				$msg = 'Új megrendelés érkezett - <a href="'.DOMAIN.'admin">'.DOMAIN.'admin</a>';

				Helper::sendMail(array(
					'recepiens' => array(ALERT_EMAIL),
					'msg' 	=> $msg,
					'tema' 	=> 'Új megrendelés',
					'from' 	=> NOREPLY_EMAIL,
					'sub' 	=> 'Új megrendelés'
				));

				// Felhasználó
				if(Lang::getLang() == 'hu'){
					$msg = '<h3>Tisztelt, '.$user[szam_firstname].' '.$user[szam_lastname].'!</h3>';
					$msg .= 'Köszönjük, hogy a(z) '.TITLE.' oldalán vásárolt!<br /><br />';
					$msg .= 'Megrendelés azonosító: <strong>'.$orderData[orderKey].'</strong> <br />';
					$msg .= 'Lépjen be oldalunkra, ahol részletesen megtekintheti a rendelés adatait és aktuális állapotát!';
					$msg .= '<br /><br />';
					$msg .= 'Üdvözlettel, <br />'.TITLE;
					$msg .= '';
				}else{
					$msg = '<h3>Dear '.$user[szam_firstname].', '.$user[szam_lastname].',</h3>';
					$msg .= 'Thank you for purchasing our products!<br /><br />';
					$msg .= 'Your order ID: <strong>'.$orderData[orderKey].'</strong> <br />';
					$msg .= 'Log in your account to see order details and status! If you chosen Credit Card for paying, you can pay after login via PayU.';
					$msg .= '<br /><br />';
					$msg .= "Your sincerely,<br /><strong> ".TITLE."</strong> <br> Hungarian, Handmade, Fine Jewelery<br> <br> Have a style to be unique have a charm to be a Lady!";

				}

				Helper::sendMail(array(
					'recepiens' => array($user[email]),
					'msg' 	=> $msg,
					'tema' 	=> __('Megrendelését fogadtuk'),
					'from' 	=> NOREPLY_EMAIL,
					'sub' 	=> __('Megrendelését fogadtuk')
				));


			return __('Megrendelését fogadtuk').'!';
		}

	}
?>
