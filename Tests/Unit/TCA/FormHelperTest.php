<?php
namespace PAGEmachine\Ats\Tests\Unit\TCA;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\TCA\FormHelper;
use Prophecy\Argument;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Testcase for PAGEmachine\Ats\Controller\ApplicationController
 */
class FormHelperTest extends UnitTestCase
{
    /**
     * @var FormHelper
     */
    protected $formHelper;

    /**
     *
     * @var DatabaseConnection
     */
    protected $databaseConnection;

    /**
     *
     * @var array
     */
    protected $params;


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
     * Set up this testcase
     */
    protected function setUp()
    {

        $this->formHelper = $this->getMockBuilder(FormHelper::class)->setMethods([
            'getDeleteClause',
            'getBackendEnableFields',
        ])->getMock();

        $result = new \stdClass();
        $result->foo = 'FOO';
        $this->databaseConnection = $this->prophesize(DatabaseConnection::class);
        $this->databaseConnection->exec_SELECTquery("uid", "be_groups", Argument::any())->willReturn($result);
        $this->databaseConnection->sql_fetch_assoc($result)->willReturn(
            [
                'uid' => 5,
              ],
            false
        );

        $userResult = new \stdClass();
        $result->foo = 'BAR';
        $this->databaseConnection->exec_SELECTquery("*", "be_users", Argument::any(), '', Argument::any())->willReturn($userResult);
        $this->databaseConnection->sql_fetch_assoc($userResult)->willReturn(
            [
                'uid' => 1,
                'realName' => 'Max Mueller',
                'username' => 'mueller',
              ],
            false
        );

        $GLOBALS['TYPO3_DB'] = $this->databaseConnection->reveal();

        $this->params = [
            'row' => [
                'location' => 'Zentrale',
            ],
        ];
    }

    /**
     *
     * @return void
     */
    protected function runAsserts()
    {
        $this->assertArrayHasKey('items', $this->params);
        $this->assertArraySubset(
            ["items" =>
                [
                    0 => [0 => 'Max Mueller (mueller)', 1 => '1'],
                ],
            ],
            $this->params
        );
    }
}
