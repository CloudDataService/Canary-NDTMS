<?php

class Drugs_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}


	public function get_journey_drugs_info($j_id)
	{
		$sql = 'SELECT
					*,
					DATE_FORMAT(jd_hep_c_test_date, "%d/%m/%Y") AS jd_hep_c_test_date
				FROM
					journey_drugs
				WHERE
					jd_j_id = "' . (int)$j_id . '";';

		return $this->db->query($sql)->row_array();
	}

	// FUNCTION update drugs depending on what was posted
	public function update_journey_drugs_info($j_id)
	{
		//what have we got to update with?
		$jd_data = array();



		foreach ([
			"jd_substance_1",
			"jd_substance_1_route",
			"jd_substance_1_age",
			"jd_substance_2",
			"jd_substance_3",
			"jd_injecting",
			"jd_injected_in_last_month",
			"jd_prev_hep_b_infection",
			"jd_hep_b_vac_count",
			"jd_hep_b_intervention",
			"jd_hep_b_prev_positive",
			"jd_hep_c_intervention",
			"jd_hep_c_test_date",
			"jd_hep_c_tested",
			"jd_hep_c_positive",
		] as $key)
			if ($this->input->post($key) !== false)
				$data[$key] = $this->input->post($key) ?: null;

		if(!empty($data))
		{
			//check we have some jd already
			$sql = "SELECT jd_j_id FROM journey_drugs WHERE jd_j_id = '". (int)$j_id ."'; ";
			$jd_exists = $this->db->query($sql)->num_rows();

			if($jd_exists == 1)
			{
				// update journeys table
				$sql = $this->db->update_string('journey_drugs', $data, 'jd_j_id = "' . (int)$j_id . '";');
				$this->db->query($sql);

				//done
				$this->session->set_flashdata('action', 'Journey drugs information updated');
				return true;
			}
			else
			{
				// update journeys table
				$jd_data['jd_j_id'] = (int)$j_id;
				$sql = $this->db->insert_string('journey_drugs', $data);
				$this->db->query($sql);

				//done
				$this->session->set_flashdata('action', 'Journey drugs information added');
				return true;

			}
		}

		$this->session->set_flashdata('action', 'No data given for Journey drugs information');
		return true;
	}


	public function set_journey_drugs_info($j_id, $drugs_info = FALSE)
	{
		if($drugs_info)
		{
			$sql = 'UPDATE
						journey_drugs
					SET
						jd_substance_1 = ?,
						jd_substance_1_route = ?,
						jd_substance_1_age = ?,
						jd_substance_2 = ?,
						jd_substance_3 = ?,
						jd_injecting = ?,
						jd_prev_hep_b_infection = ?,
						jd_hep_b_vac_count = ?,
						jd_hep_b_intervention = ?,
						jd_hep_c_intervention = ?,
						jd_hep_c_test_date = ?,
						jd_hep_c_positive = ?
					WHERE
						jd_j_id = "' . (int)$j_id . '";';
		}
		else
		{
			$sql = 'INSERT INTO
						journey_drugs
					SET
						jd_j_id = "' . (int)$j_id . '",
						jd_substance_1 = ?,
						jd_substance_1_route = ?,
						jd_substance_1_age = ?,
						jd_substance_2 = ?,
						jd_substance_3 = ?,
						jd_injecting = ?,
						jd_prev_hep_b_infection = ?,
						jd_hep_b_vac_count = ?,
						jd_hep_b_intervention = ?,
						jd_hep_c_intervention = ?,
						jd_hep_c_test_date = ?,
						jd_hep_c_positive = ?;';
		}

		$this->db->query($sql, array(($this->input->post('jd_substance_1') ?: NULL),
									 ($this->input->post('jd_substance_1_route') ?: NULL),
									 ($this->input->post('jd_substance_1_age') ?: NULL),
									 ($this->input->post('jd_substance_2') ?: NULL),
									 ($this->input->post('jd_substance_3') ?: NULL),
									 ($this->input->post('jd_injecting') ?: NULL),
									 ($this->input->post('jd_prev_hep_b_infection') ?: NULL),
									 ($this->input->post('jd_hep_b_vac_count') ?: NULL),
									 ($this->input->post('jd_hep_b_intervention') ?: NULL),
									 ($this->input->post('jd_hep_c_intervention') ?: NULL),
									 ($this->input->post('jd_hep_c_test_date') ?: NULL),
									 ($this->input->post('jd_hep_c_positive') ?: NULL)));

		$this->session->set_flashdata('action', 'Drugs information updated');
	}



}
