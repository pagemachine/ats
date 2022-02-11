<?php
return [
    'ats:anonymizeapplications' => [
        'class' => \PAGEmachine\Ats\Command\AnonymizeApplicationsCommand::class,
    ],
    'ats:cleanupapplications' => [
        'class' => \PAGEmachine\Ats\Command\CleanupApplicationsCommand::class,
    ],
    'ats:cleanupusers' => [
        'class' => \PAGEmachine\Ats\Command\CleanupUsersCommand::class,
    ],
];
