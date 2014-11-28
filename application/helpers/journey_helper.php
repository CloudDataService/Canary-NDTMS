<?php

function journey_ass_date($date = '', $format = 'd/m/Y')
{
	if (empty($date)) return 'N/A';

	$today = new DateTime();
	$dt = new DateTime($date);

	if ($dt->format('Y-m-d') === $today->format('Y-m-d'))
	{
		$str = '<span class="csop-due today">%s</span>';
	}
	elseif ($dt < $today)
	{
		$str = '<span class="csop-due overdue">%s</span>';
	}
	elseif ($dt > $today)
	{
		$str = '<span class="csop-due future">%s</span>';
	}

	return sprintf($str, $dt->format($format));
}




/**
 * Check if a new assessment can be taken in line with the allowed window.
 *
 * Adds the interval (default 6 weeks) to the original date, and returns TRUE if today is on or after that.
 *
 * @param string $last_date		Date of the previous assessment
 * @param string $interval_str		How much time must have passed since $last_date to return true
 * @return bool		TRUE if today is on or after $last_date + $interval_str; or TRUE if $last_date is empty
 */
function journey_ass_window($last_date = '')
{
	if (empty($last_date)) return array('valid' => TRUE);

	$interval_start = new DateInterval('P6W');
	$interval_end = new DateInterval('P8W');

	$today = new DateTime();

	$window_start = new DateTime($last_date);
	$window_start->add($interval_start);

	$window_end = new DateTime($last_date);
	$window_end->add($interval_end);

	return array(
		'start' => $window_start,
		'end' => $window_end,
		'valid' => $today >= $window_start,
	);
}




/**
 * Journey status: get the "next" status the journey should move to, given the current ID.
 *
 * @param int $current_j_status		ID of the current journey status
 * @return array		Array of ID => name of the next status
 */
function journey_next_status($current_j_status = 0)
{
	$status_codes = config_item('j_status_codes');
	$status_data = config_item('j_status_data');

	$next_id = element('next', $status_data[$current_j_status]);

	return array($next_id, element($next_id, $status_codes));
}




/**
 * Determine whether or not the user can edit an event based on the rules,
 *
 * Rulebook:
 *
 * 0. Implicit NO.
 * 1. If user is master admin, then YES
 * 2. If status is draft (published=0) and user is owner: YES
 * 3. If status is draft (published=0) and user is NOT owner: NO
 * 4. If status is published (published=1) then NO
 */
function can_edit_event($event)
{
	$CI =& get_instance();

	// Is user master admin?
	$is_master = $CI->session->userdata('a_master');

	// Is logged in user the owner?
	$is_owner = ((int) $CI->session->userdata('a_id') == (int) $event['je_added_a_id']);

	// Is it published?
	$published = ((int) $event['je_published'] == 1);

	// Rule 1 met.
	if ($is_master) return TRUE;

	// Rule 2 met.
	if ($is_owner && $published == 0) return TRUE;

	// YES conditions haven't ben met. Big fat no.
	return FALSE;

}