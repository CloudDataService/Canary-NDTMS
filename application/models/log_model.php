<?php

class Log_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	// returns total number of logs in the db
	public function get_total_log()
	{
		$sql = 'SELECT
					COUNT(l_id) AS total
				FROM
					log
				WHERE
					1 = 1 ';

		if(@$_GET['date_from'])
			$sql .= ' AND DATE(l_datetime) >= "' . parse_date($_GET['date_from']) . '" ';

		if(@$_GET['date_to'])
			$sql .= ' AND DATE(l_datetime) <= "' . parse_date($_GET['date_to']) . '" ';

		if(@$_GET['c_id'])
			$sql .= ' AND l_client = "' . (int)$_GET['c_id'] . '" ';

		if(@$_GET['j_id'])
			$sql .= ' AND l_journey = "' . (int)$_GET['j_id'] . '" ';

		$result = $this->db->query($sql)->row_array();

		return $result['total'];
	}

	// returns array of log information
	public function get_log($start = 0, $limit = 0, $sp_id = 0)
	{
		if( ! in_array(@$_GET['order'], array('l_a_id', 'l_datetime')) ) $_GET['order'] = 'l_datetime';

		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';

		$sql = 'SELECT
					l_id,
					l_description,
					l_client_name,
					l_journey,
					l_client,
					DATE_FORMAT(l_datetime, "%D %b %Y at %l:%i%p") AS l_datetime_format,
					CONCAT(a_fname, " ", a_sname) AS a_name
				FROM
					log
				LEFT JOIN
					admins
					ON a_id = l_a_id
				WHERE
					1 = 1 ';

		if(@$_GET['date_from'])
			$sql .= ' AND DATE(l_datetime) >= "' . parse_date($_GET['date_from']) . '" ';

		if(@$_GET['date_to'])
			$sql .= ' AND DATE(l_datetime) <= "' . parse_date($_GET['date_to']) . '" ';

		if(@$_GET['c_id'])
			$sql .= ' AND l_client = "' . (int)$_GET['c_id'] . '" ';

		if(@$_GET['j_id'])
			$sql .= ' AND l_journey = "' . (int)$_GET['j_id'] . '" ';

		// set order by clause
		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

		if($limit)
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;

		return $this->db->query($sql)->result_array();
	}

	// sets a piece of log information to the db, array of log infomration passed as argument
	public function set($description)
	{
		// Skip logging when autosave is used (preventing recurring log entries)
		if ($this->input->is_ajax_request() && $this->input->post('method') == 'autosave')
		{
			return;
		}

		$sql = 'INSERT INTO
					log
				SET
					l_a_id = "' . (int)$this->session->userdata('a_id') . '",
					l_datetime = NOW(),
					l_description = ?,
					l_client_name = ?,
					l_journey = ?,
					l_client = ? ';

		preg_match("/((?<=journey #)[\\d]+)/ui", $description, $journey_id);

        if (empty($journey_id[1])) {
			$journey_id = $journey_id[1];
			$client = $this->clientFromJourneyId($journey_id);
		} else {
			$journey_id = null;
			$client = array(
				'c_id' => null,
				'name' => null,
			);
		}

		$this->db->query($sql, array($description, $client['name'], $journey_id, $client['c_id']));
	}

	// remove a record from the log table
	public function delete_log($l_id)
	{
		$sql = 'DELETE FROM
					log
				WHERE
					l_id = "' . (int)$l_id . '" ';

		$this->db->query($sql);
	}

	// remove a record from the log table
	public function clientFromJourneyId($j_id)
	{
		$sql = 'SELECT
					*,
					CONCAT(c_fname ," ", c_sname) AS name
				FROM clients
				LEFT JOIN
					journeys
					ON c_id = j_c_id
				WHERE j_id = ?';

		$result = $this->db->query($sql, array($j_id));

		if ($result->num_rows() > 0) {
			return $result->row_array();
		}

		return false;
	}
}
