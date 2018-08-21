<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'ATS: Applicant Tracking System',
    'description' => 'Highly customizable enterprise application tracking system based on Extbase & Fluid. Provides management of job offers and job applications, allowing for complex job application workflows involving numerous roles as they are required in environments of universities as well as private and public companies.',
    'category' => 'plugin',
    'author' => 'Saskia Schreiber, Stefan Schütt',
    'author_email' => 'sschreiber@pagemachine.de, sschuett@pagemachine.de',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '1.9.0',
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
