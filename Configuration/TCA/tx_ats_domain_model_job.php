<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_job',
		'label' => 'job_number',
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
		'iconfile' => 'EXT:ats/Resources/Public/Icons/tx_ats_domain_model_job.gif',
        'requestUpdate' => 'career, location'
	],
	'interface' => [
		'showRecordFieldList' => 'sys_language_uid,  l10n_parent,  l10n_diffsource,  hidden,  starttime,  endtime, job_number, title, description, description_after_link, enable_form_link, career, internal, deactivated, location, user_pa, department, officials, contributors, contact, deadline_email_disabled, deadline_email, organization_unit'
	],
	'types' => [
		'1' => ['showitem' =>
            'hidden, --palette--;;1, deactivated, job_number, title, description, description_after_link, enable_form_link, career,
            internal, location, user_pa, department, officials, contributors, contact, deadline_email_disabled, deadline_email, sys_language_uid, l18n_parent'],
	],
	'palettes' => [
        '1' => ['showitem' => 'starttime,endtime'],
        '2' => ['showitem' => 'templatefile,--linebreak--,required_fields,--linebreak--,org_unit']
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
                'renderType' => 'selectMultipleSideBySide',
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

	]
];
