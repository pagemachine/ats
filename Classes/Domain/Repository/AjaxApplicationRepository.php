<?php
namespace PAGEmachine\Ats\Domain\Repository;

use PAGEmachine\Ats\Application\ApplicationQuery;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * AJAX application repository
 * Loads raw application data for increased backend speed
 */
class AjaxApplicationRepository
{
    public function getTotalResultsOfQuery(ApplicationQuery $query)
    {
        /** @var QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_application');

        $count = $queryBuilder->count('uid')
            ->from('tx_ats_domain_model_application')
            ->where(
                ...$this->buildQueryConstraints($query, $queryBuilder)
            )->execute()
            ->fetchColumn(0);

        return $count;
    }

    public function findWithQuery(ApplicationQuery $query)
    {
        /** @var QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_application');

        $queryBuilder->select(
            'uid',
            'crdate',
            'tstamp',
            'firstname',
            'surname',
            'job',
            'status',
            'rating',
            'disability',
            'employed'
        )->from('tx_ats_domain_model_application')
        ->setFirstResult(
            $query->getOffset()
        )
        ->setMaxResults(
            $query->getLimit()
        )
        ->orderBy(
            $query->getOrderBy(),
            $query->getOrderDirection()
        );

        $queryBuilder->where(...$this->buildQueryConstraints($query, $queryBuilder));

        $applications = $queryBuilder->execute()
            ->fetchAll();
        return $applications;
    }

    protected function buildQueryConstraints(ApplicationQuery $query, QueryBuilder $queryBuilder)
    {
        $constraints = [
            $queryBuilder->expr()->eq('anonymized', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT))
        ];

        if ($query->getJob() !== null) {
            $constraints[] = $queryBuilder->expr()->eq('job', $queryBuilder->createNamedParameter((int)$query->getJob(), Connection::PARAM_INT));
        }

        if (!empty($query->getStatusValues())) {
            $constraints[] = $queryBuilder->expr()->in('status', $query->getStatusValues());
        }

        if (!empty($query->getSearch())) {
            $searchExpression = $queryBuilder->createNamedParameter("%" . $query->getSearch() . "%", Connection::PARAM_STR);
            $constraints[] = $queryBuilder->expr()->orX(
                $queryBuilder->expr()->like('uid', $searchExpression),
                $queryBuilder->expr()->like('title', $searchExpression),
                $queryBuilder->expr()->like('firstname', $searchExpression),
                $queryBuilder->expr()->like('surname', $searchExpression),
                $queryBuilder->expr()->like('email', $searchExpression)
            );
        }

        return $constraints;
    }
}
