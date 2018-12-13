<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Controller\Application\QualificationsController;
use PAGEmachine\Ats\Domain\Model\ApplicationC;
use PAGEmachine\Ats\Domain\Repository\ApplicationCRepository;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Testcase for QualificationsController
 */
class QualificationsControllerTest extends UnitTestCase
{
    /**
     * @var QualificationsController
    */
    protected $controller;

    /**
     * @var ViewInterface|Prophecy\Prophecy\ObjectProphecy
     */
    protected $view;


    /**
     * @var ApplicationC|Prophecy\Prophecy\ObjectProphecy
     */
    protected $application;

    /**
     * Set up this testcase
    */
    public function setUp()
    {

        $this->controller = $this->getMockBuilder(QualificationsController::class)->setMethods([
            'forward',
        ])->getMock();

        $this->application = $this->prophesize(ApplicationC::class);

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->controller, 'view', $this->view->reveal());
    }

    /**
     * @test
     */
    public function showsQualificationsForm()
    {

        $this->view->assign('application', $this->application->reveal())->shouldBeCalled();

        $this->controller->editQualificationsAction($this->application->reveal());
    }

    /**
     * @test
     */
    public function updatesAndForwardsToNextStep()
    {

        $repository = $this->prophesize(ApplicationCRepository::class);
        $this->inject($this->controller, "repository", $repository->reveal());

        $request = $this->prophesize(RequestInterface::class);
        $request->getArgument('application')->willReturn([]);
        $this->inject($this->controller, 'request', $request->reveal());

        $repository->addOrUpdate($this->application->reveal())->shouldBeCalled();

        $this->controller->expects($this->once())->method('forward');
        $this->controller->updateQualificationsAction($this->application->reveal());
    }
}
