<?php

class Support_groups_model extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
	}
	
	// returns all active support groups
	public function get_support_groups()
	{
		if( ! in_array(@$_GET['order'], array('sp_name', 'sp_default_day', 'sp_default_time')) ) $_GET['order'] = 'sp_name';
		
		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';
		
		$sql = 'SELECT
					sp_id,
					sp_name,
					IF(sp_default_day IS NULL, "N/A", sp_default_day) AS sp_default_day,
					IF(sp_default_time IS NULL, "N/A", DATE_FORMAT(sp_default_time, "%H:%i")) AS sp_default_time
				FROM
					support_groups ';
									
		// set order by clause
		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';	
		
		return $this->db->query($sql)->result_array();		
	}
	
	// returns all data about a single support group
	public function get_support_group($sp_id = 0, $format = false)
	{
		// if sp_id is false
		if( ! $sp_id)
			return false;
		
		// if we want a formatted version
		if($format)
		{
			// get formatted version
			$sql = 'SELECT
						sp_id,
						sp_name,
						IF(sp_description IS NULL, "N/A", sp_description) AS sp_description,
						IF(sp_default_day IS NULL, "N/A", CONCAT(sp_default_day, "s")) AS sp_default_day,
						IF(sp_default_time IS NULL, "", CONCAT(" at ", DATE_FORMAT(sp_default_time, "%H:%i"))) AS sp_default_time
					FROM
						support_groups
					WHERE
						sp_id = "' . (int)$sp_id . '";';
		}
		else
		{
			// get raw version			
			$sql = 'SELECT
						*,
						HOUR(sp_default_time) AS time_hour,
						MINUTE(sp_default_time) AS time_minute
					FROM
						support_groups
					WHERE
						sp_id = "' . (int)$sp_id . '";';
		}
		
		return $this->db->query($sql)->row_array();	
	}
	
	// add or update a support group
	public function set_support_group($support_group = false)
	{
		// if support group is true
		if($support_group)
		{
			// update support group
			$sql = 'UPDATE
						support_groups
					SET
						sp_name = ?,
						sp_description = ?,
						sp_default_day = ?,
						sp_default_time = ?
					WHERE
						sp_id = ' . (int) $support_group['sp_id'] . '
					LIMIT 1';
			
			// set notifcation
			$this->session->set_flashdata('action', 'Support group updated');			
		}
		else
		{
			// insert new support group
			$sql = 'INSERT INTO
						support_groups
					SET	
						sp_name = ?,
						sp_description = ?,
						sp_default_day = ?,
						sp_default_time = ?;';
						
			// set notifcation
			$this->session->set_flashdata('action', 'New support group added');
		}
		
		// if default time has not been set
		if($this->input->post('time_hour') == '00' && $this->input->post('time_minute') == '00')
		{
			// default time is null
			$default_time = NULL;
		}
		else
		{
			// default time is set
			$default_time = ($this->input->post('time_hour') . ':' . $this->input->post('time_minute'));
		}
		
		// commit query with values
		$this->db->query($sql, array($this->input->post('sp_name'),
									 ($this->input->post('sp_description') != '' ? $this->input->post('sp_description') : NULL),
									 ($this->input->post('sp_default_day') != '' ? $this->input->post('sp_default_day') : NULL),
									 $default_time));
									 
		// return support group id
		return ($support_group ? $support_group['sp_id'] : $this->db->insert_id());		
	}
	
	// deletes a support group and it's related data
	public function delete_support_group($sp_id)
	{
		// delete support group
		$sql = 'DELETE FROM
					support_groups
				WHERE
					sp_id = "' . (int)$sp_id . '";';
		
		if($this->db->query($sql))
		{
			// get sessions for support group
			$sessions = $this->get_sessions($sp_id);
			
			// loop over each sessions
			foreach($sessions as $s)
			{
				// delete individual session and it's register
				$this->delete_session($s['sps_id']);
			}
			
			// set delete notification
			$this->session->set_flashdata('action', 'Support group deleted');
		}
	}
	
	// returns total number of sessions for a single support group
	public function get_total_sessions($sp_id)
	{
		$sql = 'SELECT
					COUNT(sps_id) AS total
				FROM
					support_group_sessions
				WHERE
					sps_sp_id = "' . (int)$sp_id . '";';
		
		$total = $this->db->query($sql)->row_array();
		
		return $total['total'];		
	}
	
	// returns all sessions for a single support group
	public function get_sessions($sp_id, $start = 0, $limit = 0)
	{
		if( ! in_array(@$_GET['order'], array('sps_datetime', 'sps_location', 'sps_total_registered')) ) $_GET['order'] = 'sps_datetime';
		
		if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';
			
		$sql = 'SELECT
					sps_id,
					sps_location,
					COUNT(spc_sps_id) AS sps_total_registered,
					DATE_FORMAT(sps_datetime, "%d/%m/%Y at %H:%i") AS sps_datetime_format
				FROM
					support_group_sessions
				LEFT JOIN
					support_group_clients
						ON spc_sps_id = sps_id
				WHERE
					sps_sp_id = "' . (int)$sp_id . '" ';

		// group by session
		$sql .= ' GROUP BY sps_id ';
			
		// set order by clause
		$sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';
			
		if($limit)
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
				
		return $this->db->query($sql)->result_array();
	}
	
	// returns a single session
	public function get_session($sps_id)
	{
		$sql = 'SELECT
					*,
					DATE_FORMAT(sps_datetime, "%d/%m/%Y") AS sps_date,
					HOUR(sps_datetime) AS time_hour,
					MINUTE(sps_datetime) AS time_minute
				FROM
					support_group_sessions
				WHERE
					sps_id = "' . (int)$sps_id . '";';
		
		return $this->db->query($sql)->row_array();
	}
	
	// add or update session information
	public function set_session($sp_id, $session = false)
	{
		// if session exists
		if($session)
		{
			// update session information
			$sql = 'UPDATE
						support_group_sessions
					SET
						sps_sp_id = ?,
						sps_datetime = ?,
						sps_location = ?,
						sps_notes = ?
					WHERE
						sps_id = "' . (int)$session['sps_id'] . '";';
			
			// set update notifcation
			$this->session->set_flashdata('action', 'Session information updated');					
		}
		else
		{
			// add new session information
			$sql = 'INSERT INTO
						support_group_sessions
					SET
						sps_sp_id = ?,
						sps_datetime = ?,
						sps_location = ?,
						sps_notes = ?;';
			
			// set added notifcation
			$this->session->set_flashdata('action', 'New session information added');
		}
		
		// set session date and time
		$sps_datetime = $this->input->post('sps_date') . ' ' . $this->input->post('time_hour') . ':' . $this->input->post('time_minute') . ':00';
		
		$this->db->query($sql, array($sp_id,
								     $sps_datetime,
									 $this->input->post('sps_location'),
									 $this->input->post('sps_notes')));
									 
		// return support group session id
		return ($session ? $session['sps_id'] : $this->db->insert_id());
	}
	
	// delete a single session
	public function delete_session($sps_id)
	{
		// delete register
		$sql = 'DELETE FROM
					support_group_clients
				WHERE
					spc_sps_id = "' . (int)$sps_id . '";';
		
		if($this->db->query($sql))
		{
			// delete session
			$sql = 'DELETE FROM
						support_group_sessions
					WHERE
						sps_id = "' . (int)$sps_id . '";';
			
			$this->db->query($sql);
			
			// set delete notification
			$this->session->set_flashdata('action', 'Session deleted');
		}
		
	}
	
	// returns all clients registered to attend a single session
	public function get_register($sps_id = 0)
	{
		// if session does not exist
		if( ! $sps_id)
			return false;
			
		$sql = 'SELECT
					c_id,
					CONCAT(c_fname, " ", c_sname) AS c_name,
					c_post_code,
					DATE_FORMAT(c_date_of_birth, "%d/%m/%Y") AS c_date_of_birth,
					IF(c_gender = 1, "Male", "Female") AS c_gender
				FROM
					support_group_clients,
					clients
				WHERE
					spc_sps_id = "' . (int)$sps_id . '"
					AND c_id = spc_c_id
				ORDER BY
					c_sname ASC;';
		
		return $this->db->query($sql)->result_array();
	}
	
	// adds a client to a session register
	public function add_client_to_register($sps_id, $c_id)
	{
		// check to see if client is already on register
		$sql = 'SELECT
					spc_sps_id
				FROM
					support_group_clients
				WHERE
					spc_sps_id = "' . (int)$sps_id . '"
					AND spc_c_id = "' . (int)$c_id . '";';
		
		// if client is not already on register
		if( ! $this->db->query($sql)->row_array())
		{
			// add client to register
			$sql = 'INSERT INTO
						support_group_clients
					SET
						spc_sps_id = "' . (int)$sps_id . '",
						spc_c_id = "' . (int)$c_id . '";';
			
			$this->db->query($sql);
		}
		
		// set added notification
		$this->session->set_flashdata('action', 'Client added to register');
	}
	
	// remove a client from a support group session register
	public function remove_client_from_register($sps_id, $c_id)
	{
		// add client to register
		$sql = 'DELETE FROM
					support_group_clients
				WHERE
					spc_sps_id = "' . (int)$sps_id . '"
					AND spc_c_id = "' . (int)$c_id . '";';
					
		$this->db->query($sql);
		
		// set delete notification
		$this->session->set_flashdata('action', 'Client removed from register');		
	}
	
	
	// gets the last register set for a single support group
	public function get_last_register($sp_id, $sps_id)
	{
		$sql = 'SELECT
					c_id,
					CONCAT(c_fname, " ", c_sname) AS c_name,
					c_post_code,
					DATE_FORMAT(c_date_of_birth, "%d/%m/%Y") AS c_date_of_birth,
					IF(c_gender = 1, "Male", "Female") AS c_gender
				FROM
					support_group_clients,
					clients
				WHERE
					c_id = spc_c_id
					AND spc_sps_id = (SELECT 
									  	sps_id
									  FROM 
									 	support_group_sessions 
									  WHERE
									  	sps_id != "' . (int)$sps_id . '"
										AND sps_sp_id = "' . (int)$sp_id . '"
									  ORDER BY
									  	sps_datetime DESC
									  LIMIT 1);';
		
		return $this->db->query($sql)->result_array();		
	}
	
	// returns array of groups
	public function get_groups_output_csv()
	{
		$sql = 'SELECT
					sp_id,
					sp_name,
					DATE_FORMAT(sps_datetime, "%d/%m/%Y") AS sps_datetime,
					c_id,
					CONCAT(c_fname, " ", c_sname) AS c_name,
					DATE_FORMAT(c_date_of_birth, "%d/%m/%Y") AS c_date_of_birth,
					IF(c_gender = 1, "Male", "Female") AS c_gender,
					c_post_code
				FROM
					support_group_clients
				LEFT JOIN
					support_group_sessions
						ON spc_sps_id = sps_id
				LEFT JOIN
					support_groups
						ON sps_sp_id = sp_id
				LEFT JOIN
					clients
						ON spc_c_id = c_id
				ORDER BY sp_name ASC';
						
		return $this->db->query($sql)->result_array();			
	}
	
}