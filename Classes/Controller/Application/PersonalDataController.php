<?php
namespace PAGEmachine\Ats\Controller\Application;

use PAGEmachine\Ats\Domain\Model\ApplicationB;
use PAGEmachine\Ats\Domain\Repository\CountryRepository;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

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
     */
    protected $countryRepository = null;

    /**
     * @param CountryRepository $countryRepository
     */
    public function injectCountryRepository(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

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

        $countries = $this->getStaticCountries();

        $this->view->assign('countries', $countries);
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

    protected function getStaticCountries()
    {
        if (!empty($this->settings['allowedStaticCountries'])) {
            return $this->countryRepository->findCountriesByUids(
                explode(',', $this->settings['allowedStaticCountries'])
            );
        } elseif (!empty($this->getStaticInfoTablesSettings()['countriesAllowed'])) {
            return $this->countryRepository->findCountriesByISO3(
                explode(',', $this->getStaticInfoTablesSettings()['countriesAllowed'])
            );
        }
        return $this->countryRepository->findAll();
    }

    protected function getStaticInfoTablesSettings()
    {
        $configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);
        return $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'StaticInfoTables',
            'pi1'
        );
    }
}
