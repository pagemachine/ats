<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationB;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * PersonalDataController (Second Step)
 */
class PersonalDataController extends AbstractApplicationController
{
    /**
     * applicationBRepository
     *
     * @var \PAGEmachine\Ats\Domain\Repository\ApplicationBRepository
     * @inject
     */
    protected $repository = null;

    /**
     * @param  ApplicationB $application
     * @ignorevalidation $application
     * @return void
     */
    public function editPersonalDataAction(ApplicationB $application)
    {

        $this->view->assign("application", $application);
    }


    /**
     *
     * @param  ApplicationB $application
     * @return void
     */
    public function updatePersonalDataAction(ApplicationB $application)
    {

        $this->repository->addOrUpdate($application);
        $this->forward("editQualifications", "Application\\Qualifications", null, ['application' => $application->getUid()]);
    }
}
