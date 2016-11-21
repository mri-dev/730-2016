<?
use PortalManager\Template;

class admin extends Controller{
		function __construct(){
			parent::__construct();
			parent::$pageTitle = 'Adminisztráció';

			$this->Admin 			= $this->model->open('Admin');
			$this->view->admin 		= $this->Admin->admin;
			$this->view->is_logged 	= $this->Admin->isLogged();
			$this->view->admin_priority = $this->Admin->priority;

			// Admin azonosítása
			if(Post::on('adminLogin')){
				try{
					$this->Admin->login($_POST);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if($this->gets[1] == 'exit'){
				$this->Admin->logout();
			}

			// Megrendelés adatok módosítása
			if(Post::on('editOrder')){
				try{
					$this->Admin->editOrder($this->view->gets[3], $_POST);
					$this->view->error_msg 	= Helper::makeAlertMsg('pSuccess', 'Változások mentve!');
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Megrendelés törlése
			if(Post::on('delOrder')){
				try{
					$this->Admin->delOrder( $this->view->gets[3] );
					Helper::reload(ADMROOT.'/orders');
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}


			//Megrendelések
			$this->view->orders = $this->Admin->getOrders();

			// Megrendelés adatlapja
			if($this->view->gets[2] == 'o'){
				// Megrendelés állapotok
				$this->view->orderStatus = $this->Admin->getAllOrderStatus();
				// Megrendelés fizetési módok
				$this->view->payMethodes = $this->Admin->getAllOrderPayMethod();
				// Megrendelés fizetési módok
				$this->view->payStatuses= $this->Admin->getAllOrderPayStatus();

				// Pénznemek
				$this->view->transportCurrencies = $this->Admin->getAllCurrencies();

				// Megrendelés adatok
				$this->view->order = $this->Admin->getOrderData('orderKey', $this->view->gets[3]);

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

		/* Katalógusok */
		public function books()
		{
			$this->view->templates = new Template( VIEW . '/templates/' );

			if(Post::on('addBook')){
				try{
					$this->Admin->addNewBook($_POST);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if(Post::on('saveBook')){
				try{
					$this->Admin->saveBook( $this->view->gets[3], $_POST );
					Helper::reload();
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if(Post::on('saveBookPage')){
				try{
					$this->Admin->saveBookPages( $_POST['saveBookPage'], $_POST );
					Helper::reload();
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if(Post::on('delCatalog')){
				try{
					$this->Admin->deleteBook( $this->view->gets[3] );
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			switch ( $this->view->gets[2] ) {
				case 'edit': case 'pages': case 'del':
					$books = $this->Admin->getAllBooks(array(
						'ID' => $this->view->gets[3]
					));
					$this->view->book = $books[data][0];

					$this->view->products = $this->Admin->loadAllProducts( array(
						'order' => 'c.name_hu ASC, t.name_hu ASC'
					));
				break;
			}


			$this->view->books = $this->Admin->getAllBooks();
		}
		/* END of Katalógusok */

		/* MENÜK */
		function menus(){
			//Fejrész menük
			$this->view->menu[top] = $this->Admin->loadAllMenu('top');
			//Lábrész menük
			$this->view->menu[bottom] = $this->Admin->loadAllMenu('bottom');

			// Fejrész menü létrehozása
			if(Post::on('addTopMenu')){
				try{
					$this->Admin->addNewMenu('top', $_POST);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			// Lábrész menü létrehozása
			if(Post::on('addBottomMenu')){
				try{
					$this->Admin->addNewMenu('bottom', $_POST);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Menü elem törlése
			if(Post::on('delMenu')){
				try{
					$this->Admin->delMenu($this->view->gets[3]);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Menü elem szerkesztése
			if(Post::on('editMenu')){
				try{
					$this->Admin->editMenu($this->view->gets[3], $_POST);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Menü adatok
			if($this->view->gets[2] == 'edit' || $this->view->gets[2] == 'del'){
				$this->view->menus = $this->Admin->getMenuData($this->view->gets[3]);
			}
		}
		/* FELHASZNÁLÓK */
		function users(){
			// Összes kollekció betöltése
			$arg = array();
			$arg[limit] = 25;
			$this->view->users = $this->Admin->loadAllUsers($arg);



			if($this->view->gets[2] == 'u'){
				// Műveletek végrehajtása
				if(Post::on('doAction')){
					// Tiltás/Engedélyezés
					if(Post::on('blocking')){
						$val = ($_POST[blocking] == '0') ? 1 : 0;
						$this->model->db->update(TAGS::DB_TABLE_USERS,array(
							'blocked' 	=> $val,
							'status' 	=> (($val == 1)?'Tiltva':'Aktív')
						),"ID = ".$this->view->gets[3]);
					}
					// Aktiválás/Inaktiválás
					if(Post::on('activing')){
						$val = ($_POST[activing] == '0') ? 1 : 0;
						$this->model->db->update(TAGS::DB_TABLE_USERS,array(
							'actived' 	=> $val,
							'status' 	=> (($val == 1)?'Aktív':'Nincs megerősítve')
						),"ID = ".$this->view->gets[3]);
					}
					Helper::reload();
				}
				// Felhasználó adatok
				$this->view->user = $this->Admin->loadUserData($this->view->gets[3]);
			}
		}
		/* KOLLEKCIÓK */
		function collections(){
			// Új kollekció létrehozása
			if(Post::on('addCollection')){
				try{
					$this->Admin->addNewCollection($_POST);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Kollekció szerkesztése
			if(Post::on('editCollection')){
				try{
					$this->Admin->editCollection($this->view->gets[3], $_POST);
					$this->view->error_msg 	= Helper::makeAlertMsg('pSuccess', __('A kollekció adatai mentve lettek!'));
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Kollekció törlése
			if(Post::on('delCollection')){
				try{
					$this->Admin->delCollection($this->view->gets[3]);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Kollekció adatok
			if($this->view->gets[2] == 'edit' || $this->view->gets[2] == 'del'){
				$this->view->collection = $this->Admin->getCollectionData($this->view->gets[3]);

				$this->view->cproducts 	= $this->Admin->getCollectionProducts( $this->view->collection[ID] );
			}

			// Összes kollekció betöltése
			$arg = array();
			$arg[limit] = 500;
			$this->view->collections = $this->Admin->loadAllCollections($arg);
		}
		/* OLDALAK */
		function pages(){
			// Új oldal létrehozása
			if(Post::on('addPage')){
				try{
					$this->Admin->addNewPage($_POST);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Oldal szerkesztése
			if(Post::on('editPage')){
				try{
					$this->Admin->editPage($this->view->gets[3], $_POST);
					$this->view->error_msg 	= Helper::makeAlertMsg('pSuccess', __('Az oldal adatai mentve lettek!'));
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Oldal törlése
			if(Post::on('delPage')){
				try{
					$this->Admin->delPage($this->view->gets[3]);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Oldal adatok
			if($this->view->gets[2] == 'edit' || $this->view->gets[2] == 'del'){
				$this->view->page = $this->Admin->getPageData($this->view->gets[3]);
			}

			// Összes oldal betöltése
			$arg = array();
			$arg[limit] = 500;
			$this->view->pages = $this->Admin->loadAllPages($arg);
		}
		/* TERMÉKEK */
		function products(){

			if(Post::on('filterList')){
				$filtered = false;

				if($_POST[productNumber] != ''){
					setcookie('filter_productNumber',$_POST[productNumber],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_productNumber','',time()-100,'/'.$this->view->gets[0]);
				}

				if($_POST[collection] != ''){
					setcookie('filter_collection',$_POST[collection],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_collection','',time()-100,'/'.$this->view->gets[0]);
				}

				if($_POST[name] != ''){
					setcookie('filter_name',$_POST[name],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_name','',time()-100,'/'.$this->view->gets[0]);
				}

				if($_POST[category] != ''){
					setcookie('filter_category',$_POST[category],time()+60*24,'/'.$this->view->gets[0]);
					$filtered = true;
				}else{
					setcookie('filter_category','',time()-100,'/'.$this->view->gets[0]);
				}

				if($filtered){
					setcookie('filtered','1',time()+60*24*7,'/'.$this->view->gets[0]);
				}else{
					setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
				}
				Helper::reload();
			}

			// Új termék létrehozása
			if(Post::on('addProduct')){
				try{
					$this->Admin->addNewProduct($_POST);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Termék szerkesztése
			if(Post::on('saveProduct')){
				try{
					$ins = $this->Admin->editProduct($this->view->gets[3], $_POST);
					$this->view->error_msg 	= Helper::makeAlertMsg('pSuccess', $ins);
					//Helper::reload();
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Termék törlés
			if(Post::on('delProduct')){
				try{
					$this->Admin->delProduct($this->view->gets[3]);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Kollekciók
			$this->view->collections 	= $this->Admin->loadAllCollections($arg);
			// Termék kategóriák
			$this->view->categories 	= $this->Admin->loadAllProductCategories($arg);

			// Termékek
			$arg 				= array();
			$arg[limit] 		= 25;
			$filters 			= Helper::getCookieFilter('filter',array('filtered'));
			$arg[filters] 		= $filters;
			$this->view->products = $this->Admin->loadAllProducts($arg);

			if($this->view->gets[2] == 'edit' || $this->view->gets[2] == 'del'){
				// Termék adatok
				$this->view->product = $this->Admin->getProductData($this->view->gets[3]);
			}

			if($this->view->gets[2] == 'clearfilters'){
				setcookie('filter_category','',time()-100,'/'.$this->view->gets[0]);
				setcookie('filter_collection','',time()-100,'/'.$this->view->gets[0]);
				setcookie('filter_name','',time()-100,'/'.$this->view->gets[0]);
				setcookie('filter_productNumber','',time()-100,'/'.$this->view->gets[0]);

				setcookie('filtered','',time()-100,'/'.$this->view->gets[0]);
				Helper::reload(ADMROOT.'/'.$this->view->gets[1]);
			}
		}
		/* Termék variációk */
		function product_variation(){
			if($this->view->gets[2] == 'edit'){
				// Termék variáció szerkesztés
				if(Post::on('editProductVariation')){
					try{
						$ins = $this->Admin->editProductVariation($this->view->gets[3], $_POST);
						$this->view->error_msg 	= Helper::makeAlertMsg('pSuccess', $ins);
					}catch(Exception $e){
						$this->view->err 		= true;
						$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}
			// Törlés
			if($this->view->gets[2] == 'del'){
				// Termék variáció törlése
				if(Post::on('delProductVariation')){
					try{
						$this->Admin->delProductVariation($this->view->gets[3]);
						Helper::reload( $_GET['return'] );
					}catch(Exception $e){
						$this->view->err 		= true;
						$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
					}
				}
			}

			// Termék adatok
			$this->view->product = $this->Admin->getProductVariationData($this->view->gets[3]);
		}

		/* KUPONOK */
		function coupons(){
			// Kupon létrehozása
			if(Post::on('addCoupon')){
				try{
					$this->Admin->addNewCoupon($_POST);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			// Kupon törlése
			if(Post::on('delCoupon')){
				try{
					$this->Admin->delCoupon($this->view->gets[3]);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Kupon szerkesztése
			if(Post::on('editCoupon')){
				try{
					$cp = $this->Admin->editCoupon($this->view->gets[3], $_POST);
					$this->view->error_msg 	= Helper::makeAlertMsg('pSuccess', $cp);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Kuponok
			$this->view->coupons = $this->Admin->getAllCoupon();

			if($this->view->gets[2] != 'edit' || $this->view->gets[2] != 'del' ){
				$this->view->coupon = $this->Admin->getCouponData($this->view->gets[3]);
			}
		}
		/* AJÁNDÉKKÁRTYÁK */
		function giftcards(){
			// Ajándékkártya létrehozása
			if(Post::on('addGiftcard')){
				try{
					$this->Admin->addNewGiftcard($_POST);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			// Ajándékkártya törlése
			if(Post::on('delGiftcard')){
				try{
					$this->Admin->delGiftcard($this->view->gets[3]);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Ajándékkártya szerkesztése
			if(Post::on('editGiftcard')){
				try{
					$cp = $this->Admin->editGiftcard($this->view->gets[3], $_POST);
					$this->view->error_msg 	= Helper::makeAlertMsg('pSuccess', $cp);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Ajándékkártyák
			$this->view->coupons = $this->Admin->getAllGiftcards();

			if($this->view->gets[2] != 'edit' || $this->view->gets[2] != 'del' ){
				$this->view->giftcard = $this->Admin->getGiftcardData($this->view->gets[3]);
			}
		}

		/* SZTÁRJAIN */
		function ourstars(){
			// Sztár hozzáadása
			if(Post::on('addStar')){
				try{
					$this->Admin->addNewStar($_POST);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}
			// Sztár törlése
			if(Post::on('delStar')){
				try{
					$this->Admin->delStar($this->view->gets[3]);
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Sztár szerkesztése
			if(Post::on('editStar')){
				try{
					$cp = $this->Admin->editStar($this->view->gets[3], $_POST);
					$this->view->error_msg 	= Helper::makeAlertMsg('pSuccess', $cp);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Sztárok
			$this->view->stars = $this->Admin->getAllOurStars();

			if($this->view->gets[2] != 'edit' || $this->view->gets[2] != 'del' ){
				$this->view->star = $this->Admin->getOurStarData($this->view->gets[3]);
			}
		}

		/* Beállítások */
		function settings(){
			if($this->view->admin_priority != 0){
				Helper::reload(ADMROOT);
			}

			if(Post::on('saveSettings')){
				try{
					$cp = $this->model->editSettings($_POST);
					Helper::reload();
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			if(Post::on('saveLanguage')){
				$used_lang = array();
				foreach ($_POST['lang'] as $lang => $value) {
					$used_lang[] = $lang;
				}
				$this->Admin->setUsedLanguage($used_lang);
				Helper::reload();
			}

			if(Post::on('saveCurrencyCountry')){
				$this->Admin->saveCurrencyCountries($_POST);
				Helper::reload();
			}


			// Adminok
			$this->view->admins = $this->Admin->getAllAdmins();

			// Admin prioritások
			$this->view->admin_priorities = $this->Admin->getAllAdminPriority();

			// Valuta területek
			$this->view->currency_countries = $this->Admin->getCurrencyCountries();

		}
		/* Adminisztrátorok */
		function account(){
			if($this->view->admin_priority != 0){
				Helper::reload(ADMROOT);
			}
			// Admin szerkesztés
			if(Post::on('editAccount')){
				try{
					$cp = $this->Admin->editAdmin($this->view->gets[3], $_POST);
					$this->view->error_msg 	= Helper::makeAlertMsg('pSuccess', $cp);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Admin hozzáadása
			if(Post::on('addAccount')){
				try{
					$cp = $this->Admin->addNewAdmin($_POST);
					$this->view->error_msg 	= Helper::makeAlertMsg('pSuccess', $cp);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Admin törlése
			if(Post::on('delAccount')){
				try{
					$cp = $this->Admin->delAdmin($this->view->gets[3]);
					Helper::reload(ADMROOT.'/settings');
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Admin jelszó csere
			if(Post::on('newAccountPassword')){
				try{
					$cp = $this->Admin->changeAdminPassword($this->view->gets[3], $_POST);
					$this->view->pw_msg 	= Helper::makeAlertMsg('pSuccess', $cp);
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->pw_msg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			// Admin prioritások
			$this->view->admin_priorities = $this->Admin->getAllAdminPriority();
			// Admin adatok
			$this->view->adminData = $this->Admin->getAdminData($this->view->gets[3]);
		}

		function images(){
			switch($this->view->gets[2]){
				case 'del':
					unlink(substr(base64_decode($this->view->gets[3]),1));
					Helper::reload(ADMROOT.'/'.__FUNCTION__);
				break;
				case 'upload':
					if($_FILES[image][name][0] != ''){
						$idir 	= 'images/uploads';
						if(!file_exists($idir)){
							mkdir($idir,0777,true);
						}
						try{
							$img = Images::upload(array(
								'src' 			=> 'image',
								'upDir' 		=> $idir,
								'noRoot' 		=> true,
								'maxFileSize' 	=> 2024,
								'noThumbImg' 	=> true,
								'noWaterMark' 	=> true
							));
							//print_r($img);
							Helper::reload(ADMROOT.'/'.__FUNCTION__);
						}catch(Exception $e){
							$this->view->error_msg = Helper::makeAlertMsg('pError',$e->getMessage());
							echo '<meta http-equiv="refresh" content="5; url='.ADMROOT.'/'.__FUNCTION__.'" />';
						}

					}else{
						$this->view->error_msg = Helper::makeAlertMsg('pError','<i class="fa fa-times"></i> Nincs kiválasztva kép. A feltöltés leállt! Átirányítás hamarosan...');

						echo '<meta http-equiv="refresh" content="5; url='.ADMROOT.'/'.__FUNCTION__.'" />';
					}
				break;
			}
		}

		function __destruct(){
			// RENDER OUTPUT
				parent::setThemeFolder('wires');
				parent::bodyHead('admin');			# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
