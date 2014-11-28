<?php

class My_form_validation extends CI_form_validation {


	protected $_CI;


	public function __construct()
	{
		parent::__construct();

		$this->_CI = get_instance();
	}


	public function check_admin_email_unique($email, $admin_id = 0)
	{
		if( ! isset($this->CI->admins_model))
			$this->CI->load->model('admins_model');

		return $this->CI->admins_model->check_email_unique($email, $admin_id);
	}


	public function check_admin_current_password($password, $admin_id)
	{
		if( ! isset($this->CI->admins_model))
			$this->CI->load->model('admins_model');

		return $this->CI->admins_model->check_password($password, $admin_id);
	}


	public function parse_date($date)
	{
		if( ! preg_match('/^[0-3]{1}[0-9]{1}\/[0-1]{1}[0-9]{1}\/[0-9]{4}$/', $date) )
			return false;

		$date = explode('/', $date);

		$mysql_date = $date[2] . '-' . $date[1] . '-' . $date[0];

		return $mysql_date;
	}

	/*
	 * Passwords should:
	 *
	 * 1: have no max length
	 * 2: have no restrictions on characters allowed
	 * 3: be copy-pastable from password managers (dont block ctrl/cmd+v in forms)
	 * 4: use a realistic measure of strength instead of requiring a password ruleset (see zxcvbn.js)
	 * 4a: "Abcdefgh1234" is not more secure than "some people may call me time, but that doesnt mean they're right"
	 *	   just because it has a capital letter and numbers.
	 * 5: be a sentence
	 * 6: use iterative hashing and fixed-time hash comparison
	 */
	function password_restrict($password)
	{
		return strlen($password) > 12;
	}

	function other($index)
	{
		if(@$_POST[$index] == 'Other')
		{
			return '|required';
		}

		return false;
	}


	function numeric($str)
	{
		if((bool)preg_match( '/^[\-+]?[0-9]*\.?[0-9]+$/', $str))
		{
			return sprintf('%.2F', $str);
		}

		return false;
	}


}
