<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'ATS: Applicant Tracking System',
    'description' => 'Extension for Job Application Management',
    'category' => 'plugin',
    'author' => 'Saskia Schreiber, Stefan Schütt',
    'author_email' => 'sschreiber@pagemachine.de, sschuett@pagemachine.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '1.0.2',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.7.99',
            'static_info_tables' => '6.3.0-6.99.99',
        ],
        'suggests' => [
            'hairu' => '2.0.0-0.0.0',
        ],
    ],
    'createDirs' => 'fileadmin/tx_ats',
];
