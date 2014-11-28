<?php

class Assessments extends MY_controller {


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();

        //get permissions, used by the journeys_nav view
        $this->data['permissions'] = $this->session->userdata('permissions');

		$this->load->model(array('journeys_model', 'assessments_model'));
		$this->load->helper('journey');

		// set default js for journeys section
		$this->layout->set_js(array('views/admin/journeys/default'));

		$this->layout->set_title('Journeys');
		$this->layout->set_breadcrumb('Journeys', '/admin/journeys');
	}




	public function index($j_id)
	{
		// if no journey can be found
		if ( ! $journey = $this->journeys_model->get_basic_journey_info($j_id))
			show_404();

		// if attempting to delete an assessment
		if (@$_GET['delete'])
		{
			// delete assessment
			$this->assessments_model->delete_assessment($_GET['delete'], $j_id);
			redirect(current_url());
		}

		// Check validity of CSOP/TOP assessment window dates
		$window_csop = journey_ass_window($journey['ji_csop_last']);
		$window_top = journey_ass_window($journey['ji_top_last']);

		// get ALL available ACLs (global + custom journey)
		$acls = $this->assessments_model->get_all_acl($j_id);
		$this->data['acls'] = $acls;

		// Dropdown of all ACLs to choose from when creating
		foreach ($acls as $acl)
		{
			// Do not add a CSOP or TOP list if they cannot be taken at this time (assessment date window)
			if ($acl['acl_type'] == 'csop' && $window_csop['valid'] == FALSE) continue;
			if ($acl['acl_type'] == 'top' && $window_top['valid'] == FALSE) continue;

			$this->data['acls_dropdown'][$acl['acl_id']] = $acl['acl_name'];
		}

		// get custom journey assessment criteria
		$this->data['acl'] = $this->assessments_model->get_journey_acl($journey['j_id']);

		// get all assessments for this journey
		$this->data['assessments'] = $this->assessments_model->get_assessments($journey['j_id']);

		// load chart model
		$this->load->model('charts_model');

		$this->data['journey'] =& $journey;
		$this->data['window_csop'] = $window_csop;
		$this->data['window_top'] = $window_top;

		// TEMP: set dates for current journeys.
		$this->journeys_model->set_journey_ass_dates($j_id);

		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Outcomes'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Assessments' => ''));
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/assessments'));
		$this->layout->set_view('/admin/journeys/assessments/index');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




	public function set_criteria($j_id)
	{
		$acl = $this->assessments_model->get_journey_acl($j_id);
		$journey = $this->journeys_model->get_basic_journey_info($j_id);

		// if attemping to set an assessment criteria
		if ($this->input->post())
		{
			$acl_criteria = $this->input->post('acl_criteria');

			$criteria = array();
			foreach ($acl_criteria['num'] as $idx => $num)
			{
				if ( ! empty($acl_criteria['outcome'][$idx]))
				{
					$criteria[$num] = trim($acl_criteria['outcome'][$idx]);
				}
			}
			ksort($criteria);

			$acl_data = array(
				'acl_j_id' => $j_id,
				'acl_name' => 'Custom criteria for ' . $journey['c_name'],
				'acl_type' => 'journey',
				'acl_criteria' => $criteria,
			);

			if ( ! $acl)
			{
				$res = $this->assessments_model->insert_acl($acl_data);
			}
			else
			{
				$res = $this->assessments_model->update_acl($acl['acl_id'], $acl_data);
			}

			if ($res)
			{
				$this->session->set_flashdata('action', 'Journey criteria list has been set.');
				$this->log_model->set('Assement criteria set for journey #' . $j_id);
			}

			redirect('admin/assessments/index/' . $j_id);
		}

		redirect('admin/assessments/index/' . $j_id);
	}




	/**
	 * Add a new assessment to a journey
	 */
	public function set_assessment($j_id)
	{
		if ($this->input->post())
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('jas[jas_date]', 'Assessment date', 'required|parse_date');

			// if form validates
			if ($this->form_validation->run())
			{
				$jas = $this->input->post('jas');

				$jas_data = array(
					'jas_j_id' => $j_id,
					'jas_acl_id' => $jas['jas_acl_id'],
					'jas_date' => $jas['jas_date'],
					'jas_notes' => $jas['jas_notes'],
				);

				$scores = $this->input->post('jacs');

				// set assessment in db
				$this->assessments_model->insert_assessment($jas_data, $scores);

				// Get the criteria list - if it's CSOP or TOP, we need to set those dates for the journey.
				$acl = $this->assessments_model->get_acl($jas['jas_acl_id']);
				if ($acl['acl_type'] === 'csop' || $acl['acl_type'] === 'top')
				{
					$this->journeys_model->set_journey_ass_dates($j_id);
				}

				// set log
				$this->log_model->set('Assesment set for journey #' . $j_id . ' created');

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);
			}
			else
			{
				show_error(validation_errors());
			}
		}

		// redirect to assessment page
		redirect('/admin/assessments/index/' . $j_id);
	}




	public function parse()
	{
		$outcomes = array('Motivation and taking responsibility' => 'motivation_and_taking_responsibility',
						  'Self care and living skills' => 'self_care_and_living_skills',
						  'Managing money' => 'managing_money',
						  'Social networks and relationships' => 'social_networks_and_relationships',
						  'Drug and alcohol misuse' => 'drug_and_alcohol_misuse',
						  'Physical health' => 'physical_health',
						  'Emotional and mental health' => 'emotional_and_mental_health',
						  'Meaningful use of time' => 'meaningful_use_of_time',
						  'Managing tenancy and accommodation' => 'managing_tenancy_and_accommodation',
						  'Offending' => 'offending');

		$sql = 'SELECT * FROM _old_journey_assessment_criteria';
		$criterias = $this->db->query($sql)->result_array();

		foreach ($criterias as $criteria)
		{
			$num = 1;
			foreach ($outcomes as $title => $id)
			{
				$new_crit = array(
					'jac_j_id' => $criteria['jac_j_id'],
					'jac_num' => $num,
					'jac_title' => $title,
					'jac_date_start' => $criteria[$id . '_start'],
					'jac_date_end' => $criteria[$id . '_end'],
				);
				$this->db->insert('journey_ass_criteria', $new_crit);
				$num++;
			}
		}

		$sql = 'SELECT * FROM _old_journey_assessments';
		$asses = $this->db->query($sql)->result_array();

		foreach ($asses as $ass)
		{
			$new_ass = array(
				'jas_id' => $ass['jas_id'],
				'jas_j_id' => $ass['jas_j_id'],
				'jas_date' => $ass['jas_date'],
				'jas_notes' => $ass['jas_notes'],
			);
			$this->db->insert('journey_assessments', $new_ass);

			$num = 1;
			foreach ($outcomes as $title => $id)
			{
				$ass_score = array(
					'jass_jas_id' => $ass['jas_id'],
					'jass_jac_num' => $num,
					'jass_score' => $ass[$id],
				);
				$this->db->insert('journey_ass_scores', $ass_score);
				$num++;
			}
		}
	}




	/**
	 * CSOP Reporting Charts for a journey
	 */
	public function csop($j_id = 0)
	{
		if ( ! $journey = $this->journeys_model->get_full_journey_info($j_id))
		{
			show_404();
		}

		// load datasets
		$this->load->config('datasets');

		$this->data['journey'] =& $journey;
		$this->load->model('assessments_model');
		$this->load->model('ass_criteria_model');
		$this->load->helper('assessment_helper');

		// get all assessments for this journey
		$scores = $this->assessments_model->get_assessments($journey['j_id'], 'csop');

		$this->data['scores'] = $scores;
		$this->data['assessments'] = assessment_scores_csop($scores);

		$this->layout->set_breadcrumb(array(
			'Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'],
			'CSOP Reports' => '',
		));

		$this->layout->set_title('CSOP Reports');
		$this->layout->set_external_js('https://www.google.com/jsapi');
		$this->layout->set_js(array(
			'plugins/jquery.validate',
			'views/admin/journeys/assessments/csop',
		));
		$this->layout->set_view('/admin/journeys/assessments/csop');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




}
