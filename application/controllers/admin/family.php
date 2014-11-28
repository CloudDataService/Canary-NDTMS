<?php

class Family extends MY_Controller
{


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();

        //get permissions, used by the journeys_nav view
        $this->data['permissions'] = $this->session->userdata('permissions');

		// load journeys model
		$this->load->model(array('journeys_model', 'family_model', 'clients_model'));

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

		// if notes form has been submitted
		if ($_POST)
		{
			$this->family_model->set_journey_family_info($journey['j_id']);
			$this->log_model->set('Family notes updated for journey #' . $j_id);
			redirect(current_url());
		}

		$this->load->config('datasets');

		$family_info = $this->family_model->get_journey_family_info($j_id);
		$family_clients = $this->family_model->get_clients($j_id);

		$this->data['family_clients'] =& $family_clients;
		$this->data['family_info'] =& $family_info;
		$this->data['journey'] =& $journey;

		$this->layout->set_title(array('Journey #' . $journey['j_type'] . $journey['j_id'], 'Family'));
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id'], 'Family' => ''));
		$this->layout->set_css('datepicker');
		$this->layout->set_js(array('plugins/jquery.validate', 'views/admin/journeys/family'));
		$this->layout->set_view('/admin/journeys/family');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




	function add_family_member()
	{
		// load form validation library
		$this->load->library('form_validation');
		$this->load->config('datasets');

		// set rules
		$this->form_validation->set_rules('f_fname', '', 'required|strip_tags|ucfirst')
							  ->set_rules('f_sname', '', 'required|strip_tags|ucfirst')
							  ->set_rules('f_date_of_birth', '', 'strip_tags');

		// if form validates
		if ($this->form_validation->run())
		{
			// get current array of family members
			$family_members = $this->family_model->get_family_members($this->input->post('j_id'));
			
			// set array for new family member
			$new_family_member = array(
				'fname' => $this->input->post('f_fname'),
				'sname' => $this->input->post('f_sname'),
				'date_of_birth' => $this->input->post('f_date_of_birth'),
				'rel_type' => $this->input->post('f_rel_type'),
			);
			
			// push new family member to the end of the family members array
			$family_members[] = $new_family_member;
			
			// save family members
			$this->family_model->save_family_members($this->input->post('j_id'), $family_members);
			
			// set log
			$this->log_model->set('Family member added to journey #' . $this->input->post('j_id'));
			
			// get key for new family member
			$key = (count($family_members) - 1);
			
			// HTML code of row to return
			$html = '<tr class="row no_click">
				<td></td>
				<td>' . $new_family_member['fname'] . '</td>
				<td>' . $new_family_member['sname'] . '</td>
				<td>' . $new_family_member['date_of_birth'] . '</td>
				<td>' . $this->config->item($new_family_member['rel_type'], 'relative_types') . '</td>
				<td><a href="/admin/family/promote_member/' . $this->input->post('j_id') . '/' . $key . '"><img src="/img/icons/forward_green.png" alt="Start own journey" title="Start own journey"/></a></td>
				<td><a href="/admin/family/delete_family_member/' . $this->input->post('j_id') . '/' . $key .'" class="delete"><img src="/img/icons/cross.png" alt="Delete" title="Delete family member"/></a></td>
				</tr>';
			// echo the html
			echo $html;
		}
	}


	public function delete_family_member($j_id, $key)
	{
		// get family members
		$family_members = $this->family_model->get_family_members($j_id);

		// delete family member from array
		unset($family_members[$key]);

		// set log
		$this->log_model->set('Family member deleted from journey #' . $j_id);

		$this->family_model->save_family_members($j_id, $family_members);
		
		redirect('admin/family/index/'. $j_id); //please
	}




