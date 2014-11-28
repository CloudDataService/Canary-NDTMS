<?php

class Clients extends MY_controller {
	
	
	public function __construct()
	{
		parent::__construct();
		
		// check the admin is logged in
		$this->auth->check_admin_logged_in();
		
		// load clients model
		$this->load->model('clients_model');
		
		$this->layout->set_title('Clients');
		$this->layout->set_breadcrumb('Clients', '/admin/clients');
	}


	public function index($page = 0)
	{
		$total = $this->clients_model->get_total_clients();
		
		$this->load->library('pagination');
		
		$config['base_url'] = '/admin/clients/index/';
		
		$config['total_rows'] = $total;
		
		$config['per_page'] = (@$_GET['pp'] ? (int)$_GET['pp'] : $_GET['pp'] = 20);
		
		$config['uri_segment'] = 4;
		
		$this->pagination->initialize($config);
		
		$this->data['clients'] = $this->clients_model->get_clients($page, $config['per_page']);
		
		$this->data['total'] = ($this->data['clients'] ? 'Results ' . ($page + 1) . ' - ' . ($page + count($this->data['clients'])) . ' of ' . $total . '.' : '0 results');
		
		$this->data['total_export'] = $total;
		
		$this->data['sort'] = '&amp;sort=' . (@$_GET['sort'] == 'asc' ? 'desc' : 'asc') . '&amp;pp=' . $_GET['pp'] . '&amp;c_id=' . @$_GET['c_id'] . '&amp;c_fname=' . @$_GET['c_fname'] . '&amp;c_sname=' . @$_GET['c_sname'] . '&amp;c_date_of_birth=' . @$_GET['c_date_of_birth'] . '&amp;c_post_code=' . @$_GET['c_post_code'];
				
		$this->data['pp'] = array('10', '20', '50', '100', '200');	
		
		$this->layout->set_css('datepicker');
		$this->layout->set_js(array('plugins/jquery.datepicker', 'views/admin/clients/index'));
		$this->layout->set_view('/admin/clients/index');	
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}
	
	
	public function info($c_id)
	{
		// if client does not exist
		if( ! $client = $this->clients_model->get_client($c_id))
			show_404();
		
		$this->load->config('datasets');
		
		// set coords for map
		$coords = array('lat' => $client['pc_lat'],
						'lng' => $client['pc_lng']);
			
		// get all journeys	
		$this->data['journeys'] = $this->clients_model->get_journeys($client['c_id']);
		
		// set total journeys
		$this->data['total'] = ($this->data['journeys'] ? (count($this->data['journeys']) == 1 ? '1 journey' : count($this->data['journeys']) . ' journeys') : '0 results');
		
		// get support groups client has attended
		$this->data['support_groups'] = $this->clients_model->get_support_groups($client['c_id']);
		
		// Get family clients linked to client via journeys
		$this->data['family_clients'] = $this->clients_model->get_family_clients($client['c_id']);		
		
		// set total support groups
		$this->data['total_sg'] = ($this->data['support_groups'] ? (count($this->data['support_groups']) == 1 ? '1 support group' : count($this->data['support_groups']) . ' support groups') : '0 results');
		
		$this->data['coords'] =& $coords;
		
		$this->data['client'] =& $client;
				
		$this->layout->set_title($client['c_fname'] . ' ' . $client['c_sname']);
		$this->layout->set_breadcrumb($client['c_fname'] . ' ' . $client['c_sname']);
		$this->layout->set_external_js('https://maps.google.com/maps/api/js?sensor=false');
		$this->layout->set_js('views/admin/clients/info');
		$this->layout->set_view('/admin/clients/info');	
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());			
	}
	
	// add/update a client
	public function set($c_id = 0)
	{		
		if($client = $this->clients_model->get_client($c_id, $format = false))
		{
			$title = 'Update ' . $client['c_fname'] . ' ' . $client['c_sname'];	
		}
		else
		{
			$title = 'Add client';
		}
		
		if($_POST)
		{
			// load form validation library
			$this->load->library('form_validation');
			
			// set rules
			$this->form_validation->set_rules('c_fname', '', 'required|strip_tags|ucfirst')
								  ->set_rules('c_sname', '', 'required|strip_tags|ucfirst')
								  ->set_rules('c_gender', '', 'required|integer')
								  ->set_rules('c_date_of_birth', '', 'required|parse_date|strip_tags')
								  ->set_rules('c_address', '', 'strip_tags')
								  ->set_rules('c_post_code', '', 'required|strip_tags|strtoupper')
								  ->set_rules('c_tel_home', '', 'strip_tags')
								  ->set_rules('c_tel_mob', '', 'strip_tags');
								  
			// if form validates
			if($this->form_validation->run())
			{
				// set client
				$c_id = $this->clients_model->set_client($client);
				
				// load postcode model
				$this->load->model('postcode_model');
				
				// get lat lng for postcode
				$this->postcode_model->get_lat_lng($this->input->post('c_post_code'));
				
				// set log
				$this->log_model->set(($client == true ? 'Client #' . $c_id . ' updated.' : 'Client #' . $c_id . ' added.'));
				
				// if ajax request
				if($this->input->post('ajax'))
				{
					// echo client id
					echo $c_id; 
					
					// exit script
					exit;
				}
								
				// redirect to client info page
				redirect('/admin/clients/info/' . $c_id);
			}
		}
		
		// load datasets
		$this->load->config('datasets');
		
		$this->data['title'] =& $title;
		
		$this->data['client'] =& $client;
		
		$this->layout->set_title($title);
		$this->layout->set_breadcrumb($title);
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/clients/set'));
		$this->layout->set_view('/admin/clients/set');	
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}
	
	
	public function delete_client($c_id)
	{
		// only master admin can delete journeys
		$this->auth->check_master_admin_logged_in();
		
		// if client does not exist
		if( ! $client = $this->clients_model->get_client($c_id))
			show_404();
			
		// if client has any journeys
		if($journeys = $this->clients_model->get_journeys($client['c_id']))
		{
			// load journeys model
			$this->load->model('journeys_model');
			
			// loop over each one and delete it from the db
			foreach($journeys as $j)
			{
				// delete journey
				$this->journeys_model->delete_journey($j['j_id']);
			}
		}
		
		// delete client from db
		if($this->clients_model->delete_client($client))
		{			
			// redirect to clients index page
			redirect('/admin/clients');			
		}
	}
	
}