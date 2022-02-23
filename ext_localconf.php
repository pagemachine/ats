<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'PAGEmachine.Ats',
    'Jobs',
    [
        'Job' => 'list, show',
        'Application\\Form' => 'form, updateForm',
        'Application\\PersonalData' => 'editPersonalData, updatePersonalData',
        'Application\\Qualifications' => 'editQualifications, updateQualifications',
        'Application\\AdditionalData' => 'editAdditionalData, updateAdditionalData',
        'Application\\Upload' => 'editUpload, saveUpload, updateUpload, removeUpload',
        'Application\\Submit' => 'showSummary, submit, submitted, showSimpleSummary',
        'Application\\SimpleForm' => 'simpleForm, submit, saveUpload, updateUpload, removeUpload',
    ],
    [
        'Application\\Form' => 'form, updateForm',
        'Application\\PersonalData' => 'editPersonalData, updatePersonalData',
        'Application\\Qualifications' => 'editQualifications, updateQualifications',
        'Application\\AdditionalData' => 'editAdditionalData, updateAdditionalData',
        'Application\\Upload' => 'editUpload, saveUpload, updateUpload, removeUpload',
        'Application\\Submit' => 'showSummary, submit, submitted, showSimpleSummary',
        'Application\\SimpleForm' => 'simpleForm, submit, saveUpload, updateUpload, removeUpload',
    ]
);

$typo3Version = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Information\Typo3Version::class);
if ($typo3Version->getMajorVersion() < 10) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['FileDumpEID.php']['checkFileAccess']['ats_protection'] = \PAGEmachine\Ats\Hook\LegacyFileDumpControllerHook::class;
} else {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['FileDumpEID.php']['checkFileAccess']['ats_protection'] = \PAGEmachine\Ats\Hook\FileDumpControllerHook::class;
}

if (TYPO3_MODE === 'BE') {
    $GLOBALS ['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['ats'] = \PAGEmachine\Ats\Hook\DataHandlerJobGroups::class;
}

/**
 * ATS extension configuration
 * These are default values, feel free to modify them in your site extension.
 */
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats'] = [
    // Default orderings in repository classes. Change these to modify the default order of jobs/applications.
    'defaultOrderings' => [
        'application' => [
            'surname' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
            'firstname' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        ],
        'job' => [
            'endtime' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
            'title' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING,
        ],
    ],
    // Marker replacements (CKEDITOR --> Fluid) in both mail and pdf context.
    // Useful for defining shortcuts for ViewHelpers or translations.
    // You can add your own markers here, however they need to be added in Resources/Public/JavaScript/CKEditorSetup.js as well
    'replacemarkers' => [
        'default' => [
            'application.salutation' => 'f:translate(key:"tx_ats.message.salutation.{application.salutation}",extensionName:"ats")',
        ],
        'mail' => [
            'backenduser.signature' => 'backenduser.tx_ats_email_signature',
        ],
        'pdf' => [
            'backenduser.signature' => 'backenduser.tx_ats_pdf_signature',
        ],
    ],
    // Workflows
    'workflows' => [
        'defaultworkflow' => \PAGEmachine\Ats\Workflow\DefaultWorkflowConfiguration::get(),
        'simpleworkflow' => \PAGEmachine\Ats\Workflow\SimpleWorkflowConfiguration::get(),
    ],
    // Active workflow
    'activeWorkflow' => 'defaultworkflow',
    //Settings handled by extension manager
    'emSettings' => [
        //Upload related settings
        'fileHandling' => [
            'allowedFileExtensions' => 'png,gif,jpg,tif,pdf,xls,xlsx,doc,docx,rtf,txt,zip,rar',
            'conflictMode' => \TYPO3\CMS\Core\Resource\DuplicationBehavior::RENAME,
            'uploadFolder' => '1:/tx_ats/',
        ],
    ],
];


// Add ckeditor template preset
$GLOBALS['TYPO3_CONF_VARS']['RTE']['Presets']['ats_templates'] = 'EXT:ats/Configuration/RTE/Templates.yaml';


//TypeConverters for uploaded files
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter(PAGEmachine\Ats\Property\TypeConverter\UploadedFileReferenceConverter::class);
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerTypeConverter(PAGEmachine\Ats\Property\TypeConverter\ObjectStorageConverter::class);

