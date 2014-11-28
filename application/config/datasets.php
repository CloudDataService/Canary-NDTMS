<?php

// yes no
$config['yes_no_codes'] = array(
	1 => 'Yes',
	2 => 'No',
);

// gender
$config['gender_codes'] = array(
	1 => 'Male',
	2 => 'Female',
);

// journey status
$config['j_status_codes'] = array(
	3 => 'Initial Contact',
	4 => 'Referral',
	1 => 'Open',
	2 => 'Closed',
);

$config['j_status_data'] = array(
	3 => array('next' => 4),
	4 => array('next' => 1),
	1 => array('next' => 2),
	2 => array('next' => null),
);

$config['j_tiers'] = array(
	'Brief intervention',
	'Tier 2',
	'Tier 3',
);

// hep c intervention status
$config['hep_c_intervention_status_codes'] = array(
	'A' => 'Offered and accepted',
	'B' => 'Offered and refused',
	'D' => 'Not offered',
);

// hep b vaccination count
$config['hep_b_vaccination_count_codes'] = array(
	'1' => 'One vaccination',
	'2' => 'Two vaccinations',
	'3' => 'Three vaccinations',
	'C' => 'Course completed',
);

// hep b intervention status
$config['hep_b_intervention_status_codes'] = array(
	'A' => 'Offered and accepted',
	'B' => 'Offered and refused',
	'C' => 'Immunised already',
	'D' => 'Not offered',
	'E' => 'Acquired Immunity',
);

// sex worker category
$config['sex_worker_category_codes'] = array(
	'1' => 'Selling sex on the street',
	'2' => 'Selling sex from a premises',
	'3' => 'Not a sex worker',
);

// modality exit status
$config['exit_status_codes'] = array(
	'A' => 'Mutually agreed planned exit',
	'B' => 'Clients unilateral unplanned exit',
	'C' => 'Intervention withdrawn',
);

// catchment area
$config['catchment_area_codes'] = array(

	/*
	 * Catchment areas. Add an entry for each area.
	 *
	 * 'Label' => array(
	 *		'dat_code' => "",
	 *		'pct_code' => "",
	 *		'local_authority_code' => "",
	 *		'agency_code' => "",
	 * ),
	 */
	'Wansbeck'		  => array('dat_code'			  => 'A10B',
							   'pct_code'			  => 'TAC',
							   'local_authority_code' => '',
							   'agency_code'		  => ''),
	'Castle Morpeth'  => array('dat_code'			  => 'A10B',
							   'pct_code'			  => 'TAC',
							   'local_authority_code' => '',
							   'agency_code'		  => ''),
	'Blyth Valley'	  => array('dat_code'			  => 'A10B',
							   'pct_code'			  => 'TAC',
							   'local_authority_code' => '',
							   'agency_code'		  => ''),
	'Tynedale'		  => array('dat_code'			  => 'A10B',
							   'pct_code'			  => 'TAC',
							   'local_authority_code' => '',
							   'agency_code'		  => ''),
	'Berwick'		  => array('dat_code'			  => 'A10B',
							   'pct_code'			  => 'TAC',
							   'local_authority_code' => '',
							   'agency_code'		  => ''),
	'Alnwick'		  => array('dat_code'			  => 'A10B',
							   'pct_code'			  => 'TAC',
							   'local_authority_code' => '',
							   'agency_code'		  => ''),

	'Gateshead' 	  => array('dat_code' 			  => 'A09B',
							   'pct_code' 			  => '5KF',
							   'local_authority_code' => '00CH',
							   'agency_code'		  => 'N0853'),

	'South Tyneside'  => array('dat_code' 			  => 'A11B',
							   'pct_code'			  => '5KG',
							   'local_authority_code' => '00CL',
							   'agency_code'		  => 'N0855'),

	'Sunderland'      => array('dat_code' 			  => 'A12B',
							   'pct_code'			  => '5KL',
							   'local_authority_code' => '00CM',
							   'agency_code'		  => 'N0852'),

	'Other' 		  => array('dat_code' 			  => 'A12B',
							   'pct_code'			  => '5KL',
							   'local_authority_code' => '00CM',
							   'agency_code'		  => 'N0852')
);

