<?php
namespace PAGEmachine\Ats\ViewHelpers\Form;

use PAGEmachine\Ats\Domain\Repository\CountryRepository;
use PAGEmachine\Ats\Service\IntlLocalizationService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
     * @var CountryRepository
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

        /** @var array */
        $countries = [];

        if (!empty($this->arguments['allowedStaticCountries'])) {
            $countries = $this->countryRepository->findCountriesByUids(
                explode(',', $this->settings['allowedStaticCountries'])
            );
        //@todo drop the static info tables settings fallback in V2
        } elseif (!empty($this->getStaticInfoTablesSettings()['countriesAllowed'])) {
            $countries =  $this->countryRepository->findCountriesByISO3(
                explode(',', $this->getStaticInfoTablesSettings()['countriesAllowed'])
            );
        } else {
            $countries = $this->countryRepository->findAll();
        }

        $this->arguments['options'] = IntlLocalizationService::getInstance()->orderItemsByLabel($countries, $this->arguments['optionLabelField']);
    }

    /**
     * @return array
     */
    protected function getStaticInfoTablesSettings()
    {
        $configurationManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        return $configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            'StaticInfoTables',
            'pi1'
        );
    }
}
