<?
class Admin_Model extends Model {
	const LOGINHANDLE_MODE 				= LOGIN_MODE;
	const LOGINHANDLE_COOKIE_PREFIX 	= '__admin';
	const LOGINHANDLE_SESSION_PREFIX 	= '__admin';

	public $admin = null;
	public $priority = false;
	private $lang = 'hu';

	private $menu_type_names = array(
		'link' 			=> 'Link',
		'product' 		=> 'Termék',
		'collection' 	=> 'Kollekció termékei',
		'category' 		=> 'Termék kategória',
		'page' 			=> 'Oldal',
		'__webshop' 	=> 'Webshop',
		'__newproduct' 	=> 'Új termékek',
		'__favoritproduct' => 'Kedvenc termékek',
		'__ourstars' 	=> 'Sztárjaink'
	);

	private $order_pay_status = array(
		'COMPLETE' => array(
			'text' => 'Fizetve',
			'color' => 'green'
		),
		'IN_PROGRESS' => array(
			'text' => 'Fizetve - Igazolásra vár',
			'color' => '#e3a512'
		),
		'PAYEMENT_AUTHORIZED' => array(
			'text' => 'Fizetve - Igazolva',
			'color' => 'green'
		)
	);

	private $admin_priority_codes = array(
		0 => 'Adminisztrátor',
		1 => 'Moderátor'
	);

	// Megrendelés fizetési módok
	private $order_paymethodes = array('Bankkártya','Utánvétel','PayPal','Készpénz');
	// Megrendelés státuszok
	private $order_status = array('Feldolgozásra vár', 'Folyamatban', 'Fizetésre vár', 'Fizetve - Postázás alatt', 'Postázva', 'Teljesítve', 'Elutasítva');
	// Elérhető pénznemek
	private $currencies = array('huf','usd','eur');

	function __construct(){
		parent::__construct();

		$this->lang = Lang::getLang();

		$this->settings = $this->getSettings();

		switch(self::LOGINHANDLE_MODE){
			default: case 'session':
				$this->admin = $_SESSION[self::LOGINHANDLE_SESSION_PREFIX];
			break;
			case 'cookie':
				$this->admin = $this->getAdminByCookieToken($_COOKIE[self::LOGINHANDLE_COOKIE_PREFIX]);
			break;
		}

		$this->priority = $this->getAdminPriority($this->admin);
	}

	function getAdminPriority($id){
		if($id == '') return false;
		return $this->db->query("SELECT priority FROM ".TAGS::DB_TABLE_ADMIN." WHERE user = '$id'")->fetch(PDO::FETCH_COLUMN);
	}

	function getCurrencyCountries() {
		$back = array();

		$iq = "SELECT * FROM ".TAGS::DB_TABLE_CURRENCY_COUNTRIES.";";

		$q = $this->db->query($iq);

		$qd = $q->fetchAll(PDO::FETCH_ASSOC);

		$ret = array();
		foreach( $qd as $d ){
			$ret[$d['currency_code']][] = $d['country'];
		}

		$back = $ret;
		unset($ret);

		return $back;
	}

	function saveCurrencyCountries( $post ){
		extract( $post );

		$this->db->query("TRUNCATE TABLE ".TAGS::DB_TABLE_CURRENCY_COUNTRIES.";");

		foreach( $valuta as $v => $vl ) {
			foreach( $vl as $c ) {
				$this->db->insert(
					TAGS::DB_TABLE_CURRENCY_COUNTRIES,
					array( 'currency_code', 'country' ),
					array( $v, $c )
				);
			}
		}
	}

	function isLogged(){
		if(isset($this->admin) && $this->admin != ''){
			if(self::LOGINHANDLE_MODE == 'cookie'){
				setcookie(
					self::LOGINHANDLE_COOKIE_PREFIX,
					$_COOKIE[self::LOGINHANDLE_COOKIE_PREFIX],
					time()+60*60*24,
					"/"
				);
			}
			return true;
		}else{
			return false;
		}
	}

	function logout(){
		unset($_SESSION[self::LOGINHANDLE_SESSION_PREFIX]);
		setcookie(self::LOGINHANDLE_COOKIE_PREFIX,"",time()-3600,"/");
		header('Location: '.ADMROOT); exit;
	}

	function login($post){
		extract($post);

		if($admin_email == '') throw new Exception(__('Azonosító hiányzik, pótolja!'));
		if($admin_pw 	== '') throw new Exception(__('Jelszó hiányzik, pótolja!'));

		$admin_pw = Hash::jelszo($admin_pw);

		$iq = "SELECT ID, blocked FROM ".TAGS::DB_TABLE_ADMIN." WHERE user = '$admin_email' and pw = '$admin_pw'";

		$q = $this->db->query($iq);

		$qd = $q->fetch(PDO::FETCH_ASSOC);

		if($qd[blocked] == '1') throw new Exception(__('Hozzáférés korlátozásra került!'));

		if($q->rowCount() > 0){
			switch(self::LOGINHANDLE_MODE){
				default: case 'session':
					Session::set(self::LOGINHANDLE_SESSION_PREFIX,$user);
				break;
				case 'cookie':
					setcookie(self::LOGINHANDLE_COOKIE_PREFIX,$this->setCookieToken($admin_email),time()+60*60*24,"/");
				break;
			}
			$this->db->update(
				TAGS::DB_TABLE_ADMIN,
				array(
					'lastLoginDate' => NOW
				),
				"user = '$admin_email'"
			);
			header('Location: '.ADMROOT); exit;
		}else{
			throw new Exception(__('Hibás belépési azonosító. Próbálja újra!'));
		}
	}

	private function getAdminByCookieToken($token){
		$admin = $this->db->query("SELECT user FROM ".TAGS::DB_TABLE_ADMIN." WHERE valid_cookie_token = '$token'")->fetch(PDO::FETCH_COLUMN);

		return $admin;
	}

	private function setCookieToken($admin){
		$token = md5(time());
		$this->db->update(TAGS::DB_TABLE_ADMIN,
		array(
			"valid_cookie_token" => $token
		),
		"user = '$admin'");

		return $token;
	}

	public function setUsedLanguage( $lang_array = false )
	{
		if ( !$lang_array ) {
			return false;
		}
		$set = '';
		foreach ($lang_array as $value) {
			$set .= "'".$value."',";
		}

		$set = rtrim($set,',');


		$this->db->query(" UPDATE ".TAGS::DB_TABLE_LANGUAGE." SET active = 0 WHERE code != 'hu';");
		$this->db->query( $q = " UPDATE ".TAGS::DB_TABLE_LANGUAGE." SET active = 1 WHERE code IN (".$set.");");
	}

	/* KATALÓGUS */
	public function saveBook( $book_id, $post )
	{
		extract($post);

		if($title['hu'] == '') throw new Exception(__('Katalógus főcím (HU) megadása kötelező!'));

		foreach( $this->settings['all_languages'] as $lang ): if( $lang['code'] == 'hu' ) continue;
			$title[$lang['code']] = $title['hu'];
			//if($title[$lang['code']] == '') throw new Exception(__('Katalógus főcím ('.$lang['name'].') megadása kötelező!'));
		endforeach;

		if ( $collection == '' ) {
			throw new Exception(__('Katalógus kapcsolódó kollekció kiválasztása kötelező!'));
		}

		if ( $img == '' ) {
			throw new Exception(__('Katalógus borító háttérkép kiválasztása kötelező!'));
		}

		$data = array();

		foreach( $this->settings['all_languages'] as $lang ):
			$data['title_'.$lang['code']]	= addslashes($title[$lang['code']]);
		endforeach;

		$show_title = ($show_title == 'on') ? 1 : 0;

		$data['image'] = $img;
		$data['collection'] = $collection;
		$data['collection_marketing_text'] = ( $collection_text ) ? 1 : 0;
		$data['status'] = $status;
		$data['show_title'] = $show_title;

		$this->db->update(
			TAGS::DB_TABLE_BOOKS,
			$data,
			"ID = ".$book_id
		);
	}

