<?php

class Activities extends MY_controller {
	
	
	public function __construct()
	{
		parent::__construct();
		
		// check the admin is logged in
		$this->auth->check_admin_logged_in();
		
		// load support groups model
		$this->load->model('support_groups_model');
		
		$this->layout->set_title('Activities');
		$this->layout->set_breadcrumb('Activities', '/admin/activites');
	}

	// index of all suport groups
	public function index()
	{
		// if attempting to delete support group
		if(@$_GET['delete'])
		{
			// delete support group	
			$this->support_groups_model->delete_support_group(@$_GET['delete']);
			
			// "refresh" page
			redirect('/admin/activities');
		}
		
		$this->data['support_groups'] = $this->support_groups_model->get_support_groups();
		
		$total = count($this->data['support_groups']);
		
		$this->data['total'] = ($this->data['support_groups'] ? 'Results ' . $total . ' - ' . $total . ' of ' . $total . '.' : '0 results');
		
		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc');

		$this->layout->set_view('/admin/activities/index');	
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}
	
	// add or update a support group
	public function set($sp_id = 0)
	{
		// if support group exists
		if($support_group = $this->support_groups_model->get_support_group($sp_id))
		{
			// set update title
			$title = 'Update ' . $support_group['sp_name'];
		}
		else
		{
			// set add title
			$title = 'Add new activity';
		}
		
		// if form submitted
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');
			
			// set rules
			$this->form_validation->set_rules('sp_name', '', 'required|strip_tags|ucfirst')
								  ->set_rules('sp_description', '', 'required')
								  ->set_rules('sp_default_day', '', '')
								  ->set_rules('time_hour', '', 'integer')
								  ->set_rules('time_minute', '', 'integer')
								  ->set_rules('sp_default_location', '', 'strip_tags|ucfirst');
								  
			// if form validates
			if($this->form_validation->run())
			{
				// if support group set ok
				if($sp_id = $this->support_groups_model->set_support_group($support_group))
				{
					// redirect to support group info page	
					redirect('/admin/activities/info/' . $sp_id);
				}
			}
		}
				
		// load datasets
		$this->load->config('datasets');
		
		$this->data['title'] =& $title;
		$this->data['support_group'] =& $support_group;
				
