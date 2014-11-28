<?php

class Clients_in_report_model extends Reports_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Monthly referrals
	 */
	public function month_referrals($filter = array())
	{
		// Convert to date/time objects to get date range
		$start_date = new DateTime(date('Y-m-d', strtotime($filter['referral_from'])));
		$end_date = new DateTime(date('Y-m-d', strtotime($filter['referral_to'])));
		$interval = new DateInterval('P1M');
		$months = new DatePeriod($start_date, $interval, $end_date);

		$sql_array = array();

		// Need to remove the dates from the main filter because each unioned query needs their own date range
		$this->clear_filter('referral_from');
		$this->clear_filter('referral_to');

		foreach ($months as $month)
		{
			$this_year = $month->format('Y');
			$this_month = $month->format('m');
			$month_name = $month->format('M Y');

			$month_start = "{$this_year}-{$this_month}-01";
			$month_end = "{$this_year}-{$this_month}-31";

			$sql_array[] = "SELECT
								count(DISTINCT j_id) AS 'journeys',
								'{$month_name}' AS 'month'
							FROM
								journeys
							LEFT JOIN
								clients ON j_c_id = c_id
							LEFT JOIN
								postcodes ON c_post_code = pc_postcode
							WHERE
								1 = 1
							" . $this->filter_sql() . "
							AND
								j_date_of_referral BETWEEN '$month_start' AND '$month_end'
							";
		}

		$sql = implode(' UNION ', $sql_array);

		$result = $this->db->query($sql)->result_array();
		$data = $result;

		return array(
			'sql' => $sql,
			'data' => $data,
		);
	}




	/**
	 * Journey Referral Source by Category
	 */
	public function referral_source_category($filter)
	{
		$start_date = new DateTime(date('Y-m-d', strtotime($filter['referral_from'])));
		$end_date = new DateTime(date('Y-m-d', strtotime($filter['referral_to'])));

		// Referral source codes for the types
		$codes = $this->config->item('referral_source_codes');

		$sql = "SELECT
					count(distinct j.j_id) AS journeys,
					IF(rs.rs_id IS NULL, '(Not recorded)', rs.rs_type) AS source
				FROM
					journeys j
				LEFT JOIN
					journeys_info ji
					ON j.j_id = ji.ji_j_id
				LEFT JOIN
					referral_sources rs
					ON ji.ji_rs_id = rs.rs_id
				LEFT JOIN
					clients c
					ON j.j_c_id = c.c_id
				LEFT JOIN
					postcodes pc
					ON c.c_post_code = pc.pc_postcode
				WHERE
					1 =1
				" . $this->filter_sql() . "
				GROUP BY
					rs.rs_type
				";

		$result = $this->db->query($sql)->result_array();
		$data = array();

		foreach ($result as $row)
		{
			$row['source'] = element($row['source'], $codes, $row['source']);
			$data[$row['source']] = $row;
		}

		ksort($data);

		return array(
			'sql' => $sql,
			'data' => $data,
		);
	}



	/**
	 * Journey Referral Source
	 */
	public function referral_source($filter)
	{
		$start_date = new DateTime(date('Y-m-d', strtotime($filter['referral_from'])));
		$end_date = new DateTime(date('Y-m-d', strtotime($filter['referral_to'])));

		$sql = "SELECT
					count(distinct j.j_id) AS journeys,
					IF(rs.rs_id IS NULL, '(Not recorded)', rs.rs_name) AS source
				FROM
					journeys j
				LEFT JOIN
					journeys_info ji
					ON j.j_id = ji.ji_j_id
				LEFT JOIN
					referral_sources rs
					ON ji.ji_rs_id = rs.rs_id
				LEFT JOIN
					clients c
					ON j.j_c_id = c.c_id
				LEFT JOIN
					postcodes pc
					ON c.c_post_code = pc.pc_postcode
				WHERE
					1 =1
				" . $this->filter_sql() . "
				GROUP BY
					rs.rs_name
				ORDER BY
					rs.rs_name ASC
				";

		$result = $this->db->query($sql)->result_array();
		$data = $result;

		return array(
			'sql' => $sql,
			'data' => $data,
		);
	}




	/**
	 * New & Existing clients
	 */
	public function new_existing($filter = array())
	{
		$sql = "SELECT
					SUM(IF(e_j_id IS NULL, 1, 0)) AS 'New Clients',
					SUM(IF(e_j_id IS NOT NULL, 1, 0)) AS 'Existing Clients'
				FROM
					journeys
				LEFT JOIN
					journeys_info ji
					ON journeys.j_id = ji.ji_j_id
				LEFT JOIN
					referral_sources rs
					ON ji.ji_rs_id = rs.rs_id
				LEFT JOIN
					clients c
					ON journeys.j_c_id = c.c_id
				LEFT JOIN
					postcodes pc
					ON c.c_post_code = pc.pc_postcode
				LEFT JOIN
					(SELECT
						ej.j_id AS e_j_id,
						ej.j_c_id AS e_c_id
					FROM journeys ej
					GROUP BY e_c_id
					) existing
					ON journeys.j_c_id = existing.e_c_id
					AND journeys.j_id != existing.e_j_id
				WHERE
					1 = 1
				" . $this->filter_sql();

		$result = $this->db->query($sql)->result_array();
		$data = $result;

		return array(
			'sql' => $sql,
			'data' => $data,
		);
	}




	/**
	 * Client age at referral
	 */
	public function client_age_referral($filter = array())
	{
		$age_groups = array(
			'0-15' => 'BETWEEN 0 AND 15',
			'16-25' => 'BETWEEN 16 AND 25',
			'26-35' => 'BETWEEN 26 AND 35',
			'36-45' => 'BETWEEN 36 AND 45',
			'46-55' => 'BETWEEN 46 AND 55',
			'56-70' => 'BETWEEN 56 AND 70',
			'71+' => '> 70',
			'Not Recorded' => 'IS NULL',
		);

		$age_groups_select = array();
		foreach ($age_groups as $label => $condition)
		{
			$age_groups_sql[] = "SUM(IF( (DATE_FORMAT( FROM_DAYS( TO_DAYS(j_date_of_referral) - TO_DAYS(c_date_of_birth) ), '%Y' ) + 0) $condition, 1, 0)) AS '$label'";
		}

		$age_groups_str = implode(",\n", $age_groups_sql);

		$sql = "SELECT
					$age_groups_str
				FROM
					journeys
				LEFT JOIN
					journeys_info ji
					ON journeys.j_id = ji.ji_j_id
				LEFT JOIN
					referral_sources rs
					ON ji.ji_rs_id = rs.rs_id
				LEFT JOIN
					clients c
					ON journeys.j_c_id = c.c_id
				LEFT JOIN
					postcodes pc
					ON c.c_post_code = pc.pc_postcode
				WHERE
					1 = 1
				" . $this->filter_sql();

		$result = $this->db->query($sql)->row_array();
		$data = $result;

		return array(
			'sql' => $sql,
			'data' => $data,
		);

	}




	/**
	 * Client Declared Disability Breakdown
	 */
	public function disability_count($filter = array())
	{

		$filter_sql = $this->filter_sql();

		$sql = "SELECT

					(SELECT
						COUNT(DISTINCT journeys.j_id)
					FROM
						journeys
					LEFT JOIN
						journeys_info ji
						ON journeys.j_id = ji.ji_j_id
					LEFT JOIN
						clients c
						ON journeys.j_c_id = c.c_id
					LEFT JOIN
						postcodes pc
						ON c.c_post_code = pc.pc_postcode
					LEFT JOIN
						j2disability
						ON j_id = j2d_j_id
					WHERE
						1 = 1
					AND
						(j2d_disability IS NOT NULL AND j2d_disability != 'None')

					$filter_sql

					) AS 'Yes',

					(SELECT
						COUNT(DISTINCT journeys.j_id)
					FROM
						journeys
					LEFT JOIN
						journeys_info ji
						ON journeys.j_id = ji.ji_j_id
					LEFT JOIN
						clients c
						ON journeys.j_c_id = c.c_id
					LEFT JOIN
						postcodes pc
						ON c.c_post_code = pc.pc_postcode
					LEFT JOIN
						j2disability
						ON j_id = j2d_j_id
					WHERE
						1 = 1
					AND
						j2d_disability = 'None'

					$filter_sql

					) AS 'No',

					(SELECT
						COUNT(DISTINCT journeys.j_id)
					FROM
						journeys
					LEFT JOIN
						journeys_info ji
						ON journeys.j_id = ji.ji_j_id
					LEFT JOIN
						clients c
						ON journeys.j_c_id = c.c_id
					LEFT JOIN
						postcodes pc
						ON c.c_post_code = pc.pc_postcode
					LEFT JOIN
						j2disability
						ON j_id = j2d_j_id
					WHERE
						1 = 1
					AND
						j2d_disability IS NULL

					$filter_sql

					) AS 'Not recorded'
				";

		$result = $this->db->query($sql)->row_array();
		$data = $result;

		return array(
			'sql' => $sql,
			'data' => $data,
		);
	}




	/**
	 * Client Gender
	 */
	public function client_gender($filter = array())
	{
		$sql = "SELECT
					SUM(IF(c_gender = 1, 1, 0)) AS 'Male',
					SUM(IF(c_gender = 2, 1, 0)) AS 'Female',
					SUM(IF(c_gender NOT IN (1, 2), 1, 0)) AS 'Not Recorded'
				FROM
					journeys
				LEFT JOIN
					journeys_info ji
					ON journeys.j_id = ji.ji_j_id
				LEFT JOIN
					referral_sources rs
					ON ji.ji_rs_id = rs.rs_id
				LEFT JOIN
					clients c
					ON journeys.j_c_id = c.c_id
				LEFT JOIN
					postcodes pc
					ON c.c_post_code = pc.pc_postcode
				WHERE
					1 = 1
				" . $this->filter_sql();

		$result = $this->db->query($sql)->row_array();
		$data = $result;

		return array(
			'sql' => $sql,
			'data' => $data,
		);

	}




	/**
	 * Ethnicity
	 */
	public function client_ethnicity($filter = array())
	{
		// Ethnicity codes
		$codes = $this->config->item('ethnicity_codes');

		$sql = "SELECT
					count(distinct j_id) AS 'journeys',
					IF(ci_ethnicity IS NULL OR ci_ethnicity = '', 'Not recorded', ci_ethnicity) AS 'ethnicity'
				FROM
					journeys
				LEFT JOIN
					journeys_info ji
					ON journeys.j_id = ji.ji_j_id
				LEFT JOIN
					referral_sources rs
					ON ji.ji_rs_id = rs.rs_id
				LEFT JOIN
					clients c
					ON journeys.j_c_id = c.c_id
				LEFT JOIN
					clients_info ci
					ON journeys.j_id = ci.ci_j_id
				LEFT JOIN
					postcodes pc
					ON c.c_post_code = pc.pc_postcode
				WHERE
					1 = 1
				" . $this->filter_sql() . "
				GROUP BY ci_ethnicity";

		$result = $this->db->query($sql)->result_array();

		foreach ($result as $row)
		{
			$code = $row['ethnicity'];
			$row['ethnicity'] = element($row['ethnicity'], $codes, $row['ethnicity']);
			$data[$code] = $row;
		}

		ksort($data);

		return array(
			'sql' => $sql,
			'data' => $data,
		);

	}




	/**
	 * Number of journeys by catchment area
	 */
	public function catchment_area($filter = array())
	{
		$sql = "SELECT
					COUNT(DISTINCT journeys.j_id) AS 'journeys',
					IF(ci_catchment_area IS NULL OR ci_catchment_area = '', 'Not Recorded', ci_catchment_area) AS 'area'
				FROM
					journeys
				LEFT JOIN
					journeys_info
					ON j_id = ji_j_id
				LEFT JOIN
					clients
					ON j_c_id = c_id
				LEFT JOIN
					clients_info
					ON ci_j_id = j_id
				LEFT JOIN
					postcodes
					ON c_post_code = pc_postcode
				WHERE
					1 = 1
				" . $this->filter_sql() . "
				GROUP BY ci_catchment_area";

		$result = $this->db->query($sql)->result_array();
		$data = $result;

		return array(
			'sql' => $sql,
			'data' => $data,
		);

	}




	/**
	 * Number of journeys by tier
	 */
	public function tier($filter = array())
	{
		$sql = "SELECT
					COUNT(DISTINCT journeys.j_id) AS 'journeys',
					IF(j_tier IS NULL OR j_tier = '', 'Not Recorded', j_tier) AS 'tier'
				FROM
					journeys
				LEFT JOIN
					journeys_info
					ON j_id = ji_j_id
				LEFT JOIN
					clients
					ON j_c_id = c_id
				LEFT JOIN
					clients_info
					ON ci_j_id = j_id
				LEFT JOIN
					postcodes
					ON c_post_code = pc_postcode
				WHERE
					1 = 1
				" . $this->filter_sql() . "
				GROUP BY j_tier";

		$result = $this->db->query($sql)->result_array();
		$data = $result;

		return array(
			'sql' => $sql,
			'data' => $data,
		);

	}




	public function problem_substance_1($filter = array())
	{
		return $this->_problem_substance($filter, 'jd_substance_1');
	}


	public function problem_substance_2($filter = array())
	{
		return $this->_problem_substance($filter, 'jd_substance_2');
	}




	private function _problem_substance($filter = array(), $substance_field = '')
	{
		// Allowed values for $substance_field
		$fields = array('jd_substance_1', 'jd_substance_2');

		if ( ! in_array($substance_field, $fields))
		{
			return array(
				'sql' => '',
				'data' => array(),
			);
		}

		// Drug codes
		$codes = $this->config->item('drug_codes');

		$sql = "SELECT
					COUNT(DISTINCT journeys.j_id) AS 'journeys',
					IF($substance_field IS NULL OR $substance_field = '', 'Not Recorded', $substance_field) AS 'substance'
				FROM
					journeys
				LEFT JOIN
					journeys_info
					ON j_id = ji_j_id
				LEFT JOIN
					journey_drugs
					ON j_id = jd_j_id
				LEFT JOIN
					clients
					ON j_c_id = c_id
				LEFT JOIN
					clients_info
					ON ci_j_id = j_id
				LEFT JOIN
					postcodes
					ON c_post_code = pc_postcode
				WHERE
					1 = 1
				" . $this->filter_sql() . "
				GROUP BY $substance_field";

		$result = $this->db->query($sql)->result_array();

		foreach ($result as $row)
		{
			$code = $row['substance'];
			$row['substance'] = element($row['substance'], $codes, $row['substance']);
			$data[$code] = $row;
		}

		ksort($data);

		return array(
			'sql' => $sql,
			'data' => $data,
		);
	}




}