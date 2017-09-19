<?php
namespace PAGEmachine\Ats\Domain\Model;

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/*
 * This file is part of the PAGEmachine ATS project.
 */


/**
 * Application
 * @codeCoverageIgnore
 */
class ApplicationC extends ApplicationB {

    public function initializeObject() {
        parent::initializeObject();
        $this->languageSkills = new ObjectStorage;
    }

    /**
     * @var integer $schoolQualification
     */
    protected $schoolQualification;

    /**
     * @return integer
     */
    public function getSchoolQualification() {
      return $this->schoolQualification;
    }
    
    /**
     * @param integer $schoolQualification
     * @return void
     */
    public function setSchoolQualification($schoolQualification) {
      $this->schoolQualification = $schoolQualification;
    }


    /**
     * @var string $professionalQualification
     */
    protected $professionalQualification;
    
    /**
     * @return string
     */
    public function getProfessionalQualification() {
      return $this->professionalQualification;
    }
    
    /**
     * @param string $professionalQualification
     * @return void
     */
    public function setProfessionalQualification($professionalQualification) {
      $this->professionalQualification = $professionalQualification;
    }


    /**
     * @var string $professionalQualificationFinalGrade
     */
    protected $professionalQualificationFinalGrade;
    
    /**
     * @return string
     */
    public function getProfessionalQualificationFinalGrade() {
      return $this->professionalQualificationFinalGrade;
    }
    
    /**
     * @param string $professionalQualificationFinalGrade
     * @return void
     */
    public function setProfessionalQualificationFinalGrade($professionalQualificationFinalGrade) {
      $this->professionalQualificationFinalGrade = $professionalQualificationFinalGrade;
    }


    /**
     * @var string $academicDegree
     */
    protected $academicDegree;
    
    /**
     * @return string
     */
    public function getAcademicDegree() {
      return $this->academicDegree;
    }
    
    /**
     * @param string $academicDegree
     * @return void
     */
    public function setAcademicDegree($academicDegree) {
      $this->academicDegree = $academicDegree;
    }


    
    /**
     * @var string $academicDegreeFinalGrade
     */
    protected $academicDegreeFinalGrade;
    
    /**
     * @return string
     */
    public function getAcademicDegreeFinalGrade() {
      return $this->academicDegreeFinalGrade;
    }
    
    /**
     * @param string $academicDegreeFinalGrade
     * @return void
     */
    public function setAcademicDegreeFinalGrade($academicDegreeFinalGrade) {
      $this->academicDegreeFinalGrade = $academicDegreeFinalGrade;
    }


    /**
     * @var string $doctoralDegree
     */
    protected $doctoralDegree;
    
    /**
     * @return string
     */
    public function getDoctoralDegree() {
      return $this->doctoralDegree;
    }
    
    /**
     * @param string $doctoralDegree
     * @return void
     */
    public function setDoctoralDegree($doctoralDegree) {
      $this->doctoralDegree = $doctoralDegree;
    }


    /**
     * @var string $doctoralDegreeFinalGrade
     */
    protected $doctoralDegreeFinalGrade;
    
    /**
     * @return string
     */
    public function getDoctoralDegreeFinalGrade() {
      return $this->doctoralDegreeFinalGrade;
    }
    
    /**
     * @param string $doctoralDegreeFinalGrade
     * @return void
     */
    public function setDoctoralDegreeFinalGrade($doctoralDegreeFinalGrade) {
      $this->doctoralDegreeFinalGrade = $doctoralDegreeFinalGrade;
    }


    /**
     * @var string $previousKnowledge
     */
    protected $previousKnowledge;
    
    /**
     * @return string
     */
    public function getPreviousKnowledge() {
      return $this->previousKnowledge;
    }
    
    /**
     * @param string $previousKnowledge
     * @return void
     */
    public function setPreviousKnowledge($previousKnowledge) {
      $this->previousKnowledge = $previousKnowledge;
    }


    /**
     * @var string $itKnowledge
     */
    protected $itKnowledge;
    
    /**
     * @return string
     */
    public function getItKnowledge() {
      return $this->itKnowledge;
    }
    
    /**
     * @param string $itKnowledge
     * @return void
     */
    public function setItKnowledge($itKnowledge) {
      $this->itKnowledge = $itKnowledge;
    }

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<PAGEmachine\Ats\Domain\Model\LanguageSkill>
     * @lazy
     */
    protected $languageSkills;
    
    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getLanguageSkills() {
        return $this->languageSkills;
    }
    
    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $languageSkills
     * @return void
     */
    public function setLanguageSkills(ObjectStorage $languageSkills) {
        $this->languageSkills = $languageSkills;
    }
    
    /**
     * @param PAGEmachine\Ats\Domain\Model\LanguageSkill $languageSkill
     * @return void
     */
    public function addLanguageSkill(PAGEmachine\Ats\Domain\Model\LanguageSkill $languageSkill) {
        $this->languageSkills->attach($languageSkill);
    }
    
    /**
     * @param PAGEmachine\Ats\Domain\Model\LanguageSkill $languageSkill
     * @return void
     */
    public function removeLanguageSkill(PAGEmachine\Ats\Domain\Model\LanguageSkill $languageSkill) {
        $this->languageSkills->detach($languageSkill);
    }



}