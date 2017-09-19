<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Domain\Model\ApplicationA;
use PAGEmachine\Ats\Domain\Model\Job;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * FormController (First Step)
 */
class FormController extends AbstractApplicationController
{
    /**
     * applicationARepository
     *
     * @var \PAGEmachine\Ats\Domain\Repository\ApplicationARepository
     * @inject
     */
    protected $applicationARepository = null;

    /**
     * starts a new application form
     *
     * @param  Job $job
     * @param  ApplicationA|null $application
     * @ignorevalidation $application
     * @return void
     */
    public function formAction(Job $job, ApplicationA $application = null)
    {

        if ($application == null) {
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
     * @return void
     */
    public function updateFormAction(ApplicationA $application)
    {

        $this->applicationARepository->addOrUpdate($application);

        $this->forward("editPersonalData", "Application\\PersonalData", null, ['application' => $application->getUid()]);
    }
}
