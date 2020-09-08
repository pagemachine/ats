<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationE;
use PAGEmachine\Ats\Domain\Model\FileReference;
use PAGEmachine\Ats\Domain\Repository\ApplicationERepository;

class SimpleFormController extends AbstractApplicationController
{
    /**
     * @var ApplicationERepository
     */
    protected $repository = null;

    /**
     * @param  ApplicationERepository
     */
    public function injectRepository(ApplicationERepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     *
     * @param  ApplicationE $application
     * @validate $application \PAGEmachine\Ats\Domain\Validator\TypoScriptValidator
     * @return void
     */

    /**
     * Saves upload and forwards back to edit
     *
     * @param  ApplicationE $application
     * @return void
     */
    public function saveUploadAction(ApplicationE $application)
    {
        $this->repository->addOrUpdate($application);
        $this->redirect("simpleForm", null, null, ['application' => $application->getUid()]);
    }

    /**
     * @param  ApplicationE $application
     * @return void
     */
    public function removeUploadAction(ApplicationE $application, FileReference $file)
    {

        $application->removeFile($file);

        $this->repository->addOrUpdate($application);

        $this->redirect("simpleForm", null, null, ["application" => $application->getUid()]);
    }

    /**
     * @param  Job $job
     * @param  ApplicationE $application
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("application")
     * @ignorevalidation $application
     * @return void
     */
    public function simpleFormAction(ApplicationE $application)
    {

        $this->view->assign("application", $application);
    }

    /**
     *
     * @param  ApplicationE $application
     * @validate $application \PAGEmachine\Ats\Domain\Validator\TypoScriptValidator
     * @return void
     */
    public function submitAction(ApplicationE $application)
    {

        $this->repository->addOrUpdate($application);
        $this->redirect("showSimpleSummary", "Application\\Submit", null, ['application' => $application->getUid()]);
    }
}
