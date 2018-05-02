<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Controller\JobController;
use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Domain\Repository\JobRepository;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Testcase for PAGEmachine\Ats\Controller\JobController
 */
class JobControllerTest extends UnitTestCase
{
    /**
     * @var JobController
     */
    protected $controller;

    /**
     * @var ViewInterface|Prophecy\Prophecy\ObjectProphecy
     */
    protected $view;

    /**
     * Set up this testcase
     */
    protected function setUp()
    {

        $this->controller = new JobController();

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->controller, 'view', $this->view->reveal());
    }

    /**
     * @test
     */
    public function mergesTSSettingsWithNonEmptyFlexSettings()
    {
        $settings = [
            'foo' => 'bar',
            'bar' => 'baz',
            'flexForm' => [
                'override' => '1',
                'foo' => 'overrideBar',
                'bar' => '',
            ],
        ];

        $expectedSettings = [
            'foo' => 'overrideBar',
            'bar' => 'baz',
            'override' => '1',
        ];

        $this->assertEquals(
            $expectedSettings,
            $this->controller->mergeFlexFormAndTypoScriptSettings($settings)
        );
    }


    /**
     * @test
     */
    public function mergesTSSettingsWithEmptyFlexSettings()
    {
        $settings = [
            'foo' => 'bar',
            'bar' => 'baz',
            'flexForm' => [
                'override' => '1',
                'overrideEmptyValues' => '1',
                'foo' => 'overrideBar',
                'bar' => '',
            ],
        ];

        $expectedSettings = [
            'foo' => 'overrideBar',
            'bar' => '',
            'override' => '1',
            'overrideEmptyValues' => '1',
        ];

        $this->assertEquals(
            $expectedSettings,
            $this->controller->mergeFlexFormAndTypoScriptSettings($settings)
        );
    }

    /**
     * @test
     */
    public function keepsTSSettingsIfFlexformIsDisabled()
    {
        $settings = [
            'foo' => 'bar',
            'bar' => 'baz',
            'flexForm' => [
                'override' => '0',
                'overrideEmptyValues' => '0',
                'foo' => 'overrideBar',
                'bar' => '',
            ],
        ];

        $expectedSettings = [
            'foo' => 'bar',
            'bar' => 'baz',
        ];

        $this->assertEquals(
            $expectedSettings,
            $this->controller->mergeFlexFormAndTypoScriptSettings($settings)
        );
    }

    // /**
    //  * @test
    //  * @dataProvider settings
    //  *
    //  * @param  string $override      Override. String since the parsed config contains an integer string such as '1'
    //  * @param  string $overrideEmpty Override empty values. String since the parsed config contains an integer string such as '1'
    //  * @param  array  $tsSettings    Global TS settings array
    //  * @param  array  $flexSettings  Flexform settings array
    //  * @param  array  $expectedResultSettings The expected result settings
    //  * @return void
    //  */
    // public function mergesFlexformSettingsWithTypoScriptSettings($override, $overrideEmpty, $tsSettings = [], $flexSettings = [], $expectedResultSettings = [])
    // {
    //     $settings = [


    //     ]


    //     $this->assertEquals()
    // }

    // public function settings()
    // {
    //     return [
    //         'override, ignore empty' => [
    //             '1',
    //             '0',
    //             [
    //                 'value1' => 'TS value',
    //                 'value2' => 'TS value',
    //             ],
    //             [
    //                 'value1' => 'Flex value',
    //                 'value2' => '',
    //             ],
    //             [
    //                 'value1' => 'Flex value',
    //                 'value2' => 'TS value',
    //             ],
    //         ]


    //     ];
    // }

    /**
     * @test
     */
    public function listsJobs()
    {

        $jobs = [];

        $repository = $this->prophesize(JobRepository::class);
        $repository->findAll()->willReturn($jobs);
        $this->inject($this->controller, 'jobRepository', $repository->reveal());

        $this->view->assign('jobs', $jobs)->shouldBeCalled();

        $this->controller->listAction();
    }

    /**
     * @test
     */
    public function showsSingleJob()
    {

        $job = $this->prophesize(Job::class);

        $this->view->assign('job', $job->reveal())->shouldBeCalled();

        $this->controller->showAction($job->reveal());
    }
}
