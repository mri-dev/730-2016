<? 
use PortalManager\Template;

class ajax extends Controller{
		function __construct(){	
			parent::__construct();
			
			$this->shop = $this->model->open('Shop');
		}
		
		function post(){
			extract($_POST);
			$ret = array(
				'success' => 0,
				'msg' => false
			);
			switch($type){
				
				case 'cart':
					switch($mode){
						case 'clear':
							$err = false;
							
							try{
								$this->shop->clearCart(Helper::getMachineID());
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret); 
							}
							
							if(!$err)
							$this->setSuccess(__('A kosár kiürült!'),$ret);
							
							echo json_encode($ret);
							return;
						break;
						case 'add':
							$err = false;
							
							if(!$err && $t == '') $err = $this->escape(__('Hibás termék azonosító, próbálja meg később!'),$ret);
							if(!$err && $m == '') $err = $this->escape(__('Kérjük adja meg hogy hány terméket szeretne a kosárba helyezni!'),$ret); 
							
							try{
								$this->shop->addToCart(Helper::getMachineID(), $t, $m);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret); 
							}
							
							if(!$err)
							$this->setSuccess(__('A terméket sikeresen a kosárba helyezte!'),$ret);
							
							echo json_encode($ret);
							return;
						break;
						case 'remove':
							$err = false;
							if(!$err && $id == '') $err = $this->escape(__('Hibás termék azonosító, próbálja meg később!'),$ret);
							
							try{
								$this->shop->removeFromCart(Helper::getMachineID(), $id);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret); 
							}
						
							if(!$err)
							$this->setSuccess(__('A terméket sikeresen eltávolította a kosárból!'),$ret);
							
							echo json_encode($ret);
							return;
						break;
						case 'addItem':
							$err = false;
							if(!$err && $id == '') $err = $this->escape(__('Hibás termék azonosító, próbálja meg később!'),$ret);
							if(!$err && $vid == '') $err = $this->escape(__('Hibás termék variáció azonosító, próbálja meg később!'),$ret);
							
							try{
								$this->shop->addItemToCart(Helper::getMachineID(), $id, $vid);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret); 
							}
						
							if(!$err)
							$this->setSuccess(__('Sikeresen megnövelte a termék mennyiségét a kosárban!'),$ret);
							
							echo json_encode($ret);
							return;
						break;
						case 'removeItem':
							$err = false;
							if(!$err && $id == '') $err = $this->escape(__('Hibás termék azonosító, próbálja meg később!'),$ret);
							
							if(!$err && $vid == '') $err = $this->escape(__('Hibás termék variáció azonosító, próbálja meg később!'),$ret);
							
							try{
								$this->shop->removeItemFromCart(Helper::getMachineID(), $id, $vid);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret); 
							}
						
							if(!$err)
							$this->setSuccess(__('Sikeresen csökkentette a termék mennyiségét a kosárban!'),$ret);
							
							echo json_encode($ret);
							return;
						break;
					}
				break;
				case 'user':
					switch($mode){
						case 'add':
							$err = false;
							try{
								$re = $this->User->add($_POST);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret); 
								$ret[errorCode] = $e->getCode();
							}
							
							if(!$err)
							$this->setSuccess('Regisztráció sikeres! Kellemes vásárlást kívánunk!',$ret);
													
							echo json_encode($ret);
							return;
						break;
						case 'login':
							$err = false;
							try{
								$re = $this->User->login($_POST[data]);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret); 
								$ret[errorCode] = $e->getCode();
							}
							
							if(!$err)
							$this->setSuccess('Sikeresen bejelentkezett!',$ret);
													
							echo json_encode($ret);
							return;
						break;
						case 'resetPassword':
							$err = false;
							try{
								$re = $this->User->resetPassword($_POST[data]);
							}catch(Exception $e){
								$err = $this->escape($e->getMessage(),$ret); 
								$ret[errorCode] = $e->getCode();
							}
							
							if(!$err)
							$this->setSuccess('Új jelszó sikeresen generálva!',$ret);
													
							echo json_encode($ret);
							return;
						break;
					}
				break;
				case 'switcher':
					switch($mode){
						case 'menuSorter':	
							$sortList 	= rtrim($sortList,', ');
							$list 		= explode(",",$sortList);
							
							$i = 0;
							foreach($list as $l){
								$this->model->db->update(
									"menus",
									array(
										'priority' => $i
									),
									"ID = $l"
								);
								$i++;
							}
							
						break;						
					}
				break;
			}
			echo json_encode($ret);
		}
		
		private function setSuccess($msg, &$ret){
			$ret[msg] 		= $msg;
			$ret[success] 	= 1;
			return true;
		}
		private function escape($msg, &$ret){
			$ret[msg] 		= $msg;
			$ret[success] 	= 0;
			return true;
		}
		
		function get(){
			extract($_POST);
			$this->Admin = $this->model->open('Admin');
			
			switch($type){
				case 'template':
					parse_str($arg, $arg_export); 
					
					$arg_export['hash'] = hash( 'crc32', microtime() );
					$arg_export['settings'] = $this->view->settings;

					switch ( $arg_export['get'] ) {
						case 'products':
							$arg_export['products'] = $this->Admin->loadAllProducts( array(
								'order' => 'c.name_hu ASC, t.name_hu ASC'
							));
						break;
					}

					$temp = new Template( VIEW . 'templates/' );	
					echo $temp->get( $key, $arg_export );
				break;
				case 'cartInfo':
					$mid = Helper::getMachineID();
					echo json_encode($this->shop->cartInfo($mid));
				break;
				case 'admin_menu_loadcontentByType':
					$this->view->key 	= $key;
					$this->view->by 	= $by;
					$this->view->defid 	= $defid;
					$this->view->url 	= $url;
					$this->view->items	= array();
					
					switch($key){
						// Elérhető oldalak
						case 'page':
							$pages = $this->Admin->loadAllPages();
							foreach($pages[data] as $d){
								$in = array();
									$in[ID] 	= $d[ID];
									$in[name] 	= $d[title_hu];
								
								$this->view->items[] = $in;
							}
						break;
						// Elérhető termékek
						case 'product':
							$pages = $this->Admin->loadAllProducts();
							foreach($pages[data] as $d){
								$in = array();
									$in[ID] 	= $d[ID];
									$in[name] 	= $d[collection_name_hu].' '.$d[name_hu];
								
								$this->view->items[] = $in;
							}
						break;
						// Elérhető kollekció termékek
						case 'collection':
						$pages = $this->Admin->loadAllCollections();
							foreach($pages[data] as $d){
								$in = array();
									$in[ID] 	= $d[ID];
									$in[name] 	= $d[name_hu];
								
								$this->view->items[] = $in;
							}
						break;
						// Elérhető terrmék kategóriák
						case 'category':
						$pages = $this->Admin->loadAllProductCategories();
							foreach($pages[data] as $d){
								$in = array();
									$in[ID] 	= $d[ID];
									$in[name] 	= $d[name_hu];
								
								$this->view->items[] = $in;
							}
						break;
					}
				break;
			}
		
			$this->view->render(__CLASS__.'/'.__FUNCTION__.'/'.$type, true);
		}
		
		function __destruct(){}
	}

?>