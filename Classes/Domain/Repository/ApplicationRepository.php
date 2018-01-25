<?php
namespace PAGEmachine\Ats\Domain\Repository;

use PAGEmachine\Ats\Application\ApplicationFilter;
use PAGEmachine\Ats\Application\ApplicationStatus;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * The repository for Jobs
 */
class ApplicationRepository extends AbstractApplicationRepository
{
    /**
     * Adds the constraint for exceeded deadline
     *
     * @param  QueryInterface $query
     * @param  int        $deadlineTime
     * @return array
     */
    protected function getDeadlineExceededConstraint(QueryInterface $query, $deadlineTime = 0)
    {
        $constraints = $query->logicalAnd(
            $query->greaterThan("job.endtime", 0),
            $query->lessThan("job.endtime", (time() - $deadlineTime))
        );
        return $constraints;
    }

    /**
     * Adds filter constraints according to the filter
     *
     * @param  QueryInterface    $query
     * @param  array             $constraints
     * @param  ApplicationFilter $filter
     * @return array
     */
    protected function getFilterConstraints(QueryInterface $query, $constraints = [], ApplicationFilter $filter = null)
    {
        if ($filter != null) {
            if ($filter->getJob() != null) {
                $constraints[] = $query->equals("job", $filter->getJob());
            }
            if (!empty($filter->getSearchword()) && !empty($filter->getSearchfields())) {
                $searchConstraint = [];

                foreach ($filter->getSearchfields() as $field) {
                    $searchConstraint[] = $query->like($field, "%" . $filter->getSearchword() . "%");
                }
                $constraints[] = $query->logicalOr($searchConstraint);
            }
        }

        return $constraints;
    }

    /**
     * Finds all applications with exceeded deadline
     *
     * @param  int $deadlineTime
     * @param  BackendUserAuthentication $backendUser
     * @param  ApplicationFilter $filter
     * @return QueryResultInterface
     */
    public function findDeadlineExceeded($deadlineTime, BackendUserAuthentication $backendUser = null, ApplicationFilter $filter = null)
    {
        $query = $this->createQuery();

        $constraints = [
            $this->getDeadlineExceededConstraint($query, $deadlineTime),
            $query->greaterThan("status", ApplicationStatus::INCOMPLETE),
            $query->lessThan("status", ApplicationStatus::EMPLOYED),
        ];

        if ($backendUser != null) {
            $constraints[] = $this->buildBackendUserRestriction($query, $backendUser);
        }

        $constraints = $this->getFilterConstraints($query, $constraints, $filter);

        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }

    /**
     * Finds all applications that have been submitted freshly
     *
     * @param  int $deadlineTime
     * @param  BackendUserAuthentication $backendUser
     * @param  ApplicationFilter $filter
     * @return QueryResultInterface
     */
    public function findNew($deadlineTime, BackendUserAuthentication $backendUser = null, ApplicationFilter $filter = null)
    {
        $query = $this->createQuery();

        $constraints = [
            $query->logicalNot(
                $this->getDeadlineExceededConstraint($query, $deadlineTime)
            ),
            $query->equals("status", ApplicationStatus::NEW_APPLICATION),
        ];

        if ($backendUser != null) {
            $constraints[] = $this->buildBackendUserRestriction($query, $backendUser);
        }

        $constraints = $this->getFilterConstraints($query, $constraints, $filter);

        $query->matching($query->logicalAnd($constraints));

        return $query->execute();
    }

    /**
     * Finds all applications that have been processed by the personal department and are not finished
     *
     * @param  int $deadlineTime
     * @param  BackendUserAuthentication $backendUser
     * @param  ApplicationFilter $filter
     * @return QueryResultInterface
     */
    public function findInProgress($deadlineTime, BackendUserAuthentication $backendUser = null, ApplicationFilter $filter = null)
    {
        $query = $this->createQuery();

        $constraints = [
            $query->logicalNot(
                $this->getDeadlineExceededConstraint($query, $deadlineTime)
            ),
            $query->greaterThan("status", ApplicationStatus::NEW_APPLICATION),
            $query->lessThan("status", ApplicationStatus::EMPLOYED),
        ];

        if ($backendUser != null) {
            $constraints[] = $this->buildBackendUserRestriction($query, $backendUser);
        }

        $constraints = $this->getFilterConstraints($query, $constraints, $filter);

        $query->matching($query->logicalAnd($constraints));
        return $query->execute();
    }

    /**
     * Finds all archived applications
     *
     * @param  ApplicationFilter $filter
     * @return QueryResult
     */
    public function findArchived(ApplicationFilter $filter = null)
    {

        $query = $this->createQuery();

        $constraints = [
            $query->greaterThanOrEqual("status", ApplicationStatus::EMPLOYED),
        ];
        $constraints = $this->getFilterConstraints($query, $constraints, $filter);

        $query->matching(
            $query->logicalAnd($constraints)
        );

        return $query->execute();
    }

    /**
     * Finds all pooled applications
     *
     * @param  ApplicationFilter $filter
     * @return QueryResult
     */
    public function findPooled(ApplicationFilter $filter = null)
    {

        $query = $this->createQuery();

        $constraints = [
            $query->greaterThanOrEqual("status", ApplicationStatus::EMPLOYED),
            $query->equals("pool", true),
        ];

        $constraints = $this->getFilterConstraints($query, $constraints, $filter);

        $query->matching(
            $query->logicalAnd(
                $constraints
            )
        );

        return $query->execute();
    }

    /**
     * Finds all applications for mass notification view
     *
     * @param  ApplicationFilter $filter
     * @return QueryResult
     */
    public function findNotification(ApplicationFilter $filter = null)
    {

        $query = $this->createQuery();

        $constraints = [
            $query->lessThan("status", ApplicationStatus::EMPLOYED),
            $query->logicalNot($query->equals("status", ApplicationStatus::INCOMPLETE)),
        ];
        $constraints = $this->getFilterConstraints($query, $constraints, $filter);

        $query->matching(
            $query->logicalAnd($constraints)
        );

        return $query->execute();
    }
}
