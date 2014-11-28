<?php

class Families extends MY_controller {


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();

		$this->load->model('journeys_model');
		$this->load->helper(array('journey', 'date'));

		// set default js for journeys section
		$this->layout->set_js(array('views/admin/families/default'));

		$this->layout->set_title('Families');
		$this->layout->set_breadcrumb('Families', '/admin/families');
	}


	public function index($page = 0)
	{
		if( $this->session->userdata('can_read_family') == 0 || $this->session->userdata['permissions']['apt_can_read_family'] != 1 )
		{
			show_error('You do not have permission to view family journeys.');
		}
		$_GET['j_type'] = 'F';

		$this->load->model('recovery_coaches_model');

		$total = $this->journeys_model->get_total_journeys();

		$this->load->library('pagination');

		$config['base_url'] = '/admin/families/index/';

		$config['total_rows'] = $total;

		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);

		$config['uri_segment'] = 4;

		$config['suffix'] = '?' . @http_build_query($this->input->get());

		$this->pagination->initialize($config);

		$this->data['journeys'] = $this->journeys_model->get_journeys($page, $config['per_page']);

		$this->data['total'] = ($this->data['journeys'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['journeys'])) . ' of ' . $total . '.' : '0 results');

		$this->data['total_export'] = $total;

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'] . '&amp;j_c_id=' . @$_GET['j_c_id'] . '&amp;c_sname=' . @$_GET['c_sname'] . '&amp;date_from=' . @$_GET['date_from'] . '&amp;date_to=' . @$_GET['date_to'] . '&amp;c_catchment_area=' . @$_GET['c_catchment_area'];

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->data['key_workers'] = $this->recovery_coaches_model->get_recovery_coaches();

		// load datasets
		$this->load->config('datasets');

		//$this->layout->set_css('datepicker');
		$this->layout->set_js(array('views/admin/journeys/index'));
		$this->layout->set_view('/admin/families/index');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




	public function new_journey($c_id = 0)
	{
		// load models
		$this->load->model(array('clients_model', 'referral_sources_model', 'recovery_coaches_model'));

		// get client if c_id set
		if($c_id)
			$client = $this->clients_model->get_client($c_id, $format = FALSE);

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
			if($this->form_validation->run())
			{
				// get catchment area
				$catchment_area = $this->clients_model->get_catchment_area($this->input->post('c_post_code'));

				// load postcode model
				$this->load->model('postcode_model');

				// get lat lng for postcode
				$this->postcode_model->get_lat_lng($this->input->post('c_post_code'));

				// start new journey and return its id
				$j_id = $this->families_model->start_new_journey(@$client['c_id'], $catchment_area);

				// Update journey cache
				$this->journeys_model->cache_journey($j_id);

				// set log
				$this->log_model->set('New journey #' . $j_id . ' created');

				// redirect to journey info page
				redirect('/admin/families/info/' . $j_id);
			}
		}

		// load datasets
		$this->load->config('datasets');

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
		if( ! $journey = $this->families_model->get_full_journey_info($j_id))
			show_404();

		// if trying to get appointment
		if(@$_GET['ja_id'])
		{
			// get single appointment information
			$appt = $this->journeys_model->get_appointment($_GET['ja_id']);

			// if attempting to delete appointment
			if(@$_GET['delete'])
			{
				// delete appointment information from db
				$this->journeys_model->delete_appointment($journey['j_id'], $_GET['ja_id']);

				// load ndtms library
				$this->load->library('ndtms');

				// check to see if journey is NDTMS valid
				$this->ndtms->check_is_valid($journey['j_id']);

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

				// redirect to current journey info page
				redirect(current_url());
			}
		}

		// load datasets
		$this->load->config('datasets');

		$this->data['journey'] =& $journey;

		$this->layout->set_title('Client #' . $journey['j_c_id']);
		$this->layout->set_breadcrumb('Client #' . $journey['j_c_id']);
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/info'));
		$this->layout->set_view('/admin/journeys/info/index');
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

				// load ndtms library
				$this->load->library('ndtms');

				// check to see if journey is NDTMS valid
				$this->ndtms->check_is_valid($j_id);

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

		$this->layout->set_title(array('Journey #' . $journey['j_id'], 'Client'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Client' => ''));
		$this->layout->set_js(array('plugins/jquery.validate','views/admin/journeys/client'));
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
			// update journey information
			$this->journeys_model->update_journey_info($journey['j_id']);

			// set last datetime update
			$this->journeys_model->set_last_update($j_id, $this->session->userdata('a_id'));

			// load ndtms library
			$this->load->library('ndtms');

			// check to see if journey is NDTMS valid
			$this->ndtms->check_is_valid($j_id);

			// set log
			$this->log_model->set('Journey information updated for journey #' . $j_id);

			// redirect back to same page
			redirect(current_url());
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

		$this->layout->set_title(array('Journey #' . $journey['j_id'], 'Journey'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Journey' => ''));
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/journey'));
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
		if($this->journeys_model->delete_journey($journey['j_id']))
		{
			redirect('/admin/journeys');
		}
	}

	// ajax request, checks to see whether journey is ndtms valid and returns error codes if it isn't
	public function ndtms_valid($j_id)
	{
		// load NDTMS library
		$this->load->library('ndtms');

		// get validation errors
		$this->data['validation_errors'] = $this->ndtms->get_validation_errors($j_id);

		$this->load->vars($this->data);
		$this->load->view('/admin/journeys/ndtms_valid');
	}

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
			if($j['CONSENT'] != 'Y')
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
			$parsed_journeys[$i]['Client ID'] = $j['j_c_id'];
			$parsed_journeys[$i]['Forename'] = $j['ci_fname'];
			$parsed_journeys[$i]['Surname'] = $j['ci_sname'];
			$parsed_journeys[$i]['Date of Birth'] = $j['ci_date_of_birth'];
			$parsed_journeys[$i]['Catchment area'] = $j['ci_catchment_area'];
			$parsed_journeys[$i]['Recovery coach'] = $j['rc_name'];
			$parsed_journeys[$i]['Initial contact'] = $j['j_date_of_referral'];
			$parsed_journeys[$i]['Initial referral'] = $j['j_date_of_referral'];
			$parsed_journeys[$i]['Exit'] = $j['j_closed_date'];
			$parsed_journeys[$i]['Referral Agency Type'] = @$this->config->config['referral_source_codes'][$j['rs_type']];
			$parsed_journeys[$i]['Referring Agency'] = $j['rs_name'];
			$parsed_journeys[$i]['Date first appointment offered'] = $j['j_date_first_appointment_offered'];
			$parsed_journeys[$i]['Triage Date'] = $j['j_date_of_triage'];
			$parsed_journeys[$i]['Allocation Date'] = $j['ji_date_rc_allocated'];
			$parsed_journeys[$i]['Exit Reason'] = @$this->config->config['discharge_reason_codes'][$j['ji_discharge_reason']];
			$parsed_journeys[$i]['Sex'] = $j['ci_gender'];
			$parsed_journeys[$i]['Post Code'] = $j['ci_post_code'];
			$parsed_journeys[$i]['Ethnicity'] = @$this->config->config['ethnicity_codes'][$j['ci_ethnicity']];
			$parsed_journeys[$i]['Consent'] = $j['ci_consents_to_ndtms'];

			$i++;
		}

		$csv_file_dir = $this->csv->create_csv_file($export_schema, $parsed_journeys, $file_name);

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
			$parsed_appointments[$i]['Recovery coach'] = $a['rc_name'];
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

}