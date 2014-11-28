<?php

class Admins_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}


	public function get_admins()
	{
		$sql = 'SELECT
					*,
					IF(ISNULL(a_datetime_last_login), "N/A", DATE_FORMAT(a_datetime_last_login, "%D %b %Y %l:%i%p")) AS a_datetime_last_login_format,
					IF(a_verified = 1, "Verified", "Unverified") AS a_verified
				FROM
					admins
				WHERE
					a_master = 0
					AND a_active = 1';

		return $this->db->query($sql)->result_array();
	}


	public function get_admins_select()
	{
		$sql = 'SELECT
					a_id,
					CONCAT(a_fname, " ", a_sname) AS a_name
				FROM
					admins
				WHERE
					a_active = 1
				ORDER BY
					a_sname ASC, a_fname ASC';

		return $this->db->query($sql)->result_array();
	}


	public function get_admin($admin_id)
	{
		if($admin_id)
		{
			$sql = 'SELECT
						a_id,
						a_fname,
						a_sname,
						a_email,
						a_master,
						a_type,
						a_apt_id,
						a_verified
					FROM
						admins
					WHERE
						a_id = "' . (int)$admin_id . '"
						AND a_active = 1;';

			return $this->db->query($sql)->row_array();
		}

		return FALSE;
	}


	public function check_email_unique($email, $admin_id)
	{
		if($admin_id)
		{
			$sql = 'SELECT
						a_email
					FROM
						admins
					WHERE
						a_id = "' . (int)$admin_id . '
						AND a_active = 1";';

			if($row = $this->db->query($sql)->row_array())
			{
				$sql = 'SELECT
							a_id
						FROM
							admins
						WHERE
							a_email != ?
							AND a_email = ?
							AND a_active = 1';

				return ($this->db->query($sql, array($row['a_email'], $email))->row_array() ? FALSE : TRUE);
			}
		}
		else
		{
			$sql = 'SELECT
						a_id
					FROM
						admins
					WHERE
						a_email = ?
						AND a_active = 1;';

			return ($this->db->query($sql, $email)->row_array() ? FALSE : TRUE);
		}

		return FALSE;
	}


	public function check_password($password, $admin_id)
	{
		$sql = 'SELECT
					a_password
				FROM
					admins
				WHERE
					a_id = ?
					AND a_active = 1';

		$user = $this->db->query($sql, $admin_id)->row_array();

		// normally you'd think you could just exit here if !$user, but you'd be wrong.
		// verify regardless of correct user.
		$user_pass = element('a_password', $user, "");
		$valid = password_verify($password, $user_pass);

		// check if the hash strength needs to be adjusted
		if (password_needs_rehash($user_pass, PASSWORD_DEFAULT) && $valid)
		{
			$this->update_admin_password($admin_id, $password);
		}

		return $valid;
	}


	public function update_admin_details($admin_id)
	{
		$sql = 'UPDATE
					admins
				SET
					a_email = ?,
					a_fname = ?,
					a_sname = ?
				WHERE
					a_id = "' . (int)$admin_id . '"
					AND a_active = 1;';

		$this->db->query($sql, array($this->input->post('email'),
									 $this->input->post('fname'),
									 $this->input->post('sname'))
						 );

		$this->session->set_userdata(array('a_email' => $this->input->post('email'),
									       'a_fname' => $this->input->post('fname'),
									       'a_sname' => $this->input->post('sname')));

		$this->session->set_flashdata('action', 'Details updated');
	}


	public function update_admin_password($admin_id, $password = null)
	{
		if (is_null($password))
			$password = $this->input->post('new_password');

		$sql = 'UPDATE
					admins
				SET
					a_password = ?,
					a_expires_at = ?
				WHERE
					a_id = ?
					AND a_active = 1';

		$password = password_hash($password, PASSWORD_DEFAULT);

		$now = new DateTime();
		$interval = DateInterval::createFromDateString($this->config->item("password_expiry"));
		$now = $now->add($interval);
		$expires = $now->format("Y-m-d H:i:s");

		$this->cache->delete("password_expiry:{$admin_id}");

		$this->db->query($sql, array($password, $expires, $admin_id));

		$this->session->set_flashdata('action', 'Password changed');
	}


	function update_admin_options($admin_id, $options)
	{
		$sql = 'UPDATE
					admins
				SET
					a_options = ?
				WHERE
					a_id = "' . (int)$admin_id . '"';

		$this->db->query($sql, json_encode($options));

		$this->session->set_flashdata('action', 'Options updated');
	}


	function set_admin($admin_id = 0, $options)
	{
		if($this->input->post('password'));	// != '' && $admin_id)
		{
			$password = password_hash($this->input->post('new_password'), PASSWORD_DEFAULT);
			$expiry = new DateTime();
			$expiry = $expiry->add(DateInterval::createFromDateString($this->config->item("password_expiry")));
			$expiry = $expiry->format("Y-m-d H:i:s");
		}

		if($admin_id)
		{
			$sql = 'UPDATE
						admins
					SET
						a_fname = ?,
						a_sname = ?,
						a_email = ?,
				';
			if(isset($password))
			{
				$sql .= 'a_password = ?,';
				$sql .= 'a_expires_at = ?,';
			}
			$sql .= '
						a_type = ?,
						a_apt_id = ?,
						a_options = ?
					WHERE
						a_id = "' . (int)$admin_id . '"
						AND a_active = 1';

			$this->session->set_flashdata('action', 'Administrator updated');
		}
		else
		{
			$sql = 'INSERT INTO
						admins
					SET
						a_fname = ?,
						a_sname = ?,
						a_email = ?,
				';
			if(isset($password))
			{
				$sql .= 'a_password = ?,';
				$sql .= 'a_expires_at = ?,';
			}
			$sql .= '
						a_type = ?,
						a_apt_id = ?,
						a_options = ?';

			$this->session->set_flashdata('action', 'Administrator added');
		}

		if(isset($password))
		{
			$this->db->query($sql, array($this->input->post('fname'),
									 $this->input->post('sname'),
									 $this->input->post('email'),
									 $password,
									 $expiry,
									 NULL,
									 $this->input->post('a_apt_id'),
									 json_encode($options))
						 );
		}
		else
		{
			$this->db->query($sql, array($this->input->post('fname'),
									 $this->input->post('sname'),
									 $this->input->post('email'),
									 NULL,
									 $this->input->post('a_apt_id'),
									 json_encode($options))
						 );
		}

		return ($admin_id ? $admin_id : $this->db->insert_id());
	}


	function set_master_admin($admin_id)
	{
		$sql = 'UPDATE
					admins
				SET
					a_master = 1
				WHERE
					a_id = ?
					AND a_active = 1';

		$this->db->query($sql, $admin_id);
	}


	function delete_admin($admin_id)
	{
		$sql = 'UPDATE
					admins
				SET
					a_active = 0
				WHERE
					a_id = ?';

		$this->db->query($sql, $admin_id);

		// Remove association with keyworker
		$this->load->model('recovery_coaches_model');
		$this->recovery_coaches_model->set_admin($admin_id, 0);

		$this->session->set_flashdata('action', 'Administrator deleted');
	}


	function verify_admin($admin_id)
	{
		$sql = 'UPDATE
					admins
				SET
					a_verified = 1
				WHERE
					a_id = ?
					AND a_active = 1';

		$this->db->query($sql, $admin_id);

		$this->session->set_flashdata('action', 'Administrator verified');
	}

}
