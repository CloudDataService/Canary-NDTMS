<?php

// Used to generate links/tabs to reports

$config['reports']['groups'] = array(

	'family' => array(
		'title' => 'Family Team',
		'description' => 'Family Team reporting',
		'models' => array('family_report_model'),
	),

	'clients_in' => array(
		'title' => 'Clients In',
		'description' => 'Incoming clients',
		'models' => array('clients_in_report_model'),
	),

	'clients_out' => array(
		'title' => 'Clients Out',
		'description' => 'Clients leaving',
		'models' => array('clients_out_report_model'),
	),

	'events' => array(
		'title' => 'Events',
		'description' => 'Journey events',
		'models' => array('events_report_model'),
	),

);



/**
 * Reports list config
 *
 * Each report group has a list of individual reports. These arrays determine the config for each one.
 *
 * title		Title of report
 * description		Description of report if available
 * model		Which model to use to get the data
 * function			The function on the model to call to get the data
 * view			The view to call to show the data (relative to views/admin/reports/)
 */
$config['reports']['reports'] = array(

	// Family Team
	'family' => array(

		// report. The main family report table
		'family' => array(
			'title' => 'Family Team report',
			'description' => 'This is the family team report table',
			'model' => 'family_report_model',
			'function' => 'family',
			'view' => 'family/family',
		),

	),

	// Incoming Clients
	'clients_in' => array(

		// Monthly referrals
		'month_referrals' => array(
			'title' => 'Number of Journeys by Month of Referral',
			'description' => 'Details the number of new journey commencing, by month of their referral. The default view of this report displays the journeys referred within the previous twelve months of today&rsquo;s date.',
			'model' => 'clients_in_report_model',
			'function' => 'month_referrals',
		),

		// Referral Source Category
		'referral_source_category' => array(
			'title' => 'Number of Journeys by Referral Source Category',
			'description' => 'Details the journey referral source categories.',
			'model' => 'clients_in_report_model',
			'function' => 'referral_source_category',
		),

		// Referral *Source*
		'referral_source' => array(
			'title' => 'Number of Journeys by Referral Source',
			'description' => 'Details the journey referral source.',
			'model' => 'clients_in_report_model',
			'function' => 'referral_source',
		),

		// New vs Existing Clients
		'new_existing' => array(
			'title' => 'New and Existing Client Breakdown',
			'description' => 'Details clients entering the service and identifies if they are new (i.e. has not been seen previously) or existing (i.e. has attended previously).',
			'model' => 'clients_in_report_model',
			'function' => 'new_existing',
		),

		// Age of Client at Referral
		'client_age_referral' => array(
			'title' => 'Age of Client at Referral',
			'description' => 'Details the age of the client on the date of referral.',
			'model' => 'clients_in_report_model',
			'function' => 'client_age_referral',
		),

		// Client Declared Disability Breakdown
		'disability_count' => array(
			'title' => 'Client Declared Disability Breakdown',
			'description' => 'Details the number of clients whereby their disability has been declared.',
			'model' => 'clients_in_report_model',
			'function' => 'disability_count',
		),

		// Client Gender Breakdown
		'client_gender' => array(
			'title' => 'Client Gender Breakdown',
			'description' => 'Details the gender breakdown of clients.',
			'model' => 'clients_in_report_model',
			'function' => 'client_gender',
		),

		// Client Ethnicity Breakdown
		'client_ethnicity' => array(
			'title' => 'Client Ethnicity Breakdown',
			'description' => 'Details the ethnic breakdown of clients.',
			'model' => 'clients_in_report_model',
			'function' => 'client_ethnicity',
		),

		// Client Catchment Area Breakdown
		'catchment_area' => array(
			'title' => 'Client Catchment Area Breakdown',
			'description' => 'Details the catchment area breakdown of clients.',
			'model' => 'clients_in_report_model',
			'function' => 'catchment_area',
		),

		// Client Tier Breakdown
		'tier' => array(
			'title' => 'Client Tier Breakdown',
			'description' => 'Details the tier breakdown of clients.',
			'model' => 'clients_in_report_model',
			'function' => 'tier',
		),

		// Problem Substances
		'problem_substances' => array(
			'title' => 'Problem Substances 1 &amp; 2',
			'description' => 'Details the problem substances 1 &amp; 2 breakdown.',
			'view' => 'clients_in/problem_substances',
		),

			// Problem Substance 1
			'problem_substance_1' => array(
				'title' => 'Problem Substance 1',
				'model' => 'clients_in_report_model',
				'function' => 'problem_substance_1',
				'skip_html' => TRUE,
			),

			// Problem Substance 2
			'problem_substance_2' => array(
				'title' => 'Problem Substance 2',
				'model' => 'clients_in_report_model',
				'function' => 'problem_substance_2',
				'skip_html' => TRUE,
			),

	),

	// Outgoing Clients
	'clients_out' => array(

		// Monthly closures
		'month_closures' => array(
			'title' => 'Number of Journeys by Month of Closure',
			'description' => 'Details the number of journies exiting the service, by month of exit.',
			'model' => 'clients_out_report_model',
			'function' => 'month_closures',
		),

		// Exit status
		'exit_status' => array(
			'title' => 'Exit Status Reasons by Month of Closure',
			'description' => 'Details the specific exit status for the closure of each journey closed within each month of the date range.',
			'model' => 'clients_out_report_model',
			'function' => 'exit_status',
		),

	),

	// Events
	'events' => array(

		// Event types
		'event_types' => array(
			'title' => 'Event Type',
			'description' => 'Breakdown of amount of event types happening within the given months.',
			'model' => 'events_report_model',
			'function' => 'event_types',
		),

		// First Assessments
		'first_assessments' => array(
			'title' => 'First Assessments',
			'description' => 'Total number of first assessments happening within the given months.',
			'model' => 'events_report_model',
			'function' => 'first_assessments',
		),

	),

);




