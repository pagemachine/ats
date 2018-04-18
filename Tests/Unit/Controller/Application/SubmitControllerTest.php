<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Controller\Application\SubmitController;
use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Testcase for SubmitController
 */
class SubmitControllerTest extends UnitTestCase
{
    /**
     * @var SubmitController
    */
    protected $controller;

    /**
     * @var ViewInterface|Prophecy\Prophecy\ObjectProphecy
     */
    protected $view;


    /**
     * @var Application|Prophecy\Prophecy\ObjectProphecy
     */
    protected $application;

    /**
     * Set up this testcase
    */
    public function setUp()
    {

        $this->controller = $this->getMockBuilder(SubmitController::class)->setMethods([
            'redirect',
            'forward'
            ])->getMock();

        $this->application = $this->prophesize(Application::class);

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->controller, 'view', $this->view->reveal());
    }

    /**
     * @test
     */
    public function showsSummary()
    {

        $this->view->assign('application', $this->application->reveal())->shouldBeCalled();

        $this->controller->showSummaryAction($this->application->reveal());
    }

    /**
     * @test
     */
    public function submitsApplication()
    {

        $this->application->submit()->shouldBeCalled();

        $repository = $this->prophesize(ApplicationRepository::class);
        $this->inject($this->controller, "repository", $repository->reveal());

        $request = $this->prophesize(RequestInterface::class);
        $request->getArgument('application')->willReturn([]);
        $this->inject($this->controller, 'request', $request->reveal());

        $repository->updateAndLog(
            $this->application->reveal(),
            'new'
        )->shouldBeCalled();
        $this->controller->submitAction($this->application->reveal());
    }
}
