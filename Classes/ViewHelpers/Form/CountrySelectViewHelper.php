<?php
namespace PAGEmachine\Ats\ViewHelpers\Form;

use PAGEmachine\Ats\Domain\Repository\CountryRepository;
use PAGEmachine\Ats\Domain\Repository\LegacyCountryRepository;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Custom Language SelectViewHelper for static_languages
 */
class CountrySelectViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper
{
    /**
     * @var \PAGEmachine\Ats\Domain\Repository\CountryRepository
     * @inject
     */
    protected $countryRepository;

    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('allowedStaticCountries', 'string', 'Comma-separated List of countries to show. If not set, all countries are shown.', false, null);
        $this->overrideArgument('optionLabelField', 'string', 'Option label', false, 'localizedName');
        $this->overrideArgument('optionValueField', 'string', 'Option value', false, 'uid');
    }

    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        if (!empty($this->arguments['allowedStaticCountries'])) {
            $this->arguments['options'] = $this->countryRepository->findCountriesByUids(
                explode(',', $this->settings['allowedStaticCountries'])
            );
        //@todo drop the static info tables settings fallback in V2
        } elseif (!empty($this->getStaticInfoTablesSettings()['countriesAllowed'])) {
            $this->arguments['options'] =  $this->countryRepository->findCountriesByISO3(
                explode(',', $this->getStaticInfoTablesSettings()['countriesAllowed'])
            );
        }
        $this->arguments['options'] =  $this->countryRepository->findAll();
    }

    /**
     * @return array
     */
    protected function getStaticInfoTablesSettings()
    {
        $configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);
        return $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'StaticInfoTables',
            'pi1'
        );
    }

    protected function getCountryRepository()
    {
        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 8007000) {
            return $this->objectManager->get(LegacyCountryRepository::class);
        } else {
            return $this->objectManager->get(CountryRepository::class);
        }
    }
}
