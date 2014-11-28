<?php

class Home extends MY_controller {


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();
		$this->auth->check_password_expiry();
		$this->layout->set_title('Dashboard');
		$this->layout->set_breadcrumb('Dashboard', '/admin/dashboard');
	}




	public function index()
	{
		// load models
		$this->load->model('journeys_model');
		$this->load->model('messages_model');

		$this->data['messages'] = $this->messages_model->get_messages($start = 0, $limit = 10, array('m_to_admin' => $this->session->userdata('a_id')));
		$all_journeys_limit = 10;

		if ($this->session->userdata('rc_id'))
		{
			// Get newly-assigned journeys needing assessment
			$this->journeys_model->set_filter(array(
				'j_status' => 1,
				'j_rc_id' => $this->session->userdata('rc_id'),
				'without_assessment' => 1,	// get journeys with no previous CSOP or TOP dates
			));

			$this->journeys_model->order_by('j_date_of_referral', 'desc');
			$this->journeys_model->limit(10);
			$this->data['newly_assigned'] = $this->journeys_model->get_using_filter();

			$all_journeys_limit = empty($this->data['messages']) ? 10 : 5;
		}

		$this->data['journeys'] = $this->journeys_model->get_journeys(0, $all_journeys_limit);

		$this->layout->set_external_js('https://maps.google.com/maps/api/js?sensor=false');
		$this->layout->set_js(array('plugins/markerclusterer_compiled', 'views/admin/home/index'));
		$this->layout->set_view('/admin/home/index');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	public function getJson()
	{
		// load postcode model
		$this->load->model('postcode_model');

		// get coords for all clients in system
		$client_coords = $this->postcode_model->get_clients();

		// json decode coords for google maps api
		echo $client_coords;
	}


}
