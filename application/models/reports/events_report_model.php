<?php

class Events_report_model extends Reports_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Event types breakdown by month
	 */
	public function event_types($filter = array())
	{
		// Convert to date/time objects to get date range
		$start_date = new DateTime(date('Y-m-d', strtotime($filter['event_from'])));
		$end_date = new DateTime(date('Y-m-d', strtotime($filter['event_to'])));
		$interval = new DateInterval('P1M');
		$months = new DatePeriod($start_date, $interval, $end_date);

		$sql_array = array();
		$month_names = array();

		// Need to remove the dates from the main filter because each unioned query needs their own date range
		$this->clear_filter('event_from');
		$this->clear_filter('event_to');

		foreach ($months as $month)
		{
			$this_year = $month->format('Y');
			$this_month = $month->format('m');
			$month_name = $month->format('M Y');

			$month_names[] = $month_name;

			$month_start = "{$this_year}-{$this_month}-01";
			$month_end = "{$this_year}-{$this_month}-31";

			$sql_array[] = "(SELECT
								COUNT(DISTINCT je_id) AS 'events',
								IF(ec_name IS NULL, 'Uncategorised', ec_name) AS 'type',
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
							LEFT JOIN
								journey_events
								ON j_id = je_j_id
							LEFT JOIN
								event_types
								ON je_et_id = et_id
							LEFT JOIN
								event_categories
								ON et_ec_id = ec_id
							WHERE
								1 = 1
							" . $this->filter_sql() . "
							AND
								je_datetime BETWEEN '$month_start' AND '$month_end'
							GROUP BY
								type)
							";
		}

		$sql = implode(' UNION ', $sql_array);

		$result = $this->db->query($sql)->result_array();

		$data = array('data' => array(), 'months' => $month_names, 'types' => array());
		$types = array();

		// Get information from the results first
		foreach ($result as $row)
		{
			$types[] = $row['type'];
		}

		$types = array_unique($types);
		$data['types'] = array_values($types);

		// Populate array with all possible months and inital zero count of journeys
		foreach ($month_names as $month)
		{
			foreach ($types as $type)
			{
				$data['data'][$month][$type] = 0;
			}
		}

		// Loop through the results and update array with values
		foreach ($result as $row)
		{
			$data['data'][ $row['month'] ][ $row['type'] ] = $row['events'];
		}

		return array(
			'sql' => $sql,
			'data' => $data,
		);
	}




	/**
	 * First Assessments monthly count
	 */
	public function first_assessments($filter = array())
	{
		// Convert to date/time objects to get date range
		$start_date = new DateTime(date('Y-m-d', strtotime($filter['event_from'])));
		$end_date = new DateTime(date('Y-m-d', strtotime($filter['event_to'])));
		$interval = new DateInterval('P1M');
		$months = new DatePeriod($start_date, $interval, $end_date);

		$sql_array = array();

		// Need to remove the dates from the main filter because each unioned query needs their own date range
		$this->clear_filter('event_from');
		$this->clear_filter('event_to');

		foreach ($months as $month)
		{
			$this_year = $month->format('Y');
			$this_month = $month->format('m');
			$month_name = $month->format('M Y');

			$month_start = "{$this_year}-{$this_month}-01";
			$month_end = "{$this_year}-{$this_month}-31";

			$sql_array[] = "SELECT
								count(DISTINCT je_id) AS 'first_assessments',
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
							LEFT JOIN
								journey_events
								ON j_id = je_j_id
							LEFT JOIN
								event_types
								ON je_et_id = et_id
							LEFT JOIN
								event_categories
								ON et_ec_id = ec_id
							WHERE
								1 = 1
							AND
								je_et_id = 20
							" . $this->filter_sql() . "
							AND
								je_datetime BETWEEN '$month_start' AND '$month_end'
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




}