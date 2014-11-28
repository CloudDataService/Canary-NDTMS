<?php

class Download extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
				
		// check the admin is logged in
		$this->auth->check_admin_logged_in();
	}
	
	public function index()
	{			
		// if path is not set, 404
		if( ! isset($_GET['path']))
			show_404();
			
		// load download library
		$this->load->library('download');
		
		// explode path to get directory and filename
		$path = explode('/', $_GET['path']);
		
		$this->download->get($path[1], APPPATH . '../storage/' . $path[0] . '/');
	}

}