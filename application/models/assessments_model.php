<?php

class Assessments_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	// ========================================================================
	// Criteria Lists
	// ========================================================================




	/**
	 * Add a new criteria list. If this is a list for a journey, it must have acl_j_id set.
	 *
	 * @param array $data		Array of data for assessment criteria.
	 * @return mixed		ID of new list on success, false on failure.
	 * @author CR
	 */
	public function insert_acl($data = array())
	{
		// JSON encode the outcomes
		$data['acl_criteria'] = json_encode(element('acl_criteria', $data, NULL), JSON_NUMERIC_CHECK);

		$sql = $this->db->insert_string('ass_criteria_lists', $data);

		if ($this->db->query($sql))
		{
			$acl_id = $this->set_acl_outcomes($acl_id);
			return $this->db->insert_id();
		}
		else
		{
			return FALSE;
		}
	}




	/**
	 * Update a criteria list
	 *
	 * @param int $acl_id		ID of assessment criteria to update
	 * @param array $data		Array of data to update assessment criteria with
	 * @return mixed		ID of list on success, false on failure
	 * @author CR
	 */
	public function update_acl($acl_id = 0, $data = array())
	{
		// JSON encode the outcomes
		$data['acl_criteria'] = json_encode(element('acl_criteria', $data, NULL), JSON_NUMERIC_CHECK);

		$sql = $this->db->update_string('ass_criteria_lists', $data, 'acl_id = ' . (int) $acl_id);

		if ($this->db->query($sql))
		{
			$this->set_acl_outcomes($acl_id);
			return $acl_id;
		}
		else
		{
			return FALSE;
		}
	}




	/**
	 * Get all of the available criteria lists, optionally for one journey.
	 *
	 * This function can either return system lists only (for use in admin > options)
	 * or a list of global AND list specific for one journey.
	 *
	 * @param int $j_id		ID of journey to include lists for.
	 * @return array 		DB result array of assessment criteria lists. If $j_id is 0, only global lists are returned
	 * @author CR
	 */
	public function get_all_acl($j_id = 0)
	{
		if ($j_id === 0)
		{
			// No Journey ID - ignore ALL journey lists
			$sql = 'SELECT *
					FROM ass_criteria_lists
					WHERE acl_j_id IS NULL
					AND acl_type != "journey"
					ORDER BY acl_name ASC';
		}
		else
		{
			// Get all possible lists (global + journey-specific)
			$sql = 'SELECT *
					FROM ass_criteria_lists
					WHERE (acl_j_id IS NULL AND acl_type != "journey")
					OR (acl_j_id = ? AND acl_type = "journey")
					ORDER BY FIELD(acl_type, "journey"), acl_name ASC';
		}

		$result = $this->db->query($sql, array($j_id))->result_array();

		foreach ($result as &$row)
		{
			$row['acl_criteria'] = json_decode($row['acl_criteria'], TRUE);
		}

		return $result;
	}




	/**
	 * Get a single assessment criteria list by ID
	 *
	 * @param int $acl_id		ID of list to get
	 * @return array 		DB row array
	 * @author CR
	 */
	public function get_acl($acl_id = 0)
	{
		$sql = 'SELECT * FROM ass_criteria_lists WHERE acl_id = ? LIMIT 1';
		$row = $this->db->query($sql, array($acl_id))->row_array();

		if ( ! $row)
		{
			return $row;
		}

		$row['acl_criteria'] = json_decode($row['acl_criteria'], TRUE);
		return $row;
	}




	/**
	 * Get a single criteria list (the only one) for the given journey.
	 *
	 * @param int $j_id		ID of journey that the ACL belongs to
	 * @return array 		DB row array of ACL
	 * @author CR
	 */
	public function get_journey_acl($j_id = 0)
	{
		$sql = 'SELECT *
				FROM ass_criteria_lists
				WHERE acl_j_id = ?
				AND acl_type = "journey"
				LIMIT 1';

		$row = $this->db->query($sql, array($j_id))->row_array();

		if ( ! $row)
		{
			return $row;
		}

		$row['acl_criteria'] = json_decode($row['acl_criteria'], TRUE);
		return $row;
	}




	/**
	 * Delete an assessment criteria list
	 *
	 * @param int $acl_id		ID of assessment criteria list to delete
	 * @return bool
	 * @author CR
	 */
	public function delete_acl($acl_id = 0)
	{
		$sql = 'DELETE FROM ass_criteria_lists WHERE acl_id = ? LIMIT 1';
		$delete = $this->db->query($sql, array($acl_id));

		if ($delete) $this->set_acl_outcomes($acl_id);

		return $delete;
	}




	/**
	 * Update the outcomes table with the outcomes for the given criteria list.
	 *
	 * @param int $acl_id		ID of assessment criteria list to update the outcomes table.
	 * @return bool
	 * @author CR
	 */
	public function set_acl_outcomes($acl_id = 0)
	{
		$sql = 'DELETE FROM ass_criteria_outcomes WHERE aco_acl_id = ?';
		$delete = $this->db->query($sql, array($acl_id));

		$acl = $this->get_acl($acl_id);

		if ($acl['acl_criteria'])
		{
			foreach ($acl['acl_criteria'] as $num => $title)
			{
				$outcomes[] = array(
					'aco_acl_id' => $acl_id,
					'aco_num' => $num,
					'aco_title' => $title,
				);
			}

			$insert = $this->db->insert_batch('ass_criteria_outcomes', $outcomes);

			return $insert;
		}

		return $delete;
	}




	// =======================================================================
	// Assessments
	// =======================================================================




	/**
	 * Add a new assessment for a journey
	 *
	 * @param array $jas		Assessment info, should include journey ID, ACL ID, date, notes.
	 * @param array $scores		Array of numbers => scores
	 * @return mixed		ID of new assessment ID on success
	 * @author CR
	 */
	public function insert_assessment($jas = array(), $scores = array())
	{
		$sql = $this->db->insert_string('journey_assessments', $jas);

		if ($this->db->query($sql))
		{
			$jas_id = $this->db->insert_id();

			// Update first/last ass dates
			$this->set_first_and_last_assessment_dates($jas['jas_j_id']);

			$acl = $this->get_acl($jas['jas_acl_id']);

			// Insert scores in separate table

			$jacs = array();

			foreach ($scores as $num => $score)
			{
				$jacs[] = array(
					'jacs_jas_id' => $jas_id,
					'jacs_num' => $num,
					'jacs_title' => $acl['acl_criteria'][$num],
					'jacs_score' => $score,
				);
			}

			if ($this->db->insert_batch('journey_ass_criteria_scores', $jacs))
			{
				return $jas_id;
			}
		}

		return FALSE;
	}




	/**
	 * Get the assessments for a given journey
	 *
	 * @param int $j_id		Journey ID of case to get assessments for
	 * @param string $acl_type		Get assessments that match this type of ass criteria list
	 * @param string $order		Order string
	 * @return array		Assessments for a journey including scores
	 * @author CR
	 */
	public function get_assessments($j_id = 0, $type = NULL, $order = 'jas_id DESC')
	{
		// Always ensure the journey is present
		$where = ' AND jas_j_id = ' . (int) $j_id;

		// If a type is specified, add to query
		if ($type !== NULL)
		{
			$where .= ' AND acl_type = ' . $this->db->escape($type);
		}

		$sql = 'SELECT
					*,
					DATE_FORMAT(jas_date, "%d/%m/%Y") AS jas_date_format
				FROM
					journey_assessments
				LEFT JOIN
					ass_criteria_lists
					ON jas_acl_id = acl_id
				WHERE
					1 = 1
				' . $where . '
				ORDER BY ' . $order;

		$query = $this->db->query($sql);

		$assessments = array();

		if ($query->num_rows() > 0)
		{
			// Success! Got 1+ assessments
			$result = $query->result_array();

			foreach ($result as $row)
			{
				// Get scores for this assessment
				$row['scores'] = $this->get_assessment_scores($row['jas_id']);

				// Add to final array
				$assessments[$row['jas_id']] = $row;
			}

		}

		return $assessments;
	}




	/**
	 * Get the last assessment completed for a journey of the given type.
	 *
	 * This is used to populate the last & next dates on journeys.
	 *
	 * @param int $j_id		ID of journey to get assessment for
	 * @param string $type		Type of assessment to get, based on criteria list
	 * @return array 		DB row array of the assessment, without scores
	 * @author CR
	 */
	public function get_last_assessment($j_id = 0, $type = '')
	{
		$sql = 'SELECT
					*,
					DATE_FORMAT(jas_date, "%d/%m/%Y") AS jas_date_format
				FROM
					journey_assessments
				LEFT JOIN
					ass_criteria_lists
					ON jas_acl_id = acl_id
				WHERE
					jas_j_id = ?
				AND
					acl_type = ?
				ORDER BY
					jas_date DESC, jas_id DESC
				LIMIT 1';

		return $this->db->query($sql, array($j_id, $type))->row_array();
	}




	/**
	 * Get scores for a given assessment
	 *
	 * @param int $jas_id		ID of journey assessment to get scores for
	 * @return array 		DB result array of scores
	 * @author CR
	 */
	public function get_assessment_scores($jas_id = 0)
	{
		$sql = 'SELECT
					jas_acl_id,
					jacs_num,
					jacs_title,
					jacs_score
				FROM
					journey_ass_criteria_scores
				LEFT JOIN
					journey_assessments
					ON jacs_jas_id = jas_id
				WHERE
					jacs_jas_id = ?
				ORDER BY
					jacs_num ASC';

		return $this->db->query($sql, array($jas_id))->result_array();
	}




	/**
	 * Delete a single assessment from a journey
	 *
	 * @param int $jas_id		ID of journey to delete
	 * @return bool
	 * @author CR
	 */
	public function delete_assessment($jas_id, $j_id)
	{
		$sql = 'DELETE FROM
					journey_assessments
				WHERE
					jas_id = ?';

		$query = $this->db->query($sql, array($jas_id));

		if ($query)
		{
			$this->set_first_and_last_assessment_dates($j_id);
			$this->session->set_flashdata('action', 'Assessment deleted');
		}

		return $query;
	}




	/**
	 * Get dates of first and last assessments and udpate journey table
	 *
	 * @param int $j_id		Journey ID to get dates for and to update
	 */
	public function set_first_and_last_assessment_dates($j_id)
	{
		$sql = 'INSERT INTO
					journeys
					(
						j_id,
						j_date_first_assessment,
						j_date_last_assessment
					)
				SELECT
					jas_j_id,
					IF(MIN(jas_date) IS NULL, NULL, MIN(jas_date)) AS first_assessment,
					IF(MAX(jas_date) IS NULL, NULL, MAX(jas_date)) AS last_assessment
				FROM
					journey_assessments
				WHERE
					jas_j_id = ?
				ON DUPLICATE KEY UPDATE
					j_id = VALUES(j_id),
					j_date_first_assessment = VALUES(j_date_first_assessment),
					j_date_last_assessment = VALUES(j_date_last_assessment)';

		return $this->db->query($sql, array($j_id));
	}




}