<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationC;
use PAGEmachine\Ats\Domain\Repository\ApplicationCRepository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * QualificationsController (Third Step)
 */
class QualificationsController extends AbstractApplicationController
{
    /**
     * @var ApplicationCRepository
     */
    protected $repository = null;

    /**
     * @param  ApplicationCRepository $repository
     */
    public function injectRepository(ApplicationCRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param  ApplicationC $application
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("application")
     * @return void
     */
    public function editQualificationsAction(ApplicationC $application)
    {
        $this->view->assign("application", $application);
    }

    /**
     *
     * @param  ApplicationC $application
     * @TYPO3\CMS\Extbase\Annotation\Validate("\PAGEmachine\Ats\Domain\Validator\TypoScriptValidator", param="application")
     * @return void
     */
    public function updateQualificationsAction(ApplicationC $application)
    {

        $this->repository->addOrUpdate($application);
        $this->forward("editAdditionalData", "Application\\AdditionalData", null, ['application' => $application->getUid()]);
    }
}
