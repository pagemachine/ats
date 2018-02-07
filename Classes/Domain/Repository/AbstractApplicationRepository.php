<?php
namespace PAGEmachine\Ats\Domain\Repository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Domain\Model\AbstractApplication;
use PAGEmachine\Ats\Domain\Model\History;
use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Persistence\Repository;
use TYPO3\CMS\Beuser\Domain\Model\BackendUser;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Mvc\Exception\InvalidArgumentTypeException;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/**
 * The repository for Jobs
 */
class AbstractApplicationRepository extends Repository
{

    protected $defaultOrderings = [
        'creationDate' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     *
     * @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper
     * @inject
     */
    protected $dataMapper;


    /**
     * @var \TYPO3\CMS\Extbase\Service\EnvironmentService
     * @inject
     */
    protected $environmentService;

    /**
     * @var \PAGEmachine\Ats\Workflow\WorkflowManager $workflowManager
     */
    protected $workflowManager;


    /**
     * @var \Symfony\Component\Workflow\Workflow $workflow
     */
    protected $workflow;

    /**
     *
     * @param  \PAGEmachine\Ats\Workflow\WorkflowManager $workflowManager
     * @return void
     */
    public function injectWorkflowManager(\PAGEmachine\Ats\Workflow\WorkflowManager $workflowManager)
    {
        $this->workflowManager = $workflowManager;
        $this->workflow = $workflowManager->getWorkflow();
    }


    /**
     * Checks if an application is persisted and adds or updates accordingly.
     *
     * @param AbstractApplication $application
     */
    public function addOrUpdate(AbstractApplication $application)
    {

        if ($this->persistenceManager->isNewObject($application)) {
            $this->add($application);
        } else {
            $this->update($application);
        }

        $this->persistenceManager->persistAll();
    }

    /**
     * Finds an existing application by user and job
     * @param  FrontendUser $user
     * @param  Job          $job
     * @param  Int          $minStatus The minimal status value to fetch. Standard is NEW, so no unfinished applications show up
     * @param  Int          $maxStatus The max status value to fetch. Standard is NULL which means no restriction
     * @return AbstractApplication
     */
    public function findByUserAndJob(FrontendUser $user, Job $job, $minStatus = ApplicationStatus::NEW_APPLICATION, $maxStatus = null)
    {

        $query = $this->createQuery();

        $constraints = array_merge(
            [
                $query->equals("user", $user),
                $query->equals("job", $job),
            ],
            $this->buildConstraintsForStatusRange($query, $minStatus, $maxStatus)
        );



        $query->matching(
            $query->logicalAnd(
                $constraints
            )
        );


        $result = $query->execute()->getFirst();
        return $result;
    }

    /**
     * @param  QueryInterface $query
     * @param  Int        $minStatus
     * @param  Int        $maxStatus
     * @return array
     */
    protected function buildConstraintsForStatusRange(QueryInterface $query, $minStatus, $maxStatus)
    {

        $constraints = [];

        if (is_int($minStatus)) {
            $constraints[] = $query->greaterThanOrEqual("status", $minStatus);
        } elseif ($minStatus !== null) {
            throw new InvalidArgumentTypeException('Argument "minStatus" was expected with type int or NULL, ' . gettype($minStatus) . ' given.');
        }

        if (is_int($maxStatus)) {
            $constraints[] = $query->lessThanOrEqual("status", $maxStatus);
        } elseif ($maxStatus !== null) {
            throw new InvalidArgumentTypeException('Argument "maxStatus" was expected with type int or NULL, ' . gettype($maxStatus) . ' given.');
        }

        return $constraints;
    }

    protected function buildBackendUserRestriction(QueryInterface $query, BackendUserAuthentication $backendUser)
    {
        $constraints = [];

        $constraints[] = $query->contains("job.userPa", $backendUser->user['uid']);

        foreach ($backendUser->userGroups as $group) {
            $constraints[] = $query->contains("job.department", $group['uid']);
            $constraints[] = $query->contains("job.officials", $group['uid']);
            $constraints[] = $query->contains("job.contributors", $group['uid']);
        }

        $constraint = $query->logicalAnd(
            $query->logicalOr($constraints),
            $query->lessThan("status", ApplicationStatus::EMPLOYED)
        );

        return $constraint;
    }

    /**
     * Finds all applications assigned to a specific backend user (searches fields defined in extconf)
     *
     * @param  BackendUserAuthentication $user
     * @return QueryResult
     */
    public function findByBackendUser(BackendUserAuthentication $backendUser)
    {

        $query = $this->createQuery();

        $constraints = $this->buildBackendUserRestriction($query, $backendUser);

        $query->matching(
            $constraints
        );

        return $query->execute();
    }

    /**
     * Finds all non-archived applications
     *
     * @param  BackendUserAuthentication $user
     * @return QueryResult
     */
    public function findNonArchived()
    {

        $query = $this->createQuery();

        $query->matching(
            $query->lessThan("status", ApplicationStatus::EMPLOYED)
        );

        return $query->execute();
    }

    /**
     * Updates and logs changes
     *
     * @param  AbstractApplication $application
     * @param string $subject
     * @param array $details
     * @return void
     */
    public function updateAndLog(AbstractApplication $application, $subject = 'update', $details = [])
    {
        //Apply workflow transition
        if ($this->workflow->can($application, $subject)) {
            $this->workflow->apply($application, $subject);
        }

        $history = $this->createHistory($application, $subject, $details);

        if ($history != null) {
            $application->addHistoryEntry($history);
        }

        $this->update($application);
    }

    /**
     * Builds a new history object
     *
     * @param  AbstractApplication $application
     * @param  string              $subject
     * @param  array              $details
     * @return History
     */
    public function createHistory(AbstractApplication $application, $subject, $details)
    {
        $changes = $this->collectChangedProperties($application);

        if (!empty($changes) || !empty($details)) {
            $history = new History();
            $history->setApplication($application);
            $history->setSubject($subject);
            $history->setDetails($details);
            $history->setHistoryData($changes);



            if ($this->environmentService->isEnvironmentInBackendMode()) {
                $user = $this->dataMapper->map(BackendUser::class, [$GLOBALS['BE_USER']->user])[0];
                $history->setUser($user);
            }

            return $history;
        }

        return null;
    }

    /**
     *
     * @param  AbstractApplication $application
     * @return array
     */
    protected function collectChangedProperties(AbstractApplication $application)
    {
        $changedProperties = [];

        foreach ($application->_getProperties() as $property => $value) {
            if ($application->_isDirty($property)) {
                $column = $this->dataMapper->convertPropertyNameToColumnName($property);

                $oldValue = $this->dataMapper->getPlainValue($application->_getCleanProperty($property));
                $newValue = $this->dataMapper->getPlainValue($value);

                //Sort out properties marked as dirty, but without a real value change
                //f.ex. custom relation fields
                if ($oldValue != $newValue) {
                    $changedProperties['oldRecord'][$column] = $oldValue;
                    $changedProperties['newRecord'][$column] = $newValue;
                }
            }
        }

        return $changedProperties;
    }
}
