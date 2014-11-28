<?php 

class Ndtms_old
{
	protected $_CI;
	protected $_validation_errors = array();
	
	public function __construct()
	{
		// get instance of ci
		$this->_CI =& get_instance();
	}
	
	
	public function check_is_valid($j_id)
	{
		// set validation errors to blank array
		$this->_validation_errors = array();
		
		// if journey exists	
		if($journey = $this->_get_journey($j_id))
		{
			// if journey validates
			if($this->_validate_journey($journey))
			{
				// set journey as ntdms valid
				$this->_set_j_ndtms_valid($j_id, 1);
				
				// return true
				return true;
			}
			else
			{
				// set journey as not ntdms valid
				$this->_set_j_ndtms_valid($j_id, 2);
												
				// return false
				return false;
			}
		}
		else
		{
			// return false - journey not found
			return false;
		}
	}
	
	public function get_validation_errors($j_id)
	{
		// if journey exists	
		if($journey = $this->_get_journey($j_id))
		{
			// if validation fails
			if($this->_validate_journey($journey) == false)
			{
				// return validation errors array
				return $this->_validation_errors;
			}
			else
			{
				// return false - journey is valid
				return false;
			}
		}
		else
		{
			// return false - journey not found
			return false;
		}
	}
	
	// validates journey against NDTMS validation rules
	protected function _validate_journey($journey)
	{
		// if forename is missing
		if($journey['ci_fname'] === NULL)
		{
			$this->_validation_errors[] = 'Client\'s forename is missing.';
		}
		
		// if surname is missing
		if($journey['ci_sname'] === NULL)
		{
			$this->_validation_errors[] = 'Client\'s surname is missing.';
		}
		
		// if date of birth is missing
		if($journey['ci_date_of_birth'] === NULL)
		{
			$this->_validation_errors[] = 'Client\'s date of birth is missing.';
		}
		
		if($journey['ci_age'] < 18)
		{
			$this->_validation_errors[] = 'Date of birth invalid. Client must be age 18 or over.';
		}
		
		// if year of birth is less than 1902
		if(date('Y', strtotime($journey['ci_date_of_birth'])) < 1903)
		{
			$this->_validation_errors[] = 'Year of birth must be greater than 1902.';
		}
		
		// if date of birth is less than referral date
		if(strtotime($journey['ci_date_of_birth']) > strtotime($journey['j_date_of_referral']))
		{
			$this->_validation_errors[] = 'Date of birth must be before referral date.';
		}
		
		// if gender is missing
		if($journey['ci_gender'] === NULL)
		{
			$this->_validation_errors[] = 'Client\'s gender is missing.';
		}
		
		// if referral date is missing
		if($journey['j_date_of_referral'] === NULL)
		{
			$this->_validation_errors[] = 'The referral date is missing.';
		}
		
		// if consent for ndtms is missing
		if($journey['ci_consents_to_ndtms'] === NULL)
		{
			$this->_validation_errors[] = 'You must speficy whether the client consents to data sharing.';
		}
		
		// if consent for ndtms is missing
		if($journey['jd_substance_1'] === NULL)
		{
			$this->_validation_errors[] = 'You must specify problem substance 1.';
		}
		
		// if date of triage is missing
		if($journey['j_date_of_triage'] === NULL)
		{
			$this->_validation_errors[] = 'You must record the client\'s triage appointment and they must have attended.';
		}
		else
		{
			// if date of triage is less than referral date
			if(strtotime($journey['j_date_of_triage']) < strtotime($journey['j_date_of_referral']))
			{
				$this->_validation_errors[] = 'Triage date must be greater than referral date.';
			}
		}
		
		// if any validation errors exist
		if($this->_validation_errors)
		{
			// validation failed
			return false;
		}
		else
		{
			// validation passed
			return true;
		}
	}
	
	// set value of ndtms valid
	protected function _set_j_ndtms_valid($j_id, $is_valid)
	{
		// update journeys
		$sql = 'UPDATE
					journeys
				SET
					j_ndtms_valid = "' . (int)$is_valid . '"
				WHERE
					j_id = "' . (int)$j_id . '";';
			
		// commit query		
		$this->_CI->db->query($sql);
	}
	
	// returns a single journey with all fields needed to validate
	protected function _get_journey($j_id)
	{
		// select journey
		$sql = 'SELECT
					ci_fname,
					ci_sname,
					ci_date_of_birth,
					DATE_FORMAT(CURDATE(), "%Y") - DATE_FORMAT(ci_date_of_birth, "%Y") - (DATE_FORMAT(CURDATE(), "00-%m-%d") < DATE_FORMAT(ci_date_of_birth, "00-%m-%d")) AS ci_age,
					ci_gender,
					j_date_of_referral,
					ci_consents_to_ndtms,
					jd_substance_1,
					j_date_of_triage
				FROM
					journeys
				LEFT JOIN
					clients_info
						ON ci_j_id = j_id
				LEFT JOIN
					journey_drugs
						ON jd_j_id = j_id
				WHERE
					j_id = "' . (int)$j_id . '";';
			
		// return journey		
		return $this->_CI->db->query($sql)->row_array();
	}
	
	
}