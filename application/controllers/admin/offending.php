<?php

class Offending extends MY_controller {


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();

		// load journeys model
		$this->load->model(array('journeys_model', 'offending_model'));

		// set default js for journeys section
		$this->layout->set_js(array('views/admin/journeys/default'));

		$this->layout->set_title('Journeys');
		$this->layout->set_breadcrumb('Journeys', '/admin/journeys');
	}

	public function index($j_id)
	{
		// if no journey can be found
		if( ! $journey = $this->journeys_model->get_basic_journey_info($j_id))
			show_404();

		// get offending info
		$offending_info = $this->offending_model->get_journey_offending_info($journey['j_id']);

		// if form submitted
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('jo_shop_theft', '', 'integer')
								  ->set_rules('jo_drug_selling', '', 'integer')
								  ->set_rules('jo_other_theft', '', 'integer')
								  ->set_rules('jo_assault_violence', '', 'integer')
								  ->set_rules('jo_notes', '', 'stip_tags');

			// if form validates
			if($this->form_validation->run())
			{
				// save offending information
				$this->offending_model->set_journey_offending_info($journey['j_id'], $offending_info);

				// set log
				$this->log_model->set('Offending information set for journey #' . $j_id);

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// redirect to current page
				redirect(current_url());
			}
		}

		// load datasets
		$this->load->config('datasets');

		$this->data['offending_info'] =& $offending_info;
		$this->data['journey'] =& $journey;

		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Offending'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Offending' => ''));
		$this->layout->set_css('datepicker');
		$this->layout->set_js(array('plugins/jquery.validate', 'plugins/jquery.datepicker', 'views/admin/journeys/autosave', 'views/admin/journeys/offending'));
		$this->layout->set_view('/admin/journeys/offending');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}

}