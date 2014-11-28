<?php

class Ajax extends MY_controller {


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();

		$this->load->helper('journey');
	}


	public function search_clients()
	{
		// load clients model
		$this->load->model('clients_model');

		$this->data['clients'] = $this->clients_model->search_clients_ajax();

		$this->load->vars($this->data);
		$this->load->view('/admin/ajax/search_clients');
	}

	public function get_gps()
	{
		$this->load->model('gps_model');

		$gps = $this->gps_model->get_gps_ajax();

		foreach($gps as $gp)
		{
			$gps_array[] = $gp['gp'];
		}

		$this->output->set_content_type('text/json');
		$this->output->set_output(json_encode($gps_array));
	}

	function appointment($j_id, $appt_id = 0)
	{
		// load models
		$this->load->model(array('journeys_model', 'dna_reasons_model', 'recovery_coaches_model'));

		// if appt_id is set
		if($appt_id)
		{
			// get single appointment information
			$appt = $this->journeys_model->get_appointment($appt_id);
		}

		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('ja_date_offered', '', 'parse_date')
								  ->set_rules('ja_date', '', 'required|parse_date')
								  ->set_rules('ja_rc_id', '', 'required')
								  ->set_rules('ja_length', '', 'integer')
								  ->set_rules('ja_notes', '', 'strip_tags');

			// if form validates
			if($this->form_validation->run())
			{
				// set appointment and return appointment id
				$ja_id = $this->journeys_model->set_appointment($j_id, @$appt);

				// load ndtms library
				$this->load->library('ndtms');

				// check to see if journey is NDTMS valid
				$this->ndtms->check_is_valid($j_id);

				$log = 'Appointment #' . $ja_id . ' for journey #' . $j_id . ' ' . (@$appt ? 'updated' : 'created') . '.';

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// set log
				$this->log_model->set($log);

				// echo action message
				echo (@$appt ? 'Appointment updated' : 'Appointment added');
				exit;
			}
		}

		// load datasets
		$this->load->config('datasets');

		// get form data
		$this->data['recovery_coaches'] = $this->recovery_coaches_model->get_recovery_coaches();
		$this->data['dna_reasons'] = $this->dna_reasons_model->get_dna_reasons();

		$this->data['appt'] =& $appt;

		$this->load->vars($this->data);
		$this->load->view('/admin/ajax/appointment');
	}


	function event($j_id, $event_id = 0)
	{
		$this->load->config('datasets');
		$this->load->model(array(
			'journeys_model',
			'event_types_model',
			'recovery_coaches_model',
			'dna_reasons_model',
			'appointments_model',
		));

		if ($event_id)
		{
			// get single event information
			$event = $this->journeys_model->get_event($event_id);
			$appt = $this->journeys_model->get_appointment(0, $event_id);
		}

		if ($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('je_date', '', 'required|parse_date')
								  ->set_rules('je_et_id', '', 'required')
								  ->set_rules('je_notes', '', 'strip_tags')
								  ->set_rules('ja_notes', '', 'strip_tags')
								  ->set_rules('ja_date_offered', '', 'parse_date');


			// if form validates
			if ($this->form_validation->run())
			{
				// set event and return event id
				$je_id = $this->journeys_model->set_event($j_id, @$event);

				$log = 'Event #' . $je_id . ' for journey #' . $j_id . ' ' . (@$event ? 'updated' : 'added') . '.';

				// Is this an appointment?
				if ($this->input->post('et_ec_id') == EVENT_CAT_APPT)
				{
					// Make appointment

					$appointment_data = array(
						'ja_j_id' => $j_id,
						'ja_je_id' => $je_id,
						'ja_datetime' => $this->input->post('je_date') . ' ' . $this->input->post('time_hour') . ':' . $this->input->post('time_minute') . ':00',
						'ja_date_offered' => $this->input->post('ja_date_offered'),
						'ja_attended' => ($this->input->post('ja_attended') ? $this->input->post('ja_attended') : NULL),
						'ja_dr_id' => $this->input->post('ja_dr_id'),
						'ja_length' => $this->input->post('ja_length'),
						'ja_notes' => $this->input->post('je_notes'),
					);

					if ($this->input->post('ja_id'))
					{
						$ja_id = $this->appointments_model->update($this->input->post('ja_id'), $appointment_data);
					}
					else
					{
						$ja_id = $this->appointments_model->insert($appointment_data);
					}
				}

				// set log
				$this->log_model->set($log);

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// echo action message
				echo (@$event ? 'Event updated' : 'Event added');
				exit;
			}
		}

		// load datasets
		$this->load->config('datasets');

		$this->data['event_types'] = $this->event_types_model->get_event_types();
		$this->data['event_categories'] = $this->event_types_model->get_event_categories();
		$this->data['recovery_coaches'] = $this->recovery_coaches_model->get_recovery_coaches(); //key workers
		$this->data['dna_reasons'] = $this->dna_reasons_model->get_dna_reasons();

		$this->data['event'] =& $event;
		$this->data['appt'] =& $appt;

		$this->load->vars($this->data);
		$this->load->view('/admin/ajax/event');
	}




	public function note($j_id, $note_id = 0)
    {
        // load models
        $this->load->model(array('journeys_model', 'recovery_coaches_model'));

        if($note_id)
        {
            // get single note information
            $note = $this->journeys_model->get_note($note_id);
        }

        if($_POST)
        {
            // load form validation library
            $this->load->library('form_validation');

            // set rules
            $this->form_validation->set_rules('jn_date', '', 'required|parse_date')
                                  ->set_rules('jn_rc_id', '', 'required')
                                  ->set_rules('jn_notes', '', 'strip_tags');

            // if form validates
            if($this->form_validation->run())
            {
                // set notes and return note id
                $jn_id = $this->journeys_model->set_notes($j_id, @$note);

                $log = 'Note #' . $jn_id . ' for journey #' . $j_id . ' ' . (@$note ? 'updated' : 'added') . '.';

                // set log
                $this->log_model->set($log);

                // Update journey cache
				$this->journeys_model->cache_journey($j_id);

                // echo action message
                echo (@$note ? 'Note updated' : 'Note added');
                exit;
            }
        }

        // load datasets
        $this->load->config('datasets');

        // get form data
        $this->data['recovery_coaches'] = $this->recovery_coaches_model->get_recovery_coaches();

        $this->data['note'] =& $note;

        $this->load->vars($this->data);
        $this->load->view('/admin/ajax/note');
    }

	public function modality($j_id, $mod_id = FALSE)
	{
		// load models
		$this->load->model(array(
                'journeys_model',
            ));

        if($mod_id)
        {
            // get single modality information
            $modality = $this->journeys_model->get_modality($mod_id, TRUE);
            $this->data['modality'] = $modality;
        }

        // load datasets
        $this->load->config('datasets');

        $this->data['modality_treatments']  = config_item('modality_treatments');
        $this->data['intervention_setting'] = config_item('intervention_setting');
        $this->data['exit_status']          = config_item('exit_status');

		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('mod_cpdate', '', 'required|parse_date')
                                  ->set_rules('mod_treatment', '', 'required')
                                  ->set_rules('mod_refdate', '', 'required|parse_date')
                                  ->set_rules('mod_firstapptdate', '', 'required|parse_date')
                                  ->set_rules('mod_intsetting', '', 'required')
                                  ->set_rules('mod_start', '', 'required|parse_date')
                                  ->set_rules('mod_end', '', 'required|parse_date')
								  ->set_rules('mod_exit', '', 'required');

			// if form validates
			if($this->form_validation->run())
			{
				// set modality and return modality id
				$mod_id = $this->journeys_model->set_modality($j_id, @$modality);

				$log = 'Modality #' . $mod_id . ' for journey #' . $j_id . ' ' . (@$modality ? 'updated' : 'added') . '.';

				// set log
				$this->log_model->set($log);

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// echo action message
				echo (@$modality ? 'Modality updated' : 'Modality added');
				exit;
			}
		}

		$this->load->vars($this->data);
		$this->load->view('/admin/ajax/modality');
	}

	//add/edit family members
	public function family($j_id)
	{
		// load models
		$this->load->model(array(
				'journeys_model',
				'family_model'
			));

		// if no journey can be found
		if ( ! $journey = $this->journeys_model->get_basic_journey_info($j_id))
			show_404();

		// if notes form has been submitted
		if ($_POST)
		{
			$this->family_model->set_journey_family_info($journey['j_id']);
			$this->log_model->set('Family notes updated for journey #' . $j_id);
			if ($this->input->is_ajax_request())
			{
				echo "Family information updated.";
				return;
			}
			else
			{
				redirect(current_url());
			}
		}

		$this->load->config('datasets');

		$family_info = $this->family_model->get_journey_family_info($j_id);
		$family_clients = $this->family_model->get_clients($j_id);

		$this->data['family_clients'] =& $family_clients;
		$this->data['family_info'] =& $family_info;
		$this->data['journey'] =& $journey;

		/*
		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Family'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Family' => ''));
		$this->layout->set_css('datepicker');
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/family'));
		$this->layout->set_view('/admin/journeys/family');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
		*/

		$this->load->vars($this->data);
		$this->load->view('/admin/ajax/family');
	}

	//asks the user if they are sure they want to submit the case for approval to the next status
	public function approve($j_id)
	{
		// load models
		$this->load->model(array('journeys_model', 'admin_permissions_model'));
		$this->load->config('datasets');

		$this->data['journey'] = $this->journeys_model->get_basic_journey_info($j_id);

		//get admins that can approve
		if($this->data['journey']['j_type'] == 'C')
		{
			$permis_type = 'apt_can_approve_client';
		}
		else
		{
			$permis_type = 'apt_can_approve_family';
		}

		$this->data['approving_admins'] = $this->admin_permissions_model->get_allowed_admins($permis_type);
		$this->data['next_status'] = journey_next_status($this->data['journey']['j_status']);

		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('j_new_status', '', 'required');

			// if form validates
			if($this->form_validation->run())
			{
				$this->load->model(array('messages_model'));
				//send some messages?
				$mdata = array(
					'm_from_admin' => $this->session->userdata('a_id'),
					'm_type' => 'New Journey',
					'm_status' => 'Active',
					'm_sent_date' => date('Y-m-d H:i:s'),
					'm_text' => "Please approve this journey to ". $this->data['next_status'][1] ." status.",
					'm_j_id' => $this->data['journey']['j_id'],
					'm_link' => 'admin/journeys/contact/'. $this->data['journey']['j_id'],
					'm_link_text' => 'Review',
					'm_cat_name' => 'j_status',
					'm_cat_value' => $this->data['next_status'][0],
				);

				foreach($this->data['approving_admins'] as $a)
				{
					$mdata['m_to_admin'][] = $a['a_id'];
				}

				if ($this->messages_model->add_message($mdata))
				{
					echo "Journey has been entered for approval.";
				}
				else
				{
					echo "The journey could <strong>not</strong> be entered for approval.";
				}

				//send some e-mails?

				//done
				exit;
			}
		}

		$this->load->vars($this->data);
		$this->load->view('/admin/ajax/approve');

	}

	//users with permission can change the status
	public function status($j_id)
	{
		// load models
		$this->load->model(array('journeys_model', 'admin_permissions_model'));
		$this->load->config('datasets');

		$this->data['journey'] = $this->journeys_model->get_basic_journey_info($j_id);

		//can they approve it?
		$permissions = $this->session->userdata('permissions');

		if($this->data['journey']['j_type'] == 'C' && $permissions['apt_can_approve_client'] == 0)
		{
			show_error('You do not have permission to approve client journeys.');
		}
		else if($this->data['journey']['j_type'] == 'F' && $permissions['apt_can_approve_family'] == 0)
		{
			show_error('You do not have permission to approve family journeys.');
		}

		$this->data['status_codes'] = $this->config->item('j_status_codes');
		$this->data['status_data'] = $this->config->item('j_status_data');
		$this->data['tiers'] = $this->config->item('j_tiers');
		$this->data['next_status'] = journey_next_status($this->data['journey']['j_status']);

		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('j_status', '', 'required');

			// if form validates
			if($this->form_validation->run())
			{
				$this->journeys_model->update_journey_status($j_id, $this->input->post('j_status'), $this->input->post('j_tier'));

				// Remove messages relating to this status change
				$this->load->model('messages_model');
				$this->messages_model->delete(array(
					'm_j_id' => $j_id,
					'm_cat_name' => 'j_status',
					'm_cat_value' => $this->input->post('j_status'),
				));

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				//done
				$this->session->set_flashdata('action', 'Journey status updated');
				exit;
			}
		}

		$this->load->vars($this->data);
		$this->load->view('/admin/ajax/status');

	}

	/**
	 * Looks up the journey postcode and returns ONS code of the local authority it is in
	 *
	 * @param j_id int			The journey id. If provided, it can be used to lookup the postcode or save the ONS code.
	 * @param postcode int		Postcode to lookup, if not provided then it will read from the Db for j_id.
	 * @param savetodb boolean	If TRUE, will save it the ONS code to the Db based on the j_id.
	 */
	public function locauthority($j_id=null, $postcode=null, $savetodb=true)
	{
		//some sanity checking first, check we have enough parameters
		if(($j_id == NULL && $postcode == NULL) || ($j_id == NULL && $savetodb == TRUE))
		{
			$ouput_data = array('error' => 1, 'error_msg' => 'Not enough information provided.');
			$this->output->set_content_type('text/json');
			$this->output->set_output(json_encode($ouput_data));
		}

		if($postcode == NULL)
		{
			//we need to look it up from the Db with j_id
			$postcode = 'NE14XF'; //TEST data
		}
		$postcode = preg_replace('/\s+/', '', $postcode);
		$postcode = preg_replace('/%20+/', '', $postcode);

		$json_filename = APPPATH . 'postcode_cache/' . strtolower($postcode) . '.json';
		$data = array();

		if (file_exists($json_filename)) {
			$file_contents = file_get_contents($json_filename);
			$data = json_decode($file_contents);
		}

		//lookup ONS
		if (empty($data) || !isset($data->wgs84_lat)) {
			$url = 'http://mapit.mysociety.org/postcode/' . urlencode($postcode);
			$http = array(
				'method'        => 'GET',
				'timeout'       => 30,
				'ignore_errors' => true,
			);

			$context = stream_context_create(array('http' => $http));
			$file_contents = file_get_contents($url, false, $context);
			$data = json_decode($file_contents);

			try {
				file_put_contents($json_filename, $file_contents);
			} catch (Exception $e) {}
		}

		if(isset($data->areas))
		{
			$valid_types = array('DIS', 'LBO', 'MTD', 'UTA', 'COI'); //the type of areas we want to find.
			//('DIS', 'LBO', 'MTD', 'UTA', 'COI') Should work for all postcodes in England, including the Isles of Scilly. City of London is a special case, so might need checking.

			foreach($data->areas as $area)
			{
				if(in_array($area->type, $valid_types))
				{
					$ons_code = $area->codes->ons;
					$ons_name = $area->name;
					break;
				}
			}
		}

		if(!isset($ons_code))
		{
			$ouput_data = array('error' => 1, 'error_msg' => 'Local Authority could not be found for that postcode.');
			$this->output->set_content_type('text/json');
			$this->output->set_output(json_encode($ouput_data));
		}
		else
		{
			if($savetodb == true)
			{
				//save the ONS to the Db.
				// @TODO
			}

			//return the ONS
			$ouput_data = array('ons_code' => $ons_code, 'ons_name' => $ons_name);
			$this->output->set_content_type('text/json');
			$this->output->set_output(json_encode($ouput_data));
		}
	}
}
