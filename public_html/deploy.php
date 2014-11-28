<?php

define('REV_FILE', '../.site_revision');

if (array_key_exists('payload', $_POST))
{
	$handle = fopen(REV_FILE, 'w') or die("Can't open file");
	$deploy_data = json_decode(stripslashes($_POST['payload']));
	$rev = substr($deploy_data->end_revision->ref, 0, 8);

	fwrite($handle, $rev);
	fclose($handle);
}