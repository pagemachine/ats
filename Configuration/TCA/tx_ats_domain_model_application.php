<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_application',
        'label' => 'uid',
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
        'searchFields' => '',
        'iconfile' => 'EXT:ats/Resources/Public/Icons/tx_ats_domain_model_application.gif'
    ],
    'types' => [
        '1' => ['showitem' =>
            ''],
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
                'foreign_table' => 'tx_ats_domain_model_application',
                'foreign_table_where' => 'AND tx_ats_domain_model_application.pid=###CURRENT_PID### AND tx_ats_domain_model_application.sys_language_uid IN (-1,0)',
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
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
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
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'receiptdate' => [
            'config' => [
                'type' => 'input'
            ],
        ],
        'pool' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'application_type' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'status' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'status_change' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'job' => [
            'l10n_mode' => 'exclude',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_ats_domain_model_job'

            ]
        ],
        'rating' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'rating_perso' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'aip' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'invited' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'opr' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'anonym' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'vocational_training_completed' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'privacy_policy' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'title' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'salutation' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'firstname' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'surname' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'birthday' => [
            'config' => [
                'type' => 'input',
                'eval' => 'date',
                'dbType' => 'date'
            ]
        ],
        'disability' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'nationality' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'street' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'zipcode' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'city' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'country' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'email' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'phone' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'mobile' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'employed' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'school_qualification' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'professional_qualification' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'professional_qualification_final_grade' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'academic_degree' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'academic_degree_final_grade' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'doctoral_degree' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'doctoral_degree_final_grade' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'previous_knowledge' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'it_knowledge' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'target_graduation' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'graduation_completed' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'maths_grade' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'physics_grade' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'chemistry_grade' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'german_grade' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'english_grade' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'art_grade' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'comment' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        //??
        'referrer' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'communication_channel' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'forward_to_departments' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'user' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'fe_users'
            ]
        ],
        'history' => [
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_ats_domain_model_history',
                'foreign_field' => 'application'
            ]
        ],
        'files' => [
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig('files', [
                'foreign_match_fields' => [
                    'fieldname' => 'files',
                    'tablenames' => 'tx_ats_domain_model_application',
                    'table_local' => 'sys_file',
                ]
            ], $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'])

        ],
        'language_skills' => [
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_ats_domain_model_languageskill',
                'foreign_field' => 'application'
            ]
        ],
        'notes' => [
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_ats_domain_model_note',
                'foreign_field' => 'application'
            ]
        ]
    ]
];