// treatment modality
$config['treatment_modality_codes'] = array(
	'1' => 'Inpatient treatment',
	'2' => 'Specialist prescribing',
	'3' => 'GP prescribing',
	'4' => 'Structured psychosocial intervention',
	'5' => 'Structured day programme',
	'6' => 'Residential rehabilitation',
	'7' => 'Aftercare',
	'8' => 'Needle Exchange',
	'9' => 'Outreach',
	'10' => 'Advice and information',
	'11' => 'Structured alcohol intervention',
	'12' => 'Other structured intervention',
);

// discharge reason
$config['discharge_reason_codes'] = array(
	'1' => 'Treatment completed drug free',
	'2' => 'Treatment completed',
	'3' => 'Treatment withdrawn/breach of contract',
	'4' => 'No appropriate treatment available',
	'5' => 'Referred on',
	'6' => 'Dropped out/left',
	'7' => 'Moved away',
	'8' => 'Prison',
	'9' => 'Died',
	'10' => 'Other',
	'11' => 'Not known',
	'12' => 'Treatment declined by client',
	'13' => 'Inappropriate Referral',
);

// injecting status
$config['injecting_status_codes'] = array(
	'P' => 'Previously injected (but not currently)',
	'C' => 'Currently injecting',
	'N' => 'Never injected',
	'Z' => 'Client declined to answer',
);

// route of administration
$config['route_of_admin_codes'] = array(
	'1' => 'Inject',
	'2' => 'Sniff',
	'3' => 'Smoke',
	'4' => 'Oral',
	'5' => 'Other',
);

// employment status
$config['employment_status_codes'] = array(
	'1' => 'Regular Employment',
	'2' => 'Pupil/Student',
	'9' => 'Long term sick or disabled',
	'10' => 'Homemaker',
	'12' => 'Unemployed and seeking work',
	'13' => 'Not receiving benefits',
	'14' => 'Unpaid voluntary work',
	'15' => 'Retired from paid work',
	'99' => 'Not Stated',
	'5' => 'Other',
	'6' => 'Not Known',
);

// relationship status
$config['relationship_status_codes'] = array(
	'1' => 'Single',
	'2' => 'Married',
	'3' => 'Civil Partnership',
	'4' => 'Divorced',
	'5' => 'Widowed',
	'6' => 'Prefer not to say',
	'7' => 'Living with Partner',
);

// sexuality
$config['sexuality_codes'] = array(
	'H' => 'Heterosexual',
	'G' => 'Gay',
	'B' => 'Bi-Sexual',
	'R' => 'Other',
	'Z' => 'Prefer not to say',
);

// referral source
$config['referral_source_codes'] = array(
	'1'  => 'Drug service statutory',
	'2'  => 'Drug service non-stat',
	'3'  => 'GP',
	'4'  => 'Self',
	'5'  => 'Arrest Referral / DIP',
	'6'  => 'DRR',
	'8'  => 'Probation',
	'9'  => 'A&E',
	'10' => 'Syringe Exchange',
	'11' => 'Psychiatry',
	'12' => 'Community care assessment',
	'13' => 'CARAT / Prison',
	'14' => 'Employment Service',
	'15' => 'Other',
	'16' => 'Education Service',
	'17' => 'PRU',
	'18' => 'Connexions',
	'19' => 'Social Services',
	'20' => 'LAC',
	'21' => 'Sex Worker Project',
	'22' => 'General Hospital',
	'23' => 'Psychological Services',
	'24' => 'Relative',
	'25' => 'Concerned other',
);

// accommodation needs
$config['accommodation_need_codes'] = array(
	'1' => 'NFA - urgent housing problem',
	'2' => 'Housing problem',
	'3' => 'No housing problem',
);

// accommodation status
$config['accommodation_status_codes'] = array(
	'1' => 'No fixed abode',
	'2' => 'Private tenant (secure)',
	'3' => 'Home owner',
	'4' => 'Housing association',
	'5' => 'Hostel',
	'6' => 'Hotel/B&B',
	'7' => 'Hospital',
	'8' => 'Prison',
	'9' => 'Detox establishment',
	'10' => 'Semi independent living',
	'11' => 'Not known/provided',
	'12' => 'Other',
);

// parental status
$config['parental_status_codes'] = array(
	'1' => 'Children living with client',
	'2' => 'Children living with partner',
	'3' => 'Children living with other family member',
	'4' => 'Children in care',
	'6' => 'Other',
	'7' => 'No children',
);

// parental status codes for ntdms
$config['parental_status_codes_ndtms'] = array(
	'11' => 'All the children live with client',
	'12' => 'Some of the children live with client',
	'13' => 'None of the children live with client',
	'14' => 'Not a parent',
	'15' => 'Client declined to answer',
);

