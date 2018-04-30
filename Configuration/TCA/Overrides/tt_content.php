<?php

defined('TYPO3_MODE') or die();

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['ats_jobs'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue('ats_jobs', 'FILE:EXT:ats/Configuration/FlexForms/FlexformJobs.xml');
