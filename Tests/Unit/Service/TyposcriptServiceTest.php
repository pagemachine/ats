<?php
namespace PAGEmachine\Ats\Tests\Unit\Service;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Testcase for PAGEmachine\Ats\Service\TyposcriptService
 */
class TyposcriptServiceTest extends UnitTestCase
{
    /**
     *
     * @var TyposcriptService
     */
    protected $typoscriptService;


    /**
     * Setup
     */
    protected function setUp()
    {
        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->get(ConfigurationManagerInterface::class)->willReturn(new \StdClass());

        $this->typoscriptService = new TyposcriptService($objectManager->reveal());
    }

    /**
     * @test
     */
    public function mergesTSSettingsWithNonEmptyFlexSettings()
    {
        $settings = [
            'foo' => 'bar',
            'bar' => 'baz',
            'flexForm' => [
                'override' => '1',
                'foo' => 'overrideBar',
                'bar' => '',
            ],
        ];

        $expectedSettings = [
            'foo' => 'overrideBar',
            'bar' => 'baz',
            'override' => '1',
        ];

        $this->assertEquals(
            $expectedSettings,
            $this->typoscriptService->mergeFlexFormAndTypoScriptSettings($settings)
        );
    }


    /**
     * @test
     */
    public function mergesTSSettingsWithEmptyFlexSettings()
    {
        $settings = [
            'foo' => 'bar',
            'bar' => 'baz',
            'flexForm' => [
                'override' => '1',
                'overrideEmptyValues' => '1',
                'foo' => 'overrideBar',
                'bar' => '',
            ],
        ];

        $expectedSettings = [
            'foo' => 'overrideBar',
            'bar' => '',
            'override' => '1',
            'overrideEmptyValues' => '1',
        ];

        $this->assertEquals(
            $expectedSettings,
            $this->typoscriptService->mergeFlexFormAndTypoScriptSettings($settings)
        );
    }

    /**
     * @test
     */
    public function keepsTSSettingsIfFlexformIsDisabled()
    {
        $settings = [
            'foo' => 'bar',
            'bar' => 'baz',
            'flexForm' => [
                'override' => '0',
                'overrideEmptyValues' => '0',
                'foo' => 'overrideBar',
                'bar' => '',
            ],
        ];

        $expectedSettings = [
            'foo' => 'bar',
            'bar' => 'baz',
        ];

        $this->assertEquals(
            $expectedSettings,
            $this->typoscriptService->mergeFlexFormAndTypoScriptSettings($settings)
        );
    }
}
