<?php

class event_types_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}

	// get active event_types
	public function get_event_types($cache = 1)
	{
		// if we want to try and return a cache
		if($cache)
		{
			if($event_types = $this->cache->get('event_types'))
				return $event_types;
		}

		$sql = 'SELECT
					et_id,
					et_ec_id,
					et_name,
					ec_name as "et_ec_name"
				FROM
					event_types
					LEFT JOIN
						event_categories ON et_ec_id = ec_id
				WHERE
					et_active = 1
				ORDER BY
					ec_name DESC, et_name ASC ';

		$event_types = $this->db->query($sql)->result_array();

		// cache results forever
		$this->cache->save('event_types', $event_types, 9999999999);

		return $event_types;
	}

	// get single event_type information
	public function get_event_type($et_id)
	{
		if($et_id)
		{
			$sql = 'SELECT
						event_types.*
					FROM
						event_types
					WHERE
						et_id = "' . (int)$et_id . '"
						AND et_active = 1 ';

			return $this->db->query($sql)->row_array();
		}

		return false;
	}

	// either adds or updates event_type information
	public function set_event_type($et_id = FALSE)
	{
		// if et is not false
		if($et_id != FALSE)
		{
			// update event_type information
			$sql = 'UPDATE
						event_types
					SET
						et_ec_id = ?,
						et_name = ?
					WHERE
						et_id = "' . (int) $et_id . '";';

			// set action message
			$this->session->set_flashdata('action', 'Event type updated');
		}
		else
		{
			// insert new event_type information
			$sql = 'INSERT INTO
						event_types
					SET
						et_ec_id = ?,
						et_name = ? ';

			// set action message
			$this->session->set_flashdata('action', 'New event type added');
		}

		// commit the query
		$this->db->query($sql, array(
									 $this->input->post('et_ec_id'),
									 $this->input->post('et_name')
									 ));

		// delete cache file
		$this->_delete_cache();
	}

	// marks the event_type as inactive, effectively deleting it from the system
	public function set_event_type_inactive($et_id)
	{
		$sql = 'UPDATE
					event_types
				SET
					et_active = 0
				WHERE
					et_id = "' . (int)$et_id . '";';

		$this->db->query($sql);

		// delete cache file
		$this->_delete_cache();

		$this->session->set_flashdata('action', 'Event type deleted');
	}



	// delete cache file
	protected function _delete_cache()
	{
		$this->cache->delete('event_types');
		$this->cache->delete('event_categories');
	}

	public function update_cache($type, $data=null)
	{
		if($data == null && $type == 'event_categories')
		{
			$data = $this->get_event_categories(0);
		}

		//update cache
		$this->cache->save($type, $data, 999999999);
	}



	// get active event_types
	public function get_event_categories($cache = 1)
	{
		// if we want to try and return a cache
		if($cache)
		{
			if($event_categories = $this->cache->get('event_categories'))
				return $event_categories;
		}

		$sql = 'SELECT
					ec_id,
					ec_name
				FROM
					event_categories
				WHERE
					ec_active = 1
				ORDER BY
					ec_name ASC ';

		$event_categories = $this->db->query($sql)->result_array();

		// cache results forever
		$this->cache->save('event_categories', $event_categories, 9999999999);

		return $event_categories;
	}

	// get single event category information
	function get_event_category($ec_id)
	{
		if($ec_id)
		{
			$sql = 'SELECT
						*
					FROM
						event_categories
					WHERE
						ec_id = "' . (int)$ec_id . '"
						AND ec_active = 1 ';

			return $this->db->query($sql)->row_array();
		}

		return false;
	}

	// either adds or updates event category information
	public function set_event_category($ec_id = FALSE)
	{
		// if et is not false
		if($ec_id != FALSE)
		{
			// update information
			$sql = 'UPDATE
						event_categories
					SET
						ec_name = ?
					WHERE
						ec_id = "' . (int) $ec_id . '";';

			// set action message
			$this->session->set_flashdata('action', 'Event category updated');
		}
		else
		{
			// insert new information
			$sql = 'INSERT INTO
						event_categories
					SET
						ec_name = ? ';

			// set action message
			$this->session->set_flashdata('action', 'New event category added');
		}

		// commit the query
		$this->db->query($sql, array($this->input->post('ec_name')));

		// delete cache file
		$this->_delete_cache();
	}

	// marks the event_category as inactive, effectively deleting it from the system
	public function set_event_category_inactive($ec_id)
	{
		$sql = 'UPDATE
					event_categories
				SET
					ec_active = 0
				WHERE
					ec_id = "' . (int) $ec_id . '";';

		$this->db->query($sql);

		// delete cache file
		$this->_delete_cache();

		$this->session->set_flashdata('action', 'Event category deleted');
	}

}
