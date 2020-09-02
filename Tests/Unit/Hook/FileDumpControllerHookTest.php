<?php
namespace PAGEmachine\Ats\Tests\Unit\Hook;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Hook\FileDumpControllerHook;
use PAGEmachine\Ats\Service\ExtconfService;
use PHPUnit\Framework\TestCase;
use TYPO3\CMS\Backend\FrontendBackendUserAuthentication;
use TYPO3\CMS\Core\Resource\AbstractFile;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication;

/**
 * Testcase for FileDumpControllerHook
 */
class FileDumpControllerHookTest extends TestCase
{
    /**
     * @var FileDumpControllerHook
     */
    protected $fileDumpControllerHook;

    /**
     * Set up this testcase
     */
    protected function setUp()
    {
        $this->fileDumpControllerHook = new FileDumpControllerHook();
    }

    /**
     * Tear down this testcase
     */
    protected function tearDown()
    {
        GeneralUtility::purgeInstances();
    }

    /**
     * @test
     *
     * @dataProvider grantedUserCombinations
     * @param array $application
     * @param FrontendBackendUserAuthentication $beUser
     * @param FrontendUserAuthentication $feUser
     */
    public function grantsAccess($application = [], FrontendBackendUserAuthentication $beUser = null, FrontendUserAuthentication $feUser = null)
    {
        $this->assertTrue($this->fileDumpControllerHook->hasAccess($application, $feUser, $beUser));
    }

    /**
     * @return array
     */
    public function grantedUserCombinations()
    {
        $feUser = $this->prophesize(FrontendUserAuthentication::class);
        $feUser->user = [
            'uid' => '999',
        ];
        $feUser->getKey('ses', 'Ats/Application')->willReturn('999');
        return [
            'BE logged in, FE not logged in' => [[], $this->prophesize(FrontendBackendUserAuthentication::class)->reveal(), null],
            'BE not logged in, FE logged in with application connection' => [['user' => '999'], null, $feUser->reveal()],
            'BE not logged in, no FE login needed, but session set' => [['uid'=>'999' ], null, $feUser->reveal()],
        ];
    }

    /**
     * @test
     *
     * @dataProvider deniedUserCombinations
     * @param array $application
     * @param FrontendBackendUserAuthentication $beUser
     * @param FrontendUserAuthentication $feUser
     */
    public function deniesAccess($application = [], FrontendBackendUserAuthentication $beUser = null, FrontendUserAuthentication $feUser = null)
    {
        $this->assertFalse($this->fileDumpControllerHook->hasAccess($application, $feUser, $beUser));
    }


    /**
     * @return array
     */
    public function deniedUserCombinations()
    {
        $feUser = $this->prophesize(FrontendUserAuthentication::class);
        $feUser->user = [
            'uid' => '999',
        ];
        $feUser->getKey('ses', 'Ats/Application')->willReturn('1');

        return [
            'BE not logged in, FE not logged in' => [[], null, null],
            'BE not logged in, FE logged in but no application connection' => [['user' => '123'], null, $feUser->reveal()],
            'BE not logged in, no FE login needed, but session set' => [['uid'=>'999' ], null, $feUser->reveal()],
        ];
    }

    /**
     * @test
     *
     * @dataProvider fileLocations
     * @param  string $filePath The file path to check
     * @param  bool $returnValue The desired return value
     */
    public function checksIfFileIsInAtsStorage($filePath, $returnValue)
    {
        $extconfService = $this->prophesize(ExtconfService::class);
        $extconfService->getUploadConfiguration()->willReturn([
            'uploadFolder' => '12:/somestorage/',
        ]);

        GeneralUtility::setSingletonInstance(ExtconfService::class, $extconfService->reveal());

        $file = $this->prophesize(AbstractFile::class);
        $file->getCombinedIdentifier()->willReturn($filePath);

        $this->assertEquals($returnValue, $this->fileDumpControllerHook->fileIsInAtsStorage($file->reveal()));
    }

    /**
     * @return array
     */
    public function fileLocations()
    {
        return [
            'file in storage' => ['12:/somestorage/foo.pdf', true],
            'file in storage subfolder' => ['12:/somestorage/someotherstorage/foo.pdf', true],
            'file in different storage' => ['11:/somestorage/foo.pdf', false],
            'file in different folder' => ['12:/non_ats_folder/foo.pdf', false],
        ];
    }
}
