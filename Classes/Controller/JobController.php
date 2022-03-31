<?php
namespace PAGEmachine\Ats\Controller;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Domain\Repository\JobRepository;
use PAGEmachine\Ats\Provider\PageTitleProvider;
use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * JobController
 */
class JobController extends ActionController
{
    /**
     * @var JobRepository $jobRepository
     */
    protected $jobRepository;

    /**
     * @param JobRepository $jobRepository
     */
    public function injectJobRepository(JobRepository $jobRepository)
    {
        $this->jobRepository = $jobRepository;
    }

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
        // Set title tag
        if ($job->getMetaTitle()) {
            $titleProvider = GeneralUtility::makeInstance(PageTitleProvider::class);
            $titleProvider->setTitle($job->getMetaTitle());
        }

        // Set meta description
        if ($job->getMetaDescription()) {
            $registry = GeneralUtility::makeInstance(MetaTagManagerRegistry::class);
            $metaTagManager = $registry->getManagerForProperty('description');
            $metaTagManager->addProperty('description', $job->getMetaDescription());
        }

        $this->view->assign('job', $job);
    }
}
