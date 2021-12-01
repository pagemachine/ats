<?php
declare(strict_types = 1);

return [
    \PAGEmachine\Ats\Domain\Model\FileReference::class => [
        'tableName' => 'sys_file_reference',
        'properties' => [
            'originalFileIdentifier' => [
                'fieldName' => 'uid_local'
            ],
        ],
    ],
    \PAGEmachine\Ats\Domain\Model\Job::class => [
        'properties' => [
            'creationDate' => [
                'fieldName' => 'crdate'
            ],
        ],
    ],
    \PAGEmachine\Ats\Domain\Model\Application::class => [
        'properties' => [
            'creationDate' => [
                'fieldName' => 'crdate'
            ],
        ],
    ],
    \PAGEmachine\Ats\Domain\Model\Note::class => [
        'properties' => [
            'creationDate' => [
                'fieldName' => 'crdate'
            ],
        ],
    ],
    \PAGEmachine\Ats\Domain\Model\History::class => [
        'properties' => [
            'creationDate' => [
                'fieldName' => 'crdate'
            ],
        ],
    ],
    \PAGEmachine\Ats\Domain\Model\AbstractApplication::class => [
        'tableName' => 'tx_ats_domain_model_application',
        'recordType' => \PAGEmachine\Ats\Domain\Model\AbstractApplication::class,
    ],
    \PAGEmachine\Ats\Domain\Model\ApplicationA::class => [
        'tableName' => 'tx_ats_domain_model_application',
        'recordType' => \PAGEmachine\Ats\Domain\Model\AbstractApplication::class,
    ],
    \PAGEmachine\Ats\Domain\Model\ApplicationB::class => [
        'tableName' => 'tx_ats_domain_model_application',
        'recordType' => \PAGEmachine\Ats\Domain\Model\AbstractApplication::class,
    ],
    \PAGEmachine\Ats\Domain\Model\ApplicationC::class => [
        'tableName' => 'tx_ats_domain_model_application',
        'recordType' => \PAGEmachine\Ats\Domain\Model\AbstractApplication::class,
    ],
    \PAGEmachine\Ats\Domain\Model\ApplicationD::class => [
        'tableName' => 'tx_ats_domain_model_application',
        'recordType' => \PAGEmachine\Ats\Domain\Model\AbstractApplication::class,
    ],
    \PAGEmachine\Ats\Domain\Model\ApplicationE::class => [
        'tableName' => 'tx_ats_domain_model_application',
        'recordType' => \PAGEmachine\Ats\Domain\Model\AbstractApplication::class,
    ],
    \PAGEmachine\Ats\Domain\Model\ApplicationSimple::class => [
        'tableName' => 'tx_ats_domain_model_application',
        'recordType' => \PAGEmachine\Ats\Domain\Model\AbstractApplication::class,
    ],
];
