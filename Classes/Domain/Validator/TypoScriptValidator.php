<?php
namespace PAGEmachine\Ats\Domain\Validator;

use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Extbase\Validation\Validator\GenericObjectValidator;
use TYPO3\CMS\Extbase\Validation\ValidatorResolver;

/*
 * This file is part of the PAGEmachine ATS project.
 */

/**
 * TypoScriptValidator
 */
class TypoScriptValidator extends GenericObjectValidator
{
    /**
     * @var ValidatorResolver $validatorResolver
     */
    protected $validatorResolver;

    /**
     * @param ValidatorResolver $validatorResolver
     */
    public function injectValidatorResolver(ValidatorResolver $validatorResolver)
    {
        $this->validatorResolver = $validatorResolver;
    }

    /**
     * Checks if the given value is valid according to the property validators.
     *
     * Loads TypoScript-defined validators beforehand.
     *
     * @param mixed $object The value that should be validated
     * @api
     */
    public function isValid($object)
    {
        $configuration = TyposcriptService::getInstance()->getFrameworkConfiguration()['mvc']['validation'][get_class($object)];

        if (!empty($configuration)) {
            foreach ($configuration as $propertyName => $validatorConfigurations) {
                foreach ($validatorConfigurations as $configuration) {
                    $validator = $this->validatorResolver->createValidator(
                        $configuration['type'],
                        $configuration['options'] ?: []
                    );

                    //ValidationResolver returns null if validator does not exist
                    if ($validator != null) {
                        $this->addPropertyValidator($propertyName, $validator);
                    }
                }
            }

            return parent::isValid($object);
        }

        // No validation defined via TypoScript, so we're good.
        return true;
    }
}
