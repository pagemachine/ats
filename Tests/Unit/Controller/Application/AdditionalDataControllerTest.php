<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Controller\Application\AdditionalDataController;
use PAGEmachine\Ats\Domain\Model\ApplicationD;
use PAGEmachine\Ats\Domain\Repository\ApplicationDRepository;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Testcase for AdditionalDataController
 */
class AdditionalDataControllerTest extends UnitTestCase
{
    /**
     * @var AdditionalDataController
    */
    protected $controller;

    /**
     * @var ViewInterface|Prophecy\Prophecy\ObjectProphecy
     */
    protected $view;


    /**
     * @var ApplicationD|Prophecy\Prophecy\ObjectProphecy
     */
    protected $application;

    /**
     * Set up this testcase
    */
    public function setUp()
    {

        $this->controller = $this->getMockBuilder(AdditionalDataController::class)->setMethods([
            'forward',
        ])->getMock();

        $this->application = $this->prophesize(ApplicationD::class);

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->controller, 'view', $this->view->reveal());
    }

    /**
     * @test
     */
    public function showsAdditionalDataForm()
    {

        $this->view->assign('application', $this->application->reveal())->shouldBeCalled();

        $this->controller->editAdditionalDataAction($this->application->reveal());
    }

    /**
     * @test
     */
    public function updatesAndForwardsToNextStep()
    {

        $repository = $this->prophesize(ApplicationDRepository::class);
        $this->inject($this->controller, "repository", $repository->reveal());

        $request = $this->prophesize(RequestInterface::class);
        $request->getArgument('application')->willReturn([]);
        $this->inject($this->controller, 'request', $request->reveal());

        $repository->addOrUpdate($this->application->reveal())->shouldBeCalled();

        $this->controller->expects($this->once())->method('forward');
        $this->controller->updateAdditionalDataAction($this->application->reveal());
    }
}
