<?php

class Job_roles_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}


	/**
	 * Get all active job roles.
	 *
	 * Use this method to retrieve the options that should be available for selection in other areas.
	 */
	public function get_active()
	{
		$sql = 'SELECT
					jr_id,
					jr_title
				FROM
					job_roles
				WHERE
					jr_active = 1
				ORDER BY
					jr_title ASC ';

		return $this->db->query($sql)->result_array();
	}



	/**
	 * Get all job roles regardless of active status.
	 *
	 * Use this method to retrieve the options for display in admin
	 */
	public function get_all()
	{
		$sql = 'SELECT
					*
				FROM
					job_roles
				ORDER BY
					jr_title ASC ';

		return $this->db->query($sql)->result_array();
	}



	/**
	 * Get a single job role
	 */
	function get($jr_id = 0)
	{
		$sql = 'SELECT
					*
				FROM
					job_roles
				WHERE
					jr_id = ?
				LIMIT 1';

		return $this->db->query($sql, array($jr_id))->row_array();
	}




	/**
	 * Add a new job role
	 *
	 * @param array $data		2D array of columns => values to set
	 * @return mixed		ID of new entry on success, false on failure
	 */
	public function insert($data = array())
	{
		$sql = $this->db->insert_string('job_roles', $data);
		$query = $this->db->query($sql);
		return ($query ? $this->db->insert_id() : FALSE);
	}




	/**
	 * Update a job role
	 *
	 * @param int $jr_id		ID of job role to update
	 * @param array $data		2D array of columns => values to set
	 * @return mixed		ID on success, false on failure
	 */
	public function update($jr_id = 0, $data = array())
	{
		$sql = $this->db->update_string('job_roles', $data, 'jr_id = ' . (int) $jr_id);
		$query = $this->db->query($sql);
		return ($query ? (int) $jr_id : FALSE);
	}


}