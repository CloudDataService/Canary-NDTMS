<?php

class MY_controller extends CI_Controller {

	public $data; // global variable that will contain all data to be sent to view

	public function __construct()
	{
		parent::__construct();

		// load in sentry
		require_once( APPPATH . 'third_party/sentry-raven.php' );

		// load ircmaxell/password_compat
		require_once( APPPATH . 'third_party/password-compat/lib/password.php' );

		$this->_set_version();

		$this->load->library('layout');

		// header to stop browser bfcache
		header("cache-Control: no-store, no-cache, must-revalidate");
		header("cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header('Content-Type: text/html; charset=utf-8');

		// set site name as first item of site title
		$this->layout->set_title($this->config->item('site_name'));

		$nav = array();

		if($this->uri->segment(1) == 'admin')
		{
			// set default template
			$this->layout->set_template('default');

			$nav = array(
				'Dashboard' => '/admin',
				'Clients' => '/admin/clients',
				'Journeys' => array(
					'Service User' => '/admin/journeys',
					'Families' => '/admin/families',
				),
				'Activities' => '/admin/activities',
			);

			if (element('apt_reports', $this->session->userdata('permissions'), 0) == 1)
			{
				$nav['Reports'] = '/admin/reports';
			}

			// if the master admin is not logged in
			if(isset($this->session->userdata['permissions']) && $this->session->userdata['permissions']['apt_can_manage_options'] == 1)
			{
				// create options drop down
				$nav['Options']['Key Workers'] = '/admin/options/key-workers';
				$nav['Options']['Job Roles'] = '/admin/options/job-roles';
				$nav['Options']['Staff'] = '/admin/options/staff';
				$nav['Options']['Internal Services'] = '/admin/options/internal-services';
				$nav['Options']['Referral sources'] = '/admin/options/referral-sources';
				$nav['Options']['DNA reasons'] = '/admin/options/dna-reasons';
				$nav['Options']['Event types'] = '/admin/options/event-types';
				$nav['Options']['Mail Merge'] = '/admin/mail-merge';
				$nav['Options']['Assessment Criteria'] = '/admin/options/assessment-criteria';
				$nav['Options']['Agencies'] = '/admin/options/agencies';
				$nav['Options']['Log'] = '/admin/options/usage-log';
				$nav['Options']['Terms &amp; Conditions'] = '/admin/options/terms-and-conditions';
			}

			$permissions = element('permissions', $this->session->all_userdata(), false);
			if ($permissions !== false && $permissions['apt_can_manage_admins'] == 1)
			{
				//they can manage admins
				$nav['Options']['Administrators'] = '/admin/options/administrators';
			}

			if($permissions !== false && $permissions['apt_can_manage_options'] == 1 || $permissions['apt_can_manage_admins'] == 1)
			{
				//still need to manage themselves
				$nav['Options']['My account'] = '/admin/options/my-account';
			}
			else
			{
				// just have the one link for my account, instead of options
				$nav['My account'] = '/admin/options/my-account';
			}

			if ($this->session->userdata('rc_id'))
			{
				$nav['Journeys']['Allocated to me'] = '/admin/journeys/keyworker';
			}

			$nav['Logout'] = '/logout';
		}

		// set nav
		$this->layout->set_nav($nav);

		// set global css for site
		$this->layout->set_css(array('screen', 'jquery-ui-1.8.21.custom'));

		// set global js for site
		$this->layout->set_js(array('jquery-1.7.2.min', 'jquery-ui-1.8.21.custom.min', 'plugins/jquery.chained.min', 'plugins/readmore.min', 'views/default'));

		// set and load cache driver
		$cache_adapter = (ENVIRONMENT == 'production') ? 'file' : 'dummy';
		$this->load->driver('cache', array('adapter' => $cache_adapter));

		// Allow the profiler to be shown using the GET var if we're in dev mode
		if (ENVIRONMENT == 'development' && $this->input->get('profiler'))
		{
			$this->output->enable_profiler(TRUE);
		}

		//only timeout the login if we're on the live/production site.
		if (defined('ENVIRONMENT') && ENVIRONMENT == 'production') {
				$this->layout->set_js('views/login-timeout'); //login times out after 15mins inactivity
		}
	}




	/**
	 * Get the SVN revision deployed, or generate ID on development. Stores data in config
	 */
	private function _set_version()
	{
		if (ENVIRONMENT === 'development')
		{
			// Locally, just use time
			$version = time() . ".dev on commit " . $this->_git_commit_version(false);
		}
		else
		{
			$version = $this->_git_commit_version();

			if (! $version)
			{
				$version = file_get_contents('../.site_revision', FALSE, NULL, -1, 8);
				$version = (strlen($version) < 8) ? time() : $version;
			}
		}

		$this->config->set_item('version', $version);
	}

	private function _git_commit_version($truncate = true) {
		// Get version number from git repo or file
		if (file_exists("../.git/HEAD"))
		{
			$matches = array();
			if (! preg_match("/refs\/heads\/(.*)$/", file_get_contents("../.git/HEAD"), $matches))
				return false;

			$branch = $matches[1];
			$version = file_get_contents("../.git/refs/heads/$branch");

			if ($truncate)
				$version = substr($version, 0, 9);

			return $version;
		}
		else
		{
			return false;
		}

	}



}
