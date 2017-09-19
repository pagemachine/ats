<?php
namespace PAGEmachine\Ats\Tests\Unit\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Service\AuthenticationService;
use PAGEmachine\Hairu\Domain\Service\AuthenticationService as HairuAuthenticationService;
use PAGEmachine\Hairu\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Core\Tests\UnitTestCase;

/**
 * Testcase for PAGEmachine\Ats\Service\AuthenticationService
 */
class AuthenticationServiceTest extends UnitTestCase {

    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var HairuAuthenticationService|Prophecy\Prophecy\ObjectProphecy
     */
    protected $hairuAuthenticationService;

    /**
     * @var FrontendUser|Prophecy\Prophecy\ObjectProphecy
     */
    protected $frontendUser;


    /**
     * Set up this testcase
     */
    protected function setUp() {
        $this->authenticationService = new AuthenticationService;

        $this->hairuAuthenticationService = $this->prophesize(HairuAuthenticationService::class);

        $this->frontendUser = $this->prophesize(FrontendUser::class);

        $userGroup = $this->prophesize(FrontendUserGroup::class);
        $userGroup->getUid()->willReturn(1);

        $this->frontendUser->getUsergroup()->willReturn([0 => $userGroup->reveal()]);

        $this->hairuAuthenticationService->getAuthenticatedUser()->willReturn($this->frontendUser->reveal());


    }

    /**
     * Checks function isUserAuthenticatedAndHasGroup()
     * 
     * @test
     * @dataProvider successfulAuthentication
     * @param int $userGroup
     */
    public function detectsUserIsAuthenticated($userGroup) {
        $this->hairuAuthenticationService->isUserAuthenticated()->willReturn(true);

        $this->inject($this->authenticationService, 'hairuAuthenticationService', $this->hairuAuthenticationService->reveal());

        $this->assertTrue($this->authenticationService->isUserAuthenticatedAndHasGroup($userGroup));

    }

    /**
     * @return array
     */
    public function successfulAuthentication() {
        return [
            'no group' => [null],
            'correct group' => [1],
        ];
    }

    /**
     * Checks function isUserAuthenticatedAndHasGroup()
     * 
     * @test
     * @dataProvider failingAuthentication
     * @param bool $isUserAuthenticated
     * @param int $userGroup
     */
    public function detectsUserIsNotAuthenticated($isUserAuthenticated, $userGroup) {
        $this->hairuAuthenticationService->isUserAuthenticated()->willReturn($isUserAuthenticated);

        $this->inject($this->authenticationService, 'hairuAuthenticationService', $this->hairuAuthenticationService->reveal());

        $this->assertFalse($this->authenticationService->isUserAuthenticatedAndHasGroup($userGroup));

    }

    /**
     * @return array
     */
    public function failingAuthentication() {

        return [
            'authenticated, wrong group' => [true, 15],
            'not authenticated, no group' => [false, null],
            'not authenticated, correct group' => [false, 1],
            'not authenticated, wrong group' => [false, 15],
        ];
    }

    /**
     * @test
     */
    public function returnsAuthenticatedUser() {
        $this->inject($this->authenticationService, 'hairuAuthenticationService', $this->hairuAuthenticationService->reveal());
        $this->assertEquals($this->frontendUser->reveal(), $this->authenticationService->getAuthenticatedUser());
    }

}
