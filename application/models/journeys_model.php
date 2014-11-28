<?php

class Journeys_model extends MY_Model
{

	protected $_table = 'journeys';
	protected $_primary = 'j_id';

	protected $_filter_types = array(
		'where' => array('j_id', 'j_c_id', 'j_rc_id', 'j_status'),
		'like' => array(),
		'in' => array('j_status'),
		'function' => array('date_from', 'date_to', 'without_assessment'),
	);


	function __construct()
	{
		parent::__construct();
	}




	/**
	 * Custom filter SQL function: where last csop or top dates are not set.
	 * Used when getting newly-assigned cases for keyworkers.
	 *
	 * @param string $val       Filter value (not used)
	 * @param string $operator      Operand for SQL query, passed from MY_Model
	 */
	protected function _filter_without_assessment($val = NULL, $operator = 'AND')
	{
		return " $operator (ji_csop_last IS NULL AND ji_top_last IS NULL) ";
	}


	protected function _filter_date_from($val = NULL, $operator = 'AND')
	{
		return " $operator j_date_of_referral >= '" . parse_date($val) . "' ";
	}


	protected function _filter_date_to($val = NULL, $operator = 'AND')
	{
		return " $operator j_date_of_referral <= '" . parse_date($val) . "' ";
	}




	/**
	 * Get journeys which are "newly-assigned" and that need assessment.
	 *
	 * These are ones that are "Open" status, and have NOT had a CSOP/TOP assessment.
	 */
	public function get_using_filter()
	{
		$filter_sql = $this->filter_sql();

		$permissions = $this->session->userdata('permissions');

		if ($this->session->userdata('can_read_family') != 1 || element('apt_can_read_family', $permissions, FALSE) != 1)
			$filter_sql .= ' AND j_type != "F" ';

		if ($this->session->userdata('can_read_client') != 1 || element('apt_can_read_client', $permissions, FALSE) != 1)
			$filter_sql .= ' AND j_type != "C" ';

		$sql = 'SELECT
					j_id,
					j_type,
					j_c_id,
					CONCAT(c_fname, " ", c_sname) AS c_name,
					c_is_risk,
					IF(ji_flagged_risk_summary = "" OR ji_flagged_risk_summary IS NULL, " summary unknown", ji_flagged_risk_summary) AS ji_flagged_risk_summary,
					IF(j_date_of_referral IS NULL, "N/A", DATE_FORMAT(j_date_of_referral, "%d/%m/%Y")) AS j_date_of_referral_format,
					IF(rc_name IS NULL, "N/A", rc_name) AS rc_name,
					IF(j_status = 1, "Open", IF(j_status = 3, "Initial Contact", IF(j_status = 4, "Referral", "Closed"))) as j_status,
					j_published,
					ji_csop_last,
					ji_csop_due,
					ji_top_last,
					ji_top_due,
					IF(j_ndtms_valid = 1, "Yes", "No") as j_ndtms_valid
				FROM
					journeys
				LEFT JOIN
					journeys_info
					ON j_id = ji_j_id
				LEFT JOIN
					clients
					ON j_c_id = c_id
				LEFT JOIN
					recovery_coaches
					ON j_rc_id = rc_id
				WHERE
					1 = 1
				' . $filter_sql . '
				' . $this->order_sql() . '
				' . $this->limit_sql() . '';

		return $this->db->query($sql)->result_array();
	}




	// FUNCTION
	public function get_total_journeys()
	{
		$sql = 'SELECT
					COUNT(j_id) AS total
				FROM
					journeys
				LEFT JOIN
					clients ON j_c_id = c_id
				LEFT JOIN
					clients_info ON j_id = ci_j_id
				WHERE
					1 = 1';

		if($this->session->userdata('can_read_family') != 1 || $this->session->userdata['permissions']['apt_can_read_family'] != 1)
			$sql .= ' AND j_type != "F" ';

		if($this->session->userdata('can_read_client') != 1 || $this->session->userdata['permissions']['apt_can_read_client'] != 1)
			$sql .= ' AND j_type != "C" ';

		if(@$_GET['j_type'])
			$sql .= ' AND j_type = ' . $this->db->escape($_GET['j_type']) . ' ';

		if(@$_GET['j_c_id'])
			$sql .= ' AND (j_c_id = "' . (int)$_GET['j_c_id'] . '" OR ci_previous_id = "' . (int)$_GET['j_c_id'] . '") ';

		if ($this->input->get('j_rc_id') !== FALSE && $this->input->get('j_rc_id') != '')
			$sql .= ' AND j_rc_id = ' . $this->db->escape($this->input->get('j_rc_id')) . ' ';

		if(@$_GET['c_sname'])
			$sql .= ' AND CONVERT(c_sname USING latin1) = "' . mysql_real_escape_string($_GET['c_sname']) . '" ';

		if(@$_GET['date_from'])
			$sql .= ' AND j_date_of_referral >= "' . parse_date($_GET['date_from']) . '" ';

		if(@$_GET['date_to'])
			$sql .= ' AND j_date_of_referral <= "' . parse_date($_GET['date_to']) . '" ';

		if(@$_GET['c_catchment_area'])
			$sql .= ' AND c_catchment_area = "' . mysql_real_escape_string($_GET['c_catchment_area']) . '" ';

		if(@$_GET['j_status'])
			$sql .= ' AND j_status = "' . (int)$_GET['j_status'] . '" ';

		if(@$_GET['j_published'])
			$sql .= ' AND j_published = "' . (int)$_GET['j_published'] . '" ';

		if(@$_GET['j_ndtms_valid'])
			$sql .= ' AND j_ndtms_valid = "' . (int)$_GET['j_ndtms_valid'] . '" ';

		$tiers = array('1' => 'Brief intervention', '2' => 'Tier 2', '3' => 'Tier 3', '-1' => '- None -');
		if(@$_GET['j_tier'] && array_key_exists((int)$_GET['j_tier'], $tiers))
		{
			if ($_GET['j_tier'] == -1)
			{
				$sql .= ' AND j_tier IS NULL ';
			}
			else
			{
				$sql .= ' AND j_tier = "' . $tiers[(int)$_GET['j_tier']] . '" ';
			}
		}

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}


	// FUNCTION
	public function get_journeys($start = 0, $limit = 0)
	{
		if( ! in_array(@$_GET['order'], array('j_c_id', 'c_sname', 'j_date_of_referral', 'rc_name', 'j_ndtms_valid', 'last_seen')) ) $_GET['order'] = 'j_c_id';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';

		$sql = 'SELECT
					j_id,
					j_type,
					j_c_id,
					CONCAT(c_fname, " ", c_sname) AS c_name,
					c_is_risk,
					IF(ji_flagged_risk_summary = "" OR ji_flagged_risk_summary IS NULL, " summary unknown", ji_flagged_risk_summary) AS ji_flagged_risk_summary,
					IF(j_date_of_referral IS NULL, "N/A", DATE_FORMAT(j_date_of_referral, "%d/%m/%Y")) AS j_date_of_referral_format,
					IF(rc_name IS NULL, "N/A", rc_name) AS rc_name,
					IF(j_status = 1, "Open", IF(j_status = 3, "Initial Contact", IF(j_status = 4, "Referral", "Closed"))) as j_status,
					j_published,
					ji_csop_last,
					ji_csop_due,
					ji_top_last,
					ji_top_due,
					IF(j_ndtms_valid = 1, "Yes", "No") as j_ndtms_valid,
					/* (SELECT MAX(je_datetime) FROM journey_events WHERE je_j_id = j_id LIMIT 1) AS last_seen */
					jc_last_event AS last_seen
				FROM
					journeys
				LEFT JOIN
					clients
						ON c_id = j_c_id
				LEFT JOIN
					journeys_info
						ON j_id = ji_j_id
				LEFT JOIN
					journeys_cache
						ON j_id = jc_j_id
				LEFT JOIN
					clients_info
						ON ci_j_id = j_id
				LEFT JOIN
					recovery_coaches
						ON  rc_id = j_rc_id
				WHERE
					1 = 1 ';

		if($this->session->userdata('can_read_family') != 1 || @$this->session->userdata['permissions']['apt_can_read_family'] != 1)
			$sql .= ' AND j_type != "F" ';

		if($this->session->userdata('can_read_client') != 1 || @$this->session->userdata['permissions']['apt_can_read_client'] != 1)
			$sql .= ' AND j_type != "C" ';

		if(@$_GET['j_type'])
			$sql .= ' AND j_type = ' . $this->db->escape($_GET['j_type']) . ' ';


		if(@$_GET['j_c_id'])
			$sql .= ' AND (j_c_id = "' . (int)$_GET['j_c_id'] . '" OR ci_previous_id = "' . (int)$_GET['j_c_id'] . '") ';

		if ($this->input->get('j_rc_id') !== FALSE && $this->input->get('j_rc_id') != '')
			$sql .= ' AND j_rc_id = ' . $this->db->escape($this->input->get('j_rc_id')) . ' ';

		if(@$_GET['c_sname'])
			$sql .= ' AND CONVERT(c_sname USING latin1) = "' . mysql_real_escape_string($_GET['c_sname']) . '" ';

		if(@$_GET['date_from'])
			$sql .= ' AND j_date_of_referral >= "' . parse_date($_GET['date_from']) . '" ';

		if(@$_GET['date_to'])
			$sql .= ' AND j_date_of_referral <= "' . parse_date($_GET['date_to']) . '" ';

		if(@$_GET['c_catchment_area'])
			$sql .= ' AND c_catchment_area = "' . mysql_real_escape_string($_GET['c_catchment_area']) . '" ';

		if(@$_GET['j_status'])
			$sql .= ' AND j_status = "' . (int)$_GET['j_status'] . '" ';

		if(@$_GET['j_published'])
			$sql .= ' AND j_published = "' . (int)$_GET['j_published'] . '" ';

		if(@$_GET['j_ndtms_valid'])
			$sql .= ' AND j_ndtms_valid = "' . (int)$_GET['j_ndtms_valid'] . '" ';

		$tiers = array('1' => 'Brief intervention', '2' => 'Tier 2', '3' => 'Tier 3', '-1' => '- None -');
		if(@$_GET['j_tier'] && array_key_exists((int)$_GET['j_tier'], $tiers))
		{
			if ($_GET['j_tier'] == -1)
			{
				$sql .= ' AND j_tier IS NULL ';
			}
			else
			{
				$sql .= ' AND j_tier = "' . $tiers[(int)$_GET['j_tier']] . '" ';
			}
		}

		if (@$_GET['ci_previous_id'])
		{
			$sql .= ' AND ci_previous_id = "' . (int)$_GET['ci_previous_id'] . '" ';
		}


		if (@$_GET['j_id'])
		{
			$sql .= ' AND j_id = "' . (int)str_replace(array('#', 'C', 'F'), '', $_GET['j_id']) . '" ';
		}


		// set order by clause
		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		if ($limit)
		{
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
		}

		// header("Content-Type: text/plain");print $sql . PHP_EOL;exit;

		$journeys = $this->db->query($sql)->result_array();

		return $journeys;
	}




