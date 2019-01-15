<?php
declare(strict_types = 1);
namespace PAGEmachine\Ats\Tests\Functional;
/*
 * This file is part of the Pagemachine Flat URLs project.
 */
use Nimut\TestingFramework\TestCase\FunctionalTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use PAGEmachine\Ats\TCA\FormHelper;

/**
 * Testcase for page processing
 */
class FormHelperTest extends FunctionalTestCase
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
     *
     * @var array
     */
    protected $params;

    /**
     * @var FormHelper
     */
    protected $formHelper;

    /**
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setUpBackendUserFromFixture(1);
        \TYPO3\CMS\Core\Core\Bootstrap::getInstance()->initializeLanguageObject();


        $this->importDataSet(__DIR__ . '/../Fixtures/Database/be_groups.xml');
        $this->importDataSet(__DIR__ . '/../Fixtures/Database/be_users.xml');

        /** @var \PAGEmachine\Ats\TCA\FormHelper */
        $this->formHelper = GeneralUtility::makeInstance(FormHelper::class);

        $this->params = [
            'row' => [
                'location' => 'Zentrale',
            ],
        ];

    }

    /**
     * @test
     */
    public function findsUserPa()
    {

        $this->formHelper->findUserPa($this->params);
        $this->runAsserts();
    }

    /**
     * @test
     */
    public function findsOfficials()
    {

        $this->formHelper->findOfficials($this->params);
        $this->runAsserts();
    }

    /**
     * @test
     */
    public function findsContributors()
    {

        $this->formHelper->findContributors($this->params);
        $this->runAsserts();
    }

    /**
     *
     * @return void
     */
    protected function runAsserts()
    {
        $this->assertArrayHasKey('items', $this->params);
        $this->assertArraySubset(
            [
                0 => [0 => 'Max Mueller (mueller)', 1 => '2']
            ],
            $this->params['items']
        );

    }

}
