<?php

class Clients_model extends CI_Model
{


    function __construct()
    {
        parent::__construct();
    }


    public function get_total_clients()
    {
        $sql = 'SELECT
                    COUNT(c_id) AS total
                FROM
                    clients
                LEFT JOIN
                    journeys ON c_id = j_c_id
                WHERE
                    1 = 1 ';

        if (@$_GET['c_id'])
            $sql .= ' AND c_id = ' . (int) $_GET['c_id'] . ' ';

        if(@$_GET['c_fname'])
            $sql .= ' AND CONVERT(c_fname USING latin1) = "' . mysql_real_escape_string($_GET['c_fname']) . '" ';

        if(@$_GET['c_sname'])
            $sql .= ' AND CONVERT(c_sname USING latin1) = "' . mysql_real_escape_string($_GET['c_sname']) . '" ';

        if(@$_GET['c_date_of_birth'])
            $sql .= ' AND c_date_of_birth = "' . parse_date($_GET['c_date_of_birth']) . '" ';

        if(@$_GET['c_post_code'])
            $sql .= ' AND CONVERT(c_post_code USING latin1) LIKE "' . mysql_real_escape_string(str_replace(' ', '', strtoupper($_GET['c_post_code']))) . '%" ';

        //check admin permission
        if($this->session->userdata['permissions']['apt_can_read_client'] != 1 && $this->session->userdata['permissions']['apt_can_read_family'] != 1)
        {
            return 0;
        }
        else if ($this->session->userdata['permissions']['apt_can_read_client'] != 1 )
        {
            $sql .= ' AND j_type = "F" ';
        }
        else if ($this->session->userdata['permissions']['apt_can_read_client'] != 1 )
        {
            $sql .= ' AND j_type = "C" ';
        }

        $row = $this->db->query($sql)->row_array();

        return $row['total'];
    }

    public function get_clients($start = 0, $limit = 0)
    {
        if( ! in_array(@$_GET['order'], array('c_id', 'c_sname', 'c_date_of_birth', 'c_post_code', 'j_type')) ) $_GET['order'] = 'c_id';

        if(@$_GET['sort'] != 'asc' && @$_GET['sort'] != 'desc') $_GET['sort'] = 'desc';

        $sql = 'SELECT
                    c_id,
                    CONCAT(c_fname, " ", c_sname) AS c_name,
                    IF(c_date_of_birth IS NULL, "N/A", DATE_FORMAT(c_date_of_birth, "%d/%m/%Y")) AS c_date_of_birth_format,
                    DATE_FORMAT(CURDATE(), "%Y") - DATE_FORMAT(c_date_of_birth, "%Y") - (DATE_FORMAT(CURDATE(), "00-%m-%d") < DATE_FORMAT(c_date_of_birth, "00-%m-%d")) AS c_age,
                    c_post_code,
                    c_is_risk,
                    j_type
                FROM
                    clients
                LEFT JOIN
                    journeys ON c_id = j_c_id
                WHERE
                    1 = 1 ';

        if (@$_GET['c_id'])
            $sql .= ' AND c_id = ' . (int) $_GET['c_id'] . ' ';

        if(@$_GET['c_fname'])
            $sql .= ' AND CONVERT(c_fname USING latin1) = "' . mysql_real_escape_string($_GET['c_fname']) . '" ';

        if(@$_GET['c_sname'])
            $sql .= ' AND CONVERT(c_sname USING latin1) = "' . mysql_real_escape_string($_GET['c_sname']) . '" ';

        if(@$_GET['c_date_of_birth'])
            $sql .= ' AND c_date_of_birth = "' . parse_date($_GET['c_date_of_birth']) . '" ';

        if(@$_GET['c_post_code'])
            $sql .= ' AND CONVERT(c_post_code USING latin1) LIKE "' . mysql_real_escape_string(str_replace(' ', '', strtoupper($_GET['c_post_code']))) . '%" ';

        //check admin permission
        if($this->session->userdata['permissions']['apt_can_read_client'] != 1 && $this->session->userdata['permissions']['apt_can_read_family'] != 1)
        {
            return 0;
        }
        elseif ($this->session->userdata['permissions']['apt_can_read_client'] != 1)
        {
            if(!isset($_GET['j_type']) || @$_GET['j_type'] != 'C')
            {
                $sql .= ' AND j_type = "F" ';
            }
        }
        else if ($this->session->userdata['permissions']['apt_can_read_client'] != 1)
        {
            if(!isset($_GET['j_type']) || @$_GET['j_type'] != 'F')
            {
                $sql .= ' AND j_type = "C" ';
            }
        }
        elseif(isset($_GET['j_type']) && @$_GET['j_type'] != 'C')
        {
            $sql .= ' AND j_type = "F" ';
        }
        elseif(isset($_GET['j_type']) && @$_GET['j_type'] != 'F')
        {
            $sql .= ' AND j_type = "C" ';
        }

        $sql .= ' GROUP BY c_id ';

        // set order by clause
        $sql .= ' ORDER BY ' . $_GET['order'] . ' ' . $_GET['sort'] . ' ';

        if($limit)
            $sql .= ' LIMIT ' . (int)$start . ', ' . (int)$limit;

        return $this->db->query($sql)->result_array();
    }

