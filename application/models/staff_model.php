<?php

class Staff_model extends CI_Model
{
	
	
	public function __construct()
	{
		parent::__construct();
	}
		
	// get active staff
	public function get_staff($cache = 1)
	{
		// if we want to try and return a cache
		if($cache)
		{
			if($staff = $this->cache->get('staff'))
				return $staff;
		}		
		
		$sql = 'SELECT
					s_id,
					s_name
				FROM
					staff
				WHERE
					s_active = 1
				ORDER BY
					s_name ASC ';
				
		$staff = $this->db->query($sql)->result_array();	
		
		// cache results forever
		$this->cache->save('staff', $staff, 9999999999);
		
		return $staff;
	}
	
	// get single staff_member information
	function get_staff_member($s_id)
	{
		if($s_id)
		{
			$sql = 'SELECT
						*
					FROM
						staff
					WHERE
						s_id = "' . (int)$s_id . '"
						AND s_active = 1 ';
						
			return $this->db->query($sql)->row_array();
		}
		
		return false;
	}
	
	// either adds or updates staff_member information
	public function set_staff_member($s = FALSE)
	{
		// if s is not false
		if($s != FALSE)
		{
			// update staff_member information
			$sql = 'UPDATE
						staff
					SET
						s_name = ?
					WHERE
						s_id = "' . (int)$s['s_id'] . '";';

			// set action message
			$this->session->set_flashdata('action', 'Staff member updated');
		}
		else
		{
			// insert new staff_member information
			$sql = 'INSERT INTO
						staff
					SET
						s_name = ? ';
			
			// set action message
			$this->session->set_flashdata('action', 'New staff member added');
		}
		
		// commit the query
		$this->db->query($sql, array($this->input->post('s_name')));
		
		// delete cache file					 
		$this->_delete_cache();
	}	
	
	// marks the staff_member as inactive, effectively deleting it from the system
	public function set_staff_member_inactive($s_id)
	{
		$sql = 'UPDATE
					staff
				SET
					s_active = 0
				WHERE
					s_id = "' . (int)$s_id . '";';
					
		$this->db->query($sql);
		
		// delse cache file						 
		$this->_delete_cache();
		
		$this->session->set_flashdata('action', 'Staff member deleted');
	}
	
	// delete cache file
	protected function _delete_cache()
	{
		$this->cache->delete('staff');
	}
	
	
}