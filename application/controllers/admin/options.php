<?php

class Options extends My_controller
{


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();

		// set global title
		$this->layout->set_title('Options');

		// set global breadcrumb
		$this->layout->set_breadcrumb('Options', '/admin/options');
	}


	function index()
	{
		$this->layout->set_view('/admin/options/index');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	function my_account()
	{
		$this->load->model('admins_model');

		if(@$_POST['email'])
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('email', '', 'required|valid_email|matches[email_confirmed]|check_admin_email_unique[' . $this->session->userdata('a_id') . ']');
			$this->form_validation->set_rules('fname', '', 'required|strip_tags');
			$this->form_validation->set_rules('sname', '', 'required|strip_tags');

			if($this->form_validation->run())
			{
				$this->admins_model->update_admin_details($this->session->userdata('a_id'));

				redirect(current_url());
			}
		}
		elseif(@$_POST['current_password'])
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('current_password', '', 'required|check_admin_current_password[' . $this->session->userdata('a_id') . ']');
			$this->form_validation->set_rules('new_password', '', 'required|password_restrict|matches[new_password_confirmed]');

			if($this->form_validation->run())
			{
				$this->admins_model->update_admin_password($this->session->userdata('a_id'));

				redirect(current_url());
			}
		}
		elseif(@$_POST['options'])
		{
			// turn tooltips on or off
			$options['tooltips'] = @$_POST['options']['tooltips'];

			// loop through each option and assign it's name and value to the users session
			foreach($options as $option_name => $option_value)
				$this->session->set_userdata($option_name, $option_value);

			// update the admins options in the db
			$this->admins_model->update_admin_options($this->session->userdata('a_id'), $options);

			redirect(current_url());
		}

		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/options/my_account'));
		$this->layout->set_title('My account');
		$this->layout->set_breadcrumb('My account');
		$this->layout->set_view('/admin/options/my_account.php');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	function my_account_email()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', '', 'check_admin_email_unique[' . $this->session->userdata('a_id') . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	function my_account_check_current_password()
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('current_password', '', 'check_admin_current_password[' . (int)$this->session->userdata('a_id') . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	function administrators($admin_id = 0)
	{
		//$this->auth->check_master_admin_logged_in();
		if( $this->session->userdata['permissions']['apt_can_manage_admins'] != 1)
		{
			show_error('You do not have permission to manage administrator details.');
		}

		$this->load->model('admins_model');
		$this->load->model('admin_permissions_model');

		if(isset($_GET['apt_id']))
		{
			//attempt to get the permission type from the db
			$permission_type = $this->admin_permissions_model->get_permission_type($_GET['apt_id']);

			//want to delete it?
			if($permission_type && isset($_GET['delete_apt']))
			{
				if($permission_type['apt_id'] == 0)
				{
					$this->session->set_flashdata('action', 'You can not delete the default type.');
					redirect('/admin/options/administrators');
				}
				//delete permission_type
				$this->admin_permissions_model->delete_permission_type($permission_type['apt_id']);
				$this->session->set_flashdata('action', 'Permission type deleted');
				redirect('/admin/options/administrators');
			}
		}

		if($admin = $this->admins_model->get_admin($admin_id))
		{
			$this->data['title'] = 'Update administrator';
		}
		else
		{
			$this->data['title'] = 'Add administrator';
		}

		if(@$_GET['delete'])
		{
			$this->admins_model->delete_admin($_GET['delete']);

			redirect('/admin/options/administrators');
		}

		if(@$_GET['verify'])
		{
			$this->admins_model->verify_admin($_GET['verify']);

			redirect('/admin/options/administrators');
		}

		//if admin form submitted
		if(isset($_POST['fname']))
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('fname', '', 'required|strip_tags|ucfirst');
			$this->form_validation->set_rules('sname', '', 'required|strip_tags|ucfirst');
			$this->form_validation->set_rules('email', '', 'required|valid_email|matches[email_confirmed]|check_admin_email_unique[' . @$admin['a_id'] . ']');
			$this->form_validation->set_rules('password', '', 'password_restrict|matches[password_confirmed]');

			if($this->form_validation->run())
			{
				// turn tooltips on
				$options['tooltips'] = 1;

				$admin_id = $this->admins_model->set_admin(@$admin['a_id'], $options);

				// if not upading existing admin
				if( ! @$admin)
				{
					// create activation token
					$token = md5($admin_id . $this->input->post('email') . $admin_id);

					// create activation link
					$activation_link = $this->config->item('base_url') . '/home/verify?a_id=' . $admin_id . '&token=' . $token;

					// set message containing link to activate account
					$message = 'Dear ' . $this->input->post('fname') . "\n\n";
					$message .= 'An account has been created for you on ' . $this->config->item('site_name') . '\'s client journey management system. In order to use your account you must first activate it.' . "\n\n";
					$message .= 'To active your account, please click this link or copy it into your address bar: ' . $activation_link . "\n\n";

					// load email library
					$this->load->library('email');

					// set email options
					$this->email->from($this->config->item('site_email'), $this->config->item('site_name'))
								->to($this->input->post('email'))
								->subject('Activate your ' . $this->config->item('site_name') . ' account')
								->message($message);

					// send email
					$this->email->send();
				}

				if(@$_POST['master'])
				{
					$this->admins_model->set_master_admin($admin_id);

					redirect('/logout');
				}

				redirect('/admin/options/administrators');
			}
		}

		//if admin permission type form submitted
		if(isset($_POST['apt_name']))
		{
			$this->load->library('form_validation');

			$this->form_validation->set_rules('apt_name', '', 'required|strip_tags');

			if($this->form_validation->run())
			{
				$admin_id = $this->admin_permissions_model->set_permission_type(@$_POST['apt_id']);
				redirect('/admin/options/administrators');
			}
		}

		$this->data['admin'] = &$admin;
		$this->data['permission_type'] = &$permission_type;

		$this->data['admins'] = $this->admins_model->get_admins();
		$this->data['permission_types'] = $this->admin_permissions_model->get_permission_types();

		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/options/administrators'));
		$this->layout->set_title('Administrators');
		$this->layout->set_breadcrumb('Administrators');
		$this->layout->set_view('/admin/options/administrators');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	function admin_email($admin_id = 0)
	{
		$this->load->library('form_validation');

		$this->form_validation->set_rules('email', '', 'check_admin_email_unique[' . $admin_id . ']');

		echo ($this->form_validation->run() ? 'true' : 'false');
	}


	// add, edit and delete referral sources
	function referral_sources($rs_id = 0)
	{
		// load referral sources model
		$this->load->model('referral_sources_model');

		// attempt to get referral source
		$rs = $this->referral_sources_model->get_referral_source($rs_id);

		// has form being submitted?
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			$this->form_validation->set_rules('rs_name', '', 'required|strip_tags')
								  ->set_rules('rs_type', '', 'required|integer');

			if($this->form_validation->run())
			{
				$this->referral_sources_model->set_referral_source(@$rs['rs_id']);

				redirect('/admin/options/referral-sources');
			}
		}

		// has delete button being clicked?
		if(@$_GET['delete'])
		{
			$this->referral_sources_model->set_referral_source_inactive($_GET['delete']);

			redirect('/admin/options/referral-sources');
		}

		// load datasets
		$this->load->config('datasets');

		// get referral source types
		$this->data['rs_types'] = $this->config->config['referral_source_codes'];

		$this->data['rs'] = &$rs;

		// get all active referral sources
		$this->data['referral_sources'] = $this->referral_sources_model->get_referral_sources();

		$total = count($this->data['referral_sources']);

		$this->data['total'] = ($this->data['referral_sources'] ? 'Results ' . $total . ' - ' . $total . ' of ' . $total . '.' : '0 results');

		$this->layout->set_title('Referral sources');
		$this->layout->set_breadcrumb('Referral sources');
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/options/referral_sources'));
		$this->layout->set_view('/admin/options/referral_sources');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	// add, edit and delete case workers/recovery coaches
	public function key_workers($rc_id = 0)
	{
		$this->load->model(array('recovery_coaches_model', 'admins_model', 'job_roles_model'));
		$this->load->config('datasets');

		// attempt to get recovery coach
		$rc = $this->recovery_coaches_model->get_recovery_coach($rc_id);

		// has form being submitted?
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			$this->form_validation->set_rules('rc_name', '', 'required|strip_tags|xss_clean');
			$this->form_validation->set_rules('rc_jr_id', '', 'integer');

			if ($this->form_validation->run())
			{
				$this->recovery_coaches_model->set_recovery_coach(@$rc);
				redirect('/admin/options/key-workers');
			}
		}

		// has delete button being clicked?
		if(@$_GET['delete'])
		{
			$this->recovery_coaches_model->set_recovery_coach_inactive($_GET['delete']);
			redirect('/admin/options/key-workers');
		}

		$this->data['rc'] = &$rc;
		$this->data['admins'] = $this->admins_model->get_admins();

		// Get list of job roles
		$this->data['job_roles'] = db_dropdown($this->job_roles_model->get_active(), 'jr_id', 'jr_title', '(None)');

		// get all active recovery coaches
		$this->data['recovery_coaches'] = $this->recovery_coaches_model->get_recovery_coaches();


		$total = count($this->data['recovery_coaches']);

		$this->data['total'] = ($this->data['recovery_coaches'] ? 'Results ' . $total . ' - ' . $total . ' of ' . $total . '.' : '0 results');

		$this->layout->set_title('Key Workers');
		$this->layout->set_breadcrumb('Key Workers');
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/options/key_workers'));
		$this->layout->set_view('/admin/options/key_workers');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	// add, edit and delete dna reasons
	public function dna_reasons($dr_id = 0)
	{
		// load dna reasons model
		$this->load->model('dna_reasons_model');

		// attempt to get dna reason
		$dr = $this->dna_reasons_model->get_dna_reason($dr_id);

		// has form being submitted?
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			$this->form_validation->set_rules('dr_name', '', 'required|strip_tags');

			if($this->form_validation->run())
			{
				$this->dna_reasons_model->set_dna_reason(@$dr['dr_id']);

				redirect('/admin/options/dna-reasons');
			}
		}

		// has delete button being clicked?
		if(@$_GET['delete'])
		{
			$this->dna_reasons_model->set_dna_reason_inactive($_GET['delete']);

			redirect('/admin/options/dna-reasons');
		}

		$this->data['dr'] = &$dr;

		// get all active recovery coaches
		$this->data['dna_reasons'] = $this->dna_reasons_model->get_dna_reasons();

		$total = count($this->data['dna_reasons']);

		$this->data['total'] = ($this->data['dna_reasons'] ? 'Results ' . $total . ' - ' . $total . ' of ' . $total . '.' : '0 results');

		$this->layout->set_title('DNA reasons');
		$this->layout->set_breadcrumb('DNA reasons');
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/options/dna_reasons'));
		$this->layout->set_view('/admin/options/dna_reasons');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}



	// add, edit and delete key worker job roles
	public function job_roles($jr_id = 0)
	{
		// load dna reasons model
		$this->load->model('job_roles_model');

		// attempt to get dna reason
		$jr = $this->job_roles_model->get($jr_id);

		// has form being submitted?
		if ($this->input->post())
		{
			// load form validation library
			$this->load->library('form_validation');

			$this->form_validation->set_rules('jr_title', '', 'required|strip_tags');

			if ($this->form_validation->run())
			{
				$jr_data = array(
					'jr_title' => $this->input->post('jr_title'),
					'jr_active' => (int) $this->input->post('jr_active'),
				);

				if ($jr_id)
				{
					$action = $this->job_roles_model->update($jr_id, $jr_data);
				}
				else
				{
					$action = $this->job_roles_model->insert($jr_data);
				}

				if ($action)
				{
					$this->session->set_flashdata('action', 'Job role saved successfully.');
				}

				redirect('/admin/options/job-roles');
			}
		}

		// Delete one?
		if ($this->input->get('delete'))
		{
			// Deactivate
			$jr_id = (int) $this->input->get('delete');
			$this->job_roles_model->update($jr_id, array('jr_active' => 0));
			redirect('/admin/options/job-roles');
		}

		$this->data['jr'] = $jr;

		// get all active recovery coaches
		$this->data['job_roles'] = $this->job_roles_model->get_active();

		$total = count($this->data['job_roles']);

		$this->data['total'] = ($this->data['job_roles'] ? 'Results ' . $total . ' - ' . $total . ' of ' . $total . '.' : '0 results');

		$this->layout->set_title('Job Roles');
		$this->layout->set_breadcrumb('Job Roles');
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/options/job_roles'));
		$this->layout->set_view('/admin/options/job_roles');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




	// add, edit and delete event types
	public function event_types()
	{
		// load event types model
		$this->load->model('event_types_model');

		$et_id = $this->input->get('et_id');
		$ec_id = $this->input->get('ec_id');

		$et = $this->event_types_model->get_event_type($et_id);
		$ec = $this->event_types_model->get_event_category($ec_id);


		// Check if a form is being submitted to add/update a type/category
		if ($this->input->post())
		{
			$this->load->library('form_validation');

			// Add/Update something and redirect

			switch ($this->input->post('target'))
			{

				case 'et':

					$this->form_validation->set_rules('et_name', '', 'required|strip_tags');

					if ($this->form_validation->run())
					{
						$this->event_types_model->set_event_type($this->input->post('et_id'));
					}

				break;

				case 'ec':

					$this->form_validation->set_rules('ec_name', '', 'required|strip_tags');

					if ($this->form_validation->run())
					{
						$this->event_types_model->set_event_category($this->input->post('ec_id'));
					}

				break;
			}

			redirect('/admin/options/event-types');
		}


		// Check if something is to be deleted.
		if ($this->input->get('delete'))
		{
			// $et_id and $ec_id already retrieved from GET if set.
			// The only thing we need to do is to determine *WHAT* is to be deleted, and delete it.

			if ($et_id)
			{
				$this->event_types_model->set_event_type_inactive($et_id);
			}
			elseif ($ec_id)
			{
				$this->event_types_model->set_event_category_inactive($ec_id);
				$this->event_types_model->update_cache('event_categories', NULL);
			}

			redirect('/admin/options/event-types');
		}

		$this->data['et'] = $et;
		$this->data['ec'] = $ec;

		// get all active event types
		$this->data['event_types'] = $this->event_types_model->get_event_types();
		$this->data['event_categories'] = $this->event_types_model->get_event_categories();

		$total = count($this->data['event_types']);

		$this->data['total'] = ($this->data['event_types'] ? 'Results ' . $total . ' - ' . $total . ' of ' . $total . '.' : '0 results');

		$this->layout->set_title('Event types');
		$this->layout->set_breadcrumb('Event types');
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/options/event_types'));
		$this->layout->set_view('/admin/options/event_types');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




	// add, edit and delete event types
	public function staff($s_id = 0)
	{
		// load event types model
		$this->load->model('staff_model');

		// attempt to get recovery coach
		$s = $this->staff_model->get_staff_member($s_id);

		// has form being submitted?
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			$this->form_validation->set_rules('s_name', '', 'required|strip_tags');

			if($this->form_validation->run())
			{
				$this->staff_model->set_staff_member(@$s['s_id']);

				redirect('/admin/options/staff');
			}
		}

		// has delete button being clicked?
		if(@$_GET['delete'])
		{
			$this->staff_model->set_staff_member_inactive($_GET['delete']);

			redirect('/admin/options/staff');
		}

		$this->data['s'] = &$s;

		// get all active event types
		$this->data['staff'] = $this->staff_model->get_staff();

		$total = count($this->data['staff']);

		$this->data['total'] = ($this->data['staff'] ? 'Results ' . $total . ' - ' . $total . ' of ' . $total . '.' : '0 results');

		$this->layout->set_title('Staff');
		$this->layout->set_breadcrumb('Staff');
		$this->layout->get_js(array('plugins/jquery.validate', 'views/admin/options/staff'));
		$this->layout->set_view('/admin/options/staff');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	public function internal_services($is_id = 0)
	{
		// load event types model
		$this->load->model('internal_services_model');

		// attempt to get recovery coach
		$is = $this->internal_services_model->get_internal_service($is_id);

		// has form being submitted?
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			$this->form_validation->set_rules('is_name', '', 'required|strip_tags');

			if($this->form_validation->run())
			{
				$this->internal_services_model->set_internal_service(@$is['is_id']);

				redirect('/admin/options/internal-services');
			}
		}

		// has delete button being clicked?
		if(@$_GET['delete'])
		{
			$this->internal_services_model->set_internal_service_inactive($_GET['delete']);

			redirect('/admin/options/internal-services');
		}

		$this->data['is'] = &$is;

		// get all active event types
		$this->data['internal_services'] = $this->internal_services_model->get_internal_services();

		$total = count($this->data['internal_services']);

		$this->data['total'] = ($this->data['internal_services'] ? 'Results ' . $total . ' - ' . $total . ' of ' . $total . '.' : '0 results');

		$this->layout->set_title('Internal Services');
		$this->layout->set_breadcrumb('Internal Services');
		$this->layout->get_js(array('plugins/jquery.validate', 'views/admin/options/internal-services'));
		$this->layout->set_view('/admin/options/internal_services');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}

	public function usage_log($page = 0)
	{
		$this->load->library('pagination');
		$config['base_url'] = '/admin/options/usage-log/';
		$config['total_rows'] = $total = $this->log_model->get_total_log(); // get total log
        $config['suffix'] = '?' . http_build_query($this->input->get());
        $config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);
        $config['uri_segment'] = 4;
        $this->pagination->initialize($config);

		$this->data['log'] = $this->log_model->get_log($page, $config['per_page']);

		$this->data['total'] = ($this->data['log'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['log'])) . ' of ' . $total . '.' : '0 results');

		$this->data['total_export'] = $total;

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc');
        $this->data['sort'] .= '&amp;pp=' . $_GET['pp'];
        $this->data['sort'] .= '&amp;date_to=' . @$_GET['date_to'];
        $this->data['sort'] .= '&amp;date_from=' . @$_GET['date_from'];
        $this->data['sort'] .= '&amp;c_id=' . @$_GET['c_id'];
        $this->data['sort'] .= '&amp;j_id=' . @$_GET['j_id'];

		$this->data['pp'] = array('10', '20', '50', '100', '200');

		$this->layout->set_title('Usage log');
		$this->layout->set_breadcrumb('Usage log');
		//$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/options/event_types'));
		$this->layout->set_view('/admin/options/usage_log');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




	/**
	 * Index page to show the outcome criteria lists
	 */
	public function assessment_criteria($acl_id = 0)
	{
		$this->load->model('assessments_model');
		$this->load->config('datasets');

		// Attempt to get the list entry
		$acl = $this->assessments_model->get_acl($acl_id);

		// Has form being submitted?
		if ($_POST)
		{
			$this->load->library('form_validation');
			$this->form_validation->set_rules('acl_name', '', 'required|strip_tags|xss_clean');
			$this->form_validation->set_rules('acl_type', '', 'required|strip_tags|alpha|xss_clean');

			if ($this->form_validation->run())
			{
				$acl_criteria = $this->input->post('acl_criteria');

				foreach ($acl_criteria['num'] as $idx => $num)
				{
					if ( ! empty($acl_criteria['outcome'][$idx]))
					{
						$criteria[$num] = trim($acl_criteria['outcome'][$idx]);
					}
				}
				ksort($criteria);

				$acl_data = array(
					'acl_j_id' => NULL,
					'acl_name' => $this->input->post('acl_name'),
					'acl_type' => $this->input->post('acl_type'),
					'acl_criteria' => $criteria,
				);

				if ($acl_id == 0)
				{
					$res = $this->assessments_model->insert_acl($acl_data);
					if ($res) $this->session->set_flashdata('action', 'List has been added.');
				}
				else
				{
					$res = $this->assessments_model->update_acl($acl_id, $acl_data);
					if ($res) $this->session->set_flashdata('action', 'List has been updated.');
				}

				redirect('/admin/options/assessment-criteria');
			}
		}

		// Has delete button being clicked?
		if ($this->input->get('delete'))
		{
			$this->assessments_model->delete_acl($this->input->get('acl_id'));
			redirect('/admin/options/assessment-criteria');
		}

		$this->data['acl'] =& $acl;

		// get all criteria lists
		$this->data['lists'] = $this->assessments_model->get_all_acl();

		$total = count($this->data['lists']);

		$this->data['total'] = NULL;

		$this->layout->set_title('Assessment Criteria');
		$this->layout->set_breadcrumb('Assessment Criteria');
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/options/assessment_criteria'));
		$this->layout->set_view('/admin/options/assessment_criteria');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	function terms_and_conditions()
	{

		$this->load->model('terms_model');

		if($_POST)
		{
			$terms_and_conditions = array(
				'datetime_set' => time(),
				'value' => $this->input->post('value'),
				'last_changes' => $this->input->post('last_changes'),
				'on' => $this->input->post('on'),
			);

			$this->terms_model->update_terms($terms_and_conditions);

			$this->session->set_flashdata('action', 'Terms and conditions saved');

			redirect(current_url());
		}

		$this->data['terms_and_conditions'] = $this->terms_model->get_terms();

		$this->layout->set_title('Terms and conditions');
		$this->layout->set_breadcrumb('Terms and conditions');
		$this->layout->set_js(array('tiny_mce/jquery.tinymce', 'views/admin/options/terms_and_conditions'));
		$this->layout->set_view('/admin/options/terms_and_conditions');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}


	// add, edit and delete agencies
	function agencies($ag_id = 0)
	{
		// load agency sources model
		$this->load->model('agencies_model');

		// attempt to get referral source
		$ag = $this->agencies_model->get_agency($ag_id);

		// has form being submitted?
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');

			$this->form_validation->set_rules('ag_name', '', 'required|strip_tags')
								  ->set_rules('ag_agt_id', '', 'required|integer');

			if($this->form_validation->run())
			{
				$this->agencies_model->set_agency(@$ag['ag_id']);

				redirect('/admin/options/agencies');
			}
		}

		// has delete button being clicked?
		if(@$_GET['delete'])
		{
			$this->agencies_model->set_agency_inactive($_GET['delete']);

			redirect('/admin/options/agencies');
		}

		// load datasets
		$this->load->config('datasets');

		// get agency types
		$this->data['ag_types'] = $this->agencies_model->get_agency_types();

		$this->data['ag'] = &$ag;

		// get all active agencies
		$this->data['agencies'] = $this->agencies_model->get_agencies();

		$total = count($this->data['agencies']);

		$this->data['total'] = ($this->data['agencies'] ? 'Results ' . $total . ' - ' . $total . ' of ' . $total . '.' : '0 results');

		$this->layout->set_title('Agencies');
		$this->layout->set_breadcrumb('Agencies');
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/options/agencies'));
		$this->layout->set_view('/admin/options/agencies');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




}
