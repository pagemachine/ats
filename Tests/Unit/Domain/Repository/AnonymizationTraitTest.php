<?php
namespace PAGEmachine\Ats\Tests\Unit\Domain\Repository;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Domain\Repository\AnonymizationTrait;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * Testcase for AnonymizationTrait
 */
class AnonymizationTraitTest extends UnitTestCase
{
    /**
     * @test
     */
    public function buildsQueryConstraintsforAnonymization()
    {
        $query = $this->prophesize(QueryInterface::class);

        $anonymizationTrait = $this->getMockForTrait(AnonymizationTrait::class);
        $config = [
            'property' => 'foobar',
            'operator' => 'greaterThan',
            'value' => '123',
            'cast' => 'int',
        ];

        $query->greaterThan('foobar', 123)->shouldBeCalled();

        $anonymizationTrait->buildConstraint($query->reveal(), $config);
    }
}
