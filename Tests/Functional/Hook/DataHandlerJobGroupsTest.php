<?php
declare(strict_types = 1);
namespace PAGEmachine\Ats\Tests\Functional;
/*
 * This file is part of the Pagemachine Flat URLs project.
 */
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use PAGEmachine\Ats\Hook\DataHandlerJobGroups;
use TYPO3\CMS\Core\DataHandling\DataHandler;

/**
 * Testcase for page processing
 */
class DataHandlerJobGroupsTest extends FunctionalTestCase
{
    /**
     * @var array
     */
    protected $testExtensionsToLoad = [
        'typo3conf/ext/ats',
    ];

    /**
     * @var string
     */
    protected $backendUserFixture = __DIR__ .'/../Fixtures/Database/Overrides/be_users.xml';

    /**
     * @var DataHandlerJobGroups
     */
    protected $dataHandlerJobGroups;

    /**
     * @var DataHandler
     */
    protected $dataHandler;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setUpBackendUserFromFixture(1);
        \TYPO3\CMS\Core\Core\Bootstrap::getInstance()->initializeLanguageObject();


        $this->importDataSet(__DIR__ . '/../Fixtures/Database/be_groups.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/Database/tx_ats_domain_model_job.xml');

        /** @var \PAGEmachine\Ats\Hook\DataHandlerJobGroups */
        $this->dataHandlerJobGroups = GeneralUtility::makeInstance(DataHandlerJobGroups::class);

        $this->dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['emSettings']['createJobGroups'] = true;
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['emSettings']['jobGroupSchema'] = 'bms_jobno_%s';
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ats']['emSettings']['jobGroupTemplate'] = 'bms department template %s';

    }

    /**
     * @test
     */
    public function addsTemplateGroupToJob()
    {
        $fieldArray = [
        ];

        $this->dataHandlerJobGroups->processDatamap_postProcessFieldArray(
            'update',
            'tx_ats_domain_model_job',
            1,
            $fieldArray,
            $this->dataHandler
        );

        $be_group = $this->getDatabaseConnection()->selectSingleRow('title', 'be_groups', 'uid = '.$fieldArray['department']);

        $this->assertEquals('bms_jobno_1337', $be_group['title']);
    }

}
