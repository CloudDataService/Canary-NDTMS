<?php

class Family_report_model extends Reports_Model
{


	public function __construct()
	{
		parent::__construct();
	}




	/**
	 * Family Team reporting (table)
	 */
	public function family($filter = array())
	{
		$sql = 'SELECT

					SUM(j_id) AS total,
					SUM(IF(j_type = "F", 1, 0)) AS carers_supported,
					SUM(IF(j_type = "F" AND j_status IN(1, 2, 4), 1, 0)) AS carers_referred,
					SUM(IF(j_type = "F" AND rc_jr_id = 3, 1, 0)) AS volunteers_recruited,
					SUM(IF(j_type = "F" AND jc_improve_psych = 1, 1, 0)) AS carers_improve_psych,
					SUM(IF(j_type = "F" AND jc_access_support = 1, 1, 0)) AS carers_access_support,
					SUM(IF(j_type = "F" AND jc_access_respite = 1, 1, 0)) AS carers_access_respite,
					SUM(IF(j_type = "F" AND jc_improve_social = 1, 1, 0)) AS carers_improve_social,
					SUM(IF(j_type = "F" AND jc_attend_parent = 1, 1, 0)) AS people_attend_parent_factor,
					SUM(IF(j_type = "F" AND jc_improve_child = 1, 1, 0)) AS carers_improve_child,

					NULL AS marketing,

					SUM(IF(j_type = "F" AND c_gender = 2 AND jc_attend_freedom = 1, 1, 0)) AS women_freedom,
					SUM(IF(j_type = "F" AND j_tier = "Tier 3" AND jc_interval_1to1 BETWEEN 1 AND 28, 1, 0)) AS carers_open_t3_1to1_4,
					SUM(IF(j_type = "F" AND j_tier = "Tier 3" AND ji_csop_due IS NOT NULL, 1, 0)) AS carers_open_t3_csop_plan,
					SUM(IF(j_type = "F" AND j_tier = "Tier 3" AND jc_csop_total > 1, 1, 0)) AS carers_open_t3_csop_graph,
					SUM(IF(j_type = "F" AND jc_date_ass1 IS NOT NULL AND jc_date_csop1 IS NOT NULL AND jc_date_ass1 = jc_date_csop1, 1, 0)) AS carers_csop_first_appt,
					SUM(IF(j_type = "F" AND jc_csop_interval BETWEEN 1 AND 28, 1, 0)) AS csop_reviews_28,
					SUM(IF(j_type = "F" AND j_tier = "Tier 2" AND jc_interval_1to1 BETWEEN 1 AND 84, 1, 0)) AS carers_open_t2_1to1_12,

					0 AS _final
				FROM
					journeys
				LEFT JOIN
					journeys_info
					ON ji_j_id = j_id
				LEFT JOIN
					journeys_cache
					ON jc_j_id = j_id
				LEFT JOIN
					clients
					ON j_c_id = c_id
				LEFT JOIN
					recovery_coaches
					ON j_rc_id = rc_id
				WHERE
					1 = 1
				' . $this->filter_sql() . '
				';

		return array(
			'sql' => $sql,
			'data' => $this->db->query($sql)->row_array(),
		);
	}




}