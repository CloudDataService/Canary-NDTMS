<?php

class Offending_model extends CI_Model
{
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	public function get_journey_offending_info($j_id)
	{
		$sql = 'SELECT
					*
				FROM	
					journey_offending
				WHERE
					jo_j_id = "' . (int)$j_id . '" ';
					
		return $this->db->query($sql)->row_array();		
	}
	
	
	public function set_journey_offending_info($j_id, $offending_info = FALSE)
	{
		if($offending_info)
		{
			$sql = 'UPDATE
						journey_offending
					SET
						jo_shop_theft = ?,
						jo_drug_selling = ?,
						jo_other_theft = ?,
						jo_assault_violence = ?,
						jo_notes = ?
					WHERE
						jo_j_id = "' . (int)$j_id . '"';
		}
		else
		{
			$sql = 'INSERT INTO
						journey_offending
					SET
						jo_j_id = "' . (int)$j_id . '",
						jo_shop_theft = ?,
						jo_drug_selling = ?,
						jo_other_theft = ?,
						jo_assault_violence = ?,
						jo_notes = ?';
		}
		
		$this->db->query($sql, array(($this->input->post('jo_shop_theft') ? $this->input->post('jo_shop_theft') : NULL),
									 ($this->input->post('jo_drug_selling') ? $this->input->post('jo_drug_selling') : NULL),
									 ($this->input->post('jo_other_theft') ? $this->input->post('jo_other_theft') : NULL),
									 ($this->input->post('jo_assault_violence') ? $this->input->post('jo_assault_violence') : NULL),
									 ($this->input->post('jo_notes') ? $this->input->post('jo_notes') : NULL)));
		
		$this->session->set_flashdata('action', 'Offending information updated');
	}
}