<?
use PortalManager\Giftcard;

class giftcards extends Controller
{
		function __construct(){
			parent::__construct();
			$welcome = (Lang::getLang() == 'hu') ? WELCOME_MSG_HU : WELCOME_MSG_EN;
			parent::$pageTitle = ($welcome) ? $welcome : DEFALT_WELCOME_MSG ;

			$shop = $this->model->open('Shop');

			$gc = new Giftcard(array(
				'db' => $this->model->db,
				'shop' => $shop
			));
			$gcl = $gc->getAddedCodes();

			if (Post::on('activateGiftcard'))
			{
				try {
					$add = $gc->activate($_POST['code'], $_POST['seccode']);
					Helper::reload('/giftcards/?cardAdded='.$_POST['code']);
				} catch (Exception $e) {
					$this->view->err 		= true;
					$this->view->rmsg 	= Helper::makeAlertMsg('pError', $e->getMessage());
				}
			}

			$this->view->giftcards = $gc;
			$this->view->giftcards_list = $gcl;

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
