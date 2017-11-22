<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Backend;

use PAGEmachine\Ats\Application\ApplicationFilter;
use PAGEmachine\Ats\Application\ApplicationRating;
use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Controller\Backend\ApplicationController;
use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Model\Note;
use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use PAGEmachine\Ats\Domain\Repository\JobRepository;
use PAGEmachine\Ats\Message\AcknowledgeMessage;
use PAGEmachine\Ats\Message\InviteMessage;
use PAGEmachine\Ats\Message\MessageFactory;
use PAGEmachine\Ats\Message\RejectMessage;
use PAGEmachine\Ats\Message\ReplyMessage;
use PAGEmachine\Ats\Workflow\WorkflowManager;
use Prophecy\Argument;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\Argument as ControllerArgument;
use TYPO3\CMS\Extbase\Mvc\Controller\Arguments;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfiguration;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Property\PropertyMappingConfiguration;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
* Testcase for ApplicationController
*/
class ApplicationControllerTest extends UnitTestCase
{
    /**
     * @var ApplicationController
     */
    protected $applicationController;


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
     * @var MessageFactory
     */
    protected $messageFactory;


    /**
     * @var JobRepository $jobRepository
     */
    protected $jobRepository;


    /**
     * Set up this testcase
     */
    protected function setUp()
    {

        $this->applicationController = $this->getMockBuilder(ApplicationController::class)->setMethods([
            'redirect',
            'forward',
            'addFlashMessage',
            'getMenuRegistry',
            ])->getMock();

        $this->application = new Application();


        $objectManager = $this->prophesize(ObjectManager::class);

        $argumentDummy = new \stdClass();
        $objectManager->get(\TYPO3\CMS\Extbase\Mvc\Controller\Arguments::class)->willReturn($argumentDummy);

        $this->applicationController->injectObjectManager($objectManager->reveal());

        $this->messageFactory = $this->prophesize(MessageFactory::class);
        $this->inject($this->applicationController, "messageFactory", $this->messageFactory->reveal());

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->applicationController, "view", $this->view->reveal());

        $this->applicationRepository = $this->prophesize(ApplicationRepository::class);
        $this->inject($this->applicationController, "applicationRepository", $this->applicationRepository->reveal());

        $this->jobRepository = $this->prophesize(JobRepository::class);
        $this->inject($this->applicationController, "jobRepository", $this->jobRepository->reveal());

