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
