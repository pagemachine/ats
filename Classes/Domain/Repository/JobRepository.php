<?php
namespace PAGEmachine\Ats\Domain\Repository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Persistence\Repository;
use PAGEmachine\Ats\Service\ExtconfService;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * The repository for Jobs
 */
class JobRepository extends Repository
{
    /**
     * Set default orderings on initialization
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->setDefaultOrderings(
            ExtconfService::getInstance()->getJobDefaultOrderings()
        );
    }

    public function findActive()
    {
        $query = $this->createQuery();
        return $query->matching(
            $query->logicalAnd(
                $query->equals('hidden', 0),
                $query->equals('deactivated', false)
            )
        )->execute();
    }

    /**
     * Returns a list of jobs the current user is linked to (as a department, personell staff etc.).
     *
     * @param  BackendUserAuthentication $backendUser
     * @return QueryResultInterface
     */
    public function findByBackendUser(BackendUserAuthentication $backendUser)
    {
        $query = $this->createQuery();
        $constraints = [];

        $constraints[] = $query->contains("userPa", $backendUser->user['uid']);

        foreach ($backendUser->userGroups as $group) {
            $constraints[] = $query->contains("department", $group['uid']);
            $constraints[] = $query->contains("officials", $group['uid']);
            $constraints[] = $query->contains("contributors", $group['uid']);
        }

        $query->matching(
            $query->logicalAnd(
                $query->logicalOr($constraints),
                $query->equals('deactivated', false)
            )
        );

        return $query->execute();
    }


    /**
     * select all jobs which have an end date and are not expired and not deleted
     *
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
     */
    public function findJobsWithEndtimeInFuture()
    {
        $time = time();
        $query = $this->createQuery();

        $query->matching(
            $query->greaterThan(
                'endtime',
                $time
            )
        );

        return $query->execute();
    }
}