	public function deleteBook( $id )
	{
		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_BOOKS." WHERE ID = ".$id.";");
	}

	public function saveBookPages( $bookid, $post)
	{
		extract($post);

		// Oldal törlés
		if ( $del_page != '' ) {
			$delpages 		= ltrim($del_page, ",");
			$delpagesarr 	= explode(",", $delpages);
			$delids 		= array();
			foreach ( $delpagesarr as $id ) {
				if ( !in_array($id, $delids) ) {
					$delids[] = $id;
				}
			}

			if ( count($delids) > 0 ) {
				foreach ($delids as $did) {
					$this->db->query("DELETE FROM ".TAGS::DB_TABLE_BOOKS_PAGES." WHERE ID = ".$did.";");
				}
			}
		}

		// Save
		if( count( $save_id) > 0 )
		foreach ( $save_id as $edit_id ) {
			$elink_pos 	= $link_position[$edit_id];
			$etitle 	= $title[$edit_id];
			$elinks 	= $link[$edit_id];
			$eimg  		= $image[$edit_id];


			$left_ids 	= array();
			$right_ids 	= array();

			/* */
			if( count($elinks['left']) > 0 )
			foreach ( $elinks['left'] as $id ) {
				if ( $id != '' ) {
					if ( !in_array( $id, $left_ids ) ) {
						$left_ids[] = $id;
					}
				}
			}
			/* */

			/* */
			if( count($elinks['right']) > 0 )
			foreach ( $elinks['right'] as $id ) {
				if ( $id != '' ) {
					if ( !in_array( $id, $right_ids ) ) {
						$right_ids[] = $id;
					}
				}
			}
			/* */
			/* */
			$data = array();
			$data['book_id'] 	= $bookid;
			$data['image'] 		= $eimg;
			/* */

			/* */
			foreach( $this->settings['all_languages'] as $lang ):
				$data['left_title_'.$lang['code']] 	= $etitle['left'][$lang['code']];
				$data['right_title_'.$lang['code']] = $etitle['right'][$lang['code']];
			endforeach;
			/* */

			/* */
			$data['left_product_ids'] 		= implode( ",", $left_ids);
			$data['right_product_ids'] 		= implode(",", $right_ids);
			$data['right_link_position'] 	= $elink_pos['right'];
			$data['left_link_position'] 	= $elink_pos['left'];

			$this->db->update(
				TAGS::DB_TABLE_BOOKS_PAGES,
				$data,
				"ID = ".$edit_id
			);
			/* */

		}

		// Create
		$row = -1;
		if( count( $new_title ) > 0 )
		foreach ($new_title as $key => $value) {
			$row = $key;
			break;
		}

		if( count($new_image ) > 0 ){
			foreach ( $new_image as $image ) {
				$link_pos 	= $new_link_position[$row];
				$title 		= $new_title[$row];
				$links 		= $new_link[$row];

				$left_ids = array();
				$right_ids = array();

				if( count($links['left']) > 0 )
				foreach ( $links['left'] as $id ) {
					if ( $id != '' ) {
						if ( !in_array( $id, $left_ids ) ) {
							$left_ids[] = $id;
						}
					}
				}

				if( count($links['right']) > 0 )
				foreach ( $links['right'] as $id ) {
					if ( $id != '' ) {
						if ( !in_array( $id, $right_ids ) ) {
							$right_ids[] = $id;
						}
					}
				}

				$head = array();
				$data = array();

				$head[] = 'book_id';
				$data[] = $bookid;
				$head[] = 'image';
				$data[] = $image;

				foreach( $this->settings['all_languages'] as $lang ):
					$head[] = 'left_title_'.$lang['code'];
					$data[] = $title['left'][$lang['code']];
					$head[] = 'right_title_'.$lang['code'];
					$data[] = $title['right'][$lang['code']];
				endforeach;

				$head[] = 'left_product_ids';
				$data[] = implode( ",", $left_ids);
				$head[] = 'right_product_ids';
				$data[] = implode(",", $right_ids);
				$head[] = 'right_link_position';
				$data[] = $link_pos['right'];
				$head[] = 'left_link_position';
				$data[] = $link_pos['left'];

				$this->db->insert(
					TAGS::DB_TABLE_BOOKS_PAGES,
					$head,
					$data
				);
			}
		}
	}

	public function addNewBook( $post )
	{
		extract($post);

		if($title['hu'] == '') throw new Exception(__('Katalógus főcím (HU) megadása kötelező!'));

		foreach( $this->settings['all_languages'] as $lang ): if( $lang['code'] == 'hu' ) continue;
			$title[$lang['code']] = $title['hu'];
			//if($title[$lang['code']] == '') throw new Exception(__('Katalógus főcím ('.$lang['name'].') megadása kötelező!'));
		endforeach;

		if ( $collection == '' ) {
			throw new Exception(__('Katalógus kapcsolódó kollekció kiválasztása kötelező!'));
		}

		if ( $img == '' ) {
			throw new Exception(__('Katalógus borító háttérkép kiválasztása kötelező!'));
		}

		$head = array();
		$data = array();

		foreach( $this->settings['all_languages'] as $lang ):
			$head[] = 'title_'.$lang['code'];
			$data[]	= addslashes($title[$lang['code']]);
		endforeach;

		$head[] = 'image';
		$data[] = $img;
		$head[] = 'collection';
		$data[] = $collection;
		$head[] = 'collection_marketing_text';
		$data[] = ( $collection_text == 'on' ) ? 1 : 0;
		$head[] = 'status';
		$data[] = $status;

		$this->db->insert(
			TAGS::DB_TABLE_BOOKS,
			$head,
			$data
		);
	}

	public function getAllBooks( $arg = array() )
	{
		$q = "SELECT b.* ";
		$q .= ", b.title_".$this->lang." as title ";
		$q .= ", c.name_".$this->lang." as collection_name ";
		$q .= ", c.description_".$this->lang." as collection_text ";
		$q .= ", c.ID as collection_id ";
		$q .= " FROM ".TAGS::DB_TABLE_BOOKS." as b
		LEFT OUTER JOIN ".TAGS::DB_TABLE_COLLECTIONS." as c ON c.ID = b.collection
		";
		$q .= " WHERE b.ID IS NOT NULL";
		if ( $arg['collection'] ) {
			$q .= " and c.name_".$this->lang." = '".$arg['collection']."' ";
		}
		if ( $arg['ID'] ) {
			$q .= " and b.ID = ".$arg['ID']." ";
		}

		$arg[multi] = 1;
		extract($this->db->q($q, $arg));

 		$bdata = array();
		foreach ( $data as $d ) {
			$d['pages'] = $this->getBookPage( $d['ID'] );
			$bdata[] = $d;
		}

		$ret[data] = $bdata;

		return $ret;
	}

	public function getBookPage( $book_id )
	{
		$q = "SELECT bp.* ";

		$q .= " FROM ".TAGS::DB_TABLE_BOOKS_PAGES." as bp";
		$q .= " WHERE bp.ID IS NOT NULL and bp.book_id = $book_id;";

		$arg[multi] = 1;
		extract($this->db->q($q, $arg));

 		$bdata = array();
		foreach ( $data as $d ) {
			$d['product_set']['left'] 	= $this->getBookPageProducts( $d['left_product_ids'] );
			$d['product_set']['right'] 	= $this->getBookPageProducts( $d['right_product_ids'] );
			$bdata[] = $d;
		}

		return $bdata;
	}

	public function getBookPageProducts( $id_set ) {
		$ret = array();

		if ( empty($id_set) ) {
			return $ret;
		}

		$set = explode( ",", $id_set );

		$q = "SELECT
			p.ID,
			p.name_".$this->lang." as name,
			c.name_".$this->lang." as collection
		FROM ".TAGS::DB_TABLE_PRODUCTS." as p
		LEFT OUTER JOIN ".TAGS::DB_TABLE_COLLECTIONS." as c ON c.ID = p.collectionID
		WHERE p.ID IN (".$id_set.");";

		$arg['multi'] = 1;
		extract($this->db->q($q, $arg ));

		return $data;
	}

	/* End of katalógus */

	/*MENÜK*/
	public function addNewMenu($group = false, $post){
		extract($post);
		if(!$group) return false;

		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			if($text[$lang['code']] == '') throw new Exception(__('Menü felirat('.$lang['name'].') megadása kötelező!'));
		endforeach;

		$link 			= array();
		$inserterTxt 	= NULL;

		if($contentBy == 'link'){
			foreach( $this->settings['all_languages'] as $lang ):
				$link[$lang['code']] = $menu;
			endforeach;

			$inserterTxt 	= $menu;
		}else{
			switch($contentBy){
				case '__webshop':
					$link['hu'] 	= '/webshop';
					$link['en']		= $link['hu'];
					$link['rus']	= $link['hu'];
					$link['ger']	= $link['hu'];
					$inserterTxt 	= 'Webshop';
				break;
				case '__newproduct':
					$link['hu'] 	= '/webshop/news';
					$link['en']		= $link['hu'];
					$link['rus']	= $link['hu'];
					$link['ger']	= $link['hu'];
					$inserterTxt 	= 'Új termékek';
				break;
				case '__favoritproduct':
					$link['hu']		= '/webshop/favorites';
					$link['en']		= $link['hu'];
					$link['rus']	= $link['hu'];
					$link['ger']	= $link['hu'];
					$inserterTxt 	= 'Kedvenc termékek';
				break;
				case '__ourstars':
					$link['hu']		= '/sztarjaink';
					$link['en']		= '/our_stars';
					$link['rus']	= '/our_stars';
					$link['ger']	= '/our_stars';
					$inserterTxt 	= 'Sztárjaink';
				break;
			}
		}


		$head = array();
		$data = array();

		$parent = ($parent) ? $parent : NULL;

		$head[] = 'groupKey';
		$data[]	= $group;
		$head[] = 'parent_id';
		$data[]	= $parent;
		$head[] = 'showed';
		$data[]	= $status;
		$head[] = 'insertedBy';
		$data[]	= $contentBy;
		$head[] = 'inserterTxt';
		$data[]	= $inserterTxt;
		$head[] = 'connect_item_id';
		$data[]	= $menu;

		foreach( $this->settings['all_languages'] as $lang ):
			$head[] = 'text_'.$lang['code'];
			$data[]	= addslashes($text[$lang['code']]);
			$head[] = 'link_'.$lang['code'];
			$data[]	= $link[$lang['code']];
		endforeach;

		$this->db->insert(
			TAGS::DB_TABLE_MENUS,
			$head,
			$data
		);
	}

	public function loadAllMenu($which = false){
		$menu_list = array();

		if(!$which) return $menu_list;

		$q 		= "SELECT
			*
		FROM ".TAGS::DB_TABLE_MENUS." WHERE groupKey = '$which' and parent_id is null ORDER BY priority ASC";

		$qry 	= $this->db->query($q);

		if($qry->rowCount() > 0){
			$menu_list 	= $qry->fetchAll(PDO::FETCH_ASSOC);
			$data 		= array();

			foreach($menu_list as $m){
				$m[insertedByText] = $this->getMenuTypeName($m[insertedBy]);
				$m[sub] = $this->loadAllMenuSub( $which, $m['ID'] );
				$data[] = $m;
			}

			$menu_list = $data;
		}

		return $menu_list;
	}

	public function loadAllMenuSub($which = false, $parent_id){
		$menu_list = array();

		if(!$which) return $menu_list;

		$q 		= "SELECT
			*
		FROM ".TAGS::DB_TABLE_MENUS." WHERE groupKey = '$which' and parent_id = $parent_id ORDER BY priority ASC";

		$qry 	= $this->db->query($q);

		if($qry->rowCount() > 0){
			$menu_list 	= $qry->fetchAll(PDO::FETCH_ASSOC);
			$data 		= array();

			foreach($menu_list as $m){
				$m[insertedByText] = $this->getMenuTypeName($m[insertedBy]);
				$data[] = $m;
			}

			$menu_list = $data;
		}

		return $menu_list;
	}

	protected function getMenuTypeName($type_key){
		if(!array_key_exists($type_key, $this->menu_type_names)){ return $type_key; }

		return $this->menu_type_names[$type_key];
	}
	public function getMenuData($id){
		if($id == '') return false;

		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_MENUS." WHERE ID = $id";
		$qry 	= $this->db->query($q);

		if($qry->rowCount() == 0) return false;

		return $qry->fetch(PDO::FETCH_ASSOC);
	}

	public function editMenu($id, $post){
		extract($post);

		if(!$id) throw new Exception(__('Hibás kollekció azonosító. Vagy a kollekció már nem létezik!'));

		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			if($text[$lang['code']] == '') throw new Exception(__('Menü felirat('.$lang['name'].') megadása kötelező!'));
		endforeach;

		$link = array();
		$inserterTxt 	= NULL;

		if($contentBy == 'link'){
			foreach( $this->settings['all_languages'] as $lang ):
				$link[$lang['code']] = $menu;
			endforeach;
			$inserterTxt 	= $menu;
		}else{
			switch($contentBy){
				case '__webshop':
					$link['hu'] 	= '/webshop';
					$link['en']		= $link['hu'];
					$link['rus']	= $link['hu'];
					$link['ger']	= $link['hu'];
					$inserterTxt 	= 'Webshop';
				break;
				case '__newproduct':
					$link['hu'] 	= '/webshop/news';
					$link['en']		= $link['hu'];
					$link['rus']	= $link['hu'];
					$link['ger']	= $link['hu'];
					$inserterTxt 	= 'Új termékek';
				break;
				case '__favoritproduct':
					$link['hu']		= '/webshop/favorites';
					$link['en']		= $link['hu'];
					$link['rus']	= $link['hu'];
					$link['ger']	= $link['hu'];
					$inserterTxt 	= 'Kedvenc termékek';
				break;
				case '__ourstars':
					$link['hu']		= '/sztarjaink';
					$link['en']		= '/our_stars';
					$link['rus']	= '/our_stars';
					$link['ger']	= '/our_stars';
					$inserterTxt 	= 'Sztárjaink';
				break;
			}
		}

		$data = array();
		$data['showed'] 	= $showed;
		$data['groupKey'] = $groupKey;
		$data['insertedBy'] = $contentBy;
		$data['connect_item_id'] = $menu;
		$data['inserterTxt'] = $inserterTxt;

		foreach( $this->settings['all_languages'] as $lang ):
			$data['text_'.$lang['code']] = addslashes($text[$lang['code']]);
			$data['link_'.$lang['code']] = $link[$lang['code']];
		endforeach;

		$this->db->update(
			TAGS::DB_TABLE_MENUS,
			$data,
			"ID = $id"
		);
	}

	public function delMenu($id){
		if(!$id)return false;

		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_MENUS." WHERE ID = $id");
	}
	#/MENÜK

	/* OLDALAK */
	public function addNewPage($post){
		extract($post);
		$bgName = false;

		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			if($title[$lang['code']] == '') throw new Exception(__('Hiányzik az oldal címe ('.$lang['name'].')!'));
			if($url[$lang['code']] == '') throw new Exception(__('Hiányzik az oldal egyedi url-je ('.$lang['name'].')!'));

			$hashtag[$lang['code']] = ltrim($hashtag[$lang['code']], '::');

			if ( !$bgName ) {
				$bgName = trim($url[$lang['code']]);
			}

		endforeach;

		$showed = $status;

		$head = array();
		foreach( $this->settings['all_languages'] as $lang ):
			$head[] = 'url_text_'.$lang['code'];
			$head[] = 'title_'.$lang['code'];
			$head[] = 'description_'.$lang['code'];
			$head[] = 'keywords_'.$lang['code'];
			$head[] = 'content_'.$lang['code'];
			$head[] = 'hashtag_'.$lang['code'];
		endforeach;
		$head[] = 'showed';
		$head[] = 'scrollable';

		$data = array();
		foreach( $this->settings['all_languages'] as $lang ):
			$data[] = addslashes($url[$lang['code']]);
			$data[] = addslashes($title[$lang['code']]);
			$data[] = addslashes($description[$lang['code']]);
			$data[] = addslashes($keywords[$lang['code']]);
			$data[] = addslashes($content[$lang['code']]);
			$data[] = addslashes($hashtag[$lang['code']]);
		endforeach;

		$data[]	= $showed;
		$data[]	=  $scrollable;

		$q = $this->db->insert(
			TAGS::DB_TABLE_PAGES,
			$head,
			$data
		);

		// Kép feltöltés
		if($_FILES[bgImg][name][0] != ''){
			$idir 	= TAGS::VALUE_BG_STATICPAGE_ROOT;
			if(!file_exists($idir)){
				mkdir($idir,0777,true);
			}

			$img = Images::upload(array(
				'src' 			=> 'bgImg',
				'upDir' 		=> $idir,
				'noRoot' 		=> true,
				'maxFileSize' 	=> 1024,
				'noThumbImg' 	=> true,
				'noWaterMark' 	=> true,
				'fileName' 		=> 'bg-staticpage-'.$bgName
			));
		}
	}
	public function getPageData($page_ID){
		if(!$page_ID) return array();

		$q = $this->db->query("SELECT * FROM ".TAGS::DB_TABLE_PAGES." WHERE ID = $page_ID");

		if($q->rowCount() == 0) return array();


		return $q->fetch(PDO::FETCH_ASSOC);
	}

	public function editPage($page_ID, $post){
		extract($post);
		$bgName = false;

		if(!$page_ID) throw new Exception(__('Hibás oldal azonosító. Vagy az oldal már nem létezik!'));
		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			if($title[$lang['code']] == '') throw new Exception(__('Hiányzik az oldal címe ('.$lang['name'].')!'));
			if($url[$lang['code']] == '') throw new Exception(__('Hiányzik az oldal egyedi url-je ('.$lang['name'].')!'));

			$hashtag[$lang['code']] = ltrim($hashtag[$lang['code']], '::');

			if ( !$bgName ) {
				$bgName = trim($url[$lang['code']]);
			}

		endforeach;

		$showed = $status;

		$data = array();
		foreach( $this->settings['all_languages'] as $lang ):
			$data['url_text_'.$lang['code']] = addslashes(Helper::makeSafeUrl($url[$lang['code']], ''));
			$data['title_'.$lang['code']] = addslashes($title[$lang['code']]);
			$data['description_'.$lang['code']] = addslashes($description[$lang['code']]);
			$data['keywords_'.$lang['code']] = addslashes($keywords[$lang['code']]);
			$data['content_'.$lang['code']] = addslashes($content[$lang['code']]);
			$data['hashtag_'.$lang['code']] = addslashes($hashtag[$lang['code']]);
		endforeach;

		$data['showed']	= $showed;
		$data['lastModified']	= NOW;
		$data['scrollable']	=  $scrollable;

		$this->db->update(
			TAGS::DB_TABLE_PAGES,
			$data,
			"ID = $page_ID"
		);

		// Kép feltöltés
		if($_FILES[bgImg][name][0] != ''){
			$idir 	= TAGS::VALUE_BG_STATICPAGE_ROOT;
			if(!file_exists($idir)){
				mkdir($idir,0777,true);
			}

			$img = Images::upload(array(
				'src' 			=> 'bgImg',
				'upDir' 		=> $idir,
				'noRoot' 		=> true,
				'maxFileSize' 	=> 1024,
				'noThumbImg' 	=> true,
				'noWaterMark' 	=> true,
				'fileName' 		=> 'bg-staticpage-'.$bgName
			));
		}
	}

	public function delPage($page_ID){
		if(!$page_ID)return false;

		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_PAGES." WHERE ID = $page_ID");
	}


	public function loadAllPages($arg = array() ){
		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_PAGES." ORDER BY lastModified DESC";

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		return $ret;
	}
	#/OLDALAK

	/* KOLLEKCIÓK */
	public function addNewCollection($post){
		extract($post);

		$head = array();
		foreach( $this->settings['all_languages'] as $lang ):
			$head[] = 'name_'.$lang['code'];
			$head[] = 'description_'.$lang['code'];
		endforeach;
		$head[] = 'showed';

		$data = array();
		foreach( $this->settings['all_languages'] as $lang ):
			$data[] = addslashes($name['hu']);
			$data[] = addslashes($description[$lang['code']]);
		endforeach;
		$data[] = $status;

		$this->db->insert(TAGS::DB_TABLE_COLLECTIONS,
			$head,
			$data
		);
	}
	public function getCollectionData($id){
		if(!$id) return array();

		$q = $this->db->query("SELECT * FROM ".TAGS::DB_TABLE_COLLECTIONS." WHERE ID = $id");


		if($q->rowCount() == 0) return array();

		return $q->fetch(PDO::FETCH_ASSOC);
	}

	public function getCollectionProducts( $collectionID = false ){
		$q = $this->db->query("SELECT ID, name_hu FROM ".TAGS::DB_TABLE_PRODUCTS." WHERE collectionID = $collectionID ORDER BY name_hu ASC");


		if($q->rowCount() == 0) return array();

		return $q->fetchAll(PDO::FETCH_ASSOC);
	}

	public function editCollection($id, $post){
		extract($post);

		if(!$id) throw new Exception(__('Hibás kollekció azonosító. Vagy a kollekció már nem létezik!'));

		$showed 	= $status;
		$orderInt 	= ($orderInt == '') ? 0 : $orderInt;

		$data = array();
		foreach( $this->settings['all_languages'] as $lang ):
			$data['name_'.$lang['code']] = addslashes($name['hu']);
			$data['description_'.$lang['code']] = addslashes($description[$lang['code']]);
		endforeach;

		$data['showed'] = $showed;
		$data['orderInt'] = $orderInt;
		$data['def_termekID'] = $def_termekID;
		$data['lastModified'] = NOW;

		$this->db->update(
			TAGS::DB_TABLE_COLLECTIONS,
			$data,
			"ID = $id"
		);
	}

	public function delCollection($id){
		if(!$id)return false;

		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_COLLECTIONS." WHERE ID = $id");
	}


	public function loadAllCollections($arg = array() ){
		$q 		= "SELECT
			c.*,
			p.name_hu as termek_name
		FROM ".TAGS::DB_TABLE_COLLECTIONS." as c
		LEFT OUTER JOIN ".TAGS::DB_TABLE_PRODUCTS." as p ON p.ID = c.def_termekID
		ORDER BY
			c.orderInt ASC";

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		return $ret;
	}
	#/KOLLEKCIÓK

	/* TERMÉKEK */
	public function addNewProduct($post){
		extract($post);

		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			if($name[$lang['code']] == '') throw new Exception(__('Hiányzik a termék neve ('.$lang['name'].')!'));
		endforeach;

		if($collection == '') throw new Exception(__('Válassza ki, hogy melyik kollekcióba tartozik a létrehozandó termék!'));
		if($productNumber == '') throw new Exception(__('Kérjük, hogy adja meg a termék cikkszámát!'));
		if($afa == '') throw new Exception(__('Kérjük, hogy adja meg az ÁFA mértékét!'));

		if($productVariation[name][hu][0] == '') throw new Exception(__('Terméket nem hozhat létre, ha nincs legalább egy (1) variáció!'));

		$product_error 	= false;
		$all_name 		= count($productVariation[name]);

		for($s = 0; $s <= count($productVariation[name][hu]); $s++) {
			foreach( $this->settings['all_languages'] as $lang ):
				if( $lang['active'] == 0 ) continue;
				if ( empty($productVariation[name][$lang['code']][$s]) ) {
					$product_error = true;
					$all_name--;
				}
			endforeach;

			if( true  ) {
				$p_huf 	= $productVariation[price][huf][$s];
				$p_usd 	= $productVariation[price][usd][$s];
				$p_eur 	= $productVariation[price][eur][$s];
				$p_stock= $productVariation[stock][$s];

				if( $p_huf == '' || $p_usd == '' || $p_eur == '' || $p_stock == '' ){
					$product_error = true;
					break;
				}
			}
		}

		//if( $product_error && $all_name != 0 ) throw new Exception(__('Az egyik termék variációnál hiányzik egy adat. Kérjük pótolja!'));

		//throw new Exception(__('TESZT OK'));

		$head = array();

		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			$head[] = 'name_'.$lang['code'];
			$head[] = 'keywords_'.$lang['code'];
			$head[] = 'content_'.$lang['code'];
			$head[] = 'description_'.$lang['code'];
		endforeach;

		$head[] = 'productNumber';
		$head[] = 'collectionID';
		$head[] = 'afa';
		$head[] = 'is_favorite';
		$head[] = 'is_news';
		$head[] = 'categoryID';
		$head[] = 'showed';

		$item = array();

		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			$item[] = addslashes($name[$lang['code']]);
			$item[] = addslashes($keywords[$lang['code']]);
			$item[] = addslashes($content[$lang['code']]);
			$item[] = addslashes($parameters[$lang['code']]);
		endforeach;

		$item[] =	$productNumber;
		$item[] =	$collection;
		$item[] =	$afa;
		$item[] =	$is_favorite;
		$item[] =	$is_news;
		$item[] =	$categoryID;
		$item[] =	$showed;

		$this->db->insert(TAGS::DB_TABLE_PRODUCTS,
			$head,
			$item
		);

		$insertedProductId = $this->db->lastInsertId();

		for($s = 0; $s <= count($productVariation[name][hu]); $s++){
			$p_huf 	= $productVariation[price][huf][$s];
			$p_usd 	= $productVariation[price][usd][$s];
			$p_eur 	= $productVariation[price][eur][$s];
			$p_stock= $productVariation[stock][$s];

			foreach( $this->settings['all_languages'] as $lang ):
				if( $lang['active'] == 0 ) continue;
				if ( $productVariation[name][$lang['code']][$s] == '' ) {
					$break = true; break;
				}
			endforeach;

			if( !$break ){

				$head = array('productID');

				foreach( $this->settings['all_languages'] as $lang ):
					if( $lang['active'] == 0 ) continue;
					$head[] = 'name_'.$lang['code'];
				endforeach;

				$head[] = 'price_huf';
				$head[] = 'price_usd';
				$head[] = 'price_eur';
				$head[] = 'stock';

				$item = array();
				$item[] = $insertedProductId;

				foreach( $this->settings['all_languages'] as $lang ):
					if( $lang['active'] == 0 ) continue;
					$item[]  = addslashes($productVariation[name][$lang['code']][$s]);
				endforeach;

				$item[] = $p_huf;
				$item[] = $p_usd;
				$item[] = $p_eur;
				$item[] = $p_stock;

				$this->db->insert(
					TAGS::DB_TABLE_PRODUCT_VARIATIONS,
					$head,
					$item
				);
			}
		}

		// Kép feltöltés
		if($_FILES[img][name][0] != ''){
			$idir 	= 'images/products';
			if(!file_exists($idir)){
				mkdir($idir,0777,true);
			}

			$img = Images::upload(array(
				'src' 			=> 'img',
				'upDir' 		=> $idir,
				'noRoot' 		=> true,
				'maxFileSize' 	=> 1024,
				'noThumbImg' 	=> true,
				'noWaterMark' 	=> true
			));

			$s = 0;
			$uploadedImageNum = $this->countProductImages($id);
			foreach($img[allUploadedFiles] as $ui){ $s++;
				$default = 0;
				if($uploadedImageNum == 0 && $s == 1){
					$default = 1;
				}
				$this->db->insert(
					TAGS::DB_TABLE_PRODUCTS_IMAGES,
					array('productID', 'image', 'is_default'),
					array($insertedProductId, $ui, $default)
				);
			}
		}
	}

	public function getProductImages($id){
		if(!$id) return array();

		$q = "SELECT * FROM ".TAGS::DB_TABLE_PRODUCTS_IMAGES." WHERE productID = $id";

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		return $data;
	}

	public function editProduct($id, $post){
		$backMsg = '';
		if($id == '') throw new Exception(__('A termék nem szerkeszthető. Hibás termék azonosító vagy a termék már nem létezik!'));

		extract($post);

		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			if($name[$lang['code']] == '') throw new Exception(__('Hiányzik a termék neve ('.$lang['name'].')!'));
		endforeach;

		if($collection == '') throw new Exception(__('Válassza ki, hogy melyik kollekcióba tartozik a létrehozandó termék!'));
		if($productNumber == '') throw new Exception(__('Kérjük, hogy adja meg a termék cikkszámát!'));
		if($afa == '') throw new Exception(__('Kérjük, hogy adja meg az ÁFA mértékét!'));

		$this->db->update(
			TAGS::DB_TABLE_PRODUCTS,
			array(
				'productNumber' => $productNumber,
				'collectionID' 	=> $collection,
				'afa' 			=> $afa,
				'showed' 		=> $showed,
				'is_favorite' 	=> $is_favorite,
				'is_news' 		=> $is_news,
				'categoryID' 	=> $categoryID,
				'lastModified' 	=> NOW
			),
			"ID = $id"
		);

		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			$this->db->update(
			TAGS::DB_TABLE_PRODUCTS,
			array(
				'name_'.$lang['code'] 			=> addslashes($name[$lang['code']]),
				'description_'.$lang['code']	=> addslashes($parameters[$lang['code']]),
				'content_'.$lang['code'] 		=> addslashes($content[$lang['code']]),
				'keywords_'.$lang['code']		=> addslashes($keywords[$lang['code']])
			),
			"ID = $id"
		);
		endforeach;

		$backMsg .= "Változások mentésre kerültek!";

		$product_error 	= false;
		$all_name 		= count($productVariation[name]);

		for($s = 0; $s <= count($productVariation[name][hu]); $s++) {

			foreach( $this->settings['all_languages'] as $lang ):
				if( $lang['active'] == 0 ) continue;
				if ( empty($productVariation[name][$lang['code']][$s]) ) {
					$product_error = true;
					$all_name--;
				}
			endforeach;

			if( true  ) {
				$p_huf 	= $productVariation[price][huf][$s];
				$p_usd 	= $productVariation[price][usd][$s];
				$p_eur 	= $productVariation[price][eur][$s];
				$p_stock= $productVariation[stock][$s];

				if( $p_huf == '' || $p_usd == '' || $p_eur == '' || $p_stock == '' ){
					$product_error = true;
					break;
				}
			}
		}

		$break = false;

		if( $product_error && $all_name != 0 ) throw new Exception(__('Az egyik termék variációnál hiányzik egy adat. Kérjük pótolja!'));

		$newInsertedProducts = 0;

		for($s = 0; $s <= count($productVariation[name][hu]); $s++){
			$p_huf 	= $productVariation[price][huf][$s];
			$p_usd 	= $productVariation[price][usd][$s];
			$p_eur 	= $productVariation[price][eur][$s];
			$p_stock= $productVariation[stock][$s];

			foreach( $this->settings['all_languages'] as $lang ):
				if( $lang['active'] == 0 ) continue;
				if ( $productVariation[name][$lang['code']][$s] == '' ) {
					$break = true; break;
				}
			endforeach;

			if( !$break ){

				$head = array('productID');

				foreach( $this->settings['all_languages'] as $lang ):
					$head[] = 'name_'.$lang['code'];
				endforeach;

				$head[] = 'price_huf';
				$head[] = 'price_usd';
				$head[] = 'price_eur';
				$head[] = 'stock';

				$item = array();
				$item[] = $id;

				foreach( $this->settings['all_languages'] as $lang ):
					$item[]  = addslashes($productVariation[name][$lang['code']][$s]);
				endforeach;

				$item[] = $p_huf;
				$item[] = $p_usd;
				$item[] = $p_eur;
				$item[] = $p_stock;

				$this->db->insert(
					TAGS::DB_TABLE_PRODUCT_VARIATIONS,
					$head,
					$item
				);

				$newInsertedProducts++;
			}
		}

		// Kép feltöltés
		if($_FILES[img][name][0] != ''){
			$idir 	= 'images/products';
			if(!file_exists($idir)){
				mkdir($idir,0777,true);
			}

			$img = Images::upload(array(
				'src' 			=> 'img',
				'upDir' 		=> $idir,
				'noRoot' 		=> true,
				'maxFileSize' 	=> 1024,
				'noThumbImg' 	=> true,
				'noWaterMark' 	=> true
			));

			$s = 0;
			$uploadedImageNum = $this->countProductImages($id);
			foreach($img[allUploadedFiles] as $ui){ $s++;
				$default = 0;
				if($uploadedImageNum == 0 && $s == 1){
					$default = 1;
				}
				$this->db->insert(
					TAGS::DB_TABLE_PRODUCTS_IMAGES,
					array('productID', 'image', 'is_default'),
					array($id, $ui, $default)
				);
			}
		}

		// Képek módosítása
			//Alapértelmezetté tétel
			if($defaultImg != ''){
				$this->db->update(
					TAGS::DB_TABLE_PRODUCTS_IMAGES,
					array(
						'is_default' => 0
					),
					"productID = $id"
				);
				$this->db->update(
					TAGS::DB_TABLE_PRODUCTS_IMAGES,
					array(
						'is_default' => 1
					),
					"ID = $defaultImg"
				);
			}
			// Termék kép törlése
			if(count($delImage) > 0){
				foreach($delImage as $did){
					$this->deleteProductImage($did, $id);
				}
			}

		if($newInsertedProducts > 0){
			$backMsg .= "<br><strong>".$newInsertedProducts . ' ' .__('db új variáció hozzáadva!')."</strong>";
		}

		return __($backMsg);
	}

	protected function deleteProductImage($id, $productID){
		if($id == '') return false;

		$i = $this->db->query("SELECT image, is_default FROM ".TAGS::DB_TABLE_PRODUCTS_IMAGES." WHERE ID = $id")->fetch(PDO::FETCH_ASSOC);

		if($i[image] != ''){
			$this->db->query("DELETE FROM ".TAGS::DB_TABLE_PRODUCTS_IMAGES." WHERE ID = $id");

			unlink($i[image]);

			if($i[is_default] == '1'){
				$this->setProductImageDefault($productID);
			}
		}
	}

	protected function setProductImageDefault($id){
		if($id == '') return 0;

		$i = $this->db->query("SELECT ID FROM ".TAGS::DB_TABLE_PRODUCTS_IMAGES." WHERE productID = $id ORDER BY ID ASC LIMIT 0,1")->fetch(PDO::FETCH_ASSOC);

		$this->db->update(
			TAGS::DB_TABLE_PRODUCTS_IMAGES,
			array(
				'is_default' => 1
			),
			"ID = ".$i[ID]
		);
	}

	protected function countProductImages($id){
		if($id == '') return 0;

		return $this->db->query("SELECT count(ID) FROM ".TAGS::DB_TABLE_PRODUCTS_IMAGES." WHERE productID = $id")->fetch(PDO::FETCH_COLUMN);
	}

	public function delProductVariation($id){
		if($id == '') return false;

		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_PRODUCT_VARIATIONS." WHERE ID = $id");
	}

	protected function delProductAllVariation($id){
		if($id == '') return false;

		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_PRODUCT_VARIATIONS." WHERE productID = $id");
	}

	public function delProduct($id){
		if($id == '') return false;

		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_PRODUCTS." WHERE ID = $id");

		$this->delProductAllVariation($id);
	}

	public function getProductData($id){
		if($id == '') return false;

		$q = "SELECT
			t.*,
			c.name_hu as collection_name_hu,
			c.name_en as collection_name_en
		FROM ".TAGS::DB_TABLE_PRODUCTS." as t
		LEFT OUTER JOIN ".TAGS::DB_TABLE_COLLECTIONS." as c ON c.ID = t.collectionID
		WHERE t.ID = $id";

		extract($this->db->q($q, $arg));

		$data[variations] 	= $this->getProductVariations($id);
		$data[images] 		= $this->getProductImages($id);

		return $data;
	}

	public function editProductVariation($id, $post){
		$backMsg = '';
		extract($post);

		if($id == '') throw new Exception(__('Hibás variáció azonosító vagy a variáció már nem létezik!'));

		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			if($name[$lang['code']] == '') throw new Exception(__('Hiányzik a termék neve ('.$lang['name'].')!'));
		endforeach;

		$data = array();
		foreach( $this->settings['all_languages'] as $lang ):
			if( $lang['active'] == 0 ) continue;
			$data['name_'.$lang['code']] = addslashes($name[$lang['code']]);
		endforeach;

		$data['price_huf'] 	= $price[huf];
		$data['price_usd'] 	= $price[usd];
		$data['price_eur']	= $price[eur];

		$data['stock'] 		= $stock;
		$data['avaiable'] 	= $avaiable;

		$this->db->update(
			TAGS::DB_TABLE_PRODUCT_VARIATIONS,
			$data,
			"ID = $id"
		);

		$backMsg = __("A termék variáció adatait sikeresen elmentettük!");

		return $backMsg;
	}

	public function getProductVariationData($id){
		if($id == '') return false;

		$q = "SELECT
			v.*,
			p.name_hu as product_name_hu,
			p.name_en as product_name_en,
			c.name_hu as collection_name_hu,
			c.name_en as collection_name_en,
			p.productNumber
		FROM ".TAGS::DB_TABLE_PRODUCT_VARIATIONS." as v
		LEFT OUTER JOIN ".TAGS::DB_TABLE_PRODUCTS." as p ON p.ID = v.productID
		LEFT OUTER JOIN ".TAGS::DB_TABLE_COLLECTIONS." as c ON c.ID = p.collectionID
		WHERE v.ID = $id";

		extract($this->db->q($q, $arg));

		return $data;
	}

	public function loadAllProducts($arg = array()){
		$q = "SELECT
			t.*,
			c.name_hu as collection_name_hu,
			c.name_en as collection_name_en,
			ct.name_hu as category_name_hu,
			ct.name_en as category_name_en
		FROM ".TAGS::DB_TABLE_PRODUCTS." as t
		LEFT OUTER JOIN ".TAGS::DB_TABLE_COLLECTIONS." as c ON c.ID = t.collectionID
		LEFT OUTER JOIN ".TAGS::DB_TABLE_PRODUCT_CATEGORY." as ct ON ct.ID = t.categoryID
		WHERE t.ID IS NOT NULL
		";

		if(count($arg[filters]) > 0){
			foreach($arg[filters] as $key => $v){
				switch($key)
				{
					case 'name':
						$q .= " and (t.name_hu like '%".$v."%' or t.name_en like '%".$v."%') ";
					break;
					case 'category':
						$q .= " and t.categoryID = $v ";
					break;
					case 'collection':
						$q .= " and t.collectionID = $v ";
					break;
					case 'productNumber':
						$q .= " and t.productNumber LIKE '%$v%' ";
					break;
				}

			}
		}

		// Order
		if( !$arg['order'] ) {
			$q .= " ORDER BY t.addedAt DESC ";
		} else {
			$q .= " ORDER BY ".$arg['order'];
		}

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		$bdata = array();

		foreach($data as $d){
			$d[variations] = $this->getProductVariations($d[ID]);
			$bdata[] = $d;
		}

		$ret[data] = $bdata;

		return $ret;
	}

	public function getProductVariations($productID){
		if(!$productID || $productID == '') return array();

		$q = "SELECT * FROM ".TAGS::DB_TABLE_PRODUCT_VARIATIONS." WHERE productID = $productID";

		$qry = $this->db->query($q);

		if($qry->rowCount() == 0) return array();

		return $qry->fetchAll(PDO::FETCH_ASSOC);
	}

	public function loadAllProductCategories($arg = array()){
		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_PRODUCT_CATEGORY." ORDER BY name_hu ASC";

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		return $ret;
	}
	#/TERMÉKEK

	/* FELHASZNÁLÓK */
	public function loadAllUsers($arg = array()){
		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_USERS."";

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		return $ret;
	}
	public function loadUserData($id, $arg = array()){
		if(!$id) return false;

		$ret 			= array();

		$ret[data] 		= $this->db->query("SELECT * FROM ".TAGS::DB_TABLE_USERS." WHERE ID = $id")->fetch(PDO::FETCH_ASSOC) ;

		if(!$arg[onlyUserData])
			$ret[orders] 	= ($id) ? $this->getUserOrders($id) : false;

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

		return $ret;
	}

	protected function getUserOrders($userID, $arg = array()){
		$back = array();

		$q = "SELECT
		o.*,
		c.couponKey as couponName,
		c.action_rate as couponRate,
		CONCAT(u.szam_firstname, ' ', u.szam_lastname) as who
		FROM ".TAGS::DB_TABLE_ORDERS." as o
		LEFT OUTER JOIN ".TAGS::DB_TABLE_COUPONS." as c ON c.ID = o.couponID
		LEFT OUTER JOIN ".TAGS::DB_TABLE_USERS." as u ON u.ID = o.userID
		WHERE o.ID IS NOT NULL ";

		if($userID){
			$q .= " and o.userID = $userID ";
		}

		$q .= " ORDER BY o.orderedAt DESC";

		$arg[multi] = '1';
		extract($this->db->q($q,$arg));

		foreach($data as $d){
			$price = 0;

			$d[giftcard] = $this->getGiftcardOnOrder($d[ID], $d[priceCode]);
			$d[items] = $this->orderedItems($d[ID], array('price_code' => $d[priceCode]));

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

	public function getOrderData($by = 'ID', $val = false, $arg = array()){
		if(!$val) return false;

		$q = "SELECT
			o.*,
			CONCAT(u.szam_firstname,' ', u.szam_lastname) as userName,
			c.couponKey as couponName,
			c.action_rate as couponRate
		FROM ".TAGS::DB_TABLE_ORDERS." as o
		LEFT OUTER JOIN ".TAGS::DB_TABLE_USERS." as u ON u.ID = o.userID
		LEFT OUTER JOIN ".TAGS::DB_TABLE_COUPONS." as c ON c.ID = o.couponID
		WHERE o.".$by." = '".$val."'";

		extract($this->db->q($q, $arg));

		$price = 0;

		$data[giftcard] = $this->getGiftcardOnOrder($data[ID], $data[priceCode]);
		$data[userData] = $this->loadUserData($data[userID], array('onlyUserData' => true));
		$data[orderedItems] = $this->orderedItems($data[ID], array(
			'price_code' => $data[priceCode]
		));
		foreach ($data[orderedItems] as $p) {
			$price += $p[totalPrice];
		}
		if ($data['couponRate'] != '') {
		 $price -= ($price / 100 * $data[couponRate]);
		}

		if ($data['transportPrice'] != '') {
			$price += $data[transportPrice];
		}
		if ($data[giftcard]['total'] != 0) {
			$price -= $data[giftcard]['total'];
		}
		$data[total_price] = $price;
		return $data;
	}
	public function getOrders($arg = array()){
		return $this->getUserOrders(false, $arg);
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

	protected function orderedItems($orderID, $arg = array()){
		if($orderID == '') return false;

		$price_code = ($arg[price_code]) ? $arg[price_code] : Lang::getPriceCode();

		$q = "SELECT
			i.productID,
			i.variationID,
			i.pcs,
			p.afa,
			CONCAT(coll.name_".$this->lang.",' ',p.name_".$this->lang.") as termekNev,
			pv.price_".$price_code." as price,
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
			$d[priceCode] 	= $price_code;
			$d[url] 		= '/webshop/product/'.Helper::makeSafeUrl($d[termekNev],'_-'.$d[productID]);

			$d[priceTxt] 	= Helper::cashFormat($d[price]);
			$d[totalPrice] 	= $d[price] * $d[pcs];


			$dt[] = $d;
		}

		return $dt;
	}
	#/FELHASZNÁLÓK


	/* MEGRENDELÉSEK */
	public function getAllOrderStatus(){
		return $this->order_status;
	}
	public function getAllOrderPayStatus(){
		return $this->order_pay_status;
	}
	public function getAllOrderPayMethod(){
		return $this->order_paymethodes;
	}
	public function getAllCurrencies(){
		return $this->currencies;
	}
	public function editOrder($orderKey, $post){
		if($orderKey == '') return false;
		unset($post[editOrder]);

		if( $post[payu_state] == 'COMPLETE' ){
			$post[payu_paid_time] = NOW;
		}

		$this->db->update(
			TAGS::DB_TABLE_ORDERS,
			$post,
			"orderKey = '$orderKey'"
		);
	}

	public function delOrder($orderKey){

		// Megrendelés adatai
		$data = $this->getOrderData( 'orderKey', $orderKey );
		if( !$data ) throw new Exception( 'Ismeretlen eredetű megrendelés azonosító. Nem tudjuk törölni a megrendelést!' );
		$orderID = $data['ID'];

		// Megrendelt termékek eltávolítása
		$this->db->query( "DELETE FROM ".TAGS::DB_TABLE_ORDER_ITEMS." WHERE orderID = $orderID;");

		// Megrendelés törlése
		$this->db->query( "DELETE FROM ".TAGS::DB_TABLE_ORDERS." WHERE orderKey = '$orderKey';");
	}

	public function addNewCoupon($post){
		extract($post);

		if($couponKey == '') throw new Exception(__('Kupon kódját kötelező megadni!'));
		if($activeFrom == '') throw new Exception(__('Kezdő időpontot kötelező megadni!'));
		if($activeTo == '') throw new Exception(__('Lejárati időpontot kötelező megadni!'));
		if($action_rate == '') throw new Exception(__('A kedvezmény mértékét kötelező megadni!'));

		$c = $this->db->query("SELECT ID FROM ".TAGS::DB_TABLE_COUPONS." WHERE couponKey = '$couponKey'");

		if($c->rowCount > 0){
			throw new Exception(__('Ilyen kódú kupon már létezik!'));
		}

		$this->db->insert(
			TAGS::DB_TABLE_COUPONS,
			array('couponKey', 'activeFrom', 'activeTo', 'action_rate'),
			array(trim($couponKey), $activeFrom, $activeTo, $action_rate)
		);
	}

	public function editCoupon($id, $post){
		if($id == '') throw new Exception(__('Hibás kupon azonosító, vagy hiányzik. A kupon nem szerkeszthető!'));
		extract($post);

		if($couponKey == '') throw new Exception(__('Kupon kódját kötelező megadni!'));
		if($activeFrom == '') throw new Exception(__('Kezdő időpontot kötelező megadni!'));
		if($activeTo == '') throw new Exception(__('Lejárati időpontot kötelező megadni!'));
		if($action_rate == '') throw new Exception(__('A kedvezmény mértékét kötelező megadni!'));

		$this->db->update(
			TAGS::DB_TABLE_COUPONS,
			array(
				'couponKey' 	=> addslashes(trim($couponKey)),
				'activeFrom' 	=> $activeFrom,
				'activeTo' 		=> $activeTo,
				'action_rate' 	=> $action_rate
			),
			"ID = $id"
		);

		return __('Változásokat mentettük!');
	}

	public function getCouponData($id){
		if($id == '') return false;

		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_COUPONS." WHERE ID = $id";

		extract($this->db->q($q, $arg));

		return $data;
	}

	public function delCoupon($id){
		if($id == '') return false;
		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_COUPONS." WHERE ID = $id");
	}

	public function getAllCoupon(){
		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_COUPONS." ORDER BY IF((now() >= activeFrom and now() <= activeTo), 1, 0) DESC";

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		$bdata = array();

		foreach($data as $d){
			$d[usedNum] = $this->getUsedCouponNumbers($d[ID]);
			$bdata[] = $d;
		}

		$ret[data] = $bdata;

		return $ret;
	}

	/* Giftcard */
	public function addNewGiftcard($post){
		extract($post);

		if($code == '') throw new Exception(__('Ajándékkártya kódját kötelező megadni!'));
		if($expired == '') throw new Exception(__('Lejárati időpontot kötelező megadni!'));
		if($amount_huf == '') throw new Exception(__('A kártya értékét (HUF) kötelező megadni!'));

		$c = $this->db->query($qq="SELECT ID FROM ".TAGS::DB_TABLE_GIFTCARDS." WHERE code = '$code'");
		if($c->rowCount() > 0){
			throw new Exception(__('Ezzel a számsorral már létezik egy kártya!'));
		}

		$this->db->insert(
			TAGS::DB_TABLE_GIFTCARDS,
			array('code', 'expired', 'amount_huf', 'amount_eur', 'amount_usd'),
			array(trim($code), $expired, $amount_huf, $amount_eur, $amount_usd)
		);
	}

	public function editGiftcard($id, $post){
		if($id == '') throw new Exception(__('Hibás ajándékkártya azonosító, vagy hiányzik. Az ajándékkártya nem szerkeszthető!'));
		extract($post);

		if($code == '') throw new Exception(__('Ajándékkártya kódját kötelező megadni!'));
		if($expired == '') throw new Exception(__('Lejárati időpontot kötelező megadni!'));
		if($amount_huf == '') throw new Exception(__('A kártya értékét (HUF) kötelező megadni!'));

		$this->db->update(
			TAGS::DB_TABLE_GIFTCARDS,
			array(
				'code' 	=> addslashes(trim($code)),
				'expired' 	=> $expired,
				'amount_huf' 		=> $amount_huf,
				'amount_eur' 		=> $amount_eur,
				'amount_usd' 		=> $amount_usd
			),
			"ID = $id"
		);

		return __('Változásokat mentettük!');
	}

	public function getGiftcardData($id){
		if($id == '') return false;

		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_GIFTCARDS." WHERE ID = $id";

		extract($this->db->q($q, $arg));

		return $data;
	}

	public function delGiftcard($id){
		if($id == '') return false;
		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_GIFTCARDS." WHERE ID = $id");
	}

	public function getAllGiftcards(){
		$q 		= "SELECT
			c.*,
			o.orderKey as orderkey
		FROM ".TAGS::DB_TABLE_GIFTCARDS." as c
		LEFT OUTER JOIN giftcard_using as cu ON cu.code = c.code
		LEFT OUTER JOIN orders as o ON o.ID = cu.orderID
		ORDER BY c.when_used ASC, c.code DESC";

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		$bdata = array();

		foreach($data as $d){
			$bdata[] = $d;
		}

		$ret[data] = $bdata;

		return $ret;
	}
	/* E:Giftcard */

	protected function getUsedCouponNumbers($coupon_code){
		if($coupon_code == '') return 0;

		$q = $this->db->query("SELECT count(ID) FROM ".TAGS::DB_TABLE_ORDERS." WHERE couponID = $coupon_code");

		return $q->fetch(PDO::FETCH_COLUMN);
	}
	#/ MEGRENDELÉSEK

	/* ADMINOK */
	public function addNewAdmin($post){
	extract($post);

		if($user == '') throw new Exception(__('Kérjük, hogy adja meg az adminisztrátor azonosítóját!'));
		if($pw1 == '') throw new Exception(__('Kérjük, hogy adja meg az új jelszót!'));
		if($pw2 == '') throw new Exception(__('Kérjük, hogy adja meg az új jelszót újra!'));
		if($pw1 != $pw2) throw new Exception(__('A két megadott új jelszó nem egyezik!'));

		$this->db->insert(
			TAGS::DB_TABLE_ADMIN,
			array('user', 'pw', 'blocked', 'priority'),
			array($user, Hash::jelszo($pw2), $blocked, $priority)
		);

		return __('Új adminisztrátor sikeresen hozzáadva!');
	}

	public function getAllAdmins(){
		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_ADMIN." ORDER BY lastLoginDate ASC";

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		return $data;
	}

	public function getAllAdminPriority(){
		return $this->admin_priority_codes;
	}

	public function getAdminData($id){
		if($id == '') return false;

		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_ADMIN." WHERE ID = $id";

		extract($this->db->q($q, $arg));

		return $data;
	}

	public function changeAdminPassword($id, $post){
		if($pw1 == '') throw new Exception(__('Kérjük, hogy adja meg az új jelszót!'));
		if($pw2 == '') throw new Exception(__('Kérjük, hogy adja meg az új jelszót újra!'));
		if($pw1 != $pw2) throw new Exception(__('A két megadott új jelszó nem egyezik!'));

		$pass = Hash::jelszo($pw2);

		$this->db->update(
			TAGS::DB_TABLE_ADMIN,
			array(
				'pw' => $pass
			),
			"ID = $id"
		);

		return __('A jelszót sikeresen lecseréltük');
	}

	public function editAdmin($id, $post){
		extract($post);

		if($user == '') throw new Exception(__('Kérjük, hogy adja meg az adminisztrátor azonosítóját!'));

		$this->db->update(
			TAGS::DB_TABLE_ADMIN,
			array(
				'user' 		=> $user,
				'blocked' 	=> $blocked,
				'priority' 	=> $priority
			),
			"ID = $id"
		);

		return __('Változások sikeresen mentve lettek!');
	}
	public function delAdmin($id){
		if($id == '')throw new Exception(__('Admin ID hiányzik, vagy már nem létezik.'));
		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_ADMIN." WHERE ID = $id");
	}
	#/ ADMINOK

	/* SZTÁRJAIN */
	public function getAllOurStars(){
		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_OURSTARS." ORDER BY orderInt ASC";

		$arg[multi] = '1';
		extract($this->db->q($q, $arg));

		return $ret;
	}

	public function getOurStarData($id){
		if($id == '') return false;

		$q 		= "SELECT * FROM ".TAGS::DB_TABLE_OURSTARS." WHERE ID = $id";

		extract($this->db->q($q, $arg));

		return $data;
	}

	public function addNewStar($post){
		extract($post);

		if($name == '') throw new Exception(__('A sztár nevének megadása kötelező!'));

		$head = array();
		$data = array();

		$data[] = $name;
		$head[] = 'name';

		foreach( $this->settings['all_languages'] as $lang ):
			$head[] = 'comment_'.$lang['code'];
			$data[] = addslashes($comment[$lang['code']]);
			$head[] = 'content_'.$lang['code'];
			$data[] = addslashes($content[$lang['code']]);
		endforeach;
		$data[] = $orderInt;
		$head[] = 'orderInt';

		$this->db->insert(
			TAGS::DB_TABLE_OURSTARS,
			$head,
			$data
		);

		$id = $this->db->lastInsertId();

		// Háttérkép feltöltése
		if($_FILES[bgImg][name][0] != ''){
			$idir 	= 'images/ourstars';
			if(!file_exists($idir)){
				mkdir($idir,0777,true);
			}

			$img = Images::upload(array(
				'src' 			=> 'bgImg',
				'upDir' 		=> $idir,
				'noRoot' 		=> true,
				'maxFileSize' 	=> 1024,
				'noThumbImg' 	=> true,
				'noWaterMark' 	=> true,
				'fileName'		=> 'bg-'.$id
			));

			$this->db->update(
				TAGS::DB_TABLE_OURSTARS,
				array(
					'bg_img' => $img[file]
				),
				"ID = $id"
			);
		}

		// Háttérkép feltöltése
		if($_FILES[pImg][name][0] != ''){
			$idir 	= 'images/ourstars';
			if(!file_exists($idir)){
				mkdir($idir,0777,true);
			}

			$img = Images::upload(array(
				'src' 			=> 'pImg',
				'upDir' 		=> $idir,
				'noRoot' 		=> true,
				'maxFileSize' 	=> 1024,
				'noThumbImg' 	=> true,
				'noWaterMark' 	=> true,
				'fileName'		=> 'profil-'.$id
			));

			$this->db->update(
				TAGS::DB_TABLE_OURSTARS,
				array(
					'face_img' => $img[file]
				),
				"ID = $id"
			);
		}
	}

	public function editStar($id, $post){
		extract($post);

		$updates = array();

		if($id == '') throw new Exception(__('Kérjük, hogy adja meg a Sztár azonosítóját!'));

		$data = array(
			'name' 			=> $name,
			'orderInt' 		=> $orderInt
		);
		foreach( $this->settings['all_languages'] as $lang ):
			$data['comment_'.$lang['code']] = addslashes($comment[$lang['code']]);
			$data['content_'.$lang['code']] = addslashes($content[$lang['code']]);
		endforeach;

		// Háttérkép feltöltése
		if($_FILES[bgImg][name][0] != ''){
			$idir 	= 'images/ourstars';
			if(!file_exists($idir)){
				mkdir($idir,0777,true);
			}

			$img = Images::upload(array(
				'src' 			=> 'bgImg',
				'upDir' 		=> $idir,
				'noRoot' 		=> true,
				'maxFileSize' 	=> 1024,
				'noThumbImg' 	=> true,
				'noWaterMark' 	=> true,
				'fileName'		=> 'bg-'.$id
			));

			$data[bg_img]  = $img[file];
		}

		// Háttérkép feltöltése
		if($_FILES[pImg][name][0] != ''){
			$idir 	= 'images/ourstars';
			if(!file_exists($idir)){
				mkdir($idir,0777,true);
			}

			$img = Images::upload(array(
				'src' 			=> 'pImg',
				'upDir' 		=> $idir,
				'noRoot' 		=> true,
				'maxFileSize' 	=> 1024,
				'noThumbImg' 	=> true,
				'noWaterMark' 	=> true,
				'fileName'		=> 'profil-'.$id
			));

			$data[face_img]  = $img[file];
		}

		$this->db->update(
			TAGS::DB_TABLE_OURSTARS,
			$data,
			"ID = $id"
		);

		return __('Változások sikeresen mentve lettek!');
	}
	public function delStar($id){
		if($id == '')throw new Exception(__('Sztár ID hiányzik, vagy már nem létezik.'));
		$this->db->query("DELETE FROM ".TAGS::DB_TABLE_OURSTARS." WHERE ID = $id");
	}

	#/ SZTÁRJAINK
}
?>
