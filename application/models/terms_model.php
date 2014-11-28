<?php

class Terms_model extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}

	public function get_terms()
	{
		$sql = 'SELECT o_value FROM options WHERE o_key = "terms_and_conditions" LIMIT 1';
		$row = $this->db->query($sql)->row_array();

		if (isset($row['o_value']))
		{
			return json_decode($row['o_value'], true);
		}
		else
		{
			return FALSE;
		}
	}

	public function update_terms($terms_and_conditions)
	{
		$terms = json_encode($terms_and_conditions);
		$sql = "INSERT
					INTO options
						(o_key, o_value)
					VALUES
						('terms_and_conditions', ?)
				ON DUPLICATE KEY
					UPDATE
					o_value = ?
				;";

		$this->db->query($sql, array($terms, $terms));
	}


	function set_datetime_tc_agree($a_id)
	{
		$sql = 'UPDATE
					admins
				SET
					a_datetime_tc_agree = NOW()
				WHERE
					a_id = "' . (int)$a_id . '";';

		$this->db->query($sql);
	}

}