// access to children
$config['access_to_children_codes'] = array(
	'1' => 'Living with you',
	'2' => 'Visiting you on a regular basis',
	'3' => 'Any regular contact with them',
	'4' => 'Visiting you overnight',
	'6' => 'You visiting them',
	'7' => 'None',
	'8' => 'Other',
);

// ethnicity
$config['ethnicity_codes'] = array(
	'A' => 'White British',
	'B' => 'White Irish',
	'C' => 'Other White',
	'D' => 'White and Black Caribbean',
	'E' => 'White and Black African',
	'F' => 'White and Asian',
	'G' => 'Other mixed',
	'H' => 'Indian',
	'J' => 'Pakistani',
	'K' => 'Bangladeshi',
	'L' => 'Other Asian',
	'M' => 'Caribbean',
	'N' => 'African',
	'P' => 'Other Black',
	'R' => 'Chinese',
	'S' => 'Other',
	'Z' => 'Not specified'
);

// country
$config['country_codes'] = array(
	'GBR' => 'British',
	'ABW' => 'Aruba',
	'AFG' => 'Afghanistan',
	'AGO' => 'Angola',
	'AIA' => 'Anguilla',
	'ALA' => 'Aland Islands',
	'ALB' => 'Albania',
	'AND' => 'Andorra',
	'ARE' => 'United Arab Emirates',
	'ARG' => 'Argentina',
	'ARM' => 'Armenia',
	'ASM' => 'American Samoa',
	'ATA' => 'Antarctica',
	'ATF' => 'French Southern Territories',
	'ATG' => 'Antigua and Barbuda',
	'AUS' => 'Australia',
	'AUT' => 'Austria',
	'AZE' => 'Azerbaijan',
	'BDI' => 'Burundi',
	'BEL' => 'Belgium',
	'BEN' => 'Benin',
	'BES' => 'Bonaire, Saint Eustatius and Saba',
	'BFA' => 'Burkina Faso',
	'BGD' => 'Bangladesh',
	'BGR' => 'Bulgaria',
	'BHR' => 'Bahrain',
	'BHS' => 'Bahamas',
	'BIH' => 'Bosnia and Herzegovina',
	'BLM' => 'Saint Barthélemy',
	'BLR' => 'Belarus',
	'BLZ' => 'Belize',
	'BMU' => 'Bermuda',
	'BOL' => 'Bolivia, Plurinational State of',
	'BRA' => 'Brazil',
	'BRB' => 'Barbados',
	'BRN' => 'Brunei Darussalam',
	'BTN' => 'Bhutan',
	'BVT' => 'Bouvet Island',
	'BWA' => 'Botswana',
	'CAF' => 'Central African Republic',
	'CAN' => 'Canada',
	'CCK' => 'Cocos (Keeling) Islands',
	'CHE' => 'Switzerland',
	'CHL' => 'Chile',
	'CHN' => 'China',
	'CIV' => 'Côte d\'Ivoire',
	'CMR' => 'Cameroon',
	'COD' => 'Congo, the Democratic Republic of the',
	'COG' => 'Congo',
	'COK' => 'Cook Islands',
	'COL' => 'Colombia',
	'COM' => 'Comoros',
	'CPV' => 'Cape Verde',
	'CRI' => 'Costa Rica',
	'CUB' => 'Cuba',
	'CUW' => 'Curaçao',
	'CXR' => 'Christmas Island',
	'CYM' => 'Cayman Islands',
	'CYP' => 'Cyprus',
	'CZE' => 'Czech Republic',
	'DEU' => 'Germany',
	'DJI' => 'Djibouti',
	'DMA' => 'Dominica',
	'DNK' => 'Denmark',
	'DOM' => 'Dominican Republic',
	'DZA' => 'Algeria',
	'ECU' => 'Ecuador',
	'EGY' => 'Egypt',
	'ERI' => 'Eritrea',
	'ESH' => 'Western Sahara',
	'ESP' => 'Spain',
	'EST' => 'Estonia',
	'ETH' => 'Ethiopia',
	'FIN' => 'Finland',
	'FJI' => 'Fiji',
	'FLK' => 'Falkland Islands (Malvinas)',
	'FRA' => 'France',
	'FRO' => 'Faroe Islands',
	'FSM' => 'Micronesia, Federated States of',
	'GAB' => 'Gabon',
	'GEO' => 'Georgia',
	'GGY' => 'Guernsey',
	'GHA' => 'Ghana',
	'GIB' => 'Gibraltar',
	'GIN' => 'Guinea',
	'GLP' => 'Guadeloupe',
	'GMB' => 'Gambia',
	'GNB' => 'Guinea-Bissau',
	'GNQ' => 'Equatorial Guinea',
	'GRC' => 'Greece',
	'GRD' => 'Grenada',
	'GRL' => 'Greenland',
	'GTM' => 'Guatemala',
	'GUF' => 'French Guiana',
	'GUM' => 'Guam',
	'GUY' => 'Guyana',
	'HKG' => 'Hong Kong',
	'HMD' => 'Heard Island and McDonald Islands',
	'HND' => 'Honduras',
	'HRV' => 'Croatia',
	'HTI' => 'Haiti',
	'HUN' => 'Hungary',
	'IDN' => 'Indonesia',
	'IMN' => 'Isle of Man',
	'IND' => 'India',
	'IOT' => 'British Indian Ocean Territory',
	'IRL' => 'Ireland',
	'IRN' => 'Iran, Islamic Republic of',
	'IRQ' => 'Iraq',
	'ISL' => 'Iceland',
	'ISR' => 'Israel',
	'ITA' => 'Italy',
	'JAM' => 'Jamaica',
	'JEY' => 'Jersey',
	'JOR' => 'Jordan',
	'JPN' => 'Japan',
	'KAZ' => 'Kazakhstan',
	'KEN' => 'Kenya',
	'KGZ' => 'Kyrgyzstan',
	'KHM' => 'Cambodia',
	'KIR' => 'Kiribati',
	'KNA' => 'Saint Kitts and Nevis',
	'KOR' => 'Korea, Republic of',
	'KWT' => 'Kuwait',
	'LAO' => 'Lao People\'s Democratic Republic',
	'LBN' => 'Lebanon',
	'LBR' => 'Liberia',
	'LBY' => 'Libyan Arab Jamahiriya',
	'LCA' => 'Saint Lucia',
	'LIE' => 'Liechtenstein',
	'LKA' => 'Sri Lanka',
	'LSO' => 'Lesotho',
	'LTU' => 'Lithuania',
	'LUX' => 'Luxembourg',
	'LVA' => 'Latvia',
	'MAC' => 'Macao',
	'MAF' => 'Saint Martin (French part)',
	'MAR' => 'Morocco',
	'MCO' => 'Monaco',
	'MDA' => 'Moldova, Republic of',
	'MDG' => 'Madagascar',
	'MDV' => 'Maldives',
	'MEX' => 'Mexico',
	'MHL' => 'Marshall Islands',
	'MKD' => 'Macedonia, the former Yugoslav Republic of',
	'MLI' => 'Mali',
	'MLT' => 'Malta',
	'MMR' => 'Myanmar',
	'MNE' => 'Montenegro',
	'MNG' => 'Mongolia',
	'MNP' => 'Northern Mariana Islands',
	'MOZ' => 'Mozambique',
	'MRT' => 'Mauritania',
	'MSR' => 'Montserrat',
	'MTQ' => 'Martinique',
	'MUS' => 'Mauritius',
	'MWI' => 'Malawi',
	'MYS' => 'Malaysia',
	'MYT' => 'Mayotte',
	'NAM' => 'Namibia',
	'NCL' => 'New Caledonia',
	'NER' => 'Niger',
	'NFK' => 'Norfolk Island',
	'NGA' => 'Nigeria',
	'NIC' => 'Nicaragua',
	'NIU' => 'Niue',
	'NLD' => 'Netherlands',
	'NOR' => 'Norway',
	'NPL' => 'Nepal',
	'NRU' => 'Nauru',
	'NZL' => 'New Zealand',
	'OMN' => 'Oman',
	'PAK' => 'Pakistan',
	'PAN' => 'Panama',
	'PCN' => 'Pitcairn',
	'PER' => 'Peru',
	'PHL' => 'Philippines',
	'PLW' => 'Palau',
	'PNG' => 'Papua New Guinea',
	'POL' => 'Poland',
	'PRI' => 'Puerto Rico',
	'PRK' => 'Korea, Democratic People\'s Republic of',
	'PRT' => 'Portugal',
	'PRY' => 'Paraguay',
	'PSE' => 'Palestinian Territory, Occupied',
	'PYF' => 'French Polynesia',
	'QAT' => 'Qatar',
	'REU' => 'Réunion',
	'ROU' => 'Romania',
	'RUS' => 'Russian Federation',
	'RWA' => 'Rwanda',
	'SAU' => 'Saudi Arabia',
	'SDN' => 'Sudan',
	'SEN' => 'Senegal',
	'SGP' => 'Singapore',
	'SGS' => 'South Georgia and the South Sandwich Islands',
	'SHN' => 'Saint Helena, Ascension and Tristan da Cunha',
	'SJM' => 'Svalbard and Jan Mayen',
	'SLB' => 'Solomon Islands',
	'SLE' => 'Sierra Leone',
	'SLV' => 'El Salvador',
	'SMR' => 'San Marino',
	'SOM' => 'Somalia',
	'SPM' => 'Saint Pierre and Miquelon',
	'SRB' => 'Serbia',
	'STP' => 'Sao Tome and Principe',
	'SUR' => 'Suriname',
	'SVK' => 'Slovakia',
	'SVN' => 'Slovenia',
	'SWE' => 'Sweden',
	'SWZ' => 'Swaziland',
	'SXM' => 'Sint Maarten (Dutch part)',
	'SYC' => 'Seychelles',
	'SYR' => 'Syrian Arab Republic',
	'TCA' => 'Turks and Caicos Islands',
	'TCD' => 'Chad',
	'TGO' => 'Togo',
	'THA' => 'Thailand',
	'TJK' => 'Tajikistan',
	'TKL' => 'Tokelau',
	'TKM' => 'Turkmenistan',
	'TLS' => 'Timor-Leste',
	'TON' => 'Tonga',
	'TTO' => 'Trinidad and Tobago',
	'TUN' => 'Tunisia',
	'TUR' => 'Turkey',
	'TUV' => 'Tuvalu',
	'TWN' => 'Taiwan, Province of China',
	'TZA' => 'Tanzania, United Republic of',
	'UGA' => 'Uganda',
	'UKR' => 'Ukraine',
	'UMI' => 'United States Minor Outlying Islands',
	'URY' => 'Uruguay',
	'USA' => 'United States',
	'UZB' => 'Uzbekistan',
	'VAT' => 'Holy See (Vatican City State)',
	'VCT' => 'Saint Vincent and the Grenadines',
	'VEN' => 'Venezuela, Bolivarian Republic of',
	'VGB' => 'Virgin Islands, British',
	'VIR' => 'Virgin Islands, U.S.',
	'VNM' => 'Viet Nam',
	'VUT' => 'Vanuatu',
	'WLF' => 'Wallis and Futuna',
	'WSM' => 'Samoa',
	'YEM' => 'Yemen',
	'ZAF' => 'South Africa',
	'ZMB' => 'Zambia',
	'ZWE' => 'Zimbabwe',
);