    public function get_client($c_id, $format = true)
    {
        // if client id is false
        if( ! $c_id)
            return false;


        // if we want to return the data formatted for view
        if($format)
        {
            // format sql
            $sql = 'SELECT
                        c_id,
                        c_fname,
                        c_sname,
                        IF(c_gender IS NULL, "N/A", IF(c_gender = 1, "Male", "Female")) AS c_gender,
                        IF(c_date_of_birth IS NULL, "N/A", DATE_FORMAT(c_date_of_birth, "%d/%m/%Y")) AS c_date_of_birth_format,
                        DATE_FORMAT(CURDATE(), "%Y") - DATE_FORMAT(c_date_of_birth, "%Y") - (DATE_FORMAT(CURDATE(), "00-%m-%d") < DATE_FORMAT(c_date_of_birth, "00-%m-%d")) AS c_age,
                        IF(c_address IS NULL, "N/A", c_address) AS c_address,
                        c_post_code,
                        pc_lat,
                        pc_lng,
                        c_catchment_area,
                        IF(c_tel_home IS NULL, "N/A", c_tel_home) AS c_tel_home,
                        IF(c_tel_mob IS NULL, "N/A", c_tel_mob) AS c_tel_mob,
                        c_is_risk
                    FROM
                        clients
                    LEFT JOIN
                        postcodes
                            ON
                                pc_postcode = c_post_code
                    WHERE
                        c_id = "' . (int)$c_id . '";';
        }
        else
        {
            // raw data sql
            $sql = 'SELECT
                        *,
                        DATE_FORMAT(c_date_of_birth, "%d/%m/%Y") AS c_date_of_birth
                    FROM
                        clients
                    WHERE
                        c_id = "' . (int)$c_id . '";';
        }

        return $this->db->query($sql)->row_array();
    }

    // returns client's journeys
    public function get_journeys($c_id)
    {
        $sql = 'SELECT
                    j_id,
                    j_type,
                    IF(j_date_of_referral IS NULL, "N/A", DATE_FORMAT(j_date_of_referral, "%d/%m/%Y")) AS j_date_of_referral_format,
                    IF(j_date_first_assessment IS NULL, "N/A", DATE_FORMAT(j_date_first_assessment, "%d/%m/%Y")) AS j_date_first_assessment_format,
                    IF(rc_name IS NULL, "N/A", rc_name) AS rc_name,
                    IF(j_status = 1, "Open", "Closed") as j_status
                FROM
                    journeys
                LEFT JOIN
                    recovery_coaches
                        ON rc_id = j_rc_id
                WHERE
                    j_c_id = "' . (int)$c_id . '"
                ';
            if($this->session->userdata('can_read_family') != 1)
                $sql .= ' AND j_type != "F" ';

            if($this->session->userdata('can_read_client') != 1)
                $sql .= ' AND j_type != "C" ';
        $sql .= '
                ORDER BY
                    j_date_of_referral DESC ';

        return $this->db->query($sql)->result_array();
    }

    // returns array of support groups
    public function get_support_groups($c_id)
    {
        $sql = 'SELECT
                    COUNT(spc_c_id) AS total,
                    sp_id,
                    CONCAT(sp_name, " - ", sp_default_day, " at ", DATE_FORMAT(sp_default_time, "%H:%i")) AS sp_group
                FROM
                    support_group_clients,
                    support_group_sessions,
                    support_groups
                WHERE
                    spc_c_id = "' . (int)$c_id . '"
                    AND sps_id = spc_sps_id
                    AND sp_id = sps_sp_id
                GROUP BY
                    sp_id ';

        return $this->db->query($sql)->result_array();
    }



