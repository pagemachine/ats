<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Backend;

use PAGEmachine\Ats\Controller\Backend\NotificationApplicationController;
use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use PAGEmachine\Ats\Message\RejectMessage;
use PAGEmachine\Ats\Message\ReplyMessage;
use PAGEmachine\Ats\Service\PdfService;
use Prophecy\Argument;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ControllerContext;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/*
 * This file is part of the PAGEmachine ATS project.
 */

 /**
 * Testcase for NotificationApplicationController
 */
class NotificationApplicationControllerTest extends UnitTestCase
{
    /**
     * @var NotificationApplicationController
     */
    protected $notificationApplicationController;

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
    protected function setUp()
    {
        $this->notificationApplicationController = $this->getMockBuilder(NotificationApplicationController::class)->setMethods([
            'redirect',
            'forward',
            'addFlashMessage',
            'getMenuRegistry',
            ])->getMock();

        $this->application = new Application();

        $objectManager = $this->prophesize(ObjectManager::class);

        $argumentDummy = new \stdClass();
        $objectManager->get(\TYPO3\CMS\Extbase\Mvc\Controller\Arguments::class)->willReturn($argumentDummy);

        $this->notificationApplicationController->injectObjectManager($objectManager->reveal());

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->notificationApplicationController, "view", $this->view->reveal());

        $this->applicationRepository = $this->prophesize(ApplicationRepository::class);
        $this->inject($this->notificationApplicationController, "applicationRepository", $this->applicationRepository->reveal());

        $this->controllerContext = $this->prophesize(ControllerContext::class);
        $this->inject($this->notificationApplicationController, "controllerContext", $this->controllerContext->reveal());
    }

    /**
     * @test
     */
    public function sendMultipleEmailMassNotification()
    {
        $this->applicationRepository->findByUid('22')->shouldBeCalled()->willReturn($this->application);
        $this->applicationRepository->findByUid('24')->shouldBeCalled()->willReturn($this->application);

        $this->applicationRepository->updateAndLog($this->application, Argument::cetera())->shouldBeCalled();

        $message = $this->prophesize(RejectMessage::class);
        $message->setApplication($this->application)->shouldBeCalled();
        $message->getApplication()->shouldBeCalled()->willReturn($this->application);
        $message->setRenderedBody(null)->shouldBeCalled();
        $message->getSubject()->shouldBeCalled();
        $message->getCc()->shouldBeCalled();
        $message->getBcc()->shouldBeCalled();
        $message->getRenderedBody()->shouldBeCalled();

        $message->getSendType()->shouldBeCalled()->willReturn('mail');
        $message->send()->shouldBeCalled();

        $mes[] = ['filePath' => '', 'fileName' => '', 'message' => $message];
        $mes[] = ['filePath' => '', 'fileName' => '', 'message' => $message];

        $this->view->assignMultiple([
            'messages' => $mes,
        ])->shouldBeCalled();

        $this->notificationApplicationController->sendMassNotificationAction($message->reveal(), null, 'reject', ['22' => '1', '23' => '0', '24' => '1']);
    }

    /**
     * @test
     */
    public function sendPdfMassNotification()
    {
        $this->applicationRepository->findByUid('22')->shouldBeCalled()->willReturn($this->application);

        $this->applicationRepository->updateAndLog($this->application, Argument::cetera())->shouldBeCalled();

        $message = $this->prophesize(ReplyMessage::class);
        $message->setApplication($this->application)->shouldBeCalled();
        $message->getApplication()->shouldBeCalled()->willReturn($this->application);
        $message->setRenderedBody(null)->shouldBeCalled();
        $message->getSubject()->shouldBeCalled();
        $message->getCc()->shouldBeCalled();
        $message->getBcc()->shouldBeCalled();
        $message->getRenderedBody()->shouldBeCalled();


        $message->getSendType()->shouldBeCalled()->willReturn('pdf');
        $message->generatePdf('Foo.pdf')->shouldBeCalled()->willReturn('temp/Foo.pdf');

        $pdfService = $this->prophesize(PdfService::class);
        $pdfService->generateRandomFilename()->shouldBeCalled()->willReturn('Foo.pdf');
        GeneralUtility::setSingletonInstance(PdfService::class, $pdfService->reveal());

        $mes[] = ['filePath' => 'temp/Foo.pdf', 'fileName' => 'Foo.pdf', 'message' => $message];

        $this->view->assignMultiple([
            'messages' => $mes,
        ])->shouldBeCalled();

        $this->notificationApplicationController->sendMassNotificationAction(null, $message->reveal(), 'reply', ['22' => '1', '23' => '0']);
    }

    /**
     * @test
     */
    public function downloadPdfAction()
    {
        $pdfService = $this->prophesize(PdfService::class);
        $pdfService->downloadPdf('temp/Foo.pdf', 'Foo.pdf')->shouldBeCalled()->willReturn(true);
        GeneralUtility::setSingletonInstance(PdfService::class, $pdfService->reveal());

        $this->notificationApplicationController->downloadPdfAction('temp/Foo.pdf', 'Foo.pdf');
    }
}
