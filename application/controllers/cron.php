<?php defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Handle the running of DB upgrade patch files
 *
 * @version 1.0
 */
class Cron extends CI_Controller
{

	public function __construct()
	{
		// init parent
		parent::__construct();
		$this->load('auth');
		$this->load->dbutil();
	}

	protected function keyCheck()
	{
		if (! $this->auth->key_check($this->input->get($key)))
			show_404();
	}

	protected function parseSelect($select = array())
	{
		// parse the select array into the select quieries
		foreach ($select as $alias => $sql) {
			if ($sql == '')
			{
				$select[] = $alias;
			}
			else
			{
				$select[] = "{$sql} as \"{$alias}\"";
			}

			unset($select[$alias]);
		}

		return implode(', ', $select);
	}

	/**
	 * update journeys to set last csop to null if needed
	 */
	public function last_csop()
	{
		$this->keyCheck();
		$log_file = FCPATH . '../.' . __FUNCTION__;

		if (file_exists($log_file)) {
			header('Content-Type: text/plain');
			print 'Job already ran...' . PHP_EOL . PHP_EOL;
			print file_get_contents($log_file);
			exit;
		}

		$this->db->select('ji_j_id');
		$this->db->where('ji_csop_last IS NOT NULL', '', false);
		$this->db->or_where('ji_csop_last != \'\'', '', false);
		$journeys = $this->db->get('journeys_info');

		file_put_contents($log_file, $this->db->last_query() . PHP_EOL, FILE_APPEND);
		file_put_contents($log_file, PHP_EOL . '// ------------------------------------------------------------------------' . PHP_EOL . PHP_EOL, FILE_APPEND);

		if ($journeys->num_rows() > 0) {
			foreach ($journeys->result() as $row) {
				$journey_id = trim($row->ji_j_id);

				$this->load->model('journeys_model');
				$this->journeys_model->set_journey_ass_dates($journey_id);

				file_put_contents($log_file, $this->db->last_query() . PHP_EOL, FILE_APPEND);
			}
		}

		header('Content-Type: text/plain');
		print file_get_contents($log_file);
	}

	/**
	 * email all senior managers the overdue CSOPs
	 */
	public function overdue_csop()
	{
		$this->keyCheck();
		$csv_file = APPPATH . 'csv/' . __FUNCTION__ . '.csv';

		// build the select
		$select = $this->parseSelect(array(
			'Client ID'          => 'CONCAT("#", j_c_id)',
			'Client Name'        => 'CONCAT(c_fname, " ", c_sname)',
			'Date Last CSOP Due' => 'DATE_FORMAT(DATE(ji_csop_due), "%d/%m/%Y")',
			'Journey Link'       => 'CONCAT("' . site_url('admin/journeys/info') . '/", ji_j_id)',
		));

		$this->db->select($select, false);
		$this->db->where('ji_csop_due <', date('Y-m-d'));
		$this->db->where('j_status !=', '2');
		$this->db->join('journeys', 'j_id = ji_j_id', 'left');
		$this->db->join('clients', 'c_id = j_c_id', 'left');
		$journeys = $this->db->get('journeys_info');

		if ($journeys->num_rows() < 1) {
			return;
		}

		// get our output vars setup
		$output_array = array(
			'count' => $journeys->num_rows(),
			'raw'   => $journeys->result_array(),
			'csv'   => $this->dbutil->csv_from_result($journeys),
			'txt'   => array(),
			'html'  => array(),
		);

		// free up some memory
		unset($journeys);

		// extract teh plain text and html
		foreach ($output_array['raw'] as $row) {
			$txt = $row['Client Name'] . ' (Client ID ' . $row['Client ID'] . ', Date Last CSOP Due ' . $row['Date Last CSOP Due'] . ')';

			$output_array['txt'][] = '- ' . $txt;
			$output_array['html'][] = '<li><a href="' . $row['Journey Link'] . '" target="_blank">' . $txt . '</a></li>';
		}

		// free up some memory
		unset($output_array['raw']);

		file_put_contents($csv_file, $output_array['csv']);
		unset($output_array['csv']);

		$output_array['txt'] = implode(PHP_EOL, $output_array['txt']);
		$output_array['html'] = '<ul>' . PHP_EOL . implode(PHP_EOL, $output_array['html']) . PHP_EOL . '</ul>';


		// get the current admins to email
		// build the select
		$select = $this->parseSelect(array(
			'name' => 'a_fname',
			'email' => 'a_email',
		));

		$this->db->select($select, false);
		$this->db->where('a_apt_id', 6);
		$admins = $this->db->get('admins');

		foreach ($admins->result_array() as $admin) {
			$email_data = array_merge($output_array, $admin);

			$this->load->library('email', array(
				'mailtype' => 'html',
			));

			$this->email->clear(TRUE);

			$this->email->from(config_item('site_email'), config_item('site_name'));

			switch (ENVIRONMENT) {
				case 'development':
				case 'testing':
					$this->email->to('developers@clouddataservice.co.uk');
					break;

				default:
					$this->email->to($admin['email']);
					break;
			}

			$this->email->subject('Overdue CSOP List');
			$this->email->message($this->load->view('email/cron/' . __FUNCTION__ . '/html', $email_data, true));
			$this->email->set_alt_message($this->load->view('email/cron/' . __FUNCTION__ . '/txt', $email_data, true));
			$this->email->attach($csv_file);

			$this->email->send();
		}

		header('Content-Type: text/plain');
		print file_get_contents($csv_file);

		// remove the cron csv file that was created
		unlink($csv_file);
	}

