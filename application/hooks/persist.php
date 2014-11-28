<?php

function persist_session()
{
	// get instance of CI
	$CI =& get_instance();
	
	// if we are attempting to check whether the session has expired
	if(isset($_GET['check_persist_session']))
	{
		// get persist session
		$persist_session = $CI->session->userdata('persist_session');
		
		// if the user's last active time is 15 minutes or more
		if($persist_session <= (time() - 900))
		{
			// user must be timed out
			error_log('false');
			echo 'false';
		}
		else
		{
			// user can can continue working
			echo 'true';	
		}
		
		// exit script
		exit;
	}
	else
	{
		// get current time
		$current_time = time();

		// set persist session to current time
		$CI->session->set_userdata('persist_session', $current_time);
	}	
}