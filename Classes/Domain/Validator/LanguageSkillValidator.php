<?php
namespace PAGEmachine\Ats\Domain\Validator;

use TYPO3\CMS\Extbase\Validation\Error;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * LanguageSkillValidator
 */
class LanguageSkillValidator extends AbstractValidator
{
    public function isValid($languageSkill)
    {
        $valid = true;

        //Either dropdown language or text language must be set
        if (empty($languageSkill->getLanguage()) && empty($languageSkill->getTextLanguage())) {
            $this->result->forProperty('language')->addError(new Error('You must enter a language, either via selection or input.', 1522232293));
            $valid = false;
        }

        //Level must be set
        if ($languageSkill->getLevel() === null) {
            $this->result->forProperty('level')->addError(new Error('You must enter a skill level', 1522232270));
            $valid = false;
        }

        return $valid;
    }
}
