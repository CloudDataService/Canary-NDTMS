<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ndtms_k extends CI_Driver {
	
	
	private $_catchment_areas;
	
	
	
	public function init()
	{
		// Load some configs that need to be referenced later
		$this->CI->load->config('datasets');
		$this->_catchment_areas = $this->CI->config->item('catchment_area_codes');
	}
	
	
	
	
	/**
	 * Get the list of fields that should have values if consent is not given. All others will not be set
	 *
	 * @return array 		Array of exclusive CSV headers that are allowed to keep their values when consent is not given.
	 */
	public function get_whitelist()
	{
		return array('AGNCY', 'CLIENTID', 'CONSENT');
	}
	
	
	
	/** 
	 * Determine if the whitelist should be used for a given row
	 *
	 * @param array $row		DB row array of data
	 * @return bool		True/False for if the whitelist should be used for the supplied row
	 * @author CR
	 */
	public function use_whitelist($row = array())
	{
		// If consent is No, then the whitelist should be used to restrict the data
		return (element('CONSENT', $row, 'Y') === 'N');
	}
	
	
	
	
	/**
	 * Get the schama for this ndtms version. Returns an array.
	 *
	 * The returned array's keys are the NDTMS CSV column headers.
	 *
	 * The value should be one of three possible things that specifies what value should be used, and obtained from where.
	 * - NULL. The resulting CSV value will be "NA" or equivalent.
	 * - array key name from the data array for each row that is processed.
	 * - name of a function in this class to be called that takes the row data array as the parameter and returns the desired value.
	 *
	 * @return array 		2D array of CSV headers => data source 
	 */
	public function get_schema()
	{
		return array(
			'FINITIAL' => 'FINITIAL',		// client's first name initial
			'SINITIAL' => 'SINITIAL',		// client's surname initial
			'DOB' => 'DOB',		// date of birth
			'SEX' => 'SEX',		// gender
			'ETHNIC' => 'ETHNIC',		// ethnicity
			'NATION' => 'NATION',		// nationality
			'REFLD' => 'REFLD',		// referral date
			'AGNCY' => 'agency',		// agency code
			'CLIENT' => 'CLIENTID',		// client reference
			'CLIENTID' => 'CLIENTID',		// client id
			'EPISODID' => 'EPISODID',		// episode id
			'CONSENT' => 'CONSENT',		// consent for ndtms
			'PREVTR' => 'PREVTR',		// previously treated
			'PC' => 'PC',		// postcode
			'ACCMNEED' => 'ACCMNEED',		// accommodation naed
			'CLALOCTN' => NULL,		// location of cla
			'YPLAC' => NULL,		// young person looking after child
			'PRNTSTAT' => 'PRNTSTAT',		// parental status
			'YPSLEAD' => NULL,
			'YPSMHS' => NULL,
			'YPSYOT' => NULL,
			'YPSSEXEX' => NULL,
			'YPSSLFHM' => NULL,
			'YPSUNSDR' => NULL,
			'YPSOFFND' => NULL,
			'YPSESTAT' => NULL,
			'YPSGP' => NULL,
			'YPSCAF' => NULL,
			'YPSLNDIF' => NULL,
			'YPSUSS' => NULL,
			'YPSFOU' => NULL,
			'YPTSRP' => NULL,
			'DAT' => 'catchment_dat',		// DAT of residence
			'PCT' => 'catchment_pct',		// PCT of residence
			'LA' => 'catchment_la',		// Local authority of residence
			'GPPTCE' => 'GPPTCE',		// practice code of GP
			'DRUG1' => 'DRUG1',		// probelm substance 1
			'DRUG1AGE' => 'DRUG1AGE',		// age of first use of problem substance 1
			'ROUTE' => 'ROUTE',		// route of admin of problem substance 1
			'DRUG2' => 'DRUG2',		// problem substance 2
			'DRUG3' => 'DRUG3',		// problem substance 3
			'RFLS' => 'RFLS',		// referral source
			'TRIAGED' => 'TRIAGED',		// traige date
			'CPLANDT' => 'j_date_first_assessment',		// care plan started date
			'INJSTAT' => 'INJSTAT',		// injecting status
			'CHILDWTH' => 'CHILDWTH',		// children
			'PREGNANT' => 'PREGNANT',		// pregnant
			'ALCDDAYS' => 'ALCDDAYS',		// total number of drinking days in past 28 days
			'ALCUNITS' => 'ALCUNITS',		// total alcohol units
			'DUALDIAG' => 'DUALDIAG',		// dual diagnsosis
			'HEPCTSTD' => 'HEPCTSTD',		// hep c - latest test date
			'HEPCSTAT' => 'HEPCSTAT',		// hep c - intervention status
			'HEPCTD' => 'hep_c_tested',		// hep c - tested
			'HEPBVAC' => 'HEPBVAC',		// hep b - vaccination count
			'HEPBSTAT' => 'HEPBSTAT',		// hep b - intervention status
			'HLCASSDT' => NULL,		// drug treatment health care assessment date
			'TOPCC' => 'topcc', // TOP care coordination
			'DISD' => 'DISD',		// discharge date
			'DISRSN' => 'DISRSN',		// discharge reason
			'DISDEST' => NULL,		// discharge destination
			'YPELEAD' => NULL,
			'YPEMHS' => NULL,
			'YPEYOT' => NULL,
			'YPESEXEX' => NULL,
			'YPESLFHM' => NULL,
			'YPEUNSDR' => NULL,
			'YPEOFFND' => NULL,
			'YPECAFE' => NULL,
			'YPEFOU' => NULL,
			'YPELNDIF' => NULL,
			'YPEUSS' => NULL,
			'YPESTD' => NULL,
			'YPEGP' => NULL,
			'YPECAREP' => NULL,
			'MODAL' => 'mod_modal',		// treatment modality
			'REFMODDT' => 'mod_refmod',		// date referred to modality
			'MODSET' => NULL,
			'MODID' => 'mod_modid',		// modality ID
			'FAOMODDT' => 'mod_fao',		// date of first appointment offered for modality
			'MODST' => 'mod_start',		// modality start date
			'MODEND' => 'mod_end',		// modality end date
			'MODEXIT' => 'mod_exit',		// modality exit status
			'SUBMODDT' => NULL,
			'SUBMID' => NULL,
			
			'PHSTBL' => NULL,		// Clients prescribing intention is Assessment & Stabilisation
			'PHWTH' => NULL,		// Clients prescribing intention is Withdrawal
			'PHMAIN' => NULL,		// Clients prescribing intention is Maintenance
			'PHRELPR' => NULL,		// Clients prescribing intention is Relapse Prevention
			'PSYMOTI' => NULL,		// Client involved with Motivational Interventions 
			'PSYCNMG' => NULL,		// Client involved with Contingency Management (Drug Focused) 
			'PSYFSNI' => NULL,		// Client involved with Family and Social Network Interventions
			'PSYCGBH' => NULL,		// Client involved with cognitive and behavioural interventions (Substance Misuse Specific)
			'PSYMNTH' => NULL,		// Evidence-based psychological intervention for coexisting mental health problems
			'PSYDNMC' => NULL,		// Client involved with Psychodynamic Therapy
			'PSYSTP' => NULL,		// Client involved with 12-step work
			'PSYCOUN' => NULL,		// Client involved in Counselling – BACP Accredited
			'PSYOTHR' => NULL,		// Client involved in other treatment sub interventions related to Psychosocial
			'RECPEER' => NULL,		// Client provided with Peer support involvement
			'RECMAID' => NULL,		// Client provided with Facilitated access to mutual aid
			'RECFMSP' => NULL,		// Client provided with Family Support
			'RECPRNT' => NULL,		// Client provided with Parenting Support
			'RECHSE' => NULL,		// Client provided with Housing support
			'RECEMP' => NULL,		// Client provided with Employment support
			'RECEDUT' => NULL,		// Client provided with Education and Training support
			'RECWPRJ' => NULL,		// Client provided with supported work projects
			'RECCHKP' => NULL,		// Client provided with recovery check ups
			'RECRLPP' => NULL,		// Evidence-based psychosocial interventions to support relapse prevention
			'RECCMPT' => NULL,		// Client provided with complementary therapies
			'RECGNH' => NULL,		// Client provided with Mental health Interventions
			'RECOTH' => NULL,		// Client provided with any other recovery support elements
			
			'TITDATE' => 'tit_date',		// ??? Time in Treatment Assessment Date
			'TITREAT' => 'tit_time',		// ??? Time in Treatment
			'TITID' => 'tit_id',		// ??? Time in Treatment ID
			
			'TOPDATE' => 'TOPDATE',		// TOP date
			'TOPID' => 'TOPID',		// TOP id
			
			'TRSTAGE' => NULL,		// treatment stage
			'ALCUSE' => 'ALCUSE',		// alcohol use
			'OPIUSE' => NULL,		// opitate use
			'CRAUSE' => NULL,		// crack use
			'COCAUSE' => NULL,		// cocaine use
			'AMPHUSE' => NULL,		// amphetamine use
			'CANNUSE' => NULL,		// cannabis use
			'OTDRGUSE' => NULL,		// other drug use
			'IVDRGUSE' => NULL,		// IV drug use
			'SHARING' => NULL,		// sharing
			'SHOTHEFT' => NULL,		// shop theft
			'DRGSELL' => NULL,		// drug selling
			'OTHTHEFT' => NULL,		// other theft
			'ASSAULT' => NULL,		// assault/violence
			'PSYHSTAT' => NULL,		// psychological health status
			'PWORK' => NULL,		// paid work
			'EDUCAT' => NULL,		// education
			'ACUTHPBM' => NULL,		// acute housing problem
			'HRISK' => NULL,		// housing risk
			'PHSTAT' => NULL,		// physical health status
			'QUALLIFE' => NULL,		// quality of life	
			'INJECT' => 'INJECT',		// Injected in last 28 days
			'SHARE' => NULL,		// Ever shared?
			'PREVHEPB' => 'PREVHEPB',		// Previous hep b infected?
			'HEPCPOS' => 'HEPCPOS',		// Hep c positive?
			'REFHEPGY' => NULL,		// Referred for Hepatology?
			'SEXUAL' => 'SEXUAL',		// Sexuality
			'EMPSTAT' => 'EMPSTAT',		// Employment status
			'OPRAGNCY' => NULL,		// Local Agency Details (Modality data item)
		);
	}
	
	
	
	
	/**
	 * The value that should be used for null or empty values
	 */
	public function get_null_value()
	{
		return 'NA';
	}
	
	
	
	
	// ========================================================================
	// Functions for dealing with certain fields
	// ========================================================================
	
	
	
	
	
	
	/**
	 * Agency
	 */
	public function field_agency($data = array())
	{
		if (element('CONSENT', $data, 'Y') === 'N')
		{
			// No consent
			$catchment_area = element('ci_catchment_area', $data);
			if ( ! $catchment_area) return NULL;
			
			return element('agency_code', $this->_catchment_areas[$catchment_area], NULL);
		}
		else
		{
			return element('agency_code', $this->_catchment_areas['Sunderland'], NULL);
		}
	}
	
	
	/**
	 * Catchment: DAT code
	 */
	public function field_catchment_dat($data = array())
	{
		return element('dat_code', $this->_catchment_areas[$data['ci_catchment_area']], NULL);
	}
	
	
	/**
	 * Catchment: PCT
	 */
	public function field_catchment_pct($data = array())
	{
		return element('pct_code', $this->_catchment_areas[$data['ci_catchment_area']], NULL);
	}
	
	
	/**
	 * Catchment: local authority
	 */
	public function field_catchment_la($data = array())
	{
		return element('local_authority_code', $this->_catchment_areas[$data['ci_catchment_area']], NULL);
	}
	
	
	/**
	 * HEP C Tested
	 *
	 * 99 = Not asked
	 */
	public function field_hep_c_tested($data = array())
	{
		return '99';
	}
	
	
	public function field_topcc($data = array())
	{
		return 'N';
	}
	
	
	/**
	 * Modality - only include modality information if there is an assessment has been done
	 */
	public function field_mod_modal($data = array())
	{
		return ($data['j_date_first_assessment'] ? $data['MODAL'] : NULL);
	}
	
	
	/**
	 * Modality - only include modality information if there is an assessment has been done
	 */
	public function field_mod_refmod($data = array())
	{
		return ($data['j_date_first_assessment'] ? $data['j_date_first_assessment'] : NULL);
	}
	
	
	/**
	 * Modality - only include modality information if there is an assessment has been done
	 */
	public function field_mod_modid($data = array())
	{
		return ($data['j_date_first_assessment'] ? '1' : NULL);
	}
	
	
	/**
	 * Modality - only include modality information if there is an assessment has been done
	 */
	public function field_mod_fao($data = array())
	{
		return ($data['j_date_first_assessment'] ? $data['FAOMODDT'] : NULL);
	}
	
	
	/**
	 * Modality - only include modality information if there is an assessment has been done
	 */
	public function field_mod_start($data = array())
	{
		return ($data['j_date_first_assessment'] ? $data['j_date_first_assessment'] : NULL);
	}
	
	
	/**
	 * Modality - only include modality information if there is an assessment has been done
	 */
	public function field_mod_end($data = array())
	{
		return ($data['j_date_first_assessment'] && $data['MODEXIT'] !== NULL ? $data['j_date_last_assessment'] : NULL);
	}
	
	
	/**
	 * Modality - only include modality information if there is an assessment has been done
	 */
	public function field_mod_exit($data = array())
	{
		return ($data['j_date_first_assessment'] && $data['MODEXIT'] !== NULL ? $data['MODEXIT'] : NULL);
	}
	
	
	/**
	 * Time in Treatment - date (TITDATE)
	 *
	 * Use Mod Start Date of last modality entered.
	 */
	public function field_tit_date($data = array())
	{
		return ($data['j_date_first_assessment'] ? $data['j_date_first_assessment'] : NULL); 
	}
	
	
	/**
	 * Time in Treatment - time (TITREAT)
	 *
	 * Always "1" (14 hours or less).
	 */
	public function field_tit_time($data = array())
	{
		return '1';
	}
	
	
	/**
	 * Time in Treatment ID - id (TITID)
	 *
	 * Generate incremental number based on case ID and the modality ID entered.
	 */
	public function field_tit_id($data = array())
	{
		return $data['EPISODID'] . $data['MODID_COUNT'];
	}
	
	
	
	
	// ========================================================================
	// Validation
	// ========================================================================
	
	
	
	
	/**
	 * Get journey for use in validation functions
	 *
	 * @param int $j_id		ID of journey to get
	 * @return array 		DB row array of journey data
	 * @author CR
	 */
	private function get_journey($j_id = 0)
	{
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
					j_id = ?
				LIMIT 1';
		
		return $this->CI->db->query($sql, array($j_id))->row_array();
	}
	
	
	
	
	/**
	 * Validate a given journey
	 *
	 * @param array $journey		Journey array
	 * @return array 		Array, keys 'valid' (bool) and 'errors' (array)
	 * @author CR
	 */
	public function validate_journey($j_id)
	{
		$errors = array();
		$journey = $this->get_journey($j_id);
		
		if ( ! $journey)
		{
			$errors[] = 'Journey not found.';
		}
		
		
		// Empty client forename
		if (element('ci_fname', $journey, NULL) === NULL)
		{
			$errors[] = 'Client forename is missing.';
		}
		
		// Empty client surname
		if (element('ci_sname', $journey, NULL) === NULL)
		{
			$errors[] = 'Client surname is missing.';
		}
		
		// Empty date of birth
		if (element('ci_date_of_birth', $journey, NULL) === NULL)
		{
			$errors[] = 'Client date of birth is missing.';
		}
		
		// Client age check
		if ( (int) element('ci_age', $journey, 0) < 18)
		{
			$errors[] = 'Client must be age 18 or over.';
		}
		
		// Year of birth is less than 1902
		if (date('Y', strtotime(element('ci_date_of_birth', $journey, 0))) < 1903)
		{
			$errors[] = 'Year of birth must be greater than 1902.';
		}
		
		// Date of birth is less than referral date
		if (strtotime(element('ci_date_of_birth', $journey, 1)) > strtotime(element('j_date_of_referral', $journey, 0)))
		{
			$errors[] = 'Date of birth must be before referral date.';
		}
		
		// Missing gender
		if (element('ci_gender', $journey, NULL) === NULL)
		{
			$errors[] = 'Client gender is missing.';
		}
		
		// Empty referral date
		if (element('j_date_of_referral', $journey, NULL) === NULL)
		{
			$errors[] = 'Referral date is missing.';
		}
		
		// Check consent to NDTMS sharing Y/N
		if (element('ci_consents_to_ndtms', $journey, NULL) === NULL)
		{
			$errors[] = 'Client consent to NDTMS sharing has not been recorded.';
		}
		
		// Substance misuse information
		if (element('jd_substance_1', $journey, NULL) === NULL)
		{
			$errors[] = 'Problem substance 1 is empty or missing.';
		}
		
		// No date of triage date
		if (element('j_date_of_triage', $journey, NULL) === NULL)
		{
			$errors[] = 'Client triage appointment must be present and client must have attended.';
		}
		else
		{
			// Date of triage is less than referral date
			if (strtotime(element('j_date_of_triage', $journey, 1)) < strtotime(element('j_date_of_referral', $journey, 0)))
			{
				$errors[] = 'Triage date must be after the referral date.';
			}
		}
		
		return array(
			'valid' => empty($errors),
			'errors' => $errors,
		);
	}
	
	
	
	
}