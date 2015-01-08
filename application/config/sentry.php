<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
 * Sentry config - https://getsentry.com
 *
 * Set to true and add your app client ID to enable remote error logging.
 */
$config['sentry_enabled'] = ENVIRONMENT != 'development';
$config['sentry_client_id'] = 'http://ea0130b6b3bb4982aa92aa7c59df57f9:87a8186d5ce841a0985614a60675fa1f@sentry.clouddataservice.co.uk/8';
