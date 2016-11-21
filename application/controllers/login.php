<?	class login extends Controller{
		function __construct(){
			parent::__construct();
			
			parent::$pageTitle = __('Bejelentkezés');
			
			if($this->view->user){
				Helper::reload('/user');
			}
			
			// Bejelentkezés
			if(Post::on('loginUser')){
				try{
					$this->User->login($_POST);
					Helper::reload();
				}catch(Exception $e){
					$this->view->err 		= true;
					$this->view->error_msg 	= Helper::makeAlertMsg('pError', $e->getMessage()); 
				}
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
		
		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}
?>