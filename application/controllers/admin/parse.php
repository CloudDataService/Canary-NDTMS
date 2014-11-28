<?php

class Parse extends MY_controller {
	
	
	public function __construct()
	{
		parent::__construct();
		
		// check the admin is logged in
		$this->auth->check_admin_logged_in();
		
		// load datasets
		$this->load->config('datasets');
	}
	
	
	// gets all admins and adds them as a staff member
	public function set_admin_as_staff()
	{
		$sql = 'SELECT
					a_id,
					CONCAT(a_fname, " ", a_sname) AS a_name
				FROM	
					admins
				WHERE
					a_active = 1;';
					
		$admins = $this->db->query($sql)->result_array();
		
		foreach($admins as $a)
		{
			$sql = 'INSERT INTO
						staff
					SET
						s_id = ?,
						s_name = ?;';
			
			$this->db->query($sql, array($a['a_id'],
										 $a['a_name']));
										 
			echo "Inserted: <strong>" . $a['a_name'] . "</strong> into staff<br />";
		}
		
		echo "Done";
	}
	
	
	public function gps()
	{
		$this->load->model('gps_model');
		
		$this->gps_model->parse_csv('gps.csv');
	}
	
	public function postcodes()
	{
		$sql = 'SELECT
					c_post_code
				FROM
					clients ';
					
		$postcodes = $this->db->query($sql)->result_array();
			
		// load postcode model
		$this->load->model('postcode_model');
		
		foreach($postcodes as $pc)
		{
			$this->postcode_model->get_lat_lng($pc['c_post_code']);
		}
		
	}
	
	public function check_ndtms_valid()
	{
		$sql = 'SELECT
					j_id
				FROM
					journeys';
				
		$journeys = $this->db->query($sql)->result_array();
		
		// load ndtms library
		$this->load->library('ndtms');
			
		foreach($journeys as $j)
		{
			// check to see if journey is NDTMS valid
			if($this->ndtms->check_is_valid($j['j_id']))
			{
				echo $j['j_id'] . ' is valid<br />';
			}
			else
			{
				echo $j['j_id'] . ' is invalid<br />';
			}
		}
	}
	
