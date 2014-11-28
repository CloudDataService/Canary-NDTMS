<?php

class Messages_model extends CI_Model
{
	
	
	function __construct()
	{
		parent::__construct();
	}
	
	
	public function get_total_messages($params)
	{
		$sql = 'SELECT
					COUNT(m_id) AS total
				FROM
					messages
				WHERE
					1 = 1 ';
		
		if (@$params['m_to_id'])
			$sql .= ' AND m_to_id = ' . (int) $params['m_to_id'] . ' ';
		
		if(@$params['m_type'])
			$sql .= ' AND CONVERT(m_type USING latin1) = "' . mysql_real_escape_string($params['m_type']) . '" ';
			
		if (@$params['m_status'])
			$sql .= ' AND m_status = ' . $this->db->escape($params['m_status']) . ' ';	
		
		//check they can see these messages
		$sql .= ' AND (m_to_admin = '. $this->session->userdata('a_id') .' OR m_from_admin = '. $this->session->userdata('a_id') .' )';
					
		$row = $this->db->query($sql)->row_array();
		
		return $row['total'];
	}
	
	public function get_messages($start = 0, $limit = 0, $params)
	{
		if( ! in_array(@$params['order'], array('m_id', 'm_to_admin', 'm_from_admin', 'm_type')) ) $params['order'] = 'm_id';
		
		if(@$params['sort'] != 'asc' && @$params['sort'] != 'desc') $params['sort'] = 'desc';
		
		$sql = 'SELECT
					*,
					IF(m_sent_date IS NULL, "N/A", DATE_FORMAT(m_sent_date, "%d/%m/%Y %H:%i")) AS m_sent_date_format
				FROM
					messages
				WHERE
					1 = 1 ';
		
		if (@$params['m_to_admin'])
			$sql .= ' AND m_to_admin = ' . (int) $params['m_to_admin'] . ' ';
		
		if(@$params['m_type'])
			$sql .= ' AND m_type = "' . $this->db->escape($params['m_type']) . '" ';
			
		if (@$params['m_status'])
		{
			$sql .= ' AND m_status = ' . $this->db->escape($params['m_status']) . ' ';
		}
		else
		{
			$sql .= ' AND m_status != "Deleted" ';
		}	
		
		//check they can see these messages
		$sql .= ' AND (m_to_admin = '. $this->session->userdata('a_id') .' OR m_from_admin = '. $this->session->userdata('a_id') .' )';
				
		// set order by clause
		$sql .= ' ORDER BY ' . $params['order'] . ' ' . $params['sort'] . ' ';	
		
		if($limit)
			$sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;
		
		return $this->db->query($sql)->result_array();
	}
	
	
	// FUNCTION update message
	public function update_message($m_id, $m_data)
	{
		
		// update client info table
		if(!empty($m_data))
		{
			$sql = $this->db->update_string('messages', $m_data, 'm_id = "' . (int)$m_id . '";');
			return $this->db->query($sql);
		}
		
		//$this->session->set_flashdata('action', 'Message updated');
	}
	
	
	
	// add a amessage
	public function add_message($mdata)
	{
	
		if(is_array($mdata['m_to_admin']))
		{
			$mid = array();
			foreach($mdata['m_to_admin'] as $to_id)
			{
				$this_data = $mdata;
				$this_data['m_to_admin'] = $to_id;
				$sql = $this->db->insert_string('messages', $this_data);
				$this->db->query($sql);
				$mid[] = $this->db->insert_id();
			}
		}
		else
		{
			$sql = $this->db->insert_string('messages', $mdata);
			$this->db->query($sql);

			// return client id
			$mid = $this->db->insert_id();
		}
		
		//e-mail them?	
					
		// set notification
		//$this->session->set_flashdata('action', 'Client updated');
		
				
		return $mid;
	}
	
	
	
	
	/**
	 * Delete messages that conform to a specific criteria
	 *
	 * E.g. If a journey status is getting updated, it is desirable to delete all messages relating to the status change.
	 * => array('m_j_id' => 42, 'm_cat_name' => 'j_status', 'm_cat_value' => 3);
	 *
	 * @param array $conditions		Conditions to use for message deletion
	 * @return bool
	 * @author CR
	 */
	public function delete($conditions = array())
	{
		$where = '';
		
		if (empty($conditions)) return FALSE;
		
		foreach ($conditions as $col => $val)
		{
			$where .= ' AND `' . $col . '` = ' . $this->db->escape($val) . ' ';
		}
		
		$sql = 'UPDATE messages SET m_status = "Deleted" WHERE 1 = 1 ' . $where;
		return $this->db->query($sql);
	}
	
	
}	