		$this->layout->set_title($title);
		$this->layout->set_breadcrumb($title);
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/activities/set'));
		$this->layout->set_view('/admin/activities/set');	
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());		
	}
	
	// support group information page
	public function info($sp_id, $page = 0)
	{
		// if support group does not exist
		if( ! $support_group = $this->support_groups_model->get_support_group($sp_id, $format = true))
			show_404();
		
		// get total session
		$total = $this->support_groups_model->get_total_sessions($support_group['sp_id']);
		
		// load pagination library
		$this->load->library('pagination');
		
		// pagination config
		$config['base_url'] = '/admin/activities/info/' . $support_group['sp_id'];
		$config['total_rows'] = $total;
		$config['per_page'] = 20;
		$config['uri_segment'] = 5;
		
		// initialise pagination
		$this->pagination->initialize($config);
		
		$this->data['sessions'] = $this->support_groups_model->get_sessions($support_group['sp_id'], $page, $config['per_page']);
		
		$this->data['total'] = ($this->data['sessions'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['sessions'])) . ' of ' . $total . '.' : '0 results');

		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc');

		$this->data['support_group'] =& $support_group;

		$this->layout->set_title($support_group['sp_name']);
		$this->layout->set_breadcrumb($support_group['sp_name']);
		$this->layout->set_js(array());
		$this->layout->set_view('/admin/activities/info');	
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());	
	}
	
	// individual session for a support group
	public function session($sp_id, $sps_id = 0, $c_id = 0)
	{
		// if support group does not exist
		if( ! $support_group = $this->support_groups_model->get_support_group($sp_id, $format = true))
			show_404();

		if($session = $this->support_groups_model->get_session($sps_id))
		{
			// if attempting to delete session
			if(@$_GET['delete'])
			{
				$this->support_groups_model->delete_session($sps_id);
				
				// redirect to support group info page
				redirect('/admin/activities/info/' . $support_group['sp_id']);
			}
			
			if(@$_GET['remove_client'])
			{
				$this->support_groups_model->remove_client_from_register($sps_id, $_GET['remove_client']);
				
				// "refresh" page
				redirect(current_url());
			}
			
			$title = 'Update session';
		}
		else
		{
			$title = 'Add new session';
		}
		
		// if attempting to add client to register
		if($c_id)
		{
			// add client to register
			$this->support_groups_model->add_client_to_register($sps_id, $c_id);
			
			// "refresh" page
			redirect('/admin/activities/session/' . $sp_id . '/' . $sps_id);
		}
		
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');
			
			// set rules
			$this->form_validation->set_rules('sps_date', '', 'required|parse_date')
								  ->set_rules('sps_notes', '', 'strip_tags')
								  ->set_rules('sps_location', 'strip_tags|ucfirst');
								  
			// if form validates
			if($this->form_validation->run())
			{
				// if session set ok
				if($sps_id = $this->support_groups_model->set_session($support_group['sp_id'], $session))
				{
					// "refresh" page
					redirect('/admin/activities/session/' . $support_group['sp_id'] . '/' . $sps_id);					
				}				
			}
		}
		
		// load datasets
		$this->load->config('datasets');
		
		// get register of clients
		$this->data['register'] = $this->support_groups_model->get_register(@$session['sps_id']);
		
		$total = count($this->data['register']);
		
		$this->data['total'] = ($this->data['register'] ? 'Results ' . $total . ' - ' . $total . ' of ' . $total . '.' : '0 results');
		
		$this->data['support_group'] =& $support_group;
		$this->data['session'] =& $session;
		
		$this->layout->set_title(array($support_group['sp_name'], $title));
		$this->layout->set_breadcrumb(array($support_group['sp_name'] => '/admin/activities/info/' . $support_group['sp_id'], $title => ''));
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/activities/session'));
		$this->layout->set_view('/admin/activities/session');	
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}

	// sets client's attedance at a session
	public function set_attendance($sps_id, $c_id, $attendance)
	{
		// set attendance
		$this->support_groups_model->set_attendance($sps_id, $c_id, $attendance);
		
		// echo html
		echo '<div class="attended">' . ($attendance == 1 ? 'Yes' : 'No') . '</div>';
	}
	
	//
	public function get_last_register($sp_id, $sps_id)
	{
		// get the register of the last session for a single support groups
		$register = $this->support_groups_model->get_last_register($sp_id, $sps_id);
		
		// if we are going to use this register
		if(@$_GET['use_register'])
		{
			// loop over each client in the register
			foreach($register as $c)
			{
				// add client to register
				$this->support_groups_model->add_client_to_register($sps_id, $c['c_id']);
			}
			
			// set added notification
			$this->session->set_flashdata('action', 'Clients added to register');
			
			// redirect to session info page
			redirect('/admin/activities/session/' . $sp_id . '/' . $sps_id);
		}
		
		$this->data['register'] =& $register;
		
		$this->load->vars($this->data);
		$this->load->view('/admin/activities/get_last_register');
	}
	
	
	// CSV output of groups and attendees
	function groups_output()
	{
		// load datasets
		$this->load->config('datasets');
		
		// load csv library
		$this->load->library('csv');
		
		// load csv model
		$this->load->model('csv_model');
		
		// declare file name variable
		$file_name = 'ACTIVITIES-OUTPUT-';
		
		$file_name .= time();
		
		// get groups output export schema
		$export_schema = $this->csv_model->get_support_groups_output_schema();
		
		// get groups formatted for csv
		$groupsinfo = $this->support_groups_model->get_groups_output_csv();
		
		// declare array for parsed appointments
		$parsed_groups = array();
		
		$i = 0;
				
		// loop over each group info and add columns to new array parsed_groups
		// 'Group ID', 'Group Name', 'Date', 'Client ID', 'Name', 'DOB', 'Gender', 'Postcode'
		foreach($groupsinfo as $g)
		{
			$parsed_groups[$i]['Group ID'] = $g['sp_id'];
			$parsed_groups[$i]['Group Name'] = $g['sp_name'];
			$parsed_groups[$i]['Date'] = $g['sps_datetime'];
			$parsed_groups[$i]['Client ID'] = $g['c_id'];
			$parsed_groups[$i]['Name'] = $g['c_name'];
			$parsed_groups[$i]['DOB'] = $g['c_date_of_birth'];
			$parsed_groups[$i]['Gender'] = $g['c_gender'];
			$parsed_groups[$i]['Postcode'] = $g['c_post_code'];
			$i++;
		}
		
		$csv_file_dir = $this->csv->create_csv_file($export_schema, $parsed_groups, $file_name);	
		
		header('Content-Transfer-Encoding: none');
		header('Content-Type: text/csv;');
		header('Content-Disposition: attachment; filename="' . $file_name . '.csv"');
			
		echo file_get_contents($csv_file_dir);
		
		unlink($csv_file_dir);
	}
	
}