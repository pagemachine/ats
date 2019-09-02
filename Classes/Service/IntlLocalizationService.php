<?php
namespace PAGEmachine\Ats\Service;

use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */


class IntlLocalizationService implements SingletonInterface
{
    /**
     * @return IntlLocalizationService
     */
    public static function getInstance()
    {
        return GeneralUtility::makeInstance(__CLASS__);
    }

    /**
     * Returns a intl translation of given language code or NULL if intl is not present
     *
     * @param  string $isoCodeA2
     * @return string|null
     */
    public function getLocalizedLanguageName($isoCodeA2)
    {
        if (extension_loaded('intl')) {
            return \Locale::getDisplayName(\Locale::composeLocale([
                'language' => $isoCodeA2,
            ]), $this->getActiveLocale());
        }
        return null;
    }

    public function getLocalizedRegionName($locale)
    {
        if (extension_loaded('intl')) {
            return \Locale::getDisplayRegion('-' . $locale, $this->getActiveLocale());
        }
        return null;
    }

    protected function getActiveLocale()
    {
        if (TYPO3_MODE == 'BE') {
            return $GLOBALS['BE_USER']->uc['lang'] ?: 'en';
        }
        return $GLOBALS['TSFE']->sys_language_isocode ?: 'en';
    }
}
