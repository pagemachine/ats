<?php
$typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);

if ($typo3Version->getMajorVersion() < 10) {
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
