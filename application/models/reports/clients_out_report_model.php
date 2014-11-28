<?php

class Clients_out_report_model extends Reports_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Monthly closures
	 */
	public function month_closures($filter = array())
	{
		// Convert to date/time objects to get date range
		$start_date = new DateTime(date('Y-m-d', strtotime($filter['closure_from'])));
		$end_date = new DateTime(date('Y-m-d', strtotime($filter['closure_to'])));
		$interval = new DateInterval('P1M');
		$months = new DatePeriod($start_date, $interval, $end_date);

		$sql_array = array();

		// Need to remove the dates from the main filter because each unioned query needs their own date range
		$this->clear_filter('closure_from');
		$this->clear_filter('closure_to');

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
								j_closed_date BETWEEN '$month_start' AND '$month_end'
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
	 * Monthly closure exit reason
	 */
	public function exit_status($filter = array())
	{
		// Convert to date/time objects to get date range
		$start_date = new DateTime(date('Y-m-d', strtotime($filter['closure_from'])));
		$end_date = new DateTime(date('Y-m-d', strtotime($filter['closure_to'])));
		$interval = new DateInterval('P1M');
		$months = new DatePeriod($start_date, $interval, $end_date);

		$sql_array = array();
		$month_names = array();

		// Need to remove the dates from the main filter because each unioned query needs their own date range
		$this->clear_filter('closure_from');
		$this->clear_filter('closure_to');

		$codes = $this->config->item('exit_status_codes');

		foreach ($months as $month)
		{
			$this_year = $month->format('Y');
			$this_month = $month->format('m');
			$month_name = $month->format('M Y');

			$month_names[] = $month_name;

			$month_start = "{$this_year}-{$this_month}-01";
			$month_end = "{$this_year}-{$this_month}-31";

			$sql_array[] = "(SELECT
								COUNT(DISTINCT j_id) AS 'journeys',
								IF(ji_exit_status IS NULL OR ji_exit_status = '', 'Not Recorded', ji_exit_status) AS 'status',
								'{$month_name}' AS 'month'
							FROM
								journeys
							LEFT JOIN
								journeys_info
								ON j_id = ji_j_id
							LEFT JOIN
								clients
								ON j_c_id = c_id
							LEFT JOIN
								postcodes
								ON c_post_code = pc_postcode
							WHERE
								1 = 1
							" . $this->filter_sql() . "
							AND
								j_closed_date BETWEEN '$month_start' AND '$month_end'
							GROUP BY
								status)
							";
		}

		$sql = implode(' UNION ', $sql_array);

		$result = $this->db->query($sql)->result_array();

		$data = array('data' => array(), 'months' => $month_names, 'statuses' => array());
		$statuses = array();

		// Get information from the results first
		foreach ($result as $row)
		{
			$statuses[] = element($row['status'], $codes, $row['status']);
		}

		$statuses = array_unique($statuses);
		$data['statuses'] = array_values($statuses);

		// Populate array with all possible months and inital zero count of journeys
		foreach ($month_names as $month)
		{
			foreach ($statuses as $status)
			{
				$data['data'][$month][$status] = 0;
			}
		}

		// Loop through the results and update array with values
		foreach ($result as $row)
		{
			$status = element($row['status'], $codes, $row['status']);
			$data['data'][ $row['month'] ][ $status ] = $row['journeys'];
		}

		return array(
			'sql' => $sql,
			'data' => $data,
		);
	}




}