$config['drug_codes'] = array(
	'1011' => 'Herion',
	'1105' => 'Methadone',
	'1000' => 'Other opiates',
	'2200' => 'Benzodiazepines',
	'3100' => 'Amphetamines (excluding Ecstasy)',
	'3200' => 'Cocaine (excluding Crack)',
	'3201' => 'Crack',
	'4000' => 'Hallucinogens',
	'3406' => 'Ecstasy',
	'5000' => 'Cannabis',
	'6000' => 'Solvents',
	'2100' => 'Barbiturates',
	'8200' => 'Major Tranquilisers',
	'8300' => 'Anti-depressants',
	'7000' => 'Alcohol',
	'8799' => 'Other Drugs',
	'9005' => 'Prescription Drugs',
	'3409' => 'Nicotine',
);

$config['day_codes'] = array(
	'1' => 'Monday',
	'2' => 'Tuesday',
	'3' => 'Wednesday',
	'4' => 'Thursday',
	'5' => 'Friday',
	'6' => 'Saturday',
	'7' => 'Sunday',
);

$config['contact_method_codes'] = array(
	'EMAIL' => 'E-mail',
	'TELEPHONE' => 'Telephone',
	'POST' => 'Post',
	'NO PREFERENCE' => 'No preference',
);

$config['contact_time_codes'] = array(
	'AM' => 'AM',
	'PM' => 'PM',
	'EVENING' => 'Evening',
	'WEEKEND' => 'Weekend',
	'ANYTIME' => 'Anytime',
);

