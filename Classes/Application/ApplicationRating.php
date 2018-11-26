<?php
namespace PAGEmachine\Ats\Application;

use PAGEmachine\Ats\Service\TyposcriptService;
use TYPO3\CMS\Core\Type\Enumeration;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class ApplicationRating extends Enumeration
{
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

   /**
    * Flips getConstants() so the returned array is value => constant (for fluid forms)
    *
    * @return array
    */
    public static function getFlippedConstants()
    {

        return array_flip(static::getConstants());
    }
}
