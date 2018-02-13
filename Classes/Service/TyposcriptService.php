<?php
namespace PAGEmachine\Ats\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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

    public function __construct()
    {
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);
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
}
