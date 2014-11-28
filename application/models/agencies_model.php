<?php

class Agencies_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	// get active referral_sources
	public function get_agencies($cache = 0)
	{
		// if we want to try and return a cache
		if($cache)
		{
			if($agencies = $this->cache->get('agencies'))
				return $agencies;
		}		
		
		// else hit the db	
		$sql = 'SELECT
					ag_id,
					ag_name,
					ag_agt_id
				FROM
					agencies
				WHERE
					ag_active = 1
				ORDER BY
					ag_name ASC ';
				
		$agencies =  $this->db->query($sql)->result_array();
		
		// cache results forever
		$this->cache->save('agencies', $agencies, 9999999999);
		
		return $agencies;	
	}
	
	// get single referral_source information
	function get_agency($ag_id)
	{
		if($ag_id)
		{
			$sql = 'SELECT
						*
					FROM
						agencies
					WHERE
						ag_id = "' . (int)$ag_id . '"
						AND ag_active = 1 ';
			
			return $this->db->query($sql)->row_array();
		}
		
		return false;
	}
	
	// either adds or updates referral_source information
	public function set_agency($ag = FALSE)
	{
		// if referral source is not false
		if($ag != FALSE)
		{
			// update referral source information
			$sql = 'UPDATE
						agencies
					SET
						ag_name = ?,
						ag_agt_id = ?
					WHERE
						ag_id = "' . (int) $ag . '";';

			// set action message
			$this->session->set_flashdata('action', 'Agency information updated');
		}
		else
		{
			// insert new referral_source information
			$sql = 'INSERT INTO
						agencies
					SET
						ag_name = ?,
						ag_agt_id = ?';
			
			// set action message
			$this->session->set_flashdata('action', 'New agency information added');
		}
		
		// commit the query
		$this->db->query($sql, array($this->input->post('ag_name'),
									 $this->input->post('ag_agt_id')));
		
		// delete cache file						 
		$this->_delete_cache();
	}	
	
	// marks the referral_source as inactive, effectively deleting it from the system
	public function set_agency_inactive($ag_id)
	{
		$sql = 'UPDATE
					agencies
				SET
					ag_active = 0
				WHERE
					ag_id = "' . (int)$ag_id . '";';
					
		$this->db->query($sql);
		
		// delete cache file						 
		$this->_delete_cache();
		
		$this->session->set_flashdata('action', 'Agency deleted');
	}
	
	// delete cache file
	protected function _delete_cache()
	{
		$this->cache->delete('agencies');
	}
	
	
	/*
	 * Gets the types of agencies from the database
	 * @author GM
	 */
	public function get_agency_types($cache = 1)
	{
		// if we want to try and return a cache
		if($cache)
		{
			if($agency_types = $this->cache->get('agency_types'))
				return $agency_types;
		}		
		
		// else hit the db	
		$sql = 'SELECT
					agt_id,
					agt_text
				FROM
					agency_types
				WHERE
					1
				ORDER BY
					agt_text ASC ';
				
		$types =  $this->db->query($sql)->result_array();
		$agency_types = array();
		foreach($types as $type)
		{
			$agency_types[ $type['agt_id'] ] = $type['agt_text'];
		}
		
		// cache results forever
		$this->cache->save('agency_types', $agency_types, 9999999999);
		
		return $agency_types;
	}
	
	public function get_journey_agencies($j_id)
	{
		$sql = "SELECT
					ag_id,
					ag_name,
					j2ag_date,
					DATE_FORMAT(j2ag_date, '%d/%m/%Y') AS j2ag_date_format
				FROM
					j2ag
				LEFT JOIN
					agencies ON j2ag_ag_id = ag_id
				WHERE
					j2ag_j_id = '". (int)$j_id ."'
				;";
		return $this->db->query($sql)->result_array();
	}
	
	public function update_journey_agencies($j_id)
	{
		if($this->input->post('j2ag') !== FALSE)
		{
			//remove all existing client disabilities
			$sql = "DELETE FROM j2ag WHERE j2ag_j_id = '". (int)$j_id ."';";
			$this->db->query($sql);

			//add all the ones listed
			foreach($this->input->post('j2ag') as $ag_id => $ag_date)
			{
				$ag_date = parse_date($ag_date);
				$j2ag_data[] = "('". (int)$j_id ."', '". (int)$ag_id ."', '". $ag_date ."')";
			}
			$sql = "INSERT
						INTO j2ag
						(j2ag_j_id, j2ag_ag_id, j2ag_date)
					VALUES
						". implode(', ', $j2ag_data) ."
					;";
			$this->db->query($sql);
		}
	}
}