<?php

class Alcohol_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	// get all alcohol information unformatted for specific journey
	public function get_alcohol_info($j_id)
	{
		$sql = 'SELECT
					*
				FROM
					journey_alcohol
				WHERE
					jal_j_id = "' . (int)$j_id . '"';
					
		return $this->db->query($sql)->row_array();	
	}
	
	// FUNCTION update alcomahol depending on what was posted
	public function update_journey_alcohol_info($j_id)
	{		
		//what have we got to update with?
		$jal_data = array();
		if($this->input->post('jal_safe_at_home') !== FALSE)
		{
			$jal_data['jal_safe_at_home'] = ($this->input->post('jal_safe_at_home') ? $this->input->post('jal_safe_at_home') : NULL);
		}
		if($this->input->post('jal_others_safe') !== FALSE)
		{
			$jal_data['jal_others_safe'] = ($this->input->post('jal_others_safe') ? $this->input->post('jal_others_safe') : NULL);
		}
		if($this->input->post('jal_affect_bills') !== FALSE)
		{
			$jal_data['jal_affect_bills'] = ($this->input->post('jal_affect_bills') ? $this->input->post('jal_affect_bills') : NULL);
		}
		if($this->input->post('jal_incurred_debt') !== FALSE)
		{
			$jal_data['jal_incurred_debt'] = ($this->input->post('jal_incurred_debt') ? $this->input->post('jal_incurred_debt') : NULL);
		}
		if($this->input->post('jal_avg_daily_units') !== FALSE)
		{
			// Ensure values of 0 are saved, and not interpreted as emtpy/null
			$jal_avg_daily_units = $this->input->post('jal_avg_daily_units');
			if (strlen($jal_avg_daily_units) > 0)
			{
				$jal_data['jal_avg_daily_units'] = (int) $jal_avg_daily_units;
			}
			else
			{
				$jal_data['jal_avg_daily_units'] = NULL;
			}
		}
		if($this->input->post('jal_last_28_drinking_days') !== FALSE)
		{
			$jal_data['jal_last_28_drinking_days'] = $this->input->post('jal_last_28_drinking_days');
		}
		if($this->input->post('jal_age_started_drinking') !== FALSE)
		{
			$jal_data['jal_age_started_drinking'] = ($this->input->post('jal_age_started_drinking') ? $this->input->post('jal_age_started_drinking') : NULL);
		}
		
		if(!empty($jal_data))
		{
			//check we have some jd already
			$sql = "SELECT jal_j_id FROM journey_alcohol WHERE jal_j_id = '". (int)$j_id ."'; ";
			$jal_exists = $this->db->query($sql)->num_rows();
			
			if($jal_exists == 1)
			{
				// update journeys table
				$sql = $this->db->update_string('journey_alcohol', $jal_data, 'jal_j_id = "' . (int)$j_id . '";');
				$this->db->query($sql);
				
				//done
				$this->session->set_flashdata('action', 'Journey alcohol information updated');
				return true;
			}
			else
			{
				// update journeys table
				$jal_data['jal_j_id'] = (int)$j_id;
				$sql = $this->db->insert_string('journey_alcohol', $jal_data);
				$this->db->query($sql);
				
				//done
				$this->session->set_flashdata('action', 'Journey alcohol information added');
				return true;
				
			}
		}
		
		$this->session->set_flashdata('action', 'No data given for Journey alcohol information');
		return true;
	}
	
	
	public function set_alcohol_info($j_id, $alcohol_info)
	{
		// if alcohol info already exists
		if($alcohol_info)
		{
			// update alcohol info
			$sql = 'UPDATE
						journey_alcohol
					SET
						jal_safe_at_home = ?,
						jal_others_safe = ?,
						jal_affect_bills = ?,
						jal_incurred_debt = ?,
						jal_avg_daily_units = ?,
						jal_last_28_drinking_days = ?,
						jal_age_started_drinking = ?
					WHERE
						jal_j_id = "' . (int)$j_id . '"';		
		}
		else
		{
			// add new alcohol info	
			$sql = 'INSERT INTO
						journey_alcohol
					SET
						jal_j_id = "' . (int)$j_id . '",
						jal_safe_at_home = ?,
						jal_others_safe = ?,
						jal_affect_bills = ?,
						jal_incurred_debt = ?,
						jal_avg_daily_units = ?,
						jal_last_28_drinking_days = ?,
						jal_age_started_drinking = ?;';
		}
		
		// Ensure values of 0 are saved, and not interpreted as emtpy/null
		$jal_avg_daily_units = $this->input->post('jal_avg_daily_units');
		if (strlen($jal_avg_daily_units) > 0)
		{
			$jal_avg_daily_units = (int) $jal_avg_daily_units;
		}
		else
		{
			$jal_avg_daily_units = NULL;
		}
		
		// commit query
		$this->db->query($sql, array(($this->input->post('jal_safe_at_home') ? $this->input->post('jal_safe_at_home') : NULL),
									 ($this->input->post('jal_others_safe') ? $this->input->post('jal_others_safe') : NULL),
									 ($this->input->post('jal_affect_bills') ? $this->input->post('jal_affect_bills') : NULL),
									 ($this->input->post('jal_incurred_debt') ? $this->input->post('jal_incurred_debt') : NULL),
									 $jal_avg_daily_units,
									 $this->input->post('jal_last_28_drinking_days'),
									 ($this->input->post('jal_age_started_drinking') ? $this->input->post('jal_age_started_drinking') : NULL)));	
		
		$this->session->set_flashdata('action', 'Alcohol information updated');
	}
	
}