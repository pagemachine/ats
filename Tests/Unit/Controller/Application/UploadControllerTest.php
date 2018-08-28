<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Controller\Application\UploadController;
use PAGEmachine\Ats\Domain\Model\ApplicationE;
use PAGEmachine\Ats\Domain\Model\FileReference;
use PAGEmachine\Ats\Domain\Repository\ApplicationERepository;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;

/**
 * Testcase for UploadController
 */
class UploadControllerTest extends UnitTestCase
{
    /**
     * @var UploadController
    */
    protected $controller;

    /**
     * @var ViewInterface|Prophecy\Prophecy\ObjectProphecy
     */
    protected $view;


    /**
     * @var ApplicationE|Prophecy\Prophecy\ObjectProphecy
     */
    protected $application;

    /**
     * @var ApplicationERepository
     */
    protected $repository;

    /**
     * Set up this testcase
    */
    public function setUp()
    {

        $this->controller = $this->getMockBuilder(UploadController::class)->setMethods([
            'forward',
        ])->getMock();

        $this->application = $this->prophesize(ApplicationE::class);
        $this->application->getUid()->willReturn(1);

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->controller, 'view', $this->view->reveal());

        $this->repository = $this->prophesize(ApplicationERepository::class);
        $this->inject($this->controller, "repository", $this->repository->reveal());

        $request = $this->prophesize(RequestInterface::class);
        $request->getArgument('application')->willReturn([]);
        $this->inject($this->controller, 'request', $request->reveal());
    }

    /**
     * @test
     */
    public function showsUploadForm()
    {

        $this->view->assign('application', $this->application->reveal())->shouldBeCalled();

        $this->controller->editUploadAction($this->application->reveal());
    }

    /**
     * @test
     */
    public function savesUpload()
    {

        $this->repository->addOrUpdate($this->application->reveal())->shouldBeCalled();

        $this->controller->expects($this->once())->method('forward')->with('editUpload', null, null, ['application' => 1]);
        $this->controller->saveUploadAction($this->application->reveal());
    }

    /**
     * @test
     */
    public function removesUpload()
    {

        $file = new FileReference();

        $this->application->removeFile($file)->shouldBeCalled();

        $this->repository->addOrUpdate($this->application->reveal())->shouldBeCalled();

        $this->controller->expects($this->once())->method('forward')->with('editUpload', null, null, ['application' => 1]);
        $this->controller->removeUploadAction($this->application->reveal(), $file);
    }

    /**
     * @test
     */
    public function updatesAndForwardsToNextStep()
    {

        $this->repository->addOrUpdate($this->application->reveal())->shouldBeCalled();

        $this->controller->expects($this->once())->method('forward')->with('showSummary', "Application\\Submit", null, ['application' => 1]);
        $this->controller->updateUploadAction($this->application->reveal());
    }
}