	public function set_triage_dates()
	{
		$sql = 'SELECT
					j_id
				FROM
					journeys';
				
		$journeys = $this->db->query($sql)->result_array();
					
		foreach($journeys as $j)
		{
			$j_id = $j['j_id'];
			
			/// get first appointment date
			$sql = 'SELECT
						DATE(ja_datetime) ja_date
					FROM
						journey_appointments
					WHERE
						ja_j_id = "' . (int)$j_id . '"
					ORDER BY
						ja_datetime ASC
					LIMIT 1;';
				
			// commit query and return row	
			$first_appointment = $this->db->query($sql)->row_array();
			
			// get triage date
			$sql = 'SELECT
						DATE(ja_datetime) ja_date
					FROM
						journey_appointments
					WHERE
						ja_j_id = "' . (int)$j_id . '"
						AND ja_attended = 1
					ORDER BY
						ja_datetime ASC
					LIMIT 1;';
				
			// commit query and return row	
			$triage_appointment = $this->db->query($sql)->row_array();
			
			// update journey with triage date
			$sql = 'UPDATE
						journeys
					SET
						j_date_first_appointment = ?,
						j_date_of_triage = ?
					WHERE
						j_id = "' . (int)$j_id . '";';
			
			// commit query			
			$this->db->query($sql, array(@$first_appointment['ja_date'], @$triage_appointment['ja_date']));
		}
	}
	
	
	public function set_client_catchment_area()
	{		
		// load clients model
		$this->load->model('clients_model');
	
		$sql = 'SELECT
					c_id,
					c_post_code
				FROM
					clients';
					
		if($clients = $this->db->query($sql)->result_array())
		{
			foreach($clients as $c)
			{
				$catchment_area = $this->clients_model->get_catchment_area($c['c_post_code']);

				$sql = 'UPDATE
							clients
						SET
							c_catchment_area = ?
						WHERE
							c_id = "' . (int)$c['c_id'] . '";';
				
				$this->db->query($sql, $catchment_area);				
			}
			
		}
		
		$sql = 'SELECT
					ci_j_id,
					ci_post_code
				FROM
					clients_info';
					
		if($clients = $this->db->query($sql)->result_array())
		{
			foreach($clients as $ci)
			{
				$catchment_area = $this->clients_model->get_catchment_area($ci['ci_post_code']);

				$sql = 'UPDATE
							clients_info
						SET
							ci_catchment_area = ?
						WHERE
							ci_j_id = "' . (int)$ci['ci_j_id'] . '";';
				
				$this->db->query($sql, $catchment_area);				
			}
		}
	}
	
	
	// fix injection status codes
	public function injecting_status()
	{		
		$sql = 'SELECT
					jd_j_id,
					jd_injecting
				FROM
					journey_drugs
				WHERE
					jd_injecting IS NOT NULL;';
					
		$journeys = $this->db->query($sql)->result_array();
		
		foreach($journeys as $j)
		{
			if($j['jd_injecting'] == '2')
			{
				$jd_injecting = 'N';
			}
			else
			{
				$jd_injecting = NULL;
			}
			
			$sql = 'UPDATE
						journey_drugs
					SET
						jd_injecting = ?
					WHERE
						jd_j_id = "' . (int)$j['jd_j_id'] . '";';
						
			$this->db->query($sql, $jd_injecting);			
		}
	}
	
	
	public function fix_exit_status()
	{
		$sql = 'SELECT
					ji_j_id,
					ji_exit_status
				FROM
					journeys_info';
				
		$journeys = $this->db->query($sql)->result_array();
					
		foreach($journeys as $j)
		{
			$exit_status = NULL;
			
			if($j['ji_exit_status'] == '2')
			{
				$exit_status = 'B';
			}
			elseif($j['ji_exit_status'] == '1')
			{
				$exit_status = 'A';
			}
			
			$sql = 'UPDATE
						journeys_info
					SET
						ji_exit_status = ?
					WHERE
						ji_j_id = "' . (int)$j['ji_j_id'] . '";';
						
			$this->db->query($sql, $exit_status);				
		}
	}
	
	public function fix_employment_status()
	{
		$sql = 'SELECT
					ci_j_id,
					ci_employment_status
				FROM
					clients_info';
					
		if($clients = $this->db->query($sql)->result_array())
		{
			foreach($clients as $ci)
			{
				if($ci['ci_employment_status'] == '4' || $ci['ci_employment_status'] == '3')
				{
					$sql = 'UPDATE
								clients_info
							SET
								ci_employment_status = 12
							WHERE
								ci_j_id = "' . (int)$ci['ci_j_id'] . '";';
					
					$this->db->query($sql);							
				}
			}
			
		}
	}
		
	public function fix_drug_codes()
	{
		$sql = 'SELECT
					jd_j_id,
					jd_substance_1,
					jd_substance_2,
					jd_substance_3
				FROM
					journey_drugs;';
					
		$journeys = $this->db->query($sql)->result_array();
		
		foreach($journeys as $j)
		{
			$s1 = $this->_drug_group_code($j['jd_substance_1']);
			$s2 = $this->_drug_group_code($j['jd_substance_2']);
			$s3 = $this->_drug_group_code($j['jd_substance_3']);
			
			$sql = 'UPDATE
						journey_drugs
					SET
						jd_substance_1 = ?,
						jd_substance_2 = ?,
						jd_substance_3 = ?
					WHERE
						jd_j_id = ' . $j['jd_j_id'];
						
			$this->db->query($sql, array($s1, $s2, $s3));
		}
	}
	
	protected function _drug_group_code($code)
	{
		if($substance = @$this->config->config['drug_group_codes'][$code])
		{
		
			foreach($this->config->config['drug_codes'] as $key => $drug)
			{
				if($substance == $drug)
				{
					return $key;
				}
			}
		
		}
		
		return NULL;
	}
}