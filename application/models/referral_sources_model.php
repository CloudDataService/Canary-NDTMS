<?php

class Referral_sources_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	// get active referral_sources
	public function get_referral_sources($cache = 1)
	{
		// if we want to try and return a cache
		if($cache)
		{
			if($referral_sources = $this->cache->get('referral_sources'))
				return $referral_sources;
		}		
		
		// else hit the db	
		$sql = 'SELECT
					rs_id,
					rs_name,
					rs_type
				FROM
					referral_sources
				WHERE
					rs_active = 1
				ORDER BY
					rs_name ASC ';
				
		$referral_sources =  $this->db->query($sql)->result_array();
		
		// cache results forever
		$this->cache->save('referral_sources', $referral_sources, 9999999999);
		
		return $referral_sources;		
	}
	
	// get single referral_source information
	function get_referral_source($rs_id)
	{
		if($rs_id)
		{
			$sql = 'SELECT
						*
					FROM
						referral_sources
					WHERE
						rs_id = "' . (int)$rs_id . '"
						AND rs_active = 1 ';
			
			return $this->db->query($sql)->row_array();
		}
		
		return false;
	}
	
	// either adds or updates referral_source information
	public function set_referral_source($rs = FALSE)
	{
		// if referral source is not false
		if($rs != FALSE)
		{
			// update referral source information
			$sql = 'UPDATE
						referral_sources
					SET
						rs_name = ?,
						rs_type = ?
					WHERE
						rs_id = "' . (int)$rs['rs_id'] . '";';

			// set action message
			$this->session->set_flashdata('action', 'Referral source information updated');
		}
		else
		{
			// insert new referral_source information
			$sql = 'INSERT INTO
						referral_sources
					SET
						rs_name = ?,
						rs_type = ?';
			
			// set action message
			$this->session->set_flashdata('action', 'New referral source information added');
		}
		
		// commit the query
		$this->db->query($sql, array($this->input->post('rs_name'),
									 $this->input->post('rs_type')));
			
		// delete cache file						 
		$this->_delete_cache();
	}	
	
	// marks the referral_source as inactive, effectively deleting it from the system
	public function set_referral_source_inactive($rs_id)
	{
		$sql = 'UPDATE
					referral_sources
				SET
					rs_active = 0
				WHERE
					rs_id = "' . (int)$rs_id . '";';
					
		$this->db->query($sql);
		
		// delete cache file						 
		$this->_delete_cache();
		
		$this->session->set_flashdata('action', 'Referral source deleted');
	}
	
	// delete cache file
	protected function _delete_cache()
	{
		$this->cache->delete('referral_sources');
	}
	
}