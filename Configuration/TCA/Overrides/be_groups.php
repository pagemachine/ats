<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('be_groups', array(
    'tx_ats_location' => array (
        'exclude' => 0,
        'label' => 'LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:be_groups.tx_ats_location',
        'config' => array (
            'type' => 'radio',
            'items' => array (
                array('LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:be_groups.tx_ats_location.I.0', 'Zentrale')
            ),
        )
    )
));

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
  'be_groups',
  '--div--;LLL:EXT:ats/Resources/Private/Language/locallang_db.xlf:be_groups.tx_ats, tx_ats_location',
  '',
  'after:TSconfig'
);
