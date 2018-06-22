#
# Table structure for table 'tx_ats_domain_model_job'
#
CREATE TABLE tx_ats_domain_model_job (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	job_number varchar(255) DEFAULT '' NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	description text NOT NULL,
	description_after_link text NOT NULL,
	career varchar(15) DEFAULT '' NOT NULL,
	internal varchar(4) DEFAULT '' NOT NULL,
	location varchar(15) DEFAULT '' NOT NULL,
	user_pa varchar(255) DEFAULT '' NOT NULL,
	department varchar(255) DEFAULT '' NOT NULL,
	officials varchar(255) DEFAULT '' NOT NULL,
	contributors varchar(255) DEFAULT '' NOT NULL,
	organization_unit varchar(20) DEFAULT '' NOT NULL,
	contact text NOT NULL,
	deadline_email_disabled tinyint(3) DEFAULT '0' NOT NULL,
	deadline_email int(11) DEFAULT '0' NOT NULL,
	deactivated tinyint(3) DEFAULT '0' NOT NULL,
	enable_form_link tinyint(1) DEFAULT '0' NOT NULL,
	media int(11) unsigned DEFAULT '0' NOT NULL,

	job_title varchar(255) DEFAULT '' NOT NULL,
	base_salary float(23) DEFAULT '0' NOT NULL,
	base_salary_currency int(11) DEFAULT '0' NOT NULL,
	base_salary_unit varchar(255) DEFAULT '' NOT NULL,
	education_requirements text NOT NULL,
	employment_type varchar(255) DEFAULT '' NOT NULL,
	experience_requirements varchar(255) DEFAULT '' NOT NULL,
	override_global_hiring_organization tinyint(4) DEFAULT '0' NOT NULL,
	hiring_organization varchar(255) DEFAULT '' NOT NULL,
	incentive_compensation text NOT NULL,
	industry varchar(255) DEFAULT '' NOT NULL,
	job_benefits text NOT NULL,
	override_global_location tinyint(4) DEFAULT '0' NOT NULL,
	job_location_address_country int(11) DEFAULT '0' NOT NULL,
	job_location_address_locality varchar(255) DEFAULT '' NOT NULL,
	job_location_address_region int(11) DEFAULT '0' NOT NULL,
	job_location_address_postal_code varchar(255) DEFAULT '' NOT NULL,
	job_location_address_street_address varchar(255) DEFAULT '' NOT NULL,
	occupational_category varchar(255) DEFAULT '' NOT NULL,
	qualifications text NOT NULL,
	responsibilities text NOT NULL,
	skills text NOT NULL,
	special_commitments text NOT NULL,
	work_hours varchar(255) DEFAULT '' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 	KEY language (l10n_parent,sys_language_uid)

);


#
# Table structure for table 'be_groups'
#
CREATE TABLE be_groups (
    tx_ats_location varchar(15) DEFAULT '' NOT NULL
);

CREATE TABLE be_users (
    tx_ats_email_signature text NOT NULL,
    tx_ats_pdf_signature text NOT NULL,
    tx_ats_contact_print text NOT NULL
);