/**
 * Report-specific configurations.
 *
 * Each report's config will be available to its PHP view and JSON object.
 */
$config['reports']['config'] = array(

	'family' => array(

		// Family report table has a list of outcomes and an associated value.
		// Here, we just list the result array key to its title
		'family' => array(
			'carers_supported' => 'Carers Supported',
			'carers_referred' => 'Carers Referred',
			'volunteers_recruited' => 'Volunteers/Peer Mentors recruited and supported',
			'carers_improve_psych' => 'Carers reporting improvement in psychological, physical and mental wellbeing',
			'carers_access_support' => 'Carers accessing support groups / drop ins',
			'carers_access_respite' => 'Carers accessing respite provision',
			'carers_improve_social' => 'Carers reporting improved social interactions',
			'people_attend_parent_factor' => 'People attending Parent Factor Training',
			'carers_improve_child' => 'Carers reporting improvement in child safety',
			'marketing' => 'Design, print and distribute marketing material',
			'women_freedom' => 'Women attending Freedom Programme',
			'carers_open_t3_1to1_4' => 'All carers open to Tier 3 to have 1:1s at least every 4 weeks',
			'carers_open_t3_csop_plan' => 'All carers open to Tier 3 to have a CSOP care plan',
			'carers_open_t3_csop_graph' => 'All carers open to Tier 3 to have a CSOP tracking graph',
			'carers_csop_first_appt' => 'All carers to complete CSOP at first face to face appointment',
			'csop_reviews_28' => 'CSOP reviews to be completed within 28 day window',
			'carers_open_t2_1to1_12' => 'All carers open to Tier 2 to have 1:1’s at least every 12 weeks.',
		),

	),

	'clients_in' => array(

		// Just point referral_source_category to the referral_source JS class.
		// They pretty much do the same thing. Same chart, different values!
		'referral_source_category' => array(
			'js_class' => 'referral_source',
		),

		'problem_substance_1' => array(
			'js_class' => 'substance',
		),

		'problem_substance_2' => array(
			'js_class' => 'substance',
		),

	),

);




