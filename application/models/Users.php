<? class Users_Model extends Model{
	const TABLE_NAME = TAGS::DB_TABLE_USERS;
	public $user = false;
	private $lang = 'hu';
	function __construct(){
		parent::__construct();
		$this->lang = Lang::getLang();
		$this->getUser();
	}

	function get(){
		$ret 			= array();

		if(!$this->user) return false;

		$ret[email] 	= $this->user;
		$ret[data] 		= ($this->user) ? $this->getData($this->user) : false;
		$ret[orders] 	= ($this->user) ? $this->getOrders($ret[data][ID]) : false;

		// Szállítási adat
		if($ret[data][szall_firstname] == ''){
			$ret[data][szall_firstname] = $ret[data][szam_firstname];
		}
		if($ret[data][szall_lastname] == ''){
			$ret[data][szall_lastname] = $ret[data][szam_lastname];
		}
		if($ret[data][szall_company] == ''){
			$ret[data][szall_company] = $ret[data][szam_company];
		}
		if($ret[data][szall_phone] == ''){
			$ret[data][szall_phone] = $ret[data][szam_phone];
		}
		if($ret[data][szall_vat] == ''){
			$ret[data][szall_vat] = $ret[data][szam_vat];
		}
		if($ret[data][szall_country] == ''){
			$ret[data][szall_country] = $ret[data][szam_country];
		}
		if($ret[data][szall_state] == ''){
			$ret[data][szall_state] = $ret[data][szam_state];
		}
		if($ret[data][szall_zipcode] == ''){
			$ret[data][szall_zipcode] = $ret[data][szam_zipcode];
		}
		if($ret[data][szall_city] == ''){
			$ret[data][szall_city] = $ret[data][szam_city];
		}
		if($ret[data][szall_address] == ''){
			$ret[data][szall_address] = $ret[data][szam_address];
		}
		if($ret[data][szall_housenumber] == ''){
			$ret[data][szall_housenumber] = $ret[data][szam_housenumber];
		}

		$country = $ret[data][szall_country];
		$current_currency = strtolower( $_COOKIE['__countryCurrency'] );

		$this->validateCurrency( $country, $current_currency );


		return $ret;
	}

	private function validateCurrency( $user_country, $current_currency ) {
		$country_currency = $this->db->query("SELECT currency_code FROM ".TAGS::DB_TABLE_CURRENCY_COUNTRIES." WHERE country = '$user_country';")->fetchColumn();

		if( $country_currency != $current_currency ) {
			//setcookie( '__countryCurrency', $country_currency, time() + 60 * 60 * 24, '/' );
			//Helper::reload();
		}
	}

	function deleteAccount( $user, $pass_to_check ) {

		$pass_check = Hash::jelszo($pass_to_check);

		// Ha a jelszó nem egyezik
		if( $pass_check !== $user[password] ) {
			throw new Exception( __('A megadott jelszó hibás, nem tudjuk törölni fiókját!') );
		}

		// Felhasználó törlése
		// - jelszó és email eltávolítása,
		// státusz töröltté állítása
		$this->db->query( " UPDATE ".TAGS::DB_TABLE_USERS. " SET status = 'Törölve', password = NULL, email = NULL, actived = 0 WHERE ID = {$user[ID]};" );

		// Kijelentkeztetés
		$this->logout();
	}

	function resetPassword($post){
		extract($post);
		$jelszo =  rand(1111111,9999999);

		if(!$this->userExists('email',$email)){
			throw new Exception(__('Hibás e-mail cím.'),1001);
		}

		if(Lang::getLang() == 'hu'){
			$msg = 'Weboldalunkon új jelszót igényelt. Lépjen be az automatikusan generált új jelszavával, majd sikeres bejelentkezés után változtassa meg a kívánt jelszóra. <br /><br />';
			$msg .= "<strong>Jelszavát könnyedén megváltoztathatja az <a href='".DOMAIN."user/settings'>adatain</a> belül.</strong> <br /><br />";
			$msg .= "Automatikusan generált jelszó:<br />";
			$msg .= "<strong>".$jelszo."</strong>";
		}else{
			$msg = 'Weboldalunkon új jelszót igényelt. Lépjen be az automatikusan generált új jelszavával, majd sikeres bejelentkezés után változtassa meg a kívánt jelszóra. <br /><br />';
			$msg .= "<strong>Jelszavát könnyedén megváltoztathatja az <a href='".DOMAIN."user/settings'>adatain</a> belül.</strong> <br /><br />";
			$msg .= "Automatikusan generált jelszó:<br />";
			$msg .= "<strong>".$jelszo."</strong>";
		}

		Helper::sendMail(array(
			'recepiens' => array($email),
			'msg' 	=> $msg,
			'tema' 	=> __('Új jelszó'),
			'from' 	=> NOREPLY_EMAIL,
			'sub' 	=> __('Új jelszava')
		));


		$this->db->update(
			self::TABLE_NAME,
			array(
				'password' => Hash::jelszo($jelszo)
			),
			"email = '".$email."'"
		);
	}

	private function getUser(){
		if($_SESSION[user_email]){
			$this->user = $_SESSION[user_email]	;
		}
	}
	function changeUserAdat($userID, $post){
		extract($post);
		if($nev == '') throw new Exception('A neve nem lehet üress. Kérjük írja be a nevét!');

		$this->db->update(self::TABLE_NAME,
			array(
				'nev' => $nev
			),
			"ID = $userID"
		);
		return "Változásokat elmentettük. <a href=''>Frissítés</a>";
	}
	function changeSzallitasiAdat($userID, $post){
		extract($post);
		unset($post[saveSzallitasi]);

		if($nev == '' || $city == '' || $irsz == '' || $uhsz == '' || $phone == '') throw new Exception('Minden mező kitölétse kötelező!');

		$this->db->update(self::TABLE_NAME,
			array(
				'szallitasi_adatok' => json_encode($post,JSON_UNESCAPED_UNICODE)
			),
			"ID = $userID"
		);
		return "Változásokat elmentettük. <a href=''>Frissítés</a>";
	}

	function changeSzamlazasiAdat($userID, $post){
		extract($post);
		unset($post[saveSzamlazasi]);

		if($nev == '' || $city == '' || $irsz == '' || $uhsz == '') throw new Exception('Minden mező kitölétse kötelező!');

		$this->db->update(self::TABLE_NAME,
			array(
				'szamlazasi_adatok' => json_encode($post,JSON_UNESCAPED_UNICODE)
			),
			"ID = $userID"
		);
		return "Változásokat elmentettük. <a href=''>Frissítés</a>";
	}

	public function getGiftcardOnOrder( $id, $pricecode = 'huf' )
	{
		$q = "SELECT
			gu.code,
			gu.verify_code,
			g.amount_".$pricecode." as price
		FROM giftcard_using as gu
		LEFT OUTER JOIN giftcards as g ON g.code = gu.code
		WHERE gu.orderID = $id;";

		$arg['multi'] = 1;
		extract($this->db->q($q, $arg));

		$total = 0;
		foreach ($data as $dat) {
			$total += $dat['price'];
		}
		$ret['total'] = $total;

		return $ret;
	}

	function getOrders($userID, $arg = array()){
		if($userID == '') return false;
		$back = array();

		$q = "SELECT
		o.*,
		c.couponKey as couponName,
		c.action_rate as couponRate
		FROM ".TAGS::DB_TABLE_ORDERS." as o
		LEFT OUTER JOIN ".TAGS::DB_TABLE_COUPONS." as c ON c.ID = o.couponID
		WHERE o.userID = $userID
		ORDER BY o.orderedAt DESC";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		foreach($data as $d){
			$price = 0;
			$d[giftcard] = $this->getGiftcardOnOrder($d[ID], $d[priceCode]);
			$d[items] = $this->orderedItems($d[ID], array('priceCode' => $d[priceCode]));

			foreach ($d[items] as $p) {
				$price += $p[totalPrice];
			}
			if ($d['couponRate'] != '') {
			 $price -= ($price / 100 * $d[couponRate]);
			}

			if ($d['transportPrice'] != '') {
				$price += $d[transportPrice];
			}
			if ($d[giftcard]['total'] != 0) {
				$price -= $d[giftcard]['total'];
			}

			$d[total_price] = $price;

			$back[] = $d;
		}
		return $back;
	}

	protected function orderedItems($orderID, $arg = array()){
		if($orderID == '') return false;

		$pc = ( $arg[priceCode] ) ? strtolower($arg[priceCode]) : Lang::getPriceCode() ;

		$q = "SELECT
			i.productID,
			i.variationID,
			i.pcs,
			p.afa,
			CONCAT(coll.name_".$this->lang.",' ',p.name_".$this->lang.") as termekNev,
			pv.price_".$pc." as price,
			pv.name_".$this->lang." as variationName
		FROM ".TAGS::DB_TABLE_ORDER_ITEMS." as i
		LEFT OUTER JOIN ".TAGS::DB_TABLE_PRODUCTS." as p ON p.ID = i.productID
		LEFT OUTER JOIN ".TAGS::DB_TABLE_COLLECTIONS." as coll ON coll.ID = p.collectionID
		LEFT OUTER JOIN ".TAGS::DB_TABLE_PRODUCT_VARIATIONS." as pv ON pv.ID = i.variationID
		WHERE
			i.orderID = '$orderID'
		";

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		$dt = array();
		foreach($data as $d){

			$d[price] 		= $d[price] * (($d[afa] / 100) + 1);
			$d[priceCode] 	= Lang::getPriceCode();
			$d[url] 		= '/webshop/product/'.Helper::makeSafeUrl($d[termekNev],'_-'.$d[productID]);

			$d[priceTxt] 	= Helper::cashFormat($d[price]);
			$d[totalPrice] 	= $d[price] * $d[pcs];


			$dt[] = $d;
		}

		return $dt;
	}

	function changePassword($userID, $post){
		extract($post);
		if($userID == '') throw new Exception(__('Hiányzik a felhasználó azonosító! Jelentkezzen be újra.'));
		if($old == '') throw new Exception(__('Kérjük, adja meg az aktuálisan használt, régi jelszavát!'));
		if($new == '' || $new2 == '') throw new Exception(__('Kérjük, adja meg az új jelszavát!'));
		if($new !== $new2) throw new Exception(__('A megadott jelszó nem egyezik, írja be újra!'));

		$jelszo = Hash::jelszo($old);

		$checkOld = $this->db->query("SELECT ID FROM ".self::TABLE_NAME." WHERE ID = $userID and password = '$jelszo'");
		if($checkOld->rowCount() == 0){
			throw new Exception(__('A megadott régi jelszó hibás. Póbálja meg újra!'));
		}

		$this->db->update(self::TABLE_NAME,
			array(
				'password' => Hash::jelszo($new2)
			),
			"ID = $userID"
		);

		return __("Jelszavát sikeresen lecseréltük.");
	}
	function getData($email){
		if($email == '') return false;
		$q = "SELECT * FROM ".self::TABLE_NAME." WHERE email = '$email'";

		extract($this->db->q($q));

		return $data;
	}

	function logout(){
		unset($_SESSION[user_email]);
	}

	function login($post){
		extract($post);

		if(!$this->userExists('email',$login_email)){
			throw new Exception(__('Ezzel az e-mail címmel nem regisztráltak még!'),1001);
		}

		if(!$this->validUser($login_email,$login_pw)){
			throw new Exception(__('Hibás bejelentkezési adatok!'),9000);
		}

		if(!$this->isActivated($login_email)){
			throw new Exception(__('A fiók még nincs aktiválva!'),1001);
		}

		if(!$this->isEnabled($login_email)){
			throw new Exception(__('A fiók felfüggesztésre került!'),1001);
		}

		// Refresh
		$this->db->update(self::TABLE_NAME,
			array(
				'lastLogin' 	=> NOW,
				'lastLoginIp' 	=> $_SERVER[REMOTE_ADDR]
			),
			"email = '".$login_email."'"
		);

		Session::set('user_email',$login_email);
	}

	function activate($activate_arr){
		$email 	= $activate_arr[0];
		$userID = $activate_arr[1];
		$pwHash = $activate_arr[2];

		if($email == '' || $userID == '' || $pwHash == '') throw new Exception(__('Hibás azonosító'));

		$q = $this->db->query("SELECT * FROM ".self::TABLE_NAME." WHERE ID = $userID and email = '$email' and password = '$pwHash'");

		if($q->rowCount() == 0) throw new Exception(__('Hibás azonosító'));

		$d = $q->fetch(PDO::FETCH_ASSOC);

		if($d[actived] == '1')  throw new Exception(__('A fiók már aktiválva van!'));

		$this->db->update(self::TABLE_NAME,
			array(
				'actived' 	=> '1',
				'status' 	=> 'Aktív'
			),
			"ID = $userID"
		);
	}

	function add($post){
		extract($post);

		if(
			$email == '' ||
			$pw1 == '' ||
			$pw2 == '' ||
			$szam_firstname == '' ||
			$szam_lastname == '' ||
			$szam_country == '' ||
			$szam_zipcode == '' ||
			$szam_city == '' ||
			$szam_state == '' ||
			$szam_address == '' ||
			$szam_housenumber == '' ||
			$szam_phone == ''
		) throw new Exception(__('A csillaggal megjelölt mezők kitöltése kötelező!'),1000);

		if($pw1 != $pw2) throw new Exception(__('A megadott jelszavak nem egyeznek!'),1001);

		if($this->userExists('email',$email)){
			throw new Exception(__('Ezzel az e-mail címmel már regisztráltak!'),1002);
		}

		if($ireadedaszf != 'on'){
			throw new Exception(__('Az Általános Szerződési Feltételeket kötelező elfogadni!'),1002);
		}

		$szamlazasi_keys = Helper::getArrayValueByMatch($post,'szam_');
		$szallitasi_keys = Helper::getArrayValueByMatch($post,'szall_');


		$szall_firstname 	= ($szall_firstname == '') 	? trim($szam_firstname) 	: $szall_firstname;
		$szall_lastname 	= ($szall_lastname == '') 	? trim($szam_lastname)		: $szall_lastname;
		$szall_country 		= ($szall_country == '') 	? trim($szam_country)		: $szall_country;
		$szall_zipcode 		= ($szall_zipcode == '') 	? trim($szam_zipcode)		: $szall_zipcode;
		$szall_city 		= ($szall_city == '') 		? trim($szam_city)			: $szall_city;
		$szall_state		= ($szall_state == '') 		? trim($szam_state)			: $szall_state;
		$szall_address 		= ($szall_address == '') 	? trim($szam_address)		: $szall_address;
		$szall_housenumber 	= ($szall_housenumber == '')? trim($szam_housenumber)	: $szall_housenumber;
		$szall_company 		= ($szall_company == '') 	? trim($szam_company)		: $szall_company;
		$szall_vat 			= ($szall_vat == '') 		? trim($szam_var)			: $szall_vat;
		$szall_phone 		= ($szall_phone == '') 		? trim($szam_phone)			: $szall_phone;

		$this->db->insert(
			self::TABLE_NAME,
			array('email','password', 'szam_firstname', 'szam_lastname', 'szam_country', 'szam_zipcode', 'szam_city', 'szam_state', 'szam_address', 'szam_housenumber', 'szam_company', 'szam_vat', 'szam_phone','szall_firstname', 'szall_lastname', 'szall_country', 'szall_zipcode', 'szall_city', 'szall_state',  'szall_address', 'szall_housenumber', 'szall_company', 'szall_vat', 'szall_phone', 'registerIP'),
			array(
				trim($email),
				Hash::jelszo($pw2),
				addslashes(trim($szam_firstname)),
				addslashes(trim($szam_lastname)),
				addslashes(trim($szam_country)),
				addslashes(trim($szam_zipcode)),
				addslashes(trim($szam_city)),
				addslashes(trim($szam_state)),
				addslashes(trim($szam_address)),
				addslashes(trim($szam_housenumber)),
				addslashes(trim($szam_company)),
				addslashes(trim($szam_vat)),
				addslashes(trim($szam_phone)),
				addslashes(trim($szall_firstname)),
				addslashes(trim($szall_lastname)),
				addslashes(trim($szall_country)),
				addslashes(trim($szall_zipcode)),
				addslashes(trim($szall_city)),
				addslashes(trim($szall_state)),
				addslashes(trim($szall_address)),
				addslashes(trim($szall_housenumber)),
				addslashes(trim($szall_company)),
				addslashes(trim($szall_vat)),
				addslashes(trim($szall_phone)),
				$_SERVER[REMOTE_ADDR]
			)
		);

		$activateKey = base64_encode(trim($email).'='.$this->db->lastInsertId().'='.Hash::jelszo($pw2));

		// E-mail értesítő
		if(Lang::getLang() == 'hu'){
			$msg = '<h2>Tisztelt '.trim($szam_firstname).' '.trim($szam_lastname).'!</h2>';
			$msg .= "Köszönjük, hogy regisztrált rendszerünkbe! Amennyiben nem Ön regisztrált a(z) ".TITLE." weboldalon, hagyja figyelmen kívül ezt a levelet.<br /><br />";

			$msg .= "<strong>Amennyiben Ön regisztrált, kérjük, hogy az alábbi hivatkozást megnyitva aktiválja fiókját:</strong><br />";
			$msg .= "<a href='".DOMAIN."register/activate/".$activateKey."'>".DOMAIN."register/activate/".$activateKey."</a><br /><br /><br />";
			$msg .= "Üdvözlettel,<br /><strong>A ".TITLE."</strong>";
		}else{
			$msg = '<h2>Dear '.trim($szam_lastname).', '.trim($szam_firstname).',</h2>';
			$msg .= "It is our pleasure to have you registered on our site. You can activate your account by clicking on the link under:<br /><br />";
			$msg .= "<a href='".DOMAIN."register/activate/".$activateKey."'>".DOMAIN."register/activate/".$activateKey."</a><br /><br /><br />";
			$msg .= "Your sincerely,<br /><strong> ".TITLE."</strong> <br> Hungarian, Handmade, Fine Jewelery<br> <br> Have a style to be unique have a charm to be a Lady!";
			/*
			It is our pleasure to have you registered on our site. You can activate your account by clicking on the link under:
			in case you did not registered on our site,  disregard this letter.
			Your sincerely
			Diuss
			Hungarian, Handmade, Fine Jewelery

			Have a style to be unique have a charm to be a Lady!
			*/
		}

		Helper::sendMail(array(
			'recepiens' => array($email),
			'msg' 		=> $msg,
			'tema' 		=> __('Üdvözöljük'),
			'from' 		=> NOREPLY_EMAIL,
			'sub' 		=> __('Aktiválás')
		));

		return $data;
	}

	function userExists($by = 'email', $val){
		$q = "SELECT ID FROM ".self::TABLE_NAME." WHERE ".$by." = '".$val."'";

		$c = $this->db->query($q);

		if($c->rowCount() == 0){
			return false;
		}else{
			return true;
		}
	}

	function isActivated($email){
		$q = "SELECT ID FROM ".self::TABLE_NAME." WHERE email = '".$email."' and actived != 0";

		$c = $this->db->query($q);

		if($c->rowCount() == 0){
			return false;
		}else{
			return true;
		}
	}

	function isEnabled($email){
		$q = "SELECT ID FROM ".self::TABLE_NAME." WHERE email = '".$email."' and blocked = 0";

		$c = $this->db->query($q);

		if($c->rowCount() == 0){
			return false;
		}else{
			return true;
		}
	}

	function validUser($email, $password){
		if($email == '' || $password == '') throw new Exception(__('Hiányzó adatok. Nem lehet azonosítani a felhasználót!'));

		$c = $this->db->query("SELECT ID FROM ".self::TABLE_NAME." WHERE email = '$email' and password = '".Hash::jelszo($password)."'");

		if($c->rowCount() == 0){
			return false;
		}else{
			return true;
		}
	}
}

?>
