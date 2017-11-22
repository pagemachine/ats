<?php
namespace PAGEmachine\Ats\Tests\Unit\Message;

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Repository\TextTemplateRepository;
use PAGEmachine\Ats\Message\AbstractMessage;
use PAGEmachine\Ats\Service\FluidRenderingService;
use PAGEmachine\Ats\Service\MailService;
use PAGEmachine\Ats\Service\MarkerService;
use PAGEmachine\Ats\Service\PdfService;
use Prophecy\Argument;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
 * Testcase for AbstractMessage
 */
class AbstractMessageTest extends UnitTestCase
{
    /**
     * @var AbstractMessage
     */
    protected $abstractMessage;

    /**
     * @var StandaloneView
     */
    protected $standaloneView;

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
        $this->abstractMessage = $this->getMockForAbstractClass(AbstractMessage::class);

        $this->abstractMessage->setSubject("subject with [[marker]]");
        $this->abstractMessage->setSendType(AbstractMessage::SENDTYPE_MAIL);
        $this->abstractMessage->setBody("someText");
        $this->abstractMessage->setCc("cc@foobar.de");
        $this->abstractMessage->setBcc("bcc@foobar.de");


        $this->application = new Application();

        $this->abstractMessage->setApplication($this->application);

        $this->markerService = $this->prophesize(MarkerService::class);
        $this->inject($this->abstractMessage, "markerService", $this->markerService->reveal());

        $this->fluidRenderingService = $this->prophesize(FluidRenderingService::class);
        $this->inject($this->abstractMessage, "fluidRenderingService", $this->fluidRenderingService->reveal());
    }

    /**
     * @test
     */
    public function sendsMail()
    {
        $mailService = $this->prophesize(MailService::class);
        GeneralUtility::setSingletonInstance(MailService::class, $mailService->reveal());

        $this->markerService->replaceMarkers("subject with [[marker]]", MarkerService::CONTEXT_MAIL)->shouldBeCalled()->willReturn("subject with replacedmarker");
        $this->markerService->replaceMarkers("someText", MarkerService::CONTEXT_MAIL)->shouldBeCalled()->willReturn("someText");

        $this->fluidRenderingService->render("subject with replacedmarker", Argument::type("array"))->shouldBeCalled()->willReturn("rendered subject");
        $this->fluidRenderingService->render("someText", Argument::type("array"))->shouldBeCalled()->willReturn("<p>SomeText</p>");

        $mailService->sendReplyMail($this->application, "rendered subject", "<p>SomeText</p>", "cc@foobar.de", "bcc@foobar.de")->shouldBeCalled();

        $this->abstractMessage->send();
    }


    /**
     * @test
     */
    public function generatesPdf()
    {
        $this->abstractMessage->setSendType(AbstractMessage::SENDTYPE_PDF);

        $pdfService = $this->prophesize(PdfService::class);
        GeneralUtility::setSingletonInstance(PdfService::class, $pdfService->reveal());

        $this->markerService->replaceMarkers("someText", MarkerService::CONTEXT_PDF)->shouldBeCalled()->willReturn("someText");
        $this->markerService->replaceMarkers("subject with [[marker]]", MarkerService::CONTEXT_PDF)->shouldBeCalled()->willReturn("subject with replacedmarker");

        $this->fluidRenderingService->render("subject with replacedmarker", Argument::type("array"))->shouldBeCalled()->willReturn("rendered subject");
        $this->fluidRenderingService->render("someText", Argument::type("array"))->shouldBeCalled()->willReturn("<p>SomeText</p>");

        $pdfService->generateAndDownloadPdf("rendered subject", $this->application, "<p>SomeText</p>")->shouldBeCalled();

        $this->abstractMessage->send();
    }

    /**
     * @test
     */
    public function generatePdfAction()
    {
        // mail
        $this->abstractMessage->generatePdf('Foo.pdf');

        // pdf
        $this->abstractMessage->setSendType(AbstractMessage::SENDTYPE_PDF);

        $pdfService = $this->prophesize(PdfService::class);
        GeneralUtility::setSingletonInstance(PdfService::class, $pdfService->reveal());

        $this->standaloneView->assignMultiple(Argument::type("array"))->shouldBeCalled();

        $this->markerService->replaceMarkers("someText", MarkerService::CONTEXT_PDF)->shouldBeCalled()->willReturn("someText");

        $this->standaloneView->setTemplateSource("someText")->shouldBeCalled();
        $this->standaloneView->render()->willReturn("<p>SomeText</p>");

        $pdfService->generatePdf($this->application, "<p>SomeText</p>", 'Foo.pdf')->shouldBeCalled();

        $this->abstractMessage->generatePdf('Foo.pdf');
    }

    /**
     * @test
     */
    public function returnsTemplateDropdownOptions()
    {
        $textTemplateRepository = $this->prophesize(TextTemplateRepository::class);
        $this->inject($this->abstractMessage, "textTemplateRepository", $textTemplateRepository->reveal());

        $templateContainerDummy = new \stdClass();

        $textTemplateRepository->getDropdownOptionsForType(0)->shouldBeCalled()->willReturn($templateContainerDummy);

        $this->assertEquals($templateContainerDummy, $this->abstractMessage->getTextTemplateDropdownOptions());
    }

    /**
     * @test
     */
    public function returnsCachedTemplateDropdownOptions()
    {
        $textTemplateRepository = $this->prophesize(TextTemplateRepository::class);
        $this->inject($this->abstractMessage, "textTemplateRepository", $textTemplateRepository->reveal());

        $templateContainerDummy = new \stdClass();

        $this->abstractMessage->setTextTemplateDropdownOptions($templateContainerDummy);

        $textTemplateRepository->getDropdownOptionsForType(0)->shouldNotBeCalled();

        $this->assertEquals($templateContainerDummy, $this->abstractMessage->getTextTemplateDropdownOptions());
    }
}
