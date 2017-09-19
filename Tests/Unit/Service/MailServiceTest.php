<?php
namespace PAGEmachine\Ats\Tests\Unit\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Service\MailService;
use Prophecy\Argument;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Testcase for PAGEmachine\Ats\Service\MailService
 */
class MailServiceTest extends UnitTestCase {

    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var MailMessage
     */
    protected $mailMessage;

    /**
     *
     * @var BackendUserAuthentication
     */
    protected $backendUser;

    /**
     *
     * @var Application
     */
    protected $application;


    /**
     * Set up this testcase
     */
    protected function setUp() {

        $this->backendUser = new BackendUserAuthentication();
        $this->backendUser->user = [
            'email' => 'username@domain.com',
            'realName' => 'Username'
        ];

        $this->application = new Application();
        $this->application->setFirstname("Sherlock");
        $this->application->setSurname("Holmes");
        $this->application->setEmail("sherlock@holmes.com");

        $this->mailService = $this->getMockBuilder(MailService::class)
            ->setConstructorArgs(['backenduser' => $this->backendUser])
            ->setMethods(['callStatic', 'fetchSystemFrom'])
            ->getMock();

        $this->mailMessage = $this->prophesize(MailMessage::class);

        $this->mailMessage->setSubject('Foo')->willReturn($this->mailMessage->reveal())->shouldBeCalled();
        $this->mailMessage->setBody('Bar', 'text/html')->willReturn($this->mailMessage->reveal())->shouldBeCalled();

        $this->mailMessage->setTo(['sherlock@holmes.com' => 'Sherlock Holmes'])->willReturn($this->mailMessage->reveal())->shouldBeCalled();

        $this->mailMessage->send()->shouldBeCalled();

        $this->mailService->method('callStatic')->with(GeneralUtility::class, 'makeInstance', MailMessage::class)->willReturn($this->mailMessage->reveal());
    }

    /**
     * @test
     */
    public function setsAllFieldsWithUserData() {

        $this->mailMessage->setFrom(['username@domain.com' => 'Username'])->willReturn($this->mailMessage->reveal())->shouldBeCalled();

        $this->mailService->sendReplyMail($this->application, "Foo", "Bar");

    }

    /**
     * @test
     */
    public function setsAllFieldsWithSystemData() {
        

        //Unset user and expect system settings
        $this->backendUser->user = [];
        $this->mailService->expects($this->once())->method('fetchSystemFrom')->willReturn(['system@domain.com' => 'System Name']);

        $this->mailMessage->setFrom(['system@domain.com' => 'System Name'])->willReturn($this->mailMessage->reveal())->shouldBeCalled();

        $this->mailService->sendReplyMail($this->application, "Foo", "Bar");

    }

    /**
     * @test
     */
    public function setsCc() {

        $this->mailMessage->setFrom(['username@domain.com' => 'Username'])->willReturn($this->mailMessage->reveal())->shouldBeCalled();
        $this->mailMessage->setCc('cc@domain.com')->shouldBeCalled();

        $this->mailService->sendReplyMail($this->application, "Foo", "Bar", "cc@domain.com");
    }

    /**
     * @test
     */
    public function setsBcc() {

        $this->mailMessage->setFrom(['username@domain.com' => 'Username'])->willReturn($this->mailMessage->reveal())->shouldBeCalled();
        $this->mailMessage->setBcc('bcc@domain.com')->shouldBeCalled();

        $this->mailService->sendReplyMail($this->application, "Foo", "Bar", "", "bcc@domain.com");
    }

}
