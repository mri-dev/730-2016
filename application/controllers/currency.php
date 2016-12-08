<? class currency extends Controller {
		function __construct(){
			parent::__construct();

			Lang::setCurrency($this->view->gets[1]);
			Helper::reload( $_SERVER['HTTP_REFERER'] );
		}

		function __destruct(){
			// RENDER OUTPUT
				parent::bodyHead();					# HEADER
				$this->view->render(__CLASS__);		# CONTENT
				parent::__destruct();				# FOOTER
		}
	}

?>
