<?php

class Home extends MY_Controller {

	function __construct() 
	{
		parent::__construct();
	
	}
	
	function index() {
		
		// Title, content, template
		$this->data['title'] = 'home';
		$this->data['content'] = 'home/index.php';	
		$this->load->view('template', $this->data);
	}
}

/* End of file home.php */
/* Location: ./system/application/controllers/home.php */