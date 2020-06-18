<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_languageskill',
		'label' => 'uid',
        'label_userFunc' => \PAGEmachine\Ats\Userfuncs\LanguageSkill::class . '->getTitle',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 0,
        'hideTable' => 1,
		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden'
		],
		'searchFields' => '',
		'iconfile' => 'EXT:ats/Resources/Public/Icons/tx_ats_domain_model_languageskill.gif'
	],
	'interface' => [
		'showRecordFieldList' => ''
	],
	'types' => [
		'1' => ['showitem' =>
            ''],
	],
	'palettes' => [
	],
	'columns' => [
        'language' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_languageskill.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingleBox',
                'foreign_table' => 'static_languages',
                'foreign_table_where' => ' ORDER BY static_languages.lg_name_en ASC'
            ],
        ],
        'level' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_languageskill.level',
        	'config' => [
				'type' => 'passthrough'
        	]
        ],
        'application' => [
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_ats_domain_model_application'
            ]
        ],
        'text_language' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_languageskill.text_language',
            'config' => [
                'type' => 'input'
            ],
        ],
	]
];
