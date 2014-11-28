<?php

class Journeys extends MY_controller {


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();

		//get permissions, used by the journeys_nav view
		$this->data['permissions'] = $this->session->userdata('permissions');

		// load datasets
		$this->load->config('datasets');

		$this->data['modality_treatments']  = config_item('modality_treatments');
		$this->data['intervention_setting'] = config_item('intervention_setting');
		$this->data['exit_status']          = config_item('exit_status');

		// add a N/A to exit_status
		$this->data['exit_status']['0'] = 'N/A';

		$this->load->model(array('journeys_model', 'staff_model'));
		$this->load->helper(array('journey', 'date'));

		$this->data['staff'] = $this->staff_model->get_staff();

		// set default js for journeys section
		$this->layout->set_js(array(
			'views/admin/journeys/default',
			'views/admin/journeys/family',
			'views/admin/journeys/publish-journey-confirm',
		));

		$this->layout->set_title('Journeys');
		$this->layout->set_breadcrumb('Journeys', '/admin/journeys');
	}


	public function index($page = 0)
	{
		if( $this->session->userdata('can_read_client') == 0 || $this->session->userdata['permissions']['apt_can_read_client'] != 1)
		{
			show_error('You do not have permission to view client journeys.');
		}
		// Only want client type for clients and journeys
		$_GET['j_type'] = 'C';

		$this->load->model('recovery_coaches_model');

		$total = $this->journeys_model->get_total_journeys();

		$this->load->library('pagination');

		$config['base_url'] = '/admin/journeys/index/';

		$config['total_rows'] = $total;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$config['suffix'] = '?' . @http_build_query($this->input->get());

		$this->pagination->initialize($config);

		$this->data['journeys'] = $this->journeys_model->get_journeys($page, $config['per_page']);

		$this->data['total'] = ($this->data['journeys'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['journeys'])) . ' of ' . $total . '.' : '0 results');

		$this->data['total_export'] = $total;

		$this->data['sort']  = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc');
		$this->data['sort'] .= '&amp;pp=' . $_GET['pp'];
		$this->data['sort'] .= '&amp;j_c_id=' . @$_GET['j_c_id'];
		$this->data['sort'] .= '&amp;c_sname=' . @$_GET['c_sname'];
		$this->data['sort'] .= '&amp;date_from=' . @$_GET['date_from'];
		$this->data['sort'] .= '&amp;date_to=' . @$_GET['date_to'];
		$this->data['sort'] .= '&amp;c_catchment_area=' . @$_GET['c_catchment_area'];
		$this->data['sort'] .= '&amp;j_id=' . @$_GET['j_id'];
		$this->data['sort'] .= '&amp;last_seen=' . @$_GET['last_seen'];

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->data['key_workers'] = $this->recovery_coaches_model->get_recovery_coaches();

		// load datasets
		$this->load->config('datasets');

		$this->layout->set_js(array('views/admin/journeys/index'));
		$this->layout->set_view('/admin/journeys/index');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




	/**
	 * Keyworker page to highlight own journeys
	 */
	public function keyworker($page = 0)
	{
		$rc_id = $this->session->userdata('rc_id');

		if ( ! $rc_id)
		{
			$this->session->set_flashdata('action', 'Your user account is not configured as a keyworker.');
			redirect('admin');
		}

		$this->layout->set_title('Allocated to me');
		$this->layout->set_breadcrumb('Allocated to me');	//, '/admin/journeys/keyworker');

		$this->load->model(array('journeys_model', 'recovery_coaches_model'));

		$this->data['total'] = $this->journeys_model->get_total_keyworker_journeys($rc_id);

		$this->load->library('pagination');
		$config['base_url'] = '/admin/journeys/keyworker';
		$config['total_rows'] = $this->data['total'];
		$config['per_page'] = (@$_GET['pp'] ? (int) $_GET['pp'] : $_GET['pp'] = 20);
		$config['uri_segment'] = 4;
		$config['suffix'] = '?' . @http_build_query($this->input->get());
		$this->pagination->initialize($config);

		$this->data['journeys'] = $this->journeys_model->keyworker_journeys($rc_id, $page, $config['per_page']);

		$params = $this->input->get();
		unset($params['order']);
		$params['sort'] = ($_GET['sort'] == 'asc' ? 'desc' : 'asc');
		$this->data['sort'] = '&amp;' . http_build_query($params);

		// load datasets
		$this->load->config('datasets');

		$this->layout->set_js(array('views/admin/journeys/index'));
		$this->layout->set_view('/admin/journeys/keyworker');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




	public function new_journey($c_id = 0)
	{
		//check they can create a journey of this type
		if($this->input->post())
		{
			$j_type = $this->input->post('j_type'); //if they fail when submitting, they might get confused
		}
		else
		{
			$j_type = $this->input->get('j_type');
		}
		if( $this->session->userdata('can_edit_client') == 0 && $j_type  != 'F' )
		{
			redirect(current_url() . '?j_type=F');
		}
		if( $this->session->userdata('can_edit_family') == 0 && $j_type  != 'C' )
		{
			redirect(current_url() . '?j_type=C');
		}

		// load models
		$this->load->model(array('clients_model', 'referral_sources_model', 'recovery_coaches_model', 'family_model'));

		// load datasets
		$this->load->config('datasets');

		// get client if c_id set
		if($c_id)
			$client = $this->clients_model->get_client($c_id, $format = FALSE);

		// Check if the journey is being created via a family member of another journey
		if ($from_j_id = $this->input->get_post('from_j_id'))
		{
			// Get journey + client info
			if ( ! $from_journey = $this->journeys_model->get_full_journey_info( (int) $from_j_id))
			{
				show_error('Could not find journey ' . $from_j_id);
			}
			$this->data['from_journey'] =& $from_journey;
		}

		// if form submitted
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('c_fname', '', 'required|strip_tags|ucfirst')
								  ->set_rules('c_sname', '', 'required|strip_tags|ucfirst')
								  ->set_rules('c_date_of_birth', '', 'required|parse_date')
								  ->set_rules('c_address', '', 'required|strip_tags')
								  ->set_rules('c_post_code', '', 'required|strip_tags|strtoupper')
								  ->set_rules('c_tel_home', '', 'strip_tags')
								  ->set_rules('c_tel_mob', '', 'strip_tags')
								  ->set_rules('j_date_of_referral', '', 'required|parse_date')
								  ->set_rules('j_rs_id', '', 'integer')
								  ->set_rules('j_rc_id', '', 'integer');

			// if form validates
			if ($this->form_validation->run())
			{
				// get catchment area
				$catchment_area = $this->clients_model->get_catchment_area($this->input->post('c_post_code'));

				// load postcode model
				$this->load->model('postcode_model');

				// get lat lng for postcode
				$this->postcode_model->get_lat_lng($this->input->post('c_post_code'));

				// start new journey and return its id
				$j_id = $this->journeys_model->start_new_journey(@$client['c_id'], $catchment_area);

				// set log
				$this->log_model->set('New journey #' . $j_id . ' created');

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// Set up relationship for family section if required
				if ($from_journey)
				{
					$new_journey = $this->journeys_model->get_full_journey_info($j_id);

					// Add the original journey client as a family member to new journey
					$add1_data = array(
						'fc_j_id' => $j_id,		// new journey
						'fc_j_c_id' => $new_journey['j_c_id'],		// client on new journey
						'fc_rel_type' => $this->config->item($this->input->post('fc_rel_type'), 'relative_types_opposite'),		// c_id is a X of j_c_id
						'fc_c_id' => $from_journey['j_c_id'],		// old client
					);

					$add1 = $this->family_model->add_client($add1_data);

					// Add the client from the original journey as a family member of the new journey
					$add2_data = array(
						'fc_j_id' => $from_journey['j_id'],
						'fc_j_c_id' => $from_journey['j_c_id'],
						'fc_rel_type' => $this->input->post('fc_rel_type'),
						'fc_c_id' => $new_journey['j_c_id'],
					);
					$add2 = $this->family_model->add_client($add2_data);

					if ($add1 && $add2)
					{
						// Remove the family member from the old array
						// Get the details from the session that were set from the family/promote controller
						$data = $this->session->userdata('delete_family_member');
						// get family members
						$family_members = $this->family_model->get_family_members($data['j_id']);
						// delete family member from array
						unset($family_members[$data['key']]);
						// save the changed array as the new list of family members
						$this->family_model->save_family_members($data['j_id'], $family_members);

						// set log
						$name = $this->input->post('c_fname') . ' ' . $this->input->post('c_sname');
						$this->log_model->set('Family member ' . $name . ' on #' . $from_journey['j_type'] . $from_journey['j_id'] . ' started their own journey.');

						// Go back to the original journey family page
						redirect('/admin/family/index/' . $from_journey['j_id']);
					}
					else
					{
						show_error('Failed to save the family details.');
					}
				}

				// redirect to journey info page
				redirect('/admin/journeys/info/' . $j_id);
			}
		}

		// get form values
		$this->data['referral_sources'] = $this->referral_sources_model->get_referral_sources();
		$this->data['recovery_coaches'] = $this->recovery_coaches_model->get_recovery_coaches();

		$this->data['client'] =& $client;

		$this->layout->set_title('New journey');
		$this->layout->set_breadcrumb('New journey');
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/new_journey'));
		$this->layout->set_view('/admin/journeys/new_journey');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	public function info($j_id)
	{
		// if no journey can be found
		if( ! $journey = $this->journeys_model->get_full_journey_info($j_id))
			show_404();

		//do they have permission?
		if( $journey['j_type'] == 'C' && $this->session->userdata('can_read_client') == 0)
		{
			show_error('You do not have permission to view client journeys.');
		}
		if( $journey['j_type'] == 'F' && $this->session->userdata('can_read_family') == 0)
		{
			show_error('You do not have permission to view family journeys.');
		}

		//if setting the journey to published (makes notes & events uneditable)
		if(isset($_GET['publish']) && $_GET['publish'] == 1)
		{
			$result = $this->journeys_model->set_published($journey['j_id']);
			if($result)
			{
				$this->session->set_flashdata('action', 'Journey published.');
			}
			else
			{
				$this->session->set_flashdata('action', 'Journey not published.');
			}
			redirect(current_url());
		}
		//if setting the journey to unpublished
		if(isset($_GET['unpublish']) && $_GET['unpublish'] == 1)
		{
			if($this->session->userdata('a_master')) {
				$this->data['permissions']['apt_can_unpublish'] = 1;
				$this->session->set_userdata('permissions', $this->data['permissions']);
			}

			if(!isset($this->data['permissions']['apt_can_unpublish']) || $this->data['permissions']['apt_can_unpublish'] != 1)
			{
				$this->session->set_flashdata('action', 'Sorry, you don\'t have permission to unpublish journeys.');
				redirect(current_url());
			}
			else
			{
				$result = $this->journeys_model->set_unpublished($journey['j_id']);
				if($result)
				{
					$this->session->set_flashdata('action', 'Journey unpublished.');
				}
				else
				{
					$this->session->set_flashdata('action', 'Journey not published.');
				}
				redirect(current_url());
			}
		}
		// if trying to get appointment
		elseif(@$_GET['ja_id'])
		{
			// get single appointment information
			$appt = $this->journeys_model->get_appointment($_GET['ja_id']);

			// if attempting to delete appointment
			if(@$_GET['delete'])
			{
				// delete appointment information from db
				$this->journeys_model->delete_appointment($journey['j_id'], $_GET['ja_id']);

				// Load NDTMS library to validate journey
				$this->load->driver('ndtms');
				$this->ndtms->validate_journey($j_id);

				// set log
				$this->log_model->set('Appointment #' . (int)$_GET['ja_id'] . ' for journey #' . $journey['j_id'] . ' deleted');

				// Update journey cache
				$this->journeys_model->cache_journey($journey['j_id']);

				// redirect to current journey info page
				redirect(current_url());
			}
		}
		// if trying to get event
		elseif(@$_GET['je_id'])
		{
			// get single event information
			$event = $this->journeys_model->get_event($_GET['je_id']);

			// if attempting to delete event
			if(@$_GET['delete'])
			{
				// delete event information from db
				$this->journeys_model->delete_event($_GET['je_id']);

				// set log
				$this->log_model->set('Event #' . (int)$_GET['je_id'] . ' for journey #' . $journey['j_id'] . ' deleted');

				// Update journey cache
				$this->journeys_model->cache_journey($journey['j_id']);

				// redirect to current journey info page
				redirect(current_url());
			}
		}
		// if trying to get note
		elseif(@$_GET['jn_id'])
		{
			// get single note information
			$note = $this->journeys_model->get_note($_GET['jn_id']);

			// if attempting to delete note
			if(@$_GET['delete'])
			{
				// delete note from db
				$this->journeys_model->delete_notes($_GET['jn_id']);

				// set log
				$this->log_model->set('Note #' . (int)$_GET['jn_id'] . ' for journey #' . $journey['j_id'] . ' deleted');

				// Update journey cache
				$this->journeys_model->cache_journey($journey['j_id']);

				// redirect to current journey info page
				redirect(current_url());
			}
		}

		//if uploading a file
		$this->data['file_upload_type'] = 'pdf|doc|odt|jpg|png';
		if($this->input->post('ju_name'))
		{

			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('ju_name', '', 'required|strip_tags');

			// if form validates
			if($this->form_validation->run())
			{

				// if file exists
				if(@$_FILES['userfile']['name'])
				{

					$config['upload_path'] = APPPATH . '../storage/journeyfiles';
					$config['allowed_types'] = $this->data['file_upload_type'] ;
					$config['file_name'] = time() . $_FILES['userfile']['name'];

					$this->load->library('upload', $config);

					// if file does not upload ok
					if ( ! $this->upload->do_upload())
					{
						// get upload errors
						$this->data['upload_errors'] = $this->upload->display_errors('<label class="error">', '</label>');
						//print_r('no upload'. $this->data['upload_errors']); die();
					}
					else
					{
						// get upload data
						$data = $this->upload->data();
					}
				}

				// if upload worked ok
				if(@$data)
				{
					// add template to intervention
					$this->journeys_model->add_journey_file($j_id, $data);

					// set log
					$this->log_model->set('File uploaded for journey #' . $journey['j_id'] . '.');

					//Define the message to display
					$this->session->set_flashdata('action', 'Journey file uploaded');

					// redirect to current info page
					redirect(current_url());
				}
			}
		}

		if(@$_GET['ju_id'] && @$_GET['deletefile'])
		{
			// delete template
			$this->journeys_model->delete_journey_file(@$_GET['ju_id']);

			//Define the message to display
			$this->log_model->set('File deleted for journey #' . $journey['j_id'] . '.');
			$this->session->set_flashdata('action', 'Journey file deleted');

			// "refresh" page
			redirect(current_url());
		}

		// load datasets
		$this->load->config('datasets');

		$this->data['journey'] =& $journey;
		$this->data['journey_files'] = $this->journeys_model->get_journey_files($j_id);
		$this->data['j_status_codes'] = $this->config->item('j_status_codes');
		$this->load->model('mail_merge_model');
		$this->data['mail_merges'] = $this->mail_merge_model->get_mail_merges();

		// get assessment criteria for this journey
		$this->load->model('assessments_model');
		$this->load->model('ass_criteria_model');
		$this->data['acl'] = $this->assessments_model->get_journey_acl($journey['j_id']);
		$acls = $this->ass_criteria_model->get_lists();
		foreach ($acls as $acl)
		{
			$this->data['acls_dropdown'][$acl['acl_id']] = $acl['acl_name'];
		}
		$this->data['acls'] =& $acls;

		$this->layout->set_title('Client #' . $journey['j_c_id']);
		$this->layout->set_breadcrumb('Client #' . $journey['j_c_id']);
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/info', 'views/admin/journeys/assessments'));
		$this->layout->set_view('/admin/journeys/info/index');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	public function contact($j_id=null)
	{
		if($j_id != null)
		{
			$journey = $this->journeys_model->get_basic_journey_info($j_id);
		}
		else
		{
			$journey = null;
		}
		// if no journey can be found
		if(! $journey)
		{
			if(!$this->input->get('j_type'))
			{
				show_404(); //need to specify a journey (or at least a type)
			}
			if($this->input->get('c_id'))
			{
				 $client = $this->clients_model->get_client($this->input->get('c_id'));
				//client details are not essential
			}
		}


		// load client model
		$this->load->model('clients_model');

		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('j_date_of_referral', '', 'required|parse_date')
								  ->set_rules('ji_date_referral_received', '', 'parse_date')
								  ->set_rules('ci_fname', '', 'required|strip_tags|ucfirst')
								  ->set_rules('ci_sname', '', 'required|strip_tags|ucfirst')
								  ->set_rules('ci_date_of_birth', '', 'required|parse_date')
								  ->set_rules('ci_gender', '', 'strip_tags')
								  ->set_rules('ci_address', '', 'required|strip_tags')
								  ->set_rules('ci_post_code', '', 'required|strip_tags|strtoupper')
								  ->set_rules('ci_tel_home', '', 'strip_tags')
								  ->set_rules('ci_tel_mob', '', 'strip_tags')
								  ->set_rules('ci_catchment_area', '', '')
								  ->set_rules('ji_summary_of_needs', '', 'strip_tags')
								  ->set_rules('ji_referral_received_by', '', 'integer')
								  ->set_rules('ci_interpreter_required', '', 'integer')
								  ->set_rules('ci_preferred_contact_method', '', 'strip_tags')
								  ->set_rules('ci_preferred_contact_time', '', 'strip_tags')
								  ->set_rules('ci_staff_can_leave_message', '', 'integer')
								  ->set_rules('ci_staff_can_identify_themselves', '', 'integer')
								  ->set_rules('ci_preferred_appointment_time', '', 'strip_tags')
								  ->set_rules('ci_escape_write_to_you', '', 'integer')
								  ->set_rules('ci_disabilities', '', 'strip_tags')
								  ->set_rules('ci_ethnicity', '', 'strip_tags')
								  ->set_rules('ci_nationality', '', 'strip_tags')
								  ->set_rules('ci_sexuality', '', 'strip_tags')
								  ->set_rules('jd_substance_1', '', 'integer')
								  ->set_rules('jd_substance_2', '', 'integer')
								  ->set_rules('ci_consents_to_ndtms', '', 'integer')
								  ->set_rules('ji_rs_id', '', 'integer')
								  ->set_rules('ji_referrers_name', '', 'strip_tags')
								  ->set_rules('ji_referrers_tel', '', 'strip_tags')
								  ->set_rules('ci_pregnant', '', 'integer')
								  ->set_rules('ci_relationship_status', '', 'integer')
								  ->set_rules('ci_parental_status', '', 'integer')
								  ->set_rules('ci_no_of_children', '', 'integer')
								  ->set_rules('ci_access_to_children', '', 'integer')
								  ;
			// find the gp code
			//preg_match_all('^\#([A-Za-z0-9]+)^', $this->input->post('ci_gp_code'), $gp_code);
			// set gp code in post array
			//$_POST['ci_gp_code'] = @$gp_code[1][0];

			// if form validates
			if($this->form_validation->run())
			{
				//if we have no journey type, go make one first
				if(! $journey)
				{
					if(!isset($client['c_id'])) {$client['c_id'] = null;}
					$j_id = $this->journeys_model->start_new_journey($client['c_id'], '');
					$journey = $this->journeys_model->get_basic_journey_info($j_id);
				}
				//we need the j_id by now!!!
				if(!$j_id)
				{
					$this->session->set_flashdata('action', 'New journey could not be created');
					redirect(current_url());
				}

				// update client info (and client table)
				$this->clients_model->update_client_info($j_id, $journey['j_c_id']);

				// get lat lng for postcode
				$this->load->model('postcode_model');
				$this->postcode_model->get_lat_lng($this->input->post('c_post_code'));

				// update journey information
				$this->journeys_model->update_journey_info($journey['j_id']);

				//update the jd_table
				$this->load->model('drugs_model');
				$this->drugs_model->update_journey_drugs_info($j_id);

				// set last datetime update
				$this->journeys_model->set_last_update($j_id, $this->session->userdata('a_id'));

				// set log
				$this->log_model->set('Client information updated for journey #' . $j_id);

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// Load NDTMS library to validate journey
				$this->load->driver('ndtms');
				$this->ndtms->validate_journey($j_id);

				//Define the message to display
				$this->session->set_flashdata('action', 'Journey contact information updated');

				// redirect to current url - don't use current_url as we need to add the journey id if we just made it
				redirect(site_url('admin/journeys/contact/'.$j_id));
			}
		}

		// load dataset
		$this->load->config('datasets');

		// get client info for journey
		if(isset($j_id))
		{
			$client = $this->clients_model->get_client_info($j_id);

			$this->data['journey_info'] = $this->journeys_model->get_journey_info($journey['j_id']);
			$this->data['journey'] =& $journey;
			$this->data['client'] =& $client;
			$this->data['client_info'] = $this->clients_model->get_client_info($j_id);
			$this->load->model('drugs_model');
			$this->data['drugs_info'] = $this->drugs_model->get_journey_drugs_info($journey['j_id']);
		}
		else
		{
			if(isset($client))
			{
				$this->data['client'] =& $client; //all we know about them, if we even know that
				$this->data['client_info'] = $this->clients_model->get_client_info($j_id);
			}
			else
			{
				$this->data['client'] = array();
			}
			$this->data['journey_info'] = array();
			$this->data['journey'] = array();
			$this->data['drugs_info'] = array();
		}
		if(!isset($journey) && $this->input->get('j_type'))
		{
			$this->data['journey']['j_type'] = $this->input->get('j_type');
		}

		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Contact'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Contact' => ''));
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/autosave', 'views/admin/journeys/contact'));
		$this->layout->set_view('/admin/journeys/contact');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	public function consultation($j_id)
	{
		// if no journey can be found
		if( ! $journey = $this->journeys_model->get_basic_journey_info($j_id))
			show_404();

		// load client model
		$this->load->model('clients_model');

		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('j_date_of_triage', '', 'parse_date')
								  ->set_rules('ji_date_referral_received', '', 'parse_date')
								  ->set_rules('ci_fname', '', 'strip_tags|ucfirst')
								  ->set_rules('ci_sname', '', 'strip_tags|ucfirst')
								  ->set_rules('ci_date_of_birth', '', 'parse_date')
								  ->set_rules('ci_gender', '', 'strip_tags')
								  ->set_rules('ci_address', '', 'strip_tags')
								  ->set_rules('ci_post_code', '', 'strip_tags|strtoupper')
								  ->set_rules('ci_tel_home', '', 'strip_tags')
								  ->set_rules('ci_tel_mob', '', 'strip_tags')
								  ->set_rules('ci_catchment_area', '', '')
								  ->set_rules('ji_summary_of_needs', '', 'strip_tags')
								  ->set_rules('ji_referral_received_by', '', 'integer')
								  ->set_rules('ci_disabilities', '', 'strip_tags')
								  ->set_rules('ci_ethnicity', '', 'strip_tags')
								  ->set_rules('ci_nationality', '', 'strip_tags')
								  ->set_rules('ci_sexuality', '', 'strip_tags')
								  ->set_rules('jd_substance_1', '', 'integer')
								  ->set_rules('jd_substance_2', '', 'integer')
								  ->set_rules('ci_consents_to_ndtms', '', 'integer')
								  ->set_rules('ji_rs_id', '', 'integer')
								  ->set_rules('ji_referrers_name', '', 'strip_tags')
								  ->set_rules('ji_referrers_tel', '', 'strip_tags')
								  ->set_rules('ci_pregnant', '', 'integer')
								  ->set_rules('ci_relationship_status', '', 'integer')
								  ->set_rules('ci_parental_status', '', 'integer')
								  ->set_rules('ci_no_of_children', '', 'integer')
								  ->set_rules('ci_access_to_children', '', 'integer')
								  ->set_rules('ji_date_of_drug_treatment', '', 'parse_date')
								  ;

			// find the gp code
			preg_match_all('^\#([A-Za-z0-9]+)^', $this->input->post('ci_gp_code'), $gp_code);
			// set gp code in post array
			$_POST['ci_gp_code'] = @$gp_code[1][0];

			// if form validates
			if($this->form_validation->run())
			{
				// update client info (and client table)
				$this->clients_model->update_client_info($j_id, $journey['j_c_id']);

				// get lat lng for postcode
				$this->load->model('postcode_model');
				$this->postcode_model->get_lat_lng($this->input->post('c_post_code'));

				// update journey information
				$this->journeys_model->update_journey_info($journey['j_id']);

				//update the jd_table
				$this->load->model('agencies_model');
				$this->agencies_model->update_journey_agencies($j_id);

				//update the jd_table
				$this->load->model('drugs_model');
				$this->drugs_model->update_journey_drugs_info($j_id);

				//update the jal table
				$this->load->model('alcohol_model');
				$this->alcohol_model->update_journey_alcohol_info($j_id);

				//any more to update for the consultation? check all fields are saving?

				// set last datetime update
				$this->journeys_model->set_last_update($j_id, $this->session->userdata('a_id'));

				// set log
				$this->log_model->set('Client information updated for journey #' . $j_id);

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// Load NDTMS library to validate journey
				$this->load->driver('ndtms');
				$this->ndtms->validate_journey($j_id);

				//Define the message to display
				$this->session->set_flashdata('action', 'Journey contact information updated');

				// redirect to current url
				redirect(current_url());
			}
		}

		// load dataset
		$this->load->config('datasets');

		// get client info for journey
		$client = $this->clients_model->get_client_info($j_id);

		$this->data['journey_info'] = $this->journeys_model->get_journey_info($journey['j_id']);
		$this->data['journey'] =& $journey;
		$this->data['client'] =& $client;
		$this->load->model('drugs_model');
		$this->data['drugs_info'] = $this->drugs_model->get_journey_drugs_info($journey['j_id']);

		// do you inject alcohol? (gregs note from JS)
		if (!empty($this->data['drugs_info']['jd_substance_1']) && $this->data['drugs_info']['jd_substance_1'] == 7000 && empty($this->data['drugs_info']['jd_substance_1_route'])) {
			$this->data['drugs_info']['jd_substance_1_route'] = 4;
		}

		$this->load->model('alcohol_model');
		$this->data['alcohol_info'] = $this->alcohol_model->get_alcohol_info($journey['j_id']);
		$this->load->model('staff_model');
		$this->data['staff'] = $this->staff_model->get_staff();
		$this->load->model('internal_services_model');
		$this->data['journey_internal_services'] = $this->internal_services_model->get_journey_internal_services($journey['j_id']);
		$this->data['list_of_internal_services'] = $this->internal_services_model->get_internal_services(); //for populating the list to choose from
		$this->load->model('agencies_model');
		$this->data['journey_agencies_involved'] = $this->agencies_model->get_journey_agencies($journey['j_id']);
		$this->data['list_of_agencies'] = $this->agencies_model->get_agencies(); //for populating the list to choose from



		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Consultation'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Consultation' => ''));
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/autosave', 'views/admin/journeys/consultation'));
		$this->layout->set_view('/admin/journeys/consultation');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	public function assessment($j_id)
	{
		// if no journey can be found
		if( ! $journey = $this->journeys_model->get_basic_journey_info($j_id))
			show_404();

		// load client model
		$this->load->model('clients_model');

		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('jd_hep_c_test_date', '', 'parse_date')
								  ->set_rules('jd_hep_c_intervention', '', 'strip_tags')
								  ->set_rules('jd_hep_c_positive', '', 'strip_tags')
								  ->set_rules('jd_hep_b_vac_count', '', 'strip_tags')
								  ->set_rules('jd_hep_b_intervention', '', 'strip_tags')
								  ;

			// if form validates
			if($this->form_validation->run())
			{
				//update the jd_table
				$this->load->model('drugs_model');
				$this->drugs_model->update_journey_drugs_info($j_id);

				//update the ci_table
				$this->clients_model->update_client_info($j_id, $journey['j_c_id']);

				// set last datetime update
				$this->journeys_model->set_last_update($j_id, $this->session->userdata('a_id'));

				// set log
				$this->log_model->set('Journey Assessment information updated for journey #' . $j_id);

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// Load NDTMS library to validate journey
				$this->load->driver('ndtms');
				$this->ndtms->validate_journey($j_id);

				//Define the message to display
				$this->session->set_flashdata('action', 'Journey assessment information updated');

				// redirect to current url
				redirect(current_url());
			}
		}

		// load dataset
		$this->load->config('datasets');

		// get client info for journey
		$client = $this->clients_model->get_client_info($j_id);

		$this->data['journey_info'] = $this->journeys_model->get_journey_info($journey['j_id']);
		$this->data['journey'] =& $journey;
		$this->data['client'] =& $client;
		$this->load->model('drugs_model');
		$this->data['drugs_info'] = $this->drugs_model->get_journey_drugs_info($journey['j_id']);
		$this->load->model('alcohol_model');
		$this->data['alcohol_info'] = $this->alcohol_model->get_alcohol_info($journey['j_id']);



		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Assessment'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Assessment' => ''));
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/autosave', 'views/admin/journeys/assessment'));
		$this->layout->set_view('/admin/journeys/assessment');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	public function client($j_id)
	{
		// if no journey can be found
		if( ! $journey = $this->journeys_model->get_basic_journey_info($j_id))
			show_404();

		// load client model
		$this->load->model('clients_model');

		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('ci_fname', '', 'required|strip_tags|ucfirst')
								  ->set_rules('ci_sname', '', 'required|strip_tags|ucfirst')
								  ->set_rules('ci_date_of_birth', '', 'required|parse_date')
								  ->set_rules('ci_address', '', 'required|strip_tags')
								  ->set_rules('ci_post_code', '', 'required|strip_tags|strtoupper')
								  ->set_rules('ci_tel_home', '', 'strip_tags')
								  ->set_rules('ci_tel_mob', '', 'strip_tags')
								  ->set_rules('ci_gp_name', '', 'strip_tags')
								  ->set_rules('ci_ethnicity', '', '')
								  ->set_rules('ci_nationality', '', '')
								  ->set_rules('ci_pregnant', '', 'integer')
								  ->set_rules('ci_caf_completed', '', 'integer')
								  ->set_rules('ci_relationship_status', '', 'integer')
								  ->set_rules('ci_sexuality', '', '')
								  ->set_rules('ci_mental_health_issues', '', 'integer')
								  ->set_rules('ci_learning_difficulties', '', 'integer')
								  ->set_rules('ci_consents_to_ndtms', '', 'integer')
								  ->set_rules('ci_parental_status', '', 'integer')
								  ->set_rules('ci_access_to_children', '', 'integer')
								  ->set_rules('ci_no_of_children', '', 'integer')
								  ->set_rules('ci_accommodation_need', '', 'integer')
								  ->set_rules('ci_accommodation_status', '', 'integer')
								  ->set_rules('ci_smoker', '', 'integer')
								  ->set_rules('ci_additional_information', '', 'strip_tags');

			// find the gp code
			preg_match_all('^\#([A-Za-z0-9]+)^', $this->input->post('ci_gp_code'), $gp_code);

			// set gp code in post array
			$_POST['ci_gp_code'] = @$gp_code[1][0];

			// if form validates
			if($this->form_validation->run())
			{
				// update client info
				$this->clients_model->update_client_info($j_id, $journey['j_c_id']);

				// set last datetime update
				$this->journeys_model->set_last_update($j_id, $this->session->userdata('a_id'));

				// load postcode model
				$this->load->model('postcode_model');

				// get lat lng for postcode
				$this->postcode_model->get_lat_lng($this->input->post('c_post_code'));

				// set log
				$this->log_model->set('Client information updated for journey #' . $j_id);

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// Load NDTMS library to validate journey
				$this->load->driver('ndtms');
				$this->ndtms->validate_journey($j_id);

				// redirect to current url
				redirect(current_url());
			}
		}

		// load dataset
		$this->load->config('datasets');

		// get client info for journey
		$client = $this->clients_model->get_client_info($j_id);

		$this->data['journey'] =& $journey;
		$this->data['client'] =& $client;

		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Client'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Client' => ''));
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/autosave', 'views/admin/journeys/client'));
		$this->layout->set_view('/admin/journeys/client');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	public function journey($j_id)
	{
		// if no journey can be found
		if( ! $journey = $this->journeys_model->get_basic_journey_info($j_id))
			show_404();

		// if form has been submitted
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			// set rules
			$this->form_validation->set_rules('j_date_of_referral', '', 'parse_date')
								  ->set_rules('ji_date_referral_received', '', 'parse_date')
								  ->set_rules('ji_date_rc_allocated', '', 'parse_date')
								  ->set_rules('j_closed_date', '', 'parse_date')
								  ;


			// if form validates
			if($this->form_validation->run())
			{

				// update journey information
				$this->journeys_model->update_journey_info($journey['j_id']);

				// set last datetime update
				$this->journeys_model->set_last_update($j_id, $this->session->userdata('a_id'));

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// Load NDTMS library to validate journey
				$this->load->driver('ndtms');
				$this->ndtms->validate_journey($j_id);

				// set log
				$this->log_model->set('Journey information updated for journey #' . $j_id);

				// redirect back to same page
				redirect(current_url());
			}
		}

		// load datasets
		$this->load->config('datasets');

		// load models
		$this->load->model(array('referral_sources_model', 'recovery_coaches_model', 'staff_model'));

		// get form values
		$this->data['staff'] = $this->staff_model->get_staff();
		$this->data['referral_sources'] = $this->referral_sources_model->get_referral_sources();
		$this->data['recovery_coaches'] = $this->recovery_coaches_model->get_recovery_coaches();

		// set view items
		$this->data['journey_info'] = $this->journeys_model->get_journey_info($journey['j_id']);
		$this->data['journey'] =& $journey;

		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Journey'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Journey' => ''));
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/autosave', 'views/admin/journeys/journey'));
		$this->layout->set_view('/admin/journeys/journey');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}

	// permanently removes a journey from the db
	public function delete_journey($j_id)
	{
		// only master admin can delete journeys
		$this->auth->check_master_admin_logged_in();

		// if no journey can be found
		if( ! $journey = $this->journeys_model->get_basic_journey_info($j_id))
			show_404();

		// if journey is successfully deleted
		if ($this->journeys_model->delete_journey($journey['j_id']))
		{
			if ($journey['j_type'] == 'C')
			{
				redirect('/admin/journeys');
			}
			elseif ($journey['j_type'] == 'F')
			{
				redirect('/admin/families');
			}
		}
	}

	// ajax request, checks to see whether journey is ndtms valid and returns error codes if it isn't
	public function ndtms_valid($j_id)
	{
		// Load NDTMS library to validate journey
		$this->load->driver('ndtms');
		$status = $this->ndtms->validate_journey($j_id);

		// get validation errors
		$this->data['validation_errors'] = $status['errors'];

		$this->load->vars($this->data);
		$this->load->view('/admin/journeys/ndtms_valid');
	}



	public function ndtms()
	{
		$this->load->driver('ndtms');

		// declare file name variable
		$file_name = '';
		// set primary agency code

		$file_name .= $this->config->config['catchment_area_codes']['Sunderland']['agency_code'] . '-';

		// set date from as YYYYMMDD
		$file_name .= date('Ymd', strtotime(@$_GET['date_from'])) . '-';

		// set date to as YYYYMMDD
		$file_name .= date('Ymd', strtotime(@$_GET['date_to'])) . '-';

		// set type of data contained
		$file_name .= 'TREAT-IN-';

		// set version of Core Data set that was used to produce CSV
		$file_name .= strtoupper($this->ndtms->get_dataset());

		$journeys = $this->journeys_model->get_ndtms_csv();


		// Set headers for direct download streaming
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$file_name}.csv");
		header("Expires: 0");
		header("Pragma: public");
		$fh = fopen('php://output', 'w');

		// Stream directly to the output
		$csv_data = $this->ndtms->get_csv($journeys, TRUE);

		return;
	}



	/*
	// creates ndtms csv file
	function ndtms()
	{
		// load datasets
		$this->load->config('datasets');

		// load csv library
		$this->load->library('csv');

		// load csv model
		$this->load->model('csv_model');

		// declare file name variable
		$file_name = '';

		// set primary agency code
		$file_name .= $this->config->config['catchment_area_codes']['Sunderland']['agency_code'] . '-';

		// set date from as YYYYMMDD
		$file_name .= date('Ymd', strtotime(@$_GET['date_from'])) . '-';

		// set date to as YYYYMMDD
		$file_name .= date('Ymd', strtotime(@$_GET['date_to'])) . '-';

		// set type of data contained
		$file_name .= 'TREAT-IN-';

		// set version of Core Data set that was used to produce CSV
		$file_name .= 'H';

		// make NDTMS export schema the first line of the csv file
		$export_schema = $this->csv_model->get_ndtms_export_schema();

		// get journeys formatted for csv
		$journeys = $this->journeys_model->get_ndtms_csv();

		// declare array for parsed journeys
		$parsed_journeys = array();

		$i = 0;

		// loop over each journey and parse it for ndtms
		foreach($journeys as $j)
		{
			// if client does not consent to data sharing
			if($j['CONSENT'] != 'Y') OR CONSENT = N
			{
				// simply just include the client's id
				$parsed_journeys[$i]['AGNCY'] = $this->config->config['catchment_area_codes'][$j['ci_catchment_area']]['agency_code'];
				$parsed_journeys[$i]['CLIENTID'] = $j['CLIENTID'];
				$parsed_journeys[$i]['CONSENT'] = $j['CONSENT'];
			}
			else
			{
				$parsed_journeys[$i]['FINITIAL'] = $j['FINITIAL'];
				$parsed_journeys[$i]['SINITIAL'] = $j['SINITIAL'];
				$parsed_journeys[$i]['DOB'] = $j['DOB'];
				$parsed_journeys[$i]['SEX'] = $j['SEX'];
				$parsed_journeys[$i]['ETHNIC'] = $j['ETHNIC'];
				$parsed_journeys[$i]['NATION'] = $j['NATION'];
				$parsed_journeys[$i]['REFLD'] = $j['REFLD'];
				$parsed_journeys[$i]['AGNCY'] = $this->config->config['catchment_area_codes']['Sunderland']['agency_code']; // default agency code
				$parsed_journeys[$i]['CLIENT'] = $j['CLIENTID'];
				$parsed_journeys[$i]['CLIENTID'] = $j['CLIENTID'];
				$parsed_journeys[$i]['EPISODID'] = $j['EPISODID'];
				$parsed_journeys[$i]['CONSENT'] = $j['CONSENT'];
				$parsed_journeys[$i]['PREVTR'] = $j['PREVTR'];
				$parsed_journeys[$i]['PC'] = $j['PC'];
				$parsed_journeys[$i]['ACCMNEED'] = $j['ACCMNEED'];
				$parsed_journeys[$i]['PRNTSTAT'] = $j['PRNTSTAT'];
				$parsed_journeys[$i]['DAT'] = $this->config->config['catchment_area_codes'][$j['ci_catchment_area']]['dat_code'];
				$parsed_journeys[$i]['PCT'] = $this->config->config['catchment_area_codes'][$j['ci_catchment_area']]['pct_code'];
				$parsed_journeys[$i]['LA'] = $this->config->config['catchment_area_codes'][$j['ci_catchment_area']]['local_authority_code'];
				$parsed_journeys[$i]['GPPTCE'] = $j['GPPTCE'];
				$parsed_journeys[$i]['DRUG1'] = $j['DRUG1'];
				$parsed_journeys[$i]['DRUG1AGE'] = $j['DRUG1AGE'];
				$parsed_journeys[$i]['ROUTE'] = $j['ROUTE'];
				$parsed_journeys[$i]['DRUG2'] = $j['DRUG2'];
				$parsed_journeys[$i]['DRUG3'] = $j['DRUG3'];
				$parsed_journeys[$i]['RFLS'] = $j['RFLS'];
				$parsed_journeys[$i]['TRIAGED'] = $j['TRIAGED'];
				$parsed_journeys[$i]['CPLANDT'] = $j['j_date_first_assessment'];
				$parsed_journeys[$i]['INJSTAT'] = $j['INJSTAT'];
				$parsed_journeys[$i]['CHILDWTH'] = $j['CHILDWTH'];
				$parsed_journeys[$i]['PREGNANT'] = $j['PREGNANT'];
				$parsed_journeys[$i]['ALCDDAYS'] = $j['ALCDDAYS'];
				$parsed_journeys[$i]['ALCUNITS'] = $j['ALCUNITS'];
				$parsed_journeys[$i]['DUALDIAG'] = $j['DUALDIAG'];
				$parsed_journeys[$i]['HEPCTSTD'] = $j['HEPCTSTD'];
				$parsed_journeys[$i]['HEPCSTAT'] = $j['HEPCSTAT'];
				$parsed_journeys[$i]['HEPCTD'] = '99'; // hep c tested? 99 = Not asked
				$parsed_journeys[$i]['HEPBVAC'] = $j['HEPBVAC'];
				$parsed_journeys[$i]['HEPBSTAT'] = $j['HEPBSTAT'];
				$parsed_journeys[$i]['TOPCC'] = 'N';
				$parsed_journeys[$i]['DISD'] = $j['DISD'];
				$parsed_journeys[$i]['DISRSN'] = $j['DISRSN'];

				if($j['j_date_first_assessment']) // only include modality information if there is an assessment has been done
				{
					$parsed_journeys[$i]['MODAL'] = $j['MODAL'];
					$parsed_journeys[$i]['REFMODDT'] = $j['j_date_first_assessment'];
					$parsed_journeys[$i]['MODID'] = '1';
					$parsed_journeys[$i]['FAOMODDT'] = $j['FAOMODDT'];
					$parsed_journeys[$i]['MODST'] = $j['j_date_first_assessment'];

					if($j['MODEXIT'] != NULL) // if journey exit status has been set
					{
						$parsed_journeys[$i]['MODEND'] = $j['j_date_last_assessment'];
						$parsed_journeys[$i]['MODEXIT'] = $j['MODEXIT'];
					}
				}

				$parsed_journeys[$i]['ALCUSE'] = 'NA';
				$parsed_journeys[$i]['OPIUSE'] = 'NA';
				$parsed_journeys[$i]['CRAUSE'] = 'NA';
				$parsed_journeys[$i]['COCAUSE'] = 'NA';
				$parsed_journeys[$i]['AMPHUSE'] = 'NA';
				$parsed_journeys[$i]['CANNUSE'] = 'NA';
				$parsed_journeys[$i]['OTDRGUSE'] = 'NA';
				$parsed_journeys[$i]['IVDRGUSE'] = 'NA';
				$parsed_journeys[$i]['SHARING'] = 'NA';
				$parsed_journeys[$i]['SHOTHEFT'] = 'NA';
				$parsed_journeys[$i]['DRGSELL'] = 'NA';
				$parsed_journeys[$i]['OTHTHEFT'] = 'NA';
				$parsed_journeys[$i]['ASSAULT'] = 'NA';
				$parsed_journeys[$i]['PSYHSTAT'] = 'NA';
				$parsed_journeys[$i]['PWORK	EDUCAT'] = 'NA';
				$parsed_journeys[$i]['ACUTHPBM'] = 'NA';
				$parsed_journeys[$i]['HRISK	PHSTAT'] = 'NA';
				$parsed_journeys[$i]['QUALLIFE'] = 'NA';
				$parsed_journeys[$i]['HEPCPOS'] = $j['HEPCPOS'];
				$parsed_journeys[$i]['SEXUAL'] = $j['SEXUAL'];
				$parsed_journeys[$i]['EMPSTAT'] = $j['EMPSTAT'];
			}

			$i++;
		}

		$csv_file_dir = $this->csv->create_csv_file($export_schema, $parsed_journeys, $file_name);

		header('Content-Transfer-Encoding: none');
		header('Content-Type: text/csv;');
		header('Content-Disposition: attachment; filename="' . $file_name . '.csv"');

		echo file_get_contents($csv_file_dir);

		unlink($csv_file_dir);
	}
	*/





	// create
	public function performance_output()
	{
		// load datasets
		$this->load->config('datasets');

		// load csv library
		$this->load->library('csv');

		// load csv model
		$this->load->model('csv_model');

		// declare file name variable
		$file_name = 'PERFORMANCE-OUTPUT-';

		// set date from as YYYYMMDD
		$file_name .= date('Ymd', strtotime(@$_GET['date_from'])) . '-';

		// set date to as YYYYMMDD
		$file_name .= date('Ymd', strtotime(@$_GET['date_to'])) . '-';

		$file_name .= time();

		// get performance output export schema
		$export_schema = $this->csv_model->get_performance_output_schema();

		// get journeys formatted for csv
		$journeys = $this->journeys_model->get_performance_output_csv();

		// declare array for parsed journeys
		$parsed_journeys = array();

		$i = 0;

		// loop over each journey and parse it for performance output
		foreach($journeys as $j)
		{
			$parsed_journeys[$i]['Client ID']                      = $j['j_c_id'];
			$parsed_journeys[$i]['Forename']                       = $j['ci_fname'];
			$parsed_journeys[$i]['Surname']                        = $j['ci_sname'];
			$parsed_journeys[$i]['Date of Birth']                  = $j['ci_date_of_birth'];
			$parsed_journeys[$i]['Catchment area']                 = $j['ci_catchment_area'];
			$parsed_journeys[$i]['Local Authority']                 = $j['ci_authority_code'];
			$parsed_journeys[$i]['Case worker']                    = $j['rc_name'];
			$parsed_journeys[$i]['Initial contact']                = $j['j_date_of_referral'];
			$parsed_journeys[$i]['Date of referral']               = $j['j_date_of_referral'];
			$parsed_journeys[$i]['Exit']                           = $j['j_closed_date'];
			$parsed_journeys[$i]['Referral Agency Type']           = @$this->config->config['referral_source_codes'][$j['rs_type']];
			$parsed_journeys[$i]['Referring Agency']               = $j['rs_name'];
			$parsed_journeys[$i]['Date first appointment offered'] = $j['j_date_first_appointment_offered'];
			$parsed_journeys[$i]['Triage Date']                    = $j['j_date_of_triage'];
			$parsed_journeys[$i]['Allocation Date']                = $j['ji_date_rc_allocated'];
			$parsed_journeys[$i]['Last Appointment Date']          = $j['ji_date_last_appt'];
			$parsed_journeys[$i]['First Assessment Date']          = $j['jc_date_ass1'];
			$parsed_journeys[$i]['Exit Reason']                    = @$this->config->config['discharge_reason_codes'][$j['ji_discharge_reason']];
			$parsed_journeys[$i]['Sex']                            = $j['ci_gender'];
			$parsed_journeys[$i]['Sexuality']                      = $j['ci_sexuality'];
			$parsed_journeys[$i]['Religion']                       = $j['ci_religion'];
			$parsed_journeys[$i]['Post Code']                      = $j['ci_post_code'];
			$parsed_journeys[$i]['Ethnicity']                      = @$this->config->config['ethnicity_codes'][$j['ci_ethnicity']];
			$parsed_journeys[$i]['Consent']                        = $j['ci_consents_to_ndtms'];
			$parsed_journeys[$i]['Last Seen']                      = $j['last_seen'];

			$i++;
		}

		// $csv_file_dir = $this->csv->create_csv_file($export_schema, $parsed_journeys, $file_name);
		$csv_file_dir = $this->csv->create_csv_file(array_keys($parsed_journeys[0]), $parsed_journeys, $file_name);

		header('Content-Transfer-Encoding: none');
		header('Content-Type: text/csv;');
		header('Content-Disposition: attachment; filename="' . $file_name . '.csv"');

		echo file_get_contents($csv_file_dir);

		unlink($csv_file_dir);
	}




	/**
	 * Appointment CSV output function
	 */
	public function appointment_output()
	{
		// load datasets
		$this->load->config('datasets');

		// load csv library
		$this->load->library('csv');

		// load csv model
		$this->load->model('csv_model');

		// declare file name variable
		$file_name = 'APPOINTMENT-OUTPUT-';

		// set date from as YYYYMMDD
		$file_name .= date('Ymd', strtotime(@$_GET['date_from'])) . '-';

		// set date to as YYYYMMDD
		$file_name .= date('Ymd', strtotime(@$_GET['date_to'])) . '-';

		$file_name .= time();

		// get appointments output export schema
		$export_schema = $this->csv_model->get_appointment_output_schema();

		// get appointments formatted for csv
		$appointments = $this->journeys_model->get_appointment_output_csv();

		// declare array for parsed appointments
		$parsed_appointments = array();

		$i = 0;

		// loop over each appointment and add columns to new array parsed_appointments
		// 'Client ID', 'Appointment ID', 'Recovery coach', 'Date', 'Time', 'Attended', 'Notes'
		foreach($appointments as $a)
		{
			$parsed_appointments[$i]['Client ID'] = $a['j_c_id'];
			$parsed_appointments[$i]['Appointment ID'] = $a['ja_id'];
			$parsed_appointments[$i]['Case worker'] = $a['rc_name'];
			$parsed_appointments[$i]['Date'] = $a['ja_date'];
			$parsed_appointments[$i]['Time'] = $a['ja_time'];
			$parsed_appointments[$i]['Attended'] = @$this->config->config['yes_no_codes'][$a['ja_attended']];
			$parsed_appointments[$i]['Notes'] = $a['ja_notes'];
			$i++;
		}

		$csv_file_dir = $this->csv->create_csv_file($export_schema, $parsed_appointments, $file_name);

		header('Content-Transfer-Encoding: none');
		header('Content-Type: text/csv;');
		header('Content-Disposition: attachment; filename="' . $file_name . '.csv"');

		echo file_get_contents($csv_file_dir);

		unlink($csv_file_dir);
	}

	/**
	 * Outcomes CSV output function
	 */
	public function outcomes_output()
	{
		$this->load->model('journeys_model');
		$this->journeys_model->get_outcomes_csv();
	}

	public function modal($view = '')
	{
		$this->load->view('admin/journeys/modal/' . $view);
	}
}
