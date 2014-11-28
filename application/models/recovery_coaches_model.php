<?php

class Recovery_coaches_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	// get active recovery_coaches
	public function get_recovery_coaches($cache = 1)
	{
		// if we want to try and return a cache
		if($cache)
		{
			if($recovery_coaches = $this->cache->get('recovery_coaches'))
				return $recovery_coaches;
		}

		// else hit the db
		$sql = 'SELECT
					rc_id,
					rc_jr_id,
					rc_name,
					IF(rc_family_worker = 1, CONCAT(rc_name, " (Family Worker)"), rc_name) AS rc_name_family_worker,
					rc_family_worker,
					a2rc_a_id,
					a_fname,
					a_sname,
					jr_id,
					jr_title
				FROM
					recovery_coaches
				LEFT JOIN
					a2rc
					ON rc_id = a2rc_rc_id
				LEFT JOIN
					admins a
					ON a2rc_a_id = a_id
				LEFT JOIN
					job_roles
					ON rc_jr_id = jr_id
				WHERE
					rc_active = 1
				ORDER BY
					rc_name ASC ';

		$recovery_coaches =  $this->db->query($sql)->result_array();

		// cache results forever
		$this->cache->save('recovery_coaches', $recovery_coaches, 9999999999);

		return $recovery_coaches;
	}

	// get single recovery_coach information
	function get_recovery_coach($rc_id)
	{
		if($rc_id)
		{
			$sql = 'SELECT
						*,
						jr_id,
						jr_title
					FROM
						recovery_coaches
					LEFT JOIN
						a2rc
						ON rc_id = a2rc_rc_id
					LEFT JOIN
						job_roles
						ON rc_jr_id = jr_id
					WHERE
						rc_id = "' . (int)$rc_id . '"
						AND rc_active = 1 ';

			return $this->db->query($sql)->row_array();
		}

		return false;
	}

	// either adds or updates recovery_coach information
	public function set_recovery_coach($rc = FALSE)
	{
		// if recovery coach is not false
		if($rc != FALSE)
		{
			// update recovery coach information
			$sql = 'UPDATE
						recovery_coaches
					SET
						rc_name = ?,
						rc_jr_id = ?,
						rc_family_worker = ?
					WHERE
						rc_id = ' . (int) $rc['rc_id'];
			// set action message
			$this->session->set_flashdata('action', 'Recovery coach information updated');
		}
		else
		{
			// insert new recovery_coach information
			$sql = 'INSERT INTO
						recovery_coaches
					SET
						rc_name = ?,
						rc_jr_id = ?,
						rc_family_worker = ?';
			// set action message
			$this->session->set_flashdata('action', 'New recovery coach information added');
		}

		// commit the query
		$this->db->query($sql, array(
			$this->input->post('rc_name'),
			($this->input->post('rc_jr_id') ? $this->input->post('rc_jr_id') : NULL),
			$this->input->post('rc_family_worker'),
		));

		// Link to administrator
		$rc_id = ($rc != FALSE ? $rc['rc_id'] : $this->db->insert_id());
		$this->set_admin($this->input->post('a2rc_a_id'), $rc_id);


		// delete cache file
		$this->_delete_cache();
	}

	// marks the recovery_coach as inactive, effectively deleting it from the system
	public function set_recovery_coach_inactive($rc_id)
	{
		$sql = 'UPDATE
					recovery_coaches
				SET
					rc_active = 0
				WHERE
					rc_id = "' . (int)$rc_id . '";';

		$this->db->query($sql);

		// delete cache file
		$this->_delete_cache();

		// Remove association with administrator account
		$this->set_admin(0, $rc_id);

		$this->session->set_flashdata('action', 'Recovery coach deleted');
	}

	// delete cache file
	protected function _delete_cache()
	{
		$this->cache->delete('recovery_coaches');
	}




	/**
	 * Define the relationship between an administrator and a keyworker.
	 *
	 * @param int $a_id		ID of administrator account
	 * @param int $rc_id		ID of keyworker
	 * @return bool
	 */
	public function set_admin($a_id = 0, $rc_id = 0)
	{
		$a_id = (int) $a_id;
		$rc_id = (int) $rc_id;

		// Remove any current entries
		$sql = 'DELETE FROM a2rc WHERE a2rc_a_id = ? OR a2rc_rc_id = ? LIMIT 1';
		$this->db->query($sql, array($a_id, $rc_id));

		if ($a_id === 0 || $rc_id === 0)
		{
			return TRUE;
		}

		$sql = 'INSERT INTO
					a2rc
				SET
					a2rc_a_id = ?,
					a2rc_rc_id = ?
				ON DUPLICATE KEY UPDATE
					a2rc_a_id = VALUES(a2rc_a_id),
					a2rc_rc_id = VALUES(a2rc_rc_id)';

		return $this->db->query($sql, array($a_id, $rc_id));
	}




}