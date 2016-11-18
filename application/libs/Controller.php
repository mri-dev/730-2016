<?	class Controller{
    private $hidePatern 	= true;
    private $theme_wire 	= '';
    private $theme_folder 	= '';
    public static $pageTitle;
    public $fnTemp = array();

    function __construct($arg = array()){
        Session::init();
        Helper::setMashineID();
        $this->gets 		= Helper::GET();
		
        // CORE
        $this->model 		= new Model();
        $this->view 		= new View();
        //////////////////////////////////////////////////////
        $this->gets 		= Helper::GET();
        $this->view->gets 	= $this->gets;
		$this->User 		= $this->model->open('Users');
			$this->view->user = $this->User->get();
		$this->portal      	= $this->model->open('Portal');
			$this->view->settings = $this->model->getSettings();
		
			// MENÜK
				// FELSŐ - Fejrész
				$this->view->topMenu = $this->portal->getMenus('top');
				// FELSŐ - Fejrész
				$this->view->bottomMenu = $this->portal->getMenus('bottom');

            // Collection items
                $this->view->collection_items = $this->portal->getAllCollection();
            // Kategóriák
                $this->view->all_category = $this->portal->getAllCategory();
					
        if(!$arg[hidePatern]){ $this->hidePatern = false; }
		
		
		if( $_COOKIE[Lang::COUNTRY_CURRENCY_COOKIE] == '' ) {
			setcookie( Lang::COUNTRY_CURRENCY_COOKIE, $this->getCurrencyCodeByCountry($_COOKIE['geo_country']), time() + 3600 * 24 * 30, '/' );
			if( isset($_COOKIE['geo_needrefreshpage']) ) {
				setcookie( 'geo_needrefreshpage', null, time() - 3600, '/' );
				Helper::reload();
			}
		}
    }

    function bodyHead($key = ''){
        $mode 		= false;
        $subfolder 	= '';

        $this->theme_wire 	= ($key != '') ? $key : '';
		
        if($this->getThemeFolder() != ''){
            $mode 		= true;
            $subfolder 	= $this->getThemeFolder().'/';		
        }
		
        # Oldal címe
        if(self::$pageTitle != null){
            $this->view->title = self::$pageTitle . ' | ' . TITLE;
        }
		
        # Render HEADER
        if(!$this->hidePatern){
            $this->view->render($subfolder.$this->theme_wire.'header',$mode);
        }

        # Aloldal átadása a VIEW-nek
        $this->view->called = $this->fnTemp;
    }

    function __destruct(){
        $mode 		= false;
        $subfolder 	= '';
		
        if($this->getThemeFolder() != ''){
            $mode 		= true;
            $subfolder 	= $this->getThemeFolder().'/';
        }

        if(!$this->hidePatern){
            # Render FOOTER
            $this->view->render($subfolder.$this->theme_wire.'footer',$mode);
        }
		
    }
	
	private function getCurrencyCodeByCountry( $country = 'Hungary' ) {
		$country_currency = $this->model->db->query("SELECT currency_code FROM ".TAGS::DB_TABLE_CURRENCY_COUNTRIES." WHERE country = '$country';")->fetchColumn();		
		return strtoupper($country_currency);
	}

    function setTitle($title){
        $this->view->title = $title;
    }

    function valtozok($key){
        $d = $this->model->db->query("SELECT svalue FROM settings WHERE skey = '$key'");
        $dt = $d->fetch(PDO::FETCH_ASSOC);

        return $dt[svalue];
    }

    protected function setThemeFolder($folder = ''){
        $this->theme_folder = $folder;
    }

    protected function getThemeFolder(){
        return $this->theme_folder;
    }
}

?>