	public function get_total_keyworker_journeys($rc_id = 0)
	{
		$sql = 'SELECT
					COUNT(j_id) AS total
				FROM
					journeys
				LEFT JOIN
					clients
					ON c_id = j_c_id
				LEFT JOIN
					clients_info
					ON ci_j_id = j_id
				LEFT JOIN
					recovery_coaches
					ON rc_id = j_rc_id
				WHERE
					1 = 1
				AND
					j_rc_id = ' . (int) $rc_id . '
				AND
					j_status != 2 ';

		$sql .= ' ';

		if($this->session->userdata('can_read_family') != 1 || @$this->session->userdata['permissions']['apt_can_read_family'] != 1)
			$sql .= ' AND j_type != "F" ';

		if($this->session->userdata('can_read_client') != 1 || @$this->session->userdata['permissions']['apt_can_read_client'] != 1)
			$sql .= ' AND j_type != "C" ';

		if(@$_GET['j_type'])
			$sql .= ' AND j_type = ' . $this->db->escape($_GET['j_type']) . ' ';

		if(@$_GET['j_c_id'])
			$sql .= ' AND j_c_id = "' . (int)$_GET['j_c_id'] . '" ';

		if(@$_GET['c_sname'])
			$sql .= ' AND CONVERT(c_sname USING latin1) = ' . $this->db->escape($this->input->get('c_sname')) . ' ';

		if(@$_GET['j_status'])
			$sql .= ' AND j_status = "' . (int)$_GET['j_status'] . '" ';

		if(@$_GET['j_published'])
			$sql .= ' AND j_published = "' . (int)$_GET['j_published'] . '" ';

		if(@$_GET['j_ndtms_valid'])
			$sql .= ' AND j_ndtms_valid = "' . (int)$_GET['j_ndtms_valid'] . '" ';

		$row = $this->db->query($sql)->row_array();

		return $row['total'];
	}




	public function keyworker_journeys($rc_id = 0, $start = 0, $limit = 0)
	{
		if ( ! in_array(@$_GET['order'], array('j_c_id', 'c_sname', 'j_date_of_referral', 'rc_name', 'j_ndtms_valid', 'j_status')) ) $_GET['order'] = 'j_c_id';
		if (@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';

		$sql = 'SELECT
					j_id,
					j_type,
					j_c_id,
					CONCAT(c_fname, " ", c_sname) AS c_name,
					c_is_risk,
					IF(j_date_of_referral IS NULL, "N/A", DATE_FORMAT(j_date_of_referral, "%d/%m/%Y")) AS j_date_of_referral_format,
					IF(rc_name IS NULL, "N/A", rc_name) AS rc_name,
					IF(j_status = 1, "Open", IF(j_status = 3, "Initial Contact", IF(j_status = 4, "Referral", "Closed"))) as j_status,
					j_published,
					ji_csop_last,
					ji_csop_due,
					ji_top_last,
					ji_top_due,
					IF(j_ndtms_valid = 1, "Yes", "No") as j_ndtms_valid
				FROM
					journeys
				LEFT JOIN
					journeys_info
					ON ji_j_id = j_id
				LEFT JOIN
					clients
					ON c_id = j_c_id
				LEFT JOIN
					clients_info
					ON ci_j_id = j_id
				LEFT JOIN
					recovery_coaches
					ON rc_id = j_rc_id
				WHERE
					1 = 1
				AND
					j_rc_id = ' . (int) $rc_id . '
				AND
					j_status != 2
				';

		$sql .= ' ';

		if($this->session->userdata('can_read_family') != 1 || @$this->session->userdata['permissions']['apt_can_read_family'] != 1)
			$sql .= ' AND j_type != "F" ';

		if($this->session->userdata('can_read_client') != 1 || @$this->session->userdata['permissions']['apt_can_read_client'] != 1)
			$sql .= ' AND j_type != "C" ';

		if(@$_GET['j_type'])
			$sql .= ' AND j_type = ' . $this->db->escape($_GET['j_type']) . ' ';

		if(@$_GET['j_c_id'])
			$sql .= ' AND j_c_id = "' . (int)$_GET['j_c_id'] . '" ';

		if(@$_GET['c_sname'])
			$sql .= ' AND CONVERT(c_sname USING latin1) = ' . $this->db->escape($this->input->get('c_sname')) . ' ';

		if(@$_GET['j_status'])
			$sql .= ' AND j_status = "' . (int)$_GET['j_status'] . '" ';

		if(@$_GET['j_published'])
			$sql .= ' AND j_published = "' . (int)$_GET['j_published'] . '" ';

		if(@$_GET['j_ndtms_valid'])
			$sql .= ' AND j_ndtms_valid = "' . (int)$_GET['j_ndtms_valid'] . '" ';

		// set order by clause
		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		if($limit)
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;

		return $this->db->query($sql)->result_array();
	}




	/**
	 * Start a new journey. Inserts or updates client and creates new journey.
	 *
	 * POST data should include basic client/journey information, including client/family type (j_type).
	 *
	 * @param int $c_id     ID of client to update. If not present/false, new client will be added
	 * @param string $catchment_area        Catchment area name based on postcode of client submitted
	 */
	public function start_new_journey($c_id = FALSE, $catchment_area)
	{
		// Get all POST items to use later
		$post = $this->input->post(NULL, TRUE);

		//if we're creating the journey straight at the 'contact' tab, then we told you the j_type in the get
		if(!isset($post['j_type']))
		{
			if($this->input->get('j_type') != '')
			{
				$post['j_type'] = $this->input->get('j_type');
			}
			else
			{
				return FALSE;
			}
		}

		//Check permissions
		if($post['j_type'] == 'C' && $this->session->userdata['permissions']['apt_can_add_client'] != 1)
		{
			return FALSE;
		}
		elseif($post['j_type'] == 'F' && $this->session->userdata['permissions']['apt_can_add_family'] != 1)
		{
			return FALSE;
		}

		// Array of client data that was submitted
		$client = array($this->input->post('c_fname'),
						$this->input->post('c_sname'),
						$this->input->post('c_gender'),
						$this->input->post('c_date_of_birth'),
						$this->input->post('c_address'),
						str_replace(' ', '', $this->input->post('c_post_code')),
						$catchment_area,
						element('c_tel_home', $post, NULL),
						element('c_tel_mob', $post, NULL),
		);

		// if client does not exist yet
		if ( ! $c_id)
		{
			// Add new client and retrieve ID
			$sql = 'INSERT INTO
						clients
					SET
						c_fname = ?,
						c_sname = ?,
						c_gender = ?,
						c_date_of_birth = ?,
						c_address = ?,
						c_post_code = ?,
						c_catchment_area = ?,
						c_tel_home = ?,
						c_tel_mob = ?';

			$this->db->query($sql, $client);
			$c_id = $this->db->insert_id();
		}
		else
		{
			// update existing client information
			$sql = 'UPDATE
						clients
					SET
						c_fname = ?,
						c_sname = ?,
						c_gender = ?,
						c_date_of_birth = ?,
						c_address = ?,
						c_post_code = ?,
						c_catchment_area = ?,
						c_tel_home = ?,
						c_tel_mob = ?
					WHERE
						c_id = ' . (int) $c_id . '
					LIMIT 1';

			$this->db->query($sql, $client);
		}

		// Data for the journey

		$journey = array(
			'j_c_id'                            => (int) $c_id,
			'j_rc_id'                           => (int) element('j_rc_id', $post, NULL),
			'j_type'                            => element('j_type', $post, NULL),
			'j_datetime_created'                => date('Y-m-d H:i:s', time()),
			'j_datetime_last_update'            => date('Y-m-d H:i:s', time()),
			'j_last_update_by'                  => $this->session->userdata('a_id'),
			'j_date_of_referral'                => element('j_date_of_referral', $post),
			'j_family_or_carer_involved'        => NULL,
		);

		// Add new journey and get ID
		$sql = $this->db->insert_string('journeys', $journey);
		$this->db->query($sql);
		$j_id = $this->db->insert_id();

		// Add new journey to info table
		$journey_info = array(
			'ji_j_id' => (int) $j_id,
			'ji_rs_id' => element('j_rs_id', $post, NULL),
		);

		$sql = $this->db->insert_string('journeys_info', $journey_info);
		$this->db->query($sql);

		// Insert copy of client's information into client info table
		$sql = 'INSERT INTO
					clients_info
				SET
					ci_j_id = ' . (int) $j_id . ',
					ci_fname = ?,
					ci_sname = ?,
					ci_gender = ?,
					ci_date_of_birth = ?,
					ci_address = ?,
					ci_post_code = ?,
					ci_catchment_area = ?,
					ci_tel_home = ?,
					ci_tel_mob = ?';

		$this->db->query($sql, $client);


		// return journey id
		return $j_id;
	}




	/**
	 * Returns basic journey information for setting a journey
	 */
	public function get_basic_journey_info($j_id)
	{
		$sql = 'SELECT
					j_id,
					j_date_of_referral,
					j_c_id,
					j_type,
					j_status,
					j_published,
					c_is_risk,
					CONCAT(c_fname, " ", c_sname) AS c_name,
					ji_csop_last,
					ji_csop_due,
					ji_top_last,
					ji_top_due,
					IF(j_ndtms_valid = 1, "Yes", "No") as j_ndtms_valid
				FROM
					journeys
				LEFT JOIN
					clients ON j_c_id = c_id
				LEFT JOIN
					journeys_info
					ON j_id = ji_j_id
				WHERE
					j_id = "' . (int) $j_id . '"
					AND c_id = j_c_id;';

		$result = $this->db->query($sql)->row_array();


		//Check permissions
		if($result['j_type'] == 'C' && $this->session->userdata['permissions']['apt_can_read_client'] != 1)
		{
			return FALSE;
		}
		elseif($result['j_type'] == 'F' && $this->session->userdata['permissions']['apt_can_read_family'] != 1)
		{
			return FALSE;
		}

		//permissions are good
		return $result;
	}

	// FUNCTION set the date and time of last update and who perfomed the update
	public function set_last_update($j_id, $a_id)
	{
		$sql = 'UPDATE
					journeys
				SET
					j_datetime_last_update = NOW(),
					j_last_update_by = "' . (int) $a_id . '"
				WHERE
					j_id = "' . (int) $j_id . '" ';

		$this->db->query($sql);
	}

	// FUNCTION combines journey and journey_info tables
	public function get_journey_info($j_id)
	{
		$sql = 'SELECT
					j_type,
					j_rc_id,
					j_family_or_carer_involved,
					j_status,
					j_published,
					IF(j_tier = NULL, "Not Set", j_tier) AS j_tier,
					IF(j_ndtms_valid = 1, "Yes", "No") as j_ndtms_valid,
					DATE_FORMAT(j_closed_date, "%d/%m/%Y") AS j_closed_date_format,
					DATE_FORMAT(j_date_of_referral, "%d/%m/%Y") AS j_date_of_referral_format,
					DATE_FORMAT(j_date_of_triage, "%d/%m/%Y") AS j_date_of_triage_format,
					DATE_FORMAT(ji_date_referral_received, "%d/%m/%Y") AS ji_date_referral_received_format,
					DATE_FORMAT(ji_date_rc_allocated, "%d/%m/%Y") AS ji_date_rc_allocated_format,
					DATE_FORMAT(ji_date_of_drug_treatment, "%d/%m/%Y") AS ji_date_of_drug_treatment_format,
					journeys_info.*
				FROM
					journeys,
					journeys_info
				WHERE
					j_id = "' . (int)$j_id . '"
					AND ji_j_id = j_id;';

		$result = $this->db->query($sql)->row_array();

		//Check permissions
		if($result['j_type'] == 'C' && $this->session->userdata['permissions']['apt_can_read_client'] != 1)
		{
			return FALSE;
		}
		elseif($result['j_type'] == 'F' && $this->session->userdata['permissions']['apt_can_read_family'] != 1)
		{
			return FALSE;
		}

		//result good
		return $result;
	}

	// FUNCTION update journey info
	public function update_journey_info($j_id)
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_edit_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_edit_family'] != 1)
		{
			return FALSE;
		}


		//what have we got to update with?
		$j_data = array();
		$ji_data = array();
		if($this->session->userdata('a_id') !== FALSE)
		{
			$j_data['j_last_update_by'] = $this->session->userdata('a_id');
		}
		if($this->input->post('j_rc_id') !== FALSE)
		{
			$j_data['j_rc_id'] = $this->input->post('j_rc_id');
		}
		if($this->input->post('j_date_of_referral') !== FALSE)
		{
			$j_data['j_date_of_referral'] = $this->input->post('j_date_of_referral');
		}
		if($this->input->post('j_family_or_carer_involved') !== FALSE)
		{
			$j_data['j_family_or_carer_involved'] = $this->input->post('j_family_or_carer_involved');
		}
		if($this->input->post('j_status') !== FALSE)
		{
			$j_data['j_status'] = $this->input->post('j_status');
		}
		if($this->input->post('j_published') !== FALSE)
		{
			$j_data['j_published'] = $this->input->post('j_published');
		}
		if($this->input->post('j_closed_date') !== FALSE && $this->input->post('j_closed_date') !== '')
		{
			$j_data['j_closed_date'] = $this->input->post('j_closed_date');
		}
		if($this->input->post('j_date_of_triage') !== FALSE)
		{
			$j_data['j_date_of_triage'] = $this->input->post('j_date_of_triage');
		}
		// update journeys table
		$sql = $this->db->update_string('journeys', $j_data, 'j_id = "' . (int)$j_id . '";');
		$this->db->query($sql);


		//ji data
		if($this->input->post('ji_rs_id') !== FALSE && $this->input->post('ji_rs_id') !== '')
		{
			$ji_data['ji_rs_id'] = (int)$this->input->post('ji_rs_id');
		}
		if($this->input->post('ji_referrers_name') !== FALSE)
		{
			$ji_data['ji_referrers_name'] = $this->input->post('ji_referrers_name');
		}
		if($this->input->post('ji_referrers_tel') !== FALSE)
		{
			$ji_data['ji_referrers_tel'] = $this->input->post('ji_referrers_tel');
		}
		if($this->input->post('ji_date_referral_received') !== FALSE && $this->input->post('ji_date_referral_received') !== '')
		{
			$ji_data['ji_date_referral_received'] = $this->input->post('ji_date_referral_received');
		}
		if($this->input->post('ji_date_rc_allocated') !== FALSE && $this->input->post('ji_date_rc_allocated') !== '')
		{
			$ji_data['ji_date_rc_allocated'] = $this->input->post('ji_date_rc_allocated');
		}
		if($this->input->post('ji_previously_treated') !== FALSE && $this->input->post('ji_previously_treated') !== '')
		{
			$ji_data['ji_previously_treated'] = (int)$this->input->post('ji_previously_treated');
		}
		if($this->input->post('ji_medication') !== FALSE)
		{
			$ji_data['ji_medication'] = $this->input->post('ji_medication');
		}
		if($this->input->post('ji_summary_of_needs') !== FALSE)
		{
			$ji_data['ji_summary_of_needs'] = $this->input->post('ji_summary_of_needs');
		}
		if($this->input->post('ji_additional_information') !== FALSE)
		{
			$ji_data['ji_additional_information'] = $this->input->post('ji_additional_information');
		}
		if($this->input->post('ji_exit_status') !== FALSE)
		{
			$ji_data['ji_exit_status'] = $this->input->post('ji_exit_status');
		}
		if($this->input->post('ji_discharge_reason') !== FALSE)
		{
			$ji_data['ji_discharge_reason'] = ($this->input->post('ji_discharge_reason') != "" ? $this->input->post('ji_discharge_reason') : NULL);
		}
		if($this->input->post('ji_triage_completed_by') !== FALSE)
		{
			$ji_data['ji_triage_completed_by'] = (int)$this->input->post('ji_triage_completed_by');
		}
		if($this->input->post('ji_flagged_as_risk') !== FALSE)
		{
			$ji_data['ji_flagged_as_risk'] = (int)$this->input->post('ji_flagged_as_risk');
		}
		if($this->input->post('ji_flagged_risk_summary') !== FALSE)
		{
			$ji_data['ji_flagged_risk_summary'] = $this->input->post('ji_flagged_risk_summary');
		}
		if($this->input->post('ji_date_of_drug_treatment') !== FALSE)
		{
			$ji_data['ji_date_of_drug_treatment'] = $this->input->post('ji_date_of_drug_treatment');
		}
		if($this->input->post('ji_referral_received_by') !== FALSE)
		{
			$ji_data['ji_referral_received_by'] = $this->input->post('ji_referral_received_by');
		}
		if($this->input->post('ji_rc_allocated_by') !== FALSE)
		{
			$ji_data['ji_rc_allocated_by'] = $this->input->post('ji_rc_allocated_by');
		}

		$sql = $this->db->update_string('journeys_info', $ji_data, 'ji_j_id = "' . (int)$j_id . '";');
		$this->db->query($sql);



		//update journey internal services
		if($this->input->post('j2is') !== FALSE)
		{
			//remove all existing client disabilities
			$sql = "DELETE FROM j2is WHERE j2is_j_id = '". (int)$j_id ."';";
			$this->db->query($sql);

			//add all the ones listed
			foreach($this->input->post('j2is') as $key => $is_id)
			{
				$j2is_data[] = "('". (int)$j_id ."', ". (int)$is_id .")";
			}
			$sql = "INSERT
						INTO j2is
						(j2is_j_id, j2is_is_id)
					VALUES
						". implode(', ', $j2is_data) ."
					;";
			$this->db->query($sql);
		}


		$this->session->set_flashdata('action', 'Journey information updated');
		return true;
	}


