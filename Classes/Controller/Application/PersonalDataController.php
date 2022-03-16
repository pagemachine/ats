<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationB;
use PAGEmachine\Ats\Domain\Repository\ApplicationBRepository;
use PAGEmachine\Ats\Domain\Repository\CountryRepository;

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
     */
    protected $repository = null;

    /**
     * @var CountryRepository
     */
    protected $countryRepository = null;

    /**
     * @param  ApplicationBRepository $repository
     */
    public function injectRepository(ApplicationBRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CountryRepository $countryRepository
     */
    public function injectCountryRepository(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * @param  ApplicationB $application
     * @TYPO3\CMS\Extbase\Annotation\IgnoreValidation("application")
     * @return void
     */
    public function editPersonalDataAction(ApplicationB $application)
    {
        if (!$this->hasAccess($application)) {
            return;
        }

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
