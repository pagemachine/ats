<?php
namespace PAGEmachine\Ats\Domain\Model;

use PAGEmachine\Ats\Domain\Model\Job;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
 * Application
 * @codeCoverageIgnore
 */
class ApplicationA extends AbstractApplication
{
    /**
     * @var TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     */
    protected $user;

    /**
     * @return TYPO3\CMS\Extbase\Domain\Model\FrontendUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param TYPO3\CMS\Extbase\Domain\Model\FrontendUser $user
     * @return void
     */
    public function setUser(FrontendUser $user)
    {
        $this->user = $user;
    }


    /**
     * @var PAGEmachine\Ats\Domain\Model\Job $job
     */
    protected $job;

    /**
     * @return PAGEmachine\Ats\Domain\Model\Job
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param PAGEmachine\Ats\Domain\Model\Job $job
     * @return void
     */
    public function setJob(Job $job)
    {
        $this->job = $job;
    }

    /**
     * @var bool $privacyPolicy
     */
    protected $privacyPolicy;

    /**
     * @return bool
     */
    public function getPrivacyPolicy()
    {
        return $this->privacyPolicy;
    }

    /**
     * @param bool $privacyPolicy
     * @return void
     */
    public function setPrivacyPolicy($privacyPolicy)
    {
        $this->privacyPolicy = $privacyPolicy;
    }
}
