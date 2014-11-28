<?php

class Internal_services_model extends CI_Model
{
	
	
	public function __construct()
	{
		parent::__construct();
	}
		
	// get active 
	public function get_internal_services($cache = 1)
	{
		// if we want to try and return a cache
		if($cache)
		{
			if($is = $this->cache->get('internal_service'))
				return $is;
		}		
		
		$sql = 'SELECT
					is_id,
					is_name
				FROM
					internal_services
				WHERE
					is_active = 1
				ORDER BY
					is_name ASC ';
				
		$is = $this->db->query($sql)->result_array();	
		
		// cache results forever
		$this->cache->save('internal_services', $is, 9999999999);
		
		return $is;
	}
	
	// get single staff_member information
	function get_internal_service($is_id)
	{
		if($is_id)
		{
			$sql = 'SELECT
						*
					FROM
						internal_services
					WHERE
						is_id = "' . (int)$is_id . '"
						AND is_active = 1 ';
						
			return $this->db->query($sql)->row_array();
		}
		
		return false;
	}
	
	function get_journey_internal_services($j_id)
	{
		//get internal services
		$sql = "SELECT
					is_id,
					is_name
				FROM
					j2is
				LEFT JOIN
					internal_services ON j2is_is_id = is_id
				WHERE
					j2is_j_id = '". (int)$j_id ."'
				;";
		return $this->db->query($sql)->result_array();
	}
	
	// either adds or updates information
	public function set_internal_service($is = FALSE)
	{
		// if s is not false
		if($is != FALSE)
		{
			// update staff_member information
			$sql = 'UPDATE
						internal_services
					SET
						is_name = ?
					WHERE
						is_id = "' . (int)$is['is_id'] . '";';

			// set action message
			$this->session->set_flashdata('action', 'Internal service updated');
		}
		else
		{
			// insert new information
			$sql = 'INSERT INTO
						internal_services
					SET
						is_name = ? ';
			
			// set action message
			$this->session->set_flashdata('action', 'New internal service added');
		}
		
		// commit the query
		$this->db->query($sql, array($this->input->post('is_name')));
		
		// delete cache file					 
		$this->_delete_cache();
	}	
	
	// marks as inactive, effectively deleting it from the system
	public function set_internal_service_inactive($is_id)
	{
		$sql = 'UPDATE
					internal_services
				SET
					is_active = 0
				WHERE
					is_id = "' . (int)$is_id . '";';
					
		$this->db->query($sql);
		
		// delse cache file						 
		$this->_delete_cache();
		
		$this->session->set_flashdata('action', 'Internal service deleted');
	}
	
	// delete cache file
	protected function _delete_cache()
	{
		$this->cache->delete('internal_services');
	}
	
	
}