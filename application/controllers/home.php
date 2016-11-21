<? class home extends Controller{
		function __construct(){	
			parent::__construct();
			$welcome = (Lang::getLang() == 'hu') ? WELCOME_MSG_HU : WELCOME_MSG_EN;
			parent::$pageTitle = ($welcome) ? $welcome : DEFALT_WELCOME_MSG ;
			
			$description	= (Lang::getLang() == 'hu') ? DESCRIPTION_HU : DESCRIPTION_EN;
			$keywords		= (Lang::getLang() == 'hu') ? KEYWORDS_HU : KEYWORDS_EN;		
			
			// Kollekció adatok
			$arg = array();
			
			$this->view->collection = $this->portal->loadCollactions($arg);
			
			// SEO Információk
			$SEO = null;
			// Site info
			$SEO .= $this->view->addMeta('description',$description);
			$SEO .= $this->view->addMeta('keywords',$keywords);
			$SEO .= $this->view->addMeta('revisit-after','3 days');
			
			// FB info
			$SEO .= $this->view->addOG('type','website');
			$SEO .= $this->view->addOG('url',DOMAIN);
			$SEO .= $this->view->addOG('image',DOMAIN.substr(IMG,1).'noimg.jpg');
			$SEO .= $this->view->addOG('site_name',TITLE);
			
			$this->view->SEOSERVICE = $SEO;
		}
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>