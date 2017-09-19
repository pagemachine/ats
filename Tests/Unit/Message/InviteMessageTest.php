<?php
namespace PAGEmachine\Ats\Tests\Unit\Message;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Message\AbstractMessage;
use PAGEmachine\Ats\Message\InviteMessage;
use PAGEmachine\Ats\Service\MailService;
use PAGEmachine\Ats\Service\MarkerService;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
 * Testcase for InviteMessage
 */
class InviteMessageTest extends UnitTestCase
{
    /**
     *
     * @var InviteMessage
     */
    protected $inviteMessage;

    /**
     * @var Application
     */
    protected $application;
    /**
     * @var MarkerService
     */
    protected $markerService;

    /**
     * Set up this testcase
     */
    protected function setUp()
    {
        $this->inviteMessage = new InviteMessage();

        $this->inviteMessage->setSubject("subject");
        $this->inviteMessage->setSendType(AbstractMessage::SENDTYPE_MAIL);
        $this->inviteMessage->setBody("someText");
        $this->inviteMessage->setCc("cc@foobar.de");
        $this->inviteMessage->setBcc("bcc@foobar.de");

        $this->application = new Application();

        $this->inviteMessage->setApplication($this->application);

        $this->standaloneView = $this->prophesize(StandaloneView::class);
        $objectManager = $this->prophesize(ObjectManager::class);

        $objectManager->get(StandaloneView::class)->willReturn($this->standaloneView->reveal());

        $this->inject($this->inviteMessage, "objectManager", $objectManager->reveal());

        $this->markerService = $this->prophesize(MarkerService::class);
        $this->inject($this->inviteMessage, "markerService", $this->markerService->reveal());
    }

    /**
      * @test
      */
    public function returnsCustomProperties()
    {

        $mailService = $this->prophesize(MailService::class);
        GeneralUtility::setSingletonInstance(MailService::class, $mailService->reveal());

        $GLOBALS['BE_USER'] = new BackendUserAuthentication();
        $GLOBALS['BE_USER']->user = ['username' => 'Harry'];

        $date = new \DateTime();
        $this->inviteMessage->setDateTime($date);
        $confirmDate = new \DateTime();
        $this->inviteMessage->setConfirmDate($confirmDate);
        $this->inviteMessage->setBuilding("A building");
        $this->inviteMessage->setRoom("A room");

        $this->standaloneView->assignMultiple([
           "application" => $this->application,
           "backenduser" => ['username' => 'Harry'],
           "fields" => [
               'date' => $date->format("Y-m-d"),
               'time' => $date->format("H:i"),
               'confirmDate' => $confirmDate->format("Y-m-d"),
               'building' => 'A building',
               'room' => 'A room',
           ],
        ])->shouldBeCalled();

        $this->markerService->replaceMarkers("someText", MarkerService::CONTEXT_MAIL)->shouldBeCalled()->willReturn("someText");

        $mailService->sendReplyMail($this->application, "subject", "<p>SomeText</p>", "cc@foobar.de", "bcc@foobar.de")->shouldBeCalled();

        $this->standaloneView->setTemplateSource("someText")->shouldBeCalled();
        $this->standaloneView->render()->willReturn("<p>SomeText</p>");

        $this->inviteMessage->send();
    }


    public function dateProvider()
    {
        $exampleDatetime = \DateTime::createFromFormat('Y-m-d H:i', '2017-01-01 12:30');
        $exampleDatetime2 = \DateTime::createFromFormat('Y-m-d', '2016-02-02');

        return [
           'Datetimes set' => [$exampleDatetime, $exampleDatetime2, '2017-01-01', '12:30', '2016-02-02'],
           'Datetimes null' => [null, null, null, null, null],
        ];
    }

     /**
       * @test
       * @dataProvider dateProvider
       */
    public function returnsDatesCorrectly($inputDate, $inputConfirmDate, $outputDate, $outputTime, $outputConfirmDate)
    {
        $this->inviteMessage = new InviteMessage();
        $this->inviteMessage->setDateTime($inputDate);
        $this->inviteMessage->setConfirmDate($inputConfirmDate);

        $this->assertEquals($outputDate, $this->inviteMessage->getDate());
        $this->assertEquals($outputTime, $this->inviteMessage->getTime());
        $this->assertEquals($outputConfirmDate, $this->inviteMessage->getConfirmDateString());
    }
}
