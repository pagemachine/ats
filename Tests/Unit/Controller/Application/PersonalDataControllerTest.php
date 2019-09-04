<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Controller\Application\PersonalDataController;
use PAGEmachine\Ats\Domain\Model\ApplicationB;
use PAGEmachine\Ats\Domain\Repository\ApplicationBRepository;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Testcase for PersonalDataController
 */
class PersonalDataControllerTest extends UnitTestCase
{
    /**
     * @var PersonalDataController
    */
    protected $controller;

    /**
     * @var ViewInterface|Prophecy\Prophecy\ObjectProphecy
     */
    protected $view;


    /**
     * @var ApplicationB|Prophecy\Prophecy\ObjectProphecy
     */
    protected $application;

    /**
     * Set up this testcase
    */
    public function setUp()
    {

        $this->controller = $this->getMockBuilder(PersonalDataController::class)->setMethods([
            'forward',
        ])->getMock();

        $this->application = $this->prophesize(ApplicationB::class);

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->controller, 'view', $this->view->reveal());
    }

    /**
     * @test
     */
    public function updatesAndForwardsToNextStep()
    {

        $repository = $this->prophesize(ApplicationBRepository::class);
        $this->inject($this->controller, "repository", $repository->reveal());

        $request = $this->prophesize(RequestInterface::class);
        $request->getArgument('application')->willReturn([]);
        $this->inject($this->controller, 'request', $request->reveal());

        $repository->addOrUpdate($this->application->reveal())->shouldBeCalled();

        $this->controller->expects($this->once())->method('forward');
        $this->controller->updatePersonalDataAction($this->application->reveal());
    }
}
