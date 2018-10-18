<?php
namespace PAGEmachine\Ats\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class TyposcriptService implements SingletonInterface
{
    /**
     * @var ConfigurationManagerInterface $configurationManager
     */
    protected $configurationManager;


    /**
     * @codeCoverageIgnore
     * @return TyposcriptService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    public function __construct(ObjectManager $objectManager = null)
    {
        $objectManager = $objectManager ?: GeneralUtility::makeInstance(ObjectManager::class);
        $this->configurationManager = $objectManager->get(ConfigurationManagerInterface::class);
    }

    /**
     * Shorthand function to retrieve TypoScript settings outside of the controllers.
     * @return array
     */
    public function getSettings()
    {
        return $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'ats'
        );
    }

    /**
     * Shorthand function to retrieve TypoScript framework configuration outside of the controllers.
     * @return array
     */
    public function getFrameworkConfiguration()
    {
        return $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK,
            'ats'
        );
    }

    /**
     * Merges global TypoScript and FlexForm settings depending on config (override, override of empty values).
     *
     * @return array
     */
    public function mergeFlexFormAndTypoScriptSettings($settings = [])
    {
        if (!empty($settings['flexForm']) && intval($settings['flexForm']['override']) == 1) {
            $overrideSettings = $settings['flexForm'];

            ArrayUtility::mergeRecursiveWithOverrule(
                $settings,
                $overrideSettings,
                true,
                (intval($overrideSettings['overrideEmptyValues']) == 1 ? true : false)
            );
        }
        unset($settings['flexForm']);

        return $settings;
    }
}
