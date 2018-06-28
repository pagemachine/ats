<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationD;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * AdditionalDataController (Fourth Step)
 */
class AdditionalDataController extends AbstractApplicationController
{
    /**
     * applicationDRepository
     *
     * @var \PAGEmachine\Ats\Domain\Repository\ApplicationDRepository
     * @inject
     */
    protected $repository = null;

    /**
     * @param  ApplicationD $application
     * @ignorevalidation $application
     * @return void
     */
    public function editAdditionalDataAction(ApplicationD $application)
    {

        $this->view->assign("application", $application);
    }

    /**
     *
     * @param  ApplicationD $application
     * @validate $application \PAGEmachine\Ats\Domain\Validator\TypoScriptValidator
     * @return void
     */
    public function updateAdditionalDataAction(ApplicationD $application)
    {

        $this->repository->addOrUpdate($application);
        $this->forward("editUpload", "Application\\Upload", null, ['application' => $application->getUid()]);
    }
}