$config['disability_codes'] = array(
	'Behaviour And Emotion' => 'Behaviour and Emotional',
	'Hearing' => 'Hearing',
	'Dexterity' => 'Manual Dexterity',
	'Memory' => 'Memory or ability to concentrate, learn or understand',
	'Mobility' => 'Mobility and Gross Motor',
	'Perception of Danger' => 'Perception of Physical Danger',
	'Personal' => 'Personal, Self Care and Continence',
	'Progressive and Physical' => 'Progressive Conditions and Physical Health (such as HIV, cancer, multiple sclerosis, fits etc)',
	'Sight' => 'Sight',
	'Speech' => 'Speech',
	'Other' => 'Other',
	'None' => 'None',
);

// Journey/client types
$config['types'] = array(
	'C' => 'Client',
	'F' => 'Family',
);


$config['relative_types'] = array(
	'partner' => 'Partner',
	'parent' => 'Parent',
	'child' => 'Child',
	'sibling' => 'Sibling',
	'auncle' => 'Aunt/Uncle',
	'nibling' => 'Niece/Nephew',
	'cousin' => 'Cousin',
	'other' => 'Other/extended relative',
);

$config['relative_types_opposite'] = array(
	'partner' => 'partner',
	'parent' => 'child',
	'child' => 'parent',
	'sibling' => 'sibling',
	'auncle' => 'nibling',
	'nibling' => 'auncle',
	'cousin' => 'cousin',
	'other' => 'other',
);

