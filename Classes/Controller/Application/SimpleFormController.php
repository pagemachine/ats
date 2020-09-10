<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationSimple;
use PAGEmachine\Ats\Domain\Model\FileReference;
use PAGEmachine\Ats\Domain\Repository\ApplicationSimpleRepository;

class SimpleFormController extends AbstractApplicationController
{
    /**
     * @var ApplicationSimpleRepository
     */
    protected $repository = null;

    /**
     * @param  ApplicationSimpleRepository
     */
    public function injectRepository(ApplicationSimpleRepository $repository)
    {
        $this->repository = $repository;
    }
    /**
     *
     * @param  ApplicationSimple $application
     * @validate $application \PAGEmachine\Ats\Domain\Validator\TypoScriptValidator
     * @return void
     */

    /**
     * Saves upload and forwards back to edit
     *
     * @param  ApplicationSimple $application
     * @return void
     */
    public function saveUploadAction(ApplicationSimple $application)
    {
        $this->repository->addOrUpdate($application);
        $this->redirect("simpleForm", null, null, ['application' => $application->getUid()]);
    }

    /**
     * @param  ApplicationSimple $application
     * @return void
     */
    public function removeUploadAction(ApplicationSimple $application, FileReference $file)
    {

        $application->removeFile($file);

        $this->repository->addOrUpdate($application);

        $this->redirect("simpleForm", null, null, ["application" => $application->getUid()]);
    }

    /**
     * @param  Job $job
     * @param  ApplicationSimple $application
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("application")
     * @ignorevalidation $application
     * @return void
     */
    public function simpleFormAction(ApplicationSimple $application)
    {

        $this->view->assign("application", $application);
    }

    /**
     *
     * @param  ApplicationSimple $application
     * @validate $application \PAGEmachine\Ats\Domain\Validator\TypoScriptValidator
     * @return void
     */
    public function submitAction(ApplicationSimple $application)
    {

        $this->repository->addOrUpdate($application);
        $this->redirect("showSimpleSummary", "Application\\Submit", null, ['application' => $application->getUid()]);
    }
}
