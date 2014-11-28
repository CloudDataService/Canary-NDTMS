<?php

class Logout extends My_controller
{

	public function __construct()
	{
		parent::__construct();
	}

	function index()
	{
		$this->session->sess_destroy();

		if(@$_GET['timeout'])
		{
			redirect('/?timeout=1');
		}

		else
		{
			redirect('/?logged_out=1');
		}
	}

}