<?php
namespace PAGEmachine\Ats\Controller;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * JobController
 */
class JobController extends ActionController
{
    /**
     * jobRepository
     *
     * @var \PAGEmachine\Ats\Domain\Repository\JobRepository
     * @inject
     */
    protected $jobRepository = null;

    /**
     *
     * @return void
     */
    public function initializeAction()
    {
        parent::initializeAction();

        $this->settings = TyposcriptService::getInstance()->mergeFlexFormAndTypoScriptSettings($this->settings);
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction()
    {
        $jobs = $this->jobRepository->findAll();
        $this->view->assign('jobs', $jobs);
    }

    /**
     * action show
     *
     * @param \PAGEmachine\Ats\Domain\Model\Job $job
     * @return void
     */
    public function showAction(Job $job)
    {
        $this->view->assign('job', $job);
    }
}
