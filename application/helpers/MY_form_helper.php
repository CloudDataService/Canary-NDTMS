<?php

function form_prep($str)
{
	return htmlentities($str, ENT_QUOTES, 'UTF-8');
}


function parse_date($date)
{
	if ($date === NULL)
		return NULL;

	if ( ! preg_match('/^[0-3]{1}[0-9]{1}\/[0-1]{1}[0-9]{1}\/[0-9]{4}$/', $date))
		return false;

	$date = explode('/', $date);

	$mysql_date = $date[2] . '-' . $date[1] . '-' . $date[0];

	return $mysql_date;
}




/**
 * Format an array to key => value format for use in a dropdown box.
 *
 * @param $data array		DB result array
 * @param string $id		name of ID column to use for the result array key
 * @param string $title		name of title column to use for the result array value
 * @param bool|string $placeholder		Label for the first option that has a NULL value (e.g. Please Select, or (None))
 * @author CR
 */
function db_dropdown($data = array(), $id = '', $title = '', $placeholder = FALSE)
{
	$result = array();

	if ($placeholder !== FALSE)
	{
		$result[''] = $placeholder;
	}

	foreach ($data as $row)
	{
		$result[$row[$id]] = $row[$title];
	}

	return $result;

}