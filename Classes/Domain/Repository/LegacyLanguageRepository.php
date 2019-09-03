<?php
namespace PAGEmachine\Ats\Domain\Repository;

use PAGEmachine\Ats\Service\IntlLocalizationService;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Repository for language data (static info tables) - V7 version without doctrine usage
 * @todo drop once TYPO3 7 support is dropped (V2)
 */
class LegacyLanguageRepository
{
    public function findAll()
    {
        $languages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            implode(',', ['uid', 'lg_iso_2', 'lg_name_en', 'lg_name_local']),
            'static_languages',
            'deleted = 0'
        );

        $localizationService = IntlLocalizationService::getInstance();

        foreach ($languages as $key => $language) {
            $languages[$key]['localizedName'] = $localizationService->getLocalizedLanguageName($language['lg_iso_2']) ?: $language['lg_name_local'];
        }

        return $languages;
    }

    /**
     * Finds languages by their respective uids
     *
     * @param array $uids
     * @return array $languages
     */
    public function findLanguagesByUids(array $uids = [])
    {
        if (empty($uids)) {
            return [];
        }

        //enforce integer values for uids
        $uids = array_map(function($value) {
            return (int)$value;
        });

        $languages = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            implode(',', ['uid', 'lg_iso_2', 'lg_name_en', 'lg_name_local']),
            'static_languages',
            'deleted = 0 AND uid IN(' . implode(',', $uids) . ')'
        );

        $localizationService = IntlLocalizationService::getInstance();

        foreach ($languages as $key => $language) {
            $languages[$key]['localizedName'] = $localizationService->getLocalizedLanguageName($language['lg_iso_2']) ?: $language['lg_name_local'];
        }

        return $languages;
    }
}
