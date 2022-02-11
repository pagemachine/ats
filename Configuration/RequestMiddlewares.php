<?php
if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger('10.0') > \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_branch)) {
    return;
}

return [
    'frontend' => [
        'pagemachine/ats/backend-user-authentication' => [
            'target' => \PAGEmachine\Ats\Middleware\AtsBackendUserAuthenticator::class,
            'after' => [
                'typo3/cms-core/normalized-params-attribute',
            ],
            'before' => [
                'typo3/cms-frontend/eid'
            ],
        ],
        'pagemachine/ats/authentication' => [
            'target' => \PAGEmachine\Ats\Middleware\AtsFrontendUserAuthenticator::class,
            'after' => [
                'pagemachine/ats/backend-user-authentication',
            ],
            'before' => [
                'typo3/cms-frontend/eid'
            ],
        ],
        'pagemachine/ats/request' => [
            'target' => \PAGEmachine\Ats\Middleware\AtsRequest::class,
            'after' => [
                'pagemachine/ats/authentication',
            ],
            'before' => [
                'typo3/cms-frontend/eid'
            ],
        ],
    ],
];
