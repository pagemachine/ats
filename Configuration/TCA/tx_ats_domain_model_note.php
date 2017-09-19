<?php
return [
	'ctrl' => [
		'title'	=> 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_note',
		'label' => 'uid',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,
		'versioningWS' => 0,

		'delete' => 'deleted',
		'enablecolumns' => [
			'disabled' => 'hidden'
		],
		'searchFields' => '',
		'iconfile' => 'EXT:ats/Resources/Public/Icons/tx_ats_domain_model_note.gif'
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
        'application' => [
        	'config' => [
				'type' => 'passthrough'
        	]
        ],
        'user' => [
        	'config' => [
        		'type' => 'passthrough',
                'foreign_table' => 'be_users'
        	]
        ],
		'subject' => [
			'config' => [
				'type' => 'passthrough'
			]
		],
		'details' => [
			'config' => [
				'type' => 'passthrough'
			]
		],
		'is_internal' => [
			'config' => [
				'type' => 'passthrough'
			]
		]
	]
];
