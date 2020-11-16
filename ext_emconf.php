<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'ATS: Applicant Tracking System',
    'description' => 'Highly customizable enterprise application tracking system based on Extbase & Fluid. Provides management of job offers and job applications, allowing for complex job application workflows involving numerous roles as they are required in environments of universities as well as private and public companies.',
    'category' => 'plugin',
    'author' => 'Clara Brocar, Saskia Schreiber, Stefan Schuett, Francisco Seipel',
    'author_email' => 'sschuett@pagemachine.de',
    'author_company' => 'Pagemachine AG',
    'state' => 'stable',
    'clearCacheOnLoad' => 0,
    'version' => '2.0.1',
    'constraints' => [
        'depends' => [
            'php' => '7.2.0-7.4.99',
            'typo3' => '9.5.0-9.5.99',
            'static_info_tables' => '6.7.0-6.99.99',
        ],
        'suggests' => [
            'hairu' => '2.0.0-0.0.0',
        ],
    ],
    'createDirs' => 'fileadmin/tx_ats',
];
