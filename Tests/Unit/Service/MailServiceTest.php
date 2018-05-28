<?php
namespace PAGEmachine\Ats\Tests\Unit\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Service\ExtconfService;
use PAGEmachine\Ats\Service\FluidRenderingService;
use PAGEmachine\Ats\Service\MailService;
use Prophecy\Argument;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Testcase for PAGEmachine\Ats\Service\MailService
 */
class MailServiceTest extends UnitTestCase
{
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
    protected function setUp()
    {

        $this->backendUser = new BackendUserAuthentication();
        $this->backendUser->user = [
            'email' => 'username@domain.com',
            'realName' => 'Username',
        ];

        $this->application = new Application();
        $this->application->setFirstname("Sherlock");
        $this->application->setSurname("Holmes");
        $this->application->setEmail("sherlock@holmes.com");

        $this->fluidRenderingService = $this->prophesize(FluidRenderingService::class);

        $this->mailService = $this->getMockBuilder(MailService::class)
            ->setConstructorArgs(['backenduser' => $this->backendUser, 'fluidRenderingService' => $this->fluidRenderingService->reveal()])
            ->setMethods(['callStatic', 'fetchSystemFrom'])
            ->getMock();

        $this->mailMessage = $this->prophesize(MailMessage::class);

        $this->mailMessage->setSubject('Foo')->willReturn($this->mailMessage->reveal());
        $this->mailMessage->setBody('Bar', 'text/html')->willReturn($this->mailMessage->reveal());

        $this->mailMessage->setTo(['sherlock@holmes.com' => 'Sherlock Holmes'])->willReturn($this->mailMessage->reveal());

        $this->mailMessage->send()->willReturn();

        $this->mailService->method('callStatic')->with(GeneralUtility::class, 'makeInstance', MailMessage::class)->willReturn($this->mailMessage->reveal());
    }

    public function tearDown()
    {
        GeneralUtility::purgeInstances();
    }

    /**
     * @test
     */
    public function setsAllFieldsWithUserData()
    {

        $this->mailMessage->setFrom(['username@domain.com' => 'Username'])->willReturn($this->mailMessage->reveal())->shouldBeCalled();
        $this->fluidRenderingService->renderTemplate('Mail/Html', Argument::type('array'))->shouldBeCalled()->willReturn('Bar');

        $this->mailService->sendReplyMail($this->application, "Foo", "Bar");
    }

    /**
     * @test
     */
    public function setsAllFieldsWithSystemData()
    {


        //Unset user and expect system settings
        $this->backendUser->user = [];
        $this->mailService->expects($this->once())->method('fetchSystemFrom')->willReturn(['system@domain.com' => 'System Name']);

        $this->mailMessage->setFrom(['system@domain.com' => 'System Name'])->willReturn($this->mailMessage->reveal())->shouldBeCalled();
        $this->fluidRenderingService->renderTemplate('Mail/Html', Argument::type('array'))->shouldBeCalled()->willReturn('Bar');

        $this->mailService->sendReplyMail($this->application, "Foo", "Bar");
    }

    /**
     * @test
     */
    public function setsCc()
    {

        $this->mailMessage->setFrom(['username@domain.com' => 'Username'])->willReturn($this->mailMessage->reveal())->shouldBeCalled();
        $this->mailMessage->setCc('cc@domain.com')->shouldBeCalled();

        $this->fluidRenderingService->renderTemplate('Mail/Html', Argument::type('array'))->shouldBeCalled()->willReturn('Bar');

        $this->mailService->sendReplyMail($this->application, "Foo", "Bar", "cc@domain.com");
    }

    /**
     * @test
     */
    public function setsBcc()
    {

        $this->mailMessage->setFrom(['username@domain.com' => 'Username'])->willReturn($this->mailMessage->reveal())->shouldBeCalled();
        $this->mailMessage->setBcc('bcc@domain.com')->shouldBeCalled();

        $this->fluidRenderingService->renderTemplate('Mail/Html', Argument::type('array'))->shouldBeCalled()->willReturn('Bar');

        $this->mailService->sendReplyMail($this->application, "Foo", "Bar", "", "bcc@domain.com");
    }

    /**
     * @test
     *
     * @dataProvider mailCombinations
     */
    public function fetchesCorrectFrom($useBeUserCredentials, $backendUserRecord, $atsSystemName, $atsSystemAddress, $systemFrom, $expectedFrom)
    {
        $this->backendUser->user = $backendUserRecord;

        $extconfService = $this->prophesize(ExtconfService::class);
        $extconfService->getEmailDefaultSenderName()->willReturn($atsSystemName);
        $extconfService->getEmailDefaultSenderAddress()->willReturn($atsSystemAddress);

        GeneralUtility::setSingletonInstance(ExtconfService::class, $extconfService->reveal());

        $this->mailService->method('fetchSystemFrom')->will($this->returnValue($systemFrom));

        $this->assertEquals(
            $expectedFrom,
            $this->mailService->fetchFrom($useBeUserCredentials)
        );
    }

    /**
     * @return array
     */
    public function mailCombinations()
    {
        return [
            'backend user data allowed, backend user valid' => [
                true,
                [
                    'email' => 'beuser@example.com',
                    'realName' => 'BackendUser',
                ],
                'ATS',
                'ats@example.com',
                ['system@example.com' => 'System'],
                ['beuser@example.com' => 'BackendUser'],
            ],
            'backend user data allowed, backend user email invalid' => [
                true,
                [
                    'email' => 'invalid',
                    'realName' => 'backend user',
                ],
                'ATS',
                'ats@example.com',
                ['system@example.com' => 'System'],
                ['ats@example.com' => 'ATS'],
            ],
            'backend user data allowed, backend user name invalid' => [
                true,
                [
                    'email' => 'beuser@example.com',
                    'realName' => '',
                ],
                'ATS',
                'ats@example.com',
                ['system@example.com' => 'System'],
                ['ats@example.com' => 'ATS'],
            ],
            'backend user data allowed, backend user email invalid, ATS email invalid' => [
                true,
                [
                    'email' => 'invalid',
                    'realName' => 'backend user',
                ],
                'ATS',
                'invalid',
                ['system@example.com' => 'System'],
                ['system@example.com' => 'System'],
            ],
            'backend user data allowed, backend user email invalid, ATS name invalid' => [
                true,
                [
                    'email' => 'invalid',
                    'realName' => 'backend user',
                ],
                '',
                'ats@example.com',
                ['system@example.com' => 'System'],
                ['system@example.com' => 'System'],
            ],
            'backend user data allowed, backend user name invalid, ATS email invalid' => [
                true,
                [
                    'email' => 'beuser@example.com',
                    'realName' => '',
                ],
                'ATS',
                'invalid',
                ['system@example.com' => 'System'],
                ['system@example.com' => 'System'],
            ],
            'backend user data allowed, backend user name invalid, ATS name invalid' => [
                true,
                [
                    'email' => 'beuser@example.com',
                    'realName' => '',
                ],
                '',
                'ats@example.com',
                ['system@example.com' => 'System'],
                ['system@example.com' => 'System'],
            ],
            'backend user data not allowed' => [
                false,
                [
                    'email' => 'beuser@example.com',
                    'realName' => 'backend user',
                ],
                'ATS',
                'ats@example.com',
                ['system@example.com' => 'System'],
                ['ats@example.com' => 'ATS'],
            ],
        ];
    }
}