    // searches clients using ajax request
    function search_clients_ajax()
    {
        // if no get values are present
        if(empty($_GET['c_id']) && empty($_GET['c_fname']) && empty($_GET['c_sname']) && empty($_GET['c_date_of_birth']) && empty($_GET['c_post_code']))
            return FALSE;

        $sql = 'SELECT
                    c_id,
                    CONCAT(c_fname, " ", c_sname) AS c_name,
                    c_post_code,
                    DATE_FORMAT(c_date_of_birth, "%d/%m/%Y") AS c_date_of_birth
                FROM
                    clients
                WHERE
                    1 = 1 ';

        // Clients, but NOT this one.
        if(@$_GET['not_c_id'])
            $sql .= ' AND c_id != ' . (int) $_GET['not_c_id'] . ' ';

        if(@$_GET['c_id'])
            $sql .= ' AND c_id = ' . (int) $_GET['c_id'] . ' ';

        if(@$_GET['c_fname'])
            $sql .= ' AND CONVERT(c_fname USING latin1) = "' . mysql_real_escape_string($_GET['c_fname']) . '" ';

        if(@$_GET['c_sname'])
            $sql .= ' AND CONVERT(c_sname USING latin1) = "' . mysql_real_escape_string($_GET['c_sname']) . '" ';

        if(@$_GET['c_date_of_birth'])
            $sql .= ' AND c_date_of_birth = "' . mysql_real_escape_string(parse_date($_GET['c_date_of_birth'])) . '" ';

        if(@$_GET['c_post_code'])
            $sql .= ' AND CONVERT(c_post_code USING latin1) = "' . mysql_real_escape_string($_GET['c_post_code']) . '" ';

        $sql .= ' LIMIT 15 ';

        return $this->db->query($sql)->result_array();
    }


    // finds the catchment area of any giving post code
    public function get_catchment_area($post_code)
    {
        // remove any white space from post code
        $post_code = str_replace(' ', '', $post_code);

        // list of posts codes and their associated catchment area
        $post_codes_by_catchment_area = array('NE8'  => 'Gateshead',
                                              'NE9'  => 'Gateshead',
                                              'NE10' => 'Gateshead',
                                              'NE11' => 'Gateshead',
                                              'DH2'  => 'Gateshead',
                                              'DH3'  => 'Gateshead',
                                              'NE31' => 'South Tyneside',
                                              'NE32' => 'South Tyneside',
                                              'NE33' => 'South Tyneside',
                                              'NE34' => 'South Tyneside',
                                              'NE35' => 'South Tyneside',
                                              'NE36' => 'Sunderland',
                                              'NE37' => 'Sunderland',
                                              'NE38' => 'Sunderland',
                                              'SR1'  => 'Sunderland',
                                              'SR2'  => 'Sunderland',
                                              'SR3'  => 'Sunderland',
                                              'SR4'  => 'Sunderland',
                                              'SR5'  => 'Sunderland',
                                              'SR6'  => 'Sunderland',
                                              'SR7'  => 'Sunderland',
                                              'SR8'  => 'Sunderland',
                                              'SR9'  => 'Sunderland',
                                              'DH4'  => 'Sunderland',
                                              'DH5'  => 'Sunderland');

        // loop over each post code
        foreach($post_codes_by_catchment_area as $pc => $ca)
        {
            // shorten the string length of the given post code to match that of the catchment area post code
            $post_code_substr = substr($post_code, 0, strlen($pc));

            // if the 2 post codes match, we have a winner!
            if($pc == $post_code_substr)
            {
                // set the catchment area and break the loop
                $catchment_area = $ca;
                break;
            }
        }

        // if a catchment area was never set, the given post code musn't be valid, assign other
        if( ! isset($catchment_area))
            $catchment_area = 'Other';

        // return our catchment area as a string
        return $catchment_area;
    }


