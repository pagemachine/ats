<?php
namespace PAGEmachine\Ats\Tests\Unit\Message;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Message\AbstractMessage;
use PAGEmachine\Ats\Message\InviteMessage;
use PAGEmachine\Ats\Service\FluidRenderingService;
use PAGEmachine\Ats\Service\MailService;
use PAGEmachine\Ats\Service\MarkerService;
use PAGEmachine\Ats\Service\TyposcriptService;
use Prophecy\Argument;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     * @var FluidRenderingService
     */
    protected $fluidRenderingService;

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

        $this->markerService = $this->prophesize(MarkerService::class);
        $this->inject($this->inviteMessage, "markerService", $this->markerService->reveal());

        $this->fluidRenderingService = $this->prophesize(FluidRenderingService::class);
        $this->inject($this->inviteMessage, "fluidRenderingService", $this->fluidRenderingService->reveal());

        $typoscriptService = $this->prophesize(TyposcriptService::class);
        $this->inject($this->inviteMessage, "typoscriptService", $typoscriptService->reveal());

        $typoscriptService->getSettings()->willReturn([
            'dateFormat' => 'Y-m-d',
            'timeFormat' => 'H:i',
        ]);
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

        $this->markerService->replaceMarkers("subject", MarkerService::CONTEXT_MAIL)->willReturn("subject");
        $this->markerService->replaceMarkers("someText", MarkerService::CONTEXT_MAIL)->willReturn("someText");

        $this->fluidRenderingService->render("subject", Argument::type("array"))->willReturn("subject");
        $this->fluidRenderingService->render("someText", Argument::type("array"))->willReturn("<p>SomeText</p>");

        $mailService->sendReplyMail($this->application, "subject", "<p>SomeText</p>", "cc@foobar.de", "bcc@foobar.de")->shouldBeCalled();
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
        $this->inviteMessage->setDateTime($inputDate);
        $this->inviteMessage->setConfirmDate($inputConfirmDate);

        $this->assertEquals($outputDate, $this->inviteMessage->getDate());
        $this->assertEquals($outputTime, $this->inviteMessage->getTime());
        $this->assertEquals($outputConfirmDate, $this->inviteMessage->getConfirmDateString());
    }
}
