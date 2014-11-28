<?php

class Mail_merge_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	// returns array of all mail merge documents
	public function get_mail_merges()
	{
		$sql = 'SELECT
					mm_id,
					mm_title
				FROM
					mail_merges
				WHERE
					1=1 ;';

		return $this->db->query($sql)->result_array();
	}

	// get a single mail merge document
	public function get_mail_merge($mm_id)
	{
		// if mm_id is false
		if( ! $mm_id)
			return false;

		$sql = 'SELECT
					mm_id,
					mm_title,
					mm_body,
					mmf_src,
					mmf_size
				FROM
					mail_merges
				LEFT JOIN
					mail_merge_bgfiles
						ON 1 = 1
				WHERE
					mm_id = "' . (int)$mm_id . '"
				;';

		return $this->db->query($sql)->row_array();
	}

	// add or update a mail merge document
	public function set_mail_merge($mm_id = 0)
	{
		// if mm_id is true
		if($mm_id)
		{
			$sql = 'UPDATE
						mail_merges
					SET
						mm_title = ?,
						mm_body = ?
					WHERE
						mm_id = "' . (int)$mm_id . '"
					;';

			// set update notifcation
			$this->session->set_flashdata('action', 'Mail merge document updated');
		}
		else
		{
			$sql = 'INSERT INTO
						mail_merges
					SET
						mm_title = ?,
						mm_body = ?
						;';

			// set created notifcation
			$this->session->set_flashdata('action', 'Mail merge document created');
		}

		// commit query
		$this->db->query($sql, array($this->input->post('mm_title'),
									 $this->input->post('mm_body')));
	}


	public function delete_mail_merge($mm_id, $sp_id)
	{
		$this->db->delete('mail_merges', array('mm_id' => $mm_id));
	}



	// takes
	public function parse_mail_merge($mail_merge_text, $data)
	{
		$search = array();
		$replace = array();

		$mail_merge_aliases = $this->get_aliases();

		$excludes = array(
                '[triage_appointment_datetime]',
                '[triage_appointment_counsellor]',
                '[comprehensive_appointment_datetime]',
                '[comprehensive_appointment_counsellor]',
                '[first_appointment_datetime]',
                '[first_appointment_counsellor]',
                '[general_appointment_datetime]',
                '[general_appointment_counsellor]',
                '[previous_assessment_datetime]',
                '[current_assessment_datetime]',
                '[current_assessment_counsellor]',
                '[future_assessment_datetime]',
                '[current_general_appointment_datetime]',
                '[current_general_appointment_counsellor]',
            );

		function postcode_add_space($postcode)
		{
			return substr($postcode, 0, -3) .' '. substr($postcode, -3);
		}

		foreach($mail_merge_aliases as $alias)
		{
            if(!isset($data['appointments']))
                $data['appointments'] = $this->get_journey_appointments($data['j_id']);

            if(!isset($data['events']))
                $data['events'] = $this->get_journey_events($data['j_id']);

			$value = '';
            if($alias->field_names == 'all_appointments') {
            	$value = $this->load->view('admin/options/mail_merge/pdf/appointments', array('appointments' => $data['appointments']), TRUE);
            }
            elseif($alias->field_names == 'all_events') {
            	$value = $this->load->view('admin/options/mail_merge/pdf/events', array('events' => $data['events']), TRUE);
            }
			elseif(strrpos($alias->field_names, ',') != FALSE )	{
				$parts = explode(',', $alias->field_names);
				foreach($parts as $key) {
					if( substr($key, -9) == 'post_code')
					{
						$value .= "<br />\n" . postcode_add_space($data[$key]) . " ";
					} else if($alias->is_multi_line) {
						$value .= nl2br($data[ $key ]);
					} else {
						$value .= $data[ $key ];
					}
				}
				rtrim($value);
			} else {
				if(!in_array($alias->field_names, $excludes))	{
					if( substr($alias->field_names, -9) == 'post_code')
					{
						$value = postcode_add_space($data[$alias->field_names]) . " ";
					}
					else
					{
						$value = element($alias->field_names, $data, "???");	//$data[ $alias->field_names ];
					}
				}
			}
			$search[] = $alias->alias;
			$replace[] = $value;
		}

        // header("Content-Type: text/plain");
        // print_r($data);
        // print_r($search);
        // print_r($replace);
        // exit;

        $client_address  = nl2br($data['c_address']) . '<br />' . $data['c_post_code'];
        $service_address = nl2br($this->config->item('service_address'));

		function array_search2d($needle, $haystack) {
		    for ($i = 0, $l = count($haystack); $i < $l; ++$i) {
		        if ($needle === $haystack[$i]) return $i;
		    }
		    return false;
		}

		function format_datetime($datetime) {
			return date( 'l dS F h:ia', strtotime($datetime));
		}

		if(in_array('[triage_appointment_datetime]', $search) OR in_array('[triage_appointment_counsellor]', $search))	{
			$appointments = $this->get_appointment_type(@$data['c_id'], 'Triage');
			if($appointments->num_rows() > 0) {
				if(in_array('[triage_appointment_datetime]', $search))	{
					$index = array_search2d('[triage_appointment_datetime]', $search);
					$replace[ $index ] = format_datetime($appointments->row()->a_datetime_of_appt);
				}
				if(in_array('[triage_appointment_counsellor]', $search)) {
					$index = array_search2d('[triage_appointment_counsellor]', $search);
					$replace[ $index ] = $appointments->row()->spc_name;
				}
			}
		}

		if(in_array('[comprehensive_appointment_datetime]', $search) OR in_array('[comprehensive_appointment_counsellor]', $search))	{
			$appointments = $this->get_appointment_type(@$data['c_id'], 'Assessment');
			if($appointments->num_rows() > 0) {
				if(in_array('[comprehensive_appointment_datetime]', $search)) {
					$index = array_search2d('[comprehensive_appointment_datetime]', $search);
					$replace[ $index ] = format_datetime($appointments->row()->a_datetime_of_appt);
				}
				if(in_array('[comprehensive_appointment_counsellor]', $search))	{
					$index = array_search2d('[comprehensive_appointment_counsellor]', $search);
					$replace[ $index ] = $appointments->row()->spc_name;
				}
			}
		}

		if(in_array('[first_appointment_datetime]', $search) OR in_array('[first_appointment_counsellor]', $search))	{
			$appointments = $this->get_appointments(@$data['c_id']);
			if($appointments->num_rows() > 0) {
				if(in_array('[first_appointment_datetime]', $search)) {
					$index = array_search2d('[first_appointment_datetime]', $search);
					$replace[ $index ] = format_datetime($appointments->row()->a_datetime_of_appt);
				}
				if(in_array('[first_appointment_counsellor]', $search))	{
					$index = array_search2d('[first_appointment_counsellor]', $search);
					$replace[ $index ] = $appointments->row()->spc_name;
				}
			}
		}


		if(in_array('[general_appointment_datetime]', $search) OR in_array('[general_appointment_counsellor]', $search))	{
			$appointments = $this->get_appointment_type(@$data['c_id'], 'General');
			if($appointments->num_rows() > 0) {
				if(in_array('[general_appointment_datetime]', $search)) {
					$index = array_search2d('[general_appointment_datetime]', $search);
					$replace[ $index ] = format_datetime($appointments->row()->a_datetime_of_appt);
				}
				if(in_array('[general_appointment_counsellor]', $search))	{
					$index = array_search2d('[general_appointment_counsellor]', $search);
					$replace[ $index ] = $appointments->row()->spc_name;
				}
			}
		}

		//Date of First Offer of Assessment and Appointment was not attended
		if(in_array('[previous_assessment_datetime]', $search))
		{
			$sql = "SELECT
						a_datetime_of_appt
					FROM
						appointments
					WHERE
						a_c_id = ".(int)@$data['c_id']."
					AND
						a_datetime_of_appt < NOW()
					AND
						a_type = 'Assessment'
					AND
						a_attended != 1
					ORDER BY
						a_datetime_of_appt ASC
					;";

			$result = $this->db->query($sql); //->row_array();
			if($result->num_rows() > 0) {
				$index = array_search2d('[previous_assessment_datetime]', $search);
				$replace[ $index ] = format_datetime($result->row()->a_datetime_of_appt);
			}
		}

		// perform string replace
		return str_replace($search, $replace, $mail_merge_text);
	}

	// returns all the necessary information required for a mail merge
	public function get_journey($j_id)
	{
		$sql = 'SELECT
					j_id,
					IF(j_type = "C", "Service User", IF(j_type = "F", "Family", "Unknown")),
					DATE_FORMAT(CURDATE(), "%d/%m/%Y") AS date_current,
					DATE_FORMAT(j_datetime_created, "%d/%m/%Y") AS j_datetime_created,
					DATE_FORMAT(j_date_of_referral, "%d/%m/%Y") AS j_date_of_referral,
					DATE_FORMAT(j_date_of_triage, "%d/%m/%Y") AS j_date_of_triage,
					DATE_FORMAT(j_date_first_appointment, "%d/%m/%Y") AS j_date_first_appointment,
					DATE_FORMAT(j_date_first_appointment_offered, "%d/%m/%Y") AS j_date_first_appointment_offered,
					DATE_FORMAT(j_date_first_assessment, "%d/%m/%Y") AS j_date_first_assessment,
					DATE_FORMAT(j_date_last_assessment, "%d/%m/%Y") AS j_date_last_assessment,
					j_family_or_carer_involved,
					j_tier,

                    ji_referrers_name,
                    IF(ji_rs_id IS NULL, "N/A", rs_name) AS ji_referral_source,

					c_fname,
					c_sname,
					IF(c_gender = 1, "Male", "Female") AS c_gender,
					DATE_FORMAT(c_date_of_birth, "%d/%m/%Y") AS c_date_of_birth,
					c_address,
					c_post_code,
					c_catchment_area,
					c_tel_home,
					c_tel_mob
				FROM
					journeys
				LEFT JOIN
					journeys_info
						ON j_id = ji_j_id
				LEFT JOIN
					clients
						ON j_c_id = c_id
                LEFT JOIN
                    referral_sources
                        ON rs_id = ji_rs_id
				WHERE
					j_id = "' . (int)$j_id . '"
				;';

		return $this->db->query($sql)->row_array();
	}


	public function get_aliases()
	{
		$this->db->from('mail_merge_aliases');
		$this->db->order_by('alias');
		return $this->db->get()->result();
	}

	/**
	 * Get the background file for mail merges, from the database
	 */
	public function get_background_file()
	{
		$sql = 'SELECT
					mmf_id,
					mmf_src,
					mmf_size
				FROM mail_merge_bgfiles
				WHERE 1=1;';
		$result = $this->db->query($sql)->row_array();
		return $result;
	}

	/**
	 * Changes the database to reflect the new template background uploaded
	 */
	public function upload_template_file($filedata)
	{
		// see if the service provider already has a template file
		$sql = 'SELECT mmf_id
				FROM mail_merge_bgfiles
				WHERE 1=1;';

		$fileresult = $this->db->query($sql)->row_array();

		// update the db with the new file
		if (!empty($fileresult))
		{
			$sql = 'UPDATE
						mail_merge_bgfiles
					SET
					';
		}
		else
		{
			$sql = 'INSERT INTO
						mail_merge_bgfiles
					SET
					';
		}
		$sql .= 'mmf_src = ?,
				 mmf_size = ?
				 ';
		if (!empty($fileresult))
		{
			$sql .= 'WHERE
						1=1;
					';
		}

		$this->db->query($sql, array(
                $filedata['file_name'],
                $filedata['file_size'],
            ));

		$this->session->set_flashdata('action', 'Mail merge background image updated.');
	}

	public function delete_template()
	{
		// get template
		$sql = 'SELECT
					mmf_src
				FROM
					mail_merge_bgfiles
				WHERE
					1=1;';

		// if template file is returned
		if($template = $this->db->query($sql)->row_array())
		{
			// remove template image from disk
			@unlink(FCPATH . '/mailmerge/images/' . $template['mmf_src']);

			// delete template from db
			$sql = 'DELETE FROM
						mail_merge_bgfiles
					WHERE
						1=1;';

			$this->db->query($sql);

			// set deleted notification
			$this->session->set_flashdata('action', 'Mail merge background image removed.');

			return TRUE;
		}
	}

    /**
     * Handy function to look up the journey type, to use for permission checking.
     */
    public function check_journey_type($j_id)
    {
        $sql = 'SELECT
                    j_type
                FROM
                    journeys
                WHERE
                    j_id = '. (int)$j_id .'
                LIMIT 1;';
        $result = $this->db->query($sql)->row_array();
        if($result)
        {
            return $result['j_type'];
        }
        return FALSE;
    }

    // returns a formatted array of all appointments assigned to a journey
    public function get_journey_appointments($j_id)
    {
        //Steady on, what permissions do we have?
        $j_type = $this->check_journey_type($j_id);
        if($j_type == 'C' && $this->session->userdata['permissions']['apt_can_read_client'] != 1)
        {
            return FALSE;
        }
        elseif($j_type == 'F' && $this->session->userdata['permissions']['apt_can_read_family'] != 1)
        {
            return FALSE;
        }

        $sql = 'SELECT
                    ja_id,
                    IF(ja_date_offered IS NULL, "N/A", DATE_FORMAT(ja_date_offered, "%d/%m/%Y")) AS ja_date_offered_format,
                    DATE_FORMAT(ja_datetime, "%d/%m/%Y at %H:%i") AS ja_datetime_format,
                    rc_name AS ja_rc_name,
                    ja_notes,
                    dr_name,
                    IF(ja_attended IS NULL, "N/A", IF(ja_attended = 1, "Yes", "No")) AS ja_attended,
                    IF(ja_attended = 2, dr_name, NULL) AS ja_dr_name,
                    ja_length
                FROM
                    journey_appointments
                LEFT JOIN
                    recovery_coaches
                        ON rc_id = ja_rc_id
                LEFT JOIN
                    dna_reasons
                        ON dr_id = ja_dr_id
                WHERE
                    ja_j_id = "' . (int)$j_id . '"
                ORDER BY
                    ja_datetime DESC';

        //$explain = $this->db->query($sql)->row_array(); print_r($explain); exit;

        return $this->db->query($sql)->result_array();
    }

    // returns formatted array of all events that occured during journey
    public function get_journey_events($j_id)
    {
        $sql = 'SELECT
                    je_id,
                    DATE_FORMAT(je_datetime, "%d/%m/%Y at %H:%i") AS je_datetime_format,
                    et_name AS je_et_name,
                    IF(je_notes IS NULL, "N/A", je_notes) AS je_notes,
                    IF(je_rc_id IS NULL, "N/A", rc_name) AS key_worker,
                    IF(je_added_a_id IS NULL, "N/A", added_admin.a_fname) AS added_by,
                    IF(je_updated_a_id IS NULL, "N/A", added_admin.a_sname) AS updated_by
                FROM
                    journey_events
                LEFT JOIN
                    event_types ON je_et_id = et_id
                LEFT JOIN
                    recovery_coaches ON je_rc_id = rc_id
                LEFT JOIN
                    admins added_admin ON je_added_a_id = added_admin.a_id
                LEFT JOIN
                    admins updated_admin ON je_updated_a_id = updated_admin.a_id
                WHERE
                    je_j_id = "' . (int)$j_id . '"
                ORDER BY
                	je_datetime DESC
                ';

        return $this->db->query($sql)->result_array();
    }

}
