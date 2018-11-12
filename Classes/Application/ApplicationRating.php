<?php
namespace PAGEmachine\Ats\Application;

use TYPO3\CMS\Core\Type\Enumeration;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class ApplicationRating extends Enumeration
{
    const __default = self::NONE;

   /** @var Int */
    const NONE = 0;

   /** @var Int former UNGEEIGNET*/
    const UNSUITED = 10;

   /** @var Int former GEEIGNET*/
    const SUITED = 20;

   /** @var Int former ENGERE_AUSWAHL*/
    const SHORTLISTED= 30;

   /** @var Int former AUSGEWAELT*/
    const SELECTED = 40;

   /** @var Int former ABSAGE_DURCH_BEWERBER*/
    const CANCELLED_BY_CANDIDATE = 50;

   /** @var Int */
    const SCAN_UNSUITED = 60;

   /** @var Int */
    const SCAN_SUITED = 70;

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
