<?php

class Substances extends MY_controller {


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();

		// load journeys model
		$this->load->model(array('journeys_model', 'drugs_model', 'alcohol_model'));

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

		// get drugs information
		$drugs_info = $this->drugs_model->get_journey_drugs_info($journey['j_id']);

		// get alcohol information
		$alcohol_info = $this->alcohol_model->get_alcohol_info($j_id);

		// if form has been submitted
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('jal_avg_daily_units', '', 'integer|less_than[201]')
								  ->set_rules('jal_last_28_drinking_days', '', 'integer|less_than[29]')
								  ->set_rules('jal_age_started_drinking', '', 'integer')
								  ->set_rules('ja_substance_1_age', '', 'integer')
								  ->set_rules('jd_hep_c_test_date', '', 'parse_date');

			// if form validates
			if($this->form_validation->run())
			{
				// set alcohol info to db
				$this->alcohol_model->set_alcohol_info($j_id, $alcohol_info);

				// save drugs information
				$this->drugs_model->set_journey_drugs_info($j_id, $drugs_info);

				// set last datetime update
				$this->journeys_model->set_last_update($j_id, $this->session->userdata('a_id'));

				// load ndtms library
				$this->load->library('ndtms');

				// check to see if journey is NDTMS valid
				$this->ndtms->check_is_valid($j_id);

				// set log
				$this->log_model->set('Substances information updated for journey #' . $j_id);

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// redirect to current page
				redirect(current_url());
			}
		}

		// load datasets
		$this->load->config('datasets');

		$this->data['drugs_info'] = &$drugs_info;
		$this->data['alcohol_info'] = &$alcohol_info;

		$this->data['journey'] =& $journey;

		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Substances'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Substances' => ''));
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/autosave', 'views/admin/journeys/substances'));
		$this->layout->set_view('/admin/journeys/substances/index');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}

}