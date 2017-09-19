<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('be_users', [
    'tx_ats_email_signature' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:be_users.tx_ats_email_signature',
        'config' => [
            'type' => 'text',
            'size' => 30,
            'enableRichtext' => true,
        ]
    ],
    'tx_ats_pdf_signature' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:be_users.tx_ats_pdf_signature',
        'config' => [
            'type' => 'text',
            'size' => 30,
            'enableRichtext' => true,
        ]
    ],
    'tx_ats_contact_print' => [
        'exclude' => 0,
        'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:be_users.tx_ats_contact_print',
        'config' => [
            'type' => 'text',
            'size' => 30,
        ]
    ],
]);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
  'be_users',
  '--div--;LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:be_groups.tx_ats,tx_ats_email_signature, tx_ats_pdf_signature, tx_ats_contact_print',
  '',
  'after:endtime'
);
