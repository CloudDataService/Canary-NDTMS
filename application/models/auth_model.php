<?php

class Auth_model extends CI_Model
{


	public function __construct()
	{
		parent::__construct();
	}

	public function login_admin()
	{
		$sql = 'SELECT
					a_id,
					a_datetime_tc_agree,
					a_email,
					a_password,
					a_fname,
					a_sname,
					a_options,
					a_master,
					a_type,
					a_apt_id,
					rc_id,
					rc_name,
					rc_family_worker
				FROM
					admins
				LEFT JOIN
					a2rc
					ON a_id = a2rc_a_id
				LEFT JOIN
					recovery_coaches rc
					ON a2rc_rc_id = rc_id
				WHERE
					a_email = ?
					AND a_verified = 1
					AND a_active = 1';

		$admin = $this->db->query($sql, array($this->input->post('email')))->row_array();

		if($admin && password_verify($this->input->post('password'), $admin['a_password']))
		{
			if (password_needs_rehash($admin['a_password'], PASSWORD_DEFAULT))
				$this->admins_model->update_admin_password($this->input->post("password"), $admin["a_id"]);

			// get their permissions
			if(is_null($admin['a_apt_id']))
				$admin['a_apt_id'] = 0; //default permission type

			$sql = 'SELECT
						*
					FROM
						admin_permission_types
					WHERE
						apt_id = ?
					LIMIT 1';

			// forcing a prepare here also caches it
			$admin['permissions'] = $this->db->query($sql, $admin['a_apt_id'])->row_array();

			// if they are master admin, grant all rights
			if($admin['a_master'] == 1)
			{
				foreach($admin['permissions'] as $k => $v)
				{
					$admin['permissions'][$k] = 1;
				}
			}

			// don't want password as part of session information
			unset($admin['a_password']);

			// unserialize admin's options
			$admin['a_options'] = json_decode($admin['a_options']);

			if ($admin['a_options'])
			{
				// loop through options and set them in sessions
				foreach($admin['a_options'] as $option_name => $option_value)
				{
					$admin[$option_name] = $option_value;
				}
			}
			else
			{

			}

			// unset admin options
			unset($admin['a_options']);

			$sql = 'UPDATE
						admins
					SET
						a_datetime_last_login = NOW(),
						a_ip = ?
					WHERE
						a_id = ?';

			$this->db->query($sql, array(getenv('REMOTE_ADDR'), $admin['a_id']));

			return $admin;
		}

		return false;
	}


	public function check_for_ban($ip)
	{
        return FALSE;
		$sql = 'SELECT
					ip,
					TIME_FORMAT(datetime_set, "%h:%i%p") AS datetime_set_format
				FROM
					logins
				WHERE
					ip = ?
					AND attempts = 3
					AND NOW() < DATE_ADD(datetime_set, INTERVAL 10 MINUTE);';

		return $this->db->query($sql, array($ip))->row_array();
	}


	public function set_failed_login($ip)
	{
		// get number of failed login attempts for ip
		$sql = 'SELECT
					attempts
				FROM
					logins
				WHERE
					ip = ?';

		// if failed login attempts exist, must update row
		if($row = $this->db->query($sql, array($ip))->row_array())
		{
			// if attempts = 3, we know this ip has been banned before, but 10 mins have passed
			// because it wasn't picked up before by check_for_ban(), in that case set the
			// number of attempts to 1, or if it doesn't = 3, set it to incrememnt as normal
            // $attempts = ($row['attempts'] == 3 ? 1 : $row['attempts'] + 1);
			$attempts = $row['attempts'] + 1;

			$sql = 'UPDATE
						logins
					SET
						attempts = ?,
						datetime_set = NOW()
					WHERE
						ip = ?';

			$this->db->query($sql, array($attempts, $ip));

			// if a ban has just been set, then redirect to start to invoke check_for_ban()
			// if($attempts == 3)
			// 	redirect('/');
		}
		else
		{
			// else we insert a new row
			$sql = 'INSERT INTO
						logins
					SET
						ip = ?,
						attempts = 1,
						datetime_set = NOW();';

			$this->db->query($sql, $ip);
		}
	}


	public function clear_failed_logins($ip)
	{
		$sql = 'DELETE FROM
					logins
				WHERE
					ip = ?';

		$this->db->query($sql, array($ip));
	}


	public function get_blocked_ips()
	{
		$sql = 'SELECT
					ip,
					TIME_FORMAT(datetime_set, "%h:%i%p") AS datetime_set_format
				FROM
					logins
				WHERE
					attempts = 3
					AND NOW() < DATE_ADD(datetime_set, INTERVAL 10 MINUTE);';

		return $this->db->query($sql)->result_array();
	}


	public function unblock_ip($ip)
	{
		$sql = 'DELETE FROM
					logins
				WHERE
					ip = ?';

		$this->db->query($sql, array($ip));

		$this->session->set_flashdata('action', 'IP unblocked');
	}


}
