<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationE;
use PAGEmachine\Ats\Domain\Model\FileReference;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * UploadController (Fifth Step)
 */
class UploadController extends AbstractApplicationController
{
    /**
     * applicationERepository
     *
     * @var \PAGEmachine\Ats\Domain\Repository\ApplicationERepository
     * @inject
     */
    protected $repository = null;

    /**
     * @param  Job $job
     * @param  ApplicationE $application
     * @ignorevalidation $application
     * @return void
     */
    public function editUploadAction(ApplicationE $application)
    {

        $this->view->assign("application", $application);
    }

    /**
     * Saves upload and forwards back to edit
     *
     * @param  ApplicationE $application
     * @return void
     */
    public function saveUploadAction(ApplicationE $application)
    {

        $this->repository->addOrUpdate($application);
        $this->forward("editUpload", null, null, ['application' => $application->getUid()]);
    }

    /**
     * @param  ApplicationE $application
     * @return void
     */
    public function removeUploadAction(ApplicationE $application, FileReference $file)
    {

        $application->removeFile($file);

        $this->repository->addOrUpdate($application);

        $this->forward("editUpload", null, null, ["application" => $application->getUid()]);
    }

    /**
     *
     * @param  ApplicationE $application
     * @validate $application \PAGEmachine\Ats\Domain\Validator\TypoScriptValidator
     * @return void
     */
    public function updateUploadAction(ApplicationE $application)
    {

        $this->repository->addOrUpdate($application);
        $this->forward("showSummary", "Application\\Submit", null, ['application' => $application->getUid()]);
    }
}