    // FUNCTION get client info
    public function get_client_info($j_id)
    {
        $sql = 'SELECT
                    *,
                    DATE_FORMAT(ci_date_of_birth, "%d/%m/%Y") AS ci_date_of_birth,
                    IF(ci_gp_code IS NULL, NULL, CONCAT(gp_name, " (#", gp_code, ")")) AS ci_gp_code
                FROM
                    clients_info
                LEFT JOIN
                    gps
                        ON gp_code = ci_gp_code
                WHERE
                    ci_j_id = "' . (int)$j_id . '" ';

        $client = $this->db->query($sql)->row_array();

        // unserialize
        $client['ci_contact'] = unserialize($client['ci_contact']);
        $client['ci_next_of_kin_details'] = unserialize($client['ci_next_of_kin_details']);

        //get disabilities with this
        $sql = "SELECT
                    j2d_disability
                FROM
                    j2disability
                WHERE
                    j2d_j_id = '". (int)$j_id ."'
                ;";
        $disabilities = $this->db->query($sql)->result_array();
        $client['disabilities'] = array();
        foreach($disabilities as $d)
        {
            $client['disabilities'][ $d['j2d_disability'] ] = $this->config->config['disability_codes'][ $d['j2d_disability'] ];
        }

        return $client;
    }


