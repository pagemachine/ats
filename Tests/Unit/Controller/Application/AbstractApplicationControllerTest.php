<?php
namespace PAGEmachine\Ats\Tests\Unit\Controller\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Controller\Application\AbstractApplicationController;
use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Domain\Repository\ApplicationRepository;
use PAGEmachine\Ats\Service\AuthenticationService;
use Prophecy\Argument;
use TYPO3\CMS\Core\Tests\UnitTestCase;
use TYPO3\CMS\Extbase\Mvc\Controller\Argument as ControllerArgument;
use TYPO3\CMS\Extbase\Mvc\Controller\Arguments;
use TYPO3\CMS\Extbase\Mvc\Controller\Exception\RequiredArgumentMissingException;
use TYPO3\CMS\Extbase\Mvc\Controller\MvcPropertyMappingConfiguration;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Request;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Property\TypeConverter\DateTimeConverter;

/**
 * Testcase for PAGEmachine\Ats\Controller\AbstractApplicationController
 */
class AbstractApplicationControllerTest extends UnitTestCase {

    /**
     * @var AbstractApplicationController
     */
    protected $controller;

    /**
     * @var ViewInterface|Prophecy\Prophecy\ObjectProphecy
     */
    protected $view;

    /**
     * Set up this testcase
     */
    protected function setUp() {

        $this->controller = $this->getMockBuilder(AbstractApplicationController::class)->setMethods([
            'redirectToUri',
            'setPropertyMappingConfigurationForApplication'
        ])->getMock();

        $this->view = $this->prophesize(ViewInterface::class);
        $this->inject($this->controller, 'view', $this->view->reveal());
    }

    /**
     * @test
     * @dataProvider validRequestArguments
     * @param bool $hasJob
     * @param string $job
     * @param bool $hasApplication
     * @param mixed $application
     */
    public function redirectsToLoginUriIfNotAuthenticated($hasJob, $job, $hasApplication, $application) {

        $this->inject($this->controller, 'settings', [
            'loginPage' => 2,
        ]);

        $authenticationService = $this->prophesize(AuthenticationService::class);
        $authenticationService->isUserAuthenticatedAndHasGroup(NULL)->willReturn(FALSE);
        $this->inject($this->controller, 'authenticationService', $authenticationService->reveal());

        $request = $this->prophesize(Request::class);

        $request->hasArgument('job')->willReturn($hasJob);
        $request->getArgument('job')->willReturn($job);
        $request->hasArgument('application')->willReturn($hasApplication);
        $request->getArgument('application')->willReturn($application);

        $this->inject($this->controller, 'request', $request->reveal());

        $applicationRepository = $this->prophesize(ApplicationRepository::class);
        $application = $this->prophesize(Application::class);
        $application->getJob()->willReturn('123');
        $applicationRepository->findByUid('234')->willReturn($application->reveal());
        $this->inject($this->controller, 'applicationRepository', $applicationRepository->reveal());

        $uriBuilder = $this->prophesize(UriBuilder::class);
        $uriBuilder->setCreateAbsoluteUri(TRUE)->willReturn($uriBuilder->reveal());

        $uriBuilder->uriFor('show', ['job' => 123], 'Job')->willReturn('test/uri/');
        $uriBuilder->uriFor('form', ['job' => 123], 'Application\\Form')->willReturn('http://example.org/application/foo');

        $uriBuilder->reset()->willReturn($uriBuilder->reveal());
        $uriBuilder->setTargetPageUid(2)->willReturn($uriBuilder->reveal());

        $uriBuilder->setArguments(['return_url' => 'http://example.org/application/foo', 'referrer' => 'test/uri/'])->willReturn($uriBuilder->reveal());
        $uriBuilder->build()->willReturn('login/uri/');

        $this->inject($this->controller, 'uriBuilder', $uriBuilder->reveal());

        $this->controller->expects($this->once())->method('redirectToUri')->with('login/uri/');

        $this->controller->initializeAction();
    }

    /**
     * @test
     */
    public function throwsErrorIfJobArgumentIsMissing() {

        $authenticationService = $this->prophesize(AuthenticationService::class);
        $authenticationService->isUserAuthenticatedAndHasGroup(Argument::any())->willReturn(FALSE);
        $this->inject($this->controller, 'authenticationService', $authenticationService->reveal());


        $request = $this->prophesize(Request::class);
        $request->hasArgument('job')->willReturn(FALSE);
        $request->hasArgument('application')->willReturn(FALSE);
        $request->getControllerObjectName()->willReturn("PAGEmachine\Ats\Controller\ApplicationController");
        $request->getControllerActionName()->willReturn("form");

        $this->inject($this->controller, 'request', $request->reveal());


        $this->expectException(RequiredArgumentMissingException::class);
        $this->expectExceptionCode(1298012500);

        $this->controller->initializeAction();

    }

    /**
     * @return array
     */
    public function validRequestArguments() {
        return [
            'job present' => [true, '123', false, null],
            'no job, but application id' => [false, null, true, '234'],
            'no job, but application array with job id' => [false, null, true, ['job' => '123']]
        ];

    }

    /**
     * @test
     */
    public function setsTypeConverterOptionForDateTime() {

        $this->controller = $this->getMockBuilder(AbstractApplicationController::class)->setMethods([
            'redirectToUri'
        ])->getMock();
        $this->inject($this->controller, 'view', $this->view->reveal());

        $request = $this->prophesize(Request::class);
        $request->hasArgument('application')->willReturn(true);

        $this->inject($this->controller, 'request', $request->reveal());

        //Authenticate. This is not part of the test
        $authenticationService = $this->prophesize(AuthenticationService::class);
        $authenticationService->isUserAuthenticatedAndHasGroup(Argument::any())->willReturn(true);
        $this->inject($this->controller, 'authenticationService', $authenticationService->reveal());

        $arguments = $this->prophesize(Arguments::class);
        $argument = $this->prophesize(ControllerArgument::class);
        $configuration = $this->prophesize(MvcPropertyMappingConfiguration::class);

        $argument->getPropertyMappingConfiguration()->willReturn($configuration->reveal());
        $arguments->getArgument("application")->willReturn($argument->reveal());
        $configuration->forProperty('birthday')->willReturn($configuration->reveal());

        $this->inject($this->controller, "arguments", $arguments->reveal());

        $configuration->setTypeConverterOption(DateTimeConverter::class, DateTimeConverter::CONFIGURATION_DATE_FORMAT, 'Y-m-d')->shouldBeCalled();

        $this->controller->initializeAction();


    }
}
