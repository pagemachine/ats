<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job',
		'label' => 'job_number',
        'label_alt' => 'title',
        'label_alt_force' => 1,
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		],
		'searchFields' => 'job_number, title',
		'default_sortby' => 'crdate DESC',
		'iconfile' => 'EXT:ats/Resources/Public/Icons/tx_ats_domain_model_job.svg',
        'requestUpdate' => 'career, location, override_global_hiring_organization, override_global_location'
	],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid,  l10n_parent,  l10n_diffsource,  hidden,  starttime,  endtime, job_number, title, description, description_after_link, enable_form_link, career, internal, deactivated, location, user_pa, department, officials, contributors, contact, deadline_email_disabled, deadline_email, organization_unit, job_title, base_salary, base_salary_currency, base_salary_unit, education_requirements, employment_type, experience_requirements, override_global_hiring_organization, hiring_organization, incentive_compensation, job_benefits, industry, override_global_location, job_location_address_country, job_location_address_region, job_location_address_locality, job_location_address_postal_code, job_location_address_street_address, occupational_category, qualifications, responsibilities, skills, special_commitments, work_hours'
	],
	'types' => [
		'1' => ['showitem' =>
            'hidden, --palette--;;1, deactivated, job_number, title, description, description_after_link, enable_form_link, career,
            internal, location, user_pa, department, officials, contributors, contact, deadline_email_disabled, deadline_email, sys_language_uid, l18n_parent, --div--;LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.div.json_ld, job_title, industry, occupational_category, employment_type, --palette--;LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary.palette;3, work_hours, --palette--;LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.hiring_organization.palette;4, --palette--;LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_location.palette;5, education_requirements, experience_requirements, incentive_compensation, job_benefits, qualifications, responsibilities, skills, special_commitments, '],
	],
	'palettes' => [
        '1' => ['showitem' => 'starttime,endtime'],
        '2' => ['showitem' => 'templatefile,--linebreak--,required_fields,--linebreak--,org_unit'],
        '3' => ['showitem' => 'base_salary, base_salary_currency, base_salary_unit'],
        '4' => ['showitem' => 'override_global_hiring_organization, --linebreak--, hiring_organization'],
        '5' => ['showitem' => 'override_global_location, --linebreak--, job_location_address_country, job_location_address_region, --linebreak--, job_location_address_postal_code, job_location_address_street_address, job_location_address_locality'],
	],
	'columns' => [

		'sys_language_uid' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => [
					['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
					['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0]
				],
			],
		],
		'l10n_parent' => [
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
			'config' => [
				'type' => 'select',
				'renderType' => 'selectSingle',
				'items' => [
					['', 0],
				],
				'foreign_table' => 'tx_ats_domain_model_job',
				'foreign_table_where' => 'AND tx_ats_domain_model_job.pid=###CURRENT_PID### AND tx_ats_domain_model_job.sys_language_uid IN (-1,0)',
			],
		],
		'l10n_diffsource' => [
			'config' => [
				'type' => 'passthrough',
			],
		],

		't3ver_label' => [
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
			'config' => [
				'type' => 'input',
				'size' => 30,
				'max' => 255,
			]
		],

		'hidden' => [
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
			'config' => [
				'type' => 'check',
			],
		],
        'deactivated' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.deactivated',
            'config' => [
                'type' => 'check',
            ]
        ],
		'starttime' => [
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
			],
		],
		'endtime' => [
			'exclude' => 1,
			'l10n_mode' => 'mergeIfNotBlank',
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
			'config' => [
				'type' => 'input',
				'size' => 13,
				'max' => 20,
				'eval' => 'datetime',
				'checkbox' => 0,
				'default' => 0,
			],
		],
        'job_number' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_number',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required'
            ],
        ],
        // Former field "job"
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required'
            ],
        ],
        // Former field "job_desc"
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.description',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'eval' => 'required',
            ],
            'defaultExtras' => 'richtext[]:rte_transform[mode=ts]'
        ],
         'description_after_link' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.description_after_link',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5'
            ],
            'defaultExtras' => 'richtext[]:rte_transform[mode=ts]'
        ],
        'contact' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.contact',
            'displayCond' => 'FIELD:career:!=:Berufung',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'eval' => 'required',
            ]
        ],
        'career' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.career',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.career.I.1', 'h. D.'],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.career.I.2', 'g. D.'],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.career.I.3', 'm. D.'],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.career.I.4', 'Ausbildung'],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.career.I.5', 'Berufung']
                ],
                'minitems' => 1,
                'maxitems' => 1,
            ]
        ],

        'internal' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.internal',
            'config' => [
                'type' => 'radio',
                'items' => [
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.internal.I.0', 'yes'],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.internal.I.1', 'no'],
                ],
                'default' => 'no',
                'eval' => 'required',
            ]
        ],
        'location' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.location',
            'config' => [
                'type' => 'radio',
                'items' => [
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:be_groups.tx_ats_location.I.none', ''],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.location.I.0', 'Zentrale'],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.location.I.1', 'Niederlassung'],
                ],
                'default' => 'Zentrale',
                'eval' => 'required',
            ]
        ],
        'user_pa' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.user_pa',
            'displayCond' => 'FIELD:career:!=:Berufung',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'items' => [],
                'itemsProcFunc' => \PAGEmachine\Ats\TCA\FormHelper::class . '->findUserPa',
                'size' => 5,
                'minitems' => 1,
                'maxitems' => 10,
                'suppress_icons' => '1'
            ]
        ],
        //former group_fa
        'department' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.department',
            'displayCond' => 'FIELD:career:!=:Berufung',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'itemsProcFunc' => \PAGEmachine\Ats\TCA\FormHelper::class . '->findDepartment',
                'size' => 5,
                'minitems' => 1,
                'maxitems' => 10,
                'suppress_icons' => '1'
            ]
        ],
        //former "user_cm_officials"
        'officials' => [ // Berufung Sachbearbeiter
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.officials',
            'displayCond' => 'FIELD:career:=:Berufung',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'items' => [],
                'itemsProcFunc' => \PAGEmachine\Ats\TCA\FormHelper::class . '->findOfficials',
                'size' => 5,
                'minitems' => 1,
                'maxitems' => 10,
                'suppress_icons' => '1'
            ]
        ],
        //former "contributors"
        'contributors' => [ // Berufung Mitwirkende
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.contributors',
            'displayCond' => 'FIELD:career:=:Berufung',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'items' => [],
                // TBA
                //'itemsProcFunc' => 'tx_jobmodul_TCAform->userCmContributors',
                'size' => 5,
                'minitems' => 1,
                'maxitems' => 10,
                'suppress_icons' => '1'
            ]
        ],
        'deadline_email_disabled' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.deadline_email_disabled',
            'displayCond' => 'FIELD:career:!=:Berufung',
            'config' => [
                'type'     => 'check',
                'default' => '0'
            ]
        ],
        'deadline_email' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.deadline_email',
            'displayCond' => 'FIELD:career:!=:Berufung',
            'config' => [
                'type'     => 'input',
                'size'     => '11',
                'max'      => '20',
                'eval'     => 'datetime',
                'default'  => '0',
                'checkbox' => '0',
                'readOnly' => '1'
            ]
        ],

        'organization_unit' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.organization_unit',
            'config' => [
                'type' => 'input',
                'size' => '5',
                'max'      => '20',
                'eval'     => 'trim',
            ]
        ],
        'enable_form_link' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.enable_form_link',
            'config' => [
                'type'     => 'check',
                'default' => '1'
            ]
        ],
        'job_title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_title',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'base_salary' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary',
            'config' => [
                'type' => 'input',
                'size' => 6,
                'eval' => 'double2',
            ],
        ],
        'base_salary_currency' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary_currency',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary_currency.default', 0],
                ],
                'foreign_table' => 'static_currencies',
                'foreign_table_where' => 'ORDER BY static_currencies.cu_name_en',
                'default' => 49,
            ],
        ],
        'base_salary_unit' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary_unit',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary_unit.default', ''],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary_unit.HOUR', 'HOUR'],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary_unit.DAY', 'DAY'],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary_unit.WEEK', 'WEEK'],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary_unit.MONTH', 'MONTH'],
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.base_salary_unit.YEAR', 'YEAR'],
                ],
                'default' => 'YEAR',
            ],
        ],
        'education_requirements' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.education_requirements',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'enableRichtext' => true
            ],
        ],
        'employment_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.employment_type',
            'config' => [
                'type' => 'input',
                'placeholder' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.employment_type.placeholder',
                'size' => 30,
            ],
        ],
        'experience_requirements' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.experience_requirements',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'enableRichtext' => true
            ],
        ],
        'override_global_hiring_organization' => [
            'exclude' => 1,
            'label' => '',
            'config' => [
                'type' => 'check',
                'items' => [
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.override_global_hiring_organization', 1],
                ]
            ],
        ],
        'hiring_organization' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.hiring_organization',
            'displayCond' => 'FIELD:override_global_hiring_organization:REQ:true',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'incentive_compensation' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.incentive_compensation',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'enableRichtext' => true
            ],
        ],
        'job_benefits' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_benefits',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'enableRichtext' => true
            ],
        ],
        'industry' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.industry',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'override_global_location' => [
            'exclude' => 1,
            'label' => '',
            'config' => [
                'type' => 'check',
                'items' => [
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.override_global_location', 1],
                ],
            ],
        ],
        'job_location_address_country' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_location_address_country',
            'displayCond' => 'FIELD:override_global_location:REQ:true',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_location_address_country.default', 0],
                ],
                'foreign_table' => 'static_countries',
                'default' => 54,
            ],
        ],
        'job_location_address_region' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_location_address_region',
            'displayCond' => 'FIELD:override_global_location:REQ:true',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_location_address_region.default', 0],
                ],
                'foreign_table' => 'static_country_zones',
                'foreign_table_where' => ' AND static_country_zones.zn_country_uid = ###REC_FIELD_job_location_address_country###',
            ],
        ],
        'job_location_address_locality' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_location_address_locality',
            'displayCond' => 'FIELD:override_global_location:REQ:true',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'job_location_address_postal_code' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_location_address_postal_code',
            'displayCond' => 'FIELD:override_global_location:REQ:true',
            'config' => [
                'type' => 'input',
                'size' => 6,
            ],
        ],
        'job_location_address_street_address' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.job_location_address_street_address',
            'displayCond' => 'FIELD:override_global_location:REQ:true',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'occupational_category' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.occupational_category',
            'config' => [
                'type' => 'input',
                'size' => 30,
            ],
        ],
        'qualifications' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.qualifications',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true
            ],
        ],
        'responsibilities' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.responsibilities',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true
            ],
        ],
        'skills' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.skills',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true
            ],
        ],
        'special_commitments' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.special_commitments',
            'config' => [
                'type' => 'text',
                'enableRichtext' => true
            ],
        ],
        'work_hours' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.work_hours',
            'config' => [
                'type' => 'input',
                'placeholder' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job.work_hours.placeholder',
                'size' => 30,
            ],
        ],
	]
];
