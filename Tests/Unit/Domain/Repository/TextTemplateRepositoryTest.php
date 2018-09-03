<?php
namespace PAGEmachine\Ats\Tests\Unit\Domain\Repository;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Domain\Model\TextTemplate;
use PAGEmachine\Ats\Domain\Repository\TextTemplateRepository;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Testcase for TextTemplateRepository
 */
class TextTemplateRepositoryTest extends UnitTestCase
{
    /**
     * @var TextTemplateRepository
     */
    protected $textTemplateRepository;

    /**
     * Set up this testcase
     */
    protected function setUp()
    {
        $this->textTemplateRepository = $this->getMockBuilder(TextTemplateRepository::class)
            ->disableOriginalConstructor()
            ->setMethods(['findByType'])
            ->getMock();
    }

    /**
     * @test
     */
    public function returnsDropdownOptionsForType()
    {

        $template = $this->prophesize(TextTemplate::class);
        $template->getUid()->willReturn(1);
        $template->getTitle()->willReturn("Title");

        $template2 = $this->prophesize(TextTemplate::class);
        $template2->getUid()->willReturn(2);
        $template2->getTitle()->willReturn("Title2");

        $storage = new ObjectStorage();

        $storage->attach($template->reveal());
        $storage->attach($template2->reveal());


        $this->textTemplateRepository
            ->method("findByType")
            ->with($this->equalTo(1))
            ->willReturn($storage);

        $expectedResult = [1 => 'Title', 2 => 'Title2'];

        $this->assertEquals($expectedResult, $this->textTemplateRepository->getDropdownOptionsForType(1));
    }
}