	public function get_full_journey_info($j_id)
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_read_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_read_family'] != 1)
		{
			return FALSE;
		}

		$sql = 'SELECT
					CONCAT(ci_fname, " ", ci_sname) AS ci_name,
					IF(ci_gender IS NULL, "N/A", IF(ci_gender = 1, "Male", "Female")) AS ci_gender,
					IF(ci_date_of_birth IS NULL, "N/A", DATE_FORMAT(ci_date_of_birth, "%d/%m/%Y")) AS ci_date_of_birth_format,
					DATE_FORMAT(CURDATE(), "%Y") - DATE_FORMAT(ci_date_of_birth, "%Y") - (DATE_FORMAT(CURDATE(), "00-%m-%d") < DATE_FORMAT(ci_date_of_birth, "00-%m-%d")) AS ci_age,
					ci_address,
					ci_post_code,
					ci_catchment_area,
					IF(ci_gp_code IS NULL, "N/A", CONCAT("#", ci_gp_code)) AS ci_gp_code,
					IF(ci_gp_name IS NULL, "N/A", ci_gp_name) AS ci_gp_name,
					IF(ci_tel_home IS NULL, "N/A", ci_tel_home) AS ci_tel_home,
					IF(ci_tel_mob IS NULL, "N/A", ci_tel_mob) AS ci_tel_mob,
					ci_nationality,
					ci_ethnicity,
					ci_religion,
					IF(ci_pregnant IS NULL, "N/A", IF(ci_pregnant = 1, "Yes", "No")) AS ci_pregnant,
					IF(ci_caf_completed IS NULL, "N/A", IF(ci_caf_completed = 1, "Yes", "No")) AS ci_caf_completed,
					ci_relationship_status,
					ci_sexuality,
					IF(ci_mental_health_issues IS NULL, "N/A", IF(ci_mental_health_issues = 1, "Yes", "No")) AS ci_mental_health_issues,
					IF(ci_learning_difficulties IS NULL, "N/A", IF(ci_learning_difficulties = 1, "Yes", "No")) AS ci_learning_difficulties,
					ci_disabilities,
					IF(ci_consents_to_ndtms IS NULL, "N/A", IF(ci_consents_to_ndtms = 1, "Yes", "No")) AS ci_consents_to_ndtms,
					ci_is_risk,
					ci_parental_status,
					ci_access_to_children,
					ci_no_of_children,
					ci_accommodation_need,
					ci_accommodation_status,
					ci_employment_status,
					IF(ci_smoker IS NULL, "N/A", IF(ci_smoker = 1, "Yes", "No")) AS ci_smoker,
					ci_contact,
					ci_next_of_kin_details,
					IF(ci_additional_information IS NULL, "N/A", ci_additional_information) AS ci_additional_information,
					j_id,
					j_c_id,
					j_type,
					rc_name AS j_rc_name,
					DATE_FORMAT(j_datetime_created, "%d/%m/%Y at %H:%i") AS j_datetime_created_format,
					DATE_FORMAT(j_datetime_last_update, "%d/%m/%Y at %H:%i") AS j_datetime_last_update_format,
					CONCAT(last_update_by.a_fname, " ", last_update_by.a_sname) AS j_last_update_by,
					DATE_FORMAT(j_date_of_referral, "%d/%m/%Y") AS j_date_of_referral_format,
					IF(j_date_first_assessment IS NULL, "N/A", DATE_FORMAT(j_date_first_assessment, "%d/%m/%Y")) AS j_date_first_assessment_format,
					IF(j_date_last_assessment IS NULL, "N/A", DATE_FORMAT(j_date_last_assessment, "%d/%m/%Y")) AS j_date_last_assessment_format,
					IF(j_family_or_carer_involved = 1, "Yes", "No") AS j_family_or_carer_involved,
					IF(j_status = 1, "Open", IF(j_status = 3, "Initial Contact", IF(j_status = 4, "Referral", "Closed"))) as j_status,
					j_published,
					IF(j_tier IS NULL, "Not Set", j_tier) AS j_tier,
					IF(j_ndtms_valid = 1, "Yes", "No") as j_ndtms_valid,
					IF(j_closed_date IS NULL, NULL, DATE_FORMAT(j_closed_date, "%d/%m/%Y")) AS j_closed_date_format,
					IF(ji_rs_id IS NULL, "N/A", rs_name) AS ji_referral_source,
					rs_type AS ji_rs_type,
					IF(ji_referrers_name IS NULL, "N/A", ji_referrers_name) AS ji_referrers_name,
					IF(ji_referrers_tel IS NULL, "N/A", ji_referrers_tel) AS ji_referrers_tel,
					IF(ji_date_referral_received IS NULL, "N/A", DATE_FORMAT(ji_date_referral_received, "%d/%m/%Y")) AS ji_date_referral_received_format,
					IF(ji_referral_received_by IS NULL, "N/A", referral_received_by.s_name) AS ji_referral_received_by,
					IF(ji_date_rc_allocated IS NULL, "N/A", DATE_FORMAT(ji_date_rc_allocated, "%d/%m/%Y")) AS ji_date_rc_allocated_format,
					IF(ji_rc_allocated_by IS NULL, "N/A", rc_allocated_by.s_name) AS ji_rc_allocated_by,
					IF(ji_previously_treated IS NULL, "N/A", IF(ji_previously_treated = 1, "Yes", "No")) AS ji_previously_treated,
					IF(ji_medication IS NULL, "N/A", ji_medication) AS ji_medication,
					IF(ji_summary_of_needs IS NULL, "N/A", ji_summary_of_needs) AS ji_summary_of_needs,
					IF(ji_additional_information IS NULL, "N/A", ji_additional_information) AS ji_additional_information,
					ji_exit_status,
					ji_discharge_reason,
					journey_assessments.*
				FROM
					journeys
				LEFT JOIN
					journeys_info
						ON ji_j_id = j_id
				LEFT JOIN
					clients_info
						ON ci_j_id = j_id
				LEFT JOIN
					journey_assessments
						ON jas_j_id = j_id AND jas_date = j_date_last_assessment
				LEFT JOIN
					recovery_coaches
						ON rc_id = j_rc_id
				LEFT JOIN
					admins AS last_update_by
						ON last_update_by.a_id = j_last_update_by
				LEFT JOIN
					staff AS referral_received_by
						ON referral_received_by.s_id = ji_referral_received_by
				LEFT JOIN
					staff AS rc_allocated_by
						ON rc_allocated_by.s_id = ji_rc_allocated_by
				LEFT JOIN
					referral_sources
						ON rs_id = ji_rs_id
				WHERE
					j_id = "' . (int)$j_id . '"';

