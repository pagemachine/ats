<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Domain\Model\ApplicationA;
use PAGEmachine\Ats\Domain\Model\Job;
use PAGEmachine\Ats\Domain\Repository\ApplicationARepository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * FormController (First Step)
 */
class FormController extends AbstractApplicationController
{
    /**
     * @var ApplicationARepository
     */
    protected $applicationARepository = null;

    /**
     * @param  ApplicationARepository $repository
     */
    public function injectApplicationARepository(ApplicationARepository $applicationARepository)
    {
        $this->applicationARepository = $applicationARepository;
    }

    /**
     * starts a new application form
     *
     * @param  Job $job
     * @param  ApplicationA|null $application
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("application")
     * @return void
     */
    public function formAction(Job $job, ApplicationA $application = null)
    {
        if ($application == null  && $this->settings['loginPage']) {
            $application = $this->applicationARepository->findByUserAndJob($this->authenticationService->getAuthenticatedUser(), $job, null, ApplicationStatus::INCOMPLETE);
        }

        $this->view->assignMultiple([
            "job" => $job,
            "application" => $application,
            "user" => $this->authenticationService->getAuthenticatedUser(),
            ]);
    }

    /**
     *
     * @param  ApplicationA $application
     * @TYPO3\CMS\Extbase\Annotation\Validate("\PAGEmachine\Ats\Domain\Validator\TypoScriptValidator", param="application")
     * @return void
     */
    public function updateFormAction(ApplicationA $application)
    {

        $this->applicationARepository->addOrUpdate($application);

        if ($this->settings['simpleForm']) {
            $this->forward("simpleForm", "Application\\SimpleForm", null, ['application' => $application->getUid()]);
        } else {
            $this->forward("editPersonalData", "Application\\PersonalData", null, ['application' => $application->getUid()]);
        }
    }
}
