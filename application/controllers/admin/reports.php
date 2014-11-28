<?php

class Reports extends MY_controller {


	public function __construct()
	{
		parent::__construct();

		// check the admin is logged in
		$this->auth->check_admin_logged_in();

		$this->load->model(array('recovery_coaches_model'));
		$this->load->helper('report_helper');

		$this->layout->set_title('Reports');
		$this->layout->set_breadcrumb('Reports', '/admin/reports');

		$this->load->config('datasets');
		$this->load->config('reports');

		$this->data['groups'] = $this->config->item('groups', 'reports');
		$this->data['reports'] = $this->config->item('reports', 'reports');
		$this->data['config'] = $this->config->item('config', 'reports');
	}



	public function index($group_name = '')
	{
		if (empty($group_name))
		{
			$group_name = current(array_keys($this->data['groups']));
		}

		$this->data['active_group'] = $group_name;
		$this->data['key_workers'] = $this->recovery_coaches_model->get_recovery_coaches();

		// Load required models for this report group
		$group = element($group_name, $this->data['groups']);
		if ( ! empty($group['models']))
		{
			foreach ($group['models'] as $model)
			{
				$this->load->model("reports/$model");
			}
		}

		// First JS file
		$js = array('plugins/klass.min', 'views/admin/reports/charts');

		// Output to build up from reports
		$html = '';
		$this->data['reports_html'] = '<p class="no_results">No reports configured yet.</p>';

		// Load the reports for this group
		$reports = element($group_name, $this->data['reports']);

		// Get/set filter data
		$filter = $this->_process_filter($group_name, $reports);

		// Container for the data for javascript charts
		$json = array();

		if ( ! empty($reports))
		{
			foreach ($reports as $report_name => $report)
			{
				$model = element('model', $report);
				$function = element('function', $report);
				$config = element($group_name, $this->data['config']);
				$config = element($report_name, $config, array());

				if ($model && $function && method_exists($model, $function))
				{
					$this->$model->set_filter($filter);
					$result = $this->$model->$function($filter);
				}
				else
				{
					$result = array('sql' => '', 'data' => array());
				}

				$data = array(
					'group_name' => $group_name,
					'report_name' => $report_name,
					'report' => $report,
					'result' => $result,
					'config' => $config,
				);

				if (element('skip_html', $report) === FALSE)
				{
					// HTML for this report

					$html .= '<div class="header"><h2>' . $report['title'] . '<span class="' . $report_name . '"></span></h2></div>';
					$html .= '<div class="item">';

					// Load the report view file
					$view = 'admin/reports/' . element('view', $report, 'default');
					if (file_exists(APPPATH . "/views/{$view}.php"))
					{
						// If a separate view file for this report exists, load it.
						$html .= $this->load->view($view, $data, TRUE);
					}
					else
					{
						// No unique view or it failed for some reason? Load default.
						$html .= $this->load->view('admin/reports/default', $data, TRUE);
					}

					if ($this->input->get('debug') == 1)
					{
						$sql = preg_replace("/\t+/", "\t", $data['result']['sql']);
						$html .= '<textarea class="report_sql" wrap="off">' . $sql . '</textarea>';
					}

					$html .= '</div>';
				}


				// Add to JSON array
				$json[$report_name] = $data;
				unset($json[$report_name]['result']['sql']);

				// JS for this group & report?
				$js_path = "views/admin/reports/groups/{$group_name}/{$report_name}";
				if (file_exists(FCPATH . "/scripts/{$js_path}.js"))
				{
					$js[] = $js_path;
				}

			}

			$this->data['reports_html'] = $html;
			$this->data['json'] = $json;
		}

		$js[] = 'views/admin/reports/reports';

		$this->layout->set_view('/admin/reports/index');
		$this->layout->set_external_js('https://www.google.com/jsapi');
		$this->layout->set_js($js);
		$this->load->vars($this->data);
		$this->load->view($this->layout->get_template());
	}




	/**
	 * Set, get, assign and parse the incoming filter parameters.
	 *
	 * If no date range is specified, set one.
	 * Params are supplied for reference in order to determine what (if anything) should be done to certain fields.
	 * For example, the outgoing clients group needs to filter the date range on closure date instead of referral date.
	 *
	 * @param string $group		Reporting group.
	 * @param array $reports		Array of reports in this group.
	 * @return array		Array of keys => values to be supplied to report models.
	 */
	private function _process_filter($group = '', $reports = array())
	{
		$filter = array();

		if ( ! $this->input->get('date_from'))
		{
			$_GET['date_from'] = year_start();
		}

		if ( ! $this->input->get('date_to'))
		{
			$_GET['date_to'] = year_end();
		}

		if ($group == 'family')
		{
			$_GET['j_type'] = array('F');
		}
		else
		{
			if ( ! $this->input->get('j_type'))
			{
				$_GET['j_type'] = array('C', 'F');
			}
		}

		if ($this->input->get())
		{

			/*
				Dates!

				The filtering date field changes based on which group of reports are being loaded.
				For most and incoming, we use the referral date.
				For outgoing, we use closure date.
				This is where we take the incoming date range and apply it to the correct filterable column
			*/

			$date_set = FALSE;

			if (in_array($group, array('family', 'clients_in')))
			{
				$filter['referral_from'] = parse_date($this->input->get('date_from'));
				$filter['referral_to'] = parse_date($this->input->get('date_to'));
				$date_set = TRUE;
			}
			elseif (in_array($group, array('clients_out')))
			{
				$filter['closure_from'] = parse_date($this->input->get('date_from'));
				$filter['closure_to'] = parse_date($this->input->get('date_to'));
				$date_set = TRUE;
			}
			elseif (in_array($group, array('events')))
			{
				$filter['event_from'] = parse_date($this->input->get('date_from'));
				$filter['event_to'] = parse_date($this->input->get('date_to'));
				$date_set = TRUE;
			}

			if ($date_set === FALSE)
			{
				// Default fallback
				$filter['referral_from'] = parse_date($this->input->get('date_from'));
				$filter['referral_to'] = parse_date($this->input->get('date_to'));
			}

			// Catchment area
			if ($this->input->get('c_catchment_area'))
			{
				$filter['c_catchment_area'] = $this->input->get('c_catchment_area');
			}

			// Key Worker
			if ($this->input->get('j_rc_id'))
			{
				$filter['j_rc_id'] = $this->input->get('j_rc_id');
			}

			// Journey Status
			if ($this->input->get('j_status'))
			{
				$filter['j_status'] = $this->input->get('j_status');
			}

			// Journey Type
			if ($this->input->get('j_type'))
			{
				$filter['j_type'] = $this->input->get('j_type');
			}
		}

		return $filter;
	}


}

/* End of file: ./application/controllers/admin/reports.php */
