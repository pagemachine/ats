<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationB;
use PAGEmachine\Ats\Domain\Repository\ApplicationBRepository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * PersonalDataController (Second Step)
 */
class PersonalDataController extends AbstractApplicationController
{
    /**
     * @var ApplicationBRepository
     * applicationBRepository
     *
     * @var \PAGEmachine\Ats\Domain\Repository\ApplicationBRepository
     * @inject
     */
    protected $repository = null;

    /**
     * @param  ApplicationBRepository $repository
     */
    public function injectRepository(ApplicationBRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @var \PAGEmachine\Ats\Domain\Repository\CountryRepository
     * @inject
     */
    protected $countryRepository = null;

    /**
     * @param  ApplicationB $application
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("application")
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
     * @TYPO3\CMS\Extbase\Annotation\Validate("\PAGEmachine\Ats\Domain\Validator\TypoScriptValidator", param="application")
     * @return void
     */
    public function updatePersonalDataAction(ApplicationB $application)
    {
        $this->repository->addOrUpdate($application);
        $this->forward("editQualifications", "Application\\Qualifications", null, ['application' => $application->getUid()]);
    }
}
