<?php
namespace PAGEmachine\Ats\ViewHelpers\Form;

use PAGEmachine\Ats\Domain\Repository\LanguageRepository;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * Custom Language SelectViewHelper for static_languages
 */
class LanguageSelectViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper
{
    /**
     * @var \PAGEmachine\Ats\Domain\Repository\LanguageRepository
     * @inject
     */
    protected $languageRepository;

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
        if ($this->arguments['languageUids'] != null && $this->arguments['languageUids'] != "") {
            $languageUids = explode(",", $this->arguments['languageUids']);
            $languages = $this->languageRepository->findLanguagesByUids($languageUids);
        } else {
            $languages = $this->languageRepository->findAll();
        }
        $this->arguments['options'] = $languages;
    }
}
