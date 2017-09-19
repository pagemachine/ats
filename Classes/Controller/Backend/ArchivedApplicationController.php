<?php
namespace PAGEmachine\Ats\Controller\Backend;

use PAGEmachine\Ats\Application\ApplicationFilter;
use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Domain\Model\Application;
use PAGEmachine\Ats\Domain\Model\Note;

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
        ["action" => "listAll", "label" => "Archive"],
        ["action" => "listPool", "label" => "Pool"]
    ];

    /**
     * Forwards to the first allowed action (since some could be disallowed by role)
     *
     * @return void
     */
    public function initializeIndexAction() {

        $this->forward("listAll");

    }

    /**
     * Lists archived applications in backend module
     *
     * @param  ApplicationFilter $filter
     * @param  bool $resetFilter
     * @return void
     */
    public function listAllAction(ApplicationFilter $filter = null, $resetFilter = false) {

        if ($filter == null | $resetFilter === true) {

            $filter = new ApplicationFilter();
        }

        $this->view->assignMultiple([
            'applications' => $this->applicationRepository->findArchived($filter),
            'jobs' => $this->jobRepository->findAll(),
            'filter' => $filter
        ]);
    }

    /**
     * Lists pooled applications in backend module
     *
     * @param  ApplicationFilter $filter
     * @param  bool $resetFilter
     * @return void
     */
    public function listPoolAction(ApplicationFilter $filter = null, $resetFilter = false) {

        if ($filter == null | $resetFilter === true) {

            $filter = new ApplicationFilter();
        }

        $this->view->assignMultiple([
            'applications' => $this->applicationRepository->findPooled($filter),
            'jobs' => $this->jobRepository->findAll(),
            'filter' => $filter
        ]);
    }

    /**
     * add or remove application from pool
     *
     * @param   Application  $application
     * @ignorevalidation $application
     * @return void
     */
    public function moveToPoolAction(Application $application){
        $this->view->assign('application', $application);
        $this->view->assign('beUser', $GLOBALS['BE_USER']->user);
    }

    /**
     * Updates the pool boolean
     *
     * @param    Application  $application
     * @param    Note         $note
     * @ignorevalidation $application
     * @ignorevalidation $note
     * @return   void
     */
    public function  updateMoveToPoolAction(Application $application, Note $note){
        if (!empty($note->getDetails())) {
            $application->addNote($note);
        }

        $this->applicationRepository->updateAndLog(
            $application,
            "moveToPool",
            [
                "note" => $note->getDetails()
            ]
        );
        $this->addFlashMessage("Pool status successfully updated.");
        $this->redirect("moveToPool", null, null, ['application' => $application]);
    }

}