    // FUNCTION update client info table and mirror this in client table
    public function update_client_info($j_id, $c_id)
    {
        // get catchment area using post code
        $catchment_area = $this->get_catchment_area($this->input->post('ci_post_code'));

        //what have we got to update with?
        $ci_data = array();
        $c_data = array();
        if($this->input->post('ci_fname') !== FALSE && trim($this->input->post('ci_fname')) != '')
        {
            $ci_data['ci_fname'] = $this->input->post('ci_fname');
            $c_data['c_fname'] = $this->input->post('ci_fname');
        }
        if($this->input->post('ci_sname') !== FALSE && trim($this->input->post('ci_sname')) != '')
        {
            $ci_data['ci_sname'] = $this->input->post('ci_sname');
            $c_data['c_sname'] = $this->input->post('ci_sname');
        }
        if($this->input->post('ci_gender') !== FALSE && trim($this->input->post('ci_gender')) != '')
        {
            $ci_data['ci_gender'] = $this->input->post('ci_gender');
            $c_data['c_gender'] = $this->input->post('ci_gender');
        }
        if($this->input->post('ci_date_of_birth') !== FALSE && trim($this->input->post('ci_date_of_birth')) != '')
        {
            $ci_data['ci_date_of_birth'] = $this->input->post('ci_date_of_birth');
            $c_data['c_date_of_birth'] = $this->input->post('ci_date_of_birth');
        }
        if($this->input->post('ci_address') !== FALSE && trim($this->input->post('ci_address')) != '')
        {
            $ci_data['ci_address'] = $this->input->post('ci_address');
            $c_data['c_address'] = $this->input->post('ci_address');
        }
        if($this->input->post('ci_post_code') !== FALSE && trim($this->input->post('ci_post_code')) != '')
        {
            $ci_data['ci_post_code'] = $this->input->post('ci_post_code');
            $c_data['c_post_code'] = $this->input->post('ci_post_code');
            $ci_data['ci_catchment_area'] = $catchment_area;
            $c_data['c_catchment_area'] = $catchment_area;
        }
        if($this->input->post('ci_authority_code') !== FALSE)
        {
            $ci_data['ci_authority_code'] = ($this->input->post('ci_authority_code') != "" ? $this->input->post('ci_authority_code') : NULL);
        }
        if($this->input->post('ci_authority_name') !== FALSE)
        {
            $ci_data['ci_authority_name'] = ($this->input->post('ci_authority_name') != "" ? $this->input->post('ci_authority_name') : NULL);
        }
        if($this->input->post('ci_tel_home') !== FALSE && trim($this->input->post('ci_tel_home')) != '')
        {
            $ci_data['ci_tel_home'] = ($this->input->post('ci_tel_home') != "" ? $this->input->post('ci_tel_home') : NULL);
            $c_data['c_tel_home'] = $ci_data['ci_tel_home'];
        }
        if($this->input->post('ci_tel_mob') !== FALSE && trim($this->input->post('ci_tel_mob')) != '')
        {
            $ci_data['ci_tel_mob'] = ($this->input->post('ci_tel_mob') != "" ? $this->input->post('ci_tel_mob') : NULL);
            $c_data['c_tel_mob'] = $ci_data['ci_tel_mob'];
        }
        if($this->input->post('ci_gp_code') !== FALSE && trim($this->input->post('ci_gp_code')) != '')
        {
            $ci_data['ci_gp_code'] = ($this->input->post('ci_gp_code') != "" ? $this->input->post('ci_gp_code') : NULL);
        }
        if($this->input->post('ci_gp_name') !== FALSE && trim($this->input->post('ci_gp_name')) != '')
        {
            $ci_data['ci_gp_name'] =  ($this->input->post('ci_gp_name') != "" ? $this->input->post('ci_gp_name') : NULL);
        }
        if($this->input->post('ci_ethnicity') !== FALSE && trim($this->input->post('ci_ethnicity')) != '')
        {
            $ci_data['ci_ethnicity'] = ($this->input->post('ci_ethnicity') != "" ? $this->input->post('ci_ethnicity') : NULL);
        }
        if($this->input->post('ci_nationality') !== FALSE && trim($this->input->post('ci_nationality')) != '')
        {
            $ci_data['ci_nationality'] = ($this->input->post('ci_nationality') != "" ? $this->input->post('ci_nationality') : NULL);
        }
        if($this->input->post('ci_religion') !== FALSE && trim($this->input->post('ci_religion')) != '')
        {
            $ci_data['ci_religion'] = ($this->input->post('ci_religion') != "" ? $this->input->post('ci_religion') : NULL);
        }
        if($this->input->post('ci_pregnant') !== FALSE && trim($this->input->post('ci_pregnant')) != '')
        {
            ($this->input->post('ci_pregnant') ? $this->input->post('ci_pregnant') : NULL);
        }
        if($this->input->post('ci_caf_completed') !== FALSE && trim($this->input->post('ci_caf_completed')) != '')
        {
            $ci_data['ci_caf_completed'] = ($this->input->post('ci_caf_completed') ? $this->input->post('ci_caf_completed') : NULL);
        }
        if($this->input->post('ci_relationship_status') !== FALSE && trim($this->input->post('ci_relationship_status')) != '')
        {
            $ci_data['ci_relationship_status'] = ($this->input->post('ci_relationship_status') ? $this->input->post('ci_relationship_status') : NULL);
        }
        if($this->input->post('ci_sexuality') !== FALSE && trim($this->input->post('ci_sexuality')) != '')
        {
            $ci_data['ci_sexuality'] = ($this->input->post('ci_sexuality') ? $this->input->post('ci_sexuality') : NULL);
        }
        if($this->input->post('ci_mental_health_issues') !== FALSE && trim($this->input->post('ci_mental_health_issues')) != '')
        {
            $ci_data['ci_mental_health_issues'] = ($this->input->post('ci_mental_health_issues') ? $this->input->post('ci_mental_health_issues') : NULL);
        }
        if($this->input->post('ci_learning_difficulties') !== FALSE && trim($this->input->post('ci_learning_difficulties')) != '')
        {
            $ci_data['ci_learning_difficulties'] = ($this->input->post('ci_learning_difficulties') ? $this->input->post('ci_learning_difficulties') : NULL);
        }
        if($this->input->post('ci_consents_to_ndtms') !== FALSE && trim($this->input->post('ci_consents_to_ndtms')) != '')
        {
            $ci_data['ci_consents_to_ndtms'] = ($this->input->post('ci_consents_to_ndtms') ? $this->input->post('ci_consents_to_ndtms') : NULL);
        }
        if($this->input->post('ci_parental_status') !== FALSE && trim($this->input->post('ci_parental_status')) != '')
        {
            $ci_data['ci_parental_status'] = ($this->input->post('ci_parental_status') ? $this->input->post('ci_parental_status') : NULL);
        }
        if($this->input->post('ci_access_to_children') !== FALSE && trim($this->input->post('ci_access_to_children')) != '')
        {
            $ci_data['ci_access_to_children'] = ($this->input->post('ci_access_to_children') ? $this->input->post('ci_access_to_children') : NULL);
        }
        if($this->input->post('ci_no_of_children') !== FALSE && trim($this->input->post('ci_no_of_children')) != '')
        {
            $ci_data['ci_no_of_children'] = ($this->input->post('ci_no_of_children') ? $this->input->post('ci_no_of_children') : NULL);
        }
        if($this->input->post('ci_accommodation_need') !== FALSE && trim($this->input->post('ci_accommodation_need')) != '')
        {
            $ci_data['ci_accommodation_need'] = ($this->input->post('ci_accommodation_need') ? $this->input->post('ci_accommodation_need') : NULL);
        }
        if($this->input->post('ci_accommodation_status') !== FALSE && trim($this->input->post('ci_accommodation_status')) != '')
        {
            $ci_data['ci_accommodation_status'] = ($this->input->post('ci_accommodation_status') ? $this->input->post('ci_accommodation_status') : NULL);
        }
        if($this->input->post('ci_employment_status') !== FALSE && trim($this->input->post('ci_employment_status')) != '')
        {
            $ci_data['ci_employment_status'] = ($this->input->post('ci_employment_status') ? $this->input->post('ci_employment_status') : NULL);
        }
        if($this->input->post('ci_smoker') !== FALSE && trim($this->input->post('ci_smoker')) != '')
        {
            $ci_data['ci_smoker'] = ($this->input->post('ci_smoker') ? $this->input->post('ci_smoker') : NULL);
        }
        if($this->input->post('ci_contact') !== FALSE && trim($this->input->post('ci_contact')) != '')
        {
            $ci_data['ci_contact'] = serialize($this->input->post('ci_contact'));
        }
        if($this->input->post('ci_next_of_kin_details') !== FALSE && trim($this->input->post('ci_next_of_kin_details')) != '')
        {
            $ci_data['ci_next_of_kin_details'] = serialize($this->input->post('ci_next_of_kin_details'));
        }
        if($this->input->post('ci_additional_information') !== FALSE && trim($this->input->post('ci_additional_information')) != '')
        {
            $ci_data['ci_additional_information'] = ($this->input->post('ci_additional_information') != "" ? $this->input->post('ci_additional_information') : NULL);
        }
        if($this->input->post('ci_pregnant') !== FALSE && trim($this->input->post('ci_pregnant')) != '')
        {
            $ci_data['ci_pregnant'] = $this->input->post('ci_pregnant');
        }
        if($this->input->post('ci_interpreter_required') !== FALSE && trim($this->input->post('ci_interpreter_required')) != '')
        {
            $ci_data['ci_interpreter_required'] = $this->input->post('ci_interpreter_required');
        }
        if($this->input->post('ci_preferred_contact_method') !== FALSE && trim($this->input->post('ci_preferred_contact_method')) != '')
        {
            $ci_data['ci_preferred_contact_method'] = $this->input->post('ci_preferred_contact_method');
        }
        if($this->input->post('ci_preferred_contact_time') !== FALSE && trim($this->input->post('ci_preferred_contact_time')) != '')
        {
            $ci_data['ci_preferred_contact_time'] = $this->input->post('ci_preferred_contact_time');
        }
        if($this->input->post('ci_staff_can_leave_message') !== FALSE && trim($this->input->post('ci_staff_can_leave_message')) != '')
        {
            $ci_data['ci_staff_can_leave_message'] = $this->input->post('ci_staff_can_leave_message');
        }
        if($this->input->post('ci_staff_can_identify_themselves') !== FALSE && trim($this->input->post('ci_staff_can_identify_themselves')) != '')
        {
            $ci_data['ci_staff_can_identify_themselves'] = $this->input->post('ci_staff_can_identify_themselves');
        }
        if($this->input->post('ci_preferred_appointment_time') !== FALSE && trim($this->input->post('ci_preferred_appointment_time')) != '')
        {
            $ci_data['ci_preferred_appointment_time'] = $this->input->post('ci_preferred_appointment_time');
        }
        if($this->input->post('ci_escape_write_to_you') !== FALSE && trim($this->input->post('ci_escape_write_to_you')) != '')
        {
            $ci_data['ci_escape_write_to_you'] = $this->input->post('ci_escape_write_to_you');
        }
        if($this->input->post('ci_previous_offender') !== FALSE && trim($this->input->post('ci_previous_offender')) != '')
        {
            $ci_data['ci_previous_offender'] = $this->input->post('ci_previous_offender');
        }
        if($this->input->post('ci_current_offender') !== FALSE && trim($this->input->post('ci_current_offender')) != '')
        {
            $ci_data['ci_current_offender'] = $this->input->post('ci_current_offender');
        }
        if($this->input->post('ci_partner_pregnant') !== FALSE && trim($this->input->post('ci_partner_pregnant')) != '')
        {
            $ci_data['ci_partner_pregnant'] = $this->input->post('ci_partner_pregnant');
        }
        if($this->input->post('ci_childrens_services') !== FALSE && trim($this->input->post('ci_childrens_services')) != '')
        {
            $ci_data['ci_childrens_services'] = $this->input->post('ci_childrens_services');
        }
        if($this->input->post('ci_outcome') !== FALSE && trim($this->input->post('ci_outcome')) != '')
        {
            $ci_data['ci_outcome'] = $this->input->post('ci_outcome');
        }
        if($this->input->post('ci_kinship_carer') !== FALSE && trim($this->input->post('ci_kinship_carer')) != '')
        {
            $ci_data['ci_kinship_carer'] = (int)$this->input->post('ci_kinship_carer');
        }
        if($this->input->post('ci_escape_are_top_responsible') !== FALSE && trim($this->input->post('ci_escape_are_top_responsible')) != '')
        {
            $ci_data['ci_escape_are_top_responsible'] = (int)$this->input->post('ci_escape_are_top_responsible');
        }
        if($this->input->post('ci_pbr_client') !== FALSE && trim($this->input->post('ci_pbr_client')) != '')
        {
            $ci_data['ci_pbr_client'] = (int)$this->input->post('ci_pbr_client');
        }
        if($this->input->post('ci_lasar_complexity') !== FALSE && trim($this->input->post('ci_lasar_complexity')) != '')
        {
            $ci_data['ci_lasar_complexity'] = ($this->input->post('ci_lasar_complexity') != "" ? $this->input->post('ci_lasar_complexity') : NULL);
        }
        if($this->input->post('ci_external_client_id') !== FALSE && trim($this->input->post('ci_external_client_id')) != '')
        {
            $ci_data['ci_external_client_id'] = ($this->input->post('ci_external_client_id') != "" ? $this->input->post('ci_external_client_id') : NULL);
        }
        if($this->input->post('ci_consent_signed') !== FALSE && trim($this->input->post('ci_consent_signed')) != '')
        {
            $ci_data['ci_consent_signed'] = (int)$this->input->post('ci_consent_signed');
        }
        if($this->input->post('ci_ndtms_consent') !== FALSE && trim($this->input->post('ci_ndtms_consent')) != '')
        {
            $ci_data['ci_ndtms_consent'] = (int)$this->input->post('ci_ndtms_consent');
        }
        if($this->input->post('ci_nta_consent') !== FALSE && trim($this->input->post('ci_nta_consent')) != '')
        {
            $ci_data['ci_nta_consent'] = (int)$this->input->post('ci_nta_consent');
        }
        if($this->input->post('ci_csop_consent') !== FALSE && trim($this->input->post('ci_csop_consent')) != '')
        {
            $ci_data['ci_csop_consent'] = (int)$this->input->post('ci_csop_consent');
        }
        if($this->input->post('ci_photography_consent') !== FALSE && trim($this->input->post('ci_photography_consent')) != '')
        {
            $ci_data['ci_photography_consent'] = (int)$this->input->post('ci_photography_consent');
        }
        if($this->input->post('ci_previous_id') !== FALSE && trim($this->input->post('ci_previous_id')) != '')
        {
            $ci_data['ci_previous_id'] = (int)$this->input->post('ci_previous_id');
        }
        if($this->input->post('ji_flagged_as_risk') !== FALSE && trim($this->input->post('ji_flagged_as_risk')) != '')
        {
            $c_data['c_is_risk'] = (int)$this->input->post('ji_flagged_as_risk');
        }

        // update client info table
        if(!empty($ci_data))
        {
            $sql = $this->db->update_string('clients_info', $ci_data, 'ci_j_id = "' . (int)$j_id . '";');
            $this->db->query($sql);
        }

        // update client table
        if(!empty($c_data))
        {
            $sql = $this->db->update_string('clients', $c_data, 'c_id = "' . (int)$c_id . '";');
            $this->db->query($sql);
        }

        //update client disabilities
        if($this->input->post('d_id') !== FALSE)
        {
            //remove all existing client disabilities
            $sql = "DELETE FROM j2disability WHERE j2d_j_id = '". (int)$j_id ."';";
            $this->db->query($sql);

            //add all the ones listed
            foreach($this->input->post('d_id') as $i => $disability)
            {
                $j2d_data[] = "('". (int)$j_id ."', ". $this->db->escape($disability) .")";
            }
            $sql = "INSERT
                        INTO j2disability
                        (j2d_j_id, j2d_disability)
                    VALUES
                        ". implode(', ', $j2d_data) ."
                    ;";
            $this->db->query($sql);
            //$ci_data['ci_disabilities'] = ($this->input->post('ci_disabilities') != '' ? $this->input->post('ci_disabilities') : NULL);
        }

        $this->session->set_flashdata('action', 'Client information updated');
    }