$config['lasar_complexity_levels'] = array(
	'very low'	=> 'Very Low',
	'low' 		=> 'Low',
	'moderate'	=> 'Moderate',
	'high'		=> 'High',
);

// Journey Modality Info
$config['modality_treatments'] = array(
	8    => 'Needle Exchange',
	9    => 'Outreach',
	10   => 'Advice and information',
	76   => 'ALC - Brief Intervention',
	93   => 'LASARS Assessment',
	94   => 'Pharmacological Intervention',
	95   => 'Psychosocial Intervention',
	96   => 'Recovery Support',
	'F1' => 'Tier 2',
	'F2' => 'Tier 3',
);

$config['intervention_setting'] = array(
	1 => 'GP',
	2 => 'Inpatient',
	3 => 'Outpatient',
	4 => 'Inpatient/Outpatient',
	5 => 'Prison',
	6 => 'Community',
	7 => 'Recovery House',
	8 => 'Residential',
);

$config['exit_status'] = array(
	'A' => 'Mutually agreed planned exit',
	'B' => 'Clients unilateral unplanned exit',
	'C' => 'Intervention withdrawn',
);

$config['religions'] = array(
	'BUDDHIST',
	'CHRISTIAN',
	'HINDU',
	'JEWISH',
	'MUSLIM',
	'SIKH',
	'NO RELIGION',
	'OTHER',
	'PREFER NOT TO SAY',
);

// Assessment criteria list types
$config['acl_types'] = array(
	'csop' => 'CSOP',
	'top' => 'TOP',
	'other' => 'Other',
);


// Mapping of categories to releveant outcomes from assessment lists.
// acl_id is the ID of the corresponding `ass_criteria_lists` entry.
// the numeric key on the inner array (each "outcome") is the matching `aco_num` column in the `ass_criteria_outcomes` table.
$config['acl_reports'] = array(

	'csop' => array(

		'acl_id' => 10,
		'outcomes' => array(

			'Knowledge of substance use' => array(
				1 => 'Physical effects',
				2 => 'Psychological effects',
			),

			'Relationship with substance user' => array(
				3 => 'Trust',
				4 => 'Communication',
				5 => 'Conflict',
			),

			'Relationship with family' => array(
				7 => 'Trust',
				6 => 'Communication',
				8 => 'Conflict',
				9 => 'Support',
			),

			'Safety of Household' => array(
				10 => 'Concern',
			),

			'Relationship with wider community' => array(
				12 => 'Social interactions',
			),

			'Physical & Psychological health' => array(
				13 => 'Anxiety and fear',
				14 => 'Physical symptoms',
				15 => 'Mood',
				16 => 'Sleep',
			),

			'Effects on daily life' => array(
				17 => 'Regular work',
				18 => 'Concentration',
				19 => 'Cope financially',
				20 => 'Stress',
			),

			'Tackling the problem' => array(
				21 => 'Dealing with User',
				22 => 'Accessing support',
				23 => 'Overdose prevention',
			),

		),		// end outcomes

	),		// end csop

);