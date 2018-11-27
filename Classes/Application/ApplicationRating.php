<?php
namespace PAGEmachine\Ats\Application;

use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Core\Type\Enumeration;
use TYPO3\CMS\Core\Type\Exception\InvalidEnumerationValueException;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class ApplicationRating extends Enumeration
{
    const __default = 0;

    /**
     * @param mixed $value
     * @throws Exception\InvalidEnumerationValueException
     */
    public function __construct($value = null)
    {
        try {
            parent::__construct($value);
        } catch(InvalidEnumerationValueException $e) {
            /**
             * Catch invalid enumeration values
             * Since enumeration values can change by config, this should not result in an exception.
             *
             * Instead, the value is reset to default.
             */
            $this->setValue(static::__default);
        }
    }

    /**
     * Overrides the parent method to set enum values by config
     *
     * @throws Exception\InvalidEnumerationValueException
     * @throws Exception\InvalidEnumerationDefinitionException
     * @internal param string $class
     */
    protected static function loadValues()
    {
        $ratingOptions = TyposcriptService::getInstance()->getSettings()['ratingOptions'];

        foreach($ratingOptions as $value => $option) {
            static::$enumConstants[get_called_class()][$option['name']] = $value;
        }
    }
}
