<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\Application;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * SubmitController (Last Step)
 */
class SubmitController extends AbstractApplicationController
{

    /**
     * applicationRepository
     *
     * @var \PAGEmachine\Ats\Domain\Repository\ApplicationRepository
     * @inject
     */
    protected $repository = NULL;

    /**
     * @param  Application $application
     * @ignorevalidation $application
     * @return void
     */
    public function showSummaryAction(Application $application) {

        $this->view->assign("application", $application);
    }

    /**
     * @param  Application $application
     * @return void
     */
    public function submitAction(Application $application) {
        
        $application->submit();

        $this->repository->updateAndLog(
            $application,
            'new'
        );
    }



}
