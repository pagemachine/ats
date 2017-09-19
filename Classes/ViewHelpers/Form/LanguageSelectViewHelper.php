<?php
namespace PAGEmachine\Ats\ViewHelpers\Form;

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
        $this->registerArgument('multiple', 'boolean', 'If set multiple options may be selected.', false, false);
    }

    protected function getOptions()
    {
    }
}