		// if a journey is returned
		if($journey_info = $this->db->query($sql)->row_array())
		{
			// unserialize
			$journey_info['ci_contact'] = unserialize($journey_info['ci_contact']);
			$journey_info['ci_next_of_kin_details'] = unserialize($journey_info['ci_next_of_kin_details']);

			// get journey appointments
			$journey_info['appointments'] = $this->get_journey_appointments($j_id);

			// get journey events
			$journey_info['events'] = $this->get_journey_events($j_id);

			// get journey notes
			$journey_info['notes'] = $this->get_journey_notes($j_id);

			// get journey modalities
			$journey_info['modalities'] = $this->get_journey_modalities($j_id);
			// print $this->db->last_query();exit;

			//get disabilities
			$sql = "SELECT
						j2d_disability
					FROM
						j2disability
					WHERE
						j2d_j_id = '". (int)$j_id ."'
					;";
			$disabilities = $this->db->query($sql)->result_array();
			$this->load->config('datasets');
			$journey_info['disabilities'] = array();
			foreach($disabilities as $d)
			{
				$journey_info['disabilities'][ $d['j2d_disability'] ] = $this->config->config['disability_codes'][ $d['j2d_disability'] ];
			}

			return $journey_info;
		}

		// return false if no journey
		return false;
	}




	public function cache_journey($j_id = 0)
	{
		$this->_set_appointment_dates($j_id);

		$sql = 'INSERT INTO

					journeys_cache
					(
						jc_j_id,
						jc_access_support,
						jc_access_respite,
						jc_attend_parent,
						jc_attend_freedom,
						jc_improve_psych,
						jc_improve_child,
						jc_improve_social,
						jc_interval_1to1,
						jc_date_ass1,
						jc_date_csop1,
						jc_csop_total,
						jc_csop_interval,
						jc_last_event
					)

				SELECT

					j_id,

					/* Accessing support */
					(
						SELECT IF(COUNT(je_id) > 0, 1, 0)
						FROM journey_events
						LEFT JOIN event_types ON je_et_id = et_id
						WHERE je_j_id = j_id
						/* AND (et_ec_id = 2 OR et_id = 55) */
						AND et_id in (25, 27, 28, 42)
					) AS jc_access_support,

					/* Access respite */
					(
						SELECT IF(COUNT(je_id) > 0, 1, 0)
						FROM journey_events
						LEFT JOIN event_types ON je_et_id = et_id
						WHERE je_j_id = j_id
						/* AND et_ec_id = 8 */
						AND (et_ec_id = 9 OR et_id in (34, 54, 102, 98))
					) AS js_access_respite,

					/* Attending parent factor */
					(
						SELECT IF(COUNT(je_id) > 0, 1, 0)
						FROM journey_events
						WHERE je_j_id = j_id
						AND je_et_id = 57
					) AS jc_attend_parent,

					/* Attending freedom programme */
					(
						SELECT IF(COUNT(je_id) > 0, 1, 0)
						FROM journey_events
						WHERE je_j_id = j_id
						AND je_et_id = 73
					) AS jc_attend_freedom,

					/* Improvement in CSOP "physical and psychological health" */
					(
						SELECT IF( ROUND(AVG(scores_last.jacs_score)) > ROUND(AVG(scores_first.jacs_score)), 1, 0)
						FROM journey_assessments jas
						LEFT JOIN ass_criteria_lists acl ON jas.jas_acl_id = acl.acl_id
						LEFT JOIN (
							SELECT
								MIN(jas_id) AS first_ass_id,
								MAX(jas_id) AS last_ass_id,
								jas_j_id
							FROM journey_assessments
							GROUP BY jas_j_id
						) assessments ON jas.jas_j_id = assessments.jas_j_id
						LEFT JOIN journey_ass_criteria_scores scores_first ON first_ass_id = scores_first.jacs_jas_id
						LEFT JOIN journey_ass_criteria_scores scores_last ON last_ass_id = scores_last.jacs_jas_id
						WHERE jas.jas_j_id = j_id
						AND acl_type = "csop"
						AND scores_first.jacs_num IN (13, 14, 15, 16)
						AND scores_last.jacs_num IN (13, 14, 15, 16)
					) AS jc_improve_psych,

					/* Improvement in CSOP "safety of household" */
					(
						SELECT IF( ROUND(AVG(scores_last.jacs_score)) > ROUND(AVG(scores_first.jacs_score)), 1, 0)
						FROM journey_assessments jas
						LEFT JOIN ass_criteria_lists acl ON jas.jas_acl_id = acl.acl_id
						LEFT JOIN (
							SELECT
								MIN(jas_id) AS first_ass_id,
								MAX(jas_id) AS last_ass_id,
								jas_j_id
							FROM journey_assessments
							GROUP BY jas_j_id
						) assessments ON jas.jas_j_id = assessments.jas_j_id
						LEFT JOIN journey_ass_criteria_scores scores_first ON first_ass_id = scores_first.jacs_jas_id
						LEFT JOIN journey_ass_criteria_scores scores_last ON last_ass_id = scores_last.jacs_jas_id
						WHERE jas.jas_j_id = j_id
						AND acl_type = "csop"
						AND scores_first.jacs_num = 10
						AND scores_last.jacs_num = 10
					) AS jc_improve_child,

					/* Improvement in CSOP "relationship with wider community" */
					(
						SELECT IF( ROUND(AVG(scores_last.jacs_score)) > ROUND(AVG(scores_first.jacs_score)), 1, 0)
						FROM journey_assessments jas
						LEFT JOIN ass_criteria_lists acl ON jas.jas_acl_id = acl.acl_id
						LEFT JOIN (
							SELECT
								MIN(jas_id) AS first_ass_id,
								MAX(jas_id) AS last_ass_id,
								jas_j_id
							FROM journey_assessments
							GROUP BY jas_j_id
						) assessments ON jas.jas_j_id = assessments.jas_j_id
						LEFT JOIN journey_ass_criteria_scores scores_first ON first_ass_id = scores_first.jacs_jas_id
						LEFT JOIN journey_ass_criteria_scores scores_last ON last_ass_id = scores_last.jacs_jas_id
						WHERE jas.jas_j_id = j_id
						AND acl_type = "csop"
						AND scores_first.jacs_num = 12
						AND scores_last.jacs_num = 12
					) AS jc_improve_social,

					/* Interval (num days) between 1 to 1 appointment event types */
					(
						SELECT ROUND(AVG(DATEDIFF(
							je1.je_datetime,
							(
								SELECT MAX(je_datetime)
								FROM journey_events je2
								WHERE je2.je_datetime < je1.je_datetime
								AND je2.je_j_id = je1.je_j_id
								AND je2.je_et_id = je1.je_et_id
							)
						)))
						FROM
						journey_events je1
						WHERE je_j_id = j_id
						AND je_et_id = 6
						ORDER BY je_datetime ASC
					) AS jc_interval_1to1,

					/*  Date of first assessment */
					(
						SELECT DATE(MIN(je_datetime))
						FROM journey_events
						WHERE je_j_id = j_id
						AND je_et_id = 20
					) AS jc_date_ass1,

					/* Date of first CSOP */
					(
						SELECT MIN(jas_date)
						FROM journey_assessments
						LEFT JOIN ass_criteria_lists ON jas_acl_id = acl_id
						WHERE jas_j_id = j_id
						AND acl_type = "csop"
					) AS jc_date_csop1,

					/* CSOP count */
					(
						SELECT COUNT(jas_id)
						FROM journey_assessments
						LEFT JOIN ass_criteria_lists ON jas_acl_id = acl_id
						WHERE jas_j_id = j_id
						AND acl_type = "csop"
					) AS jc_csop_total,

					/* CSOP interval */
					(
						SELECT ROUND(AVG(DATEDIFF(
							jas_date,
							(
								SELECT MAX(jas_date)
								FROM journey_assessments jas2
								WHERE jas2.jas_date < jas1.jas_date
								AND jas2.jas_j_id = jas1.jas_j_id
								AND jas2.jas_acl_id = jas1.jas_acl_id
							)
						)))
						FROM journey_assessments jas1
						LEFT JOIN ass_criteria_lists ON jas_acl_id = acl_id
						WHERE jas_j_id = j_id
						AND acl_type = "csop"
						ORDER BY jas_date ASC
					) AS jc_csop_interval,

					/* Last Journey Event */
					(
						SELECT MAX(je_datetime)
						FROM journey_events
						WHERE je_j_id = j_id
						LIMIT 1
					) AS jc_last_event

				FROM
					journeys
				WHERE
					j_id = ?
				LIMIT 1
				ON DUPLICATE KEY UPDATE
					jc_access_support = IF(VALUES(jc_access_support) IS NULL, 0, VALUES(jc_access_support)),
					jc_access_respite = IF(VALUES(jc_access_respite) IS NULL, 0, VALUES(jc_access_respite)),
					jc_attend_parent  = IF(VALUES(jc_attend_parent)  IS NULL, 0, VALUES(jc_attend_parent)),
					jc_attend_freedom = IF(VALUES(jc_attend_freedom) IS NULL, 0, VALUES(jc_attend_freedom)),
					jc_improve_psych  = IF(VALUES(jc_improve_psych)  IS NULL, 0, VALUES(jc_improve_psych)),
					jc_improve_child  = IF(VALUES(jc_improve_child)  IS NULL, 0, VALUES(jc_improve_child)),
					jc_improve_social = IF(VALUES(jc_improve_social) IS NULL, 0, VALUES(jc_improve_social)),
					jc_interval_1to1  = IF(VALUES(jc_interval_1to1)  IS NULL, 0, VALUES(jc_interval_1to1)),
					jc_date_ass1      = VALUES(jc_date_ass1),
					jc_date_csop1     = VALUES(jc_date_csop1),
					jc_csop_total     = IF(VALUES(jc_csop_total)     IS NULL, 0, VALUES(jc_csop_total)),
					jc_csop_interval  = IF(VALUES(jc_csop_interval)  IS NULL, 0, VALUES(jc_csop_interval)),
					jc_last_event     = VALUES(jc_last_event)
		';

		return $this->db->query($sql, array($j_id));
	}



	// deletes a journey permanently from the db
	public function delete_journey($j_id)
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_delete_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_delete_family'] != 1)
		{
			return FALSE;
		}

		$tables = array(
			'journeys'             => 'j_id',
			'journeys_cache'       => 'jc_j_id',
			'journeys_info'        => 'ji_j_id',
			'journey_alcohol'      => 'jal_j_id',
			'journey_appointments' => 'ja_j_id',
			'journey_assessments'  => 'jas_j_id',
			'journey_ass_criteria' => 'jac_j_id',
			'journey_drugs'        => 'jd_j_id',
			'journey_events'       => 'je_j_id',
			'journey_family'       => 'jf_j_id',
			'journey_notes'        => 'jn_j_id',
			'journey_offending'    => 'jo_j_id',
			'clients_info'         => 'ci_j_id',
			'family_clients'       => 'fc_j_id',
			'messages'             => 'm_j_id',
		);

		$err = FALSE;

		// Loop through all the tables and remove entries relating to the journey
		foreach ($tables as $table => $key)
		{
			$sql = 'DELETE FROM `' . $table . '` WHERE `' . $key . '` = ' . (int) $j_id;
			if ( ! $this->db->query($sql))
			{
				$err = TRUE;
				break;
			}
		}

		if ($err)
		{
			return FALSE;
		}
		else
		{
			// set notification
			$this->session->set_flashdata('action', 'Journey #' . $j_id . ' successfully deleted');

			// set log
			$this->log_model->set('Journey #' . $j_id . ' deleted.');

			// successful delete - return true
			return TRUE;
		}
	}




	// ========================================================================
	// Journey Appointment
	// ========================================================================


	// returns a formatted array of all appointments assigned to a journey
	public function get_journey_appointments($j_id)
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_read_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_read_family'] != 1)
		{
			return FALSE;
		}

		$sql = 'SELECT
					ja_id,
					ja_je_id,
					IF(ja_date_offered IS NULL, "N/A", DATE_FORMAT(ja_date_offered, "%d/%m/%Y")) AS ja_date_offered_format,
					DATE_FORMAT(ja_datetime, "%d/%m/%Y at %H:%i") AS ja_datetime_format,
					rc_name AS ja_rc_name,
					ja_notes,
					dr_name,
					IF(ja_attended IS NULL, "N/A", IF(ja_attended = 1, "Yes", "No")) AS ja_attended,
					IF(ja_attended = 2, dr_name, NULL) AS ja_dr_name,
					ja_length,
					je_published,
					je_added_a_id
				FROM
					journey_appointments
				LEFT JOIN
					recovery_coaches
						ON rc_id = ja_rc_id
				LEFT JOIN
					dna_reasons
						ON dr_id = ja_dr_id
				LEFT JOIN
					journey_events
						ON ja_je_id = je_id
				WHERE
					ja_j_id = "' . (int)$j_id . '"
				ORDER BY
					ja_datetime DESC';

		//$explain = $this->db->query($sql)->row_array(); print_r($explain); exit;

		return $this->db->query($sql)->result_array();
	}

	// get single appointment information
	public function get_appointment($ja_id = 0, $ja_je_id = 0)
	{
		$sql = 'SELECT
					ja_id,
					ja_je_id,
					ja_rc_id,
					ja_notes,
					ja_attended,
					ja_dr_id,
					ja_length,
					DATE_FORMAT(ja_date_offered, "%d/%m/%Y") AS ja_date_offered,
					DATE_FORMAT(DATE(ja_datetime), "%d/%m/%Y") AS date_of_appt,
					HOUR(ja_datetime) AS hour_of_appt,
					MINUTE(ja_datetime) AS minute_of_appt
				FROM
					journey_appointments
				WHERE
					1 = 1 ';

		if ($ja_id)
			$sql .= ' AND ja_id = ' . (int) $ja_id . ' ';

		if ($ja_je_id)
			$sql .= ' AND ja_je_id = ' . (int) $ja_je_id . ' ';

		return $this->db->query($sql)->row_array();
	}

	// set an appointment for a specific journey
	public function set_appointment($j_id, $appt = FALSE)
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_edit_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_edit_family'] != 1)
		{
			return FALSE;
		}

		if($appt)
		{
			$sql = 'UPDATE
						journey_appointments
					SET
						ja_j_id = ?,
						ja_datetime = ?,
						ja_date_offered = ?,
						ja_rc_id = ?,
						ja_notes = ?,
						ja_attended = ?,
						ja_dr_id = ?,
						ja_length = ?
					WHERE
						ja_id = "' . (int)$appt['ja_id'] . '"';
		}
		else
		{
			$sql = 'INSERT INTO
						journey_appointments
					SET
						ja_j_id = ?,
						ja_datetime = ?,
						ja_date_offered = ?,
						ja_rc_id = ?,
						ja_notes = ?,
						ja_attended = ?,
						ja_dr_id = ?,
						ja_length = ? ';
		}

		$this->db->query($sql, array($j_id,
									 $this->input->post('ja_date') . ' ' . $this->input->post('time_hour') . ':' . $this->input->post('time_minute') . ':00',
									 $this->input->post('ja_date_offered'),
									 $this->input->post('ja_rc_id'),
									 ($this->input->post('ja_notes') ? $this->input->post('ja_notes') : NULL),
									 ($this->input->post('ja_attended') ? $this->input->post('ja_attended') : NULL),
									 ($this->input->post('ja_dr_id') ? $this->input->post('ja_dr_id') : NULL),
									 ($this->input->post('ja_length') ? $this->input->post('ja_length') : NULL)));

		// get appointment id
		$appt_id = ($appt ? $appt['ja_id'] : $this->db->insert_id());

		// set first appointment and triage date
		$this->_set_appointment_dates($j_id);

		// return appointment id
		return $appt_id;
	}


	// delete a single appointment
	public function delete_appointment($j_id, $ja_id)
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_edit_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_edit_family'] != 1)
		{
			return FALSE;
		}

		$sql = 'DELETE FROM
					journey_appointments
				WHERE
					ja_id = "' . (int)$ja_id . '";';

		$this->db->query($sql);

		// set first appointment and triage date
		$this->_set_appointment_dates($j_id);

		$this->session->set_flashdata('action', 'Appointment deleted');
	}

	// set triage date for a journey
	protected function _set_appointment_dates($j_id)
	{
		// get first appointment date
		$sql = 'SELECT
					ja_date_offered,
					DATE(ja_datetime) ja_date
				FROM
					journey_appointments
				WHERE
					ja_j_id = "' . (int)$j_id . '"
				ORDER BY
					ja_datetime ASC
				LIMIT 1;';

		// commit query and return row
		$first_appointment = $this->db->query($sql)->row_array();

		// get triage date
		$sql = 'SELECT
					DATE(ja_datetime) ja_date
				FROM
					journey_appointments
				WHERE
					ja_j_id = "' . (int)$j_id . '"
					AND ja_attended = 1
				ORDER BY
					ja_datetime ASC
				LIMIT 1;';

		// commit query and return row
		$triage_appointment = $this->db->query($sql)->row_array();

		// update journey with triage date
		$sql = 'UPDATE
					journeys
				SET
					j_date_first_appointment_offered = ?,
					j_date_first_appointment = ?,
					j_date_of_triage = ?
				WHERE
					j_id = "' . (int)$j_id . '";';

		// commit query
		$this->db->query($sql, array(@$first_appointment['ja_date_offered'], @$first_appointment['ja_date'], @$triage_appointment['ja_date']));

		// Update the journey info 'last appointment date' field
		$sql = 'UPDATE
					journeys_info ji
				SET
					ji_date_last_appt = (
						SELECT ja_datetime
						FROM journey_appointments ja
						WHERE ja_j_id = ji_j_id
						AND ja_datetime != "0000-00-00"
						AND ja_datetime IS NOT NULL
						ORDER BY ja_datetime DESC
						LIMIT 1
					)
				WHERE
					ji_j_id = ?
				LIMIT 1';

		$this->db->query($sql, array($j_id));
	}




	// ========================================================================
	// Journey events
	// ========================================================================




	// returns formatted array of all events that occured during journey
	public function get_journey_events($j_id)
	{
		$sql = 'SELECT
					je_id,
					DATE_FORMAT(je_datetime, "%d/%m/%Y at %H:%i") AS je_datetime_format,
					DATE_FORMAT(je_created_datetime, "%d/%m/%Y at %H:%i") AS je_created_datetime_format,
					et_name AS je_et_name,
					ec_name AS je_ec_name,
					IF(je_notes IS NULL, "N/A", je_notes) AS je_notes,
					IF(je_rc_id IS NULL, "N/A", rc_name) AS key_worker,
					je_published,
					je_added_a_id,
					IF(je_added_a_id IS NULL, "N/A", added_admin.a_fname) AS added_by,
					IF(je_updated_a_id IS NULL, "N/A", added_admin.a_sname) AS updated_by
				FROM
					journey_events
				LEFT JOIN
					event_types
					ON je_et_id = et_id
				LEFT JOIN
					event_categories
					ON et_ec_id = ec_id
				LEFT JOIN
					recovery_coaches ON je_rc_id = rc_id
				LEFT JOIN
					admins added_admin ON je_added_a_id = added_admin.a_id
				LEFT JOIN
					admins updated_admin ON je_updated_a_id = updated_admin.a_id
				WHERE
					je_j_id = "' . (int)$j_id . '"
				ORDER BY je_datetime DESC';

		return $this->db->query($sql)->result_array();
	}

	// get single event information
	public function get_event($je_id)
	{
		$sql = 'SELECT
					je_id,
					je_et_id,
					et_ec_id AS je_ec_id,
					je_notes,
					DATE_FORMAT(DATE(je_datetime), "%d/%m/%Y") AS date_of_event,
					DATE_FORMAT(je_created_datetime, "%d/%m/%Y at %H:%i") AS je_created_datetime_format,
					HOUR(je_datetime) AS hour_of_event,
					MINUTE(je_datetime) AS minute_of_event,
					DATE_FORMAT(DATE(je_date_offered), "%d/%m/%Y") AS date_offered,
					je_rc_id,
					IF(rc_name IS NULL, "N/A", rc_name) AS key_worker,
					je_attended,
					je_published,
					je_added_a_id
				FROM
					journey_events
				LEFT JOIN
					event_types ON je_et_id = et_id
				LEFT JOIN
					recovery_coaches ON je_rc_id = rc_id
				WHERE
					je_id = "' . (int)$je_id . '"';

		return $this->db->query($sql)->row_array();
	}

	// set an event for a specific journey
	public function set_event($j_id, $event = FALSE)
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_edit_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_edit_family'] != 1)
		{
			return FALSE;
		}

		if($event)
		{
			$sql = 'UPDATE
						journey_events
					SET
						je_j_id = ?,
						je_datetime = ?,
						je_et_id = ?,
						je_notes = ?,
						je_date_offered = ?,
						je_rc_id = ?,
						je_attended = ?,
						je_updated_a_id = ?,
						je_published = ?
					WHERE
						je_id = "' . (int)$event['je_id'] . '"';
		}
		else
		{
			$sql = 'INSERT INTO
						journey_events
					SET
						je_j_id = ?,
						je_datetime = ?,
						je_et_id = ?,
						je_notes = ?,
						je_date_offered = ?,
						je_rc_id = ?,
						je_attended = ?,
						je_added_a_id = ?,
						je_published = ?,
						je_created_datetime = ' . $this->db->escape(date('Y-m-d H:i:s')) . ' ';
		}

		$this->db->query($sql, array(
				$j_id,
				$this->input->post('je_date') . ' ' . $this->input->post('time_hour') . ':' . $this->input->post('time_minute') . ':00',
				$this->input->post('je_et_id'),
				($this->input->post('je_notes') ? $this->input->post('je_notes') : NULL),
				parse_date($this->input->post('je_date_offered')),
				(int)$this->input->post('je_rc_id'),
				(int)$this->input->post('je_attended'),
				(int)$this->session->userdata('a_id'),
				($this->input->post('submit') == 'publish' ? 1 : 0)
			));

		// return event id
		return ($event ? $event['je_id'] : $this->db->insert_id());
	}

	// delete a single event
	public function delete_event($je_id)
	{
		//permissions check first
		$jtype = $this->check_journey_type($je_id);
		if($jtype == 'C' && $this->session->userdata['permissions']['apt_can_edit_client'] != 1)
		{
			$this->session->set_flashdata('action', 'You do not have permission to edit this journey.');
			return FALSE;
		}
		elseif($jtype == 'F' && $this->session->userdata['permissions']['apt_can_edit_family'] != 1)
		{
			$this->session->set_flashdata('action', 'You do not have permission to edit this journey.');
			return FALSE;
		}

		$sql = 'DELETE FROM
					journey_events
				WHERE
					je_id = "' . (int)$je_id . '";';

		$this->db->query($sql);

		$this->session->set_flashdata('action', 'Event deleted');
	}




	// ========================================================================
	// Journey notes
	// ========================================================================




	// returns formatted array of all notes set by recovery coaches during journey
	public function get_journey_notes($j_id)
	{
		$sql = 'SELECT
					jn_id,
					DATE_FORMAT(jn_date, "%d/%m/%Y") AS jn_date_format,
					rc_name AS jn_rc_name,
					jn_notes
				FROM
					journey_notes,
					recovery_coaches
				WHERE
					jn_j_id = "' . (int)$j_id . '"
					AND rc_id = jn_rc_id';

		return $this->db->query($sql)->result_array();
	}

	// get single note information
	public function get_note($jn_id)
	{
		$sql = 'SELECT
					jn_id,
					jn_rc_id,
					jn_notes,
					DATE_FORMAT(DATE(jn_date), "%d/%m/%Y") AS date_of_note
				FROM
					journey_notes
				WHERE
					jn_id = "' . (int)$jn_id . '"';

		return $this->db->query($sql)->row_array();
	}

	// set notes for a specific journey
	public function set_notes($j_id, $note = FALSE)
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_edit_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_edit_family'] != 1)
		{
			return FALSE;
		}

		if($note)
		{
			$sql = 'UPDATE
						journey_notes
					SET
						jn_j_id = ?,
						jn_date = ?,
						jn_rc_id = ?,
						jn_notes = ?
					WHERE
						jn_id = "' . (int)$note['jn_id'] . '"';

			$this->session->set_flashdata('action', 'Notes updated');
		}
		else
		{
			$sql = 'INSERT INTO
						journey_notes
					SET
						jn_j_id = ?,
						jn_date = ?,
						jn_rc_id = ?,
						jn_notes = ? ';

			$this->session->set_flashdata('action', 'Notes added');
		}

		$this->db->query($sql, array($j_id,
									 $this->input->post('jn_date'),
									 $this->input->post('jn_rc_id'),
									 ($this->input->post('jn_notes') ? $this->input->post('jn_notes') : NULL)));

		// return note id
		return ($note ? $note['jn_id'] : $this->db->insert_id());
	}

	// delete a single note
	public function delete_notes($jn_id)
	{
		//permissions check first
		$jtype = $this->check_journey_type($jn_id);
		if($jtype == 'C' && $this->session->userdata['permissions']['apt_can_edit_client'] != 1)
		{
			$this->session->set_flashdata('action', 'You do not have permission to edit this journey.');
			return FALSE;
		}
		elseif($jtype == 'F' && $this->session->userdata['permissions']['apt_can_edit_family'] != 1)
		{
			$this->session->set_flashdata('action', 'You do not have permission to edit this journey.');
			return FALSE;
		}
		$sql = 'DELETE FROM
					journey_notes
				WHERE
					jn_id = "' . (int)$jn_id . '";';

		$this->db->query($sql);

		$this->session->set_flashdata('action', 'Notes deleted');
	}




	// ========================================================================
	// Journey modalities
	// ========================================================================




	// returns formatted array of all modalities
	public function get_journey_modalities($j_id, $date_format = '%d/%m/%Y')
	{
		$sql = "SELECT
					mod_id AS MODID,
					DATE_FORMAT(DATE(mod_cpdate), '$date_format') AS CPLANDT,
					mod_treatment AS MODAL,
					DATE_FORMAT(DATE(mod_refdate), '$date_format') AS REFMODDT,
					DATE_FORMAT(DATE(mod_firstapptdate), '$date_format') AS FAOMODDT,
					mod_intsetting AS MODSET,
					DATE_FORMAT(DATE(mod_start), '$date_format') AS MODST,
					DATE_FORMAT(DATE(mod_end), '$date_format') AS MODEND,
					mod_exit AS MODEXIT
				FROM
					journey_modalities
				WHERE
					mod_j_id = ?";

		return $this->db->query($sql, (int)$j_id)->result_array();
	}

	// get single modality information
	public function get_modality($mod_id, $format = FALSE, $date_format = '%d/%m/%Y')
	{
		if($format)
		{
			$sql = "SELECT
						mod_id AS MODID,
						DATE_FORMAT(DATE(mod_cpdate), '$date_format') AS CPLANDT,
						mod_treatment AS MODAL,
						DATE_FORMAT(DATE(mod_refdate), '$date_format') AS REFMODDT,
						DATE_FORMAT(DATE(mod_firstapptdate), '$date_format') AS FAOMODDT,
						mod_intsetting AS MODSET,
						DATE_FORMAT(DATE(mod_start), '$date_format') AS MODST,
						DATE_FORMAT(DATE(mod_end), '$date_format') AS MODEND,
						mod_exit AS MODEXIT
					FROM
						journey_modalities
					WHERE
						mod_id = ?";
		}
		else
		{
			$sql = 'SELECT
						*
					FROM
						journey_modalities
					WHERE
						mod_id = ?';
		}

		return $this->db->query($sql, (int)$mod_id )->row_array();
	}

	// set modality for a specific journey
	public function set_modality($j_id, $modality = FALSE)
	{
		if($modality)
		{
			$sql = 'UPDATE
						journey_modalities
					SET
						mod_j_id = ?,
						mod_cpdate = ?,
						mod_treatment = ?,
						mod_refdate = ?,
						mod_firstapptdate = ?,
						mod_intsetting = ?,
						mod_start = ?,
						mod_end = ?,
						mod_exit = ?
					WHERE
						mod_id = "' . (int)$modality['MODID'] . '"';

			$this->session->set_flashdata('action', 'Modality updated');
		}
		else
		{
			$sql = 'INSERT INTO
						journey_modalities
					SET
						mod_j_id = ?,
						mod_cpdate = ?,
						mod_treatment = ?,
						mod_refdate = ?,
						mod_firstapptdate = ?,
						mod_intsetting = ?,
						mod_start = ?,
						mod_end = ?,
						mod_exit = ?';

			$this->session->set_flashdata('action', 'Modality added');
		}

		$this->db->query($sql, array(
				$j_id,
				$this->input->post('mod_cpdate'),
				$this->input->post('mod_treatment'),
				$this->input->post('mod_refdate'),
				$this->input->post('mod_firstapptdate'),
				(int)$this->input->post('mod_intsetting'),
				$this->input->post('mod_start'),
				$this->input->post('mod_end'),
				$this->input->post('mod_exit'),
			));

		// return note id
		return ($modality ? $modality['MODID'] : $this->db->insert_id());
	}

	// delete a single modality
	public function delete_modality($mod_id)
	{
		$sql = 'DELETE FROM
					journey_modalities
				WHERE
					mod_id = ?;';

		$this->db->query($sql, (int)$mod_id);

		$this->session->set_flashdata('action', 'Notes deleted');
	}





	/**
	 * Intelligently update the CSOP and TOP last & due dates for a journey.
	 *
	 * When an assessment has been completed, the "last" ass. date for that type is set to it.
	 * The next due date for that assessment type is the date of assessment + 8 weeks.
	 *
	 * Call this function when a relevant assessment has been completed.
	 *
	 * @param int $j_id     ID of journey to update dates for
	 * @return bool     True on successful update or no update necessary
	 */
	public function set_journey_ass_dates($j_id = 0)
	{
		$this->load->model('assessments_model');
		$ji_data = array();

		// Get last csop assessment. "Last" CSOP date is set to this
		$last_csop = $this->assessments_model->get_last_assessment($j_id, 'csop');

		// Get last top assessment. "Last" TOP date is set to this
		$last_top = $this->assessments_model->get_last_assessment($j_id, 'top');

		// interval for next due date
		$interval = new DateInterval("P8W");

		if ($last_csop)
		{
			$jas_date = new DateTime($last_csop['jas_date']);

			$ji_data['ji_csop_last'] = $jas_date->format('Y-m-d');

			// does this journey have a CSOP Discharge event? (Event ID: 3)
			$this->db->select('COUNT(*) as count');
			$this->db->where('je_j_id', $j_id);
			$this->db->where('je_et_id', 3);
			$query = $this->db->get('journey_events')->row();

			if ($query->count > 0)
			{
				$ji_data['ji_csop_due'] = NULL;
			}
			else
			{
				$ji_data['ji_csop_due'] = $jas_date->add($interval)->format('Y-m-d');
			}
		}
		else
		{
			$ji_data['ji_csop_last'] = NULL;
			$ji_data['ji_csop_due'] = NULL;
		}

		if ($last_top)
		{
			$jas_date = new DateTime($last_top['jas_date']);

			$ji_data['ji_top_last'] = $jas_date->format('Y-m-d');
			$ji_data['ji_top_due'] = $jas_date->add($interval)->format('Y-m-d');
		}
		else
		{
			$ji_data['ji_top_last'] = NULL;
			$ji_data['ji_top_due'] = NULL;
		}

		if ( ! empty($ji_data))
		{
			$sql = $this->db->update_string('journeys_info', $ji_data, 'ji_j_id = ' . (int) $j_id);
			return $this->db->query($sql);
		}

		return TRUE;
	}




	// ========================================================================
	// CSV outputs
	// ========================================================================




	// returns journeys for ndtms csv
	public function get_ndtms_csv()
	{
		$sql = 'SELECT
					ci_catchment_area,
					SUBSTR(ci_fname, 1, 1) AS FINITIAL,
					SUBSTR(ci_sname, 1, 1) AS SINITIAL,
					ci_date_of_birth AS DOB,
					IF(ci_gender = 1, "M", "F") AS SEX,
					ci_ethnicity AS ETHNIC,
					ci_nationality AS NATION,
					j_date_of_referral AS REFLD,
					j_c_id AS CLIENTID,
					j_id AS EPISODID,
					IF(ci_consents_to_ndtms = 1, "Y", "N") AS CONSENT,
					IF(ji_previously_treated IS NULL, "", IF(ji_previously_treated = 1, "Y", "N")) AS PREVTR,
					ci_post_code AS PC,
					ci_accommodation_need AS ACCMNEED,
					ci_parental_status AS PRNTSTAT,
					ci_gp_code AS GPPTCE,
					jd_substance_1 AS DRUG1,
					jd_substance_1_age AS DRUG1AGE,
					jd_substance_1_route AS ROUTE,
					IF(jd_substance_2 IS NULL, "9996", jd_substance_2) AS DRUG2,
					IF(jd_substance_3 IS NULL, "9997", jd_substance_3) AS DRUG3,
					rs_type AS RFLS,
					j_date_of_triage AS TRIAGED,
					j_date_first_assessment,
					j_date_last_assessment,
					jd_injecting AS INJSTAT,
					ci_no_of_children AS CHILDWTH,
					IF(ci_pregnant IS NULL, "", IF(ci_pregnant = 1, "Y", "N")) AS PREGNANT,
					jal_last_28_drinking_days AS ALCDDAYS,
					jal_avg_daily_units AS ALCUNITS,
					IF(ci_mental_health_issues IS NULL OR ci_learning_difficulties IS NULL, NULL, IF(ci_mental_health_issues = 1 OR ci_learning_difficulties = 1, "Y", "N")) AS DUALDIAG,
					jd_hep_c_test_date AS HEPCTSTD,
					jd_hep_c_intervention AS HEPCSTAT,
					jd_hep_b_vac_count AS HEPBVAC,
					jd_hep_b_intervention AS HEPBSTAT,
					j_closed_date AS DISD,
					ji_discharge_reason AS DISRSN,
					IF(ji_previously_treated = 1, "Y", "N") AS PREVTR,
					IF(mod_j_id IS NULL, "9", mod_treatment) AS MODAL,
					j_date_first_appointment AS FAOMODDT,
					ji_exit_status AS MODEXIT,
					IF(ji_top_last IS NULL, NULL, ji_top_last) AS TOPDATE,
					IF(ji_top_last IS NULL, NULL, 1) AS TOPID,
					jd_prev_hep_b_infection AS PREVHEPB,
					IF(jd_hep_c_positive IS NULL, NULL, IF(jd_hep_c_positive = 1, "Y", "N")) AS HEPCPOS,
					ci_sexuality AS SEXUAL,
					ci_employment_status AS EMPSTAT,
					jal_last_28_drinking_days AS ALCUSE,
					IF(jd_injected_in_last_month IS NULL, NULL, IF(jd_injected_in_last_month = 1, "Y", "N")) AS INJECT,
					IF(jd_hep_b_prev_positive IS NULL, NULL, IF(jd_hep_b_prev_positive = 1, "Y", "N")) AS PREVHEPB,
					COUNT(mod_j_id) AS MODID_COUNT
				FROM
					journeys
				LEFT JOIN
					journeys_info
						ON ji_j_id = j_id
				LEFT JOIN
					clients_info
						ON ci_j_id = j_id
				LEFT JOIN
					journey_drugs
						ON jd_j_id = j_id
				LEFT JOIN
					referral_sources
						ON rs_id = ji_rs_id
				LEFT JOIN
					journey_alcohol
						ON jal_j_id = j_id
				LEFT JOIN
					journey_modalities
					ON j_id = mod_j_id
				WHERE
					j_ndtms_valid = 1
				AND
					j_type = "C" ';

		if(@$_GET['j_id'])
			$sql .= ' AND j_id = "' . (int)$_GET['j_id'] . '" ';

		if(@$_GET['j_c_id'])
			$sql .= ' AND j_c_id = "' . (int)$_GET['j_c_id'] . '" ';

		if(@$_GET['c_sname'])
			$sql .= ' AND CONVERT(ci_sname USING latin1) = "' . mysql_real_escape_string($_GET['c_sname']) . '" ';

		if(@$_GET['date_from'])
			$sql .= ' AND j_date_of_referral >= "' . parse_date($_GET['date_from']) . '" ';

		if(@$_GET['date_to'])
			$sql .= ' AND j_date_of_referral <= "' . parse_date($_GET['date_to']) . '" ';

		if(@$_GET['c_catchment_area'])
			$sql .= ' AND ci_catchment_area = "' . mysql_real_escape_string($_GET['c_catchment_area']) . '" ';

		if(@$_GET['j_status'])
			$sql .= ' AND j_status = "' . (int)$_GET['j_status'] . '" ';

		if(@$_GET['j_published'])
			$sql .= ' AND j_published = "' . (int)$_GET['j_published'] . '" ';

		$sql .= ' GROUP BY j_id';

		return $this->db->query($sql)->result_array();
	}

	// returns array of journeys for performance output csv
	public function get_performance_output_csv()
	{
		$sql = 'SELECT
					j_c_id,
					ci_fname,
					ci_sname,
					ci_date_of_birth,
					ci_catchment_area,
					IF(ci_authority_code IS NULL, "Unknown", ci_authority_code) AS ci_authority_code,
					ci_sexuality,
					ci_religion,
					rc_name,
					j_date_of_referral,
					j_closed_date,
					rs_type,
					rs_name,
					IF(j_date_first_appointment_offered = "0000-00-00", "N/A", j_date_first_appointment_offered) AS j_date_first_appointment_offered,
					j_date_of_triage,
					ji_date_rc_allocated,
					ji_date_last_appt,
					ji_discharge_reason,
					IF(ci_gender = 1, "M", "F") AS ci_gender,
					ci_post_code,
					ci_ethnicity,
					IF(ci_consents_to_ndtms IS NULL, "", IF(ci_consents_to_ndtms = 1, "Y", "N")) AS ci_consents_to_ndtms,
					jc_date_ass1,
					DATE_FORMAT(jc_last_event,"%Y-%m-%d") AS last_seen
				FROM
					journeys
				LEFT JOIN
					journeys_cache
						ON jc_j_id = j_id
				LEFT JOIN
					journeys_info
						ON ji_j_id = j_id
				LEFT JOIN
					clients_info
						ON ci_j_id = j_id
				LEFT JOIN
					recovery_coaches
						ON  rc_id = j_rc_id
				LEFT JOIN
					referral_sources
						ON rs_id = ji_rs_id
				WHERE
					1 = 1 ';

		if(@$_GET['j_id'])
			$sql .= ' AND j_id = "' . (int)$_GET['j_id'] . '" ';

		if(@$_GET['j_type'])
			$sql .= ' AND j_type = ' . $this->db->escape($_GET['j_type']) . ' ';

		//permission check
		if($this->session->userdata('can_read_family') != 1 || $this->session->userdata['permissions']['apt_can_read_family'] != 1)
			$sql .= ' AND j_type != "F" ';

		if($this->session->userdata('can_read_client') != 1 || $this->session->userdata['permissions']['apt_can_read_client'] != 1)
			$sql .= ' AND j_type != "C" ';


		if(@$_GET['c_sname'])
			$sql .= ' AND CONVERT(ci_sname USING latin1) = "' . mysql_real_escape_string($_GET['c_sname']) . '" ';

		if(@$_GET['date_from'])
			$sql .= ' AND j_date_of_referral >= "' . parse_date($_GET['date_from']) . '" ';

		if(@$_GET['date_to'])
			$sql .= ' AND j_date_of_referral <= "' . parse_date($_GET['date_to']) . '" ';

		if(@$_GET['c_catchment_area'])
			$sql .= ' AND ci_catchment_area = "' . mysql_real_escape_string($_GET['c_catchment_area']) . '" ';

		if(@$_GET['j_status'])
			$sql .= ' AND j_status = "' . (int) $_GET['j_status'] . '" ';

		if(@$_GET['j_published'])
			$sql .= ' AND j_published = "' . (int) $_GET['j_published'] . '" ';

		if(@$_GET['j_ndtms_valid'])
			$sql .= ' AND j_ndtms_valid = "' . (int) $_GET['j_ndtms_valid'] . '" ';

		return $this->db->query($sql)->result_array();
	}



	// returns array of journeys for performance output csv
	public function get_appointment_output_csv()
	{
		$sql = 'SELECT
					j_c_id,
					ja_id,
					rc_name,
					DATE_FORMAT(DATE(ja_datetime), "%Y-%m-%d") AS ja_date,
					DATE_FORMAT(ja_datetime, "%H:%i") AS ja_time,
					ja_attended,
					ja_notes
				FROM
					journey_appointments
				LEFT JOIN
					journeys
						ON j_id = ja_j_id
				LEFT JOIN
					clients_info
						ON ci_j_id = j_id
				LEFT JOIN
					recovery_coaches
						ON ja_rc_id = rc_id
				WHERE
					1=1';

		if(@$_GET['j_id'])
			$sql .= ' AND j_id = "' . (int) $_GET['j_id'] . '" ';


		if(@$_GET['j_type'])
			$sql .= ' AND j_type = ' . $this->db->escape($_GET['j_type']) . ' ';

		if(@$_GET['c_sname'])
			$sql .= ' AND CONVERT(ci_sname USING latin1) = "' . mysql_real_escape_string($_GET['c_sname']) . '" ';

		if(@$_GET['date_from'])
			$sql .= ' AND j_date_of_referral >= "' . parse_date($_GET['date_from']) . '" ';

		if(@$_GET['date_to'])
			$sql .= ' AND j_date_of_referral <= "' . parse_date($_GET['date_to']) . '" ';

		if(@$_GET['c_catchment_area'])
			$sql .= ' AND ci_catchment_area = "' . mysql_real_escape_string($_GET['c_catchment_area']) . '" ';

		if(@$_GET['j_status'])
			$sql .= ' AND j_status = "' . (int)$_GET['j_status'] . '" ';

		if(@$_GET['j_published'])
			$sql .= ' AND j_published = "' . (int) $_GET['j_published'] . '" ';

		if(@$_GET['j_ndtms_valid'])
			$sql .= ' AND j_ndtms_valid = "' . (int)$_GET['j_ndtms_valid'] . '" ';

		return $this->db->query($sql)->result_array();
	}




	/**
	 * Returns the data for all outcomes
	 *
	 */
	public function get_outcomes_csv()
	{
		//set up the filter of cases
		$sql_and = '';
		if(@$_GET['j_id'])
			$sql_and .= ' AND j_id = "' . (int) $_GET['j_id'] . '" ';

		if(@$_GET['j_type'])
			$sql_and .= ' AND j_type = ' . $this->db->escape($_GET['j_type']) . ' ';

		if(@$_GET['c_sname'])
			$sql_and .= ' AND CONVERT(ci_sname USING latin1) = "' . mysql_real_escape_string($_GET['c_sname']) . '" ';

		if(@$_GET['date_from'])
			$sql_and .= ' AND j_date_of_referral >= "' . parse_date($_GET['date_from']) . '" ';

		if(@$_GET['date_to'])
			$sql_and .= ' AND j_date_of_referral <= "' . parse_date($_GET['date_to']) . '" ';

		if(@$_GET['c_catchment_area'])
			$sql_and .= ' AND ci_catchment_area = "' . mysql_real_escape_string($_GET['c_catchment_area']) . '" ';

		if(@$_GET['j_status'])
			$sql_and .= ' AND j_status = "' . (int)$_GET['j_status'] . '" ';

		if(@$_GET['j_published'])
			$sql_and .= ' AND j_published = "' . (int) $_GET['j_published'] . '" ';

		if(@$_GET['j_ndtms_valid'])
			$sql_and .= ' AND j_ndtms_valid = "' . (int)$_GET['j_ndtms_valid'] . '" ';


		//initial sql to get cases and their assessments
		$sql = 'SELECT
					jas_id,
					j_id AS "Journey_id",
					c_id AS "Client ID",
					c_fname AS "First Name",
					c_sname AS "Surname",
					DATE_FORMAT(jas_date, "%d/%m/%Y") AS "Assessment Date",
					jas_notes AS "Outcome Notes",
					UPPER(acl_type) AS "Assessment Type"
				FROM
					journey_assessments
				LEFT JOIN
					ass_criteria_lists ON jas_acl_id = acl_id
				LEFT JOIN
					journeys ON jas_j_id = j_id
				LEFT JOIN
					clients ON j_c_id = c_id
				WHERE
					1 = 1';
		$sql .= $sql_and;
		// set order by clause
		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		$results = $this->db->query($sql)->result_array();

		//what's the maximum number of outcomes we will get?
		$sql = 'SELECT COUNT(aco_acl_id) AS maximum
				FROM ass_criteria_outcomes
				GROUP BY aco_acl_id
				ORDER BY maximum DESC
				LIMIT 1';

		$scores_count = $this->db->query($sql)->row_array();

		//set up csv headings, with maximum scores
		$headings = current($results);
		unset($headings['jas_id']);

		for ($i = 1; $i <= $scores_count['maximum']; $i++)
		{
			$headings['Assessment Number'] = 0;
			$headings['Outcome '. $i .' Name'] = 0;
			$headings['Outcome '. $i .' Score'] = 0;
		}

		$headings = array_keys($headings);

		$this->load->model('assessments_model');

		//go through each assessment, and append outcome score data
		$ass_num = 0;
		$last_j_id = 0;
		$outcome_data = null;
		foreach ($results as $i => $row)
		{
			$scores = $this->assessments_model->get_assessment_scores($row['jas_id']);
			$ass_num++;
			$results[$i]['Assessment Number'] = 'Assessment '. $ass_num;

			//$scores = $this->db->query($sql)->result_array();
			$j = 1;
			foreach ($scores as $score)
			{
				$results[$i]['Outcome '. $score['jacs_num'] .' Name'] = $score['jacs_title'];
				$results[$i]['Outcome '. $score['jacs_num'] .' Score'] = $score['jacs_score'];
				$j++;
			}

			//ready, we can ditch the id now
			unset($results[$i]['jas_id']);
		}

		$filename = "export-outcomes-" . date('d-m-Y');

		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename={$filename}.csv");
		header("Cache-Control: private");
		header("Pragma: token");
		header("Expires: 0");

		//build csv content, based on $this->dbutil->csv_from_result($results); (in /system/database/DB_utility.php);
		$delim = ","; $newline = "\n"; $enclosure = '"';

		$out = '';
		// First generate the headings from the table column names
		foreach ($headings as $name)
		{
			$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $name).$enclosure.$delim;
		}

		$out = rtrim($out);
		$out .= $newline;

		// Next blast through the result array and build out the rows
		foreach ($results as $row)
		{
			foreach ($headings as $item_name)
			{
				if(isset($row[$item_name]))
				{
					$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $row[$item_name]).$enclosure.$delim;
				}
				else
				{
					$out .= $enclosure."N/A".$enclosure.$delim;
				}

			}
			$out = rtrim($out);
			$out .= $newline;
		}

		echo $out;
		exit;
	}

