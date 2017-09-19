<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_texttemplate',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,
        'versioningWS' => 2,
        'versioning_followPages' => TRUE,

        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'searchFields' => '',
        'iconfile' => 'EXT:ats/Resources/Public/Icons/tx_ats_domain_model_texttemplate.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, title, type'
    ],
    'types' => [
        '1' => ['showitem' =>
            'hidden, --palette--;;1, title, type, subject, texttemplate'],
    ],
    'palettes' => [
        '1' => ['showitem' => 'starttime,endtime'],
        '2' => ['showitem' => 'templatefile,--linebreak--,required_fields,--linebreak--,org_unit']
    ],
    'columns' => [
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_texttemplate.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required'
            ],
        ],
        'type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_texttemplate.type',
            'config' => [
                'type' => 'select',
                'items' => array(
                    array('', ''),
                    array('LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_texttemplate.type.1', '1'),
                    array('LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_texttemplate.type.2', '2'),
                    array('LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_texttemplate.type.3', '3'),
                    array('LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_texttemplate.type.4', '4'),
                    array('LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_texttemplate.type.5', '5'),
                ),
            ],
        ],
        'subject' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_texttemplate.subject',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required'
            ],
        ],
        'texttemplate' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:tx_ats_domain_model_texttemplate.texttemplate',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'required',
                'enableRichtext' => true
            ],
        ],

        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
    ]
];
