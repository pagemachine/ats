<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Controller\Application\AbstractApplicationController;
use PAGEmachine\Ats\Controller\Application\FormController;
use PAGEmachine\Ats\Domain\Model\ApplicationA;
use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Domain\Repository\ApplicationARepository;
use PAGEmachine\Ats\Service\AuthenticationService;
use Prophecy\Argument;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

/**
 * Testcase for PAGEmachine\Ats\Controller\AbstractApplicationController
 */
class FormControllerTest extends UnitTestCase {

    /**
     * @var AbstractApplicationController
     */
    protected $controller;

    /**
     * @var ViewInterface|Prophecy\Prophecy\ObjectProphecy
     */
    protected $view;


    /**
     * @var ApplicationA|Prophecy\Prophecy\ObjectProphecy
     */
    protected $applicationA;

    /**
     * @var Job|Prophecy\Prophecy\ObjectProphecy
     */
    protected $job;

     /**
     * @var ApplicationARepository|Prophecy\Prophecy\ObjectProphecy
     */
    protected $applicationARepository;


    /**
     * Set up this testcase
     */
    protected function setUp() {

        $this->controller = $this->getMockBuilder(FormController::class)->setMethods([
            'forward'
        ])->getMock();

        $this->application = $this->prophesize(ApplicationA::class);

        $this->job = $this->prophesize(Job::class);

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->controller, 'view', $this->view->reveal());

        $authenticationService = $this->prophesize(AuthenticationService::class);
        $authenticationService->getAuthenticatedUser()->willReturn(new FrontendUser);
        $this->inject($this->controller, 'authenticationService', $authenticationService->reveal());

        $this->applicationARepository = $this->prophesize(ApplicationARepository::class);
        $this->applicationARepository->findByUserAndJob(new FrontendUser, $this->job->reveal(), NULL, ApplicationStatus::INCOMPLETE)->willReturn($this->application->reveal());
        $this->inject($this->controller, 'applicationARepository', $this->applicationARepository->reveal());

    }

    /**
     * @test
     */
    public function showsForm() {

    	$this->view->assignMultiple(['job' => $this->job->reveal(), 'application' => $this->application->reveal(), 'user' => new FrontendUser])->shouldBeCalled();

    	$this->controller->formAction($this->job->reveal(), $this->application->reveal());

    }

    /**
     * @test
     */
    public function showsFormForPersistedApplication() {

    	$this->view->assignMultiple(['job' => $this->job->reveal(), 'application' => $this->application->reveal(), 'user' => new FrontendUser])->shouldBeCalled();
    	$this->controller->formAction($this->job->reveal(), NULL);

    }

    /**
     * @test
     */
    public function updatesAndForwardsToNextStep() {

    	$request = $this->prophesize(RequestInterface::class);
    	$request->getArgument('application')->willReturn([]);
    	$this->inject($this->controller, 'request', $request->reveal());

    	$this->applicationARepository->addOrUpdate($this->application->reveal())->shouldBeCalled();

        $this->controller->expects($this->once())->method('forward');
    	$this->controller->updateFormAction($this->application->reveal());



    }




}
