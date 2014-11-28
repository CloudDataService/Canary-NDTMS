<?php

class Risks extends MY_controller {


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();

		// load journeys model
		$this->load->model(array('journeys_model', 'risks_model'));

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

		$risk_summaries = $this->risks_model->get_risk_summaries($journey['j_c_id']);

		$clients_risks = $this->risks_model->get_clients_risks($journey['j_c_id']);

		if(@$_GET['rt_id'])
		{
			$risk_type = $this->risks_model->get_clients_risk($_GET['rt_id'], $journey['j_c_id']);

			if(@$_GET['delete'])
			{
				$this->risks_model->delete_clients_risk($_GET['rt_id'], $journey['j_c_id']);

				// set log
				$this->log_model->set('Risk type deleted for journey #' . $j_id);

				redirect(current_url());
			}
		}

		if(@$_POST['risk_type_form'])
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('rt_id', '', 'required')
								  ->set_rules('impact_score', '', 'required|integer')
								  ->set_rules('likelihood_score', '', 'required|integer')
								  ->set_rules('risk_to_whom', '', 'strip_tags')
								  ->set_rules('protective_factors', '', 'strip_tags');

			// if form validates
			if($this->form_validation->run())
			{
				// set notes and return note id
				$jn_id = $this->risks_model->set_risk_type($journey['j_c_id'], $risk_type);

				$log = 'Risk type ' . ($risk_type ? 'updated' : 'added') . ' for journey #' . $j_id;

				// set log
				$this->log_model->set($log);

				// redirect to current url
				redirect(current_url());
			}
		}
		elseif(@$_POST['risk_summaries_form'])
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('crs_physical_risks', '', 'strip_tags')
								  ->set_rules('crs_psychological_risks', '', 'strip_tags')
								  ->set_rules('crs_social_risks', '', 'strip_tags')
								  ->set_rules('crs_violence/aggression_risks', '', 'strip_tags')
								  ->set_rules('protective_factors', '', 'strip_tags');

			// if form validates
			if($this->form_validation->run())
			{
				// set notes and return note id
				$jn_id = $this->risks_model->set_risk_summaries($journey['j_c_id'], $risk_summaries);

				// if client has been flagged as risk
				$this->risks_model->set_is_risk($journey);

				// set log
				$this->log_model->set('Risk summary information updated for journey #' . $j_id);

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// redirect to current url
				redirect(current_url());
			}
		}

		// load datasets
		$this->load->config('datasets');

		$this->data['risk_summaries'] =& $risk_summaries;

		$this->data['clients_risks'] =& $clients_risks;

		$this->data['risk_type'] =& $risk_type;

		$this->data['risk_types'] = $this->risks_model->get_risk_types($journey['j_c_id']);

		$this->data['journey'] =& $journey;

		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Risks'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Risks' => ''));
		$this->layout->set_css('datepicker');
		$this->layout->set_js(array('plugins/jquery.validate', 'plugins/jquery.datepicker', 'views/admin/journeys/autosave', 'views/admin/journeys/risks'));
		$this->layout->set_view('/admin/journeys/risks/index');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}
}