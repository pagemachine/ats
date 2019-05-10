<?php
namespace PAGEmachine\Ats\Slots\StaticInfoTables;

use PAGEmachine\Ats\Service\IntlLocalizationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class SelectViewHelperSlot
{
    /**
     * Filters Language Items by config
     *
     * @param  array  $arguments
     * @param  array  $items
     * @return array
     */
    public function filterLanguageItems($arguments = [], $items = [])
    {

        if ($arguments['staticInfoTable'] == 'language') {
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            $configurationManager = $objectManager->get(ConfigurationManagerInterface::class);

            $settings = $configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS);

            $allowedLanguages = $settings['allowedStaticLanguages'];

            if (!empty($allowedLanguages)) {
                $uidList = explode(",", $allowedLanguages);

                foreach ($items as $key => $item) {
                    if (!in_array($item->getUid(), $uidList)) {
                        unset($items[$key]);
                    } else {
                        $items[$key]->setNameLocalized(IntlLocalizationService::getInstance()->getLocalizedLanguageName($items[$key]->getIsoCodeA2()));
                    }
                }
            }
        }

        return [
            'arguments' => $arguments,
            'items' => $items,
        ];
    }
}
