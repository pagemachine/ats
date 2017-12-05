<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
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
            'Backend\\Application' => 'index, listAll, listMine, show, edit, removeUpload, update, editStatus, updateStatus, notes, addNote, close, confirmClose, reply, sendReply, invite, sendInvitation, acknowledge, sendAcknowledgement, ratingPerso, rating, addRating, backToPerso, sendBackToPerso, reject, sendRejection, history, clone, confirmClone'
        ),
        array(
            'access'    => 'user,group',
            'icon'      => 'EXT:ats/Resources/Public/Icons/module_applications.svg',
            'labels'    => 'LLL:EXT:ats/Resources/Private/Language/locallang_mod_applications.xlf',
            'navigationComponentId' => 'typo3-pagetree'
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
            'navigationComponentId' => 'typo3-pagetree'
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
            'navigationComponentId' => 'typo3-pagetree'
        )
    );

    //Fourth module, mass notification

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'PAGEmachine.Ats',
        'ats',
        'notification',
        '',
        array(
            'Backend\\NotificationApplication' => 'index, listAll, show, edit, removeUpload, update, editStatus, updateStatus, notes, addNote, history, clone, confirmClone, newMassNotification, sendMassNotification, downloadPdf, result'
        ),
        array(
            'access'    => 'user,group',
            'icon'      => 'EXT:ats/Resources/Public/Icons/module_massnotifications.svg',
            'labels'    => 'LLL:EXT:ats/Resources/Private/Language/locallang_mod_notification.xlf',
            'navigationComponentId' => 'typo3-pagetree'
        )
    );
}
