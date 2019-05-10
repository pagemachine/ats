<?php
namespace PAGEmachine\Ats\Tests\Unit\Slots\StaticInfoTables;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use PAGEmachine\Ats\Service\IntlLocalizationService;
use PAGEmachine\Ats\Slots\StaticInfoTables\SelectViewHelperSlot;
use SJBR\StaticInfoTables\Domain\Model\Language;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\DomainObject\AbstractDomainObject;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Testcase for SelectViewHelperSlot
 */
class SelectViewHelperSlotTest extends UnitTestCase
{
    /**
     * @var SelectViewHelperSlot
     */
    protected $selectViewHelperSlot;

    /**
    * Set up this testcase
    */
    public function setUp()
    {

        $this->selectViewHelperSlot = new SelectViewHelperSlot();
    }

    /**
     * @test
     */
    public function limitsItemsBasedOnSettings()
    {

        $configurationManager = $this->prophesize(ConfigurationManagerInterface::class);
        $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS)
        ->willReturn([

            'allowedStaticLanguages' => '1,2,3',
            ]);

        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->get(ConfigurationManagerInterface::class)->willReturn($configurationManager->reveal());

        GeneralUtility::setSingletonInstance(ObjectManager::class, $objectManager->reveal());

        $intlLocalizationService = $this->prophesize(IntlLocalizationService::class);
        $intlLocalizationService->getLocalizedLanguageName('de')->willReturn('deutsch');
        GeneralUtility::setSingletonInstance(IntlLocalizationService::class, $intlLocalizationService->reveal());

        $allowedLanguage = $this->prophesize(Language::class);
        $allowedLanguage->getUid()->willReturn(2);
        $allowedLanguage->getIsoCodeA2()->willReturn('de');
        $allowedLanguage->setNameLocalized('deutsch')->shouldBeCalled();

        $filteredLanguage = $this->prophesize(Language::class);
        $filteredLanguage->getUid()->willReturn(5);

        $arguments = ['staticInfoTable' => 'language'];
        $items = [$allowedLanguage->reveal(), $filteredLanguage->reveal()];

        $result = $this->selectViewHelperSlot->filterLanguageItems($arguments, $items);

        $this->assertEquals($result['items'], [$allowedLanguage->reveal()]);
    }

    /**
     * @test
     */
    public function onlyHandlesLanguageSelects()
    {

        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->get(ConfigurationManagerInterface::class)->shouldNotBeCalled();

        GeneralUtility::setSingletonInstance(ObjectManager::class, $objectManager->reveal());

        $arguments = ['staticInfoTable' => 'somethingelse'];
        $items = ['item', 'item2', 'item3'];

        $result = $this->selectViewHelperSlot->filterLanguageItems($arguments, $items);

        $this->assertEquals($result['items'], $items);
    }

    /**
     * @test
     */
    public function keepsItemsIfSettingIsEmpty()
    {

        $configurationManager = $this->prophesize(ConfigurationManagerInterface::class);
        $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS)
        ->willReturn([

            'allowedStaticLanguages' => '',
            ]);

        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->get(ConfigurationManagerInterface::class)->willReturn($configurationManager->reveal());

        GeneralUtility::setSingletonInstance(ObjectManager::class, $objectManager->reveal());

        $allowedLanguage = $this->prophesize(AbstractDomainObject::class);
        $allowedLanguage->getUid()->willReturn(2);

        $filteredLanguage = $this->prophesize(AbstractDomainObject::class);
        $filteredLanguage->getUid()->willReturn(5);

        $arguments = ['staticInfoTable' => 'language'];
        $items = [$allowedLanguage->reveal(), $filteredLanguage->reveal()];

        $result = $this->selectViewHelperSlot->filterLanguageItems($arguments, $items);

        $this->assertEquals($result['items'], $items);
    }
}
