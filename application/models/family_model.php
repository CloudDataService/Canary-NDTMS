<?php

class Family_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	public function get_journey_family_info($j_id)
	{
		$sql = 'SELECT
					jf_family_members,
					jf_notes
				FROM
					journey_family
				WHERE
					jf_j_id = "' . (int)$j_id . '";';

		// if family info exists
		if($family_info = $this->db->query($sql)->row_array())
		{
			// unserialize family members
			$family_info['jf_family_members'] = json_decode($family_info['jf_family_members'], true);

			// return family info
			return $family_info;
		}
		else
		{
			// create new journey family entry with no data
			$sql = 'INSERT INTO
						journey_family
					SET
						jf_j_id = ?,
						jf_family_members = ?,
						jf_notes = ?';

			$this->db->query($sql, array($j_id,
										 json_encode(array()),
										 NULL));

			return false;
		}
	}




	public function set_journey_family_info($j_id)
	{
		$sql = 'UPDATE
					journey_family
				SET
					jf_notes = ?
				WHERE
					jf_j_id = "' . (int)$j_id . '";';

		$this->db->query($sql, strip_tags($this->input->post('jf_notes')));

		$this->session->set_flashdata('action', 'Family information updated');
	}




	public function get_family_members($j_id)
	{
		$sql = 'SELECT
					jf_family_members
				FROM
					journey_family
				WHERE
					jf_j_id = "' . (int)$j_id . '";';

		// if some family members are returned
		if($family_members = $this->db->query($sql)->row_array())
		{
			// return them unserialized
			return json_decode($family_members['jf_family_members'], true);
		}

		// otherwise return a blank array to add the first family member
		return array();
	}




	// serializes array of family members and stores it in db
	public function save_family_members($j_id, $family_members)
	{
		$sql = 'UPDATE
					journey_family
				SET
					jf_family_members = ?
				WHERE
					jf_j_id = "' . (int)$j_id . '";';

		$this->db->query($sql, json_encode($family_members));
	}




	/**
	 * Get all the clients who have been added as family members to a family journey
	 *
	 * @param int $j_id		Journey ID to get family client members for
	 * @return array		Array of clients
	 * @author CR
	 */
	public function get_clients($j_id)
	{
		$sql = 'SELECT
					family_clients.*,
					c_id,
					c_fname,
					c_sname,
					c_gender,
					DATE_FORMAT(c_date_of_birth, "%d/%m/%Y") AS c_date_of_birth_format
				FROM
					family_clients
				LEFT JOIN
					clients
						ON fc_c_id = c_id
				WHERE
					fc_j_id = ?
				ORDER BY
					c_fname ASC';

		return $this->db->query($sql, array($j_id))->result_array();
	}




	/**
	 * Add existing client as a relative of client in a journey
	 *
	 * @param array $data		Array of DB fileds => values for family client data
	 * @return bool
	 * @author CR
	 */
	public function add_client($data)
	{
		$sql = $this->db->insert_string('family_clients', $data);
		return $this->db->query($sql);
	}




	/**
	 * Remove a referenced client from the family
	 *
	 * @param int $j_id		ID of journey to remove client from
	 * @param int $c_id		ID of referenced client to remove from journey
	 * @return bool
	 * @author CR
	 */
	public function delete_client($j_id, $c_id)
	{
		$sql = 'DELETE FROM
					family_clients
				WHERE
					fc_j_id = ?
				AND
					fc_c_id = ?
				LIMIT 1';

		return $this->db->query($sql, array($j_id, $c_id));
	}


}