	/**
	 * Add an existing "main" client as a member of the family
	 *
	 * @param int $j_id		Family journey ID
	 * @param int $c_id		Primary client ID to add as a member
	 * @author CR
	 */
	public function add_client($j_id, $c_id)
	{
		if ( ! $j_id && ! $c_id)
		{
			show_error('No journey and client IDs present.');
		}

		if ( ! $journey = $this->journeys_model->get_full_journey_info($j_id))
		{
			show_404();
		}

		if ( ! $client = $this->clients_model->get_client($c_id))
		{
			show_404();
		}

		$this->load->config('datasets');

		$this->data['journey'] =& $journey;
		$this->data['client'] =& $client;

		if ($this->input->post())
		{
			// Add the client as a family member
			$data = array(
				'fc_j_id' => $j_id,
				'fc_j_c_id' => $journey['j_c_id'],
				'fc_rel_type' => $this->input->post('fc_rel_type'),
				'fc_c_id' => $c_id,
			);

			if ($this->family_model->add_client($data))
			{
				// OK
				$rel_type_title = $this->config->item($data['fc_rel_type'], 'relative_types');
				$this->log_model->set("Client #$c_id added as a $rel_type_title family member of client #{$journey['j_c_id']} to journey #{$journey['j_type']}{$j_id}");
				$this->session->set_flashdata('action', 'Family member has been added');
				redirect('/admin/family/index/' . $j_id);
			}
			else
			{
				show_error('Could not add client as a family member.');
			}
		}

		$this->layout->set_title('Journey #' . $journey['j_type'] . $journey['j_id']);
		$this->layout->set_breadcrumb(array('Journey #' . $journey['j_type'] . $journey['j_id'] => '/admin/journeys/info/' . $journey['j_id']));
		$this->layout->set_breadcrumb(array('Family' => '/admin/family/index/' . $journey['j_id']));
		$this->layout->set_breadcrumb('Add client as family member');
		$this->layout->set_view('/admin/journeys/family/add_client');
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




	/**
	 * Remove an existing client as a member from a family
	 *
	 * @param int $j_id		Journey to remove the client from
	 * @param int $c_id		Client ID to remove from journey
	 */
	public function delete_client($j_id, $c_id)
	{
		if ( ! $j_id && ! $c_id)
		{
			show_error('No journey and client IDs present.');
		}

		if ( ! $journey = $this->journeys_model->get_full_journey_info($j_id))
		{
			show_error('No journey ID ' . $j_id);
		}

		$this->load->config('datasets');

		if ($this->family_model->delete_client($j_id, $c_id))
		{
			// OK
			$this->log_model->set("Family member client #$c_id removed from journey #{$journey['j_type']}{$j_id}");
		}
		else
		{
			show_error('Could not remove client from family.');
		}
		
		
		redirect('admin/family/index/'. $j_id); //please
	}




	/**
	 * Promote an existing standard "family member" to a client and journey.
	 *
	 * Creates a new client and journey for the person, then deletes the "old" entry
	 * in the family member list array.
	 *
	 * @param int $j_id		Family journey the member is on
	 * @param int $key		Array key of family members array
	 * @author
	 */
	public function promote_member($j_id, $key)
	{
		if ( ! $j_id && empty($key))
		{
			show_error('No journey and client IDs present.');
		}

		if ( ! $journey = $this->journeys_model->get_full_journey_info($j_id))
		{
			show_404();
		}

		// Get current array of family members to see if the member is valid
		$family_members = $this->family_model->get_family_members($j_id);

		$family_member = element($key, $family_members);
		if ( ! $family_member)
		{
			show_error('Member "' . $key . '" not found in family.');
		}

		// Store the journey ID and family member key in the session to retrieve
		// later on successful creation of client + journey.
		$this->session->set_userdata('delete_family_member', array(
			'j_id' => $j_id,
			'key' => $key,
		));

		/*
		  <td><?php echo $family_member['fname']; ?></td>
					<td><?php echo $family_member['sname']; ?></td>
					<td><?php echo $family_member['date_of_birth']; ?></td>
					*/

		// Go to new journey page and pass in the client name
		$params = array(
			'j_type' => 'F',
			'c_fname' => $family_member['fname'],
			'c_sname' => $family_member['sname'],
			'c_date_of_birth' => $family_member['date_of_birth'],
			'fc_rel_type' => $family_member['rel_type'],
			'from_j_id' => $j_id,
		);

		redirect(site_url('/admin/journeys/new-journey?') . http_build_query($params));
	}




}