#
# Table structure for table 'tx_ats_application'
#
CREATE TABLE tx_ats_domain_model_application (
  uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	starttime int(11) unsigned DEFAULT '0' NOT NULL,
	endtime int(11) unsigned DEFAULT '0' NOT NULL,

	#changed to default int (timestamp) for better handling
  receiptdate int(11) unsigned DEFAULT '0' NOT NULL,
  pool tinyint(4) DEFAULT '0' NOT NULL,
  application_type tinyint(4) DEFAULT '0' NOT NULL,
  status tinyint(4) DEFAULT '0' NOT NULL,
  status_change int(11) DEFAULT '0' NOT NULL,
  job int(11) DEFAULT '0' NOT NULL,
  rating tinyint(4) DEFAULT '0' NOT NULL,
  rating_perso tinyint(4) DEFAULT '0' NOT NULL,
  aip tinyint(4) DEFAULT '0' NOT NULL,
  invited tinyint(4) DEFAULT '0' NOT NULL,
  opr tinyint(4) DEFAULT '0' NOT NULL,
  anonym tinyint(4) DEFAULT '0' NOT NULL,

  vocational_training_completed tinyint(4) DEFAULT '0' NOT NULL,
  privacy_policy tinyint(4) DEFAULT '0' NOT NULL,
  title varchar(40) DEFAULT '' NOT NULL,
  salutation tinyint(4) DEFAULT '0' NOT NULL,
  firstname varchar(50) DEFAULT '' NOT NULL,
  surname varchar(50) DEFAULT '' NOT NULL,

  birthday date DEFAULT NULL,
  disability tinyint(4) DEFAULT '0' NOT NULL,
  nationality varchar(10) DEFAULT '' NOT NULL,
  street tinytext NOT NULL,
  zipcode varchar(10) DEFAULT '' NOT NULL,
  city varchar(50) DEFAULT '' NOT NULL,
  country varchar(10) DEFAULT '' NOT NULL,
  email varchar(80) DEFAULT '' NOT NULL,
  phone varchar(20) DEFAULT '' NOT NULL,
  mobile varchar(20) DEFAULT '' NOT NULL,
  employed tinyint(4) DEFAULT '0' NOT NULL,
  school_qualification tinyint(4) DEFAULT '0' NOT NULL,
  professional_qualification varchar(100) DEFAULT '' NOT NULL,
  professional_qualification_final_grade varchar(20) DEFAULT '' NOT NULL,
  academic_degree varchar(100) DEFAULT '' NOT NULL,
  academic_degree_final_grade varchar(50) DEFAULT '' NOT NULL,
  doctoral_degree tinytext NOT NULL,
  doctoral_degree_final_grade varchar(50) DEFAULT '' NOT NULL,
  previous_knowledge tinytext NOT NULL,
  it_knowledge tinytext NOT NULL,
  target_graduation tinyint(4) DEFAULT '0' NOT NULL,
  graduation_completed varchar(4) DEFAULT '' NOT NULL,
  maths_grade varchar(1) DEFAULT '' NOT NULL,
  physics_grade varchar(1) DEFAULT '' NOT NULL,
  chemistry_grade varchar(1) DEFAULT '' NOT NULL,
  german_grade varchar(1) DEFAULT '' NOT NULL,
  english_grade varchar(1) DEFAULT '' NOT NULL,
  art_grade varchar(1) DEFAULT '' NOT NULL,
  comment tinytext NOT NULL,
  referrer tinyint(4) DEFAULT '0' NOT NULL,
  communication_channel tinyint(4) DEFAULT '0' NOT NULL,
  forward_to_departments tinyint(4) DEFAULT '0' NOT NULL,
  files int(11) unsigned DEFAULT '0',

  user int(11) DEFAULT '0' NOT NULL,
  language_skills int(11) unsigned DEFAULT '0' NOT NULL,
  notes int(11) unsigned DEFAULT '0' NOT NULL,
  history int(11) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l10n_parent int(11) DEFAULT '0' NOT NULL,
	l10n_diffsource mediumblob,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid),
 	KEY language (l10n_parent,sys_language_uid)
);

#
# Table structure for tx_ats_domain_model_languageskill (former tx_jobmodul_application_mm_language)
#
CREATE TABLE tx_ats_domain_model_languageskill (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	level int(11) unsigned DEFAULT '0' NOT NULL,
	language int(11) DEFAULT '0' NOT NULL,
	text_language varchar(50) DEFAULT '' NOT NULL,
	application int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for tx_ats_domain_model_note (former tx_jobmodul_note)
#
CREATE TABLE tx_ats_domain_model_note (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	application int(11) DEFAULT '0' NOT NULL,
	user int(11) DEFAULT '0' NOT NULL,
	subject varchar(255) DEFAULT '0' NOT NULL,
	details text NOT NULL,
	is_internal int(1) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for tx_ats_domain_model_texttemplate
#
CREATE TABLE tx_ats_domain_model_texttemplate (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	type tinyint(4) DEFAULT '0' NOT NULL,
	texttemplate text NOT NULL,
	title varchar(255) DEFAULT '' NOT NULL,
	subject varchar(255) DEFAULT '' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(255) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage int(11) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3ver_move_id int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);

#
# Table structure for tx_ats_domain_model_history
#
CREATE TABLE tx_ats_domain_model_history (

	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,

	application int(11) DEFAULT '0' NOT NULL,
  subject varchar(255) DEFAULT '' NOT NULL,
  details mediumtext NOT NULL,
  history_data mediumtext NOT NULL,
  user int(11) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)

);
