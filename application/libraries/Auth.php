<?php

class Auth {

	protected $_CI;

	public function __construct()
	{
		// get instance of CI object
		$this->_CI =& get_instance();
		$this->_CI->load->helper('url');
	}

	public function check_admin_logged_in($redirect = TRUE)
	{
		// is the admin not logged in?
		if( ! $this->_CI->session->userdata('a_id') OR ! $this->_CI->session->userdata('logged_in'))
		{
			// is redirect parameter is true?
			if($redirect == TRUE)
			{
				// redirect auth failed
				redirect('/?auth_failed=1');
			}
			else
			{
				return FALSE;
			}
		}
		else
		{
			// admin is logged in, return object
			return $this;
		}
	}

	public function check_password_expiry() {

		if ($this->check_password_expired())
			redirect("/admin/reset");
	}

	public function check_password_expired() {
		$id = $this->_CI->session->userdata('a_id');

		$expiry = $this->_CI->cache->get("password_expiry:{$id}");

		if (!$expiry)
		{
			$sql = "select a_expires_at from admins where a_id = ?";
			$result = $this->_CI->db->query($sql, array($id))->row_array();
			$expiry = @$result["a_expires_at"];
			$this->_CI->cache->save("password_expiry:{$id}", $expiry, 300);
		}
		$now = new DateTime;
		return is_null($expiry) or ($now > DateTime::createFromFormat("Y-m-d H:i:s", $expiry));
	}

	public function check_master_admin_logged_in()
	{
		if( ! $this->_CI->session->userdata('a_master'))
			show_404();
	}

	// checks to see if the user is logged in, in any capacity, if they are, redirects them to the appropriate section
	public function check_logged_in()
	{
		// if admin is logged in
		if($this->_CI->session->userdata('a_id') && $this->_CI->session->userdata('logged_in'))
			redirect('/admin');

	}

	/*
	 * Taken from https://github.com/ircmaxell/PHP-PasswordLib/blob/master/lib/PasswordLib/Password/AbstractPassword.php
	 * License: MIT (c) Anthony Ferrara <ircmaxell@ircmaxell.com>
	 * A time-constant string comparison that resists timing attacks.
	 */
	public function compare_strings($hash1, $hash2)
	{
        $key   = $this->secure_random(1024);
        $hash1 = hash_hmac('sha512', $hash1, $key, true);
        $hash2 = hash_hmac('sha512', $hash2, $key, true);

        $len  = strlen($hash1);
        $result = 0;
        for ($i = 0; $i < $len; $i++)
        {
            $result |= ord($hash1[$i]) ^ ord($hash2[$i]);
        }
        return $result === 0;
    }

    public function secure_random($bytes)
    {
    	if ( ! function_exists('mcrypt_create_iv'))
    		die('The mcrypt extension is required for security-related functions.');

    	return mcrypt_create_iv($bytes, MCRYPT_DEV_URANDOM);
    }

    public function key_check($key)
	{
		if (ENVIRONMENT == 'development')
			return true;

		$secure = $this->is_secure();

		return $this->compare_strings($this->_CI->config->item('cron_key'), $key) and $secure;
	}

	public function hash_hmac($data) {
		return hash_hmac("sha512", $data, $this->_CI->config->item('encryption_key'));
	}

	public function is_secure() {
		if ( ! $this->_CI->config->item('cron_secure'))
			return true;

		return
			// normally set by FastCGI/fpm
			(strtolower($this->_CI->input->server('HTTPS')) === "on") or
			// sometimes cgi variables aren't set, so check the port
			($this->_CI->input->server('SERVER_PORT') == '443');
	}
}