	/**
	 * update log to set client name and journey id
	 */
	public function log_add_client_name_and_journey_id()
	{
		$this->keyCheck($key);
		$log_file = FCPATH . '../.' . __FUNCTION__;

		if (file_exists($log_file)) {
			header('Content-Type: text/plain');
			print 'Job already ran...' . PHP_EOL . PHP_EOL;
			print file_get_contents($log_file);
			exit;
		}

		$this->db->select('l_id, l_description');
		$logs = $this->db->get('log');

		file_put_contents($log_file, $this->db->last_query() . PHP_EOL, FILE_APPEND);
		file_put_contents($log_file, PHP_EOL . '// ------------------------------------------------------------------------' . PHP_EOL . PHP_EOL, FILE_APPEND);

		if ($logs->num_rows() > 0) {
			foreach ($logs->result() as $row) {
				$log_id = $row->l_id;
				$description = $row->l_description;
				preg_match("/((?<=journey #)[\\d]+)/ui", $description, $journey_id);

				if (empty($journey_id[1])) {
					continue;
				}

				$journey_id = $journey_id[1];

				$this->load->model('log_model');
				$client = $this->log_model->clientFromJourneyId($journey_id);

				$update_data = array(
					'l_journey' => $journey_id,
					'l_client' => $client ? $client['c_id'] : null,
					'l_client_name' => $client ? $client['name'] : null,
				);

				$this->db->where('l_id', $log_id);
				$this->db->update('log', $update_data);

				file_put_contents($log_file, $this->db->last_query() . PHP_EOL, FILE_APPEND);
			}
		}

		header('Content-Type: text/plain');
		print file_get_contents($log_file);
	}

	/**
	 * update journey cache for all journeys
	 */
	public function update_journey_cache()
	{
		$this->keyCheck();

		$this->db->select('j_id');
		$journeys = $this->db->get('journeys');

		if ($journeys->num_rows() > 0) {
			header('Content-Type: text/plain');

			$this->load->model('journeys_model');

			foreach ($journeys->result() as $row) {
				$j_id = $row->j_id;

				print $j_id . ": " . var_export($this->journeys_model->cache_journey($j_id), true) . PHP_EOL;
			}
		}
	}
}
