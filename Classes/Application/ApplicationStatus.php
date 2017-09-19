<?php
namespace PAGEmachine\Ats\Application;

use TYPO3\CMS\Core\Type\Enumeration;

/*
 * This file is part of the PAGEmachine ATS project.
 */

class ApplicationStatus extends Enumeration {

   const __default = self::INCOMPLETE;

   /** @var int Incomplete application that is not sent by user yet */
   const INCOMPLETE = 0;

   /** @var Int New application sent by user (Z1)*/
   const NEW_APPLICATION = 10;

   /** @var Int Requested attestation for disabled applicants (Z1)*/
   const REQUESTED_ATTESTATION = 20;

   /** @var Int Attestation is ready and in BSCHWB department */
   const BSCHWB = 30;

   /** @var Int GleiB (1) */
   const GLEIB = 40;

   /** @var Int Application is at concrete department */
   const DEPARTMENT = 50;

   /** @var Int Application is at PERSO (again) (Z2) */
   const PERSO = 60;

   /** @var Int GleiB (2) */
   const GLEIB2 = 70;

   /** @var Int OEPR */
   const OPR = 80;

   /** @var Int Perso (Z) final (Z3) */
   const PERSO_FINAL = 90;

   /** @var Int FINAL STATUS: Applicant was employed */
   const EMPLOYED = 100;

   /** @var Int FINAL STATUS: Application was cancelled by employer */
   const CANCELLED_BY_EMPLOYER = 110;

   /** @var Int FINAL STATUS: Application was cancelled by candidate */
   const CANCELLED_BY_CANDIDATE = 120;


   /**
    * Returns true if the application is submitted, meaning it is no longer at the FE User's side and can be sent to the departments etc.
    * @return bool
    */
   public function isSubmitted()
   {
      return (int)$this->__toString() > self::INCOMPLETE;
   }

   /**
    * Returns true if the application is in progress at some point (not new and not employed/dismissed)
    * @return bool
    */
   public function isInProgress()
   {
      return self::NEW_APPLICATION < (int)$this->__toString() && (int)$this->__toString() < self::EMPLOYED;
   }

   /**
    * Returns true if the application is new
    *
    * @return bool
    */
   public function isNew() 
   {
      return (int)$this->__toString() == self::NEW_APPLICATION;
   }

   /**
    * Flips getConstants() so the returned array is value => constant (for fluid forms)
    *
    * @return array
    */
   public static function getFlippedConstants() {

      return array_flip(static::getConstants());
   }

   /**
    * Returns flipped constants without the default one (which should not be selectable by anyone)
    *
    * @return array
    */
   public static function getConstantsForWorkflow() {

      $constants = static::getFlippedConstants();
      unset($constants[self::INCOMPLETE]);

      return $constants;
   }

   /**
    * Returns flipped constants which mark the application as complete (>= 100)
    *
    * @return array
    */
   public static function getConstantsForCompletion() {

    $constants = static::getFlippedConstants();

    $completionConstants = [];

    foreach ($constants as $number => $text) {
      if ($number >= self::EMPLOYED) {
        $completionConstants[$number] = $text;
      }
    }

    return $completionConstants;

   }


}