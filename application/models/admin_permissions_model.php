<?php

class Admin_permissions_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}


	public function get_permission_types()
	{
		$sql = 'SELECT
					*
				FROM
					admin_permission_types
				WHERE
					apt_active = 1';

		return $this->db->query($sql)->result_array();
	}

	public function get_permission_type($apt_id)
	{
		if(isset($apt_id))
		{
			$sql = 'SELECT
						*
					FROM
						admin_permission_types
					WHERE
						apt_id = "' . (int)$apt_id . '"
						AND apt_active = 1;';


			return $this->db->query($sql)->row_array();
		}
		return FALSE;
	}

	public function set_permission_type($apt_id=null)
	{
		$post_data = array('apt_name' => $this->input->post('apt_name'));

		$permissions = array(
			'apt_can_read_client',
			'apt_can_edit_client',
			'apt_can_add_client',
			'apt_can_delete_client',
			'apt_can_read_family',
			'apt_can_edit_family',
			'apt_can_add_family',
			'apt_can_delete_family',
			'apt_can_manage_admins',
			'apt_can_manage_options',
			'apt_can_approve_client',
			'apt_can_approve_family',
			'apt_reports',
		);

		foreach ($permissions as $apt)
		{
			$post_data[$apt] = ($this->input->post($apt) == 1 ? 1 : 0);
		}

		if(isset($apt_id) && $apt_id != null)
		{

			$sql = $this->db->update_string('admin_permission_types', $post_data, 'apt_id = "'. (int)$apt_id .'";');
			$this->db->query($sql);

			$this->session->set_flashdata('action', 'Permission type updated');
			return $apt_id;
		}
		else
		{
			$sql = $this->db->insert_string('admin_permission_types', $post_data);
			$this->db->query($sql);

			$this->session->set_flashdata('action', 'Permission type added');
			return $this->db->insert_id();
		}
		return false;
	}

	public function delete_permission_type($apt_id)
	{
		$sql = 'UPDATE
					admin_permission_types
				SET
					apt_active = 0
				WHERE
					apt_id = "' . (int)$apt_id . '";';

		return $this->db->query($sql);
	}

	public function get_allowed_admins($permission)
	{
		//check it's allowed
		$fields = $this->db->list_fields('admin_permission_types');
		if(!in_array($permission, $fields))
		{
			return false;
		}

		$sql = 'SELECT
					a_id,
					CONCAT(a_fname, " ", a_sname) AS "a_fullname",
					a_email,
					a_type
				FROM
					admins
				LEFT JOIN
					admin_permission_types ON a_apt_id = apt_id
							AND `'. $permission .'` = 1
				WHERE
					a_active = 1
					AND apt_id IS NOT NULL
				';
		$result = $this->db->query($sql)->result_array();

		return $result;
	}

}
