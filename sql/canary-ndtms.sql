# ************************************************************
# Sequel Pro SQL dump
# Version 4096
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: localhost (MySQL 5.6.21)
# Database: canary
# Generation Time: 2014-11-07 15:38:10 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table a2rc
# ------------------------------------------------------------

DROP TABLE IF EXISTS `a2rc`;

CREATE TABLE `a2rc` (
  `a2rc_a_id` tinyint(3) unsigned NOT NULL,
  `a2rc_rc_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`a2rc_a_id`,`a2rc_rc_id`),
  UNIQUE KEY `a2rc_a_id` (`a2rc_a_id`),
  UNIQUE KEY `a2rc_rc_id` (`a2rc_rc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table admin_permission_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admin_permission_types`;

CREATE TABLE `admin_permission_types` (
  `apt_id` int(11) NOT NULL AUTO_INCREMENT,
  `apt_name` varchar(150) NOT NULL,
  `apt_active` tinyint(1) NOT NULL DEFAULT '1',
  `apt_can_read_client` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_edit_client` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_add_client` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_delete_client` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_read_family` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_edit_family` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_add_family` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_delete_family` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_manage_admins` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_manage_options` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_approve_client` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_approve_family` tinyint(1) NOT NULL DEFAULT '0',
  `apt_reports` tinyint(1) NOT NULL DEFAULT '0',
  `apt_can_unpublish` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`apt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table admins
# ------------------------------------------------------------

DROP TABLE IF EXISTS `admins`;

CREATE TABLE `admins` (
  `a_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `a_ip` varchar(16) DEFAULT NULL,
  `a_datetime_last_login` datetime DEFAULT NULL,
  `a_datetime_tc_agree` datetime DEFAULT NULL,
  `a_fname` varchar(32) NOT NULL,
  `a_sname` varchar(32) NOT NULL,
  `a_email` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `a_password` varchar(128) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `a_master` tinyint(1) NOT NULL,
  `a_type` enum('Service','Family') DEFAULT NULL,
  `a_apt_id` int(11) DEFAULT NULL,
  `a_verified` tinyint(1) NOT NULL DEFAULT '0',
  `a_active` tinyint(1) NOT NULL DEFAULT '1',
  `a_options` varchar(255) NOT NULL,
  `a_expires_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`a_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table agencies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `agencies`;

CREATE TABLE `agencies` (
  `ag_id` int(11) NOT NULL AUTO_INCREMENT,
  `ag_name` text,
  `ag_agt_id` int(11) NOT NULL,
  `ag_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table agency_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `agency_types`;

CREATE TABLE `agency_types` (
  `agt_id` int(11) NOT NULL AUTO_INCREMENT,
  `agt_text` text,
  PRIMARY KEY (`agt_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table ass_criteria_lists
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ass_criteria_lists`;

CREATE TABLE `ass_criteria_lists` (
  `acl_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `acl_j_id` mediumint(8) unsigned DEFAULT NULL,
  `acl_type` enum('csop','top','other','journey') NOT NULL DEFAULT 'other',
  `acl_name` varchar(32) DEFAULT NULL,
  `acl_criteria` text,
  PRIMARY KEY (`acl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table ass_criteria_outcomes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `ass_criteria_outcomes`;

CREATE TABLE `ass_criteria_outcomes` (
  `aco_acl_id` smallint(5) unsigned NOT NULL,
  `aco_num` tinyint(2) unsigned NOT NULL,
  `aco_title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`aco_acl_id`,`aco_num`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table client_risk_summary
# ------------------------------------------------------------

DROP TABLE IF EXISTS `client_risk_summary`;

CREATE TABLE `client_risk_summary` (
  `crs_c_id` mediumint(8) unsigned NOT NULL,
  `crs_summary` text COLLATE utf8_bin,
  `crs_physical_risks` text COLLATE utf8_bin,
  `crs_psychological_risks` text COLLATE utf8_bin,
  `crs_social_risks` text COLLATE utf8_bin,
  `crs_violence_aggression_risks` text COLLATE utf8_bin,
  PRIMARY KEY (`crs_c_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table client_risks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `client_risks`;

CREATE TABLE `client_risks` (
  `cr_rt_id` tinyint(3) unsigned NOT NULL,
  `cr_c_id` int(10) unsigned NOT NULL,
  `cr_impact_score` tinyint(1) unsigned NOT NULL,
  `cr_likelihood_score` tinyint(1) unsigned NOT NULL,
  `cr_risk_score` tinyint(2) unsigned DEFAULT NULL,
  `cr_risk_level` enum('Very low','Low','Moderate','High') COLLATE utf8_bin DEFAULT NULL,
  `cr_risk_to_whom` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `cr_protective_factors` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`cr_rt_id`,`cr_c_id`),
  KEY `cr_rt_id` (`cr_rt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clients`;

CREATE TABLE `clients` (
  `c_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `c_fname` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `c_sname` varchar(32) NOT NULL,
  `c_gender` tinyint(1) DEFAULT NULL,
  `c_date_of_birth` date NOT NULL,
  `c_address` varchar(255) DEFAULT NULL,
  `c_post_code` varchar(8) NOT NULL,
  `c_catchment_area` enum('Wansbeck','Castle Morpeth','Blyth Valley','Tynedale','Berwick','Alnwick','Other') NOT NULL,
  `c_tel_home` varchar(16) DEFAULT NULL,
  `c_tel_mob` varchar(16) DEFAULT NULL,
  `c_is_risk` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`c_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table clients_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `clients_info`;

CREATE TABLE `clients_info` (
  `ci_j_id` mediumint(8) unsigned NOT NULL,
  `ci_fname` varchar(32) COLLATE utf8_bin NOT NULL,
  `ci_sname` varchar(32) COLLATE utf8_bin NOT NULL,
  `ci_gender` tinyint(1) DEFAULT NULL,
  `ci_date_of_birth` date DEFAULT NULL,
  `ci_address` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ci_post_code` varchar(8) COLLATE utf8_bin DEFAULT NULL,
  `ci_authority_code` varchar(4) COLLATE utf8_bin DEFAULT NULL,
  `ci_authority_name` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `ci_catchment_area` enum('Gateshead','South Tyneside','Sunderland','Other') COLLATE utf8_bin DEFAULT NULL,
  `ci_gp_code` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `ci_gp_name` varchar(64) COLLATE utf8_bin DEFAULT NULL,
  `ci_tel_home` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `ci_tel_mob` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `ci_ethnicity` char(1) COLLATE utf8_bin DEFAULT NULL,
  `ci_religion` enum('BUDDHIST','CHRISTIAN','HINDU','JEWISH','MUSLIM','SIKH','NO RELIGION','OTHER','PREFER NOT TO SAY') COLLATE utf8_bin DEFAULT NULL,
  `ci_nationality` char(3) COLLATE utf8_bin DEFAULT NULL,
  `ci_pregnant` tinyint(1) DEFAULT NULL,
  `ci_caf_completed` tinyint(1) DEFAULT NULL,
  `ci_relationship_status` tinyint(4) DEFAULT NULL,
  `ci_sexuality` char(1) COLLATE utf8_bin DEFAULT NULL,
  `ci_mental_health_issues` tinyint(1) DEFAULT NULL,
  `ci_learning_difficulties` tinyint(1) DEFAULT NULL,
  `ci_disabilities` enum('Behaviour And Emotion','Hearing','Dexterity','Memory','Mobility','Perception of Danger','Personal','Progressive and Physical','Sight','Speech','Other','None') COLLATE utf8_bin DEFAULT NULL,
  `ci_consents_to_ndtms` tinyint(1) DEFAULT NULL,
  `ci_is_risk` tinyint(1) NOT NULL DEFAULT '0',
  `ci_parental_status` tinyint(1) DEFAULT NULL,
  `ci_access_to_children` tinyint(4) DEFAULT NULL,
  `ci_no_of_children` tinyint(2) DEFAULT NULL,
  `ci_accommodation_need` tinyint(1) DEFAULT NULL,
  `ci_accommodation_status` tinyint(4) DEFAULT NULL,
  `ci_employment_status` tinyint(2) DEFAULT NULL,
  `ci_smoker` tinyint(3) unsigned DEFAULT NULL,
  `ci_contact` text COLLATE utf8_bin COMMENT 'Serialized array',
  `ci_next_of_kin_details` text COLLATE utf8_bin COMMENT 'Serialized array',
  `ci_interpreter_required` tinyint(1) DEFAULT NULL,
  `ci_preferred_contact_method` enum('EMAIL','TELEPHONE','POST','NO PREFERENCE') COLLATE utf8_bin DEFAULT NULL,
  `ci_preferred_contact_time` enum('AM','PM','EVENING','WEEKEND','ANYTIME') COLLATE utf8_bin DEFAULT NULL,
  `ci_staff_can_leave_message` tinyint(1) DEFAULT NULL,
  `ci_staff_can_identify_themselves` tinyint(1) DEFAULT NULL,
  `ci_preferred_appointment_time` enum('AM','PM','EVENING','WEEKEND','ANYTIME') COLLATE utf8_bin DEFAULT NULL,
  `ci_escape_write_to_you` tinyint(1) DEFAULT NULL,
  `ci_previous_offender` tinyint(1) DEFAULT NULL,
  `ci_current_offender` tinyint(1) DEFAULT NULL,
  `ci_partner_pregnant` tinyint(1) DEFAULT NULL,
  `ci_childrens_services` tinyint(1) DEFAULT NULL,
  `ci_outcome` text COLLATE utf8_bin,
  `ci_kinship_carer` tinyint(1) DEFAULT NULL,
  `ci_escape_are_top_responsible` tinyint(1) DEFAULT NULL,
  `ci_pbr_client` tinyint(1) DEFAULT NULL,
  `ci_lasar_complexity` enum('very low','low','moderate','high') COLLATE utf8_bin DEFAULT NULL,
  `ci_external_client_id` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `ci_additional_information` text COLLATE utf8_bin,
  `ci_consent_signed` tinyint(1) DEFAULT NULL,
  `ci_ndtms_consent` tinyint(1) DEFAULT NULL,
  `ci_nta_consent` tinyint(1) DEFAULT NULL,
  `ci_csop_consent` tinyint(1) DEFAULT NULL,
  `ci_photography_consent` tinyint(1) DEFAULT NULL,
  `ci_previous_id` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`ci_j_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table dna_reasons
# ------------------------------------------------------------

DROP TABLE IF EXISTS `dna_reasons`;

CREATE TABLE `dna_reasons` (
  `dr_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `dr_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `dr_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`dr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table event_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_categories`;

CREATE TABLE `event_categories` (
  `ec_id` int(11) NOT NULL AUTO_INCREMENT,
  `ec_name` varchar(32) NOT NULL,
  `ec_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ec_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table event_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `event_types`;

CREATE TABLE `event_types` (
  `et_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `et_ec_id` int(11) DEFAULT NULL,
  `et_name` varchar(32) COLLATE utf8_bin NOT NULL,
  `et_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`et_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table family_clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `family_clients`;

CREATE TABLE `family_clients` (
  `fc_j_id` mediumint(8) unsigned NOT NULL COMMENT 'Journey ID',
  `fc_j_c_id` int(10) unsigned NOT NULL COMMENT 'ID of client for family journey j_id',
  `fc_rel_type` enum('partner','parent','child','sibling','auncle','nibling','cousin','other') NOT NULL COMMENT 'Relationship type of c_id to j_c_id (c_id is a "X" of j_c_id)',
  `fc_c_id` int(10) unsigned NOT NULL COMMENT 'Client ID'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='References clients who are family members of family journeys';



# Dump of table gps
# ------------------------------------------------------------

DROP TABLE IF EXISTS `gps`;

CREATE TABLE `gps` (
  `gp_code` varchar(32) COLLATE utf8_bin NOT NULL,
  `gp_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `gp_surgery` varchar(255) COLLATE utf8_bin NOT NULL,
  `gp_telephone` varchar(16) COLLATE utf8_bin NOT NULL,
  `gp_postcode` varchar(8) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`gp_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table internal_services
# ------------------------------------------------------------

DROP TABLE IF EXISTS `internal_services`;

CREATE TABLE `internal_services` (
  `is_id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `is_name` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`is_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table j2ag
# ------------------------------------------------------------

DROP TABLE IF EXISTS `j2ag`;

CREATE TABLE `j2ag` (
  `j2ag_id` int(11) NOT NULL AUTO_INCREMENT,
  `j2ag_j_id` int(11) NOT NULL,
  `j2ag_ag_id` int(11) NOT NULL,
  `j2ag_date` date DEFAULT NULL,
  PRIMARY KEY (`j2ag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table j2disability
# ------------------------------------------------------------

DROP TABLE IF EXISTS `j2disability`;

CREATE TABLE `j2disability` (
  `j2d_id` int(11) NOT NULL AUTO_INCREMENT,
  `j2d_j_id` int(11) NOT NULL,
  `j2d_disability` enum('Behaviour And Emotion','Hearing','Dexterity','Memory','Mobility','Perception of Danger','Personal','Progressive and Physical','Sight','Speech','Other','None') NOT NULL,
  PRIMARY KEY (`j2d_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table j2is
# ------------------------------------------------------------

DROP TABLE IF EXISTS `j2is`;

CREATE TABLE `j2is` (
  `j2is_id` int(11) NOT NULL AUTO_INCREMENT,
  `j2is_j_id` int(11) NOT NULL,
  `j2is_is_id` int(11) NOT NULL,
  PRIMARY KEY (`j2is_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table job_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `job_roles`;

CREATE TABLE `job_roles` (
  `jr_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `jr_title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `jr_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`jr_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table journey_alcohol
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_alcohol`;

CREATE TABLE `journey_alcohol` (
  `jal_j_id` mediumint(8) unsigned NOT NULL,
  `jal_safe_at_home` tinyint(1) DEFAULT NULL,
  `jal_others_safe` tinyint(1) DEFAULT NULL,
  `jal_affect_bills` tinyint(1) DEFAULT NULL,
  `jal_incurred_debt` tinyint(1) DEFAULT NULL,
  `jal_avg_daily_units` tinyint(3) unsigned DEFAULT NULL,
  `jal_last_28_drinking_days` tinyint(2) unsigned DEFAULT NULL,
  `jal_age_started_drinking` tinyint(2) unsigned DEFAULT NULL,
  PRIMARY KEY (`jal_j_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table journey_appointments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_appointments`;

CREATE TABLE `journey_appointments` (
  `ja_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ja_j_id` mediumint(8) unsigned NOT NULL,
  `ja_je_id` mediumint(8) unsigned NOT NULL,
  `ja_datetime` datetime NOT NULL,
  `ja_date_offered` date DEFAULT NULL,
  `ja_rc_id` tinyint(3) unsigned NOT NULL,
  `ja_notes` text COLLATE utf8_bin,
  `ja_attended` tinyint(1) DEFAULT NULL,
  `ja_dr_id` tinyint(3) unsigned DEFAULT NULL,
  `ja_length` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`ja_id`),
  KEY `ja_j_id` (`ja_j_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table journey_ass_criteria
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_ass_criteria`;

CREATE TABLE `journey_ass_criteria` (
  `jac_j_id` mediumint(8) unsigned NOT NULL COMMENT 'Journey ID',
  `jac_num` tinyint(2) unsigned NOT NULL COMMENT 'Criteria number',
  `jac_title` varchar(64) NOT NULL COMMENT 'Outcome',
  `jac_date_start` date DEFAULT NULL COMMENT 'Start date',
  `jac_date_end` date DEFAULT NULL COMMENT 'End date',
  PRIMARY KEY (`jac_j_id`,`jac_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table journey_ass_criteria_scores
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_ass_criteria_scores`;

CREATE TABLE `journey_ass_criteria_scores` (
  `jacs_jas_id` int(10) unsigned NOT NULL COMMENT 'Assessment ID',
  `jacs_num` tinyint(2) unsigned NOT NULL COMMENT 'Outcome number',
  `jacs_title` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `jacs_score` tinyint(2) unsigned NOT NULL COMMENT 'Score',
  PRIMARY KEY (`jacs_jas_id`,`jacs_num`),
  CONSTRAINT `journey_ass_criteria_scores_ibfk_1` FOREIGN KEY (`jacs_jas_id`) REFERENCES `journey_assessments` (`jas_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table journey_ass_scores
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_ass_scores`;

CREATE TABLE `journey_ass_scores` (
  `jass_jas_id` int(10) unsigned NOT NULL COMMENT 'Assessment ID',
  `jass_jac_num` int(10) unsigned NOT NULL COMMENT 'Criteria number',
  `jass_score` tinyint(2) unsigned NOT NULL COMMENT 'Score',
  PRIMARY KEY (`jass_jas_id`,`jass_jac_num`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table journey_assessments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_assessments`;

CREATE TABLE `journey_assessments` (
  `jas_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Assessment ID',
  `jas_j_id` mediumint(8) unsigned NOT NULL COMMENT 'Journey ID',
  `jas_acl_id` smallint(5) unsigned NOT NULL COMMENT 'Criteria list ID',
  `jas_date` date NOT NULL COMMENT 'Date of assessment',
  `jas_notes` text COMMENT 'Notes for the assessment',
  PRIMARY KEY (`jas_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table journey_changes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_changes`;

CREATE TABLE `journey_changes` (
  `jc_id` int(11) NOT NULL AUTO_INCREMENT,
  `jc_j_id` int(11) NOT NULL,
  `jc_changed_date` datetime NOT NULL,
  `jc_a_id` int(11) NOT NULL,
  `jc_old_status` int(11) NOT NULL,
  `jc_new_status` int(11) NOT NULL,
  `jc_old_tier` enum('Brief intervention','Tier 2','Tier 3') DEFAULT NULL,
  `jc_new_tier` enum('Brief intervention','Tier 2','Tier 3') DEFAULT NULL,
  PRIMARY KEY (`jc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table journey_drugs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_drugs`;

CREATE TABLE `journey_drugs` (
  `jd_j_id` mediumint(8) unsigned NOT NULL,
  `jd_injecting` char(1) COLLATE utf8_bin DEFAULT NULL,
  `jd_injected_in_last_month` tinyint(1) DEFAULT NULL,
  `jd_prev_hep_b_infection` tinyint(1) DEFAULT NULL,
  `jd_hep_b_vac_count` char(1) COLLATE utf8_bin DEFAULT NULL,
  `jd_hep_b_intervention` char(1) COLLATE utf8_bin DEFAULT NULL,
  `jd_hep_b_prev_positive` tinyint(1) DEFAULT NULL,
  `jd_hep_c_intervention` char(1) COLLATE utf8_bin DEFAULT NULL,
  `jd_hep_c_test_date` date DEFAULT NULL,
  `jd_hep_c_tested` tinyint(1) DEFAULT NULL,
  `jd_hep_c_positive` tinyint(1) DEFAULT NULL,
  `jd_substance_1` smallint(4) unsigned DEFAULT NULL,
  `jd_substance_1_route` tinyint(1) DEFAULT NULL,
  `jd_substance_1_age` tinyint(4) DEFAULT NULL,
  `jd_substance_2` smallint(4) unsigned DEFAULT NULL,
  `jd_substance_3` smallint(4) unsigned DEFAULT NULL,
  PRIMARY KEY (`jd_j_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table journey_events
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_events`;

CREATE TABLE `journey_events` (
  `je_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `je_j_id` mediumint(8) unsigned NOT NULL,
  `je_datetime` datetime NOT NULL,
  `je_et_id` tinyint(3) unsigned NOT NULL,
  `je_notes` text COLLATE utf8_bin,
  `je_date_offered` date DEFAULT NULL,
  `je_rc_id` tinyint(3) DEFAULT NULL,
  `je_attended` tinyint(1) DEFAULT NULL,
  `je_published` tinyint(1) unsigned DEFAULT '0',
  `je_added_a_id` int(11) DEFAULT NULL,
  `je_updated_a_id` int(11) DEFAULT NULL,
  `je_created_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`je_id`),
  KEY `e_j_id` (`je_j_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table journey_family
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_family`;

CREATE TABLE `journey_family` (
  `jf_j_id` mediumint(8) unsigned NOT NULL,
  `jf_family_members` text COLLATE utf8_bin NOT NULL,
  `jf_notes` text COLLATE utf8_bin,
  PRIMARY KEY (`jf_j_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table journey_modalities
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_modalities`;

CREATE TABLE `journey_modalities` (
  `mod_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mod_j_id` mediumint(8) unsigned NOT NULL,
  `mod_cpdate` date DEFAULT NULL,
  `mod_treatment` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `mod_refdate` date DEFAULT NULL,
  `mod_firstapptdate` date DEFAULT NULL,
  `mod_intsetting` tinyint(2) unsigned NOT NULL DEFAULT '6',
  `mod_start` date DEFAULT NULL,
  `mod_end` date DEFAULT NULL,
  `mod_exit` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`mod_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table journey_notes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_notes`;

CREATE TABLE `journey_notes` (
  `jn_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `jn_j_id` mediumint(8) unsigned NOT NULL,
  `jn_date` date NOT NULL,
  `jn_rc_id` tinyint(3) unsigned NOT NULL,
  `jn_notes` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`jn_id`),
  KEY `jn_j_id` (`jn_j_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table journey_offending
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journey_offending`;

CREATE TABLE `journey_offending` (
  `jo_j_id` mediumint(8) unsigned NOT NULL,
  `jo_shop_theft` tinyint(1) DEFAULT NULL,
  `jo_drug_selling` tinyint(1) DEFAULT NULL,
  `jo_other_theft` tinyint(1) DEFAULT NULL,
  `jo_assault_violence` tinyint(1) DEFAULT NULL,
  `jo_notes` text COLLATE utf8_bin,
  PRIMARY KEY (`jo_j_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table journeys
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journeys`;

CREATE TABLE `journeys` (
  `j_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `j_c_id` int(10) unsigned NOT NULL COMMENT 'Client',
  `j_rc_id` tinyint(3) unsigned DEFAULT NULL COMMENT 'Recovery coach',
  `j_type` enum('C','F') COLLATE utf8_bin NOT NULL DEFAULT 'C',
  `j_datetime_created` datetime NOT NULL,
  `j_datetime_last_update` datetime NOT NULL,
  `j_last_update_by` tinyint(3) unsigned NOT NULL COMMENT 'Admin',
  `j_date_of_referral` date DEFAULT NULL,
  `j_date_of_triage` date DEFAULT NULL,
  `j_date_first_appointment` date DEFAULT NULL,
  `j_date_first_appointment_offered` date DEFAULT NULL,
  `j_date_first_assessment` date DEFAULT NULL,
  `j_date_last_assessment` date DEFAULT NULL,
  `j_family_or_carer_involved` tinyint(1) DEFAULT NULL,
  `j_status` int(1) NOT NULL DEFAULT '3',
  `j_published` tinyint(1) NOT NULL DEFAULT '0',
  `j_tier` enum('Brief intervention','Tier 2','Tier 3') COLLATE utf8_bin DEFAULT NULL,
  `j_closed_date` date DEFAULT NULL,
  `j_ndtms_valid` tinyint(1) NOT NULL DEFAULT '2',
  PRIMARY KEY (`j_id`),
  KEY `j_c_id` (`j_c_id`),
  KEY `j_rc_id` (`j_rc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table journeys_cache
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journeys_cache`;

CREATE TABLE `journeys_cache` (
  `jc_j_id` mediumint(8) unsigned NOT NULL,
  `jc_access_support` tinyint(1) unsigned DEFAULT '0' COMMENT 'Accessing support groups / drop ins',
  `jc_access_respite` tinyint(1) unsigned DEFAULT '0' COMMENT 'Carers accessing respite provision',
  `jc_attend_parent` tinyint(1) unsigned DEFAULT '0' COMMENT 'Attending Parent Factor training',
  `jc_attend_freedom` tinyint(1) unsigned DEFAULT '0' COMMENT 'Attending Freedom Programme',
  `jc_improve_psych` tinyint(1) unsigned DEFAULT '0' COMMENT 'Reported improvement in psychological...',
  `jc_improve_child` tinyint(1) unsigned DEFAULT '0' COMMENT 'Reported improvement in child safety',
  `jc_improve_social` tinyint(1) unsigned DEFAULT '0' COMMENT 'Reported improvement in social interactions',
  `jc_interval_1to1` smallint(5) unsigned DEFAULT '0' COMMENT 'Interval in days between 1-to-1 appointments',
  `jc_date_ass1` date DEFAULT NULL COMMENT 'Date of first assessment',
  `jc_date_csop1` date DEFAULT NULL COMMENT 'Date of first CSOP',
  `jc_csop_total` tinyint(1) unsigned DEFAULT '0' COMMENT 'Total CSOPs taken',
  `jc_csop_interval` smallint(5) unsigned DEFAULT '0' COMMENT 'Interval in days between CSOPs',
  `jc_last_event` datetime DEFAULT NULL,
  PRIMARY KEY (`jc_j_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table journeys_info
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journeys_info`;

CREATE TABLE `journeys_info` (
  `ji_j_id` mediumint(8) unsigned NOT NULL,
  `ji_rs_id` tinyint(4) DEFAULT NULL COMMENT 'Referral source',
  `ji_referrers_name` varchar(32) COLLATE utf8_bin DEFAULT NULL,
  `ji_referrers_tel` varchar(16) COLLATE utf8_bin DEFAULT NULL,
  `ji_date_referral_received` date DEFAULT NULL,
  `ji_referral_received_by` tinyint(3) unsigned DEFAULT NULL COMMENT 'Admin',
  `ji_date_rc_allocated` date DEFAULT NULL,
  `ji_date_last_appt` date DEFAULT NULL COMMENT 'Date of last appointment',
  `ji_rc_allocated_by` tinyint(3) unsigned DEFAULT NULL COMMENT 'Admin',
  `ji_previously_treated` tinyint(1) DEFAULT NULL,
  `ji_medication` text COLLATE utf8_bin,
  `ji_summary_of_needs` text COLLATE utf8_bin,
  `ji_additional_information` text COLLATE utf8_bin,
  `ji_exit_status` char(1) COLLATE utf8_bin DEFAULT NULL,
  `ji_discharge_reason` tinyint(4) DEFAULT NULL,
  `ji_triage_completed_by` tinyint(3) DEFAULT NULL,
  `ji_flagged_as_risk` tinyint(1) DEFAULT NULL,
  `ji_flagged_risk_summary` text COLLATE utf8_bin,
  `ji_date_of_drug_treatment` date DEFAULT NULL,
  `ji_csop_last` date DEFAULT NULL,
  `ji_csop_due` date DEFAULT NULL,
  `ji_top_last` date DEFAULT NULL,
  `ji_top_due` date DEFAULT NULL,
  PRIMARY KEY (`ji_j_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table journeys_uploads
# ------------------------------------------------------------

DROP TABLE IF EXISTS `journeys_uploads`;

CREATE TABLE `journeys_uploads` (
  `ju_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ju_j_id` int(10) unsigned NOT NULL,
  `ju_name` varchar(64) NOT NULL,
  `ju_src` varchar(128) NOT NULL,
  `ju_size` float NOT NULL,
  PRIMARY KEY (`ju_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `log`;

CREATE TABLE `log` (
  `l_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `l_a_id` tinyint(3) unsigned NOT NULL,
  `l_datetime` datetime NOT NULL,
  `l_description` varchar(255) NOT NULL,
  `l_client_name` varchar(255) DEFAULT NULL,
  `l_journey` int(11) DEFAULT NULL,
  `l_client` int(11) DEFAULT NULL,
  PRIMARY KEY (`l_id`),
  KEY `l_a_id` (`l_a_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table logins
# ------------------------------------------------------------

DROP TABLE IF EXISTS `logins`;

CREATE TABLE `logins` (
  `ip` varchar(16) NOT NULL,
  `attempts` tinyint(1) unsigned NOT NULL,
  `datetime_set` datetime NOT NULL,
  PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table mail_merge_aliases
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mail_merge_aliases`;

CREATE TABLE `mail_merge_aliases` (
  `alias_id` int(11) NOT NULL AUTO_INCREMENT,
  `field_names` varchar(255) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `is_multi_line` tinyint(1) NOT NULL,
  PRIMARY KEY (`alias_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table mail_merge_bgfiles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mail_merge_bgfiles`;

CREATE TABLE `mail_merge_bgfiles` (
  `mmf_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mmf_src` varchar(128) NOT NULL,
  `mmf_size` float NOT NULL,
  PRIMARY KEY (`mmf_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table mail_merges
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mail_merges`;

CREATE TABLE `mail_merges` (
  `mm_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `mm_title` varchar(255) COLLATE utf8_bin NOT NULL,
  `mm_body` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`mm_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table messages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `messages`;

CREATE TABLE `messages` (
  `m_id` int(11) NOT NULL AUTO_INCREMENT,
  `m_to_admin` int(11) NOT NULL,
  `m_from_admin` int(11) NOT NULL,
  `m_type` varchar(255) DEFAULT NULL,
  `m_status` enum('Active','Read','Deleted') NOT NULL,
  `m_sent_date` datetime NOT NULL,
  `m_text` varchar(255) NOT NULL,
  `m_j_id` int(11) DEFAULT NULL,
  `m_link` varchar(255) DEFAULT NULL,
  `m_link_text` varchar(255) DEFAULT NULL,
  `m_cat_name` varchar(255) DEFAULT NULL,
  `m_cat_value` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`m_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table options
# ------------------------------------------------------------

DROP TABLE IF EXISTS `options`;

CREATE TABLE `options` (
  `o_key` varchar(100) NOT NULL,
  `o_value` text NOT NULL,
  PRIMARY KEY (`o_key`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table patch_history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `patch_history`;



# Dump of table postcodes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `postcodes`;

CREATE TABLE `postcodes` (
  `pc_postcode` varchar(8) COLLATE utf8_bin NOT NULL,
  `pc_lat` float(10,6) NOT NULL,
  `pc_lng` float(10,6) NOT NULL,
  PRIMARY KEY (`pc_postcode`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table recovery_coaches
# ------------------------------------------------------------

DROP TABLE IF EXISTS `recovery_coaches`;

CREATE TABLE `recovery_coaches` (
  `rc_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `rc_jr_id` tinyint(3) unsigned DEFAULT NULL,
  `rc_name` varchar(32) COLLATE utf8_bin NOT NULL,
  `rc_active` tinyint(1) NOT NULL DEFAULT '1',
  `rc_family_worker` tinyint(1) unsigned DEFAULT '2',
  PRIMARY KEY (`rc_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table referral_sources
# ------------------------------------------------------------

DROP TABLE IF EXISTS `referral_sources`;

CREATE TABLE `referral_sources` (
  `rs_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `rs_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `rs_type` tinyint(4) NOT NULL,
  `rs_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`rs_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table risk_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `risk_types`;

CREATE TABLE `risk_types` (
  `rt_id` tinyint(4) NOT NULL AUTO_INCREMENT,
  `rt_name` varchar(64) COLLATE utf8_bin NOT NULL,
  `rt_group` enum('Physical','Psychological','Social','Violence/Aggression') COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`rt_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table staff
# ------------------------------------------------------------

DROP TABLE IF EXISTS `staff`;

CREATE TABLE `staff` (
  `s_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `s_name` varchar(64) NOT NULL,
  `s_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`s_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



# Dump of table support_group_clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `support_group_clients`;

CREATE TABLE `support_group_clients` (
  `spc_sps_id` int(10) unsigned NOT NULL,
  `spc_c_id` int(10) unsigned NOT NULL,
  `spc_attended` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`spc_sps_id`,`spc_c_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table support_group_sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `support_group_sessions`;

CREATE TABLE `support_group_sessions` (
  `sps_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sps_sp_id` tinyint(3) unsigned NOT NULL,
  `sps_datetime` datetime NOT NULL,
  `sps_location` varchar(64) COLLATE utf8_bin NOT NULL,
  `sps_notes` text COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`sps_id`),
  KEY `sps_sp_id` (`sps_sp_id`),
  KEY `datetime` (`sps_sp_id`,`sps_datetime`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table support_groups
# ------------------------------------------------------------

DROP TABLE IF EXISTS `support_groups`;

CREATE TABLE `support_groups` (
  `sp_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `sp_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `sp_description` text COLLATE utf8_bin NOT NULL,
  `sp_default_day` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') COLLATE utf8_bin DEFAULT NULL,
  `sp_default_time` time DEFAULT NULL,
  PRIMARY KEY (`sp_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
