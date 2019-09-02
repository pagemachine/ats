<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationB;
use PAGEmachine\Ats\Domain\Repository\CountryRepository;
use PAGEmachine\Ats\Domain\Repository\LegacyCountryRepository;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

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
        $countryRepository = $this->getCountryRepository();

        if (!empty($this->settings['defaultCountry'])) {
            $this->view->assign('defaultCountry', $countryRepository->findOneByIsoCodeA3($this->settings['defaultCountry']));
        }
        if (!empty($this->settings['defaultNationality'])) {
            $this->view->assign('defaultNationality', $countryRepository->findOneByIsoCodeA3($this->settings['defaultNationality']));
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

    /**
     * Legacy country repository switch for TYPO3 7
     *
     * @return CountryRepository|LegacyCountryRepository
     * @todo remove this in V2
     */
    protected function getCountryRepository()
    {
        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 8007000) {
            return $this->objectManager->get(LegacyCountryRepository::class);
        } else {
            return $this->objectManager->get(CountryRepository::class);
        }
    }
}
