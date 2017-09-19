<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Backend;

use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Controller\Backend\ArchivedApplicationController;
use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Model\Note;
use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use Prophecy\Argument;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;


/*
 * This file is part of the PAGEmachine ATS project.
 */

 /**
 * Testcase for ArchivedApplicationController
 */
 class ArchivedApplicationControllerTest extends UnitTestCase {

    /**
     * @var ArchivedApplicationController
     */
    protected $archivedApplicationController;

    /**
     * @var ApplicationRepository $applicationRepository
     */
    protected $applicationRepository;

    /**
     * @var Application
     */
    protected $application;


    /**
     * @var ControllerContext
     */
    protected $controllerContext;

    /**
     * @var ViewInterface
     */
    protected $view;

    /**
     * Set up this testcase
     */
    protected function setUp() {
        $this->archivedApplicationController = $this->getMockBuilder(ArchivedApplicationController::class)->setMethods([
            'redirect',
            'forward',
            'addFlashMessage',
            'getMenuRegistry'
            ])->getMock();

        $this->application = new Application();

        $objectManager = $this->prophesize(ObjectManager::class);

        $argumentDummy = new \stdClass();
        $objectManager->get(\TYPO3\CMS\Extbase\Mvc\Controller\Arguments::class)->willReturn($argumentDummy);

        $this->archivedApplicationController->injectObjectManager($objectManager->reveal());

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->archivedApplicationController, "view", $this->view->reveal());

        $this->applicationRepository = $this->prophesize(ApplicationRepository::class);
        $this->inject($this->archivedApplicationController, "applicationRepository", $this->applicationRepository->reveal());

        $this->controllerContext = $this->prophesize(ControllerContext::class);
        $this->inject($this->archivedApplicationController, "controllerContext", $this->controllerContext->reveal());
    }

    /**
     * @test
     */
    public function redirectsToCorrectListFunction()
    {

        $request = $this->prophesize(Request::class);
        $request->getControllerName()->willReturn("ControllerName");

        $this->inject($this->archivedApplicationController, "request", $request->reveal());

        $this->archivedApplicationController->expects($this->once())->method("forward")->with(
            "listAll"
        );

        $this->archivedApplicationController->initializeIndexAction();

    }

    /**
     * @test
     */
    public function runsMoveToPoolAction()
    {
        $GLOBALS['BE_USER'] = new \stdClass();
        $GLOBALS['BE_USER']->user = ['foo'];
        $this->view->assign("application", $this->application)->shouldBeCalled();
        $this->view->assign("beUser", ['foo'])->shouldBeCalled();

        $this->archivedApplicationController->moveToPoolAction($this->application);
    }

    /**
     * @test
     */
    public function updateMoveToPool(){
        $note = $this->prophesize(Note::class);
        $note->getDetails()->willReturn("Hello");

        $application = $this->prophesize(Application::class);
        $application->addNote($note->reveal())->shouldBeCalled();

        $this->applicationRepository->updateAndLog($application->reveal(), Argument::cetera())->shouldBeCalled();

        $this->archivedApplicationController->expects($this->once())->method("addFlashMessage");
        $this->archivedApplicationController->expects($this->once())->method("redirect")->with("moveToPool");

        $this->archivedApplicationController->updateMoveToPoolAction($application->reveal(), $note->reveal());
    }

 }
