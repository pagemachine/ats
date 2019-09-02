<?php
namespace PAGEmachine\Ats\Domain\Repository;

use PAGEmachine\Ats\Service\IntlLocalizationService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Repository for country data (static info tables)
 */
class CountryRepository
{
    public function findAll()
    {
        /** @var QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('static_countries');

        $countries = $queryBuilder
            ->select('uid', 'cn_iso_2', 'cn_iso_3', 'cn_short_en', 'cn_short_local')
            ->from('static_countries')
            ->execute()
            ->fetchAll();

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
    public function findCountriesByUids($uids = [])
    {
        /** @var QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('static_countries');

        $countries = $queryBuilder
            ->select('uid', 'cn_iso_2', 'cn_iso_3', 'cn_short_en', 'cn_short_local')
            ->from('static_countries')
            ->where(
                $queryBuilder->expr()->in('uid', $uids)
            )
            ->execute()
            ->fetchAll();

        $localizationService = IntlLocalizationService::getInstance();

        foreach ($countries as $key => $country) {
            $countries[$key]['localizedName'] = $localizationService->getLocalizedRegionName($country['cn_iso_2']) ?: $country['cn_short_local'];
        }

        return $countries;
    }

    public function findCountriesByISO3($isoCodes = [])
    {
        /** @var QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('static_countries');

        $isoCodes = array_map(function ($value) {
            return sprintf('"%s"', trim($value));
        }, $isoCodes);

        $countries = $queryBuilder
            ->select('uid', 'cn_iso_2', 'cn_iso_3', 'cn_short_en', 'cn_short_local')
            ->from('static_countries')
            ->where(
                $queryBuilder->expr()->in('cn_iso_3', $isoCodes)
            )
            ->execute()
            ->fetchAll();

        $localizationService = IntlLocalizationService::getInstance();

        foreach ($countries as $key => $country) {
            $countries[$key]['localizedName'] = $localizationService->getLocalizedRegionName($country['cn_iso_2']) ?: $country['cn_short_local'];
        }

        return $countries;
    }

    public function findOneByIsoCodeA3($isoCode)
    {
        /** @var QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('static_countries');

        $country = $queryBuilder
            ->select('uid', 'cn_iso_2', 'cn_iso_3', 'cn_short_en', 'cn_short_local')
            ->from('static_countries')
            ->where(
                $queryBuilder->expr()->eq('cn_iso_3', $queryBuilder->createNamedParameter($isoCode))
            )
            ->execute()
            ->fetch();

        $localizationService = IntlLocalizationService::getInstance();
        $country['localizedName'] = $localizationService->getLocalizedRegionName($country['cn_iso_2']) ?: $country['cn_short_local'];

        return $country;
    }
}
