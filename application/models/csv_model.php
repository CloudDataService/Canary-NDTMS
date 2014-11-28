<?php

class Csv_model extends CI_Model
{


	function __construct()
	{
		parent::__construct();
	}


	// returns an array of headers for NDTMS data set H CSV
	public function get_ndtms_export_schema()
	{
		$schema = array('FINITIAL', // client's first name initial
					 	'SINITIAL', // client's surname initial
						'DOB', // date of birth
						'SEX', // gender
						'ETHNIC', // ethnicity
						'NATION', // nationality
						'REFLD', // referral date
						'AGNCY', // agency code
						'CLIENT', // client reference
						'CLIENTID', // client id
						'EPISODID', // episode id
						'CONSENT', // consent for ndtms
						'PREVTR', // previously treated
						'PC', // postcode
						'ACCMNEED', // accommodation naed
						'CLALOCTN', // location of cla
						'YPLAC', // young person looking after child
						'PRNTSTAT', // parental status
						'YPSLEAD',
						'YPSMHS',
						'YPSYOT',
						'YPSSEXEX',
						'YPSSLFHM',
						'YPSUNSDR',
						'YPSOFFND',
						'YPSESTAT',
						'YPSGP',
						'YPSCAF',
						'YPSLNDIF',
						'YPSUSS',
						'YPSFOU',
						'YPTSRP',
						'DAT', // DAT of residence
						'PCT', // PCT of residence
						'LA', // Local authority of residence
						'GPPTCE', // practice code of GP
						'DRUG1', // probelm substance 1
						'DRUG1AGE', // age of first use of problem substance 1
						'ROUTE', // route of admin of problem substance 1
						'DRUG2', // problem substance 2
						'DRUG3', // problem substance 3
						'RFLS', // referral source
						'TRIAGED', // traige date
						'CPLANDT', // care plan started date
						'INJSTAT', // injecting status
						'CHILDWTH', // children
						'PREGNANT', // pregnant
						'ALCDDAYS', // total number of drinking days in past 28 days
						'ALCUNITS', // total alcohol units
						'DUALDIAG', // dual diagnsosis
						'HEPCTSTD', // hep c - latest test date
						'HEPCSTAT', // hep c - intervention status
						'HEPCTD', // hep c - tested
						'HEPBVAC', // hep b - vaccination count
						'HEPBSTAT', // hep b - intervention status
						'HLCASSDT', // drug treatment health care assessment date
						'TOPCC', // TOP care coordination
						'DISD', // discharge date
						'DISRSN', // discharge reason
						'DISDEST', // discharge destination
						'YPELEAD',
						'YPEMHS',
						'YPEYOT',
						'YPESEXEX',
						'YPESLFHM',
						'YPEUNSDR',
						'YPEOFFND',
						'YPECAFE',
						'YPEFOU',
						'YPELNDIF',
						'YPEUSS',
						'YPESTD',
						'YPEGP',
						'YPECAREP',
						'MODAL', // treatment modality
						'REFMODDT', // date referred to modality
						'MODID', // modality ID
						'FAOMODDT', // date of first appointment offered for modality
						'MODST', // modality start date
						'MODEND', // modality end date
						'MODEXIT', // modality exit status
						'TOPDATE', // TOP date
						'TOPID', // TOP id
						'TRSTAGE', // treatment stage
						'ALCUSE', // alcohol use
						'OPIUSE', // opitate use
						'CRAUSE', // crack use
						'COCAUSE', // cocaine use
						'AMPHUSE', // amphetamine use
						'CANNUSE', // cannabis use
						'OTDRGUSE', // other drug use
						'IVDRGUSE', // IV drug use
						'SHARING', // sharing
						'SHOTHEFT', // shop theft
						'DRGSELL', // drug selling
						'OTHTHEFT', // other theft
						'ASSAULT', // assault/violence
						'PSYHSTAT', // psychological health status
						'PWORK', // paid work
						'EDUCAT', // education
						'ACUTHPBM', // acute housing problem
						'HRISK', // housing risk
						'PHSTAT', // physical health status
						'QUALLIFE', // quality of life
						'INJECT', // Injected in last 28 days
						'SHARE', // Ever shared?
						'PREVHEPB', // Previous hep b infected?
						'HEPCPOS', // Hep c positive?
						'REFHEPGY', // Referred for Hepatology?
						'SEXUAL', // Sexuality
						'EMPSTAT', // Employment status
						'OPRAGNCY' // Local Agency Details (Modality data item)
						);

		return $schema;
	}

	// returns array of performance out fields
	public function get_performance_output_schema()
	{
		$schema = array('Client ID',
						'Forename',
						'Surname',
						'Date of Birth',
						'Catchment area',
						'Local Authority',
						'Recovery coach',
						'Initial contact',
						'Initial referral',
						'Exit',
						'Referral Agency Type',
						'Referring Agency',
						'Date first appointment offered',
						'Triage Date',
						'Allocation Date',
						'Last Appointment Date',
						'Exit Reason',
						'Sex',
						'Post Code',
						'Ethnicity',
						'Consent');

		return $schema;
	}

	// returns array of appointment out fields
	public function get_appointment_output_schema()
	{
		$schema = array('Client ID',
						'Appointment ID',
						'Recovery coach',
						'Date',
						'Time',
						'Attended',
						'Notes');

		return $schema;
	}

	// returns array of support groups and attendees output fields
	public function get_support_groups_output_schema()
	{
		$schema = array('Group ID',
						'Group Name',
						'Date',
						'Client ID',
						'Name',
						'DOB',
						'Gender',
						'Postcode');

		return $schema;
	}


}
