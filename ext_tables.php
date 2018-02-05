<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$extensionConfiguration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ats']);

// If legacy TS mode is enabled, load legacy files
// This means ALL TS, plugin and module, is loaded in backend and module settings are inherited from plugin settings
// This option is not recommended and will be removed in V2
if ($extensionConfiguration['enableLegacyBackendTS'] == true) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('ats', 'constants', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ats/Configuration/TypoScript/Backend/constants_legacy.ts">');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('ats', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ats/Configuration/TypoScript/Backend/setup_legacy.ts">');
} else {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('ats', 'constants', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ats/Configuration/TypoScript/Backend/constants.ts">');
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript('ats', 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ats/Configuration/TypoScript/Backend/setup.ts">');
}


\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'PAGEmachine.Ats',
    'Jobs',
    'Jobs'
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('ats', 'Configuration/TypoScript', 'ATS');

// PageTS extensions
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
    '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:ats/Configuration/PageTS/main.ts">'
);

//Main module in backend (expandable section "ATS")
$GLOBALS['TBE_MODULES']['_configuration']['AtsAts'] = [
    'name' => 'ats',
    'labels' => ['ll_ref' => 'LLL:EXT:ats/Resources/Private/Language/locallang_mod.xlf'],
];


if (TYPO3_MODE === 'BE') {
    //First module, application listing

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'PAGEmachine.Ats',
        'ats',
        'applications',
        '',
        array(
            'Backend\\Application' => 'index, listAll, listMine, show, edit, removeUpload, update, editStatus, updateStatus, notes, addNote, close, confirmClose, reply, sendReply, invite, sendInvitation, acknowledge, sendAcknowledgement, ratingPerso, rating, addRating, backToPerso, sendBackToPerso, reject, sendRejection, history, clone, confirmClone, new, create'
        ),
        array(
            'access'    => 'user,group',
            'icon'      => 'EXT:ats/Resources/Public/Icons/module_applications.svg',
            'labels'    => 'LLL:EXT:ats/Resources/Private/Language/locallang_mod_applications.xlf',
        )
    );

    //Second module, application archive

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'PAGEmachine.Ats',
        'ats',
        'archive',
        '',
        array(
            'Backend\\ArchivedApplication' => 'index, listAll, show, edit, removeUpload, update, editStatus, updateStatus, notes, addNote, history, clone, confirmClone, listPool, moveToPool, updateMoveToPool'
        ),
        array(
            'access'    => 'user,group',
            'icon'      => 'EXT:ats/Resources/Public/Icons/module_archive.svg',
            'labels'    => 'LLL:EXT:ats/Resources/Private/Language/locallang_mod_archive.xlf',
        )
    );

    //Third module, statistics

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'PAGEmachine.Ats',
        'ats',
        'statistics',
        '',
        array(
            'Backend\\Statistics' => 'statistics, export, getCsv'
        ),
        array(
            'access'    => 'user,group',
            'icon'      => 'EXT:ats/Resources/Public/Icons/module_statistics.svg',
            'labels'    => 'LLL:EXT:ats/Resources/Private/Language/locallang_mod_statistics.xlf',
        )
    );

    //Fourth module, mass notification

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'PAGEmachine.Ats',
        'ats',
        'multiSend',
        '',
        array(
            'Backend\\NotificationApplication' => 'index, listAll, show, edit, removeUpload, update, editStatus, updateStatus, notes, addNote, history, clone, confirmClone, newMassNotification, sendMassNotification, downloadPdf, result'
        ),
        array(
            'access'    => 'user,group',
            'icon'      => 'EXT:ats/Resources/Public/Icons/module_massnotifications.svg',
            'labels'    => 'LLL:EXT:ats/Resources/Private/Language/locallang_mod_notification.xlf',
        )
    );
}
