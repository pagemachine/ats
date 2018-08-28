<?php
namespace PAGEmachine\Ats\Tests\Unit\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Service\AuthenticationService;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUserGroup;
use TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository;

/**
 * Testcase for PAGEmachine\Ats\Service\AuthenticationService
 */
class AuthenticationServiceTest extends UnitTestCase
{
    /**
     * @var AuthenticationService
     */
    protected $authenticationService;

    /**
     * @var FrontendUserRepository|Prophecy\Prophecy\ObjectProphecy
     */
    protected $frontendUserRepository;

    /**
     * @var FrontendUser|Prophecy\Prophecy\ObjectProphecy
     */
    protected $frontendUser;


    /**
     * Set up this testcase
     */
    protected function setUp()
    {
        $this->authenticationService = new AuthenticationService;

        $this->frontendUser = $this->prophesize(FrontendUser::class);

        $this->frontendUserRepository = $this->prophesize(FrontendUserRepository::class);
        $this->frontendUserRepository->findByIdentifier(1)->willReturn($this->frontendUser->reveal());

        $userGroup = $this->prophesize(FrontendUserGroup::class);
        $userGroup->getUid()->willReturn(1);

        $this->frontendUser->getUsergroup()->willReturn([0 => $userGroup->reveal()]);

        $this->inject($this->authenticationService, 'frontendUserRepository', $this->frontendUserRepository->reveal());

        $GLOBALS['TSFE'] = new \stdClass();
        $GLOBALS['TSFE']->fe_user = new \stdClass();
    }

    /**
     * Checks function isUserAuthenticatedAndHasGroup()
     *
     * @test
     * @dataProvider successfulAuthentication
     * @param int $userGroup
     */
    public function detectsUserIsAuthenticated($userGroup)
    {
        $GLOBALS['TSFE']->loginUser = true;
        $GLOBALS['TSFE']->fe_user->user = ['uid' => 1];

        $this->assertTrue($this->authenticationService->isUserAuthenticatedAndHasGroup($userGroup));
    }

    /**
     * @return array
     */
    public function successfulAuthentication()
    {
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
    public function detectsUserIsNotAuthenticated($isUserAuthenticated, $userGroup)
    {

        $this->assertFalse($this->authenticationService->isUserAuthenticatedAndHasGroup($userGroup));
    }

    /**
     * @return array
     */
    public function failingAuthentication()
    {

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
    public function returnsAuthenticatedUser()
    {
        $GLOBALS['TSFE']->loginUser = true;
        $GLOBALS['TSFE']->fe_user->user = ['uid' => 1];

        $this->assertEquals($this->frontendUser->reveal(), $this->authenticationService->getAuthenticatedUser());
    }
}
