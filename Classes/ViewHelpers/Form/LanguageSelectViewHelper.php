<?php
namespace PAGEmachine\Ats\ViewHelpers\Form;

use PAGEmachine\Ats\Domain\Repository\LanguageRepository;
use PAGEmachine\Ats\Domain\Repository\LegacyLanguageRepository;
use PAGEmachine\Ats\Service\IntlLocalizationService;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Custom Language SelectViewHelper for static_languages
 */
class LanguageSelectViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper
{
    /**
     * Initialize arguments.
     *
     * @return void
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('languageUids', 'string', 'Comma-separated List of languages to show. If not set, all languages are shown.', false, null);
        $this->overrideArgument('optionLabelField', 'string', 'Option label', false, 'localizedName');
        $this->overrideArgument('optionValueField', 'string', 'Option value', false, 'uid');
    }

    /**
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $languageRepository = $this->getLanguageRepository();

        /** @var array */
        $languages = [];

        if ($this->arguments['languageUids'] != null && $this->arguments['languageUids'] != "") {
            $languageUids = explode(",", $this->arguments['languageUids']);
            $languages = $languageRepository->findLanguagesByUids($languageUids);
        } else {
            $languages = $languageRepository->findAll();
        }

        $this->arguments['options'] = IntlLocalizationService::getInstance()->orderItemsByLabel($languages, $this->arguments['optionLabelField']);
        ;
    }

    /**
     * @return LanguageRepository|LegacyLanguageRepository
     * @todo remove this in V2
     */
    protected function getLanguageRepository()
    {
        if (VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 8007000) {
            return $this->objectManager->get(LegacyLanguageRepository::class);
        } else {
            return $this->objectManager->get(LanguageRepository::class);
        }
    }
}