        $this->controllerContext = $this->prophesize(ControllerContext::class);
        $this->inject($this->applicationController, "controllerContext", $this->controllerContext->reveal());
    }

    /**
     * @test
     */
    public function redirectsToCorrectListFunction()
    {

        $request = $this->prophesize(Request::class);
        $request->getControllerName()->willReturn("ControllerName");

        $this->inject($this->applicationController, "request", $request->reveal());

        $this->applicationController->expects($this->once())->method("forward")->with(
            "listMine"
        );

        $this->applicationController->initializeIndexAction();
    }

    /**
     * @test
     */
    public function listsAllApplications()
    {
        $filter = new ApplicationFilter();

        $this->jobRepository->findAll()->shouldBeCalled();

        $this->applicationRepository->findDeadlineExceeded(Argument::any(), null, $filter)->shouldBeCalled()->willReturn(['foo']);
        $this->applicationRepository->findNew(Argument::any(), null, $filter)->shouldBeCalled()->willReturn(['bar']);
        $this->applicationRepository->findInProgress(Argument::any(), null, $filter)->shouldBeCalled()->willReturn(['baz']);
        $this->view->assignMultiple(Argument::size(5))->shouldBeCalled();

        $this->applicationController->listAllAction();
    }

    /**
     * @test
     */
    public function listsMyApplications()
    {
        $filter = new ApplicationFilter();

        $user = $this->prophesize(BackendUserAuthentication::class);
        $user->user = [
            'uid' => 4,
        ];
        $GLOBALS['BE_USER'] = $user->reveal();

        $this->jobRepository->findAll()->shouldBeCalled();

        $this->applicationRepository->findDeadlineExceeded(argument::any(), $user->reveal(), $filter)->shouldBeCalled()->willReturn(['foo']);
        $this->applicationRepository->findNew(argument::any(), $user->reveal(), $filter)->shouldBeCalled()->willReturn(['bar']);
        $this->applicationRepository->findInProgress(argument::any(), $user->reveal(), $filter)->shouldBeCalled()->willReturn(['baz']);
        $this->view->assignMultiple(Argument::size(5))->shouldBeCalled();

        $this->applicationController->listMineAction();
    }

    /**
     * @test
     */
    public function showsASingleApplication()
    {
        $this->view->assign("application", $this->application)->shouldBeCalled();

        $this->applicationController->showAction($this->application);
    }

    public function propertyFixActions()
    {

        return [['initializeEditAction'], ['initializeUpdateAction']];
    }

    /**
     * @test
     * @dataProvider propertyFixActions
     */
    public function fixesDynamicPropertyMapping($action)
    {

        $arguments = $this->prophesize(Arguments::class);
        $mappingConfiguration = $this->prophesize(MvcPropertyMappingConfiguration::class);
        $subConfiguration = $this->prophesize(PropertyMappingConfiguration::class);
        $argument = $this->prophesize(ControllerArgument::class);

        $arguments->getArgument("application")->willReturn($argument->reveal());
        $this->inject($this->applicationController, "arguments", $arguments->reveal());

        $argument->getPropertyMappingConfiguration()->willReturn($mappingConfiguration->reveal());



        $mappingConfiguration->forProperty("languageSkills")->willReturn($subConfiguration->reveal());
        $mappingConfiguration->forProperty("languageSkills.*")->willReturn($subConfiguration->reveal());

        $subConfiguration->allowAllProperties()->shouldBeCalled();
        $subConfiguration->allowProperties("language")->shouldBeCalled();
        $subConfiguration->allowProperties("level")->shouldBeCalled();

        $mappingConfiguration->allowCreationForSubProperty("languageSkills.*")->shouldBeCalled();

        $this->applicationController->$action();
    }


    /**
     * @test
     */
    public function editsASingleApplication()
    {
        $this->view->assign("application", $this->application)->shouldBeCalled();

        $this->applicationController->editAction($this->application);
    }

    /**
     * @test
     */
    public function updatesASingleAction()
    {
        $this->applicationRepository->updateAndLog($this->application, Argument::type("string"))->shouldBeCalled();
        $this->applicationController->expects($this->once())->method("addFlashMessage");

        $this->applicationController->expects($this->once())->method("redirect")->with(
            "edit",
            null,
            null,
            ['application' => $this->application]
        );

        $this->applicationController->updateAction($this->application);
    }

    /**
     * @test
     */
    public function editsStatus()
    {
        $user = ['foo'];
        $GLOBALS['BE_USER'] = new \stdClass();
        $GLOBALS['BE_USER']->user = $user;

        $workflowManager = $this->prophesize(WorkflowManager::class);
        GeneralUtility::setSingletonInstance(WorkflowManager::class, $workflowManager->reveal());

        $places = [
            10 => 10,
            20 => 20,
        ];
        $workflowManager->getPlaces()->shouldBeCalled()->willReturn($places);


        $this->view->assign('statusOptions', $places)->shouldBeCalled();
        $this->view->assign('application', $this->application)->shouldBeCalled();
        $this->view->assign('beUser', $user)->shouldBeCalled();

        $this->applicationController->editStatusAction($this->application);
    }

    /**
     * @test
     */
    public function updatesStatus()
    {
        $application = $this->prophesize(Application::class);
        $note = $this->prophesize(Note::class);
        $note->getDetails()->willReturn("blabla");

        $application->addNote($note->reveal())->shouldBeCalled();
        $application->getStatus()->willReturn(new ApplicationStatus());

        $this->applicationRepository->updateAndLog($application->reveal(), Argument::type("string"), Argument::type("array"))->shouldBeCalled();

        $this->applicationController->expects($this->once())->method("addFlashMessage");
        $this->applicationController->expects($this->once())->method("redirect")->with(
            "editStatus",
            null,
            null,
            ['application' => $application->reveal()]
        );

        $this->applicationController->updateStatusAction($application->reveal(), $note->reveal());
    }

    /**
     * @test
     */
    public function runsNotesAction()
    {
        $GLOBALS['BE_USER'] = new \stdClass();
        $GLOBALS['BE_USER']->user = ['foo'];
        $this->view->assign("application", $this->application)->shouldBeCalled();
        $this->view->assign("beUser", ['foo'])->shouldBeCalled();

        $this->applicationController->notesAction($this->application);
    }

    /**
     * @test
     */
    public function addsNote()
    {
        $note = $this->prophesize(Note::class);
        $application = $this->prophesize(Application::class);

        $note->getDetails()->willReturn("foobar");
        $note->getIsInternal()->willReturn(false);

        $application->addNote($note->reveal())->shouldBeCalled();

        $this->applicationRepository->updateAndLog($application->reveal(), Argument::cetera())->shouldBeCalled();

        $this->applicationController->expects($this->once())->method("addFlashMessage");
        $this->applicationController->expects($this->once())->method("redirect")->with(
            "notes",
            null,
            null,
            ['application' => $application->reveal()]
        );

        $this->applicationController->addNoteAction($note->reveal(), $application->reveal());
    }

    /**
     * @test
     */
    public function runsCloseApplicationAction()
    {
        $this->view->assign('statusOptions', ApplicationStatus::getConstantsForCompletion())->shouldBeCalled();

        $GLOBALS['BE_USER'] = new \stdClass();
        $GLOBALS['BE_USER']->user = ['foo'];
        $this->view->assign("application", $this->application)->shouldBeCalled();
        $this->view->assign("beUser", ['foo'])->shouldBeCalled();

        $this->applicationController->closeAction($this->application);
    }

    /**
     * @test
     */
    public function closesApplicationAndAddsNote()
    {
        $note = $this->prophesize(Note::class);
        $application = $this->prophesize(Application::class);

        $note->getDetails()->willReturn("foobar");
        $application->addNote($note->reveal())->shouldBeCalled();
        $application->getStatus()->willReturn(new ApplicationStatus());

        $this->applicationRepository->updateAndLog($application->reveal(), Argument::cetera())->shouldBeCalled();

        $this->applicationController->expects($this->once())->method("addFlashMessage");
        $this->applicationController->expects($this->once())->method("redirect")->with(
            "index"
        );

        $this->applicationController->confirmCloseAction($application->reveal(), $note->reveal());
    }


    public function messageActions()
    {

        return [
            'Reply' => ['replyAction', 'reply', ReplyMessage::class],
            'Invite' => ['inviteAction', 'invite', InviteMessage::class],
            'Acknowledge' => ['acknowledgeAction', 'acknowledge', AcknowledgeMessage::class],
            'Reject' => ['rejectAction', 'reject', RejectMessage::class],
        ];
    }

    /**
     * @test
     * @dataProvider messageActions
     */
    public function runsMessageActionWithEmptyMessage($action, $messageName, $messageClass)
    {
        $message = $this->prophesize($messageClass);
        $message->getApplication()->willReturn($this->application);
        $message->applyTextTemplate()->shouldBeCalled();


        $this->messageFactory->createMessage($messageName, $this->application)->shouldBeCalled()->willReturn($message->reveal());

        $this->view->assignMultiple([
            'message' => $message->reveal(),
            'application' => $this->application,
        ])->shouldBeCalled();

        $this->applicationController->$action(null, $this->application);
    }

    /**
     * @test
     * @dataProvider messageActions
     */
    public function runsMessageActionWithExistingMessage($action, $messageName, $messageClass)
    {
        $message = $this->prophesize($messageClass);
        $message->getApplication()->willReturn($this->application);
        $message->applyTextTemplate()->shouldBeCalled();


        $this->messageFactory->createMessage($messageName, $this->application)->shouldNotBeCalled();

        $this->view->assignMultiple([
            'message' => $message->reveal(),
            'application' => $this->application,
        ])->shouldBeCalled();

        $this->applicationController->$action($message->reveal(), null);
    }

    public function messageSendActions()
    {

        return [
            'Reply' => ['sendReplyAction', ReplyMessage::class],
            'Invite' => ['sendInvitationAction', InviteMessage::class],
            'Acknowledge' => ['sendAcknowledgementAction', AcknowledgeMessage::class],
            'Reject' => ['sendRejectionAction', RejectMessage::class],
        ];
    }

    /**
     * @test
     * @dataProvider messageSendActions
     */
    public function sendsMessageAndRedirects($action, $messageClass)
    {
        $message = $this->prophesize($messageClass);
        $message->send()->shouldBeCalled();
        $message->getApplication()->willReturn($this->application);
        $message->getRenderedSubject()->willReturn("foo");
        $message->getCc()->willReturn("cc");
        $message->getBcc()->willReturn("bcc");
        $message->getRenderedBody()->willReturn("Body");
        $message->getSendType()->willReturn("mail");

        $this->applicationRepository->updateAndLog($this->application, Argument::cetera())->shouldBeCalled();

        $this->applicationController->expects($this->once())->method("redirect")->with(
            "show",
            null,
            null,
            ['application' => $this->application]
        );

        $this->applicationController->$action($message->reveal());
    }

    /**
     * @test
     */
    public function setsMessageDatePropertyMapping()
    {
        $request = $this->prophesize(Request::class);
        $request->hasArgument('message')->willReturn(true);
        $request->hasArgument('application')->willReturn(false);

        $this->inject($this->applicationController, "request", $request->reveal());

        $arguments = $this->prophesize(Arguments::class);
        $mappingConfiguration = $this->prophesize(PropertyMappingConfiguration::class);

        $argument = $this->prophesize(ControllerArgument::class);

        $arguments->getArgument("message")->willReturn($argument->reveal());
        $this->inject($this->applicationController, "arguments", $arguments->reveal());
        $argument->getPropertyMappingConfiguration()->willReturn($mappingConfiguration->reveal());

        $mappingConfiguration->forProperty('dateTime')->shouldBeCalled()->willReturn($mappingConfiguration->reveal());
        $mappingConfiguration->setTypeConverterOption(\TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class, \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d H:i')->shouldBeCalled();


        $mappingConfiguration->forProperty('confirmDate')->shouldBeCalled()->willReturn($mappingConfiguration->reveal());
        $mappingConfiguration->setTypeConverterOption(\TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class, \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d')->shouldBeCalled();

        $this->applicationController->initializeAction();
    }

    /**
     * @test
     */
    public function setsApplicationDatePropertyMapping()
    {
        $request = $this->prophesize(Request::class);
        $request->hasArgument('message')->willReturn(false);
        $request->hasArgument('application')->willReturn(true);

        $this->inject($this->applicationController, "request", $request->reveal());

        $arguments = $this->prophesize(Arguments::class);
        $mappingConfiguration = $this->prophesize(PropertyMappingConfiguration::class);

        $argument = $this->prophesize(ControllerArgument::class);

        $arguments->getArgument("application")->willReturn($argument->reveal());
        $this->inject($this->applicationController, "arguments", $arguments->reveal());
        $argument->getPropertyMappingConfiguration()->willReturn($mappingConfiguration->reveal());


        $mappingConfiguration->forProperty('birthday')->shouldBeCalled()->willReturn($mappingConfiguration->reveal());
        $mappingConfiguration->setTypeConverterOption(\TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::class, \TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d')->shouldBeCalled();

        $this->applicationController->initializeAction();
    }
    /**
     * @test
     */
    public function runsRatingAction()
    {
        $GLOBALS['BE_USER'] = new \stdClass();
        $GLOBALS['BE_USER']->user = ['foo'];
        $this->view->assign("application", $this->application)->shouldBeCalled();
        $this->view->assign("beUser", ['foo'])->shouldBeCalled();
        $this->view->assign('ratingOptions', ApplicationRating::getFlippedConstants())->shouldBeCalled();

        $this->applicationController->ratingAction($this->application);
    }

    /**
     * @test
     */
    public function runsRatingPersoAction()
    {
        $GLOBALS['BE_USER'] = new \stdClass();
        $GLOBALS['BE_USER']->user = ['foo'];
        $this->view->assign("application", $this->application)->shouldBeCalled();
        $this->view->assign("beUser", ['foo'])->shouldBeCalled();
        $this->view->assign('ratingOptions', ApplicationRating::getFlippedConstants())->shouldBeCalled();

        $this->applicationController->ratingPersoAction($this->application);
    }

    /**
     * @test
     */
    public function addsRating()
    {
        $note = new Note();
        $application = $this->prophesize(Application::class);

        $note->setDetails("foobar");

        $application->addNote($note)->shouldBeCalled();
        $application->getRatingPerso()->willReturn(new ApplicationRating());

        $this->applicationRepository->updateAndLog($application->reveal(), Argument::cetera())->shouldBeCalled();

        $this->applicationController->expects($this->once())->method("addFlashMessage");
        $this->applicationController->expects($this->once())->method("redirect")->with(
            "foo",
            null,
            null,
            ['application' => $application->reveal()]
        );

        $this->applicationController->addRatingAction($note, $application->reveal(), 'foo');
    }

    /**
     * @test
     */
    public function runsBackToPersoAction()
    {
        $GLOBALS['BE_USER'] = new \stdClass();
        $GLOBALS['BE_USER']->user = ['foo'];
        $this->view->assign("application", $this->application)->shouldBeCalled();
        $this->view->assign("beUser", ['foo'])->shouldBeCalled();

        $this->applicationController->backToPersoAction($this->application);
    }

    /**
     * @test
     */
    public function sendBackToPerso()
    {
        $note = $this->prophesize(Note::class);
        $note->getDetails()->willReturn("Hello");

        $application = $this->prophesize(Application::class);
        $application->addNote($note->reveal())->shouldBeCalled();

        $this->applicationRepository->updateAndLog($application->reveal(), Argument::cetera())->shouldBeCalled();

        $this->applicationController->expects($this->once())->method("addFlashMessage");
        $this->applicationController->expects($this->once())->method("redirect")->with("index");

        $this->applicationController->sendBackToPersoAction($application->reveal(), $note->reveal());
    }
}
