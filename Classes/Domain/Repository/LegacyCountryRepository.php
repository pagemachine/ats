<?php
namespace PAGEmachine\Ats\Domain\Repository;

use PAGEmachine\Ats\Service\IntlLocalizationService;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Repository for country data (static info tables) - Legacy version for TYPO3 7 without doctrine
 */
class LegacyCountryRepository
{
    public function findAll()
    {
        $countries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            implode(',', ['uid', 'cn_iso_2', 'cn_iso_3', 'cn_short_en', 'cn_short_local']),
            'static_countries',
            'deleted = 0'
        );

        $localizationService = IntlLocalizationService::getInstance();

        foreach ($countries as $key => $country) {
            $countries[$key]['localizedName'] = $localizationService->getLocalizedRegionName($country['cn_iso_2']) ?: $country['cn_short_local'];
        }

        return $countries;
    }

    /**
     * Finds languages by their respective uids
     *
     * @param array $uids
     * @return array $countries
     */
    public function findCountriesByUids(array $uids = [])
    {
        if (empty($uids)) {
            return [];
        }

        //enforce integer values for uids
        $uids = array_map(function ($value) {
            return (int)$value;
        });

        $countries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            implode(',', ['uid', 'cn_iso_2', 'cn_iso_3', 'cn_short_en', 'cn_short_local']),
            'static_countries',
            'deleted = 0 AND uid IN(' . implode(',', $uids) . ')'
        );

        $localizationService = IntlLocalizationService::getInstance();

        foreach ($countries as $key => $country) {
            $countries[$key]['localizedName'] = $localizationService->getLocalizedRegionName($country['cn_iso_2']) ?: $country['cn_short_local'];
        }

        return $countries;
    }

    /**
     * Finds countries by their respective isoCodes
     *
     * @param array $isoCodes
     * @return array $countries
     */
    public function findCountriesByISO3(array $isoCodes = [])
    {
        if (empty($isoCodes)) {
            return [];
        }

        // clean and quote values
        $isoCodes = array_map(function ($value) {
            return sprintf('"%s"', preg_replace("/([^A-Z]+)/", "", $value));
        }, $isoCodes);

        $countries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
            implode(',', ['uid', 'cn_iso_2', 'cn_iso_3', 'cn_short_en', 'cn_short_local']),
            'static_countries',
            'deleted = 0 AND cn_iso_3 IN(' . implode(',', $isoCodes) . ')'
        );

        $localizationService = IntlLocalizationService::getInstance();

        foreach ($countries as $key => $country) {
            $countries[$key]['localizedName'] = $localizationService->getLocalizedRegionName($country['cn_iso_2']) ?: $country['cn_short_local'];
        }

        return $countries;
    }

    public function findOneByIsoCodeA3($isoCode)
    {
        $country = $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
            implode(',', ['uid', 'cn_iso_2', 'cn_iso_3', 'cn_short_en', 'cn_short_local']),
            'static_countries',
            'deleted = 0 AND uid = ' . preg_replace("/([^A-Z]+)/", "", $isoCode)
        );

        $localizationService = IntlLocalizationService::getInstance();
        $country['localizedName'] = $localizationService->getLocalizedRegionName($country['cn_iso_2']) ?: $country['cn_short_local'];

        return $country;
    }
}
