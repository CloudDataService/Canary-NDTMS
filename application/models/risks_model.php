<?php

class Risks_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	// get risks types readily parsed for a select box
	public function get_risk_types($c_id)
	{		
		$sql = 'SELECT
					risk_types.*,
					IF(cr_c_id, TRUE, FALSE) AS has_risk
				FROM
					risk_types
				LEFT JOIN
					client_risks
						ON cr_c_id = "' . (int)$c_id . '" AND cr_rt_id = rt_id ';
					
		if($risk_types = $this->db->query($sql, $sql)->result_array())
		{
			$risk_groups = array();
			
			// loop over each risk
			foreach($risk_types as $rt)
			{
				// group risk by group
				$risk_groups[$rt['rt_group']][] = $rt;
			}
						
			// return new groups
			return $risk_groups;
		}	
	}
	
	// set a risk to a client
	public function set_risk_type($c_id, $risk_type = FALSE)
	{
		if($risk_type)
		{
			$sql = 'UPDATE
						client_risks
					SET
						cr_rt_id = ?,
						cr_c_id = ?,
						cr_impact_score = ?,
						cr_likelihood_score = ?,
						cr_risk_score = ?,
						cr_risk_level = ?,
						cr_risk_to_whom = ?,
						cr_protective_factors = ?
					WHERE	
						cr_rt_id = "' . (int)$risk_type['cr_rt_id'] . '"
						AND cr_c_id = "' . (int)$c_id . '"';
			
			$this->session->set_flashdata('action', 'Risk updated');	
		}
		else
		{	
			$sql = 'INSERT INTO
						client_risks
					SET
						cr_rt_id = ?,
						cr_c_id = ?,
						cr_impact_score = ?,
						cr_likelihood_score = ?,
						cr_risk_score = ?,
						cr_risk_level = ?,
						cr_risk_to_whom = ?,
						cr_protective_factors = ?;';
						
			$this->session->set_flashdata('action', 'Risk added');	
		}
		
		// multiply impact score by likelihood score to get risk score
		$risk_score = ($this->input->post('impact_score') * $this->input->post('likelihood_score'));
		
		// get risk level
		switch($risk_score)
		{
			case $risk_score > 0 && $risk_score < 4 :
				$risk_level = 'Very low';
			break;	
			
			case $risk_score > 3 && $risk_score < 8 :
				$risk_level = 'Low';
			break;
			
			case $risk_score > 7 && $risk_score < 15 :
				$risk_level = 'Moderate';
			break;
			
			case $risk_score > 14 && $risk_score < 26 :
				$risk_level = 'High';
			break;
		}
		
		$this->db->query($sql, array($this->input->post('rt_id'),
									 $c_id,
									 $this->input->post('impact_score'),
									 $this->input->post('likelihood_score'),
									 $risk_score,
									 $risk_level,
									 ($this->input->post('risk_to_whom') ? $this->input->post('risk_to_whom') : NULL),
									 ($this->input->post('protective_factors') ? $this->input->post('protective_factors') : NULL)));
	}
	
	// returns list of client's risks
	public function get_clients_risks($c_id)
	{
		$sql = 'SELECT
					cr_rt_id, 
					cr_impact_score,
					cr_likelihood_score,
					cr_risk_score,
					cr_risk_level,
					IF(cr_risk_to_whom IS NULL, "N/A", cr_risk_to_whom) AS cr_risk_to_whom,
					IF(cr_protective_factors IS NULL, "N/A", cr_protective_factors) AS cr_protective_factors,
					rt_name,
					rt_group
				FROM
					client_risks,
					risk_types
				WHERE
					cr_c_id = "' . (int)$c_id . '"
					AND rt_id = cr_rt_id ';	
		
		$risk_types = $this->db->query($sql)->result_array();
		
		$risk_groups = array('total_risks' => count($risk_types));
			
		// loop over each risk
		foreach($risk_types as $rt)
		{
			// group risk by group
			$risk_groups[$rt['rt_group']][] = $rt;
		}
		
		return $risk_groups;		
	}
	
	// returns single risk information for a specific client
	public function get_clients_risk($rt_id, $c_id)
	{
			$sql = 'SELECT
					cr_rt_id, 
					cr_impact_score,
					cr_likelihood_score,
					cr_risk_score,
					cr_risk_level,
					cr_risk_to_whom,
					cr_protective_factors,
					rt_name,
					rt_group
				FROM
					client_risks,
					risk_types
				WHERE
					cr_c_id = "' . (int)$c_id . '"
					AND cr_rt_id = "' . (int)$rt_id . '";';	
		
		return $this->db->query($sql)->row_array();
	}
	
	public function delete_clients_risk($rt_id, $c_id)
	{
		$sql = 'DELETE FROM
					client_risks
				WHERE
					cr_c_id = "' . (int)$c_id . '"
					AND cr_rt_id = "' . (int)$rt_id . '";';	
					
		$this->db->query($sql);
		
		$this->session->set_flashdata('action', 'Risk deleted');						
	}
	
	public function get_risk_summaries($c_id)
	{
		$sql = 'SELECT
					crs_summary,
					crs_physical_risks,
					crs_psychological_risks,
					crs_social_risks,
					crs_violence_aggression_risks AS "crs_violence/aggression_risks"
				FROM
					client_risk_summary
				WHERE
					crs_c_id = "' . (int)$c_id . '";';
					
		return $this->db->query($sql)->row_array();
	}
	
	
	public function set_risk_summaries($c_id, $risk_summaries = FALSE)
	{
		if($risk_summaries)
		{
			$sql = 'UPDATE
						client_risk_summary
					SET
						crs_summary = ?,
						crs_physical_risks = ?,
						crs_psychological_risks = ?,
						crs_social_risks = ?,
						crs_violence_aggression_risks = ?
					WHERE
						crs_c_id = "' . (int)$c_id . '" ';
		}
		else
		{
			$sql = 'INSERT INTO
						client_risk_summary
					SET
						crs_c_id = "' . (int)$c_id . '",
						crs_summary = ?,
						crs_physical_risks = ?,
						crs_psychological_risks = ?,
						crs_social_risks = ?,
						crs_violence_aggression_risks = ?';
		}
		
		$this->db->query($sql, array(($this->input->post('crs_summary') ? $this->input->post('crs_summary') : NULL),
									 ($this->input->post('crs_physical_risks') ? $this->input->post('crs_physical_risks') : NULL),
									 ($this->input->post('crs_psychological_risks') ? $this->input->post('crs_psychological_risks') : NULL),
									 ($this->input->post('crs_social_risks') ? $this->input->post('crs_social_risks') : NULL),
									 ($this->input->post('crs_violence/aggression_risks') ? $this->input->post('crs_violence/aggression_risks') : NULL)));
									 
		$this->session->set_flashdata('action', 'Risks updated');
	}
	
	// marks client as a potential risk in db
	public function set_is_risk($journey)
	{
		// update clients table
		$sql = 'UPDATE
					clients
				SET
					c_is_risk = ' . ($this->input->post('is_risk') ? (int)$this->input->post('is_risk') : 'NULL') . '
				WHERE
					c_id = "' . (int)$journey['j_c_id'] . '";';
					
		$this->db->query($sql);
		
		
		// update clients info table for specific journey
		$sql = 'UPDATE
					clients_info
				SET
					ci_is_risk = ' . ($this->input->post('is_risk') ? (int)$this->input->post('is_risk') : 'NULL') . '
				WHERE
					ci_j_id = "' . (int)$journey['j_id'] . '";';
					
		$this->db->query($sql);
	}

}