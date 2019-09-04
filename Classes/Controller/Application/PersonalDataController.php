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
     * @var \PAGEmachine\Ats\Domain\Repository\CountryRepository
     * @inject
     */
    protected $countryRepository = null;

    /**
     * @param  ApplicationB $application
     * @ignorevalidation $application
     * @return void
     */
    public function editPersonalDataAction(ApplicationB $application)
    {
        if (!empty($this->settings['defaultCountry'])) {
            $this->view->assign('defaultCountry', $this->countryRepository->findOneByIsoCodeA3($this->settings['defaultCountry']));
        }
        if (!empty($this->settings['defaultNationality'])) {
            $this->view->assign('defaultNationality', $this->countryRepository->findOneByIsoCodeA3($this->settings['defaultNationality']));
        }

        $this->view->assign("application", $application);
    }


    /**
     *
     * @param  ApplicationB $application
     * @validate $application \PAGEmachine\Ats\Domain\Validator\TypoScriptValidator
     * @return void
     */
    public function updatePersonalDataAction(ApplicationB $application)
    {
        $this->repository->addOrUpdate($application);
        $this->forward("editQualifications", "Application\\Qualifications", null, ['application' => $application->getUid()]);
    }
}