//Load Extension Manager settings into EXTCONF for easier usage

if (!empty(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('ats'))) {
    $extensionManagementConfig = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Configuration\ExtensionConfiguration::class)->get('ats');

    foreach ($extensionManagementConfig as $key => $value) {
      //Merge instance settings
        if (is_array($value) && isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['emSettings'][$key])) {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['emSettings'][$key] = array_merge($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['emSettings'][$key], $extensionManagementConfig[$key]);
        } else {
            $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['emSettings'][$key] = $extensionManagementConfig[$key];
        }
    }
}

// Register alternative repositories if TYPO3 is below version 8
if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 8007000) {
    $objectContainer = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class);
    $objectContainer->registerImplementation(
        \PAGEmachine\Ats\Domain\Repository\CountryRepository::class,
        \PAGEmachine\Ats\Domain\Repository\LegacyCountryRepository::class
    );
    $objectContainer->registerImplementation(
        \PAGEmachine\Ats\Domain\Repository\LanguageRepository::class,
        \PAGEmachine\Ats\Domain\Repository\LegacyLanguageRepository::class
    );
}

// Access configuration, if EXT:extbase_acl is available

if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('extbase_acl')) {
    // Add all backend controller actions to this array.
    // This is a temporary solution to make them accessible by the role configuration API.
    // The API runs its preparations before ext_tables.php is loaded, so no module configuration is available
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase_acl']['protectedControllerActions'][\PAGEmachine\Ats\Controller\Backend\ApplicationController::class] = [
        'list',
        'listAll',
        'listMine',
        'show',
        'edit',
        'removeUpload',
        'update',
        'editStatus',
        'updateStatus',
        'notes',
        'addNote',
        'close',
        'confirmClose',
        'reply',
        'sendReply',
        'invite',
        'sendInvitation',
        'acknowledge',
        'sendAcknowledgement',
        'reject',
        'sendRejection',
        'rating',
        'ratingPerso',
        'addRating',
        'backToPerso',
        'sendBackToPerso',
        'clone',
        'confirmClone',
        'history',
    ];

    //Copy array for archived application controller
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase_acl']['protectedControllerActions'][\PAGEmachine\Ats\Controller\Backend\ArchivedApplicationController::class] = [
        'listAll',
        'show',
        'edit',
        'removeUpload',
        'update',
        'editStatus',
        'updateStatus',
        'notes',
        'addNote',
        'history',
        'clone',
        'confirmClone',
        'listPool',
        'moveToPool',
        'updateMoveToPool',
        'newMassPoolMoving',
        'setMassPoolMoving',
    ];

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase_acl']['protectedControllerActions'][\PAGEmachine\Ats\Controller\Backend\StatisticsController::class] = [
        'statistics',
        'export',
        'getCsv',
    ];

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase_acl']['protectedControllerActions'][\PAGEmachine\Ats\Controller\Backend\NotificationApplicationController::class] = [
        'listAll',
        'show',
        'edit',
        'removeUpload',
        'update',
        'editStatus',
        'updateStatus',
        'notes',
        'addNote',
        'history',
        'clone',
        'confirmClone',
        'newMassNotification',
        'sendMassNotification',
        'downloadPdf',
    ];

    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['extbase_acl']['protectedPartials']['ats'] = [
        'Backend\\Application\\ListNew',
        'Backend\\Application\\ListInProgress',
        'Backend\\Application\\ListDeadlineExceeded',
        'Backend\\Application\\ListArchived',
        'Backend\\Application\\ListNotification',
    ];
}

\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)->registerImplementation(\TYPO3\CMS\Extbase\Domain\Model\FileReference::class, \PAGEmachine\Ats\Domain\Model\FileReference::class);
\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)->registerImplementation(\TYPO3\CMS\Extbase\Persistence\Generic\QueryFactoryInterface::class, \PAGEmachine\Ats\Persistence\Generic\QueryFactory::class);

$iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
$iconRegistry->registerIcon(
   'ats-joblist',
   \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class,
   ['source' => 'EXT:ats/ext_icon.png']
);
