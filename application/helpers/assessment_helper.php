<?php

/**
 * Parse the database-returned assessments & scores to a structure more suitable for the charts.
 *
 * This generates the array structure for the grouped outcomes & scores over 8 assessments.
 */
function assessment_scores_csop($assessments = array())
{
	$data = array();
	$scores = array();

	// Put the score data in ascending order
	ksort($assessments);

	$x_labels = array(
		1 => 'Intake',
		2 => 'Review 1',
		3 => 'Review 2',
		4 => 'Review 3',
		5 => 'Review 4',
		6 => 'Review 5',
		7 => 'Review 6',
		8 => 'Discharge',
	);

	$acl_reports = config_item('acl_reports');
	$csop = $acl_reports['csop'];

	$i = 1;
	foreach ($assessments as $ass)
	{
		foreach ($ass['scores'] as $score)
		{
			$scores[$i]["{$score['jacs_num']}"] = $score['jacs_score'];
		}

		$i++;
	}

	foreach ($csop['outcomes'] as $title => $outcomes)
	{
		$data[$title] = array();

		// First row - headings.

		$data[$title][0] = array('Assessment');
		foreach ($outcomes as $num => $label)
		{
			$data[$title][0][] = $label;
		}

		// Loop through the fixed number of assessments

		for ($i = 1; $i <= 8; $i++)
		{
			// Build up a row array for this assessment
			$row = array();

			// First col will be the assessment index name
			$row[0] = $x_labels[$i];

			// Iterate through the outcomes for this category
			foreach ($outcomes as $aco_num => $out)
			{
				// Retrieve the score based on the outcome number
				$score = element($i, $scores, array());
				$score = element($aco_num, $score, NULL);
				// Add the score to the row
				$row[] = $score;
			}

			$data[$title][$i] = $row;
		}
	}

	return $data;
}