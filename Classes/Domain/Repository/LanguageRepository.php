<?php
namespace PAGEmachine\Ats\Domain\Repository;

use PAGEmachine\Ats\Service\IntlLocalizationService;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Repository for language data (static info tables)
 */
class LanguageRepository
{
    public function findAll()
    {
        /** @var QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('static_languages');

        $languages = $queryBuilder
            ->select('uid', 'lg_iso_2', 'lg_name_en', 'lg_name_local')
            ->from('static_languages')
            ->execute()
            ->fetchAll();

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
    public function findLanguagesByUids($uids = [])
    {
        /** @var QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('static_languages');

        $languages = $queryBuilder
            ->select('uid', 'lg_iso_2', 'lg_name_en', 'lg_name_local')
            ->from('static_languages')
            ->where(
                $queryBuilder->expr()->in('uid', $uids)
            )
            ->execute()
            ->fetchAll();

        $localizationService = IntlLocalizationService::getInstance();

        foreach ($languages as $key => $language) {
            $languages[$key]['localizedName'] = $localizationService->getLocalizedLanguageName($language['lg_iso_2']) ?: $language['lg_name_local'];
        }

        return $languages;
    }
}
