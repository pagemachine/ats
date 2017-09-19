<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_history',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,
        'delete' => 'deleted',
        'searchFields' => '',
        'iconfile' => 'EXT:ats/Resources/Public/Icons/tx_ats_domain_model_history.gif'
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
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'foreign_table' => 'tx_ats_domain_model_application'
            ]
        ],
        'subject' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'details' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'history_data' => [
            'config' => [
                'type' => 'input'
            ]
        ],
        'user' => [
            'config' => [
                'type' => 'input'
            ]
        ],
    ]
];
