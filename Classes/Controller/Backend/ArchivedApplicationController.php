<?php
namespace PAGEmachine\Ats\Controller\Backend;

use PAGEmachine\Ats\Application\ApplicationFilter;
use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Domain\Model\Note;
use PAGEmachine\Ats\Service\DuplicationService;
use PAGEmachine\Ats\Workflow\WorkflowManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
 * ArchivedApplicationController
 * Used in the "Archive and Pools" Module
 */
class ArchivedApplicationController extends ApplicationController
{
    /**
     * Action URLs for the action menu
     *
     * @var array
     */
    protected $menuUrls = [
        "listAll" => ["action" => "listAll", "label" => "be.label.Archive.AllApplications"],
        "listPool" => ["action" => "listPool", "label" => "be.label.Archive.Pool"],
    ];

    /**
     * Forwards to the first allowed action (since some could be disallowed by role)
     *
     * @return void
     */
    public function initializeIndexAction()
    {

        $this->forward("listAll");
    }

    /**
     * Lists archived applications in backend module
     *
     * @param  ApplicationFilter $filter
     * @param  bool $resetFilter
     * @return void
     */
    public function listAllAction(ApplicationFilter $filter = null, $resetFilter = false)
    {

        if ($filter == null | $resetFilter === true) {
            $filter = new ApplicationFilter();
        }

        $this->view->assignMultiple([
            'applications' => $this->applicationRepository->findArchived($filter),
            'jobs' => $this->jobRepository->findAll(),
            'filter' => $filter,
        ]);
    }

    /**
     * Lists pooled applications in backend module
     *
     * @param  ApplicationFilter $filter
     * @param  bool $resetFilter
     * @return void
     */
    public function listPoolAction(ApplicationFilter $filter = null, $resetFilter = false)
    {

        if ($filter == null | $resetFilter === true) {
            $filter = new ApplicationFilter();
        }

        $this->view->assignMultiple([
            'applications' => $this->applicationRepository->findPooled($filter),
            'jobs' => $this->jobRepository->findAll(),
            'filter' => $filter,
        ]);
    }

    /**
     * add or remove application from pool
     *
     * @param   Application  $application
     * @return void
     */
    public function moveToPoolAction(Application $application)
    {
        $this->view->assign('application', $application);
        $this->view->assign('beUser', $GLOBALS['BE_USER']->user);
    }

    /**
     * Updates the pool boolean
     *
     * @param    Application  $application
     * @param    Note         $note
     * @ignorevalidation $note
     * @return   void
     */
    public function updateMoveToPoolAction(Application $application, Note $note)
    {
        if (!empty($note->getDetails())) {
            $application->addNote($note);
        }

        $this->applicationRepository->updateAndLog(
            $application,
            "moveToPool",
            [
                "note" => $note->getDetails(),
            ]
        );
        if ($application->getPool() == 1) {
            $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.pool.ok', 'ats'));
        } else {
            $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.nopool.ok', 'ats'));
        }

        $this->redirect("moveToPool", null, null, ['application' => $application]);
    }

    /**
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\PAGEmachine\Ats\Domain\Model\Application>  $applications
     *
     * @validate $applications NotEmpty
     */
    public function newMassPoolMovingAction($applications)
    {
        $this->view->assign('applications', $applications);
        $this->view->assign('statusOptions', WorkflowManager::getInstance()->getPlaces());
        $this->view->assign('jobs', $this->jobRepository->findAll());
        $this->view->assign('beUser', $GLOBALS['BE_USER']->user);
    }

    /**
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\PAGEmachine\Ats\Domain\Model\Application>  $applications
     * @param  Job $job
     * @param  string $status
     * @param  Note $note
     * @validate $applications NotEmpty
     * @ignorevalidation $note
     * @return void
     */
    public function setMassPoolMovingAction($applications, Job $job, $status, Note $note)
    {
        $useDuplicate = 0;
        foreach ($applications as $application) {
            if ($useDuplicate) {
                $note = DuplicationService::getInstance()->duplicateObject($note);
            }
            $useDuplicate = 1;

            $application->setJob($job);
            $application->setStatus(ApplicationStatus::cast($status));

            if (!empty($note->getDetails())) {
                $application->addNote($note);
            }

            $this->applicationRepository->updateAndLog(
                $application,
                'massPoolMoving',
                [
                    'status' => $application->getStatus()->__toString(),
                    'note' => $note->getDetails(),
                    'job' => $application->getJob()->getTitle(),
                ]
            );
        }
        $this->addFlashMessage($this->callStatic(LocalizationUtility::class, 'translate', 'be.flashMessage.massPoolMoving.ok', 'ats'));
        $this->redirect("listPool", null, null, []);
    }
}
