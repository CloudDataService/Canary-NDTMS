<?php

class Home extends MY_controller {

	public function __construct()
	{
		parent::__construct();

		// check to se4 if already logged in
		//$this->auth->check_logged_in();

		// set default template
		$this->layout->set_template('home');

		// load authentication model
		$this->load->model('auth_model');

		// clear css and set home css
		$this->layout->clear_css()->set_css('home');

		// clear js
		$this->layout->clear_js();
	}


	public function index()
	{
		$this->auth->check_logged_in();

		// check to see if user is banned
		if($banned = $this->auth_model->check_for_ban(getenv('REMOTE_ADDR')))
		{
			$this->data['banned'] = $banned;
		}
		elseif($_POST)
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('email', '', 'required|valid_email|callback__login');
			$this->form_validation->set_rules('password', '', 'required');

			if( ! $this->form_validation->run())
			{
				$this->auth_model->set_failed_login(getenv('REMOTE_ADDR'));

				$this->data['failed_login'] = true;
			}
		}

		$this->layout->set_view('/default/home');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}

	public function verify()
	{
		// if admin id or token don't exist
		if( ! @$_GET['a_id'] || ! @$_GET['token'])
			show_404();

		// load admins model
		$this->load->model('admins_model');

		// if admin id does not map to an active admin in the db
		if( ! $admin = $this->admins_model->get_admin(@$_GET['a_id']))
			show_404();

		// create token from admin's details
		$token = $this->auth->hash_hmac($admin['a_id'] . $admin['a_email'] . $admin['a_id']);

		// if token in url matches token just created
		if($this->auth->compare_strings(@$_GET['token'] ?: "", $token))
		{
			// verify admin's account
			$this->admins_model->verify_admin($admin['a_id']);

			// redirect to login page
			redirect('/?verified=1');
		}
		else
		{
			show_404();
		}
	}

	public function _login()
	{
		if ($admin = $this->auth_model->login_admin())
		{
			// clear failed login attempts
			$this->auth_model->clear_failed_logins(getenv('REMOTE_ADDR'));

			// log in
			$admin['logged_in'] = true;
			$this->session->set_userdata($admin);

			//permissions
			$admin['can_edit_client'] = 1;
			$admin['can_read_client'] = 1;
			$admin['can_edit_family'] = 1;
			$admin['can_read_family'] = 1;
			if($admin['a_type'] == 'Service')
			{
				$admin['can_edit_family'] = 0;
			}
			else if($admin['a_type'] == 'Family')
			{
				$admin['can_edit_client'] = 0;
				$admin['can_read_client'] = 0;
			}

			$this->session->set_userdata($admin);

			$this->load->model('terms_model');
			$terms_and_conditions = $this->terms_model->get_terms();

			$agreed = (int)strtotime($admin['a_datetime_tc_agree']);

			/* check to see if admin has agreed to the most recent terms and conditions */
            if ($terms_and_conditions['on'] == 1 && $agreed < $terms_and_conditions['datetime_set'])
			{
				/* if they have not, redirect to terms and conditions page */
				redirect('/home/terms-and-conditions?new=' . ($agreed == 0 ? 1 : 0));
				die('done');
			}
			else
			{
				//redirect to right place
				if($request_uri = $this->session->userdata('admin_request_uri'))
					$this->session->unset_userdata('admin_request_uri');

				redirect(($request_uri ? $request_uri : '/admin'));
				die();
			}
		}

		return false;
	}

	public function terms_and_conditions()
	{
		$this->load->model('terms_model');
		$this->data['terms_and_conditions'] = $this->terms_model->get_terms();

		if(isset($_GET['agree']) && @$_GET['token'] == $this->session->flashdata('token'))
		{
			if($_GET['agree'] == 1)
			{
				$this->terms_model->set_datetime_tc_agree($this->session->userdata('a_id'));

				$this->session->set_userdata('logged_in', TRUE);

				/* set datetime_last_login to NOW() */
				$this->auth_model->login_admin($this->session->userdata('a_id'));
				redirect('/admin');

			}
			elseif($_GET['agree'] == 0)
			{
				$this->session->sess_destroy();
				redirect('/?terms_and_conditions=1');
			}

		}

		/* generate token */
		$token = $this->auth->hash_hmac($this->auth->secure_random(256));

		/* set token */
		$this->session->set_flashdata('token', $token);

		$this->data['token'] = $token;

		$this->layout->set_title('Terms and Conditions');
		$this->layout->set_view('/default/terms_and_conditions');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}
}
