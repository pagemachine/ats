<?php
namespace PAGEmachine\Ats\Controller;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\Job;
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

        $this->settings = $this->mergeFlexFormAndTypoScriptSettings($this->settings);
    }

    /**
     * Merges global TypoScript and FlexForm settings depending on config (override, override of empty values).
     *
     * @return array
     */
    public function mergeFlexFormAndTypoScriptSettings($settings = [])
    {
        if (!empty($settings['flexForm']) && intval($settings['flexForm']['override']) == 1) {
            $overrideSettings = $settings['flexForm'];

            ArrayUtility::mergeRecursiveWithOverrule(
                $settings,
                $overrideSettings,
                true,
                (intval($overrideSettings['overrideEmptyValues']) == 1 ? true : false)
            );
        }
        unset($settings['flexForm']);

        return $settings;
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
