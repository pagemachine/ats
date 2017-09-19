<?php
namespace PAGEmachine\Ats\Slots\StaticInfoTables;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;


/*
 * This file is part of the PAGEmachine ATS project.
 */


class SelectViewHelperSlot {

    /**
     * Filters Language Items by config
     *
     * @param  array  $arguments
     * @param  array  $items
     * @return array
     */
    public function filterLanguageItems($arguments = [], $items = []) {
        
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
                    }
                }
            }

        }

        return [
            'arguments' => $arguments,
            'items' => $items
        ];
    }


}
