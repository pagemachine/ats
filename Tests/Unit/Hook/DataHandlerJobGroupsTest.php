<?php
namespace PAGEmachine\Ats\Tests\Unit\Hook;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Hook\DataHandlerJobGroups;
use PAGEmachine\Ats\Service\ExtconfService;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Testcase for DataHandlerJobGroups
 */
class DataHandlerJobGroupsTest extends TestCase
{
    /**
     * @var DataHandlerJobGroups
     */
    protected $dataHandlerJobGroups;

    /**
     * @var DatabaseConnection
     */
    protected $databaseConnection;

    /**
     * Set up this testcase
     */
    protected function setUp()
    {
        $this->databaseConnection = $this->prophesize(DatabaseConnection::class);

        $this->dataHandlerJobGroups = $this->getMockBuilder(DataHandlerJobGroups::class)
            ->setMethods(['getDatabaseConnection'])
            ->getMock();
        $this->dataHandlerJobGroups->method('getDatabaseConnection')->will($this->returnValue($this->databaseConnection->reveal()));


        $this->databaseConnection->exec_SELECTgetSingleRow(
            'job_number, location, department',
            'tx_ats_domain_model_job',
            'uid = ' . intval($uid)
        )->willReturn([
            'job_number' => 'foo',
            'location' => 'Location',
            'department' => '',
        ]);

        $this->databaseConnection->exec_SELECTgetSingleRow(
            'uid',
            'be_groups',
            'title = "Job_number_foo"'
        )->willReturn(['uid' => 10]);

        $extconfService = $this->prophesize(ExtconfService::class);
        $extconfService->getCreateJobGroups()->willReturn(true);
        $extconfService->getJobGroupTemplate()->willReturn("Template for location %s");
        $extconfService->getJobGroupPattern()->willReturn("Job_number_%s");

        GeneralUtility::setSingletonInstance(ExtconfService::class, $extconfService->reveal());
    }

    /**
     * @test
     */
    public function addsExistingGroupToJob()
    {
        $this->dataHandlerJobGroups = $this->getMockBuilder(DataHandlerJobGroups::class)
            ->setMethods(['getJob', 'ensureGroupForJob'])
            ->getMock();

        $this->dataHandlerJobGroups->method('getJob')
            ->with($this->equalTo(25))
            ->will($this->returnValue([
                'job_number' => 'foo',
                'location' => 'Location',
                'department' => '',
            ]));

        $this->dataHandlerJobGroups
            ->method('ensureGroupForJob')
            ->with($this->equalTo('Job_number_foo'), $this->equalTo('Location'))
            ->will($this->returnValue(10));

        $fieldArray = [
        ];

        $this->dataHandlerJobGroups->processDatamap_postProcessFieldArray(
            'update',
            'tx_ats_domain_model_job',
            25,
            $fieldArray,
            $this->prophesize(DataHandler::class)->reveal()
        );

        $this->assertEquals([
            'department' => 10,
        ], $fieldArray);
    }
}
