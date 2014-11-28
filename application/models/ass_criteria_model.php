<?php

class Ass_criteria_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Get all the outcome criteria lists
	 *
	 * @return mixed		Array of row arrays on success
	 */
	public function get_lists()
	{
		$sql = 'SELECT
					*
				FROM
					ass_criteria_lists
				ORDER BY
					acl_name ASC ';

		$result = $this->db->query($sql)->result_array();
		foreach ($result as &$row)
		{
			$row['acl_criteria'] = json_decode($row['acl_criteria'], TRUE);
		}

		return $result;
	}




	/**
	 * Get a single criteria list
	 *
	 * @param int $acl_id		ID of outcome criteria list to get
	 * @return mixed		Array on success
	 */
	public function get_list($acl_id)
	{
		$sql = 'SELECT
					*
				FROM
					ass_criteria_lists
				WHERE
					acl_id = ?
				LIMIT 1';

		$row = $this->db->query($sql, array($acl_id))->row_array();
		if ($row)
		{
			$row['acl_criteria'] = json_decode(element('acl_criteria', $row, NULL), TRUE);
		}
		return $row;
	}




	/**
	 * Add or update list
	 *
	 * @param int $acl_id		ID of assessment criteria list to delete
	 * @param array $data		Array of DB columns => values to set
	 * @return bool
	 */
	public function set_list($acl_id = NULL, $data)
	{
		// JSON encode the criteria items
		$data['acl_criteria'] = json_encode($data['acl_criteria'], JSON_NUMERIC_CHECK);

		if ($acl_id)
		{
			// Update
			$sql = $this->db->update_string('ass_criteria_lists', $data, 'acl_id = ' . (int) $acl_id);
			$msg = 'Criteria list updated.';
		}
		else
		{
			// Insert
			$sql = $this->db->insert_string('ass_criteria_lists', $data);
			$msg = 'Criteria list added';
		}

		if ($this->db->query($sql))
		{
			// Set flash message and return
			$this->session->set_flashdata('action', $msg);
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}




	/**
	 * Delete an outcome criteria list
	 *
	 * @param int $acl_id		ID of certeria list to delete
	 * @return bool
	 */
	public function delete_list($acl_id)
	{
		$sql = 'DELETE FROM ass_criteria_lists WHERE acl_id = ? LIMIT 1';
		return $this->db->query($sql, array($acl_id));
	}


}
