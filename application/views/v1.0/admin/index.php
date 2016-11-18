<? 
	if(!$this->admin)
		$this->render('/admin/auth', true);
	else
		$this->render('/admin/control', true);
?>