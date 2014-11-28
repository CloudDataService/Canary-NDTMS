<?php

class Dna_reasons_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	// get active dna_reasons
	public function get_dna_reasons($cache = 1)
	{
		// if we want to try and return a cache
		if($cache)
		{
			if($dna_reasons = $this->cache->get('dna_reasons'))
				return $dna_reasons;
		}

		// else hit the db
		$sql = 'SELECT
					dr_id,
					dr_name
				FROM
					dna_reasons
				WHERE
					dr_active = 1
				ORDER BY
					dr_name ASC ';

		$dna_reasons =  $this->db->query($sql)->result_array();

		// cache results forever
		$this->cache->save('dna_reasons', $dna_reasons, 9999999999);

		return $dna_reasons;
	}

	// get single dna_reason information
	public function get_dna_reason($dr_id)
	{
		if($dr_id)
		{
			$sql = 'SELECT
						*
					FROM
						dna_reasons
					WHERE
						dr_id = "' . (int)$dr_id . '"
						AND dr_active = 1 ';

			return $this->db->query($sql)->row_array();
		}

		return false;
	}

	// either adds or updates dna_reason information
	public function set_dna_reason($dr = FALSE)
	{
		// if DNA reason is not false
		if($dr != FALSE)
		{
			// update DNA reason information
			$sql = 'UPDATE
						dna_reasons
					SET
						dr_name = ?
					WHERE
						dr_id = "' . (int)$dr['dr_id'] . '";';

			// set action message
			$this->session->set_flashdata('action', 'DNA reason information updated');
		}
		else
		{
			// insert new dna_reason information
			$sql = 'INSERT INTO
						dna_reasons
					SET
						dr_name = ?';
			// set action message
			$this->session->set_flashdata('action', 'New DNA reason information added');
		}

		// commit the query
		$this->db->query($sql, array($this->input->post('dr_name')));

		// delete cache file
		$this->_delete_cache();
	}

	// marks the dna_reason as inactive, effectively deleting it from the system
	public function set_dna_reason_inactive($dr_id)
	{
		$sql = 'UPDATE
					dna_reasons
				SET
					dr_active = 0
				WHERE
					dr_id = "' . (int)$dr_id . '";';

		$this->db->query($sql);

		// delete cache file
		$this->_delete_cache();

		$this->session->set_flashdata('action', 'DNA reason deleted');
	}

	// delete cache file
	protected function _delete_cache()
	{
		$this->cache->delete('dna_reasons');
	}

}
