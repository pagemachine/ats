<?php
namespace PAGEmachine\Ats\Domain\Repository;

use PAGEmachine\Ats\Application\ApplicationQuery;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\EndTimeRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\HiddenRestriction;
use TYPO3\CMS\Core\Database\Query\Restriction\StartTimeRestriction;
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

        // Remove restrictions to include all jobs inside join (since this method is called in BE context)
        // For applications these are not used, but jobs of course may be hidden, time-restricted etc.
        $queryBuilder->getRestrictions()
            ->removeByType(StartTimeRestriction::class)
            ->removeByType(EndTimeRestriction::class)
            ->removeByType(HiddenRestriction::class);

        $count = $queryBuilder->count('application.uid')
            ->from('tx_ats_domain_model_application', 'application')
            ->join(
                'application',
                'tx_ats_domain_model_job',
                'jobtable',
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('jobtable.uid', 'application.job'),
                    $queryBuilder->expr()->eq('jobtable.deactivated', 0)
                )
            )
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

        // Remove restrictions to include all jobs inside join (since this method is called in BE context)
        // For applications these are not used, but jobs of course may be hidden, time-restricted etc.
        $queryBuilder->getRestrictions()
            ->removeByType(StartTimeRestriction::class)
            ->removeByType(EndTimeRestriction::class)
            ->removeByType(HiddenRestriction::class);

        $queryBuilder->select(
            'application.uid AS uid',
            'application.crdate AS crdate',
            'application.tstamp AS tstamp',
            'application.firstname AS firstname',
            'application.surname AS surname',
            'application.job AS job',
            'application.status AS status',
            'application.rating AS rating',
            'application.disability AS disability',
            'application.employed AS employed'
        )->from('tx_ats_domain_model_application', 'application')
        ->join(
            'application',
            'tx_ats_domain_model_job',
            'jobtable',
            $queryBuilder->expr()->andX(
                $queryBuilder->expr()->eq('jobtable.uid', 'application.job'),
                $queryBuilder->expr()->eq('jobtable.deactivated', 0)
            )
        )
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
            $queryBuilder->expr()->eq('anonymized', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
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
        if ($query->getOnlyDeadlineExceeded() == true) {
            $constraints[] = $queryBuilder->expr()->in('job', $queryBuilder->createNamedParameter($this->getExceededJobUids($query->getDeadlineTime()), Connection::PARAM_INT_ARRAY));
        }
        if ($query->getOnlyMyApplications() == true) {
            $constraints[] = $queryBuilder->expr()->in('job', $queryBuilder->createNamedParameter($this->getJobUidsAssignedToCurrentUser(), Connection::PARAM_INT_ARRAY));
        }

        return $constraints;
    }

    protected function getExceededJobUids($deadlineTime)
    {
        /** @var QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_job');

        $queryBuilder->getRestrictions()
            ->removeByType(StartTimeRestriction::class)
            ->removeByType(EndTimeRestriction::class)
            ->removeByType(HiddenRestriction::class);

        $maxEndtime = time() - $deadlineTime;

        $jobs = $queryBuilder
            ->select('uid')
            ->from('tx_ats_domain_model_job')
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('deactivated', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                    $queryBuilder->expr()->gt('endtime', $queryBuilder->createNamedParameter(0, Connection::PARAM_INT)),
                    $queryBuilder->expr()->lt('endtime', $queryBuilder->createNamedParameter($maxEndtime, Connection::PARAM_INT))
                )
            )
            ->execute()
            ->fetchAll();
        return $jobs ? array_map(function ($job) {
            return $job['uid'];
        }, $jobs) : [];
    }

    protected function getJobUidsAssignedToCurrentUser()
    {
        $backendUser = $GLOBALS['BE_USER'];

        /** @var QueryBuilder */
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('tx_ats_domain_model_job');

        $queryBuilder->getRestrictions()
            ->removeByType(StartTimeRestriction::class)
            ->removeByType(EndTimeRestriction::class)
            ->removeByType(HiddenRestriction::class);

        $constraints = [];
        $constraints[] = $queryBuilder->expr()->inSet('user_pa', $backendUser->user['uid']);

        foreach ($backendUser->userGroups as $group) {
            $constraints[] = $queryBuilder->expr()->inSet("department", $group['uid']);
            $constraints[] = $queryBuilder->expr()->inSet("officials", $group['uid']);
            $constraints[] = $queryBuilder->expr()->inSet("contributors", $group['uid']);
        }

        $jobs = $queryBuilder
            ->select('uid')
            ->from('tx_ats_domain_model_job')
            ->where(
                $queryBuilder->expr()->orX(...$constraints)
            )
            ->execute()
            ->fetchAll();


        return $jobs ? array_map(function ($job) {
            return $job['uid'];
        }, $jobs) : [];
    }
}