// ========================================================================
// Journey Files
// ========================================================================

	// get array of files attached to a journey
	public function get_journey_files($j_id)
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_read_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_read_family'] != 1)
		{
			return FALSE;
		}

		// use lookup table
		$sql = 'SELECT
					ju_id,
					ju_name,
					ju_src,
					SUBSTRING_INDEX(ju_src, ".", -1) AS ju_ext,
					ju_size
				FROM
					journeys_uploads
				WHERE
					ju_j_id = "' . (int)$j_id . '";';

		return $this->db->query($sql)->result_array();
	}

	/**
	 * Following a file upload, save info about it so we can retrieve it.
	 */
	public function add_journey_file($j_id, $data)
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_edit_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_edit_family'] != 1)
		{
			return FALSE;
		}

		$sql = 'INSERT INTO
					journeys_uploads
				SET
					ju_j_id = ?,
					ju_name = ?,
					ju_src = ?,
					ju_size = ?';

		// commit query
		$this->db->query($sql, array($j_id,
									 $this->input->post('ju_name'),
									 $data['file_name'],
									 $data['file_size']));

		// set added notification
		$this->session->set_flashdata('action', 'File added');
	}

	public function delete_journey_file($ju_id)
	{
		//permissions check first
		$jtype = $this->check_journey_type($ju_id);
		if($jtype == 'C' && $this->session->userdata['permissions']['apt_can_edit_client'] != 1)
		{
			$this->session->set_flashdata('action', 'You do not have permission to edit this journey.');
			return FALSE;
		}
		elseif($jtype == 'F' && $this->session->userdata['permissions']['apt_can_edit_family'] != 1)
		{
			$this->session->set_flashdata('action', 'You do not have permission to edit this journey.');
			return FALSE;
		}
		// get template
		$sql = 'SELECT
					ju_src
				FROM
					journeys_uploads
				WHERE
					ju_id = "' . (int)$ju_id . '";';

		// if casefile exists
		if($journeyfile = $this->db->query($sql)->row_array())
		{
			// delete from templates table
			$sql = 'DELETE FROM
						journeys_uploads
					WHERE
						ju_id = "' . (int)$ju_id . '";';

			$this->db->query($sql);

			// unlink file from server
			unlink(APPPATH . '../storage/journeyfiles/' . $journeyfile['ju_src']);

			// return true
			return true;
		}
	}

	/**
	 * Handy function to look up the journey type, to use for permission checking.
	 */
	public function check_journey_type($j_id)
	{
		$sql = 'SELECT
					j_type
				FROM
					journeys
				WHERE
					j_id = '. (int)$j_id .'
				LIMIT 1;';
		$result = $this->db->query($sql)->row_array();
		if($result)
		{
			return $result['j_type'];
		}
		return FALSE;
	}

	/**
	 * Update the journey status, and record the change
	 */
	public function update_journey_status($j_id, $new_status, $new_tier='')
	{
		//Steady on, what permissions do we have?
		$j_type = $this->check_journey_type($j_id);
		if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_approve_client'] != 1)
		{
			return FALSE;
		}
		elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_approve_family'] != 1)
		{
			return FALSE;
		}

		if($new_tier == '')
		{
			$new_tier = 'NULL';
		}
		else
		{
			$new_tier = $this->db->escape($new_tier);
		}

		//record the change
		$sql = 'INSERT
				INTO journey_changes
					(jc_j_id,
					jc_changed_date,
					jc_a_id,
					jc_old_status,
					jc_new_status,
					jc_old_tier,
					jc_new_tier)
				SELECT
					j_id,
					NOW(),
					'. (int)$this->session->userdata('a_id') .',
					j_status,
					'. (int)$new_status .',
					j_tier,
					'. $new_tier .'
				FROM
					journeys
				WHERE
					j_id = '. (int)$j_id .'
				;';

		// commit query
		$result = $this->db->query($sql);
		if(!$result)
		{
			return FALSE;
		}

		//make the change
		$sql = 'UPDATE
					journeys
				SET
					j_status = '. (int)$new_status .',
					j_tier = '. $new_tier .'
				WHERE
					j_id = '. (int)$j_id .'
					;';

		// commit query
		return $this->db->query($sql);
	}


	/*
	 * Set the journey to published. This should stop events and notes being editable
	 */
	public function set_published($j_id)
	{
		//make the change
		$sql = 'UPDATE
					journeys
				SET
					j_published = 1
				WHERE
					j_id = '. (int)$j_id .'
					;';

		// commit query
		return $this->db->query($sql);
	}

	/*
	 * Set the journey to unpublished.
	 * This can only be done by master admins.
	 */
	public function set_unpublished($j_id)
	{
		//make the change
		$sql = 'UPDATE
					journeys
				SET
					j_published = 0
				WHERE
					j_id = '. (int)$j_id .'
					;';

		// commit query
		return $this->db->query($sql);
	}
}
