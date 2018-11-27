<?php
namespace PAGEmachine\Ats\Tests\Unit\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Application\ApplicationRating;
use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Testcase for ApplicationRating pseudo-enumeration
 */
class ApplicationRatingTest extends UnitTestCase
{
    /**
     * @var \Prophecy\Prophecy\ProphecyInterface
     */
    protected $typoscriptService;

    /**
     * Set up this testcase
     */
    protected function setUp()
    {
        $this->typoscriptService = $this->prophesize(TyposcriptService::class);

        $this->typoscriptService->getSettings()->willReturn([
            'ratingOptions' => [
                0 => [
                    'name' => 'NONE',
                    'label' => 'LLL:EXT:ats/Resources/Private/locallang.xlf:rating.none',
                ],
                10 => [
                    'name' => 'FOOBAR',
                    'label' => 'LLL:EXT:ats/Resources/Private/locallang.xlf:rating.foobar',
                ],
                20 => [
                    'name' => 'BARBAZ',
                    'label' => 'LLL:EXT:ats/Resources/Private/locallang.xlf:rating.barbaz',
                ],
            ],
        ]);

        GeneralUtility::setSingletonInstance(TyposcriptService::class, $this->typoscriptService->reveal());
    }

    /**
     * @test
     */
    public function loadsEnumValuesByConfig()
    {
        $this->assertEquals(
            [
                'NONE' => 0,
                'FOOBAR' => 10,
                'BARBAZ' => 20,
            ],
            ApplicationRating::getConstants()
        );
    }

    /**
     * @test
     */
    public function allowsConstructorWithoutValue()
    {
        $rating = new ApplicationRating();

        $this->assertTrue($rating->equals(ApplicationRating::__default));
    }

    /**
     * @test
     */
    public function castsValueToDefaultIfInvalid()
    {
        $rating = ApplicationRating::cast(234);

        $this->assertTrue($rating->equals(ApplicationRating::__default));
    }
}
