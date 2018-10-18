<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Controller\JobController;
use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Domain\Repository\JobRepository;
use PAGEmachine\Ats\Service\TyposcriptService;
use Prophecy\Argument;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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

        $typoscriptService = $this->prophesize(TyposcriptService::class);
        //Return orginal settings without modification
        $typoscriptService->mergeFlexFormAndTypoScriptSettings(Argument::any())->willReturnArgument(0);
        GeneralUtility::setSingletonInstance(TyposcriptService::class, $typoscriptService->reveal());
    }

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