    public function delete_client($client)
    {
        $tables = array(
            'clients' => 'c_id',
            'client_risks' => 'cr_c_id',
            'client_risk_summary' => 'crs_c_id',
            'support_group_clients' => 'spc_c_id',
            'family_clients' => 'fc_c_id',
        );

        $err = FALSE;

        // Loop through all the tables and remove entries relating to the journey
        foreach ($tables as $table => $key)
        {
            $sql = 'DELETE FROM `' . $table . '` WHERE `' . $key . '` = ' . $client['c_id'];
            if ( ! $this->db->query($sql))
            {
                $err = TRUE;
                break;
            }
        }

        if ($err)
        {
            return FALSE;
        }
        else
        {
            // set notification
            $this->session->set_flashdata('action', $client['c_fname'] . ' ' . $client['c_sname'] . ' successfully deleted');

            // set log
            $this->log_model->set('Client #' . $c_id . ' (' . $client['c_fname'] . ' ' . $client['c_sname'] . ') deleted');

            // successfully delete client - return true
            return TRUE;
        }
    }




    // add/update a client
    public function set_client($client)
    {
        // if client exists
        if($client == true)
        {
            // update client information
            $sql = 'UPDATE
                        clients
                    SET
                        c_fname = ?,
                        c_sname = ?,
                        c_gender = ?,
                        c_date_of_birth = ?,
                        c_address = ?,
                        c_post_code = ?,
                        c_catchment_area = ?,
                        c_tel_home = ?,
                        c_tel_mob = ?
                    WHERE
                        c_id = "' . (int)$client['c_id'] . '";';

            // set notification
            $this->session->set_flashdata('action', 'Client updated');
        }
        else
        {
            // insert client information
            $sql = 'INSERT INTO
                        clients
                    SET
                        c_fname = ?,
                        c_sname = ?,
                        c_gender = ?,
                        c_date_of_birth = ?,
                        c_address = ?,
                        c_post_code = ?,
                        c_catchment_area = ?,
                        c_tel_home = ?,
                        c_tel_mob = ?;';

            // set notification
            $this->session->set_flashdata('action', 'Client updated');
        }

        // remove spaces from post code
        $post_code = str_replace(' ', '', $this->input->post('c_post_code'));

        // get catchemnt area
        $catchment_area = $this->get_catchment_area($post_code);

        // commit query
        $this->db->query($sql, array($this->input->post('c_fname'),
                                     $this->input->post('c_sname'),
                                     $this->input->post('c_gender'),
                                     $this->input->post('c_date_of_birth'),
                                     ($this->input->post('c_address') != '' ? $this->input->post('c_address') : NULL),
                                     $post_code,
                                     $catchment_area,
                                     ($this->input->post('c_tel_home') ? $this->input->post('c_tel_home') : NULL),
                                     ($this->input->post('c_tel_mob') ? $this->input->post('c_tel_mob') : NULL)));

        // return client id
        return ($client ? $client['c_id'] : $this->db->insert_id());
    }




    /**
     * Get all family clients linked to given client
     */
    public function get_family_clients($c_id)
    {
        $sql = 'SELECT
                    *
                FROM

                    (SELECT
                        *,
                        DATE_FORMAT(c_date_of_birth, "%d/%m/%Y") AS c_date_of_birth_format,
                        "primary" AS type
                    FROM
                        family_clients
                    LEFT JOIN
                        clients
                            ON fc_c_id = c_id
                    WHERE
                        fc_j_c_id = ?

                    UNION

                    SELECT
                        *,
                        DATE_FORMAT(c_date_of_birth, "%d/%m/%Y") AS c_date_of_birth_format,
                        "secondary" AS type
                    FROM
                        family_clients
                    LEFT JOIN
                        clients
                            ON fc_j_c_id = c_id
                    WHERE
                        fc_c_id = ?
                    ) AS tmp

                GROUP BY c_id';

        return $this->db->query($sql, array($c_id, $c_id))->result_array();
    }

}
