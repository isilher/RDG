<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

	public $data = array();
	
	/*****
	 * MY_Controller constructor
	 */
	public function __construct() {
		parent::__construct();

		// Set timezone
		date_default_timezone_set('Europe/Amsterdam');
		
		// no caching
		$this->output->set_header('Pragma: no-cache');
		$this->output->set_header('Cache-Control: no-cache, must-revalidate');
		$this->output->set_header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		
		// profiler enabled in development
		if(ENVIRONMENT == 'development')
		{
			$this->output->enable_profiler(TRUE);
		}
		
		// Set logged in status
		if($this->ion_auth->logged_in())
		{
			// Get the currently logged in user
			$this->data['user'] = $this->ion_auth->user()->row();
		}
		else
		{
			// User is not logged in
			$this->data['user'] = FALSE;
		}
		
		
		// load bootstrap
 		$this->carabiner->css('bootstrap.css');
 		//$this->carabiner->css('bootstrap-responsive.css');
 		$this->carabiner->js('bootstrap.js');
	}
}