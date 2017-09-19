<?php
namespace PAGEmachine\Ats\Domain\Model;

/*
 * This file is part of the PAGEmachine ATS project.
 */

use PAGEmachine\Ats\Application\ApplicationStatus;
use PAGEmachine\Ats\Domain\Model\Job;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

/**
 * Application
 * @codeCoverageIgnore
 */
class Application extends ApplicationE implements CloneableInterface {

    /**
     * @var \DateTime $creationDate
     */
    protected $creationDate;

    /**
     * @return \DateTime
     */
    public function getCreationDate() {
      return $this->creationDate;
    }

    /**
     * @param \DateTime $creationDate
     * @return void
     */
    public function setCreationDate(\DateTime $creationDate) {
      $this->creationDate = $creationDate;
    }


    /**
     * @var \DateTime $receiptdate
     */
    protected $receiptdate;
    
    /**
     * @return \DateTime
     */
    public function getReceiptdate() {
      return $this->receiptdate;
    }
    
    /**
     * @param \DateTime $receiptdate
     * @return void
     */
    public function setReceiptdate(\DateTime $receiptdate) {
      $this->receiptdate = $receiptdate;
    }


    /**
     * @var integer $pool
     */
    protected $pool;
    
    /**
     * @return integer
     */
    public function getPool() {
      return $this->pool;
    }
    
    /**
     * @param integer $pool
     * @return void
     */
    public function setPool($pool) {
      $this->pool = $pool;
    }


    /**
     * @var integer $applicationType
     */
    protected $applicationType;
    
    /**
     * @return integer
     */
    public function getApplicationType() {
      return $this->applicationType;
    }
    
    /**
     * @param integer $applicationType
     * @return void
     */
    public function setApplicationType($applicationType) {
      $this->applicationType = $applicationType;
    }

    /**
     * @var integer $statusChange
     */
    protected $statusChange;
    
    /**
     * @return integer
     */
    public function getStatusChange() {
      return $this->statusChange;
    }
    
    /**
     * @param integer $statusChange
     * @return void
     */
    public function setStatusChange($statusChange) {
      $this->statusChange = $statusChange;
    }


    /**
     * @var PAGEmachine\Ats\Application\ApplicationRating $rating
     */
    protected $rating;
    
    /**
     * @return PAGEmachine\Ats\Application\ApplicationRating
     */
    public function getRating() {
      return $this->rating;
    }
    
    /**
     * @param PAGEmachine\Ats\Application\ApplicationRating $rating
     * @return void
     */
    public function setRating($rating) {
      $this->rating = $rating;
    }


    /**
     * @var PAGEmachine\Ats\Application\ApplicationRating $ratingPerso
     */
    protected $ratingPerso;
    
    /**
     * @return PAGEmachine\Ats\Application\ApplicationRating
     */
    public function getRatingPerso() {
      return $this->ratingPerso;
    }
    
    /**
     * @param PAGEmachine\Ats\Application\ApplicationRating $ratingPerso
     * @return void
     */
    public function setRatingPerso($ratingPerso) {
      $this->ratingPerso = $ratingPerso;
    }


    /**
     * @var integer $aip
     */
    protected $aip;
    
    /**
     * @return integer
     */
    public function getAip() {
      return $this->aip;
    }
    
    /**
     * @param integer $aip
     * @return void
     */
    public function setAip($aip) {
      $this->aip = $aip;
    }


    /**
     * @var boolean $invited
     */
    protected $invited;
    
    /**
     * @return boolean
     */
    public function getInvited() {
      return $this->invited;
    }
    
    /**
     * @param boolean $invited
     * @return void
     */
    public function setInvited($invited) {
      $this->invited = $invited;
    }


    /**
     * @var integer $opr
     */
    protected $opr;
    
    /**
     * @return integer
     */
    public function getOpr() {
      return $this->opr;
    }
    
    /**
     * @param integer $opr
     * @return void
     */
    public function setOpr($opr) {
      $this->opr = $opr;
    }


    /**
     * @var boolean $anonym
     */
    protected $anonym;
    
    /**
     * @return boolean
     */
    public function getAnonym() {
      return $this->anonym;
    }
    
    /**
     * @param boolean $anonym
     * @return void
     */
    public function setAnonym($anonym) {
      $this->anonym = $anonym;
    }


    /**
     * @var boolean $vocationalTrainingCompleted
     */
    protected $vocationalTrainingCompleted;
    
    /**
     * @return boolean
     */
    public function getVocationalTrainingCompleted() {
      return $this->vocationalTrainingCompleted;
    }
    
    /**
     * @param boolean $vocationalTrainingCompleted
     * @return void
     */
    public function setVocationalTrainingCompleted($vocationalTrainingCompleted) {
      $this->vocationalTrainingCompleted = $vocationalTrainingCompleted;
    }


    public function submit() {
        $this->setCreationDate(new \DateTime);
        $this->setReceiptdate(new \DateTime);

        $this->setStatus(ApplicationStatus::cast(ApplicationStatus::NEW_APPLICATION));
    }
    
}
